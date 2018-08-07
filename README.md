# Arctos Framework
<p align="center">
<img src="public/img/logo_light.png"  width="20%" align="center" /></p>

# Features
* User authentication
* Ticketing
  * Item 2a
  * Item 2b
* User action logging

# Classes
Multiple classes are integrated in this framework. Designed to be flexible and easy to use or extend.
- Authentication class
- CSRF class
- LDAP class
- Mailer class
- Router class
- Logger class
- Language class
- Api service
- Session manager
- File manager
- Error manager


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