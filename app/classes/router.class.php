<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * FastRoute implementation
 * Documentation:
 * @see https://github.com/nikic/FastRoute
 *
 * Routes are arrays defined in routes.php containing a HTTP method (get, post etc) a URL 'trigger' and the handler.
 * The router class breaks up the URL and routes to a class, specified as the handler in routes.php, based on elements in the URL
 * e.g: http://example.com/device/5 will be translated too; device, 5.
 * In routes.php there should be a URL trigger defined for /device/5
 * E.g: array('GET', '/device/{id:[0-9]+}/config', 'DeviceController/getDeviceConfig'), {id:[0-9]+} is a regex placeholder which will only accept integers in this case.
 * The above defined route only accepts a GET method on the URL /device/5, the handler called in the route is class DeviceController() and the method called is getDeviceConfig().
 * As the getDeviceConfig() method accepts a parameter, a parameter must be send with the url. In this case 5.
 *
 * By default the router class returns a view to display.
 * There are two ways of showing views:
 *
 * - Views which are returned by the router based on the controllers name.
 *  the router returns a array containing the view key e.g:
 *  array(2) {
 *      ["response"]=> array(1) {
 *          ["device_id"]=> string(1) "4"
 *      }
 *      ["view"]=> string(28) "../src/views/Device.view.php"
 *  }
 * - Views which are returned by a controllers method
 *  the controllers returns a multidimensional array containing a object response array with the view e.g:
 *  array(2) {
 *      ["response"]=> array(1) {
 *          ["view"]=> string(35) "../src/views/device_config_schedule.view.php"
 *      }
 *      ["view"]=> string(28) "../src/views/Device.view.php"
 *  }
 *  a response view has priority over a view returned by the router.
 *
 * This method creates the possibility to create and show sub views even is the URL elements are in the same controllers class
 */

namespace App\Classes;

use \Config;
use App\Routes\Routes;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher;

class Router
{
    /**
     * Current cache location
	 *
     * @param string
     */	
    private static $cachePath = '../storage/framework/route.cache';
	
    /**
     * Disable or enable the caching of routes. Which improves performance
	 * Default: true;
     * @param bool
     */	
    private static $cacheDisabled = true;

    /**
     * Route the request to the registered class and method
	 *
     * @param string $httpMethod The requested httpmethod. Eg POST, GET, DELETE etc
     * @param string $uri The raw url from which the request originated
	 * @return array Array containing the object response and the view to be shown
     */	
    public static function route($httpMethod, $uri)
    {
        $dispatcher = \FastRoute\cachedDispatcher(function (RouteCollector $collector)
        {
            foreach (Routes::getRoutes() as $route)
            {
                $collector->addRoute($route[0], $route[1], $route[2]);
            }
        }, ['cacheFile' => self::$cachePath, 'cacheDisabled' => self::$cacheDisabled]);

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?'))
        {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        //var_dump( $routeInfo );
        switch ($routeInfo[0])
        {
            case Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                http_response_code(404);
                include '../src/views/errors/page_404.view.php';
                die();
            break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                http_response_code(403);
                include '../src/views/errors/page_403.view.php';
                die();
            break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
				
                list($class, $method) = explode("/", $handler, 2);

                $named_class = 'App\Controllers\\' . $class;

                $obj = call_user_func_array(array(
                    new $named_class,
                    $method
                ) , $vars);

                $view_name = str_replace('Controller', '', $class);

                $view = '../src/views/' . strtolower($view_name) . '.view.php';

                return array(
                    'response' => $obj,
                    'view' => $view
                );
            break;
        }

    }
}

