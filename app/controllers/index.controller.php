<?php

	namespace App\Controllers;
	
	use App\Models\IndexModel as Index;
	use App\Classes\Helper as Helper;

    class IndexController
    {
        private $model;

        function __construct()
        {
            $this->model = new Index;
        }

        public function Welcome()
        {
			$this->model->setMessage('Welcome to the '.\Config::APP_TITLE);
            return Helper::shout($this->model->getMessage());
        }


    }