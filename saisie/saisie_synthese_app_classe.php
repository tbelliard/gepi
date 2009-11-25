<?php
/*
* $Id$
*
* Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
@set_time_limit(0);

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

// Classe choisie:
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
// Périodes de visualisation:
$periode1=isset($_POST['periode1']) ? $_POST['periode1'] : NULL;
$periode2=isset($_POST['periode2']) ? $_POST['periode2'] : NULL;
// Période de saisie:
$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);

$url_retour=isset($_POST['url_retour']) ? $_POST['url_retour'] : NULL;
if(my_ereg("/saisie/saisie_avis1.php",$_SERVER['HTTP_REFERER'])) {$url_retour="../saisie/saisie_avis1.php?id_classe=$id_classe&amp;periode_num=$num_periode";}
if(my_ereg("/saisie/saisie_avis2.php",$_SERVER['HTTP_REFERER'])) {$url_retour="../saisie/saisie_avis2.php?id_classe=$id_classe&amp;periode_num=$num_periode";}

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

//debug_var();

//if((isset($periode1))&&(isset($periode2))&&(isset($id_classe))&&(isset($num_periode))) {
if((isset($id_classe))&&(isset($num_periode))) {

	if(!isset($periode1)) {$periode1=$num_periode;}
	if(!isset($periode2)) {$periode2=$num_periode;}

	if($_SESSION['statut']=='professeur') {
		// Le prof est-il PP
		$sql="SELECT 1=1 FROM j_eleves_professeurs jep, j_eleves_classes jec WHERE jep.professeur='".$_SESSION['login']."' AND jec.login=jep.login AND jec.periode='$num_periode';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			//tentative_intrusion("2", "Tentative d'accès par un prof à un bulletin simplifié d'un élève qu'il n'a pas en cours, sans en avoir l'autorisation.");

			header('Location: ../accueil.php&msg='.rawurlencode("Vous n'êtes pas autorisé à saisir la synthèse pour cette classe."));

			die();
		}
	}
	else {
		// Sinon, c'est un compte scolarité.
		$sql="SELECT 1=1 FROM j_scol_classes WHERE id_classe='$id_classe';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			header('Location: ../accueil.php&msg='.rawurlencode("Vous n'êtes pas autorisé à saisir la synthèse pour cette classe."));
			die();
		}
	}

	// Tout est choisi, on va passer à l'affichage

	//$synthese="";

	//if(isset($_POST['no_anti_inject_synthese'])) {
	if (isset($NON_PROTECT["synthese"])){
		// On enregistre la synthese
		$synthese=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["synthese"]));

		$synthese=my_ereg_replace('(\\\r\\\n)+',"\r\n",$synthese);

		$sql="SELECT 1=1 FROM synthese_app_classe WHERE id_classe='$id_classe' AND periode='$num_periode';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$sql="INSERT INTO synthese_app_classe SET id_classe='$id_classe', periode='$num_periode', synthese='$synthese';";
			$insert=mysql_query($sql);
			if(!$insert) {$msg="Erreur lors de l'enregistrement de la synthèse.";}
			else {$msg="La synthèse a été enregistrée.";}
		}
		else {
			$sql="UPDATE synthese_app_classe SET synthese='$synthese' WHERE id_classe='$id_classe' AND periode='$num_periode';";
			$update=mysql_query($sql);
			if(!$update) {$msg="Erreur lors de la mise à jour de la synthèse.";}
			else {$msg="La synthèse a été mise à jour.";}
		}
	}

	$sql="SELECT * FROM synthese_app_classe WHERE (id_classe='$id_classe' AND periode='$num_periode');";
	//echo "$sql<br />";
	$res_current_synthese=mysql_query($sql);
	$synthese=@mysql_result($res_current_synthese, 0, "synthese");

	$titre_page="Synthèse classe";
	require_once("../lib/header.inc");
	include "../lib/periodes.inc.php";
	include "../lib/bulletin_simple.inc.php";
	include "../lib/bulletin_simple_classe.inc.php";

	echo "<p class=\"bold\">";
	if(isset($url_retour)) {
		echo "<a href=\"$url_retour\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à la saisie des avis</a>";
	}
	else {
		echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
	}
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Autre classe</a>\n";
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Autre période</a>\n";
	echo "</p>\n";

	$gepiYear = getSettingValue("gepiYear");
	
	if ($periode1 > $periode2) {
		$temp = $periode2;
		$periode2 = $periode1;
		$periode1 = $temp;
	}
	// On teste la présence d'au moins un coeff pour afficher la colonne des coef
	$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
	//echo "\$test_coef=$test_coef<br />";
	// Apparemment, $test_coef est réaffecté plus loin dans un des include()
	$nb_coef_superieurs_a_zero=$test_coef;
	
	// On regarde si on affiche les catégories de matières
	$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
	if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}
	
	// Si le rang des élèves est demandé, on met à jour le champ rang de la table matieres_notes
	$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
	if ($affiche_rang == 'y') {
		$periode_num=$periode1;
		while ($periode_num < $periode2+1) {
			include "../lib/calcul_rang.inc.php";
			$periode_num++;
		}
	}
	
	/*
	// On regarde si on affiche les catégories de matières
	$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
	if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}
	*/
	//echo "\$choix_edit=$choix_edit<br />";
	
	//=========================
	// AJOUT: boireaus 20080316
	$coefficients_a_1="non";
	$affiche_graph = 'n';
	/*
	$get_cat = mysql_query("SELECT id FROM matieres_categories");
	$categories = array();
	while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
		$categories[] = $row["id"];
	}
	*/
	unset($tab_moy_gen);
	//unset($tab_moy_cat_classe);
	for($loop=$periode1;$loop<=$periode2;$loop++) {
		$periode_num=$loop;
		include "../lib/calcul_moy_gen.inc.php";
		$tab_moy_gen[$loop]=$moy_generale_classe;
		//$tab_moy_cat_classe
	}
	
	$display_moy_gen=sql_query1("SELECT display_moy_gen FROM classes WHERE id='".$id_classe."'");


	bulletin_classe($nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$nb_coef_superieurs_a_zero,$affiche_categories);

	// Formulaire de saisie
	echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
	echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";
	echo "<input type='hidden' name='periode1' value='$periode1' />\n";
	echo "<input type='hidden' name='periode2' value='$periode2' />\n";

	if(isset($url_retour)) {echo "<input type='hidden' name='url_retour' value='$url_retour' />\n";}

	echo "<a name='synthese'></a>\n";
	echo "<p><b>Saisie de la synthèse pour le groupe classe&nbsp;:</b><br />\n";
	echo "<textarea class='wrap' name=\"no_anti_inject_synthese\" rows='5' cols='60' onchange=\"changement()\"";
	echo ">".stripslashes($synthese)."</textarea>\n";

	echo "<br /><center><input type='submit' value=Valider /></center>\n";

	require("../lib/footer.inc.php");
	die();

}

//********************************
$titre_page="Synthèse classe";
require_once("../lib/header.inc");
//********************************

if(!isset($id_classe)) {
	// Choix de la classe:
	if($_SESSION['statut']=='professeur') {
		// Le prof est-il PP
		$sql="SELECT DISTINCT id, classe FROM j_eleves_professeurs jep, j_eleves_classes jec WHERE jep.professeur='".$_SESSION['login']."' AND jec.login=jep.login;";
		$res_classe=mysql_query($sql);
		if(mysql_num_rows($res_classe)==0) {
			header('Location: ../accueil.php&msg='.rawurlencode("Vous n'êtes pas autorisé à saisir la synthèse d'une classe."));
			die();
		}
	}
	else {
		$sql="SELECT DISTINCT id, classe FROM j_scol_classes jsc, classes c WHERE jsc.id_classe=c.id ORDER BY c.classe;";
		$res_classe=mysql_query($sql);
		if(mysql_num_rows($res_classe)==0) {
			header('Location: ../accueil.php&msg='.rawurlencode("Vous n'êtes pas autorisé à saisir la synthèse pour une classe."));
			die();
		}
	}

	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Autre classe</a>\n";
	echo "</p>\n";

	$nombreligne = mysql_num_rows($res_classe);
	echo "<p>Cliquez sur la classe pour laquelle vous souhaitez saisir la synthèse des appréciations groupe classe.</p>\n";
	//echo "<table border=0>\n";
	$nb_class_par_colonne=round($nombreligne/3);
		//echo "<table width='100%' border='1'>\n";
		echo "<table width='100%' summary='Choix de la classe'>\n";
		echo "<tr valign='top' align='center'>\n";
		echo "<td align='left'>\n";
	$i = 0;
	while ($i < $nombreligne){
		$id_classe = mysql_result($res_classe, $i, "id");
		$classe_liste = mysql_result($res_classe, $i, "classe");
		//echo "<tr><td><a href='index3.php?id_classe=$id_classe'>$classe_liste</a></td></tr>\n";
		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td align='left'>\n";
		}
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>$classe_liste</a><br />\n";
		$i++;
	}
	echo "</table>\n";

}
else {
	// Choix de la période de saisie et des périodes d'affichage
	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

	echo "</p>\n";

	$sql="SELECT * FROM classes WHERE id='$id_classe'";
	$res_classe=mysql_query($sql);
	if(mysql_num_rows($res_classe)==0) {
		echo "<p style='color:red'>La classe choisie n'existe pas.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	$nom_classe = mysql_result($res_classe, 0, "classe");

	echo "<p class='grand'>Classe de $nom_classe</p>\n";

	include "../lib/periodes.inc.php";

	$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' AND verouiller!='O';";
	$res_per=mysql_query($sql);
	if(mysql_num_rows($res_classe)==0) {
		echo "<p>Toutes les périodes sont closes pour cette classe.<br />Plus aucune modification n'est possible.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";

	echo "<p>Choisissez la période pour laquelle vous souhaitez saisir la synthèse&nbsp;: <br />\n";
	$cpt=0;
	while($lig=mysql_fetch_object($res_per)) {
		echo "<input type='radio' name='num_periode' id='num_periode_$lig->num_periode' value='$lig->num_periode' ";
		if($cpt==0) {echo "checked ";}
		echo "/><label for='num_periode_$lig->num_periode'>".$nom_periode[$lig->num_periode]."</label><br />\n";
		$cpt++;
	}

	echo "<p>Choisissez la(les) période(s) à afficher dans le bulletin de classe&nbsp;: <br />\n";
	echo "De la période : <select onchange=\"change_periode()\" size=1 name=\"periode1\">\n";
	$i = "1" ;
	while ($i < $nb_periode) {
		echo "<option value=$i>$nom_periode[$i] </option>\n";
		$i++;
	}
	echo "</select>\n";
	echo "&nbsp;à la période : <select size=1 name=\"periode2\">\n";
	$i = "1" ;
	while ($i < $nb_periode) {
		echo "<option value=$i>$nom_periode[$i] </option>\n";
		$i++;
	}
	echo "</select>\n";
	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
	echo "<br /><br /><center><input type='submit' value='Valider' /></center>\n";
	echo "</form>\n";

}

require("../lib/footer.inc.php");
?>