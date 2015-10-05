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

$mysqli = $GLOBALS["mysqli"];

if(!isset($mess)) {$mess="";}

// $is_posted = isset($_POST['is_posted']) ? $_POST['is_posted'] : (isset($is_posted) ? $is_posted : NULL);

$aid_id = isset($aid_id) ? $aid_id : "";
$mode = isset($mode) ? $mode : "";
$action = isset($action) ? $action : "";
$sous_groupe = isset($sous_groupe) ? $sous_groupe : "n";
$parent = isset($parent) ? $parent : "";

if (isset($is_posted) && $is_posted) {
	if ("y" == $sous_groupe) {
		$sql_parent = "INSERT INTO `aid_sous_groupes` (aid , parent) VALUES ('".$aid_id."','".$parent."')"
		   . "ON DUPLICATE KEY "
		   . "UPDATE parent='".$parent."' ;";
		$reg_parent = mysqli_query($mysqli, $sql_parent); 
		if (!$reg_parent) {
		   $mess = rawurlencode("Erreur lors de l'enregistrement des données.".$sql_parent);
		   header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
		   die();
		}
	}
	
	//  On regarde si une aid porte déjà le même nom
	$sql_test = "SELECT * FROM aid WHERE (nom='$aid_nom' and indice_aid='$indice_aid')";
	$test = mysqli_query($mysqli,$sql_test );
	$count = mysqli_num_rows($test);
	check_token();
	if (isset($is_posted) and ($is_posted =="1")) { // nouveau
		// On calcule le nouveau id pour l'aid à insérer → Plus gros id + 1
		$sql = "SELECT CAST( aid.id AS SIGNED INTEGER ) AS idAid FROM aid ORDER BY idAid DESC ";
		$result = mysqli_query($mysqli,$sql);
		$aid_id = $result->fetch_object()->idAid + 1;		
	} else {
		$count--;
	}
	$sql = "INSERT INTO aid "
	   . "SET id = '$aid_id', nom='$aid_nom', numero='$aid_num', indice_aid='$indice_aid', sous_groupe='$sous_groupe'"
	   . "ON DUPLICATE KEY "
	   . "UPDATE nom='$aid_nom', numero='$aid_num', sous_groupe='$sous_groupe'";
	$reg_data = mysqli_query($mysqli, $sql); 
	if (!$reg_data) {
	   $mess = rawurlencode("Erreur lors de l'enregistrement des données.".$sql);
	   header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
	   die();
	}
	if ($count == "1") {
		$msg=$msg." Attention, une AID portant le même nom existait déja !<br />";
	} else if ($count > 1) {
		$msg=$msg." Attention, plusieurs AID portant le même nom existaient déja !<br />";
	}
	if ($mode == "multiple") {
	   $msg .= "AID enregistrée !" ;
	   $mess = rawurlencode($msg);
	   header("Location: add_aid.php?action=add_aid&mode=multiple&msg=$mess&indice_aid=$indice_aid");
	   die();
	} else{
		$msg .= "AID enregistrée !";
		
		$mess = rawurlencode($msg);
		header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
		die();
	 }
} else {
	// on remplit tous les champs pour n'avoir qu'un affichage
	
	$id_aid_prec=-1;
	$id_aid_suiv=-1;
	$temoin_tmp=0;
	$aid_nom = "";		
	$aid_num = "";
	$nouveau = "Entrez un nom : ";
	$is_posted = (isset($action) && $action == "modif_aid") ? 2 : ((isset($action) && $action == "add_aid") ? 1 : "" );

	if ("modif_aid" == $action) {
		$sql="SELECT id FROM aid where indice_aid='$indice_aid' ORDER BY numero , nom";
		//echo "$sql<br />";
		$res_aid_tmp=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_aid_tmp)>0){
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
	}
	
	if ($action == "modif_aid") {
		$sql = "SELECT * FROM aid where (id = '".$aid_id."' and indice_aid='".$indice_aid."')";
		$calldata = mysqli_query($mysqli, $sql)->fetch_object();
		$aid_nom = $calldata->nom;	
		$aid_num = $calldata->numero;
		$sous_groupe = $calldata->sous_groupe;		
		$nouveau = "Entrez le nouveau nom à la place de l'ancien : ";
		$sous_groupe_de = NULL;
		if ('y' == $sous_groupe) {
			$sql = "SELECT parent FROM `aid_sous_groupes` WHERE `aid` LIKE '".$aid_id."'";
			//echo $sql.'<br />';
			$res_groupe_de=mysqli_query($mysqli, $sql);
			if ($res_groupe_de->num_rows) {
				$sous_groupe_de = $res_groupe_de->fetch_object()->parent;
			}
		}
		$sql2 = "SELECT `id` , `nom` , `numero` FROM `aid` WHERE `indice_aid`='".$indice_aid."' ORDER BY `nom` ASC ";
		$res_parents=mysqli_query($mysqli, $sql2);
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


// debug_var();
//**************** FIN EN-TETE *****************

if ($_SESSION['statut'] == 'professeur') {
	$retour = 'index2.php';
} else {
	$retour = 'index.php';
}

?>
<p class="bold">
	<a href="<?php echo $retour; ?>?indice_aid=<?php echo $indice_aid; ?>">
		<img src='../images/icons/back.png' alt='Retour' class='back_link'/>
		Retour
	</a>

<?php
	if ($action == "modif_aid") {


		if($id_aid_prec!=-1) {
?>
	|
	<a href='<?php echo $_SERVER['PHP_SELF']; ?>?action=modif_aid&amp;aid_id=<?php echo $id_aid_prec; ?>&amp;indice_aid=<?php echo $indice_aid; ?>' 
	   onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
		AID précédent
	</a>
<?php
		}
		if($id_aid_suiv!=-1) {
?>
	|
	<a href='<?php echo $_SERVER['PHP_SELF']; ?>?action=modif_aid&amp;aid_id=<?php echo $id_aid_suiv; ?>&amp;indice_aid=<?php echo $indice_aid; ?>'
	   onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
		AID suivant
	</a>
<?php
		}
	}
?>

</p>

<form enctype="multipart/form-data" action="add_aid.php" method="post">




    <p><?php echo $nouveau; ?></p>

	<?php
		echo add_token_field();
	?>

    <p>
		<label for="aidRegNom">
			Nom : 
		</label>
		<input type="text" 
			   id="aidRegNom" 
			   name="aid_nom" 
			   size="100" 
				<?php echo " value=\"".$aid_nom."\"";?>/>
	</p>
	<p>
		<label for="aidRegNum">
			Numéro (fac.) : 
		</label>
		<input type="text" id="aidRegNum" name="aid_num" size="4" <?php echo " value=\"".$aid_num."\""; ?> />
	</p>

	<p title="Pour affecter un parent, cochez la case, enregistrez puis revenez choisir le parent">
		<label for="sous_groupe">
			Sous-groupe d'un autre AID
		</label>
		<input type="checkbox"
			   name='sous_groupe'
			   id='sous_groupe'
			   value="y"
				<?php if ($sous_groupe=='y') {echo " checked='checked' ";} ?>  
			   onchange="afficher_cacher_parent('');"
			   />
	</p>
	
	<div id="aidParent">
		
<?php if(isset($res_parents) && $res_parents->num_rows){ ?>
	<select name="parent" id="choix_parent">
		<option value="" 
				<?php if (!$sous_groupe_de) {echo " selected='selected' ";} ?>
				>
			Aucun parent

		<?php while ($parent = $res_parents->fetch_object()){ ?>
		<option value="<?php echo $parent->id; ?>" 
				<?php if ($parent->id == $sous_groupe_de) {echo " selected='selected' ";} ?>
				>
			<?php echo $parent->nom; ?>
		</option>
		<?php } ?>
	</select>
<?php } ?>
		
	</div>
	
	<p class="center">
		<input type="hidden" name="indice_aid" value="<?php echo $indice_aid; ?>" />
		<input type="hidden" name="aid_id" value="<?php echo $aid_id; ?>" />
		<input type="hidden" name="mode" value="<?php echo $mode; ?>" />
		<input type="hidden" name="is_posted" value="<?php echo $is_posted; ?>" />
		<input type="submit" value="Enregistrer" />
	</p>
	
</form>

<script type='text/javascript'>
if(document.getElementById('aidRegNom')) {
	document.getElementById('aidRegNom').focus();
}
</script>

<script type='text/javascript'>
	function afficher_cacher(id)
{
    if(document.getElementById(id).style.visibility=="hidden")
    {
        document.getElementById(id).style.visibility="visible";
    }
    else
    {
        document.getElementById(id).style.visibility="hidden";
    }
    return true;
}

	function afficher_cacher_parent()
{
    if(document.getElementById('sous_groupe').checked)
    {
        document.getElementById('choix_parent').style.visibility="visible";
    }
    else
    {
        document.getElementById('choix_parent').style.visibility="hidden";
    }
    return true;
}

afficher_cacher_parent();
</script>

<?php 
require("../lib/footer.inc.php");