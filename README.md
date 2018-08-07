# Arctos Framework
<p align="center">
<img src="public/img/logo_light.png"  width="20%" align="center" /></p>
Flexible and lightweight framework

# Features
- Easy to use configuration file
- User authentication
- Ticketing
  - Create tickets
  - Update tickets
- User action logging
- Pretty urls
- Model View Controller based routing

# Classes
Multiple classes are integrated in this framework. Designed to be flexible and easy to use or extend.
## Authentication class
The authentication class is designed to check if an user is authenticated. Sub functions include
	- Check if user is admin
	- Check if csrf token is valid
	- Check if application is being brute forced
	- Get current authenticated user id
## CSRF class
The Cross-Site Request Forgery class prevents csrf by attaching an 32bit string to each form. Which will be validated server site.
## LDAP class
The LDAP class provides Single Sign On capabilities. Read below instructions on how to enable and configure SSO.
## Mailer class
The mailer class is an wrapper over PHPmailer. This enables easy creation and sending of application mails. Custom email templates can be created or edited in the folder `C:\xampp\htdocs\Arctos\app\mail`
## Router class
The router class is the main component of the application and uses [FastRoute](https://github.com/nikic/FastRoute). This class regulates all http requests to their respective controller and through the default index.php page located in `C:\xampp\htdocs\Arctos\public\index.php`. 
Routes are defined in the file `C:\xampp\htdocs\Arctos\routes\routes.php`. 
### Usage 
Say you want to show the privacy page if you navigate to `http://arctos.localhost/privacy`. And route should be defined in the routes.php files as followed:
```php
	array('GET', '/privacy', 'PrivacyController/index'),
```
If the url matches the route conditions http method `GET` and url path `/privacy` it will call the `PrivacyController` class and the method `index`.
The method could look like below and would return an array with a new view ```php '../src/views/docs/privacy_'.strtolower(Config::APP_LANG).'.view.php' ```
```php
    public function index()
    {
		$available_lang = $this->lang->getAvailableLanguageFiles();

		$backup_lang = array_diff( $available_lang, array(Config::APP_LANG));
				
		if(file_exists('../src/views/docs/privacy_'.strtolower(Config::APP_LANG).'.view.php'))
		{
			return array('view' => '../src/views/docs/privacy_'.strtolower(Config::APP_LANG).'.view.php');
		}
		else
		{
			foreach($backup_lang as $backup)
			{
				if(file_exists('../src/views/docs/privacy_'.strtolower($backup).'.view.php'))
				{
					return array('view' => '../src/views/docs/privacy_'.strtolower($backup).'.view.php');
				}
			}
		}
    }
```
## Logger class
## Language class
## Api service
## Session manager
## File manager
## Error manager


# IIS7 config
- Create new folder in `%SystemDrive%\inetpub\wwwroot` named **Arctos**
- Go to IIS manager
- Expand **Sites**, right click and choose **Add Web Site**
- Name the new site **Arctos**
- Click on your new site
- Right click on **Default Document** icon and select **Basic Settings**
- Change physical path to : `%SystemDrive%\inetpub\wwwroot\arctos`
- web.config file is essential for the configuration of IIS web site  
- web.config can be edited manually by copying the below web.config to the web.config located in `%SystemDrive%\inetpub\wwwroot\arctos` 

```xml
	<?xml version="1.0" encoding="UTF-8"?>
	<configuration>
		<system.webServer>
			<rewrite>
				<rules>
					<rule name="Imported Rule 1">
						<match url="^403/?$" />
						<action type="Rewrite" url="/src/views/errors/page_403.view.php" />
					</rule>
					<rule name="Imported Rule 2">
						<match url="^404/?$" />
						<action type="Rewrite" url="/src/views/errors/page_404.view.php" />
					</rule>
					<rule name="Imported Rule 3">
						<match url="^500/?$" />
						<action type="Rewrite" url="/src/views/errors/page_500.view.php" />
					</rule>
					<rule name="Imported Rule 4">
						<match url="^(app|config|routes|storage|vendor)(/.*|)$" />
						<action type="CustomResponse" statusCode="403" statusReason="Forbidden" statusDescription="Forbidden" />
					</rule>
					<rule name="Imported Rule 5" enabled="true" stopProcessing="true">
						<match url="^(.+)$" ignoreCase="false" />
						<conditions logicalGrouping="MatchAll">
							<add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
							<add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
						</conditions>
						<action type="Rewrite" url="public/index.php/{R:1}" />
					</rule>
				</rules>
			</rewrite>
			<directoryBrowse enabled="true" />
			<defaultDocument>
				<files>
					<add value="public/index.php" />
				</files>
			</defaultDocument>
			<staticContent>
				<mimeMap fileExtension=".hxd" mimeType="application/hxd" />
			</staticContent>
		</system.webServer>
	</configuration>
```

# Domain controller SSO config
- Create user group **FlowAuthenticated** in Active Directory users
- Add users who should be able to access the app to **FlowAuthenticated** group
- User are required to have a email value set in domain user properties
- User email is required to authenticate users
		
# LDAP/SSO config
- Uncomment or add `extension=php_ldap.dll` in php.ini file (`C:\Program Files\PHP\v7.2` in ISS env or `C:\xampp\php` in apache env)
- Firefox needs to be configured before client deployment
	- In firefox browser go to: *about:config*
	- Search: `network.automatic-ntlm-auth.trusted-uris`
	- Enter url of application
	- Search: `network.automatic-ntlm-auth.allow-non-fqdn`
	- Set to true

# Application config
- Go to `C:\inetpub\wwwroot\arctos\config` in ISS env or `C:\xampp\htdocs\arctos\config` in apache env
- Go to `\config` folder
- Copy `config-example.class.php` and rename to `config.class.php`
- Edit `config.class.php`
- Set **DEBUG** to `false` on production env to disable error messages being displayed in the application
- If **DEBUG** is `false`, error messages are writen to error logs located at: `%SystemDrive%\inetpub\wwwroot\arctos\storage\logs\2018\Errors`
- Enter Database credentials under **DB_HOST**, **DB_USER**, **DB_PASS**, **DB_NAME**
- Enter WEB services url under **WS_GATEWAY_URL**

## SSO config
- If application needs to use LDAP authentication set **LDAP_ENABLED** to `true`
- Specify LDAP domain server under **LDAP_DOMAIN**. 
	- You can specify an array with multiple Ip addresses if there are multiple LDAP server in your network
	- Or you can specify a string with the FQDN e.g. *ldap.asb.nl*
- Enter LDAP domain admin account to **LDAP_USERNM** and **USERPASSWD**
	- You can specify a FQDN account e.g. *LDAP\Administrator*
	- or you can specify a distinguished name e.g. *CN=Administrator,CN=Users,DC=ldap,DC=asb,DC=nl*
- Specify LDAP port **(LDAP_PORT)** if it is different than the default *389*