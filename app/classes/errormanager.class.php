<?php
namespace App\Classes;

/**
 * Set default values to error reporting variables:
 * No error displaying to end user, no startup errors (not to reveal inner workings of the site)
 * Error logging turned on and html errors off since in text logfile, html errors have no meaning.
 */
$errarr = array(
    'display_errors' => 'Off',
    'display_startup_errors' => 'Off',
    'log_errors' => 'On',
    'html_errors' => 'Off',
);

foreach ($errarr as $inikey => $inival)
{
    ini_set($inikey, $inival);
}

class ErrorManager
{
    /**
     * Current log level
     * @var int
     */
    private static $logLevel = E_ALL;

    /**
     * Current debug mode
     * @var bool
     */
    private static $debug = false;

    /**
     * Error log file, stacktrace will be written to that file and detailed error information
     * @var string
     */
    private static $logFile = '';

    /**
     * Sets current log level, log level that is caught by errorhandler when occurs
     * @param int $newLogLevel Log level to set the error handler to catch
     * @param bool $systemlevelalso Whether to set the php error_reporting to set variable too or not (might reduce php overhead of always calling log function when using only potion of available error codes)
     */
    public static function SetLogLevel($newLogLevel)
    {
        self::$logLevel = (int)$newLogLevel;
        error_reporting($newLogLevel);
    }

    /**
     * Sets the debug mode on or off (debug mode = errors get output to browser, but between <!-- -->
     * @param bool $debug
     */
    public static function SetDebug($debug)
    {
        self::$debug = $debug === true || $debug === 'On';
        if (self::$debug)
        {
            $mode = 'On';
        }
        else
        {
            $mode = 'Off';
        }
        ini_set('display_errors', $mode);
        ini_set('display_startup_errors', $mode);
        ini_set('html_errors', $mode);
    }

    /**
     * Sets main error logfile to new value
     * @param string $logFile Log file where full stacktraces are logged (please use full path if possible)
     */
    public static function SetLogFile($logFile)
    {
        self::$logFile = $logFile;
    }

    /**
     * Custom error handler
     * @param integer $code
     * @param string $description
     * @param string $file
     * @param integer $line
     * @param mixed $context
     * @return boolean
     */
    public static function handleError($code, $description, $file = null, $line = null, $context = null)
    {
        $displayErrors = ini_get("display_errors");
        $displayErrors = strtolower($displayErrors);
        if (error_reporting() === 0 || $displayErrors === 'on')
        {
            return false;
        }
        list($error, $log) = self::mapErrorCode($code);

        $datum = date("D Y-m-d H:i:s");
        $env = \Config::APP_ENV;
        $user = (isset($_SESSION[\Config::SES_NAME]['app_location_data'])) ? htmlentities($_SESSION[Config::SES_NAME]['app_location_data'], ENT_QUOTES, 'UTF-8') : '---';

        $str = "[{$datum}] [{$error}] [{$user}] [{$env}] [{$file}, line {$line}] {$description}" . PHP_EOL;
        self::fileLog($str);

        echo '<div class="text-center">
				<h3 class="font-bold text-danger">Oops!</h3>
				<div class="error-desc">
					<p>Something definitely went wrong here! Please try again, otherwise contact your administrators.</p>
				</div>
			</div>';
    }

    /**
     * This method is used to write data in file
     * @param mixed $logData
     * @param string $fileName
     * @return boolean
     */
    private static function fileLog($logData)
    {
        $file = self::$logFile;
        // Open file
        $fileContent = @file_get_contents($file);
        $status = file_put_contents($file, $logData . $fileContent);

        return ($status) ? true : false;
    }

    /**
     * Map an error code into an Error word, and log location.
     *
     * @param int $code Error code to map
     * @return array Array of error word, and log location.
     */
    private static function mapErrorCode($code)
    {
        $error = $log = null;
        switch ($code)
        {
            case E_PARSE:
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $error = 'Fatal Error';
                $log = LOG_ERR;
            break;
            case E_WARNING:
            case E_USER_WARNING:
            case E_COMPILE_WARNING:
            case E_RECOVERABLE_ERROR:
                $error = 'Warning';
                $log = LOG_WARNING;
            break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $error = 'Notice';
                $log = LOG_NOTICE;
            break;
            case E_STRICT:
                $error = 'Strict';
                $log = LOG_NOTICE;
            break;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $error = 'Deprecated';
                $log = LOG_NOTICE;
            break;
            default:
            break;
        }
        return array(
            $error,
            $log
        );
    }

    /**
     * Set the error handling function to ErrorManager::handleError()
     */
    public function catchError()
    {
        set_error_handler(array(
            $this,
            'handleError'
        ));
    }

    /**
     * Catch fatal error and display nice error view to user.
     * Write fatal error to error log.
     *
     * @return Die statement with error message.
     */
    public static function shutdown()
    {
        $isError = false;

        if ($error = error_get_last())
        {
            switch ($error['type'])
            {
                case E_PARSE:
                case E_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    $datum = date("D Y-m-d H:i:s");
                    $name = 'Fatal';
                    $user = (isset($_SESSION[\Config::SES_NAME]['user_email'])) ? htmlentities($_SESSION[\Config::SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8') : '---';
                    $env = \Config::APP_ENV;
                    $file = $error['file'];
                    $line = $error['line'];
                    $description = $error['message'];

                    $str = "[{$datum}] [{$name}] [{$user}] [{$env}] [{$file}, line {$line}] {$description}" . PHP_EOL;

                    $isError = self::fileLog($str);
                break;
            }
        }

        if ($isError && self::$debug !== 1)
        {
            // If an ajax call is being proccesed
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
            {
                $return = array();
                $return['title'] = "Fatal error";
                $return['msg'] = '<b>URL</b><br>
								<small>' . htmlspecialchars($_SERVER['HTTP_REFERER']) . '</small><br>
								<b>Error message</b><br>
								<pre>' . $error['message'] . '</pre>
								<div style="margin-bottom: 20px;"></div>
								<b>Error on file</b><br>
								<small>' . $error['file'] . '</small><br>
								<b>Error on line</b><br>
								<small>' . $error['line'] . '</small><br>';

                header('Cache-Control: no-cache, must-revalidate');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Content-type: application/json');
                die(json_encode($return));
            }

            echo '<div >
							<h3 class="font-bold text-danger">Super Oops!</h3>
							<div class="error-desc">
								<p>Something absolutely definitely went wrong here! Please don\'t try again, but contact your administrators!</p>
							</div>
						</div>';
        }
    }

    /**
     * Set the error handling function to ErrorManager::handleError()
     */
    public function catchFatalError()
    {
        register_shutdown_function(array(
            $this,
            'shutdown'
        ));
    }
}

