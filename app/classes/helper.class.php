<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * Helper class
 * 
 * Provides a class where custom helper functions can be stored
 */
 
namespace App\Classes;

use \Config;
use \SafeMySQL;

class Helper
{
	public static function getUserStatusSelect()
	{
		$conn = new SafeMySQL;
		$select ='<option></option>';
		foreach($conn->getAll("SELECT * FROM app_user_status") as $status)
		{
			$select .= '<option value="'.$status['id'].'">'.$status['status_name'].'</option>';
		}
		return $select;
	}
	
	public static function getUserRoleSelect()
	{
		$conn = new SafeMySQL;
		$select ='<option></option>';
		foreach($conn->getAll("SELECT * FROM app_role") as $role)
		{
			$select .= '<option value="'.$role['id'].'">'.$role['role_name'].'</option>';
		}
		return $select;
	}	
	
	public static function getLanguageSelect()
	{
		$conn = new SafeMySQL;
		$select ='<option></option>';
		foreach($conn->getAll("SELECT * FROM app_languages") as $language)
		{
			$select .= '<option value="'.$language['language_locale'].'">'.$language['language_name'].'</option>';
		}
		return $select;
	}	

    /**
     * in_array() does not work on multidimensional arrays. Custom recursive function
     *
     * @param value $needle
     * @param array $haystack
     * @param bool  $strict
     * @return bool
     */
    public static function in_array_r($needle, $haystack, $strict = false) 
	{
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && self::in_array_r($needle, $item, $strict))) {
                return true;
            }
        }
        return false;
    }
	
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

