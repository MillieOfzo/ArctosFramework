<?php
namespace App\Routes;

class Routes
{
    /**
     * Define http routes in a array as followed
     * - First = Request type e.g. POST, GET, PUT, DELETE;
     * - Second = Which browser url to match;
     * - Third = Which class and method to route too;
     */
    public static function getRoutes()
    {
        $routes_arr = array(
			array('GET', '/', 'IndexController/index'),
			array('GET', '/user', 'UserController/index'),
			array('POST', '/user/password/update', 'UserController/updateUserPass'),
			array('GET', '/about', 'AboutController/index'),
			array('POST','/login', 'LoginController/processLogin'),
			array('GET','/logout/{id:[0-9A-Za-z]+}', 'LoginController/processLogout'),
			array('POST','/login/gentoken', 'LoginController/genRecoverToken'),
			array('GET','/login/gentoken/{id:[0-9A-Za-z]+}/{csrf:[0-9A-Za-z]+}/{user:[0-9]+}', 'LoginController/processPassReset')
		);

        return $routes_arr;
    }
}

