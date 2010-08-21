<?php

/*
 * $Id$
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

//$variables_non_protegees = 'yes';

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

// SQL : INSERT INTO droits VALUES ( '/bulletin/autorisation_exceptionnelle_saisie_app.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Autorisation exceptionnelle de saisie d appréciation', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/bulletin/autorisation_exceptionnelle_saisie_app.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Autorisation exceptionnelle de saisie d appréciation', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$periode=isset($_POST['periode']) ? $_POST['periode'] : (isset($_GET['periode']) ? $_GET['periode'] : NULL);
$is_posted=isset($_POST['is_posted']) ? $_POST['is_posted'] : (isset($_GET['is_posted']) ? $_GET['is_posted'] : NULL);

$display_date_limite=isset($_POST['display_date_limite']) ? $_POST['display_date_limite'] : (isset($_GET['display_date_limite']) ? $_GET['display_date_limite'] : NULL);
$display_heure_limite=isset($_POST['display_heure_limite']) ? $_POST['display_heure_limite'] : (isset($_GET['display_heure_limite']) ? $_GET['display_heure_limite'] : NULL);

$msg="";

if((isset($is_posted))&&(isset($id_classe))&&(isset($id_groupe))&&(isset($periode))&&(isset($display_date_limite))&&(isset($display_heure_limite))) {
	if (my_ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $_POST['display_date_limite'])) {
		$annee = substr($_POST['display_date_limite'],6,4);
		$mois = substr($_POST['display_date_limite'],3,2);
		$jour = substr($_POST['display_date_limite'],0,2);
		//echo "$jourd/$moisd/$anneed<br />";

		if(!checkdate($mois, $jour, $annee)) {
			$msg.="ERREUR : La date $jour/$mois/$annee n'est pas valide.<br />";
		}
		else {
			if (my_ereg("([0-9]{1,2}):([0-9]{0,2})", str_ireplace('h',':',$display_heure_limite))) {
				$heure = substr($_POST['display_heure_limite'],0,2);
				$minute = substr($_POST['display_heure_limite'],3,2);

				if(($heure>23)||($heure<0)||($minute<0)||($minute>59)) {
					$msg.="ERREUR : L'heure $heure/$minute n'est pas valide.<br />";
				}
				else {
					$sql="DELETE FROM matieres_app_delais WHERE id_groupe='$id_groupe' AND periode='$periode';";
					$res=mysql_query($sql);

					$date_limite_email="$annee/$mois/$jour à $heure:$minute";
					$sql="INSERT INTO matieres_app_delais SET id_groupe='$id_groupe', periode='$periode', date_limite='$annee-$mois-$jour $heure:$minute:00';";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="ERREUR lors de l'insertion de l'enregistrement.<br />";
					}
					else {
						$msg.="Enregistrement de l'autorisation effectué.<br />";

						$envoi_mail_actif=getSettingValue('envoi_mail_actif');
						if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
							$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
						}
			
						if($envoi_mail_actif=='y') {
							$email_personne_autorisant="";
							$nom_personne_autorisant="";
							$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
							$req=mysql_query($sql);
							if(mysql_num_rows($req)>0) {
								$lig_u=mysql_fetch_object($req);
								$nom_personne_autorisant=$lig_u->civilite." ".casse_mot($lig_u->nom,'maj')." ".casse_mot($lig_u->prenom,'majf');
								$email_personne_autorisant=$lig_u->email;
							}
		
							$email_destinataires="";
							// Recherche des profs du groupe
							$sql="SELECT DISTINCT u.email, u.civilite, u.nom, u.prenom FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.id_groupe='$id_groupe' AND jgp.login=u.login AND u.email!='';";
							//echo "$sql<br />";
							$req=mysql_query($sql);
							if(mysql_num_rows($req)>0) {
								$lig_u=mysql_fetch_object($req);
								$email_destinataires.=remplace_accents($lig_u->civilite." ".$lig_u->nom." ".casse_mot($lig_u->prenom,'majf2'),'all_nospace')." <".$lig_u->email.">";
								while($lig_u=mysql_fetch_object($req)) {$email_destinataires.=",".$lig_u->email;}

								$sujet_mail="[GEPI] Autorisation exceptionnelle de saisie/correction d'appréciation";
				
								$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";
								if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}
					
								$ajout_header="";
								if($email_personne_autorisant!="") {
									$ajout_header.="Cc: $nom_personne_autorisant <".$email_personne_autorisant.">";
									$ajout_header.="\r\n";
									$ajout_header.="Reply-to: $nom_personne_autorisant <".$email_personne_autorisant.">\r\n";
								}

								$tab_champs=array('classes');
								$current_group=get_group($id_groupe,$tab_champs);
								$texte_mail="Vous avez jusqu'au $date_limite_email pour saisir/corriger une ou des appréciations pour l'enseignement ".$current_group['name']." (".$current_group['description']." en ".$current_group['classlist_string'].") en période $periode.\n\nCette autorisation est exceptionnelle.\nIl conviendra de veiller à effectuer les saisies dans les temps une prochaine fois.\n";

								$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
								$texte_mail=$salutation.",\n\n".$texte_mail."\nCordialement.\n-- \n".$nom_personne_autorisant;
	
								$envoi = mail($email_destinataires,
									$gepiPrefixeSujetMail.$sujet_mail,
									$texte_mail,
									"From: Mail automatique Gepi\r\n".$ajout_header."X-Mailer: PHP/".phpversion());
								if($envoi) {$msg.="Email expédié à ".htmlentities($email_destinataires)."<br />";}
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


//**************** EN-TETE *****************
$titre_page = "Autorisation exceptionnelle de saisie d'appréciations";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var();
echo "<p class='bold'>\n";
echo "<a href=\"../accueil.php\" ><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>\n";

if(!isset($id_classe)) {
	echo "</p>\n";

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

	$res_clas=mysql_query($sql);
	if(mysql_num_rows($res_clas)>0) {
		echo "<p>Choisir une classe&nbsp;:</p>\n";

		$tab_txt=array();
		$tab_lien=array();

		while($lig_clas=mysql_fetch_object($res_clas)) {
			$tab_txt[]=$lig_clas->classe;
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
	echo "<p>Pour quel enseignement souhaitez-vous autoriser un enseignant à proposer des saisies/corrections d'appréciations?</p>\n";
	$groups=get_groups_for_class($id_classe);

	include("../lib/periodes.inc.php");

	$date_courante=time();

	$alt=1;
	echo "<table class='boireaus' summary='Tableau des enseignements et périodes'>\n";
	echo "<tr>\n";
	echo "<th>Enseignements</th>\n";
	echo "<th>Classe(s)</th>\n";
	echo "<th>Enseignants</th>\n";
	echo "<th colspan='$nb_periode'>Périodes</th>\n";
	echo "</tr>\n";
	foreach($groups as $current_group)	{
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td style='text-align:left;'>".$current_group['name']." (<span style='font-size:xx-small;'>".$current_group['description']."</span>)</td>\n";

		echo "<td>".$current_group["classlist_string"]."</td>\n";

		echo "<td>\n";
		$sql="SELECT u.login, u.nom, u.prenom, u.civilite FROM utilisateurs u, j_groupes_professeurs j WHERE (u.login = j.login and j.id_groupe = '" . $current_group['id'] . "') ORDER BY u.nom, u.prenom";
		$get_profs=mysql_query($sql);

		$nb = mysql_num_rows($get_profs);
		for ($i=0;$i<$nb;$i++){
			if($i>0) {echo ",<br />\n";}
			$p_login = mysql_result($get_profs, $i, "login");
			$p_nom = mysql_result($get_profs, $i, "nom");
			$p_prenom = mysql_result($get_profs, $i, "prenom");
			$civilite = mysql_result($get_profs, $i, "civilite");
			echo "$civilite $p_nom $p_prenom";
		}
		echo "</td>\n";

		for($i=1;$i<$nb_periode;$i++) {
			if($ver_periode[$i]=='P') {
				//echo "<td><input type='checkbox' name='periode_grp_".$current_group['id']."[]' value='$i' /></td>\n";
				echo "<td>\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;id_groupe=".$current_group['id']."&amp;periode=$i'>Période $i</a>\n";
				$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM matieres_app_delais WHERE id_groupe='".$current_group['id']."' AND periode='$i';";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					$lig=mysql_fetch_object($res);
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
	echo "</table>\n";
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>\n";
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Choisir un autre enseignement de la classe</a>\n";
	echo "</p>\n";

	//if(!isset($is_posted)) {
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
		$group=get_group($id_groupe);
		echo "<p>Vous souhaitez autoriser exceptionnellement un enseignant à proposer des saisies/corrections d'apprécations pour l'enseignement ".$group['name']." (<span style='font-size:x-small;'>".$group['description']." en ".$group['classlist_string']."</span>) en période $periode.</p>\n";

		$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM matieres_app_delais WHERE id_groupe='".$group['id']."' AND periode='$periode';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);
			$date_limite=$lig->date_limite;

			$date_courante=time();

			//$tab_date_limite=get_date($date_limite);
			echo "<p class='bold'>Une autorisation exceptionnelle de proposition de saisie existe pour cet enseignement/période&nbsp;: ".strftime("%d/%m/%Y à %H:%M",$date_limite)."</p>\n";
			$display_date_limite=strftime("%d/%m/%Y",$date_limite);
			$display_heure_limite=strftime("%H:%M",$date_limite);

			if($date_courante>$date_limite) {
				echo "<p class='bold' style='color:red;'>Le délais imparti pour la proposition de saisie/correction est dépassé.</p>\n";
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
		}

		echo "<p>Quelle doit être la date/heure limite de cette autorisation de proposition d'appréciation&nbsp;?<br />\n";
		include("../lib/calendrier/calendrier.class.php");
		$cal = new Calendrier("formulaire", "display_date_limite");

	
		echo "<input type='hidden' name='is_posted' value='y' />\n";
		echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
		echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
		echo "<input type='hidden' name='periode' value='$periode' />\n";
		echo "<input type='text' name = 'display_date_limite' size='8' value = \"".$display_date_limite."\" />\n";
		echo "<a href=\"#\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Calendrier\" /></a>\n";
		echo " à <input type='text' name='display_heure_limite' id='display_heure_limite' size='8' value = \"".$display_heure_limite."\" onKeyDown=\"clavier_heure(this.id,event);\" />\n";
		echo "<input type='submit' name='Valider' value='Valider' />\n";
		echo "</p>\n";
	
		// Mail
	
		echo "</form>\n";

/*
	}
	else {
		// Si le mail n'a pas pu être envoyé, proposer un lien mailto
			$message="Vous avez jusqu'au $date_limite_email pour saisir/corriger une ou des appréciations pour l'enseignement ".$current_group['name']." ($current_group['description']) en période $periode.\n\nCette autorisation est exceptionnelle.\nIl conviendra de veiller à effectuer les saisies dans les temps une prochaine fois.\n";

			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			if($tab_alerte_prof[$login_prof]['email']!="") {
				$sujet_mail="[Gepi]: Appreciations et/ou moyennes manquantes";
				echo "<a href='mailto:".$tab_alerte_prof[$login_prof]['email']."?subject=$sujet_mail&amp;body=".rawurlencode($message)."'>".$info_prof."</a>";
	}
*/
}

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>