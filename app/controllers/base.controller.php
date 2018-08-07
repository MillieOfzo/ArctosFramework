<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * Base controller
 * 
 * Provides a base controller with basic functions
 */
 
namespace App\Controllers;

use \Config;
use App\Classes\Language;

abstract class BaseController
{
	private $label;
	protected $lang;
	protected $res = [];

    function __construct()
    {
		$this->lang = (new Language)->getLanguageFile();		
    }	
	
	protected function returnMsg($title, $text = '', $type = 'success')
	{
		return $this->res = array(
			'title' => $title,
			'text' => $text,
			'type' => $type
		);
	}	
	
	protected function setResponseMsg($label, $test, $type = 'success')
	{
		switch($type)
		{
			case 'warning':
				$type = 'alert alert-warning';
				break;
			case 'danger':
				$type = 'alert alert-danger';
				break;
			default:
				$type = 'alert alert-success';
				break;
		}
		
		$msg = "<div class=\"{$type}\"><b>{$label}</b><br><span>{$test}</span></div>";
		
		return $this->res = array(
			'res' => $msg
		);
	}	

}

