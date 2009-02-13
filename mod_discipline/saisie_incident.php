<?php

/*
 * $Id$
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$variables_non_protegees = 'yes';

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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/saisie_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Saisie incident', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/saisie_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Saisie incident', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

require('sanctions_func_lib.php');

function choix_heure($champ_heure,$div_choix_heure) {
	global $tabdiv_infobulle;

	$sql="SELECT * FROM absences_creneaux ORDER BY heuredebut_definie_periode;";
	$res_abs_cren=mysql_query($sql);
	if(mysql_num_rows($res_abs_cren)>0) {
		echo " <a href='#' onclick=\"afficher_div('$div_choix_heure','y',10,-40); return false;\">Choix</a>";

		$texte="<table class='boireaus' style='margin: auto;' border='1' summary=\"Choix d'une heure\">\n";
		while($lig_ac=mysql_fetch_object($res_abs_cren)) {
			$td_style="";
			if($lig_ac->type_creneaux=='cours') {
				$td_style=" style='background-color: lightgreen;'";
			}
			elseif($lig_ac->type_creneaux=='pause') {
				$td_style=" style='background-color: lightgrey;'";
			}
			elseif($lig_ac->type_creneaux=='repas') {
				$td_style=" style='background-color: lightgrey;'";
			}

			$texte.="<tr$td_style>\n";
			$texte.="<td><a href='#' onclick=\"document.getElementById('$champ_heure').value='$lig_ac->nom_definie_periode';cacher_div('$div_choix_heure');return false;\">".$lig_ac->nom_definie_periode."</a></td>\n";
			$texte.="<td>".$lig_ac->heuredebut_definie_periode."</td>\n";
			$texte.="<td>".$lig_ac->heurefin_definie_periode."</td>\n";
			$texte.="</tr>\n";
		}
		$texte.="</table>\n";

		$tabdiv_infobulle[]=creer_div_infobulle("$div_choix_heure","Choix d'une heure","",$texte,"",12,0,'y','y','n','n');
	}
}

function recherche_ele($rech_nom,$page) {
	$rech_nom=ereg_replace("[^A-Za-zÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõ¨ûüùúýÿ¸]","",$rech_nom);

	$sql="SELECT * FROM eleves WHERE nom LIKE '%$rech_nom%';";
	$res_ele=mysql_query($sql);

	$nb_ele=mysql_num_rows($res_ele);

	if($nb_ele==0){
		// On ne devrait pas arriver là.
		//echo "<p>Aucun nom d'élève ne contient la chaine $rech_nom.</p>\n";
		echo "<p>Aucun nom d'&eacute;l&egrave;ve ne contient la chaine $rech_nom.</p>\n";
	}
	else{
		//echo "<p>La recherche a retourné <b>$nb_ele</b> réponse(s):</p>\n";
		echo "<p>La recherche a retourn&eacute; <b>$nb_ele</b> r&eacute;ponse";
		if($nb_ele>1) {echo "s";}
		echo ":</p>\n";
		echo "<table border='1' class='boireaus' summary='Liste des élèves'>\n";
		echo "<tr>\n";
		//echo "<th>Elève</th>\n";
		echo "<th>Sélectionner</th>\n";
		echo "<th>El&egrave;ve</th>\n";
		echo "<th>Classe(s)</th>\n";
		echo "</tr>\n";
		$alt=1;
		$cpt1=0;
		while($lig_ele=mysql_fetch_object($res_ele)) {
			$ele_login=$lig_ele->login;
			$ele_nom=$lig_ele->nom;
			$ele_prenom=$lig_ele->prenom;
			//echo "<b>$ele_nom $ele_prenom</b>";
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			echo "<input type='checkbox' name='ele_login[]' id='ele_login_$cpt1' value=\"$ele_login\" />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='ele_login_$cpt1' style='cursor:pointer;'>".htmlentities("$ele_nom $ele_prenom")."</label>";

			$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_classes jec WHERE jec.login='$ele_login' AND c.id=jec.id_classe ORDER BY jec.periode;";
			$res_clas=mysql_query($sql);
			if(mysql_num_rows($res_clas)==0) {
				echo "<td>\n";
				echo "aucune classe";
				echo "</td>\n";
			}
			else {
				echo "<td>\n";
				$cpt=0;
				while($lig_clas=mysql_fetch_object($res_clas)) {
					if($cpt>0) {echo ", ";}
					//echo $lig_clas->classe;
					echo htmlentities($lig_clas->classe);
					$cpt++;
				}
				echo "</td>\n";
			}
			echo "</tr>\n";
			$cpt1++;
		}
		echo "</table>\n";
	}
}

function recherche_utilisateur($rech_nom,$page) {
	$rech_nom=ereg_replace("[^A-Za-zÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõ¨ûüùúýÿ¸]","",$rech_nom);

	$sql="SELECT * FROM utilisateurs WHERE (nom LIKE '%$rech_nom%' AND statut!='responsable');";
	$res_utilisateur=mysql_query($sql);

	$nb_utilisateur=mysql_num_rows($res_utilisateur);

	if($nb_utilisateur==0){
		// On ne devrait pas arriver là.
		echo "<p>Aucun nom d'utilisateur ne contient la chaine $rech_nom.</p>\n";
	}
	else{
		echo "<p>La recherche a retourn&eacute; <b>$nb_utilisateur</b> r&eacute;ponse";
		if($nb_utilisateur>1) {echo "s";}
		echo ":</p>\n";
		echo "<table border='1' class='boireaus' summary='Liste des utilisateurs'>\n";
		echo "<tr>\n";
		echo "<th>Sélectionner</th>\n";
		echo "<th>Utilisateur</th>\n";
		echo "</tr>\n";
		$alt=1;
		$cpt1=0;
		while($lig_utilisateur=mysql_fetch_object($res_utilisateur)) {
			$utilisateur_login=$lig_utilisateur->login;
			$utilisateur_nom=$lig_utilisateur->nom;
			$utilisateur_prenom=$lig_utilisateur->prenom;
			//echo "<b>$utilisateur_nom $utilisateur_prenom</b>";
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			echo "<input type='checkbox' name='u_login[]' id='u_login_$cpt1' value=\"$utilisateur_login\" />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='u_login_$cpt1' style='cursor:pointer;'>".htmlentities("$utilisateur_nom $utilisateur_prenom")."</label>";
            echo "</td>\n";

            echo "</tr>\n";
			$cpt1++;
		}
		echo "</table>\n";
	}
}


$id_incident=isset($_POST['id_incident']) ? $_POST['id_incident'] : (isset($_GET['id_incident']) ? $_GET['id_incident'] : NULL);

$rech_nom=isset($_POST['rech_nom']) ? $_POST['rech_nom'] : (isset($_GET['rech_nom']) ? $_GET['rech_nom'] : "");

$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : array());
//$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL);
//$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);
$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : 0);

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$is_posted=isset($_POST['is_posted']) ? $_POST['is_posted'] : (isset($_GET['is_posted']) ? $_GET['is_posted'] : NULL);

$display_date=isset($_POST['display_date']) ? $_POST['display_date'] : (isset($_GET['display_date']) ? $_GET['display_date'] : NULL);
$display_heure=isset($_POST['display_heure']) ? $_POST['display_heure'] : (isset($_GET['display_heure']) ? $_GET['display_heure'] : NULL);
$nature=isset($_POST['nature']) ? $_POST['nature'] : (isset($_GET['nature']) ? $_GET['nature'] : NULL);

$qualite=isset($_POST['qualite']) ? $_POST['qualite'] : NULL;


$categ_u=isset($_POST['categ_u']) ? $_POST['categ_u'] : (isset($_GET['categ_u']) ? $_GET['categ_u'] : NULL);
$u_login=isset($_POST['u_login']) ? $_POST['u_login'] : (isset($_GET['u_login']) ? $_GET['u_login'] : array());

$id_lieu=isset($_POST['id_lieu']) ? $_POST['id_lieu'] : NULL;

$avertie=isset($_POST['avertie']) ? $_POST['avertie'] : NULL;

//$mesure_prise=isset($_POST['mesure_prise']) ? $_POST['mesure_prise'] : NULL;
//$mesure_demandee=isset($_POST['mesure_demandee']) ? $_POST['mesure_demandee'] : NULL;
$mesure_ele_login=isset($_POST['mesure_ele_login']) ? $_POST['mesure_ele_login'] : NULL;

$clore_incident=isset($_POST['clore_incident']) ? $_POST['clore_incident'] : NULL;

$etat_incident="";
if(isset($id_incident)) {
	$sql="SELECT 1=1 FROM s_incidents WHERE id_incident='$id_incident' AND etat='clos';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		$etat_incident="clos";
		$step=2;
	}
	elseif($_SESSION['statut']=='professeur') {
		// Si le visiteur est un professeur et que l'incident a été ouvert par une autre personne, on fait comme si l'incident était clos.
		// Aucune modification ne peut être effectuée par le professeur.
		// Il doit s'adresser à un cpe, scol, admin ou au déclarant pour apporter un commentaire.
		// Remarque: S'il arrive sur cette page c'est qu'il est protagoniste de l'incident ou déclarant... ou alors il a bricolé les valeurs en barre d'adresse... -> METTRE DES TESTS POUR L'INTERDIRE
		$sql="SELECT 1=1 FROM s_incidents WHERE id_incident='$id_incident' AND declarant!='".$_SESSION['login']."';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$etat_incident="clos";
			$step=2;
		}
	}
}

$msg="";

if($etat_incident!='clos') {
	if((isset($_POST['suppr_ele_incident']))&&(isset($id_incident))) {
		$suppr_ele_incident=$_POST['suppr_ele_incident'];
		for($i=0;$i<count($suppr_ele_incident);$i++) {
			$sql="DELETE FROM s_protagonistes WHERE login='$suppr_ele_incident[$i]' AND id_incident='$id_incident';";
			//echo "$sql<br />\n";
			$res=mysql_query($sql);
			if(!$res) {
				$msg.="ERREUR lors de la suppression de ".$suppr_ele_incident[$i]." pour l'incident $id_incident<br />\n";
			}
		}
	}
	elseif(isset($_POST['enregistrer_qualite'])) {
		$nb_protagonistes=isset($_POST['nb_protagonistes']) ? $_POST['nb_protagonistes'] : NULL;
		if(isset($nb_protagonistes)) {
			//for($i=0;$i<count($ele_login);$i++) {
			for($i=0;$i<$nb_protagonistes;$i++) {
				if(isset($ele_login[$i])) {
					$sql="SELECT 1=1 FROM s_protagonistes WHERE id_incident='$id_incident' AND login='".$ele_login[$i]."';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)==0) {
						$sql="INSERT INTO s_protagonistes SET id_incident='$id_incident', login='".$ele_login[$i]."', statut='eleve', qualite='".addslashes(ereg_replace("&#039;","'",html_entity_decode($qualite[$i])))."';";
						//echo "$sql<br />\n";
						$res=mysql_query($sql);
						if(!$res) {
							$msg.="ERREUR lors de l'enregistrement de ".$ele_login[$i]."<br />\n";
						}
					}
					else {
						//$sql="UPDATE s_protagonistes SET qualite='$qualite[$i]' WHERE id_incident='$id_incident' AND login='".$ele_login[$i]."' AND statut='eleve';";
						$sql="UPDATE s_protagonistes SET qualite='".addslashes(ereg_replace("&#039;","'",html_entity_decode($qualite[$i])))."' WHERE id_incident='$id_incident' AND login='".$ele_login[$i]."' AND statut='eleve';";
						//$sql="UPDATE s_protagonistes SET qualite='".$qualite[$i]."' WHERE id_incident='$id_incident' AND login='".$ele_login[$i]."' AND statut='eleve';";
						//echo "$sql<br />\n";
						$res=mysql_query($sql);
						if(!$res) {
							$msg.="ERREUR lors de l'enregistrement de ".$ele_login[$i]."<br />\n";
						}
					}

					if(isset($avertie[$i])) {
						$sql="UPDATE s_protagonistes SET avertie='$avertie[$i]' WHERE id_incident='$id_incident' AND login='".$ele_login[$i]."';";
						//echo "$sql<br />\n";
						$update=mysql_query($sql);
						if(!$update) {
							echo "Echec de l'enregistrement pour la famille de ".$ele_login[$i].".";
						}
					}
				}
			}

			//for($i=0;$i<count($u_login);$i++) {
			for($i=0;$i<$nb_protagonistes;$i++) {
				if(isset($u_login[$i])) {
					$sql="SELECT 1=1 FROM s_protagonistes WHERE id_incident='$id_incident' AND login='".$u_login[$i]."';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)==0) {
						$tmp_statut="";
						$sql="SELECT statut FROM utilisateurs WHERE login='".$u_login[$i]."'";
						$res_statut=mysql_query($sql);
						if(mysql_num_rows($res_statut)>0) {
							$lig_statut=mysql_fetch_object($res_statut);
							$tmp_statut=$lig_statut->statut;
						}

						$sql="INSERT INTO s_protagonistes SET id_incident='$id_incident', login='".$u_login[$i]."', statut='$tmp_statut', qualite='".addslashes(ereg_replace("&#039;","'",html_entity_decode($qualite[$i])))."';";
						//echo "$sql<br />\n";
						$res=mysql_query($sql);
						if(!$res) {
							$msg.="ERREUR lors de l'enregistrement de ".$u_login[$i]."<br />\n";
						}
					}
					else {
						//$sql="UPDATE s_protagonistes SET qualite='$qualite[$i]' WHERE id_incident='$id_incident' AND login='".$u_login[$i]."' AND statut='uve';";
						$sql="UPDATE s_protagonistes SET qualite='".addslashes(ereg_replace("&#039;","'",html_entity_decode($qualite[$i])))."' WHERE id_incident='$id_incident' AND login='".$u_login[$i]."';";
						//echo "$sql<br />\n";
						$res=mysql_query($sql);
						if(!$res) {
							$msg.="ERREUR lors de l'enregistrement de ".$u_login[$i]."<br />\n";
						}
					}
				}
			}
		}
	}
	elseif(isset($is_posted)) {
		if(!isset($id_incident)) {
			if(!isset($display_date)) {
				$annee = strftime("%Y");
				$mois = strftime("%m");
				$jour = strftime("%d");
				//$display_date = $jour."/".$mois."/".$annee;
			}
			else {
				/*
				$annee = substr($display_date,0,4);
				$mois =  substr($display_date,5,2);
				$jour =  substr($display_date,8,2);
				*/
				$jour =  substr($display_date,0,2);
				$mois =  substr($display_date,3,2);
				$annee = substr($display_date,6,4);
			}

			if(!checkdate($mois,$jour,$annee)) {
				$annee = strftime("%Y");
				$mois = strftime("%m");
				$jour = strftime("%d");

				$msg.="La date proposée n'était pas valide. Elle a été remplacée par la date du jour courant.";
			}

			if(!isset($display_heure)) {
				//$display_heure=strftime("%H").":".strftime("%M");
				$display_heure="";
			}

			if(!isset($nature)) {
				$nature="";
			}

			if (isset($NON_PROTECT["description"])){
				$description=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["description"]));

				// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
				$description=ereg_replace('(\\\r\\\n)+',"\r\n",$description);
			}
			else {
				$description="";
			}

			if(!isset($id_lieu)) {
				$id_lieu="";
			}

			$sql="INSERT INTO s_incidents SET declarant='".$_SESSION['login']."',
												date='$annee-$mois-$jour',
												heure='$display_heure',
												nature='".traitement_magic_quotes(corriger_caracteres($nature))."',
												description='".$description."',
												id_lieu='$id_lieu';";
			//echo "$sql<br />\n";
			$res=mysql_query($sql);
			if(!$res) {
				$msg.="ERREUR lors de l'enregistrement de l'incident&nbsp;:".$sql."<br />\n";
			}
			else {
				$id_incident=mysql_insert_id();
				$msg.="Enregistrement de l'incident n°".$id_incident." effectué.<br />\n";
			}
		}
		else {
			$temoin_modif="n";
			$sql="UPDATE s_incidents SET ";
			if(isset($display_date)) {
				/*
				$annee = substr($display_date,0,4);
				$mois =  substr($display_date,5,2);
				$jour =  substr($display_date,8,2);
				*/
				$jour =  substr($display_date,0,2);
				$mois =  substr($display_date,3,2);
				$annee = substr($display_date,6,4);
				/*
				echo "\$jour=$jour<br />";
				echo "\$mois=$mois<br />";
				echo "\$annee=$annee<br />";
				*/

				if(!checkdate($mois,$jour,$annee)) {
					$annee = strftime("%Y");
					$mois = strftime("%m");
					$jour = strftime("%d");

					$msg.="La date proposée n'était pas valide. Elle a été remplacée par la date du jour courant.";
				}

				$sql.="date='$annee-$mois-$jour' ,";

				$temoin_modif="y";
			}

			if(isset($display_heure)) {
				$sql.="heure='$display_heure' ,";
				$temoin_modif="y";
			}

			if(isset($nature)) {
				$sql.="nature='".traitement_magic_quotes(corriger_caracteres($nature))."' ,";
				$temoin_modif="y";
			}

			if (isset($NON_PROTECT["description"])){
				$description=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["description"]));

				// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
				$description=ereg_replace('(\\\r\\\n)+',"\r\n",$description);

				$sql.="description='".$description."' ,";
				$temoin_modif="y";
			}

			if(isset($id_lieu)) {
				$sql.="id_lieu='$id_lieu' ,";
				$temoin_modif="y";
			}

			// Pour faire sauter le ", " en fin de $sql:
			$sql=substr($sql,0,strlen($sql)-2);

			$sql.=" WHERE id_incident='$id_incident';";

			if($temoin_modif=="y") {
				//echo "$sql<br />\n";
				$res=mysql_query($sql);
				if(!$res) {
					$msg.="ERREUR lors de la mise à jour de l'incident ".$id_incident."<br />\n";
				}
				else {
					$msg.="Mise à jour de l'incident n°".$id_incident." effectuée.<br />\n";
				}
			}
		}

		//echo "\$id_incident=$id_incident<br />";
		//echo "count(\$ele_login)=".count($ele_login)."<br />";

		if(isset($id_incident)) {
			for($i=0;$i<count($ele_login);$i++) {
				$sql="SELECT 1=1 FROM s_protagonistes WHERE id_incident='$id_incident' AND login='".$ele_login[$i]."';";
				//echo "$sql<br />\n";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)==0) {
					//$sql="INSERT INTO s_protagonistes SET id_incident='$id_incident', login='".$ele_login[$i]."', statut='eleve', qualite='$qualite[$i]';";
					//$sql="INSERT INTO s_protagonistes SET id_incident='$id_incident', login='".$ele_login[$i]."', statut='eleve', qualite='".addslashes(ereg_replace("&#039;","'",html_entity_decode($qualite[$i])))."', avertie='avertie='".$avertie[$i]."';";
					$sql="INSERT INTO s_protagonistes SET id_incident='$id_incident', login='".$ele_login[$i]."', statut='eleve';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de l'enregistrement de ".$ele_login[$i]."<br />\n";
					}
				}
				/*
				else {
					$sql="UPDATE s_protagonistes SET qualite='$qualite[$i]' WHERE id_incident='$id_incident' AND login='".$ele_login[$i]."' AND statut='eleve';";
					echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de l'enregistrement de ".$ele_login[$i]."<br />\n";
					}
				}
				*/
			}


			for($i=0;$i<count($u_login);$i++) {
				$sql="SELECT 1=1 FROM s_protagonistes WHERE id_incident='$id_incident' AND login='".$u_login[$i]."';";
				//echo "$sql<br />\n";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)==0) {
					$tmp_statut="";
					$sql="SELECT statut FROM utilisateurs WHERE login='".$u_login[$i]."'";
					$res_statut=mysql_query($sql);
					if(mysql_num_rows($res_statut)>0) {
						$lig_statut=mysql_fetch_object($res_statut);
						$tmp_statut=$lig_statut->statut;

						$sql="INSERT INTO s_protagonistes SET id_incident='$id_incident', login='".$u_login[$i]."', statut='$tmp_statut', qualite='".addslashes(ereg_replace("&#039;","'",html_entity_decode($qualite[$i])))."';";
						//echo "$sql<br />\n";
						$res=mysql_query($sql);
						if(!$res) {
							$msg.="ERREUR lors de l'enregistrement de ".$u_login[$i]."<br />\n";
						}
					}
					else {
						$msg.="ERREUR lors de l'enregistrement de ".$u_login[$i].": statut inconnu???<br />\n";
					}
				}
			}


			if(isset($mesure_ele_login)) {
				// Recherche des mesures déjà enregistrées:
				for($i=0;$i<count($mesure_ele_login);$i++) {

					$tab_mes_enregistree=array();
					//$sql="SELECT mesure FROM s_traitement_incident WHERE id_incident='$id_incident' AND login_ele='".$mesure_ele_login[$i]."';";
					$sql="SELECT id_mesure FROM s_traitement_incident WHERE id_incident='$id_incident' AND login_ele='".$mesure_ele_login[$i]."';";
					$res_mes=mysql_query($sql);
					if(mysql_num_rows($res_mes)>0) {
						while($lig_mes=mysql_fetch_object($res_mes)) {
							//$tab_mes_enregistree[]=$lig_mes->mesure;
							$tab_mes_enregistree[]=$lig_mes->id_mesure;
						}
					}

					unset($mesure_prise);
					$mesure_prise=isset($_POST['mesure_prise_'.$i]) ? $_POST['mesure_prise_'.$i] : array();
					unset($mesure_demandee);
					$mesure_demandee=isset($_POST['mesure_demandee_'.$i]) ? $_POST['mesure_demandee_'.$i] : array();

					//for($i=0;$i<count($mesure_demandee);$i++) {
					//}

					//$tab_mesure_possible=array();
					//$sql="SELECT mesure FROM s_mesures;";
					$sql="SELECT * FROM s_mesures;";
					$res_mes=mysql_query($sql);
					if(mysql_num_rows($res_mes)>0) {
						while($lig_mes=mysql_fetch_object($res_mes)) {
							//$tab_mesure_possible[]=$lig_mes->mesure;

							/*
							if(in_array($lig_mes->mesure,$tab_mes_enregistree)) {
								if((!in_array($lig_mes->mesure,$mesure_prise))&&
									(!in_array($lig_mes->mesure,$mesure_demandee))) {
							*/
							if(in_array($lig_mes->id,$tab_mes_enregistree)) {
								if((!in_array($lig_mes->id,$mesure_prise))&&
									(!in_array($lig_mes->id,$mesure_demandee))) {
									// Cette mesure n'a plus lieu d'être enregistrée
									//$sql="DELETE FROM s_traitement_incident WHERE id_incident='$id_incident' AND login_ele='".$mesure_ele_login[$i]."' AND mesure='".$lig_mes->mesure."';";
									$sql="DELETE FROM s_traitement_incident WHERE id_incident='$id_incident' AND login_ele='".$mesure_ele_login[$i]."' AND id_mesure='".$lig_mes->id."';";
									$suppr=mysql_query($sql);
									if(!$suppr) {
										$msg.="ERREUR lors de la suppression de la mesure ".$lig_mes->mesure." pour ".$mesure_ele_login[$i]."<br />\n";
									}
								}
							}
							else {
								//if(in_array($lig_mes->mesure,$mesure_prise)) {
								//if((in_array($lig_mes->mesure,$mesure_prise))||
								//	(in_array($lig_mes->mesure,$mesure_demandee))) {
								if((in_array($lig_mes->id,$mesure_prise))||
									(in_array($lig_mes->id,$mesure_demandee))) {
									// Cette mesure doit être enregistrée
									//$sql="INSERT INTO s_traitement_incident SET id_incident='$id_incident', login_ele='".$mesure_ele_login[$i]."', mesure='".$lig_mes->mesure."', login_u='".$_SESSION['login']."';";
									$sql="INSERT INTO s_traitement_incident SET id_incident='$id_incident', login_ele='".$mesure_ele_login[$i]."', id_mesure='".$lig_mes->id."', login_u='".$_SESSION['login']."';";
									$insert=mysql_query($sql);
									if(!$insert) {
										$msg.="ERREUR lors de l'enregistrement de la mesure ".$lig_mes->mesure." pour ".$mesure_ele_login[$i]."<br />\n";
									}
								}

								if((in_array($lig_mes->id,$mesure_demandee))) {unset($clore_incident);}
							}
						}
					}
				}
			}

			if(isset($clore_incident)) {
				$sql="UPDATE s_incidents SET etat='clos' WHERE id_incident='$id_incident';";
				$update=mysql_query($sql);
				if(!$update) {
					$msg.="ERREUR lors de la clôture de l'incident n°$id_incident.<br />\n";
				}
				else {
					$msg.="Clôture de l'incident n°$id_incident.<br />\n";
				}
			}
		}
	}
}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Discipline: Signaler un incident";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<div id='div_svg_qualite' style='margin:auto; color:red; text-align:center;'></div>\n";
echo "<div id='div_svg_avertie' style='margin:auto; color:red; text-align:center;'></div>\n";
//debug_var();

$page="saisie_incident.php";

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

if(($_SESSION['statut']=='administrateur')||
($_SESSION['statut']=='cpe')||
($_SESSION['statut']=='scolarite')) {
	$sql="SELECT 1=1 FROM s_incidents si
	LEFT JOIN s_protagonistes sp ON sp.id_incident=si.id_incident
	WHERE sp.id_incident IS NULL;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo " | <a href='incidents_sans_protagonistes.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Incidents sans protagonistes</a>\n";
		echo " | <a href='traiter_incident.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Liste des incidents</a> (<i>avec protagonistes</i>)\n";
	}
	else {
		echo " | <a href='traiter_incident.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Liste des incidents</a>\n";
	}
}
elseif (($_SESSION['statut']=='professeur')||
($_SESSION['statut']=='autre')) {
	$sql="SELECT 1=1 FROM s_incidents si
	LEFT JOIN s_protagonistes sp ON sp.id_incident=si.id_incident
	WHERE sp.id_incident IS NULL;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo " | <a href='incidents_sans_protagonistes.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Incidents sans protagonistes</a>\n";
	}

	// Rechercher les incidents signalés par le prof ou ayant le prof pour protagoniste
	$sql="SELECT 1=1 FROM s_incidents WHERE declarant='".$_SESSION['login']."';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo " | <a href='traiter_incident.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Liste des incidents</a>\n";
	}
	else {
		$sql="SELECT 1=1 FROM s_protagonistes WHERE login='".$_SESSION['login']."';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			echo " | <a href='traiter_incident.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Liste des incidents</a>\n";
		}
		else {
			$sql="SELECT 1=1 FROM j_eleves_professeurs jep, s_protagonistes sp WHERE sp.login=jep.login AND jep.professeur='".$_SESSION['login']."';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				echo " | <a href='traiter_incident.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Liste des incidents</a>\n";
			}
		}
	}
}

/* ERIC a décommenter pour la gestion des modele ooo des rapport d'incident.
if ($step==2) {   //Eric Ajout génération du modèle Ooo pour imprimer le rapport d'incident.
    echo " | <a href='../mod_ooo/rapport_incident.php?mode=module_discipline&amp;id_incident=$id_incident' onclick=\"return confirm_abandon (this, change, '$themessage')\">Imprimer le rapport d'incident</a>\n";
}
*/
echo "</p>\n";

$etat_incident="";
if(isset($id_incident)) {
	$sql="SELECT 1=1 FROM s_incidents WHERE id_incident='$id_incident' AND etat='clos';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		$etat_incident="clos";
		$step=2;
	}
	elseif($_SESSION['statut']=='professeur') {
		// Si le visiteur est un professeur et que l'incident a été ouvert par une autre personne, on fait comme si l'incident était clos.
		// Aucune modification ne peut être effectuée par le professeur.
		// Il doit s'adresser à un cpe, scol, admin ou au déclarant pour apporter un commentaire.
		// Remarque: S'il arrive sur cette page c'est qu'il est protagoniste de l'incident ou déclarant.
		$sql="SELECT 1=1 FROM s_incidents WHERE id_incident='$id_incident' AND declarant!='".$_SESSION['login']."';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$etat_incident="clos";
			$step=2;
		}
	}
}

if($etat_incident!='clos') {
	//=====================================================
	// MENU
	echo "<div id='s_menu' style='float:right; border: 1px solid black; background-color: white; width: 15em;'>\n";
	echo "<ul style='margin:0px;'>\n";
	if($step!=0) {
		echo "<li>\n";
		echo "<a href='".$_SERVER['PHP_SELF']."?step=0";
		if(isset($id_incident)) {echo "&amp;id_incident=$id_incident";}
		echo "'";
		echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
		echo ">Ajouter des élèves</a>";
		echo "</li>\n";
	}
	if($step!=1) {
		echo "<li>\n";
		echo "<a href='".$_SERVER['PHP_SELF']."?step=1";
		if(isset($id_incident)) {echo "&amp;id_incident=$id_incident";}
		echo "'";
		echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
		echo ">Ajouter des personnels</a>";
		echo "</li>\n";
	}
	if($step!=2) {
		echo "<li>\n";
		echo "<a href='".$_SERVER['PHP_SELF']."?step=2";
		if(isset($id_incident)) {echo "&amp;id_incident=$id_incident";}
		echo "'";
		echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
		echo ">Préciser l'incident</a>";
		echo "</li>\n";
	}
	if((isset($id_incident))&&($_SESSION['statut']!='professeur')&&(($_SESSION['statut']!='autre'))) {
		echo "<li>\n";
		echo "<a href='saisie_sanction.php?id_incident=$id_incident";
		echo "'";
		echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
		echo ">Traitement/sanction</a>";
		echo "</li>\n";
	}

	echo "</ul>\n";
	echo "</div>\n";
	//=====================================================
}

if(isset($id_incident)) {
	// AFFICHAGE DES PROTAGONISTES (déjà enregistrés) DE L'INCIDENT

	// Récupération des qualités
	$tab_qualite=array();
	$sql="SELECT * FROM s_qualites ORDER BY qualite;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab_qualite[]=$lig->qualite;
		}
	}

	$sql="SELECT * FROM s_protagonistes WHERE id_incident='$id_incident' ORDER BY statut,qualite,login;";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		if($etat_incident!='clos') {
			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='form_suppr'>\n";
			echo "<input type='hidden' name='step' value='$step' />\n";
		}

		echo "<p class='bold'>Protagonistes de l'incident n°$id_incident&nbsp;:</p>\n";

		echo "<blockquote>\n";

		echo "<table class='boireaus' border='1' summary='Protagonistes'>\n";
		echo "<tr>\n";
		echo "<th>Individu</th>\n";
		echo "<th>Statut</th>\n";
		//echo "<th>Qualité dans l'incident</th>\n";
		echo "<th>Rôle dans l'incident</th>\n";

//Eric modèle Ooo
		if ($gepiSettings['active_mod_ooo'] == 'y') {
		echo "<th>Retenue</th>\n";
		}

		if($_SESSION['statut']!='professeur') {
			echo "<th>\n";
			// Avertir la famille... et afficher les avertissements effectués.
			echo "Famille avertie\n";
			echo "</th>\n";
		}

		if($etat_incident!='clos') {
			echo "<th>\n";
			echo "<input type='submit' name='supprimer' value='Supprimer' />\n";
			// A FAIRE: Ajouter des liens Tout cocher/décocher
			echo "</th>\n";
		}

		echo "</tr>\n";
		$alt=1;
		$cpt=0;
		while($lig=mysql_fetch_object($res)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";

			//Individu
			if($lig->statut=='eleve') {
				echo "<td>";
				$sql="SELECT nom,prenom FROM eleves WHERE login='$lig->login';";
				//echo "$sql<br />\n";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0) {
					$ele_login[]=$lig->login;

					$lig2=mysql_fetch_object($res2);
					echo ucfirst(strtolower($lig2->prenom))." ".strtoupper($lig2->nom);
				}
				else {
					echo "ERREUR: Login inconnu";
				}

				echo "</td>\n";
				echo "<td>";
				echo "élève (<i>";
				$tmp_tab=get_class_from_ele_login($lig->login);
				//if(isset($tmp_tab['liste'])) {echo $tmp_tab['liste'];}
				if(isset($tmp_tab['liste_nbsp'])) {echo $tmp_tab['liste_nbsp'];}
				echo "</i>)";
				echo "</td>\n";

				echo "<td>";
				if($etat_incident!='clos') {
					echo "<input type='hidden' name='ele_login[$cpt]' value=\"$lig->login\" />\n";

					//echo "<select name='qualite[$cpt]' onchange='changement();'>\n";
					echo "<select name='qualite[$cpt]' id='qualite_$cpt' onchange=\"sauve_role('$id_incident','$lig->login','$cpt');update_colonne_retenue('$id_incident','$lig->login','$cpt');\">\n";
					echo "<option value=''";
					if($lig->qualite=="") {echo " selected='selected'";}
					echo ">---</option>\n";
					for($loop=0;$loop<count($tab_qualite);$loop++) {
						echo "<option value=\"$tab_qualite[$loop]\"";
						if($lig->qualite==$tab_qualite[$loop]) {echo " selected='selected'";}
						echo ">$tab_qualite[$loop]</option>\n";
					}
					echo "</select>\n";
				}
				else {
					echo "$lig->qualite";
				}
				echo "</td>\n";
			}
			else {
				echo "<td>";
				$sql="SELECT nom,prenom,civilite FROM utilisateurs WHERE login='$lig->login';";
				//echo "$sql<br />\n";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0) {
					$lig2=mysql_fetch_object($res2);
					echo ucfirst(strtolower($lig2->prenom))." ".strtoupper($lig2->nom);
				}
				else {
					echo "ERREUR: Login inconnu";
				}

				echo "</td>\n";

				//echo "<td>$lig->statut</td>\n";
				if($lig->statut=='autre') {
					//echo "<td>".$_SESSION['statut_special']."</td>\n";

					$sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
													WHERE du.login_user = '".$lig->login."'
													AND du.id_statut = ds.id;";
					$query = mysql_query($sql);
					$result = mysql_fetch_array($query);

					echo "<td>".$result['nom_statut']."</td>\n";
				}
				else {
					echo "<td>$lig->statut</td>\n";
				}

				echo "<td>";
				if($etat_incident!='clos') {
					echo "<input type='hidden' name='u_login[$cpt]' value=\"$lig->login\" />\n";

					//echo "<select name='qualite[$cpt]' onchange='changement();'>\n";
					echo "<select name='qualite[$cpt]' id='qualite_$cpt' onchange=\"sauve_role('$id_incident','$lig->login','$cpt');\">\n";
					echo "<option value=''";
					if($lig->qualite=="") {echo " selected='selected'";}
					echo ">---</option>\n";
					for($loop=0;$loop<count($tab_qualite);$loop++) {
						echo "<option value=\"$tab_qualite[$loop]\"";
						if($lig->qualite==$tab_qualite[$loop]) {echo " selected='selected'";}
						echo ">$tab_qualite[$loop]</option>\n";
					}
					echo "</select>\n";
				}
				else {
					echo "$lig->qualite";
				}
				echo "</td>\n";
			}
//Eric  modèle Ooo
			if ($gepiSettings['active_mod_ooo'] == 'y') {
			    echo "<td id='td_retenue_$cpt'>";
				if ($lig->qualite=='Responsable') { //un retenue seulement pour un responsable !
		            echo "<a href='../mod_ooo/retenue.php?mode=module_discipline&amp;id_incident=$id_incident&amp;ele_login=$lig->login' title='Imprimer la retenue'><img src='../images/icons/print.png' width='16' height='16' alt='Imprimer Retenue' /></a>\n";
				}
                echo "</td>";
		    }


			if($_SESSION['statut']!='professeur') {
				echo "<td>\n";
				if($lig->statut=='eleve') {
					// Avertir la famille... et afficher les avertissements effectués.
					/*
					$sql="SELECT * FROM s_communication WHERE id_incident='$id_incident' AND login='$lig->login' ORDER BY nature;";
					$res_comm=mysql_query($sql);
					if(mysql_num_rows($res_comm)>0) {
						while($lig_comm=mysql_fetch_object($res_comm)) {
							// Nature: mail, courrier
							echo "<a href='avertir_famille.php?id_incident=$id_incident&amp;ele_login=$lig->login&amp;id_communication=$id_communication'>$nature</a>\n";
						}
					}
					*/
					$defaut="N";
					/*
					$sql="SELECT * FROM s_comm_incident WHERE id_incident='$id_incident' AND login='$lig->login';";
					$res_comm=mysql_query($sql);
					if(mysql_num_rows($res_comm)>0) {
						$lig_comm=mysql_fetch_object($res_comm);
						$defaut=$lig_comm->avertie;
					}
					*/

					if($etat_incident!='clos') {
						$defaut=$lig->avertie;

						echo "<input type='radio' name='avertie[$cpt]' id='avertie_O_$cpt' value='O' ";
						if($defaut=="O") {echo "checked='checked' ";}
						echo "onchange=\"sauve_avertie('$id_incident','$lig->login','O')\" ";
						echo "/><label for='avertie_O_$cpt' style='cursor:pointer;'> O </label>/";
						echo "<label for='avertie_N_$cpt' style='cursor:pointer;'> N </label><input type='radio' name='avertie[$cpt]' id='avertie_N_$cpt' value='N' ";
						if($defaut=="N") {echo "checked='checked' ";}
						echo "onchange=\"sauve_avertie('$id_incident','$lig->login','N')\" ";
						echo "/> ";

						echo "<a href='avertir_famille.php?id_incident=$id_incident&amp;ele_login=$lig->login' title='Avertir par courrier'><img src='../images/icons/saisie.png' width='16' height='16' alt='Avertir' /></a>\n";
					}
					else {
						if($lig->avertie=="O") {echo "Oui";} else {echo "Non";}
					}
				}
				else {
					echo "&nbsp;\n";
				}
				echo "</td>\n";
			}

			if($etat_incident!='clos') {
				// J'ai laissé le nom suppr_ELE_incident[] même pour la suppression de personnels protagonistes
				echo "<td><input type='checkbox' name='suppr_ele_incident[]' id='suppr_$cpt' value=\"$lig->login\" /></td>\n";
			}

			echo "</tr>\n";
			$cpt++;
		}
		echo "</table>\n";
		if($etat_incident!='clos') {
			echo "<input type='hidden' name='nb_protagonistes' value='$cpt' />\n";
			echo "<input type='hidden' name='id_incident' value='$id_incident' />\n";
			echo "<p><input type='submit' name='enregistrer_qualite' value='Enregistrer' /></p>\n";
		}

		if($step!=2) {
			$sql="SELECT 1=1 FROM s_incidents WHERE id_incident='$id_incident' AND (nature!='' OR description!='');";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0) {
				echo "<p style='color:red;'>N'oubliez pas de ";
				echo "<a href='".$_SERVER['PHP_SELF']."?step=2";
				if(isset($id_incident)) {echo "&amp;id_incident=$id_incident";}
				echo "'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				echo ">préciser l'incident</a> après ajout des protagonistes.</p>\n";
			}
		}
		echo "</blockquote>\n";


		echo "<script type='text/javascript'>
	// <![CDATA[
	function sauve_role(id_incident,login,cpt) {
		//qualite=document.getElementById('qualite_'+cpt).selectedIndex;
		qualite=document.getElementById('qualite_'+cpt).options[document.getElementById('qualite_'+cpt).selectedIndex].value;
		//alert('qualite='+qualite);
		new Ajax.Updater($('div_svg_qualite'),'sauve_role.php?id_incident='+id_incident+'&login='+login+'&qualite='+qualite,{method: 'get'});
	}

	function update_colonne_retenue(id_incident,login,cpt) {
		//qualite=document.getElementById('qualite_'+cpt).selectedIndex;
		qualite=document.getElementById('qualite_'+cpt).options[document.getElementById('qualite_'+cpt).selectedIndex].value;
		//alert('qualite='+qualite);
		new Ajax.Updater($('td_retenue_'+cpt),'update_colonne_retenue.php?id_incident='+id_incident+'&login='+login+'&qualite='+qualite,{method: 'get'});
	}

	function sauve_avertie(id_incident,login,avertie) {
		//avertie=document.getElementById('avertie_'+cpt).value;
		new Ajax.Updater($('div_svg_avertie'),'sauve_famille_avertie.php?id_incident='+id_incident+'&login='+login+'&avertie='+avertie,{method: 'get'});
	}
	//]]>
</script>\n";

		if($etat_incident!='clos') {
			echo "</form>\n";
		}
	}
	else {
		echo "<p style='color:red;'>Aucun protagoniste n'a (<i>encore</i>) été spécifié pour cet incident.</p>\n";
	}
}
else {
	echo "<p class='bold'>Protagonistes de l'incident&nbsp;:</p>\n";
	echo "<blockquote>\n";
	echo "<p style='color:red;'>Aucun protagoniste n'a (<i>encore</i>) été spécifié pour cet incident.</p>\n";
	echo "</blockquote>\n";
}

//==========================================
if($step==0) {
	// AJOUT DE PROTAGONISTES ELEVES A L'INCIDENT
	echo "<p class='bold'>Ajouter des protagonistes de l'incident.</p>\n";

	echo "<blockquote>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire1'>
	<p>
	Afficher les élèves dont le <b>nom</b> contient: <input type='text' name='rech_nom' value='' />
	<input type='hidden' name='page' value='$page' />
	<input type='hidden' name='step' value='$step' />
	<input type='hidden' name='is_posted' value='y' />
	<input type='submit' name='recherche_eleve' value='Rechercher'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo " />
	</p>\n";
	if(isset($id_incident)) {echo "<input type='hidden' name='id_incident' value='$id_incident' />\n";}
	echo "</form>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire2'>\n";

	if(isset($_POST['recherche_eleve'])) {
		//echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir un autre élève</a>\n";
		//echo "</p>\n";
		//echo "</div>\n";

		recherche_ele($rech_nom,$_SERVER['PHP_SELF']);
		echo "<p><input type='submit' name='Ajouter' value='Ajouter' /></p>\n";

	}
	elseif(isset($id_classe)) {
		$sql="SELECT DISTINCT e.login,e.nom,e.prenom FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe' ORDER BY e.nom,e.prenom;";
		//echo "$sql<br />";
		$res_ele=mysql_query($sql);
		if(mysql_num_rows($res_ele)>0) {
			echo "<p>Elèves de la classe de ".get_class_from_id($id_classe)."&nbsp;:</p>\n";

			echo "<blockquote>\n";

			$nombreligne=mysql_num_rows($res_ele);

			$nbcol=3;

			// Nombre de lignes dans chaque colonne:
			$nb_par_colonne=round($nombreligne/$nbcol);

			echo "<table width='100%' summary=\"Tableau de choix des élèves\">\n";
			echo "<tr valign='top' align='center'>\n";
			echo "<td align='left'>\n";

			$i = 0;
			$alt=1;
			echo "<table class='boireaus'>\n";
			while ($i < $nombreligne){
				$lig_ele=mysql_fetch_object($res_ele);

				if(($i>0)&&(round($i/$nb_par_colonne)==$i/$nb_par_colonne)){
						echo "</table>\n";
					echo "</td>\n";
					echo "<td align='left'>\n";

						$alt=1;
						echo "<table class='boireaus'>\n";
				}

				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='ele_login[]' id='ele_login_$i' value=\"$lig_ele->login\" />\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "<label for='ele_login_$i' style='cursor:pointer;'>".ucfirst(strtolower($lig_ele->prenom))." ".strtoupper($lig_ele->nom)."</label>";
				echo "</td>\n";
				echo "</tr>\n";

				$i++;
			}
				echo "</table>\n";

			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			echo "<p><input type='submit' name='Ajouter' value='Ajouter' /></p>\n";

			echo "</blockquote>\n";

		}

	}

	if(isset($id_incident)) {echo "<input type='hidden' name='id_incident' value='$id_incident' />\n";}
	echo "<input type='hidden' name='is_posted' value='y' />\n";
	echo "</form>\n";

	// On adapte la liste des classes selon le visiteur
	// Peut-être faudrait-il permettre d'accéder à toutes les classes...
	// On peut signaler un incident survenu dans un couloir avec un élève que l'on n'a pas...
	// ... on peut toujours le faire en faisant une recherche par nom de l'élève.
	if($_SESSION['statut']=='scolarite') {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur') {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc,j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgc.id_groupe=jgp.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
	}
	elseif($_SESSION['statut']=='cpe') {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe=c.id ORDER BY c.classe";
	}
	elseif(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='secours')) {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	//echo "$sql<br />";
	if ($_SESSION['statut']!='autre') { //statut autre : ajout Eric de la condition 
		$res_clas=mysql_query($sql);
		if(mysql_num_rows($res_clas)>0) {
			echo "<p>Ou choisir un élève dans une classe:</p>\n";

			$tab_txt=array();
			$tab_lien=array();

			while($lig_clas=mysql_fetch_object($res_clas)) {
				$tab_txt[]=$lig_clas->classe;
				if(isset($id_incident)) {
					//$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id."&amp;id_incident=$id_incident";
					$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id."&amp;id_incident=$id_incident'onclick='return confirm_abandon (this, change, \"$themessage\")";
				}
				else {
					$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id;
				}
			}

			echo "<blockquote>\n";
			tab_liste($tab_txt,$tab_lien,4);
			echo "</blockquote>\n";
	}
}
	echo "</blockquote>\n";

}
elseif($step==1) {
	//==========================================
	// AJOUT DE PERSONNELS COMME PROTAGONISTES DE L'INCIDENT
	echo "<p class='bold'>Ajouter des personnels&nbsp;:</p>\n";

	//$sql="SELECT DISTINCT statut FROM utilisateurs WHERE statut!='responsable' ORDER BY statut;";
	$sql="SELECT DISTINCT statut FROM utilisateurs WHERE statut!='responsable' AND etat='actif' ORDER BY statut;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		// Ca ne doit pas arriver;o)
		echo "<p style='color:red;'>La table 'utilisateurs' ne comporte aucun compte???</p>\n";
		echo "<p><br /></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<blockquote>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire1'>
	<p>
	Afficher les utilisateurs dont le <b>nom</b> contient: <input type='text' name='rech_nom' value='' />
	<input type='hidden' name='page' value='$page' />
	<input type='hidden' name='step' value='$step' />
	<input type='hidden' name='is_posted' value='y' />
	<input type='submit' name='recherche_utilisateur' value='Rechercher'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo " />
	</p>\n";
	if(isset($id_incident)) {echo "<input type='hidden' name='id_incident' value='$id_incident' />\n";}
	echo "</form>\n";

	if(isset($_POST['recherche_utilisateur'])) {
        echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire2'>\n";
        echo "<input type='hidden' name='page' value='$page' />\n";
        echo "<input type='hidden' name='step' value='$step' />\n";

		recherche_utilisateur($rech_nom,$_SERVER['PHP_SELF']);
		echo "<p><input type='submit' name='Ajouter' value='Ajouter' /></p>\n";

        if(isset($id_incident)) {echo "<input type='hidden' name='id_incident' value='$id_incident' />\n";}
        echo "<input type='hidden' name='is_posted' value='y' />\n";
		echo "</form>\n";
	}
	elseif(isset($categ_u)) {
		$sql="SELECT login, nom, prenom, civilite FROM utilisateurs WHERE statut='$categ_u' ORDER BY nom, prenom;";
		$res2=mysql_query($sql);
		if(mysql_num_rows($res2)==0) {
			// Ca ne doit pas arriver;o)
			echo "<p style='color:red;'>La table 'utilisateurs' ne comporte pas de comptes de statut '$categ_u'.</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
		echo "<input type='hidden' name='step' value='$step' />\n";
		echo "<input type='hidden' name='is_posted' value='y' />\n";
		if(isset($id_incident)) {echo "<input type='hidden' name='id_incident' value='$id_incident' />\n";}

		$nombreligne=mysql_num_rows($res2);

		$nbcol=3;

		// Nombre de lignes dans chaque colonne:
		$nb_par_colonne=round($nombreligne/$nbcol);

		echo "<table width='100%' summary=\"Tableau de choix des $categ_u\">\n";
		echo "<tr valign='top' align='center'>\n";
		echo "<td align='left'>\n";

		$i = 0;
		$alt=1;
		echo "<table class='boireaus' summary='Colonne de $categ_u'>\n";
		while ($i < $nombreligne){
			$lig2=mysql_fetch_object($res2);

			if(($i>0)&&(round($i/$nb_par_colonne)==$i/$nb_par_colonne)){
					echo "</table>\n";
				echo "</td>\n";
				echo "<td align='left'>\n";

					$alt=1;
					echo "<table class='boireaus' summary='Colonne de $categ_u'>\n";
			}

			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			echo "<input type='checkbox' name='u_login[]' id='u_login_$i' value=\"$lig2->login\" />\n";
			echo "</td>\n";
			echo "<td>\n";
			//echo "<label for='u_login_$i' style='cursor:pointer;'>".$lig2->civilite." ".ucwords(strtolower($lig2->prenom))." ".strtoupper($lig2->nom)."</label>";
			echo "<label for='u_login_$i' style='cursor:pointer;'>".$lig2->civilite." ".strtoupper($lig2->nom)." ".ucfirst(substr($lig2->prenom,0,1)).".</label>";
			echo "</td>\n";

			$sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
											WHERE du.login_user = '".$lig2->login."'
											AND du.id_statut = ds.id;";
			$query = mysql_query($sql);
			$result = mysql_fetch_array($query);
			echo "<td>".$result['nom_statut']."</td>\n";

			echo "</tr>\n";

			//echo "<br />\n";
			$i++;
		}
			echo "</table>\n";

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<p><input type='submit' name='Ajouter' value='Ajouter' /></p>\n";

		echo "</form>\n";
		echo "<p><br /></p>\n";
	}

	echo "<p class='bold'>Choisir une catégorie de personnels&nbsp;:</p>\n";
	echo "<blockquote>\n";
	while($lig=mysql_fetch_object($res)) {
		echo "<p><a href='".$_SERVER['PHP_SELF']."?step=1&amp;categ_u=".$lig->statut;
		if(isset($id_incident)) {echo "&amp;id_incident=$id_incident";}
		echo "'";
		echo " onclick='return confirm_abandon (this, change, \"$themessage\")'";
		echo ">".ucfirst($lig->statut)."</a></p>\n";
	}
	echo "</blockquote>\n";
	echo "</blockquote>\n";

}
elseif($step==2) {
	//==========================================
	// SAISIE DES DETAILS DE L'INCIDENT
	echo "<p class='bold'>Détails de l'incident";
	if(isset($id_incident)) {
		echo " n°$id_incident";

		$sql="SELECT declarant FROM s_incidents WHERE id_incident='$id_incident';";
		$res_dec=mysql_query($sql);
		if(mysql_num_rows($res_dec)>0) {
			$lig_dec=mysql_fetch_object($res_dec);
			echo " (<span style='font-size:x-small; font-style:italic; color:red;'>signalé par ".u_p_nom($lig_dec->declarant)."</span>)";
		}
	}
	echo "&nbsp;:</p>\n";

	if($etat_incident!='clos') {
		//echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire' onsubmit='verif_details_incident();'>\n";
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	}

	// Si aucune date n'est encore saisie, proposer la date du jour
	$annee = strftime("%Y");
	$mois = strftime("%m");
	$jour = strftime("%d");
	$display_date = $jour."/".$mois."/".$annee;
	// Si aucune heure n'est encore saisie, proposer l'heure courante
	$display_heure=strftime("%H").":".strftime("%M");

	//$timestamp_heure=time();
	$sql="SELECT nom_definie_periode FROM absences_creneaux WHERE CURTIME()>=heuredebut_definie_periode AND CURTIME()<heurefin_definie_periode;";
	$res_time=mysql_query($sql);
	if(mysql_num_rows($res_time)>0) {
		$lig_time=mysql_fetch_object($res_time);
		$display_heure=$lig_time->nom_definie_periode;
	}

	// Initialisation de variables:
	$nature="";
	$description="";

	if(isset($id_incident)) {
		if($etat_incident!='clos') {
			echo "<input type='hidden' name='id_incident' value='$id_incident' />\n";
		}
		$sql="SELECT * FROM s_incidents WHERE id_incident='$id_incident';";
		$res_inc=mysql_query($sql);
		if(mysql_num_rows($res_inc)>0) {
			$lig_inc=mysql_fetch_object($res_inc);

			$display_date=formate_date($lig_inc->date);
			//$display_heure=$lig_inc->heure;
			if($lig_inc->heure!="") {
				$display_heure=$lig_inc->heure;
			}
			$nature=$lig_inc->nature;
			$description=$lig_inc->description;
		}
	}

	echo "<blockquote>\n";

	$alt=1;
	echo "<table class='boireaus' border='1' summary='Details incident'>\n";
	// Date de l'incident
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>\n";
	echo "Date de l'incident&nbsp;:";
	echo "</td>\n";

	if($etat_incident!='clos') {
		echo "<td style='text-align:left;'>\n";
		//Configuration du calendrier
		include("../lib/calendrier/calendrier.class.php");
		$cal = new Calendrier("formulaire", "display_date");

		echo "<input type='text' name='display_date' size='10' value=\"".$display_date."\" />\n";
		echo "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";

		echo "</td>\n";

		//echo "<td width='1%' style='text-align:right;'><input type='submit' name='enregistrer' value='Enregistrer' onclick='verif_details_incident();' /></td>\n";
		echo "<td width='1%' style='text-align:right;'>\n";
		echo "<input type='button' name='enregistrer' value='Enregistrer' onclick='verif_details_incident();' />\n";
		echo "<noscript><input type='submit' name='enregistrer' value='Enregistrer vraiment' /></noscript>\n";
		echo "</td>\n";
	}
	else {
		echo "<td style='text-align:left;'>\n";
		echo $display_date;
		echo "</td>\n";
	}

	echo "</tr>\n";
	//========================
	// Heure
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>\n";
	echo "Heure de l'incident&nbsp;:";
	echo "</td>\n";
	echo "<td style='text-align:left;'";
	if($etat_incident!='clos') {echo " colspan='2'";}
	echo ">\n";

	if($etat_incident!='clos') {
		$texte="ATTENTION&nbsp;: Il ne faut pas mettre une heure vide.<br />En cas d'incertitude sur l'heure, il vaut mieux mettre un point d'interrogation en guise d'heure.<br />Sinon, s'il n'y a pas d'incertitude, le créneau horaire M1, S2,... est préférable.";
		$tabdiv_infobulle[]=creer_div_infobulle("div_infobulle_avertissement_heure","Heure non vide","",$texte,"",30,0,'y','y','n','n',2);

		//echo "<div id='div_avertissement_heure' style='float:right; text-align: center; border:1px solid black; margin-top: 2px; min-width: 19px;'>\n";
		echo "<div id='div_avertissement_heure' style='float:right;'>\n";
		echo "<a href='#' onmouseover=\"delais_afficher_div('div_infobulle_avertissement_heure','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onmouseout=\"cacher_div('div_infobulle_avertissement_heure');\" onclick=\"return false;\" title='Attention heure'><img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Attention heure' /></a>";
		echo "</div>\n";

		echo "<input type='text' name='display_heure' id='display_heure' size='6' value=\"".$display_heure."\" />\n";

		choix_heure('display_heure','div_choix_heure');
	}
	else {
		echo $display_heure;
	}

	echo "</td>\n";
	echo "</tr>\n";



	//========================
	// Lieu
	/*
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Lieu&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	//echo "<input type='text' name='lieu_incident' id='lieu_incident' value='$lieu_incident' onchange='changement();' />\n";
	// Sélectionner parmi des lieux déjà saisis?
	//$sql="SELECT DISTINCT lieu FROM s_retenues WHERE lieu!='' ORDER BY lieu;";
	$sql="(SELECT DISTINCT lieu FROM s_incidents WHERE lieu!='')";
	if(param_edt($_SESSION["statut"]) == 'yes') {
		$sql.=" UNION (SELECT DISTINCT nom_salle AS lieu FROM salle_cours WHERE nom_salle!='')";
	}
	$sql.=" ORDER BY lieu;";
	//echo "$sql<br />";
	$res_lieu=mysql_query($sql);
	*/

	$sql="(SELECT * FROM s_lieux_incidents WHERE lieu!='')";
	//echo "$sql<br />\n";
	$res_lieu=mysql_query($sql);
	if(mysql_num_rows($res_lieu)>0) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Lieu&nbsp;: </td>\n";

		echo "<td style='text-align:left;'";
		if($etat_incident!='clos') {
			echo " colspan='2'";
			echo ">\n";

			//echo "<select name='choix_lieu' id='choix_lieu' onchange=\"maj_lieu('lieu_incident','choix_lieu');changement();\">\n";
			echo "<select name='id_lieu' id='id_lieu'>\n";
			echo "<option value=''>---</option>\n";
			while($lig_lieu=mysql_fetch_object($res_lieu)) {
				echo "<option value=\"$lig_lieu->id\"";
				if($lig_lieu->id==$id_lieu) {echo " selected='selected'";}
				echo ">$lig_lieu->lieu</option>\n";
			}
			echo "</select>\n";
		}
		else {
			echo ">\n";
			while($lig_lieu=mysql_fetch_object($res_lieu)) {
				if($lig_lieu->id==$id_lieu) {echo "$lig_lieu->lieu\n";}
			}
		}
		echo "</td>\n";
		echo "</tr>\n";
	}
	//echo "</td>\n";
	//echo "</tr>\n";

	//========================
	// Nature de l'incident
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>\n";
	echo "Nature de l'incident <span style='color:red;'>(*)</span>&nbsp;:";
	echo "</td>\n";
	echo "<td style='text-align:left;'";
	if($etat_incident!='clos') {echo " colspan='2'";}
	echo ">\n";

	// Pouvoir sélectionner une nature parmi les natures déjà saisies auparavant et parmi une liste type
	// insulte,violence,refus de travail,...
	// Actuellement, la liste des propositions est uniquement recherchée dans les saisies précédentes.

	//$nature="";

	if($etat_incident!='clos') {
		echo "<input type='text' name='nature' id='nature' size='30' value=\"".$nature."\" />\n";

		$sql="SELECT DISTINCT nature FROM s_incidents WHERE nature!='' ORDER BY nature;";
		$res_nat=mysql_query($sql);
		if(mysql_num_rows($res_nat)>0) {
			echo " <a href='#' onclick=\"cacher_toutes_les_infobulles();afficher_div('div_choix_nature','y',10,-40); return false;\">Choix</a>";
			//echo " <a href='#' onclick=\"afficher_div('div_choix_nature','y',10,-40); return false;\" onmouseover=\"delais_afficher_div('div_explication_choix_nature','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onmouseout=\"cacher_div('div_explication_choix_nature')\">Choix</a>";

			$texte="<table class='boireaus' style='margin: auto;' border='1' summary=\"Choix d'une nature\">\n";
			$alt2=1;
			while($lig_nat=mysql_fetch_object($res_nat)) {
				$alt2=$alt2*(-1);
				$texte.="<tr class='lig$alt2'>\n";
				$texte.="<td><a href='#' onclick=\"document.getElementById('nature').value='$lig_nat->nature';cacher_div('div_choix_nature');return false;\">".$lig_nat->nature."</a></td>\n";
				$texte.="</tr>\n";
			}
			$texte.="</table>\n";

			$tabdiv_infobulle[]=creer_div_infobulle('div_choix_nature',"Nature de l'incident","",$texte,"",14,0,'y','y','n','n');


			echo " <a href='#' onclick=\"return false;\" onmouseover=\"delais_afficher_div('div_explication_choix_nature','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onmouseout=\"cacher_div('div_explication_choix_nature')\"><img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Choix nature' /></a>";

			$texte="Cliquez pour choisir une nature existante.<br />Ou si aucune nature n'est déjà définie, saisissez la nature d'incident de votre choix.";
			$tabdiv_infobulle[]=creer_div_infobulle('div_explication_choix_nature',"Choix nature de l'incident","",$texte,"",18,0,'y','y','n','n');

		}
	}
	else {
		echo $nature;
	}

	echo "</td>\n";
	echo "</tr>\n";
	//========================
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>\n";
	echo "Description de l'incident&nbsp;:";
	echo "</td>\n";
	echo "<td style='text-align:left;'";
	if($etat_incident!='clos') {echo " colspan='2'";}
	echo ">\n";

	if($etat_incident!='clos') {
		echo "<textarea id=\"description\" class='wrap' name=\"no_anti_inject_description\" rows='8' cols='60' onchange=\"changement()\">$description</textarea>\n";
	}
	else {
		echo nl2br($description);
	}

	echo "</td>\n";

	//========================
	if(count($ele_login)>0) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>\n";
		echo "Mesures&nbsp;:";
		echo "</td>\n";

		if($etat_incident!='clos') {
			echo "<td style='text-align:left;'";
			echo " colspan='2'";
			echo ">\n";

			$tab_mes_prise=array();
			//$sql="SELECT mesure FROM s_mesures WHERE type='prise';";
			$sql="SELECT * FROM s_mesures WHERE type='prise';";
			$res_mes=mysql_query($sql);
			if(mysql_num_rows($res_mes)>0) {
				while($lig_mes=mysql_fetch_object($res_mes)) {
					$tab_id_mes_prise[]=$lig_mes->id;
					$tab_mes_prise[]=$lig_mes->mesure;
					$tab_c_mes_prise[]=$lig_mes->commentaire;
				}
			}

			$tab_mes_demandee=array();
			//$sql="SELECT mesure FROM s_mesures WHERE type='demandee';";
			$sql="SELECT * FROM s_mesures WHERE type='demandee';";
			$res_mes=mysql_query($sql);
			if(mysql_num_rows($res_mes)>0) {
				while($lig_mes=mysql_fetch_object($res_mes)) {
					$tab_id_mes_demandee[]=$lig_mes->id;
					$tab_mes_demandee[]=$lig_mes->mesure;
					$tab_c_mes_demandee[]=$lig_mes->commentaire;
				}
			}

			if((count($tab_mes_prise)>0)||(count($tab_mes_demandee)>0)) {
				echo "<table class='boireaus' summary='Mesures' style='margin:2px;'>\n";
				echo "<tr>\n";
				echo "<th>Elève</th>\n";
				if(count($tab_mes_prise)>0) {
					echo "<th>Prises</th>\n";
				}
				if(count($tab_mes_demandee)>0) {
					echo "<th>";

					$texte="Les mesures demandées le sont par des professeurs.<br />";
					$texte.="Un compte cpe ou scolarité peut ensuite saisir la sanction correspondante s'il juge la demande appropriée.<br />";
					$texte.="<br />";
					$texte.="Il n'y a pas d'intérêt pour un CPE à cocher une de ces cases.<br />";
					$texte.="Il vaut mieux passer à la saisie en suivant le lien Tratement/sanction en haut à droite.<br />";
					$tabdiv_infobulle[]=creer_div_infobulle("div_mesures_demandees","Mesures demandées","",$texte,"",30,0,'y','y','n','n');

					echo "<a href='#' onmouseover=\"delais_afficher_div('div_mesures_demandees','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onclick=\"return false;\">";
					echo "Demandées";
					echo "</a>\n";
					echo "</th>\n";
				}
				echo "</tr>\n";


				// Boucle sur la liste des élèves
				$alt2=1;
				for($i=0;$i<count($ele_login);$i++) {
					$alt2=$alt2*(-1);
					echo "<tr class='lig$alt2'>\n";
					echo "<td>\n";
					echo "<input type='hidden' name='mesure_ele_login[$i]' value=\"".$ele_login[$i]."\" />\n";
					echo p_nom($ele_login[$i]);

					$tmp_tab=get_class_from_ele_login($ele_login[$i]);
					if(isset($tmp_tab['liste_nbsp'])) {echo "<br /><em style='font-size:x-small;'>(".$tmp_tab['liste_nbsp'].")</em>";}

					$tab_mes_eleve=array();
					//$sql="SELECT mesure FROM s_traitement_incident WHERE id_incident='$id_incident' AND login_ele='".$ele_login[$i]."';";
					$sql="SELECT id_mesure FROM s_traitement_incident WHERE id_incident='$id_incident' AND login_ele='".$ele_login[$i]."';";
					$res_mes=mysql_query($sql);
					if(mysql_num_rows($res_mes)>0) {
						while($lig_mes=mysql_fetch_object($res_mes)) {
							//$tab_mes_eleve[]=$lig_mes->mesure;
							$tab_mes_eleve[]=$lig_mes->id_mesure;
						}
					}
					echo "</td>\n";

					if(count($tab_mes_prise)>0) {
						echo "<td style='text-align:left; vertical-align:top;'>\n";
						for($loop=0;$loop<count($tab_mes_prise);$loop++) {
							//echo "<input type='checkbox' name='mesure_prise_".$i."[]' id='mesure_prise_".$i."_$loop' value=\"".$tab_mes_prise[$loop]."\" onchange='changement();' ";
							//if(in_array($tab_mes_prise[$loop],$tab_mes_eleve)) {echo "checked='checked' ";}
							echo "<input type='checkbox' name='mesure_prise_".$i."[]' id='mesure_prise_".$i."_$loop' value=\"".$tab_id_mes_prise[$loop]."\" onchange='changement();' ";
							if(in_array($tab_id_mes_prise[$loop],$tab_mes_eleve)) {echo "checked='checked' ";}
							echo "/>\n";

							echo "<label for='mesure_prise_".$i."_$loop' style='cursor:pointer;'>&nbsp;";
							echo $tab_mes_prise[$loop];
							echo "</label>";

							if($tab_c_mes_prise[$loop]!='') {
								if($i==0) {
									$tabdiv_infobulle[]=creer_div_infobulle("div_commentaire_mesures_prise_$loop",$tab_mes_prise[$loop],"",$tab_c_mes_prise[$loop],"",30,0,'y','y','n','n');
								}

								echo " <a href='#' onmouseover=\"delais_afficher_div('div_commentaire_mesures_prise_$loop','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onmouseout=\"cacher_div('div_commentaire_mesures_prise_$loop')\" onclick=\"return false;\">";
								echo "<img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Précision' />";
								echo "</a>\n";
							}

							echo "<br />\n";
						}
						echo "</td>\n";
					}

					if(count($tab_mes_demandee)>0) {
						echo "<td style='text-align:left; vertical-align:top;'>\n";
						for($loop=0;$loop<count($tab_mes_demandee);$loop++) {
							//echo "<input type='checkbox' name='mesure_demandee_".$i."[]' id='mesure_demandee_".$i."_$loop' value=\"".$tab_mes_demandee[$loop]."\" onchange='changement();' ";
							//if(in_array($tab_mes_demandee[$loop],$tab_mes_eleve)) {echo "checked='checked' ";}
							echo "<input type='checkbox' name='mesure_demandee_".$i."[]' id='mesure_demandee_".$i."_$loop' value=\"".$tab_id_mes_demandee[$loop]."\" onchange='changement();' ";
							if(in_array($tab_id_mes_demandee[$loop],$tab_mes_eleve)) {echo "checked='checked' ";}
							echo "/>\n";

							echo "<label for='mesure_demandee_".$i."_$loop' style='cursor:pointer;'>&nbsp;";
							echo $tab_mes_demandee[$loop];
							echo "</label>";

							if($tab_c_mes_demandee[$loop]!='') {
								if($i==0) {
									$tabdiv_infobulle[]=creer_div_infobulle("div_commentaire_mesures_demandee_$loop",$tab_mes_demandee[$loop],"",$tab_c_mes_demandee[$loop],"",30,0,'y','y','n','n');
								}

								echo " <a href='#' onmouseover=\"delais_afficher_div('div_commentaire_mesures_demandee_$loop','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onmouseout=\"cacher_div('div_commentaire_mesures_demandee_$loop')\" onclick=\"return false;\">";
								echo "<img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Précision' />";
								echo "</a>\n";
							}

							echo "<br />\n";
						}
						echo "</td>\n";
					}
					echo "</tr>\n";
				}

				echo "</table>\n";
			}
			else {
				echo "<p>Aucun type de mesure n'est défini.</p>\n";
			}

			echo "</td>\n";
		}
		else {
			echo "<td style='text-align:left;'";
			echo ">\n";

			// MESURES A AFFICHER
			//echo "A FAIRE...";
			/*
			$texte="";

			$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident' AND sti.id_mesure=s.id AND s.type='prise' ORDER BY login_ele";
			//$texte.="<br />$sql";
			$res_t_incident=mysql_query($sql);

			$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident' AND sti.id_mesure=s.id AND s.type='demandee' ORDER BY login_ele";
			//$texte.="<br />$sql";
			$res_t_incident2=mysql_query($sql);

			if((mysql_num_rows($res_t_incident)>0)||
				(mysql_num_rows($res_t_incident2)>0)) {
				$texte.="<br /><table class='boireaus' summary='Mesures' style='margin:1px;'>";
			}

			if(mysql_num_rows($res_t_incident)>0) {
				$texte.="<tr class='lig-1'>";
				$texte.="<td style='font-size:x-small; vertical-align:top;' rowspan='".mysql_num_rows($res_t_incident)."'>";
				if(mysql_num_rows($res_t_incident)==1) {
					$texte.="Mesure prise&nbsp;:";
				}
				else {
					$texte.="Mesures prises&nbsp;:";
				}
				$texte.="</td>";
				//$texte.="<td>";
				$cpt_tmp=0;
				while($lig_t_incident=mysql_fetch_object($res_t_incident)) {
					if($cpt_tmp>0) {$texte.="<tr class='lig-1'>\n";}
					$texte.="<td>";
					$texte.=p_nom($lig_t_incident->login_ele);
					$texte.="</td>\n";
					$texte.="<td>";
					$texte.="$lig_t_incident->mesure";
					$texte.="</td>\n";
					$texte.="</tr>\n";
					$cpt_tmp++;
				}
				//$texte.="</td>\n";
				//$texte.="</tr>\n";
			}

			//$possibilite_prof_clore_incident='y';
			if(mysql_num_rows($res_t_incident2)>0) {
				if($_SESSION['statut']=='professeur') {$possibilite_prof_clore_incident='n';}
				$texte.="<tr class='lig1'>";
				//$texte.="<td style='font-size:x-small; vertical-align:top;'>";
				$texte.="<td style='font-size:x-small; vertical-align:top;' rowspan='".mysql_num_rows($res_t_incident)."'>";
				if(mysql_num_rows($res_t_incident2)==1) {
					$texte.="Mesure demandée&nbsp;:";
				}
				else {
					$texte.="Mesures demandées&nbsp;:";
				}
				$texte.="</td>";
				//$texte.="<td>";
				$cpt_tmp=0;
				while($lig_t_incident=mysql_fetch_object($res_t_incident2)) {
					if($cpt_tmp>0) {$texte.="<tr class='lig1'>\n";}
					$texte.="<td>";
					$texte.=p_nom($lig_t_incident->login_ele);
					$texte.="</td>\n";
					$texte.="<td>";
					$texte.="$lig_t_incident->mesure";
					$texte.="</td>\n";
					$texte.="</tr>\n";
					$cpt_tmp++;
				}
				//$texte.="</td>\n";
				//$texte.="</tr>\n";
			}

			if((mysql_num_rows($res_t_incident)>0)||
				(mysql_num_rows($res_t_incident2)>0)) {
				$texte.="</table>";
			}
			*/
			$texte=affiche_mesures_incident($id_incident);
			echo $texte;

			echo "</td>\n";
		}

		echo "</tr>\n";
	}
	//========================
	echo "</table>\n";

	if($etat_incident!='clos') {
		echo "<input type='hidden' name='is_posted' value='y' />\n";
		echo "<input type='hidden' name='step' value='$step' />\n";
		echo "<p style='text-align:center;'><input type='checkbox' name='clore_incident' id='clore_incident' value='y' />\n";
		echo "<label for='clore_incident' style='cursor:pointer;'>&nbsp;Clore l'incident.</label>\n";
		//echo "<br />";
		echo "<em style='font-size:x-small;'>(sous réserve de ne pas <strong>Demander</strong> de mesure)</em>\n";
		echo "</p>\n";
		//echo "<p style='text-align:center;'><input type='submit' name='enregistrer2' value='Enregistrer' onclick='verif_details_incident();' /></p>\n";

		//echo "<p style='text-align:center;'><input type='submit' name='enregistrer2' value='Enregistrer' /></p>\n";
		echo "<p style='text-align:center;'><input type='button' name='enregistrer2' value='Enregistrer' onclick='verif_details_incident();' /></p>\n";
		echo "<noscript><input type='submit' name='enregistrer2' value='Enregistrer vraiment' /></noscript>\n";

		//echo "<p style='text-align:center;'><input type='submit' name='enregistrer2' value='Enregistrer' onsubmit='verif_details_incident();' /></p>\n";


		echo "<p><em>NOTE&nbsp;</em> <span style='color:red;'>(*)</span> Il est impératif de saisir une Nature d'incident pour des questions de facilité de traitement par la suite.</p>\n";
	}
	echo "</blockquote>\n";

	if($etat_incident!='clos') {

		echo "<script type='text/javascript'>
	function verif_details_incident() {
		if(document.getElementById('nature').value=='') {
			alert(\"La nature de l'incident doit être précisé.\");
			return false;
		}
		else {
			if(document.getElementById('display_heure').value=='') {
				alert(\"L'heure de l'incident (non vide) doit être précisée. \\nEn cas de doute sur l'heure, mettre un '?'.\");
				return false;
			}
			else {
				document.formulaire.submit();
			}
		}
	}
</script>\n";

		echo "</form>\n";
	}


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

}

/*
}
else {
	echo "<p>Vous avez choisi $ele_login</p>\n";
}
*/

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>