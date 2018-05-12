<?php
namespace App\Classes;

use \Config;

class Auth
{
    private $db;

    function __construct()
    {
        $this->db = new SafeMySQL;
    }

    private static function getAuthUser()
    {
        $auth_user = (isset($_SESSION[Config::SES_NAME])) ? htmlentities($_SESSION[Config::SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8') : '---';
        return $auth_user;
    }

    public static function checkAuth()
    {
        return (!empty($_SESSION[Config::SES_NAME])) ? true : false;
    }

    public function checkBrute($user_id)
    {
        $conn = $this->db;

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

    public static function checkAuthUserIsAdmin()
    {
        $conn = new SafeMySQL;

        $user_id = (int)htmlentities($_SESSION[Config::SES_NAME]['user_id'], ENT_QUOTES, 'UTF-8');

        if ($conn->getOne("SELECT user_id FROM app_users WHERE user_status = 'Active' AND user_id =  ?i AND user_role = 1", $user_id))
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    public static function checkCsrfToken($token)
    {
        if (hash_equals($token, $_SESSION['_token']))
        {
            return true;
        }
        else
        {
            // Log to file
            $msg = "CSRF token invalid for user: " . self::getAuthUser();
            Logger::logToFile(__FILE__, 0, $msg);
            return false;
        }
    }

    public static function genPassSeed($length = 2)
    {
        $path = Config::ROOT_PATH . '/storage/framework/seed_words.txt';
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

