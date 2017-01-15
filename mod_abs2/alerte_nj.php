<?php
/*
*
* Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence") != '2') {
	die("Le module n'est pas activé.");
}

if ($utilisateur->getStatut() != "cpe" && $utilisateur->getStatut() != "scolarite" && $utilisateur->getStatut() != "professeur" && $utilisateur->getStatut() != "autre" ) {
	die("acces interdit");
}
if($utilisateur->getStatut() == "professeur" && $utilisateur->getClasses()->isEmpty()){
	die("acces interdit");
}

//debug_var();

//====================================
// Valeurs par défaut
$abs2_afficher_alerte_nb_nj=getSettingValue("abs2_afficher_alerte_nb_nj");
if($abs2_afficher_alerte_nb_nj=="") {
	$abs2_afficher_alerte_nb_nj=4;
}

$abs2_afficher_alerte_nj_delai=getSettingValue("abs2_afficher_alerte_nj_delai");
if($abs2_afficher_alerte_nj_delai=="") {
	$abs2_afficher_alerte_nj_delai=30;
}

// Valeurs transmises:
$nb_nj=isset($_POST['nb_nj']) ? $_POST['nb_nj'] : "";
$nj_delai=isset($_POST['nj_delai']) ? $_POST['nj_delai'] : "";


if(($nb_nj=="")||(!preg_match("/^[0-9]{1,}$/", $nb_nj))||($nb_nj<1)) {
	$nb_nj=$abs2_afficher_alerte_nb_nj;
}

if(($nj_delai=="")||(!preg_match("/^[0-9]{1,}$/", $nj_delai))||($nj_delai<1)) {
	$nj_delai=$abs2_afficher_alerte_nj_delai;
}
//====================================
// Valeurs par défaut
$abs2_afficher_alerte_nb_abs=getSettingValue("abs2_afficher_alerte_nb_abs");
if($abs2_afficher_alerte_nb_abs=="") {
	$abs2_afficher_alerte_nb_abs=4;
}

$abs2_afficher_alerte_abs_delai=getSettingValue("abs2_afficher_alerte_abs_delai");
if($abs2_afficher_alerte_abs_delai=="") {
	$abs2_afficher_alerte_abs_delai=30;
}

// Valeurs transmises:
$nb_abs=isset($_POST['nb_abs']) ? $_POST['nb_abs'] : "";
$abs_delai=isset($_POST['abs_delai']) ? $_POST['abs_delai'] : "";


if(($nb_abs=="")||(!preg_match("/^[0-9]{1,}$/", $nb_abs))||($nb_abs<1)) {
	$nb_abs=$abs2_afficher_alerte_nb_abs;
}

if(($abs_delai=="")||(!preg_match("/^[0-9]{1,}$/", $abs_delai))||($abs_delai<1)) {
	$abs_delai=$abs2_afficher_alerte_abs_delai;
}
//====================================

$periode_courante_seulement=isset($_POST['periode_courante_seulement']) ? $_POST['periode_courante_seulement'] : "y";

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : "");

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";
//**************** EN-TETE *****************
$titre_page = "Les absences";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

if($mode=="abs") {

	echo "<p class='bold'>
	<a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='".$_SERVER['PHP_SELF']."'>Absences non justifiées</a>
</p>

<h2>Absences justifiées ou non</h2>

	<p>Cette page est destinée à rappeler les absences/manquements justifiés ou non.<br />";

	if($periode_courante_seulement=="y") {
		$checked_periode_courante_seulement_y=" checked";
		$checked_periode_courante_seulement_n="";
	}
	else {
		$checked_periode_courante_seulement_y="";
		$checked_periode_courante_seulement_n=" checked";
	}
	if(getSettingAOui("abs2_afficher_alerte_abs")) {
		echo "
	L'affichage en page d'accueil est paramétré par défaut pour les comptes CPE.<br />";
	}
	else {
		echo "
	L'affichage en page d'accueil n'est pas activé pour les comptes CPE.<br />";
	}
	echo "
	Les valeurs par défaut sont les suivantes&nbsp;:<br />
	Lorsque plus de ".$abs2_afficher_alerte_nb_abs." manquement(s) ont été relevés alors qu'il s'est écoulé au moins ".$abs2_afficher_alerte_abs_delai." jour(s) depuis le manquement, un affichage est réalisé.<br />
	Par défaut, seuls les manquements de la période courante sont affichés.<br />
	Les paramètres par défaut sont définis par l'administrateur dans le Paramétrage du module Absences 2.<br />
	Vous pouvez modifier dans le formulaire ci-dessous les critères pour un affichage ponctuel.</p>
	<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" name='form1' method=\"post\">
		<fieldset class='fieldset_opacite50''>
			<input type='hidden' name='mode' value='$mode' />
			<p>
				<label for='nb_abs'>Nombre de manquements&nbsp;: </label><input type='text' name='nb_abs' id='nb_abs' onkeydown='clavier_2(this.id,event,1,50);' size='2' value='$nb_abs' /><br />
				<label for='abs_delai'>Manquements remontant à au moins &nbsp;: </label><input type='text' name='abs_delai' id='abs_delai' onkeydown='clavier_2(this.id,event,1,300);' size='2' value='$abs_delai' /> jour(s).<br />
				Afficher les manquements<br />
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='periode_courante_seulement' id='periode_courante_seulement_y' value='y'".$checked_periode_courante_seulement_y." /><label for='periode_courante_seulement_y'> de la période courante seulement</label><br />
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='periode_courante_seulement' id='periode_courante_seulement_n' value='n'".$checked_periode_courante_seulement_n." /><label for='periode_courante_seulement_n'> sur toute l'année</label><br />
				<input type='submit' value='Afficher' />
			</p>
		</fieldset>
	</form>";

	$lignes_alerte_abs2_abs=abs2_afficher_tab_alerte_abs($nb_abs, $abs_delai, $periode_courante_seulement);
	if($lignes_alerte_abs2_abs!="") {
		echo $lignes_alerte_abs2_abs;
	}
	else {
		echo "<p style='color:red'>Aucun manquement n'est relevé avec les critères choisis.</p>";
	}
}
else {
	echo "<p class='bold'>
	<a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='".$_SERVER['PHP_SELF']."?mode=abs'>Absences justifiées ou non</a>
</p>

<h2>Absences non justifiées</h2>

	<p>Cette page est destinée à rappeler les absences/manquements non justifiés.<br />";

	if($periode_courante_seulement=="y") {
		$checked_periode_courante_seulement_y=" checked";
		$checked_periode_courante_seulement_n="";
	}
	else {
		$checked_periode_courante_seulement_y="";
		$checked_periode_courante_seulement_n=" checked";
	}
	if(getSettingAOui("abs2_afficher_alerte_nj")) {
		echo "
	L'affichage en page d'accueil est paramétré par défaut pour les comptes CPE.<br />";
	}
	else {
		echo "
	L'affichage en page d'accueil n'est pas activé pour les comptes CPE.<br />";
	}
	echo "
	Les valeurs par défaut sont les suivantes&nbsp;:<br />
	Lorsque plus de ".$abs2_afficher_alerte_nb_nj." manquement(s) n'ont pas été justifiés alors qu'il s'est écoulé au moins ".$abs2_afficher_alerte_nj_delai." jour(s) depuis le manquement, un affichage est réalisé.<br />
	Par défaut, seuls les manquements de la période courante sont affichés.<br />
	Les paramètres par défaut sont définis par l'administrateur dans le Paramétrage du module Absences 2.<br />
	Vous pouvez modifier dans le formulaire ci-dessous les critères pour un affichage ponctuel.</p>
	<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" name='form1' method=\"post\">
		<fieldset class='fieldset_opacite50''>
			<p>
				<label for='nb_nj'>Nombre de manquements non justifiés&nbsp;: </label><input type='text' name='nb_nj' id='nb_nj' onkeydown='clavier_2(this.id,event,1,50);' size='2' value='$nb_nj' /><br />
				<label for='nj_delai'>Manquements remontant à au moins &nbsp;: </label><input type='text' name='nj_delai' id='nj_delai' onkeydown='clavier_2(this.id,event,1,300);' size='2' value='$nj_delai' /> jour(s).<br />
				Afficher les manquements<br />
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='periode_courante_seulement' id='periode_courante_seulement_y' value='y'".$checked_periode_courante_seulement_y." /><label for='periode_courante_seulement_y'> de la période courante seulement</label><br />
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='periode_courante_seulement' id='periode_courante_seulement_n' value='n'".$checked_periode_courante_seulement_n." /><label for='periode_courante_seulement_n'> sur toute l'année</label><br />
				<input type='submit' value='Afficher' />
			</p>
		</fieldset>
	</form>";

	$lignes_alerte_abs2_nj=abs2_afficher_tab_alerte_nj($nb_nj, $nj_delai, $periode_courante_seulement);
	if($lignes_alerte_abs2_nj!="") {
		echo $lignes_alerte_abs2_nj;
	}
	else {
		echo "<p style='color:red'>Aucun manquement non justifié n'est relevé avec les critères choisis.</p>";
	}
}

require_once("../lib/footer.inc.php");
?>
