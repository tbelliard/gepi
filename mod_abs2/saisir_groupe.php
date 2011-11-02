<?php
/**
 *
 * @version $Id: saisir_groupe.php 7888 2011-08-22 11:20:19Z dblanqui $
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
$current_creneau = null;
$current_semaine = null;
if ($id_semaine == null || $id_semaine == -1 || !is_numeric($id_semaine) || $id_semaine > 53
	|| ($utilisateur->getStatut() == 'professeur' && (getSettingValue("abs2_saisie_prof_decale")!='y'))) {
    $id_semaine = date('W');
}
$current_semaine = EdtSemaineQuery::create()->findPk($id_semaine);

if ($utilisateur->getStatut() == 'professeur' && getSettingValue("abs2_saisie_prof_decale")!='y' && getSettingValue("abs2_saisie_prof_decale_journee")!='y') {
    $id_creneau = null;
    $id_cours = null;
}

if ($utilisateur->getStatut() == 'professeur' && (getSettingValue("abs2_saisie_prof_decale")!='y')) {
    $dt_date_absence_eleve = new DateTime('now');
} elseif ($date_absence_eleve != null) {
    try {
	$dt_date_absence_eleve = new DateTime(str_replace("/",".",$date_absence_eleve));
    } catch (Exception $x) {
	echo "<span style='color :red'>Erreur : Mauvais format de date d'absence.</span><br/>";
	$dt_date_absence_eleve = new DateTime('now');
    }
} else {
    $dt_date_absence_eleve = new DateTime('now');
}

if ($type_selection == 'id_cours') {
    if ($utilisateur->getStatut() == "professeur") {
	$current_cours = EdtEmplacementCoursQuery::create()->filterByUtilisateurProfessionnel($utilisateur)->findPk($id_cours);
    } else {
	$current_cours = EdtEmplacementCoursQuery::create()->findPk($id_cours);
    }
    $current_creneau = null;
    if ($current_cours != null) {
	$current_creneau = $current_cours->getEdtCreneau();
	$current_groupe = $current_cours->getGroupe();
	$current_aid = $current_cours->getAidDetails();
	$dt_date_absence_eleve = $current_cours->getDate($id_semaine);
    }
} else {
    if ($id_creneau == null) {
	$current_creneau = EdtCreneauPeer::retrieveEdtCreneauActuel();
    } else {
	$current_creneau = EdtCreneauPeer::retrieveByPK($id_creneau);
    }
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
} else if ($type_selection != 'id_cours' && getSettingValue("autorise_edt_tous") == 'y'){//rien n'as ete selectionner, on va regarder le cours actuel
    $current_cours = $utilisateur->getEdtEmplacementCours();
    if ($current_cours != null) {
	$current_creneau = $current_cours->getEdtCreneau();
	$current_groupe = $current_cours->getGroupe();
	$current_aid = $current_cours->getAidDetails();
	$type_selection = 'id_cours';
    } else {
	if (isset($_SESSION['id_groupe_session'])) {
	    $id_groupe =  $_SESSION['id_groupe_session'];
	    $current_groupe = GroupeQuery::create()->filterByUtilisateurProfessionnel($utilisateur)->findPk($id_groupe);
	    $type_selection = 'id_groupe';
	}
    }
}
$id_groupe = null;
$id_classe = null;
$id_aid = null;
$id_creneau = null;
$id_cours = null;
if ($current_groupe != null) {$id_groupe = $current_groupe->getId();}
if ($current_classe != null) {$id_classe = $current_classe->getId();}
if ($current_aid != null) {$id_aid = $current_aid->getId();}
if ($current_creneau != null) {$id_creneau = $current_creneau->getIdDefiniePeriode();}
if ($current_cours != null) {$id_cours = $current_cours->getIdCours();}
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
if (getSettingValue("abs2_saisie_prof_hors_cours")!='y'
	&& $utilisateur->getStatut() == "professeur") {
	//le reglage specifie que le prof n'a pas le droit de saisir autre chose que son cours
	//donc on affiche pas de selection, le cours est automatiquement selectionné
} else {
    if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
	$groupe_col = GroupeQuery::create()->orderByName()->useJGroupesClassesQuery()->useClasseQuery()->orderByNom()->endUse()->endUse()
						->leftJoinWith('Groupe.JGroupesClasses')
					    ->leftJoinWith('JGroupesClasses.Classe')
						->find();
    } else {
	$groupe_col = $utilisateur->getGroupes();
    }
    if (!$groupe_col->isEmpty()) {
	echo "<td style='border : 1px solid; padding : 10 px;'>";
	    echo "<form action=\"./saisir_groupe.php\" method=\"post\" style=\"width: 100%;\">\n";
	    echo "<p>";
	echo '<input type="hidden" name="type_selection" value="id_groupe"/>';
	echo ("Groupe : <select name=\"id_groupe\" class=\"small\">");
	echo "<option value='-1'>choisissez un groupe</option>\n";
	foreach ($groupe_col as $group) {
		echo "<option value='".$group->getId()."'";
		if ($id_groupe == $group->getId()) echo " selected='selected' ";
		echo ">";
		echo $group->getNameAvecClasses();
		echo "</option>\n";
	}
	echo "</select>&nbsp;";
	format_selectbox_heure($utilisateur, $id_creneau, $dt_date_absence_eleve);
	echo '<button type="submit">Afficher les élèves</button>';
	echo "</p>";
	echo "</form>";
	echo "</td>";
    }
}


if (getSettingValue("abs2_saisie_prof_hors_cours")!='y'
	&& $utilisateur->getStatut() == "professeur") {
	//le reglage specifie que le prof n'a pas le droit de saisir autre chose que son cours
	//donc on affiche pas de selection, le cours est automatiquement selectionné
} else {
    if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
	$aid_col = AidDetailsQuery::create()->find();
    } else {
	$aid_col = $utilisateur->getAidDetailss();
    }
    //on affiche une boite de selection avec les aid et les creneaux
    if (!$aid_col->isEmpty()) {
	echo "<td style='border : 1px solid;'>";
	    echo "<form action=\"./saisir_groupe.php\" method=\"post\" style=\"width: 100%;\">\n";
	    echo "<p>";
	echo '<input type="hidden" name="type_selection" value="id_aid"/>';
	echo ("Aid : <select name=\"id_aid\" class=\"small\">");
	echo "<option value='-1'>choisissez une aid</option>\n";
	foreach ($aid_col as $aid) {
		echo "<option value='".$aid->getPrimaryKey()."'";
		if ($id_aid == $aid->getPrimaryKey()) echo " selected='selected' ";
		echo ">";
		echo $aid->getNom();
		echo "</option>\n";
	}
	echo "</select>&nbsp;";
	format_selectbox_heure($utilisateur, $id_creneau, $dt_date_absence_eleve);
	echo '<button type="submit">Afficher les élèves</button>';
	echo "</p>";
	echo "</form>";
	echo "</td>";
    }
}


if (getSettingValue("abs2_saisie_prof_decale_journee")!='y'
	&& getSettingValue("abs2_saisie_prof_decale")!='y'
	&& $utilisateur->getStatut() == "professeur") {
	//le reglage specifie que le prof n'a pas le droit de saisir autre chose que son cours
	//donc on affiche pas de selection, le cours est automatiquement selectionné
} else if (getSettingValue("autorise_edt_tous") != 'y') {
    //edt desactivé
} else {
    //on affiche une boite de selection avec les cours
    if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
		//la collection entière des cours est trop grosse et inexploitable sous la forme d'une liste. ça consomme de la ressource donc c'est désactivé
		$edt_cours_col = new PropelCollection();
    } else {
	$edt_cours_col = $utilisateur->getEdtEmplacementCourssPeriodeCalendrierActuelle();
    }
    if (!$edt_cours_col->isEmpty()) {
	echo "<td style='border : 1px solid;'>";
	echo "<form action=\"./saisir_groupe.php\" method=\"post\" style=\"width: 100%;\">\n";
	    echo "<p>";
	echo '<input type="hidden" name="type_selection" value="id_cours"/>';
	echo ("<select name=\"id_cours\" class=\"small\">");
	echo "<option value='-1'>choisissez un cours</option>\n";
	foreach ($edt_cours_col as $edt_cours) {
//	    $edt_cours = new EdtEmplacementCours();
		if ($edt_cours->getEdtCreneau() == NULL) {
		    //on affiche pas le cours si il n'est associé avec aucun creneau
		    continue;
		}
		if (getSettingValue("abs2_saisie_prof_decale") != 'y' && $utilisateur->getStatut() == "professeur") {
		    if ($edt_cours->getJourSemaineNumeric() != date('w')) {
			//on affiche pas ce cours car il n'est pas aujourd'hui
			continue;
		    }
		    if ($edt_cours->getTypeSemaine() != '' && $edt_cours->getTypeSemaine() != '0' && $edt_cours->getTypeSemaine() != $current_semaine->getTypeEdtSemaine()) {
			//on affiche pas ce cours car il n'est pas aujourd'hui
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


	if (getSettingValue("abs2_saisie_prof_decale")=='y' || $utilisateur->getStatut() != "professeur") {
	    $col = EdtSemaineQuery::create()->find();
	    echo ("<select name=\"id_semaine\" class=\"small\">");
	    echo "<option value='-1'>choisissez une semaine</option>\n";
	    //on va commencer la liste à la semaine 31 (milieu des vacances d'ete)
	    for ($i = 0; $i < $col->count(); $i++) {
		$pos = ($i + 30) % $col->count();
		$semaine = $col[$pos];
		//$semaine = new EdtSemaine();
		    echo "<option value='".$semaine->getPrimaryKey()."'";
		    if ($id_semaine == $semaine->getPrimaryKey()) echo " selected='selected' ";
		    echo ">";
		    echo "Semaine ".$semaine->getNumEdtSemaine()." ".$semaine->getTypeEdtSemaine();
		    echo " du ".$semaine->getLundi('d/m').' au '.$semaine->getSamedi('d/m');
		    echo "</option>\n";
	    }
	    echo "</select>&nbsp;";
	} else {
	    echo "Semaine&nbsp;".$current_semaine->getNumEdtSemaine()."&nbsp;".$current_semaine->getTypeEdtSemaine();
	    echo '<input type="hidden" name="id_semaine" value="'.$id_semaine.'"/>&nbsp;';
	}

	echo '<button type="submit">Afficher les élèves</button>';
	if ($current_cours != null && $current_cours->getTypeSemaine() != '' && $current_cours->getTypeSemaine() != '0' && $current_semaine != null && $current_cours->getTypeSemaine() != $current_semaine->getTypeEdtSemaine()) {
	    echo '<br>Erreur : le cours ne correspond pas au type de semaine.';
	    $current_cours = null;
	    $current_groupe = null;
	    $current_classe = null;
	    $current_aid = null;
	}
	echo "</p>";
	echo "</form>";
	echo "</td>";
    }
}


if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
    $classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
} else {
    $classe_col = $utilisateur->getClasses();
}
if (!$classe_col->isEmpty()) {
    echo "<td style='border : 1px solid; padding : 10 px;'>";
	echo "<form action=\"./saisir_groupe.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>";
    echo '<input type="hidden" name="type_selection" value="id_classe"/>';
    echo ("Classe : <select name=\"id_classe\" class=\"small\">");
    echo "<option value='-1'>choisissez une classe</option>\n";
    foreach ($classe_col as $classe) {
	    echo "<option value='".$classe->getId()."'";
	    if ($id_classe == $classe->getId()) echo " selected='selected' ";
	    echo ">";
	    echo $classe->getNom();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    format_selectbox_heure($utilisateur, $id_creneau, $dt_date_absence_eleve);
    echo '<button type="submit">Afficher les élèves</button>';
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
    $query = EleveQuery::create();
    $eleve_col = $query->useJEleveGroupeQuery()
                        ->filterByIdGroupe($current_groupe->getId())
                        ->endUse()
            ->where('Eleve.DateSortie<?','0')
            ->orWhere('Eleve.DateSortie is NULL')
            ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve->format('U'))
            ->orderBy('Eleve.Nom','asc')
            ->orderBy('Eleve.Prenom','asc')
            ->distinct()
            ->find();
} else if (isset($current_aid) && $current_aid != null) {
    $query = EleveQuery::create();
    $eleve_col = $query->useJAidElevesQuery()
                        ->filterByIdAid($current_aid->getId())
                        ->endUse()
            ->where('Eleve.DateSortie<?','0')
            ->orWhere('Eleve.DateSortie is NULL')
            ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve->format('U'))
            ->orderBy('Eleve.Nom','asc')
            ->orderBy('Eleve.Prenom','asc')
            ->distinct()
            ->find();
} else if (isset($current_classe) && $current_classe != null) {
    $query = EleveQuery::create();
    $eleve_col = $query->useJEleveClasseQuery()
                        ->filterByIdClasse($current_classe->getId())
                        ->endUse()
            ->where('Eleve.DateSortie<?','0')
            ->orWhere('Eleve.DateSortie is NULL')
            ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve->format('U'))
            ->orderBy('Eleve.Nom','asc')
            ->orderBy('Eleve.Prenom','asc')
            ->distinct()
            ->find();
}

//l'utilisateurs a-t-il deja saisie ce creneau ?
$deja_saisie = false;
if ($current_cours != null) {
    $query = AbsenceEleveSaisieQuery::create();
    if ($current_aid != null) {
	$query->filterByIdAid($current_aid->getId());
    }
    if ($current_groupe != null) {
	$query->filterByIdGroupe($current_groupe->getId());
    }
    if ($current_classe != null) {
	$query->filterByIdClasse($current_classe->getId());
    }
    $query->filterByUtilisateurProfessionnel($utilisateur);
    $dt = clone $dt_date_absence_eleve;
    $dt->setTime($current_cours->getHeureDebut('H'), $current_cours->getHeureDebut('i'));
    $dt_end = clone $dt;
    $dt_end->setTime($current_cours->getHeureFin('H'), $current_cours->getHeureFin('i'));
    $query->filterByPlageTemps($dt, $dt_end);
    if ($query->count() > 0) {
	$deja_saisie = true;
    }
} elseif ($current_creneau != null) {
    $query = AbsenceEleveSaisieQuery::create();
    if ($current_aid != null) {
	$query->filterByIdAid($current_aid->getId());
    }
    if ($current_groupe != null) {
	$query->filterByIdGroupe($current_groupe->getId());
    }
    if ($current_classe != null) {
	$query->filterByIdClasse($current_classe->getId());
    }
    $query->filterByUtilisateurProfessionnel($utilisateur);
    $dt = clone $dt_date_absence_eleve;
    $dt->setTime($current_creneau->getHeuredebutDefiniePeriode('H'), $current_creneau->getHeuredebutDefiniePeriode('i'));
    $dt_end = clone $dt;
    $dt_end->setTime($current_creneau->getHeurefinDefiniePeriode('H'), $current_creneau->getHeurefinDefiniePeriode('i'));
    $query->filterByPlageTemps($dt, $dt_end);
    if ($query->count() > 0) {
	$deja_saisie = true;
    }
}

if ($current_creneau == null) {
    echo 'Aucun créneau selectionné';
    //on vide la liste des eleves pour eviter de proposer une saisie
    $eleve_col = new PropelObjectCollection();
}

//afichage de la saisie des absences des eleves
if (!$eleve_col->isEmpty()) {
?>
    <div class="centre_tout_moyen" style="width : 900px;">
		<form method="post" action="enregistrement_saisie_groupe.php" id="liste_absence_eleve">
	<p>
		    <input type="hidden" name="total_eleves" value="<?php echo($eleve_col->count()); ?>"/>
		    <input type="hidden" name="id_aid" value="<?php echo($id_aid); ?>"/>
		    <input type="hidden" name="id_groupe" value="<?php echo($id_groupe); ?>"/>
		    <input type="hidden" name="id_classe" value="<?php echo($id_classe); ?>"/>
		    <input type="hidden" name="id_creneau" value="<?php echo($id_creneau); ?>"/>
		    <input type="hidden" name="id_cours" value="<?php echo($id_cours); ?>"/>
		    <input type="hidden" name="type_selection" value="<?php echo($type_selection); ?>"/>
		    <input type="hidden" name="id_semaine" value="<?php echo($id_semaine); ?>"/>
		    <input type="hidden" name="date_absence_eleve" value="<?php echo($dt_date_absence_eleve->format('d/m/Y')); ?>"/>
	</p>
			<p class="expli_page choix_fin">
				Saisie des absences du <strong><?php echo strftime  ('%A %d/%m/%Y',  $dt_date_absence_eleve->format('U')); ?></strong>
				pour 
				<strong>
				<?php if (isset($current_groupe) && $current_groupe != null) {
				    echo 'le groupe '.$current_groupe->getNameAvecClasses();
				} else if (isset($current_aid) && $current_aid != null) {
				    echo 'l\'aid '.$current_aid->getNom();
				} else if (isset($current_classe) && $current_classe != null) {
				    echo 'la classe '.$current_classe->getNom();
				}?>
				</strong>
				<?php if ($current_creneau != null) { ?>
				de
				<?php
				    echo ' <input style="font-size:88%;" name="heure_debut_appel" id="heure_debut_appel" value="';
				    if (isset($_POST["heure_debut_appel"])) {$heure_debut_appel = ($_POST["heure_debut_appel"]);}
				    elseif (isset($_GET["heure_debut_appel"])) {$heure_debut_appel = ($_GET["heure_debut_appel"]);}
				    elseif ($current_cours != null) {
					if ($current_cours->getHeureDebut('s') > 0) {
					    //on arrondi le debut de saisie au-dessus pour ne pas depasser l'heure du cours
					    if ($current_cours->getHeureDebut("i") == 59) {
						$heure_debut_appel = ($current_cours->getHeureDebut("H") + 1).':00';
					    } else {
						$heure_debut_appel = $current_cours->getHeureDebut("H").':'.($current_cours->getHeureDebut("i") + 1);
					    }
					} else {
					    $heure_debut_appel = $current_cours->getHeureDebut("H:i");
					}
				    } elseif ($current_creneau != null) {
					$heure_debut_appel = $current_creneau->getHeuredebutDefiniePeriode("H:i");
				    };
				    echo $heure_debut_appel;
				    echo '" type="text" maxlength="5" size="4"/>';
				?>
				à
				<?php
				    echo ' <input style="font-size:88%;" name="heure_fin_appel" id="heure_fin_appel" value="';
				    if (isset($_POST["heure_fin_appel"])) {$heure_fin_appel = ($_POST["heure_fin_appel"]);}
				    elseif (isset($_GET["heure_fin_appel"])) {$heure_fin_appel = ($_GET["heure_fin_appel"]);}
				    elseif ($current_cours != null) {$heure_fin_appel = $current_cours->getHeureFin("H:i");}
				    elseif ($current_creneau != null) { $heure_fin_appel = $current_creneau->getHeurefinDefiniePeriode("H:i");};
				    echo $heure_fin_appel;
				    echo '" type="text" maxlength="5" size="4"/> ';
				    echo '<button onclick="SetAllTextFields(\'liste_absence_eleve\', \'heure_debut_absence_eleve\',\'\',document.getElementById(\'heure_debut_appel\').value);
							    SetAllTextFields(\'liste_absence_eleve\', \'heure_fin_absence_eleve\',\'\',document.getElementById(\'heure_fin_appel\').value);
							    return false;">Changer</button>';
				?>
				<?php } ?>
				<br/>
				(les élèves non cochés seront considérés présents)
			</p>
			<p class="choix_fin">
				<input value="Enregistrer" name="Valider" type="submit"  onclick="this.form.submit();this.disabled=true;this.value='En cours'" />
			</p>
			<?php if ($utilisateur->getStatut() == 'professeur' && getSettingValue("active_cahiers_texte")=='y') {
			    echo '
			    <p class="choix_fin">
				    <input value="Enregistrer et passer au cahier de texte" name="cahier_texte" type="submit"/>
			    </p>';
			} ?>

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
                ?>
			    <tr style='background-color :<?php echo $background_couleur;?>'>
                <?php

				$Yesterday = date("Y-m-d",mktime(0,0,0,$dt_date_absence_eleve->format("m") ,$dt_date_absence_eleve->format("d")-1,$dt_date_absence_eleve->format("Y")));
				$compter_hier = $eleve->getAbsenceEleveSaisiesDuJour($Yesterday)->count();
				$color_hier = ($compter_hier >= 2) ? ' style="background-color: blue; text-align: center; color: white; font-weight: bold;"' : '';
				$aff_compter_hier = ($compter_hier >= 1) ? $compter_hier.' enr.' : '';
?>
				<td<?php echo $color_hier; ?>><?php echo $aff_compter_hier; ?></td>
				<td class='td_abs_eleves'>
					<input type="hidden" name="id_eleve_absent[<?php echo $eleve_col->getPosition(); ?>]" value="<?php echo $eleve->getIdEleve(); ?>" />
<?php

			echo '<span class="td_abs_eleves">'.strtoupper($eleve->getNom()).' '.ucfirst($eleve->getPrenom()).' ('.$eleve->getCivilite().')';
			if (	(isset($current_groupe) && $current_groupe != null && $current_groupe->getClasses()->count() == 1)
				|| (isset($current_classe) && $current_classe != null)) {
			    //si le groupe a une seule classe ou si c'est une classe qui est sélectionner pas la peine d'afficher la classe.
			} else {
                            if ($eleve->getClasse() != null) {
                                echo ' '.$eleve->getClasse()->getNom().' ';
                            }
                        }
                        echo'</span>';
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
					if ($current_creneau != null && $current_creneau->getPrimaryKey() == $edt_creneau->getPrimaryKey()) {
					    //on va faire une boucle pour calculer le nombre de creneaux dans ce cours
					    if ($current_cours == null) {
						$nb_creneau_a_saisir = 1;
						$absences_du_creneau = $eleve->getAbsenceEleveSaisiesDuCreneau($edt_creneau, $dt_date_absence_eleve);
					    } else {
						$nb_creneau_a_saisir = 0;
						$dt_fin_cours = $current_cours->getHeureFin(null);
						$it_creneau = $edt_creneau;
						$absences_du_creneau = new PropelObjectCollection();
						while ($it_creneau != null && $dt_fin_cours->format('U') > $it_creneau->getHeuredebutDefiniePeriode('U')) {
						    foreach ($eleve->getAbsenceEleveSaisiesDuCreneau($it_creneau, $dt_date_absence_eleve) as $abs) {
							if (!$absences_du_creneau->contains($abs)) {
							    $absences_du_creneau->append($abs);
							}
						    }
						    $it_creneau = $it_creneau->getNextEdtCreneau();
						    $nb_creneau_a_saisir++;
						}
					    }
					    //pour le creneau en cours on garde uniquement les absences de l'utilisateur pour ne pas l'influencer par d'autres saisies
					    $absences_du_creneau_du_prof = new PropelObjectCollection();
					    foreach ($absences_du_creneau as $abs) {
						if ($abs->getUtilisateurId() == $utilisateur->getPrimaryKey()) {
						    $absences_du_creneau_du_prof->append($abs);
						}
					    }
					    $absences_du_creneau = $absences_du_creneau_du_prof;
					} else if ($current_creneau != null && $edt_creneau->getHeuredebutDefiniePeriode('U') > $current_creneau->getHeuredebutDefiniePeriode('U')) {
					    //on affiche pas les informations apres le creneau en cours pour ne pas influencer la saisie si c'est un enseignant
                        if($utilisateur->getStatut() == "professeur"){
                            $absences_du_creneau = new PropelCollection(); 
                        }else{
                           $absences_du_creneau = $eleve->getAbsenceEleveSaisiesDuCreneau($edt_creneau, $dt_date_absence_eleve);
                        }					    
					}   else {
					    //on affiche  les informations pour les crenaux avant la saisie
					    $absences_du_creneau = $eleve->getAbsenceEleveSaisiesDuCreneau($edt_creneau, $dt_date_absence_eleve);
					}

					$style = '';
					if (!$absences_du_creneau->isEmpty()) {
                                            foreach ($absences_du_creneau as $abs_saisie) {
                                                if ($abs_saisie->getManquementObligationPresence()) {
                                                    $style = 'style="background-color : red"';
                                                    break;
                                                }
                                            }
					} else if ($deja_saisie && $nb_creneau_a_saisir > 0) {
					    $style = 'style="background-color : green"';
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
						    echo "<a style='font-size:78%;' href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."'>Modif.&nbsp;saisie de ".$saisie->getCreatedAt("H:i").' ';
						    $besoin_echo_virgule = false;
						    foreach ($saisie->getAbsenceEleveTraitements() as $bou_traitement) {
							if ($bou_traitement->getAbsenceEleveType() != null) {
							    if ($besoin_echo_virgule) {
								echo ', ';
								$besoin_echo_virgule = false;
							    }
							    echo $bou_traitement->getAbsenceEleveType()->getNom();
							    $besoin_echo_virgule = true;
							}
						    }
						    echo "</a><br>";
						}
					    }
					}

					if ($nb_creneau_a_saisir > 0) {
					    $i = $i + $nb_creneau_a_saisir - 1;
					    //le message d'erreur de l'enregistrement precedent provient du fichier enregistrement_saisies_groupe.php
					    if (isset($message_erreur_eleve[$eleve->getIdEleve()]) && $message_erreur_eleve[$eleve->getIdEleve()] != '') {
						echo "Erreur : ".$message_erreur_eleve[$eleve->getIdEleve()];
					    }

					    //la saisie sur ce creneau
					    $type_autorises = AbsenceEleveTypeQuery::create()->orderByRank()->useAbsenceEleveTypeStatutAutoriseQuery()->filterByStatut($utilisateur->getStatut())->endUse()->find();
					    if ($type_autorises->count() != 0) {
						    echo ("<select style='font-size:88%; width:140px' onChange='this.form.elements[\"active_absence_eleve[".$eleve_col->getPosition()."]\"].checked = (this.options[this.selectedIndex].value != -1);' name=\"type_absence_eleve[".$eleve_col->getPosition()."]\">");
						    echo "<option style='font-size:88%;' value='-1'></option>\n";
						    foreach ($type_autorises as $type) {
							//$type = new AbsenceEleveTypeStatutAutorise();
							    echo "<option style='font-size:88%;' value='".$type->getId()."'>";
							    echo $type->getNom();
							    echo "</option>\n";
						    }
						    echo "</select>";
					    }
					    echo ' <input style="font-size:88%;" id="active_absence_eleve['.$eleve_col->getPosition().']" name="active_absence_eleve['.$eleve_col->getPosition().']" value="1" type="checkbox" />';
					    echo ' <input style="font-size:88%;" onChange="this.form.elements[\'active_absence_eleve['.$eleve_col->getPosition().']\'].checked = true;" name="heure_debut_absence_eleve['.$eleve_col->getPosition().']" id="heure_debut_absence_eleve_'.$eleve_col->getPosition().'" value="';
					    echo $heure_debut_appel;
					    echo '" type="text" maxlength="5" size="4"/>&nbsp;';
					    echo '<input style="font-size:88%;" name="date_debut_absence_eleve['.$eleve_col->getPosition().']" value="'.$dt_date_absence_eleve->format('d/m/Y').'" type="hidden"/>';
					    echo '<input style="font-size:88%;" onChange="this.form.elements[\'active_absence_eleve['.$eleve_col->getPosition().']\'].checked = true;" name="heure_fin_absence_eleve['.$eleve_col->getPosition().']" value="';
					    echo $heure_fin_appel;
					    echo '" type="text" maxlength="5" size="4"/>';
					    echo '<input style="font-size:88%;" name="date_fin_absence_eleve['.$eleve_col->getPosition().']" value="'.$dt_date_absence_eleve->format('d/m/Y').'" type="hidden"/>';
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
				?>
				<td>
					<img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" />
				</td>
<?php				}

				if ($current_creneau != null) {
				    echo '<td style="font-size:88%;">Commentaire : ';
				    echo '<input  style="font-size:88%;" name="commentaire_absence_eleve['.$eleve_col->getPosition().']" value="'.'" type="text" maxlength="150" size="13"/>';
				    echo '</td>';
				}
?>
    </tr>
<?php
}
?>
</tbody>
</table>
<?php
echo '
<p class="choix_fin">
    <input value="Enregistrer" name="Valider" type="submit"  onclick="this.form.submit();this.disabled=true;this.value=\'En cours\'" />
</p>
';
if ($utilisateur->getStatut() == 'professeur' && getSettingValue("active_cahiers_texte")=='y') {
    echo '
    <p class="choix_fin">
	    <input value="Enregistrer et passer au cahier de texte" name="cahier_texte" type="submit"/>
    </p>';
}
?>
</form>
</div>
<?php
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

 function format_selectbox_heure($utilisateur, $id_creneau, $dt_date_absence_eleve) {
     	if ($utilisateur->getStatut() != 'professeur' || getSettingValue("abs2_saisie_prof_decale_journee")=='y' || getSettingValue("abs2_saisie_prof_decale")=='y') {
	    echo ("<select name=\"id_creneau\" class=\"small\">");
	    $edt_creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();
	    echo "<option value='-1'>choisissez un créneau</option>\n";
	    foreach ($edt_creneau_col as $edt_creneau) {
		    if ($edt_creneau->getTypeCreneaux() == EdtCreneau::TYPE_PAUSE
			    || $edt_creneau->getTypeCreneaux() == EdtCreneau::TYPE_REPAS) {
			continue;
		    }
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
		echo $current_creneau->getDescription().' ';
		echo '<input type="hidden" name="id_creneau" value="'.$current_creneau->getIdDefiniePeriode().'"/>';
	    } else {
		echo "Aucun creneau actuellement.&nbsp;";
	    }
	}

	if ($utilisateur->getStatut() != 'professeur' || (getSettingValue("abs2_saisie_prof_decale")=='y' && getSettingValue("abs2_saisie_prof_decale_journee")=='y')) {
	    $rand_id = rand(0,10000000);
	    echo '<input size="9" id="date_absence_eleve_'.$rand_id.'" name="date_absence_eleve" value="'.$dt_date_absence_eleve->format('d/m/Y').'" />&nbsp;';
	    echo '
	    <script type="text/javascript">
		Calendar.setup({
		    inputField     :    "date_absence_eleve_'.$rand_id.'",     // id of the input field
		    ifFormat       :    "%d/%m/%Y",      // format of the input field
		    button         :    "date_absence_eleve_1",  // trigger for the calendar (button ID)
		    align          :    "Bl",           // alignment (defaults to "Bl")
		    singleClick    :    true
		});
	    </script>';
	} else {
	    echo ' Le '.$dt_date_absence_eleve->format('d/m/Y').' ';
	}
 }
 
?>
