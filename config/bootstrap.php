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
use App\Classes\Package;
use App\Classes\SessionManager;
use App\Classes\Router;
use App\Models\UserModel;

/**
 * Setup Errormanager
 */	
$errormanager = new ErrorManager;

ErrorManager::SetLogLevel(E_ALL);
ErrorManager::SetDebug(Config::DEBUG);
ErrorManager::SetLogFile(Config::ROOT_PATH . '/Storage/Logs/' . date("Y") . '/Errors/' . date("Y-m-d") . '_error.log');

/**
 * Init the catch of errors and fatal errors
 */
$errormanager->catchError();
$errormanager->catchFatalError();

/**
 * Generate secure session
 */	
SessionManager::sessionStart('ses');

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
$arr_js = Package::jsPackage();
$arr_css = Package::cssPackage();

/**
 * Fetch method and URI from somewhere
 */
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

/**
 * Init router on requests and return response from method
 */
$obj = Router::route($httpMethod, $uri);

