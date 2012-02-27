<?php
/* 
 * The configuration of simpleSAMLphp
 * 
 */

$config = array (

	/**
	 * Setup the following parameters to match the directory of your installation.
	 * See the user manual for more details.
	 *
	 * Valid format for baseurlpath is:
	 * [(http|https)://(hostname|fqdn)[:port]]/[path/to/simplesaml/]
	 * (note that it must end with a '/')
	 *
	 * The full url format is useful if your simpleSAMLphp setup is hosted behind
	 * a reverse proxy. In that case you can specify the external url here.
	 *
	 * Please note that simpleSAMLphp will then redirect all queries to the
	 * external url, no matter where you come from (direct access or via the
	 * reverse proxy).
	 */
	'baseurlpath'           => 'gepi/lib/simplesaml/www/', //voir configuration automatique à la fin de ce fichier
	'certdir'               => 'cert/',
	'loggingdir'            => 'log/',
	'datadir'               => 'data/',

	/*
	 * A directory where simpleSAMLphp can save temporary files.
	 *
	 * SimpleSAMLphp will attempt to create this directory if it doesn't exist.
	 */
	'tempdir'               => '../../temp/simpleSAML',
	

	/*
	 * If you enable this option, simpleSAMLphp will log all sent and received messages
	 * to the log file.
	 *
	 * This option also enables logging of the messages that are encrypted and decrypted.
	 *
	 * Note: The messages are logged with the DEBUG log level, so you also need to set
	 * the 'logging.level' option to LOG_DEBUG.
	 */
	'debug' => FALSE,


	'showerrors'            =>	TRUE,

	/**
	 * Custom error show function called from SimpleSAML_Error_Error::show.
	 * See docs/simplesamlphp-errorhandling.txt for function code example.
	 *
	 * Example:
	 *   'errors.show_function' => 'sspmod_exmaple_Error_Show::show',
	 */

	/**
	 * This option allows you to enable validation of XML data against its
	 * schemas. A warning will be written to the log if validation fails.
	 */
	'debug.validatexml' => FALSE,

	/**
	 * This password must be kept secret, and modified from the default value 123.
	 * This password will give access to the installation page of simpleSAMLphp with
	 * metadata listing and diagnostics pages.
	 */
	'auth.adminpassword'		=> '482qcabv',//rÃ©glage inutile car l'authentification de l'admin simplesaml se fait par gepi, donc il faut un login admin gepi
	'admin.protectindexpage'	=> true,
	'admin.protectmetadata'		=> true,

	/**
	 * This is a secret salt used by simpleSAMLphp when it needs to generate a secure hash
	 * of a value. It must be changed from its default value to a secret value. The value of
	 * 'secretsalt' can be any valid string of any length.
	 *
	 * A possible way to generate a random salt is by running the following command from a unix shell:
	 * tr -c -d '0123456789abcdefghijklmnopqrstuvwxyz' </dev/urandom | dd bs=32 count=1 2>/dev/null;echo
	 */
	'secretsalt' => 'fc3bs6eojtk8ax1zv77e6m08r73bva4h',
	
	/*
	 * Some information about the technical persons running this installation.
	 * The email address will be used as the recipient address for error reports, and
	 * also as the technical contact in generated metadata.
	 */
	'technicalcontact_name'     => 'Administrateur',
	'technicalcontact_email'    => 'noemail', //voir configuration automatique à la fin de ce fichier

	/*
	 * The timezone of the server. This option should be set to the timezone you want
	 * simpleSAMLphp to report the time in. The default is to guess the timezone based
	 * on your system timezone.
	 *
	 * See this page for a list of valid timezones: http://php.net/manual/en/timezones.php
	 */
	'timezone' => NULL,

	/*
	 * Logging.
	 * 
	 * define the minimum log level to log
	 *		LOG_ERR				No statistics, only errors
	 *		LOG_WARNING			No statistics, only warnings/errors
	 *		LOG_NOTICE			Statistics and errors 
	 *		LOG_INFO			Verbose logs
	 *		LOG_DEBUG			Full debug logs - not reccomended for production
	 * 
	 * Choose logging handler.
	 * 
	 * Options: [syslog,file,errorlog]
	 * 
	 */
	'logging.level'         => LOG_ERR,
	'logging.handler'       => 'syslog',

	/*
	 * Choose which facility should be used when logging with syslog.
	 *
	 * These can be used for filtering the syslog output from simpleSAMLphp into its
	 * own file by configuring the syslog daemon.
	 *
	 * See the documentation for openlog (http://php.net/manual/en/function.openlog.php) for available
	 * facilities. Note that only LOG_USER is valid on windows.
	 *
	 * The default is to use LOG_LOCAL5 if available, and fall back to LOG_USER if not.
	 */
	'logging.facility' => defined('LOG_LOCAL5') ? constant('LOG_LOCAL5') : LOG_USER,

	/*
	 * The process name that should be used when logging to syslog.
	 * The value is also written out by the other logging handlers.
	 */
	'logging.processname' => 'simplesamlphp',

	/* Logging: file - Logfilename in the loggingdir from above.
	 */
	'logging.logfile'		=> 'simplesamlphp.log',
	
	

	/*
	 * Enable
	 * 
	 * Which functionality in simpleSAMLphp do you want to enable. Normally you would enable only 
	 * one of the functionalities below, but in some cases you could run multiple functionalities.
	 * In example when you are setting up a federation bridge.
	 */
	'enable.saml20-idp'		=> false, //voir configuration automatique à la fin de ce fichier
	'enable.shib13-idp'		=> false,
	'enable.adfs-idp'		=> false,
	'enable.wsfed-sp'		=> false,
	'enable.authmemcookie' => false,

	/* 
	 * This value is the duration of the session in seconds. Make sure that the time duration of
	 * cookies both at the SP and the IdP exceeds this duration.
	 */
	'session.duration'		=>  8 * (60*60), // 8 hours. //voir configuration automatique à la fin de ce fichier
	'session.requestcache'	=>  4 * (60*60), // 4 hours

	/*
	 * Sets the duration, in seconds, data should be stored in the datastore. As the datastore is used for
	 * login and logout requests, thid option will control the maximum time these operations can take.
	 * The default is 4 hours (4*60*60) seconds, which should be more than enough for these operations.
	 */
	'session.datastore.timeout' => (4*60*60), // 4 hours
	

	/*
	 * Expiration time for the session cookie, in seconds.
	 *
	 * Defaults to 0, which means that the cookie expires when the browser is closed.
	 *
	 * Example:
	 *  'session.cookie.lifetime' => 30*60,
	 */
	'session.cookie.lifetime' => 0,

	/*
	 * Limit the path of the cookies.
	 *
	 * Can be used to limit the path of the cookies to a specific subdirectory.
	 *
	 * Example:
	 *  'session.cookie.path' => '/simplesaml/',
	 */
	'session.cookie.path' => '/',

	/*
	 * Cookie domain.
	 *
	 * Can be used to make the session cookie available to several domains.
	 *
	 * Example:
	 *  'session.cookie.domain' => '.example.org',
	 */
	'session.cookie.domain' => NULL,

	/*
	 * Set the secure flag in the cookie.
	 *
	 * Set this to TRUE if the user only accesses your service
	 * through https. If the user can access the service through
	 * both http and https, this must be set to FALSE.
	 */
	'session.cookie.secure' => false,

	/*
	 * Options to override the default settings for php sessions.
	 */
	'session.phpsession.cookiename'  => 'GEPI',
	'session.phpsession.savepath'    => null,
	'session.phpsession.httponly'    => FALSE,
	
	/*
	 * Languages available and what language is default
	 */
	'language.available'	=> array('en', 'no', 'nn', 'se', 'da', 'de', 'sv', 'fi', 'es', 'fr', 'it', 'nl', 'lb', 'cs', 'sl', 'lt', 'hr', 'hu', 'pl', 'pt', 'pt-BR', 'tr', 'ja', 'zh-tw'),
	'language.default'		=> 'fr',

	/*
	 * Extra dictionary for attribute names.
	 * This can be used to define local attributes.
	 *
	 * The format of the parameter is a string with <module>:<dictionary>.
	 *
	 * Specifying this option will cause us to look for modules/<module>/dictionaries/<dictionary>.definition.json
	 * The dictionary should look something like:
	 *
	 * {
	 *     "firstattribute": {
	 *         "en": "English name",
	 *         "no": "Norwegian name"
	 *     },
	 *     "secondattribute": {
	 *         "en": "English name",
	 *         "no": "Norwegian name"
	 *     }
	 * }
	 *
	 * Note that all attribute names in the dictionary must in lowercase.
	 *
	 * Example: 'attributes.extradictionary' => 'ourmodule:ourattributes',
	 */
	'attributes.extradictionary' => NULL,

	/*
	 * Which theme directory should be used?
	 */
	//'theme.use' 		=> 'default',
	'theme.use'         => 'gepimodule:gepitheme',
	
	/*
	 * Default IdP for WS-Fed.
	 */
	'default-wsfed-idp'	=> 'urn:federation:pingfederate:localhost',

	/*
	 * Whether the discovery service should allow the user to save his choice of IdP.
	 */
	'idpdisco.enableremember' => TRUE,
	'idpdisco.rememberchecked' => TRUE,
	
	// Disco service only accepts entities it knows.
	'idpdisco.validate' => TRUE,
	
	'idpdisco.extDiscoveryStorage' => NULL, 

	/*
	 * IdP Discovery service look configuration. 
	 * Wether to display a list of idp or to display a dropdown box. For many IdP' a dropdown box  
	 * gives the best use experience.
	 * 
	 * When using dropdown box a cookie is used to highlight the previously chosen IdP in the dropdown.  
	 * This makes it easier for the user to choose the IdP
	 * 
	 * Options: [links,dropdown]
	 * 
	 */
	'idpdisco.layout' => 'dropdown',

	/*
	 * Whether simpleSAMLphp should sign the response or the assertion in SAML 1.1 authentication
	 * responses.
	 *
	 * The default is to sign the assertion element, but that can be overridden by setting this
	 * option to TRUE. It can also be overridden on a pr. SP basis by adding an option with the
	 * same name to the metadata of the SP.
	 */
	'shib13.signresponse' => TRUE,
	
	
	
	/*
	 * Authentication processing filters that will be executed for all IdPs
	 * Both Shibboleth and SAML 2.0
	 */
	'authproc.idp' => array(
		/* Enable the authproc filter below to add URN Prefixces to all attributes
 		10 => array(
 			'class' => 'core:AttributeMap', 'addurnprefix'
 		), */
 		/* Enable the authproc filter below to automatically generated eduPersonTargetedID. 
 		20 => 'core:TargetedID',
 		*/

		// Adopts language from attribute to use in UI
 		30 => 'core:LanguageAdaptor',
 		
		/* Add a realm attribute from edupersonprincipalname
		40 => 'core:AttributeRealm',
		 */
		45 => array(
			'class' => 'core:StatisticsWithAttribute',
			'attributename' => 'realm',
			'type' => 'saml20-idp-SSO',
		),

		/* When called without parameters, it will fallback to filter attributes â€¹the old wayâ€º
		 * by checking the 'attributes' parameter in metadata on IdP hosted and SP remote.
		 */
		50 => 'core:AttributeLimit', 
		
		/* 
		 * Search attribute "distinguishedName" for pattern and replaces if found

		60 => array(
			'class'		=> 'core:AttributeAlter',
			'pattern'	=> '/OU=studerende/',
			'replacement'	=> 'Student',
			'subject'	=> 'distinguishedName',
			'%replace',	
		),
		 */

		/*
		 * Consent module is enabled (with no permanent storage, using cookies).

		90 => array(
			'class' 	=> 'consent:Consent', 
			'store' 	=> 'consent:Cookie', 
			'focus' 	=> 'yes', 
			'checked' 	=> TRUE
		),
		 */
		// If language is set in Consent module it will be added as an attribute.
 		99 => 'core:LanguageAdaptor',
	),
	/*
	 * Authentication processing filters that will be executed for all IdPs
	 * Both Shibboleth and SAML 2.0
	 */
	'authproc.sp' => array(
		/*
		10 => array(
			'class' => 'core:AttributeMap', 'removeurnprefix'
		),
		*/

		/* When called without parameters, it will fallback to filter attributes â€¹the old wayâ€º
		 * by checking the 'attributes' parameter in metadata on SP hosted and IdP remote.
		 */
		50 => 'core:AttributeLimit', 

		/*
		 * Generate the 'group' attribute populated from other variables, including eduPersonAffiliation.
		 */
 		60 => array('class' => 'core:GenerateGroups', 'eduPersonAffiliation'),
 		// All users will be members of 'users' and 'members' 	
 		61 => array('class' => 'core:AttributeAdd', 'groups' => array('users', 'members')),
 		
		// Adopts language from attribute to use in UI
 		90 => 'core:LanguageAdaptor',

	),
	

	/*
	 * This option configures the metadata sources. The metadata sources is given as an array with
	 * different metadata sources. When searching for metadata, simpleSAMPphp will search through
	 * the array from start to end.
	 *
	 * Each element in the array is an associative array which configures the metadata source.
	 * The type of the metadata source is given by the 'type' element. For each type we have
	 * different configuration options.
	 *
	 * Flat file metadata handler:
	 * - 'type': This is always 'flatfile'.
	 * - 'directory': The directory we will load the metadata files from. The default value for
	 *                this option is the value of the 'metadatadir' configuration option, or
	 *                'metadata/' if that option is unset.
	 *
	 * XML metadata handler:
	 * This metadata handler parses an XML file with either an EntityDescriptor element or an
	 * EntitiesDescriptor element. The XML file may be stored locally, or (for debugging) on a remote
	 * web server.
	 * The XML hetadata handler defines the following options:
	 * - 'type': This is always 'xml'.
	 * - 'file': Path to the XML file with the metadata.
	 * - 'url': The url to fetch metadata from. THIS IS ONLY FOR DEBUGGING - THERE IS NO CACHING OF THE RESPONSE.
	 *
	 *
	 * Examples:
	 *
	 * This example defines two flatfile sources. One is the default metadata directory, the other
	 * is a metadata directory with autogenerated metadata files.
	 *
	 * 'metadata.sources' => array(
	 *     array('type' => 'flatfile'),
	 *     array('type' => 'flatfile', 'directory' => 'metadata-generated'),
	 *     ),
	 *
	 * This example defines a flatfile source and an XML source.
	 * 'metadata.sources' => array(
	 *     array('type' => 'flatfile'),
	 *     array('type' => 'xml', 'file' => 'idp.example.org-idpMeta.xml'),
	 *     ),
	 *
	 *
	 * Default:
	 * 'metadata.sources' => array(
	 *     array('type' => 'flatfile')
	 *     ),
	 */
	'metadata.sources' => array(
		array('type' => 'flatfile'),
	),


	/*
	 * Configure the datastore for simpleSAMLphp.
	 *
	 * - 'phpsession': Limited datastore, which uses the PHP session.
	 * - 'memcache': Key-value datastore, based on memcache.
	 * - 'sql': SQL datastore, using PDO.
	 *
	 * The default datastore is 'phpsession'.
	 *
	 * (This option replaces the old 'session.handler'-option.)
	 */
	'store.type' => 'phpsession',


	/*
	 * Should signing of generated metadata be enabled by default.
	 *
	 * Metadata signing can also be enabled for a individual SP or IdP by setting the
	 * same option in the metadata for the SP or IdP.
	 */
	'metadata.sign.enable' => FALSE,

	/*
	 * The default key & certificate which should be used to sign generated metadata. These
	 * are files stored in the cert dir.
	 * These values can be overridden by the options with the same names in the SP or
	 * IdP metadata.
	 *
	 * If these aren't specified here or in the metadata for the SP or IdP, then
	 * the 'certificate' and 'privatekey' option in the metadata will be used.
	 * if those aren't set, signing of metadata will fail.
	 */
	'metadata.sign.privatekey' => NULL,
	'metadata.sign.privatekey_pass' => NULL,
	'metadata.sign.certificate' => NULL,


	/*
	 * Proxy to use for retrieving URLs.
	 *
	 * Example:
	 *   'proxy' => 'tcp://proxy.example.com:5100'
	 */
	'proxy' => NULL,

);


$path = dirname(dirname(dirname(dirname(__FILE__))));
include("$path/secure/connect.inc.php");
// Database connection
require_once("$path/lib/mysql.inc");
require_once("$path/lib/settings.inc");
// Load settings
if (!loadSettings()) {
    die("Erreur chargement settings");
}
$config['technicalcontact_email'] = getSettingValue('gepiAdminAdress');	
if (getSettingValue('gepiEnableIdpSaml20') == 'yes') {
	$config['enable.saml20-idp'] = true;
}

$str = $gepiPath;
//si le premier caractère est un / on le prend pas en compte
if (substr($str,0,1) == '/') {
       $str = substr($str,1);
}
// Le dernier caractère doit être un / si on a un chemin non null
if (strlen($str) > 0 and substr($str, 0, -1) != '/') {
       $str .= '/';
}
$hoststr = '';
if (isset($gepiBaseUrl)) {
       $hoststr = $gepiBaseUrl;
       if (substr($hoststr,0,-1) != '/') {
               $hoststr .= '/';
       }
}
$config['baseurlpath'] = $hoststr.$str.'lib/simplesaml/www/';
