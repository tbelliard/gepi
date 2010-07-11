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
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :(isset($_SESSION["id_groupe_abs"]) ? $_SESSION["id_groupe_abs"] : NULL));
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :(isset($_SESSION["id_classe_abs"]) ? $_SESSION["id_classe_abs"] : NULL));
$id_aid = isset($_POST["id_aid"]) ? $_POST["id_aid"] :(isset($_GET["id_aid"]) ? $_GET["id_aid"] :(isset($_SESSION["id_aid"]) ? $_SESSION["id_aid"] : NULL));
$type_selection = isset($_POST["type_selection"]) ? $_POST["type_selection"] :(isset($_GET["type_selection"]) ? $_GET["type_selection"] :(isset($_SESSION["type_selection"]) ? $_SESSION["type_selection"] : NULL));
$date_absence_eleve = isset($_POST["date_absence_eleve"]) ? $_POST["date_absence_eleve"] :(isset($_GET["date_absence_eleve"]) ? $_GET["date_absence_eleve"] :(isset($_SESSION["date_absence_eleve"]) ? $_SESSION["date_absence_eleve"] : NULL));
$cahier_texte = isset($_POST["cahier_texte"]) ? $_POST["cahier_texte"] :(isset($_GET["cahier_texte"]) ? $_GET["cahier_texte"] :NULL);

if (isset($id_groupe) && $id_groupe != null) $_SESSION['id_groupe_abs'] = $id_groupe;
if (isset($id_classe) && $id_classe != null) $_SESSION['id_classe_abs'] = $id_classe;
if (isset($id_aid) && $id_aid != null) $_SESSION['id_aid'] = $id_aid;
if (isset($type_selection) && $type_selection != null) $_SESSION['type_selection'] = $type_selection;
if (isset($date_absence_eleve) && $date_absence_eleve != null) $_SESSION['date_absence_eleve'] = $date_absence_eleve;


//initialisation des variables
$current_classe = null;
$current_groupe = null;
$current_aid = null;
if ($date_absence_eleve != null) {
    $dt_date_absence_eleve = new DateTime(str_replace("/",".",$date_absence_eleve));
} else {
    $dt_date_absence_eleve = new DateTime('now');
}

if ($type_selection == 'id_groupe') {
    if ($utilisateur->getStatut() == "professeur") {
	$current_groupe = GroupeQuery::create()->filterByUtilisateurProfessionnel($utilisateur)->findPk($id_groupe);
    } else {
	$current_groupe = GroupeQuery::create()->findPk($id_groupe);
    }
} else if ($type_selection == 'id_aid') {
    $current_aid = AidDetailsQuery::create()->findPk($id_aid);
} else if ($type_selection == 'id_classe') {
    $current_classe = ClasseQuery::create()->findPk($id_classe);
} else {
    if ($id_groupe == null) {
	if (isset($_SESSION['id_groupe_session'])) {
	    $id_groupe =  $_SESSION['id_groupe_session'];
	    $current_groupe = GroupeQuery::create()->filterByUtilisateurProfessionnel($utilisateur)->findPk($id_groupe);
	}
    }
}

if ($cahier_texte != null && $cahier_texte != "") {
    $location = "Location: ../cahier_texte/index.php";
    if ($id_groupe != null) {
	$location .= "?id_groupe=".$id_groupe;
    } else if ($current_cours != null) {
	$location .= "?id_groupe=".$current_cours->getIdGroupe();
    }
    header($location);
    die();
}


//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
$javascript_specifique[] = "mod_abs2/lib/include";
$titre_page = "Les absences";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

include('menu_abs2.inc.php');
//===========================
echo "<div class='css-panes' id='containDiv'>\n";

echo "<table cellspacing='15px' cellpadding='5px'><tr>";
//on affiche une boite de selection avec les groupes et les creneaux
if (!$utilisateur->getGroupes()->isEmpty()) {
    echo "<td style='border : 1px solid; padding : 10 px;'>";
    echo "<form action=\"./absences_du_jour.php\" method=\"post\" style=\"width: 100%;\">\n";
    echo '<input type="hidden" name="type_selection" value="id_groupe"/>';
    echo ("Groupe : <select name=\"id_groupe\">");
    echo "<option value='-1'>choisissez un groupe</option>\n";
    foreach ($utilisateur->getGroupes() as $group) {
	    echo "<option value='".$group->getId()."'";
	    if ($id_groupe == $group->getId()) echo " SELECTED ";
	    echo ">";
	    echo $group->getNameAvecClasses();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";

    echo '<input size="8" id="date_absence_eleve_1" name="date_absence_eleve" value="'.$dt_date_absence_eleve->format('d/m/Y').'" />&nbsp;';
    echo '
    <script type="text/javascript">
	Calendar.setup({
	    inputField     :    "date_absence_eleve_1",     // id of the input field
	    ifFormat       :    "%d/%m/%Y",      // format of the input field
	    button         :    "date_absence_eleve_1",  // trigger for the calendar (button ID)
	    align          :    "Bl",           // alignment (defaults to "Bl")
	    singleClick    :    true
	});
    </script>';
    echo '<button type="submit">Afficher les eleves</button>';
    echo "</form>";
    echo "</td>";
}

//on affiche une boite de selection avec les classe
if ($utilisateur->getStatut() == "cpe") {
    echo "<td style='border : 1px solid; padding : 10 px;'>";
    echo "<form action=\"./absences_du_jour.php\" method=\"post\" style=\"width: 100%;\">\n";
    echo '<input type="hidden" name="type_selection" value="id_classe"/>';
    echo ("Classe : <select name=\"id_classe\">");
    echo "<option value='-1'>choisissez une classe</option>\n";
    foreach ($utilisateur->getClasses() as $classe) {
	    echo "<option value='".$classe->getId()."'";
	    if ($id_classe == $classe->getId()) echo " SELECTED ";
	    echo ">";
	    echo $classe->getNomComplet();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";

    echo '<input size="8" id="date_absence_eleve_2" name="date_absence_eleve" value="'.$dt_date_absence_eleve->format('d/m/Y').'" />&nbsp;';
    echo '
    <script type="text/javascript">
	Calendar.setup({
	    inputField     :    "date_absence_eleve_2",     // id of the input field
	    ifFormat       :    "%d/%m/%Y",      // format of the input field
	    button         :    "date_absence_eleve_2",  // trigger for the calendar (button ID)
	    align          :    "Bl",           // alignment (defaults to "Bl")
	    singleClick    :    true
	});
    </script>';
    echo '<button type="submit">Afficher les eleves</button>';
    echo "</form>";
    echo "</td>";
}

//on affiche une boite de selection avec les aid et les creneaux
if (getSettingValue("abs2_saisie_prof_hors_cours")=='y' && !$utilisateur->getAidDetailss()->isEmpty()) {
    echo "<td style='border : 1px solid;'>";
    echo "<form action=\"./absences_du_jour.php\" method=\"post\" style=\"width: 100%;\">\n";
    echo '<input type="hidden" name="type_selection" value="id_aid"/>';
    echo ("Aid : <select name=\"id_aid\">");
    echo "<option value='-1'>choisissez une aid</option>\n";
    foreach ($utilisateur->getAidDetailss() as $aid) {
	    echo "<option value='".$aid->getPrimaryKey()."'";
	    if ($id_aid == $aid->getPrimaryKey()) echo " SELECTED ";
	    echo ">";
	    echo $aid->getNom();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    

    echo '<input size="8" id="date_absence_eleve_3" name="date_absence_eleve" value="'.$dt_date_absence_eleve->format('d/m/Y').'" />&nbsp;';
    echo '<script type="text/javascript">
	Calendar.setup({
	    inputField     :    "date_absence_eleve_3",     // id of the input field
	    ifFormat       :    "%d/%m/%Y",      // format of the input field
	    button         :    "date_absence_eleve_3",  // trigger for the calendar (button ID)
	    align          :    "Bl",           // alignment (defaults to "Bl")
	    singleClick    :    true
	});
    </script>';
    echo '<button type="submit">Afficher les eleves</button>';
    echo "</form>";
    echo "</td>";
}

echo "</tr></table>";

if (isset($message_enregistrement)) {
    echo($message_enregistrement);
}

//afichage des eleves. Il nous faut au moins un groupe ou une aid
$eleve_col = new PropelCollection();
if (isset($current_groupe) && $current_groupe != null) {
    $eleve_col = $current_groupe->getEleves();
} else if (isset($current_aid) && $current_aid != null) {
    $eleve_col = $current_aid->getEleves();
} else if (isset($current_classe) && $current_classe != null) {
    $eleve_col = $current_classe->getEleves();
} else {
    //on fait une requete pour recuperer les eleves qui sont absents aujourd'hui
    $dt_debut = clone $dt_date_absence_eleve;
    $dt_debut->setTime(0,0,0);
    $dt_fin = clone $dt_date_absence_eleve;
    $dt_fin->setTime(23,59,59);
    $eleve_col = EleveQuery::create()->useAbsenceEleveSaisieQuery()
	    ->filterByFinAbs($dt_debut, Criteria::GREATER_EQUAL)
	    ->filterByFinAbs($dt_fin, Criteria::LESS_EQUAL)
	    ->endUse()->distinct()->find();
}

//afichage de la saisie des absences des eleves
if (!$eleve_col->isEmpty()) {
    ?>
	<div class="centre_tout_moyen" style="width : 900px;">
		    <form method="post" action="creation_traitement.php" id="liste_absence_eleve">
			    <p class="expli_page choix_fin">
				    <strong><?php echo strftime  ('%A %d %B %G',  $dt_date_absence_eleve->format('U')); ?></strong>
				    <br/></p>
<?php echo '<button type="submit" name="creation_traitement" value="creation_traitement">Creer un traitement</button>';

$id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
if ($id_traitement != null && AbsenceEleveTraitementQuery::create()->findPk($id_traitement) != null) {
    $traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
    echo '<button type="submit" name="ajout_saisie_traitement" value="ajout_saisie_traitement">Ajouter les saisies au traitement n° '.$id_traitement.' ('.$traitement->getDescription().')</button>';
    echo '<input type="hidden" name="id_traitement" value="'.$id_traitement.'"/>';
}?>

    <!-- Afichage du tableau de la liste des élèves -->
    <!-- <table style="text-align: left; width: 600px;" border="0" cellpadding="0" cellspacing="1"> -->
	    <table class="tb_absences" summary="Liste des élèves pour l'appel. Colonne 1 : élèves, colonne 2 : absence, colonne3 : retard, colonnes suivantes : suivi de la journée par créneaux, dernière colonne : photos si actif">
		    <caption class="invisible no_print">Absences</caption>
		    <tbody>
			    <tr class="titre_tableau_gestion" style="white-space: nowrap;">
				    <th style="text-align : center;" >Veille</th>
				    <th style="text-align : center;" abbr="élèves">Liste des &eacute;l&egrave;ves</th>
				    <th colspan="<?php echo (EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime()->count());?>" class="th_abs_suivi" abbr="Créneaux">Suivi sur la journ&eacute;e</th>
			    </tr>
			    <tr>
				    <td></td>
				    <td></td>
				    <?php foreach(EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $edt_creneau){
					    echo '		<td class="td_nom_creneau" style="text-align: center;">'.$edt_creneau->getNomDefiniePeriode().'</td>';
				    }?>
			    </tr>

    <?php
    $item_per_page = 0; //nombre de checkbox
    foreach($eleve_col as $eleve) {
		//$eleve = new Eleve();
			$saisie_affiches = array ();
			if ($eleve_col->getPosition() %2 == '1') {
				$background_couleur="#E8F1F4";
			} else {
				$background_couleur="#C6DCE3";
			}
			echo "<tr style='background-color :$background_couleur'>\n";


			$Yesterday = date("Y-m-d",mktime(0,0,0,$dt_date_absence_eleve->format("m") ,$dt_date_absence_eleve->format("d")-1,$dt_date_absence_eleve->format("Y")));
			$compter_hier = $eleve->getAbsenceSaisiesDuJour($Yesterday)->count();
			$color_hier = ($compter_hier >= 1) ? ' style="background-color: red; text-align: center; color: white; font-weight: bold;"' : '';
			$aff_compter_hier = ($compter_hier >= 1) ? $compter_hier.' enr.' : '';
?>
			<td<?php echo $color_hier; ?>><?php echo $aff_compter_hier; ?></td>
			<td class='td_abs_eleves'>
<?php

			echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."&amp;onglet=absences' target='_blank'>";
			echo strtoupper($eleve->getNom()).' '.ucfirst($eleve->getPrenom())
				.'</a> ('.$eleve->getCivilite().')';
			
			echo("</td>");

			$col_creneaux = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();
			
			for($i = 0; $i<$col_creneaux->count(); $i++){
					$edt_creneau = $col_creneaux[$i];
					$absences_du_creneau = $eleve->getAbsenceSaisiesDuCreneau($edt_creneau, $dt_date_absence_eleve);

					$red = false;
					foreach ($absences_du_creneau as $absence) {
					    if (!$absence->getResponsabiliteEtablissement()) {
						$red = true;
						break;
					    }
					}
					if ($red) {
					    $style = 'style="background-color : red"';
					} else {
					    $dt_green = clone $dt_date_absence_eleve;
					    $dt_green->setTime($edt_creneau->getHeuredebutDefiniePeriode('H'), $edt_creneau->getHeuredebutDefiniePeriode('i'), 0);
					    if ($eleve->getPresent($dt_green)) {
						$style = 'style="background-color : green"';
					    } else {
						$style = '';
					    }
					}
					echo '<td '.$style.'>';

					//si il y a des absences de l'utilisateurs on va proposer de les modifier
					foreach ($absences_du_creneau as $saisie) {
					    if (in_array($saisie->getPrimaryKey(), $saisie_affiches)) {
						//on affiche les saisies une seule fois
						continue;
					    }
					    $saisie_affiches[] = $saisie->getPrimaryKey();
					    $item_per_page = $item_per_page + 1;
					    if ($saisie->getNotifiee()) {
						$prop = 'saisie_notifie';
					    } elseif ($saisie->getTraitee()) {
						$prop = 'saisie_traite';
					    } else {
						$prop = 'saisie_vierge';
					    }
					    echo '<nobr>';
					    echo '<input name="select_saisie[]" value="'.$saisie->getPrimaryKey().'" type="checkbox" id="'.$prop.'_eleve_id_'.$eleve->getPrimaryKey().'_saisie_id_'.$saisie->getPrimaryKey().'"/>';
					    echo ("<a style='font-size:88%;' href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."'>".$saisie->getPrimaryKey());
					    if ($prop == 'saisie_notifie') {
						echo " (notifiée)";
					    }
					    echo '</a>';
					    echo '</nobr>';
					    echo '<br/>';
					}

					echo '</td>';
			    }

					       // Avec ou sans photo
			if ((getSettingValue("active_module_trombinoscopes")=='y')) {
			    $nom_photo = $eleve->getNomPhoto(1);
			    $photos = "../photos/eleves/".$nom_photo;
			    if (($nom_photo == "") or (!(file_exists($photos)))) {
				    $photos = "../mod_trombinoscopes/images/trombivide.jpg";
			    }
			    $valeur = redimensionne_image_petit($photos);

			    echo '<td>
				    <img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" />
			    </td>';
			}

			echo '<td>';
			echo 'Sélectionner: ';
			echo '<a href="" onClick="SetAllCheckBoxes(\'liste_absence_eleve\', \'select_saisie[]\', \'eleve_id_'.$eleve->getPrimaryKey().'\', true); return false;">Tous</a>, ';
			echo '<a href="" onClick="SetAllCheckBoxes(\'liste_absence_eleve\', \'select_saisie[]\', \'eleve_id_'.$eleve->getPrimaryKey().'\', false); return false;">Aucun</a>, ';
			echo '<a href="" onClick="SetAllCheckBoxes(\'liste_absence_eleve\', \'select_saisie[]\', \'eleve_id_'.$eleve->getPrimaryKey().'\', false);
			    SetAllCheckBoxes(\'liste_absence_eleve\', \'select_saisie[]\', \'saisie_vierge_eleve_id_'.$eleve->getPrimaryKey().'\', true);
			    return false;">Non traités</a>, ';
			echo '<a href="" onClick="SetAllCheckBoxes(\'liste_absence_eleve\', \'select_saisie[]\', \'eleve_id_'.$eleve->getPrimaryKey().'\', true);
			    SetAllCheckBoxes(\'liste_absence_eleve\', \'select_saisie[]\', \'saisie_notifie_eleve_id_'.$eleve->getPrimaryKey().'\', false);
			    return false;">Non notifiés</a>';
			echo '</td>';
			echo "</tr>";
    }

    echo "</table>";
    
    echo '<button type="submit" name="creation_traitement" value="creation_traitement">Creer un traitement</button>';
    $id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
    if ($id_traitement != null && AbsenceEleveTraitementQuery::create()->findPk($id_traitement) != null) {
	$traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
	echo '<button type="submit" name="ajout_saisie_traitement" value="ajout_saisie_traitement">Ajouter les saisies au traitement n° '.$id_traitement.' ('.$traitement->getDescription().')</button>';
	echo '<input type="hidden" name="id_traitement" value="'.$id_traitement.'"/>';
    }
    echo '<input type="hidden" name="item_per_page" value="'.$item_per_page.'"/>';

    echo "</div>\n";
}

require_once("../lib/footer.inc.php");

//fonction redimensionne les photos petit format
function redimensionne_image_petit($photo) {
    // prendre les informations sur l'image
    $info_image = getimagesize($photo);
    // largeur et hauteur de l'image d'origine
    $largeur = $info_image[0];
    $hauteur = $info_image[1];
    // largeur et/ou hauteur maximum à afficher
             $taille_max_largeur = 45;
             $taille_max_hauteur = 45;

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