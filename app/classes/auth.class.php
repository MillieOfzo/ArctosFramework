<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * Authentication class
 *
 * Functions to check if user are authenticated
 * and to prevent unauthorized access as well as
 * providing brute force protection
 */ 
namespace App\Classes;

use \Config;
use \SafeMySQL;

class Auth
{

    function __construct()
    {

    }
	
	/**
	* Get the user_id of the currently logged in user
	* @return int $auth_user User id of authenticated user
	*/
    public static function getAuthUser()
    {
        $auth_user = (isset($_SESSION[Config::SES_NAME])) ? htmlentities($_SESSION[Config::SES_NAME]['user_id'], ENT_QUOTES, 'UTF-8') : '---';
        return (int) $auth_user;
    }

	/**
	* Check if there is a session active
	* @return bool true if there is a session active else return false
	*/	
    public static function checkAuth()
    {
        return (!empty($_SESSION[Config::SES_NAME])) ? true : false;
    }

	/**
	* Check if there the currently logged in user is admin
	* @return bool true if there is a session active else return false
	*/		
    public static function checkAuthUserIsAdmin()
    {
        $conn = new SafeMySQL;
		
        $user_id = (int)self::getAuthUser();

        if ($conn->getOne("SELECT user_id FROM app_users WHERE user_status = 1 AND user_id =  ?i AND user_role = 1", $user_id))
        {
            return true;
        }
        else
        {
            return false;
        }

    }

	/**
	* Brute force prevention
	*
	* @param int $user_id Accepts user is as integer
	* @return bool true if there are more than 5 failed login attempts else return false
	*/		
	public static function checkBrute($user_id)
    {
        $conn = new SafeMySQL;

        // Get timestamp of current time
        $now = time();
        // All login attempts are counted from the past 2 hours.
        $valid_attempts = $now - (2 * 60 * 60);

        $user_id = (int)$user_id;
        $result = $conn->query("SELECT user_id FROM app_users_login_attempts WHERE user_id = ?i AND user_attempt_time > ?s", $user_id, $valid_attempts);

        $count = $conn->numRows($result);
        // If there have been more than 5 failed logins
        if ($count >= 5)
        {
            return true;
        }
        else
        {
            return false;
        }
    }	

	/**
	* Check if the csrf token is valid
	*
	* @param string $token 
	* @return bool true if the $token hash equals the token hash in the active session else return false
	*/	
    public static function checkCsrfToken($token)
    {
        if(Csrf::checkCsrfToken($token))
        {
            return true;
        }
        else
        {
            http_response_code(403);
            include '../src/views/errors/page_403.view.php';
            die();
        }
    }

	/**
	* Check if the csrf token is valid
	*
	* @param int $length Defines the length of the for loop. Per loop 3 words are chosen. Default length is 2 which equals 6 words 
	* @return string $seed Return the password seed based on length. Default returns 6 words as string
	*/	
    public static function genPassSeed($length = 2)
    {
        $path = '../storage/framework/seed_words.conf';
        $file = file_get_contents($path);
        $word_list = preg_split('/[\s]+/', $file, -1, PREG_SPLIT_NO_EMPTY);
        // var_dump($words);
        $word_count = count($word_list);
        $words = '';

        //$r = unpack('c*', bin2hex(openssl_random_pseudo_bytes(4))); // array bytes
        //$x = rand(0, PHP_INT_MAX).str_pad(rand(0,999999999),9,0, STR_PAD_LEFT); //long int
        for ($i = 0;$i < $length;$i++)
        {
            $r = unpack('c*', bin2hex(openssl_random_pseudo_bytes(4)));
            $x = (int)($r[4] & 0xff) + (($r[3] & 0xff) << 8) + (($r[2] & 0xff) << 16) + (($r[1] & 0xff) << 24);
            $w1 = (int)($x % $word_count);
            $w2 = (int)(((($x / $word_count) >> 0) + $w1) % $word_count);
            $w3 = (int)(((($x / $word_count) >> 0) + $w2) % $word_count);
            //$w3 = (int)(((((($x / $word_count) >> 0) / $word_count) >> 0) + $w2) % $word_count);
            $words .= $word_list[$w1] . " ";
            $words .= $word_list[$w2] . " ";
            $words .= $word_list[$w3] . " ";
        }
        $seed = rtrim($words);

        return $seed;
    }
}

