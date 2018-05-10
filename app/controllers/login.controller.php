<?php

	namespace App\Controllers;
	
	use App\Models\LoginModel as Login;
	use App\Classes\Logger as Logger;
	use App\Classes\Helper as Helper;
	use App\Classes\Auth as Auth;
	use App\Classes\Mailer as Mailer;
	use \Config as Config;
	use PHPMailer\PHPMailer\PHPMailer as PHPmailer;

    class LoginController
    {
        private $model;
        private $auth;
        private $purifier;

        function __construct()
        {
            $this->model = new Login;
            $this->auth = new Auth;
			$this->purifier = new \HTMLPurifier(\HTMLPurifier_Config::createDefault());
        }

        public function processLogin()
        {
			$cleaned_email = strtolower($this->purifier->purify($_POST['email']));
			if (!empty($_POST['login']) && Auth::checkCsrfToken($_POST['csrf']))
			{				
				$row = $this->model->getUserRow($cleaned_email);

				$login_ok = false;
				
				if ($row)
				{
					// Brute force preventie
					if ($this->auth->checkBrute($row['user_id']) === true)
					{
						// Log to file
						$msg = "Account: " . $row['user_email'] . " geblokkeerd";
						Logger::logToFile(__FILE__, 1, $msg);

						//die(header("Location: ../../?lck&id=" . $cleaned_email));
					}
					else
					{
						// Indien account status Blocked is log de inlog poging en stop het script.
						$check_status = $row['user_status'];
						// Check of user DEV role heeft.
						$check_dev = true; //containsWord($row['Overige'], "DEV");
						if ($check_status === "Blocked")
						{
							// Log to file
							$msg = "Login attempt. Blocked user: " . $cleaned_email;
							Logger::logToFile(__FILE__, 2, $msg);
	
							die(header("Location: ../../?blc&id=" . $cleaned_email));
							// Check of APP_ENV op OTAP staat en of user DEV role heeft.
							
						}
						elseif (Config::getEnv() == "OTAP" && $check_dev == false)
						{
							// Log to file
							$msg = "Login attempt. Non DEV user: " . $cleaned_email;
							Logger::logToFile(__FILE__, 2, $msg);
	
							die(header("Location: ../../?dev&id=" . $cleaned_email));
						}
						else
						{
							if (password_verify($_POST['password'], $row['user_password']))
							{
								$login_ok = true;
							} 
	
						}
					}
				}

				if ($login_ok)
				{
					unset($row['user_password']);
					unset($row['user_status']);
	
					$_SESSION[Config::SES_NAME] = $row;
	
					// Log to file
					$msg = "Login success. User: " . $_SESSION[Config::SES_NAME]['user_email'];
					Logger::logToFile(__FILE__, 0, $msg);
	
					// Verwijder alle login attempts van de user.
					$id = $row['user_id'];
					//$this->db_conn->query("DELETE FROM app_users_login_attempts WHERE user_id =?i", $id);
	
					// Redirect the user to the private members-only page.
					die($this->redirectLogin());
					//header("Location: ../login/redirect.php"));
					
				}
				else
				{
	
					// Log to file
					$msg = "Login failed. User: " . $cleaned_email;
					Logger::logToFile(__FILE__, 2, $msg);
	
					// Save login attempt in database
					$ip_adres = $_SERVER['REMOTE_ADDR'];
					$ip_port = $_SERVER['REMOTE_PORT'];
	
					$now = time();
					$date_time_now = date("Y-m-d H:i:s");
					$id = $row['user_id'];
	
					$query_arr = array(
						'user_id' => $id,
						'user_ip_adres' => $ip_adres,
						'user_ip_port' => $ip_port,
						'user_attempt_time' => $now,
						'user_attempt_date_time' => $date_time_now
					);
					$this->model->failedLoginAttempt($query_arr);
	
					die(header("Location: ../../?id=" . $cleaned_email));
	
				}				
			}
			else
			{
				die(Helper::redirect('/?fail'));
			}			
        }
		
		public function processLogout($csrf)
		{
			if (isset($csrf) && Auth::checkCsrfToken($csrf))
			{
				$user_email = htmlspecialchars($_SESSION[Config::SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
				$cleaned_email = strtolower($this->purifier->purify($user_email));
				
				// Verwijderd de cookie(s) aan de client side
				if (isset($_COOKIE['modal']))
				{
					unset($_COOKIE['modal']);
					setcookie('modal', '', time() - 3600, "/"); // -3600 = 1 uur geleden.
					
				}
	
				// If we want to keep some session information such as shopping cart contents,
				// we only remove the user's data from the session without unsetting remaining
				// session variables and without destroying the session.
				unset($_SESSION[Config::SES_NAME]);
				unset($_SESSION['_token']);
	
				// Otherwise, we unset all of the session variables.
				$_SESSION = array();
	
				// If it's desired to kill the session, also delete the session cookie.
				// Note: This will destroy the session, and not just the session data!
				if (ini_get("session.use_cookies"))
				{
					$params = session_get_cookie_params();
					setcookie(session_name() , '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
				}
	
				// Finally, destroy the session.
				session_destroy();
				
				$msg = "Logout success. User: ".$cleaned_email;
				Logger::logToFile(__FILE__, 0, $msg);
				
				//$session->destroy($id);
				// Whether we destroy the session or not, we redirect them to the login page
				Helper::redirect('/');
			}
			else
			{
				die(Helper::redirect('/'));
			}
		}

		public function genRecoverToken()
		{
			$cleaned_email = strtolower($this
				->purifier
				->purify($_POST['email']));
	
			if (!empty($_POST['request']) && Auth::checkCsrfToken($_POST['csrf']))
			{
				$row = $this->model->getUserRow($cleaned_email);
	
				$user_auth = false;
	
				if ($row)
				{
					$user_auth = true;
				}
				else
				{
					die(Helper::redirect('/?uknw=' . $cleaned_email));
				}
	
				if ($user_auth)
				{
					// Generate random token
					$token = openssl_random_pseudo_bytes(32);
					$token = bin2hex($token);
	
					$salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
	
					$token_hash = hash('sha256', $token . $salt);
	
					for ($round = 0;$round < 65536;$round++)
					{
						$token_hash = hash('sha256', $token_hash . $salt);
					}
					
					$token_array = array(
						'email' => $cleaned_email,
						'hash' => $token_hash,
						'salt' => $salt
					);
					
					$this->model->saveRecoverToken($token_array );
	
					// Enkel voor testing
					//die(header("Location: ../token.php?rec=".$token."&id=".$row['Initialen']));

					$email_values = array(
						'user_name' => $row['user_name'] . " " . $row['user_last_name'],
						'recover_link' => '<a class="link" href="'.$_SERVER['HTTP_HOST'].'/token.php?rec=' . $token . '&id=' . $row['user_email'] . '">Recover token</a>'
					);
	
					//$mail_body = Mailer::build('gen_token',$email_values);
					
					$mail = new PHPmailer;
					$mail->isSMTP();
					$mail->Host = Config::SMTP_HOST;
					$mail->Port = Config::SMTP_PORT;
					$mail->AddAddress($row['user_email']);
					$mail->SetFrom(Config::APP_EMAIL);
					$mail->Subject = Config::APP_TITLE . ' test '; //LANG['tokenmsg']['email']['token_req'];
					$mail->MsgHTML('test');
					//$mail->WordWrap = 80;
	
					if ($mail->Send())
					{
						$msg = "Token request successful user: " . $row['user_email'];
						Logger::logToFile(__FILE__, 0, $msg);
						die(header("Location: ../../?tok=suc"));
						
					}
					else
					{
						$msg = "Token request failed user: " . $row['user_email'];
						Logger::logToFile(__FILE__, 0, $msg);						
						die(header("Location: ../../?tok=err"));
					}
	
				}
				else
				{
					die(Helper::redirect('/?uknw=' . $cleaned_email));
				}
			}
			else
			{
				die(header("Location: ../../?fail"));
			}			
		}
		
		public function redirectLogin()
		{
			// We check to see whether the user is logged in or not
			if (Auth::checkAuth())
			{
				// Remember that this die statement is absolutely critical.  Without it,
				// people can view your members-only content without logging in.
				die(Helper::redirect('/'));
			}
	
			//Update Lastaccess kolom in users database
			$id = $_SESSION[Config::SES_NAME]['user_id'];
	
			$connected = $this->model->updateUserLastAccess($id);
	
			//Redirect naar juiste index pagina op basis van Userrole
			$user_role = $_SESSION[Config::SES_NAME]['user_role'];
	
			if (APP_INITIALIZE === 0 && $connected)
			{
				Helper::redirect('/view');
			}
			elseif ($user_role == 1 && $connected)
			{
				Helper::redirect('/home');
			}
			elseif ($user_role == 2 && $connected)
			{
				Helper::redirect('/home');
			}
			else
			{
				Helper::redirect('/');
			}
		}
    }