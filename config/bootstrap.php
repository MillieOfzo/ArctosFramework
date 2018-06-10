<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * @see       https://github.com/MillieOfzo/ArctosFramework
 *
 * @author    Roelof Jan van Golen
 * @copyright 2018 RGO
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */
 
require '../vendor/autoload.php';

use App\Classes\ErrorManager;
use App\Classes\FileManager;
use App\Classes\SessionManager;
use App\Classes\Router;
use App\Models\UserModel;

//ob_start();

/**
 * Setup Errormanager
 */	
$errormanager = new ErrorManager;
$errormanager->SetLogLevel(E_ALL);
$errormanager->SetDebug(Config::DEBUG);
$errormanager->SetLogFile(__DIR__ .'/../storage/logs/' . date("Y") . '/Errors/' . date("Y-m-d") . '_error.log');

/**
 * Init the catch of errors and fatal errors
 */
$errormanager->catchError();
$errormanager->catchFatalError();

/**
 * Generate secure session
 */
$sessionmanager = new SessionManager; 
$sessionmanager->sessionStart('ses');

/**
 * Update Lastaccess on every request
 */	
if (isset($_SESSION[Config::SES_NAME]))
{
    $id = $_SESSION[Config::SES_NAME]['user_id'];
    $login = new UserModel;
    $login->updateUserLastAccess($id);
}

/**
 * Define packages as variables to allow pushes to the array
 */	
$arr_js = FileManager::jsFiles();
$arr_css = FileManager::cssFiles();

/**
 * Init router on requests and return response from method
 */
$router = new Router;
$obj = $router->route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
