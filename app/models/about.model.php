<?php
namespace App\Models;

class AboutModel
{

    private $message;

    function __construct()
    {

    }

    public function setMessage($msg)
    {
        $this->message = $msg;
    }

    public function getMessage()
    {
        return $this->message;
    }
}

