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

// 20130814
if ((isset($_POST["creation_lot_traitements"]))&&($_POST["creation_lot_traitements"]=="yes")) {
    // On ne fait pas un header("Location:...");
    // Avec include, les variables POSTées sont récupérées dans la page incluse... mais on reste en apparence sur absences_du_jour.php
    include('traitements_par_lots.php');
    die();
}

/*
if ((isset($_POST["chercher_traitements_a_rattacher"]))&&($_POST["chercher_traitements_a_rattacher"]=="yes")) {
    // On reçoit:
    // id_traitement=XXXXX
    // filter_recherche_saisie_a_rattacher=oui
    // rattachement_preselection=y
    include('liste_saisies_selection_traitement.php');
    die();
}
*/

if (isset($_POST["creation_traitement"]) || isset($_POST["ajout_traitement"])) {
    include('creation_traitement.php');
}

if (isset($_POST["creation_notification"])) {
    include('creation_notification.php');
}

$photo_redim_taille_max_largeur=45;
$photo_redim_taille_max_hauteur=45;

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

if(isset($_POST['suppr_saisies_cochees'])) {
	$msg="";

	// $_POST[select_saisie]['0']=	40004
	$select_saisie=isset($_POST['select_saisie']) ? $_POST['select_saisie'] : array();
	if(count($select_saisie)==0) {
		$msg.="Aucune saisie n'a été cochée.<br />";
	}
	else {
		foreach($select_saisie as $key => $value) {

			$saisie = AbsenceEleveSaisieQuery::create()->includeDeleted()->findPk($value);
			if ($saisie == null) {
				//$message_enregistrement .= 'Modification impossible : saisie non trouvée.';
				$msg .= 'Modification impossible : saisie n°'.$value.' non trouvée.';
			}
			else {
				if ($utilisateur->getStatut() == 'cpe' || $utilisateur->getStatut() == 'scolarite'
					|| ($utilisateur->getStatut() == 'professeur' && $saisie->getUtilisateurId() == $utilisateur->getPrimaryKey()) ) {
					//ok
					$msg .= 'Suppression de la saisie n°'.$value.'.<br />';
					$saisie->delete();
				} else {
					//$message_enregistrement .= 'Modification non autorisée.';
					$msg .= 'Modification de la saisie n°'.$value.' non autorisée.<br />';
				}
			}
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

//debug_var();

include('menu_abs2.inc.php');
include('menu_bilans.inc.php');
//===========================

echo "<div class='css-panes' id='containDiv'>\n";

//==========================================
// Vérification:
$begin_bookings=getSettingValue('begin_bookings');
$mysql_begin_bookings=strftime("%Y-%m-%d %H:%M:%S", $begin_bookings);
$slash_date_begin_bookings=strftime("%d/%m/%Y", $begin_bookings);
$end_bookings=getSettingValue('end_bookings');
$mysql_end_bookings=strftime("%Y-%m-%d %H:%M:%S", $end_bookings);
$slash_date_end_bookings=strftime("%d/%m/%Y", $end_bookings);
$sql="SELECT e.nom,e.prenom,a.id,a.debut_abs,a.fin_abs,datediff(fin_abs,debut_abs) as duree FROM a_saisies a,eleves e WHERE (debut_abs<'".$mysql_begin_bookings."' OR fin_abs<'".$mysql_begin_bookings."' OR debut_abs>'".$mysql_end_bookings."' OR fin_abs>'".$mysql_end_bookings."' OR fin_abs<debut_abs) AND a.eleve_id=e.id_eleve AND deleted_at IS NULL ORDER BY e.nom, e.prenom;";
$res_anomalie=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_anomalie)>0) {
	echo "<p class='fieldset_opacite50' style='text-indent:-6em;margin-left:6em;'><strong style='color:red'>Anomalie&nbsp;:</strong> Une ou des saisies sont hors année scolaire (<em>du ".$slash_date_begin_bookings." au ".$slash_date_end_bookings."</em>) ou avec des dates de début/fin anormales.<br />Vous devriez corriger pour ne pas conserver des données érronées et éviter des totaux d'absences incorrects.<br />En voici la liste<br />";
	while($lig=mysqli_fetch_object($res_anomalie)) {
		echo '<a href="visu_saisie.php?id_saisie='.$lig->id.'" onClick="showwindow(\'visu_saisie.php?id_saisie='.$lig->id.'&menu=false\',\'Modifier,traiter ou notifier une saisie\');return false">Saisie n°'.$lig->id.'&nbsp;: '.$lig->nom.' '.$lig->prenom.' du  '.formate_date($lig->debut_abs, "y", "court").' au '.formate_date($lig->fin_abs, "y", "court").'</a><br />';
	}
	echo "</p>";
}
//==========================================

//==========================================
// 20131107 : Contournement bug dojo
$ne_pas_afficher_form_choix_grp_classe=isset($_POST['ne_pas_afficher_form_choix_grp_classe']) ? $_POST['ne_pas_afficher_form_choix_grp_classe'] : NULL;
if((!isset($ne_pas_afficher_form_choix_grp_classe))||($ne_pas_afficher_form_choix_grp_classe!="y")) {
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
}
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
				echo "<input type='checkbox' id='signaler_saisies_englobees' name='signaler_saisies_englobees' value='y'$checked_ou_pas /><label for='signaler_saisies_englobees' title=\"Afficher un témoin comme quoi la saisie est englobée/couverte par une autre.\">Signaler les saisies englobées <img src='../images/icons/ico_toit2.png' width='16' height='16' alt='Témoin que la saisie est englobée' /></label>\n";

				echo " - ";
				$ne_pas_afficher_saisies_englobees=isset($_POST['ne_pas_afficher_saisies_englobees']) ? $_POST['ne_pas_afficher_saisies_englobees'] : NULL;
				if((!isset($_POST['date_absence_eleve']))&&(getSettingAOui('abs2_ne_pas_afficher_saisies_englobees'))) {
					$ne_pas_afficher_saisies_englobees="y";
				}
				$checked_ou_pas="";
				if($ne_pas_afficher_saisies_englobees=="y") {$checked_ou_pas=" checked";}
				echo "<input type='checkbox' id='ne_pas_afficher_saisies_englobees' name='ne_pas_afficher_saisies_englobees' value='y'$checked_ou_pas /><label for='ne_pas_afficher_saisies_englobees' title='Ne pas afficher les saisies englobées... sous réserve que les saisies ne soient pas conflictuelles.'>Ne pas afficher les saisies englobées</label>\n";

				echo " - ";
				$ne_pas_afficher_lignes_avec_traitement_englobant=isset($_POST['ne_pas_afficher_lignes_avec_traitement_englobant']) ? $_POST['ne_pas_afficher_lignes_avec_traitement_englobant'] : NULL;
				if((!isset($_POST['date_absence_eleve']))&&(getSettingAOui('abs2_ne_pas_afficher_lignes_avec_traitement_englobant'))) {
					$ne_pas_afficher_lignes_avec_traitement_englobant="y";
				}
				$checked_ou_pas="";
				if($ne_pas_afficher_lignes_avec_traitement_englobant=="y") {$checked_ou_pas=" checked";}
				echo "<input type='checkbox' id='ne_pas_afficher_lignes_avec_traitement_englobant' name='ne_pas_afficher_lignes_avec_traitement_englobant' value='y'$checked_ou_pas /><label for='ne_pas_afficher_lignes_avec_traitement_englobant' title='Ne pas afficher les lignes pour lesquelles les saisies sont traitées ou englobées...
de façon à ne laisser que ce qui reste à traiter.'>Ne pas afficher les lignes traitées ou englobées</label>\n";

				//==========================================
				// 20131107 : Contournement bug dojo
				echo " - ";
				$checked_ou_pas="";
				if($ne_pas_afficher_form_choix_grp_classe=="y") {$checked_ou_pas=" checked";}
				echo "<input type='checkbox' id='ne_pas_afficher_form_choix_grp_classe' name='ne_pas_afficher_form_choix_grp_classe' value='y'$checked_ou_pas /><label for='ne_pas_afficher_form_choix_grp_classe' title=\"Dans le cas où vous subissez un bug d'affichage, avec par exemple
la date qui ne s'affiche pas au format jj/mm/aaaa, mais aaaa-mm-jj
et des boutons de sélection qui n'ont pas le format habituel,
vous pouvez ne pas afficher les formulaires de choix de la classe, du groupe ou du régime en cochant cette case.
Cela devrait permettre de contourner le problème.\">
<img src='../images/icons/flag2.gif' width='17' height='18' alt='Bug dojo' id='img_bug_dojo' />
<img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Bug dojo' style='display:none' id='img_bug_dojo2' />
</label>\n";
				//==========================================

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
			<input type="hidden" id="creation_lot_traitements" name="creation_lot_traitements" value="no"/>

			<!--input type="hidden" id="chercher_traitements_a_rattacher" name="chercher_traitements_a_rattacher" value="no"/>
			<input type="hidden" id="filter_recherche_saisie_a_rattacher" name="filter_recherche_saisie_a_rattacher" value=""/>
			<input type="hidden" id="rattachement_preselection" name="rattachement_preselection" value=""/-->

			<p>
			<div dojoType="dijit.form.DropDownButton" style="display: inline">
				<span>Ajouter au traitement</span>
				<?php
					// 20130814
					// Ajouter un type de "traitement" redirigeant vers une nouvelle page sans créer immédiatement de id_traitement... pour les traitements par lots (cas des grèves,...): Même traitements/notifications pour tous
				?>
				<div dojoType="dijit.Menu" style="display: inline">
					<button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_traitement').value = 'yes'; document.getElementById('ajout_traitement').value = 'no'; document.getElementById('creation_lot_traitements').value = 'no'; document.creer_traitement.submit();">
						Créer un nouveau traitement
					</button>
					<button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_notification').value = 'yes'; document.getElementById('ajout_traitement').value = 'no'; document.getElementById('creation_lot_traitements').value = 'no'; document.creer_traitement.submit();">
						Créer une nouvelle notification
					</button>

<?php
	$id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
	if ($id_traitement != null && AbsenceEleveTraitementQuery::create()->findPk($id_traitement) != null) {
	$traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
	echo '
					<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'creation_lot_traitements\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; document.getElementById(\'id_traitement\').value = \''.$id_traitement.'\'; document.creer_traitement.submit();">
						Ajouter les saisies au traitement n° '.$id_traitement.' ('.$traitement->getDescription().')
					</button>'."\n";
	}
?>

					<button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_notification').value = 'no'; document.getElementById('ajout_traitement').value = 'no'; document.getElementById('creation_lot_traitements').value = 'yes'; document.creer_traitement.submit();">
						Créer un lot de traitements
					</button>

				</div>
			</div>

			<div dojoType="dijit.form.DropDownButton" style="display: inline">
				<span>Ajouter au traitement (popup)</span>
				<div dojoType="dijit.Menu" style="display: inline">
					<button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_traitement').value = 'yes'; document.getElementById('creation_lot_traitements').value = 'no'; document.getElementById('ajout_traitement').value = 'no'; pop_it(document.creer_traitement);">
						Créer un nouveau traitement
					</button>
					<button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_notification').value = 'yes'; document.getElementById('ajout_traitement').value = 'no'; document.getElementById('creation_lot_traitements').value = 'no'; pop_it(document.creer_traitement);">
						Créer une nouvelle notification
					</button>
<?php
	$id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
	if ($id_traitement != null && AbsenceEleveTraitementQuery::create()->findPk($id_traitement) != null) {
		$traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
		echo '
					<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; document.getElementById(\'creation_lot_traitements\').value = \'no\'; document.getElementById(\'id_traitement\').value = \''.$id_traitement.'\'; pop_it(document.creer_traitement);">
						Ajouter les saisies au traitement n° '.$id_traitement.' ('.$traitement->getDescription().') dans une popup
					</button>'."\n";
	}
?>
				</div>
			</div>

			<div style="display: inline">
				<!--input type='submit' name='suppr_saisies_cochees' value='Supprimer les saisies' title="Supprimer les saisies cochées" dojoType="dijit.form.Button" /-->
				<button type='submit' name='suppr_saisies_cochees' value='Supprimer les saisies' title="Supprimer les saisies cochées" dojoType="dijit.form.Button" />Supprimer les saisies</button>
				<!-- Avec ce champ, on obtient un retour à la bonne date, mais avec l'impression que ce ne sont pas les bonnes qui ont été supprimées.
				Par exemple, si on coche les saisies heure par heure englobées dans une saisie 8h-16h30, après clic sur suppression,
				on revient à la page mais avec l'impression que toutes les saisies à supprimer sont toujours là et seule la saisie à conserver a disparu.
				Pourtant, il n'en est rien.
				En changeant de date et en revenant, les choses sont correctes, seule la saisie 8h-16h30 reste et les autres sont supprimées.
				Bizarre!?
				<input type='hidden' name='date_absence_eleve' value='<?php echo $date_absence_eleve;?>'/>
				-->
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
		echo '
						<td class="td_nom_creneau" style="text-align: center;"><a href="javascript:coche_checkbox_creneau('.$edt_creneau->getIdDefiniePeriode().')">'.$edt_creneau->getNomDefiniePeriode().'</a></td>';
	}
?>
					</tr>

<?php

	$nb_checkbox = 0; //nombre de checkbox
	$compteur = 0;
	$tab_eleve_id=array();
	foreach($eleve_col as $eleve) {
		//$tab_traitements_deja_affiches=array();

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

			// Témoin de ligne avec Traitement créé et les autres saisies englobées ou alors toutes les saisies sont traitées
			//$ligne_avec_traitement_englobant_autres_saisies="n";
			$nb_saisies_non_englobees=0;

			$saisie_affiches = array ();
			/*
			if ($compteur % 2 == '1') {
				$background_couleur="#E8F1F4";
			} else {
				$background_couleur="#C6DCE3";
			}
			echo "<tr style='background-color :$background_couleur'>\n";
			*/
			$ligne_courante="<tr class='lignes_couleurs_alternees'>\n";

			$Yesterday = date("Y-m-d",mktime(0,0,0,$dt_date_absence_eleve->format("m") ,$dt_date_absence_eleve->format("d")-1,$dt_date_absence_eleve->format("Y")));
			$compter_hier = $eleve->getAbsenceEleveSaisiesDuJour($Yesterday)->count();
			$color_hier = ($compter_hier >= 1) ? ' style="background-color: red; text-align: center; color: white; font-weight: bold;"' : '';
			$aff_compter_hier = ($compter_hier >= 1) ? $compter_hier.' enr.' : '';
//20151025
			$ligne_courante.="<td".$color_hier.">";
			$ligne_courante.='<a style="font-size:88%;color:white;" href="#" onClick="javascript:document.getElementById(\'absences_du_jour\').action=\'absences_du_jour.php#ancre_id_eleve_'.$eleve->getId().'\';showwindow(\'visu_saisies_ele_jour.php?date_jour='.$Yesterday.'&id_eleve='.$eleve->getId().'&menu=false\',\'Voir les saisies du '.formate_date($Yesterday).' pour '.preg_replace("/'/"," ",$eleve->getNom()).' '.preg_replace("/'/"," ",$eleve->getPrenom()).'\');return false" title="Voir les saisies du '.formate_date($Yesterday).' pour '.preg_replace("/'/"," ",$eleve->getNom()).' '.preg_replace("/'/"," ",$eleve->getPrenom()).'">';
			$ligne_courante.=$aff_compter_hier;
			$ligne_courante.="</a>\n";
			$ligne_courante.="</td>\n";
			$ligne_courante.="<td>";

			$ligne_courante.="<a name='ancre_login_eleve_".$eleve->getLogin()."'></a>";
			$ligne_courante.="<a name='ancre_id_eleve_".$eleve->getId()."'></a>";

			$ligne_courante.=strtoupper($eleve->getNom()).' '.ucfirst($eleve->getPrenom()).' ('.$eleve->getCivilite().') ('.$regime_eleve.')';
			$ligne_courante.=' ';
			$ligne_courante.=$eleve->getClasseNom($dt_date_absence_eleve);
			if ($utilisateur->getAccesFicheEleve($eleve)) {
				//$ligne_courante.="<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."' target='_blank'>";
				$ligne_courante.="<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."&amp;onglet=responsables&amp;quitter_la_page=y' target='_blank'>";
				$ligne_courante.=' (voir&nbsp;fiche)';
				$ligne_courante.="</a>";
			}
			$ligne_courante.="<br />\n";
			$ligne_courante.="<div class='div_tab_tel_resp'>\n";
				$ligne_courante.="<div style='float:left; width:2em;'>\n";
				$ligne_courante.="<img src='../images/imabulle/tel3.jpg' width='20' height='15' />";
				$ligne_courante.="</div>\n";

				$ligne_courante.="<div class='tableau_tel_resp'>\n";
				$ligne_courante.=tableau_tel_resp_ele($eleve->getLogin());
				$ligne_courante.="</div>\n";
			$ligne_courante.="</div>\n";

			$ligne_courante.="</td>";

			$col_creneaux = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();

			$nb_checkbox_eleve_courant=0;
			$id_checkbox_eleve_courant='';
			for($i = 0; $i<$col_creneaux->count(); $i++) {
				$edt_creneau = $col_creneaux[$i];
				$absences_du_creneau = $eleve->getAbsenceEleveSaisiesDuCreneau($edt_creneau, $dt_date_absence_eleve);
				$violet = false;
				$style = '';
				$chaine_conflits="";
				foreach ($absences_du_creneau as $absence) {
					$traitement_col->addCollection($absence->getAbsenceEleveTraitements());
					// A FAIRE : Eplucher isSaisiesContradictoiresManquementObligation parce qu'il semble y avoir des faux positifs
					if (getSettingValue("abs2_alleger_abs_du_jour")!='y' && $absence->isSaisiesContradictoiresManquementObligation()) {
						//if (!($absence->getSaisiesContradictoiresManquementObligation()->isEmpty())) {
						$violet = true;
/*
if($eleve->getLogin()=='coiffeyl') {
$tmp_obj=$absence->getSaisiesContradictoiresManquementObligation();
echo "<span style='color:red'>Créneau $i</span><br /><pre>";
print_r($tmp_obj);
echo "</pre>";
}
*/
						break;
					} else {
						$style = 'style="background-color :'.$absence->getColor().'"';
					}
				}
				if ($violet) {
					$style = 'style="background-color : purple"';
				} 
				$ligne_courante.='<td '.$style.' title="Créneau '.$edt_creneau->getNomDefiniePeriode()."\n".'('.preg_replace("/:[0-9]*$/","",$edt_creneau->getHeuredebutDefiniePeriode()).' - '.preg_replace("/:[0-9]*$/","",$edt_creneau->getHeurefinDefiniePeriode()).')">';

				/*
				if($violet) {
					echo "<div style='float:left; width:30em; height:30em; font-size:x-small; overflow:auto;'>";
					echo "<p>".strtoupper($eleve->getNom()).' '.ucfirst($eleve->getPrenom())." en ".$edt_creneau->getNomDefiniePeriode()." (".$absence->getId().")</p>";
					echo "<pre>";
					print_r($absence->getSaisiesContradictoiresManquementObligation(false));
					echo "</pre>";
					echo "</div>";
				}
				*/

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
/*
echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getCreatedAt('U')));
echo ' par '.  $saisie->getUtilisateurProfessionnel()->getCivilite().' '.$saisie->getUtilisateurProfessionnel()->getNom().' '.mb_substr($saisie->getUtilisateurProfessionnel()->getPrenom(), 0, 1).'.';

if ($saisie->getCreatedAt('U') != $saisie->getVersionCreatedAt('U')) {
    echo 'Modifiée le : ';
    echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getVersionCreatedAt('U')));
    $modifie_par_utilisateur = UtilisateurProfessionnelQuery::create()->filterByLogin($saisie->getVersionCreatedBy())->findOne();
    if ($modifie_par_utilisateur != null) {
		echo ' par '.  $modifie_par_utilisateur->getCivilite().' '.$modifie_par_utilisateur->getNom().' '.mb_substr($modifie_par_utilisateur->getPrenom(), 0, 1).'.';
    }
}
*/
					$chaine_contenu_td.='<a style="font-size:88%;" href="#" onClick="javascript:document.getElementById(\'absences_du_jour\').action=\'absences_du_jour.php#ancre_id_eleve_'.$eleve->getId().'\';showwindow(\'visu_saisie.php?id_saisie='.$saisie->getPrimaryKey().'&menu=false\',\'Modifier,traiter ou notifier une saisie\');return false"><img src="../images/icons/saisie.png" title="Voir la saisie n°'.$saisie->getPrimaryKey().'
Du '.get_date_heure_from_mysql_date($saisie->getDebutAbs()).' au '.get_date_heure_from_mysql_date($saisie->getFinAbs()).'"/>';

					$nb_checkbox_eleve_courant++;
					$nb_checkbox_eleve_courant_sur_ce_creneau++;

					//if ($saisie->getNotifiee()) {$ligne_courante.=" (notifiée)";}
					$chaine_contenu_td.='</nobr> ';
					//$ligne_courante.=$saisie->getTypesDescription();
					$chaine_contenu_td.='</a>';
					if($saisie->getNotificationEnCours()) {
						$chaine_contenu_td.='<img src="../images/icons/courrier_envoi.png" title="'.$saisie->getTypesNotificationsDescription().'" />';
					}
					if($saisie->getNotifiee()) {
						$chaine_contenu_td.='<img src="../images/icons/courrier_retour.png" title="'.$saisie->getTypesNotificationsDescription().'" />';
					}
					$chaine_contenu_td.='<br/>';
					if(!$saisie->getTraitee()) {
						// Saisie non traitée

						//Debug
						//$chaine_contenu_td.='non traitée<br/>';

						//if(!isset($ne_pas_afficher_saisies_englobees)) {
						if((!isset($ne_pas_afficher_saisies_englobees))||($violet)) {
							$ligne_courante.=$chaine_contenu_td;
							if((isset($signaler_saisies_englobees))||(isset($ne_pas_afficher_lignes_avec_traitement_englobant))) {
								$saisies_englobante_col = $saisie->getAbsenceEleveSaisiesEnglobantes();
								if($saisies_englobante_col->isEmpty()) {
									$ligne_courante.='<img src="../images/icons/ico_attention.png" title="Saisie non traitée" />';
									$nb_saisies_non_englobees++;
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

									$ligne_courante.=$lien_saisie_couverte.'<img src="../images/icons/ico_toit2.png" title="'.$texte_saisie_couverte.'" /></a>';
								}
							}
							else {
								$ligne_courante.='<img src="../images/icons/ico_attention.png" title="Saisie non traitée" />';
								$nb_saisies_non_englobees++;
							}
							$ligne_courante.='<br/>';
						}
						else {
							// Faut-il quand même vérifier s'il n'y a pas de conflit?
							// Fait (?) avec le test $violet plus haut.
						}
					} else {

						if((!isset($ne_pas_afficher_saisies_englobees))||($violet)) {

							if((isset($signaler_saisies_englobees))||(isset($ne_pas_afficher_lignes_avec_traitement_englobant))) {

								$saisies_englobante_col = $saisie->getAbsenceEleveSaisiesEnglobantes();
								if($saisies_englobante_col->isEmpty()) {

									$ligne_courante.=$chaine_contenu_td;
									$nb_saisies_non_englobees++;

									$saisie_justifiee_ou_pas="";
									$ligne_courante.="<span title=\"";
									$tab_traitements_deja_affiches=array();
									foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
										if(!in_array($traitement->getId(), $tab_traitements_deja_affiches)) {
											$description_traitement_courant=$traitement->getDescription();
											$ligne_courante.="Traitement ".$description_traitement_courant."\n";
											if(preg_match("/justification :/", $description_traitement_courant)) {$saisie_justifiee_ou_pas=" <img src='../images/vert.png' width='16' height='16' title='Saisie justifiée' />";}
											$tab_traitements_deja_affiches[]=$traitement->getId();
										}
									}
									$ligne_courante.="\">";
									$ligne_courante.=$saisie->getTypesDescription();
									$ligne_courante.=$saisie_justifiee_ou_pas;
									$ligne_courante.="</span>";

									// 20140429
									if($violet) {
										foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
											$saisies_englobante_col = $saisie->getAbsenceEleveSaisiesEnglobantes();
											if($saisies_englobante_col->isEmpty()) {
												$ligne_courante.="<a href=\"liste_saisies_selection_traitement.php?id_traitement=".$traitement->getId()."&amp;filter_recherche_saisie_a_rattacher=oui&amp;rattachement_preselection=y&amp;id_eleve=".$eleve->getId()."\" title=\"Chercher des saisies à rattacher au traitement n°".$traitement->getId()." pour régler le conflit.\"\"><img src='../images/icons/chercher.png' class='icone16' alt='Chercher' /></a>";
											}

										}
									}
									$ligne_courante.='<br/>';
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

									$ligne_courante.=$lien_saisie_couverte.'<img src="../images/icons/ico_toit2.png" title="'.$texte_saisie_couverte.'" /></a>';
								}

							}
							else {
								$ligne_courante.=$chaine_contenu_td;

								// Debug
								//$ligne_courante.="traitée";

								//$ligne_courante.='<img src="../images/icons/flag_green.png" title="'.$saisie->getTypesDescription().'" />';
								$saisie_justifiee_ou_pas="";
								$ligne_courante.="<span title=\"";
								$tab_traitements_deja_affiches=array();
								foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
									if(!in_array($traitement->getId(), $tab_traitements_deja_affiches)) {
										$description_traitement_courant=$traitement->getDescription();
										$ligne_courante.="Traitement ".$description_traitement_courant."\n";
										if(preg_match("/justification :/", $description_traitement_courant)) {$saisie_justifiee_ou_pas=" <img src='../images/vert.png' width='16' height='16' title='Saisie justifiée' />";}
										$tab_traitements_deja_affiches[]=$traitement->getId();
									}
								}
								$ligne_courante.="\">";
								$ligne_courante.=$saisie->getTypesDescription();
								$ligne_courante.=$saisie_justifiee_ou_pas;
								$ligne_courante.="</span>";

								if($violet) {
									foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
										$saisies_englobante_col = $saisie->getAbsenceEleveSaisiesEnglobantes();
										if($saisies_englobante_col->isEmpty()) {
											$ligne_courante.="<a href=\"liste_saisies_selection_traitement.php?id_traitement=".$traitement->getId()."&amp;filter_recherche_saisie_a_rattacher=oui&amp;rattachement_preselection=y&amp;id_eleve=".$eleve->getId()."\" title=\"Chercher des saisies à rattacher au traitement n°".$traitement->getId()." pour régler le conflit.\"\"><img src='../images/icons/chercher.png' class='icone16' alt='Chercher' /></a>";
										}
									}
								}
								$ligne_courante.='<br/>';
							}
						}
						else {




							$saisies_englobante_col = $saisie->getAbsenceEleveSaisiesEnglobantes();
							if($saisies_englobante_col->isEmpty()) {

								$ligne_courante.=$chaine_contenu_td;
								$nb_saisies_non_englobees++;

								$saisie_justifiee_ou_pas="";
								$ligne_courante.="<span title=\"";
								$tab_traitements_deja_affiches=array();
								foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
									if(!in_array($traitement->getId(), $tab_traitements_deja_affiches)) {
										$description_traitement_courant=$traitement->getDescription();
										$ligne_courante.="Traitement ".$description_traitement_courant."\n";
										if(preg_match("/justification :/", $description_traitement_courant)) {$saisie_justifiee_ou_pas=" <img src='../images/vert.png' width='16' height='16' title='Saisie justifiée' />";}
										$tab_traitements_deja_affiches[]=$traitement->getId();
									}
								}
								$ligne_courante.="\">";
								$ligne_courante.=$saisie->getTypesDescription();
								$ligne_courante.=$saisie_justifiee_ou_pas;
								$ligne_courante.="</span>";

								// 20140429
								if($violet) {
									foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
										$saisies_englobante_col = $saisie->getAbsenceEleveSaisiesEnglobantes();
										if($saisies_englobante_col->isEmpty()) {
											$ligne_courante.="<a href=\"liste_saisies_selection_traitement.php?id_traitement=".$traitement->getId()."&amp;filter_recherche_saisie_a_rattacher=oui&amp;rattachement_preselection=y&amp;id_eleve=".$eleve->getId()."\" title=\"Chercher des saisies à rattacher au traitement n°".$traitement->getId()." pour régler le conflit.\"\"><img src='../images/icons/chercher.png' class='icone16' alt='Chercher' /></a>";
										}

									}
								}
								$ligne_courante.='<br/>';
							}
							elseif(isset($signaler_saisies_englobees)) {
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

								$ligne_courante.=$lien_saisie_couverte.'<img src="../images/icons/ico_toit2.png" title="'.$texte_saisie_couverte.'" /></a>';
							}


						}








					}
					//$ligne_courante.='<br/>';
					//$ligne_courante.='</nobr>';
				}
				$ligne_courante.='</td>';
			}

			// Avec ou sans photo
			if ((getSettingValue("active_module_trombinoscopes")=='y')) {
				$nom_photo = $eleve->getNomPhoto(1);
				$photos = $nom_photo;
				if (($nom_photo == NULL) or (!(file_exists($photos)))) {
					$photos = "../mod_trombinoscopes/images/trombivide.jpg";
				}
				$valeur = redimensionne_image_petit($photos);

				$ligne_courante.='
				<td>
					<img src="'.$photos.'" style="width: '.$valeur[0].'px; height: '.$valeur[1].'px; border: 0px" alt="" title="" />
				</td>';
			}

			$ligne_courante.='<td style="width : 7em">';
			$ligne_courante.='<div add_select_shorcuts_button="true" eleve_id="'.$eleve->getPrimaryKey().'"></div>';
			$ligne_courante.='<div dojoType="dijit.form.DropDownButton"  style="white-space: nowrap; display: inline">
				<span>Ajouter au traitement</span>
				<div dojoType="dijit.Menu"  style="white-space: nowrap; display: inline">';
			foreach ($traitement_col as $traitement) {
				$ligne_courante.='<button dojoType="dijit.MenuItem" onClick="';
				if($nb_checkbox_eleve_courant==1) {
					$ligne_courante.="if(document.getElementById('$id_checkbox_eleve_courant')) {document.getElementById('$id_checkbox_eleve_courant').checked=true;}";
				}
				$ligne_courante.='document.getElementById(\'id_traitement\').value = \''.$traitement->getId().'\'; document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; document.getElementById(\'creation_lot_traitements\').value = \'no\'; document.creer_traitement.submit();">';
				$ligne_courante.=' Ajouter au traitement n° '.$traitement->getId().' ('.$traitement->getDescription().')';
				$ligne_courante.='</button>';
			}
			$ligne_courante.='	<button dojoType="dijit.MenuItem" onClick="';
			if($nb_checkbox_eleve_courant==1) {
				$ligne_courante.="if(document.getElementById('$id_checkbox_eleve_courant')) {document.getElementById('$id_checkbox_eleve_courant').checked=true;}";
			}
			$ligne_courante.='document.getElementById(\'creation_traitement\').value = \'yes\'; document.getElementById(\'ajout_traitement\').value = \'no\'; document.getElementById(\'creation_lot_traitements\').value = \'no\'; document.creer_traitement.submit();">
				Créer un nouveau traitement
			</button>';
			$ligne_courante.='<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_notification\').value = \'yes\'; document.getElementById(\'ajout_traitement\').value = \'no\'; document.getElementById(\'creation_lot_traitements\').value = \'no\'; document.creer_traitement.submit();">
				Créer une nouvelle notification
			</button>';
			$ligne_courante.='</div></div><br/>';

			$ligne_courante.='<div dojoType="dijit.form.DropDownButton"  style="white-space: nowrap; display: inline">
				<span>Ajouter (fenêtre)</span>
				<div dojoType="dijit.Menu"  style="white-space: nowrap; display: inline">';
			foreach ($traitement_col as $traitement) {
				$ligne_courante.='<button dojoType="dijit.MenuItem" onClick="';
				if($nb_checkbox_eleve_courant==1) {
					$ligne_courante.="if(document.getElementById('$id_checkbox_eleve_courant')) {document.getElementById('$id_checkbox_eleve_courant').checked=true;}";
				}
				$ligne_courante.='document.getElementById(\'id_traitement\').value = \''.$traitement->getId().'\'; document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; document.getElementById(\'creation_lot_traitements\').value = \'no\'; postwindow(document.creer_traitement,\'Traiter et notifier des saisies\');">';
				$ligne_courante.=' Ajouter au traitement n° '.$traitement->getId().' ('.$traitement->getDescription().')';
				$ligne_courante.='</button>';
			}

			$ligne_courante.='<button dojoType="dijit.MenuItem" onClick="';
			if($nb_checkbox_eleve_courant==1) {
				$ligne_courante.="if(document.getElementById('$id_checkbox_eleve_courant')) {document.getElementById('$id_checkbox_eleve_courant').checked=true;}";
			}
			$ligne_courante.='document.getElementById(\'creation_traitement\').value = \'yes\'; document.getElementById(\'ajout_traitement\').value = \'no\'; document.getElementById(\'creation_lot_traitements\').value = \'no\'; postwindow(document.creer_traitement,\'Traiter et notifier des saisies\');">
				Créer un nouveau traitement (fenêtre)
			</button>';
			$ligne_courante.='<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_notification\').value = \'yes\'; document.getElementById(\'ajout_traitement\').value = \'no\'; document.getElementById(\'creation_lot_traitements\').value = \'no\'; postwindow(document.creer_traitement,\'Traiter et notifier des saisies\');">
				Créer une nouvelle notification (fenêtre)
			</button>';
			$ligne_courante.='</div></div><br/>';

			$ligne_courante.='<div dojoType="dijit.form.DropDownButton"  style="white-space: nowrap; display: inline">
				<span>Ajouter (popup)</span>
				<div dojoType="dijit.Menu"  style="white-space: nowrap; display: inline">';				
			foreach ($traitement_col as $traitement) {
				$ligne_courante.='<button dojoType="dijit.MenuItem" onClick="';
				if($nb_checkbox_eleve_courant==1) {
					$ligne_courante.="if(document.getElementById('$id_checkbox_eleve_courant')) {document.getElementById('$id_checkbox_eleve_courant').checked=true;}";
				}
				$ligne_courante.='document.getElementById(\'id_traitement\').value = \''.$traitement->getId().'\'; document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; document.getElementById(\'creation_lot_traitements\').value = \'no\'; pop_it(document.creer_traitement);">';
				$ligne_courante.=' Ajouter au traitement n° '.$traitement->getId().' ('.$traitement->getDescription().')';
				$ligne_courante.='</button>';
			}

			$ligne_courante.='<button dojoType="dijit.MenuItem" onClick="';
			if($nb_checkbox_eleve_courant==1) {
				$ligne_courante.="if(document.getElementById('$id_checkbox_eleve_courant')) {document.getElementById('$id_checkbox_eleve_courant').checked=true;}";
			}
			$ligne_courante.='document.getElementById(\'creation_traitement\').value = \'yes\'; document.getElementById(\'ajout_traitement\').value = \'no\'; document.getElementById(\'creation_lot_traitements\').value = \'no\'; pop_it(document.creer_traitement);">
				Créer un nouveau traitement (popup)
			</button>';
			$ligne_courante.='<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_notification\').value = \'yes\'; document.getElementById(\'ajout_traitement\').value = \'no\'; document.getElementById(\'creation_lot_traitements\').value = \'no\'; pop_it(document.creer_traitement);">
				Créer une nouvelle notification (popup)
			</button>';
			$ligne_courante.='</div></div>';
			$ligne_courante.='</td>';
			$ligne_courante.="</tr>";


			if((!isset($ne_pas_afficher_lignes_avec_traitement_englobant))||($ne_pas_afficher_lignes_avec_traitement_englobant!="y")) {
				echo $ligne_courante;
			}
			else {
				if($nb_saisies_non_englobees>0) {
					echo $ligne_courante;
				}
				/*
				if($ligne_avec_traitement_englobant_autres_saisies!="y") {
					echo $ligne_courante;
				}
				*/
			}

    }

    echo " </tbody>";
    echo "</table>";   
    ?>
    <div dojoType="dijit.form.DropDownButton" style="display: inline">
	<span>Ajouter Les saisies cochées à un traitement</span>
	<div dojoType="dijit.Menu" style="display: inline">
	    <button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_traitement').value = 'yes'; document.getElementById('ajout_traitement').value = 'no'; document.getElementById('creation_lot_traitements').value = 'no'; document.creer_traitement.submit();">
		Créer un nouveau traitement
	    </button>
	    <button dojoType="dijit.MenuItem" onClick="document.getElementById('creation_traitement').value = 'yes'; document.getElementById('ajout_traitement').value = 'no'; document.getElementById('creation_lot_traitements').value = 'no'; pop_it(document.creer_traitement)">
		Créer un nouveau traitement dans une popup
	    </button>
    <?php
    $id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
    if ($id_traitement != null && AbsenceEleveTraitementQuery::create()->findPk($id_traitement) != null) {
	$traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
	echo '	<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; document.getElementById(\'creation_lot_traitements\').value = \'no\'; document.getElementById(\'id_traitement\').value = \''.$id_traitement.'\'; document.creer_traitement.submit();">'."\n";
	echo '	    Ajouter les saisies au traitement n° '.$id_traitement.' ('.$traitement->getDescription().')'."\n";
	echo '	</button>'."\n";
	echo '	<button dojoType="dijit.MenuItem" onClick="document.getElementById(\'creation_traitement\').value = \'no\'; document.getElementById(\'ajout_traitement\').value = \'yes\'; document.getElementById(\'creation_lot_traitements\').value = \'no\'; document.getElementById(\'id_traitement\').value = \''.$id_traitement.'\'; pop_it(document.creer_traitement);">'."\n";
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
//echo "</p>";
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

	if(document.getElementById("img_bug_dojo2")) {
		document.getElementById("img_bug_dojo2").style.display="";
		if(document.getElementById("img_bug_dojo")) {
			document.getElementById("img_bug_dojo").style.display="none";
		}
	}

	// Nettoyage après submit
	document.getElementById(\'absences_du_jour\').action=\'absences_du_jour.php\';
</script>';

require_once("../lib/footer.inc.php");

?>
