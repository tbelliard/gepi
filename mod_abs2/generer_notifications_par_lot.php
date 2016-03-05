<?php
/**
 *
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
require_once("../lib/envoi_SMS.inc.inc.php");
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

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activé.");
}

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite") {
    die("acces interdit");
}

$repertoireDestination = "../temp/";

if(isset ($_FILES["monFichier"])) {
	if ( is_uploaded_file($_FILES["monFichier"]["tmp_name"])) {
		$nomDestination = $_FILES["monFichier"]["name"];
		if (rename($_FILES["monFichier"]["tmp_name"], $repertoireDestination.$nomDestination)) {
			$_SESSION['monFichier'] = $_FILES["monFichier"]["name"];
		}
	} else {
		unset ($_SESSION['monFichier']);
	}	
}

$menu = isset($_POST["menu"]) ? $_POST["menu"] :(isset($_GET["menu"]) ? $_GET["menu"] : NULL);
//récupération des id des notifications
$nb = 100;
if (isset($_POST["nb_checkbox"])) {
    $nb = $_POST["nb_checkbox"];
} else if (isset($_POST["item_per_page"])) {
    $nb = $_POST["item_per_page"];
}
$id_notif_col = new PropelCollection();
for($i=0; $i<$nb; $i++) {
    if (isset($_POST["select_notification"][$i])) {
	$id_notif_col->add($_POST["select_notification"][$i]);
    }
}
if ($id_notif_col->isEmpty() && isset($_SESSION['id_notif_col'])) {
    $id_notif_col = $_SESSION['id_notif_col'];
}
if (isset($_GET['retirer_id_notification'])) {
    $key = $id_notif_col->search($_GET['retirer_id_notification']);
    if ($key !== false) {
	$id_notif_col->remove($key);
    }
}
$_SESSION['id_notif_col'] = $id_notif_col;
$notifications_col = AbsenceEleveNotificationQuery::create()->filterByPrimaryKeys($id_notif_col)->find();

if (isset ($_POST['valide']) && ('confirmer' == $_POST['valide'])) {
	$_SESSION['compile'] = isset ($_POST['compile']) ? $_POST['compile'] : FALSE;
}

$_SESSION['compile'] = isset ($_SESSION['compile']) ? $_SESSION['compile'] : TRUE;
//
//on imprime les courriers par lot
//
if (isset($_GET['envoyer_courrier']) && $_GET['envoyer_courrier'] == 'true') {
	
    $courrier_recap_col = new PropelCollection();
    $courrier_nouvellement_envoyés_col = new PropelCollection();
	
    // load the TinyButStrong libraries    
	include_once('../tbs/tbs_class.php'); // TinyButStrong template engine
    
    $TBS = new clsTinyButStrong; // new instance of TBS
    include_once('../tbs/plugins/tbs_plugin_opentbs.php');
    $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin
    include_once 'lib/function.php';
	
	if (isset ($_SESSION['monFichier'])) { 
		$courrier_lot_modele=$repertoireDestination.$_SESSION['monFichier'];
	} else {
		$courrier_lot_modele=repertoire_modeles('absence_modele_lettre_parents.odt');
	}	
    
    $TBS->LoadTemplate($courrier_lot_modele, OPENTBS_ALREADY_UTF8);
	
	
	if ($_SESSION['compile'])  {
		include_once 'lib/genere_table_notification.php';
		include 'lib/regroupe_notifications.php';
	} else {
		include_once 'lib/genere_table_notification.php';

		foreach($notifications_col as $notif) {
			if ($notif->getTypeNotification() != AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER) {
				continue;
			}

			$TBS = AbsencesNotificationHelper::MergeNotification($notif, $courrier_lot_modele);

			$recap = $notif->getId().', ';
			foreach ($notif->getResponsableEleves() as $responsable) {
				$recap .= $responsable->getCivilite().' '.strtoupper($responsable->getNom()).' '.$responsable->getPrenom();
				if (!$notif->getResponsableEleves()->isLast()) {
				$recap .=  ' ';
				}
			}
			$courrier_recap_col->append($recap);

			//on met un code d'erreur au cas ou la generation se fait mal
			if ($notif->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL
				|| $notif->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_PRET_A_ENVOYER) {
				$notif->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_ECHEC);
				$notif->setErreurMessageEnvoi('Echec de l\'impression par lot');
				$notif->save();
				$courrier_nouvellement_envoyés_col->append($notif);
			} else {
				$notif->setUpdatedAt('now');
				$notif->save();
			}
		}

		//on imprime le global
		$TBS->MergeBlock('notifications',$tableNotifications);

		$TBS->MergeField('nb_impressions',count($tableNotifications));
		$TBS->MergeBlock('courrier_recap_col',$courrier_recap_col);
		// Output as a download file (some automatic fields are merged here)
		//on change le statut des notifications
		foreach($courrier_nouvellement_envoyés_col as $notif) {
		$notif->setDateEnvoi('now');
		$notif->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_EN_COURS);
		$notif->setErreurMessageEnvoi('');
		$notif->save();
		}
	}

    //$TBS->PlugIn(OPENTBS_DEBUG_XML_CURRENT);
	//$TBS->PlugIn(OPENTBS_DEBUG_XML_SHOW);
    $now = new DateTime();
    $TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, 'lot_abs_notif_'.$now->format('Y_m_d__H_i').'.odt');

    die();
}

//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
if(!$menu){
    $titre_page = "Les absences";
}
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
$dojo = true;
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

if(!$menu){
    include('menu_abs2.inc.php');
}

echo "<div class='css-panes' style='background-color:#c7e3ec;' id='containDiv' style='overflow : none; float : left; margin-top : -1px; border-width : 1px;'>\n";

if ($id_notif_col->isEmpty()) {
    echo 'Aucune notification sélectionnée -> ';
    echo '<a href="liste_notifications.php">liste des notifications</a>';
    die;
}

//
//on envoie les emails
//
$nb_mail_envoyes = 0;
if (isset($_GET['envoyer_email']) && $_GET['envoyer_email'] == 'true') {
    // Load the template
    include_once 'lib/function.php';
    $email_modele=repertoire_modeles('absence_email.txt');
    include_once '../orm/helpers/AbsencesNotificationHelper.php';
    foreach($notifications_col as $notif) {
	$TBS = AbsencesNotificationHelper::MergeNotification($notif, $email_modele);
	$retour_envoi = AbsencesNotificationHelper::EnvoiNotification($notif, $TBS->Source);
	if ($retour_envoi == '') {
	    $nb_mail_envoyes = $nb_mail_envoyes + 1;
	}
    }
    echo 'Mail envoyés : '.$nb_mail_envoyes.'<br/>';
}
//
//on affiche les notifications de type email
//
$notif_mail_a_envoyer_col = new PropelCollection();
$notif_mail_fini_col = new PropelCollection();
foreach($notifications_col as $notif) {
    if ($notif->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_EMAIL) {
	if ($notif->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL || $notif->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_PRET_A_ENVOYER) {
	    $notif_mail_a_envoyer_col->add($notif);
	} else {
	    $notif_mail_fini_col->add($notif);
	}
    }
}
if (!$notif_mail_fini_col->isEmpty()) {$notif = new AbsenceEleveNotification();
    echo 'Email envoyés';
    echo '<table id="table_liste_absents" style="border-spacing:0px;">';
    //en tete commentaire
    echo '</tr>';
    echo '<th>id</th>';
    echo '<th></th>';
    echo '<th></th>';
    echo '<th>email</th>';
    echo '<th>statut</th>';
    echo '<th>date d\'envoi</th>';
    echo '<th>traitement</th>';
    echo '</tr>';
    foreach($notif_mail_fini_col as $notif) {
	echo '<tr>';
	echo '<td><a href="visu_notification.php?id_notification='.$notif->getId().'">'.$notif->getId().'</a></td>';
	echo '<td>';
	if ($notif->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_SUCCES
		|| $notif->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_SUCCES_AVEC_ACCUSE_DE_RECEPTION) {
	    echo '<div style="color : green;">envoi réussi</div>';
	} else {
	    echo '<div style="color : red;">Erreur : '.$notif->getErreurMessageEnvoi().'</div>';
	}
	echo '</td>';
	echo '<td>';
	echo ' <a href="generer_notifications_par_lot.php?retirer_id_notification='.$notif->getId().'">Retirer du lot</a>';
	echo '</td>';
	echo '<td>'.$notif->getEmail().'</td>';
	echo '<td>Statut '.$notif->getStatutEnvoi().'</td>';
	echo '<td>'.$notif->getDateEnvoi('d/m/Y H:i').'</td>';
	echo '<td>Traitement '.$notif->getAbsenceEleveTraitement()->getDescription().'</td>';
	echo '</tr>';
    }
    echo '</table></br>';
    echo '<br/><br/>';
}
if (!$notif_mail_a_envoyer_col->isEmpty()) {$notif = new AbsenceEleveNotification();
    echo 'Notifications à envoyer par mail';
    echo '<table id="table_liste_absents" style="border-spacing:0px;">';
    //en tete commentaire
    echo '</tr>';
    echo '<th>id</th>';
    echo '<th></th>';
    echo '<th>email</th>';
    echo '<th>statut</th>';
    echo '<th>traitement</th>';
    echo '</tr>';
    foreach($notif_mail_a_envoyer_col as $notif) {
	echo '<tr>';
	    echo '<td><a href="visu_notification.php?id_notification='.$notif->getId().'">'.$notif->getId().'</a></td>';
	    echo '<td><a href="generer_notifications_par_lot.php?retirer_id_notification='.$notif->getId().'">Retirer du lot</a></td>';
	    echo '<td>'.$notif->getEmail().'</td>';
	    echo '<td>Statut '.$notif->getStatutEnvoi().'</td>';
	    echo '<td>Traitement '.$notif->getAbsenceEleveTraitement()->getDescription().'</td>';
	echo '</tr>';
    }
    echo '</table></br>';
    echo '<a dojoType="dijit.form.Button" onclick="location.href=\'generer_notifications_par_lot.php?envoyer_email=true\';" href="generer_notifications_par_lot.php?envoyer_email=true">Envoyer les emails</a>';
    echo '<br/><br/>';
}

if(getSettingAOui("autorise_envoi_sms") && getSettingAOui("abs2_sms")) {
	//
	//on envoie les SMS
	//
	$nb_sms_envoyes = 0;
	if (isset($_GET['envoyer_sms']) && $_GET['envoyer_sms'] == 'true') {
	    // Load the template
	    include_once 'lib/function.php';
	    $sms_modele=repertoire_modeles('absence_sms.txt');
	    include_once '../orm/helpers/AbsencesNotificationHelper.php';
	    foreach($notifications_col as $notif) {
		$TBS = AbsencesNotificationHelper::MergeNotification($notif, $sms_modele);
		$retour_envoi = AbsencesNotificationHelper::EnvoiNotification($notif, $TBS->Source);
		if ($retour_envoi == '') {
		    $nb_sms_envoyes = $nb_sms_envoyes + 1;
		}
	    }
	    echo 'SMS envoyés : '.$nb_sms_envoyes.'<br/>';
	}

	//
	//on affiche les notifications de type SMS
	//
	$notif_sms_a_envoyer_col = new PropelCollection();
	$notif_sms_fini_col = new PropelCollection();
	foreach($notifications_col as $notif) {
	    if ($notif->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_SMS) {
		if ($notif->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL || $notif->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_PRET_A_ENVOYER) {
		    $notif_sms_a_envoyer_col->add($notif);
		} else {
		    $notif_sms_fini_col->add($notif);
		}
	    }
	}
	if (!$notif_sms_fini_col->isEmpty()) {$notif = new AbsenceEleveNotification();
	    echo 'SMS envoyés';
	    // A CORRIGER : id table_liste_absents répété plusieurs fois dans la page
	    echo '<table id="table_liste_absents" style="border-spacing:0px;">';
	    //en tete commentaire
	    echo '</tr>';
	    echo '<th>id</th>';
	    echo '<th></th>';
	    echo '<th></th>';
	    echo '<th>SMS</th>';
	    echo '<th>statut</th>';
	    echo '<th>date d\'envoi</th>';
	    echo '<th>traitement</th>';
	    echo '</tr>';
	    foreach($notif_sms_fini_col as $notif) {
		echo '<tr>';
		echo '<td><a href="visu_notification.php?id_notification='.$notif->getId().'">'.$notif->getId().'</a></td>';
		echo '<td>';
		if ($notif->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_SUCCES
			|| $notif->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_SUCCES_AVEC_ACCUSE_DE_RECEPTION) {
		    echo '<div style="color : green;">envoi réussi</div>';
		} else {
		    echo '<div style="color : red;">Erreur : '.$notif->getErreurMessageEnvoi().'</div>';
		}
		echo '</td>';
		echo '<td>';
		echo ' <a href="generer_notifications_par_lot.php?retirer_id_notification='.$notif->getId().'">Retirer du lot</a>';
		echo '</td>';
		echo '<td>'.$notif->getTelephone().'</td>';
		echo '<td>Statut '.$notif->getStatutEnvoi().'</td>';
		echo '<td>'.$notif->getDateEnvoi('d/m/Y H:i').'</td>';
		echo '<td>Traitement '.$notif->getAbsenceEleveTraitement()->getDescription().'</td>';
		echo '</tr>';
	    }
	    echo '</table></br>';
	    echo '<br/><br/>';
	}
	if (!$notif_sms_a_envoyer_col->isEmpty()) {$notif = new AbsenceEleveNotification();
	    echo 'Notifications à envoyer par SMS';
	    echo '<table id="table_liste_absents" style="border-spacing:0px;">';
	    //en tete commentaire
	    echo '</tr>';
	    echo '<th>id</th>';
	    echo '<th></th>';
	    echo '<th>SMS</th>';
	    echo '<th>statut</th>';
	    echo '<th>traitement</th>';
	    echo '</tr>';
	    foreach($notif_sms_a_envoyer_col as $notif) {
		echo '<tr>';
		    echo '<td><a href="visu_notification.php?id_notification='.$notif->getId().'">'.$notif->getId().'</a></td>';
		    echo '<td><a href="generer_notifications_par_lot.php?retirer_id_notification='.$notif->getId().'">Retirer du lot</a></td>';
		    echo '<td>'.$notif->getTelephone().'</td>';
		    echo '<td>Statut '.$notif->getStatutEnvoi().'</td>';
		    echo '<td>Traitement '.$notif->getAbsenceEleveTraitement()->getDescription().'</td>';
		echo '</tr>';
	    }
	    echo '</table></br>';
	    echo '<a dojoType="dijit.form.Button" onclick="location.href=\'generer_notifications_par_lot.php?envoyer_sms=true\';" href="generer_notifications_par_lot.php?envoyer_sms=true">Envoyer les SMS</a>';
	    echo '<br/><br/>';
	}
}

//
//on affiche les notifications de type courrier
//
$notif_courrier_a_envoyer_col = new PropelCollection();
$notif_courrier_fini = new PropelCollection();
foreach($notifications_col as $notif) {
    if ($notif->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER) {
	if ($notif->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL || $notif->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_PRET_A_ENVOYER) {
	    $notif_courrier_a_envoyer_col->add($notif);
	} else {
	    $notif_courrier_fini->add($notif);
	}
    }
}
if (!$notif_courrier_fini->isEmpty()) {$notif = new AbsenceEleveNotification();
    echo 'Courriers duplicata';
    echo '<table id="table_liste_absents" style="border-spacing:0px;">';
    //en tete commentaire
    echo '</tr>';
    echo '<th>id</th>';
    echo '<th></th>';
    echo '<th></th>';
    echo '<th>responsables</th>';
    echo '<th>adresse</th>';
    echo '<th>statut</th>';
    echo '<th>date d\'envoi</th>';
    echo '<th>traitement</th>';
    echo '</tr>';
    foreach($notif_courrier_fini as $notif) {
	echo '<tr>';
	echo '<td><a href="visu_notification.php?id_notification='.$notif->getId().'">'.$notif->getId().'</a></td>';
	echo '<td>';
	if ($notif->getStatutEnvoi() != AbsenceEleveNotificationPeer::STATUT_ENVOI_ECHEC) {
	    echo '<div style="color : green;">Impression réussie</div>';
	} else {
	    echo '<div style="color : red;">Erreur</div>';
	}
	echo '</td>';
	echo '<td>';
	echo ' <a href="generer_notifications_par_lot.php?retirer_id_notification='.$notif->getId().'">Retirer du lot</a>';
	echo '</td>';
	echo '<td>';
	foreach ($notif->getResponsableEleves() as $responsable) {
	    //$responsable = new ResponsableEleve();
	    echo $responsable->getCivilite().' '.strtoupper($responsable->getNom()).' '.$responsable->getPrenom();
	    if (!$notif->getResponsableEleves()->isLast()) {
		echo ', ';
	    }
	}
	echo '</td>';
	echo '<td>';
	if ($notif->getAdresse() != null) {
	    echo $notif->getAdresse()->getDescriptionSurUneLigne();
	}
	echo '</td>';
	echo '<td>Statut '.$notif->getStatutEnvoi().'</td>';
	echo '<td>'.$notif->getDateEnvoi('d/m/Y H:i').'</td>';
	echo '<td>';
	if ($notif->getAbsenceEleveTraitement() != null) {
	    echo 'Traitement '.$notif->getAbsenceEleveTraitement()->getDescription();
	}
	echo '</td>';
	echo '</tr>';
    }
    echo '</table></br>';
    echo '<br/>';
}
if (!$notif_courrier_a_envoyer_col->isEmpty()) {$notif = new AbsenceEleveNotification();
    echo 'Nouveaux courriers';
    echo '<table id="table_liste_absents" style="border-spacing:0px;">';
    //en tete commentaire
    echo '</tr>';
    echo '<th>id</th>';
    echo '<th></th>';
    echo '<th>responsables</th>';
    echo '<th>adresse</th>';
    echo '<th>statut</th>';
    echo '<th>date d\'envoi</th>';
    echo '<th>traitement</th>';
    echo '</tr>';
    foreach($notif_courrier_a_envoyer_col as $notif) {
	echo '<tr>';
	echo '<td><a href="visu_notification.php?id_notification='.$notif->getId().'">'.$notif->getId().'</a></td>';
	echo '<td>';
	echo ' <a href="generer_notifications_par_lot.php?retirer_id_notification='.$notif->getId().'">Retirer du lot</a>';
	echo '</td>';
	echo '<td>';
	foreach ($notif->getResponsableEleves() as $responsable) {
	    //$responsable = new ResponsableEleve();
	    echo $responsable->getCivilite().' '.strtoupper($responsable->getNom()).' '.$responsable->getPrenom();
	    if (!$notif->getResponsableEleves()->isLast()) {
		echo ', ';
	    }
	}
	echo '</td>';
	echo '<td>';
	if ($notif->getAdresse() != null) {
	    echo $notif->getAdresse()->getDescriptionSurUneLigne();
	}
	echo '</td>';
	echo '<td>Statut '.$notif->getStatutEnvoi().'</td>';
	echo '<td>'.$notif->getDateEnvoi('d/m/Y H:i').'</td>';
	echo '<td>';
	if ($notif->getAbsenceEleveTraitement() != null) {
	    echo 'Traitement '.$notif->getAbsenceEleveTraitement()->getDescription();
	}
	echo '</td>';
	echo '</tr>';
    }
    echo '</table></br></br>';
}

if ($notif_courrier_a_envoyer_col->isEmpty() && !$notif_courrier_fini->isEmpty()) {
    echo '<a dojoType="dijit.form.Button" onclick="window.open(\'generer_notifications_par_lot.php?envoyer_courrier=true\'); var loc = \'window.location = \\\'generer_notifications_par_lot.php\\\'\'; setTimeout(loc, 3000);" href="generer_notifications_par_lot.php?envoyer_courrier=true">Imprimer les courriers duplicata</a>';
    echo '<br/><br/>';
} elseif (!$notif_courrier_a_envoyer_col->isEmpty() || !$notif_courrier_fini->isEmpty()) {
    echo '<a dojoType="dijit.form.Button" onclick="window.open(\'generer_notifications_par_lot.php?envoyer_courrier=true\'); var loc = \'window.location = \\\'generer_notifications_par_lot.php\\\'\'; setTimeout(loc, 3000);" href="generer_notifications_par_lot.php?envoyer_courrier=true">Imprimer tous les courriers</a>';
    echo '<br/><br/>';
}
?>
<fieldset>
	<legend>Options</legend>
	<form id="options" method="post" action="generer_notifications_par_lot.php">
		<input type="checkbox" 
			   name="compile" 
			   id="compile" 
			   <?php if($_SESSION['compile'])	 echo 'checked ="checked"';?>
			   onchange="javascript: submit(document.getElementById('options'))"
				/>
		<input type="hidden" name="notification" value="<?php echo serialize($notif_courrier_fini); ?>" />
		<input type="hidden" name="valide" value="confirmer" />
		<label for="compile">
			Une seule notification par élève
		</label>
		<input type="submit" name="valide" id="btn_submit" value="confirmer" />
	</form>
	<form id="fichierPerso" method="post" action="generer_notifications_par_lot.php" enctype="multipart/form-data" >
		<label for="gabarit" title="Pour revenir au modèle de GEPI, laissez le champ vide et validez">
			Utiliser le gabarit : <?php echo (isset ($_SESSION['monFichier']) ? $_SESSION['monFichier'] : 'de GEPI'); ?>
		</label>
		<br />
		<input type="file" 
			   name="monFichier" 
			   id="gabarit"
			   />
		<button type="submit" 
			   name="valide" 
			   id="btn_submit" 
			   value="Choisir" 
			   >
			Charger le fichier
		</button>
	</form>
</fieldset>
<script type="text/javascript">
	document.getElementById('btn_submit').className='invisible';
</script>
	   
<?php

echo "</div>\n";

$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dijit.form.Button");
</script>';

require_once("../lib/footer.inc.php");

?>
