<?php
/*
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
require_once("../lib/initialisations.inc.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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

// Vérification du niveau de gestion des AIDs
if (NiveauGestionAid($_SESSION["login"],$indice_aid) < 5) {
    header("Location: ../logout.php?auto=1");
    die();
}

if(!isset($mess)) {$mess="";}

if (isset($is_posted) and ($is_posted =="1")) {
	check_token();

    //  On regarde si une aid porte déjà le même nom
    $test = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid WHERE (nom='$reg_nom' and indice_aid='$indice_aid')");
    $count = mysqli_num_rows($test);

    // On calcule le nouveau id pour l'aid à insérer.
    $call_id = mysqli_query($GLOBALS["mysqli"], "SELECT id FROM aid order by 'id'");
    $count_id = mysqli_num_rows($call_id);
    $i = 0;
    $new_id = 0;
    while ($i < $count_id) {
       $id = old_mysql_result($call_id, $i, 'id');
       while ($new_id <= $id) $new_id++;
       $i++;
    }

    // Vérification ultime avant d'enregistrer
    $test_id = mysqli_num_rows( mysqli_query($GLOBALS["mysqli"], "SELECT id FROM aid WHERE id = '$new_id'"));
    if ($test_id != 0) {
       $mess = rawurlencode("Erreur lors de l'enregistrement des données.");
       header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
       die();
    } else {
       $reg_data = mysqli_query($GLOBALS["mysqli"], "INSERT INTO aid SET id = '$new_id', nom='$reg_nom', numero='$reg_num', indice_aid='$indice_aid'");
       if (!$reg_data) {
          $mess = rawurlencode("Erreur lors de l'enregistrement des données.");
          header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
          die();
       } elseif ($mode == "unique") {
          $msg = "AID enregistrée !";
          if ($count == "1") {
              $msg=$msg." Attention, une AID portant le même nom existait déja !";
          } else if ($count > 1) {
              $msg=$msg." Attention, plusieurs AID portant le même nom existaient déja !";
          }
          $mess = rawurlencode($msg);
          header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
          die();
       } else if ($mode == "multiple") {
          $msg = "AID enregistrée !" ;
          $mess = rawurlencode($msg);
          header("Location: add_aid.php?action=add_aid&mode=multiple&msg=$mess&indice_aid=$indice_aid");
          die();
       }
    }
}
if (isset($is_posted) and ($is_posted =="2")) {
	check_token();
// On vérifie d'abord que le nom n'est pas déjà utilisé :
    $test = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid WHERE (nom='$reg_nom' and indice_aid='$indice_aid')");
    $count = mysqli_num_rows($test);
    $flag = 0;
    if ($count != "0") {
        $aid_id_test = old_mysql_result($test, 0, "id");
        if ($aid_id_test != $aid_id) {$flag = 1;}
    }
    $reg_data = mysqli_query($GLOBALS["mysqli"], "UPDATE aid SET nom='$reg_nom', numero='$reg_num' WHERE (id = '$aid_id' and indice_aid='$indice_aid')");
    if (!$reg_data) {
        $msg = "Erreur lors de l'enregistrement des données";
    } else {
        $msg = "AID enregistrée !" ;
        if ($flag == "1") {
            $msg=$msg." Attention, une AID portant le même nom existait déja !";
        } else if ($count > 1) {
            $msg=$msg." Attention, plusieurs AID portant le même nom existaient déja !";
        }
        $mess = rawurlencode($msg);

        header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
        die();
    }
}

//**************** EN-TETE *********************
if ($action == "modif_aid") {
	$titre_page = "Gestion des AID | Modifier Une AID";
}
else {
	$titre_page = "Gestion des AID | Ajouter Une AID";
}
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

if ($_SESSION['statut'] == 'professeur') {
	$retour = 'index2.php';
} else {
	$retour = 'index.php';
}
?>
<p class="bold">
	
<a href="<?php echo $retour; ?>?indice_aid=<?php echo $indice_aid; ?>"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>

<?php
	if ($action == "modif_aid") {
		//$calldata = mysql_query("SELECT * FROM aid where (id = '$aid_id' and indice_aid='$indice_aid')");
		//$aid_nom = old_mysql_result($calldata, 0, "nom");
		//$aid_num = old_mysql_result($calldata, 0, "numero");

		$sql="SELECT id FROM aid where indice_aid='$indice_aid' ORDER BY id";
		//echo "$sql<br />";
		$res_aid_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_aid_tmp)>0){
			$id_aid_prec=-1;
			$id_aid_suiv=-1;
			$temoin_tmp=0;
			while($lig_aid_tmp=mysqli_fetch_object($res_aid_tmp)){
				if($lig_aid_tmp->id==$aid_id){
					$temoin_tmp=1;
					if($lig_aid_tmp=mysqli_fetch_object($res_aid_tmp)){
						$id_aid_suiv=$lig_aid_tmp->id;
					}
					else{
						$id_aid_suiv=-1;
					}
				}
				if($temoin_tmp==0){
					$id_aid_prec=$lig_aid_tmp->id;
				}
			}
		}

		if($id_aid_prec!=-1) {
			echo " | <a href='".$_SERVER['PHP_SELF']."?action=modif_aid&amp;aid_id=$id_aid_prec&amp;indice_aid=$indice_aid' onclick=\"return confirm_abandon (this, change, '$themessage')\">AID précédent</a>";
		}
		if($id_aid_suiv!=-1) {
			echo " | <a href='".$_SERVER['PHP_SELF']."?action=modif_aid&amp;aid_id=$id_aid_suiv&amp;indice_aid=$indice_aid' onclick=\"return confirm_abandon (this, change, '$themessage')\">AID suivant</a>";
		}
	}
?>

</p>

<?php if ($action == "add_aid") { ?>

    <form enctype="multipart/form-data" action="add_aid.php" method="post">

	<?php
		echo add_token_field();
	?>
    <div class='norme'>

    <p><label for="aidRegNom">Nom : <input type="text" id="aidRegNom" name="reg_nom" size="100" <?php if (isset($reg_nom)) { echo "value=\"".$reg_nom."\"";}?> /></label></p>

    <p><label for="aidRegNum">Numéro (fac.) : <input type="text" id="aidRegNom" name="reg_num" size="4" <?php if (isset($reg_num)) { echo "value=\"".$reg_num."\"";}?> /></label></p>

    </div>

    <input type="hidden" name="is_posted" value="1" />

    <input type="hidden" name="indice_aid" value="<?php echo $indice_aid;?>" />

    <input type="hidden" name="mode" value="<?php echo $mode;?>" />

    <input type="submit" value="Enregistrer" />

    </form>

<?php }



if ($action == "modif_aid") { ?>

    <p>Entrez le nouveau nom à la place de l'ancien : </p>

    <form enctype="multipart/form-data" action="add_aid.php" method="post">
	<?php
		echo add_token_field();

		$calldata = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid where (id = '$aid_id' and indice_aid='$indice_aid')");

		$aid_nom = old_mysql_result($calldata, 0, "nom");
		
		$aid_num = old_mysql_result($calldata, 0, "numero");
	?>



    <p><label for="aidRegNom">Nom : <input type="text" id="aidRegNom" name="reg_nom" size="100" <?php echo "value=\"".$aid_nom."\"";?> /></label></p>

    <p><label for="aidRegNum">Numéro (fac.) : <input type="text" id="aidRegNum" name="reg_num" size="4" <?php echo "value=\"".$aid_num."\"";?> /></label></p>

    <input type="hidden" name="is_posted" value="2" />

    <input type="hidden" name="indice_aid" value="<?php echo $indice_aid;?>" />

    <input type="hidden" name="aid_id" value="<?php echo $aid_id; ?>" />

    <input type="submit" value="Enregistrer" />

    </form>

<?php }

echo "
<script type='text/javascript'>
if(document.getElementById('aidRegNom')) {
	document.getElementById('aidRegNom').focus();
}
</script>";

require("../lib/footer.inc.php");
?>
