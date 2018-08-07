<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * Helper class
 * 
 * Provides a class where custom helper functions can be stored
 */
 
namespace App\Classes;

class Helper
{
	public static function purifyInput($value)
	{
		$purifier = new \HTMLPurifier(\HTMLPurifier_Config::createDefault());
		return $purifier->purify($value);
		
	}	
	
	public static function getUrlProtocol()
	{
		$protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
		return $protocol;
	}
	
    public static function jsonArr($response_array)
    {
        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($response_array);
        exit();
    }

    public static function redirect($url)
    {
        header("location: {$url}");
		exit();		 
    }
}

