<?php
 
namespace App\Controllers;

use \Config;
use App\Models\LoginModel;
use App\Models\UserModel;
use App\Classes\Logger;
use App\Classes\Helper;
use App\Classes\Auth;
use App\Classes\Mailer;
use App\Classes\Language;
use App\Classes\LdapAuth;

class LoginController
{
    private $model;
    private $auth;
    private $lang;
    private $purifier;
	
	/**
	 * Token expiration time
	 * 1 day = 60 seconds * 60 minutes * 24 hours = 86400 sec
     * 10 min = 60 seconds * 10 = 600 sec
	 *
	 * @var int
	 */
    private $token_expiration = 600;
	
    function __construct()
    {
        $this->model = new LoginModel;
        $this->user = new UserModel;
        $this->auth = new Auth;
        $this->lang = (new Language)->getLanguageFile();
        $this->purifier = new \HTMLPurifier(\HTMLPurifier_Config::createDefault());
    }
	public function processLdapLogin()
	{
		if (!empty($_POST['login']) && Auth::checkCsrfToken($_POST['csrf']))
        {
			$cleaned_email = strtolower($this->purifier->purify($_POST['email']));
			$cleaned_password = $this->purifier->purify($_POST['password']);
			
			$filter = "mail={$cleaned_email}";
			
			$ldap = new LdapAuth();
			$ldap->setLdapConn(\Config::LDAP_DOMAIN);
			$ldap->setLdapOption(LDAP_OPT_REFERRALS, 0);
			$ldap->setLdapOption(LDAP_OPT_PROTOCOL_VERSION, 3);
			$ldap->ldapBind(Config::LDAP_USERNM, Config::LDAP_PASSWD);
			
			$user_info = $ldap->ldapSearch($filter);
			$user_token = $user_info[0]["dn"];
			$user_guid = $user_info[0]["objectguid"][0];
			
			$access = false;
			if($ldap->ldapBind($user_token, $cleaned_password))
			{
				$entries = $ldap->ldapSearch($filter, array("memberof"));
				
				foreach($entries[0] as $key => $val)
				{
					if($key == 'memberof'){
						foreach($entries[0]['memberof'] as $grps) {
							// is user
							if(strpos($grps, $ldap->getLdapAuthGroup())) 
							{ 
								$access = true; 
								break; 
							};
						}
					}
				}
				if($access)
				{
					$this->model->removeLoginAttempt($user_guid);
					$ldap->ldapCreateUserSession($user_info);
					die($this->redirectLogin());
				}
			}
			else
			{
				$query_arr = array(
					'user_id' => $user_guid,
					'user_ip_adres' => $_SERVER['REMOTE_ADDR'],
					'user_ip_port' => $_SERVER['REMOTE_PORT'],
					'user_attempt_time' => time() ,
					'user_attempt_date_time' => date("Y-m-d H:i:s")
				);
	
				// Save login attempt in database
				$this->model->failedLoginAttempt($query_arr);
				Logger::logToFile(__FILE__, 2, "Login failed. User: " . $cleaned_email);
	
				return '<div class="alert alert-danger" ><b>' . $this->lang->loginmsg->id->label . '</b><br><span>' . $this->lang->loginmsg->id->msg . '</span></div>';

			}
        }
        else
        {
            return '<div class="alert alert-danger"><b>' . $this->lang->loginmsg->csrf->label . '</b><br><span>' . $this->lang->loginmsg->csrf->msg . '</span></div>';
        }		
	}
	
    public function processLogin()
    {
        $cleaned_email = strtolower($this->purifier->purify($_POST['email']));
        if (!empty($_POST['login']) && Auth::checkCsrfToken($_POST['csrf']))
        {
            $row = $this->user->getUserRow($cleaned_email);

            $login_ok = false;

            if ($row)
            {
                // Brute force prevention
                if (Auth::checkBrute($row['user_id']) === true)
                {
                    Logger::logToFile(__FILE__, 1, "Account: " . $row['user_email'] . " geblokkeerd");
                    return '<div class="alert alert-danger" ><b>' . $this->lang->loginmsg->lck->label . '</b><br><span>' . $this->lang->loginmsg->lck->msg . '</span></div>';
                }
                else
                {
                    // Check of user DEV role heeft.
                    $check_dev = true; //containsWord($row['Overige'], "DEV");
                    // Indien account status Blocked is log de inlog poging en stop het script.
                    if ($row['user_status'] === "Blocked")
                    {
                        Logger::logToFile(__FILE__, 2, "Login attempt. Blocked user: " . $cleaned_email);
                        return '<div class="alert alert-danger" ><b>' . $this->lang->loginmsg->blc->label . '</b><br><span>' . $this->lang->loginmsg->blc->msg . '</span></div>';

                    }
                    elseif (Config::APP_ENV == "OTAP" && $check_dev == false)
                    {
                        Logger::logToFile(__FILE__, 2, "Login attempt. Non DEV user: " . $cleaned_email);
                        return '<div class="alert alert-warning" ><b>' . $this->lang->loginmsg->dev->label . '</b><br><span><b>' . $this->lang->loginmsg->dev->msg . '</b></span></div>';
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
                //unset($row['user_email']);
                unset($row['user_role']);

                // Add user row to current session
                $_SESSION[Config::SES_NAME] = $row;

                // Remove all login attempt from user to prevent getting blocked.
                $id = $row['user_id'];
                $this->model->removeLoginAttempt($id);

                Logger::logToFile(__FILE__, 0, "Login success. User: " . $_SESSION[Config::SES_NAME]['user_email']);

                // Redirect the user to the private members-only page.
                die($this->redirectLogin());

            }
            else
            {
                $query_arr = array(
                    'user_id' => $row['user_id'],
                    'user_ip_adres' => $_SERVER['REMOTE_ADDR'],
                    'user_ip_port' => $_SERVER['REMOTE_PORT'],
                    'user_attempt_time' => time() ,
                    'user_attempt_date_time' => date("Y-m-d H:i:s")
                );

                // Save login attempt in database
                $this->model->failedLoginAttempt($query_arr);

                Logger::logToFile(__FILE__, 2, "Login failed. User: " . $cleaned_email);

                return '<div class="alert alert-danger" ><b>' . $this->lang->loginmsg->id->label . '</b><br><span>' . $this->lang->loginmsg->id->msg . '</span></div>';

            }
        }
        else
        {
            return '<div class="alert alert-danger"><b>' . $this->lang->loginmsg->csrf->label . '</b><br><span>' . $this->lang->loginmsg->csrf->msg . '</span></div>';
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

            Logger::logToFile(__FILE__, 0, "Logout success. User: " . $cleaned_email);

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
        $cleaned_email = strtolower($this->purifier->purify($_POST['email']));

        if (!empty($_POST['request']) && Auth::checkCsrfToken($_POST['csrf']))
        {
            // Get current user row
            $row = $this->user->getUserRow($cleaned_email);

            if ($row)
            {
				// If there already has been requested a token don't request again but check if the token is expired
				if ($this->model->checkRecoverTokenExsists() > 0)
				{
					$row_token = $this->model->getRecoverToken($row['user_id']);
					
					if ($_SERVER["REQUEST_TIME"] - $row_token['user_token_date_time'] > $this->token_expiration)
					{
						// If token expired remove record
						$this->model->deleteRecoverToken($row_token['user_id']);
					}
					else
					{
						return '<div class="alert alert-danger"><b>' . $this->lang->loginmsg->tok->mul->label . '</b><br><span >' . $this->lang->loginmsg->tok->mul->msg . '</span></div>';					
					}
					
				}
				
                // Generate random token
                $token = openssl_random_pseudo_bytes(32);
                $token = bin2hex($token);

                $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));

                $token_hash = hash('sha256', $token . $salt);

                for ($round = 0;$round < 65536;$round++)
                {
                    $token_hash = hash('sha256', $token_hash . $salt);
                }


                $email_values = array(
                    'user_name' => $row['user_name'] . " " . $row['user_last_name'],
                    'recover_link' => '<a class="link" href="http://' . $_SERVER['HTTP_HOST'] . '/login/gentoken/' . $token . '/' . $_SESSION['_token'] . '/' . $row['user_id'] . '">Recover token</a>'
                );

                $mail_body = Mailer::build('gen_token', $email_values);
				$send_mail = Mailer::send(Config::APP_TITLE . ' ' . $this->lang->send_mail->recover_token->subject, $mail_body, array($row['user_email']));
				
                if($send_mail == 0)
				{
					$this->model->deleteRecoverToken($row['user_id']);

                    return array(
                        'view' => $this->return_view,
                        'msg' => '<div class="alert alert-danger"><b>' . $this->lang->loginmsg->tok->notsend->label . '</b><br><span>' . $this->lang->loginmsg->tok->notsend->msg . '</span></div>'
                    );					
				}
				
                if($send_mail)
				{

					$query_params = array(
						'user_id' => $row['user_id'],
						'user_token_date_time' => time() ,
						'user_token_date_request' => date("Y-m-d H:i:s") ,
						'user_token_hash' => $token_hash,
						'user_token_salt' => $salt
					);
	
					$this->model->saveRecoverToken($query_params);	
					
					Logger::logToFile(__FILE__, 0, "Token request successful user: " . $row['user_email']);

					return '<div class="alert alert-success" ><b>' . $this->lang->loginmsg->tok->suc->label . '</b><br><span>' . $this->lang->loginmsg->tok->suc->msg . '</span></div>';						
				}
				else 
				{
					Logger::logToFile(__FILE__, 0, 'Message could not be sent.');
					$this->model->deleteRecoverToken($row['user_id']);
					return '<div class="alert alert-danger"><b>' . $this->lang->loginmsg->tok->err->label . '</b><br><span>' . $this->lang->loginmsg->tok->err->msg . '</span></div>';
				}

            }
            else
            {
                return '<div class="alert alert-danger" ><b>' . $this->lang->loginmsg->uknw->label . '</b><br><span>' . $this->lang->loginmsg->uknw->msg . '</span></div>';
            }
        }
        else
        {
            return '<div class="alert alert-danger"><b>' . $this->lang->loginmsg->tok->err->label . '</b><br><span>' . $this->lang->loginmsg->tok->err->msg . '</span></div>';

        }
    }

    public function processPassReset($token, $csrf_token, $user_id)
    {

        $cleaned_user_id = $this->purifier->purify($user_id);

        if (Auth::checkCsrfToken($csrf_token))
        {

            $row_token = $this->model->getRecoverToken($cleaned_user_id);

            $token_auth = false;

            if ($row_token)
            {
                if ($_SERVER["REQUEST_TIME"] - $row_token['user_token_date_time'] > $this->token_expiration)
                {

                    Logger::logToFile(__FILE__, 0, "Token expired user " . $row_token['user_id']);

                    // If token expired remove record
                    $this->model->deleteRecoverToken($cleaned_user_id);

                    return '<div class="alert alert-danger" ><b>' . $this->lang->loginmsg->tok->exp->label . '</b><br><span>' . $this->lang->loginmsg->tok->exp->msg . '</span></div>';
                }
                else
                {
                    // Re-hash token
                    $check_token_hash = hash('sha256', $token . $row_token['user_token_salt']);
                    for ($round = 0;$round < 65536;$round++)
                    {
                        $check_token_hash = hash('sha256', $check_token_hash . $row_token['user_token_salt']);
                    }
					// Compare hash with token hash from table
                    if (hash_equals($check_token_hash, $row_token['user_token_hash']))
                    {
                        $token_auth = true;
                    }
                }
            }
            else
            {
                return '<div class="alert alert-danger" ><b>' . $this->lang->loginmsg->tok->uknw->label . '</b><br><span>' . $this->lang->loginmsg->tok->uknw->msg . '</span></div>';
            }

            if ($token_auth)
            {
                // Generate random password seed
                $gen_password = Auth::genPassSeed(2);
                $hash = password_hash($gen_password, PASSWORD_ARGON2I);

                $row = $this->user->getUserRow($cleaned_user_id);

                $query_params = array(
                    'user_password' => $hash,
                    'user_new' => 1
                );

                $email_values = array(
                    'user_name' => $row['user_name'] . " " . $row['user_last_name'],
                    'link' => '<a class="link" href="http://' . $_SERVER['HTTP_HOST'] . '">'.Config::APP_TITLE.'</a>',
                    'gen_password' => $gen_password,
                );
                
                $mail_body = Mailer::build('password_reset', $email_values);
				$send_mail = Mailer::send(Config::APP_TITLE . ' ' . $this->lang->send_mail->new_password->subject, $mail_body, array($row['user_email']));
                
				if($send_mail)
				{
					if ($this->user->update($query_params, $cleaned_user_id))
					{	
						Logger::logToFile(__FILE__, 0, "Password reset. Mail send to user: " . $row['user_email']);
		        
						$this->model->deleteRecoverToken($cleaned_user_id);
		        
						return '<div class="alert alert-success" ><b >' . $this->lang->loginmsg->res->suc->label . '</b><br><span>' . $this->lang->loginmsg->res->suc->msg . '</span></div>';
					}
					else
					{
						Logger::logToFile(__FILE__, 0, "Password not updated for user: " . $row['user_email']);
						return '<div class="alert alert-danger"><b>' . $this->lang->loginmsg->res->err->label . '</b><br><span>' . $this->lang->loginmsg->res->err->msg . '</span></div>';
					}						
				}
				else 
				{
					Logger::logToFile(__FILE__, 0, 'Message could not be sent.');
					return '<div class="alert alert-danger"><b>' . $this->lang->loginmsg->res->notsend->label . '</b><br><span>' . $this->lang->loginmsg->res->notsend->msg . '</span></div>';
				}
            }
            else
            {
                return '<div class="alert alert-danger" ><b>' . $this->lang->loginmsg->tok->inv->label . '</b><br><span>' . $this->lang->loginmsg->tok->inv->msg . '</span></div>';

            }
        }
        else
        {
			// CRSF token invalid but ask user to request a new token
			return '<div class="alert alert-danger"><b>' . $this->lang->loginmsg->res->err->label . '</b><br><span>' . $this->lang->loginmsg->res->err->msg . '</span></div>';
        }
    }

    public function redirectLogin()
    {
        // We check to see whether the user is logged in or not
        if (!Auth::checkAuth())
        {
            // Remember that this die statement is absolutely critical.  Without it,
            // people can view your members-only content without logging in.
            die(Helper::redirect('/'));
        }

        // Update Lastaccess kolom in users database
        // TODO: Check if user exist
        $connected = $this->user->updateUserLastAccess(Auth::getAuthUser());
        $user_row = $this->user->getUserRow(Auth::getAuthUser());

        //Redirect naar juiste index pagina op basis van Userrole
        if ($user_row['user_role'] == 1 && $connected)
        {
            Helper::redirect('/home');
        }
        elseif ($user_row['user_role'] == 2 && $connected)
        {
            Helper::redirect('/home');
        }
        else
        {
            Helper::redirect('/home');
        }
    }
}

