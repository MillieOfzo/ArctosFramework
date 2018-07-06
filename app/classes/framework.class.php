<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * Framework class
 * 
 * FRAMEWORK config items to showcase the used framework the application is build on
 *
 */
 
namespace App\Classes;

use \Config;

class Framework
{
    public static function getFrameWorkName()
    {
        $array = self::getFrameWorkCompose();

        return $array['framework'];
    }

    public static function getFrameWorkVersion()
    {
        $array = self::getFrameWorkCompose();

        return $array['version'];
    }

    public static function getFrameWorkCopyright()
    {
		$array = self::getFrameWorkCompose();
		if(\Config::APP_COPYRIGHT == '')
		{
			return $array['copyright'];
		}
        else
		{
			return \Config::APP_COPYRIGHT;
		}
    }

    private static function getFrameWorkCompose()
    {
        $content = file_get_contents('../composer.json');
        return json_decode($content, true);

    }
}

