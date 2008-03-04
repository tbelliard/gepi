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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// rajout christian

function verif_num($texte_ver) {
	if(!ereg("^[0-9]+$",$texte_ver)){ $texte_ver = ""; } else { $texte_ver = $texte_ver; }
	return $texte_ver;
 }


function verif_date($date_fr)
 {
     $jour = ''; $mois = ''; $annee = '';
     if($date_fr!='') {
       list($jour, $mois, $annee) = explode('/', $date_fr);
	if(verif_num($jour) and verif_num($mois) and verif_num($annee))
	 {
		if(checkdate($mois,$jour,$annee)) { $verif = TRUE; } else { $verif = FALSE; }
	 } else { $verif = FALSE; }
       } else { $verif = FALSE; }
	return($verif);
 }
// fin rajout christian

// Christian renvoye vers le fichier PDF
	if (empty($_GET['classe']) AND empty($_POST['classe'])) {$classe="";}
	    else { if (isset($_GET['classe'])) {$classe=$_GET['classe'];} if (isset($_POST['classe'])) {$classe=$_POST['classe'];} }
	if (empty($_GET['eleve']) AND empty($_POST['eleve'])) {$eleve="";}
	    else { if (isset($_GET['eleve'])) {$eleve=$_GET['eleve'];} if (isset($_POST['eleve'])) {$eleve=$_POST['eleve'];} }
	if (empty($_GET['creer_pdf']) AND empty($_POST['creer_pdf'])) {$creer_pdf="";}
	    else { if (isset($_GET['creer_pdf'])) {$creer_pdf=$_GET['creer_pdf'];} if (isset($_POST['creer_pdf'])) {$creer_pdf=$_POST['creer_pdf'];} }
	if (empty($_GET['avec_nom_devoir']) AND empty($_POST['avec_nom_devoir'])) {$avec_nom_devoir="";}
	    else { if (isset($_GET['avec_nom_devoir'])) {$avec_nom_devoir=$_GET['avec_nom_devoir'];} if (isset($_POST['avec_nom_devoir'])) {$avec_nom_devoir=$_POST['avec_nom_devoir'];} }
	if (empty($_GET['avec_appreciation_devoir']) AND empty($_POST['avec_appreciation_devoir'])) {$avec_appreciation_devoir="";}
	    else { if (isset($_GET['avec_appreciation_devoir'])) {$avec_appreciation_devoir=$_GET['avec_appreciation_devoir'];} if (isset($_POST['avec_appreciation_devoir'])) {$avec_appreciation_devoir=$_POST['avec_appreciation_devoir'];} }
	if (empty($_GET['type']) AND empty($_POST['type'])) {$type="";}
	    else { if (isset($_GET['type'])) {$type=$_GET['type'];} if (isset($_POST['type'])) {$type=$_POST['type'];} }
	if (empty($_GET['avec_adresse_responsable']) AND empty($_POST['avec_adresse_responsable'])) {$avec_adresse_responsable="";}
	    else { if (isset($_GET['avec_adresse_responsable'])) {$avec_adresse_responsable=$_GET['avec_adresse_responsable'];} if (isset($_POST['avec_adresse_responsable'])) {$avec_adresse_responsable=$_POST['avec_adresse_responsable'];} }

    //=========================
	//AJOUT: eric 16022008
    if (empty($_GET['choix_adr_parent']) AND empty($_POST['choix_adr_parent'])) {$choix_adr_parent="";}
	    else { if (isset($_GET['choix_adr_parent'])) {$choix_adr_parent=$_GET['choix_adr_parent'];} if (isset($_POST['choix_adr_parent'])) {$choix_adr_parent=$_POST['choix_adr_parent'];} }
	//=========================


	if (empty($_GET['active_entete_regroupement']) and empty($_POST['active_entete_regroupement'])) {$active_entete_regroupement="";}
	    else { if (isset($_GET['active_entete_regroupement'])) {$active_entete_regroupement=$_GET['active_entete_regroupement'];} if (isset($_POST['active_entete_regroupement'])) {$active_entete_regroupement=$_POST['active_entete_regroupement'];} }
	if (empty($_GET['selection_eleve']) and empty($_POST['selection_eleve'])) { $selection_eleve = ''; }
	   else { if (isset($_GET['selection_eleve'])) { $selection_eleve = $_GET['selection_eleve']; } if (isset($_POST['selection_eleve'])) { $selection_eleve = $_POST['selection_eleve']; } }
	//=========================
	// AJOUT: chapel 20071026
	if (empty($_GET['aff_classe_nom']) and empty($_POST['aff_classe_nom'])) { $aff_classe_nom = ''; }
	   else { if (isset($_GET['aff_classe_nom'])) { $aff_classe_nom = $_GET['aff_classe_nom']; } if (isset($_POST['aff_classe_nom'])) { $aff_classe_nom = $_POST['aff_classe_nom']; } }
	//=========================

	//=========================
	// AJOUT: chapel 20071019
    if (empty($_GET['avec_coef']) and empty($_POST['avec_coef'])) { $avec_coef = ''; }
       else { if (isset($_GET['avec_coef'])) { $avec_coef = $_GET['avec_coef']; } if (isset($_POST['avec_coef'])) { $avec_coef = $_POST['avec_coef']; } }
    if (empty($_GET['avec_date_devoir']) and empty($_POST['avec_date_devoir'])) { $avec_date_devoir = ''; }
       else { if (isset($_GET['avec_date_devoir'])) { $avec_date_devoir = $_GET['avec_date_devoir']; } if (isset($_POST['avec_date_devoir'])) { $avec_date_devoir = $_POST['avec_date_devoir']; } }
    if (empty($_GET['avec_bloc_obser']) and empty($_POST['avec_bloc_obser'])) { $avec_bloc_obser = ''; }
       else { if (isset($_GET['avec_bloc_obser'])) { $avec_bloc_obser = $_GET['avec_bloc_obser']; } if (isset($_POST['avec_bloc_obser'])) { $avec_bloc_obser = $_POST['avec_bloc_obser']; } }
    if (empty($_GET['avec_sign_parent']) and empty($_POST['avec_sign_parent'])) { $avec_sign_parent = ''; }
       else { if (isset($_GET['avec_sign_parent'])) { $avec_sign_parent = $_GET['avec_sign_parent']; } if (isset($_POST['avec_sign_parent'])) { $avec_sign_parent = $_POST['avec_sign_parent']; } }
    if (empty($_GET['avec_sign_pp']) and empty($_POST['avec_sign_pp'])) { $avec_sign_pp = ''; }
       else { if (isset($_GET['avec_sign_pp'])) { $avec_sign_pp = $_GET['avec_sign_pp']; } if (isset($_POST['avec_sign_pp'])) { $avec_sign_pp = $_POST['avec_sign_pp']; } }
	//=========================

	if (empty($_POST['display_date_debut'])) {$date_debut="";} else {$date_debut=$_POST['display_date_debut'];}
	if (empty($_POST['display_date_fin'])) {$date_fin="";} else {$date_fin=$_POST['display_date_fin'];}
	if (!isset($date_debut_exp[0])) { $date_debut_exp = ''; }

	$message_erreur = '';
		if ( !empty($classe[0]) and ( empty($date_debut) or empty($date_fin) ) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oublier pas de sélectioner une date de début et une date de fin !'; }
		if ( empty($classe[0]) and ( !empty($date_debut) and !empty($date_fin) ) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oublier pas de sélectioner la ou les classe(s) !'; }
		if ( empty($classe[0]) and ( empty($date_debut) or empty($date_fin) ) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oublier pas de sélectioner la ou les classe(s) et une date de début et une date de fin !'; }

	$_SESSION['classe'] = $classe;
	$_SESSION['eleve'] = $eleve;
	$_SESSION['avec_nom_devoir'] = $avec_nom_devoir;
	$_SESSION['avec_appreciation_devoir'] = $avec_appreciation_devoir;
	//=========================
	// AJOUT: chapel 20071019
    $_SESSION['avec_coef'] = $avec_coef;
    $_SESSION['avec_date_devoir'] = $avec_date_devoir;
    $_SESSION['avec_bloc_obser'] = $avec_bloc_obser;
    $_SESSION['avec_sign_parent'] = $avec_sign_parent;
    $_SESSION['avec_sign_pp'] = $avec_sign_pp;
	//=========================
	//AJOUT: chapel 20071026
    $_SESSION['aff_classe_nom'] = $aff_classe_nom;
	//=========================
	$_SESSION['type'] = $type;
	$_SESSION['avec_adresse_responsable'] = $avec_adresse_responsable;

	//=========================
	//AJOUT: eric 16022008
	$_SESSION['choix_adr_parent'] = $choix_adr_parent;
	//=========================

	$_SESSION['date_debut_aff'] = $date_debut;
	$_SESSION['date_fin_aff'] = $date_fin;
	$_SESSION['active_entete_regroupement'] = $active_entete_regroupement;
	$date_debut_exp = explode('/', $date_debut);
	$date_fin_exp = explode('/', $date_fin);
	if (isset($date_debut_exp[2])) { $_SESSION['date_debut_exp'] = $date_debut_exp[2]."-".$date_debut_exp[1]."-".$date_debut_exp[0]." 00:00:00"; }
	if (isset($date_fin_exp[2])) { $_SESSION['date_fin_exp'] = $date_fin_exp[2]."-".$date_fin_exp[1]."-".$date_fin_exp[0]." 00:00:00"; }

if(!empty($creer_pdf) and !empty($_SESSION['date_debut_exp']) and !empty($_SESSION['date_fin_exp']) and !empty($classe) and verif_date($date_debut) and verif_date($date_fin))
{ header("Location: releve_pdf.php"); }
// FIN Christian renvoye vers le fichier PDF

//Configuration du calendrier
include("../lib/calendrier/calendrier.class.php");
$cal1 = new Calendrier("form_choix_edit", "display_date_debut");
$cal2 = new Calendrier("form_choix_edit", "display_date_fin");
// rajout christian
$cal3 = new Calendrier("imprime_pdf", "display_date_debut");
$cal4 = new Calendrier("imprime_pdf", "display_date_fin");
// fin rajout christian

// Initialisation des variables
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :NULL);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
    $current_group = get_group($id_groupe);
} else {
    $current_group = false;
}

$choix_edit = isset($_POST["choix_edit"]) ? $_POST["choix_edit"] :NULL;
$login_prof  = isset($_POST["login_prof"]) ? $_POST["login_prof"] :NULL;
$login_eleve   = isset($_POST["login_eleve"]) ? $_POST["login_eleve"] :NULL;
$display_date_debut = isset($_POST["display_date_debut"]) ? $_POST["display_date_debut"] :NULL;
$display_date_fin = isset($_POST["display_date_fin"]) ? $_POST["display_date_fin"] :NULL;

//Ajout Eric
$choix_periode = isset($_POST["choix_periode"]) ? $_POST["choix_periode"] :NULL;

// Modif Christian pour le PDF
$selection = isset($_POST["selection"]) ? $_POST["selection"] :NULL;
if (empty($_GET['format']) AND empty($_POST['format'])) {$format="";}
    else { if (isset($_GET['format'])) {$format=$_GET['format'];} if (isset($_POST['format'])) {$format=$_POST['format'];} }
if (empty($_GET['creer_pdf']) AND empty($_POST['creer_pdf'])) {$creer_pdf="";}
    else { if (isset($_GET['creer_pdf'])) {$creer_pdf=$_GET['creer_pdf'];} if (isset($_POST['creer_pdf'])) {$creer_pdf=$_POST['creer_pdf'];} }
// fin Christian

//====================================================================
// MODIF: boireaus
if(!isset($choix_edit)){
	if((isset($id_classe))||(isset($id_groupe))){
		// On recherche les infos dans la table 'classes':
		if(!isset($id_classe)){
			// Récupérer l'identifiant de la première classe associée au groupe.
			$sql="SELECT id_classe FROM j_groupes_classes WHERE id_groupe='$id_groupe'";
			$res_grp_tmp=mysql_query($sql);
			if(mysql_num_rows($res_grp_tmp)>0){
				$lig_grp_tmp=mysql_fetch_object($res_grp_tmp);
				$tmp_id_classe=$lig_grp_tmp->id_classe;
			}
			else{
				$tmp_id_classe="NON_TROUVé";
			}
		}
		else{
			$tmp_id_classe=$id_classe;
		}

		$sql="SELECT * FROM classes WHERE id='$tmp_id_classe'";
		$res_class_tmp=mysql_query($sql);
		if(mysql_num_rows($res_class_tmp)>0){
			$lig_class_tmp=mysql_fetch_object($res_class_tmp);
			/*
			$rn_nomdev=$lig_class_tmp->rn_nomdev;
			$rn_toutcoefdev=$lig_class_tmp->rn_toutcoefdev;
			$rn_coefdev_si_diff=$lig_class_tmp->rn_coefdev_si_diff;
			$rn_sign_chefetab=$lig_class_tmp->rn_sign_chefetab;
			$rn_sign_pp=$lig_class_tmp->rn_sign_pp;
			$rn_sign_resp=$lig_class_tmp->rn_sign_resp;
			$rn_sign_nblig=$lig_class_tmp->rn_sign_nblig;
			$rn_formule=htmlentities($lig_class_tmp->rn_formule);
			*/
			$avec_nom_devoir=$lig_class_tmp->rn_nomdev;
			$avec_appreciation_devoir="";
			$avec_tous_coef_devoir=$lig_class_tmp->rn_toutcoefdev;
			$avec_coef_devoir=$lig_class_tmp->rn_coefdev_si_diff;
			$avec_date_devoir=$lig_class_tmp->rn_datedev;

			$rn_sign_chefetab=$lig_class_tmp->rn_sign_chefetab;
			$rn_sign_pp=$lig_class_tmp->rn_sign_pp;
			$rn_sign_resp=$lig_class_tmp->rn_sign_resp;
			$rn_sign_nblig=$lig_class_tmp->rn_sign_nblig;
			$rn_formule=htmlentities($lig_class_tmp->rn_formule);

			$chaine_coef="coef.: ";
		}
		else{
			$avec_nom_devoir="";
			$avec_appreciation_devoir="";
			$avec_coef_devoir="";
			$avec_tous_coef_devoir="";
			$chaine_coef="coef.: ";
			$avec_date_devoir="";

			$rn_sign_chefetab="n";
			$rn_sign_pp="n";
			$rn_sign_resp="n";
			$rn_sign_nblig="n";
			$rn_formule="";
		}
	}
	/*
	else{
		$avec_nom_devoir="";
		$avec_coef_devoir="";
		$avec_tous_coef_devoir="";
		$chaine_coef="coef.: ";
		$avec_date_devoir="";

		$rn_sign_chefetab="n";
		$rn_sign_pp="n";
		$rn_sign_resp="n";
		$rn_sign_nblig="n";
		$rn_formule="";
	}
	*/
}
else{
	$avec_nom_devoir=isset($_POST["avec_nom_devoir"]) ? $_POST["avec_nom_devoir"] : "";
	$avec_appreciation_devoir=isset($_POST["avec_appreciation_devoir"]) ? $_POST["avec_appreciation_devoir"] : "";
	$avec_coef_devoir=isset($_POST["avec_coef_devoir"]) ? $_POST["avec_coef_devoir"] : "";
	$avec_tous_coef_devoir=isset($_POST["avec_tous_coef_devoir"]) ? $_POST["avec_tous_coef_devoir"] : "";
	$chaine_coef="coef.: ";
	//echo "<!--\$chaine_coef=$chaine_coef-->\n";
	$avec_date_devoir=isset($_POST["avec_date_devoir"]) ? $_POST["avec_date_devoir"] : "";

	$rn_sign_chefetab=isset($_POST["rn_sign_chefetab"]) ? $_POST["rn_sign_chefetab"] : "";
	$rn_sign_pp=isset($_POST["rn_sign_pp"]) ? $_POST["rn_sign_pp"] : "";
	$rn_sign_resp=isset($_POST["rn_sign_resp"]) ? $_POST["rn_sign_resp"] : "";
	$rn_sign_nblig=isset($_POST["rn_sign_nblig"]) ? $_POST["rn_sign_nblig"] : 3;
	$rn_formule=isset($_POST["rn_formule"]) ? $_POST["rn_formule"] : "";
}//====================================================================

include "../lib/periodes.inc.php";

$get_cat = mysql_query("SELECT id FROM matieres_categories");
$categories = array();
while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
  	$categories[] = $row["id"];
}

$cat_names = array();
foreach ($categories as $cat_id) {
	$cat_names[$cat_id] = mysql_result(mysql_query("SELECT nom_complet FROM matieres_categories WHERE id = '" . $cat_id . "'"), 0);
}

function releve_notes($current_eleve_login,$nb_periode,$anneed,$moisd,$jourd,$anneef,$moisf,$jourf) {
	$gepiYear = getSettingValue("gepiYear");

	// Ajout Eric
	global $choix_periode;

	//echo $choix_periode;


	//====================================================================
	// AJOUT: boireaus
	global $avec_nom_devoir;
	global $avec_appreciation_devoir;
	global $avec_coef_devoir;
	global $avec_tous_coef_devoir;
	global $chaine_coef;
	global $avec_date_devoir;

	global $rn_sign_chefetab;
	global $rn_sign_pp;
	global $rn_sign_resp;
	global $rn_sign_nblig;
	global $rn_formule;
	//====================================================================
	global $categories;
	global $cat_names;
	global $tabdiv_infobulle;
	// données requise :
	//- le login de l'élève    : $current_eleve_login
	//- $periode1 : numéro de la première période à afficher
	//- $periode2 : numéro de la dernière période à afficher
	//- $nom_periode : tableau des noms de période
	//- $gepiYear : année
	//- $id_classe : identifiant de la classe.

	$data_eleve = mysql_query("SELECT * FROM eleves WHERE login='$current_eleve_login'");
	$current_eleve_nom = mysql_result($data_eleve, 0, "nom");
	$current_eleve_prenom = mysql_result($data_eleve, 0, "prenom");
	$current_eleve_sexe = mysql_result($data_eleve, 0, "sexe");
	$current_eleve_naissance = mysql_result($data_eleve, 0, "naissance");
	$current_eleve_naissance = affiche_date_naissance($current_eleve_naissance);
	$call_classe = mysql_query("SELECT id_classe FROM j_eleves_classes WHERE login = '" . $current_eleve_login . "' ORDER BY periode DESC");
	$id_classe = mysql_result($call_classe, 0, "id_classe");
	$classe_eleve = mysql_query("SELECT * FROM classes WHERE id='$id_classe'");
	$current_eleve_classe = mysql_result($classe_eleve, 0, "classe");
	$current_eleve_classe_complet = mysql_result($classe_eleve, 0, "nom_complet");

	$id_classe = mysql_result($classe_eleve, 0, "id");

	$regime_doublant_eleve = mysql_query("SELECT * FROM j_eleves_regime WHERE login = '$current_eleve_login'");
	//$current_eleve_regime = mysql_result($regime_doublant_eleve, 0, "regime");
	//$current_eleve_doublant = mysql_result($regime_doublant_eleve, 0, "doublant");
	$sql="SELECT * FROM j_eleves_regime WHERE login = '$current_eleve_login'";
	//echo "$sql<br />\n";
	$regime_doublant_eleve = mysql_query($sql);
	if(mysql_num_rows($regime_doublant_eleve)>0){
		$current_eleve_regime = mysql_result($regime_doublant_eleve, 0, "regime");
		$current_eleve_doublant = mysql_result($regime_doublant_eleve, 0, "doublant");
	}
	else{
		$current_eleve_regime = "-";
		$current_eleve_doublant = "ext.";
	}


	//Gestion des dates
	$date_fin = $anneef."-".$moisf."-".$jourf." 00:00:00";
	$date_debut = $anneed."-".$moisd."-".$jourd." 00:00:00";
	$display_date_debut = $jourd."/".$moisd."/".$anneed;
	$display_date_fin = $jourf."/".$moisf."/".$anneef;

	// Modif Eric CODE MORT
	/*
	// On calcule le nombre de périodes
	$nb_periode_dans_classe = mysql_query("SELECT * FROM j_eleves_classes WHERE (login='$current_eleve_login' AND id_classe='$id_classe')");
	$count_per = mysql_num_rows($nb_periode_dans_classe);
	*/

	// Est-ce qu'on affiche les catégories de matières ?
	$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
	if ($affiche_categories == "y") {
		$affiche_categories = true;
	} else {
		$affiche_categories = false;
	}

	//-------------------------------
	// On affiche l'en-tête : Les données de l'élève
	//-------------------------------

	echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"2\">\n";
	echo "<tr><td width=30%><p class='bull_simpl'>";
	echo "<b><span class=\"bull_simpl_g\">$current_eleve_nom $current_eleve_prenom</span></b><br />\n";
	if ($current_eleve_sexe == "M") {
		echo "Né le $current_eleve_naissance";
	} else {
		echo "Née le $current_eleve_naissance";
	}
	if ($current_eleve_regime == "d/p") {echo ",&nbsp;demi-pensionnaire";}
	if ($current_eleve_regime == "ext.") {echo ",&nbsp;externe";}
	if ($current_eleve_regime == "int.") {echo ",&nbsp;interne";}
	if ($current_eleve_regime == "i-e")
	if ($current_eleve_sexe == "M"){
		echo ",&nbsp;interne&nbsp;externé";
	}
	else{
		echo ",&nbsp;interne&nbsp;externée";
	}
	echo ", $current_eleve_classe";
	if ($current_eleve_doublant == 'R') {
		if ($current_eleve_sexe == "M") {
			echo "<br /><b>redoublant</b>";
		}
		else {
			echo "<br /><b>redoublante</b>";
		}
	}
	echo "</p>\n";

	echo "</td><td width=40% align=\"center\">";
	echo "<span class=\"bull_simpl_g\">Classe de $current_eleve_classe_complet<br />Année scolaire ".getSettingValue("gepiYear")."<br />";

	//modif ERIC
	if ($choix_periode==0) {
		echo "Relevé de notes du <b>".$display_date_debut."</b> au <b>".$display_date_fin."</b></span>";
	} else {
		// On récupère le nom de la période.
		$requete_periode = "SELECT * FROM `periodes` WHERE `id_classe`=".$id_classe." AND `num_periode`=".$choix_periode."";
		//echo $requete_periode;
		$resultat_periode = mysql_query($requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.mysql_error());
		$data_periode = mysql_fetch_array ($resultat_periode);
		echo "<b>".$data_periode['nom_periode']."</b> : Relevé de notes</span>";
	}

	$nom_fic_logo = getSettingValue("logo_etab");
	$nom_fic_logo_c = "../images/".$nom_fic_logo;
	if (($nom_fic_logo != '') and (file_exists($nom_fic_logo_c))) {
		//echo "</td><td width=* align=\"right\"><IMG SRC=\"".$nom_fic_logo_c."\" BORDER=0 ALT=\"\">";
		//echo "</td><td width='100%' align=\"right\"><IMG SRC=\"".$nom_fic_logo_c."\" BORDER=0 ALT=\"\" />";
		echo "</td><td align=\"right\"><IMG SRC=\"".$nom_fic_logo_c."\" BORDER=0 ALT=\"\" />";
	} else {
		echo "</td><td>&nbsp;";
	}
	echo "</td><td width=20% align=\"center\"><p class='bull_simpl'><span class=\"bull_simpl_g\">".getSettingValue("gepiSchoolName")."</span><br />".getSettingValue("gepiSchoolAdress1")."<br />".getSettingValue("gepiSchoolAdress2")." ".getSettingValue("gepiSchoolZipCode")." ".getSettingValue("gepiSchoolCity")."</p></td></tr></table>\n";
	//-------------------------------
	// Fin de l'en-tête


	// On initialise le tableau :

	$larg_tab = 680;
	$larg_col1 = 120;
	$larg_col2 = $larg_tab - $larg_col1;
	echo "<table width=\"$larg_tab\" border=1 cellspacing=3 cellpadding=3>\n";
	echo "<tr><td width=\"$larg_col1\" class='bull_simpl'><b>Matière</b><br /><i>Professeur</i>";
	echo "</td>";
	echo "<td width=\"$larg_col2\" class='bull_simpl'>Notes sur 20</td></tr>\n";

	//------------------------------
	// Boucle 'groupes'
	//------------------------------
  if ($affiche_categories) {
		// On utilise les valeurs spécifiées pour la classe en question
		$appel_liste_groupes = mysql_query("SELECT DISTINCT jgc.id_groupe, jgm.id_matiere matiere, jgc.categorie_id ".
		"FROM j_eleves_groupes jeg, j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
		"WHERE ( " .
		"jeg.login = '" . $current_eleve_login ."' AND " .
		"jgc.id_groupe = jeg.id_groupe AND " .
		"jgc.categorie_id = jmcc.categorie_id AND " .
		"jgc.id_classe = '".$id_classe."' AND " .
		"jgm.id_groupe = jgc.id_groupe AND " .
		"m.matiere = jgm.id_matiere" .
		") " .
		"ORDER BY jmcc.priority,jgc.priorite,m.nom_complet");
	} else {
		$appel_liste_groupes = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.categorie_id, jgc.coef, jgm.id_matiere matiere " .
		"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_eleves_groupes jeg " .
		"WHERE ( " .
		"jeg.login = '" . $current_eleve_login . "' AND " .
		"jgc.id_groupe = jeg.id_groupe AND " .
		"jgc.id_classe = '".$id_classe."' AND " .
		"jgm.id_groupe = jgc.id_groupe" .
		") " .
		"ORDER BY jgc.priorite,jgm.id_matiere");
	}

	$nombre_groupes = mysql_num_rows($appel_liste_groupes);

	$j = 0;
	$prev_cat_id = null;
	while ($j < $nombre_groupes) {
		// On appelle toutes les infos relatives à la matière
		$current_groupe = mysql_result($appel_liste_groupes, $j, "id_groupe");
		$current_matiere = mysql_result($appel_liste_groupes, $j, "matiere");
		$current_groupe_cat = mysql_result($appel_liste_groupes, $j, "categorie_id");
		if ($affiche_categories) {
		// On regarde si on change de catégorie de matière
			if ($current_groupe_cat != $prev_cat_id) {
				$prev_cat_id = $current_groupe_cat;
				// On est dans une nouvelle catégorie
				// On récupère les infos nécessaires, et on affiche une ligne

				// On détermine le nombre de colonnes pour le colspan
				$nb_total_cols = 2;

				// On regarde s'il faut afficher la moyenne de l'élève pour cette catégorie
				$affiche_cat_moyenne = mysql_result(mysql_query("SELECT affiche_moyenne FROM j_matieres_categories_classes WHERE (classe_id = '" . $id_classe . "' and categorie_id = '" . $prev_cat_id . "')"), 0);

				// On a toutes les infos. On affiche !
				echo "<tr>";
				echo "<td colspan='" . $nb_total_cols . "'>";
				echo "<p style='padding: 0; margin:0; font-size: 10px;'>".$cat_names[$prev_cat_id]."</p></td>";
				echo "</tr>\n";
			}
		}


		$call_profs = mysql_query("SELECT u.login FROM utilisateurs u, j_groupes_professeurs j WHERE ( u.login = j.login and j.id_groupe='$current_groupe') ORDER BY j.ordre_prof");
		$nombre_profs = mysql_num_rows($call_profs);
		$k = 0;
		while ($k < $nombre_profs) {
			$current_matiere_professeur_login[$k] = mysql_result($call_profs, $k, "login");
			$k++;
		}
		$current_matiere_nom_complet_query = mysql_query("SELECT nom_complet FROM matieres WHERE matiere='$current_matiere'");
		$current_matiere_nom_complet = mysql_result($current_matiere_nom_complet_query, 0, "nom_complet");

		echo "<tr><td class='bull_simpl'><b>".htmlentities($current_matiere_nom_complet)."</b>";
		$k = 0;
		While ($k < $nombre_profs) {
			echo "<br /><i>".affiche_utilisateur($current_matiere_professeur_login[$k],$id_classe)."</i>";
			$k++;
		}
		echo "</td>\n";

		echo "<td class='bull_simpl'>";


		//====================================================
		// MODIF: boireaus

		if($avec_coef_devoir=="oui"){

			if ($choix_periode ==0) {
				$sql="SELECT DISTINCT d.coef FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
				nd.login = '".$current_eleve_login."' and
				nd.id_devoir = d.id and
				d.display_parents='1' and
				d.id_racine = cn.id_cahier_notes and
				cn.id_groupe = '".$current_groupe."' and
				d.date >= '".$date_debut."' and
				d.date <= '".$date_fin."'
				)";
			} else {
				$sql="SELECT DISTINCT d.coef FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
				nd.login = '".$current_eleve_login."' and
				nd.id_devoir = d.id and
				d.display_parents='1' and
				d.id_racine = cn.id_cahier_notes and
				cn.id_groupe = '".$current_groupe."' and
				cn.periode = '".$choix_periode."'
				)";
				//echo "<td>".$sql."</td>";
			}


			$res_differents_coef=mysql_query($sql);
			if(mysql_num_rows($res_differents_coef)>1){
				$affiche_coef="oui";
			}
			else{
				$affiche_coef="non";
			}
		}
		//$query_notes = mysql_query("SELECT nd.note, d.nom_court, nd.statut FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
		if ($choix_periode ==0) {
			//$sql1="SELECT d.coef, nd.note, d.nom_court, nd.statut FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
			$sql1="SELECT d.coef, nd.note, nd.comment, d.nom_court, nd.statut, d.date, d.display_parents_app FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
			nd.login = '".$current_eleve_login."' and
			nd.id_devoir = d.id and
			d.display_parents='1' and
			d.id_racine = cn.id_cahier_notes and
			cn.id_groupe = '".$current_groupe."' and
			d.date >= '".$date_debut."' and
			d.date <= '".$date_fin."'
			)
			ORDER BY d.date
			";
			$query_notes = mysql_query($sql1);
		} else {
			$sql1 = "SELECT d.coef, nd.note, nd.comment, d.nom_court, nd.statut, d.date, d.display_parents_app FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
			nd.login = '".$current_eleve_login."' and
			nd.id_devoir = d.id and
  		d.display_parents='1' and
			d.id_racine = cn.id_cahier_notes and
			cn.id_groupe = '".$current_groupe."' and
			cn.periode = '".$choix_periode."'
			)
			ORDER BY d.date
			";
			$query_notes = mysql_query($sql1);
		}
		//echo $sql1;
		//====================================================

		$count_notes = mysql_num_rows($query_notes);
		$m = 0;
		$tiret = "no";
		while ($m < $count_notes) {
      $eleve_display_app = @mysql_result($query_notes,$m,'d.display_parents_app');
			$eleve_app = @mysql_result($query_notes,$m,'nd.comment');
			$eleve_note = @mysql_result($query_notes,$m,'nd.note');
			$eleve_statut = @mysql_result($query_notes,$m,'nd.statut');
			$eleve_nom_court = @mysql_result($query_notes,$m,'d.nom_court');
 			if (($eleve_statut != '') and ($eleve_statut != 'v')) {
    		$affiche_note = $eleve_statut;
	    } else if ($eleve_statut == 'v') {
		    $affiche_note = "";
	    } else {
		    if ($eleve_note != '') {
			    $affiche_note = $eleve_note;
		    } else {
			    $affiche_note = "";
		    }
	    }
			if(($avec_appreciation_devoir=="oui") and ($eleve_display_app=="1")) {
			    if ($affiche_note=="")
          if ($avec_nom_devoir!="oui")
              $affiche_note = $eleve_nom_court;
          else
              $affiche_note = "&nbsp;";
			}
			if ($affiche_note != '') {
				if ($tiret == "yes")
            if (($avec_appreciation_devoir=="oui") or ($avec_nom_devoir=="oui"))
                echo "<br />";
            else
                echo " - ";
				//====================================================================
				// MODIF: boireaus
				//echo "<b>".$affiche_note."</b> (".$eleve_nom_court.")";
				if($avec_nom_devoir=="oui"){
					//echo "<b>".$affiche_note."</b> (".$eleve_nom_court.")";
					echo "$eleve_nom_court: <b>".$affiche_note."</b>";
				}
				else{
					echo "<b>".$affiche_note."</b>";
  			}

				if(($avec_tous_coef_devoir=="oui")||(($avec_coef_devoir=="oui")&&($affiche_coef=="oui"))){
					$coef_devoir = @mysql_result($query_notes,$m,'d.coef');
					echo " (<i><small>".$chaine_coef.$coef_devoir."</small></i>)";
					//echo " \$affiche_coef=$affiche_coef";
				}
				if(($avec_appreciation_devoir=="oui") and ($eleve_display_app=="1")) {
				  echo " - Appréciation : ";
          if ($eleve_app!="")
              echo $eleve_app;
          else
              echo "-";
        }
				if($avec_date_devoir=="oui"){
					$date_note = @mysql_result($query_notes,$m,'d.date');
					// Format: 2006-09-28 00:00:00
					$tmpdate=explode(" ",$date_note);
					$tmpdate=explode("-",$tmpdate[0]);
					echo " (<i><small>$tmpdate[2]/$tmpdate[1]/$tmpdate[0]</small></i>)";
				}
				//====================================================================
				$tiret = "yes";
			}
			$m++;
		}
		echo "&nbsp;";
		echo "</td></tr>\n";
		$j++;
	}
	echo "</table>\n";

/*
	global $rn_sign_chefetab;
	global $rn_sign_pp;
	global $rn_sign_resp;
	global $rn_sign_nblig;
	global $rn_formule;
*/

	if(($rn_sign_chefetab=='y')||($rn_sign_pp=='y')||($rn_sign_resp=='y')){
		$nb_cases=0;
		if($rn_sign_chefetab=='y'){
			$nb_cases++;
		}
		if($rn_sign_pp=='y'){
			$nb_cases++;
		}
		if($rn_sign_resp=='y'){
			$nb_cases++;
		}
		$largeur_case=round($larg_tab/$nb_cases);

		echo "<table border='1' width='$larg_tab'>\n";
		echo "<tr>\n";

		if($rn_sign_chefetab=='y'){
			echo "<td width='$largeur_case'>\n";
			echo "<b>Signature du chef d'établissement:</b>";
			for($i=0;$i<$rn_sign_nblig;$i++){
				echo "<br />\n";
			}
			echo "</td>\n";
		}

		if($rn_sign_pp=='y'){
			echo "<td width='$largeur_case'>\n";
			echo "<b>Signature du ".getSettingValue("gepi_prof_suivi").":</b>";
			for($i=0;$i<$rn_sign_nblig;$i++){
				echo "<br />\n";
			}
			echo "</td>\n";
		}

		if($rn_sign_resp=='y'){
			echo "<td width='$largeur_case'>\n";
			echo "<b>Signature des responsables:</b>";
			for($i=0;$i<$rn_sign_nblig;$i++){
				echo "<br />\n";
			}
			echo "</td>\n";
		}

		echo "</tr>\n";
		echo "</table>\n";
	}

	if($rn_formule!=""){
		echo "<p>".htmlentities($rn_formule)."</p>\n";
	}
}

//**************** EN-TETE *******************************
if (!isset($_POST['display_entete'])) $titre_page = "Visualisation des relevés de notes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE ****************************
?>
<script type='text/javascript' language='javascript'>
function active(num) {
 document.form_choix_edit.choix_edit[num].checked=true;
}

//=======================
// AJOUT chapel 20071019
// fonction permettant d'afficher ou cacher un div
function affichercacher(a) {

	c = a.substr(4);
	var b = document.getElementById(a);

	var f = "img_"+c+"";

	if (b.style.display == "none" || b.style.display == "") {
		b.style.display = "block";
		document.images[f].src="../images/fleche_a.gif";
	}
	else
	{
		b.style.display = "none";
		document.images[f].src="../images/fleche_na.gif";
	}
}

// Inutile: et cela se déclenchait avant que le DIV soit écrit si bien qu'on avait une erreur javascript
//affichercacher('div_1');
//setTimeout("affichercacher('div_1')",1000);
//=======================
</script>

<?php

// Première étape : on choisit la classe ou le groupe
if (!isset($id_classe) and (!isset($id_groupe)) and $_SESSION['statut'] != "responsable" and $_SESSION['statut'] != "eleve") {

    if (
    	(($_SESSION['statut'] == 'scolarite') AND (getSettingValue("GepiAccesReleveScol") == "yes"))
    	OR (($_SESSION['statut'] == 'cpe') AND (getSettingValue("GepiAccesReleveCpe") == "yes"))
    	) {

        //$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	if (($_SESSION['statut'] == 'scolarite') AND (getSettingValue("GepiAccesReleveScol") == "yes")) {
		$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	}
	else{
		$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	}

        $nombreligne = mysql_num_rows($calldata);
//        echo "<b><a href='../accueil.php'>Accueil</a> | Total : ".$nombreligne." classes</b>\n";
// rajout christian
	?><b><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <?php if(empty($format)) { ?><a href='visu_releve_notes.php?format=pdf'>Impression au format PDF</a><?php } else { ?><a href='visu_releve_notes.php?format='>Impression au format HTML</a><?php } ?> | Total : <?php echo $nombreligne; ?> classes</b><?php
	if(((($_SESSION['statut'] == 'scolarite') AND (getSettingValue("GepiAccesReleveScol") == "yes")) OR (($_SESSION['statut'] == 'cpe') AND (getSettingValue("GepiAccesReleveCpe") == "yes"))) AND empty($format))
	{
// fin rajout christian
        //echo "<p>Cliquez sur la classe pour laquelle vous souhaitez extraire les relevés de notes :</p>\n";
/*
        echo "<table border='0'>\n";
        $i = 0;
        while ($i < $nombreligne){
            $id_classe = mysql_result($calldata, $i, "id");
            $classe_liste = mysql_result($calldata, $i, "classe");
            echo "<tr><td><a href='visu_releve_notes.php?id_classe=$id_classe'>$classe_liste</a></td></tr>\n";
            $i++;
        }
        echo "</table>\n";
*/

        //echo "<table border='0'>\n";

		/*
		$nb_class_par_colonne=round($nombreligne/3);
        //echo "<table width='100%' border='1'>\n";
        echo "<table width='100%'>\n";
        echo "<tr valign='top' align='center'>\n";
        $i = 0;
        echo "<td align='left'>\n";
        while ($i < $nombreligne){
		$id_classe = mysql_result($calldata, $i, "id");
		$classe_liste = mysql_result($calldata, $i, "classe");

		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td align='left'>\n";
		}

		echo "<a href='visu_releve_notes.php?id_classe=$id_classe'>$classe_liste</a><br />\n";
		$i++;
        }
        echo "</table>\n";
		*/

		if($nombreligne==0){
			echo "<p>Aucune classe ne vous est attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
		}
		else{
	        echo "<p>Cliquez sur la classe pour laquelle vous souhaitez extraire les relevés de notes :</p>\n";
			$i = 0;
			unset($tab_lien);
			unset($tab_txt);
			while ($i < $nombreligne){
				$tab_lien[$i] = "visu_releve_notes.php?id_classe=".mysql_result($calldata, $i, "id");
				$tab_txt[$i] = mysql_result($calldata, $i, "classe");
				$i++;

			}
			tab_liste($tab_txt,$tab_lien,3);
		}
// rajout christian
	}
// fin rajout christian

// rajout christian
	if(
		((($_SESSION['statut'] == 'scolarite') AND (getSettingValue("GepiAccesReleveScol") == "yes"))
			OR (($_SESSION['statut'] == 'cpe') AND (getSettingValue("GepiAccesReleveCpe") == "yes")))
		AND $format == "pdf")
	{
		?>
		<form method="post" action="visu_releve_notes.php" name="imprime_pdf">
		  <fieldset><legend>S&eacute;lection</legend>
		  <center>
		<?php
		    $annee = strftime("%Y");
		    $mois = strftime("%m");
		    $jour = strftime("%d");

			//=========================
			// MODIF: boireaus 20071118
			// Pour éviter de refaire le choix des dates en changeant de classe, on utilise la SESSION...
			/*
		    if (!isset($_POST['display_date_debut'])) { $display_date_debut = $jour."/".$mois."/".$annee; } else { $display_date_debut = $_POST['display_date_debut']; }
		    if (!isset($_POST['display_date_fin'])) { $display_date_fin = $jour."/".$mois."/".$annee; } else { $display_date_fin = $_POST['display_date_fin']; }
			*/
			$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : (isset($_SESSION['display_date_debut']) ? $_SESSION['display_date_debut'] : $jour."/".$mois."/".$annee);
			$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : (isset($_SESSION['display_date_fin']) ? $_SESSION['display_date_fin'] : $jour."/".$mois."/".$annee);
			//=========================


		?>
		  <a name="calend"></a>Du : <input type="text" name="display_date_debut" size="10" value="<?php echo $display_date_debut; ?>" /><a href="#calend" onClick="<?php echo $cal3->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../lib/calendrier/petit_calendrier.gif" alt="Calendrier" border="0" /></a>&nbsp;au : <input type="text" name = "display_date_fin" size="10" value="<?php echo $display_date_fin; ?>" /><a href="#calend" onClick="<?php echo $cal4->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../lib/calendrier/petit_calendrier.gif" alt="Calendrier" border="0" /></a><br /><span style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 0.8em;">(Veillez à respecter le format jj/mm/aaaa)</span>
		  <br /><br />
		  <select name="classe[]" size="6" multiple="multiple" tabindex="3">
		  <optgroup label="----- Listes des classes -----">
		    <?php
                        $requete_classe = mysql_query('SELECT * FROM '.$prefix_base.'classes, '.$prefix_base.'periodes WHERE '.$prefix_base.'periodes.id_classe = '.$prefix_base.'classes.id  GROUP BY id_classe ORDER BY '.$prefix_base.'classes.classe');
	  		while ($donner_classe = mysql_fetch_array($requete_classe))
		  	 {
				$requete_cpt_nb_eleve_1 =  mysql_query('SELECT count(*) FROM '.$prefix_base.'eleves, '.$prefix_base.'classes, '.$prefix_base.'j_eleves_classes WHERE '.$prefix_base.'classes.id = "'.$donner_classe['id_classe'].'" AND '.$prefix_base.'j_eleves_classes.id_classe='.$prefix_base.'classes.id AND '.$prefix_base.'j_eleves_classes.login='.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.login');
				$requete_cpt_nb_eleve = mysql_num_rows($requete_cpt_nb_eleve_1);
			   ?><option value="<?php echo $donner_classe['id_classe']; ?>" <?php if(!empty($classe) and in_array($donner_classe['id_classe'], $classe)) { ?>selected="selected"<?php } ?>><?php echo $donner_classe['nom_complet']." (".$donner_classe['classe'].") "; ?>&nbsp;;&nbsp; Eff : <?php echo $requete_cpt_nb_eleve; ?></option><?php
			 }
			?>
		  </optgroup>
		  </select>
		  <input value="Liste élève >" name="selection_eleve" onclick="this.form.submit();this.disabled=true;this.value='En cours'" type="submit" title="Transfère les élèves des classe sélectionné" alt="Transfère les élèves des classe sélectionné" />
		  <select name="eleve[]" size="6" multiple="multiple" tabindex="4">
		  <optgroup label="----- Listes des &eacute;l&egrave;ves -----">
		    <?php
			// sélection des id eleves sélectionné.
			if(!empty($classe[0]))
			{
				$cpt_classe_selec = 0; $selection_classe = "";
				while(!empty($classe[$cpt_classe_selec])) { if($cpt_classe_selec == 0) { $selection_classe = $prefix_base."j_eleves_classes.id_classe = ".$classe[$cpt_classe_selec]; } else { $selection_classe = $selection_classe." OR ".$prefix_base."j_eleves_classes.id_classe = ".$classe[$cpt_classe_selec]; } $cpt_classe_selec = $cpt_classe_selec + 1; }
	                        $requete_eleve = mysql_query('SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes WHERE ('.$selection_classe.') AND '.$prefix_base.'j_eleves_classes.login='.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'eleves.nom ASC');
		  		while ($donner_eleve = mysql_fetch_array($requete_eleve))
			  	 {
				   ?><option value="<?php echo $donner_eleve['login']; ?>"  <?php if(!empty($eleve) and in_array($donner_eleve['login'], $eleve)) { ?>selected="selected"<?php } ?>><?php echo strtoupper($donner_eleve['nom'])." ".ucfirst($donner_eleve['prenom']); ?></option><?php
				 }
			}
			?>
		     <?php if(empty($classe[0]) and empty($eleve)) { ?><option value="" disabled="disabled">Vide</option><?php } ?>
		  </optgroup>
		  </select>
			<?php if ( $message_erreur != '' ) { ?><br /><span style="color: #FF0000; font-weight: bold;"><?php echo $message_erreur; ?></span><?php } ?>




			<?php
			//=================================
			// MODIF: chapel 20071019
			?>
		  <div style="text-align: left;">
            <span id='ligne_adresse_parent'><br />
		    <input type="checkbox" name="avec_adresse_responsable" id="avec_adresse_responsable" value="1" <?php if(isset($avec_adresse_responsable) and $avec_adresse_responsable === '1') { ?>checked="checked"<?php } ?> /> <label for="avec_adresse_responsable" style="cursor: pointer;">Afficher les adresses responsables.</label>
		    <br/>
			<!-- DEBUT AJOUT Eric 16022008-->

			<b>choix de l'adresse à imprimer</b>
				<br /><input name="choix_adr_parent" id="choix_adr_parent1" value="1" type="radio" <?php if(isset($choix_adr_parent) and ( $choix_adr_parent === '1' or $choix_adr_parent == '' ) ) { ?>checked="checked"<?php } ?> /><label for="choix_adr_parent1" style="cursor: pointer;"> Tous les Responsables 1</label>
				<br /><input name="choix_adr_parent" id="choix_adr_parent2" value="2" type="radio" <?php if(isset($choix_adr_parent) and $choix_adr_parent === '2') { ?>checked="checked"<?php } ?> /><label for="choix_adr_parent2" style="cursor: pointer;">Tous les Responsables 1 et uniquement les responsables 2 différent du responsable 1</label>
				<br /><input name="choix_adr_parent" id="choix_adr_parent3" value="3" type="radio" <?php if(isset($choix_adr_parent) and $choix_adr_parent === '3') { ?>checked="checked"<?php } ?> /><label for="choix_adr_parent3" style="cursor: pointer;">Tous les Responsables 2</label>
				<br /><input name="choix_adr_parent" id="choix_adr_parent4" value="4" type="radio" <?php if(isset($choix_adr_parent) and $choix_adr_parent === '4') { ?>checked="checked"<?php } ?> /><label for="choix_adr_parent4" style="cursor: pointer;">Uniquement les Responsables 2 différent du responsable 1</label>

			</div>
			<!-- FIN AJOUT Eric 16022008-->
		  </span>
          <br />
          <br />Type
          <select tabindex="5" name="type" id="type">
            <option value="1" <?php if(!empty($type) and $type === '1') { ?>selected="selected"<?php } ?> onClick="javascript:aff_lig_adresse_parent('afficher')">format PDF 1/1 page</option>
            <option value="2" <?php if((!empty($type) and $type === '2') or empty($type)) { ?>selected="selected"<?php } ?> onClick="aff_lig_adresse_parent('cacher')">format PDF 2/1 page</option>
          </select>

	<div style="text-align: left;"><a href="#ao" onclick="affichercacher('div_1')" style="cursor: pointer;"><img style="border: 0px solid ; width: 13px; height: 13px; border: none; padding:2px; margin:2px; float: left;" name="img_1" alt="" title="Information" src="../images/fleche_na.gif" align="middle" />Autres options</a></div>
	<a name="ao"></a>
	<div style="text-align: left;">
		<div id="div_1" style="display: <?php if( $avec_nom_devoir != '' or $avec_appreciation_devoir != '' or $active_entete_regroupement != '' or $avec_coef != '' or $avec_date_devoir != '' or $avec_sign_parent != '' or $avec_sign_pp != '' ) { ?>block<?php } else { ?>none<?php } ?>; border-top: solid 1px; border-bottom: solid 1px; padding: 10px; background-color: #E0EEEF"><!--a name="ao"></a-->
		  <span style="font-family: Arial;">

			<!-- DEBUT AJOUT chapel 20071026-->
			Affichage du nom de la classe
				<input name="aff_classe_nom" id="aff_classe_nom1" value="1" type="radio" <?php if(isset($aff_classe_nom) and ( $aff_classe_nom === '1' or $aff_classe_nom == '' ) ) { ?>checked="checked"<?php } ?> /><label for="aff_classe_nom1" style="cursor: pointer;">Nom long</label>
				<input name="aff_classe_nom" id="aff_classe_nom2" value="2" type="radio" <?php if(isset($aff_classe_nom) and $aff_classe_nom === '2') { ?>checked="checked"<?php } ?> /><label for="aff_classe_nom2" style="cursor: pointer;">Nom court</label>
				<input name="aff_classe_nom" id="aff_classe_nom3" value="3" type="radio" <?php if(isset($aff_classe_nom) and $aff_classe_nom === '3') { ?>checked="checked"<?php } ?> /><label for="aff_classe_nom3" style="cursor: pointer;">Nom long (Nom court)</label><br />
			<!-- FIN AJOUT chapel 20071026-->

			<input type="checkbox" name="avec_nom_devoir" id="avec_nom_devoir" value="oui" <?php if(isset($avec_nom_devoir) and $avec_nom_devoir === 'oui') { ?>checked="checked"<?php } ?> /> <label for="avec_nom_devoir" style="cursor: pointer;">Afficher le nom des devoirs.</label><br />
			<input type="checkbox" name="avec_appreciation_devoir" id="avec_appreciation_devoir" value="oui" <?php if(isset($avec_appreciation_devoir) and $avec_appreciation_devoir === 'oui') { ?>checked="checked"<?php } ?> /> <label for="avec_appreciation_devoir" style="cursor: pointer;"> Afficher les appréciations (lorsque le professeur a validé cette option).</label><br />
			<input type="checkbox" name="active_entete_regroupement" id="active_entete_regroupement" value="1" <?php if(isset($active_entete_regroupement) and $active_entete_regroupement === '1') { ?>checked="checked"<?php } ?> /> <label for="active_entete_regroupement" style="cursor: pointer;">Afficher les catégories.</label><br />
			<input type="checkbox" name="avec_coef" id="avec_coef1" value="oui1" onclick="activedesactive('avec_ceof2','avec_coef1');"  <?php if(isset($avec_coef) and $avec_coef === 'oui1') { ?>checked="checked"<?php } ?> /> <label for="avec_coef1" style="cursor: pointer;">Afficher tous les coefficients des devoirs.</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="avec_coef" id="avec_coef2" value="oui2" onclick="activedesactive('avec_ceof1','avec_coef2');" <?php if(isset($avec_coef) and $avec_coef === 'oui2') { ?>checked="checked"<?php } ?> /> <label for="avec_coef2" style="cursor: pointer;">Afficher les coefficients des devoirs si différent de 1.</label><br />
			<input type="checkbox" name="avec_date_devoir" id="avec_date_devoir" value="1" <?php if(isset($avec_date_devoir) and $avec_date_devoir === '1') { ?>checked="checked"<?php } ?> /> <label for="avec_date_devoir" style="cursor: pointer;">Afficher les dates des devoirs.</label><br />
			<input type="checkbox" name="avec_bloc_obser" id="avec_bloc_obser" value="1" <?php if(isset($avec_bloc_obser) and $avec_bloc_obser === '1') { ?>checked="checked"<?php } ?> /> <label for="avec_bloc_obser" style="cursor: pointer;">Afficher le bloc observation.</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="avec_sign_parent" id="avec_sign_parent" value="1" <?php if(isset($avec_sign_parent) and $avec_sign_parent === '1') { ?>checked="checked"<?php } ?> /> <label for="avec_sign_parent" style="cursor: pointer;">Afficher le bloc signature des parents.</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="avec_sign_pp" id="avec_sign_pp" value="1" <?php if(isset($avec_sign_pp) and $avec_sign_pp === '1') { ?>checked="checked"<?php } ?> /> <label for="avec_sign_pp" style="cursor: pointer;">Afficher le bloc signature du professeur principal.</label>
		  </span>
		</div>
	</div>
			<?php
			//=================================
			?>

			<br />
	 	  <input type="hidden" name="format" value="<?php echo $format; ?>" />
		  <input type="submit" id="creer_pdf" name="creer_pdf" value="Créer le PDF" />
		  </center>
		  </fieldset>
		</form>

		<?php /* rajout christian */ ?>
		<script type='text/javascript' language='javascript'>
			test='cacher';
			function aff_lig_adresse_parent(mode){
				if(mode=='afficher'){
					document.getElementById('ligne_adresse_parent').style.display='';
				}
				else{
					document.getElementById('ligne_adresse_parent').style.display='none';
				}
			   test=document.getElementById('type').value;
			}

			if(test=='cacher') {
				aff_lig_adresse_parent('cacher');
			}
		</script>
		<?php /* fin rajout christian */ ?>


		<?php
	}
// fin rajout christian

    } else if ($_SESSION['statut'] == 'professeur') {
        if ((getSettingValue("GepiAccesReleveProfP") == "yes") AND (getSettingValue("GepiAccesReleveProf") !="yes") AND (getSettingValue("GepiAccesReleveProfTousEleves") !="yes") AND (getSettingValue("GepiAccesReleveProfToutesClasses") !="yes")) {
            // Si on est là ça veut dire que seul un prof de suivi peut voir les relevés
            $call_prof_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)");
            $nombre_classe = mysql_num_rows($call_prof_classe);
            if ($nombre_classe == "0") {
                echo "Vous n'êtes pas ".getSettingValue("gepi_prof_suivi")." ! Vous ne pouvez pas accéder à cette page.</body></html>\n";
                die();
            } else {
                $i = "0";
                echo "<p>Vous êtes ".getSettingValue("gepi_prof_suivi")." dans la classe de :</p>\n";
                while ($i < $nombre_classe) {
                    $id_classe = mysql_result($call_prof_classe, $i, "id");
                    $classe_suivi = mysql_result($call_prof_classe, $i, "classe");
                    echo "<P>$classe_suivi --- <a href='visu_releve_notes.php?id_classe=$id_classe'> relevés de notes.</a></p>\n";
                    $i++;
                }
            }
        } else if (getSettingValue("GepiAccesReleveProf") == "yes" OR (getSettingValue("GepiAccesReleveProfToutesClasses") =="yes") OR (getSettingValue("GepiAccesReleveProfTousEleves") =="yes")) {
            if (getSettingValue("GepiAccesReleveProfTousEleves") == "yes" AND (getSettingValue("GepiAccesReleveProfToutesClasses") != "yes")) {
            echo "<p>Vous pouvez choisir de visualiser les relevés de notes de tous les élèves des classes dans lesquelles vous enseignez, ou bien seulement les élèves que vous avez effectivement en cours. Si vous n'enseignez qu'à des classes entières, cela revient au même.</p>\n";
            // Ici le code pour sélectionner les classes dans lesquelles le prof enseigne
            $_login = $_SESSION['login'];
            $calldata = mysql_query("SELECT DISTINCT jgc.id_classe id_classe, c.classe classe " .
                                    "FROM classes c, j_groupes_professeurs jgp, j_groupes_classes jgc " .
                                    "WHERE (" .
                                    "c.id=jgc.id_classe and ".
                                    "jgc.id_groupe = jgp.id_groupe and ".
                                    "jgp.login = '" . $_login . "'" .
                                    ")");
            $nb_classes = mysql_num_rows($calldata);
            if ($nb_classes == "0") {
                echo "Vous n'êtes professeur dans aucune classe !";
        	echo "</body></html>\n";
                die();
            }

            $i = "0";
            if ($nb_classes == "1") {
                echo "<p>Vous êtes professeur dans la classe suivante :</p>\n";
            } else {
                echo "<p>Vous êtes professeur dans les classes suivantes :</p>\n";
            }
            while ($i < $nb_classes) {
                $id_classe = mysql_result($calldata, $i, "id_classe");
                $classe = mysql_result($calldata, $i, "classe");
                echo "<p><a href='visu_releve_notes.php?id_classe=$id_classe'>$classe</a></p>";
                $i++;
            }
        } elseif (getSettingValue("GepiAccesReleveProfToutesClasses") =="yes") {
            echo "<p>Vous pouvez choisir de visualiser les relevés de notes de toutes les classes, ou bien seulement pour les élèves que vous avez effectivement en cours.</p>\n";
            // Ici le code pour sélectionner toutes les classes
            $_login = $_SESSION['login'];
            //$sql="SELECT id id_classe, classe classe " .
            $sql="SELECT id, classe " .
                                    "FROM classes " .
                                    "ORDER BY classe";
            //echo "<p>$sql</p>\n";
			$calldata = mysql_query($sql);
            $nb_classes = mysql_num_rows($calldata);

            $i = "0";
            if ($nb_classes == "1") {
                echo "<p>Vous avez accès à la classe suivante :</p>\n";
            } else {
                echo "<p>Vous avez accès aux classes suivantes :</p>\n";
            }

			/*
			$nb_class_par_colonne=round($nb_classes/3);
	        echo "<table width='100%'>\n";
	        echo "<tr valign='top' align='center'>\n";
	        $i = 0;
	        echo "<td align='left'>\n";
	        while ($i < $nb_classes){
			$id_classe = mysql_result($calldata, $i, "id_classe");
			$classe_liste = mysql_result($calldata, $i, "classe");

			if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}

			echo "<a href='visu_releve_notes.php?id_classe=$id_classe'>$classe_liste</a><br />\n";
			$i++;
	        }
	        echo "</table>\n";
			*/

			$i = 0;
			unset($tab_lien);
			unset($tab_txt);
			while ($i < $nb_classes){
				$tab_lien[$i] = "visu_releve_notes.php?id_classe=".mysql_result($calldata, $i, "id");
				$tab_txt[$i] = mysql_result($calldata, $i, "classe");
				$i++;

			}
			tab_liste($tab_txt,$tab_lien,3);

        }

        $login = $_SESSION['login'];

		$requete_sql = "SELECT j.id_groupe, jgm.id_matiere, g.description, jgc.id_classe
					   FROM j_groupes_professeurs j, groupes g, j_groupes_matieres jgm, j_groupes_classes jgc
                       WHERE g.id = j.id_groupe
					   AND j.id_groupe=jgm.id_groupe
					   AND j.id_groupe=jgc.id_groupe
                       AND j.login = '" . $login . "'
					   ORDER BY jgm.id_matiere, jgc.id_classe";

        //echo $requete_sql;
        $calldata = mysql_query($requete_sql);

        $nb_groupes = mysql_num_rows($calldata);
        if ($nb_groupes == "0") {
            echo "Vous n'êtes professeur dans aucun groupe ! Vous ne pouvez pas accéder aux relevés de notes...";
            echo "</body></html>\n";
            die();
        }

        $i = "0";
        if ($nb_groupes == "1") {
            echo "<p>Vous êtes professeur dans le groupe suivant :</p>\n";
        } else {
            echo "<p>Vous êtes professeur dans les groupes suivants :</p>\n";
        }
        while ($i < $nb_groupes) {
            $id_groupe = mysql_result($calldata, $i, "id_groupe");
            $groupe_description = mysql_result($calldata, $i, "description");
            $call_classes = mysql_query("SELECT c.classe classe FROM classes c, j_groupes_classes j WHERE (" .
                                        "c.id = j.id_classe AND " .
                                        "j.id_groupe = '" . $id_groupe . "')");
            $nb_classes = mysql_num_rows($call_classes);
            echo "<p><a href='visu_releve_notes.php?id_groupe=$id_groupe'>$groupe_description (";
            for($c=0;$c<$nb_classes;$c++) {
                if ($c!= 0) echo ", ";
                $classe = mysql_result($call_classes, $c, "classe");
                echo $classe;
            }
            echo ")</a></p>\n";
            $i++;
        }


        } else {
            tentative_intrusion(2, "Tentative d'un professeur d'accéder aux relevés de notes sans y être autorisé.");
            echo "<p>Vous n'êtes pas autorisés à être ici.</p>\n";
            die();
        }
    }

} else if (!isset($choix_edit)) {

    // On vérifie que l'on a bien un choix unique : soit une classe soit un groupe, mais pas les deux
    if ($current_group) unset($id_classe);
    // On teste si le professeur a le droit d'être ici

	//echo "<p>\$_SESSION['statut']=".$_SESSION['statut']."</p>";

    if (($_SESSION['statut']=='professeur') AND (getSettingValue("GepiAccesReleveProf")!="yes") AND (getSettingValue("GepiAccesReleveProfTousEleves")!="yes") AND (getSettingValue("GepiAccesReleveProfToutesClasses")!="yes") AND (getSettingValue("GepiAccesReleveProfP") != "yes")) {
        tentative_intrusion(2, "Tentative d'un professeur d'accéder aux relevés de notes sans y être autorisé.");
        echo "Vous ne pouvez pas accéder à cette page.";
        require("../lib/footer.inc.php");
        die();
    } else if (
    	(($_SESSION['statut'] == "scolarite") AND (getSettingValue("GepiAccesReleveScol") != "yes"))
    	 OR (($_SESSION['statut'] == 'cpe') AND (getSettingValue("GepiAccesReleveCpe") != "yes"))
    	 OR ($_SESSION['statut'] == 'responsable' AND getSettingValue("GepiAccesReleveParent") != "yes")
    	 OR ($_SESSION['statut'] == 'eleve' AND getSettingValue("GepiAccesReleveEleve") != "yes")) {
        tentative_intrusion(2, "Tentative d'un utilisateur d'accéder aux relevés de notes sans y être autorisé.");
        echo "Vous ne pouvez pas accéder à cette page.";
        require("../lib/footer.inc.php");
        die();
    } else if (($_SESSION['statut']=='professeur') AND
    			(getSettingValue("GepiAccesReleveProf")!="yes") AND
    			(getSettingValue("GepiAccesReleveProfTousEleves")!="yes") AND
    			(getSettingValue("GepiAccesReleveProfToutesClasses")!="yes") AND
    			(getSettingValue("GepiAccesReleveProfP") == "yes")) {
        $test_classe = sql_query1("SELECT distinct c.id FROM classes c, j_eleves_professeurs s, j_eleves_classes cc
        WHERE (
        s.professeur='" . $_SESSION['login'] . "' AND
        s.login = cc.login AND
        cc.id_classe = c.id AND
        c.id= '".$id_classe."'
        )");
        if ($test_classe == '-1') {
            echo "Vous n'êtes pas ".getSettingValue("gepi_prof_suivi")." de cette classe ! Vous ne pouvez pas accéder à cette page.</body></html>\n";
            die();
        }
	/*
    } else if (($_SESSION['statut'] == "professeur") AND
     			(getSettingValue("GepiAccesReleveProf") == "yes") OR
     			(getSettingValue("GepiAccesReleveProfTousEleves") == "yes") OR
     			(getSettingValue("GepiAccesReleveProfToutesClasses") == "yes")
     			) {
	*/
    } else if (($_SESSION['statut'] == "professeur") AND
     			((getSettingValue("GepiAccesReleveProf") == "yes") OR
     			(getSettingValue("GepiAccesReleveProfTousEleves") == "yes") OR
     			(getSettingValue("GepiAccesReleveProfToutesClasses") == "yes"))
     			) {

        // On commence par regarder si on est dans le cas de la sélection d'un groupe un d'une classe
        if (!$current_group) {
			//echo "<p>\$current_group false</p>";

            // Dans le cas d'une classe, on vérifie que l'accès est autorisé
            //if (getSettingValue("GepiAccesReleveProfTousEleves") != "yes" AND getSettingValue("GepiAccesReleveProfToutesClasses") == "yes") {
            if (getSettingValue("GepiAccesReleveProfTousEleves") != "yes" AND getSettingValue("GepiAccesReleveProfToutesClasses") != "yes") {
                tentative_intrusion(2, "Tentative d'un professeur d'accéder aux relevés de notes de toute une classe alors qu'il n'est autorisé qu'à accéder aux relevés des élèves de ses groupes uniquement.");
                echo "Vous n'êtes pas autorisé à visualiser l'ensemble des élèves de cette classe ! Sélectionnez uniquement un groupe parmi ceux auxquels vous enseignez.</body></html>\n";
                die();
            } else {
                if (getSettingValue("GepiAccesReleveProfToutesClasses") != "yes") {
	                // il a le droit de visualiser des classes. On vérifie s'il est bien professeur dans la classe demandée

	                $test_classe = sql_query1("SELECT DISTINCT jgc.id_classe FROM " .
	                        "j_groupes_professeurs jgp, j_groupes_classes jgc ".
	                        "WHERE (" .
	                        "jgc.id_classe = '". $id_classe . "' AND ".
	                        "jgp.login = '". $_SESSION['login'] . "' AND " .
	                        "jgc.id_groupe = jgp.id_groupe" .
	                        ")");
	                if ($test_classe == '-1') {
	                    tentative_intrusion(2, "Tentative d'un professeur d'accéder aux relevés de notes d'une classe dans laquelle il n'est pas professeur.");
	                    echo "Vous n'êtes pas professeur dans cette classe ! Vous ne pouvez pas accéder à cette page.</body></html>\n";
	                die();
	                }
                }
            }
        }
    }

	if ($_SESSION['statut'] != "responsable" AND $_SESSION['statut'] != "eleve") {
	    if (!$current_group) {
	        $classe_eleve = mysql_query("SELECT classe FROM classes WHERE id='$id_classe'");
	        $nom_classe = mysql_result($classe_eleve, 0, "classe");

	        echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <a href='visu_releve_notes.php'>Choisir une autre classe</a></p>\n";
	        echo "<p class='grand'>Classe de $nom_classe</p>\n";
	        echo "<form enctype=\"multipart/form-data\" action=\"visu_releve_notes.php\" method=\"post\" name=\"form_choix_edit\" target=\"_blank\">\n";
	        echo "<table><tr>\n";
	        echo "<td><input type=\"radio\" name=\"choix_edit\" value=\"1\" checked /></td>\n";
//echo "<input type='hidden' name='choix_periode' value='".$choix_periode. "'/>";

	        echo "<td>Les relevés de notes de tous les élèves de la classe</td></tr>\n";
	    } else {
	        echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <a href='visu_releve_notes.php'>Choisir un autre groupe</a></p>\n";
	        echo "<p class='grand'>Groupe : " . $current_group["description"] . " (" . $current_group["classlist_string"] .")</p>\n";
	        echo "<form enctype=\"multipart/form-data\" action=\"visu_releve_notes.php\" method=\"post\" name=\"form_choix_edit\" target=\"_blank\">\n";
	        echo "<table><tr>\n";
	        echo "<td><input type=\"radio\" name=\"choix_edit\" value=\"1\" checked /></td>\n";
	        echo "<td>Les relevés de notes de tous les élèves du groupe</td></tr>\n";
	        //=========================
	        // AJOUT: boireaus
	        echo "<tr>\n";
	        //=========================
	    }

	    $indice = 1;
	    if (!$current_group) {
	        $call_suivi = mysql_query("SELECT DISTINCT professeur FROM j_eleves_professeurs WHERE id_classe='$id_classe' ORDER BY professeur");
	        $nb_lignes = mysql_num_rows($call_suivi);
	        if ($nb_lignes > 1) {
	            echo "<td><input type=\"radio\" name=\"choix_edit\" value=\"3\" /></td>\n";
	            echo "<td>Uniquement les relevés de notes des élèves dont le ".getSettingValue("gepi_prof_suivi")." est :";
	            echo "<select size=\"1\" name=\"login_prof\" onclick=\"active(1)\">\n";
	            $i=0;
	            while ($i < $nb_lignes) {
	                $login_pr = mysql_result($call_suivi,$i,"professeur");
	                $call_prof = mysql_query("SELECT * FROM utilisateurs WHERE login='$login_pr'");
	                $nom_prof = mysql_result($call_prof,0,"nom");
	                $prenom_prof = mysql_result($call_prof,0,"prenom");
	                echo "<option value=".$login_pr.">".$nom_prof." ".$prenom_prof."</option>\n";
	                $i++;
	            }
	            echo "</select></td></tr>\n";
	            $indice = 2;
	        }
	        //=========================
	        // AJOUT: boireaus
	        echo "<tr>\n";
	        //=========================
	    }


	    echo "<td><input type=\"radio\" name=\"choix_edit\" value=\"2\" /></td>\n";
	    echo "<td>Uniquement le relevé de notes de l'élève sélectionné ci-contre : \n";
	    echo "<select size=\"1\" name=\"login_eleve\" onclick=\"active(".$indice.")\">\n";

	    if (!$current_group) {
	        $call_eleve = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe = '$id_classe' and j.login=e.login) order by nom");
	        $nombreligne = mysql_num_rows($call_eleve);
	        $i = "0" ;
	        while ($i < $nombreligne) {
	            $eleve = mysql_result($call_eleve, $i, 'login');
	            $nom_el = mysql_result($call_eleve, $i, 'nom');
	            $prenom_el = mysql_result($call_eleve, $i, 'prenom');
	            echo "<option value=$eleve>$nom_el  $prenom_el </option>\n";
	            $i++;
	        }
	    } else {
	        foreach($current_group["eleves"]["all"]["list"] as $eleve_login) {
	            $flag = true;
	            $p=1;
	            while ($flag) {
	                if (in_array($eleve_login, $current_group["eleves"][$p]["list"])) {
	                    echo "<option value=" . $eleve_login . ">" . $current_group["eleves"][$p]["users"][$eleve_login]["nom"] . " " . $current_group["eleves"][$p]["users"][$eleve_login]["prenom"] . "</option>\n";
	                    $flag = false;
	                } else {
	                    $p++;
	                }
	            }
	        }

	    }
	    echo "</select></td></tr></table>\n";
	} else {
		// Sélection de l'élève dans le cas d'un responsable d'élève ou d'un élève
		if ($_SESSION['statut'] == "responsable") {
			// MODIFICATION POUR ALLEGER LA REQUETE
			/*
			$sql_quels_eleves = "SELECT DISTINCT jec.id_classe, e.login, e.nom, e.prenom " .
								"FROM j_eleves_classes jec, eleves e, responsables2 re, resp_pers r WHERE (" .
								"e.ele_id = re.ele_id AND " .
								"re.pers_id = r.pers_id AND " .
								"r.login = '" . $_SESSION['login'] .
								"' AND jec.login = e.login )";
			*/

			$sql="SELECT pers_id FROM resp_pers WHERE login='".$_SESSION['login']."';";
			$res_pers_id=mysql_query($sql);
			if(mysql_num_rows($res_pers_id)==0){
				echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a></p>\n";
				echo "<p>Erreur : vous semblez ne pas avoir d'identifiant 'pers_id'. Veuillez contacter l'administrateur.</p>";
				require("../lib/footer.inc.php");
				die();
			}

			$lig_tmp=mysql_fetch_object($res_pers_id);

			$sql_quels_eleves = "SELECT DISTINCT jec.id_classe, e.login, e.nom, e.prenom " .
								"FROM j_eleves_classes jec, eleves e, responsables2 re WHERE (" .
								"e.ele_id = re.ele_id AND " .
								"re.pers_id = '$lig_tmp->pers_id' AND " .
								"jec.login = e.login )";
			//echo "$sql_quels_eleves<br />";
			$quels_eleves=mysql_query($sql_quels_eleves);

		} elseif ($_SESSION['statut'] == "eleve") {
		    $sql_quels_eleves = "SELECT DISTINCT jec.id_classe, e.login, e.nom, e.prenom
								FROM eleves e, j_eleves_classes jec WHERE (
								e.login = '" . $_SESSION['login'] .
								"' AND jec.login = e.login)";
			//echo $sql_quels_eleves;
			$quels_eleves = mysql_query($sql_quels_eleves);
		}
		if (mysql_num_rows($quels_eleves) == 0) {
	        echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a></p>\n";
			echo "<p>Erreur : vous ne semblez être associé à aucun élève. Veuillez contacter l'administrateur.</p>\n";
			require("../lib/footer.inc.php");
			die();
		} elseif (mysql_num_rows($quels_eleves) == 1) {
		    echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a></p>\n";
			$current_eleve = mysql_fetch_object($quels_eleves);
			$id_classe =  $current_eleve->id_classe;
			echo "<br /><br />\n";
			echo "<p class='bold'>Elève : ".$current_eleve->prenom . " " . $current_eleve->nom."</p>\n";
	        echo "<form enctype=\"multipart/form-data\" action=\"visu_releve_notes.php\" method=\"post\" name=\"form_choix_edit\" target=\"_blank\">\n";
			echo "<input type='hidden' name='login_eleve' value='".$current_eleve->login . "'/>\n";
			echo "<input type='hidden' name='choix_edit' value='2' />\n";
		} else {
	        echo "<form enctype=\"multipart/form-data\" action=\"visu_releve_notes.php\" method=\"post\" name=\"form_choix_edit\" target=\"_blank\">\n";
			echo "<p class='bold'>Elève : ";
			echo "<input type='hidden' name='choix_edit' value='2'/>\n";
	    	echo "<select size=\"1\" name=\"login_eleve\">\n";
	    	// On initialise un tableau pour stocker les différentes classes impliquées (ceci pour récupérer ensuite les périodes)
	    	$eleves_classes = array();
	    	while ($current_eleve = mysql_fetch_object($quels_eleves)) {
	        	echo "<option value=" . $current_eleve->login . ">" . $current_eleve->prenom . " " . $current_eleve->nom . "</option>\n";
	        	$eleves_classes[] = $current_eleve->id_classe;
	    	}
	    	echo "</select>\n";
		}
	}

    //Modif Eric
    echo "<br /><br />\n<p><b>Choisissez la période d'affichage : </b></p>\n<br />\n";

	if ($id_groupe != NULL) { // on recherche la classe à partir de id_groupe
	  $requete_classe = "SELECT * FROM `j_groupes_classes` WHERE `id_groupe`='".$id_groupe."'";
	  //echo $requete_classe;
	  $resultat_classe = mysql_query($requete_classe) or die('Erreur SQL !'.$requete_classe.'<br />'.mysql_error());
	  $data_classe = mysql_fetch_array ($resultat_classe);
	  $id_classe = $data_classe['id_classe'];
	  //echo $id_classe;
	}
	if ($id_classe != NULL)  { // on recherche les périodes pour la classe
		$requete_periode = "SELECT * FROM `periodes` WHERE `id_classe`='".$id_classe."'";
		//echo $requete_periode;
		$resultat_periode = mysql_query($requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.mysql_error());
		while ($data_periode = mysql_fetch_array ($resultat_periode)) {
		   echo "<input type=\"radio\" name=\"choix_periode\" id='choix_periode_".$data_periode['num_periode']."' value='".$data_periode['num_periode']."'  /><label for='choix_periode_".$data_periode['num_periode']."' style='cursor: pointer;'> ".$data_periode['nom_periode']." </label><br />\n";
		}
    }
    if ($_SESSION['statut'] == "responsable" AND mysql_num_rows($quels_eleves) > 1) {
    	// Cas où on a plusieurs élèves pour un même parent. Problème : on ne sait pas si
    	// le schéma de périodes pour les différents élèves est identique... On va donc
    	// vérifier ça. Si le schéma est identique, on propose le choix de périodes, sinon
    	// on va simplement afficher le choix de dates...
    	// TODO: ajouter un peu de javascript pour que dans le cas de schémas de périodes différents
    	// on change simplement le formulaire dynamiquement lorsque l'on change d'élève dans le
    	// select ci-dessus...
    	$ok = true;
    	$classe_periodes = array();
    	$periode_prec = false;
    	foreach($eleves_classes as $current_classe) {
			$requete_periode = mysql_query("SELECT * FROM `periodes` WHERE `id_classe`='".$current_classe."'");
			if ($periode_prec and mysql_num_rows($requete_periode) != $periode_prec) {
				$ok = false;
			}
			$classe_periodes[$current_classe] = $requete_periode;
			$periode_prec = mysql_num_rows($requete_periode);
    	}
    	if ($ok) {
    		// On a un schéma de périodes similaire pour les classes des élèves considérés.
    		// On affiche le choix des périodes.
    		// On prend les informations de la première classe...
    		$resultat_periode = current($classe_periodes);
    		while ($data_periode = mysql_fetch_array($resultat_periode)) {
		   		echo "<input type=\"radio\" name=\"choix_periode\" id='choix_periode_".$data_periode['num_periode']."' value='".$data_periode['num_periode']."'  /><label for='choix_periode_".$data_periode['num_periode']."' style='cursor: pointer;'> ".$data_periode['nom_periode']." </label><br />\n";
			}
    	}
    }
    $annee = strftime("%Y");
    $mois = strftime("%m");
    $jour = strftime("%d");
	//=========================
	// MODIF: boireaus 20071118
	// Pour éviter de refaire le choix des dates en changeant de classe, on utilise la SESSION...
    /*
    if (!isset($_POST['display_date_debut'])) $display_date_debut = $jour."/".$mois."/".$annee;
    if (!isset($_POST['display_date_fin'])) $display_date_fin = $jour."/".$mois."/".$annee;
	*/
	$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : (isset($_SESSION['display_date_debut']) ? $_SESSION['display_date_debut'] : $jour."/".$mois."/".$annee);
	$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : (isset($_SESSION['display_date_fin']) ? $_SESSION['display_date_fin'] : $jour."/".$mois."/".$annee);
	//=========================

    echo "<a name=\"calend\"></a>";
	echo "<input type=\"radio\" name=\"choix_periode\" id='choix_periode_dates' value=\"0\" checked /><label for='choix_periode_dates' style='cursor: pointer;'> \nDe la date : ";
    echo "<input type='text' name = 'display_date_debut' size='10' value = \"".$display_date_debut."\" />";
    echo "<a href=\"#calend\" onClick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";

    echo "&nbsp;à la date : ";
    echo "<input type='text' name = 'display_date_fin' size='10' value = \"".$display_date_fin."\" />";
    echo "<a href=\"#calend\" onClick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";

    echo " (Veillez à respectez le format jj/mm/aaaa)</label>";



    //====================================================================
    // MODIF: boireaus

	if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
		$avec_nom_devoir="y";
		$avec_appreciation_devoir="n";
		$avec_tous_coef_devoir="n";
		$avec_coef_devoir="n";
		$avec_date_devoir="n";
		$rn_sign_chefetab="n";
		$rn_sign_pp="n";
		$rn_sign_resp="n";
		$rn_sign_nblig=0;
		$rn_formule="";

		//Modif Eric pour masquage des options sur le relevé de notes pour un responsable ou un élève)
		if (($_SESSION['statut']=='eleve') AND (getSettingValue("GepiAccesOptionsReleveEleve") == "yes"))
		{
			echo "<br /><br /><br /><p><b>Options d'affichage : </b></p>\n";

			echo "\n<br />\n<input type='checkbox' name='avec_nom_devoir' id='avec_nom_devoir' value='oui' ";
			if($avec_nom_devoir=="y"){echo "checked ";}
			echo "/><label for='avec_nom_devoir' style='cursor: pointer;'> Afficher le nom des devoirs.</label>\n";

      echo "\n<br />\n<input type='checkbox' name='avec_appreciation_devoir' id='avec_appreciation_devoir' value='oui' ";
			if($avec_appreciation_devoir=="y"){echo "checked ";}
			echo "/><label for='avec_appreciation_devoir' style='cursor: pointer;'> Afficher les appréciations (lorsque le professeur a validé cette option).</label>\n";

			echo "<br />\n";
			echo "<input type='checkbox' name='avec_tous_coef_devoir' id='avec_tous_coef_devoir' value='oui' ";
			if($avec_tous_coef_devoir=="y"){echo "checked ";}
			echo "/><label for='avec_tous_coef_devoir' style='cursor: pointer;'> Afficher tous les coefficients des devoirs.</label>\n";

			echo "<br />\n";
			echo "<input type='checkbox' name='avec_coef_devoir' id='avec_coef_devoir' value='oui' ";
			if($avec_coef_devoir=="y"){echo "checked ";}
			echo "/><label for='avec_coef_devoir' style='cursor: pointer;'> Afficher les coefficients des devoirs si des coefficients différents sont présents.</label>\n";

			echo "<br />\n";
			echo "<input type='checkbox' name='avec_date_devoir' id='avec_date_devoir' value='oui' ";
			if($avec_date_devoir=="y"){echo "checked ";}
			echo "/><label for='avec_date_devoir' style='cursor: pointer;'> Afficher les dates des devoirs.</label>\n";
		}

		if (($_SESSION['statut']=='responsable') AND (getSettingValue("GepiAccesOptionsReleveParent") == "yes"))
		{
			echo "<br /><br /><br /><p><b>Options d'affichage : </b></p>\n";

			echo "\n<br />\n<input type='checkbox' name='avec_nom_devoir' id='avec_nom_devoir' value='oui' ";
			if($avec_nom_devoir=="y"){echo "checked ";}
			echo "/><label for='avec_nom_devoir' style='cursor: pointer;'> Afficher le nom des devoirs.</label>\n";

			echo "\n<br />\n<input type='checkbox' name='avec_appreciation_devoir' id='avec_appreciation_devoir' value='oui' ";
			if($avec_appreciation_devoir=="y"){echo "checked ";}
			echo "/><label for='avec_appreciation_devoir' style='cursor: pointer;'> Afficher les appréciations (lorsque le professeur a validé cette option).</label>\n";

			echo "<br />\n";
			echo "<input type='checkbox' name='avec_tous_coef_devoir' id='avec_tous_coef_devoir' value='oui' ";
			if($avec_tous_coef_devoir=="y"){echo "checked ";}
			echo "/><label for='avec_tous_coef_devoir' style='cursor: pointer;'> Afficher tous les coefficients des devoirs.</label>\n";

			echo "<br />\n";
			echo "<input type='checkbox' name='avec_coef_devoir' id='avec_coef_devoir' value='oui' ";
			if($avec_coef_devoir=="y"){echo "checked ";}
			echo "/><label for='avec_coef_devoir' style='cursor: pointer;'> Afficher les coefficients des devoirs si des coefficients différents sont présents.</label>\n";

			echo "<br />\n";
			echo "<input type='checkbox' name='avec_date_devoir' id='avec_date_devoir' value='oui' ";
			if($avec_date_devoir=="y"){echo "checked ";}
			echo "/><label for='avec_date_devoir' style='cursor: pointer;'> Afficher les dates des devoirs.</label>\n";
		}

		echo "<br />\n";

		echo "<input type='hidden' name='rn_sign_chefetab' value='n' />\n";
		echo "<input type='hidden' name='rn_sign_pp' value='n' />\n";
		echo "<input type='hidden' name='rn_sign_resp' value='n'/>\n";
		echo "<input type='hidden' name='rn_sign_nblig' value='$rn_sign_nblig' />\n";
		echo "<input type='hidden' name='rn_formule' value=\"$rn_formule\" />\n";
	}
	else{
		// Pour permettre de ne pas afficher les noms des devoirs
		echo "<br /><br /><br /><p><b>Options d'affichage : </b></p>\n";

		echo "\n<br />\n<input type='checkbox' name='avec_nom_devoir' id='avec_nom_devoir' value='oui' ";
		if($avec_nom_devoir=="y"){echo "checked ";}
		echo "/><label for='avec_nom_devoir' style='cursor: pointer;'> Afficher le nom des devoirs.</label>\n";

		echo "\n<br />\n<input type='checkbox' name='avec_appreciation_devoir' id='avec_appreciation_devoir' value='oui' ";
		if($avec_appreciation_devoir=="y"){echo "checked ";}
		echo "/><label for='avec_appreciation_devoir' style='cursor: pointer;'> Afficher les appréciations (lorsque le professeur a validé cette option).</label>\n";

		echo "<br />\n";
		echo "<input type='checkbox' name='avec_tous_coef_devoir' id='avec_tous_coef_devoir' value='oui' ";
		if($avec_tous_coef_devoir=="y"){echo "checked ";}
		echo "/><label for='avec_tous_coef_devoir' style='cursor: pointer;'> Afficher tous les coefficients des devoirs.</label>\n";

		echo "<br />\n";
		echo "<input type='checkbox' name='avec_coef_devoir' id='avec_coef_devoir' value='oui' ";
		if($avec_coef_devoir=="y"){echo "checked ";}
		echo "/><label for='avec_coef_devoir' style='cursor: pointer;'> Afficher les coefficients des devoirs si des coefficients différents sont présents.</label>\n";

		echo "<br />\n";
		echo "<input type='checkbox' name='avec_date_devoir' id='avec_date_devoir' value='oui' ";
		if($avec_date_devoir=="y"){echo "checked ";}
		echo "/><label for='avec_date_devoir' style='cursor: pointer;'> Afficher les dates des devoirs.</label>\n";

		echo "<br />\n";
		echo "<input type='checkbox' name='rn_sign_chefetab' id='rn_sign_chefetab' value='y' ";
		if($rn_sign_chefetab=="y"){echo "checked ";}
		echo "/><label for='rn_sign_chefetab' style='cursor: pointer;'> Afficher une case pour la signature du chef d'établissement.</label>\n";

		echo "<br />\n";
		echo "<input type='checkbox' name='rn_sign_pp' id='rn_sign_pp' value='y' ";
		if($rn_sign_pp=="y"){echo "checked ";}
		echo "/><label for='rn_sign_pp' style='cursor: pointer;'> Afficher une case pour la signature du ".getSettingValue("gepi_prof_suivi").".</label>\n";

		echo "<br />\n";
		echo "<input type='checkbox' name='rn_sign_resp' id='rn_sign_resp' value='y' ";
		if($rn_sign_resp=="y"){echo "checked ";}
		echo "/><label for='rn_sign_resp' style='cursor: pointer;'> Afficher une case pour la signature des parents/responsables.</label>\n";

		echo "<br />\n";
		echo "<label for='rn_sign_nblig' style='cursor: pointer;'>Nombre de lignes pour la signature si une case est affichée: </label><input type='text' name='rn_sign_nblig' id='rn_sign_nblig' value='$rn_sign_nblig' size='2' onKeyDown=\"clavier_2(this.id,event,0,100);\" />\n";

		echo "<br />\n";
		echo "<label for='rn_formule' style='cursor: pointer;'>Formule à afficher en bas de page:</label><br /><input type='text' name='rn_formule' id='rn_formule' value=\"$rn_formule\" size='40' />\n";
	}
    //====================================================================

    if (!$current_group) {
        echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
    } else {
        echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
    }
    echo "<input type='hidden' name='display_entete' value='yes' />\n";

    echo "<br /><br /><center><input type='submit' value='Valider' /></center>\n";
    echo "</form>\n";

} else {

	// Si on arrive là, on va afficher des relevés. On fait tout un tas de vérifications de sécurité
	// pour s'assurer que personne n'est là illégalement.
    if (($_SESSION['statut']=='professeur') AND (getSettingValue("GepiAccesReleveProf")!="yes") AND (getSettingValue("GepiAccesReleveProfTousEleves")!="yes") AND (getSettingValue("GepiAccesReleveProfToutesClasses")!="yes") AND (getSettingValue("GepiAccesReleveProfP") != "yes")) {
        tentative_intrusion(3, "Tentative d'un professeur d'accéder aux relevés de notes sans y être autorisé, avec passage volontaire de paramètres à la page.");
        echo "Vous ne pouvez pas accéder à cette page.";
        require("../lib/footer.inc.php");
        die();
    } else if (
    	(($_SESSION['statut'] == 'scolarite') AND (getSettingValue("GepiAccesReleveScol") != "yes"))
    	 OR (($_SESSION['statut'] == 'cpe') AND (getSettingValue("GepiAccesReleveCpe") != "yes"))
    	 OR ($_SESSION['statut'] == 'responsable' AND getSettingValue("GepiAccesReleveParent") != "yes")
    	 OR ($_SESSION['statut'] == 'eleve' AND getSettingValue("GepiAccesReleveEleve") != "yes")) {
        tentative_intrusion(3, "Tentative d'un utilisateur d'accéder aux relevés de notes sans y être autorisé, avec passage volontaire de paramètres à la page.");
        echo "Vous ne pouvez pas accéder à cette page.";
        require("../lib/footer.inc.php");
        die();
    } else if (($_SESSION['statut']=='professeur')
    	AND (getSettingValue("GepiAccesReleveProfP") == "yes")
    	AND (getSettingValue("GepiAccesReleveProf") != "yes")
    	AND (getSettingValue("GepiAccesReleveProfTousEleves") != "yes")
    	AND (getSettingValue("GepiAccesReleveProfToutesClasses") != "yes")
    	) {
        $test_classe = sql_query1("SELECT distinct c.id FROM classes c, j_eleves_professeurs s, j_eleves_classes cc
        WHERE (
        s.professeur='" . $_SESSION['login'] . "' AND
        s.login = cc.login AND
        cc.id_classe = c.id AND
        c.id= '".$id_classe."'
        )");
        if ($test_classe == '-1') {
        	tentative_intrusion(3, "Tentative d'un professeur d'accéder aux relevés de notes sans y être autorisé, avec passage volontaire de paramètres à la page.");
            echo "Vous n'êtes pas ".getSettingValue("gepi_prof_suivi")." de cette classe ! Vous ne pouvez pas accéder à cette page.</body></html>\n";
            die();
        }

    } else if (($_SESSION['statut'] == "professeur") AND ((getSettingValue("GepiAccesReleveProf") == "yes") OR (getSettingValue("GepiAccesReleveProfTousEleves") == "yes") OR (getSettingValue("GepiAccesReleveProfToutesClasses") == "yes"))) {

        // On commence par regarder si on est dans le cas de la sélection d'un groupe un d'une classe
        if (!$current_group) {
            // Dans le cas d'une classe, on vérifie que l'accès est autorisé
            if (getSettingValue("GepiAccesReleveProfTousEleves") != "yes" AND getSettingValue("GepiAccesReleveProfToutesClasses") != "yes") {
                tentative_intrusion(3, "Tentative d'un professeur d'accéder aux relevés de notes d'élèves pour toutes la classe (alors qu'il n'est autorisé à voir que ses groupes), avec passage volontaire de paramètres à la page.");
                echo "Vous n'êtes pas autorisé à visualiser l'ensemble des élèves de cette classe ! Sélectionnez uniquement un groupe parmi ceux auxquels vous enseignez.</body></html>\n";
                die();
            } else {
            	if (getSettingValue("GepiAccesReleveProfToutesClasses") != "yes") {
	                // il a le droit de visualiser des classes. On vérifie s'il est bien professeur dans la classe demandée

	                $test_classe = sql_query1("SELECT DISTINCT jgc.id_classe FROM " .
	                        "j_groupes_professeurs jgp, j_groupes_classes jgc ".
	                        "WHERE (" .
	                        "jgc.id_classe = '". $id_classe . "' AND ".
	                        "jgp.login = '". $_SESSION['login'] . "' AND " .
	                        "jgc.id_groupe = jgp.id_groupe" .
	                        ")");
	                if ($test_classe == '-1') {
	                	tentative_intrusion(3, "Tentative d'un professeur d'accéder aux relevés de notes d'une classe où il n'enseigne pas, avec passage volontaire de paramètres à la page.");
	                    echo "Vous n'êtes pas professeur dans cette classe ! Vous ne pouvez pas accéder à cette page.\n";
	                    require("../lib/footer.inc.php");
	                	die();
	                }
            	}
            }
        } else {
        	// On est dans le cas d'un groupe, on s'assure que le prof est bien prof dans le groupe !
        	$test = mysql_num_rows(mysql_query("SELECT * FROM j_groupes_professeurs WHERE (login = '".$_SESSION['login']."' AND id_groupe = '".$id_groupe."')"));
            if ($test == "0") {
            	tentative_intrusion(3, "Tentative d'un professeur d'accéder aux relevés de notes d'un groupe où il n'enseigne pas, avec passage volontaire de paramètres à la page.");
                echo "Vous n'êtes pas professeur dans ce groupe ! Vous ne pouvez pas accéder à cette page.\n";
                require("../lib/footer.inc.php");
            	die();
            }
        }
    }

	if ($_SESSION['statut'] == "responsable" OR $_SESSION['statut'] == "eleve") {
		if ($choix_edit != "2") {
            tentative_intrusion(3, "Tentative d'un parent ou élève de visualiser des relevés de notes autrement que pour un élève unique.");
            echo "Vous n'êtes pas autorisé à utiliser ce mode de visualisation.\n";
            require("../lib/footer.inc.php");
        	die();
		}

		if ($_SESSION['statut'] == "eleve") {
			if (strtoupper($login_eleve) != strtoupper($_SESSION['login'])) {
	            tentative_intrusion(3, "Tentative d'un élève de visualiser les relevés de notes d'un autre élève.");
	            echo "Vous ne pouvez visualiser que vos relevés de notes.\n";
	            require("../lib/footer.inc.php");
	        	die();
			}
		}

		if ($_SESSION['statut'] == "responsable") {
			$test = mysql_query("SELECT count(e.login) " .
					"FROM eleves e, responsables2 re, resp_pers r " .
					"WHERE (" .
					"e.login = '" . $login_eleve . "' AND " .
					"e.ele_id = re.ele_id AND " .
					"re.pers_id = r.pers_id AND " .
					"r.login = '" . $_SESSION['login'] . "')");
			if (mysql_result($test, 0) == 0) {
	            tentative_intrusion(3, "Tentative d'un parent d'accès aux relevés de notes d'un élève dont il n'est pas responsable légal.");
	            echo "Vous ne pouvez visualiser que les relevés de notes des élèves pour lesquels vous êtes responsable légal.\n";
	            require("../lib/footer.inc.php");
	        	die();
			}
		}
	}

    // Affichage du relevé de notes

    if (ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $_POST['display_date_debut'])) {
        $anneed = substr($_POST['display_date_debut'],6,4);
        $moisd = substr($_POST['display_date_debut'],3,2);
        $jourd = substr($_POST['display_date_debut'],0,2);

		//=========================
		// AJOUT: boireaus 20071118
		$_SESSION['display_date_debut']=$_POST['display_date_debut'];
		// Pour éviter de refaire le choix des dates en changeant de classe, on utilise la SESSION...
		//=========================
    } else {
        $anneed = strftime("%Y");
        $moisd = strftime("%m");
        $jourd = strftime("%d");
    }

    if (ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $_POST['display_date_fin'])) {
        $anneef= substr($_POST['display_date_fin'],6,4);
        $moisf= substr($_POST['display_date_fin'],3,2);
        $jourf = substr($_POST['display_date_fin'],0,2);

		//=========================
		// AJOUT: boireaus 20071118
		$_SESSION['display_date_fin']=$_POST['display_date_fin'];
		// Pour éviter de refaire le choix des dates en changeant de classe, on utilise la SESSION...
		//=========================
    } else {
        $anneef = strftime("%Y");
        $moisf = strftime("%m");
        $jourf = strftime("%d");
    }


    if ($choix_edit == '2') {
        releve_notes($login_eleve,$nb_periode,$anneed,$moisd,$jourd,$anneef,$moisf,$jourf);
    }

    if ($choix_edit != '2') {
        if ($choix_edit == '1') {
            if ($current_group) {
                $appel_liste_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_groupes jeg WHERE (jeg.id_groupe='$id_groupe' AND e.login = jeg.login) ORDER BY e.nom,e.prenom");
            } else {
                $appel_liste_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE (jec.id_classe='$id_classe' AND e.login = jec.login) ORDER BY e.nom,e.prenom");
            }
        } else {
            $appel_liste_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c, j_eleves_professeurs p WHERE (c.id_classe='$id_classe' AND e.login = c.login AND p.login=c.login AND p.professeur='$login_prof') ORDER BY e.nom,e.prenom");
        }
        $nombre_eleves = mysql_num_rows($appel_liste_eleves);
        $i=0;
        while ($i < $nombre_eleves) {
            $current_eleve_login = mysql_result($appel_liste_eleves, $i, "login");

            releve_notes($current_eleve_login,$nb_periode,$anneed,$moisd,$jourd,$anneef,$moisf,$jourf);
            if ($i != $nombre_eleves-1) {echo "<p class=saut>&nbsp;</p>\n";}
            $i++;
        }

    }

}
require("../lib/footer.inc.php");
?>
