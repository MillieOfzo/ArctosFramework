<?php
namespace App\Controllers;

use App\Models\LoggingModel;
use App\Classes\Helper;

class LoggingController
{
    private $model;

    function __construct()
    {
        $this->model = new LoggingModel;
    }

    public function index()
    {
        $year = date('Y');
        $log_rows = '';
        $error_rows = '';
        if($handle=opendir('../storage/logs/'.date("Y"))){
            while(false !==($file = readdir($handle))) {
                if(strpos($file, $year.'-' ) === 0) {
                    $log_rows .= '<tr><td>Log: <a class="link" id="'.$file.'" onClick="setLogFile(this.id)">'.$file.'</a></td></tr>';
                }

            }
            closedir($handle);
        }

        if($handle=opendir('../storage/logs/'.date("Y").'/Errors')){

            while(false !==($file = readdir($handle))) {
                if(strpos($file, $year.'-' ) === 0) {
                    $error_rows .= '<tr><td>Log: <a class="link" id="'.$file.'" onClick="setErrorLogFile(this.id)">'.$file.'</a></td></tr>';
                }
            }
            closedir($handle);
        }
        return array(
            'logs' => $log_rows,
            'error_logs' => $error_rows,
            'current_log' => date("Y-m-d").".log"
        );
	}		
	
    public function getLogFile()
    {
		$year 		= date("Y");
		$today		= date("Y-m-d");
		
		// Check if GET is defined and not empty
		if($_POST['file']!= '')
		{
			// parse file, prevent access to other files
			$parse_file  = strtolower(preg_replace("/[^a-z0-9-_.]/", "", $_POST['file']));
			
			// if filename contains 'error' add errors directory
			if(strpos($parse_file, 'error') !== false){
				$parse_file = 'errors/'.$parse_file;
			}
		
			$filename = $year.'/'.$parse_file;
		} else {
			$filename = $year.'/'.$today.'.log';
		}
		
		// file location
		$filelocation 	= '../storage/logs/'.$filename;
		
		// show content of file
		$file = file($filelocation);

		$file_content = '<pre style="height: 520px; overflow: auto;">';
			foreach($file as $text) {
				$file_content .= strip_tags($text);
			}  
		$file_content .= "</pre>";	
		
		return Helper::jsonArr($file_content);
		
    }

}

