<?php

	namespace App\Classes;
	
	class Mailer
	{
		function __construct()
		{
			
		}
		
		public static function build($template_name, $val_arr)
		{
			$template = file_get_contents('../mail/'.$template_name.'.mail.php');
			//$header = file_get_contents('../mail/header.mail.php');
			//$footer = file_get_contents('../mail/footer.mail.php');
			
			foreach($val_arr as $key => $value){
				$template = str_replace('{{'.$key.'}}', $value, $template);
			}
			
			//$template = str_replace('{{header}}', $header, $template);
			//$template = str_replace('{{footer}}', $footer, $template);
			
			return $template;			
		}
	}