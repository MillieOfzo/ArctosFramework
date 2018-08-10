<?php
namespace App\Controllers;

use \Config;
use \SafeMySQL;
use App\Models\UserModel;
use App\Classes\Logger;
use App\Classes\Helper;
use App\Classes\Auth;
use App\Classes\Mailer;
use App\Classes\SSP;

class UsersController extends BaseController
{
    private $user;
    private $conn;
    private $auth_user;

    function __construct()
    {
		parent::__construct();
        $this->user = new UserModel;
		$this->conn = new SafeMySQL;
		$this->auth_user = htmlentities($_SESSION[Config::SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
    }

    public function index()
    {

    }

    public function newUser()
    {
        $cleaned_email = Helper::purifyInput($_POST['new_user_email']);

        $user_row = $this->user->getUserRow($cleaned_email);

        // Check if mail exsists
        if ($user_row['user_email']) {
			$this->res 		= $this->returnMsg($this->lang->swal->title->warning, '<b>' . $_POST['new_user_email'] . '</b> already exists', 'warning');
            Helper::jsonArr($this->res);
        }

        if (Auth::checkCsrfToken($_POST['csrf'])) {

            // Generate random password
            $gen_password = Auth::genPassSeed(2);
            $hash         = password_hash($gen_password, PASSWORD_ARGON2I);

            $query_data = array(
                'user_name' => $_POST['new_user_name'],
                'user_last_name' => $_POST['new_user_last_name'],
                'user_email' => $_POST['new_user_email'],
                'user_password' => $hash
            );
            if (isset($_POST['user_language']) && !empty($_POST['user_language'])) {
                $query_data['user_language'] = $_POST['user_language'];
            }
            if($this->user->create($query_data))
            {
                $email_values = array(
                    'user_name' => $_POST['new_user_name'] . " " . $_POST['new_user_last_name'],
                    'login_link' => '<a style="color:#eda02b; text-decoration:none;" href="http://' . $_SERVER['HTTP_HOST'] . '">' . Config::APP_TITLE . '</a>',
                    'gen_password' => $gen_password,
					'app_name' => Config::APP_TITLE . ' team',
                    'contact_mail' => '<a style="color:#eda02b; text-decoration:none;" href="mailto:'.Config::APP_EMAIL.'" target="_blank">'.Config::APP_EMAIL.'</a>',
                );

                $mail_body = Mailer::build('new_user', $email_values);
                $send_mail = Mailer::send('Welcome too '.Config::APP_TITLE, $mail_body, array($_POST['new_user_email']));

                if ($send_mail) {
                    $send = $this->lang->users->new->msg->login_email->suc_msg . ' <b>' . $_POST['new_user_email'] . '</b>';
                } else {
                    $send = $this->lang->users->new->msg->login_email->err_msg . ' <b>' . $_POST['new_user_email'] . '</b>';
                }

                // Log to file
                $msg     = "Nieuwe user " . $_POST['new_user_email'] . " aangemaakt door " . $this->auth_user;
                $err_lvl = 0;
				$this->res 		= $this->returnMsg($this->lang->swal->title->success, $send, 'success');
            }

            
        } else {
            $msg     = "New user " . $_POST['new_user_email'] . " not created ";
            $err_lvl = 2;
			$this->res 		= $this->returnMsg($this->lang->swal->title->error, $this->lang->users->edit->msg->err->msg . $send, 'error');           
        }
        
        Logger::logToFile(__FILE__, $err_lvl, $msg);

        Helper::jsonArr($this->res);
    }
    
    public function updateUser()
    {
        $cleaned_user_id = Helper::purifyInput($_POST['user_id']);

        if (Auth::checkCsrfToken($_POST['csrf'])) {
            $query_data = array(
                'user_name' => $_POST['user_name'],
                'user_last_name' => $_POST['user_last_name'],
                'user_email' => $_POST['user_email']
            );
            if (isset($_POST['user_status']) && !empty($_POST['user_status'])) {
                $query_data['user_status'] = $_POST['user_status'];
            }
            if (isset($_POST['user_role']) && !empty($_POST['user_role'])) {
                $query_data['user_role'] = $_POST['user_role'];
            }
            if (isset($_POST['user_language']) && !empty($_POST['user_language'])) {
                $query_data['user_language'] = $_POST['user_language'];
            }
			
            if ($this->user->update( $query_data, $cleaned_user_id)) {
                // Log to file
                $msg = "User " . $_POST['user_email'] . " updatet by " . $this->auth_user;
                $err_lvl = 0;
				
				$this->res 		= $this->returnMsg($this->lang->swal->title->success, '', 'success');
            }
        } else {
			$this->res 		= $this->returnMsg($this->lang->swal->title->error, $this->lang->users->edit->msg->err->msg, 'error');
        }
        
        Logger::logToFile(__FILE__, $err_lvl, $msg);

        Helper::jsonArr($this->res);
    }
    
    public function deleteUser()
    {
        $cleaned_user_id = Helper::purifyInput($_POST['user_id']);

        $user_row = $this->user->getUserRow($cleaned_user_id);

        if (Auth::checkCsrfToken($_POST['csrf'])) {
			
			$this->user->delete($cleaned_user_id);
			
            // Log to file
            $msg     = "User " . $user_row['user_email'] . " removed by " . $this->auth_user;
            $err_lvl = 0;
			$this->res 		= $this->returnMsg($this->lang->swal->title->success, '', 'success');

            
        } else {
            $msg          = "User " . $user_row['user_email'] . " NOT removed";
            $err_lvl      = 2;
            $this->res 		= $this->returnMsg($this->lang->swal->title->error, $this->lang->users->edit->msg->err->msg, 'error'); 
        }
        
        Logger::logToFile(__FILE__, $err_lvl, $msg);

        Helper::jsonArr($this->res);
    }	
	
	public function resetUserPass()
	{
        $cleaned_user_id = Helper::purifyInput($_POST['user_id']);

        if (Auth::checkCsrfToken($_POST['csrf']))
        {
            $row = $this->user->getUserRow($cleaned_user_id);

            if ($row)
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
					'user_name_authenticator' => htmlentities($_SESSION[Config::SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8'),
                    'link' => '<a style="color:#eda02b; text-decoration:none;" href="http://' . $_SERVER['HTTP_HOST'] . '">'.Config::APP_TITLE.'</a>',
                    'gen_password' => $gen_password,
					'app_name' => Config::APP_TITLE . ' team',
                    'contact_mail' => '<a style="color:#eda02b; text-decoration:none;" href="mailto:'.Config::APP_EMAIL.'" target="_blank">'.Config::APP_EMAIL.'</a>',
                );
                
                $mail_body = Mailer::build('password_reset_request', $email_values);
				$send_mail = Mailer::send(Config::APP_TITLE . ' ' . $this->lang->send_mail->password_request->subject, $mail_body, array($row['user_email']));
				
                if($send_mail == 0)
				{
                    $this->res 	= $this->returnMsg( $this->lang->loginmsg->tok->notsend->label, $this->lang->loginmsg->tok->notsend->msg, 'danger');			
				}         
				
				if($send_mail)
				{
					if ($this->user->update($query_params, $cleaned_user_id))
					{	
						Logger::logToFile(__FILE__, 0, "Password reset. Mail send to user: " . $row['user_email']);
						$this->res 	= $this->returnMsg( $this->lang->loginmsg->res->suc->label, $this->lang->loginmsg->res->suc->msg );
					}
					else
					{
						Logger::logToFile(__FILE__, 0, "Password not updated for user: " . $row['user_email']);
						$this->res 	= $this->returnMsg( $this->lang->loginmsg->res->err->label, $this->lang->loginmsg->res->err->msg, 'danger');
					}				
				}
				else 
				{
					Logger::logToFile(__FILE__, 0, 'Message could not be sent.');
					$this->res 	= $this->returnMsg( $this->lang->loginmsg->res->notsend->label, $this->lang->loginmsg->res->notsend->msg, 'danger');
				}
            }
            else
            {
                $this->res 	= $this->returnMsg( $this->lang->loginmsg->tok->inv->label, $this->lang->loginmsg->tok->inv->msg, 'danger');

            }
        }
        else
        {
			// CRSF token invalid but ask user to request a new token
			$this->res 	= $this->returnMsg( $this->lang->loginmsg->res->err->label, $this->lang->loginmsg->res->err->msg, 'danger');
        }
		Helper::jsonArr($this->res);
	}
	
	public function getTableUsers()
    {

        $db   = @new \PDO('mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8', Config::DB_USER, Config::DB_PASS, array(
            \PDO::ATTR_PERSISTENT => true
        ));
        
        $columns = array(
            array(
                'db' => "user_id",
                'dt' => 'DT_RowClass'
            ),
            array(
                'db' => "user_id",
                'dt' => 0
            ),			
            array(
                'db' => "user_name",
                'dt' => 1
            ),
            array(
                'db' => "user_last_name",
                'dt' => 2
            ),			
            array(
                'db' => "user_email",
                'dt' => 3
            ),
            array(
                'db' => "user_role",
                'dt' => 4,
                'formatter' => function($d, $row)
                {
                    return $this->conn->getOne('SELECT role_name FROM app_role WHERE id = ?i', $d);
                }
            ),
            array(
                'db' => "user_status",
                'dt' => 5,
                'formatter' => function($d, $row)
                {
                    $status_name = $this->conn->getOne('SELECT status_name FROM app_user_status WHERE id = ?i', (int) $d);
                    if ($d == 1) {
                        $status = '<span class="badge badge-success"  >' . $this->lang->users->status->s1 . '</span>';
                    } elseif ($d == 2) {
                        $status = '<span class="badge badge-danger" >' . $this->lang->users->status->s2 . '</span>';
                    } else {
                        $status = '<span class="badge badge-warning" >' . $this->lang->users->status->s3 . '</span>';
                    }
                    return $status;
                }
            ),
            array(
                'db' => "user_last_access",
                'dt' => 6,
				'formatter' => function($d, $row)
                {
                    // display users who were active in last 10 minutes  
                    $minutes = 480;
                    $t       = date('Y-m-d H:i:s', time() - $minutes * 60);
                    if ($this->conn->getOne("select 1 from app_users WHERE user_last_access > '" . $t . "' AND `user_name` = '" . $row[2] . "' AND `user_status` NOT IN (2)")) {
                        $active = '<i class="fa fa-circle text-navy"></i>';
                    } else {
                        $active = '<i class="fa fa-circle text-danger"></i>';
                    }
                    return $active . ' ' . $d ;
                }				
            ),			
            array(
                'db' => "user_id",
                'dt' => 7,
                'formatter' => function($d, $row)
                {
                    $edit = "<a class='btn btn-success btn-xs' id='edit' value='" . $row[0] . "' rel='" . $row[2] . "' >" . $this->lang->users->actions->edit . "</a>";
					$edit .= " <a class='btn btn-success btn-xs' id='password_reset' value='" . $row[0] . "' rel='" . $row[2] . "' >" . $this->lang->users->actions->password . "</a>";					
                    if($d != Auth::getAuthUser())
                    {
                        $edit .= " <a class='btn btn-danger btn-xs' id='delete' value='" . $row[0] . "' rel='" . $row[2] . "' >" . $this->lang->users->actions->delete . "</a>";
                    }
                    return $edit;
                }
            ),
            array(
                'db' => "user_role",
                'dt' => 8
            ),
            array(
                'db' => "user_status",
                'dt' => 9
            ),
            array(
                'db' => "user_language",
                'dt' => 10
            )			
        );
        
		Helper::jsonArr(SSP::complex($_GET, $db, 'app_users', 'user_id', $columns, $whereResult = null, $whereAll = null));
    }
}

