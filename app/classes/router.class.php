<?php
namespace App\Classes;

use \Config;
use App\Routes\Routes;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher;

class Router
{
    private static $cachePath = '../storage/framework/route.cache';
    private static $cacheDisabled = Config::DISABLE_ROUTING_CACHE;

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
                $view = '../src/views/' . $view_name . '.view.php';

                return array(
                    'response' => $obj,
                    'view' => $view
                );
            break;
        }

    }
}

