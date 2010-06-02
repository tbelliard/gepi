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

//if ($utilisateur->getStatut()=="professeur" &&  getSettingValue("active_module_absence_professeur")!='y') {
//    die("Le module n'est pas activé.");
//}

//récupération des paramètres de la requète
$id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
if (isset($id_traitement) && $id_traitement != null) $_SESSION['id_traitement'] = $id_traitement;

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


$traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
if ($traitement == null) {
    $criteria = new Criteria();
    $criteria->addDescendingOrderByColumn(AbsenceElevetraitementPeer::UPDATED_AT);
    $criteria->setLimit(1);
    $traitement = $utilisateur->getAbsenceEleveTraitements($criteria)->getFirst();
    if ($traitement == null) {
	echo "traitement non trouvée";
	die();
    }
}

if (isset($message_enregistrement)) {
    echo $message_enregistrement;
}

echo '<table class="normal">';
echo '<form method="post" action="enregistrement_modif_traitement.php">';
echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
echo '<TBODY>';
echo '<tr><TD>';
echo 'N° de traitement : ';
echo '</TD><TD>';
echo $traitement->getPrimaryKey();
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Saisies : ';
echo '</TD><TD>';
echo '<table>';
$eleve_prec_id = null;
foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {
    //$saisie = new AbsenceEleveSaisie();
    echo '<tr><td>';
    if ($saisie->getEleve() == null) {
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
    }
    if ($eleve_prec_id != $saisie->getEleve()->getPrimaryKey()) {
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

    }
    echo ' De : ';
    echo (strftime("%a %d %b %Y %H:%M", $saisie->getDebutAbs('U')));
    echo ' à : ';
    echo (strftime("%a %d %b %Y %H:%M", $saisie->getFinAbs('U')));
    echo '</td></tr>';
    $eleve_prec_id = $saisie->getEleve()->getPrimaryKey();
}
echo '</table>';
echo '</TD></tr>';

if ($traitement->getEdtEmplacementCours() != null) {
    echo '<tr><TD>';
    echo 'Cours : ';
    echo '</TD><TD>';
    echo $traitement->getEdtEmplacementCours()->getDescription();
    echo '</TD></tr>';
}

echo '<tr><TD>';
echo 'Traitement : ';
echo '</TD><TD>';
foreach ($traitement->getAbsenceEleveTraitements() as $traitement) {
    //on affiche les traitements uniquement si ils ne sont pas modifiables, car si ils sont modifiables on va afficher un input pour pouvoir les modifier
    if ($traitement->getUtilisateurId() != $utilisateur->getPrimaryKey() || !$modifiable) {
	echo "<nobr>";
	echo $traitement->getDescriptionCourte();
	echo "</nobr><br>";
    }
}

$total_traitements = 0;
$type_autorises = AbsenceEleveTypeStatutAutoriseQuery::create()->filterByStatut($utilisateur->getStatut())->find();
foreach ($traitement->getAbsenceEleveTraitements() as $traitement) {
    //on affiche les traitements uniquement si ils ne sont pas modifiables, car si ils sont modifiables on va afficher un input pour pouvoir les modifier
    if ($traitement->getUtilisateurId() == $utilisateur->getPrimaryKey() && $modifiable) {
	$total_traitements = $total_traitements + 1;
	$type_autorises->getFirst();
	if ($type_autorises->count() != 0) {
		echo '<input type="hidden" name="id_traitement[';
		echo ($total_traitements - 1);
		echo ']" value="'.$traitement->getId().'"/>';
		echo ("<select name=\"type_traitement[");
		echo ($total_traitements - 1);
		echo ("]\">");
		echo "<option value='-1'></option>\n";
		foreach ($type_autorises as $type) {
		    //$type = new AbsenceEleveTypeStatutAutorise();
			echo "<option value='".$type->getAbsenceEleveType()->getId()."'";
			if ($type->getAbsenceEleveType()->getId() == $traitement->getATypeId()) {
			    echo "selected";
			}
			echo ">";
			echo $type->getAbsenceEleveType()->getNom();
			echo "</option>\n";
		}
		echo "</select><br>";
	}
    }
}
echo '<input type="hidden" name="total_traitements" value="'.$total_traitements.'"/>';


if ($modifiable && $total_traitements == 0) {
    echo ("<select name=\"ajout_type_absence\">");
    echo "<option value='-1'></option>\n";
    foreach ($type_autorises as $type) {
	//$type = new AbsenceEleveTypeStatutAutorise();
	    echo "<option value='".$type->getAbsenceEleveType()->getId()."'";
	    echo ">";
	    echo $type->getAbsenceEleveType()->getNom();
	    echo "</option>\n";
    }
    echo "</select>";
}

echo '</TD></tr>';

if ($modifiable || ($traitement->getCommentaire() != null && $traitement->getCommentaire() != "")) {
    echo '<tr><TD>';
    echo 'Commentaire : ';
    echo '</TD><TD>';
    if (!$modifiable) {
	echo ($traitement->getCommentaire());
    } else {
	echo '<input name="commentaire" value="'.$traitement->getCommentaire().'" type="text" maxlength="150" size="25"/>';
    }
    echo '</TD></tr>';
}

echo '<tr><TD>';
echo 'traitement le : ';
echo '</TD><TD>';
echo (strftime("%a %d %b %Y %H:%M", $traitement->getCreatedAt('U')));
echo '</TD></tr>';

if ($traitement->getCreatedAt() != $traitement->getUpdatedAt()) {
    echo '<tr><TD>';
    echo 'Modifiée le : ';
    echo '</TD><TD>';
    echo (strftime("%a %d %b %Y %H:%M", $traitement->getUpdatedAt('U')));
    echo '</TD></tr>';
}

if ($traitement->getIdSIncidents() != null && $traitement->getIdSIncidents() != -1) {
    echo '<tr><TD>';
    echo 'Discipline : ';
    echo '</TD><TD>';
    echo "<a href='../mod_discipline/traitement_incident.php?id_incident=".
    $traitement->getIdSIncidents()."&step=2&return_url=no_return'>Visualiser l'incident </a>";
    echo '</TD></tr>';
} elseif ($modifiable && $traitement->hasTypetraitementDiscipline()) {
    echo '<tr><TD>';
    echo 'Discipline : ';
    echo '</TD><TD>';
    echo "<a href='../mod_discipline/traitement_incident_abs2.php?id_absence_eleve_traitement=".
	$traitement->getId()."&return_url=no_return'>Saisir un incident disciplinaire</a>";
    echo '</TD></tr>';
}

echo '</TD></tr>';
if ($modifiable) {
    echo '<tr><TD colspan="2" style="text-align : center;">';
    echo '<button type="submit">Enregistrer les modifications</button>';
    echo '</TD></tr>';
}

echo '</TBODY>';

echo '</form>';
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
