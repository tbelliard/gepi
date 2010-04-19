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

if ($utilisateur->getStatut()=="professeur" &&  getSettingValue("active_module_absence_professeur")!='y') {
    die("Le module n'est pas activé.");
}

//récupération des paramètres de la requète
$order = isset($_POST["order"]) ? $_POST["order"] :(isset($_GET["order"]) ? $_GET["order"] :NULL);

$id_saisie = isset($_POST["id_saisie"]) ? $_POST["id_saisie"] :(isset($_GET["id_saisie"]) ? $_GET["id_saisie"] :NULL);

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

//===========================

echo "<div id='aidmenu' style='display: none;'>test</div>\n";

// Etiquettes des onglets:
$onglet_abs='liste_saisies';
include('menu_abs2.inc.php');
//===========================
echo "<div class='css-panes' id='containDiv' style='overflow : auto;'>\n";


$saisie = AbsenceEleveSaisieQuery::create()->findPk($id_saisie);
if ($saisie == null) {
    echo 'Saisie non trouvée';
}

//la saisie est-elle modifiable ?
//Une saisie est modifiable ssi : elle appartient à l'utilisateur de la session,
//elle date de moins d'une heure et l'option a ete coché partie admin
$modifiable = false;
if (getSettingValue("abs2_modification_saisie_une_heure")=='y') {
    if ($saisie->getUtilisateurId() == $utilisateur->getPrimaryKey() && $saisie->getCreatedAt('U') > (time() - 3600)) {
	$modifiable = true;
    }
}


echo '<table>';

echo '<TBODY>';
    echo '<tr><TD>';
    echo 'N° de saisie';
    echo '</TD><TD>';
    echo $saisie->getPrimaryKey();
    echo '</TD></tr>';

    if ($saisie->getEleve() != null) {
	echo '<tr><TD>';
	echo 'Eleve';
	echo '</TD><TD>';
	echo $saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom();
	echo '</TD></tr>';
    }

    if ($saisie->getClasse() != null) {
	echo '<tr><TD>';
	echo 'Classe';
	echo '</TD><TD>';
	echo $classe->getNomComplet();
	echo '</TD></tr>';
    }


    echo '<TD>';
    if ($saisie->getClasse() != null) {
	//$classe = new Classe();
	echo $classe->getNomComplet();
    }
    echo '</TD>';

    echo '<TD>';
    if ($saisie->getGroupe() != null) {
	//$groupe = new Groupe();
	echo $saisie->getGroupe()->getNameAvecClasses();
    }
    echo '</TD>';

    echo '<TD>';
    if ($saisie->getAidDetails() != null) {
	//$groupe = new Groupe();
	echo $saisie->getAidDetails()->getNom();
    }
    echo '</TD>';

    echo '<TD><nobr>';
    echo (strftime("%a %d %b %Y %H:%M", $saisie->getDebutAbs('U')));
    echo '</nobr></TD>';

    echo '<TD><nobr>';
    echo (strftime("%a %d %b %Y %H:%M", $saisie->getFinAbs('U')));
    echo '</nobr></TD>';

    echo '<TD>';
    if ($saisie->getEdtCreneau() != null) {
	//$groupe = new Groupe();
	echo $saisie->getEdtCreneau()->getDescription();
    }
    echo '</TD>';

    echo '<TD>';
    echo '<nobr>';
    if ($saisie->getEdtEmplacementCours() != null) {
	//$groupe = new Groupe();
	echo $saisie->getEdtEmplacementCours()->getDescription();
    }
    echo '</nobr>';
    echo '</TD>';

    echo '<TD><nobr>';
    echo (strftime("%a %d %b %Y %H:%M", $saisie->getCreatedAt('U')));
    echo '</nobr></TD>';

    echo '<TD><nobr>';
    if ($saisie->getCreatedAt() != $saisie->getUpdatedAt()) {
	echo (strftime("%a %d %b %Y %H:%M", $saisie->getUpdatedAt('U')));
    }
    echo '</nobr></TD>';

    echo '<TD>';
    foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
	echo "<nobr>";
	echo $traitement->getDescriptionCourte();
	echo "</nobr> ";
    }
    echo '</TD>';

    echo '<TD>';
    if ((getSettingValue("active_module_trombinoscopes")=='y') && $saisie->getEleve() != null) {
	$nom_photo = $saisie->getEleve()->getNomPhoto(1);
	$photos = "../photos/eleves/".$nom_photo;
	if (($nom_photo == "") or (!(file_exists($photos)))) {
		$photos = "../mod_trombinoscopes/images/trombivide.jpg";
	}
	$valeur = redimensionne_image_petit($photos);
	echo '<img src="'.$photos.'" style="width: '.$valeur[0].'px; height: '.$valeur[1].'px; border: 0px; vertical-align: middle;" alt="" title="" />';
	}
    echo '</TD>';

    echo '<TD>';
    echo ($saisie->getCommentaire());
    echo '</TD>';

    echo '</TR>';


echo '</TBODY>';
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