<?php

    class Config
    {
        const APP_TITLE = 'ARCTOS framework';
        const APP_LANG = 'nl';
        const APP_ROOT = '/arctos';
        const LOGO_NAME = 'logo.png';
        const FAVICON_NAME = 'favicon.png';
        const ROOT_PATH = 'C:/xampp/htdocs/arctos';
        const SES_NAME = 'arc_user';
        const DEBUG = true;
        const DISABLE_CACHE = true;

        const DB_HOST = '10.10.10.11';
        const DB_USER = 'scsClient';
        const DB_PASS = 'ikhouvanjou';
        const DB_NAME = 'arc_data';

        public static function getVersion()
        {
            $array = self::getCompose();

            return $array['version'];
        }

        public static function getEnv()
        {
            $array = self::getCompose();

            return $array['env'];
        }

        public static function getCopyright()
        {
            $array = self::getCompose();

            return $array['copyright'];
        }

        private static function getCompose()
        {
            $content = file_get_contents('../composer.json');
            return json_decode($content,true);

        }
    }