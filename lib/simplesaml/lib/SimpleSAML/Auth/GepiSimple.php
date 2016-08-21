<?php

/**
 * Classe pour l'authentification dans gepi
 *
 * Provides the same interface as Auth_Simple.
 *
 * @package simpleSAMLphp
 */
class SimpleSAML_Auth_GepiSimple extends SimpleSAML_Auth_Simple {

	/**
	 * La configuration de la source sélectionnée
	 *
	 * @var array
	 */
	protected $authSourceConfig;
		
	/**
	 * The id of the authentication source we are accessing.
	 *
	 * @var string
	 */
	private $authSource;
	
	/**
	 * Initialise une authentification en utilisant les paramêtre renseignés dans gepi
	 *
	 * @param string|NULL $auth  The authentication source. Si non précisé, utilise la source configurée dans gepi.
	 */
	public function __construct($auth = null) {
		if ($auth == null) {
			if (isset($_SESSION['utilisateur_saml_source'])) {
				//on prend la source précisée précedemment en session.
				//Cela sert si le mode d'authentification a changé au cours de la session de l'utilisateur
				$auth = $_SESSION['utilisateur_saml_source'];
			} else {
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
		}
		
		$config = SimpleSAML_Configuration::getOptionalConfig('authsources.php');
		$sources = $config->getOptions();
		if (!count($sources)) {
			echo 'Erreur simplesaml : Aucune source configurée dans le fichier authsources.php';
			die;
		}
		if (!in_array($auth, $sources)) {
			//si la source précisée n'est pas trouvée, utilisation par défaut d'une source proposant tout les choix possible
			//(voir le fichier authsources.php)
			if ($auth == 'unset') {
				//l'admin a réglé la source à unset, ce n'est pas la peine de mettre un message d'erreur
			} else {
				echo 'Erreur simplesaml : source '.$auth.' non configurée. Utilisation par défaut de la source : «Authentification au choix entre toutes les sources configurees».';
			}
			$auth = 'Authentification au choix entre toutes les sources configurees';
		}
		
		//on utilise une variable en session pour se souvenir quelle est la source utilisé pour cette session. Utile pour le logout, si entretemps l'admin a changé la source d'authentification.
		$_SESSION['utilisateur_saml_source'] = $auth;
		
		//print_r($config);die;
		$this->authSourceConfig = $config->getArray($auth);
		
		assert('is_string($auth)');

		$this->authSource = $auth;
		
		parent::__construct($auth);
	}

	/**
	 * Ajouter pour gepi : utilisation des cookies et requetes organisation
	 * Start an authentication process.
	 *
	 * This function never returns.
	 *
	 * This function accepts an array $params, which controls some parts of
	 * the authentication. The accepted parameters depends on the authentication
	 * source being used. Some parameters are generic:
	 *  - 'ErrorURL': An URL that should receive errors from the authentication.
	 *  - 'KeepPost': If the current request is a POST request, keep the POST
	 *    data until after the authentication.
	 *  - 'ReturnTo': The URL the user should be returned to after authentication.
	 *  - 'ReturnCallback': The function we should call after the user has
	 *    finished authentication.
	 *
	 * @param array $params  Various options to the authentication request.
	 */
	public function login(array $params = array()) {
		if (!isset($params['multiauth:preselect'])) {
			if (isset($_REQUEST['source'])) {
				$params['multiauth:preselect'] = $_REQUEST['source'];
			} else if (isset($_COOKIE['source'])) {
				$params['multiauth:preselect'] = $_COOKIE['source'];
			}
		}

		if (!isset($params['core:organization'])) {
			if (isset($_REQUEST['organization'])) {
				$params['core:organization'] = $_REQUEST['organization'];
			} else if (isset($_COOKIE['organization'])) {
				$params['core:organization'] = $_COOKIE['organization'];
			} else if (isset($_REQUEST['rne'])) {
				$params['core:organization'] = $_REQUEST['rne'];
			} else if (isset($_COOKIE['RNE'])) {
				$params['core:organization'] = $_COOKIE['RNE'];
			}
		}
		
		parent::login($params);
	}
	
	/**
	 * Log the user out.
	 * Ajout : Efface la variable de la source d'authentification de la session
	 * Ajout : ne fait pas le logout de la source si c'est précisé dans la configuration. La fonction retourne dans ce cas là
	 *
	 * This function logs the user out. It will never return. By default,
	 * it will cause a redirect to the current page after logging the user
	 * out, but a different URL can be given with the $params parameter.
	 *
	 * Generic parameters are:
	 *  - 'ReturnTo': The URL the user should be returned to after logout.
	 *  - 'ReturnCallback': The function that should be called after logout.
	 *  - 'ReturnStateParam': The parameter we should return the state in when redirecting.
	 *  - 'ReturnStateStage': The stage the state array should be saved with.
	 *
	 * @param string|array|NULL $params  Either the url the user should be redirected to after logging out,
	 *                                   or an array with parameters for the logout. If this parameter is
	 *                                   NULL, we will return to the current page.
	 */
	public function logout($params = NULL) {
		unset($_SESSION['utilisateur_saml_source']);
		

		if ($this->getDoSourceLogout()) {
			parent::logout($params);
		} else {
			assert('is_array($params) || is_string($params) || is_null($params)');
	
			if ($params === NULL) {
				$params = SimpleSAML_Utilities::selfURL();
			}
	
			if (is_string($params)) {
				$params = array(
					'ReturnTo' => $params,
				);
			}
	
			assert('is_array($params)');
			assert('isset($params["ReturnTo"]) || isset($params["ReturnCallback"])');
	
			if (isset($params['ReturnStateParam']) || isset($params['ReturnStateStage'])) {
				assert('isset($params["ReturnStateParam"]) && isset($params["ReturnStateStage"])');
			}
	
			$session = SimpleSAML_Session::getInstance();
			if ($session->isValid($this->authSource)) {
				$state = $session->getAuthData($this->authSource, 'LogoutState');
				if ($state !== NULL) {
					$params = array_merge($state, $params);
				}
	
				$session->doLogout($this->authSource);
	
				$params['LogoutCompletedHandler'] = array(get_class(), 'logoutCompleted');
			}
			
			//on rajoute dans la requet le portal_return_url, ça sera utilisé dans un refresh ultérieur (logout.php ou Session.class.php)
			if (isset($params["ReturnTo"])) {
					$portal_return_url = $this->getPortalReturnUrl();
					//echo $portal_return_url;die;
					if ($portal_return_url != null) {
				 		if (strpos($params["ReturnTo"],'?') === false)  {
				 			$portal_parameter = '?portal_return_url='.$portal_return_url;
				 		} else {
				 			$portal_parameter = '&portal_return_url='.$portal_return_url;
				 		}
						$params["ReturnTo"] .=  $portal_parameter;
					}
			}
			
			self::logoutCompleted($params);
		}
	}
	
	
	/**
	 * retourne la configuration de la source sélectionnée
	 *
	 * @return array
	 */
	public function getAuthSourceConfig() {
		return $this->authSourceConfig;
	}
	
	/**
	 * retourne l'url de retour vers le portail soit des préférences gepi, ou sinon des préférence de la source configurée dans authsource
	 *
	 * @return string
	 */
	public function getPortalReturnUrl() {
		if (getSettingValue('portal_return_url') != null) {
			return getSettingValue('portal_return_url');
		}
		$config = $this->getChosenSourceConfig();
		if (isset($config['portal_return_url'])) {
			return $config['portal_return_url'];
		} else {
			return null;
		}
	}
	
	/**
	 * retourne la configuration de la source réelle utilisée pour l'authentification
	 *
	 * @return array
	 */
	protected function getChosenSourceConfig() {
		//si on est en multiauth, il va y avoir une délégation de source
		//on va donc regarder la configuration de la source choisie par l'utilisateur
		if($this->authSourceConfig[0] != 'multiauth:MultiAuth') {
			return $this->authSourceConfig;
		} else {
			$session = SimpleSAML_Session::getInstance();
			$delegationAuthId = $session->getData(sspmod_multiauth_Auth_Source_MultiAuth::SESSION_SOURCE, $this->authSource);
			if ($delegationAuthId == null) {
				//aucune source choisie pour l'instant par l'utilisateur
				return $this->authSourceConfig;
			}
			$config = SimpleSAML_Configuration::getOptionalConfig('authsources.php');
			return $config->getArray($delegationAuthId);
		}
	}

	/**
	 * retourne l'url de retour vers le portail
	 *
	 * @return array
	 */
	public function getDoSourceLogout() {
		$config = $this->getChosenSourceConfig();
		if (isset($config['do_source_logout'])) {
			return $config['do_source_logout'];
		} else {
			return true;
		}
	}
}
