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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

//$niveau_arbo=1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Témoin destiné à ne pas enregistrer dans les logs les accès à la page sans être logué.
// Une mauvaise déconnexion peut provoquer énormément d'alertes et de mail (toutes les minutes potentiellement)
$pas_acces_a_une_page_sans_etre_logue="y";

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	if(!isset($_GET['mode_js'])) {
		header("Location: ../logout.php?auto=1");
	}
	die();
}

if (($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
	// Précaution pour éviter une désactivation de compte.
	// Cas vécu: Un professeur fait une démonstration à un élève qui dit "Gepi, ça ne marche pas".
	//           il ouvre une session dans un onglet, mais sans refermer un onglet prof
	//           Les tests de présence de nouveau message dans l'onglet prof finissent par désactiver le compte élève
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_alerte/form_message.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_alerte/form_message.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='V',
autre='F',
description='Dispositif d alerte',
statut='';";
$insert=mysql_query($sql);
}

// Pour éviter des blagues avec le plugin change_compte (sinon les comptes 'autre' sont désactivés en quelques secondes)
//if (!checkAccess()) {
//if ((!checkAccess())&&($_SESSION['statut']!='autre')) {
if (($_SESSION['statut']!='autre')&&(!checkAccess())) {
	// Si in reste sur une page sans se déconnecter, on n'envoie pas, en fin de session, de redir, ni de message par mail du type:
	/*
	** Alerte automatique sécurité Gepi **

	Une nouvelle tentative d'intrusion a été détectée par Gepi. Les détails suivants ont été enregistrés dans la base de données :

	Date : 2013-05-12 17:15:24
	Fichier visé : /mod_alerte/form_message.php
	Url d'origine : https://XXX/*.php
	Niveau de gravité : 1
	Description : Accès à une page sans être logué (peut provenir d'un timeout de session).

	La tentative d'intrusion a été effectuée par un utilisateur non connecté à Gepi.
	Adresse IP : 127.0.0.1
	*/

	// En fait, on n'arrive même pas jusque là si la session est terminée.

	//header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('active_mod_alerte')) {
	$active_messagerie=getSettingValue('active_messagerie');
	if($active_messagerie!="") {
		saveSetting('active_mod_alerte', $active_messagerie);
	}

	if(!getSettingAOui('active_mod_alerte')) {
		$mess=rawurlencode("Vous tentez d accéder au dispositif d'alerte qui est désactivé !");
		tentative_intrusion(1, "Tentative d'accès au dispositif d'alerte qui est désactivé.");
		header("Location: ../accueil.php?msg=$mess");
		die();
	}
}

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

if((isset($mode))&&($mode=='maj_span_nom_jour_semaine')) {
	header('Content-Type: text/html; charset=utf-8');

	$jour=isset($_GET['jour']) ? $_GET['jour'] : NULL;
	//if(isset($jour)) {
	if((isset($jour))&&(preg_match("|[0-9]{2}/[0-9]{2}/[0-9]{4}|", $jour))) {
		$tmp_tab=explode("/", $jour);
		if(checkdate($tmp_tab[1],$tmp_tab[0],$tmp_tab[2])) {
			echo strftime("%A", mktime ("12", "59" , "00" , $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]));
		}
	}
	die();
}

// Test de la présence de messages non lus et mise à jour du témoin en barre d'entête
if((isset($mode))&&($mode=='check')) {
	$messages_non_lus=check_messages_recus($_SESSION['login']);
	if($messages_non_lus!="") {
		echo "<a href='$gepiPath/mod_alerte/form_message.php?mode=afficher_messages_non_lus' target='_blank'><img src='$gepiPath/images/icons/new_mail.gif' width='16' height='16' alt='Nouveau mail' title='Vous avez $messages_non_lus' /></a>";
		if((getSettingAOui('MessagerieAvecSon'))&&(!isset($_GET['sound']))) {
			$AlertesAvecSon=getPref($_SESSION['login'], "AlertesAvecSon","y");
			if((!getSettingAOui("PeutChoisirAlerteSansSon".ucfirst($_SESSION['statut'])))||
			((getSettingAOui("PeutChoisirAlerteSansSon".ucfirst($_SESSION['statut'])))&&($AlertesAvecSon=="y"))) {
				//echo joueSon('pluck.wav',"","1");
				echo joueSon('pluck.wav',"");
			}
		}
	}
	else {
		$sql="SELECT 1=1 FROM messagerie WHERE login_dest='".$_SESSION['login']."' OR login_src='".$_SESSION['login']."' ;";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			echo "<span id='span_messages_recus'><a href='$gepiPath/mod_alerte/form_message.php' target='_blank'><img src='$gepiPath/images/icons/no_mail.png' width='16' height='16' title='Aucun message' alt='Aucun message' /></a></span>";
		}
		else {
			echo "<img src='$gepiPath/images/icons/no_mail.png' width='16' height='16' title='Aucun message' alt='Aucun message' />";
		}
	}
	die();
}

if((isset($mode))&&($mode=='check2')) {
	$messages_non_lus=check_messages_recus($_SESSION['login']);
	if($messages_non_lus!="") {
		$MessagerieLargeurImg=getSettingValue('MessagerieLargeurImg');
		//echo "<a href='$gepiPath/mod_alerte/form_message.php?mode=afficher_messages_non_lus' target='_blank'><img src='$gepiPath/images/icons/new_mail.gif' width='$MessagerieLargeurImg' height='$MessagerieLargeurImg' title='Vous avez $messages_non_lus' /></a>";
		echo "<a href='$gepiPath/mod_alerte/form_message.php?mode=afficher_messages_non_lus' target='_blank'><img src='$gepiPath/images/icons/temoin_message_non_lu.gif' width='$MessagerieLargeurImg' height='$MessagerieLargeurImg' title='Vous avez $messages_non_lus non lus' alt='$messages_non_lus  message(s) non lus' /></a>";
	}
	else {
		echo "";
	}
	die();
}

// Marquer un message comme lu
if((isset($mode))&&($mode=='marquer_lu')) {
	check_token();
	$id_msg=$_GET['id_msg'];
	if(is_numeric($id_msg)) {
		$retour=marquer_message_lu($id_msg);
		if(!isset($_GET['mode_no_js'])) {
			if($retour=="Erreur") {
				//echo "<img src='../images/disabled.png' width='20' height='20' title='Lu/vu' />";
				echo "<span style='color:red'>Erreur</span>";
			}
			else {
				echo "<img src='../images/enabled.png' width='20' height='20' title='Lu/vu' alt='Lu/vu' />";
			}
		}
		else {
			if($retour=="Erreur") {
				echo "<span style='color:red'>Erreur</span>";
			}
			else {
				echo "Message marqué comme lu.<br />Vous pouvez refermer cette page.";
				// Il faudrait trouver une meilleure façon de gérer le marquage quand JS est inactif.
			}
		}
	}
	die();
}

// Relancer un message : le marquer comme non-lu
if((isset($mode))&&($mode=='relancer')) {
	check_token();
	$id_msg=$_GET['id_msg'];

	if(is_numeric($id_msg)) {
		$retour=marquer_message_lu($id_msg, false);
		if(!isset($_GET['mode_no_js'])) {
			if($retour=="Erreur") {
				//echo "<img src='../images/disabled.png' width='20' height='20' title='Lu/vu' />";
				echo "<span style='color:red'>Erreur</span>";
			}
			else {
				echo "<img src='../images/disabled.png' width='20' height='20' title='Lu/vu' alt='Lu/vu' />";
			}
		}
		else {
			if($retour=="Erreur") {
				echo "<span style='color:red'>Erreur</span>";
			}
			else {
				echo "Message marqué comme non lu.<br />Vous pouvez refermer cette page.";
				// Il faudrait trouver une meilleure façon de gérer le marquage quand JS est inactif.
			}
		}
	}
	die();
}

// Clore un message
if((isset($mode))&&($mode=='clore')) {
	check_token();
	$id_msg=$_GET['id_msg'];

	if(is_numeric($id_msg)) {
		$retour=clore_declore_message($id_msg);
		if(!isset($_GET['mode_no_js'])) {
			if($retour=="Erreur") {
				//echo "<img src='../images/disabled.png' width='20' height='20' title='Lu/vu' />";
				echo "<span style='color:red'>Erreur</span>";
			}
			elseif($retour==2) {
				echo "<img src='../images/icons/securite.png' width='16' height='16' title='Message clos/traité.' alt='Message clos/traité' />";
			}
			else {
				echo "<img src='../images/disabled.png' width='20' height='20' title='Non lu/vu' alt='Non lu/vu' />";
			}
		}
		else {
			if($retour=="Erreur") {
				echo "<span style='color:red'>Erreur</span>";
			}
			else {
				echo "Message marqué comme non lu.<br />Vous pouvez refermer cette page.";
				// Il faudrait trouver une meilleure façon de gérer le marquage quand JS est inactif.
			}
		}
	}
	die();
}

if((isset($mode))&&($mode=='affiche_messages_recus')) {
	$tri=isset($_GET['tri']) ? $_GET['tri'] : "date";
	if(!in_array($tri, array("date", "source", "sujet", "vu"))) {$tri="date";}

	$mode_affiche_historique_messages_recus=isset($_GET['mode_affiche_historique_messages_recus']) ? $_GET['mode_affiche_historique_messages_recus'] : "tous";
	if(!in_array($mode_affiche_historique_messages_recus, array("tous", "non_lus"))) {$mode_affiche_historique_messages_recus="tous";}

	echo affiche_historique_messages_recus($_SESSION['login'], $mode_affiche_historique_messages_recus, $tri);
	die;
}

if((isset($mode))&&($mode=='affiche_messages')) {
	$tri=isset($_GET['tri']) ? $_GET['tri'] : "date";
	if(!in_array($tri, array("date", "source", "sujet", "vu"))) {$tri="date";}

	$mode_affiche_historique_messages=isset($_GET['mode_affiche_historique_messages']) ? $_GET['mode_affiche_historique_messages'] : "tous";
	if(!in_array($mode_affiche_historique_messages, array("tous", "non_lus"))) {$mode_affiche_historique_messages="tous";}

	echo affiche_historique_messages($_SESSION['login'], $mode_affiche_historique_messages, $tri);
	die;
}

// Envoi de message
$message_envoye=isset($_POST['message_envoye']) ? $_POST['message_envoye'] : (isset($_GET['message_envoye']) ? $_GET['message_envoye'] : "n");

$sujet=isset($_POST['sujet']) ? $_POST['sujet'] : (isset($_GET['sujet']) ? $_GET['sujet'] : NULL);
$message=isset($_POST['message']) ? $_POST['message'] : (isset($_GET['message']) ? $_GET['message'] : NULL);
$date_visibilite=isset($_POST['date_visibilite']) ? $_POST['date_visibilite'] : (isset($_GET['date_visibilite']) ? $_GET['date_visibilite'] : NULL);
$heure_visibilite=isset($_POST['heure_visibilite']) ? $_POST['heure_visibilite'] : (isset($_GET['heure_visibilite']) ? $_GET['heure_visibilite'] : NULL);

if (($message_envoye=='y')&&(peut_poster_message($_SESSION['statut']))) {
	check_token();

	$login_dest=isset($_POST['login_dest']) ? $_POST['login_dest'] : (isset($_GET['login_dest']) ? $_GET['login_dest'] : NULL);

	$msg="";

	$date_heure_visibilite="";
	if(isset($date_visibilite)) {
		$tmp_tab=explode("/", $date_visibilite);
		if(!checkdate($tmp_tab[1],$tmp_tab[0],$tmp_tab[2])) {
			$msg.="Erreur sur la date de visibilité proposée $date_visibilite<br />";
		}
		else {
			// On teste maintenant l'heure
			if((!preg_match("/^[0-9]{1,2}:[0-9]{1,2}$/", $heure_visibilite))&&(!preg_match("/^[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/", $heure_visibilite))) {
				if((!preg_match("/[0-9]{1,2}", $heure_visibilite))||($heure_visibilite<0)||($heure_visibilite>23)) {
					$msg.="Erreur sur l'heure de visibilité proposée $heure_visibilite<br />";
				}
				else {
					$date_heure_visibilite=$tmp_tab[2].":".$tmp_tab[1].":".$tmp_tab[0]." ".$heure_visibilite.":00:00";
				}
			}
			else {
				$tmp_tab2=explode(":", $heure_visibilite);
				if(($tmp_tab2[0]<0)||($tmp_tab2[0]>23)||($tmp_tab2[1]<0)||($tmp_tab2[1]>59)) {
					$msg.="Erreur sur l'heure de visibilité proposée $heure_visibilite<br />";
				}
				else {
					$date_heure_visibilite=$tmp_tab[2].":".$tmp_tab[1].":".$tmp_tab[0]." ".$tmp_tab2[0].":".$tmp_tab2[1].":00";
				}
			}
		}
	}

	if((isset($login_dest))&&(isset($sujet))&&(isset($message))) {

		unset($in_reply_to);
		if((isset($_POST['in_reply_to']))&&(is_numeric($_POST['in_reply_to']))) {
			$sql="SELECT 1=1 FROM messagerie WHERE id='".$_POST['in_reply_to']."' AND login_dest='".$_SESSION['login']."';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$in_reply_to=$_POST['in_reply_to'];
			}
		}

		if(is_array($login_dest)) {
			$tmp_login_dest=$login_dest;
			$login_dest=array_unique($tmp_login_dest);
			$nb_reg=0;
			//for($loop=0;$loop<count($login_dest);$loop++) {
				//$retour=enregistre_message($sujet, $message, $_SESSION['login'], $login_dest[$loop], $date_heure_visibilite);
			foreach($login_dest as $key => $value) {
				if(isset($in_reply_to)) {
					$retour=enregistre_message($sujet, $message, $_SESSION['login'], $value, $date_heure_visibilite,$in_reply_to);
				}
				else {
					$retour=enregistre_message($sujet, $message, $_SESSION['login'], $value, $date_heure_visibilite);
				}

				if($retour!="") {
					$nb_reg++;
				}
				else {
					//$msg.="Erreur lors de l'enregistrement du message pour ".civ_nom_prenom($login_dest[$loop]).".<br />";
					$msg.="Erreur lors de l'enregistrement du message pour ".civ_nom_prenom($value).".<br />";
				}
			}
			$msg.="Message enregistré pour $nb_reg destinataire(s).<br />";
		}
		elseif(($login_dest!='')&&($sujet!='')&&($message!='')) {
			if(isset($in_reply_to)) {
				$retour=enregistre_message($sujet, $message, $_SESSION['login'], $login_dest, $date_heure_visibilite,$in_reply_to);
			}
			else {
				$retour=enregistre_message($sujet, $message, $_SESSION['login'], $login_dest, $date_heure_visibilite);
			}

			if($retour!="") {
				$msg.="Message pour ".civ_nom_prenom($login_dest)." enregistré.<br />";

				if(isset($_GET['envoi_js'])) {
					echo "<img src='$gepiPath/images/icons/mail_succes.png' width='16' height='16' title='Message envoyé' alt='Message envoyé' />";
					die();
				}
			}
			else {
				$msg.="Erreur lors de l'enregistrement du message pour ".civ_nom_prenom($login_dest).".<br />";

				if(isset($_GET['envoi_js'])) {
					echo "<img src='$gepiPath/images/icons/mail_echec.png' width='16' height='16' title='Erreur lors de l envoi du message' alt='Erreur' />";
					die();
				}
			}
		}

		unset($in_reply_to);
	}
}

if((isset($mode))&&($mode=='repondre')) {
	check_token();

	$id_msg=$_GET['id_msg'];
	if(is_numeric($id_msg)) {
		$sql="SELECT * FROM messagerie WHERE id='$id_msg' AND login_dest='".$_SESSION['login']."';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			$msg.="Le message n°$id_msg s'il existe ne vous était pas destiné.<br />";
		}
		else {
			$retour=marquer_message_lu($id_msg);

			$login_dest=mysql_result($res,0,"login_src");
			$sujet="Re: ".mysql_result($res,0,"sujet");

			//$date_visibilite=mysql_result($res,0,"date_visibilite");
			$date_msg=mysql_result($res,0,"date_msg");

			$message="Le ".formate_date($date_msg, 'y').", vous avez écrit:\n================================\n".mysql_result($res,0,"message")."\n================================\n";

			$in_reply_to=$id_msg;
		}
	}
}

$themessage = 'Un message est en cours de rédaction. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";
$utilisation_prototype = "ok";
//**************** EN-TETE *****************
$titre_page = "Alertes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class='bold'><a href='../accueil.php'>Retour à l'accueil</a> ";
if(((!isset($mode))||($mode!='rediger_message'))&&(peut_poster_message($_SESSION['statut']))) {
	echo " | <a href='".$_SERVER['PHP_SELF']."?mode=rediger_message'>Rédiger un message</a>";
}

$sql="SELECT 1=1 FROM messagerie WHERE login_src='".$_SESSION['login']."';";
$test=mysql_query($sql);
if(mysql_num_rows($test)>0) {
	echo " | <a href='".$_SERVER['PHP_SELF']."#messages_envoyes'>Tous mes envois</a>";
}

$sql="SELECT 1=1 FROM messagerie WHERE login_dest='".$_SESSION['login']."';";
$test=mysql_query($sql);
if(mysql_num_rows($test)>0) {
	echo " | <a href='".$_SERVER['PHP_SELF']."#messages_recus' title='Lus/vus ou non'>Tous mes messages reçus</a>";
}
echo "</p>";

//debug_var();

$messages_non_lus=check_messages_recus($_SESSION['login']);
if($messages_non_lus!="") {
	//echo "<p>Vous avez <a href='#messages_recus'>$messages_non_lus</a></p>";
	echo "<p>Vous avez <a href='".$_SERVER['PHP_SELF']."?mode=afficher_messages_non_lus' onclick=\"return confirm_abandon (this, change, '$themessage')\">$messages_non_lus</a></p>";
}

if((isset($mode))&&($mode=='afficher_messages_non_lus')) {

	echo "<div id='div_messages_recus'>
".affiche_historique_messages_recus($_SESSION['login'], 'non_lus')."
</div>
<p><br /></p>
<p style='text-indent:-4em; margin-left:4em;'><em style='color:red'>NOTE&nbsp;:</em> Pour faire cesser l'alerte, il faut cliquer sur les croix rouges.<br />Le test de présence de messages non lus n'est effectué que toutes les ".getSettingValue('MessagerieDelaisTest')."min.<br />".getSettingValue('MessagerieDelaisTest')."min après que vous ayez cliqué, l'alerte disparaitra donc.</p>";
	require("../lib/footer.inc.php");
	die();
}

if(peut_poster_message($_SESSION['statut'])) {
?>

<form action='../mod_alerte/form_message.php' method='post' name='formulaire'>
	<fieldset style='border:1px solid grey; background-image: url("../images/background/opacite50.png");'>
		<legend style='border:1px solid grey; background-image: url("../images/background/opacite50.png");'>Formulaire de rédaction d'un message/alerte</legend>
		<?php
			echo add_token_field(true);
		?>
		<p class='bold'>Envoi d'un message/post-it&nbsp;: 
		<?php
			if(isset($in_reply_to)) {
				echo "<span style='color:red'>Réponse à un autre message</span>";
				echo "<input type='hidden' name='in_reply_to' value='$in_reply_to' />\n";
			}
		?>
		</p>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th>Destinataire(s)</th>
				<td style='text-align:left;'>
					<!-- ======================================================= -->
					<!-- Balises concernant JavaScript -->
					<div id='p_ajout_dest_js' style='display:none;float:right;whidth:16px;'><a href="javascript:affiche_ajout_dest();"><img src='../images/icons/add.png' width='16' height='16' title='Ajouter un ou des destinataires' alt='Ajouter' /></a></div>

					<div id='div_login_dest_js'>
						<span style='color:red' id='span_ajoutez_un_ou_des_destinataires'><a href='javascript:affiche_ajout_dest();' style='color:red'>Ajoutez un ou des destinataires --&gt;</a></span>
						<?php
							if(isset($login_dest)) {
								if(is_array($login_dest)) {
									/*
									echo "<pre>";
									print_r($login_dest);
									echo "</pre>";
									*/
									//for($loop=0;$loop<count($login_dest);$loop++) {
									$loop=0;
									foreach($login_dest as $key => $value) {
										// Avec l'identifiant spécial, on peut se retrouver, en ajoutant des destinataires, à avoir deux fois un même destinataire.
										echo "<br /><span id='span_login_u_choisi_special_$loop'>";
										//echo "<input type='hidden' name='login_dest[]' value='".$login_dest[$loop]."' />";
										//echo civ_nom_prenom($login_dest[$loop]);
										echo "<input type='hidden' name='login_dest[]' value='".$value."' />";
										echo civ_nom_prenom($value);
										echo " <a href=\"javascript:removeElement('span_login_u_choisi_special_$loop')\"><img src='../images/icons/delete.png' width='16' height='16' alt='Enlever' /></a></span>";
										$loop++;
									}
								}
								else {
									echo "<br /><span id='span_login_u_choisi_special'>";
									echo "<input type='hidden' name='login_dest[]' value='".$login_dest."' />";
									echo civ_nom_prenom($login_dest);
									echo " <a href=\"javascript:removeElement('span_login_u_choisi_special')\"><img src='../images/icons/delete.png' width='16' height='16' alt='Enlever' /></a></span>";
								}
							}
						?>
					</div>
					<!-- ======================================================= -->
					<!-- Balises concernant JavaScript inactif -->
					<div id='div_select_no_js'>
						<select name='login_dest[]' onchange='changement()' multiple size='6'>
							<?php
								// Cela donne la possibilité à un utilisateur de découvrir le login des autres comptes... pas génial.
								$tab_statut=array('professeur', 'scolarite', 'cpe', 'administrateur');
								for($loop=0;$loop<count($tab_statut);$loop++) {
									$sql="SELECT * FROM utilisateurs WHERE etat='actif' AND statut='".$tab_statut[$loop]."' ORDER BY nom, prenom";
									$res_u=mysql_query($sql);
									if(mysql_num_rows($res_u)>0) {
										echo "
							<optgroup label='".$tab_statut[$loop]."'>";
										while($lig_u=mysql_fetch_object($res_u)) {
											echo "
								<option value='$lig_u->login'";
											if(isset($login_dest)) {
												if(is_array($login_dest)) {
													if(in_array($lig_u->login, $login_dest)) {
														echo " selected";
													}
												}
												else {
													if($lig_u->login==$login_dest) {
														echo " selected";
													}
												}
											}
											echo ">$lig_u->civilite ".casse_mot($lig_u->nom, 'maj')." ".casse_mot($lig_u->prenom, 'majf2')."</option>";
										}
										echo "
							</optgroup>";
									}
								}
							?>
						</select>
					</div>
					<!-- ======================================================= -->
					<script type='text/javascript'>
						if(document.getElementById('p_ajout_dest_js')) {document.getElementById('p_ajout_dest_js').style.display='';}
						if(document.getElementById('div_login_dest_js')) {document.getElementById('div_login_dest_js').style.display='';}
						if(document.getElementById('div_select_no_js')) {document.getElementById('div_select_no_js').style.display='none';}

						function affiche_ajout_dest() {
							if(document.getElementById('div_choix_dest')) {
								afficher_div('div_choix_dest','y',10,-40);
							}
							else {
								alert('Erreur');
							}
						}
					</script>
					<!-- ======================================================= -->
				</td>
			</tr>
			<tr>
				<th><label for='sujet'>Sujet</label></th>
				<td><input type='text' name='sujet' id='sujet' size='40' value="<?php
					if(isset($sujet)) {
						echo $sujet;
					}
				?>" onchange='changement()' /></td>
			</tr>
			<tr>
				<th><label for='message_messagerie'>Message</label></th>
				<td><textarea id='message_messagerie' name='message' cols='50' rows='5' onchange='changement()'><?php
					if(isset($message)) {
						echo stripslashes(preg_replace("/\\\\n/", "\n", $message));
					}
				?></textarea></td>
			</tr>
			<tr>
				<th title="Une date de visibilité permet par exemple aux cpe/surveillants de saisir en fin de journée un message destiné aux professeurs de telle classe pour qu'ils envoient au bureau CPE tel élève le lendemain à 8h.
Avec une date de visibilité pour le lendemain, le message ne dérangera pas les professeurs pendant qu'ils saisissent des notes ou leur cahier de textes la veille.
Ils risqueraient de cocher le message comme vu la veille et d'oublier le lendemain d'envoyer l'élève au bureau.">Visible à compter du</th>
				<td>
					<?php
						include("../lib/calendrier/calendrier.class.php");
						$cal = new Calendrier("formulaire", "date_visibilite");
						if(!isset($date_visibilite)) {$date_visibilite=strftime("%d/%m/%Y");}
						if(!isset($heure_visibilite)) {$heure_visibilite=strftime("%H:%M");}
						else {
							if(preg_match("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/", $heure_visibilite)) {
								$tmp_tab=explode(":", $heure_visibilite);
								$heure_visibilite=$tmp_tab[0].":".$tmp_tab[1];
							}
						}
					?>
					<div style='float:right; width:16px;'><a href='javascript:date_visibilite_maintenant()' title="Fixer la date/heure de visibilité à l'instant présent."><img src='../images/icons/wizard.png' width='16' height='16' alt="Instant présent" /></a></div>
					<span id='span_nom_jour_semaine'></span> 
					<input type='text' name='date_visibilite' id='date_visibilite' size='10' value = "<?php echo $date_visibilite;?>" onKeyDown="clavier_date(this.id,event);maj_span_nom_jour_semaine();" AutoComplete="off" title="Vous pouvez modifier la date à l'aide des flèches Up et Down du pavé de direction." onchange="changement();maj_span_nom_jour_semaine();" onblur="maj_span_nom_jour_semaine();" />
					<a href="#calend" onClick="<?php echo $cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170);?>;document.getElementById('span_nom_jour_semaine').innerHTML='';"
					><img src="../lib/calendrier/petit_calendrier.gif" border="0" alt="Petit calendrier" /></a>
					à
					<input name="heure_visibilite" value="<?php echo $heure_visibilite;?>" type="text" maxlength="5" size="4" id="heure_visibilite" onKeyDown="clavier_heure2(this.id,event,1,30);" AutoComplete="off" title="Vous pouvez modifier l'heure à l'aide des flèches Up et Down du pavé de direction et les flèches PageUp/PageDown." />
				</td>
			</tr>
		</table>
		<input type='hidden' name='message_envoye' value='y' />
		<p><input type='submit' name='envoyer' value='Envoyer' /></p>
	</fieldset>
</form>
<p><br /></p>

<?php
$titre_infobulle="Choix des destinataires";
$texte_infobulle="<p>Cochez les destinataires de votre message et validez.</p>";
$tab_statut=array('professeur', 'scolarite', 'cpe', 'administrateur');
$cpt_u=0;
$chaine_js_login_u="var login_u=new Array(";
$chaine_js_designation_u="var designation_u=new Array(";
$chaine_prof_classe="";
for($loop=0;$loop<count($tab_statut);$loop++) {
	$sql="SELECT * FROM utilisateurs WHERE etat='actif' AND statut='".$tab_statut[$loop]."' ORDER BY nom, prenom";
	$res_u=mysql_query($sql);
	if(mysql_num_rows($res_u)>0) {
		$texte_infobulle.="<br /><p class='bold'><a href=\"javascript:cocher_decocher_statut('$tab_statut[$loop]')\">".ucfirst($tab_statut[$loop])."</a>";

		if($tab_statut[$loop]=='professeur') {
			//$chaine_prof_classe="";
			$sql="SELECT c.id, c.classe FROM classes c ORDER BY classe;";
			$res_classe=mysql_query($sql);
			if(mysql_num_rows($res_classe)>0) {
				$texte_infobulle.=" de <select name='id_classe' id='id_classe' onchange='coche_prof_de_la_classe()'><option value=''>---</option>";
				while($lig_classe=mysql_fetch_object($res_classe)) {
					$texte_infobulle.="<option value='$lig_classe->id'>$lig_classe->classe</option>";

					$chaine_prof_classe.="var prof_classe_".$lig_classe->id."=new Array(";
					$sql="SELECT DISTINCT login FROM j_groupes_professeurs jgp, j_groupes_classes jgc WHERE jgc.id_classe='$lig_classe->id' AND jgc.id_groupe=jgp.id_groupe;";
					$res_prof=mysql_query($sql);
					$cpt_prof=0;
					if(mysql_num_rows($res_prof)>0) {
						while($lig_prof=mysql_fetch_object($res_prof)) {
							if($cpt_prof>0) {
								$chaine_prof_classe.=", ";
							}
							$chaine_prof_classe.="'$lig_prof->login'";
							$cpt_prof++;
						}
					}
					$chaine_prof_classe.=");";
				}
				$texte_infobulle.="</select>";
			}
		}

		$texte_infobulle.=" <input type='button' value='Ajouter' onclick=\"ajouter_dest_choisis(); cacher_div('div_choix_dest')\"></p>";
		$texte_infobulle.="<div style='margin-left:1em;'><table class='boireaus boireaus_alt'>";

		while($lig_u=mysql_fetch_object($res_u)) {
			$designation_u="$lig_u->civilite ".casse_mot($lig_u->nom, 'maj')." ".casse_mot($lig_u->prenom, 'majf2');
			$texte_infobulle.="<tr class='white_hover'><td style='text-align:left'><input type='checkbox' name='login_dest[]' id='login_dest_$cpt_u' value='$lig_u->login' onchange=\"checkbox_change('login_dest_$cpt_u')\" attribut_statut=\"".$tab_statut[$loop]."\"><label for='login_dest_$cpt_u' id='texte_login_dest_$cpt_u'>$designation_u</label></td></tr>";
			$chaine_js_login_u.="'$lig_u->login',";
			$chaine_js_designation_u.="'".preg_replace("/'/", " ", $designation_u)."',";
			$cpt_u++;
		}
		$texte_infobulle.="</table></div>";
	}
}
if($cpt_u>0) {
	$chaine_js_login_u=substr($chaine_js_login_u,0,-1);
	$chaine_js_designation_u=substr($chaine_js_designation_u,0,-1);
}
$chaine_js_login_u.=");";
$chaine_js_designation_u.=");";
$texte_infobulle.="<p style='text-align:center;'><input type='button' value='Ajouter' onclick=\"ajouter_dest_choisis(); cacher_div('div_choix_dest')\"></p><p><br /></p>";
$tabdiv_infobulle[]=creer_div_infobulle("div_choix_dest",$titre_infobulle,"",$texte_infobulle,"",30,0,'y','y','n','n');

?>
<script type='text/javascript'>
	<?php
		echo js_checkbox_change_style('checkbox_change', 'texte_', 'n');

		echo $chaine_js_login_u;
		echo $chaine_js_designation_u;
		echo $chaine_prof_classe;
	?>

	function ajouter_dest_choisis() {
		if(document.getElementById('div_login_dest_js')) {
			for(i=0;i<<?php echo $cpt_u;?>;i++) {
				if(document.getElementById('login_dest_'+i)) {
					if(document.getElementById('login_dest_'+i).checked==true) {
						document.getElementById('div_login_dest_js').innerHTML=document.getElementById('div_login_dest_js').innerHTML+"<br /><span id='span_login_u_choisi_"+i+"'><input type='hidden' name='login_dest[]' value='"+login_u[i]+"' />"+designation_u[i]+" <a href=\"javascript:removeElement('span_login_u_choisi_"+i+"')\"><img src='../images/icons/delete.png' width='16' height='16' alt='Enlever' /></a></span>";

						// On décoche les cases pour que si on ajoute par la suite d'autres destinataires,
						// ils ne soient pas pré-sélectionnés, au risque de faire apparaitre des doublons.
						document.getElementById('login_dest_'+i).checked=false;
						checkbox_change('login_dest_'+i);

						if(document.getElementById('id_classe')) {document.getElementById('id_classe').selectedIndex=0;}

						// Masquage du texte initial d'ajout de destinataires
						if(document.getElementById('span_ajoutez_un_ou_des_destinataires')) {document.getElementById('span_ajoutez_un_ou_des_destinataires').style.display='none';}
					}
				}
			}
		}
		window.scrollTo(0,0);
	}
	
	function cocher_decocher_statut(statut) {
		var etat_souhaite="";
		for(i=0;i<<?php echo $cpt_u;?>;i++) {
			if(document.getElementById('login_dest_'+i)) {
				input_courant=document.getElementById('login_dest_'+i)
				if(input_courant.getAttribute('attribut_statut')==statut) {
					if(etat_souhaite=='') {
						if(input_courant.checked==true) {
							etat_souhaite=false;
						}
						else {
							etat_souhaite=true;
						}
						input_courant.checked=etat_souhaite;
					}
					else {
						input_courant.checked=etat_souhaite;
					}
					checkbox_change('login_dest_'+i);
				}
			}
		}
	}

	function removeElement(id) {
		element = document.getElementById(id);
		element.parentNode.removeChild(element);
	}

	function coche_prof_de_la_classe() {
		id_classe=document.getElementById('id_classe').options[document.getElementById('id_classe').selectedIndex].value;
		if(id_classe!='') {
			//alert(id_classe);
			tab=eval('prof_classe_'+id_classe);
			//alert(tab.length);
			for(i=0;i<<?php echo $cpt_u;?>;i++) {
				if(document.getElementById('login_dest_'+i)) {
					for(j=0;j<tab.length;j++) {
						if(tab[j]==document.getElementById('login_dest_'+i).value) {
							document.getElementById('login_dest_'+i).checked=true;
							checkbox_change('login_dest_'+i);
						}
					}
				}
			}
		}
	}

	function maj_span_nom_jour_semaine() {
		if(document.getElementById('date_visibilite')) {
			jour_visibilite=document.getElementById('date_visibilite').value;
			//alert(jour_visibilite);
			new Ajax.Updater($('span_nom_jour_semaine'),'form_message.php?mode=maj_span_nom_jour_semaine&jour='+jour_visibilite,{method: 'get'});
		}
	}

	maj_span_nom_jour_semaine();
	//setTimeout('maj_span_nom_jour_semaine()', 3000);

	function date_visibilite_maintenant() {
		maintenant = new Date();
		document.getElementById('date_visibilite').value = maintenant.getDate()+'/'+(maintenant.getMonth()+1)+'/'+maintenant.getFullYear();
		document.getElementById('heure_visibilite').value = maintenant.getHours()+':'+maintenant.getMinutes();
		delete (maintenant);
	}

</script>

<?php
} // Fin du test PeutPosterMessage<statut>
?>
<!-- ======================================================= -->

<a name='messages_envoyes'></a>
<p class='bold'>Historique de vos messages envoyés&nbsp;:</p>
<!--p style='color:red'>ENCORE A FAIRE : Pouvoir afficher/masquer les messages<br />N'afficher par défaut que les messages des 7 derniers jours,...</p-->
<!--div style='margin-left:3em; height:30em; maxheight:30em; overflow:auto;'-->
<?php
	$sql="SELECT 1=1 FROM messagerie WHERE login_src='".$_SESSION['login']."';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)<=2) {
		echo "<div style='margin-left:3em; height:6em; maxheight:10em; overflow:auto;'>\n";
	}
	elseif(mysql_num_rows($test)<=4) {
		echo "<div style='margin-left:3em; height:10em; maxheight:10em; overflow:auto;'>\n";
	}
	else {
		echo "<div style='margin-left:3em; height:30em; maxheight:30em; overflow:auto;'>\n";
	}
	echo "<div id='div_messages_envoyes'>\n";
	echo affiche_historique_messages($_SESSION['login']);
?>
</div>
</div>

<!-- ======================================================= -->

<a name='messages_recus'></a>
<p class='bold'>Historique de vos messages reçus&nbsp;:</p>
<!--p style='color:red'>ENCORE A FAIRE : Pouvoir afficher/masquer les messages<br />N'afficher par défaut que les messages des 7 derniers jours,...</p-->
<div style='margin-left:3em; height:30em; maxheight:30em; overflow:auto;'>
	<div id='div_messages_recus'>
	<?php
		echo affiche_historique_messages_recus($_SESSION['login']);
	?>
	</div>
</div>

<!-- ======================================================= -->

<p><br /></p>
<?php
	require("../lib/footer.inc.php");
?>

