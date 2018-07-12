<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * LDAP authentication class
 *
 */
 
namespace App\Classes;

use \Config;
use \App\Classes\Logger;

abstract class LdapAuthStatus
{
    const LDAP_CONN_FAIL 	= "DC N/A, PLEASE TRY AGAIN LATER";
    const LDAP_CONN_NA 		= "NO DOMAIN CONTROLLERS AVAILABLE AT PRESENT, PLEASE TRY AGAIN LATER";
    const LDAP_CONN_SUCCESS = "LDAP BIND SUCCESSFULL...";
    const LDAP_BIND_FAIL 	= "LDAP BIND FAILED";
    const LDAP_SOCKET_FAIL 	= "FAILED TO CONNECT TO SOCKET";
    const LDAP_ANON 		= "ANONYMOUS LOG ON";
}

class LdapAuth {
	private $ldap_conn;
	private $ldap_port 		= Config::LDAP_PORT; 
	private $ldap_timeout 	= Config::LDAP_TIMEOUT;
	private $base 			= 'cn=users,dc=ldap,dc=asb,dc=nl';
	private $group_name 	= 'FlowAuthenticated';
	private $attributes 	= array("member","memberof","dn","distinguishedName","sAMAccountName","userPrincipalName","objectGUID","displayName","givenName","sn","mail","company","title","department","ipPhone","mobile");
	
	function __construct(){

	}
	
	public function setLdapConn($dclist)
	{
		$list = array();
		$ldap_dc = false;
		if(is_array($dclist))
		{
			$list = $dclist;
		}
		else
		{
			$list = (gethostbynamel($dclist)) ? gethostbynamel($dclist) : array();
		}

		foreach ($list as $k => $dc) {
			if ($this->ldapServerPing($dc) === true) {
				$ldap_dc = $dc;
				break; 
			} 
		}

		if (!$ldap_dc) {
			throw new Exception(LdapAuthStatus::LDAP_CONN_NA);
		}
		
		$this->ldap_conn = ldap_connect($ldap_dc);	
		if(!$this->ldap_conn)
		{
			throw new Exception(LdapAuthStatus::LDAP_CONN_FAIL);
		}
	}

	public function setLdapOption($option, $new_val)
	{
		ldap_set_option($this->getLdapConn(), $option, $new_val);
	}
	
	public function getLdapConn()
	{
		return $this->ldap_conn;
	}

	public function getLdapAuthGroup()
	{
		return $this->group_name;
	}
	
	public function ldapBind($ldaprdn = null,$ldappass = null)
	{

		if ($this->getLdapConn()) {
			// binding to ldap server
			$ldapbind = @ldap_bind($this->getLdapConn(), $ldaprdn, $ldappass);
	
			// verify binding
			if ($ldapbind) {
				//echo LdapAuthStatus::LDAP_CONN_SUCCESS;
				//return $this->getLdapConn();
				return true;
			} else {
				//throw new \Exception(LdapAuthStatus::LDAP_BIND_FAIL);
				return false;
				ldap_close($this->getLdapConn());
			}   
		}		
	}
	
	public function ldapAuthenticate($user)
	{
		$sam_account = explode("\\",$user);
		$sam_account = $sam_account[1];
		
		//$user_result = ldap_list($this->getLdapConn(), $this->base, "sAMAccountName={$sam_account}", $this->attributes);
		//$user_info = ldap_get_entries($this->getLdapConn(), $user_result);
		$user_info = $this->ldapSearch("sAMAccountName={$sam_account}", $this->attributes);
		$user_token = $user_info[0]["dn"];
		
		$group_info = $this->ldapSearch("cn=".$this->group_name, $this->attributes);
		$group_token = $group_info[0]['member'];
		
		$auth = false;
		foreach($group_token as $group)
		{
			if($user_token == $group)
			{
				// Create LDAP user session
				$this->ldapCreateUserSession($user_info);
				$auth = true;
				break;
			}
	
		}
		return $auth;		
	}
	
	public function ldapSearch($filter, $attr = null)
	{
		$attributes = ($attr == null) ? $this->attributes : $attr; 
		$conn = $this->getLdapConn();
		$result = ldap_list($conn, $this->base, $filter, $attributes);
		return ldap_get_entries($conn, $result);
	}	
	
	public function ldapCountRemover($arr)
	{
		foreach($arr as $key=>$val) {
			# (int)0 == "count", so we need to use ===
			if($key === "count") 
			unset($arr[$key]);
			elseif(is_array($val))
			$arr[$key] = $this->ldapCountRemover($arr[$key]);
		}
		return $arr;		
	}

	public function ldapCreateUserSession($info)
	{
		for($i = 0; $i < $info["count"]; $i++)
        {
			$users["user_id"] = $info[$i]["objectguid"][0];
            $users["user_name"] = $info[$i]["givenname"][0];
            $users["user_last_name"] = $info[$i]["sn"][0];
            $users["user_email"] = $info[$i]["mail"][0];
            $users["mobile"] = $info[$i]["mobile"][0];
            $users["user_ip_phone"] = $info[$i]["ipphone"][0];
            $users["department"] = $info[$i]["department"][0];
            $users["title"] = $info[$i]["title"][0];

        }
		$_SESSION[Config::SES_NAME] = $users;
	}
		
	private function checkSecureConnection()
	{
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true : false;
	}
	
	private function ldapServerPing($host)
	{
		$op = @fsockopen($host, $this->ldap_port, $errno, $errstr, $this->ldap_timeout);
		if (!$op) {
			//DC is N/A
			// Maybe log failed attempt
			// echo "SOCKET ERROR: {$errno} - {$errstr}";
			//throw new Exception(LdapAuthStatus::LDAP_SOCKET_FAIL);
			return false; 
		} else {
			//explicitly close open socket connection
			fclose($op); 
			//DC is up & running, we can safely connect with ldap_connect
			return true; 
		}
	}
}

	/*
	try {
		//$name = 'LDAP\RGO';
		//print_r( explode("\\",$name));
		//echo $_SERVER['LOGON_USER'];
		//echo $_SERVER['AUTH_USER'];
		//echo '<br>';
		
		$ldap = new LdapAuth();
		$ldap->setLdapConn(Config::LDAP_DOMAIN);
		$ldap->setLdapOption(LDAP_OPT_REFERRALS, 0);
		$ldap->setLdapOption(LDAP_OPT_PROTOCOL_VERSION, 3);
		$ldap->ldapBind(Config::LDAP_USERNM, Config::LDAP_PASSWD);
		
		if($ldap->ldapAuthenticate($_SERVER['AUTH_USER']))
		{
			echo 'home page';
			session_start();
			$_SESSION['user'] = $_SERVER['AUTH_USER'];
		} 
		else
		{
			echo 'Please login';
		}
		
		if(isset($_SESSION['user']))
		{
			print_r($_SESSION['user']);
		}

	} catch (Exception $e) {
		echo 'Exception: ',  $e->getMessage(), "\n";
	}
	*/