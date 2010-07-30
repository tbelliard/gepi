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

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite") {
    die("acces interdit");
}

if (isset($_POST["creation_traitement"]) || isset($_POST["ajout_saisie_traitement"])) {
    include('creation_traitement.php');
}

//récupération des paramètres de la requète
//contrairement aux autres pages, on ne recupere pas les parametres dans la session
$nom_eleve = isset($_POST["nom_eleve"]) ? $_POST["nom_eleve"] :(isset($_GET["nom_eleve"]) ? $_GET["nom_eleve"] : NULL);
$id_eleve = isset($_POST["id_eleve"]) ? $_POST["id_eleve"] :(isset($_GET["id_eleve"]) ? $_GET["id_eleve"] : NULL);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] : NULL);
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] : NULL);
$id_aid = isset($_POST["id_aid"]) ? $_POST["id_aid"] :(isset($_GET["id_aid"]) ? $_GET["id_aid"] : NULL);
$type_selection = isset($_POST["type_selection"]) ? $_POST["type_selection"] :(isset($_GET["type_selection"]) ? $_GET["type_selection"] : NULL);
$date_absence_eleve = isset($_POST["date_absence_eleve"]) ? $_POST["date_absence_eleve"] :(isset($_GET["date_absence_eleve"]) ? $_GET["date_absence_eleve"] :(isset($_SESSION["date_absence_eleve"]) ? $_SESSION["date_absence_eleve"] : NULL));
if ($date_absence_eleve != null) {$_SESSION["date_absence_eleve"] = $date_absence_eleve;}

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
include('menu_bilans.inc.php');
//===========================
echo "<div class='css-panes' id='containDiv'>\n";

echo "<table cellspacing='15px' cellpadding='5px'><tr>";

//on affiche une boite de selection avec les groupes et les creneaux
$groupe_col = $utilisateur->getGroupes();
if (!$groupe_col->isEmpty()) {
    echo "<td style='border : 1px solid; padding : 10 px;'>";
    echo "<form action=\"./absences_du_jour.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>\n";
    echo '<input type="hidden" name="type_selection" value="id_groupe"/>';
    echo ("Groupe : <select name=\"id_groupe\" onchange='submit()'>");
    echo "<option value='-1'>choisissez un groupe</option>\n";
    foreach ($groupe_col as $group) {
	    echo "<option value='".$group->getId()."'";
	    if ($id_groupe == $group->getId()) echo " SELECTED ";
	    echo ">";
	    echo $group->getNameAvecClasses();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    echo '<button type="submit">Afficher les eleves</button>';
	echo "</p>\n";
    echo "</form>";
    echo "</td>";
}

//on affiche une boite de selection avec les classe
$classe_col = ClasseQuery::create()->distinct()->find();
if (!$classe_col->isEmpty()) {
    echo "<td style='border : 1px solid; padding : 10 px;'>";
    echo "<form action=\"./absences_du_jour.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>\n";
    echo '<input type="hidden" name="type_selection" value="id_classe"/>';
    echo ("Classe : <select name=\"id_classe\" onchange='submit()'>");
    echo "<option value='-1'>choisissez une classe</option>\n";
    foreach ($classe_col as $classe) {
	    echo "<option value='".$classe->getId()."'";
	    if ($id_classe == $classe->getId()) echo " SELECTED ";
	    echo ">";
	    echo $classe->getNomComplet();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    echo '<button type="submit">Afficher les eleves</button>';
	echo "</p>\n";
    echo "</form>";
    echo "</td>";
}


//on affiche une boite de selection avec les aid et les creneaux
$aid_col = $utilisateur->getAidDetailss();
if (!$aid_col->isEmpty()) {
    echo "<td style='border : 1px solid;'>";
    echo "<form action=\"./absences_du_jour.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>\n";
    echo '<input type="hidden" name="type_selection" value="id_aid"/>';
    echo ("Aid : <select name=\"id_aid\" onchange='submit()'>");
    echo "<option value='-1'>choisissez une aid</option>\n";
    foreach ($aid_col as $aid) {
	    echo "<option value='".$aid->getPrimaryKey()."'";
	    if ($id_aid == $aid->getPrimaryKey()) echo " SELECTED ";
	    echo ">";
	    echo $aid->getNom();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    echo '<button type="submit">Afficher les eleves</button>';
	echo "</p>\n";
    echo "</form>";
    echo "</td>";
}

//on affiche une boite de selection pour l'eleve
echo "<td style='border : 1px solid; padding : 10 px;'>";
echo "<form action=\"./absences_du_jour.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>\n";
echo 'Nom : <input type="hidden" name="type_selection" value="nom_eleve"/> ';
echo '<input type="text" name="nom_eleve" size="10" value="'.$nom_eleve.'"/> ';
echo '<button type="submit">Rechercher</button>';
	echo "</p>\n";
echo '</form>';
echo '</td>';

echo "</tr></table>";

if (isset($message_erreur_traitement)) {
    echo $message_erreur_traitement;
}

if (isset($message_enregistrement)) {
    echo($message_enregistrement);
}

//afichage des eleves. Il nous faut au moins un groupe ou une aid
$eleve_col = new PropelCollection();

if ($type_selection == 'id_eleve') {
    $query = EleveQuery::create();
    if ($utilisateur->getStatut() != "cpe" || getSettingValue("GepiAccesAbsTouteClasseCpe")!='yes') {
	$query->filterByUtilisateurProfessionnel($utilisateur);
    }
    $eleve_col->append($query->findPk($id_eleve));
} else if ($type_selection == 'nom_eleve') {
    $query = EleveQuery::create();
    if ($utilisateur->getStatut() != "cpe" || getSettingValue("GepiAccesAbsTouteClasseCpe")!='yes') {
	$query->filterByUtilisateurProfessionnel($utilisateur);
    }
    $eleve_col = $query->filterByNomOrPrenomLike($nom_eleve)->limit(20)->find();
} elseif ($current_groupe != null) {
    $eleve_col = $current_groupe->getEleves();
} elseif ($current_aid != null) {
    $eleve_col = $current_aid->getEleves();
} elseif ($current_classe != null) {
    $eleve_col = $current_classe->getEleves();
} else {
    //on fait une requete pour recuperer les eleves qui sont absents aujourd'hui
    $dt_debut = clone $dt_date_absence_eleve;
    $dt_debut->setTime(0,0,0);
    $dt_fin = clone $dt_date_absence_eleve;
    $dt_fin->setTime(23,59,59);
    $query = EleveQuery::create();
    if ($utilisateur->getStatut() != "cpe" || getSettingValue("GepiAccesAbsTouteClasseCpe")!='yes') {
	$query->filterByUtilisateurProfessionnel($utilisateur);
    }
    $eleve_col = $query
	    ->useAbsenceEleveSaisieQuery()
	    ->filterByPlageTemps($dt_debut, $dt_fin)
	    ->endUse()->distinct()->find();
}

?>
	<div class="centre_tout_moyen" style="width : 900px;">
			    <!-- <p class="expli_page choix_fin"> -->
				    <form action="./absences_du_jour.php" method="post" style="width: 100%;">
			    <p class="expli_page choix_fin">
				    <input size="8" id="date_absence_eleve_1" name="date_absence_eleve" value="<?php echo $dt_date_absence_eleve->format('d/m/Y')?>" />
				    <script type="text/javascript">
					Calendar.setup({
					    inputField     :    "date_absence_eleve_1",     // id of the input field
					    ifFormat       :    "%d/%m/%Y",      // format of the input field
					    button         :    "date_absence_eleve_1",  // trigger for the calendar (button ID)
					    align          :    "Bl",           // alignment (defaults to "Bl")
					    singleClick    :    true
					});
				    </script>
				    <button type="submit">Changer</button>
			</p>
				    </form>
				<!--     <br/> -->
			<!-- </p> -->
<?php if (!$eleve_col->isEmpty()) { ?>
			<form method="post" action="./absences_du_jour.php" id="liste_absence_eleve">
			  <p>
			<button type="submit" name="creation_traitement" value="creation_traitement">Creer un traitement</button>
			 
<?php $id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
if ($id_traitement != null && AbsenceEleveTraitementQuery::create()->findPk($id_traitement) != null) {
    $traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
    echo '<button type="submit" name="ajout_saisie_traitement" value="ajout_saisie_traitement">Ajouter les saisies au traitement n° '.$id_traitement.' ('.$traitement->getDescription().')</button>';
    echo '<input type="hidden" name="id_traitement" value="'.$id_traitement.'"/>';
}?>
 </p>
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
    $nb_checkbox = 0; //nombre de checkbox
    foreach($eleve_col as $eleve) {
		//$eleve = new Eleve();
			$resp_etab = true;
			foreach ($eleve->getAbsenceEleveSaisiesDuJour($dt_date_absence_eleve) as $absence) {
			    if (!$absence->getResponsabiliteEtablissement()) {
				$resp_etab = false;
				break;
			    }
			}
			if ($resp_etab) {
			    //l'eleve n'a aucune absence deresponsabilisant l'etablissement
			    //donc on ne l'affiche pas
			    continue;
			}
			$saisie_affiches = array ();
			if ($eleve_col->getPosition() %2 == '1') {
				$background_couleur="#E8F1F4";
			} else {
				$background_couleur="#C6DCE3";
			}
			echo "<tr style='background-color :$background_couleur'>\n";


			$Yesterday = date("Y-m-d",mktime(0,0,0,$dt_date_absence_eleve->format("m") ,$dt_date_absence_eleve->format("d")-1,$dt_date_absence_eleve->format("Y")));
			$compter_hier = $eleve->getAbsenceEleveSaisiesDuJour($Yesterday)->count();
			$color_hier = ($compter_hier >= 1) ? ' style="background-color: red; text-align: center; color: white; font-weight: bold;"' : '';
			$aff_compter_hier = ($compter_hier >= 1) ? $compter_hier.' enr.' : '';
?>
			<td<?php echo $color_hier; ?>><?php echo $aff_compter_hier; ?></td>
			<td class='td_abs_eleves'>
<?php
			echo strtoupper($eleve->getNom()).' '.ucfirst($eleve->getPrenom()).' ('.$eleve->getCivilite().')';
			echo ' ';
			echo $eleve->getClasseNomComplet($dt_date_absence_eleve);
			if ($utilisateur->getAccesFicheEleve($eleve)) {
			    //echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."' target='_blank'>";
			    echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."' >";
			    echo ' (voir&nbsp;fiche)';
			    echo "</a>";
			}
			
			echo("</td>");

			$col_creneaux = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();
			
			for($i = 0; $i<$col_creneaux->count(); $i++){
					$edt_creneau = $col_creneaux[$i];
					$absences_du_creneau = $eleve->getAbsenceEleveSaisiesDuCreneau($edt_creneau, $dt_date_absence_eleve);

					$red = false;
					$violet = false;
					foreach ($absences_du_creneau as $absence) {
					    if ($absence->isSaisiesContradictoires()) {
					    //if (!($absence->getSaisiesContradictoires()->isEmpty())) {
						$violet = true;
						break;
					    }
					    if ($red || !$absence->getResponsabiliteEtablissement()) {
						$red = true;
					    }
					}
					if ($violet) {
					    $style = 'style="background-color : purple"';
					} elseif ($red) {
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
					    $nb_checkbox = $nb_checkbox + 1;
					    if ($saisie->getNotifiee()) {
						$prop = 'saisie_notifie';
					    } elseif ($saisie->getTraitee()) {
						$prop = 'saisie_traite';
					    } else {
						$prop = 'saisie_vierge';
					    }
					    //echo '<nobr>';
					    echo '<nobr><input name="select_saisie[]" value="'.$saisie->getPrimaryKey().'" type="checkbox" id="'.$prop.'_eleve_id_'.$eleve->getPrimaryKey().'_saisie_id_'.$saisie->getPrimaryKey().'"/>';
					    echo ("<a style='font-size:88%;' href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."'>".$saisie->getPrimaryKey());
					    if ($prop == 'saisie_notifie') {
						echo " (notifiée)";
					    }
					    echo '</nobr> ';
					    echo $saisie->getTypesDescription();
					    echo '</a>';
					    //echo '</nobr>';
					    echo '<br/>';
					}

					echo '</td>';
			    }

					       // Avec ou sans photo
			if ((getSettingValue("active_module_trombinoscopes")=='y')) {
			    $nom_photo = $eleve->getNomPhoto(1);
			    //$photos = "../photos/eleves/".$nom_photo;
			    $photos = $nom_photo;
			    //if (($nom_photo == "") or (!(file_exists($photos)))) {
			    if (($nom_photo == NULL) or (!(file_exists($photos)))) {
				    $photos = "../mod_trombinoscopes/images/trombivide.jpg";
			    }
			    $valeur = redimensionne_image_petit($photos);

			    echo '<td>
				    <img src="'.$photos.'" style="width: '.$valeur[0].'px; height: '.$valeur[1].'px; border: 0px" alt="" title="" />
			    </td>';
			}

			echo '<td>';
			echo 'Sélectionner: ';
			echo '<a href="" onclick="SetAllCheckBoxes(\'liste_absence_eleve\', \'select_saisie[]\', \'eleve_id_'.$eleve->getPrimaryKey().'\', true); return false;">Tous</a>, ';
			echo '<a href="" onclick="SetAllCheckBoxes(\'liste_absence_eleve\', \'select_saisie[]\', \'eleve_id_'.$eleve->getPrimaryKey().'\', false); return false;">Aucun</a>, ';
			echo '<a href="" onclick="SetAllCheckBoxes(\'liste_absence_eleve\', \'select_saisie[]\', \'eleve_id_'.$eleve->getPrimaryKey().'\', false);
			    SetAllCheckBoxes(\'liste_absence_eleve\', \'select_saisie[]\', \'saisie_vierge_eleve_id_'.$eleve->getPrimaryKey().'\', true);
			    return false;">Non traités</a>, ';
			echo '<a href="" onclick="SetAllCheckBoxes(\'liste_absence_eleve\', \'select_saisie[]\', \'eleve_id_'.$eleve->getPrimaryKey().'\', true);
			    SetAllCheckBoxes(\'liste_absence_eleve\', \'select_saisie[]\', \'saisie_notifie_eleve_id_'.$eleve->getPrimaryKey().'\', false);
			    return false;">Non notifiés</a>';
			echo '</td>';
			echo "</tr>";
    }

		   echo " </tbody>";
    echo "</table>";
    echo '<table><tr>';
    echo '<td>Legende : </td>';
    echo '<td style="border : 1px solid; background-color : red;">absent</td>';
    echo '<td style="border : 1px solid; background-color : green;">present</td>';
    echo '<td style="border : 1px solid; background-color : purple;">Saisies conflictuelles</td>';
    echo '<td style="border : 1px solid;">Sans couleur : pas de saisie</td>';
    echo '</tr></table>';
    echo "<p>";
    echo '<button type="submit" name="creation_traitement" value="creation_traitement">Creer un traitement</button>';
    $id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
    if ($id_traitement != null && AbsenceEleveTraitementQuery::create()->findPk($id_traitement) != null) {
	$traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
	echo '<button type="submit" name="ajout_saisie_traitement" value="ajout_saisie_traitement">Ajouter les saisies au traitement n° '.$id_traitement.' ('.$traitement->getDescription().')</button>';
	echo '<input type="hidden" name="id_traitement" value="'.$id_traitement.'"/>';
    }
    echo '<input type="hidden" name="nb_checkbox" value="'.$nb_checkbox.'"/>';

} else {
    echo 'Aucune absence';
}
echo "</p>";
echo "</form>";
echo "</div>\n";
echo "</div>\n";

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