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

function liste_sanctions($id_incident,$ele_login) {
	// Pour que les infobulles définies ici fonctionnent même si elles sont appelées depuis une autre infobulle
	global $tabdiv_infobulle;
	global $delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle;

	$retour="";

	$sql="SELECT etat FROM s_incidents WHERE id_incident='$id_incident';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$retour="<p style='color:red;'>L'incident n°$id_incident n'existe pas???</p>\n";
	}
	else {
		$lig_inc=mysql_fetch_object($res);
		$etat_incident=$lig_inc->etat;

		// Retenues
		$sql="SELECT * FROM s_sanctions s, s_retenues sr WHERE s.id_incident=$id_incident AND s.login='".$ele_login."' AND sr.id_sanction=s.id_sanction ORDER BY sr.date, sr.heure_debut;";
		//$retour.="$sql<br />\n";
		$res_sanction=mysql_query($sql);
		$res_sanction_tmp=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
			$retour.="<table class='boireaus' border='1' summary='Retenues' style='margin:2px;'>\n";
			$retour.="<tr>\n";
			$retour.="<th>Nature</th>\n";
			$retour.="<th>Date</th>\n";
			$retour.="<th>Heure</th>\n";
			$retour.="<th>Durée</th>\n";
			$retour.="<th>Lieu</th>\n";
			$retour.="<th>Travail</th>\n";
			
			$lig_sanction_tmp=mysql_fetch_object($res_sanction_tmp);
			$nombre_de_report=nombre_reports($lig_sanction_tmp->id_sanction,0);
			if ($nombre_de_report <> 0) {
			   $retour.="<th>Nbre report</th>\n";
			}
			
			//if($etat_incident!='clos') {
			if(($etat_incident!='clos')&&($_SESSION['statut']!='professeur')) {
				$retour.="<th>Suppr</th>\n";
			}
			$retour.="</tr>\n";
			$alt_b=1;
			while($lig_sanction=mysql_fetch_object($res_sanction)) {
				$alt_b=$alt_b*(-1);
				$retour.="<tr class='lig$alt_b'>\n";
				//$retour.="<td>Retenue</td>\n";
				if(($etat_incident!='clos')&&(($_SESSION['statut']!='professeur')&&($_SESSION['statut']!='autre'))) {
					$retour.="<td><a href='saisie_sanction.php?mode=modif&amp;valeur=retenue&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$ele_login'>".ucfirst($lig_sanction->nature)."</a></td>\n";
				}
				else {
					$retour.="<td>".ucfirst($lig_sanction->nature)."</td>\n";
				}
				$retour.="<td>".formate_date($lig_sanction->date)."</td>\n";
				$retour.="<td>$lig_sanction->heure_debut</td>\n";
				$retour.="<td>$lig_sanction->duree</td>\n";
				$retour.="<td>$lig_sanction->lieu</td>\n";
				//$retour.="<td>".nl2br($lig_sanction->travail)."</td>\n";
				
				$retour.="<td>";

				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
					$texte="Aucun travail";
				}
				else {
					$texte=nl2br($lig_sanction->travail);
					if($tmp_doc_joints!="") {
						if($texte!="") {$texte.="<br />";}
						$texte.=$tmp_doc_joints;
					}
				}

				$tabdiv_infobulle[]=creer_div_infobulle("div_travail_sanction_$lig_sanction->id_sanction","Travail (sanction n°$lig_sanction->id_sanction)","",$texte,"",20,0,'y','y','n','n',2);

				$retour.=" <a href='#' onmouseover=\"document.getElementById('div_travail_sanction_$lig_sanction->id_sanction').style.zIndex=document.getElementById('sanctions_incident_$id_incident').style.zIndex+1;delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onclick=\"return false;\">Détails</a>";
				$retour.="</td>\n";
				
				if ($nombre_de_report <> 0) {
					$retour.="<td>\n";
					$retour.=$nombre_de_report;
					$retour.="</td>";
				}
				
				if(($etat_incident!='clos')&&($_SESSION['statut']!='professeur')) {
					//$retour.="<td><a href='".$_SERVER['PHP_SELF']."?mode=suppr_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident' title='Supprimer la sanction n°$lig_sanction->id_sanction'><img src='../images/icons/delete.png' width='16' height='16' alt='Supprimer la sanction n°$lig_sanction->id_sanction' /></a></td>\n";
					$retour.="<td><a href='saisie_sanction.php?mode=suppr_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident".add_token_in_url()."' title='Supprimer la sanction n°$lig_sanction->id_sanction'><img src='../images/icons/delete.png' width='16' height='16' alt='Supprimer la sanction n°$lig_sanction->id_sanction' /></a></td>\n";
				}
				$retour.="</tr>\n";
			}
			$retour.="</table>\n";
		}

		// Exclusions
		$sql="SELECT * FROM s_sanctions s, s_exclusions se WHERE s.id_incident=$id_incident AND s.login='".$ele_login."' AND se.id_sanction=s.id_sanction ORDER BY se.date_debut, se.heure_debut;";
		//$retour.="$sql<br />\n";
		$res_sanction=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
			$retour.="<table class='boireaus' border='1' summary='Exclusions' style='margin:2px;'>\n";
			$retour.="<tr>\n";
			$retour.="<th>Nature</th>\n";
			$retour.="<th>Date début</th>\n";
			$retour.="<th>Heure début</th>\n";
			$retour.="<th>Date fin</th>\n";
			$retour.="<th>Heure fin</th>\n";
			$retour.="<th>Lieu</th>\n";
			$retour.="<th>Travail</th>\n";
			if(($etat_incident!='clos')&&($_SESSION['statut']!='professeur')) {
				$retour.="<th>Suppr</th>\n";
			}
			$retour.="</tr>\n";
			$alt_b=1;
			while($lig_sanction=mysql_fetch_object($res_sanction)) {
				$alt_b=$alt_b*(-1);
				$retour.="<tr class='lig$alt_b'>\n";
				//$retour.="<td>Exclusion</td>\n";
				if(($etat_incident!='clos')&&(($_SESSION['statut']!='professeur')&&($_SESSION['statut']!='autre'))) {
					$retour.="<td><a href='saisie_sanction.php?mode=modif&amp;valeur=exclusion&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$ele_login'>".ucfirst($lig_sanction->nature)."</a></td>\n";
				}
				else {
					$retour.="<td>".ucfirst($lig_sanction->nature)."</td>\n";
				}
				$retour.="<td>".formate_date($lig_sanction->date_debut)."</td>\n";
				$retour.="<td>$lig_sanction->heure_debut</td>\n";
				$retour.="<td>".formate_date($lig_sanction->date_fin)."</td>\n";
				$retour.="<td>$lig_sanction->heure_fin</td>\n";
				$retour.="<td>$lig_sanction->lieu</td>\n";
				//$retour.="<td>".nl2br($lig_sanction->travail)."</td>\n";
				$retour.="<td>";

				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
					$texte="Aucun travail";
				}
				else {
					$texte=nl2br($lig_sanction->travail);
					if($tmp_doc_joints!="") {
						if($texte!="") {$texte.="<br />";}
						$texte.=$tmp_doc_joints;
					}
				}
				$tabdiv_infobulle[]=creer_div_infobulle("div_travail_sanction_$lig_sanction->id_sanction","Travail (sanction n°$lig_sanction->id_sanction)","",$texte,"",20,0,'y','y','n','n',2);

				$retour.=" <a href='#' onmouseover=\"document.getElementById('div_travail_sanction_$lig_sanction->id_sanction').style.zIndex=document.getElementById('sanctions_incident_$id_incident').style.zIndex+1;delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onclick=\"return false;\">Détails</a>";
				$retour.="</td>\n";

				if(($etat_incident!='clos')&&($_SESSION['statut']!='professeur')) {
					//$retour.="<td><a href='".$_SERVER['PHP_SELF']."?mode=suppr_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident' title='Supprimer la sanction n°$lig_sanction->id_sanction'><img src='../images/icons/delete.png' width='16' height='16' alt='Supprimer la sanction n°$lig_sanction->id_sanction' /></a></td>\n";
					$retour.="<td><a href='saisie_sanction.php?mode=suppr_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident".add_token_in_url()."' title='Supprimer la sanction n°$lig_sanction->id_sanction'><img src='../images/icons/delete.png' width='16' height='16' alt='Supprimer la sanction n°$lig_sanction->id_sanction' /></a></td>\n";
				}
				$retour.="</tr>\n";
			}
			$retour.="</table>\n";
		}

		// Simple travail
		$sql="SELECT * FROM s_sanctions s, s_travail st WHERE s.id_incident=$id_incident AND s.login='".$ele_login."' AND st.id_sanction=s.id_sanction ORDER BY st.date_retour;";
		//$retour.="$sql<br />\n";
		$res_sanction=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
			$retour.="<table class='boireaus' border='1' summary='Travail' style='margin:2px;'>\n";
			$retour.="<tr>\n";
			$retour.="<th>Nature</th>\n";
			$retour.="<th>Date retour</th>\n";
			$retour.="<th>Travail</th>\n";
			if($etat_incident!='clos') {
				$retour.="<th>Suppr</th>\n";
			}
			$retour.="</tr>\n";
			$alt_b=1;
			while($lig_sanction=mysql_fetch_object($res_sanction)) {
				$alt_b=$alt_b*(-1);
				$retour.="<tr class='lig$alt_b'>\n";
				if (($etat_incident!='clos')&&(($_SESSION['statut']!='professeur')&&($_SESSION['statut']!='autre'))) {
					$retour.="<td><a href='saisie_sanction.php?mode=modif&amp;valeur=travail&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$ele_login'>".ucfirst($lig_sanction->nature)."</a></td>\n";
				}
				else {
					$retour.="<td>".ucfirst($lig_sanction->nature)."</td>\n";
				}
				$retour.="<td>".formate_date($lig_sanction->date_retour)."</td>\n";
				$retour.="<td>";


				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
					$texte="Aucun travail";
				}
				else {
					$texte=nl2br($lig_sanction->travail);
					if($tmp_doc_joints!="") {
						if($texte!="") {$texte.="<br />";}
						$texte.=$tmp_doc_joints;
					}
				}
				$tabdiv_infobulle[]=creer_div_infobulle("div_travail_sanction_$lig_sanction->id_sanction","Travail (sanction n°$lig_sanction->id_sanction)","",$texte,"",20,0,'y','y','n','n',2);

				$retour.=" <a href='#' onmouseover=\"document.getElementById('div_travail_sanction_$lig_sanction->id_sanction').style.zIndex=document.getElementById('sanctions_incident_$id_incident').style.zIndex+1;delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onclick=\"return false;\">Détails</a>";
				$retour.="</td>\n";

				if(($etat_incident!='clos')&&($_SESSION['statut']!='professeur')) {
					//$retour.="<td><a href='".$_SERVER['PHP_SELF']."?mode=suppr_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident' title='Supprimer la sanction n°$lig_sanction->id_sanction'><img src='../images/icons/delete.png' width='16' height='16' alt='Supprimer la sanction n°$lig_sanction->id_sanction' /></a></td>\n";
					$retour.="<td><a href='saisie_sanction.php?mode=suppr_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident".add_token_in_url()."' title='Supprimer la sanction n°$lig_sanction->id_sanction'><img src='../images/icons/delete.png' width='16' height='16' alt='Supprimer la sanction n°$lig_sanction->id_sanction' /></a></td>\n";
				}
				$retour.="</tr>\n";
			}
			$retour.="</table>\n";
		}

		// Autres sanctions
		$sql="SELECT * FROM s_sanctions s, s_autres_sanctions sa, s_types_sanctions2 sts WHERE s.id_incident='$id_incident' AND s.login='".$ele_login."' AND sa.id_sanction=s.id_sanction AND sa.id_nature=sts.id_nature ORDER BY sts.nature;";
		//echo "$sql<br />\n";
		$res_sanction=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
			$retour.="<table class='boireaus' border='1' summary='Autres sanctions' style='margin:2px;'>\n";
			$retour.="<tr>\n";
			$retour.="<th>Nature</th>\n";
			$retour.="<th>Description</th>\n";
			$retour.="<th>Suppr</th>\n";
			$retour.="</tr>\n";
			$alt_b=1;
			while($lig_sanction=mysql_fetch_object($res_sanction)) {
				$alt_b=$alt_b*(-1);
				$retour.="<tr class='lig$alt_b'>\n";
				$retour.="<td><a href='".$_SERVER['PHP_SELF']."?mode=modif&amp;valeur=".$lig_sanction->id_nature."&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$ele_login'>$lig_sanction->nature</a></td>\n";

				$retour.="<td>\n";
				$texte=nl2br($lig_sanction->description);
				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if($tmp_doc_joints!="") {
					$texte.="<br />";
					$texte.=$tmp_doc_joints;
				}
				$tabdiv_infobulle[]=creer_div_infobulle("div_autre_sanction_$lig_sanction->id_sanction","$lig_sanction->nature (sanction n°$lig_sanction->id_sanction)","",$texte,"",20,0,'y','y','n','n');

				$retour.=" <a href='#' onmouseover=\"document.getElementById('div_autre_sanction_$lig_sanction->id_sanction').style.zIndex=document.getElementById('sanctions_incident_$id_incident').style.zIndex+1;delais_afficher_div('div_autre_sanction_$lig_sanction->id_sanction','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onclick=\"return false;\">Détails</a>";
				$retour.="</td>\n";

				if(($etat_incident!='clos')&&($_SESSION['statut']!='professeur')) {
					//$retour.="<td><a href='".$_SERVER['PHP_SELF']."?mode=suppr_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident' title='Supprimer la sanction n°$lig_sanction->id_sanction'><img src='../images/icons/delete.png' width='16' height='16' alt='Supprimer la sanction n°$lig_sanction->id_sanction' /></a></td>\n";
					$retour.="<td><a href='saisie_sanction.php?mode=suppr_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident".add_token_in_url()."' title='Supprimer la sanction n°$lig_sanction->id_sanction'><img src='../images/icons/delete.png' width='16' height='16' alt='Supprimer la sanction n°$lig_sanction->id_sanction' /></a></td>\n";
				}
				$retour.="</tr>\n";
			}
			$retour.="</table>\n";
		}


	}
	return $retour;
}

// Pour choisir de n'afficher que les incidents de la date indiquée:
$date_incident=isset($_POST['date_incident']) ? $_POST['date_incident'] : (isset($_GET['date_incident']) ? $_GET['date_incident'] : "");
$heure_incident=isset($_POST['heure_incident']) ? $_POST['heure_incident'] : (isset($_GET['heure_incident']) ? $_GET['heure_incident'] : "");
//$nature_incident=isset($_POST['nature_incident']) ? $_POST['nature_incident'] : (isset($_GET['nature_incident']) ? $_GET['nature_incident'] : "");
$nature_incident=isset($_POST['nature_incident']) ? $_POST['nature_incident'] : (isset($_GET['nature_incident']) ? $_GET['nature_incident'] : "---");
$protagoniste_incident=isset($_POST['protagoniste_incident']) ? $_POST['protagoniste_incident'] : (isset($_GET['protagoniste_incident']) ? $_GET['protagoniste_incident'] : "");

//$declarant_incident=isset($_POST['declarant_incident']) ? $_POST['declarant_incident'] : (isset($_GET['declarant_incident']) ? $_GET['declarant_incident'] : "");
$declarant_incident=isset($_POST['declarant_incident']) ? $_POST['declarant_incident'] : (isset($_GET['declarant_incident']) ? $_GET['declarant_incident'] : "---");
//echo "\$declarant_incident=$declarant_incident<br />";
$declarant_incident2=isset($_POST['declarant_incident2']) ? $_POST['declarant_incident2'] : (isset($_GET['declarant_incident2']) ? $_GET['declarant_incident2'] : NULL);
if(isset($declarant_incident2)) {$declarant_incident=$declarant_incident2;}
//echo "\$declarant_incident2=$declarant_incident2<br />";
//echo "\$declarant_incident=$declarant_incident<br />";

$incidents_clos=isset($_POST['incidents_clos']) ? $_POST['incidents_clos'] : (isset($_GET['incidents_clos']) ? $_GET['incidents_clos'] : "n");

$id_classe_incident=isset($_POST['id_classe_incident']) ? $_POST['id_classe_incident'] : (isset($_GET['id_classe_incident']) ? $_GET['id_classe_incident'] : "");

//$declarant_incident=isset($_POST['declarant_incident']) ? $_POST['declarant_incident'] : (isset($_GET['declarant_incident']) ? $_GET['declarant_incident'] : "");

$msg="";

//if(isset($_POST['modifier_etat_incidents'])) {
	//$etat_incident=isset($_POST['etat_incident']) ? $_POST['etat_incident'] : NULL;
	$form_id_incident=isset($_POST['form_id_incident']) ? $_POST['form_id_incident'] : NULL;
	//if(isset($etat_incident)) {
	if(isset($form_id_incident)) {
		//$form_id_incident=isset($_POST['form_id_incident']) ? $_POST['form_id_incident'] : NULL;
		$etat_incident=isset($_POST['etat_incident']) ? $_POST['etat_incident'] : array();
		for($i=0;$i<count($form_id_incident);$i++) {
			$acces_modif_etat="y";
			if($_SESSION['statut']=='professeur') {
				$sql="SELECT 1=1 FROM s_incidents WHERE id_incident='".$form_id_incident[$i]."' AND declarant='".$_SESSION['login']."';";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)==0) {$acces_modif_etat="n";}
			}

			if($acces_modif_etat=="y") {
				check_token();

				if(isset($etat_incident[$form_id_incident[$i]])) {
					$sql="UPDATE s_incidents SET etat='clos' WHERE id_incident='".$form_id_incident[$i]."';";
				}
				else {
					$sql="UPDATE s_incidents SET etat='' WHERE id_incident='".$form_id_incident[$i]."';";
				}
				//echo "$sql<br />";
				$res=mysql_query($sql);
				if(!$res) {
					$msg.="ERREUR lors de la mise à jour de l'état de l'incident n°".$form_id_incident[$i].".<br />\n";
				}
			}
		}
	}
//}

//if(isset($_POST['suppr_incident'])) {
if((isset($_POST['suppr_incident']))&&(($_SESSION['statut']!='professeur')||($_SESSION['statut']=='autre'))) {
	check_token();

	$suppr_incident=$_POST['suppr_incident'];
	for($i=0;$i<count($suppr_incident);$i++) {
		$temoin_erreur="n";

		// Nettoyage fichiers incident
		$suppr_doc_joints_incident=suppr_doc_joints_incident($suppr_incident[$i],"y");
		if($suppr_doc_joints_incident!="") {
			$msg.=$suppr_doc_joints_incident;
			$temoin_erreur="y";
		}

		if($temoin_erreur=="n") {
			$sql="DELETE FROM s_protagonistes WHERE id_incident='$suppr_incident[$i]';";
			//echo "$sql<br />\n";
			$res=mysql_query($sql);
			if(!$res) {
				$msg.="ERREUR lors de la suppression des protagonistes de l'incident ".$suppr_incident[$i].".<br />\n";
				$temoin_erreur="y";
			}
		}

		if($temoin_erreur=="n") {
			$sql="SELECT id_sanction FROM s_sanctions s WHERE s.id_incident='$suppr_incident[$i]';";
			//echo "$sql<br />\n";
			$res_sanction=mysql_query($sql);
			if(mysql_num_rows($res_sanction)>0) {
				while($lig=mysql_fetch_object($res_sanction)) {
					$sql="DELETE FROM s_retenues WHERE id_sanction='$lig->id_sanction';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de la suppression de retenues attachées à l'incident ".$suppr_incident[$i].".<br />\n";
						$temoin_erreur="y";
					}

					$sql="DELETE FROM s_exclusions WHERE id_sanction='$lig->id_sanction';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de la suppression d'excluions attachées à l'incident ".$suppr_incident[$i].".<br />\n";
						$temoin_erreur="y";
					}

					$sql="DELETE FROM s_travail WHERE id_sanction='$lig->id_sanction';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de la suppression de travaux attachés à l'incident ".$suppr_incident[$i].".<br />\n";
						$temoin_erreur="y";
					}

					$sql="DELETE FROM s_autres_sanctions WHERE id_sanction='$lig->id_sanction';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de la suppression d'autres sanctions attachées à l'incident ".$suppr_incident[$i].".<br />\n";
						$temoin_erreur="y";
					}
				}

				if($temoin_erreur=="n") {
					//$sql="DELETE FROM s_sanctions s WHERE s.id_incident='$suppr_incident[$i]';";
					$sql="DELETE FROM s_sanctions WHERE id_incident='$suppr_incident[$i]';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de la suppression de la sanction associée à l'incident ".$suppr_incident[$i].".<br />\n";
						$temoin_erreur="y";
					}
				}
			}

			if($temoin_erreur=="n") {
				$sql="DELETE FROM s_traitement_incident WHERE id_incident='$suppr_incident[$i]';";
				//echo "$sql<br />\n";
				$res=mysql_query($sql);
				if(!$res) {
					$msg.="ERREUR lors de la suppression des traitements d'incident (mesures) de l'incident ".$suppr_incident[$i].".<br />\n";
					$temoin_erreur="y";
				}

				if($temoin_erreur=="n") {
					$sql="DELETE FROM s_travail_mesure WHERE id_incident='$suppr_incident[$i]';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de la suppression des travaux proposés pour une mesure demandée de l'incident ".$suppr_incident[$i].".<br />\n";
						$temoin_erreur="y";
					}
				}

				if($temoin_erreur=="n") {
					$sql="DELETE FROM s_incidents WHERE id_incident='$suppr_incident[$i]';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de la suppression de l'incident ".$suppr_incident[$i].".<br />\n";
					}
				}
			}
		}
	}
}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
if (($_SESSION['statut']=='professeur')||($_SESSION['statut']=='autre'))  {
	$titre_page = "Discipline: Consulter un incident";
}
else {
	$titre_page = "Discipline: Traiter un incident";
}
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

// Paramètres destinés à récupérer une partie seulement des incidents
$limit=isset($_POST['limit']) ? $_POST['limit'] : (isset($_GET['limit']) ? $_GET['limit'] : 20);
$debut=isset($_POST['debut']) ? $_POST['debut'] : (isset($_GET['debut']) ? $_GET['debut'] : 0);

//===================================
$email_visiteur="";
$sql="SELECT email FROM utilisateurs WHERE login='".$_SESSION['login']."' AND email!='';";
$res_mail=mysql_query($sql);
if(mysql_num_rows($res_mail)>0) {
	$lig_mail=mysql_fetch_object($res_mail);
	$email_visiteur=$lig_mail->email;
}
//===================================

// Tableau destiné à stocker la désignation d'un professeur, élève,... pour ne pas faire plusieurs fois les mêmes requêtes
$tab_individu=array();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'index</a>\n";
//echo "</p>\n";

if(($_SESSION['statut']=='administrateur')||
($_SESSION['statut']=='cpe')||
($_SESSION['statut']=='scolarite')) {
	$sql="SELECT 1=1 FROM s_incidents si
	LEFT JOIN s_protagonistes sp ON sp.id_incident=si.id_incident
	WHERE sp.id_incident IS NULL
	LIMIT 1;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo " | <a href='incidents_sans_protagonistes.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Incidents sans protagonistes</a>\n";
	}
}
elseif (($_SESSION['statut']=='professeur')||($_SESSION['statut']=='autre')) {
	$sql="SELECT 1=1 FROM s_incidents si
	LEFT JOIN s_protagonistes sp ON sp.id_incident=si.id_incident
	WHERE sp.id_incident IS NULL
	LIMIT 1;";
	//echo "$sql<br />";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo " | <a href='incidents_sans_protagonistes.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Incidents sans protagonistes</a>\n";
	}
}

if((file_exists('mod_discipline_extraction_ooo.php'))&&(($_SESSION['statut']=='cpe')||($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite'))) {
	echo " | <a href='mod_discipline_extraction_ooo.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Export ODT</a>\n";
}

if(!isset($id_incident)) {
	$chaine_criteres="";
	//$sql="SELECT * FROM s_incidents si, s_protagonistes sp ORDER BY date,heure,login;";
	//$sql="SELECT si.* FROM s_incidents si WHERE 1";

	if(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='autre')) {
		$sql="(SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp WHERE (sp.login='".$_SESSION['login']."' OR si.declarant='".$_SESSION['login']."') AND sp.id_incident=si.id_incident";
	}
	else {
		if($id_classe_incident=="") {
			$sql="(SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp WHERE sp.id_incident=si.id_incident";
		}
		else {
			$sql="(SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp, j_eleves_classes jec WHERE sp.id_incident=si.id_incident AND jec.id_classe='$id_classe_incident' AND jec.login=sp.login";
		}
	}

	$ajout_sql="";
	if($date_incident!="") {$ajout_sql.=" AND si.date='$date_incident'";$chaine_criteres.="&amp;date_incident=$date_incident";}
	if($heure_incident!="") {$ajout_sql.=" AND si.heure='$heure_incident'";$chaine_criteres.="&amp;heure_incident=$heure_incident";}
	//if($nature_incident!="") {$ajout_sql.=" AND si.nature='$nature_incident'";$chaine_criteres.="&amp;nature_incident=$nature_incident";}
	if($nature_incident!="---") {$ajout_sql.=" AND si.nature='$nature_incident'";$chaine_criteres.="&amp;nature_incident=$nature_incident";}
	if($protagoniste_incident!="") {$ajout_sql.=" AND sp.login='$protagoniste_incident'";$chaine_criteres.="&amp;protagoniste_incident=$protagoniste_incident";}

	//echo "\$declarant_incident=$declarant_incident<br />";

	if($declarant_incident!="---") {$ajout_sql.=" AND si.declarant='$declarant_incident'";$chaine_criteres.="&amp;declarant_incident=$declarant_incident";}

	if($id_classe_incident!="") {
		$chaine_criteres.="&amp;id_classe_incident=$id_classe_incident";
	}

	$sql.=$ajout_sql;
	$sql2=$sql;
	if($incidents_clos!="y") {$sql.=" AND si.etat!='clos'";}

	$sql.=")";
	$sql2.=")";

	//if($_SESSION['statut']=='professeur') {
	if($_SESSION['statut']=='professeur') {
		//if($declarant_incident=="") {
		// On ne devrait plus avoir $declarant_incident=="", mais plutôt $declarant_incident=="---"
		if(($declarant_incident=="")||($declarant_incident=="---")) {
			if(getSettingValue('visuDiscProfClasses')=='yes') {
				$ajout2_sql=" UNION (SELECT DISTINCT si.* FROM s_incidents si, 
																s_protagonistes sp, 
																j_eleves_classes jec,
																j_groupes_classes jgc,
																j_groupes_professeurs jgp
											WHERE sp.id_incident=si.id_incident AND 
													sp.login=jec.login AND
													jgp.id_groupe=jgc.id_groupe AND
													jgc.id_classe=jec.id_classe AND
													jgp.login='".$_SESSION['login']."'";
		
				$ajout2_sql.=$ajout_sql;
		
				$sql.=$ajout2_sql;
				$sql2.=$ajout2_sql;
				if($incidents_clos!="y") {$sql.=" AND si.etat!='clos'";}
		
				$sql.=")";
				$sql2.=")";
			}
			elseif(getSettingValue('visuDiscProfGroupes')=='yes') {
				$ajout2_sql=" UNION (SELECT DISTINCT si.* FROM s_incidents si, 
																s_protagonistes sp, 
																j_eleves_groupes jeg, 
																j_groupes_professeurs jgp 
											WHERE sp.id_incident=si.id_incident AND 
													sp.login=jeg.login AND 
													jgp.id_groupe=jeg.id_groupe AND
													jgp.login='".$_SESSION['login']."'";
		
				$ajout2_sql.=$ajout_sql;
		
				$sql.=$ajout2_sql;
				$sql2.=$ajout2_sql;
				if($incidents_clos!="y") {$sql.=" AND si.etat!='clos'";}
		
				$sql.=")";
				$sql2.=")";
			}

			// Pour qu'un professeur principal puisse consulter les incidents mettant en cause ses élèves
			$ajout2_sql=" UNION (SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp, j_eleves_professeurs jep WHERE sp.id_incident=si.id_incident AND sp.login=jep.login AND jep.professeur='".$_SESSION['login']."'";
	
			$ajout2_sql.=$ajout_sql;
	
			$sql.=$ajout2_sql;
			$sql2.=$ajout2_sql;
			if($incidents_clos!="y") {$sql.=" AND si.etat!='clos'";}
	
			$sql.=")";
			$sql2.=")";
		}
	}

	// 20130716
	if(!isset($limit)){
		$limit=20;
	}

	if(!isset($debut)){
		$debut=0;
	}
	elseif($limit=='TOUS') {
		$debut=0;
	}
	else{
		if(mb_strlen(preg_replace("/[0-9]/","",$debut))){
			$debut=0;
		}
	}

	$res=mysql_query($sql);
	$nb_incidents_en_tout_avec_criteres_choisis_hors_limitation_de_tranche=mysql_num_rows($res);
	if($debut>$nb_incidents_en_tout_avec_criteres_choisis_hors_limitation_de_tranche) {
		$debut=0;
	}
	// On va refaire la requête un peu après avec les limitations de tranche choisies
	if($limit!='TOUS'){
		$sql.=" LIMIT $debut,$limit";
		$sql2.=" LIMIT $debut,$limit";
	}

	//$sql.=" ORDER BY si.date DESC, si.heure DESC;";
	//$sql2.=" ORDER BY si.date DESC, si.heure DESC;";
	//$sql.=" ORDER BY date DESC, heure DESC;";
	//$sql2.=" ORDER BY date DESC, heure DESC;";

	//echo "$sql<br />";
	//echo "$sql2<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo " | <a href='".$_SERVER['PHP_SELF']."' onclick='history.go(-1);return false;'> Retour à la page précédente</a>\n";
		echo "</p>\n";

		if($incidents_clos=="y") {
			echo "<p>Aucun incident n'est encore déclaré";
			if(($date_incident!="")||
			($heure_incident!="")||
			($nature_incident!="---")||
			(($declarant_incident!="---")&&($declarant_incident!=""))||
			($protagoniste_incident!="")||
			//($declarant_incident!="")||
			($id_classe_incident!="")) {echo " avec les critères choisis";}
			echo ".</p>\n";
		}
		else {
			$res=mysql_query($sql2);
			if(mysql_num_rows($res)==0) {
				echo "<p>Aucun incident n'est encore déclaré";
				if(($date_incident!="")||
				($heure_incident!="")||
				($nature_incident!="---")||
				(($declarant_incident!="---")&&($declarant_incident!=""))||
				($protagoniste_incident!="")||
				//($declarant_incident!="")||
				($id_classe_incident!="")) {echo " avec les critères choisis";}
				echo ".</p>\n";
			}
			else {
				echo "<p>Aucun incident (<i>non clos</i>) n'est déclaré";
				if(($date_incident!="")||
				($heure_incident!="")||
				($nature_incident!="---")||
				(($declarant_incident!="---")&&($declarant_incident!=""))||
				($protagoniste_incident!="")||
				//($declarant_incident!="")||
				($id_classe_incident!="")) {echo " avec les critères choisis";}
				echo ".</p>\n";

				echo "<p><a href='".$_SERVER['PHP_SELF']."?incidents_clos=y$chaine_criteres'>Afficher les incidents clos avec les mêmes critères</a>.</p>\n";
			}
		}
		echo "<p><br /></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	echo "</p>\n";

	/*
	echo "<div style='float: right; border: 1px solid black;'>";
	echo mysql_num_rows($res)." incidents";
	if($chaine_criteres!="") {echo " avec les critères choisis";}
	echo "</div>\n";
	*/

	echo "<p class='bold'>Choisir l'incident à traiter/consulter&nbsp;:</p>\n";
	echo "<blockquote>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	echo add_token_field();
	echo "<p align='left'><input type='checkbox' name='incidents_clos' id='incidents_clos' value='y'";
	if($incidents_clos=="y") {echo " checked='checked'";}
	echo " /><label for='incidents_clos' style='cursor:pointer;'> Afficher les incidents clos</label>";

	// 20130716
	$sql_test_mes_incidents="SELECT 1=1 FROM s_incidents si, s_protagonistes sp WHERE si.declarant='".$_SESSION['login']."' AND si.id_incident=sp.id_incident LIMIT 1;";
	//$sql_test_mes_incidents="SELECT * FROM s_incidents si, s_protagonistes sp WHERE si.declarant='".$_SESSION['login']."' AND si.id_incident=sp.id_incident LIMIT 1;";
	//echo "<br />$sql_test_mes_incidents<br />";
	$res_test_mes_incidents=mysql_query($sql_test_mes_incidents);
	if(mysql_num_rows($res_test_mes_incidents)>0) {
		echo "<br />\n";
		echo "<input type='checkbox' name='declarant_incident2' id='declarant_incident2' value='".$_SESSION['login']."'";
		if($declarant_incident==$_SESSION['login']) {echo " checked='checked'";}
		echo " /><label for='declarant_incident2' style='cursor:pointer;'> Ne voir que mes déclarations d'incidents</label>\n";
	}
	echo "</p>\n";

	echo "<div style='float: right; border: 1px solid black;'>";
	echo "<a href='javascript:afficher_tous_les_incidents()' title=\"Afficher tous ces incidents\">".$nb_incidents_en_tout_avec_criteres_choisis_hors_limitation_de_tranche." incidents</a>";
	if($chaine_criteres!="") {echo " avec le(s) critère(s) choisi(s)";}
	echo "</div>\n";


//======================================
echo "<p>";
if($debut > 0){
	echo "<input type='button' value='<<' onClick='tranche_precedente()' /> \n";
}

/*
//echo "Afficher <select name='limit'>\n";
//echo "<input type='submit' value='Afficher' />\n";
echo "<input type='button' value='Afficher' onClick='decoche_suppr_et_valide();' />\n";
*/
echo "<select name='limit'>\n";
if($limit==20){$selected=" selected='true'";}else{$selected="";}
echo "<option value='20'$selected>20</option>\n";
if($limit==50){$selected=" selected='true'";}else{$selected="";}
echo "<option value='50'$selected>50</option>\n";
for($i=100;$i<=500;$i+=100){
	if($limit==$i){$selected=" selected='true'";}else{$selected="";}
	echo "<option value='$i'$selected>$i</option>\n";
}
if($limit=='TOUS'){$selected=" selected='true'";}else{$selected="";}
echo "<option value='TOUS'$selected>TOUS</option>\n";
echo "</select> enregistrements à partir de l'enregistrement n°\n";
echo "<input type='text' name='debut' value='$debut' size='5' /> \n";


if(isset($nb_incidents_en_tout_avec_criteres_choisis_hors_limitation_de_tranche)){
	if($limit+$debut<$nb_incidents_en_tout_avec_criteres_choisis_hors_limitation_de_tranche){
		echo "<input type='button' value='>>' onClick='tranche_suivante()' /> \n";
	}
}
else{
	echo "<input type='button' value='>>' onClick='tranche_suivante()' /> \n";
}

echo "</p>\n";



echo "<script type='text/javascript'>
	function afficher_tous_les_incidents() {
		document.forms.formulaire.debut.value=0;
		document.forms.formulaire.limit.selectedIndex=7;
		document.forms.formulaire.submit();
	}

	function tranche_precedente(){
		debut=document.forms.formulaire.debut.value;
		limit=document.forms.formulaire.limit.value;
		//alert('debut='+debut+' et limit='+limit);

		if(limit=='TOUS'){
			document.forms.formulaire.debut.value=0;
		}
		else{
			document.forms.formulaire.debut.value=Math.max(debut-limit,0);
		}
		document.forms.formulaire.submit();
	}

	function tranche_suivante(){
		debut=document.forms.formulaire.debut.value;
		limit=document.forms.formulaire.limit.value;
		//alert('debut='+debut+' et limit='+limit);

		if(limit=='TOUS'){
			document.forms.formulaire.debut.value=0;
		}
		else{
			// Il faudrait récupérer le nombre de lignes du tableau...\n";

if(isset($nb_incidents_en_tout_avec_criteres_choisis_hors_limitation_de_tranche)){
	echo "			document.forms.formulaire.debut.value=Math.min(eval(debut)+eval(limit),eval($nb_incidents_en_tout_avec_criteres_choisis_hors_limitation_de_tranche)-eval(limit));\n";
}
else{
	echo "			document.forms.formulaire.debut.value=eval(debut)+eval(limit);\n";
}

echo "		}
		document.forms.formulaire.submit();
	}
</script>\n";
//======================================



	echo "<p align='center'><input type='submit' name='valider' value='Valider' /></p>\n";

	$prof_is_pp="n";
	if($_SESSION['statut']=='professeur') {
		$prof_is_pp=is_pp($_SESSION['login']);
	}

	echo "<table class='boireaus' border='1' summary='Incidents'>\n";
	echo "<tr>\n";
	echo "<th>Id</th>\n";
	echo "<th>Date\n";
	echo "<br />\n";
	echo "<select name='date_incident' onchange=\"document.formulaire.submit();\">\n";
	echo "<option value=''>---</option>\n";
	if(($_SESSION['statut']=='professeur')) {
		$sql="(SELECT DISTINCT si.date FROM s_incidents si, s_protagonistes sp WHERE (sp.login='".$_SESSION['login']."' OR si.declarant='".$_SESSION['login']."') AND sp.id_incident=si.id_incident)";

		if($prof_is_pp) {
			$sql.=" UNION (SELECT DISTINCT si.date FROM s_incidents si, s_protagonistes sp, j_eleves_professeurs jep WHERE jep.login=sp.login AND jep.professeur='".$_SESSION['login']."' AND sp.id_incident=si.id_incident)";
		}

		if(getSettingAOui('visuDiscProfGroupes')) {
			$sql.=" UNION (SELECT DISTINCT si.date FROM s_incidents si, s_protagonistes sp, j_groupes_professeurs jgp, j_eleves_groupes jeg WHERE jeg.login=sp.login AND jeg.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' AND sp.id_incident=si.id_incident)";
		}

		if(getSettingAOui('visuDiscProfClasses')) {
			$sql.=" UNION (SELECT DISTINCT si.date FROM s_incidents si, s_protagonistes sp, j_groupes_professeurs jgp, j_groupes_classes jgc, j_eleves_classes jec WHERE jec.login=sp.login AND jec.id_classe=jgc.id_classe AND jgc.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' AND sp.id_incident=si.id_incident)";
		}

		//$sql.=" ORDER BY si.date DESC;";
		$sql.=" ORDER BY date DESC;";
	}
	else {
		$sql="SELECT DISTINCT si.date FROM s_incidents si ORDER BY si.date DESC;";
	}
	$res_dates=mysql_query($sql);
	while($lig_date=mysql_fetch_object($res_dates)) {
		echo "<option value='$lig_date->date'";
		if($date_incident==$lig_date->date) {echo " selected='selected'";}
		echo ">".formate_date($lig_date->date)."</option>\n";
	}
	echo "</select>\n";
	//echo "$sql<br />";
	echo "</th>\n";

	echo "<th>Heure\n";
	echo "<br />\n";
	echo "<select name='heure_incident' onchange=\"document.formulaire.submit();\">\n";
	echo "<option value=''>---</option>\n";
	if(($_SESSION['statut']=='professeur')) {
		$sql="(SELECT DISTINCT si.heure FROM s_incidents si, s_protagonistes sp WHERE (sp.login='".$_SESSION['login']."' OR si.declarant='".$_SESSION['login']."') AND sp.id_incident=si.id_incident)";

		if($prof_is_pp) {
			$sql.=" UNION (SELECT DISTINCT si.heure FROM s_incidents si, s_protagonistes sp, j_eleves_professeurs jep WHERE jep.login=sp.login AND jep.professeur='".$_SESSION['login']."' AND sp.id_incident=si.id_incident)";
		}

		if(getSettingAOui('visuDiscProfGroupes')) {
			$sql.=" UNION (SELECT DISTINCT si.heure FROM s_incidents si, s_protagonistes sp, j_groupes_professeurs jgp, j_eleves_groupes jeg WHERE jeg.login=sp.login AND jeg.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' AND sp.id_incident=si.id_incident)";
		}

		if(getSettingAOui('visuDiscProfClasses')) {
			$sql.=" UNION (SELECT DISTINCT si.heure FROM s_incidents si, s_protagonistes sp, j_groupes_professeurs jgp, j_groupes_classes jgc, j_eleves_classes jec WHERE jec.login=sp.login AND jec.id_classe=jgc.id_classe AND jgc.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' AND sp.id_incident=si.id_incident)";
		}

		//$sql.=" ORDER BY si.heure ASC;";
		$sql.=" ORDER BY heure ASC;";
	}
	else {
		$sql="SELECT DISTINCT si.heure FROM s_incidents si ORDER BY si.heure ASC;";
	}
	$res_heures=mysql_query($sql);
	while($lig_heure=mysql_fetch_object($res_heures)) {
		echo "<option value='$lig_heure->heure'";
		if($heure_incident==$lig_heure->heure) {echo " selected='selected'";}
		echo ">".$lig_heure->heure."</option>\n";
	}
	echo "</select>\n";
	//echo "$sql<br />";
	echo "</th>\n";
	
	if (!(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='autre'))) {
		echo "<th>Déclarant\n";
		echo "<br />\n";
		echo "<select name='declarant_incident' onchange=\"document.getElementById('declarant_incident2').checked=false;document.formulaire.submit();\">\n";
		//echo "<option value=''>---</option>\n";
		echo "<option value='---'>---</option>\n";
		if($_SESSION['statut']=='professeur') {
			$sql="(SELECT DISTINCT si.declarant FROM s_incidents si, s_protagonistes sp WHERE (sp.login='".$_SESSION['login']."' OR si.declarant='".$_SESSION['login']."') AND sp.id_incident=si.id_incident)";
			$sql.=" UNION (SELECT DISTINCT si.declarant FROM s_incidents si, s_protagonistes sp, j_eleves_professeurs jep WHERE jep.login=sp.login AND jep.professeur='".$_SESSION['login']."' AND sp.id_incident=si.id_incident)";
			//$sql.=" ORDER BY si.nature ASC;";
			$sql.=" ORDER BY declarant ASC;";
		}
		else {
			$sql="SELECT DISTINCT si.declarant FROM s_incidents si ORDER BY si.declarant ASC;";
		}
		//$chaine_tmp="<br />$sql<br />";
		$res_declarant=mysql_query($sql);
		while($lig_declarant=mysql_fetch_object($res_declarant)) {
			$chaine_tmp.="Declarant: $lig_declarant->declarant<br />";
			echo "<option value='$lig_declarant->declarant'";
			if($declarant_incident==$lig_declarant->declarant) {echo " selected='selected'";}
			if($lig_declarant->declarant!='') {
				if(!in_array($lig_declarant->declarant, array_keys($tab_individu))) {
					$sql_declarant="SELECT nom,prenom,civilite,statut, email FROM utilisateurs WHERE login='$lig_declarant->declarant';";
					//echo "$sql_declarant<br />\n";
					//$chaine_tmp.="$sql_declarant<br />";
					$res1_declarant=mysql_query($sql_declarant);
					if(mysql_num_rows($res1_declarant)>0) {
						$lig1_declarant=mysql_fetch_object($res1_declarant);
						$chaine=$lig1_declarant->civilite." ".casse_mot($lig1_declarant->nom, 'maj')." ".ucfirst(mb_substr($lig1_declarant->prenom,0,1));
						$tab_individu[$lig_declarant->declarant]['designation']=$chaine;
						$tab_individu[$lig_declarant->declarant]['email']=$lig1_declarant->email;
					}
					else {
						$chaine="ERREUR: Login $lig1_declarant->declarant inconnu";
					}
				}
				else {
					$chaine=$tab_individu[$lig_declarant->declarant]['designation']."";
				}
				echo ">".mb_substr($chaine,0,40)."</option>\n";
			}
			else {
				echo ">(vide)</option>\n";
			}
		}
		echo "</select>\n";
		//echo "$sql<br />\n";
		//echo $chaine_tmp;
		echo "</th>\n";
	}
	
	echo "<th>Nature\n";
	echo "<br />\n";
	echo "<select name='nature_incident' onchange=\"document.formulaire.submit();\">\n";
	//echo "<option value=''>---</option>\n";
	echo "<option value='---'>---</option>\n";
	if($_SESSION['statut']=='professeur') {
		//$sql="SELECT DISTINCT si.nature FROM s_incidents si, s_protagonistes sp WHERE (sp.login='".$_SESSION['login']."' OR si.declarant='".$_SESSION['login']."') AND si.nature!='' AND sp.id_incident=si.id_incident ORDER BY si.nature ASC;";

		//$sql="(SELECT DISTINCT si.nature FROM s_incidents si, s_protagonistes sp WHERE (sp.login='".$_SESSION['login']."' OR si.declarant='".$_SESSION['login']."') AND si.nature!='' AND sp.id_incident=si.id_incident)";
		//$sql.=" UNION (SELECT DISTINCT si.nature FROM s_incidents si, s_protagonistes sp, j_eleves_professeurs jep WHERE jep.login=sp.login AND jep.professeur='".$_SESSION['login']."' AND si.nature!='' AND sp.id_incident=si.id_incident)";

		$sql="(SELECT DISTINCT si.nature FROM s_incidents si, s_protagonistes sp WHERE (sp.login='".$_SESSION['login']."' OR si.declarant='".$_SESSION['login']."') AND sp.id_incident=si.id_incident)";

		if($prof_is_pp) {
			$sql.=" UNION (SELECT DISTINCT si.nature FROM s_incidents si, s_protagonistes sp, j_eleves_professeurs jep WHERE jep.login=sp.login AND jep.professeur='".$_SESSION['login']."' AND sp.id_incident=si.id_incident)";
		}

		if(getSettingAOui('visuDiscProfGroupes')) {
			$sql.=" UNION (SELECT DISTINCT si.nature FROM s_incidents si, s_protagonistes sp, j_groupes_professeurs jgp, j_eleves_groupes jeg WHERE jeg.login=sp.login AND jeg.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' AND sp.id_incident=si.id_incident)";
		}

		if(getSettingAOui('visuDiscProfClasses')) {
			$sql.=" UNION (SELECT DISTINCT si.nature FROM s_incidents si, s_protagonistes sp, j_groupes_professeurs jgp, j_groupes_classes jgc, j_eleves_classes jec WHERE jec.login=sp.login AND jec.id_classe=jgc.id_classe AND jgc.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' AND sp.id_incident=si.id_incident)";
		}

		//$sql.=" ORDER BY si.nature ASC;";
		$sql.=" ORDER BY nature ASC;";
	}
	else {
		//$sql="SELECT DISTINCT si.nature FROM s_incidents si WHERE si.nature!='' ORDER BY si.nature ASC;";
		$sql="SELECT DISTINCT si.nature FROM s_incidents si ORDER BY si.nature ASC;";
	}
	$res_natures=mysql_query($sql);
	while($lig_nature=mysql_fetch_object($res_natures)) {
		echo "<option value=\"$lig_nature->nature\"";
		if($nature_incident==$lig_nature->nature) {echo " selected='selected'";}
		if($lig_nature->nature!='') {
			//echo ">".$lig_nature->nature."</option>\n";
			echo ">".mb_substr($lig_nature->nature,0,40)."</option>\n";
		}
		else {
			echo ">(vide)</option>\n";
		}
	}
	echo "</select>\n";
	//echo "$sql<br />\n";
	echo "</th>\n";

	echo "<th>Protagonistes\n";
	echo "<br />\n";
	/*
	echo "<pre>";
	print_r($tab_individu);
	echo "</pre>";
	*/
	echo "<select name='protagoniste_incident' onchange=\"document.formulaire.submit();\">\n";
	echo "<option value=''>---</option>\n";
	//$sql="SELECT DISTINCT sp.login FROM s_protagonistes sp ORDER BY sp.login ASC;";
	// Avec cette modif, on n'affiche que les protagonistes élèves:
	//$sql="SELECT DISTINCT sp.login FROM s_protagonistes sp, eleves e ORDER BY e.nom, e.prenom ASC;";
	//$sql="SELECT DISTINCT sp.login FROM s_protagonistes sp, eleves e WHERE sp.login=e.login ORDER BY e.nom, e.prenom ASC;";

	$sql="(SELECT DISTINCT sp.login, e.login, e.nom, e.prenom, sp.statut FROM s_protagonistes sp, eleves e WHERE sp.login=e.login ORDER BY nom, prenom ASC)";
	if(($_SESSION['statut']!='professeur')||($_SESSION['statut']!='autre')) {
		$sql.=" UNION (SELECT DISTINCT sp.login, u.login, u.nom, u.prenom, u.statut FROM s_protagonistes sp, utilisateurs u WHERE sp.login=u.login ORDER BY u.statut, u.nom, u.prenom ASC)";
		$sql.=" ORDER BY nom, prenom ASC;";
	}
	//echo "$sql<br />";
	$res_protagonistes=mysql_query($sql);
	while($lig_protagoniste=mysql_fetch_object($res_protagonistes)) {
		$affiche_option_protagoniste="y";
		if(($_SESSION['statut']=='professeur') ||($_SESSION['statut']=='autre')){
			$affiche_option_protagoniste="n";

			if(getSettingAOui('visuDiscProfGroupes')) {
				$sql="SELECT 1=1 FROM j_groupes_professeurs jgp, j_eleves_groupes jeg WHERE jgp.login='".$_SESSION['login']."' AND jgp.id_groupe=jeg.id_groupe AND jeg.login='".$lig_protagoniste->login."';";
				//$chaine_tmp_debug.="$sql<br />";
				$res_test=mysql_query($sql);
				if(mysql_num_rows($res_test)>0) {
					$affiche_option_protagoniste="y";
				}
			}

			if($affiche_option_protagoniste=="n") {
				if(getSettingAOui('visuDiscProfClasses')) {
					$sql="SELECT 1=1 FROM j_groupes_professeurs jgp, j_groupes_classes jgc, j_eleves_classes jec WHERE jgp.login='".$_SESSION['login']."' AND jgp.id_groupe=jgc.id_groupe AND jec.id_classe=jgc.id_classe AND jec.login='".$lig_protagoniste->login."';";
					//$chaine_tmp_debug.="$sql<br />";
					$res_test=mysql_query($sql);
					if(mysql_num_rows($res_test)>0) {
						$affiche_option_protagoniste="y";
					}
				}
			}

			if($affiche_option_protagoniste=="n") {
				$sql="SELECT 1=1 FROM j_eleves_professeurs jep WHERE jep.professeur='".$_SESSION['login']."' AND jep.login='$lig_protagoniste->login';";
				//echo "$sql<br />";
				$res_test=mysql_query($sql);
				if(mysql_num_rows($res_test)>0) {
					$affiche_option_protagoniste="y";
				}
				else {
					$sql="SELECT si.id_incident FROM s_protagonistes sp, s_incidents si WHERE sp.id_incident=si.id_incident AND sp.login='$lig_protagoniste->login';";
					//echo "$sql<br />";
					$res_test=mysql_query($sql);
					if(mysql_num_rows($res_test)>0) {
						while($lig_test=mysql_fetch_object($res_test)) {
							$sql="SELECT 1=1 FROM s_protagonistes sp WHERE sp.id_incident='$lig_test->id_incident' AND sp.login='".$_SESSION['login']."';";
							//echo "$sql<br />";
							$res_test2=mysql_query($sql);
							if(mysql_num_rows($res_test2)>0) {
								$affiche_option_protagoniste="y";
								break;
							}
							else {
								$sql="SELECT 1=1 FROM s_incidents si WHERE si.id_incident='$lig_test->id_incident' AND si.declarant='".$_SESSION['login']."';";
								//echo "$sql<br />";
								$res_test2=mysql_query($sql);
								if(mysql_num_rows($res_test2)>0) {
									$affiche_option_protagoniste="y";
									break;
								}
							}
						}
					}
				}
			}
		}

		if($affiche_option_protagoniste=='y') {
			echo "<option value='$lig_protagoniste->login'";
			if($protagoniste_incident==$lig_protagoniste->login) {echo " selected='selected'";}

			if(in_array($lig_protagoniste->statut, array('professeur', 'cpe', 'scolarite', 'administrateur', 'autre', 'secours'))) {
				echo " style='background-color: white;' title='$lig_protagoniste->statut'";
			}
			echo ">";

			if(!in_array($lig_protagoniste->login, array_keys($tab_individu))) {
				$chaine="";

				$sql="SELECT 1=1 FROM eleves WHERE login='$lig_protagoniste->login';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0) {
					$chaine=p_nom($lig_protagoniste->login,"np");

					$chaine2="";
					$tmp_tab=get_class_from_ele_login($lig_protagoniste->login);
					if(isset($tmp_tab['liste'])) {
						$chaine2=" (".$tmp_tab['liste'].")";
						$chaine2b=" (<i>élève ".$tmp_tab['liste']."</i>)";
					}

					echo $chaine.$chaine2;
					$tab_individu[$lig_protagoniste->login]['designation']=$chaine.$chaine2;
					$tab_individu[$lig_protagoniste->login]['designation2']=$chaine.$chaine2b;
				}
				else {
					$tab_individu[$lig_protagoniste->login]['designation']=u_p_nom($lig_protagoniste->login);
					echo $tab_individu[$lig_protagoniste->login]['designation'];
				}
			}
			else {
				echo $tab_individu[$lig_protagoniste->login]['designation'];
			}

			echo "</option>\n";
		}
	}
	echo "</select>\n";
	//echo "$sql<br />";

	$chaine_tmp_debug="";
	//if($_SESSION['statut']!='professeur') {
		echo " ";
		echo "<select name='id_classe_incident' onchange=\"document.formulaire.submit();\">\n";
		echo "<option value=''>---</option>\n";
		$sql="SELECT DISTINCT c.id,c.classe FROM s_protagonistes sp, j_eleves_classes jec, classes c WHERE sp.login=jec.login AND jec.id_classe=c.id ORDER BY c.classe ASC;";
		$chaine_tmp_debug.="$sql<br />";
		$res_classes=mysql_query($sql);
		while($lig_classe=mysql_fetch_object($res_classes)) {
			$affiche_option_classe="y";

			if($_SESSION['statut']=='professeur') {
				$affiche_option_classe="n";

				if((getSettingAOui('visuDiscProfClasses'))||(getSettingAOui('visuDiscProfGroupes'))) {
					$sql="SELECT 1=1 FROM j_groupes_professeurs jgp, j_groupes_classes jgc WHERE jgp.login='".$_SESSION['login']."' AND jgp.id_groupe=jgc.id_groupe AND jgc.id_classe='".$lig_classe->id."';";
					$chaine_tmp_debug.="$sql<br />";
					$res_test=mysql_query($sql);
					if(mysql_num_rows($res_test)>0) {
						$affiche_option_classe="y";
					}
				}

				if($affiche_option_classe=="n") {
					$sql="SELECT 1=1 FROM j_eleves_professeurs jep, j_eleves_classes jec WHERE jep.professeur='".$_SESSION['login']."' AND jep.login=jec.login AND jec.id_classe='".$lig_classe->id."';";
					$chaine_tmp_debug.="$sql<br />";
					$res_test=mysql_query($sql);
					if(mysql_num_rows($res_test)>0) {
						$affiche_option_classe="y";
					}
					else {
						// REQUETE A REVOIR:
						$sql="SELECT si.id_incident FROM s_protagonistes sp, s_incidents si, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE sp.id_incident=si.id_incident AND jgp.id_groupe=jgc.id_groupe AND jgp.login=sp.login AND sp.login='".$_SESSION['login']."' AND jgc.id_classe='".$lig_classe->id."';";
						$chaine_tmp_debug.="$sql<br />";
						$res_test=mysql_query($sql);
						if(mysql_num_rows($res_test)>0) {
							$affiche_option_classe="y";
						}
						else {
							$sql="SELECT si.id_incident FROM s_protagonistes sp, s_incidents si, j_groupes_classes jgc, j_groupes_professeurs jgp, j_eleves_classes jec WHERE jgp.id_groupe=jgc.id_groupe AND jgp.login=si.declarant AND si.declarant='".$_SESSION['login']."' AND jgc.id_classe='".$lig_classe->id."' AND sp.id_incident=si.id_incident AND sp.login=jec.login AND jec.id_classe=jgc.id_classe;";
							$chaine_tmp_debug.="$sql<br />";
							//echo "$sql<br />";
							$res_test=mysql_query($sql);
							if(mysql_num_rows($res_test)>0) {
								$affiche_option_classe="y";
							}
						}
					}
				}
			}

			if($affiche_option_classe=='y') {
				echo "<option value='$lig_classe->id'";
				if($id_classe_incident==$lig_classe->id) {echo " selected='selected'";}
				echo ">".$lig_classe->classe;
				echo "</option>\n";
			}
		}
		echo "</select>\n";
	//echo $chaine_tmp_debug;
	//}

	echo "</th>\n";

	echo "<th>Description</th>\n";
	echo "<th>Sanctions</th>\n";
	echo "<th>Etat<br />";
	//echo "<input type='submit' name='modifier_etat_incidents' value='Valider' />\n";
	echo "clos ou non";
	echo "</th>\n";
	// Ne proposer le bouton pour supprimer qu'à certains utilisateurs?
	//echo "<th><input type='submit' name='supprimer' value='Suppr' /></th>\n";
	if(($_SESSION['statut']!='professeur')&&($_SESSION['statut']!='autre')) {
	    echo "<th>Imprimer le rapport d'incident</th>\n";
		echo "<th>Suppr</th>\n";
	}
	//echo "<th></th>\n";
	echo "</tr>\n";

	//=========================================================
	// Fin de l'entête du tableau
	//=========================================================
	// Début des lignes d'incidents
	//=========================================================
	// 20130630

	//$date_du_jour_format_mysql=strftime("%Y-%m-%d");
	$jour_courant=strftime("%d");
	$mois_courant=sprintf("%02d",strftime("%m"));
	$an_courant=strftime("%Y");
	$date_du_jour_format_mysql="$an_courant-$mois_courant-$jour_courant";

	$alt=1;
	while($lig=mysql_fetch_object($res)) {
		$affiche_ligne_incident='y';
		if(($_SESSION['statut']=='professeur')&&($id_classe_incident!="")) {
			$affiche_ligne_incident='n';

			$sql="SELECT 1=1 FROM s_protagonistes sp,j_eleves_classes jec WHERE sp.id_incident='$lig->id_incident' AND sp.login=jec.login AND jec.id_classe='$id_classe_incident' LIMIT 1;";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				$affiche_ligne_incident='y';
			}
		}

		if($affiche_ligne_incident=='y') {
			$alt=$alt*(-1);

			$liste_protagonistes="";

			if($lig->etat=='clos') {
				echo "<tr style='background-color:lightgrey;'>\n";
			}
			else {
				echo "<tr class='lig$alt'>\n";
			}

			echo "<td>$lig->id_incident</td>\n";
			//echo "<td>".formate_date($lig->date)."</td>\n";
			$date_declaration_incident=formate_date($lig->date);
			if($date_du_jour_format_mysql==$lig->date) {
				echo "<td><strong>".$date_declaration_incident."</strong></td>\n";
			}
			else {
				echo "<td>".$date_declaration_incident."</td>\n";
			}
			echo "<td>$lig->heure</td>\n";
			
			//=================================================
			// Colonne declarant
			if (!(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='autre'))) {

				echo "<td>";
				$identite_trouvee="n";
				if(in_array($lig->declarant, array_keys($tab_individu))) {
					echo $tab_individu[$lig->declarant]['designation'];
					$identite_trouvee="y";
				}
				else {
					$sql_declarant="SELECT nom,prenom,civilite,statut,email FROM utilisateurs WHERE login='$lig->declarant';";
					//echo "$sql<br />\n";
					$res_declarant=mysql_query($sql_declarant);
					if(mysql_num_rows($res_declarant)>0) {
						$lig_declarant=mysql_fetch_object($res_declarant);
						$chaine=$lig_declarant->civilite." ".mb_strtoupper($lig_declarant->nom)." ".ucfirst(mb_substr($lig_declarant->prenom,0,1)).".";
						$tab_individu[$lig->declarant]['designation']=$chaine;
						$tab_individu[$lig->declarant]['email']=$lig_declarant->email;
						echo $chaine;
						// 20130630: Prendre en compte la possibilité de poster un message/alerte.
						$identite_trouvee="y";
					}
					else {
						echo "ERREUR: Login $lig->declarant inconnu";
					}
				}
				if(($identite_trouvee=='y')&&((peut_poster_message($_SESSION['statut'])))) {
					echo "<a href='$gepiPath/mod_alerte/form_message.php?login_dest=".$lig->declarant."&amp;sujet=Incident du $date_declaration_incident&amp;id_incident=$lig->id_incident".add_token_in_url()."' title='Déposer un message dans le module Alertes de Gepi' target='_blank'><img src='../images/icons/mail.png' width='16' height='16' alt='Alerter' /></a>";
				}
				echo "</td>\n";
			}

			//=================================================
			// Colonne nature
			echo "<td>$lig->nature</td>\n";

			//=================================================
			// Colonne Protagonistes
			echo "<td>\n";
			$sql="SELECT * FROM s_protagonistes WHERE id_incident='$lig->id_incident' ORDER BY statut,qualite,login;";
			$res2=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				echo "Aucun";
			}
			else {
				$cpt=0;
				$tab_protagonistes=array();
				while($lig2=mysql_fetch_object($res2)) {
					$tab_protagonistes[]=$lig2->login;
					if($cpt>0) {echo "<br />";}
					if($lig2->statut=='eleve') {
						if(in_array($lig2->login, array_keys($tab_individu))) {
							if(isset($tab_individu[$lig2->login]['designation2'])) {
								echo $tab_individu[$lig2->login]['designation2'];
							}
							else {
								echo $tab_individu[$lig2->login]['designation'];
							}

							if($liste_protagonistes!="") {$liste_protagonistes.=", ";}
							$liste_protagonistes.=$tab_individu[$lig2->login]['designation'];
						}
						else {
							$sql="SELECT nom,prenom,email FROM eleves WHERE login='$lig2->login';";
							//echo "$sql<br />\n";
							$res3=mysql_query($sql);
							if(mysql_num_rows($res3)>0) {
								$lig3=mysql_fetch_object($res3);
								$chaine=casse_mot($lig3->nom,'maj')." ".casse_mot($lig3->prenom, 'majf2');
								$tab_individu[$lig2->login]['designation']=$chaine;
								$tab_individu[$lig2->login]['email']=$lig3->email;
								echo $chaine;

								if($liste_protagonistes!="") {$liste_protagonistes.=", ";}
								$liste_protagonistes.=$chaine;
							}
							else {
								echo "ERREUR: Login $lig2->login inconnu";
							}

							echo " (<i>élève ";
							$tmp_tab=get_class_from_ele_login($lig2->login);
							if(isset($tmp_tab['liste'])) {
								echo $tmp_tab['liste'];
								$liste_protagonistes.=" (".$tmp_tab['liste'].")";
								$tab_individu[$lig2->login]['designation2']=$tab_individu[$lig2->login]['designation']." (<i>".$tmp_tab['liste']."</i>)";
							}
							echo "</i>)";
						}
					}
					else {
						if(in_array($lig2->login, array_keys($tab_individu))) {
							if($liste_protagonistes!="") {$liste_protagonistes.=", ";}
							$liste_protagonistes.=$tab_individu[$lig2->login]['designation'];
							echo $tab_individu[$lig2->login]['designation'];
						}
						else {
							$sql="SELECT nom,prenom,civilite,statut FROM utilisateurs WHERE login='$lig2->login';";
							//echo "$sql<br />\n";
							$res3=mysql_query($sql);
							if(mysql_num_rows($res3)>0) {
								$lig3=mysql_fetch_object($res3);
								$chaine=$lig3->civilite." ".mb_strtoupper($lig3->nom)." ".ucfirst(mb_substr($lig3->prenom,0,1)).".";
								$tab_individu[$lig2->login]['designation']=$chaine;

								if($liste_protagonistes!="") {$liste_protagonistes.=", ";}
								$liste_protagonistes.=$lig3->civilite." ".mb_strtoupper($lig3->nom)." ".ucfirst(mb_substr($lig3->prenom,0,1)).".";
							}
							else {
								echo "ERREUR: Login $lig2->login inconnu";
							}

							if($lig3->statut=='autre') {
								//echo " (<i>".$_SESSION['statut_special']."</i>)\n";

								$sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
																WHERE du.login_user = '".$lig2->login."'
																AND du.id_statut = ds.id;";
								$query = mysql_query($sql);
								$result = mysql_fetch_array($query);

								echo " (<i>".$result['nom_statut']."</i>)\n";
							}
							else {
								echo " (<i>$lig3->statut</i>)\n";
							}
						}
					}

					if($lig2->qualite!='') {
						echo " <span style='color:green;'>$lig2->qualite</span>\n";
					}
					$cpt++;
				}
			}
			echo "</td>\n";

			//=================================================
			// Colonne détails incident
			echo "<td>\n";
			$texte="";
			if($lig->nature!="") {
				$texte="<b>$lig->nature</b><br />";
			}

			if($lig->description=="") {
				$texte.="Aucun détail n'a été saisi.";

				if($lig->nature=='') {
					if((!in_array($lig->declarant, array_keys($tab_individu)))||(!isset($tab_individu[$lig->declarant]['email']))) {
						$sql="SELECT email,civilite,nom,prenom FROM utilisateurs WHERE login='$lig->declarant' AND email!='';";
						$res_mail=mysql_query($sql);
						if(mysql_num_rows($res_mail)>0) {
							$lig_mail=mysql_fetch_object($res_mail);

							$tab_individu[$lig->declarant]['designation']=$lig_mail->civilite." ".casse_mot($lig_mail->nom, 'maj')." ".casse_mot($lig_mail->prenom, 'majf2');
							$tab_individu[$lig->declarant]['email']=$lig_mail->email;
						}
					}

					if((isset($tab_individu[$lig->declarant]['email']))&&(check_mail($tab_individu[$lig->declarant]['email']))) {
						$texte="<a href=\"mailto:".$tab_individu[$lig->declarant]['email']."?subject="."Incident sans détails";
						if($email_visiteur!='') {
							$texte.="&amp;bcc=".$email_visiteur;
						}
						$texte.="&amp;body=Bonjour%20".preg_replace("/ /","%20",$tab_individu[$lig->declarant]['designation']).",%0A%0a"."Vous avez déclaré un incident (numéro $lig->id_incident) sans en préciser la nature, les détails."."%0A%0a"."L'incident a eu lieu le ".formate_date($lig->date)." en $lig->heure avec pour protagonistes: $liste_protagonistes"."%0A%0a"."Pourriez-vous préciser?"."%0A%0a"."Merci."."\">";

						$texte.="Aucun détail n'a été saisi.";
						$texte.="</a>";
					}
					else {
						$texte="Aucun détail n'a été saisi.";
					}
				}
			}
			else {
				$texte.=nl2br($lig->description);
			}
			$lieu_incident=get_lieu_from_id($lig->id_lieu);
			if($lieu_incident=="") {
				//$texte.="<br /><span style='font-size:x-small;'>Lieu&nbsp;: ".$lieu_incident."</span>";
				$lieu_incident="non précisé";
			}
			$texte.="<br /><span style='font-size:x-small;'>Lieu&nbsp;: ".$lieu_incident."</span>";

			if($lig->heure!="") {
				$texte.="<span style='font-size:x-small;'> à l'heure $lig->heure</span>";
			}

			$texte.="<br /><span style='font-size:x-small;'>Incident signalé par ".u_p_nom($lig->declarant)."</span>";

			if(($lig->declarant==$_SESSION['login'])||($_SESSION['statut']!='professeur')) {$possibilite_prof_clore_incident='y';} else {$possibilite_prof_clore_incident='n';}

			$mesure_demandee_non_validee="n";
			$texte.=affiche_mesures_incident($lig->id_incident);

			$tabdiv_infobulle[]=creer_div_infobulle("incident_".$lig->id_incident,"Incident n°$lig->id_incident","",$texte,"",30,0,'y','y','n','n');

			if(($lig->etat=='clos')||(($_SESSION['statut']=='professeur')&&($lig->declarant!=$_SESSION['login']))||(($_SESSION['statut']=='autre')&&($lig->declarant!=$_SESSION['login']))) {
				echo "<a href='#'";
				echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('incident_".$lig->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo " onclick='return false;'";
				echo ">Détails</a>";
			}
			else {
				echo "<a href='";
				echo "saisie_incident.php?id_incident=$lig->id_incident&amp;step=2";
				echo "' onmouseover=\"cacher_toutes_les_infobulles(); delais_afficher_div('incident_".$lig->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo ">Modifier</a>";
				//echo "><img src='../images/edit16b.png' class='icone16 title='Modifier' /></a>";
			}

			if($mesure_demandee_non_validee=="y") {
				echo " <img src='../images/icons/ico_attention.png' width='22' height='19' alt='Mesure(s) demandée(s)' title='Mesure(s) demandée(s)' />";
			}

			echo "</td>\n";
			//=================================================
			// Colonne Sanction
			echo "<td>\n";

			$texte="";
			for($loop=0;$loop<count($tab_protagonistes);$loop++) {
				$tmp_texte=liste_sanctions($lig->id_incident,$tab_protagonistes[$loop]);
				if($tmp_texte!="") {
					// On aura peut-être des blagues à régler là avec p_nom() quand on aura des protagonistes non élèves
					$texte.="<p class='bold'>".p_nom($tab_protagonistes[$loop])."</p>\n";
					$texte.=$tmp_texte;
				}
			}

			if($texte!="") {
				$tabdiv_infobulle[]=creer_div_infobulle("sanctions_incident_".$lig->id_incident,"Sanctions incident n°$lig->id_incident","",$texte,"",40,0,'y','y','n','n');

				$txt_lien="Modifier";
			}
			else {
				$tabdiv_infobulle[]=creer_div_infobulle("sanctions_incident_".$lig->id_incident,"Sanctions incident n°$lig->id_incident","","Aucune sanction n'est encore saisie","",20,0,'y','y','n','n');

				$txt_lien="<strong>Saisir</strong>";
			}

			if(($lig->etat=='clos')||($_SESSION['statut']=='professeur')||($_SESSION['statut']=='autre')) {
				echo "<a href='#'";
				echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('sanctions_incident_".$lig->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo " onclick='return false;'";
				echo ">Sanctions</a>";
			}
			else {
				echo "<a href='saisie_sanction.php?id_incident=$lig->id_incident'";
				echo " onmouseover=\"cacher_toutes_les_infobulles();delais_afficher_div('sanctions_incident_".$lig->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo ">$txt_lien</a>";
			}
			echo "</td>\n";

			//=================================================
			// Colonne cloture d'incident
			echo "<td>\n";
			if((($_SESSION['statut']!='professeur')&&($_SESSION['statut']!='autre'))||
				(($_SESSION['statut']=='professeur')&&($possibilite_prof_clore_incident=='y'))||
				(($_SESSION['statut']=='autre')&&($possibilite_prof_clore_incident=='y')&&($lig->declarant==$_SESSION['login']))
			) {
				echo "<input type='checkbox' name='etat_incident[$lig->id_incident]' value='clos' ";
				if($lig->etat=='clos') {echo "checked='checked' ";}
				echo "onchange='changement()' />";
				echo "<input type='hidden' name='form_id_incident[]' value='$lig->id_incident' />\n";
			}
			else {
				if($lig->etat=='clos') {echo "Clos";} else {echo "Non";}
			}
			echo "</td>\n";

			//Ajout Eric ===============================
			// Colonne imprimer
			if(($_SESSION['statut']!='professeur')&&($_SESSION['statut']!='autre')) {
				echo "<td>\n";
				echo "<a href='../mod_ooo/rapport_incident.php?mode=module_discipline&id_incident=$lig->id_incident".add_token_in_url()."' title='Imprimer le rapport d'\incident'><img src='../images/icons/print.png' width='16' height='16' alt='Imprimer le Rapport d\'incident'></a>\n";
				echo "</td>\n";
			}
			//===============================
			
			//=================================================
			// Colonne suppression
			if(($_SESSION['statut']!='professeur')&&($_SESSION['statut']!='autre')) {
				echo "<td>\n";
				if($lig->etat!='clos') {
					echo "<input type='checkbox' name='suppr_incident[]' value='$lig->id_incident' onchange='changement()' />\n";
				}
				else {
					echo "&nbsp;";
				}
				echo "</td>\n";
			}
			echo "</tr>\n";
			flush();
		}
	}

	echo "</table>\n";
	echo "<p align='center'><input type='submit' name='valider2' value='Valider' /></p>\n";

	echo "</form>\n";

	echo "</blockquote>\n";

}
else {
	$sql="SELECT * FROM s_protagonistes WHERE id_incident='$id_incident' ORDER BY statut,qualite,login;";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		echo "<p>Incident n°$id_incident</p>\n";

		echo "<p>Normalement, on n'arrive pas ici...</p>\n";
	}
}

echo "<p><br /></p>\n";

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

echo "<p><em>NOTES&nbsp;:</em></p>
<ul>
	<li><p>Lorsqu'un incident est clos, on ne peut plus modifier l'incident, ni saisir/modifier de sanction.<br />
On peut en revanche le déclore</p></li>
	<li><p>Ne jamais clore les incidents peut provoquer des lenteurs de chargement de la présente page lorsque le nombre d'incidents augmente (<em>avec le temps, l'accumulation est inéluctable</em>).</p></li>
</ul>
<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
