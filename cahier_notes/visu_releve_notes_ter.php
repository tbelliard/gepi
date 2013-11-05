<?php
/*
*
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau, Christian Chapel
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

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_notes/visu_releve_notes_ter.php';";
$res_test=mysql_query($sql);
if (mysql_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/cahier_notes/visu_releve_notes_ter.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F','F', 'Relevé de notes : accès parents et élèves', '1');";
	$res_insert=mysql_query($sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$nom_cc=getSettingValue('nom_cc');
if($nom_cc=='') {
	$nom_cc="evaluation-cumul";
}

/*
if(($_SESSION['statut']=='autre')&&(!acces("/cahier_notes/visu_releve_notes_ter.php", $_SESSION['statut']))) {
	header("Location: ../accueil.php?msg=Acces_non_autorise");
	die();
}
*/
$contexte_document_produit="releve_notes";

$releve_pdf_debug=isset($_POST['releve_pdf_debug']) ? $_POST['releve_pdf_debug'] : "n";

//$mode_bulletin=isset($_POST['mode_bulletin']) ? $_POST['mode_bulletin'] : (isset($_GET['mode_bulletin']) ? $_GET['mode_bulletin'] : NULL);

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);
$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL);

$mode_bulletin="html";

//====================================================
//=============== ENTETE STANDARD ====================

// Tant qu'on ne propose pas le relevé PDF, on commente ça

//if(!isset($mode_bulletin)) {
	//**************** EN-TETE *********************
	$titre_page = "Visualisation relevé de notes";
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE *****************
//}
//============== FIN ENTETE STANDARD =================
//====================================================
//============== ENTETE BULLETIN HTML ================
//else
if ((isset($mode_bulletin))&&($mode_bulletin=='html')) {
	include("header_releve_html.php");
	//debug_var();
}
//============ FIN ENTETE BULLETIN HTML ==============
//====================================================
//============== ENTETE BULLETIN PDF ================
elseif ((isset($mode_bulletin))&&($mode_bulletin=='pdf')) {
	if($releve_pdf_debug=='y') {
		echo "<p style='color:red'>DEBUG:<br />
La génération du PDF va échouer parce qu'on affiche ces informations de debuggage,<br />
mais il se peut que vous ayez ainsi des précisions sur ce qui pose problème.<br />
</p>\n";
	}

	include("../bulletin/header_bulletin_pdf.php");
	include("../bulletin/header_releve_pdf.php");
}
//============ FIN ENTETE BULLETIN HTML ==============
//====================================================

//echo "microtime()=".microtime()."<br />";
//echo "time()=".time()."<br />";

$debug="n";
$tab_instant=array();
include("visu_releve_notes_func.lib.php");

//=========================

echo "<p class='bold'>";
echo "<a href='../accueil.php'>Retour à l'accueil</a>";
echo " | <a href='visu_releve_notes_bis.php' title=\"Par opposition à l'interface simplifiée propsoée ici.\">Interface classique</a>";

// l'élève a-t-il des évaluations cumules

if ($ele_login) {
    $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbDb);
/* Modification du jeu de résultats en utf8 */
    if (!$mysqli->set_charset("utf8")) {
        printf("Erreur lors du chargement du jeu de caractères utf8 : %s\n", $mysqli->error);
    }
    
    $result = $mysqli->query("SELECT 1=1 FROM `cc_notes_eval` WHERE login ='".$ele_login."'");
    if ($result->num_rows) {
        echo " | <a href='visu_cc_elv.php' title=\"\">$nom_cc</a>";
    }
}


if($_SESSION['statut']=='eleve') {
	echo "</p>\n";

	$ele_login=$_SESSION['login'];

	if(getSettingValue("GepiAccesReleveEleve") != "yes") {
		echo "<p>Vous n'êtes pas autorisé à accéder aux relevés de notes.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (jec.id_classe=c.id AND jec.login='".$_SESSION['login']."');";
	$test_ele_clas=mysql_query($sql);
	if(mysql_num_rows($test_ele_clas)==0) {
		echo "<p>Vous n'êtes pas affecté dans une classe et donc pas autorisé à accéder aux relevés de notes.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<p>Pour quelle période souhaitez-vous afficher vos notes&nbsp;?</p>\n";
	while ($lig_clas=mysql_fetch_object($test_ele_clas)) {
		if(mysql_num_rows($test_ele_clas)==1) {
			$id_classe=$lig_clas->id;
		}

		echo "<p><strong>$lig_clas->classe (<em>$lig_clas->nom_complet</em>)&nbsp;:</strong> ";
		$sql="SELECT * FROM periodes WHERE id_classe='".$lig_clas->id."' ORDER BY num_periode;";
		$res_per=mysql_query($sql);
		if(mysql_num_rows($res_per)==0) {
			echo " <span style='color:red'>Aucune période???</span>";
		}
		else {
			$cpt_per=0;
			while($lig_per=mysql_fetch_object($res_per)) {
				if($cpt_per>0) {echo " - ";}
				echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id."&amp;num_periode=".$lig_per->num_periode."&amp;mode_bulletin=html'>$lig_per->nom_periode</a>";
				$cpt_per++;
			}
		}
		echo "</p>\n";
	}

	if(!isset($num_periode)) {
		// Récupérer la période courante
		//$num_periode=1;
		$num_periode=cherche_periode_courante($id_classe, time(), 1);
	}
}
elseif($_SESSION['statut']=='responsable') {
	if(getSettingValue("GepiAccesReleveParent") != "yes") {
		echo "<p>Vous n'êtes pas autorisé à accéder aux relevés de notes.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	// Récupérer la liste des élèves

	$sql="(SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec, responsables2 r, resp_pers rp
			WHERE (e.ele_id=r.ele_id AND
					r.pers_id=rp.pers_id AND
					rp.login='".$_SESSION['login']."' AND
					(r.resp_legal='1' OR r.resp_legal='2') AND jec.login=e.login) ORDER BY e.naissance)";
	if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
		$sql.=" UNION (SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec, responsables2 r, resp_pers rp
			WHERE (e.ele_id=r.ele_id AND
					r.pers_id=rp.pers_id AND
					rp.login='".$_SESSION['login']."' AND
					r.resp_legal='0' AND r.acces_sp='y' AND jec.login=e.login) ORDER BY e.naissance)";
	}
	$sql.=";";
	$res_ele=mysql_query($sql);
	if(mysql_num_rows($res_ele)==0) {
		echo "</p>\n";
		echo "<p style='color:red'>Vous n'êtes responsable d'aucun élève???</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$tab_ele=array();
	$tab_login=array();
	$cpt_ele=0;
	while($lig_ele=mysql_fetch_object($res_ele)) {
		$tab_ele[$cpt_ele]['login']=$lig_ele->login;
		$tab_ele[$cpt_ele]['nom_prenom']=casse_mot($lig_ele->nom, 'maj')." ".casse_mot($lig_ele->prenom, 'majf2');
		$tab_login[]=$lig_ele->login;
		$cpt_ele++;
	}

	if($cpt_ele==1) {
		$ele_login=$tab_login[0];
	}

	if(isset($ele_login)) {
		$indice_eleve_courant="";
		// Lien vers autre élève
		for($loop=0;$loop<count($tab_ele);$loop++) {
			if($tab_ele[$loop]['login']!=$ele_login) {
				echo " | <a href='".$_SERVER['PHP_SELF']."?ele_login=".$tab_ele[$loop]['login']."'>".$tab_ele[$loop]['nom_prenom']."</a>";
			}
			else {
				$indice_eleve_courant=$loop;
				//echo "\$indice_eleve_courant=$indice_eleve_courant<br />";
			}
		}
		echo "</p>\n";

		if(!in_array($ele_login, $tab_login)) {
			echo "<p style='color:red'>Vous n'êtes pas responsable de '".$ele_login."'.</p>\n";
			// AJOUTER tentative_intrusion()
			require("../lib/footer.inc.php");
			die();
		}

		// L'indice 0 est assimilé à ""
		//if("$indice_eleve_courant"=="") {
		if($indice_eleve_courant==="") {
			echo "<p style='color:red'>Anomalie&nbsp;: L'élève '".$ele_login."' n'a pas été trouvé???</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<p class='bold'>".$tab_ele[$indice_eleve_courant]['nom_prenom']."</p>\n";

		// Liste des classes/périodes
		$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (jec.id_classe=c.id AND jec.login='".$ele_login."');";
		$test_ele_clas=mysql_query($sql);
		if(mysql_num_rows($test_ele_clas)==0) {
			echo "<p>".$tab_ele[$indice_eleve_courant]['nom_prenom']." n'est affecté dans aucune classe???</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<p>Pour quelle période souhaitez-vous afficher les notes&nbsp;?</p>\n";
		while ($lig_clas=mysql_fetch_object($test_ele_clas)) {
			if(mysql_num_rows($test_ele_clas)==1) {
				$id_classe=$lig_clas->id;
			}

			echo "<p><strong>$lig_clas->classe (<em>$lig_clas->nom_complet</em>)&nbsp;:</strong> ";
			$sql="SELECT * FROM periodes WHERE id_classe='".$lig_clas->id."' ORDER BY num_periode;";
			$res_per=mysql_query($sql);
			if(mysql_num_rows($res_per)==0) {
				echo " <span style='color:red'>Aucune période???</span>";
			}
			else {
				$cpt_per=0;
				while($lig_per=mysql_fetch_object($res_per)) {
					if($cpt_per>0) {echo " - ";}
					echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;id_classe=".$lig_clas->id."&amp;num_periode=".$lig_per->num_periode."&amp;mode_bulletin=html'>$lig_per->nom_periode</a>";
					$cpt_per++;
				}
			}
			echo "</p>\n";
		}

		if(!isset($num_periode)) {
			//$num_periode=1;
			$num_periode=cherche_periode_courante($id_classe, time(), 1);
		}
	}
	else {
		echo "</p>\n";

		echo "<p>Pour quel élève voulez-vous consulter les relevés de notes&nbsp;?\n</p>
<ul>\n";

		for($loop=0;$loop<count($tab_ele);$loop++) {
			if($tab_ele[$loop]['login']!=$ele_login) {
				echo "	<li><a href='".$_SERVER['PHP_SELF']."?ele_login=".$tab_ele[$loop]['login']."'>".$tab_ele[$loop]['nom_prenom']."</a></li>\n";
			}
		}
		echo "</ul>\n";

		require("../lib/footer.inc.php");
		die();
	}
}
//=========================

if((!isset($id_classe))||(!isset($num_periode))||(!isset($ele_login))) {
	require("../lib/footer.inc.php");
	die();
}

// Pour ne pas remettre un deuxième entête HTML dans header_releve_html.php
$sans_header_html="y";

$rn_couleurs_alternees="y";

$un_seul_bull_par_famille="oui";
$deux_releves_par_page="non";

$tab_id_classe[]=$id_classe;

$tab_periode_num[]=$num_periode;
$choix_periode=$num_periode;

//tab_selection_ele_".$i."_".$j."[]
$tab_selection_ele_0_0[]=$ele_login;

$tri_par_etab_orig="n";

$use_cell_ajustee="y";
$releve_pdf_debug='y';

// Pour mémoriser le temps de la session ces paramètres
$_SESSION['pref_use_cell_ajustee']=$use_cell_ajustee;
$_SESSION['pref_un_seul_bull_par_famille']=$un_seul_bull_par_famille;
$_SESSION['pref_deux_releves_par_page']=$deux_releves_par_page;
$_SESSION['pref_tri_par_etab_orig']=$tri_par_etab_orig;

// Prof principal
$gepi_prof_suivi=getSettingValue("gepi_prof_suivi");

// Initialisation:
unset($tab_rn_nomdev);
unset($tab_rn_toutcoefdev);
unset($tab_rn_coefdev_si_diff);
unset($tab_rn_datedev);
unset($tab_rn_app);
unset($tab_rn_sign_chefetab);
unset($tab_rn_sign_pp);
unset($tab_rn_sign_resp);
unset($tab_rn_sign_nblig);
unset($tab_rn_formule);
unset($tab_rn_adr_resp);
unset($tab_rn_bloc_obs);
unset($tab_rn_bloc_abs2);
unset($tab_rn_aff_classe_nom);
unset($tab_rn_moy_min_max_classe);
unset($tab_rn_moy_classe);
unset($tab_rn_retour_ligne);
unset($tab_rn_rapport_standard_min_font);
unset($chaine_coef);

// On force des valeurs:
// ********************************
// **          A FAIRE           **
// ********************************
// A MODULER EN FONCTION DE L'ACCES OU NON DONNé DANS droits d'accès
$rn_aff_classe_nom[0]=3;
$tab_rn_nomdev[0]="y";
$tab_rn_toutcoefdev[0]="n";
$tab_rn_coefdev_si_diff[0]="y";
$tab_rn_datedev[0]="y";
$tab_rn_app[0]="y";
$tab_rn_sign_chefetab[0]="n";
$tab_rn_sign_pp[0]="n";
$tab_rn_sign_resp[0]="n";

$tab_rn_sign_nblig[0]="";
$tab_rn_formule[0]="";

$tab_rn_adr_resp[0]="n";

// Bloc observation sur la droite pour le relevé PDF:
$tab_rn_bloc_obs[0]="n";
$tab_rn_bloc_abs2[0]="n";

//echo getSettingValue('GepiAccesColMoyReleveEleve');
if(($_SESSION['statut']=='eleve')&&(getSettingAOui('GepiAccesColMoyReleveEleve'))||
($_SESSION['statut']=='responsable')&&(getSettingAOui('GepiAccesColMoyReleveParent'))) {
	$tab_rn_col_moy[0]="y";
}
else {
	$tab_rn_col_moy[0]="n";
}
//echo $tab_rn_col_moy[0];

if(($_SESSION['statut']=='eleve')&&(getSettingAOui('GepiAccesMoyMinClasseMaxReleveEleve'))||
($_SESSION['statut']=='responsable')&&(getSettingAOui('GepiAccesMoyMinClasseMaxReleveParent'))) {
	$tab_rn_moy_min_max_classe[0]="y";
	$tab_rn_moy_classe[0]="n";
}
elseif(($_SESSION['statut']=='eleve')&&(getSettingAOui('GepiAccesMoyClasseReleveEleve'))||
($_SESSION['statut']=='responsable')&&(getSettingAOui('GepiAccesMoyClasseReleveParent'))) {
	$tab_rn_moy_min_max_classe[0]="n";
	$tab_rn_moy_classe[0]="y";
}
else {
	$tab_rn_moy_min_max_classe[0]="n";
	$tab_rn_moy_classe[0]="n";
}

$tab_rn_retour_ligne[0]="y";
$tab_rn_rapport_standard_min_font[0]=3;

$tab_rn_sign_chefetab[0]="n";
$tab_rn_sign_pp[0]="n";
$tab_rn_sign_resp[0]="n";

$chaine_coef="coef:";

//========================================
// Extraction des données externalisée pour permettre un appel depuis la génération de bulletins de façon à intercaler les relevés de notes entre les bulletins
include("extraction_donnees_releves_notes.php");
//========================================

//========================================================================
// A CE STADE LE TABLEAU $tab_releve EST RENSEIGNé
// PLUS AUCUNE REQUETE NE DEVRAIT ETRE NECESSAIRE
// OU ALORS IL FAUDRAIT LES EFFECTUER AU-DESSUS ET COMPLETER $tab_releve
//
// IL Y AURA A RENSEIGNER $tab_releve[$id_classe][$periode_num]['modele_pdf']
// SI ON FAIT UNE IMPRESSION DE RELEVE PDF, POUR NE PAS REFAIRE LES REQUETES
// POUR CHAQUE ELEVE.
//========================================================================

function regime($id_reg) {
	switch($id_reg) {
		case "d/p":
			$regime="demi-pensionnaire";
			break;
		case "ext.":
			$regime="externe";
			break;
		case "int.":
			$regime="interne";
			break;
		case "i-e":
			$regime="interne-externé";
			break;
		default:
			$regime="Régime inconnu???";
			break;
	}

	return $regime;
}

// Compteur pour gérer les 2 relevés par page en PDF
$compteur_releve=0;
// Compteur pour les insertions de saut de page en HTML
$compteur_releve_bis=0;
// Initialisation pour récup global dans releve_html() et signalement ensuite s'il s'agit de deux relevés pour des parents séparés
$nb_releves=1;
for($loop_classe=0;$loop_classe<count($tab_id_classe);$loop_classe++) {
	$id_classe=$tab_id_classe[$loop_classe];
	$classe=get_class_from_id($id_classe);

	for($loop_periode_num=0;$loop_periode_num<count($tab_periode_num);$loop_periode_num++) {

		$periode_num=$tab_periode_num[$loop_periode_num];

		//$compteur_releve=0;
		if(isset($tab_releve[$id_classe][$periode_num]['eleve'])) {

			unset($tmp_tab);
			unset($rg);
			//$tri_par_etab_orig="y";
			if($tri_par_etab_orig=='y') {
				for($k=0;$k<count($tab_releve[$id_classe][$periode_num]['eleve']);$k++) {
					$rg[$k]=$k;
					$tmp_tab[$k]=$tab_releve[$id_classe][$periode_num]['eleve'][$k]['etab_id'];
				}
				array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);
			}

			for($i=0;$i<count($tab_releve[$id_classe][$periode_num]['eleve']);$i++) {
				if($tri_par_etab_orig=='n') {$rg[$i]=$i;}

				if(isset($tab_releve[$id_classe][$periode_num]['selection_eleves'])) {
					//if (in_array($tab_releve[$id_classe][$periode_num]['eleve'][$i]['login'],$tab_releve[$id_classe][$periode_num]['selection_eleves'])) {

						//+++++++++++++++++++++++++++++++++++
						//===============================================
						
						$autorisation_acces='y';
						//===============================================
						//+++++++++++++++++++++++++++++++++++

						if($autorisation_acces=='y') {
							if($mode_bulletin!="pdf") {

								// Saut de page si jamais ce n'est pas le premier bulletin
								//if($compteur_releve>0) {echo "<p class='saut'>&nbsp;</p>\n";}
								if($compteur_releve_bis>0) {echo "<p class='saut'>&nbsp;</p>\n";}

								// Génération du bulletin de l'élève
								releve_html($tab_releve[$id_classe][$periode_num],$rg[$i],-1);
							}
							else {
								// Relevé PDF

								// Génération du relevé PDF de l'élève
								releve_pdf($tab_releve[$id_classe][$periode_num],$rg[$i]);
							}

							$compteur_releve_bis++;
						}
				}
			}
		}
	}
}

/*
if($mode_bulletin=="pdf") {
	// Envoyer le PDF et quitter
	$nom_releve = date("Ymd_Hi");
	$nom_fichier = 'releve_notes_'.$nom_releve.'.pdf';

	if(((isset($bull_pdf_debug))&&($bull_pdf_debug=='y'))||((isset($releve_pdf_debug))&&($releve_pdf_debug=='y'))) {
		echo $pdf->Output($nom_fichier,'S');
	}
	else {
		$pdf->Output($nom_fichier,'I');
	}

	die();
}
*/

require("../lib/footer.inc.php");
?>
