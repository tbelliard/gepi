<?php
/**
 * SAML 2.0 remote SP metadata for simpleSAMLphp.
 *
 * See: http://simplesamlphp.org/docs/trunk/simplesamlphp-reference-sp-remote
 */

/* configuration automatique */
if (getSettingValue('gepiEnableIdpSaml20') == 'yes') {
	//on va charger l'adresse SACoche configurÃ©e en admin gepi si elle est prÃ©cisÃ©e
	$path = dirname(dirname(dirname(dirname(__FILE__))));
	require_once("$path/secure/connect.inc.php");
	// Database connection
	require_once("$path/lib/mysql.inc");
	require_once("$path/lib/settings.inc");
	// Load settings
	if (!loadSettings()) {
	    die("Erreur chargement settings");
	}
	
	if (getSettingValue('sacocheUrl') != null) {
		$sacocheUrl = getSettingValue('sacocheUrl');
		if (substr($sacocheUrl,strlen($sacocheUrl)-1,1) != '/') {$sacocheUrl .= '/';} //on rajout un / a  la fin
		$firstEntityID = 'sacoche-sp';
		$firstEntityArray = array();
		$firstEntityArray['AssertionConsumerService'] = $sacocheUrl.'_lib/SimpleSAMLphp/www/module.php/saml/sp/saml2-acs.php/distant-gepi-saml';
		$firstEntityArray['SingleLogoutService'] = $sacocheUrl.'_lib/SimpleSAMLphp/www/module.php/saml/sp/saml2-logout.php/distant-gepi-saml';
		$metadata[$firstEntityID]= $firstEntityArray;
	}

	/*configuration pour un gepi distant qui va venir s'identifier sur nous à décommenter sur le serveur maitre
	$metadata['gepi-esclave-sp'] = array(
		'AssertionConsumerService' => 'https://www.mon-serveur-esclave-gepi.fr/gepi/_lib/simplesaml/www/module.php/saml/sp/saml2-acs.php/distant-gepi-saml',
		'SingleLogoutService' => 'http://www.mon-serveur-esclave-gepi.fr/gepi/_lib/simplesaml/www/module.php/saml/sp/saml2-logout.php/distant-gepi-saml',
	);*/
	
}
/* fin de configuration automatique */
?>
