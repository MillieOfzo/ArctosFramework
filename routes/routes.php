<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * Routes class
 *
 * Route class to be used by FastRoute class.
 * predefined http request which should be routed by router.class.php
 *
 * Define http routes in an array as followed
 *   First 	- Request type e.g. POST, GET, PUT, DELETE
 *   Second - Which browser url to match
 *   Third 	- Which class and method to route too, class/method
 */
 
namespace App\Routes;

use App\Classes\Auth;

class Routes
{
    /**
     * Define routes for unauthorized users
     * @return array
     */
	private static function setUnAuthRoutes()
	{
		if(!Auth::checkAuth())
		{		
			return array(
				array('GET', '/', 'IndexController/index'),
				array('GET', '/privacy', 'PrivacyController/index'),
				array('GET', '/terms', 'TermsController/index'),
				array('GET', '/cookie', 'CookieController/index'),

				// Login
				array('POST','/login', 'LoginController/processLogin'),
				array('POST','/login/gentoken', 'LoginController/genRecoverToken'),
				array('GET','/login/gentoken/{id:[0-9A-Za-z]+}/{csrf:[0-9A-Za-z]+}/{user:[0-9]+}', 'LoginController/processPassReset'),
				
				// Register
				array('POST', '/register', 'RegisterController/processUserRegistration'),					
				array('POST', '/register/dealer', 'RegisterController/processDealerRegistration'),	
				
			);
		}
		return array();	
	}

    /**
     * Define routes only if user is logged in
     * @return array
     */
	private static function setAuthRoutes()
	{
		if(Auth::checkAuth()) 
		{
            return array(
                // Framework routes
                // ===================================================================
				array('GET', '/', 'LoginController/redirectLogin'),
                array('GET', '/logout/{id:[0-9A-Za-z]+}', 'LoginController/processLogout'),
				// Autocomplete
                array('GET', '/autocomplete', 'IndexController/getAutoComplete'),
				// Home
				array('GET', '/home', 'HomeController/index'),
                // User
                array('GET', '/user', 'UserController/index'),
                array('POST', '/user/password/update', 'UserController/updateUserPass'),
				// Tickets
				array('GET', '/tickets', 'TicketsController/index'),
				array('GET', '/tickets/table', 'TicketsController/getTableTickets'),
				array('GET', '/tickets/new', 'TicketsController/newTicket'),
				array('GET', '/tickets/view/{id:[0-9A-Za-z]+}', 'TicketsController/updateView'),
				array('GET', '/tickets/get/updates/{id:[0-9A-Za-z]+}', 'TicketsController/getTicketUpdates'),
				array('GET', '/tickets/get/info/{id:[0-9A-Za-z]+}', 'TicketsController/getTicketInfo'),
				array('POST', '/tickets/update', 'TicketsController/updateTicket'),
                // ===================================================================

                array('GET', '/privacy', 'PrivacyController/index'),
                array('GET', '/terms', 'TermsController/index'),
                array('GET', '/cookie', 'CookieController/index'),
                // Application routes
            );
        }
		return array();			
	}

    /**
     * Define routes only for admins
     * @return array
     */
	private static function setAdminRoutes()
	{
		if(Auth::checkAuthUserIsAdmin())
		{
			return array(
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
		return array();			
	}
	
    /**
     * @return array | Merged array with routes, used by route.class.php
     */
    public static function getRoutes()
    {
        return array_merge(self::setUnAuthRoutes(),self::setAuthRoutes(),self::setAdminRoutes());
    }
}

