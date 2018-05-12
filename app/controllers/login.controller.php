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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class LoginController
{
    private $model;
    private $auth;
    private $lang;
    private $purifier;

    function __construct()
    {
        $this->model = new LoginModel;
        $this->user = new UserModel;
        $this->auth = new Auth;
        $this->lang = (new Language)->getLanguageFile();
        $this->purifier = new \HTMLPurifier(\HTMLPurifier_Config::createDefault());
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
                // Brute force preventie
                if ($this->auth->checkBrute($row['user_id']) === true)
                {
                    Logger::logToFile(__FILE__, 1, "Account: " . $row['user_email'] . " geblokkeerd");
                    return '<div class="alert alert-danger" ><font color="red"><b>' . $this->lang->loginmsg->lck->label . '</b></font><br><span>' . $this->lang->loginmsg->lck->msg . '</span></div>';
                }
                else
                {
                    // Check of user DEV role heeft.
                    $check_dev = true; //containsWord($row['Overige'], "DEV");
                    // Indien account status Blocked is log de inlog poging en stop het script.
                    if ($row['user_status'] === "Blocked")
                    {
                        Logger::logToFile(__FILE__, 2, "Login attempt. Blocked user: " . $cleaned_email);
                        return '<div class="alert alert-danger" ><font color="red"><b>' . $this->lang->loginmsg->blc->label . '</b></font><br><span>' . $this->lang->loginmsg->blc->msg . '</span></div>';

                    }
                    elseif (Config::APP_ENV == "OTAP" && $check_dev == false)
                    {
                        Logger::logToFile(__FILE__, 2, "Login attempt. Non DEV user: " . $cleaned_email);
                        return '<div class="alert alert-warning" ><font color="orange"><b>' . $this->lang->loginmsg->dev->label . '</b></font><br><span><b>' . $this->lang->loginmsg->dev->msg . '</b></span></div>';
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

                return '<div class="alert alert-danger" ><font color="red"><b>' . $this->lang->loginmsg->id->label . '</b></font><br><span>' . $this->lang->loginmsg->id->msg . '</span></div>';

            }
        }
        else
        {
            return '<div class="alert alert-danger" data-i18n="[html]login.res.err"><font color="red"><b>' . $this->lang->loginmsg->csrf->label . '</b></font><br><span>' . $this->lang->loginmsg->csrf->msg . '</span></div>';
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
            // If there already has been requested a token don't do it again
            if ($this->model->checkRecoverTokenExsists() > 0)
            {
                return '<div class="alert alert-danger"><font color="red"><b>' . $this->lang->loginmsg->tok->mul->label . '</b></font><br><span >' . $this->lang->loginmsg->tok->mul->msg . '</span></div>';
            }
            // Get current user row
            $row = $this->user->getUserRow($cleaned_email);

            if ($row)
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

                $query_params = array(
                    'user_id' => $row['user_id'],
                    'user_token_date_time' => time() ,
                    'user_token_date_request' => date("Y-m-d H:i:s") ,
                    'user_token_hash' => $token_hash,
                    'user_token_salt' => $salt
                );

                $this->model->saveRecoverToken($query_params);

                // Enkel voor testing
                //die(header("Location: ../token.php?rec=".$token."&id=".$row['Initialen']));
                $email_values = array(
                    'user_name' => $row['user_name'] . " " . $row['user_last_name'],
                    'recover_link' => '<a class="link" href="' . $_SERVER['HTTP_HOST'] . '/login/gentoken/' . $token . '/' . $_SESSION['_token'] . '/' . $row['user_id'] . '">Recover token</a>'
                );

                $mail_body = Mailer::build('gen_token', $email_values);

                $mail = new PHPMailer(true);
                try
                {
                    //Server settings
                    //$mail->SMTPDebug = 2;
                    $mail->isSMTP();
                    $mail->SMTPAutoTLS = false;
                    $mail->Host = Config::SMTP_HOST;
                    $mail->Port = Config::SMTP_PORT;

                    //Recipients
                    $mail->SetFrom(Config::APP_EMAIL);
                    $mail->AddAddress($cleaned_email);

                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = Config::APP_TITLE . ' ' . $this->lang->send_mail->recover_token->subject;
                    $mail->Body = $mail_body;
                    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                    $mail->send();

                    Logger::logToFile(__FILE__, 0, "Token request successful user: " . $row['user_email']);

                    return '<div class="alert alert-success" ><font color="green"><b>' . $this->lang->loginmsg->tok->suc->label . '</b></font><br><span>' . $this->lang->loginmsg->tok->suc->msg . '</span></div>';

                }
                catch(Exception $e)
                {

                    Logger::logToFile(__FILE__, 0, 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);

                    return '<div class="alert alert-danger"><font color="red"><b>' . $this->lang->loginmsg->tok->err->label . '</b></font><br><span>' . $this->lang->loginmsg->tok->err->msg . '</span></div>';
                }

            }
            else
            {
                return '<div class="alert alert-danger" ><font color="red"><b>' . $this->lang->loginmsg->tok->uknw->label . '</b></font><br><span>' . $this->lang->loginmsg->tok->uknw->msg . '</span></div>';
            }
        }
        else
        {
            return '<div class="alert alert-danger"><font color="red"><b>' . $this->lang->loginmsg->tok->err->label . '</b></font><br><span>' . $this->lang->loginmsg->tok->err->msg . '</span></div>';

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
                // 1 dag beschikbaar = 60 seconds * 60 minutes * 24 hours
                // 10 min = 60 seconds * 10
                $delta = 600;
                // Als server tijd min timestamp uit database groter is dan $delta
                // dan is de token vervallen.
                if ($_SERVER["REQUEST_TIME"] - $row_token['user_token_date_time'] > $delta)
                {

                    Logger::logToFile(__FILE__, 0, "Token expired user " . $row_token['user_id']);

                    // Indien token vervallen is verwijder uit database
                    $this->model->deleteRecoverToken($cleaned_user_id);

                    return '<div class="alert alert-danger" ><font color="red"><b>' . $this->lang->loginmsg->tok->exp->label . '</b></font><br><span>' . $this->lang->loginmsg->tok->exp->msg . '</span></div>';
                }
                else
                {
                    // Anders hash de token
                    $check_token_hash = hash('sha256', $token . $row_token['user_token_salt']);
                    for ($round = 0;$round < 65536;$round++)
                    {
                        $check_token_hash = hash('sha256', $check_token_hash . $row_token['user_token_salt']);
                    }
                    // Komt de gehashde token overeen met de hash in de database dan is auth ok
                    if (hash_equals($check_token_hash, $row_token['user_token_hash']))
                    {
                        $token_auth = true;
                    }
                }
            }
            else
            {
                return '<div class="alert alert-danger" ><font color="red"><b>' . $this->lang->loginmsg->tok->inv->label . '</b></font><br><span>' . $this->lang->loginmsg->tok->inv->msg . '</span></div>';
            }

            if ($token_auth)
            {
                // Generate random password
                $gen_password = Auth::genPassSeed(2);
                $hash = password_hash($gen_password, PASSWORD_ARGON2I);

                $row = $this->user->getUserRow($cleaned_user_id);

                $query_params = array(
                    'user_password' => $hash,
                    'user_new' => 1
                );

                if ($this->user->updateUserPassword($query_params, $cleaned_user_id))
                {
                    $email_values = array(
                        'user_name' => $row['user_name'] . " " . $row['user_last_name'],
                        'link' => '<a class="link" href="' . $_SERVER['HTTP_HOST'] . '?ini=' . $cleaned_user_id . '">DB+</a>',
                        'gen_password' => $gen_password,
                    );

                    $mail_body = Mailer::build('password_reset', $email_values);

                    $mail = new PHPMailer(true);
                    try
                    {
                        //Server settings
                        //$mail->SMTPDebug = 2;
                        $mail->isSMTP();
                        $mail->SMTPAutoTLS = false;
                        $mail->Host = Config::SMTP_HOST;
                        $mail->Port = Config::SMTP_PORT;

                        //Recipients
                        $mail->SetFrom(Config::APP_EMAIL);
                        $mail->AddAddress($row['user_email']);

                        //Content
                        $mail->isHTML(true);
                        $mail->Subject = Config::APP_TITLE . ' ' . $this->lang->send_mail->new_password->subject;
                        $mail->Body = $mail_body;
                        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send();

                        Logger::logToFile(__FILE__, 0, "Password reset. Mail send to user: " . $row['user_email']);

                        $this->model->deleteRecoverToken($cleaned_user_id);

                        return '<div class="alert alert-success" ><font color="green"><b >' . $this->lang->loginmsg->res->suc->label . '</b></font><br><span>' . $this->lang->loginmsg->res->suc->msg . '</span></div>';

                    }
                    catch(Exception $e)
                    {

                        Logger::logToFile(__FILE__, 0, 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);

                        return '<div class="alert alert-danger"><font color="red"><b>' . $this->lang->loginmsg->res->notsend->label . '</b></font><br><span>' . $this->lang->loginmsg->res->notsend->msg . '</span></div>';

                    }
                }
                else
                {
                    Logger::logToFile(__FILE__, 0, "Password not updated for user: " . $row['user_email']);
                    return '<div class="alert alert-danger"><font color="red"><b>' . $this->lang->loginmsg->res->err->label . '</b></font><br><span>' . $this->lang->loginmsg->res->err->msg . '</span></div>';
                }

            }
            else
            {
                return '<div class="alert alert-danger" data-i18n="[html]login.res.err"><font color="red"><b>' . $this->lang->loginmsg->tok->inv->label . '</b></font><br><span>' . $this->lang->loginmsg->tok->inv->msg . '</span></div>';

            }
        }
        else
        {
            return '<div class="alert alert-danger" data-i18n="[html]login.res.err"><font color="red"><b>' . $this->lang->loginmsg->csrf->label . '</b></font><br><span>' . $this->lang->loginmsg->csrf->msg . '</span></div>';
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
        // TODO: Check if user exist
        $id = $_SESSION[Config::SES_NAME]['user_id'];

        $connected = $this->user->updateUserLastAccess($id);

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

