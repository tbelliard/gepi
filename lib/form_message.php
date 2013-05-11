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

$sql="SELECT 1=1 FROM droits WHERE id='/lib/form_message.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/lib/form_message.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='V',
autre='F',
description='Messagerie',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('active_messagerie')) {
	$mess=rawurlencode("Vous tentez d accéder au module Messagerie qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Messagerie qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

// Test de la présence de messages non lus et mise à jour du témoin en barre d'entête
if((isset($mode))&&($mode=='check')) {
	$messages_non_lus=check_messages_recus($_SESSION['login']);
	if($messages_non_lus!="") {
		echo "<a href='$gepiPath/lib/form_message.php?mode=afficher_messages_non_lus' target='_blank'><img src='$gepiPath/images/icons/new_mail.gif' width='16' height='16' title='Vous avez $messages_non_lus' /></a>";
		if((getSettingAOui('MessagerieAvecSon'))&&(!isset($_GET['sound']))) {
			echo joueSon('pluck.wav',"","1");
		}
	}
	else {
		$sql="SELECT 1=1 FROM messagerie WHERE login_dest='".$_SESSION['login']."' OR login_src='".$_SESSION['login']."';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			echo "<span id='span_messages_recus'><a href='$gepiPath/lib/form_message.php' target='_blank'><img src='$gepiPath/images/icons/no_mail.png' width='16' height='16' title='Aucun message' /></a></span>";
		}
		else {
			echo "<img src='$gepiPath/images/icons/no_mail.png' width='16' height='16' title='Aucun message' />";
		}
	}
	die();
}

if((isset($mode))&&($mode=='check2')) {
	$messages_non_lus=check_messages_recus($_SESSION['login']);
	if($messages_non_lus!="") {
		echo "<a href='$gepiPath/lib/form_message.php?mode=afficher_messages_non_lus' target='_blank'><img src='$gepiPath/images/icons/new_mail.gif' width='16' height='16' title='Vous avez $messages_non_lus' /></a>";
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
				echo "<img src='../images/enabled.png' width='20' height='20' title='Lu/vu' />";
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

// Marquer un message comme lu
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
				echo "<img src='../images/disabled.png' width='20' height='20' title='Lu/vu' />";
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

// Envoi de message
$message_envoye=isset($_POST['message_envoye']) ? $_POST['message_envoye'] : (isset($_GET['message_envoye']) ? $_GET['message_envoye'] : "n");

$sujet=isset($_POST['sujet']) ? $_POST['sujet'] : (isset($_GET['sujet']) ? $_GET['sujet'] : NULL);
$message=isset($_POST['message']) ? $_POST['message'] : (isset($_GET['message']) ? $_GET['message'] : NULL);

if (($message_envoye=='y')&&(peut_poster_message($_SESSION['statut']))) {
	check_token();

	$login_dest=isset($_POST['login_dest']) ? $_POST['login_dest'] : (isset($_GET['login_dest']) ? $_GET['login_dest'] : NULL);

	$msg="";

	if((isset($login_dest))&&(isset($sujet))&&(isset($message))) {
		if(is_array($login_dest)) {
			$tmp_login_dest=$login_dest;
			$login_dest=array_unique($tmp_login_dest);
			$nb_reg=0;
			for($loop=0;$loop<count($login_dest);$loop++) {
				$retour=enregistre_message($sujet, $message, $_SESSION['login'], $login_dest[$loop]);
				if($retour!="") {
					$nb_reg++;
				}
				else {
					$msg.="Erreur lors de l'enregistrement du message pour ".civ_nom_prenom($login_dest[$loop]).".<br />";
				}
			}
			$msg.="Message enregistré pour $nb_reg destinataire(s).<br />";
		}
		elseif(($login_dest!='')&&($sujet!='')&&($message!='')) {
			$retour=enregistre_message($sujet, $message, $_SESSION['login'], $login_dest);
			if($retour!="") {
				$msg.="Message pour ".civ_nom_prenom($login_dest)." enregistré.<br />";

				if(isset($_GET['envoi_js'])) {
					echo "<img src='$gepiPath/images/icons/mail_succes.png' width='16' height='16' title='Message envoyé' />";
					die();
				}
			}
			else {
				$msg.="Erreur lors de l'enregistrement du message pour ".civ_nom_prenom($login_dest).".<br />";

				if(isset($_GET['envoi_js'])) {
					echo "<img src='$gepiPath/images/icons/mail_echec.png' width='16' height='16' title='Erreur lors de l envoi du message' />";
					die();
				}
			}
		}
	}
}

$themessage = 'Un message est en cours de rédaction. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";
$utilisation_prototype = "ok";
//**************** EN-TETE *****************
$titre_page = "Messagerie";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class='bold'><a href='../accueil.php'>Retour à l'accueil</a> ";
if(((!isset($mode))||($mode!='rediger_message'))&&(peut_poster_message($_SESSION['statut']))) {
	echo " | <a href='".$_SERVER['PHP_SELF']."?mode=rediger_message'>Rédiger un message</a>";
}
echo "</p>";

//debug_var();

$messages_non_lus=check_messages_recus($_SESSION['login']);
if($messages_non_lus!="") {
	//echo "<p>Vous avez <a href='#messages_recus'>$messages_non_lus</a></p>";
	echo "<p>Vous avez <a href='".$_SERVER['PHP_SELF']."?mode=afficher_messages_non_lus' onclick=\"return confirm_abandon (this, change, '$themessage')\">$messages_non_lus</a></p>";
}

if((isset($mode))&&($mode=='afficher_messages_non_lus')) {

	echo affiche_historique_messages_recus($_SESSION['login'], 'non_lus');

	echo "<p><br /></p>";
	require("../lib/footer.inc.php");
	die();
}

if(peut_poster_message($_SESSION['statut'])) {
?>

<form action='../lib/form_message.php' method='post'>
	<fieldset style='border:1px solid grey; background-image: url("../images/background/opacite50.png");'>
		<?php
			echo add_token_field(true);
		?>
		<p class='bold'>Envoi d'un message/post-it&nbsp;:</p>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th>Destinataire(s)</th>
				<td style='text-align:left;'>
					<!-- ======================================================= -->
					<!-- Balises concernant JavaScript -->
					<div id='p_ajout_dest_js' style='display:none;float:right;whidth:16px;'><a href="javascript:affiche_ajout_dest();"><img src='../images/icons/add.png' width='16' height='16' title='Ajouter un ou des destinataires' /></a></div>

					<div id='div_login_dest_js'><span style='color:red' id='span_ajoutez_un_ou_des_destinataires'>Ajoutez un ou des destinataires --&gt;</span></div>
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
								<option value='$lig_u->login'>$lig_u->civilite ".casse_mot($lig_u->nom, 'maj')." ".casse_mot($lig_u->prenom, 'majf2')."</option>";
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
				<th>Sujet</th>
				<td><input type='text' name='sujet' size='40' value="<?php
					if(isset($sujet)) {
						echo $sujet;
					}
				?>" onchange='changement()' /></td>
			</tr>
			<tr>
				<th>Message</th>
				<td><textarea id='message_messagerie' name='message' cols='50' rows='5' onchange='changement()'><?php
					if(isset($message)) {
						echo stripslashes(preg_replace("/\\\\n/", "\n", $message));
					}
				?></textarea></td>
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
						document.getElementById('div_login_dest_js').innerHTML=document.getElementById('div_login_dest_js').innerHTML+"<br /><span id='span_login_u_choisi_"+i+"'><input type='hidden' name='login_dest[]' value='"+login_u[i]+"' />"+designation_u[i]+" <a href=\"javascript:removeElement('span_login_u_choisi_"+i+"')\"><img src='../images/icons/delete.png' width='16' height='16' /></a></span>";

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

</script>

<?php
} // Fin du test PeutPosterMessage<statut>
?>
<!-- ======================================================= -->

<a name='messages_envoyes'></a>
<p class='bold'>Historique de vos messages envoyés&nbsp;:</p>
<p style='color:red'>Pouvoir afficher/masquer les messages<br />N'afficher par défaut que les messages des 7 derniers jours,...</p>
<div style='margin-left:3em; height:30em; maxheight:30em; overflow:auto;'>
<?php
	echo affiche_historique_messages($_SESSION['login']);
?>
</div>

<!-- ======================================================= -->

<a name='messages_recus'></a>
<p class='bold'>Historique de vos messages reçus&nbsp;:</p>
<p style='color:red'>Pouvoir afficher/masquer les messages<br />N'afficher par défaut que les messages des 7 derniers jours,...</p>
<div style='margin-left:3em; height:30em; maxheight:30em; overflow:auto;'>
<?php
	echo affiche_historique_messages_recus($_SESSION['login']);
?>
</div>

<!-- ======================================================= -->

<p><br /></p>
<?php
	require("../lib/footer.inc.php");
?>

