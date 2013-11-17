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

	if (isset($_POST['fb_largeur_col_disc'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_largeur_col_disc'])) || $_POST['fb_largeur_col_disc'] < 1) {
			$_POST['fb_largeur_col_disc'] = 24;
		}
		if (!saveSetting("fb_largeur_col_disc", $_POST['fb_largeur_col_disc'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_largeur_col_disc !";
		}
	}

	if (isset($_POST['fb_largeur_col_note'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_largeur_col_note'])) || $_POST['fb_largeur_col_note'] < 1) {
			$_POST['fb_largeur_col_note'] = 8;
		}
		if (!saveSetting("fb_largeur_col_note", $_POST['fb_largeur_col_note'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_largeur_col_note !";
		}
	}

	if (isset($_POST['fb_largeur_col_app'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_largeur_col_app'])) || $_POST['fb_largeur_col_app'] < 1) {
			$_POST['fb_largeur_col_app'] = 31;
		}
		if (!saveSetting("fb_largeur_col_app", $_POST['fb_largeur_col_app'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_largeur_col_app !";
		}
	}

	if (isset($_POST['fb_largeur_col_opt'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_largeur_col_opt'])) || $_POST['fb_largeur_col_opt'] < 1) {
			$_POST['fb_largeur_col_opt'] = 13;
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

	if (isset($_POST['fb_textetab'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['fb_textetab'])) || $_POST['fb_textetab'] < 1) {
			$_POST['fb_textetab'] = 7;
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
			$_POST['fb_marg_h'] = 7;
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
			$_POST['fb_marg_h_ele'] = 3;
		}
		if (!saveSetting("fb_marg_h_ele", $_POST['fb_marg_h_ele'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_marg_h_ele !";
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

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form_param' method='post'>\n";
	echo "<table border='0'>\n";

	$alt=1;
	$fb_academie=getSettingValue("fb_academie");
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Académie de: </td>\n";
	echo "<td><input type='text' name='fb_academie' value='$fb_academie' /></td>\n";
	echo "</tr>\n";

	$fb_departement=getSettingValue("fb_departement");
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Département de: </td>\n";
	echo "<td><input type='text' name='fb_departement' value='$fb_departement' /></td>\n";
	echo "</tr>\n";

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
	//echo "<tr><td colspan='2'>\$fb_session=$fb_session</td></tr>";
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Session: </td>\n";
	echo "<td><input type='text' name='fb_session' value='$fb_session' /></td>\n";
	echo "</tr>\n";

	// ****************************************************************************
	// MODE DE CALCUL POUR LES MOYENNES DES REGROUPEMENTS DE MATIERES:
	// - LV1: on fait la moyenne de toutes les LV1 (AGL1, ALL1)
	// ou
	// - LV1: on présente pour chaque élève, la moyenne qui correspond à sa LV1: ALL1 s'il fait ALL1,...
	// ****************************************************************************
	$fb_mode_moyenne=getSettingValue("fb_mode_moyenne");
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td valign='top'>Mode de calcul des moyennes pour les options Notanet associées à plusieurs matières (<i>ex.: LV1 associée à AGL1 et ALL1</i>): </td>\n";
	echo "<td>";
		echo "<table border='0'>\n";
		echo "<tr>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='fb_mode_moyenne' value='1' ";
		if($fb_mode_moyenne!="2"){
			echo "checked />";
		}
		else{
			echo "/>";
		}
		echo "</td>\n";
		echo "<td>\n";
		echo "Calculer la moyenne de toutes matières d'une même option Notanet confondues<br />\n";
		echo "(<i>on compte ensemble les AGL1 et ALL1; c'est la moyenne de toute la LV1 qui est effectuée</i>)\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='fb_mode_moyenne' value='2' ";
		if($fb_mode_moyenne=="2"){
			echo "checked />";
		}
		else{
			echo "/>";
		}
		echo "</td>\n";
		echo "<td>\n";
		echo "Calculer les moyennes par matières<br />\n";
		echo "(<i>on ne mélange pas AGL1 et ALL1 dans le calcul de la moyenne de classe pour un élève</i>)\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "</tr>\n";


	$fb_nblig_avis_chef=getSettingValue("fb_nblig_avis_chef");
	if($fb_nblig_avis_chef==""){
		$fb_nblig_avis_chef=4;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Nombre de lignes pour l'avis du chef d'établissement: </td>\n";
	echo "<td><input type='text' name='fb_nblig_avis_chef' value='$fb_nblig_avis_chef' /></td>\n";
	echo "</tr>\n";

	$fb_largeur_col_disc=getSettingValue("fb_largeur_col_disc");
	if($fb_largeur_col_disc==""){
		$fb_largeur_col_disc=24;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur de la colonne DISCIPLINES (<i>1ère colonne</i>) en pourcentage: </td>\n";
	echo "<td><input type='text' name='fb_largeur_col_disc' value='$fb_largeur_col_disc' /></td>\n";
	echo "</tr>\n";

	$fb_largeur_col_note=getSettingValue("fb_largeur_col_note");
	if($fb_largeur_col_note==""){
		$fb_largeur_col_note=8;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur des colonnes Note moyenne de la classe et Note moyenne de l'élève (<i>2ème et 3ème colonnes</i>) en pourcentage: </td>\n";
	echo "<td><input type='text' name='fb_largeur_col_note' value='$fb_largeur_col_note' /></td>\n";
	echo "</tr>\n";

	$fb_largeur_col_app=getSettingValue("fb_largeur_col_app");
	if($fb_largeur_col_app==""){
		$fb_largeur_col_app=31;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur de la colonne Appréciation des professeurs (<i>4ème colonne</i>) en pourcentage: </td>\n";
	echo "<td><input type='text' name='fb_largeur_col_app' value='$fb_largeur_col_app' /></td>\n";
	echo "</tr>\n";

	$fb_largeur_col_opt=getSettingValue("fb_largeur_col_opt");
	if($fb_largeur_col_opt==""){
		$fb_largeur_col_opt=13;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur des colonnes Note retenue pour Notanet (<i>5ème et 6ème colonnes</i>) en pourcentage: </td>\n";
	echo "<td><input type='text' name='fb_largeur_col_opt' value='$fb_largeur_col_opt' /></td>\n";
	echo "</tr>\n";

	$fb_titrepage=getSettingValue("fb_titrepage");
	if($fb_titrepage==""){
		$fb_titrepage=14;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Taille en points du titre de la page 'Fiche scolaire brevet...': </td>\n";
	echo "<td><input type='text' name='fb_titrepage' value='$fb_titrepage' /></td>\n";
	echo "</tr>\n";

	$fb_titretab=getSettingValue("fb_titretab");
	if($fb_titretab==""){
		$fb_titretab=10;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Taille en points des intitulés de l'entête de page et 'Disciplines', 'Total des points' et 'Décision': </td>\n";
	echo "<td><input type='text' name='fb_titretab' value='$fb_titretab' /></td>\n";
	echo "</tr>\n";

	$fb_tittab_lineheight=getSettingValue("fb_tittab_lineheight");
	if($fb_tittab_lineheight==""){
		$fb_tittab_lineheight=14;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Hauteur de ligne en points des intitulés de l'entête de page et 'Disciplines', 'Total des points' et 'Décision': </td>\n";
	echo "<td><input type='text' name='fb_tittab_lineheight' value='$fb_tittab_lineheight' /></td>\n";
	echo "</tr>\n";

	$fb_textetab=getSettingValue("fb_textetab");
	if($fb_textetab==""){
		$fb_textetab=7;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Taille en points des autres textes: </td>\n";
	echo "<td><input type='text' name='fb_textetab' value='$fb_textetab' /></td>\n";
	echo "</tr>\n";

	$fb_txttab_lineheight=getSettingValue("fb_txttab_lineheight");
	if($fb_txttab_lineheight==""){
		$fb_txttab_lineheight=11;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Hauteur de ligne en points des autres textes: </td>\n";
	echo "<td><input type='text' name='fb_txttab_lineheight' value='$fb_txttab_lineheight' /></td>\n";
	echo "</tr>\n";

	$fb_marg_h=getSettingValue("fb_marg_h");
	if($fb_marg_h==""){
		$fb_marg_h=7;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Marges haute et basse des disciplines en pixels: </td>\n";
	echo "<td><input type='text' name='fb_marg_h' value='$fb_marg_h' /></td>\n";
	echo "</tr>\n";

	$fb_marg_l=getSettingValue("fb_marg_l");
	if($fb_marg_l==""){
		$fb_marg_l=2;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Marges gauche et droite des disciplines en pixels: </td>\n";
	echo "<td><input type='text' name='fb_marg_l' value='$fb_marg_l' /></td>\n";
	echo "</tr>\n";

	$fb_marg_etab=getSettingValue("fb_marg_etab");
	if($fb_marg_etab==""){
		$fb_marg_etab=2;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Marges des infos établissement en pixels: </td>\n";
	echo "<td><input type='text' name='fb_marg_etab' value='$fb_marg_etab' /></td>\n";
	echo "</tr>\n";

	$fb_marg_h_ele=getSettingValue("fb_marg_h_ele");
	if($fb_marg_h_ele==""){
		$fb_marg_h_ele=3;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Marges haute et basses des infos élève en pixels: </td>\n";
	echo "<td><input type='text' name='fb_marg_h_ele' value='$fb_marg_h_ele' /></td>\n";
	echo "</tr>\n";

	echo "</table>\n";
	echo "<p align='center'><input type='submit' name='enregistrer_param' value='Enregistrer' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	function fixe_proportions(mode){
		if(mode=='agri'){
			document.form_param.fb_largeur_col_disc.value='31';
			document.form_param.fb_largeur_col_note.value='8';
			document.form_param.fb_largeur_col_app.value='42';
			document.form_param.fb_largeur_col_opt.value='10';
		}
		else{
			document.form_param.fb_largeur_col_disc.value='24';
			document.form_param.fb_largeur_col_note.value='8';
			document.form_param.fb_largeur_col_app.value='31';
			document.form_param.fb_largeur_col_opt.value='13';
		}
	}
</script>\n";

	echo "<p>Les proportions de largeur des colonnes diffèrent un peu entre les brevets agricoles et les brevets non agricoles.<br />Les liens suivants permettent de fixer les proportions par défaut correspondant à ces deux cas.<br />\n";

	echo "Brevets \n";
	echo "<a href='#' onClick='fixe_proportions(\"non_agri\")'>non agricoles</a>";
	echo " ou \n";
	echo "<a href='#' onClick='fixe_proportions(\"agri\")'>agricoles</a>";
	echo ".</p>\n";

	// FIN DU PARAMETRAGE

	require("../../lib/footer.inc.php");
	die();

}








$sql="SELECT DISTINCT type_brevet FROM notanet_ele_type ORDER BY type_brevet;";
$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune association élève/type de brevet n'a encore été réalisée.<br />Commencez par <a href='../select_eleves.php'>sélectionner les élèves</a></p>\n";

	require("../../lib/footer.inc.php");
	die();
}

$sql="SELECT DISTINCT type_brevet FROM notanet_corresp ORDER BY type_brevet;";
$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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

// Type de brevet:
//$type_brevet = isset($_POST['type_brevet']) ? $_POST['type_brevet'] : NULL;
//$type_brevet=getSettingValue("type_brevet");

/*
if($type_brevet==""){
	echo "<p><b style='color:red'>ERREUR:</b> Commencez par effectuer le <a href='notanet.php'>traitement Notanet</a>.</p>";
	echo "</body>";
	echo "</html>";
	die();
}
*/

// Adresse établissement:
$gepiSchoolName=getSettingValue("gepiSchoolName");
$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1");
$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2");
$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode");
$gepiSchoolCity=getSettingValue("gepiSchoolCity");

/*
function get_classe_from_id($id){
	//$sql="SELECT * FROM classes WHERE id='$id_classe[0]'";
	$sql="SELECT * FROM classes WHERE id='$id'";
	$resultat_classe=mysql_query($sql);
	if(mysql_num_rows($resultat_classe)!=1){
		//echo "<p>ERREUR! La classe d'identifiant '$id_classe[0]' n'a pas pu être identifiée.</p>";
		echo "<p>ERREUR! La classe d'identifiant '$id' n'a pas pu être identifiée.</p>";
	}
	else{
		$ligne_classe=mysql_fetch_object($resultat_classe);
		$classe=$ligne_classe->classe;
		return $classe;
	}
}
*/

$tabmatieres=array();
for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
	$tabmatieres[$j]=array();
}

$tabmatieres=tabmatieres($type_brevet);

/*
if (isset($_GET['parametrer'])) {
	// Paramétrage des tailles de police, dimensions, nom d'académie, de département,...
	echo "<div class='noprint'>\n";
	echo "<p class='bold'>| <a href='../accueil.php'>Accueil</a>";
	echo " | <a href='index.php'>Accueil Notanet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix des classes</a>";
	echo "</p>\n";
	echo "</div>\n";

	echo "<h2>Paramètres d'affichage des Fiches Brevet</h2>\n";

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form_param' method='post'>\n";
	echo "<table border='0'>\n";

	$alt=1;
	$fb_academie=getSettingValue("fb_academie");
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Académie de: </td>\n";
	echo "<td><input type='text' name='fb_academie' value='$fb_academie' /></td>\n";
	echo "</tr>\n";

	$fb_departement=getSettingValue("fb_departement");
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Département de: </td>\n";
	echo "<td><input type='text' name='fb_departement' value='$fb_departement' /></td>\n";
	echo "</tr>\n";

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
	//echo "<tr><td colspan='2'>\$fb_session=$fb_session</td></tr>";
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Session: </td>\n";
	echo "<td><input type='text' name='fb_session' value='$fb_session' /></td>\n";
	echo "</tr>\n";

	// ****************************************************************************
	// MODE DE CALCUL POUR LES MOYENNES DES REGROUPEMENTS DE MATIERES:
	// - LV1: on fait la moyenne de toutes les LV1 (AGL1, ALL1)
	// ou
	// - LV1: on présente pour chaque élève, la moyenne qui correspond à sa LV1: ALL1 s'il fait ALL1,...
	// ****************************************************************************
	$fb_mode_moyenne=getSettingValue("fb_mode_moyenne");
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td valign='top'>Mode de calcul des moyennes pour les options Notanet associées à plusieurs matières (<i>ex.: LV1 associée à AGL1 et ALL1</i>): </td>\n";
	echo "<td>";
		echo "<table border='0'>\n";
		echo "<tr>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='fb_mode_moyenne' value='1' ";
		if($fb_mode_moyenne!="2"){
			echo "checked />";
		}
		else{
			echo "/>";
		}
		echo "</td>\n";
		echo "<td>\n";
		echo "Calculer la moyenne de toutes matières d'une même option Notanet confondues<br />\n";
		echo "(<i>on compte ensemble les AGL1 et ALL1; c'est la moyenne de toute la LV1 qui est effectuée</i>)\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='fb_mode_moyenne' value='2' ";
		if($fb_mode_moyenne=="2"){
			echo "checked />";
		}
		else{
			echo "/>";
		}
		echo "</td>\n";
		echo "<td>\n";
		echo "Calculer les moyennes par matières<br />\n";
		echo "(<i>on ne mélange pas AGL1 et ALL1 dans le calcul de la moyenne de classe pour un élève</i>)\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "</tr>\n";


	$fb_nblig_avis_chef=getSettingValue("fb_nblig_avis_chef");
	if($fb_nblig_avis_chef==""){
		$fb_nblig_avis_chef=4;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Nombre de lignes pour l'avis du chef d'établissement: </td>\n";
	echo "<td><input type='text' name='fb_nblig_avis_chef' value='$fb_nblig_avis_chef' /></td>\n";
	echo "</tr>\n";

	$fb_largeur_col_disc=getSettingValue("fb_largeur_col_disc");
	if($fb_largeur_col_disc==""){
		$fb_largeur_col_disc=24;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur de la colonne DISCIPLINES (<i>1ère colonne</i>) en pourcentage: </td>\n";
	echo "<td><input type='text' name='fb_largeur_col_disc' value='$fb_largeur_col_disc' /></td>\n";
	echo "</tr>\n";

	$fb_largeur_col_note=getSettingValue("fb_largeur_col_note");
	if($fb_largeur_col_note==""){
		$fb_largeur_col_note=8;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur des colonnes Note moyenne de la classe et Note moyenne de l'élève (<i>2ème et 3ème colonnes</i>) en pourcentage: </td>\n";
	echo "<td><input type='text' name='fb_largeur_col_note' value='$fb_largeur_col_note' /></td>\n";
	echo "</tr>\n";

	$fb_largeur_col_app=getSettingValue("fb_largeur_col_app");
	if($fb_largeur_col_app==""){
		$fb_largeur_col_app=31;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur de la colonne Appréciation des professeurs (<i>4ème colonne</i>) en pourcentage: </td>\n";
	echo "<td><input type='text' name='fb_largeur_col_app' value='$fb_largeur_col_app' /></td>\n";
	echo "</tr>\n";

	$fb_largeur_col_opt=getSettingValue("fb_largeur_col_opt");
	if($fb_largeur_col_opt==""){
		$fb_largeur_col_opt=13;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur des colonnes Note retenue pour Notanet (<i>5ème et 6ème colonnes</i>) en pourcentage: </td>\n";
	echo "<td><input type='text' name='fb_largeur_col_opt' value='$fb_largeur_col_opt' /></td>\n";
	echo "</tr>\n";

	$fb_titrepage=getSettingValue("fb_titrepage");
	if($fb_titrepage==""){
		$fb_titrepage=14;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Taille en points du titre de la page 'Fiche scolaire brevet...': </td>\n";
	echo "<td><input type='text' name='fb_titrepage' value='$fb_titrepage' /></td>\n";
	echo "</tr>\n";

	$fb_titretab=getSettingValue("fb_titretab");
	if($fb_titretab==""){
		$fb_titretab=10;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Taille en points des intitulés de l'entête de page et 'Disciplines', 'Total des points' et 'Décision': </td>\n";
	echo "<td><input type='text' name='fb_titretab' value='$fb_titretab' /></td>\n";
	echo "</tr>\n";

	$fb_tittab_lineheight=getSettingValue("fb_tittab_lineheight");
	if($fb_tittab_lineheight==""){
		$fb_tittab_lineheight=14;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Hauteur de ligne en points des intitulés de l'entête de page et 'Disciplines', 'Total des points' et 'Décision': </td>\n";
	echo "<td><input type='text' name='fb_tittab_lineheight' value='$fb_tittab_lineheight' /></td>\n";
	echo "</tr>\n";

	$fb_textetab=getSettingValue("fb_textetab");
	if($fb_textetab==""){
		$fb_textetab=7;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Taille en points des autres textes: </td>\n";
	echo "<td><input type='text' name='fb_textetab' value='$fb_textetab' /></td>\n";
	echo "</tr>\n";

	$fb_txttab_lineheight=getSettingValue("fb_txttab_lineheight");
	if($fb_txttab_lineheight==""){
		$fb_txttab_lineheight=11;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Hauteur de ligne en points des autres textes: </td>\n";
	echo "<td><input type='text' name='fb_txttab_lineheight' value='$fb_txttab_lineheight' /></td>\n";
	echo "</tr>\n";

	$fb_marg_h=getSettingValue("fb_marg_h");
	if($fb_marg_h==""){
		$fb_marg_h=7;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Marges haute et basse des disciplines en pixels: </td>\n";
	echo "<td><input type='text' name='fb_marg_h' value='$fb_marg_h' /></td>\n";
	echo "</tr>\n";

	$fb_marg_l=getSettingValue("fb_marg_l");
	if($fb_marg_l==""){
		$fb_marg_l=2;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Marges gauche et droite des disciplines en pixels: </td>\n";
	echo "<td><input type='text' name='fb_marg_l' value='$fb_marg_l' /></td>\n";
	echo "</tr>\n";

	$fb_marg_etab=getSettingValue("fb_marg_etab");
	if($fb_marg_etab==""){
		$fb_marg_etab=2;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Marges des infos établissement en pixels: </td>\n";
	echo "<td><input type='text' name='fb_marg_etab' value='$fb_marg_etab' /></td>\n";
	echo "</tr>\n";

	$fb_marg_h_ele=getSettingValue("fb_marg_h_ele");
	if($fb_marg_h_ele==""){
		$fb_marg_h_ele=3;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Marges haute et basses des infos élève en pixels: </td>\n";
	echo "<td><input type='text' name='fb_marg_h_ele' value='$fb_marg_h_ele' /></td>\n";
	echo "</tr>\n";

	echo "</table>\n";
	echo "<p align='center'><input type='submit' name='enregistrer_param' value='Enregistrer' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	function fixe_proportions(mode){
		if(mode=='agri'){
			document.form_param.fb_largeur_col_disc.value='31';
			document.form_param.fb_largeur_col_note.value='8';
			document.form_param.fb_largeur_col_app.value='42';
			document.form_param.fb_largeur_col_opt.value='10';
		}
		else{
			document.form_param.fb_largeur_col_disc.value='24';
			document.form_param.fb_largeur_col_note.value='8';
			document.form_param.fb_largeur_col_app.value='31';
			document.form_param.fb_largeur_col_opt.value='13';
		}
	}
</script>\n";

	echo "<p>Les proportions de largeur des colonnes diffèrent un peu entre les brevets agricoles et les brevets non agricoles.<br />Les liens suivants permettent de fixer les proportions par défaut correspondant à ces deux cas.<br />\n";

	echo "Brevets \n";
	echo "<a href='#' onClick='fixe_proportions(\"non_agri\")'>non agricoles</a>";
	echo " ou \n";
	echo "<a href='#' onClick='fixe_proportions(\"agri\")'>agricoles</a>";
	echo ".</p>\n";

	// FIN DU PARAMETRAGE
}
*/
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
	$call_data = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT c.* FROM classes c, periodes p, notanet n,notanet_ele_type net WHERE p.id_classe = c.id AND c.id=n.id_classe AND n.login=net.login ORDER BY classe");
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
		echo "<option value='$ide_classe'>$classe</option>\n";
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
	// DEBUT DE L'AFFICHAGE DES FICHES BREVET POUR LES CLASSES CHOISIES
	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../../accueil.php'>Accueil</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?type_brevet=$type_brevet'>Retour au choix des classes</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?parametrer=y'>Paramètrer</a>";
	echo "</p>\n";
	echo "</div>\n";

	// Saut de page pour ne pas imprimer la page avec l'entête...
	echo "<p>Pour ne pas être gêné par l'entête de la page lors de l'impression, la première page est exclue.<br />Un saut de page est inséré.<br />Veillez donc simplement à ne pas imprimer la première page.</p>\n";
	//echo "<p class='saut'>&nbsp;</p>\n";

	/*
	//echo formate_date("2007-1-24 00:00:00");
	$fb_academie="ROUEN";
	$fb_departement="Eure";

	// En pourcentages:
	$fb_largeur_col_disc="24";
	$fb_largeur_col_note="8";
	$fb_largeur_col_app="31";
	//$fb_largeur_col_notanet="25";
	$fb_largeur_col_opt="13";

	$fb_nblig_avis_chef=4;

	$fb_titrepage="14";
	$fb_titretab="10";
	$fb_tittab_lineheight="14";
	$fb_textetab="7";
	$fb_txttab_lineheight="11";

	// Marges haute et basse des disciplines en pixels
	$fb_marg_h=7;
	// Marges gauche et droite des disciplines en pixels
	$fb_marg_l=2;

	// Marges des infos établissement en pixels
	$fb_marg_etab=2;

	// Marges haute et basses des infos élève en pixels
	$fb_marg_h_ele=3;

	$gepiYear=getSettingValue('gepiYear');
	$fb_session=$gepiYear;
	*/

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

	// Bricolage pour laisser un espace d'à peu près $fb_nblig_avis_chef lignes aux chef d'établissement
	$fb_nblig_avis_chef_em=$fb_nblig_avis_chef*1.7;

	$fb_largeur_col_disc=getSettingValue("fb_largeur_col_disc");
	if($fb_largeur_col_disc==""){
		$fb_largeur_col_disc=24;
	}

	$fb_largeur_col_note=getSettingValue("fb_largeur_col_note");
	if($fb_largeur_col_note==""){
		$fb_largeur_col_note=8;
	}

	$fb_largeur_col_app=getSettingValue("fb_largeur_col_app");
	if($fb_largeur_col_app==""){
		$fb_largeur_col_app=31;
	}

	$fb_largeur_col_opt=getSettingValue("fb_largeur_col_opt");
	if($fb_largeur_col_opt==""){
		$fb_largeur_col_opt=13;
	}

	$fb_titrepage=getSettingValue("fb_titrepage");
	if($fb_titrepage==""){
		$fb_titrepage=14;
	}

	$fb_titretab=getSettingValue("fb_titretab");
	if($fb_titretab==""){
		$fb_titretab=10;
	}

	$fb_tittab_lineheight=getSettingValue("fb_tittab_lineheight");
	if($fb_tittab_lineheight==""){
		$fb_tittab_lineheight=14;
	}

	$fb_textetab=getSettingValue("fb_textetab");
	if($fb_textetab==""){
		$fb_textetab=7;
	}

	$fb_txttab_lineheight=getSettingValue("fb_txttab_lineheight");
	if($fb_txttab_lineheight==""){
		$fb_txttab_lineheight=11;
	}

	$fb_marg_h=getSettingValue("fb_marg_h");
	if($fb_marg_h==""){
		$fb_marg_h=7;
	}

	$fb_marg_l=getSettingValue("fb_marg_l");
	if($fb_marg_l==""){
		$fb_marg_l=2;
	}

	$fb_marg_etab=getSettingValue("fb_marg_etab");
	if($fb_marg_etab==""){
		$fb_marg_etab=2;
	}

	$fb_marg_h_ele=getSettingValue("fb_marg_h_ele");
	if($fb_marg_h_ele==""){
		$fb_marg_h_ele=3;
	}


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
		font-weight: bold;
	}

	.div_etab{
		display: block;
		text-align: left;
		/*border: 1px solid black;*/
		margin: ".$fb_marg_etab."px;
		float: left;
	}

	.info_ele{
		margin: ".$fb_marg_h_ele."px 0 ".$fb_marg_h_ele."px 0;
	}
</style>\n";

	$ele_lieu_naissance=getSettingValue("ele_lieu_naissance") ? getSettingValue("ele_lieu_naissance") : "n";

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
				// Dans la table 'notanet', matiere='PREMIERE LANGUE VIVANTE'
				//                       et mat='AGL1'
				//                       ou mat='ALL1'
				// ... avec une seule ligne/enregistrement par élève pour la matière (aucun élève ne suit à la fois ALL1 et AGL1)
				// Dans la table 'notanet_corresp', notanet_mat='PREMIERE LANGUE VIVANTE'
				//                       et matiere='AGL1'
				//                       ou matiere='ALL1'
				// ... avec plusieurs lignes/enregistrements pour une même notanet_mat
				//$sql="SELECT ROUND(AVG(note),1) moyenne FROM notanet WHERE note!='DI' AND note!='AB' AND note!='NN' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
				$sql="SELECT ROUND(AVG(note),1) moyenne FROM notanet WHERE note!='DI' AND note!='AB' AND note!='NN' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
				//$sql="SELECT ROUND(AVG(note),1) moyenne FROM notanet n,notanet_ele_type net WHERE n.note!='DI' AND n.note!='AB' AND n.note!='NN' AND n.id_classe='$id_classe[$i]' AND n.matiere='".$tabmatieres[$j][0]."' AND n.login=net.login AND net.type_brevet='$type_brevet';";
				//echo "$sql<br />";
				$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_moy)>0){
					$lig_moy=mysqli_fetch_object($res_moy);
					$moy_classe[$j]=$lig_moy->moyenne;
					//echo "\$moy_classe[$j]=$moy_classe[$j]<br />";
					// Là on fait la moyenne de l'ALL1 et de l'AGL1 ensemble car one ne fait pas la différence:
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
				$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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



		//$sql="SELECT DISTINCT login FROM notanet WHERE id_classe='$id_classe[$i]' ORDER BY login";
		//$sql="SELECT DISTINCT e.* FROM eleves e, notanet n WHERE n.id_classe='$id_classe[$i]' AND n.login=e.login ORDER BY e.login";
		$sql="SELECT DISTINCT e.* FROM eleves e,
										notanet n,
										notanet_ele_type net
								WHERE n.id_classe='$id_classe[$i]' AND
										n.login=e.login AND
										net.login=n.login
								ORDER BY e.login;";
		$res1=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res1)>0){
			// Boucle sur la liste des élèves
			while($lig1=mysqli_fetch_object($res1)){
				//echo "$lig1->login<br />\n";


				//=======================================
				echo "<table border='0' width='100%'>\n";
				echo "<tr>\n";
				echo "<td valign='top' width='50%' align='left' style='font-weight:bold; font-size:".$fb_titretab."pt'>\n";
				// Mettre une page de saisie...
				echo "ACADEMIE DE $fb_academie<br />\n";
				// Mettre une page de saisie...
				echo "Département: $fb_departement\n";
				echo "</td>\n";
				echo "<td valign='top' width='50%' align='right' style='font-weight:bold; font-size:".$fb_titretab."pt'>\n";
				echo "Session: $fb_session";
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";

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
				if($ele_lieu_naissance=="y") {echo get_commune($lig1->lieu_naissance,1);}
				echo "</p>\n";
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";

				//=======================================
				//echo "<div class='fb'>\n";
				echo "<table class='fb' width='100%'>\n";
				echo "<tr>\n";
				if(($type_brevet==4)||($type_brevet==7)){
					echo "<td colspan='5' align='left'>\n";
				}
				else{
					echo "<td colspan='6' align='left'>\n";
				}
					echo "<div class='div_etab'>\n";
					echo "<b>Etablissement fréquenté:</b> ";
					echo "</div>\n";
					echo "<div class='div_etab'>\n";
					//echo $gepiSchoolName."<br />\n".$gepiSchoolAdress1."<br />\n".$gepiSchoolAdress2."<br />\n".$gepiSchoolZipCode." ".$gepiSchoolCity;
					echo $gepiSchoolName."<br />\n".$gepiSchoolAdress1;
					if($gepiSchoolAdress1!=""){echo ", ";}
					echo $gepiSchoolAdress2;
					if($gepiSchoolAdress2!=""){echo ", ";}
					echo $gepiSchoolZipCode." ".$gepiSchoolCity;
					echo "</div>\n";
				echo "</td>\n";
				echo "</tr>\n";

				//=====================

				if(($type_brevet==0)||($type_brevet==1)||($type_brevet==5)||($type_brevet==6)){
					// Brevets série COLLEGE avec LV2 ou avec DP6
					// Brevets série TECHNOLOGIQUE avec ou sans DP6
					echo "<tr>\n";

					echo "<td rowspan='4' width='".$fb_largeur_col_disc."%' style='font-weight:bold; font-size:".$fb_titretab."pt;'>\n";
					echo "DISCIPLINES";
					echo "</td>\n";

					echo "<td colspan='3' style='border: 1px solid black; text-align:center; font-weight:bold;'>\n";
					echo "Classe de 3ème de collège";
					echo "</td>\n";

					echo "<td rowspan='2' colspan='2' style='font-weight:bold; line-height: ".$fb_txttab_lineheight."pt;'>\n";
					echo "Note Globale affectée du coefficient";
					echo "</td>\n";

					echo "</tr>\n";
					//=====================
					echo "<tr>\n";

					// La colonne discipline est dans le rowspan de la ligne précédente.

					echo "<td rowspan='3' width='".$fb_largeur_col_note."%' style='font-weight:bold; line-height: ".$fb_txttab_lineheight."pt;'>\n";
					echo "Note<br />moyenne<br />de la<br />classe<br />0 à 20";
					echo "</td>\n";

					//echo "<td style='border: 1px solid black; text-align:center;'>\n";
					echo "<td rowspan='3' width='".$fb_largeur_col_note."%' style='font-weight:bold; line-height: ".$fb_txttab_lineheight."pt;'>\n";
					echo "Note<br />moyenne<br />de<br />l'élève<br />0 à 20";
					echo "</td>\n";

					//echo "<td style='border: 1px solid black; text-align:center;'>\n";
					echo "<td rowspan='3' width='".$fb_largeur_col_app."%' style='font-weight:bold;'>\n";
					echo "Appréciations des professeurs";
					echo "</td>\n";

					echo "</tr>\n";

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
							//if(($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')){
							if(($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')&&($tabmatieres[$j]['socle']=='n')){

								//$tabmatieres[$j]['fb_col'][1]
								//$SUR_TOTAL+=$tabmatieres[$j][-2]*20;
								//echo "<tr><td>".$tabmatieres[$j]['fb_col'][1]."</td></tr>";
								//if(ctype_digit($tabmatieres[$j]['fb_col'][1])){$SUR_TOTAL[1]+=$tabmatieres[$j]['fb_col'][1];}
								//if(ctype_digit($tabmatieres[$j]['fb_col'][2])){$SUR_TOTAL[2]+=$tabmatieres[$j]['fb_col'][2];}

								// ************************************
								// A REVOIR
								// PROBLEME AVEC CES TOTAUX: SI UN ELEVE EST AB, DI ou NN, IL NE FAUDRAIT PAS AUGMENTER???...
								if((mb_strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][1]))==0)&&($tabmatieres[$j][-1]!='PTSUP')){
									$SUR_TOTAL[1]+=$tabmatieres[$j]['fb_col'][1];
								}
								if((mb_strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][2]))==0)&&($tabmatieres[$j][-1]!='PTSUP')){
									$SUR_TOTAL[2]+=$tabmatieres[$j]['fb_col'][2];
								}
								// ************************************

								//echo "<tr><td>$SUR_TOTAL[1]</td></tr>";

								echo "<tr>\n";

								// Discipline
								//echo "<td style='border: 1px solid black; text-align:left;'>\n";
								echo "<td";
								if($tabmatieres[$j][-1]=='PTSUP'){
									echo " rowspan='2'";
								}
								//echo " class='fb'";
								echo ">\n";
								echo "<p class='discipline fb'>";
								//echo "<span class='discipline'>";

								if(!isset($tabmatieres[$j]["lig_speciale"])){
									echo ucfirst(accent_min(mb_strtolower($tabmatieres[$j][0])));
									//if($tabmatieres[$j][0]=="OPTION FACULTATIVE (1)"){echo ": ".$tabmatieres[$j][-5];}
									if($tabmatieres[$j][0]=="OPTION FACULTATIVE (1)"){

										// ==============================
										// recherche de la matière facultative pour l'élève
										//$sql_mat_fac="SELECT mat FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
										$sql_mat_fac="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
										$res_mat_fac=mysqli_query($GLOBALS["___mysqli_ston"], $sql_mat_fac);
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
									echo ucfirst(accent_min(mb_strtolower($tabmatieres[$j]["lig_speciale"])));
								}
								//echo "</span>\n";
								echo "</p>\n";
								echo "</td>\n";

								// Moyenne classe
								echo "<td ";
								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "rowspan='2' ";
								}
								echo " class='fb' ";
								echo "style='border: 1px solid black; text-align:center;'>\n";
								if($fb_mode_moyenne==1){
									echo $moy_classe[$j];
								}
								else{
									//$sql="SELECT mat FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
									$sql="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
									if(mysqli_num_rows($res_mat)>0){
										$lig_mat=mysqli_fetch_object($res_mat);
										//echo "$lig_mat->mat: ";

										//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
										$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
										//echo "$sql<br />";
										$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
								echo " class='fb' ";
								echo "style='border: 1px solid black; text-align:center;'>\n";
								//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
								$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
								$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
								echo " class='fb' ";
								//echo "style='border: 1px solid black; text-align:center;'>\n";
								echo "style='border: 1px solid black; text-align:left;'>\n";

								if($avec_app=="y") {
									$sql="SELECT appreciation FROM notanet_app na,
																	notanet_corresp nc
															 WHERE na.login='$lig1->login' AND
																	nc.notanet_mat='".$tabmatieres[$j][0]."' AND
																	nc.matiere=na.matiere;";
									//echo "$sql<br />";
									$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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



								// EXTRACTION POUR LA(ES) COLONNE(S) DE DROITE
								$valeur_tmp="&nbsp;";
								//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
								$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
								$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
								if(mysqli_num_rows($res_note)){
									$lig_note=mysqli_fetch_object($res_note);
									//echo "$lig_note->note";
									//$valeur_tmp="$lig_note->note";
									//$valeur_tmp=$lig_note->note*$tabmatieres[$j][-2];
									if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
										$valeur_tmp=$lig_note->note*$tabmatieres[$j][-2];
										/*
										if($tabmatieres[$j][-1]=='PTSUP'){
											$TOTAL+=max(0,$lig_note->note-10);
										}
										else{
										*/
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
											if($num_fb_col==1){
												$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
											}
											else{
												$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
											}
										}
									}
									//$note="$lig_note->note";
								}
								else{
									// FAUT-IL UN TEMOIN POUR DECREMENTER LE SUR_TOTAL ?
									if(($tabmatieres[$j][-1]!='PTSUP')){
										if($num_fb_col==1){
											$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
										}
										else{
											$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
										}
									}
								}





								echo "<td ";
								echo " class='fb' ";
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
									if($tabmatieres[$j]['fb_col'][1]!="X"){
										if(($temoin_note_non_numerique=="n")||($num_fb_col==2)) {
											echo " / ".$tabmatieres[$j]['fb_col'][1];
										}
									}


									// DEBUG:
									// echo "<br />$TOTAL/$SUR_TOTAL[1]";


								}
								echo "</td>\n";


								//echo "style='border: 1px solid black; text-align:center;'>\n";
								echo "<td ";
								echo " class='fb' ";
								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "style='border: 1px solid black; text-align:center;'>\n";
									echo "<b>Points > à 10</b>";
								}
								else{
									/*
									echo "style='border: 1px solid black; text-align:right;'>\n";
									// TRICHE... Mon dispositif ne permet pas de gérer correctement ce double affichage
									// Il faudrait /40 pour la 2è LV ou découverte professionnelle 6H.
									if($tabmatieres[$j][0]=='DEUXIEME LANGUE VIVANTE'){
										echo " / 40";
									}
									else{
										echo " / 20";
									}
									*/
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
									if(($temoin_note_non_numerique=="n")||($num_fb_col==1)) {
										echo " / ".$tabmatieres[$j]['fb_col'][2];
									}
								}
								echo "</td>\n";

								echo "</tr>\n";

								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "<tr>\n";
									//echo "<td style='border: 1px solid black; text-align:right;'>\n";

									$valeur_tmp="";
									//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
									$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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


									//echo "<td style='border: 1px solid black; text-align:center;'>\n";
									/*
									$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
									$res_note=mysql_query($sql);
									if(mysql_num_rows($res_note)){
										$lig_note=mysql_fetch_object($res_note);
										//echo "$lig_note->note";
										//$note="$lig_note->note";
										$ptsup=$lig_note->note-10;
										if($ptsup>0){
											echo "$ptsup";
											if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
												$TOTAL+=$ptsup;
											}
										}
										else{
											echo "0";
										}
									}
									else{
										echo "&nbsp;";
										//$note="&nbsp;";
									}
									*/
									if($num_fb_col==1){
										echo "<td ";
										echo " class='fb' ";
										echo "style='border: 1px solid black; text-align:center;'>\n";
										echo "$valeur_tmp";
									}
									else{
										echo "<td ";
										echo " class='fb' ";
										echo "style='border: 1px solid black; text-align:right;'>\n";
										echo "&nbsp;";
										//$note="&nbsp;";
									}

									// DEBUG:
									//echo "<br />$TOTAL/$SUR_TOTAL[1]";

									echo "</td>\n";



									//echo "<td>\n";
									//echo "&nbsp;";
									if($num_fb_col==2){
										echo "<td ";
										echo " class='fb' ";
										echo "style='border: 1px solid black; text-align:center;'>\n";
										echo "$valeur_tmp";
									}
									else{
										echo "<td ";
										echo " class='fb' ";
										echo "style='border: 1px solid black; text-align:right;'>\n";
										echo "&nbsp;";
										//$note="&nbsp;";
									}
									echo "</td>\n";

									echo "</tr>\n";
								}
							}
							//else{
							elseif($tabmatieres[$j]['socle']=='n'){
								//if($tabmatieres[$j][-4]!='non dispensee dans l etablissement'){
								if($tabmatieres[$j][-1]=="NOTNONCA"){
									$temoin_NOTNONCA++;
									//echo "<!-- \$temoin_NOTNONCA=$temoin_NOTNONCA \n\$tabmatieres[$j][0]=".$tabmatieres[$j][0]."-->\n";
								}
								//}
							}
							// ...=====...($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')

						}
						else{
							// $tabmatieres[$j][0]==0, mais il faut quand même afficher la ligne:
							// CAS PARTICULIER DE LA LIGNE DECOUVERTE PROFESSIONNELLE INUTILE MAIS PRESENTE POUR LES SERIES TECHNOLOGIQUE SANS DP6 ET PROFESSIONNELLE SANS DP6

							if(isset($tabmatieres[$j]["lig_speciale"])) {
								echo "<tr>\n";

								if(mb_strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][2]))==0){
									$SUR_TOTAL[2]+=$tabmatieres[$j]['fb_col'][2];
								}

								// Discipline
								echo "<td style='border: 1px solid black; text-align:left;'>\n";
								echo "<p class='discipline fb'>";
								echo ucfirst(mb_strtolower($tabmatieres[$j]["lig_speciale"]));
								echo "</p>\n";
								echo "</td>\n";

								// Moyenne classe
								echo "<td style='border: 1px solid black; text-align:center;'>\n";
								echo "&nbsp;";
								echo "</td>\n";

								// Moyenne élève
								echo "<td style='border: 1px solid black; text-align:center;'>\n";
								echo "&nbsp;";
								echo "</td>\n";

								// Appréciation
								echo "<td ";
								echo " class='fb' ";
								echo "style='border: 1px solid black; text-align:left;'>\n";
								//echo "&nbsp;";
								//echo "</td>\n";
								//echo "style='border: 1px solid black; text-align:left;'>\n";

								if($avec_app=="y") {
									$sql="SELECT appreciation FROM notanet_app na,
																	notanet_corresp nc
															 WHERE na.login='$lig1->login' AND
																	nc.notanet_mat='".$tabmatieres[$j][0]."' AND
																	nc.matiere=na.matiere;";
									//echo "$sql<br />";
									$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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


								echo "<td style='border: 1px solid black; text-align:center; background-color: gray;'>\n";
								echo "&nbsp;";
								echo "</td>\n";

								echo "<td ";
								echo " class='fb' ";
								echo "style='border: 1px solid black; text-align:right;'>\n";
								echo "&nbsp;";
								echo " / ".$tabmatieres[$j]['fb_col'][2];
								echo "</td>\n";

								echo "</tr>\n";

							}
							// Fin du isset($tabmatieres[$j]["lig_speciale"])
						}
					}
					// FIN DE ...
					//=====================



					//=====================
					// SOCLES B2I ET A2
					include("b2i_a2.inc.php");
					//=====================


					//=====================

					if($temoin_NOTNONCA>0){
						// ON TRAITE LES MATIERES NOTNONCA
						echo "<tr>\n";

						echo "<td colspan='4' ";
						echo " class='fb' ";
						echo "style='border: 1px solid black; text-align:center; font-weight:bold;'>\n";
						echo "A titre indicatif";
						echo "</td>\n";

						echo "<td style='font-weight:bold; font-size:".$fb_titretab."pt; line-height: ".$fb_tittab_lineheight."pt;'>\n";
						echo "TOTAL DES POINTS";
						echo "</td>\n";

						echo "<td style='font-weight:bold; font-size:".$fb_titretab."pt; line-height: ".$fb_tittab_lineheight."pt;'>\n";
						echo "TOTAL DES POINTS";
						echo "</td>\n";

						echo "</tr>\n";

						$num_lig=0;
						// On repasse en revue toutes les matières en ne retenant que celles qui sont NOTNONCA
						for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
							//if($tabmatieres[$j][0]!=''){
							//if(($tabmatieres[$j][0]!='')&&($tabmatieres[$j][-1]=='NOTNONCA')){
							if(($tabmatieres[$j][0]!='')&&($tabmatieres[$j][-1]=='NOTNONCA')&&($tabmatieres[$j]['socle']=='n')){
								if($tabmatieres[$j][-4]!='non dispensee dans l etablissement'){
									echo "<tr>\n";

									echo "<td style='border: 1px solid black; text-align:left;'>\n";
									echo "<p class='discipline fb'>";
									echo ucfirst(accent_min(mb_strtolower($tabmatieres[$j][0])));
									echo "</p>";
									echo "</td>\n";

									// Moyenne de la classe
									echo "<td ";
									echo " class='fb' ";
									echo "style='border: 1px solid black; text-align:center;'>\n";
									//echo $moy_classe[$j];
									if($fb_mode_moyenne==1){
										echo $moy_classe[$j];
									}
									else{
										//$sql="SELECT mat FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
										$sql="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
										$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
										if(mysqli_num_rows($res_mat)>0){
											$lig_mat=mysqli_fetch_object($res_mat);
											//echo "$lig_mat->mat: ";

											//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
											//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."';";
											$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
											//echo "$sql<br />";
											$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
											if(mysqli_num_rows($res_moy)>0){
												$lig_moy=mysqli_fetch_object($res_moy);
												echo "$lig_moy->moyenne_mat";
											}
										}
									}
									echo "</td>\n";

									// Moyenne de l'élève
									echo "<td ";
									echo " class='fb' ";
									echo "style='border: 1px solid black; text-align:center;'>\n";
									//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
									$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
									echo " class='fb' ";
									echo "style='border: 1px solid black; text-align:left;'>\n";
									//echo "&nbsp;";
									//echo "</td>\n";
									//echo "style='border: 1px solid black; text-align:left;'>\n";

									if($avec_app=="y") {
										$sql="SELECT appreciation FROM notanet_app na,
																		notanet_corresp nc
																WHERE na.login='$lig1->login' AND
																		nc.notanet_mat='".$tabmatieres[$j][0]."' AND
																		nc.matiere=na.matiere;";
										//echo "$sql<br />";
										$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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

									// Colonne total des lignes calculées (non NOTNONCA)...
									if($num_lig==0){
										$nb_info=$temoin_NOTNONCA;


										//echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:right;'>\n";
										if($num_fb_col==1){
											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:center;'>\n";
											//echo "$TOTAL / 220";
											echo "$TOTAL";
											echo " / ".$SUR_TOTAL[1];
											echo "</td>\n";

											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:right;'>\n";
											//echo "/ 240";
											echo " / ".$SUR_TOTAL[2];
											echo "</td>\n";
										}
										else{
											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:right;'>\n";
											//echo "/ 220";
											echo " / ".$SUR_TOTAL[1];
											echo "</td>\n";

											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:center;'>\n";
											//echo "$TOTAL / 220";
											echo "$TOTAL";
											echo " / ".$SUR_TOTAL[2];
											echo "</td>\n";
										}
										//echo "</td>\n";

										//$num_lig++;
									}
									$num_lig++;

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
									echo "<p class='discipline fb'>";
									echo ucfirst(mb_strtolower($tabmatieres[$j][0]));
									echo "</p>";
									echo "</td>\n";

									echo "<td ";
									echo " class='fb' ";
									echo "style='border: 1px solid black; text-align:center;'>\n";
									echo "&nbsp;";
									echo "</td>\n";

									echo "<td ";
									echo " class='fb' ";
									echo "style='border: 1px solid black; text-align:center;'>\n";
									echo "&nbsp;";
									echo "</td>\n";

									echo "<td ";
									echo " class='fb' ";
									echo "style='border: 1px solid black; text-align:center;'>\n";
									echo "&nbsp;";
									echo "</td>\n";

									if($num_lig==0){
										$nb_info=$temoin_NOTNONCA;

										/*
										//echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:right;'>\n";
										if($num_fb_col==1){
											echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:center;'>\n";
											echo "$TOTAL / 220";
											echo "</td>\n";

											echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:right;'>\n";
											echo "/ 240";
											echo "</td>\n";
										}
										else{
											echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:right;'>\n";
											echo "/ 220";
											echo "</td>\n";

											echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:center;'>\n";
											echo "$TOTAL / 240";
											echo "</td>\n";
										}
										*/
										//echo "</td>\n";
										if($num_fb_col==1){
											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:center;'>\n";
											//echo "$TOTAL / 220";
											echo "$TOTAL";
											echo " / ".$SUR_TOTAL[1];
											echo "</td>\n";

											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:right;'>\n";
											//echo "/ 240";
											echo " / ".$SUR_TOTAL[2];
											echo "</td>\n";
										}
										else{
											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:right;'>\n";
											//echo "/ 220";
											echo " / ".$SUR_TOTAL[1];
											echo "</td>\n";

											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:center;'>\n";
											//echo "$TOTAL / 220";
											echo "$TOTAL";
											echo " / ".$SUR_TOTAL[2];
											echo "</td>\n";
										}

										//$num_lig++;
									}
									$num_lig++;

									echo "</tr>\n";
								}
							}
						}
						// FIN DE LA BOUCLE SUR LA LISTE DES MATIERES
					}
					// FIN DU TRAITEMENT DES MATIERES NOTNONCA

					// ET SINON??? ON N'AFFICHE PAS LE TOTAL??? A REVOIR...






/*
					echo "<tr>\n";
					echo "<td colspan='4' rowspan='2' valign='top' style='border: 1px solid black; text-align:left;'>\n";
					echo "<b>Avis et signature du chef d'établissement:</b>";
					for($k=0;$k<=$fb_nblig_avis_chef;$k++){
						echo "<br />\n";
					}
					echo "</td>\n";
					echo "<td colspan='2' style='font-weight:bold; font-size:".$fb_titretab."pt;'>\n";
					echo "DECISION";
					echo "</td>\n";
					echo "</tr>\n";

					echo "<tr>\n";
					echo "<td style='border: 1px solid black; text-align:center;'>\n";
					echo "&nbsp;";
					echo "</td>\n";
					echo "<td style='border: 1px solid black; text-align:center;'>\n";
					echo "&nbsp;";
					echo "</td>\n";
					echo "</tr>\n";

					echo "</table>\n";
					//echo "</div>\n";
					//=======================================
					//=======================================
					//=======================================
					//=======================================

					//echo "<hr />\n";
					echo "<p class='saut'>&nbsp;</p>\n";
*/
				}
				// ************************************************************************************************
				// ************************************************************************************************
				// ************************************************************************************************
				// AUTRE TYPE DE BREVET
				elseif(($type_brevet==2)||($type_brevet==3)){
					// Brevet série PROFESSIONNELLE avec ou sans DP6

					//echo "<tr><td>A FAIRE... avec la difficulté de regrouper les lignes LV1 et PHYSIQUE dans une seule ligne.</td></tr>";
					echo "<tr>\n";

					echo "<td rowspan='4' width='".$fb_largeur_col_disc."%' style='font-weight:bold; font-size:".$fb_titretab."pt;'>\n";
					echo "DISCIPLINES";
					echo "</td>\n";

					echo "<td colspan='3' style='border: 1px solid black; text-align:center; font-weight:bold;'>\n";
					echo "Classe de 3ème de collège";
					echo "</td>\n";

					echo "<td rowspan='2' colspan='2' style='font-weight:bold; line-height: ".$fb_txttab_lineheight."pt;'>\n";
					echo "Note Globale affectée du coefficient";
					echo "</td>\n";

					echo "</tr>\n";
					//=====================
					echo "<tr>\n";

					// La colonne discipline est dans le rowspan de la ligne précédente.

					echo "<td rowspan='3' width='".$fb_largeur_col_note."%' style='font-weight:bold; line-height: ".$fb_txttab_lineheight."pt;'>\n";
					echo "Note<br />moyenne<br />de la<br />classe<br />0 à 20";
					echo "</td>\n";

					//echo "<td style='border: 1px solid black; text-align:center;'>\n";
					echo "<td rowspan='3' width='".$fb_largeur_col_note."%' style='font-weight:bold; line-height: ".$fb_txttab_lineheight."pt;'>\n";
					echo "Note<br />moyenne<br />de<br />l'élève<br />0 à 20";
					echo "</td>\n";

					//echo "<td style='border: 1px solid black; text-align:center;'>\n";
					echo "<td rowspan='3' width='".$fb_largeur_col_app."%' style='font-weight:bold;'>\n";
					echo "Appréciations des professeurs";
					echo "</td>\n";

					echo "</tr>\n";

					//=====================
					echo "<tr>\n";

					//echo "<td style='border: 1px solid black; text-align:center;'>\n";
					echo "<td colspan='2' ";
					echo " class='fb' ";
					echo "style='font-weight:bold;'>\n";
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
					echo "<td ";
					echo " class='fb' ";
					echo "width='".$fb_largeur_col_opt."%' style='font-weight:bold;'>\n";
					//echo "LV2";
					//echo $fb_intitule_col[1];
					echo $tabmatieres["fb_intitule_col"][1];
					echo "</td>\n";

					echo "<td ";
					echo " class='fb' ";
					echo "width='".$fb_largeur_col_opt."%' style='font-weight:bold; line-height: ".$fb_txttab_lineheight."pt;'>\n";
					//echo "A module découverte professionnelle<br />6 heures";
					//echo $fb_intitule_col[2];
					echo $tabmatieres["fb_intitule_col"][2];
					echo "</td>\n";

					echo "</tr>\n";

					//=====================

					$TOTAL=0;
					$SUR_TOTAL=array();
					$SUR_TOTAL[1]=0;
					$SUR_TOTAL[2]=0;
					$temoin_NOTNONCA=0;

					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					// FRANCAIS ET MATHS
					//for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
					for($j=101;$j<=102;$j++){
						$temoin_note_non_numerique="n";
						//if($tabmatieres[$j][0]!=''){
						if($tabmatieres[$j][0]!=''){
							//if($tabmatieres[$j][-1]!='NOTNONCA'){
							//if(($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')){
							if(($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')&&($tabmatieres[$j]['socle']=='n')){

								
								// ************************************
								// A REVOIR
								// PROBLEME AVEC CES TOTAUX: SI UN ELEVE EST AB, DI ou NN, IL NE FAUDRAIT PAS AUGMENTER???...
								if((mb_strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][1]))==0)&&($tabmatieres[$j][-1]!='PTSUP')){
									$SUR_TOTAL[1]+=$tabmatieres[$j]['fb_col'][1];
								}
								if((mb_strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][2]))==0)&&($tabmatieres[$j][-1]!='PTSUP')){
									$SUR_TOTAL[2]+=$tabmatieres[$j]['fb_col'][2];
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
								echo "<p class='discipline fb'>";
								//echo "<span class='discipline'>";
								if(!isset($tabmatieres[$j]["lig_speciale"])){
									echo ucfirst(mb_strtolower($tabmatieres[$j][0]));
									//if($tabmatieres[$j][0]=="OPTION FACULTATIVE (1)"){echo ": ".$tabmatieres[$j][-5];}
									if($tabmatieres[$j][0]=="OPTION FACULTATIVE (1)"){

										// ==============================
										// recherche de la matière facultative pour l'élève
										//$sql_mat_fac="SELECT mat FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
										$sql_mat_fac="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
										$res_mat_fac=mysqli_query($GLOBALS["___mysqli_ston"], $sql_mat_fac);
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
								echo " class='fb' ";
								echo "style='border: 1px solid black; text-align:center;'>\n";
								if($fb_mode_moyenne==1){
									echo $moy_classe[$j];
								}
								else{
									//$sql="SELECT mat FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
									$sql="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
									if(mysqli_num_rows($res_mat)>0){
										$lig_mat=mysqli_fetch_object($res_mat);
										//echo "$lig_mat->mat: ";

										//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
										$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
										//echo "$sql<br />";
										$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
								echo " class='fb' ";
								echo "style='border: 1px solid black; text-align:center;'>\n";
								//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
								$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
								$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
								echo " class='fb' ";
								echo "style='border: 1px solid black; text-align:left;'>\n";
								//echo "&nbsp;";
								//echo "DEBUG: $TOTAL";
								//echo "</td>\n";
								//echo "style='border: 1px solid black; text-align:left;'>\n";

								if($avec_app=="y") {
									$sql="SELECT appreciation FROM notanet_app na,
																	notanet_corresp nc
															 WHERE na.login='$lig1->login' AND
																	nc.notanet_mat='".$tabmatieres[$j][0]."' AND
																	nc.matiere=na.matiere;";
									//echo "$sql<br />";
									$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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


								/*
								// CALCUL POUR LES COLONNES DE DROITE
								$valeur_tmp="&nbsp;";
								$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
								$res_note=mysql_query($sql);
								if(mysql_num_rows($res_note)){
									$lig_note=mysql_fetch_object($res_note);
									//echo "$lig_note->note";
									//$valeur_tmp="$lig_note->note";
									$valeur_tmp=$lig_note->note*$tabmatieres[$j][-2];
									if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
										//$TOTAL+=$lig_note->note;
										// Le cas PTSUP est calculé plus loin
										if($tabmatieres[$j][-1]!='PTSUP'){
											$TOTAL+=$lig_note->note;
										}
									}
									//$note="$lig_note->note";
								}
								*/

								// EXTRACTION POUR LA(ES) COLONNE(S) DE DROITE
								$valeur_tmp="&nbsp;";
								//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
								$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
								$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
											if($num_fb_col==1){
												$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
											}
											else{
												$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
											}
										}
									}
								}
								else{
									// FAUT-IL UN TEMOIN POUR DECREMENTER LE SUR_TOTAL ?
									if(($tabmatieres[$j][-1]!='PTSUP')){
										if($num_fb_col==1){
											$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
										}
										else{
											$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
										}
									}
								}






								echo "<td ";
								echo " class='fb' ";
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
									if($tabmatieres[$j]['fb_col'][1]!="X"){
										if(($temoin_note_non_numerique=="n")||($num_fb_col==2)) {
											echo " / ".$tabmatieres[$j]['fb_col'][1];
										}
									}
								}
								echo "</td>\n";


								//echo "style='border: 1px solid black; text-align:center;'>\n";
								echo "<td ";
								echo " class='fb' ";
								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "style='border: 1px solid black; text-align:center;'>\n";
									echo "<b>Points > à 10</b>";
								}
								else{
									/*
									echo "style='border: 1px solid black; text-align:right;'>\n";
									// TRICHE... Mon dispositif ne permet pas de gérer correctement ce double affichage
									// Il faudrait /40 pour la 2è LV ou découverte professionnelle 6H.
									if($tabmatieres[$j][0]=='DEUXIEME LANGUE VIVANTE'){
										echo " / 40";
									}
									else{
										echo " / 20";
									}
									*/
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
									if(($temoin_note_non_numerique=="n")||($num_fb_col==1)) {
										echo " / ".$tabmatieres[$j]['fb_col'][2];
									}
								}
								echo "</td>\n";

								echo "</tr>\n";

								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "<tr>\n";
									//echo "<td style='border: 1px solid black; text-align:right;'>\n";

									$valeur_tmp="";
									//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
									$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
									if(mysqli_num_rows($res_note)){
										$lig_note=mysqli_fetch_object($res_note);
										//echo "$lig_note->note";
										//$note="$lig_note->note";
										$ptsup=$lig_note->note-10;
										if($ptsup>0){
											//echo "$ptsup";
											$valeur_tmp=$ptsup;
											if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
												$TOTAL+=$ptsup;
											}
										}
										else{
											//echo "0";
											$valeur_tmp=0;
										}
									}
									else{
										//echo "&nbsp;";
										$valeur_tmp="&nbsp;";
										//$note="&nbsp;";
									}


									//echo "<td style='border: 1px solid black; text-align:center;'>\n";
									/*
									$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
									$res_note=mysql_query($sql);
									if(mysql_num_rows($res_note)){
										$lig_note=mysql_fetch_object($res_note);
										//echo "$lig_note->note";
										//$note="$lig_note->note";
										$ptsup=$lig_note->note-10;
										if($ptsup>0){
											echo "$ptsup";
											if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
												$TOTAL+=$ptsup;
											}
										}
										else{
											echo "0";
										}
									}
									else{
										echo "&nbsp;";
										//$note="&nbsp;";
									}
									*/
									if($num_fb_col==1){
										echo "<td ";
										echo " class='fb' ";
										echo "style='border: 1px solid black; text-align:center;'>\n";
										echo "$valeur_tmp";
									}
									else{
										echo "<td ";
										echo " class='fb' ";
										echo "style='border: 1px solid black; text-align:right;'>\n";
										echo "&nbsp;";
										//$note="&nbsp;";
									}
									echo "</td>\n";



									//echo "<td>\n";
									//echo "&nbsp;";
									if($num_fb_col==2){
										echo "<td ";
										echo " class='fb' ";
										echo "style='border: 1px solid black; text-align:center;'>\n";
										echo "$valeur_tmp";
									}
									else{
										echo "<td ";
										echo " class='fb' ";
										echo "style='border: 1px solid black; text-align:right;'>\n";
										echo "&nbsp;";
										//$note="&nbsp;";
									}
									echo "</td>\n";

									echo "</tr>\n";
								}
							}
							//else{
							elseif($tabmatieres[$j]['socle']=='n') {
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
						else{
							// $tabmatieres[$j][0]==0, mais il faut quand même afficher la ligne:
							// CAS PARTICULIER DE LA LIGNE DECOUVERTE PROFESSIONNELLE INUTILE MAIS PRESENTE POUR LES SERIES TECHNOLOGIQUE SANS DP6 ET PROFESSIONNELLE SANS DP6

							if(isset($tabmatieres[$j]["lig_speciale"])) {
								echo "<tr>\n";

								if(mb_strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][2]))==0){
									$SUR_TOTAL[2]+=$tabmatieres[$j]['fb_col'][2];
								}

								// Discipline
								echo "<td style='border: 1px solid black; text-align:left;'>\n";
								echo "<p class='discipline fb'>";
								echo ucfirst(mb_strtolower($tabmatieres[$j]["lig_speciale"]));
								echo "</p>\n";
								echo "</td>\n";

								// Moyenne classe
								echo "<td style='border: 1px solid black; text-align:center;'>\n";
								echo "&nbsp;";
								echo "</td>\n";

								// Moyenne élève
								echo "<td style='border: 1px solid black; text-align:center;'>\n";
								echo "&nbsp;";
								echo "</td>\n";

								// Appréciation
								echo "<td ";
								echo " class='fb' ";
								echo "style='border: 1px solid black; text-align:left;'>\n";
								//echo "&nbsp;";
								//echo "</td>\n";
								//echo "style='border: 1px solid black; text-align:left;'>\n";

								if($avec_app=="y") {
									$sql="SELECT appreciation FROM notanet_app na,
																	notanet_corresp nc
															 WHERE na.login='$lig1->login' AND
																	nc.notanet_mat='".$tabmatieres[$j][0]."' AND
																	nc.matiere=na.matiere;";
									//echo "$sql<br />";
									$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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


								echo "<td style='border: 1px solid black; text-align:center; background-color: gray;'>\n";
								echo "&nbsp;";
								echo "</td>\n";

								echo "<td ";
								echo " class='fb' ";
								echo "style='border: 1px solid black; text-align:right;'>\n";
								echo "&nbsp;";
								echo " / ".$tabmatieres[$j]['fb_col'][2];
								echo "</td>\n";

								echo "</tr>\n";

							}
							// Fin du isset($tabmatieres[$j]["lig_speciale"])
						}
					}
					// FIN DE la boucle matière pour les matières calculées: FRANCAIS ET MATHS


					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					// LV1 ET SC.PHY EN UNE SEULE LIGNE

					// Il faut récupérer celle des deux matières suivie par l'élève $lig1->login et afficher la moyenne correspondante

					$temoin_note_non_numerique="n";
					$tmp_note="&nbsp;";
					$tmp_moy="&nbsp;";
					$valeur_tmp="&nbsp;";
					$j=103;
					//$sql="SELECT mat,note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
					$sql="SELECT matiere,note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
					$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res_mat)>0){
						$lig_mat=mysqli_fetch_object($res_mat);
						//echo "$lig_mat->mat: ";
						$tmp_note=$lig_mat->note;
						if(($tmp_note!='AB')&&($tmp_note!='DI')&&($tmp_note!='NN')) {
							$valeur_tmp=$tmp_note*$tabmatieres[$j][-2];
							$TOTAL+=$valeur_tmp;
						}
						else{
							$valeur_tmp=$tmp_note;
							$temoin_note_non_numerique="y";

							if(($tabmatieres[$j][-1]!='PTSUP')){
								if($num_fb_col==1){
									$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
								}
								else{
									$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
								}
							}
						}

						//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
						$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
						//echo "$sql<br />";
						$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($res_moy)>0){
							$lig_moy=mysqli_fetch_object($res_moy);
							$tmp_moy="$lig_moy->moyenne_mat";
						}
					}
					else{
						$j=104;
						//$sql="SELECT mat,note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
						$sql="SELECT matiere,note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
						$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($res_mat)>0){
							$lig_mat=mysqli_fetch_object($res_mat);
							//echo "$lig_mat->mat: ";
							$tmp_note=$lig_mat->note;

							//$valeur_tmp=$tmp_note*$tabmatieres[$j][-2];
							if(($tmp_note!='AB')&&($tmp_note!='DI')&&($tmp_note!='NN')) {
								$valeur_tmp=$tmp_note*$tabmatieres[$j][-2];
								$TOTAL+=$valeur_tmp;
							}
							else{
								$valeur_tmp=$tmp_note;
								$temoin_note_non_numerique="y";

								if(($tabmatieres[$j][-1]!='PTSUP')){
									if($num_fb_col==1){
										$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
									}
									else{
										$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
									}
								}
							}

							$tmp_moy="&nbsp;";
							//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
							$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
							//echo "$sql<br />";
							$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
							if(mysqli_num_rows($res_moy)>0){
								$lig_moy=mysqli_fetch_object($res_moy);
								$tmp_moy="$lig_moy->moyenne_mat";
							}
						}
					}


					// CE N'EST PAS EN PTSUP...
					$SUR_TOTAL[1]+=$tabmatieres[$j]['fb_col'][1];
					$SUR_TOTAL[2]+=$tabmatieres[$j]['fb_col'][2];


					echo "<tr>\n";

					// Discipline
					echo "<td";
					echo ">\n";
					echo "<p class='discipline fb'>";
					//echo "GRRRRRRRRR";
					echo ucfirst(mb_strtolower($tabmatieres[$j]["lig_speciale"]));
					echo "</p>\n";
					echo "</td>\n";

					// Moyenne classe
					echo "<td ";
					echo " class='fb' ";
					echo "";
					echo "style='border: 1px solid black; text-align:center;'>\n";
					echo $tmp_moy;
					echo "</td>\n";

					// Moyenne élève
					echo "<td ";
					echo " class='fb' ";
					echo "";
					echo "style='border: 1px solid black; text-align:center;'>\n";
					echo $tmp_note;
					echo "</td>\n";

					// Appréciation
					echo "<td ";
					echo " class='fb' ";
					echo "";
					echo "style='border: 1px solid black; text-align:left;'>\n";
					//echo "&nbsp;";
					//echo "DEBUG: $TOTAL";
					//echo "</td>\n";
					//echo "style='border: 1px solid black; text-align:left;'>\n";

					if($avec_app=="y") {
						$sql="SELECT appreciation FROM notanet_app na,
														notanet_corresp nc
													WHERE na.login='$lig1->login' AND
														nc.notanet_mat='".$tabmatieres[$j][0]."' AND
														nc.matiere=na.matiere;";
						//echo "$sql<br />";
						$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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

					echo "<td ";
					echo " class='fb' ";
					echo "";
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
					if($tabmatieres[$j]['fb_col'][1]!="X"){
						if(($temoin_note_non_numerique=="n")||($num_fb_col==2)) {
							echo " / ".$tabmatieres[$j]['fb_col'][1];
						}
					}
					echo "</td>\n";


					echo "<td ";
					echo " class='fb' ";
					echo "";
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
					//echo " / ".$tabmatieres[$j]['fb_col'][2];
					if(($temoin_note_non_numerique=="n")||($num_fb_col==1)) {
						echo " / ".$tabmatieres[$j]['fb_col'][2];
					}
					echo "</td>\n";

					echo "</tr>\n";

					// FIN DE LA LIGNE COMMUNE POUR LV1 et SC.PHY


					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					// TOUTES LES AUTRES MATIERES

					//for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
					for($j=105;$j<=122;$j++){
						$temoin_note_non_numerique="n";
						//if($tabmatieres[$j][0]!=''){
						if($tabmatieres[$j][0]!=''){
							//if($tabmatieres[$j][-1]!='NOTNONCA'){
							//if(($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')){
							if(($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')&&($tabmatieres[$j]['socle']=='n')){

								//$tabmatieres[$j]['fb_col'][1]
								//$SUR_TOTAL+=$tabmatieres[$j][-2]*20;
								//echo "<tr><td>".$tabmatieres[$j]['fb_col'][1]."</td></tr>";
								if(mb_strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][1]))==0){$SUR_TOTAL[1]+=$tabmatieres[$j]['fb_col'][1];}
								if(mb_strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][2]))==0){$SUR_TOTAL[2]+=$tabmatieres[$j]['fb_col'][2];}
								//echo "<tr><td>$SUR_TOTAL[1]</td></tr>";

								echo "<tr>\n";

								// Discipline
								//echo "<td style='border: 1px solid black; text-align:left;'>\n";
								echo "<td";
								if($tabmatieres[$j][-1]=='PTSUP'){
									echo " rowspan='2'";
								}
								echo ">\n";
								echo "<p class='discipline fb'>";
								//echo "<span class='discipline'>";
								if(!isset($tabmatieres[$j]["lig_speciale"])){
									echo ucfirst(mb_strtolower($tabmatieres[$j][0]));
									//if($tabmatieres[$j][0]=="OPTION FACULTATIVE (1)"){echo ": ".$tabmatieres[$j][-5];}
									if($tabmatieres[$j][0]=="OPTION FACULTATIVE (1)"){

										// ==============================
										// recherche de la matière facultative pour l'élève
										//$sql_mat_fac="SELECT mat FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
										$sql_mat_fac="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
										$res_mat_fac=mysqli_query($GLOBALS["___mysqli_ston"], $sql_mat_fac);
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
								echo " class='fb' ";
								echo "";
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
									$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
									if(mysqli_num_rows($res_mat)>0){
										$lig_mat=mysqli_fetch_object($res_mat);
										//echo "$lig_mat->mat: ";

										//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
										$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
										//echo "$sql<br />";
										$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
										if(mysqli_num_rows($res_moy)>0){
											$lig_moy=mysqli_fetch_object($res_moy);
											echo "$lig_moy->moyenne_mat";
										}
									}
								}
								echo "</td>\n";

								// Moyenne élève
								echo "<td ";
								echo " class='fb' ";
								echo "";
								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "rowspan='2' ";
								}
								echo "style='border: 1px solid black; text-align:center;'>\n";
								//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
								$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
								$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
								echo " class='fb' ";
								echo "";
								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "rowspan='2' ";
								}
								//echo "style='border: 1px solid black; text-align:center;'>\n";
								//echo "&nbsp;";
								//echo "DEBUG: $TOTAL";
								//echo "</td>\n";
								echo "style='border: 1px solid black; text-align:left;'>\n";

								if($avec_app=="y") {
									$sql="SELECT appreciation FROM notanet_app na,
																	notanet_corresp nc
															 WHERE na.login='$lig1->login' AND
																	nc.notanet_mat='".$tabmatieres[$j][0]."' AND
																	nc.matiere=na.matiere;";
									//echo "$sql<br />";
									$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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



								/*
								$valeur_tmp="&nbsp;";
								$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
								$res_note=mysql_query($sql);
								if(mysql_num_rows($res_note)){
									$lig_note=mysql_fetch_object($res_note);
									//echo "$lig_note->note";
									//$valeur_tmp="$lig_note->note";
									$valeur_tmp=$lig_note->note*$tabmatieres[$j][-2];
									if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
										//$TOTAL+=$lig_note->note;
										// Le cas PTSUP est calculé plus loin
										if($tabmatieres[$j][-1]!='PTSUP'){
											$TOTAL+=$lig_note->note;
										}
									}
									//$note="$lig_note->note";
								}
								*/

								// EXTRACTION POUR LA(ES) COLONNE(S) DE DROITE
								$valeur_tmp="&nbsp;";
								//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
								$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
								$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
											if($num_fb_col==1){
												$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
											}
											else{
												$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
											}
										}
									}
								}
								else{
									// FAUT-IL UN TEMOIN POUR DECREMENTER LE SUR_TOTAL ?
									if(($tabmatieres[$j][-1]!='PTSUP')){
										if($num_fb_col==1){
											$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
										}
										else{
											$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
										}
									}
								}




								echo "<td ";
								echo " class='fb' ";
								echo "";
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
									if($tabmatieres[$j]['fb_col'][1]!="X"){
										if(($temoin_note_non_numerique=="n")||($num_fb_col==2)) {
											echo " / ".$tabmatieres[$j]['fb_col'][1];
										}
									}
								}
								echo "</td>\n";


								//echo "style='border: 1px solid black; text-align:center;'>\n";
								echo "<td ";
								echo " class='fb' ";
								echo "";
								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "style='border: 1px solid black; text-align:center;'>\n";
									echo "<b>Points > à 10</b>";
								}
								else{
									/*
									echo "style='border: 1px solid black; text-align:right;'>\n";
									// TRICHE... Mon dispositif ne permet pas de gérer correctement ce double affichage
									// Il faudrait /40 pour la 2è LV ou découverte professionnelle 6H.
									if($tabmatieres[$j][0]=='DEUXIEME LANGUE VIVANTE'){
										echo " / 40";
									}
									else{
										echo " / 20";
									}
									*/
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
									if(($temoin_note_non_numerique=="n")||($num_fb_col==1)) {
										echo " / ".$tabmatieres[$j]['fb_col'][2];
									}
								}
								echo "</td>\n";

								echo "</tr>\n";

								if($tabmatieres[$j][-1]=='PTSUP'){
									echo "<tr>\n";
									//echo "<td style='border: 1px solid black; text-align:right;'>\n";

									$valeur_tmp="";
									//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
									$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
											$temoin_note_non_numerique="y";
										}
									}
									else{
										//echo "&nbsp;";
										$valeur_tmp="&nbsp;";
										//$note="&nbsp;";
									}


									//echo "<td style='border: 1px solid black; text-align:center;'>\n";
									/*
									$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
									$res_note=mysql_query($sql);
									if(mysql_num_rows($res_note)){
										$lig_note=mysql_fetch_object($res_note);
										//echo "$lig_note->note";
										//$note="$lig_note->note";
										$ptsup=$lig_note->note-10;
										if($ptsup>0){
											echo "$ptsup";
											if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
												$TOTAL+=$ptsup;
											}
										}
										else{
											echo "0";
										}
									}
									else{
										echo "&nbsp;";
										//$note="&nbsp;";
									}
									*/
									if($num_fb_col==1){
										echo "<td ";
										echo " class='fb' ";
										echo "style='border: 1px solid black; text-align:center;'>\n";
										echo "$valeur_tmp";
									}
									else{
										echo "<td style='border: 1px solid black; text-align:right;'>\n";
										echo "&nbsp;";
										//$note="&nbsp;";
									}
									echo "</td>\n";



									//echo "<td>\n";
									//echo "&nbsp;";
									if($num_fb_col==2){
										echo "<td ";
										echo " class='fb' ";
										echo "style='border: 1px solid black; text-align:center;'>\n";
										echo "$valeur_tmp";
									}
									else{
										echo "<td style='border: 1px solid black; text-align:right;'>\n";
										echo "&nbsp;";
										//$note="&nbsp;";
									}
									echo "</td>\n";

									echo "</tr>\n";
								}
							}
							//else{
							elseif($tabmatieres[$j]['socle']=='n') {
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
						else{
							// $tabmatieres[$j][0]==0, mais il faut quand même afficher la ligne:
							// CAS PARTICULIER DE LA LIGNE DECOUVERTE PROFESSIONNELLE INUTILE MAIS PRESENTE POUR LES SERIES TECHNOLOGIQUE SANS DP6 ET PROFESSIONNELLE SANS DP6

							if(isset($tabmatieres[$j]["lig_speciale"])) {
								echo "<tr>\n";

								if(mb_strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][2]))==0){
									$SUR_TOTAL[2]+=$tabmatieres[$j]['fb_col'][2];
								}

								// Discipline
								echo "<td style='border: 1px solid black; text-align:left;'>\n";
								echo "<p class='discipline fb'>";
								echo ucfirst(mb_strtolower($tabmatieres[$j]["lig_speciale"]));
								echo "</p>\n";
								echo "</td>\n";

								// Moyenne classe
								echo "<td style='border: 1px solid black; text-align:center;'>\n";
								echo "&nbsp;";
								echo "</td>\n";

								// Moyenne élève
								echo "<td style='border: 1px solid black; text-align:center;'>\n";
								echo "&nbsp;";
								echo "</td>\n";

								// Appréciation
								echo "<td ";
								echo " class='fb' ";
								echo "style='border: 1px solid black; text-align:left;'>\n";
								//echo "&nbsp;";
								//echo "</td>\n";
								//echo "style='border: 1px solid black; text-align:left;'>\n";

								if($avec_app=="y") {
									$sql="SELECT appreciation FROM notanet_app na,
																	notanet_corresp nc
															 WHERE na.login='$lig1->login' AND
																	nc.notanet_mat='".$tabmatieres[$j][0]."' AND
																	nc.matiere=na.matiere;";
									//echo "$sql<br />";
									$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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


								echo "<td style='border: 1px solid black; text-align:center; background-color: gray;'>\n";
								echo "&nbsp;";
								echo "</td>\n";

								echo "<td ";
								echo " class='fb' ";
								echo "style='border: 1px solid black; text-align:right;'>\n";
								echo "&nbsp;";
								echo " / ".$tabmatieres[$j]['fb_col'][2];
								echo "</td>\n";

								echo "</tr>\n";

							}
							// Fin du isset($tabmatieres[$j]["lig_speciale"])
						}
					}
					// FIN DE la boucle matière pour les matières calculées


					//=====================
					// SOCLES B2I ET A2
					include("b2i_a2.inc.php");
					//=====================


					//=====================

					if($temoin_NOTNONCA>0){
						// ON TRAITE LES MATIERES NOTNONCA
						echo "<tr>\n";

						echo "<td colspan='4' ";
						echo " class='fb' ";
						echo "style='border: 1px solid black; text-align:center; font-weight:bold;'>\n";
						echo "A titre indicatif";
						echo "</td>\n";

						echo "<td style='font-weight:bold; font-size:".$fb_titretab."pt; line-height: ".$fb_tittab_lineheight."pt;'>\n";
						echo "TOTAL DES POINTS";
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
									echo "<p class='discipline fb'>";
									echo ucfirst(mb_strtolower($tabmatieres[$j][0]));
									echo "</p>";
									echo "</td>\n";

									// Moyenne de la classe
									echo "<td ";
									echo " class='fb' ";
									echo "style='border: 1px solid black; text-align:center;'>\n";
									//echo $moy_classe[$j];
									if($fb_mode_moyenne==1){
										echo $moy_classe[$j];
									}
									else{
										//$sql="SELECT mat FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
										$sql="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
										$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
										if(mysqli_num_rows($res_mat)>0){
											$lig_mat=mysqli_fetch_object($res_mat);
											//echo "$lig_mat->mat: ";

											//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
											$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
											//echo "$sql<br />";
											$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
											if(mysqli_num_rows($res_moy)>0){
												$lig_moy=mysqli_fetch_object($res_moy);
												echo "$lig_moy->moyenne_mat";
											}
										}
									}
									echo "</td>\n";

									// Moyenne de l'élève
									echo "<td ";
									echo " class='fb' ";
									echo "style='border: 1px solid black; text-align:center;'>\n";
									//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
									$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
									echo " class='fb' ";
									echo "style='border: 1px solid black; text-align:left;'>\n";
									//echo "&nbsp;";
									//echo "</td>\n";
									//echo "style='border: 1px solid black; text-align:left;'>\n";

									if($avec_app=="y") {
										$sql="SELECT appreciation FROM notanet_app na,
																		notanet_corresp nc
																WHERE na.login='$lig1->login' AND
																		nc.notanet_mat='".$tabmatieres[$j][0]."' AND
																		nc.matiere=na.matiere;";
										//echo "$sql<br />";
										$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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

									// Colonne total des lignes calculées (non NOTNONCA)...
									if($num_lig==0){
										$nb_info=$temoin_NOTNONCA;


										//echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:right;'>\n";
										if($num_fb_col==1){
											echo "<td ";
											echo " class='fb' ";
											echo "rowspan='$nb_info' style='border: 1px solid black; text-align:center;'>\n";
											//echo "$TOTAL / 220";
											echo "$TOTAL";
											echo " / ".$SUR_TOTAL[1];
											echo "</td>\n";

											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:right;'>\n";
											//echo "/ 240";
											echo " / ".$SUR_TOTAL[2];
											echo "</td>\n";
										}
										else{
											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:right;'>\n";
											//echo "/ 220";
											echo " / ".$SUR_TOTAL[1];
											echo "</td>\n";

											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:center;'>\n";
											//echo "$TOTAL / 220";
											echo "$TOTAL";
											echo " / ".$SUR_TOTAL[2];
											echo "</td>\n";
										}
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
									echo "<p class='discipline fb'>";
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

										/*
										//echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:right;'>\n";
										if($num_fb_col==1){
											echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:center;'>\n";
											echo "$TOTAL / 220";
											echo "</td>\n";

											echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:right;'>\n";
											echo "/ 240";
											echo "</td>\n";
										}
										else{
											echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:right;'>\n";
											echo "/ 220";
											echo "</td>\n";

											echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:center;'>\n";
											echo "$TOTAL / 240";
											echo "</td>\n";
										}
										*/
										//echo "</td>\n";
										if($num_fb_col==1){
											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:center;'>\n";
											//echo "$TOTAL / 220";
											echo "$TOTAL";
											echo " / ".$SUR_TOTAL[1];
											echo "</td>\n";

											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:right;'>\n";
											//echo "/ 240";
											echo " / ".$SUR_TOTAL[2];
											echo "</td>\n";
										}
										else{
											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:right;'>\n";
											//echo "/ 220";
											echo " / ".$SUR_TOTAL[1];
											echo "</td>\n";

											echo "<td rowspan='$nb_info' ";
											echo " class='fb' ";
											echo "style='border: 1px solid black; text-align:center;'>\n";
											//echo "$TOTAL / 220";
											echo "$TOTAL";
											echo " / ".$SUR_TOTAL[2];
											echo "</td>\n";
										}

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
							//if(($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')){
							if(($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')&&($tabmatieres[$j]['socle']=='n')){

								
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
								echo "<p class='discipline fb'>";
								//echo "<span class='discipline'>";
								if(!isset($tabmatieres[$j]["lig_speciale"])){
									echo ucfirst(mb_strtolower($tabmatieres[$j][0]));
									//if($tabmatieres[$j][0]=="OPTION FACULTATIVE (1)"){echo ": ".$tabmatieres[$j][-5];}
									if($tabmatieres[$j][0]=="OPTION FACULTATIVE (1)"){

										// ==============================
										// recherche de la matière facultative pour l'élève
										//$sql_mat_fac="SELECT mat FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
										$sql_mat_fac="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
										$res_mat_fac=mysqli_query($GLOBALS["___mysqli_ston"], $sql_mat_fac);
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
									$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
									if(mysqli_num_rows($res_mat)>0){
										$lig_mat=mysqli_fetch_object($res_mat);
										//echo "$lig_mat->mat: ";

										//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
										$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
										//echo "$sql<br />";
										$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
								$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
								//echo "</td>\n";
								echo "style='border: 1px solid black; text-align:left;'>\n";

								if($avec_app=="y") {
									$sql="SELECT appreciation FROM notanet_app na,
																	notanet_corresp nc
															 WHERE na.login='$lig1->login' AND
																	nc.notanet_mat='".$tabmatieres[$j][0]."' AND
																	nc.matiere=na.matiere;";
									//echo "$sql<br />";
									$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
								$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
									$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
							//else{
							elseif($tabmatieres[$j]['socle']=='n') {
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
					// SOCLES B2I ET A2
					include("b2i_a2.inc.php");
					//=====================


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
									echo "<p class='discipline fb'>";
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
										$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
										if(mysqli_num_rows($res_mat)>0){
											$lig_mat=mysqli_fetch_object($res_mat);
											//echo "$lig_mat->mat: ";

											//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
											$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
											//echo "$sql<br />";
											$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
									$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
									if(mysqli_num_rows($res_note)){
										$lig_note=mysqli_fetch_object($res_note);
										echo "$lig_note->note";
									}
									else{
										echo "&nbsp;";
									}
									echo "</td>\n";

									// Appréciation
									echo "<td style='border: 1px solid black; text-align:left;'>\n";
									//echo "&nbsp;";
									//echo "</td>\n";
									//echo "style='border: 1px solid black; text-align:left;'>\n";

									if($avec_app=="y") {
										$sql="SELECT appreciation FROM notanet_app na,
																		notanet_corresp nc
																WHERE na.login='$lig1->login' AND
																		nc.notanet_mat='".$tabmatieres[$j][0]."' AND
																		nc.matiere=na.matiere;";
										//echo "$sql<br />";
										$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
									echo "<p class='discipline fb'>";
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

				echo "<tr>\n";
				echo "<td colspan='4' rowspan='2' ";
				echo " class='fb' ";
				echo "valign='top' style='border: 1px solid black; text-align:left;'>\n";
				echo "<p style='min-height:".$fb_nblig_avis_chef_em."em;'>\n";
				//echo "<p style='min-height:20em;'>\n";
				echo "<b>Avis et signature du chef d'établissement:</b>";

				//$sql="SELECT avis FROM notanet_avis WHERE login='$lig1->login';";
				$sql="SELECT * FROM notanet_avis WHERE login='$lig1->login';";
				$res_avis=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_avis)>0) {
					echo "<br />\n";
					$lig_avis=mysqli_fetch_object($res_avis);
					if($lig_avis->favorable=="O") {echo "Avis favorable.<br />";}
					elseif($lig_avis->favorable=="N") {echo "Avis défavorable.<br />";}
					echo htmlspecialchars($lig_avis->avis);
				}
				//for($k=0;$k<=$fb_nblig_avis_chef;$k++){
				//	echo "<br />\n";
				//}
				echo "</p>\n";
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
