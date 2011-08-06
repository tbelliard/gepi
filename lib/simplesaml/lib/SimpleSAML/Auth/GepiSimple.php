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
	 * @param string|NULL $auth  The authentication source. Si non précisé, utilise la source configurée dans gepi.
	 */
	public function __construct($auth = null) {
		if ($auth == null) {
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
		    $auth = getSettingValue('auth_simpleSAML_source');
		}
		
		$config = SimpleSAML_Configuration::getOptionalConfig('authsources.php');
		$sources = $config->getOptions();
		if (!in_array($auth, $sources)) {
			//en cas d'erreur, pour forcer le choix, décommenter la ligne suivante:
			//$auth = 'gepi-local-db';
			//et commenter les deux lignes ci-dessous.
			echo 'Erreur : source '.$auth.' non configurée.';
			die;
		}
			
		parent::__construct($auth);
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
