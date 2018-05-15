<?php
/**
 * ARCTOS - Lightweight framework.
 */
 
namespace App\Classes;

use \Config as Config;

class Logger
{
    /**
     * Custom log to file function
	 *
     * @param sting $file The file the method is called on
	 * @param integer $level 
	 *    the following integers are accepted
	 *     1 - CRITICAL
	 *     2 - WARNING
	 *     Default - NOTICE 
     * @param string $msg The message to be logged
     */
    public static function logToFile($file, $level, $msg)
    {
        $user = (isset($_SESSION[Config::SES_NAME]['user_email'])) ? htmlentities($_SESSION[Config::SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8') : '---';
        $env = Config::APP_ENV;

        $date = date("Y-m-d");
        $path = $_SERVER['DOCUMENT_ROOT'];
        $path .= '/Storage/Logs/' . date("Y") . '/';

        // If path doesnt exist create folder
        if (!file_exists($path))
        {
            mkdir($path);
            mkdir($path . '/Errors');
        }

        $filename = $path . $date . '.log';
        // Open file
        $fileContent = @file_get_contents($filename);

        $datum = date("D Y-m-d H:i:s");
        // Log level
        if ($level === 1)
        {
            $level = "CRITICAL";
        }
        elseif ($level === 2)
        {
            $level = "WARNING";
        }
        else
        {
            $level = "NOTICE";
        }

        $str = "[{$datum}] [{$level}] [{$user}] [{$env}] [{$file}] {$msg}" . PHP_EOL;
        // Schrijf string naar file
        file_put_contents($filename, $str . $fileContent);

    }

}

