<?php
/*
 *
 *
 * Copyright 2010-2011 Josselin Jacquard
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
}

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

//$justification = new AbsenceElevejustification();
$justification = AbsenceEleveJustificationQuery::create()->findPk($id);
if ($action == 'supprimer') {
	check_token();
    if ($justification != null) {
	$justification->delete();
    }
} elseif ($action == "monter") {
	check_token();
    if ($justification != null) {
	$justification->moveUp();
    }
} elseif ($action == 'descendre') {
	check_token();
    if ($justification != null) {
	$justification->moveDown();
    }
} elseif ($action == 'ajouterdefaut') {
	check_token();
    include("function.php");
    ajoutJustificationsParDefaut();
} else {
    if ($nom != '') {
		check_token();
		$justification = AbsenceEleveJustificationQuery::create()->findPk($id);
		if ($justification == null) {
			$justification = new AbsenceEleveJustification();
		}
		$justification->setNom(stripslashes($nom));
		$justification->setCommentaire(stripslashes($commentaire));
		$justification->save();
    }
}

// header
$titre_page = "Gestion des justifications d'absence";
require_once("../../lib/header.inc.php");

echo "<p class=bold>";
echo "<a href=\"index.php\">";
echo "<img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>";
?>

<div style="text-align:center">
    <h2>Définition des justifications d'absence</h2>
<?php if ($action == "ajouter" OR $action == "modifier" OR $action == "supprimer_statut") { ?>
<div style="text-align:center">
    <?php
    	if($action=="ajouter") { 
	    echo "<h2>Ajout d'une justification</h2>";
	} elseif ($action=="modifier") {
	    echo "<h2>Modifier une justification</h2>";
	}
	?>

    <form action="admin_justifications_absences.php" method="post" name="form2" id="form2">
<?php
echo add_token_field();
?>
      <table cellpadding="2" cellspacing="2" class="menu">
        <tr>
          <td>Nom (obligatoire)</td>
          <td>Commentaire (facultatif)</td>
       </tr>
        <tr>
          <td>
           <?php
           //$justification = AbsenceElevejustificationQuery::create()->findPk($id);
	   if ($justification != null) { ?>
	      <input name="id" type="hidden" id="id" value="<?php echo $id ?>" />
	   <?php } ?>
	      <input name="nom" type="text" id="nom" size="14" maxlength="50" value="<?php  if ($justification != null) {echo $justification->getNom();} ?>" />
           </td>
           <td><textarea name="commentaire"><?php  if ($justification != null) {echo $justification->getCommentaire();} ?></textarea></td>
        </tr>
      </table>
     <input type="submit" name="Submit" value="Enregistrer" />
    </form>
<br/><br/>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php
} ?>
	<a href="admin_justifications_absences.php?action=ajouter"><img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter une nouvelle justification</a>
	<br/><br/>
	<a href="admin_justifications_absences.php?action=ajouterdefaut<?php echo add_token_in_url();?>"><img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter les justifications par défaut</a>
	<br/><br/>
    <table cellpadding="0" cellspacing="1" class="menu">
      <tr>
        <td>Nom</td>
        <td>Commentaire</td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
      </tr>
    <?php
    $justification_collection = new PropelCollection();
    $justification_collection = AbsenceEleveJustificationQuery::create()->findList();
    $justification = new AbsenceElevejustification();
    $i = '1';
    foreach ($justification_collection as $justification) { ?>
        <tr>
	  <td><?php echo $justification->getNom(); ?></td>
	  <td><?php echo $justification->getCommentaire(); ?></td>
          <td><a href="admin_justifications_absences.php?action=modifier&amp;id=<?php echo $justification->getId(); echo add_token_in_url();?>"><img src="../../images/icons/configure.png" title="Modifier" border="0" alt="" /></a></td>
          <td><a href="admin_justifications_absences.php?action=supprimer&amp;id=<?php echo $justification->getId(); echo add_token_in_url();?>" onClick="return confirm('Etes-vous sûr de vouloir supprimer ce justification ?')"><img src="../../images/icons/delete.png" width="22" height="22" title="Supprimer" border="0" alt="" /></a></td>
          <td><a href="admin_justifications_absences.php?action=monter&amp;id=<?php echo $justification->getId(); echo add_token_in_url();?>"><img src="../../images/up.png" width="22" height="22" title="monter" border="0" alt="" /></a></td>
          <td><a href="admin_justifications_absences.php?action=descendre&amp;id=<?php echo $justification->getId(); echo add_token_in_url();?>"><img src="../../images/down.png" width="22" height="22" title="descendre" border="0" alt="" /></a></td>
        </tr>
     <?php } ?>
    </table>
    <br/><br/>
</div>


<?php require("../../lib/footer.inc.php");?>
