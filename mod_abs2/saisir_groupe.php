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
$id_creneau = isset($_POST["id_creneau"]) ? $_POST["id_creneau"] :(isset($_GET["id_creneau"]) ? $_GET["id_creneau"] :(isset($_SESSION["id_creneau"]) ? $_SESSION["id_creneau"] : NULL));
$id_cours = isset($_POST["id_cours"]) ? $_POST["id_cours"] :(isset($_GET["id_cours"]) ? $_GET["id_cours"] :(isset($_SESSION["id_cours"]) ? $_SESSION["id_cours"] : NULL));
$type_selection = isset($_POST["type_selection"]) ? $_POST["type_selection"] :(isset($_GET["type_selection"]) ? $_GET["type_selection"] :(isset($_SESSION["type_selection"]) ? $_SESSION["type_selection"] : NULL));
$date_absence_eleve = isset($_POST["date_absence_eleve"]) ? $_POST["date_absence_eleve"] :(isset($_GET["date_absence_eleve"]) ? $_GET["date_absence_eleve"] :(isset($_SESSION["date_absence_eleve"]) ? $_SESSION["date_absence_eleve"] : NULL));
$id_semaine = isset($_POST["id_semaine"]) ? $_POST["id_semaine"] :(isset($_GET["id_semaine"]) ? $_GET["id_semaine"] :(isset($_SESSION["id_semaine"]) ? $_SESSION["id_semaine"] : NULL));
$cahier_texte = isset($_POST["cahier_texte"]) ? $_POST["cahier_texte"] :(isset($_GET["cahier_texte"]) ? $_GET["cahier_texte"] :NULL);

if (isset($id_groupe) && $id_groupe != null) $_SESSION['id_groupe_abs'] = $id_groupe;
if (isset($id_classe) && $id_classe != null) $_SESSION['id_classe_abs'] = $id_classe;
if (isset($id_aid) && $id_aid != null) $_SESSION['id_aid'] = $id_aid;
if (isset($id_creneau) && $id_creneau != null) $_SESSION['id_creneau'] = $id_creneau;
if (isset($id_cours) && $id_cours != null) $_SESSION['id_cours'] = $id_cours;
if (isset($type_selection) && $type_selection != null) $_SESSION['type_selection'] = $type_selection;
if (isset($date_absence_eleve) && $date_absence_eleve != null) $_SESSION['date_absence_eleve'] = $date_absence_eleve;
if (isset($id_semaine) && $id_semaine != null) $_SESSION['id_semaine'] = $id_semaine;


//initialisation des variables
$current_cours = null;
$current_classe = null;
$current_groupe = null;
$current_aid = null;
if ($id_semaine == null || $id_semaine == -1) {
    $id_semaine = date('W');
}
if ($date_absence_eleve != null) {
    $dt_date_absence_eleve = new DateTime(str_replace("/",".",$date_absence_eleve));
} else {
    $dt_date_absence_eleve = new DateTime('now');
}

if ($type_selection == 'id_cours') {
    $current_cours = EdtEmplacementCoursQuery::create()->filterByUtilisateurProfessionnel($utilisateur)->findPk($id_cours);
    if ($current_cours != null) {
	$current_creneau = $current_cours->getEdtCreneau();
	$current_groupe = $current_cours->getGroupe();
	$dt_date_absence_eleve = $current_cours->getDate($id_semaine);
    }
} else if ($type_selection == 'id_groupe') {
    if ($utilisateur->getStatut() == "professeur") {
	$current_groupe = GroupeQuery::create()->filterByUtilisateurProfessionnel($utilisateur)->findPk($id_groupe);
    } else {
	$current_groupe = GroupeQuery::create()->findPk($id_groupe);
    }
    $current_creneau = EdtCreneauQuery::create()->findPk($id_creneau);
} else if ($type_selection == 'id_aid') {
    $current_aid = AidDetailsQuery::create()->findPk($id_aid);
    $current_creneau = EdtCreneauQuery::create()->findPk($id_creneau);
} else if ($type_selection == 'id_classe') {
    $current_classe = ClasseQuery::create()->findPk($id_classe);
    $current_creneau = EdtCreneauQuery::create()->findPk($id_creneau);
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
    } else {
	$current_creneau = EdtCreneauPeer::retrieveByPK($id_creneau);
    }

    if ($id_cours == null) {
	$current_cours = $utilisateur->getEdtEmplacementCours();
	if ($current_cours != null) {
	    $current_creneau = $current_cours->getEdtCreneau();
	    $current_groupe = $current_cours->getGroupe();
	}
    }

    //on va utiliser le numero de semaine precisée pour regler la date
    if ($id_semaine == null || $id_semaine == -1) {
	$id_semaine = date('W');
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
if ($utilisateur->getStatut() != "professeur" || (getSettingValue("abs2_saisie_prof_hors_cours")=='y' && !$utilisateur->getGroupes()->isEmpty())) {
    echo "<td style='border : 1px solid; padding : 10 px;'>";    
	echo "<form action=\"./saisir_groupe.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>";
    echo '<input type="hidden" name="type_selection" value="id_groupe"/>';
    echo ("Groupe : <select name=\"id_groupe\">");
    echo "<option value='-1'>choisissez un groupe</option>\n";
    if ($utilisateur->getStatut() == "professeur") {
	$groupe_col = $utilisateur->getGroupes();
    } else {
	$groupe_col = GroupeQuery::create()->find();
    }
    foreach ($groupe_col as $group) {
	    echo "<option value='".$group->getId()."'";
	    if ($id_groupe == $group->getId()) echo " selected='selected' ";
	    echo ">";
	    echo $group->getNameAvecClasses();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";

    if (getSettingValue("abs2_saisie_prof_decale_journee")=='y' || getSettingValue("abs2_saisie_prof_decale")=='y') {
	echo ("<select name=\"id_creneau\">");
	$edt_creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();

	echo "<option value='-1'>choisissez un creneau</option>\n";
	foreach ($edt_creneau_col as $edt_creneau) {
	    //$edt_creneau = new EdtCreneau();
		echo "<option value='".$edt_creneau->getIdDefiniePeriode()."'";
		if ($id_creneau == $edt_creneau->getIdDefiniePeriode()) echo " selected='selected' ";
		echo ">";
		echo $edt_creneau->getDescription();
		echo "</option>\n";
	}
	echo "</select>&nbsp;";
    } else {
	$current_creneau = EdtCreneauPeer::retrieveEdtCreneauActuel();
	if ($current_creneau != null) {
	    echo " Creneau : ";
	    echo $current_creneau->getDescription();
	    echo "&nbsp;";
	    echo '<input type="hidden" name="id_creneau" value="'.$id_creneau.'"/>';
	} else {
	    echo "Aucun creneau actuellement.&nbsp;";
	}
    }

    if (getSettingValue("abs2_saisie_prof_decale")=='y') {
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
    } else {
	$dt_date_absence_eleve = new DateTime('now');
	echo $dt_date_absence_eleve->format('d/m/Y');
    }
    echo '<button type="submit">Afficher les élèves</button>';
	echo "</p>";
    echo "</form>";
    echo "</td>";
}

//on affiche une boite de selection avec les classe
if ($utilisateur->getStatut() != "professeur") {
    echo "<td style='border : 1px solid; padding : 10 px;'>";    
	echo "<form action=\"./saisir_groupe.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>";
    echo '<input type="hidden" name="type_selection" value="id_classe"/>';
    echo ("Classe : <select name=\"id_classe\">");
    echo "<option value='-1'>choisissez une classe</option>\n";
    if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
	$classe_col = ClasseQuery::create()->find();
    } else {
	$classe_col = $utilisateur->getClasses();
    }
    foreach ($classe_col as $classe) {
	    echo "<option value='".$classe->getId()."'";
	    if ($id_classe == $classe->getId()) echo " selected='selected' ";
	    echo ">";
	    echo $classe->getNomComplet();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";

    if ($utilisateur->getStatut() != "professeur" || getSettingValue("abs2_saisie_prof_decale_journee")=='y' || getSettingValue("abs2_saisie_prof_decale")=='y') {
	echo ("<select name=\"id_creneau\">");
	$edt_creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();

	echo "<option value='-1'>choisissez un creneau</option>\n";
	foreach ($edt_creneau_col as $edt_creneau) {
	    //$edt_creneau = new EdtCreneau();
		echo "<option value='".$edt_creneau->getIdDefiniePeriode()."'";
		if ($id_creneau == $edt_creneau->getIdDefiniePeriode()) echo " selected='selected' ";
		echo ">";
		echo $edt_creneau->getDescription();
		echo "</option>\n";
	}
	echo "</select>&nbsp;";
    } else {
	$current_creneau = EdtCreneauPeer::retrieveEdtCreneauActuel();
	if ($current_creneau != null) {
	    echo " Creneau : ";
	    echo $current_creneau->getDescription();
	    echo "&nbsp;";
	    echo '<input type="hidden" name="id_creneau" value="'.$id_creneau.'"/>';
	} else {
	    echo "Aucun creneau actuellement.&nbsp;";
	}
    }

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
    echo '<button type="submit">Afficher les élèves</button>';
	echo "</p>";
    echo "</form>";
    echo "</td>";
}

//on affiche une boite de selection avec les aid et les creneaux
if ($utilisateur->getStatut() != "professeur" || (getSettingValue("abs2_saisie_prof_hors_cours")=='y' && !$utilisateur->getAidDetailss()->isEmpty())) {
    echo "<td style='border : 1px solid;'>";    
	echo "<form action=\"./saisir_groupe.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>";
    echo '<input type="hidden" name="type_selection" value="id_aid"/>';
    echo ("Aid : <select name=\"id_aid\">");
    echo "<option value='-1'>choisissez une aid</option>\n";
    if ($utilisateur->getStatut() == "professeur") {
	$aid_col = $utilisateur->getAidDetailss();
    } else {
	$aid_col = AidDetailsQuery::create()->find();
    }
    foreach ($aid_col as $aid) {
	    echo "<option value='".$aid->getPrimaryKey()."'";
	    if ($id_aid == $aid->getPrimaryKey()) echo " selected='selected' ";
	    echo ">";
	    echo $aid->getNom();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    
    if ($utilisateur->getStatut() != "professeur" || getSettingValue("abs2_saisie_prof_decale_journee")=='y' || getSettingValue("abs2_saisie_prof_decale")=='y') {
	echo ("<select name=\"id_creneau\">");
	$edt_creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();

	echo "<option value='-1'>choisissez un creneau</option>\n";
	foreach ($edt_creneau_col as $edt_creneau) {
	    //$edt_creneau = new EdtCreneau();
		echo "<option value='".$edt_creneau->getIdDefiniePeriode()."'";
		if ($id_creneau == $edt_creneau->getIdDefiniePeriode()) echo " selected='selected' ";
		echo ">";
		echo $edt_creneau->getDescription();
		echo "</option>\n";
	}
	echo "</select>&nbsp;";
    } else {
	$current_creneau = EdtCreneauPeer::retrieveEdtCreneauActuel();
	if ($current_creneau != null) {
	    echo " Creneau : ";
	    echo $current_creneau->getDescription();
	    echo "&nbsp;";
	    echo '<input type="hidden" name="id_creneau" value="'.$id_creneau.'"/>';
	} else {
	    echo "Aucun creneau actuellement.&nbsp;";
	}
    }

    if ($utilisateur->getStatut() != "professeur" || getSettingValue("abs2_saisie_prof_decale")=='y') {
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
    } else {
	$dt_date_absence_eleve = new DateTime('now');
	echo $dt_date_absence_eleve->format('d/m/Y');
    }
    echo '<button type="submit">Afficher les élèves</button>';
	echo "</p>";
    echo "</form>";
    echo "</td>";
}

//on affiche une boite de selection avec les cours
$edt_cours_col = $utilisateur->getEdtEmplacementCourssPeriodeCalendrierActuelle();
if (!$edt_cours_col->isEmpty()) {
    echo "<td style='border : 1px solid;'>";
    echo "<form action=\"./saisir_groupe.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>";
    echo '<input type="hidden" name="type_selection" value="id_cours"/>';
    echo ("<select name=\"id_cours\">");
    echo "<option value='-1'>choisissez un cours</option>\n";
    foreach ($edt_cours_col as $edt_cours) {
	//$edt_cours = new EdtEmplacementCours();
	    if ($edt_cours->getEdtCreneau() == NULL) {
		//on affiche pas le cours si il n'est associé avec aucun creneau
		continue;
	    }
	    if (getSettingValue("abs2_saisie_prof_decale") != 'y') {
		if ($edt_cours->getJourSemaineNumeric() != date('W')) {
		    //on affiche pas ce cours
		    continue;
		}
	    }
	    echo "<option value='".$edt_cours->getIdCours()."'";
	    if ($id_cours == $edt_cours->getIdCours()) echo " selected='selected' ";
	    echo ">";
	    echo $edt_cours->getDescription();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";


    if (getSettingValue("abs2_saisie_prof_decale")=='y') {
	$col = EdtSemaineQuery::create()->find();
	echo ("<select name=\"id_semaine\">");
	echo "<option value='-1'>choisissez une semaine</option>\n";
	//on va commencer la liste à la semaine 31 (miliu des vacances d'ete)
	for ($i = 0; $i < $col->count(); $i++) {
	    $pos = ($i + 30) % $col->count();
	    $semaine = $col[$pos];
	    //$semaine = new EdtSemaine();
		echo "<option value='".$semaine->getPrimaryKey()."'";
		if ($id_semaine == $semaine->getPrimaryKey()) echo " selected='selected' ";
		echo ">";
		echo "Semaine ".$semaine->getNumEdtSemaine()." ".$semaine->getTypeEdtSemaine();
		echo "</option>\n";
	}
	echo "</select>&nbsp;";
    } else {
	$semaine = EdtSemaineQuery::create()->findPk($id_semaine);
	echo "Semaine ".$semaine->getNumEdtSemaine()." ".$semaine->getTypeEdtSemaine();
	echo '<input type="hidden" name="id_semaine" value="'.$id_semaine.'"/>&nbsp;';
    }

    echo '<button type="submit">Afficher les élèves</button>';
    $current_semaine = EdtSemaineQuery::create()->findPk($id_semaine);
    if ($current_cours != null && $current_cours->getTypeSemaine() != '' && $current_cours->getTypeSemaine() != '0' && $current_semaine != null && $current_cours->getTypeSemaine() != $current_semaine->getTypeEdtSemaine()) {
	echo '<br>Erreur : le cours ne correspond pas au type de semaine.';
	$current_cours = null;
	$current_groupe = null;
	$current_aid = null;
    }
	echo "</p>";
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
}

//l'utilisateurs a-t-il deja saisie ce creneau ?
$deja_saisie = false;
if ($current_cours != null) {
    $query = AbsenceEleveSaisieQuery::create();
    $query->filterByUtilisateurProfessionnel($utilisateur);
    $dt = clone $dt_date_absence_eleve;
    $dt->setTime($current_cours->getHeureDebut('H'), $current_cours->getHeureDebut('i'));
    $query->filterByFinAbs($dt, Criteria::GREATER_EQUAL);
    $dt_end = clone $dt;
    $dt_end->setTime($current_cours->getHeureFin('H'), $current_cours->getHeureFin('i'));
    $query->filterByDebutAbs($dt_end, Criteria::LESS_THAN);
    if ($query->count() > 0) {
	$deja_saisie = true;
    }
} elseif ($current_creneau != null) {
    if (!$utilisateur->getEdtCreneauAbsenceSaisie($id_creneau, $dt_date_absence_eleve)->isEmpty()) {
	$deja_saisie = true;
    }
}


//afichage de la saisie des absences des eleves
if (!$eleve_col->isEmpty()) {
?>
    <div class="centre_tout_moyen" style="width : 900px;">
		<form method="post" action="enregistrement_saisie_groupe.php" id="liste_absence_eleve">
	<p>
		    <input type="hidden" name="total_eleves" value="<?php echo($eleve_col->count()); ?>"/>
		    <?php if ($type_selection == 'id_aid') {?><input type="hidden" name="id_aid" value="<?php echo($id_aid); ?>"/><?php }?>
		    <?php if ($type_selection == 'id_groupe') {?><input type="hidden" name="id_groupe" value="<?php echo($id_groupe); ?>"/><?php }?>
		    <?php if ($id_creneau != null) {?><input type="hidden" name="id_creneau" value="<?php echo($id_creneau); ?>"/><?php }?>
		    <?php if ($type_selection == 'id_cours') {?><input type="hidden" name="id_cours" value="<?php echo($id_cours); ?>"/><?php }?>
		    <input type="hidden" name="type_selection" value="<?php echo($type_selection); ?>"/>
		    <?php if ($id_semaine != null) {?><input type="hidden" name="id_semaine" value="<?php echo($id_semaine); ?>"/><?php }?>
		    <input type="hidden" name="date_absence_eleve" value="<?php echo($dt_date_absence_eleve->format('d/m/Y')); ?>"/>
	</p>
			<p class="expli_page choix_fin">
				Saisie des absences<br/>du <strong><?php echo strftime  ('%A %d %B %G',  $dt_date_absence_eleve->format('U')); ?></strong>
				Pour le groupe
				<strong>
				<?php if (isset($current_groupe) && $current_groupe != null) {
				    echo $current_groupe->getName();
				} else if (isset($current_aid) && $current_aid != null) {
				    echo $current_aid->getNom();
				} else if (isset($current_classe) && $current_classe != null) {
				    echo $current_classe->getNomComplet();
				}?>
				</strong> (les élèves non cochés seront considérés présents)
			</p>
			<p class="choix_fin">
				<input value="Enregistrer" name="Valider" type="submit"  onclick="this.form.submit();this.disabled=true;this.value='En cours'" />
			</p>
			<p class="choix_fin">
				<input value="Enregistrer et passer au cahier de texte" name="cahier_texte" type="submit"/>
			</p>

<!-- Afichage du tableau de la liste des élèves -->
<!-- Legende du tableau-->
	<?php echo ('<p>'.$eleve_col->count().' élèves.</p>') ?>

<!-- Fin de la legende -->
<!-- <table style="text-align: left; width: 600px;" border="0" cellpadding="0" cellspacing="1"> -->
	<table class="tb_absences" summary="Liste des élèves pour l'appel. Colonne 1 : élèves, colonne 2 : absence, colonne3 : retard, colonnes suivantes : suivi de la journée par créneaux, dernière colonne : photos si actif">
		<caption class="invisible no_print">Absences</caption>
		<tbody>
			<tr class="titre_tableau_gestion" style="white-space: nowrap;">
				<th style="text-align : center;" >Veille</th>
				<th style="text-align : center;" abbr="élèves">Liste des &eacute;l&egrave;ves</th>
				<th colspan='"<?php echo (EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime()->count());?>"' class="th_abs_suivi" abbr="Créneaux">Suivi sur la journ&eacute;e</th>
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
				$saisie_affiches = array ();
				if ($eleve_col->isOdd()) {
					$background_couleur="#E8F1F4";
				} else {
					$background_couleur="#C6DCE3";
				}
			        echo "<tr style='background-color :$background_couleur'>\n";


				$Yesterday = date("Y-m-d",mktime(0,0,0,$dt_date_absence_eleve->format("m") ,$dt_date_absence_eleve->format("d")-1,$dt_date_absence_eleve->format("Y")));
				$compter_hier = $eleve->getAbsenceEleveSaisiesDuJour($Yesterday)->count();
				$color_hier = ($compter_hier >= 2) ? ' style="background-color: blue; text-align: center; color: white; font-weight: bold;"' : '';
				$aff_compter_hier = ($compter_hier >= 1) ? $compter_hier.' enr.' : '';
?>
				<td<?php echo $color_hier; ?>><?php echo $aff_compter_hier; ?></td>
				<td class='td_abs_eleves'>
					<input type="hidden" name="id_eleve_absent[<?php echo $eleve_col->getPosition(); ?>]" value="<?php echo $eleve->getIdEleve(); ?>" />
<?php

			echo '<span class="td_abs_eleves">'.strtoupper($eleve->getNom()).' '.ucfirst($eleve->getPrenom()).' ('.$eleve->getCivilite().')</span> ';
			if ($utilisateur->getAccesFicheEleve($eleve)) {
			    //echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."' target='_blank'>";
			    echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."' >";
			    echo '(voir&nbsp;fiche)';
			    echo "</a>";
			}
			echo("</td>");

			$col_creneaux = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();
			
			for($i = 0; $i<$col_creneaux->count(); $i++){
					$edt_creneau = $col_creneaux[$i];
					$nb_creneau_a_saisir = 0; //il faut calculer le nombre de creneau a saisir pour faire un colspan
					$absences_du_creneau = $eleve->getAbsenceEleveSaisiesDuCreneau($edt_creneau, $dt_date_absence_eleve);
					if ($current_creneau != null && $current_creneau->getPrimaryKey() == $edt_creneau->getPrimaryKey()) {
					    //on va faire une boucle pour calculer le nombre de creneaux dans ce cours
					    if ($current_cours == null) {
						$nb_creneau_a_saisir = 1;
					    } else {
						$nb_creneau_a_saisir = 0;
						$dt_fin_cours = $current_cours->getHeureFin();
						$it_creneau = $edt_creneau;
						$absences_du_creneau = new PropelObjectCollection();
						while ($it_creneau != null && $dt_fin_cours > $it_creneau->getHeuredebutDefiniePeriode()) {
						    foreach ($eleve->getAbsenceEleveSaisiesDuCreneau($it_creneau, $dt_date_absence_eleve) as $abs) {
							if (!$absences_du_creneau->contains($abs)) {
							    $absences_du_creneau->append($abs);
							}
						    }
						    $it_creneau = $it_creneau->getNextEdtCreneau();
						    $nb_creneau_a_saisir++;
						}
					    }
					}
					
					if (!$absences_du_creneau->isEmpty()) {
					    $style = 'style="background-color : red"';
					} else if ($deja_saisie && $nb_creneau_a_saisir > 0) {
					    $style = 'style="background-color : green"';
					} else {
					    $style = '';
					}
					if ($nb_creneau_a_saisir>1){
					  echo '<td '.$style.' colspan="'.$nb_creneau_a_saisir.'">';
					}else {
					  echo '<td '.$style.' colspan="1">';
					}

					//si il y a des absences de l'utilisateurs on va proposer de les modifier
					if (getSettingValue("abs2_modification_saisie_une_heure")=='y') {
					    foreach ($absences_du_creneau as $saisie) {
						if (in_array($saisie->getPrimaryKey(), $saisie_affiches)) {
						    //on affiche les saisies une seule fois
						    continue;
						}
						$saisie_affiches[] = $saisie->getPrimaryKey();
						if ($saisie->getUtilisateurId() == $utilisateur->getPrimaryKey() && $saisie->getCreatedAt('U') > (time() - 3600)) {
						    echo ("<a style='font-size:88%;' href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."'>Modif. saisie de ".$saisie->getCreatedAt("H:i")."</a><br>");
						}
					    }
					}

					if ($nb_creneau_a_saisir > 0) {
					    $i = $i + $nb_creneau_a_saisir - 1;
					    //le message d'erreur de l'enregistrement precedent provient du fichier enregistrement_saisies.php
					    if (isset($message_erreur_eleve[$eleve->getIdEleve()]) && $message_erreur_eleve[$eleve->getIdEleve()] != '') {
						echo "Erreur : ".$message_erreur_eleve[$eleve->getIdEleve()];
					    }

					    //la saisie sur ce creneau
					    echo '<input style="font-size:88%;" name="active_absence_eleve['.$eleve_col->getPosition().']" value="1" type="checkbox" />';
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
					    echo '<input style="font-size:88%;" name="heure_debut_absence_eleve['.$eleve_col->getPosition().']" value="';
					    if ($current_cours != null) {echo $current_cours->getHeureDebut("H:i");} else { echo $edt_creneau->getHeuredebutDefiniePeriode("H:i");};
					    echo '" type="text" maxlength="5" size="4"/>&nbsp;';
					    //if (getSettingValue("abs2_saisie_prof_decale")=='y') {
					    if (false) {
						    echo '<input style="font-size:88%;" id="date_debut_absence_eleve_'.$eleve_col->getPosition().'" name="date_debut_absence_eleve['.$eleve_col->getPosition().']" value="'.$dt_date_absence_eleve->format('d/m/Y').'" type="text" maxlength="10" size="8"/> ';
						    echo '<script type="text/javascript">
							Calendar.setup({
							    inputField     :    "date_debut_absence_eleve_'.$eleve_col->getPosition().'",     // id of the input field
							    ifFormat       :    "%d/%m/%Y",      // format of the input field
							    button         :    "date_debut_absence_eleve_'.$eleve_col->getPosition().'",  // trigger for the calendar (button ID)
							    align          :    "Tl",           // alignment (defaults to "Bl")
							    singleClick    :    true
							});
						    </script>';
					    } else {
						    echo '<input style="font-size:88%;" name="date_debut_absence_eleve['.$eleve_col->getPosition().']" value="'.$dt_date_absence_eleve->format('d/m/Y').'" type="hidden"/>';
						    
					    }
					    echo '<input style="font-size:88%;" name="heure_fin_absence_eleve['.$eleve_col->getPosition().']" value="';
					    if ($current_cours != null) {echo $current_cours->getHeureFin("H:i");} else { echo $edt_creneau->getHeurefinDefiniePeriode("H:i");};
					    echo '" type="text" maxlength="5" size="4"/>&nbsp;';
					    //if (getSettingValue("abs2_saisie_prof_decale")=='y') {
					    if (false) {
						    echo '<input style="font-size:88%;" id="date_fin_absence_eleve_'.$eleve_col->getPosition().'" name="date_fin_absence_eleve['.$eleve_col->getPosition().']" value="'.$dt_date_absence_eleve->format('d/m/Y').'" type="text" maxlength="10" size="8"/> ';
						    echo '<script type="text/javascript">
							Calendar.setup({
							    inputField     :    "date_fin_absence_eleve_'.$eleve_col->getPosition().'",     // id of the input field
							    ifFormat       :    "%d/%m/%Y",      // format of the input field
							    button         :    "date_fin_absence_eleve_'.$eleve_col->getPosition().'",  // trigger for the calendar (button ID)
							    align          :    "Tl",           // alignment (defaults to "Bl")
							    singleClick    :    true
							});
						    </script>';
					    } else {
						    echo '<input style="font-size:88%;" name="date_fin_absence_eleve['.$eleve_col->getPosition().']" value="'.$dt_date_absence_eleve->format('d/m/Y').'" type="hidden"/>';
						    
					    }
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

echo "</tbody>\n</table>\n</form>\n</div>\n";
}
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