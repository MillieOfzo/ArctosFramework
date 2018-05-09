<?php
    
	namespace App\Controllers;

	use App\Models\AboutModel as About;
    use App\Classes\Helper as Helper;
	
    class AboutController
    {
        private $model;

        function __construct()
        {
			$this->model = new About;
        }

        public function getView()
        {
            return Helper::shout($this->message = 'My about Page');
        }
     }