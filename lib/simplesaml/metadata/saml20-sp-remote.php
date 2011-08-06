<?php
/**
 * SAML 2.0 remote SP metadata for simpleSAMLphp.
 *
 * See: http://simplesamlphp.org/docs/trunk/simplesamlphp-reference-sp-remote
 */

/* Pour utiliser une configuration manuelle, décommenter la configuration ci dessous et commenter la configuration automatique en fin de fichier
//la chaîne nom-arbitraire-SACoche-monserveur est un entityID qui doit être le même que le fichier simplesaml/config/authsources.cfg du fournisseur de service (sacoche)

$metadata['nom-arbitraire-monserveur-sacoche'] = array(
	'AssertionConsumerService' => 'https://www.mon-serveur-acoche.fr/SACoche/simplesaml/module.php/saml/sp/saml2-acs.php/distant-gepi-saml',
	'SingleLogoutService' => 'http://www.mon-serveur-acoche.fr/SACoche/simplesaml/module.php/saml/sp/saml2-logout.php/distant-gepi-saml',
);*/

/* configuration automatique */
if (getSettingValue('gepiEnableIdpSaml20') == 'yes') {
	//on va charger l'adresse SACoche configurée en admin gepi si elle est précisée
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
		if (substr($sacocheUrl,strlen($sacocheUrl)-1,1) != '/') {$sacocheUrl .= '/';} //on rajout un / a� la fin
		$firstEntityID = 'sacoche-sp';
		$firstEntityArray = array();
		$firstEntityArray['AssertionConsumerService'] = $sacocheUrl.'_lib/SimpleSAMLphp/module.php/saml/sp/saml2-acs.php/distant-gepi-saml';
		$firstEntityArray['SingleLogoutService'] = $sacocheUrl.'_lib/SimpleSAMLphp/module.php/saml/sp/saml2-logout.php/distant-gepi-saml';
		$metadata[$firstEntityID]= $firstEntityArray;
	}
}
/* fin de configuration automatique */
?>
