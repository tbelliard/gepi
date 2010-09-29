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

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite") {
    die("acces interdit");
}

if (isset($_POST["creation_traitement"]) || isset($_POST["ajout_traitement"])) {
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
$choix_regime = isset($_POST["choix_regime"]) ? $_POST["choix_regime"] :(isset($_GET["choix_regime"]) ? $_GET["choix_regime"] : NULL);
//if ($date_absence_eleve != null) {$_SESSION["date_absence_eleve"] = $date_absence_eleve;}

include('include_pagination.php');

//initialisation des variables
$current_classe = null;
$current_groupe = null;
$current_aid = null;
if ($date_absence_eleve != null) {
    try {
	$dt_date_absence_eleve = new DateTime(str_replace("/",".",$date_absence_eleve));
    } catch (Exception $x) {
	try {
	    $dt_date_absence_eleve = new DateTime($date_absence_eleve);
	} catch (Exception $x) {
	   $dt_date_absence_eleve = new DateTime('now');
	}
    }
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
$javascript_specifique[] = "mod_abs2/lib/include";
$titre_page = "Absences du jour";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
$dojo = true;
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

include('menu_abs2.inc.php');
include('menu_bilans.inc.php');
//===========================

?><!--//affichage d'une page blanche de chargement pour les widget dojo.-->
<div id="overlay_container"></div>
<script language="javascript">
   dojo.style("overlay_container", "opacity", "0");
   dojo.byId("overlay_container").innerHTML = '<div id="overlay" style="background:#fff; width:100%; height:100%; position:absolute; top:0; left:0;"><div class="innerOverlay">Chargement</div></div>';
    var fadeArgs = {
	node: "overlay_container",
	duration: 250
    };
    dojo.fadeIn(fadeArgs).play();
</script>
<?php


echo "<div class='css-panes' id='containDiv'>\n";

echo "<table cellspacing='15px' cellpadding='5px'><tr>";

//on affiche une boite de selection avec les groupes et les creneaux
if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
    $groupe_col = GroupeQuery::create()->orderByName()->useJGroupesClassesQuery()->useClasseQuery()->orderByNom()->endUse()->endUse()->find();
} else {
    $groupe_col = $utilisateur->getGroupes();
}
if (!$groupe_col->isEmpty()) {
    echo "<td style='border : 1px solid; padding : 10 px;'>";
    echo "<form action=\"./absences_du_jour.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>\n";
    echo '<input type="hidden" name="type_selection" value="id_groupe"/>';
    echo ("Groupe : <select name=\"id_groupe\" onchange='submit()' class=\"small\">");
    echo "<option value='-1'>choisissez un groupe</option>\n";
    foreach ($groupe_col as $group) {
	    echo "<option value='".$group->getId()."'";
	    if ($id_groupe == $group->getId()) echo " SELECTED ";
	    echo ">";
	    echo $group->getNameAvecClasses();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    echo"<input type='hidden' name='date_absence_eleve' value='$date_absence_eleve'/>";
    echo '<button type="submit">Afficher les élèves</button>';
	echo "</p>\n";
    echo "</form>";
    echo "</td>";
}

//on affiche une boite de selection avec les classe
if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
    $classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
} else {
    $classe_col = $utilisateur->getClasses();
}
if (!$classe_col->isEmpty()) {
    echo "<td style='border : 1px solid; padding : 10 px;'>";
    echo "<form action=\"./absences_du_jour.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>\n";
    echo '<input type="hidden" name="type_selection" value="id_classe"/>';
    echo ("Classe : <select name=\"id_classe\" onchange='submit()' class=\"small\">");
    echo "<option value='-1'>choisissez une classe</option>\n";
    foreach ($classe_col as $classe) {
	    echo "<option value='".$classe->getId()."'";
	    if ($id_classe == $classe->getId()) echo " SELECTED ";
	    echo ">";
	    echo $classe->getNom();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    echo"<input type='hidden' name='date_absence_eleve' value='$date_absence_eleve'/>";
    echo '<button type="submit">Afficher les élèves</button>';
	echo "</p>\n";
    echo "</form>";
    echo "</td>";
} else {
    echo '<td>Aucune classe avec élève affecté n\'a été trouvée</td>';
}


//on affiche une boite de selection avec les aid et les creneaux
if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
    $aid_col = AidDetailsQuery::create()->find();
} else {
    $aid_col = $utilisateur->getAidDetailss();
}
if (!$aid_col->isEmpty()) {
    echo "<td style='border : 1px solid;'>";
    echo "<form action=\"./absences_du_jour.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>\n";
    echo '<input type="hidden" name="type_selection" value="id_aid"/>';
    echo ("Aid : <select name=\"id_aid\" onchange='submit()' class=\"small\">");
    echo "<option value='-1'>choisissez une aid</option>\n";
    foreach ($aid_col as $aid) {
	    echo "<option value='".$aid->getPrimaryKey()."'";
	    if ($id_aid == $aid->getPrimaryKey()) echo " SELECTED ";
	    echo ">";
	    echo $aid->getNom();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    echo"<input type='hidden' name='date_absence_eleve' value='$date_absence_eleve'/>";
    echo '<button type="submit">Afficher les élèves</button>';
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
echo"<input type='hidden' name='date_absence_eleve' value='$date_absence_eleve'/>";
echo '<button type="submit">Rechercher</button>';
	echo "</p>\n";
echo '</form>';
echo '</td>';
//on affiche une boite de selection pour le regime
echo "<td style='border : 1px solid; padding : 10 px;'>";
echo "<form action=\"./absences_du_jour.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>\n";
        echo '<input type="hidden" name="type_selection" value="choix_regime"/>';
echo ("Régime : <select name=\"choix_regime\" onchange='submit()' class=\"small\">");
    echo "<option value='-1'>choisissez un régime</option>\n";
    	    echo "<option value='d/p'";
	    if ($choix_regime == 'd/p') echo " SELECTED ";
	    echo ">";
	    echo 'd/p';
            echo "<option value='ext.'";
	    if ($choix_regime == 'ext.') echo " SELECTED ";
	    echo ">";
	    echo 'ext.';
            echo "<option value='int.'";
	    if ($choix_regime == 'int.') echo " SELECTED ";
	    echo ">";
	    echo 'int.';
	    echo "</option>\n";
    echo "</select>&nbsp;";
    echo"<input type='hidden' name='date_absence_eleve' value='$date_absence_eleve'/>";
    echo '<button type="submit">Filtrer sur le régime</button>';
echo '</form>';
echo '</td>';

echo "</tr></table>";

if (isset($message_erreur_traitement)) {
    echo $message_erreur_traitement;
}

if (isset($message_enregistrement)) {
    echo($message_enregistrement);
}

//afichage des eleves.
$eleve_col = new PropelCollection();

if ($type_selection == 'id_eleve') {
    $query = EleveQuery::create()->orderBy('Nom', Criteria::ASC)->orderBy('Prenom', Criteria::ASC);
    if ($utilisateur->getStatut() != "cpe" || getSettingValue("GepiAccesAbsTouteClasseCpe")!='yes') {
	$query->filterByUtilisateurProfessionnel($utilisateur);
    }
    $eleve_col->append($query->findPk($id_eleve));
} else if ($type_selection == 'nom_eleve') {
    $query = EleveQuery::create()->orderBy('Nom', Criteria::ASC)->orderBy('Prenom', Criteria::ASC);
    if ($utilisateur->getStatut() != "cpe" || getSettingValue("GepiAccesAbsTouteClasseCpe")!='yes') {
	$query->filterByUtilisateurProfessionnel($utilisateur);
    }
    $eleve_col = $query->filterByNomOrPrenomLike($nom_eleve)->paginate($page_number, $item_per_page);
}else if ($type_selection == 'choix_regime' && $choix_regime!=-1) {
    $query = EleveQuery::create()->orderBy('Nom', Criteria::ASC)->orderBy('Prenom', Criteria::ASC);
    if ($utilisateur->getStatut() != "cpe" || getSettingValue("GepiAccesAbsTouteClasseCpe")!='yes') {
	$query->filterByUtilisateurProfessionnel($utilisateur);
    }
    $eleve_col = $query->filterByRegime($choix_regime)->paginate($page_number, $item_per_page);
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
    //on récupere les saisies car avant puis on va filtrer avec les ids car filterManquementObligationPresence bug un peu avec les requetes imbriquées
    $saisie_col = AbsenceEleveSaisieQuery::create()->select('Id')->filterByPlageTemps($dt_debut, $dt_fin)->filterByManquementObligationPresence()->setFormatter(ModelCriteria::FORMAT_ARRAY)->find();
    $query = EleveQuery::create();
    if ($utilisateur->getStatut() != "cpe" || getSettingValue("GepiAccesAbsTouteClasseCpe")!='yes') {
	$query->filterByUtilisateurProfessionnel($utilisateur);        
    }
    $eleve_col = $query
            ->orderBy('Nom', Criteria::ASC)->orderBy('Prenom', Criteria::ASC)
	    ->useAbsenceEleveSaisieQuery()
	    ->filterById($saisie_col->toKeyValue('Id', 'Id'))
	    ->endUse()->distinct()->paginate($page_number, $item_per_page);
}

?>
	<div style="text-align: center">
			    <!-- <p class="expli_page choix_fin"> -->
				    <form action="./absences_du_jour.php" name="absences_du_jour" id="absences_du_jour" method="post" style="width: 100%;">
			    <p class="expli_page choix_fin">
				<input type="hidden" name="type_selection" value="<?php echo $type_selection?>"/>
				<input type="hidden" name="nom_eleve" value="<?php echo $nom_eleve?>"/>
				<input type="hidden" name="id_eleve" value="<?php echo $id_eleve?>"/>
				<input type="hidden" name="id_groupe" value="<?php echo $id_groupe?>"/>
				<input type="hidden" name="id_classe" value="<?php echo $id_classe?>"/>
				<input type="hidden" name="id_aid" value="<?php echo $id_aid?>"/>
                                <input type="hidden" name="choix_regime" value="<?php echo $choix_regime?>"/>
                                <input type="hidden" name="date_absence_eleve" value="<?php echo $date_absence_eleve?>"/>
                                <input type="hidden" name="reinit_filtre" value="n"/>
				    <input onchange="document.absences_du_jour.submit()" style="width : 7em" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve" name="date_absence_eleve" value="<?php echo $dt_date_absence_eleve->format('Y-m-d')?>" />
				    <button dojoType="dijit.form.Button" type="submit" onClick="
					document.absences_du_jour.type_selection.value='';
					document.absences_du_jour.nom_eleve.value='';
					document.absences_du_jour.id_eleve.value='';
					document.absences_du_jour.id_groupe.value='';
					document.absences_du_jour.id_classe.value='';
					document.absences_du_jour.id_aid.value='';
                                        document.absences_du_jour.choix_regime.value='';
					document.absences_du_jour.date_absence_eleve.value='';
					document.absences_du_jour.reinit_filtre.value='y';
					return true;">Réinitialiser les filtres</button>
			    </p>
			<?php
			if ($eleve_col->count() != 0) {
			    if (method_exists($eleve_col, 'haveToPaginate')) {
				if ($eleve_col->haveToPaginate()) {
				    echo "Page ";
				    echo '<input type="submit" name="page_deplacement" value="-"/>';
				    echo '<input type="text" name="page_number" size="1" value="'.$eleve_col->getPage().'"/>';
				    echo '<input type="submit" name="page_deplacement" value="+"/> ';
				    echo "sur ".$eleve_col->getLastPage()." page(s) ";
				    echo "| ";
				}
				echo "Voir ";
				echo '<input type="text" name="item_per_page" size="1" value="'.$item_per_page.'"/>';
				echo " par page |  Nombre d'enregistrements : ";
				echo $eleve_col->count();
				echo '<button dojoType="dijit.form.Button" type="submit">Afficher</button>';
			    }
			}
			?>
			    </form>
				<!--     <br/> -->
			<!-- </p> -->
<?php if ($eleve_col->count() != 0) { ?>
			<form dojoType="dijit.form.Form" jsId="creer_traitement" id="creer_traitement" name="creer_traitement" method="post" action="./absences_du_jour.php">
			<input type="hidden" id="creation_traitement" name="creation_traitement" value="no"/>
			<input type="hidden" id="ajout_traitement" name="ajout_traitement" value="no"/>
			<input type="hidden" id="id_traitement" name="id_traitement" value=""/>
			<p>
			<div dojoType="dijit.form.DropDownButton" style="display: inline">
			    <span>Ajouter au traitement</span>
			    <div dojoType="dijit.Menu" style="display: inline">
				<button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_traitement').value = 'yes'; document.getElementById('ajout_traitement').value = 'no'; document.creer_traitement.submit();">
				    Créer un nouveau traitement
				</button>
			<?php
			$id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
			if ($id_traitement != null && AbsenceEleveTraitementQuery::create()->findPk($id_traitement) != null) {
			    $traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
			    echo '	<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; document.getElementById(\'id_traitement\').value = \''.$id_traitement.'\'; document.creer_traitement.submit();">'."\n";
			    echo '	    Ajouter les saisies au traitement n° '.$id_traitement.' ('.$traitement->getDescription().')'."\n";
			    echo '	</button>'."\n";
			}
			?>
			    </div>
			</div>
			<div dojoType="dijit.form.DropDownButton" style="display: inline">
			    <span>Ajouter au traitement (popup)</span>
			    <div dojoType="dijit.Menu" style="display: inline">
				<button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_traitement').value = 'yes'; document.getElementById('ajout_traitement').value = 'no'; pop_it(document.creer_traitement)">
				    Créer un nouveau traitement dans une popup
				</button>
			<?php
			$id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
			if ($id_traitement != null && AbsenceEleveTraitementQuery::create()->findPk($id_traitement) != null) {
			    $traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
			    echo '	<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; document.getElementById(\'id_traitement\').value = \''.$id_traitement.'\'; pop_it(document.creer_traitement);">'."\n";
			    echo '	    Ajouter les saisies au traitement n° '.$id_traitement.' ('.$traitement->getDescription().') dans une popup'."\n";
			    echo '	</button>'."\n";
			}
			?>
			    </div>
			</div>
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
    $compteur = 0;
    foreach($eleve_col as $eleve) {
	$compteur = $compteur + 1;
        $regime_eleve=EleveRegimeDoublantQuery::create()->findPk($eleve->getlogin())->getRegime();
		//$eleve = new Eleve();
			$traitement_col = new PropelCollection();//liste des traitements pour afficher des boutons 'ajouter au traitement'

			$saisie_affiches = array ();
			if ($compteur % 2 == '1') {
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
			<td>
<?php
			echo strtoupper($eleve->getNom()).' '.ucfirst($eleve->getPrenom()).' ('.$eleve->getCivilite().') ('.$regime_eleve.')';
			echo ' ';
			echo $eleve->getClasseNom($dt_date_absence_eleve);
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
					    $traitement_col->addCollection($absence->getAbsenceEleveTraitements());
					    if ($absence->isSaisiesContradictoiresManquementObligation()) {
					    //if (!($absence->getSaisiesContradictoiresManquementObligation()->isEmpty())) {
						$violet = true;
						break;
					    }
					    if ($red || $absence->getManquementObligationPresence()) {
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
					    echo '<nobr><input eleve_id="'.$eleve->getPrimaryKey().'" name="select_saisie[]" value="'.$saisie->getPrimaryKey().'" type="checkbox" ';
					    if ($saisie->getNotifiee()) {echo 'saisie_notifiee="true"';}
					    if ($saisie->getTraitee()) {echo 'saisie_traitee="true"';}
					    echo '/>';
					    echo ("<a style='font-size:88%;' href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."'>".$saisie->getPrimaryKey());
					    if ($saisie->getNotifiee()) {echo " (notifiée)";}
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

			echo '<td style="width : 7em">';
			echo '<div add_select_shorcuts_button="true" eleve_id="'.$eleve->getPrimaryKey().'"></div>';
			echo '<div dojoType="dijit.form.DropDownButton"  style="white-space: nowrap; display: inline">
			    <span>Ajouter au traitement</span>
			    <div dojoType="dijit.Menu"  style="white-space: nowrap; display: inline">
				<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_traitement\').value = \'yes\'; document.getElementById(\'ajout_traitement\').value = \'no\'; document.creer_traitement.submit();">
				    Créer un nouveau traitement
				</button>';
			foreach ($traitement_col as $traitement) {
			    echo '<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'id_traitement\').value = \''.$traitement->getId().'\'; document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; document.creer_traitement.submit();">';
			    echo ' Ajouter au traitement n° '.$traitement->getId().' ('.$traitement->getDescription().')';
			    echo '</button>';
			}
			echo '</div></div><br/>';

			echo '<div dojoType="dijit.form.DropDownButton"  style="white-space: nowrap; display: inline">
			    <span>Ajouter (popup)</span>
			    <div dojoType="dijit.Menu"  style="white-space: nowrap; display: inline">
				<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_traitement\').value = \'yes\'; document.getElementById(\'ajout_traitement\').value = \'no\'; pop_it(document.creer_traitement);">
				    Créer un nouveau traitement (popup)
				</button>';
			foreach ($traitement_col as $traitement) {
			    echo '<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'id_traitement\').value = \''.$traitement->getId().'\'; document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; pop_it(document.creer_traitement);">';
			    echo ' Ajouter au traitement n° '.$traitement->getId().' ('.$traitement->getDescription().')';
			    echo '</button>';
			}
			echo '</div></div>';
			echo '</td>';
			echo "</tr>";
    }

    echo " </tbody>";
    echo "</table>";
    echo '<table><tr>';
    echo '<td>Légende : </td>';
    echo '<td style="border : 1px solid; background-color : red;">absent</td>';
    echo '<td style="border : 1px solid; background-color : green;">présent</td>';
    echo '<td style="border : 1px solid; background-color : purple;">Saisies conflictuelles</td>';
    echo '<td style="border : 1px solid;">Sans couleur : pas de saisie</td>';
    echo '</tr></table>';
    ?>
    <div dojoType="dijit.form.DropDownButton" style="display: inline">
	<span>Ajouter Les saisies cochées à un traitement</span>
	<div dojoType="dijit.Menu" style="display: inline">
	    <button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_traitement').value = 'yes'; document.getElementById('ajout_traitement').value = 'no'; document.creer_traitement.submit();">
		Créer un nouveau traitement
	    </button>
	    <button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_traitement').value = 'yes'; document.getElementById('ajout_traitement').value = 'no'; pop_it(document.creer_traitement)">
		Créer un nouveau traitement dans une popup
	    </button>
    <?php
    $id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
    if ($id_traitement != null && AbsenceEleveTraitementQuery::create()->findPk($id_traitement) != null) {
	$traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
	echo '	<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; document.getElementById(\'id_traitement\').value = \''.$id_traitement.'\'; document.creer_traitement.submit();">'."\n";
	echo '	    Ajouter les saisies au traitement n° '.$id_traitement.' ('.$traitement->getDescription().')'."\n";
	echo '	</button>'."\n";
	echo '	<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; document.getElementById(\'id_traitement\').value = \''.$id_traitement.'\'; pop_it(document.creer_traitement);">'."\n";
	echo '	    Ajouter les saisies au traitement n° '.$id_traitement.' ('.$traitement->getDescription().') dans une popup'."\n";
	echo '	</button>'."\n";
    }
    ?>
	</div>
    </div>
    <?php
    echo '<input type="hidden" name="nb_checkbox" value="'.$nb_checkbox.'"/>';

} else {
    echo 'Aucune absence';
}
echo "</p>";
echo "</form>";
echo "</div>\n";
echo "</div>\n";

$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dijit.form.Button");
    dojo.require("dijit.Menu");
    dojo.require("dijit.form.Form");
    dojo.require("dijit.form.CheckBox");
    dojo.require("dijit.form.DateTextBox");
    dojo.require("dojo.parser");

    dojo.addOnLoad(function() {
	dojo.query("[add_select_shorcuts_button=true]").forEach(function(node, index, arr){
	    var menu = new dijit.Menu({
		style: "display: none;"
	    });
	    var menuItem1 = new dijit.MenuItem({
		label: "tous",
		onClick: function() {
		    var eleve_id = dojo.attr(node,\'eleve_id\');
		    var query_string = \'input[type=checkbox][eleve_id=\'+eleve_id+\']\';
		    dojo.query(query_string).attr(\'checked\', true);
		}
	    });
	    menu.addChild(menuItem1);

	    var menuItem2 = new dijit.MenuItem({
		label: "aucun",
		onClick: function() {
		    var eleve_id = dojo.attr(node,\'eleve_id\');
		    var query_string = \'input[type=checkbox][eleve_id=\'+eleve_id+\']\';
		    dojo.query(query_string).attr(\'checked\', false);
		}
	    });
	    menu.addChild(menuItem2);

	    var menuItem3 = new dijit.MenuItem({
		label: "non traités",
		onClick: function() {
		    var eleve_id = dojo.attr(node,\'eleve_id\');
		    var query_string = \'input[type=checkbox][eleve_id=\'+eleve_id+\']\';
		    dojo.query(query_string).attr(\'checked\', true);
		    query_string = \'input[type=checkbox][eleve_id=\'+eleve_id+\'][saisie_traitee=true]\';
		    dojo.query(query_string).attr(\'checked\', false);
		}
	    });
	    menu.addChild(menuItem3);

	    var menuItem4 = new dijit.MenuItem({
		label: "non notifiés",
		onClick: function() {
		    var eleve_id = dojo.attr(node,\'eleve_id\');
		    var query_string = \'input[type=checkbox][eleve_id=\'+eleve_id+\']\';
		    dojo.query(query_string).attr(\'checked\', true);
		    query_string = \'input[type=checkbox][eleve_id=\'+eleve_id+\'][saisie_notifiee=true]\';
		    dojo.query(query_string).attr(\'checked\', false);
		}
	    });
	    menu.addChild(menuItem4);

	    var button = new dijit.form.DropDownButton({
		label: "Selectionner",
		dropDown: menu
	    });
	    node.appendChild(button.domNode);
	});

	//effacement de la page blanche de chargement
	dojo.fadeOut({
		      node:"overlay",
                      onEnd: function(){
                             // hide it completely after fadeout
                             dojo.style("overlay","display","none");
                      }
                }).play();
	//dojo.byId("overlay").hide();
    });
</script>';

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