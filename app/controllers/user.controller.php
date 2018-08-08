<?php
namespace App\Controllers;

use \Config;
use App\Models\UserModel;
use App\Classes\Logger;
use App\Classes\Helper;
use App\Classes\Auth;

class UserController extends BaseController
{
    private $user;

    function __construct()
    {
		parent::__construct();
        $this->user = new UserModel;
    }

    public function index()
    {

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
            if (isset($_POST['user_language']) && !empty($_POST['user_language'])) {
                $query_data['user_language'] = $_POST['user_language'];
            }
            if ($this->user->update( $query_data, $cleaned_user_id)) {
                // Log to file
                $msg = "User " . $_POST['user_email'] . " updatet by " . Auth::getAuthUser();
                $err_lvl = 0;

				$this->res = $this->returnMsg($this->lang->swal->title->success, '', 'success');
            }
        } else {
			$this->res = $this->returnMsg($this->lang->swal->title->error, $this->lang->users->edit->msg->err->msg . $send, 'error'); 
        }

        Logger::logToFile(__FILE__, $err_lvl, $msg);

        Helper::jsonArr($this->res);
    }

    public function getUserInfo()
    {
        // Get current dealer row
        $user_row = $this->user->getUserRow($_SESSION[Config::SES_NAME]['user_id']);

        if ($user_row['user_status'] == 1) {
            $status = '<span class="label label-success"  >' . $this->lang->users->status->s1 . '</span>';
        } elseif ($user_row['user_status'] == 2) {
            $status = '<span class="label label-danger" >' . $this->lang->users->status->s2 . '</span>';
        } else {
            $status = '<span class="label label-warning" >' . $this->lang->users->status->s3 . '</span>';
        }

        $res = array(
            'user_status'       => $status,
            'user_id' 	        => $user_row['user_id'],
            'user_language' 	=> $user_row['user_language'],
            'user_name' 	    => $user_row['user_name'],
            'user_last_name' 	=> $user_row['user_last_name'],
            'user_email' 	    => $user_row['user_email'],
            'user_last_access' 	=> $user_row['user_last_access'],
            'user_role'         => $this->user->getUserRole($user_row['user_id']),
        );

        Helper::jsonArr($res);
    }	
	
    public function updateUserPass()
    {

        $password_post = $_POST['password'];
        $cleaned_user_id = Helper::purifyInput(Auth::getAuthUser());

        if (Auth::checkCsrfToken($_POST['csrf']) && !empty($password_post))
        {
            $row = $this->user->getUserRow($cleaned_user_id);

            if ($row['user_new'] == 1)
            {

                // First time logging in. Change password and verify user
                $query_params = array(
                    'user_password' => password_hash($password_post, PASSWORD_ARGON2I) ,
                    'user_new' => 0,
                    'user_status' => 1
                );

                try
                {
                    if ($this->user->update($query_params, $cleaned_user_id))
                    {
                        Logger::logToFile(__FILE__, 0, "Password user: " . $row['user_email'] . " changed");
						$this->res = $this->returnMsg($this->lang->swal->title->success, '', 'success');

                        // Update session with new user status
                        $_SESSION[Config::SES_NAME]['user_new'] = $query_params['user_new'];

                        Helper::jsonArr($this->res);
                    }
                }
                catch(Exception $ex)
                {
                    Logger::logToFile(__FILE__, 1, 'Regel: ' . $ex->getLine() . ' Bestand: ' . $ex->getFile() . ' Error: ' . $ex->getMessage());
					$this->res = $this->returnMsg($this->lang->swal->title->error, $this->lang->users->edit->msg->err->msg, 'error'); 

                    Helper::jsonArr($this->res);
                }

            }
        }
        else
        {
            $this->res = $this->returnMsg($this->lang->swal->title->error, $this->lang->users->edit->msg->err->msg, 'error'); 

            Helper::jsonArr($this->res);
        }
    }
}

