<?php
/*
 * $Id$
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

//$traite_anti_inject="yes";

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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_abs_prof/proposer_remplacement.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/mod_abs_prof/proposer_remplacement.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Proposer des remplacements aux professeurs',
statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('active_mod_abs_prof')) {
	header("Location: ../accueil.php?msg=Module désactivé");
	die();
}

if(($_SESSION['statut']=='scolarite')&&(!getSettingAOui('AbsProfProposerRemplacementScol'))) {
	header("Location: ../accueil.php?msg=Vous n êtes pas autorisé à proposer des remplacements aux professeurs");
	die();
}

if(($_SESSION['statut']=='cpe')&&(!getSettingAOui('AbsProfProposerRemplacementCpe'))) {
	header("Location: ../accueil.php?msg=Vous n êtes pas autorisé à proposer des remplacements aux professeurs");
	die();
}

include("../ckeditor/ckeditor.php") ;

$id_absence=isset($_POST['id_absence']) ? $_POST['id_absence'] : (isset($_GET['id_absence']) ? $_GET['id_absence'] : NULL);
//echo "\$id_absence=$id_absence<br />";

if(!isset($id_absence)) {
	header("Location: index.php?msg=Numéro d absence non choisi.");
	die();
}

if(!preg_match("/^[0-9]{1,}$/", $id_absence)) {
	header("Location: index.php?msg=Numéro d absence non valide.");
	die();
}

$sql="SELECT * FROM abs_prof WHERE id='$id_absence';";
//echo "$sql<br />";
$res_infos_id_absence_courante=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_infos_id_absence_courante)==0) {
	header("Location: index.php?msg=Numéro d absence non valide.");
	die();
}

/*
function get_datetime_debut_fin_jour_creneau($jour, $id_creneau) {
	$tab=array();

	$sql="SELECT * FROM edt_creneaux WHERE id_definie_periode='$id_creneau';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$tab[0]=$jour." ".$lig->heuredebut_definie_periode;
		$tab[1]=$jour." ".$lig->heurefin_definie_periode;
	}

	return $tab;
}

function get_heures_debut_fin_creneau($id_creneau) {
	$tab=array();

	$sql="SELECT * FROM edt_creneaux WHERE id_definie_periode='$id_creneau';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$tab[0]=$lig->heuredebut_definie_periode;
		$tab[1]=$lig->heurefin_definie_periode;
	}

	return $tab;
}
*/

$envoi_mail_actif=getSettingValue('envoi_mail_actif');
if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
	$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
}

if(isset($_POST['is_posted'])) {
	check_token();

	$proposition=isset($_POST['proposition']) ? $_POST['proposition'] : array();
	$envoi_mail=isset($_POST['envoi_mail']) ? $_POST['envoi_mail'] : "n";

	$msg="";

	$tab_heures=get_heures_debut_fin_creneaux();
	$nb_reg=0;
	//$tab_propositions_postees=array();
	for($loop=0;$loop<count($proposition);$loop++) {
		// Contrôler si le remplacement est déjà proposé?
		// Supprimer les propositions existantes?
		// N'envoyer de mail que pour les nouvelles propositions

		$tab=explode("|", $proposition[$loop]);

		//echo $proposition[$loop]."<br />";
		if(preg_match("/^AID_/", $tab[0])) {
			// 20151006

			$id_aid=preg_replace("/^AID_/", "", $tab[0]);
			$id_classe=$tab[1];
			$jour=$tab[2];
			$id_creneau=$tab[3];
			$login_user=$tab[4];

			$jour_mysql=substr($jour,0,4)."-".substr($jour,4,2)."-".substr($jour,6,2);

			$date_debut_r=$jour_mysql." ".$tab_heures[$id_creneau]['debut'];
			$date_fin_r=$jour_mysql." ".$tab_heures[$id_creneau]['fin'];

			$temoin_remplacement_confirme="n";
			$tab_propositions_deja_enregistrees=array();
			$sql="SELECT * FROM abs_prof_remplacement WHERE id_absence='$id_absence' AND 
											id_aid='".$id_aid."' AND 
											id_classe='".$id_classe."' AND 
											date_debut_r='".$date_debut_r."' AND 
											date_fin_r='".$date_fin_r."';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				while($lig=mysqli_fetch_object($res)) {
					$tab_propositions_deja_enregistrees[]=$lig->login_user;
					if($lig->validation_remplacement=="y") {
						$temoin_remplacement_confirme="y";
					}
				}
			}

			// On ne refait pas de proposition si le remplacement est déjà attribué.
			if($temoin_remplacement_confirme=="n") {
				// id_creneau et heure début fin...
				// Tester si le prof fait partie de ceux à qui on a déjà proposé.
				// Tester si la proposition est acceptée/validée pour quelqu'un... si oui, vider les autres
				if(!in_array($login_user, $tab_propositions_deja_enregistrees)) {
					$sql="INSERT INTO abs_prof_remplacement SET id_absence='$id_absence',
												id_aid='".$id_aid."',
												id_classe='".$id_classe."',
												jour='".$jour."',
												id_creneau='".$id_creneau."',
												date_debut_r='".$date_debut_r."',
												date_fin_r='".$date_fin_r."',
												login_user='".$login_user."';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if($insert) {
						$nb_reg++;
						$id_proposition=mysqli_insert_id($GLOBALS["mysqli"]);

						if(($envoi_mail_actif=='y')&&($envoi_mail=="y")) {

							$mail_dest=get_mail_user($login_user);

							if(check_mail($mail_dest)) {
								$tab_info_creneau=get_infos_creneau($id_creneau);
								$info_creneau=$tab_info_creneau['nom_creneau']." (".$tab_info_creneau['debut_court']."-".$tab_info_creneau['fin_court'].")";

								$subject = "[GEPI]: Proposition de remplacement n°$id_proposition";
								$texte_mail="Bonjour ".civ_nom_prenom($login_user).",

En raison de l'absence d'un professeur, une ou des classes sont libérées.
Je vous propose le remplacement suivant (pour soulager la permanence,...):

".get_nom_classe($id_classe)." le ".formate_date($date_debut_r,"n","complet")." en ".$info_creneau."
en remplacement de ".get_info_aid($id_aid,array('nom_general_complet', 'classes', 'profs'), "").".

Vous pouvez accepter ou rejeter cette proposition dans Gepi.
Un message doit être affiché en page d'accueil pour vous permettre de répondre.

D'avance merci.


Cordialement.
-- 
".civ_nom_prenom($_SESSION['login']);

								$tab_param_mail['destinataire']=$mail_dest;

								$headers = "";
								if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
									$headers.="Reply-to:".$_SESSION['email']."\r\n";
									$tab_param_mail['replyto']=$_SESSION['email'];
								}

								$message_id='proposition_remplacement_'.$id_proposition."_".$jour;
								if(isset($message_id)) {$headers .= "Message-id: $message_id\r\n";}
								//if(isset($references_mail)) {$headers .= "References: $references_mail\r\n";}

								// On envoie le mail
								$envoi = envoi_mail($subject, $texte_mail, $mail_dest, $headers, "plain", $tab_param_mail);
							}
						}
					}
					else {
						$msg.="Erreur $sql<br />";
					}
				}
			}
		}
		else {
			$id_groupe=$tab[0];
			$id_classe=$tab[1];
			$jour=$tab[2];
			$id_creneau=$tab[3];
			$login_user=$tab[4];

			$jour_mysql=substr($jour,0,4)."-".substr($jour,4,2)."-".substr($jour,6,2);

			$date_debut_r=$jour_mysql." ".$tab_heures[$id_creneau]['debut'];
			$date_fin_r=$jour_mysql." ".$tab_heures[$id_creneau]['fin'];

			$temoin_remplacement_confirme="n";
			$tab_propositions_deja_enregistrees=array();
			$sql="SELECT * FROM abs_prof_remplacement WHERE id_absence='$id_absence' AND 
											id_groupe='".$id_groupe."' AND 
											id_classe='".$id_classe."' AND 
											date_debut_r='".$date_debut_r."' AND 
											date_fin_r='".$date_fin_r."';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				while($lig=mysqli_fetch_object($res)) {
					$tab_propositions_deja_enregistrees[]=$lig->login_user;
					if($lig->validation_remplacement=="y") {
						$temoin_remplacement_confirme="y";
					}
				}
			}

			// On ne refait pas de proposition si le remplacement est déjà attribué.
			if($temoin_remplacement_confirme=="n") {
				// id_creneau et heure début fin...
				// Tester si le prof fait partie de ceux à qui on a déjà proposé.
				// Tester si la proposition est acceptée/validée pour quelqu'un... si oui, vider les autres
				if(!in_array($login_user, $tab_propositions_deja_enregistrees)) {
					$sql="INSERT INTO abs_prof_remplacement SET id_absence='$id_absence',
												id_groupe='".$id_groupe."',
												id_classe='".$id_classe."',
												jour='".$jour."',
												id_creneau='".$id_creneau."',
												date_debut_r='".$date_debut_r."',
												date_fin_r='".$date_fin_r."',
												login_user='".$login_user."';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if($insert) {
						$nb_reg++;
						$id_proposition=mysqli_insert_id($GLOBALS["mysqli"]);

						if(($envoi_mail_actif=='y')&&($envoi_mail=="y")) {

							$mail_dest=get_mail_user($login_user);

							if(check_mail($mail_dest)) {
								$tab_info_creneau=get_infos_creneau($id_creneau);
								$info_creneau=$tab_info_creneau['nom_creneau']." (".$tab_info_creneau['debut_court']."-".$tab_info_creneau['fin_court'].")";

								$subject = "[GEPI]: Proposition de remplacement n°$id_proposition";
								$texte_mail="Bonjour ".civ_nom_prenom($login_user).",

En raison de l'absence d'un professeur, une ou des classes sont libérées.
Je vous propose le remplacement suivant (pour soulager la permanence,...):

".get_nom_classe($id_classe)." le ".formate_date($date_debut_r,"n","complet")." en ".$info_creneau."
en remplacement de ".get_info_grp($id_groupe,array('description', 'matieres', 'classes', 'profs'), "").".

Vous pouvez accepter ou rejeter cette proposition dans Gepi.
Un message doit être affiché en page d'accueil pour vous permettre de répondre.

D'avance merci.


Cordialement.
-- 
".civ_nom_prenom($_SESSION['login']);

								$tab_param_mail['destinataire']=$mail_dest;

								$headers = "";
								if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
									$headers.="Reply-to:".$_SESSION['email']."\r\n";
									$tab_param_mail['replyto']=$_SESSION['email'];
								}

								$message_id='proposition_remplacement_'.$id_proposition."_".$jour;
								if(isset($message_id)) {$headers .= "Message-id: $message_id\r\n";}
								//if(isset($references_mail)) {$headers .= "References: $references_mail\r\n";}

								// On envoie le mail
								$envoi = envoi_mail($subject, $texte_mail, $mail_dest, $headers, "plain", $tab_param_mail);
							}
						}
					}
					else {
						$msg.="Erreur $sql<br />";
					}
				}
			}
		}
	}


	$nb_suppr=0;
	$sql="SELECT * FROM abs_prof_remplacement WHERE id_absence='$id_absence';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if(($lig->id_groupe!="")&&($lig->id_groupe!="0")) {
				$chaine=$lig->id_groupe."|".$lig->id_classe."|".$lig->jour."|".$lig->id_creneau."|".$lig->login_user;
			}
			else {
				$chaine="AID_".$lig->id_aid."|".$lig->id_classe."|".$lig->jour."|".$lig->id_creneau."|".$lig->login_user;
			}
			if(!in_array($chaine, $proposition)) {
				$sql="DELETE FROM abs_prof_remplacement WHERE id='$lig->id';";
				//echo "$sql<br />";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
				if($del) {
					//echo "$chaine suppr<br />";
					$nb_suppr++;
				}
				else {
					$msg.="Erreur $sql<br />";
				}
			}
		}
	}


	if($nb_reg>0) {
		$msg.="$nb_reg proposition(s) enregistrée(s)/ajoutée(s).<br />";
	}

	if($nb_suppr>0) {
		$msg.="$nb_suppr proposition(s) supprimée(s).<br />";
	}
}


if((isset($_GET['valider_proposition']))||(isset($_POST['valider_proposition']))) {
	check_token();

	$valider_proposition=isset($_POST['valider_proposition']) ? $_POST['valider_proposition'] : (isset($_GET['valider_proposition']) ? $_GET['valider_proposition'] : "");
	$tab=explode("|", $valider_proposition);

	$commentaire_validation=isset($_POST['commentaire_validation']) ? $_POST['commentaire_validation'] : "";
	$salle=isset($_POST['salle']) ? $_POST['salle'] : "";

	if(isset($tab[4])) {

		if(preg_match("/^AID_/", $tab[0])) {
			// C'est un AID

			$id_aid=preg_replace("/^AID_/", "", $tab[0]);
			$id_classe=$tab[1];
			$jour=$tab[2];
			$id_creneau=$tab[3];
			$login_user=$tab[4];

			$deja_validee=check_proposition_remplacement_validee($id_absence, $id_groupe, $id_aid, $id_classe, $jour, $id_creneau);
			if($deja_validee!="") {
				if($deja_validee==civ_nom_prenom($login_user)) {
					// On ne devrait pas arriver là
					$msg="Le remplacement était déjà attribué à ce professeur.<br />";
				}
				else {
					// On désinscrit le professeur précédemment choisi
					$sql="UPDATE abs_prof_remplacement SET validation_remplacement='' WHERE id_absence='".$id_absence."' AND 
																	id_aid='".$id_aid."' AND 
																	id_classe='".$id_classe."' AND 
																	jour='".$jour."' AND 
																	id_creneau='".$id_creneau."';";
					//echo "$sql<br />";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if($update) {
						$chaine_commentaire_validation="";
						$chaine_salle="";

						$sql="UPDATE abs_prof_remplacement SET validation_remplacement='oui'";
						if($commentaire_validation!="") {
							$sql.=", commentaire_validation='$commentaire_validation'";
							$chaine_commentaire_validation=preg_replace('/(\\\n)+/',"\n", $commentaire_validation)."\n";
						}
						if($salle!="") {
							$sql.=", salle='$salle'";
							$chaine_salle="Salle $salle\n";
						}
						$sql.=" WHERE id_absence='".$id_absence."' AND 
																		id_aid='".$id_aid."' AND 
																		id_classe='".$id_classe."' AND 
																		jour='".$jour."' AND 
																		id_creneau='".$id_creneau."' AND 
																		login_user='$login_user';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if($update) {
							$msg="Remplacement attribué à ".civ_nom_prenom($login_user).".<br />";
							$msg.="N'oubliez pas d'<a href='$gepiPath/mod_abs_prof/afficher_remplacements.php?mode=familles_non_informees'>informer les familles</a>.<br />";

							if($envoi_mail_actif=='y') {
								$mail_dest="";
								$references_mail="";
								$sql="SELECT u.email, apr.id FROM abs_prof_remplacement apr, utilisateurs u WHERE id_absence='".$id_absence."' AND 
																id_aid='".$id_aid."' AND 
																id_classe='".$id_classe."' AND 
																jour='".$jour."' AND 
																id_creneau='".$id_creneau."' AND 
																reponse!='non';";
								//echo "$sql<br />";
								$res_mail=mysqli_query($GLOBALS["mysqli"], $sql);
								while($lig=mysqli_fetch_object($res_mail)) {
									if(check_mail($lig->email)) {
										if($mail_dest!="") {
											$mail_dest.=",";
											$references_mail.="\r\n";
										}
										//$mail_dest.=$lig->email;
										if((!preg_match("/^$lig->email,/", $mail_dest))&&(!preg_match("/,$lig->email,/", $mail_dest))&&(!preg_match("/,$lig->email$/", $mail_dest))) {
											$mail_dest.=$lig->email;
											$tab_param_mail['destinataire'][]=$lig->email;
										}
										$references_mail.="proposition_remplacement_".$lig->id."_".$jour;
									}
								}

								if($mail_dest!="") {
									$tab_info_creneau=get_infos_creneau($id_creneau);
									$info_creneau=$tab_info_creneau['nom_creneau']." (".$tab_info_creneau['debut_court']."-".$tab_info_creneau['fin_court'].")";

									$date_debut_r=substr($jour, 0, 4)."-".substr($jour, 4, 2)."-".substr($jour, 6, 2)." 08:00:00";

									$designation_user=civ_nom_prenom($login_user);
									$subject = "[GEPI]: Remplacement attribué à ".$designation_user;
									$texte_mail="Bonjour ".$designation_user.",

Le remplacement suivant vous est attribué:

".get_nom_classe($id_classe)." le ".formate_date($date_debut_r,"n","complet")." en ".$info_creneau."
".$chaine_commentaire_validation.$chaine_salle."en remplacement de ".get_info_aid($id_aid,array('nom_general_complet', 'classes', 'profs'), "").".

Merci.


Cordialement.
-- 
".civ_nom_prenom($_SESSION['login']);

									$headers = "";
									if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
										$headers.="Reply-to:".$_SESSION['email']."\r\n";
										$tab_param_mail['replyto']=$_SESSION['email'];
									}

									$message_id='remplacement_c'.$id_creneau."_j".$jour;
									if(isset($message_id)) {$headers .= "Message-id: $message_id\r\n";}
									if(isset($references_mail)) {$headers .= "References: $references_mail\r\n";}

									// On envoie le mail
									$envoi = envoi_mail($subject, $texte_mail, $mail_dest, $headers, "plain", $tab_param_mail);
								}
							}

						}
						else {
							$msg="Erreur lors de l'attribution du remplacement à ".civ_nom_prenom($login_user).".<br />";
						}
					}
					else {
						$msg="Erreur lors de la désattribution du remplacement à un autre professeur.<br />";
					}
				}
			}
			else {
				$sql="UPDATE abs_prof_remplacement SET validation_remplacement='oui'";
				if($commentaire_validation!="") {
					$sql.=", commentaire_validation='$commentaire_validation'";
					//$chaine_commentaire_validation=$commentaire_validation."\n";
					$chaine_commentaire_validation=preg_replace('/(\\\n)+/',"\n", $commentaire_validation)."\n";
				}
				if($salle!="") {
					$sql.=", salle='$salle'";
					$chaine_salle="Salle $salle\n";
				}
				$sql.=" WHERE id_absence='".$id_absence."' AND 
									id_aid='".$id_aid."' AND 
									id_classe='".$id_classe."' AND 
									jour='".$jour."' AND 
									id_creneau='".$id_creneau."' AND 
									login_user='$login_user';";
				//echo "$sql<br />";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if($update) {
					$msg="Remplacement attribué à ".civ_nom_prenom($login_user).".<br />";
					$msg.="N'oubliez pas d'<a href='$gepiPath/mod_abs_prof/afficher_remplacements.php?mode=familles_non_informees'>informer les familles</a>.<br />";
					if($envoi_mail_actif=='y') {
						$mail_dest="";
						$references_mail="";
						$sql="SELECT u.email, apr.id FROM abs_prof_remplacement apr, utilisateurs u WHERE id_absence='".$id_absence."' AND 
														id_aid='".$id_aid."' AND 
														id_classe='".$id_classe."' AND 
														jour='".$jour."' AND 
														id_creneau='".$id_creneau."' AND 
														reponse!='non';";
						//echo "$sql<br />";
						$res_mail=mysqli_query($GLOBALS["mysqli"], $sql);
						while($lig=mysqli_fetch_object($res_mail)) {
							if(check_mail($lig->email)) {
								if($mail_dest!="") {
									$mail_dest.=",";
									$references_mail.="\r\n";
								}
								//$mail_dest.=$lig->email;
								if((!preg_match("/^$lig->email,/", $mail_dest))&&(!preg_match("/,$lig->email,/", $mail_dest))&&(!preg_match("/,$lig->email$/", $mail_dest))) {
									$mail_dest.=$lig->email;
									$tab_param_mail['destinataire'][]=$lig->email;
								}
								$references_mail.="proposition_remplacement_".$lig->id."_".$jour;
							}
						}

						if($mail_dest!="") {
							$tab_info_creneau=get_infos_creneau($id_creneau);
							$info_creneau=$tab_info_creneau['nom_creneau']." (".$tab_info_creneau['debut_court']."-".$tab_info_creneau['fin_court'].")";

							$date_debut_r=substr($jour, 0, 4)."-".substr($jour, 4, 2)."-".substr($jour, 6, 2)." 08:00:00";

							$designation_user=civ_nom_prenom($login_user);
							$subject = "[GEPI]: Remplacement attribué à ".$designation_user;
							$texte_mail="Bonjour ".$designation_user.",

Le remplacement suivant vous est attribué:

".get_nom_classe($id_classe)." le ".formate_date($date_debut_r,"n","complet")." en ".$info_creneau."
".$chaine_commentaire_validation.$chaine_salle."en remplacement de ".get_info_aid($id_aid, array('nom_general_complet', 'classes', 'profs'), "").".

Merci.


Cordialement.
-- 
".civ_nom_prenom($_SESSION['login']);

							$headers = "";
							if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
								$headers.="Reply-to:".$_SESSION['email']."\r\n";
								$tab_param_mail['replyto']=$_SESSION['email'];
							}

							$message_id='remplacement_c'.$id_creneau."_j".$jour;
							if(isset($message_id)) {$headers .= "Message-id: $message_id\r\n";}
							//if(isset($references_mail)) {$headers .= "References: $references_mail\r\n";}

							// On envoie le mail
							$envoi = envoi_mail($subject, $texte_mail, $mail_dest, $headers, "plain", $tab_param_mail);
						}
					}

				}
				else {
					$msg="Erreur lors de l'attribution du remplacement à ".civ_nom_prenom($login_user).".<br />";
				}
			}
		}
		else {
			// C'est un groupe
			$id_groupe=$tab[0];
			$id_classe=$tab[1];
			$jour=$tab[2];
			$id_creneau=$tab[3];
			$login_user=$tab[4];

			$deja_validee=check_proposition_remplacement_validee($id_absence, $id_groupe, $id_classe, $jour, $id_creneau);
			if($deja_validee!="") {
				if($deja_validee==civ_nom_prenom($login_user)) {
					// On ne devrait pas arriver là
					$msg="Le remplacement était déjà attribué à ce professeur.<br />";
				}
				else {
					// On désinscrit le professeur précédemment choisi
					$sql="UPDATE abs_prof_remplacement SET validation_remplacement='' WHERE id_absence='".$id_absence."' AND 
																	id_groupe='".$id_groupe."' AND 
																	id_classe='".$id_classe."' AND 
																	jour='".$jour."' AND 
																	id_creneau='".$id_creneau."';";
					//echo "$sql<br />";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if($update) {
						$chaine_commentaire_validation="";
						$chaine_salle="";

						$sql="UPDATE abs_prof_remplacement SET validation_remplacement='oui'";
						if($commentaire_validation!="") {
							$sql.=", commentaire_validation='$commentaire_validation'";
							$chaine_commentaire_validation=preg_replace('/(\\\n)+/',"\n", $commentaire_validation)."\n";
						}
						if($salle!="") {
							$sql.=", salle='$salle'";
							$chaine_salle="Salle $salle\n";
						}
						$sql.=" WHERE id_absence='".$id_absence."' AND 
																		id_groupe='".$id_groupe."' AND 
																		id_classe='".$id_classe."' AND 
																		jour='".$jour."' AND 
																		id_creneau='".$id_creneau."' AND 
																		login_user='$login_user';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if($update) {
							$msg="Remplacement attribué à ".civ_nom_prenom($login_user).".<br />";
							$msg.="N'oubliez pas d'<a href='$gepiPath/mod_abs_prof/afficher_remplacements.php?mode=familles_non_informees'>informer les familles</a>.<br />";

							if($envoi_mail_actif=='y') {
								$mail_dest="";
								$references_mail="";
								$sql="SELECT u.email, apr.id FROM abs_prof_remplacement apr, utilisateurs u WHERE id_absence='".$id_absence."' AND 
																id_groupe='".$id_groupe."' AND 
																id_classe='".$id_classe."' AND 
																jour='".$jour."' AND 
																id_creneau='".$id_creneau."' AND 
																reponse!='non';";
								//echo "$sql<br />";
								$res_mail=mysqli_query($GLOBALS["mysqli"], $sql);
								while($lig=mysqli_fetch_object($res_mail)) {
									if(check_mail($lig->email)) {
										if($mail_dest!="") {
											$mail_dest.=",";
											$references_mail.="\r\n";
										}
										//$mail_dest.=$lig->email;
										if((!preg_match("/^$lig->email,/", $mail_dest))&&(!preg_match("/,$lig->email,/", $mail_dest))&&(!preg_match("/,$lig->email$/", $mail_dest))) {
											$mail_dest.=$lig->email;
											$tab_param_mail['destinataire'][]=$lig->email;
										}
										$references_mail.="proposition_remplacement_".$lig->id."_".$jour;
									}
								}

								if($mail_dest!="") {
									$tab_info_creneau=get_infos_creneau($id_creneau);
									$info_creneau=$tab_info_creneau['nom_creneau']." (".$tab_info_creneau['debut_court']."-".$tab_info_creneau['fin_court'].")";

									$date_debut_r=substr($jour, 0, 4)."-".substr($jour, 4, 2)."-".substr($jour, 6, 2)." 08:00:00";

									$designation_user=civ_nom_prenom($login_user);
									$subject = "[GEPI]: Remplacement attribué à ".$designation_user;
									$texte_mail="Bonjour ".$designation_user.",

Le remplacement suivant vous est attribué:

".get_nom_classe($id_classe)." le ".formate_date($date_debut_r,"n","complet")." en ".$info_creneau."
".$chaine_commentaire_validation.$chaine_salle."en remplacement de ".get_info_grp($id_groupe,array('description', 'matieres', 'classes', 'profs'), "").".

Merci.


Cordialement.
-- 
".civ_nom_prenom($_SESSION['login']);

									$headers = "";
									if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
										$headers.="Reply-to:".$_SESSION['email']."\r\n";
										$tab_param_mail['replyto']=$_SESSION['email'];
									}

									$message_id='remplacement_c'.$id_creneau."_j".$jour;
									if(isset($message_id)) {$headers .= "Message-id: $message_id\r\n";}
									if(isset($references_mail)) {$headers .= "References: $references_mail\r\n";}

									// On envoie le mail
									$envoi = envoi_mail($subject, $texte_mail, $mail_dest, $headers, "plain", $tab_param_mail);
								}
							}

						}
						else {
							$msg="Erreur lors de l'attribution du remplacement à ".civ_nom_prenom($login_user).".<br />";
						}
					}
					else {
						$msg="Erreur lors de la désattribution du remplacement à un autre professeur.<br />";
					}
				}
			}
			else {
				/*
				$sql="UPDATE abs_prof_remplacement SET validation_remplacement='oui' WHERE id_absence='".$id_absence."' AND 
																id_groupe='".$id_groupe."' AND 
																id_classe='".$id_classe."' AND 
																jour='".$jour."' AND 
																id_creneau='".$id_creneau."' AND 
																login_user='$login_user';";
				//echo "$sql<br />";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if($update) {
					$msg="Remplacement attribué à ".civ_nom_prenom($login_user).".<br />";
				}
				else {
					$msg="Erreur lors de l'attribution du remplacement à ".civ_nom_prenom($login_user).".<br />";
				}
				*/
				$sql="UPDATE abs_prof_remplacement SET validation_remplacement='oui'";
				if($commentaire_validation!="") {
					$sql.=", commentaire_validation='$commentaire_validation'";
					//$chaine_commentaire_validation=$commentaire_validation."\n";
					$chaine_commentaire_validation=preg_replace('/(\\\n)+/',"\n", $commentaire_validation)."\n";
				}
				if($salle!="") {
					$sql.=", salle='$salle'";
					$chaine_salle="Salle $salle\n";
				}
				$sql.=" WHERE id_absence='".$id_absence."' AND 
									id_groupe='".$id_groupe."' AND 
									id_classe='".$id_classe."' AND 
									jour='".$jour."' AND 
									id_creneau='".$id_creneau."' AND 
									login_user='$login_user';";
				//echo "$sql<br />";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if($update) {
					$msg="Remplacement attribué à ".civ_nom_prenom($login_user).".<br />";
					$msg.="N'oubliez pas d'<a href='$gepiPath/mod_abs_prof/afficher_remplacements.php?mode=familles_non_informees'>informer les familles</a>.<br />";
					if($envoi_mail_actif=='y') {
						$mail_dest="";
						$references_mail="";
						$sql="SELECT u.email, apr.id FROM abs_prof_remplacement apr, utilisateurs u WHERE id_absence='".$id_absence."' AND 
														id_groupe='".$id_groupe."' AND 
														id_classe='".$id_classe."' AND 
														jour='".$jour."' AND 
														id_creneau='".$id_creneau."' AND 
														reponse!='non';";
						//echo "$sql<br />";
						$res_mail=mysqli_query($GLOBALS["mysqli"], $sql);
						while($lig=mysqli_fetch_object($res_mail)) {
							if(check_mail($lig->email)) {
								if($mail_dest!="") {
									$mail_dest.=",";
									$references_mail.="\r\n";
								}
								//$mail_dest.=$lig->email;
								if((!preg_match("/^$lig->email,/", $mail_dest))&&(!preg_match("/,$lig->email,/", $mail_dest))&&(!preg_match("/,$lig->email$/", $mail_dest))) {
									$mail_dest.=$lig->email;
									$tab_param_mail['destinataire'][]=$lig->email;
								}
								$references_mail.="proposition_remplacement_".$lig->id."_".$jour;
							}
						}

						if($mail_dest!="") {
							$tab_info_creneau=get_infos_creneau($id_creneau);
							$info_creneau=$tab_info_creneau['nom_creneau']." (".$tab_info_creneau['debut_court']."-".$tab_info_creneau['fin_court'].")";

							$date_debut_r=substr($jour, 0, 4)."-".substr($jour, 4, 2)."-".substr($jour, 6, 2)." 08:00:00";

							$designation_user=civ_nom_prenom($login_user);
							$subject = "[GEPI]: Remplacement attribué à ".$designation_user;
							$texte_mail="Bonjour ".$designation_user.",

Le remplacement suivant vous est attribué:

".get_nom_classe($id_classe)." le ".formate_date($date_debut_r,"n","complet")." en ".$info_creneau."
".$chaine_commentaire_validation.$chaine_salle."en remplacement de ".get_info_grp($id_groupe,array('description', 'matieres', 'classes', 'profs'), "").".

Merci.


Cordialement.
-- 
".civ_nom_prenom($_SESSION['login']);

							$headers = "";
							if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
								$headers.="Reply-to:".$_SESSION['email']."\r\n";
								$tab_param_mail['replyto']=$_SESSION['email'];
							}

							$message_id='remplacement_c'.$id_creneau."_j".$jour;
							if(isset($message_id)) {$headers .= "Message-id: $message_id\r\n";}
							//if(isset($references_mail)) {$headers .= "References: $references_mail\r\n";}

							// On envoie le mail
							$envoi = envoi_mail($subject, $texte_mail, $mail_dest, $headers, "plain", $tab_param_mail);
						}
					}

				}
				else {
					$msg="Erreur lors de l'attribution du remplacement à ".civ_nom_prenom($login_user).".<br />";
				}
			}
		}
	}
}

//=======================================================
// Récupération des informations sur l'absence courante:
$lig=mysqli_fetch_object($res_infos_id_absence_courante);

$date_debut=$lig->date_debut;
$annee_debut=mb_substr($date_debut, 0, 4);
$mois_debut=mb_substr($date_debut, 5, 2);
$jour_debut=mb_substr($date_debut, 8, 2);

$date_fin=$lig->date_fin;
$annee_fin=mb_substr($date_fin, 0, 4);
$mois_fin=mb_substr($date_fin, 5, 2);
$jour_fin=mb_substr($date_fin, 8, 2);

$display_date_debut=formate_date($date_debut);
$display_date_fin=formate_date($date_fin);

// Extraire l'heure de début/fin
$display_heure_debut=get_heure_2pt_minute_from_mysql_date($date_debut);
$display_heure_fin=get_heure_2pt_minute_from_mysql_date($date_fin);

$titre=$lig->titre;
$description=$lig->description;
$login_user=$lig->login_user;

$cpt=0;
$tab_propositions_deja_enregistrees=array('chaine', 'id_groupe', 'id_aid', 'id_classe', 'jour', 'id_creneau', 'login_user', 'commentaire_prof', 'reponse', 'date_reponse', 'validation_remplacement', 'commentaire_validation', 'salle', 'indice_chaine');
$sql="SELECT * FROM abs_prof_remplacement WHERE id_absence='$id_absence';";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	while($lig=mysqli_fetch_object($res)) {
		if(($lig->id_groupe!="")&&($lig->id_groupe!="0")) {
			$chaine=$lig->id_groupe."|".$lig->id_classe."|".$lig->jour."|".$lig->id_creneau."|".$lig->login_user;
		}
		else {
			$chaine="AID_".$lig->id_aid."|".$lig->id_classe."|".$lig->jour."|".$lig->id_creneau."|".$lig->login_user;
		}
		/*
		$tab_propositions_deja_enregistrees[$cpt]['chaine']=$chaine;
		$tab_propositions_deja_enregistrees[$cpt]['id_groupe']=$lig->id_groupe;
		$tab_propositions_deja_enregistrees[$cpt]['id_classe']=$lig->id_classe;
		$tab_propositions_deja_enregistrees[$cpt]['jour']=$lig->jour;
		$tab_propositions_deja_enregistrees[$cpt]['id_creneau']=$lig->id_creneau;
		$tab_propositions_deja_enregistrees[$cpt]['login_user']=$lig->login_user;
		$tab_propositions_deja_enregistrees[$cpt]['commentaire_prof']=$lig->commentaire_prof;
		$tab_propositions_deja_enregistrees[$cpt]['reponse']=$lig->reponse;
		$tab_propositions_deja_enregistrees[$cpt]['date_reponse']=$lig->date_reponse;
		$tab_propositions_deja_enregistrees[$cpt]['validation_remplacement']=$lig->validation_remplacement;
		$tab_propositions_deja_enregistrees[$cpt]['commentaire_validation']=$lig->commentaire_validation;
		$tab_propositions_deja_enregistrees[$cpt]['salle']=$lig->salle;
		*/
		$tab_propositions_deja_enregistrees['chaine'][$cpt]=$chaine;
		$tab_propositions_deja_enregistrees['id_groupe'][$cpt]=$lig->id_groupe;
		$tab_propositions_deja_enregistrees['id_aid'][$cpt]=$lig->id_aid;
		$tab_propositions_deja_enregistrees['id_classe'][$cpt]=$lig->id_classe;
		$tab_propositions_deja_enregistrees['jour'][$cpt]=$lig->jour;
		$tab_propositions_deja_enregistrees['id_creneau'][$cpt]=$lig->id_creneau;
		$tab_propositions_deja_enregistrees['login_user'][$cpt]=$lig->login_user;
		$tab_propositions_deja_enregistrees['commentaire_prof'][$cpt]=$lig->commentaire_prof;
		$tab_propositions_deja_enregistrees['reponse'][$cpt]=$lig->reponse;
		$tab_propositions_deja_enregistrees['date_reponse'][$cpt]=$lig->date_reponse;
		$tab_propositions_deja_enregistrees['validation_remplacement'][$cpt]=$lig->validation_remplacement;
		$tab_propositions_deja_enregistrees['commentaire_validation'][$cpt]=$lig->commentaire_validation;
		$tab_propositions_deja_enregistrees['salle'][$cpt]=$lig->salle;

		$tab_propositions_deja_enregistrees['indice_chaine'][$chaine]=$cpt;

		$cpt++;
	}
}
//=======================================================

// Configuration du calendrier
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

/*
$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";
*/

$avec_js_et_css_edt="y";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_suppression = "Confirmation de suppression";
//**************** EN-TETE *****************
$titre_page = "Proposition remplacement";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

//============================================================================================================
// Dispositif pour l'affichage EDT en infobulle
if((getSettingAOui('autorise_edt_tous'))||
	((getSettingAOui('autorise_edt_admin'))&&($_SESSION['statut']=='administrateur'))) {

	$titre_infobulle="EDT de <span id='id_ligne_titre_infobulle_edt'></span>";
	$texte_infobulle="";
	$tabdiv_infobulle[]=creer_div_infobulle('edt_prof',$titre_infobulle,"",$texte_infobulle,"",40,0,'y','y','n','n');

	function affiche_lien_edt_prof($login_prof, $info_prof) {
		return " <a href='../edt_organisation/index_edt.php?login_edt=".$login_prof."&amp;type_edt_2=prof&amp;no_entete=y&amp;no_menu=y&amp;lien_refermer=y' onclick=\"affiche_edt_prof_en_infobulle('$login_prof', '".addslashes($info_prof)."');return false;\" title=\"Emploi du temps de ".$info_prof."\" target='_blank'><img src='../images/icons/edt.png' class='icone16' alt='EDT' /></a>";
	}

	$titre_infobulle="EDT de la classe de <span id='span_id_nom_classe'></span>";
	$texte_infobulle="";
	$tabdiv_infobulle[]=creer_div_infobulle('edt_classe',$titre_infobulle,"",$texte_infobulle,"",40,0,'y','y','n','n');

	echo "
<style type='text/css'>
	.lecorps {
		margin-left:0px;
	}
</style>

<script type='text/javascript'>
	function affiche_edt_classe_en_infobulle(id_classe, classe) {
		document.getElementById('span_id_nom_classe').innerHTML=classe;

		new Ajax.Updater($('edt_classe_contenu_corps'),'../edt_organisation/index_edt.php?login_edt='+id_classe+'&type_edt_2=classe&visioedt=classe1&no_entete=y&no_menu=y&mode_infobulle=y',{method: 'get'});
		afficher_div('edt_classe','y',-20,20);
	}

	function affiche_edt_prof_en_infobulle(login_prof, info_prof) {
		document.getElementById('id_ligne_titre_infobulle_edt').innerHTML=info_prof;

		new Ajax.Updater($('edt_prof_contenu_corps'),'../edt_organisation/index_edt.php?login_edt='+login_prof+'&type_edt_2=prof&no_entete=y&no_menu=y&mode_infobulle=y',{method: 'get'});
		afficher_div('edt_prof','y',-20,20);
	}
</script>\n";
}
else {
	function affiche_lien_edt_prof($login_prof, $info_prof) {
		return "";
	}
}

//============================================================================================================
function affiche_lien_mailto_prof($mail_prof, $info_prof) {
	$retour=" <a href='mailto:".$mail_prof."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI&amp;body=";
	$tmp_date=getdate();
	if($tmp_date['hours']>=18) {$retour.="Bonsoir";} else {$retour.="Bonjour";}
	$retour.=" ".$info_prof;
	$retour.=",%0d%0aCordialement.' title=\"Envoyer un mail à $info_prof\">";
	$retour.="<img src='../images/icons/mail.png' class='icone16' alt='mail' />";
	$retour.="</a>";
	return $retour;
}
//============================================================================================================

$tab_jours_vacances=array();
// Commenter la ligne ci-dessous pour désactiver la prise en compte des jours de vacances:
$tab_jours_vacances=get_tab_jours_vacances();

//==================================================================
$civ_nom_prenom_absent=civ_nom_prenom($login_user);
$lien_mailto_absent="";
$mail_prof=get_mail_user($login_user);
if(check_mail($mail_prof)) {
	$lien_mailto_absent=affiche_lien_mailto_prof($mail_prof, $civ_nom_prenom_absent);
}

echo "<a name=\"debut_de_page\"></a>
<p class='bold'>
	<a href='index.php' onclick=\"return confirm_abandon (this, change, '".$themessage."')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>

<h2>Proposition de remplacement</h2>

<p><strong>Absence n°";

if(($_SESSION['statut']=="administrateur")||
(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AbsProfSaisieAbsScol')))||
(($_SESSION['statut']=="cpe")&&(getSettingAOui('AbsProfSaisieAbsCpe')))) {
	echo "<a href='saisir_absence.php?id_absence=$id_absence' title=\"Modifier l'absence.\" onclick=\"return confirm_abandon (this, change, '".$themessage."')\">$id_absence</a>";
}
else {
	echo $id_absence;
}
echo "&nbsp;:</strong> ".$civ_nom_prenom_absent." ".affiche_lien_edt_prof($login_user, $civ_nom_prenom_absent)." (<em>$lien_mailto_absent</em>) est absent(e) du ".formate_date($date_debut,"y","complet")." au ".formate_date($date_fin,"y","complet")."</p>";

$chaine_js_var_user="var nom_user=new Array();\n";
$chaine_js_var_user.="nom_user['$login_user']=\"$civ_nom_prenom_absent\";\n";
$chaine_js_var_classe="var nom_classe=new Array();\n";
/*
echo "jour_debut=$jour_debut<br />";
echo "mois_debut=$mois_debut<br />";
echo "annee_debut=$annee_debut<br />";
*/

//===================================================================
// Récupérer la liste des créneaux
$sql="SELECT * FROM edt_creneaux ORDER BY heuredebut_definie_periode;";
$res_abs_cren=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_abs_cren)==0) {
	echo "<p style='color:red'>Aucun créneau n'a été trouvé dans 'edt_creneaux'.</p>";
	require("../lib/footer.inc.php");
	die();
}

$chaine_js_var_creneau="var nom_creneau=new Array();\n";

// id_definie_periode | nom_definie_periode | heuredebut_definie_periode | heurefin_definie_periode | suivi_definie_periode | type_creneaux | jour_creneau
$cpt=0;
$tab_id_definie_periode=array();
$tab_creneau=array();
$tab_creneau_cours=array();
while($lig=mysqli_fetch_object($res_abs_cren)) {
	$tab_creneau[$cpt]['id_definie_periode']=$lig->id_definie_periode;
	$tab_creneau[$cpt]['nom_definie_periode']=$lig->nom_definie_periode;
	$tab_creneau[$cpt]['heuredebut_definie_periode']=$lig->heuredebut_definie_periode;
	$tab_creneau[$cpt]['heurefin_definie_periode']=$lig->heurefin_definie_periode;
	$tab_creneau[$cpt]['suivi_definie_periode']=$lig->suivi_definie_periode;
	$tab_creneau[$cpt]['type_creneaux']=$lig->type_creneaux;
	$tab_creneau[$cpt]['jour_creneau']=$lig->jour_creneau;

	if($lig->type_creneaux=='cours') {
		$tab_creneau_cours[]=$lig->id_definie_periode;
	}

	$tab_id_definie_periode[$lig->id_definie_periode]=$cpt;

	$chaine_js_var_creneau.="nom_creneau[$lig->id_definie_periode]=\"$lig->nom_definie_periode\";\n";

	$cpt++;
}


$tab_creneau_cours_suivant=array();
for($loop=0;$loop<count($tab_creneau_cours);$loop++) {
	if(isset($tab_creneau_cours[$loop+1])) {
		$tab_creneau_cours_suivant[$tab_creneau_cours[$loop]]=$tab_creneau_cours[$loop+1];
	}
}
//===================================================================


//$sql="SELECT * FROM edt_cours ec, edt_creneaux ecr WHERE login_prof='$login_user' AND jour_semaine='".strftime("%A", $timestamp_courant)."' AND ec.id_definie_periode=ecr.id_definie_periode ORDER BY ecr.heuredebut_definie_periode;";

function get_cours_prof($login, $jour, $timestamp="") {
	$tab=array();

	$sql="SELECT ec.* FROM edt_cours ec, 
					edt_creneaux ecr 
				WHERE login_prof='$login' AND 
					jour_semaine='".$jour."' AND 
					ec.id_definie_periode=ecr.id_definie_periode";
	if($timestamp!="") {
		$sql.="		 AND (id_semaine='0' OR id_semaine='".get_type_semaine(strftime('%V', $timestamp))."')";
	}
	$sql.="		ORDER BY ecr.heuredebut_definie_periode;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab[$lig->id_definie_periode]=$lig->id_cours;
		}
	}
	return $tab;
}

function get_cours_prof2($login, $jour, $timestamp="") {
	global $tab_creneau_cours_suivant;

	$tab=array();

	$sql="SELECT ec.* FROM edt_cours ec, 
					edt_creneaux ecr 
				WHERE login_prof='$login' AND 
					jour_semaine='".$jour."' AND 
					ec.id_definie_periode=ecr.id_definie_periode";
	if($timestamp!="") {
		$sql.="		 AND (id_semaine='0' OR id_semaine='".get_type_semaine(strftime('%V', $timestamp))."')";
	}
	$sql.="		ORDER BY ecr.heuredebut_definie_periode;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if($lig->heuredeb_dec==0) {
				$tab[$lig->id_definie_periode][0]['id_cours']=$lig->id_cours;
				if(($lig->duree>2)&&($lig->duree<=4)) {
					// entre 1.5 et 2h
					if(isset($tab_creneau_cours_suivant[$lig->id_definie_periode])) {
						$id_creneau_suivant=$tab_creneau_cours_suivant[$lig->id_definie_periode];
						$tab[$id_creneau_suivant][0]['id_cours']=$lig->id_cours;
					}
					else {
						// Anomalie
					}
				}
				elseif(($lig->duree>4)&&($lig->duree<=6)) {
					// entre 2.5 et 3h
					if(isset($tab_creneau_cours_suivant[$lig->id_definie_periode])) {
						$id_creneau_suivant=$tab_creneau_cours_suivant[$lig->id_definie_periode];
						$tab[$id_creneau_suivant][0]['id_cours']=$lig->id_cours;

						if(isset($tab_creneau_cours_suivant[$id_creneau_suivant])) {
							$id_creneau_suivant=$tab_creneau_cours_suivant[$id_creneau_suivant];
							$tab[$id_creneau_suivant][0]['id_cours']=$lig->id_cours;
						}
					}
					else {
						// Anomalie
					}
				}
				elseif(($lig->duree>6)&&($lig->duree<=8)) {
					// entre 3.5 et 4h
					if(isset($tab_creneau_cours_suivant[$lig->id_definie_periode])) {
						$id_creneau_suivant=$tab_creneau_cours_suivant[$lig->id_definie_periode];
						$tab[$id_creneau_suivant][0]['id_cours']=$lig->id_cours;

						if(isset($tab_creneau_cours_suivant[$id_creneau_suivant])) {
							$id_creneau_suivant=$tab_creneau_cours_suivant[$id_creneau_suivant];
							$tab[$id_creneau_suivant][0]['id_cours']=$lig->id_cours;

							if(isset($tab_creneau_cours_suivant[$id_creneau_suivant])) {
								$id_creneau_suivant=$tab_creneau_cours_suivant[$id_creneau_suivant];
								$tab[$id_creneau_suivant][0]['id_cours']=$lig->id_cours;
							}
						}
					}
					else {
						// Anomalie
					}
				}
			}
			else {
				// Le cours commence sur un demi-creneau
				$tab[$lig->id_definie_periode][1]['id_cours']=$lig->id_cours;

				if(($lig->duree>1)&&($lig->duree<=3)) {
					// entre 1 et 1.5h
					if(isset($tab_creneau_cours_suivant[$lig->id_definie_periode])) {
						$id_creneau_suivant=$tab_creneau_cours_suivant[$lig->id_definie_periode];
						$tab[$id_creneau_suivant][0]['id_cours']=$lig->id_cours;
					}
					else {
						// Anomalie
					}
				}
				elseif(($lig->duree>4)&&($lig->duree<=6)) {
					// entre 2 et 2.5h
					if(isset($tab_creneau_cours_suivant[$lig->id_definie_periode])) {
						$id_creneau_suivant=$tab_creneau_cours_suivant[$lig->id_definie_periode];
						$tab[$id_creneau_suivant][0]['id_cours']=$lig->id_cours;

						if(isset($tab_creneau_cours_suivant[$id_creneau_suivant])) {
							$id_creneau_suivant=$tab_creneau_cours_suivant[$id_creneau_suivant];
							$tab[$id_creneau_suivant][0]['id_cours']=$lig->id_cours;
						}
					}
					else {
						// Anomalie
					}
				}
				elseif(($lig->duree>6)&&($lig->duree<=8)) {
					// entre 3 et 3.5h
					if(isset($tab_creneau_cours_suivant[$lig->id_definie_periode])) {
						$id_creneau_suivant=$tab_creneau_cours_suivant[$lig->id_definie_periode];
						$tab[$id_creneau_suivant][0]['id_cours']=$lig->id_cours;

						if(isset($tab_creneau_cours_suivant[$id_creneau_suivant])) {
							$id_creneau_suivant=$tab_creneau_cours_suivant[$id_creneau_suivant];
							$tab[$id_creneau_suivant][0]['id_cours']=$lig->id_cours;

							if(isset($tab_creneau_cours_suivant[$id_creneau_suivant])) {
								$id_creneau_suivant=$tab_creneau_cours_suivant[$id_creneau_suivant];
								$tab[$id_creneau_suivant][0]['id_cours']=$lig->id_cours;
							}
						}
					}
					else {
						// Anomalie
					}
				}

			}
		}
	}
	return $tab;
}

$groups=array();

$cpt=0;
$cpt2=0;
$tab_num_proposition=array();
$tab_num_proposition2=array();

// Récupérer la liste des jours
//$jour_courant=$annee_debut.$mois_debut.$jour_debut;
$timestamp_courant=mktime (12, 59, 59 , $mois_debut, $jour_debut, $annee_debut);
//$jour_fin=$annee_fin.$mois_fin.$jour_fin;
$timestamp_fin=mktime (13, 59, 59 , $mois_fin, $jour_fin, $annee_fin);
if($timestamp_fin<$timestamp_courant) {
	echo "<p style='color:red'>Anomalie&nbsp;: La date de fin semble antérieure à la date de début de l'absence.</p>";
	require("../lib/footer.inc.php");
	die();
}

// Récupération de la liste des jours d'ouverture de l'établissement
$tab_jour=get_tab_jour_ouverture_etab();

$tab_jour_u=array();
$tab_jour_u[1]="lundi";
$tab_jour_u[2]="mardi";
$tab_jour_u[3]="mercredi";
$tab_jour_u[4]="jeudi";
$tab_jour_u[5]="vendredi";
$tab_jour_u[6]="samedi";
$tab_jour_u[7]="dimanche";

$AbsProfGroupesClasseSeulement=getSettingValue('AbsProfGroupesClasseSeulement');

$tmp_tab_profs_exclus_des_propositions_de_remplacement=get_tab_profs_exclus_des_propositions_de_remplacement();
$tab_profs_refusant_toute_proposition_de_remplacement=get_tab_profs_refusant_toute_proposition_de_remplacement();
$tab_profs_exclus_des_propositions_de_remplacement=array_merge($tmp_tab_profs_exclus_des_propositions_de_remplacement, $tab_profs_refusant_toute_proposition_de_remplacement);

$tab_matieres_exclues_des_propositions_de_remplacement=get_tab_matieres_exclues_des_propositions_de_remplacement();

/*
echo "<pre>";
print_r($tab_profs_exclus_des_propositions_de_remplacement);
echo "</pre>";
*/

$sql="SELECT * FROM abs_prof WHERE id='$id_absence';";
$res_abs=mysqli_query($GLOBALS['mysqli'], $sql);
if(mysqli_num_rows($res_abs)==0) {
	echo "<p style='color:red'>Absence n°$id_absence non trouvée.</p>";
	require("../lib/footer.inc.php");
	die();
}
$tab_abs=mysqli_fetch_array($res_abs);
$ts_debut_abs=mysql_date_to_unix_timestamp($tab_abs['date_debut']);
$ts_fin_abs=mysql_date_to_unix_timestamp($tab_abs['date_fin']);
//echo "<p>Absence du ".strftime("%A %d/%m/%Y à %H:%M:%S", $ts_debut_abs)." au ".strftime("%A %d/%m/%Y à %H:%M:%S", $ts_fin_abs)."</p>";

$texte="<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" style=\"width: 100%;\" name=\"form0\" onSubmit=\"submit_div_validation_remplacement()\">
	".add_token_field()."
	<input type='hidden' name='id_absence' value='$id_absence' />
	<input type='hidden' name='valider_remplacement_ancre' id='valider_remplacement_ancre' value='' />
	<input type='hidden' name='valider_proposition' id='valider_proposition' value='' />
	<p>Attribuer le remplacement en classe de <span id='valider_remplacement_classe'></span> à <span id='valider_remplacement_nom_user'></span> le <span id='valider_remplacement_jour'></span> en <span id='valider_remplacement_creneau'></span>.</p>
	<table>
		<tr style='vertical-align:top;'>
			<td>
				Salle&nbsp;: 
			</td>
			<td>
				<input type='text' name='salle' value=\"\" />
			</td>
		</tr>
		<tr style='vertical-align:top;'>
			<td>
				Commentaire&nbsp;: 
			</td>
			<td>
				<textarea name='commentaire_validation' style='vertical-align:top;'></textarea>
			</td>
		</tr>
	</table>

	<p><input type='submit' value='Valider le remplacement' /></p>
</form>";
$tabdiv_infobulle[]=creer_div_infobulle("div_valider_remplacement","Validation","",$texte,"",30,0,'y','y','n','n',2);

echo "<form action=\"".$_SERVER['PHP_SELF']."#debut_de_page\" method=\"post\" style=\"width: 100%;\" name=\"form1\">
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='id_absence' value='$id_absence' />";

$tab_jours_remplacements=array();
while($timestamp_courant<=$timestamp_fin) {
	// Ne retenir que les jours ouvrés

	// Rechercher les cours du prof dans l'EDT:
	//echo "get_cours_prof($login_user, ".strftime("%A", $timestamp_courant).", $timestamp_courant)<br />";
	//$tab_cours_prof_absent=get_cours_prof($login_user, strftime("%A", $timestamp_courant), $timestamp_courant);
	$tab_cours_prof_absent=get_cours_prof2($login_user, strftime("%A", $timestamp_courant), $timestamp_courant);
	/*
	echo "<pre>";
	print_r($tab_cours_prof_absent);
	echo "</pre>";
	*/
	$date_aaaa_mm_jj=strftime("%Y-%m-%d", $timestamp_courant);

	$date_aaaammjj=strftime("%Y%m%d", $timestamp_courant);
	$tab_jours_remplacements[]=$date_aaaammjj;
	echo "
<a name='jour_".$date_aaaammjj."'></a>
<h4>".ucfirst(strftime("%A %d/%m/%Y", $timestamp_courant))."</h4>

<table class='boireaus boireaus_alt' style='margin-left:3em;'>
	<thead>
		<tr>
			<th rowspan='2'>Créneau</th>
			<th rowspan='2'>Cours</th>
			<th rowspan='2'>Classe</th>
			<th>Professeurs susceptibles de remplacer</th>
			<!--
			<th colspan='2'>Professeurs susceptibles de remplacer</th>
			-->
		</tr>
		<tr>
			<th>
				Profs de la classe sans cours<br />
				<a href='#' onclick=\"tout_cocher_sans_cours();return false;\" title=\"Cocher tous les professeurs proposés pour tous les jours et créneaux d'absence proposés.\"><img src='../images/enabled.png' class='icone16' alt='Cocher' /></a>/<a href='#' onclick=\"tout_decocher_sans_cours();return false;\" title=\"Décocher tous les professeurs proposés pour tous les jours et créneaux d'absence proposés.\"><img src='../images/disabled.png' class='icone16' alt='Décocher' /></a>
			</th>
			<!--
			<th>
				Profs sans cours
			</th>
			-->
		</tr>
	</thead>
	<tbody>";
	for($loop=0;$loop<count($tab_creneau);$loop++) {
		$ts_test_debut=mysql_date_to_unix_timestamp($date_aaaa_mm_jj." ".$tab_creneau[$loop]['heuredebut_definie_periode']);
		$ts_test_fin=mysql_date_to_unix_timestamp($date_aaaa_mm_jj." ".$tab_creneau[$loop]['heurefin_definie_periode']);
/*
echo "<tr><td colspan='5'>
<p>Absence du ".strftime("%A %d/%m/%Y à %H:%M:%S", $ts_debut_abs)." ($ts_debut_abs) au ".strftime("%A %d/%m/%Y à %H:%M:%S", $ts_fin_abs)." ($ts_fin_abs)</p>
<p>Creneau courant du ".strftime("%A %d/%m/%Y à %H:%M:%S", $ts_test_debut)." ($ts_test_debut) au ".strftime("%A %d/%m/%Y à %H:%M:%S", $ts_test_fin)." ($ts_test_fin)</p>
</td></tr>";
*/
		if(($ts_test_debut>=$ts_debut_abs)&&($ts_test_fin<=$ts_fin_abs)) {
			$temoin_num_proposition=0;
			$id_groupe_courant="";
			$id_aid_courant="";
			$tr_style="";
			if($tab_creneau[$loop]['type_creneaux']!='cours') {
				$tr_style=" style='background-color:gray;'";
			}

			$chaine_rowspan="";
			if(isset($tab_cours_prof_absent[$tab_creneau[$loop]['id_definie_periode']])) {
				if(count($tab_cours_prof_absent[$tab_creneau[$loop]['id_definie_periode']])>1) {
					$chaine_rowspan=" rowspan='".count($tab_cours_prof_absent[$tab_creneau[$loop]['id_definie_periode']])."'";
				}
			}

			$id_creneau_courant=$tab_creneau[$loop]['id_definie_periode'];

			echo "
			<tr".$tr_style.">
				<td".$chaine_rowspan.">".$tab_creneau[$loop]['nom_definie_periode']."</td>";
			$temoin_cours="n";
			if(isset($tab_cours_prof_absent[$tab_creneau[$loop]['id_definie_periode']])) {
				$cpt_ligne=0;
				foreach($tab_cours_prof_absent[$tab_creneau[$loop]['id_definie_periode']] as $key => $value) {
					if($cpt_ligne>0) {
						echo "
			</tr>
			<tr>";
					}
					echo "
				<td>";
					$cpt_ligne++;

					$temoin_cours="y";
					//$tab_id_cours=get_tab_id_cours($tab_cours_prof_absent[$tab_creneau[$loop]['id_definie_periode']]);
					$tab_id_cours=get_tab_id_cours($value['id_cours']);
					/*
					echo "<pre>";
					print_r($tab_id_cours);
					echo "</pre>";
					*/
					if((isset($tab_id_cours['id_groupe']))&&($tab_id_cours['id_groupe']!="")) {
						$id_groupe_courant=$tab_id_cours['id_groupe'];
						if(!isset($groups[$id_groupe_courant])) {
							$groups[$id_groupe_courant]=get_group($id_groupe_courant, array('matieres', 'classes', 'profs'));
						}

						echo "<span title=\"".$groups[$id_groupe_courant]['description']."
	Matière :          ".$groups[$id_groupe_courant]["matiere"]["matiere"]." (".$groups[$id_groupe_courant]["matiere"]["nom_complet"].")
	Classe(s) :       ".$groups[$id_groupe_courant]["classlist_string"]."
	Professeur(s) : ".$groups[$id_groupe_courant]['profs']['proflist_string']."\">".$groups[$id_groupe_courant]['name']."</span>";
					}
					elseif((isset($tab_id_cours['id_aid']))&&($tab_id_cours['id_aid']!="")) {
						$id_aid_courant=$tab_id_cours['id_aid'];
						if(!isset($aid[$id_aid_courant])) {
							$aid[$id_aid_courant]=get_tab_aid($id_aid_courant);
						}

						echo "<span title=\"".$aid[$id_aid_courant]['nom_aid']."
".$aid[$id_aid_courant]['nom_general_complet']."
	Classe(s) :       ".$aid[$id_aid_courant]["classlist_string"]."
	Professeur(s) : ".$aid[$id_aid_courant]['profs']['proflist_string']."\">".$aid[$id_aid_courant]['nom_aid']."</span>";
					}
					else {
						//echo get_info_id_cours($tab_cours_prof_absent[$tab_creneau[$loop]['id_definie_periode']]);
						echo get_info_id_cours($value['id_cours']);
					}

					echo "</td>
				<td>";
					if(isset($groups[$id_groupe_courant])) {
						foreach($groups[$id_groupe_courant]['classes']['classes'] as $current_id_classe => $current_tab_classe) {
							echo $current_tab_classe['classe']."<br />";
						}
					}
					elseif(isset($aid[$id_aid_courant])) {
						foreach($aid[$id_aid_courant]['classes']['classes'] as $current_id_classe => $current_tab_classe) {
							echo $current_tab_classe['classe']."<br />";
						}
					}
					/*
					echo "
					<a href='#' onclick=\"cocher_sans_cours(".$date_aaaammjj.",".$tab_creneau[$loop]['id_definie_periode'].");return false;\" title=\"Cocher tous les professeurs proposés pour le créneau courant.\"><img src='../images/enabled.png' class='icone16' alt='Cocher' /></a>/<a href='#' onclick=\"decocher_sans_cours(".$date_aaaammjj.",".$tab_creneau[$loop]['id_definie_periode'].");return false;\" title=\"Décocher tous les professeurs proposés pour le créneau courant.\"><img src='../images/disabled.png' class='icone16' alt='Décocher' /></a>";
					*/

					echo "
				</td>
				<td style='text-align:left;'>

					<a name='jour_".$date_aaaammjj."_creneau_".$id_creneau_courant."'></a>";

					// 20150420 : Tester si on ne veut pas remplacer les cours qui ne correspondent pas à des groupes classe.

					if(isset($groups[$id_groupe_courant])) {
						if(($AbsProfGroupesClasseSeulement!="yes")||(count($groups[$id_groupe_courant]['classes']['list'])==1)) {
							if(!in_array($groups[$id_groupe_courant]['matiere']['matiere'], $tab_matieres_exclues_des_propositions_de_remplacement)) {
								echo "
					<div style='float:right; width:40px;'>
						<a href='#' onclick=\"cocher_sans_cours(".$date_aaaammjj.",".$tab_creneau[$loop]['id_definie_periode'].");return false;\" title=\"Cocher tous les professeurs proposés pour le créneau courant.\"><img src='../images/enabled.png' class='icone16' alt='Cocher' /></a>/<a href='#' onclick=\"decocher_sans_cours(".$date_aaaammjj.",".$tab_creneau[$loop]['id_definie_periode'].");return false;\" title=\"Décocher tous les professeurs proposés pour le créneau courant.\"><img src='../images/disabled.png' class='icone16' alt='Décocher' /></a>
					</div>";
							}
						}
					}
					elseif(isset($aid[$id_aid_courant])) {
						if(($AbsProfGroupesClasseSeulement!="yes")||(count($aid[$id_aid_courant]['classes']['list'])==1)) {
							echo "
					<div style='float:right; width:40px;'>
						<a href='#' onclick=\"cocher_sans_cours(".$date_aaaammjj.",".$tab_creneau[$loop]['id_definie_periode'].");return false;\" title=\"Cocher tous les professeurs proposés pour le créneau courant.\"><img src='../images/enabled.png' class='icone16' alt='Cocher' /></a>/<a href='#' onclick=\"decocher_sans_cours(".$date_aaaammjj.",".$tab_creneau[$loop]['id_definie_periode'].");return false;\" title=\"Décocher tous les professeurs proposés pour le créneau courant.\"><img src='../images/disabled.png' class='icone16' alt='Décocher' /></a>
					</div>";
						}
					}

					// +++++++++++++++++++++++
					// A FAIRE : Tester aussi si le prof a déjà accepté un remplacement
					// Récupérer la liste des profs ayant accepté un remplacement sur ce créneau
					//           et les remplacements acceptés... et une fois validés/confirmés, ne faire apparaitre que les remplacements acceptés en lieu et place des propositions.
					//           Pouvoir compter/extraire la liste des remplacements acceptés (listing par jour/classe et listing par prof pour rémunération).
					// +++++++++++++++++++++++

					// Professeurs de la classe:
					// Dans le cas d'un groupe avec plusieurs classes, on peut récupérer deux fois le même prof (prof qui aurait les différentes classes)
					//$tab_profs_deja_proposes=array();
					if(isset($groups[$id_groupe_courant])) {
						if(($AbsProfGroupesClasseSeulement!="yes")||(count($groups[$id_groupe_courant]['classes']['list'])==1)) {
							if(!in_array($groups[$id_groupe_courant]['matiere']['matiere'], $tab_matieres_exclues_des_propositions_de_remplacement)) {
								foreach($groups[$id_groupe_courant]['classes']['classes'] as $current_id_classe => $current_tab_classe) {
									$sql="SELECT DISTINCT u.login, nom, prenom FROM utilisateurs u, 
														j_groupes_classes jgc, 
														j_groupes_professeurs jgp
													WHERE u.login=jgp.login AND 
														jgp.id_groupe=jgc.id_groupe AND 
														jgc.id_classe='$current_id_classe' 
													ORDER BY u.nom, u.prenom;";
									//echo "$sql<br />";
									$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_prof)>0) {
										if(count($groups[$id_groupe_courant]['classes']['classes']>1)) {
											echo "
								<a name='jour_".$date_aaaammjj."_creneau_".$id_creneau_courant."_classe_".$current_id_classe."'></a>
								<span class='bold'>".$current_tab_classe['classe']."</span>";
										}

										if(!preg_match("/nom_classe\[$current_id_classe\]/", $chaine_js_var_classe)) {
											$chaine_js_var_classe.="nom_classe[$current_id_classe]=\"".$current_tab_classe['classe']."\";\n";
										}

										echo "
								<table>";
										while($lig_prof=mysqli_fetch_object($res_prof)) {
											//if(!in_array($lig_prof->login, $tab_profs_deja_proposes)) {

												// +++++++++++++++++++++++
												// A FAIRE : Tester aussi si le prof a déjà accepté un remplacement
												// +++++++++++++++++++++++

											if(!in_array($lig_prof->login, $tab_profs_exclus_des_propositions_de_remplacement)) {

												$tab_cours_prof_courant=get_cours_prof2($lig_prof->login, strftime("%A", $timestamp_courant), $timestamp_courant);
												// Il faudrait affiner avec les longueurs de cours...
												//if(!isset($tab_cours_prof_courant[$tab_creneau[$loop]['id_definie_periode']][$key])) {
												if((!isset($tab_cours_prof_courant[$tab_creneau[$loop]['id_definie_periode']][0]))&&
												(!isset($tab_cours_prof_courant[$tab_creneau[$loop]['id_definie_periode']][1]))) {
													$denomination_prof_courant=civ_nom_prenom($lig_prof->login);

													if(!preg_match("/'$lig_prof->login'/", $chaine_js_var_user)) {
														$chaine_js_var_user.="nom_user['$lig_prof->login']=\"$denomination_prof_courant\";\n";
													}

													$lien_mailto_courant="";
													$mail_prof_courant=get_mail_user($lig_prof->login);
													if(check_mail($mail_prof_courant)) {
														$lien_mailto_courant=" ".affiche_lien_mailto_prof($mail_prof_courant, $denomination_prof_courant);
													}


													if($temoin_num_proposition==0) {
														$tab_num_proposition[$date_aaaammjj][$tab_creneau[$loop]['id_definie_periode']]=$cpt;
														$temoin_num_proposition++;
													}

													$chaine=$id_groupe_courant."|".$current_id_classe."|".$date_aaaammjj."|".$id_creneau_courant."|".$lig_prof->login;
													$checked="";
													if((isset($tab_propositions_deja_enregistrees['chaine']))&&(in_array($chaine, $tab_propositions_deja_enregistrees['chaine']))) {
														$checked=" checked";
													}

													$td_bg="";
													$reponse="<img src=\"../images/case_blanche.png\" alt='Pas de réponse' title=\"Le professeur n'a pas répondu à la proposition.\" />";
													if(isset($tab_propositions_deja_enregistrees['indice_chaine'][$chaine])) {
														$indice_prop=$tab_propositions_deja_enregistrees['indice_chaine'][$chaine];
														if($tab_propositions_deja_enregistrees['reponse'][$indice_prop]=='oui') {
															// Permettre de valider le remplacement
															$reponse="<a href='".$_SERVER['PHP_SELF']."?id_absence=$id_absence&amp;valider_proposition=".$chaine.add_token_in_url()."#jour_".$date_aaaammjj."_creneau_".$id_creneau_courant."' onclick=\"if(confirm_abandon (this, change, '".$themessage."')) {afficher_div_validation('$chaine', 'jour_".$date_aaaammjj."_creneau_".$id_creneau_courant."')}; return false;\"><img src=\"../images/vert.png\" alt='Oui' title=\"Le professeur accepte la proposition.
		".(($tab_propositions_deja_enregistrees['commentaire_prof'][$indice_prop]!="") ? $tab_propositions_deja_enregistrees['commentaire_prof'][$indice_prop] : "")."
		Réponse donnée le ".formate_date($tab_propositions_deja_enregistrees['date_reponse'][$indice_prop], "y").".

		Il faut encore que vous validiez/confirmiez l'attribution.
		Si plusieurs professeurs acceptent la proposition, 
		il est même indispensable de choisir lequel assurera le remplacement.\" />";
															$td_bg=" style='background-color:#FFF168;'";
														}
														elseif($tab_propositions_deja_enregistrees['reponse'][$indice_prop]=='non') {
															$reponse="<img src=\"../images/rouge.png\" alt='Non' title=\"Le professeur ne souhaite pas effectuer ce remplacement.
		".(($tab_propositions_deja_enregistrees['commentaire_prof'][$indice_prop]!="") ? $tab_propositions_deja_enregistrees['commentaire_prof'][$indice_prop] : "")."
		Réponse donnée le ".formate_date($tab_propositions_deja_enregistrees['date_reponse'][$indice_prop], "y").".\" />";
															$td_bg=" style='background-color:grey;'";
														}

														if($tab_propositions_deja_enregistrees['validation_remplacement'][$indice_prop]=='oui') {
															$reponse="<img src=\"../images/enabled.png\" alt='Confirmé' title=\"Le remplacement est confirmé.\" />";
															$td_bg=" style='background-color:aquamarine;'";
														}
													}

													echo "
									<tr class='fieldset_opacite50'>
										<td>
											<input type='checkbox' name='proposition[]' id='proposition_$cpt' value='".$chaine."' onchange=\"checkbox_change('proposition_$cpt');changement()\"$checked />
										</td>
										<td".$td_bg.">
											<label for='proposition_$cpt' id='texte_proposition_$cpt'>".$denomination_prof_courant."</label>
										</td>
										<td".$td_bg.">
											$reponse
										</td>
										<td>
											".affiche_lien_edt_prof($lig_prof->login, $denomination_prof_courant)."
										</td>
										<td>
											".$lien_mailto_courant."
										</td>
									</tr>";

													$cpt++;
												}
												/*
												else {
													echo "<tr><td></td><td><span style='color:red'>".$lig_prof->login."</span></td></tr>";
												}
												*/
												//$tab_profs_deja_proposes[]=$lig_prof->login;

											}

											//}
										}
										echo "
								</table>";
									}
									else {
										echo "<span style='color:red'>Aucun prof trouvé.</span>";
									}
								}
							}
						}
						elseif(($AbsProfGroupesClasseSeulement=="yes")&&(count($groups[$id_groupe_courant]['classes']['list'])>1)) {
							echo "Ce n'est pas un groupe classe et<br /><span title=\"Cela peut être modifié dans\nGestion des modules/Remplacements.\">vous ne souhaitez pas proposer au remplacement</span><br />les groupes/enseignements<br />qui ne sont pas des groupes classe.";
						}
					}
					elseif(isset($aid[$id_aid_courant])) {
						if(($AbsProfGroupesClasseSeulement!="yes")||(count($aid[$id_aid_courant]['classes']['list'])==1)) {
							foreach($aid[$id_aid_courant]['classes']['classes'] as $current_id_classe => $current_tab_classe) {
//20151006
								$sql="SELECT DISTINCT u.login, nom, prenom FROM utilisateurs u, 
													j_groupes_classes jgc, 
													j_groupes_professeurs jgp
												WHERE u.login=jgp.login AND 
													jgp.id_groupe=jgc.id_groupe AND 
													jgc.id_classe='$current_id_classe' 
												ORDER BY u.nom, u.prenom;";
								//echo "$sql<br />";
								$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_prof)>0) {
									if(count($aid[$id_aid_courant]['classes']['classes']>1)) {
										echo "
							<a name='jour_".$date_aaaammjj."_creneau_".$id_creneau_courant."_classe_".$current_id_classe."'></a>
							<span class='bold'>".$current_tab_classe['classe']."</span>";
									}

									if(!preg_match("/nom_classe\[$current_id_classe\]/", $chaine_js_var_classe)) {
										$chaine_js_var_classe.="nom_classe[$current_id_classe]=\"".$current_tab_classe['classe']."\";\n";
									}

									echo "
							<table>";
									while($lig_prof=mysqli_fetch_object($res_prof)) {
										//if(!in_array($lig_prof->login, $tab_profs_deja_proposes)) {

											// +++++++++++++++++++++++
											// A FAIRE : Tester aussi si le prof a déjà accepté un remplacement
											// +++++++++++++++++++++++

										if(!in_array($lig_prof->login, $tab_profs_exclus_des_propositions_de_remplacement)) {

											$tab_cours_prof_courant=get_cours_prof2($lig_prof->login, strftime("%A", $timestamp_courant), $timestamp_courant);
											// Il faudrait affiner avec les longueurs de cours...
											//if(!isset($tab_cours_prof_courant[$tab_creneau[$loop]['id_definie_periode']][$key])) {
											if((!isset($tab_cours_prof_courant[$tab_creneau[$loop]['id_definie_periode']][0]))&&
											(!isset($tab_cours_prof_courant[$tab_creneau[$loop]['id_definie_periode']][1]))) {
												$denomination_prof_courant=civ_nom_prenom($lig_prof->login);

												if(!preg_match("/'$lig_prof->login'/", $chaine_js_var_user)) {
													$chaine_js_var_user.="nom_user['$lig_prof->login']=\"$denomination_prof_courant\";\n";
												}

												$lien_mailto_courant="";
												$mail_prof_courant=get_mail_user($lig_prof->login);
												if(check_mail($mail_prof_courant)) {
													$lien_mailto_courant=" ".affiche_lien_mailto_prof($mail_prof_courant, $denomination_prof_courant);
												}


												if($temoin_num_proposition==0) {
													$tab_num_proposition[$date_aaaammjj][$tab_creneau[$loop]['id_definie_periode']]=$cpt;
													$temoin_num_proposition++;
												}

												$chaine="AID_".$id_aid_courant."|".$current_id_classe."|".$date_aaaammjj."|".$id_creneau_courant."|".$lig_prof->login;
												$checked="";
												if((isset($tab_propositions_deja_enregistrees['chaine']))&&(in_array($chaine, $tab_propositions_deja_enregistrees['chaine']))) {
													$checked=" checked";
												}

												$td_bg="";
												$reponse="<img src=\"../images/case_blanche.png\" alt='Pas de réponse' title=\"Le professeur n'a pas répondu à la proposition.\" />";
												if(isset($tab_propositions_deja_enregistrees['indice_chaine'][$chaine])) {
													$indice_prop=$tab_propositions_deja_enregistrees['indice_chaine'][$chaine];
													if($tab_propositions_deja_enregistrees['reponse'][$indice_prop]=='oui') {
														// Permettre de valider le remplacement
														$reponse="<a href='".$_SERVER['PHP_SELF']."?id_absence=$id_absence&amp;valider_proposition=".$chaine.add_token_in_url()."#jour_".$date_aaaammjj."_creneau_".$id_creneau_courant."' onclick=\"if(confirm_abandon (this, change, '".$themessage."')) {afficher_div_validation('$chaine', 'jour_".$date_aaaammjj."_creneau_".$id_creneau_courant."')}; return false;\"><img src=\"../images/vert.png\" alt='Oui' title=\"Le professeur accepte la proposition.
	".(($tab_propositions_deja_enregistrees['commentaire_prof'][$indice_prop]!="") ? $tab_propositions_deja_enregistrees['commentaire_prof'][$indice_prop] : "")."
	Réponse donnée le ".formate_date($tab_propositions_deja_enregistrees['date_reponse'][$indice_prop], "y").".

	Il faut encore que vous validiez/confirmiez l'attribution.
	Si plusieurs professeurs acceptent la proposition, 
	il est même indispensable de choisir lequel assurera le remplacement.\" />";
														$td_bg=" style='background-color:#FFF168;'";
													}
													elseif($tab_propositions_deja_enregistrees['reponse'][$indice_prop]=='non') {
														$reponse="<img src=\"../images/rouge.png\" alt='Non' title=\"Le professeur ne souhaite pas effectuer ce remplacement.
	".(($tab_propositions_deja_enregistrees['commentaire_prof'][$indice_prop]!="") ? $tab_propositions_deja_enregistrees['commentaire_prof'][$indice_prop] : "")."
	Réponse donnée le ".formate_date($tab_propositions_deja_enregistrees['date_reponse'][$indice_prop], "y").".\" />";
														$td_bg=" style='background-color:grey;'";
													}

													if($tab_propositions_deja_enregistrees['validation_remplacement'][$indice_prop]=='oui') {
														$reponse="<img src=\"../images/enabled.png\" alt='Confirmé' title=\"Le remplacement est confirmé.\" />";
														$td_bg=" style='background-color:aquamarine;'";
													}
												}

												echo "
								<tr class='fieldset_opacite50'>
									<td>
										<input type='checkbox' name='proposition[]' id='proposition_$cpt' value='".$chaine."' onchange=\"checkbox_change('proposition_$cpt');changement()\"$checked />
									</td>
									<td".$td_bg.">
										<label for='proposition_$cpt' id='texte_proposition_$cpt'>".$denomination_prof_courant."</label>
									</td>
									<td".$td_bg.">
										$reponse
									</td>
									<td>
										".affiche_lien_edt_prof($lig_prof->login, $denomination_prof_courant)."
									</td>
									<td>
										".$lien_mailto_courant."
									</td>
								</tr>";

												$cpt++;
											}
											/*
											else {
												echo "<tr><td></td><td><span style='color:red'>".$lig_prof->login."</span></td></tr>";
											}
											*/
											//$tab_profs_deja_proposes[]=$lig_prof->login;

										}

										//}
									}
									echo "
							</table>";
								}
								else {
									echo "<span style='color:red'>Aucun prof trouvé.</span>";
								}
							}
						}
						elseif(($AbsProfGroupesClasseSeulement=="yes")&&(count($aid[$id_aid_courant]['classes']['list'])>1)) {
							echo "Ce n'est pas un groupe classe et<br /><span title=\"Cela peut être modifié dans\nGestion des modules/Remplacements.\">vous ne souhaitez pas proposer au remplacement</span><br />les groupes/enseignements<br />qui ne sont pas des groupes classe.";
						}
					}
					else {
						echo "<span style='color:red'>Groupe non identifié.</span>";
					}
					echo "</td>
				<!--
				<td style='color:red'>Professeurs à extraire...</td>
				-->";
				}
				echo "
			</tr>";
			}
			else {
					echo "
				<td></td>
				<td></td>
				<td></td>
				<!--
				<td></td>
				-->
			</tr>";
			}
		}
	}
	echo "
	</tbody>
</table>";

	$timestamp_courant+=3600*24;

	$cpt_secu=0;
	while((!in_array($tab_jour_u[strftime("%u", $timestamp_courant)], $tab_jour))&&($cpt_secu<8)) {
		$timestamp_courant+=3600*24;
		$cpt_secu++;
	}

	$jour_aaaammjj_ts_courant=strftime("%Y%m%d", $timestamp_courant);
	while((in_array($jour_aaaammjj_ts_courant, $tab_jours_vacances))&&($timestamp_courant<=$timestamp_fin)) {
		$timestamp_courant+=3600*24;
		$jour_aaaammjj_ts_courant=strftime("%Y%m%d", $timestamp_courant);
	}
}

$chaine_js_var="";
$chaine_prec="";
foreach($tab_num_proposition as $jour_aaaammjj => $tab_cr) {
	//echo "<p>\$jour_aaaammjj=$jour_aaaammjj<br />";
	foreach($tab_cr as $id_creneau => $compteur) {
		//echo "\$id_creneau=$id_creneau - $compteur<br />";
		$chaine_js_var.="var cpt_debut_".$jour_aaaammjj."_".$id_creneau."=$compteur;\n";
		if(($chaine_prec!="")&&($jour_aaaammjj."_".$id_creneau!=$chaine_prec)) {
			$chaine_js_var.="var cpt_fin_".$chaine_prec."=".($compteur-1).";\n";
			//$chaine_prec=$jour_aaaammjj."_".$id_creneau;
		}
		$chaine_prec=$jour_aaaammjj."_".$id_creneau;
	}
}
$chaine_js_var.="var cpt_fin_".$chaine_prec."=".$cpt.";";
/*
echo "<pre>";
echo $chaine_js_var;
echo "</pre>";

echo "<pre>";
print_r($tab_num_proposition);
echo "</pre>";
*/
echo "
		<input type='hidden' name='is_posted' value='y' />
		<p><input type='submit' value='Valider les propositions' /></p>
		<div id='fixe' style='width:12em;'>
			<p style='text-indent:-2em;margin-left:2em;'><strong>Jours&nbsp;:</strong><br />";
for($loop=0;$loop<count($tab_jours_remplacements);$loop++) {
	$current_jour=substr($tab_jours_remplacements[$loop],6,2)."/".substr($tab_jours_remplacements[$loop],4,2)."/".substr($tab_jours_remplacements[$loop],0,4);
	echo "
			<a href='#jour_".$tab_jours_remplacements[$loop]."'>".$current_jour."</a><br />";
}
echo "
			</p>
			<hr />
			<input type='checkbox' name='envoi_mail' id='envoi_mail' value='y' onchange=\"checkbox_change('envoi_mail')\" /><label for='envoi_mail' id='texte_envoi_mail' title=\"Envoyer un mail pour les nouvelles propositions faites.\">Envoyer un mail</label><br />
			<input type='submit' value='Valider les propositions' />
		</div>
	</fieldset>
</form>

<p style='text-indent:-4em;margin-left:4em; margin-top:1em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li>Lorsqu'une proposition est formulée, elle apparait en page d'accueil du professeur jusqu'à ce que le professeur y ait répondu.<br />
	Lorsqu'un remplacement est validé, le professeur a également un rappel en page d'accueil (<em>jusqu'à ce que la date du remplacement soit passée</em>)</li>
	<li><em style='color:red;'>A FAIRE&nbsp;:</em> Pouvoir envoyer la proposition à tous les professeurs de la classe (cas du professeur privé d'un cours parce qu'une sortie est organisée pour la classe).</li>
	<li><em style='color:red;'>A FAIRE&nbsp;:</em> Proposer de relancer les propositions par mail pour le cas où les propositions sont saisies sans mail dans un premier temps.</li>
	<li><em style='color:red;'>A FAIRE&nbsp;:</em> Réduire/masquer les jours passés.</li>
	<!--li><em style='color:red;'>A FAIRE&nbsp;:</em> Pouvoir ne pas proposer de remplacement pour des cours qui ne sont pas en classe entière.</li-->
</ul>

<script type='text/javascript'>
	var change='no';

	function tout_cocher_sans_cours() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('proposition_'+i)) {
				document.getElementById('proposition_'+i).checked=true;
				checkbox_change('proposition_'+i);
			}
		}
	}

	function tout_decocher_sans_cours() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('proposition_'+i)) {
				document.getElementById('proposition_'+i).checked=false;
				checkbox_change('proposition_'+i);
			}
		}
	}

	$chaine_js_var

	function cocher_sans_cours(jour, creneau) {
		cpt_deb=eval('cpt_debut_'+jour+'_'+creneau);
		cpt_fin=eval('cpt_fin_'+jour+'_'+creneau);
		for(i=cpt_deb;i<=cpt_fin;i++) {
			if(document.getElementById('proposition_'+i)) {
				document.getElementById('proposition_'+i).checked=true;
				checkbox_change('proposition_'+i);
			}
		}
	}

	function decocher_sans_cours(jour, creneau) {
		cpt_deb=eval('cpt_debut_'+jour+'_'+creneau);
		cpt_fin=eval('cpt_fin_'+jour+'_'+creneau);
		for(i=cpt_deb;i<=cpt_fin;i++) {
			if(document.getElementById('proposition_'+i)) {
				document.getElementById('proposition_'+i).checked=false;
				checkbox_change('proposition_'+i);
			}
		}
	}

	$chaine_js_var_user
	$chaine_js_var_creneau
	$chaine_js_var_classe
	function afficher_div_validation(chaine, ancre) {
		//alert('plip');
		document.getElementById('valider_proposition').value=chaine;

		document.getElementById('valider_remplacement_ancre').value=ancre;

		tab=chaine.split('|');

		jour=tab[2].substr(6,2)+'/'+tab[2].substr(4,2)+'/'+tab[2].substr(0,4);

		document.getElementById('valider_remplacement_classe').innerHTML=nom_classe[tab[1]];
		document.getElementById('valider_remplacement_jour').innerHTML=jour;
		document.getElementById('valider_remplacement_creneau').innerHTML=nom_creneau[tab[3]];
		document.getElementById('valider_remplacement_nom_user').innerHTML=nom_user[tab[4]];

		afficher_div('div_valider_remplacement','y',10,-40);
	}

	function submit_div_validation_remplacement() {
		ancre=document.getElementById('valider_remplacement_ancre').value;
		document.form0.action='proposer_remplacement.php#'+ancre;
	}

	".js_checkbox_change_style('checkbox_change', 'texte_', 'n')."

	for(i=0;i<$cpt;i++) {
		checkbox_change('proposition_'+i);
	}
</script>";

require("../lib/footer.inc.php");
?>
