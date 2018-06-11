<?php
namespace App\Controllers;

use \Config;
use \SafeMySQL;
use App\Classes\Auth;
use App\Classes\Helper;
use App\Models\HomeModel;

class HomeController
{
    private $model;

    function __construct()
    {
        $this->model = new HomeModel;
    }

    public function index()
    {

    }

}

