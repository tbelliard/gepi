<?php
/*
 * Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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
// ========== Iniialisation des variables ==========
$reg_nom = isset($_POST["reg_nom"]) ? $_POST["reg_nom"] : NULL;
$reg_nom_complet = isset($_POST["reg_nom_complet"]) ? $_POST["reg_nom_complet"] : NULL;
$note_max = isset($_POST["note_max"]) ? $_POST["note_max"] : NULL;
$display_begin = isset($_POST["display_begin"]) ? $_POST["display_begin"] : NULL;
$display_end = isset($_POST["display_end"]) ? $_POST["display_end"] : NULL;
$type_note = isset($_POST["type_note"]) ? $_POST["type_note"] : NULL;
$type_aid = isset($_POST["type_aid"]) ? $_POST["type_aid"] : NULL;
$order_display1 = isset($_POST["order_display1"]) ? $_POST["order_display1"] : NULL;
$order_display2 = isset($_POST["order_display2"]) ? $_POST["order_display2"] : NULL;
$message = isset($_POST["message"]) ? $_POST["message"] : NULL;
$display_nom = isset($_POST["display_nom"]) ? $_POST["display_nom"] : NULL;
$indice_aid = isset($_POST["indice_aid"]) ? $_POST["indice_aid"] : (isset($_GET["indice_aid"]) ? $_GET["indice_aid"] : NULL);
$display_bulletin = isset($_POST["display_bulletin"]) ? $_POST["display_bulletin"] : 'n';
$autoriser_inscript_multiples = isset($_POST["autoriser_inscript_multiples"]) ? $_POST["autoriser_inscript_multiples"] : 'n';
$bull_simplifie = isset($_POST["bull_simplifie"]) ? $_POST["bull_simplifie"] : 'n';
$activer_outils_comp = isset($_POST["activer_outils_comp"]) ? $_POST["activer_outils_comp"] : 'n';
$feuille_presence = isset($_POST["feuille_presence"]) ? $_POST["feuille_presence"] : 'n';
$is_posted = isset($_POST["is_posted"]) ? $_POST["is_posted"] : NULL;

if((isset($_GET['mode']))&&($_GET['mode']=="ajout")) {
	unset($_SESSION["indice_aid"]);
}
else {
	$indice_aid = isset($indice_aid) ? $indice_aid : (isset($_SESSION["indice_aid"]) ? $_SESSION["indice_aid"] : NULL);
	$_SESSION["indice_aid"] = $indice_aid;
}

if((!isset($indice_aid))&&(isset($_GET['aid_id']))) {
	$sql="SELECT indice_aid FROM aid WHERE id='".$_GET['aid_id']."';";
	$res_aid=mysqli_query($GLOBALS["mysqli"],$sql);
	if(mysqli_num_rows($res_aid)>0) {
		$lig_aid=mysqli_fetch_object($res_aid);
		$indice_aid=$lig_aid->indice_aid;
	}
}
//debug_var();

$mysqli = $GLOBALS["mysqli"];
// ========== fin initialisation ===================

if (isset($is_posted) and ($is_posted == "1")) {
  check_token();
  $msg_inter = "";
  if ($autoriser_inscript_multiples != 'y') {
    $test = sql_query1("select count(login) c from j_aid_eleves where indice_aid='".$indice_aid."' group by login order by c desc limit 1");
    if ($test > 1) {
      $msg_inter = "Actuellement, un ou plusieurs élèves sont inscrits dans plusieurs AID à la fois.
      Impossible donc de supprimer l'autorisation d'inscrire un &eacute;l&egrave;ve &agrave; plusieurs AID d'une m&ecirc;me cat&eacute;gorie.";
      $autoriser_inscript_multiples = 'y';
    }
  }
	if ($display_end < $display_begin) {$display_end = $display_begin;}
	$del = mysqli_query($GLOBALS["mysqli"], "DELETE FROM aid_config WHERE indice_aid = '".$indice_aid."'");
	echo "<!-- DELETE FROM aid_config WHERE indice_aid = '".$indice_aid."' -->";

	if($reg_nom=="") {
		if($reg_nom_complet!="") {
			$reg_nom=$reg_nom_complet;
		}
		else {
			$reg_nom="Nom AID vide ".microtime();
			$reg_nom_complet=$reg_nom;
		}
	}

	if($type_aid!=0) {
		$activer_outils_comp="y";
	}

	$reg_data = mysqli_query($GLOBALS["mysqli"], "INSERT INTO aid_config SET
			nom='".$reg_nom."',
			nom_complet='".$reg_nom_complet."',
			note_max='".$note_max."',
			display_begin='".$display_begin."',
			display_end='".$display_end."',
			type_note='".$type_note."',
			type_aid='".$type_aid."',
			order_display1 = '".$order_display1."',
			order_display2 = '".$order_display2."',
			message ='".$message."',
			display_nom='".$display_nom."',
			indice_aid='".$indice_aid."',
			display_bulletin='".$display_bulletin."',
			autoriser_inscript_multiples='".$autoriser_inscript_multiples."',
			bull_simplifie = '".$bull_simplifie."',
			feuille_presence = '".$feuille_presence."',
			outils_complementaires = '".$activer_outils_comp."'");
	  if (!$reg_data)
		  $msg_inter .= "Erreur lors de l'enregistrement des données !<br />";

		// Suppression de professeurs dans le cas des outils complémentaires
		$call_profs = mysqli_query($GLOBALS["mysqli"], "SELECT id_utilisateur FROM j_aidcateg_utilisateurs WHERE (indice_aid='$indice_aid')");
		$nb_profs = mysqli_num_rows($call_profs);
		$i = 0;
		// while($i < $nb_profs) {
		while ($aid_prof = mysqli_fetch_object($call_profs)) {
		    // $login_prof = old_mysql_result($call_profs,$i);
		    $login_prof = $aid_prof->id_utilisateur;
		    if (isset($_POST["delete_".$login_prof])) {
		       $reg_data = mysqli_query($GLOBALS["mysqli"], "delete from j_aidcateg_utilisateurs WHERE (id_utilisateur = '$login_prof' and indice_aid='$indice_aid')");
			   if (!$reg_data) $msg_inter .= "Erreur lors de la suppression du professeur ".$login_prof." !<br />";
		    }
		    $i++;
		} // while
    if (isset($_POST["reg_prof_login"]) and ($_POST["reg_prof_login"] !="")) {
        // On commence par vérifier que le professeur n'est pas déjà présent dans cette liste.
        $test = sql_query1("SELECT count(id_utilisateur) FROM j_aidcateg_utilisateurs WHERE (id_utilisateur = '".$_POST["reg_prof_login"]."' and indice_aid='$indice_aid')");
        if ($test != "0") {
            $msg = "Le professeur que vous avez tenté d'ajouter appartient déjà à cet AID";
        } else {
            $reg_data = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_aidcateg_utilisateurs SET id_utilisateur= '".$_POST["reg_prof_login"]."', indice_aid='".$indice_aid."'");
            if (!$reg_data) $msg_inter .= "Erreur lors de l'ajout du professeur !<br />";
        }
    }
		// Suppression de "super-gestionaires"
		$call_profs = mysqli_query($GLOBALS["mysqli"], "SELECT id_utilisateur FROM j_aidcateg_super_gestionnaires WHERE (indice_aid='$indice_aid')");
		$nb_profs = mysqli_num_rows($call_profs);
		$i = 0;
		// while($i < $nb_profs) {
		while ($aid_prof = mysqli_fetch_object($call_profs)) {
		    // $login_gestionnaire = old_mysql_result($call_profs,$i);
		    $login_gestionnaire = $aid_prof->id_utilisateur;
		    if (isset($_POST["delete_gestionnaire_".$login_gestionnaire])) {
		        $reg_data = mysqli_query($GLOBALS["mysqli"], "delete from j_aidcateg_super_gestionnaires WHERE (id_utilisateur = '$login_gestionnaire' and indice_aid='$indice_aid')");
            if (!$reg_data) $msg_inter .= "Erreur lors de la suppression du professeur $login_gestionnaire!<br />";
		    }
		    $i++;
		} // while


    if (isset($_POST["reg_gestionnaire_login"]) and ($_POST["reg_gestionnaire_login"] !="")) {
        // On commence par vérifier que le professeur n'est pas déjà présent dans cette liste.
        $test = sql_query1("SELECT count(id_utilisateur) FROM j_aidcateg_super_gestionnaires WHERE (id_utilisateur = '".$_POST["reg_gestionnaire_login"]."' and indice_aid='$indice_aid')");
        if ($test != "0") {
            $msg = "Le professeur que vous avez tenté d'ajouter appartient déjà à cet AID";
        } else {
            $reg_data = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_aidcateg_super_gestionnaires SET id_utilisateur= '".$_POST["reg_gestionnaire_login"]."', indice_aid='".$indice_aid."'");
            if (!$reg_data) $msg_inter .= "Erreur lors de l'ajout du professeur !<br />";
        }
    }


    if ($msg_inter !="") {
        $msg = $msg_inter;
    } else {
        $msg = "Enregistrement réussi !";
    }
}

$themessage = 'Des modifications ont été effectuées. Voulez-vous vraiment quitter sans enregistrer ?';

//**************** EN-TETE *********************
$titre_page = "Gestion des AID";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
// echo "Indice ".$indice_aid;

$NiveauGestionAid_categorie=NiveauGestionAid($_SESSION["login"],$indice_aid);
?>

<script type="text/javascript" language="javascript">
var errorMsg0  = 'Le formulaire est incomplet !';
var errorMsg1  = ' veuillez rentrer un nombre ! ';
var errorMsg2  = ' : cette valeur n\'est pas autorisée ! ';
function mise_a_zero() {
    window.document.formulaire.note_max.value = '';
}

function verif_type_note() {
    if (window.document.formulaire.type_note[2].checked == true) {
        window.document.formulaire.note_max.value = '';
    }
    if (window.document.formulaire.type_note[2].checked != true && window.document.formulaire.note_max.value == '')
        {
            window.document.formulaire.note_max.value = '20';
        }
}

//=================================
// AJOUT: boireaus
function emptyFormElements(formulaire,champ){
	//eval("document.forms['"+formulaire+"']."+champ+".value=''");
	// J'ai viré la ligne parce qu'elle vide le champ avant que la valeur soit transmise
	// et du coup on insère dans la table des noms vides.
	return true;
}

function checkFormElementInRange(formulaire,champ,vmin,vmax){
	eval("vchamp=document.forms['"+formulaire+"']."+champ+".value");
	chaine_reg=new RegExp('[0-9]+');
	if((vchamp<0)||(vchamp>100)||(vchamp.replace(chaine_reg,'')).length!=0){
		alert("La valeur du champ "+champ+" ("+vchamp+") n'est pas comprise entre 0 et 100.");
		return false;
	}
	else{
		return true;
	}
}
//=================================

	// Initialisation
	change='no';

	function confirm_changement_aid(thechange, themessage)
	{
		if(document.getElementById('aid_id_tel_aid').selectedIndex!=0) {
			if (!(thechange)) thechange='no';
			if (thechange != 'yes') {
				document.form_tel_aid.submit();
			}
			else{
				var is_confirmed = confirm(themessage);
				if(is_confirmed){
					document.form_tel_aid.submit();
				}
				else {
					document.getElementById('aid_id_tel_aid').selectedIndex=0;
				}
			}
		}
	}

</script>

<?php
if (isset($indice_aid)) {
    $call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
	$obj_data = $call_data->fetch_object();
	$reg_nom = $obj_data->nom;
    $reg_nom_complet = $obj_data->nom_complet;
    $note_max = $obj_data->note_max;
    $order_display1 = $obj_data->order_display1;
    $order_display2 = $obj_data->order_display2;
    $type_note = $obj_data->type_note;
    $type_aid = $obj_data->type_aid;
    $display_begin = $obj_data->display_begin;
    $display_end = $obj_data->display_end;
    $message = $obj_data->message;
    $display_nom = $obj_data->display_nom;
    $display_bulletin = $obj_data->display_bulletin;
    $autoriser_inscript_multiples = $obj_data->autoriser_inscript_multiples;
    $bull_simplifie = $obj_data->bull_simplifie;
    $activer_outils_comp = $obj_data->outils_complementaires;
    $feuille_presence = $obj_data->feuille_presence;
	
    // Compatibilité avec version
    if ($display_bulletin=='')  $display_bulletin = "y";
    if ($autoriser_inscript_multiples=='')  $autoriser_inscript_multiples = "n";
} else {
    $call_data = mysqli_query($GLOBALS["mysqli"], "SELECT max(indice_aid) max FROM aid_config");
	$obj_data = $call_data->fetch_object();
    // $indice_aid = @old_mysql_result($call_data, 0, "max");
    $indice_aid = $obj_data->max;
    $indice_aid++;
    $note_max = 20;
    $display_begin = '';
    $display_end = '';
    $display_nom = '';
    $message = '';
    $order_display1 = '';
    $order_display2 = '';
    $type_note = "every";
    $type_aid = 0;
    $display_bulletin = "y";
    $autoriser_inscript_multiples = "n";
    $bull_simplifie = "y";
    $activer_outils_comp = "n";
    $feuille_presence = "n";
}
?>

<div class='norme'>
<!--form enctype="multipart/form-data" name="form_tel_aid" action="add_aid.php" method="post"-->

<?php
	// Modifier le form action selon que les fiches projet sont activées ou non,...
	//	add_aid.php?action=modif_aid&aid_id=39&indice_aid=5
	//	modif_fiches.php?aid_id=2&indice_aid=4&action=modif&retour=index2.php
	//	modify_aid.php?flag=eleve&aid_id=2&indice_aid=4

//echo add_token_field();
?>
	<div class="bold" style='float:left'>
		<!--input type="hidden" name="indice_aid" value="<?php echo $indice_aid ?>" /-->
		<a href="index.php" onclick="return confirm_abandon (this, change, '<?php echo $themessage;?>')"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour</a>
		<?php
			$sql="SELECT * FROM aid WHERE indice_aid='".$indice_aid."';";
			$res_aid_cat=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_aid_cat)>0) {
				echo "
		 | <a href='index2.php?indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accéder à la liste des (".mysqli_num_rows($res_aid_cat).") AID de la catégorie</a>";
			}


			// On affiche un select avec la liste des aid de cette catégorie
			if ($NiveauGestionAid_categorie>=5) {
				// Normalement dans la page de config Catégorie, on a NiveauGestionAid à 10
				$sql = "SELECT id, nom FROM aid WHERE indice_aid = '".$indice_aid."' ORDER BY numero, nom";
			}
			else if ($NiveauGestionAid_categorie>=1) {
				$sql = "SELECT a.id, a.nom FROM aid a, j_aid_utilisateurs_gest j WHERE a.indice_aid = '".$indice_aid."' and j.id_utilisateur = '" . $_SESSION["login"] . "' and j.indice_aid = '".$indice_aid."' and  a.id=j.id_aid ORDER BY a.numero, a.nom";
			}
			$query = mysqli_query($GLOBALS["mysqli"], $sql) OR DIE('Erreur dans la requête select * from aid : '.mysqli_error($GLOBALS["mysqli"]));

	echo "</div>";

			if(mysqli_num_rows($query)>0) {
				echo "
		<form enctype='multipart/form-data' name='form_tel_aid' action='add_aid.php' method='post' style='float:left; margin-left:1em;'>
			".add_token_field()."

			<input type='hidden' name='indice_aid' value='".$indice_aid."' />
			<input type='hidden' name='action' value='modif_aid' />
			<select name='aid_id' id='aid_id_tel_aid' onchange=\"confirm_changement_aid(change, '$themessage');\">
				<option value=''>Accéder à un AID particulier</option>";
					while($lig_aid=mysqli_fetch_object($query)) {
						echo "
				<option value='".$lig_aid->id."'>".$lig_aid->nom."</option>";
					}
					echo "
			</select>
		</form>";
			}

			$sql="SELECT * FROM aid_config ORDER BY nom, nom_complet;";
			$query = mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($query)>1) {
				echo "
		<form enctype='multipart/form-data' name='form_telle_categorie_aid' action='".$_SERVER["PHP_SELF"]."' method='post' style='float:left; margin-left:1em;'>
			".add_token_field()."

			<input type='hidden' name='action' value='modif_aid' />
			 | Catégorie 
			<select name='indice_aid' id='form_chgt_categorie_indice_aid' onchange=\"confirm_changement_categorie_aid(change, '$themessage');\">
				<!--option value=''>Accéder à une autre catégorie AID</option-->";
					$indice_form_chgt_aid=-1;
					$cpt_cat_aid=0;
					while($lig_cat_aid=mysqli_fetch_object($query)) {
						$selected="";
						if($lig_cat_aid->indice_aid==$indice_aid) {
							$selected=" selected='true'";
							$indice_form_chgt_aid=$cpt_cat_aid;
						}
						echo "
				<option value='".$lig_cat_aid->indice_aid."'".$selected.">".$lig_cat_aid->nom." (".$lig_cat_aid->nom_complet.")</option>";
						$cpt_cat_aid++;
					}
					echo "
			</select>
		</form>";
			}

			echo "<div style='clear:both;'></div>";
		?>

<!--/form-->
</div>

<script type='text/javascript'>
	function confirm_changement_categorie_aid(thechange, themessage)
	{
		if(document.getElementById('form_chgt_categorie_indice_aid').selectedIndex!=0) {
			if (!(thechange)) thechange='no';
			if (thechange != 'yes') {
				document.form_telle_categorie_aid.submit();
			}
			else{
				var is_confirmed = confirm(themessage);
				if(is_confirmed){
					document.form_telle_categorie_aid.submit();
				}
				else {
					document.getElementById('form_chgt_categorie_indice_aid').selectedIndex=<?php echo $indice_form_chgt_aid;?>;
				}
			}
		}
	}
</script>

<form enctype="multipart/form-data" name="formulaire" action="config_aid.php" method="post" onsubmit="return (emptyFormElements('formulaire', 'reg_nom_complet') &amp;&amp; (emptyFormElements('formulaire', 'reg_nom')) &amp;&amp; checkFormElementInRange('formulaire', 'order_display2', 0, 100))">
	<input type="hidden" name="indice_aid" value="<?php echo $indice_aid ?>" />

<?php
echo add_token_field();
?>
<div class='norme'>
	<p style='margin-top:1em;'>
		<strong>Configuration de la catégorie d'AID <em>(Activités Inter-Disciplinaires)</em>&nbsp;:</strong>
		<hr />
		Choisissez le nom complet de la catégorie d'AID <em>(par exemple Travaux Personnels Encadrés)</em>&nbsp;:
		<br />
		Nom complet : 
		<input type="text" 
			   name="reg_nom_complet" 
			   id="reg_nom_complet" 
			   size="40"
			   onchange="changement();"
				   <?php if (isset($reg_nom_complet)) { echo "value=\"".$reg_nom_complet."\"";}
				   ?> 
			   />
		<br /><br />
		Choisissez le nom abrégé de la catégorie d'AID <em>(par exemple T.P.E.)</em>&nbsp;:
		<br />
		Nom : 
		<input type="text" 
			   name="reg_nom" 
			   size="20" 
			   onchange="changement();"
				   <?php if (isset($reg_nom)) { echo "value=\"".$reg_nom."\"";}?> 
			   />
		<hr />
		Type de notation :
		<br />
		<input type="radio" name="type_note" id="type_note_every" value="every" onchange="changement();" 
			<?php if (($type_note == "every") or ($type_note == "")) { echo ' checked="checked"';} ?> 
			   />
		<label for='type_note_every'> Une note pour chaque période</label>
		<input type="radio" name="type_note" id="type_note_last" value="last" onchange="changement();" 
			<?php if ($type_note == "last") { echo ' checked="checked"';} ?> />
		<label for='type_note_last'> Une note uniquement pour la dernière période</label>
		<input type="radio" name="type_note" id="type_note_no" value="no" onchange="changement();" 
			<?php if ($type_note == "no") { echo ' checked="checked"';} ?> 
			   onclick="mise_a_zero()" />
		<label for='type_note_no'> Pas de note</label>
		<hr />

<?php

	echo "Type particulier pour l'AID&nbsp;: 
		<select name='type_aid' id='type_aid' onchange=\"verif_type_aid();changement();\">";
	for($loop=0;$loop<count($tab_type_aid);$loop++) {
		if($type_aid==$loop) {
			$selected=" selected";
		}
		else {
			$selected="";
		}
		echo "
			<option value='".$loop."' title=\"".$tab_type_aid[$loop]["nom_complet"]."\"".$selected.">".$tab_type_aid[$loop]["nom_court"]."</option>";
	}
	echo "
		</select>
		<hr />";

	//=====================================================================================

$query_max_periode = mysqli_query($GLOBALS["mysqli"], "SELECT max(num_periode) max FROM periodes");

$obj_periode = $query_max_periode->fetch_object();
$max_periode = $obj_periode->max+1;
?>
		Durée de la catégorie d'AID :
<?php
if ($max_periode == '1') {
?>
		<span style="color:red;">
			Attention, aucune période n'est actuellement définie 
			(commencez par créer une ou plusieurs classes sur une ou plusieurs périodes).
		</span>";
<?php
   $max_periode = '2';
}
?>
		<br />
		L'aid débute à la période;
		<select name="display_begin" onchange="changement();">
<?php

$i = 1;

while ($i < $max_periode) {
?>
			<option <?php if ($display_begin==$i) {echo ' selected="selected"';} ?>  ><?php echo $i ?>
<?php
    $i++;
}

?>
		</select>
		(incluse) jusqu'à la période
		<select name="display_end" onchange="changement();">
<?php

$i = 1;

while ($i < $max_periode) {
?>
			<option <?php  if ($display_end==$i) {echo ' selected="selected"';} ?>  ><?php echo $i ?>
<?php
    $i++;
}
?>
		</select>
		(incluse).
		<hr />
		Choisissez le cas échéant la note maximum sur laquelle est notée la catégorie d'AID :
		<br />
		Note maximum : 
		<input type="text" name="note_max" size="4" onchange="changement();" 
			<?php if ($note_max) { echo "value=\"".$note_max."\"";}?> 
			   onBlur="verif_type_note()" />
		<hr />

		Dans le bulletin final<br />
		<input type="radio" name="display_nom" id='display_nom_n' value="n" onchange="changement();" 
			<?php if ($display_nom == "n") { echo ' checked="checked"';} ?> />
		<label for='display_nom_n'> Ne faire apparaître que le nom <em>(complet)</em> de la catégorie d'AID en colonne Matière/enseignement,</label><br />

		<input type="radio" name="display_nom" id='display_nom_y' value="y" onchange="changement();" 
			<?php if (($display_nom == "y") or ($display_nom == "")) { echo ' checked="checked"';} ?> />
		<label for='display_nom_y'> Faire apparaître en colonne Matière/enseignement le nom <em>(complet)</em> de la catégorie d'AID et faire précéder l'appréciation du nom de l'AID,</label><br />

		<input type="radio" name="display_nom" id='display_nom_z' value="z" onchange="changement();" 
			<?php if ($display_nom == "z") { echo ' checked="checked"';} ?> />
		<label for='display_nom_z'> Faire apparaître en colonne Matière/enseignement le nom de l'AID plutôt que le nom de la catégorie d'AID,</label><br />

		<input type="radio" name="display_nom" id='display_nom_x' value="x" onchange="changement();" 
			<?php if ($display_nom == "x") { echo ' checked="checked"';} ?> />
		<label for='display_nom_x'> Faire apparaître en colonne Matière/enseignement le nom court de la catégorie d'AID suivi du nom de l'AID.</label><br />


		<!--
		Dans le bulletin final, le titre complet apparaît et précède l'appréciation dans la case appréciation :
		<br />
		<input type="radio" name="display_nom" id='display_nom_y' value="y" 
			<?php if (($display_nom == "y") or ($display_nom == "")) { echo ' checked="checked"';} ?> />
		<label for='display_nom_y'> Oui</label>
		<input type="radio" name="display_nom" id='display_nom_n' value="n" 
			<?php if ($display_nom == "n") { echo ' checked="checked"';} ?> />
		<label for='display_nom_n'> Non</label>
		-->
		<hr />

		Dans le bulletin final, le message suivant précède le titre complet dans la case appréciation :
		<br />
		<input type="text" name="message" size="40" maxlength="40" onchange="changement();" 
			<?php if ($message) { echo "value=\"".$message."\"";}?> />
		<br />
		<span style='font-size:small;'>(Ce message prendra de la place dans la case appréciation sur le bulletin)</span>

		<hr />

	</p>
	<p>Place de la case réservée à cette catégorie d'aid dans le bulletin final :</p>
	<p>
		<input type="radio" id="orderDisplay1Y" name="order_display1" value="b" onchange="changement();" 
			<?php if (($order_display1 == "b") or (!$order_display1)) { echo ' checked="checked"' ;} ?> />
		<label for="orderDisplay1Y"> En début du bulletin</label>
		<input type="radio" id="orderDisplay1N" name="order_display1" value="e" onchange="changement();" 
			<?php if ($order_display1 == "e") { echo ' checked="checked"';} ?> />
		<label for="orderDisplay1N"> En fin de bulletin</label>
	</p>
	<p>
		Position par rapport aux autres catégories d'aid (entrez un nombre entre 1 et 100) :
		<input type="text" name="order_display2" size="1" onchange="changement();" 
			<?php if (isset($order_display2)) { echo "value=\"".$order_display2."\"";} ?> />
	</p>
	<hr />
	<p><strong>Affichage :  </strong></p>
	<p>
		<input type="checkbox" id="display_Bulletin" name="display_bulletin" value="y" onchange="changement();" 
			<?php if ($display_bulletin == "y") { echo ' checked="checked"';} ?> />
		<label for="display_Bulletin">Les AID de cette catégorie apparaissent dans le bulletin officiel</label>
	</p>
	<p>
		<input type="checkbox" id="bullSimplifie" name="bull_simplifie" value='y' onchange="changement();" 
			<?php if ($bull_simplifie == "y") { echo ' checked="checked"';} ?> />
		<label for="bullSimplifie">Les AID de cette catégorie apparaissent dans le bulletin simplifi&eacute;.</label>
	</p>
	<hr />
	<p><strong>Inscriptions multiples :  </strong></p>
	<p>
		Par d&eacute;faut, un &eacute;l&egrave;ve ne peut &ecirc;tre inscrit dans plus d'un AID par cat&eacute;gorie d'AID.
		<br />
		Cependant, dans certains cas, il peut &ecirc;tre utile d'autoriser l'inscription 
		d'un &eacute;l&egrave;ve &agrave; plusieurs AID d'une m&ecirc;me cat&eacute;gorie.
	</p>
	<p>
		<input type="checkbox" id="autoriser_inscript_multiples" name="autoriser_inscript_multiples" value="y" onchange="changement();" 
			<?php if ($autoriser_inscript_multiples == "y") { echo ' checked="checked"';} ?> />
		<label for="autoriser_inscript_multiples">Autoriser les inscriptions multiples</label>
	</p>
	<hr />
<?php
// si le plugin "gestion_autorisations_publications" existe et est activé, on exclue la rubrique correspondante
$test_plugin = sql_query1("SELECT ouvert FROM plugins WHERE nom='gestion_autorisations_publications'");

//if ((getSettingValue("active_mod_gest_aid")=="y") and ($test_plugin=='y') and (getSettingValue("indice_aid_autorisations_publi") != $indice_aid)) {
if (getSettingValue("active_mod_gest_aid")=="y") {
?>
	<p>
		<strong>Ajout/suppression de "super-gestionnaires"</strong>
	</p>
	<p>En plus des professeurs responsable de chaque AID, vous pouvez indiquer ci-dessous 
		des utilisateurs (professeurs ou CPE) ayant le droit de g&eacute;rer les AIDs 
		de cette cat&eacute;gorie (ajout, suppression, modification d'AID, de professeurs ou d'&eacute;l&egrave;ves)
	</p>
<?php
$sql_liste_data = "SELECT u.login, u.prenom, u.nom, u.statut "
   . "FROM utilisateurs u, j_aidcateg_super_gestionnaires j "
   . "WHERE (j.indice_aid='".$indice_aid."' AND u.login=j.id_utilisateur AND (statut='professeur' or statut='cpe'))  "
   . "ORDER BY u.nom, u.prenom";
// echo $sql_liste_data;
$call_liste_data = mysqli_query($GLOBALS["mysqli"], $sql_liste_data);
$nombre = mysqli_num_rows($call_liste_data);
if ($nombre !=0){
?>
	<table border=0>
<?php
$i = "0";
// while ($i < $nombre) {
while ($obj_liste_data = $call_liste_data->fetch_object()) {
    // $login_gestionnaire = old_mysql_result($call_liste_data, $i, "login");
    $login_gestionnaire = $obj_liste_data->login;
    // $nom_prof = old_mysql_result($call_liste_data, $i, "nom");
    $nom_prof =  $obj_liste_data->nom;
    // $prenom_prof = @old_mysql_result($call_liste_data, $i, "prenom");
    $prenom_prof = $obj_liste_data->prenom;
?>
		<tr>
			<td>
				<strong title="<?php echo $obj_liste_data->login; ?>">
					<?php echo casse_mot($nom_prof, "maj")." ". casse_mot($prenom_prof, "majf2")." <em>(". $obj_liste_data->statut.")</em>"; ?>
				</strong>
			</td>
			<td>
				<input type="checkbox" 
					   name="delete_gestionnaire_<?php echo $login_gestionnaire; ?>" 
					   onchange="changement();" 
					   value="y" />
				(cocher pour supprimer)
			</td>
		</tr>
<?php
    $i++;
}
//if ($nombre !=0)
?>
	</table>
<?php
}
?>
	<select size=1 name="reg_gestionnaire_login" onchange="changement();">
		<option value=''>(aucun)</option>
<?php
$call_prof = mysqli_query($GLOBALS["mysqli"], "SELECT login, nom, prenom, statut  FROM utilisateurs WHERE  etat!='inactif' AND (statut = 'professeur' OR statut = 'cpe') order by nom");
$nombreligne = mysqli_num_rows($call_prof);
$i = "0" ;
// while ($i < $nombreligne) {
while ($obj_call_prof = $call_prof->fetch_object()) {
    // $login_prof = old_mysql_result($call_prof, $i, 'login');
    $login_prof = $obj_call_prof->login;
    // $nom_el = old_mysql_result($call_prof, $i, 'nom');
    $nom_el = $obj_call_prof->nom;
    // $prenom_el = old_mysql_result($call_prof, $i, 'prenom');
    $prenom_el = $obj_call_prof->prenom;
?>
		<option value="<?php echo $login_prof; ?>" title="<?php echo $obj_call_prof->login; ?>"><?php echo casse_mot($nom_el, "maj"); ?> <?php echo casse_mot($prenom_el, "majf2")." (".$obj_call_prof->statut.")"; ?></option>
<?php
    $i++;
}
?>
	</select>
	<hr />
<?php } ?>
	<p>
		<strong>Outils complémentaires de gestion des AIDs :</strong>
	</p>
	<p>
		En activant les outils complémentaires de gestion des AIDs, vous avez accès à des champs supplémentaires
		(<em>attribution d'une salle, possibilité de définir un résumé, le type de production, des mots_clés, 
			un public destinataire, trombinoscope,...
		</em>).
		<a href="javascript:centrerpopup('help.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')">
			Consulter l'aide
		</a>.
	</p>
	<p>
		<!--  onclick="javascript:Element.show('outils_comp');" -->
		<input type="radio" name="activer_outils_comp" id="activer_outils_comp_y" value="y" 
			   onchange="changement(); js_adapte_outil_comp()" 
				   <?php if ($activer_outils_comp=='y') echo " checked='checked' "; ?> />
		<label for='activer_outils_comp_y'>&nbsp;Activer les outils compl&eacute;mentaires</label>
		<br />
		<input type="radio" name="activer_outils_comp" id="activer_outils_comp_n" value="n" 
			   onchange="changement();js_adapte_outil_comp()" 
				   <?php if ($activer_outils_comp=='n') echo " checked='checked' "; ?> />
		<label for='activer_outils_comp_n'>&nbsp;Désactiver les outils compl&eacute;mentaires</label>
	</p>
<script type="text/javascript">
function js_adapte_outil_comp() {
	if(document.getElementById('activer_outils_comp_y').checked==true) {
		Element.show('outils_comp');
	}
	else {
		Element.hide('outils_comp');
	}
}

function verif_type_aid() {
	if(document.getElementById('type_aid').selectedIndex!=0) {
		document.getElementById('activer_outils_comp_y').checked=true;
		js_adapte_outil_comp();
	}
}

if(document.getElementById('reg_nom_complet')) {
	document.getElementById('reg_nom_complet').focus();
}
</script>
<?php if ($activer_outils_comp=='y') {?>
	<div id="outils_comp">
<?php } else { ?>
    <div id="outils_comp" style="display:none;">
<?php } ?>
		<hr />
		<p>
			<strong>Modification des fiches projet : </strong>
		</p>
		<p>
			En plus des professeurs responsable de chaque AID, vous pouvez indiquer ci-dessous des utilisateurs 
			(<em>professeurs ou CPE</em>) ayant le droit de modifier les fiches projet (<em>documentaliste,...</em>)
			même lorsque l'administrateur a désactivé cette possibilité pour les professeurs responsables.
		</p>
<?php
$call_liste_data = mysqli_query($GLOBALS["mysqli"], "SELECT u.login, u.prenom, u.nom, u.statut FROM utilisateurs u, j_aidcateg_utilisateurs j WHERE (j.indice_aid='$indice_aid' and u.login=j.id_utilisateur and (statut='professeur' or statut='cpe'))  order by u.nom, u.prenom");
$nombre = mysqli_num_rows($call_liste_data);
if ($nombre !=0) { ?>
		<table border=0>
<?php
$i = "0";
// while ($i < $nombre) {
while ($obj_call_data = $call_liste_data->fetch_object()) {
    // $login_prof = old_mysql_result($call_liste_data, $i, "login");
    $login_prof = $obj_call_data->login;
    // $nom_prof = old_mysql_result($call_liste_data, $i, "nom");
    $nom_prof = $obj_call_data->nom;
    // $prenom_prof = @old_mysql_result($call_liste_data, $i, "prenom");
    $prenom_prof = $obj_call_data->prenom;
?>
			<tr title="<?php echo $obj_call_data->login;?>">
				<td>
					<strong><?php echo $nom_prof." ".$prenom_prof; ?></strong>
				</td>
				<td>
					<em><?php echo $obj_call_data->statut; ?></em>
				</td>
				<td> <input type="checkbox" name="delete_<?php echo $login_prof; ?>" value="y" onchange="changement();" />
					(cocher pour supprimer)
				</td>
			</tr>
<?php
    $i++;
}
//if ($nombre !=0) ?>
		</table>
<?php } ?>
		<select size=1 name="reg_prof_login" onchange="changement();">
			<option value=''>(aucun)</option>
<?php
$call_prof = mysqli_query($GLOBALS["mysqli"], "SELECT login, nom, prenom, statut FROM utilisateurs WHERE  etat!='inactif' AND (statut = 'professeur' OR statut = 'cpe') order by nom");
$nombreligne = mysqli_num_rows($call_prof);
$i = "0" ;
// while ($i < $nombreligne) {
while ($obj_call_prof = $call_prof->fetch_object()) {
    // $login_gestionnaire = old_mysql_result($call_prof, $i, 'login');
    $login_gestionnaire = $obj_call_prof->login;
    // $nom_el = old_mysql_result($call_prof, $i, 'nom');
    $nom_el = $obj_call_prof->nom;
    // $prenom_el = old_mysql_result($call_prof, $i, 'prenom');
    $prenom_el = $obj_call_prof->prenom;
?>
			<option value="<?php echo $login_gestionnaire; ?>" title="<?php echo $obj_call_prof->login;?>"><?php echo $nom_el; ?> <?php echo $prenom_el; ?> (<?php echo $obj_call_prof->statut;?>)</option>
<?php
    $i++;
}

?>
</select>
<hr /><p><b>Feuille de présence : </b></p>
<p>En cochant la case présence ci-dessous, vous avez la possibilité, dans l'interface de visualisation, d'afficher un lien permettant d'imprimer des feuilles de présence.</p>
<p>
<input type="checkbox" id="feuillePresence" name="feuille_presence" value="y" <?php if ($feuille_presence == "y") { echo ' checked="checked"';} ?> onchange="changement();" />
<label for="feuillePresence"> Afficher un lien permettant l'impression de feuilles de pr&eacute;sence</label>
</p>
</div>

</div>
<input type="hidden" name="is_posted" value="1" />
<input type="hidden" name="indice_aid" value="<?php echo $indice_aid;?>" />
<div id='fixe'>
<input type="submit" value="Enregistrer" />
</div>
</form>
<?php require("../lib/footer.inc.php"); ?>
