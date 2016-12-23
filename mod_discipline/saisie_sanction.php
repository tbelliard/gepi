<?php

/*
 *
 * Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/saisie_sanction.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Saisie sanction', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/saisie_sanction.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Saisie sanction', '');;";
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

$acces_visu_eleve=acces('/eleves/visu_eleve.php', $_SESSION['statut']);

$msg="";

$id_incident=isset($_POST['id_incident']) ? $_POST['id_incident'] : (isset($_GET['id_incident']) ? $_GET['id_incident'] : NULL);
$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);
$id_sanction=isset($_POST['id_sanction']) ? $_POST['id_sanction'] : (isset($_GET['id_sanction']) ? $_GET['id_sanction'] : NULL);
$id_report=isset($_POST['id_report']) ? $_POST['id_report'] : (isset($_GET['id_report']) ? $_GET['id_report'] : NULL);

$odt = isset($_POST["odt"]) ? $_POST["odt"] : (isset($_GET["odt"]) ? $_GET["odt"] : Null);

//debug_var();

if(isset($_POST['enregistrer_sanction'])) {
	check_token();

	$autre_protagoniste_meme_sanction=isset($_POST['autre_protagoniste_meme_sanction']) ? $_POST['autre_protagoniste_meme_sanction'] : array();

	$message_mail="";

	$id_nature_sanction=$_POST['traitement'];
	$sql="SELECT * FROM s_types_sanctions2 WHERE id_nature='".$id_nature_sanction."';";
	$res_ns=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ns)==0) {
		$msg.="Le type de ".$mod_disc_terme_sanction." est inconnu???<br />";
	}
	else {
		$type_sanction=old_mysql_result($res_ns, 0, "type");
		$nature_sanction=old_mysql_result($res_ns, 0, "nature");

		if($type_sanction=='retenue') {

			$date_retenue=isset($_POST['date_retenue']) ? $_POST['date_retenue'] : NULL;
			$heure_debut=isset($_POST['heure_debut']) ? $_POST['heure_debut'] : NULL;
			$heure_debut_main=isset($_POST['heure_debut_main']) ? $_POST['heure_debut_main'] : '00:00';
			$duree_retenue=isset($_POST['duree_retenue']) ? $_POST['duree_retenue'] : 1;
			$lieu_retenue=isset($_POST['lieu_retenue']) ? $_POST['lieu_retenue'] : NULL;
			$materiel=isset($_POST['materiel']) ? $_POST['materiel'] : NULL;

			$report_demande=isset($_POST['report_demande']) ? $_POST['report_demande'] : NULL;
			$choix_motif_report=isset($_POST['choix_motif_report']) ? $_POST['choix_motif_report'] : NULL;

			$deleguer_check=isset($_POST['deleguer_check']) ? $_POST['deleguer_check'] : NULL;

			$duree_retenue=preg_replace("/[^0-9.]/","",preg_replace("/,/",".",$duree_retenue));
			if($duree_retenue=="") {
				$duree_retenue=1;
				$msg.="La durée de $nature_sanction saisie n'était pas correcte. Elle a été remplacée par '1'.<r />";
			}

			if(!isset($date_retenue)) {
				$annee = strftime("%Y");
				$mois = strftime("%m");
				$jour = strftime("%d");
				//$display_date = $jour."/".$mois."/".$annee;
			}
			else {
				$jour =  mb_substr($date_retenue,0,2);
				$mois =  mb_substr($date_retenue,3,2);
				$annee = mb_substr($date_retenue,6,4);
			}

			if(!checkdate($mois,$jour,$annee)) {
				$annee = strftime("%Y");
				$mois = strftime("%m");
				$jour = strftime("%d");

				$msg.="La date proposée n'était pas valide. Elle a été remplacée par la date du jour courant.";
			}
			$date_retenue="$annee-$mois-$jour";

			if (isset($NON_PROTECT["travail"])){
				$travail=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["travail"]));
				// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
				$travail=suppression_sauts_de_lignes_surnumeraires($travail);
			}
			else {
				$travail="";
			}

			if(isset($id_sanction)) {
		
				// traitement du report de la retenue (seulement si elle existe déjà !)
				if ($report_demande=="OK") { // c'est un report
					// on récupère les informations précédente dans la table s_retenues pour les inscrire dans s_reports
					$sql="SELECT * FROM s_retenues WHERE id_sanction='$id_sanction';";
					//echo "$sql<br />\n";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)==0) {
						$msg.="La $nature_sanction n°$id_sanction n'existe pas dans 's_retenues'.<br />Elle ne peut pas être reportée.<br />";
					}
					else {
						$lig=mysqli_fetch_object($res);
						$id_retenue=$lig->id_retenue;
						$ancienne_date=$lig->date;
						$ancienne_duree=$lig->duree;
					}
					// enregistrement des données du report dans la table s_report
					$choix_motif_report = str_replace("_", " ", $choix_motif_report);

					//$sql="INSERT INTO s_reports SET id_sanction='$id_sanction', id_type_sanction='$id_retenue', nature_sanction='retenue', date='$ancienne_date', informations='Durée : ".$ancienne_duree."H', motif_report='$choix_motif_report';";
					$sql="INSERT INTO s_reports SET id_sanction='$id_sanction', id_type_sanction='$id_retenue', nature_sanction='$nature_sanction', date='$ancienne_date', informations='Durée : ".$ancienne_duree."H', motif_report='$choix_motif_report';";
					//echo "$sql<br />\n";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$res) {
						$msg.="Erreur lors de l'insertion des informations de report dans 's_reports'.<br />";
					}
					else {
						$message_mail.="La $nature_sanction n°$id_sanction initialement prévue le ".$ancienne_date." pour une durée de ".$ancienne_duree."H est reportée à une date ultérieure (motif: ".$choix_motif_report.").\n";
					}
				}
		
				// Modification???
				$sql="SELECT 1=1 FROM s_sanctions WHERE id_sanction='$id_sanction';";
				//echo "$sql<br />\n";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)==0) {
					$msg.="La ".$mod_disc_terme_sanction." n°$id_sanction n'existe pas dans 's_sanctions'.<br />Elle ne peut pas être mise à jour.<br />";
				}
				else {
					$sql="SELECT 1=1 FROM s_retenues WHERE id_sanction='$id_sanction';";
					//echo "$sql<br />\n";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)==0) {
						$msg.="La $nature_sanction n°$id_sanction n'existe pas dans 's_retenues'.<br />Elle ne peut pas être mise à jour.<br />";
					}
					else {
						//Eric
						//choix de l'heure de retenue à conserver (champs sasie manuellement ou par la liste déroulante
						//par defaut la liste déroulante
						if ($heure_debut_main !='00:00') {
							$heure_debut=$heure_debut_main;
						}
						//$sql="UPDATE s_retenues SET date='$date_retenue', heure_debut='$heure_debut', duree='$duree_retenue', travail='$travail', lieu='$lieu_retenue', effectuee='N' WHERE id_sanction='$id_sanction';";
						$sql="UPDATE s_retenues SET date='$date_retenue', heure_debut='$heure_debut', duree='$duree_retenue', travail='$travail', lieu='$lieu_retenue', materiel='$materiel' WHERE id_sanction='$id_sanction';";
						//echo "$sql<br />\n";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$update) {
							$msg.="Erreur lors de la mise à jour de la ".$mod_disc_terme_sanction." '$nature_sanction' n°$id_sanction.<br />";
						}
						else {
							if(preg_match("/^[0-9]/", $heure_debut)) {
								$heure_debut_descr="à ".$heure_debut;
							}
							else {
								$heure_debut_descr="à ".$heure_debut;
								$tmp_h=get_mysql_heure($heure_debut);
								if($tmp_h!="") {
									$heure_debut_descr="en ".$heure_debut;
									$heure_debut_descr.=" (".$tmp_h.")";
								}
							}

							$message_mail.="La $nature_sanction n°$id_sanction est définie pour le $date_retenue $heure_debut_descr pour une durée de ".$duree_retenue;
							if((preg_match("/^[0-9]{1,}$/", $duree_retenue))||(preg_match("/^[0-9]{1,}.[0-9]{1,}$/", $duree_retenue))||(preg_match("/^[0-9]{1,},[0-9]{1,}$/", $duree_retenue))) {
								$message_mail.="H";
							}
							$message_mail.=".\n";
							if($lieu_retenue!="") {
								$message_mail.="Lieu: $lieu_retenue\n";
							}
							if($materiel!="") {
								$message_mail.="Matériel: $materiel\n";
							}

							if(isset($deleguer_check)) {
								$sql="SELECT * FROM s_sanctions_check WHERE id_sanction='$id_sanction';";
								$res_deleguer_check=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_deleguer_check)==0) {
									if($deleguer_check!="") {
										$sql="INSERT INTO s_sanctions_check SET id_sanction='$id_sanction', login='".$deleguer_check."';";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$insert) {
											$msg.="Erreur lors de l'enregistrement de la délégation du pointage effectuée/non_effectuée.<br />";
										}
										else {
											$message_mail.="\n\nLa validation de l'état effectuée ou non de la ".$mod_disc_terme_sanction." est déléguée à ".civ_nom_prenom($deleguer_check)."\n";
										}
									}
								}
								else {
									if($deleguer_check=="") {
										$sql="DELETE FROM s_sanctions_check WHERE id_sanction='$id_sanction';";
										$del=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$del) {
											$msg.="Erreur lors de la suppression de la délégation du pointage effectuée/non_effectuée.<br />";
										}
										else {
											$message_mail.="\n\nSuppression de la délégation de la validation de l'état effectuée ou non de la ".$mod_disc_terme_sanction."\n";
										}
									}
									else {
										$lig_deleguer_check=mysqli_fetch_object($res_deleguer_check);
										if($lig_deleguer_check->login!=$deleguer_check) {
											$sql="UPDATE s_sanctions_check SET login='".$deleguer_check."' WHERE id_sanction='$id_sanction';";
											$update=mysqli_query($GLOBALS["mysqli"], $sql);
											if(!$update) {
												$msg.="Erreur lors de la mise à jour de la délégation du pointage effectuée/non_effectuée.<br />";
											}
											else {
												$message_mail.="\n\nLa validation de l'état effectuée ou non de la ".$mod_disc_terme_sanction." est maintenant déléguée à ".civ_nom_prenom($deleguer_check)."\n";
											}
										}
									}
								}
							}
						}
					}
				}
			}
			else {
				//$sql="INSERT INTO s_sanctions SET login='$ele_login', nature='retenue', id_incident='$id_incident';";
				//$sql="INSERT INTO s_sanctions SET login='$ele_login', nature='retenue', id_incident='$id_incident';";
				$sql="INSERT INTO s_sanctions SET login='$ele_login', id_nature_sanction='$id_nature_sanction', nature='".addslashes($nature_sanction)."', id_incident='$id_incident', saisie_par='".$_SESSION['login']."';";
				//echo "$sql<br />\n";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$res) {
					$msg.="Erreur lors de l'insertion de la ".$mod_disc_terme_sanction." dans 's_sanctions'.<br />";
				}
				else {
					$id_sanction=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
					//Eric
					//choix de l'heure de retenue à conserver (champs sasie manuellement ou par la liste déroulante
					//par defaut la liste déroulante
					if ($heure_debut_main !='00:00') {
						$heure_debut=$heure_debut_main;
					}
					//$sql="INSERT INTO s_retenues SET id_sanction='$id_sanction', date='$date_retenue', heure_debut='$heure_debut', duree='$duree_retenue', travail='$travail', lieu='$lieu_retenue', effectuee='N';";
					$sql="INSERT INTO s_retenues SET id_sanction='$id_sanction', date='$date_retenue', heure_debut='$heure_debut', duree='$duree_retenue', travail='$travail', lieu='$lieu_retenue', materiel='$materiel';";
					//echo "$sql<br />\n";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if($res) {
						if(preg_match("/^[0-9]/", $heure_debut)) {
							$heure_debut_descr="à ".$heure_debut;
						}
						else {
							$heure_debut_descr="à ".$heure_debut;
							$tmp_h=get_mysql_heure($heure_debut);
							if($tmp_h!="") {
								$heure_debut_descr="en ".$heure_debut;
								$heure_debut_descr.=" (".$tmp_h.")";
							}
						}
						$message_mail.="Une $nature_sanction (n°$id_sanction) concernant ".get_nom_prenom_eleve($ele_login, "avec_classe")." est définie pour le ".$date_retenue." ".$heure_debut_descr." pour une durée de ".$duree_retenue;
						if((preg_match("/^[0-9]{1,}$/", $duree_retenue))||(preg_match("/^[0-9]{1,}.[0-9]{1,}$/", $duree_retenue))||(preg_match("/^[0-9]{1,},[0-9]{1,}$/", $duree_retenue))) {
							$message_mail.="H";
						}
						$message_mail.=".\n";
						if($travail!="") {
							$message_mail.="Travail: $travail\n";
						}
						if($lieu_retenue!="") {
							$message_mail.="Lieu: $lieu_retenue\n";
						}
						if($materiel!="") {
							$message_mail.="Matériel: $materiel\n";
						}

						if($deleguer_check!="") {
							$sql="INSERT INTO s_sanctions_check SET id_sanction='$id_sanction', login='".$deleguer_check."';";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$insert) {
								$msg.="Erreur lors de l'enregistrement de la délégation du pointage effectuée/non_effectuée.<br />";
							}
							else {
								$message_mail.="\n\nLa validation de l'état effectuée ou non de la ".$mod_disc_terme_sanction." est déléguée à ".civ_nom_prenom($deleguer_check)."\n";
							}
						}

						// Ménage:
						$sql="DELETE FROM messagerie WHERE sujet LIKE '% (incident n°".$id_incident.")';";
						$menage=mysqli_query($GLOBALS["mysqli"], $sql);
					}
				}

				if(count($autre_protagoniste_meme_sanction)>0) {
					for($loop=0;$loop<count($autre_protagoniste_meme_sanction);$loop++) {
						$sql="INSERT INTO s_sanctions SET login='$autre_protagoniste_meme_sanction[$loop]', id_nature_sanction='$id_nature_sanction', nature='".addslashes($nature_sanction)."', id_incident='$id_incident', saisie_par='".$_SESSION['login']."';";
						//echo "$sql<br />\n";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$res) {
							$msg.="Erreur lors de l'insertion de la ".$mod_disc_terme_sanction." dans 's_sanctions' pour $autre_protagoniste_meme_sanction[$loop].<br />";
						}
						else {
							$tmp_id_sanction=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
							$tab_tmp_id_sanction[]=$tmp_id_sanction;
							//Eric
							//choix de l'heure de retenue à conserver (champs sasie manuellement ou par la liste déroulante
							//par defaut la liste déroulante
							if ($heure_debut_main !='00:00') {
								$heure_debut=$heure_debut_main;
							}
							//$sql="INSERT INTO s_retenues SET id_sanction='$id_sanction', date='$date_retenue', heure_debut='$heure_debut', duree='$duree_retenue', travail='$travail', lieu='$lieu_retenue', effectuee='N';";
							$sql="INSERT INTO s_retenues SET id_sanction='$tmp_id_sanction', date='$date_retenue', heure_debut='$heure_debut', duree='$duree_retenue', travail='$travail', lieu='$lieu_retenue', materiel='$materiel';";
							//echo "$sql<br />\n";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if($res) {
								$designation_autre_protagoniste=get_nom_prenom_eleve($autre_protagoniste_meme_sanction[$loop], "avec_classe");
								$message_mail.="Même sanction (n°".$tmp_id_sanction.") pour ".$designation_autre_protagoniste."\n";

								if($deleguer_check!="") {
									$sql="INSERT INTO s_sanctions_check SET id_sanction='$tmp_id_sanction', login='".$deleguer_check."';";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$insert) {
										$msg.="Erreur lors de l'enregistrement de la délégation du pointage effectuée/non_effectuée pour ".$designation_autre_protagoniste.".<br />";
									}
								}
							}
						}
					}
				}
			}

		}
		elseif($type_sanction=='exclusion') {

			$date_debut=isset($_POST['date_debut']) ? $_POST['date_debut'] : NULL;
			$heure_debut=isset($_POST['heure_debut']) ? $_POST['heure_debut'] : NULL;
			$date_fin=isset($_POST['date_fin']) ? $_POST['date_fin'] : NULL;
			$heure_fin=isset($_POST['heure_fin']) ? $_POST['heure_fin'] : NULL;
			$lieu_exclusion=isset($_POST['lieu_exclusion']) ? $_POST['lieu_exclusion'] : NULL;
			$nombre_jours=isset($_POST['nombre_jours']) ? $_POST['nombre_jours'] : NULL;
			$qualification_faits=isset($_POST['qualification_faits']) ? $_POST['qualification_faits'] : NULL;
			$numero_courrier=isset($_POST['numero_courrier']) ? $_POST['numero_courrier'] : NULL;
			$type_exclusion=isset($_POST['type_exclusion']) ? $_POST['type_exclusion'] : NULL;
			$signataire=isset($_POST['signataire']) ? $_POST['signataire'] : NULL;
		

			if(!isset($date_debut)) {
				$annee = strftime("%Y");
				$mois = strftime("%m");
				$jour = strftime("%d");
				//$display_date = $jour."/".$mois."/".$annee;
			}
			else {
				$jour =  mb_substr($date_debut,0,2);
				$mois =  mb_substr($date_debut,3,2);
				$annee = mb_substr($date_debut,6,4);
			}

			if(!checkdate($mois,$jour,$annee)) {
				$annee = strftime("%Y");
				$mois = strftime("%m");
				$jour = strftime("%d");

				$msg.="La date proposée n'était pas valide. Elle a été remplacée par la date du jour courant.";
			}
			$date_debut="$annee-$mois-$jour";
			$tmp_timestamp_debut=mktime(0, 0, 0, $mois, $jour, $annee);

			if(!isset($date_fin)) {
				if(!isset($date_debut)) {
					$annee = strftime("%Y");
					$mois = strftime("%m");
					$jour = strftime("%d");
				}
				else {
					$jour =  mb_substr($date_debut,0,2);
					$mois =  mb_substr($date_debut,3,2);
					$annee = mb_substr($date_debut,6,4);
				}
			}
			else {
				$jour =  mb_substr($date_fin,0,2);
				$mois =  mb_substr($date_fin,3,2);
				$annee = mb_substr($date_fin,6,4);
			}

			if(!checkdate($mois,$jour,$annee)) {
				$annee = strftime("%Y");
				$mois = strftime("%m");
				$jour = strftime("%d");

				$msg.="La date proposée n'était pas valide. Elle a été remplacée par la date du jour courant.";
			}
			$date_fin="$annee-$mois-$jour";
			$tmp_timestamp_fin=mktime(0, 0, 0, $mois, $jour, $annee);

			if($tmp_timestamp_debut>$tmp_timestamp_fin) {
				//echo "\$date_debut=$date_debut<br />";
				//echo "\$date_fin=$date_fin<br />";
				//echo "\$tmp_timestamp_debut=$tmp_timestamp_debut<br />";
				//echo "\$tmp_timestamp_fin=$tmp_timestamp_fin<br />";

				//echo "MODIF:<br />";
				$tmp_date_debut=$date_fin;
				//echo "\$tmp_date_debut=$date_fin<br />";
				$date_fin=$date_debut;
				//echo "\$date_fin=$date_debut<br />";
				$date_debut=$tmp_date_debut;
				//echo "\$date_debut=$tmp_date_debut<br />";

				$msg.="La date de fin était antérieure à la date de début de l'exclusion.<br />Les dates ont été interverties.<br />";
			}

			if (isset($NON_PROTECT["travail"])){
				$travail=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["travail"]));
				// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
				$travail=suppression_sauts_de_lignes_surnumeraires($travail);
			}
			else {
				$travail="";
			}
		
			if (isset($NON_PROTECT["qualification_faits"])){
				$qualification_faits=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["qualification_faits"]));
				// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
				$qualification_faits=suppression_sauts_de_lignes_surnumeraires($qualification_faits);
			}
			else {
				$qualification_faits="";
			}
		
			if(isset($id_sanction)) {
				// Modification???
				$sql="SELECT 1=1 FROM s_sanctions WHERE id_sanction='$id_sanction';";
				//echo "$sql<br />\n";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)==0) {
					$msg.="La ".$mod_disc_terme_sanction." n°$id_sanction n'existe pas dans 's_sanctions'.<br />Elle ne peut pas être mise à jour.<br />";
				}
				else {
					$sql="SELECT 1=1 FROM s_exclusions WHERE id_sanction='$id_sanction';";
					//echo "$sql<br />\n";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)==0) {
						$msg.="La ".$mod_disc_terme_sanction." n°$id_sanction n'existe pas dans 's_exclusions'.<br />Elle ne peut pas être mise à jour.<br />";
					}
					else {
						$sql="UPDATE s_exclusions SET date_debut='$date_debut', heure_debut='$heure_debut', date_fin='$date_fin', heure_fin='$heure_fin', travail='$travail', lieu='$lieu_exclusion', nombre_jours='$nombre_jours', qualification_faits='$qualification_faits', num_courrier='$numero_courrier', type_exclusion='$type_exclusion', id_signataire='$signataire' WHERE id_sanction='$id_sanction';";
						//echo "$sql<br />\n";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$update) {
							$msg.="Erreur lors de la mise à jour de la ".$mod_disc_terme_sanction." '$nature_sanction' n°$id_sanction.<br />";
						}
						else {

							if(preg_match("/^[0-9]/", $heure_debut)) {
								$heure_debut_descr="à ".$heure_debut;
							}
							else {
								$heure_debut_descr="à ".$heure_debut;
								$tmp_h=get_mysql_heure($heure_debut);
								if($tmp_h!="") {
									$heure_debut_descr="en ".$heure_debut;
									$heure_debut_descr.=" (".$heure_debut.")";
								}
							}

							if(preg_match("/^[0-9]/", $heure_fin)) {
								$heure_fin_descr="à ".$heure_fin;
							}
							else {
								$heure_fin_descr="à ".$heure_fin;
								$tmp_h=get_mysql_heure($heure_fin, "fin");
								if($tmp_h!="") {
									$heure_fin_descr="en ".$heure_fin;
									$heure_fin_descr.=" (fin de créneau ".$tmp_h.")";
								}
							}

							$message_mail.="Une $nature_sanction (type $type_exclusion) (sanction n°$id_sanction) concernant ".get_nom_prenom_eleve($ele_login, "avec_classe")." est mise à jour du $date_debut ".$heure_debut_descr." au $date_fin ".$heure_fin_descr.".\nNombre de jours: $nombre_jours\nQualification des faits: $qualification_faits\n";
							if($travail!="") {
								$message_mail.="Travail: $travail\n";
							}
							if($lieu_exclusion!="") {
								$message_mail.="Lieu: $lieu_exclusion\n";
							}
							if($materiel!="") {
								$message_mail.="Matériel: $materiel\n";
							}
							if($signataire!="") {
								$message_mail.="Signataire: $signataire\n";
							}
						}
					}
				}
			}
			else {
				$sql="INSERT INTO s_sanctions SET login='$ele_login', id_nature_sanction='$id_nature_sanction', nature='".addslashes($nature_sanction)."', id_incident='$id_incident', saisie_par='".$_SESSION['login']."';";
				//echo "$sql<br />\n";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$res) {
					$msg.="Erreur lors de l'insertion de la ".$mod_disc_terme_sanction." dans 's_sanctions'.<br />";
				}
				else {
					$id_sanction=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

					$sql="INSERT INTO s_exclusions SET id_sanction='$id_sanction', date_debut='$date_debut', heure_debut='$heure_debut', date_fin='$date_fin', heure_fin='$heure_fin', travail='$travail', lieu='$lieu_exclusion', nombre_jours='$nombre_jours', qualification_faits='$qualification_faits', num_courrier='$numero_courrier', type_exclusion='$type_exclusion', id_signataire='$signataire';";
					//echo "$sql<br />\n";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if($res) {
						if(preg_match("/^[0-9]/", $heure_debut)) {
							$heure_debut_descr="à ".$heure_debut;
						}
						else {
							$heure_debut_descr="à ".$heure_debut;
							$tmp_h=get_mysql_heure($heure_debut);
							if($tmp_h!="") {
								$heure_debut_descr="en ".$heure_debut;
								$heure_debut_descr.=" (".$tmp_h.")";
							}
						}

						if(preg_match("/^[0-9]/", $heure_fin)) {
							$heure_fin_descr="à ".$heure_fin;
						}
						else {
							$heure_fin_descr="à ".$heure_fin;
							$tmp_h=get_mysql_heure($heure_fin);
							if($tmp_h!="") {
								$heure_fin_descr="en ".$heure_fin;
								$heure_fin_descr.=" (".$tmp_h.")";
							}
						}

						$message_mail.="Une $nature_sanction (type $type_exclusion) (sanction n°$id_sanction) concernant ".get_nom_prenom_eleve($ele_login, "avec_classe")." est définie du $date_debut ".$heure_debut_descr." au $date_fin ".$heure_fin_descr.".\nNombre de jours: $nombre_jours\nQualification des faits: $qualification_faits\n";
						if($travail!="") {
							$message_mail.="Travail: $travail\n";
						}
						if($lieu_exclusion!="") {
							$message_mail.="Lieu: $lieu_exclusion\n";
						}
						/*
						if($materiel!="") {
							$message_mail.="Matériel: $materiel\n";
						}
						*/
						if($signataire!="") {
							$message_mail.="Signataire: $signataire\n";
						}

						// Ménage:
						$sql="DELETE FROM messagerie WHERE sujet LIKE '% (incident n°".$id_incident.")';";
						$menage=mysqli_query($GLOBALS["mysqli"], $sql);
					}
				}

				if(count($autre_protagoniste_meme_sanction)>0) {
					for($loop=0;$loop<count($autre_protagoniste_meme_sanction);$loop++) {
						$sql="INSERT INTO s_sanctions SET login='".$autre_protagoniste_meme_sanction[$loop]."', id_nature_sanction='$id_nature_sanction', nature='".addslashes($nature_sanction)."', id_incident='$id_incident', saisie_par='".$_SESSION['login']."';";
						//echo "$sql<br />\n";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$res) {
							$msg.="Erreur lors de l'insertion de la ".$mod_disc_terme_sanction." dans 's_sanctions' pour $autre_protagoniste_meme_sanction[$loop].<br />";
						}
						else {
							$tmp_id_sanction=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
							$tab_tmp_id_sanction[]=$tmp_id_sanction;

							$sql="INSERT INTO s_exclusions SET id_sanction='$tmp_id_sanction', date_debut='$date_debut', heure_debut='$heure_debut', date_fin='$date_fin', heure_fin='$heure_fin', travail='$travail', lieu='$lieu_exclusion', nombre_jours='$nombre_jours', qualification_faits='$qualification_faits', num_courrier='$numero_courrier', type_exclusion='$type_exclusion', id_signataire='$signataire';";
							//echo "$sql<br />\n";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if($res) {
								$message_mail.="Même sanction (n°".$tmp_id_sanction.") pour ".get_nom_prenom_eleve($autre_protagoniste_meme_sanction[$loop], "avec_classe")."\n";
							}
						}
					}
				}
			}

		}
		elseif($type_sanction=='travail') {

			$date_retour=isset($_POST['date_retour']) ? $_POST['date_retour'] : NULL;
			$heure_retour=isset($_POST['heure_retour']) ? $_POST['heure_retour'] : NULL;

			if(!isset($date_retour)) {
				$annee = strftime("%Y");
				$mois = strftime("%m");
				$jour = strftime("%d");
				//$display_date = $jour."/".$mois."/".$annee;
			}
			else {
				$jour =  mb_substr($date_retour,0,2);
				$mois =  mb_substr($date_retour,3,2);
				$annee = mb_substr($date_retour,6,4);
			}

			if(!checkdate($mois,$jour,$annee)) {
				$annee = strftime("%Y");
				$mois = strftime("%m");
				$jour = strftime("%d");

				$msg.="La date proposée n'était pas valide. Elle a été remplacée par la date du jour courant.";
			}
			$date_retour="$annee-$mois-$jour";

			if (isset($NON_PROTECT["travail"])){
				$travail=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["travail"]));
				// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
				$travail=suppression_sauts_de_lignes_surnumeraires($travail);
			}
			else {
				$travail="";
			}

			if(isset($id_sanction)) {
				// Modification???
				$sql="SELECT 1=1 FROM s_sanctions WHERE id_sanction='$id_sanction';";
				//echo "$sql<br />\n";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)==0) {
					$msg.="La ".$mod_disc_terme_sanction." n°$id_sanction n'existe pas dans 's_sanctions'.<br />Elle ne peut pas être mise à jour.<br />";
				}
				else {
					$sql="SELECT 1=1 FROM s_travail WHERE id_sanction='$id_sanction';";
					//echo "$sql<br />\n";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)==0) {
						$msg.="Le travail n°$id_sanction n'existe pas dans 's_travail'.<br />Il ne peut pas être mis à jour.<br />";
					}
					else {
						//$sql="UPDATE s_travail SET date_retour='$date_retour', heure_retour='$heure_retour', travail='$travail', effectuee='N' WHERE id_sanction='$id_sanction';";
						$sql="UPDATE s_travail SET date_retour='$date_retour', heure_retour='$heure_retour', travail='$travail' WHERE id_sanction='$id_sanction';";
						//echo "$sql<br />\n";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$update) {
							$msg.="Erreur lors de la mise à jour de la ".$mod_disc_terme_sanction." '$nature_sanction' n°$id_sanction.<br />";
						}
					}
				}
			}
			else {
				$sql="INSERT INTO s_sanctions SET login='$ele_login', id_nature_sanction='$id_nature_sanction', nature='".addslashes($nature_sanction)."', id_incident='$id_incident', saisie_par='".$_SESSION['login']."';";
				//echo "$sql<br />\n";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$res) {
					$msg.="Erreur lors de l'insertion de la ".$mod_disc_terme_sanction." dans 's_sanctions'.<br />";
				}
				else {
					$id_sanction=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

					//$sql="INSERT INTO s_travail SET id_sanction='$id_sanction', date_retour='$date_retour', heure_retour='$heure_retour', travail='$travail', effectuee='N';";
					$sql="INSERT INTO s_travail SET id_sanction='$id_sanction', date_retour='$date_retour', heure_retour='$heure_retour', travail='$travail';";
					//echo "$sql<br />\n";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if($res) {
						$message_mail.="Un $nature_sanction supplémentaire (sanction n°$id_sanction) a été donné à ".get_nom_prenom_eleve($ele_login, "avec_classe")." a été définie pour le $date_retour à $heure_retour.\n";
						if($travail!="") {
							$message_mail.="Travail: $travail\n";
						}

						// Ménage:
						$sql="DELETE FROM messagerie WHERE sujet LIKE '% (incident n°".$id_incident.")';";
						$menage=mysqli_query($GLOBALS["mysqli"], $sql);
					}
				}

				if(count($autre_protagoniste_meme_sanction)>0) {
					for($loop=0;$loop<count($autre_protagoniste_meme_sanction);$loop++) {
						$sql="INSERT INTO s_sanctions SET login='$autre_protagoniste_meme_sanction[$loop]', id_nature_sanction='$id_nature_sanction', nature='".addslashes($nature_sanction)."', id_incident='$id_incident', saisie_par='".$_SESSION['login']."';";
						//echo "$sql<br />\n";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$res) {
							$msg.="Erreur lors de l'insertion de la ".$mod_disc_terme_sanction." dans 's_sanctions' pour $autre_protagoniste_meme_sanction[$loop]<br />";
						}
						else {
							$tmp_id_sanction=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
							$tab_tmp_id_sanction[]=$tmp_id_sanction;

							//$sql="INSERT INTO s_travail SET id_sanction='$id_sanction', date_retour='$date_retour', heure_retour='$heure_retour', travail='$travail', effectuee='N';";
							$sql="INSERT INTO s_travail SET id_sanction='$tmp_id_sanction', date_retour='$date_retour', heure_retour='$heure_retour', travail='$travail';";
							//echo "$sql<br />\n";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if($res) {
								$message_mail.="Même sanction (n°".$tmp_id_sanction.") pour ".get_nom_prenom_eleve($autre_protagoniste_meme_sanction[$loop], "avec_classe")." a été définie pour le $date_retour à $heure_retour.\nTravail: $travail\n";
							}
						}
					}
				}
			}
		}
		else {
			/*
			$id_nature=$_POST['traitement'];
			$sql="SELECT * FROM s_types_sanctions WHERE id_nature='".$id_nature."';";
			//echo "$sql<br />\n";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$lig=mysql_fetch_object($res);
				$type_sanction=$lig->nature;
			*/
				if (isset($NON_PROTECT["description"])){
					$description=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["description"]));
					// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
					$description=suppression_sauts_de_lignes_surnumeraires($description);
				}
				else {
					$description="";
				}

				if(isset($id_sanction)) {
					// Modification???
					$sql="SELECT 1=1 FROM s_sanctions WHERE id_sanction='$id_sanction';";
					//echo "$sql<br />\n";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)==0) {
						$msg.="La ".$mod_disc_terme_sanction." n°$id_sanction n'existe pas dans 's_sanctions'.<br />Elle ne peut pas être mise à jour.<br />";
					}
					else {
						$sql="SELECT 1=1 FROM s_autres_sanctions WHERE id_sanction='$id_sanction';";
						//echo "$sql<br />\n";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)==0) {
							$msg.="La ".$mod_disc_terme_sanction." n°$id_sanction n'existe pas dans 's_autres_sanctions'.<br />Elle ne peut pas être mis à jour.<br />";
						}
						else {
							$sql="UPDATE s_autres_sanctions SET description='$description', id_nature='$id_nature_sanction' WHERE id_sanction='$id_sanction';";
							//echo "$sql<br />\n";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$update) {
								//$msg.="Erreur lors de la mise à jour de la sanction '$type_sanction' n°$id_sanction.<br />";
								$msg.="Erreur lors de la mise à jour de la ".$mod_disc_terme_sanction." '$nature_sanction' n°$id_sanction.<br />";
							}
							else {
								$message_mail.="Sanction ($nature_sanction) (type $id_nature_sanction) (n°$id_sanction) mise à jour pour ".get_nom_prenom_eleve($ele_login, "avec_classe")."\nDescription: $description\n";
							}
						}
					}
				}
				else {
					$sql="INSERT INTO s_sanctions SET login='$ele_login', id_nature_sanction='$id_nature_sanction', nature='".addslashes($nature_sanction)."', id_incident='$id_incident', saisie_par='".$_SESSION['login']."';";
					//echo "$sql<br />\n";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$res) {
						$msg.="Erreur lors de l'insertion de la ".$mod_disc_terme_sanction." dans 's_sanctions'.<br />";
					}
					else {
						$id_sanction=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

						$sql="INSERT INTO s_autres_sanctions SET id_sanction='$id_sanction', id_nature='$id_nature_sanction', description='$description';";
						//echo "$sql<br />\n";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$res) {
							//$msg.="Erreur lors de l'enregistrement de la sanction '$type_sanction' n°$id_sanction.<br />";
							$msg.="Erreur lors de l'enregistrement de la ".$mod_disc_terme_sanction." '$nature_sanction' n°$id_sanction.<br />";
						}
						else {
							$message_mail.="Sanction ($nature_sanction) (type $id_nature_sanction) (n°$id_sanction) a été donnée à ".get_nom_prenom_eleve($ele_login, "avec_classe")."\nDescription: $description\n";
						}

						// Ménage:
						$sql="DELETE FROM messagerie WHERE sujet LIKE '% (incident n°".$id_incident.")';";
						$menage=mysqli_query($GLOBALS["mysqli"], $sql);
					}

					if(count($autre_protagoniste_meme_sanction)>0) {
						for($loop=0;$loop<count($autre_protagoniste_meme_sanction);$loop++) {
							$sql="INSERT INTO s_sanctions SET login='$autre_protagoniste_meme_sanction[$loop]', id_nature_sanction='$id_nature_sanction', nature='".addslashes($nature_sanction)."', id_incident='$id_incident', saisie_par='".$_SESSION['login']."';";
							//echo "$sql<br />\n";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res) {
								$msg.="Erreur lors de l'insertion de la ".$mod_disc_terme_sanction." dans 's_sanctions' pour $autre_protagoniste_meme_sanction[$loop]<br />";
							}
							else {
								$tmp_id_sanction=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
								$tab_tmp_id_sanction[]=$tmp_id_sanction;

								$sql="INSERT INTO s_autres_sanctions SET id_sanction='$tmp_id_sanction', id_nature='$id_nature_sanction', description='$description';";
								//echo "$sql<br />\n";
								$res=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$res) {
									//$msg.="Erreur lors de l'enregistrement de la sanction '$type_sanction' n°$tmp_id_sanction.<br />";
									$msg.="Erreur lors de l'enregistrement de la ".$mod_disc_terme_sanction." '$nature_sanction' n°$tmp_id_sanction.<br />";
								}
								else {
									$message_mail.="Même sanction (n°".$tmp_id_sanction.") pour ".get_nom_prenom_eleve($autre_protagoniste_meme_sanction[$loop], "avec_classe")."\n";
								}
							}
						}
					}
				}
			//}
		}
	}

	if($message_mail!="") {
		//20150925
		$envoi_mail_actif=getSettingValue('envoi_mail_actif');
		if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
			$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
		}

		if($envoi_mail_actif=='y') {
			$sql="SELECT * FROM s_incidents WHERE id_incident='$id_incident';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig_incident=mysqli_fetch_object($res);
				$nature=$lig_incident->nature;

				$message_id_incident=$lig_incident->message_id;
				if($message_id_incident=="") {
					$message_id_incident=$id_incident.".".strftime("%Y%m%d%H%M%S",time()).".".mb_substr(md5(microtime()),0,6);
					$sql="UPDATE s_incidents SET message_id='$message_id_incident' WHERE id_incident='$id_incident';";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
				}

				$references_mail=$message_id_incident;
				//$tab_param_mail['message_id']=$message_id;
				//$tab_param_mail['references']=$message_id;

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

					$sql="SELECT DISTINCT u.login, u.email, u.nom, u.prenom, u.civilite FROM utilisateurs u, s_sanctions_check ssc, s_sanctions ss WHERE u.login=ssc.login AND ssc.id_sanction=ss.id_sanction AND ss.id_incident='$id_incident';";
					//echo "$sql<br />";
					$res_ssc=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_ssc)>0) {
						// Normalement, on ne fait qu'un tour dans la boucle
						while($lig_ssc=mysqli_fetch_object($res_ssc)) {
							if((check_mail($lig_ssc->email))&&(!preg_match("/^$lig_ssc->email$/", $lig_ssc->email))&&(!preg_match("/^$lig_ssc->email,/", $lig_ssc->email))&&(!preg_match("/ $lig_ssc->email$/", $lig_ssc->email))&&(!preg_match("/ $lig_ssc->email,/", $lig_ssc->email))) {
								if($destinataires!="") {
									$destinataires.=", ";
								}
								$destinataires.=$lig_ssc->email;
								$tab_param_mail['destinataire'][]=$lig_ssc->email;
							}

							$contenu_cor=mysqli_real_escape_string($GLOBALS['mysqli'], "Le pointage du fait qu'une sanction a été effectuée vous a été délégué.<br />Vous pourrez effectuer ce pointage dans le <a href='$gepiPath/mod_discipline/index.php'>module Discipline</a>");
							$id_message=set_message2($contenu_cor,time(),time()+3600*24*7,time()+3600*24*7,"_",$lig_ssc->login);
							//echo "\$id_message=$id_message<br />";
							ajout_bouton_supprimer_message($contenu_cor,$id_message);
						}
					}

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

						$texte_mail="Bonjour,\n\n".$message_mail."\nCordialement.\n-- \n".civ_nom_prenom($_SESSION['login']);

						// On envoie le mail
						$envoi = envoi_mail($subject, $texte_mail, $destinataires, $headers, "plain", $tab_param_mail);
					}
				}
			}
		}
	}

	if(isset($id_sanction)) {
		$temoin_modif_fichier=0;

		unset($suppr_doc_joint);
		$suppr_doc_joint=isset($_POST['suppr_doc_joint']) ? $_POST['suppr_doc_joint'] : array();
		for($loop=0;$loop<count($suppr_doc_joint);$loop++) {
			if((preg_match("/\.\./",$suppr_doc_joint[$loop]))||(preg_match("#/#",$suppr_doc_joint[$loop]))) {
				$msg.="Nom de fichier ".$suppr_doc_joint[$loop]." invalide<br />";
			}
			else {
				$fichier_courant="../$dossier_documents_discipline/incident_".$id_incident."/sanction_".$id_sanction."/".$suppr_doc_joint[$loop];
				if(!unlink($fichier_courant)) {
					$msg.="Erreur lors de la suppression de $fichier_courant<br />";
				}
			}
		}
		$temoin_modif_fichier+=count($suppr_doc_joint);

		$ajouter_doc_joint=isset($_POST['ajouter_doc_joint']) ? $_POST['ajouter_doc_joint'] : array();
		for($loop=0;$loop<count($ajouter_doc_joint);$loop++) {
			if((preg_match("/\.\./",$ajouter_doc_joint[$loop]))||(preg_match("#/#",$ajouter_doc_joint[$loop]))) {
				$msg.="Nom de fichier ".$ajouter_doc_joint[$loop]." invalide<br />";
			}
			else {
				$chemin_src="../$dossier_documents_discipline/incident_".$id_incident."/mesures/".$ele_login;
				$chemin_dest="../$dossier_documents_discipline/incident_".$id_incident."/sanction_".$id_sanction;

				$fichier_src=$chemin_src."/".$ajouter_doc_joint[$loop];
				$fichier_dest=$chemin_dest."/".$ajouter_doc_joint[$loop];
				if(file_exists($fichier_src)) {
					if($discipline_droits_mkdir=="") {
						@mkdir($chemin_dest,0770,true);
					}
					else {
						@mkdir("../$dossier_documents_discipline");
						@mkdir("../$dossier_documents_discipline/incident_".$id_incident);
						@mkdir($chemin_dest);
					}
					copy($fichier_src, $fichier_dest);

					/*
					if(!unlink($fichier_src)) {
						$msg.="Erreur lors de la suppression de $fichier_src<br />";
					}
					*/
				}

				if((isset($tab_tmp_id_sanction))&&(count($tab_tmp_id_sanction)>0)) {
					for($loop2=0;$loop2<count($tab_tmp_id_sanction);$loop2++) {
						$chemin_dest="../$dossier_documents_discipline/incident_".$id_incident."/sanction_".$tab_tmp_id_sanction[$loop2];
						$fichier_dest=$chemin_dest."/".$ajouter_doc_joint[$loop];
						if(file_exists($fichier_src)) {
							if($discipline_droits_mkdir=="") {
								@mkdir($chemin_dest,0770,true);
							}
							else {
								@mkdir("../$dossier_documents_discipline");
								@mkdir("../$dossier_documents_discipline/incident_".$id_incident);
								@mkdir($chemin_dest);
							}
							copy($fichier_src, $fichier_dest);
						}
					}
				}
			}
		}
		$temoin_modif_fichier+=count($ajouter_doc_joint);

		unset($document_joint);
		$document_joint=isset($_FILES["document_joint"]) ? $_FILES["document_joint"] : NULL;
		if((isset($document_joint['tmp_name']))&&($document_joint['tmp_name']!="")) {
			//$msg.="\$document_joint['tmp_name']=".$document_joint['tmp_name']."<br />";
			if(!is_uploaded_file($document_joint['tmp_name'])) {
				$msg.="L'upload du fichier a échoué.<br />\n";
			}
			else{
				if(!file_exists($document_joint['tmp_name'])){
					$msg.="Le fichier aurait été uploadé... mais ne serait pas présent/conservé.<br />\n";
				}
				else {
					//echo "<p>Le fichier a été uploadé.</p>\n";

					$source_file=$document_joint['tmp_name'];
					$dossier_courant="../$dossier_documents_discipline/incident_".$id_incident."/sanction_".$id_sanction;
					if(!file_exists($dossier_courant)) {
						if($discipline_droits_mkdir=="") {
							mkdir($dossier_courant, 0770, true);
						}
						else {
							@mkdir("../$dossier_documents_discipline");
							@mkdir("../$dossier_documents_discipline/incident_".$id_incident);
							@mkdir($dossier_courant);
						}
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
			$temoin_modif_fichier++;
		}

		if($temoin_modif_fichier>0) {
			$mode="modif";
			$valeur=$_POST['traitement'];
		}
	}



}

if(($mode=="suppr_sanction")&&(isset($id_sanction))) {
	check_token();

	$suppression_sanction_possible="y";
	if(($_SESSION['statut']=='professeur')&&(!sanction_saisie_par($id_sanction, $_SESSION['login']))) {
		$suppression_sanction_possible="n";
		$msg.="Vous n'avez pas le droit de supprimer cette sanction.<br />";
		// Tentative intrusion?
	}

	if($suppression_sanction_possible=="y") {
		$msg.=suppr_doc_joints_sanction($id_sanction);

		$sql="DELETE FROM s_travail WHERE id_sanction='$id_sanction';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$sql="DELETE FROM s_exclusions WHERE id_sanction='$id_sanction';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$sql="DELETE FROM s_retenues WHERE id_sanction='$id_sanction';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$sql="DELETE FROM s_autres_sanctions WHERE id_sanction='$id_sanction';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$sql="DELETE FROM s_sanctions WHERE id_sanction='$id_sanction';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$sql="DELETE FROM s_reports WHERE id_sanction='$id_sanction';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
	}
}

if(($mode=="suppr_report")&&(isset($id_report))) {
	check_token();

	$sql="DELETE FROM s_reports WHERE id_report='$id_report';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
}

if(isset($odt)&&
	(($odt=="exclusion")||
	($odt=="retenue")||
	($odt=="travail")||
	($odt=="autre"))) { //impression de l'exclusion en Ooo
	//recup des informations à exporter dans l'ODT
	//Nom et prenom eleve;
	if ($ele_login != null && $ele_login != '') {
		$eleve_current=  EleveQuery::create()->filterByLogin($ele_login)->findOne();
		$nom_ele = $eleve_current->getNom();
		$prenom_ele= $eleve_current->getPrenom();

		// Pour la page mod_ooo/retenue.php
		$nom_prenom_eleve=$nom_ele." ".$prenom_ele;
		$classe_ele="";

		$id_classe_ele= $eleve_current->getClasse()->getId();
	}
	//classe de l'élève
	if ($id_classe_ele != null && $id_classe_ele != '') {
		$classe = ClasseQuery::create()->findOneById($id_classe_ele);
		if ($classe != null) {
		    $classe_ele = $classe->getNom();
		}
	}

	require_once("./lib_tbs_courrier.php"); //fonction pour le traitement de l'adresse

	$tab_adresse=adresse_responsables($ele_login); 

	// Pour le moment on ne traite que pour le R1
	$ad_nom_resp=$tab_adresse[0]['civilite'];
	$adr1_resp=$tab_adresse[0]['adresse1'];
	$adr2_resp=$tab_adresse[0]['adresse2'];
	$adr3_resp=$tab_adresse[0]['adresse3'];
	$cp_ville_resp=$tab_adresse[0]['cp_ville'];
	$civilite_courrier=$tab_adresse[0]['civilite_courrier'];

	// Pour la page mod_ooo/retenue.php
	$cp_resp=$tab_adresse[0]['cp'];
	$commune_resp=$tab_adresse[0]['commune'];

	//Contenu du courrier
	if ($id_sanction != null && $id_sanction != '') {

		$sql="SELECT * FROM s_sanctions WHERE id_sanction='$id_sanction';";
		$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_sanction)>0) {
			$lig_sanction=mysqli_fetch_object($res_sanction);
			$id_nature_sanction=$lig_sanction->id_nature_sanction;
			$nature_sanction=$lig_sanction->nature;
		}

		if($odt=='retenue') {
			$sql="SELECT * FROM s_retenues WHERE id_sanction='$id_sanction';";
			$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_sanction)==0) {
				$date="";
				$heure_debut="";
				$duree="";
				$travail="";
				$lieu="";
				$materiel="";
			}
			else {
				$lig_sanction=mysqli_fetch_object($res_sanction);
				$date=$lig_sanction->date;
				$heure_debut=$lig_sanction->heure_debut;
				$duree=$lig_sanction->duree;
				$travail=preg_replace("/^Travail : /", "", $lig_sanction->travail);
				$lieu=ucfirst($lig_sanction->lieu);
				$materiel=$lig_sanction->materiel;
			}
			$date_retenue=formate_date($date);
			if(preg_match("/^[0-9]/", $heure_debut)) {
				$h_deb=$heure_debut;
			}
			else {
				$h_deb=get_mysql_heure($heure_debut);
				if(preg_match("/^[0-9]{1,2}:[0-9]{2}:[0-9]{2}$/", $h_deb)) {
					$tmp_h_deb=explode(":", $h_deb);
					$h_deb=$tmp_h_deb[0]."H".$tmp_h_deb[1];
				}
			}

			$num_incident=$id_incident;

			$ets_nom=getSettingValue("gepiSchoolName");
			$ets_adr1=getSettingValue("gepiSchoolAdress1");
			$ets_adr2=getSettingValue("gepiSchoolAdress2");
			$ets_cp=getSettingValue("gepiSchoolZipCode");
			$ets_ville=getSettingValue("gepiSchoolCity");
			$ets_tel=getSettingValue("gepiSchoolTel");
			$ets_fax=getSettingValue("gepiSchoolFax");
			$ets_email=getSettingValue("gepiSchoolEmail");

			$motif="MOTIF A EXTRAIRE";
			$texte_report="TEXTE REPORT";
			$nom_resp="NOM DECLARANT";
			$fct_resp="FONCTION DECLARANT";

			$motif=get_valeur_champ("s_incidents", "id_incident='".$id_incident."'", "description");
			$nature_incident=get_valeur_champ("s_incidents", "id_incident='".$id_incident."'", "nature");

			$nb_report=nombre_reports($id_sanction,0);
			if ($nb_report<>0) {
				$texte_report="REPORT N° ".$nb_report;
			} else {
				$texte_report="";
			}

			$sql="SELECT login,nom,prenom,civilite,statut FROM utilisateurs u, s_incidents si WHERE id_incident='$id_incident' AND u.login=si.declarant;";
			//echo "$sql<br />\n";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);
				$nom_resp = $lig->civilite." ".strtoupper($lig->nom)." ".ucfirst(mb_substr($lig->prenom,0,1)).".";

				if($lig->statut=='autre') {
					$sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
													WHERE du.login_user = '".$lig->login."'
													AND du.id_statut = ds.id;";
					$query = mysqli_query($GLOBALS["mysqli"], $sql);
					$result = mysqli_fetch_array($query);

					//var retenue
					$fct_resp = $result['nom_statut'] ;
				}
				else {
					$fct_resp = $lig->statut ;
				}
			}

			$fct_resp = ucfirst($fct_resp);
		}
		elseif($odt=='exclusion') {
			$sql="SELECT * FROM s_exclusions WHERE id_sanction='$id_sanction';";
			$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_sanction)==0) {
				$num_courrier="";
				$type_exclusion="";
				$qualification_faits="";
				$duree_exclusion="";
				$date_debut="";
				$date_fin="";
				$signataire="";
			}
			else {
				$lig_sanction=mysqli_fetch_object($res_sanction);
				$num_courrier=$lig_sanction->num_courrier;
				$type_exclusion=$lig_sanction->type_exclusion;
				$qualification_faits=$lig_sanction->qualification_faits;
				$duree_exclusion=$lig_sanction->nombre_jours;
				$date_debut=$lig_sanction->date_debut;
				$date_fin=$lig_sanction->date_fin;
				$signataire=$lig_sanction->id_signataire;
			}

			$sql="SELECT * FROM s_delegation WHERE id_delegation='$signataire';";
			$res_delegation=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_delegation)==0) {
				$fct_delegation="";
				$fct_autorite="";
				$nom_autorite="";
			}
			else {
				$lig_delegation=mysqli_fetch_object($res_delegation);
				$fct_delegation=$lig_delegation->fct_delegation;
				$fct_autorite=$lig_delegation->fct_autorite;
				$nom_autorite=$lig_delegation->nom_autorite;
			}
		}
		elseif($odt=='travail') {
			$nom_autorite=civ_nom_prenom($_SESSION['login'],'');

			$sql="SELECT * FROM s_incidents WHERE id_incident='$id_incident';";
			$res_incident=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_incident)==0) {
				$qualification_faits="";
			}
			else {
				$qualification_faits=old_mysql_result($res_incident,0,"description");
			}

			$sql="SELECT * FROM s_travail WHERE id_sanction='$id_sanction';";
			$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_sanction)==0) {
				$date_retour="";
				$heure_retour="";
				$travail="";
			}
			else {
				$lig_sanction=mysqli_fetch_object($res_sanction);
				$date_retour=formate_date($lig_sanction->date_retour);
				$heure_retour=$lig_sanction->heure_retour;
				$travail=$lig_sanction->travail;
			}
		}
		elseif($odt=='autre') {
			$nom_autorite=civ_nom_prenom($_SESSION['login'],'');

			$sql="SELECT * FROM s_incidents WHERE id_incident='$id_incident';";
			$res_incident=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_incident)==0) {
				$qualification_faits="";
			}
			else {
				$qualification_faits=old_mysql_result($res_incident,0,"description");
			}

			//$sql="SELECT sas.*, sts.nature FROM s_autres_sanctions sas, s_types_sanctions sts WHERE sas.id_sanction='$id_sanction' AND sas.id_nature=sts.id_nature;";
			$sql="SELECT sas.*, sts.nature FROM s_autres_sanctions sas, s_types_sanctions2 sts WHERE sas.id_sanction='$id_sanction' AND sas.id_nature=sts.id_nature;";
			$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_sanction)==0) {
				$nature_sanction="";
				$description_sanction="";
			}
			else {
				$lig_sanction=mysqli_fetch_object($res_sanction);
				$nature_sanction=$lig_sanction->nature;
				$description_sanction=$lig_sanction->description;
			}
		}
	}
/*
echo "<pre>";
print_r($classe_ele);
echo "</pre>";
echo "classe=$classe_ele<br />";
die();
*/
	$export = array();
	if($odt=='retenue') {
		$export[] = Array('nom' => $nom_ele, 'prenom' => $prenom_ele, 'classe' => $classe_ele,
					  'ad_nom_resp' => $ad_nom_resp, 
					  'adr1_resp' => $adr1_resp, 'adr2_resp' => $adr2_resp, 'adr3_resp' => $adr3_resp,
					  'cp_ville_resp' => $cp_ville_resp,
					  'civilite_courrier' => $civilite_courrier,
					  'date' => $date,
					  'duree' => $duree,
					  'travail' => $travail,
					  'lieu' => $lieu,
					  'materiel' => $materiel,

					  'cp_resp' => $cp_resp,
					  'commune_resp' => $commune_resp,

					  'ets_nom' => $ets_nom,
					  'ets_adr1' => $ets_adr1,
					  'ets_adr2' => $ets_adr2,
					  'ets_cp' => $ets_cp,
					  'ets_ville' => $ets_ville,
					  'ets_tel' => $ets_tel,
					  'ets_fax' => $ets_fax,
					  'ets_email' => $ets_email,

					  'date_retenue' => $date_retenue,
					  'h_deb' => $h_deb,
					  'num_incident' => $id_incident,
					  'nature_incident' => $nature_incident,

					  'nom_prenom_eleve' => $nom_prenom_eleve,
					  'classe_ele' => $classe_ele,
					  'classe' => $classe_ele,

					  'travail' => $travail,

					  'motif' => $motif,
					  'texte_report' => $texte_report,
					  'nom_resp' => $nom_resp,
					  'fct_resp' => $fct_resp

					  );
	}
	elseif($odt=='exclusion') {
		//conversion des dates
		//Voici les deux tableaux des jours et des mois traduits en français
		$nom_jour_fr = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
		$mois_fr = Array("", "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", 
				"septembre", "octobre", "novembre", "décembre");
		// on extrait la date du jour pour la date de debut
		list($annee, $mois, $jour) = explode('-', $date_debut);
		$mois=intval($mois);
		$timestamp = mktime (0, 0, 0, $mois, $jour, $annee);
		// affichage du jour de la semaine
		$date_debut = $nom_jour_fr[date("w",$timestamp)].' '.$jour.' '.$mois_fr[$mois].' '.$annee; 
		// on extrait la date du jour pour la date de fin
		list($annee, $mois, $jour) = explode('-', $date_fin); 
		$mois=intval($mois);
		$timestamp = mktime (0, 0, 0, $mois, $jour, $annee);
		// affichage du jour de la semaine
		$date_fin = $nom_jour_fr[date("w",$timestamp)].' '.$jour.' '.$mois_fr[$mois].' '.$annee; 

		if ($date_debut==$date_fin) {
			$chaine_date = "du $date_debut";
			$journee = "la journée";
		} else {
			$chaine_date = "du $date_debut au $date_fin inclus";
			$journee = "les journées";
		}

		$export[] = Array('nom' => $nom_ele, 'prenom' => $prenom_ele, 'classe' => $classe_ele,
					  'ad_nom_resp' => $ad_nom_resp, 
					  'adr1_resp' => $adr1_resp, 'adr2_resp' => $adr2_resp, 'adr3_resp' => $adr3_resp,
					  'cp_ville_resp' => $cp_ville_resp,
					  'civilite_courrier' => $civilite_courrier,
					  'num_courrier' => $num_courrier,
					  'type_exclusion' => $type_exclusion,
					  'qualif_faits' => $qualification_faits,
					  'duree_exclusion' => $duree_exclusion,
					  'date_debut' => $date_debut,
					  'date_fin' => $date_fin,
					  'chaine_date' => $chaine_date,
					  'journee' => $journee,
					  'fonction_delegation' => $fct_delegation,
					  'fonction_autorite' => $fct_autorite,
					  'nom_autorite' => $nom_autorite
					  );
	}
	elseif($odt=='travail') {
		$export[] = Array('nom' => $nom_ele, 'prenom' => $prenom_ele, 'classe' => $classe_ele,
					  'ad_nom_resp' => $ad_nom_resp, 
					  'adr1_resp' => $adr1_resp, 'adr2_resp' => $adr2_resp, 'adr3_resp' => $adr3_resp,
					  'cp_ville_resp' => $cp_ville_resp,
					  'civilite_courrier' => $civilite_courrier,
					  'id_incident' => $id_incident,
					  'id_sanction' => $id_sanction,
					  'qualif_faits' => $qualification_faits,
					  'date_retour' => $date_retour,
					  'heure_retour' => $heure_retour,
					  'travail' => $travail,
					  'nom_autorite' => $nom_autorite
					  );
	}
	elseif($odt=='autre') {
		$export[] = Array('nom' => $nom_ele, 'prenom' => $prenom_ele, 'classe' => $classe_ele,
					  'ad_nom_resp' => $ad_nom_resp, 
					  'adr1_resp' => $adr1_resp, 'adr2_resp' => $adr2_resp, 'adr3_resp' => $adr3_resp,
					  'cp_ville_resp' => $cp_ville_resp,
					  'civilite_courrier' => $civilite_courrier,
					  'id_incident' => $id_incident,
					  'id_sanction' => $id_sanction,
					  'qualif_faits' => $qualification_faits,
					  'nature_sanction' => $nature_sanction,
					  'description_sanction' => $description_sanction,
					  'nom_autorite' => $nom_autorite
					  );
	}
	/*
	echo "<pre>";
	echo print_r($mois);
	echo "</pre>";
	*/

	// génération Ooo
	include_once '../mod_abs2/lib/function.php'; //pour la fonction repertoire_modeles
	include_once '../orm/helpers/AbsencesNotificationHelper.php'; // pour la fonction tbs_str et MergeInfosEtab
	if($odt=='retenue') {
		$fichier_modele_discipline='retenue.odt';
		$prefixe_fichier_odt="retenue";
	}
	elseif($odt=='exclusion') {
		$fichier_modele_discipline='discipline_exclusion.odt';
		$prefixe_fichier_odt="exclusion";
	}
	elseif($odt=='travail') {
		$fichier_modele_discipline='discipline_travail.odt';
		$prefixe_fichier_odt="travail";
	}
	elseif($odt=='autre') {
		$fichier_modele_discipline='discipline_autre.odt';
		$prefixe_fichier_odt="autre_sanction";
	}

	$extraction_bilans = repertoire_modeles($fichier_modele_discipline);

	if(isset($id_nature_sanction)) {
		// Tester l'existence du modèle
		if ($_SESSION['rne']!='') {
			$rne=$_SESSION['rne']."/";
		} else {
			$rne='';
		}

		if(file_exists("../mod_ooo/mes_modeles/".$rne."discipline_sanction_".$id_nature_sanction.".odt")) {
			$extraction_bilans="../mod_ooo/mes_modeles/".$rne."discipline_sanction_".$id_nature_sanction.".odt";
		}

		$prefixe_fichier_odt=ensure_ascii($nature_sanction);
	}

	//Coordonnées etab
	$TBS = AbsencesNotificationHelper::MergeInfosEtab($extraction_bilans);

	if($odt=='retenue') {
		$TBS->MergeBlock('var', $export);
	}
	else {
		$TBS->MergeBlock('export', $export);
	}

	$nom_fichier = $prefixe_fichier_odt.'_'. $nom_ele.'_'.$prenom_ele.'_'.$id_sanction. '.odt';
	$TBS->Show(OPENTBS_DOWNLOAD + TBS_EXIT, $nom_fichier);
} //fin Ooo

$avec_js_et_css_edt="y";

$utilisation_prototype="ok";
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Discipline: Traitement/".$mod_disc_terme_sanction;
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

$page="saisie_sanction.php";

echo "<script type='text/javascript'>
	// La fonction est ramenée de saisie_sanction.inc.php dans saisie_sanction.php
	// parce que les javascript définis dans la page saisie_sanction.inc.php appelée via ajax ne sont pas pris en compte.
	function occupation_lieu_heure(id_sanction) {
";
if(acces("/mod_discipline/occupation_lieu_heure.php", $_SESSION['statut'])) {
	echo "
		lieu=document.getElementById('lieu_retenue').value;
		date_retenue=document.getElementById('date_retenue').value;
		heure_debut=document.getElementById('heure_debut').options[document.getElementById('heure_debut').selectedIndex].value;
		duree_retenue=document.getElementById('duree_retenue').value;

		centrerpopup('occupation_lieu_heure.php?id_sanction='+id_sanction+'&lieu='+lieu+'&date='+date_retenue+'&heure='+heure_debut+'&duree='+duree_retenue,600,480,'scrollbars=yes,statusbar=no,resizable=yes');
";
}
echo "
	}";

echo "
	function maj_div_liste_retenues_jour() {
";
if(acces("/mod_discipline/liste_retenues_jour.php", $_SESSION['statut'])) {
echo "
		if($('date_retenue')) {
			date=$('date_retenue').value;
			//alert('date='+date);
			new Ajax.Updater($('div_liste_retenues_jour'),'liste_retenues_jour.php?date='+date,{method: 'get'});
		}
";
}
echo "
	}

	//maj_div_liste_retenues_jour();
</script>\n";

//=====================================================
// MENU
echo "<div id='s_menu' style='float:right; border: 1px solid black; background-color: white; width: 17em;'>\n";
echo "<ul style='margin:0px;'>\n";
echo "<li>\n";
echo "<a href='traiter_incident.php'";
echo " onclick='return confirm_abandon (this, change, \"$themessage\")'";
echo ">Liste des ".$mod_disc_terme_incident."s</a>";
echo "</li>\n";

echo "<li>\n";
echo "<a href='liste_sanctions_jour.php'";
echo " onclick='return confirm_abandon (this, change, \"$themessage\")'";
echo ">Liste des sanctions du jour</a>";
echo "</li>\n";
echo "</ul>\n";
echo "</div>\n";
//=====================================================

echo "<p class='bold'><a href='index.php'";
echo " onclick='return confirm_abandon (this, change, \"$themessage\")'";
echo "><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour index</a>\n";

if(!isset($id_incident)) {
	echo "</p>\n";

	echo "<p><strong>Erreur&nbsp;:</strong> Il faut commencer par sélectionner l'".$mod_disc_terme_incident.".</p>\n";
	require("../lib/footer.inc.php");
	die();
}

echo " | <a href='saisie_incident.php?id_incident=$id_incident&amp;step=2'";
echo " onclick='return confirm_abandon (this, change, \"$themessage\")'";
echo ">Retour ".$mod_disc_terme_incident."</a>\n";

//if(!isset($mode)) {
if((!isset($mode))||($mode=="suppr_sanction")||($mode=="suppr_report")) {
	//echo " | <a href='traiter_incident.php'>Liste des incidents</a>\n";
	echo "</p>\n";

	// Affichage des protagonistes:
	$sql="SELECT * FROM s_protagonistes WHERE id_incident='$id_incident' ORDER BY statut,qualite,login;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		//echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
		//echo "<input type='hidden' name='step' value='$step' />\n";

		echo "<p class='bold'>Protagonistes de l'".$mod_disc_terme_incident." n°$id_incident&nbsp;:</p>\n";

		echo "<blockquote>\n";

		echo "<table class='boireaus' border='1' summary='Protagonistes'>\n";
		echo "<tr>\n";
		echo "<th>Individu</th>\n";
		echo "<th>Statut</th>\n";
		//echo "<th>Qualité dans l'incident</th>\n";
		echo "<th>Rôle dans l'".$mod_disc_terme_incident."</th>\n";
		echo "<th>Traitement/".$mod_disc_terme_sanction."</th>\n";
		echo "</tr>\n";
		$alt=1;
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";

			//Individu
			if($lig->statut=='eleve') {
				echo "<td>";
				$sql="SELECT nom,prenom FROM eleves WHERE login='$lig->login';";
				//echo "$sql<br />\n";
				$res2=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res2)>0) {
					$lig2=mysqli_fetch_object($res2);
					echo ucfirst(mb_strtolower($lig2->prenom))." ".mb_strtoupper($lig2->nom);
					echo infobulle_photo($lig->login);
				}
				else {
					echo "ERREUR: Login inconnu";
				}

				echo "</td>\n";
				echo "<td>";
				echo "élève (<i>";
				$tmp_tab=get_class_from_ele_login($lig->login);
				if(isset($tmp_tab['liste_nbsp'])) {echo $tmp_tab['liste_nbsp'];}
				echo "</i>)";
				echo "</td>\n";
			}
			else {
				echo "<td>";
				$sql="SELECT nom,prenom,civilite FROM utilisateurs WHERE login='$lig->login';";
				//echo "$sql<br />\n";
				$res2=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res2)>0) {
					$lig2=mysqli_fetch_object($res2);
					echo ucfirst(mb_strtolower($lig2->prenom))." ".mb_strtoupper($lig2->nom);
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
					$query = mysqli_query($GLOBALS["mysqli"], $sql);
					$result = mysqli_fetch_array($query);

					echo "<td>".$result['nom_statut']."</td>\n";
				}
				else {
					echo "<td>$lig->statut</td>\n";
				}
			}

			echo "<td>\n";
			//echo "<input type='hidden' name='ele_login[$cpt]' value=\"$lig->login\" />\n";
			echo $lig->qualite;
			echo "</td>\n";

			//echo "<td style='padding:3px;'>\n";
			echo "<td>\n";
			if($lig->statut=='eleve') {

				// Retenues
				$passage_report=false; //traiter les cas ou une sanction correspond à plusieurs retenues
				//$sql="SELECT * FROM s_sanctions s, s_retenues sr WHERE s.id_incident=$id_incident AND s.login='".$lig->login."' AND sr.id_sanction=s.id_sanction ORDER BY sr.date, sr.heure_debut;";
				$sql="SELECT s.*, sr.*, sts.nature AS nature_sts FROM s_sanctions s, s_retenues sr, s_types_sanctions2 sts WHERE s.id_incident=$id_incident AND s.login='".$lig->login."' AND sr.id_sanction=s.id_sanction AND sts.id_nature=s.id_nature_sanction AND sts.type='retenue' ORDER BY sts.nature, sr.date, sr.heure_debut;";
				//echo "$sql<br />\n";
				$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
				$res_sanction_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_sanction)>0) {
					echo "<table class='boireaus' border='1' summary='Retenues' style='margin:2px;'>\n";
					echo "<tr>\n";
					echo "<th>Nature</th>\n";
					echo "<th>Date</th>\n";
					echo "<th>Heure</th>\n";
					echo "<th>Durée</th>\n";
					echo "<th>Lieu</th>\n";
					echo "<th>Travail</th>\n";
					
					$lig_sanction_tmp=mysqli_fetch_object($res_sanction_tmp);
					$nombre_de_report=nombre_reports($lig_sanction_tmp->id_sanction,0);
					if ($nombre_de_report <> 0) {
					   echo "<th>Nbre report</th>\n";
					   $passage_report = true;
					}
//Eric
					echo "<th>Imprimer</th>\n";
//
					// 20141106
					echo "<th>Effectuée</th>\n";

					echo "<th>Suppr</th>\n";
					echo "</tr>\n";
					$alt_b=1;
					while($lig_sanction=mysqli_fetch_object($res_sanction)) {
						$alt_b=$alt_b*(-1);
						echo "<tr class='lig$alt_b'>\n";
						//echo "<td>Retenue</td>\n";
						//echo "<td><a href='".$_SERVER['PHP_SELF']."?mode=modif&amp;valeur=retenue&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$lig->login'>".ucfirst($lig_sanction->nature_sts)."</a></td>\n";
						echo "<td><a href='".$_SERVER['PHP_SELF']."?mode=modif&amp;valeur=$lig_sanction->id_nature_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$lig->login' title=\"Consulter/Modifier.\">".ucfirst($lig_sanction->nature_sts)."</a></td>\n";
						echo "<td>".formate_date($lig_sanction->date)."</td>\n";
						echo "<td>$lig_sanction->heure_debut</td>\n";
						echo "<td>$lig_sanction->duree</td>\n";
						echo "<td>$lig_sanction->lieu</td>\n";
						//echo "<td>".nl2br($lig_sanction->travail)."</td>\n";
						echo "<td>";

						$texte=nl2br($lig_sanction->travail);
						$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);

						$texte.="<br />À apporter&nbsp;: ".$lig_sanction->materiel."<br />";

						if($tmp_doc_joints!="") {
							if($texte!="") {$texte.="<br />";}
							$texte.="<strong>Documents joints</strong>&nbsp;:<br />";
							$texte.=$tmp_doc_joints;
						}

						$tabdiv_infobulle[]=creer_div_infobulle("div_travail_sanction_$lig_sanction->id_sanction","Travail (sanction n°$lig_sanction->id_sanction)","",$texte,"",20,0,'y','y','n','n');

						echo " <a href='#' onmouseover=\"delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onclick=\"return false;\" title=\"Afficher les détails en infobulle.\">Détails</a>";
						echo "</td>\n";
//Eric
						if ($passage_report) {
							$nombre_de_report=nombre_reports($lig_sanction->id_sanction,0);
							if ($nombre_de_report <> 0) {
								echo "<td>\n";
								echo $nombre_de_report;
								echo "</td>";
							} else {
							    echo "<td>\n";
								echo "";
								echo "</td>";
							}
						}
						
						echo "<td>";
						if ($gepiSettings['active_mod_ooo'] == 'y') { //impression avec mod_ooo
							//echo "<a href='../mod_ooo/retenue.php?mode=module_retenue&amp;id_incident=$id_incident&amp;id_sanction=$lig_sanction->id_sanction&amp;ele_login=$lig->login".add_token_in_url()."' title='Imprimer la retenue'><img src='../images/icons/print.png' width='16' height='16' alt='Imprimer Retenue' /></a>\n";

							if(responsables_adresses_separees($lig->login)) {
								$tmp_tab_resp=get_resp_from_ele_login($lig->login);
								for($loop_resp=0;$loop_resp<count($tmp_tab_resp);$loop_resp++) {
									if($loop_resp>0) {echo "&nbsp;";}
									echo "<a href='../mod_ooo/retenue.php?mode=module_retenue&amp;id_incident=$id_incident&amp;id_sanction=$lig_sanction->id_sanction&amp;ele_login=$lig->login&amp;pers_id=".$tmp_tab_resp[$loop_resp]['pers_id'].add_token_in_url()."' title=\"Imprimer la retenue pour ".$tmp_tab_resp[$loop_resp]['designation']."\"><img src='../images/icons/print.png' width='16' height='16' alt='Imprimer Retenue' /></a>\n";
								}
							}
							else {
								echo "<a href='../mod_ooo/retenue.php?mode=module_retenue&amp;id_incident=$id_incident&amp;id_sanction=$lig_sanction->id_sanction&amp;ele_login=$lig->login".add_token_in_url()."' title='Imprimer la retenue'><img src='../images/icons/print.png' width='16' height='16' alt='Imprimer Retenue' /></a>\n";
							}

						}
						else {
							echo "<span title=\"Le module openDocument n'est pas activé. La génération de fichier ODT destiné à l'impression n'est pas possible.\">-</span>";
						}
						echo "</td>\n";

						// 20141106
						// Sanction effectuée
						echo "<td";
						if((in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe')))||
						(($_SESSION['statut']=='professeur')&&(sanction_saisie_par($lig_sanction->id_sanction, $_SESSION['login'])))) {
							echo " title=\"Cliquez pour marquer la sanction comme effectuée ou non effectuée\">";
							if($lig_sanction->effectuee=="O") {
								$valeur_alt="N";
							}
							else {
								$valeur_alt="O";
							}

							echo "<a href='#' onclick=\"maj_etat_sanction_effectuee_ou_non($lig_sanction->id_sanction, '$valeur_alt')\" title=\"Marquer la sanction comme effectuée ou non effectuée\">";
							echo "<span id='span_sanction_effectuee_".$lig_sanction->id_sanction."'>";
							if($lig_sanction->effectuee=="O") {
								echo "<span style='color:green'>O</span>";
							}
							else {
								echo "<span style='color:red'>N</span>";
							}
							echo "</span>";
							echo "</a>";
						}
						else {
							echo ">";
							if($lig_sanction->effectuee=="O") {echo "<span style='color:green'>O</span>";} else {echo "<span style='color:red'>N</span>";}
						}
						echo "</td>\n";

						// Suppression
						echo "<td>";
						if(($_SESSION['statut']!='professeur')||($lig_sanction->saisie_par==$_SESSION['login'])) {
							echo "<a href='".$_SERVER['PHP_SELF']."?mode=suppr_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident".add_token_in_url()."' title='Supprimer la sanction n°$lig_sanction->id_sanction'><img src='../images/icons/delete.png' width='16' height='16' alt='Supprimer la sanction n°$lig_sanction->id_sanction' /></a>";
						}
						else {
							echo "<img src='../images/disabled.png' class='icone16' title=\"Un professeur ne peut supprimer que ses propres sanctions.\" />";
						}
						echo "</td>\n";
						echo "</tr>\n";
					}
					echo "</table>\n";
				}

				// Exclusions
				//$sql="SELECT * FROM s_sanctions s, s_exclusions se WHERE s.id_incident=$id_incident AND s.login='".$lig->login."' AND se.id_sanction=s.id_sanction ORDER BY se.date_debut, se.heure_debut;";
				$sql="SELECT s.*, se.*, sts.nature AS nature_sts  FROM s_sanctions s, s_exclusions se, s_types_sanctions2 sts WHERE s.id_incident=$id_incident AND s.login='".$lig->login."' AND se.id_sanction=s.id_sanction AND sts.id_nature=s.id_nature_sanction AND sts.type='exclusion' ORDER BY sts.nature, se.date_debut, se.heure_debut;";
				//echo "$sql<br />\n";
				$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_sanction)>0) {
					echo "<table class='boireaus' border='1' summary='Exclusions' style='margin:2px;'>\n";
					echo "<tr>\n";
					echo "<th>Nature</th>\n";
					echo "<th>Date début</th>\n";
					echo "<th>Heure début</th>\n";
					echo "<th>Date fin</th>\n";
					echo "<th>Heure fin</th>\n";
					echo "<th>Lieu</th>\n";
					echo "<th>Travail</th>\n";
					echo "<th>Impr.</th>\n";
					// 20141106
					echo "<th>Effectuée</th>\n";
					echo "<th>Suppr</th>\n";
					echo "</tr>\n";
					$alt_b=1;
					while($lig_sanction=mysqli_fetch_object($res_sanction)) {
						$alt_b=$alt_b*(-1);
						echo "<tr class='lig$alt_b'>\n";
						//echo "<td>Exclusion</td>\n";
						//echo "<td><a href='".$_SERVER['PHP_SELF']."?mode=modif&amp;valeur=exclusion&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$lig->login'>".ucfirst($lig_sanction->nature_sts)."</a></td>\n";
						echo "<td><a href='".$_SERVER['PHP_SELF']."?mode=modif&amp;valeur=$lig_sanction->id_nature_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$lig->login'>".ucfirst($lig_sanction->nature_sts)."</a></td>\n";
						echo "<td>".formate_date($lig_sanction->date_debut)."</td>\n";
						echo "<td>$lig_sanction->heure_debut</td>\n";
						echo "<td>".formate_date($lig_sanction->date_fin)."</td>\n";
						echo "<td>$lig_sanction->heure_fin</td>\n";
						echo "<td>$lig_sanction->lieu</td>\n";
						//echo "<td>".nl2br($lig_sanction->travail)."</td>\n";
						echo "<td>";

						$texte=nl2br($lig_sanction->travail);
						$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
						if($tmp_doc_joints!="") {
							if($texte!="") {$texte.="<br />";}
							$texte.="<strong>Documents joints</strong>&nbsp;:<br />";
							$texte.=$tmp_doc_joints;
						}
						$tabdiv_infobulle[]=creer_div_infobulle("div_travail_sanction_$lig_sanction->id_sanction","Travail (sanction n°$lig_sanction->id_sanction)","",$texte,"",20,0,'y','y','n','n');

						echo " <a href='#' onmouseover=\"delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onclick=\"return false;\">Détails</a>";
						echo "</td>\n";
//Eric						
						echo "<td>";
						if ($gepiSettings['active_mod_ooo'] == 'y') { //impression avec mod_ooo
							echo "<a href='".$_SERVER['PHP_SELF']."?odt=exclusion&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$lig->login".add_token_in_url()."' title='Imprimer la sanction n°$lig_sanction->id_sanction'><img src='../images/icons/print.png' width='16' height='16' alt=\"Imprimer le document\" /></a>\n";
						}
						else {
							echo "-";
						}
						echo "</td>\n";


						// 20141106
						// Sanction effectuée
						echo "<td";
						if((in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe')))||
						(($_SESSION['statut']=='professeur')&&(sanction_saisie_par($lig_sanction->id_sanction, $_SESSION['login'])))) {
							echo " title=\"Cliquez pour marquer la sanction comme effectuée ou non effectuée\">";
							if($lig_sanction->effectuee=="O") {
								$valeur_alt="N";
							}
							else {
								$valeur_alt="O";
							}

							echo "<a href='#' onclick=\"maj_etat_sanction_effectuee_ou_non($lig_sanction->id_sanction, '$valeur_alt')\" title=\"Marquer la sanction comme effectuée ou non effectuée\">";
							echo "<span id='span_sanction_effectuee_".$lig_sanction->id_sanction."'>";
							if($lig_sanction->effectuee=="O") {
								echo "<span style='color:green'>O</span>";
							}
							else {
								echo "<span style='color:red'>N</span>";
							}
							echo "</span>";
							echo "</a>";
						}
						else {
							echo ">";
							if($lig_sanction->effectuee=="O") {echo "<span style='color:green'>O</span>";} else {echo "<span style='color:red'>N</span>";}
						}
						echo "</td>\n";

						// Suppression
						echo "<td>";
						if(($_SESSION['statut']!='professeur')||($lig_sanction->saisie_par==$_SESSION['login'])) {
							echo "<a href='".$_SERVER['PHP_SELF']."?mode=suppr_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident".add_token_in_url()."' title='Supprimer la sanction n°$lig_sanction->id_sanction'><img src='../images/icons/delete.png' width='16' height='16' alt='Supprimer la sanction n°$lig_sanction->id_sanction' /></a>\n";
						}
						else {
							echo "<img src='../images/disabled.png' class='icone16' title=\"Un professeur ne peut supprimer que ses propres sanctions.\" />";
						}
						echo "</td>\n";

						echo "</tr>\n";
					}
					echo "</table>\n";
				}

				// Simple travail
				//$sql="SELECT * FROM s_sanctions s, s_travail st WHERE s.id_incident=$id_incident AND s.login='".$lig->login."' AND st.id_sanction=s.id_sanction ORDER BY st.date_retour;";
				$sql="SELECT s.*, st.*, sts.nature AS nature_sts  FROM s_sanctions s, s_travail st, s_types_sanctions2 sts WHERE s.id_incident=$id_incident AND s.login='".$lig->login."' AND st.id_sanction=s.id_sanction AND sts.id_nature=s.id_nature_sanction AND sts.type='travail' ORDER BY sts.nature, st.date_retour;";
				//echo "$sql<br />\n";
				$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_sanction)>0) {
					echo "<table class='boireaus' border='1' summary='Travail' style='margin:2px;'>\n";
					echo "<tr>\n";
					echo "<th>Nature</th>\n";
					echo "<th>Date retour</th>\n";
					echo "<th>Travail</th>\n";
					echo "<th>Imprimer</th>\n";
					// 20141106
					echo "<th>Effectuée</th>\n";
					echo "<th>Suppr</th>\n";
					echo "</tr>\n";
					$alt_b=1;
					while($lig_sanction=mysqli_fetch_object($res_sanction)) {
						$alt_b=$alt_b*(-1);
						echo "<tr class='lig$alt_b'>\n";
						//echo "<td><a href='".$_SERVER['PHP_SELF']."?mode=modif&amp;valeur=travail&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$lig->login'>".ucfirst($lig_sanction->nature_sts)."</a></td>\n";
						echo "<td><a href='".$_SERVER['PHP_SELF']."?mode=modif&amp;valeur=$lig_sanction->id_nature_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$lig->login'>".ucfirst($lig_sanction->nature_sts)."</a></td>\n";
						echo "<td>".formate_date($lig_sanction->date_retour)."</td>\n";
						//echo "<td>".nl2br($lig_sanction->travail)."</td>\n";
						echo "<td>";

						$texte=nl2br($lig_sanction->travail);
						$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
						if($tmp_doc_joints!="") {
							if($texte!="") {$texte.="<br />";}
							$texte.="<strong>Documents joints</strong>&nbsp;:<br />";
							$texte.=$tmp_doc_joints;
						}
						$tabdiv_infobulle[]=creer_div_infobulle("div_travail_sanction_$lig_sanction->id_sanction","Travail (sanction n°$lig_sanction->id_sanction)","",$texte,"",20,0,'y','y','n','n');

						echo " <a href='#' onmouseover=\"delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onclick=\"return false;\">Détails</a>";
						echo "</td>\n";

						echo "<td>";
						if ($gepiSettings['active_mod_ooo'] == 'y') { //impression avec mod_ooo
							echo "<a href='".$_SERVER['PHP_SELF']."?odt=travail&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$lig->login".add_token_in_url()."' title='Imprimer la sanction n°$lig_sanction->id_sanction'><img src='../images/icons/print.png' width='16' height='16' alt=\"Imprimer le document\" /></a>\n";
						}
						else {
							echo "-";
						}
						echo "</td>\n";

						// 20141106
						// Sanction effectuée
						echo "<td";
						if((in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe')))||
						(($_SESSION['statut']=='professeur')&&(sanction_saisie_par($lig_sanction->id_sanction, $_SESSION['login'])))) {
							echo " title=\"Cliquez pour marquer la sanction comme effectuée ou non effectuée\">";
							if($lig_sanction->effectuee=="O") {
								$valeur_alt="N";
							}
							else {
								$valeur_alt="O";
							}

							echo "<a href='#' onclick=\"maj_etat_sanction_effectuee_ou_non($lig_sanction->id_sanction, '$valeur_alt')\" title=\"Marquer la sanction comme effectuée ou non effectuée\">";
							echo "<span id='span_sanction_effectuee_".$lig_sanction->id_sanction."'>";
							if($lig_sanction->effectuee=="O") {
								echo "<span style='color:green'>O</span>";
							}
							else {
								echo "<span style='color:red'>N</span>";
							}
							echo "</span>";
							echo "</a>";
						}
						else {
							echo ">";
							if($lig_sanction->effectuee=="O") {echo "<span style='color:green'>O</span>";} else {echo "<span style='color:red'>N</span>";}
						}
						echo "</td>\n";

						// Suppression
						echo "<td>";
						if(($_SESSION['statut']!='professeur')||($lig_sanction->saisie_par==$_SESSION['login'])) {
							echo "<a href='".$_SERVER['PHP_SELF']."?mode=suppr_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident".add_token_in_url()."' title='Supprimer la sanction n°$lig_sanction->id_sanction'><img src='../images/icons/delete.png' width='16' height='16' alt='Supprimer la ".$mod_disc_terme_sanction." n°$lig_sanction->id_sanction' /></a>\n";
						}
						else {
							echo "<img src='../images/disabled.png' class='icone16' title=\"Un professeur ne peut supprimer que ses propres sanctions.\" />";
						}
						echo "</td>\n";

						echo "</tr>\n";
					}
					echo "</table>\n";
				}



				// Autres sanctions
				//$sql="SELECT * FROM s_sanctions s, s_autres_sanctions sa, s_types_sanctions sts WHERE s.id_incident='$id_incident' AND s.login='".$lig->login."' AND sa.id_sanction=s.id_sanction AND sa.id_nature=sts.id_nature ORDER BY sts.nature;";
				$sql="SELECT s.*, sa.*, sts.nature AS nature_sts  FROM s_sanctions s, s_autres_sanctions sa, s_types_sanctions2 sts WHERE s.id_incident='$id_incident' AND s.login='".$lig->login."' AND sa.id_sanction=s.id_sanction AND sa.id_nature=sts.id_nature AND sts.id_nature=s.id_nature_sanction AND sts.type='autre' ORDER BY sts.nature;";
				//echo "$sql<br />\n";
				$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_sanction)>0) {
					echo "<table class='boireaus' border='1' summary='Autres ".$mod_disc_terme_sanction."s' style='margin:2px;'>\n";
					echo "<tr>\n";
					echo "<th>Nature</th>\n";
					echo "<th>Description</th>\n";
					echo "<th>Imprimer</th>\n";
					// 20141106
					echo "<th>Effectuée</th>\n";
					echo "<th>Suppr</th>\n";
					echo "</tr>\n";
					$alt_b=1;
					while($lig_sanction=mysqli_fetch_object($res_sanction)) {
						$alt_b=$alt_b*(-1);
						echo "<tr class='lig$alt_b'>\n";
						//echo "<td><a href='".$_SERVER['PHP_SELF']."?mode=modif&amp;valeur=".$lig_sanction->id_nature."&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$lig->login'>".ucfirst($lig_sanction->nature_sts)."</a></td>\n";
						echo "<td><a href='".$_SERVER['PHP_SELF']."?mode=modif&amp;valeur=".$lig_sanction->id_nature_sanction."&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$lig->login'>".ucfirst($lig_sanction->nature_sts)."</a></td>\n";

						echo "<td>\n";
						$texte=nl2br($lig_sanction->description);
						$tabdiv_infobulle[]=creer_div_infobulle("div_autre_sanction_$lig_sanction->id_sanction","$lig_sanction->nature (sanction n°$lig_sanction->id_sanction)","",$texte,"",20,0,'y','y','n','n');

						echo " <a href='#' onmouseover=\"delais_afficher_div('div_autre_sanction_$lig_sanction->id_sanction','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onclick=\"return false;\">Détails</a>";
						echo "</td>\n";
						
						echo "<td>";
						if ($gepiSettings['active_mod_ooo'] == 'y') { //impression avec mod_ooo
							echo "<a href='".$_SERVER['PHP_SELF']."?odt=autre&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$lig->login".add_token_in_url()."' title='Imprimer la ".$mod_disc_terme_sanction." n°$lig_sanction->id_sanction'><img src='../images/icons/print.png' width='16' height='16' alt=\"Imprimer le document\" /></a>\n";
						}
						else {
							echo "-";
						}
						echo "</td>\n";

						// 20141106
						// Sanction effectuée
						echo "<td";
						if((in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe')))||
						(($_SESSION['statut']=='professeur')&&(sanction_saisie_par($lig_sanction->id_sanction, $_SESSION['login'])))) {
							echo " title=\"Cliquez pour marquer la sanction comme effectuée ou non effectuée\">";
							if($lig_sanction->effectuee=="O") {
								$valeur_alt="N";
							}
							else {
								$valeur_alt="O";
							}

							echo "<a href='#' onclick=\"maj_etat_sanction_effectuee_ou_non($lig_sanction->id_sanction, '$valeur_alt')\" title=\"Marquer la sanction comme effectuée ou non effectuée\">";
							echo "<span id='span_sanction_effectuee_".$lig_sanction->id_sanction."'>";
							if($lig_sanction->effectuee=="O") {
								echo "<span style='color:green'>O</span>";
							}
							else {
								echo "<span style='color:red'>N</span>";
							}
							echo "</span>";
							echo "</a>";
						}
						else {
							echo ">";
							if($lig_sanction->effectuee=="O") {echo "<span style='color:green'>O</span>";} else {echo "<span style='color:red'>N</span>";}
						}
						echo "</td>\n";

						// Suppression
						echo "<td>";
						if(($_SESSION['statut']!='professeur')||($lig_sanction->saisie_par==$_SESSION['login'])) {
							echo "<a href='".$_SERVER['PHP_SELF']."?mode=suppr_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident".add_token_in_url()."' title='Supprimer la sanction n°$lig_sanction->id_sanction'><img src='../images/icons/delete.png' width='16' height='16' alt='Supprimer la ".$mod_disc_terme_sanction." n°$lig_sanction->id_sanction' /></a>\n";
						}
						else {
							echo "<img src='../images/disabled.png' class='icone16' title=\"Un professeur ne peut supprimer que ses propres sanctions.\" />";
						}
						echo "</td>\n";

						echo "</tr>\n";
					}
					echo "</table>\n";
				}


				echo "<a href='".$_SERVER['PHP_SELF']."?id_incident=$id_incident&amp;ele_login=$lig->login&amp;mode=ajout' title='Ajouter une ".$mod_disc_terme_sanction."'><img src='../images/icons/add.png' width='16' height='16' alt='Ajouter une ".$mod_disc_terme_sanction."' /></a>";
			}
			else {
				// Pas de sanction pour un personnel, non mais sans blagues;o)
				echo "&nbsp;";
			}
			echo "</td>\n";

			echo "</tr>\n";
			$cpt++;
		}
		echo "</table>\n";
		echo "</blockquote>\n";

		// 20141106
		echo "<script type='text/javascript'>
	function maj_etat_sanction_effectuee_ou_non(id_sanction) {
		new Ajax.Updater($('span_sanction_effectuee_'+id_sanction),'ajax_discipline.php?id_sanction='+id_sanction+'&modif_sanction=etat_effectuee".add_token_in_url(false)."',{method: 'get'});
	}
</script>";

		rappel_incident($id_incident);
	}
	else {
		echo "<p>Aucun protagoniste n'a (<i>encore</i>) été spécifié pour cet ".$mod_disc_terme_incident.".</p>\n";
	}

	require("../lib/footer.inc.php");
	die();
}
elseif($mode=='ajout') {
	echo " | <a href='saisie_sanction.php?id_incident=$id_incident&amp;step=2'";
	echo " onclick='return confirm_abandon (this, change, \"$themessage\")'";
	echo ">Retour ".$mod_disc_terme_sanction."</a>\n";
	//echo " | <a href='traiter_incident.php'>Liste des incidents</a>\n";
	echo "</p>\n";

	echo "<script type='text/javascript'>
	//function edt_eleve(id_sanction) {
	function edt_eleve() {
		// Avec l'appel Ajax lors d'un Ajout de sanction, on ne parvient pas à récupérer la valeur de ele_login
		//ele_login=document.getElementById('ele_login').value;
		ele_login='$ele_login';
		centrerpopup('edt_eleve.php?ele_login='+ele_login,800,600,'scrollbars=yes,statusbar=no,resizable=yes');
	}
</script>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	//echo "<input type='hidden' name='step' value='$step' />\n";

	echo add_token_field(true);

	$current_identite_ele=p_nom($ele_login);
	echo "<p class='bold'>Ajout d'une ".$mod_disc_terme_sanction." pour ".$current_identite_ele;
	echo infobulle_photo($ele_login);

	if((getSettingAOui('autorise_edt_tous'))||
		((getSettingAOui('autorise_edt_admin'))&&($_SESSION['statut']=='administrateur'))
	) {

		$titre_infobulle="EDT de ".$current_identite_ele;
		$texte_infobulle="";
		$tabdiv_infobulle[]=creer_div_infobulle('edt_eleve',$titre_infobulle,"",$texte_infobulle,"",40,0,'y','y','n','n');

		echo " <a href='../edt_organisation/index_edt.php?login_edt=".$ele_login."&amp;type_edt_2=eleve&amp;no_entete=y&amp;no_menu=y&amp;lien_refermer=y' onclick=\"affiche_edt_en_infobulle();return false;\" title=\"Emploi du temps de ".$current_identite_ele."\" target='_blank'><img src='../images/icons/edt.png' class='icone16' alt='EDT' /></a>

<style type='text/css'>
	.lecorps {
		margin-left:0px;
	}
</style>

<script type='text/javascript'>
	function affiche_edt_en_infobulle() {
		new Ajax.Updater($('edt_eleve_contenu_corps'),'../edt_organisation/index_edt.php?login_edt=".$ele_login."&type_edt_2=eleve&no_entete=y&no_menu=y&mode_infobulle=y',{method: 'get'});
		afficher_div('edt_eleve','y',-20,20);
	}
</script>\n";
	}

	// 20161203
	if($acces_visu_eleve) {
		echo " <a href='../eleves/visu_eleve.php?ele_login=$ele_login' target='_blank' title=\"Voir (dans un nouvel onglet) la fiche élève avec les onglets Élève, Enseignements, Bulletins, CDT, Absences,...\"><img src='../images/icons/ele_onglets.png' class='icone16' alt='Élève' /></a>";
	}

	echo " (<em>".$mod_disc_terme_incident." n°$id_incident</em>)&nbsp;:</p>\n";

	echo "<blockquote>\n";

	$largeur_champ_select=11;
	$tab_autres_sanctions=array();
	//$sql="SELECT * FROM s_types_sanctions ORDER BY nature;";
	if($_SESSION['statut']=='professeur') {
		$sql="SELECT * FROM s_types_sanctions2 WHERE saisie_prof='y' ORDER BY type, nature;";
	}
	else {
		$sql="SELECT * FROM s_types_sanctions2 ORDER BY type, nature;";
	}
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_autres_sanctions[$lig->id_nature]=$lig->nature;
		}
		$largeur_champ_select=20;
		// On peut quand même se retrouver avec une superposition.
	}

	echo "<div style='float:left; width:".$largeur_champ_select."em;'>\n";

	echo "<p class='bold'>Nature de la ".$mod_disc_terme_sanction."&nbsp;:<br />\n";
	echo "<select name='traitement' id='traitement' onchange=\"maj_traitement(); setTimeout('maj_div_liste_retenues_jour()', 1000)\">\n";
	if(count($tab_autres_sanctions)>1) {
		echo "<option value=''";
		echo ">---</option>\n";
	}

	/*
	echo "<option value='travail'";
	echo ">Travail</option>\n";

	echo "<option value='retenue'";
	echo ">Retenue</option>\n";

	echo "<option value='exclusion'";
	echo ">Exclusion</option>\n";
	*/

	foreach($tab_autres_sanctions AS $key => $value) {
		echo "<option value='$key'";
		echo ">$value</option>\n";
	}

	/*
	$sql="SELECT * FROM s_types_sanctions ORDER BY nature;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			echo "<option value='$lig->id_nature'";
			echo ">$lig->nature</option>\n";
		}
	}
	*/

	echo "</select>\n";
	echo "<input type='button' value='Go' onclick='maj_traitement()' />";
	echo "</p>\n";
	echo "</div>\n";

	echo "<div style='clear:both;'></div>\n";

	// 20161203
	// Rechercher les sanctions à venir concernant cet élève pour les afficher sur la droite et éviter de saisir deux sanctions sur le même créneau
	$sql="SELECT * FROM s_sanctions ss, 
				s_retenues sr 
			WHERE ss.id_sanction=sr.id_sanction AND ss.login='".$ele_login."';";
	//echo "$sql<br />";
	//echo "<div style='float:right;width:20em;' class='fieldset_opacite50'>"."</div>";
	//Difficulté: Lister les dates/heures s_sanctions<->s_types_sanctions/s_types_sanctions2<->s_retenues|s_exclusions|s_travail,...
	// Quand on change de champ après le champ date, on a la liste des retenues du jour,...


	// Pour afficher les autres champs de formulaire des détails de la sanction:
	echo "<div id='div_details_sanction'>\n";
	echo "</div>\n";

	//echo "<input type='submit' name='enregistrer_sanction' value='Enregistrer' />\n";
	echo "</blockquote>\n";

	echo "<input type='hidden' name='id_incident' value='$id_incident' />\n";
	echo "<input type='hidden' name='ele_login' value='$ele_login' />\n";
	echo "</form>\n";


	echo "<script type='text/javascript'>
	// Avec cette fonction, on ne fait qu'ajouter des champs dans le formulaire (aucun enregistrement avant validation du formulaire)
	function maj_traitement() {
		valeur=$('traitement').value;
		//new Ajax.Updater($('div_details_sanction'),'ajout_sanction.php?cpt=0&valeur='+valeur,{method: 'get'});
		new Ajax.Updater($('div_details_sanction'),'ajout_sanction.php?cpt=0&valeur='+valeur+'&ele_login=$ele_login&id_incident=$id_incident',{method: 'get'});
	}
</script>\n";


echo "<script type='text/javascript'>
	function maj_lieu(id_lieu,champ_select) {
		if(document.getElementById(id_lieu)) {
			//document.getElementById(id_lieu).value=document.getElementById(champ_select).selectedIndex;
			document.getElementById(id_lieu).value=document.getElementById(champ_select).options[document.getElementById(champ_select).selectedIndex].value;
		}
	}
</script>\n";

	//echo "<div style='clear:both;'></div>\n";

	echo envoi_mail_rappel_js();

	rappel_incident($id_incident);

}
elseif($mode=='modif') {
	echo " | <a href='saisie_sanction.php?id_incident=$id_incident&amp;step=2'";
	echo " onclick='return confirm_abandon (this, change, \"$themessage\")'";
	echo ">Retour ".$mod_disc_terme_sanction."</a>\n";
	//echo " | <a href='traiter_incident.php'>Liste des incidents</a>\n";
	echo "</p>\n";

	if(!isset($temoin_modif_fichier)) {
		$valeur=isset($_GET['valeur']) ? $_GET['valeur'] : NULL;
	}
	//$valeur=isset($_POST['traitement']) ? $_POST['traitement'] : (isset($_GET['valeur']) ? $_GET['valeur'] : NULL);
	$traitement=$valeur;

	echo "<script type='text/javascript'>
	//function edt_eleve(id_sanction) {
	function edt_eleve() {
		// Avec l'appel Ajax lors d'un Ajout de sanction, on ne parvient pas à récupérer la valeur de ele_login
		//ele_login=document.getElementById('ele_login').value;
		ele_login='$ele_login';
		centrerpopup('edt_eleve.php?ele_login='+ele_login,800,600,'scrollbars=yes,statusbar=no,resizable=yes');
	}
</script>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

	echo add_token_field(true);

	echo "<p class='bold'>".ucfirst($mod_disc_terme_sanction)." (<em>$traitement</em>) n°$id_sanction concernant ".p_nom($ele_login);
	echo infobulle_photo($ele_login);
	echo "&nbsp;: ";
	echo get_nature_sanction($traitement);
	echo "</p>\n";
	echo "<blockquote>\n";

	echo "<input type='hidden' name='ele_login' id='ele_login' value='$ele_login' />\n";
	include('saisie_sanction.inc.php');
	echo "</blockquote>\n";

	echo "<input type='hidden' name='traitement' value='$traitement' />\n";
	echo "<input type='hidden' name='id_sanction' value='$id_sanction' />\n";
	echo "<input type='hidden' name='id_incident' value='$id_incident' />\n";
	echo "</form>\n";

	echo envoi_mail_rappel_js();

	rappel_incident($id_incident);

	echo "<script type='text/javascript'>
	function maj_lieu(id_lieu,champ_select) {
		if(document.getElementById(id_lieu)) {
			//document.getElementById(id_lieu).value=document.getElementById(champ_select).selectedIndex;
			document.getElementById(id_lieu).value=document.getElementById(champ_select).options[document.getElementById(champ_select).selectedIndex].value;
		}
	}
</script>\n";

}
else {
	echo "<p>Euh... c'est pas prévu qu'on arrive là (pour le moment;o).</p>\n";
}

echo insere_fonctions_js_recherche_ajax_ele();
echo insere_infobulle_recherche_ajax_ele();

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>

