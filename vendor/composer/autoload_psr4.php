<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Psr\\Http\\Message\\' => array($vendorDir . '/psr/http-message/src'),
    'PHPMailer\\PHPMailer\\' => array($vendorDir . '/phpmailer/phpmailer/src'),
    'GuzzleHttp\\Psr7\\' => array($vendorDir . '/guzzlehttp/psr7/src'),
    'GuzzleHttp\\Promise\\' => array($vendorDir . '/guzzlehttp/promises/src'),
    'GuzzleHttp\\' => array($vendorDir . '/guzzlehttp/guzzle/src'),
    'FastRoute\\' => array($vendorDir . '/nikic/fast-route/src'),
    'App\\Routes\\' => array($baseDir . '/routes'),
    'App\\Models\\' => array($baseDir . '/app/models'),
    'App\\Controllers\\' => array($baseDir . '/app/controllers'),
    'App\\Classes\\' => array($baseDir . '/app/classes'),
);
