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
		/**
		 * Define routes only if user is logged in
		 */
		if(Auth::checkAuth())
		{		
			$routes_arr = array(
				// Framework routes
				// ===================================================================
				array('GET', '/', 'IndexController/index'),
				array('GET','/logout/{id:[0-9A-Za-z]+}', 'LoginController/processLogout'),
	
				// User
				array('GET', '/user', 'UserController/index'),
				array('POST', '/user/password/update', 'UserController/updateUserPass'),
	
				// Tickets
				array('GET', '/tickets', 'TicketsController/index'),
				array('GET', '/tickets/table', 'TicketsController/getTableTickets'),
				array('GET', '/tickets/new', 'TicketsController/newView'),
				array('GET', '/tickets/view/{id:[0-9A-Za-z]+}', 'TicketsController/updateView'),
				array('GET', '/tickets/get/updates/{id:[0-9A-Za-z]+}', 'TicketsController/getTicketUpdates'),
				array('GET', '/tickets/get/info/{id:[0-9A-Za-z]+}', 'TicketsController/getTicketInfo'),
				array('POST', '/tickets/update', 'TicketsController/updateTicket'),
				// ===================================================================
	
			);
		}
		else
		{
			$routes_arr = array(
				array('GET', '/', 'IndexController/index'),
				// Login
				array('POST','/login', 'LoginController/processLogin'),
				array('POST','/login/gentoken', 'LoginController/genRecoverToken'),
				array('GET','/login/gentoken/{id:[0-9A-Za-z]+}/{csrf:[0-9A-Za-z]+}/{user:[0-9]+}', 'LoginController/processPassReset'),			
			);
		}
		
		/**
		 * Define routes only for admins
		 */
		 
		if(Auth::checkAuthUserIsAdmin())
		{
			$admin_arr = array(
				// Framework routes
				// ===================================================================
                // Users
                array('GET', '/users', 'UsersController/index'),
                array('GET', '/users/table', 'UsersController/getTableUsers'),
                array('POST', '/users/new', 'UsersController/newUser'),
                array('POST', '/users/update', 'UsersController/updateUser'),
                array('POST', '/users/delete', 'UsersController/deleteUser'),

                // Logging
				array('GET', '/logging', 'LoggingController/index'),
				array('POST', '/logging/file/', 'LoggingController/getLogFile'),
				// ===================================================================

            );
		} 
		else
		{
			$admin_arr = array();			
		}			
		
        return array_merge($routes_arr,$admin_arr);
    }
}

