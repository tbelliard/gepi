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

if(getSettingValue('acces_archives_cdt')=="") {
	$acces="y";
}
elseif(getSettingAOui('acces_archives_cdt')) {
	$acces="y";
}
else {
	header("Location: $pref_arbo_include/accueil.php?msg=Accès aux archives CDT non autorisé.");
	die();
}

if((isset($chaine_login_prof))&&($chaine_login_prof!='')) {
	//$tab_login=array(my_strtolower($chaine_login_prof));

	$tab_login=array();
	$tmp_tab=explode(",", my_strtolower(preg_replace("/ /", "", $chaine_login_prof)));
	for($loop=0;$loop<count($tmp_tab);$loop++) {
		$tab_login[]=preg_replace("/^'/", "", preg_replace("/'$/", "", $tmp_tab[$loop]));
	}
	/*
	echo "<pre>";
	print_r($tab_login);
	echo "</pre>";
	*/
	//if(($_SESSION["statut"]=="professeur")&&(!in_array("'".my_strtolower($_SESSION["login"])."'",$tab_login))) {
	if(($_SESSION["statut"]=="professeur")&&(!in_array(my_strtolower($_SESSION["login"]),$tab_login))) {
		if(getSettingValue('debug_acces_archives_cdt')=='y') {
			echo "<p style='color:red'>Le login ".$_SESSION["login"]." n'est pas dans la liste autorisée ".$chaine_login_prof."</p>\n";
		}
		else {
			header("Location: $pref_arbo_include/logout.php?auto=1");
		}
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

if(isset($_POST['archive_selection_notices_textarea'])) {
	//$tempdir=get_user_temp_directory($_SESSION['login']);

	$sql="SELECT temp_dir FROM utilisateurs WHERE login='".$_SESSION['login']."'";
	$res=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($res)==0) {
		$msg="<p style='color:red; text-align:center;'>Dossier temporaire non trouvé.</p>";
	}
	else {
		$lig=mysqli_fetch_object($res);
		$tempdir=$lig->temp_dir;

		if(file_exists($pref_arbo_include."/temp/".$tempdir)) {
			$f=fopen($pref_arbo_include."/temp/".$tempdir."/cdt_selection.txt", "w+");
			fwrite($f, stripslashes($_POST['archive_selection_notices_textarea']));
			fclose($f);
			$msg="<p style='color:green; text-align:center;'>Sélection enregistrée&nbsp;: <a href='".$pref_arbo_include."/temp/".$tempdir."/cdt_selection.txt"."' target='_blank'>TXT</a><br />Vous pourrez l'insérer dans le cahier de textes en cliquant sur l'icone <img src='".$pref_arbo_include."/images/icons/paste_A.png' class='icone16' alt='Coller' /> de la page de saisie de CDT2.</p>";
		}
		else {
			$msg="<p style='color:red; text-align:center;'>Dossier temporaire non trouvé.</p>";
		}
	}
}
?>
