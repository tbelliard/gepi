<?php

/**
 *
 */
class sspmod_gepicas_Auth_Source_GepiCAS  extends sspmod_cas_Auth_Source_CAS  {
//class sspmod_gepicas_Auth_Source_GepiCAS  extends SimpleSAML_Auth_Source   {

	/**
	 * The string used to identify our states.
	 */
	const STAGE_INIT = 'sspmod_cas_Auth_Source_CAS.state';
	//const STAGE_INIT = 'sspmod_elyco_Auth_Source_gepiSSO.state';

	/**
	 * The key of the AuthId field in the state.
	 */
	const AUTHID = 'sspmod_cas_Auth_Source_CAS.AuthId';
	//const AUTHID = 'sspmod_elyco_Auth_Source_gepiSSO.AuthId';

	/**
	 * @var array with ldap configuration
	 */
	private $_ldapConfig;

	/**
	 * @var cas configuration
	 */
	private $_casConfig;

	/**
	 * @var cas chosen validation method
	 */
	private $_validationMethod;
	/**
	 * @var cas login method
	 */
	private $_loginMethod;


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
	 * @var search_champ_uid_retour Où trouver l'UID entre $username et $casattributes
	 */
	private $_search_champ_uid_retour;

	/**
	 * Constructor for this authentication source.
	 *
	 * @param array $info  Information about this authentication source.
	 * @param array $config  Configuration.
	 */
	public function __construct($info, $config) {
		assert('is_array($info)');
		assert('is_array($config)');

		/* Call the parent constructor first, as required by the interface. */
		parent::__construct($info, $config);

		$this->_casConfig = $config['cas'];
		$this->_ldapConfig = $config['ldap'];
		
		if(isset($this->_casConfig['serviceValidate'])){
			$this->_validationMethod = 'serviceValidate';
		}elseif(isset($this->_casConfig['validate'])){
			$this->_validationMethod = 'validate';
		}else{
			throw new Exception("validate or serviceValidate not specified");
		}

		if(isset($this->_casConfig['login'])){
			$this->_loginMethod =  $this->_casConfig['login'];
		}else{
			throw new Exception("cas login url not specified");
		}

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

		if(isset($search_table_array['champ_uid_retour'])){
			$this->_search_champ_uid_retour =  $search_table_array['champ_uid_retour'];
		}else{
			throw new Exception("champ_uid_retour not specified");
		}
	}

	/**
	 * This the most simple version of validating, this provides only authentication validation
	 *
	 * @param string $ticket
	 * @param string $service
	 * @return list username and attributes
	 */
	private function casValidate($ticket, $service){
		$url = SimpleSAML_Utilities::addURLparameter($this->_casConfig['validate'], array(
				'ticket' => $ticket,
				'service' => $service,
		));
		$result = SimpleSAML_Utilities::fetch($url);
		$res = preg_split("/\r?\n/",$result);

		if (strcmp($res[0], "yes") == 0) {
			return array($res[1], array());
		} else {
			throw new Exception("Failed to validate CAS service ticket: $ticket");
		}
	}
	


	/**
	 * Uses the cas service validate, this provides additional attributes
	 *
	 * @param string $ticket
	 * @param string $service
	 * @return list username and attributes
	 */
	private function casServiceValidate($ticket, $service){
		$url = SimpleSAML_Utilities::addURLparameter($this->_casConfig['serviceValidate'], array(
				'ticket' => $ticket,
				'service' => $service,
		));
		$result = SimpleSAML_Utilities::fetch($url);

		$dom = DOMDocument::loadXML($result);
		$xPath = new DOMXpath($dom);
		$xPath->registerNamespace("cas", 'http://www.yale.edu/tp/cas');
		$success = $xPath->query("/cas:serviceResponse/cas:authenticationSuccess/cas:user");
		if ($success->length == 0) {
			$failure = $xPath->evaluate("/cas:serviceResponse/cas:authenticationFailure");
			throw new Exception("Error when validating CAS service ticket: " . $failure->item(0)->textContent);
		} else {

			$attributes = array();
			if ($casattributes = $this->_casConfig['attributes']) { # some has attributes in the xml - attributes is a list of XPath expressions to get them
				foreach ($casattributes as $name => $query) {
					$attrs = $xPath->query($query);
					foreach ($attrs as $attrvalue) $attributes[$name][] = $attrvalue->textContent;
				}
			}
			$casusername = $success->item(0)->textContent;

			return array($casusername, $attributes);

		}
	}


	/**
	 * Main validation method, redirects to correct method
	 * (keeps finalStep clean)
	 *
	 * @param string $ticket
	 * @param string $service
	 * @return list username and attributes
	 */
	private function casValidation($ticket, $service){
		switch($this->_validationMethod){
			case 'validate':
				return  $this->casValidate($ticket, $service);
				break;
			case 'serviceValidate':
				return $this->casServiceValidate($ticket, $service);
				break;
			default:
				throw new Exception("validate or serviceValidate not specified");
		}
	}

	/**
	 * Called by linkback, to finish validate/ finish logging in.
	 * @param state $state
	 * @return list username, casattributes/ldap attributes
	 */
	public function finalStep(&$state) {


		$ticket = $state['cas:ticket'];
		$stateID = SimpleSAML_Auth_State::saveState($state, self::STAGE_INIT);
		$service =  SimpleSAML_Module::getModuleURL('cas/linkback.php', array('stateID' => $stateID));
		list($username, $casattributes) = $this->casValidation($ticket, $service);

		//recherche du login gepi
		$path = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
		require_once($path."/secure/connect.inc.php");
		// Database connection
		require_once($path."/lib/mysql.inc");
		
		if ($this->_search_champ_uid_retour == 'username') {
			$uid = $username;
		} else {
			$uid = $casattributes['uid'];
		}
		
		$requete = 'SELECT '.$this->_search_table_gepi_login_column.' FROM '.$this->_search_table_name.' WHERE '.$this->_search_table_cas_uid_column.'=\''.$uid.'\'';
		$result = mysql_query($requete);
		$valeur = mysql_fetch_array($result);
		$attributes['login'] = array($valeur[0]);
		$attributes['login_gepi'][0] = $valeur[0];
		
		$state['Attributes'] = $attributes;
		
		SimpleSAML_Auth_Source::completeAuth($state);
	}





}