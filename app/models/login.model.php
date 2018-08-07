<?php
namespace App\Models;

use \SafeMySQL;
use App\Classes\Logger;

class LoginModel
{
    private $conn;

    function __construct()
    {
        $this->conn = new SafeMySQL;
    }

    public function removeLoginAttempt($user_id)
    {
        try
        {
            $this->conn->query("DELETE FROM app_users_login_attempts WHERE user_id =?i", $user_id);
        }
        catch(Exception $ex)
        {
            return Logger::logError($ex);
        }
    }

    public function failedLoginAttempt($query_arr = array())
    {
        try
        {
            $this->conn->query("INSERT INTO app_users_login_attempts SET ?u", $query_arr);
        }
        catch(Exception $ex)
        {
            return Logger::logError($ex);
        }
    }

    public function saveRecoverToken($query_params)
    {

        try
        {
            $this->conn->query("INSERT INTO app_users_tokens SET ?u", $query_params);

        }
        catch(Exception $ex)
        {
            return Logger::logError($ex);
        }
    }

    public function checkRecoverTokenExsists()
    {
        try
        {
            return $this->conn->getOne("SELECT COUNT(user_id) FROM app_users_tokens");
        }
        catch(Exception $ex)
        {
            return Logger::logError($ex);
        }
    }

    public function getRecoverToken($user_id)
    {
        try
        {
            return $this->conn->getRow("SELECT * FROM app_users_tokens WHERE user_id = ?i ORDER BY user_token_date_request DESC LIMIT 1", $user_id);
        }
        catch(Exception $ex)
        {
            return Logger::logError($ex);
        }
    }

    public function deleteRecoverToken($user_id)
    {
        try
        {
            $this->conn->query("DELETE FROM app_users_tokens WHERE user_id = ?i", $user_id);

        }
        catch(Exception $ex)
        {
            return Logger::logError($ex);
        }
    }

}

