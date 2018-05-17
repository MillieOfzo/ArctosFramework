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
     * @param string
     */
    const APP_TITLE = 'ARCTOS framework';

    /**
     * Language file to be used in the application. Default: en
     * Langeage files are available in: /src/lang/
     * @param string
     */
    const APP_LANG = 'en';

    /**
     * Email address from which email are send
     * @param string
     */
    const APP_EMAIL = '';

    /**
     * Application version number
     * @param string
     */
    const APP_VER = '0.0.1';

    /**
     * Application enviroment e.g OTAP, TESTING, LIVE etc
     * @param string
     */
    const APP_ENV = 'OTAP';

    /**
     * Application theme.
     * Default: light
     * Options:
     *      - light
     *      - dark
     * @param string
     */
    const APP_THEME = 'light';

    /**
     * Specify the name of the image to be used as logo
     * @param string
     */
    const LOGO_NAME = 'logo_light.png';

    /**
     * Specify the name of the image to be used as favicon
     * @param string
     */
    const FAVICON_NAME = 'favicon.png';

    /**
     * Specify the session name
     * @param string
     */
    const SES_NAME = 'arc_user';

    /**
     * Enable or disable debug.
     * Default: false
     * @param bool
     */
    const DEBUG = false;

    /**
     * Enable or disable route cacheing in router.class.php.
     * Default: false
     * @param bool
     */
    const DISABLE_ROUTING_CACHE = false;

    /**
     * Specify the SMTP host to be used by mailer class
     * @param string
     */
    const SMTP_HOST = '';

    /**
     * Specify the SMTP port nr.
     * Default: 25
     * @param int
     */
    const SMTP_PORT = 25;

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

