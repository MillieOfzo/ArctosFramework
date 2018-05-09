<?php
    
	namespace App\Controllers;
	
	use App\Models\UserModel as User;
    use App\Classes\Helper as Helper;

	class UserController 
	{
		private $model;
		
		function __construct( )
		{
			$this->model = new User;
		}

		public function index()
        {
            $this->model->setMessage('This is a user view');
            return Helper::shout($this->model->getMessage());
        }

		public function login()
		{
			echo "Login Method";
		}
	}