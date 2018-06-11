<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * Package class
 * 
 * Configure css an js package to be used in html views
 */
 
namespace App\Classes;

use \Config;

class FileManager
{
    public static function cssFiles()
    {
        $cssArr = array(
			'/public/css/pe-icons/pe-icon-7-stroke.css',
			'/public/css/pe-icons/helper.css',		   
            '/public/css/bootstrap/bootstrap.min.css',
            '/public/css/fonts/font-awesome/css/font-awesome.css',
            '/public/css/stroke-icons/stroke-style.css',
            '/public/css/main/animate.css',
            '/public/css/formvalidation/dist/css/formValidation.min.css',
            '/public/css/dataTables/datatables.min.css',
            '/public/css/dataTables/datatables_responsive.min.css',
            '/public/css/sweetalert/sweetalert.css',
            '/public/css/select2/dist/css/select2.min.css',
            '/public/css/datepicker/datepicker3.css',
            '/public/css/main/style_'.strtolower(Config::APP_THEME).'.css',
        );

        return $cssArr;
    }

    public static function jsFiles()
    {
        $jsArr = array(
            '/public/js/jquery/jquery-3.1.1.min.js',
            '/public/js/bootstrap/bootstrap.min.js',
            '/public/js/metisMenu/jquery.metisMenu.js',
            '/public/js/slimscroll/jquery.slimscroll.min.js',
            '/public/js/i18next/i18next.min.js',
            '/public/js/main/main.js',
			'/public/js/pace/pace.min.js',
            '/public/js/formvalidation/dist/js/formValidation.min.js',
            '/public/js/formvalidation/dist/js/framework/bootstrap.min.js',
            '/public/js/formvalidation/dist/js/language/' . strtolower(Config::APP_LANG) . '_' . strtoupper(Config::APP_LANG) . '.js',
            '/public/js/sweetalert/sweetalert.min.js',
            '/public/js/zxcvbn/zxcvbn.js',
            '/public/js/select2/dist/js/select2.js',
            '/public/js/autocomplete/dist/jquery.autocomplete.min.js',
        );
        return $jsArr;
    }
}

