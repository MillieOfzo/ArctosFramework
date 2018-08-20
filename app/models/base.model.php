<?php
namespace App\Models;

use \Config;
use \SafeMySQL;
use App\Classes\Logger;

abstract class BaseModel
{
	protected $conn;
	
    function __construct()
    {
		$this->conn = new SafeMySQL;		
    }	
	
	protected function logError($exception)
    {
        $msg = 'Regel: ' . $exception->getLine() . ' Bestand: ' . $exception->getFile() . ' Error: ' . $exception->getMessage();
        Logger::logToFile(__FILE__, 1, $msg);
        return array(
			'status' => false,
			'message'=>$exception->getMessage(),
		);
    }	
}

