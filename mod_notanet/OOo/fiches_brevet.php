<?php
/* $Id$ */
/*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Construction du tableau de notes des fiches brevets





// Bibliothèque pour Notanet et Fiches brevet
include("../lib_brevets.php");


//===================================
//=== Enregistrement des paramètres d'impression ===
//===================================

if (isset($_POST['enregistrer_param'])) {
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

	if (isset($_POST['fb_gab_perso'])) {
		if (!saveSetting("fb_gab_perso", $_POST['fb_gab_perso'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_gab_perso !";
		}
	}

	if (isset($_POST['fb_dezip_ooo'])) {
		if (!saveSetting("fb_dezip_ooo", $_POST['fb_dezip_ooo'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_dezip_ooo !";
		}
	}

	if($msg==""){$msg="Enregistrement effectué.";}
}



//=======================
//=== Initialisation des variables ===
//=======================

$titre_page = "Fiches Brevet";

// Récupération des variables:
// Tableau des classes:
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$type_brevet = isset($_POST['type_brevet']) ? $_POST['type_brevet'] : (isset($_GET['type_brevet']) ? $_GET['type_brevet'] : NULL);
if(isset($type_brevet)) {
	if((!my_ereg("[0-9]",$type_brevet))||(mb_strlen(my_ereg_replace("[0-9]","",$type_brevet))!=0)) {
		$type_brevet=NULL;
	}
}

$avec_app=isset($_POST['avec_app']) ? $_POST['avec_app'] : "n";



//=====================================
//=== PARAMÉTRAGE GENERAL DES FICHES BREVETS ===
//=====================================

if (isset($_GET['parametrer'])) {

	//**************** EN-TETE *****************
	require_once("../../lib/header.inc.php");
	//**************** FIN EN-TETE *****************


	// Paramétrage des tailles de police, dimensions, nom d'académie, de département,...
	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../../accueil.php'>Accueil</a>";
	echo " | <a href='../index.php'>Accueil Notanet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix des classes</a>";
	echo "</p>\n";
	echo "</div>\n";

	echo "<h2>Paramètres d'affichage des Fiches Brevet</h2>\n";

	echo "<form action='".$_SERVER['PHP_SELF']."' id='form_param' method='post'>\n";
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
		echo "<input type='radio' name='fb_mode_moyenne' id='fb_mode_moyenne_1' value='1' ";
		if($fb_mode_moyenne!="2"){
			echo "checked='checked' />";
		}
		else{
			echo "/>";
		}
		echo "</td>\n";
		echo "<td>\n";
		echo "<label for='fb_mode_moyenne_1'>Calculer la moyenne de toutes matières d'une même option Notanet confondues<br />\n";
		echo "(<i>on compte ensemble les AGL1 et ALL1; c'est la moyenne de toute la LV1 qui est effectuée</i>)</label>\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='fb_mode_moyenne' id='fb_mode_moyenne_2' value='2' ";
		if($fb_mode_moyenne=="2"){
			echo "checked='checked' />";
		}
		else{
			echo "/>";
		}
		echo "</td>\n";
		echo "<td>\n";
		echo "<label for='fb_mode_moyenne_2'>Calculer les moyennes par matières<br />\n";
		echo "(<i>on ne mélange pas AGL1 et ALL1 dans le calcul de la moyenne de classe pour un élève</i>)</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "</tr>\n";

// choix des gabarits
$alt=$alt*(-1);
$fb_gab_perso=getSettingValue("fb_gab_perso");
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td valign='top'>Gabarits : <br />Vous pouvez utiliser les gabarits intégrés à Gepi (construits à partir des fiches brevets de Nantes) <br />ou utiliser le module OpenOffice pour enregistrer vos propres gabarits</td>\n";
	echo "<td>";
	echo "<input type='radio' name='fb_gab_perso' id='fb_gab_perso_1' value='1' ";
	if($fb_gab_perso=="1"){
	  echo "checked='checked' />";
	}
	else{
	  echo "/>";
	}
	echo "<label for='fb_gab_perso_1'> Gabarits personnels </label><br />";
	echo "<input type='radio' name='fb_gab_perso' id='fb_gab_perso_0' value='0' ";
	if($fb_gab_perso=="0"){
	  echo "checked='checked' />";
	}
	else{
	  echo "/>";
	}
	echo "<label for='fb_gab_perso_0'> Gabarits Gepi </label><br />";
	echo "</td>\n";
	echo "</tr>\n";

// choix du dézippeur
$alt=$alt*(-1);
$fb_dezip_ooo=getSettingValue("fb_dezip_ooo");
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";

	echo "<td valign='top'>Décompresseur d'archive : <br />Gepi a besoin d'un décompresseur d'archive pour créer les fiches brevets. Vous devez sélectionner celui que vous voulez utiliser.</td>\n";
	echo "</td>";
	echo "<td>";

	echo "<input type='radio' name='fb_dezip_ooo' id='fb_dezip_ooo_3' value='3' ";
	if($fb_dezip_ooo=="3"){
	  echo "checked='checked' />";
	}
	else{
	  echo "/>";
	}
	echo "<label for='fb_dezip_ooo_3'> Zlib + openTBS : le meilleur choix mais nécessite que php ait été compilé avec l'extension zlib (voir la configuration du serveur)</label><br />\n";
	

	echo "<input type='radio' name='fb_dezip_ooo' id='fb_dezip_ooo_0' value='0' ";
	if($fb_dezip_ooo=="0"){
	  echo "checked='checked' />";
	}
	else{
	  echo "/>";
	}
	echo "<label for='fb_dezip_ooo_0'> ZIPARCHIVE et TinyDoc : peut créer des fichiers corrompus si votre version de PHP est inférieure à 5.2.8 (utiliser OOo 3.2 pour réparer les fichiers) </label><br />\n";
	
	echo "<input type='radio' name='fb_dezip_ooo' id='fb_dezip_ooo_1' value='1' ";
	if($fb_dezip_ooo=="1"){
	  echo "checked='checked' />";
	}
	else{
	  echo "/>";
	}
	echo "<label for='fb_dezip_ooo_1'> ZIP-UNZIP et TinyDoc : nécessite que ZIP et UNZIP soient installés sur le serveur et que leurs chemins soient définis dans la variable d'environnement PATH </label><br />\n";

	echo "<input type='radio' name='fb_dezip_ooo' id='fb_dezip_ooo_2' value='2' ";
	if($fb_dezip_ooo=="2"){
	  echo "checked='checked' />";
	}
	else{
	  echo "/>";
	}
	echo "<label for='fb_dezip_ooo_2'> PCLZIP et TBSooo : classe plus ancienne, toutes les fonctionnalités de TinyDoc ne sont pas disponible dans les gabarits mais fonctionne avec PHP 5.2 </label><br />\n";

	echo "</td>\n";

	echo "</tr>\n";

	echo "</table>\n";

	echo "<p style='text-align: center;'><input type='submit' name='enregistrer_param' value='Enregistrer' /></p>\n";
	echo "</form>\n";

	require("../../lib/footer.inc.php");
	die();

}

//============================
//=== FIN DU PARAMETRAGE  GENERAL ===
//============================




//===================================================
//=== VÉRIFICATION QUE DES ÉLÈVES SONT BIEN AFFECTÉS À UN BREVET ===
//===================================================

$sql="SELECT DISTINCT type_brevet FROM notanet_ele_type ORDER BY type_brevet;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {

	//**************** EN-TETE *****************
	require_once("../../lib/header.inc.php");
	//**************** FIN EN-TETE *****************

	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune association élève/type de brevet n'a encore été réalisée.<br />Commencez par <a href='../select_eleves.php'>sélectionner les élèves</a></p>\n";

	require("../../lib/footer.inc.php");
	die();
}

$sql="SELECT DISTINCT type_brevet FROM notanet_corresp WHERE $sql_indices_types_brevets ORDER BY type_brevet;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_type_brevet=mysqli_num_rows($res);

if($nb_type_brevet==0) {

	//**************** EN-TETE *****************
	require_once("../../lib/header.inc.php");
	//**************** FIN EN-TETE *****************

	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune association matières/type de brevet n'a encore été réalisée.<br />Commencez par <a href='../select_matieres.php'>sélectionner les matières</a></p>\n";

	require("../../lib/footer.inc.php");
	die();
}

//=======================
//=== FIN DE LA VÉRIFICATION ===
//=======================




//==========================
//=== CHOIX DU BREVET À TRAITER ===
//==========================

// Bibliothèque pour Notanet et Fiches brevet
// include("../lib_brevets.php");

if(!isset($type_brevet)) {

	//**************** EN-TETE *****************
	require_once("../../lib/header.inc.php");
	//**************** FIN EN-TETE *****************

	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../../accueil.php'>Accueil</a> | <a href='../index.php'>Accueil Notanet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?parametrer=y'>Paramétrer</a>";
	echo "</p>\n";
	echo "</div>\n";

	echo "<ul>\n";
		while($lig=mysqli_fetch_object($res)) {
			switch ($lig->type_brevet ) {
				case 0 :
					echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>Générer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
				break;
				case 1 :
					echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>Générer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
				break;
				case 2 :
					echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>Générer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
				break;
				case 3 :
					echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>Générer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
				break;
				case 4 :
					echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>Générer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
				break;
				case 5 :
					echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>Générer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
				break;
				case 6 :
					echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>Générer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
				break;
				case 7 :
					echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>Générer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
				break;
			}
		}
	echo "</ul>\n";

	require("../../lib/footer.inc.php");
	die();
}

//========================
//===  FIN DU CHOIX DU BREVET ===
//========================


//===================
//=== Données communes ===
//===================

// Adresse établissement:
$gepiSchoolName=getSettingValue("gepiSchoolName");
$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1");
$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2");
$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode");
$gepiSchoolCity=getSettingValue("gepiSchoolCity");


$tabmatieres=array();
for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
	$tabmatieres[$j]=array();
}

//========================
//=== Fin des données communes ===
//========================



//====================
//=== CHOIX DE LA CLASSE ===
//====================

if (!isset($id_classe)) {

	//**************** EN-TETE *****************
	require_once("../../lib/header.inc.php");
	//**************** FIN EN-TETE *****************

	// Choix de la classe:
	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../../accueil.php'>Accueil</a>";
	echo " | <a href='../index.php'>Accueil Notanet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Type de brevet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?parametrer=y'>Paramétrer</a>";
	echo "</p>\n";
	echo "</div>\n";

	// Les tables notanet ne sont pas renseignées, on s'arrête
	$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, periodes p, notanet n,notanet_ele_type net WHERE p.id_classe = c.id AND c.id=n.id_classe AND n.login=net.login AND net.type_brevet='$type_brevet' ORDER BY classe");
	if(!$call_data){
		echo "<p><font color='red'>Attention:</font> Il semble que vous n'ayez pas mené la procédure notanet à son terme.<br />Cette procédure renseigne des tables requises pour générer les fiches brevet.<br />Effectuez la <a href='../index.php'>procédure notanet</a>.</p>\n";

		require("../../lib/footer.inc.php");
		die();
	}
	$nombre_lignes = mysqli_num_rows($call_data);

	echo "<div>\n";

	echo "<p>Choisissez les classes pour lesquelles vous souhaitez générer les fiches brevet:</p>\n";

	echo "<form action='".$_SERVER['PHP_SELF']."' id='form_choix_classe' method='post'>\n";
	echo "<p><input type='hidden' name='type_brevet' value='$type_brevet' /></p>\n";
	echo "<p>Sélectionnez les classes : </p>\n";
	echo "<blockquote>\n";

	$size=min(10,$nombre_lignes);
	echo "<p><select name='id_classe[]' multiple='multiple' size='$size'>\n";
	$i = 0;
	while ($i < $nombre_lignes){
		$classe = old_mysql_result($call_data, $i, "classe");
		$ide_classe = old_mysql_result($call_data, $i, "id");
		echo "<option value='$ide_classe'";
		if($nombre_lignes==1) {echo " selected='selected'";}
		echo ">$classe</option>\n";
		$i++;
	}
	echo "</select></p>\n";
	echo "<p>\n<input type='radio' name='tri' id='tri_alpha' value='alpha' checked='checked' /><label for='tri_alpha' style='cursor: pointer;'> Tri alphabétique sur les noms et prénoms des élèves indépendamment des classes.</label><br />\n";
	echo "<input type='radio' name='tri' id='tri_classe' value='classe' /><label for='tri_classe' style='cursor: pointer;'> Tri par classe puis tri alphabétique sur les noms et prénoms des élèves.</label></p>\n";
	echo "<p>\n<input type='checkbox' name='avec_app' id='avec_app' value='y' checked='checked' /><label for='avec_app' id='avec_app_label' style='cursor: pointer;'> Avec les appréciations</label>\n";
	echo "<input type='submit' name='choix_classe' value='Envoyer' />\n</p>\n";

	echo "</blockquote>\n";
	echo "</form>\n";

	echo "<p style='margin-top:2em; text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Si une ou des classes n'apparaissent pas, c'est très probablement que l'extraction n'a pas été faite pour ces classes.<br />
	Cela peut arriver par exemple quand les notes d'EPS n'ont pas été saisies.</p>";

	// Fermeture du DIV container initialisé dans le header.inc.php
	echo "</div>\n";
	require("../../lib/footer.inc.php");
	die();

}

//====================================
//=== FIN DU FORMULAIRE DE CHOIX DES CLASSES ===
//====================================


//==============================
// === Construction du tableau fiche brevet===
//==============================

// On récupère le tableau des paramètres associés à ce type de brevet:
$tabmatieres=tabmatieres($type_brevet);

// tableau de correspondance Champs de $tabmatieres -> champs de publipostage OOo
$tab_champs_OOo=array();
for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
	if($tabmatieres[$j][0]!=''){
		$tab_champs_OOo[$j]=array();
		$tab_champs_OOo[$j][0]=$j;									// code de la matière
		$tab_champs_OOo[$j][1]=$tabmatieres[$j][0];					// Nom long
		$tab_champs_OOo[$j][2]="fb_note_".$j;						// Nom variable OOo
//echo "\$tabmatieres[$j][-1]<br />\n";
		switch($tabmatieres[$j][-1]){								// Coefficient
			case "NOTNONCA":										// Note non comptabilisée
				$tab_champs_OOo[$j][3]="-1";
			break;
			case "PTSUP":											// Seuls les points au dessus de la moyenne sont comptabilisés
				$tab_champs_OOo[$j][3]="0";
			break;
			case "POINTS":											// On récupère le coef
				if ($tabmatieres[$j]['socle']=='n'){
					$tab_champs_OOo[$j][3]=$tabmatieres[$j][-2];	// On récupère le coef
				} else{
					$tab_champs_OOo[$j][3]="-2";					// cas du B2I et A2 langue
				}
			break;
		}
//echo "<hr />\n";
	}
}




/***** Données communes *****/
$fb_academie=getSettingValue("fb_academie");
$fb_departement=getSettingValue("fb_departement");
$fb_session=getSettingValue("fb_session");
// Si la session n'est pas renseignée, on la calcule
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
// Mode de calcul des moyennes
$fb_mode_moyenne=getSettingValue("fb_mode_moyenne");
if($fb_mode_moyenne!=2){$fb_mode_moyenne=1;}


/***** Fin des données communes *****/



/***** Faut-il afficher le lieu de naissance *****/
$ele_lieu_naissance=getSettingValue("ele_lieu_naissance") ? getSettingValue("ele_lieu_naissance") : "n";

// création d'un tableau pour stocker les notes d'élèves
$tab_eleves_OOo=array();
$nb_eleve=0;

// BOUCLE SUR LA LISTE DES CLASSES

for($i=0;$i<count($id_classe);$i++){

	// Calcul des moyennes de classes... pb avec le statut...
	$moy_classe=array();
	for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
		if($tabmatieres[$j][0]!=''){
			$sql="SELECT ROUND(AVG(note),1) moyenne FROM notanet WHERE note!='DI' AND note!='AB' AND note!='NN' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
			$res_moy=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_moy)>0){
				$lig_moy=mysqli_fetch_object($res_moy);
				$moy_classe[$j]=$lig_moy->moyenne;
			}
			else{
				$moy_classe[$j]="";
			}
		}
	}


	// Récupération du statut des matières : ceux validés lors du traitement NOTANET
	// pour repérer les matières non dispensées.
	for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
		if($tabmatieres[$j][0]!=''){
			$sql="SELECT * FROM notanet_corresp WHERE notanet_mat='".$tabmatieres[$j][0]."' AND type_brevet='$type_brevet' LIMIT 1";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0){
				$lig=mysqli_fetch_object($res);
				$tabmatieres[$j][-4]=$lig->statut;
				// On ne récupère qu'une seule des matières Gepi associées, là.
				$tabmatieres[$j][-5]=$lig->matiere;
			}
			else{
				$tabmatieres[$j][-4]="";
				$tabmatieres[$j][-5]="";
			}
		}
	}


	//echo "<div class='noprint'>\n";

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

			$tab_eleves_OOo[$nb_eleve]=array();
			$tab_eleves_OOo[$nb_eleve]['fb_session']=$fb_session;

			$tab_eleves_OOo[$nb_eleve]['mefcode']=$lig1->mef_code;
			
			/***** MEF *****/
			$tab_eleves_OOo[$nb_eleve]['mef']="";
			$sql_mef="SELECT libelle_edition FROM mef WHERE mef_code='".$lig1->mef_code."';";
			$res_mef=mysqli_query($GLOBALS["mysqli"], $sql_mef);
			if(mysqli_num_rows($res_mef)>0) {
			   $lig_mef=mysqli_fetch_object($res_mef);
			   $tab_eleves_OOo[$nb_eleve]['mef']=$lig_mef->libelle_edition;
			}	
			
			/***** Langue régionale	*****/
			$tab_eleves_OOo[$nb_eleve]['lvr']="";
			$tab_eleves_OOo[$nb_eleve]['lvrNom']="";
			$sql_lvr="SELECT * FROM `notanet_lvr_ele` WHERE `login` LIKE '".$lig1->login."';";
			$res_lvr=mysqli_query($GLOBALS["mysqli"], $sql_lvr);
			if(mysqli_num_rows($res_lvr)>0) {
			   $lig_lvr=mysqli_fetch_object($res_lvr);
			   $tab_eleves_OOo[$nb_eleve]['lvr']=$lig_lvr->note;
			   $sql_lvrNom="SELECT * FROM `notanet_lvr` WHERE `id` = ".$lig_lvr->id_lvr.";";
			   $res_lvrNom=mysqli_query($GLOBALS["mysqli"], $sql_lvrNom);
			   if(mysqli_num_rows($res_lvrNom)>0) {
				  $lig_lvrNom=mysqli_fetch_object($res_lvrNom);
				  $tab_eleves_OOo[$nb_eleve]['lvrNom']=$lig_lvrNom->intitule;
			   }
			}

			//echo "<p>$lig1->nom $lig1->prenom<br />";
			$tab_eleves_OOo[$nb_eleve]['nom']=$lig1->nom;
			$tab_eleves_OOo[$nb_eleve]['prenom']=$lig1->prenom;
			$tab_eleves_OOo[$nb_eleve]['ine']=$lig1->no_gep;
			$tab_eleves_OOo[$nb_eleve]['fille']="";										// on initialise les champs pour ne pas avoir d'erreurs
			if($lig1->sexe=='F') {$tab_eleves_OOo[$nb_eleve]['fille']="e";} // ajouter un e à née si l'élève est une fille
			$tab_eleves_OOo[$nb_eleve]['date_nais']=formate_date($lig1->naissance);
			$tab_eleves_OOo[$nb_eleve]['lieu_nais']="";										// on initialise les champs pour ne pas avoir d'erreurs
			if($ele_lieu_naissance=="y") {$tab_eleves_OOo[$nb_eleve]['lieu_nais']=preg_replace ( '@<[\/\!]*?[^<>]*?>@si'  , ''  , get_commune($lig1->lieu_naissance,1)) ;} // récupérer la commune
			$tab_eleves_OOo[$nb_eleve]['ecole']=$gepiSchoolName;
			$tab_eleves_OOo[$nb_eleve]['adresse1']=$gepiSchoolAdress1;
			$tab_eleves_OOo[$nb_eleve]['adresse2']=$gepiSchoolAdress2;
			$tab_eleves_OOo[$nb_eleve]['codeposte']=$gepiSchoolZipCode;
			$tab_eleves_OOo[$nb_eleve]['commune']=$gepiSchoolCity;
			$tab_eleves_OOo[$nb_eleve]['acad']=mb_strtoupper($fb_academie);
			$tab_eleves_OOo[$nb_eleve]['departe']=$fb_departement;
			$tab_eleves_OOo[$nb_eleve]['session']=$fb_session;

			$sql="SELECT doublant FROM j_eleves_regime WHERE login='".$lig1->login."';";
			$res_reg=mysqli_query($GLOBALS["mysqli"], $sql);
			$doublant='n';
			if(mysqli_num_rows($res_reg)>0) {
				$lig_reg=mysqli_fetch_object($res_reg);
				if($lig_reg->doublant=='R') {
					$doublant='y';
				}
			}
			$tab_eleves_OOo[$nb_eleve]['redoublant']=$doublant;

			//=======================================

			if(($type_brevet==0)||($type_brevet==1)||($type_brevet==2)||($type_brevet==3)||($type_brevet==4)||($type_brevet==5)||($type_brevet==6)||($type_brevet==7)){

				$TOTAL=0;
				$TOTAL_COEF=0;
				$TOTAL_POINTS=0;
				for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
					unset($matiere_gepi_courante);

					//if ($tab_champs_OOo[$j][0]!='') {
					if ((isset($tab_champs_OOo[$j][0]))&&($tab_champs_OOo[$j][0]!='')) {

						$tab_eleves_OOo[$nb_eleve][$j]=array();
						for($l=0;$l<=10;$l++){
							/*
								0: Moyenne de l'élève
								1: Moyenne ou points Notanet de l'élève
								2: Identifiant de matière gepi
								3: Moyenne de la classe
								4: Appréciation
								5: Nom de la langue vivante ou option
								6: Nom de la langue vivante ou option
							*/
							$tab_eleves_OOo[$nb_eleve][$j][$l]=""; 						// on initialise les champs pour ne pas avoir d'erreurs
						}
						if($tab_champs_OOo[$j][3]>-2) {$tab_eleves_OOo[$nb_eleve][$j][3] = $moy_classe[$j];}

						$sql="SELECT note, matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
						//echo "$sql<br />";
						$res_note=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_note)>0){
							$lig_note=mysqli_fetch_object($res_note);
							$tab_eleves_OOo[$nb_eleve][$j][0]=$lig_note->note;			// On récupère la note

							// Dans le cas où il a fallu vider une des deux notes parce quil y avait plusieurs matières gepi remplies et associées à la même matière notanet, il faut récupérer l'appréciation associée à la bonne matière
							$matiere_gepi_courante=$lig_note->matiere;

							switch($tab_champs_OOo[$j][3]){
								case '-2':      										// Socle B2I et A2
								  // rechercher la langue
								  if ($j=='115') {
									$sql_langue="SELECT m.nom_complet FROM matieres m,
																		   notanet_socles s
																	  WHERE m.matiere=s.lv AND
																			login='$lig1->login'";
									$res_lang=mysqli_query($GLOBALS["mysqli"], $sql_langue);
									if(mysqli_num_rows($res_lang)>0){
									  $lig_matiere=mysqli_fetch_object($res_lang);
									  $tab_eleves_OOo[$nb_eleve][115][1]=$lig_matiere->nom_complet;
									}
									else {
									  $tab_eleves_OOo[$nb_eleve][115][1]='';
									}
								  }
								break;
								case '-1':												// Note non prise en compte dans le calcul
									// on calcule la moyenne de la matière
									include("fb_moyenne.inc.php");
									// on va chercher les appréciations si besoin
									include("fb_appreciation.inc.php");
								break;
								case '0':												// Seuls les points au dessus de la moyenne comptent
									// on cherche le nom de l'option
									$sql_mat_fac="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_mat_fac=mysqli_query($GLOBALS["mysqli"], $sql_mat_fac);
									if(mysqli_num_rows($res_mat_fac)>0){
										$lig_mat_fac=mysqli_fetch_object($res_mat_fac);
										$tab_eleves_OOo[$nb_eleve][$j][2]=ucfirst(accent_min(mb_strtolower($lig_mat_fac->matiere)));

										$sql_opt="SELECT m.nom_complet FROM matieres m
										WHERE m.matiere='$lig_mat_fac->matiere'";
										$res_opt=mysqli_query($GLOBALS["mysqli"], $sql_opt);
										if(mysqli_num_rows($res_opt)>0){
											$lig_matiere=mysqli_fetch_object($res_opt);
											$tab_eleves_OOo[$nb_eleve][$j][5]=$lig_matiere->nom_complet;
										}

									}
									// on calcule la moyenne de la matière
									include("fb_moyenne.inc.php");

									// on va chercher les appréciations si besoin
									include("fb_appreciation.inc.php");

									// on extrait les points à ajouter
									if($tab_eleves_OOo[$nb_eleve][$j][0]!="DI" && $tab_eleves_OOo[$nb_eleve][$j][0]!="NN" && $tab_eleves_OOo[$nb_eleve][$j][0]!="ABS" && $tab_eleves_OOo[$nb_eleve][$j][0]!="AB") {
										if($tab_eleves_OOo[$nb_eleve][$j][0]>10) {
											$tab_eleves_OOo[$nb_eleve][$j][1]=($lig_note->note)-10;
											$TOTAL_POINTS= $TOTAL_POINTS+$tab_eleves_OOo[$nb_eleve][$j][1];
										}
									}
								break;
								default:
									// on cherche le nom de la matière
									$sql_mat_fac="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_mat_fac=mysqli_query($GLOBALS["mysqli"], $sql_mat_fac);
									if(mysqli_num_rows($res_mat_fac)>0){
										$lig_mat_fac=mysqli_fetch_object($res_mat_fac);
										$tab_eleves_OOo[$nb_eleve][$j][2]=ucfirst(accent_min(mb_strtolower($lig_mat_fac->matiere)));

										//if(preg_match("/|/", $lig_mat_fac->matiere)) {
										if(strstr($lig_mat_fac->matiere, '|')) {
											unset($tab_tmp_mat);
											$tab_tmp_mat=explode('|', $lig_mat_fac->matiere);
											for($loop=0;$loop<count($tab_tmp_mat);$loop++) {
												if($tab_tmp_mat[$loop]!="") {
													$sql_opt="SELECT m.nom_complet FROM matieres m
													WHERE m.matiere='".$tab_tmp_mat[$loop]."'";
													$res_opt=mysqli_query($GLOBALS["mysqli"], $sql_opt);
													if(mysqli_num_rows($res_opt)>0){
														$lig_matiere=mysqli_fetch_object($res_opt);
														$tab_eleves_OOo[$nb_eleve][$j][5+$loop]=$lig_matiere->nom_complet;
													}
												}
											}
										}
										else {
											$sql_opt="SELECT m.nom_complet FROM matieres m
											WHERE m.matiere='$lig_mat_fac->matiere'";
											$res_opt=mysqli_query($GLOBALS["mysqli"], $sql_opt);
											if(mysqli_num_rows($res_opt)>0){
												$lig_matiere=mysqli_fetch_object($res_opt);
												$tab_eleves_OOo[$nb_eleve][$j][5]=$lig_matiere->nom_complet;
											}
										}
									}

									// On calcule la note coefficientée
									//if($tab_eleves_OOo[$nb_eleve][$j][0]!="DI" && $tab_eleves_OOo[$nb_eleve][$j][0]!="NN" && $tab_eleves_OOo[$nb_eleve][$j][0]!="ABS" && $tab_eleves_OOo[$nb_eleve][$j][0]!="AB") {
										// ABS ne doit pas arriver... c'est AB
									if($tab_eleves_OOo[$nb_eleve][$j][0]!="DI" && $tab_eleves_OOo[$nb_eleve][$j][0]!="NN" && $tab_eleves_OOo[$nb_eleve][$j][0]!="ABS" && $tab_eleves_OOo[$nb_eleve][$j][0]!="AB") {
										// ABS ne doit pas arriver... c'est AB
										$tab_eleves_OOo[$nb_eleve][$j][1]=($lig_note->note)*$tab_champs_OOo[$j][3];
										$TOTAL_POINTS =$TOTAL_POINTS+$tab_eleves_OOo[$nb_eleve][$j][1];
										$TOTAL_COEF= $TOTAL_COEF+$tab_champs_OOo[$j][3];
									}
									elseif($tab_eleves_OOo[$nb_eleve][$j][0]=="ABS" || $tab_eleves_OOo[$nb_eleve][$j][0]=="AB") {
										$tab_eleves_OOo[$nb_eleve][$j][1]=$tab_eleves_OOo[$nb_eleve][$j][0];
										$TOTAL_COEF= $TOTAL_COEF+$tab_champs_OOo[$j][3];
									}
									else {
										$tab_eleves_OOo[$nb_eleve][$j][1]=$tab_eleves_OOo[$nb_eleve][$j][0];
									}
									// on calcule la moyenne de la matière
									include("fb_moyenne.inc.php");

									// on va chercher les appréciations si besoin
									include("fb_appreciation.inc.php");

								//break;
							}
						}else{				// l'éleve n'a pas de note
							// on va chercher les appréciations si besoin
							include("fb_appreciation.inc.php");
						}

						//echo "\$TOTAL_POINTS=$TOTAL_POINTS<br />";
						//echo "\$TOTAL_COEF=$TOTAL_COEF<br /><br />";
					}
				}
                // Langue régionale

                $sql="SELECT * FROM notanet_lvr_ele WHERE login='$lig1->login' ";
                $res_note=mysqli_query($GLOBALS["mysqli"], $sql);
                if(mysqli_num_rows($res_note)>0){
                  $lig_note=mysqli_fetch_object($res_note);
                  $tab_eleves_OOo[$nb_eleve][130][0]=$lig_note->note;
                  $sql_lvr="SELECT Intitule FROM notanet_lvr WHERE id='$lig_note->id_lvr' ";
                  $res_lvr=mysqli_query($GLOBALS["mysqli"], $sql_lvr);
                  if(mysqli_num_rows($res_lvr)>0){
                    $lig_lvr=mysqli_fetch_object($res_lvr);
                    $tab_eleves_OOo[$nb_eleve][130][1]=$lig_lvr->Intitule;
                  }
                }
                else{
                    $tab_eleves_OOo[$nb_eleve][130][0]="";
                    $tab_eleves_OOo[$nb_eleve][130][1]="";
                }

			}
			// ************************************************************************************************
			// ************************************************************************************************
			// ************************************************************************************************
			else{
				// echo "<p>BIZARRE! Ce type de brevet n'est pas prévu</p>";
			}

			// ************************************************************************************************
			//	 On récupère l'avis du chef d'établissement
			$tab_eleves_OOo[$nb_eleve]['decision']="";						// on initialise le champ pour ne pas avoir d'erreur
			$tab_eleves_OOo[$nb_eleve]['appreciation']= "";			// on initialise le champ pour ne pas avoir d'erreur
			$tab_eleves_OOo[$nb_eleve]['avis']= "";			// on initialise le champ pour ne pas avoir d'erreur

			$sql="SELECT * FROM notanet_avis WHERE login='$lig1->login';";
			$res_avis=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_avis)>0) {
				$lig_avis=mysqli_fetch_object($res_avis);
				if($lig_avis->favorable=="O") {$tab_eleves_OOo[$nb_eleve]['decision']="Avis favorable";}
				elseif($lig_avis->favorable=="N") {$tab_eleves_OOo[$nb_eleve]['decision']="Doit faire ses preuves";}
				//$tab_eleves_OOo[$nb_eleve]['appreciation']= htmlspecialchars($lig_avis->avis);
				$tab_eleves_OOo[$nb_eleve]['appreciation']= $lig_avis->avis;
				$tab_eleves_OOo[$nb_eleve]['avis']=$lig_avis->favorable;
			}

			$tab_eleves_OOo[$nb_eleve]['totalpoints']=$TOTAL_POINTS;
			$tab_eleves_OOo[$nb_eleve]['totalcoef']=$TOTAL_COEF*20;

			// L'Histoire des arts ne doit pas être dans le total sur les fiches brevet... alors qu'elle y est pour Notanet
			$tab_eleves_OOo[$nb_eleve]['totalpoints_bis']=$TOTAL_POINTS;
			$tab_eleves_OOo[$nb_eleve]['totalcoef_bis']=$TOTAL_COEF*20;
			// La note AB compte comme un zéro... donc pour le total sans Histoire_des_Arts, il faut décompter les points et coef d'Histoire_des_Arts même si la note est AB
			//if($tab_eleves_OOo[$nb_eleve][5][0]!='AB') {

			/*
				// 20120508: L'Histoire des Arts n'est plus saisie dans Notanet

				if($tab_eleves_OOo[$nb_eleve][5][0]!='DI') {
					$tab_eleves_OOo[$nb_eleve]['totalpoints_bis']-=$tab_eleves_OOo[$nb_eleve][5][1];
					//$tab_eleves_OOo[$nb_eleve]['totalcoef_bis']-=$tab_eleves_OOo[$nb_eleve][5][-2]*20;
					// L'Histoire des arts est sur 40... à extraire de là par la suite
					$tab_eleves_OOo[$nb_eleve]['totalcoef_bis']-=2*20;
				}
			*/
			$tab_eleves_OOo[$nb_eleve]['classe']=get_classe_from_id($id_classe[$i]);


			//===== Ajout mai 2011 ======
			/*
			// Ce qui suit n'est plus d'actualité en 2013
			// Afficher soit LV1 soit Sciences physiques
			if ($tab_eleves_OOo[$nb_eleve][103][0] > $tab_eleves_OOo[$nb_eleve][104][0]) {
				$tab_eleves_OOo[$nb_eleve]['LV1_ou_ScPhy']=$tab_eleves_OOo[$nb_eleve][103];
			}else{
				$tab_eleves_OOo[$nb_eleve]['LV1_ou_ScPhy']=$tab_eleves_OOo[$nb_eleve][104];
			}
			*/

			/*
				// 20120508: L'Histoire des Arts n'est plus saisie dans Notanet
				// Total des points sans Histoire des arts
				$pointsHistoireArts=40;
				if ($tab_eleves_OOo[$nb_eleve][5][1]) {
					$tab_eleves_OOo[$nb_eleve]['totalSansHistoireArts']=$tab_eleves_OOo[$nb_eleve]['totalpoints']-$tab_eleves_OOo[$nb_eleve][5][1];
					$tab_eleves_OOo[$nb_eleve]['totalcoefSansHistoireArts']=$tab_eleves_OOo[$nb_eleve]['totalcoef']-$pointsHistoireArts;
				}else{
					$tab_eleves_OOo[$nb_eleve]['totalSansHistoireArts']=$tab_eleves_OOo[$nb_eleve]['totalpoints'];
					$tab_eleves_OOo[$nb_eleve]['totalcoefSansHistoireArts']=$tab_eleves_OOo[$nb_eleve]['totalcoef'];
				}
			*/

			//===== Fin ajout mai 2011 ======

			/*
			// Ce qui suit n'est plus d'actualité en 2013
			// Pour les brevets PRO, on a soit LV1 soit ScPhy
			if(($type_brevet==2)||($type_brevet==3)) {
				$max_indice=5;
				for($loop=0;$loop<$max_indice;$loop++) {
					$tab_eleves_OOo[$nb_eleve]['LV_SC'][$loop]="";
				}

				//echo "<p>$lig1->login<br />\n";
				//echo "\$tab_eleves_OOo[$nb_eleve][103][0]=".$tab_eleves_OOo[$nb_eleve][103][0]."<br />\n";
				//echo "\$tab_eleves_OOo[$nb_eleve][103][1]=".$tab_eleves_OOo[$nb_eleve][103][1]."<br />\n";
				//echo "\$tab_eleves_OOo[$nb_eleve][104][0]=".$tab_eleves_OOo[$nb_eleve][104][0]."<br />\n";
				//echo "\$tab_eleves_OOo[$nb_eleve][104][1]=".$tab_eleves_OOo[$nb_eleve][104][1]."<br />\n";

				if((isset($tab_eleves_OOo[$nb_eleve][103][0]))&&(preg_match("/^[0-9\.]*$/",$tab_eleves_OOo[$nb_eleve][103][0]))) {

					if((isset($tab_eleves_OOo[$nb_eleve][104][0]))&&(preg_match("/^[0-9\.]*$/",$tab_eleves_OOo[$nb_eleve][104][0]))) {
						if($tab_eleves_OOo[$nb_eleve][103][0]>$tab_eleves_OOo[$nb_eleve][104][0]) {
							$num_lv_ou_sc=103;

							$num_matiere_a_decompter=104;
						}
						else {
							$num_lv_ou_sc=104;

							$num_matiere_a_decompter=103;
						}

						for($loop=0;$loop<$max_indice;$loop++) {
							if(isset($tab_eleves_OOo[$nb_eleve][$num_lv_ou_sc][$loop])) {
								$tab_eleves_OOo[$nb_eleve]['LV_SC'][$loop]=$tab_eleves_OOo[$nb_eleve][$num_lv_ou_sc][$loop];
								//echo "1 \$tab_eleves_OOo[$nb_eleve]['LV_SC'][$loop]=\$tab_eleves_OOo[$nb_eleve][$num_lv_ou_sc][$loop]=".$tab_eleves_OOo[$nb_eleve][$num_lv_ou_sc][$loop]."<br />\n";
							}
						}

						$tab_eleves_OOo[$nb_eleve]['totalpoints']-=$tab_eleves_OOo[$nb_eleve][$num_matiere_a_decompter][1];
						$tab_eleves_OOo[$nb_eleve]['totalcoef']-=$tab_champs_OOo[$num_matiere_a_decompter][3]*20;
						$tab_eleves_OOo[$nb_eleve]['totalpoints_bis']-=$tab_eleves_OOo[$nb_eleve][$num_matiere_a_decompter][1];
						$tab_eleves_OOo[$nb_eleve]['totalcoef_bis']-=$tab_champs_OOo[$num_matiere_a_decompter][3]*20;
						//$tab_eleves_OOo[$nb_eleve]['totalSansHistoireArts']-=$tab_eleves_OOo[$nb_eleve][$num_matiere_a_decompter][1];
						//$tab_eleves_OOo[$nb_eleve]['totalcoefSansHistoireArts']-=$tab_champs_OOo[$num_matiere_a_decompter][3]*20;

					}
					else {
						$num_lv_ou_sc=103;

						for($loop=0;$loop<$max_indice;$loop++) {
							if(isset($tab_eleves_OOo[$nb_eleve][$num_lv_ou_sc][$loop])) {
								$tab_eleves_OOo[$nb_eleve]['LV_SC'][$loop]=$tab_eleves_OOo[$nb_eleve][$num_lv_ou_sc][$loop];
								//echo "2 \$tab_eleves_OOo[$nb_eleve]['LV_SC'][$loop]=\$tab_eleves_OOo[$nb_eleve][$num_lv_ou_sc][$loop]=".$tab_eleves_OOo[$nb_eleve][$num_lv_ou_sc][$loop]."<br />\n";
							}
						}
					}
				}
				else {
					$num_lv_ou_sc=104;

					for($loop=0;$loop<$max_indice;$loop++) {
						if(isset($tab_eleves_OOo[$nb_eleve][$num_lv_ou_sc][$loop])) {
							$tab_eleves_OOo[$nb_eleve]['LV_SC'][$loop]=$tab_eleves_OOo[$nb_eleve][$num_lv_ou_sc][$loop];
							//echo "3 \$tab_eleves_OOo[$nb_eleve]['LV_SC'][$loop]=\$tab_eleves_OOo[$nb_eleve][$num_lv_ou_sc][$loop]=".$tab_eleves_OOo[$nb_eleve][$num_lv_ou_sc][$loop]."<br />\n";
						}
					}
				}
				//echo "</p>\n";

			}
			*/

			// Socle commun
			// Initialisation
			$tab_indices_socle=array('116', '116A', '116B', '116C', '116D', '116E', '116F', '116G');
			for($loop=0;$loop<count($tab_indices_socle);$loop++) {
				$tab_eleves_OOo[$nb_eleve]['sc'][$tab_indices_socle[$loop]]="";
				//echo "\$tab_eleves_OOo[$nb_eleve]['sc']['$loop']=".$tab_eleves_OOo[$nb_eleve]['sc']["$loop"]."<br />\n";
			}
			$tab_eleves_OOo[$nb_eleve]['sc']['nbItemValide']=0;

			$sql="SELECT * FROM notanet_socle_commun WHERE login='$lig1->login';";
			$res_socle=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_socle)>0) {
				$ligne=0;
				while($lig_socle=mysqli_fetch_object($res_socle)) {
					$tab_eleves_OOo[$nb_eleve]['sc']["$lig_socle->champ"]=$lig_socle->valeur;
					//echo "\$tab_eleves_OOo[$nb_eleve]['sc']['$lig_socle->champ']=".$tab_eleves_OOo[$nb_eleve]['sc']["$lig_socle->champ"]."<br />\n";
					if (($lig_socle->valeur =="MS")&&($ligne!=0)){
						$tab_eleves_OOo[$nb_eleve]['sc']['nbItemValide']++;
					}
					$ligne++;
				}
			}

			$nb_eleve=$nb_eleve+1;
		}
		// Fin de la boucle sur la liste des élèves
	}

	// FIN DE LA BOUCLE SUR LA LISTE DES CLASSES
}

/*
echo "<pre>";
print_r($tab_eleves_OOo);
echo "</pre>";
*/

// Tri
if((isset($_POST['tri']))&&($_POST['tri']=='alpha')) {
	$tmp_tab_eleves_OOo=$tab_eleves_OOo;

	/*
	echo "<table><tr>";
	echo "<td>";
	*/

	$rg=array();
	$tab_nom_prenom_classe=array();
	for($i=0;$i<count($tab_eleves_OOo);$i++) {
		$rg[$i]=$i;
		$tab_nom_prenom_classe[$i]=$tab_eleves_OOo[$i]['nom']." ".$tab_eleves_OOo[$i]['prenom']." ".$tab_eleves_OOo[$i]['classe'];
		//echo "\$tab_nom_prenom_classe[$i]=".$tab_nom_prenom_classe[$i]."<br />\n";
	}

	array_multisort ($tab_nom_prenom_classe, SORT_ASC, SORT_STRING, $rg, SORT_ASC, SORT_NUMERIC);

	/*
	echo "</td>";
	echo "<td>";
	for($i=0;$i<count($tab_eleves_OOo);$i++) {
		echo "\$rg[$i]=".$rg[$i]."<br />\n";
	}
	echo "</td>";
	echo "<td>";
	for($i=0;$i<count($tab_eleves_OOo);$i++) {
		echo "\$tab_nom_prenom_classe[$i]=".$tab_nom_prenom_classe[$i]."<br />\n";
	}
	echo "</td>";
	echo "<td>";
	for($i=0;$i<count($tab_eleves_OOo);$i++) {
		//echo "\$tab_nom_prenom_classe[\$rg[$i]]="."\$tab_nom_prenom_classe[".$rg[$i]."]=".$tab_nom_prenom_classe[$rg[$i]]."<br />\n";
		echo "\$tab_eleves_OOo[\$rg[$i]]['nom']="."\$tab_eleves_OOo[".$rg[$i]."]['nom']=".$tab_eleves_OOo[$rg[$i]]['nom']."<br />\n";
	}
	echo "</td>";
	echo "</tr></table>";
	*/

	unset($tab_eleves_OOo);

	for($i=0;$i<count($tmp_tab_eleves_OOo);$i++) {
		$tab_eleves_OOo[$i]=$tmp_tab_eleves_OOo[$rg[$i]];
	}
}
//================================
// === Fin construction du tableau fiche brevet===
//================================


?>
