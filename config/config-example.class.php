<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * APPLICATION defined config items
 *
 * Predefined items which can be added to fit your application
 * it is possible to add new config items according to the following:
 *  - Config constants must be all UPPERCASE
 *
 * Config class is available through the root namespace: \
 */

class Config
{
	/**
     * Name of the application
     * @var string
     */
    const APP_COPYRIGHT = ''; 
	
    /**
     * Name of the application
     * @var string
     */
    const APP_TITLE = 'ARCTOS';
	
    /**
     * Language file to be used in the application. 
	 * Default: en
	 * Langeage files are available in: /src/lang/
	 * @var string
     */
    const APP_LANG = 'nl';
	
    /**
     * Email address from which email are send
     * @var string
     */	
    const APP_EMAIL = 'info@beheercentrum.nl';
	
    /**
     * Application version number
     * @var string
     */	
    const APP_VER = '1.0.6';
	
    /**
     * Application enviroment e.g OTAP, TESTING, LIVE etc
     * @var string
     */	
    const APP_ENV = 'OTAP';
	
    /**
     * Application theme.
     * Default: light
     * Options:
     *      - light
     *      - dark
     * @var string
     */		
    const APP_THEME = 'light';

    /**
     * Specify the name of the image to be used as logo
     * @var string
     */		
    const LOGO_NAME = 'logo_light.png';
	
    /**
     * Specify the name of the image to be used as favicon
     * @var string
     */		
    const FAVICON_NAME = 'favicon.png';
		
    /**
     * Specify the session name
     * @var string
     */		
    const SES_NAME = 'arc_user';
	
    /**
     * Enable or disable debug.
     * Default: false
     * @var bool
     */		
    const DEBUG = true;

    /**
     * Enable or disable maintenance
     * Default: false
     * @var bool
     */		
    const MAINTENANCE = false;
	
    /**
     * DATABASE config items
     */
    const DB_HOST = '';
    const DB_USER = '';
    const DB_PASS = '';
    const DB_NAME = '';

    /**
     * Enable LDAP authentication
	 * Default: false
     * @var bool
     */	
	const LDAP_ENABLED 	= false;
	
	/**
     * LDAP host.
	 * Enter static array with multiple IPs of host or
	 * enter domain name string e.g: ldap.asb.nl
	 * Default: array()
     * @var array|string
     */	
	const LDAP_DOMAIN 	= array();
	
	/**
     * LDAP connection port
	 * Default: 389
     * @var integer
     */	
	const LDAP_PORT 	= 389;
	
	/**
     * LDAP connection ping timeout
	 * Default: 1 second
     * @var integer
     */	
	const LDAP_TIMEOUT 	= 1;	
	
	/**
     * LDAP authentication user
     * @var string
     */	
	const LDAP_USERNM 	= '';
	
	/**
     * LDAP authentication user password
     * @var string
     */		
	const LDAP_PASSWD 	= '';	
}

