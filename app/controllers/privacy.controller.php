<?php
namespace App\Controllers;

use \Config;
use App\Classes\Language;

class PrivacyController
{

    function __construct()
    {
		$this->lang = new Language;
    }

    public function index()
    {
		$available_lang = $this->lang->getAvailableLanguageFiles();

		$backup_lang = array_diff( $available_lang, array(Config::APP_LANG));
				
		if(file_exists('../src/views/docs/privacy_'.strtolower(Config::APP_LANG).'.view.php'))
		{
			return array('view' => '../src/views/docs/privacy_'.strtolower(Config::APP_LANG).'.view.php');
		}
		else
		{
			foreach($backup_lang as $backup)
			{
				if(file_exists('../src/views/docs/privacy_'.strtolower($backup).'.view.php'))
				{
					return array('view' => '../src/views/docs/privacy_'.strtolower($backup).'.view.php');
				}
			}
			
		}
        
    }

}