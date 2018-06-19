<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * APPLICATION defined config items
 *
 * Predefined items which can be added to fit your application
 * it is possible to add new config items according to the following:
 *  - Config constants must be all UPPERCASE
 *
 * Config class is available through the root namespace: \
 */

class Config
{
	 
    /**
     * Name of the application
     * @var string
     */
    const APP_TITLE = 'ARCTOS';
	
    /**
     * Language file to be used in the application. 
	 * Default: en
	 * Langeage files are available in: /src/lang/
	 * @var string
     */
    const APP_LANG = 'nl';
	
    /**
     * Email address from which email are send
     * @var string
     */	
    const APP_EMAIL = 'info@beheercentrum.nl';
	
    /**
     * Application version number
     * @var string
     */	
    const APP_VER = '1.0.4';
	
    /**
     * Application enviroment e.g OTAP, TESTING, LIVE etc
     * @var string
     */	
    const APP_ENV = 'OTAP';
	
    /**
     * Application theme.
     * Default: light
     * Options:
     *      - light
     *      - dark
     * @var string
     */		
    const APP_THEME = 'light';

    /**
     * Specify the name of the image to be used as logo
     * @var string
     */		
    const LOGO_NAME = 'logo_light.png';
	
    /**
     * Specify the name of the image to be used as favicon
     * @var string
     */		
    const FAVICON_NAME = 'favicon.png';
		
    /**
     * Specify the session name
     * @var string
     */		
    const SES_NAME = 'arc_user';
	
    /**
     * Enable or disable debug.
     * Default: false
     * @var bool
     */		
    const DEBUG = true;
	
    /**
     * DATABASE config items
     */
    const DB_HOST = '';
    const DB_USER = '';
    const DB_PASS = '';
    const DB_NAME = '';

    /**
     * FRAMEWORK config items to showcase the used framework
	 * the application is build on
     */
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

        return $array['copyright'];
    }

    private static function getFrameWorkCompose()
    {
        $content = file_get_contents('../composer.json');
        return json_decode($content, true);

    }
}

