<?php

/*
 *
 * Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql = "SELECT 1=1 FROM droits WHERE id='/mod_discipline/afficher_incidents_eleve.php';";
$test = mysqli_query($GLOBALS["mysqli"], $sql);
if (mysqli_num_rows($test) == 0) {
	$sql = "INSERT INTO droits VALUES ( '/mod_discipline/afficher_incidents_eleve.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Discipline: Affichage des incidents pour un élève.', '');";
	$insert = mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('active_mod_discipline')) {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');

$mod_disc_terme_avertissement_fin_periode=getSettingValue('mod_disc_terme_avertissement_fin_periode');
if($mod_disc_terme_avertissement_fin_periode=="") {$mod_disc_terme_avertissement_fin_periode="avertissement de fin de période";}

$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : (isset($_GET['login_ele']) ? $_GET['login_ele'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$annee = strftime("%Y");
$mois = strftime("%m");
$jour = strftime("%d");

if($mois>7) {$date_debut_tmp="01/09/$annee";} else {$date_debut_tmp="01/09/".($annee-1);}

$date_debut_disc=isset($_POST['date_debut_disc']) ? $_POST['date_debut_disc'] : (isset($_SESSION['date_debut_disc']) ? $_SESSION['date_debut_disc'] : $date_debut_tmp);
$date_fin_disc=isset($_POST['date_fin_disc']) ? $_POST['date_fin_disc'] : (isset($_SESSION['date_fin_disc']) ? $_SESSION['date_fin_disc'] : "$jour/$mois/$annee");

$restreindre_affichage_a_eleve_seul=isset($_GET['restreindre_affichage_a_eleve_seul']) ? $_GET['restreindre_affichage_a_eleve_seul'] : "y";

$lien_refermer=isset($_POST['lien_refermer']) ? $_POST['lien_refermer'] : (isset($_GET['lien_refermer']) ? $_GET['lien_refermer'] : "n");

if(isset($login_ele)) {
	if($_SESSION['statut']=='professeur') {
		$acces_suite="n";

		if((getSettingAOui('visuDiscProfClasses'))&&(is_prof_ele($_SESSION['login'], $login_ele))) {
			$acces_suite="y";
		}
		elseif((getSettingAOui('visuDiscProfGroupes'))&&(is_prof_classe_ele($_SESSION['login'], $login_ele))) {
			$acces_suite="y";
		}
		elseif(is_pp($_SESSION['login'], "", $login_ele)) {
			$acces_suite="y";
		}

		if($acces_suite=="n") {
			$msg="Vous n'avez pas accès à cet élève.<br />";
			tentative_intrusion(1, "Tentative d'accès à la consultation d'$mod_disc_terme_incident pour l'élève ".get_nom_prenom_eleve($login_ele).".");
			unset($login_ele);
		}
	}
	/*
	elseif(($_SESSION['statut']=='cpe')&&(!is_cpe($_SESSION['login'], "", $login_ele))) {
		$msg="Vous n'avez pas accès à cet élève.<br />";
		tentative_intrusion(1, "Tentative d'accès à la consultation d'$mod_disc_terme_incident pour l'élève ".get_nom_prenom_eleve($login_ele).".");
		unset($login_ele);
	}
	*/
}

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Incidents d'un élève";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

if($lien_refermer=="y") {
	echo "<p class='bold noprint'><a href=\"#\" onclick=\"confirm_close (this, change, '$themessage');\">Refermer la page</a></p>\n";
}
else {
	echo "<p class='bold noprint'><a href=\"index.php\" onclick=\"confirm_abandon (this, change, '$themessage');\" title=\"Retour à la page d'accueil du module Discipline\">Retour</a>\n";

	if(isset($id_classe)) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?lien_refermer=$lien_refermer'>Choisir une autre classe</a>";
	}

	if(isset($login_ele)) {
		if(isset($id_classe)) {
			echo " | <a href='".$_SERVER['PHP_SELF']."?lien_refermer=$lien_refermer&amp;id_classe=$id_classe'>Choisir un autre élève</a>";
		}
		else {
			echo " | <a href='".$_SERVER['PHP_SELF']."?lien_refermer=$lien_refermer'>Choisir un autre élève</a>";
		}
	}
}
echo "</p>";


if(isset($login_ele)) {
	$tableau_des_avertissements_de_fin_de_periode_eleve_de_cet_eleve=tableau_des_avertissements_de_fin_de_periode_eleve($login_ele);
	if($tableau_des_avertissements_de_fin_de_periode_eleve_de_cet_eleve!='') {
		echo "<div style='float:right; width:25em; margin-bottom:0.5em; margin-left:0.5em;'>".$tableau_des_avertissements_de_fin_de_periode_eleve_de_cet_eleve."</div>\n";
	}

	if($restreindre_affichage_a_eleve_seul=="y") {
		echo "<p class='noprint' style='color:green;'>Les ".$mod_disc_terme_incident."s affichés ne concernent que l'élève choisi.<br />
	Les informations concernant les autres protagonistes des incidents ne sont pas affichés.<br />
	<a href='".$_SERVER['PHP_SELF']."?lien_refermer=$lien_refermer&amp;login_ele=$login_ele&amp;restreindre_affichage_a_eleve_seul=n'>Afficher toutes les informations</a>.</p>";
	}
	else {
		$restreindre_affichage_a_eleve_seul="n";

		echo "<p class='noprint' style='color:green;'>Les ".$mod_disc_terme_incident."s affichés présentent aussi ce qui concerne les éventuels autres protagonistes des $mod_disc_terme_incident.<br />
	Les informations concernant les autres protagonistes des incidents ne sont pas affichés<br />
	<a href='".$_SERVER['PHP_SELF']."?lien_refermer=$lien_refermer&amp;login_ele=$login_ele&amp;restreindre_affichage_a_eleve_seul=y'>Masquer ce qui concerne les autres protagonistes</a>.</p>";
	}


	echo "<form action='".$_SERVER['PHP_SELF']."' name='form_date_disc' method='post' class='noprint' style='max-width:40em; text-align:center;' />
	<fieldset class='fieldset_opacite50'>
		<input type='hidden' name='lien_refermer' value='$lien_refermer' />
		<p>Extraire les ".$mod_disc_terme_incident."s entre le 
		<input type='text' name = 'date_debut_disc' id= 'date_debut_disc' size='10' value = \"".$date_debut_disc."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />
		".img_calendrier_js("date_debut_disc", "img_bouton_date_debut_disc")."
		et le 
		<input type='text' name = 'date_fin_disc' id= 'date_fin_disc' size='10' value = \"".$date_fin_disc."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />".
		img_calendrier_js("date_fin_disc", "img_bouton_date_fin_disc")."
		<input type='submit' name='restreindre_intervalle_dates' value='Valider' />
		<input type='hidden' name='login_ele' value=\"$login_ele\" />
		</p>
	</fieldset>
</form>\n";


	$mode="";
	echo tab_mod_discipline($login_ele, $mode, $date_debut_disc, $date_fin_disc, $restreindre_affichage_a_eleve_seul);

	require("../lib/footer.inc.php");
	die();
}

//===============================
// Choix de la classe

if(!isset($id_classe)) {

	if($_SESSION['statut']=='administrateur') {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}
	elseif($_SESSION['statut']=='secours') {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}
	elseif($_SESSION['statut']=='scolarite') {
		//$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}
	elseif(($_SESSION['statut']=='professeur')&&(getSettingAOui('visuDiscProfClasses'))) {
		$sql="SELECT DISTINCT c.id, c.classe FROM j_groupes_classes jgc, 
									j_groupes_professeurs jgp, 
									classes c 
								WHERE jgp.login='".$_SESSION['login']."' AND 
									jgp.id_groupe=jgc.id_groupe AND 
									jgc.id_classe=c.id 
								ORDER BY c.classe;";
	}
	elseif(($_SESSION['statut']=='professeur')&&(getSettingAOui('visuDiscProfGroupes'))) {
		$sql="SELECT DISTINCT c.id, c.classe FROM j_groupes_classes jgc, 
									j_groupes_professeurs jgp, 
									j_eleves_groupes jeg, 
									classes c 
								WHERE jgp.login='".$_SESSION['login']."' AND 
									jgp.id_groupe=jgc.id_groupe AND 
									jeg.id_groupe=jgp.id_groupe AND 
									jgc.id_classe=c.id 
								ORDER BY c.classe;";

		// A FAIRE: Créer un droit pour le PP
		//$sql="SELECT DISTINCT jec.id_classe AS id, c.classe FROM j_eleves_professeurs jep, j_eleves_classes jec, classes c WHERE jep.professeur='".$_SESSION['login']."' AND jep.login=jec.login AND jec.id_classe=c.id ORDER BY c.classe;";

	}
	elseif($_SESSION['statut']=='cpe') {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
	}
	else {
		echo "
<p style='color:red'>Vous n'avez pas accès à la consultation d'$mod_disc_terme_incident.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	//echo "$sql<br />";
	//$tab=array();
	$txt_classe=array();
	$lien_classe=array();
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if($res->num_rows > 0) {
		while($lig=$res->fetch_object()) {
			//$tab['id_classe'][]=$lig->id_classe;
			//$tab['classe'][]=$lig->classe;
			$txt_classe[]=$lig->classe;
			$lien_classe[]=$_SERVER['PHP_SELF']."?id_classe=".$lig->id;
		}
		$res->close();
	}

	echo "
<p>Sélectionnez la classe : </p>
<blockquote>\n";

	if(count($txt_classe)>0) {
		tab_liste($txt_classe,$lien_classe,3);
	}
	else {
		echo "<p style='color:red'>Vous n'êtes associé à aucun élève.</p>\n";
	}

	require("../lib/footer.inc.php");
	die();
}
//=======================================
if(!isset($login_ele)) {

	echo "
	<p class='bold'>Classe de ".get_nom_classe($id_classe)."</p>
	<p style='margin-left:4em; text-indent:-2em;'>Choix de l'élève&nbsp;:<br />";

	$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='$id_classe' ORDER BY e.nom, e.prenom;";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>Aucun élève n'a été trouvé.</p>\n";
	}
	else {
		while($lig=mysqli_fetch_object($res)) {
			echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;login_ele=".$lig->login."' title=\"Consulter les ".$mod_disc_terme_incident." pour cet élève\">".$lig->nom." ".$lig->prenom."</a><br />\n";
		}
	}
	echo "</p>";

	require("../lib/footer.inc.php");
	die();
}
//=======================================

?>
