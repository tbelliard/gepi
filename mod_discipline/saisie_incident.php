<?php

/*
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

// Paramètre pour autoriser ou non une zone de saisie de commentaires pour un incident
$autorise_commentaires_mod_disc = getSettingValue("autorise_commentaires_mod_disc");

function choix_heure($champ_heure,$div_choix_heure) {
	global $tabdiv_infobulle;

	$sql="SELECT * FROM edt_creneaux ORDER BY heuredebut_definie_periode;";
	$res_abs_cren=mysql_query($sql);
	if(mysql_num_rows($res_abs_cren)>0) {
		echo " <a href='#' onclick=\"afficher_div('$div_choix_heure','y',10,-40); return false;\">Choix</a>";

		$texte="<table class='boireaus' style='margin: auto;border:1px;'><caption class='invisible'>Choix d'une heure</caption>\n";
		while($lig_ac=mysql_fetch_object($res_abs_cren)) {
			$td_style="";
			$tmp_bgcolor="";
			if($lig_ac->type_creneaux=='cours') {
				$td_style=" style='background-color: lightgreen;'";
				$tmp_bgcolor="lightgreen";
			}
			elseif($lig_ac->type_creneaux=='pause') {
				$td_style=" style='background-color: lightgrey;'";
				$tmp_bgcolor="lightgrey";
			}
			elseif($lig_ac->type_creneaux=='repas') {
				$td_style=" style='background-color: lightgrey;'";
				$tmp_bgcolor="lightgrey";
			}

			$texte.="<tr class='white_hover'$td_style onmouseover=\"this.style.backgroundColor='white'\" onmouseout=\"this.style.backgroundColor='$tmp_bgcolor'\">\n";
			$texte.="<td><a href='#' onclick=\"document.getElementById('$champ_heure').value='$lig_ac->nom_definie_periode';cacher_div('$div_choix_heure');changement();return false;\">".$lig_ac->nom_definie_periode."</a></td>\n";
			$texte.="<td><a href='#' onclick=\"document.getElementById('$champ_heure').value='$lig_ac->nom_definie_periode';cacher_div('$div_choix_heure');changement();return false;\" style='text-decoration: none; color: black;'>".$lig_ac->heuredebut_definie_periode."</a></td>\n";
			$texte.="<td><a href='#' onclick=\"document.getElementById('$champ_heure').value='$lig_ac->nom_definie_periode';cacher_div('$div_choix_heure');changement();return false;\" style='text-decoration: none; color: black;'>".$lig_ac->heurefin_definie_periode."</a></td>\n";
			$texte.="</tr>\n";
		}
		$texte.="</table>\n";

		$tabdiv_infobulle[]=creer_div_infobulle("$div_choix_heure","Choix d'une heure","",$texte,"",12,0,'y','y','n','n');
	}
}

function recherche_ele($rech_nom,$page) {
	$rech_nom=preg_replace("/[^A-Za-zÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõ¨ûüùúýÿ¸]/","",$rech_nom);

	$sql="SELECT * FROM eleves WHERE nom LIKE '%$rech_nom%';";
	$res_ele=mysql_query($sql);

	$nb_ele=mysql_num_rows($res_ele);

	if($nb_ele==0){
		// On ne devrait pas arriver là.
		echo "<p>Aucun nom d'élève ne contient la chaine $rech_nom.</p>\n";
	}
	else{
		echo "<p>La recherche a retourné <strong>$nb_ele</strong> réponse";
		if($nb_ele>1) {echo "s";}
		echo ":</p>\n";
		echo "<table style='border:1px;' class='boireaus'><caption class='invisible'>Liste des élèves</caption>\n";
		echo "<tr>\n";
		//echo "<th>Elève</th>\n";
		echo "<th>Sélectionner</th>\n";
		echo "<th>Elève</th>\n";
		echo "<th>Classe(s)</th>\n";
		echo "</tr>\n";
		$alt=1;
		$cpt1=0;
		while($lig_ele=mysql_fetch_object($res_ele)) {
			$ele_login=$lig_ele->login;
			$ele_nom=$lig_ele->nom;
			$ele_prenom=$lig_ele->prenom;
			//echo "<strong>$ele_nom $ele_prenom</strong>";
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			echo "<input type='checkbox' name='ele_login[]' id='ele_login_$cpt1' value=\"$ele_login\" />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='ele_login_$cpt1' style='cursor:pointer;'>".htmlspecialchars("$ele_nom $ele_prenom")."</label>";

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
					echo htmlspecialchars($lig_clas->classe);
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
	$rech_nom=preg_replace("/[^A-Za-zÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõ¨ûüùúýÿ¸]/","",$rech_nom);

	$sql="SELECT * FROM utilisateurs WHERE (nom LIKE '%$rech_nom%' AND statut!='responsable');";
	$res_utilisateur=mysql_query($sql);

	$nb_utilisateur=mysql_num_rows($res_utilisateur);

	if($nb_utilisateur==0){
		// On ne devrait pas arriver là.
		echo "<p>Aucun nom d'utilisateur ne contient la chaine $rech_nom.</p>\n";
	}
	else{
		echo "<p>La recherche a retourné <strong>$nb_utilisateur</strong> réponse";
		if($nb_utilisateur>1) {echo "s";}
		echo ":</p>\n";
		echo "<table style='border:1px;' class='boireaus'><caption class='invisible'>Liste des  utilisateurs</caption>\n";
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
			//echo "<strong>$utilisateur_nom $utilisateur_prenom</strong>";
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			echo "<input type='checkbox' name='u_login[]' id='u_login_$cpt1' value=\"$utilisateur_login\" />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='u_login_$cpt1' style='cursor:pointer;'>".htmlspecialchars("$utilisateur_nom $utilisateur_prenom")."</label>";
            echo "</td>\n";

            echo "</tr>\n";
			$cpt1++;
		}
		echo "</table>\n";
	}
}


$id_incident=isset($_POST['id_incident']) ? $_POST['id_incident'] : (isset($_GET['id_incident']) ? $_GET['id_incident'] : NULL);

$return_url=isset($_POST['return_url']) ? $_POST['return_url'] : (isset($_GET['return_url']) ? $_GET['return_url'] : NULL);

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

$change_declarant=isset($_POST['change_prof']) ? $_POST['change_prof'] : NULL;

//$mesure_prise=isset($_POST['mesure_prise']) ? $_POST['mesure_prise'] : NULL;
//$mesure_demandee=isset($_POST['mesure_demandee']) ? $_POST['mesure_demandee'] : NULL;
$mesure_ele_login=isset($_POST['mesure_ele_login']) ? $_POST['mesure_ele_login'] : NULL;

$clore_incident=isset($_POST['clore_incident']) ? $_POST['clore_incident'] : NULL;

//debug_var();

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

// on change le déclarant si demandé
if (($change_declarant=='Changer') && isset($_POST['choixProf']) && ($_POST['choixProf']!= '0') && ($etat_incident!='clos')) {
    check_token();
    $sql="UPDATE  s_incidents SET declarant='".$_POST['choixProf']."' WHERE id_incident='".$_POST['id_incident']."'";
    $test=mysql_query($sql);
    $msg .= "Déclarant modifié";
    // On recherche le primo-déclarant
    $resPrimo=mysql_query("SELECT primo_declarant FROM s_incidents WHERE id_incident='".$id_incident."'");
    if (mysql_fetch_object($resPrimo)->primo_declarant == '') {
        $sql="UPDATE  s_incidents SET primo_declarant ='".$_SESSION['login']."' WHERE id_incident='".$_POST['id_incident']."'";
        // echo $sql;
        $test=mysql_query($sql);
        $msg .= " - Primo-déclarant modifié";
    }
}

if($etat_incident!='clos') {
	//echo "etat_incident=$etat_incident<br />";
	if((isset($_POST['suppr_ele_incident']))&&(isset($id_incident))) {
		//echo "suppr_ele_incident $id_incident<br />";

		check_token();
		$suppr_ele_incident=$_POST['suppr_ele_incident'];
		for($i=0;$i<count($suppr_ele_incident);$i++) {
			$sql="SELECT 1=1 FROM s_sanctions WHERE login='$suppr_ele_incident[$i]' AND id_incident='$id_incident';";
			$test_sanction=mysql_query($sql);
			if(mysql_num_rows($test_sanction)>0) {
				$msg.="ERREUR: Il n'est pas possible de supprimer ".$suppr_ele_incident[$i]." pour l'incident $id_incident car une ou des sanctions sont prises. Vous devez d'abord supprimer les sanctions associées.<br />\n";
			}
			else {
				$sql="DELETE FROM s_traitement_incident WHERE login_ele='$suppr_ele_incident[$i]' AND id_incident='$id_incident';";
				//echo "$sql<br />";
				$menage=mysql_query($sql);
				if(!$menage) {
					$msg.="ERREUR lors de la suppression des traitements associés à ".$suppr_ele_incident[$i]." pour l'incident $id_incident. Les mesures demandées ou prises posent un problème.<br />\n";
				}
				else {
					$sql="DELETE FROM s_protagonistes WHERE login='$suppr_ele_incident[$i]' AND id_incident='$id_incident';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de la suppression de ".$suppr_ele_incident[$i]." pour l'incident $id_incident<br />\n";
					}
				}
			}
		}
	}
	elseif(isset($_POST['enregistrer_qualite'])) {
		//echo "enregistrer_qualite<br />";

		check_token();

		$nb_protagonistes=isset($_POST['nb_protagonistes']) ? $_POST['nb_protagonistes'] : NULL;
		if(isset($nb_protagonistes)) {
			//for($i=0;$i<count($ele_login);$i++) {
			for($i=0;$i<$nb_protagonistes;$i++) {
				if(isset($ele_login[$i])) {
					$sql="SELECT 1=1 FROM s_protagonistes WHERE id_incident='$id_incident' AND login='".$ele_login[$i]."';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)==0) {
						$sql="INSERT INTO s_protagonistes SET id_incident='$id_incident', login='".$ele_login[$i]."', statut='eleve', qualite='".addslashes(preg_replace("/&#039;/","'",html_entity_decode($qualite[$i])))."';";
						//echo "$sql<br />\n";
						$res=mysql_query($sql);
						if(!$res) {
							$msg.="ERREUR lors de l'enregistrement de ".$ele_login[$i]."<br />\n";
						}
					}
					else {
						//$sql="UPDATE s_protagonistes SET qualite='$qualite[$i]' WHERE id_incident='$id_incident' AND login='".$ele_login[$i]."' AND statut='eleve';";
						$sql="UPDATE s_protagonistes SET qualite='".addslashes(preg_replace("/&#039;/","'",html_entity_decode($qualite[$i])))."' WHERE id_incident='$id_incident' AND login='".$ele_login[$i]."' AND statut='eleve';";
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

						$sql="INSERT INTO s_protagonistes SET id_incident='$id_incident', login='".$u_login[$i]."', statut='$tmp_statut', qualite='".addslashes(preg_replace("/&#039;/","'",html_entity_decode($qualite[$i])))."';";
						//echo "$sql<br />\n";
						$res=mysql_query($sql);
						if(!$res) {
							$msg.="ERREUR lors de l'enregistrement de ".$u_login[$i]."<br />\n";
						}
					}
					else {
						$sql="UPDATE s_protagonistes SET qualite='".addslashes(preg_replace("/&#039;/","'",html_entity_decode($qualite[$i])))."' WHERE id_incident='$id_incident' AND login='".$u_login[$i]."';";
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
		//echo "is_posted<br />";

		if((!isset($_POST['recherche_eleve']))&&(!isset($_POST['recherche_utilisateur']))) {
			//echo "Ce n'est pas une recherche_eleve ni recherche_utilisateur<br />";

			if(!isset($id_incident)) {
				//echo "Nouvel incident, \$id_incident n'est pas encore affecté<br />";
				check_token();

				if(!isset($display_date)) {
					$annee = strftime("%Y");
					$mois = strftime("%m");
					$jour = strftime("%d");
					//$display_date = $jour."/".$mois."/".$annee;
				}
				else {
					
					$jour =  mb_substr($display_date,0,2);
					$mois =  mb_substr($display_date,3,2);
					$annee = mb_substr($display_date,6,4);
				}
	
				if(!checkdate($mois,$jour,$annee)) {
					$annee = strftime("%Y");
					$mois = strftime("%m");
					$jour = strftime("%d");
	
					$msg.="La date proposée n'était pas valide. Elle a été remplacée par la date du jour courant.";
				}
	
				if(!isset($display_heure)) {
					$display_heure="";
				}
	
				if(!isset($nature)) {
					$nature="";
				}
	
				if (isset($NON_PROTECT["description"])){
					$description=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["description"]));
	
					// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
					$description=preg_replace('/(\\\r\\\n)+/',"\r\n",$description);
					$description=preg_replace('/(\\\r)+/',"\r",$description);
					$description=preg_replace('/(\\\n)+/',"\n",$description);

				}
				else {
					$description="";
				}
				
				// Ajout Eric zone de commentaire
				if (isset($NON_PROTECT["commentaire"])){
					$commentaire=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["commentaire"]));
	
					// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
					$commentaire=preg_replace('/(\\\r\\\n)+/',"\r\n",$commentaire);
					$commentaire=preg_replace('/(\\\r)+/',"\r",$commentaire);
					$commentaire=preg_replace('/(\\\n)+/',"\n",$commentaire);
	
				} else {
				    $commentaire="";
				}
				// Fin ajout Eric
	
				if(!isset($id_lieu)) {
					$id_lieu="";
				}
	
				// ALTER TABLE s_incidents ADD message_id VARCHAR(50) NOT NULL;
				//$message_id=strftime("%Y%m%d%H%M%S",time()).".".mb_substr(md5(microtime()),0,6);
				// Pour ne pas spammer tant que la nature n'est pas saisie
				if($nature!='') {
					$message_id=$id_incident.".".strftime("%Y%m%d%H%M%S",time()).".".mb_substr(md5(microtime()),0,6);
				}
				else {
					$message_id="";
				}
	
				$sql="INSERT INTO s_incidents SET declarant='".$_SESSION['login']."',
													date='$annee-$mois-$jour',
													heure='$display_heure',
													nature='".traitement_magic_quotes(corriger_caracteres($nature))."',
													description='".$description."',
													id_lieu='$id_lieu',
													message_id='$message_id';";
				//echo "$sql<br />\n";
				$res=mysql_query($sql);
				if(!$res) {
					$msg.="ERREUR lors de l'enregistrement de l'incident&nbsp;:".$sql."<br />\n";
				}
				else {
					$id_incident=mysql_insert_id();
					$msg.="Enregistrement de l'incident n°".$id_incident." effectué.<br />\n";
				}
	
				$texte_mail="Saisie par ".civ_nom_prenom($_SESSION['login'])." d'un incident (n°$id_incident) survenu le $jour/$mois/$annee à $display_heure:\n";
				$texte_mail.="Nature: $nature\nDescription: $description\n";
			}
			else {
				//echo "Incident n°$id_incident<br />";

				//check_token();

				$temoin_modif="n";
				$sql="UPDATE s_incidents SET ";
				if(isset($display_date)) {
					$jour =  mb_substr($display_date,0,2);
					$mois =  mb_substr($display_date,3,2);
					$annee = mb_substr($display_date,6,4);
	
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
					//on vérifie si une catégorie est définie pour cette nature
					$sql2="SELECT id_categorie FROM s_incidents WHERE nature='".traitement_magic_quotes(corriger_caracteres($nature))."' GROUP BY id_categorie";
					$res2=mysql_query($sql2);
					//if($res2) {
					if(mysql_num_rows($res2)>0) {
						while ($lign_cat=mysql_fetch_object($res2)){
							$tab_res[]=$lign_cat->id_categorie;
						}
						//il ne devrait pas y avoir plus d'un enregistrement; dans le cas contraire on envoi un message
						if (count($tab_res)>1) {$msg.="Il y a plusieurs catégories affectées à cette nature. La première est retenue pour cet incident. Vous devriez mettre à jour vos catégories d'incidents.<br />";}
						//on affecte la categorie a l'incident ou on met à null dans le cas contraire;
						if ($tab_res['0']==null) {$sql.="id_categorie=NULL ,";}
						else {$sql.="id_categorie='".$tab_res['0']."' ,";}
					} 
					$temoin_modif="y";
				}
	
				if (isset($NON_PROTECT["description"])){
					$description=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["description"]));
	
					// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
					$description=preg_replace('/(\\\r\\\n)+/',"\r\n",$description);
					$description=preg_replace('/(\\\r)+/',"\r",$description);
					$description=preg_replace('/(\\\n)+/',"\n",$description);

					$sql.="description='".$description."' ,";
					$temoin_modif="y";
				}
				
				// Ajout Eric zone de commentaire
				if (isset($NON_PROTECT["commentaire"])){
					$commentaire=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["commentaire"]));
	
					// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
					$commentaire=preg_replace('/(\\\r\\\n)+/',"\r\n",$commentaire);
					$commentaire=preg_replace('/(\\\r)+/',"\r",$commentaire);
					$commentaire=preg_replace('/(\\\n)+/',"\n",$commentaire);

					$sql.="commentaire='".$commentaire."' ,";
					$temoin_modif="y";
				}
				// Fin ajout Eric
				
				if(isset($id_lieu)) {
					$sql.="id_lieu='$id_lieu' ,";
					$temoin_modif="y";
				}
	
				// Pour faire sauter le ", " en fin de $sql:
				$sql=mb_substr($sql,0,mb_strlen($sql)-2);
	
				$sql.=" WHERE id_incident='$id_incident';";
	
				if($temoin_modif=="y") {
					check_token();

					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de la mise à jour de l'incident ".$id_incident."<br />\n";
					}
					else {
						$msg.="Mise à jour de l'incident n°".$id_incident." effectuée.<br />\n";
					}
				}
				
				$sql_declarant="SELECT declarant FROM s_incidents WHERE id_incident='$id_incident';";
		        $res_declarant=mysql_query($sql_declarant);
		        if(mysql_num_rows($res_declarant)>0) {
			        $lig_decclarant=mysql_fetch_object($res_declarant);
			        $texte_mail= "Déclaration initiale de l'incident par ".u_p_nom($lig_decclarant->declarant)."\n";
		        }
	
				$texte_mail.="Mise à jour par ".civ_nom_prenom($_SESSION['login'])." d'un incident (n°$id_incident)";
				if(isset($display_heure)) {
					$texte_mail.=" survenu le $jour/$mois/$annee à/en $display_heure:\n";
				}
				if(isset($nature)) {
					$texte_mail.="\nNature: $nature\nDescription: $description\n";
				}
			}
	
	
			if(isset($id_incident)) {
				//echo "Ce n'est pas une recherche_eleve ni recherche_utilisateur avec $id_incident (2)<br />";

				for($i=0;$i<count($ele_login);$i++) {
					$sql="SELECT 1=1 FROM s_protagonistes WHERE id_incident='$id_incident' AND login='".$ele_login[$i]."';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)==0) {
						check_token();
						$sql="INSERT INTO s_protagonistes SET id_incident='$id_incident', login='".$ele_login[$i]."', statut='eleve';";
						//echo "$sql<br />\n";
						$res=mysql_query($sql);
						if(!$res) {
							$msg.="ERREUR lors de l'enregistrement de ".$ele_login[$i]."<br />\n";
						}
					}

				}

				for($i=0;$i<count($u_login);$i++) {
					$sql="SELECT 1=1 FROM s_protagonistes WHERE id_incident='$id_incident' AND login='".$u_login[$i]."';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)==0) {
						check_token();

						$tmp_statut="";
						$sql="SELECT statut FROM utilisateurs WHERE login='".$u_login[$i]."'";
						$res_statut=mysql_query($sql);
						if(mysql_num_rows($res_statut)>0) {
							$lig_statut=mysql_fetch_object($res_statut);
							$tmp_statut=$lig_statut->statut;
	
							$sql="INSERT INTO s_protagonistes SET id_incident='$id_incident', login='".$u_login[$i]."', statut='$tmp_statut', qualite='".addslashes(preg_replace("/&#039;/","'",html_entity_decode($qualite[$i])))."';";
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
					//echo "\$mesure_ele_login=$mesure_ele_login<br />";
					check_token();

					// Recherche des mesures déjà enregistrées:
					for($i=0;$i<count($mesure_ele_login);$i++) {

						
						$tab_mes_enregistree=array();
						$sql="SELECT id_mesure FROM s_traitement_incident WHERE id_incident='$id_incident' AND login_ele='".$mesure_ele_login[$i]."';";
				/*
echo "<pre>
envoi_mail($subject, 
$texte_mail, 
$destinataires, 
$headers);
</pre>";
*/
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

						unset($suppr_doc_joint);
						$suppr_doc_joint=isset($_POST['suppr_doc_joint_'.$i]) ? $_POST['suppr_doc_joint_'.$i] : array();
						for($loop=0;$loop<count($suppr_doc_joint);$loop++) {
							if((preg_match("/\.\./",$suppr_doc_joint[$loop]))||(preg_match("#/#",$suppr_doc_joint[$loop]))) {
								$msg.="Nom de fichier ".$suppr_doc_joint[$loop]." invalide<br />";
							}
							else {
								$fichier_courant="../$dossier_documents_discipline/incident_".$id_incident."/mesures/".$mesure_ele_login[$i]."/".$suppr_doc_joint[$loop];
								if(!unlink($fichier_courant)) {
									$msg.="Erreur lors de la suppression de $fichier_courant<br />";
								}
							}
						}

						unset($document_joint);
						$document_joint=isset($_FILES["document_joint_".$i]) ? $_FILES["document_joint_".$i] : NULL;
						if((isset($document_joint['tmp_name']))&&($document_joint['tmp_name']!="")) {
							/*
							foreach($document_joint as $key => $value) {
								echo "\$document_joint[$key]=$value<br />";
							}
							// Image PNM
							$document_joint[name]=
							$document_joint[type]=image/x-portable-anymap
							$document_joint[tmp_name]=/tmp/php0zquJ4
							$document_joint[error]=0
							$document_joint[size]=69472
							*/

							//$msg.="\$document_joint['tmp_name']=".$document_joint['tmp_name']."<br />";
							if(!is_uploaded_file($document_joint['tmp_name'])) {
								$msg.="L'upload du fichier a échoué.<br />\n";
							}
							else{
								if(!file_exists($document_joint['tmp_name'])) {
									if($document_joint['name']!="") {
										$extension_tmp=mb_substr(strrchr($document_joint['name'],'.'),1);
										if(!in_array($extension, $AllowedFilesExtensions)) {
											$msg.="Vous avez proposé : ".$document_joint['name']."<br />L'extension $extension n'est pas autorisée.<br />\n";
										}
										else {
											$msg.="Le fichier aurait été uploadé... mais ne serait pas présent/conservé.<br />\n";
										}
									}
									else {
										$msg.="Le fichier aurait été uploadé... mais ne serait pas présent/conservé.<br />\n";
										$msg.="Il se peut que l'extension du fichier proposé ne soit pas autorisée.<br />\n";
										$msg.="Les types autorisés sont ".array_to_chaine($AllowedFilesExtensions)."<br />";
									}
								}
						/*
echo "<pre>
envoi_mail($subject, 
$texte_mail, 
$destinataires, 
$headers);
</pre>";
*/		else {
									$source_file=$document_joint['tmp_name'];
									$dossier_courant="../$dossier_documents_discipline/incident_".$id_incident."/mesures/".$mesure_ele_login[$i];
									if(!file_exists($dossier_courant)) {
										mkdir($dossier_courant, 0770, true);
									}

									if(strstr($document_joint['name'],".")) {
										$extension_fichier=substr(strrchr($document_joint['name'],'.'),1);
										$nom_fichier_sans_extension=preg_replace("/.$extension_fichier$/","",$document_joint['name']);

										$dest_file=$dossier_courant."/".remplace_accents($nom_fichier_sans_extension, "all").".".$extension_fichier;
									}
									else {
										// Pas d'extension dans le nom de fichier fourni
										$dest_file=$dossier_courant."/".remplace_accents($document_joint['name'], "all");
									}

									$res_copy=copy("$source_file" , "$dest_file");
									if(!$res_copy) {$msg.="Echec de la mise en place du fichier ".$document_joint['name']."<br />";}
								}
							}
						}

						if(count($mesure_demandee)>0) {
							if (isset($NON_PROTECT["travail_pour_mesure_demandee_".$i])){
								$texte_travail=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["travail_pour_mesure_demandee_".$i]));
				
								// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
								$texte_travail=preg_replace('/(\\\r\\\n)+/',"\r\n",$texte_travail);
								$texte_travail=preg_replace('/(\\\r)+/',"\r",$texte_travail);
								$texte_travail=preg_replace('/(\\\n)+/',"\n",$texte_travail);
	
								if($texte_travail=="") {
									$sql="DELETE FROM s_travail_mesure WHERE id_incident='$id_incident' AND login_ele='".$mesure_ele_login[$i]."';";
									$res_del=mysql_query($sql);
								}
								else {
									$sql="SELECT * FROM s_travail_mesure WHERE id_incident='$id_incident' AND login_ele='".$mesure_ele_login[$i]."';";
									$res_mes=mysql_query($sql);
									if(mysql_num_rows($res_mes)>0) {
										$sql="UPDATE s_travail_mesure SET travail='".$texte_travail."' WHERE id_incident='$id_incident' AND login_ele='".$mesure_ele_login[$i]."';";
										$update=mysql_query($sql);
									}
									else {
										$sql="INSERT INTO s_travail_mesure SET travail='".$texte_travail."', id_incident='$id_incident', login_ele='".$mesure_ele_login[$i]."';";
										$insert=mysql_query($sql);
									}
								}
							}
						}
						
						//$tab_mesure_possible=array();
						//$sql="SELECT mesure FROM s_mesures;";
						$sql="SELECT * FROM s_mesures;";
						$res_mes=mysql_query($sql);
						if(mysql_num_rows($res_mes)>0) {
							while($lig_mes=mysql_fetch_object($res_mes)) {
								//$tab_mesure_possible[]=$lig_mes->mesure;
	
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
					//echo "\$clore_incident=$clore_incident<br />";
					check_token();

					$sql="UPDATE s_incidents SET etat='clos' WHERE id_incident='$id_incident';";
					$update=mysql_query($sql);
					if(!$update) {
						$msg.="ERREUR lors de la clôture de l'incident n°$id_incident.<br />\n";
					}
					else {
						$msg.="Clôture de l'incident n°$id_incident.<br />\n";
					}
				}
	
				$envoi_mail_actif=getSettingValue('envoi_mail_actif');
				if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
					$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
				}
	
				if($envoi_mail_actif=='y') {
					//echo "\$envoi_mail_actif=$envoi_mail_actif<br />";

					//echo "plip";
					check_token();

					$temoin_envoyer_mail="y";
					//echo "nature=$nature<br />";
					if((!isset($nature))||($nature=='')) {
						$sql="SELECT * FROM s_incidents WHERE id_incident='$id_incident' AND (nature!='' OR description!='');";
						//echo "$sql<br />";
						$res_test=mysql_query($sql);
						if(mysql_num_rows($res_test)==0) {
							$temoin_envoyer_mail="n";
						}
					}
	
					if($temoin_envoyer_mail=="y") {
	
						// Recuperation du message_id pour les fils de discussion dans les mails
						$sql_mi="SELECT message_id FROM s_incidents WHERE id_incident='$id_incident';";
						$res_mi=mysql_query($sql_mi);
						$lig_mi=mysql_fetch_object($res_mi);
						if($lig_mi->message_id=="") {
							$message_id=$id_incident.".".strftime("%Y%m%d%H%M%S",time()).".".mb_substr(md5(microtime()),0,6);
							$sql="UPDATE s_incidents SET message_id='$message_id' WHERE id_incident='$id_incident';";
							$update=mysql_query($sql);
						}
						else {
							$references_mail=$lig_mi->message_id;
						}
	
						$tab_alerte_classe=array();
	
						$info_classe_prot="";
						$liste_protagonistes_responsables="";
						$sql="SELECT * FROM s_protagonistes WHERE id_incident='$id_incident' ORDER BY login;";
						//echo "$sql<br />";
						$res_prot=mysql_query($sql);
						if(mysql_num_rows($res_prot)>0) {
							$texte_mail.="\n";
							$texte_mail.="Protagonistes de l'incident: \n";
							while($lig_prot=mysql_fetch_object($res_prot)) {
								if($lig_prot->statut=='eleve') {
									$classe_elv = get_noms_classes_from_ele_login($lig_prot->login);
									if ($classe_elv[0] != "") {$classe_elv[0]="[".$classe_elv[0]."]";};
									$texte_mail.=get_nom_prenom_eleve($lig_prot->login)." $classe_elv[0] ($lig_prot->qualite)\n";
								}
								else {
									$texte_mail.=civ_nom_prenom($lig_prot->login)." ($lig_prot->statut) ($lig_prot->qualite)\n";
								}
	
								if(mb_strtolower($lig_prot->qualite)=='responsable') {
									$sql="SELECT DISTINCT c.classe FROM classes c,j_eleves_classes jec WHERE jec.id_classe=c.id AND jec.login='$lig_prot->login' ORDER BY jec.periode DESC limit 1;";
									//echo "$sql<br />";
									$res_prot_classe=mysql_query($sql);
									if(mysql_num_rows($res_prot)>0) {
										$lig_prot_classe=mysql_fetch_object($res_prot_classe);
										$info_classe_prot="[$lig_prot_classe->classe]";
								
										if(getSettingValue('mod_disc_sujet_mail_sans_nom_eleve')!="n") {
											if($liste_protagonistes_responsables!="") {$liste_protagonistes_responsables.=", ";}
											$liste_protagonistes_responsables.=$lig_prot->login;
											//echo "\$liste_protagonistes_responsables=$liste_protagonistes_responsables<br />";
										}
									}
								}
	
								$sql="SELECT * FROM s_mesures sm, s_traitement_incident sti WHERE sti.id_incident='$id_incident' AND sti.login_ele='".$lig_prot->login."' AND sti.id_mesure=sm.id ORDER BY type, mesure;";
								//echo "$sql<br />";
								$res_mes=mysql_query($sql);
								if(mysql_num_rows($res_mes)>0) {
									while($lig_mes=mysql_fetch_object($res_mes)) {
										$texte_mail.="   $lig_mes->mesure ($lig_mes->type)\n";
									}
									$texte_mail.="\n";
								}
		
								// On va avoir des personnes alertees inutilement pour les élèves qui ont changé de classe.
								// NON
								$sql="SELECT DISTINCT id_classe FROM j_eleves_classes WHERE login='$lig_prot->login' ORDER BY periode DESC LIMIT 1;";
								$res_clas_prot=mysql_query($sql);
								if(mysql_num_rows($res_clas_prot)>0) {
									$lig_clas_prot=mysql_fetch_object($res_clas_prot);
									if(!in_array($lig_clas_prot->id_classe,$tab_alerte_classe)) {
										$tab_alerte_classe[]=$lig_clas_prot->id_classe;
									}
								}
							}
						}
	
						//echo "\$texte_mail=$texte_mail<br />";
	
						if(count($tab_alerte_classe)>0) {
							$destinataires=get_destinataires_mail_alerte_discipline($tab_alerte_classe);
							// La liste des destinataires, admin inclus doivent être définis dans "Définition des destinataires d'alertes"
							//if($destinataires=="") {
							//	$destinataires=getSettingValue("gepiAdminAdress");
							//}
	
							if($destinataires!="") {
								$texte_mail=$texte_mail."\n\n"."Message: ".preg_replace('#<br />#',"\n",$msg);
						
								$subject = "[GEPI][Incident n°$id_incident]".$info_classe_prot.$liste_protagonistes_responsables;

								$headers = "";
								if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
									$headers.="Reply-to:".$_SESSION['email']."\r\n";
								}

								if(isset($message_id)) {$headers .= "Message-id: $message_id\r\n";}
								if(isset($references_mail)) {$headers .= "References: $references_mail\r\n";}
      
								// On envoie le mail
								$envoi = envoi_mail($subject, $texte_mail, $destinataires, $headers);

							}
						}
					}
				}
			}
		}
	}
}

//$utilisation_scriptaculous="y";
//$utilisation_jsdivdrag='non';
//$javascript_specifique[]="lib/prototype";
$javascript_specifique[]="lib/scriptaculous";
$javascript_specifique[]="lib/unittest";
$javascript_specifique[]="lib/effects";
$javascript_specifique[]="lib/controls";
$javascript_specifique[]="lib/builder";
$style_specifique[]="mod_discipline/mod_discipline";


$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Discipline: Signaler un incident";
//require_once("../lib/header.inc.php");

include_once("../lib/header_template.inc.php");
$tbs_statut_utilisateur = $_SESSION['statut'];
$tbs_last_connection = '';

if (!suivi_ariane($_SERVER['PHP_SELF'],"Discipline : Saisie"))
		echo "erreur lors de la création du fil d'ariane";
//$tbs_retour="../accueil.php";

include_once("../templates/origine/gabarit_entete.php");



$page="saisie_incident.php";

if (!isset($return_url) || $return_url == null) {
    $return_url = 'index.php';
}
//**************** FIN EN-TETE *****************

//debug_var();

?>
<div id='div_svg_qualite' style='margin:auto; color:red; text-align:center;'></div>
<div id='div_svg_avertie' style='margin:auto; color:red; text-align:center;'></div>
<p class='bold'>


<?php

// ===== On construit le menu spécifique de la page =====

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='cpe')||($_SESSION['statut']=='scolarite')) {
	$sql="SELECT 1=1 FROM s_incidents si
	LEFT JOIN s_protagonistes sp ON sp.id_incident=si.id_incident
	WHERE sp.id_incident IS NULL;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
?>
    <a href='incidents_sans_protagonistes.php' onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
        Incidents sans protagonistes
    </a>
    |
    <a href='traiter_incident.php' onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
        Liste des incidents
    </a>
    (<em>avec protagonistes</em>)
<?php
	}
	else {
?>
    <a href='traiter_incident.php' onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
        Liste des incidents
    </a>
<?php
	}
}
elseif (($_SESSION['statut']=='professeur')||($_SESSION['statut']=='autre')) {
	$sql="SELECT 1=1 FROM s_incidents si
	LEFT JOIN s_protagonistes sp ON sp.id_incident=si.id_incident
	WHERE sp.id_incident IS NULL;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
?>
    <a href='incidents_sans_protagonistes.php' onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
        Incidents sans protagonistes
    </a> 
    | 
<?php
	}
	// Rechercher les incidents signalés par le prof ou ayant le prof pour protagoniste
	$sql="SELECT 1=1 FROM s_incidents WHERE declarant='".$_SESSION['login']."';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
?>
    <a href='traiter_incident.php' onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
        Liste des incidents
    </a>
<?php
	}
	else {
		$sql="SELECT 1=1 FROM s_protagonistes WHERE login='".$_SESSION['login']."';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
?>
    <a href='traiter_incident.php' onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
        Liste des incidents
    </a>
<?php
		}
		else {
			$sql="SELECT 1=1 FROM j_eleves_professeurs jep, s_protagonistes sp WHERE sp.login=jep.login AND jep.professeur='".$_SESSION['login']."';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
?>
    <a href='traiter_incident.php' onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
        Liste des incidents
    </a>
<?php
			}
		}
	}
}

// ERIC a décommenter pour la gestion des modele ooo des rapport d'incident.
if ($step==2) {   //Eric Ajout génération du modèle Ooo pour imprimer le rapport d'incident.
?>
    | 
    <a href='../mod_ooo/rapport_incident.php?mode=module_discipline&amp;id_incident=<?php echo $id_incident.add_token_in_url();?>' 
       onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
        Imprimer le rapport d'incident
    </a>
<?php
}
?>
</p>
<?php

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
?>
<div id='s_menu' style='float:right; border: 1px solid black; background-color: white; width: 15em; margin-left:0.5em;'>
    <ul style='margin:0px;'>   
<?php
if($step!=0) {  
?>
        <li>
            <a href='saisie_incident.php?step=0<?php if(isset($id_incident)) {echo "&amp;id_incident=".$id_incident;} ?>' 
               onclick='return confirm_abandon (this, change, "<?php echo $themessage; ?>")'>
                Ajouter des élèves
            </a>
        </li>
<?php
}
if($step!=1) { 
?>
        <li>
            <a href='saisie_incident.php?step=1<?php if(isset($id_incident)) {echo "&amp;id_incident=".$id_incident;} ?>'
               onclick='return confirm_abandon (this, change, "<?php echo $themessage; ?>")'>
                Ajouter des personnels
            </a>
        </li>
        
<?php
}
if($step!=2) {
?>
        <li>
            <a href='saisie_incident.php?step=2<?php if(isset($id_incident)) {echo "&amp;id_incident=".$id_incident;} ?>'
               onclick='return confirm_abandon (this, change, "<?php echo $themessage; ?>")'>
                Préciser l'incident
            </a>
        </li>            
<?php
}
if((isset($id_incident))&&($_SESSION['statut']!='professeur')&&(($_SESSION['statut']!='autre'))) {
?>
        <li>
            <a href='saisie_sanction.php?id_incident=<?php echo $id_incident; ?>' 
               onclick='return confirm_abandon (this, change, "<?php echo $themessage; ?>")'>
                Traitement/sanction
            </a>
        </li>     
<?php
}
?>
    </ul>
</div>
<?php
	//=====================================================
}

if(isset($id_incident) ) {
	// ===== Pour les CPE, on ajoute la possibilité de changer le déclarant =====
    if($_SESSION['statut']=='cpe' && $step== '2') {
        if (getSettingAOui('DisciplineCpeChangeDeclarant')) {
            $peutChanger = FALSE;
            // On recherche le primo-déclarant
            $resPrimo=mysql_query("SELECT primo_declarant , declarant FROM s_incidents WHERE id_incident='".$id_incident."'");
            $response = mysql_fetch_object($resPrimo);
            if ($response->primo_declarant == $_SESSION['login'] || ($response->primo_declarant == '' && $response->declarant == $_SESSION['login'])) {
                $peutChanger= TRUE;
            }
            if($peutChanger){
                if (getSettingAOui('DisciplineCpeChangeDefaut')) {
                    // ===== Par défaut changement autorisé
                    $sql_test="SELECT 1=1 FROM preferences p WHERE p.name='cpePeuChanger';";
                    $res_test=mysql_query($sql_test);
                    if(mysql_num_rows($res_test)==0) {
                        $sqlProf="SELECT u.login , u.nom , u.prenom FROM utilisateurs u
                            WHERE u.statut='professeur' 
                                AND u.etat='actif'
                            ORDER BY u.nom , u.prenom";
                    }
                    else {
                        $sqlProf="SELECT u.login , u.nom , u.prenom FROM utilisateurs u
                            WHERE u.statut='professeur' 
                                AND u.etat='actif'
                                AND (u.login = (SELECT p.login FROM preferences p WHERE p.name='cpePeuChanger' AND p.value LIKE 'yes')
                                    OR u.login != (SELECT p.login FROM preferences p WHERE p.name='cpePeuChanger'))
                            ORDER BY u.nom , u.prenom";
                    }
                } else {
                    // ===== Par défaut changement interdit
                    $sqlProf="SELECT u.login , u.nom , u.prenom FROM utilisateurs u
                        WHERE u.statut='professeur' 
                            AND u.etat='actif'
                            AND u.login = (SELECT p.login FROM preferences p WHERE p.name='cpePeuChanger' AND p.value LIKE 'yes')
                        ORDER BY u.nom , u.prenom";
                }
                // echo $sqlProf."<br />";
                $resProf=mysql_query($sqlProf);
    ?>
        <form enctype='multipart/form-data' action='saisie_incident.php' method='post' id='change_declare'>
        <fieldset style='border: 1px solid grey; margin-bottom:0.5em; background-image: url("../images/background/opacite50.png");'>
        <legend style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>Déclarant</legend>
            <p class='bold'>Changer le déclarant</p>
            <p>
                <select id="choixProf" name="choixProf">
                    <option value='0'>Choisir un déclarant</option>
<?php
                    if(mysql_num_rows($resProf)>0){
                        while($lig_class_tmp=mysql_fetch_object($resProf)){
?>
                            <option value='<?php echo $lig_class_tmp->login; ?>'>
                                <?php echo $lig_class_tmp->nom; ?> <?php echo $lig_class_tmp->prenom; ?>
                            </option>
<?php
                }
            }
?>
                </select>
                <input type='hidden' name='id_incident' value='<?php echo $id_incident; ?>' />
                <input type='hidden' name='step' value='<?php echo $step; ?>' />
                <?php echo add_token_field(true);?>
                <input type="submit" name="change_prof" value="Changer" />
            </p>
        </fieldset>
        </form>
<!--hr /-->
<?php
            }   
        }
    }   
    
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

    //echo "count(\$ele_login)=".count($ele_login)."<br />";

    $sql="SELECT * FROM s_protagonistes WHERE id_incident='$id_incident' ORDER BY statut,qualite,login;";
    //echo "$sql<br />";
    $res=mysql_query($sql);
    if(mysql_num_rows($res)>0) {
        if($etat_incident!='clos') {
?>
<form enctype='multipart/form-data' action='saisie_incident.php' method='post' id='form_suppr'>
    <fieldset style='border: 1px solid grey;background-image: url("../images/background/opacite50.png");'>
    <legend style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>Protagonistes</legend>
    <p><input type='hidden' name='step' value='<?php echo $step; ?>' /></p>
<?php
        }
?>
    <p class='bold'>Protagonistes de l'incident n°<?php echo $id_incident; ?>&nbsp;:</p>
    <blockquote>
        <table class='boireaus' style="border:1px;">
            <caption class='invisible'>Protagonistes</caption>
            <tr>
                <th>Individu</th>
                <th>Statut</th>
                <th>Rôle dans l'incident</th>                
<?php
        if(($gepiSettings['active_mod_ooo'] == 'y')&&
                ((($_SESSION['statut']=='professeur')&&(getSettingValue('imprDiscProfRetenueOOo')=='yes'))
                ||($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='cpe'))) {
?>
                <th>Retenue</th>               
<?php
        }
        if($_SESSION['statut']!='professeur') {
?>
                <th>
                    <!--Avertir la famille... et afficher les avertissements effectués.-->
                    Famille avertie
                </th>             
<?php
        }
        if($etat_incident!='clos') {
?>
                <th>
                    <input type='submit' name='supprimer' value='Supprimer' />
                    <!-- A FAIRE: Ajouter des liens Tout cocher/décocher-->
                </th>           
<?php
        }
?>
            </tr>
<?php
		$ele_login=array();
		$alt=1;
		$cpt=0;
		while($lig=mysql_fetch_object($res)) {
			$alt=$alt*(-1);
?>
            <tr class='lig<?php echo $alt; ?>'>
<?php
        //Individu
        if($lig->statut=='eleve') {
?>
                <td>
<?php
        $sql="SELECT nom,prenom FROM eleves WHERE login='$lig->login';";
            //echo "$sql<br />\n";
            $res2=mysql_query($sql);
            if(mysql_num_rows($res2)>0) {
                $ele_login[]=$lig->login;
                $lig2=mysql_fetch_object($res2);
                echo ucfirst(mb_strtolower($lig2->prenom))." ".mb_strtoupper($lig2->nom);
            }
            else {
?>
                    ERREUR: Login inconnu
<?php
            }
?>
                </td>
                <td>
<?php
        $tmp_tab=get_class_from_ele_login($lig->login);
?>
                    élève (<em><?php if(isset($tmp_tab['liste_nbsp'])) {echo $tmp_tab['liste_nbsp'];} ?></em>)
                </td>
                <td id='td_qualite_protagoniste_<?php echo "$cpt"; ?>'>
<?php
        if($etat_incident!='clos') {
//echo "<select name='qualite[$cpt]' onchange='changement();'>\n";
?>
                    <input type='hidden' name='ele_login[<?php echo $cpt; ?>]' value="<?php echo $lig->login; ?>" />
                    <select name='qualite[<?php echo $cpt; ?>]' 
                            onchange="sauve_role('<?php echo $id_incident; ?>','<?php echo $lig->login; ?>','<?php echo $cpt; ?>');update_colonne_retenue('<?php echo $id_incident; ?>','<?php echo $lig->login; ?>','<?php echo $cpt; ?>');"
                            id='qualite_<?php echo $cpt; ?>' >
                        <option value=''<?php if($lig->qualite=="") {echo " selected='selected'";} ?>>---</option>
<?php
            for($loop=0;$loop<count($tab_qualite);$loop++) {
?>
                        <option value="<?php echo $tab_qualite[$loop]; ?>"<?php if($lig->qualite==$tab_qualite[$loop]) {echo " selected='selected'";} ?>>
                            <?php echo $tab_qualite[$loop]; ?>
                        </option>
<?php
            }
?>
                    </select>
<?php
            }
            else {
?>
                    <?php echo $lig->qualite; ?>
<?php
            }
?>
                </td>
<?php
            }
            else {
?>
                <td>
<?php
                $sql="SELECT nom,prenom,civilite FROM utilisateurs WHERE login='$lig->login';";
                //echo "$sql<br />\n";
                $res2=mysql_query($sql);
                if(mysql_num_rows($res2)>0) {
                    $lig2=mysql_fetch_object($res2);
                    echo ucfirst(mb_strtolower($lig2->prenom))." ".mb_strtoupper($lig2->nom);
                }
                else {
?>
                    ERREUR: Login inconnu
<?php
                }
?>
                </td>
<?php
                //echo "<td>$lig->statut</td>\n";
                if($lig->statut=='autre') {
                    //echo "<td>".$_SESSION['statut_special']."</td>\n";

                    $sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
                                                                                    WHERE du.login_user = '".$lig->login."'
                                                                                    AND du.id_statut = ds.id;";
                    $query = mysql_query($sql);
                    $result = mysql_fetch_array($query);
?>
                <td><?php echo $result['nom_statut']; ?></td>
<?php
                }
                else {
?>
                <td><?php echo $lig->statut; ?></td>
<?php
                }
?>
                <td id='td_qualite_protagoniste_<?php echo $cpt; ?>'>
<?php
                if($etat_incident!='clos') {
?>
                    <input type='hidden' name='u_login[<?php echo $cpt; ?>]' value="<?php echo $lig->login; ?>" />
                    <select name='qualite[<?php echo $cpt; ?>]' 
                            id='qualite_<?php echo $cpt; ?>' 
                            onchange="sauve_role('<?php echo $id_incident;?>','<?php echo $lig->login;?>','<?php echo $cpt; ?>');">
                        <option value=''<?php if($lig->qualite=="") {echo " selected='selected'";} ?>>---</option>
 <?php
                    for($loop=0;$loop<count($tab_qualite);$loop++) {
?>
                        <option value="<?php echo $tab_qualite[$loop]; ?>"<?php if($lig->qualite==$tab_qualite[$loop]) {echo " selected='selected'";} ?>>
                            <?php echo $tab_qualite[$loop]; ?>
                        </option>
<?php
                    }
?>
                    </select>
<?php
                }
                else {
?>
                    <?php echo $lig->qualite; ?>
<?php
                }
?>
                </td>
<?php
            }
//Eric  modèle Ooo
                if(($gepiSettings['active_mod_ooo'] == 'y')&&
                        ((($_SESSION['statut']=='professeur')&&(getSettingValue('imprDiscProfRetenueOOo')=='yes'))
                        ||($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='cpe'))) {
                    echo "<td id='td_retenue_$cpt'>";
                    if ($lig->qualite=='Responsable') { //une retenue seulement pour un responsable !
                        if(responsables_adresses_separees($lig->login)) {
                            $tmp_tab_resp=get_resp_from_ele_login($lig->login);
                            for($loop_resp=0;$loop_resp<count($tmp_tab_resp);$loop_resp++) {
                                if($loop_resp>0) {echo "&nbsp;";}
?>
            <a href='../mod_ooo/retenue.php?mode=module_discipline&amp;id_incident=<?php echo $id_incident; ?>&amp;ele_login=<?php echo $lig->login."&amp;pers_id=".$tmp_tab_resp[$loop_resp]['pers_id'].add_token_in_url(); ?>' title="Imprimer la retenue pour <?php echo $tmp_tab_resp[$loop_resp]['designation'];?>">
                <img src='../images/icons/print.png' width='16' height='16' alt='Imprimer Retenue' />
            </a>
<?php
                            }
                        }
                        else {
?>
            <a href='../mod_ooo/retenue.php?mode=module_discipline&amp;id_incident=<?php echo $id_incident; ?>&amp;ele_login=<?php echo $lig->login.add_token_in_url(); ?>' title='Imprimer la retenue'>
                <img src='../images/icons/print.png' width='16' height='16' alt='Imprimer Retenue' />
            </a>
<?php
                        }
                    }
?>
        </td>
<?php
		}
                
                if($_SESSION['statut']!='professeur') {
?>
        <td>
<?php
                    if($lig->statut=='eleve') {
                        // Avertir la famille... et afficher les avertissements effectués.
                        $defaut="N";

                        if($etat_incident!='clos') {
                                $defaut=$lig->avertie;
?>
            <input type='radio' 
                   name='avertie[<?php echo $cpt; ?>]' 
                   id='avertie_O_<?php echo $cpt; ?>' 
                   value='O' 
                   <?php if($defaut=="O") {echo "checked='checked' ";} ?>
                   onchange="sauve_avertie('<?php echo $id_incident; ?>','<?php echo $lig->login; ?>','O')" />
            <label for='avertie_O_<?php echo $cpt; ?>' style='cursor:pointer;'> O </label>
            /
            <label for='avertie_N_<?php echo $cpt; ?>' style='cursor:pointer;'> N </label>
            <input type='radio' 
                   name='avertie[<?php echo $cpt; ?>]' 
                   id='avertie_N_<?php echo $cpt; ?>' 
                   value='N' 
                   <?php if($defaut=="N") {echo "checked='checked' ";} ?>
                   onchange="sauve_avertie('<?php echo $id_incident; ?>','<?php echo $lig->login; ?>','N')" />
            
            <a href='avertir_famille.php?id_incident=<?php echo $id_incident; ?>&amp;ele_login=<?php echo $lig->login; ?>' 
               title='Avertir par courrier'>
                <img src='../images/icons/saisie.png' width='16' height='16' alt='Avertir' />
            </a>
 <?php
                        }
                        else {
                            if($lig->avertie=="O") {echo "Oui";} else {echo "Non";}
                        }
                    }
                    else {
?>
            &nbsp;
 <?php
                    }
?>
        </td>
 <?php
                }

                if($etat_incident!='clos') {
                        // J'ai laissé le nom suppr_ELE_incident[] même pour la suppression de personnels protagonistes
?>
        <td>
            <input type='checkbox' 
                   name='suppr_ele_incident[]' 
                   id='suppr_<?php echo $cpt; ?>' 
                   value="<?php echo $lig->login; ?>" />
        </td>
 <?php
                }
?>
        </tr>
 <?php
                 $cpt++;
             }
?>
        </table>
 <?php
              if($cpt>0) {
?>
        <script type='text/javascript'>
//<![CDATA[
	function check_protagonistes_sans_qualite() {
		temoin_qualite_protagoniste='n';
		for(i=0;i<<?php echo $cpt; ?>;i++) {
			if(document.getElementById('td_qualite_protagoniste_'+i)) {
				if(document.getElementById('qualite_'+i)) {
					//alert(document.getElementById('qualite_'+i).selectedIndex);
					if(document.getElementById('qualite_'+i).selectedIndex==0) {
						document.getElementById('td_qualite_protagoniste_'+i).style.backgroundColor='red';
						temoin_qualite_protagoniste='y';
					}
					else {
						document.getElementById('td_qualite_protagoniste_'+i).style.backgroundColor='';
					}
				}
			}
		}
		if(temoin_qualite_protagoniste=='y') {
			alert('Le rôle d\'un protagoniste n\'est pas renseigné.');
		}

		setTimeout('check_protagonistes_sans_qualite()',10000);
	}

	setTimeout('check_protagonistes_sans_qualite()',10000);
//]]>
</script>
 <?php
		}

		if($etat_incident!='clos') {
?>
        <p>
            <input type='hidden' name='nb_protagonistes' value='<?php echo $cpt; ?>' />
            <input type='hidden' name='id_incident' value='<?php echo $id_incident; ?>' />
        </p>
        <p class='center'>
            <input type='submit' name='enregistrer_qualite' value='Enregistrer' />
            <?php echo add_token_field(TRUE); ?>
        </p>
 <?php	
		}

                if($step!=2) {
                    $sql="SELECT 1=1 FROM s_incidents WHERE id_incident='$id_incident' AND (nature!='' OR description!='');";
                    $test=mysql_query($sql);
                    if(mysql_num_rows($test)==0) {
?>
        <p style='color:red;'>
            N'oubliez pas de 
            <a href='saisie_incident.php?step=2<?php if(isset($id_incident)) {echo "&amp;id_incident=".$id_incident ;} ?>'
                onclick="return confirm_abandon (this, change, '$themessage')">
                préciser l'incident
            </a>
            après ajout des protagonistes.
        </p>
 <?php	
                    }
                }
?>
    </blockquote>
    <script type='text/javascript'>
	// <![CDATA[
	function sauve_role(id_incident,login,cpt) {
		csrf_alea=document.getElementById('csrf_alea').value;

		//qualite=document.getElementById('qualite_'+cpt).selectedIndex;
		qualite=document.getElementById('qualite_'+cpt).options[document.getElementById('qualite_'+cpt).selectedIndex].value;
		//alert('qualite='+qualite);
		new Ajax.Updater($('div_svg_qualite'),'sauve_role.php?id_incident='+id_incident+'&login='+login+'&qualite='+qualite+'&csrf_alea='+csrf_alea,{method: 'get'});
	}

	function update_colonne_retenue(id_incident,login,cpt) {
		csrf_alea=document.getElementById('csrf_alea').value;

		//qualite=document.getElementById('qualite_'+cpt).selectedIndex;
		qualite=document.getElementById('qualite_'+cpt).options[document.getElementById('qualite_'+cpt).selectedIndex].value;
		//alert('qualite='+qualite);
		new Ajax.Updater($('td_retenue_'+cpt),'update_colonne_retenue.php?id_incident='+id_incident+'&login='+login+'&qualite='+qualite+'&csrf_alea='+csrf_alea,{method: 'get'});
	}

	function sauve_avertie(id_incident,login,avertie) {
		//csrf_alea=document.getElementById('csrf_alea').value;

		//avertie=document.getElementById('avertie_'+cpt).value;
		//+'&csrf_alea='+csrf_alea // inutile... dans sauve_famille_avertie.php, on propose un formulaire avant de générer/enregistrer quoi que ce soit
		//new Ajax.Updater($('div_svg_avertie'),'sauve_famille_avertie.php?id_incident='+id_incident+'&login='+login+'&avertie='+avertie+'".add_token_in_url(false)."',{method: 'get'});
		new Ajax.Updater($('div_svg_avertie'),'sauve_famille_avertie.php?id_incident='+id_incident+'&login='+login+'&avertie='+avertie+'<?php echo add_token_in_url(false);?>',{method: 'get'});
	}
	//]]>
    </script>
 <?php	
		if($etat_incident!='clos') {
?>
</fieldset>
</form>
 <?php	
		}
            }
            else {
?>
<p style='color:red;'>
    Aucun protagoniste n'a (<em>encore</em>) été spécifié pour cet incident.
</p>
 <?php	
            }
    }
    else {
?>
<p class='bold'>Protagonistes de l'incident&nbsp;:</p>
<blockquote>
    <p style='color:red;'>Aucun protagoniste n'a (<em>encore</em>) été spécifié pour cet incident.</p>
</blockquote>
 <?php	
    }

//==========================================
    if($step==0) {
	// AJOUT DE PROTAGONISTES ELEVES A L'INCIDENT
?>
<p class='bold'>Ajouter des protagonistes de l'incident.</p>

<blockquote>
    <form enctype='multipart/form-data' action='saisie_incident.php' method='post' id='formulaire1'>
    <fieldset style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>
    <legend style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>Recherche d'élèves</legend>
    <p>
            Afficher les élèves dont le <strong>nom</strong> contient: 
            <input type='text' name='rech_nom' value='' />
            <input type='hidden' name='page' value='<?php echo $page; ?>' />
            <input type='hidden' name='step' value='<?php echo $step; ?>' />
            <input type='hidden' name='is_posted' value='y' />
            <input type='submit' 
                   name='recherche_eleve' 
                   value='Rechercher'
                   onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')" />
    </p>
 <?php	
        if(isset($id_incident)) {echo "<p><input type='hidden' name='id_incident' value='$id_incident' /></p>\n";}
?>
        <p>
            <?php echo add_token_field(); ?>
        </p>
    </fieldset>
    </form>
    
    <form enctype='multipart/form-data' action='saisie_incident.php' method='post' id='formulaire2'>
<?php if((isset($_POST['recherche_eleve']))||(isset($id_classe))) {?>
        <fieldset style='border: 1px solid grey; margin-top:0.5em; background-image: url("../images/background/opacite50.png");'>
        <legend style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>Choix des élèves</legend>
<?php }?>
        <p>
            <?php echo add_token_field(); ?>
        </p>
 <?php
         if(isset($_POST['recherche_eleve'])) {
             recherche_ele($rech_nom,$_SERVER['PHP_SELF']);
?>
        <p class='center'><input type='submit' name='Ajouter' value='Ajouter' /></p>
 <?php
	}
	elseif(isset($id_classe)) {
            $sql="SELECT DISTINCT e.login,e.nom,e.prenom FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe' ORDER BY e.nom,e.prenom;";
            //echo "$sql<br />";
            $res_ele=mysql_query($sql);
            if(mysql_num_rows($res_ele)>0) {
?>
        <p>Elèves de la classe de <?php echo get_class_from_id($id_classe); ?>&nbsp;:</p>
        
        <blockquote>
 <?php
                $nombreligne=mysql_num_rows($res_ele);

                $nbcol=3;

                // Nombre de lignes dans chaque colonne:
                $nb_par_colonne=round($nombreligne/$nbcol);
?>
            <table width='100%'>
                   <caption class='invisible'>Tableau de choix des élèves</caption>
                   <tr valign='top' align='center'>
                       <td align='left'>
 <?php
                $i = 0;
                $alt=1;
?>
                           <table class='boireaus'>
 <?php
                while ($i < $nombreligne){
                    $lig_ele=mysql_fetch_object($res_ele);

                    if(($i>0)&&(round($i/$nb_par_colonne)==$i/$nb_par_colonne)){
?>
                           </table>
                       </td>
                       <td align='left'>
 <?php
                        $alt=1;
?>
                           <table class='boireaus'>
<?php
                    }

                    $alt=$alt*(-1);
?>
                               <tr class='lig<?php echo $alt; ?>'>
                                   <td>
                                       <input type='checkbox' 
                                              name='ele_login[]' 
                                              id='ele_login_<?php echo $i; ?>' 
                                              value="<?php echo $lig_ele->login; ?>" />
                                   </td>
                                   <td>
                                       <label for='ele_login_<?php echo $i; ?>' 
                                              style='cursor:pointer;'><?php echo ucfirst(mb_strtolower($lig_ele->prenom))." ".mb_strtoupper($lig_ele->nom); ?>
                                       </label>
                                       <?php add_token_field(true); ?>
                                   </td>
                               </tr>
<?php
                    $i++;
                }
?>
                           </table>
                       </td>
                   </tr>
            </table>
            <p class='center'><input type='submit' name='Ajouter' value='Ajouter' /></p>
        </blockquote>
<?php
            }

	}
        
?>
        <p>
<?php
        if(isset($id_incident)) {echo "<input type='hidden' name='id_incident' value='".$id_incident."' />\n";}     
?>
            <input type='hidden' name='is_posted' value='y' />
        </p>
<?php if((isset($_POST['recherche_eleve']))||(isset($id_classe))) {?>
        </fieldset>
<?php }?>
    </form>
<?php

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
?>
    <p>Ou choisir un élève dans une classe:</p>
<?php

			$tab_txt=array();
			$tab_lien=array();

			while($lig_clas=mysql_fetch_object($res_clas)) {
				$tab_txt[]=$lig_clas->classe;
				if(isset($id_incident)) {
					//$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id."&amp;id_incident=$id_incident";
					$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id."&amp;id_incident=$id_incident' onclick='return confirm_abandon (this, change, \"$themessage\")";
				}
				else {
					$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id;
				}
			}
 
?>
    <blockquote>
        <?php tab_liste($tab_txt,$tab_lien,4);?>
    </blockquote>
<?php
	}
}
?>
</blockquote>
<?php

}
elseif($step==1) {
	//==========================================
	// AJOUT DE PERSONNELS COMME PROTAGONISTES DE L'INCIDENT
?>
<p class='bold'>Ajouter des personnels&nbsp;:</p>
<?php

	//$sql="SELECT DISTINCT statut FROM utilisateurs WHERE statut!='responsable' ORDER BY statut;";
	$sql="SELECT DISTINCT statut FROM utilisateurs WHERE statut!='responsable' AND statut!='eleve' AND etat='actif' ORDER BY statut;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		// Ca ne doit pas arriver;o)
?>
<p style='color:red;'>La table 'utilisateurs' ne comporte aucun compte???</p>
<p><br /></p>
<?php
		require("../lib/footer.inc.php");
		die();
	}
?>
<blockquote>
    <form enctype='multipart/form-data' action='saisie_incident.php' method='post' id='formulaire1'>
        <fieldset style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>
        <legend style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>Recherche de personnels</legend>
        <p>
            Afficher les utilisateurs dont le <strong>nom</strong> contient : 
            <input type='text' name='rech_nom' value='' />
            <input type='hidden' name='page' value='<?php echo $page; ?>' />
            <input type='hidden' name='step' value='<?php echo $step; ?>' />
            <input type='hidden' name='is_posted' value='y' />
            <input type='submit' 
                   name='recherche_utilisateur' 
                   value='Rechercher'
                   onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')" />
        </p>
<?php
	echo "<p>".add_token_field()."</p>\n";
	if(isset($id_incident)) {echo "<p><input type='hidden' name='id_incident' value='".$id_incident."' /></p>\n";}
?>
        </fieldset>
    </form>  
<?php
	if(isset($_POST['recherche_utilisateur'])) {
?>
    <form enctype='multipart/form-data' action='saisie_incident.php' method='post' id='formulaire2'>\n"; 
        <fieldset style='border: 1px solid grey; margin-top:0.5em; background-image: url("../images/background/opacite50.png");'>
        <legend style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>Choix de personnels</legend>
<?php
		echo "<p>".add_token_field()."</p>\n";
?>
        <p>
            <input type='hidden' name='page' value='<?php echo $page; ?>' />
            <input type='hidden' name='step' value='<?php echo $step; ?>' />
        </p>
<?php
		recherche_utilisateur($rech_nom,$_SERVER['PHP_SELF']);
?>
        <p>
            <input type='submit' name='Ajouter' value='Ajouter' />
        </p>
<?php
        if(isset($id_incident)) {echo "<p><input type='hidden' name='id_incident' value='$id_incident' /></p>\n";}
?>
        <p>
            <input type='hidden' name='is_posted' value='y' />
        </p>
        </fieldset>
    </form>
<?php
	}
	elseif(isset($categ_u)) {
		$sql="SELECT login, nom, prenom, civilite FROM utilisateurs WHERE statut='$categ_u' ORDER BY nom, prenom;";
		$res2=mysql_query($sql);
		if(mysql_num_rows($res2)==0) {
			// Ca ne doit pas arriver;o)
?>
    <p style='color:red;'>
        La table 'utilisateurs' ne comporte pas de comptes de statut '<?php echo $categ_u; ?>'.
    </p>
    <p><br /></p>
<?php
			require("../lib/footer.inc.php");
			die();
		}
?>
    <form enctype='multipart/form-data' action='saisie_incident.php' method='post' id='formulaire'>
<?php
		echo "<fieldset style='border: 1px solid grey; margin-top:0.5em; background-image: url(\"../images/background/opacite50.png\");'>\n";
		echo "<legend style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>Choix de personnels ($categ_u)</legend>\n";
		echo "<p>".add_token_field()."</p>\n";
?>
        <p>
            <input type='hidden' name='step' value='<?php echo $step; ?>' />
            <input type='hidden' name='is_posted' value='y' />
        </p>
<?php
		if(isset($id_incident)) {echo "<p><input type='hidden' name='id_incident' value='$id_incident' /></p>\n";}

		$nombreligne=mysql_num_rows($res2);

		$nbcol=3;

		// Nombre de lignes dans chaque colonne:
		$nb_par_colonne=round($nombreligne/$nbcol);
?>
        <table width='100%'>
            <caption class="invisible">Tableau de choix des <?php echo $categ_u; ?></caption>
            <tr valign='top' align='center'>
                <td align='left'>
<?php
		$i = 0;
		$alt=1;
?>
                    <table class='boireaus'>
                        <caption class="invisible">Colonne de <?php echo $categ_u; ?></caption>
<?php
		while ($i < $nombreligne){
			$lig2=mysql_fetch_object($res2);

			if(($i>0)&&(round($i/$nb_par_colonne)==$i/$nb_par_colonne)){
?>
                    </table>
                </td>
                <td align='left'>
<?php
					$alt=1;
?>
                    <table class='boireaus'>
                        <caption class="invisible">Colonne de <?php echo $categ_u; ?></caption>
<?php
			}

			$alt=$alt*(-1);
?>
                        <tr class='lig<?php echo $alt; ?>'>
                            <td>
                                <input type='checkbox' 
                                       name='u_login[]' 
                                       id='u_login_<?php echo $i; ?>' 
                                       value="<?php echo $lig2->login; ?>" />
                            </td>
                            <td>
                                <label for='u_login_<?php echo $i; ?>' 
                                       style='cursor:pointer;'>
                                    <?php echo $lig2->civilite; ?> 
                                    <?php echo mb_strtoupper($lig2->nom); ?> 
                                    <?php echo ucfirst(mb_substr($lig2->prenom,0,1)); ?>.
                                </label>
                            </td>
<?php
			$sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
											WHERE du.login_user = '".$lig2->login."'
											AND du.id_statut = ds.id;";
			$query = mysql_query($sql);
			$result = mysql_fetch_array($query);
?>
                            <td><?php echo $result['nom_statut']; ?></td>
                        </tr>
<?php
			echo "\n";

			$i++;
		}
?>
                    </table>
                </td>
            </tr>
        </table>
        <p>
            <input type='submit' name='Ajouter' value='Ajouter' />
        </p>
        </fieldset>
    </form>
    <p><br /></p>
<?php
	}
?>
    <p class='bold'>
        Choisir une catégorie de personnels&nbsp;:
    </p>
    <blockquote>
<?php
	while($lig=mysql_fetch_object($res)) {
?>
        <p>
            <a href='saisie_incident.php?step=1&amp;categ_u=<?php echo $lig->statut; ?><?php if(isset($id_incident)) {echo "&amp;id_incident=$id_incident";}; ?>'
               onclick='return confirm_abandon (this, change, "<?php echo $themessage; ?>")'>
                <?php echo ucfirst($lig->statut); ?>
            </a>
        </p>
<?php
	}
?>
    </blockquote>
</blockquote>
<?php
}
elseif($step==2) {
	//==========================================
	// SAISIE DES DETAILS DE L'INCIDENT
?>
<p class='bold'>Détails de l'incident
<?php
	if(isset($id_incident)) {
		echo " n°$id_incident";

		$sql="SELECT declarant FROM s_incidents WHERE id_incident='$id_incident';";
		$res_dec=mysql_query($sql);
		if(mysql_num_rows($res_dec)>0) {
			$lig_dec=mysql_fetch_object($res_dec);
?>
    (<span style='font-size:x-small; font-style:italic; color:red;'>
        signalé par <?php echo u_p_nom($lig_dec->declarant); ?>
    </span>)
<?php
		}
	}
?>
    &nbsp;:
</p>
<?php

	if($etat_incident!='clos') {
?>
<form enctype='multipart/form-data' action='saisie_incident.php' method='post' id='formulaire'>
    <fieldset style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>
    <legend style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>Détails de l'incident</legend>
    <p><?php echo add_token_field(); ?></p>
<?php
	}

	//echo "count(\$ele_login)=".count($ele_login)."<br />";
	// Si aucune date n'est encore saisie, proposer la date du jour
	$annee = strftime("%Y");
	$mois = strftime("%m");
	$jour = strftime("%d");
	$display_date = $jour."/".$mois."/".$annee;
	// Si aucune heure n'est encore saisie, proposer l'heure courante
	$display_heure=strftime("%H").":".strftime("%M");

	//$timestamp_heure=time();
	$sql="SELECT nom_definie_periode FROM edt_creneaux WHERE CURTIME()>=heuredebut_definie_periode AND CURTIME()<heurefin_definie_periode;";
	$res_time=mysql_query($sql);
	if(mysql_num_rows($res_time)>0) {
		$lig_time=mysql_fetch_object($res_time);
		$display_heure=$lig_time->nom_definie_periode;
	}

	// Initialisation de variables:
	$nature="";
	$description="";
    $commentaire="";
	
	if(isset($id_incident)) {
		if($etat_incident!='clos') {
?>
    <p>
        <input type='hidden' name='id_incident' value='<?php echo $id_incident; ?>' />
    </p>
<?php
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
			$commentaire=$lig_inc->commentaire;
			$id_lieu=$lig_inc->id_lieu;
                        //echo add_token_field(true);
		}
	}

?>
    <blockquote style='margin-right: 0.5em;'>
<?php
	$alt=1;
?>
        <table class='boireaus' style="border:1px;">
            <caption class="invisible">Détails de l'incident</caption>
<?php
	// Date de l'incident
	$alt=$alt*(-1);
?>
            <tr class='lig<?php echo $alt; ?>'>
                <td style='font-weight:bold;vertical-align:top;text-align:left;'>
                    Date de l'incident&nbsp;:
                </td>
<?php
	if($etat_incident!='clos') {
?>
                <td style='text-align:left;'>
<?php
		//Configuration du calendrier
		include("../lib/calendrier/calendrier.class.php");
		$cal = new Calendrier("formulaire", "display_date");
?>
                    <input type='text' 
                           name='display_date' 
                           id='display_date' 
                           size='10' 
                           value="<?php echo $display_date; ?>" 
                           onkeydown="clavier_date_plus_moins(this.id,event);" 
                           onchange='changement()' />
                    <a href="#calend" onclick="<?php echo $cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170); ?>">
                        <img src="../lib/calendrier/petit_calendrier.gif" 
                             style="border:0px;"
                             alt="Petit calendrier" />
                    </a>
                </td>
<?php
/*
                <td style='text-align:right; width:1%;'>
                    <input type='submit' name='enregistrer' value='Enregistrer' onclick='verif_details_incident();' />
                </td>
 * 
 */
?>
                <td style='text-align:right; width:1%;'>
                    <input type='button' name='enregistrer' value='Enregistrer' onclick='verif_details_incident();' />
                    <noscript><p><input type='submit' name='enregistrer' value='Enregistrer vraiment' /></p></noscript>
                </td>
<?php
	}
	else {
		echo "<td style='text-align:left;'>\n";
		echo $display_date;
		echo "</td>\n";
	}

?>
            </tr>
<?php
	//========================
	// Heure
	$alt=$alt*(-1);
?>
            <tr class='lig<?php echo $alt; ?>'>
                <td style='font-weight:bold;vertical-align:top;text-align:left;'>
                    Heure de l'incident&nbsp;:
                </td>
                <td style='text-align:left;'<?php if($etat_incident!='clos') {echo " colspan='2'";}?>>
<?php
	if($etat_incident!='clos') {
		$texte="ATTENTION&nbsp;: Il ne faut pas mettre une heure vide.<br />En cas d'incertitude sur l'heure, il vaut mieux mettre un point d'interrogation en guise d'heure.<br />Sinon, s'il n'y a pas d'incertitude, le créneau horaire M1, S2,... est préférable.";
		$tabdiv_infobulle[]=creer_div_infobulle("div_infobulle_avertissement_heure","Heure non vide","",$texte,"",30,0,'y','y','n','n',2);

?>
                    <div id='div_avertissement_heure' style='float:right;'>
                        <a href='#' 
                           onmouseover="delais_afficher_div('div_infobulle_avertissement_heure','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);" 
                           onmouseout="cacher_div('div_infobulle_avertissement_heure');" 
                           onclick="return false;" 
                           title='Attention heure'>
                            <img src='../images/icons/ico_question_petit.png' 
                                 width='15' 
                                 height='15' 
                                 alt='Attention heure' />
                        </a>
                    </div>
                    
                    <input type='text' 
                           name='display_heure' 
                           id='display_heure' 
                           size='6' 
                           value="<?php echo $display_heure; ?>" 
                           onkeydown="clavier_heure(this.id,event);" 
                           AutoComplete="off" 
                           onchange='changement()' />

<?php
		choix_heure('display_heure','div_choix_heure');
	}
	else {
		echo $display_heure;
	}

?>
                </td>
            </tr>
<?php

	//========================
	// Lieu

	$sql="(SELECT * FROM s_lieux_incidents WHERE lieu!='')";
	//echo "$sql<br />\n";
	$res_lieu=mysql_query($sql);
	if(mysql_num_rows($res_lieu)>0) {
		$alt=$alt*(-1);
?>
            <tr class='lig<?php echo $alt; ?>'>
                <td style='font-weight:bold;vertical-align:top;text-align:left;'>Lieu&nbsp;: </td>
<?php

		echo "<td style='text-align:left;'";
		if($etat_incident!='clos') {
			echo " colspan='2'";
			echo ">\n";

			//echo "<select name='choix_lieu' id='choix_lieu' onchange=\"maj_lieu('lieu_incident','choix_lieu');changement();\">\n";
			echo "<select name='id_lieu' id='id_lieu' onchange='changement()'>\n";
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
?>
            </tr>
<?php
	}

	//========================
	// Nature de l'incident
	$alt=$alt*(-1);
?>
            <tr class='lig<?php echo $alt; ?>'>
                <td style='font-weight:bold;vertical-align:top;text-align:left;'>
                    Nature de l'incident <span style='color:red;'>(*)</span>&nbsp;:
                </td>
                <td style='text-align:left;'<?php if($etat_incident!='clos') {echo " colspan='2'";} ?>>
<?php
	// Pouvoir sélectionner une nature parmi les natures déjà saisies auparavant et parmi une liste type
	// insulte,violence,refus de travail,...
	// Actuellement, la liste des propositions est uniquement recherchée dans les saisies précédentes.

	//$nature="";

	$DisciplineNaturesRestreintes=getSettingValue('DisciplineNaturesRestreintes');
	//echo "\$DisciplineNaturesRestreintes=$DisciplineNaturesRestreintes<br />";
	if($etat_incident!='clos') {
		$saisie_nature_libre="y";
		if($DisciplineNaturesRestreintes==2) {
			
			// On limite les natures d'incident au contenu de s_natures
			$sql="SELECT DISTINCT nature FROM s_natures WHERE nature!='' ORDER BY nature;";
			$res_nat=mysql_query($sql);
			if(mysql_num_rows($res_nat)>0) {
				$saisie_nature_libre="n";
?>
                    <select name='nature' id='nature' onchange='changement()'>
<?php
				while($lig_nat=mysql_fetch_object($res_nat)) {
?>
                        <option value="<?php echo $lig_nat->nature;?>"<?php if($lig_nat->nature==$nature) {echo " selected='selected'";} ?>>
                            <?php echo $lig_nat->nature; ?>
                        </option>
<?php
				}
?>
                    </select>
<?php
			}

		}

		if($saisie_nature_libre=="y") {
?>
                    <input type='text' 
                           name='nature' 
                           id='nature' 
                           size='30' 
                           value="<?php echo $nature; ?>" />
                    <div id='div_completion_nature' class='infobulle_corps'></div>
                    
                    <script type='text/javascript'>
//<![CDATA[
new Ajax.Autocompleter (
	'nature',      // ID of the source field
	'div_completion_nature',  // ID of the DOM element to update
	'check_nature_incident.php', // Remote script URI
	{method: 'post', paramName: 'nature'}
);
//]]>
                    </script>

<?php
			if($DisciplineNaturesRestreintes!=1) {
				$sql="SELECT DISTINCT nature FROM s_incidents WHERE nature!='' ORDER BY nature;";
			}
			else {
				$sql="SELECT DISTINCT nature FROM s_natures WHERE nature!='' ORDER BY nature;";
			}
			$res_nat=mysql_query($sql);
			if(mysql_num_rows($res_nat)>0) {
?>
                    <a href='#' 
                       onclick="cacher_toutes_les_infobulles();afficher_div('div_choix_nature','y',10,-40); return false;">
                        Choix
                    </a>
                    
<?php	
				$texte="<table class='boireaus' style='margin: auto; border:1px;' summary=\"Choix d'une nature\">\n";
				$alt2=1;
				while($lig_nat=mysql_fetch_object($res_nat)) {
					$alt2=$alt2*(-1);
					$texte.="<tr class='lig$alt2' onmouseover=\"this.style.backgroundColor='white';\" onmouseout=\"this.style.backgroundColor='';\">\n";
					$texte.="<td ><a href='#' onclick=\"document.getElementById('nature').value='$lig_nat->nature';cacher_div('div_choix_nature');changement();return false;\">".$lig_nat->nature."</a></td>\n";
					$texte.="</tr>\n";
				}
				$texte.="</table>\n";
	
				$tabdiv_infobulle[]=creer_div_infobulle('div_choix_nature',"Nature de l'incident","",$texte,"",14,0,'y','y','n','n');

?>
                    <a href='#' 
                       onclick="return false;" 
                       onmouseover="delais_afficher_div('div_explication_choix_nature','y',10,-40,<?php echo $delais_affichage_infobulle; ?>,<?php echo $largeur_survol_infobulle; ?>,<?php echo $hauteur_survol_infobulle; ?>);" 
                       onmouseout="cacher_div('div_explication_choix_nature')">
                        <img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Choix nature' />
                    </a>

<?php		
				$texte="Cliquez pour choisir une nature existante.<br />Ou si aucune nature n'est déjà définie, saisissez la nature d'incident de votre choix.";
				$tabdiv_infobulle[]=creer_div_infobulle('div_explication_choix_nature',"Choix nature de l'incident","",$texte,"",18,0,'y','y','n','n');
	
				//====================================================
	
				$id_infobulle_nature2='div_choix_nature2';
				$largeur_infobulle_nature2=35;
				$hauteur_infobulle_nature2=10;

				// Pour que le div soit caché via le code en footer lors du chargement de la page.
				$tabid_infobulle[]=$id_infobulle_nature2;

				// Conteneur:
?>
                    <div id='<?php echo $id_infobulle_nature2; ?>' 
                         class='infobulle_corps' 
                         style='color: #000000; 
                                border: 1px solid #000000; 
                                padding: 0px; 
                                position: absolute; 
                                width: <?php echo $largeur_infobulle_nature2; ?>em; 
                                height: <?php echo $hauteur_infobulle_nature2; ?>em; 
                                left: 1600px;'>
<?php
					// Ligne d'entête/titre
?>
                        <div class='infobulle_entete' 
                             style='color: #ffffff; 
                                    cursor: move; 
                                    font-weight: bold; 
                                    padding: 0px;
                                    width: <?php echo $largeur_infobulle_nature2; ?>em;' 
                             onmousedown="dragStart(event, '<?php echo $id_infobulle_nature2; ?>')">

                            <div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>
                                <a href='#' onclick="cacher_div('<?php echo $id_infobulle_nature2; ?>');return false;">
                                    <img src='../images/icons/close16.png' style='width:16px; height:16px' alt='Fermer' />
                                </a>
                            </div>
                            
                            <span style='padding-left: 1px; margin-bottom: 3px;'>
                                Natures d'incidents semblables
                            </span>
                        </div>			
<?php
					// Partie texte:
					$hauteur_hors_titre=$hauteur_infobulle_nature2-1.5;
?>
                        <div id='<?php echo $id_infobulle_nature2; ?>_texte' 
                             style='width: <?php echo $largeur_infobulle_nature2; ?>em; 
                                    height: <?php echo $hauteur_hors_titre; ?>em; 
                                    overflow: auto; 
                                    padding-left: 1px;'>
                        </div>
                    </div>
<?php
				//=========================================
	
?>
                    <script type='text/javascript'>
//<![CDATA[
	function check_incident(event) {

		saisie=document.getElementById('nature').value;

		//var reg=new RegExp(\"[ ,;.-']+\", \"g\");
		var reg=new RegExp("[ ,;]+", "g");
		var tab1=saisie.split(reg);
		var j=0;
		var tab2=new Array();
		for (var i=0; i<tab1.length; i++) {
			chaine_tmp=tab1[i];
			//alert('chaine_tmp='+chaine_tmp+' et chaine_tmp.length='+chaine_tmp.length);
			if(chaine_tmp.length>=4) {
				tab2[j]=tab1[i];
				j++;
			}
		}

		if(tab2.length>0) {
			chaine_rech='';
			for (var i=0; i<tab2.length; i++) {
				chaine_rech=chaine_rech+'_'+tab2[i];
			}

			new Ajax.Updater($('div_choix_nature2_texte'),'check_nature_incident.php?chaine_rech='+chaine_rech,{method: 'get'});
			afficher_div('div_choix_nature2','y',10,40); ;
		}
	}

//]]>
</script>
<?php
				//====================================================
			}
		}
	}
	else {
		echo $nature;
	}	
?>
                </td>
            </tr>
<?php
	//========================
	$alt=$alt*(-1);	
?>
            <tr class='lig<?php echo $alt; ?>'>
                <td style='font-weight:bold;vertical-align:top;text-align:left;'>
                    Description de l'incident&nbsp;:&nbsp;
                    <div id='div_avertissement_description' style='float:right;'>
                        <a href='#' 
                           onclick="afficher_div('div_explication_description','y',10,-40);return false;" 
                           onmouseover="delais_afficher_div('div_explication_description','y',10,-40,<?php echo $delais_affichage_infobulle; ?>,<?php echo $largeur_survol_infobulle; ?>,<?php echo $hauteur_survol_infobulle; ?>);" 
                           >
                           <!--
                           onmouseout="cacher_div('div_explication_description')">
                           -->
                            <img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Description' />
                        </a>
                    </div>
<?php
	$texte="Récit détaillé des faits (<em>paroles prononcées, gestes, réactions,...</em>)<br />";
	$texte.="<br />";
	//$texte.="Que ce compte-rendu soit visible ou non par défaut des parents des élèves concernés, retenez qu'ils ont le droit d'après la CNIL de réclamer l'accès à ces données.<br />";
	$texte.="Veillez donc à respecter les préconisations suivantes&nbsp;:<br />";

	$texte.="<strong>Règle n° 1 :</strong> Avoir à l'esprit, quand on renseigne ces zones commentaires,<br />que la personne qui est concernée peut exercer son droit d'accès et lire ces commentaires !<br />";
	$texte.="<strong>Règle n° 2 :</strong> Rédiger des commentaires purement objectifs<br />et jamais excessifs ou insultants.<br />";
	$texte.="<br />";
	$texte.="Pour plus de détails, consultez <a href='http://www.cnil.fr/la-cnil/actualite/article/article/zones-bloc-note-et-commentaires-les-bons-reflexes-pour-ne-pas-deraper/' target='_blank'>l'article de la CNIL</a>?<br />";

	$tabdiv_infobulle[]=creer_div_infobulle('div_explication_description',"Description de l'incident","",$texte,"",30,0,'y','y','n','n');
?>
                    <div id='div_avertissement_description2' style='display:none; font-size:small; color:red; font-weight:normal;'><?php echo $texte;?></div>

                </td>
                <td style='text-align:left;'<?php if($etat_incident!='clos') {if ($autorise_commentaires_mod_disc !="yes") echo " colspan='2'";} ?>>
<?php
	if(count($ele_login)>0) {
		$chaine_avertissement="";
		for($i=0;$i<count($ele_login);$i++) {
			if(acces_ele_disc($ele_login[$i])) {
				if($chaine_avertissement=='') {$chaine_avertissement.="Détails visibles de ";}
				else {$chaine_avertissement.=", ";}
				$chaine_avertissement.=get_nom_prenom_eleve($ele_login[$i]);
			}
			$tab_resp=get_resp_from_ele_login($ele_login[$i]);
			for($j=0;$j<count($tab_resp);$j++) {
				if((isset($tab_resp[$j]['login']))&&(acces_resp_disc($tab_resp[$j]['login']))) {
					if($chaine_avertissement=='') {$chaine_avertissement.="Détails visibles de ";}
					else {$chaine_avertissement.=", ";}
					$chaine_avertissement.=$tab_resp[$j]['designation'];
				}
			}
		}
		if($chaine_avertissement!="") {
			$chaine_avertissement="<div style='float:right; color:red; width:15em; border: 1px solid red; margin: 1px;'>$chaine_avertissement</div>\n";
			echo $chaine_avertissement;
		}
	}

	if($etat_incident!='clos') {
?>
                    <textarea id="description" 
                              class='wrap' 
                              name="no_anti_inject_description" 
                              rows='8' 
                              cols='60' 
                              onfocus="document.getElementById('div_avertissement_description2').style.display=''"
                              onchange="changement()"><?php echo $description; ?></textarea>
                    
                    <div id='div_compteur_caracteres_textarea' style='width:20em; text-align:center'></div>

<script type='text/javascript'>
//<![CDATA[
function compte_caracteres_textarea(textarea_id, compteur_id) {
	if(document.getElementById(compteur_id)) {
		if(document.getElementById(textarea_id)) {
			document.getElementById(compteur_id).innerHTML=document.getElementById(textarea_id).value.length+' caractere(s).';
		}
	}
}

function comptage_caracteres_textarea() {
	compte_caracteres_textarea('description', 'div_compteur_caracteres_textarea');
	setTimeout('comptage_caracteres_textarea()', 1000);
}

setTimeout('comptage_caracteres_textarea()', 1000);
//]]>
</script>
<?php
	}
	else {
		echo nl2br($description);
	}
?>
                </td>
<?php
	// Ajout Eric
	if ($autorise_commentaires_mod_disc=="yes") {
?>
                <td style='text-align:center;'>
                    <strong>Zone de dialogue sur l'incident</strong>
                    <a href='#' 
                       onclick="return false;" 
                       onmouseover="delais_afficher_div('div_explication_commentaires','y',10,-40,<?php echo $delais_affichage_infobulle; ?>,<?php echo $largeur_survol_infobulle; ?>,<?php echo $hauteur_survol_infobulle; ?>)"
                       onmouseout="cacher_div('div_explication_choix_nature')">
                        <img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Choix nature' />
                    </a>
<?php
		$texte="Cette zone de texte est disponible pour dialoguer avec la vie scolaire des modalités particulières de traitement de l'incident ou suivre les suites de celui-ci.<br /> Horaire et lieu d'une retenue demandée, demande de convocation de l'élève par le CPE, ...";
		$tabdiv_infobulle[]=creer_div_infobulle('div_explication_commentaires',"Zone de texte : dialogue","",$texte,"",18,0,'y','y','n','n');
?>
                    <textarea id="commentaire"  
                              name="no_anti_inject_commentaire" rows='8' cols='60' onchange="changement()">
                        <?php echo $commentaire; ?>
                    </textarea>
                </td>
<?php
	}
	// Fin ajout Eric
?>
            </tr>
<?php
	//========================
	if(count($ele_login)>0) {
		$alt=$alt*(-1);
?>
            <tr class='lig<?php echo $alt; ?>'>
                <td style='font-weight:bold;vertical-align:top;text-align:left;'>
                    Mesures&nbsp;:
                </td>
<?php

		if($etat_incident!='clos') {
?>
                <td style='text-align:left;' colspan='2'>
<?php
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
?>
                    <table class='boireaus' style='margin:2px;'>
                        <caption class="invisible">Mesures</caption>
                        <tr>
                            <th>Elève</th>
<?php
				if(count($tab_mes_prise)>0) {
?>
                            <th>Prises</th>
<?php
				}
				if(count($tab_mes_demandee)>0) {
?>
                            <th>
<?php

					$texte="Les mesures demandées le sont par des professeurs.<br />";
					$texte.="Un compte cpe ou scolarité peut ensuite saisir la sanction correspondante s'il juge la demande appropriée.<br />";
					$texte.="<br />";
					$texte.="Il n'y a pas d'intérêt pour un CPE à cocher une de ces cases.<br />";
					$texte.="Il vaut mieux passer à la saisie en suivant le lien Traitement/sanction en haut à droite.<br />";
					$tabdiv_infobulle[]=creer_div_infobulle("div_mesures_demandees","Mesures demandées","",$texte,"",30,0,'y','y','n','n');
?>
                                <a href='#' 
                                   onmouseover="delais_afficher_div('div_mesures_demandees','y',10,-40,<?php echo $delais_affichage_infobulle; ?>,<?php echo $largeur_survol_infobulle; ?>,<?php echo $hauteur_survol_infobulle; ?>);" 
                                   onclick="return false;">
                                    Demandées
                                </a>
                            </th>
                            <th>
                                Travail et/ou document(s) joint(s) à une mesure demandée
                            </th>
<?php
				}
?>
                        </tr>
<?php

				//echo "<tr><td>count(\$ele_login)=".count($ele_login)."</td></tr>";
				// Boucle sur la liste des élèves
				$alt2=1;
				for($i=0;$i<count($ele_login);$i++) {
					$alt2=$alt2*(-1);
?>
                        <tr class='lig<?php echo $alt2; ?>'>
                            <td>
                                <input type='hidden' name='mesure_ele_login[<?php echo $i; ?>]' value="<?php echo $ele_login[$i]; ?>" />
                                <?php echo p_nom($ele_login[$i]); ?>
<?php
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
?>
                            </td>
<?php

					if(count($tab_mes_prise)>0) {
?>
                            <td style='text-align:left; vertical-align:top;'>
<?php
						for($loop=0;$loop<count($tab_mes_prise);$loop++) {
							//echo "<input type='checkbox' name='mesure_prise_".$i."[]' id='mesure_prise_".$i."_$loop' value=\"".$tab_mes_prise[$loop]."\" onchange='changement();' ";
							//if(in_array($tab_mes_prise[$loop],$tab_mes_eleve)) {echo "checked='checked' ";}
?>
                                <input type='checkbox' 
                                       name='mesure_prise_<?php echo $i; ?>[]' 
                                       id='mesure_prise_<?php echo $i; ?>_<?php echo $loop; ?>' 
                                       value="<?php echo $tab_id_mes_prise[$loop]; ?>" 
                                       onchange='changement();'
                                           <?php if(in_array($tab_id_mes_prise[$loop],$tab_mes_eleve)) {echo "checked='checked' ";} ?> />
                                <label for='mesure_prise_<?php echo $i; ?>_<?php echo $loop; ?>' style='cursor:pointer;'>
                                    &nbsp;<?php echo $tab_mes_prise[$loop]; ?></label>
<?php 
							if($tab_c_mes_prise[$loop]!='') {
								if($i==0) {
									$tabdiv_infobulle[]=creer_div_infobulle("div_commentaire_mesures_prise_$loop",$tab_mes_prise[$loop],"",$tab_c_mes_prise[$loop],"",30,0,'y','y','n','n');
								}
?>
                                <a href='#' 
                                   onmouseover="delais_afficher_div('div_commentaire_mesures_prise_<?php echo $loop; ?>','y',10,-40,<?php echo $delais_affichage_infobulle; ?>,<?php echo $largeur_survol_infobulle; ?>,<?php echo $hauteur_survol_infobulle; ?>);" 
                                   onmouseout="cacher_div('div_commentaire_mesures_prise_<?php echo $loop; ?>')" 
                                   onclick="return false;">
                                    <img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Précision' />
                                </a>
<?php 
							}
?>
                                <br />
<?php 
						}
?>
                            </td>
<?php 
					}

					if(count($tab_mes_demandee)>0) {
?>
                            <td style='text-align:left; vertical-align:top;'>
<?php 
						for($loop=0;$loop<count($tab_mes_demandee);$loop++) {
							//echo "<input type='checkbox' name='mesure_demandee_".$i."[]' id='mesure_demandee_".$i."_$loop' value=\"".$tab_mes_demandee[$loop]."\" onchange='changement();' ";
							//if(in_array($tab_mes_demandee[$loop],$tab_mes_eleve)) {echo "checked='checked' ";}
?>
                                <input type='checkbox' 
                                       name='mesure_demandee_<?php echo $i; ?>[]'
                                       id='mesure_demandee_<?php echo $i; ?>_<?php echo $loop; ?>' 
                                       value="<?php echo $tab_id_mes_demandee[$loop]; ?>" 
                                       onchange='changement(); check_coche_mes_demandee(<?php echo $i; ?>);'
                                           <?php if(in_array($tab_id_mes_demandee[$loop],$tab_mes_eleve)) {echo "checked='checked' ";}; ?>
                                       />
                                <label for='mesure_demandee_<?php echo $i; ?>_<?php echo $loop; ?>' 
                                       style='cursor:pointer;'>
                                    &nbsp;<?php echo $tab_mes_demandee[$loop]; ?>
                                </label>
<?php 

							if($tab_c_mes_demandee[$loop]!='') {
								if($i==0) {
									$tabdiv_infobulle[]=creer_div_infobulle("div_commentaire_mesures_demandee_$loop",$tab_mes_demandee[$loop],"",$tab_c_mes_demandee[$loop],"",30,0,'y','y','n','n');
								}
?>
                                <a href='#' 
                                   onmouseover="delais_afficher_div('div_commentaire_mesures_demandee_<?php echo $loop; ?>','y',10,-40,<?php echo $delais_affichage_infobulle; ?>,<?php echo $largeur_survol_infobulle; ?>,<?php echo $hauteur_survol_infobulle; ?>);" 
                                   onmouseout="cacher_div('div_commentaire_mesures_demandee_<?php echo $loop; ?>')" 
                                   onclick="return false;">
                                    <img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Précision' />
                                </a>
<?php 
							}
?>
                                <br />
<?php 
						}
?>
                            </td>
<?php 
					}
?>
                            <td>
<?php 
					//echo "Travail&nbsp;: <textarea name='travail_pour_mesure_demandee_".$i."' id='travail_pour_mesure_demandee_".$i."' cols='30'>Nature du travail pour la mesure demandée</textarea>\n";

					$texte_travail="Travail : ";
					$tmp_pref_texte_travail=getPref($_SESSION['login'], 'mod_discipline_travail_par_defaut', '');
					if($tmp_pref_texte_travail!='') {
						$texte_travail=$tmp_pref_texte_travail;
					}
					elseif(getSettingValue('mod_discipline_travail_par_defaut')!='') {
						$texte_travail=getSettingValue('mod_discipline_travail_par_defaut');
					}

					$sql="SELECT * FROM s_travail_mesure WHERE id_incident='$id_incident' AND login_ele='".$ele_login[$i]."';";
					$res_travail_mesure_demandee=mysql_query($sql);
					if(mysql_num_rows($res_travail_mesure_demandee)>0) {
						$lig_travail_mesure_demandee=mysql_fetch_object($res_travail_mesure_demandee);
						$texte_travail=$lig_travail_mesure_demandee->travail;
					}
?>
                   <textarea name='no_anti_inject_travail_pour_mesure_demandee_<?php echo $i; ?>' 
                                          id='travail_pour_mesure_demandee_<?php echo $i; ?>' 
                                          cols='30' 
                                          rows='5' ><?php echo $texte_travail; ?></textarea>
<?php

					// Liste des fichiers déjà joints
					$tab_file=get_documents_joints($id_incident, "mesure", $ele_login[$i]);
					if(count($tab_file)>0) {
?>
                                <table class='boireaus' width='100%'>
                                    <caption class="invisible">Fichiers</caption>
                                    <tr>\
                                        <th>Fichier</th>
                                        <th>Supprimer</th>
                                    </tr>
<?php
						$alt3=1;
						for($loop=0;$loop<count($tab_file);$loop++) {
							$alt3=$alt3*(-1);
?>
                                    <tr class='lig<?php echo $alt3; ?> white_hover'>
                                        <td><?php echo $tab_file[$loop]; ?></td>
                                        <td>
                                            <input type='checkbox' 
                                                   name='suppr_doc_joint_<?php echo $i; ?>[]' 
                                                   value="<?php echo $tab_file[$loop]; ?>" />
                                        </td>
<?php
							// PB: Est-ce qu'on ne risque pas de permettre d'aller supprimer des fichiers d'un autre incident?
							//     Tester le nom de fichier et l'id_incident
							//     Fichier en ../$dossier_documents_discipline/incident_<$id_incident>/mesures/<LOGIN_ELE>
?>
                                    </tr>
<?php
						}
?>
                                </table>
<?php
					}
?>
                                <input type="file" 
                                       size="15" 
                                       name="document_joint_<?php echo $i; ?>" 
                                       id="document_joint_<?php echo $i; ?>" />
                                <br />
                            </td>
                        </tr>
<?php
				}
?>
                    </table>
<?php
			}
			else {
?>
                    <p>Aucun type de mesure n'est défini.</p>
<?php
			}
?>
                </td>
<?php
		}
		else {
?>
                <td style='text-align:left;'>
<?php
		$texte=affiche_mesures_incident($id_incident);
?>
                    <?php echo $texte; ?>
                </td>
<?php
		}
?>
            </tr>
<?php
	}
	//========================
?>
        </table>
<?php

	if($etat_incident!='clos') {
?>
        <p>
            <input type='hidden' name='is_posted' value='y' />
            <input type='hidden' name='step' value='<?php echo $step; ?>' />
        </p>
        <p style='text-align:center;'><input type='checkbox' name='clore_incident' id='clore_incident' value='y' />
            <label for='clore_incident' style='cursor:pointer;'>&nbsp;Clore l'incident.</label>
            <br />
            <em style='font-size:x-small;'>(sous réserve de ne pas <strong>Demander</strong> de mesure)</em>
        </p>
        <p style='text-align:center;'>
            <input type='button' name='enregistrer2' value='Enregistrer' onclick='verif_details_incident();' />
        </p>
        <noscript><p><input type='submit' name='enregistrer2' value='Enregistrer vraiment' /></p></noscript>

        <p><em>NOTES&nbsp;</em>&nbsp;:</p>
        <ul>
            <li><span style='color:red;'>(*)</span> Il est impératif de saisir une Nature d'incident pour des questions de facilité de traitement par la suite.</li>
            <li>Les formulaires de saisie du rôle des protagonistes et de saisie des détails de l'incident sont séparés.<br />
Ne faites pas de modification dans les deux formulaires sans valider entre les deux, vous perdriez la modification.</li>
        </ul>

<?php
	}
?>
    </blockquote>
<?php
	if($etat_incident!='clos') {
?>
    <script type="text/javascript">
//<![CDATA[
	function verif_details_incident() {
<?php
		if($saisie_nature_libre=="y") {
?>
			if(document.getElementById('nature').value=='') {
<?php
		}
		else {
			echo "if(document.getElementById('nature').options[document.getElementById('nature').selectedIndex].value=='') {";
		}
?>
			alert("La nature de l'incident doit être précisée.");
			return false;
		}
		else {
			if(document.getElementById('display_heure').value=='') {
				alert("L'heure de l'incident (non vide) doit être précisée. \\nEn cas de doute sur l'heure, mettre un '?'.");
				return false;
			}
			else {
				document.getElementById('formulaire').submit();
			}
		}
	}

	function check_coche_mes_demandee(num) {
		if(document.getElementById('document_joint_'+num)) {
			temoin_check='n';
			for(i=0;i < <?php echo count($tab_mes_demandee); ?> ;i++) {
				if(document.getElementById('mesure_demandee_'+num+'_'+i)) {
					if(document.getElementById('mesure_demandee_'+num+'_'+i).checked==true) {
						temoin_check='y';
					}
					//alert('mesure_demandee_'+num+'_'+i+':'+temoin_check);
				}
			}
			if(temoin_check=='n') {
				document.getElementById('document_joint_'+num).style.display='none';
				document.getElementById('travail_pour_mesure_demandee_'+num).style.display='none';
			}
			else {
				document.getElementById('document_joint_'+num).style.display='';
				document.getElementById('travail_pour_mesure_demandee_'+num).style.display='';
			}
		}
	}
<?php
		for($loop=0;$loop<count($ele_login);$loop++) {
			echo "check_coche_mes_demandee($loop);\n";
		}
?>

//]]>
</script>
    </fieldset>
</form>
<?php
	}


	if(isset($tabid_infobulle)){
?>
<script type='text/javascript'>
//<![CDATA[
function cacher_toutes_les_infobulles() {
<?php
		if(count($tabid_infobulle)>0){
			for($i=0;$i<count($tabid_infobulle);$i++){
				echo "cacher_div('".$tabid_infobulle[$i]."');\n";
			}
		}
?>
}

//]]>
</script>
<?php
	}

}
?>
<p>
    <br />
</p>
<?php

require("../lib/footer.inc.php");
?>
