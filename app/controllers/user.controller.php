<?php
namespace App\Controllers;

use \Config;
use App\Models\UserModel;
use App\Classes\Logger;
use App\Classes\Helper;
use App\Classes\Auth;
use App\Classes\Language;

class UserController
{
    private $model;
    private $auth;
    private $lang;
    private $purifier;

    function __construct()
    {
        $this->model = new UserModel;
        $this->auth = new Auth;
        $this->lang = (new Language)->getLanguageFile();
        $this->purifier = new \HTMLPurifier(\HTMLPurifier_Config::createDefault());
    }

    public function index()
    {

    }

    public function updateUserPass()
    {

        $password_post = $_POST['password'];
        $cleaned_user_id = $this->purifier->purify(Auth::getAuthUser());

        if (Auth::checkCsrfToken($_POST['csrf']) && !empty($password_post))
        {
            $row = $this->model->getUserRow($cleaned_user_id);

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
                    if ($this->model->update($query_params, $cleaned_user_id))
                    {
                        Logger::logToFile(__FILE__, 0, "Password user: " . $row['user_email'] . " changed");

                        $res['label'] = $this->lang->user->acc_update->msg->suc->label;
                        $res['text'] = $this->lang->user->acc_update->msg->suc->msg;
                        $res['type'] = 'success';

                        // Update session with new user status
                        $_SESSION[Config::SES_NAME]['user_new'] = $query_params['user_new'];

                        Helper::jsonArr($res);
                    }
                }
                catch(Exception $ex)
                {
                    Logger::logToFile(__FILE__, 1, 'Regel: ' . $ex->getLine() . ' Bestand: ' . $ex->getFile() . ' Error: ' . $ex->getMessage());

                    $res['label'] = $this->lang->user->acc_update->msg->err->label;
                    $res['text'] = $this->lang->user->acc_update->msg->err->msg;
                    $res['type'] = 'error';

                    Helper::jsonArr($res);
                }

            }
        }
        else
        {
            $res['label'] = $this->lang->user->acc_update->msg->err->label;
            $res['text'] = $this->lang->user->acc_update->msg->err->msg;
            $res['type'] = 'error';

            Helper::jsonArr($res);
        }
    }
}

