<?php
namespace App\Controllers;

use App\Models\IndexModel;
use App\Classes\Helper;

class IndexController
{
    private $model;

    function __construct()
    {
        $this->model = new IndexModel;
    }

    public function index()
    {

    }

}

