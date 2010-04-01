<?php
class LDAPServer {
	# Le chemin vers le fichier de configuration du LDAP.
	# Le répertoire de Gepi est considéré comme la racine (mais il est possible
	# de remonter plus haut en commençant par "../")
	const config_file = "secure/config_ldap.inc.php";

	# Les données pour se connecter à l'annuaire LDAP
	# Les champs login et password peuvent être laissés vides
	# dans le cas d'une connexion anonyme.
	private $host = "localhost";
	private $port = "389";
	private $login = "";
	private $password = "";
	public $base_dn = "o=gouv,o=fr";
	public $people_ou = "ou=People";

	# Les classes de l'entrée LDAP d'un utilisateur. Elles doivent
	# être cohérentes avec les attributs utilisés.
	private $people_object_classes = array("top","person","inetOrgPerson");

	# Les attributs suivants permettent de lier les champs du
	# LDAP à des champs de la table utilisateurs de Gepi.
	# Seuls ces champs sont utilisés lors de la création à la
	# volée de comptes utilisateurs depuis le LDAP, ou bien lors
	# de la synchronisation dans le sens Gepi -> LDAP (accès en
	# écriture.
	public $champ_login = "uid";
	public $champ_prenom = "";
	public $champ_nom = "sn";
	public $champ_nom_complet = "cn";
	public $champ_email = "mail";
	public $champ_statut = "";
	public $champ_civilite = "";
	public $champ_rne = "";

	public $code_civilite_madame = "Mme";
	public $code_civilite_monsieur = "M.";
	public $code_civilite_mademoiselle = "Mlle";

	# Les attributs ci-dessous permettent de déterminer quel
	# statut donner à des utilisateurs importés à la volée
	# depuis le LDAP.
	# Le test est effectué sur la chaîne du DN. Ces attributs
	# ne sont donc utiles que dans l'hypothèse où le DN contient
	# une information fiable quant au statut de l'utilisateur.
	private $chaine_dn_statut_professeur = "";
	private $chaine_dn_statut_eleve = "";
	private $chaine_dn_statut_responsable = "";
	private $chaine_dn_statut_scolarite = "";
	private $chaine_dn_statut_cpe = "";

	# Type de cryptage utilisé pour la génération des mots de passe
	private $password_encryption = "ssha"; # clear, crypt, md5, ssha

	# Cet attribut contient la connexion à l'annuaire LDAP. Cela
	# évite d'avoir à refaire plusieurs fois la connexion lors de
	# l'exécution d'un même script faisant appel à plusieurs reprises
	# à des requêtes vers l'annuaire.
	public $ds = false;


	public function __construct() {
		# On charge la configuration et on établit la connexion si
		# le serveur a été configuré.
		if (self::is_setup()) {
			$this->load_config();
			$this->ds = $this->connect();
		}
	}

	# Retourne un lien de connexion LDAP
	public function connect() {
		return self::connect_ldap($this->host, $this->port, $this->login, $this->password);
	}

	# Retourne true ou false selon qu'un utilisateur a été trouvé avec le login indiqué
	public function test_user($_login) {
		if ($this->get_user_profile($_login)) {
			return true;
		} else {
			return false;
		}
	}

	# Retourne true ou false selon que l'utilisateur a pu être authentifié
	# avec son mot de passe.
	public function authenticate_user($_login, $_password) {
		// On tente un bind
		$user = $this->get_user_profile($_login);
		$test_bind = @ldap_bind($this->ds,$user["dn"],$_password);

		// On refait le bind pour reprendre les droits
		ldap_bind($this->ds,$this->login,$this->password);

		if ($user && $_password != '' && $test_bind) {
			return true;
		} else {
			return false;
		}
	}

	# Renvoie les informations de l'utilisateur, au format correct Gepi
	# dans un tableau
	public function get_user_profile($_login) {
		$_login = my_ereg_replace("[^-@._[:space:][:alnum:]]", "", $_login); // securite
	    $search_dn = $this->get_dn();
	    $search_filter = "(".$this->champ_login."=".$_login.")";
		$sr = ldap_search($this->ds,$search_dn,$search_filter);
	    $user = array();
	    $user = ldap_get_entries($this->ds,$sr);
        if (array_key_exists(0, $user)) {
        	$infos = array();
        	$infos["dn"] = $user[0]["dn"];

            if ($this->champ_prenom == '' || !array_key_exists($this->champ_prenom, $user[0])) {
        		$user[0][$this->champ_prenom][0] = '';
        	}
            if ($this->champ_nom == '' || !array_key_exists($this->champ_nom, $user[0])) {
        		$user[0][$this->champ_nom][0] = '';
        	}
            if ($this->champ_nom_complet == '' || !array_key_exists($this->champ_nom_complet, $user[0])) {
        		$user[0][$this->champ_nom_complet][0] = '';
        	}

        	$nom = $this->format_name($user[0][$this->champ_prenom][0], $user[0][$this->champ_nom][0], $user[0][$this->champ_nom_complet][0]);

        	$infos["prenom"] = $nom['prenom'];
        	$infos["nom"] = $nom['nom'];

        	if (!array_key_exists($this->champ_email, $user[0])) {
        		$user[0][$this->champ_email][0] = null;
        	}
        	$infos["email"] = $user[0][$this->champ_email][0];

        	if (!array_key_exists($this->champ_civilite, $user[0])) {
        		$user[0][$this->champ_civilite][0] = $this->code_cilivite_madame;
        	}
        	switch ($user[0][$this->champ_civilite][0]) {
        		case $this->code_civilite_madame:
        			$infos["civilite"] = "Mme";
        		break;
        		case $this->code_civilite_mademoiselle:
        			$infos["civilite"] = "Mlle";
        		break;
        		case $this->code_civilite_monsieur:
        			$infos["civilite"] = "M.";
        		break;
        		default:
        			$infos["civilite"] = "Mme";
        		break;
        	}

            if ($this->champ_rne == '' || !array_key_exists($this->champ_rne, $user[0])) {
        		$user[0][$this->champ_rne][0] = "";
        		$user[0][$this->champ_rne]['count'] = 0;
        	}
        	$nbre_rne = $user[0][$this->champ_rne]['count'];

        	// S'il y a plusieurs RNE dans le ldap, on les renvoie tous
        	$infos["rne"] = array();
        	for($a = 0 ; $a < $nbre_rne ; $a++){

				$infos["rne"][$a] = $user[0][$this->champ_rne][$a];

			}


        	# La détermination du statut est la manipulation la plus délicate.
        	# On dispose de deux moyens : un champ du LDAP (le plus simple...)
        	# ou bien une chaîne à tester sur le DN.
        	if ($this->champ_statut != null) {
        		// Le champ statut est défini, alors on teste
        		if (array_key_exists($this->champ_statut, $user[0])) {
        			if (in_array($user[0][$this->champ_statut][0], array("administrateur","professeur","eleve","responsable","scolarite","cpe"))) {
        				$infos["statut"] = $user[0][$this->champ_statut][0];
        			}
        		}
        	} else {
        		// Si on est là, ce qu'on va essayer de tester avec des chaînes de caractères sur le DN
        		// En raison du risque d'erreur en cas de mauvaise configuration, on ne teste pas
        		// le statut administrateur.
        		if ($this->chaine_dn_statut_professeur != '' && strstr($infos["dn"],$this->chaine_dn_statut_professeur)) {
        			$infos["statut"] = "professeur";
        		} else if ($this->chaine_dn_statut_eleve != '' && strstr($infos["dn"],$this->chaine_dn_statut_eleve)) {
        			$infos["statut"] = "eleve";
        		} else if ($this->chaine_dn_statut_responsable != '' && strstr($infos["dn"],$this->chaine_dn_statut_responsable)) {
        			$infos["statut"] = "responsable";
        		} else if ($this->chaine_dn_statut_scolarite != '' && strstr($infos["dn"],$this->chaine_dn_statut_scolarite)) {
        			$infos["statut"] = "scolarite";
        		} else if ($this->chaine_dn_statut_cpe != '' && strstr($infos["dn"],$this->chaine_dn_statut_cpe)) {
        			$infos["statut"] = "cpe";
        		}
        	}
			if (!isset($info["statut"]) || !in_array($infos["statut"], array("administrateur","professeur","eleve","responsable","scolarite","cpe"))) {
				$infos["statut"] = getSettingValue("statut_utilisateur_defaut");
			}

        	return $infos;
        } else {
        	return false;
        }
	}

	/* Permet de récupérer tou sles utilisateurs du LDAP en fonction d'un paramètre
	* retourne la liste des utilisateurs
	*/
	public function get_all_users($type, $param){
		// On laisse la possibilité d'ajouter des lignes dans le code
		if ($type == 'rne') {
			// On utilise le rne de l'établissement pour récupérer les utilisateurs
			$filter = '('.$this->champ_rne.'='.$param.')';
			$sr = ldap_search($this->ds, $this->get_dn(), $filter) ;
			$infos = array();
			$infos = ldap_get_entries($this->ds, $sr);

			return $infos;
		}else{
			return false;
		}
	}

	/*
	* renvoie le dn de recherche dans le ldap
	*/
	private function get_dn(){
		return $this->people_ou.",".$this->base_dn;
	}


	# Ajoute un utilisateur à l'annuaire.
	# Retourne true/false.
	public function add_user($_login, $_nom, $_prenom, $_email, $_civilite, $_password, $_statut) {

		# Si l'utilisateur existe déjà, on abandonne. La mise à jour d'une entrée passe par
		# une autre méthode.
		if ($this->test_user($_login)) {
			return false;
			exit;
		} else {
			# L'utilisateur n'existe pas, on formate les données, et on le créé.
			$dn = $this->champ_login."=".$_login.",".$this->people_ou.",".$this->base_dn;
			$donnees = $this->format_user_data($_login, $_nom, $_prenom, $_email, $_civilite, $_password, $_statut);
			$add = ldap_add($this->ds, $dn, $donnees);
			return $add;
		}
	}

	# Met à jour un utilisateur dans l'annuaire.
	# Retourne true/false
	public function update_user($_login, $_nom, $_prenom, $_email, $_civilite, $_password, $_statut) {

		# Si l'utilisateur n'existe pas, on abandonne. L'ajout d'une entrée passe par
		# une autre méthode.
		if (!$this->test_user($_login)) {
			return false;
			exit;
		} else {
			# L'utilisateur existe, on formate les données, et on modifie l'annuaire.
			$dn = $this->champ_login."=".$_login.",".$this->people_ou.",".$this->base_dn;
			$donnees = $this->format_user_data($_login, $_nom, $_prenom, $_email, $_civilite, $_password, $_statut);
			$modify = ldap_modify($this->ds, $dn, $donnees);
			return $modify;
		}
	}

	# Supprime un utilisateur du LDAP.
	# Retourne true/false
	public function delete_user($_login) {
		# Si l'utilisateur n'existe pas, on arrête tout de suite.
		if (!$this->test_user($_login)) {
			return true;
			exit;
		} else {
			# L'utilisateur existe, on supprime.
			$dn = $this->champ_login."=".$_login.",".$this->people_ou.",".$this->base_dn;
			$delete = ldap_delete($this->ds, $dn);
			return $delete;
		}
	}


	# Cette méthode est utilisée lorsque l'on dispose déjà d'un mot de passé crypté,
	# et que l'on veut l'enregistrer manuellement.
	public function set_manual_password($_login, $_password) {
		$user = $this->get_user_profile($_login);
		$update = ldap_mod_replace($this->ds, $user['dn'], array("userPassword" => $_password));
		return $update;
	}

	public static function connect_ldap($_adresse,$_port,$_login,$_password) {
		# Pour avoir du débug en log serveur, décommenter la ligne suivante.
		#ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
	    $ds = ldap_connect($_adresse, $_port);
	    if($ds) {
	       // On dit qu'on utilise LDAP V3, sinon la V2 par d?faut est utilis? et le bind ne passe pas.
	       $norme = ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	       // Accès non anonyme
	       if ($_login != '') {
	          // On tente un bind
	          $b = ldap_bind($ds, $_login, $_password);
	       } else {
	          // Accès anonyme
	          $b = ldap_bind($ds);
	       }
	       if ($b) {
	           return $ds;
	       } else {
	           return false;
	       }
	    } else {
	       return false;
	    }
	}

	public static function is_setup() {
		return file_exists(dirname(__FILE__)."/../".self::config_file);
	}

	# On récupère les données de configuration présentes dans le fichier
	# /secure/config_ldap.inc.php
	private function load_config() {
		$ldap_config = array();
		if (self::is_setup()) {
			$path = dirname(__FILE__)."/../".self::config_file;
			include($path);

			$available_settings = get_class_vars(get_class($this));
			foreach($available_settings as $key => $value) {
				$varname = "ldap_".$key;
				if (isset($$varname)) {
					$this->$key = $$varname;
				}
			}
		}
	}

	# Encodage d'un mot de passe utilisateur pour l'enregistrer
	# Ce code a été pris de phpLdapPasswd, par Karyl F. Stein
	# voir : http://www.karylstein.com/phpLdapPasswd
	private function encode_password ($password = '', $encoding = '') {
		if ($encoding == '') $encoding = $this->password_encryption;

		if (strcasecmp($encoding, "clear") == 0) {
			$encodedpass = $password;
		} elseif (strcasecmp($encoding, "crypt") == 0) {
			$encodedpass = "{CRYPT}".crypt($password);
		} elseif (strcasecmp($encoding, "md5") == 0) {
			$encodedpass = "{MD5}".base64_encode(pack("H*",md5($password)));
		} elseif (strcasecmp($encoding, "ssha") == 0) {
			mt_srand((double)microtime()*1000000);
			$salt = mhash_keygen_s2k(MHASH_SHA1, $password, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
			$encodedpass = "{SSHA}".base64_encode(mhash(MHASH_SHA1, $password.$salt).$salt);
		} else {
			return false;
			exit;
		}

		return($encodedpass);
	}

	# Cette méthode prend trois paramètres : nom, prénom, nom complet.
	# L'idée est de retourner les trois valeurs complètes en sortie en
	# n'ayant que deux valeurs saisies en entrée. Il n'est en effet pas
	# courant d'avoir les nom et prénom présents de manière distincte dans
	# l'annuaire...
	private function format_name($_prenom, $_nom, $_nom_complet) {
		$result = array();
		if ($_prenom == '' and $_nom == '' and $_nom_complet == '') {
			// On n'a rien... On renvoie donc rien...
			$result['nom'] = '';
			$result['prenom'] = '';
			$result['nom_complet'] = '';
		} elseif ($_prenom == '' and $_nom == '' and $_nom_complet != '') {
			// On n'a que le nom complet. On prend le premier morceau pour le prénom
			$parties = explode(" ", $_nom_complet);
			if (count($parties) == 1) {
				$result['prenom'] = '';
				$result['nom'] = $parties[0];
				$result['nom_complet'] = $parties[0];
			} else {
				$result['prenom'] = $parties[0];
				$result['nom'] = trim(str_replace($result['prenom'], "", $_nom_complet));
				$result['nom_complet'] = $_nom_complet;
			}
		} elseif ($_prenom == '' and $_nom != '' and $_nom_complet != '') {
			$result['prenom'] = trim(str_replace($_nom, "", $_nom_complet));
			$result['nom'] = $_nom;
			$result['nom_complet'] = $_nom_complet;
		} elseif ($_prenom != '' and $_nom == '' and $_nom_complet != '') {
			$result['prenom'] = $_prenom;
			$result['nom'] = trim(str_replace($_prenom, "", $_nom_complet));
			$result['nom_complet'] = $_nom_complet;
		} elseif ($_prenom != '' and $_nom != '' and $_nom_complet == '') {
			$result['prenom'] = $_prenom;
			$result['nom'] = $_nom;
			$result['nom_complet'] = $_prenom." ".$_nom;
		} elseif ($_prenom != '' and $_nom != '' and $_nom_complet != '') {
			$result['prenom'] = $_prenom;
			$result['nom'] = $_nom;
			$result['nom_complet'] = $_nom_complet;
		}
		return $result;
	}

	# Cette méthode formatte des données utilisateurs au format accepté par ldap_add ou ldap_modify.
	# Les paramètres vides sont ignorés.
	private function format_user_data($_login, $_nom, $_prenom, $_email, $_civilite, $_password, $_statut) {

		$data = array();
		$data['objectClass'] = $this->people_object_classes;
		$data[$this->champ_login] = $_login;

		// Les nom, prénom, nom complet
		if ($_prenom != '' and $_nom != '') {
			$nom = $this->format_name($_prenom, $_nom, '');
			if ($this->champ_prenom != '') {
				$data[$this->champ_prenom] = $nom['prenom'];
			}
			if ($this->champ_nom != '') {
				$data[$this->champ_nom] = $nom['nom'];
			}
			if ($this->champ_nom_complet != '') {
				$data[$this->champ_nom_complet] = $nom['nom_complet'];
			}
		}

		// L'email
		if ($_email != '' and $this->champ_email != '') {
			$data[$this->champ_email] = $_email;
		}

		// La civilité
		if ($_civilite != '' and $this->champ_civilite != '') {
		    switch ($_civilite) {
        		case "Mme":
        			$data[$this->champ_civilite] = $this->code_civilite_madame;
        		break;
        		case "Mlle":
        			$data[$this->champ_civilite] = $this->code_civilite_mademoiselle;
        		break;
        		case "M.":
        			$data[$this->champ_civilite] = $this->code_civilite_monsieur;
        		break;
        		default:
        			$data[$this->champ_civilite] = $this->code_civilite_madame;
        		break;
        	}
		}

		// Le mot de passe
		if ($_password != '') {
			$data['userPassword'] = $this->encode_password($_password);
		}

		// Le statut
		if ($_statut != '' && $this->champ_statut != '') {
			$data[$this->champ_statut] = $_statut;
		}

		return $data;
	}

}
?>
