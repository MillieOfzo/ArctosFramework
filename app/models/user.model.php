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

    public function create($query_param)
    {
        try
        {
            $this->conn->query("INSERT INTO app_users SET ?u", $query_param);
            return true;
        }
        catch(Exception $ex)
        {
            return Logger::logError($ex);
        }
    }

    public function update($query_param, $user_id)
    {
        try
        {
            $this->conn->query("UPDATE app_users SET ?u WHERE user_id = ?i", $query_param, $user_id);
            return true;
        }
        catch(Exception $ex)
        {
            return Logger::logError($ex);
        }
    }

    public function delete($user_id)
    {
        try
        {
            $this->conn->query("DELETE FROM app_users WHERE user_id = ?i", $user_id);
            return true;
        }
        catch(Exception $ex)
        {
            return Logger::logError($ex);
        }
    }

    public function getUserRow($selector)
    {
        if (is_numeric($selector))
        {
            $parse = $this->conn->parse("user_id = ?i", (int)$selector);
        }
        else
        {
            $parse = $this->conn->parse("user_email = ?s", $selector);
        }

        try
        {
            $row = $this->conn->getRow("SELECT * FROM app_users WHERE ?p LIMIT 1", $parse);
            return $row;
        }
        catch(Exception $ex)
        {
            return Logger::logError($ex);
        }
    }

	public function getUserRole($id)
    {
        try
        {
            return $this->conn->getOne("SELECT role_name FROM app_role WHERE id = (SELECT user_role FROM app_users WHERE user_id = ?i);", $id);
        }
        catch(Exception $ex)
        {
            return Logger::logError($ex);
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
            return Logger::logError($ex);
        }
    }

}

