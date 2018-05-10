<?php

	namespace App\Models;

	use App\Classes\Logger as Logger;
	use App\Classes\SafeMySQL as Sql;	
	
    class LoginModel
    {

		private $conn;
	
		function __construct()
		{
			$this->conn = new Sql;
		}
		
		public function getUserRow($user_email)
		{
            try
            {
                $row = $this->conn->getRow("SELECT * FROM app_users WHERE user_email = ?s LIMIT 1", $user_email);
				return $row;
            }
            catch(Exception $ex)
            {

                $msg = 'Regel: ' . $ex->getLine() . ' Bestand: ' . $ex->getFile() . ' Error: ' . $ex->getMessage();
                Logger::logToFile(__FILE__, 1, $msg);
                return false;
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

                $msg = 'Regel: ' . $ex->getLine() . ' Bestand: ' . $ex->getFile() . ' Error: ' . $ex->getMessage();
                Logger::logToFile(__FILE__, 1, $msg);
                return false;
            }
		}	

		public function updateUserLastAccess($user_id)
		{
			try
			{
				$this->conn->query("UPDATE app_users SET user_last_access = now() WHERE user_id = ?s", $user_id);
				$updated = true;
			}
			catch(Exception $ex)
			{
				$msg = 'Regel: ' . $ex->getLine() . ' Bestand: ' . $ex->getFile() . ' Error: ' . $ex->getMessage();
				Logger::logToFile(__FILE__, 1, $msg);
				$updated = false;
			}
			return $updated;
		}
		
		public function saveRecoverToken($token_arr = array())
		{
	
			$query_params = array(
				'user_email' => $token_arr['email'],
				'user_token_date_time' => time() ,
				'user_token_date_request' => date("Y-m-d H:i:s") ,
				'user_token_hash' => $token_arr['hash'],
				'user_token_salt' => $token_arr['salt']
			);
	
			try
			{
				$this->conn->query("INSERT INTO app_users_tokens SET ?u",$query_params);

			}
			catch(PDOException $ex)
			{
				$msg = 'Regel: ' . $ex->getLine() . ' Bestand: ' . $ex->getFile() . ' Error: ' . $ex->getMessage();
				Logger::logToFile(__FILE__, 1, $msg);
				return false;
			}			
		}
    }