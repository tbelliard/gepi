<?php
/* $Id$ */
/*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
};





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

	if($msg==""){$msg="Enregistrement effectué.";}
}



//**************** EN-TETE *****************
$titre_page = "Fiches Brevet";
//echo "<div class='noprint'>\n";
require_once("../../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************



// Récupération des variables:
// Tableau des classes:
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$type_brevet = isset($_POST['type_brevet']) ? $_POST['type_brevet'] : (isset($_GET['type_brevet']) ? $_GET['type_brevet'] : NULL);
if(isset($type_brevet)) {
	if((!ereg("[0-9]",$type_brevet))||(strlen(ereg_replace("[0-9]","",$type_brevet))!=0)) {
		$type_brevet=NULL;
	}
}

$avec_app=isset($_POST['avec_app']) ? $_POST['avec_app'] : "n";


/* PARAMÉTRAGE GENERAL DES FICHES BREVETS */

if (isset($_GET['parametrer'])) {
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

	echo "</table>\n";
	
	echo "<p align='center'><input type='submit' name='enregistrer_param' value='Enregistrer' /></p>\n";
	echo "</form>\n";

	require("../../lib/footer.inc.php");
	die();

}

/* FIN DU PARAMETRAGE  GENERAL */








/* VÉRIFICATION QUE DES ÉLÈVES SONT BIEN AFFEVTÉS À UN BREVET*/

$sql="SELECT DISTINCT type_brevet FROM notanet_ele_type ORDER BY type_brevet;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune association élève/type de brevet n'a encore été réalisée.<br />Commencez par <a href='../select_eleves.php'>sélectionner les élèves</a></p>\n";

	require("../../lib/footer.inc.php");
	die();
}

$sql="SELECT DISTINCT type_brevet FROM notanet_corresp ORDER BY type_brevet;";
$res=mysql_query($sql);
$nb_type_brevet=mysql_num_rows($res);

if($nb_type_brevet==0) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune association matières/type de brevet n'a encore été réalisée.<br />Commencez par <a href='../select_matieres.php'>sélectionner les matières</a></p>\n";

	require("../../lib/footer.inc.php");
	die();
}

/* FIN DE LA VÉRIFICATION */


/* CHOIX DU BREVET À TRAITER */

// Bibliothèque pour Notanet et Fiches brevet
include("../lib_brevets.php");

if(!isset($type_brevet)) {

	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../../accueil.php'>Accueil</a> | <a href='../index.php'>Accueil Notanet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?parametrer=y'>Paramètrer</a>";
	echo "</p>\n";
	echo "</div>\n";

	echo "<ul>\n";
	while($lig=mysql_fetch_object($res)) {
		echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>Générer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
	}
	echo "</ul>\n";

	require("../../lib/footer.inc.php");
	die();
}

/* FIN DU CHOIX DU BREVET */



// Adresse établissement:
$gepiSchoolName=getSettingValue("gepiSchoolName");
$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1");
$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2");
$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode");
$gepiSchoolCity=getSettingValue("gepiSchoolCity");


$tabmatieres=array();
for($j=101;$j<=122;$j++){
	$tabmatieres[$j]=array();
}



//*****************************************************************************************************************************************

/* CHOIX DE LA CLASSE */
if (!isset($id_classe)) {
	// Choix de la classe:
	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../../accueil.php'>Accueil</a>";
	echo " | <a href='../index.php'>Accueil Notanet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Type de brevet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?parametrer=y'>Paramètrer</a>";
	echo "</p>\n";
	echo "</div>\n";


	// Les tables notanet ne sont pas renseignées, on s'arrête
	$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, notanet n,notanet_ele_type net WHERE p.id_classe = c.id AND c.id=n.id_classe AND n.login=net.login AND net.type_brevet='$type_brevet' ORDER BY classe");
	if(!$call_data){
		echo "<p><font color='red'>Attention:</font> Il semble que vous n'ayez pas mené la procédure notanet à son terme.<br />Cette procédure renseigne des tables requises pour générer les fiches brevet.<br />Effectuez la <a href='../index.php'>procédure notanet</a>.</p>\n";

		require("../../lib/footer.inc.php");
		die();
	}
	$nombre_lignes = mysql_num_rows($call_data);

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
		$classe = mysql_result($call_data, $i, "classe");
		$ide_classe = mysql_result($call_data, $i, "id");
		echo "<option value='$ide_classe'";
		if($nombre_lignes==1) {echo " selected";}
		echo ">$classe</option>\n";
		$i++;
	}
	echo "</select></p>\n";
	echo "<p>\n<label id='avec_app_label' style='cursor: pointer;'><input type='checkbox' name='avec_app' id='avec_app' value='y' checked='checked' /> Avec les appréciations</label>\n";
	echo "<input type='submit' name='choix_classe' value='Envoyer' />\n</p>\n";
	
	echo "</blockquote>\n";
	echo "</form>\n";
	
}
// FIN DU FORMULAIRE DE CHOIX DES CLASSES
//*****************************************************************************************************************************************
else {
	// DEBUT DE L'AFFICHAGE DES FICHES BREVET POUR LES CLASSES CHOISIES ET POUR LE TYPE_BREVET CHOISI
	
	// menu retour
	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../../accueil.php'>Accueil</a>";
	echo " | <a href='../index.php'>Accueil Notanet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Type de brevet</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?type_brevet=$type_brevet'>Choix des classes</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?parametrer=y'>Paramétrer</a>";
	echo "</p>\n";
	echo "</div>\n";
	// fin menu retour

	// On récupère le tableau des paramètres associés à ce type de brevet:
	$tabmatieres=tabmatieres($type_brevet);
	
	// tableau de correspondance Champs de $tabmatieres -> champs de publipostage OOo
	$tab_champs_OOo=array();
	for($j=101;$j<=122;$j++){
		if($tabmatieres[$j][0]!=''){
			$tab_champs_OOo[$j]=array();
			$tab_champs_OOo[$j][0]=$j;												// code de la matière
			$tab_champs_OOo[$j][1]=$tabmatieres[$j][0];			// Nom long
			$tab_champs_OOo[$j][2]="fb_note_".$j;						// Nom variable OOo
			switch($tabmatieres[$j][-1]){											// Coefficient
				case "NOTNONCA":																// Note non comptabilisée
					$tab_champs_OOo[$j][3]="-1";
				break;
				case "PTSUP":																		// Seuls les points au dessus de la moyenne sont comptabilisés
					$tab_champs_OOo[$j][3]="0";
				break;
				case "POINTS":																		// On récupère le coef
					if ($tabmatieres[$j]['socle']=='n'){
						$tab_champs_OOo[$j][3]=$tabmatieres[$j][-2];	// On récupère le coef
					} else{
						$tab_champs_OOo[$j][3]="-2";									// cas du B2I et A2 langue
					}
				break;
			}
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
		for($j=101;$j<=122;$j++){
			if($tabmatieres[$j][0]!=''){
				$sql="SELECT ROUND(AVG(note),1) moyenne FROM notanet WHERE note!='DI' AND note!='AB' AND note!='NN' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
				$res_moy=mysql_query($sql);
				if(mysql_num_rows($res_moy)>0){
					$lig_moy=mysql_fetch_object($res_moy);
					$moy_classe[$j]=$lig_moy->moyenne;
				}
				else{
					$moy_classe[$j]="";
				}
			}
		}


		// Récupération du statut des matières : ceux validés lors du traitement NOTANET
		// pour repérer les matières non dispensées.
		for($j=101;$j<=122;$j++){
			if($tabmatieres[$j][0]!=''){
				$sql="SELECT * FROM notanet_corresp WHERE notanet_mat='".$tabmatieres[$j][0]."' AND type_brevet='$type_brevet' LIMIT 1";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0){
					$lig=mysql_fetch_object($res);
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

		$sql="SELECT DISTINCT e.* FROM eleves e,
										notanet n,
										notanet_ele_type net
								WHERE n.id_classe='$id_classe[$i]' AND
										n.login=e.login AND
										net.login=n.login AND
										net.type_brevet='$type_brevet'
								ORDER BY e.login;";
		$res1=mysql_query($sql);
		if(mysql_num_rows($res1)>0){
			// Boucle sur la liste des élèves
			while($lig1=mysql_fetch_object($res1)){

				$tab_eleves_OOo[$nb_eleve]=array();
				$tab_eleves_OOo[$nb_eleve]['nom']=$lig1->nom;
				$tab_eleves_OOo[$nb_eleve]['prenom']=$lig1->prenom;
				if($lig1->sexe=='F') {
					$tab_eleves_OOo[$nb_eleve]['fille']="e";  // ajouter un e à née si l'élève est une fille
				}
				else {
					$tab_eleves_OOo[$nb_eleve]['fille']="";  // ajouter un e à née si l'élève est une fille
				}
				$tab_eleves_OOo[$nb_eleve]['date_nais']=formate_date($lig1->naissance);
				if($ele_lieu_naissance=="y") {$tab_eleves_OOo[$nb_eleve]['lieu_nais']=get_commune($lig1->lieu_naissance,1);} // récupérer la commune
				$tab_eleves_OOo[$nb_eleve]['ecole']=$gepiSchoolName;
				$tab_eleves_OOo[$nb_eleve]['adresse1']=$gepiSchoolAdress1;
				$tab_eleves_OOo[$nb_eleve]['adresse2']=$gepiSchoolAdress2;
				$tab_eleves_OOo[$nb_eleve]['codeposte']=$gepiSchoolZipCode;
				$tab_eleves_OOo[$nb_eleve]['commune']=$gepiSchoolCity;
				$tab_eleves_OOo[$nb_eleve]['acad']=$fb_academie;
				$tab_eleves_OOo[$nb_eleve]['departe']=$fb_departement;
				$tab_eleves_OOo[$nb_eleve]['session']=$fb_session;
				
				$sql="SELECT doublant FROM j_eleves_regime WHERE login='".$lig1->login."';";
				$res_reg=mysql_query($sql);
				$doublant='n';
				if(mysql_num_rows($res_reg)>0) {
					$lig_reg=mysql_fetch_object($res_reg);
					if($lig_reg->doublant=='R') {
						$doublant='y';
					}
				}
				$tab_eleves_OOo[$nb_eleve]['doublant']=$doublant;
	
				//=======================================
								
				if(($type_brevet==0)||($type_brevet==1)||($type_brevet==5)||($type_brevet==6)){

					$TOTAL=0;
					$TOTAL_COEF=0;
					$TOTAL_POINTS=0;
					for($j=101;$j<=122;$j++){
						if ($tab_champs_OOo[$j][0]!='') {
						
							$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
							$res_note=mysql_query($sql);
							if(mysql_num_rows($res_note)>0){
								$lig_note=mysql_fetch_object($res_note);
								$tab_eleves_OOo[$nb_eleve][$j]=array();
								$tab_eleves_OOo[$nb_eleve][$j][0]=$lig_note->note;			// On récupère la note
								switch($tab_champs_OOo[$j][3]){
									case '-2':																							// Socle B2I et A2
									break;
									case '-1':																							// Note non prise en compte dans le calcul
										// on calcule la moyenne de la matière
										include("fb_moyenne.inc.php");	
										// on va chercher les appréciations si besoin
										include("fb_appreciation.inc.php");				
									break;			
									case '0':																							// Seuls les points au dessus de la moyenne comptent
										// on cherche le nom de l'option
										$sql_mat_fac="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
										$res_mat_fac=mysql_query($sql_mat_fac);
										if(mysql_num_rows($res_mat_fac)>0){
											$lig_mat_fac=mysql_fetch_object($res_mat_fac);
											$tab_eleves_OOo[$nb_eleve][$j][2]=ucfirst(accent_min(strtolower($lig_mat_fac->matiere)));
										}
										// on calcule la moyenne de la matière
										include("fb_moyenne.inc.php");
										
										// on va chercher les appréciations si besoin
										include("fb_appreciation.inc.php");
										
										// on extrait les points à ajouter
										if($tab_eleves_OOo[$nb_eleve][$j][0]>10) {
											$tab_eleves_OOo[$nb_eleve][$j][1]=($lig_note->note)-10;
											$TOTAL_POINTS= $TOTAL_POINTS+$tab_eleves_OOo[$nb_eleve][$j][1];
										}
									break;				
									default:																						
										// On calcul la note coefficientée
										if($tab_eleves_OOo[$nb_eleve][$j][0]!="DI" && $tab_eleves_OOo[$nb_eleve][$j][0]!="NN" && $tab_eleves_OOo[$nb_eleve][$j][0]!="ABS") {
											$tab_eleves_OOo[$nb_eleve][$j][1]=($lig_note->note)*$tab_champs_OOo[$j][3];
											$TOTAL_POINTS =$TOTAL_POINTS+$tab_eleves_OOo[$nb_eleve][$j][1];
											$TOTAL_COEF= $TOTAL_COEF+$tab_champs_OOo[$j][3];
										}else {
											$tab_eleves_OOo[$nb_eleve][$j][1]="";
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
						}
					}					
				}
				// ************************************************************************************************
				// ************************************************************************************************
				// ************************************************************************************************	
				
				// AUTRE TYPE DE BREVET
				elseif(($type_brevet==2)||($type_brevet==3)){
					// à faire
				}
				// ************************************************************************************************
				// ************************************************************************************************
				// ************************************************************************************************
				// AUTRE TYPE DE BREVET
				elseif(($type_brevet==4)||($type_brevet==7)){
					// à faire
				}
				else{
					echo "<p>BIZARRE! Ce type de brevet n'est pas prévu</p>";
				}
				
				// ************************************************************************************************
				//	 On récupère l'avis du chef d'établissement
					$sql="SELECT * FROM notanet_avis WHERE login='$lig1->login';";
					$res_avis=mysql_query($sql);
					if(mysql_num_rows($res_avis)>0) {
						$lig_avis=mysql_fetch_object($res_avis);
						if($lig_avis->favorable=="O") {$tab_eleves_OOo[$nb_eleve]['decision']="Avis favorable.";}
						elseif($lig_avis->favorable=="N") {$tab_eleves_OOo[$nb_eleve]['decision']="Avis défavorable.";}
						$tab_eleves_OOo[$nb_eleve]['appreciation']= htmlentities($lig_avis->avis);
					}
					
					$tab_eleves_OOo[$nb_eleve]['totalpoints']=$TOTAL_POINTS;
					$tab_eleves_OOo[$nb_eleve]['totalcoef']=$TOTAL_COEF*20;
					$tab_eleves_OOo[$nb_eleve]['classe']=get_classe_from_id($id_classe[$i]);
				
					
				
				$nb_eleve=$nb_eleve+1;
			}
			// Fin de la boucle sur la liste des élèves
		}

		// FIN DE LA BOUCLE SUR LA LISTE DES CLASSES
	}


		//=======================================
		// AFFICHAGE DES DONNÉES
		//=======================================
	for($ne=0;$ne<$nb_eleve;$ne++){
		// affichage des données générales
		echo "<p>".$tab_eleves_OOo[$ne]['acad']." - ";
		echo $tab_eleves_OOo[$ne]['departe']." - ";
		echo "session ".$tab_eleves_OOo[$ne]['session']." - ";
		echo $tab_eleves_OOo[$ne]['classe']. "</p>";

		// affichage des données élève
		echo "<p>".$tab_eleves_OOo[$ne]['nom']." ";
		echo $tab_eleves_OOo[$ne]['prenom']." - ";
		echo "né".$tab_eleves_OOo[$ne]['fille'];
		echo " le ".$tab_eleves_OOo[$ne]['date_nais'];
		echo " à ".$tab_eleves_OOo[$ne]['lieu_nais'];
		echo " - Doublant : ".$tab_eleves_OOo[$ne]['doublant']."</p>";
		echo "<p>".$tab_eleves_OOo[$ne]['ecole']."</p>";
		echo "<p>".$tab_eleves_OOo[$ne]['adresse1'];
		echo $tab_eleves_OOo[$ne]['adresse2'];
		echo " - ".$tab_eleves_OOo[$ne]['codeposte'];
		echo " ".$tab_eleves_OOo[$ne]['commune']."</p>";
	
		// affichage des notes et appréciations des élèves
		for($j=101;$j<=122;$j++){
			if ($tab_champs_OOo[$j][0]!="") {
				echo "<p>".$tab_champs_OOo[$j][1]." - ".$tab_champs_OOo[$j][0]." - ".$tab_champs_OOo[$j][3];
				for($l=0;$l<=4;$l++){
					echo " - ".$tab_eleves_OOo[$ne][$j][$l];
				}
				echo "</p>";
			}
		}
		echo "<p>".$tab_eleves_OOo[$ne]['totalpoints']."  sur ".$tab_eleves_OOo[$ne]['totalcoef']."</p>";
		echo "<p>Décision : ".$tab_eleves_OOo[$ne]['decision']." Appréciation : ".$tab_eleves_OOo[$ne]['appreciation'].".</p>";
		
		echo "<p>Fin des données élèves de ".$tab_eleves_OOo[$ne]['nom']." ".$tab_eleves_OOo[$ne]['prenom']." de ".$tab_eleves_OOo[$ne]['classe']."</p>";
		echo "<hr />";
		//=======================================
		// FIN AFFICHAGE DES DONNÉES
		//=======================================
	}




echo "<p><a href='imprime_ooo.php?id_classe[]=".$id_classe."&amp;type_brevet=".$type_brevet."&amp;avec_app=".$avec_app."'>Imprimer</a></p>";


}
// Fermeture du DIV container initialisé dans le header.inc
echo "</div>\n";
require("../../lib/footer.inc.php");
?>
