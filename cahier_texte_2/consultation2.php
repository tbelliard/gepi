<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer, Stephane Boireau
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

function aff_debug($tableau){
	echo '<pre>';
	print_r($tableau);
	echo '</pre>';
}

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

if (getSettingValue("GepiCahierTexteVersion") != '2') {
	tentative_intrusion(1, "Tentative d'accès au cahier de textes v2 alors qu'il n'est pas ouvert.");
	header("Location: ../cahier_texte/consultation.php");
	die();
}


$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/consultation2.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/cahier_texte_2/consultation2.php',
	administrateur='V',
	professeur='V',
	cpe='V',
	scolarite='V',
	eleve='V',
	responsable='V',
	secours='F',
	autre='V',
	description='Cahiers de textes: Consultation',
	statut='';";
	$insert=mysql_query($sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	tentative_intrusion(1, "Tentative d'accès au cahier de textes en consultation alors que le module n'est pas activé.");
	die("Le module n'est pas activé.");
}

//include "../lib/mincals.inc";

//==========================================================================
// Si la date est transmise, on l'utilise, sinon, on prend la date du jour:
unset($day);
$day = isset($_POST["day"]) ? $_POST["day"] : (isset($_GET["day"]) ? $_GET["day"] : date("d"));
unset($month);
$month = isset($_POST["month"]) ? $_POST["month"] : (isset($_GET["month"]) ? $_GET["month"] : date("m"));
unset($year);
$year = isset($_POST["year"]) ? $_POST["year"] : (isset($_GET["year"]) ? $_GET["year"] : date("Y"));

// Si la date est transmise lors d'un passage à la semaine suivante/précédente:
unset($today);
$today = isset($_POST["today"]) ? $_POST["today"] : (isset($_GET["today"]) ? $_GET["today"] : NULL);
if(isset($today)) {
	$day=strftime("%d",$today);
	$month=strftime("%m",$today);
	$year=strftime("%Y",$today);
}

// Vérification sur les dates: Est-ce une date dans la période d'ouverture des cahiers de textes
settype($month,"integer");
settype($day,"integer");
settype($year,"integer");
$minyear = strftime("%Y", getSettingValue("begin_bookings"));
$maxyear = strftime("%Y", getSettingValue("end_bookings"));

if ($day < 1) {$day = 1;}
if ($day > 31) {$day = 31;}
if ($month < 1) {$month = 1;}
if ($month > 12) {$month = 12;}
if ($year < $minyear) {$year = $minyear;}
if ($year > $maxyear) {$year = $maxyear;}

# Make the date valid if day is more then number of days in month
while (!checkdate($month, $day, $year)) {$day--;}

// Timestamp du jour choisi:
$today=mktime(0,0,0,$month,$day,$year);

// On vérifie que la date demandée est bien comprise entre la date de début des cahiers de texte et la date de fin des cahiers de texte :
if ($today < getSettingValue("begin_bookings")) {
	$today = getSettingValue("begin_bookings");
} else if ($today > getSettingValue("end_bookings")) {
	$today = getSettingValue("end_bookings");
}

// Semaine précédente: Pour tester avec une base pas trop récente...
//$today=$today-3600*24*7;
//==========================================================================

unset($mode);
$mode = isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : NULL);

unset($id_classe);
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : (isset($_GET["id_classe"]) ? $_GET["id_classe"] : NULL);

unset($login_eleve);
$login_eleve = isset($_POST["login_eleve"]) ? $_POST["login_eleve"] : (isset($_GET["login_eleve"]) ? $_GET["login_eleve"] : NULL);

unset($login_prof);
$login_prof = isset($_POST["login_prof"]) ? $_POST["login_prof"] : (isset($_GET["login_prof"]) ? $_GET["login_prof"] : NULL);

$tab_couleur_edt=array('','blue','lime','maroon','purple','red','yellow','aqua','grey','green','olive','teal','#799C13','#4BA829','#D4D600','#FFEC00','#FCC300','#DBAA73','#745A32','#E95D0F','#99141B','#009EE0','#C19CC4');

// Associer aussi des couleurs aux classes (ou aux niveaux?) pour l'affichage prof.
// C'est fait plus loin

$style_specifique[] = "cahier_texte_2/consultation2";
$javascript_specifique[] = "cahier_texte_2/consultation2";

//**************** EN-TETE *****************
$titre_page = "Cahier de textes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

//=============================================================
echo "<p class='bold'>";
echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/>\n";
	echo "Retour à l'accueil\n";
echo "</a>\n";
echo " | ";
if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
	echo "<a href=\"consultation.php";
	if(($_SESSION['statut']=='responsable')&&(isset($login_eleve))) {echo "?login_eleve=$login_eleve";}
	echo "\">\n";
}
else {
	echo "<a href=\"see_all.php\">\n";
}
	echo "Affichage classique\n";
echo "</a>\n";
echo "</p>\n";
//=============================================================

//=============================================================
// Mode d'affichage:
// On force le mode par défaut pour les élèves et responsables
if($_SESSION['statut']=='eleve') {
	$mode='eleve';
	$login_eleve=$_SESSION['login'];
}
elseif($_SESSION['statut']=='responsable') {
	$mode='eleve';

	// On récupère la liste des élèves associés au responsable:
	if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
		$tab_eleve=get_enfants_from_resp_login($_SESSION['login'], "simple", "yy");
	}
	else {
		$tab_eleve=get_enfants_from_resp_login($_SESSION['login']);
	}
	if(count($tab_eleve)==0) {
		echo "<p>Vous n'avez aucun élève en responsabilité&nbsp;???</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	/*
	echo "<pre>";
	echo print_r($tab_eleve);
	echo "</pre>";
	*/
	for($i=0;$i<count($tab_eleve);$i+=2) {
		$tab_eleve_login[]=$tab_eleve[$i];
	}

	// On contrôle que l'élève choisi est bien associé au responsable:
	if((isset($login_eleve))&&(isset($tab_eleve_login))&&(!in_array($login_eleve,$tab_eleve_login))) {
		$login_eleve="";
		// AJOUTER UN APPEL A tentative_intrusion()
	}

	// Initialisation:
	if(!isset($login_eleve)) {$login_eleve="";}

	// On propose le choix de l'élève s'il y a plusieurs élèves associés au responsable
	echo make_eleve_select_html('consultation2.php', $_SESSION['login'], $login_eleve, $year, $month, $day);

	if((!isset($login_eleve))||($login_eleve=='')) {
		// On sélectionne le premier élève de la liste

		if(!isset($tab_eleve[0])) {
			echo "<p>Vous n'avez aucun élève en responsabilité&nbsp;???</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$login_eleve=$tab_eleve[0];
	}

	//echo "\$login_eleve=$login_eleve<br />";
}
else {
	// Proposer le formulaire de choix de mode:

	// Récupération de la liste des profs de l'établissement
	$tab_profs=array();
	$tab_profs2=array();
	$sql="SELECT u.civilite, u.nom, u.prenom, u.login FROM utilisateurs u WHERE etat='actif' AND statut='professeur' ORDER BY u.nom, u.prenom;";
	$res_prof=mysql_query($sql);
	if(mysql_num_rows($res_prof)>0) {
		$cpt=0;
		while($lig_prof=mysql_fetch_object($res_prof)) {
			$tab_profs[$cpt]['login']=$lig_prof->login;
			$tab_profs[$cpt]['civ_nom_prenom']=$lig_prof->civilite." ".casse_mot($lig_prof->nom,'maj')." ".casse_mot($lig_prof->prenom,'majf2');
			$tab_profs2[$lig_prof->login]=$tab_profs[$cpt]['civ_nom_prenom'];
			$cpt++;
		}
	}

	// Récupération de la liste des classes de l'établissement
	$tab_classe=array();
	$sql="SELECT id, classe FROM classes ORDER BY classe;";
	$res_classe=mysql_query($sql);
	if(mysql_num_rows($res_classe)>0) {
		$cpt=0;
		while($lig_class_prof=mysql_fetch_object($res_classe)) {
			$tab_classe[$cpt]['id_classe']=$lig_class_prof->id;
			$tab_classe[$cpt]['classe']=$lig_class_prof->classe;
			$cpt++;
		}
	}

	// Récupération de la liste des classes d'un professeur
	if($_SESSION['statut']=='professeur') {
		$tab_classe_du_prof=array();
		$sql="SELECT c.classe, jgc.id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp, classes c WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' AND jgc.id_classe=c.id ORDER BY c.classe;";

		$res_classe=mysql_query($sql);
		if(mysql_num_rows($res_classe)>0) {
			$cpt=0;
			while($lig_prof=mysql_fetch_object($res_classe)) {
				$tab_classe_du_prof[$cpt]['id_classe']=$lig_prof->id_classe;
				$tab_classe_du_prof[$cpt]['classe']=$lig_prof->classe;
				$cpt++;
			}
		}
	}

	if(isset($id_classe)) {
		$classe=get_class_from_id($id_classe);
	
		// Récupérer la liste des élèves de la classe pour proposer l'affichage pour tel élève
		$tab_eleve_de_la_classe=array();
		$sql="SELECT DISTINCT e.nom, e.prenom, e.login FROM j_eleves_classes jec, eleves e WHERE jec.login=e.login AND jec.id_classe='$id_classe' ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />";
		$res_ele_classe=mysql_query($sql);
		if(mysql_num_rows($res_ele_classe)>0) {
			$cpt=0;
			while($lig_ele=mysql_fetch_object($res_ele_classe)) {
				$tab_eleve_de_la_classe[$cpt]['login']=$lig_ele->login;
				$tab_eleve_de_la_classe[$cpt]['nom_prenom']=casse_mot($lig_ele->nom,'maj')." ".casse_mot($lig_ele->prenom,'majf2');
				$cpt++;
			}
		}
	}

	// Choix par défaut selon le statut:
	if(!isset($mode)) {
		if($_SESSION['statut']=='professeur') {
			$mode='professeur';
		}
		else {
			$mode='classe';
		}
	}

	// Afficher les formulaires de choix pour les non-élève/non-responsable
	if(($_SESSION['statut']!='professeur')||
	(getSettingAOui('GepiAccesCDTToutesClasses'))) {
		// Choix d'une classe
		echo "<form name='form_choix_classe' enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
		echo "<fieldset id='choixClasse' style='border: 1px solid grey; width:15%; float:left; margin-right:1em;'>\n";
		echo "<legend style='border: 1px solid grey;'>Choix d'une classe</legend>\n";
		echo "<input type='hidden' name='mode' value='classe' />\n";

		if(isset($today)) {
			echo "<input type='hidden' name='today' value='$today' />\n";
		}

		echo "<select name='id_classe' onchange='document.form_choix_classe.submit();'>\n";
		echo "<option value=''>---</option>\n";
		for($i=0;$i<count($tab_classe);$i++) {
			echo "<option value='".$tab_classe[$i]['id_classe']."'";
			if((isset($id_classe))&&($id_classe==$tab_classe[$i]['id_classe'])) {echo " selected='selected'";}
			echo ">".$tab_classe[$i]['classe']."</option>\n";
		}
		echo "</select>\n";

		echo "<input type=\"submit\" id='bouton_submit_classe' value=\"Valider\" />\n";
		echo "</fieldset>\n";
		echo "</form>\n";
	}

	// Choix d'une classe du prof connecté
	if(isset($tab_classe_du_prof)) {
		echo "<form name='form_choix_une_de_mes_classes' enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
		echo "<fieldset id='choixUneDe_MesClasses' style='border: 1px solid grey; width:25%; float:left; margin-right:1em;'>\n";
		echo "<legend style='border: 1px solid grey;'>Choix d'une de mes classes</legend>\n";
		echo "<input type='hidden' name='mode' value='classe' />\n";

		if(isset($today)) {
			echo "<input type='hidden' name='today' value='$today' />\n";
		}

		echo "<select name='id_classe' onchange='document.form_choix_une_de_mes_classes.submit();'>\n";
		echo "<option value=''>---</option>\n";
		for($i=0;$i<count($tab_classe_du_prof);$i++) {
			echo "<option value='".$tab_classe_du_prof[$i]['id_classe']."'";
			if((isset($id_classe))&&($id_classe==$tab_classe_du_prof[$i]['id_classe'])) {echo " selected='selected'";}
			echo ">".$tab_classe_du_prof[$i]['classe']."</option>\n";
		}
		echo "</select>\n";

		echo "<input type=\"submit\" id='bouton_submit_une_de_mes_classes' value=\"Valider\" />\n";
		echo "</fieldset>\n";
		echo "</form>\n";
	}

	if(isset($tab_eleve_de_la_classe)) {
		echo "<form name='form_choix_eleve' enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
		echo "<fieldset id='choixEleve' style='border: 1px solid grey; width:25%; float:left; margin-right:1em;'>\n";
		echo "<legend style='border: 1px solid grey;'>Choix d'un élève de ".$classe."</legend>\n";
		echo "<input type='hidden' name='mode' value='eleve' />\n";
		echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";

		if(isset($today)) {
			echo "<input type='hidden' name='today' value='$today' />\n";
		}

		echo "<select name='login_eleve' onchange='document.form_choix_eleve.submit();'>\n";
		echo "<option value=''>---</option>\n";
		for($i=0;$i<count($tab_eleve_de_la_classe);$i++) {
			echo "<option value='".$tab_eleve_de_la_classe[$i]['login']."'";
			if((isset($login_eleve))&&($login_eleve==$tab_eleve_de_la_classe[$i]['login'])) {echo " selected='selected'";}
			echo ">".$tab_eleve_de_la_classe[$i]['nom_prenom']."</option>\n";
		}
		echo "</select>\n";

		echo "<input type=\"submit\" id='bouton_submit_eleve' value=\"Valider\" />\n";
		echo "</fieldset>\n";
		echo "</form>\n";
	}

	// Il faudra peut-être revoir plus finement quels statuts peuvent accéder... il peut y avoir pas mal de catégories en statut 'autre'
	if(($_SESSION['statut']!='professeur')) {
		// Choix d'un professeur
		echo "<form name='form_choix_prof' enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
		echo "<fieldset id='choixProf' style='border: 1px solid grey; width:20%; float:left; margin-right:1em;'>\n";
		echo "<legend style='border: 1px solid grey;'>Choix d'un professeur</legend>\n";
		echo "<input type='hidden' name='mode' value='professeur' />\n";

		if(isset($today)) {
			echo "<input type='hidden' name='today' value='$today' />\n";
		}

		echo "<select name='login_prof' onchange='document.form_choix_prof.submit();'>\n";
		echo "<option value=''>---</option>\n";
		for($i=0;$i<count($tab_profs);$i++) {
			echo "<option value='".$tab_profs[$i]['login']."'";
			if((isset($login_prof))&&($login_prof==$tab_profs[$i]['login'])) {echo " selected='selected'";}
			echo ">".$tab_profs[$i]['civ_nom_prenom']."</option>\n";
		}
		echo "</select>\n";

		echo "<input type=\"submit\" id='bouton_submit_prof' value=\"Valider\" />\n";
		echo "</fieldset>\n";
		echo "</form>\n";

	}
	else {
		//echo "<div style='border: 1px solid grey; width:20%; float:left; margin-right:1em;'><a href='".$_SERVER['PHP_SELF']."?mode=professeur'>Mes enseignements</a></div>";
		//echo "<div style='border: 1px solid grey; width:20%; float:left; margin-right:1em;'>";
		echo "<fieldset id='choixMesEnseignements' style='border: 1px solid grey; width:20%; float:left; margin-right:1em;'>\n";
		echo "<legend style='border: 1px solid grey;'>Mes enseignements</legend>\n";
		echo "<a href='".$_SERVER['PHP_SELF']."?mode=professeur";
		if(isset($today)) {
			echo "&amp;today$today";
		}
		echo "'>Mes enseignements</a></div>\n";
		echo "</fieldset>\n";
	}

	// Retour à la ligne pour ce qui va suivre les cadres formulaires de choix:
	echo "<div style='clear:both;'>&nbsp;</div>";

}
//=============================================================

$ts_aujourdhui=time();

$ts_semaine_precedente=$today-7*24*3600;
$ts_semaine_suivante=$today+2*7*24*3600;

$ts_limite_visibilite_devoirs_pour_eleves=$ts_aujourdhui+getSettingValue('delai_devoirs')*24*3600;

//=============================================================
// Définition de valeurs par défaut si nécessaire, et récupération des groupes associés au mode choisi:
if($mode=='classe') {
	if(!isset($id_classe)) {
		if($_SESSION['statut']=='professeur') {
			$sql="SELECT id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp, classes c WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' AND jgc.id_classe=c.id ORDER BY c.classe LIMIT 1;";
			$res_classe=mysql_query($sql);
			if(mysql_num_rows($res_classe)>0) {
				$id_classe = mysql_result($res_classe, 0, 'id_classe');
			}
		}

		if(!isset($id_classe)) {
			$sql="SELECT id AS id_classe FROM classes ORDER BY classe LIMIT 1;";
			$res_classe=mysql_query($sql);
			if(mysql_num_rows($res_classe)>0) {
				$id_classe = mysql_result($res_classe, 0, 'id_classe');
			}
		}
	}

	if(!isset($id_classe)) {
		echo "<p>Aucune classe n'a été trouvée.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	// Passage à la semaine précédente/courante/suivante
	echo "<div style='float: right; width:25em; text-align:center;'><a href='".$_SERVER['PHP_SELF']."?today=".$ts_aujourdhui."&amp;mode=$mode&amp;id_classe=$id_classe'>Aujourd'hui</a> - Semaines <a href='".$_SERVER['PHP_SELF']."?today=".$ts_semaine_precedente."&amp;mode=$mode&amp;id_classe=$id_classe'>précédente</a> / <a href='".$_SERVER['PHP_SELF']."?today=".$ts_semaine_suivante."&amp;mode=$mode&amp;id_classe=$id_classe'>suivante</a></div>\n";

	$classe=get_class_from_id($id_classe);

	echo "<p>Affichage pour une classe&nbsp;: <strong>".$classe."</strong></p>\n";
	$groups=get_groups_for_class($id_classe);

}
elseif($mode=='eleve') {
	if(!isset($login_eleve)) {
		echo "<p>Aucun élève n'a été choisi.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	if(!isset($id_classe)) {
		$sql="SELECT id_classe FROM j_eleves_classes WHERE login='$login_eleve' ORDER BY periode DESC LIMIT 1;";
		$res_classe=mysql_query($sql);
		if(mysql_num_rows($res_classe)>0) {
			$id_classe = mysql_result($res_classe, 0, 'id_classe');
		}
	}

	$classe=get_class_from_id($id_classe);

	$groups=get_groups_for_eleve($login_eleve, $id_classe);

	// Passage à la semaine précédente/courante/suivante
	echo "<div style='float: right; width:25em;'><a href='".$_SERVER['PHP_SELF']."?today=".$ts_aujourdhui."&amp;mode=$mode&amp;login_eleve=$login_eleve&amp;id_classe=$id_classe'>Aujourd'hui</a> - Semaines <a href='".$_SERVER['PHP_SELF']."?today=".$ts_semaine_precedente."&amp;mode=$mode&amp;login_eleve=$login_eleve&amp;id_classe=$id_classe'>précédente</a> / <a href='".$_SERVER['PHP_SELF']."?today=".$ts_semaine_suivante."&amp;mode=$mode&amp;login_eleve=$login_eleve&amp;id_classe=$id_classe'>suivante</a></div>\n";

	echo "<p>Affichage pour un élève&nbsp;: <strong>".civ_nom_prenom($login_eleve)." (<em>$classe</em>)</strong></p>\n";

}
elseif($mode=='professeur') {
	if(!isset($login_prof)) {
		if($_SESSION['statut']=='professeur') {
			$login_prof=$_SESSION['login'];
		}
		else {
			$sql="SELECT u.civilite, u.nom, u.prenom, u.login FROM utilisateurs u WHERE statut='professeur' AND etat='actif' ORDER BY u.nom, u.prenom LIMIT 1;";
			$res_prof=mysql_query($sql);
			if(mysql_num_rows($res_prof)>0) {
				$login_prof = mysql_result($res_prefs, 0, 'login');
			}
		}

		if(!isset($login_prof)) {
			echo "<p>Aucun professeur n'a été trouvé.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}

	$groups=get_groups_for_prof($login_prof);

	// Passage à la semaine précédente/courante/suivante
	echo "<div style='float: right; width:25em;'><a href='".$_SERVER['PHP_SELF']."?today=".$ts_aujourdhui."&amp;mode=$mode&amp;login_prof=$login_prof'>Aujourd'hui</a> - Semaines <a href='".$_SERVER['PHP_SELF']."?today=".$ts_semaine_precedente."&amp;mode=$mode&amp;login_prof=$login_prof'>précédente</a> / <a href='".$_SERVER['PHP_SELF']."?today=".$ts_semaine_suivante."&amp;mode=$mode&amp;login_prof=$login_prof'>suivante</a></div>\n";

	echo "<p>Affichage pour un professeur&nbsp;: <strong>".$tab_profs2[$login_prof]."</strong></p>\n";
}
//=============================================================

//=============================================================
// Récupération des groupes du professeur connecté:
if($_SESSION['statut']=='professeur') {
	$tab_mes_groupes=array();

	if($mode!='professeur') {
		//$tab_mes_groupes=get_groups_for_prof($_SESSION['login']);
		$sql="SELECT id_groupe FROM j_groupes_professeurs WHERE login='".$_SESSION['login']."'";
		$res_tmp=mysql_query($sql);
		if(mysql_num_rows($res_tmp)>0) {
			while($lig_tmp=mysql_fetch_object($res_tmp)) {
				$tab_mes_groupes[]=$lig_tmp->id_groupe;
			}
		}
	}
	else {
		foreach($groups as $key => $value) {
			$tab_mes_groupes[]=$value['id'];
		}
	}
}
//=============================================================

//================================================================
// Récupération des identifiants de couleurs associées aux matières dans l'EDT
$couleur_matiere=array();

$sql="SELECT m.matiere, es.valeur FROM edt_setting es, matieres m WHERE es.reglage = CONCAT('M_',m.matiere);";
//echo "$sql<br />";
$res_couleur=mysql_query($sql);
if(mysql_num_rows($res_couleur)>0) {
	while($lig_couleur=mysql_fetch_object($res_couleur)) {
		$couleur_matiere[$lig_couleur->matiere]=$lig_couleur->valeur;
	}
}
//================================================================

//================================================================
// Faire une sélection parmi les couleurs... on n'aura jamais autant de classes dans un établissement:
$tab_toutes_couleurs=array("aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen");

$couleur_classe=array();
if(isset($tab_classe)) {
	for($i=0;$i<count($tab_classe);$i++) {
		$couleur_classe[$tab_classe[$i]['id_classe']]=$tab_toutes_couleurs[$i];
	}
}
//================================================================

//=============================================================
// Récupération du premier jour de la semaine:
//$num_jour_semaine=strftime("%u",$today);
$num_jour_semaine=strftime("%w",$today);
if($num_jour_semaine==0) {$num_jour_semaine=7;}
//echo "\$num_jour_semaine=$num_jour_semaine<br />";
$premier_jour_semaine=$today-(3600*24*($num_jour_semaine-1));
//echo "strftime('%d/%m/%Y',\$today)=".strftime("%d/%m/%Y",$today)."<br />";
//echo "strftime('%u',\$today)=".strftime("%u",$today)."<br />";
//echo "strftime('%w',\$today)=".strftime("%w",$today)."<br />";
// %u 	Représentation ISO-8601 du jour de la semaine 	De 1 (pour Lundi) à 7 (pour Dimanche)
// %w 	Représentation numérique du jour de la semaine 	De 0 (pour Dimanche) à 6 (pour Samedi)
//=============================================================

//=============================================================
// Récupération des notices:
$tab_notice=array();
for($i=0;$i<14;$i++) {
	$tab_notice[$i]=array();

	$ts_jour_debut=$premier_jour_semaine+$i*3600*24;
	$ts_jour_fin=$premier_jour_semaine+($i+1)*3600*24;

	//echo "<p>".strftime("%d/%m/%Y",$ts_jour_debut)."</p>";

	foreach($groups as $current_group) {
		$id_groupe=$current_group['id'];

		if(!isset($couleur_matiere[$current_group['matiere']['matiere']])) {
			$couleur_matiere[$current_group['matiere']['matiere']]="";
		}

		$sql="SELECT * FROM ct_entry WHERE id_groupe='$id_groupe' AND date_ct>=$ts_jour_debut AND date_ct<$ts_jour_fin ORDER BY date_ct;";
		//echo "$sql<br />";
		$res_ct=mysql_query($sql);
		$cpt=0;
		while($ligne_ct=mysql_fetch_object($res_ct)) {
			//if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
			if((($_SESSION['statut']=='professeur')&&(in_array($id_groupe,$tab_mes_groupes)))||
			($ligne_ct->date_ct<=$ts_aujourdhui)) {
				//echo "<div style='border:1px solid black; margin:0.5em;'>".$current_group['name']."<br />".$ligne_ct->contenu."</div>\n";
				$tab_notice[$i][$id_groupe]['ct_entry'][$cpt]="";

				// Lien d'édition de la notice:
				//if(($_SESSION['statut']=='professeur')&&(in_array($id_groupe,$tab_mes_groupes))) {
					if(($_SESSION['statut']=='professeur')&&(($ligne_ct->id_login==$_SESSION['login'])||(getSettingAOui('cdt_autoriser_modif_multiprof')))) {
						if((!getSettingAOui('visa_cdt_inter_modif_notices_visees'))||($ligne_ct->vise!='y')){
							$tab_notice[$i][$id_groupe]['ct_entry'][$cpt].="<div style='float:right; width:16px;'><a href='../cahier_texte/index.php?id_groupe=$id_groupe&amp;id_ct=$ligne_ct->id_ct&amp;type_notice=cr'><img src='../images/edit16.png' width='16' height='16' /></a></div>";
						}
					}

					// Notice proprement dite:
					$tab_notice[$i][$id_groupe]['ct_entry'][$cpt].=$ligne_ct->contenu;
				/*
				}
				else {
					// Un élève,... ne voit pas les compte-rendus dans le futur
					if($ligne_ct->date_ct<=$ts_aujourdhui) {
						$tab_notice[$i][$id_groupe]['ct_entry'][$cpt].=$ligne_ct->contenu;
					}
				}
				*/

				// Documents joints:
				// Dans le futur, ils ne sont vus que par les profs du groupe
				if((($_SESSION['statut']=='professeur')&&(in_array($id_groupe,$tab_mes_groupes)))||
					($ligne_ct->date_ct<=$ts_aujourdhui)) {
					$sql="SELECT * FROM ct_documents where id_ct='$ligne_ct->id_ct';";
					$res_doc=mysql_query($sql);
					if(mysql_num_rows($res_doc)>0) {
						$tab_notice[$i][$id_groupe]['ct_entry'][$cpt].="<br /><strong>Documents joints&nbsp;:</strong>";
						while($ligne_ct_doc=mysql_fetch_object($res_doc)) {
							// Tester si le document est visible ou non dans le cas ele/resp
							if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
							($ligne_ct_doc->visible_eleve_parent==1))
							{
								$tab_notice[$i][$id_groupe]['ct_entry'][$cpt].="<br />\n<a href='$ligne_ct_doc->emplacement' title=\"$ligne_ct_doc->titre\" target='_blank'>".$ligne_ct_doc->titre."</a>";
							}
						}
					}
					$cpt++;
				}
			}
		}

		$sql="SELECT * FROM ct_devoirs_entry WHERE id_groupe='$id_groupe' AND date_ct>=$ts_jour_debut AND date_ct<$ts_jour_fin ORDER BY date_ct;";
		//echo "$sql<br />";
		$res_ct=mysql_query($sql);
		$cpt=0;
		while($ligne_ct=mysql_fetch_object($res_ct)) {
			if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
			($ligne_ct->date_ct<=$ts_limite_visibilite_devoirs_pour_eleves)) {

				$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$cpt]="";

				// Lien d'édition de la notice:
				if(($_SESSION['statut']=='professeur')&&(in_array($id_groupe,$tab_mes_groupes))) {
					if(($ligne_ct->id_login==$_SESSION['login'])||(getSettingAOui('cdt_autoriser_modif_multiprof'))) {
						if((!getSettingAOui('visa_cdt_inter_modif_notices_visees'))||($ligne_ct->vise!='y')){
							$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$cpt].="<div style='float:right; width:16px;'><a href='../cahier_texte/index.php?id_groupe=$id_groupe&amp;id_ct=$ligne_ct->id_ct&amp;edit_devoir=yes&amp;type_notice=dev'><img src='../images/edit16.png' width='16' height='16' /></a></div>";
						}
					}
				}

				// Notice proprement dite:
				$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$cpt].=$ligne_ct->contenu;

				// Documents joints:
				$sql="SELECT * FROM ct_devoirs_documents where id_ct_devoir='$ligne_ct->id_ct';";
				$res_doc=mysql_query($sql);
				if(mysql_num_rows($res_doc)>0) {
					$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$cpt].="<br /><strong>Documents joints&nbsp;:</strong>";
					while($ligne_ct_doc=mysql_fetch_object($res_doc)) {
						if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
						($ligne_ct_doc->visible_eleve_parent==1))
						{
							$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$cpt].="<br />\n<a href='$ligne_ct_doc->emplacement' title=\"$ligne_ct_doc->titre\" target='_blank'>".$ligne_ct_doc->titre."</a>";
						}
					}
				}
				$cpt++;
			}
		}

		$sql="SELECT * FROM ct_private_entry WHERE id_groupe='$id_groupe' AND date_ct>=$ts_jour_debut AND date_ct<$ts_jour_fin ORDER BY date_ct;";
		//echo "$sql<br />";
		$res_ct=mysql_query($sql);
		$cpt=0;
		while($ligne_ct=mysql_fetch_object($res_ct)) {
			//$tab_notice[$i][$id_groupe]['ct_private_entry'][$cpt]="";

			// Lien d'édition de la notice:
			// Les notices privées en multiprof??? sont-elles visibles du seul prof ou des profs du groupe?
			if(($_SESSION['statut']=='professeur')&&(in_array($id_groupe,$tab_mes_groupes))) {
				if(($ligne_ct->id_login==$_SESSION['login'])||(getSettingAOui('cdt_autoriser_modif_multiprof'))) {
					$tab_notice[$i][$id_groupe]['ct_private_entry'][$cpt]="<div style='float:right; width:16px;'><a href='../cahier_texte/index.php?id_groupe=$id_groupe&amp;id_ct=$ligne_ct->id_ct&amp;type_notice=np'><img src='../images/edit16.png' width='16' height='16' /></a></div>";

					// Notice proprement dite:
					$tab_notice[$i][$id_groupe]['ct_private_entry'][$cpt].=$ligne_ct->contenu;
					$cpt++;
				}
			}
		}
	}
}
//=============================================================

//=============================================================
$chaine_id_groupe="";
foreach($groups as $current_group) {
	$id_groupe=$current_group['id'];
	if($chaine_id_groupe!="") {$chaine_id_groupe.=", ";}
	$chaine_id_groupe.="'$id_groupe'";
}
//=============================================================

//=============================================================
// Boucle sur les 14 jours affichés
$max_cpt_grp=0;
for($i=0;$i<14;$i++) {
	$ts_jour_debut=$premier_jour_semaine+$i*3600*24;

	if($i%7==0) {
		if($i>0) {
			echo "</tr>\n";
			echo "</table>\n";
			echo "</div>\n";

			//echo "<br />";
		}

		echo "<div class='cdt_cadre_semaine'>\n";
		echo "<table border='1' width='100%'>\n";
		echo "<tr class='cdt_tab_semaine'>\n";
	}

	echo "<td>\n";
	echo "<div class='cdt_cadre_jour'>\n";

	$temoin_dev_non_vides=0;
	$temoin_cr_non_vides=0;
	$temoin_np_non_vides=0;

	$jour_courant=ucfirst(strftime("%a %d/%m/%y",$ts_jour_debut));

	echo "<p id='p_jour_$i' class='infobulle_entete' style='text-align:center;'>";
	echo "<a href='#ancre_travail_jour_$i' onclick=\"affichage_travail_jour($i);return false;\" class='cdt_lien_jour'>";
	echo $jour_courant;
	echo "</a>";
	echo "</p>\n";

	$titre_infobulle_jour="<a name='ancre_travail_jour_$i'></a>".$jour_courant;
	$texte_infobulle_jour="";

	$cpt_grp=0;
	$cpt_notice=0;
	// Boucle sur les groupes
	foreach($groups as $current_group) {
		$id_groupe=$current_group['id'];

		// Si il y a une notice pour ce groupe sur le jour courant de la boucle:
		if(isset($tab_notice[$i][$id_groupe])) {
			echo "   <div style='border: 1px solid orange; margin:3px;";
			//echo "opacity:0.5;";
			// Colorisation différente selon le mode d'affichage:
			if($mode=='professeur') {
				if($couleur_classe[$current_group["classes"]["list"][0]]!='') {echo " background-color:".$couleur_classe[$current_group["classes"]["list"][0]].";";}
			}
			else {
				if($couleur_matiere[$current_group['matiere']['matiere']]!='') {echo " background-color:".$tab_couleur_edt[$couleur_matiere[$current_group['matiere']['matiere']]].";";}
			}
			echo "'>\n";

			//echo "<div style='color:black; opacity:1;'>";

			/*
			echo "<a href=\"#ancre_travail_jour_".$i."_groupe_".$id_groupe."\" onclick=\"affichage_notices_tel_groupe($i, $id_groupe);return false;\" class='cdt_lien_groupe'>";
			if($mode=='professeur') {
				echo $current_group['name']." (<em>".$current_group['classlist_string']."</em>)";
			}
			else {
				echo $current_group['name'];
			}
			echo "</a>";
			*/

			//==================================================================
			// Cadre pour le groupe courant dans le cadre du jour courant dans l'infobulle du jour:
			$texte_infobulle_jour.="<div id='travail_jour_".$i."_groupe_".$id_groupe."'>\n";

			$texte_dev_courant="";
			if(isset($tab_notice[$i][$id_groupe]['ct_devoirs_entry'])) {
				// Liste des devoirs donnés pour ce jour dans ce groupe:
				for($j=0;$j<count($tab_notice[$i][$id_groupe]['ct_devoirs_entry']);$j++) {
					$texte_dev_courant.="<div style='background-color:".$color_fond_notices['t']."; border: 1px solid black; margin: 1px;'>\n";
					$texte_dev_courant.=$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$j];
					$texte_dev_courant.="</div>\n";
					$temoin_dev_non_vides++;
				}

				if($texte_dev_courant!="") {
					/*
					$texte_infobulle_jour.="<div style='width: 1em; background-color: pink; float: right; margin-left:3px; text-align:center;'>\n";
					$texte_infobulle_jour.="<a href='#' onclick=\"alterne_affichage('travail_jour_".$i."_groupe_".$id_groupe."_devoirs');return false;\">";
					$texte_infobulle_jour.="T";
					$texte_infobulle_jour.="</a>";
					$texte_infobulle_jour.="</div>\n";
					*/

					$texte_dev_courant="<div id='travail_jour_".$i."_groupe_".$id_groupe."_devoirs' style='background-color:".$color_fond_notices['t']."'>\n".$texte_dev_courant;
					$texte_dev_courant.="</div>\n";
				}
			}

			$texte_cr_courant="";
			if(isset($tab_notice[$i][$id_groupe]['ct_entry'])) {
				// Liste des compte-renddus pour ce jour dans ce groupe:
				for($j=0;$j<count($tab_notice[$i][$id_groupe]['ct_entry']);$j++) {
					$texte_cr_courant.="<div style='background-color:palegreen; border: 1px solid black; margin: 1px;'>\n";
					$texte_cr_courant.=$tab_notice[$i][$id_groupe]['ct_entry'][$j];
					$texte_cr_courant.="</div>\n";
					$temoin_cr_non_vides++;
				}

				if($texte_cr_courant!="") {
					/*
					$texte_infobulle_jour.="<div style='width: 1em; background-color: pink; float: right; margin-left:3px; text-align:center;'>\n";
					$texte_infobulle_jour.="<a href='#' onclick=\"alterne_affichage('travail_jour_".$i."_groupe_".$id_groupe."_compte_rendu');return false;\">";
					$texte_infobulle_jour.="C";
					$texte_infobulle_jour.="</a>";
					$texte_infobulle_jour.="</div>\n";
					*/

					$texte_cr_courant="<div id='travail_jour_".$i."_groupe_".$id_groupe."_compte_rendu' style='background-color:".$color_fond_notices['c']."'>\n".$texte_cr_courant;
					$texte_cr_courant.="</div>\n";
				}
			}

			$texte_np_courant="";
			if(isset($tab_notice[$i][$id_groupe]['ct_private_entry'])) {
				// Liste des notices privées pour ce jour dans ce groupe:
				for($j=0;$j<count($tab_notice[$i][$id_groupe]['ct_private_entry']);$j++) {
					$texte_np_courant.="<div style='background-color:".$color_fond_notices['p']."; border: 1px solid black; margin: 1px;'>\n";
					$texte_np_courant.=$tab_notice[$i][$id_groupe]['ct_private_entry'][$j];
					$texte_np_courant.="</div>\n";
					$temoin_np_non_vides++;
				}

				if($texte_np_courant!="") {
					/*
					$texte_infobulle_jour.="<div style='width: 1em; background-color: ".$color_fond_notices['p']."; float: right; margin-left:3px; text-align:center;'>\n";
					$texte_infobulle_jour.="<a href='#' onclick=\"alterne_affichage('travail_jour_".$i."_groupe_".$id_groupe."_notice_privee');return false;\">";
					$texte_infobulle_jour.="P";
					$texte_infobulle_jour.="</a>";
					$texte_infobulle_jour.="</div>\n";
					*/

					$texte_np_courant="<div id='travail_jour_".$i."_groupe_".$id_groupe."_notice_privee' style='background-color:".$color_fond_notices['p']."'>\n".$texte_np_courant;
					$texte_np_courant.="</div>\n";
				}
			}

			// On remplit le cadre pour le groupe courant dans le cadre du jour courant dans l'infobulle du jour
			// avec le nom du groupe, puis les devoirs donnés pour ce jour et enfin les compte-rendus de séance
			$texte_infobulle_jour.="<a name='ancre_travail_jour_".$i."_groupe_".$id_groupe."'></a>\n";
			$texte_infobulle_jour.="<strong>";
			if($mode=='professeur') {
				$texte_infobulle_jour.=$current_group['name']." (<em>".$current_group['classlist_string']."</em>)";
			}
			else {
				$texte_infobulle_jour.=$current_group['name'];
			}
			$texte_infobulle_jour.="</strong>\n";
			$texte_infobulle_jour.=$texte_dev_courant;
			$texte_infobulle_jour.=$texte_cr_courant;
			$texte_infobulle_jour.=$texte_np_courant;

			$texte_infobulle_jour.="&nbsp;<br />\n";

			$texte_infobulle_jour.="</div>\n";
			// Fin du cadre pour le groupe courant dans le cadre du jour courant dans l'infobulle du jour
			//==================================================================


			// Pour repérer les enseignements avec tel ou tel type de notice
			if($texte_np_courant!='') {
				// La restriction des notices visibles est fait plus haut
				echo "      <!-- Témoin de présence de notices privées pour le groupe $id_groupe sur le jour $i -->\n";
				echo "      <div style='width: 1em; background-color: ".$color_fond_notices['p']."; float: right; margin-left:3px; text-align:center;'>\n";
				echo "         <a href='#ancre_travail_jour_".$i."_groupe_".$id_groupe."' onclick=\"affichage_notices_tel_groupe($i, $id_groupe);return false;\" title=\"Notice privée\">P</a>\n";
				echo "      </div>\n";
			}
			if($texte_dev_courant!='') {
				// La restriction des notices visibles est fait plus haut
				echo "      <!-- Témoin de présence de notices de devoirs pour le groupe $id_groupe sur le jour $i -->\n";
				echo "      <div style='width: 1em; background-color: ".$color_fond_notices['t']."; float: right; margin-left:3px; text-align:center;'>\n";
				echo "         <a href='#ancre_travail_jour_".$i."_groupe_".$id_groupe."' onclick=\"affichage_notices_tel_groupe($i, $id_groupe);return false;\" title=\"Travail à faire\">T</a>\n";
				echo "      </div>\n";
			}
			if($texte_cr_courant!='') {
				// La restriction des notices visibles est fait plus haut
				echo "      <!-- Témoin de présence de comptes-rendus pour le groupe $id_groupe sur le jour $i -->\n";
				echo "      <div style='width: 1em; background-color: ".$color_fond_notices['c']."; float: right; margin-left:3px; text-align:center;'>\n";
				echo "         <a href='#ancre_travail_jour_".$i."_groupe_".$id_groupe."' onclick=\"affichage_notices_tel_groupe($i, $id_groupe);return false;\" title=\"Compte-rendu de séance\">C</a>\n";
				echo "      </div>\n";
			}


			echo "      <!-- Lien d'affichage du jour $i pour le groupe $id_groupe -->\n";
			echo "      <a href=\"#ancre_travail_jour_".$i."_groupe_".$id_groupe."\" onclick=\"affichage_notices_tel_groupe($i, $id_groupe);return false;\" class='cdt_lien_groupe'>";
			if($mode=='professeur') {
				echo $current_group['name']." (<em>".$current_group['classlist_string']."</em>)";
			}
			else {
				echo $current_group['name'];
			}
			echo "</a>\n";


			//echo "</div>\n";

			echo "   </div>\n\n";
		}
		$cpt_grp++;
	}

	// Ajouter un lien jour précédent avant
	if($i>0) {
		$indice_prec=$i-1;
		$lien_jour_prec="<a href='#' onclick=\"cacher_div('travail_jour_$i');
												var tmp_x=document.getElementById('travail_jour_$i').style.left;
												var tmp_y=document.getElementById('travail_jour_$i').style.top;
												affichage_travail_jour($indice_prec);
												document.getElementById('travail_jour_$indice_prec').style.left=tmp_x;
												document.getElementById('travail_jour_$indice_prec').style.top=tmp_y;
												return false;\" style='text-decoration:none; color:white'>";

		$lien_jour_prec.="<img src='../images/icons/arrow-left.png' />";
		$lien_jour_prec.="</a>";

		$titre_infobulle_jour=$lien_jour_prec." \n".$titre_infobulle_jour;
	}

	// Ajouter un lien jour suivant après
	if($i<14) {
		$indice_suiv=$i+1;
		$lien_jour_suiv="<a href='#' onclick=\"cacher_div('travail_jour_$i');
												var tmp_x=document.getElementById('travail_jour_$i').style.left;
												var tmp_y=document.getElementById('travail_jour_$i').style.top;
												affichage_travail_jour($indice_suiv);
												document.getElementById('travail_jour_$indice_suiv').style.left=tmp_x;
												document.getElementById('travail_jour_$indice_suiv').style.top=tmp_y;
												return false;\" style='text-decoration:none; color:white'>";
		$lien_jour_suiv.="<img src='../images/icons/arrow-right.png' />";
		$lien_jour_suiv.="</a>";

		$titre_infobulle_jour=$titre_infobulle_jour." \n".$lien_jour_suiv."\n";
	}

	// Masquage de tel type de notice d'un clic:
	if(($temoin_dev_non_vides>0)||($temoin_cr_non_vides>0)||($temoin_np_non_vides>0)) {
		$ajout="";
		if($temoin_cr_non_vides>0) {
			$ajout.="<a id='lien_alterne_affichage_compte_rendu_jour_$i' href='#' onclick=\"alterne_affichage_global('compte_rendu',$i);return false;\" style='background-color: ".$color_fond_notices['c'].";'>";
			$ajout.="C";
			$ajout.="</a>\n";
		}

		if($temoin_dev_non_vides>0) {
			$ajout.=" ";
			$ajout.="<a id='lien_alterne_affichage_devoirs_jour_$i' href='#' onclick=\"alterne_affichage_global('devoirs',$i);return false;\" style='background-color: ".$color_fond_notices['t'].";'>";
			$ajout.="T";
			$ajout.="</a>\n";
		}

		if($temoin_np_non_vides>0) {
			$ajout.=" ";
			$ajout.="<a href='#' id='lien_alterne_affichage_notice_privee_jour_$i' onclick=\"alterne_affichage_global('notice_privee',$i);return false;\" style='background-color: ".$color_fond_notices['p'].";'>";
			$ajout.="P";
			$ajout.="</a>\n";
		}

		$titre_infobulle_jour=$titre_infobulle_jour." ".$ajout;
	}
	else {
		$texte_infobulle_jour.="Aucune saisie pour ce jour.";
		$texte_infobulle_jour.="&nbsp;<br />";
	}

	if($cpt_grp>$max_cpt_grp) {$max_cpt_grp=$cpt_grp;}

	if($texte_infobulle_jour=="") {

		echo "<script type='text/javascript'>
document.getElementById('p_jour_$i').style.innerHTML='$jour_courant';
</script>\n";
	}

	$tabdiv_infobulle[]=creer_div_infobulle("travail_jour_".$i,$titre_infobulle_jour,"",$texte_infobulle_jour,"pink",20,0,'y','y','n','n');

	echo "</div></td>\n";
}
echo "</tr>\n";
echo "</table>\n";
echo "</div>\n";
//=============================================================

echo "<script type='text/javascript'>
var tab_grp=new Array($chaine_id_groupe);

// Si javascript est actif, on cache les boutons submit inutiles (déclenchement du submit sur onchange()):
if(document.getElementById('bouton_submit_classe')) {document.getElementById('bouton_submit_classe').style.display='none';}
if(document.getElementById('bouton_submit_une_de_mes_classes')) {document.getElementById('bouton_submit_une_de_mes_classes').style.display='none';}
if(document.getElementById('bouton_submit_prof')) {document.getElementById('bouton_submit_prof').style.display='none';}
</script>\n";

/*
echo "<pre>";
echo print_r($tab_mes_groupes);
echo "</pre>";

echo "id_classe=$id_classe<br />";
*/

$tab_grp=array();
/*
if($_SESSION['statut']=='professeur') {

	if($mode=='professeur') {
		//$tab_champs=array();
		$tab_grp=get_groups_for_prof($_SESSION['login']);
	}
}
elseif(($_SESSION['statut']=='responsable')||($_SESSION['statut']=='eleve')) {
	// A VOIR: Cas des élèves qui changent de classe...
	$tab_grp=get_groups_for_eleve($login_eleve, $id_classe);
}
*/

if($mode=='professeur') {
	//$tab_champs=array();
	$tab_grp=get_groups_for_prof($_SESSION['login']);
}
elseif($mode=='classe') {
	$tab_grp=get_groups_for_class($id_classe);
}
elseif($mode=='eleve') {
	// A VOIR: Cas des élèves qui changent de classe...
	$tab_grp=get_groups_for_eleve($login_eleve, $id_classe);
}

if(count($tab_grp)>0) {
	$infos_generales="";

	foreach($tab_grp as $current_group) {
		$id_groupe=$current_group['id'];

		// Affichage des informations générales
		//$sql="SELECT contenu, id_ct  FROM ct_entry WHERE (id_groupe='$id_groupe' and (date_ct='' OR date_ct='0'));";
		$sql="SELECT contenu, id_ct  FROM ct_entry WHERE (id_groupe='$id_groupe' and date_ct='');";
		//echo "$sql<br />";
		$appel_info_cahier_texte = mysql_query($sql);
		$nb_cahier_texte = mysql_num_rows($appel_info_cahier_texte);
		$content = @mysql_result($appel_info_cahier_texte, 0, 'contenu');
		$id_ct = @mysql_result($appel_info_cahier_texte, 0, 'id_ct');
		$content.=affiche_docs_joints($id_ct,"c");

		if($content!="") {
			$infos_generales.="<div class='see_all_general couleur_bord_tableau_notice color_fond_notices_i' style='width:98%;'>";
			$infos_generales.="<h3>".$current_group['name']." (<em>".$current_group['description']." en ".$current_group['classlist_string']."</em>)"."</h3>";
			$infos_generales.=$content;
			$infos_generales.="</div>";
		}
	}

	if ($infos_generales != '') {
		echo "<div style='padding:1em;'>\n";
		echo "<h2 class='grande_ligne couleur_bord_tableau_notice'>\n<strong>INFORMATIONS GENERALES</strong>\n</h2>\n";
		echo $infos_generales;
		echo "</div>\n";
	}
}

echo "<hr />\n";
echo "<p style='text-align:center; font-style:italic;'>Cahiers de textes du ";
echo strftime("%d/%m/%Y", getSettingValue("begin_bookings"));
echo " au ";
echo strftime("%d/%m/%Y", getSettingValue("end_bookings"));
echo "</p>\n";

require("../lib/footer.inc.php");
?>
