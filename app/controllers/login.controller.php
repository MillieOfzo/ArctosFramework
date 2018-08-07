<?php
 
namespace App\Controllers;

use \Config;
use App\Models\LoginModel;
use App\Models\UserModel;
use App\Classes\Logger;
use App\Classes\Helper;
use App\Classes\Auth;
use App\Classes\Mailer;
use App\Classes\LdapAuth;
use App\Classes\SessionManager;

class LoginController extends BaseController
{
    private $model;
    private $auth;
	
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
		parent::__construct();
        $this->login = new LoginModel;
        $this->user = new UserModel;
    }
	
	public function processLdapLogin()
	{
		if (!empty($_POST['login']) && Auth::checkCsrfToken($_POST['csrf']))
        {
			$cleaned_email = strtolower(Helper::purifyInput($_POST['email']));
			$cleaned_password = Helper::purifyInput($_POST['password']);
			
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
					$this->login->removeLoginAttempt($user_guid);
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
				$this->login->failedLoginAttempt($query_arr);
				Logger::logToFile(__FILE__, 2, "Login failed. User: " . $cleaned_email);
	
				return $this->setResponseMsg( $this->lang->loginmsg->id->label, $this->lang->loginmsg->id->msg, 'danger');

			}
        }
        else
        {
            return $this->setResponseMsg($this->lang->loginmsg->csrf->label, $this->lang->loginmsg->csrf->msg, 'danger');
        }		
	}
	
    public function processLogin()
    {
        $cleaned_email = strtolower(Helper::purifyInput($_POST['email']));
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
                    return $this->setResponseMsg($this->lang->loginmsg->lck->label, $this->lang->loginmsg->lck->msg, 'danger');
                }
                else
                {
                    // Check of user DEV role heeft.
                    $check_dev = true; //containsWord($row['Overige'], "DEV");
                    // Indien account status Blocked is log de inlog poging en stop het script.
                    if ($row['user_status'] === "Blocked")
                    {
                        Logger::logToFile(__FILE__, 2, "Login attempt. Blocked user: " . $cleaned_email);
                        return $this->setResponseMsg($this->lang->loginmsg->blc->label, $this->lang->loginmsg->blc->msg, 'danger');

                    }
                    elseif (Config::APP_ENV == "OTAP" && $check_dev == false)
                    {
                        Logger::logToFile(__FILE__, 2, "Login attempt. Non DEV user: " . $cleaned_email);
                        return $this->setResponseMsg( $this->lang->loginmsg->dev->label, $this->lang->loginmsg->dev->msg, 'warning');
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
                $this->login->removeLoginAttempt($id);

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
                $this->login->failedLoginAttempt($query_arr);

                Logger::logToFile(__FILE__, 2, "Login failed. User: " . $cleaned_email);

                return $this->setResponseMsg( $this->lang->loginmsg->id->label, $this->lang->loginmsg->id->msg, 'danger');

            }
        }
        else
        {
            return $this->setResponseMsg($this->lang->loginmsg->csrf->label, $this->lang->loginmsg->csrf->msg, 'danger');
        }
    }

    public function processLogout($csrf)
    {
        if (isset($csrf) && Auth::checkCsrfToken($csrf))
        {
            $user_email = htmlspecialchars($_SESSION[Config::SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
            $cleaned_email = strtolower(Helper::purifyInput($user_email));

			$session = new SessionManager();
			$session->sessionDestroy();

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
        $cleaned_email = strtolower(Helper::purifyInput($_POST['email']));

        if (!empty($_POST['request']) && Auth::checkCsrfToken($_POST['csrf']))
        {
            // Get current user row
            $row = $this->user->getUserRow($cleaned_email);

            if ($row)
            {
				// If there already has been requested a token don't request again but check if the token is expired
				if ($this->login->checkRecoverTokenExsists() > 0)
				{
					$row_token = $this->login->getRecoverToken($row['user_id']);
					
					if ($_SERVER["REQUEST_TIME"] - $row_token['user_token_date_time'] > $this->token_expiration)
					{
						// If token expired remove record
						$this->login->deleteRecoverToken($row_token['user_id']);
					}
					else
					{
						return $this->setResponseMsg( $this->lang->loginmsg->tok->mul->label, $this->lang->loginmsg->tok->mul->msg, 'danger');					
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
					$this->login->deleteRecoverToken($row['user_id']);

                    return $this->setResponseMsg( $this->lang->loginmsg->tok->notsend->label, $this->lang->loginmsg->tok->notsend->msg, 'danger');			
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
	
					$this->login->saveRecoverToken($query_params);	
					
					Logger::logToFile(__FILE__, 0, "Token request successful user: " . $row['user_email']);

					return $this->setResponseMsg( $this->lang->loginmsg->tok->suc->label, $this->lang->loginmsg->tok->suc->msg);						
				}
				else 
				{
					Logger::logToFile(__FILE__, 0, 'Message could not be sent.');
					$this->login->deleteRecoverToken($row['user_id']);
					return $this->setResponseMsg( $this->lang->loginmsg->tok->err->label, $this->lang->loginmsg->tok->err->msg, 'danger');
				}
            }
            else
            {
                return $this->setResponseMsg( $this->lang->loginmsg->uknw->label, $this->lang->loginmsg->uknw->msg, 'danger');
            }
        }
        else
        {
            return $this->setResponseMsg( $this->lang->loginmsg->tok->err->label, $this->lang->loginmsg->tok->err->msg, 'danger');

        }
    }

    public function processPassReset($token, $csrf_token, $user_id)
    {

        $cleaned_user_id = Helper::purifyInput($user_id);

        if (Auth::checkCsrfToken($csrf_token))
        {

            $row_token = $this->login->getRecoverToken($cleaned_user_id);

            $token_auth = false;

            if ($row_token)
            {
                if ($_SERVER["REQUEST_TIME"] - $row_token['user_token_date_time'] > $this->token_expiration)
                {

                    Logger::logToFile(__FILE__, 0, "Token expired user " . $row_token['user_id']);

                    // If token expired remove record
                    $this->login->deleteRecoverToken($cleaned_user_id);

                    return $this->setResponseMsg( $this->lang->loginmsg->tok->exp->label, $this->lang->loginmsg->tok->exp->msg, 'danger');
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
                return $this->setResponseMsg( $this->lang->loginmsg->tok->uknw->label, $this->lang->loginmsg->tok->uknw->msg, 'danger');
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
				
                if($send_mail == 0)
				{
					$this->login->deleteRecoverToken($row['user_id']);
                    return $this->setResponseMsg( $this->lang->loginmsg->tok->notsend->label, $this->lang->loginmsg->tok->notsend->msg, 'danger');			
				}         
				
				if($send_mail)
				{
					if ($this->user->update($query_params, $cleaned_user_id))
					{	
						Logger::logToFile(__FILE__, 0, "Password reset. Mail send to user: " . $row['user_email']);
						$this->login->deleteRecoverToken($cleaned_user_id);
						return $this->setResponseMsg( $this->lang->loginmsg->res->suc->label, $this->lang->loginmsg->res->suc->msg );
					}
					else
					{
						Logger::logToFile(__FILE__, 0, "Password not updated for user: " . $row['user_email']);
						return $this->setResponseMsg( $this->lang->loginmsg->res->err->label, $this->lang->loginmsg->res->err->msg, 'danger');
					}				
				}
				else 
				{
					Logger::logToFile(__FILE__, 0, 'Message could not be sent.');
					return $this->setResponseMsg( $this->lang->loginmsg->res->notsend->label, $this->lang->loginmsg->res->notsend->msg, 'danger');
				}
            }
            else
            {
                return $this->setResponseMsg( $this->lang->loginmsg->tok->inv->label, $this->lang->loginmsg->tok->inv->msg, 'danger');

            }
        }
        else
        {
			// CRSF token invalid but ask user to request a new token
			return $this->setResponseMsg( $this->lang->loginmsg->res->err->label, $this->lang->loginmsg->res->err->msg, 'danger');
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

        // Redirect naar juiste index pagina op basis van Userrole
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

