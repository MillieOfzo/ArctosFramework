<?php
namespace App\Classes;

use \Config;

class Mailer
{
    function __construct()
    {

    }

    public static function build($template_name, $val_arr)
    {
        $header_arr = array(
            'header_logo' => '/public/img/' . Config::LOGO_NAME,
            'header_title' => Config::APP_TITLE,
            'header_text' => 'text'
        );

        $header = file_get_contents(Config::ROOT_PATH . '/app/mail/header.mail.php');
        foreach ($header_arr as $keyh => $valueh)
        {
            $header = str_replace('{{' . $keyh . '}}', $valueh, $header);
        }

        $footer_arr = array(
            'footer_date' => date('D d.m.Y H:i:s')
        );

        $footer = file_get_contents(Config::ROOT_PATH . '/app/mail/footer.mail.php');
        foreach ($footer_arr as $keyf => $valuef)
        {
            $footer = str_replace('{{' . $keyf . '}}', $valuef, $footer);
        }

        $template = file_get_contents(Config::ROOT_PATH . '/app/mail/' . $template_name . '.mail.php');
        $val_arr['header'] = $header;
        $val_arr['footer'] = $footer;
        foreach ($val_arr as $key => $value)
        {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        return $template;
    }
}

