<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * Mailer class
 * 
 * Builds template mails and wrapper class for PHPmailer
 */
 
namespace App\Classes;

use \Config;
use App\Classes\Language;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    /**
     * Enable or disable the use of phpmailer smtp functions
     * Default: false
     * @param bool
     */	
	public static $enable_smtp = true;
	
    /**
     * SMTP hosts.
     * Either a single hostname or multiple semicolon-delimited hostnames.
     * You can also specify a different port
     * for each host by using this format: [hostname:port]
     * (e.g. "smtp1.example.com:25;smtp2.example.com").
     * You can also specify encryption type, for example:
     * (e.g. "tls://smtp1.example.com:587;ssl://smtp2.example.com:465").
     * Hosts will be tried in order.
     *
     * @param string
     */	
	public static $smtp_host = '192.168.100.254';
	
    /**
     * The default SMTP server port.
     * Default: 25
     * @param int
     */	
	public static $smtp_port = 25;
	
    /**
     * Whether to use SMTP authentication.
     * Uses the Username and Password properties.
     *
     * @see PHPMailer::$Username
     * @see PHPMailer::$Password
     *
     * @param bool
     */	
	public static $enable_smtp_auth = false;
	
    /**
     * SMTP username.
     *
     * @param string
     */	
	public static $smtp_auth_user = '';
	
    /**
     * SMTP password.
     *
     * @param string
     */	
	public static $smtp_auth_pass = '';
	
    /**
     * What kind of encryption to use on the SMTP connection.
     * Options: '', 'ssl' or 'tls'.
     *
     * @param string
     */	
	public static $smtp_secure = 'tls';
	
    /**
     * Whether to enable TLS encryption automatically if a server supports it,
     * even if `SMTPSecure` is not set to 'tls'.
     * Be aware that in PHP >= 5.6 this requires that the server's certificates are valid.
     *
     * @param bool
     */	
	public static $smtp_auto_tls = false;
	
    /**
     * Specify if SMTP debug should be enabled. Default: false
     *
     * @param bool	 
     */		
	public static $enable_smtp_debug = false;	
	
    /**
     * SMTP class debug output mode.
     * Debug output level.
     * Options:
     * * `0` No output
     * * `1` Commands
     * * `2` Data and commands
     * * `3` As 2 plus connection status
     * * `4` Low-level data output.
     *
     * @param int
     */	
	public static $smtp_debug_lvl = 1;
	
    /**
     * How to handle debug output.
     * Options:
     * * `echo` Output plain-text as-is, appropriate for CLI
     * * `html` Output escaped, line breaks converted to `<br>`, appropriate for browser output
     * * `error_log` Output to error log as configured in php.ini
     * By default PHPMailer will use `echo` if run from a `cli` or `cli-server` SAPI, `html` otherwise.
     * Alternatively, you can provide a callable expecting two params: a message string and the debug level:
     *
     * ```php
     * $mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str";};
     * ```
     *
     * Alternatively, you can pass in an instance of a PSR-3 compatible logger, though only `debug`
     * level output is used:
     *
     * ```php
     * $mail->Debugoutput = new myPsr3Logger;
     * ```
     *
     * @see SMTP::$Debugoutput
     *
     * @param string|callable|\Psr\Log\LoggerInterface
     */	
	public static $smtp_debug_output = 'error_log';

    /**
     * Build the specified template
     *
     * @param string $template_name The template name
     * @param array  $val_arr Array containing the values to replace the placeholders in the template
     * @return bool|mixed|string Returns the complete template as html
     */
    public static function build($template_name = '', $val_arr = array())
    {
		$lang = (new Language)->getLanguageFile();
		
        $header_arr = array(
            'header_logo' => Helper::getUrlProtocol() . '://' . $_SERVER['HTTP_HOST'] . '/public/img/' . Config::LOGO_NAME,
            'header_link' => Helper::getUrlProtocol() . '://' . $_SERVER['HTTP_HOST'],
            'header_title' => Config::APP_TITLE,
            //'header_text' => $lang->loginscreen->text
        );

        $header = file_get_contents('../app/mail/header.mail.php');
        foreach ($header_arr as $keyh => $valueh)
        {
            $header = str_replace('{{' . $keyh . '}}', $valueh, $header);
        }

        $footer_arr = array(
            'footer_date' => date('D d/m/Y H:i:s'),
            'footer_year' => date('Y')
        );

        $footer = file_get_contents('../app/mail/footer.mail.php');
        foreach ($footer_arr as $keyf => $valuef)
        {
            $footer = str_replace('{{' . $keyf . '}}', $valuef, $footer);
        }

        $template = file_get_contents('../app/mail/' . $template_name . '.mail.php');
        $val_arr['header'] = $header;
        $val_arr['footer'] = $footer;
        foreach ($val_arr as $key => $value)
        {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        return $template;
    }

    /**
     * Send the build template
     *
     * @param string $subject The subject of the email
     * @param string $mail_body Message body containing the build template
     * @param array  $to Array containing the addresses to send the email to
     * @param array  $cc Array containing the Carbon Copy addresses
     * @param string $attachment
     * @return bool if mail is send else false
     * @throws \PHPMailer\PHPMailer\Exception
     */
	public static function send($subject ='', $mail_body ='', $to = array(), $cc = array(), $attachment = '')
	{
		$mail = new PHPMailer(true);
		
        // SMTP server settings
		if(self::$enable_smtp)
		{
			if(self::$enable_smtp_debug)
			{
				$mail->SMTPDebug = self::$smtp_debug_lvl;
				$mail->Debugoutput = self::$smtp_debug_output;					
			}
			
			$mail->isSMTP();
			$mail->SMTPAutoTLS = self::$smtp_auto_tls;
			$mail->Host = self::$smtp_host;
			$mail->Port = self::$smtp_port;
			
			if(self::$enable_smtp_auth)
			{
				$mail->SMTPAuth = self::$enable_smtp;
				$mail->Username  = self::$smtp_auth_user;
				$mail->Password   = self::$smtp_auth_pass;
				$mail->SMTPSecure = self::$smtp_secure;
			}
		}


        // Recipients
        $mail->SetFrom(Config::APP_EMAIL);
		if(count($to) > 0)
		{
			foreach($to as $address)
			{
				$mail->AddAddress($address);
			}
		}
		
		if(count($cc) > 0)
		{
			foreach($cc as $cc_address)
			{
				$mail->addCC($cc_address);
			}
		}

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $mail_body;	
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        // Attachment
        if($attachment != '')
        {
            $mail->addAttachment($attachment);
        }

        if($mail->send())
		{
			return true;					
		}
		else 
		{
			Logger::logToFile(__FILE__, 0, 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
			return false;
		}		
	}
}

