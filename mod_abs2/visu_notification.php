<?php
/**
 *
 * @version $Id: visu_notification.php 7437 2011-07-18 19:20:27Z dblanqui $
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

//récupération des paramètres de la requète
$id_notification = isset($_POST["id_notification"]) ? $_POST["id_notification"] :(isset($_GET["id_notification"]) ? $_GET["id_notification"] :(isset($_SESSION["id_notification"]) ? $_SESSION["id_notification"] : NULL));
if (isset($id_notification) && $id_notification != null) $_SESSION['id_notification'] = $id_notification;
$menu = isset($_POST["menu"]) ? $_POST["menu"] :(isset($_GET["menu"]) ? $_GET["menu"] : NULL);

//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
if(!$menu){
    $titre_page = "Les absences";
}
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

if(!$menu){
    include('menu_abs2.inc.php');
}
//===========================
echo "<div class='css-panes' style='background-color:#c7e3ec;' id='containDiv' style='overflow : auto;'>\n";

if (isset($message_enregistrement)) {
    echo $message_enregistrement;
}

$notification = new AbsenceEleveNotification();
$notification = AbsenceEleveNotificationQuery::create()->findPk($id_notification);
if ($notification == null) {
    $criteria = new Criteria();
    $criteria->addDescendingOrderByColumn(AbsenceEleveNotificationPeer::UPDATED_AT);
    $criteria->setLimit(1);
    $notification = $utilisateur->getAbsenceEleveNotifications($criteria)->getFirst();
    if ($notification == null) {
	echo "Notification non trouvée";
	die();
    }
}

echo '<table class="normal">';
echo '<tbody>';
echo '<tr><td>';
echo 'N° de notification';
echo '</td><td>';
echo $notification->getPrimaryKey();
if ($notification->getAbsenceEleveTraitement() == null) {
	echo ' (notification orpheline)';
} else {
	echo " <a style='background-color:#ebedb5;' href='visu_traitement.php?id_traitement=".$notification->getATraitementId()."";
    if($menu){
                echo"&menu=false";
            } 
    echo "';>";
	echo '(voir traitement)';
	echo "</a>";
}
echo '</td></tr>';

echo '<tr><td>';
echo 'Saisies : ';
echo '</td><td>';
echo '<table style="background-color:#cae7cb;">';
$eleve_prec_id = null;
if ($notification->getAbsenceEleveTraitement() != null) {
    foreach ($notification->getAbsenceEleveTraitement()->getAbsenceEleveSaisies() as $saisie) {
	//$saisie = new AbsenceEleveSaisie();
	if ($saisie->getEleve() == null) {
	    if (!$notification->getAbsenceEleveTraitement()->getAbsenceEleveSaisies()->isFirst()) {
		echo '</td></tr>';
	    }
	    echo '<tr><td>';
	    echo 'Aucune absence';
	    if ($saisie->getGroupe() != null) {
		echo ' pour le groupe ';
		echo $saisie->getGroupe()->getdescription();
	    }
	    if ($saisie->getClasse() != null) {
		echo ' pour la classe ';
		echo $saisie->getClasse()->getNom();
	    }
	    if ($saisie->getAidDetails() != null) {
		echo ' pour l\'aid ';
		echo $saisie->getAidDetails()->getNom();
	    }
	    echo '<tr><td>';
	} elseif ($eleve_prec_id != $saisie->getEleve()->getPrimaryKey()) {
	    if (!$notification->getAbsenceEleveTraitement()->getAbsenceEleveSaisies()->isFirst()) {
		echo '</td></tr>';
	    }
	    echo '<tr><td>';
	    echo '<div>';
	    echo $saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom();
	    if ((getSettingValue("active_module_trombinoscopes")=='y') && $saisie->getEleve() != null) {
		$nom_photo = $saisie->getEleve()->getNomPhoto(1);
		//$photos = "../photos/eleves/".$nom_photo;
		$photos = $nom_photo;
		//if (($nom_photo == "") or (!(file_exists($photos)))) {
		if (($nom_photo == NULL) or (!(file_exists($photos)))) {
			$photos = "../mod_trombinoscopes/images/trombivide.jpg";
		}
		$valeur = redimensionne_image_petit($photos);
		echo ' <img src="'.$photos.'" style="width: '.$valeur[0].'px; height: '.$valeur[1].'px; border: 0px; vertical-align: middle;" alt="" title="" />';
	    }
	    if ($utilisateur->getAccesFicheEleve($saisie->getEleve())) {
		//echo "<a href='../eleves/visu_eleve.php?ele_login=".$saisie->getEleve()->getLogin()."' target='_blank'>";
		echo "<a href='../eleves/visu_eleve.php?ele_login=".$saisie->getEleve()->getLogin()."' >";
		echo ' (voir fiche)';
		echo "</a>";
	    }
	    echo '</div>';
	    echo '<br/>';
	    $eleve_prec_id = $saisie->getEleve()->getPrimaryKey();
	}
	echo '<div>';
	echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."";
    if($menu){
                echo"&menu=false";
            } 
    echo "' style='display: block; height: 100%;'> ";
	echo $saisie->getdateDescription();
	echo "</a>";
	echo '</div>';
	if (!$notification->getAbsenceEleveTraitement()->getAbsenceEleveSaisies()->isLast()) {
	    echo '<br/>';
	}
    }
}
echo '</td></tr>';
echo '</table>';
echo '</td></tr>';

echo '<tr><td>';
echo 'Type de notification : ';
echo '</td><td>';
if ($notification->getModifiable()) {
    echo '<form method="post" action="enregistrement_modif_notification.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
    echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
    echo '<input type="hidden" name="modif" value="type"/>';
    echo ("<select name=\"type\" onchange='submit()'>");
    foreach (AbsenceEleveNotificationPeer::getValueSet(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION) as $type) {
	if ($type === AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_SMS && (getSettingValue("abs2_sms") != 'y')) {
	    //pas d'option sms
	} else {
	    echo "<option value='$type'";
	    if ($notification->getTypeNotification() === $type) {
		echo ' selected="selected" ';
	    }
	    echo ">".$type."</option>\n";
	}
    }
    echo "</select>";
    echo '<button type="submit">Modifier</button>';
	echo '</p>';
    echo '</form>';
} else {
    echo $notification->getTypeNotification();
}
echo '</td></tr>';


if ($notification->getErreurMessageEnvoi() != null && $notification->getErreurMessageEnvoi() != '') {
    echo '<tr><td>';
    echo 'Message d\'erreur : ';
    echo '</td><td>';
    echo $notification->getErreurMessageEnvoi();
    echo '</td></tr>';
}


echo '<tr><td>';
echo 'Responsables : ';
echo '</td><td>';
foreach ($notification->getResponsableEleves() as $responsable) {
    echo '<div>';
    //$responsable = new ResponsableEleve();
    echo $responsable->getCivilite().' '.strtoupper($responsable->getNom()).' '.$responsable->getPrenom();
    if ($notification->getModifiable()) {
	echo '<div style="float: right;">';
	echo '<form method="post" action="enregistrement_modif_notification.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
	echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
	echo '<input type="hidden" name="pers_id" value="'.$responsable->getPrimaryKey().'"/>';
	echo '<input type="hidden" name="modif" value="enlever_responsable"/>';
	echo '<button type="submit">Enlever</button>';
	echo '</p>';
	echo '</form>';
	echo '</div>';
    }
    echo '</div>';
    if (!$notification->getResponsableEleves()->isLast()) {
	echo '<br/>';
    }
}
if ($notification->getModifiable()) {
    if ($notification->getAbsenceEleveTraitement() != null) {
	if ($notification->getResponsableEleves()->count() != $notification->getAbsenceEleveTraitement()->getResponsablesInformationsSaisies()->count()) {
	    echo '<div>';
	    echo '<form method="post" action="enregistrement_modif_notification.php">';
        echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	    echo '<p>';
	    echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
	    echo '<input type="hidden" name="modif" value="ajout_responsable"/>';
	    echo ("<select name=\"pers_id\">");
	    foreach ($notification->getAbsenceEleveTraitement()->getResponsablesInformationsSaisies() as $responsable_information) {
		$responsable = $responsable_information->getResponsableEleve();
		echo '<option value="'.$responsable->getPersId().'"';
		echo ">".$responsable->getCivilite().' '.strtoupper($responsable->getNom()).' '.$responsable->getPrenom()
			.' (Resp '.$responsable_information->getRespLegal().")</option>\n";
	    }
	    echo "</select>";
	    echo '<button type="submit">Ajouter</button>';
	    echo '</p>';
	    echo '</form>';
	    echo '</div>';
	}
    }
}
echo '</td></tr>';

if ($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_EMAIL) {
    echo '<tr><td>';
    echo 'Email : ';
    echo '</td><td>';
    if (!$notification->getModifiable()) {
	echo $notification->getEmail();
    } else {
	echo '<div>';
	echo '<form method="post" action="enregistrement_modif_notification.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
	echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
	echo '<input type="hidden" name="modif" value="email"/>';
	echo ("<select name=\"email\" onchange='submit()'>");
	$selected = false;
	if ($notification->getAbsenceEleveTraitement() != null) {
		foreach ($notification->getAbsenceEleveTraitement()->getResponsablesInformationsSaisies() as $responsable_information) {
		    $responsable = $responsable_information->getResponsableEleve();
		    if ($responsable->getMel() != null && $responsable->getMel() != '') {
			//$responsable = new ResponsableEleve();
			echo '<option value="'.$responsable->getMel().'"';
			if ($responsable->getMel() == $notification->getEmail()) {
			    echo " selected='selected' ";
			    $selected = true;
			}
			echo ">".$responsable->getMel().' ('.$responsable->getCivilite().' '.$responsable->getNom().' ; Resp '.$responsable_information->getRespLegal().")</option>\n";
		    }
		}
	}
	if (!$selected) {
	    echo '<option value="'.$notification->getEmail().'"';
	    echo " selected='selected' ";
	    echo ">".$notification->getEmail()."</option>\n";
	}
	echo "</select>";
	echo '<button type="submit">Valider</button>';
	echo '</p>';
	echo '</form>';
	echo '</div>';
	echo ' ou ';
	echo '<div>';
	echo '<form method="post" action="enregistrement_modif_notification.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
	echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
	echo '<input type="hidden" name="modif" value="email"/>';
	echo '<input type="text" size="20" name="email" value=""/>';
	echo '<button type="submit">Valider</button>';
	echo '</p>';
	echo '</form>';
	echo '</div>';
	
    }
    echo '</td></tr>';
}

if ($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_SMS ||
	$notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COMMUNICATION_TELEPHONIQUE) {
    echo '<tr><td>';
    echo 'Tel : ';
    echo '</td><td>';
    if (!$notification->getModifiable()) {
	echo $notification->getTelephone();
    } else {
	echo '<div>';
	echo '<form method="post" action="enregistrement_modif_notification.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
	echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
	echo '<input type="hidden" name="modif" value="tel"/>';
	//echo ("<select style='width:200px;' name=\"tel\" onchange='submit()'>");
	echo ("<select name=\"tel\" onchange='submit()'>");
	$selected = false;
	foreach ($notification->getAbsenceEleveTraitement()->getResponsablesInformationsSaisies() as $responsable_information) {
	    $responsable = $responsable_information->getResponsableEleve();
	    if ($responsable->getTelPort() != null || $responsable->getTelPort() != '') {
		echo '<option value="'.$responsable->getTelPort().'"';
		if ($responsable->getTelPort() == $notification->getTelephone()) {
		    echo " selected='selected' ";
		    $selected = true;
		}
		echo ">".$responsable->getTelPort().' ('.$responsable->getCivilite().' '.$responsable->getNom().' ; Resp '.$responsable_information->getRespLegal()." ; Tel portable)</option>\n";
	    }

	    if ($responsable->getTelPers() != null || $responsable->getTelPers() != '') {
		echo '<option value="'.$responsable->getTelPers().'"';
		if ($responsable->getTelPers() == $notification->getTelephone()) {
		    echo " selected='selected' ";
		    $selected = true;
		}
		echo ">".$responsable->getTelPers().' ('.$responsable->getCivilite().' '.$responsable->getNom().' ; Resp '.$responsable_information->getRespLegal()." ; Tel personnel)</option>\n";
	    }

	    if ($responsable->getTelProf() != null || $responsable->getTelProf() != '') {
		echo '<option value="'.$responsable->getTelProf().'"';
		if ($responsable->getTelProf() == $notification->getTelephone()) {
		    echo " selected='selected' ";
		    $selected = true;
		}
		echo ">".$responsable->getTelProf().' ('.$responsable->getCivilite().' '.$responsable->getNom().' ; Resp '.$responsable_information->getRespLegal()." ; Tel professionnel)</option>\n";
	    }
	}
	if (!$selected) {
	    echo '<option value="'.$notification->getTelephone().'"';
	    echo " selected='selected' ";
	    echo ">".$notification->getTelephone()."</option>\n";
	}
	echo "</select>";
	echo '<button type="submit">Valider</button>';
	echo '</p>';
	echo '</form>';
	echo '</div>';
	echo ' ou ';
	echo '<div>';
	echo '<form method="post" action="enregistrement_modif_notification.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
	echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
	echo '<input type="hidden" name="modif" value="tel"/>';
	echo '<input type="text" size="20" name="tel" value=""/>';
	echo '<button type="submit">Valider</button>';
	echo '</p>';
	echo '</form>';
	echo '</div>';

    }
    echo '</td></tr>';
}

// 	Affichage des adresses pour l'envoi du SMS (permettre de savoir à quel responsable l'envoyer selon l'adresse)
if (($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER) ||
    ($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_SMS))  {
    echo '<tr><td>';
    echo 'Adresse : ';
    echo '</td><td>';
	//pour information : Nom du ou des responsables sélectionnés
	echo 'De : <i> ';
	echo $notification->getResponsableEleveAdresse()->getDescriptionHabitant();
	echo '</i><br/><br/>';
    if ($notification->getResponsableEleveAdresse() != null) {
	//on ne modifie le statut si le type est courrier ou communication téléphonique
	if ($notification->getResponsableEleveAdresse()->getAdr1() != null && $notification->getResponsableEleveAdresse()->getAdr1() != '') {
	    echo $notification->getResponsableEleveAdresse()->getAdr1();
	    echo '<br/>';
	}
	if ($notification->getResponsableEleveAdresse()->getAdr2() != null && $notification->getResponsableEleveAdresse()->getAdr2() != '') {
	    echo $notification->getResponsableEleveAdresse()->getAdr2();
	    echo '<br/>';
	}
	if ($notification->getResponsableEleveAdresse()->getAdr3() != null && $notification->getResponsableEleveAdresse()->getAdr3() != '') {
	    echo $notification->getResponsableEleveAdresse()->getAdr3();
	    echo '<br/>';
	}
	if ($notification->getResponsableEleveAdresse()->getAdr4() != null && $notification->getResponsableEleveAdresse()->getAdr4() != '') {
	    echo $notification->getResponsableEleveAdresse()->getAdr4();
	    echo '<br/>';
	}
	echo $notification->getResponsableEleveAdresse()->getCp().' '.$notification->getResponsableEleveAdresse()->getCommune();
	if ($notification->getResponsableEleveAdresse()->getPays() != null && $notification->getResponsableEleveAdresse()->getPays() != '' && $notification->getResponsableEleveAdresse()->getPays() != getsettingvalue('gepiSchoolPays')) {
	    echo '<br/>';
	    echo $notification->getResponsableEleveAdresse()->getPays();
	}	
    }

    if ($notification->getModifiable()) {
	echo '<div>';
	echo '<form method="post" action="enregistrement_modif_notification.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
	echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
	echo '<input type="hidden" name="modif" value="adresse"/>';
	echo ("<select name=\"adr_id\" onchange='submit()'>");
	$adresse_col = new PropelCollection();
	if ($notification->getAbsenceEleveTraitement() != null) {
	    foreach ($notification->getAbsenceEleveTraitement()->getResponsablesInformationsSaisies() as $responsable_information) {
		if ($responsable_information->getResponsableEleve() != null && $responsable_information->getResponsableEleve()->getResponsableEleveAdresse() != null) {
		     $adresse_col->add($responsable_information->getResponsableEleve()->getResponsableEleveAdresse());
		}
	    }
	}
	foreach ($adresse_col as $responsable_addresse) {
	    //$responsable_addresse = new ResponsableEleveAdresse();
	    echo '<option value="'.$responsable_addresse->getPrimaryKey().'"';
	    if ($notification->getResponsableEleveAdresse() != null &&
		    $responsable_addresse->getPrimaryKey() == $notification->getResponsableEleveAdresse()->getPrimaryKey()) {
		echo " selected='selected' ";
	    }
	    echo ">";
	    if ($responsable_addresse->getAdr1() != null && $responsable_addresse->getAdr1() != '') {
		echo $responsable_addresse->getAdr1();
		echo ' ';
	    }
	    if ($responsable_addresse->getAdr2() != null && $responsable_addresse->getAdr2() != '') {
		echo $responsable_addresse->getAdr2();
		echo ' ';
	    }
	    if ($responsable_addresse->getAdr3() != null && $responsable_addresse->getAdr3() != '') {
		echo $responsable_addresse->getAdr3();
		echo ' ';
	    }
	    if ($responsable_addresse->getAdr4() != null && $responsable_addresse->getAdr4() != '') {
		echo $responsable_addresse->getAdr4();
		echo ' ';
	    }
	    echo $responsable_addresse->getCp().' '.$responsable_addresse->getCommune();
	    if ($responsable_addresse->getPays() != null && $responsable_addresse->getPays() != '' && $responsable_addresse->getPays() != getsettingvalue('gepiSchoolPays')) {
		echo ' ';
		echo $responsable_addresse->getPays();
	    }
	    echo ' ('.$responsable_addresse->getDescriptionHabitant().')';
	    echo "</option>\n";
	}
	echo "</select>";
	echo '<button type="submit">Modifier</button>';
	echo '</p>';
	echo '</form>';
	echo '</div>';
    }

    echo '</td></tr>';
}

echo '<tr><td>';
echo 'Statut : ';
echo '</td><td>';
//on ne modifie manuellement le statut si le type est courrier ou communication téléphonique
if ($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER || $notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COMMUNICATION_TELEPHONIQUE) {
    echo '<form method="post" action="enregistrement_modif_notification.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
    echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
    echo '<input type="hidden" name="modif" value="statut"/>';
    echo ("<select name=\"statut\" onchange='submit()'>");
    $i = 0;
    foreach (AbsenceEleveNotificationPeer::getValueSet(AbsenceEleveNotificationPeer::STATUT_ENVOI) as $status) {
        echo "<option value='$status'";
        if ($notification->getStatutEnvoi() === $status) {
            echo 'selected';
        }
        echo ">".$status."</option>\n";
    }
    echo "</select>";
    echo '<button type="submit">Modifier</button>';
	echo '</p>';
    echo '</form>';
} else if ($notification->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL ||
	$notification->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_PRET_A_ENVOYER) {
    //on autorise un changement de statut entre initial et pret a envoyer
    echo '<form method="post" action="enregistrement_modif_notification.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
    echo '<p>';
    echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
    echo '<input type="hidden" name="modif" value="statut"/>';
    echo ("<select name=\"statut\" onchange='submit()'>");

    echo "<option value='".AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL."'";
    if ($notification->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL) { echo ' selected="selected" ';}
    echo ">".AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL."</option>\n";

    echo "<option value='".AbsenceEleveNotificationPeer::STATUT_ENVOI_PRET_A_ENVOYER."'";
    if ($notification->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_PRET_A_ENVOYER) { echo ' selected="selected" ';}
    echo ">".AbsenceEleveNotificationPeer::STATUT_ENVOI_PRET_A_ENVOYER."</option>\n";

    echo "</select>";
    echo '<button type="submit">Modifier</button>';
    echo '</p>';
    echo '</form>';
} else {
    echo $notification->getStatutEnvoi();
}
echo '</td></tr>';


echo '<tr><td>';
echo 'Créé par : ';
echo '</td><td>';
if ($notification->getUtilisateurProfessionnel() != null) {
    echo $notification->getUtilisateurProfessionnel()->getCivilite();
    echo ' ';
    echo $notification->getUtilisateurProfessionnel()->getNom();
}
echo '</td></tr>';

if ($notification->getdateEnvoi() != null) {
    echo '<tr><td>';
    echo 'Date d\'envoi : ';
    echo '</td><td>';
    echo (strftime("%a %d/%m/%Y %H:%M", $notification->getdateEnvoi('U')));
    echo '</td></tr>';
} else {
    echo '<tr><td>';
    echo 'Créé le : ';
    echo '</td><td>';
    echo (strftime("%a %d/%m/%Y %H:%M", $notification->getCreatedAt('U')));
    echo '</td></tr>';

    if ($notification->getCreatedAt() != $notification->getUpdatedAt()) {
	echo '<tr><td>';
	echo 'Modifiée le : ';
	echo '</td><td>';
	echo (strftime("%a %d/%m/%Y %H:%M", $notification->getUpdatedAt('U')));
	echo '</td></tr>';
    }
}

if ($notification->getTypeNotification() != AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COMMUNICATION_TELEPHONIQUE &&
	($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER  ||
	$notification->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL  ||
	$notification->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_PRET_A_ENVOYER)) {
    echo '<tr><td colspan="2" style="text-align : center;">';
    echo '<form method="post" action="generer_notification.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
    echo '<p>';
    echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
    if ($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER) {
	echo '<button type="submit" onclick=\'window.open("generer_notification.php?id_notification='.$notification->getPrimaryKey().'"); setTimeout("window.location = \"visu_notification.php\"", 1000); return false;\'>Génerer la notification</button>';
    } else {
	if ($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_EMAIL && ($notification->getEmail() == null || $notification->getEmail() == '')) {
	    //on affiche pas le bouton de generation car l'adresse n'est pas renseignee
	} else {
	    echo '<button type="submit">Générer la notification</button>';
	}
    }
    echo '</p>';
    echo '</form>';
    echo '</td></tr>';
}
if ($notification->getStatutEnvoi() != AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL) {
    echo '<tr><td colspan="2" style="text-align : center;">';
    echo '<form method="post" action="enregistrement_modif_notification.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
    echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
    echo '<input type="hidden" name="modif" value="duplication"/>';
    echo '<button type="submit">Créer une autre notification</button>';
	echo '</p>';
    echo '</form>';
    echo '</td></tr>';
}

echo '</tbody>';

echo '</table>';


echo "</div>\n";

require_once("../lib/footer.inc.php");

//fonction redimensionne les photos petit format
function redimensionne_image_petit($photo)
 {
    // prendre les informations sur l'image
    $info_image = getimagesize($photo);
    // largeur et hauteur de l'image d'origine
    $largeur = $info_image[0];
    $hauteur = $info_image[1];
    // largeur et/ou hauteur maximum à afficher
             $taille_max_largeur = 35;
             $taille_max_hauteur = 35;

    // calcule le ratio de redimensionnement
     $ratio_l = $largeur / $taille_max_largeur;
     $ratio_h = $hauteur / $taille_max_hauteur;
     $ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

    // définit largeur et hauteur pour la nouvelle image
     $nouvelle_largeur = $largeur / $ratio;
     $nouvelle_hauteur = $hauteur / $ratio;

   // on renvoit la largeur et la hauteur
    return array($nouvelle_largeur, $nouvelle_hauteur);
 }
?>
