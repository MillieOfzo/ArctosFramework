<?php

namespace App\Classes;

class Autocomplete
{

    public static function get()
    {

        $arr = array();

        $reply['suggestions'] 	= array();

        foreach($arr as $key){
            //Add this row to the reply
            $reply['suggestions'][] = array(
                'value'=>htmlentities(stripslashes($key)),
                'data'=>htmlentities(stripslashes($key))
            );
        }

        Helper::jsonArr($reply);

    }

}