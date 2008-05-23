<?php
$niveau_arbo = 1;
	// Initialisations files
	require_once("../lib/initialisations.inc.php");
		// Resume session
	$resultat_session = resumeSession();
	if ($resultat_session == 'c') {
		header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
		die();
	} else if ($resultat_session == '0') {
		header("Location: ../logout.php?auto=1");
		die();
	};

include("./utilisateurs.class.php");

$utilisateur = new user($_SESSION["login"]);

echo $utilisateur->vraiStatut();

echo 'fraise';
?>