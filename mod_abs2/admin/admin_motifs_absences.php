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
if (empty($_GET['id_motif']) and empty($_POST['id_motif'])) { $id_motif="";}
    else { if (isset($_GET['id_motif'])) {$id_motif=$_GET['id_motif'];} if (isset($_POST['id_motif'])) {$id_motif=$_POST['id_motif'];} }
if (empty($_GET['nom_motif']) and empty($_POST['nom_motif'])) { $nom_motif=""; }
    else { if (isset($_GET['nom_motif'])) {$nom_motif=$_GET['nom_motif'];} if (isset($_POST['nom_motif'])) {$nom_motif=$_POST['nom_motif'];} }
if (empty($_GET['com_motif']) and empty($_POST['com_motif'])) { $com_motif="";}
    else { if (isset($_GET['com_motif'])) {$com_motif=$_GET['com_motif'];} if (isset($_POST['com_motif'])) {$com_motif=$_POST['com_motif'];} }

include("function.php");

$motif = AbsenceEleveMotifQuery::create()->findPk($id_motif);
if ($action == 'supprimer') {
	check_token();
    if ($motif != null) {
	$motif->delete();
    }
} elseif ($action == "monter") {
	check_token();
    if ($motif != null) {
	$motif->moveUp();
    }
} elseif ($action == 'descendre') {
	check_token();
    if ($motif != null) {
	$motif->moveDown();
    }
} elseif ($action == 'ajouterdefaut') {
	check_token();
    //include("function.php");
    ajoutMotifsParDefaut();
} else {
    if ($nom_motif != '') {
		check_token();
		$motif = AbsenceEleveMotifQuery::create()->findPk($id_motif);
		if ($motif == null) {
			$motif = new AbsenceEleveMotif();
		}
		$motif->setNom(stripslashes($nom_motif));
		$motif->setCommentaire(stripslashes($com_motif));
		$motif->save();
    }
}

if(isset($_GET['corriger'])) {
	check_token();

	$table="a_motifs";

	$sql="SELECT * FROM $table ORDER BY sortable_rank, nom;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$cpt=1;
	while($lig=mysqli_fetch_object($res)) {
		$sql="UPDATE $table SET sortable_rank='$cpt' WHERE id='$lig->id';";
		//echo "$sql<br />";
		$update=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$update) {
			$msg="Erreur lors de la correction des rangs.<br />";
			break;
		}
		$cpt++;
	}
	$msg="Correction effectuée.<br />";
}
//==========================================
// header
$titre_page = "Gestion des motifs d'absence";
require_once("../../lib/header.inc.php");
//==========================================

echo "<p class=bold>";
echo "<a href=\"index.php\">";
echo "<img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>";
?>

<div style="text-align:center">
    <h2>Définition des motifs d'absence</h2>
<?php if ($action == "ajouter" OR $action == "modifier") { ?>
<div style="text-align:center">
    <?php
    	if($action=="ajouter") { 
	    echo "<h2>Ajout d'un motif</h2>";
	} elseif ($action=="modifier") {
	    echo "<h2>Modifier un motif</h2>";
	}
	?>

    <form action="admin_motifs_absences.php" method="post" name="form2" id="form2">
<?php
echo add_token_field();
?>
     <fieldset>
      <table cellpadding="2" cellspacing="2" class="menu">
        <tr>
          <td>Nom (obligatoire)</td>
          <td colspan="2">Commentaire (facultatif)</td>
        </tr>
        <tr>
          <td>
           <?php
           $motif = AbsenceEleveMotifQuery::create()->findPk($id_motif);
	   if ($motif != null) { ?>
	      <input name="id_motif" type="hidden" id="id_motif" value="<?php echo $id_motif ?>" />
	   <?php } ?>
	      <input name="nom_motif" type="text" id="nom_motif" size="15" maxlength="15" value="<?php  if ($motif != null) {echo $motif->getNom();} ?>"/>
           </td>
           <td colspan="2">
	       <input name="com_motif" type="text" id="com_motif" size="40" value="<?php  if ($motif != null) {echo $motif->getCommentaire();} ?>"/>
           </td>
        </tr>
      </table>
     </fieldset>
     <input type="submit" name="Submit" value="Enregistrer" />
    </form>
<br/><br/>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php }  ?>

	<a href="admin_motifs_absences.php?action=ajouter"><img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter un motif</a>
	<br/><br/>
	<a href="admin_motifs_absences.php?action=ajouterdefaut<?php echo add_token_in_url();?>"><img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter les motifs par défaut</a>
	<br/><br/>
    <table cellpadding="0" cellspacing="1" class="menu">
      <tr>
        <td>Nom</td>
        <td>Commentaire</td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
      </tr>
    <?php
    $motif_collection = new PropelCollection();
    $motif_collection = AbsenceEleveMotifQuery::create()->findList();
    $motif = new AbsenceEleveMotif();
    $i = '1';
    foreach ($motif_collection as $motif) { ?>
        <tr>
	  <td><?php echo $motif->getNom(); ?></td>
	  <td><?php echo $motif->getCommentaire(); ?></td>
          <td><a href="admin_motifs_absences.php?action=modifier&amp;id_motif=<?php echo $motif->getId(); echo add_token_in_url();?>"><img src="../../images/icons/configure.png" title="Modifier" border="0" alt="" /></a></td>
          <td><a href="admin_motifs_absences.php?action=supprimer&amp;id_motif=<?php echo $motif->getId(); echo add_token_in_url();?>" onClick="return confirm('Etes-vous sûr de vouloir supprimer ce motif ?')"><img src="../../images/icons/delete.png" width="22" height="22" title="Supprimer" border="0" alt="" /></a></td>
          <td><a href="admin_motifs_absences.php?action=monter&amp;id_motif=<?php echo $motif->getId(); echo add_token_in_url();?>"><img src="../../images/up.png" width="22" height="22" title="monter" border="0" alt="" /></a></td>
          <td><a href="admin_motifs_absences.php?action=descendre&amp;id_motif=<?php echo $motif->getId(); echo add_token_in_url();?>"><img src="../../images/down.png" width="22" height="22" title="descendre" border="0" alt="" /></a></td>
        </tr>
     <?php } ?>
    </table>
    <br/><br/>
</div>

<?php
	echo check_sortable_rank_trouble('a_motifs', 'motifs');
	require("../../lib/footer.inc.php");
?>
