<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * Routes class
 *
 * Route class to be used by FastRoute class.
 * predefined http request which should be routed by router.class.php 
 */
 
namespace App\Routes;

use App\Classes\Auth;

class Routes
{
    /**
     * Define http routes in an array as followed
	 *   First 	- Request type e.g. POST, GET, PUT, DELETE
	 *   Second - Which browser url to match
	 *   Third 	- Which class and method to route too, class/method
     * @return array
     */
    public static function getRoutes()
    {
        $routes_arr = array(
			// Framework routes
			// ===================================================================
			array('GET', '/', 'IndexController/index'),
			array('POST', '/user/password/update', 'UserController/updateUserPass'),
			array('POST','/login', 'LoginController/processLogin'),
			array('GET','/logout/{id:[0-9A-Za-z]+}', 'LoginController/processLogout'),
			array('POST','/login/gentoken', 'LoginController/genRecoverToken'),
			array('GET','/login/gentoken/{id:[0-9A-Za-z]+}/{csrf:[0-9A-Za-z]+}/{user:[0-9]+}', 'LoginController/processPassReset'),
			// ===================================================================
			
			// Application routes
			array('GET', '/about', 'AboutController/index'),
		);
		
		/*
		 * Define routes only for admins
		 */
		 
		if(Auth::checkAuthUserIsAdmin())
		{
			$admin_arr = array(
				// Framework routes
				// ===================================================================
				array('GET', '/logging', 'LoggingController/index'),
				array('POST', '/logging/file/', 'LoggingController/getLogFile'),
				// ===================================================================
				
				// Application routes
				array('GET', '/user', 'UserController/index'),
			);
		} 
		else
		{
			$admin_arr = array();			
		}			
		
        return array_merge($routes_arr,$admin_arr);
    }
}

