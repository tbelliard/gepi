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
$id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
if (isset($id_traitement) && $id_traitement != null) $_SESSION['id_traitement'] = $id_traitement;
$menu = isset($_POST["menu"]) ? $_POST["menu"] :(isset($_GET["menu"]) ? $_GET["menu"] : Null);
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
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

if(!$menu){
    include('menu_abs2.inc.php');
}
//===========================
echo "<div class='css-panes' style='background-color:#ebedb5;' id='containDiv' style='overflow : auto;'>\n";


$traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
if ($traitement == null) {
    $criteria = new Criteria();
    $criteria->addDescendingOrderByColumn(AbsenceEleveTraitementPeer::UPDATED_AT);
    $criteria->setLimit(1);
    $traitement = $utilisateur->getAbsenceEleveTraitements($criteria)->getFirst();
    if ($traitement == null) {
	echo "Traitement non trouvé";
	die();
    }
}

if (isset($message_enregistrement)) {
    echo $message_enregistrement;
}

echo '<table class="normal">';
echo '<tbody>';
echo '<tr><td>';
echo 'N° de traitement';
echo '</td><td>';
echo $traitement->getPrimaryKey();
echo '</td></tr>';

echo '<tr><TD>';
echo 'Créé par : ';
echo '</TD><TD>';
if ($traitement->getUtilisateurProfessionnel() != null) {
	echo $traitement->getUtilisateurProfessionnel()->getCivilite().' '.$traitement->getUtilisateurProfessionnel()->getNom().' '.mb_substr($traitement->getUtilisateurProfessionnel()->getPrenom(), 0, 1).'.';
}
echo '</TD></tr>';

if ($traitement->getModifieParUtilisateurId() != null && $traitement->getUtilisateurId() != $traitement->getModifieParUtilisateurId()) {
    echo '<tr><TD>';
    echo 'Modifié par : ';
    echo '</TD><TD>';
    echo $traitement->getModifieParUtilisateur()->getCivilite().' '.$traitement->getModifieParUtilisateur()->getNom().' '.mb_substr($traitement->getModifieParUtilisateur()->getPrenom(), 0, 1).'.';
    echo '</TD></tr>';
}

echo '<tr><td>';
echo 'Saisies : ';
echo '</td><td>';
echo '<table style="background-color:#cae7cb;">';
$eleve_prec_id = null;

foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {
    //$saisie = new AbsenceEleveSaisie();
    if ($saisie->getEleve() == null) {
	if (!$traitement->getAbsenceEleveSaisies()->isFirst()) {
	    echo '</td></tr>';
	}
	echo '<tr><td>';
	echo 'Aucune absence';
	if ($saisie->getGroupe() != null) {
	    echo ' pour le groupe ';
	    echo $saisie->getGroupe()->getNameAvecClasses();
	}
	if ($saisie->getClasse() != null) {
	    echo ' pour la classe ';
	    echo $saisie->getClasse()->getNom();
	}
	if ($saisie->getAidDetails() != null) {
	    echo ' pour l\'aid ';
	    echo $saisie->getAidDetails()->getNom();
	}
	echo ' ';
	echo $saisie->getTypesDescription();
	echo '<tr><td>';
    } elseif ($eleve_prec_id != $saisie->getEleve()->getPrimaryKey()) {
	if (!$traitement->getAbsenceEleveSaisies()->isFirst()) {
	    echo '</td></tr>';
	}
	echo '<tr><td>';
	echo '<div>';
	echo $saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom();
	if ((getSettingValue("active_module_trombinoscopes")=='y') && $saisie->getEleve() != null) {
	    $nom_photo = $saisie->getEleve()->getNomPhoto(1);
	    $photos = $nom_photo;
	    //if (($nom_photo == "") or (!(file_exists($photos)))) {
	    if (($nom_photo == NULL) or (!(file_exists($photos)))) {
		    $photos = "../mod_trombinoscopes/images/trombivide.jpg";
	    }
	    $valeur = redimensionne_image_petit($photos);
	    echo ' <img src="'.$photos.'" style="width: '.$valeur[0].'px; height: '.$valeur[1].'px; border: 0px; vertical-align: middle;" alt="" title="" />';
	}
	if ($utilisateur->getAccesFicheEleve($saisie->getEleve())) {
	    echo "<a href='../eleves/visu_eleve.php?ele_login=".$saisie->getEleve()->getLogin()."&amp;onglet=responsable&amp;quitter_la_page=y' target='_blank'>";
	    //echo "<a href='../eleves/visu_eleve.php?ele_login=".$saisie->getEleve()->getLogin()."' >";
	    echo ' (voir fiche)';
	    echo "</a>";
	}
	echo '<div style="float: right; margin-top:0.35em; margin-left:0.2em;">';
	if ($traitement->getAbsenceEleveSaisies()->isEmpty() && $traitement->getModifiable()) {
	    echo '<form method="post" action="liste_saisies_selection_traitement.php">';
        echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
	    echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
	    echo '<input type="hidden" name="filter_eleve" value="'.$saisie->getEleve()->getNom().'"/>';
	    echo '<button type="submit">Ajouter</button>';
	echo '</p>';
	    echo '</form>';
	}
	echo '</div>';
	echo '</div>';
	echo '<br/>';
	$eleve_prec_id = $saisie->getEleve()->getPrimaryKey();
    }
    echo '<div>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."";
    if($menu){
                echo"&menu=false";
            } 
    echo"' style='height: 100%;'> ";
    echo $saisie->getDateDescription();
    echo ' ';
    echo $saisie->getTypesDescription();
    echo "</a>";
    echo '<div style="float: right;  margin-top:-0.22em; margin-left:0.2em;">';
    if ($traitement->getModifiable()) {
	echo '<form method="post" action="enregistrement_modif_traitement.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
	echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
	echo '<input type="hidden" name="modif" value="enlever_saisie"/>';
	echo '<input type="hidden" name="id_saisie" value="'.$saisie->getPrimaryKey().'"/>';
	echo '<button type="submit">Enlever</button>';
	echo '</p>';
	echo '</form>';
    }
    echo '</div>';
    echo '</div>';
    if (!$traitement->getAbsenceEleveSaisies()->isLast()) {
	echo '<br/>';
    }
}
if (!$traitement->getAbsenceEleveSaisies()->isEmpty()) {
    echo '<br/>';
    echo '<form method="post" action="liste_saisies_selection_traitement.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
    echo '<p>';
    echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
    echo '<input type="hidden" name="filter_recherche_saisie_a_rattacher" value="oui"/>';
    echo '<button type="submit">Chercher des saisies à rattacher</button>';
    echo '</p>';
    echo '</form>';
}
echo '</td></tr>';
echo '</table>';

echo '</td></tr>';

echo '<tr><td>';
echo 'Type : ';
echo '</td><td>';
//on ne modifie le type que si aucun envoi n'a ete fait //on fait non
//if ($traitement->getModifiable()) {
    $type_autorises = AbsenceEleveTypeStatutAutoriseQuery::create()->filterByStatut($utilisateur->getStatut())->useAbsenceEleveTypeQuery()->orderBySortableRank()->endUse()->find();
    if ($type_autorises->count() != 0) {
	echo '<form method="post" action="enregistrement_modif_traitement.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
	echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
	echo '<input type="hidden" name="modif" value="type"/>';
	echo ("<select name=\"id_type\" onchange='submit()'>");
	echo "<option value='-1'></option>\n";
	$type_in_list = false;
	foreach ($type_autorises as $type) {
	    //$type = new AbsenceEleveTypeStatutAutorise();
		echo "<option value='".$type->getAbsenceEleveType()->getId()."'";
		if ($type->getAbsenceEleveType()->getId() == $traitement->getATypeId()) {
		    echo " selected='selected'";
		    $type_in_list = true;
		}
		echo ">";
		echo $type->getAbsenceEleveType()->getNom();
		echo "</option>\n";
	}
	if (!$type_in_list && $traitement->getAbsenceEleveType() != null) {
	    echo "<option value='".$traitement->getAbsenceEleveType()->getId()."'";
	    echo " selected='selected'";
	    echo ">";
	    echo $traitement->getAbsenceEleveType()->getNom();
	    echo "</option>\n";
	}
	echo "</select>";
	echo '<button type="submit">Modifier</button>';
	echo '</p>';
	echo '</form>';
    }
//} else {
//    if ($traitement->getAbsenceEleveType() != null) {
//	echo $traitement->getAbsenceEleveType()->getNom();
//    }
//}
echo '</td></tr>';

echo '<tr><td>';
echo 'Motif : ';
echo '</td><td>';
$motifs = AbsenceEleveMotifQuery::create()->orderByRank()->find();
echo '<form method="post" action="enregistrement_modif_traitement.php">';
echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
echo '<input type="hidden" name="modif" value="motif"/>';
echo ("<select name=\"id_motif\" onchange='submit()'>");
echo "<option value='-1'></option>\n";
foreach ($motifs as $motif) {
    //$justification = new AbsenceEleveJustification();
    echo "<option value='".$motif->getId()."'";
    if ($motif->getId() == $traitement->getAMotifId()) {
	echo " selected='selected'";
    }
    echo ">";
    echo $motif->getNom();
    echo "</option>\n";
}
echo "</select>";
echo '<button type="submit">Modifier</button>';
	echo '</p>';
echo '</form>';
echo '</td></tr>';

echo '<tr><td>';
echo 'Justification : ';
echo '</td><td>';
$justifications = AbsenceEleveJustificationQuery::create()->orderByRank()->find();
echo '<form method="post" action="enregistrement_modif_traitement.php">';
echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
echo '<input type="hidden" name="modif" value="justification"/>';
echo ("<select name=\"id_justification\" onchange='submit()'>");
echo "<option value='-1'></option>\n";
foreach ($justifications as $justification) {
    //$justification = new AbsenceEleveJustification();
    echo "<option value='".$justification->getId()."'";
    if ($justification->getId() == $traitement->getAJustificationId()) {
	echo " selected='selected'";
    }
    echo ">";
    echo $justification->getNom();
    echo "</option>\n";
}
echo "</select>";
echo '<button type="submit">Modifier</button>';
	echo '</p>';
echo '</form>';
echo '</td></tr>';

echo '<tr><td>';
echo 'Commentaire : ';
echo '</td><td>';
echo '<form method="post" action="enregistrement_modif_traitement.php">';
echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
echo '<input type="hidden" name="modif" value="commentaire"/>';
echo '<input type="text" name="commentaire" size="30" value="'.$traitement->getCommentaire().'" />';
echo '<button type="submit">Modifier</button>';
	echo '</p>';
echo '</form>';
echo '</td></tr>';

echo '<tr><td>';
echo 'Notification : ';
echo '</td><td>';
echo '<table style="background-color:#c7e3ec;">';
$eleve_prec_id = null;
foreach ($traitement->getAbsenceEleveNotifications() as $notification) {
    echo '<tr><td>';
    echo "<a href='visu_notification.php?id_notification=".$notification->getId()."";
    if($menu){
                echo"&menu=false";
            } 
    echo"' style='display: block; height: 100%;'> ";
    if ($notification->getDateEnvoi() != null) {
	echo (strftime("%a %d/%m/%Y %H:%M", $notification->getDateEnvoi('U')));
    } else {
	echo (strftime("%a %d/%m/%Y %H:%M", $notification->getCreatedAt('U')));
    }
    if ($notification->getTypeNotification() != null) {
	echo ', type : '.$notification->getTypeNotification();
    }
    echo ', statut : '.$notification->getStatutEnvoi();
    echo "</a>";
    echo '</td></tr>';
}
echo '<tr><td>';
echo '<form method="post" action="enregistrement_modif_notification.php">';
echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
echo '<input type="hidden" name="creation_notification" value="oui"/>';
echo '<button type="submit">Nouvelle notification à la famille</button>';
	echo '</p>';
echo '</form>';
echo '</td></tr>';

echo '</table>';
echo '</td></tr>';

echo '<tr><td>';
echo 'Créé par : ';
echo '</td><td>';
if ($traitement->getUtilisateurProfessionnel() != null) {
    echo $traitement->getUtilisateurProfessionnel()->getCivilite();
    echo ' ';
    echo $traitement->getUtilisateurProfessionnel()->getNom();
}
echo '</td></tr>';

echo '<tr><td>';
echo 'Créé le : ';
echo '</td><td>';
echo (strftime("%a %d/%m/%Y %H:%M", $traitement->getCreatedAt('U')));
echo '</td></tr>';

if ($traitement->getCreatedAt() != $traitement->getUpdatedAt()) {
    echo '<tr><td>';
    echo 'Modifiée le : ';
    echo '</td><td>';
    echo (strftime("%a %d/%m/%Y %H:%M", $traitement->getUpdatedAt('U')));
    echo '</td></tr>';
}

if ($traitement->getModifiable()) {
    echo '<tr><td colspan="2" align="center">';
    echo '<form method="post" action="enregistrement_modif_traitement.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
    echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
    echo '<input type="hidden" name="modif" value="supprimer"/>';
    echo '<button type="submit">Supprimer le traitement</button>';
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
