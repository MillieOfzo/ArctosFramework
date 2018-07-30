<?php
namespace App\Controllers;

use App\Models\IndexModel;
use App\Classes\Helper;

class IndexController
{
    private $index;

    function __construct()
    {
        $this->index = new IndexModel;
    }

    public function index()
    {

    }

    /**
     * Autocomplete function
     *
     * Attached to topnav.layout.php
     * Called in main.js
     */
    public static function getAutoComplete()
    {


    }	
	
}

