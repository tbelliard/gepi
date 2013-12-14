 <?php

/**
 * Simple SQL authentication source
 *
 * This class is an example authentication source which authenticates an user
 * against a SQL database.
 *
 * @package simpleSAMLphp
 */
class sspmod_gepiauth_Auth_Source_LocalDB extends sspmod_core_Auth_UserPassOrgBase {

    /**
     * Le statut requis pour cette connexion (utilisÃ© pour l'admin simplesaml
     */
    private $requiredStatut = null;

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

        if (array_key_exists('required_statut', $config)) {
            $this->requiredStatut = $config['required_statut'];
        }
    }
    
    /**
     * Retrieve list of organizations.
     *
     * The list of organizations is an associative array. The key of the array is the
     * id of the organization, and the value is the description. The value can be another
     * array, in which case that array is expected to contain language-code to
     * description mappings.
     *
     * @return array  Associative array with the organizations.
     */
    protected function getOrganizations() {
        $path = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
        if (file_exists($path."/secure/multisite.ini.php")) { 
            $init = parse_ini_file($path."/secure/multisite.ini.php", TRUE);
            $orgs = Array();
            foreach($init as $key => $value) {
                if (isset($value['nometablissement'])) {
                    $orgs[$key] = $value['nometablissement'];
                } else {
                    $orgs[$key] = $key;
                }
            }
            return $orgs;
        }
        return null;
    }
    
    /**
     * Initialize login.
     *
     * This function saves the information about the login, and redirects to a
     * login page.
     *
     * @param array &$state  Information about the current authentication.
     */
    public function authenticate(&$state) {
        assert('is_array($state)');

        /* We are going to need the authId in order to retrieve this authentication source later. */
        $state[self::AUTHID] = $this->authId;

        $id = SimpleSAML_Auth_State::saveState($state, self::STAGEID);

        $url = SimpleSAML_Module::getModuleURL('gepiauth/loginuserpassorg.php');
        $params = array('AuthState' => $id);
        SimpleSAML_Utilities::redirect($url, $params);
    }

    /**
     * Attempt to log in using the given username and password.
     *
     * On a successful login, this function should return the users attributes. On failure,
     * it should throw an exception. If the error was caused by the user entering the wrong
     * username or password, a SimpleSAML_Error_Error('WRONGUSERPASS') should be thrown.
     *
     * Note that both the username and the password are UTF-8 encoded.
     *
     * @param string $username  The username the user wrote.
     * @param string $password  The password the user wrote.
     * @param string $organization  The id of the organization the user chose.
     * @return array  Associative array with the users attributes.
     */
    protected function login($username, $password, $organization) {
        assert('is_string($username)');
        assert('is_string($password)');
        assert('is_string($organization)');
        
        if ($organization != '') {
            //$organization contient le numÃ©ro de rne
            setcookie('RNE', $organization, null, '/');
        }

        $path = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
        require_once("$path/secure/connect.inc.php");
        // Database connection
        require_once("$path/lib/mysql.inc");
        require_once("$path/lib/mysqli.inc.php");
        require_once("$path/lib/settings.inc");
        require_once("$path/lib/settings.inc.php");
        require_once("$path/lib/old_mysql_result.php");
        // Load settings
        if (!loadSettings()) {
            die("Erreur chargement settings");
        }
        // Global configuration file
        require_once("$path/lib/global.inc.php");
        // Libraries
        include "$path/lib/share.inc.php";

        // Session related functions
        require_once("$path/lib/Session.class.php");
        
        $session_gepi = new Session();
        
        # L'instance de Session permettant de gÃ©rer directement les authentifications
        # SSO, on ne s'embÃªte pas :
        $auth = $session_gepi->authenticate_gepi($username, $password);
                
        if ($auth != "1") {
            # Echec d'authentification.
            $session_gepi->record_failed_login($username);
            session_write_close();
            SimpleSAML_Logger::error('gepiauth:' . $this->authId .
                ': not authenticated. Probably wrong username/password.');
            throw new SimpleSAML_Error_Error('WRONGUSERPASS');            
        }

        SimpleSAML_Logger::info('gepiauth:' . $this->authId . ': authenticated');
        
        # On interroge la base de donnÃ©es pour rÃ©cupÃ©rer des attributs qu'on va retourner
        $query = mysqli_query($GLOBALS["mysqli"], "SELECT nom, prenom, email, statut FROM utilisateurs WHERE (login = '".$username."')");
        $row = mysqli_fetch_object($query);
        
        //on vÃ©rifie le status
        if ($this->requiredStatut != null) {
            if ($this->requiredStatut != $row->statut) {
                # Echec d'authentification pour ce statut
                $session_gepi->close('2');
                session_write_close();
                SimpleSAML_Logger::error('gepiauth:' . $this->authId .
                    ': not authenticated. Statut is wrong.');
                throw new SimpleSAML_Error_Error('WRONGUSERPASS');            
            }
        }
        
        $attributes = array();
        $attributes['login_gepi'] = array($username);
        $attributes['nom'] = array($row->nom);
        $attributes['prenom'] = array($row->prenom);
        $attributes['statut'] = array($row->statut);
        $attributes['email'] = array($row->email);
        
        $sql = "SELECT id_matiere FROM j_professeurs_matieres WHERE (id_professeur = '" . $username . "') ORDER BY ordre_matieres LIMIT 1";
        $matiere_principale = sql_query1($sql);
        $attributes['matieres'] = array($matiere_principale);
        
        SimpleSAML_Logger::info('gepiauth:' . $this->authId . ': Attributes: ' .
            implode(',', array_keys($attributes)));
            
        return $attributes;
    }

}

?> 