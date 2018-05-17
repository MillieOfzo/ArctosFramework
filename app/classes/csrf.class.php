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
     * @throws \Exception
     */
    public static function genCsrfToken()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Compare token hash with session token
     * @param string $token Token hash
     * @return bool returns false if token is not valid
     */
    public static function checkCsrfToken($token)
    {
        if (hash_equals($token, $_SESSION['_token']))
        {
            return true;
        }
        else
        {
            // Log to file
            $msg = "CSRF token invalid for user: " . Auth::getAuthUser();
            Logger::logToFile(__FILE__, 0, $msg);
            return false;
        }
    }
}

