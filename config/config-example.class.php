<?php

class Config
{
    /**
     * APPLICATION defined config items
     *
     * Predefined items which can be added to fit your application
     * it is possible to add new config items according to the following:
     *  - Config items must be all UPPERCASE
     *
     */
    const APP_TITLE = 'ARCTOS framework';
    const APP_LANG = 'nl';
    const APP_EMAIL = '';
    const APP_VER = '0.0.1';
    const APP_ENV = 'OTAP';
    const APP_THEME = 'light';
    const LOGO_NAME = 'logo_light.png';
    const FAVICON_NAME = 'favicon.png';
    const ROOT_PATH = ''; // e.g 'C:/xampp/htdocs/arctos'
    const SES_NAME = 'arc_user';
    const DEBUG = false;
    const DISABLE_ROUTING_CACHE = false;
    const SMTP_HOST = '';
    const SMTP_PORT = 25;
    /**
     * DATABASE items
     *
     */
    const DB_HOST = '';
    const DB_USER = '';
    const DB_PASS = '';
    const DB_NAME = '';

    /**
     * FRAMEWORK config items
     *
     */
    public static function getFrameWorkName()
    {
        $array = self::getFrameWorkCompose();

        return $array['name'];
    }

    public static function getFrameWorkVersion()
    {
        $array = self::getFrameWorkCompose();

        return $array['version'];
    }

    public static function getFrameWorkEnv()
    {
        $array = self::getFrameWorkCompose();

        return $array['env'];
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

