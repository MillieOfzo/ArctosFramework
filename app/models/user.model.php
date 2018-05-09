<?php

	namespace App\Models;

    class UserModel
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