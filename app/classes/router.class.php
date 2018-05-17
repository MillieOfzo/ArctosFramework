<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * FastRoute implementation
 * Documentation:
 * @see https://github.com/nikic/FastRoute
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
    const CACHE_PATH = '../storage/framework/route.cache';
	
    /**
     * Disable or enable the caching of routes. Which improves performance
	 *
     * @param bool
     */	
    const CACHE_DISABLED = Config::DISABLE_ROUTING_CACHE;

    /**
     * Route the request to the registered class and method
	 *
     * @param string $httpMethod The requested httpmethod. Eg POST, GET, DELETE etc
     * @param string $uri The raw url from which the request originated
	 * @return array Array containing the object response and the view to be shown
     */	
    public function route($httpMethod, $uri)
    {
        $dispatcher = \FastRoute\cachedDispatcher(function (RouteCollector $collector)
        {
            foreach (Routes::getRoutes() as $route)
            {
                $collector->addRoute($route[0], $route[1], $route[2]);
            }
        }, ['cacheFile' => self::CACHE_PATH, 'cacheDisabled' => self::CACHE_DISABLED]);

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?'))
        {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        //var_dump( $uri );
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

                $method_response = call_user_func_array(array(
                    new $named_class,
                    $method
                ) , $vars);

                $view_name = str_replace('Controller', '', $class);

                // Methods can return new view
                // if the response is empty show the default view
                // else show the returned view
                if(empty($method_response['returned_view']))
                {
                    $view = '../src/views/' . $view_name . '.view.php';
                }
                else
                {
                    $view = $method_response['returned_view'];
                }

                return array(
                    'response' => $method_response,
                    'view' => $view
                );
            break;
        }

    }
}

