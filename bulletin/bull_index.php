<?php
/**
 * Edition des bulletins
 *
 * $Id$
 *
 * @copyright Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau, Christian Chapel
 * @todo Les bulletins HTML utilisent les infos display_rang, display_coef,... de la table 'classes'.
 *Les bulletins PDF utilisent plutôt les infos de la table 'modele_bulletin' il me semble.
 *Il faudrait peut-être revoir le dispositif pour adopter la même stratégie.
 *On a aussi ajouté des champs dans la table 'classes' pour les relevés de notes,... faut-il envisager une autre structure ?
 * @package Bulletin
 * @subpackage Edition
 */

/*
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

/**
 * Fichiers d'initialisation
 */
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


$sql="SELECT 1=1 FROM droits WHERE id='/bulletin/bull_index.php';";
$res_test=mysqli_query($GLOBALS["mysqli"], $sql);
if (mysqli_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/bulletin/bull_index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1');";
	$res_insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('active_bulletins')) {
	header("Location: ../accueil.php?msg=Module_inactif");
	die();
}

//================================
$gepi_denom_mention=getSettingValue("gepi_denom_mention");
if($gepi_denom_mention=="") {
	$gepi_denom_mention="mention";
}
//================================

$generer_fichiers_pdf_archivage=isset($_POST['generer_fichiers_pdf_archivage']) ? $_POST['generer_fichiers_pdf_archivage'] : (isset($_GET['generer_fichiers_pdf_archivage']) ? $_GET['generer_fichiers_pdf_archivage'] : "n");

$intercaler_releve_notes=isset($_POST['intercaler_releve_notes']) ? $_POST['intercaler_releve_notes'] : NULL;

$mode_bulletin=isset($_POST['mode_bulletin']) ? $_POST['mode_bulletin'] : NULL;

// Variable non encore utilisée:
$contexte_document_produit="bulletin";
// Pour sur le verso du bulletin n'avoir qu'un relevé de notes et pas deux... et surtout pas celui de l'élève suivant dans la liste:
$nb_releve_par_page=1;

$bull_pdf_debug=isset($_POST['bull_pdf_debug']) ? $_POST['bull_pdf_debug'] : "n";

// HTML ou PDF par défaut:
$type_bulletin_par_defaut=getSettingValue('type_bulletin_par_defaut');
if(($type_bulletin_par_defaut!='html')&&($type_bulletin_par_defaut!='pdf')) {$type_bulletin_par_defaut='html';}

//debug_var();

//====================================================
//=============== ENTETE STANDARD ====================
if (!isset($_POST['valide_select_eleves'])) {
	//**************** EN-TETE *********************
	$titre_page = "Edition des bulletins";
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE *****************
}
//============== FIN ENTETE STANDARD =================
//====================================================
//============== ENTETE BULLETIN HTML ================
elseif ((isset($_POST['mode_bulletin']))&&($_POST['mode_bulletin']=='html')) {
	//=============================================
	// Faire les extractions pour le relevé de notes si jamais cela a été demandé.
	//$intercaler_releve_notes="y";
	if(isset($intercaler_releve_notes)) {
		// On n'extrait les relevés de notes que pour la/les périodes choisies pour les bulletins
		$choix_periode='periode';
		include("../cahier_notes/initialisations_header_releves_html.php");
	}
	//=============================================
	include("header_bulletin_html.php");
}
//============ FIN ENTETE BULLETIN HTML ==============
//====================================================
//============== ENTETE BULLETIN HTML ================
elseif ((isset($_POST['mode_bulletin']))&&($_POST['mode_bulletin']=='pdf')) {

	// DEBUG Décommenter la ligne ci-dessous pour débugger
	//echo "<p style='color:red;'>Insertion d'une ligne avant le Header pour provoquer l'affichage dans le navigateur et ainsi repérer des erreurs.</p>";
	//echo "\$bull_pdf_debug=$bull_pdf_debug<br />";
	if($bull_pdf_debug=='y') {
		echo "<p style='color:red'>DEBUG:<br />
La génération du PDF va échouer parce qu'on affiche ces informations de debuggage,<br />
mais il se peut que vous ayez ainsi des précisions sur ce qui pose problème.<br />
</p>\n";
	}

	include("header_bulletin_pdf.php");

	//=============================================
	// Faire les extractions pour le relevé de notes si jamais cela a été demandé.
	//$intercaler_releve_notes="y";
	if(isset($intercaler_releve_notes)) {
		// On n'extrait les relevés de notes que pour la/les périodes choisies pour les bulletins
		$choix_periode='periode';
		// REVOIR LE HEADER POUR PDF: QUE FAUT-IL EXTRAIRE COMME PARAMETRES SPECIFIQUES AU PDF
		//include("../cahier_notes/initialisations_header_releves_html.php");
		include("header_releve_pdf.php");
	}
	//=============================================

}
//============ FIN ENTETE BULLETIN HTML ==============

//echo "microtime()=".microtime()."<br />";
//echo "time()=".time()."<br />";

$debug="n";
$tab_instant=array();
include("bull_func.lib.php");

if((!isset($_POST['mode_bulletin']))||($_POST['mode_bulletin']!='pdf')) {
	//==============================
	$motif="Duree_totale";
	//decompte_debug($motif,"Témoin de $motif initialisation");
	decompte_debug($motif,"");
	flush();
	//==============================
}

$tab_id_classe=isset($_POST['tab_id_classe']) ? $_POST['tab_id_classe'] : (isset($_GET['tab_id_classe']) ? $_GET['tab_id_classe'] : NULL);
$tab_periode_num=isset($_POST['tab_periode_num']) ? $_POST['tab_periode_num'] : (isset($_GET['tab_periode_num']) ? $_GET['tab_periode_num'] : NULL);
$choix_periode_num=isset($_POST['choix_periode_num']) ? $_POST['choix_periode_num'] : (isset($_GET['choix_periode_num']) ? $_GET['choix_periode_num'] : NULL);

//======================================================
//==================CHOIX DES CLASSES===================
if(!isset($tab_id_classe)) {
	//echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	if((($_SESSION['statut']=='scolarite')&&(getSettingValue('GepiScolImprBulSettings')=='yes'))||
	(($_SESSION['statut']=='professeur')&&(getSettingValue('GepiProfImprBulSettings')=='yes'))||
	(($_SESSION['statut']=='administrateur')&&(getSettingValue('GepiAdminImprBulSettings')=='yes'))||
	(($_SESSION['statut']=='cpe')&&(getSettingValue('GepiCpeImprBulSettings')=='yes'))) {
		if($type_bulletin_par_defaut=='pdf') {
			echo " | <a id='lien_param_bull' href='param_bull_pdf.php' target='_blank'>Paramètres d'impression des bulletins PDF</a>";
		}
		else {
			echo " | <a id='lien_param_bull' href='param_bull.php' target='_blank'>Paramètres d'impression des bulletins</a>";
		}
	}
	
	if((getSettingValue('ancien_dispositif_bulletins')=='y')&&($_SESSION['statut']!='autre')) {
	  echo " | <a href='index.php'>Ancien dispositif</a>";
	}
	
	echo "</p>\n";

	echo "<p class='bold'>Choix des classes:</p>\n";

	if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiProfImprBul")!='yes') {
		echo "<p>Droits insuffisants pour effectuer cette opération</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	if (($_SESSION['statut'] == 'cpe') and getSettingValue("GepiCpeImprBul")!='yes') {
		echo "<p>Droits insuffisants pour effectuer cette opération</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	// Liste des classes avec élève:
	//$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE c.id=jec.id_classe ORDER BY c.classe;";
	if ($_SESSION["statut"] == "scolarite") {
		// On sélectionne les classes associées au compte scolarité
		$sql="SELECT DISTINCT c.* FROM classes c, j_scol_classes jsc, j_eleves_classes jec WHERE (jec.id_classe=c.id AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."') ORDER BY c.classe;";

		$message_0="Aucune classe (<i>avec élève</i>) ne vous est affectée.";
	}
	elseif (($_SESSION["statut"] == "administrateur")||($_SESSION["statut"] == "secours")||($_SESSION["statut"] == "autre")) {
		// On selectionne toutes les classes
		$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) ORDER BY c.classe;";

		$message_0="Aucune classe avec élève n'a été trouvée.";
	}
	elseif ($_SESSION["statut"] == "professeur") {
		$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs jep, j_eleves_classes jec WHERE (jep.professeur='".$_SESSION['login']."' AND jep.login = jec.login AND jec.id_classe = c.id) ORDER BY c.classe;";

			$message_0="Aucune classe (<i>avec élève</i>) ne vous est affectée pour l'édition des bulletins.";
	}
	elseif ($_SESSION["statut"] == "cpe") {
		$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_cpe jecpe, j_eleves_classes jec WHERE (jecpe.cpe_login='".$_SESSION['login']."' AND jecpe.e_login = jec.login AND jec.id_classe = c.id) ORDER BY c.classe;";

			$message_0="Aucune classe (<i>avec élève</i>) ne vous est affectée pour l'édition des bulletins.";
	}
	else {
		// On ne devrait pas arriver jusque-là...
		echo "<p>Droits insuffisants pour effectuer cette opération</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$call_classes=mysqli_query($GLOBALS["mysqli"], $sql);

	$nb_classes=mysqli_num_rows($call_classes);
	if($nb_classes==0){
		//echo "<p>Aucune classe avec élève affecté n'a été trouvée.</p>\n";
		echo "<p>".$message_0."</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	// Affichage sur 3 colonnes
	$nb_classes_par_colonne=round($nb_classes/3);

	echo "<table width='100%' summary='Choix des classes'>\n";
	echo "<tr valign='top' align='center'>\n";

	$cpt = 0;

	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";

	while($lig_clas=mysqli_fetch_object($call_classes)) {

		//affichage 2 colonnes
		if(($cpt>0)&&(round($cpt/$nb_classes_par_colonne)==$cpt/$nb_classes_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='tab_id_classe[]' id='tab_id_classe_$cpt' value='$lig_clas->id' onchange='change_style_classe($cpt)' /> $lig_clas->classe</label>";
		echo "<br />\n";
		$cpt++;
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('tab_id_classe_'+k)){
				document.getElementById('tab_id_classe_'+k).checked = mode;
				change_style_classe(k);
			}
		}
	}

	function change_style_classe(num) {
		if(document.getElementById('tab_id_classe_'+num)) {
			if(document.getElementById('tab_id_classe_'+num).checked) {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";


}
//======================================================
//=================CHOIX DES PERIODES===================
//elseif(!isset($tab_periode_num)) {
//elseif(!isset($choix_periode_num)) {
elseif((!isset($choix_periode_num))||(!isset($tab_periode_num))) {
	//debug_var();

	echo "<p class='bold'>";
	//echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
	echo "<a href='bull_index.php'>Choisir d'autres classes</a>\n";
	if((($_SESSION['statut']=='scolarite')&&(getSettingValue('GepiScolImprBulSettings')=='yes'))||
	(($_SESSION['statut']=='professeur')&&(getSettingValue('GepiProfImprBulSettings')=='yes'))||
	(($_SESSION['statut']=='administrateur')&&(getSettingValue('GepiAdminImprBulSettings')=='yes'))||
	(($_SESSION['statut']=='cpe')&&(getSettingValue('GepiCpeImprBulSettings')=='yes'))) {
		if($type_bulletin_par_defaut=='pdf') {
			echo " | <a id='lien_param_bull' href='param_bull_pdf.php' target='_blank'>Paramètres d'impression des bulletins PDF</a>";
		}
		else {
			echo " | <a id='lien_param_bull' href='param_bull.php' target='_blank'>Paramètres d'impression des bulletins</a>";
		}
	}
	echo "</p>\n";

	// Choisir les périodes permettant l'édition de bulletin

	echo "<p class='bold'>Choix des périodes:</p>\n";

	/*
	$sql="SELECT MAX(num_periode) max_per FROM periodes;";
	$res_max_per=mysql_query($sql);
	$lig_max_per=mysql_fetch_object($res_max_per);
	$max_per=$lig_max_per->max_per;
	*/

	$tab_periode_num_excluses=array();

	//$tab_periode_num=array();

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

	$max_per=0;
	for($i=0;$i<count($tab_id_classe);$i++) {
		// Est-ce bien un entier?
		if((mb_strlen(preg_replace("/[0-9]/","",$tab_id_classe[$i])))||($tab_id_classe[$i]=="")) {
			echo "<p>Identifiant de classe erroné: <span style='color:red'>".$tab_id_classe[$i]."</span></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";

		$sql="SELECT p.* FROM periodes p WHERE p.id_classe='".$tab_id_classe[$i]."' ORDER BY num_periode;";
		$res_per=mysqli_query($GLOBALS["mysqli"], $sql);

		if(mysqli_num_rows($res_per)>0) {
			$ligne_debug[$i]="<tr><td>".get_class_from_id($tab_id_classe[$i])."</td>\n";
			while($lig_per=mysqli_fetch_object($res_per)) {
				if($lig_per->verouiller=="O") {
					$ligne_debug[$i].="<td style='background-color:lightgreen; text-align:center;'>Close</td>\n";
				}
				elseif($lig_per->verouiller=="N") {
					$ligne_debug[$i].="<td style='background-color:red; text-align:center;'>Non close</td>\n";
					if(!in_array($lig_per->num_periode,$tab_periode_num_excluses)) {$tab_periode_num_excluses[]=$lig_per->num_periode;}
					/*
					if($_SESSION['statut']=='scolarite') {
						$ligne_debug[$i].=" <a href='verrouillage.php' target='_blank'><img src='../images/icons/configure.png' width='16' height='16' title='Verrouillage/déverrouillage'/></a>\n";
					}
					*/
				}
				else {
					$ligne_debug[$i].="<td style='background-color:orange; text-align:center;'>Partiellement close</td>\n";
				}

				if($lig_per->num_periode>$max_per) {$max_per=$lig_per->num_periode;}
			}
			$ligne_debug[$i].="</tr>\n";
		}
	}



	echo "<table class='boireaus' summary='Choix des périodes'>\n";
	echo "<tr>\n";
	echo "<th>Classe</th>\n";
	for($i=1;$i<=$max_per;$i++) {
		echo "<th>Période $i</th>\n";
	}
	echo "</tr>\n";

	for($i=0;$i<count($tab_id_classe);$i++) {
		echo $ligne_debug[$i];
	}

	echo "<tr>\n";
	echo "<th>Choix</th>\n";
	for($i=1;$i<=$max_per;$i++) {
		if(!in_array($i,$tab_periode_num_excluses)) {
			echo "<td style='background-color:lightgreen; text-align:center;'><input type='checkbox' name='tab_periode_num[]' value='$i' /></td>\n";
		}
		else {
			echo "<td style='background-color:red; text-align:center;'>Période non close<br />pour une classe au moins";
			// 20120713
			echo "<br /><input type='checkbox' name='tab_periode_num[]' value='$i' title=\"ATTENTION: Les notes et appréciations des bulletins peuvent encore évoluer\" />\n";
			if($_SESSION['statut']=='scolarite') {
				echo " <a href='verrouillage.php' target='_blank'><img src='../images/icons/configure.png' width='16' height='16' title='Verrouillage/déverrouillage'/></a>\n";
			}
			echo "</td>\n";
		}
	}
	echo "</tr>\n";

	echo "</table>\n";

	echo "<input type='hidden' name='choix_periode_num' value='fait' />\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

}
//======================================================
//==============CHOIX DE LA SELECTION D'ELEVES==========
elseif(!isset($_POST['valide_select_eleves'])) {

	//debug_var();

	$preselection_eleves=isset($_POST['preselection_eleves']) ? $_POST['preselection_eleves'] : (isset($_GET['preselection_eleves']) ? $_GET['preselection_eleves'] : NULL);

	echo "<p class='bold'>";
	echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
	//echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres périodes</a>\n";
	//echo " | <a href='#' onClick='history.go(-1);'>Choisir d'autres périodes</a>\n";
	echo " | <a href='".$_SERVER['PHP_SELF']."' onClick=\"document.forms['form_retour'].submit();return false;\">Choisir d'autres périodes</a>\n";
	if((($_SESSION['statut']=='scolarite')&&(getSettingValue('GepiScolImprBulSettings')=='yes'))||
	(($_SESSION['statut']=='professeur')&&(getSettingValue('GepiProfImprBulSettings')=='yes'))||
	(($_SESSION['statut']=='administrateur')&&(getSettingValue('GepiAdminImprBulSettings')=='yes'))||
	(($_SESSION['statut']=='cpe')&&(getSettingValue('GepiCpeImprBulSettings')=='yes'))) {
		if($type_bulletin_par_defaut=='pdf') {
			echo " | <a id='lien_param_bull' href='param_bull_pdf.php' target='_blank'>Paramètres d'impression des bulletins PDF</a>";
		}
		else {
			echo " | <a id='lien_param_bull' href='param_bull.php' target='_blank'>Paramètres d'impression des bulletins</a>";
		}
	}
	echo "</p>\n";

	//===========================
	// FORMULAIRE POUR LE RETOUR AU CHOIX DES PERIODES
	echo "\n<!-- Formulaire de retour au choix des périodes -->\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='form_retour'>\n";
	$temoin_periode_non_close="n";
	$tab_per_non_close=array();
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";
		//if($temoin_periode_non_close=="n") {
		for($j=0;$j<count($tab_periode_num);$j++) {
			$sql="SELECT 1=1 FROM periodes WHERE id_classe='".$tab_id_classe[$i]."' AND verouiller='N' AND num_periode='".$tab_periode_num[$j]."';";
			$test_per=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test_per)>0) {
				$tab_per_non_close[$tab_id_classe[$i]][]=$tab_periode_num[$j];
				$temoin_periode_non_close="y";
			}
		}
	}
	for($j=0;$j<count($tab_periode_num);$j++) {
		echo "<input type='hidden' name='tab_periode_num[$j]' value='".$tab_periode_num[$j]."' />\n";
	}
	echo "</form>\n";
	//===========================

	if($temoin_periode_non_close=="y") {
		echo "<br /><p style='text-indent:-7em; margin-left:7em;'><strong style='color:red; text-decoration:blink;'>ATTENTION&nbsp;:</strong> Les saisies ne sont pas closes (<em>période encore ouverte en saisie</em>).<br />Cela signifie que les notes et appréciations peuvent encore changer.<br />Les bulletins vont être marqués d'une indication comme quoi la période n'est pas close.<br />Vous ne devriez pas imprimer ces bulletins.<br />Vous pouvez tester l'affichage pour ajuster les paramètres d'impression, mais vous devriez verrouiller la période avec un compte 'scolarité' avant d'imprimer les bulletins.</p><br />\n";
	}

	//echo "<p class='bold'>Sélection des élèves:</p>\n";
	echo "<p class='bold'>Sélection des élèves et paramètres:</p>\n";

	echo "\n<!-- Formulaire de sélection des élèves et de paramétrage -->\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire' target='_blank'>\n";

	//=======================================

	/*
	// HTML ou PDF
	$type_bulletin_par_defaut=getSettingValue('type_bulletin_par_defaut');
	if(($type_bulletin_par_defaut!='html')&&($type_bulletin_par_defaut!='pdf')) {$type_bulletin_par_defaut='html';}
	*/

	// A remplacer par la suite par un choix:
	//echo "<input type='hidden' name='mode_bulletin' value='html' />\n";
	echo "<table border='0' summary='Choix du type de bulletin'>\n";
	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='mode_bulletin' id='mode_bulletin_html' value='html' onchange='display_div_modele_bulletin_pdf();display_param_b_adr_pg();checkbox_change(this.id);checkbox_change(\"mode_bulletin_pdf\");change_lien_param_bull(\"html\");griser_lignes_specifiques_pdf();' ";
	if($type_bulletin_par_defaut=='html') {echo "checked ";}
	echo "/> ";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='mode_bulletin_html' id='texte_mode_bulletin_html' style='cursor:pointer;'>Bulletin HTML</label>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	$test = sql_query1("SHOW TABLES LIKE 'modele_bulletin';");
	if ($test == -1) {
		echo "&nbsp;</td>\n";
		echo "<td valign='top'>\n";
		echo "Les modèles PDF utilisent une table 'modele_bulletin' qui semble absente.<br />\n";
		if($_SESSION['statut']=='administrateur') {
			echo "Visitez la page de <a href='test_modele_bull.php'>création de cette table</a> d'après l'ancienne table 'model_bulletin' pour permettre l'impression de bulletins PDF.";
		}
		else {
			echo "Le remplissage de la table doit être effectué en admin.";
		}
	}
	else {
		$sql="SELECT 1=1 FROM modele_bulletin LIMIT 1;";
		//echo "$sql<br />";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			echo "&nbsp;</td>\n";
			echo "<td valign='top'>\n";
			echo "Les modèles PDF utilisent une table 'modele_bulletin' qui semble absente.<br />\n";
			if($_SESSION['statut']=='administrateur') {
				echo "Visitez la page de <a href='test_modele_bull.php'>création de cette table</a> d'après l'ancienne table 'model_bulletin' pour permettre l'impression de bulletins PDF.";
			}
			else {
				echo "Le remplissage de la table doit être effectué en admin.";
			}
		}
		else {
			echo "<input type='radio' name='mode_bulletin' id='mode_bulletin_pdf' value='pdf' onchange='display_div_modele_bulletin_pdf();display_param_b_adr_pg();checkbox_change(this.id);checkbox_change(\"mode_bulletin_html\");change_lien_param_bull(\"pdf\");griser_lignes_specifiques_html();' ";
			if($type_bulletin_par_defaut=='pdf') {echo "checked ";}
			echo "/> ";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='mode_bulletin_pdf' id='texte_mode_bulletin_pdf' style='cursor:pointer;'>Bulletin PDF</label>\n";

			echo "<br />\n";
			//echo "<span id='div_modele_bulletin_pdf'>\n";
			echo "<div id='div_modele_bulletin_pdf'>\n";

				$option_modele_bulletin=getSettingValue("option_modele_bulletin");
				if (($option_modele_bulletin==2)||($option_modele_bulletin==3)) { //Par défaut  le modèle défini pour les classes
					echo "Choisir le modèle de bulletin<br />\n";
					// sélection des modèles des bulletins PDF
					//$sql='SELECT id_model_bulletin, nom_model_bulletin FROM modele_bulletin ORDER BY modele_bulletin.nom_model_bulletin ASC';
					$sql="SELECT DISTINCT id_model_bulletin,valeur FROM modele_bulletin WHERE nom='nom_model_bulletin' ORDER BY id_model_bulletin ASC";
					//echo "$sql<br />";
					$requete_modele = mysqli_query($GLOBALS["mysqli"], $sql);
					echo "<select tabindex=\"5\" name=\"type_bulletin\">";
					$option_modele_bulletin=getSettingValue("option_modele_bulletin");
					if ($option_modele_bulletin==2) { //Par défaut  le modèle défini pour les classes
						echo "<option value=\"-1\">Utiliser les modèles pré-sélectionnés par classe</option>\n";
					}
	
					while($donner_modele = mysqli_fetch_array($requete_modele)) {
						echo "<option value=\"".$donner_modele['id_model_bulletin']."\"";
						echo ">".ucfirst($donner_modele['valeur'])."</option>\n";
					}
					echo "</select>\n";
					echo "<br />\n";
				}
				else {
					echo "<input type='hidden' name='type_bulletin' value='-1' />\n";
				}
			//echo "</span>\n";

			echo "<input type='checkbox' name='use_cell_ajustee' id='use_cell_ajustee' value='n' onchange=\"checkbox_change(this.id)\" /><label for='use_cell_ajustee' id='texte_use_cell_ajustee' style='cursor: pointer;'> Ne pas utiliser la nouvelle fonction use_cell_ajustee() pour l'écriture des appréciations.</label>";

			$titre_infobulle="Fonction cell_ajustee()\n";
			$texte_infobulle="Pour les appréciations sur les bulletins, relevés,... on utilisait auparavant la fonction DraxTextBox() de FPDF.<br />Cette fonction avait parfois un comportement curieux avec des textes tronqués ou beaucoup plus petits dans la cellule que ce qui semblait pouvoir tenir dans la case.<br />La fonction cell_ajustee() est une fonction que mise au point pour tenter de faire mieux que DraxTextBox().<br />Comme elle n'a pas été expérimentée par suffisamment de monde sur trunk, nous avons mis une case à cocher qui permet d'utiliser l'ancienne fonction DrawTextBox() si cell_ajustee() ne se révélait pas aussi bien fichue que nous l'espérons;o).<br />\n";
			//$texte_infobulle.="\n";
			$tabdiv_infobulle[]=creer_div_infobulle('a_propos_cell_ajustee',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');
	
			echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_cell_ajustee','y',100,100);\"  onmouseout=\"cacher_div('a_propos_cell_ajustee');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";

			echo "<br />\n";

			// Debug
			echo "<input type='checkbox' name='bull_pdf_debug' id='bull_pdf_debug' value='y' onchange=\"checkbox_change(this.id)\" />&nbsp;<label for='bull_pdf_debug' id='texte_bull_pdf_debug' style='cursor: pointer;'>Activer le debug pour afficher les erreurs perturbant la génération de PDF.</label>\n";

			$titre_infobulle="Debug\n";
			$texte_infobulle="Il arrive que la génération de PDF échoue.<br />Les raisons peuvent être variables (<em>manque de ressources serveur, bug,...</em>).<br />Dans ce cas, la présence d'un plugin lecteur PDF peut empêcher de voir quelles erreurs provoquent l'échec.<br />En cochant la case DEBUG, vous obtiendrez l'affichage des erreurs et ainsi vous pourrez obtenir de l'aide plus facilement sur la liste 'gepi-users'<br />\n";
			//$texte_infobulle.="\n";
			$tabdiv_infobulle[]=creer_div_infobulle('div_bull_debug_pdf',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');
	
			echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('div_bull_debug_pdf','y',100,100);\"  onmouseout=\"cacher_div('div_bull_debug_pdf');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";

			echo "<br />\n";

			echo "<br />\n";

			echo "</div>\n";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo js_checkbox_change_style('checkbox_change', 'texte_', 'y');

	echo "<script type='text/javascript'>
	checkbox_change('mode_bulletin_pdf');
	checkbox_change('mode_bulletin_html');

	function display_div_modele_bulletin_pdf() {
		if(document.getElementById('div_modele_bulletin_pdf')) {
			if(document.getElementById('mode_bulletin_pdf').checked==true) {
				document.getElementById('div_modele_bulletin_pdf').style.display='';
			}
			else {
				document.getElementById('div_modele_bulletin_pdf').style.display='none';
			}
		}

		/*
		if(document.getElementById('id_tr_moyennes_periodes_precedentes')) {
			if(document.getElementById('mode_bulletin_pdf').checked==true) {
				document.getElementById('id_tr_moyennes_periodes_precedentes').style.display='';
			}
			else {
				document.getElementById('id_tr_moyennes_periodes_precedentes').style.display='none';
			}
		}

		if(document.getElementById('id_tr_evolution_moyenne_periode_precedente')) {
			if(document.getElementById('mode_bulletin_pdf').checked==true) {
				document.getElementById('id_tr_evolution_moyenne_periode_precedente').style.display='';
			}
			else {
				document.getElementById('id_tr_evolution_moyenne_periode_precedente').style.display='none';
			}
		}
		*/
	}

	display_div_modele_bulletin_pdf();
	
	function change_lien_param_bull(type) {
		if(document.getElementById('lien_param_bull')) {
			if(type=='pdf') {
				document.getElementById('lien_param_bull').href='param_bull_pdf.php';
				document.getElementById('lien_param_bull').innerHTML='Paramètres d\'impression des bulletins PDF';
			}
			else {
				document.getElementById('lien_param_bull').href='param_bull.php';
				document.getElementById('lien_param_bull').innerHTML='Paramètres d\'impression des bulletins';
			}
		}
	}
</script>\n";


	//echo "<input type='hidden' name='un_seul_bull_par_famille' value='non' />\n";
	//=======================================
	echo "<input type='hidden' name='choix_periode_num' value='fait' />\n";

	//=======================================
	//echo "<div style='float:right; width:40%'>\n";
	echo "<div id='div_parametres'>\n";
	echo "<table border='0' summary='Tableau des paramètres'>\n";
	echo "<tr><td valign='top'><input type='checkbox' name='un_seul_bull_par_famille' id='un_seul_bull_par_famille' value='oui' onchange=\"checkbox_change(this.id)\" /></td><td><label for='un_seul_bull_par_famille' id='texte_un_seul_bull_par_famille' style='cursor: pointer;'>Ne pas imprimer de bulletin pour le deuxième parent<br />(<i>même dans le cas de parents séparés</i>).</label></td></tr>\n";

	// A FAIRE:
	// Tester et ne pas afficher:
	// - si tous les coeff sont à 1
	//$test_coef=mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef!='1.0')"));
	//if($test_coef>0){
		/*
		echo "<tr>\n";
		echo "<td colspan=\"2\"><b>Calcul des moyennes générales";
		// Ne pas afficher la mention de catégorie, si on n'affiche pas les catégories dans cette classe.
		//$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
		//if ($affiche_categories == "y") {
			echo " (<i>et par catégorie</i>)";
		//}
		echo ".</b></td>\n";
		echo "</tr>\n";
		echo "<tr><td valign='top'><input type='checkbox' name='coefficients_a_1' id='coefficients_a_1' value='oui' /></td><td><label for='coefficients_a_1' style='cursor: pointer;'>Forcer les coefficients des matières à 1, indépendamment des coefficients saisis dans les paramètres de la classe.</label></td></tr>\n";
		*/
		echo "<tr><td valign='top'><input type='checkbox' name='coefficients_a_1' id='coefficients_a_1' value='oui' onchange=\"checkbox_change(this.id)\" /></td><td><label for='coefficients_a_1' id='texte_coefficients_a_1' style='cursor: pointer;'>Forcer, dans le calcul des moyennes générales, les coefficients des matières à 1, indépendamment des coefficients saisis dans les paramètres de la classe.</label></td></tr>\n";

		// A FAIRE: A déplacer et mettre dans le modèle PDF
		//echo "<tr id='id_tr_moyennes_periodes_precedentes'><td valign='top'><input type='checkbox' name='moyennes_periodes_precedentes' id='moyennes_periodes_precedentes' value='y'  /></td><td><label for='moyennes_periodes_precedentes' style='cursor: pointer;'>Afficher les moyennes de l'élève pour les périodes précédentes (<i>incompatible avec l'affichage des moyennes min/max/classe dans la même cellule que la moyenne de l'élève</i>).</label></td></tr>\n";

		//echo "<tr id='id_tr_evolution_moyenne_periode_precedente'><td valign='top'><input type='checkbox' name='evolution_moyenne_periode_precedente' id='evolution_moyenne_periode_precedente' value='y'  /></td><td><label for='evolution_moyenne_periode_precedente' style='cursor: pointer;'>Indiquer par un + ou - l'évolution de la moyenne (<i>hausse/stable/baisse</i>).</label></td></tr>\n";

		echo "<tr><td valign='top'><input type='checkbox' name='tri_par_etab_orig' id='tri_par_etab_orig' value='y' onchange=\"checkbox_change(this.id)\" /></td><td><label for='tri_par_etab_orig' id='texte_tri_par_etab_orig' style='cursor: pointer;'>Trier les bulletins par établissement d'origine.</label></td></tr>\n";

		/*
		echo "<tr><td valign='top'><input type='checkbox' name='avec_coches_mentions' id='avec_coches_mentions' value='y' /></td><td><label for='avec_coches_mentions' style='cursor: pointer;'>Faire apparaître des cases à cocher pour les ".$gepi_denom_mention."s (<i>Félicitations, Mention honorable, Encouragements, ou autre...</i>) sur les bulletins PDF.</label></td></tr>\n";
		*/

		if(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='administrateur')) {
			echo "<tr><td valign='top'><input type='checkbox' name='forcer_recalcul_moy_conteneurs' id='forcer_recalcul_moy_conteneurs' value='y' onchange=\"checkbox_change(this.id)\" /></td><td><label for='forcer_recalcul_moy_conteneurs' id='texte_forcer_recalcul_moy_conteneurs' style='cursor: pointer;'>Forcer le recalcul des moyennes de conteneurs.</label></td></tr>\n";

			echo "<tr><td valign='top'><input type='checkbox' name='forcer_recalcul_rang' id='forcer_recalcul_rang' value='y' onchange=\"checkbox_change(this.id)\" /></td><td><label for='forcer_recalcul_rang' id='texte_forcer_recalcul_rang' style='cursor: pointer;'>Forcer le recalcul des rangs.</label></td></tr>\n";
		}
	//}
	echo "</table>\n";

	//===========================================
	$b_adr_pg_defaut=isset($_SESSION['b_adr_pg']) ? $_SESSION['b_adr_pg'] : "xx";

	echo "<div id='div_param_b_adr_pg'>\n";
	echo "<br />\n";
	echo "<p><b>Bloc adresse responsable et page de garde&nbsp;:</b></p>\n";
	echo "<blockquote>\n";
	echo "<input type='radio' name='b_adr_pg' id='b_adr_pg_xx' value='xx' ";
	if($b_adr_pg_defaut=="xx") {
		echo "checked='checked' ";
	}
	echo "/><label for='b_adr_pg_xx' style='cursor:pointer'> D'après les paramètres du bulletin HTML</label><br />\n";

	echo "<input type='radio' name='b_adr_pg' id='b_adr_pg_nn' value='nn' ";
	if($b_adr_pg_defaut=="nn") {
		echo "checked='checked' ";
	}
	echo "/><label for='b_adr_pg_nn' style='cursor:pointer'> sans bloc adresse ni page de garde</label><br />\n";

	echo "<input type='radio' name='b_adr_pg' id='b_adr_pg_yn' value='yn' ";
	if($b_adr_pg_defaut=="yn") {
		echo "checked='checked' ";
	}
	echo "/><label for='b_adr_pg_yn' style='cursor:pointer'> avec bloc adresse sans page de garde</label><br />\n";

	echo "<input type='radio' name='b_adr_pg' id='b_adr_pg_ny' value='ny' ";
	if($b_adr_pg_defaut=="ny") {
		echo "checked='checked' ";
	}
	echo "/><label for='b_adr_pg_ny' style='cursor:pointer'> sans bloc adresse avec page de garde</label><br />\n";

	echo "<input type='radio' name='b_adr_pg' id='b_adr_pg_yy' value='yy' ";
	if($b_adr_pg_defaut=="yy") {
		echo "checked='checked' ";
	}
	echo "/><label for='b_adr_pg_yy' style='cursor:pointer'> avec bloc adresse et page de garde</label><br />\n";
	echo "</blockquote>\n";
	echo "</div>\n";
	//===========================================

	// L'admin peut avoir accès aux bulletins, mais il n'a de toute façon pas accès au relevés de notes.
	$sql="SELECT 1=1 FROM droits WHERE id='/cahier_notes/visu_releve_notes_bis.php' AND ".$_SESSION['statut']."='V';";
	$res_verif_droit=mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res_verif_droit)>0) {
		echo "<p><input type='checkbox' name='intercaler_releve_notes' id='intercaler_releve_notes' value='y' onchange='display_param_releve();checkbox_change(this.id)' /> <label for='intercaler_releve_notes' id='texte_intercaler_releve_notes' style='cursor: pointer;'>Intercaler les relevés de notes</label>\n";

		echo "<span id='pliage_param_releve'>\n";
		echo "(<i>";
		echo "<a href='#' onclick=\"document.getElementById('div_param_releve').style.display='';return false;\">Afficher</a>";
		echo " / \n";
		echo "<a href='#' onclick=\"document.getElementById('div_param_releve').style.display='none';return false;\">Masquer</a>";
		echo " les paramètres du relevé de notes</i>).";
		echo "</span>\n";

		echo "<br />\n";
		echo "(<i>pour permettre par exemple une impression recto/verso</i>).";
		echo "</p>\n";


		echo "<div id='div_param_releve'>\n";
		include("../cahier_notes/tableau_choix_parametres_releves_notes.php");
		echo "</div>\n";

		echo "<script type='text/javascript'>";

		if($type_bulletin_par_defaut=='html') {
			echo "griser_lignes_specifiques_pdf();";
		}
		else {
			echo "griser_lignes_specifiques_html();";
		}

		echo "
function CocheLigne(item) {
	for (var i=0;i<".count($tab_id_classe).";i++) {
		if(document.getElementById(item+'_'+i)){
			document.getElementById(item+'_'+i).checked = true;
		}
	}
}

function DecocheLigne(item) {
	for (var i=0;i<".count($tab_id_classe).";i++) {
		if(document.getElementById(item+'_'+i)){
			document.getElementById(item+'_'+i).checked = false;
		}
	}
}

function ToutCocher() {
";
		for($k=0;$k<count($tab_item);$k++) {
			echo "	CocheLigne('".$tab_item[$k]."');\n";
		}
		echo "	CocheLigne('rn_app');
	CocheLigne('rn_adr_resp');
}

function ToutDeCocher() {
";
		for($k=0;$k<count($tab_item);$k++) {
			echo "	DecocheLigne('".$tab_item[$k]."');\n";
		}
		echo "	DecocheLigne('rn_app');
	DecocheLigne('rn_adr_resp');
}

</script>\n";

	}

	$tab_signature=get_tab_signature_bull();
	if(count($tab_signature)>0) {
		echo "<p class='bold'>Signature des bulletins&nbsp;: <a href='#'><img src='../images/edit16.png' class='icone16' title=\"Éditer/Modifier les signatures.
Le dépot de fichiers de signature pour les différents utilisateurs et classes n'est pour le moment possible qu'en tant qu'administrateur dans Gestion des modules/Bulletins\" /></a></p>\n";
		echo "<table class='boireaus boireaus_alt' summary='Tableau des signatures possibles'>\n";
		echo "<tr><th>Classe</th><th>Signer</th></tr>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<tr><td>".get_nom_classe($tab_id_classe[$i])."</td><td>";
			if((isset($tab_signature['classe']))&&(array_key_exists($tab_id_classe[$i] ,$tab_signature['classe']))) {
				if((isset($tab_signature['fichier']))&&(array_key_exists($tab_signature['classe'][$tab_id_classe[$i]]['id_fichier'] ,$tab_signature['fichier']))) {
					echo "<input type='checkbox' name='signer[]' id='signer_".$tab_id_classe[$i]."' value= '".$tab_id_classe[$i]."' onchange=\"checkbox_change(this.id)\" /><label for='signer_".$tab_id_classe[$i]."' id='texte_signer_".$tab_id_classe[$i]."'> Signer avec l'image ci-contre ";
					echo "<img src='".$tab_signature['fichier'][$tab_signature['classe'][$tab_id_classe[$i]]['id_fichier']]['chemin']."' width='100' style='vertical-align:middle;' />";
					echo "</label>";
				}
				else {
					echo "Le droit de signer est présent,<br />mais aucun fichier de signature n'est associé à la classe.";
				}
			}
			else {
				echo "<img src='../images/disabled.png' class='icone20' title=\"Vous n'avez pas le droit de signer d'un fichier les bulletins de cette classe.\" />";
			}
			echo "</td></tr>\n";
			//$sql="SELECT ";
		}
		echo "</table>\n";
	}


	echo "<p align='center'><input type='submit' name='bouton_valide_select_eleves1' value='Valider' /></p>\n";
	echo "</div>\n";


	if (getSettingValue("active_module_absence")!='2' || getSettingValue("abs2_import_manuel_bulletin")=='y') {
		echo "<br />\n";
		echo "<p class='bold'>Absences saisies&nbsp;:</p>\n";
		echo "<table class='boireaus'>\n";
		echo "<tr>\n";
		echo "<th rowspan='2'>Classe</th>\n";
		echo "<th colspan='".count($tab_periode_num)."'>Période(s)</th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		for($j=0;$j<count($tab_periode_num);$j++) {
			echo "<th>P".$tab_periode_num[$j]."</th>\n";
		}
		echo "</tr>\n";
		$alt=1;
		for($i=0;$i<count($tab_id_classe);$i++) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
			echo "<td>".get_nom_classe($tab_id_classe[$i])."</td>\n";
			for($j=0;$j<count($tab_periode_num);$j++) {
				echo "<td>\n";

				$sql="SELECT a.* FROM absences a, j_eleves_classes jec WHERE (jec.login=a.login AND jec.periode=a.periode AND jec.id_classe='".$tab_id_classe[$i]."' AND a.periode='".$tab_periode_num[$j]."');";
				$test_abs_1 = mysqli_query($GLOBALS["mysqli"], $sql);
				echo mysqli_num_rows($test_abs_1)." enregistrement(s)<br />\n";

				//$sql="SELECT a.* FROM absences a, j_eleves_classes jec WHERE (jec.login=a.login AND jec.periode=a.periode AND a.periode='".$tab_periode_num[$j]."' AND (a.nb_absences>0 OR non_justifie>0 OR nb_retards>0));";
				//$test_abs_2 = mysql_query($sql);

				$sql="SELECT 1=1 FROM absences a, j_eleves_classes jec WHERE (jec.login=a.login AND jec.periode=a.periode AND jec.id_classe='".$tab_id_classe[$i]."' AND a.periode='".$tab_periode_num[$j]."' AND a.nb_absences>0);";
				$test_nb_abs = mysqli_query($GLOBALS["mysqli"], $sql);
				echo "<span title=\"Nombre total de demi-journées d'absence pour des élèves de la classe\">".mysqli_num_rows($test_nb_abs)."</span>\n";
				echo " | ";
				$sql="SELECT 1=1 FROM absences a, j_eleves_classes jec WHERE (jec.login=a.login AND jec.periode=a.periode AND a.periode='".$tab_periode_num[$j]."' AND jec.id_classe='".$tab_id_classe[$i]."' AND a.non_justifie>0);";
				$test_nb_nj = mysqli_query($GLOBALS["mysqli"], $sql);
				echo "<span title=\"Nombre d'absences non justifiées pour la classe\">".mysqli_num_rows($test_nb_nj)."</span>\n";
				echo " | ";
				$sql="SELECT 1=1 FROM absences a, j_eleves_classes jec WHERE (jec.login=a.login AND jec.periode=a.periode AND a.periode='".$tab_periode_num[$j]."' AND jec.id_classe='".$tab_id_classe[$i]."' AND a.nb_retards>0);";
				$test_nb_ret = mysqli_query($GLOBALS["mysqli"], $sql);
				echo "<span title=\"Nombre de retards pour la classe\">".mysqli_num_rows($test_nb_ret)."</span>\n";

				echo "</td>\n";
			}
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "<br />\n";
	}


	//=======================================
	/*
	echo "<div style='float:right; width:5em;'>\n";
	echo "<input type='submit' name='bouton_valide_select_eleves1' value='Valider' />\n";
	echo "</div>\n";
	*/
	//=======================================



	if(count($tab_id_classe)>1) {
		echo "<p>Pour toutes les classes";
		if(count($tab_periode_num)>1) {
			echo " et toutes les périodes";
		}
		echo " <a href='#' onClick='cocher_tous_eleves();return false;'>Cocher tous les élèves</a> / <a href='#' onClick='decocher_tous_eleves();return false;'>Décocher tous les élèves</a></p>\n";
	}

	$max_eff_classe=0;
	for($i=0;$i<count($tab_id_classe);$i++) {
		// Est-ce bien un entier?
		if((mb_strlen(preg_replace("/[0-9]/","",$tab_id_classe[$i])))||($tab_id_classe[$i]=="")) {
			echo "<p>Identifiant de classe erroné: <span style='color:red'>".$tab_id_classe[$i]."</span></p></form>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";

		$classe_courante=get_class_from_id($tab_id_classe[$i]);
		echo "<p class='bold'>Classe de ".$classe_courante."</p>\n";

		echo "<div style='float:right'>\n";
		echo "<div align='left' style='margin-left:11em; font-size: xx-small;'>\n";

		if (getSettingValue("active_module_absence")=='2' && getSettingValue("abs2_import_manuel_bulletin")!='y') {
			echo "<p>Voici les dates prises en compte<br />pour les extractions d'absences&nbsp;:</p>\n";
			echo "<table class='boireaus'>\n";
			echo "<tr>\n";
			echo "<th>Période</th>\n";
			echo "<th>Date de fin</th>\n";
			echo "</tr>\n";
			$sql="SELECT nom_periode, num_periode, date_fin FROM periodes WHERE id_classe='".$tab_id_classe[$i]."' ORDER BY num_periode;";
			$res_tmp_per=mysqli_query($GLOBALS["mysqli"], $sql);
			$alt=1;
			while($lig_tmp_per=mysqli_fetch_object($res_tmp_per)) {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td>".$lig_tmp_per->nom_periode."</td>\n";
				echo "<td>".formate_date($lig_tmp_per->date_fin)."</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		}

		echo "<table class='boireaus' summary='Coefficients des enseignements de ".$classe_courante."'>\n";
		echo "<tr>\n";
		echo "<th>Enseignement</th>\n";
		echo "<th>Enseignant(s)</th>\n";
		echo "<th>Classes</th>\n";
		echo "<th>Coefficient</th>\n";
		echo "</tr>\n";
		$alt=1;
		$tmp_groups = get_groups_for_class($tab_id_classe[$i],"","n");
		foreach($tmp_groups as $tmp_current_group) {
			$sql="SELECT * FROM j_groupes_visibilite WHERE id_groupe='".$tmp_current_group['id']."' AND domaine='bulletins' AND visible='n';";
			$test_visu=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test_visu)==0) {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td>\n";
				echo $tmp_current_group['name']."\n";
				echo "</td>\n";

				echo "<td>\n";
				$tab_champs=array('profs');
				$tmp_current_group_complement=get_group($tmp_current_group['id'] ,$tab_champs);
				echo $tmp_current_group_complement['profs']['proflist_string']."\n";
				echo "</td>\n";

				echo "<td>\n";
				echo $tmp_current_group['classlist_string']."\n";
				echo "</td>\n";

				echo "<td>\n";
				$sql="SELECT coef FROM j_groupes_classes WHERE id_classe='".$tab_id_classe[$i]."' AND id_groupe='".$tmp_current_group['id']."';";
				$res_coef=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_coef)>0) {
					$tmp_coef=old_mysql_result($res_coef, 0, 'coef');
					if($_SESSION['statut']=='administrateur') {
						echo "<a href='../groupes/edit_class.php?id_classe=".$tab_id_classe[$i]."' target='_blank'>".$tmp_coef."</a>";
					}
					else {
						echo $tmp_coef;
					}
				}
				echo "</td>\n";
				echo "</tr>\n";
			}
		}
		unset($tmp_groups);
		echo "</table>\n";

		echo "</div>\n";
		echo "</div>\n";

		echo "<table class='boireaus' summary='Choix des élèves'>\n";
		echo "<tr>\n";
		echo "<th>Elèves</th>\n";
		for($j=0;$j<count($tab_periode_num);$j++) {
			// Est-ce bien un entier?
			if((mb_strlen(preg_replace("/[0-9]/","",$tab_periode_num[$j])))||($tab_periode_num[$j]=="")) {
				echo "<td>Identifiant de période erroné: <span style='color:red'>".$tab_periode_num[$j]."</span></td></tr></table></form>\n";
				require("../lib/footer.inc.php");
				die();
			}

			//echo "<th>Période $j</th>\n";
			$sql="SELECT nom_periode FROM periodes WHERE id_classe='".$tab_id_classe[$i]."' AND num_periode='".$tab_periode_num[$j]."';";
			$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
			echo "<th";
			if((isset($tab_per_non_close[$tab_id_classe[$i]]))&&(in_array($tab_periode_num[$j], $tab_per_non_close[$tab_id_classe[$i]]))) {
				echo " style='background-color:red' title='Période non close. Vous ne devriez pas imprimer ces bulletins.'";
			}
			echo ">\n";
			$lig_per=mysqli_fetch_object($res_per);

			echo "<input type='hidden' name='tab_periode_num[$j]' value='".$tab_periode_num[$j]."' />\n";

			// En imprimant deux classes l'une à semestres, l'autre à trimestres, on peut choisir la période 3 et provoquer des erreurs sur les semestres...
			if(isset($lig_per->nom_periode)) {
				echo $lig_per->nom_periode;
			}
			else {
				echo "<span style='color:orange' title='Nom de période inconnu???'>X</span>";
			}

			echo "<br />\n";

			echo "<a href=\"javascript:CocheColonneSelectEleves(".$i.",".$j.");changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonneSelectEleves(".$i.",".$j.");changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";

			echo "</th>\n";
		}
		echo "</tr>\n";

		/*
		$sql="SELECT e.* FROM eleves e,
						j_eleves_classes jec
			WHERE jec.login=e.login AND
						jec.id_classe='".$tab_id_classe[$i]."' AND
						jec.periode='".$tab_periode_num[$j]."';";
		*/
		$sql="SELECT DISTINCT e.* FROM eleves e,
						j_eleves_classes jec
			WHERE jec.login=e.login AND
						jec.id_classe='".$tab_id_classe[$i]."'
			ORDER BY e.nom,e.prenom;";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		$alt=1;
		$cpt=0;
		while($lig_ele=mysqli_fetch_object($res_ele)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td style='text-align:left;'>".$lig_ele->nom." ".$lig_ele->prenom."</td>\n";
			for($j=0;$j<count($tab_periode_num);$j++) {

				$sql="SELECT 1=1 FROM j_eleves_classes jec
					WHERE jec.id_classe='".$tab_id_classe[$i]."' AND
							jec.periode='".$tab_periode_num[$j]."';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					echo "<td><input type='checkbox' name='tab_selection_ele_".$i."_".$j."[]' id='tab_selection_ele_".$i."_".$j."_".$cpt."' value=\"".$lig_ele->login."\" ";
					if(!isset($preselection_eleves)) {echo "checked ";}
					//elseif((isset($preselection_eleves[$tab_periode_num[$j]]))&&(in_array($lig_ele->login,$preselection_eleves[$tab_periode_num[$j]]))) {echo "checked ";}
					elseif((isset($preselection_eleves[$tab_periode_num[$j]]))&&(strstr($preselection_eleves[$tab_periode_num[$j]],"|".$lig_ele->login."|"))) {echo "checked ";}
					echo "/></td>\n";
				}
				else {
					echo "<td>-</td>\n";
				}
			}
			echo "</tr>\n";
			$cpt++;
		}
		echo "</table>\n";

		if($max_eff_classe<$cpt) {$max_eff_classe=$cpt;}

	}

//count($tab_periode_num)

	echo "<script type='text/javascript'>

function CocheColonneSelectEleves(i,j) {
	for (var k=0;k<$max_eff_classe;k++) {
		if(document.getElementById('tab_selection_ele_'+i+'_'+j+'_'+k)){
			document.getElementById('tab_selection_ele_'+i+'_'+j+'_'+k).checked = true;
		}
	}
}

function DecocheColonneSelectEleves(i,j) {
	for (var k=0;k<$max_eff_classe;k++) {
		if(document.getElementById('tab_selection_ele_'+i+'_'+j+'_'+k)){
			document.getElementById('tab_selection_ele_'+i+'_'+j+'_'+k).checked = false;
		}
	}
}

function cocher_tous_eleves() {
";

	for($i=0;$i<count($tab_id_classe);$i++) {
		for($j=0;$j<count($tab_periode_num);$j++) {
			echo "CocheColonneSelectEleves($i,$j);\n";
		}
	}

	echo "}
function decocher_tous_eleves() {
";

	for($i=0;$i<count($tab_id_classe);$i++) {
		for($j=0;$j<count($tab_periode_num);$j++) {
			echo "DecocheColonneSelectEleves($i,$j);\n";
		}
	}

	echo "}

function display_param_releve() {
	if(document.getElementById('div_param_releve')) {
		if(document.getElementById('intercaler_releve_notes')) {
			if(document.getElementById('intercaler_releve_notes').checked==true) {
				document.getElementById('div_param_releve').style.display='';
			}
			else {
				document.getElementById('div_param_releve').style.display='none';
			}
		}
	}
	// On donne l'accès aux liens de pliage/dépliage des paramètres du relevé de notes
	if(document.getElementById('pliage_param_releve')) {document.getElementById('pliage_param_releve').style.display='';}
}
display_param_releve();

// On cache l'accès aux liens de pliage/dépliage des paramètres du relevé de notes
// jusqu'à ce que la case d'insertion des relevés de notes entre les bulletins ait été cochée (au moins une fois)
if(document.getElementById('pliage_param_releve')) {document.getElementById('pliage_param_releve').style.display='none';}



function display_param_b_adr_pg() {
	if(document.getElementById('div_param_b_adr_pg')) {
		if(document.getElementById('mode_bulletin_html')) {
			if(document.getElementById('mode_bulletin_html').checked==true) {
				document.getElementById('div_param_b_adr_pg').style.display='';
			}
			else {
				document.getElementById('div_param_b_adr_pg').style.display='none';
			}
		}
	}
}
display_param_b_adr_pg();

</script>\n";

	echo "<input type='hidden' name='valide_select_eleves' value='y' />\n";
	echo "<p><input type='submit' name='bouton_valide_select_eleves2' value='Valider' /></p>\n";
	echo "</form>\n";
}
//=======================================================
//==EXTRACTION DES DONNEES PUIS AFFICHAGE DES BULLETINS==
else {
	/*
	echo "<p class='bold'>";
	echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
	echo " | <a href='#' onClick='history.go(-1);'>Choisir d'autres périodes</a>\n";
	echo " | <a href='#' onClick='history.go(-2);'>Choisir d'autres élèves</a>\n";
	echo "</p>\n";
	*/

	//debug_var();

	$mode_bulletin=isset($_POST['mode_bulletin']) ? $_POST['mode_bulletin'] : "html";
	$un_seul_bull_par_famille=isset($_POST['un_seul_bull_par_famille']) ? $_POST['un_seul_bull_par_famille'] : "non";
	$coefficients_a_1=isset($_POST['coefficients_a_1']) ? $_POST['coefficients_a_1'] : "non";

	$use_cell_ajustee=isset($_POST['use_cell_ajustee']) ? $_POST['use_cell_ajustee'] : "y";

	$tri_par_etab_orig=isset($_POST['tri_par_etab_orig']) ? $_POST['tri_par_etab_orig'] : "n";

	$signer=isset($_POST['signer']) ? $_POST['signer'] : array();

	//$avec_coches_mentions=isset($_POST['avec_coches_mentions']) ? $_POST['avec_coches_mentions'] : "n";


	// 20100615
	//$moyennes_periodes_precedentes=isset($_POST['moyennes_periodes_precedentes']) ? $_POST['moyennes_periodes_precedentes'] : "n";
	//$evolution_moyenne_periode_precedente=isset($_POST['evolution_moyenne_periode_precedente']) ? $_POST['evolution_moyenne_periode_precedente'] : "n";

	//$bull_pdf_debug=isset($_POST['bull_pdf_debug']) ? $_POST['bull_pdf_debug'] : "n";

	//========================================
	/*
	echo "<style type='text/css'>
	@media screen{
		#infodiv {
			float: right;
			width: 20em;
			background-color: white;
		}
	}
	@media print{
		#infodiv {
			display:none;
		}
	}
</style>\n";
	*/


	if($mode_bulletin=="html") {
		echo "<div id='infodiv'>
<p id='titre_infodiv' style='font-weight:bold; text-align:center; border:1px solid black;'></p>
<table class='boireaus' width='100%' summary='Tableau de progression'>
<tr>
<th colspan='3' id='td_info'></th>
</tr>
<tr>
<th width='33%'>Classe</th>
<th width='33%'>Période</th>
<th>Elève</th>
</tr>
<tr>
<td id='td_classe'></td>
<td id='td_periode'></td>
<td id='td_ele'></td>
</tr>
</table>
</div>\n";
	}

	//=============================================
	// Faire les extractions pour le relevé de notes si jamais cela a été demandé.
	if(isset($intercaler_releve_notes)) {
		include("../cahier_notes/extraction_donnees_releves_notes.php");
	}
	else {
		// On initialise un tableau qui restera vide...
		$tab_releve=array();
	}
	//=============================================


	if($mode_bulletin=="html") {
		echo "<script type='text/javascript'>
	document.getElementById('titre_infodiv').innerHTML='Bulletins';
	document.getElementById('td_info').innerHTML='Préparatifs';
	document.getElementById('td_classe').innerHTML='';
	document.getElementById('td_periode').innerHTML='';
	document.getElementById('td_ele').innerHTML='';
</script>\n";
	}

	//========================================


	//========================================
	// RECUPERER LES INFOS ETABLISSEMENT

	//==============================
	$motif="Temoin_1";
	decompte_debug($motif,"Initialisation $motif");
	//==============================

	// Peut-être déplacer ça vers un fichier externe d'initialisation de variables
	// Et appeler les variables en 'global' dans les fonctions générant les bulletins

	/*
	// Récupérés plus haut
	$RneEtablissement=getSettingValue("gepiSchoolRne");
	$gepiSchoolName=getSettingValue("gepiSchoolName");
	$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1");
	$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2");
	$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode");
	$gepiSchoolCity=getSettingValue("gepiSchoolCity");
	$gepiSchoolPays=getSettingValue("gepiSchoolPays");

	$logo_etab=getSettingValue("logo_etab");

	if(!getSettingValue("bull_intitule_app")){
		$bull_intitule_app="Appréciations/Conseils";
	}
	else{
		$bull_intitule_app=getSettingValue("bull_intitule_app");
	}

	if(!getSettingValue("bull_affiche_tel")){
		$bull_affiche_tel="n";
	}
	else{
		$bull_affiche_tel=getSettingValue("bull_affiche_tel");
	}

	if(!getSettingValue("bull_affiche_fax")){
		$bull_affiche_fax="n";
	}
	else{
		$bull_affiche_fax=getSettingValue("bull_affiche_fax");
	}

	if($bull_affiche_fax=="y"){
		$gepiSchoolFax=getSettingValue("gepiSchoolFax");
	}

	if($bull_affiche_tel=="y"){
		$gepiSchoolTel=getSettingValue("gepiSchoolTel");
	}

	//========================================

	if(getSettingValue("genre_periode")){
		$genre_periode=getSettingValue("genre_periode");
	}
	else{
		$genre_periode="M";
	}
	*/

	//========================================

	// Absences
	$bull_affiche_absences=getSettingValue("bull_affiche_absences");
	$bull_affiche_abs_tot=getSettingValue("bull_affiche_abs_tot");
	$bull_affiche_abs_nj=getSettingValue("bull_affiche_abs_nj");
	$bull_affiche_abs_ret=getSettingValue("bull_affiche_abs_ret");
	$bull_affiche_abs_cpe=getSettingValue("bull_affiche_abs_cpe");
	if(($bull_affiche_abs_tot=='')&&($bull_affiche_abs_nj=='')&&($bull_affiche_abs_ret=='')&&($bull_affiche_abs_cpe=='')) {
		if($bull_affiche_absences=='y') {
			$bull_affiche_abs_tot="y";
			$bull_affiche_abs_nj="y";
			$bull_affiche_abs_ret="y";
			$bull_affiche_abs_cpe="y";
		}
	}

	// Prof principal
	$gepi_prof_suivi=getSettingValue("gepi_prof_suivi");
	// CPE
	$gepi_cpe_suivi=getSettingValue("gepi_cpe_suivi");
	if($gepi_cpe_suivi=="") {$gepi_cpe_suivi="C.P.E.";}
	//=========================================
	// AID
	$bull_affiche_aid=getSettingValue("bull_affiche_aid");

	if ($bull_affiche_aid == 'y') {
		// Initialisations diverses
		unset($call_data_aid_b);
		unset($call_data_aid_e);

		// On prépare l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en tête des bulletins :
		if (!isset($call_data_aid_b)){
			$sql="SELECT * FROM aid_config WHERE (order_display1 ='b' and display_bulletin!='n') ORDER BY order_display2;";
			//echo "$sql<br />";
			$call_data_aid_b = mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_aid_b = mysqli_num_rows($call_data_aid_b);
			//echo "\$nb_aid_b=$nb_aid_b<br />";
		}

		// On prépare l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en fin des bulletins :
		if (!isset($call_data_aid_e)){
			$sql="SELECT * FROM aid_config WHERE (order_display1 ='e' and display_bulletin!='n') ORDER BY order_display2;";
			//echo "$sql<br />";
			$call_data_aid_e = mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_aid_e = mysqli_num_rows($call_data_aid_e);
			//echo "\$nb_aid_e=$nb_aid_e<br />";
		}
	}
	//=========================================


	// Tableau destiné à stocker toutes les infos
	$tab_bulletin=array();

	//==============================
	$motif="Temoin_1";
	decompte_debug($motif,"$motif avant la boucle classes");
	//==============================

	// 20120419
	$tableau_eleve=array();
	$tableau_eleve['login']=array();
	$tableau_eleve['no_gep']=array();
	$tableau_eleve['nom_prenom']=array();

	// 20120505...
	// Si on en est aux élèves ayant changé de classe... récupérer les classes de l'élève en cours
	/*
	if(isset($_POST['tab_login_ele_chgt_classe'])) {
		$tab_login_ele_chgt_classe=$_POST['tab_login_ele_chgt_classe'];
		unset($tab_id_classe);
		$tab_id_classe=array();
		for($loop=0;$loop_classe<count($tab_login_ele_chgt_classe);$loop_classe++) {
			$sql="SELECT DISTINCT id_classe FROM j_eleves_classes WHERE login='".$tab_login_ele_chgt_classe[$loop]."' ORDER BY periode;";
			$res_classes=mysql_query($sql);
			if(mysql_num_rows($res_classes)>0) {
				while($lig_classe=mysql_fetch_object($res_classes)) {
					if(!) {}
				}
			}
		}
	}
	*/
	if(isset($_POST['ele_chgt_classe'])) {
		$sql="SELECT col1 AS login FROM tempo2 ORDER BY col1 LIMIT 1;";
		$res_login_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_login_ele)>0) {
			$lig_login_ele=mysqli_fetch_object($res_login_ele);
			$tab_restriction_ele=array();
			$tab_restriction_ele[]=$lig_login_ele->login;

			$sql="SELECT DISTINCT id_classe FROM j_eleves_classes WHERE login='".$lig_login_ele->login."' ORDER BY periode;";
			$res_classes=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_classes)>0) {
				$tab_id_classe=array();
				while($lig_classe=mysqli_fetch_object($res_classes)) {
					$tab_id_classe[]=$lig_classe->id_classe;
				}
			}
		}
	}


	$signature_bull=array();
	if(count($signer)>0) {
		$tab_signature=get_tab_signature_bull();
		if((count($tab_signature)>0)&&(isset($tab_signature['classe']))) {
			for($loop_classe=0;$loop_classe<count($tab_id_classe);$loop_classe++) {

				if(array_key_exists($tab_id_classe[$loop_classe], $tab_signature['classe'])) {
					if(array_key_exists($tab_signature['classe'][$tab_id_classe[$loop_classe]]['id_fichier'], $tab_signature['fichier'])) {
						$signature_bull[$tab_id_classe[$loop_classe]]=$tab_signature['fichier'][$tab_signature['classe'][$tab_id_classe[$loop_classe]]['id_fichier']]['chemin'];
					}
				}

			}
		}
	}


	$nb_bulletins_edites=0;
	// Boucle sur les classes
	for($loop_classe=0;$loop_classe<count($tab_id_classe);$loop_classe++) {


		if((isset($_POST['forcer_recalcul_rang']))&&($_POST['forcer_recalcul_rang']=='y')) {
			$sql="SELECT num_periode FROM periodes WHERE id_classe='".$tab_id_classe[$loop_classe]."' ORDER BY num_periode DESC LIMIT 1;";
			$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_per)>0) {
				$lig_per=mysqli_fetch_object($res_per);
				$recalcul_rang="";
				for($i=0;$i<$lig_per->num_periode;$i++) {$recalcul_rang.="y";}
				$sql="UPDATE groupes SET recalcul_rang='$recalcul_rang' WHERE id in (SELECT id_groupe FROM j_groupes_classes WHERE id_classe='".$tab_id_classe[$loop_classe]."');";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$res) {
					$msg.="<br />Erreur lors de la programmation du recalcul des rangs pour la classe ".get_nom_classe($tab_id_classe[$loop_classe]).".";
				}
			}
			// Les rangs seront recalculés lors de l'appel à calcul_rang.inc.php
		}



		//==============================
		if($mode_bulletin=="html") {
			$motif="Temoin_classe";
			decompte_debug($motif,"$motif classe $loop_classe");
			flush();
			echo "<script type='text/javascript'>
	document.getElementById('td_classe').innerHTML='".get_class_from_id($tab_id_classe[$loop_classe])."';
</script>\n";
		}
		//==============================

		// Les deux choix ci-dessous sont maintenant dans les Paramètres des bulletins HTML
		//$moyennes_annee="n";
		//$moyennes_periodes_precedentes="n";

		$evolution_moyenne_periode_precedente="n";
		// Remplissage des paramètres du modèle de bulletin PDF:
		if($mode_bulletin=="pdf") {
			$moyennes_annee="n";
			$moyennes_periodes_precedentes="n";

			require_once("bulletin_pdf.inc.php");
			foreach($val_defaut_champ_bull_pdf as $key => $value) {
				$tab_modele_pdf[$key][$tab_id_classe[$loop_classe]]=$value;
			}

			// Modèle de bulletin PDF
			$type_bulletin=isset($_POST['type_bulletin']) ? $_POST['type_bulletin'] : 1;
			// CONTROLER SI type_bulletin EST BIEN UN ENTIER éventuellement -1
			if(isset($type_bulletin)) {

				$option_modele_bulletin=getSettingValue("option_modele_bulletin");
				if($option_modele_bulletin==1) {$type_bulletin=-1;}

				//echo "\$type_bulletin=$type_bulletin<br />";
				if ($type_bulletin == -1) {
					// cas modèle par classe
					$sql="SELECT modele_bulletin_pdf FROM classes WHERE id='".$tab_id_classe[$loop_classe]."';";
					//echo "$sql<br />";
					$res_model=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_model)==0) {
						$sql="SELECT * FROM modele_bulletin WHERE id_model_bulletin='1';";
					}
					else {
						$lig_mb=mysqli_fetch_object($res_model);

						//echo "\$lig_mb->modele_bulletin_pdf=$lig_mb->modele_bulletin_pdf<br />";
						if(($lig_mb->modele_bulletin_pdf=='NULL')||($lig_mb->modele_bulletin_pdf=='')) {
							$sql="SELECT * FROM modele_bulletin WHERE id_model_bulletin='1';";
						}
						else {
							$sql="SELECT * FROM modele_bulletin WHERE id_model_bulletin='".$lig_mb->modele_bulletin_pdf."';";
						}
					}
				} else {
					$sql="SELECT * FROM modele_bulletin WHERE id_model_bulletin='".$type_bulletin."';";
				}
				//echo "$sql<br />";
			}

			//$type_bulletin=3;
			//$sql='SELECT * FROM modele_bulletin WHERE id_model_bulletin="'.$type_bulletin.'"';
			//echo "$sql<br />";
			$requete_model = mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($requete_model)>0) {
				$cpt=0;
				while($lig_model=mysqli_fetch_object($requete_model)) {
					$tab_modele_pdf["$lig_model->nom"][$tab_id_classe[$loop_classe]]=$lig_model->valeur;
					if(($lig_model->nom=='moyennes_periodes_precedentes')&&($lig_model->valeur=='y')) {
						// Pour que l'on extraie les moyennes pour les différentes périodes si nécessaire
						$moyennes_periodes_precedentes="y";
					}

					if(($lig_model->nom=='evolution_moyenne_periode_precedente')&&($lig_model->valeur=='y')) {
						// Pour que l'on extraie les moyennes pour les différentes périodes si nécessaire
						$evolution_moyenne_periode_precedente="y";
					}

					if(($lig_model->nom=='moyennes_annee')&&($lig_model->valeur=='y')) {
						// Moyennes des moyennes de périodes pour les différents enseignements
						$moyennes_annee="y";
					}
				}
			}

			// Il faudrait appliquer d'autres correctifs:
			//echo "\$tab_modele_pdf[\"largeur_nombre_note\"][$tab_id_classe[$loop_classe]]='".$tab_modele_pdf["largeur_nombre_note"][$tab_id_classe[$loop_classe]]."'<br />";
			if($tab_modele_pdf["largeur_nombre_note"][$tab_id_classe[$loop_classe]]=="0") {$tab_modele_pdf["largeur_nombre_note"][$tab_id_classe[$loop_classe]] = 8;}

			if($tab_modele_pdf["active_regroupement_cote"][$tab_id_classe[$loop_classe]]==='1') {
				$tab_modele_pdf["X_note_app"][$tab_id_classe[$loop_classe]]=$tab_modele_pdf["X_note_app"][$tab_id_classe[$loop_classe]]+5;
				$tab_modele_pdf["Y_note_app"][$tab_id_classe[$loop_classe]]=$tab_modele_pdf["Y_note_app"][$tab_id_classe[$loop_classe]];
				$tab_modele_pdf["longeur_note_app"][$tab_id_classe[$loop_classe]]=$tab_modele_pdf["longeur_note_app"][$tab_id_classe[$loop_classe]]-5;
				$tab_modele_pdf["hauteur_note_app"][$tab_id_classe[$loop_classe]]=$tab_modele_pdf["hauteur_note_app"][$tab_id_classe[$loop_classe]];
			}

			//================================
			//================================
			//================================
		}

		//echo "\$moyennes_annee=$moyennes_annee<br />";

		//$id_classe=2;
		$id_classe=$tab_id_classe[$loop_classe];
		// Est-ce bien un entier?
		if((mb_strlen(preg_replace("/[0-9]/","",$id_classe)))||($id_classe=="")) {
			echo "<p>Identifiant de classe erroné: <span style='color:red'>$id_classe</span></p>\n";
			require("../lib/footer.inc.php");
			die();
		}


		// ++++++++++++++++++++++++++++++++++++++
		// ++++++++++++++++++++++++++++++++++++++
		// AJOUTER UN TEST: Le visiteur a-t-il le droit d'accéder à cette page pour cette classe
		if ($_SESSION["statut"] == "scolarite") {
			$sql="SELECT 1=1 FROM classes c, j_scol_classes jsc, j_eleves_classes jec WHERE (jec.id_classe=c.id AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' AND c.id='$id_classe');";
		}
		//elseif ($_SESSION["statut"] == "administrateur") {
		elseif (($_SESSION["statut"] == "administrateur")||($_SESSION["statut"] == "secours")||($_SESSION["statut"] == "autre")) {
			// On selectionne toutes les classes
			//$sql="SELECT DISTINCT c.* FROM classes c WHERE 1";
			$sql="SELECT 1=1 FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) LIMIT 1;";
		}
		elseif ($_SESSION["statut"] == "professeur") {
			$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs jep, j_eleves_classes jec WHERE (jep.professeur='".$_SESSION['login']."' AND jep.login = jec.login AND jec.id_classe = c.id AND c.id='$id_classe');";
		}
		elseif ($_SESSION["statut"] == "cpe") {
			$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_cpe jecpe, j_eleves_classes jec WHERE (jecpe.cpe_login='".$_SESSION['login']."' AND jecpe.e_login = jec.login AND jec.id_classe = c.id AND c.id='$id_classe');";
		}
		else {
			// On ne devrait pas arriver jusque-là...
			echo "<p>Droits insuffisants pour effectuer cette opération</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$test_acces_classe=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test_acces_classe)==0) {
            tentative_intrusion(2, "Tentative d'un ".$_SESSION["statut"]." (".$_SESSION["login"].") à une classe (".get_class_from_id($id_classe).") sans y être autorisé.");
            echo "<p>Vous n'êtes pas autorisés à être ici.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
		// ++++++++++++++++++++++++++++++++++++++
		// ++++++++++++++++++++++++++++++++++++++


		// Tableau destiné à stocker toutes les infos
		$tab_bulletin[$id_classe]=array();
		if(!isset($intercaler_releve_notes)) {
			// On initialise un tableau qui va rester vide
			$tab_releve[$id_classe]=array();
		}

		$affiche_adresse = sql_query1("SELECT display_address FROM classes WHERE id='".$id_classe."'");
		//echo "\$affiche_adresse=$affiche_adresse<br />";

		//===========================================
		$b_adr_pg=isset($_POST['b_adr_pg']) ? $_POST['b_adr_pg'] : 'xx';
		if($b_adr_pg=='nn') {
			$affiche_adresse="n";
			$page_garde_imprime="n";
		}
		elseif($b_adr_pg=='yn') {
			$affiche_adresse="y";
			$page_garde_imprime="n";
		}
		elseif($b_adr_pg=='ny') {
			$affiche_adresse="n";
			$page_garde_imprime="yes";
		}
		elseif($b_adr_pg=='yy') {
			$affiche_adresse="y";
			$page_garde_imprime="yes";
		}
		$affiche_page_garde=$page_garde_imprime;
		$_SESSION['b_adr_pg']=$b_adr_pg;
		//===========================================

		$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe' GROUP BY login;";
		$res_eff_total_classe=mysqli_query($GLOBALS["mysqli"], $sql);
		$eff_total_classe=mysqli_num_rows($res_eff_total_classe);

		//=========================================================================
		// Pour l'archivage des bulletins PDF:
		if(((isset($_POST['tous_les_eleves']))&&($_POST['tous_les_eleves']=='y'))&&(!isset($_POST['ele_chgt_classe']))) {
			// Si on en est aux élèves ayant changé de classe, on les parcourt un par un et $tab_restriction_ele est rempli plus haut

			// On se restreint à une partie de la classe:
			$arch_bull_eff_tranche=getPref($_SESSION['login'],'arch_bull_eff_tranche',10);

			$tab_restriction_ele=array();
			$sql="SELECT col2 AS login FROM tempo2 WHERE col1='$id_classe' ORDER BY col2 LIMIT $arch_bull_eff_tranche;";
			$res_ele_tranche=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_ele_tranche)>0) {
				while($lig_ele_tranche=mysqli_fetch_object($res_ele_tranche)) {
					$tab_restriction_ele[]=$lig_ele_tranche->login;
				}
			}
		}

		// Pour l'archivage des bulletins PDF:
		if((isset($_POST['toutes_les_periodes']))&&($_POST['toutes_les_periodes']=='y')) {
			$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode;";
			$res_periode=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_periode)>0) {
				$tab_periode_num=array();
				while($lig_periode=mysqli_fetch_object($res_periode)) {
					$tab_periode_num[]=$lig_periode->num_periode;
				}
			}
		}
		//=========================================================================

		// Boucle sur les périodes
		for($loop_periode_num=0;$loop_periode_num<count($tab_periode_num);$loop_periode_num++) {

			if((isset($_POST['forcer_recalcul_moy_conteneurs']))&&($_POST['forcer_recalcul_moy_conteneurs']=='y')) {
					$sql="SELECT DISTINCT ccn.id_cahier_notes,ccn.id_groupe FROM cn_cahier_notes ccn,groupes g,j_groupes_classes jgc,classes c WHERE
						ccn.id_groupe=g.id AND
						jgc.id_groupe=g.id AND
						c.id=jgc.id_classe AND
						ccn.periode='".$tab_periode_num[$loop_periode_num]."' AND
						c.id='".$id_classe."'
						ORDER BY c.classe,g.description";
					//echo "$sql";
					$res_recalcul_moy_conteneurs=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_recalcul_moy_conteneurs)>0){
						while($lig_recalcul_moy_conteneurs=mysqli_fetch_object($res_recalcul_moy_conteneurs)) {
							$current_group=get_group($lig_recalcul_moy_conteneurs->id_groupe);
							$periode_num=$tab_periode_num[$loop_periode_num];
							$id_racine=$lig_recalcul_moy_conteneurs->id_cahier_notes;
							recherche_enfant($lig_recalcul_moy_conteneurs->id_cahier_notes);
						}
						unset($current_group);
						unset($periode_num);
						unset($id_racine);
					}
			}
	

			//$periode_num=1;
			$periode_num=$tab_periode_num[$loop_periode_num];

			// Est-ce bien un entier?
			if((mb_strlen(preg_replace("/[0-9]/","",$periode_num)))||($periode_num=="")) {
				echo "<p>Identifiant de période erroné: <span style='color:red'>".$periode_num."</span></p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			//==============================
			if($mode_bulletin=="html") {
				$motif="Temoin_periode";
				decompte_debug($motif,"$motif classe $loop_classe période $periode_num");
				flush();
				echo "<script type='text/javascript'>
	document.getElementById('td_periode').innerHTML='".$periode_num."';
</script>\n";
			}
			//==============================

			//============================
			// On vide les variables de la boucle précédente avant le calcul dans calcul_moy_gen.inc.php
			unset($moy_gen_eleve);
			unset($moy_gen_classe);
			unset($moy_generale_classe);
			unset($moy_max_classe);
			unset($moy_min_classe);

			unset($moy_cat_classe);
			unset($moy_cat_eleve);

			unset($quartile1_classe_gen);
			unset($quartile2_classe_gen);
			unset($quartile3_classe_gen);
			unset($quartile4_classe_gen);
			unset($quartile5_classe_gen);
			unset($quartile6_classe_gen);
			unset($place_eleve_classe);

			unset($current_eleve_login);
			unset($current_group);
			unset($current_eleve_note);
			unset($current_eleve_statut);
			unset($current_coef);
			unset($categories);
			unset($current_classe_matiere_moyenne);

			unset($current_coef_eleve);
			unset($moy_min_classe_grp);
			unset($moy_max_classe_grp);
			unset($current_eleve_rang);

			unset($current_group_effectif_avec_note);

			unset($current_eleve_app);
			//============================


			// Tableau destiné à stocker toutes les infos
			$tab_bulletin[$id_classe][$periode_num]=array();
			if(!isset($intercaler_releve_notes)) {
				// On initialise un tableau qui va rester vide
				$tab_releve[$id_classe][$periode_num]=array();
			}


			$tab_bulletin[$id_classe][$periode_num]['affiche_adresse']=$affiche_adresse;
			//echo "\$tab_bulletin[$id_classe][$periode_num]['affiche_adresse']=".$tab_bulletin[$id_classe][$periode_num]['affiche_adresse']."<br />";

			// Informations sur la période
			$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' AND num_periode='$periode_num';";
			$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_per)>0) {
				$lig_per=mysqli_fetch_object($res_per);
				$tab_bulletin[$id_classe][$periode_num]['num_periode']=$lig_per->num_periode;
				//$tab_bulletin[$id_classe][$periode_num]['nom_periode']=$lig_per->nom_periode;
				$tab_bulletin[$id_classe][$periode_num]['nom_periode']=preg_replace("/&#039;/","'",$lig_per->nom_periode);
				$tab_bulletin[$id_classe][$periode_num]['verouiller']=$lig_per->verouiller;
			}

			// Liste des élèves à éditer/afficher/imprimer (sélection):
			// tab_ele_".$i."_".$j.
			//$tab_bulletin[$id_classe][$periode_num]['selection_eleves']=array();
			$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$loop_classe.'_'.$loop_periode_num]) ? $_POST['tab_selection_ele_'.$loop_classe.'_'.$loop_periode_num] : array();
			if((isset($_POST['tous_les_eleves']))&&($_POST['tous_les_eleves']=='y')) {
				$sql="SELECT login FROM j_eleves_classes WHERE id_classe='".$id_classe."' AND periode='".$periode_num."' ORDER BY login;";
				$res_liste_ele=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_liste_ele)>0) {
					$tab_selection_eleves=array();
					while($lig_liste_ele=mysqli_fetch_object($res_liste_ele)) {
						if(in_array($lig_liste_ele->login, $tab_restriction_ele)) {
							$tab_selection_eleves[]=$lig_liste_ele->login;
							
							// Ménage:
							$sql="DELETE FROM tempo2 WHERE col1='$id_classe' AND col2='$lig_liste_ele->login';";
							$menage=mysqli_query($GLOBALS["mysqli"], $sql);
							// On va faire plusieurs fois le ménage (plusieurs périodes) pour un même élève,
							// mais la liste des logins à retenir est faite hors de la boucle sur les périodes.
						}
					}
				}
			}
			$tab_bulletin[$id_classe][$periode_num]['selection_eleves']=$tab_selection_eleves;


			$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
			if($mode_bulletin=="pdf") {
				if($tab_modele_pdf["active_rang"][$id_classe]==1) {
					$affiche_rang="y";
				}
				else {
					$affiche_rang="n";
				}
			}


			$affiche_nbdev=sql_query1("SELECT display_nbdev FROM classes WHERE id='".$id_classe."'");
			if($mode_bulletin=="pdf") {
				if(($tab_modele_pdf["active_nombre_note"][$id_classe]==1)||($tab_modele_pdf["active_nombre_note_case"][$id_classe]==1)) {
					$affiche_nbdev="y";
				}
				else {
					$affiche_nbdev="n";
				}
			}



			// On teste si on affiche les graphiques
			if (getSettingValue("bull_affiche_graphiques") == 'yes'){$affiche_graph = 'y';}else{$affiche_graph = 'n';}

			//========================================
			// Afficher la moyenne générale? (également conditionné par la présence d'un coef non nul au moins)
			$display_moy_gen = sql_query1("SELECT display_moy_gen FROM classes WHERE id='".$id_classe."'");
			//========================================


			//========================================
			// On teste la présence d'au moins un coeff pour afficher la colonne des coef
			$test_coef = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
			//echo "\$test_coef=$test_coef<br />\n";
			//Afficher les coefficients des matières (uniquement si au moins un coef différent de 0)
			if($test_coef>0){
				$affiche_coef = sql_query1("SELECT display_coef FROM classes WHERE id='".$id_classe."'");
			}
			else{
				$affiche_coef = "n";
			}
			//========================================


			//========================================
			$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
			if ($affiche_categories == "y") {
				$affiche_categories = true;
			} else {
				$affiche_categories = false;
			}

			if($mode_bulletin=="pdf") {
				if(($tab_modele_pdf["active_entete_regroupement"][$id_classe]==1)||($tab_modele_pdf["active_regroupement_cote"][$id_classe]==1)) {
					$affiche_categories = true;
				}
				else {
					$affiche_categories = false;
				}
			}

			// Vérifier si il n'y a pas de bêtise sur les catégories.
			if($affiche_categories) {
				$sql="SELECT DISTINCT jmcc.priority ".
				"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
				"WHERE ( " .
				"jgc.categorie_id = jmcc.categorie_id AND " .
				"jgc.id_classe=jmcc.classe_id AND " .
				"jgc.id_classe='".$id_classe."' AND " .
				"jgm.id_groupe=jgc.id_groupe AND " .
				"m.matiere = jgm.id_matiere" .
				");";
				$res_nb_cat_priorites=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_cat_priorites=mysqli_num_rows($res_nb_cat_priorites);
				//echo "\$nb_cat_priorites=$nb_cat_priorites<br />\n";

				$sql="SELECT DISTINCT jgc.categorie_id ".
				"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
				"WHERE ( " .
				"jgc.categorie_id = jmcc.categorie_id AND " .
				"jgc.id_classe=jmcc.classe_id AND " .
				"jgc.id_classe='".$id_classe."' AND " .
				"jgm.id_groupe=jgc.id_groupe AND " .
				"m.matiere = jgm.id_matiere" .
				");";
				$res_nb_cat=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_cat=mysqli_num_rows($res_nb_cat);
				//echo "\$nb_cat=$nb_cat<br />\n";

				if($nb_cat_priorites!=$nb_cat) {
					// Tester si les catégories de matières ont bien des priorités différentes au niveau Gestion des matières
					// Si ce n'est pas le cas, produire une alerte et sortir

					$sql="SELECT DISTINCT mc.priority ".
					"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m, matieres_categories mc " .
					"WHERE ( " .
					"mc.id=jmcc.categorie_id AND ".
					"jgc.categorie_id = jmcc.categorie_id AND " .
					"jgc.id_classe=jmcc.classe_id AND " .
					"jgc.id_classe='".$id_classe."' AND " .
					"jgm.id_groupe=jgc.id_groupe AND " .
					"m.matiere = jgm.id_matiere" .
					");";
					$res_nb_cat_priorites_glob=mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_cat_priorites_glob=mysqli_num_rows($res_nb_cat_priorites_glob);
					//echo "\$nb_cat_priorites=$nb_cat_priorites<br />\n";

					if($nb_cat_priorites_glob!=$nb_cat) {
						if($mode_bulletin!="pdf") {
							echo "<h1 align='center'>Erreur</h1>";
							echo "<p>Vous avez demandé à afficher les catégories de matières, mais les priorités d'affichage des catégories ne sont pas correctement définies, ni au niveau global dans Gestion des matières, ni au niveau particulier dans Gestion des classes/&lt;Classe&gt; Paramètres<br />Il ne faut pas que deux catégories aient la même priorité sans quoi il peut survenir des anomalies d'ordre des matières sur le bulletin.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}
						else {

							$pdf=new bul_PDF('p', 'mm', 'A4');
							$pdf->SetCreator($gepiSchoolName);
							$pdf->SetAuthor($gepiSchoolName);
							$pdf->SetKeywords('');
							$pdf->SetSubject('Bulletin');
							$pdf->SetTitle('Bulletin');
							$pdf->SetDisplayMode('fullwidth', 'single');
							$pdf->SetCompression(TRUE);
							$pdf->SetAutoPageBreak(TRUE, 5);

							$pdf->AddPage(); //ajout d'une page au document
							$pdf->SetFont('DejaVu');
							$pdf->SetXY(20,20);
							$pdf->SetFontSize(14);
							$pdf->Cell(90,7, "ERREUR",0,2,'');

							$pdf->SetXY(20,40);
							$pdf->SetFontSize(10);
							$pdf->Cell(150,7, "Vous avez demandé à afficher les catégories de matières,",0,2,'');
							$pdf->SetXY(20,45);
							$pdf->Cell(150,7, "mais les priorités d'affichage des catégories ne sont pas correctement définies,",0,2,'');
							$pdf->SetXY(20,50);
							$pdf->Cell(150,7, "ni au niveau global dans Gestion des matières/Éditer les catégories de matières,",0,2,'');
							$pdf->SetXY(20,55);
							$pdf->Cell(150,7, "ni au niveau particulier dans Gestion des classes/<Classe> Paramètres",0,2,'');
							$pdf->SetXY(20,65);
							$pdf->Cell(150,7, "Il ne faut pas que deux catégories aient la même priorité",0,2,'');
							$pdf->SetXY(20,70);
							$pdf->Cell(150,7, "sans quoi il peut survenir des anomalies d'ordre des matières sur le bulletin.",0,2,'');
							$pdf->SetXY(20,80);
							$pdf->Cell(150,7, "Vous pouvez définir un ordre des catégories pour toutes les classes via",0,2,'');
							$pdf->SetXY(20,85);
							$pdf->Cell(150,7, "   Gestion des classes/Paramétrage des classes par lots",0,2,'');

							$nom_bulletin = 'Erreur_bulletin.pdf';
							$pdf->Output($nom_bulletin,'I');
							die();
						}
					}
				}
				
				// Tester si au moins une matière est dans une catégorie autre que AUCUNE...
				$sql="SELECT DISTINCT categorie_id FROM j_groupes_classes jgc, matieres_categories mc WHERE mc.id=jgc.categorie_id AND jgc.id_classe='$id_classe' AND jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n');";
				$test_cat_auc=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test_cat_auc)==0) {
					if($mode_bulletin!="pdf") {
						echo "<h1 align='center'>Erreur</h1>";
						echo "<p>Vous avez demandé à afficher les catégories de matières, mais aucun enseignement n'est dans une catégorie pour la classe n°$id_classe.<br />Contrôlez, en compte administrateur, Gestion des classes/&lt;Classe&gt; Enseignements.</p>\n";
						require("../lib/footer.inc.php");
						die();
					}
					else {

						$pdf=new bul_PDF('p', 'mm', 'A4');
						$pdf->SetCreator($gepiSchoolName);
						$pdf->SetAuthor($gepiSchoolName);
						$pdf->SetKeywords('');
						$pdf->SetSubject('Bulletin');
						$pdf->SetTitle('Bulletin');
						$pdf->SetDisplayMode('fullwidth', 'single');
						$pdf->SetCompression(TRUE);
						$pdf->SetAutoPageBreak(TRUE, 5);

						$pdf->AddPage(); //ajout d'une page au document
						$pdf->SetFont('DejaVu');
						$pdf->SetXY(20,20);
						$pdf->SetFontSize(14);
						$pdf->Cell(90,7, "ERREUR",0,2,'');

						$pdf->SetXY(20,40);
						$pdf->SetFontSize(10);
						$pdf->Cell(150,7, "Vous avez demandé à afficher les catégories de matières,",0,2,'');
						$pdf->SetXY(20,45);
						$pdf->Cell(150,7, "mais aucun enseignement n'est dans une catégorie pour la classe ".get_nom_classe($id_classe).".",0,2,'');
						$pdf->SetXY(20,50);
						$pdf->Cell(150,7, "Contrôlez, en compte administrateur:",0,2,'');
						$pdf->SetXY(20,55);
						$pdf->Cell(150,7, "    Gestion des classes/<Classe> Enseignements",0,2,'');
						$pdf->SetXY(20,60);
						$pdf->Cell(150,7, "Ou bien, modifiez les Paramètres d'impression des bulletins",0,2,'');
						$pdf->SetXY(20,65);
						$pdf->Cell(150,7, "pour ne pas utiliser les catégories de matières.",0,2,'');

						$nom_bulletin = 'Erreur_bulletin.pdf';
						$pdf->Output($nom_bulletin,'I');
						die();
					}
				}
			}

			//========================================

			//$affiche_rang="y";
			if (($affiche_rang == 'y')||
			((isset($_POST['forcer_recalcul_rang']))&&($_POST['forcer_recalcul_rang']=='y'))) {
				// On teste la présence d'au moins un coeff pour afficher la colonne des coef
				$test_coef = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
				include("../lib/calcul_rang.inc.php");
			}


			$tab_bulletin[$id_classe][$periode_num]['test_coef']=$test_coef;
			if($coefficients_a_1=="oui") {
				// On force la valeur de test_coef si on impose des coef 1 pour le calcul des moyennes générales dans /lib/calcul_moy_gen.inc.php
				$tab_bulletin[$id_classe][$periode_num]['test_coef']=1;
			}


			// Informations sur la classe
			$sql="SELECT * FROM classes WHERE id='".$id_classe."';";
			$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
			$lig_classe=mysqli_fetch_object($res_classe);

			$tab_bulletin[$id_classe][$periode_num]['id_classe']=$lig_classe->id;
			$tab_bulletin[$id_classe][$periode_num]['classe']=$lig_classe->classe;
			$tab_bulletin[$id_classe][$periode_num]['classe_nom_complet']=$lig_classe->nom_complet;
			$tab_bulletin[$id_classe][$periode_num]['formule']=$lig_classe->formule;
			$tab_bulletin[$id_classe][$periode_num]['suivi_par']=$lig_classe->suivi_par;

			$classe=$lig_classe->classe;
			$classe_nom_complet=$lig_classe->nom_complet;



			// Récupérer l'effectif de la classe,...
			$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='$periode_num';";
			//echo "\$sql=$sql<br />\n";
			$res_eff_classe=mysqli_query($GLOBALS["mysqli"], $sql);
			//$lig_eff_classe=mysql_fetch_object($res_eff_classe);
			$eff_classe=mysqli_num_rows($res_eff_classe);
			//echo "<p>Effectif de la classe: $eff_classe</p>\n";
			//echo "\$eff_classe=$eff_classe<br />\n";

			//if($eff_classe==0) {
			if(($eff_classe==0)&&($generer_fichiers_pdf_archivage!='y')) {
				if($mode_bulletin!="pdf") {
					echo "<p>La classe '$classe' est vide sur la période '$periode_num'.<br />Il n'est pas possible de poursuivre.</p>\n";
					require("../lib/footer.inc.php");
					die();
				}
				else {

					$pdf=new bul_PDF('p', 'mm', 'A4');
					$pdf->SetCreator($gepiSchoolName);
					$pdf->SetAuthor($gepiSchoolName);
					$pdf->SetKeywords('');
					$pdf->SetSubject('Bulletin');
					$pdf->SetTitle('Bulletin');
					$pdf->SetDisplayMode('fullwidth', 'single');
					$pdf->SetCompression(TRUE);
					$pdf->SetAutoPageBreak(TRUE, 5);

					$pdf->AddPage(); //ajout d'une page au document
					$pdf->SetFont('DejaVu');
					$pdf->SetXY(20,20);
					$pdf->SetFontSize(14);
					$pdf->Cell(90,7, "ERREUR",0,2,'');

					$pdf->SetXY(20,40);
					$pdf->SetFontSize(10);
					$pdf->Cell(150,7, "La classe '$classe' est vide sur la période '$periode_num'.",0,2,'');
					$pdf->SetXY(20,45);
					$pdf->Cell(150,7, "Il n'est pas possible de poursuivre.",0,2,'');

					$nom_bulletin = 'Erreur_bulletin.pdf';
					$pdf->Output($nom_bulletin,'I');
					die();
				}
			}

			// 20120713
			$tab_bulletin[$id_classe][$periode_num]['eff_classe']=$eff_classe;

			// Pour ne pas bloquer dans le cas de l'archivage...
			if($eff_classe==0) {
				enregistre_infos_actions("ERREUR Archivage bulletins PDF", "Aucun bulletin généré pour la classe <a href='classes/classes_const.php?id_classe=$id_classe'>$classe</a> en période $periode_num (la classe est vide sur cette période).",array("administrateur"),'statut');
			}
			else {
				//==============================
				if($mode_bulletin=="html") {
					$motif="Temoin_calcul_moy_gen".$id_classe."_".$periode_num;
					decompte_debug($motif,"$motif avant");
					flush();
				}
				//==============================

				//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				// 20100615
				//$moyennes_periodes_precedentes="y";
				if((
						((isset($moyennes_periodes_precedentes))&&($moyennes_periodes_precedentes=='y'))||
						((isset($evolution_moyenne_periode_precedente))&&($evolution_moyenne_periode_precedente=='y'))
					)&&($periode_num>1)&&(!isset($tab_bulletin[$id_classe][$periode_num]['note_prec']))) {
					//echo "\$moyennes_periodes_precedentes=$moyennes_periodes_precedentes<br />\n";
					//echo "\$evolution_moyenne_periode_precedente=$evolution_moyenne_periode_precedente<br />\n";
					$reserve_periode_num=$periode_num;
					for($periode_num=1;$periode_num<$reserve_periode_num;$periode_num++) {
						//echo "\$periode_num=$periode_num<br />";
						include("../lib/calcul_moy_gen.inc.php");

						$tab_bulletin[$id_classe][$reserve_periode_num]['login_prec'][$periode_num]=$current_eleve_login;
						$tab_bulletin[$id_classe][$reserve_periode_num]['group_prec'][$periode_num]=$current_group;
						if(isset($current_eleve_note)) {
							$tab_bulletin[$id_classe][$reserve_periode_num]['note_prec'][$periode_num]=$current_eleve_note;
						}
						if(isset($current_eleve_statut)) {
							$tab_bulletin[$id_classe][$reserve_periode_num]['statut_prec'][$periode_num]=$current_eleve_statut;
						}
						$tab_bulletin[$id_classe][$reserve_periode_num]['moy_gen_eleve_prec'][$periode_num]=$moy_gen_eleve;

						//============================
						// On vide les variables de la boucle avant le calcul dans calcul_moy_gen.inc.php hors du dispositif périodes précédentes
						unset($moy_gen_eleve);
						unset($moy_gen_classe);
						unset($moy_generale_classe);
						unset($moy_max_classe);
						unset($moy_min_classe);
		
						unset($moy_cat_classe);
						unset($moy_cat_eleve);
		
						unset($quartile1_classe_gen);
						unset($quartile2_classe_gen);
						unset($quartile3_classe_gen);
						unset($quartile4_classe_gen);
						unset($quartile5_classe_gen);
						unset($quartile6_classe_gen);
						unset($place_eleve_classe);
		
						unset($current_eleve_login);
						unset($current_group);
						unset($current_eleve_note);
						unset($current_eleve_statut);
						unset($current_coef);
						unset($categories);
						unset($current_classe_matiere_moyenne);
		
						unset($current_coef_eleve);
						unset($moy_min_classe_grp);
						unset($moy_max_classe_grp);
						unset($current_eleve_rang);
		
						unset($current_group_effectif_avec_note);
		
						unset($current_eleve_app);
						//============================
					}
					$periode_num=$reserve_periode_num;
				}
				//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


				//========================================
				//if($eff_classe>0) {
					include("../lib/calcul_moy_gen.inc.php");
				//}
				// On récupère la plus grande partie des infos via calcul_moy_gen.inc.php
				// Voir en fin du fichier calcul_moy_gen.inc.php la liste des infos récupérées
				//========================================
				//==============================
				if($mode_bulletin=="html") {
					$motif="Temoin_calcul_moy_gen".$id_classe."_".$periode_num;
					decompte_debug($motif,"$motif après");
					flush();
				}
				//==============================

				//echo "\$affiche_categories=$affiche_categories<br />";
				// $affiche_categories=1

				/*
				$classe=get_class_from_id($id_classe);

				$tab_bulletin[$id_classe][$periode_num]['classe']=$classe;
				$tab_bulletin[$id_classe][$periode_num]['id_classe']=$id_classe;

				// Informations sur la période
				$sql="SELECT nom_periode FROM periodes WHERE id_classe='$id_classe' AND num_periode='$periode_num';";
				$res_per=mysql_query($sql);
				$lig_per=mysql_fetch_object($res_per);

				$tab_bulletin[$id_classe][$periode_num]['nom_periode']=$lig_per->nom_periode;
				*/

				/*
				// Récupérer l'effectif de la classe,...
				$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='$periode_num';";
				$res_eff_classe=mysql_query($sql);
				//$lig_eff_classe=mysql_fetch_object($res_eff_classe);
				$eff_classe=mysql_num_rows($res_eff_classe);
				//echo "<p>Effectif de la classe: $eff_classe</p>\n";
				*/

				// Variables simples
				$tab_bulletin[$id_classe][$periode_num]['eff_classe']=$eff_classe;
				//echo "\$eff_classe=$eff_classe<br />\n";
				//echo "\$tab_bulletin[$id_classe][$periode_num]['eff_classe']=".$tab_bulletin[$id_classe][$periode_num]['eff_classe']."<br />\n";

				// Effectif total sur l'année pour pouvoir parcourir tout $tab_rel pour intercaler bulletin/relevé
				$tab_bulletin[$id_classe][$periode_num]['eff_total_classe']=$eff_total_classe;

				// Variables simples
				$tab_bulletin[$id_classe][$periode_num]['affiche_categories']=$affiche_categories;
				$tab_bulletin[$id_classe][$periode_num]['affiche_coef']=$affiche_coef;
				$tab_bulletin[$id_classe][$periode_num]['affiche_rang']=$affiche_rang;
				$tab_bulletin[$id_classe][$periode_num]['affiche_graph']=$affiche_graph;
				$tab_bulletin[$id_classe][$periode_num]['affiche_nbdev']=$affiche_nbdev;

				$tab_bulletin[$id_classe][$periode_num]['display_moy_gen']=$display_moy_gen;

				// Variables récupérées de calcul_moy_gen.inc.php
				// $current_group est un tableau obtenu par get_group()
				$tab_bulletin[$id_classe][$periode_num]['groupe']=$current_group;

				// Variables récupérées de calcul_moy_gen.inc.php
				// Tableaux d'indices [$j][$i] (groupe, élève)
				// A VéRIFIER: Tableaux d'indices [$i][$j] (élève, groupe)
				if(isset($current_eleve_note)) {$tab_bulletin[$id_classe][$periode_num]['note']=$current_eleve_note;}
				if(isset($current_eleve_statut)) {$tab_bulletin[$id_classe][$periode_num]['statut']=$current_eleve_statut;}
				/*
				for($j=0;$j<count($current_eleve_statut);$j++) {
					for($i=0;$i<count($current_eleve_statut[$j]);$i++) {
						echo "\$current_eleve_statut[$j][$i]=".$current_eleve_statut[$j][$i]."<br />";
					}
				}
				*/

				if(isset($current_eleve_rang)) {$tab_bulletin[$id_classe][$periode_num]['rang']=$current_eleve_rang;}
				$tab_bulletin[$id_classe][$periode_num]['coef_eleve']=$current_coef_eleve;

				// Tableaux d'indice $i (correspondant à l'élève)
				$tab_bulletin[$id_classe][$periode_num]['tot_points_eleve']=$tot_points_eleve;
				$tab_bulletin[$id_classe][$periode_num]['total_coef_eleve']=$total_coef_eleve;
				$tab_bulletin[$id_classe][$periode_num]['tot_points_eleve1']=$tot_points_eleve1;
				$tab_bulletin[$id_classe][$periode_num]['total_coef_eleve1']=$total_coef_eleve1;

				// Variables récupérées de calcul_moy_gen.inc.php
				// Tableau d'indice [$i] élève.. mais cette moyenne générale ne prend en compte que les options suivies par l'élève si bien que les moyennes générales de classe diffèrent selon les élèves
				$tab_bulletin[$id_classe][$periode_num]['moy_gen_classe']=$moy_gen_classe;
				$tab_bulletin[$id_classe][$periode_num]['moy_gen_eleve']=$moy_gen_eleve;
				//===============
				// Ajout J.Etheve
				$tab_bulletin[$id_classe][$periode_num]['moy_gen_classe_noncoef']=$moy_gen_classe1;
				$tab_bulletin[$id_classe][$periode_num]['moy_gen_eleve_noncoef']=$moy_gen_eleve1;
				//===============

				// Variables récupérées de calcul_moy_gen.inc.php
				// Variables simples
				$tab_bulletin[$id_classe][$periode_num]['moy_min_classe']=$moy_min_classe;
				$tab_bulletin[$id_classe][$periode_num]['moy_generale_classe']=$moy_generale_classe;
				$tab_bulletin[$id_classe][$periode_num]['moy_max_classe']=$moy_max_classe;
				//===============
				// Ajout J.Etheve
				$tab_bulletin[$id_classe][$periode_num]['moy_min_classe_noncoef']=$moy_min_classe1;
				$tab_bulletin[$id_classe][$periode_num]['moy_generale_classe_noncoef']=$moy_generale_classe1;
				$tab_bulletin[$id_classe][$periode_num]['moy_max_classe_noncoef']=$moy_max_classe1;
				//===============
				$tab_bulletin[$id_classe][$periode_num]['moy_min_classe_grp']=$moy_min_classe_grp;
				$tab_bulletin[$id_classe][$periode_num]['moy_classe_grp']=$current_classe_matiere_moyenne;
				/*
				for($kl=0;$kl<count($current_classe_matiere_moyenne);$kl++) {
					echo "\$current_group[$kl]['name']=".$current_group[$kl]['name']." ";
					echo "\$current_classe_matiere_moyenne[$kl]='".$current_classe_matiere_moyenne[$kl]."'<br />";
				}
				*/
				$tab_bulletin[$id_classe][$periode_num]['moy_max_classe_grp']=$moy_max_classe_grp;


				if($mode_bulletin=="pdf") {
					$tab_bulletin[$id_classe][$periode_num]['affiche_moyenne_general_coef_1']=$tab_modele_pdf["affiche_moyenne_general_coef_1"][$tab_id_classe[$loop_classe]];
					if(($temoin_tous_coef_a_1=='y')||($coefficients_a_1=="oui")) {
						// Si tous les coeff sont à 1, on n'imprime pas deux lignes de moyenne générale (moy.gen.coefficientée d'après Gestion des classes/<Classe> Enseignements et moy.gen avec coef à 1) même si la case est cochée dans le modèle PDF.
						// Si on force les coef à 1, on n'affiche pas non plus deux lignes de moyenne générale
						$tab_bulletin[$id_classe][$periode_num]['affiche_moyenne_general_coef_1']=0;
					}
				}
				else {
					// Pour l'instant en mode HTML, on ne propose pas les deux moyennes
					// Il faut décider où on fait le paramétrage.
					// Les paramètres HTML sont généraux à toutes les classes sauf ceux décidés directement dans bull_index.php alors que les paramètres PDF sont essentiellement liés aux modèles.
					$tab_bulletin[$id_classe][$periode_num]['affiche_moyenne_general_coef_1']=0;
				}
			
				//ERIC
				if($mode_bulletin=="pdf") { // affichage du numéro du responsable
					$tab_bulletin[$id_classe][$periode_num]['affiche_numero_responsable']=$tab_modele_pdf["affiche_numero_responsable"][$tab_id_classe[$loop_classe]];
				}
		
				// Variables récupérées de calcul_moy_gen.inc.php
				// Quartiles au niveau moyenne générale:
				// $place_eleve_classe est un tableau d'indice [$i] le numéro de l'élève
				if(isset($place_eleve_classe)) {$tab_bulletin[$id_classe][$periode_num]['place_eleve_classe']=$place_eleve_classe;}
				// Variables simples
				$tab_bulletin[$id_classe][$periode_num]['quartile1_classe_gen']=$quartile1_classe_gen;
				$tab_bulletin[$id_classe][$periode_num]['quartile2_classe_gen']=$quartile2_classe_gen;
				$tab_bulletin[$id_classe][$periode_num]['quartile3_classe_gen']=$quartile3_classe_gen;
				$tab_bulletin[$id_classe][$periode_num]['quartile4_classe_gen']=$quartile4_classe_gen;
				$tab_bulletin[$id_classe][$periode_num]['quartile5_classe_gen']=$quartile5_classe_gen;
				$tab_bulletin[$id_classe][$periode_num]['quartile6_classe_gen']=$quartile6_classe_gen;

				// Initialisation des quartiles par groupe:
				for($j=0;$j<count($current_group);$j++) {
					$tab_bulletin[$id_classe][$periode_num]['quartile1_grp'][$j]=0;
					$tab_bulletin[$id_classe][$periode_num]['quartile2_grp'][$j]=0;
					$tab_bulletin[$id_classe][$periode_num]['quartile3_grp'][$j]=0;
					$tab_bulletin[$id_classe][$periode_num]['quartile4_grp'][$j]=0;
					$tab_bulletin[$id_classe][$periode_num]['quartile5_grp'][$j]=0;
					$tab_bulletin[$id_classe][$periode_num]['quartile6_grp'][$j]=0;
				}

				$tab_bulletin[$id_classe][$periode_num]['place_eleve']=$place_eleve_grp;
				$tab_bulletin[$id_classe][$periode_num]['quartile1_grp']=$quartile1_grp;
				$tab_bulletin[$id_classe][$periode_num]['quartile2_grp']=$quartile2_grp;
				$tab_bulletin[$id_classe][$periode_num]['quartile3_grp']=$quartile3_grp;
				$tab_bulletin[$id_classe][$periode_num]['quartile4_grp']=$quartile4_grp;
				$tab_bulletin[$id_classe][$periode_num]['quartile5_grp']=$quartile5_grp;
				$tab_bulletin[$id_classe][$periode_num]['quartile6_grp']=$quartile6_grp;
				//for($kl=0;$kl<count($quartile1_grp);$kl++) {echo "\$quartile1_grp[$kl]=".$quartile1_grp[$kl]."<br />";}

				// Variables récupérées de calcul_moy_gen.inc.php
				// Tableaux d'indices [$i][$cat] (où $i: eleve et $cat: $categorie_id)
				$tab_bulletin[$id_classe][$periode_num]['moy_cat_classe']=$moy_cat_classe;
				$tab_bulletin[$id_classe][$periode_num]['moy_cat_min']=$moy_cat_min;
				$tab_bulletin[$id_classe][$periode_num]['moy_cat_max']=$moy_cat_max;

				$tab_bulletin[$id_classe][$periode_num]['moy_cat_eleve']=$moy_cat_eleve;

				// matieres_categories(id,nom_court,nom_complet,priority)
				// j_matieres_categories_classes(categorie_id,classe_id,priority,affiche_moyenne)
				// matieres(matiere,nom_complet,priority,categorie_id,matiere_aid,matiere_atelier)
				for($j=0;$j<count($current_group);$j++) {
					//echo "\$current_group[$j]['id']=".$current_group[$j]['id']."<br />";
					//echo "\$current_group[$j]['name']=".$current_group[$j]['name']."<br />";
					//echo "\$current_group[$j]['matiere']['matiere']=".$current_group[$j]['matiere']['matiere']."<br />";
					if(isset($current_group[$j]['matiere']['matiere'])) {
		                /*
						$sql="SELECT mc.id,
									mc.nom_court,
									mc.nom_complet,
									jmcc.priority,
									jmcc.affiche_moyenne
								FROM j_matieres_categories_classes jmcc,
									matieres_categories mc,
									matieres m
								WHERE jmcc.classe_id='$id_classe' AND
									jmcc.categorie_id=m.categorie_id AND
									jmcc.categorie_id=mc.id AND
									m.matiere='".$current_group[$j]['matiere']['matiere']."'
								ORDER BY mc.priority, jmcc.priority, jmcc.categorie_id;";
		                */
								//ORDER BY jmcc.categorie_id, jmcc.priority, mc.priority;";
						$sql="SELECT mc.id,
									mc.nom_court,
									mc.nom_complet,
									jmcc.priority,
									jmcc.affiche_moyenne
								FROM j_matieres_categories_classes jmcc,
									matieres_categories mc,
		                            j_groupes_classes jgc
								WHERE jmcc.classe_id='$id_classe' AND
		                              jgc.id_classe=jmcc.classe_id AND
		                              jgc.categorie_id=jmcc.categorie_id AND
									jmcc.categorie_id=mc.id AND
									jgc.id_groupe='".$current_group[$j]['id']."'
								ORDER BY mc.priority, jmcc.priority, jmcc.categorie_id;";
		                //echo "\$current_group[$j]['matiere']['matiere']=".$current_group[$j]['matiere']['matiere']."<br />";
		                //echo "$sql<br />";
						$res_cat=mysqli_query($GLOBALS["mysqli"], $sql);

						if(mysqli_num_rows($res_cat)>0) {
							$lig_cat=mysqli_fetch_object($res_cat);

							$tab_bulletin[$id_classe][$periode_num]['cat_id'][$j]=$lig_cat->id;
							$tab_bulletin[$id_classe][$periode_num]['nom_cat_court'][$j]=$lig_cat->nom_court;
							$tab_bulletin[$id_classe][$periode_num]['nom_cat_complet'][$j]=$lig_cat->nom_complet;
							$tab_bulletin[$id_classe][$periode_num]['priority'][$j]=$lig_cat->priority;
							$tab_bulletin[$id_classe][$periode_num]['affiche_moyenne'][$j]=$lig_cat->affiche_moyenne;
						}
					}
				}



				if ($affiche_rang == 'y'){
					for($j=0;$j<count($current_group);$j++) {
						/*
						$sql="SELECT 1=1
								FROM j_eleves_groupe jeg
								WHERE jeg.id_groupe='".$current_group[$j]['id']."' AND
									jeg.periode='$periode_num';";
						$res_eff_grp=mysql_query($sql);

						$tab_bulletin[$id_classe][$periode_num]['groupe'][$j]['effectif']=mysql_num_rows($res_cat);
						*/
						// On pourrait utiliser count($tab_bulletin[$id_classe][$periode_num]['groupe'][$j]["eleves"][$key]["list"])
						// avec $key=$periode_num
						$tab_bulletin[$id_classe][$periode_num]['groupe'][$j]['effectif']=count($tab_bulletin[$id_classe][$periode_num]['groupe'][$j]["eleves"][$periode_num]["list"]);

						$tab_bulletin[$id_classe][$periode_num]['groupe'][$j]['effectif_avec_note']=$current_group_effectif_avec_note[$j];
					}
				}


				// L'ordre des matières est obtenu via calcul_moy_gen.inc.php dans lequel le $affiche_categorie fixe l'ordre par catégories ou non.



				// Boucle élèves de la classe $id_classe pour la période $periode_num
				for($i=0;$i<count($current_eleve_login);$i++) {
					// Réinitialisation pour ne pas récupérer des infos de l'élève précédent
					unset($tab_ele);
					$tab_ele=array();

					if (in_array($current_eleve_login[$i],$tab_bulletin[$id_classe][$periode_num]['selection_eleves'])) {

						// ++++++++++++++++++++++++++++++++++++++
						// ++++++++++++++++++++++++++++++++++++++
						// AJOUTER UN TEST: L'élève fait-il bien partie de la classe?
						//                  Inutile: la liste $current_eleve_login est obtenue de calcul_moy_gen.inc.php
						//                  Pas d'injection/intervention possible.
						//                  Le test sur l'accès à la classe plus haut (*) doit suffire.
						//                  On pourrait injecter un login dans la sélection d'élève, mais pas dans $current_eleve_login
						//                  Et on test seulement si $current_eleve_login[$i] est bien dans la sélection (pas le contraire)
						//                  (*) dans cette section tout de même.
						// ++++++++++++++++++++++++++++++++++++++
						// ++++++++++++++++++++++++++++++++++++++

						//==============================

						if($mode_bulletin=="html") {
							$motif="Temoin_eleve".$id_classe."_".$periode_num;
							decompte_debug($motif,"$motif élève $i: ".$current_eleve_login[$i]);
							flush();
							echo "<script type='text/javascript'>
		document.getElementById('td_ele').innerHTML='".$current_eleve_login[$i]."';
	</script>\n";
						}
						//==============================

						// Récup des infos sur l'élève, les responsables, le PP, le CPE,...
						$sql="SELECT * FROM eleves e WHERE e.login='".$current_eleve_login[$i]."';";
						$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
						$lig_ele=mysqli_fetch_object($res_ele);

						$tab_ele['login']=$current_eleve_login[$i];
						$tab_ele['nom']=$lig_ele->nom;
						$tab_ele['prenom']=$lig_ele->prenom;
						$tab_ele['sexe']=$lig_ele->sexe;
						$tab_ele['naissance']=formate_date($lig_ele->naissance);
						$tab_ele['lieu_naissance']=get_commune($lig_ele->lieu_naissance,2);
						$tab_ele['elenoet']=$lig_ele->elenoet;
						$tab_ele['ele_id']=$lig_ele->ele_id;
						$tab_ele['no_gep']=$lig_ele->no_gep;

						$tab_ele['classe']=$classe;
						$tab_ele['id_classe']=$id_classe;
						$tab_ele['classe_nom_complet']=$classe_nom_complet;

						// Régime et redoublement
						$sql="SELECT * FROM j_eleves_regime WHERE login='".$current_eleve_login[$i]."';";
						$res_ele_reg=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_ele_reg)>0) {
							$lig_ele_reg=mysqli_fetch_object($res_ele_reg);

							$tab_ele['regime']=$lig_ele_reg->regime;
							$tab_ele['doublant']=$lig_ele_reg->doublant;
						}

						//$sql="SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve ='".$current_eleve_login[$i]."' AND e.id = j.id_etablissement);";
						$sql="SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve ='".$tab_ele['elenoet']."' AND e.id = j.id_etablissement);";
						//echo "$sql<br />";
						$data_etab = mysqli_query($GLOBALS["mysqli"], $sql);
						$tab_ele['etab_id']="";
						$tab_ele['etab_nom']="";
						$tab_ele['etab_niveau']="";
						$tab_ele['etab_type']="";
						$tab_ele['etab_cp']="";
						$tab_ele['etab_ville']="";
						if(mysqli_num_rows($data_etab)>0) {
							$tab_ele['etab_id'] = @old_mysql_result($data_etab, 0, "id");
							$tab_ele['etab_nom'] = @old_mysql_result($data_etab, 0, "nom");
							$tab_ele['etab_niveau'] = @old_mysql_result($data_etab, 0, "niveau");
							$tab_ele['etab_type'] = @old_mysql_result($data_etab, 0, "type");
							$tab_ele['etab_cp'] = @old_mysql_result($data_etab, 0, "cp");
							$tab_ele['etab_ville'] = @old_mysql_result($data_etab, 0, "ville");

							if ($tab_ele['etab_niveau']!='') {
								foreach ($type_etablissement as $type_etab => $nom_etablissement) {
									if ($tab_ele['etab_niveau'] == $type_etab) {
										$tab_ele['etab_niveau_nom']=$nom_etablissement;
									}
								}
								if ($tab_ele['etab_cp']==0) {
									$tab_ele['etab_cp']='';
								}

								if (($tab_ele['etab_type']=='aucun')||($tab_ele['etab_type']=='')||($tab_ele['etab_niveau']=='')) {
									$tab_ele['etab_type']='';
								}
								else {
									//$tab_ele['etab_type']= $type_etablissement2[remplace_accents($tab_ele['etab_type'],'')][remplace_accents($tab_ele['etab_niveau'],'')];
									if(my_strtoupper($tab_ele['etab_niveau'])=='EREA') {
										$tmp_etab_niveau='EREA';
									}
									else {
										$tmp_etab_niveau=my_strtolower(remplace_accents($tab_ele['etab_niveau'],''));
									}
									$tab_ele['etab_type']= $type_etablissement2[my_strtolower(remplace_accents($tab_ele['etab_type'],''))][$tmp_etab_niveau];
									//echo "\$type_etablissement2[".$tab_ele['etab_type']."][".$tab_ele['etab_niveau']."]=".$type_etablissement2[remplace_accents($tab_ele['etab_type'],'')][remplace_accents($tab_ele['etab_niveau'],'')]."<br />\n";
								}
							}
						}

						// Récup infos CPE
						$sql="SELECT u.* FROM j_eleves_cpe jec, utilisateurs u WHERE e_login='".$current_eleve_login[$i]."' AND jec.cpe_login=u.login;";
						$res_cpe=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_cpe)>0) {
							$lig_cpe=mysqli_fetch_object($res_cpe);
							$tab_ele['cpe']=array();

							$tab_ele['cpe']['login']=$lig_cpe->login;
							$tab_ele['cpe']['nom']=$lig_cpe->nom;
							$tab_ele['cpe']['prenom']=$lig_cpe->prenom;
							$tab_ele['cpe']['civilite']=$lig_cpe->civilite;
						}

						// Récup infos Prof Principal (prof_suivi)
						$sql="SELECT u.* FROM j_eleves_professeurs jep, utilisateurs u WHERE jep.login='".$current_eleve_login[$i]."' AND id_classe='$id_classe' AND jep.professeur=u.login;";
						$res_pp=mysqli_query($GLOBALS["mysqli"], $sql);
						//echo "$sql<br />";
						if(mysqli_num_rows($res_pp)>0) {
							$tab_ele['pp']=array();

							$cpt_pp=0;
							while($lig_pp=mysqli_fetch_object($res_pp)) {
								$tab_ele['pp'][$cpt_pp]=array();
								$tab_ele['pp'][$cpt_pp]['login']=$lig_pp->login;
								$tab_ele['pp'][$cpt_pp]['nom']=$lig_pp->nom;
								$tab_ele['pp'][$cpt_pp]['prenom']=$lig_pp->prenom;
								$tab_ele['pp'][$cpt_pp]['civilite']=$lig_pp->civilite;
								$cpt_pp++;
							}
						}

						// Récup infos des profs principaux (éventuellement multiples) associés à la classe:
						$sql="SELECT DISTINCT u.login, u.nom, u.prenom, u.civilite FROM j_eleves_professeurs jep, utilisateurs u WHERE jep.id_classe='$id_classe' AND jep.professeur=u.login;";
						$res_pp=mysqli_query($GLOBALS["mysqli"], $sql);
						//echo "$sql<br />";
						if(mysqli_num_rows($res_pp)>0) {
							$tab_ele['pp_classe']=array();

							$cpt_pp=0;
							while($lig_pp=mysqli_fetch_object($res_pp)) {
								$tab_ele['pp_classe'][$cpt_pp]=array();
								$tab_ele['pp_classe'][$cpt_pp]['login']=$lig_pp->login;
								$tab_ele['pp_classe'][$cpt_pp]['nom']=$lig_pp->nom;
								$tab_ele['pp_classe'][$cpt_pp]['prenom']=$lig_pp->prenom;
								$tab_ele['pp_classe'][$cpt_pp]['civilite']=$lig_pp->civilite;
								$cpt_pp++;
							}
						}

						// Récup infos responsables
						$sql="SELECT rp.*,ra.adr1,ra.adr2,ra.adr3,ra.adr3,ra.adr4,ra.cp,ra.pays,ra.commune,r.resp_legal FROM resp_pers rp,
														resp_adr ra,
														responsables2 r
									WHERE r.ele_id='".$tab_ele['ele_id']."' AND
											r.resp_legal!='0' AND
											r.pers_id=rp.pers_id AND
											rp.adr_id=ra.adr_id
									ORDER BY resp_legal;";
						$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
						//echo "$sql<br />";
						if(mysqli_num_rows($res_resp)>0) {
							$cpt=0;
							while($lig_resp=mysqli_fetch_object($res_resp)) {
								$tab_ele['resp'][$cpt]=array();

								$tab_ele['resp'][$cpt]['pers_id']=$lig_resp->pers_id;

								$tab_ele['resp'][$cpt]['login']=$lig_resp->login;
								$tab_ele['resp'][$cpt]['nom']=$lig_resp->nom;
								$tab_ele['resp'][$cpt]['prenom']=$lig_resp->prenom;
								$tab_ele['resp'][$cpt]['civilite']=$lig_resp->civilite;
								$tab_ele['resp'][$cpt]['tel_pers']=$lig_resp->tel_pers;
								$tab_ele['resp'][$cpt]['tel_port']=$lig_resp->tel_port;
								$tab_ele['resp'][$cpt]['tel_prof']=$lig_resp->tel_prof;

								$tab_ele['resp'][$cpt]['adr1']=$lig_resp->adr1;
								$tab_ele['resp'][$cpt]['adr2']=$lig_resp->adr2;
								$tab_ele['resp'][$cpt]['adr3']=$lig_resp->adr3;
								$tab_ele['resp'][$cpt]['adr4']=$lig_resp->adr4;
								$tab_ele['resp'][$cpt]['cp']=$lig_resp->cp;
								$tab_ele['resp'][$cpt]['pays']=$lig_resp->pays;
								$tab_ele['resp'][$cpt]['commune']=$lig_resp->commune;

								$tab_ele['resp'][$cpt]['adr_id']=$lig_resp->adr_id;

								$tab_ele['resp'][$cpt]['resp_legal']=$lig_resp->resp_legal;

								$cpt++;
							}
						}

						// Vérification
						if(mysqli_num_rows($res_resp)>2) {
							if($mode_bulletin=="html") {
								echo "<div class='alerte_erreur'><b style='color:red;'>Erreur:</b>";
								echo $tab_ele['nom']." ".$tab_ele['prenom']." a plus de deux responsables légaux 1 et 2.<br />C'est une anomalie.<br />";
								for ($z=0;$z<count($tab_ele['resp']);$z++) {
									echo $tab_ele['resp'][$z]['nom']." ".$tab_ele['resp'][$z]['prenom']." (<i>responsable légal ".$tab_ele['resp'][$z]['resp_legal']."</i>)<br />";
								}
								echo "Seuls les deux premiers apparaitront sur des bulletins.";
								echo "</div>\n";
							}
						}


						// Rang
						if ($affiche_rang == 'y'){
							$rang = sql_query1("select rang from j_eleves_classes where (
							periode = '".$periode_num."' and
							id_classe = '".$id_classe."' and
							login = '".$current_eleve_login[$i]."' )
							");
							if (($rang == 0)||($rang == -1)) {
								$rang = "-";
							}

							// Rang de l'élève dans la classe (par rapport à la moyenne générale)
							$tab_bulletin[$id_classe][$periode_num]['rang_classe'][$i]=$rang;
						}

						//========================
						// Récupérer les infos AID

						// Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
						if ($bull_affiche_aid == 'y') {
							//==============================
							if($mode_bulletin=="html") {
								$motif="Temoin_eleve".$id_classe."_".$periode_num."_".$i;
								decompte_debug($motif,"$motif élève $i (".$current_eleve_login[$i].") avant AID");
								flush();
							}
							//==============================

							// On attaque maintenant l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en tête des bulletins :
							//------------------------------
							// $z est l'indice de $call_data_aid_b
							$z=0;
							// $zz est l'indice des AID effectivement utiles pour l'élève et la période
							$zz=0;
							while ($z < $nb_aid_b) {
								$display_begin = @old_mysql_result($call_data_aid_b, $z, "display_begin");
								$display_end = @old_mysql_result($call_data_aid_b, $z, "display_end");
								$type_note = @old_mysql_result($call_data_aid_b, $z, "type_note");
								$note_max = @old_mysql_result($call_data_aid_b, $z, "note_max");
								/*
								echo "\$z=$z<br />";
								echo "\$display_begin=$display_begin<br />";
								echo "\$display_end=$display_end<br />";
								echo "\$type_note=$type_note<br />";
								echo "\$note_max=$note_max<br />";
								*/
								if (($periode_num >= $display_begin) and ($periode_num <= $display_end)) {
									$indice_aid = @old_mysql_result($call_data_aid_b, $z, "indice_aid");
									//$aid_query = mysql_query("SELECT id_aid FROM j_aid_eleves WHERE (login='".$current_eleve_login[$i]."' and indice_aid='$indice_aid')");
									$sql="SELECT id_aid FROM j_aid_eleves WHERE (login='".$current_eleve_login[$i]."' and indice_aid='$indice_aid');";
									//echo "$sql<br />";
									$aid_query = mysqli_query($GLOBALS["mysqli"], $sql);
									$aid_id = @old_mysql_result($aid_query, 0, "id_aid");
									if ($aid_id != '') {

										$tab_ele['aid_b'][$zz]['display_begin']=$display_begin;
										$tab_ele['aid_b'][$zz]['display_end']=$display_end;

										$tab_ele['aid_b'][$zz]['nom']=@old_mysql_result($call_data_aid_b, $z, "nom");
										$tab_ele['aid_b'][$zz]['nom_complet']=@old_mysql_result($call_data_aid_b, $z, "nom_complet");
										$tab_ele['aid_b'][$zz]['message']=@old_mysql_result($call_data_aid_b, $z, "message");

										$tab_ele['aid_b'][$zz]['display_nom']=@old_mysql_result($call_data_aid_b, $z, "display_nom");

										//echo "\$tab_ele['aid_b'][$zz]['nom_complet']=".$tab_ele['aid_b'][$zz]['nom_complet']."<br />";
										//echo "\$type_note=".$type_note."<br />";

										$aid_nom_query = mysqli_query($GLOBALS["mysqli"], "SELECT nom FROM aid WHERE (id='$aid_id' and indice_aid='$indice_aid');");
										$tab_ele['aid_b'][$zz]['aid_nom']=@old_mysql_result($aid_nom_query, 0, "nom");

										//echo "\$tab_ele['aid_b'][$zz]['aid_nom']=".$tab_ele['aid_b'][$z]['aid_nom']."<br />";

										// On regarde maintenant quelle sont les profs responsables de cette AID
										$aid_prof_resp_query = mysqli_query($GLOBALS["mysqli"], "SELECT id_utilisateur FROM j_aid_utilisateurs WHERE (id_aid='$aid_id'  and indice_aid='$indice_aid')");
										$nb_lig = mysqli_num_rows($aid_prof_resp_query);
										$n = '0';
										while ($n < $nb_lig) {
											//$aid_prof_resp_login[$n] = old_mysql_result($aid_prof_resp_query, $n, "id_utilisateur");
											$tab_ele['aid_b'][$zz]['aid_prof_resp_login'][$n]=old_mysql_result($aid_prof_resp_query, $n, "id_utilisateur");

											//echo "\$tab_ele['aid_b'][$zz]['aid_prof_resp_login'][$n]=".$tab_ele['aid_b'][$zz]['aid_prof_resp_login'][$n]."<br />";

											$n++;
										}


										// Initialisation pour le cas d'une période avec appréciation seule (note sur une autre préiode)
										$tab_ele['aid_b'][$zz]['aid_note']='-';
										$tab_ele['aid_b'][$zz]['aid_statut']='';
										$tab_ele['aid_b'][$zz]['aid_note_moyenne']='-';
										$tab_ele['aid_b'][$zz]['aid_note_max']='-';
										$tab_ele['aid_b'][$zz]['aid_note_min']='-';

										//------
										// On appelle l'appréciation de l'élève, et sa note
										//------
										$current_eleve_aid_appreciation_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_appreciations WHERE (login='".$current_eleve_login[$i]."' AND periode='$periode_num' and id_aid='$aid_id' and indice_aid='$indice_aid')");
										$current_eleve_aid_appreciation = @old_mysql_result($current_eleve_aid_appreciation_query, 0, "appreciation");
										$periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$id_classe'");
										$periode_max = mysqli_num_rows($periode_query);
										if ($type_note == 'last') {$last_periode_aid = min($periode_max,$display_end);}
										if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {
											$place_eleve = "";
											$current_eleve_aid_note = @old_mysql_result($current_eleve_aid_appreciation_query, 0, "note");
											$current_eleve_aid_statut = @old_mysql_result($current_eleve_aid_appreciation_query, 0, "statut");
											if (($current_eleve_aid_statut == '') and ($note_max != 20) ) {
												$current_eleve_aid_appreciation = "(note sur ".$note_max.") ".$current_eleve_aid_appreciation;
											}
											if ($current_eleve_aid_note == '') {
												$current_eleve_aid_note = '-';
											} else {
												if ($affiche_graph == 'y')  {
													if ($current_eleve_aid_note<5) { $place_eleve=6;}
													if (($current_eleve_aid_note>=5) and ($current_eleve_aid_note<8))  { $place_eleve=5;}
													if (($current_eleve_aid_note>=8) and ($current_eleve_aid_note<10)) { $place_eleve=4;}
													if (($current_eleve_aid_note>=10) and ($current_eleve_aid_note<12)) {$place_eleve=3;}
													if (($current_eleve_aid_note>=12) and ($current_eleve_aid_note<15)) { $place_eleve=2;}
													if ($current_eleve_aid_note>=15) { $place_eleve=1;}

													// Pas idéal: on fait ces requêtes autant de fois qu'il y a d'élève...
													$quartile1_classe = sql_query1("SELECT COUNT( a.note ) as quartile1 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=15)");
													$quartile2_classe = sql_query1("SELECT COUNT( a.note ) as quartile2 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=12 AND a.note<15)");
													$quartile3_classe = sql_query1("SELECT COUNT( a.note ) as quartile3 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=10 AND a.note<12)");
													$quartile4_classe = sql_query1("SELECT COUNT( a.note ) as quartile4 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=8 AND a.note<10)");
													$quartile5_classe = sql_query1("SELECT COUNT( a.note ) as quartile5 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=5 AND a.note<8)");
													$quartile6_classe = sql_query1("SELECT COUNT( a.note ) as quartile6 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note<5)");

													$tab_ele['aid_b'][$zz]['quartile1_classe']=$quartile1_classe;
													$tab_ele['aid_b'][$zz]['quartile2_classe']=$quartile2_classe;
													$tab_ele['aid_b'][$zz]['quartile3_classe']=$quartile3_classe;
													$tab_ele['aid_b'][$zz]['quartile4_classe']=$quartile4_classe;
													$tab_ele['aid_b'][$zz]['quartile5_classe']=$quartile5_classe;
													$tab_ele['aid_b'][$zz]['quartile6_classe']=$quartile6_classe;

												}
												$current_eleve_aid_note=number_format($current_eleve_aid_note,1, ',', ' ');
											}
											$aid_note_min_query = mysqli_query($GLOBALS["mysqli"], "SELECT MIN(note) note_min FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");

											$aid_note_min = @old_mysql_result($aid_note_min_query, 0, "note_min");
											if ($aid_note_min == '') {
												$aid_note_min = '-';
											} else {
												$aid_note_min=number_format($aid_note_min,1, ',', ' ');
											}
											$aid_note_max_query = mysqli_query($GLOBALS["mysqli"], "SELECT MAX(note) note_max FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
											$aid_note_max = @old_mysql_result($aid_note_max_query, 0, "note_max");

											if ($aid_note_max == '') {
												$aid_note_max = '-';
											} else {
												$aid_note_max=number_format($aid_note_max,1, ',', ' ');
											}

											$aid_note_moyenne_query = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(note),1) moyenne FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
											$aid_note_moyenne = @old_mysql_result($aid_note_moyenne_query, 0, "moyenne");
											if ($aid_note_moyenne == '') {
												$aid_note_moyenne = '-';
											} else {
												$aid_note_moyenne=number_format($aid_note_moyenne,1, ',', ' ');
											}

											$tab_ele['aid_b'][$zz]['aid_note']=$current_eleve_aid_note;
											$tab_ele['aid_b'][$zz]['aid_statut']=$current_eleve_aid_statut;
											$tab_ele['aid_b'][$zz]['aid_note_moyenne']=$aid_note_moyenne;
											$tab_ele['aid_b'][$zz]['aid_note_max']=$aid_note_max;
											$tab_ele['aid_b'][$zz]['aid_note_min']=$aid_note_min;
											$tab_ele['aid_b'][$zz]['place_eleve']=$place_eleve;
										}

										if ($type_note == 'no') {
											$tab_ele['aid_b'][$zz]['aid_note']='-';
											$tab_ele['aid_b'][$zz]['aid_statut']='';
											$tab_ele['aid_b'][$zz]['aid_note_moyenne']='-';
											$tab_ele['aid_b'][$zz]['aid_note_max']='-';
											$tab_ele['aid_b'][$zz]['aid_note_min']='-';
											//$tab_ele['aid_b'][$zz]['place_eleve']=$place_eleve;
										}

										$tab_ele['aid_b'][$zz]['aid_appreciation']=$current_eleve_aid_appreciation;

										//echo "\$tab_ele['aid_b'][$z]['aid_appreciation']=".$tab_ele['aid_b'][$z]['aid_appreciation']."<br />";

										// Vaut-il mieux un tableau $tab_ele['aid_b']
										// ou calquer sur $tab_bulletin[$id_classe][$periode_num]['groupe']
										// La deuxième solution réduirait sans doute le nombre de requêtes

										$zz++;
									}
								}
								$z++;
							}


							//echo "<p>".$tab_ele['login']."<br />";
							// On attaque maintenant l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en fin des bulletins :
							//------------------------------
							// $z est l'indice de $call_data_aid_e
							$z=0;
							// $zz est l'indice des AID effectivement utiles pour l'élève et la période
							$zz=0;
							while ($z < $nb_aid_e) {
								$display_begin = @old_mysql_result($call_data_aid_e, $z, "display_begin");
								$display_end = @old_mysql_result($call_data_aid_e, $z, "display_end");
								$type_note = @old_mysql_result($call_data_aid_e, $z, "type_note");
								$note_max = @old_mysql_result($call_data_aid_e, $z, "note_max");

								/*
								echo "\$z=$z<br />";
								echo "\$display_begin=$display_begin<br />";
								echo "\$display_end=$display_end<br />";
								echo "\$type_note=$type_note<br />";
								echo "\$note_max=$note_max<br />";
								*/

								if (($periode_num >= $display_begin) and ($periode_num <= $display_end)) {
									$indice_aid = @old_mysql_result($call_data_aid_e, $z, "indice_aid");
									$aid_query = mysqli_query($GLOBALS["mysqli"], "SELECT id_aid FROM j_aid_eleves WHERE (login='".$current_eleve_login[$i]."' and indice_aid='$indice_aid')");
									$aid_id = @old_mysql_result($aid_query, 0, "id_aid");
									if ($aid_id != '') {

										$tab_ele['aid_e'][$zz]['display_begin']=$display_begin;
										$tab_ele['aid_e'][$zz]['display_end']=$display_end;

										$tab_ele['aid_e'][$zz]['nom']=@old_mysql_result($call_data_aid_e, $z, "nom");
										$tab_ele['aid_e'][$zz]['nom_complet']=@old_mysql_result($call_data_aid_e, $z, "nom_complet");
										$tab_ele['aid_e'][$zz]['message']=@old_mysql_result($call_data_aid_e, $z, "message");

										$tab_ele['aid_e'][$zz]['display_nom']=@old_mysql_result($call_data_aid_e, $z, "display_nom");

										//echo "\$tab_ele['aid_e'][$zz]['nom_complet']=".$tab_ele['aid_e'][$zz]['nom_complet']."<br />";
										//echo "\$type_note=".$type_note."<br />";

										$aid_nom_query = mysqli_query($GLOBALS["mysqli"], "SELECT nom FROM aid WHERE (id='$aid_id' and indice_aid='$indice_aid');");
										$tab_ele['aid_e'][$zz]['aid_nom']=@old_mysql_result($aid_nom_query, 0, "nom");

										//echo "\$tab_ele['aid_e'][$zz]['aid_nom']=".$tab_ele['aid_e'][$zz]['aid_nom']."<br />";

										// On regarde maintenant quelle sont les profs responsables de cette AID
										$aid_prof_resp_query = mysqli_query($GLOBALS["mysqli"], "SELECT id_utilisateur FROM j_aid_utilisateurs WHERE (id_aid='$aid_id'  and indice_aid='$indice_aid')");
										$nb_lig = mysqli_num_rows($aid_prof_resp_query);
										$n = '0';
										while ($n < $nb_lig) {
											//$aid_prof_resp_login[$n] = old_mysql_result($aid_prof_resp_query, $n, "id_utilisateur");
											$tab_ele['aid_e'][$zz]['aid_prof_resp_login'][$n]=old_mysql_result($aid_prof_resp_query, $n, "id_utilisateur");

											//echo "\$tab_ele['aid_e'][$zz]['aid_prof_resp_login'][$n]=".$tab_ele['aid_e'][$zz]['aid_prof_resp_login'][$n]."<br />";

											$n++;
										}

										// Initialisation pour le cas d'une période avec appréciation seule (note sur une autre préiode)
										$tab_ele['aid_e'][$zz]['aid_note']='-';
										$tab_ele['aid_e'][$zz]['aid_statut']='';
										$tab_ele['aid_e'][$zz]['aid_note_moyenne']='-';
										$tab_ele['aid_e'][$zz]['aid_note_max']='-';
										$tab_ele['aid_e'][$zz]['aid_note_min']='-';


										//------
										// On appelle l'appréciation de l'élève, et sa note
										//------
										$current_eleve_aid_appreciation_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_appreciations WHERE (login='".$current_eleve_login[$i]."' AND periode='$periode_num' and id_aid='$aid_id' and indice_aid='$indice_aid')");
										$current_eleve_aid_appreciation = @old_mysql_result($current_eleve_aid_appreciation_query, 0, "appreciation");
										$periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$id_classe'");
										$periode_max = mysqli_num_rows($periode_query);
										if ($type_note == 'last') {$last_periode_aid = min($periode_max,$display_end);}
										if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {
											$place_eleve = "";
											$current_eleve_aid_note = @old_mysql_result($current_eleve_aid_appreciation_query, 0, "note");
											$current_eleve_aid_statut = @old_mysql_result($current_eleve_aid_appreciation_query, 0, "statut");
											if (($current_eleve_aid_statut == '') and ($note_max != 20) ) {
												$current_eleve_aid_appreciation = "(note sur ".$note_max.") ".$current_eleve_aid_appreciation;
											}
											if ($current_eleve_aid_note == '') {
												$current_eleve_aid_note = '-';
											} else {
												if ($affiche_graph == 'y')  {
													if ($current_eleve_aid_note<5) { $place_eleve=6;}
													if (($current_eleve_aid_note>=5) and ($current_eleve_aid_note<8))  { $place_eleve=5;}
													if (($current_eleve_aid_note>=8) and ($current_eleve_aid_note<10)) { $place_eleve=4;}
													if (($current_eleve_aid_note>=10) and ($current_eleve_aid_note<12)) {$place_eleve=3;}
													if (($current_eleve_aid_note>=12) and ($current_eleve_aid_note<15)) { $place_eleve=2;}
													if ($current_eleve_aid_note>=15) { $place_eleve=1;}

													// Pas idéal: on fait ces requêtes autant de fois qu'il y a d'élève...
													$quartile1_classe = sql_query1("SELECT COUNT( a.note ) as quartile1 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=15)");
													$quartile2_classe = sql_query1("SELECT COUNT( a.note ) as quartile2 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=12 AND a.note<15)");
													$quartile3_classe = sql_query1("SELECT COUNT( a.note ) as quartile3 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=10 AND a.note<12)");
													$quartile4_classe = sql_query1("SELECT COUNT( a.note ) as quartile4 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=8 AND a.note<10)");
													$quartile5_classe = sql_query1("SELECT COUNT( a.note ) as quartile5 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=5 AND a.note<8)");
													$quartile6_classe = sql_query1("SELECT COUNT( a.note ) as quartile6 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note<5)");

													$tab_ele['aid_e'][$zz]['quartile1_classe']=$quartile1_classe;
													$tab_ele['aid_e'][$zz]['quartile2_classe']=$quartile2_classe;
													$tab_ele['aid_e'][$zz]['quartile3_classe']=$quartile3_classe;
													$tab_ele['aid_e'][$zz]['quartile4_classe']=$quartile4_classe;
													$tab_ele['aid_e'][$zz]['quartile5_classe']=$quartile5_classe;
													$tab_ele['aid_e'][$zz]['quartile6_classe']=$quartile6_classe;

												}
												$current_eleve_aid_note=number_format($current_eleve_aid_note,1, ',', ' ');
											}
											$aid_note_min_query = mysqli_query($GLOBALS["mysqli"], "SELECT MIN(note) note_min FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");

											$aid_note_min = @old_mysql_result($aid_note_min_query, 0, "note_min");
											if ($aid_note_min == '') {
												$aid_note_min = '-';
											} else {
												$aid_note_min=number_format($aid_note_min,1, ',', ' ');
											}
											$aid_note_max_query = mysqli_query($GLOBALS["mysqli"], "SELECT MAX(note) note_max FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
											$aid_note_max = @old_mysql_result($aid_note_max_query, 0, "note_max");

											if ($aid_note_max == '') {
												$aid_note_max = '-';
											} else {
												$aid_note_max=number_format($aid_note_max,1, ',', ' ');
											}

											$aid_note_moyenne_query = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(note),1) moyenne FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
											$aid_note_moyenne = @old_mysql_result($aid_note_moyenne_query, 0, "moyenne");
											if ($aid_note_moyenne == '') {
												$aid_note_moyenne = '-';
											} else {
												$aid_note_moyenne=number_format($aid_note_moyenne,1, ',', ' ');
											}

											$tab_ele['aid_e'][$zz]['aid_note']=$current_eleve_aid_note;
											$tab_ele['aid_e'][$zz]['aid_statut']=$current_eleve_aid_statut;
											$tab_ele['aid_e'][$zz]['aid_note_moyenne']=$aid_note_moyenne;
											$tab_ele['aid_e'][$zz]['aid_note_max']=$aid_note_max;
											$tab_ele['aid_e'][$zz]['aid_note_min']=$aid_note_min;
											$tab_ele['aid_e'][$zz]['place_eleve']=$place_eleve;
										}


										if ($type_note == 'no') {
											$tab_ele['aid_e'][$zz]['aid_note']='-';
											$tab_ele['aid_e'][$zz]['aid_statut']='';
											$tab_ele['aid_e'][$zz]['aid_note_moyenne']='-';
											$tab_ele['aid_e'][$zz]['aid_note_max']='-';
											$tab_ele['aid_e'][$zz]['aid_note_min']='-';
											//$tab_ele['aid_e'][$zz]['place_eleve']=$place_eleve;
										}

										$tab_ele['aid_e'][$zz]['aid_appreciation']=$current_eleve_aid_appreciation;

										//echo "\$tab_ele['aid_e'][$z]['aid_appreciation']=".$tab_ele['aid_e'][$z]['aid_appreciation']."<br />";

										// Vaut-il mieux un tableau $tab_ele['aid_b']
										// ou calquer sur $tab_bulletin[$id_classe][$periode_num]['groupe']
										// La deuxième solution réduirait sans doute le nombre de requêtes

										$zz++;
									}
								}
								$z++;
							}

							//==============================
							if($mode_bulletin=="html") {
								$motif="Temoin_eleve".$id_classe."_".$periode_num."_".$i;
								decompte_debug($motif,"$motif élève $i (".$current_eleve_login[$i].") après AID");
								flush();
							}
							//==============================
						}

						//========================




						//==========================================
						// ABSENCES
						//On vérifie si le module est activé
						if (getSettingValue("active_module_absence")!='2' || getSettingValue("abs2_import_manuel_bulletin")=='y') {
							$sql="SELECT * FROM absences WHERE (login='".$current_eleve_login[$i]."' AND periode='$periode_num');";
							$current_eleve_absences_query = mysqli_query($GLOBALS["mysqli"], $sql);
							$current_eleve_absences = @old_mysql_result($current_eleve_absences_query, 0, "nb_absences");
							$current_eleve_nj = @old_mysql_result($current_eleve_absences_query, 0, "non_justifie");
							$current_eleve_retards = @old_mysql_result($current_eleve_absences_query, 0, "nb_retards");
							$current_eleve_appreciation_absences = @old_mysql_result($current_eleve_absences_query, 0, "appreciation");
						} else {
							// Initialisations files
							require_once("../lib/initialisationsPropel.inc.php");
							$eleve = EleveQuery::create()->findOneByLogin($current_eleve_login[$i]);
							if ($eleve != null) {
								$current_eleve_absences = strval($eleve->getDemiJourneesAbsenceParPeriode($periode_num)->count());
								$current_eleve_nj = strval($eleve->getDemiJourneesNonJustifieesAbsenceParPeriode($periode_num)->count());
								$current_eleve_retards = strval($eleve->getRetardsParPeriode($periode_num)->count());
								$sql="SELECT * FROM absences WHERE (login='".$current_eleve_login[$i]."' AND periode='$periode_num');";
								//echo "$sql< br />";
								$current_eleve_absences_query = mysqli_query($GLOBALS["mysqli"], $sql);
								$current_eleve_appreciation_absences = @old_mysql_result($current_eleve_absences_query, 0, "appreciation");
							}
						}
						if ($current_eleve_absences === '') { $current_eleve_absences = "?"; }
						if ($current_eleve_nj === '') { $current_eleve_nj = "?"; }
						if ($current_eleve_retards==='') { $current_eleve_retards = "?"; }

						$tab_ele['eleve_absences']=$current_eleve_absences;
						$tab_ele['eleve_nj']=$current_eleve_nj;
						$tab_ele['eleve_retards']=$current_eleve_retards;
						$tab_ele['appreciation_absences']=$current_eleve_appreciation_absences;

						// Indice non encore exploité dans les paramètres d'impression des bulletins, ni dans bull_func.lib.php
						if((is_numeric($current_eleve_absences))&&(is_numeric($current_eleve_nj))) {
							$tab_ele['eleve_justif']=$current_eleve_absences-$current_eleve_nj;
						}
						else {
							$tab_ele['eleve_justif']="?";
						} 

						$sql="SELECT u.login login,u.civilite FROM utilisateurs u,
													j_eleves_cpe j
												WHERE (u.login=j.cpe_login AND
													j.e_login='".$current_eleve_login[$i]."');";
						$query = mysqli_query($GLOBALS["mysqli"], $sql);
						$current_eleve_cperesp_login = @old_mysql_result($query, 0, "login");
						$tab_ele['cperesp_login']=$current_eleve_cperesp_login;
						$current_eleve_cperesp_civilite = @old_mysql_result($query, 0, "civilite");
						$tab_ele['cperesp_civilite']=$current_eleve_cperesp_civilite;
						//==========================================


						// Boucle groupes de la classe $id_classe pour la période $periode_num
						for($j=0;$j<count($current_group);$j++) {
							//============================================
							// A REVOIR: On fait cette requête autant de fois qu'il y a d'élève... l'extraire de la boucle élèves en faisant une autre boucle sur $current_group
							// Nombre total de devoirs:
							$sql="SELECT cd.id FROM cn_devoirs cd, cn_cahier_notes ccn WHERE cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group[$j]["id"]."' AND ccn.periode='$periode_num';";
							//echo "\n<!--sql=$sql-->\n";
							$result_nbct_tot=mysqli_query($GLOBALS["mysqli"], $sql);
							if($result_nbct_tot) {
								$current_matiere_nbct=mysqli_num_rows($result_nbct_tot);
							}
							else {
								$current_matiere_nbct=0;
							}

							$tab_bulletin[$id_classe][$periode_num]['groupe'][$j]['nbct']=$current_matiere_nbct;

							//============================================

							// Si l'élève suit l'option, sa note est affectée (éventuellement vide)
							if(isset($current_eleve_note[$j][$i])) {
								// Quartiles
								/*
								if($current_eleve_note[$j][$i]!="") {
									if($current_eleve_note[$j][$i]>=15) {
										$tab_bulletin[$id_classe][$periode_num]['quartile1_grp'][$j]++;
										$tab_bulletin[$id_classe][$periode_num]['place_eleve'][$j][$i]=1;
									}
									elseif(($current_eleve_note[$j][$i]>=12)&&($current_eleve_note[$j][$i]<15)) {
										$tab_bulletin[$id_classe][$periode_num]['quartile2_grp'][$j]++;
										$tab_bulletin[$id_classe][$periode_num]['place_eleve'][$j][$i]=2;
									}
									elseif(($current_eleve_note[$j][$i]>=10)&&($current_eleve_note[$j][$i]<12)) {
										$tab_bulletin[$id_classe][$periode_num]['quartile3_grp'][$j]++;
										$tab_bulletin[$id_classe][$periode_num]['place_eleve'][$j][$i]=3;
									}
									elseif(($current_eleve_note[$j][$i]>=8)&&($current_eleve_note[$j][$i]<10)) {
										$tab_bulletin[$id_classe][$periode_num]['quartile4_grp'][$j]++;
										$tab_bulletin[$id_classe][$periode_num]['place_eleve'][$j][$i]=4;
									}
									elseif(($current_eleve_note[$j][$i]>=5)&&($current_eleve_note[$j][$i]<8)) {
										$tab_bulletin[$id_classe][$periode_num]['quartile5_grp'][$j]++;
										$tab_bulletin[$id_classe][$periode_num]['place_eleve'][$j][$i]=5;
									}
									else {
										$tab_bulletin[$id_classe][$periode_num]['quartile6_grp'][$j]++;
										$tab_bulletin[$id_classe][$periode_num]['place_eleve'][$j][$i]=6;
									}
								}
								*/

								//================================
								// Notes des boites/conteneurs/sous-matieres
								unset($cn_note);
								unset($cn_nom);
								unset($cn_id);

								// On teste si des notes de une ou plusieurs boites du carnet de notes doivent être affichée
								$sql="select distinct c.nom_court, c.id, nc.note from cn_cahier_notes cn, cn_conteneurs c, cn_notes_conteneurs nc
								where (
								cn.periode = '".$periode_num."' and
								cn.id_groupe='".$current_group[$j]["id"]."' and
								cn.id_cahier_notes = c.id_racine and
								c.id_racine!=c.id and
								nc.id_conteneur = c.id and
								nc.statut ='y' and
								nc.login='".$current_eleve_login[$i]."' and
								c.display_bulletin = 1
								) ";
								//echo "$sql<br />";
								$test_cn = mysqli_query($GLOBALS["mysqli"], $sql);
								$nb_ligne_cn = mysqli_num_rows($test_cn);
								$n = 0;
								while ($n < $nb_ligne_cn) {
									$cn_id[$n] = old_mysql_result($test_cn, $n, 'c.id');
									$cn_nom[$n] = old_mysql_result($test_cn, $n, 'c.nom_court');
									$cn_note[$n] = @old_mysql_result($test_cn, $n ,'nc.note');
									$cn_note[$n] = number_format($cn_note[$n],1, ',', ' ');
									$n++;
								}
								$nb_ligne_par_matiere = max($nb_ligne_cn,1) ;

								$tab_bulletin[$id_classe][$periode_num]['groupe'][$j][$i]['nb_ligne_par_matiere']=$nb_ligne_par_matiere;

								if(isset($cn_note)) {
									$tab_bulletin[$id_classe][$periode_num]['groupe'][$j][$i]['cn_note']=$cn_note;
									$tab_bulletin[$id_classe][$periode_num]['groupe'][$j][$i]['cn_id']=$cn_id;
									$tab_bulletin[$id_classe][$periode_num]['groupe'][$j][$i]['cn_nom']=$cn_nom;
								}

								//================================
								// 20130520 : Faire le calcul de moyenne annuelle là?
								if((isset($moyennes_annee))&&($moyennes_annee=='y')) {
									$sql="SELECT round(avg(note),1) as moy_annee from matieres_notes where login='".$current_eleve_login[$i]."' and statut='' and id_groupe='".$current_group[$j]['id']."';";
									//echo "$sql<br />";
									$res_annee=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_annee)>0) {
										$tab_bulletin[$id_classe][$periode_num]['moy_annee'][$j][$i]=old_mysql_result($res_annee, 0, "moy_annee");
									}
								}
								/*
								// Ou recherche d'indices?
								if(isset($tab_bull['login_prec'])) {

									//for($loop_p=1;$loop_p<count($tab_bull['login_prec']);$loop_p++) {
									foreach($tab_bull['login_prec'] as $key => $value) {
										// Il faut récupérer l'id_groupe et l'indice de l'élève... dans les tableaux récupérés de calcul_moy_gen.inc.php
										// Tableaux d'indices [$j][$i] (groupe, élève)
										//		$tab_bull['note_prec'][$loop_p]=$current_eleve_note;
										//		$tab_bull['statut_prec'][$loop_p]=$current_eleve_statut;
										$indice_eleve=-1;
										//for($loop_l=0;$loop_l<count($tab_bull['login_prec'][$loop_p]);$loop_l++) {
										for($loop_l=0;$loop_l<count($tab_bull['login_prec'][$key]);$loop_l++) {
											//echo "\$tab_bull['login_prec'][$key][$loop_l]=".$tab_bull['login_prec'][$key][$loop_l]." et \$tab_bull['eleve'][$i]['login']=".$tab_bull['eleve'][$i]['login']."<br />\n";
											if($tab_bull['login_prec'][$key][$loop_l]==$tab_bull['eleve'][$i]['login']) {$indice_eleve=$loop_l;break;}
										}

								*/
								//================================

								//================================
								// Récup appréciation
								$sql="SELECT appreciation FROM matieres_appreciations WHERE id_groupe='".$current_group[$j]['id']."' AND periode='$periode_num' AND login='".$current_eleve_login[$i]."';";
								$res_app=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_app)>0) {
									$lig_app=mysqli_fetch_object($res_app);
									$current_eleve_app[$j][$i]=$lig_app->appreciation;
									if($current_eleve_app[$j][$i]=="") {$current_eleve_app[$j][$i]="-";}
								}
								else {
									$current_eleve_app[$j][$i]="-";
								}
								//================================

								// Nombre de contrôles
								$sql="SELECT cnd.note, cd.note_sur FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE cnd.login='".$current_eleve_login[$i]."' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group[$j]["id"]."' AND ccn.periode='$periode_num' AND cnd.statut='';";
								//echo "\n<!--sql=$sql-->\n";
								$result_nbct=mysqli_query($GLOBALS["mysqli"], $sql);
								if($result_nbct) {
									$current_eleve_nbct=mysqli_num_rows($result_nbct);
								}
								else {
									$current_eleve_nbct=0;
								}

								$tab_bulletin[$id_classe][$periode_num]['nbct'][$j][$i]=$current_eleve_nbct;

								if((isset($place_eleve_classe[$i]))&&($place_eleve_classe[$i]!="")) {
									$tab_bulletin[$id_classe][$periode_num]['nbct'][$j][$i]=$current_eleve_nbct;
								}

								//++++++++++++++++++++++++
								// Modif d'après F.Boisson
								// Pour remplacer la chaine de caractères @@Notes par une insertion des notes dans l'appréciation du bulletin.
								// on prend les notes dans $string_notes
								$string_notes='';
								if ($result_nbct ) {
									while ($snnote =  mysqli_fetch_assoc($result_nbct)) {
										if ($string_notes != '') $string_notes .= ", ";
										$string_notes .= $snnote['note'];
										if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $snnote['note_sur']!=getSettingValue("referentiel_note")) {
											$string_notes .= "/".$snnote['note_sur'];
										}
									}
								}
								$current_eleve_app[$j][$i]=str_replace('@@Notes', $string_notes,$current_eleve_app[$j][$i]);
								//++++++++++++++++++++++++

								//$tab_bulletin[$id_classe][$periode_num]['groupe'][$j][$i]['app']=$current_eleve_app[$j][$i];

								// Structure plus proche des autres données:
								$tab_bulletin[$id_classe][$periode_num]['app'][$j][$i]=$current_eleve_app[$j][$i];

							}
						}

						// Avis du conseil de classe
						$sql="SELECT * FROM avis_conseil_classe WHERE login='".$current_eleve_login[$i]."' AND periode='$periode_num';";
						$res_avis=mysqli_query($GLOBALS["mysqli"], $sql);
						//echo "$sql<br />";
						if(mysqli_num_rows($res_avis)>0) {
							$lig_avis=mysqli_fetch_object($res_avis);
							$tab_bulletin[$id_classe][$periode_num]['avis'][$i]=$lig_avis->avis;
							$tab_bulletin[$id_classe][$periode_num]['id_mention'][$i]=$lig_avis->id_mention;
							//echo $lig_avis->avis;
						}
						else {
							//$tab_bulletin[$id_classe][$periode_num]['avis'][$i]="-";
							$tab_bulletin[$id_classe][$periode_num]['avis'][$i]="";
							$tab_bulletin[$id_classe][$periode_num]['id_mention'][$i]="-";
						}

						// On affecte la partie élève $tab_ele dans $tab_bulletin
						$tab_bulletin[$id_classe][$periode_num]['eleve'][$i]=$tab_ele;

						if(!in_array($current_eleve_login[$i],$tableau_eleve['login'])) {
							$tableau_eleve['login'][]=$current_eleve_login[$i];
							$tableau_eleve['no_gep'][]=$tab_ele['no_gep'];
							$tableau_eleve['nom_prenom'][]=remplace_accents($tab_ele['nom']."_".$tab_ele['prenom'],'all');
						}

						$nb_bulletins_edites++;
					}
				}
			}
		}
	}

	//echo "\$nb_bulletins_edites=$nb_bulletins_edites<br />";

	//========================================================================
	// A CE STADE LE TABLEAU $tab_bulletin EST RENSEIGNé
	// PLUS AUCUNE REQUETE NE DEVRAIT ETRE NECESSAIRE
	// OU ALORS IL FAUDRAIT LES EFFECTUER AU-DESSUS ET COMPLETER $tab_bulletin
	//
	// IL Y AURA A RENSEIGNER $tab_bulletin[$id_classe][$periode_num]['modele_pdf']
	// SI ON FAIT UNE IMPRESSION DE BULLETIN PDF, POUR NE PAS REFAIRE LES REQUETES
	// POUR CHAQUE ELEVE.
	//========================================================================

	if($mode_bulletin=="html") {
		echo "<script type='text/javascript'>
	document.getElementById('td_info').innerHTML='Affichage';
</script>\n";
	}

	// 20120419
	if($generer_fichiers_pdf_archivage=='y') {

		//**************** EN-TETE *********************
		$titre_page = "Archivage des bulletins PDF";
		require_once("../lib/header.inc.php");
		//**************** FIN EN-TETE *****************

		if(count($tab_id_classe)==1) {
			$id_classe=$tab_id_classe[0];
			$classe=get_class_from_id($id_classe);
			echo "<p>Classe de ".$classe."</p>\n";
		}

		echo "<p>Patience...</p>\n";
		flush();

		//$dirname = "../backup/".getSettingValue("backup_directory")."/bulletins";
		$dirname = "../temp/".get_user_temp_directory()."/".getPref($_SESSION['login'], 'dossier_archivage_pdf', 'bulletins_pdf_individuels_eleves_'.strftime('%Y%m%d'));
		@mkdir($dirname);
		if(!file_exists($dirname)) {
			echo "<p>ERREUR d'acces au dossier des bulletins: $dirname</p>";
			die();
		}

		$arch_bull_nom_prenom=getPref($_SESSION['login'], 'arch_bull_nom_prenom', 'yes');
		$arch_bull_INE=getPref($_SESSION['login'], 'arch_bull_INE', 'yes');
		$arch_bull_annee_scolaire=getPref($_SESSION['login'], 'arch_bull_annee_scolaire', 'yes');
		$arch_bull_date_edition=getPref($_SESSION['login'], 'arch_bull_date_edition', 'yes');
		$arch_bull_classe=getPref($_SESSION['login'], 'arch_bull_classe', 'yes');

		for($j=0;$j<count($tableau_eleve['login']);$j++) {
			//send_file_download_headers('application/pdf','bulletin.pdf');

			//$nom_fichier_bulletin = 'bulletin_'.$tableau_eleve['no_gep'][$j]."_".$tableau_eleve['nom_prenom'][$j]."_".$nom_bulletin.'.pdf';
			//$nom_fichier_bulletin = 'bulletin_'.$tableau_eleve['no_gep'][$j]."_".$tableau_eleve['nom_prenom'][$j]."_".strftime("%Y%m%d").'.pdf';
			//$nom_fichier_bulletin = 'bulletin_'.$tableau_eleve['nom_prenom'][$j]."_".$tableau_eleve['no_gep'][$j]."_annee_scolaire_".remplace_accents(getSettingValue('gepiYear'),"all")."_".strftime("%Y%m%d").'.pdf';

			$nom_fichier_bulletin='bulletin';
			if($arch_bull_nom_prenom=='yes') {$nom_fichier_bulletin.='_'.$tableau_eleve['nom_prenom'][$j];}
			if($arch_bull_INE=='yes') {$nom_fichier_bulletin.='_'.$tableau_eleve['no_gep'][$j];}
			if($arch_bull_annee_scolaire=='yes') {$nom_fichier_bulletin.="_annee_scolaire_".remplace_accents(getSettingValue('gepiYear'),"all");}
			if($arch_bull_date_edition=='yes') {$nom_fichier_bulletin.="_".strftime("%Y%m%d");}
			if($arch_bull_classe=='yes') {
				if(isset($_POST['ele_chgt_classe'])) {
					$tab_tmp_classe=get_class_from_ele_login($tableau_eleve['login']);
					if(isset($tab_tmp_classe['liste'])) {
						$nom_fichier_bulletin.="_".remplace_accents($tab_tmp_classe['liste'], 'all');
					}
				}
				elseif(isset($classe)) {
					$nom_fichier_bulletin.="_".$classe;
				}
			}
			$nom_fichier_bulletin.='.pdf';

			//création du PDF en mode Portrait, unitée de mesure en mm, de taille A4
			$pdf=new bul_PDF('p', 'mm', 'A4');
			$nb_eleve_aff = 1;
			$categorie_passe = '';
			$categorie_passe_count = 0;
			$pdf->SetCreator($gepiSchoolName);
			$pdf->SetAuthor($gepiSchoolName);
			$pdf->SetKeywords('');
			$pdf->SetSubject('Bulletin');
			$pdf->SetTitle('Bulletin');
			$pdf->SetDisplayMode('fullwidth', 'single');
			$pdf->SetCompression(TRUE);
			$pdf->SetAutoPageBreak(TRUE, 5);

			$responsable_place = 0;

			// A faire: Forcer 1 seul bulletin par parent

			for($loop_classe=0;$loop_classe<count($tab_id_classe);$loop_classe++) {
				$id_classe=$tab_id_classe[$loop_classe];
				$classe=get_class_from_id($id_classe);

				$sql="INSERT INTO tempo4 SET col1='$id_classe', col2='".$tableau_eleve['login'][$j]."', col3='$nom_fichier_bulletin', col4='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tableau_eleve['nom_prenom'][$j]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
				$res_t4=mysqli_query($GLOBALS["mysqli"], $sql);

				for($loop_periode_num=0;$loop_periode_num<count($tab_periode_num);$loop_periode_num++) {
					$periode_num=$tab_periode_num[$loop_periode_num];

					for($i=0;$i<$tab_bulletin[$id_classe][$periode_num]['eff_classe'];$i++) {

						if(isset($tab_bulletin[$id_classe][$periode_num]['selection_eleves'])) {
							if((isset($tab_bulletin[$id_classe][$periode_num]['eleve'][$i]['login']))&&($tab_bulletin[$id_classe][$periode_num]['eleve'][$i]['login']==$tableau_eleve['login'][$j])) {
								bulletin_pdf($tab_bulletin[$id_classe][$periode_num],$i,$tab_releve[$id_classe][$periode_num]);
							}
						}
					}
				}
			}

			echo $pdf->Output($dirname."/".$nom_fichier_bulletin,'F');
			echo "<p><a href='$dirname/$nom_fichier_bulletin'>$nom_fichier_bulletin</a></p>\n";

			flush();
		}

		$archivage_fichiers_bull_pdf_auto=isset($_POST['archivage_fichiers_bull_pdf_auto']) ? $_POST['archivage_fichiers_bull_pdf_auto'] : (isset($_GET['archivage_fichiers_bull_pdf_auto']) ? $_GET['archivage_fichiers_bull_pdf_auto'] : "n");

		if(isset($_POST['ele_chgt_classe'])) {
			//get_nom_prenom_eleve($tab_restriction_ele[0])
			echo "<p>Bulletins de ".$tableau_eleve['nom_prenom'][0]." générés.<br />";
			echo "<a href='../mod_annees_anterieures/archivage_bull_pdf.php?id_classe=$id_classe&amp;ele_chgt_classe=y&amp;generer_fichiers_pdf_archivage=y&amp;archivage_fichiers_bull_pdf_auto=$archivage_fichiers_bull_pdf_auto".add_token_in_url()."'>Suite</a>";

			if($archivage_fichiers_bull_pdf_auto=='y') {
				echo "<script type='text/javascript'>
	function archivage_suite() {
		document.location='../mod_annees_anterieures/archivage_bull_pdf.php?id_classe=$id_classe&ele_chgt_classe=y&generer_fichiers_pdf_archivage=y&archivage_fichiers_bull_pdf_auto=$archivage_fichiers_bull_pdf_auto".add_token_in_url(false)."';
	}
	setTimeout('archivage_suite()',2000);
</script>\n";
			}
		}
		else {
			$sql="SELECT col2 AS login FROM tempo2 WHERE col1='$id_classe' ORDER BY col2 LIMIT $arch_bull_eff_tranche;";
			$res_ele_classe=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_ele_classe)>0) {
				echo "Classe de $classe en partie traitée (<em>il reste ".mysqli_num_rows($res_ele_classe)." élève(s)</em>).<br />";
			}
			else {
				echo "Classe de $classe traitée.<br />";
			}
			echo "<a href='../mod_annees_anterieures/archivage_bull_pdf.php?id_classe=$id_classe&amp;generer_fichiers_pdf_archivage=y&amp;archivage_fichiers_bull_pdf_auto=$archivage_fichiers_bull_pdf_auto".add_token_in_url()."'>Suite</a>";

			if($archivage_fichiers_bull_pdf_auto=='y') {
				echo "<script type='text/javascript'>
	function archivage_suite() {
		document.location='../mod_annees_anterieures/archivage_bull_pdf.php?id_classe=$id_classe&generer_fichiers_pdf_archivage=y&archivage_fichiers_bull_pdf_auto=$archivage_fichiers_bull_pdf_auto".add_token_in_url(false)."';
	}
	setTimeout('archivage_suite()',2000);
</script>\n";
			}
		}

		require("../lib/footer.inc.php");
		die();
	}

	/*
	if($mode_bulletin=="html") {
		// 20120716
		// Si une image de signature doit être insérée...
		$bull_affiche_img_signature=getSettingValue('bull_affiche_img_signature');
		$url_fich_sign="";

		if($bull_affiche_img_signature=='y') {
			$tmp_fich=getSettingValue('fichier_signature');
			$fich_sign = '../backup/'.getSettingValue('backup_directory').'/'.$tmp_fich;
			//echo "\$fich_sign=$fich_sign<br />\n";
			if(($tmp_fich!='') and (file_exists($fich_sign))) {
				$sql="SELECT 1=1 FROM droits_acces_fichiers WHERE fichier='signature_img' AND ((identite='".$_SESSION['statut']."' AND type='statut') OR (identite='".$_SESSION['login']."' AND type='individu'))";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0) {
					// Si un .htaccess est en place dans backup, on n'atteind pas l'image sans fournir compte/mdp
					$url_fich_sign="../temp/".get_user_temp_directory()."/".$tmp_fich;
					copy($fich_sign, $url_fich_sign);
					// La copie sera supprimée à la déconnexion
				}
			}
		}
	}
	*/

	if($mode_bulletin=="pdf") {
		// définition d'une variable
		$hauteur_pris = 0;

		/*****************************************
		* début de la génération du fichier PDF  *
		* ****************************************/

		/*
		Faire la liste de tous les logins élèves
		Boucler ensuite sur les logins élèves, puis sur les périodes, puis sur les classes
		Retenir ce qui concerne le login courant et générer un fichier à chaque fois
		
		Ou alors remplir autrement le tableau $tab_bulletin
		*/

		//if((!isset($bull_pdf_debug))||($bull_pdf_debug!='y')) {
		// 20120418
		if(((!isset($bull_pdf_debug))||($bull_pdf_debug!='y'))&&($generer_fichiers_pdf_archivage!='y')) {
			send_file_download_headers('application/pdf','bulletin.pdf');
		}
		//création du PDF en mode Portrait, unitée de mesure en mm, de taille A4
		$pdf=new bul_PDF('p', 'mm', 'A4');
		$nb_eleve_aff = 1;
		$categorie_passe = '';
		$categorie_passe_count = 0;
		$pdf->SetCreator($gepiSchoolName);
		$pdf->SetAuthor($gepiSchoolName);
		$pdf->SetKeywords('');
		$pdf->SetSubject('Bulletin');
		$pdf->SetTitle('Bulletin');
		$pdf->SetDisplayMode('fullwidth', 'single');
		$pdf->SetCompression(TRUE);
		$pdf->SetAutoPageBreak(TRUE, 5);

		$responsable_place = 0;
	}

	// Compteur pour insérer un saut dans les bulletins HTML
	$compteur_bulletins=0;
	for($loop_classe=0;$loop_classe<count($tab_id_classe);$loop_classe++) {
		$id_classe=$tab_id_classe[$loop_classe];
		$classe=get_class_from_id($id_classe);

		if($mode_bulletin=="html") {
			echo "<script type='text/javascript'>
	document.getElementById('td_classe').innerHTML='".$classe."';
</script>\n";
		}

		for($loop_periode_num=0;$loop_periode_num<count($tab_periode_num);$loop_periode_num++) {

			$periode_num=$tab_periode_num[$loop_periode_num];

			//==============================
			if($mode_bulletin=="html") {
				$motif="Classe_".$id_classe."_".$periode_num;
				decompte_debug($motif,"$motif avant");
				flush();
				echo "<script type='text/javascript'>
	document.getElementById('td_periode').innerHTML='".$periode_num."';
</script>\n";
			}
			//==============================

			if($mode_bulletin=="html") {
				echo "<div class='noprint' style='background-color:white; border: 1px solid red;'>\n";
				echo "<h2>Classe de ".$classe."</h2>\n";
				echo "<p><b>Période $periode_num</b></p>\n";

				echo "<p>Effectif de la classe: ".$tab_bulletin[$id_classe][$periode_num]['eff_classe']."</p>\n";
				echo "</div>\n";
			}

			//======================================
			// Pour le tri par établissement d'origine
			unset($tmp_tab);
			unset($rg);
			//$tri_par_etab_orig="y";
			if($tri_par_etab_orig=='y') {
				//echo "count(\$tab_bulletin[$id_classe][$periode_num]['eleve'])=".count($tab_bulletin[$id_classe][$periode_num]['eleve'])."<br />\n";
				//echo "count(\$tab_bulletin[$id_classe][$periode_num]['eff_classe'])=".count($tab_bulletin[$id_classe][$periode_num]['eff_classe'])."<br />\n";
				//for($k=0;$k<count($tab_bulletin[$id_classe][$periode_num]['eleve']);$k++) {
				for($k=0;$k<$tab_bulletin[$id_classe][$periode_num]['eff_classe'];$k++) {
					$rg[$k]=$k;
					//echo "\$tab_bulletin[$id_classe][$periode_num]['eleve'][$k]['nom']=".$tab_bulletin[$id_classe][$periode_num]['eleve'][$k]['nom']."<br />\n";
					//echo "\$tab_bulletin[$id_classe][$periode_num]['eleve'][$k]['etab_id']=".$tab_bulletin[$id_classe][$periode_num]['eleve'][$k]['etab_id']."<br />\n";
					if(!isset($tab_bulletin[$id_classe][$periode_num]['eleve'][$k])) {
						$tmp_tab[$k]="";
					}
					else {
						$tmp_tab[$k]=$tab_bulletin[$id_classe][$periode_num]['eleve'][$k]['etab_id'];
					}
				}
				array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);
			}
			//======================================

			//$compteur=0;
			//for($i=0;$i<count($tab_bulletin[$id_classe][$periode_num]['eleve']);$i++) {
			for($i=0;$i<$tab_bulletin[$id_classe][$periode_num]['eff_classe'];$i++) {
				if($tri_par_etab_orig=='n') {$rg[$i]=$i;}

				if(isset($tab_bulletin[$id_classe][$periode_num]['selection_eleves'])) {
					//if(isset($tab_bulletin[$id_classe][$periode_num]['eleve'][$i]['login'])) {
					if((isset($rg[$i]))&&(isset($tab_bulletin[$id_classe][$periode_num]['eleve'][$rg[$i]]['login']))) {
					//if((isset($tab_bulletin[$id_classe][$periode_num]['eleve'][$rg[$i]]))&&(isset($tab_bulletin[$id_classe][$periode_num]['eleve'][$rg[$i]]['login']))) {

						//if (in_array($tab_bulletin[$id_classe][$periode_num]['eleve'][$i]['login'],$tab_bulletin[$id_classe][$periode_num]['selection_eleves'])) {
						if (in_array($tab_bulletin[$id_classe][$periode_num]['eleve'][$rg[$i]]['login'],$tab_bulletin[$id_classe][$periode_num]['selection_eleves'])) {

							// ++++++++++++++++++++++++++++++++++++++
							// ++++++++++++++++++++++++++++++++++++++
							// AJOUTER UN TEST: L'élève fait-il bien partie de la classe?
							//                  Inutile: la liste $current_eleve_login est obtenue de calcul_moy_gen.inc.php
							//                  Pas d'injection/intervention possible.
							//                  Le test sur l'accès à la classe plus haut (*) doit suffire.
							//                  On pourrait injecter un login dans la sélection d'élève, mais pas dans $current_eleve_login
							//                  Et on test seulement si $current_eleve_login[$i] est bien dans la sélection (pas le contraire)
							//                  (*) dans cette section tout de même.
							// ++++++++++++++++++++++++++++++++++++++
							// ++++++++++++++++++++++++++++++++++++++

							if($mode_bulletin=="html") {
								echo "<script type='text/javascript'>
	document.getElementById('td_ele').innerHTML='".$tab_bulletin[$id_classe][$periode_num]['eleve'][$rg[$i]]['login']."';
</script>\n";
							}


							if($mode_bulletin=="html") {
								//$motif="Bulletin_eleve".$id_classe."_".$periode_num."_".$i;
								//decompte_debug($motif,"$motif élève $i avant");
								$motif="Bulletin_eleve".$id_classe."_".$periode_num."_".$rg[$i];
								decompte_debug($motif,"$motif élève $rg[$i] avant");
								flush();

								// Saut de page si jamais ce n'est pas le premier bulletin
								if($compteur_bulletins>0) {echo "<p class='saut'>&nbsp;</p>\n";}

								// Génération du bulletin de l'élève
								//bulletin_html($tab_bulletin[$id_classe][$periode_num],$i);
								//bulletin_html($tab_bulletin[$id_classe][$periode_num],$i,$tab_releve[$id_classe][$periode_num]);
								bulletin_html($tab_bulletin[$id_classe][$periode_num],$rg[$i],$tab_releve[$id_classe][$periode_num]);

								//$motif="Bulletin_eleve".$id_classe."_".$periode_num."_".$i;
								//decompte_debug($motif,"$motif élève $i après");
								$motif="Bulletin_eleve".$id_classe."_".$periode_num."_".$rg[$i];
								decompte_debug($motif,"$motif élève $rg[$i] après");
								flush();
							}
							else {
								//bulletin_pdf($tab_bulletin[$id_classe][$periode_num],$i,$tab_releve[$id_classe][$periode_num]);
								bulletin_pdf($tab_bulletin[$id_classe][$periode_num],$rg[$i],$tab_releve[$id_classe][$periode_num]);
							}

/*
echo "Tableau de la classe $id_classe en période $periode_num<br />
<pre>";
print_r($tab_bulletin[$id_classe][$periode_num]);
echo "</pre>";

echo "Tableau du modèle PDF<br />
<pre>";
print_r($tab_modele_pdf);
echo "</pre>";
*/

							//==============================================================================================
							// PAR LA SUITE, ON POURRA INSERER ICI, SI L'OPTION EST COCHEE, LE RELEVE DE NOTES DE LA PERIODE
							//==============================================================================================

							if($mode_bulletin=="html") {
								echo "<div class='espacement_bulletins'><div align='center'>Espacement (non imprimé) entre les bulletins</div></div>\n";
							}

							$compteur_bulletins++;

							if($mode_bulletin=="html") {
								flush();
							}
						}
					}
				}
			}

			//==============================
			if($mode_bulletin=="html") {
				$motif="Classe_".$id_classe."_".$periode_num;
				decompte_debug($motif,"$motif après");
				flush();
			}
			//==============================

		}
	}

	//echo "\$compteur_bulletins=$compteur_bulletins<br />";

	if($mode_bulletin=="html") {
		if($compteur_bulletins==0) {
			echo "<h1 style='color:red'>Anomalie</h1>\n";
			echo "<p>Aucun bulletin ne semble avoir été édité.<br />C'est un problème qui peut apparaître si vous avez demandé à afficher les catégories de matières alors que les catégories sont mal paramétrées.<br />Effectuez un Nettoyage des tables&nbsp;: ";
			if($_SESSION['statut']=='administrateur') {
				echo "<a href='../utilitaires/clean_tables.php?maj=controle_categories_matieres".add_token_in_url()."'>Gestion générale/Nettoyage des tables/Vérifier les catégories de matières</a>";
			}
			else {
				echo "Gestion générale/Nettoyage des tables/Vérifier les catégories de matières";
			}
			echo ".<br />Et contrôlez par ailleurs l'ordre des catégories dans ";
			if($_SESSION['statut']=='administrateur') {
				echo "<a href='../classes/index.php'>Gestion des bases/Gestion des classes/&ltTelle_classe;&gt; Paramètres</a>";
			}
			else {
				echo "Gestion des bases/Gestion des classes/&lt;Telle_classe&gt; Paramètres";
			}
			echo "<br />(<em>deux catégories ne doivent pas avoir le même rang</em>).</p>\n";
		}

		echo "<script type='text/javascript'>
	document.getElementById('infodiv').style.display='none';

    var aElm=document.body.getElementsByTagName('*');
    for(var i=0; i<aElm.length; i++) {
        if(aElm[i].className=='espacement_bulletins') {
            //do something
            //aElm[i].style.color='lime';
            aElm[i].style.display='none';
        }
    }

</script>\n";

	}
}

//==============================
if($mode_bulletin=="html") {
	$motif="Duree_totale";
	//decompte_debug($motif,"$motif après");
	decompte_debug($motif,"$motif");
	flush();
}
//==============================

if((!isset($mode_bulletin))||($mode_bulletin!="pdf")) {
	echo "<div id='remarques_bas_de_page' style='display:none;'>
<p><br /></p>
<p>A REVOIR:</p>
<ul>
<li>Les bulletins HTML utilisent les infos display_rang, display_coef,... de la table 'classes'.<br />
Les bulletins PDF utilisent plutôt les infos de la table 'modele_bulletin' il me semble.<br />
Il faudrait peut-être revoir le dispositif pour adopter la même stratégie.<br />
On a aussi ajouté des champs dans la table 'classes' pour les relevés de notes,... faut-il envisager une autre structure?</li>
</ul>
</div>\n";

	require("../lib/footer.inc.php");
}
elseif((isset($mode_bulletin))&&($mode_bulletin=="pdf")) {

	if($compteur_bulletins==0) {
		$pdf->AddPage(); //ajout d'une page au document
		$pdf->SetFont('DejaVu');
		$pdf->SetXY(20,20);
		$pdf->SetFontSize(14);
		$pdf->Cell(90,7, "Anomalie",0,2,'');

		$pdf->SetXY(20,40);
		$pdf->SetFontSize(10);
		$pdf->Cell(150,7, "Aucun bulletin ne semble avoir été édité.",0,2,'');
		$pdf->SetXY(20,45);
		$pdf->Cell(150,7, "C'est un problème qui peut apparaître si vous avez demandé à afficher les catégories de matières",0,2,'');
		$pdf->SetXY(20,50);
		$pdf->Cell(150,7, "alors que les catégories sont mal paramétrées.",0,2,'');

		$pdf->SetXY(20,60);
		$pdf->Cell(150,7, "Effectuez un Nettoyage des tables :",0,2,'');
		$pdf->SetXY(20,65);
		$pdf->Cell(150,7, "      Gestion générale/Nettoyage des tables/Vérifier les catégories de matières",0,2,'');

		$pdf->SetXY(20,75);
		$pdf->Cell(150,7, "Et contrôlez par ailleurs l'ordre des catégories dans ",0,2,'');
		$pdf->SetXY(20,80);
		$pdf->Cell(150,7, "      Gestion des bases/Gestion des classes/<Telle_classe> Paramètres",0,2,'');
		$pdf->SetXY(20,85);
		$pdf->Cell(150,7, "(deux catégories ne doivent pas avoir le même rang)",0,2,'');

		$pdf->SetXY(20,95);
		$pdf->Cell(150,7, "Vous pouvez définir un ordre des catégories pour toutes les classes via",0,2,'');
		$pdf->SetXY(20,100);
		$pdf->Cell(150,7, "      Gestion des classes/Paramétrage des classes par lots",0,2,'');
	}

	//fermeture du fichier pdf et lecture dans le navigateur 'nom', 'I/D'
	$nom_bulletin = 'bulletin_'.$nom_bulletin.'.pdf';

	//echo "\$bull_pdf_debug=$bull_pdf_debug<br />\n";
	if((isset($bull_pdf_debug))&&($bull_pdf_debug=='y')) {
		echo $pdf->Output($nom_bulletin,'S');
		die();
	}
	else {
		$pref_output_mode_pdf=get_output_mode_pdf();
		$pdf->Output($nom_bulletin,$pref_output_mode_pdf);
	}
}

?>
