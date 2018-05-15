<?php
namespace App\Models;

class LoggingModel
{
    private $message;

    function __construct()
    {

    }
    
    public function setMessage($msg)
    {
        $this->message = $msg;
        return $msg;
    }

    public function getMessage()
    {
        return $this->message;
    }
}

