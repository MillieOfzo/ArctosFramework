<?php
namespace App\Classes;

// Singleton to connect db. NOT CURRENTLY USED
class DB
{

    // Hold the class instance.
    private static $instance = null;
    private $conn;

    protected $defaults = array(
        'host' => \Config::DB_HOST,
        'user' => \Config::DB_USER,
        'pass' => \Config::DB_PASS,
        'db' => \Config::DB_NAME
    );

    private function __construct($opt = array())
    {
        $opt = array_merge($this->defaults, $opt);
        $this->instance = new SafeMySQL($opt);
    }

    public static function getInstance()
    {
        if (!self::$instance)
        {
            self::$instance = new DB;
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}

