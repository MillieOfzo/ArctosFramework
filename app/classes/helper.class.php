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
    public static function jsonArr($response_array)
    {
        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($response_array);
        exit();
    }

    public static function redirect($url)
    {
        header("location: {$url}");
    }
}

