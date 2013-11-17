<?php
/* $Id$ */
/*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$niveau_arbo = 2;
require_once("../../lib/initialisations.inc.php");


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../../logout.php?auto=1");
	die();
}





//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
// Pour GEPI 1.4.3 à 1.4.4
// INSERT INTO droits VALUES('/mod_notanet/fiches_brevet.php','V','F','F','F','F','F','Accès aux fiches brevet','');
// Pour GEPI 1.5.x
// INSERT INTO droits VALUES('/mod_notanet/fiches_brevet.php','V','F','F','F','F','F','F','F','Accès à l export NOTANET','');
if (!checkAccess()) {
	header("Location: ../../logout.php?auto=1");
	die();
}
//======================================================================================






if (isset($_POST['enregistrer_param'])) {
	/*
	if(!isset($msg)){
		$msg="";
	}
	*/
	$msg="";

	if (isset($_POST['fb_academie'])) {
		if (!saveSetting("fb_academie", $_POST['fb_academie'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_academie !";
		}
	}

	if (isset($_POST['fb_departement'])) {
		if (!saveSetting("fb_departement", $_POST['fb_departement'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_departement !";
		}
	}

	if (isset($_POST['fb_session'])) {
		if (!saveSetting("fb_session", $_POST['fb_session'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_session !";
		}
	}


	if (isset($_POST['fb_mode_moyenne'])) {
		if (!saveSetting("fb_mode_moyenne", $_POST['fb_mode_moyenne'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_mode_moyenne !";
		}
	}


	if (isset($_POST['fb_largeur_tableau'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_largeur_tableau'])) || $_POST['fb_largeur_tableau'] < 1) {
			$_POST['fb_largeur_tableau'] = 950;
		}
		if (!saveSetting("fb_largeur_tableau", $_POST['fb_largeur_tableau'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_largeur_tableau !";
		}
	}

	if (isset($_POST['fb_largeur_col_disc'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_largeur_col_disc'])) || $_POST['fb_largeur_col_disc'] < 1) {
			$_POST['fb_largeur_col_disc'] = 31;
		}
		if (!saveSetting("fb_largeur_col_disc", $_POST['fb_largeur_col_disc'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_largeur_col_disc !";
		}
	}

	if (isset($_POST['fb_largeur_col_note'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_largeur_col_note'])) || $_POST['fb_largeur_col_note'] < 1) {
			$_POST['fb_largeur_col_note'] = 7;
		}
		if (!saveSetting("fb_largeur_col_note", $_POST['fb_largeur_col_note'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_largeur_col_note !";
		}
	}

	if (isset($_POST['fb_largeur_col_app'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_largeur_col_app'])) || $_POST['fb_largeur_col_app'] < 1) {
			$_POST['fb_largeur_col_app'] = 46;
		}
		if (!saveSetting("fb_largeur_col_app", $_POST['fb_largeur_col_app'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_largeur_col_app !";
		}
	}

	if (isset($_POST['fb_largeur_col_opt'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_largeur_col_opt'])) || $_POST['fb_largeur_col_opt'] < 1) {
			$_POST['fb_largeur_col_opt'] = 8;
		}
		if (!saveSetting("fb_largeur_col_opt", $_POST['fb_largeur_col_opt'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_largeur_col_opt !";
		}
	}

	if (isset($_POST['fb_nblig_avis_chef'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_nblig_avis_chef'])) || $_POST['fb_nblig_avis_chef'] < 1) {
			$_POST['fb_nblig_avis_chef'] = 4;
		}
		if (!saveSetting("fb_nblig_avis_chef", $_POST['fb_nblig_avis_chef'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_nblig_avis_chef !";
		}
	}

	if (isset($_POST['fb_titrepage'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_titrepage'])) || $_POST['fb_titrepage'] < 1) {
			$_POST['fb_titrepage'] = 14;
		}
		if (!saveSetting("fb_titrepage", $_POST['fb_titrepage'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_titrepage !";
		}
	}

	if (isset($_POST['fb_titretab'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_titretab'])) || $_POST['fb_titretab'] < 1) {
			$_POST['fb_titretab'] = 10;
		}
		if (!saveSetting("fb_titretab", $_POST['fb_titretab'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_titretab !";
		}
	}

	if (isset($_POST['fb_tittab_lineheight'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_tittab_lineheight'])) || $_POST['fb_tittab_lineheight'] < 1) {
			$_POST['fb_tittab_lineheight'] = 14;
		}
		if (!saveSetting("fb_tittab_lineheight", $_POST['fb_tittab_lineheight'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_tittab_lineheight !";
		}
	}



	if (isset($_POST['fb_taille_etab'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_taille_etab'])) || $_POST['fb_taille_etab'] < 1) {
			$_POST['fb_taille_etab'] = 10;
		}
		if (!saveSetting("fb_taille_etab", $_POST['fb_taille_etab'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_taille_etab !";
		}
	}


	if (isset($_POST['fb_taille_acad'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_taille_acad'])) || $_POST['fb_taille_acad'] < 1) {
			$_POST['fb_taille_acad'] = 10;
		}
		if (!saveSetting("fb_taille_acad", $_POST['fb_taille_acad'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_taille_acad !";
		}
	}

	if (isset($_POST['fb_taille_txt_disc'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_taille_txt_disc'])) || $_POST['fb_taille_txt_disc'] < 1) {
			$_POST['fb_taille_txt_disc'] = 10;
		}
		if (!saveSetting("fb_taille_txt_disc", $_POST['fb_taille_txt_disc'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_taille_txt_disc !";
		}
	}


	if (isset($_POST['fb_textetab'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_textetab'])) || $_POST['fb_textetab'] < 1) {
			$_POST['fb_textetab'] = 9;
		}
		if (!saveSetting("fb_textetab", $_POST['fb_textetab'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_textetab !";
		}
	}

	if (isset($_POST['fb_txttab_lineheight'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_txttab_lineheight'])) || $_POST['fb_txttab_lineheight'] < 1) {
			$_POST['fb_txttab_lineheight'] = 11;
		}
		if (!saveSetting("fb_txttab_lineheight", $_POST['fb_txttab_lineheight'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_txttab_lineheight !";
		}
	}

	if (isset($_POST['fb_marg_h'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_marg_h'])) || $_POST['fb_marg_h'] < 1) {
			$_POST['fb_marg_h'] = 2;
		}
		if (!saveSetting("fb_marg_h", $_POST['fb_marg_h'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_marg_h !";
		}
	}

	if (isset($_POST['fb_marg_l'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_marg_l'])) || $_POST['fb_marg_l'] < 1) {
			$_POST['fb_marg_l'] = 2;
		}
		if (!saveSetting("fb_marg_l", $_POST['fb_marg_l'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_marg_l !";
		}
	}

	if (isset($_POST['fb_marg_etab'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_marg_etab'])) || $_POST['fb_marg_etab'] < 1) {
			$_POST['fb_marg_etab'] = 2;
		}
		if (!saveSetting("fb_marg_etab", $_POST['fb_marg_etab'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_marg_etab !";
		}
	}

	if (isset($_POST['fb_marg_h_ele'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_marg_h_ele'])) || $_POST['fb_marg_h_ele'] < 1) {
			$_POST['fb_marg_h_ele'] = 1;
		}
		if (!saveSetting("fb_marg_h_ele", $_POST['fb_marg_h_ele'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_marg_h_ele !";
		}
	}

	if (isset($_POST['fb_taille_txt_ele'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_taille_txt_ele'])) || $_POST['fb_taille_txt_ele'] < 1) {
			$_POST['fb_taille_txt_ele'] = 10;
		}
		if (!saveSetting("fb_taille_txt_ele", $_POST['fb_taille_txt_ele'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_taille_txt_ele !";
		}
	}


	if (isset($_POST['fb_largeur_b2i'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_largeur_b2i'])) || $_POST['fb_largeur_b2i'] < 1) {
			$_POST['fb_largeur_b2i'] = 12;
		}
		if (!saveSetting("fb_largeur_b2i", $_POST['fb_largeur_b2i'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_largeur_b2i !";
		}
	}

	if (isset($_POST['fb_largeur_coche_b2i'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_largeur_coche_b2i'])) || $_POST['fb_largeur_coche_b2i'] < 1) {
			$_POST['fb_largeur_coche_b2i'] = 12;
		}
		if (!saveSetting("fb_largeur_coche_b2i", $_POST['fb_largeur_coche_b2i'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_largeur_coche_b2i !";
		}
	}

	if (isset($_POST['fb_modele_img'])) {
		if (($_POST['fb_modele_img']!=1)&&($_POST['fb_modele_img']!=2)) {
			$_POST['fb_modele_img'] = 1;
		}
		if (!saveSetting("fb_modele_img", $_POST['fb_modele_img'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_modele_img !";
		}
	}



/*
	if (isset($_POST['sessionMaxLength'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['sessionMaxLength'])) || $_POST['sessionMaxLength'] < 1) {
			$_POST['sessionMaxLength'] = 30;
		}
		if (!saveSetting("sessionMaxLength", $_POST['sessionMaxLength'])) {
			$msg .= "Erreur lors de l'enregistrement da durée max d'inactivité !";
		}
	}
*/
	if($msg==""){$msg="Enregistrement effectué.";}
}




//echo '<link rel="stylesheet" type="text/css" media="print" href="impression.css">';
//echo '<link rel="stylesheet" type="text/css" href="../mod_notanet.css">';

//**************** EN-TETE *****************
$titre_page = "Fiches Brevet";
//echo "<div class='noprint'>\n";
require_once("../../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************



// Récupération des variables:
// Tableau des classes:
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
// Vérifier s'il peut y avoir des accents dans un id_classe.

$type_brevet = isset($_POST['type_brevet']) ? $_POST['type_brevet'] : (isset($_GET['type_brevet']) ? $_GET['type_brevet'] : NULL);
if(isset($type_brevet)) {
	if((!my_ereg("[0-9]",$type_brevet))||(mb_strlen(my_ereg_replace("[0-9]","",$type_brevet))!=0)) {
		$type_brevet=NULL;
	}
}



//$avec_app=isset($_POST['avec_app']) ? $_POST['avec_app'] : NULL;
$avec_app=isset($_POST['avec_app']) ? $_POST['avec_app'] : "n";



if (isset($_GET['parametrer'])) {
	// Paramétrage des tailles de police, dimensions, nom d'académie, de département,...
	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../../accueil.php'>Accueil</a>";
	echo " | <a href='../index.php'>Accueil Notanet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix des classes</a>";
	echo "</p>\n";
	echo "</div>\n";

	echo "<h2>Paramètres d'affichage des Fiches Brevet</h2>\n";

	include("param_fiche_brevet.php");

	// FIN DU PARAMETRAGE

	require("../../lib/footer.inc.php");
	die();

}








$sql="SELECT DISTINCT type_brevet FROM notanet_ele_type ORDER BY type_brevet;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune association élève/type de brevet n'a encore été réalisée.<br />Commencez par <a href='../select_eleves.php'>sélectionner les élèves</a></p>\n";

 require("../../lib/footer.inc.php");
	die();
}

$sql="SELECT DISTINCT type_brevet FROM notanet_corresp ORDER BY type_brevet;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_type_brevet=mysqli_num_rows($res);
//if(mysql_num_rows($res)==0) {
if($nb_type_brevet==0) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune association matières/type de brevet n'a encore été réalisée.<br />Commencez par <a href='../select_matieres.php'>sélectionner les matières</a></p>\n";

 require("../../lib/footer.inc.php");
	die();
}

// Bibliothèque pour Notanet et Fiches brevet
include("../lib_brevets.php");

if(!isset($type_brevet)) {

	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../../accueil.php'>Accueil</a> | <a href='../index.php'>Accueil Notanet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?parametrer=y'>Paramètrer</a>";
	echo "</p>\n";
	echo "</div>\n";

	echo "<ul>\n";
	while($lig=mysqli_fetch_object($res)) {
		echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>Générer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
	}
	echo "</ul>\n";

 require("../../lib/footer.inc.php");
	die();
}

//echo "\$type_brevet=$type_brevet<br />";

//*****************************************************************************************************************************************
//elseif (!isset($id_classe)) {
if (!isset($id_classe)) {
	// Choix de la classe:
	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../../accueil.php'>Accueil</a>";
	echo " | <a href='../index.php'>Accueil Notanet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?parametrer=y'>Paramètrer</a>";
	echo "</p>\n";
	echo "</div>\n";


	//$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, notanet n WHERE p.id_classe = c.id AND c.id=n.id_classe ORDER BY classe");
	//$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, notanet n,notanet_ele_type net WHERE p.id_classe = c.id AND c.id=n.id_classe AND n.login=net.login ORDER BY classe");
	$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, periodes p, notanet n,notanet_ele_type net WHERE p.id_classe = c.id AND c.id=n.id_classe AND n.login=net.login AND net.type_brevet='$type_brevet' ORDER BY classe");
	if(!$call_data){
		//echo "<p><font color='red'>Attention:</font> Il semble que vous n'ayez pas mené la procédure notanet à son terme.<br />Cette procédure renseigne des tables requises pour générer les fiches brevet.<br />Effectuez la <a href='notanet.php'>procédure notanet</a>.</p>\n";
		echo "<p><font color='red'>Attention:</font> Il semble que vous n'ayez pas mené la procédure notanet à son terme.<br />Cette procédure renseigne des tables requises pour générer les fiches brevet.<br />Effectuez la <a href='../index.php'>procédure notanet</a>.</p>\n";

  require("../../lib/footer.inc.php");
		die();
	}
	$nombre_lignes = mysqli_num_rows($call_data);


	echo "<p>Choisissez les classes pour lesquelles vous souhaitez générer les fiches brevet:</p>\n";

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form_choix_classe' method='post'>\n";
	echo "<input type='hidden' name='type_brevet' value='$type_brevet' />\n";
	//echo "<input type='hidden' name='choix1' value='export' />\n";
	//echo "<input type='hidden' name='type_brevet' value='".$type_brevet."' />\n";
	echo "<p>Sélectionnez les classes : </p>\n";
	echo "<blockquote>\n";
	//$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");

	/*
	$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, notanet n WHERE p.id_classe = c.id AND c.id=n.id_classe ORDER BY classe");
	$nombre_lignes = mysql_num_rows($call_data);
	*/

	$size=min(10,$nombre_lignes);
	echo "<select name='id_classe[]' multiple='true' size='$size'>\n";
	$i = 0;
	while ($i < $nombre_lignes){
		$classe = mysql_result($call_data, $i, "classe");
		$ide_classe = mysql_result($call_data, $i, "id");
		echo "<option value='$ide_classe'";
		if($nombre_lignes==1) {echo " selected";}
		echo ">$classe</option>\n";
		$i++;
	}
	echo "</select><br />\n";
	echo "<label name='avec_app' style='cursor: pointer;'><input type='checkbox' name='avec_app' id='avec_app' value='y' checked /> Avec les appréciations</label>\n";
	echo "<input type='submit' name='choix_classe' value='Envoyer' />\n";
	echo "</blockquote>\n";
	//echo "</p>\n";
	echo "</form>\n";
	// FIN DU FORMULAIRE DE CHOIX DES CLASSES
}
//*****************************************************************************************************************************************
else {
	// DEBUT DE L'AFFICHAGE DES FICHES BREVET POUR LES CLASSES CHOISIES ET POUR LE TYPE_BREVET CHOISI
	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../../accueil.php'>Accueil</a>";
	echo " | <a href='../index.php'>Accueil Notanet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Type de brevet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?type_brevet=$type_brevet'>Choix des classes</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?parametrer=y'>Paramètrer</a>";
	echo "</p>\n";
	echo "</div>\n";

	// On récupère le tableau des paramètres associés à ce type de brevet:
	$tabmatieres=tabmatieres($type_brevet);

	// Saut de page pour ne pas imprimer la page avec l'entête...
	echo "<p>Pour ne pas être gêné par l'entête de la page lors de l'impression, la première page est exclue.<br />Un saut de page est inséré.<br />Veillez donc simplement à ne pas imprimer la première page.</p>\n";
	//echo "<p class='saut'>&nbsp;</p>\n";

	$fb_academie=getSettingValue("fb_academie");

	$fb_departement=getSettingValue("fb_departement");

	$fb_session=getSettingValue("fb_session");
	//echo "<tr><td colspan='2'>\$fb_session=$fb_session</td></tr>";
	if($fb_session==""){
		$tmp_date=getdate();
		$tmp_mois=$tmp_date['mon'];
		if($tmp_mois>9){
			$fb_session=$tmp_date['year']+1;
		}
		else{
			$fb_session=$tmp_date['year'];
		}
	}

	$fb_mode_moyenne=getSettingValue("fb_mode_moyenne");
	if($fb_mode_moyenne!=2){$fb_mode_moyenne=1;}

	$fb_nblig_avis_chef=getSettingValue("fb_nblig_avis_chef");
	if($fb_nblig_avis_chef==""){
		$fb_nblig_avis_chef=4;
	}

	$fb_largeur_tableau=getSettingValue("fb_largeur_tableau");
	if($fb_largeur_tableau==""){
		$fb_largeur_tableau=950;
	}

	$fb_largeur_col_disc=getSettingValue("fb_largeur_col_disc");
	if($fb_largeur_col_disc==""){
		$fb_largeur_col_disc=31;
	}

	$fb_largeur_col_note=getSettingValue("fb_largeur_col_note");
	if($fb_largeur_col_note==""){
		$fb_largeur_col_note=7;
	}

	$fb_largeur_col_app=getSettingValue("fb_largeur_col_app");
	if($fb_largeur_col_app==""){
		$fb_largeur_col_app=46;
	}

	$fb_largeur_col_opt=getSettingValue("fb_largeur_col_opt");
	if($fb_largeur_col_opt==""){
		$fb_largeur_col_opt=8;
	}

	$fb_titrepage=getSettingValue("fb_titrepage");
	if($fb_titrepage==""){
		$fb_titrepage=14;
	}

	$fb_titretab=getSettingValue("fb_titretab");
	if($fb_titretab==""){
		$fb_titretab=10;
	}

	$fb_taille_acad=getSettingValue("fb_taille_acad");
	if($fb_taille_acad==""){
		$fb_taille_acad=10;
	}

	$fb_taille_etab=getSettingValue("fb_taille_etab");
	if($fb_taille_etab==""){
		$fb_taille_etab=10;
	}


	$fb_tittab_lineheight=getSettingValue("fb_tittab_lineheight");
	if($fb_tittab_lineheight==""){
		$fb_tittab_lineheight=14;
	}

	$fb_textetab=getSettingValue("fb_textetab");
	if($fb_textetab==""){
		$fb_textetab=9;
	}

	$fb_txttab_lineheight=getSettingValue("fb_txttab_lineheight");
	if($fb_txttab_lineheight==""){
		$fb_txttab_lineheight=11;
	}

	$fb_taille_txt_disc=getSettingValue("fb_taille_txt_disc");
	if($fb_taille_txt_disc==""){
		$fb_taille_txt_disc=10;
	}

	$fb_marg_h=getSettingValue("fb_marg_h");
	if($fb_marg_h==""){
		$fb_marg_h=2;
	}

	$fb_marg_l=getSettingValue("fb_marg_l");
	if($fb_marg_l==""){
		$fb_marg_l=2;
	}

	$fb_marg_etab=getSettingValue("fb_marg_etab");
	if($fb_marg_etab==""){
		$fb_marg_etab=2;
	}


	$fb_taille_txt_ele=getSettingValue("fb_taille_txt_ele");
	if($fb_taille_txt_ele==""){
		$fb_taille_txt_ele=10;
	}

	$fb_marg_h_ele=getSettingValue("fb_marg_h_ele");
	if($fb_marg_h_ele==""){
		$fb_marg_h_ele=1;
	}

	$fb_largeur_b2i=getSettingValue("fb_largeur_b2i");
	if($fb_largeur_b2i==""){
		$fb_largeur_b2i=12;
	}

	$fb_largeur_coche_b2i=getSettingValue("fb_largeur_coche_b2i");
	if($fb_largeur_coche_b2i==""){
		$fb_largeur_coche_b2i=5;
	}

	$fb_modele_img=getSettingValue("fb_modele_img");
	if($fb_modele_img==""){
		$fb_modele_img=1;
	}

	if($fb_modele_img==1) {$img_alt="";} else {$img_alt="2";}

	echo "<style type='text/css'>

	@media print{
		body{
			background-color:white;
		}
	}

	.fb{
		font-size: ".$fb_textetab."pt;
	}

	table.fb{
		border-collapse: collapse;
	}

	.fb td{
		border: 1px solid black;
		text-align: center;
	}

	.discipline{
		text-align: left;
		margin: ".$fb_marg_h."px ".$fb_marg_l."px ".$fb_marg_h."px ".$fb_marg_l."px;
		font-size: ".$fb_taille_txt_disc."pt;
		/*font-weight: bold;*/
	}

	.div_etab{
		display: block;
		text-align: left;
		/*border: 1px solid black;*/
		margin: ".$fb_marg_etab."px;
		float: left;
	}

	.info_ele{
		font-size: ".$fb_taille_txt_ele."pt;
		margin: ".$fb_marg_h_ele."px 0 ".$fb_marg_h_ele."px 0;
	}
</style>\n";



	$ele_lieu_naissance=getSettingValue("ele_lieu_naissance") ? getSettingValue("ele_lieu_naissance") : "n";


	// Nom et adresse de l'établissement
	$gepiSchoolName=getSettingValue("gepiSchoolName");
	$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1");
	$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2");
	$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode");
	$gepiSchoolCity=getSettingValue("gepiSchoolCity");
	$gepiSchoolPays=getSettingValue("gepiSchoolPays");



	// Numéro de la colonne de moyenne à remplir: 1 ou 2
	// Ex.: pour 3ème à option, on a LV2 en colonne 1 et DP6h en colonne 2
	$num_fb_col=$tabmatieres["num_fb_col"];

/*
	echo "<style type='text/css'>
	@media print {
		.noprint {
				display: none;
		}
	}
</style>\n";
*/

	// BOUCLE SUR LA LISTE DES CLASSES
	for($i=0;$i<count($id_classe);$i++){

		// Calcul des moyennes de classes... pb avec le statut...
		$moy_classe=array();
		for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
			if($tabmatieres[$j][0]!=''){
				//$somme=0;
				// Dans la table 'notanet', notanet_mat='PREMIERE LANGUE VIVANTE'
				//                       et matiere='AGL1'
				//                       ou matiere='ALL1'
				// ... avec une seule ligne/enregistrement par élève pour la matière (aucun élève ne suit à la fois ALL1 et AGL1)
				// Dans la table 'notanet_corresp', notanet_mat='PREMIERE LANGUE VIVANTE'
				//                       et matiere='AGL1'
				//                       ou matiere='ALL1'
				// ... avec plusieurs lignes/enregistrements pour une même notanet_mat
				//$sql="SELECT ROUND(AVG(note),1) moyenne FROM notanet WHERE note!='DI' AND note!='AB' AND note!='NN' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
				$sql="SELECT ROUND(AVG(note),1) moyenne FROM notanet WHERE note!='DI' AND note!='AB' AND note!='NN' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
				//$sql="SELECT ROUND(AVG(note),1) moyenne FROM notanet n,notanet_ele_type net WHERE n.note!='DI' AND n.note!='AB' AND n.note!='NN' AND n.id_classe='$id_classe[$i]' AND n.matiere='".$tabmatieres[$j][0]."' AND n.login=net.login AND net.type_brevet='$type_brevet';";
				//echo "$sql<br />";
				$res_moy=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_moy)>0){
					$lig_moy=mysqli_fetch_object($res_moy);
					$moy_classe[$j]=$lig_moy->moyenne;
					//echo "\$moy_classe[$j]=$moy_classe[$j]<br />";
					// Là on fait la moyenne de l'ALL1 et de l'AGL1 ensemble car on ne fait pas la différence:
					// $tabmatieres[$j][0]='PREMIERE LANGUE VIVANTE'
				}
				else{
					$moy_classe[$j]="";
				}
			}
		}


		// Récupération du statut des matières: ceux validés lors du traitement NOTANET
		// pour repérer les matières non dispensées.
		for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
			if($tabmatieres[$j][0]!=''){
				//$sql="SELECT * FROM notanet_corresp WHERE notanet_mat='".$tabmatieres[$j][0]."' LIMIT 1";
				$sql="SELECT * FROM notanet_corresp WHERE notanet_mat='".$tabmatieres[$j][0]."' AND type_brevet='$type_brevet' LIMIT 1";
				//echo "<p>$sql</p>";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0){
					$lig=mysqli_fetch_object($res);
					$tabmatieres[$j][-4]=$lig->statut;
					$tabmatieres[$j][-5]=$lig->matiere;
				}
				else{
					$tabmatieres[$j][-4]="";
					$tabmatieres[$j][-5]="";
				}
			}
		}


		echo "<div class='noprint'>\n";
		//echo $type_brevet;
		echo "<p>Fiches Brevet de la classe de <b>".get_classe_from_id($id_classe[$i])."</b></p>\n";
		//echo "<hr />\n";
		if($i>0){echo "<p class='saut'>&nbsp;</p>\n";}
		echo "</div>\n";
		if($i==0){echo "<p class='saut'>&nbsp;</p>\n";}


		// Liste des élèves de la classe:
		//$sql="SELECT DISTINCT login FROM notanet WHERE id_classe='$id_classe[$i]' ORDER BY login";
		//$sql="SELECT DISTINCT e.* FROM eleves e, notanet n WHERE n.id_classe='$id_classe[$i]' AND n.login=e.login ORDER BY e.login";
		$sql="SELECT DISTINCT e.* FROM eleves e,
										notanet n,
										notanet_ele_type net
								WHERE n.id_classe='$id_classe[$i]' AND
										n.login=e.login AND
										net.login=n.login AND
										net.type_brevet='$type_brevet'
								ORDER BY e.login;";
		$res1=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res1)>0){
			// Boucle sur la liste des élèves
			while($lig1=mysqli_fetch_object($res1)){
				//echo "$lig1->login<br />\n";


				//=======================================
				//echo "<table border='0' width='100%'>\n";
				echo "<table border='0' width='".$fb_largeur_tableau."'>\n";
				echo "<tr>\n";

				// CASE DE GAUCHE: Académie, département et établissement
				//echo "<td valign='top' width='50%' align='left' style='font-weight:bold; font-size:".$fb_titretab."pt'>\n";
				echo "<td valign='top' width='30%' align='left' style='font-size:".$fb_titretab."pt'>\n";

				echo "<div align='left'>\n";
					//echo "<div align='center'>\n";
					echo "<table border='0'><tr><td align='center' style='font-size:".$fb_taille_acad."pt; margin: $fb_marg_etab;'>\n";
						echo "Académie de $fb_academie<br />\n";
						// Mettre une page de saisie...
						echo "Département: $fb_departement\n";
					echo "</td></tr></table>\n";
					//echo "</div>\n";
				echo "</div>\n";

				echo "<div style='border: 1px solid black;'>\n";
					//echo "<div class='div_etab'>\n";
					echo "<table border='0'>\n";
					echo "<tr>\n";
					echo "<td valign='top' style='font-size:".$fb_taille_etab."pt; margin: $fb_marg_etab;'>\n";
					echo "Etablissement&nbsp;: ";
					echo "</td>\n";
					echo "<td style='font-size:".$fb_taille_etab."pt; margin: $fb_marg_etab;'>\n";
					//echo "</div>\n";
					//echo "<div class='div_etab'>\n";
					//echo $gepiSchoolName."<br />\n".$gepiSchoolAdress1."<br />\n".$gepiSchoolAdress2."<br />\n".$gepiSchoolZipCode." ".$gepiSchoolCity;
					echo $gepiSchoolName."<br />\n".$gepiSchoolAdress1;
					if($gepiSchoolAdress1!=""){
						//echo ", ";
						echo "<br />";
					}
					echo $gepiSchoolAdress2;
					if($gepiSchoolAdress2!=""){echo ", ";}
					echo $gepiSchoolZipCode." ".$gepiSchoolCity;
					//echo "</div>\n";
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
				echo "</div>\n";
				echo "</td>\n";

				//echo "<td valign='top' width='50%' align='right' style='font-weight:bold; font-size:".$fb_titretab."pt'>\n";

				// CASE CENTRALE
				echo "<td valign='middle' width='40%' align='center' style='font-size:".$fb_titretab."pt;'>\n";
				echo "<span style='font-size:".$fb_titrepage."pt; font-weight:bold; text-align:center; letter-spacing:0.2em;'>FICHE SCOLAIRE BREVET</span>\n";
				echo "<br />\n";
				$fb_taille_texte_session=$fb_titrepage-2;
				echo "<span style='font-size:".$fb_taille_texte_session."pt; font-weight:bold; text-align:center;'>- session $fb_session -</span>\n";
				echo "<br />\n";
				echo "<span style='font-size:".$fb_titrepage."pt; font-weight:bold; text-align:center;'>série ".$tab_type_brevet[$type_brevet]."\n</span>\n";
				echo "</td>\n";

				// CASE DE DROITE: INFOS ELEVE
				echo "<td valign='top' width='30%' align='right'>\n";
					echo "<table class='boireaus' width='100%' >\n";
					echo "<tr>\n";
					echo "<td colspan='3' style='text-align:left;'>\n";
					echo "<p class='info_ele'>\n";
					echo "NOM&nbsp;: ".$lig1->nom."\n";
					echo "</p>\n";
					echo "</td>\n";
					echo "</tr>\n";

					echo "<tr>\n";
					echo "<td colspan='3' style='text-align:left;'>\n";
					echo "<p class='info_ele'>\n";
					echo "Prénom&nbsp;: ".$lig1->prenom."\n";
					echo "</p>\n";
					echo "</td>\n";
					echo "</tr>\n";

					echo "<tr>\n";
					echo "<td colspan='3' style='text-align:left;'>\n";
					echo "<p class='info_ele'>\n";
					echo "né";
					if($lig1->sexe=='F'){echo "e";}
					echo " le ".formate_date($lig1->naissance);
					echo "</p>\n";
					echo "</td>\n";
					echo "</tr>\n";

					echo "<tr>\n";
					echo "<td colspan='3' style='text-align:left;'>\n";
					echo "<p class='info_ele'>\n";
					echo "à \n";
					if($ele_lieu_naissance=="y") {echo get_commune($lig1->lieu_naissance,1);}
					echo "</p>\n";
					echo "</td>\n";
					echo "</tr>\n";

					echo "<tr>\n";
					echo "<td style='text-align:left;border:0px;'>\n";
					echo "<p class='info_ele'>\n";
					echo "redoublant";
					if($lig1->sexe=='F'){echo "e";}
					echo "&nbsp;: \n";
					echo "</p>\n";
					echo "</td>\n";

					$sql="SELECT doublant FROM j_eleves_regime WHERE login='".$lig1->login."';";
					$res_reg=mysqli_query($GLOBALS["mysqli"], $sql);
					//$img="case_non_cochee.png";
					$doublant='n';
					if(mysqli_num_rows($res_reg)>0) {
						$lig_reg=mysqli_fetch_object($res_reg);
						if($lig_reg->doublant=='R') {
							//$img="case_cochee.png";
							$doublant='y';
						}
					}
					echo "<td style='text-align:left;border:0px;'>\n";
					echo "<p class='info_ele'>\n";
					echo "oui ";
					echo "<img src='";
					if($doublant=='y') {echo "case_cochee".$img_alt.".png";} else {echo "case_non_cochee".$img_alt.".png";}
					echo "' width='17' height='16' alt='case à cocher' />\n";
					//file:///home/steph/2008_04_25/gepi/images/case_cochee.png
					// 17 16
					echo "</p>\n";
					echo "</td>\n";

					echo "<td style='text-align:left;border:0px;'>\n";
					echo "<p class='info_ele'>\n";
					echo "non ";
					echo "<img src='";
					if($doublant=='y') {echo "case_non_cochee".$img_alt.".png";} else {echo "case_cochee".$img_alt.".png";}
					echo "' width='17' height='16' alt='case à cocher' />\n";
					echo "</p>\n";
					echo "</td>\n";

					echo "</tr>\n";

					echo "</table>\n";
				echo "</td>\n";

				echo "</tr>\n";
				echo "</table>\n";

				/*
				echo "<h2 style='font-size:".$fb_titrepage."pt; text-align:center;'>FICHE SCOLAIRE DU BREVET<br />Série ".$tab_type_brevet[$type_brevet]."\n</h2>\n";

				echo "<table border='0' width='100%'>\n";
				echo "<tr>\n";
				echo "<td valign='top' width='50%' style='font-size:".$fb_titretab."pt; text-align:left;'>\n";
				echo "<p class='info_ele'>\n";
				echo "<b>Nom:</b> ".$lig1->nom."\n";
				echo "</p>\n";
				echo "</td>\n";
				echo "<td valign='top' width='50%' style='font-size:".$fb_titretab."pt; text-align:left;'>\n";
				echo "<p class='info_ele'>\n";
				echo "<b>Prénom(s):</b> ".$lig1->prenom."\n";
				echo "</p>\n";
				echo "</td>\n";
				echo "</tr>\n";

				echo "<tr>\n";
				echo "<td valign='top' width='50%' style='font-size:".$fb_titretab."pt; text-align:left;'>\n";
				echo "<p class='info_ele'>\n";
				echo "<b>Né";
				if($lig1->sexe=='F'){echo "e";}
				echo " le:</b> ".formate_date($lig1->naissance);
				echo "</p>\n";
				echo "</td>\n";
				echo "<td valign='top' width='50%' style='font-size:".$fb_titretab."pt; text-align:left;'>\n";
				echo "<p class='info_ele'>\n";
				echo "<b>à:</b> \n";
				echo "</p>\n";
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
				*/

				//=======================================
				// TABLEAU DES DISCIPLINES...
				echo "<table class='fb' width='".$fb_largeur_tableau."'>\n";

				//if(($type_brevet==0)||($type_brevet==1)||($type_brevet==5)||($type_brevet==6)){
					// Brevets série COLLEGE avec LV2 ou avec DP6
					// Brevets série TECHNOLOGIQUE avec ou sans DP6

				if(($type_brevet==0)||($type_brevet==1)) {
					// Brevets série COLLEGE avec LV2 ou avec DP6
					include('lignes_disc_01.php');
				}
				// ************************************************************************************************
				// ************************************************************************************************
				// ************************************************************************************************
				// AUTRE TYPE DE BREVET
				elseif(($type_brevet==2)||($type_brevet==3)){
					// Brevet série PROFESSIONNELLE avec ou sans DP6
					include('lignes_disc_23.php');
				}
				elseif(($type_brevet==5)||($type_brevet==6)){
					// Brevets série TECHNOLOGIQUE sans option ou avec DP6
					include('lignes_disc_56.php');
				}
				// ************************************************************************************************
				// ************************************************************************************************
				// ************************************************************************************************
				// AUTRE TYPE DE BREVET
				elseif(($type_brevet==4)||($type_brevet==7)){
					// Brevets séries PROFESSIONNELLE AGRICOLE et TECHNOLOGIQUE AGRICOLE
					//echo "<tr><td>A FAIRE...</td></tr>";

					echo "<tr>\n";
					echo "<td colspan='5' style='color:red; font-weight:bold; font-size:".$fb_titretab."pt;'>\n";
					echo "<b>ATTENTION:</b> CES FICHES BREVET NE SONT PAS CORRECTES.<br />JE NE SAVAIS PAS COMMENT TRAITER LES TROIS MATIERES (séparées dans NOTANET et regroupées dans la fiche brevet) Technologie, Sciences biologiques et sciences physiques DANS UN CAS ET L'EQUIVALENT EN série technologique agricole.";
					echo "</td>\n";
					echo "</tr>\n";

					echo "<tr>\n";

					echo "<td rowspan='2' width='".$fb_largeur_col_disc."%' style='font-weight:bold; font-size:".$fb_titretab."pt;'>\n";
					echo "DISCIPLINES";
					echo "</td>\n";

					echo "<td colspan='3' style='border: 1px solid black; text-align:center; font-weight:bold;'>\n";
					echo "Classe de 3ème de collège";
					echo "</td>\n";

					echo "<td rowspan='2' style='font-weight:bold; line-height: ".$fb_txttab_lineheight."pt; width=".$fb_largeur_col_opt."%'>\n";
					echo "Note Globale<br />affectée du<br />coefficient";
					echo "</td>\n";

					echo "</tr>\n";
					//=====================
					echo "<tr>\n";

					// La colonne discipline est dans le rowspan de la ligne précédente.

					echo "<td width='".$fb_largeur_col_note."%' style='font-weight:bold; line-height: ".$fb_txttab_lineheight."pt;'>\n";
					echo "Note<br />moyenne<br />de la<br />classe<br />0 à 20";
					echo "</td>\n";

					//echo "<td style='border: 1px solid black; text-align:center;'>\n";
					echo "<td width='".$fb_largeur_col_note."%' style='font-weight:bold; line-height: ".$fb_txttab_lineheight."pt;'>\n";
					echo "Note<br />moyenne<br />de<br />l'élève<br />0 à 20";
					echo "</td>\n";

					//echo "<td style='border: 1px solid black; text-align:center;'>\n";
					echo "<td width='".$fb_largeur_col_app."%' style='font-weight:bold;'>\n";
					echo "Appréciations des professeurs";
					echo "</td>\n";

					echo "</tr>\n";

					/*
					//=====================
					echo "<tr>\n";

					//echo "<td style='border: 1px solid black; text-align:center;'>\n";
					echo "<td colspan='2' style='font-weight:bold;'>\n";
					echo "3ème à option";
					echo "</td>\n";

					echo "</tr>\n";
					//=====================
					echo "<tr>\n";

					// La colonne discipline est dans le rowspan de la ligne précédente.
					// La colonne moyenne classe est dans le rowspan de la ligne précédente.
					// La colonne moyenne eleve est dans le rowspan de la ligne précédente.
					// La colonne appréciation est dans le rowspan de la ligne précédente.

					//echo "<td style='border: 1px solid black; text-align:center;'>\n";
					echo "<td width='".$fb_largeur_col_opt."%' style='font-weight:bold;'>\n";
					//echo "LV2";
					//echo $fb_intitule_col[1];
					echo $tabmatieres["fb_intitule_col"][1];
					echo "</td>\n";

					echo "<td width='".$fb_largeur_col_opt."%' style='font-weight:bold; line-height: ".$fb_txttab_lineheight."pt;'>\n";
					//echo "A module découverte professionnelle<br />6 heures";
					//echo $fb_intitule_col[2];
					echo $tabmatieres["fb_intitule_col"][2];
					echo "</td>\n";

					echo "</tr>\n";
					//=====================
					*/

					$TOTAL=0;
					$SUR_TOTAL=array();
					$SUR_TOTAL[1]=0;
					$SUR_TOTAL[2]=0;
					$temoin_NOTNONCA=0;
					for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
						$temoin_note_non_numerique="n";
						//if($tabmatieres[$j][0]!=''){
						if($tabmatieres[$j][0]!=''){
							//if($tabmatieres[$j][-1]!='NOTNONCA'){
							if(($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')){

								// ************************************
								// A REVOIR
								// PROBLEME AVEC CES TOTAUX: SI UN ELEVE EST AB, DI ou NN, IL NE FAUDRAIT PAS AUGMENTER???...
								if((mb_strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][1]))==0)&&($tabmatieres[$j][-1]!='PTSUP')){
									$SUR_TOTAL[1]+=$tabmatieres[$j]['fb_col'][1];
								}
								// ************************************

								echo "<tr>\n";

								// Discipline
								//echo "<td style='border: 1px solid black; text-align:left;'>\n";
								echo "<td";
								if($tabmatieres[$j][-1]=='PTSUP'){
									echo " rowspan='2'";
								}
								echo ">\n";
								echo "<p class='discipline'>";
								//echo "<span class='discipline'>";
								if(!isset($tabmatieres[$j]["lig_speciale"])){
									echo ucfirst(mb_strtolower($tabmatieres[$j][0]));
									//if($tabmatieres[$j][0]=="OPTION FACULTATIVE (1)"){echo ": ".$tabmatieres[$j][-5];}
									if($tabmatieres[$j][0]=="OPTION FACULTATIVE (1)"){

										// ==============================
										// recherche de la matière facultative pour l'élève
										//$sql_mat_fac="SELECT mat FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
										$sql_mat_fac="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
										$res_mat_fac=mysqli_query($GLOBALS["mysqli"], $sql_mat_fac);
										if(mysqli_num_rows($res_mat_fac)>0){
											$lig_mat_fac=mysqli_fetch_object($res_mat_fac);

											//echo ": ".$lig_mat_fac->mat;
											echo ": ".$lig_mat_fac->matiere;
										}
										// ==============================

										//echo ": ".$lig_mat_fac->mat;
									}
								}
								else{
									// Lignes spéciales: LV2 ou DP6
									echo ucfirst(mb_strtolower($tabmatieres[$j]["lig_speciale"]));
								}
								//echo "</span>\n";
								echo "</p>\n";
								echo "</td>\n";

								// Moyenne classe
								echo "<td ";
								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "rowspan='2' ";
								}
								echo "style='border: 1px solid black; text-align:center;'>\n";
								if($fb_mode_moyenne==1){
									echo $moy_classe[$j];
								}
								else{
									//$sql="SELECT mat FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
									$sql="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_mat)>0){
										$lig_mat=mysqli_fetch_object($res_mat);
										//echo "$lig_mat->mat: ";

										//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
										$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
										//echo "$sql<br />";
										$res_moy=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_moy)>0){
											$lig_moy=mysqli_fetch_object($res_moy);
											echo "$lig_moy->moyenne_mat";
										}
									}
								}
								echo "</td>\n";

								// Moyenne élève
								echo "<td ";
								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "rowspan='2' ";
								}
								echo "style='border: 1px solid black; text-align:center;'>\n";
								//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
								$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
								$res_note=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_note)>0){
									$lig_note=mysqli_fetch_object($res_note);
									echo "$lig_note->note";
									//$note="$lig_note->note";
								}
								else{
									echo "&nbsp;";
									//$note="&nbsp;";
								}
								//echo "$note";
								echo "</td>\n";

								// Appréciation
								echo "<td ";
								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "rowspan='2' ";
								}
								//echo "style='border: 1px solid black; text-align:center;'>\n";
								//echo "&nbsp;";

								echo "style='border: 1px solid black; text-align:left; font-size:".$fb_textetab."pt;'>\n";

								if($avec_app=="y") {
									$sql="SELECT appreciation FROM notanet_app na,
																	notanet_corresp nc
																WHERE na.login='$lig1->login' AND
																	nc.notanet_mat='".$tabmatieres[$j][0]."' AND
																	nc.matiere=na.matiere;";
									//echo "$sql<br />";
									$res_app=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_app)>0){
										$lig_app=mysqli_fetch_object($res_app);
										echo "$lig_app->appreciation";
									}
									else{
										echo "&nbsp;";
									}
								}
								else {
									echo "&nbsp;";
								}

								echo "</td>\n";


								/*
								// EXTRACTION POUR LA COLONNE DE DROITE
								$valeur_tmp="&nbsp;";
								$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
								$res_note=mysql_query($sql);
								if(mysql_num_rows($res_note)){
									$lig_note=mysql_fetch_object($res_note);
									//echo "$lig_note->note";
									//$valeur_tmp="$lig_note->note";
									//$valeur_tmp=$lig_note->note*$tabmatieres[$j][-2];
									if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
										$valeur_tmp=$lig_note->note*$tabmatieres[$j][-2];
										//$TOTAL+=$lig_note->note;
										// Le cas PTSUP est calculé plus loin
										if($tabmatieres[$j][-1]!='PTSUP'){
											$TOTAL+=$lig_note->note;
										}
									}
									else{
										$valeur_tmp=$lig_note->note;
									}
									//$note="$lig_note->note";
								}
								*/

								// EXTRACTION POUR LA(ES) COLONNE(S) DE DROITE
								$valeur_tmp="&nbsp;";
								//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
								$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
								$res_note=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_note)){
									$lig_note=mysqli_fetch_object($res_note);
									if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
										$valeur_tmp=$lig_note->note*$tabmatieres[$j][-2];
										// Le cas PTSUP est calculé plus loin
										if($tabmatieres[$j][-1]!='PTSUP'){
											//$TOTAL+=$lig_note->note;
											$TOTAL+=$valeur_tmp;
										}
									}
									else{
										$valeur_tmp=$lig_note->note;
										$temoin_note_non_numerique="y";

										if(($tabmatieres[$j][-1]!='PTSUP')){
											$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
										}
									}
								}
								else{
									// FAUT-IL UN TEMOIN POUR DECREMENTER LE SUR_TOTAL ?
									if(($tabmatieres[$j][-1]!='PTSUP')){
										$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
									}
								}




								echo "<td ";
								//echo "style='border: 1px solid black; text-align:right;'>\n";
								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "style='border: 1px solid black; text-align:center;'>\n";
									echo "<b>Points > à 10</b>";
								}
								else{
									/*
									$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
									$res_note=mysql_query($sql);
									if(mysql_num_rows($res_note)){
										$lig_note=mysql_fetch_object($res_note);
										echo "$lig_note->note";
										if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
											$TOTAL+=$lig_note->note;
										}
										//$note="$lig_note->note";
									}
									else{
										echo "&nbsp;";
										//$note="&nbsp;";
									}
									*/
									echo "style='border: 1px solid black;";
									if($num_fb_col==1){
										echo " text-align:center;";
										echo "'>\n";
										echo "$valeur_tmp";
									}
									else{
										echo " text-align:right;";
										if($tabmatieres[$j]['fb_col'][1]=="X"){echo " background-color:gray;";}
										echo "'>\n";
										echo "&nbsp;";
									}

									//$nb=$tabmatieres[$j][-2]*20;
									//echo " / $nb";
									//if($tabmatieres[$j]['fb_col'][1]!="X"){
									//	echo " / ".$tabmatieres[$j]['fb_col'][1];
									//}
									if($tabmatieres[$j]['fb_col'][1]!="X"){
										if(($temoin_note_non_numerique=="n")||($num_fb_col==2)) {
											echo " / ".$tabmatieres[$j]['fb_col'][1];
										}
									}
								}
								echo "</td>\n";


								/*
								//echo "style='border: 1px solid black; text-align:center;'>\n";
								echo "<td ";
								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "style='border: 1px solid black; text-align:center;'>\n";
									echo "<b>Points > à 10</b>";
								}
								else{
									//echo "style='border: 1px solid black; text-align:right;'>\n";
									echo "style='border: 1px solid black;";
									if($num_fb_col==2){
										echo " text-align:center;";
										echo "'>\n";
										echo "$valeur_tmp";
									}
									else{
										echo " text-align:right;";
										echo "'>\n";
										echo "&nbsp;";
									}

									//$nb=$tabmatieres[$j][-2]*20;
									//echo " / $nb";
									echo " / ".$tabmatieres[$j]['fb_col'][2];
								}
								echo "</td>\n";
								*/

								echo "</tr>\n";

								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "<tr>\n";
									//echo "<td style='border: 1px solid black; text-align:right;'>\n";

									$valeur_tmp="";
									//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
									$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_note=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_note)){
										$lig_note=mysqli_fetch_object($res_note);
										//echo "$lig_note->note";
										//$note="$lig_note->note";

										if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
											$ptsup=$lig_note->note-10;
											if($ptsup>0){
												//echo "$ptsup";
												$valeur_tmp=$ptsup;
												//if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
													$TOTAL+=$ptsup;
												//}
											}
											else{
												//echo "0";
												$valeur_tmp=0;
											}
										}
										else{
											$valeur_tmp=$lig_note->note;
										}
									}
									else{
										//echo "&nbsp;";
										$valeur_tmp="&nbsp;";
										//$note="&nbsp;";
									}


									echo "<td style='border: 1px solid black; text-align:center;'>\n";
									echo "$valeur_tmp";
									echo "</td>\n";

									echo "</tr>\n";
								}
							}
							else{
								//if($tabmatieres[$j][-4]!='non dispensee dans l etablissement'){
									//$temoin_NOTNONCA++;
								if($tabmatieres[$j][-1]=="NOTNONCA"){
									$temoin_NOTNONCA++;
									//echo "<!-- \$temoin_NOTNONCA=$temoin_NOTNONCA \n\$tabmatieres[$j][0]=".$tabmatieres[$j][0]."-->\n";
								}
								//}
							}
							// ...=====...($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')

						}
					}
					// FIN DE ...




					//=====================

					if($temoin_NOTNONCA>0){
						// ON TRAITE LES MATIERES NOTNONCA
						echo "<tr>\n";

						echo "<td colspan='4' style='border: 1px solid black; text-align:center; font-weight:bold;'>\n";
						echo "A titre indicatif";
						echo "</td>\n";

						echo "<td style='font-weight:bold; font-size:".$fb_titretab."pt; line-height: ".$fb_tittab_lineheight."pt;'>\n";
						echo "TOTAL DES POINTS";
						echo "</td>\n";
						echo "</tr>\n";

						$num_lig=0;
						// On repasse en revue toutes les matières en ne retenant que celles qui sont NOTNONCA
						for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
							//if($tabmatieres[$j][0]!=''){
							if(($tabmatieres[$j][0]!='')&&($tabmatieres[$j][-1]=='NOTNONCA')){
								if($tabmatieres[$j][-4]!='non dispensee dans l etablissement'){
									echo "<tr>\n";

									echo "<td style='border: 1px solid black; text-align:left;'>\n";
									echo "<p class='discipline'>";
									echo ucfirst(mb_strtolower($tabmatieres[$j][0]));
									echo "</p>";
									echo "</td>\n";

									// Moyenne de la classe
									echo "<td style='border: 1px solid black; text-align:center;'>\n";
									//echo $moy_classe[$j];
									if($fb_mode_moyenne==1){
										echo $moy_classe[$j];
									}
									else{
										//$sql="SELECT mat FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
										$sql="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
										$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_mat)>0){
											$lig_mat=mysqli_fetch_object($res_mat);
											//echo "$lig_mat->mat: ";

											//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
											$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
											//echo "$sql<br />";
											$res_moy=mysqli_query($GLOBALS["mysqli"], $sql);
											if(mysqli_num_rows($res_moy)>0){
												$lig_moy=mysqli_fetch_object($res_moy);
												echo "$lig_moy->moyenne_mat";
											}
										}
									}
									echo "</td>\n";

									// Moyenne de l'élève
									echo "<td style='border: 1px solid black; text-align:center;'>\n";
									//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
									$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_note=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_note)){
										$lig_note=mysqli_fetch_object($res_note);
										echo "$lig_note->note";
									}
									else{
										echo "&nbsp;";
									}
									echo "</td>\n";

									// Appréciation
									echo "<td ";
									//style='border: 1px solid black; text-align:center;'>\n";

									echo "style='border: 1px solid black; text-align:left; font-size:".$fb_textetab."pt;'>\n";

									if($avec_app=="y") {
										$sql="SELECT appreciation FROM notanet_app na,
																		notanet_corresp nc
																	WHERE na.login='$lig1->login' AND
																		nc.notanet_mat='".$tabmatieres[$j][0]."' AND
																		nc.matiere=na.matiere;";
										//echo "$sql<br />";
										$res_app=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_app)>0){
											$lig_app=mysqli_fetch_object($res_app);
											echo "$lig_app->appreciation";
										}
										else{
											echo "&nbsp;";
										}
									}
									else {
										echo "&nbsp;";
									}

									echo "</td>\n";

									// Colonne total des lignes calculées (non NOTNONCA)...
									if($num_lig==0){
										$nb_info=$temoin_NOTNONCA;

										//echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:right;'>\n";
										echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:center;'>\n";
										//echo "$TOTAL / 220";
										echo "$TOTAL";
										echo " / ".$SUR_TOTAL[1];
										echo "</td>\n";
										//echo "</td>\n";

										$num_lig++;
									}

									/*
									echo "<td style='border: 1px solid black; text-align:right;'>\n";
									echo "/20";
									echo "</td>\n";
									*/

									echo "</tr>\n";
								}
								else{
									// Matière 'non dispensee dans l etablissement'
									// On affiche seulement les intitulés et le total des barèmes...
									echo "<tr>\n";

									echo "<td style='border: 1px solid black; text-align:left;'>\n";
									echo "<p class='discipline'>";
									echo ucfirst(mb_strtolower($tabmatieres[$j][0]));
									echo "</p>";
									echo "</td>\n";

									echo "<td style='border: 1px solid black; text-align:center;'>\n";
									echo "&nbsp;";
									echo "</td>\n";

									echo "<td style='border: 1px solid black; text-align:center;'>\n";
									echo "&nbsp;";
									echo "</td>\n";

									echo "<td style='border: 1px solid black; text-align:center;'>\n";
									echo "&nbsp;";
									echo "</td>\n";

									if($num_lig==0){
										$nb_info=$temoin_NOTNONCA;

										echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:center;'>\n";
										//echo "$TOTAL / 220";
										echo "$TOTAL";
										echo " / ".$SUR_TOTAL[1];
										echo "</td>\n";

										$num_lig++;
									}

									echo "</tr>\n";
								}
							}
						}
						// FIN DE LA BOUCLE SUR LA LISTE DES MATIERES
					}
					// FIN DU TRAITEMENT DES MATIERES NOTNONCA

					// ET SINON??? ON N'AFFICHE PAS LE TOTAL??? A REVOIR...

				}
				else{
					echo "<tr><td>BIZARRE! Ce type de brevet n'est pas prévu</td></tr>";
				}


				// SOCLES B2I ET A2
				$note_b2i="";
				$note_a2="";
				$lv_a2="";

				$sql="SELECT * FROM notanet_socles WHERE login='".$lig1->login."';";
				$res_soc=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_soc)>0) {
					$lig_soc=mysqli_fetch_object($res_soc);
					$note_b2i=$lig_soc->b2i;
					$note_a2=$lig_soc->a2;

					$sql="SELECT nom_complet FROM matieres WHERE matiere='".$lig_soc->lv."';";
					$res_nom_mat_a2=mysqli_query($GLOBALS["mysqli"], $sql);

					if(mysqli_num_rows($res_nom_mat_a2)>0) {
						$lig_lv_a2=mysqli_fetch_object($res_nom_mat_a2);
						$lv_a2=$lig_lv_a2->nom_complet;
					}
					else {
						$lv_a2=$lig_soc->lv;
					}
				}



				// PARTIE B2i, A2 de LANGUE et AVIS DU PRINCIPAL
				echo "<tr>\n";
				echo "<td rowspan='3' width='".$fb_largeur_b2i."%' valign='top' style='font-size:".$fb_textetab."pt; border: 1px solid black; text-align:center;'>\n";
				echo "<b>Brevet<br />informatique<br />et internet (B2i)<br />(*)</b>";
				echo "</td>\n";
				echo "<td width='".$fb_largeur_coche_b2i."%' style='font-size:".$fb_textetab."pt; border-right:0px;'>\n";
				if($note_b2i=='MS') {
					echo "<img src='case_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' /> MS&nbsp;: \n";
				}
				else {
					echo "<img src='case_non_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' /> MS&nbsp;: \n";
				}
				echo "</td>\n";
				echo "<td colspan='5' style='font-size:".$fb_textetab."pt; text-align:left; border-left:0px;'>\n";
				echo "maîtrise du socle\n";
				echo "</td>\n";

				// AVIS DU PRINCIPAL
				echo "<td rowspan='7' valign='top' style='border: 1px solid black; text-align:left;'>\n";

				$def_fav="";
				$def_avis="";
				$sql="SELECT * FROM notanet_avis WHERE login='".$lig1->login."';";
				$res_avis_ce=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_avis_ce)>0) {
					$lig_avis=mysqli_fetch_object($res_avis_ce);
					$def_fav=$lig_avis->favorable;
					$def_avis=$lig_avis->avis;
				}

					echo "<table border='0' width='100%'>\n";
					echo "<tr>\n";
					echo "<td style='text-align:left;border:0px;'>\n";
					echo "<b>Avis du chef d'établissement</b>&nbsp;:";

					echo "<p>";
					if($def_fav=="O") {
						echo "<img src='case_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' />";
					}
					else {
						echo "<img src='case_non_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' />";
					}
					echo " avis favorable</p>\n";
					echo "<br />\n";

					echo "<p>";
					if($def_fav=="N") {
						echo "<img src='case_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' />";
					}
					else {
						echo "<img src='case_non_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' />";
					}
					echo " avis défavorable<br />\n";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<img src='fleche_evidee".$img_alt.".png' width='16' height='19' alt='Flèche' /> avis motivé&nbsp;:<br />\n";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;";
					if($def_avis=="") {
						echo "................................";
					}
					else {
						echo $def_avis;
					}
					echo "</td>\n";
					echo "<td style='text-align:center;border:0px;'>\n";
					echo "signature";
					echo "</td>\n";
					echo "</tr>\n";
					echo "</table>\n";

				echo "</td>\n";
				echo "</tr>\n";

				// Lignes 2 et 3 du B2i
				echo "<tr>\n";
				echo "<td style='font-size:".$fb_textetab."pt; border-right:0px;'>\n";
				if($note_b2i=='ME') {
					echo "<img src='case_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' /> ME&nbsp;: \n";
				}
				else {
					echo "<img src='case_non_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' /> ME&nbsp;: \n";
				}
				echo "</td>\n";
				echo "<td colspan='5' style='font-size:".$fb_textetab."pt; text-align:left; border-left:0px;'>\n";
				echo "maîtrise de certains éléments (<i><b>feuille de position ci-jointe</b></i>)\n";
				echo "</td>\n";
				echo "</tr>\n";

				echo "<tr>\n";
				echo "<td style='font-size:".$fb_textetab."pt; border-right:0px;'>\n";
				if($note_b2i=='MN') {
					echo "<img src='case_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' /> MN&nbsp;: \n";
				}
				else {
					echo "<img src='case_non_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' /> MN&nbsp;: \n";
				}
				echo "</td>\n";
				echo "<td colspan='5' style='font-size:".$fb_textetab."pt; text-align:left; border-left:0px;'>\n";
				echo "maîtrise du socle non évaluée\n";
				echo "</td>\n";
				echo "</tr>\n";


				// NIVEAU A2
				echo "<tr>\n";
				echo "<td rowspan='3' valign='top' style='font-size:".$fb_textetab."pt; border: 1px solid black; text-align:center;'>\n";
				echo "<b>Niveau A2 ";
				if(($type_brevet=='2')||($type_brevet=='3')) {
					echo "(**)";
				}
				else {
					echo "(*)";
				}
				echo "</b><br />Langue vivante&nbsp;:<br />";
				if($lv_a2=="") {
					echo ".......................";
				}
				else {
					echo $lv_a2;
				}
				echo "</td>\n";
				echo "<td style='font-size:".$fb_textetab."pt; border-right:0px;'>\n";
				if($note_a2=='MS') {
					echo "<img src='case_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' /> MS&nbsp;: \n";
				}
				else {
					echo "<img src='case_non_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' /> MS&nbsp;: \n";
				}
				echo "</td>\n";
				echo "<td colspan='5' style='font-size:".$fb_textetab."pt; text-align:left; border-left:0px;'>\n";
				echo "maîtrise du socle\n";
				echo "</td>\n";
				echo "</tr>\n";

				// Lignes 2 et 3 du Niveau A2
				echo "<tr>\n";
				echo "<td style='font-size:".$fb_textetab."pt; border-right:0px;'>\n";
				if($note_a2=='ME') {
					echo "<img src='case_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' /> ME&nbsp;: \n";
				}
				else {
					echo "<img src='case_non_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' /> ME&nbsp;: \n";
				}
				echo "</td>\n";
				echo "<td colspan='5' style='font-size:".$fb_textetab."pt; text-align:left; border-left:0px;'>\n";
				echo "maîtrise de certains éléments (<i><b>annexe ci-jointe</b></i>)\n";
				echo "</td>\n";
				echo "</tr>\n";

				echo "<tr>\n";
				echo "<td style='font-size:".$fb_textetab."pt; border-right:0px;'>\n";
				if($note_a2=='MN') {
					echo "<img src='case_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' /> MN&nbsp;: \n";
				}
				else {
					echo "<img src='case_non_cochee".$img_alt.".png' width='17' height='16' alt='case à cocher' /> MN&nbsp;: \n";
				}
				echo "</td>\n";
				echo "<td colspan='5' style='font-size:".$fb_textetab."pt; text-align:left; border-left:0px;'>\n";
				echo "maîtrise du socle non évaluée\n";
				echo "</td>\n";
				echo "</tr>\n";


				// Ligne d'information sur les (*)
				echo "<tr>\n";
				echo "<td colspan='7' style='font-size:".$fb_textetab."pt; text-align:left;'>\n";
				echo "(*) Cocher une des 3 cases\n";
				if(($type_brevet=='2')||($type_brevet=='3')) {
					echo "<br />\n";
					echo "(**) Ne pas compléter si option \"sciences physiques\" choisie.\n";
				}
				echo "</td>\n";
				echo "</tr>\n";

				/*
				echo "<tr>\n";
				echo "<td style='border: 1px solid black; text-align:center;'>\n";
				echo "&nbsp;";
				echo "</td>\n";
				if(($type_brevet!=4)&&($type_brevet!=7)){
					echo "<td style='border: 1px solid black; text-align:center;'>\n";
					echo "&nbsp;";
					echo "</td>\n";
				}
				echo "</tr>\n";







				echo "<tr>\n";
				echo "<td colspan='4' rowspan='2' valign='top' style='border: 1px solid black; text-align:left;'>\n";
				echo "<b>Avis et signature du chef d'établissement:</b>";
				for($k=0;$k<=$fb_nblig_avis_chef;$k++){
					echo "<br />\n";
				}
				echo "</td>\n";
				if(($type_brevet==4)||($type_brevet==7)){
					echo "<td style='font-weight:bold; font-size:".$fb_titretab."pt;'>\n";
				}
				else{
					echo "<td colspan='2' style='font-weight:bold; font-size:".$fb_titretab."pt;'>\n";
				}
				echo "DECISION";
				echo "</td>\n";
				echo "</tr>\n";

				echo "<tr>\n";
				echo "<td style='border: 1px solid black; text-align:center;'>\n";
				echo "&nbsp;";
				echo "</td>\n";
				if(($type_brevet!=4)&&($type_brevet!=7)){
					echo "<td style='border: 1px solid black; text-align:center;'>\n";
					echo "&nbsp;";
					echo "</td>\n";
				}
				echo "</tr>\n";
				*/

				echo "</table>\n";
				//echo "</div>\n";
				//=======================================
				//=======================================
				//=======================================
				//=======================================

				//echo "<hr />\n";
				echo "<p class='saut'>&nbsp;</p>\n";
			}
			// Fin de la boucle sur la liste des élèves
		}
		//echo "</p>\n";

		// FIN DE LA BOUCLE SUR LA LISTE DES CLASSES
	}

}
// Fermeture du DIV container initialisé dans le header.inc
//echo "</div>\n";
require("../../lib/footer.inc.php");
?>
