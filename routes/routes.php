<?php

	$dispatcher = FastRoute\cachedDispatcher(function(FastRoute\RouteCollector $r) {

		$r->addGroup('', function (FastRoute\RouteCollector $r) {
			$r->addRoute('GET', '/', 'IndexController/Welcome');
			$r->addRoute('GET', '/user', 'UserController/index');
			$r->addRoute('GET', '/about', 'AboutController/getView');
		});

		// Login
		$r->post('/login', 'LoginController/processLogin');
		$r->get('/logout/{id:[0-9A-Za-z]+}', 'LoginController/processLogout');
		$r->post('/login/gentoken', 'LoginController/genRecoverToken');

		// Users
		$r->addGroup('/user', function (FastRoute\RouteCollector $r) {
			$r->addRoute('GET', '/', 'UserController/getView');
			$r->addRoute('GET', '/{id:\d+}', 'UserController/getCount');
		});	
		
		// About
		$r->addGroup('/about', function (FastRoute\RouteCollector $r) {
			$r->addRoute('GET', '/', 'AboutController/getView');
		});	
		
	}, [
		'cacheFile' => '../storage/framework/route.cache',
		'cacheDisabled' => \Config::DISABLE_ROUTING_CACHE
	]);