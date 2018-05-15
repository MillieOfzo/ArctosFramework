<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * Cross-Site Request Forgery class (CSRF)
 *
 * Class to prevent CSRF 
 */
 
namespace App\Classes;

use \App\Classes\Logger as Logger;

class Csrf
{

	/**
	* Creates a string with 32 random characters and stores it in the active session
	*/
    public static function genCsrfToken()
    {
        $_SESSION['_token'] = bin2hex(random_bytes(32));
    }

    /*
    public function checkCsrfToken($post_val)
    {
        if (!hash_equals($post_val['csrf'], $_SESSION['_token']))
        {
            $msg = "CSRF token invalid for user: " . $this->auth_user;
            Logger::logToFile( 0, $msg);
    
            $response_array['type'] = 'warning';
            $response_array['title'] = LANG['error_msg']['csrf']['label'];
            $response_array['body'] = LANG['error_msg']['csrf']['msg'];
            jsonArr($response_array);
        }
    }
    */
}

