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

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite" && $utilisateur->getStatut()!="autre") {
    echo $utilisateur->getStatut();
    die("acces interdit");
}

$photo_redim_taille_max_largeur=45;
$photo_redim_taille_max_hauteur=45;

//récupération des paramètres de la requète
$nom_eleve = isset($_POST["nom_eleve"]) ? $_POST["nom_eleve"] :(isset($_GET["nom_eleve"]) ? $_GET["nom_eleve"] :(isset($_SESSION["nom_eleve"]) ? $_SESSION["nom_eleve"] : NULL));
$id_eleve = isset($_POST["id_eleve"]) ? $_POST["id_eleve"] :(isset($_GET["id_eleve"]) ? $_GET["id_eleve"] :(isset($_SESSION["id_eleve"]) ? $_SESSION["id_eleve"] : NULL));
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :(isset($_SESSION["id_groupe_abs"]) ? $_SESSION["id_groupe_abs"] : NULL));
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :(isset($_SESSION["id_classe_abs"]) ? $_SESSION["id_classe_abs"] : NULL));
$id_aid = isset($_POST["id_aid"]) ? $_POST["id_aid"] :(isset($_GET["id_aid"]) ? $_GET["id_aid"] :(isset($_SESSION["id_aid"]) ? $_SESSION["id_aid"] : NULL));
$id_creneau = isset($_POST["id_creneau"]) ? $_POST["id_creneau"] :(isset($_GET["id_creneau"]) ? $_GET["id_creneau"] : NULL);
$id_cours = isset($_POST["id_cours"]) ? $_POST["id_cours"] :(isset($_GET["id_cours"]) ? $_GET["id_cours"] :(isset($_SESSION["id_cours"]) ? $_SESSION["id_cours"] : NULL));
$type_selection = isset($_POST["type_selection"]) ? $_POST["type_selection"] :(isset($_GET["type_selection"]) ? $_GET["type_selection"] :(isset($_SESSION["type_selection"]) ? $_SESSION["type_selection"] : NULL));
$date_absence_eleve_debut_saisir_eleve = isset($_POST["date_absence_eleve_debut_saisir_eleve"]) ? $_POST["date_absence_eleve_debut_saisir_eleve"] :(isset($_GET["date_absence_eleve_debut_saisir_eleve"]) ? $_GET["date_absence_eleve_debut_saisir_eleve"] :(isset($_SESSION["date_absence_eleve_debut_saisir_eleve"]) ? $_SESSION["date_absence_eleve_debut_saisir_eleve"] : NULL));
$date_absence_eleve_fin_saisir_eleve = isset($_POST["date_absence_eleve_fin_saisir_eleve"]) ? $_POST["date_absence_eleve_fin_saisir_eleve"] :(isset($_GET["date_absence_eleve_fin_saisir_eleve"]) ? $_GET["date_absence_eleve_fin_saisir_eleve"] :(isset($_SESSION["date_absence_eleve_fin_saisir_eleve"]) ? $_SESSION["date_absence_eleve_fin_saisir_eleve"] : (isset($_SESSION["date_absence_eleve_debut_saisir_eleve"]) ? $_SESSION["date_absence_eleve_debut_saisir_eleve"] : NULL)));
$id_semaine = isset($_POST["id_semaine"]) ? $_POST["id_semaine"] :(isset($_GET["id_semaine"]) ? $_GET["id_semaine"] :(isset($_SESSION["id_semaine"]) ? $_SESSION["id_semaine"] : NULL));

if (isset($id_groupe) && $id_groupe != null) $_SESSION['id_groupe_abs'] = $id_groupe;
if (isset($id_classe) && $id_classe != null) $_SESSION['id_classe_abs'] = $id_classe;
if (isset($id_aid) && $id_aid != null) $_SESSION['id_aid'] = $id_aid;
if (isset($nom_eleve) && $nom_eleve != null) $_SESSION['nom_eleve'] = $nom_eleve;
if (isset($id_eleve) && $id_eleve != null) $_SESSION['id_eleve'] = $id_eleve;
if (isset($id_cours) && $id_cours != null) $_SESSION['id_cours'] = $id_cours;
if (isset($type_selection) && $type_selection != null) $_SESSION['type_selection'] = $type_selection;
if (isset($date_absence_eleve_debut_saisir_eleve) && $date_absence_eleve_debut_saisir_eleve != null) $_SESSION['date_absence_eleve_debut_saisir_eleve'] = $date_absence_eleve_debut_saisir_eleve;
if (isset($date_absence_eleve_fin_saisir_eleve) && $date_absence_eleve_fin_saisir_eleve != null) $_SESSION['date_absence_eleve_fin_saisir_eleve'] = $date_absence_eleve_fin_saisir_eleve;


//initialisation des variables
$current_eleve = null;
if ($id_semaine == null || $id_semaine == -1) {
    $id_semaine = date('W');
}
if ($date_absence_eleve_debut_saisir_eleve != null) {
    $dt_date_absence_eleve_debut_saisir_eleve = new DateTime(str_replace("/",".",$date_absence_eleve_debut_saisir_eleve));
} else {
    $dt_date_absence_eleve_debut_saisir_eleve = new DateTime('now');
}
if ($date_absence_eleve_fin_saisir_eleve != null) {
    $dt_date_absence_eleve_fin_saisir_eleve = new DateTime(str_replace("/",".",$date_absence_eleve_fin_saisir_eleve));
} else {
    $dt_date_absence_eleve_fin_saisir_eleve = new DateTime('now');
}

if ($type_selection == 'id_groupe') {
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
}

//==============================================
$style_specifique[] = "templates/DefaultEDT/css/style_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
$javascript_specifique[] = "mod_abs2/lib/include";
$titre_page = "Les absences";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();
include('menu_abs2.inc.php');
//===========================
echo "<div class='css-panes' id='containDiv'>\n";

echo "<table cellspacing='15px' cellpadding='5px'><tr>";

//on affiche une boite de selection pour l'eleve
echo "<td style='border : 1px solid; padding : 10px;'>";
echo "<form action=\"./saisir_eleve.php\" method=\"post\" style=\"width: 100%;\">\n";
echo '<p>';
echo 'Nom : <input type="hidden" name="type_selection" value="nom_eleve"/> ';
echo '<input type="text" name="nom_eleve" size="10" value="'.$nom_eleve.'"/> ';
echo '<button type="submit">Rechercher</button>';
echo '</p>';
echo '</form>';
echo '</td>';


//on affiche une boite de selection avec les groupes et les creneaux
//on affiche une boite de selection avec les aid
if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
    $groupe_col = GroupeQuery::create()->orderByName()->useJGroupesClassesQuery()->useClasseQuery()->orderByNom()->endUse()->endUse()
		->leftJoinWith('Groupe.JGroupesClasses')
		->leftJoinWith('JGroupesClasses.Classe')
                ->find();
} else {
    $groupe_col = $utilisateur->getGroupes();
}
if (!$groupe_col->isEmpty()) {
	echo "<td style='border : 1px solid; padding : 10px;'>";
	echo "<form action=\"./saisir_eleve.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo '<p>';
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

	echo '<button type="submit">Afficher les élèves</button>';
	echo '</p>';
	echo "</form>";
	echo "</td>";
}

//on affiche une boite de selection avec les classes
if ((getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe")||($utilisateur->getStatut() == "autre")) {
    $classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
} else {
    $classe_col = $utilisateur->getClasses();
}
if (!$classe_col->isEmpty()) {
	echo "<td style='border : 1px solid; padding : 10px;'>";
	echo "<form action=\"./saisir_eleve.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo '<p>';
	echo '<input type="hidden" name="type_selection" value="id_classe"/>';
	echo ("Classe : <select name=\"id_classe\" style=\"width:160px\">");
	echo "<option value='-1'>choisissez une classe</option>\n";
	foreach ($classe_col as $classe) {
		echo "<option value='".$classe->getId()."'";
		if ($id_classe == $classe->getId()) echo " selected='selected' ";
		echo ">";
		echo $classe->getNom();
		echo "</option>\n";
	}
	echo "</select>&nbsp;";

	echo '<button type="submit">Afficher les élèves</button>';
	echo '</p>';
	echo "</form>";
	echo "</td>";
} else {
    echo '<td>Aucune classe avec élève affecté n\'a été trouvée</td>';
}

//on affiche une boite de selection avec les aid
if ((getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe")||($utilisateur->getStatut() == "autre")) {
    $aid_col = AidDetailsQuery::create()->find();
} else {
    $aid_col = $utilisateur->getAidDetailss();
}
if (!$aid_col->isEmpty()) {
	echo "<td style='border : 1px solid;'>";
	echo "<form action=\"./saisir_eleve.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo '<p>';
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

	echo '<button type="submit">Afficher les élèves</button>';
echo '</p>';
	echo "</form>";
	echo "</td>";
}
echo "</tr></table>";

if (isset($message_enregistrement)) {
	if($temoin_erreur_saisie=="y") {
		echo "<span style='color:red'>".$message_enregistrement."</span>";
	}
	else {
		echo "<span style='color:green'>".$message_enregistrement."</span>";
	}
}

//afichage des eleves
$eleve_col = new PropelCollection();
if ($type_selection == 'id_eleve') {
    $query = EleveQuery::create();
    if ($utilisateur->getStatut() != "cpe" || getSettingValue("GepiAccesAbsTouteClasseCpe")!='yes') {
	$query->filterByUtilisateurProfessionnel($utilisateur);
    }
    $eleve = $query->filterById($id_eleve)
            ->where('Eleve.DateSortie<?','0')
            ->orWhere('Eleve.DateSortie is NULL')
            ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve_debut_saisir_eleve->format('U'))
            ->findOne();
    if ($eleve != null) {
	$eleve_col->append($eleve);
    }
} else if ($type_selection == 'nom_eleve') {
    $query = EleveQuery::create();
    if ($utilisateur->getStatut() != "cpe" || getSettingValue("GepiAccesAbsTouteClasseCpe")!='yes') {
	$query->filterByUtilisateurProfessionnel($utilisateur);
    }
    $eleve_col = $query->filterByNomOrPrenomLike($nom_eleve)
            ->where('Eleve.DateSortie<?','0')
            ->orWhere('Eleve.DateSortie is NULL')
            ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve_debut_saisir_eleve->format('U'))
            ->limit(20)->find();
} elseif (isset($current_groupe) && $current_groupe != null) {
    $query = EleveQuery::create();
    $eleve_col = $query->useJEleveGroupeQuery()
                        ->filterByIdGroupe($current_groupe->getId())
                        ->endUse()
            ->where('Eleve.DateSortie<?','0')
            ->orWhere('Eleve.DateSortie is NULL')
            ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve_debut_saisir_eleve->format('U'))
            ->orderBy('Eleve.Nom','asc')
            ->orderBy('Eleve.Prenom','asc')
            ->distinct()
            ->find();
} elseif (isset($current_aid) && $current_aid != null) {
    $query = EleveQuery::create();
    $eleve_col = $query->useJAidElevesQuery()
                        ->filterByIdAid($current_aid->getId())
                        ->endUse()
            ->where('Eleve.DateSortie<?','0')
            ->orWhere('Eleve.DateSortie is NULL')
            ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve_debut_saisir_eleve->format('U'))
            ->orderBy('Eleve.Nom','asc')
            ->orderBy('Eleve.Prenom','asc')
            ->distinct()
            ->find();
} elseif (isset($current_classe) && !$current_classe == null) {
    $query = EleveQuery::create();
    $eleve_col = $query->useJEleveClasseQuery()
                        ->filterByIdClasse($current_classe->getId())
                        ->endUse()
            ->where('Eleve.DateSortie<?','0')
            ->orWhere('Eleve.DateSortie is NULL')
            ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve_debut_saisir_eleve->format('U'))
            ->orderBy('Eleve.Nom','asc')
            ->orderBy('Eleve.Prenom','asc')
            ->distinct()
            ->find();
}

//afichage de la saisie des absences des eleves
if (!$eleve_col->isEmpty()) {
?>
    <div class="centre_tout_moyen" style="width : 940px;">
		<form autocomplete = "off" method="post" action="enregistrement_saisie_eleve.php" id="liste_absence_eleve">
<p>
		    <input type="hidden" name="total_eleves" value="<?php echo($eleve_col->count()); ?>"/>
</p>
			<p class="choix_fin">
				<input value="Enregistrer" name="Valider" type="submit"  onclick="this.form.submit();this.disabled=true;this.value='En cours'" />
			</p>

<!-- Afichage du tableau de la liste des élèves -->
<!-- Legende du tableau-->
	<?php
	    //echo ('<p>');
	    echo ("<table align='center'><tr><td align='left'>\n");
	    $type_autorises = AbsenceEleveTypeStatutAutoriseQuery::create()->useAbsenceEleveTypeQuery()->orderBySortableRank()->endUse()->filterByStatut($utilisateur->getStatut())->find();
	    if ($type_autorises->count() != 0) {
		    echo ("Type : <select name=\"type_absence\" class=\"small\">");
		    echo "<option value='-1'></option>\n";
		    foreach ($type_autorises as $type) {
			//$type = new AbsenceEleveTypeStatutAutorise();
			    echo "<option value='".$type->getAbsenceEleveType()->getId()."'>";
			    echo $type->getAbsenceEleveType()->getNom();
			    echo "</option>\n";
		    }
		    echo "</select>";
	    }
	    echo ("</td>\n");
	    echo ("<td>&nbsp;&nbsp;&nbsp;</td>\n");
	    echo ("<td align='left'>\n");
	    //echo '&nbsp;&nbsp;&nbsp;';
	    echo 'Commentaire : <input name="commentaire" type="text" maxlength="150" size="20"/>';

	    echo ("</tr>\n");
		//=============================================================
		echo ("<tr><td align='left'>\n");

		//echo '<span title="Non fonctionnel pour le moment" style="color:red; text-decoration: blink;">Motif : </span>';
		echo 'Motif : ';
		$motifs = AbsenceEleveMotifQuery::create()->orderByRank()->find();
		/*
		echo '<form method="post" action="enregistrement_modif_traitement.php">';
		echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
		echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
		echo '<input type="hidden" name="modif" value="motif"/>';
		*/
		echo ("<select name=\"id_motif\">");
		echo "<option value='-1'></option>\n";
		foreach ($motifs as $motif) {
			echo "<option value='".$motif->getId()."'";
			echo ">";
			echo $motif->getNom();
			echo "</option>\n";
		}
		echo "</select>";
		/*
		echo '<button type="submit">Modifier</button>';
		echo '</form>';
		*/

		echo ("</td>\n");
		echo ("<td>&nbsp;&nbsp;&nbsp;</td>\n");
		echo ("<td align='left'>\n");

		//echo '<span title="Non fonctionnel pour le moment" style="color:red; text-decoration: blink;">Justification : </span>';
		echo 'Justification : ';
		$justifications = AbsenceEleveJustificationQuery::create()->orderByRank()->find();
		/*
		echo '<form method="post" action="enregistrement_modif_traitement.php">';
		echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
		echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
		echo '<input type="hidden" name="modif" value="justification"/>';
		*/
		echo ("<select name=\"id_justification\">");
		echo "<option value='-1'></option>\n";
		foreach ($justifications as $justification) {
			echo "<option value='".$justification->getId()."'";
			echo ">";
			echo $justification->getNom();
			echo "</option>\n";
		}
		echo "</select>";
		/*
		echo '<button type="submit">Modifier</button>';
		echo '</form>';
		*/

		echo ("</td>\n</tr>\n");
	    echo ("</table>\n");
		//=============================================================
	//echo '</p> ';
	?>
<!-- Fin de la legende -->

<table><tr><td style="vertical-align : top;">
	<table class="tb_absences" style="width:750px;" summary="Liste des élèves pour l'appel. Colonne 1 : élèves, colonne 2 : absence, colonne3 : retard, colonnes suivantes : suivi de la journée par créneaux, dernière colonne : photos si actif">
		<caption class="invisible no_print">Absences</caption>
		<tbody>
			<tr class="titre_tableau_gestion" style="white-space: nowrap;">
				<th style="text-align : center;" abbr="élèves">Liste des &eacute;l&egrave;ves.
				Sélectionner :
				<a href="#" onclick="SetAllCheckBoxes('liste_absence_eleve', 'active_absence_eleve[]', '', true); return false;">Tous</a>
				<a href="#" onclick="SetAllCheckBoxes('liste_absence_eleve', 'active_absence_eleve[]', '', false); return false;">Aucun</a>
				</th>
				<!--th></th>
				<th></th-->
			</tr>
<?php
//on va recuperer les cours mais seuleument si ils sont identiques pour tout les eleves
$cours_col = new PropelObjectCollection();
if (!$eleve_col->isEmpty()) {
    $cours_col = $eleve_col->getFirst()->getEdtEmplacementCourssPeriodeCalendrierActuelle();
}
echo '<input type="hidden" name="total_eleves" value="'.$eleve_col->count().'" />';
foreach($eleve_col as $eleve) {
			if ($cours_col->isEmpty() || $cours_col->getData() != $eleve->getEdtEmplacementCourssPeriodeCalendrierActuelle()->getData()) {
			    $cours_col = new PropelObjectCollection();
			}
			if ($eleve_col->getPosition() %2 == '1') {
				$background_couleur="#E8F1F4";
			} else {
				$background_couleur="#C6DCE3";
			}
			echo "<tr style='background-color :$background_couleur'>\n";
?>
			<td class='td_abs_eleves' style="width:580px;" >
				<input type="hidden" name="id_eleve_absent[<?php echo $eleve_col->getPosition(); ?>]" value="<?php echo $eleve->getId(); ?>" />
<?php
		  //echo '<a href="./saisir_eleve.php?type_selection=id_eleve&id_eleve='.$eleve->getPrimaryKey().'">';
?>
		  <a href="./saisir_eleve.php?type_selection=id_eleve&amp;id_eleve=<?php echo $eleve->getPrimaryKey() ;?>">
<?php
		  echo '<span class="td_abs_eleves" id="label_nom_prenom_eleve_'.$eleve->getPrimaryKey().'" title="Effectuer une saisie pour cet élève.">'.strtoupper($eleve->getNom()).' '.ucfirst($eleve->getPrenom()).' ('.$eleve->getCivilite().')';
			if(!isset($current_classe) && $eleve->getClasse()!=null){
                            echo ' '.$eleve->getClasse()->getNom().'';
                        }
                        echo'</span>';
?>
           </a>
<?php
			if (isset($message_erreur_eleve[$eleve->getId()]) && $message_erreur_eleve[$eleve->getId()] != '') {
			    echo "<br/>Erreur : ".$message_erreur_eleve[$eleve->getId()];
			}
echo '<div id="edt_'.$eleve->getLogin().'" style="display: none; position: static;"/>';
			echo("</td>");


			echo '<td style="vertical-align: top;"><input style="font-size:88%;" name="active_absence_eleve[]" id="active_absence_eleve_'.$eleve->getPrimaryKey().'" value="'.$eleve->getPrimaryKey().'" type="checkbox" onchange="click_active_absence('.$eleve->getPrimaryKey().')" ';
			if ($eleve_col->count() == 1) {
			    echo "checked=\"true\" ";
			}
			echo '/>';
			echo '</td> ';

			echo("<td style='vertical-align: top;'>");
			// Avec ou sans photo
			if ((getSettingValue("active_module_trombinoscopes")=='y')) {
			    $nom_photo = $eleve->getNomPhoto(1);
			    $photos = $nom_photo;
			   // if (($nom_photo == "") or (!(file_exists($photos)))) {
			    if (($nom_photo == NULL) or (!(file_exists($photos)))) {
				    $photos = "../mod_trombinoscopes/images/trombivide.jpg";
			    }
			    $valeur = redimensionne_image_petit($photos);
			    ?>
		      <div style="float: left;"><label for="active_absence_eleve_<?php echo $eleve->getPrimaryKey(); ?>"><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px;" alt="" title="Cocher cet élève" id="img_photo_eleve_<?php echo $eleve->getPrimaryKey(); ?>" class='trombine' /></label>
		      </div>
<?php
			}
			echo '<div style="float: left;">';
			if ($utilisateur->getAccesFicheEleve($eleve)) {
			    //on est pas sur que le statut autre a acces a l'onglet abs de la fiche donc on affiche pas cet onglet au chargement
				echo '<a href="javascript:centrerpopup(\'../eleves/visu_eleve.php?ele_login='.$eleve->getLogin().'\',600,550,\'scrollbars=yes,statusbar=no,resizable=yes\');">
				    Voir fiche</a>';
			}
			echo '<br>';
			echo "<a href=\"#\" style=\"font-size: 11pt;\"  onclick=\"javascript:
					if (!$('edt_".$eleve->getLogin()."').visible()) {
					    new Ajax.Updater('edt_".$eleve->getLogin()."', './ajax_edt_eleve.php?eleve_login=".$eleve->getLogin()."', {encoding: 'utf-8'});
					    $('edt_".$eleve->getLogin()."').show();
					} else {
					    $('edt_".$eleve->getLogin()."').hide();
					}

					return false;
			\">";
			echo 'voir edt';
			echo '</a>';
			echo '</div>';
			echo '</td>';
echo "</tr>";
}
?>
        </tbody>
    </table>
<?php
echo "</td>";
echo "<td style='width:10px;'>&nbsp;";
echo "</td>";
echo "<td style='width:270px; vertical-align: top;'>";
    echo '<div style="border-width: 1px; border-style: solid; text-align: left; padding : 4px;">';
	echo '<p>';
    echo 'Début : <input size="9" id="date_absence_eleve_debut_saisir_eleve" name="date_absence_eleve_debut_saisir_eleve" value="'.$dt_date_absence_eleve_debut_saisir_eleve->format('d/m/Y').'" />&nbsp;';
   echo '</p>';
     echo '
    <script type="text/javascript">
	Calendar.setup({
	    inputField     :    "date_absence_eleve_debut_saisir_eleve",     // id of the input field
	    ifFormat       :    "%d/%m/%Y",      // format of the input field
	    button         :    "date_absence_eleve_debut_saisir_eleve",  // trigger for the calendar (button ID)
	    align          :    "Bl",           // alignment (defaults to "Bl")
	    singleClick    :    true
	});
    </script>&nbsp;';
$edt_creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();
echo '<br/>';
	echo '<p>';
echo 'Fin : <input size="9" id="date_absence_eleve_fin_saisir_eleve" name="date_absence_eleve_fin_saisir_eleve" value="'.$dt_date_absence_eleve_fin_saisir_eleve->format('d/m/Y').'" />&nbsp;';

	echo '</p>';
	echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "date_absence_eleve_fin_saisir_eleve",     // id of the input field
	ifFormat       :    "%d/%m/%Y",      // format of the input field
	button         :    "date_absence_eleve_fin_saisir_eleve",  // trigger for the calendar (button ID)
	align          :    "Bl",           // alignment (defaults to "Bl")
	singleClick    :    true
    });
</script><br/>';
echo '<div style="border-width: 1px; border-style: solid; text-align: left; padding : 2px; margin : 4px;">';
echo '<p>';
echo 'De <input name="heure_debut_absence_eleve" value="';
echo $edt_creneau_col->getFirst()->getHeuredebutDefiniePeriode("H:i");
echo '" type="text" maxlength="5" size="4" ';
echo ' id="heure_debut_absence_eleve" onKeyDown="clavier_heure2(this.id,event,30,300);" AutoComplete="off" ';
echo '/> à ';

echo '<input name="heure_fin_absence_eleve" value="';
echo $edt_creneau_col->getLast()->getHeurefinDefiniePeriode("H:i");
echo '" type="text" maxlength="5" size="4"';
echo ' id="heure_fin_absence_eleve" onKeyDown="clavier_heure2(this.id,event,30,300);" AutoComplete="off" ';
echo '/><br/>';

echo '<input type="radio" name="multisaisie" value="n" checked="checked" />';
echo '	Créer une seule saisie <br/>';
echo '	<input type="radio" name="multisaisie" value="y"/>';
echo '	Créer une saisie par jour';
echo '</p></div>';
echo 'ou ';
echo '<div style="border-width: 1px; border-style: solid; text-align: left; padding : 2px; margin : 4px;">';
    echo '<p>';
    echo ("<select name=\"id_creneau\" class=\"small\">");


    echo "<option value='-1'>choisissez un créneau</option>\n";
    foreach ($edt_creneau_col as $edt_creneau) {
	//$edt_creneau = new EdtCreneau();
	    echo "<option value='".$edt_creneau->getIdDefiniePeriode()."'";
	    if ($id_creneau == $edt_creneau->getIdDefiniePeriode()) echo " selected='selected' ";
	    echo ">";
	    echo $edt_creneau->getDescription();
	    echo "</option>\n";
    }
    echo "</select></p></div>";
echo '</div>';
//on affiche une boite de selection avec les cours
if (!$cours_col->isEmpty()) {
	//echo '<br/>ou<br/><br/>';
    echo '<p>ou</p>';
	echo '<div style="border-width: 1px; border-style: solid; text-align: left; padding : 4px;">';
    echo '<p>';
	echo ("<select name=\"id_cours\" class=\"small\">");
	echo "<option value='-1'>choisissez un cours</option>\n";
	foreach ($cours_col as $edt_cours) {
	    //$edt_cours = new EdtEmplacementCours();
		if ($edt_cours->getEdtCreneau() == NULL) {
		    //on affiche pas le cours si il n'est associé avec aucun creneau
		    continue;
		}
		echo "<option value='".$edt_cours->getIdCours()."'";
		if ($id_cours == $edt_cours->getIdCours()) echo " selected='selected' ";
		echo ">";
		echo $edt_cours->getDescription();
		echo "</option>\n";
	}
	echo "</select>";
    echo '</p>';


	$col = EdtSemaineQuery::create()->find();
	if (isset($_GET["affiche_toute_semaine"])) {
    echo '<p>';
	    //on va commencer la liste à la semaine 31 (milieu des vacances d'ete)
	    for ($i = 0; $i < $col->count(); $i++) {
		$pos = ($i + 30) % $col->count();
		$semaine = $col[$pos];
		//$semaine = new EdtSemaine();
		    echo "<input type='checkbox' name='semaine_".$semaine->getPrimaryKey()."'/>";
		    echo "Semaine ".$semaine->getNumEdtSemaine()." ".$semaine->getTypeEdtSemaine();
		    if (date('W') == $semaine->getPrimaryKey())  {
			echo " (courante) ";
		    } else {
			echo " du ".$semaine->getLundi('d/m').' au '.$semaine->getSamedi('d/m');
		    }
		    echo "<br/>\n";
	    }
	    echo '<a href="./saisir_eleve.php">Ne pas afficher toutes les semaines</a>';
    echo '</p>';
	} else {
    echo '<p>';
	    //on va commencer la liste à la semaine 31 (milieu des vacances d'ete)
	    for ($i = 0; $i < 10; $i++) {
		$pos = ($i + date('W') + 2*$col->count() - 5) % $col->count();
		$semaine = $col[$pos];
		//$semaine = new EdtSemaine();
		    echo "<input type='checkbox' name='semaine_".$semaine->getPrimaryKey()."'/>";
		    echo "Semaine ".$semaine->getNumEdtSemaine()." ".$semaine->getTypeEdtSemaine();
		    if (date('W') == $semaine->getPrimaryKey())  {
			echo " (courante) ";
		    } else {
			echo " du ".$semaine->getLundi('d/m').' au '.$semaine->getSamedi('d/m');
		    }
		    echo "<br/>\n";
	    }
	    echo '<a href="./saisir_eleve.php?affiche_toute_semaine=oui">Afficher toutes les semaines</a>';
    echo '</p>';
	}
	echo '</div>';

}

echo "</td></tr>";
?>
</table>
</form>
</div>
<?php
}
echo "</div>\n";

require_once("../lib/footer.inc.php");

?>
