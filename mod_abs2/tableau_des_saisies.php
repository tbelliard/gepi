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
//mes fonctions
include("../edt_organisation/fonctions_calendrier.php");
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
$date_absence_eleve = isset($_POST["date_absence_eleve"]) ? $_POST["date_absence_eleve"] :(isset($_GET["date_absence_eleve"]) ? $_GET["date_absence_eleve"] :(isset($_SESSION["date_absence_eleve"]) ? $_SESSION["date_absence_eleve"] : NULL));
if ($date_absence_eleve != null) {$_SESSION["date_absence_eleve"] = $date_absence_eleve;}
if ($date_absence_eleve != null) {
    $dt_date_absence_eleve = new DateTime(str_replace("/",".",$date_absence_eleve));
} else {
    $dt_date_absence_eleve = new DateTime('now');
}
$choix_creneau = isset($_POST["choix_creneau"]) ? $_POST["choix_creneau"] : (isset($_GET["choix_creneau"]) ? $_GET["choix_creneau"] : null);
if ($choix_creneau === null) {
    $choix_creneau_obj = EdtCreneauPeer::retrieveEdtCreneauActuel();
    if ($choix_creneau_obj != null) {
	$choix_creneau = $choix_creneau_obj->getIdDefiniePeriode();
    }
} else {
    $choix_creneau_obj= EdtCreneauPeer::retrieveByPK($choix_creneau);
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
$titre_page = "Les absents du jour.";
require_once("../lib/header.inc");
include('menu_abs2.inc.php');
include('menu_bilans.inc.php');
?>
<div id="contain_div" class="css-panes">
<form name="choix_du_creneau" action="<?php $_SERVER['PHP_SELF']?>" method="post">
<h2>Les saisies du
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
    rangés par classe et par ordre alphabétique</h2>

	<p>Vous devez choisir un cr&eacute;neau pour visionner les absents
	<select name="choix_creneau" onchange='document.choix_du_creneau.submit();'>
		<option value="rien">Choix du cr&eacute;neau</option>
<?php
	foreach (EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $edtCreneau)	 {
	    //$edtCreneau = new EdtCreneau();
		if ($edtCreneau->getIdDefiniePeriode() == $choix_creneau) {
			$selected = ' selected="selected"';
		}else{
			$selected = '';
		}
		echo '<option value="'.$edtCreneau->getIdDefiniePeriode().'"'.$selected.'>'.$edtCreneau->getNomDefiniePeriode().'</option>';
	}
?>
	</select>
<br />
<?php
$creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();
foreach ($creneau_col as $creneau) {
    if ($creneau->getPrimaryKey() == $choix_creneau) {
	    $color_selected = 'style="color: red; font-weight: bold;"';
    }else{
	    $color_selected = '';
    }
    echo '<a href="" '.$color_selected.' onclick="document.choix_du_creneau.choix_creneau.selectedIndex = '.($creneau_col->getPosition() + 1).'; document.choix_du_creneau.submit(); return false;">'.$creneau->getNomDefiniePeriode();
    echo '</a>';
    if (!$creneau_col->isLast()) {
	echo '&nbsp;-&nbsp;';
    }
}
?>
</form>
<br />
<?php
if ($choix_creneau_obj != null) {
	echo '<br/>Voir les absences de <span style="color: blue;">'.$choix_creneau_obj->getHeuredebutDefiniePeriode('H:i').'</span> à <span style="color: blue;">'.$choix_creneau_obj->getHeurefinDefiniePeriode('H:i').'</span>.';
?>
<br />

<!-- Affichage des réponses-->
<table class="tab_edt" summary="Liste des absents r&eacute;partie par classe">
<?php
// On affiche la liste des classes
$classe_col = ClasseQuery::create()->orderByNom()->distinct()->find();
$dt_debut_creneau = clone $dt_date_absence_eleve;
$dt_debut_creneau->setTime($choix_creneau_obj->getHeuredebutDefiniePeriode('H'), $choix_creneau_obj->getHeuredebutDefiniePeriode('i'));
$dt_fin_creneau = clone $dt_date_absence_eleve;
$dt_fin_creneau->setTime($choix_creneau_obj->getHeurefinDefiniePeriode('H'), $choix_creneau_obj->getHeurefinDefiniePeriode('i'));
foreach($classe_col as $classe){
    //$classe = new Classe();
	// On détermine si sur deux colonnes, le compte tombe juste
	$calc = $classe_col->count() / 2;
	$modulo = $classe_col->count() % 2;
	$num_id = 'id'.remplace_accents($classe->getNom(), 'all');
	$id_classe = $classe->getId();
	if ($classe_col->isEven()) {
	    echo '<tr>';
	}
	echo '	<td>
			<h4 style="color: red;"><a href="#" onclick="AfficheEdtClasseDuJour(\''.$id_classe.'\',\''.$num_id.'\', 1); return false;">'.$classe->getNom().'</a></h4>
			<div id="'.$num_id.'" style="display: none; position: absolute; background-color: white; -moz-border-radius: 10px; padding: 10px;">
			</div>
		</td>';

	//la classe a-t-elle des cours actuellement ?
	//on regarde au debut du creneau et a la fin car il peut y avoir des demi creneau
	$cours_col = $classe->getEdtEmplacementCours($dt_debut_creneau);
	$dt_presque_fin_creneau = clone $dt_fin_creneau;
	$dt_presque_fin_creneau->setTime($choix_creneau_obj->getHeurefinDefiniePeriode('H'), $choix_creneau_obj->getHeurefinDefiniePeriode('i') - 1);
	$cours_col_2 = $classe->getEdtEmplacementCours($dt_presque_fin_creneau);
	$cours_col->addCollection($cours_col_2);

	//on teste si l'appel a été fait
	$appel_manquant = false;
	$echo_str = '';
	$classe_deja_sorties = Array();//liste des appels deja affiché sous la form [id_classe, id_utilisateur]
	$groupe_deja_sortis = Array();//liste des appels deja affiché sous la form [id_groupe, id_utilisateur]
	foreach ($cours_col as $edtCours) {//on regarde tous les cours enregistrés dans l'edt
	    //$edtCours = new EdtEmplacementCours();
	    $abs_col = AbsenceEleveSaisieQuery::create()->filterByPlageTemps($dt_debut_creneau, $dt_fin_creneau)
		      ->filterByEdtEmplacementCours($edtCours)->find();
	    if ($abs_col->isEmpty()) {
		$appel_manquant = true;
		$echo_str .= 'Non fait ';
	    } else {
		$echo_str .= $abs_col->getFirst()->getCreatedAt('H:i').' ';
	    }
	    if ($edtCours->getGroupe() != null) {
		$echo_str .= $edtCours->getGroupe()->getName().' ';
		if ($abs_col->getFirst() !== null) {
		    $groupe_deja_sortis[] = Array($edtCours->getIdGroupe(),  $abs_col->getFirst()->getUtilisateurId());
		}
	    }
	    if ($edtCours->getUtilisateurProfessionnel() != null) {
		$echo_str .= $edtCours->getUtilisateurProfessionnel()->getCivilite().' '
			.$edtCours->getUtilisateurProfessionnel()->getNom().' '
			.strtoupper(substr($edtCours->getUtilisateurProfessionnel()->getPrenom(), 0 ,1)).'. ';
	    }
	    if ($edtCours->getEdtSalle() != null) {
		$echo_str .= $edtCours->getEdtSalle()->getNumeroSalle();
	    }
	    $echo_str .= '<br/>';
	}

	//$classe = new Classe();
	//on regarde si il y a d'autres appels
	$abs_col = AbsenceEleveSaisieQuery::create()->filterByPlageTemps($dt_debut_creneau, $dt_fin_creneau)
		  ->condition('cond1', 'AbsenceEleveSaisie.IdClasse = ?', $classe->getId()) // create a condition named 'cond1'
		  ->condition('cond2', 'AbsenceEleveSaisie.IdGroupe IN ?', $classe->getGroupes()->toKeyValue('Id', 'Id'))       // create a condition named 'cond2'
		  ->where(array('cond1', 'cond2'), 'or')              // combine 'cond1' and 'cond2' with a logical OR
		  ->find();
	if ($abs_col->isEmpty()) {
	    if ($cours_col->isEmpty()) {
		$appel_manquant = true;
		$echo_str .= 'Appel non fait<br/>';
	    }
	} else {
	    if ($cours_col->isEmpty()) {
		$appel_manquant = false;
	    }
	    foreach ($abs_col as $abs) {//$abs = new AbsenceEleveSaisie();
		$affiche = false;
		if ($abs->getIdClasse()!=null && !in_array(Array($abs->getIdClasse(), $abs->getUtilisateurId()), $classe_deja_sorties)) {
		    $echo_str .= $abs->getCreatedAt('H:i').' ';
		    $echo_str .= ' '.$abs->getClasse()->getNom().' ';
		    $classe_deja_sorties[] = Array($abs->getClasse()->getId(), $abs->getUtilisateurId());
		    $affiche = true;
		}
		if ($abs->getIdGroupe()!=null && !in_array(Array($abs->getIdGroupe(), $abs->getUtilisateurId()), $groupe_deja_sortis)) {
		    $echo_str .= $abs->getCreatedAt('H:i').' ';
		    $echo_str .= ' '.$abs->getGroupe()->getName().' ';
		    $groupe_deja_sortis[] = Array($abs->getIdGroupe(), $abs->getUtilisateurId());
		    $affiche = true;
		}
		if ($affiche) {//on affiche un appel donc on va afficher les infos du prof
		    $echo_str .= ' '.$abs->getUtilisateurProfessionnel()->getCivilite().' '
			    .$abs->getUtilisateurProfessionnel()->getNom().' '
			    .strtoupper(substr($abs->getUtilisateurProfessionnel()->getPrenom(), 0 ,1)).'. ';
		    $prof_deja_sortis[] = $abs->getUtilisateurProfessionnel()->getPrimaryKey();
		    $echo_str .= '<br/>';
		}
	    }
	}
	if ($appel_manquant) {
	    echo '<td style="min-width: 350px;">';
	} else {
	    echo '<td style="min-width: 350px; background-color:green">';
	}
	echo $echo_str;


	//on affiche les saisies du creneau
	$abs_col = AbsenceEleveSaisieQuery::create()->filterByPlageTemps($dt_debut_creneau, $dt_fin_creneau)
			->useEleveQuery()->orderByNom()->useJEleveClasseQuery()->filterByClasse($classe)->endUse()->endUse()
			->leftJoinWith('AbsenceEleveSaisie.JTraitementSaisieEleve')
			->leftJoinWith('JTraitementSaisieEleve.AbsenceEleveTraitement')
			->leftJoinWith('AbsenceEleveTraitement.AbsenceEleveType')
			->find();
	//echo $td_classe1[$a].$td_classe[$a];
	if (!$abs_col->isEmpty()) {
	    echo '<br/>';
	}
	foreach ($abs_col as $absenceSaisie) {
	    if ($absenceSaisie->getManquementObligationPresence()) {
		echo "<a style='color: red;' href='visu_saisie.php?id_saisie=".$absenceSaisie->getPrimaryKey()."'>";
	    } else {
		echo "<a href='visu_saisie.php?id_saisie=".$absenceSaisie->getPrimaryKey()."'>";
	    }
	    echo ($absenceSaisie->getEleve()->getCivilite().' '.$absenceSaisie->getEleve()->getNom().' '.$absenceSaisie->getEleve()->getPrenom());
	    echo "</a>";
	    if ($utilisateur->getAccesFicheEleve($absenceSaisie->getEleve())) {
		echo "<a href='../eleves/visu_eleve.php?ele_login=".$absenceSaisie->getEleve()->getLogin()."' target='_blank'>";
		echo ' (voir fiche)';
		echo "</a>";
	    }
	    echo '<br/>';
	}
	echo '</td>';
	if ($classe_col->isOdd()) {
	    echo '</tr>';
	}else if ($classe_col->isLast()) {
	    echo '<td></td><td></td>';
	    echo '</tr>';
	}
}
?>
	<tr>
		<td>Les Aid</td>
		<td colspan="3">
<?php
	//on affiche les saisies du creneau
	$abs_col = AbsenceEleveSaisieQuery::create()->filterByPlageTemps($dt_debut_creneau, $dt_fin_creneau)
			->filterByIdAid(null, Criteria::NOT_EQUAL)
			->useAidDetailsQuery()->orderByNom()->endUse()
			->find();
	if (!$abs_col->isEmpty()) {
	    $aid_deja_sorties = Array();
	    foreach ($abs_col as $abs) {
		    if ($abs->getIdAid()!==null && !in_array($abs->getIdAid(), $aid_deja_sorties)) {
			echo 'Appel fait pour l\'aid '.$abs->getAidDetails()->getNom();
			$aid_deja_sorties[] = $abs->getAidDetails()->getId();
			echo '<br/>';
		    }
		    if ($absenceSaisie->getEleve() != null) {
			if ($absenceSaisie->getManquementObligationPresence()) {
			    echo "<a style='color: red;' href='visu_saisie.php?id_saisie=".$absenceSaisie->getPrimaryKey()."'>";
			} else {
			    echo "<a href='visu_saisie.php?id_saisie=".$absenceSaisie->getPrimaryKey()."'>";
			}
			echo ($absenceSaisie->getEleve()->getCivilite().' '.$absenceSaisie->getEleve()->getNom().' '.$absenceSaisie->getEleve()->getPrenom());
			echo "</a>";
			if ($utilisateur->getAccesFicheEleve($absenceSaisie->getEleve())) {
			    echo "<a href='../eleves/visu_eleve.php?ele_login=".$absenceSaisie->getEleve()->getLogin()."' target='_blank'>";
			    echo ' (voir fiche)';
			    echo "</a>";
			}
			echo '<br/>';
		    }

	    }
	}
?>
		</td>
	</tr>
</table>

<?php
}
echo '</div>';
require("../lib/footer.inc.php");
?>