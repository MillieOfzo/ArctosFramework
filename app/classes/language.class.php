<?php
namespace App\Classes;

use \Config;

class Language
{
    function __construct()
    {

    }

    public function getLanguageFile()
    {
        return json_decode(file_get_contents('../Src/lang/' . Config::APP_LANG . '.json'));
    }
}

