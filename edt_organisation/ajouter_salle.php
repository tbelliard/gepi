<?php

/**
 * Fichier permettant d'ajouter des salles dans l'EdT de Gepi
 *
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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
require_once("./choix_langue.php");

$titre_page = TITLE_ADD_CLASSROOM;
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die(ASK_AUTHORIZATION_TO_ADMIN);
}
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";

// On insère l'entête de Gepi
require_once("../lib/header.inc.php");

// On ajoute le menu EdT
require_once("./menu.inc.php"); ?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php
    require_once("./menu.inc.new.php");
	// Initialisation des variables
$ajoutsalle=isset($_GET['ajoutsalle']) ? $_GET['ajoutsalle'] : (isset($_POST['ajoutsalle']) ? $_POST['ajoutsalle'] : NULL);
$numero_salle = isset($_POST["numerosalle"]) ? $_POST["numerosalle"] : NULL;
$nom_salle = isset($_POST["nomsalle"]) ? $_POST["nomsalle"] : NULL;
$del_salle = isset($_POST["del_salle"]) ? $_POST["del_salle"] : NULL;
$modif_salle = isset($_POST["modif_salle"]) ? $_POST["modif_salle"] : NULL;
$new_name = isset($_POST["new_name"]) ? $_POST["new_name"] : NULL;
?>

<?php echo MANAGE_GEPI_CLASSROOMS ?>
<br /><br />
<fieldset id="aj_salle">

	<legend><?php echo ADD_CLASSROOM_IN_DB ?></legend>
<form name="ajouter_salle" action="ajouter_salle.php" method="post">
	<table cellspacing="0" border="0">

	<tr>
		<td></td>
		<td><span class="legende">5 caract&egrave;res maximum</span></td>
		<td></td>
		<td><span class="legende">30 caract&egrave;res maximum</span></td>
	</tr>

	<tr>
    	<td>Num&eacute;ro de la salle : </td>
    	<td><input type="text" name="numerosalle" value="" size="5" maxlength="5" title="Vous pouvez utiliser le tabulateur pour changer de champ" onfocus="this.className='focus';" onblur="this.className='normal';" />
			<img src="../images/icons/ico_ampoule.png" title="Si vous ne pr&eacute;cisez pas le nom de la salle, son num&eacute;ro sera son nom." /></td>
    	<td>Nom de la salle : </td>
    	<td><input type="text" name="nomsalle" value="" size="30" maxlength="30" onfocus="this.className='focus';" onblur="this.className='normal';" /></td>
	</tr>

	<tr>
    	<td><input type="hidden" name="ajoutsalle" value="ok" /></td>
    	<td></td>
    	<td><input type="reset" name="retablir" /></td>
    	<td><input type="submit" name="valider" /></td>
	</tr>

	</table>
</form>

<?php
if (isset($nom_salle) AND isset($numero_salle)) {
	// Si le nom de la salle n'est pas précisé
	if ($numero_salle == "") {
		echo "<font color=\"red\">Vous devez précisez un numéro de salle !</font>\n<br />\n";
	}
	if ($nom_salle == "") {
		$nom_salle = "salle ".$numero_salle;
	}else{
		$nom_salle = $_POST["nomsalle"];
	}
}

// Quelques vérifications d'usage

$verif_champs = 0;
	$verif_long_num = mb_strlen($numero_salle);
	$verif_long_nom = mb_strlen($nom_salle);

	if ($verif_long_num > 0) {
		if ($verif_long_num <= 5 OR $verif_long_nom <= 30) {
			$add_new_numero = traitement_magic_quotes($numero_salle);
			$add_new_salle = traitement_magic_quotes($nom_salle);
			$verif_champs = 1;

		}else{
			trigger_error('Une erreur de saisie a bloqué le système, veuillez recommencer. ', E_USER_ERROR);
		}
	}

	// Ultime vérification avant de rentrer de nouvelles salles dans la base
if (isset($add_new_numero) AND isset($add_new_salle)) {
	if ($verif_champs = 1) {
		$reche_salle = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT numero_salle FROM salle_cours WHERE numero_salle = '".$add_new_numero."'");
		$nbre_salle = mysqli_num_rows($reche_salle);
		if ($nbre_salle === 0) {
			$req_ajout = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO salle_cours
									(id_salle, numero_salle, nom_salle) VALUES
									('', '$add_new_numero', '$add_new_salle')")
								OR trigger_error('Echec lors de l\'enregistrement : '.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), E_USER_ERROR);

			echo "<span class=\"accept\">La salle numéro ".unslashes($add_new_numero)." appelée \"".unslashes($add_new_salle)."\" a bien été enregistrée !</span>";
		}
		else{
			echo "<span class=\"refus\">Cette salle existe déjà ! Veuillez changer son numéro</span>";
		}
	}else{
		trigger_error('Impossible de rentrer de nouvelles salles. ', E_USER_WARNING);
	}
}


?>
<br />
</fieldset>
<br />

	<form action="ajouter_salle.php" method="post" name="modifier_nom_salle1">

<fieldset id="modifier_nom">
	<legend>Modifier le nom d'une salle</legend>
		<table border="0" cellpading="0" cellspacing="0">
<?php
if(isset($modif_salle)) {
	echo '
			<tr>
				<td></td>
				<td><span class="legende">30 caract&egrave;res maximum</span></td>
			</tr>';
}
?>
			<tr>
				<td>

		<input type="hidden" name="ajoutsalle" value="ok" />
		<select name="modif_salle" onchange='document.modifier_nom_salle1.submit();'>
		<option value="rien">Choix de la salle</option>
<?php
	$tab_select = renvoie_liste("salle");

	for($i=0;$i<count($tab_select);$i++) {
			if(isset($modif_salle)){
		if($modif_salle==$tab_select[$i]["id_salle"]){
			$selected=" selected='selected'";
		}
		else{
			$selected="";
		}
	}
	else{
		$selected="";
	}
		if ($tab_select[$i]["nom_salle"] != "") {
			$aff_nom_salle = "(".$tab_select[$i]["nom_salle"].")";
		} else {
			$aff_nom_salle = "";
		}

		echo ("		<option value='".$tab_select[$i]["id_salle"]."'".$selected.">".$tab_select[$i]["numero_salle"]." ".$aff_nom_salle."</option>\n");
	}

?>
		</select>
	</form>
				</td>
				<td>
<?php
	// On affiche alors un texte qui donne le nom actuel de la salle
if (isset($modif_salle)) {
	$req_modif_nom = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT numero_salle, nom_salle FROM salle_cours WHERE id_salle = $modif_salle");
	$rep_modif_nom = mysqli_fetch_array($req_modif_nom);
		$numero_salle = $rep_modif_nom["numero_salle"];
		$ancien_nom = $rep_modif_nom["nom_salle"];
	if (isset($new_name)) {
		$ancien_nom = $new_name;
	}
	echo '
	<form action="ajouter_salle.php" name="modifier_nom_salle" method="post">
		<input type="hidden" name="ajoutsalle" value="ok" />
		<input type="text" name="new_name" size="30" maxlenght="30" value="'.$ancien_nom.'" />
		<input type="hidden" name="modif_salle" value="'.$modif_salle.'" />
		<input type="submit" name="Valider" value="Enregistrer" />
	';
}

	// Traitement du nouveau nom de la salle
if (isset($new_name) AND $new_name != "" ) {
	$nettoyage1 = mb_substr($new_name, 0, 30);
	$new_name_propre = traitement_magic_quotes($nettoyage1); // cette fonction est dans le traitement_data.inc.php

	$req_modif_nom = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE salle_cours SET nom_salle = '$new_name_propre' WHERE id_salle = '$modif_salle'")
						OR trigger_error('Echec dans le changement de nom', E_USER_WARNING);
	$req_numero = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT numero_salle FROM salle_cours WHERE id_salle = '$modif_salle'")
						OR trigger_error('Echec dans le changement du nom', E_USER_WARNING);

	$rep_numero = mysqli_fetch_array($req_numero);
		$num_salle = $rep_numero["numero_salle"];
	echo '		</td>
			</tr>
			<tr>
				<td></td>
				<td>
	<span class="accept">'; printf(CHANGE_CLASSROOM_NAME, $num_salle, unslashes($new_name_propre)); echo'</span>
		</form>';
}
?>
				</td>
			</tr>
		</table>
<br />
</fieldset>

<br />

	<form action="ajouter_salle.php" name="effacer_salle" method="post">

<fieldset id="enlever">
	<legend>Effacer une salle de la base de donn&eacute;es</legend>
		<table border="0" cellpading="0" cellspacing="0">
			<tr><td>
<?php
if ($_SESSION["statut"] == "administrateur" AND isset($del_salle) AND $del_salle != NULL AND $del_salle != "rien") {
	$req_verif = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT numero_salle, nom_salle FROM salle_cours WHERE id_salle = '".$del_salle."'");
	$rep_nom = mysqli_fetch_array($req_verif);
	$rep_verif = mysqli_num_rows($req_verif);
		if ($rep_verif != 1) {
			echo "Impossible d'effacer cette salle car elle n'existe pas !";
			trigger_error("Vous essayez d'effacer une salle qui n'existe pas !", E_USER_ERROR);
		}
	$req_effacer = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM salle_cours WHERE id_salle = '".$del_salle."'")
						OR trigger_error('Cette salle n\'a pas pu être effacée', E_USER_WARNING);

	if ($rep_nom["nom_salle"] != '') {
		$aff_nom_salle = ' ('.$rep_nom["nom_salle"].')';
	}else{
		$aff_nom_salle = '';
	}

	echo '
	<font color="green">La salle '.$rep_nom["numero_salle"].$aff_nom_salle.' a été effacée de la base de Gepi.</font>';
}
?>
			</td><td>

	<!--choix de la salle-->

		<select name="del_salle">
			<option value="rien">Choix de la salle</option>
<?php
	$tab_select = renvoie_liste("salle");

	for($i=0;$i<count($tab_select);$i++) {
		if ($tab_select[$i]["nom_salle"] != "") {
			$aff_nom_salle = "(".$tab_select[$i]["nom_salle"].")";
		} else {
			$aff_nom_salle = "";
		}
		echo ("			<OPTION value='".$tab_select[$i]["id_salle"]."'>".$tab_select[$i]["numero_salle"]." ".$aff_nom_salle."</OPTION>\n");
	}
?>
		</select>
		<input type="hidden" name="ajoutsalle" value="ok" />
		<br /><br />
		<input type="submit" name="Valider" value="Effacer" />
		<br />
			</td></tr>
		</table>
</fieldset>
	</form>
	</div>
<br />
<br />
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>
