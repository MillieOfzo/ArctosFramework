<?php

	namespace App\Classes;
	
	class Logger
	{
		/**
		 * Custom log to file function
		 * @param integer $level
		 * @param string $msg
		 */		
	    public static function logToFile($file,$level,$msg)
        {
            $user = (isset($_SESSION[\Config::SES_NAME]['user_email'])) ? htmlentities($_SESSION[\Config::SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8') : '---';
            $env  = \Config::getEnv();

            $date = date("Y-m-d");
			$path = \Config::ROOT_PATH;
            $path .= '/Storage/Logs/'.date("Y").'/';
            //$path .= "/Src/Logs/".$year."/";
            // Bestaat de folder niet maak deze dan aan
            if(!file_exists($path)){
                mkdir($path);
                mkdir($path.'/Errors');
            }

            $filename = $path . $date . '.log';
            // Open file
            $fileContent = @file_get_contents($filename);

            $datum = date("D Y-m-d H:i:s");
            // Log level
            if ($level === 1) {
                $level = "CRITICAL";
            } elseif ($level === 2) {
                $level = "WARNING";
            } else {
                $level = "NOTICE";
            }

            $str = "[{$datum}] [{$level}] [{$user}] [{$env}] [{$file}] {$msg}" . PHP_EOL;
            // Schrijf string naar file
            file_put_contents($filename, $str . $fileContent);

        }

	}