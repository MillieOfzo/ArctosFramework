<?php

	namespace App\Classes;
	
	class Helper
	{
		public static function shout($string)
		{
			return strtoupper($string);
		}

		public static function redirect($url){
			header("location: {$url}");
		}
	}