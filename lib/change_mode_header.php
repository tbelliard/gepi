<?php
	require_once("../lib/initialisations.inc.php");

	// Resume session
	$resultat_session = $session_gepi->security_check();
	if ($resultat_session == 'c') {
		header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
		die();
	} else if ($resultat_session == '0') {
		header("Location: ../logout.php?auto=1");
		die();
	}

	// Sécurité
/*
	if (!checkAccess()) {
		header("Location: ../logout.php?auto=2");
		die();
	}
*/
	// La SESSION est valide, on teste juste les bricolages d'URL
	$url = parse_url($_SERVER['REQUEST_URI']);
	if (mb_substr($url['path'], 0, mb_strlen($gepiPath)) != $gepiPath) {
		tentative_intrusion(2, "Tentative d'accès avec modification sauvage de gepiPath");
		header("Location: ../logout.php?auto=2");
		die();
	}

	// On récupère le mode pour le Header (normalement en POST):
	$cacher_header = isset($_POST['cacher_header']) ? $_POST['cacher_header'] : (isset($_GET['cacher_header']) ? $_GET['cacher_header'] : "n");

	// On n'accepte que deux valeurs:
	if(($cacher_header!="y")&&($cacher_header!="n")){$cacher_header="n";}

	// On enregistre le mode pour le Header:
	$_SESSION['cacher_header'] = $cacher_header;

	// Et on renvoie un message http valide
	header("HTTP/1.0 200 OK");
	echo ' ';
?>
