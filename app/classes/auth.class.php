<?php

	namespace App\Classes;
	
	use App\Classes\SafeMySQL as Sql;
	
	class Auth
	{
		private $db;
		
		function __construct()
		{
			$this->db = new Sql;
		}
		
		private static function getAuthUser()
		{
			$auth_user = (isset($_SESSION[\Config::SES_NAME])) ? htmlentities($_SESSION[\Config::SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8') : '---';
			return $auth_user;
		}
		
		public static function checkAuth()
		{
			return (!empty($_SESSION[\Config::SES_NAME])) ? true : false;
		}
		
		public function checkBrute($user_id) {
			$conn = $this->db;
			
			// Get timestamp of current time 
			$now 	= time();
			// All login attempts are counted from the past 2 hours. 
			$valid_attempts = $now - (2 * 60 * 60);
			
			$user_id = (int)$user_id;
			$result = $conn->query("SELECT * from app_users_login_attempts where user_id = ?i AND user_attempt_time > ?s", $user_id, $valid_attempts);

			$count 	= $conn->numRows($result); 
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

		public function checkUserIsAdmin($user_id)
		{
			$conn =  $this->db;
			
			$user_id = (int)$user_id;
	
			if($conn->getRow("SELECT user_id FROM app_users WHERE user_status = 'Active' AND user_id =  ?i",$user_id))
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
			if(hash_equals($token, $_SESSION['_token']))
			{
				return true;
			}
			else 
			{
				// Log to file
				$msg = "CSRF token invalid during logout for user: " . self::getAuthUser();
				Logger::logToFile(__FILE__, 0, $msg);				
				return false;
			}
		}
	}