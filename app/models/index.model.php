<?php
namespace App\Models;

class IndexModel
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

