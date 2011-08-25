<?php
/**
 * Appel des fichiers de configuration
 * 
 * @license GNU/GPL, 
 * @package Initialisation
 * @subpackage initialisation
 */

/**
 * Niveau de la page dans l'arborescence de GEPI
 *
 * @global int $GLOBALS['niveau_arbo']
 * @name $niveau_arbo
 */
$GLOBALS['niveau_arbo']=$niveau_arbo;


/**
 * Chemin de la racine de GEPI
 * 
 * initialisée dans secure/connect.inc.php
 * 
 * On la retrouve en tant que global dans share.inc.php
 *
 * @global string $GLOBALS['gepiPath']
 * @name $gepiPath
 */
$GLOBALS['gepiPath']=$gepiPath;

/**
 * Chemin relatif vers la racine de GEPI
 *
 * @global string $GLOBALS['relatif_gepi']
 * @name $chemin_relatif_gepi
 */
$GLOBALS['relatif_gepi']=NULL;

/**
 * y si on est en multisite, n sinon
 * 
 * @global string $GLOBALS['multisite']
 * @name $multisite
 */
$GLOBALS['multisite'] = $multisite;

/**
 * Version de GEPI stable
 * 
 * @global mixed $GLOBALS['gepiVersion']
 * @name $gepiVersion
 */
$GLOBALS['gepiVersion'] = NULL;

/**
 * Version de GEPI release candidate
 * 
 * @global mixed $GLOBALS['gepiRcVersion']
 * @name $gepiRcVersion
 */
$GLOBALS['gepiRcVersion'] = NULL;

/**
 * Version de GEPI Beta
 * 
 * @global mixed $GLOBALS['gepiBetaVersion']
 * @name $gepiBetaVersion
 */
$GLOBALS['gepiBetaVersion'] = NULL;

/**
 * Les informations du groupes obtenues à partir de get_group()
 * 
 * @global array $GLOBALS['current_group']
 * @name $current_group
 * @see get_group()
 */
$GLOBALS['current_group'] = NULL;

/**
 * @global array $GLOBALS['tab_conteneurs_enfants'] 
 * @name $tab_conteneurs_enfants
 */
$GLOBALS['tab_conteneurs_enfants'] = NULL;

/**
 * @global int $GLOBALS['id_groupe'] 
 * @name $id_groupe
 */
$GLOBALS['id_groupe']  = NULL;

/**
 * 
 * 
 * @global string $GLOBALS['active_hostbyaddr']
 * @name  $active_hostbyaddr
 */
$GLOBALS['active_hostbyaddr'] = NULL;

/**
 * Affichage des statistiques de la classe sur les bulletins si à 1
 * 
 * @global int $GLOBALS['min_max_moyclas']
 * @name $min_max_moyclas
 */
$GLOBALS['min_max_moyclas']=NULL;

/**
 * Effectif du groupe
 * 
 * @global int $GLOBALS['eff_groupe']
 * @name $eff_groupe
 */
$GLOBALS['eff_groupe']=NULL;

/**
 * Tableau contenant les informations pour afficher une infobulle
 * 
 * @global array $GLOBALS['tabdiv_infobulle']
 * @name $tabdiv_infobulle
 */
$GLOBALS['tabdiv_infobulle']=NULL;

/**
 * Texte à afficher quand une période est close
 * 
 * @global string $GLOBALS['gepiClosedPeriodLabel']
 * @name $gepiClosedPeriodLabel
 */
$GLOBALS['gepiClosedPeriodLabel']=NULL;

/**
 * 
 * @global int $GLOBALS['totalsize']
 * @name $totalsize
 */
$GLOBALS['gepiClosedPeriodLabel']=NULL;




// Initialisation de variables utilisées si javascript activé
$tabdiv_infobulle=array();
$tabid_infobulle=array();

// Initialisation des chemins relatifs
// $chemin_relatif_gepi2 utilisé avec secure/connect.inc.php
// $chemin_relatif_gepi pour les autres fichiers
$chemin_relatif_gepi = $chemin_relatif_gepi2 = dirname(dirname(__FILE__));

if (isset($niveau_arbo) and ($niveau_arbo == "public")) {
  $chemin_relatif_gepi2 = './';
}


$is_lcs_plugin="no";

if (file_exists($chemin_relatif_gepi."/secure/config_lcs.inc.php")) {
    $is_lcs_plugin="yes";
}

if($is_lcs_plugin=='yes') {
  /**
   * authentification lcs
   */
  require_once ('/usr/share/lcs/Plugins/Gepi/secure/config_lcs.inc.php');
  /**
   * authentification lcs page auth
   */
  include LCS_PAGE_AUTH_INC_PHP;
  /**
   * authentification lcs page LDAP
   */
  include LCS_PAGE_LDAP_INC_PHP;
  list ($idpers,$login) = isauth();

}

$version = substr(phpversion(), 0, 1);
if ($version == 4) {
  $ldap_class = "/lib/LDAPServer.php4.class.php";
  $session_class = "/lib/Session.php4.class.php";
} else {
  $ldap_class = "/lib/LDAPServer.class.php";
  $session_class = "/lib/Session.class.php";
}

// Pour le multisite
if (isset($_REQUEST['rne'])) {
	setcookie('RNE', $_REQUEST['rne'], null, '/');
} elseif (isset($_REQUEST['RNE'])) {
	setcookie('RNE', $_REQUEST['RNE'], null, '/');
}
// Pour le choix de la préférence de source d'authentification pour l'authentification multiauth
if (isset($_REQUEST["source"])) {
	setcookie('source', $_REQUEST["source"], null, '/');
}

/**
 * Données de connexion à la base
 */
   require_once($chemin_relatif_gepi2."/secure/connect.inc.php");
/**
 * Connection à la base
 */
   require_once($chemin_relatif_gepi."/lib/mysql.inc");
 /**
  * Ajout pour utiliser ou pas les fonctions mb_
  */
   require_once($chemin_relatif_gepi."/lib/mb_ou_pas.php");
 /**
  * Fichier de configuration générale
  */
   require_once($chemin_relatif_gepi."/lib/global.inc.php");
 /**
  * Filtrage html
  */
   require_once($chemin_relatif_gepi."/lib/filtrage_html.inc.php");
	if($filtrage_html=="htmlpurifier") {
 /**
  * Utilisation de HTMLPurifier.standalone pour filtrer les saisies
  */
		require_once($chemin_relatif_gepi."/lib/HTMLPurifier.standalone.php");
	}
	elseif($filtrage_html=="inputfilter") {
 /**
  * Utilisation de class.inputfilter_clean.php pour filtrer les saisies
  */
		require_once($chemin_relatif_gepi."/lib/class.inputfilter_clean.php");
	}

 /**
  * Traitement des données
  */
   require_once($chemin_relatif_gepi."/lib/traitement_data.inc.php");
 /**
  * Librairies
  * 
  * @see share.inc.php
  */
   include $chemin_relatif_gepi."/lib/share.inc.php";
 /**
  * Fonctions relatives aux groupes
  * 
  * @see groupes.inc.php
  */
    include $chemin_relatif_gepi."/lib/groupes.inc.php";
 /**
  * classes
  */
    include $chemin_relatif_gepi."/lib/classes.inc.php";
 /**
  * Fonctions de manipulation de la table settings
  * 
  * @see settings.inc
  * @see loadSettings()
  */
   require_once($chemin_relatif_gepi."/lib/settings.inc");
   // Load settings
   if (!loadSettings()) {
     die("Erreur chargement settings");
   }
   /**
    * Fonctions relatives à l'identification via LDAP
    */
   require_once($chemin_relatif_gepi.$ldap_class);
   /**
    * Fonctions relatives à la session
    * 
    * @see class_exists()
    * @see get_include_path()
    */
   require_once($chemin_relatif_gepi.$session_class);

// Modif pour la longueur des logins par $longmax_login du global.inc.php
// Si le champ de setting existe alors il faut l'utiliser car il est réglé par la page param_gen.php
if(isset($gepiSettings['longmax_login'])){
    $longmax_login = $gepiSettings['longmax_login'];
}

if (!isset($mode_debug)) {
    $mode_debug = false;
}


// Initialisation de la session Gepi :
if (!isset($prevent_session_init)) {
  $session_gepi = new Session();
}

if (!class_exists('Propel')
	|| !strstr(get_include_path(), '/orm/propel-build/classes')) {
    //on retire les objets propel de la session car propel n'a pas ete initialise,
    //donc les objets ne seront pas correctement deserialiser
    if (isset($_SESSION['objets_propel'])) unset($_SESSION['objets_propel']);
}

?>