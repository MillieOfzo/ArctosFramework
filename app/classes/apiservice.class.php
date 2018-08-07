<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * API service class
 * 
 */

namespace App\Classes;
	
use \Config;
use \GuzzleHttp\Client;
use \GuzzleHttp\Psr7;
use \GuzzleHttp\Psr7\Request;
use \GuzzleHttp\Psr7\Http\Message\RequestInterface;
use \GuzzleHttp\HandlerStack;
use \GuzzleHttp\Exception\RequestException;
use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ConnectException;

class ApiException
{
    /**
     * Prettify error message output.
     *
     * @param object $e Exception object
     * @return array
     */
    public static function failedRequest($e)
    {
		//echo '<pre>'.Psr7\str($e->getRequest()).'</pre>';
		//echo '<pre>'.Psr7\str($e->getResponse()).'</pre>';
		Logger::logToFile(__FILE__, 1, Psr7\str($e->getRequest()));
		Logger::logToFile(__FILE__, 1, Psr7\str($e->getResponse()));
		return array(
			'status' => false,
			'msg' => json_decode($e->getResponse()->getBody(true)->getContents(), true),
		);		
    }
}

Class ApiService
{
    /**
     * The API endpoint url
	 *
     * @param string
     */		
	const API_ENDPOINT = 'http://10.10.10.11/';
	
    /**
     * The API endpoint version
	 *
     * @param string
     */	
	const API_VERSION = 'v2';
	
    /**
     * The API endpoint token refresh url
	 *
     * @param string
     */		
	const API_AUTH_URI = 'auth/currentUser/refreshTokens';
	
	/**
     * Enable api exceptions
	 *
	 * Default: false
     * @param bool
     */		
	const API_HTTP_ERRORS = false; 
	
    /**
     * Force regeneration of API bearer token
	 *
	 * Default: false
     * @param bool
     */		
	const API_FORCE_TOKEN = false; 

    /**
     * Refresh token if current token has expired
	 *
	 * Default: false
     * @param bool
     */	
	const API_TOKEN_REFRESH = false; 
	
    /**
     * Array to hold token variables
	 *
     * @param array
     */	
	protected $token = [];

	/**
     * Basic auth username variable
	 *
     * @param string
     */
	protected $user = '';
	
	/**
     * Basic auth password variable
	 *
     * @param string
     */	
	protected $pass = '';
	
    /**
     * Array to hold return message variables
	 *
     * @param array
     */		
	protected $msg = [
		'status' => true,
		'msg' => '',
	];
	
    /**
     * Constuctor creates an new Http guzzle client
	 *
     * @return client object
     */			
	public function __construct()
	{
		$client = new Client([
			'base_uri' => self::API_ENDPOINT . self::API_VERSION . '/',
			'http_errors' => self::API_HTTP_ERRORS,
			'headers' => [
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
			]
		]);
		$this->client = $client;
	}

    /**
     * Authenticate user against the API
     * 
     * @param string $user Username
     * @param string $pass Password
     * @return bool | True if user can authenticate with API
     */		
	public function authApi($user, $pass)
	{
		$this->user = $user;
		$this->pass = $pass;
		
		return $this->acquireAccessToken();	
	}

    /**
     * Execute an API call through guzzle
     * 
     * @param string $endpoint API call url
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param array $options Optional set of options
     * @return array | Json array with results
	 * @throws \App\Classes\ApiService\ApiException
     */		
	public function callApi($endpoint = '/', $method = 'GET', $options = [])
	{
		try {
			$response = $this->send($method, $endpoint, $options);
		} catch (RequestException $e) {
			return ApiException::failedRequest($e);
		}
		return $response;
	}
	
    /**
     * Perform guzzle request
     * 
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $endpoint API call url	 
     * @param array $options Optional set of options
     * @return array|string | Json array with results or a string with results
     */		
	protected function send($method, $endpoint = '', $options = [])
	{		
		$request = new Request($method, $endpoint);
		$request = $this->applyToken($request);	
		$response = $this->client->send($request, ['json' => $options]);
		
		return json_decode($response->getBody(), true) ?: (string) $response->getBody();
	}
	
    /**
     * On first authentication of an user or if the current session token is expired. An new token will be requested.
     * 
     * @return bool
	 * @throws \App\Classes\ApiService\ApiException
     */	
	protected function acquireAccessToken()
	{
		$url = self::API_AUTH_URI;
		try {
			$response = $this->client->request('POST', $url, ['auth' => [$this->user, $this->pass]]);
		} catch (RequestException $e) {
			return ApiException::failedRequest($e);
		};
				
		$response = \GuzzleHttp\json_decode((string) $response->getBody(), true);
		if(@array_key_exists ('errorCode',$response))
		{
			return $this->setMsg(false, $response['message']);
		}		
		$token_expires = preg_replace("/[^0-9]/", "", $response['expiresAt']);
		
		$this->token = array(
			'token'		=>	$response['token'],
			'expires'	=>	$token_expires,
		);	
		
		$_SESSION['API_TOKEN'] = $this->token;
		return $this->setMsg(true);
	}	
	
    /**
	 * If API_TOKEN_REFRESH is set to false the user will be logged out and forced to authenticate again	
     * Modify guzzle request and add header with authentication bearer token to request
     * 
     * @param object $request Guzzle request object
     * @return object | Modified request with attached auth token
     */
	protected function applyToken($request)
	{
		if(!$this->hasValidToken()) {
			if(!self::API_TOKEN_REFRESH)
			{
				Logger::logToFile(__FILE__, 0, "User token has expired");
				$session = new SessionManager();
				$session->sessionDestroy();				
			}
			// Function to refresh token if expired
			$this->acquireAccessToken();			
		};
		return \GuzzleHttp\Psr7\modify_request($request, [
			'set_headers' => [
				'Authorization' => 'Bearer '. (string) $this->getToken(),
			],
		]);
	}

    /**
     * Check if bearer token is still valid
	 * If API_FORCE_TOKEN is true or API_TOKEN is not set in session then the session will be destroyed. User will be redirected to login page.
	 * If current timestamp is greater than the expiration timestamp of token or if API_FORCE_TOKEN is true
	 * then the token is invalid (or forced to be invalid) and returns false so the token can be renewed.
     * 
     * @return bool | True if token has not yet expired
     */	
	protected function hasValidToken()
	{
		$date = new \DateTime();
		$timestamp = $date->getTimestamp();

		return ( !self::API_FORCE_TOKEN && $timestamp > $_SESSION['API_TOKEN']['expires'] ) ? false : true;
	}

    /**
     * Returns current session token array
     * 
     * @return array
     */		
	protected function getToken()
	{
		return (isset($_SESSION['API_TOKEN']['token'])) ? $_SESSION['API_TOKEN']['token'] : false;
	}
	
    /**
     * Returns response message array
     * 
     * @return array
     */	
	protected function setMsg($status, $msg = '')
	{
		return $this->msg = [
			'status' => $status,
			'msg' => $msg,
		];
	}	
}

