<?php

//$niveau_arbo=5;

if($niveau_arbo=="4") {
	$pref_arbo_include="../../../..";
}
elseif($niveau_arbo=="5") {
	$pref_arbo_include="../../../../..";
}
elseif($niveau_arbo=="6") {
	$pref_arbo_include="../../../../../..";
}

require_once("$pref_arbo_include/lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
	header("Location: $pref_arbo_include/utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == "0") {
	header("Location: $pref_arbo_include/logout.php?auto=1");
	die();
}

$tab_statuts=array("administrateur","professeur","scolarite","cpe","autre");
if(!in_array($_SESSION["statut"],$tab_statuts)) {
	header("Location: $pref_arbo_include/logout.php?auto=1");
	die();
}

if((isset($chaine_login_prof))&&($chaine_login_prof!='')) {
	$tab_login=array($chaine_login_prof);
	if(($_SESSION["statut"]=="professeur")&&(!in_array("'".$_SESSION["login"]."'",$tab_login))) {
		header("Location: $pref_arbo_include/logout.php?auto=1");
		die();
	}
}

//========================================
// Ajouter un test pour l'accès Inspecteur

//========================================

// Contrôler en multisite que l'on a bien documents/archives/$_COOKIE['RNE']/ dans le chemin
// et documents/archives/etablissement/ sinon
//if(getSettingValue('multisite')=='y') {
if(((isset($multisite))&&($multisite=='y'))||(getSettingValue('multisite')=='y')) {
	if(!preg_match('|documents/archives/'.$_COOKIE['RNE'].'/|',$_SERVER['SCRIPT_FILENAME'])) {

		// A REVOIR: LES $msg NE SONT PAS PRIS EN COMPTE DANS logout.php

		header("Location: $pref_arbo_include/logout.php?auto=1&msg=".rawurlencode("Tentative d'accès non autorisé à un autre établissement"));
		die();
	}
}
else {
	if(!preg_match('|documents/archives/etablissement/|',$_SERVER['SCRIPT_FILENAME'])) {
		header("Location: $pref_arbo_include/logout.php?auto=1&msg=".rawurlencode("Tentative d'accès non autorisé à un autre établissement"));
		die();
	}
}

// Corriger par la suite les liens de retour Professeur dans l'archivage et mettre avant l'appel entete.php: $liens_retour_ok2="y";
//if(!isset($liens_retour_ok)) {
if(!isset($liens_retour_ok2)) {
	echo "<script type='text/javascript'>

function cacher_div_lien_retour() {
	t=document.title;
	if(t.substring(0,16)=='CDT: Professeur ') {
		if(document.getElementById('div_lien_retour')) {
			document.getElementById('div_lien_retour').style.display='none';
		}
	}
}
//setTimeout(\"cacher_div_lien_retour()\",1000);


function corriger_div_lien_retour() {
	t=document.title;
	if(t.substring(0,16)=='CDT: Professeur ') {
		if(document.getElementById('div_lien_retour')) {
			document.getElementById('div_lien_retour').innerHTML='<a href=\'$gepiPath/documents/archives/index.php\'>Retour</a>';
		}
	}
}
setTimeout(\"corriger_div_lien_retour()\",1000);

</script>\n";
}
?>