<?php
# Une fois renseigné, pensez à renommer ce fichier connect-modele.inc.php
# en connect.inc.php dans le repertoire secure
#
# ============
# Premier cas : vous êtes en configuration mono-site
# (une installation de Gepi / un établissement)
#
# 1- Laissez la variable $multisite à "n",
$multisite = "n";
#
# 2- Renseignez les cinq variables suivantes
# ($dbHost, $dbDb, $dbUser, $dbPass, et $gepiPath)
# selon votre configuration
#
# Le nom du serveur qui héberge votre base mysql.
# (si c'est le même que celui qui héberge les scripts php, mettre "localhost")
$dbHost="localhost";
# Port mySQL sur le serveur; c'est généralement le 3306
//$dbPort=ini_get("mysqli.default_port");
# Le nom de votre base mysql
$dbDb="gepi";
# Le nom de l'utilisateur mysql qui a les droits sur la base
$dbUser="gepi";
# Le mot de passe de l'utilisateur mysql ci-dessus
$dbPass="gepi";
# Chemin relatif vers GEPI
$gepiPath="/gepi";

/**
 * Connexion permanente à la base
 * 
 * Décommenter la ligne et remplacer "false" par "true" pour activer les connexions non permanentes
 *
 * @global int $GLOBALS['db_nopersist']
 * @name $db_nopersist
 */
//$GLOBALS['db_nopersist']=false;


/* Base de l'URL (sans le chemin relatif défini ci-dessus)
 * Cette variable est utile dans le cas de l'installation derrière un reverse proxy,
 * ce qui peut induire en erreur les mécanismes de détection automatique
 * de l'adresse. Si cette variable n'est pas défini, les mécanismes automatiques
 * seront utilisés.
 */
#$gepiBaseUrl = 'https://mongepi.fr'

# ============
# Deuxième cas : vous êtes en configuration multi-site
# (une installation de Gepi / plusieurs établissements)
#
# 1- Passez la variable $multisite à "y",
#    Remplacez "n" par "y" dans la ligne [$multisite = "n";]
#    située au 1- du premier cas ci-dessus
#    ou dé-commentez -retirez le "# " en début de ligne- la ligne ci-dessous
#$multisite = "y";
#
# 2- Renseignez le fichier /secure/multisite.ini comme indiqué
#
# 3- Modifiez la valeur "multisite" de la table "settings"
# en passant (via phpmyadmin par ex.) la commande sql suivante :
# UPDATE `nombase`.`setting` SET `VALUE` = 'y' WHERE NAME = 'multisite' LIMIT 1 ;
# décommentez toute la portion de code suivante pour le multisite
/*
if ($multisite == "y" AND $_SERVER["SCRIPT_NAME"] != "/login.php") {
	$RNE = isset($_REQUEST['rne']) ? $_REQUEST['rne'] : (isset($_REQUEST['RNE']) ? $_REQUEST['RNE'] : (isset($_REQUEST['organization']) ? $_REQUEST['organization'] : (isset($_COOKIE['RNE']) ? $_COOKIE['RNE'] : NULL)));
	if (!isset($RNE) || $RNE == 'RNE') {
		echo 'Erreur : Numero d\'etablissement manquant dans la requete (parametre rne ou organization).'; die();
	} else {
		setcookie('RNE', $RNE, null, '/');
		$_COOKIE['RNE'] = $RNE;
		$init = parse_ini_file(dirname(__FILE__)."/../secure/multisite.ini.php", TRUE);
		if (	!isset($init[$RNE]["nomhote"]) ||
			!isset($init[$RNE]["nombase"]) ||
			!isset($init[$RNE]["mysqluser"]) ||
			!isset($init[$RNE]["mysqlmdp"]) ||
			!isset($init[$RNE]["pathname"])
			) {
			echo 'Erreur : Numero d\'etablissement '.$RNE.' non trouve dans la configuration'; die();
		}
		$dbHost		= $init[$RNE]["nomhote"];
		$dbDb		= $init[$RNE]["nombase"];
		$dbUser		= $init[$RNE]["mysqluser"];
		$dbPass		= $init[$RNE]["mysqlmdp"];
		$gepiPath	= $init[$RNE]["pathname"];
	}
}
 */
#

$mode_debug = false;
$debug_log_file = '/var/log/gepi.log';
?>
