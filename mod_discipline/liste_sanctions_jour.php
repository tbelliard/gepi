<?php

/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$msg="";

$jour_sanction=isset($_POST['jour_sanction']) ? $_POST['jour_sanction'] : (isset($_GET['jour_sanction']) ? $_GET['jour_sanction'] : NULL);
$details=isset($_POST['details']) ? $_POST['details'] : (isset($_GET['details']) ? $_GET['details'] : "n");

$order_by_date=isset($_POST['order_by_date']) ? $_POST['order_by_date'] : (isset($_GET['order_by_date']) ? $_GET['order_by_date'] : "asc");
if(($order_by_date!="asc")&&($order_by_date!="desc")) {$order_by_date="asc";}

$form_id_sanction=isset($_POST['form_id_sanction']) ? $_POST['form_id_sanction'] : NULL;
$sanction_effectuee=isset($_POST['sanction_effectuee']) ? $_POST['sanction_effectuee'] : array();
if(isset($form_id_sanction)) {
	check_token();

	for($i=0;$i<count($form_id_sanction);$i++) {
		if(isset($sanction_effectuee[$form_id_sanction[$i]])) {
			$sql="UPDATE s_sanctions SET effectuee='O' WHERE id_sanction='".$form_id_sanction[$i]."';";
		}
		else {
			$sql="UPDATE s_sanctions SET effectuee='N' WHERE id_sanction='".$form_id_sanction[$i]."';";
		}
		//echo "$sql<br />\n";
		$res=mysql_query($sql);
		if(!$res) {
			$msg.="ERREUR lors de la mise à jour du statut de la sanction n°".$form_id_sanction[$i].".<br />\n";
		}
	}
}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Discipline: Liste des sanctions";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
echo "<p class='bold'><a href='index.php'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo "><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

echo " | Choix de la date&nbsp;: ";

include("../lib/calendrier/calendrier.class.php");
$cal = new Calendrier("formulaire", "jour_sanction");

if(!isset($jour_sanction)) {
	$annee=strftime("%Y");
	$mois=strftime("%m");
	$jour=strftime("%d");
	$jour_sanction=$jour."/".$mois."/".$annee;
}
else {
	$jour=mb_substr($jour_sanction,0,2);
	$mois=mb_substr($jour_sanction,3,2);
	$annee=mb_substr($jour_sanction,6,4);
}

$timestamp=mktime(0,0,0,$mois,$jour,$annee);
$timestamp_precedent=$timestamp-3600*24;
$annee_precedent=strftime("%Y",$timestamp_precedent);
$mois_precedent=strftime("%m",$timestamp_precedent);
$jour_precedent=strftime("%d",$timestamp_precedent);
$jour_sanction_precedent=$jour_precedent."/".$mois_precedent."/".$annee_precedent;

$timestamp_suivant=$timestamp+3600*24;
$annee_suivant=strftime("%Y",$timestamp_suivant);
$mois_suivant=strftime("%m",$timestamp_suivant);
$jour_suivant=strftime("%d",$timestamp_suivant);
$jour_sanction_suivant=$jour_suivant."/".$mois_suivant."/".$annee_suivant;

echo " | <a href='".$_SERVER['PHP_SELF']."?jour_sanction=$jour_sanction_precedent'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">Jour précédent</a>";

echo " | ";
echo "<input type='text' name='jour_sanction' id='jour_sanction' size='10' value=\"".$jour_sanction."\" onKeyDown=\"clavier_date_plus_moins(this.id,event);\" />\n";
echo "<a href=\"#calend\" onclick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170).";";
//echo "return confirm_abandon (this, change, '$themessage')";
echo "\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
echo " <input type='submit' name='valide_jour' value=\"Go\" ";
echo "onclick=\"return confirm_abandon (this, change, '$themessage')\" ";
echo "/>\n";

echo " | <a href='".$_SERVER['PHP_SELF']."?jour_sanction=$jour_sanction_suivant'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">Jour suivant</a>";

echo "</p>\n";

echo "</form>\n";

//===========================================================

// Formulaire de saisie du statut "effectuée" d'une retenue ou d'un travail
echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire2'>\n";

//echo add_token_field();
echo add_token_field(true);

echo "<input type='hidden' name='jour_sanction' value='$jour_sanction' />\n";

$cpt_sanctions=0;
$login_declarant="";

/*
$jour =  mb_substr($jour_sanction,0,2);
$mois =  mb_substr($jour_sanction,3,2);
$annee = mb_substr($jour_sanction,6,4);
*/
$mysql_jour_sanction=$annee."-".$mois."-".$jour;

//===============================================
// Incidents dont le prof est le déclarant ou un protagoniste
$tab_incidents_prof=array();
$tab_incidents_prof_declarant=array();
if($_SESSION['statut']=='professeur') {
	//$sql="(SELECT si.id_incident FROM s_incidents si WHERE si.declarant='".$_SESSION['login']."') UNION (SELECT sp.id_incident FROM s_protagonistes sp WHERE sp.login='".$_SESSION['login']."');";
	$sql="SELECT si.id_incident FROM s_incidents si WHERE si.declarant='".$_SESSION['login']."';";
	//echo "$sql<br />";
	$res_incidents_prof=mysql_query($sql);
	if(mysql_num_rows($res_incidents_prof)>0) {
		while($lig_tmp=mysql_fetch_object($res_incidents_prof)) {
			$tab_incidents_prof[]=$lig_tmp->id_incident;
		}
	}
	$tab_incidents_prof_declarant=$tab_incidents_prof;

	$sql="SELECT sp.id_incident FROM s_protagonistes sp WHERE sp.login='".$_SESSION['login']."';";
	//echo "$sql<br />";
	$res_incidents_prof=mysql_query($sql);
	if(mysql_num_rows($res_incidents_prof)>0) {
		while($lig_tmp=mysql_fetch_object($res_incidents_prof)) {
			$tab_incidents_prof[]=$lig_tmp->id_incident;
		}
	}

	// Incidents dont des protagonistes sont dans des classes du prof
	//$tab_incidents_classes_prof=array();
	if(getSettingAOui('visuDiscProfClasses')) {
		$sql="SELECT DISTINCT sp.id_incident FROM s_protagonistes sp, 
									j_groupes_professeurs jgp, 
									j_groupes_classes jgc, 
									j_eleves_classes jec 
							WHERE jgp.login='".$_SESSION['login']."' AND 
									jgp.id_groupe=jgc.id_groupe AND 
									jgc.id_classe=jec.id_classe AND 
									jec.login=sp.login;";
		//echo "$sql<br />";
		// Il faudrait peut-être une restriction sur le rôle du protagoniste dans l'incident
		$res_incidents_prof=mysql_query($sql);
		if(mysql_num_rows($res_incidents_prof)>0) {
			while($lig_tmp=mysql_fetch_object($res_incidents_prof)) {
				$tab_incidents_prof[]=$lig_tmp->id_incident;
			}
		}
	}
	if(getSettingAOui('visuDiscProfGroupes')) {
		// Incidents dont des protagonistes sont dans des groupes du prof
		//$tab_incidents_groupes_prof=array();
		$sql="SELECT DISTINCT sp.id_incident FROM s_protagonistes sp, 
									j_groupes_professeurs jgp, 
									j_eleves_groupes jeg 
							WHERE jgp.login='".$_SESSION['login']."' AND 
									jgp.id_groupe=jeg.id_groupe AND 
									jeg.login=sp.login;";
		//echo "$sql<br />";
		// Il faudrait peut-être une restriction sur le rôle du protagoniste dans l'incident
		$res_incidents_prof=mysql_query($sql);
		if(mysql_num_rows($res_incidents_prof)>0) {
			while($lig_tmp=mysql_fetch_object($res_incidents_prof)) {
				$tab_incidents_prof[]=$lig_tmp->id_incident;
			}
		}
	}
}
//===============================================

// Retenues
$sql="SELECT * FROM s_sanctions s, s_retenues sr WHERE sr.date='".$mysql_jour_sanction."' AND sr.id_sanction=s.id_sanction ORDER BY sr.date, sr.heure_debut, sr.lieu, s.login;";
//$retour.="$sql<br />\n";
//echo "$sql<br />\n";
$res_sanction=mysql_query($sql);
if(mysql_num_rows($res_sanction)>0) {
	echo "<p class='bold'>Retenues (<em>et assimilées</em>) du jour&nbsp;: $jour_sanction</p>\n";
	echo "<blockquote>\n";
	echo "<table class='boireaus' border='1' summary='Retenues' style='margin:2px;'>\n";
	echo "<tr>\n";
	echo "<th title=\"Numéro de l'incident\">N°i</th>\n";
	echo "<th>Nature</th>\n";
	echo "<th>Heure</th>\n";
	echo "<th>Durée</th>\n";
	echo "<th>Lieu</th>\n";
	echo "<th>Elève</th>\n";
	echo "<th>Travail</th>\n";
	echo "<th>Donné par (Déclarant)</th>\n";
	echo "<th>Nbre de report</th>\n";
	echo "<th>Effectuée</th>\n";
	echo "</tr>\n";
	$alt_b=1;
	$num=0;
	while($lig_sanction=mysql_fetch_object($res_sanction)) {
		if(($_SESSION['statut']!='professeur')||(in_array($lig_sanction->id_incident, $tab_incidents_prof))) {
			$alt_b=$alt_b*(-1);
			if($lig_sanction->effectuee=="O") {
				echo "<tr style='background-color: lightgrey;'>\n";
			}
			else {
				echo "<tr class='lig$alt_b'>\n";
			}
			// \$lig_sanction->effectuee=$lig_sanction->effectuee et \$lig_sanction->id_sanction=$lig_sanction->id_sanction


			echo "<td>";

			$texte=rappel_incident($lig_sanction->id_incident, 'retour');
			$tabdiv_infobulle[]=creer_div_infobulle("incident_".$lig_sanction->id_incident,"".ucfirst($mod_disc_terme_incident)." n°$lig_sanction->id_incident","",$texte,"",30,0,'y','y','n','n');

			if(($_SESSION['statut']!='professeur')||(in_array($lig_sanction->id_incident, $tab_incidents_prof_declarant))) {
				echo "<a href='saisie_incident.php?id_incident=$lig_sanction->id_incident&amp;step=2'";
				echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('incident_".$lig_sanction->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo ">$lig_sanction->id_incident</a>";
			}
			else {
				echo "<a href='#'";
				echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('incident_".$lig_sanction->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo " onclick='return false;'";
				echo ">";
				echo $lig_sanction->id_incident;
				echo "</a>";
			}
			echo "</td>\n";

			echo "<td>$lig_sanction->nature</td>\n";
			echo "<td>$lig_sanction->heure_debut</td>\n";
			echo "<td>$lig_sanction->duree</td>\n";
			echo "<td>$lig_sanction->lieu</td>\n";
			echo "<td>";
			echo p_nom($lig_sanction->login);
			echo " (<i>";
			$tmp_tab=get_class_from_ele_login($lig_sanction->login);
			//if(isset($tmp_tab['liste'])) {echo $tmp_tab['liste'];}
			if(isset($tmp_tab['liste_nbsp'])) {echo $tmp_tab['liste_nbsp'];}
			echo "</i>)";
			echo "</td>\n";
			echo "<td style='text-align:left;'>";
			$travail=$lig_sanction->travail;

			$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
			if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
				$texte="Aucun travail";
			}
			else {
				$texte=nl2br($lig_sanction->travail);
				if($tmp_doc_joints!="") {
					if($texte!="") {$texte.="<br />";}
					$texte.="<b>Documents joints</b>&nbsp;:<br />";
					$texte.=$tmp_doc_joints;
				}

				if($details=="y") {
					echo $texte;
				}
				else {
					$tabdiv_infobulle[]=creer_div_infobulle("div_travail_sanction_$lig_sanction->id_sanction","Travail (sanction n°$lig_sanction->id_sanction)","",$texte,"",20,0,'y','y','n','n',2);
	
					echo " <a href=\"".$_SERVER['PHP_SELF']."?jour_sanction=$jour_sanction&amp;details=y\"";
					//echo " onmouseover=\"delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
					//echo " onmouseover=\"cacher_toutes_les_infobulles();afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',20,20);\"";
					echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo ">Détails</a>";
				}
			}
			echo "</td>\n";

			echo "<td>\n";
			echo lien_envoi_mail_rappel($lig_sanction->id_sanction, $num);

		    echo "</td>\n";

			echo "<td>\n";
			echo nombre_reports($lig_sanction->id_sanction,"Néant");
		    echo "</td>\n";

			echo "<td>\n";
			$marquer_sanction_effectuee_possible="y";
			if(($_SESSION['statut']=='professeur')&&(!sanction_saisie_par($lig_sanction->id_sanction, $_SESSION['login']))) {
				$marquer_sanction_effectuee_possible="n";
			}

			if($marquer_sanction_effectuee_possible=="y") {
				echo "<input type='checkbox' name='sanction_effectuee[$lig_sanction->id_sanction]' value='effectuee' ";
				if($lig_sanction->effectuee=="O") {echo "checked='checked' ";}
				echo "onchange='changement();' ";
				echo "/>\n";
				echo "<input type='hidden' name='form_id_sanction[]' value='$lig_sanction->id_sanction' />\n";
			}
			else {
				if($lig_sanction->effectuee=="O") {echo "<span style='color:green'>O</span>";} else {echo "<span style='color:red'>N</span>";}
			}
			echo "</td>\n";
		
			echo "</tr>\n";
			$cpt_sanctions++;
			$num++;
		}
	}
	echo "</table>\n";
	echo "<p align='center'><input type='submit' value=\"Valider\" /></p>\n";
	echo "</blockquote>\n";

	echo envoi_mail_rappel_js();
}

// Exclusions
$sql="SELECT * FROM s_sanctions s, s_exclusions se WHERE se.id_sanction=s.id_sanction AND se.date_debut<='".$mysql_jour_sanction."' AND se.date_fin>='".$mysql_jour_sanction."' ORDER BY se.date_debut, se.heure_debut, se.lieu;";
//echo "$sql<br />\n";
$res_sanction=mysql_query($sql);
if(mysql_num_rows($res_sanction)>0) {
	echo "<p class='bold'>Exclusions (<em>et assimilées</em>) du jour&nbsp;: $jour_sanction</p>\n";
	echo "<blockquote>\n";
	echo "<table class='boireaus' border='1' summary='Exclusions' style='margin:2px;'>\n";
	echo "<tr>\n";
	echo "<th title=\"Numéro de l'incident\">N°i</th>\n";
	echo "<th>Nature</th>\n";
	echo "<th>Elève</th>\n";
	echo "<th>Date début</th>\n";
	echo "<th>Heure début</th>\n";
	echo "<th>Date fin</th>\n";
	echo "<th>Heure fin</th>\n";
	echo "<th>Lieu</th>\n";
	echo "<th>Travail</th>\n";
    echo "<th>Effectuée</th>\n";
	echo "</tr>\n";
	$alt_b=1;
	while($lig_sanction=mysql_fetch_object($res_sanction)) {
		if(($_SESSION['statut']!='professeur')||(in_array($lig_sanction->id_incident, $tab_incidents_prof))) {
			$alt_b=$alt_b*(-1);
			echo "<tr class='lig$alt_b'>\n";

			echo "<td>";

			$texte=rappel_incident($lig_sanction->id_incident, 'retour');
			$tabdiv_infobulle[]=creer_div_infobulle("incident_".$lig_sanction->id_incident,"".ucfirst($mod_disc_terme_incident)." n°$lig_sanction->id_incident","",$texte,"",30,0,'y','y','n','n');

			if(($_SESSION['statut']!='professeur')||(in_array($lig_sanction->id_incident, $tab_incidents_prof_declarant))) {
				echo "<a href='saisie_incident.php?id_incident=$lig_sanction->id_incident&amp;step=2'";
				echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('incident_".$lig_sanction->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo ">$lig_sanction->id_incident</a>";
			}
			else {
				echo "<a href='#'";
				echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('incident_".$lig_sanction->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo " onclick='return false;'";
				echo ">";
				echo $lig_sanction->id_incident;
				echo "</a>";
			}
			echo "</td>\n";

			echo "<td>".ucfirst($lig_sanction->nature)."</td>\n";

			echo "<td>";
			echo p_nom($lig_sanction->login);
			echo " (<i>";
			$tmp_tab=get_class_from_ele_login($lig_sanction->login);
			//if(isset($tmp_tab['liste'])) {echo $tmp_tab['liste'];}
			if(isset($tmp_tab['liste_nbsp'])) {echo $tmp_tab['liste_nbsp'];}
			echo "</i>)";
			echo "</td>\n";

			echo "<td>".formate_date($lig_sanction->date_debut)."</td>\n";
			echo "<td>$lig_sanction->heure_debut</td>\n";
			echo "<td>".formate_date($lig_sanction->date_fin)."</td>\n";
			echo "<td>$lig_sanction->heure_fin</td>\n";
			echo "<td>$lig_sanction->lieu</td>\n";
			//echo "<td>".nl2br($lig_sanction->travail)."</td>\n";
			echo "<td style='text-align:left;'>";
			$travail=$lig_sanction->travail;

			$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
			if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
				$texte="Aucun travail";
			}
			else {
				$texte=nl2br($lig_sanction->travail);
				if($tmp_doc_joints!="") {
					if($texte!="") {$texte.="<br />";}
					$texte.="<b>Documents joints</b>&nbsp;:<br />";
					$texte.=$tmp_doc_joints;
				}

				if($details=="y") {
					echo $texte;
				}
				else {
					$tabdiv_infobulle[]=creer_div_infobulle("div_travail_sanction_$lig_sanction->id_sanction","Travail (sanction n°$lig_sanction->id_sanction)","",$texte,"",20,0,'y','y','n','n',2);
	
					echo " <a href=\"".$_SERVER['PHP_SELF']."?jour_sanction=$jour_sanction&amp;details=y\"";
					//echo " onmouseover=\"delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
					//echo " onmouseover=\"cacher_toutes_les_infobulles();afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',20,20);\"";
					echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo ">Détails</a>";
				}
			}

			echo "<td>\n";
			$marquer_sanction_effectuee_possible="y";
			if(($_SESSION['statut']=='professeur')&&(!sanction_saisie_par($lig_sanction->id_sanction, $_SESSION['login']))) {
				$marquer_sanction_effectuee_possible="n";
			}

			if($marquer_sanction_effectuee_possible=="y") {
				echo "<input type='checkbox' name='sanction_effectuee[$lig_sanction->id_sanction]' value='effectuee' ";
				if($lig_sanction->effectuee=="O") {echo "checked='checked' ";}
				echo "onchange='changement();' ";
				echo "/>\n";
				echo "<input type='hidden' name='form_id_sanction[]' value='$lig_sanction->id_sanction' />\n";
			}
			else {
				if($lig_sanction->effectuee=="O") {echo "<span style='color:green'>O</span>";} else {echo "<span style='color:red'>N</span>";}
			}
			echo "</td>\n";

			echo "</tr>\n";
			$cpt_sanctions++;
		}
	}
	echo "</table>\n";
    echo "<p align='center'><input type='submit' value=\"Valider\" /></p>\n";
	echo "</blockquote>\n";
}

// Simple travail
$sql="SELECT * FROM s_sanctions s, s_travail st WHERE st.id_sanction=s.id_sanction AND st.date_retour='".$mysql_jour_sanction."' ORDER BY st.date_retour;";
//echo "$sql<br />\n";
$res_sanction=mysql_query($sql);
if(mysql_num_rows($res_sanction)>0) {
	echo "<p class='bold'>Travaux à rendre pour le jour&nbsp;: $jour_sanction</p>\n";
	echo "<blockquote>\n";
	echo "<table class='boireaus' border='1' summary='Travail' style='margin:2px;'>\n";
	echo "<tr>\n";
	echo "<th title=\"Numéro de l'incident\">N°i</th>\n";
	echo "<th>Nature</th>\n";
	echo "<th>Elève</th>\n";
	echo "<th>Travail</th>\n";
	echo "<th>Donné par (Déclarant)</th>\n";
	echo "<th>Effectué</th>\n";
	echo "</tr>\n";
	$alt_b=1;
	while($lig_sanction=mysql_fetch_object($res_sanction)) {
		if(($_SESSION['statut']!='professeur')||(in_array($lig_sanction->id_incident, $tab_incidents_prof))) {
			$alt_b=$alt_b*(-1);
			if($lig_sanction->effectuee=="O") {
				echo "<tr style='background-color: lightgrey;'>\n";
			}
			else {
				echo "<tr class='lig$alt_b'>\n";
			}

			echo "<td>";

			$texte=rappel_incident($lig_sanction->id_incident, 'retour');
			$tabdiv_infobulle[]=creer_div_infobulle("incident_".$lig_sanction->id_incident,"".ucfirst($mod_disc_terme_incident)." n°$lig_sanction->id_incident","",$texte,"",30,0,'y','y','n','n');

			if(($_SESSION['statut']!='professeur')||(in_array($lig_sanction->id_incident, $tab_incidents_prof_declarant))) {
				echo "<a href='saisie_incident.php?id_incident=$lig_sanction->id_incident&amp;step=2'";
				echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('incident_".$lig_sanction->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo ">$lig_sanction->id_incident</a>";
			}
			else {
				echo "<a href='#'";
				echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('incident_".$lig_sanction->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo " onclick='return false;'";
				echo ">";
				echo $lig_sanction->id_incident;
				echo "</a>";
			}
			echo "</td>\n";

			echo "<td>".ucfirst($lig_sanction->nature)."</td>\n";

			echo "<td>";
			echo p_nom($lig_sanction->login);
			echo " (<i>";
			$tmp_tab=get_class_from_ele_login($lig_sanction->login);
			//if(isset($tmp_tab['liste'])) {echo $tmp_tab['liste'];}
			if(isset($tmp_tab['liste_nbsp'])) {echo $tmp_tab['liste_nbsp'];}
			echo "</i>)";
			echo "</td>\n";

			echo "<td style='text-align:left;'>\n";
			$travail=$lig_sanction->travail;

			$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
			if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
				$texte="Aucun travail";
			}
			else {
				$texte=nl2br($lig_sanction->travail);
				if($tmp_doc_joints!="") {
					if($texte!="") {$texte.="<br />";}
					$texte.="<b>Documents joints</b>&nbsp;:<br />";
					$texte.=$tmp_doc_joints;
				}

				if($details=="y") {
					echo $texte;
				}
				else {
					$tabdiv_infobulle[]=creer_div_infobulle("div_travail_sanction_$lig_sanction->id_sanction","Travail (sanction n°$lig_sanction->id_sanction)","",$texte,"",20,0,'y','y','n','n',2);
	
					echo " <a href=\"".$_SERVER['PHP_SELF']."?jour_sanction=$jour_sanction&amp;details=y\"";
					//echo " onmouseover=\"delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
					//echo " onmouseover=\"cacher_toutes_les_infobulles();afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',20,20);\"";
					echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo ">Détails</a>";
				}
			}
			echo "</td>\n";
		
			echo "<td>\n";
			echo civ_nom_prenom(get_login_declarant_incident($lig_sanction->id_incident));
		    echo "</td>\n";
		
			echo "<td>\n";
			$marquer_sanction_effectuee_possible="y";
			if(($_SESSION['statut']=='professeur')&&(!sanction_saisie_par($lig_sanction->id_sanction, $_SESSION['login']))) {
				$marquer_sanction_effectuee_possible="n";
			}

			if($marquer_sanction_effectuee_possible=="y") {
				echo "<input type='checkbox' name='sanction_effectuee[$lig_sanction->id_sanction]' value='effectuee' ";
				if($lig_sanction->effectuee=="O") {echo "checked='checked' ";}
				echo "onchange='changement();' ";
				echo "/>\n";
				echo "<input type='hidden' name='form_id_sanction[]' value='$lig_sanction->id_sanction' />\n";
			}
			else {
				if($lig_sanction->effectuee=="O") {echo "<span style='color:green'>O</span>";} else {echo "<span style='color:red'>N</span>";}
			}
			echo "</td>\n";

			echo "</tr>\n";
			$cpt_sanctions++;
		}
	}
	echo "</table>\n";
	echo "<p align='center'><input type='submit' value=\"Valider\" /></p>\n";
	echo "</blockquote>\n";
}

if($cpt_sanctions==0) {
	echo "<p class='bold'>Liste des sanctions pour le $jour_sanction</p>\n";
	echo "<blockquote>\n";
	echo "<p>Aucune sanction ce jour&nbsp;: $jour_sanction</p>\n";
	echo "</blockquote>\n";
}

echo "<p><br /></p>\n";

//================================================================
// Liste des sanctions en souffrance... pouvoir les reprogrammer...

// Tableau des sanctions données par le prof
$tab_sanctions_prof=array();
if($_SESSION['statut']=='professeur') {
	$sql="SELECT id_sanction FROM s_sanctions WHERE saisie_par='".$_SESSION['login']."';";
	$res_sanctions_prof=mysql_query($sql);
	if(mysql_num_rows($res_sanctions_prof)>0) {
		while($lig_tmp=mysql_fetch_object($res_sanctions_prof)) {
			$tab_sanctions_prof[]=$lig_tmp->id_sanction;
		}
	}
}

echo "<a name='retenues_en_souffrance'></a>\n";
// Retenues
$sql="SELECT * FROM s_sanctions s, s_retenues sr WHERE sr.date<'$annee-$mois-$jour' AND s.effectuee!='O' AND sr.id_sanction=s.id_sanction ORDER BY sr.date $order_by_date, sr.heure_debut, sr.lieu, s.login;";
//echo "$sql<br />";
$res_sanction=mysql_query($sql);
if(mysql_num_rows($res_sanction)>0) {
	echo "<p class='bold'>Liste des retenues (<em>et assimilées</em>) non effectuées pour une date antérieure au $jour_sanction</p>\n";
	echo "<blockquote>\n";
	echo "<table class='boireaus' border='1' summary='Retenues' style='margin:2px;'>\n";
	echo "<tr>\n";
	//echo "<th>Date</th>\n";
	echo "<th title=\"Numéro de l'incident\">N°i</th>\n";
	echo "<th>Nature</th>\n";
	echo "<th><a href='".$_SERVER['PHP_SELF']."?jour_sanction=$jour_sanction&amp;details=$details&amp;order_by_date=";
	if($order_by_date=='asc') {echo "desc";} else {echo "asc";}
	echo "#retenues_en_souffrance'>Date</a></th>\n";
	echo "<th>Heure</th>\n";
	echo "<th>Durée</th>\n";
	echo "<th>Lieu</th>\n";
	echo "<th>Elève</th>\n";
	echo "<th>Travail</th>\n";
	echo "<th>Donné par (Déclarant)</th>\n";
	echo "<th>Nbre de report</th>\n";
	echo "<th>Effectuée</th>\n";
	echo "</tr>\n";
	$alt_b=1;
	while($lig_sanction=mysql_fetch_object($res_sanction)) {
		if(($_SESSION['statut']!='professeur')||(in_array($lig_sanction->id_incident, $tab_incidents_prof))) {
			$alt_b=$alt_b*(-1);
			echo "<tr class='lig$alt_b'>\n";

			echo "<td>";

			$texte=rappel_incident($lig_sanction->id_incident, 'retour');
			$tabdiv_infobulle[]=creer_div_infobulle("incident_".$lig_sanction->id_incident,"".ucfirst($mod_disc_terme_incident)." n°$lig_sanction->id_incident","",$texte,"",30,0,'y','y','n','n');

			if(($_SESSION['statut']!='professeur')||(in_array($lig_sanction->id_incident, $tab_incidents_prof_declarant))) {
				echo "<a href='saisie_incident.php?id_incident=$lig_sanction->id_incident&amp;step=2'";
				echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('incident_".$lig_sanction->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo ">$lig_sanction->id_incident</a>";
			}
			else {
				echo "<a href='#'";
				echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('incident_".$lig_sanction->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo " onclick='return false;'";
				echo ">";
				echo $lig_sanction->id_incident;
				echo "</a>";
			}
			echo "</td>\n";

			echo "<td>".ucfirst($lig_sanction->nature)."</td>\n";
			//echo "<td><a href='saisie_sanction.php?mode=modif&amp;valeur=retenue&amp;ele_login=$lig_sanction->login&amp;id_incident=$lig_sanction->id_incident&amp;id_sanction=$lig_sanction->id_sanction' title='Reprogrammer'";
			echo "<td><a href='saisie_sanction.php?mode=modif&amp;valeur=$lig_sanction->id_nature_sanction&amp;ele_login=$lig_sanction->login&amp;id_incident=$lig_sanction->id_incident&amp;id_sanction=$lig_sanction->id_sanction' title='Reprogrammer'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">".formate_date($lig_sanction->date)."</a></td>\n";
			echo "<td>$lig_sanction->heure_debut</td>\n";
			echo "<td>$lig_sanction->duree</td>\n";
			echo "<td>$lig_sanction->lieu</td>\n";
			echo "<td>";
			echo p_nom($lig_sanction->login);
			echo " (<i>";
			$tmp_tab=get_class_from_ele_login($lig_sanction->login);
			//if(isset($tmp_tab['liste'])) {echo $tmp_tab['liste'];}
			if(isset($tmp_tab['liste_nbsp'])) {echo $tmp_tab['liste_nbsp'];}
			echo "</i>)";
			echo "</td>\n";
			echo "<td style='text-align:left;'>";
			$travail=$lig_sanction->travail;

			$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
			if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
				$texte="Aucun travail";
			}
			else {
				$texte=nl2br($lig_sanction->travail);
				if($tmp_doc_joints!="") {
					if($texte!="") {$texte.="<br />";}
					$texte.="<b>Documents joints</b>&nbsp;:<br />";
					$texte.=$tmp_doc_joints;
				}

				if($details=="y") {
					echo $texte;
				}
				else {
					$tabdiv_infobulle[]=creer_div_infobulle("div_travail_sanction_$lig_sanction->id_sanction","Travail (sanction n°$lig_sanction->id_sanction)","",$texte,"",20,0,'y','y','n','n',2);
	
					echo " <a href=\"".$_SERVER['PHP_SELF']."?jour_sanction=$jour_sanction&amp;details=y\"";
					//echo " onmouseover=\"delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
					//echo " onmouseover=\"cacher_toutes_les_infobulles();afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',20,20);\"";
					echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo ">Détails</a>";
				}
			}
			echo "</td>\n";
		
			echo "<td>\n";
			$login_declarant=get_login_declarant_incident($lig_sanction->id_incident);
			echo u_p_nom($login_declarant);
		    echo "</td>\n";
		
			echo "<td>\n";
			echo nombre_reports($lig_sanction->id_sanction,"Néant");
		    echo "</td>\n";


			echo "<td>\n";
			if(($_SESSION['statut']=='professeur')&&(in_array($lig_sanction->id_sanction, $tab_sanctions_prof))) {
				echo "<input type='checkbox' name='sanction_effectuee[$lig_sanction->id_sanction]' value='effectuee' ";
				if($lig_sanction->effectuee=="O") {echo "checked='checked' ";}
				echo "onchange='changement();' ";
				echo "/>\n";
				echo "<input type='hidden' name='form_id_sanction[]' value='$lig_sanction->id_sanction' />\n";
			}
			else {
				if($lig_sanction->effectuee=="O") {echo "<span style='color:green'>O</span>";} else {echo "<span style='color:red'>N</span>";}
			}
			echo "</td>\n";

			echo "</tr>\n";
			$cpt_sanctions++;
		}
	}
	echo "</table>\n";
	echo "<p align='center'><input type='submit' value=\"Valider\" /></p>\n";
	echo "</blockquote>\n";
}

// Simple travail
echo "<a name='travaux_en_souffrance'></a>\n";
$sql="SELECT * FROM s_sanctions s, s_travail st WHERE st.id_sanction=s.id_sanction AND st.date_retour<'$annee-$mois-$jour' AND s.effectuee!='O' ORDER BY st.date_retour $order_by_date;";
//echo "$sql<br />\n";
$res_sanction=mysql_query($sql);
if(mysql_num_rows($res_sanction)>0) {
	echo "<p class='bold'>Travaux à rendre pour une date antérieure au $jour_sanction</p>\n";
	echo "<blockquote>\n";
	echo "<table class='boireaus' border='1' summary='Travail' style='margin:2px;'>\n";
	echo "<tr>\n";
	echo "<th title=\"Numéro de l'incident\">N°i</th>\n";
	echo "<th>Nature</th>\n";
	echo "<th>Elève</th>\n";
	//echo "<th>Date de retour</th>\n";
	echo "<th><a href='".$_SERVER['PHP_SELF']."?jour_sanction=$jour_sanction&amp;details=$details&amp;order_by_date=";
	if($order_by_date=='asc') {echo "desc";} else {echo "asc";}
	echo "#travaux_en_souffrance'>Date de retour</a></th>\n";
	echo "<th>Travail</th>\n";
	echo "<th>Donné par (Déclarant)</th>\n";
	echo "<th>Effectué</th>\n";
	echo "</tr>\n";
	$alt_b=1;
	while($lig_sanction=mysql_fetch_object($res_sanction)) {
		if(($_SESSION['statut']!='professeur')||(in_array($lig_sanction->id_incident, $tab_incidents_prof))) {
			$alt_b=$alt_b*(-1);
			echo "<tr class='lig$alt_b'>\n";

			echo "<td>";

			$texte=rappel_incident($lig_sanction->id_incident, 'retour');
			$tabdiv_infobulle[]=creer_div_infobulle("incident_".$lig_sanction->id_incident,"".ucfirst($mod_disc_terme_incident)." n°$lig_sanction->id_incident","",$texte,"",30,0,'y','y','n','n');

			if(($_SESSION['statut']!='professeur')||(in_array($lig_sanction->id_incident, $tab_incidents_prof_declarant))) {
				echo "<a href='saisie_incident.php?id_incident=$lig_sanction->id_incident&amp;step=2'";
				echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('incident_".$lig_sanction->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo ">$lig_sanction->id_incident</a>";
			}
			else {
				echo "<a href='#'";
				echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('incident_".$lig_sanction->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo " onclick='return false;'";
				echo ">";
				echo $lig_sanction->id_incident;
				echo "</a>";
			}
			echo "</td>\n";

			echo "<td>".ucfirst($lig_sanction->nature)."</td>\n";

			echo "<td>";
			echo p_nom($lig_sanction->login);
			echo " (<i>";
			$tmp_tab=get_class_from_ele_login($lig_sanction->login);
			//if(isset($tmp_tab['liste'])) {echo $tmp_tab['liste'];}
			if(isset($tmp_tab['liste_nbsp'])) {echo $tmp_tab['liste_nbsp'];}
			echo "</i>)";
			echo "</td>\n";

			echo "<td>";
			echo formate_date($lig_sanction->date_retour);
			echo "</td>\n";

			echo "<td style='text-align:left;'>\n";
			$travail=$lig_sanction->travail;

			$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
			if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
				$texte="Aucun travail";
			}
			else {
				$texte=nl2br($lig_sanction->travail);
				if($tmp_doc_joints!="") {
					if($texte!="") {$texte.="<br />";}
					$texte.="<b>Documents joints</b>&nbsp;:<br />";
					$texte.=$tmp_doc_joints;
				}

				if($details=="y") {
					echo $texte;
				}
				else {
					$tabdiv_infobulle[]=creer_div_infobulle("div_travail_sanction_$lig_sanction->id_sanction","Travail (sanction n°$lig_sanction->id_sanction)","",$texte,"",20,0,'y','y','n','n',2);
	
					echo " <a href=\"".$_SERVER['PHP_SELF']."?jour_sanction=$jour_sanction&amp;details=y\"";
					//echo " onmouseover=\"delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
					//echo " onmouseover=\"cacher_toutes_les_infobulles();afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',20,20);\"";
					echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo ">Détails</a>";
				}
			}
			echo "</td>\n";

			echo "<td>\n";
			$login_declarant=get_login_declarant_incident($lig_sanction->id_incident);
			echo u_p_nom($login_declarant);
		    echo "</td>\n";

			echo "<td>\n";
			if(($_SESSION['statut']=='professeur')&&(in_array($lig_sanction->id_sanction, $tab_sanctions_prof))) {
				echo "<input type='checkbox' name='sanction_effectuee[$lig_sanction->id_sanction]' value='effectuee' ";
				if($lig_sanction->effectuee=="O") {echo "checked='checked' ";}
				echo "onchange='changement();' ";
				echo "/>\n";
				echo "<input type='hidden' name='form_id_sanction[]' value='$lig_sanction->id_sanction' />\n";
			}
			else {
				if($lig_sanction->effectuee=="O") {echo "<span style='color:green'>O</span>";} else {echo "<span style='color:red'>N</span>";}
			}
			echo "</td>\n";

			echo "</tr>\n";
			$cpt_sanctions++;
		}
	}
	echo "</table>\n";
	echo "<p align='center'><input type='submit' value=\"Valider\" /></p>\n";
	echo "</blockquote>\n";
}

echo "</form>\n";

if(isset($tabid_infobulle)){
	echo "<script type='text/javascript'>\n";
	echo "function cacher_toutes_les_infobulles() {\n";
	if(count($tabid_infobulle)>0){
		for($i=0;$i<count($tabid_infobulle);$i++){
			echo "cacher_div('".$tabid_infobulle[$i]."');\n";
		}
	}
	echo "}\n";
	echo "</script>\n";
}

echo "<p><br /></p>\n";

echo "<p><i>Remarques&nbsp;:</i></p>\n";
echo "<blockquote>\n";
echo "<p><b>Lorsqu'une retenue doit être reprogrammée</b>, cliquer sur la date initiale de la retenue et renseigner la section Gestion d'un report<br />\n";
echo "<p>Lorsqu'un travail doit être reprogrammé, l'information comme quoi l'élève ne l'a pas effectué à la date prévue n'est pas conservée.<br />A défaut, vous pouvez ajouter des détails sur l'incident ou en commentaire dans le Travail attribué</p>\n";
echo "</blockquote>\n";

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
