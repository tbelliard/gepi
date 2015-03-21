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

$action = isset($_POST["action"]) ? $_POST["action"] :(isset($_GET["action"]) ? $_GET["action"] :NULL);

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Dans le cas ou on poste un message, pas de traitement anti_inject
// Pour ne pas interférer avec fckeditor
if ((isset($action)) and ($action == 'evenement') and (isset($_POST['texte_avant']) || isset($_POST['texte_apres'])) and isset($_POST['ok'])) {$traite_anti_inject = 'no';}

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

include("../ckeditor/ckeditor.php") ;

$sql="SELECT 1=1 FROM droits WHERE id='/classes/info_dates_classes.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/classes/info_dates_classes.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Informer des dates d événements pour les classes',
statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

/*
// Configuration du calendrier
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
*/

// initialisation des notifications
$msg_erreur="";
$msg_OK="";

/*
// initialisation des variables
$order_by = isset($_POST["order_by"]) ? $_POST["order_by"] :(isset($_GET["order_by"]) ? $_GET["order_by"] :"date_debut");
if ($order_by != "date_debut" and $order_by != "date_fin" and $order_by != "id") {
	$order_by = "date_debut";
}
*/

$id_ev = isset($_POST["id_ev"]) ? $_POST["id_ev"] :(isset($_GET["id_ev"]) ? $_GET["id_ev"] :NULL);

if(!isset($id_ev)) {
	header("Location: ../accueil.php?msg=Evénement non choisi");
	die();
}

$envoi_mail=isset($_POST['envoi_mail']) ? $_POST['envoi_mail'] : NULL;
$mail_prof=isset($_POST['mail_prof']) ? $_POST['mail_prof'] : array();
$mail_cpe=isset($_POST['mail_cpe']) ? $_POST['mail_cpe'] : array();
$mail_scol=isset($_POST['mail_scol']) ? $_POST['mail_scol'] : array();
$mail_resp=isset($_POST['mail_resp']) ? $_POST['mail_resp'] : array();
$mail_eleve=isset($_POST['mail_eleve']) ? $_POST['mail_eleve'] : array();

$msg="";

//
// Insertion ou modification d'un événement
//
if ((isset($envoi_mail))&&
((count($mail_prof>0))||
(count($mail_prof>0))||
(count($mail_prof>0))||
(count($mail_prof>0))||
(count($mail_prof>0)))) {
	check_token();

	// Récupérer les infos de l'événément
	$sql="SELECT * FROM d_dates_evenements WHERE id_ev='".$id_ev."';";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		$msg="ERREUR : L'événement $id_ev n'a pas été trouvé.<br />";
	}
	else {
		$tab_ev=mysqli_fetch_assoc($res);

		$envoi_mail_actif=getSettingValue('envoi_mail_actif');
		//$envoi_mail_actif="n";

		$message_mail2="Bonjour,

Compte-rendu de l'envoi de mail pour l'événement n°$id_ev\n\n";

		$sql="SELECT DISTINCT id_classe, classe, nom_complet, date_evenement FROM d_dates_evenements_classes d, classes c WHERE id_ev='".$id_ev."' AND d.id_classe=c.id ORDER BY classe;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$msg="ERREUR : Aucune classe n'est concernée par cet événement???<br />";
		}
		else {
			//$tab_classe=array();
			while($obj_classe=mysqli_fetch_object($res)) {
				if((isset($mail_prof[$obj_classe->id_classe]))||
				(isset($mail_cpe[$obj_classe->id_classe]))||
				(isset($mail_scol[$obj_classe->id_classe]))||
				(isset($mail_resp[$obj_classe->id_classe]))||
				(isset($mail_eleve[$obj_classe->id_classe]))) {

					$date_ev_classe=formate_date($obj_classe->date_evenement, "y", "court");

					$sujet_mail="[GEPI]: Date événement pour la classe de ".$obj_classe->classe." : ".$date_ev_classe;

					$message_mail="<p>Bonjour madame, monsieur,<br />
<br />
Nous souhaitons vous informer de la date de l'\"événement\" suivant pour la classe de ".$obj_classe->classe.":
<hr />
".$tab_ev['texte_avant']."
<p>Classe de ".$obj_classe->classe." : $date_ev_classe</p>
".$tab_ev['texte_apres']."
<hr />
Bien cordialement.<br />
-- <br />
".getSettingValue('gepiSchoolName');
					$destinataire=getSettingValue('gepiSchoolEmail');

					$destinataires_bcc="";

					$nb_dest_prof=0;
					$nb_dest_cpe=0;
					$nb_dest_scol=0;
					$nb_dest_eleve=0;
					$nb_dest_resp=0;
					if(isset($mail_prof[$obj_classe->id_classe])) {
						$sql="SELECT DISTINCT civilite, nom, prenom, email FROM utilisateurs u, j_groupes_professeurs jgp, j_groupes_classes jgc WHERE u.login=jgp.login AND jgp.id_groupe=jgc.id_groupe AND jgc.id_classe='".$obj_classe->id_classe."' AND u.email LIKE '%@%' ORDER BY u.nom, u.prenom;";
						//echo "$sql<br />";
						$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_u)==0) {
							$msg.="Aucun email professeur pour la classe de ".$obj_classe->classe."<br />";
						}
						else {
							while($lig_u=mysqli_fetch_object($res_u)) {
								if(check_mail($lig_u->email)) {
									if($destinataires_bcc!="") {
										$destinataires_bcc.=", ";
									}
									$destinataires_bcc.=$lig_u->civilite." ".$lig_u->prenom." ".$lig_u->nom." <".$lig_u->email.">";
									$nb_dest_prof++;
								}
							}
						}
					}

					if(isset($mail_cpe[$obj_classe->id_classe])) {
						$sql="SELECT DISTINCT civilite, nom, prenom, email FROM utilisateurs u, j_eleves_classes jec, j_eleves_cpe jecpe WHERE u.login=jecpe.cpe_login AND jec.login=jecpe.e_login AND jec.id_classe='".$obj_classe->id_classe."' AND u.email LIKE '%@%' ORDER BY u.nom, u.prenom;";
						//echo "$sql<br />";
						$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_u)==0) {
							$msg.="Aucun email CPE pour la classe de ".$obj_classe->classe."<br />";
						}
						else {
							while($lig_u=mysqli_fetch_object($res_u)) {
								if(check_mail($lig_u->email)) {
									if($destinataires_bcc!="") {
										$destinataires_bcc.=", ";
									}
									$destinataires_bcc.=$lig_u->civilite." ".$lig_u->prenom." ".$lig_u->nom." <".$lig_u->email.">";
									$nb_dest_cpe++;
								}
							}
						}
					}

					if(isset($mail_scol[$obj_classe->id_classe])) {
						$sql="SELECT DISTINCT civilite, nom, prenom, email FROM utilisateurs u, j_scol_classes jsc WHERE u.login=jsc.login AND jsc.id_classe='".$obj_classe->id_classe."' AND u.email LIKE '%@%' ORDER BY u.nom, u.prenom;";
						//echo "$sql<br />";
						$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_u)==0) {
							$msg.="Aucun email scolarité pour la classe de ".$obj_classe->classe."<br />";
						}
						else {
							while($lig_u=mysqli_fetch_object($res_u)) {
								if(check_mail($lig_u->email)) {
									if($destinataires_bcc!="") {
										$destinataires_bcc.=", ";
									}
									$destinataires_bcc.=$lig_u->civilite." ".$lig_u->prenom." ".$lig_u->nom." <".$lig_u->email.">";
									$nb_dest_scol++;
								}
							}
						}
					}

					if(isset($mail_eleve[$obj_classe->id_classe])) {
						$sql="(SELECT DISTINCT nom, prenom, email FROM utilisateurs u, j_eleves_classes jec WHERE u.login=jec.login AND jec.id_classe='".$obj_classe->id_classe."' AND u.email LIKE '%@%') UNION (SELECT DISTINCT nom, prenom, email FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='".$obj_classe->id_classe."' AND e.email LIKE '%@%');";
						//echo "$sql<br />";
						$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_u)==0) {
							$msg.="Aucun email élève pour la classe de ".$obj_classe->classe."<br />";
						}
						else {
							$tab_email_deja=array();
							while($lig_u=mysqli_fetch_object($res_u)) {
								if(!in_array($lig_u->email, $tab_email_deja)) {
									if(check_mail($lig_u->email)) {
										if($destinataires_bcc!="") {
											$destinataires_bcc.=", ";
										}
										$destinataires_bcc.=$lig_u->prenom." ".$lig_u->nom." <".$lig_u->email.">";
										$nb_dest_eleve++;
										$tab_email_deja[]=$lig_u->email;
									}
								}
							}
						}
					}

					if(isset($mail_resp[$obj_classe->id_classe])) {
						$sql="SELECT DISTINCT u.civilite, u.nom, u.prenom, u.email, rp.mel FROM utilisateurs u, 
								j_eleves_classes jec, 
								eleves e, 
								responsables2 r, 
								resp_pers rp 
							WHERE u.login=rp.login AND 
								rp.pers_id=r.pers_id AND 
								r.ele_id=e.ele_id AND 
								jec.login=e.login AND 
								jec.id_classe='".$obj_classe->id_classe."' AND 
								(u.email LIKE '%@%' OR rp.mel LIKE '%@%') AND 
								(r.resp_legal='1' OR r.resp_legal='2' OR acces_sp='y' OR envoi_bulletin='y');";
						//echo "$sql<br />";
						$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_u)==0) {
							$msg.="Aucun email responsable pour la classe de ".$obj_classe->classe."<br />";
						}
						else {
							$tab_email_deja=array();
							while($lig_u=mysqli_fetch_object($res_u)) {
								$responsable_courant_ok=0;

								if(!in_array($lig_u->email, $tab_email_deja)) {
									if(check_mail($lig_u->email)) {
										if($destinataires_bcc!="") {
											$destinataires_bcc.=", ";
										}
										$destinataires_bcc.=$lig_u->civilite." ".$lig_u->prenom." ".$lig_u->nom." <".$lig_u->email.">";
										$responsable_courant_ok++;
										$tab_email_deja[]=$lig_u->email;
									}
								}

								if(!in_array($lig_u->mel, $tab_email_deja)) {
									if(check_mail($lig_u->mel)) {
										if($destinataires_bcc!="") {
											$destinataires_bcc.=",";
										}
										$destinataires_bcc.=$lig_u->civilite." ".$lig_u->prenom." ".$lig_u->nom." <".$lig_u->mel.">";
										$responsable_courant_ok++;
										$tab_email_deja[]=$lig_u->mel;
									}
								}

								if($responsable_courant_ok>0) {
									$nb_dest_resp++;
								}
							}
						}
					}


					if($destinataires_bcc=="") {
						$msg.="Aucun destinataire (avec mail) pour la classe de ".$obj_classe->classe."<br />";
					}
					else {
						if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
							$destinataires_bcc.=",".$_SESSION['email'];
						}

						//echo "<div class='fieldset_opacite50' style='border:1px solid red; margin:1em;'>Destinataire: $destinataire<br />Destinataires BCC: ".htmlentities($destinataires_bcc)."<hr />".$sujet_mail."<hr />".$message_mail."</div>";
						// Envoyer le mail
						//echo "\$envoi_mail_actif=$envoi_mail_actif<br />";
						if($envoi_mail_actif!="n") {
							$ajout_headers="Bcc:".$destinataires_bcc;
							if(envoi_mail($sujet_mail, $message_mail, $destinataire, $ajout_headers, "html")) {
								$msg.=$obj_classe->classe." message envoyé pour ";
								if((isset($mail_prof[$obj_classe->id_classe]))&&($nb_dest_prof>0)) {
									$msg.=$nb_dest_prof." professeur(s), ";
								}
								if((isset($mail_cpe[$obj_classe->id_classe]))&&($nb_dest_cpe>0)) {
									$msg.=$nb_dest_cpe." CPE(s), ";
								}
								if((isset($mail_scol[$obj_classe->id_classe]))&&($nb_dest_scol>0)) {
									$msg.=$nb_dest_scol." compte(s) scolarité, ";
								}
								if((isset($mail_eleve[$obj_classe->id_classe]))&&($nb_dest_eleve>0)) {
									$msg.=$nb_dest_eleve." élève(s), ";
								}
								if((isset($mail_resp[$obj_classe->id_classe]))&&($nb_dest_resp>0)) {
									$msg.=$nb_dest_resp." responsable(s), ";
								}
								$msg=mb_substr($msg, 0, mb_strlen($msg)-2);
								$msg.=".<br />";

								$message_mail2.="Compte-rendu ".$obj_classe->classe.": Mail envoyé à $destinataires_bcc\n\n";
							}
							else {
								$msg.=$obj_classe->classe." échec de l'envoi du mail.<br />";
							}
						}
					}
				}
			}

			if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
				$sujet_mail2="[GEPI]: Compte-rendu envoi de mail ev n°".$id_ev;
				$message_mail2.="\nFin du compte-rendu.\n\nBien cordialement.
-- 
".getSettingValue('gepiSchoolName');
				envoi_mail($sujet_mail2, $message_mail2, $_SESSION['email']);
			}
		}
	}
}

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_suppression = "Confirmation de suppression";
//**************** EN-TETE *****************
$titre_page = "Gestion des dates";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

echo "<a name=\"debut_de_page\"></a>";

//debug_var();
/*
echo "<div style='color: #FF0000; text-align: center; padding: 0.5%;'>";
if ($msg_erreur!="") echo "<p style='color: #FF0000; font-variant: small-caps;'>".$msg_erreur."</p>";
if ($msg_OK!="") echo "<p style='color: #0000FF; font-variant: small-caps;'>".$msg_OK."</p>";
echo "</div>";
*/

echo "<p class='bold'><a href='../accueil.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2>Description de l'événement n°$id_ev&nbsp;:</h2>
<div style='margin-left:3em; margin-right:3em; margin-bottom:1em; padding:1em;' class='fieldset_opacite50'>";

$afficher_obsolete="y";
echo affiche_evenement($id_ev, $afficher_obsolete);

echo "</div>
<h3>Informer par mail les personnes concernées&nbsp;:</h3>";

if(getSettingValue('envoi_mail_actif')=="n") {
	echo "<p style='color:red'>L'envoi de mail est noté comme désactivé.</p>";
}

$sql="SELECT DISTINCT id_classe, classe, nom_complet FROM d_dates_evenements_classes d, classes c WHERE id_ev='".$id_ev."' AND d.id_classe=c.id ORDER BY classe;";
//echo "$sql<br />";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p style='color:red'>Aucune classe n'est concernée par cet événement???</p>";
	require("../lib/footer.inc.php");
	die();
}
else {
	$tab_classe=array();
	while($obj_classe=mysqli_fetch_object($res)) {
		$tab_classe[$obj_classe->id_classe]['classe']=$obj_classe->classe;
		$tab_classe[$obj_classe->id_classe]['nom_complet']=$obj_classe->nom_complet;
	}
}

$style_et_title_prof=" style='background-color:silver' title=\"L'événement n'est pas affiché en page d'accueil pour les professeurs de la classe.\"";
$style_et_title_cpe=" style='background-color:silver' title=\"L'événement n'est pas affiché en page d'accueil pour les CPE de la classe.\"";
$style_et_title_scol=" style='background-color:silver' title=\"L'événement n'est pas affiché en page d'accueil pour les comptes scolarité de la classe.\"";
$style_et_title_eleve=" style='background-color:silver' title=\"L'événement n'est pas affiché en page d'accueil pour les élèves de la classe.\"";
$style_et_title_resp=" style='background-color:silver' title=\"L'événement n'est pas affiché en page d'accueil pour les responsables des élèves de la classe.\"";
$tab_statut=array();
$sql="SELECT DISTINCT statut FROM d_dates_evenements_utilisateurs d WHERE id_ev='".$id_ev."' ORDER BY statut;";
//echo "$sql<br />";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	while($obj=mysqli_fetch_object($res)) {
		$tab_statut[]=$obj->statut;

		if($obj->statut=='professeur') {
			$style_et_title_prof="";
		}
		elseif($obj->statut=='cpe') {
			$style_et_title_cpe="";
		}
		elseif($obj->statut=='scolarite') {
			$style_et_title_scol="";
		}
		elseif($obj->statut=='eleve') {
			$style_et_title_eleve="";
		}
		elseif($obj->statut=='responsable') {
			$style_et_title_resp="";
		}
	}
}

echo "
<form action=\"".$_SERVER['PHP_SELF']."#debut_de_page\" method=\"post\" style=\"width: 100%;\" name=\"formulaire\">
	<fieldset style='margin-left:3em; border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		".add_token_field()."
		<input type=\"hidden\" name=\"id_ev\" value=\"$id_ev\" />
		<input type=\"hidden\" name=\"envoi_mail\" value=\"y\" />

		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th>Classe/Statuts</th>
					<th>Professeurs</th>
					<th>Scolarité</th>
					<th>Cpe</th>
					<th>Élèves</th>
					<th>Responsables</th>
				</tr>
			</thead>
			<tbody>";
foreach($tab_classe as $id_classe =>$classe_courante) {
	// A faire: afficher le nombre d'adresses mail valides dans chaque cas
	echo "
				<tr>
					<td>".$classe_courante['classe']."</td>
					<td".$style_et_title_prof."><input type='checkbox' name='mail_prof[$id_classe]' value='$id_classe' /></td>
					<td".$style_et_title_scol."><input type='checkbox' name='mail_scol[$id_classe]' value='$id_classe' /></td>
					<td".$style_et_title_cpe."><input type='checkbox' name='mail_cpe[$id_classe]' value='$id_classe' /></td>
					<td".$style_et_title_eleve."><input type='checkbox' name='mail_eleve[$id_classe]' value='$id_classe' /></td>
					<td".$style_et_title_resp."><input type='checkbox' name='mail_resp[$id_classe]' value='$id_classe' /></td>
				</tr>";
}
echo "
			</tbody>
		</table>

		<p></p>

		<p><input type='submit' value='Envoyer le mail' /></p>



	</fieldset>
</form>";

require("../lib/footer.inc.php");
?>
