<?php

/**
 *
 */
class sspmod_gepicas_Auth_Source_GepiCAS  extends sspmod_cas_Auth_Source_CAS  {
//class sspmod_gepicas_Auth_Source_GepiCAS  extends SimpleSAML_Auth_Source   {

	/**
	 * @var string search_table_name SQL name of the table
	 */
	private $_search_table_name;

	/**
	 * @var string search_table_name SQL name of the field
	 */
	private $_search_table_cas_uid_column;

	/**
	 * @var string search_table_name SQL name of the field
	 */
	private $_search_table_gepi_login_column;

	/**
	 * @var champ_cas_uid_retour Où trouver l'UID entre $username et $casattributes
	 */
	private $_champ_cas_uid_retour;

	/**
	 * Constructor for this authentication source.
	 *
	 * @param array $info  Information about this authentication source.
	 * @param array $config  Configuration.
	 */
	public function __construct($info, $config) {
		assert('is_array($info)');
		assert('is_array($config)');

		//le ldap n'est pas utilisé, mais il faut une configuration pour éviter une erreur de la classe parente CAS
		$config['ldap'] = array();
		
		/* Call the parent constructor first, as required by the interface. */
		parent::__construct($info, $config);

		if (!array_key_exists('search_table', $config)){
			throw new Exception('gepiCAS authentication source is not properly configured: missing [search_table]');
		}
		$search_table_array = $config['search_table'];
	
		if(isset($search_table_array['name'])){
			$this->_search_table_name = $search_table_array['name'];
		}else{
			throw new Exception("name not specified");
		}

		if(isset($search_table_array['cas_uid_column'])){
			$this->_search_table_cas_uid_column =  $search_table_array['cas_uid_column'];
		}else{
			throw new Exception("cas_uid_column not specified");
		}

		if(isset($search_table_array['gepi_login_column'])){
			$this->_search_table_gepi_login_column =  $search_table_array['gepi_login_column'];
		}else{
			throw new Exception("gepi_login_column not specified");
		}

		if(isset($search_table_array['champ_cas_uid_retour'])){
			$this->_champ_cas_uid_retour =  $search_table_array['champ_cas_uid_retour'];
		}else{
			throw new Exception("champ_uid_retour not specified");
		}
		
	}

	/**
	 * Called by linkback, to finish validate/ finish logging in.
	 * @param state $state
	 * @return list username, casattributes/ldap attributes
	 */
	public function finalStep(&$state) {
global $mysqli;
		$ticket = $state['cas:ticket'];
		$stateID = SimpleSAML_Auth_State::saveState($state, self::STAGE_INIT);
		$service =  SimpleSAML_Module::getModuleURL('cas/linkback.php', array('stateID' => $stateID));
		list($username, $casattributes) = $this->casValidation($ticket, $service);

		//recherche du login gepi
		$path = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
		require_once($path."/secure/connect.inc.php");
		// Database connection
		require_once($path."/lib/mysql.inc");
		
		if ($this->_champ_cas_uid_retour == 'username') {
			$uid = $username;
		} else {
			$uid = $casattributes['uid'];
		}

		$requete = 'SELECT '.$this->_search_table_gepi_login_column.' FROM '.$this->_search_table_name.' WHERE '.$this->_search_table_cas_uid_column.'=\''.$uid.'\'';
		$result = $mysqli->query($requete);
		
		$valeur = $result->fetch_array(MYSQLI_NUM);
		if (!$valeur) {
			//utilisateur non trouvé dans la base gepi, l'authentification a échoué
				SimpleSAML_Logger::error('gepicas:' . $this->authId .
					': not authenticated. User is in the CAS but not in the gepi local database.');
				throw new SimpleSAML_Error_UserNotFound('Utilisateur non trouve dans la base locale');			
		}
		$attributes['login'] = array($valeur[0]);
		$attributes['login_gepi'] = array($valeur[0]);
		
		# On interroge la base de données pour récupérer des attributs qu'on va retourner
		# Cela ne sert pas à gepi directement mais à des services qui peuvent s'appuyer sur gepi pour l'athentification
		$query = $mysqli->query("SELECT nom, prenom, email, statut FROM utilisateurs WHERE (login = '".$attributes['login_gepi'][0]."')");
		$row = $query->fetch_object();
		
		$attributes['nom'] = array($row->nom);
		$attributes['prenom'] = array($row->prenom);
		$attributes['statut'] = array($row->statut);
		$attributes['email'] = array($row->email);
		
		$state['Attributes'] = $attributes;
		
		SimpleSAML_Auth_Source::completeAuth($state);
	}
}