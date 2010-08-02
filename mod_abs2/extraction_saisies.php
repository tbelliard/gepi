<?php
/**
 *
 * @version $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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
};

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

// Initialisation des variables
//récupération des paramètres de la requète
$nom_eleve = isset($_POST["nom_eleve"]) ? $_POST["nom_eleve"] :(isset($_GET["nom_eleve"]) ? $_GET["nom_eleve"] :(isset($_SESSION["nom_eleve"]) ? $_SESSION["nom_eleve"] : NULL));
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :(isset($_SESSION["id_classe_abs"]) ? $_SESSION["id_classe_abs"] : NULL));
$date_absence_eleve_debut = isset($_POST["date_absence_eleve_debut"]) ? $_POST["date_absence_eleve_debut"] :(isset($_GET["date_absence_eleve_debut"]) ? $_GET["date_absence_eleve_debut"] :(isset($_SESSION["date_absence_eleve_debut"]) ? $_SESSION["date_absence_eleve_debut"] : NULL));
$date_absence_eleve_fin = isset($_POST["date_absence_eleve_fin"]) ? $_POST["date_absence_eleve_fin"] :(isset($_GET["date_absence_eleve_fin"]) ? $_GET["date_absence_eleve_fin"] :(isset($_SESSION["date_absence_eleve_fin"]) ? $_SESSION["date_absence_eleve_fin"] : NULL));
$type_extrait = isset($_POST["type_extrait"]) ? $_POST["type_extrait"] :(isset($_GET["type_extrait"]) ? $_GET["type_extrait"] : NULL);
$affichage = isset($_POST["affichage"]) ? $_POST["affichage"] :(isset($_GET["affichage"]) ? $_GET["affichage"] : NULL);

if (isset($id_classe) && $id_classe != null) $_SESSION['id_classe_abs'] = $id_classe;
if (isset($date_absence_eleve_debut) && $date_absence_eleve_debut != null) $_SESSION['date_absence_eleve_debut'] = $date_absence_eleve_debut;
if (isset($date_absence_eleve_fin) && $date_absence_eleve_fin != null) $_SESSION['date_absence_eleve_fin'] = $date_absence_eleve_fin;

if ($date_absence_eleve_debut != null) {
    $dt_date_absence_eleve_debut = new DateTime(str_replace("/",".",$date_absence_eleve_debut));
} else {
    $dt_date_absence_eleve_debut = new DateTime('now');
}
if ($date_absence_eleve_fin != null) {
    $dt_date_absence_eleve_fin = new DateTime(str_replace("/",".",$date_absence_eleve_fin));
} else {
    $dt_date_absence_eleve_fin = new DateTime('now');
}

$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
//$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
//**************** EN-TETE *****************
$titre_page = "Bilan du jour.";
if ($affichage != 'ods') {// on affiche pas de html
    require_once("../lib/header.inc");

    include('menu_abs2.inc.php');
    include('menu_bilans.inc.php');
    ?>
    <div id="contain_div" class="css-panes">
    <form name="choix_extraction" action="<?php $_SERVER['PHP_SELF']?>" method="post">
    <h2>Les saisies du
	<input size="8" id="date_absence_eleve_1" name="date_absence_eleve_debut" value="<?php echo $dt_date_absence_eleve_debut->format('d/m/Y')?>" />
	<script type="text/javascript">
	    Calendar.setup({
		inputField     :    "date_absence_eleve_1",     // id of the input field
		ifFormat       :    "%d/%m/%Y",      // format of the input field
		button         :    "date_absence_eleve_1",  // trigger for the calendar (button ID)
		align          :    "Bl",           // alignment (defaults to "Bl")
		singleClick    :    true
	    });
	</script>
	au
	<input size="8" id="date_absence_eleve_2" name="date_absence_eleve_fin" value="<?php echo $dt_date_absence_eleve_fin->format('d/m/Y')?>" />
	<script type="text/javascript">
	    Calendar.setup({
		inputField     :    "date_absence_eleve_2",     // id of the input field
		ifFormat       :    "%d/%m/%Y",      // format of the input field
		button         :    "date_absence_eleve_2",  // trigger for the calendar (button ID)
		align          :    "Bl",           // alignment (defaults to "Bl")
		singleClick    :    true
	    });
	</script>
	<br/>
    Nom (facultatif) : <input type="text" name="nom_eleve" size="10" value="<?php echo $nom_eleve?>"/>

    <?php
    //on affiche une boite de selection avec les classe
    if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
	$classe_col = ClasseQuery::create()->find();
    } else {
	$classe_col = $utilisateur->getClasses();
    }
    if (!$classe_col->isEmpty()) {
	    echo ("Classe : <select name=\"id_classe\">");
	    echo "<option value='-1'>Toute les classes</option>\n";
	    foreach ($classe_col as $classe) {
		    echo "<option value='".$classe->getId()."'";
		    if ($id_classe == $classe->getId()) echo " selected='selected' ";
		    echo ">";
		    echo $classe->getNomComplet();
		    echo "</option>\n";
	    }
	    echo "</select> ";
    }?>
    <br/>
    Type :
    <select style="width:200px" name="type_extrait">
    <option value='1'>Liste des saisies occasionnant un manquement aux obligations de présence</option>
    <option value='2'>Liste de toute les saisies</option>
    </select>

    <button type="submit" name="affichage" value="html">Afficher</button>
    <button type="submit" name="affichage" value="ods">Enregistrer au format ods</button>

    <?php
}
if ($affichage != null && $affichage != '') {
    $eleve_query = EleveQuery::create();
    if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
    } else {
	$eleve_query->filterByUtilisateurProfessionnel($utilisateur);
    }
    if ($id_classe !== null && $id_classe != -1) {
	$eleve_query->useJEleveClasseQuery()->filterByIdClasse($id_classe)->endUse();
    }
    if ($nom_eleve !== null && $nom_eleve != '') {
	$eleve_query->filterByNomOrPrenomLike($nom_eleve);
    }
    $eleve_col = $eleve_query->distinct()->find();

    $saisie_query = AbsenceEleveSaisieQuery::create()
	->filterByPlageTemps($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)
	->filterByEleveId($eleve_col->toKeyValue('IdEleve', 'IdEleve'));

    if ($type_extrait == '1') {
	$saisie_query->filterManquementObligationPresence(true);
    }

    $saisie_query->useEleveQuery()->orderByNom()->orderByPrenom()->endUse();
    $saisie_query->orderByDebutAbs();
    $saisie_col = $saisie_query->find();
}

if ($affichage == 'html') {
    echo '<table style="border:1px solid">';
    $precedent_eleve_id = null;
    foreach ($saisie_col as $saisie) {
	if ($type_extrait == '1' && !$saisie->getManquementObligationPresence()) {
	    continue;
	}
	if ($precedent_eleve_id != $saisie->getEleveId()) {
	    if ($precedent_eleve_id != null) {
		//on fini la nouvelle ligne
		echo '</table>';
		echo '</td>';
		echo '</tr>';
	    }
	    $precedent_eleve_id = $saisie->getEleveId();
	    //on affiche une nouvelle ligne
	    echo '<tr style="border:1px solid">';
	    echo '<td style="border:1px solid; vertical-align:top">';
	    echo $saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom().' '.$saisie->getEleve()->getClasse()->getNom();
	    echo '</td>';
	    echo '<td style="border:1px solid">';
	    echo '<table>';
	}
	echo '<tr>';
	echo '<td>';
	echo $saisie->getDateDescription();
	echo '</td>';
	echo '<td>';
	echo $saisie->getTypesDescription();
	echo '</td>';
	echo '</tr>';
    }
    echo '</table>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '<h5>Extraction faite le '.date("d/m/Y - h:i").'</h5>';
    
} else if ($affichage == 'ods') {
    // load the TinyButStrong libraries
    if (version_compare(PHP_VERSION,'5')<0) {
	include_once('../tbs/tbs_class.php'); // TinyButStrong template engine for PHP 4
    } else {
	include_once('../tbs/tbs_class_php5.php'); // TinyButStrong template engine
    }
    //include_once('../tbs/plugins/tbsdb_php.php');
    $TBS = new clsTinyButStrong; // new instance of TBS
    include_once('../tbs/plugins/tbs_plugin_opentbs.php');
    $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin

    // Load the template
    $TBS->LoadTemplate('modeles/extraction_saisies.ods');

    $titre = 'Extrait des absences du '.$dt_date_absence_eleve_debut->format('d/m/Y').' au '.$dt_date_absence_eleve_fin->format('d/m/Y');
    $classe = null;
    if ($id_classe != null && $id_classe != '') {
	$classe = ClasseQuery::create()->findOneById($id_classe);
	if ($classe != null) {
	    $titre .= ' pour la classe '.$classe->getNom();
	}
    }
    if ($nom_eleve != null && $nom_eleve != '' ) {
	$titre .= ' pour les élèves dont le nom ou le prenom contient '.$nom_eleve;
    }
    $TBS->MergeField('titre', $titre);

    $TBS->MergeBlock('saisie_col',$saisie_col);

    // Output as a download file (some automatic fields are merged here)
    $nom_fichier = 'saisies_absences_';
    if ($classe != null) {
	$nom_fichier .= $classe->getNom().'_';
    }
    $nom_fichier .=  $dt_date_absence_eleve_fin->format("d_m_Y").'.ods';
    $TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, $nom_fichier);
}

require("../lib/footer.inc.php");
?>