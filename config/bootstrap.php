<?php
	require '../vendor/autoload.php';
	require '../routes/routes.php';

	// Fetch method and URI from somewhere
	$httpMethod = $_SERVER['REQUEST_METHOD'];
	$uri = $_SERVER['REQUEST_URI'];
	
	// Strip query string (?foo=bar) and decode URI
	if (false !== $pos = strpos($uri, '?')) {
		$uri = substr($uri, 0, $pos);
	}
	$uri = rawurldecode($uri);
	
	$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
	//var_dump( $routeInfo );
	
	switch ($routeInfo[0]) {
		case FastRoute\Dispatcher::NOT_FOUND:
			// ... 404 Not Found
            http_response_code(404);
            include '../src/views/errors/page_404.view.php';
            die();
			break;
		case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
			$allowedMethods = $routeInfo[1];
            http_response_code(403);
            include '../src/views/errors/page_403.view.php';
            die();
			break;
		case FastRoute\Dispatcher::FOUND:
			$handler = $routeInfo[1];
			$vars = $routeInfo[2];        
			
			list($class, $method) = explode("/", $handler, 2);
			
			$named_class = 'App\Controllers\\'.$class;
			
			$obj = call_user_func_array(array(new $named_class, $method), $vars);
			
			//$reflect  = new ReflectionClass($class);
			//$instance = $reflect->newInstanceArgs($vars);
			//var_dump( $method_return );
			//echo $handler.'<br>';
			//var_dump( $obj);
			
			$view = str_replace('Controller', '',$class);
			$content = '../src/views/'.$view.'.view.php';
			
			break;
	}

