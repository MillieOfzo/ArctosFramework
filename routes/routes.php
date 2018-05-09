<?php

	$dispatcher = FastRoute\cachedDispatcher(function(FastRoute\RouteCollector $r) {

		$r->addGroup('/arctos', function (FastRoute\RouteCollector $r) {
			$r->addRoute('GET', '/', 'IndexController/Welcome');
			$r->addRoute('GET', '/user', 'UserController/index');
			$r->addRoute('GET', '/about', 'AboutController/getView');
		});
	
		// Users
		$r->addGroup('/arctos/users', function (FastRoute\RouteCollector $r) {
			$r->addRoute('GET', '/', 'UserController/getView');
			$r->addRoute('GET', '/{id:\d+}', 'UserController/getCount');
		});	
		
		// About
		$r->addGroup('/arctos/about', function (FastRoute\RouteCollector $r) {
			$r->addRoute('GET', '/', 'AboutController/getView');
		});	
		
	}, [
		'cacheFile' => '../storage/framework/route.cache',
		'cacheDisabled' => Config::DISABLE_CACHE
	]);