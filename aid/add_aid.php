<?php
/*
 * Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Régis Bouguin
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

include_once 'fonctions_aid.php';
$mysqli = $GLOBALS["mysqli"];
$javascript_specifique = "aid/aid_ajax";

if(!isset($mess)) {$mess="";}

// $is_posted = isset($_POST['is_posted']) ? $_POST['is_posted'] : (isset($is_posted) ? $is_posted : NULL);

$aid_id = isset($aid_id) ? $aid_id : "";
$mode = isset($mode) ? $mode : "";
$action = isset($action) ? $action : "";
$sous_groupe = isset($sous_groupe) ? $sous_groupe : "n";
$parent = isset($parent) ? $parent : "";
$sous_groupe_de =isset($sous_groupe_de) ? $sous_groupe_de : NULL;
$inscrit_direct =isset($inscrit_direct) ? $inscrit_direct : NULL;

if (isset($is_posted) && $is_posted) {
	if ("n" == $sous_groupe) {
		Efface_sous_groupe($aid_id);
		//die($aid_id);
	}
	if ("y" == $sous_groupe || $sous_groupe_de != NULL) {
		$reg_parent = Sauve_sous_groupe($aid_id, $parent);
		if (!$reg_parent) {
		   $mess = rawurlencode("Erreur lors de l'enregistrement des données.");
		   header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
		   die();
		}
	}
	
	//  On regarde si une aid porte déjà le même nom
	$count = mysqli_num_rows(Extrait_aid_sur_nom($aid_nom , $indice_aid));
	check_token();
	if (isset($is_posted) and ($is_posted =="1")) { // nouveau
		// On calcule le nouveau id pour l'aid à insérer → Plus gros id + 1
		$aid_id = Dernier_id ($ordre = "DESC") + 1;	
	} else {
		$count--;
	}
//if ($inscrit_direct) die ($inscrit_direct);
	$reg_data = Sauve_definition_aid ($aid_id , $aid_nom , $aid_num , $indice_aid , $sous_groupe , $inscrit_direct);
	if (!$reg_data) {
	   $mess = rawurlencode("Erreur lors de l'enregistrement des données.");
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
		$res_aid_tmp = Extrait_aid_sur_indice_aid ($indice_aid);
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
	$res_parents=Extrait_aid_sur_indice_aid ($indice_aid);
	
	if ($action == "modif_aid") {
		$calldata = Extrait_aid_sur_id ($aid_id, $indice_aid)->fetch_object();
		$aid_nom = $calldata->nom;	
		$aid_num = $calldata->numero;
		$sous_groupe = $calldata->sous_groupe;		
		$nouveau = "Entrez le nouveau nom à la place de l'ancien : ";
		if ('y' == $sous_groupe) {
			$res_groupe_de=Extrait_parent ($aid_id);
			if ($res_groupe_de->num_rows) {
				$sous_groupe_de = $res_groupe_de->fetch_object()->parent;
			}
		}
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

	<div <?php if (!Multiples_possible ($indice_aid)) {echo " style='display:none;'";} ?> >
		<p title="Cochez pour affecter un parent puis choisissez le parent">
			<label for="sous_groupe">
				Sous-groupe d'une autre AID
			</label>
			<input type="checkbox"
				   name='sous_groupe'
				   id='sous_groupe'
				   value="y"
					<?php if ($sous_groupe=='y') {echo " checked='checked' ";} ?>  
				   onchange="afficher_cacher_parent();"
				   />
		</p>

		<div id="aidParent" >
		
<?php if((isset($res_parents) && $res_parents->num_rows)){ ?>
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
		
		<p>
			<label for="inscrit_direct">
				Un élève peut s'inscrire directement
			</label>
			<input type="checkbox"
				   name='inscrit_direct'
				   id='inscrit_direct'
				   value="y"
					<?php if (eleve_inscrit_direct($aid_id, $indice_aid)) {echo " checked='checked' ";} ?>
				   />
		</p>
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