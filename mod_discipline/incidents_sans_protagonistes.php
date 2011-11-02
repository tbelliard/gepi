<?php

/*
 * $Id: incidents_sans_protagonistes.php 6606 2011-03-03 14:09:03Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/incidents_sans_protagonistes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Incidents sans protagonistes', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/incidents_sans_protagonistes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Incidents sans protagonistes', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

if(strtolower(substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');


// Pour choisir de n'afficher que les incidents de la date indiquée:
$date_incident=isset($_POST['date_incident']) ? $_POST['date_incident'] : (isset($_GET['date_incident']) ? $_GET['date_incident'] : "");
$heure_incident=isset($_POST['heure_incident']) ? $_POST['heure_incident'] : (isset($_GET['heure_incident']) ? $_GET['heure_incident'] : "");
$nature_incident=isset($_POST['nature_incident']) ? $_POST['nature_incident'] : (isset($_GET['nature_incident']) ? $_GET['nature_incident'] : "");
//$protagoniste_incident=isset($_POST['protagoniste_incident']) ? $_POST['protagoniste_incident'] : (isset($_GET['protagoniste_incident']) ? $_GET['protagoniste_incident'] : "");

$incidents_clos=isset($_POST['incidents_clos']) ? $_POST['incidents_clos'] : (isset($_GET['incidents_clos']) ? $_GET['incidents_clos'] : "n");

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
if((isset($_POST['suppr_incident']))&&($_SESSION['statut']!='professeur')) {
	check_token();

	$suppr_incident=$_POST['suppr_incident'];
	for($i=0;$i<count($suppr_incident);$i++) {
		$temoin_erreur="n";

		$sql="DELETE FROM s_protagonistes WHERE id_incident='$suppr_incident[$i]';";
		//echo "$sql<br />\n";
		$res=mysql_query($sql);
		if(!$res) {
			$msg.="ERREUR lors de la suppression des protagonistes de l'incident ".$suppr_incident[$i].".<br />\n";
			$temoin_erreur="y";
		}

		if($temoin_erreur=="n") {
			$sql="SELECT id_sanction FROM s_sanctions s WHERE s.id_incident='$suppr_incident[$i]';";
			$res_sanction=mysql_query($sql);
			if(mysql_num_rows($res_sanction)>0) {
				while($lig=mysql_fetch_object($res_sanction)) {
					$sql="DELETE FROM s_retenues WHERE id_sanction='$lig->id_sanction';";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de la suppression de retenues attachées à l'incident ".$suppr_incident[$i].".<br />\n";
						$temoin_erreur="y";
					}

					$sql="DELETE FROM s_exclusions WHERE id_sanction='$lig->id_sanction';";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de la suppression d'excluions attachées à l'incident ".$suppr_incident[$i].".<br />\n";
						$temoin_erreur="y";
					}

					$sql="DELETE FROM s_travail WHERE id_sanction='$lig->id_sanction';";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de la suppression de travaux attachés à l'incident ".$suppr_incident[$i].".<br />\n";
						$temoin_erreur="y";
					}
				}

				if($temoin_erreur=="n") {
					$sql="DELETE FROM s_sanctions s WHERE s.id_incident='$suppr_incident[$i]';";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de la suppression de la sanction associée à l'incident ".$suppr_incident[$i].".<br />\n";
						$temoin_erreur="y";
					}
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


//**************** EN-TETE *****************
$titre_page = "Discipline: Incidents sans protagonistes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//echo "\$gepiPath=$gepiPath<br />";
//debug_var();

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'index</a>\n";

if(($_SESSION['statut']=='administrateur')||
($_SESSION['statut']=='cpe')||
($_SESSION['statut']=='scolarite')) {
	echo " | <a href='traiter_incident.php'>Incidents avec protagonistes</a>\n";
}
elseif ($_SESSION['statut']=='professeur') {
	// Rechercher les incidents signalés par le prof ou ayant le prof pour protagoniste
	$sql="SELECT 1=1 FROM s_incidents WHERE declarant='".$_SESSION['login']."';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo " | <a href='traiter_incident.php'>Incidents avec protagonistes</a>\n";
	}
	else {
		$sql="SELECT 1=1 FROM s_protagonistes WHERE login='".$_SESSION['login']."';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			echo " | <a href='traiter_incident.php'>Incidents avec protagonistes</a>\n";
		}
		else {
			$sql="SELECT 1=1 FROM j_eleves_professeurs jep, s_protagonistes sp WHERE sp.login=jep.login AND jep.professeur='".$_SESSION['login']."';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				echo " | <a href='traiter_incident.php'>Incidents avec protagonistes</a>\n";
			}
		}
	}
}

//echo "</p>\n";

if(!isset($id_incident)) {
	$chaine_criteres="";
	$sql="SELECT DISTINCT si.* FROM s_incidents si WHERE 1";
	if($date_incident!="") {$sql.=" AND si.date='$date_incident'";$chaine_criteres.="&amp;date_incident=$date_incident";}
	if($heure_incident!="") {$sql.=" AND si.heure='$heure_incident'";$chaine_criteres.="&amp;heure_incident=$heure_incident";}
	if($nature_incident!="") {$sql.=" AND si.nature='$nature_incident'";$chaine_criteres.="&amp;nature_incident=$nature_incident";}
	//if($protagoniste_incident!="") {$sql.=" AND sp.login='$protagoniste_incident'";$chaine_criteres.="&amp;protagoniste_incident=$protagoniste_incident";}

	$sql2=$sql;
	if($incidents_clos!="y") {$sql.=" AND si.etat!='clos'";}

	/*
	$sql.=" LEFT JOIN s_protagonistes sp ON sp.id_incident=si.id_incident
	where sp.id_incident is NULL;";
	$sql2.=" LEFT JOIN s_protagonistes sp ON sp.id_incident=si.id_incident
	where sp.id_incident is NULL;";

	// A RETESTER AVEC DES PARENTHESES SUR (SELECT ...NULL) puis ORDER BY hors des parenthèses...
	*/

	$sql.=" ORDER BY si.date DESC, si.heure DESC;";
	$sql2.=" ORDER BY si.date DESC, si.heure DESC;";

	$res=mysql_query($sql);
	//echo "$sql<br />";
	if(mysql_num_rows($res)==0) {
		// Cette partie ne sert quasiment jamais parce qu'on teste tous les incidents, pas seulement ceux sans protagonistes.
		echo " | <a href='".$_SERVER['PHP_SELF']."' onclick='history.go(-1);return false;'> Retour à la page précédente</a>\n";
		echo "</p>\n";

		if($incidents_clos=="y") {
			echo "<p>Aucun incident n'est encore déclaré";
			if(($date_incident!="")||
			($heure_incident!="")||
			($nature_incident!="")||
			($protagoniste_incident!="")) {echo " avec les critères choisis";}
			echo ".</p>\n";
		}
		else {
			$res=mysql_query($sql2);
			if(mysql_num_rows($res)==0) {
				echo "<p>Aucun incident n'est encore déclaré";
				if(($date_incident!="")||
				($heure_incident!="")||
				($nature_incident!="")||
				($protagoniste_incident!="")) {echo " avec les critères choisis";}
				echo ".</p>\n";
			}
			else {
				echo "<p>Aucun incident (<i>non clos</i>) n'est déclaré";
				if(($date_incident!="")||
				($heure_incident!="")||
				($nature_incident!="")||
				($protagoniste_incident!="")) {echo " avec les critères choisis";}
				echo ".</p>\n";

				echo "<p><a href='".$_SERVER['PHP_SELF']."?incidents_clos=y$chaine_criteres'>Afficher les incidents clos avec les mêmes critères</a>.</p>\n";
			}
		}
		echo "<p><br /></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	echo "</p>\n";

	echo "<p class='bold'>Choisir l'incident à traiter/consulter&nbsp;:</p>\n";
	echo "<blockquote>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

	echo add_token_field();

	echo "<p align='left'><input type='checkbox' name='incidents_clos' id='incidents_clos' value='y'";
	if($incidents_clos=="y") {echo " checked='checked'";}
	echo " /><label for='incidents_clos' style='cursor:pointer;'> Afficher les incidents clos</label></p>\n";

	echo "<p align='center'><input type='submit' name='valider' value='Valider' /></p>\n";

	echo "<table class='boireaus' border='1' summary='Incidents'>\n";
	echo "<tr>\n";
	echo "<th>Id</th>\n";
	echo "<th>Date\n";
	echo "<br />\n";
	echo "<select name='date_incident' onchange=\"document.formulaire.submit();\">\n";
	echo "<option value=''>---</option>\n";
	//$sql="SELECT DISTINCT si.date FROM s_incidents si ORDER BY si.date DESC;";
	$sql="(SELECT DISTINCT si.date FROM s_incidents si
	LEFT JOIN s_protagonistes sp ON sp.id_incident=si.id_incident
	WHERE sp.id_incident IS NULL) ORDER BY si.date DESC;";
	$res_dates=mysql_query($sql);
	while($lig_date=mysql_fetch_object($res_dates)) {
		echo "<option value='$lig_date->date'";
		if($date_incident==$lig_date->date) {echo " selected='selected'";}
		echo ">".formate_date($lig_date->date)."</option>\n";
	}
	echo "</select>\n";
	/*
	echo "<noscript>\n";
	echo "<input type='submit' name='valider_choix_date' value='V' />\n";
	echo "</noscript>\n";
	*/
	echo "</th>\n";

	echo "<th>Heure\n";
	echo "<br />\n";
	echo "<select name='heure_incident' onchange=\"document.formulaire.submit();\">\n";
	echo "<option value=''>---</option>\n";
	//$sql="SELECT DISTINCT si.heure FROM s_incidents si ORDER BY si.heure ASC;";
	$sql="(SELECT DISTINCT si.heure FROM s_incidents si
	LEFT JOIN s_protagonistes sp ON sp.id_incident=si.id_incident
	WHERE sp.id_incident IS NULL) ORDER BY si.heure ASC;";
	$res_heures=mysql_query($sql);
	while($lig_heure=mysql_fetch_object($res_heures)) {
		echo "<option value='$lig_heure->heure'";
		if($heure_incident==$lig_heure->heure) {echo " selected='selected'";}
		echo ">".$lig_heure->heure."</option>\n";
	}
	echo "</select>\n";
	echo "</th>\n";

	echo "<th>Nature\n";
	echo "<br />\n";
	echo "<select name='nature_incident' onchange=\"document.formulaire.submit();\">\n";
	echo "<option value=''>---</option>\n";
	//$sql="SELECT DISTINCT si.nature FROM s_incidents si WHERE si.nature!='' ORDER BY si.nature ASC;";
	$sql="(SELECT DISTINCT si.nature FROM s_incidents si
	LEFT JOIN s_protagonistes sp ON sp.id_incident=si.id_incident
	WHERE sp.id_incident IS NULL) ORDER BY si.nature ASC;";
	$res_natures=mysql_query($sql);
	while($lig_nature=mysql_fetch_object($res_natures)) {
		echo "<option value='$lig_nature->nature'";
		if($nature_incident==$lig_nature->nature) {echo " selected='selected'";}
		//echo ">".$lig_nature->nature."</option>\n";
		echo ">".substr($lig_nature->nature,0,40)."</option>\n";
	}
	echo "</select>\n";
	echo "</th>\n";

	echo "<th>Description</th>\n";

	echo "<th>Etat<br />";
	//echo "<input type='submit' name='modifier_etat_incidents' value='Valider' />\n";
	echo "clos ou non";
	echo "</th>\n";
	// Ne proposer le bouton pour supprimer qu'à certains utilisateurs?
	//echo "<th><input type='submit' name='supprimer' value='Suppr' /></th>\n";

	if(($_SESSION['statut']!='professeur')) {
		echo "<th>Suppr</th>\n";
	}

	//echo "<th></th>\n";
	echo "</tr>\n";

	$alt=1;
	while($lig=mysql_fetch_object($res)) {
		$sql="SELECT 1=1 FROM s_protagonistes WHERE id_incident='$lig->id_incident';";
		$test=mysql_query($sql);
		// On n'affiche que les incidents sans protagonistes
		if(mysql_num_rows($test)==0) {
			$alt=$alt*(-1);

			if($lig->etat=='clos') {
				echo "<tr style='background-color:lightgrey;'>\n";
			}
			else {
				echo "<tr class='lig$alt'>\n";
			}

			echo "<td>$lig->id_incident</td>\n";
			echo "<td>".formate_date($lig->date)."</td>\n";
			echo "<td>$lig->heure</td>\n";
			echo "<td>$lig->nature</td>\n";

			echo "<td>\n";
			if($lig->description=="") {
				$texte="Aucun détail n'a été saisi.";
			}
			else {
				$texte=nl2br($lig->description);
			}
			$lieu_incident=get_lieu_from_id($lig->id_lieu);
			if($lieu_incident!="") {$texte.="<br /><span style='font-size:x-small;'>Lieu&nbsp;:".$lieu_incident."</span>";}
			$texte.="<br /><span style='font-size:x-small;'>Incident signalé par ".u_p_nom($lig->declarant)."</span>";

			$tabdiv_infobulle[]=creer_div_infobulle("incident_".$lig->id_incident,"Incident n°$lig->id_incident","",$texte,"",30,0,'y','y','n','n');

			//if($lig->etat=='clos') {
			if(($lig->etat=='clos')||(($_SESSION['statut']=='professeur')&&($lig->declarant!=$_SESSION['login']))) {
				echo "<a href='#'";
				//echo " onmouseover=\"delais_afficher_div('incident_".$lig->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo " onmouseover=\"cacher_toutes_les_infobulles();afficher_div('incident_".$lig->id_incident."','y',20,20);\"";
				echo " onclick='return false;'";
				echo ">Détails</a>";
			}
			else {
				//echo "<a href='saisie_incident.php?id_incident=$lig->id_incident&amp;step=2' onmouseover=\"delais_afficher_div('incident_".$lig->id_incident."','y',20,20,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\"";
				echo "<a href='saisie_incident.php?id_incident=$lig->id_incident&amp;step=2' onmouseover=\"cacher_toutes_les_infobulles();afficher_div('incident_".$lig->id_incident."','y',20,20);\"";
				//echo ">Détails</a>";
				//echo " (*)";
				echo ">Modifier</a>";
			}

			echo "</td>\n";

			/*
			echo "<td>\n";
			if(($_SESSION['statut']!='professeur')) {
				echo "<input type='checkbox' name='etat_incident[$lig->id_incident]' value='clos' ";
				if($lig->etat=='clos') {echo "checked='checked' ";}
				echo "/>";
				echo "<input type='hidden' name='form_id_incident[]' value='$lig->id_incident' />\n";
			}
			else {
				if($lig->etat=='clos') {echo "Clos";} else {echo "Non";}
			}
			echo "</td>\n";
			*/
			echo "<td>\n";
			if(($_SESSION['statut']!='professeur')||
				(($_SESSION['statut']=='professeur')&&($lig->declarant==$_SESSION['login']))
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


			if(($_SESSION['statut']!='professeur')) {
				echo "<td>\n";
				if($lig->etat!='clos') {
					echo "<input type='checkbox' name='suppr_incident[]' value='$lig->id_incident' />\n";
				}
				else {
					echo "&nbsp;";
				}
				echo "</td>\n";
			}
			echo "</tr>\n";
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

echo "<p><em>NOTE&nbsp;:</em></p>\n";
echo "<blockquote>\n";
echo "<p>Lorsqu'un incident est clos, on ne peut plus modifier l'incident, ni saisir/modifier de sanction.</p>\n";
echo "</blockquote>\n";
echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>