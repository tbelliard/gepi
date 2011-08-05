<?php

/**
 * Classe pour l'authentification dans gepi
 *
 * Provides the same interface as Auth_Simple.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_Auth_GepiSimple extends SimpleSAML_Auth_Simple {


	/**
	 * Initialise une authentification en utilisant les paramêtre renseignés dans gepi
	 *
	 * @param string $auth  The authentication page.
	 * @param string|NULL $authority  The authority we should validate the login against.
	 */
	public function __construct() {
		//on va sélectionner la source d'authentification gepi
		$path = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
		include_once("$path/secure/connect.inc.php");
		// Database connection
		require_once("$path/lib/mysql.inc");
		require_once("$path/lib/settings.inc");
		// Load settings
		if (!loadSettings()) {
		    die("Erreur chargement settings");
		}
		$selected_authSource = getSettingValue('auth_simpleSAML_source');
		$config = SimpleSAML_Configuration::getOptionalConfig('authsources.php');
		$sources = $config->getOptions();
		if (!in_array($selected_authSource, $sources)) {
			echo 'Erreur : source '.$selected_authSourcec.' non configurée.';
			die;
		}
			
		parent::__construct($selected_authSource);
	}
	
	/**
	 * Initialise un login en spécifiant automatiquement si besoin un rne
	 *
	 * @param array $params  Various options to the authentication request.
	 */
	public function login(array $params = array()) {
		//on rajoute le rne aux paramètres du login
		$RNE = isset($_GET['rne']) ? $_GET['rne'] : (isset($_COOKIE['RNE']) ? $_COOKIE['RNE'] : (isset($_POST['RNE']) ? $_POST['RNE'] : (isset($_REQUEST['organization']) ? $_REQUEST['organization'] : NULL)));
		if (isset($RNE)) {
			$params['core:organization'] = $RNE;
		}
		parent::login($params);
	}
}
