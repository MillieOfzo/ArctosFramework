<?php

namespace App\Controllers;

use App\Classes\ApiService;
use App\Classes\Helper;

class AutocompleteController
{

    public static function get()
    {
		$api = new ApiService;
		$term = trim(strip_tags($_GET['query']));
		
		$search_param = "freeSearch=".rawurlencode($term);
		$res = $api->callApi("scs/currentUser/alarmAccounts?{$search_param}", 'GET');

		$reply['suggestions'] 	= array();

		foreach($res['pageItems'] as $device){
            
            $reply['suggestions'][] = array(
                'value' => htmlentities(stripslashes(@$device['alarmAccountNmbr']))
            );
        }		

		Helper::jsonArr($reply);
    }

}