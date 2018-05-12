<?php
namespace App\Classes;

use \App\Classes\Logger as Logger;

class Csrf
{

    function __construct()
    {

    }

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

