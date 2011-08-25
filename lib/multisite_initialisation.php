<?php

//recherche des parametres de la base dans le multisite.ini.php
//réglage du cookie

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
			echo 'Erreur : Numero d\'etablissement '.$RNE.' non trouve dans la configuration'; die;
		}
		$dbHost		= $init[$RNE]["nomhote"];
		$dbDb		= $init[$RNE]["nombase"];
		$dbUser		= $init[$RNE]["mysqluser"];
		$dbPass		= $init[$RNE]["mysqlmdp"];
		$gepiPath	= $init[$RNE]["pathname"];
	}
}
?>
