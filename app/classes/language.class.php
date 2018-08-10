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
	private $file_location = '../Src/lang/';

    /**
     * @return mixed array|language file content
     */
    public function getLanguageFile()
    {
        return json_decode(file_get_contents($this->file_location . Auth::getAuthUserLanguage() . '.json'));
    }

    /**
     * @return array Containing the available languages
     */
	public function getAvailableLanguageFiles()
	{
		$files = array_slice(scandir($this->file_location), 2);
				
		$available = array();
		
		foreach($files as $file)
		{
			$available[] = substr($file,0,2);
		}
		
		return $available;
	}
}

