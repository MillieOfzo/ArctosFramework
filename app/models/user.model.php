<?php
namespace App\Models;

use \SafeMySQL;
use App\Classes\Logger;

class UserModel
{
    private $conn;

    function __construct()
    {
        $this->conn = new SafeMySQL;
    }

    public function getUserRow($user_selector)
    {
        if (is_numeric($user_selector))
        {
            $parse = $this->conn->parse("user_id = ?i", $user_selector);
        }
        else
        {
            $parse = $this->conn->parse("user_email = ?s", $user_selector);
        }

        try
        {
            $row = $this->conn->getRow("SELECT * FROM app_users WHERE ?p LIMIT 1", $parse);
            return $row;
        }
        catch(Exception $ex)
        {
            return self::logError($ex);
        }
    }

    public function updateUserPassword($query_param, $user_id)
    {
        try
        {
            $this->conn->query("UPDATE app_users SET ?u WHERE user_id = ?i", $query_param, $user_id);
            return true;
        }
        catch(Exception $ex)
        {
            return self::logError($ex);
        }
    }

    public function updateUserLastAccess($user_id)
    {
        try
        {
            $this->conn->query("UPDATE app_users SET user_last_access = now() WHERE user_id = ?s", $user_id);
            return true;
        }
        catch(Exception $ex)
        {
            return self::logError($ex);
        }
    }

    private static function logError($exception)
    {
        $msg = 'Regel: ' . $exception->getLine() . ' Bestand: ' . $exception->getFile() . ' Error: ' . $exception->getMessage();
        Logger::logToFile(__FILE__, 1, $msg);
        return false;
    }
}

