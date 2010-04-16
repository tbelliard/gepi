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
$id_creneau = isset($_POST["id_creneau"]) ? $_POST["id_creneau"] :(isset($_GET["id_creneau"]) ? $_GET["id_creneau"] :NULL);
$id_cours = isset($_POST["id_cours"]) ? $_POST["id_cours"] :(isset($_GET["id_cours"]) ? $_GET["id_cours"] :NULL);
$type_selection = isset($_POST["type_selection"]) ? $_POST["type_selection"] :(isset($_GET["type_selection"]) ? $_GET["type_selection"] :NULL);
$d_date_absence_eleve = isset($_POST["d_date_absence_eleve"]) ? $_POST["d_date_absence_eleve"] : null;

//initialisation des variable
if ($d_date_absence_eleve == null) {
    $d_date_absence_eleve = new DateTime('now');
} else {
    $d_date_absence_eleve = new DateTime(str_replace("/",".",$d_date_absence_eleve));
}

if ($id_cours == null) {
    $current_cours = $utilisateur->getEdtEmplacementCours($d_date_absence_eleve);
    if ($current_cours != null) {
	if ($id_creneau == null) {
	    $id_creneau = $current_cours->getIdDefiniePeriode();
	}
	if ($id_groupe == null)  {
	    $id_groupe = $current_cours->getIdGroupe();
	}
    }
} else {
    $current_cours = EdtEmplacementCoursQuery::create()->findPk($id_cours);
    if ($current_cours != null) {
	$id_cours = $current_cours->getIdCours();
	$id_creneau = $current_cours->getIdDefiniePeriode();
	$id_groupe = $current_cours->getIdGroupe();
    }
}

if ($id_groupe == null) {
    if (isset($_SESSION['id_groupe_session'])) {
	$id_groupe =  $_SESSION['id_groupe_session'];
    }
}

if ($id_creneau == null) {
    $current_creneau = EdtCreneauPeer::retrieveEdtCreneauActuel();
    if ($current_creneau != null) {
	$id_creneau = $current_creneau->getIdDefiniePeriode();
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

//===========================

echo "<div id='aidmenu' style='display: none;'>test</div>\n";

// Etiquettes des onglets:
$onglet_abs='saisie';
include('menu_abs2.inc.php');
//===========================
echo "<div class='css-panes' id='containDiv'>\n";


//on affiche une boite de selection avec les groupes
if (!$utilisateur->getGroupes()->isEmpty()) {
    echo "<form action=\"./saisie_absences.php\" method=\"post\" style=\"width: 100%;\">\n";
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
	    $d_date_absence_eleve = new DateTime('d/m/Y');
    }
    echo '<input size="10" id="d_date_absence_eleve" name="d_date_absence_eleve" value="'.$d_date_absence_eleve->format('d/m/Y').'" />&nbsp;';
    echo '<img id="f_trigger_c" src="../images/icons/calendrier.gif"/>';
    echo '
    <script type="text/javascript">
	Calendar.setup({
	    inputField     :    "d_date_absence_eleve",     // id of the input field
	    ifFormat       :    "%d/%m/%Y",      // format of the input field
	    button         :    "f_trigger_c",  // trigger for the calendar (button ID)
	    align          :    "Tl",           // alignment (defaults to "Bl")
	    singleClick    :    true
	});
    </script>';
    echo '<button type="submit">Afficher les eleves</button>';
    echo "</form><br/>";
}


//on affiche une boite de selection avec les cours
$edt_cours_col = $utilisateur->getEdtEmplacementCourssPeriodeCalendrierActuelle();
if (!$edt_cours_col->isEmpty()) {
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
    if($d_date_absence_eleve == null) {
	    $d_date_absence_eleve = new DateTime('d/m/Y');
    }
    echo '<input size="10" id="d_date_absence_eleve_2" name="d_date_absence_eleve" value="'.$d_date_absence_eleve->format('d/m/Y').'" />&nbsp;';
    echo '<img id="f_trigger_c_2" src="../images/icons/calendrier.gif"/>';
    echo '
    <script type="text/javascript">
	Calendar.setup({
	    inputField     :    "d_date_absence_eleve_2",     // id of the input field
	    ifFormat       :    "%d/%m/%Y",      // format of the input field
	    button         :    "f_trigger_c_2",  // trigger for the calendar (button ID)
	    align          :    "Tl",           // alignment (defaults to "Bl")
	    singleClick    :    true
	});
    </script>';
    echo '<button type="submit">Afficher les eleves</button>';
    echo "</form><br/>";
}

if (isset($message_enregistrement)) {
    echo($message_enregistrement);
}

//afichage des eleves
$eleve_col = new PropelCollection();
$groupe = GroupeQuery::create()->filterByPrimaryKey($id_groupe)->findOne();
if ($groupe != null) {
    $eleve_col = $groupe->getEleves();
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
		    <input type="hidden" name="id_groupe" value="<?php echo($id_groupe); ?>"/>
		    <input type="hidden" name="id_creneau" value="<?php echo($id_creneau); ?>"/>
		    <input type="hidden" name="id_cours" value="<?php echo($id_cours); ?>"/>
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
					    $style = 'style="background-color : green"';;
					} else {
					    $style = '';
					}
					echo '		<td '.$style.'>';

					//si il y a des absences de l'utilisateurs on va proposer de les modifier
					if (getSettingValue("abs2_modification_saisie_une_heure")=='y') {
					    foreach ($absences_du_creneau as $saisie) {
						if ($saisie->getUtilisateurId() == $utilisateur->getPrimaryKey() && $saisie->getCreatedAt('U') > (time() - 3600)) {
						    echo ("<a href='modifier_saisie.php'><nobr>Modif. saisie</nobr> <nobr>de ".$saisie->getCreatedAt("H:i")."</nobr></a><br>");
						}
					    }
					}

					if ($id_creneau == $edt_creneau->getIdDefiniePeriode()) {
					    //on a un creneau on va afficher la saisie sur ce creneau
					    echo '<nobr><input name="active_absence_eleve['.$eleve_col->getPosition().']" value="1" type="checkbox" />';
					    $type_autorises = AbsenceEleveTypeStatutAutoriseQuery::create()->filterByStatut($utilisateur->getStatut())->find();
					    if ($type_autorises->count() != 0) {
						    echo ("<select name=\"type_absence_eleve[".$eleve_col->getPosition()."]\">");
						    echo "<option value='-1'>type d'absence : </option>\n";
						    foreach ($type_autorises as $type) {
							//$type = new AbsenceEleveTypeStatutAutorise();
							    echo "<option value='".$type->getAbsenceEleveType()->getId()."'>";
							    echo $type->getAbsenceEleveType()->getNom();
							    echo "</option>\n";
						    }
						    echo "</select>";
					    }
					    echo '</nobr> ';
					    echo '<nobr><input name="heure_debut_absence_eleve['.$eleve_col->getPosition().']" value="'.$edt_creneau->getHeuredebutDefiniePeriode("H:i").'" type="text" maxlength="5" size="4"/>&nbsp;';
					    echo '<input name="date_debut_absence_eleve['.$eleve_col->getPosition().']" value="'.$d_date_absence_eleve->format('d/m/Y').'" type="text" maxlength="10" size="8"/></nobr> ';
					    echo '<nobr><input name="heure_fin_absence_eleve['.$eleve_col->getPosition().']" value="'.$edt_creneau->getHeurefinDefiniePeriode("H:i").'" type="text" maxlength="5" size="4"/>&nbsp;';
					    echo '<input name="date_fin_absence_eleve['.$eleve_col->getPosition().']" value="'.$d_date_absence_eleve->format('d/m/Y').'" type="text" maxlength="10" size="8"/></nobr>';
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

				if ($id_creneau != null) {
				    echo '<td>Commentaire de la saisie : ';
				    echo '<input name="commentaire_absence_eleve['.$eleve_col->getPosition().']" value="'.'" type="text" maxlength="150" size="20"/>';
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