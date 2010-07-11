<?php
/**
 *
 * @version $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
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

if ($utilisateur->getStatut()!="cpe") {
    die("acces interdit");
}

//récupération des paramètres de la requète
$id_notification = isset($_POST["id_notification"]) ? $_POST["id_notification"] :(isset($_GET["id_notification"]) ? $_GET["id_notification"] :(isset($_SESSION["id_notification"]) ? $_SESSION["id_notification"] : NULL));
if (isset($id_notification) && $id_notification != null) $_SESSION['id_notification'] = $id_notification;

//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
$titre_page = "Les absences";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

include('menu_abs2.inc.php');
//===========================
echo "<div class='css-panes' id='containDiv' style='overflow : auto;'>\n";

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
echo '<TBODY>';
echo '<tr><TD>';
echo 'N° de notification';
echo '</TD><TD>';
echo $notification->getPrimaryKey();
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Saisies : ';
echo '</TD><TD>';
echo '<table>';
$eleve_prec_id = null;
foreach ($notification->getAbsenceEleveTraitement()->getAbsenceEleveSaisies() as $saisie) {
    //$saisie = new AbsenceEleveSaisie();
    if ($saisie->getEleve() == null) {
	echo '<tr><td>';
	echo 'Aucune absence';
	if ($saisie->getGroupe() != null) {
	    echo ' pour le groupe ';
	    echo $saisie->getGroupe()->getDescription();
	}
	if ($saisie->getClasse() != null) {
	    echo ' pour la classe ';
	    echo $saisie->getClasse()->getNomComplet();
	}
	if ($saisie->getAidDetails() != null) {
	    echo ' pour l\'aid ';
	    echo $saisie->getClasse()->getNomComplet();
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
	    $photos = "../photos/eleves/".$nom_photo;
	    if (($nom_photo == "") or (!(file_exists($photos)))) {
		    $photos = "../mod_trombinoscopes/images/trombivide.jpg";
	    }
	    $valeur = redimensionne_image_petit($photos);
	    echo ' <img src="'.$photos.'" style="width: '.$valeur[0].'px; height: '.$valeur[1].'px; border: 0px; vertical-align: middle;" alt="" title="" />';
	}
	echo '</div>';
	echo '<br/>';
    }
    echo '<div>';
    echo $saisie->getDateDescription();
    echo '</div>';
    if (!$notification->getAbsenceEleveTraitement()->getAbsenceEleveSaisies()->isLast()) {
	echo '<br/>';
    }
    $eleve_prec_id = $saisie->getEleve()->getPrimaryKey();
}
echo '</table>';
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Type de notification : ';
echo '</TD><TD>';
//on ne modifie le type que si aucun envoi n'a ete fait
if ($notification->getStatutEnvoi() == AbsenceEleveNotification::$STATUT_INITIAL ||
	$notification->getTypeNotification() == AbsenceEleveNotification::$TYPE_COURRIER) {
    echo '<form method="post" action="enregistrement_modif_notification.php">';
    echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
    echo '<input type="hidden" name="modif" value="type"/>';
    echo ("<select name=\"type\">");
    echo "<option value='-1'></option>\n";
    $i = 0;
    while (isset(AbsenceEleveNotification::$LISTE_LABEL_TYPE[$i])) {
	echo "<option value='$i'";
	if ($notification->getTypeNotification() == $i) {
	    echo 'selected';
	}
	echo ">".AbsenceEleveNotification::$LISTE_LABEL_TYPE[$i]."</option>\n";
	$i = $i + 1;
    }
    echo "</select>";
    echo '<button type="submit">Modifier</button>';
    echo '</form>';
} else {
    echo AbsenceEleveNotification::$LISTE_LABEL_TYPE[$notification->getTypeNotification()];
}
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Statut : ';
echo '</TD><TD>';
//on ne modifie le statut si le type est courrier ou communication téléphonique
if ($notification->getTypeNotification() == AbsenceEleveNotification::$TYPE_COURRIER ||
	$notification->getTypeNotification() == AbsenceEleveNotification::$TYPE_TELEPHONIQUE) {
    echo '<form method="post" action="enregistrement_modif_notification.php">';
    echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
    echo '<input type="hidden" name="modif" value="statut"/>';
    echo ("<select name=\"statut\">");
    $i = 0;
    while (isset(AbsenceEleveNotification::$LISTE_LABEL_STATUT[$i])) {
	echo "<option value='$i'";
	if ($notification->getStatutEnvoi() == $i) {
	    echo 'selected';
	}
	echo ">".AbsenceEleveNotification::$LISTE_LABEL_STATUT[$i]."</option>\n";
	$i = $i + 1;
    }
    echo "</select>";
    echo '<button type="submit">Modifier</button>';
    echo '</form>';
} else {
    echo AbsenceEleveNotification::$LISTE_LABEL_STATUT[$notification->getStatutEnvoi()];
}
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Commentaire (ajouté à la notification) : ';
echo '</TD><TD>';
echo '<form method="post" action="enregistrement_modif_notification.php">';
echo '<input type="hidden" name="id_notification" value="'.$notification->getPrimaryKey().'"/>';
echo '<input type="hidden" name="modif" value="commentaire"/>';
echo '<input type="text" name="commentaire" size="30" value="'.$notification->getCommentaire().'" />';
echo '<button type="submit">Modifier</button>';
echo '</form>';
echo '</TD></tr>';


echo '<tr><TD>';
echo 'Créé par : ';
echo '</TD><TD>';
if ($notification->getUtilisateurProfessionnel() != null) {
    echo $notification->getUtilisateurProfessionnel()->getCivilite();
    echo ' ';
    echo $notification->getUtilisateurProfessionnel()->getNom();
}
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Créé le : ';
echo '</TD><TD>';
echo (strftime("%a %d %b %Y %H:%M", $notification->getCreatedAt('U')));
echo '</TD></tr>';

if ($notification->getCreatedAt() != $notification->getUpdatedAt()) {
    echo '<tr><TD>';
    echo 'Modifiée le : ';
    echo '</TD><TD>';
    echo (strftime("%a %d %b %Y %H:%M", $notification->getUpdatedAt('U')));
    echo '</TD></tr>';
}


echo '</TBODY>';

echo '</table>';

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
