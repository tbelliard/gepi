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
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
$id_aid = isset($_POST["id_aid"]) ? $_POST["id_aid"] :(isset($_GET["id_aid"]) ? $_GET["id_aid"] :NULL);
$id_creneau = isset($_POST["id_creneau"]) ? $_POST["id_creneau"] :(isset($_GET["id_creneau"]) ? $_GET["id_creneau"] :NULL);
$id_cours = isset($_POST["id_cours"]) ? $_POST["id_cours"] :(isset($_GET["id_cours"]) ? $_GET["id_cours"] :NULL);
$type_selection = isset($_POST["type_selection"]) ? $_POST["type_selection"] :(isset($_GET["type_selection"]) ? $_GET["type_selection"] :NULL);
$d_date_absence_eleve = isset($_POST["d_date_absence_eleve"]) ? $_POST["d_date_absence_eleve"] : null;
$id_semaine = isset($_POST["id_semaine"]) ? $_POST["id_semaine"] : null;

//initialisation des variables
$current_cours = null;
$current_groupe = null;
$current_aid = null;

if ($type_selection == 'edt_cours') {
    $current_cours = new EdtEmplacementCours();
    $current_cours = EdtEmplacementCoursQuery::create()->filterByUtilisateurProfessionnel($utilisateur)->findPk($id_cours);
    if ($current_cours != null) {
	$current_creneau = $current_cours->getEdtCreneau();
	$current_groupe = $current_cours->getGroupe();

	//on va utiliser le numero de semaine precisée pour regler la date
	if ($id_semaine == null || $id_semaine == -1) {
	    $id_semaine = date('W');
	}
	$day_of_the_week = $current_cours->getJourSemaineNumeric();
	$week_of_the_year = $id_semaine;
	$current_week_of_the_year = date('W');
	$year = date('Y');
	//if faut peut etre decaler l'année
	if ($current_week_of_the_year > 30 && $week_of_the_year > 30) {
	    //ne rien faire on garde la meme annee
	} else if ($current_week_of_the_year < 30 && $week_of_the_year < 30) {
	    //ne rien faire on garde la meme annee
	} else if ($current_week_of_the_year > 30 && $week_of_the_year < 30) {
	    //on augmente d'un an
	    $year = $year + 1;
	} else if ($current_week_of_the_year < 30 && $week_of_the_year > 30) {
	    //on reduit  d'un an
	    $year = $year - 1;
	}
	if (strlen($week_of_the_year) == 1) {
	    $week_of_the_year = '0'.$week_of_the_year;
	}
	$d_date_absence_eleve = new DateTime($year.'-W'.$week_of_the_year.'-'.$day_of_the_week);
    }
} else if ($type_selection == 'id_groupe') {
    $current_groupe = GroupeQuery::create()->filterByUtilisateurProfessionnel($utilisateur)->findPk($id_groupe);
    $current_creneau = EdtCreneauQuery::create()->findPk($id_creneau);
    $d_date_absence_eleve = new DateTime(str_replace("/",".",$d_date_absence_eleve));
} else if ($type_selection == 'id_aid') {
    $current_aid = AidDetailsQuery::create()->findPk($id_aid);
    //todo : ajout d'un test sur le professeur
    $current_creneau = EdtCreneauQuery::create()->findPk($id_creneau);
    $d_date_absence_eleve = new DateTime(str_replace("/",".",$d_date_absence_eleve));
} else {
    if ($id_groupe == null) {
	if (isset($_SESSION['id_groupe_session'])) {
	    $id_groupe =  $_SESSION['id_groupe_session'];
	    $current_groupe = GroupeQuery::create()->filterByUtilisateurProfessionnel($utilisateur)->findPk($id_groupe);
	}
    }

    if ($id_creneau == null) {
	$current_creneau = EdtCreneauPeer::retrieveEdtCreneauActuel();
	if ($current_creneau != null) {
	    $id_creneau = $current_creneau->getIdDefiniePeriode();
	}
    }

    if ($id_cours == null) {
	$current_cours = $utilisateur->getEdtEmplacementCours();
	if ($current_cours != null) {
	    $current_creneau = $current_cours->getEdtCreneau();
	    $current_groupe = $current_cours->getGroupe();
	}
    }

    if ($d_date_absence_eleve != null) {
	    $d_date_absence_eleve = new DateTime(str_replace("/",".",$d_date_absence_eleve));
    } else {
	$d_date_absence_eleve = new DateTime('now');
    }

    //on va utiliser le numero de semaine precisée pour regler la date
    if ($id_semaine == null || $id_semaine == -1) {
	$id_semaine = date('W');
    }

}

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

// Etiquettes des onglets:
$onglet_abs='saisie';
include('menu_abs2.inc.php');
//===========================
echo "<div class='css-panes' id='containDiv'>\n";

echo "<table cellspacing='15px' cellpadding='5px'><tr>";
//on affiche une boite de selection avec les groupes et les creneaux
if (getSettingValue("abs2_saisie_prof_hors_cours")=='y' && !$utilisateur->getGroupes()->isEmpty()) {
    echo "<td style='border : 1px solid; padding : 10 px;'>";    echo "<form action=\"./saisie_absences.php\" method=\"post\" style=\"width: 100%;\">\n";
    echo '<input type="hidden" name="type_selection" value="id_groupe"/>';
    echo ("<select name=\"id_groupe\">");
    echo "<option value='-1'>choisissez un groupe</option>\n";
    foreach ($utilisateur->getGroupes() as $group) {
	    echo "<option value='".$group->getId()."'";
	    if ($id_groupe == $group->getId()) echo " SELECTED ";
	    echo ">";
	    echo $group->getNameAvecClasses();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    echo ("<select name=\"id_creneau\">");
    $edt_creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();

    echo "<option value='-1'>choisissez un creneau</option>\n";
    foreach ($edt_creneau_col as $edt_creneau) {
	//$edt_creneau = new EdtCreneau();
	    echo "<option value='".$edt_creneau->getIdDefiniePeriode()."'";
	    if ($id_creneau == $edt_creneau->getIdDefiniePeriode()) echo " SELECTED ";
	    echo ">";
	    echo $edt_creneau->getDescription();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    if($d_date_absence_eleve == null) {
	    $d_date_absence_eleve = new DateTime();
    }
    echo '<input size="8" id="d_date_absence_eleve" name="d_date_absence_eleve" value="'.$d_date_absence_eleve->format('d/m/Y').'" />&nbsp;';
    echo '
    <script type="text/javascript">
	Calendar.setup({
	    inputField     :    "d_date_absence_eleve",     // id of the input field
	    ifFormat       :    "%d/%m/%Y",      // format of the input field
	    button         :    "d_date_absence_eleve",  // trigger for the calendar (button ID)
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
    echo "<td style='border : 1px solid;'>";    echo "<form action=\"./saisie_absences.php\" method=\"post\" style=\"width: 100%;\">\n";
    echo '<input type="hidden" name="type_selection" value="id_aid"/>';
    echo ("<select name=\"id_aid\">");
    echo "<option value='-1'>choisissez une aid</option>\n";
    foreach ($utilisateur->getAidDetailss() as $aid) {
	    echo "<option value='".$aid->getPrimaryKey()."'";
	    if ($id_aid == $aid->getPrimaryKey()) echo " SELECTED ";
	    echo ">";
	    echo $aid->getNom();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    echo ("<select name=\"id_creneau\">");
    $edt_creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();

    echo "<option value='-1'>choisissez un creneau</option>\n";
    foreach ($edt_creneau_col as $edt_creneau) {
	//$edt_creneau = new EdtCreneau();
	    echo "<option value='".$edt_creneau->getIdDefiniePeriode()."'";
	    if ($id_creneau == $edt_creneau->getIdDefiniePeriode()) echo " SELECTED ";
	    echo ">";
	    echo $edt_creneau->getDescription();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    if($d_date_absence_eleve == null && $d_date_absence_eleve = '') {
	    $d_date_absence_eleve = new DateTime();
    }
    echo '<input size="8" id="d_date_absence_eleve_2" name="d_date_absence_eleve" value="'.$d_date_absence_eleve->format('d/m/Y').'" />&nbsp;';
    echo '
    <script type="text/javascript">
	Calendar.setup({
	    inputField     :    "d_date_absence_eleve_2",     // id of the input field
	    ifFormat       :    "%d/%m/%Y",      // format of the input field
	    button         :    "d_date_absence_eleve_2",  // trigger for the calendar (button ID)
	    align          :    "Bl",           // alignment (defaults to "Bl")
	    singleClick    :    true
	});
    </script>';
    echo '<button type="submit">Afficher les eleves</button>';
    echo "</form>";
    echo "</td>";
}

//on affiche une boite de selection avec les cours
$edt_cours_col = $utilisateur->getEdtEmplacementCourssPeriodeCalendrierActuelle();
if (!$edt_cours_col->isEmpty()) {
    echo "<td style='border : 1px solid;'>";
    echo "<form action=\"./saisie_absences.php\" method=\"post\" style=\"width: 100%;\">\n";
    echo '<input type="hidden" name="type_selection" value="edt_cours"/>';
    echo ("<select name=\"id_cours\">");
    echo "<option value='-1'>choisissez un cours</option>\n";
    foreach ($edt_cours_col as $edt_cours) {
	    echo "<option value='".$edt_cours->getIdCours()."'";
	    if ($id_cours == $edt_cours->getIdCours()) echo " SELECTED ";
	    echo ">";
	    echo $edt_cours->getDescription();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";


    $col = EdtSemaineQuery::create()->find();    echo ("<select name=\"id_semaine\">");
    echo "<option value='-1'>choisissez une semaine</option>\n";
    //on va commencer la liste à la semaine 31 (miliu des vacances d'ete)
    for ($i = 0; $i < $col->count(); $i++) {
	$pos = ($i + 30) % $col->count();
	$semaine = $col[$pos];
	//$semaine = new EdtSemaine();
	    echo "<option value='".$semaine->getPrimaryKey()."'";
	    if ($id_semaine == $semaine->getPrimaryKey()) echo " SELECTED ";
	    echo ">";
	    echo "Semaine ".$semaine->getNumEdtSemaine()." ".$semaine->getTypeEdtSemaine();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";

    echo '<button type="submit">Afficher les eleves</button>';
    $current_semaine = EdtSemaineQuery::create()->findPk($id_semaine);
    if ($current_cours != null && $current_semaine != null && $current_cours->getTypeSemaine() != $current_semaine->getTypeEdtSemaine()) {
	echo '<br>Erreur : le cours ne correspond pas au type de semaine.';
	$current_cours = null;
	$current_groupe = null;
	$current_aid = null;
    }
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
}

//l'utilisateurs a-t-il deja saisie ce creneau ?
$deja_saisie = false;
if ($id_creneau != null && !$utilisateur->getEdtCreneauAbsenceSaisie($id_creneau, $d_date_absence_eleve)->isEmpty()) {
    $deja_saisie = true;
}


//afichage de la saisie des absences des eleves
if (!$eleve_col->isEmpty()) {
?>
    <div class="centre_tout_moyen">
		<form method="post" action="enregistrement_saisies.php" id="liste_absence_eleve">
		    <input type="hidden" name="total_eleves" value="<?php echo($eleve_col->count()); ?>"/>
		    <input type="hidden" name="id_aid" value="<?php echo($id_aid); ?>"/>
		    <input type="hidden" name="id_groupe" value="<?php echo($id_groupe); ?>"/>
		    <input type="hidden" name="id_creneau" value="<?php echo($id_creneau); ?>"/>
		    <input type="hidden" name="id_cours" value="<?php echo($id_cours); ?>"/>
		    <input type="hidden" name="type_selection" value="<?php echo($type_selection); ?>"/>
		    <input type="hidden" name="id_semaine" value="<?php echo($id_semaine); ?>"/>
		    <input type="hidden" name="d_date_absence_eleve" value="<?php echo($d_date_absence_eleve->format('d/m/Y')); ?>"/>
			<p class="expli_page choix_fin">
				Saisie des absences<br/>du <strong><?php echo strftime  ('%A %d %B %G',  $d_date_absence_eleve->format('U')); ?></strong>
				<br/></p>
			<p class="choix_fin">
				<input value="Enregistrer" name="Valider" type="submit"  onclick="this.form.submit();this.disabled=true;this.value='En cours'" />
			</p>
			<p class="choix_fin">
				<input type="hidden" name="passer_cahier_texte" id="passer_cahier_texte" value="false" />
				<input value="Enregistrer et passer au cahier de texte" name="Valider" type="submit"  onclick="document.getElementById('passer_cahier_texte').value = true; this.form.submit(); this.disabled=true; this.value='En cours'" />
			</p>

<!-- Afichage du tableau de la liste des élèves -->
<!-- Legende du tableau-->
	<?php echo '<p>'.$eleve_col->count().' élèves.</p>'; ?>

<!-- Fin de la legende -->
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
foreach($eleve_col as $eleve) {
		//$eleve = new Eleve();
				if ($eleve_col->getPosition() %2 == '1') {
					$background_couleur="#E8F1F4";
				} else {
					$background_couleur="#C6DCE3";
				}
			        echo "<tr style='background-color :$background_couleur'>\n";


				$Yesterday = date("Y-m-d",mktime(0,0,0,$d_date_absence_eleve->format("m") ,$d_date_absence_eleve->format("d")-1,$d_date_absence_eleve->format("Y")));
				$compter_hier = $eleve->getAbsenceSaisiesDuJour($Yesterday)->count();
				$color_hier = ($compter_hier >= 2) ? ' style="background-color: blue; text-align: center; color: white; font-weight: bold;"' : '';
				$aff_compter_hier = ($compter_hier >= 1) ? $compter_hier.' enr.' : '';
?>
				<td<?php echo $color_hier; ?>><?php echo $aff_compter_hier; ?></td>
				<td class='td_abs_eleves'>
					<input type="hidden" name="id_eleve_absent[<?php echo $eleve_col->getPosition(); ?>]" value="<?php echo $eleve->getIdEleve(); ?>" />
<?php

			// On vérifie si le prof a le droit de voir la fiche de l'élève
			if ($utilisateur->getStatut() == "professeur" AND getSettingValue("voir_fiche_eleve") == "n" OR getSettingValue("voir_fiche_eleve") == '') {
				echo '<span class="td_abs_eleves">'.strtoupper($eleve->getNom()).' '.ucfirst($eleve->getPrenom()).'&nbsp;('.$eleve->getCivilite().')</span>';
			}elseif($utilisateur->getStatut() != "professeur" OR getSettingValue("voir_fiche_eleve") == "y"){
				echo '
				<a href="javascript:centrerpopup(\'../lib/fiche_eleve.php?select_fiche_eleve='.$eleve->getLogin().'\',550,500,\'scrollbars=yes,statusbar=no,resizable=yes\');">
				'.strtoupper($eleve->getNom()).' '.ucfirst($eleve->getPrenom())
				.'</a> ('.$eleve->getCivilite().')
				';
			}
			echo("</td>");
			
			foreach(EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $edt_creneau){
					$absences_du_creneau = $eleve->getAbsenceSaisiesDuCreneau($edt_creneau, $d_date_absence_eleve);
					if (!$absences_du_creneau->isEmpty()) {
					    $style = 'style="background-color : red"';
					} else if ($deja_saisie && $id_creneau == $edt_creneau->getIdDefiniePeriode()) {
					    $style = 'style="background-color : green"';
					} else {
					    $style = '';
					}
					echo '		<td '.$style.'>';

					//si il y a des absences de l'utilisateurs on va proposer de les modifier
					if (getSettingValue("abs2_modification_saisie_une_heure")=='y') {
					    foreach ($absences_du_creneau as $saisie) {
						if ($saisie->getUtilisateurId() == $utilisateur->getPrimaryKey() && $saisie->getCreatedAt('U') > (time() - 3600)) {
						    echo ("<a style='font-size:88%;' href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."'><nobr>Modif. saisie</nobr> <nobr>de ".$saisie->getCreatedAt("H:i")."</nobr></a><br>");
						}
					    }
					}

					if ($current_creneau != null && $current_creneau->getPrimaryKey() == $edt_creneau->getPrimaryKey()) {
					    //le message d'erreur provient du fichier enregistrement_saisies.php
					    if (isset($message_erreur_eleve[$eleve->getIdEleve()])) {
						echo "Erreur : ".$message_erreur_eleve[$eleve->getIdEleve()];
					    }

					    //on a un creneau on va afficher la saisie sur ce creneau
					    echo '<nobr><input style="font-size:88%;" name="active_absence_eleve['.$eleve_col->getPosition().']" value="1" type="checkbox" />';
					    $type_autorises = AbsenceEleveTypeStatutAutoriseQuery::create()->filterByStatut($utilisateur->getStatut())->find();
					    if ($type_autorises->count() != 0) {
						    echo ("<select style='font-size:88%;' name=\"type_absence_eleve[".$eleve_col->getPosition()."]\">");
						    echo "<option style='font-size:88%;' value='-1'></option>\n";
						    foreach ($type_autorises as $type) {
							//$type = new AbsenceEleveTypeStatutAutorise();
							    echo "<option style='font-size:88%;' value='".$type->getAbsenceEleveType()->getId()."'>";
							    echo $type->getAbsenceEleveType()->getNom();
							    echo "</option>\n";
						    }
						    echo "</select>";
					    }
					    echo '</nobr> ';
					    echo '<nobr><input style="font-size:88%;" name="heure_debut_absence_eleve['.$eleve_col->getPosition().']" value="'.$edt_creneau->getHeuredebutDefiniePeriode("H:i").'" type="text" maxlength="5" size="4"/>&nbsp;';
					    echo '<input style="font-size:88%;" name="date_debut_absence_eleve['.$eleve_col->getPosition().']" value="'.$d_date_absence_eleve->format('d/m/Y').'" type="text" maxlength="10" size="8"/></nobr> ';
					    echo '<nobr><input style="font-size:88%;" name="heure_fin_absence_eleve['.$eleve_col->getPosition().']" value="'.$edt_creneau->getHeurefinDefiniePeriode("H:i").'" type="text" maxlength="5" size="4"/>&nbsp;';
					    echo '<input style="font-size:88%;" name="date_fin_absence_eleve['.$eleve_col->getPosition().']" value="'.$d_date_absence_eleve->format('d/m/Y').'" type="text" maxlength="10" size="8"/></nobr>';
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
				?>
				<td>
					<img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" />
				</td>
<?php				}

				if ($current_creneau != null) {
				    echo '<td style="font-size:88%;">Commentaire de la saisie : ';
				    echo '<input  style="font-size:88%;" name="commentaire_absence_eleve['.$eleve_col->getPosition().']" value="'.'" type="text" maxlength="150" size="13"/>';
				    echo '</td>';
				}
?>


<?php echo "</tr>";
} ?>
<?php

echo "</table></div>\n";
}

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