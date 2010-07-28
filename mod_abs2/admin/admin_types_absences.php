<?php
/*
 *
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel, Josselin Jacquard
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

$niveau_arbo = 2;
// Initialisations files
include("../../lib/initialisationsPropel.inc.php");
require_once("../../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
};

// Check access
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

if (empty($_GET['action']) and empty($_POST['action'])) { $action="";}
    else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
if (empty($_GET['id']) and empty($_POST['id'])) { $id="";}
    else { if (isset($_GET['id'])) {$id=$_GET['id'];} if (isset($_POST['id'])) {$id=$_POST['id'];} }
if (empty($_GET['statut_id']) and empty($_POST['statut_id'])) { $statut_id="";}
    else { if (isset($_GET['statut_id'])) {$statut_id=$_GET['statut_id'];} if (isset($_POST['statut_id'])) {$statut_id=$_POST['statut_id'];} }
if (empty($_GET['nom']) and empty($_POST['nom'])) { $nom="";}
    else { if (isset($_GET['nom'])) {$nom=$_GET['nom'];} if (isset($_POST['nom'])) {$nom=$_POST['nom'];} }
if (empty($_GET['commentaire']) and empty($_POST['commentaire'])) { $commentaire="";}
    else { if (isset($_GET['commentaire'])) {$commentaire=$_GET['commentaire'];} if (isset($_POST['commentaire'])) {$commentaire=$_POST['commentaire'];} }
if (empty($_GET['justification_exigible']) and empty($_POST['justification_exigible'])) { $justification_exigible="";}
    else { if (isset($_GET['justification_exigible'])) {$justification_exigible=$_GET['justification_exigible'];} if (isset($_POST['justification_exigible'])) {$justification_exigible=$_POST['justification_exigible'];} }
if (empty($_GET['responsabilite_etablissement']) and empty($_POST['responsabilite_etablissement'])) { $responsabilite_etablissement="";}
    else { if (isset($_GET['responsabilite_etablissement'])) {$responsabilite_etablissement=$_GET['responsabilite_etablissement'];} if (isset($_POST['responsabilite_etablissement'])) {$responsabilite_etablissement=$_POST['responsabilite_etablissement'];} }
if (empty($_GET['type_saisie']) and empty($_POST['type_saisie'])) { $type_saisie="";}
    else { if (isset($_GET['type_saisie'])) {$type_saisie=$_GET['type_saisie'];} if (isset($_POST['type_saisie'])) {$type_saisie=$_POST['type_saisie'];} }
if (empty($_GET['ajout_statut_type_saisie']) and empty($_POST['ajout_statut_type_saisie'])) { $ajout_statut_type_saisie="";}
    else { if (isset($_GET['ajout_statut_type_saisie'])) {$type_saisie=$_GET['ajout_statut_type_saisie'];} if (isset($_POST['ajout_statut_type_saisie'])) {$ajout_statut_type_saisie=$_POST['ajout_statut_type_saisie'];} }

//$type = new AbsenceEleveType();
$type = AbsenceEleveTypeQuery::create()->findPk($id);
if ($action == 'supprimer') {
    if ($type != null) {
	$type->delete();
    }
} elseif ($action == "monter") {
    if ($type != null) {
	$type->moveUp();
    }
} elseif ($action == 'descendre') {
    if ($type != null) {
	$type->moveDown();
    }
} elseif ($action == 'ajouterdefaut') {
    include("function.php");
    ajoutTypesParDefaut();
} elseif ($action == 'supprimer_statut') {
	$type_statut = AbsenceEleveTypeStatutAutoriseQuery::create()->findPk($statut_id);
	if ($type_statut != null) {
	    $type_statut->delete();
	}
} else {
    if ($nom != '') {
	$type = AbsenceEleveTypeQuery::create()->findPk($id);
	if ($type == null) {
	    $type = new AbsenceEleveType();
	}
	$type->setNom(stripslashes($nom));
	$type->setCommentaire(stripslashes($commentaire));
	$type->setJustificationExigible($justification_exigible);
	$type->setResponsabiliteEtablissement($responsabilite_etablissement);
	$type->setTypeSaisie($type_saisie);
	$type->getAbsenceEleveTypeStatutAutorises(); //corrige un bug de propel sur la lecture de la base
	if ($ajout_statut_type_saisie != '') {
	    //test si le statut est deja autorisé
	    if (AbsenceEleveTypeStatutAutoriseQuery::create()->
		    filterByStatut($ajout_statut_type_saisie)->
		    filterByIdAType($type->getId())->
		    find()->isEmpty()) {
			//on creer un nouveau statut autorisé
		$statut_ajout = new AbsenceEleveTypeStatutAutorise();
		$statut_ajout->setStatut($ajout_statut_type_saisie);
		$type->addAbsenceEleveTypeStatutAutorise($statut_ajout);
		$statut_ajout->save();
	    }
	    $action = "modifier";
	}
	$type->save();
    }
}

// header
$titre_page = "Gestion des types d'absence";
require_once("../../lib/header.inc");

echo "<p class=bold>";
echo "<a href=\"index.php\">";
echo "<img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>";
?>

<div style="text-align:center">
    <h2>Définition des types d'absence</h2>
<?php if ($action == "ajouter" OR $action == "modifier" OR $action == "supprimer_statut") { ?>
<div style="text-align:center">
    <?php
    	if($action=="ajouter") { 
	    echo "<h2>Ajout d'un type</h2>";
	} elseif ($action=="modifier") {
	    echo "<h2>Modifier un type</h2>";
	}
	?>

    <form action="admin_types_absences.php" method="post" name="form2" id="form2">
     <fieldset class="fieldset_efface">
      <table cellpadding="2" cellspacing="2" class="menu">
        <tr>
          <td>Nom (obligatoire)</td>
          <td>Commentaire (facultatif)</td>
	    <td>Justification exigible</td>
	    <td>Eleve sous la responsabilite de l'etablissement</td>
	    <td>Type de saisie</td>
	    <td>Statut autorisé à la saisie</td>
       </tr>
        <tr>
          <td>
           <?php
           //$type = AbsenceEleveTypeQuery::create()->findPk($id);
	   if ($type != null) { ?>
	      <input name="id" type="hidden" id="id" value="<?php echo $id ?>" />
	   <?php } ?>
	      <input name="nom" type="text" id="nom" size="14" maxlength="50" value="<?php  if ($type != null) {echo $type->getNom();} ?>" />
           </td>
           <td><textarea name="commentaire" rows="3" cols="22"><?php  if ($type != null) {echo $type->getCommentaire();} ?></textarea></td>
           <td><input name="justification_exigible" type="checkbox" id="justification_exigible" <?php  if ($type != null && $type->getJustificationExigible()) {echo "checked";} ?> /></td>
           <td><input name="responsabilite_etablissement" type="checkbox" id="responsabilite_etablissement" <?php  if ($type != null && $type->getResponsabiliteEtablissement()) {echo "checked";} ?> class="input_sans_bord" /></td>
           <td>
	     <select name="type_saisie" id="type_saisie">
		<option value='NON_PRECISE' <?php  if ($type != null && $type->getTypeSaisie() == 'NON_PRECISE') {echo "selected";} ?>>Type de saisie non precise</option>
		<!--<option value='DEBUT_ABS' <?php  if ($type != null && $type->getTypeSaisie() == 'DEBUT_ABS') {echo "selected";} ?>>Saisir le moment de debut de l'absence</option>
		<option value='FIN_ABS' <?php  if ($type != null && $type->getTypeSaisie() == 'FIN_ABS') {echo "selected";} ?>>Saisir le moment de fin de l'absence</option>
		<option value='DEBUT_ET_FIN_ABS' <?php  if ($type != null && $type->getTypeSaisie() == 'DEBUT_ET_FIN_ABS') {echo "selected";} ?>>Saisir le moment de debut et de fin</option>
		<option value='COMMENTAIRE_EXIGE' <?php  if ($type != null && $type->getTypeSaisie() == 'COMMENTAIRE_EXIGE') {echo "selected";} ?>>Saisir un commentaire</option>
		--><option value='DISCIPLINE' <?php  if ($type != null && $type->getTypeSaisie() == 'DISCIPLINE') {echo "selected";} ?>>Saisir un incident disciplinaire</option>
	     </select>
	   </td>
           <td>
		<table class="menu"><?php
		if ($type != null) {
			foreach ($type->getAbsenceEleveTypeStatutAutorises() as $statut_saisie) {
				echo "<tr><td>";
				echo $statut_saisie->getStatut();
				echo "</td>";
		  		echo '<td><a href="admin_types_absences.php?action=supprimer_statut&amp;id='. $type->getId(). '&amp;statut_id='. $statut_saisie->getId() .'"><img src="../../images/icons/delete.png" width="22" height="22" title="Supprimer" border="0" alt="" /></a></td>';
				echo "</tr>";
			}
		}
	  	?>
           <tr><td>
	     <select name="ajout_statut_type_saisie" id="ajout_statut_type_saisie">
		<option value=''>Ajout d'un statut</option>
		<option value='professeur'>professeur</option>
		<option value='cpe'>cpe</option>
		<option value='scolarite'>scolarite</option>
		<option value='autre'>autre</option>
	     </select>
	   </td></tr>

		</table>
	   </td>
        </tr>
      </table>
     </fieldset>
     <input type="submit" name="Submit" value="Enregistrer" />
    </form>
<br/><br/>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php
} ?>
	<a href="admin_types_absences.php?action=ajouter"><img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter un nouveau type</a>
	<br/><br/>
	<a href="admin_types_absences.php?action=ajouterdefaut"><img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter les types par defaut</a>
	<br/><br/>
    <table cellpadding="0" cellspacing="1" class="menu">
      <tr>
        <td>Nom</td>
        <td>Commentaire</td>
        <td>Justification exigible</td>
        <td>Eleve sous la responsabilite de l'etablissement</td>
        <td>Type de saisie</td>
	<td>Statuts autorisés à la saisie</td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
      </tr>
    <?php
    $type_collection = new PropelCollection();
    $type_collection = AbsenceEleveTypeQuery::create()->findList();
    $type = new AbsenceEleveType();
    $i = '1';
    foreach ($type_collection as $type) { ?>
        <tr>
	  <td><?php echo $type->getNom(); ?></td>
	  <td><?php echo $type->getCommentaire(); ?></td>
	  <td><?php if ($type->getJustificationExigible()) { ?><img src='../../images/enabled.png' width='20' height='20' title='oui' alt='oui' /><?php } ?></td>
	  <td><?php if ($type->getResponsabiliteEtablissement()) { ?><img src='../../images/enabled.png' width='20' height='20' title='oui' alt='oui' /><?php } ?></td>
	  <td><?php echo $type->getTypeSaisieDescription(); ?></td>
	  <td><?php
		foreach ($type->getAbsenceEleveTypeStatutAutorises() as $statut_saisie) {
			echo $statut_saisie->getStatut();
			echo " ";
		}
	  ?></td>
          <td><a href="admin_types_absences.php?action=modifier&amp;id=<?php echo $type->getId(); ?>"><img src="../../images/icons/configure.png" title="Modifier" border="0" alt="" /></a></td>
          <td><a href="admin_types_absences.php?action=supprimer&amp;id=<?php echo $type->getId(); ?>" onClick="return confirm('Etes-vous sûr de vouloir supprimer ce type ?')"><img src="../../images/icons/delete.png" width="22" height="22" title="Supprimer" border="0" alt="" /></a></td>
          <td><a href="admin_types_absences.php?action=monter&amp;id=<?php echo $type->getId(); ?>"><img src="../../images/up.png" width="22" height="22" title="monter" border="0" alt="" /></a></td>
          <td><a href="admin_types_absences.php?action=descendre&amp;id=<?php echo $type->getId(); ?>"><img src="../../images/down.png" width="22" height="22" title="descendre" border="0" alt="" /></a></td>
        </tr>
     <?php } ?>
    </table>
    <br/><br/>
</div>


<?php require("../../lib/footer.inc.php");?>
