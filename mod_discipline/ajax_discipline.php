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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/ajax_discipline.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Ajax', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/ajax_discipline.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Ajax', '');;";
$sql="SELECT 1=1 FROM droits WHERE id='/mod_discipline/ajax_discipline.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_discipline/ajax_discipline.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Discipline: Ajax',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
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

if((isset($_GET['modif_sanction']))&&($_GET['modif_sanction']=="etat_effectuee")&&(isset($_GET['id_sanction']))&&(preg_match("/^[0-9]{1,}$/", $_GET['id_sanction']))) {
	check_token();

	if((in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe')))||
	(($_SESSION['statut']=='professeur')&&(sanction_saisie_par($_GET['id_sanction'], $_SESSION['login'])))||
	(($_SESSION['statut']=='professeur')&&(sanction_check_delegue($_GET['id_sanction'], $_SESSION['login'])))) {
		$sql="SELECT effectuee FROM s_sanctions WHERE id_sanction='".$_GET['id_sanction']."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<span style='color:red'>Identifiant de sanction inconnu (<em>".$_GET['id_sanction']."</em>)</span>";
		}
		else {
			$lig=mysqli_fetch_object($res);
			if($lig->effectuee=="O") {
				$valeur_alt="N";
			}
			else {
				$valeur_alt="O";
			}

			$sql="UPDATE s_sanctions SET effectuee='".$valeur_alt."' WHERE id_sanction='".$_GET['id_sanction']."';";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$update) {
				echo "<span style='color:red'>Erreur</span>";
			}
			elseif($valeur_alt=="O") {
				echo "<span style='color:green'>O</span>";
				$statut_sanction="effectuée";

				// On ne laisse pas de délégation de validation une fois la sanction effectuée.
				$sql="DELETE FROM s_sanctions_check WHERE id_sanction='".$_GET['id_sanction']."';";
				$menage=mysqli_query($GLOBALS["mysqli"], $sql);
			}
			elseif($valeur_alt=="N") {
				echo "<span style='color:red'>N</span>";
				$statut_sanction="non effectuée";
			}

			if(isset($statut_sanction)) {

				$sql="SELECT si.*,ss.login FROM s_incidents si, s_sanctions ss WHERE si.id_incident=ss.id_incident AND ss.id_sanction='".$_GET['id_sanction']."';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					$lig_incident=mysqli_fetch_object($res);
					$id_incident=$lig_incident->id_incident;
					$nature=$lig_incident->nature;

					$message_id_incident=$lig_incident->message_id;
					if($message_id_incident=="") {
						$message_id_incident=$id_incident.".".strftime("%Y%m%d%H%M%S",time()).".".mb_substr(md5(microtime()),0,6);
						$sql="UPDATE s_incidents SET message_id='$message_id_incident' WHERE id_incident='$id_incident';";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
					}

					$references_mail=$message_id_incident;

					$message_mail="Sanction";
					$sql="SELECT sts.nature FROM s_types_sanctions2 sts, s_sanctions ss WHERE ss.id_sanction='".$_GET['id_sanction']."' AND sts.id_nature=ss.id_nature_sanction;";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res2)>0) {
						$lig2=mysqli_fetch_object($res2);
						$message_mail=$lig2->nature;
					}
					$message_mail.=" n°".$_GET['id_sanction']." concernant ".get_nom_prenom_eleve($lig_incident->login)." est ".$statut_sanction;

					$tab_alerte_mail=array();
					if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
						$tab_alerte_mail[]=$_SESSION['email'];
					}
					if($lig_incident->declarant!=$_SESSION['login']) {
						$current_mail=get_valeur_champ("utilisateurs", "login='".$lig_incident->declarant."'", "email");
						if((!in_array($current_mail, $tab_alerte_mail))&&(check_mail($current_mail))) {
							$tab_alerte_mail[]=$current_mail;
						}
					}

					$info_classe_prot="";
					$liste_protagonistes_responsables="";
					$tab_alerte_classe=array();
					$sql="SELECT login FROM s_protagonistes WHERE id_incident='$id_incident' AND qualite='responsable';";
					$res_prot=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_prot)) {
						while($lig_prot=mysqli_fetch_object($res_prot)) {
				
							if(getSettingValue('mod_disc_sujet_mail_sans_nom_eleve')!="n") {
								if($liste_protagonistes_responsables!="") {$liste_protagonistes_responsables.=", ";}
								$liste_protagonistes_responsables.=$lig_prot->login;
								//echo "\$liste_protagonistes_responsables=$liste_protagonistes_responsables<br />";
							}

							// On va avoir des personnes alertees inutilement pour les élèves qui ont changé de classe.
							// NON
							$sql="SELECT DISTINCT id_classe, c.classe FROM j_eleves_classes jec, classes c WHERE jec.login='$lig_prot->login' AND jec.id_classe=c.id ORDER BY periode DESC LIMIT 1;";
							$res_clas_prot=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_clas_prot)>0) {
								$lig_clas_prot=mysqli_fetch_object($res_clas_prot);
								if((!in_array($lig_clas_prot->id_classe,$tab_alerte_classe))&&(!in_array($lig_clas_prot->id_classe,$tab_alerte_mail))) {
									$tab_alerte_classe[]=$lig_clas_prot->id_classe;
								}

								$info_classe_prot="[$lig_clas_prot->classe]";
							}
						}
					}


					if(count($tab_alerte_classe)>0) {
						$tab_param_mail=array();
						$destinataires=get_destinataires_mail_alerte_discipline($tab_alerte_classe, $nature);

						if($destinataires!="") {
							//$texte_mail=$message_mail."\n\n"."Message: ".preg_replace('#<br />#',"\n",$msg);
			
							$subject = "[GEPI][".ucfirst($mod_disc_terme_incident)." n°$id_incident]".$info_classe_prot.$liste_protagonistes_responsables;

							$headers = "";
							if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
								$headers.="Reply-to:".$_SESSION['email']."\r\n";
								$tab_param_mail['replyto']=$_SESSION['email'];
							}

							// Non: Il ne faut pas prendre le message_id de l'incident pour un nouveau message... il faut le mettre seulement en référence
							//if(isset($message_id)) {$headers .= "Message-id: $message_id\r\n";}
							if(isset($references_mail)) {
								$headers .= "References: $references_mail\r\n";
								$tab_param_mail['references']=$references_mail;
							}

							$texte_mail="Bonjour,\n\n".$message_mail."\n\nCordialement.\n-- \n".civ_nom_prenom($_SESSION['login']);

							// On envoie le mail
							$envoi = envoi_mail($subject, $texte_mail, $destinataires, $headers, "plain", $tab_param_mail);
						}
					}


				}
			}

		}
	}
	else {
		echo "<span style='color:red'>Modification non autorisée.</span>";
	}

	die();
}

?>
