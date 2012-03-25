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
 * the Free Software Foundation; either version 3 of the License, or
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

$niveau_arbo = 1;
// Initialisations files
include("../lib/initialisationsPropel.inc.php");
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

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if (empty($_GET['action']) and empty($_POST['action'])) { $action="";}
    else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
if (empty($_GET['id']) and empty($_POST['id'])) { $id="";}
    else { if (isset($_GET['id'])) {$id=$_GET['id'];} if (isset($_POST['id'])) {$id=$_POST['id'];} }
if (empty($_GET['EXT_ID']) and empty($_POST['EXT_ID'])) { $EXT_ID="";}
    else { if (isset($_GET['EXT_ID'])) {$EXT_ID=$_GET['EXT_ID'];} if (isset($_POST['EXT_ID'])) {$EXT_ID=$_POST['EXT_ID'];} }
if (empty($_GET['LIBELLE_COURT']) and empty($_POST['LIBELLE_COURT'])) { $LIBELLE_COURT="";}
    else { if (isset($_GET['LIBELLE_COURT'])) {$LIBELLE_COURT=$_GET['LIBELLE_COURT'];} if (isset($_POST['LIBELLE_COURT'])) {$LIBELLE_COURT=$_POST['LIBELLE_COURT'];} }
if (empty($_GET['LIBELLE_LONG']) and empty($_POST['LIBELLE_LONG'])) { $LIBELLE_LONG="";}
    else { if (isset($_GET['LIBELLE_LONG'])) {$LIBELLE_LONG=$_GET['LIBELLE_LONG'];} if (isset($_POST['LIBELLE_LONG'])) {$LIBELLE_LONG=$_POST['LIBELLE_LONG'];} }
if (empty($_GET['LIBELLE_EDITION']) and empty($_POST['LIBELLE_EDITION'])) { $LIBELLE_EDITION="";}
    else { if (isset($_GET['LIBELLE_EDITION'])) {$LIBELLE_EDITION=$_GET['LIBELLE_EDITION'];} if (isset($_POST['LIBELLE_EDITION'])) {$LIBELLE_EDITION=$_POST['LIBELLE_EDITION'];} }

$mef = MefQuery::create()->findPk($id);
if ($action == 'supprimer') {
	check_token();
    if ($mef != null) {
	$mef->delete();
    }
} elseif ($action == 'ajouterdefaut') {
	check_token();
    ajoutMefParDefaut();
} else {
    if ($EXT_ID != '') {
		check_token();
		if ($mef == null) {
			$mef = new Mef();
		}
		$mef->setMefCode(stripslashes($EXT_ID));
		$mef->setLibelleCourt(stripslashes($LIBELLE_COURT));
		$mef->setLibelleLong(stripslashes($LIBELLE_LONG));
		$mef->setLibelleEdition(stripslashes($LIBELLE_EDITION));
		$mef->save();
    }
}

// header
$titre_page = "Gestion des mef (module élémentaire de formation)";
require_once("../lib/header.inc.php");

echo "<p class='bold'>";
echo "<a href=\"../accueil_admin.php\">";
echo "<img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | ";
echo "<a href=\"associer_eleve_mef.php\">associer les élèves au mef</a>";
echo "</p>";
?>

<div style="text-align:center">
    <h2>Définition des mef</h2>
<?php if ($action == "ajouter" OR $action == "modifier") { ?>
<div style="text-align:center">
    <?php
    	if($action=="ajouter") { 
	    echo "<h2>Ajout d'un mef</h2>";
	} elseif ($action=="modifier") {
	    echo "<h2>Modifier un mef</h2>";
	}
	?>

    <form action="admin_mef.php" method="post" id="form2">
    	<p>
<?php
echo add_token_field();
?>
    	</p>
      <table cellpadding="2" cellspacing="2" class="menu">
        <tr>
          <td>Id extérieur (nomenclature EN)</td>
          <td>Libellé court</td>
          <td>Libellé long</td>
          <td>Libellé d'édition</td>
       </tr>
        <tr>
              <td>
           <?php
	   if ($mef != null) { ?>
	      <input name="id" type="hidden" id="id" value="<?php echo $id ?>" />
	   <?php } ?>
              	<input name="EXT_ID" type="text" size="14" maxlength="50" value="<?php  if ($mef != null) {echo $mef->getMefCode();} ?>" />
              </td>
              <td><input name="LIBELLE_COURT" type="text" size="14" maxlength="50" value="<?php  if ($mef != null) {echo $mef->getLibelleCourt();} ?>" /></td>
              <td><input name="LIBELLE_LONG" type="text" size="14" maxlength="50" value="<?php  if ($mef != null) {echo $mef->getLibelleLong();} ?>" /></td>
              <td><input name="LIBELLE_EDITION" type="text" size="14" maxlength="50" value="<?php  if ($mef != null) {echo $mef->getLibelleEdition();} ?>" /></td>
        </tr>
      </table>
     <p><input type="submit" name="Submit" value="Enregistrer" /></p>
    </form>
<br/><br/>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php
} ?>
	<a href="admin_mef.php?action=ajouter"><img src='../images/icons/add.png' alt='' class='back_link' /> Ajouter les mef</a>
	<br/><br/>
	<a href="admin_mef.php?action=ajouterdefaut<?php echo add_token_in_url();?>"><img src='../images/icons/add.png' alt='' class='back_link' /> Ajouter les mef par défaut</a>
	<br/><br/>
    <table cellpadding="0" cellspacing="1" class="menu">
      <tr>
        <td>Id</td>
        <td>Numéro mef nomenclature EN</td>
        <td>Libelle Court</td>
        <td>Libelle Long</td>
        <td>Libelle Edition</td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
     </tr>
    <?php
    $mef_collection = new PropelCollection();
    $mef_collection = MefQuery::create()->find();
   foreach ($mef_collection as $mef) {
 ?>
        <tr>
	  <td><?php echo $mef->getId(); ?></td>
          <td><?php echo $mef->getMefCode(); ?></td>
          <td><?php echo $mef->getLibelleCourt(); ?></td>
          <td><?php echo $mef->getLibelleLong(); ?></td>
          <td><?php echo $mef->getLibelleEdition(); ?></td>
          <td><a href="admin_mef.php?action=modifier&amp;id=<?php echo $mef->getId(); echo add_token_in_url();?>"><img src="../images/icons/configure.png" title="Modifier" alt="" /></a></td>
          <td><a href="admin_mef.php?action=supprimer&amp;id=<?php echo $mef->getId(); echo add_token_in_url();?>" onclick="return confirm('Etes-vous sûr de vouloir supprimer ce mef ?')"><img src="../images/icons/delete.png" width="22" height="22" title="Supprimer" alt="" /></a></td>
       </tr>
     <?php } ?>
    </table>
    <br/><br/>
</div>


<?php require("../lib/footer.inc.php");

function ajoutMefParDefaut() {
    $mef = new Mef();
    $mef->setMefCode("1031000111");
    $mef->setLibelleCourt("3G");
    $mef->setLibelleLong("3EME");
    $mef->setLibelleEdition("3eme");
    if (MefQuery::create()->filterByMefCode($mef->getMefCode())->find()->isEmpty()) {
	$mef->save();
    }

    $mef = new Mef();
    $mef->setMefCode("1021000111");
    $mef->setLibelleCourt("4G");
    $mef->setLibelleLong("4EME");
    $mef->setLibelleEdition("4eme");
    if (MefQuery::create()->filterByMefCode($mef->getMefCode())->find()->isEmpty()) {
	$mef->save();
    }

    $mef = new Mef();
    $mef->setMefCode("1011000111");
    $mef->setLibelleCourt("5G");
    $mef->setLibelleLong("5EME");
    $mef->setLibelleEdition("5eme");
    if (MefQuery::create()->filterByMefCode($mef->getMefCode())->find()->isEmpty()) {
	$mef->save();
    }

    $mef = new Mef();
    $mef->setMefCode("1001000111");
    $mef->setLibelleCourt("6G");
    $mef->setLibelleLong("6EME");
    $mef->setLibelleEdition("6eme");
    if (MefQuery::create()->filterByMefCode($mef->getMefCode())->find()->isEmpty()) {
	$mef->save();
    }

}
