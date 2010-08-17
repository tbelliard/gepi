<?php
/**
 *
 * @version $Id$
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

if ($utilisateur->getStatut()=="professeur" &&  getSettingValue("active_module_absence_professeur")!='y') {
    die("Le module n'est pas activé.");
}

//récupération des paramètres de la requète
$id_saisie = isset($_POST["id_saisie"]) ? $_POST["id_saisie"] :(isset($_GET["id_saisie"]) ? $_GET["id_saisie"] :(isset($_SESSION["id_saisie"]) ? $_SESSION["id_saisie"] : NULL));
if (isset($id_saisie) && $id_saisie != null) $_SESSION['id_saisie'] = $id_saisie;

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

echo "<div class='css-panes' style='background-color:#cae7cb;' id='containDiv' style='overflow : auto;'>\n";


$saisie = AbsenceEleveSaisieQuery::create()->findPk($id_saisie);
if ($saisie == null) {
    $criteria = new Criteria();
    $criteria->addDescendingOrderByColumn(AbsenceEleveSaisiePeer::UPDATED_AT);
    $criteria->setLimit(1);
    $saisie_col = $utilisateur->getAbsenceEleveSaisiesJoinEdtCreneau($criteria);
    $saisie = $saisie_col->getFirst();
    if ($saisie == null) {
	echo "Saisie non trouvée";
	die();
    }
}


//on va mettre dans la session l'identifiant de la saisie pour faciliter la navigation par onglet
if ($saisie != null) {
    $_SESSION['id_saisie_visu'] = $saisie->getPrimaryKey();
}


//la saisie est-elle modifiable ?
//Une saisie est modifiable ssi : elle appartient à l'utilisateur de la session,
//si elle date de moins de 24 heure (sauf pour le statut prof)
//elle date de moins d'une heure et l'option a ete coché partie admin pour le statut prof
$modifiable = $saisie->getUtilisateurId() == $utilisateur->getPrimaryKey() && ($saisie->getCreatedAt('U') > (time() - 24*3600));
if ($modifiable && $utilisateur->getStatut() == 'professeur') {
    if (getSettingValue("abs2_modification_saisie_une_heure")=='y') {
	$modifiable =  ($saisie->getCreatedAt('U') > (time() - 3600));
    } else {
	$modifiable = false;
    }
}
if (!$modifiable) {
    echo "La saisie n'est pas modifiable<br/>";
}

if (isset($message_enregistrement)) {
    echo $message_enregistrement;
}

echo '<table class="normal">';
echo '<form method="post" action="enregistrement_modif_saisie.php">';
echo '<input type="hidden" name="id_saisie" value="'.$saisie->getPrimaryKey().'"/>';
echo '<TBODY>';
echo '<tr><TD>';
echo 'N° de saisie : ';
echo '</TD><TD>';
echo $saisie->getPrimaryKey();
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Saisie par : ';
echo '</TD><TD>';
echo $saisie->getUtilisateurProfessionnel()->getCivilite().' '.$saisie->getUtilisateurProfessionnel()->getNom().' '.substr($saisie->getUtilisateurProfessionnel()->getPrenom(), 0, 1).'.';
echo '</TD></tr>';

    echo '<tr>';
if ($saisie->getEleve() == null) {
    echo '<TD colspan="2">';
    echo "Marqueur d'appel effectué";
    echo '</TD>';
} else {
    echo '<TD>Élève : </td>';
    echo '<TD>';
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
	echo "<a href='../eleves/visu_eleve.php?ele_login=".$saisie->getEleve()->getLogin()."' target='_blank'>";
	echo ' (voir fiche)';
	echo "</a>";
    }
echo '</TD>';
}
echo '</tr>';

if ($saisie->getClasse() != null) {
    echo '<tr><TD>';
    echo 'Classe : ';
    echo '</TD><TD>';
    echo $saisie->getClasse()->getNom();
    echo '</TD></tr>';
}

if ($saisie->getGroupe() != null) {
    echo '<tr><TD>';
    echo 'Groupe : ';
    echo '</TD><TD>';
    echo $saisie->getGroupe()->getNameAvecClasses();
    echo '</TD></tr>';
}

if ($saisie->getAidDetails() != null) {
    echo '<tr><TD>';
    echo 'Aid : ';
    echo '</TD><TD>';
    echo $saisie->getAidDetails()->getNom();
    echo '</TD></tr>';
}

if ($saisie->getEdtEmplacementCours() != null) {
    echo '<tr><TD>';
    echo 'Cours : ';
    echo '</TD><TD>';
    echo $saisie->getEdtEmplacementCours()->getDescription();
    echo '</TD></tr>';
}

if ($saisie->getEdtCreneau() != null) {
    echo '<tr><TD>';
    echo 'Créneau : ';
    echo '</TD><TD>';
    echo $saisie->getEdtCreneau()->getDescription();
    echo '</TD></tr>';
}

echo '<tr><TD>';
echo 'Début : ';
echo '</TD><TD>';
if (!$modifiable) {
    echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getDebutAbs('U')));
} else {
    echo '<nobr><input name="heure_debut" value="'.$saisie->getDebutAbs("H:i").'" type="text" maxlength="5" size="4"/>&nbsp;';
    if ($utilisateur->getStatut() == 'professeur') {//on autorise pas au professeur a changer la date
	echo (strftime(" %a %d/%m/%Y", $saisie->getDebutAbs('U')));
	echo '<input name="date_debut" value="'.$saisie->getDebutAbs('d/m/Y').'" type="hidden"/></nobr> ';
    } else {
	echo '<input id="trigger_calendrier_debut" name="date_debut" value="'.$saisie->getDebutAbs('d/m/Y').'" type="text" maxlength="10" size="8"/></nobr> ';

    //    echo '<img id="trigger_date_debut" src="../images/icons/calendrier.gif"/>';
	echo '</nobr>';
	echo '
	<script type="text/javascript">
	    Calendar.setup({
		inputField     :    "trigger_calendrier_debut",     // id of the input field
		ifFormat       :    "%d/%m/%Y",      // format of the input field
		button         :    "trigger_calendrier_debut",  // trigger for the calendar (button ID)
		align          :    "Tl",           // alignment (defaults to "Bl")
		singleClick    :    true
	    });
	</script>';
    }
}
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Fin : ';
echo '</TD><TD>';
if (!$modifiable) {
    echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getFinAbs('U')));
} else {
    echo '<nobr><input name="heure_fin" value="'.$saisie->getFinAbs("H:i").'" type="text" maxlength="5" size="4"/>&nbsp;';
    //if ($utilisateur->getStatut() == 'professeur' && getSettingValue("abs2_saisie_prof_decale") != 'y') {
    if ($utilisateur->getStatut() == 'professeur') {
	echo (strftime(" %a %d/%m/%Y", $saisie->getFinAbs('U')));
	echo '<input name="date_fin" value="'.$saisie->getFinAbs('d/m/Y').'" type="hidden"/></nobr> ';
    } else {
	echo '<input id="trigger_calendrier_fin" name="date_fin" value="'.$saisie->getFinAbs('d/m/Y').'" type="text" maxlength="10" size="8"/></nobr> ';

	//echo '<img id="trigger_date_debut" src="../images/icons/calendrier.gif"/>';
	echo '</nobr>';
	echo '
	<script type="text/javascript">
	    Calendar.setup({
		inputField     :    "trigger_calendrier_fin",     // id of the input field
		ifFormat       :    "%d/%m/%Y",      // format of the input field
		button         :    "trigger_calendrier_fin",  // trigger for the calendar (button ID)
		align          :    "Tl",           // alignment (defaults to "Bl")
		singleClick    :    true
	    });
	</script>';
    }
}
echo '</TD></tr>';

echo '<tr><TD>';
echo 'Traitement : ';
echo '</TD><TD style="background-color:#ebedb5;">';
foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
    //on affiche les traitements uniquement si ils ne sont pas modifiables, car si ils sont modifiables on va afficher un input pour pouvoir les modifier
    if ($traitement->getUtilisateurId() != $utilisateur->getPrimaryKey() || ! $traitement->getModifiable()) {
	echo "<nobr>";
	if ($utilisateur->getStatut() != 'professeur') {
	    echo "<a href='visu_traitement.php?id_traitement=".$traitement->getId()."' style='display: block; height: 100%;'> ";
	    echo $traitement->getDescription();
	    echo "</a>";
	} else {
	    echo $traitement->getDescription();
	}
	echo "</nobr><br/>";
    }
}

$total_traitements_modifiable = 0;
$type_autorises = AbsenceEleveTypeStatutAutoriseQuery::create()->filterByStatut($utilisateur->getStatut())->find();
foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
    //on affiche les traitements uniquement si ils ne sont pas modifiables, car si ils sont modifiables on va afficher un input pour pouvoir les modifier
    if ($traitement->getUtilisateurId() != $utilisateur->getPrimaryKey() || !$traitement->getModifiable()) {
	continue;
    }
    $total_traitements_modifiable = $total_traitements_modifiable + 1;
    $type_autorises->getFirst();
    if ($type_autorises->count() != 0) {
	    echo '<input type="hidden" name="id_traitement[';
	    echo ($total_traitements_modifiable - 1);
	    echo ']" value="'.$traitement->getId().'"/>';
	    echo ("<select name=\"type_traitement[");
	    echo ($total_traitements_modifiable - 1);
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
echo '<input type="hidden" name="total_traitements" value="'.$total_traitements_modifiable.'"/>';

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite") {
//pour les cpe et scola, ajouter un type est fait avec ajout d'un traitement, donc on affiche pas ce bloc
    if ($total_traitements_modifiable == 0) {
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
    if (!$modifiable) {
	echo '<button type="submit" name="modifier_type" value="vrai">Modifier le type</button>';
    }
}

echo '</TD></tr>';

if ($modifiable || ($saisie->getCommentaire() != null && $saisie->getCommentaire() != "")) {
    echo '<tr><TD>';
    echo 'Commentaire : ';
    echo '</TD><TD>';
    if (!$modifiable) {
	echo ($saisie->getCommentaire());
    } else {
	echo '<input name="commentaire" value="'.$saisie->getCommentaire().'" type="text" maxlength="150" size="25"/>';
    }
    echo '</TD></tr>';
}

echo '<tr><TD>';
echo 'Saisie le : ';
echo '</TD><TD>';
echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getCreatedAt('U')));
echo '</TD></tr>';

if ($saisie->getCreatedAt() != $saisie->getUpdatedAt()) {
    echo '<tr><TD>';
    echo 'Modifiée le : ';
    echo '</TD><TD>';
    echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getUpdatedAt('U')));
    echo '</TD></tr>';
}

if ($saisie->getIdSIncidents() !== null) {
    echo '<tr><TD>';
    echo 'Discipline : ';
    echo '</TD><TD>';
    echo "<a href='../mod_discipline/saisie_incident.php?id_incident=".
    $saisie->getIdSIncidents()."&step=2&return_url=no_return'>Visualiser l'incident </a>";
    echo '</TD></tr>';
} elseif ($modifiable && $saisie->hasTypeSaisieDiscipline()) {
    echo '<tr><TD>';
    echo 'Discipline : ';
    echo '</TD><TD>';
    echo "<a href='../mod_discipline/saisie_incident_abs2.php?id_absence_eleve_saisie=".
	$saisie->getId()."&return_url=no_return'>Saisir un incident disciplinaire</a>";
    echo '</TD></tr>';
}
$saisies_conflit = $saisie->getSaisiesContradictoiresManquementObligation();
if (!$saisies_conflit->isEmpty()) {
    echo '<tr><TD>';
    echo 'La saisie est en contradiction avec : ';
    echo '</TD><TD>';
    foreach ($saisies_conflit as $saisie) {
	echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style=''> ";
	echo $saisie->getId();
	echo "</a>";
	if (!$saisies_conflit->isLast()) {
	    echo ' - ';
	}
    }
    echo '</TD></tr>';
}

echo '</TD></tr>';
if ($modifiable) {
    echo '<tr><TD colspan="2" style="text-align : center;">';
    echo '<button type="submit">Enregistrer les modifications</button>';
    echo '</TD></tr>';
}

if ($utilisateur->getStatut()=="cpe" || $utilisateur->getStatut()=="scolarite") {
    echo '<tr><TD colspan="2" style="text-align : center;">';
    echo '<button type="submit" name="creation_traitement" value="oui">Traiter la saisie</button>';
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