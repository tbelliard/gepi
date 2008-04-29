<?php
/*
*
* $Id$
*
* Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau, Christian Chapel
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


$sql="SELECT 1=1 FROM droits WHERE id='/bulletin/bull_index.php';";
$res_test=mysql_query($sql);
if (mysql_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/bulletin/bull_index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1');";
	$res_insert=mysql_query($sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//================================

$intercaler_releve_notes=isset($_POST['intercaler_releve_notes']) ? $_POST['intercaler_releve_notes'] : NULL;

//====================================================
//=============== ENTETE STANDARD ====================
if (!isset($_POST['valide_select_eleves'])) {
	//**************** EN-TETE *********************
	$titre_page = "Edition des bulletins";
	require_once("../lib/header.inc");
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

//echo "microtime()=".microtime()."<br />";
//echo "time()=".time()."<br />";

$debug="n";
$tab_instant=array();
include("bull_func.lib.php");

//==============================
$motif="Duree_totale";
//decompte_debug($motif,"Témoin de $motif initialisation");
decompte_debug($motif,"");
flush();
//==============================


$tab_id_classe=isset($_POST['tab_id_classe']) ? $_POST['tab_id_classe'] : NULL;
$tab_periode_num=isset($_POST['tab_periode_num']) ? $_POST['tab_periode_num'] : NULL;
$choix_periode_num=isset($_POST['choix_periode_num']) ? $_POST['choix_periode_num'] : NULL;

//======================================================
//==================CHOIX DES CLASSES===================
if(!isset($tab_id_classe)) {
	echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	if(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='administrateur')) {
		echo " | <a href='param_bull.php' target='_blank'>Paramètres d'impression des bulletins</a>";
	}
	echo "</p>\n";

	echo "<p class='bold'>Choix des classes:</p>\n";

	if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiProfImprBul")!='yes') {
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
	elseif ($_SESSION["statut"] == "administrateur") {
		// On selectionne toutes les classes
		//$sql="SELECT DISTINCT c.* FROM classes c WHERE 1";
		$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) ORDER BY c.classe;";

		$message_0="Aucune classe avec élève n'a été trouvée.";
	}
	elseif ($_SESSION["statut"] == "professeur") {
		$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs jep, j_eleves_classes jec WHERE (jep.professeur='".$_SESSION['login']."' AND jep.login = jec.login AND jec.id_classe = c.id);";

			$message_0="Aucune classe (<i>avec élève</i>) ne vous est affectée pour l'édition des bulletins.";
	}
	else {
		// On ne devrait pas arriver jusque-là...
		echo "<p>Droits insuffisants pour effectuer cette opération</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$call_classes=mysql_query($sql);

	$nb_classes=mysql_num_rows($call_classes);
	if($nb_classes==0){
		//echo "<p>Aucune classe avec élève affecté n'a été trouvée.</p>\n";
		echo "<p>".$message_0."</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	// Affichage sur 3 colonnes
	$nb_classes_par_colonne=round($nb_classes/3);

	echo "<table width='100%'>\n";
	echo "<tr valign='top' align='center'>\n";

	$cpt = 0;

	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";

	while($lig_clas=mysql_fetch_object($call_classes)) {

		//affichage 2 colonnes
		if(($cpt>0)&&(round($cpt/$nb_classes_par_colonne)==$cpt/$nb_classes_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		echo "<label for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='tab_id_classe[]' id='tab_id_classe_$cpt' value='$lig_clas->id' /> $lig_clas->classe</label>";
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
	echo "<p class='bold'>";
	//echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
	echo "<a href='bull_index.php'>Choisir d'autres classes</a>\n";
	if(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='administrateur')) {
		echo " | <a href='param_bull.php' target='_blank'>Paramètres d'impression des bulletins</a>";
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
		if((strlen(ereg_replace("[0-9]","",$tab_id_classe[$i])))||($tab_id_classe[$i]=="")) {
			echo "<p>Identifiant de classe erroné: <span style='color:red'>".$tab_id_classe[$i]."</span></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";

		$sql="SELECT p.* FROM periodes p WHERE p.id_classe='".$tab_id_classe[$i]."' ORDER BY num_periode;";
		$res_per=mysql_query($sql);

		if(mysql_num_rows($res_per)>0) {
			$ligne_debug[$i]="<tr><td>".get_class_from_id($tab_id_classe[$i])."</td>\n";
			while($lig_per=mysql_fetch_object($res_per)) {
				if($lig_per->verouiller=="O") {
					$ligne_debug[$i].="<td style='background-color:lightgreen; text-align:center;'>Close</td>\n";
				}
				elseif($lig_per->verouiller=="N") {
					$ligne_debug[$i].="<td style='background-color:red; text-align:center;'>Non close</td>\n";
					if(!in_array($lig_per->num_periode,$tab_periode_num_excluses)) {$tab_periode_num_excluses[]=$lig_per->num_periode;}
				}
				else {
					$ligne_debug[$i].="<td style='background-color:orange; text-align:center;'>Partiellement close</td>\n";
				}

				if($lig_per->num_periode>$max_per) {$max_per=$lig_per->num_periode;}
			}
			$ligne_debug[$i].="</tr>\n";
		}
	}



	echo "<table class='boireaus'>\n";
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
			echo "<td style='background-color:red; text-align:center;'>Période non close<br />pour une classe au moins</td>\n";
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

	echo "<p class='bold'>";
	echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
	//echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres périodes</a>\n";
	//echo " | <a href='#' onClick='history.go(-1);'>Choisir d'autres périodes</a>\n";
	echo " | <a href='".$_SERVER['PHP_SELF']."' onClick=\"document.forms['form_retour'].submit();return false;\">Choisir d'autres périodes</a>\n";
	if(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='administrateur')) {
		echo " | <a href='param_bull.php' target='_blank'>Paramètres d'impression des bulletins</a>";
	}
	echo "</p>\n";

	//===========================
	// FORMULAIRE POUR LE RETOUR AU CHOIX DES PERIODES
	echo "\n<!-- Formulaire de retour au choix des périodes -->\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='form_retour'>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";
	}
	for($j=0;$j<count($tab_periode_num);$j++) {
		echo "<input type='hidden' name='tab_periode_num[$j]' value='".$tab_periode_num[$j]."' />\n";
	}
	echo "</form>\n";
	//===========================


	//echo "<p class='bold'>Sélection des élèves:</p>\n";
	echo "<p class='bold'>Sélection des élèves et paramètres:</p>\n";

	echo "\n<!-- Formulaire de sélection des élèves et de paramétrage -->\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire' target='_blank'>\n";

	//=======================================
	// A remplacer par la suite par un choix:
	echo "<input type='hidden' name='mode_bulletin' value='html' />\n";
	//echo "<input type='hidden' name='un_seul_bull_par_famille' value='non' />\n";
	//=======================================
	echo "<input type='hidden' name='choix_periode_num' value='fait' />\n";



	//=======================================
	//echo "<div style='float:right; width:40%'>\n";
	echo "<div id='div_parametres'>\n";
	echo "<table border='0'>\n";
	echo "<tr><td valign='top'><input type='checkbox' name='un_seul_bull_par_famille' id='un_seul_bull_par_famille' value='oui' /></td><td><label for='un_seul_bull_par_famille' style='cursor: pointer;'>Ne pas imprimer de bulletin pour le deuxième parent<br />(<i>même dans le cas de parents séparés</i>).</label></td></tr>\n";

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
		echo "<tr><td valign='top'><input type='checkbox' name='coefficients_a_1' id='coefficients_a_1' value='oui' /></td><td><label for='coefficients_a_1' style='cursor: pointer;'>Forcer, dans le calcul des moyennes générales, les coefficients des matières à 1, indépendamment des coefficients saisis dans les paramètres de la classe.</label></td></tr>\n";
	//}
	echo "</table>\n";

	// L'admin peut avoir accès aux bulletins, mais il n'a de toute façon pas accès au relevés de notes.
	$sql="SELECT 1=1 FROM droits WHERE id='/cahier_notes/visu_releve_notes.php' AND ".$_SESSION['statut']."='V';";
	$res_verif_droit=mysql_query($sql);
	if (mysql_num_rows($res_verif_droit)>0) {
		echo "<p><input type='checkbox' name='intercaler_releve_notes' id='intercaler_releve_notes' value='y' onchange='display_param_releve();' /> <label for='intercaler_releve_notes' style='cursor: pointer;'>Intercaler les relevés de notes</label>\n";

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

		echo "<script type='text/javascript'>
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


	echo "<p align='center'><input type='submit' name='bouton_valide_select_eleves1' value='Valider' /></p>\n";
	echo "</div>\n";


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
		echo " <a href='#' onClick='cocher_tous_eleves()'>Cocher tous les élèves</a> / <a href='#' onClick='decocher_tous_eleves()'>Décocher tous les élèves</a></p>\n";
	}

	$max_eff_classe=0;
	for($i=0;$i<count($tab_id_classe);$i++) {
		// Est-ce bien un entier?
		if((strlen(ereg_replace("[0-9]","",$tab_id_classe[$i])))||($tab_id_classe[$i]=="")) {
			echo "<p>Identifiant de classe erroné: <span style='color:red'>".$tab_id_classe[$i]."</span></p></form>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";

		echo "<p class='bold'>Classe de ".get_class_from_id($tab_id_classe[$i])."</p>\n";

		echo "<table class='boireaus'>\n";
		echo "<tr>\n";
		echo "<th>Elèves</th>\n";
		for($j=0;$j<count($tab_periode_num);$j++) {
			// Est-ce bien un entier?
			if((strlen(ereg_replace("[0-9]","",$tab_periode_num[$j])))||($tab_periode_num[$j]=="")) {
				echo "<td>Identifiant de période erroné: <span style='color:red'>".$tab_periode_num[$j]."</span></td></tr></table></form>\n";
				require("../lib/footer.inc.php");
				die();
			}

			//echo "<th>Période $j</th>\n";
			$sql="SELECT nom_periode FROM periodes WHERE id_classe='".$tab_id_classe[$i]."' AND num_periode='".$tab_periode_num[$j]."';";
			$res_per=mysql_query($sql);
			$lig_per=mysql_fetch_object($res_per);
			echo "<th>\n";

			echo "<input type='hidden' name='tab_periode_num[$j]' value='".$tab_periode_num[$j]."' />\n";

			echo $lig_per->nom_periode;

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
		$res_ele=mysql_query($sql);
		$alt=1;
		$cpt=0;
		while($lig_ele=mysql_fetch_object($res_ele)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td style='text-align:left;'>".$lig_ele->nom." ".$lig_ele->prenom."</td>\n";
			for($j=0;$j<count($tab_periode_num);$j++) {

				$sql="SELECT 1=1 FROM j_eleves_classes jec
					WHERE jec.id_classe='".$tab_id_classe[$i]."' AND
							jec.periode='".$tab_periode_num[$j]."';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0) {
					echo "<td><input type='checkbox' name='tab_selection_ele_".$i."_".$j."[]' id='tab_selection_ele_".$i."_".$j."_".$cpt."' value=\"".$lig_ele->login."\" checked /></td>\n";
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
	document.getElementById('pliage_param_releve').style.display='';
}
display_param_releve();

// On cache l'accès aux liens de pliage/dépliage des paramètres du relevé de notes
// jusqu'à ce que la case d'insertion des relevés de notes entre les bulletins ait été cochée (au moins une fois)
document.getElementById('pliage_param_releve').style.display='none';

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

	$mode_bulletin=isset($_POST['mode_bulletin']) ? $_POST['mode_bulletin'] : "html";
	$un_seul_bull_par_famille=isset($_POST['un_seul_bull_par_famille']) ? $_POST['un_seul_bull_par_famille'] : "non";
	$coefficients_a_1=isset($_POST['coefficients_a_1']) ? $_POST['coefficients_a_1'] : "non";

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

	echo "<div id='infodiv'>
<p id='titre_infodiv' style='font-weight:bold; text-align:center; border:1px solid black;'></p>
<table class='boireaus' width='100%'>
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


	echo "<script type='text/javascript'>
	document.getElementById('titre_infodiv').innerHTML='Bulletins';
	document.getElementById('td_info').innerHTML='Préparatifs';
	document.getElementById('td_classe').innerHTML='';
	document.getElementById('td_periode').innerHTML='';
	document.getElementById('td_ele').innerHTML='';
</script>\n";

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

	// Prof principal
	$gepi_prof_suivi=getSettingValue("gepi_prof_suivi");

	//=========================================
	// AID
	$bull_affiche_aid=getSettingValue("bull_affiche_aid");

	if ($bull_affiche_aid == 'y') {
		// Initialisations diverses
		unset($call_data_aid_b);
		unset($call_data_aid_e);

		// On prépare l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en tête des bulletins :
		if (!isset($call_data_aid_b)){
			$call_data_aid_b = mysql_query("SELECT * FROM aid_config WHERE (order_display1 ='b' and display_bulletin!='n') ORDER BY order_display2");
			$nb_aid_b = mysql_num_rows($call_data_aid_b);
		}

		// On prépare l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en fin des bulletins :
		if (!isset($call_data_aid_e)){
			$call_data_aid_e = mysql_query("SELECT * FROM aid_config WHERE (order_display1 ='e' and display_bulletin!='n') ORDER BY order_display2");
			$nb_aid_e = mysql_num_rows($call_data_aid_e);
		}
	}
	//=========================================


	// Tableau destiné à stocker toutes les infos
	$tab_bulletin=array();

	//==============================
	$motif="Temoin_1";
	decompte_debug($motif,"$motif avant la boucle classes");
	//==============================

	// Boucle sur les classes
	for($loop_classe=0;$loop_classe<count($tab_id_classe);$loop_classe++) {

		//==============================
		$motif="Temoin_classe";
		decompte_debug($motif,"$motif classe $loop_classe");
		flush();
		echo "<script type='text/javascript'>
	document.getElementById('td_classe').innerHTML='".get_class_from_id($tab_id_classe[$loop_classe])."';
</script>\n";
		//==============================

		//$id_classe=2;
		$id_classe=$tab_id_classe[$loop_classe];
		// Est-ce bien un entier?
		if((strlen(ereg_replace("[0-9]","",$id_classe)))||($id_classe=="")) {
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
		elseif ($_SESSION["statut"] == "administrateur") {
			// On selectionne toutes les classes
			//$sql="SELECT DISTINCT c.* FROM classes c WHERE 1";
			$sql="SELECT 1=1 FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) LIMIT 1;";
		}
		elseif ($_SESSION["statut"] == "professeur") {
			$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs jep, j_eleves_classes jec WHERE (jep.professeur='".$_SESSION['login']."' AND jep.login = jec.login AND jec.id_classe = c.id AND c.id='$id_classe');";
		}
		else {
			// On ne devrait pas arriver jusque-là...
			echo "<p>Droits insuffisants pour effectuer cette opération</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$test_acces_classe=mysql_query($sql);
		if(mysql_num_rows($test_acces_classe)==0) {
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

		// Boucle sur les périodes
		for($loop_periode_num=0;$loop_periode_num<count($tab_periode_num);$loop_periode_num++) {

			//$periode_num=1;
			$periode_num=$tab_periode_num[$loop_periode_num];

			// Est-ce bien un entier?
			if((strlen(ereg_replace("[0-9]","",$periode_num)))||($periode_num=="")) {
				echo "<p>Identifiant de période erroné: <span style='color:red'>".$periode_num."</span></p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			//==============================
			$motif="Temoin_periode";
			decompte_debug($motif,"$motif classe $loop_classe période $periode_num");
			flush();
			echo "<script type='text/javascript'>
	document.getElementById('td_periode').innerHTML='".$periode_num."';
</script>\n";
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
			$res_per=mysql_query($sql);
			$lig_per=mysql_fetch_object($res_per);
			$tab_bulletin[$id_classe][$periode_num]['nom_periode']=$lig_per->nom_periode;
			$tab_bulletin[$id_classe][$periode_num]['verouiller']=$lig_per->verouiller;


			// Liste des élèves à éditer/afficher/imprimer (sélection):
			// tab_ele_".$i."_".$j.
			//$tab_bulletin[$id_classe][$periode_num]['selection_eleves']=array();
			$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$loop_classe.'_'.$loop_periode_num]) ? $_POST['tab_selection_ele_'.$loop_classe.'_'.$loop_periode_num] : array();
			$tab_bulletin[$id_classe][$periode_num]['selection_eleves']=$tab_selection_eleves;


			$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
			$affiche_nbdev=sql_query1("SELECT display_nbdev FROM classes WHERE id='".$id_classe."'");

			// On teste si on affiche les graphiques
			if (getSettingValue("bull_affiche_graphiques") == 'yes'){$affiche_graph = 'y';}else{$affiche_graph = 'n';}

			//========================================
			// Afficher la moyenne générale? (également conditionné par la présence d'un coef non nul au moins)
			$display_moy_gen = sql_query1("SELECT display_moy_gen FROM classes WHERE id='".$id_classe."'");
			//========================================


			//========================================
			// On teste la présence d'au moins un coeff pour afficher la colonne des coef
			$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
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
			//========================================


			if ($affiche_rang == 'y'){
				// On teste la présence d'au moins un coeff pour afficher la colonne des coef
				$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
				include("../lib/calcul_rang.inc.php");
			}


			$tab_bulletin[$id_classe][$periode_num]['test_coef']=$test_coef;
			if($coefficients_a_1=="oui") {
				// On force la valeur de test_coef si on impose des coef 1 pour le calcul des moyennes générales dans /lib/calcul_moy_gen.inc.php
				$tab_bulletin[$id_classe][$periode_num]['test_coef']=1;
			}


			// Informations sur la classe
			$sql="SELECT * FROM classes WHERE id='".$id_classe."';";
			$res_classe=mysql_query($sql);
			$lig_classe=mysql_fetch_object($res_classe);

			$tab_bulletin[$id_classe][$periode_num]['id_classe']=$lig_classe->id;
			$tab_bulletin[$id_classe][$periode_num]['classe']=$lig_classe->classe;
			$tab_bulletin[$id_classe][$periode_num]['formule']=$lig_classe->formule;
			$tab_bulletin[$id_classe][$periode_num]['suivi_par']=$lig_classe->suivi_par;

			$classe=$lig_classe->classe;
			$classe_nom_complet=$lig_classe->nom_complet;

			//==============================
			$motif="Temoin_calcul_moy_gen".$id_classe."_".$periode_num;
			decompte_debug($motif,"$motif avant");
			flush();
			//==============================
			//========================================
			include("../lib/calcul_moy_gen.inc.php");
			// On récupère la plus grande partie des infos via calcul_moy_gen.inc.php
			// Voir en fin du fichier calcul_moy_gen.inc.php la liste des infos récupérées
			//========================================
			//==============================
			$motif="Temoin_calcul_moy_gen".$id_classe."_".$periode_num;
			decompte_debug($motif,"$motif après");
			flush();
			//==============================

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

			// Récupérer l'effectif de la classe,...
			$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='$periode_num';";
			$res_eff_classe=mysql_query($sql);
			//$lig_eff_classe=mysql_fetch_object($res_eff_classe);
			$eff_classe=mysql_num_rows($res_eff_classe);
			//echo "<p>Effectif de la classe: $eff_classe</p>\n";

			// Variables simples
			$tab_bulletin[$id_classe][$periode_num]['eff_classe']=$eff_classe;

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
			$tab_bulletin[$id_classe][$periode_num]['note']=$current_eleve_note;
			$tab_bulletin[$id_classe][$periode_num]['statut']=$current_eleve_statut;
			if(isset($current_eleve_rang)) {$tab_bulletin[$id_classe][$periode_num]['rang']=$current_eleve_rang;}
			$tab_bulletin[$id_classe][$periode_num]['coef_eleve']=$current_coef_eleve;

			// Variables récupérées de calcul_moy_gen.inc.php
			// Tableau d'indice [$i] élève.. mais cette moyenne générale ne prend en compte que les options suivies par l'élève si bien que les moyennes générales de classe diffèrent selon les élèves
			$tab_bulletin[$id_classe][$periode_num]['moy_gen_classe']=$moy_gen_classe;
			$tab_bulletin[$id_classe][$periode_num]['moy_gen_eleve']=$moy_gen_eleve;

			// Variables récupérées de calcul_moy_gen.inc.php
			// Variables simples
			$tab_bulletin[$id_classe][$periode_num]['moy_min_classe']=$moy_min_classe;
			$tab_bulletin[$id_classe][$periode_num]['moy_generale_classe']=$moy_generale_classe;
			$tab_bulletin[$id_classe][$periode_num]['moy_max_classe']=$moy_max_classe;
			$tab_bulletin[$id_classe][$periode_num]['moy_min_classe_grp']=$moy_min_classe_grp;
			$tab_bulletin[$id_classe][$periode_num]['moy_classe_grp']=$current_classe_matiere_moyenne;
			$tab_bulletin[$id_classe][$periode_num]['moy_max_classe_grp']=$moy_max_classe_grp;

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

			// Variables récupérées de calcul_moy_gen.inc.php
			// Tableaux d'indices [$i][$cat] (où $i: eleve et $cat: $categorie_id)
			$tab_bulletin[$id_classe][$periode_num]['moy_cat_classe']=$moy_cat_classe;
			$tab_bulletin[$id_classe][$periode_num]['moy_cat_eleve']=$moy_cat_eleve;

			// matieres_categories(id,nom_court,nom_complet,priority)
			// j_matieres_categories_classes(categorie_id,classe_id,priority,affiche_moyenne)
			// matieres(matiere,nom_complet,priority,categorie_id,matiere_aid,matiere_atelier)
			for($j=0;$j<count($current_group);$j++) {
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
				$res_cat=mysql_query($sql);

				if(mysql_num_rows($res_cat)>0) {
					$lig_cat=mysql_fetch_object($res_cat);

					$tab_bulletin[$id_classe][$periode_num]['cat_id'][$j]=$lig_cat->id;
					$tab_bulletin[$id_classe][$periode_num]['nom_cat_court'][$j]=$lig_cat->nom_court;
					$tab_bulletin[$id_classe][$periode_num]['nom_cat_complet'][$j]=$lig_cat->nom_complet;
					$tab_bulletin[$id_classe][$periode_num]['priority'][$j]=$lig_cat->priority;
					$tab_bulletin[$id_classe][$periode_num]['affiche_moyenne'][$j]=$lig_cat->affiche_moyenne;
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
				}
			}


			// L'ordre des matières est obtenu via calcul_moy_gen.inc.php dans lequel le $affiche_categorieq fixe l'ordre par catégories ou non.



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
					$motif="Temoin_eleve".$id_classe."_".$periode_num;
					decompte_debug($motif,"$motif élève $i: ".$current_eleve_login[$i]);
					flush();

					echo "<script type='text/javascript'>
	document.getElementById('td_ele').innerHTML='".$current_eleve_login[$i]."';
</script>\n";
					//==============================

					// Récup des infos sur l'élève, les responsables, le PP, le CPE,...
					$sql="SELECT * FROM eleves e WHERE e.login='".$current_eleve_login[$i]."';";
					$res_ele=mysql_query($sql);
					$lig_ele=mysql_fetch_object($res_ele);

					$tab_ele['login']=$current_eleve_login[$i];
					$tab_ele['nom']=$lig_ele->nom;
					$tab_ele['prenom']=$lig_ele->prenom;
					$tab_ele['sexe']=$lig_ele->sexe;
					$tab_ele['naissance']=formate_date($lig_ele->naissance);
					$tab_ele['elenoet']=$lig_ele->elenoet;
					$tab_ele['ele_id']=$lig_ele->ele_id;
					$tab_ele['no_gep']=$lig_ele->no_gep;

					$tab_ele['classe']=$classe;
					$tab_ele['id_classe']=$id_classe;
					$tab_ele['classe_nom_complet']=$classe_nom_complet;

					// Régime et redoublement
					$sql="SELECT * FROM j_eleves_regime WHERE login='".$current_eleve_login[$i]."';";
					$res_ele_reg=mysql_query($sql);
					if(mysql_num_rows($res_ele_reg)>0) {
						$lig_ele_reg=mysql_fetch_object($res_ele_reg);

						$tab_ele['regime']=$lig_ele_reg->regime;
						$tab_ele['doublant']=$lig_ele_reg->doublant;
					}

					//$sql="SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve ='".$current_eleve_login[$i]."' AND e.id = j.id_etablissement);";
					$sql="SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve ='".$tab_ele['elenoet']."' AND e.id = j.id_etablissement);";
					$data_etab = mysql_query($sql);
					if(mysql_num_rows($data_etab)>0) {
						$tab_ele['etab_id'] = @mysql_result($data_etab, 0, "id");
						$tab_ele['etab_nom'] = @mysql_result($data_etab, 0, "nom");
						$tab_ele['etab_niveau'] = @mysql_result($data_etab, 0, "niveau");
						$tab_ele['etab_type'] = @mysql_result($data_etab, 0, "type");
						$tab_ele['etab_cp'] = @mysql_result($data_etab, 0, "cp");
						$tab_ele['etab_ville'] = @mysql_result($data_etab, 0, "ville");

						if ($tab_ele['etab_niveau']!='') {
						foreach ($type_etablissement as $type_etab => $nom_etablissement) {
							if ($tab_ele['etab_niveau'] == $type_etab) {
								$tab_ele['etab_niveau_nom']=$nom_etablissement;
							}
						}
						if ($tab_ele['etab_cp']==0) {
							$tab_ele['etab_cp']='';
						}
						if ($tab_ele['etab_type']=='aucun')
							$tab_ele['etab_type']='';
						else
							$tab_ele['etab_type']= $type_etablissement2[$tab_ele['etab_type']][$tab_ele['etab_niveau']];
						}
					}

					// Récup infos CPE
					$sql="SELECT u.* FROM j_eleves_cpe jec, utilisateurs u WHERE e_login='".$current_eleve_login[$i]."' AND jec.cpe_login=u.login;";
					$res_cpe=mysql_query($sql);
					if(mysql_num_rows($res_cpe)>0) {
						$lig_cpe=mysql_fetch_object($res_cpe);
						$tab_ele['cpe']=array();

						$tab_ele['cpe']['login']=$lig_cpe->login;
						$tab_ele['cpe']['nom']=$lig_cpe->nom;
						$tab_ele['cpe']['prenom']=$lig_cpe->prenom;
						$tab_ele['cpe']['civilite']=$lig_cpe->civilite;
					}

					// Récup infos Prof Principal (prof_suivi)
					$sql="SELECT u.* FROM j_eleves_professeurs jep, utilisateurs u WHERE jep.login='".$current_eleve_login[$i]."' AND id_classe='$id_classe' AND jep.professeur=u.login;";
					$res_pp=mysql_query($sql);
					//echo "$sql<br />";
					if(mysql_num_rows($res_pp)>0) {
						$lig_pp=mysql_fetch_object($res_pp);
						$tab_ele['pp']=array();

						$tab_ele['pp']['login']=$lig_pp->login;
						$tab_ele['pp']['nom']=$lig_pp->nom;
						$tab_ele['pp']['prenom']=$lig_pp->prenom;
						$tab_ele['pp']['civilite']=$lig_pp->civilite;
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
					$res_resp=mysql_query($sql);
					//echo "$sql<br />";
					if(mysql_num_rows($res_resp)>0) {
						$cpt=0;
						while($lig_resp=mysql_fetch_object($res_resp)) {
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
					if(mysql_num_rows($res_resp)>2) {
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
						$motif="Temoin_eleve".$id_classe."_".$periode_num."_".$i;
						decompte_debug($motif,"$motif élève $i (".$current_eleve_login[$i].") avant AID");
						flush();
						//==============================

						// On attaque maintenant l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en tête des bulletins :
						//------------------------------
						$z=0;
						while ($z < $nb_aid_b) {
							$display_begin = @mysql_result($call_data_aid_b, $z, "display_begin");
							$display_end = @mysql_result($call_data_aid_b, $z, "display_end");
							$type_note = @mysql_result($call_data_aid_b, 0, "type_note");
							$note_max = @mysql_result($call_data_aid_b, 0, "note_max");
							if (($periode_num >= $display_begin) and ($periode_num <= $display_end)) {
								$indice_aid = @mysql_result($call_data_aid_b, $z, "indice_aid");
								$aid_query = mysql_query("SELECT id_aid FROM j_aid_eleves WHERE (login='".$current_eleve_login[$i]."' and indice_aid='$indice_aid')");
								$aid_id = @mysql_result($aid_query, 0, "id_aid");
								if ($aid_id != '') {

									$tab_ele['aid_b'][$z]['display_begin']=$display_begin;
									$tab_ele['aid_b'][$z]['display_end']=$display_end;

									$tab_ele['aid_b'][$z]['nom']=@mysql_result($call_data_aid_b, $z, "nom");
									$tab_ele['aid_b'][$z]['nom_complet']=@mysql_result($call_data_aid_b, $z, "nom_complet");
									$tab_ele['aid_b'][$z]['message']=@mysql_result($call_data_aid_b, $z, "message");

									//echo "\$tab_ele['aid_b'][$z]['nom_complet']=".$tab_ele['aid_b'][$z]['nom_complet']."<br />";
									//echo "\$type_note=".$type_note."<br />";

									$aid_nom_query = mysql_query("SELECT nom FROM aid WHERE (id='$aid_id' and indice_aid='$indice_aid');");
									$tab_ele['aid_b'][$z]['aid_nom']=@mysql_result($aid_nom_query, 0, "nom");

									//echo "\$tab_ele['aid_b'][$z]['aid_nom']=".$tab_ele['aid_b'][$z]['aid_nom']."<br />";

									// On regarde maintenant quelle sont les profs responsables de cette AID
									$aid_prof_resp_query = mysql_query("SELECT id_utilisateur FROM j_aid_utilisateurs WHERE (id_aid='$aid_id'  and indice_aid='$indice_aid')");
									$nb_lig = mysql_num_rows($aid_prof_resp_query);
									$n = '0';
									while ($n < $nb_lig) {
										//$aid_prof_resp_login[$n] = mysql_result($aid_prof_resp_query, $n, "id_utilisateur");
										$tab_ele['aid_b'][$z]['aid_prof_resp_login'][$n]=mysql_result($aid_prof_resp_query, $n, "id_utilisateur");

										//echo "\$tab_ele['aid_b'][$z]['aid_prof_resp_login'][$n]=".$tab_ele['aid_b'][$z]['aid_prof_resp_login'][$n]."<br />";

										$n++;
									}
									//------
									// On appelle l'appréciation de l'élève, et sa note
									//------
									$current_eleve_aid_appreciation_query = mysql_query("SELECT * FROM aid_appreciations WHERE (login='".$current_eleve_login[$i]."' AND periode='$periode_num' and id_aid='$aid_id' and indice_aid='$indice_aid')");
									$current_eleve_aid_appreciation = @mysql_result($current_eleve_aid_appreciation_query, 0, "appreciation");
									$periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
									$periode_max = mysql_num_rows($periode_query);
									if ($type_note == 'last') {$last_periode_aid = min($periode_max,$display_end);}
									if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {
										$place_eleve = "";
										$current_eleve_aid_note = @mysql_result($current_eleve_aid_appreciation_query, 0, "note");
										$current_eleve_aid_statut = @mysql_result($current_eleve_aid_appreciation_query, 0, "statut");
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

												$tab_ele['aid_b'][$z]['quartile1_classe']=$quartile1_classe;
												$tab_ele['aid_b'][$z]['quartile2_classe']=$quartile2_classe;
												$tab_ele['aid_b'][$z]['quartile3_classe']=$quartile3_classe;
												$tab_ele['aid_b'][$z]['quartile4_classe']=$quartile4_classe;
												$tab_ele['aid_b'][$z]['quartile5_classe']=$quartile5_classe;
												$tab_ele['aid_b'][$z]['quartile6_classe']=$quartile6_classe;

											}
											$current_eleve_aid_note=number_format($current_eleve_aid_note,1, ',', ' ');
										}
										$aid_note_min_query = mysql_query("SELECT MIN(note) note_min FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");

										$aid_note_min = @mysql_result($aid_note_min_query, 0, "note_min");
										if ($aid_note_min == '') {
											$aid_note_min = '-';
										} else {
											$aid_note_min=number_format($aid_note_min,1, ',', ' ');
										}
										$aid_note_max_query = mysql_query("SELECT MAX(note) note_max FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
										$aid_note_max = @mysql_result($aid_note_max_query, 0, "note_max");

										if ($aid_note_max == '') {
											$aid_note_max = '-';
										} else {
											$aid_note_max=number_format($aid_note_max,1, ',', ' ');
										}

										$aid_note_moyenne_query = mysql_query("SELECT round(avg(note),1) moyenne FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
										$aid_note_moyenne = @mysql_result($aid_note_moyenne_query, 0, "moyenne");
										if ($aid_note_moyenne == '') {
											$aid_note_moyenne = '-';
										} else {
											$aid_note_moyenne=number_format($aid_note_moyenne,1, ',', ' ');
										}

										$tab_ele['aid_b'][$z]['aid_note']=$current_eleve_aid_note;
										$tab_ele['aid_b'][$z]['aid_statut']=$current_eleve_aid_statut;
										$tab_ele['aid_b'][$z]['aid_note_moyenne']=$aid_note_moyenne;
										$tab_ele['aid_b'][$z]['aid_note_max']=$aid_note_max;
										$tab_ele['aid_b'][$z]['aid_note_min']=$aid_note_min;
										$tab_ele['aid_b'][$z]['place_eleve']=$place_eleve;
									}
									$tab_ele['aid_b'][$z]['aid_appreciation']=$current_eleve_aid_appreciation;

									//echo "\$tab_ele['aid_b'][$z]['aid_appreciation']=".$tab_ele['aid_b'][$z]['aid_appreciation']."<br />";

									// Vaut-il mieux un tableau $tab_ele['aid_b']
									// ou calquer sur $tab_bulletin[$id_classe][$periode_num]['groupe']
									// La deuxième solution réduirait sans doute le nombre de requêtes
								}
							}
							$z++;
						}


						//echo "<p>".$tab_ele['login']."<br />";
						// On attaque maintenant l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en fin des bulletins :
						//------------------------------
						$z=0;
						while ($z < $nb_aid_e) {
							$display_begin = @mysql_result($call_data_aid_e, $z, "display_begin");
							$display_end = @mysql_result($call_data_aid_e, $z, "display_end");
							$type_note = @mysql_result($call_data_aid_e, 0, "type_note");
							$note_max = @mysql_result($call_data_aid_e, 0, "note_max");
							if (($periode_num >= $display_begin) and ($periode_num <= $display_end)) {
								$indice_aid = @mysql_result($call_data_aid_e, $z, "indice_aid");
								$aid_query = mysql_query("SELECT id_aid FROM j_aid_eleves WHERE (login='".$current_eleve_login[$i]."' and indice_aid='$indice_aid')");
								$aid_id = @mysql_result($aid_query, 0, "id_aid");
								if ($aid_id != '') {

									$tab_ele['aid_e'][$z]['display_begin']=$display_begin;
									$tab_ele['aid_e'][$z]['display_end']=$display_end;

									$tab_ele['aid_e'][$z]['nom']=@mysql_result($call_data_aid_e, $z, "nom");
									$tab_ele['aid_e'][$z]['nom_complet']=@mysql_result($call_data_aid_e, $z, "nom_complet");
									$tab_ele['aid_e'][$z]['message']=@mysql_result($call_data_aid_e, $z, "message");

									//echo "\$tab_ele['aid_e'][$z]['nom_complet']=".$tab_ele['aid_e'][$z]['nom_complet']."<br />";
									//echo "\$type_note=".$type_note."<br />";

									$aid_nom_query = mysql_query("SELECT nom FROM aid WHERE (id='$aid_id' and indice_aid='$indice_aid');");
									$tab_ele['aid_e'][$z]['aid_nom']=@mysql_result($aid_nom_query, 0, "nom");

									//echo "\$tab_ele['aid_e'][$z]['aid_nom']=".$tab_ele['aid_e'][$z]['aid_nom']."<br />";

									// On regarde maintenant quelle sont les profs responsables de cette AID
									$aid_prof_resp_query = mysql_query("SELECT id_utilisateur FROM j_aid_utilisateurs WHERE (id_aid='$aid_id'  and indice_aid='$indice_aid')");
									$nb_lig = mysql_num_rows($aid_prof_resp_query);
									$n = '0';
									while ($n < $nb_lig) {
										//$aid_prof_resp_login[$n] = mysql_result($aid_prof_resp_query, $n, "id_utilisateur");
										$tab_ele['aid_e'][$z]['aid_prof_resp_login'][$n]=mysql_result($aid_prof_resp_query, $n, "id_utilisateur");

										//echo "\$tab_ele['aid_e'][$z]['aid_prof_resp_login'][$n]=".$tab_ele['aid_e'][$z]['aid_prof_resp_login'][$n]."<br />";

										$n++;
									}
									//------
									// On appelle l'appréciation de l'élève, et sa note
									//------
									$current_eleve_aid_appreciation_query = mysql_query("SELECT * FROM aid_appreciations WHERE (login='".$current_eleve_login[$i]."' AND periode='$periode_num' and id_aid='$aid_id' and indice_aid='$indice_aid')");
									$current_eleve_aid_appreciation = @mysql_result($current_eleve_aid_appreciation_query, 0, "appreciation");
									$periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
									$periode_max = mysql_num_rows($periode_query);
									if ($type_note == 'last') {$last_periode_aid = min($periode_max,$display_end);}
									if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {
										$place_eleve = "";
										$current_eleve_aid_note = @mysql_result($current_eleve_aid_appreciation_query, 0, "note");
										$current_eleve_aid_statut = @mysql_result($current_eleve_aid_appreciation_query, 0, "statut");
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

												$tab_ele['aid_e'][$z]['quartile1_classe']=$quartile1_classe;
												$tab_ele['aid_e'][$z]['quartile2_classe']=$quartile2_classe;
												$tab_ele['aid_e'][$z]['quartile3_classe']=$quartile3_classe;
												$tab_ele['aid_e'][$z]['quartile4_classe']=$quartile4_classe;
												$tab_ele['aid_e'][$z]['quartile5_classe']=$quartile5_classe;
												$tab_ele['aid_e'][$z]['quartile6_classe']=$quartile6_classe;

											}
											$current_eleve_aid_note=number_format($current_eleve_aid_note,1, ',', ' ');
										}
										$aid_note_min_query = mysql_query("SELECT MIN(note) note_min FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");

										$aid_note_min = @mysql_result($aid_note_min_query, 0, "note_min");
										if ($aid_note_min == '') {
											$aid_note_min = '-';
										} else {
											$aid_note_min=number_format($aid_note_min,1, ',', ' ');
										}
										$aid_note_max_query = mysql_query("SELECT MAX(note) note_max FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
										$aid_note_max = @mysql_result($aid_note_max_query, 0, "note_max");

										if ($aid_note_max == '') {
											$aid_note_max = '-';
										} else {
											$aid_note_max=number_format($aid_note_max,1, ',', ' ');
										}

										$aid_note_moyenne_query = mysql_query("SELECT round(avg(note),1) moyenne FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
										$aid_note_moyenne = @mysql_result($aid_note_moyenne_query, 0, "moyenne");
										if ($aid_note_moyenne == '') {
											$aid_note_moyenne = '-';
										} else {
											$aid_note_moyenne=number_format($aid_note_moyenne,1, ',', ' ');
										}

										$tab_ele['aid_e'][$z]['aid_note']=$current_eleve_aid_note;
										$tab_ele['aid_e'][$z]['aid_statut']=$current_eleve_aid_statut;
										$tab_ele['aid_e'][$z]['aid_note_moyenne']=$aid_note_moyenne;
										$tab_ele['aid_e'][$z]['aid_note_max']=$aid_note_max;
										$tab_ele['aid_e'][$z]['aid_note_min']=$aid_note_min;
										$tab_ele['aid_e'][$z]['place_eleve']=$place_eleve;
									}
									$tab_ele['aid_e'][$z]['aid_appreciation']=$current_eleve_aid_appreciation;

									//echo "\$tab_ele['aid_e'][$z]['aid_appreciation']=".$tab_ele['aid_e'][$z]['aid_appreciation']."<br />";

									// Vaut-il mieux un tableau $tab_ele['aid_b']
									// ou calquer sur $tab_bulletin[$id_classe][$periode_num]['groupe']
									// La deuxième solution réduirait sans doute le nombre de requêtes
								}
							}
							$z++;
						}

						//==============================
						$motif="Temoin_eleve".$id_classe."_".$periode_num."_".$i;
						decompte_debug($motif,"$motif élève $i (".$current_eleve_login[$i].") après AID");
						flush();
						//==============================
					}

					//========================




					//==========================================
					// ABSENCES
					$sql="SELECT * FROM absences WHERE (login='".$current_eleve_login[$i]."' AND periode='$periode_num');";
					$current_eleve_absences_query = mysql_query($sql);
					$current_eleve_absences = @mysql_result($current_eleve_absences_query, 0, "nb_absences");
					$current_eleve_nj = @mysql_result($current_eleve_absences_query, 0, "non_justifie");
					$current_eleve_retards = @mysql_result($current_eleve_absences_query, 0, "nb_retards");
					$current_eleve_appreciation_absences = @mysql_result($current_eleve_absences_query, 0, "appreciation");

					if ($current_eleve_absences == '') { $current_eleve_absences = "?"; }
					if ($current_eleve_nj == '') { $current_eleve_nj = "?"; }
					if ($current_eleve_retards=='') { $current_eleve_retards = "?"; }

					$tab_ele['eleve_absences']=$current_eleve_absences;
					$tab_ele['eleve_nj']=$current_eleve_nj;
					$tab_ele['eleve_retards']=$current_eleve_retards;
					$tab_ele['appreciation_absences']=$current_eleve_appreciation_absences;

					$sql="SELECT u.login login,u.civilite FROM utilisateurs u,
												j_eleves_cpe j
											WHERE (u.login=j.cpe_login AND
												j.e_login='".$current_eleve_login[$i]."');";
					$query = mysql_query($sql);
					$current_eleve_cperesp_login = @mysql_result($query, "0", "login");
					$tab_ele['cperesp_login']=$current_eleve_cperesp_login;
					$current_eleve_cperesp_civilite = @mysql_result($query, "0", "civilite");
					$tab_ele['cperesp_civilite']=$current_eleve_cperesp_civilite;
					//==========================================


					// Boucle groupes de la classe $id_classe pour la période $periode_num
					for($j=0;$j<count($current_group);$j++) {
						//============================================
						// A REVOIR: On fait cette requête autant de fois qu'il y a d'élève... l'extraire de la boucle élèves en faisant une autre boucle sur $current_group
						// Nombre total de devoirs:
						$sql="SELECT cd.id FROM cn_devoirs cd, cn_cahier_notes ccn WHERE cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group[$j]["id"]."' AND ccn.periode='$periode_num';";
						//echo "\n<!--sql=$sql-->\n";
						$result_nbct_tot=mysql_query($sql);
						$current_matiere_nbct=mysql_num_rows($result_nbct_tot);

						$tab_bulletin[$id_classe][$periode_num]['groupe'][$j]['nbct']=$current_matiere_nbct;

						//============================================

						// Si l'élève suit l'option, sa note est affectée (éventuellement vide)
						if(isset($current_eleve_note[$j][$i])) {

							// Quartiles
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


							//================================
							// Notes des boites/conteneurs/sous-matieres
							unset($cn_note);
							unset($cn_nom);
							unset($cn_id);

							// On teste si des notes de une ou plusieurs boites du carnet de notes doivent être affichée
							$test_cn = mysql_query("select distinct c.nom_court, c.id, nc.note from cn_cahier_notes cn, cn_conteneurs c, cn_notes_conteneurs nc
							where (
							cn.periode = '".$periode_num."' and
							cn.id_groupe='".$current_group[$j]["id"]."' and
							cn.id_cahier_notes = c.id_racine and
							c.id_racine!=c.id and
							nc.id_conteneur = c.id and
							nc.statut ='y' and
							nc.login='".$current_eleve_login[$i]."' and
							c.display_bulletin = 1
							) ");
							$nb_ligne_cn = mysql_num_rows($test_cn);
							$n = 0;
							while ($n < $nb_ligne_cn) {
								$cn_id[$n] = mysql_result($test_cn, $n, 'c.id');
								$cn_nom[$n] = mysql_result($test_cn, $n, 'c.nom_court');
								$cn_note[$n] = @mysql_result($test_cn, $n ,'nc.note');
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
							// Récup appréciation
							$sql="SELECT appreciation FROM matieres_appreciations WHERE id_groupe='".$current_group[$j]['id']."' AND periode='$periode_num' AND login='".$current_eleve_login[$i]."';";
							$res_app=mysql_query($sql);
							$lig_app=mysql_fetch_object($res_app);
							$current_eleve_app[$j][$i]=$lig_app->appreciation;
							if($current_eleve_app[$j][$i]=="") {$current_eleve_app[$j][$i]="-";}
							//================================

							// Nombre de contrôles
							$sql="SELECT cnd.note FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE cnd.login='".$current_eleve_login[$i]."' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group[$j]["id"]."' AND ccn.periode='$periode_num' AND cnd.statut='';";
							//echo "\n<!--sql=$sql-->\n";
							$result_nbct=mysql_query($sql);
							$current_eleve_nbct=mysql_num_rows($result_nbct);

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
								while ($snnote =  mysql_fetch_assoc($result_nbct)) {
									if ($string_notes != '') $string_notes .= ", ";
									$string_notes .= $snnote['note'];
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
					$sql="SELECT avis FROM avis_conseil_classe WHERE login='".$current_eleve_login[$i]."' AND periode='$periode_num';";
					$res_avis=mysql_query($sql);
					//echo "$sql<br />";
					if(mysql_num_rows($res_avis)>0) {
						$lig_avis=mysql_fetch_object($res_avis);
						$tab_bulletin[$id_classe][$periode_num]['avis'][$i]=$lig_avis->avis;
						//echo $lig_avis->avis;
					}
					else {
						$tab_bulletin[$id_classe][$periode_num]['avis'][$i]="-";
					}

					// On affecte la partie élève $tab_ele dans $tab_bulletin
					$tab_bulletin[$id_classe][$periode_num]['eleve'][$i]=$tab_ele;
				}
			}
		}
	}

	//========================================================================
	// A CE STADE LE TABLEAU $tab_bulletin EST RENSEIGNé
	// PLUS AUCUNE REQUETE NE DEVRAIT ETRE NECESSAIRE
	// OU ALORS IL FAUDRAIT LES EFFECTUER AU-DESSUS ET COMPLETER $tab_bulletin
	//
	// IL Y AURA A RENSEIGNER $tab_bulletin[$id_classe][$periode_num]['modele_pdf']
	// SI ON FAIT UNE IMPRESSION DE BULLETIN PDF, POUR NE PAS REFAIRE LES REQUETES
	// POUR CHAQUE ELEVE.
	//========================================================================

	echo "<script type='text/javascript'>
	document.getElementById('td_info').innerHTML='Affichage';
</script>\n";

	$compteur=0;
	for($loop_classe=0;$loop_classe<count($tab_id_classe);$loop_classe++) {
		$id_classe=$tab_id_classe[$loop_classe];
		$classe=get_class_from_id($id_classe);

		echo "<script type='text/javascript'>
	document.getElementById('td_classe').innerHTML='".$classe."';
</script>\n";

		for($loop_periode_num=0;$loop_periode_num<count($tab_periode_num);$loop_periode_num++) {

			$periode_num=$tab_periode_num[$loop_periode_num];

			//==============================
			$motif="Classe_".$id_classe."_".$periode_num;
			decompte_debug($motif,"$motif avant");
			flush();
			echo "<script type='text/javascript'>
	document.getElementById('td_periode').innerHTML='".$periode_num."';
</script>\n";
			//==============================

			echo "<div class='noprint' style='background-color:white; border: 1px solid red;'>\n";
			echo "<h2>Classe de ".$classe."</h2>\n";
			echo "<p><b>Période $periode_num</b></p>\n";

			echo "<p>Effectif de la classe: ".$tab_bulletin[$id_classe][$periode_num]['eff_classe']."</p>\n";
			echo "</div>\n";

			//$compteur=0;
			//for($i=0;$i<count($tab_bulletin[$id_classe][$periode_num]['eleve']);$i++) {
			for($i=0;$i<$tab_bulletin[$id_classe][$periode_num]['eff_classe'];$i++) {

				if(isset($tab_bulletin[$id_classe][$periode_num]['selection_eleves'])) {
					if(isset($tab_bulletin[$id_classe][$periode_num]['eleve'][$i]['login'])) {

						if (in_array($tab_bulletin[$id_classe][$periode_num]['eleve'][$i]['login'],$tab_bulletin[$id_classe][$periode_num]['selection_eleves'])) {

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

							echo "<script type='text/javascript'>
	document.getElementById('td_ele').innerHTML='".$tab_bulletin[$id_classe][$periode_num]['eleve'][$i]['login']."';
</script>\n";

							$motif="Bulletin_eleve".$id_classe."_".$periode_num."_".$i;
							decompte_debug($motif,"$motif élève $i avant");
							flush();

							// Saut de page si jamais ce n'est pas le premier bulletin
							if($compteur>0) {echo "<p class='saut'>&nbsp;</p>\n";}

							// Génération du bulletin de l'élève
							//bulletin_html($tab_bulletin[$id_classe][$periode_num],$i);
							bulletin_html($tab_bulletin[$id_classe][$periode_num],$i,$tab_releve[$id_classe][$periode_num]);

							$motif="Bulletin_eleve".$id_classe."_".$periode_num."_".$i;
							decompte_debug($motif,"$motif élève $i après");
							flush();

							//==============================================================================================
							// PAR LA SUITE, ON POURRA INSERER ICI, SI L'OPTION EST COCHEE, LE RELEVE DE NOTES DE LA PERIODE
							//==============================================================================================

							echo "<div class='espacement_bulletins'><div align='center'>Espacement (non imprimé) entre les bulletins</div></div>\n";

							$compteur++;

							flush();
						}
					}
				}
			}

			//==============================
			$motif="Classe_".$id_classe."_".$periode_num;
			decompte_debug($motif,"$motif après");
			flush();
			//==============================

		}
	}

	/*
	echo "<style type='text/css'>
	@media screen{
		.espacement_bulletins {
			width: 100%;
			height: 50px;
			border:1px solid red;
			background-color: white;
		}
	}
	@media print{
		.espacement_bulletins {
			display:none;
		}

		#remarques_bas_de_page {
			display:none;
		}

		.alerte_erreur {
			display:none;
		}
	}
</style>\n";
*/

	echo "<script type='text/javascript'>
	document.getElementById('infodiv').style.display='none';
</script>\n";

}

//==============================
$motif="Duree_totale";
//decompte_debug($motif,"$motif après");
decompte_debug($motif,"$motif");
flush();
//==============================

echo "<div id='remarques_bas_de_page'>
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
?>
