<?php
/*
 * $Id$
 *
 * Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_abs_prof/attribuer_remplacement.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/mod_abs_prof/attribuer_remplacement.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Attribuer les remplacements de professeurs',
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

if(($_SESSION['statut']=='scolarite')&&(!getSettingAOui('AbsProfAttribuerRemplacementScol'))) {
	header("Location: ../accueil.php?msg=Vous n êtes pas autorisé à attribuer les remplacements");
	die();
}

if(($_SESSION['statut']=='cpe')&&(!getSettingAOui('AbsProfAttribuerRemplacementCpe'))) {
	header("Location: ../accueil.php?msg=Vous n êtes pas autorisé à attribuer les remplacements");
	die();
}

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : "");

if((isset($_POST['is_posted']))) {
	check_token();

	$msg="";

	$validation=isset($_POST['validation']) ? $_POST['validation'] : array();
	$commentaire_validation=isset($_POST['commentaire_validation']) ? $_POST['commentaire_validation'] : array();
	$salle=isset($_POST['salle']) ? $_POST['salle'] : array();

	$nb_validations=0;
	foreach($validation as $key => $value) {
		if($validation[$key]!="") {
			// On s'assure qu'il n'y a pas d'autre validation déjà enregistrée
			$sql="SELECT * FROM abs_prof_remplacement WHERE id='".$validation[$key]."';";
			//$sql="SELECT *, TIMEDIFF(date_fin_r,date_debut_r) AS duree FROM abs_prof_remplacement WHERE id='".$validation[$key]."';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				$msg.="La proposition n°".$validation[$key]." n'existe pas.<br />";
			}
			else {
				$lig=mysqli_fetch_object($res);

				$id_absence=$lig->id_absence;
				$id_groupe=$lig->id_groupe;
				$id_aid=$lig->id_aid;
				$id_classe=$lig->id_classe;
				$id_creneau=$lig->id_creneau;
				$jour=$lig->jour;
				$login_user=$lig->login_user;

				$sql="SELECT * FROM abs_prof_remplacement WHERE id_absence='".$id_absence."' AND 
												id_aid='".$id_aid."' AND 
												id_groupe='".$id_groupe."' AND 
												id_classe='".$id_classe."' AND 
												jour='".$jour."' AND 
												id_creneau='".$id_creneau."' AND 
												validation_remplacement='oui';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$lig=mysqli_fetch_object($test);
					$msg.="La proposition n°".$validation[$key]." est déjà validée pour ".civ_nom_prenom($lig->login_user)." <em>(pas possible simultanément pour ".civ_nom_prenom($login_user).")</em>.<br />";
					// Ca ne devrait pas arriver sauf si deux admin/scol/cpe valident en même temps des remplacements.
					// A FAIRE: Ailleurs pouvoir forcer/modifier
				}
				else {
					// On valide créneau par créneau, donc en principe 2 tranches de 30min.
					$duree=2;

					$sql="UPDATE abs_prof_remplacement SET validation_remplacement='oui',
												commentaire_validation='".$commentaire_validation[$key]."',
												salle='".$salle[$key]."', 
												duree='".$duree."'
											WHERE id='".$validation[$key]."';";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$update) {
						$msg.="Erreur lors de la validation du remplacement pour la proposition n°".$validation[$key]."<br />";
						$msg.="$sql<br />";
					}
					else {
						/*
						// Durée:
						$tmp_tab_duree=explode(":", $lig->duree);
						$duree=

						// Salle

						// id_j_semaine() : 	ISO-8601 numeric representation of the day of the week 	1 (for Monday) through 7 (for Sunday)
						$tmp_tab_jour=array("lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche");

						$tmp_date=mktime(12, 0, 0, substr($jour, 4,2), substr($jour, 6,2), substr($jour, 0,4));
						$nom_jour=$tmp_tab_jour[strftime($tmp_date, "%u")];

						$sql="DELETE FROM edt_cours_remplacements WHERE id_absence='".$id_absence."' AND 
														id_groupe='".$id_groupe."' AND 
														id_aid='".$id_aid."' AND 
														jour_semaine='".$nom_jour."' AND 
														id_definie_periode='".$id_creneau."' AND 
														jour='".$jour."';";
						$del=mysqli_query($GLOBALS["mysqli"], $sql);

						$sql="INSERT INTO edt_cours_remplacements SET id_absence='".$id_absence."', 
														id_groupe='".$id_groupe."', 
														id_aid='".$id_aid."', 
														jour_semaine='".$nom_jour."', 
														id_definie_periode='".$id_creneau."', 
														duree='".$duree."', 
														jour='".$jour."', 
														id_salle='".$lig->salle."', 
														heuredeb_dec='', 
														id_semaine='', 
														id_calendrier='', 
														modif_edt='', 
														login_prof='".$login_user."';";
						echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						*/


						// A FAIRE : Envoyer un mail à l'heureux destinataire, mettre un message en page d'accueil, et signaler aux autres candidats que le remplacement est attribué
						$nb_validations++;

						// On n'envoie pas les mails pour des remplacements passés
						if($mode=="") {
							$chaine_commentaire_validation="";
							$chaine_salle="";
							if($commentaire_validation[$key]!="") {
								//$chaine_commentaire_validation=$commentaire_validation[$key]."\n";
								$chaine_commentaire_validation=preg_replace('/(\\\n)+/',"\n", $commentaire_validation[$key])."\n";
							}
							if($salle[$key]!="") {
								$chaine_salle="Salle ".$salle[$key]."\n";
							}

							$envoi_mail_actif=getSettingValue('envoi_mail_actif');
							if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
								$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
							}

							$mail_dest="";
							$references_mail="";
							$sql="SELECT u.email, apr.id FROM abs_prof_remplacement apr, 
													utilisateurs u 
												WHERE apr.id_absence='".$id_absence."' AND 
															apr.id_groupe='".$id_groupe."' AND 
															apr.id_aid='".$id_aid."' AND 
															apr.id_classe='".$id_classe."' AND 
															apr.jour='".$jour."' AND 
															apr.id_creneau='".$id_creneau."' AND 
															apr.reponse!='non' AND 
															u.login=apr.login_user;";
							//echo "$sql<br />";
							$res_mail=mysqli_query($GLOBALS["mysqli"], $sql);
							while($lig_mail=mysqli_fetch_object($res_mail)) {
								if(check_mail($lig_mail->email)) {
									if($mail_dest!="") {
										$mail_dest.=",";
										$references_mail.="\r\n";
									}
									//$mail_dest.=$lig_mail->email;
									if((!preg_match("/^$lig_mail->email,/", $mail_dest))&&
									(!preg_match("/,$lig_mail->email,/", $mail_dest))&&
									(!preg_match("/,$lig_mail->email$/", $mail_dest))) {
										$mail_dest.=$lig_mail->email;
										$tab_param_mail['destinataire'][]=$lig_mail->email;
									}
									$references_mail.="proposition_remplacement_".$lig_mail->id."_".$jour;
								}
							}

							if(($envoi_mail_actif=='y')&&($mail_dest!="")) {
								$tab_info_creneau=get_infos_creneau($id_creneau);
								$info_creneau=$tab_info_creneau['nom_creneau']." (".$tab_info_creneau['debut_court']."-".$tab_info_creneau['fin_court'].")";

								$date_debut_r=substr($jour, 0, 4)."-".substr($jour, 4, 2)."-".substr($jour, 6, 2)." 08:00:00";

								if(($id_groupe!="")&&($id_groupe!="0")) {
									$chaine_info_grp=get_info_grp($id_groupe,array('description', 'matieres', 'classes', 'profs'), "");
								}
								else {
									$chaine_info_grp=get_info_aid($id_absence,array('nom_general_complet', 'classes', 'profs'), "");
								}

								$designation_user=civ_nom_prenom($login_user);
								$subject = "[GEPI]: Remplacement attribué à ".$designation_user;
								$texte_mail="Bonjour ".$designation_user.",

Le remplacement suivant vous est attribué:

".get_nom_classe($id_classe)." le ".formate_date($date_debut_r,"n","complet")." en ".$info_creneau."
".$chaine_commentaire_validation.$chaine_salle."en remplacement de ".$chaine_info_grp.".

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
				}
			}
		}
	}

	if($nb_validations>0) {
		$msg.="$nb_validations remplacement(s) validé(s).<br />";
		$msg.="N'oubliez pas d'<a href='$gepiPath/mod_abs_prof/afficher_remplacements.php?mode=familles_non_informees'>informer les familles</a>.<br />";
	}

}

if((isset($_GET['annuler_remplacement']))) {
	check_token();

	$msg="";

	$annuler_remplacement=$_GET['annuler_remplacement'];

	$sql="UPDATE abs_prof_remplacement SET validation_remplacement='' WHERE id='".$annuler_remplacement."';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(!$res) {
		$msg="Erreur lors de l'annulation du remplacement n°$annuler_remplacement<br />";
	}
	else {
		$msg="Remplacement n°$annuler_remplacement annulé.<br />";

		$envoi_mail_actif=getSettingValue('envoi_mail_actif');
		if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
			$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
		}

		$sql="SELECT * FROM abs_prof_remplacement WHERE id='".$annuler_remplacement."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$lig=mysqli_fetch_object($res);
		$id_absence=$lig->id_absence;
		$id_groupe=$lig->id_groupe;
		$id_aid=$lig->id_aid;
		$id_classe=$lig->id_classe;
		$id_creneau=$lig->id_creneau;
		$jour=$lig->jour;
		$login_user=$lig->login_user;
		$salle=$lig->salle;

		$mail_dest="";
		$references_mail="";
		$sql="SELECT u.email, apr.id FROM abs_prof_remplacement apr, 
								utilisateurs u 
							WHERE apr.id='".$annuler_remplacement."' AND 
								apr.reponse!='non' AND 
								u.login=apr.login_user;";
		//echo "$sql<br />";
		$res_mail=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig_mail=mysqli_fetch_object($res_mail)) {
			if(check_mail($lig_mail->email)) {
				if($mail_dest!="") {
					$mail_dest.=",";
					$references_mail.="\r\n";
				}
				//$mail_dest.=$lig_mail->email;
				if((!preg_match("/^$lig_mail->email,/", $mail_dest))&&(!preg_match("/,$lig_mail->email,/", $mail_dest))&&(!preg_match("/,$lig_mail->email$/", $mail_dest))) {
					$mail_dest.=$lig_mail->email;
					$tab_param_mail['destinataire'][]=$lig_mail->email;
				}
				$references_mail.="proposition_remplacement_".$lig_mail->id."_".$jour;
			}
		}

		$chaine_commentaire_validation="";
		$chaine_salle="";
		if($salle!="") {
			$chaine_salle="Salle ".$salle."\n";
		}

		if(($envoi_mail_actif=='y')&&($mail_dest!="")) {
			$tab_info_creneau=get_infos_creneau($id_creneau);
			$info_creneau=$tab_info_creneau['nom_creneau']." (".$tab_info_creneau['debut_court']."-".$tab_info_creneau['fin_court'].")";

			$date_debut_r=substr($jour, 0, 4)."-".substr($jour, 4, 2)."-".substr($jour, 6, 2)." 08:00:00";

			if(($id_groupe!="")&&($id_groupe!="0")) {
				$chaine_info_grp=get_info_grp($id_groupe,array('description', 'matieres', 'classes', 'profs'), "");
			}
			else {
				$chaine_info_grp=get_info_aid($id_absence,array('nom_general_complet', 'classes', 'profs'), "");
			}

			$designation_user=civ_nom_prenom($login_user);
			$subject = "[GEPI]: Remplacement annulé";
			$texte_mail="Bonjour ".$designation_user.",

Le *remplacement* que vous deviez effectuer est *annulé*:

".get_nom_classe($id_classe)." le ".formate_date($date_debut_r,"n","complet")." en ".$info_creneau."
".$chaine_commentaire_validation.$chaine_salle."en remplacement de ".$chaine_info_grp.".

Si d'autres professeurs sont intéressés, le remplacement du cours reste bienvenu.

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

//$javascript_specifique[] = "lib/tablekit";
//$utilisation_tablekit="ok";

$avec_js_et_css_edt="y";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
// onclick=\"return confirm_abandon (this, change, '$themessage')\"
$message_suppression = "Confirmation de suppression";
//**************** EN-TETE *****************
$titre_page = "Attribuer remplacements";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

//===================================================================
// Récupérer la liste des créneaux
$tab_creneau=get_heures_debut_fin_creneaux();
//===================================================================

if($mode=="") {
	$lien_alt="<a href='attribuer_remplacement.php?mode=anciens'>Remplacements passés non validés</a>";
}
else {
	$lien_alt="<a href='attribuer_remplacement.php'>Remplacements à venir à valider</a>";
}

echo "<a name=\"debut_de_page\"></a>
<p class='bold'>
	<a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | $lien_alt
</p>";

//============================================================================================================
if((getSettingAOui('autorise_edt_tous'))||
	((getSettingAOui('autorise_edt_admin'))&&($_SESSION['statut']=='administrateur'))) {
	// Lien vers l'EDT des salles
	echo "
<div style='float:right; width:5em; text-align:center;' class='fieldset_opacite50' title=\"Voir l'emploi du temps des salles dans une nouvelle page.\"><a href='../edt_organisation/index_edt.php?visioedt=salle1' target='_blank'>EDT des salles</a></div>";

	// Dispositif pour l'affichage EDT en infobulle

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

echo "
<h2>Attribuer les remplacements de professeurs</h2>";

$tab_propositions_avec_reponse_positive=array();
if($mode=="") {

	$sql="SELECT * FROM abs_prof_remplacement WHERE date_debut_r>='".strftime('%Y-%m-%d %H:%M:%S')."' AND reponse='oui' ORDER BY date_debut_r, id_absence, id_classe, date_reponse;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			//if(check_proposition_remplacement_validee2($lig->id)=="") {
			if(check_proposition_remplacement_validee($lig->id_absence, $lig->id_groupe, $lig->id_aid, $lig->id_classe, $lig->jour, $lig->id_creneau)=="") {
				// Créneau sans remplacement programmé
				$tab_propositions_avec_reponse_positive[$cpt]['id']=$lig->id;
				$tab_propositions_avec_reponse_positive[$cpt]['id_absence']=$lig->id_absence;
				$tab_propositions_avec_reponse_positive[$cpt]['id_groupe']=$lig->id_groupe;
				$tab_propositions_avec_reponse_positive[$cpt]['id_aid']=$lig->id_aid;
				$tab_propositions_avec_reponse_positive[$cpt]['id_classe']=$lig->id_classe;
				$tab_propositions_avec_reponse_positive[$cpt]['jour']=$lig->jour;
				$tab_propositions_avec_reponse_positive[$cpt]['id_creneau']=$lig->id_creneau;
				$tab_propositions_avec_reponse_positive[$cpt]['date_debut_r']=$lig->date_debut_r;
				$tab_propositions_avec_reponse_positive[$cpt]['date_fin_r']=$lig->date_fin_r;
				$tab_propositions_avec_reponse_positive[$cpt]['date_reponse']=$lig->date_reponse;
				$tab_propositions_avec_reponse_positive[$cpt]['login_user']=$lig->login_user;
				$tab_propositions_avec_reponse_positive[$cpt]['commentaire_prof']=$lig->commentaire_prof;
				// Normalement ce qui suit est vide
				$tab_propositions_avec_reponse_positive[$cpt]['validation_remplacement']=$lig->validation_remplacement;
				$tab_propositions_avec_reponse_positive[$cpt]['commentaire_validation']=$lig->commentaire_validation;
				$tab_propositions_avec_reponse_positive[$cpt]['salle']=$lig->salle;
				$cpt++;
			}
		}
	}
	if(count($tab_propositions_avec_reponse_positive)==0) {
		echo "<p>Aucune proposition de remplacement n'a reçu d'accueil favorable pour le moment.</p>";
		//require("../lib/footer.inc.php");
		//die();
	}

	$tab_r=$tab_propositions_avec_reponse_positive;

}
else {
	// On affiche les anciens remplacements pour attribuer après coup.

	echo "<p>Si des remplacements de cours (<em>dans le passé</em>) ont eu lieu sans pour autant être validés dans Gepi, vous pouvez les attribuer/valider maintenant à des fins de statistiques/totaux dans le cas où des rémunérations de remplacements sont prévues.</p>";

	$sql="SELECT * FROM abs_prof_remplacement WHERE date_debut_r<'".strftime('%Y-%m-%d %H:%M:%S')."';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$tab_remplacements=array();
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			//if(check_proposition_remplacement_validee2($lig->id)=="") {
			if(check_proposition_remplacement_validee($lig->id_absence, $lig->id_groupe, $lig->id_aid, $lig->id_classe, $lig->jour, $lig->id_creneau)=="") {
				// Créneau sans remplacement programmé
				//$tab_remplacements[]=$lig->id;
				$tab_remplacements[$cpt]['id']=$lig->id;
				$tab_remplacements[$cpt]['id_absence']=$lig->id_absence;
				$tab_remplacements[$cpt]['id_groupe']=$lig->id_groupe;
				$tab_remplacements[$cpt]['id_aid']=$lig->id_aid;
				$tab_remplacements[$cpt]['id_classe']=$lig->id_classe;
				$tab_remplacements[$cpt]['jour']=$lig->jour;
				$tab_remplacements[$cpt]['id_creneau']=$lig->id_creneau;
				$tab_remplacements[$cpt]['date_debut_r']=$lig->date_debut_r;
				$tab_remplacements[$cpt]['date_fin_r']=$lig->date_fin_r;
				$tab_remplacements[$cpt]['date_reponse']=$lig->date_reponse;
				$tab_remplacements[$cpt]['login_user']=$lig->login_user;
				$tab_remplacements[$cpt]['commentaire_prof']=$lig->commentaire_prof;
				// Normalement ce qui suit est vide
				$tab_remplacements[$cpt]['validation_remplacement']=$lig->validation_remplacement;
				$tab_remplacements[$cpt]['commentaire_validation']=$lig->commentaire_validation;
				$tab_remplacements[$cpt]['salle']=$lig->salle;
				$cpt++;
			}
		}
	}

	if(count($tab_remplacements)==0) {
		echo "<p>Aucune proposition passée sans attribution du remplacement.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	/*
	echo "<p style='color:red'>A FAIRE : Faire la liste des propositions passées...</p>";
	echo "<pre>";
	print_r($tab_remplacements);
	echo "</pre>";
	*/

	$tab_r=$tab_remplacements;

}
/*
echo "<pre>";
print_r($tab_r);
echo "</pre>";
*/
if(count($tab_propositions_avec_reponse_positive)>0) {
echo "
<form action=\"".$_SERVER['PHP_SELF']."#debut_de_page\" method=\"post\" style=\"width: 100%;\" name=\"formulaire_saisie_login_user\">
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='is_posted' value='y' />
		<input type='hidden' name='mode' value='$mode' />";

if($mode=="") {
	echo "
		<p>La ou les propositions de remplacement suivantes ont reçu un accueil favorable du ou des professeurs indiqués.<br />Veuillez choisir à qui attribuer le remplacement.</p>";
}
else {
	echo "
		<p>Voici la ou les propositions de remplacement pour lequelles le remplacement n'a pas été validé.</p>";
}

$id_cours_creneau_precedent="";
$cpt=0;
for($loop=0;$loop<count($tab_r);$loop++) {
	$id_cours_creneau=$tab_r[$loop]['id_absence']."|".$tab_r[$loop]['jour']."|".$tab_r[$loop]['id_creneau']."|".$tab_r[$loop]['id_classe'];
	if($id_cours_creneau!=$id_cours_creneau_precedent) {
		if($id_cours_creneau_precedent!="") {
			echo "
			<table>
				<tr style='vertical-align:top;'>
					<td>
						Salle&nbsp;: 
					</td>
					<td>
						<input type='text' name='salle[$cpt]' value=\"".$tab_r[$loop]['salle']."\" onchange='changement()' />
					</td>
				</tr>
				<tr style='vertical-align:top;'>
					<td>
						Commentaire&nbsp;: 
					</td>
					<td>
						<textarea name='commentaire_validation[$cpt]' style='vertical-align:top;' onchange='changement()'>".$tab_r[$loop]['commentaire_validation']."</textarea>
					</td>
				</tr>
			</table>
		</div>";
		}

		$cpt++;
		echo "
		<div style='margin-bottom:0.5em; padding:0.2em;' class='fieldset_opacite50'>";
		// Debug:
		//echo "<span style='color:green'>".$id_cours_creneau."</span><br />";

		echo get_nom_classe($tab_r[$loop]['id_classe'])."&nbsp;: ".formate_date($tab_r[$loop]['date_debut_r'], "n", "complet")." de ".$tab_creneau[$tab_r[$loop]['id_creneau']]['debut_court']." à ".$tab_creneau[$tab_r[$loop]['id_creneau']]['fin_court']." (<em>".$tab_creneau[$tab_r[$loop]['id_creneau']]['nom_creneau']."</em>)";
		if(($tab_r[$loop]['id_groupe']!="")&&($tab_r[$loop]['id_groupe']!="0")) {
			echo " (<em style='font-size:x-small;'>remplacement de ".get_info_grp($tab_r[$loop]['id_groupe'])."</em>)";
		}
		else {
			echo " (<em style='font-size:x-small;'>remplacement de ".get_info_aid($tab_r[$loop]['id_aid'])."</em>)";
		}
		echo "<br />";


		echo "<input type='radio' name='validation[$cpt]' id='validation_".$cpt."_vide' value='' onchange='change_style_radio();changement();' checked /><label for='validation_".$cpt."_vide' id='texte_validation_".$cpt."_vide' style='font-weight:bold;'>Ne pas attribuer pour le moment</label><br />";
		$id_cours_creneau_precedent=$id_cours_creneau;
	}

	if(!isset($civ_nom_prenom[$tab_r[$loop]['login_user']])) {
			$civ_nom_prenom[$tab_r[$loop]['login_user']]=civ_nom_prenom($tab_r[$loop]['login_user']);
	}

	echo "<input type='radio' name='validation[$cpt]' id='validation_".$cpt."_".$tab_r[$loop]['id']."' value='".$tab_r[$loop]['id']."' onchange='change_style_radio();changement();' />
		<label for='validation_".$cpt."_".$tab_r[$loop]['id']."' id='texte_validation_".$cpt."_".$tab_r[$loop]['id']."'>".$civ_nom_prenom[$tab_r[$loop]['login_user']];

	if($tab_r[$loop]['date_reponse']!="0000-00-00 00:00:00") {
		echo "
		 (<em style='font-size:small;' title=\"Date de la réponse\">".formate_date($tab_r[$loop]['date_reponse'], "y")."</em>)";
	}
	if($tab_r[$loop]['commentaire_prof']!="") {
		echo " (<em style='font-size:small;' title=\"Commentaire saisi par le professeur.\">".$tab_r[$loop]['commentaire_prof']."</em>)";
	}

	echo " ".affiche_lien_edt_prof($tab_r[$loop]['login_user'], $civ_nom_prenom[$tab_r[$loop]['login_user']]);

	echo "
		</label><br />";
}
if($id_cours_creneau_precedent!="") {
	echo "
			<table>
				<tr style='vertical-align:top;'>
					<td>
						Salle&nbsp;: 
					</td>
					<td>
						<input type='text' name='salle[$cpt]' value=\"".$tab_r[$loop-1]['salle']."\" onchange='changement()' />
					</td>
				</tr>
				<tr style='vertical-align:top;'>
					<td>
						Commentaire&nbsp;: 
					</td>
					<td>
						<textarea name='commentaire_validation[$cpt]' style='vertical-align:top;' onchange='changement()'>".$tab_r[$loop-1]['commentaire_validation']."</textarea>
					</td>
				</tr>
			</table>
		</div>";
}

echo "

		<p><input type='submit' value='Valider' /></p>
		<div id='fixe'><input type='submit' value='Valider' title=\"Valider l'attribution des remplacements\" /></div>
	</fieldset>
</form>

<p style='color:red; text-indent:-4em;margin-left:4em;'><em>NOTES&nbsp;:</em> A FAIRE: Pouvoir afficher un EDT de salle en infobulle... ou rechercher les salles vides.</p>

<script type='text/javascript'>
	".js_checkbox_change_style('checkbox_change', 'texte_', 'n', 0.5)."

	function change_style_radio() {
		item=document.getElementsByTagName('input');
		for(i=0;i<item.length;i++) {
			if(item[i].getAttribute('type')=='radio') {
				checkbox_change(item[i].getAttribute('id'));
			}
		}
	}

	change_style_radio();
</script>";
}

if($mode=="") {
	$tab_remplacements_a_venir_valides=array();

	echo "
<h2>Remplacements à venir validés</h2>";
	$sql="SELECT * FROM abs_prof_remplacement WHERE date_debut_r>='".strftime('%Y-%m-%d %H:%M:%S')."' AND validation_remplacement='oui' ORDER BY date_debut_r, id_absence, id_classe, date_reponse;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			$tab_remplacements_a_venir_valides[$cpt]['id']=$lig->id;
			$tab_remplacements_a_venir_valides[$cpt]['id_absence']=$lig->id_absence;
			$tab_remplacements_a_venir_valides[$cpt]['id_groupe']=$lig->id_groupe;
			$tab_remplacements_a_venir_valides[$cpt]['id_aid']=$lig->id_aid;
			$tab_remplacements_a_venir_valides[$cpt]['id_classe']=$lig->id_classe;
			$tab_remplacements_a_venir_valides[$cpt]['jour']=$lig->jour;
			$tab_remplacements_a_venir_valides[$cpt]['id_creneau']=$lig->id_creneau;
			$tab_remplacements_a_venir_valides[$cpt]['date_debut_r']=$lig->date_debut_r;
			$tab_remplacements_a_venir_valides[$cpt]['date_fin_r']=$lig->date_fin_r;
			$tab_remplacements_a_venir_valides[$cpt]['date_reponse']=$lig->date_reponse;
			$tab_remplacements_a_venir_valides[$cpt]['login_user']=$lig->login_user;
			$tab_remplacements_a_venir_valides[$cpt]['commentaire_prof']=$lig->commentaire_prof;
			$tab_remplacements_a_venir_valides[$cpt]['validation_remplacement']=$lig->validation_remplacement;
			$tab_remplacements_a_venir_valides[$cpt]['commentaire_validation']=$lig->commentaire_validation;
			$tab_remplacements_a_venir_valides[$cpt]['salle']=$lig->salle;
			$cpt++;
		}
	}

	if(count($tab_remplacements_a_venir_valides)==0) {
		echo "<p>Aucun remplacement à venir n'est validé.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$tab_r=$tab_remplacements_a_venir_valides;

	echo "
<p>Le ou les remplacements à venir suivants sont validés.<br />Vous pouvez en cas de contre-ordre les annuler.</p>
<ul>";
	for($loop=0;$loop<count($tab_r);$loop++) {
		echo "<li style='margin-bottom:0.5em;'>".get_nom_classe($tab_r[$loop]['id_classe'])."&nbsp;: ".formate_date($tab_r[$loop]['date_debut_r'], "n", "complet")." de ".$tab_creneau[$tab_r[$loop]['id_creneau']]['debut_court']." à ".$tab_creneau[$tab_r[$loop]['id_creneau']]['fin_court']." (<em>".$tab_creneau[$tab_r[$loop]['id_creneau']]['nom_creneau']."</em>)";
		if(($tab_r[$loop]['id_groupe']!="")&&($tab_r[$loop]['id_groupe']!="0")) {
			echo " (<em style='font-size:x-small;'>remplacement de ".get_info_grp($tab_r[$loop]['id_groupe'])."</em>)";
		}
		else {
			echo " (<em style='font-size:x-small;'>remplacement de ".get_info_aid($tab_r[$loop]['id_aid'])."</em>)";
		}
		echo "<br />";

		if(!isset($civ_nom_prenom[$tab_r[$loop]['login_user']])) {
			$civ_nom_prenom[$tab_r[$loop]['login_user']]=civ_nom_prenom($tab_r[$loop]['login_user']);
		}

		echo $civ_nom_prenom[$tab_r[$loop]['login_user']]." - <a href='".$_SERVER['PHP_SELF']."?annuler_remplacement=".$tab_r[$loop]['id'].add_token_in_url()."' onclick=\"return confirm_abandon (this, change, '".$themessage."')\">Annuler le remplacement</a></li>";
	}
	echo "</ul>";
}

require("../lib/footer.inc.php");
?>
