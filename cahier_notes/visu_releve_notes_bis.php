<?php
/*
*
*
* Copyright 2001, 2021 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau, Christian Chapel, Stephane Boireau
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


$sql="SELECT 1=1 FROM droits WHERE id='/cahier_notes/visu_releve_notes_bis.php';";
$res_test=mysqli_query($GLOBALS["mysqli"], $sql);
if (mysqli_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/cahier_notes/visu_releve_notes_bis.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V','F', 'Relevé de notes', '1');";
	$res_insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(($_SESSION['statut']=='autre')&&(!acces("/cahier_notes/visu_releve_notes_bis.php", $_SESSION['statut']))) {
	header("Location: ../accueil.php?msg=Acces_non_autorise");
	die();
}

// 20241019
$GLOBALS['dont_get_modalite_elect']=true;

//debug_var();

//================================

//+++++++++++++++++++++++++
// A FAIRE:
// Ajouter un témoin pour ne pas générer d'affichage pouvant empêcher la génération de relevé PDF...
//+++++++++++++++++++++++++

$contexte_document_produit="releve_notes";

$releve_pdf_debug=isset($_POST['releve_pdf_debug']) ? $_POST['releve_pdf_debug'] : "n";

$choix_parametres=isset($_POST['choix_parametres']) ? $_POST['choix_parametres'] : (isset($_GET['choix_parametres']) ? $_GET['choix_parametres'] : NULL);
$mode_bulletin=isset($_POST['mode_bulletin']) ? $_POST['mode_bulletin'] : (isset($_GET['mode_bulletin']) ? $_GET['mode_bulletin'] : NULL);

//====================================================
//=============== ENTETE STANDARD ====================
if(!isset($choix_parametres)) {
	$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
	$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
	$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
	$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

	//**************** EN-TETE *********************
	$titre_page = "Visualisation relevé de notes";
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE *****************
}
//============== FIN ENTETE STANDARD =================
//====================================================
//============== ENTETE RELEVE PAR MAIL ================
// 20191012
elseif(isset($_POST['envoi_rel_par_mail'])) {
	// L'entête est généré plus bas
}
//====================================================
//============== ENTETE BULLETIN HTML ================
elseif ((isset($mode_bulletin))&&($mode_bulletin=='html')) {
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
// Classes sélectionnées:
$tab_id_classe=isset($_POST['tab_id_classe']) ? $_POST['tab_id_classe'] : (isset($_GET['tab_id_classe']) ? $_GET['tab_id_classe'] : NULL);

// Période:
// $choix_periode='periode' ou 'intervalle'
$choix_periode=isset($_POST['choix_periode']) ? $_POST['choix_periode'] : (isset($_GET['choix_periode']) ? $_GET['choix_periode'] : NULL);
// Si $choix_periode='periode'
//$periode=isset($_POST['periode']) ? $_POST['periode'] : NULL;
$tab_periode_num=isset($_POST['tab_periode_num']) ? $_POST['tab_periode_num'] : (isset($_GET['tab_periode_num']) ? $_GET['tab_periode_num'] : NULL);
// Si $choix_periode='intervalle'
$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : NULL;
$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : NULL;

$valide_select_eleves=isset($_POST['valide_select_eleves']) ? $_POST['valide_select_eleves'] : (isset($_GET['valide_select_eleves']) ? $_GET['valide_select_eleves'] : NULL);

//$choix_parametres=isset($_POST['choix_parametres']) ? $_POST['choix_parametres'] : NULL;

// Un prof peut choisir un groupe plutôt qu'une liste de classes
$id_groupe=($_SESSION['statut']=='professeur') ? (isset($_POST['id_groupe']) ? $_POST['id_groupe'] : NULL) : NULL;
if(isset($id_groupe)) {
	// 20210301
	if((isset($id_groupe))&&(preg_match('/^[0-9]{1,}$/', $id_groupe))&&(is_groupe_exclu_tel_module($id_groupe, 'cahier_notes'))) {
		echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

		echo "<p style='color:red'>L'enseignement n°$id_groupe n'utilise pas les carnets de notes.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}


	if(($id_groupe=='')||(mb_strlen(my_ereg_replace("[0-9]","",$id_groupe))!=0)) {
		tentative_intrusion(2, "Tentative d'un professeur de manipuler l'identifiant id_groupe en y mettant des caractères non numériques ou un identifiant de groupe vide.");
		echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

		echo "<p style='color:red'>L'identifiant de groupe est erroné.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	// On vérifie si le prof est bien associé au groupe
	$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE id_groupe='$id_groupe' AND login='".$_SESSION['login']."';";
	$test_grp_prof=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_grp_prof)==0) {
		//intrusion
		tentative_intrusion(2, "Tentative d'un professeur d'accéder aux relevés de notes d'un groupe auquel il n'est pas associé.");
		echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

		echo "<p style='color:red'>Vous n'êtes pas associé au groupe choisi.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}
	//echo "id_groupe=$id_groupe<br />\n";
}
//=========================


//=========================
// A FAIRE

// CAS D'UN ELEVE
// - Préremplir la classe pour sauter l'étape? sauf s'il a changé de classe?
// - Permettre de choisir la période
// - Préremplir la sélection d'élèves pour sauter l'étape (en renseignant un champ caché) et en cas de manipulation (vider le champ) interdire l'accès élève dans la section de choix des élèves
// Contrôler avant et après validation des paramètres si les champs proposés/reçus sont corrects (conforme à ce qu'on autorise à l'élève)

// CAS D'UN RESPONSABLE
// Si un seul enfant, cf CAS D'UN ELEVE
// Si plusieurs, permettre de choisir l'enfant et remplir la classe en conséquence...
// ...


if($_SESSION['statut']=='eleve') {
	if(getSettingValue("GepiAccesReleveEleve") != "yes") {
		echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

		echo "<p style='color:red'>Vous n'êtes pas autorisé à accéder aux relevés de notes.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (jec.id_classe=c.id AND jec.login='".$_SESSION['login']."');";
	$test_ele_clas=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_ele_clas)==0) {
		echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

		echo "<p style='color:red'>Vous n'êtes pas affecté dans une classe et donc pas autorisé à accéder aux relevés de notes.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	elseif(mysqli_num_rows($test_ele_clas)==1) {
		// On préremplit la classe
		$lig_clas=mysqli_fetch_object($test_ele_clas);
		$tab_id_classe=array();
		$tab_id_classe[]=$lig_clas->id;
		//echo "\$lig_clas->id=$lig_clas->id<br />";
	}
	else {
		// C'est un élève qui a changé de classe en cours d'année.
		// Il faut laisser faire le choix de la classe ou des classes
	}
}
elseif($_SESSION['statut']=='responsable') {
	if(getSettingValue("GepiAccesReleveParent") != "yes") {
		echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

		echo "<p style='color:red'>Vous n'êtes pas autorisé à accéder aux relevés de notes.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT DISTINCT c.*  FROM eleves e, j_eleves_classes jec, classes c, responsables2 r, resp_pers rp
			WHERE (e.ele_id=r.ele_id AND
					r.pers_id=rp.pers_id AND
					rp.login='".$_SESSION['login']."' AND
					c.id=jec.id_classe AND
					jec.login=e.login);";
	$test_ele_clas=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_ele_clas)==0) {
		echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

		echo "<p style='color:red'>Aucun des élèves dont vous êtes responsable ne semble inscrit dans une classe; vous n'êtes donc pas autorisé à accéder aux relevés de notes.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	elseif(mysqli_num_rows($test_ele_clas)==1) {
		// On préremplit la classe
		$lig_clas=mysqli_fetch_object($test_ele_clas);
		$tab_id_classe=array();
		$tab_id_classe[]=$lig_clas->id;
		//echo "\$lig_clas->id=$lig_clas->id<br />";
	}
	else {
		// Le ou les enfants du responsable sont dans plusieurs classes ou l'élève a changé de classe
		// Il faut laisser faire le choix de la classe ou des classes
	}
}
//=========================

//======================================================
//==================CHOIX DES CLASSES===================
//if(!isset($tab_id_classe)) {
// On contrôle plus haut que $id_groupe=NULL si on n'est pas prof
if ((!isset($tab_id_classe))&&(!isset($id_groupe))) {
	echo "<p class='bold'>";
	if($_SESSION['statut']=='professeur') {
		echo "<a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
		if(getSettingAOui('GepiProfImprRelSettings')) {
			echo " | ";
			echo "<a href='param_releve_html.php' target='_blank'><img src='../images/icons/configure.png' class='icone16' alt='Paramétrer' /> Paramètres du relevé HTML</a>";
		}
	}
	elseif($_SESSION['statut']=='scolarite') {
		echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>";
		if(getSettingAOui('GepiScolImprRelSettings')) {
			echo " | ";
			echo "<a href='param_releve_html.php' target='_blank'><img src='../images/icons/configure.png' class='icone16' alt='Paramétrer' /> Paramètres du relevé HTML</a>";
		}
		//echo " | ";
		//echo "<a href='visu_releve_notes.php'>Ancien dispositif</a>";
	}
	elseif($_SESSION['statut']=='cpe') {
		echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>";
		if(getSettingAOui('GepiCpeImprRelSettings')) {
			echo " | ";
			echo "<a href='param_releve_html.php' target='_blank'><img src='../images/icons/configure.png' class='icone16' alt='Paramétrer' /> Paramètres du relevé HTML</a>";
		}
		//echo " | ";
		//echo "<a href='visu_releve_notes.php'>Ancien dispositif</a>";
	}
	elseif($_SESSION['statut']=='administrateur') {
		// Normalement, l'administrateur n'a pas accès aux relevés de notes...
		echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>";
		echo " | ";
		echo "<a href='param_releve_html.php' target='_blank'><img src='../images/icons/configure.png' class='icone16' alt='Paramétrer' /> Paramètres du relevé HTML</a>";
		//echo " | ";
		//echo "<a href='visu_releve_notes.php'>Ancien dispositif</a>";
	}
	else {
		echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>";
	}
	echo "</p>\n";

	echo "<p class='bold'>Choix des classes";
	if ($_SESSION['statut']=='professeur') {echo " ou d'un groupe";}
	echo ":</p>\n";

	//==============================
	// A REVOIR:
	// Laissé pour le moment, parce que le cas PROF n'a pas été traité:
	//$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE c.id=jec.id_classe ORDER BY c.classe;";
	// Pas plus que le cas Eleve ou Responsable

	// Et un prof doit pouvoir lister les relevés de notes associés à ses groupes...
	//==============================

	if (($_SESSION['statut'] == 'scolarite') AND (getSettingValue("GepiAccesReleveScol") == "yes")) {
		$sql="SELECT DISTINCT c.* FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif (($_SESSION['statut'] == 'cpe') AND (getSettingValue("GepiAccesReleveCpeTousEleves") == "yes")) {
		$sql="SELECT DISTINCT c.* FROM classes c ORDER BY classe";
	}
	elseif(($_SESSION['statut'] == 'cpe') AND (getSettingValue("GepiAccesReleveCpe") == "yes")) {
		$sql="SELECT DISTINCT c.* FROM classes c,
										j_eleves_classes jecl,
										j_eleves_cpe jec
								WHERE (
										c.id=jecl.id_classe AND
										jecl.login=jec.e_login AND
										jec.cpe_login='".$_SESSION['login']."'
									)
								ORDER BY classe";
		// A REVOIR:
		// Les droits ne sont pas corrects:
		// - on restreint scolarité à ses classes alors que GepiAccesReleveScol correspond dans Droits d'accès à Toutes les classes
		// - on ne restreint pas le CPE de semblable façon aux élèves dont il est responsable -> CORRIGé
	}
	elseif($_SESSION['statut']=='professeur') {
		// GepiAccesReleveProf               -> que les élèves de ses groupes
		// GepiAccesReleveProfTousEleves     -> tous les élèves de ses classes
		// GepiAccesReleveProfToutesClasses  -> tous les élèves de toutes les classes


		// GepiAccesReleveProfToutesClasses  -> tous les élèves de toutes les classes
		if(getSettingValue("GepiAccesReleveProfToutesClasses") == "yes") {
			$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE c.id=jec.id_classe ORDER BY c.classe;";
		}
		elseif ((getSettingValue("GepiAccesReleveProf") == "yes")||(getSettingValue("GepiAccesReleveProfTousEleves") == "yes")) {
			// A ce stade on ne récupère pas les élèves, mais seulement les classes:
			// GepiAccesReleveProf               -> que les élèves de ses groupes
			// GepiAccesReleveProfTousEleves     -> tous les élèves de ses classes
			$sql="SELECT DISTINCT c.* FROM j_groupes_classes jgc,
											j_groupes_professeurs jgp,
											classes c
									WHERE (
										c.id=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='".$_SESSION['login']."'
										)
									ORDER BY c.classe;";
		}
		elseif(getSettingValue("GepiAccesReleveProfP")=="yes") {
			$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."' LIMIT 1;";
			$test_acces=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test_acces)>0) {
				$sql="SELECT DISTINCT c.* FROM j_eleves_professeurs jep,
											classes c
									WHERE (
										c.id=jep.id_classe AND
										jep.professeur='".$_SESSION['login']."'
										)
									ORDER BY c.classe;";
			}
			else {
				echo "<p>Vous n'êtes pas ".getSettingValue("gepi_prof_suivi").", donc pas autorisé à accéder aux relevés de notes des élèves.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}
		else {
			echo "<p>Vous n'êtes pas autorisé à accéder aux relevés de notes des élèves.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
	elseif(($_SESSION['statut']=='eleve')&&(getSettingValue("GepiAccesReleveEleve") == "yes")) {
		// Un élève qui change de classe peut avoir plusieurs classes où retrouver ses notes
		$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe AND jec.login='".$_SESSION['login']."') ORDER BY c.classe;";
	}
	elseif(($_SESSION['statut']=='responsable')&&(getSettingValue("GepiAccesReleveParent") == "yes")) {
		$sql="(SELECT DISTINCT c.* FROM eleves e, j_eleves_classes jec, classes c, responsables2 r, resp_pers rp
				WHERE (e.ele_id=r.ele_id AND
						r.pers_id=rp.pers_id AND
						rp.login='".$_SESSION['login']."' AND
						(r.resp_legal='1' OR r.resp_legal='2') AND
						c.id=jec.id_classe AND
						jec.login=e.login))";
		if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
			$sql.=" UNION (SELECT DISTINCT c.* FROM eleves e, j_eleves_classes jec, classes c, responsables2 r, resp_pers rp
				WHERE (e.ele_id=r.ele_id AND
						r.pers_id=rp.pers_id AND
						rp.login='".$_SESSION['login']."' AND
						r.resp_legal='0' AND
						r.acces_sp='y' AND 
						c.id=jec.id_classe AND
						jec.login=e.login))";
		}
		$sql.=";";

	}
	elseif($_SESSION['statut']=='autre') {
		$sql="SELECT DISTINCT c.* FROM classes c ORDER BY classe";
	}
	else {
		echo "<p>Vous n'êtes pas autorisé à accéder aux relevés de notes des élèves.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	//echo "$sql<br />";
	$call_classes=mysqli_query($GLOBALS["mysqli"], $sql);

	$nb_classes=mysqli_num_rows($call_classes);
	if($nb_classes==0) {
		if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
			echo "<p>Aucun carnet de notes n'a été trouvé.</p>\n";
		}
		else {
			echo "<p>Aucune classe avec élève affecté n'a été trouvée.</p>\n";
		}
		require("../lib/footer.inc.php");
		die();
	}

	// 20210301
	$tab_id_classe_exclues_module_cahier_notes=get_classes_exclues_tel_module('cahier_notes');
	$tab_classe=array();
	$cpt=0;
	while($lig_clas=mysqli_fetch_assoc($call_classes)) {
		if(!in_array($lig_clas['id'], $tab_id_classe_exclues_module_cahier_notes)) {
			$tab_classe[$cpt]=$lig_clas;
			$cpt++;
		}
	}

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' id='formulaire'>\n";

	if(count($tab_classe)==0) {
		echo "<p>Aucune classe avec carnet de notes n'a été trouvé.</p>\n";
	}
	else {

		// Affichage sur 3 colonnes
		//$nb_classes_par_colonne=round($nb_classes/3);
		$nb_classes_par_colonne=round(count($tab_classe)/3);

		echo "<table style='width:100%'>\n";
		echo "<caption class='invisible'>Choix des classes</caption>\n";
		echo "<tr style='vertical-align:top;'>\n";

		$cpt = 0;

		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td>\n";

		foreach($tab_classe as $current_classe) {

			//affichage 2 colonnes
			if(($cpt>0)&&(round($cpt/$nb_classes_par_colonne)==$cpt/$nb_classes_par_colonne)){
				echo "</td>\n";
				echo "<td>\n";
			}

			echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='tab_id_classe[]' id='tab_id_classe_$cpt' value='".$current_classe['id']."' onchange='unCheckRadio();change_style_classe($cpt)' /> ".$current_classe['classe']."</label>";
			echo "<br />\n";
			$cpt++;
		}

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "<script type='text/javascript'>
		//<![CDATA[
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
		////]]>
	</script>\n";

		echo "<p><a href='#' onclick='ModifCase(true)'>Tout cocher</a> / <a href='#' onclick='ModifCase(false)'>Tout décocher</a></p>\n";
	}

	$nb_grp_prof=0;
	if(($_SESSION['statut']=='professeur')&&((getSettingValue("GepiAccesReleveProfToutesClasses") == "yes")||(getSettingValue("GepiAccesReleveProf") == "yes")||(getSettingValue("GepiAccesReleveProfTousEleves") == "yes"))) {
		echo "<p><strong>Alternativement</strong>, vous pouvez choisir un groupe:</p>\n";

		$groupes_prof=get_groups_for_prof($_SESSION['login']);
		$nb_grp_prof=count($groupes_prof);

		echo "<p>\n";
		for($i=0;$i<$nb_grp_prof;$i++) {
			if(!is_groupe_exclu_tel_module($groupes_prof[$i]['id'], 'cahier_notes')) {
				echo "<input type='radio' name='id_groupe' id='id_groupe_".$i."' value='".$groupes_prof[$i]['id']."' /> ";
				echo "<label for='id_groupe_".$i."' style='cursor: pointer;'>\n";
				echo htmlspecialchars($groupes_prof[$i]['name']);
				echo "(<em>";
				if($groupes_prof[$i]['name']!=$groupes_prof[$i]["matiere"]['nom_complet']) {echo htmlspecialchars($groupes_prof[$i]["matiere"]['nom_complet'])." en ";}
				echo htmlspecialchars($groupes_prof[$i]['classlist_string']);
				echo "</em>)";
				echo "</label>\n";
				echo "<br />\n";
			}
		}
		echo "</p>\n";
	}

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	//<![CDATA[
	function ModifCase(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('tab_id_classe_'+k)){
				document.getElementById('tab_id_classe_'+k).checked = mode;
				change_style_classe(k);
			}
		}

		if(mode==true) {unCheckRadio();}
	}

	function unCheckRadio() {
		for (var k=0;k<$nb_grp_prof;k++) {
			if(document.getElementById('id_groupe_'+k)){
				document.getElementById('id_groupe_'+k).checked = false;
				change_style_classe(k);
			}
		}
	}
	////]]>
</script>\n";


}
//======================================================
//=================CHOIX DE LA PERIODE==================
elseif(!isset($choix_periode)) {

	echo "<p class='bold'>";
	if($_SESSION['statut']=='professeur') {
		echo "<a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
	}
	elseif($_SESSION['statut']=='scolarite') {
		echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
		if(getSettingAOui('GepiScolImprRelSettings')) {
			echo " | ";
			echo "<a href='param_releve_html.php' target='_blank'><img src='../images/icons/configure.png' class='icone16' alt='Paramétrer' /> Paramètres du relevé HTML</a>";
		}
	}
	elseif($_SESSION['statut']=='administrateur') {
		// Normalement, l'administrateur n'a pas accès aux relevés de notes...
		// On ne devrait jamais passer là sauf modification de la politique des rôles.
		echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
		echo " | ";
		echo "<a href='param_releve_html.php' target='_blank'><img src='../images/icons/configure.png' class='icone16' alt='Paramétrer' /> Paramètres du relevé HTML</a>";
	}
	else {
		echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>";
	}
	echo "</p>\n";

	// Choisir les périodes permettant l'édition des relevés de notes

	if($_SESSION['statut']=="professeur") {
		if(isset($id_groupe)) {
			// On a fait un choix de groupe... on refait la liste des classes d'après l'id_groupe choisi
			unset($tab_id_classe);
			$sql="SELECT DISTINCT id_classe FROM j_groupes_classes WHERE id_groupe='$id_groupe';";
			$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_clas)==0) {
				echo "<p>ERREUR: Le groupe choisi ne semble associé à aucune classe???</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			while($lig_clas=mysqli_fetch_object($res_clas)) {
				$tab_id_classe[]=$lig_clas->id_classe;
			}
		}
	}

	echo "<p class='bold'>Choisissez la période d'affichage pour ";
	if(isset($id_groupe)) {
		$current_group=get_group($id_groupe);
		echo htmlspecialchars($current_group['name']);
		echo "(<em>";
		if($current_group['name']!=$current_group['matiere']['nom_complet']) {echo htmlspecialchars($current_group['matiere']['nom_complet'])." en ";}
		echo htmlspecialchars($current_group['classlist_string']);
		echo "</em>)";

	}
	else {
		//echo "la ou les classes ";
		if(count($tab_id_classe)==1) {
			echo "la classe ";
		}
		else {
			echo "les classes ";
		}
		for($i=0;$i<count($tab_id_classe);$i++) {
			if($i>0) {echo ", ";}
			echo get_class_from_id($tab_id_classe[$i]);
		}
	}
	echo ":</p>\n";
	//debug_var();
	//=======================
	//Configuration du calendrier
	/*
	include("../lib/calendrier/calendrier.class.php");
	//$cal1 = new Calendrier("form_choix_edit", "display_date_debut");
	//$cal2 = new Calendrier("form_choix_edit", "display_date_fin");
	$cal1 = new Calendrier("formulaire", "display_date_debut");
	$cal2 = new Calendrier("formulaire", "display_date_fin");
	*/
	//=======================

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' id='formulaire'>\n";
	echo "<table border='0' summary='Tableau de choix des périodes'>\n";
	echo "<tr valign='top'>\n";
	echo "<td>\n";
    echo "<a name=\"calend\"></a>";
	//echo "<input type=\"radio\" name=\"choix_periode\" id='choix_periode_dates' value=\"intervalle\" checked='checked '/>";
	echo "<input type=\"radio\" name=\"choix_periode\" id='choix_periode_dates' value=\"intervalle\" ";
	// Dans le cas d'un retour en arrière, le champ peut avoir été préalablement coché
	//if((!isset($choix_periode))||($choix_periode=="intervalle")) {
	if(!isset($tab_periode_num)) {
		echo "checked='checked' ";
	}
	echo "/>";
	echo "</td>\n";


	//=======================
	// Pour éviter de refaire le choix des dates en changeant de classe, on utilise la SESSION...
	$annee = strftime("%Y");
	$mois = strftime("%m");
	$jour = strftime("%d");

	if($mois>8) {$date_debut_tmp="01/09/$annee";} else {$date_debut_tmp="01/09/".($annee-1);}

	//$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : (isset($_SESSION['display_date_debut']) ? $_SESSION['display_date_debut'] : $jour."/".$mois."/".$annee);
	$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : (isset($_SESSION['display_date_debut']) ? $_SESSION['display_date_debut'] : $date_debut_tmp);

	$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : (isset($_SESSION['display_date_fin']) ? $_SESSION['display_date_fin'] : $jour."/".$mois."/".$annee);
	//=======================


	echo "<td>\n";
	echo "<label for='display_date_debut' style='cursor: pointer;'> \nDe la date : </label>";

    echo "<input type='text' name = 'display_date_debut' id = 'display_date_debut' size='10' value = \"".$display_date_debut."\" onfocus=\"document.getElementById('choix_periode_dates').checked=true;\" onkeydown=\"clavier_date(this.id,event);\" />";
    echo "<label for='display_date_fin' style='cursor: pointer;'>";
    //echo "<a href=\"#calend\" onclick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier début\" style=\"border:0;\" /></a>\n";
	echo img_calendrier_js("display_date_debut", "img_bouton_display_date_debut");

    echo "&nbsp;à la date : </label>";
    echo "<input type='text' name = 'display_date_fin' id = 'display_date_fin' size='10' value = \"".$display_date_fin."\" onfocus=\"document.getElementById('choix_periode_dates').checked=true;\" onkeydown=\"clavier_date(this.id,event);\" />";
    echo "<label for='choix_periode_dates' style='cursor: pointer;'>";
    //echo "<a href=\"#calend\" onclick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier fin\" style=\"border:0;\" /></a>\n";
	echo img_calendrier_js("display_date_fin", "img_bouton_display_date_fin");

	echo "<br />\n";
    echo " (<em>Veillez à respecter le format jj/mm/aaaa</em>)</label>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr valign='top'>\n";
	echo "<td colspan='2'>\n";
	echo "Ou";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr valign='top'>\n";
	echo "<td>\n";

	echo "<input type=\"radio\" name=\"choix_periode\" id='choix_periode' value='periode' ";
	// Dans le cas d'un retour en arrière, le champ peut avoir été préalablement coché
	//if((isset($choix_periode))&&($choix_periode=="periode")) {
	if(isset($tab_periode_num)) {
		echo "checked= 'checked'";
	}
	echo "/><label for='choix_periode' style='cursor: pointer;'> <strong>Période</strong></label>\n";

	echo "</td>\n";
	echo "<td>\n";

		$sql="SELECT MAX(num_periode) max_per FROM periodes;";
		$res_max_per=mysqli_query($GLOBALS["mysqli"], $sql);
		$lig_max_per=mysqli_fetch_object($res_max_per);
		$max_per=$lig_max_per->max_per;

		$tab_periode_exclue=array();

		$tab_nom_periode=array();

		echo "<table class='boireaus' border='1' summary='Tableau de choix des périodes'>\n";
		echo "<tr>\n";
		echo "<th>Classe</th>\n";
		for($j=1;$j<=$max_per;$j++) {
			echo "<th>Période $j</th>\n";
			$date_1ere_evaluation[$j]="9999-12-31 00:00:00";
			$date_derniere_evaluation[$j]="0000-00-00 00:00:00";
		}
		echo "</tr>\n";
		$alt=1;
		for($i=0;$i<count($tab_id_classe);$i++) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>".get_class_from_id($tab_id_classe[$i])."\n";
			echo "<input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";
			echo "</td>\n";
			for($j=1;$j<=$max_per;$j++) {
				if(!isset($tab_nom_periode[$j])) {$tab_nom_periode[$j]=array();}

				$sql="SELECT * FROM periodes WHERE num_periode='$j' AND id_classe='".$tab_id_classe[$i]."';";
				$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_per)==0) {
					if(!in_array($j,$tab_periode_exclue)) {$tab_periode_exclue[]=$j;}
					echo "<td style='background-color:red;'>X</td>\n";
				}
				else {
					$lig_per=mysqli_fetch_object($res_per);
					if(!in_array($lig_per->nom_periode,$tab_nom_periode[$j])) {$tab_nom_periode[$j][]=$lig_per->nom_periode;}
					echo "<td>";
					$dates_notes_first_last=get_premiere_et_derniere_date_cn_devoirs_classe_periode($tab_id_classe[$i], $j);
					if(isset($dates_notes_first_last[0])) {
						$tmp_date=formate_date($dates_notes_first_last[0]);
						echo "<span style='font-size:x-small' title=\"Date de la première évaluation de la période $j pour la classe.

Cliquez pour prendre cette date comme date de début
dans l'extraction DATE à DATE.\"><a href=\"#\" onclick=\"document.getElementById('display_date_debut').value='$tmp_date';return false;\" style='color:black;text-decoration:none;'>".$tmp_date."</a></span><br />";
						if($dates_notes_first_last[0]<$date_1ere_evaluation[$j]) {$date_1ere_evaluation[$j]=$dates_notes_first_last[0];}
					}
					if($lig_per->verouiller=="O") {
						echo "<span style='color:".$couleur_verrouillage_periode['O']."' title=\"".$explication_verrouillage_periode['O']."\">Close.</span>";
					}
					elseif($lig_per->verouiller=="N") {
						echo "<span style='color:".$couleur_verrouillage_periode['N']."' title=\"".$explication_verrouillage_periode['N']."\">Non close.</span>";
					}
					else {
						echo "<span style='color:".$couleur_verrouillage_periode['P']."' title=\"".$explication_verrouillage_periode['P']."\">Partiellement close.</span>";
					}
					if(isset($dates_notes_first_last[1])) {
						$tmp_date=formate_date($dates_notes_first_last[1]);
						echo "<br /><span style='font-size:x-small' title=\"Date de la dernière évaluation de la période $j pour la classe.

Cliquez pour prendre cette date comme date de fin
dans l'extraction DATE à DATE.\"><a href=\"#\" onclick=\"document.getElementById('display_date_fin').value='$tmp_date';return false;\" style='color:black;text-decoration:none;'>".$tmp_date."</a></span>";
						if($dates_notes_first_last[1]>$date_derniere_evaluation[$j]) {$date_derniere_evaluation[$j]=$dates_notes_first_last[1];}
					}
					echo "</td>\n";
				}
			}
			echo "</tr>\n";
		}

		echo "<tr>\n";
		echo "<th>Choix</th>\n";
		for($j=1;$j<=$max_per;$j++) {
			if(!in_array($j,$tab_periode_exclue)) {
				// Problème: Si on clique sur la case, elle change deux fois d'état
				//echo "<td style='background-color:lightgreen;' onclick=\"alterne_coche('tab_periode_num_$j')\">";
				echo "<td style='background-color:lightgreen;'>";
				//echo "<label for='choix_periode' style='cursor: pointer;'><input type=\"radio\" name=\"periode\" value='$j' /></label>\n";
				if(($date_derniere_evaluation[$j]!="9999-12-31 00:00:00")&&
				($date_derniere_evaluation[$j]!="0000-00-00 00:00:00")) {
					$tmp_date=formate_date($date_1ere_evaluation[$j]);
					echo "<span style='font-size:x-small' title=\"Date de la première évaluation de la période $j pour les classes sélectionnées.

Cliquez pour prendre cette date comme date de début
dans l'extraction DATE à DATE.\"><a href=\"#\" onclick=\"document.getElementById('display_date_debut').value='$tmp_date';return false;\" style='color:black;text-decoration:none;'>".$tmp_date."</a></span><br />";
				}
				echo "<span style='cursor: pointer;'>
				<label for=\"tab_periode_num_$j\" style=\"display:none;\">Période $j</label>
				<input type=\"checkbox\" name=\"tab_periode_num[]\" id=\"tab_periode_num_$j\" value='$j' ";
				// Dans le cas d'un retour en arrière, le champ peut avoir été préalablement coché
				if((isset($tab_periode_num))&&(in_array($j,$tab_periode_num))) {
					echo "checked ='checked'";
				}
				echo "onchange=\"document.getElementById('choix_periode').checked=true\" ";
				echo "/></span>\n";
				if($date_derniere_evaluation[$j]!="0000-00-00 00:00:00") {
					$tmp_date=formate_date($date_derniere_evaluation[$j]);
						echo "<br /><span style='font-size:x-small' title=\"Date de la dernière évaluation de la période $j pour les classes sélectionnées.

Cliquez pour prendre cette date comme date de fin
dans l'extraction DATE à DATE.\"><a href=\"#\" onclick=\"document.getElementById('display_date_fin').value='$tmp_date';return false;\" style='color:black;text-decoration:none;'>".$tmp_date."</a></span>";
				}
			}
			else {
				echo "<td style='background-color:red;'>&nbsp;";
			}
			echo "</td>\n";
		}
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<th>Classe</th>\n";
		for($j=1;$j<=$max_per;$j++) {
			echo "<th>";
			for($k=0;$k<count($tab_nom_periode[$j]);$k++) {
				if($k>0) {echo "<br />\n";}
				echo $tab_nom_periode[$j][$k];
			}
			echo "</th>\n";
		}
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<p>";
	if(isset($id_groupe)) {
		// Cas d'un prof (on a forcé plus haut à NULL $id_groupe si on n'a pas affaire à un prof)
		echo "<input type='hidden' name='id_groupe' value='".$id_groupe."' />\n";
	}

	echo "<input type='button' name='valide_choix_periode' value='Valider' onclick='check_et_submit_choix_periode()' /></p>\n";

	echo "<noscript>\n";
	echo "<p><input type='submit' name='valide_choix_periode' value='Valider' /></p>\n";
	echo "</noscript>\n";

	echo "</form>\n";
?>
<script type="text/javascript">
	//<![CDATA[
	document.getElementById('formulaire').setAttribute( "autocomplete", "off" );

	function alterne_coche(id) {
		if(document.getElementById(id)) {
			if(document.getElementById(id).checked==true) {
				document.getElementById(id).checked=false;
			}
			else {
				document.getElementById(id).checked=true;
			}
		}
	}
	////]]>
</script>
	
	
<?php
	echo "<p><br /></p>\n";

	echo "<p><em>Remarques&nbsp;:</em></p>\n";
	echo "<ul>\n";
	echo "<li><p>Les relevés d'une date à une autre ne font apparaître que les matières dans lesquelles il y a des notes.<br />
	C'est un bon choix pour un relevé de notes de demi-période (<em>afficher les enseignements dans lesquels il n'y a pas encore de notes ne présente pas vraiment d'intérêt</em>).<br />
	Ce choix permet généralement d'imprimer deux relevés de notes par page (<em>case à cocher à l'étape suivante</em>).</p></li>\n";
	echo "<li><p>Les relevés pour une période complète en revanche font apparaître toutes les matières, même si aucune note n'est saisie.</p>\n";
	echo "<p>On choisit en général la période complète lorsqu'on veut imprimer un relevé en même temps que le bulletin (<em>au verso par exemple</em>) et en fin de période, il est bon d'avoir tous les enseignements.</p></li>\n";
	echo "</ul>\n";

	echo "<script type='text/javascript'>
	//<![CDATA[ 
	function check_et_submit_choix_periode() {
		if(document.getElementById('choix_periode').checked==true) {
			var une_periode_cochee='n';
			for(j=1;j<=$max_per;j++) {
				if((document.getElementById('tab_periode_num_'+j))&&(document.getElementById('tab_periode_num_'+j).checked==true)) {
					une_periode_cochee='y';
				}
			}

			if(une_periode_cochee=='n') {
				alert('Vous n\'avez coché aucune période.');
			}
			else {
				document.getElementById('formulaire').submit();
			}
		}
		else {
			document.getElementById('formulaire').submit();
		}
	}
	//]]>
</script>\n";

}
//======================================================
//==============CHOIX DE LA SELECTION D'ELEVES==========
elseif(!isset($valide_select_eleves)) {

	//$preselection_eleves=isset($_POST['preselection_eleves']) ? $_POST['preselection_eleves'] : (isset($_GET['preselection_eleves']) ? $_GET['preselection_eleves'] : NULL);

	if(isset($display_date_debut)) {$_SESSION['display_date_debut']=$display_date_debut;}
	if(isset($display_date_fin)) {$_SESSION['display_date_fin']=$display_date_fin;}

	echo "<p class='bold'>";
	if($_SESSION['statut']=='professeur') {
		echo "<a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."' onclick=\"document.forms['form_retour'].submit();return false;\">Choisir d'autres périodes</a>\n";
	}
	elseif($_SESSION['statut']=='scolarite') {
		echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."' onclick=\"document.forms['form_retour'].submit();return false;\">Choisir d'autres périodes</a>\n";
		if(getSettingAOui('GepiScolImprRelSettings')) {
			echo " | ";
			echo "<a href='param_releve_html.php' target='_blank'><img src='../images/icons/configure.png' class='icone16' alt='Paramétrer' /> Paramètres du relevé HTML</a>";
		}
	}
	elseif($_SESSION['statut']=='administrateur') {
		// Normalement, l'administrateur n'a pas accès aux relevés de notes...
		// On ne devrait jamais passer là sauf modification de la politique des rôles.
		echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."' onclick=\"document.forms['form_retour'].submit();return false;\">Choisir d'autres périodes</a>\n";
		echo " | ";
		echo "<a href='param_releve_html.php' target='_blank'><img src='../images/icons/configure.png' class='icone16' alt='Paramétrer' /> Paramètres du relevé HTML</a>";
	}
	else {
		echo "<a href='../accueil.php'>Retour à l'accueil</a>";
	}
	echo "</p>\n";

	//===========================
	// FORMULAIRE POUR LE RETOUR AU CHOIX DES PERIODES
	echo "\n<!-- Formulaire de retour au choix des périodes -->\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' id='form_retour'>\n";
	echo "<p>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";
	}
	//echo "<input type='hidden' name='choix_periode' value='$choix_periode' />\n";
	if($choix_periode=='periode') {
		//echo "<input type='hidden' name='periode' value='$periode' />\n";
		for($j=0;$j<count($tab_periode_num);$j++) {
			echo "<input type='hidden' name='tab_periode_num[$j]' value='".$tab_periode_num[$j]."' />\n";
		}
	}
	else {
		$periode="intervalle";
		echo "<input type='hidden' name='display_date_debut' value='$display_date_debut' />\n";
		echo "<input type='hidden' name='display_date_fin' value='$display_date_fin' />\n";
	}

	if(isset($id_groupe)) {
		// Cas d'un prof (on a forcé plus haut à NULL $id_groupe si on n'a pas affaire à un prof)
		echo "<input type='hidden' name='id_groupe' value='".$id_groupe."' />\n";
	}
	echo "</p>\n";
	echo "</form>\n";
	//===========================

	//debug_var();

	//if((isset($_POST['choix_periode']))&&($_POST['choix_periode']=='periode')&&(!isset($_POST['tab_periode_num']))) {
	if((isset($choix_periode))&&($choix_periode=='periode')&&(!isset($tab_periode_num))) {
		echo "<p style='color:red'>Vous avez choisi un relevé de période, mais omis de choisir la période.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<p class='bold'>Sélection des élèves parmi les élèves de ";
	for($i=0;$i<count($tab_id_classe);$i++) {
		if($i>0) {echo ", ";}
		echo get_class_from_id($tab_id_classe[$i]);
	}
	if($choix_periode=='periode') {
		if(count($tab_periode_num)==1) {
			echo " pour la période ".$tab_periode_num[0];
		}
		else {
			echo " pour les périodes ";
			for($j=0;$j<count($tab_periode_num);$j++) {
				if($j>0) {echo ", ";}
				echo $tab_periode_num[$j];
			}
		}
	}
	else {
		echo "<br />pour une extraction des notes entre le $display_date_debut et le $display_date_fin";
	}
	echo ":</p>\n";

	echo "\n<!-- Formulaire de choix des élèves et des paramètres -->\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' id='formulaire' target='_blank'>\n";
	
	echo "<p><input type='button' name='bouton_valide_select_eleves2' value='Valider' onclick='test_check_ele()' /></p>\n";

	// Période d'affichage:
	echo "<input type='hidden' name='choix_periode' value='$choix_periode' />\n";
	if($choix_periode=='periode') {
		for($j=0;$j<count($tab_periode_num);$j++) {
			echo "<input type='hidden' name='tab_periode_num[$j]' value='".$tab_periode_num[$j]."' />\n";
		}
	}
	else {
		$periode="intervalle";
		echo "<input type='hidden' name='display_date_debut' value='$display_date_debut' />\n";
		echo "<input type='hidden' name='display_date_fin' value='$display_date_fin' />\n";
	}
echo "</p>";





	//===========================================================
	//===========================================================
	//===========================================================

	//=======================================
	// A remplacer par la suite par un choix:
	//echo "<input type='hidden' name='mode_bulletin' value='html' />\n";
	//echo "<input type='hidden' name='un_seul_bull_par_famille' value='non' />\n";

	// 20131013
	if(getParamClasse($tab_id_classe[0], "rn_type_par_defaut", "html")=="html") {
		$checked_html=" checked='checked'";
		$checked_pdf="";
	}
	else {
		$checked_html="";
		$checked_pdf=" checked='checked'";
	}

	echo "<p><input type='radio' id='releve_html' name='mode_bulletin' value='html'$checked_html onchange='griser_lignes_specifiques_pdf();' /><label for='releve_html'> Relevé HTML</label><br />\n";
	echo "<input type='radio' id='releve_pdf' name='mode_bulletin' value='pdf'$checked_pdf onchange='display_div_param_pdf();griser_lignes_specifiques_html();' /><label for='releve_pdf'> Relevé PDF</label></p>\n";

	echo "<div id='div_param_pdf'>\n";
		//echo "<br />\n";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='use_cell_ajustee' id='use_cell_ajustee' value='n' ";
		if((isset($_SESSION['pref_use_cell_ajustee']))&&($_SESSION['pref_use_cell_ajustee']=='n')) {echo "checked='checked' ";}
		echo "/><label for='use_cell_ajustee' style='cursor: pointer;'> Ne pas utiliser la fonction use_cell_ajustee() pour l'écriture des appréciations.</label>";

		$titre_infobulle="Fonction cell_ajustee()\n";
		$texte_infobulle="Pour les appréciations sur les bulletins, relevés,... on utilisait auparavant la fonction DraxTextBox() de FPDF.<br />Cette fonction avait parfois un comportement curieux avec des textes tronqués ou beaucoup plus petits dans la cellule que ce qui semblait pouvoir tenir dans la case.<br />La fonction cell_ajustee() est une fonction que mise au point pour tenter de faire mieux que DraxTextBox().<br />Comme elle n'a pas été expérimentée par suffisamment de monde sur trunk, nous avons mis une case à cocher qui permet d'utiliser l'ancienne fonction DrawTextBox() si cell_ajustee() ne se révélait pas aussi bien fichue que nous l'espèrons;o).<br />\n";
		//$texte_infobulle.="\n";
		$tabdiv_infobulle[]=creer_div_infobulle('a_propos_cell_ajustee',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_cell_ajustee','y',100,100);\"  onmouseout=\"cacher_div('a_propos_cell_ajustee');\"><img src='../images/icons/ico_ampoule.png' class='icone15x25' alt='Aide Fonction cell_ajustee()' /></a>";

		echo "<br />\n";

		// Debug
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='releve_pdf_debug' id='releve_pdf_debug' value='y' />&nbsp;<label for='releve_pdf_debug' style='cursor: pointer;'>Activer le debug pour afficher les erreurs perturbant la génération de PDF.</label>\n";

		$titre_infobulle="Debug\n";
		$texte_infobulle="Il arrive que la génération de PDF échoue.<br />Les raisons peuvent être variables (<em>manque de ressources serveur, bug,...</em>).<br />Dans ce cas, la présence d'un plugin lecteur PDF peut empêcher de voir quelles erreurs provoquent l'échec.<br />En cochant la case DEBUG, vous obtiendrez l'affichage des erreurs et ainsi vous pourrez obtenir de l'aide plus facilement sur la liste 'gepi-users'<br />\n";
		//$texte_infobulle.="\n";
		$tabdiv_infobulle[]=creer_div_infobulle('div_bull_debug_pdf',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('div_bull_debug_pdf','y',100,100);\"  onmouseout=\"cacher_div('div_bull_debug_pdf');\"><img src='../images/icons/ico_ampoule.png' class='icone15x25' alt='Aide Debug' /></a>";

		echo "<br />\n";


		// 20191012
		// NON ACHEVE DONC DESACTIVE
		/*if(in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe'))) {

			$sql="CREATE TABLE IF NOT EXISTS releve_cn_mail (
				id INT(11) unsigned NOT NULL auto_increment,
				login_sender VARCHAR(50) NOT NULL DEFAULT '',
				pers_id VARCHAR(10) NOT NULL DEFAULT '',
				email VARCHAR(100) NOT NULL DEFAULT '',
				login_ele VARCHAR(50) NOT NULL DEFAULT '',
				nom_prenom_ele VARCHAR(255) NOT NULL DEFAULT '',
				id_classe INT(11) unsigned NOT NULL DEFAULT '0',
				periodes VARCHAR(100) NOT NULL DEFAULT '',
				date_debut DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
				date_fin DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
				id_envoi VARCHAR(255) NOT NULL DEFAULT '',
				envoi VARCHAR(50) NOT NULL DEFAULT '',
				date_envoi DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
				PRIMARY KEY ( id )
				) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
			$create_table=mysqli_query($mysqli, $sql);

			$sql="SHOW TABLES LIKE 'releve_cn_mail'";
			$test=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test)>0) {
				echo "<p style='margin-top:1em;'>
				<input type='checkbox' name='envoi_rel_par_mail' id='envoi_rel_par_mail' value='1' onchange=\"checkbox_change(this.id)\" />
				<label for='envoi_rel_par_mail' id='texte_envoi_rel_par_mail'>
					Envoyer le relevé de notes par mail lorsqu'une adresse mail responsable est disponible dans la base <em>(PDF seulement pour le moment)</em>.
				</label>
			</p>";
			}
			else {
				echo "<p style='color:red'>Une mise à jour de la base est requise pour créer la table 'releve_cn_mail'.</p>";
			}
		}
		*/



	echo "</div>\n";

	echo "<script type='text/javascript'>
		//<![CDATA[
	function display_div_param_pdf() {
		if(document.getElementById('div_param_pdf')) {
			if(document.getElementById('releve_pdf').checked==true) {
				document.getElementById('div_param_pdf').style.display='';
			}
			else {
				document.getElementById('div_param_pdf').style.display='none';
			}
		}
	}

	display_div_param_pdf();
	////]]>
</script>\n";

	//=======================================

	if ((($_SESSION['statut']=='eleve') AND (getSettingValue("GepiAccesOptionsReleveEleve") != "yes"))||
		(($_SESSION['statut']=='responsable') AND (getSettingValue("GepiAccesOptionsReleveParent") != "yes"))) {
		echo "<p>\n";
		// Témoin destiné à sauter l'étape des paramètres
		echo "<input type='hidden' name='choix_parametres' value='y' />\n";
		//echo "<input type='hidden' name='mode_bulletin' value='html' />\n";
		echo "<input type='hidden' name='un_seul_bull_par_famille' value='oui' />\n";

		echo "<input type='hidden' name='deux_releves_par_page' value='non' />\n";
		echo "</p>\n";
	}
	else {
		echo "<p>\n";
		echo "<strong><img src='../images/icons/configure.png' class='icone16' alt='Paramètres' /> Paramètres:</strong> \n";
		echo "<span id='pliage_param_releve'>\n";
		echo "(<em>";
		echo "<a href='#' onclick=\"document.getElementById('div_param_releve').style.display='';return false;\">Afficher</a>";
		echo " / \n";
		echo "<a href='#' onclick=\"document.getElementById('div_param_releve').style.display='none';return false;\">Masquer</a>";
		echo " les paramètres du relevé de notes</em>).";
		echo "</span>\n";
		echo "</p>\n";

		echo "<div id='div_param_releve'>\n";

		if (($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
			echo "<table border='0' summary='Tableau de paramètres'>\n";
			echo "<tr><td valign='top'><input type='checkbox' name='un_seul_bull_par_famille' id='un_seul_bull_par_famille' value='oui' ";
			if((isset($_SESSION['pref_un_seul_bull_par_famille']))&&($_SESSION['pref_un_seul_bull_par_famille']=='oui')) {echo "checked='checked' ";}
			echo "/></td><td><label for='un_seul_bull_par_famille' style='cursor: pointer;'>Ne pas imprimer de relevé de notes pour le deuxième parent<br />(<em>même dans le cas de parents séparés</em>).</label></td></tr>\n";

			echo "<tr><td valign='top'><input type='checkbox' name='deux_releves_par_page' id='deux_releves_par_page' value='oui' ";
			if((isset($_SESSION['pref_deux_releves_par_page']))&&($_SESSION['pref_deux_releves_par_page']=='oui')) {echo "checked='checked' ";}
			echo "/></td><td><label for='deux_releves_par_page' style='cursor: pointer;'>Produire deux relevés par page (<em>PDF</em>).</label></td></tr>\n";

			echo "<tr><td valign='top'><input type='checkbox' name='tri_par_etab_orig' id='tri_par_etab_orig' value='y' ";
			if((isset($_SESSION['pref_tri_par_etab_orig']))&&($_SESSION['pref_tri_par_etab_orig']=='y')) {echo "checked='checked' ";}
			echo "/></td><td><label for='tri_par_etab_orig' style='cursor: pointer;'>Trier les relevés par établissement d'origine.</label></td></tr>\n";

			echo "</table>\n";
		}
		else {
			echo "<p>\n";
			echo "<input type='hidden' name='un_seul_bull_par_famille' value='oui' />\n";
			echo "<input type='hidden' name='deux_releves_par_page' value='non' />\n";
			echo "</p>\n";
		}

		// AJOUTER LES PARAMETRES...
		//echo "<hr width='100' />\n";

		//=======================================
		// Tableau des paramètres mis dans un fichier externe pour permettre la même exploitation dans le cas d'insertion des relevés de notes entre les bulletins
		include("tableau_choix_parametres_releves_notes.php");
		//=======================================

			echo "<p>\n";
		echo "<input type='hidden' name='valide_select_eleves' value='y' />\n";
		//echo "<p><input type='submit' name='choix_parametres' value='Valider' /></p>\n";
		echo "<input type='hidden' name='choix_parametres' value='effectue' />\n";
			echo "</p>\n";

		echo "</div>\n";

		echo "<script type='text/javascript'>
//<![CDATA[

if((document.getElementById('releve_html'))&&(document.getElementById('releve_html').checked)) {
	griser_lignes_specifiques_pdf();
}
else {
	display_div_param_pdf();
	griser_lignes_specifiques_html();
}

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
	CocheLigne('rn_moy_classe');
	CocheLigne('rn_moy_min_max_classe');
	CocheLigne('rn_retour_ligne');
	CocheLigne('rn_bloc_obs');
}

function ToutDeCocher() {
";
		for($k=0;$k<count($tab_item);$k++) {
			echo "	DecocheLigne('".$tab_item[$k]."');\n";
		}
		echo "	DecocheLigne('rn_app');
	DecocheLigne('rn_adr_resp');
	DecocheLigne('rn_moy_classe');
	DecocheLigne('rn_moy_min_max_classe');
	DecocheLigne('rn_retour_ligne');
	DecocheLigne('rn_bloc_obs');
}";


if(($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
    echo "document.getElementById('div_param_releve').style.display='none';";
} else {
    echo "document.getElementById('div_param_releve').style.display='';";
}

echo "	////]]>";
echo "</script>\n";

	}
	//===========================================================
	//===========================================================
	//===========================================================





	if(count($tab_id_classe)>1) {
		echo "<p>Pour toutes les classes";
		/*
		if(count($tab_periode_num)>1) {
			echo " et toutes les périodes";
		}
		*/
		echo " <a href='#' onclick='cocher_tous_eleves();return false;'>Cocher tous les élèves</a> / <a href='#' onclick='decocher_tous_eleves();return false;'>Décocher tous les élèves</a></p>\n";
	}

	$max_eff_classe=0;
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<p class='bold'><input type='hidden' name='tab_id_classe[$i]' value='".$tab_id_classe[$i]."' />\n";

		echo "Classe de ".get_class_from_id($tab_id_classe[$i])."</p>\n";

		echo "<table class='boireaus' summary='Tableau de choix des élèves'>\n";
		echo "<tr>\n";
		echo "<th>Elèves</th>\n";

		if($choix_periode=='periode') {
			for($j=0;$j<count($tab_periode_num);$j++) {
				//echo "<th>Période $j</th>\n";
				$sql="SELECT nom_periode FROM periodes WHERE id_classe='".$tab_id_classe[$i]."' AND num_periode='".$tab_periode_num[$j]."';";
				$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
				$lig_per=mysqli_fetch_object($res_per);
				echo "<th>\n";

				echo $lig_per->nom_periode;

				echo "<br />\n";

				echo "<a href=\"javascript:CocheColonneSelectEleves(".$i.",".$j.");\"><img src='../images/enabled.png' class='icone15' alt='Cocher tous les élèves' /></a> / <a href=\"javascript:DecocheColonneSelectEleves(".$i.",".$j.");\"><img src='../images/disabled.png' class='icone15' alt='Décocher tous les élèves' /></a>\n";

				echo "</th>\n";
			}
		}
		else {
			echo "<th>\n";
			echo "Du $display_date_debut au $display_date_fin<br />\n";

			echo "<a href=\"javascript:CocheColonneSelectEleves(".$i.",'".$periode."');\"><img src='../images/enabled.png' class='icone15' alt='Cocher tous les élèves' /></a> / <a href=\"javascript:DecocheColonneSelectEleves(".$i.",'".$periode."');\"><img src='../images/disabled.png' class='icone15' alt='Décocher tous les élèves' /></a>\n";

			echo "</th>\n";
		}

		echo "</tr>\n";




		if (($_SESSION['statut'] == 'scolarite') AND (getSettingValue("GepiAccesReleveScol") == "yes")) {
			$sql="SELECT DISTINCT e.* FROM eleves e,
							j_eleves_classes jec
				WHERE jec.login=e.login AND
							jec.id_classe='".$tab_id_classe[$i]."'
				ORDER BY e.nom,e.prenom;";
		}
		elseif (($_SESSION['statut'] == 'cpe') AND (getSettingValue("GepiAccesReleveCpeTousEleves") == "yes")) {
			$sql="SELECT DISTINCT e.* FROM eleves e,
							j_eleves_classes jec
				WHERE jec.login=e.login AND
						jec.id_classe='".$tab_id_classe[$i]."'
				ORDER BY e.nom,e.prenom;";
		}
		elseif (($_SESSION['statut'] == 'cpe') AND (getSettingValue("GepiAccesReleveCpe") == "yes")) {
			$sql="SELECT DISTINCT e.* FROM eleves e,
							j_eleves_classes jec,
							j_eleves_cpe jecpe
				WHERE jec.login=e.login AND
						jecpe.e_login=e.login AND
						jecpe.cpe_login='".$_SESSION['login']."' AND
						jec.id_classe='".$tab_id_classe[$i]."'
				ORDER BY e.nom,e.prenom;";
		}
		elseif (($_SESSION['statut'] == 'professeur') AND
				(
					(getSettingValue("GepiAccesReleveProf") == "yes") ||
					(getSettingValue("GepiAccesReleveProfTousEleves") == "yes") ||
					(getSettingValue("GepiAccesReleveProfToutesClasses") == "yes")
				)
			) {
			$sql="SELECT DISTINCT e.* FROM eleves e,
							j_eleves_classes jec
				WHERE jec.login=e.login AND
							jec.id_classe='".$tab_id_classe[$i]."'
				ORDER BY e.nom,e.prenom;";
			// On fait le filtrage des élèves plus bas dans le cas du prof
		}
		elseif(($_SESSION['statut'] == 'professeur')&&(getSettingValue("GepiAccesReleveProfP")=="yes")) {
			if(is_pp($_SESSION['login'], $tab_id_classe[$i])) {
				if(getSettingAOui('GepiAccesPPTousElevesDeLaClasse')) {
					// Le prof est PP de la classe, on lui donne l'accès à tous les élèves de la classe
					$sql="SELECT DISTINCT e.* FROM eleves e,
									j_eleves_classes jec
						WHERE jec.login=e.login AND
									jec.id_classe='".$tab_id_classe[$i]."'
						ORDER BY e.nom,e.prenom;";
				}
				else {
					// Le prof est PP d'au moins une partie des élèves de la classe
					$sql="SELECT DISTINCT e.* FROM eleves e,
									j_eleves_classes jec,
									j_eleves_professeurs jep
						WHERE jec.login=e.login AND
								jec.login=jep.login AND
								jep.professeur='".$_SESSION['login']."' AND
								jec.id_classe='".$tab_id_classe[$i]."'
						ORDER BY e.nom,e.prenom;";
					$test_acces=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test_acces)==0) {
						// On pourrait mettre un tentative_intrusion()
						$gepi_prof_suivi=ucfirst(retourne_denomination_pp($tab_id_classe[$i]));
						echo "<p>Vous n'êtes pas ".$gepi_prof_suivi." de cette classe, donc pas autorisé à accéder aux relevés de notes de ces élèves.</p>\n";
						require("../lib/footer.inc.php");
						die();
					}
				}
			}
			else {
				// On pourrait mettre un tentative_intrusion()
				$gepi_prof_suivi=ucfirst(retourne_denomination_pp($tab_id_classe[$i]));
				echo "<p>Vous n'êtes pas ".$gepi_prof_suivi." de cette classe, donc pas autorisé à accéder aux relevés de notes de ces élèves.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}
		elseif(($_SESSION['statut']=='eleve')&&(getSettingValue("GepiAccesReleveEleve") == "yes")) {
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE (jec.id_classe='".$tab_id_classe[$i]."' AND jec.login='".$_SESSION['login']."' AND jec.login=e.login);";
		}
		elseif(($_SESSION['statut']=='responsable')&&(getSettingValue("GepiAccesReleveParent") == "yes")) {
			$sql="(SELECT DISTINCT e.*  FROM eleves e, j_eleves_classes jec, responsables2 r, resp_pers rp
					WHERE (e.ele_id=r.ele_id AND
							r.pers_id=rp.pers_id AND
							rp.login='".$_SESSION['login']."' AND
							jec.id_classe='".$tab_id_classe[$i]."' AND
							(r.resp_legal='1' OR r.resp_legal='2') AND
							jec.login=e.login))";
			if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
				$sql.=" UNION (SELECT DISTINCT e.*  FROM eleves e, j_eleves_classes jec, responsables2 r, resp_pers rp
					WHERE (e.ele_id=r.ele_id AND
							r.pers_id=rp.pers_id AND
							rp.login='".$_SESSION['login']."' AND
							jec.id_classe='".$tab_id_classe[$i]."' AND
							r.resp_legal='0' AND
							r.acces_sp='y' AND 
							jec.login=e.login))";
			}
			$sql.=";";
		}
		elseif($_SESSION['statut'] == 'autre') {
			$sql="SELECT DISTINCT e.* FROM eleves e,
							j_eleves_classes jec
				WHERE jec.login=e.login AND
							jec.id_classe='".$tab_id_classe[$i]."'
				ORDER BY e.nom,e.prenom;";
		}
		else {
			echo "<p style='color:red'>La recherche de la liste des élèves n'est pas possible pour vos statut et autorisations???</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
		//echo "$sql<br />";

		$temoin_preselection_eleve_faite="n";

		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		$alt=1;
		$cpt=0;
		while($lig_ele=mysqli_fetch_object($res_ele)) {

			//$acces_prof_a_cet_eleve="y";
			//if() {
			//============================================
			// A FAIRE -> FAIT PLUS BAS
			// Dans le cas du choix d'un groupe, on contrôle si l'élève fait partie du groupe
			//$sql="SELECT 1=1 FROM j_eleves_groupes WHERE login='".$lig_ele->login."' AND id_groupe='';";
			//$sql="SELECT 1=1 FROM j_eleves_groupes WHERE login='".$lig_ele->login."' AND id_groupe='' AND (periode='' OR periode='');";
			//============================================

			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td style='text-align:left;'>".$lig_ele->nom." ".$lig_ele->prenom."</td>\n";

			if($choix_periode=='periode') {
				for($j=0;$j<count($tab_periode_num);$j++) {

					$sql="SELECT 1=1 FROM j_eleves_classes jec
						WHERE jec.id_classe='".$tab_id_classe[$i]."' AND
								jec.periode='".$tab_periode_num[$j]."';";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)>0) {
						if (isset($id_groupe)) {
							// Cas d'un prof
							// Dans le cas du choix d'un groupe, on contrôle si l'élève fait partie du groupe
							$sql="SELECT 1=1 FROM j_eleves_groupes WHERE (login='".$lig_ele->login."' AND id_groupe='$id_groupe' AND periode='".$tab_periode_num[$j]."');";
							$test_ele_grp=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test_ele_grp)>0) {
								echo "<td>
									<label for='tab_selection_ele_".$i."_".$j."[]'_".$cpt."' class='invisible'>".$lig_ele->nom." ".$lig_ele->prenom." periode ".$j."</label>
									<input type='checkbox' name='tab_selection_ele_".$i."_".$j."[]' id='tab_selection_ele_".$i."_".$j."_".$cpt."' value=\"".$lig_ele->login."\" ";
								// Dans le cas d'un retour en arrière, des cases peuvent avoir été cochées
								$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_'.$j]) ? $_POST['tab_selection_ele_'.$i.'_'.$j] : (isset($_GET['tab_selection_ele_'.$i.'_'.$j]) ? $_GET['tab_selection_ele_'.$i.'_'.$j] : array());

								if(in_array($lig_ele->login, $tab_selection_eleves)) {
									echo "checked='checked' ";
									$temoin_preselection_eleve_faite="y";
								}

								echo "/></td>\n";
							}
							else {
								echo "<td>-</td>\n";
							}
						}
						else {
							// Si c'est un prof avec seulement le droit de voir les élèves de ses groupes, il faut limiter les cases à cocher
							if(($_SESSION['statut']=='professeur') &&
							(getSettingValue("GepiAccesReleveProf") == "yes") &&
							(getSettingValue("GepiAccesReleveProfTousEleves") != "yes") &&
							(getSettingValue("GepiAccesReleveProfToutesClasses") != "yes")) {
								$sql="SELECT 1=1 FROM j_eleves_groupes jeg, j_groupes_professeurs jgp
												WHERE (
														jeg.id_groupe=jgp.id_groupe AND
														jeg.login='".$lig_ele->login."' AND
														jgp.login='".$_SESSION['login']."' AND
														jeg.periode='".$tab_periode_num[$j]."'
														);";
								$test_ele_grp=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test_ele_grp)==0) {
									echo "<td>-</td>\n";
								}
								else {
									echo "<td>
									<label for='tab_selection_ele_".$i."_".$j."_".$cpt."' class='invisible'>".$lig_ele->nom." ".$lig_ele->prenom." periode ".$j."</label>
									<input type='checkbox' name='tab_selection_ele_".$i."_".$j."[]' id='tab_selection_ele_".$i."_".$j."_".$cpt."' value=\"".$lig_ele->login."\" ";
									// Dans le cas d'un retour en arrière, des cases peuvent avoir été cochées
									$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_'.$j]) ? $_POST['tab_selection_ele_'.$i.'_'.$j] : (isset($_GET['tab_selection_ele_'.$i.'_'.$j]) ? $_GET['tab_selection_ele_'.$i.'_'.$j] : array());
									if(in_array($lig_ele->login,$tab_selection_eleves)) {
										echo "checked='checked' ";
										$temoin_preselection_eleve_faite="y";
									}
									echo "/></td>\n";
								}
							}
							elseif($_SESSION['statut']=='eleve') {
								// Un élève ne doit voir que lui-même
								echo "<td>";
								echo "<label for='tab_selection_ele_".$i."_".$j."_".$cpt."' class='invisible'>".$lig_ele->nom." ".$lig_ele->prenom." periode ".$j."</label>
									<input type='hidden' name='tab_selection_ele_".$i."_".$j."[]' id='tab_selection_ele_".$i."_".$j."_".$cpt."' value=\"".$_SESSION['login']."\" />";
								echo "<img src='../images/enabled.png' class='icone15' alt='Coché' />";
								echo "</td>\n";
							}
							elseif($_SESSION['statut']=='responsable') {
								// Un responsable ne voit que ses enfants
								echo "<td>
									<label for='tab_selection_ele_".$i."_".$j."_".$cpt."' class='invisible'>".$lig_ele->nom." ".$lig_ele->prenom." periode ".$j."</label>
									<input type='checkbox' name='tab_selection_ele_".$i."_".$j."[]' id='tab_selection_ele_".$i."_".$j."_".$cpt."' value=\"".$lig_ele->login."\" ";
								echo "checked='checked' ";
								echo "/></td>\n";
							}
							else {
								$sql="SELECT 1=1 FROM j_eleves_classes jec
												WHERE (jec.id_classe='".$tab_id_classe[$i]."' AND
														jec.login='".$lig_ele->login."' AND
														jec.periode='".$tab_periode_num[$j]."');";
								$test_ele_grp=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test_ele_grp)==0) {
									echo "<td>-</td>\n";
								}
								else {
									echo "<td>
										<label for='tab_selection_ele_".$i."_".$j."_".$cpt."' class='invisible'>".$lig_ele->nom." ".$lig_ele->prenom." periode ".$j."</label>
									<input type='checkbox' name='tab_selection_ele_".$i."_".$j."[]' id='tab_selection_ele_".$i."_".$j."_".$cpt."' value=\"".$lig_ele->login."\" ";
									// Dans le cas d'un retour en arrière, des cases peuvent avoir été cochées
									$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_'.$j]) ? $_POST['tab_selection_ele_'.$i.'_'.$j] : (isset($_GET['tab_selection_ele_'.$i.'_'.$j]) ? $_GET['tab_selection_ele_'.$i.'_'.$j] : array());
									if(in_array($lig_ele->login,$tab_selection_eleves)) {
										echo "checked='checked' ";
										$temoin_preselection_eleve_faite="y";
									}
									echo "/>";
								}
							}
						}
					}
					else {
						echo "<td>-</td>\n";
					}
				}
			}
			else {
				echo "<td>\n";

				if (isset($id_groupe)) {
					// Dans le cas du choix d'un groupe, on contrôle si l'élève fait partie du groupe
					$sql="SELECT 1=1 FROM j_eleves_groupes WHERE (login='".$lig_ele->login."' AND id_groupe='$id_groupe');";
					$test_ele_grp=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test_ele_grp)>0) {
						echo "<label for='tab_selection_ele_".$i."_".$periode."_".$cpt."' class='invisible'>".$lig_ele->nom." ".$lig_ele->prenom." periode ".$periode."</label>
									<input type='checkbox' name='tab_selection_ele_".$i."_".$periode."[]' id='tab_selection_ele_".$i."_".$periode."_".$cpt."' value=\"".$lig_ele->login."\" ";

						// Dans le cas d'un retour en arrière, des cases peuvent avoir été cochées
						$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_'.$periode]) ? $_POST['tab_selection_ele_'.$i.'_'.$periode] : (isset($_GET['tab_selection_ele_'.$i.'_'.$periode]) ? $_GET['tab_selection_ele_'.$i.'_'.$periode] : array());
						if(in_array($lig_ele->login,$tab_selection_eleves)) {
							echo "checked='checked' ";
							$temoin_preselection_eleve_faite="y";
						}
						echo "/></td>\n";
					}
					else {
						echo "<td>-</td>\n";
					}
				}
				else {
					if(($_SESSION['statut']=='professeur') &&
					(getSettingValue("GepiAccesReleveProf") == "yes") &&
					(getSettingValue("GepiAccesReleveProfTousEleves") != "yes") &&
					(getSettingValue("GepiAccesReleveProfToutesClasses") != "yes")) {
						$sql="SELECT 1=1 FROM j_eleves_groupes jeg, j_groupes_professeurs jgp
										WHERE (
												jeg.id_groupe=jgp.id_groupe AND
												jeg.login='".$lig_ele->login."' AND
												jgp.login='".$_SESSION['login']."'
												);";
						$test_ele_grp=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test_ele_grp)==0) {
							echo "<td>-</td>\n";
						}
						else {
							echo "<label for='tab_selection_ele_".$i."_".$periode."_".$cpt."' class='invisible'>".$lig_ele->nom." ".$lig_ele->prenom." periode ".$periode."</label>
								<input type='checkbox' name='tab_selection_ele_".$i."_".$periode."[]' id='tab_selection_ele_".$i."_".$periode."_".$cpt."' value=\"".$lig_ele->login."\" ";

							// Dans le cas d'un retour en arrière, des cases peuvent avoir été cochées
							$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_'.$periode]) ? $_POST['tab_selection_ele_'.$i.'_'.$periode] : (isset($_GET['tab_selection_ele_'.$i.'_'.$periode]) ? $_GET['tab_selection_ele_'.$i.'_'.$periode] : array());
							if(in_array($lig_ele->login,$tab_selection_eleves)) {
								echo "checked='checked' ";
								$temoin_preselection_eleve_faite="y";
							}
							echo "/></td>\n";
						}
					}
					else {
						echo "<label for='tab_selection_ele_".$i."_".$periode."_".$cpt."' class='invisible'>".$lig_ele->nom." ".$lig_ele->prenom." periode ".$periode."</label>
								<input type='checkbox' name='tab_selection_ele_".$i."_".$periode."[]' id='tab_selection_ele_".$i."_".$periode."_".$cpt."' value=\"".$lig_ele->login."\" ";

						// Dans le cas d'un retour en arrière, des cases peuvent avoir été cochées
						$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$i.'_'.$periode]) ? $_POST['tab_selection_ele_'.$i.'_'.$periode] : (isset($_GET['tab_selection_ele_'.$i.'_'.$periode]) ? $_GET['tab_selection_ele_'.$i.'_'.$periode] : array());
						if(in_array($lig_ele->login,$tab_selection_eleves)) {
							echo "checked='checked' ";
							$temoin_preselection_eleve_faite="y";
						}
						echo "/></td>\n";
					}
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
//<![CDATA[
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
		if($choix_periode=='periode') {
			for($j=0;$j<count($tab_periode_num);$j++) {
				echo "CocheColonneSelectEleves($i,$j);\n";
			}
		}
		else {
			echo "CocheColonneSelectEleves($i,'$periode');\n";
		}
	}

	echo "}
function decocher_tous_eleves() {
";

	for($i=0;$i<count($tab_id_classe);$i++) {
		if($choix_periode=='periode') {
			for($j=0;$j<count($tab_periode_num);$j++) {
				echo "DecocheColonneSelectEleves($i,$j);\n";
			}
		}
		else {
			echo "DecocheColonneSelectEleves($i,'$periode');\n";
		}
	}

	echo "}\n";

	if($_SESSION['statut']!='eleve') {
		echo "function test_check_ele() {
	eff_eleve_checked=0;
	
	tabinput=document.getElementsByTagName('input');
	
	for(i=0;i<tabinput.length;i++) {
		//nom_element=tabinput[i].getAttribute('name');
		// On ne peut pas tester si le champ est coché en utilisant l'attribut 'name' parce que cet attribut est de la forme 'tab_selection_ele_...[]' et les crochets posent pb.
		
		if(tabinput[i].getAttribute('id')) {
			nom_element=tabinput[i].getAttribute('id');

			t=nom_element.substring(0,18);

			if(t=='tab_selection_ele_') {
				if(document.getElementById(nom_element).checked==true) {
					eff_eleve_checked++;
				}
			}
		}
	}
	
	if(eff_eleve_checked==0) {
		//alert('Aucun élève n est sélectionné.');
		//return confirm('Aucun élève n est sélectionné. Voulez-vous quand même générer le relevé?');
		if(confirm('Aucun élève n est sélectionné. Voulez-vous quand même générer le relevé?')) {document.getElementById('formulaire').submit();}
	}
	else {
	//	alert('Au moins un élève est sélectionné: '+eff_eleve_checked);
	document.getElementById('formulaire').submit();
	}
}\n";
	}
	else {
		echo "function test_check_ele() {
	// On ne fait pas de test dans le cas d'un login eleve
	// L'élève ne peut consulter que ses notes et est donc nécessairement coché
	document.getElementById('formulaire').submit();
}\n";
	}

	if($temoin_preselection_eleve_faite=="n") {
		echo "// On coche tous les élèves par défaut:
cocher_tous_eleves();";
	}
	echo "
////]]>
</script>\n";

	//echo "<p><a href='javascript:test_check_ele();return false;'>Test élève</a></p>\n";
	//echo "<p><a href='#' onclick='test_check_ele();return false;'>Test élève</a></p>\n";

	echo "<p>\n";
	if(isset($id_groupe)) {
		// Cas d'un prof (on a forcé plus haut à NULL $id_groupe si on n'a pas affaire à un prof)
		echo "<input type='hidden' name='id_groupe' value='".$id_groupe."' />\n";
	}

	/*
	if ((($_SESSION['statut']=='eleve') AND (getSettingValue("GepiAccesOptionsReleveEleve") != "yes"))||
		(($_SESSION['statut']=='responsable') AND (getSettingValue("GepiAccesOptionsReleveParent") != "yes"))) {
		// Témoin destiné à sauter l'étape des paramètres
		echo "<input type='hidden' name='choix_parametres' value='y' />\n";
		echo "<input type='hidden' name='mode_bulletin' value='html' />\n";
		echo "<input type='hidden' name='un_seul_bull_par_famille' value='oui' />\n";
	}
	*/
	echo "<input type='hidden' name='valide_select_eleves' value='y' />\n";
	//echo "<p><input type='submit' name='bouton_valide_select_eleves2' value='Valider' /></p>\n";
	//echo "<p><input type='submit' name='bouton_valide_select_eleves2' value='Valider' onclick='test_check_ele()' /></p>\n";
	echo "<input type='button' name='bouton_valide_select_eleves2' value='Valider' onclick='test_check_ele()' /></p>\n";
	echo "</form>\n";
}

//=======================================================
//===EXTRACTION DES DONNEES PUIS AFFICHAGE DES RELEVES===
else {
	//$mode_bulletin=isset($_POST['mode_bulletin']) ? $_POST['mode_bulletin'] : "html";

	$un_seul_bull_par_famille=isset($_POST['un_seul_bull_par_famille']) ? $_POST['un_seul_bull_par_famille'] : "non";
	$deux_releves_par_page=isset($_POST['deux_releves_par_page']) ? $_POST['deux_releves_par_page'] : "non";

	$tri_par_etab_orig=isset($_POST['tri_par_etab_orig']) ? $_POST['tri_par_etab_orig'] : "n";

	if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
		$deux_releves_par_page="non";
		$un_seul_bull_par_famille="oui";
	}

	$nb_releve_par_page=1;
	if($deux_releves_par_page=="oui") {
		$nb_releve_par_page=2;
	}

	$use_cell_ajustee=isset($_POST['use_cell_ajustee']) ? $_POST['use_cell_ajustee'] : "y";

	// 20191012
	$envoi_rel_par_mail=isset($_POST['envoi_rel_par_mail']) ? $_POST['envoi_rel_par_mail'] : NULL;

	// 20191012
	// NON ACHEVE DONC DESACTIVE:
	unset($envoi_rel_par_mail);
	if(isset($envoi_rel_par_mail)) {
		// envoi_rel_par_mail == 1 -> remplir la liste des élèves/parents dans la table releve_cn_mail
		// envoi_rel_par_mail == 2 -> suite/boucle par tranches de N envois

		//**************** EN-TETE *********************
		$titre_page = "Envoi par mail des relevés";
		require_once("../lib/header.inc.php");
		//**************** FIN EN-TETE *****************

		debug_var();

		echo "
<p class='bold'><a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

		$tableau_eleve=array();
		$tableau_eleve['login']=array();
		$tableau_eleve['no_gep']=array();
		$tableau_eleve['nom_prenom']=array();
		$tableau_resp=array();

/*
			if(!in_array($current_eleve_login[$i],$tableau_eleve['login'])) {
				$tableau_eleve['login'][]=$current_eleve_login[$i];
				$tableau_eleve['no_gep'][]=$tab_ele['no_gep'];
				$tableau_eleve['nom_prenom'][]=remplace_accents($tab_ele['nom']."_".$tab_ele['prenom'],'all');
				// 20190531
				if(isset($tab_ele['resp'])) {
					$tableau_resp[$current_eleve_login[$i]]=$tab_ele['resp'];
				}
				else {
					$tableau_resp[$current_eleve_login[$i]]=array();
				}
			}
*/

		foreach($tab_id_classe as $key0 => $value0) {
			foreach($tab_periode_num as $key1 => $value1) {
				if(isset($_POST['tab_selection_ele_'.$key0.'_'.$key1])) {
					foreach($_POST['tab_selection_ele_'.$key0.'_'.$key1] as $key2 => $value2) {
						if(!in_array($value2, $tableau_eleve['login'])) {

							$tmp_tab=get_info_eleve($value2);

							$indice=count($tableau_eleve['login']);

							$tableau_eleve['login'][$indice]=$value2;
							$tableau_eleve['no_gep'][$indice]=$tmp_tab['no_gep'];
							$tableau_eleve['nom_prenom'][$indice]=$tmp_tab['nom'].' '.$tmp_tab['prenom'];

							$tmp_tab=get_resp_from_ele_login($value2);
							foreach($tmp_tab as $current_resp) {
								$tableau_resp[$value2]["adresses"]["adresse"][]=get_info_responsable('', $current_resp['pers_id']);
							}

						}
					}
				}
			}

			if(isset($_POST['tab_selection_ele_'.$key0.'_intervalle'])) {
				foreach($_POST['tab_selection_ele_'.$key0.'_intervalle'] as $key2 => $value2) {
					if(!in_array($value2, $tableau_eleve['login'])) {

						$tmp_tab=get_info_eleve($value2);

						$indice=count($tableau_eleve['login']);

						$tableau_eleve['login'][$indice]=$value2;
						$tableau_eleve['no_gep'][$indice]=$tmp_tab['no_gep'];
						$tableau_eleve['nom_prenom'][$indice]=$tmp_tab['nom'].' '.$tmp_tab['prenom'];

						$tmp_tab=get_resp_from_ele_login($value2);
						foreach($tmp_tab as $current_resp) {
							$tableau_resp[$value2]["adresses"]["adresse"][]=get_info_responsable('', $current_resp['pers_id']);
						}

					}
				}
			}
		}







		if($envoi_rel_par_mail=='1') {
			echo "
<form action='".$_SERVER['PHP_SELF']."' method='POST'>
	<fieldset class='fieldset_opacite50'>
		<h2>Envoi de relevés de notes par mail</h2>
		<input type='hidden' name='envoi_rel_par_mail' value ='2' />
		<input type='hidden' name='mode_bulletin' value ='pdf' />";
			// Liste des paramètres:
			// - classes
			// - périodes
			// - dates
			// - élèves sélectionnés
			// - paramètres choisis dans le tableau des paramètres des relevés

			echo (isset($use_cell_ajustee) ? "<input type='hidden' name='use_cell_ajustee' value ='".$use_cell_ajustee."' />" : '')."
				<input type='hidden' name='nb_releve_par_page' value ='1' />";

			$tmp_tab_param=array('tab_periode_num', 'rn_aff_classe_nom', 'rn_nomdev', 'rn_aff_nomdev_choix', 'rn_toutcoefdev', 'rn_coefdev_si_diff', 'rn_datedev', 'rn_col_moy', 'rn_sign_pp', 'rn_sign_resp', 'rn_app', 'rn_moy_classe', 'rn_moy_min_max_classe', 'rn_retour_ligne', 'rn_rapport_standard_min_font', 'rn_adr_resp', 'rn_bloc_obs', 'rn_sign_nblig', 'rn_formule', 'tab_id_classe');
			foreach($tmp_tab_param as $key0 => $value0) {
				if(isset($_POST[$value0])) {
					foreach($_POST[$value0] as $key => $value) {
						echo "
						<input type='hidden' name='".$value0."[".$key."]' value ='".$value."' />";
					}
				}
			}


			$tmp_tab_param2=array('choix_periode', 'display_date_debut', 'display_date_fin', 'chaine_coef', 'rn_couleurs_alternees', 'valide_select_eleves', 'choix_parametres');
			foreach($tmp_tab_param2 as $key0 => $value0) {
				if(isset($_POST[$value0])) {
					echo "
					<input type='hidden' name='".$value0."' value ='".$_POST[$value0]."' />";
				}
			}

			$id_envoi_rel=$_SESSION['login'].'_'.time();
			echo "
		<input type='hidden' name='id_envoi_rel' value ='".$id_envoi_rel."' />";


		foreach($tab_id_classe as $key0 => $value0) {
			foreach($tab_periode_num as $key1 => $value1) {
				if(isset($_POST['tab_selection_ele_'.$key0.'_'.$key1])) {
					foreach($_POST['tab_selection_ele_'.$key0.'_'.$key1] as $key2 => $value2) {
						echo "
		<input type='hidden' name='tab_selection_ele_".$key0.'_'.$key1."[$key2]' value ='".$value2."' />";
					}
				}
			}

			if(isset($_POST['tab_selection_ele_'.$key0.'_intervalle'])) {
				foreach($_POST['tab_selection_ele_'.$key0.'_intervalle'] as $key2 => $value2) {
					echo "
		<input type='hidden' name='tab_selection_ele_".$key0."_intervalle[$key2]' value ='".$value2."' />";
				}
			}
		}

		echo "tableau_eleve<pre>";
		print_r($tableau_eleve);
		echo "</pre>";

		echo "tableau_resp<pre>";
		print_r($tableau_resp);
		echo "</pre>";







// PARTIE COPIEE DE bull_index.php PAS ENCORE ADAPTEE

			$tableau_id_classe_eleve=array();
			$tableau_periodes_eleve=array();
			for($loop_classe=0;$loop_classe<count($tab_id_classe);$loop_classe++) {
				$id_classe=$tab_id_classe[$loop_classe];
				for($loop_periode_num=0;$loop_periode_num<count($tab_periode_num);$loop_periode_num++) {
					$tmp_tab_select=isset($_POST['tab_selection_ele_'.$loop_classe.'_'.$loop_periode_num]) ? $_POST['tab_selection_ele_'.$loop_classe.'_'.$loop_periode_num] : NULL;
					if(isset($tmp_tab_select)) {
						for($j=0;$j<count($tableau_eleve['login']);$j++) {
							if(in_array($tableau_eleve['login'][$j], $tmp_tab_select)) {
								if(!isset($tableau_id_classe_eleve[$tableau_eleve['login'][$j]])) {
									$tableau_id_classe_eleve[$tableau_eleve['login'][$j]]=$id_classe;
								}
								$tableau_periodes_eleve[$tableau_eleve['login'][$j]][]=$tab_periode_num[$loop_periode_num];
							}
						}
					}
				}

				// Prendre en compte les intervalles de dates
				$tmp_tab_select=isset($_POST['tab_selection_ele_'.$loop_classe.'_intervalle']) ? $_POST['tab_selection_ele_'.$loop_classe.'_intervalle'] : NULL;
				if(isset($tmp_tab_select)) {
					for($j=0;$j<count($tableau_eleve['login']);$j++) {
						if(in_array($tableau_eleve['login'][$j], $tmp_tab_select)) {
							if(!isset($tableau_id_classe_eleve[$tableau_eleve['login'][$j]])) {
								$tableau_id_classe_eleve[$tableau_eleve['login'][$j]]=$id_classe;
							}
							$tableau_periodes_eleve[$tableau_eleve['login'][$j]][]='';
						}
					}
				}

			}

			if(!isset($tableau_eleve['login'])) {
				echo "<p style='color:red'>Aucun élève n'est sélectionné.</p>";
			}
			else {
				for($j=0;$j<count($tableau_eleve['login']);$j++) {
					if(!isset($tableau_id_classe_eleve[$tableau_eleve['login'][$j]])) {
						echo "<p style='color:red'>L'élève ".$tableau_eleve['login'][$j]." n'a pas de classe.</p>";
					}
					// Modifier le test pour prendre en compte les intervalles de dates
					elseif(!isset($tableau_periodes_eleve[$tableau_eleve['login'][$j]])) {
						echo "<p style='color:red'>L'élève ".$tableau_eleve['login'][$j]." n'a pas été coché sur une seule période.</p>";
					}
					else {
						$id_classe_eleve=$tableau_id_classe_eleve[$tableau_eleve['login'][$j]];
						$chaine_periodes=implode(",", $tableau_periodes_eleve[$tableau_eleve['login'][$j]]);

						// DEBUG
						//echo "\$tableau_id_classe_eleve[\$tableau_eleve['login'][$j]]=\$tableau_id_classe_eleve[".$tableau_eleve['login'][$j]."]=".$tableau_id_classe_eleve[$tableau_eleve['login'][$j]]."<br />";

						if(isset($tableau_resp[$tableau_eleve['login'][$j]]["adresses"]["adresse"])) {
							$tmp_tab_arch_ele_courant=$tableau_resp[$tableau_eleve['login'][$j]]["adresses"]["adresse"];

							foreach($tmp_tab_arch_ele_courant as $tmp_num_resp_destinataire => $current_resp_adr) {

								echo "<pre>";
								print_r($current_resp_adr);
								echo "</pre>";

								$email='';
								if(isset($current_resp_adr['email']) && check_mail($current_resp_adr['email'])) {
									$email=$current_resp_adr['email'];
								}
								elseif(isset($current_resp_adr['mel']) && check_mail($current_resp_adr['mel'])) {
									$email=$current_resp_adr['mel'];
								}

								$sql="INSERT INTO releve_cn_mail SET login_sender='".$_SESSION['login']."', 
													pers_id='".$current_resp_adr['pers_id']."',
													email='".$email."',
													login_ele='".$tableau_eleve['login'][$j]."', 
													nom_prenom_ele='".mysqli_real_escape_string($mysqli, $tableau_eleve['nom_prenom'][$j])."', 
													id_classe='".$id_classe_eleve."',
													periodes='".$chaine_periodes."',
													date_debut='".$display_date_debut."',
													date_fin='".$display_date_fin."',
													id_envoi='".$id_envoi_rel."',
													envoi='en_attente',
													date_envoi='"."';";
								echo "$sql<br />";
								$insert=mysqli_query($mysqli, $sql);
								if(!$insert) {
									echo "<p style='color:red'>ERREUR lors de l'enregistrement de l'envoi à effectuer.</p>";
								}
								else {
									echo "<p>Enregistrement de la préparation d'envoi pour ".$tableau_eleve['nom_prenom'][$j]." <span title='Responsable n°".$current_resp_adr['pers_id']."'>(".$current_resp_adr['pers_id'].")</span></p>";
								}

							}
						}
						else {
							echo "<p style='color:red'>L'élève ".$tableau_eleve['login'][$j]." n'a pas d'adresse responsable à laquelle envoyer le mail.</p>";
						}

					}
				}

				echo "
		<p><input type='submit' value='Suite...' /></p>";
			}

			echo "
	</fieldset>
</form>";

		}
		else {

			// envoi_rel_par_mail == 2 -> suite/boucle par tranches de N envois
			$id_envoi_rel=isset($_POST['id_envoi_rel']) ? $_POST['id_envoi_rel'] : NULL;
			if(!isset($id_envoi_rel)) {
				echo "<h2>Envoi de relevés de notes par mail</h2>
				<p style='color:red'>ERREUR&nbsp;: L'identifiant de l'envoi par mail n'a pas été reçu.</p>";
				require("../lib/footer.inc.php");
				die();
			}

			$dirname = "../temp/".get_user_temp_directory()."/".$id_envoi_rel;
			@mkdir($dirname);
			if(!file_exists($dirname)) {
				echo "<h2>Envoi de relevés de notes par mail</h2>
				<p>ERREUR d'acces au dossier des relevés de notes&nbsp;: $dirname</p>";
				die();
			}

			// Pour ne pas laisser de scories
			vider_dir($dirname);


			$sql="SELECT * FROM releve_cn_mail WHERE login_sender='".$_SESSION['login']."' AND 
									id_envoi='".$id_envoi_rel."' AND 
									envoi='en_attente' 
								ORDER BY id_classe, nom_prenom_ele 
								LIMIT 10;";
			//echo "$sql<br />";
			$res=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res)>0) {
				echo "
	<form action='".$_SERVER['PHP_SELF']."' method='POST'>
		<fieldset class='fieldset_opacite50'>
			<h2>Envoi de relevés de notes par mail</h2>
			<input type='hidden' name='envoi_rel_par_mail' value ='2' />
			<input type='hidden' name='mode_bulletin' value ='pdf' />";
				// Liste des paramètres:
				// - classes
				// - périodes
				// - dates
				// - élèves sélectionnés
				// - paramètres choisis dans le tableau des paramètres des relevés

				echo (isset($use_cell_ajustee) ? "<input type='hidden' name='use_cell_ajustee' value ='".$use_cell_ajustee."' />" : '')."
					<input type='hidden' name='nb_releve_par_page' value ='1' />";

				$tmp_tab_param=array('tab_periode_num', 'rn_aff_classe_nom', 'rn_nomdev', 'rn_aff_nomdev_choix', 'rn_toutcoefdev', 'rn_coefdev_si_diff', 'rn_datedev', 'rn_col_moy', 'rn_sign_pp', 'rn_sign_resp', 'rn_app', 'rn_moy_classe', 'rn_moy_min_max_classe', 'rn_retour_ligne', 'rn_rapport_standard_min_font', 'rn_adr_resp', 'rn_bloc_obs', 'rn_sign_nblig', 'rn_formule', 'tab_id_classe');
				foreach($tmp_tab_param as $key0 => $value0) {
					if(isset($_POST[$value0])) {
						foreach($_POST[$value0] as $key => $value) {
							echo "
							<input type='hidden' name='".$value0."[".$key."]' value ='".$value."' />";
						}
					}
				}


				$tmp_tab_param2=array('choix_periode', 'display_date_debut', 'display_date_fin', 'chaine_coef', 'rn_couleurs_alternees', 'valide_select_eleves', 'choix_parametres');
				foreach($tmp_tab_param2 as $key0 => $value0) {
					if(isset($_POST[$value0])) {
						echo "
						<input type='hidden' name='".$value0."' value ='".$_POST[$value0]."' />";
					}
				}

				$id_envoi_rel=$_SESSION['login'].'_'.time();
				echo "
			<input type='hidden' name='id_envoi_rel' value ='".$id_envoi_rel."' />";


			foreach($tab_id_classe as $key0 => $value0) {
				foreach($tab_periode_num as $key1 => $value1) {
					if(isset($_POST['tab_selection_ele_'.$key0.'_'.$key1])) {
						foreach($_POST['tab_selection_ele_'.$key0.'_'.$key1] as $key2 => $value2) {
							echo "
			<input type='hidden' name='tab_selection_ele_".$key0.'_'.$key1."[$key2]' value ='".$value2."' />";
						}
					}
				}

				if(isset($_POST['tab_selection_ele_'.$key0.'_intervalle'])) {
					foreach($_POST['tab_selection_ele_'.$key0.'_intervalle'] as $key2 => $value2) {
						echo "
			<input type='hidden' name='tab_selection_ele_".$key0."_intervalle[$key2]' value ='".$value2."' />";
					}
				}
			}














				//========================================
				// Extraction des données externalisée pour permettre un appel depuis la génération de bulletins de façon à intercaler les relevés de notes entre les bulletins

				// Il faudrait voir comment restreindre l'extraction aux élèves traités sur ce passage
				include("extraction_donnees_releves_notes.php");
				//========================================


				$id_classe_prec='';
				while($lig=mysqli_fetch_object($res)) {
					// La boucle se fait avec un ORDER BY sur id_classe d'abord
					// Tester un changement d'id_classe pour refaire calcul_moy_gen






					// Rechercher l'indice $j de $tableau_eleve['nom_prenom'][$j] ?
					/*
					echo "Recherche de ".$lig->login_ele." dans \$tableau_eleve['login']<br />";
					echo "\$tableau_eleve<pre>";
					print_r($tableau_eleve);
					echo "</pre>";
					*/

					$j='';
					for($k=0;$k<count($tableau_eleve['login']);$k++) {
						//echo "\$tableau_eleve['login'][$k]=".$tableau_eleve['login'][$k]."<br />";
						if($tableau_eleve['login'][$k]==$lig->login_ele) {
							//echo "Trouvé.<br />";
							$j=$k;
							break;
						}
					}

					// Si $j==0, on a un test $j=='' qui est considéré comme vrai
					if("$j"=='') {
						echo "<p style='color:red'>".$lig->login_ele." non trouvé.</p>";
					}
					else {
						if(!isset($tab_classe[$lig->id_classe])) {
							$tab_classe[$lig->id_classe]=get_valeur_champ("classes", "id='".$lig->id_classe."'", "classe");
						}




// A FAIRE :
// DEUX CAS : PERIODES OU INTERVALLE DE DATES




						$tmp_tab_periodes_ele=explode(',', $lig->periodes);



						//==================================
						if(($_POST['choix_periode']!='intervalle')&&(isset($afficher_ligne_moy_gen))&&($afficher_ligne_moy_gen=='y')&&($lig->id_classe!=$id_class_prec)) {
							$id_classe_prec=$lig->id_classe;
							$id_classe=$lig->id_classe;

							unset($tab_calcul_moy_gen);
							$tab_calcul_moy_gen=array();

							// A FAIRE : VERIFIER COMMENT CA SE PASSE AVEC LES ELEVES CHANGEANT DE CLASSE ENTRE DEUX PERIODES

							for($loop_periode_num=0;$loop_periode_num<count($tmp_tab_periodes_ele);$loop_periode_num++) {

								$periode_num=$tmp_tab_periodes_ele[$loop_periode_num];

								//==============================
								if($mode_bulletin!="pdf") {
									echo "<script type='text/javascript'>
						document.getElementById('td_periode').innerHTML='".$periode_num."';
					</script>\n";
									flush();
								}
								//==============================

								// 20171229
								if(($periode_num!='intervalle')&&(isset($afficher_ligne_moy_gen))&&($afficher_ligne_moy_gen=='y')) {
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

									$affiche_graph='n';
									$calculer_moy_gen_pour_carnets_de_notes=true;
									include("../lib/calcul_moy_gen.inc.php");






									$tab_calcul_moy_gen['moy_gen_eleve'][$periode_num]=$moy_gen_eleve;
									$tab_calcul_moy_gen['moy_gen_classe'][$periode_num]=$moy_gen_classe;
									$tab_calcul_moy_gen['moy_generale_classe'][$periode_num]=$moy_generale_classe;
									$tab_calcul_moy_gen['moy_max_classe'][$periode_num]=$moy_max_classe;
									$tab_calcul_moy_gen['moy_min_classe'][$periode_num]=$moy_min_classe;

									$tab_calcul_moy_gen['moy_cat_classe'][$periode_num]=$moy_cat_classe;
									$tab_calcul_moy_gen['moy_cat_eleve'][$periode_num]=$moy_cat_eleve;

									$tab_calcul_moy_gen['quartile1_classe_gen'][$periode_num]=$quartile1_classe_gen;
									$tab_calcul_moy_gen['quartile2_classe_gen'][$periode_num]=$quartile2_classe_gen;
									$tab_calcul_moy_gen['quartile3_classe_gen'][$periode_num]=$quartile3_classe_gen;
									$tab_calcul_moy_gen['quartile4_classe_gen'][$periode_num]=$quartile4_classe_gen;
									$tab_calcul_moy_gen['quartile5_classe_gen'][$periode_num]=$quartile5_classe_gen;
									$tab_calcul_moy_gen['quartile6_classe_gen'][$periode_num]=$quartile6_classe_gen;
									$tab_calcul_moy_gen['place_eleve_classe'][$periode_num]=$place_eleve_classe;

									$tab_calcul_moy_gen['current_eleve_login'][$periode_num]=$current_eleve_login;
									$tab_calcul_moy_gen['current_group'][$periode_num]=$current_group;
									$tab_calcul_moy_gen['current_eleve_note'][$periode_num]=$current_eleve_note;
									$tab_calcul_moy_gen['current_eleve_statut'][$periode_num]=$current_eleve_statut;
									$tab_calcul_moy_gen['current_coef'][$periode_num]=$current_coef;
									$tab_calcul_moy_gen['categories'][$periode_num]=$categories;
									$tab_calcul_moy_gen['current_classe_matiere_moyenne'][$periode_num]=$current_classe_matiere_moyenne;

									$tab_calcul_moy_gen['current_coef_eleve'][$periode_num]=$current_coef_eleve;
									$tab_calcul_moy_gen['moy_min_classe_grp'][$periode_num]=$moy_min_classe_grp;
									$tab_calcul_moy_gen['moy_max_classe_grp'][$periode_num]=$moy_max_classe_grp;
									$tab_calcul_moy_gen['current_eleve_rang'][$periode_num]=$current_eleve_rang;

									$tab_calcul_moy_gen['current_group_effectif_avec_note'][$periode_num]=$current_group_effectif_avec_note;

									$tab_calcul_moy_gen['current_eleve_app'][$periode_num]=$current_eleve_app;


									/*
									// DEBUG: 20180414
									if($periode_num==3) {
										echo "<div style='float:left;width:30em;'><pre>";
										print_r($current_eleve_login);
										echo "</pre></div>";
										echo "<div style='float:left;width:30em;'><pre>";
										print_r($moy_gen_eleve);
										echo "</pre></div>";
									}
									*/
								}
							}
						}
						//==================================






						/*
						echo "\$tableau_resp[$lig->login_ele]<pre>";
						print_r($tableau_resp[$lig->login_ele]);
						echo "</pre>";
						*/
						foreach($tableau_resp[$lig->login_ele]["adresses"]["adresse"] as $tmp_num_resp_destinataire => $current_resp_adr) {
							
							//echo "<pre>";
							//print_r($current_resp_adr);
							//echo "</pre>";

							if($current_resp_adr['pers_id']==$lig->pers_id) {
								$nom_fichier_releve_notes='releve_de_notes';
								$nom_fichier_releve_notes.='_'.$tableau_eleve['nom_prenom'][$j];
								$nom_fichier_releve_notes.='_'.$tableau_eleve['no_gep'][$j];
								$nom_fichier_releve_notes.="_annee_scolaire_".remplace_accents(getSettingValue('gepiYear'),"all");
								$nom_fichier_releve_notes.="_".strftime("%Y%m%d");
								$nom_fichier_releve_notes.="_".remplace_accents($tab_classe[$lig->id_classe], "all");

								$nom_fichier_releve_notes.='_resp_legal_'.$current_resp_adr["resp_legal"]."_".$current_resp_adr["pers_id"];
								$nom_fichier_releve_notes.='.pdf';

								/*
								//création du PDF en mode Portrait, unitée de mesure en mm, de taille A4
								$pdf=new bul_PDF('p', 'mm', 'A4');
								$nb_eleve_aff = 1;
								$categorie_passe = '';
								$categorie_passe_count = 0;
								$pdf->SetCreator($gepiSchoolName);
								$pdf->SetAuthor($gepiSchoolName);
								$pdf->SetKeywords('');
								$pdf->SetSubject('Relevé de notes');
								$pdf->SetTitle('Relevé de notes');
								$pdf->SetDisplayMode('fullwidth', 'single');
								$pdf->SetCompression(TRUE);
								$pdf->SetAutoPageBreak(TRUE, 5);

								$responsable_place = 0;
								*/

								// définition d'une variable
								$hauteur_pris = 0;

								/*****************************************
								* début de la génération du fichier PDF  *
								* ****************************************/
								//header('Content-type: application/pdf');
								//création du PDF en mode Portrait, unitée de mesure en mm, de taille A4
								$pdf=new bul_PDF('p', 'mm', 'A4');
								$nb_eleve_aff = 1;
								$categorie_passe = '';
								$categorie_passe_count = 0;
								$pdf->SetCreator($gepiSchoolName);
								$pdf->SetAuthor($gepiSchoolName);
								$pdf->SetKeywords('');
								$pdf->SetSubject('Releve_de_notes');
								$pdf->SetTitle('Releve_de_notes');
								$pdf->SetDisplayMode('fullwidth', 'single');
								//$pdf->SetCompression(TRUE);
								$pdf->SetAutoPageBreak(TRUE, 5);

								$responsable_place = 0;









								// A faire: Forcer 1 seul bulletin par parent

								for($loop_classe=0;$loop_classe<count($tab_id_classe);$loop_classe++) {
									$id_classe=$tab_id_classe[$loop_classe];
									$classe=get_class_from_id($id_classe);

									//if($arch_bull_signature=="yes") {
										if(!isset($tab_signature_classe[$id_classe])) {
											$tab_signature_classe[$id_classe]=get_tab_signature_bull_archivage($id_classe);
										}

										if(isset($tab_signature_classe[$id_classe]['fichier'][0]['chemin'])) {
											$signature_bull[$id_classe]=$tab_signature_classe[$id_classe]['fichier'][0]['chemin'];
											/*
											echo "\$signature_bull[$id_classe]<pre>";
											print_r($signature_bull[$id_classe]);
											echo "</pre>";
											*/
										}
									//}

									for($loop_periode_num=0;$loop_periode_num<count($tab_periode_num);$loop_periode_num++) {
										$periode_num=$tab_periode_num[$loop_periode_num];

										if(in_array($periode_num, $tmp_tab_periodes_ele)) {
											// Recherche de l'indice $i de l'élève dans le tableau tab_bulletin pour la période considérée.
											for($i=0;$i<$tab_bulletin[$id_classe][$periode_num]['eff_classe'];$i++) {

												if(isset($tab_bulletin[$id_classe][$periode_num]['selection_eleves'])) {
													if((isset($tab_bulletin[$id_classe][$periode_num]['eleve'][$i]['login']))&&($tab_bulletin[$id_classe][$periode_num]['eleve'][$i]['login']==$tableau_eleve['login'][$j])) {






														if(($_POST['choix_periode']!='intervalle')&&(isset($afficher_ligne_moy_gen))&&($afficher_ligne_moy_gen=='y')&&($lig->id_classe!=$id_class_prec)) {


/*

									$tab_calcul_moy_gen['moy_gen_eleve'][$periode_num]=$moy_gen_eleve;
									$tab_calcul_moy_gen['moy_gen_classe'][$periode_num]=$moy_gen_classe;
									$tab_calcul_moy_gen['moy_generale_classe'][$periode_num]=$moy_generale_classe;
									$tab_calcul_moy_gen['moy_max_classe'][$periode_num]=$moy_max_classe;
									$tab_calcul_moy_gen['moy_min_classe'][$periode_num]=$moy_min_classe;

									$tab_calcul_moy_gen['moy_cat_classe'][$periode_num]=$moy_cat_classe;
									$tab_calcul_moy_gen['moy_cat_eleve'][$periode_num]=$moy_cat_eleve;

									$tab_calcul_moy_gen['quartile1_classe_gen'][$periode_num]=$quartile1_classe_gen;
									$tab_calcul_moy_gen['quartile2_classe_gen'][$periode_num]=$quartile2_classe_gen;
									$tab_calcul_moy_gen['quartile3_classe_gen'][$periode_num]=$quartile3_classe_gen;
									$tab_calcul_moy_gen['quartile4_classe_gen'][$periode_num]=$quartile4_classe_gen;
									$tab_calcul_moy_gen['quartile5_classe_gen'][$periode_num]=$quartile5_classe_gen;
									$tab_calcul_moy_gen['quartile6_classe_gen'][$periode_num]=$quartile6_classe_gen;
									$tab_calcul_moy_gen['place_eleve_classe'][$periode_num]=$place_eleve_classe;

									$tab_calcul_moy_gen['current_eleve_login'][$periode_num]=$current_eleve_login;
									$tab_calcul_moy_gen['current_group'][$periode_num]=$current_group;
									$tab_calcul_moy_gen['current_eleve_note'][$periode_num]=$current_eleve_note;
									$tab_calcul_moy_gen['current_eleve_statut'][$periode_num]=$current_eleve_statut;
									$tab_calcul_moy_gen['current_coef'][$periode_num]=$current_coef;
									$tab_calcul_moy_gen['categories'][$periode_num]=$categories;
									$tab_calcul_moy_gen['current_classe_matiere_moyenne'][$periode_num]=$current_classe_matiere_moyenne;

									$tab_calcul_moy_gen['current_coef_eleve'][$periode_num]=$current_coef_eleve;
									$tab_calcul_moy_gen['moy_min_classe_grp'][$periode_num]=$moy_min_classe_grp;
									$tab_calcul_moy_gen['moy_max_classe_grp'][$periode_num]=$moy_max_classe_grp;
									$tab_calcul_moy_gen['current_eleve_rang'][$periode_num]=$current_eleve_rang;

									$tab_calcul_moy_gen['current_group_effectif_avec_note'][$periode_num]=$current_group_effectif_avec_note;

									$tab_calcul_moy_gen['current_eleve_app'][$periode_num]=$current_eleve_app;
*/

															}

														bulletin_pdf($tab_bulletin[$id_classe][$periode_num],$i,$tab_releve[$id_classe][$periode_num]);
													}
												}
											}
										}
									}
								}

								echo $pdf->Output($dirname."/".$nom_fichier_releve_notes,'F');
								echo "<p><a href='$dirname/$nom_fichier_releve_notes' target='_blank'>$nom_fichier_releve_notes</a>";
								if(isset($current_resp_adr["email"])) {
									// 20170714 : On envoie les bulletins pour $current_resp_adr["email"][]
									for($loop_mail=0;$loop_mail<count($current_resp_adr["email"]);$loop_mail++) {

										$destinataire_mail="";
										$tab_param_mail=array();
										if(check_mail($current_resp_adr["email"][$loop_mail])) {
											$destinataire_mail.=$current_resp_adr["email"][$loop_mail];
											$tab_param_mail['destinataire'][]=$current_resp_adr["email"][$loop_mail];
										}
										else {
											$sql="UPDATE releve_cn_mail SET envoi='mail_non_valide' WHERE id='".$lig->id."';";
											//echo "$sql<br />";
											$update=mysqli_query($mysqli, $sql);
										}

										if($destinataire_mail!="") {
											$sujet_mail="Envoi du bulletin ".$nom_fichier_releve_notes;
											$message_mail="Bonjour ".$current_resp_adr[1].",

Veuillez trouver en pièce jointe le bulletin ".$nom_fichier_releve_notes."


Bien cordialement.
-- 
".getSettingValue('gepiSchoolName');

											// On enlève le préfixe ../ parce que le chemin absolu est reconstruit via SERVER_ROOT dans envoi_mail()
											if(envoi_mail($sujet_mail, $message_mail, $destinataire_mail, '', "plain", $tab_param_mail, preg_replace("#^\.\./#", "", $dirname."/".$nom_fichier_releve_notes))) {
												echo " <em style='color:green' title=\"Mail envoyé avec succès pour ".$current_resp_adr[1]."\">(".$destinataire_mail.")</em>";
												$sql="UPDATE releve_cn_mail SET envoi='succes' WHERE id='".$lig->id."';";
												//echo "$sql<br />";
												$update=mysqli_query($mysqli, $sql);
											}
											else {
												echo " <em style='color:red' title=\"Échec de l'envoi du mail ".$current_resp_adr[1]."\">(".$destinataire_mail.")</em>";
												$sql="UPDATE releve_cn_mail SET envoi='echec' WHERE id='".$lig->id."';";
												//echo "$sql<br />";
												$update=mysqli_query($mysqli, $sql);
											}
										}
									}

								}
								else {
									$sql="UPDATE releve_cn_mail SET envoi='mail_non_valide' WHERE id='".$lig->id."';";
									//echo "$sql<br />";
									$update=mysqli_query($mysqli, $sql);
								}
								echo "</p>\n";

								flush();
							}
						}
					}
				}

				echo "<p><input type='submit' value='Suite...' /></p>
	</fieldset>
</form>";

















			}
			else {
				// LES ENVOIS SONT ACHEVES
				// IL FAUT LISTER LES RELEVES NON ENVOYES






			}




























		}


		require("../lib/footer.inc.php");
		die();
	}

	// Pour mémoriser le temps de la session ces paramètres
	$_SESSION['pref_use_cell_ajustee']=$use_cell_ajustee;
	$_SESSION['pref_un_seul_bull_par_famille']=$un_seul_bull_par_famille;
	$_SESSION['pref_deux_releves_par_page']=$deux_releves_par_page;
	$_SESSION['pref_tri_par_etab_orig']=$tri_par_etab_orig;

	// Prof principal
	//$gepi_prof_suivi=getSettingValue("gepi_prof_suivi");

	if($mode_bulletin!="pdf") {
		echo "<div id='infodiv'>
<p id='titre_infodiv' style='font-weight:bold; text-align:center; border:1px solid black;'></p>
<table class='boireaus'  style='width:100%;' summary=\"Tableau de déroulement de l'extraction/génération\">
<tr>
<th colspan='3' id='td_info'></th>
</tr>
<tr>
<th style='width:33%;'>Classe</th>
<th style='width:33%;'>Période</th>
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

	//========================================
	// Extraction des données externalisée pour permettre un appel depuis la génération de bulletins de façon à intercaler les relevés de notes entre les bulletins
	include("extraction_donnees_releves_notes.php");
	//========================================

	// DEBUG:
	/*
	echo "\$tab_releve[$id_classe][$periode_num]['eleve'][0]['groupe'][0]['id_cn'][2367]['conteneurs'][0]['moy']=".$tab_releve[$id_classe][$periode_num]['eleve'][0]['groupe'][0]['id_cn'][2367]['conteneurs'][0]['moy']."<br />\n";
	echo "\$tab_releve[$id_classe][$periode_num]['eleve'][0]['groupe'][0]['devoir'][1]['note']=".$tab_releve[$id_classe][$periode_num]['eleve'][0]['groupe'][0]['devoir'][1]['note']."<br />\n";
	echo "\$tab_releve[$id_classe][$periode_num]['eleve'][0]['groupe'][0]['devoir'][1]['statut']=".$tab_releve[$id_classe][$periode_num]['eleve'][0]['groupe'][0]['devoir'][1]['statut']."<br />\n";
	*/

	//========================================================================
	// A CE STADE LE TABLEAU $tab_releve EST RENSEIGNé
	// PLUS AUCUNE REQUETE NE DEVRAIT ETRE NECESSAIRE
	// OU ALORS IL FAUDRAIT LES EFFECTUER AU-DESSUS ET COMPLETER $tab_releve
	//
	// IL Y AURA A RENSEIGNER $tab_releve[$id_classe][$periode_num]['modele_pdf']
	// SI ON FAIT UNE IMPRESSION DE RELEVE PDF, POUR NE PAS REFAIRE LES REQUETES
	// POUR CHAQUE ELEVE.
	//========================================================================

	if($mode_bulletin!="pdf") {
		echo "<script type='text/javascript'>
	document.getElementById('td_info').innerHTML='Affichage';
</script>\n";
	}
	else {
		// définition d'une variable
		$hauteur_pris = 0;

		/*****************************************
		* début de la génération du fichier PDF  *
		* ****************************************/
		//header('Content-type: application/pdf');
		//création du PDF en mode Portrait, unitée de mesure en mm, de taille A4
		$pdf=new bul_PDF('p', 'mm', 'A4');
		$nb_eleve_aff = 1;
		$categorie_passe = '';
		$categorie_passe_count = 0;
		$pdf->SetCreator($gepiSchoolName);
		$pdf->SetAuthor($gepiSchoolName);
		$pdf->SetKeywords('');
		$pdf->SetSubject('Releve_de_notes');
		$pdf->SetTitle('Releve_de_notes');
		$pdf->SetDisplayMode('fullwidth', 'single');
		//$pdf->SetCompression(TRUE);
		$pdf->SetAutoPageBreak(TRUE, 5);

		$responsable_place = 0;
	}

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

	// 20180819
	$message_acces_non_ouvert="<img src='$gepiPath/images/disabled.png' class='icone16' title='Accès non encore ouvert aux élèves/parents' />";

	$acces_moy_ele_resp_cn=getSettingValue('acces_moy_ele_resp_cn');
	if($acces_moy_ele_resp_cn!='immediat') {
		$acces_moy_ele_resp=getSettingValue('acces_moy_ele_resp');

		if($acces_moy_ele_resp!='immediat') {
			if($_SESSION['statut']=='eleve') {
				$acces_moy='n';

				// Récupérer les id_classe de l'élève
				$sql="SELECT DISTINCT id_classe FROM j_eleves_classes WHERE login='".$_SESSION['login']."';";
				$res_clas_ele=mysqli_query($GLOBALS['mysqli'], $sql);
				if(mysqli_num_rows($res_clas_ele)>0) {
					while($lig_clas_ele=mysqli_fetch_object($res_clas_ele)) {

						$sql="SELECT MAX(num_periode) AS tmp_max_per FROM periodes WHERE id_classe='".$lig_clas_ele->id_classe."';";
						//echo "$sql<br />";
						$res_clas_ele_per=mysqli_query($GLOBALS['mysqli'], $sql);
						if(mysqli_num_rows($res_clas_ele_per)>0) {
							$lig_clas_ele_per=mysqli_fetch_object($res_clas_ele_per);

							$tab_acces_app[$lig_clas_ele->id_classe]=acces_appreciations(1, $lig_clas_ele_per->tmp_max_per, $lig_clas_ele->id_classe, '', $_SESSION['login']);

							foreach($tab_acces_app[$lig_clas_ele->id_classe] as $tmp_per => $tmp_acces) {
								$tab_acces_moy[$_SESSION['login']][$lig_clas_ele->id_classe][$tmp_per]=$tab_acces_app[$lig_clas_ele->id_classe][$tmp_per];
							}
						}
					}
				}
			}
			elseif($_SESSION['statut']=='responsable') {
				$tmp_tab_resp_ele=get_enfants_from_resp_login($_SESSION['login'], 'simple', 'y');
				for($loop_ele=0;$loop_ele<count($tmp_tab_resp_ele);$loop_ele+=2) {
					// Récupérer les id_classe de l'élève
					$sql="SELECT DISTINCT id_classe FROM j_eleves_classes WHERE login='".$tmp_tab_resp_ele[$loop_ele]."';";
					$res_clas_ele=mysqli_query($GLOBALS['mysqli'], $sql);
					if(mysqli_num_rows($res_clas_ele)>0) {
						while($lig_clas_ele=mysqli_fetch_object($res_clas_ele)) {

							$sql="SELECT MAX(num_periode) AS tmp_max_per FROM periodes WHERE id_classe='".$lig_clas_ele->id_classe."';";
							$res_clas_ele_per=mysqli_query($GLOBALS['mysqli'], $sql);
							if(mysqli_num_rows($res_clas_ele_per)>0) {
								$lig_clas_ele_per=mysqli_fetch_object($res_clas_ele_per);

								$tab_acces_app[$lig_clas_ele->id_classe]=acces_appreciations(1, $lig_clas_ele_per->tmp_max_per, $lig_clas_ele->id_classe, '', $tmp_tab_resp_ele[$loop_ele]);

								foreach($tab_acces_app[$lig_clas_ele->id_classe] as $tmp_per => $tmp_acces) {
									$tab_acces_moy[$tmp_tab_resp_ele[$loop_ele]][$lig_clas_ele->id_classe][$tmp_per]=$tab_acces_app[$lig_clas_ele->id_classe][$tmp_per];
								}
							}
						}
					}
				}
				/*
				echo "<pre>";
				print_r($tab_acces_moy);
				echo "</pre>";
				*/
			}
			else {
				$acces_moy='y';
			}
		}
		else {
			$acces_moy='y';
		}
	}
	else {
		$acces_moy='y';
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

		if($mode_bulletin!="pdf") {
			echo "<script type='text/javascript'>
	document.getElementById('td_classe').innerHTML='".$classe."';
</script>\n";
		}

		// 20171229
		if(getSettingAOui('cn_affiche_moy_gen')) {
			$afficher_ligne_moy_gen=getParamClasse($id_classe, 'rn_moy_gen', 'n');
		}
		else {
			$afficher_ligne_moy_gen='n';
		}
		//echo "\$afficher_ligne_moy_gen=$afficher_ligne_moy_gen<br />";

		//20190101
		//$acces_adresse_responsable;
		$acces_adresse_responsable=get_acces_adresse_resp('', $id_classe);
		//echo "\$acces_adresse_responsable=$acces_adresse_responsable<br />";

		//==========================================
		/*
		// 20180819
		// $tmp_max_per à récupérer pour $id_classe
		if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
			$acces_moy_ele_resp_cn=getSettingValue('acces_moy_ele_resp_cn');
			if($acces_moy_ele_resp_cn!='immediat') {
				$acces_moy_ele_resp=getSettingValue('acces_moy_ele_resp');

				if($acces_moy_ele_resp!='immediat') {

					// PB: il faudrait extraire les tableaux pour chacun des élèves du responsable
					$tab_acces_app[$id_classe]=acces_appreciations(1, $tmp_max_per, $id_classe, '', $ele_login);

					foreach($tab_acces_app as $tmp_per => $tmp_acces) {
						$tab_acces_moy[$tmp_per]=$tab_acces_app[$tmp_per];
					}

				}
				else {
					foreach($tab_acces_app as $tmp_per => $tmp_acces) {
						$tab_acces_moy[$tmp_per]='y';
					}
				}
			}
			else {
				$tab_acces_moy[$num_periode]='y';
			}
		}
		else {
			$tab_acces_app[$id_classe]=acces_appreciations(1, $tmp_max_per, $id_classe, '', $ele_login);
		}
		*/
		//==========================================



		for($loop_periode_num=0;$loop_periode_num<count($tab_periode_num);$loop_periode_num++) {

			$periode_num=$tab_periode_num[$loop_periode_num];

			//==============================
			if($mode_bulletin!="pdf") {
				echo "<script type='text/javascript'>
	document.getElementById('td_periode').innerHTML='".$periode_num."';
</script>\n";
				flush();
			}
			//==============================

			// 20171229
			if(($periode_num!='intervalle')&&(isset($afficher_ligne_moy_gen))&&($afficher_ligne_moy_gen=='y')) {
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

				$affiche_graph='n';
				$calculer_moy_gen_pour_carnets_de_notes=true;
				include("../lib/calcul_moy_gen.inc.php");

				/*
				// DEBUG: 20180414
				if($periode_num==3) {
					echo "<div style='float:left;width:30em;'><pre>";
					print_r($current_eleve_login);
					echo "</pre></div>";
					echo "<div style='float:left;width:30em;'><pre>";
					print_r($moy_gen_eleve);
					echo "</pre></div>";
				}
				*/
			}

			if(($mode_bulletin!="pdf")&&($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
				echo "<div class='noprint' style='background-color:white; border: 1px solid red;'>\n";
				echo "<h2>Classe de ".$classe."</h2>\n";
				if($periode_num=="intervalle") {
					echo "<p><strong>Du $display_date_debut au $display_date_fin</strong></p>\n";
				}
				else {
					echo "<p><strong>Période $periode_num</strong></p>\n";
				}

				echo "<p>Effectif de la classe: ".$tab_releve[$id_classe][$periode_num]['eff_classe']."</p>\n";
				//echo "</div>\n";
			}

			//if(!isset($tab_releve[$id_classe][$periode_num]['eleve'])) {
			if((!isset($tab_releve[$id_classe][$periode_num]['eleve']))&&($mode_bulletin!="pdf")) {
				echo "<p>Aucun élève sélectionné/coché dans cette classe pour cette période.</p>\n";
				echo "</div>\n";
			}
			else {

				if(($mode_bulletin!="pdf")&&($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
					//+++++++++++++++++++++++++++++++++++
					// A FAIRE: Il faudrait afficher l'effectif des élèves choisis faisant partie de la classe/période...
					echo "<p>".count($tab_releve[$id_classe][$periode_num]['eleve'])." élève(s) sélectionné(s) dans cette classe (<em>pour cette période</em>).</p>\n";
					//+++++++++++++++++++++++++++++++++++
	
					echo "</div>\n";
				}
				/*
				echo "DEBUG : 1837
				<pre>";
				print_r($tab_releve);
				echo "</pre>";
				*/
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
										echo "<script type='text/javascript'>
	document.getElementById('td_ele').innerHTML='".$tab_releve[$id_classe][$periode_num]['eleve'][$rg[$i]]['login']."';
</script>\n";
										flush();
	
										// Saut de page si jamais ce n'est pas le premier bulletin
										//if($compteur_releve>0) {echo "<p class='saut'>&nbsp;</p>\n";}
										if($compteur_releve_bis>0) {echo "<p class='saut'>&nbsp;</p>\n";}
	
										// Génération du bulletin de l'élève
										releve_html($tab_releve[$id_classe][$periode_num],$rg[$i],-1);

										$chaine_info_deux_releves="";
										if(($un_seul_bull_par_famille=="non")&&($nb_releves>1)&&($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
											$chaine_info_deux_releves=".<br /><span style='color:red'>Plusieurs relevés pour une même famille&nbsp: les adresses des deux responsables diffèrent.</span><br /><span style='color:red'>Si vous ne souhaitez pas de deuxième relevé, pensez à cocher la case 'Un seul relevé par famille'.</span>";
										}

										if(($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
											echo "<div class='espacement_bulletins'><div align='center'>Espacement (<em>non imprimé</em>) entre les relevés".$chaine_info_deux_releves."</div></div>\n";
										}

										flush();
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
	}



	if($mode_bulletin!="pdf") {
		echo "<script type='text/javascript'>
	document.getElementById('infodiv').style.display='none';
</script>\n";
	}
	else {
		// Envoyer le PDF et quitter
		$nom_releve = date("Ymd_Hi");
		$nom_fichier = 'releve_notes_'.$nom_releve.'.pdf';

		if(((isset($bull_pdf_debug))&&($bull_pdf_debug=='y'))||((isset($releve_pdf_debug))&&($releve_pdf_debug=='y'))) {
			echo $pdf->Output($nom_fichier,'S');
		}
		else {
			$pref_output_mode_pdf=get_output_mode_pdf();
			$pdf->Output($nom_fichier, $pref_output_mode_pdf);
		}

		die();
	}

}

require("../lib/footer.inc.php");
?>
