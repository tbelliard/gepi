<?php

/**
 *
 * Modif table `droits` : ALTER TABLE `droits` ADD `autre` VARCHAR( 1 ) NOT NULL DEFAULT 'F' AFTER `secours` ;
 * @version $Id$
 * @copyright 2008
 */
$affiche_connexion = 'yes';
$niveau_arbo = 1;
	// Initialisations files
	require_once("../lib/initialisations.inc.php");

	// Resume session
	$resultat_session = resumeSession();
	if ($resultat_session == 'c') {
		header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
		die();
	} else if ($resultat_session == '0') {
		header("Location: ../logout.php?auto=1");
		die();
	};

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
if (getSettingValue("statuts_prives") != "y") {
	trigger_error('Impossible d\'accéder à ce module de Gepi.', E_USER_ERROR);
}

//	include("utilisateurs.class.php");
$titre_page = 'Gestion des nouveaux statuts';
include("../lib/header.inc");


// ========================================= Variables ============================
$action = isset($_POST["action"]) ? $_POST["action"] : NULL;
$nouveau_statut = isset($_POST["nouveau_statut"]) ? $_POST["nouveau_statut"] : NULL;
$msg = NULL;


if ($action == 'ajouter') {

	// on fait quelques vérifications sur le nom du statut (si il existe déjà, longueur du nom, enlever les ' et les ",...)
	// On ne garde que les 12 premières lettres
	$stat_1 = substr($nouveau_statut, 0, 12);
	// On enlève les accents
	$stat_2 = strtr($stat_1, "éèêëîïôöâàäùûüç", "eeeeiiooaaauuuc");
	// On enlève les apostrophes et les guillemets
	$insert_statut = htmlentities($stat_2, ENT_QUOTES);

	// On ajoute le statut privé après avoir vérifié qu'il n'existe pas déjà
	$query_v = mysql_query("SELECT id FROM droits_statut WHERE nom_statut = '".$insert_statut."'");
	$verif = mysql_num_rows($query_v);

	if ($verif >= 1) {

		$msg .= "<h3 class='red'>Ce statut priv&eacute;, existe d&eacute;j&agrave; !</h3>";

	}else{

		$sql = "INSERT INTO droits_statut (id, nom_statut) VALUES ('', '".$insert_statut."')";
		$enregistre = mysql_query($sql) OR trigger_error('Impossible d\'enregistrer ce nouveau statut', E_USER_WARNING);
		if ($enregistre) {
			$msg .= "<h3 class='green'>Ce statut est enregistr&eacute !</h3>";
		}

	}


} // if ($action == 'ajouter')

if ($action == 'modifier') {
	// On initialise toutes les variables envoyées
	$sql = "SELECT id, nom_statut FROM droits_statut ORDER BY nom_statut";
	$query = mysql_query($sql);
	$nbre = mysql_num_rows($query);

	for($a = 0; $a < $nbre; $a++){

		$b = mysql_result($query, $a, "id");

		$suppr[$a] = isset($_POST["suppr|".$b]) ? $_POST["suppr|".$b] : NULL;
		$ne[$a] = isset($_POST["ne|".$b]) ? $_POST["ne|".$b] : NULL;
		$bs[$a] = isset($_POST["bs|".$b]) ? $_POST["bs|".$b] : NULL;
		$va[$a] = isset($_POST["va|".$b]) ? $_POST["va|".$b] : NULL;
		$sa[$a] = isset($_POST["sa|".$b]) ? $_POST["sa|".$b] : NULL;
		$cdt[$a] = isset($_POST["cdt|".$b]) ? $_POST["cdt|".$b] : NULL;
		$ee[$a] = isset($_POST["ee|".$b]) ? $_POST["ee|".$b] : NULL;
		$te[$a] = isset($_POST["te|".$b]) ? $_POST["te|".$b] : NULL;

		// On assure les différents traitements traitements
		if ($suppr[$a] == 'on') {
			// On supprime le statut demandé
			$sql_d = "DELETE FROM droits_statut WHERE id = '".$b."'";
			$query_d = mysql_query($sql_d) OR trigger_error('Impossible de supprimer ce statut : '.mysql_error(), E_USER_NOTICE);
		}

	}
}

// On récupère tous les statuts nouveaux qui existent
$aff_tableau = $aff_select = $aff_users = '';
$sql = "SELECT id, nom_statut FROM droits_statut ORDER BY nom_statut";
$query = mysql_query($sql);

if ($query) {
	while($rep = mysql_fetch_array($query)){

	$aff_select .= '
		<option value="'.$rep["id"].'">'.$rep["nom_statut"].'</option>';

	$aff_tableau .= '
	<tr style="border: 1px solid lightblue; text-align: center;">
		<td style="font-weight: bold; color: red;">'.$rep["nom_statut"].'</td>
		<td><input type="checkbox" name="ne|'.$rep["id"].'" /></td>
		<td><input type="checkbox" name="bs|'.$rep["id"].'" /></td>
		<td><input type="checkbox" name="va|'.$rep["id"].'" /></td>
		<td><input type="checkbox" name="sa|'.$rep["id"].'" /></td>
		<td><input type="checkbox" name="cdt|'.$rep["id"].'" /></td>
		<td><input type="checkbox" name="ee|'.$rep["id"].'" /></td>
		<td><input type="checkbox" name="te|'.$rep["id"].'" /></td>
		<td><input type="checkbox" name="suppr|'.$rep["id"].'" /></td>
	</tr>
	<tr style="background-color: white;"><td colspan="9"></td></tr>';
	}
}

// On traite la partie sur les utilisateurs 'autre' pour leur définir le bon statut

	// On traite les demandes de l'admin sur la définition des statuts des utilisateurs 'autre'
	/*if ($action == "defStatut") {
		// On vérifie si cet utilisateur existe déjà
		$query_v2 = mysql_query("SELECT id_statut FROM droits_utilisateurs WHERE login_user = '".$login_user."'");
		if ($query_v2) {
			if () {

			}
		}
	}
*/
	// On récupère les utilisateurs qui ont un statut 'autre'
	$sql_u = "SELECT * FROM utilisateurs WHERE statut = 'autre' AND etat = 'actif'";
	$query_u = mysql_query($sql_u);

	// On affiche la liste des utilisateurs avec un select des nouveaux statuts

	while($tab = mysql_fetch_array($query_u)){

		$aff_users .= '
		<tr>
			<td>'.$tab["nom"].' '.$tab["prenom"].'</td>
			<td>
		<form action="creer_statut.php" method="post">
			<input type="hidden" name="action" value="defStatut" />
			<input type="hidden" name="login_user" value="'.$tab["login"].'" />
			<select name="statut_user">
				<option value="rien">Choix du statut</option>
				'.$aff_select.'
			</select>
		</form>
		</td></tr>';

	}



?>
<!-- Début de la page sur les statut privés -->

<br />
<?php echo $essai; ?>
<?php echo $msg; ?>
<p>Pour pouvoir donner un statut priv&eacute; &agrave; un utilisateur, il faut qu'il soit enregistrer avec le statut Gepi 'autre' lors de sa cr&eacute;ation
(<a href="./modify_user.php">CREER UN UTILISATEUR</a>).
 Vous pourrez ensuite d&eacute;finir des statuts priv&eacute;s et leur donner des droits. Pour terminer, il suffira de faire le lien entre les stauts priv&eacute;s et les utilisateurs
 en bas de cette page.</p>

<div style="background-color: lightblue;">
<p style="color: grey; text-align: right; font-style: italic;">Gestion des droits des statuts priv&eacute;s&nbsp;&nbsp;</p>

<form action="creer_statut.php" method="post">
	<input type="hidden" name="action" value="modifier" />

<table style="border: 1px solid lightblue; background: #CCFFFF;">
	<thead>
	<tr>
		<th style="border: 1px solid lightblue;">Statut</th>
		<th style="border: 1px solid lightblue;">Voir les notes des élèves</th>
		<th style="border: 1px solid lightblue;">Voir les bulletins simplifiés</th>
		<th style="border: 1px solid lightblue;">Voir les absences des élèves</th>
		<th style="border: 1px solid lightblue;">Saisir les absences des élèves</th>
		<th style="border: 1px solid lightblue;">Voir les cahiers de textes</th>
		<th style="border: 1px solid lightblue;">Voir les emplois du temps des élèves</th>
		<th style="border: 1px solid lightblue;">Voir tous les emplois du temps</th>
		<th style="border: 1px solid lightblue;">Supprimer le statut</th>
	</tr>
	<tr style="background-color: white;"><td colspan="9"></td></tr>
	</thead>
	<tbody>

		<?php echo $aff_tableau; ?>
		<tr style="background-color: white;"><td colspan="9"></td></tr>
	</tbody>
	<tfoot>
		<tr><td colspan="8"><input type="submit" name="modifier" value="Enregistrer et mettre &agrave; jour" /></td></tr>
	</tfoot>
</table>

</form>

</div>

<br />

<p style="cursor: pointer;" onClick="changementDisplay('ajoutStatut', '');">Ajouter un statut priv&eacute;</p>
<div id="ajoutStatut" style="display: none;">

	<form method="post" action="creer_statut.php">
		<p>
		<label for="new">Nom du nouveau statut</label>
		<input type="text" name="nouveau_statut" value="" />
		<input type="hidden" name="action" value="ajouter" />

		<input type="submit" name="Ajouter" value="Ajouter" />
		</p>

		<p style="color: grey; font-style: italic; margin-left: 10em;">Il vaut mieux ne mettre que des lettres. Longueur maximum : 12 caract&egrave;res.</p>

	</form>

</div>

<br />
<hr />
<br />
<!-- Quel statut pour quelle personne ? -->

<div id="userStatut" style="border: 5px solid silver; width: 20em;">

	<table>
		<?php echo $aff_users; ?>
	</table>

</div>

<?php
require("../lib/footer.inc.php");