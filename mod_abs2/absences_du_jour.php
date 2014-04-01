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

if (isset($_POST["creation_traitement"]) || isset($_POST["ajout_traitement"])) {
    include('creation_traitement.php');
}

if (isset($_POST["creation_notification"])) {
    include('creation_notification.php');
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
$filter_regime = isset($_POST["filter_regime"]) ? $_POST["filter_regime"] :(isset($_GET["filter_regime"]) ? $_GET["choix_regime"] : NULL);

//if ($date_absence_eleve != null) {$_SESSION["date_absence_eleve"] = $date_absence_eleve;}
include('include_requetes_filtre_de_recherche.php');

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
$utilisation_scriptaculous="ok";
$utilisation_win = 'oui';
$_SESSION['cacher_header'] = "y";
$dojo = true;
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

include('menu_abs2.inc.php');
include('menu_bilans.inc.php');
//===========================

echo "<div class='css-panes' id='containDiv'>\n";

echo "<table cellspacing='15px' cellpadding='5px'><tr>";

//on affiche une boite de selection avec les groupes et les creneaux
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
    echo "<form dojoType=\"dijit.form.Form\" action=\"./absences_du_jour.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>\n";
    echo '<input type="hidden" name="type_selection" value="id_groupe"/>';
    echo ("Groupe : <select dojoType=\"dijit.form.Select\" maxheight=\"-1\" style=\"width :12em;font-size:12px;\" name=\"id_groupe\" onchange='submit()' class=\"small\">");
    echo "<option value='-1'>choisissez un groupe</option>\n";
    foreach ($groupe_col as $group) {
	    echo "<option value='".$group->getId()."'";
	    if ($id_groupe == $group->getId()) echo " selected='SELECTED' ";
	    echo ">";
	    echo $group->getNameAvecClasses();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    echo"<input type='hidden' name='date_absence_eleve' value='$date_absence_eleve'/>";
    echo '<button style="font-size:12px" dojoType="dijit.form.Button" type="submit">Afficher les élèves</button>';
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
    echo ("Classe : <select dojoType=\"dijit.form.Select\" maxheight=\"-1\" style=\"width :12em;font-size:12px;\" name=\"id_classe\" onchange='submit()' class=\"small\">");
    echo "<option value='-1'>choisissez une classe</option>\n";
    foreach ($classe_col as $classe) {
	    echo "<option value='".$classe->getId()."'";
	    if ($id_classe == $classe->getId()) echo " selected='SELECTED' ";
	    echo ">";
	    echo $classe->getNom();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    echo"<input type='hidden' name='date_absence_eleve' value='$date_absence_eleve'/>";
    echo '<button style="font-size:12px" dojoType="dijit.form.Button" type="submit">Afficher les élèves</button>';
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
    echo ("Aid : <select dojoType=\"dijit.form.Select\" maxheight=\"-1\" style=\"width :12em;font-size:12px;\" name=\"id_aid\" onchange='submit()' class=\"small\">");
    echo "<option value='-1'>choisissez une aid</option>\n";
    foreach ($aid_col as $aid) {
	    echo "<option value='".$aid->getPrimaryKey()."'";
	    if ($id_aid == $aid->getPrimaryKey()) echo " selected='SELECTED' ";
	    echo ">";
	    echo $aid->getNom();
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    echo"<input type='hidden' name='date_absence_eleve' value='$date_absence_eleve'/>";
    echo '<button style="font-size:12px" dojoType="dijit.form.Button" type="submit">Afficher les élèves</button>';
	echo "</p>\n";
    echo "</form>";
    echo "</td>";
}

//on affiche une boite de selection pour l'eleve
echo "<td style='border : 1px solid; padding : 10 px;'>";
echo "<form action=\"./absences_du_jour.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>\n";
echo 'Nom : <input type="hidden" name="type_selection" value="nom_eleve"/> ';
echo '<input dojoType="dijit.form.TextBox" type="text" name="nom_eleve" style="width : 10em" value="'.$nom_eleve.'"/> ';
echo"<input type='hidden' name='date_absence_eleve' value='$date_absence_eleve'/>";
echo '<button style="font-size:12px" dojoType="dijit.form.Button" type="submit">Rechercher</button>';
	echo "</p>\n";
echo '</form>';
echo '</td>';
//on affiche une boite de selection pour le regime
echo "<td style='border : 1px solid; padding : 10 px;'>";
echo "<form action=\"./absences_du_jour.php\" method=\"post\" style=\"width: 100%;\">\n";
	echo "<p>\n";
    echo ("Régime : <select dojoType=\"dijit.form.Select\" maxheight=\"-1\" style=\"width :12em;font-size:12px;\" name=\"filter_regime\" onchange='submit()' class=\"small\">");
    echo "<option value='-1'>choisissez un régime</option>\n";
    	    echo "<option value='d/p'";
	    if (getFiltreRechercheParam('filter_regime') == 'd/p') echo " selected='SELECTED' ";
	    echo ">";
	    echo 'd/p';
            echo "<option value='ext.'";
	    if (getFiltreRechercheParam('filter_regime') == 'ext.') echo " selected='SELECTED' ";
	    echo ">";
	    echo 'ext.';
            echo "<option value='int.'";
	    if (getFiltreRechercheParam('filter_regime') == 'int.') echo " selected='SELECTED' ";
	    echo ">";
	    echo 'int.';
	    echo "</option>\n";
    echo "</select>";
    echo "</p>\n";
    echo "<p>\n";
    echo ("Afficher : <select dojoType=\"dijit.form.Select\" maxheight=\"-1\" style=\"font-size:12px;\" name=\"filter_manqement_obligation\" onchange='submit()' class=\"small\">");
    echo "<option value='y'>Manquements à l'obligation de présence</option>";
    	    echo "<option value='n'";
	    if (getFiltreRechercheParam('filter_manqement_obligation') == 'n') echo " selected='SELECTED' ";
	    echo ">";
	    echo 'toutes les saisies';
	    echo "</option>";
    echo "</select>";
    echo "</p>\n";

    echo"<input type='hidden' name='date_absence_eleve' value='$date_absence_eleve'/>";
    echo '<button style="font-size:12px" dojoType="dijit.form.Button" type="submit">Filtrer</button>';
echo '</form>';
echo '</td>';

echo "</tr></table>";
?>
<div class="legende">
    <h3 class="legende">Légende  </h3>
    <table class="legende">
        <tr>
            <td>
            Couleur de fond de cellule     
            </td>
            <td>
              Saisie et traitements  
            </td>
            <td>
              Notifications : Courrier,téléphone,mail ou SMS    
            </td>
        </tr>
        <tr>
            <td>
                <font color="orange">&#9632;</font> Retard<br />
                <font color="red">&#9632;</font> Manquement aux obligations de présence<br />
                <font color="blue">&#9632;</font> Non manquement aux obligations de présence<br /> 
                <font color="green">&#9632;</font> Autre saisie.<br />  
                <?php if (getSettingValue("abs2_alleger_abs_du_jour")!='y'): ?>
                <font color="purple">&#9632;</font> Saisie conflictuelle<br />
                <?php endif; ?>
            </td>
            <td>
                <img src="../images/icons/saisie.png" /> Modifier la saisie<br/>  
               <!-- <img src="../images/icons/flag_green.png" /> Saisie traitée<br/> -->
                <img src="../images/icons/ico_attention.png" /> Saisie non traitée.<br/>                
            </td>
            <td>
            <img src="../images/icons/courrier_envoi.png" /> Saisie en cours de notification (état initial ou en cours).<br/>
            <img src="../images/icons/courrier_retour.png" /> Saisie notifiée (reçue ou reçue avec accusé de réception).<br/>
            </td>    
        </tr>
    </table>     
</div> <br />
<?php
if (isset($message_erreur_traitement)) {
    echo "<span style='color:red'>".$message_erreur_traitement."</span>";
}

if (isset($message_enregistrement)) {
    echo "<span style='color:green'>".$message_enregistrement."</span>";
}

//afichage des eleves.
$eleve_col = new PropelCollection();
//on fait une requete pour recuperer les eleves qui sont absents aujourd'hui
$dt_debut = clone $dt_date_absence_eleve;
$dt_debut->setTime(0,0,0);
$dt_fin = clone $dt_date_absence_eleve;
$dt_fin->setTime(23,59,59);
//on récupere les saisies car avant puis on va filtrer avec les ids car filterManquementObligationPresence bug un peu avec les requetes imbriquées
$saisie_query = AbsenceEleveSaisieQuery::create()->filterByPlageTemps($dt_debut, $dt_fin)->setFormatter(ModelCriteria::FORMAT_ARRAY);
if (!isFiltreRechercheParam('filter_manqement_obligation') || getFiltreRechercheParam('filter_manqement_obligation') != 'n') {
    //par défaut on filtre les manquement à l'obligation de présence
    $saisie_query->filterByManquementObligationPresence();
}
$saisie_col = $saisie_query->find();
$query = EleveQuery::create()->orderBy('Nom', Criteria::ASC)->orderBy('Prenom', Criteria::ASC)
	->useAbsenceEleveSaisieQuery()
	->filterById($saisie_col->toKeyValue('Id', 'Id'))
	->endUse();
if ($utilisateur->getStatut() != "cpe" || getSettingValue("GepiAccesAbsTouteClasseCpe")!='yes') {
    $query->filterByUtilisateurProfessionnel($utilisateur);
}

if ($type_selection == 'id_eleve') {    
    $eleve_col->append($query->findPk($id_eleve));
} else if ($type_selection == 'nom_eleve') {    
    $query->filterByNomOrPrenomLike($nom_eleve);
} elseif ($current_groupe != null) {   
    $query->useJEleveGroupeQuery()->filterByIdGroupe($current_groupe->getId())->enduse();    
} elseif ($current_aid != null) {    
    $query->useJAidElevesQuery()->filterByIdAid($current_aid->getId())->enduse();  
} elseif ($current_classe != null) {   
    $query->useJEleveClasseQuery()->filterByIdClasse($current_classe->getId())->enduse();    
} else {
    //rien à faire
}
if ($type_selection != 'id_eleve' && $type_selection != 'nom_eleve') {
    //on filtre
    if (isFiltreRechercheParam('filter_regime') != null && getFiltreRechercheParam('filter_regime')!=-1) {
        $query->filterByRegime($filter_regime);
    }
}
$eleve_col = $query
                ->where('Eleve.DateSortie<?','0')
                ->orWhere('Eleve.DateSortie is NULL')
                ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve->format('U'))
                ->distinct()->paginate($page_number, $item_per_page);

?>
	<div style="text-align: center">
			    <!-- <p class="expli_page choix_fin"> -->
				    <form dojoType="dijit.form.Form" action="./absences_du_jour.php" name="absences_du_jour" id="absences_du_jour" method="post" style="width: 100%;">
			    <p class="expli_page choix_fin">
				<input type="hidden" name="type_selection" value="<?php echo $type_selection?>"/>
				<input type="hidden" name="nom_eleve" value="<?php echo $nom_eleve?>"/>
				<input type="hidden" name="id_eleve" value="<?php echo $id_eleve?>"/>
				<input type="hidden" name="id_groupe" value="<?php echo $id_groupe?>"/>
				<input type="hidden" name="id_classe" value="<?php echo $id_classe?>"/>
				<input type="hidden" name="id_aid" value="<?php echo $id_aid?>"/>
                                <input type="hidden" name="filter_regime" value="<?php echo $filter_regime?>"/>
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
                                        document.absences_du_jour.filter_regime.value='';
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

				echo "<br />\n";
				$signaler_saisies_englobees=isset($_POST['signaler_saisies_englobees']) ? $_POST['signaler_saisies_englobees'] : NULL;
				$checked_ou_pas="";
				if($signaler_saisies_englobees=="y") {$checked_ou_pas=" checked";}
				echo "<input type='checkbox' id='signaler_saisies_englobees' name='signaler_saisies_englobees' value='y'$checked_ou_pas /><label for='signaler_saisies_englobees'>Signaler les saisies englobées <img src='../images/icons/ico_toit2.png' width='16' height='16' title='Témoin que la saisie est englobée' /></label>\n";

				echo " - ";
				$ne_pas_afficher_saisies_englobees=isset($_POST['ne_pas_afficher_saisies_englobees']) ? $_POST['ne_pas_afficher_saisies_englobees'] : NULL;
				$checked_ou_pas="";
				if($ne_pas_afficher_saisies_englobees=="y") {$checked_ou_pas=" checked";}
				echo "<input type='checkbox' id='ne_pas_afficher_saisies_englobees' name='ne_pas_afficher_saisies_englobees' value='y'$checked_ou_pas /><label for='ne_pas_afficher_saisies_englobees' title='Ne pas afficher les saisies englobées... sous réserve que les saisies ne soient pas conflictuelles.'>Ne pas afficher les saisies englobées</label>\n";
				// Pour quand même afficher le bouton validant les checkbox ci-dessus:
				if (!method_exists($eleve_col, 'haveToPaginate')) {
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
			<input type="hidden" id="creation_notification" name="creation_notification" value="no"/>
			<input type="hidden" id="ajout_traitement" name="ajout_traitement" value="no"/>
			<input type="hidden" id="id_traitement" name="id_traitement" value=""/>
			<p>
			<div dojoType="dijit.form.DropDownButton" style="display: inline">
			    <span>Ajouter au traitement</span>
			    <div dojoType="dijit.Menu" style="display: inline">
				<button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_traitement').value = 'yes'; document.getElementById('ajout_traitement').value = 'no'; document.creer_traitement.submit();">
				    Créer un nouveau traitement
				</button>
				<button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_notification').value = 'yes'; document.getElementById('ajout_traitement').value = 'no'; document.creer_traitement.submit();">
				    Créer une nouvelle notification
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
				<button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_traitement').value = 'yes'; document.getElementById('ajout_traitement').value = 'no'; pop_it(document.creer_traitement);">
				    Créer un nouveau traitement
				</button>
				<button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_notification').value = 'yes'; document.getElementById('ajout_traitement').value = 'no'; pop_it(document.creer_traitement);">
				    Créer une nouvelle notification
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
				    <?php
						foreach(EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $edt_creneau){
							echo '		<td class="td_nom_creneau" style="text-align: center;"><a href="javascript:coche_checkbox_creneau('.$edt_creneau->getIdDefiniePeriode().')">'.$edt_creneau->getNomDefiniePeriode().'</a></td>';
						}
				    ?>
			    </tr>

    <?php
    $nb_checkbox = 0; //nombre de checkbox
    $compteur = 0;
    $tab_eleve_id=array();
    foreach($eleve_col as $eleve) {        
		$compteur = $compteur + 1;
		//$regime_eleve=$eleve->getEleveRegimeDoublant();
		//$regime_eleve=EleveRegimeDoublantQuery::create()->findPk($eleve->getlogin())->getRegime();
		if(EleveRegimeDoublantQuery::create()->findPk($eleve->getlogin())!=null) {
			$regime_eleve=EleveRegimeDoublantQuery::create()->findPk($eleve->getlogin())->getRegime();

			if($regime_eleve=="") {
				$regime_eleve="<span style='color:red; text-decoration:blink;' title=\"Le régime de cet élève enregistré dans la base pour cet élève est vide.
Vous devriez demander à l'administrateur de revalider la fiche de l'élève dans
   Gestion des bases/Gestion des élèves/...\">???</span>";
			}
		}
		else {
			$regime_eleve="<span style='color:red; text-decoration:blink;' title=\"Le régime de cet élève n'est pas enregistré dans la base.
Demandez à l'administrateur de revalider la fiche de l'élève dans
   Gestion des bases/Gestion des élèves/
      Les élèves (affectés dans des classes) dont le régime
      n'est pas renseigné \">???</span>";
		}

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
			    echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."&amp;onglet=responsables&amp;quitter_la_page=y' target='_blank'>";
			    echo ' (voir&nbsp;fiche)';
			    echo "</a>";
			}
			echo "<br />\n";
			echo "<div class='div_tab_tel_resp'>\n";
				echo "<div style='float:left; width:2em;'>\n";
				echo "<img src='../images/imabulle/tel3.jpg' width='20' height='15' />";
				echo "</div>\n";

				echo "<div class='tableau_tel_resp'>\n";
				echo tableau_tel_resp_ele($eleve->getLogin());
				echo "</div>\n";
			echo "</div>\n";

			echo("</td>");

			$col_creneaux = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();

			$nb_checkbox_eleve_courant=0;
			$id_checkbox_eleve_courant='';
			for($i = 0; $i<$col_creneaux->count(); $i++){
					$edt_creneau = $col_creneaux[$i];
					$absences_du_creneau = $eleve->getAbsenceEleveSaisiesDuCreneau($edt_creneau, $dt_date_absence_eleve);
					$violet = false;
                    $style = '';
					foreach ($absences_du_creneau as $absence) {                      
					    $traitement_col->addCollection($absence->getAbsenceEleveTraitements());
					    if (getSettingValue("abs2_alleger_abs_du_jour")!='y' && $absence->isSaisiesContradictoiresManquementObligation()) {
					    //if (!($absence->getSaisiesContradictoiresManquementObligation()->isEmpty())) {
						$violet = true;
						break;
					    }else{
                         $style = 'style="background-color :'.$absence->getColor().'"';   
                        }					    
					}
					if ($violet) {
					    $style = 'style="background-color : purple"';
					} 
					echo '<td '.$style.'>';
                    
					//si il y a des absences de l'utilisateurs on va proposer de les modifier
					$nb_checkbox_eleve_courant_sur_ce_creneau=0;
					foreach ($absences_du_creneau as $saisie) {
					    if (in_array($saisie->getPrimaryKey(), $saisie_affiches)) {
							//on affiche les saisies une seule fois
							continue;
					    }
					    $saisie_affiches[] = $saisie->getPrimaryKey();
					    $nb_checkbox = $nb_checkbox + 1;
					    $chaine_contenu_td='<nobr><input eleve_id="'.$eleve->getPrimaryKey().'" name="select_saisie[]" value="'.$saisie->getPrimaryKey().'" type="checkbox" ';
					    if ($saisie->getNotificationEnCours()){$chaine_contenu_td.='saisie_notification_en_cours="true"';}
                        if ($saisie->getNotifiee()) {$chaine_contenu_td.='saisie_notifiee="true"';}
					    if ($saisie->getTraitee()) {$chaine_contenu_td.='saisie_traitee="true"';}

						$eleve_id_courant=$eleve->getPrimaryKey();
						$id_checkbox_eleve_courant=$eleve_id_courant."_".$edt_creneau->getIdDefiniePeriode()."_".$nb_checkbox_eleve_courant_sur_ce_creneau;

						$chaine_contenu_td.=" id='".$id_checkbox_eleve_courant."' ";
						if(!in_array($eleve_id_courant, $tab_eleve_id)) {$tab_eleve_id[]=$eleve_id_courant;}

					    $chaine_contenu_td.='/>';
                        $chaine_contenu_td.='<a style="font-size:88%;" href="#" onClick="javascript:showwindow(\'visu_saisie.php?id_saisie='.$saisie->getPrimaryKey().'&menu=false\',\'Modifier,traiter ou notifier une saisie\');return false"><img src="../images/icons/saisie.png" title="Voir la saisie n°'.$saisie->getPrimaryKey().'"/>';

						$nb_checkbox_eleve_courant++;
						$nb_checkbox_eleve_courant_sur_ce_creneau++;

                        //if ($saisie->getNotifiee()) {echo " (notifiée)";}
					    $chaine_contenu_td.='</nobr> ';                        
					    //echo $saisie->getTypesDescription();
					    $chaine_contenu_td.='</a>';                        
                        if($saisie->getNotificationEnCours()){
                            $chaine_contenu_td.='<img src="../images/icons/courrier_envoi.png" title="'.$saisie->getTypesNotificationsDescription().'" />';
                        }                        
                        if($saisie->getNotifiee()){
                            $chaine_contenu_td.='<img src="../images/icons/courrier_retour.png" title="'.$saisie->getTypesNotificationsDescription().'" />';
                        }
                        $chaine_contenu_td.='<br/>';
                        if(!$saisie->getTraitee()) {
                            //if(!isset($ne_pas_afficher_saisies_englobees)) {
                            if((!isset($ne_pas_afficher_saisies_englobees))||($violet)) {
                                echo $chaine_contenu_td;
                                if(isset($signaler_saisies_englobees)) {
									$saisies_englobante_col = $saisie->getAbsenceEleveSaisiesEnglobantes();
									if($saisies_englobante_col->isEmpty()) {
										echo '<img src="../images/icons/ico_attention.png" title="Saisie non traitée" />';
									}
									else {
										$texte_saisie_couverte='La saisie est englobée par : ';
										$cpt_saisie_couverte=0;
										foreach ($saisies_englobante_col as $saisies_englobante) {
											if($cpt_saisie_couverte==0) {
												$lien_saisie_couverte="<a href='visu_saisie.php?id_saisie=".$saisies_englobante->getPrimaryKey()."' style='color:".$saisies_englobante->getColor()."'> ";
											}
											$texte_saisie_couverte.=$saisies_englobante->getDateDescription();
											$texte_saisie_couverte.=' '.$saisies_englobante->getTypesTraitements();
											if (!$saisies_englobante_col->isLast()) {
												$texte_saisie_couverte.=' - ';
											}
											$cpt_saisie_couverte++;
										}

										echo $lien_saisie_couverte.'<img src="../images/icons/ico_toit2.png" title="'.$texte_saisie_couverte.'" /></a>';
									}
								}
								else {
									echo '<img src="../images/icons/ico_attention.png" title="Saisie non traitée" />';
								}
								echo '<br/>';
							}
							else {
								// Faut-il quand même vérifier s'il n'y a pas de conflit?
								// Fait (?) avec le test $violet plus haut.
							}
                        }else{
                            echo $chaine_contenu_td;

                            //echo '<img src="../images/icons/flag_green.png" title="'.$saisie->getTypesDescription().'" />';
                            $saisie_justifiee_ou_pas="";
                            echo "<span title=\"";
							$tab_traitements_deja_affiches=array();
							foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
								if(!in_array($traitement->getId(), $tab_traitements_deja_affiches)) {
									$description_traitement_courant=$traitement->getDescription();
									echo "Traitement ".$description_traitement_courant."\n";
									if(preg_match("/justification :/", $description_traitement_courant)) {$saisie_justifiee_ou_pas=" <img src='../images/vert.png' width='16' height='16' title='Saisie justifiée' />";}
								}
							}
                            echo "\">";
                            echo $saisie->getTypesDescription();
                            echo $saisie_justifiee_ou_pas;
                            echo "</span>";

							/*
							echo "<br />";
							$tab_traitements_deja_affiches=array();
							foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
								if(!in_array($traitement->getId(), $tab_traitements_deja_affiches)) {
									echo $traitement->getDescription().' : ';
								}
							}
							*/
                            echo '<br/>';
                        }
                        //echo '<br/>';
					    //echo '</nobr>';					    
					}                    
					echo '</td>';
			    }             
					       // Avec ou sans photo
			if ((getSettingValue("active_module_trombinoscopes")=='y')) {
			    $nom_photo = $eleve->getNomPhoto(1);
			    $photos = $nom_photo;
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
			    <div dojoType="dijit.Menu"  style="white-space: nowrap; display: inline">';              		
			foreach ($traitement_col as $traitement) {
			    echo '<button dojoType="dijit.MenuItem" onClick="';
			    if($nb_checkbox_eleve_courant==1) {
			    	echo "if(document.getElementById('$id_checkbox_eleve_courant')) {document.getElementById('$id_checkbox_eleve_courant').checked=true;}";
			    }
			    echo 'document.getElementById(\'id_traitement\').value = \''.$traitement->getId().'\'; document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; document.creer_traitement.submit();">';
			    echo ' Ajouter au traitement n° '.$traitement->getId().' ('.$traitement->getDescription().')';
			    echo '</button>';
			}
            echo'	<button dojoType="dijit.MenuItem" onClick="';
			    if($nb_checkbox_eleve_courant==1) {
			    	echo "if(document.getElementById('$id_checkbox_eleve_courant')) {document.getElementById('$id_checkbox_eleve_courant').checked=true;}";
			    }
			    echo 'document.getElementById(\'creation_traitement\').value = \'yes\'; document.getElementById(\'ajout_traitement\').value = \'no\'; document.creer_traitement.submit();">
				    Créer un nouveau traitement
				</button>';
                            echo '<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_notification\').value = \'yes\'; document.getElementById(\'ajout_traitement\').value = \'no\'; document.creer_traitement.submit();">
                                Créer une nouvelle notification
                            </button>';
			echo '</div></div><br/>';

			echo '<div dojoType="dijit.form.DropDownButton"  style="white-space: nowrap; display: inline">
			    <span>Ajouter (fenêtre)</span>
			    <div dojoType="dijit.Menu"  style="white-space: nowrap; display: inline">';				
			foreach ($traitement_col as $traitement) {
			    echo '<button dojoType="dijit.MenuItem" onClick="';
			    if($nb_checkbox_eleve_courant==1) {
			    	echo "if(document.getElementById('$id_checkbox_eleve_courant')) {document.getElementById('$id_checkbox_eleve_courant').checked=true;}";
			    }
			    echo 'document.getElementById(\'id_traitement\').value = \''.$traitement->getId().'\'; document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; postwindow(document.creer_traitement,\'Traiter et notifier des saisies\');">';
			    echo ' Ajouter au traitement n° '.$traitement->getId().' ('.$traitement->getDescription().')';
			    echo '</button>';
			}
            echo'<button dojoType="dijit.MenuItem" onClick="';
			    if($nb_checkbox_eleve_courant==1) {
			    	echo "if(document.getElementById('$id_checkbox_eleve_courant')) {document.getElementById('$id_checkbox_eleve_courant').checked=true;}";
			    }
			    echo 'document.getElementById(\'creation_traitement\').value = \'yes\'; document.getElementById(\'ajout_traitement\').value = \'no\'; postwindow(document.creer_traitement,\'Traiter et notifier des saisies\');">
				    Créer un nouveau traitement (fenêtre)
				 </button>';
                            echo '<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_notification\').value = \'yes\'; document.getElementById(\'ajout_traitement\').value = \'no\'; postwindow(document.creer_traitement,\'Traiter et notifier des saisies\');">
                                Créer une nouvelle notification (fenêtre)
                            </button>';
			echo '</div></div><br/>';

            echo '<div dojoType="dijit.form.DropDownButton"  style="white-space: nowrap; display: inline">
			    <span>Ajouter (popup)</span>
			    <div dojoType="dijit.Menu"  style="white-space: nowrap; display: inline">';				
			foreach ($traitement_col as $traitement) {
			    echo '<button dojoType="dijit.MenuItem" onClick="';
			    if($nb_checkbox_eleve_courant==1) {
			    	echo "if(document.getElementById('$id_checkbox_eleve_courant')) {document.getElementById('$id_checkbox_eleve_courant').checked=true;}";
			    }
			    echo 'document.getElementById(\'id_traitement\').value = \''.$traitement->getId().'\'; document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; pop_it(document.creer_traitement);">';
			    echo ' Ajouter au traitement n° '.$traitement->getId().' ('.$traitement->getDescription().')';
			    echo '</button>';
			}
            echo'<button dojoType="dijit.MenuItem" onClick="';
			    if($nb_checkbox_eleve_courant==1) {
			    	echo "if(document.getElementById('$id_checkbox_eleve_courant')) {document.getElementById('$id_checkbox_eleve_courant').checked=true;}";
			    }
			    echo 'document.getElementById(\'creation_traitement\').value = \'yes\'; document.getElementById(\'ajout_traitement\').value = \'no\'; pop_it(document.creer_traitement);">
				    Créer un nouveau traitement (popup)
				 </button>';
                            echo '<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_notification\').value = \'yes\'; document.getElementById(\'ajout_traitement\').value = \'no\'; pop_it(document.creer_traitement);">
                                Créer une nouvelle notification (popup)
                            </button>';
			echo '</div></div>';
			echo '</td>';
			echo "</tr>";
    }

    echo " </tbody>";
    echo "</table>";   
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

if(isset($tab_eleve_id)) {
	$chaine_js_eleve_id="";
	for($loop=0;$loop<count($tab_eleve_id);$loop++) {
		if($loop>0) {$chaine_js_eleve_id.=", ";}
		$chaine_js_eleve_id.="'".$tab_eleve_id[$loop]."'";
	}

	echo "<script type='text/javascript'>
function coche_checkbox_creneau(num) {
	var tab_eleve_id=new Array($chaine_js_eleve_id);
	for(i=0;i<tab_eleve_id.length;i++) {
		// suffixe '_0' on ne coche que la première saisie du créneau
		// (tant pis s'il y en a plusieurs)
		if(document.getElementById(tab_eleve_id[i]+'_'+num+'_0')) {
			if(document.getElementById(tab_eleve_id[i]+'_'+num+'_0').checked!=true) {
				document.getElementById(tab_eleve_id[i]+'_'+num+'_0').checked=true;
			}
			else {
				document.getElementById(tab_eleve_id[i]+'_'+num+'_0').checked=false;
			}
		}
	}
}
</script>\n";
}

$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dijit.form.Button");
    dojo.require("dijit.Menu");
    dojo.require("dijit.form.Form");
    dojo.require("dijit.form.CheckBox");
    dojo.require("dijit.form.TextBox");
    dojo.require("dijit.form.Select");   
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
		label: "sans notification créée",
		onClick: function() {
		    var eleve_id = dojo.attr(node,\'eleve_id\');
		    var query_string = \'input[type=checkbox][eleve_id=\'+eleve_id+\']\';
		    dojo.query(query_string).attr(\'checked\', true);
		    query_string = \'input[type=checkbox][eleve_id=\'+eleve_id+\'][saisie_notification_en_cours=true]\';
		    dojo.query(query_string).attr(\'checked\', false);
		}
	    });
	    menu.addChild(menuItem4);

	    var menuItem5 = new dijit.MenuItem({
		label: "non notifiés",
		onClick: function() {
		    var eleve_id = dojo.attr(node,\'eleve_id\');
		    var query_string = \'input[type=checkbox][eleve_id=\'+eleve_id+\']\';
		    dojo.query(query_string).attr(\'checked\', true);
		    query_string = \'input[type=checkbox][eleve_id=\'+eleve_id+\'][saisie_notifiee=true]\';
		    dojo.query(query_string).attr(\'checked\', false);
		}
	    });
	    menu.addChild(menuItem5);

	    var button = new dijit.form.DropDownButton({
		label: "Selectionner",
		dropDown: menu
	    });
	    node.appendChild(button.domNode);
	});	
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
