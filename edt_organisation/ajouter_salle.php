<?php

/**
 * Fichier permettant d'ajouter des salles dans l'EdT de Gepi
 *
 * @version $Id$
 * @copyright 2007
 */
$titre_page = "Emploi du temps";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = resumeSession();
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
	Die('Vous devez demander à votre administrateur l\'autorisation de voir cette page.');
}
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "edt_organisation/style_edt";

// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php"); ?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php
	// Initialisation des variables
$ajoutsalle=isset($_GET['ajoutsalle']) ? $_GET['ajoutsalle'] : (isset($_POST['ajoutsalle']) ? $_POST['ajoutsalle'] : NULL);
$numero_salle = isset($_POST["numerosalle"]) ? $_POST["numerosalle"] : NULL;
$nom_salle = isset($_POST["nomsalle"]) ? $_POST["nomsalle"] : NULL;
$del_salle = isset($_POST["del_salle"]) ? $_POST["del_salle"] : NULL;
$modif_salle = isset($_POST["modif_salle"]) ? $_POST["modif_salle"] : NULL;
$new_name = isset($_POST["new_name"]) ? $_POST["new_name"] : NULL;
?>
<!-- AJAX de formulaire -->

<script type="text/javascript">
function nomSalle(ident_salle) {
	var xmlhttp;

	//if (typeof XMLHttpRequest == "object") xmlhttp = new XMLHttpRequest();
	//if (typeof ActiveXObject == "object" || typeof ActiveXObject == "function") xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
//--Début du scrip Stéphane Boireau
			if(window.XMLHttpRequest) // Firefox
				xmlhttp = new XMLHttpRequest();
			else if(window.ActiveXObject) // Internet Explorer
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			else { // XMLHttpRequest non supporté par le navigateur
				alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
				return;
			}
//--fin du script Stephane Boireau
	xmlhttp.open("POST", "ajax_edt.php", true);

	xmlhttp.onreadystatechange = function() {
		try {
			if (xmlhttp.readyState == 4) {
				var resultat = xmlhttp.responseText;
				nomSalle.innerHTML = resultat;
			}
		} catch (erreur) {}
	}

	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	var data = "salle="+ident_salle;
	xmlhttp.send(data);
}
</script>

G&eacute;rer les salles de Gepi
<br /><br />
<fieldset id="aj_salle">

	<legend>Ajouter une salle dans la base de donn&eacute;es</legend>
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
    	<script type='text/javascript'>
		document.getElementById('numerosalle').focus();
		</script>
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
		// SI le nom de la salle n'est pas précisé
		if ($numero_salle == "") {
			echo "<font color=\"red\">Vous devez précisez un numéro de salle !</font>\n<br />\n";
		}
	if ($nom_salle == "") {
		$nom_salle = "salle ".$numero_salle;
	}
	else $nom_salle = $_POST["nomsalle"];
}

// Quelques vérifications d'usage

$verif_champs = 0;
	$verif_long_num = strlen($numero_salle);
	$verif_long_nom = strlen($nom_salle);

	if ($verif_long_num > 0) {
		if ($verif_long_num <= 5 OR $verif_long_nom <= 30) {
			$add_new_numero = addslashes($numero_salle);
			$add_new_salle = addslashes($nom_salle);
			$verif_champs = 1;
		}
		else die();
	}

	// Ultime vérification avant de rentrer de nouvelles salles dans la base
if (isset($add_new_numero) AND isset($add_new_salle)) {
	if ($verif_champs = 1) {
		$reche_salle = mysql_query("SELECT numero_salle FROM salle_cours WHERE numero_salle = '".$add_new_numero."'");
		$nbre_salle = mysql_num_rows($reche_salle);
		if ($nbre_salle === 0) {
			$req_ajout = mysql_query("INSERT INTO salle_cours (id_salle, numero_salle, nom_salle) VALUES ('', '$add_new_numero', '$add_new_salle')")
			OR die ('Echec lors de l\'enregistrement');
			echo "<span class=\"accept\">La salle numéro ".$add_new_numero." appelée \"".$add_new_salle."\" a bien été enregistrée !</span>";
		}
		else
		echo "<span class=\"refus\">Cette salle existe déjà ! Veuillez changer son numéro</span>";
	}
	else die ('Impossible de rentrer de nouvelles salles');
}


?>
<br />
</fieldset>
<br />

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

	<form action="ajouter_salle.php" method="post" name="modifier_nom_salle1">
		<input type="hidden" name="ajoutsalle" value="ok" />
		<select name="modif_salle" onchange='document.modifier_nom_salle1.submit();'>>
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

		echo ("		<option value='".$tab_select[$i]["id_salle"]."'".$selected.">".$tab_select[$i]["nom_salle"]."</option>\n");
	}

?>
		</select>
	</form>
				</td>
				<td>
<?php
	// On affiche alors un text qui donne le nom actuel de la salle
if (isset($modif_salle)) {
	$req_modif_nom = mysql_query("SELECT numero_salle, nom_salle FROM salle_cours WHERE id_salle = $modif_salle");
	$rep_modif_nom = mysql_fetch_array($req_modif_nom);
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
	$req_modif_nom = mysql_query("UPDATE salle_cours SET nom_salle = '$new_name' WHERE id_salle = '$modif_salle'") OR DIE ('Echec dans le changement de nom');
	$req_numero = mysql_query("SELECT numero_salle FROM salle_cours WHERE id_salle = '$modif_salle'") OR DIE ('Echec dans le changement du nom');
	$rep_numero = mysql_fetch_array($req_numero);
		$num_salle = $rep_numero["numero_salle"];
	echo '		</td>
			</tr>
			<tr>
				<td></td>
				<td>
	<span class="accept">La salle numéro '.$num_salle.' s\'appelle désormais : '.$new_name.'.</span>
	';
}
?>
				</td>
			</tr>
		</table>
<br />
</fieldset>

<br />
<fieldset id="enlever">
	<legend>Effacer une salle de la base de donn&eacute;es</legend>
		<table border="0" cellpading="0" cellspacing="0">
			<tr><td>
<?php
if ($_SESSION["statut"] == "administrateur" AND isset($del_salle) AND $del_salle != NULL AND $del_salle != "rien") {
	$req_verif = mysql_query("SELECT nom_salle FROM salle_cours WHERE id_salle = '".$del_salle."'");
	$rep_nom = mysql_fetch_array($req_verif);
	$rep_verif = mysql_num_rows($req_verif);
		if ($rep_verif != 1) {
			echo "Impossible d'effacer cette salle car elle n'existe pas !";
			die();
		}
	$req_effacer = mysql_query("DELETE FROM salle_cours WHERE id_salle = '".$del_salle."'")
	OR die ('Cette salle n\'a pas pu être effacée');
	echo "<font color=\"green\">la salle \"".$rep_nom["nom_salle"]."\" a été effacée de la base de Gepi</font>\n";
}
?>
			</td><td>

	<!--choix de la salle-->
	<form action="ajouter_salle.php" name="effacer_salle" method="post">
		<select name="del_salle">
			<option value="rien">Choix de la salle</option>
<?php
	$tab_select = renvoie_liste("salle");

	for($i=0;$i<count($tab_select);$i++) {

		echo ("			<OPTION value='".$tab_select[$i]["id_salle"]."'>".$tab_select[$i]["nom_salle"]."</OPTION>\n");
	}
?>
		</select>
		<input type="hidden" name="ajoutsalle" value="ok" />
		<br /><br />
		<input type="submit" name="Valider" value="Effacer" />
		<br />
	</form>

			</td></tr>
		</table>
</fieldset>
	</div>
<br />
<br />
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>
