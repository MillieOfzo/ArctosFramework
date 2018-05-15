<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * Language class
 */
 
namespace App\Classes;

use \Config;

class Language
{
	
	/**
	 * $return object Language file
	 */
    public function getLanguageFile()
    {
        return json_decode(file_get_contents('../Src/lang/' . Config::APP_LANG . '.json'));
    }
}

