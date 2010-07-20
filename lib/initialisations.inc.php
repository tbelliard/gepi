<?php

//======================================
/*
$utiliser_mb="y";
if($utiliser_mb=="y") {
	mb_internal_encoding("iso-8859-15");
}
*/
//======================================

$is_lcs_plugin="no";
// Pour les scripts situés à la racine de GEPI
if (isset($niveau_arbo) and ($niveau_arbo == "0")) {
	if (file_exists("./secure/config_lcs.inc.php")) {
		$is_lcs_plugin="yes";
	}
}
// Pour les scripts situés dans un sous-répertoire à l'intérieur d'une sous-répertoire de GEPI
else if (isset($niveau_arbo) and ($niveau_arbo == "2")) {
	if (file_exists("../../secure/config_lcs.inc.php")) {
		$is_lcs_plugin="yes";
	}
}
// Pour les scripts situés dans un sous-sous-répertoire à l'intérieur d'une sous-répertoire de GEPI
else if (isset($niveau_arbo) and ($niveau_arbo == "3")) {
	if (file_exists("../../../secure/config_lcs.inc.php")) {
		$is_lcs_plugin="yes";
	}
}
else {
	if (file_exists("../secure/config_lcs.inc.php")) {
		$is_lcs_plugin="yes";
	}
}

if($is_lcs_plugin=='yes') {
	//authentification lcs
	//require_once ('/usr/share/lcs/Plugins/Gepi/secure/config_lcs.cfg');
	require_once ('/usr/share/lcs/Plugins/Gepi/secure/config_lcs.inc.php');
	include LCS_PAGE_AUTH_INC_PHP;
	include LCS_PAGE_LDAP_INC_PHP;
	list ($idpers,$login) = isauth();
	/*
	//on se reconnecte à la base gepi
	if (empty($db_nopersist))
		$db_c = mysql_pconnect($dbHost, $dbUser, $dbPass);
	else
		$db_c = mysql_connect($dbHost, $dbUser, $dbPass);

	if (!$db_c || !mysql_select_db ($dbDb))
	{
		echo "\n<p>Erreur grave: Echec de la connexion à la base de données";
		exit;
	}

	// Ce n'est plus utile. On realise cette operation plus loin lors de l'include connect.inc.php
	*/
}

$version = substr(phpversion(), 0, 1);
if ($version == 4) {
  $ldap_class = "lib/LDAPServer.php4.class.php";
  $session_class = "lib/Session.php4.class.php";
} else {
  $ldap_class = "lib/LDAPServer.class.php";
  $session_class = "lib/Session.class.php";
}

// Pour les scripts situés à la racine de GEPI
if (isset($niveau_arbo) and ($niveau_arbo == "0")) {
   // Database configuration file
   require_once("./secure/connect.inc.php");
   // Database connection
   require_once("./lib/mysql.inc");
   // Ajout pour utiliser ou pas les fonctions mb_*
   require_once("./lib/mb_ou_pas.php");
   // Global configuration file
   require_once("./lib/global.inc");
   // Traitement des données
   require_once("./lib/filtrage_html.inc.php");
	if($filtrage_html=="htmlpurifier") {
		require_once("./lib/HTMLPurifier.standalone.php");
	}
	elseif($filtrage_html=="inputfilter") {
		require_once("./lib/class.inputfilter_clean.php");
	}

   require_once("./lib/traitement_data.inc.php");
   // Libraries
   include "./lib/share.inc.php";
   // Fonctions relatives aux groupes
    include "./lib/groupes.inc.php";
   // Fonctions relatives aux groupes
    include "./lib/classes.inc.php";
   // Settings
   require_once("./lib/settings.inc");
   // Load settings
   if (!loadSettings()) {
     die("Erreur chargement settings");
   }
   // Session related functions
   require_once("./".$ldap_class);
   require_once("./".$session_class);

// Pour les scripts situés dans un sous-répertoire à l'intérieur d'une sous-répertoire de GEPI
} else if (isset($niveau_arbo) and ($niveau_arbo == "2")) {
   // Database configuration file
   require_once("../../secure/connect.inc.php");
   // Database connection
   require_once("../../lib/mysql.inc");
   // Ajout pour utiliser ou pas les fonctions mb_*
   require_once("../../lib/mb_ou_pas.php");
   // Global configuration file
   require_once("../../lib/global.inc");
   // Traitement des données
   require_once("../../lib/filtrage_html.inc.php");
	if($filtrage_html=="htmlpurifier") {
		require_once("../../lib/HTMLPurifier.standalone.php");
	}
	elseif($filtrage_html=="inputfilter") {
		require_once("../../lib/class.inputfilter_clean.php");
	}
   require_once("../../lib/traitement_data.inc.php");
   // Libraries
   include "../../lib/share.inc.php";
   // Fonctions relatives aux groupes
   include "../../lib/groupes.inc.php";
   // Fonctions relatives aux groupes
   include "../../lib/classes.inc.php";
   // Settings
   require_once("../../lib/settings.inc");
   // Load settings
   if (!loadSettings()) {
       die("Erreur chargement settings");
   }
   // Session related functions
   require_once("../../".$ldap_class);
   require_once("../../".$session_class);

// Pour les scripts situés dans un sous-sous-répertoire à l'intérieur d'une sous-répertoire de GEPI
} else if (isset($niveau_arbo) and ($niveau_arbo == "3")) {
   // Database configuration file
   require_once("../../../secure/connect.inc.php");
   // Database connection
   require_once("../../../lib/mysql.inc");
   // Ajout pour utiliser ou pas les fonctions mb_*
   require_once("../../../lib/mb_ou_pas.php");
   // Global configuration file
   require_once("../../../lib/global.inc");
   // Traitement des données
   require_once("../../../lib/filtrage_html.inc.php");
	if($filtrage_html=="htmlpurifier") {
		require_once("../../../lib/HTMLPurifier.standalone.php");
	}
	elseif($filtrage_html=="inputfilter") {
		require_once("../../../lib/class.inputfilter_clean.php");
	}
   require_once("../../../lib/traitement_data.inc.php");
   // Libraries
   include "../../../lib/share.inc.php";
   // Fonctions relatives aux groupes
   include "../../../lib/groupes.inc.php";
   // Fonctions relatives aux groupes
   include "../../../lib/classes.inc.php";
   // Settings
   require_once("../../../lib/settings.inc");
   // Load settings
   if (!loadSettings()) {
       die("Erreur chargement settings");
   }
   // Session related functions
   require_once("../../../".$ldap_class);
   require_once("../../../".$session_class);

// Pour les scripts situés dans le sous-répertoire "public"
// Ces scripts font appel au fichier /public/secure/connect.inc et non pas /secure/connect.inc
} else if (isset($niveau_arbo) and ($niveau_arbo == "public")) {
   // Database configuration file
    require_once("./secure/connect.inc.php");
    // Database
    require_once("../lib/mysql.inc");
   // Ajout pour utiliser ou pas les fonctions mb_*
   require_once("../lib/mb_ou_pas.php");
    // Global configuration file
    require_once("../lib/global.inc");
    // Traitement des données
   require_once("../lib/filtrage_html.inc.php");
	if($filtrage_html=="htmlpurifier") {
		require_once("../lib/HTMLPurifier.standalone.php");
	}
	elseif($filtrage_html=="inputfilter") {
		require_once("../lib/class.inputfilter_clean.php");
	}
    require_once("../lib/traitement_data.inc.php");
    // Libraries
    include "../lib/share.inc.php";
    // Fonctions relatives aux groupes
    include "../lib/groupes.inc.php";
    // Settings
    require_once("../lib/settings.inc");
    // Load settings
    if (!loadSettings()) {
        die("Erreur chargement settings");
    }
   // Session related functions
   require_once("../".$ldap_class);
   require_once("../".$session_class);

// Pour les scripts situés dans un sous-répertoire GEPI
} else {
   // Database configuration file
   require_once("../secure/connect.inc.php");
   // Database connection
   require_once("../lib/mysql.inc");
   // Ajout pour utiliser ou pas les fonctions mb_*
   require_once("../lib/mb_ou_pas.php");
   // Global configuration file
   require_once("../lib/global.inc");
   // Traitement des données
   require_once("../lib/filtrage_html.inc.php");
	if($filtrage_html=="htmlpurifier") {
		require_once("../lib/HTMLPurifier.standalone.php");
	}
	elseif($filtrage_html=="inputfilter") {
		require_once("../lib/class.inputfilter_clean.php");
	}
   require_once("../lib/traitement_data.inc.php");
   // Libraries
   include "../lib/share.inc.php";
    // Fonctions relatives aux groupes
    include "../lib/groupes.inc.php";
     // Fonctions relatives aux groupes
    include "../lib/classes.inc.php";
   // Settings
   require_once("../lib/settings.inc");
   // Load settings
   if (!loadSettings()) {
       die("Erreur chargement settings");
   }
   // Session related functions
   require_once("../".$ldap_class);
   require_once("../".$session_class);
}

	// Modif pour la longueur des logins par $longmax_login du global.inc
	// Si le champ de setting existe alors il faut l'utiliser car il est réglé par la page param_gen.php
	if(isset($gepiSettings['longmax_login'])){
		$longmax_login = $gepiSettings['longmax_login'];
	}

if (!isset($mode_debug)) {
    $mode_debug = false;
}

// Initialisaton de la session Gepi :
$session_gepi = new Session();

if (!class_exists('Propel')
	|| !strstr(get_include_path(), '/orm/propel-build/classes')) {
    //on retire les objets propel de la session car propel n'a pas ete initialise,
    //donc les objets ne seront pas correctement deserialiser
    unset($_SESSION['objets_propel']);
}


?>