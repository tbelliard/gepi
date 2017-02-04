<?php
/*
 *
 *
 * Copyright 2010-2017 Josselin Jacquard, Stephane Boireau
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

if(!acces_consultation_admin_abs2("/mod_abs2/admin/admin_lieux_absences.php")) {
	header("Location: ../../accueil.php?msg=Accès non autorisé");
	die();
}

$acces_saisie_admin_abs2=acces_saisie_admin_abs2("/mod_abs2/admin/admin_lieux_absences.php");

if (empty($_GET['action']) and empty($_POST['action'])) { $action="";}
    else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
if (empty($_GET['id_lieu']) and empty($_POST['id_lieu'])) { $id_lieu="";}
    else { if (isset($_GET['id_lieu'])) {$id_lieu=$_GET['id_lieu'];} if (isset($_POST['id_lieu'])) {$id_lieu=$_POST['id_lieu'];} }
if (empty($_GET['nom_lieu']) and empty($_POST['nom_lieu'])) { $nom_lieu=""; }
    else { if (isset($_GET['nom_lieu'])) {$nom_lieu=$_GET['nom_lieu'];} if (isset($_POST['nom_lieu'])) {$nom_lieu=$_POST['nom_lieu'];} }
if (empty($_GET['com_lieu']) and empty($_POST['com_lieu'])) { $com_lieu="";}
    else { if (isset($_GET['com_lieu'])) {$com_lieu=$_GET['com_lieu'];} if (isset($_POST['com_lieu'])) {$com_lieu=$_POST['com_lieu'];} }

include("function.php");

if(((($action!="")&&($action!="visualiser"))||($nom_lieu!="")||(isset($_GET['corriger'])))&&
(!$acces_saisie_admin_abs2)) {
	//debug_var();
	header("Location: ../../accueil.php?msg=Saisie non autorisée");
	die();
}
else {
	$lieu = AbsenceEleveLieuQuery::create()->findPk($id_lieu);
	if ($action == 'supprimer') {
		check_token();
	    if ($lieu != null) {
		$lieu->delete();
	    }
	} elseif ($action == "monter") {
		check_token();
	    if ($lieu != null) {
		$lieu->moveUp();
	    }
	} elseif ($action == 'descendre') {
		check_token();
	    if ($lieu != null) {
		$lieu->moveDown();
	    }
	} elseif ($action == 'ajouterdefaut') {
		check_token();
	    //include("function.php");
	    ajoutLieuxParDefaut();
	} else {
	    if ($nom_lieu != '') {
			check_token();
			$lieu = AbsenceEleveLieuQuery::create()->findPk($id_lieu);
			if ($lieu == null) {
				$lieu = new AbsenceEleveLieu();
			}
			$lieu->setNom(stripslashes($nom_lieu));
			$lieu->setCommentaire(stripslashes($com_lieu));
			$lieu->save();
	    }
	}

	if(isset($_GET['corriger'])) {
		check_token();

		$table="a_lieux";

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
}
//==========================================
// header
$titre_page = "Gestion des lieux d'absence";
require_once("../../lib/header.inc.php");
//==========================================

echo "<p class=bold>";
echo "<a href=\"index.php";
if($_SESSION['statut']!="administrateur") {
	echo "#config_avancee";
}
echo "\">";
echo "<img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>";
?>

<div style="text-align:center">
    <h2>Définition des lieux d'absence</h2>
    <p>
        Un lieu pourra être affecté à un type de saisie pour permettre de savoir ou se trouve l'élève saisie. <br />
        Pour les types sans lieu défini on considèrera que l'élève n'est pas dans l'enceinte de l'établissement.<br />
    </p>
<?php
if($acces_saisie_admin_abs2) {
	if ($action == "ajouter" OR $action == "modifier") { ?>
<div style="text-align:center">
    <?php
    	if($action=="ajouter") { 
	    echo "<h2>Ajout d'un lieu</h2>";
	} elseif ($action=="modifier") {
	    echo "<h2>Modifier un lieu</h2>";
	}
	?>

    <form action="admin_lieux_absences.php" method="post" name="form2" id="form2">
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
           $lieu = AbsenceEleveLieuQuery::create()->findPk($id_lieu);
	   if ($lieu != null) { ?>
	      <input name="id_lieu" type="hidden" id="id_lieu" value="<?php echo $id_lieu ?>" />
	   <?php } ?>
	      <input name="nom_lieu" type="text" id="nom_lieu" size="15" maxlength="15" value="<?php  if ($lieu != null) {echo $lieu->getNom();} ?>"/>
           </td>
           <td colspan="2">
	       <input name="com_lieu" type="text" id="com_lieu" size="40" value="<?php  if ($lieu != null) {echo $lieu->getCommentaire();} ?>"/>
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

	<a href="admin_lieux_absences.php?action=ajouter"><img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter un lieu</a>
	<br/><br/>
	<a href="admin_lieux_absences.php?action=ajouterdefaut<?php echo add_token_in_url();?>"><img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter les lieux par défaut</a>
<?php
}
?>
	<br/><br/>
    <table cellpadding="0" cellspacing="1" class="menu">
      <tr>
        <td>Nom</td>
        <td>Commentaire</td>
        <td title="Nombre de saisies/traitements avec ce lieu.">Effectif</td>
<?php 
if($acces_saisie_admin_abs2) {
?>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
<?php 
}
?>
      </tr>
    <?php
    $lieu_collection = new PropelCollection();
    $lieu_collection = AbsenceEleveLieuQuery::create()->findList();
    $lieu = new AbsenceEleveLieu();
    $i = '1';
    foreach ($lieu_collection as $lieu) { ?>
        <tr>
	  <td><?php echo $lieu->getNom(); ?></td>
	  <td><?php echo $lieu->getCommentaire(); ?></td>
<?php 
	$nb_saisies_traitements_avec_ce_lieu=abs2_nombre_de_saisies_avec_tel_lieu($lieu->getId());
	echo "
	<td title=\"$nb_saisies_traitements_avec_ce_lieu saisies/traitements ont ce lieu\">".$nb_saisies_traitements_avec_ce_lieu."</td>";
	if($acces_saisie_admin_abs2) {
?>
          <td><a href="admin_lieux_absences.php?action=modifier&amp;id_lieu=<?php echo $lieu->getId(); echo add_token_in_url();?>"><img src="../../images/icons/configure.png" title="Modifier" border="0" alt="" /></a></td>
          <td>
          <?php
          	if(($nb_saisies_traitements_avec_ce_lieu==0)||($_SESSION['statut']=="administrateur")) {
          ?>
              <a href="admin_lieux_absences.php?action=supprimer&amp;id_lieu=<?php echo $lieu->getId(); echo add_token_in_url();?>" onClick="return confirm('Etes-vous sûr de vouloir supprimer ce lieu ?')"><img src="../../images/icons/delete.png" width="22" height="22" title="Supprimer" border="0" alt="" /></a>
          <?php
          	}
          ?>
          </td>
          <td><a href="admin_lieux_absences.php?action=monter&amp;id_lieu=<?php echo $lieu->getId(); echo add_token_in_url();?>"><img src="../../images/up.png" width="22" height="22" title="monter" border="0" alt="" /></a></td>
          <td><a href="admin_lieux_absences.php?action=descendre&amp;id_lieu=<?php echo $lieu->getId(); echo add_token_in_url();?>"><img src="../../images/down.png" width="22" height="22" title="descendre" border="0" alt="" /></a></td>
<?php 
}
?>
        </tr>
     <?php } ?>
    </table>
    <br/><br/>
</div>

<?php
	echo check_sortable_rank_trouble('a_lieux', 'lieux');
	require("../../lib/footer.inc.php");
?>
