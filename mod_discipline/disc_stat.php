<?php

/*
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

if(mb_strtolower(mb_substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');

function get_denomination_prof($login) {
	$sql="SELECT nom,prenom,civilite FROM utilisateurs WHERE login='$login';";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)==0) {
		return "Utilisateur inconnu";
	}
	else {
		$lig=mysqli_fetch_object($res);
		return $lig->civilite." ".casse_mot($lig->nom)." ".mb_strtoupper(mb_substr($lig->prenom,0,1));
	}
}

$is_posted=isset($_POST['is_posted']) ? $_POST['is_posted'] : NULL;
$mode=isset($_POST['mode']) ? $_POST['mode'] : NULL;
$nb_ele=isset($_POST['nb_ele']) ? $_POST['nb_ele'] : 10;


$annee = strftime("%Y");
$mois = strftime("%m");
$jour = strftime("%d");
if($mois>7) {
	$date_debut_tmp="01/09/$annee";
	// Et au format MySQL:
	$date_debut_annee="$annee-09-01";
	$date_du_jour="$annee-$mois-$jour";
}
else {
	$date_debut_tmp="01/09/".($annee-1);
	// Et au format MySQL:
	$date_debut_annee=($annee-1)."-09-01";
	$date_du_jour=($annee-1)."-$mois-$jour";
}
$date_debut_disc=isset($_POST['date_debut_disc']) ? $_POST['date_debut_disc'] : (isset($_POST['date_debut_disc1']) ? $_POST['date_debut_disc1'] : (isset($_SESSION['date_debut_disc']) ? $_SESSION['date_debut_disc'] : $date_debut_tmp));
$date_fin_disc=isset($_POST['date_fin_disc']) ? $_POST['date_fin_disc'] : (isset($_POST['date_fin_disc1']) ? $_POST['date_fin_disc1'] : (isset($_SESSION['date_fin_disc']) ? $_SESSION['date_fin_disc'] : "$jour/$mois/$annee"));


$nature=isset($_POST['nature']) ? $_POST['nature'] : NULL;
$id_mesure=isset($_POST['id_mesure']) ? $_POST['id_mesure'] : NULL;
$nature_sanction=isset($_POST['nature_sanction']) ? $_POST['nature_sanction'] : NULL;
$id_nature_sanction=isset($_POST['id_nature_sanction']) ? $_POST['id_nature_sanction'] : NULL;

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

//**************** EN-TETE *****************
$titre_page = "Discipline: Statistiques";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if(!isset($is_posted)) {
	echo "</p>\n";

	// Afficher les statistiques globales?
	// ou choisir quoi afficher

	//echo "<div style='border:1px solid black; padding: 1em;'>\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='form1'>\n";
	echo "<fieldset id='effectifsIncidents' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); '>\n";
	echo "<legend style='border: 1px solid grey; background-color: white;  '>Effectifs par ".$mod_disc_terme_incident."s,...</legend>\n";

	//echo "<p class='bold'>Totaux&nbsp;:</p>\n";

	echo add_token_field();

	//=======================
	//Configuration du calendrier
	/*
	include("../lib/calendrier/calendrier.class.php");
	$cal1 = new Calendrier("form1", "date_debut_disc");
	$cal2 = new Calendrier("form1", "date_fin_disc");
	*/
	//=======================

	echo "<p>Intervalle de dates&nbsp;: du ";
	//echo "<input type='text' name='date_debut_disc' value='' />\n";
	echo "<input type='text' name = 'date_debut_disc1' id='date_debut_disc' size='10' value = \"".$date_debut_disc."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
	//echo "<a href=\"#\" onClick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";
	//echo "<a href=\"javascript:".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";
	echo img_calendrier_js("date_debut_disc", "img_bouton_date_debut_disc");

	echo " au ";
	//echo "<input type='text' name='date_fin_disc' value='' />\n";
	echo "<input type='text' name = 'date_fin_disc1' id='date_fin_disc' size='10' value = \"".$date_fin_disc."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
	//echo "<a href=\"#\" onClick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";
	//echo "<a href=\"javascript:".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";
	echo img_calendrier_js("date_fin_disc", "img_bouton_date_fin_disc");
	echo "</p>\n";


	echo "<p>Choisissez ce que vous souhaitez afficher&nbsp;:</p>\n";
	echo "<table class='boireaus' summary='Choix des affichages'>\n";
	echo "<tr>\n";
	echo "<th>\n";
	echo "Nature<br />d'".$mod_disc_terme_incident."s\n";
	echo "<br />\n";
	echo "<a href='javascript:modif_case(\"nature\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
	echo "<a href='javascript:modif_case(\"nature\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
	echo "</th>\n";
	echo "<th>\n";
	echo "Mesures prises\n";
	echo "<br />\n";
	echo "<a href='javascript:modif_case(\"id_mesure\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
	echo "<a href='javascript:modif_case(\"id_mesure\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
	echo "</th>\n";
	echo "<th>\n";
	echo ucfirst($mod_disc_terme_sanction)."s\n";
	echo "<br />\n";
	echo "<a href='javascript:modif_case(\"nature_sanction\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
	echo "<a href='javascript:modif_case(\"nature_sanction\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
	echo "</th>\n";
	echo "</tr>\n";

	$max_cpt=0;

	echo "<tr class='lig-1'>\n";
	echo "<td style='vertical-align:top; text-align: left;'>\n";
	$sql="SELECT DISTINCT nature FROM s_incidents ORDER BY nature;";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	$cpt=0;
	while($lig=mysqli_fetch_object($res)) {
		echo "<input type='checkbox' name='nature[]' id='nature_$cpt' value=\"$lig->nature\" /><label for='nature_$cpt'>";
		if($lig->nature=='') {echo "vide";} else {echo $lig->nature;}
		echo "</label><br />\n";
		$cpt++;
	}
	$max_cpt=max($max_cpt,$cpt);
	echo "</td>\n";

	echo "<td style='vertical-align:top; text-align: left;'>\n";
	$sql="SELECT * FROM s_mesures WHERE type='prise' ORDER BY mesure;";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	$cpt=0;
	while($lig=mysqli_fetch_object($res)) {
		echo "<input type='checkbox' name='id_mesure[]' id='id_mesure_$cpt' value='$lig->id' /><label for='id_mesure_$cpt'>$lig->mesure</label><br />\n";
		$cpt++;
	}
	echo "</td>\n";

	echo "<td style='vertical-align:top; text-align: left;'>\n";
	/*
	echo "<input type='checkbox' name='nature_sanction[]' id='nature_sanction_$cpt' value='travail' /><label for='nature_sanction_$cpt'>Travail</label><br />\n";
	$cpt++;
	echo "<input type='checkbox' name='nature_sanction[]' id='nature_sanction_$cpt' value='retenue' /><label for='nature_sanction_$cpt'>Retenue</label><br />\n";
	$cpt++;
	echo "<input type='checkbox' name='nature_sanction[]' id='nature_sanction_$cpt' value='exclusion' /><label for='nature_sanction_$cpt'>Exclusion</label><br />\n";
	$cpt++;
	*/
	$sql="SELECT * FROM s_types_sanctions2 ORDER BY type, nature;";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	//$cpt=0;
	while($lig=mysqli_fetch_object($res)) {
		//echo "<input type='checkbox' name='id_nature_sanction[]' id='id_nature_sanction_$cpt' value='$lig->id_nature' /><label for='id_nature_sanction_$cpt'>$lig->nature</label><br />\n";
		echo "<input type='checkbox' name='id_nature_sanction[]' id='nature_sanction_$cpt' value='$lig->id_nature' /><label for='nature_sanction_$cpt'>$lig->nature</label><br />\n";
		$cpt++;
	}
	$max_cpt=max($max_cpt,$cpt);
	echo "</td>\n";

	echo "</tr>\n";
	echo "</table>\n";

	echo "<input type='hidden' name='is_posted' value='1' />\n";
	echo "<input type='hidden' name='mode' value='totaux' />\n";
	echo "<input type='submit' name='valider' value='Valider' />\n";

	//echo "<p style='color:red;'>Ajouter des liens Tout cocher/décocher.</p>\n";
	echo "<p style='color:red;'>A FAIRE: Totaux par classes...</p>\n";
	echo "<p style='color:red;'>A FAIRE: Pouvoir faire des tableaux mois par mois.</p>\n";
	//echo "</div>\n";

	echo "</fieldset>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	function modif_case(pref,statut){
		for(k=0;k<$max_cpt;k++){
			if(document.getElementById(pref+'_'+k)){
				document.getElementById(pref+'_'+k).checked=statut;
			}
		}
	}

	tab_topten=new Array('topten_incidents','topten_sanctions','topten_retenues','topten_exclusions');
	function topten_coche(statut){
		var k;
		for(k=0;k<tab_topten.length;k++){
			if(document.getElementById(tab_topten[k])){
				document.getElementById(tab_topten[k]).checked=statut;
			}
		}
	}

</script>\n";


	echo "<p>&nbsp;</p>\n";

	//=================================================================================

	//echo "<div style='border:1px solid black; padding: 1em;'>\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='form2'>\n";
	echo "<fieldset id='topten' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); '>\n";
	echo "<legend style='border: 1px solid grey; background-color: white;  '>Top-ten,...</legend>\n";

	//echo "<p class='bold'>Top ten&nbsp;:</p>\n";

	echo add_token_field();

	//=======================
	//Configuration du calendrier
	//include("../lib/calendrier/calendrier.class.php");
	//$cal3 = new Calendrier("form2", "date_debut_disc");
	//$cal4 = new Calendrier("form2", "date_fin_disc");
	//=======================

	echo "<p>Intervalle de dates&nbsp;: du ";
	//echo "<input type='text' name='date_debut_disc' value='' />\n";
	echo "<input type='text' name = 'date_debut_disc' id = 'date_debut_disc2' size='10' value = \"".$date_debut_disc."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
	//echo "<a href=\"#\" onClick=\"".$cal3->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";
	//echo "<a href=\"javascript:".$cal3->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";
	echo img_calendrier_js("date_debut_disc2", "img_bouton_date_debut_disc2");

	echo " au ";
	//echo "<input type='text' name='date_fin_disc' value='' />\n";
	echo "<input type='text' name = 'date_fin_disc' id = 'date_fin_disc2' size='10' value = \"".$date_fin_disc."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
	//echo "<a href=\"#\" onClick=\"".$cal4->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";
	//echo "<a href=\"javascript:".$cal4->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";
	echo img_calendrier_js("date_fin_disc2", "img_bouton_date_fin_disc2");
	echo "</p>\n";

	echo "<p>Choisissez ce que vous souhaitez afficher&nbsp;:</p>\n";

	echo "<p>Les élèves \n";
	echo "<a href='javascript:topten_coche(true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
	echo "<a href='javascript:topten_coche(false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
	echo "<br />\n";
	echo "<input type='checkbox' name='topten_incidents' id='topten_incidents' value='y' /><label for='topten_incidents'>responsables du plus grand nombre d'".$mod_disc_terme_incident."s,</label><br />\n";
	echo "<input type='checkbox' name='topten_sanctions' id='topten_sanctions' value='y' /><label for='topten_sanctions'>qui ont le plus de ".$mod_disc_terme_sanction."s (<i>travail, retenue, exclusion,...</i>),</label><br />\n";
	echo "<input type='checkbox' name='topten_retenues' id='topten_retenues' value='y' /><label for='topten_retenues'>qui ont le plus de retenues (<em>et assimilées</em>),</label><br />\n";
	echo "<input type='checkbox' name='topten_exclusions' id='topten_exclusions' value='y' /><label for='topten_exclusions'>qui ont le plus d'exclusions (<em>et assimilées</em>).</label><br />\n";

	echo "Ne retenir que les <input type='text' name='nb_ele' value='10' size='2' /> premiers.<br />\n";

	echo "<input type='hidden' name='mode' value='topten' />\n";
	echo "<input type='hidden' name='is_posted' value='1' />\n";
	echo "<input type='submit' name='valider' value='Valider' />\n";
	echo "</p>\n";
	echo "</fieldset>\n";
	echo "</form>\n";
	//echo "</div>\n";

}
elseif($mode=='totaux') {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Statistiques</a>";
	echo "</p>\n";

	check_token(false);

	echo "<p><b>Dates&nbsp;:</b> ";
	if($date_debut_disc!="") {

		// Tester la validité de la date
		// Si elle n'est pas valide... la vider
		if(preg_match("#/#",$date_debut_disc)) {
			$tmp_tab_date=explode("/",$date_debut_disc);

			if(!checkdate($tmp_tab_date[1],$tmp_tab_date[0],$tmp_tab_date[2])) {
				$date_debut_disc="";
			}
			else {
				$date_debut_disc=$tmp_tab_date[2]."-".$tmp_tab_date[1]."-".$tmp_tab_date[0];
			}
		}
		elseif(preg_match("/-/",$date_debut_disc)) {
			$tmp_tab_date=explode("-",$date_debut_disc);
	
			if(!checkdate($tmp_tab_date[1],$tmp_tab_date[2],$tmp_tab_date[0])) {
				$date_debut_disc="";
			}
		}
		else {
			$date_debut_disc="";
		}

		if($date_debut_disc=="") {
			// Si la date proposée est invalide, on force la date initiale au début de l'année:
			$date_debut_disc=$date_debut_annee;
		}

		if($date_debut_disc!="") {
			$date_debut_disc_formatee=formate_date($date_debut_disc);
			echo "du ".$date_debut_disc_formatee;
			$_SESSION['date_debut_disc']=$date_debut_disc_formatee;
		}
	}

	if($date_fin_disc!="") {
		// Tester la validité de la date
		// Si elle n'est pas valide... la vider
		// Tester la validité de la date
		// Si elle n'est pas valide... la vider
		if(preg_match("#/#",$date_fin_disc)) {
			$tmp_tab_date=explode("/",$date_fin_disc);

			if(!checkdate($tmp_tab_date[1],$tmp_tab_date[0],$tmp_tab_date[2])) {
				$date_fin_disc="";
			}
			else {
				$date_fin_disc=$tmp_tab_date[2]."-".$tmp_tab_date[1]."-".$tmp_tab_date[0];
			}
		}
		elseif(preg_match("/-/",$date_fin_disc)) {
			$tmp_tab_date=explode("-",$date_fin_disc);
	
			if(!checkdate($tmp_tab_date[1],$tmp_tab_date[2],$tmp_tab_date[0])) {
				$date_fin_disc="";
			}
		}
		else {
			$date_fin_disc="";
		}

		if($date_fin_disc=="") {
			// Si la date proposée est invalide, on force la date finale à la date du jour:
			$date_fin_disc=$date_du_jour;
		}

		if($date_fin_disc!="") {
			$date_fin_disc_formatee=formate_date($date_fin_disc);
			echo " au ".$date_fin_disc_formatee;
			$_SESSION['date_fin_disc']=$date_fin_disc_formatee;
		}
	}

	if(($date_debut_disc=="")&&($date_fin_disc=="")) {
		// Ca ne devrait plus arriver
		echo "aucune limite de dates";
	}
	echo "</p>\n";

	$restriction_date="";
	if(($date_debut_disc!="")&&($date_fin_disc!="")) {
		$restriction_date.=" AND (si.date>='$date_debut_disc' AND si.date<='$date_fin_disc') ";
	}
	elseif($date_debut_disc!="") {
		$restriction_date.=" AND (si.date>='$date_debut_disc') ";
	}
	elseif($date_fin_disc!="") {
		$restriction_date.=" AND (si.date<='$date_fin_disc') ";
	}

	echo "<p class='bold'>".ucfirst($mod_disc_terme_incident)."s&nbsp;:</p>\n";
	echo "<table class='boireaus' summary='".ucfirst($mod_disc_terme_incident)."s'>\n";
	echo "<tr>\n";
	echo "<th>Nature</th>\n";
	echo "<th>Nombre d'".$mod_disc_terme_incident."s</th>\n";
	/*
	echo "<th>Classes</th>\n";
	*/
	echo "</tr>\n";
	$alt=1;
	for($i=0;$i<count($nature);$i++) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td>\n";
		if($nature[$i]=='') {
			echo "vide\n";
		}
		else {
			echo $nature[$i];
		}
		echo "</td>\n";
		echo "<td>\n";
		$sql="SELECT * FROM s_incidents si WHERE si.nature='$nature[$i]' $restriction_date ORDER BY si.date DESC;";
		$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		$nb_incidents=mysqli_num_rows($res);
		echo $nb_incidents;
		echo "</td>\n";
		/*
		echo "<td>\n";
		if($nb_incidents==0) {
			echo "&nbsp;";
		}
		else {
			// PROBLEME: ON RECUPERE LE TRIPLE DE L'EFFECTIF: On compte autant de fois un élève pour un incident qu'il appartient à des périodes de j_eleves_classes
			$sql="SELECT DISTINCT c.classe, COUNT(sp.login) AS nb FROM classes c, j_eleves_classes jec, s_protagonistes sp, s_incidents si WHERE c.id=jec.id_classe AND jec.login=sp.login AND sp.id_incident=si.id_incident AND si.nature='$nature[$i]' AND sp.qualite='responsable' $restriction_date GROUP BY c.classe ORDER BY count(sp.login) DESC;";
			echo "$sql<br />\n";
			$res2=mysql_query($sql);
			if(mysql_num_rows($res2)==0) {
			}
			else {
				$lig2=mysql_fetch_object($res2);
				echo "$lig2->classe ($lig2->nb)";
				while($lig2=mysql_fetch_object($res2)) {
					echo ", $lig2->classe ($lig2->nb)";
				}
			}
		}
		echo "</td>\n";
		*/
		echo "</tr>\n";
	}
	echo "</table>\n";


	echo "<p class='bold'>Mesures prises&nbsp;:</p>\n";
	echo "<table class='boireaus' summary='Mesures prises'>\n";
	echo "<tr>\n";
	echo "<th>Mesure</th>\n";
	echo "<th>Nombre de mesures prises</th>\n";
	echo "</tr>\n";
	$alt=1;
	for($i=0;$i<count($id_mesure);$i++) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td>\n";
		$sql="SELECT * FROM s_mesures WHERE id='$id_mesure[$i]';";
		$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<span style='color:red;'>Anomalie&nbsp;: Mesure inconnue</span>";
		}
		else {
			$lig=mysqli_fetch_object($res);
			echo $lig->mesure;
		}
		echo "</td>\n";
		echo "<td>\n";
		$sql="SELECT * FROM s_traitement_incident sti, s_incidents si WHERE si.id_incident=sti.id_incident AND sti.id_mesure='$id_mesure[$i];' $restriction_date;";
		$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		echo mysqli_num_rows($res);
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";


	echo "<p class='bold'>".ucfirst($mod_disc_terme_sanction)."s&nbsp;:</p>\n";
	echo "<table class='boireaus' summary='".ucfirst($mod_disc_terme_sanction)."s'>\n";
	echo "<tr>\n";
	echo "<th>".ucfirst($mod_disc_terme_sanction)."</th>\n";
	echo "<th>Nombre de ".$mod_disc_terme_sanction."s</th>\n";
	echo "</tr>\n";
	$alt=1;
	/*
	for($i=0;$i<count($nature_sanction);$i++) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td>\n";
		echo ucfirst($nature_sanction[$i]);
		echo "</td>\n";
		echo "<td>\n";
		if($nature_sanction[$i]=='travail') {
			$sql="SELECT * FROM s_travail WHERE 1=1";

			$restriction_date="";
			if(($date_debut_disc!="")&&($date_fin_disc!="")) {
				$restriction_date.=" AND (date_retour>='$date_debut_disc' AND date_retour<='$date_fin_disc') ";
			}
			elseif($date_debut_disc!="") {
				$restriction_date.=" AND (date_retour>='$date_debut_disc') ";
			}
			elseif($date_fin_disc!="") {
				$restriction_date.=" AND (date_retour<='$date_fin_disc') ";
			}

			$sql.=$restriction_date;
		}
		elseif($nature_sanction[$i]=='retenue') {
			$sql="SELECT * FROM s_retenues WHERE 1=1";

			$restriction_date="";
			if(($date_debut_disc!="")&&($date_fin_disc!="")) {
				$restriction_date.=" AND (date>='$date_debut_disc' AND date<='$date_fin_disc') ";
			}
			elseif($date_debut_disc!="") {
				$restriction_date.=" AND (date>='$date_debut_disc') ";
			}
			elseif($date_fin_disc!="") {
				$restriction_date.=" AND (date<='$date_fin_disc') ";
			}

			$sql.=$restriction_date;
		}
		elseif($nature_sanction[$i]=='exclusion') {
			$sql="SELECT * FROM s_exclusions WHERE 1=1";

			$restriction_date="";
			if(($date_debut_disc!="")&&($date_fin_disc!="")) {
				$restriction_date.=" AND ((date_debut>='$date_debut_disc' AND date_debut<='$date_fin_disc') OR (date_fin>='$date_debut_disc' AND date_fin<='$date_fin_disc') OR (date_debut<='$date_debut_disc' AND date_fin>='$date_fin_disc'))";
			}
			elseif($date_debut_disc!="") {
				$restriction_date.=" AND (date_fin>='$date_debut_disc') ";
			}
			elseif($date_fin_disc!="") {
				$restriction_date.=" AND (date_debut<='$date_fin_disc') ";
			}

			$sql.=$restriction_date;
		}
		$res=mysql_query($sql);
		echo mysql_num_rows($res);

		echo "</td>\n";
		echo "</tr>\n";
	}
	*/

	for($i=0;$i<count($id_nature_sanction);$i++) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td>\n";
		$sql="SELECT * FROM s_types_sanctions2 WHERE id_nature='$id_nature_sanction[$i]';";
		$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<span style='color:red;'>Anomalie&nbsp;: ".ucfirst($mod_disc_terme_sanction)." inconnue</span>";
		}
		else {
			$lig=mysqli_fetch_object($res);
			echo ucfirst($lig->nature);
		}
		echo "</td>\n";
		echo "<td>\n";

		if($lig->type=='travail') {
			$sql="SELECT * FROM s_travail st, s_sanctions s WHERE st.id_sanction=s.id_sanction AND id_nature_sanction='$id_nature_sanction[$i]'";

			$restriction_date="";
			if(($date_debut_disc!="")&&($date_fin_disc!="")) {
				$restriction_date.=" AND (date_retour>='$date_debut_disc' AND date_retour<='$date_fin_disc') ";
			}
			elseif($date_debut_disc!="") {
				$restriction_date.=" AND (date_retour>='$date_debut_disc') ";
			}
			elseif($date_fin_disc!="") {
				$restriction_date.=" AND (date_retour<='$date_fin_disc') ";
			}

			$sql.=$restriction_date;
		}
		elseif($lig->type=='retenue') {
			$sql="SELECT * FROM s_retenues sr, s_sanctions s WHERE sr.id_sanction=s.id_sanction AND id_nature_sanction='$id_nature_sanction[$i]'";

			$restriction_date="";
			if(($date_debut_disc!="")&&($date_fin_disc!="")) {
				$restriction_date.=" AND (date>='$date_debut_disc' AND date<='$date_fin_disc') ";
			}
			elseif($date_debut_disc!="") {
				$restriction_date.=" AND (date>='$date_debut_disc') ";
			}
			elseif($date_fin_disc!="") {
				$restriction_date.=" AND (date<='$date_fin_disc') ";
			}

			$sql.=$restriction_date;
		}
		elseif($lig->type=='exclusion') {
			$sql="SELECT * FROM s_exclusions se, s_sanctions s WHERE se.id_sanction=s.id_sanction AND id_nature_sanction='$id_nature_sanction[$i]'";

			$restriction_date="";
			if(($date_debut_disc!="")&&($date_fin_disc!="")) {
				$restriction_date.=" AND ((date_debut>='$date_debut_disc' AND date_debut<='$date_fin_disc') OR (date_fin>='$date_debut_disc' AND date_fin<='$date_fin_disc') OR (date_debut<='$date_debut_disc' AND date_fin>='$date_fin_disc'))";
			}
			elseif($date_debut_disc!="") {
				$restriction_date.=" AND (date_fin>='$date_debut_disc') ";
			}
			elseif($date_fin_disc!="") {
				$restriction_date.=" AND (date_debut<='$date_fin_disc') ";
			}

			$sql.=$restriction_date;
		}
		else {
			$restriction_date="";
			if(($date_debut_disc!="")&&($date_fin_disc!="")) {
				$restriction_date.=" AND (si.date>='$date_debut_disc' AND si.date<='$date_fin_disc') ";
			}
			elseif($date_debut_disc!="") {
				$restriction_date.=" AND (si.date>='$date_debut_disc') ";
			}
			elseif($date_fin_disc!="") {
				$restriction_date.=" AND (si.date<='$date_fin_disc') ";
			}

			$sql="SELECT * FROM s_incidents si, s_sanctions s, s_autres_sanctions sas WHERE si.id_incident=s.id_incident AND s.id_sanction=sas.id_sanction AND id_nature='$id_nature_sanction[$i]' $restriction_date;";
		}

		$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		echo mysqli_num_rows($res);

		echo "</td>\n";
		echo "</tr>\n";
	}

	echo "</table>\n";
}
elseif($mode=='topten') {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Statistiques</a>";
	echo "</p>\n";

	check_token(false);

	echo "<p><b>Dates&nbsp;:</b> ";
	if($date_debut_disc!="") {

		// Tester la validité de la date
		// Si elle n'est pas valide... la vider
		if(preg_match("#/#",$date_debut_disc)) {
			$tmp_tab_date=explode("/",$date_debut_disc);

			if(!checkdate($tmp_tab_date[1],$tmp_tab_date[0],$tmp_tab_date[2])) {
				$date_debut_disc="";
			}
			else {
				$date_debut_disc=$tmp_tab_date[2]."-".$tmp_tab_date[1]."-".$tmp_tab_date[0];
			}
		}
		elseif(preg_match("/-/",$date_debut_disc)) {
			$tmp_tab_date=explode("-",$date_debut_disc);
	
			if(!checkdate($tmp_tab_date[1],$tmp_tab_date[2],$tmp_tab_date[0])) {
				$date_debut_disc="";
			}
		}
		else {
			$date_debut_disc="";
		}

		if($date_debut_disc=="") {
			// Si la date proposée est invalide, on force la date initiale au début de l'année:
			$date_debut_disc=$date_debut_annee;
		}

		if($date_debut_disc!="") {
			echo "du ".formate_date($date_debut_disc);
		}
	}

	if($date_fin_disc!="") {
		// Tester la validité de la date
		// Si elle n'est pas valide... la vider
		// Tester la validité de la date
		// Si elle n'est pas valide... la vider
		if(preg_match("#/#",$date_fin_disc)) {
			$tmp_tab_date=explode("/",$date_fin_disc);

			if(!checkdate($tmp_tab_date[1],$tmp_tab_date[0],$tmp_tab_date[2])) {
				$date_fin_disc="";
			}
			else {
				$date_fin_disc=$tmp_tab_date[2]."-".$tmp_tab_date[1]."-".$tmp_tab_date[0];
			}
		}
		elseif(preg_match("#-#",$date_fin_disc)) {
			$tmp_tab_date=explode("-",$date_fin_disc);
	
			if(!checkdate($tmp_tab_date[1],$tmp_tab_date[2],$tmp_tab_date[0])) {
				$date_fin_disc="";
			}
		}
		else {
			$date_fin_disc="";
		}

		if($date_fin_disc=="") {
			// Si la date proposée est invalide, on force la date finale à la date du jour:
			$date_fin_disc=$date_du_jour;
		}

		if($date_fin_disc!="") {
			echo " au ".formate_date($date_fin_disc);
		}
	}

	if(($date_debut_disc=="")&&($date_fin_disc=="")) {
		// Ca ne devrait plus arriver
		echo "aucune limite de dates";
	}
	echo "</p>\n";
	echo "<p><br /></p>\n";

	$restriction_date="";
	if(($date_debut_disc!="")&&($date_fin_disc!="")) {
		$restriction_date.=" AND (si.date>='$date_debut_disc' AND si.date<='$date_fin_disc') ";
	}
	elseif($date_debut_disc!="") {
		$restriction_date.=" AND (si.date>='$date_debut_disc') ";
	}
	elseif($date_fin_disc!="") {
		$restriction_date.=" AND (si.date<='$date_fin_disc') ";
	}

	$tab_classe=array();

	$sql="select sp.login, count(sp.login) AS nb FROM s_protagonistes sp, s_incidents si WHERE sp.id_incident=si.id_incident AND sp.qualite='responsable' $restriction_date GROUP BY sp.login ORDER BY count(sp.login) DESC LIMIT $nb_ele;";
	//echo "$sql<br />\n";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucun ".$mod_disc_terme_incident." avec élève responsable n'est enregistré.</p>\n";
	}
	else {
		echo "<p>Les $nb_ele élèves responsables du plus grand nombre d'".$mod_disc_terme_incident."s&nbsp;:</p>\n";
		echo "<table class='boireaus' summary='Tableau des fauteurs d ".$mod_disc_terme_incident."s'>\n";
		echo "<tr>\n";
		echo "<th>Elève</th>\n";
		echo "<th>Classe</th>\n";
		echo "<th>Nombre d'".$mod_disc_terme_incident."s</th>\n";
		echo "</tr>\n";
		$alt=1;
		while($lig=mysqli_fetch_object($res)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			echo "<a href='../eleves/visu_eleve.php?ele_login=$lig->login&amp;onglet=discipline' target='_blank'>";
			echo get_nom_prenom_eleve($lig->login);
			echo "</a>\n";
			echo "</td>\n";
			echo "<td>\n";
			if(!isset($tab_classe[$lig->login])) {
				$tab_classe[$lig->login]=get_class_from_ele_login($lig->login);
			}
			echo $tab_classe[$lig->login]['liste_nbsp'];
			echo "</td>\n";
			echo "<td>\n";
			echo $lig->nb;
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "<p><br /></p>\n";
	}


	$sql="select s.login, count(s.login) AS nb FROM s_sanctions s, s_incidents si WHERE si.id_incident=s.id_incident $restriction_date GROUP BY s.login ORDER BY count(s.login) DESC LIMIT $nb_ele;";
	//echo "$sql<br />\n";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucun avec élève avec ".$mod_disc_terme_sanction." n'est enregistré.</p>\n";
	}
	else {
		echo "<p>Les $nb_ele élèves qui ont le plus de ".$mod_disc_terme_sanction."s&nbsp;:</p>\n";
		echo "<table class='boireaus' summary='Tableau des ".$mod_disc_terme_sanction."nés'>\n";
		echo "<tr>\n";
		echo "<th>Elève</th>\n";
		echo "<th>Classe</th>\n";
		echo "<th>Nombre de ".$mod_disc_terme_sanction."s</th>\n";
		echo "</tr>\n";
		$alt=1;
		while($lig=mysqli_fetch_object($res)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			echo "<a href='../eleves/visu_eleve.php?ele_login=$lig->login&amp;onglet=discipline' target='_blank'>";
			echo get_nom_prenom_eleve($lig->login);
			echo "</a>\n";
			echo "</td>\n";
			echo "<td>\n";
			if(!isset($tab_classe[$lig->login])) {
				$tab_classe[$lig->login]=get_class_from_ele_login($lig->login);
			}
			echo $tab_classe[$lig->login]['liste_nbsp'];
			echo "</td>\n";
			echo "<td>\n";
			echo $lig->nb;
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "<p><br /></p>\n";
	}


	$sql="select login, sum(duree) AS nb FROM s_retenues sr, s_sanctions s, s_incidents si WHERE s.id_sanction=sr.id_sanction AND si.id_incident=s.id_incident $restriction_date GROUP BY s.login ORDER BY sum(duree) DESC LIMIT $nb_ele;";
	//echo "$sql<br />\n";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucun élève avec retenue n'est enregistré.</p>\n";
	}
	else {
		echo "<p>Les $nb_ele élèves qui ont le plus de retenues&nbsp;:</p>\n";
		echo "<table class='boireaus' summary='Tableau des sanctionnés par des retenues'>\n";
		echo "<tr>\n";
		echo "<th>Elève</th>\n";
		echo "<th>Classe</th>\n";
		echo "<th>Nombre de retenues</th>\n";
		echo "</tr>\n";
		$alt=1;
		while($lig=mysqli_fetch_object($res)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			echo "<a href='../eleves/visu_eleve.php?ele_login=$lig->login&amp;onglet=discipline' target='_blank'>";
			echo get_nom_prenom_eleve($lig->login);
			echo "</a>\n";
			echo "</td>\n";
			echo "<td>\n";
			if(!isset($tab_classe[$lig->login])) {
				$tab_classe[$lig->login]=get_class_from_ele_login($lig->login);
			}
			echo $tab_classe[$lig->login]['liste_nbsp'];
			echo "</td>\n";
			echo "<td>\n";
			echo $lig->nb;
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "<p><br /></p>\n";
	}



	//$sql="select s.login, count(se.*) AS nb FROM s_exclusions se, s_sanctions s, s_incidents si WHERE s.id_sanction=se.id_sanction AND si.id_incident=s.id_incident $restriction_date GROUP BY s.login ORDER BY count(se.*) desc;";
	$sql="select s.login, count(se.id_exclusion) AS nb FROM s_exclusions se, s_sanctions s, s_incidents si WHERE s.id_sanction=se.id_sanction AND si.id_incident=s.id_incident $restriction_date GROUP BY s.login ORDER BY count(se.id_exclusion) DESC LIMIT $nb_ele;";
	//echo "$sql<br />\n";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucun élève avec exclusion n'est enregistré.</p>\n";
	}
	else {
		echo "<p>Les $nb_ele élèves qui ont le plus d'exclusions&nbsp;:</p>\n";
		echo "<table class='boireaus' summary='Tableau des sanctionnés par des exclusions'>\n";
		echo "<tr>\n";
		echo "<th>Elève</th>\n";
		echo "<th>Classe</th>\n";
		echo "<th>Nombre d'exclusions</th>\n";
		echo "</tr>\n";
		$alt=1;
		while($lig=mysqli_fetch_object($res)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			echo "<a href='../eleves/visu_eleve.php?ele_login=$lig->login&amp;onglet=discipline' target='_blank'>";
			echo get_nom_prenom_eleve($lig->login);
			echo "</a>\n";
			echo "</td>\n";
			echo "<td>\n";
			if(!isset($tab_classe[$lig->login])) {
				$tab_classe[$lig->login]=get_class_from_ele_login($lig->login);
			}
			echo $tab_classe[$lig->login]['liste_nbsp'];
			echo "</td>\n";
			echo "<td>\n";
			echo $lig->nb;
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
	}

/*
select login, count(login) FROM s_protagonistes WHERE qualite='responsable' GROUP BY login ORDER BY count(login) desc;

select login, count(login) FROM s_sanctions GROUP BY login ORDER BY count(login) desc;

select login, sum(duree) FROM s_retenues sr, s_sanctions s
WHERE s.id_sanction=sr.id_sanction
GROUP BY login
ORDER BY sum(duree) desc;

select login, count(*) FROM s_exclusions se, s_sanctions s  WHERE s.id_sanction=se.id_sanction  GROUP BY login  ORDER BY count(*) desc;
*/

}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Statistiques</a>";
	echo "</p>\n";

	// (</i>$mode</i>)
	echo "<p style='color:red;'>Valeur de mode inconnu.</p>\n";
}

require("../lib/footer.inc.php");
die();
?>
