<?php

/*
 * $Id$
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

// SQL : INSERT INTO droits VALUES ( '/cahier_notes/autorisation_exceptionnelle_saisie.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Autorisation exceptionnelle de saisie dans le carnet de notes.', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

/*

DROP TABLE IF EXISTS acces_cn;
CREATE TABLE acces_cn (
id INT( 11 ) NOT NULL AUTO_INCREMENT ,
id_groupe INT( 11 ) NOT NULL ,
periode INT( 11 ) NOT NULL ,
date_limite timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
commentaires text NOT NULL,
PRIMARY KEY ( id )
) CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Acces exceptionnel au CN en periode close';

*/

if(($_SESSION['statut']=='scolarite')&&(!getSettingAOui('PeutDonnerAccesCNPeriodeCloseScol'))) {
	$mess=rawurlencode("Accès interdit !");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$periode=isset($_POST['periode']) ? $_POST['periode'] : (isset($_GET['periode']) ? $_GET['periode'] : NULL);
$is_posted=isset($_POST['is_posted']) ? $_POST['is_posted'] : (isset($_GET['is_posted']) ? $_GET['is_posted'] : NULL);

$display_date_limite=isset($_POST['display_date_limite']) ? $_POST['display_date_limite'] : (isset($_GET['display_date_limite']) ? $_GET['display_date_limite'] : NULL);
$display_heure_limite=isset($_POST['display_heure_limite']) ? $_POST['display_heure_limite'] : (isset($_GET['display_heure_limite']) ? $_GET['display_heure_limite'] : NULL);

// Pour refermer la page plutôt que proposer un lien retour dans certains cas
$refermer_page=isset($_POST['refermer_page']) ? $_POST['refermer_page'] : (isset($_GET['refermer_page']) ? $_GET['refermer_page'] : NULL);


$msg="";

if((isset($is_posted))&&(isset($_POST['no_anti_inject_message_autorisation_exceptionnelle_cn']))&&($_SESSION['statut']=='administrateur')) {
	check_token();
	//echo "BLIP";
	if (isset($NON_PROTECT["message_autorisation_exceptionnelle_cn"])){
		$message_autorisation_exceptionnelle_cn= traitement_magic_quotes(corriger_caracteres($NON_PROTECT["message_autorisation_exceptionnelle_cn"]));
	}
	else{
		$message_autorisation_exceptionnelle_cn="";
	}

	// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
	$message_autorisation_exceptionnelle_cn=suppression_sauts_de_lignes_surnumeraires($message_autorisation_exceptionnelle_cn);

	if(!saveSetting('message_autorisation_exceptionnelle_cn',$message_autorisation_exceptionnelle_cn)) {
		$msg="Erreur lors de l'enregistrement du message personnalisé.<br />";
	}
	else {
		$msg="Enregistrement du message personnalisé effectué.<br />";
	}
}

if((isset($is_posted))&&(isset($id_classe))&&(isset($id_groupe))&&(isset($periode))&&(isset($display_date_limite))&&(isset($display_heure_limite))) {
	check_token();
	if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $_POST['display_date_limite'])) {
		$annee = mb_substr($_POST['display_date_limite'],6,4);
		$mois = mb_substr($_POST['display_date_limite'],3,2);
		$jour = mb_substr($_POST['display_date_limite'],0,2);
		//echo "$jourd/$moisd/$anneed<br />";

		if(!checkdate($mois, $jour, $annee)) {
			$msg.="ERREUR : La date $jour/$mois/$annee n'est pas valide.<br />";
		}
		else {
			if (preg_match("/([0-9]{1,2}):([0-9]{0,2})/", str_ireplace('h',':',$display_heure_limite))) {
				$heure = mb_substr($_POST['display_heure_limite'],0,2);
				$minute = mb_substr($_POST['display_heure_limite'],3,2);

				if(($heure>23)||($heure<0)||($minute<0)||($minute>59)) {
					$msg.="ERREUR : L'heure $heure/$minute n'est pas valide.<br />";
				}
				else {
					$sql="DELETE FROM acces_cn WHERE id_groupe='$id_groupe' AND periode='$periode';";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);

					$date_limite_email="$annee/$mois/$jour à $heure:$minute";
					$sql="INSERT INTO acces_cn SET id_groupe='$id_groupe', periode='$periode', date_limite='$annee-$mois-$jour $heure:$minute:00';";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$res) {
						$msg.="ERREUR lors de l'insertion de l'enregistrement.<br />";
					}
					else {
						$msg.="Enregistrement de l'autorisation effectué.<br />";

						$_SESSION['autorisation_saisie_date_limite']=mktime($heure, $minute, 0, $mois, $jour, $annee);

						$complement_texte_mail="";
						if(($_SESSION['statut']=='administrateur')||(($_SESSION['statut']=='scolarite')&&(getSettingAOui('PeutDonnerAccesBullNotePeriodeCloseScol')))) {
							if((isset($_POST['donner_acces_modif_bull_note']))&&($_POST['donner_acces_modif_bull_note']=='y')) {
								$sql="DELETE FROM acces_exceptionnel_matieres_notes WHERE id_groupe='$id_groupe' AND periode='$periode';";
								$menage=mysqli_query($GLOBALS["mysqli"], $sql);
								$sql="INSERT INTO acces_exceptionnel_matieres_notes SET id_groupe='$id_groupe', periode='$periode', date_limite='$annee-$mois-$jour $heure:$minute:00';";
								$res=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$res) {
									$msg.="ERREUR lors de l'insertion de l'enregistrement pour les notes des bulletins.<br />";
								}
								else {
									$msg.="Enregistrement de l'autorisation pour les notes des bulletins effectué.<br />";
									$complement_texte_mail="Vous pourrez aussi corriger les moyennes du bulletin.\n\n";
								}
							}
						}

						if(($_SESSION['statut']=='administrateur')||(($_SESSION['statut']=='scolarite')&&(getSettingAOui('PeutDonnerAccesBullAppPeriodeCloseScol')))) {
							if((isset($_POST['donner_acces_modif_bull_app']))&&($_POST['donner_acces_modif_bull_app']=='y')) {
								$sql="DELETE FROM matieres_app_delais WHERE id_groupe='$id_groupe' AND periode='$periode';";
								$menage=mysqli_query($GLOBALS["mysqli"], $sql);

								$mode=isset($_POST['mode']) ? $_POST['mode'] : "proposition";
								if((isset($mode))&&(!in_array($mode, array('proposition', 'acces_complet')))) {
									$mode="proposition";
									$msg.="Mode de validation de la saisie d'appréciation incorrect.<br />On opte pour une 'proposition seule' devant être controlée/validée par la suite.<br />";
								}
								$sql="INSERT INTO matieres_app_delais SET id_groupe='$id_groupe', periode='$periode', date_limite='$annee-$mois-$jour $heure:$minute:00', mode='".$mode."';";
								$res=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$res) {
									$msg.="ERREUR lors de l'insertion de l'enregistrement pour les appréciations des bulletins.<br />";
								}
								else {
									$msg.="Enregistrement de l'autorisation pour les appréciations des bulletins effectué.<br />";
									$complement_texte_mail="Vous pourrez aussi corriger les appreciations du bulletin.\n\n";
								}
							}
						}

						$envoi_mail_actif=getSettingValue('envoi_mail_actif');
						if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
							$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
						}
			
						if($envoi_mail_actif=='y') {
							$email_personne_autorisant="";
							$nom_personne_autorisant="";
							$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
							$req=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($req)>0) {
								$lig_u=mysqli_fetch_object($req);
								$nom_personne_autorisant=$lig_u->civilite." ".casse_mot($lig_u->nom,'maj')." ".casse_mot($lig_u->prenom,'majf');
								$email_personne_autorisant=$lig_u->email;
								if(check_mail($email_personne_autorisant)) {
									$tab_param_mail['cc'][]=$email_personne_autorisant;
									$tab_param_mail['cc_name'][]=$nom_personne_autorisant;
									$tab_param_mail['replyto']=$email_personne_autorisant;
									$tab_param_mail['replyto_name']=$nom_personne_autorisant;
								}
							}
		
							$email_destinataires="";
							$designation_destinataires="";
							// Recherche des profs du groupe
							$sql="SELECT DISTINCT u.email, u.civilite, u.nom, u.prenom FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.id_groupe='$id_groupe' AND jgp.login=u.login AND u.email!='';";
							//echo "$sql<br />";
							$req=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($req)>0) {
								$lig_u=mysqli_fetch_object($req);
								$designation_destinataires.=remplace_accents($lig_u->civilite." ".$lig_u->nom." ".casse_mot($lig_u->prenom,'majf2'),'all_nospace');
								$email_destinataires.=$designation_destinataires." <".$lig_u->email.">";
								$tab_param_mail['destinataire'][]=$lig_u->email;
								$tab_param_mail['destinataire_name'][]=$designation_destinataires;
								while($lig_u=mysqli_fetch_object($req)) {
									$designation_destinataire_courant=remplace_accents($lig_u->civilite." ".$lig_u->nom." ".casse_mot($lig_u->prenom,'majf2'),'all_nospace');
									$designation_destinataires.=", ".$designation_destinataire_courant;
									// Il se passe un truc bizarre avec les suivants
									//$email_destinataires.=$designation_destinataires." <".$lig_u->email.">";
									$email_destinataires.=", ".$lig_u->email;
									$tab_param_mail['destinataire'][]=$lig_u->email;
									$tab_param_mail['destinataire_name'][]=$designation_destinataire_courant;
								}

								$sujet_mail="[GEPI] Autorisation exceptionnelle de saisie/correction de CN";
				
								//$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";
								//if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}

								$ajout_header="";
								if($email_personne_autorisant!="") {
									$ajout_header.="Cc: $nom_personne_autorisant <".$email_personne_autorisant.">";
									// 20160615 PP
									$ajout_header.="\r\n";
								}

								if($email_personne_autorisant!="") {
									$ajout_header.="Reply-to: $nom_personne_autorisant <".$email_personne_autorisant.">\r\n";
								}

								$tab_champs=array('classes');
								$current_group=get_group($id_groupe,$tab_champs);

								$texte_mail="Vous avez jusqu'au $date_limite_email pour saisir/corriger une ou des notes du carnet de notes pour l'enseignement ".$current_group['name']." (".$current_group['description']." en ".$current_group['classlist_string'].") en période $periode.\n\n";
								$texte_mail.=$complement_texte_mail;
								$message_autorisation_exceptionnelle_cn=getSettingValue('message_autorisation_exceptionnelle_cn');

								if($message_autorisation_exceptionnelle_cn=='') {
									$texte_mail.="Cette autorisation est exceptionnelle.\nIl conviendra de veiller à effectuer les saisies dans les temps une prochaine fois.\n";
								}
								else {
									$texte_mail.=$message_autorisation_exceptionnelle_cn."\n";
								}

								$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
								$texte_mail=$salutation." ".$designation_destinataires.",\n\n".$texte_mail."\nCordialement.\n-- \n".$nom_personne_autorisant;

								$envoi = envoi_mail($sujet_mail, $texte_mail, $email_destinataires, $ajout_header, "plain", $tab_param_mail);

								if($envoi) {$msg.="Email expédié à ".htmlspecialchars($email_destinataires)."<br />";}
							}
			
						}
					}
				}
			}
			else {
				$msg = "ATTENTION : L'heure limite n'est pas valide.<br />L'enregistrement ne peut avoir lieu.<br />";
			}
		}
	}
	else {
		$msg = "ATTENTION : La date limite n'est pas valide.<br />L'enregistrement ne peut avoir lieu.<br />";
	}
}

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

//**************** EN-TETE *****************
$titre_page = "Autorisation exceptionnelle de saisie de CN";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();
echo "<p class='bold'>\n";

if($refermer_page=='y') {
	echo "<a href='../accueil.php' onClick='self.close();return false;'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Refermer la page </a>\n";
}
else {
	echo "<a href=\"../accueil.php\" ><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>\n";
}

if(($_SESSION['statut']=='administrateur')&&(isset($_GET['definir_message']))) {
	echo " | <a href=\"".$_SERVER['PHP_SELF']."\" > Autorisation exceptionnelle</a>";
	echo "</p>\n";

	echo "<p>Par défaut le message reçu par un professeur exceptionnellement autorisé à saisir en retard ou corriger des notes d'un de ses carnets de notes est le suivant&nbsp;:<br />\n";

	$texte_mail="Bonjour/Bonsoir\n\nVous avez jusqu'au TELLE DATE TELLE HEURE\npour saisir/corriger une ou des notes du carnet de notes pour l'enseignement XXXXXXXXXX\nen TELLE(S) CLASSE(S) en période NUMERO_PERIODE.\n\n";
	$texte_mail.="<b>Cette autorisation est exceptionnelle.\nIl conviendra de veiller à effectuer les saisies dans les temps une prochaine fois.</b>\n";
	$texte_mail.="\nCordialement.";

	echo "<pre style='color:blue;'>".$texte_mail."</pre>\n";

	echo "<p>Ce message peut être partiellement personnalisé.<br />Vous pouvez intervenir sur la partie en gras du message.</p>\n";

	$message_autorisation_exceptionnelle_cn=getSettingValue('message_autorisation_exceptionnelle_cn');

	if($message_autorisation_exceptionnelle_cn!='') {
		echo "<p>Votre message est actuellement personnalisé de la façon suivante&nbsp;:";
		$texte_mail="Bonjour/Bonsoir\n\nVous avez jusqu'au TELLE DATE TELLE HEURE\npour saisir/corriger une ou des notes du carnet de notes pour l'enseignement XXXXXXXXXX\nen TELLE(S) CLASSE(S) en période NUMERO_PERIODE.\n\n";
		$texte_mail.="<b>$message_autorisation_exceptionnelle_cn</b>\n";
		$texte_mail.="\nCordialement.";
	
		echo "<pre style='color:green;'>".$texte_mail."</pre>\n";
	}
	else {
		$texte_mail.=$message_autorisation_exceptionnelle_cn."\n";
	}

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	echo "<p><b>Message personnalisé&nbsp;:</b><br />\n";
	echo "<textarea name='no_anti_inject_message_autorisation_exceptionnelle_cn' rows='2' cols='100'>$message_autorisation_exceptionnelle_cn</textarea>\n";
	echo "<br />\n";
	echo add_token_field();
	echo "<input type='hidden' name='is_posted' value='1' />\n";
	echo "<input type='submit' name='Valider' value='Valider' />\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	die();
}

if(!isset($id_classe)) {
	if($_SESSION['statut']=='administrateur') {
		echo " | <a href=\"".$_SERVER['PHP_SELF']."?definir_message=y\" > Définir le message</a>";
	}
	echo "</p>\n";

	echo "<h2>Autoriser la modification/saisie de notes du carnet de notes</h2>";

	//echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

	// On adapte la liste des classes selon le visiteur
	if($_SESSION['statut']=='scolarite') {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='administrateur') {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	else {
		// On ne doit pas arriver là
		echo "<p style='color:red;'>Statut non autorisé.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_clas)==0) {
		echo "<p style='color:red'>Aucune classe n'a été trouvée.";
		if($_SESSION['statut']=='scolarite') {
			echo "<br />Vous n'êtes pas désigné dans Gepi pour suivre une classe.<br />Un administrateur doit vous associer des classes dans <strong>Gestion des bases/Gestion des classes/Paramétrage scolarité</strong>.";
		}
		echo "</p>\n";
	}
	else {
		echo "<p>Choisir une classe&nbsp;:</p>\n";

		$tab_conseils_de_classe=get_tab_date_prochain_evenement_telle_classe("", 'conseil_de_classe');

		$tab_txt=array();
		$tab_lien=array();

		while($lig_clas=mysqli_fetch_object($res_clas)) {
			if(isset($tab_conseils_de_classe[$lig_clas->id])) {

				$lieu_conseil_de_classe="";
				if(isset($tab_conseils_de_classe[$lig_clas->id]['lieu']['designation_complete'])) {
					$lieu_conseil_de_classe=" (".$tab_conseils_de_classe[$lig_clas->id]['lieu']['designation_complete'].")";
				}

				$chaine_tmp=" <span style='font-size:small;' title=\"Date du prochain conseil de classe pour la\n".$tab_conseils_de_classe[$lig_clas->id]['classe']." : ".$tab_conseils_de_classe[$lig_clas->id]['slashdate_heure_ev'].$lieu_conseil_de_classe."\">(".$tab_conseils_de_classe[$lig_clas->id]['slashdate_ev'].")</span>";

				$tab_txt[]=$lig_clas->classe.$chaine_tmp;
			}
			else {
				$tab_txt[]=$lig_clas->classe;
			}

			if(isset($id_incident)) {
				//$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id."&amp;id_incident=$id_incident";
				$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id."";
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
elseif((!isset($id_groupe))||(!isset($periode))) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>\n";
	echo "</p>\n";

	$chaine_date_conseil_classe=affiche_date_prochain_conseil_de_classe_classe($id_classe, "", "span");
	if($chaine_date_conseil_classe!="") {
		$chaine_date_conseil_classe="<div class='fieldset_opacite50' style='float:right; width:10em; font-size:normal; text-align:center;'>".$chaine_date_conseil_classe."</div>";
		echo $chaine_date_conseil_classe;
	}

	echo "<h2>Autoriser la modification/saisie de notes du carnet de notes</h2>";

	echo "<p>Pour quel enseignement souhaitez-vous autoriser un enseignant à effectuer des modifications dans son carnet de notes&nbsp;?</p>\n";
	$get_groups_for_class_avec_visibilite="y";
	$groups=get_groups_for_class($id_classe,"","n");

	include("../lib/periodes.inc.php");

	$date_courante=time();

	echo "<table class='boireaus boireaus_alt' summary='Tableau des enseignements et périodes'>\n";
	echo "<tr>\n";
	echo "<th>Enseignements</th>\n";
	echo "<th>Classe(s)</th>\n";
	echo "<th>Enseignants</th>\n";
	echo "<th colspan='$nb_periode'>Périodes</th>\n";
	echo "</tr>\n";
	foreach($groups as $current_group) {
		if((!isset($current_group["visibilite"]["cahier_notes"]))||($current_group["visibilite"]["cahier_notes"]=="y")) {
			echo "<tr class='white_hover'>\n";
			echo "<td style='text-align:left;'>".$current_group['name']." (<span style='font-size:xx-small;'>".$current_group['description']."</span>)</td>\n";

			echo "<td>".$current_group["classlist_string"]."</td>\n";

			echo "<td>\n";
			$sql="SELECT u.login, u.nom, u.prenom, u.civilite FROM utilisateurs u, j_groupes_professeurs j WHERE (u.login = j.login and j.id_groupe = '" . $current_group['id'] . "') ORDER BY u.nom, u.prenom";
			$get_profs=mysqli_query($GLOBALS["mysqli"], $sql);

			$nb = mysqli_num_rows($get_profs);
			for ($i=0;$i<$nb;$i++){
				if($i>0) {echo ",<br />\n";}
				$p_login = old_mysql_result($get_profs, $i, "login");
				$p_nom = old_mysql_result($get_profs, $i, "nom");
				$p_prenom = old_mysql_result($get_profs, $i, "prenom");
				$civilite = old_mysql_result($get_profs, $i, "civilite");
				echo "$civilite $p_nom $p_prenom";
			}
			echo "</td>\n";

			for($i=1;$i<$nb_periode;$i++) {
				if($ver_periode[$i]=='P') {
					//echo "<td><input type='checkbox' name='periode_grp_".$current_group['id']."[]' value='$i' /></td>\n";
					echo "<td>\n";
					echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;id_groupe=".$current_group['id']."&amp;periode=$i'>Période $i</a>\n";
					$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM acces_cn WHERE id_groupe='".$current_group['id']."' AND periode='$i';";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)>0) {
						$lig=mysqli_fetch_object($res);
						if($lig->date_limite>$date_courante) {
							echo "<br />";
							echo "Autorisation jusqu'au<br />".strftime("%d/%m/%Y à %H:%M",$lig->date_limite);
						}
					}
					echo "</td>\n";
				}
				elseif($ver_periode[$i]=='O') {
					echo "<td><img src='../images/disabled.png' width='20' height='20' alt='Période $i close' title='Période $i close' /></td>\n";
				}
				else {
					echo "<td><img src='../images/enabled.png' width='20' height='20' alt='Période $i ouverte en saisie' title='Période $i ouverte en saisie' /></td>\n";
				}
			}
			echo "</tr>\n";
		}
	}
	echo "</table>\n";
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>\n";
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Choisir un autre enseignement de la classe</a>\n";
	echo "</p>\n";

	$chaine_date_conseil_classe=affiche_date_prochain_conseil_de_classe_classe($id_classe, "", "span");
	if($chaine_date_conseil_classe!="") {
		$chaine_date_conseil_classe="<div class='fieldset_opacite50' style='float:right; width:10em; font-size:normal; text-align:center;'>".$chaine_date_conseil_classe."</div>";
		echo $chaine_date_conseil_classe;
	}

	echo "<h2>Autoriser la modification/saisie de notes du carnet de notes</h2>";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	echo add_token_field();
	$group=get_group($id_groupe);
	echo "<p>Vous souhaitez autoriser exceptionnellement un enseignant à effectuer des saisies/corrections de notes du carnet de notes pour l'enseignement ".$group['name']." (<span style='font-size:x-small;'>".$group['description']." en ".$group['classlist_string']."</span>) en période $periode.</p>\n";

	$sql="SELECT commentaires, UNIX_TIMESTAMP(date_limite) AS date_limite FROM acces_cn WHERE id_groupe='".$group['id']."' AND periode='$periode';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$date_limite=$lig->date_limite;
		$commentaires=$lig->commentaires;

		$date_courante=time();

		//$tab_date_limite=get_date($date_limite);
		echo "<p class='bold'>Une autorisation exceptionnelle de saisie existe pour cet enseignement/période&nbsp;: ".strftime("%d/%m/%Y à %H:%M",$date_limite)."</p>\n";
		$display_date_limite=strftime("%d/%m/%Y",$date_limite);
		$display_heure_limite=strftime("%H:%M",$date_limite);

		if($commentaires!="") {
			echo "<div style='margin:1em; border:1px solid black; height: 10em;max-height: 10em; overflow:auto; background-image: url(\"../images/background/opacite50.png\");'><strong>Compte-rendu des modifications effectuées&nbsp;:</strong><br />".nl2br($commentaires)."</div>\n";
		}

		if($date_courante>$date_limite) {
			echo "<p class='bold' style='color:red;'>Le délais imparti pour la saisie/correction est dépassé.</p>\n";
		}
	}
	else {
		$annee = strftime("%Y");
		$mois = strftime("%m");
		$jour = strftime("%d");
		$display_date_limite=$jour."/".$mois."/".$annee;
	
		$date_courante=getdate();
		$heure_courante=$date_courante['hours'];
		$minute_courante=$date_courante['minutes'];
		if($minute_courante+15>=60) {
			if($heure_courante+1>=24) {
				$heure_limite=$heure_courante+1-24;
				$minute_limite=$minute_courante+15-60;
				// A charge au couche-tard d'augmenter d'un jour...
			}
			else {
				$heure_limite=$heure_courante+1;
				$minute_limite=$minute_courante+15-60;
			}
		}
		else {
			$heure_limite=$heure_courante;
			$minute_limite=$minute_courante+15;
		}

		$display_heure_limite="$heure_limite:$minute_limite";

		$ts_display_date_limite=mktime($heure_limite, $minute_limite, 0, $mois, $jour, $annee);
		if((isset($_SESSION['autorisation_saisie_date_limite']))&&($_SESSION['autorisation_saisie_date_limite']>=$ts_display_date_limite)) {
			$display_date_limite=strftime("%d/%m/%Y", $_SESSION['autorisation_saisie_date_limite']);
			$display_heure_limite=strftime("%H:%M", $_SESSION['autorisation_saisie_date_limite']);
		}
	}

	echo "<p>Quelle doit être la date/heure limite de cette autorisation de modification de notes du carnet de notes&nbsp;?<br />\n";
	//include("../lib/calendrier/calendrier.class.php");
	//$cal = new Calendrier("formulaire", "display_date_limite");

	if(isset($refermer_page)) {
		echo "<input type='hidden' name='refermer_page' value='y' />\n";
	}
	echo "<input type='hidden' name='is_posted' value='y' />\n";
	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
	echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
	echo "<input type='hidden' name='periode' value='$periode' />\n";
	echo "<input type='text' name = 'display_date_limite' id = 'display_date_limite' size='8' value = \"".$display_date_limite."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
	//echo "<a href=\"#\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Calendrier\" /></a>\n";
	echo img_calendrier_js("display_date_limite", "img_bouton_display_date_limite");

	echo " à <input type='text' name='display_heure_limite' id='display_heure_limite' size='8' value = \"".$display_heure_limite."\" onKeyDown=\"clavier_heure(this.id,event);\" autocomplete=\"off\" />\n";

	if((!isset($group["visibilite"]["bulletins"]))||($group["visibilite"]["bulletins"]=="y")) {
		if(($_SESSION['statut']=='administrateur')||(($_SESSION['statut']=='scolarite')&&(getSettingAOui('PeutDonnerAccesBullNotePeriodeCloseScol')))) {
			echo "<br />\n";
			echo "<input type='checkbox' name='donner_acces_modif_bull_note' id='donner_acces_modif_bull_note' value='y' /><label for='donner_acces_modif_bull_note'> Donner aussi l'accès à la modification de la moyenne sur les bulletins associés</label>";
			echo "<br />\n";
		}

		if(($_SESSION['statut']=='administrateur')||(($_SESSION['statut']=='scolarite')&&(getSettingAOui('PeutDonnerAccesBullAppPeriodeCloseScol')))) {
			echo "<input type='checkbox' name='donner_acces_modif_bull_app' id='donner_acces_modif_bull_app' value='y' /><label for='donner_acces_modif_bull_app'> Donner aussi l'accès à la modification de l'appréciation sur les bulletins associés</label><br />";
			echo "<div style='margin-left:3em;'>";
				echo "<input type='radio' name='mode' id='mode_proposition' value='proposition' checked /><label for='mode_proposition'> Permettre la proposition de corrections (<em>proposition qui devront ensuite être validées par un compte scolarité ou administrateur</em>).</label>\n";
				echo "<br />";
				if(getSettingAOui('autoriser_correction_bulletin')) {
					echo "<span style='color:red'>Ce premier mode ne présente pas d'intérêt ici puisque vous avez donné globalement le droit (<em>en administrateur dans Gestion générale/Droits d'accès</em>) de proposer des corrections tant que la période n'est pas complètement close</span>.<br /><span style='color:red'>Seul le mode ci-dessous apporte quelque chose dans votre configuration.</span><br />";
				}
				echo "<input type='radio' name='mode' id='mode_acces_complet' value='acces_complet' /><label for='mode_acces_complet'> Permettre la saisie/modification des appréciations sans contrôle de votre part avant validation.</label>\n";
				echo "<br />";
			echo "</div>";
		}
	}
	echo "<input type='submit' name='Valider' value='Valider' />\n";
	echo "</p>\n";

	// Mail

	echo "</form>\n";

	if((isset($commentaires))&&($commentaires!="")) {
		echo "<p><br /></p>\n<p style='text-indent:-4.4em;margin-left:4.4em;'><em>NOTES&nbsp;:</em> Si vous définissez un nouveau délais, le texte des modifications effectuées (<em>affiché ci-dessus</em>) sera supprimé.<br />Si vous souhaitez en conserver la trace, prenez soin de copier/coller le contenu vers un bloc-notes,...</p>\n";
	}
}

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
