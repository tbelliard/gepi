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
$login_user = isset($_POST["login_user"]) ? $_POST["login_user"] : NULL;
$statut_user = isset($_POST["statut_user"]) ? $_POST["statut_user"] : NULL;
$msg = $msg2 = $msg3 = NULL;

// Ces tableaux définissent les différents fichiers à autoriser en fonction du statut
$values_b = '';
// La liste des fichiers à traiter
require_once('creer_statut_autorisation.php');

//print_r($autorise);

// Fonction qui permet d'afficher  le selected de l'affichage
function verifChecked($id){

	global $autorise;

	for($i = 1 ; $i < 8 ; $i++){
		// On récupère les droits de ce statut privé
		$sql_ds = "SELECT autorisation FROM droits_speciaux WHERE id_statut = '".$id."' AND nom_fichier = '".$autorise[$i][0]."'";
		$query_ds = mysql_query($sql_ds) OR trigger_error('Erreur dans la fonction verifChecked ', E_USER_ERROR);
		$rep = mysql_result($query_ds, "autorisation");

		if ($rep == 'V') {
			$retour[$i] = ' checked="checked"';
		}else{
			$retour[$i] = '';
		}
	}

	return $retour;
}

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
		$cherche_id = mysql_query("SELECT id FROM droits_statut WHERE nom_statut = '".$insert_statut."'");
		$last_id = mysql_result($cherche_id, "id");

		if ($enregistre) {

			// On enregistre les droits généraux adéquats avec la virgule qui va bien entre chaque value
			// Chaque droit correspond à un ensemble d'autorisations sur un ou plusieurs fichiers
			// Pour ajouter des droits, il suffit d'ajouter des braches au tableau $autorise plus haut avec tous les fichiers utiles

			for($a = 0 ; $a < 8 ; $a++){
				$nbre = count($autorise[$a]);
				// On met V pour les autorisations de base mais F pour les autres
				if ($a != 0) {
					$vf = 'F';
				}else{
					$vf = 'V';
				}
				for($c = 0 ; $c < $nbre ; $c++){

					$values_b .= '("", "'.$last_id.'", "'.$autorise[$a][$c].'", "'.$vf.'")';

					if ($c <= ($nbre - 2)) {
						$values_b .= ', ';
					}

				}
				// On ajoute une virgule entre chaque droit sauf à la fin
				if ($a <= 6) {
					$values_b .= ', ';
				}
			}

 			$autorise_b = mysql_query("INSERT INTO droits_speciaux (id, id_statut, nom_fichier, autorisation) VALUES ".$values_b."")
			 										OR trigger_error('Impossible d\'enregistrer : '.$values.' : '.mysql_error(), E_USER_WARNING);

			if ($autorise_b) {
				$msg .= '<h4 style="color: green;">Ce statut est enregistr&eacute !</h4>';
			}

		}

	}

} // if ($action == 'ajouter')

if ($action == 'modifier') {
	// On initialise toutes les variables envoyées
	$sql = "SELECT id, nom_statut FROM droits_statut ORDER BY nom_statut";
	$query = mysql_query($sql) OR trigger_error('Erreur '.$sql, E_USER_ERROR);
	$nbre = mysql_num_rows($query);

	for($a = 0; $a < $nbre; $a++){

		$b = mysql_result($query, $a, "id");

		$test[0][$a] = isset($_POST["suppr|".$b]) ? $_POST["suppr|".$b] : NULL;
		$test[1][$a] = isset($_POST["ne|".$b]) ? $_POST["ne|".$b] : NULL;
		$test[2][$a] = isset($_POST["bs|".$b]) ? $_POST["bs|".$b] : NULL;
		$test[3][$a] = isset($_POST["va|".$b]) ? $_POST["va|".$b] : NULL;
		$test[4][$a] = isset($_POST["sa|".$b]) ? $_POST["sa|".$b] : NULL;
		$test[5][$a] = isset($_POST["cdt|".$b]) ? $_POST["cdt|".$b] : NULL;
		$test[6][$a] = isset($_POST["ee|".$b]) ? $_POST["ee|".$b] : NULL;
		$test[7][$a] = isset($_POST["te|".$b]) ? $_POST["te|".$b] : NULL;

		//echo $ne[$a].$suppr[$a].'|a'.$a.'|b'.$b;

		// On assure les différents traitements traitements
		if ($test[0][$a] == 'on') {
			// On supprime le statut demandé
			$sql_d = "DELETE FROM droits_statut WHERE id = '".$b."'";
			$query_d = mysql_query($sql_d) OR trigger_error('Impossible de supprimer ce statut : '.mysql_error(), E_USER_NOTICE);

			// Il faut aussi effacer toutes les références à ce statut dans les autres tables
			$sql_d = "DELETE FROM droits_utilisateurs WHERE id_statut = '".$b."'";
			$query_d = mysql_query($sql_d) OR trigger_error('Impossible de supprimer ce statut du : '.mysql_error(), E_USER_NOTICE);

			$sql_d = "DELETE FROM droits_speciaux WHERE id_statut = '".$b."'";
			$query_d = mysql_query($sql_d) OR trigger_error('Impossible de supprimer ce statut ds : '.mysql_error(), E_USER_NOTICE);

		}else{
			// On va vérifier les droits un par un
			// ne = notes élèves ; bs = bulletins simplifiés ; va = voir absences ; sa = saisir absences
			// cdt = cahier de textes ; ee = emploi du temps des élèves ; te = tous les emplois du temps

			for($m = 1 ; $m < 8 ; $m++){

				$nbre2 = count($autorise[$m]);
				// On vérifie si le droit est coché ou non
				if ($test[$m][$a] == 'on') {
					$vf = 'V';
				}else{
					$vf = 'F';
				}
					// On n'oublie pas de mettre à jour tous les fichiers adéquats
					for($i = 0 ; $i < $nbre2 ; $i++){
						$sql_maj = "UPDATE droits_speciaux SET autorisation = '".$vf."' WHERE id_statut = '".$b."' AND nom_fichier = '".$autorise[$m][$i]."'";
						$query_maj = mysql_query($sql_maj) OR trigger_error("Mauvaise mise à jour  : ".mysql_error(), E_USER_WARNING);

						if (!$query_maj) {
							$msg3 .= '<span class="red">Erreur</span>';
						}
					}
			}
		}
	}
	// On assure un message de confirmation si les modifications se sont bien passées
	if ($msg3 === NULL) {
		$msg3 .= '<p style="color: green">Les modifications sont bien enregistrées.</p>';
	}
}



// On récupère tous les statuts nouveaux qui existent
$aff_tableau = $aff_select = $aff_users = $selected = '';
$sql = "SELECT id, nom_statut FROM droits_statut ORDER BY nom_statut";
$query = mysql_query($sql);

if ($query) {
	while($rep = mysql_fetch_array($query)){

		// On vérifie s'il faut le cocher par défaut ou pas
		$checked = verifChecked($rep["id"]);

	$aff_tableau .= '
	<tr style="border: 1px solid lightblue; text-align: center;">
		<td style="font-weight: bold; color: red;">'.$rep["nom_statut"].'</td>
		<td><input type="checkbox" name="ne|'.$rep["id"].'"'.$checked[1].' /></td>
		<td><input type="checkbox" name="bs|'.$rep["id"].'"'.$checked[2].' /></td>
		<td><input type="checkbox" name="va|'.$rep["id"].'"'.$checked[3].' /></td>
		<td><input type="checkbox" name="sa|'.$rep["id"].'"'.$checked[4].' /></td>
		<td><input type="checkbox" name="cdt|'.$rep["id"].'"'.$checked[5].' /></td>
		<td><input type="checkbox" name="ee|'.$rep["id"].'"'.$checked[6].' /></td>
		<td><input type="checkbox" name="te|'.$rep["id"].'"'.$checked[7].' /></td>
		<td><input type="checkbox" name="suppr|'.$rep["id"].'" /></td>
	</tr>
	<tr style="background-color: white;"><td colspan="9"></td></tr>';
	}
}

// On traite la partie sur les utilisateurs 'autre' pour leur définir le bon statut

	// On traite les demandes de l'admin sur la définition des statuts des utilisateurs 'autre'
	if ($action == "defStatut") {
		// On vérifie si cet utilisateur existe déjà
		$query_v2 = mysql_query("SELECT id_statut FROM droits_utilisateurs WHERE login_user = '".$login_user."'")
									OR trigger_error('Impossible de vérifier le statut privé de cet utilisateur.', E_USER_WARNING);
		$verif_v2 = mysql_num_rows($query_v2);
		if ($verif_v2 >= 1) {
			// alors le statut de cet utilisateur existe, on va donc le mettre à jour
			$sql_d = "UPDATE droits_utilisateurs SET id_statut = '".$statut_user."' WHERE login_user = '".$login_user."'";
		}else{
			$sql_d = "INSERT INTO droits_utilisateurs (id, id_statut, login_user) VALUES ('', '".$statut_user."', '".$login_user."')";
		}

		$query_statut = mysql_query($sql_d) OR trigger_error('Impossible d\'enregistrer dans la base.'.mysql_error(), E_USER_WARNING);

		if ($query_statut) {
			$msg2 .= '<h4 style="color: green;">Modification enregistrée.</h4>';
		}

	}

	// On récupère les utilisateurs qui ont un statut 'autre'
	$sql_u = "SELECT nom, prenom, login  FROM utilisateurs
											WHERE statut = 'autre'
											AND etat = 'actif'";
	$query_u = mysql_query($sql_u);

	// On affiche la liste des utilisateurs avec un select des statuts privés
	$i = 1;
	while($tab = mysql_fetch_array($query_u)){

		// On récupère son statut s'il existe
		$query_s = mysql_query("SELECT id_statut FROM droits_utilisateurs WHERE login_user = '".$tab["login"]."'");
		$statut = mysql_result($query_s, "id_statut");

		$aff_users .= '
		<tr>
			<td>'.$tab["nom"].' '.$tab["prenom"].'</td>
			<td>
		<form name="form'.$i.'" action="creer_statut.php" method="post">
			<input type="hidden" name="action" value="defStatut" />
			<input type="hidden" name="login_user" value="'.$tab["login"].'" />

			<select name="statut_user" onchange=\'document.form'.$i.'.submit();\'>
				<option value="rien">Choix du statut</option>';

		$sql = "SELECT id, nom_statut FROM droits_statut ORDER BY nom_statut";
		$query = mysql_query($sql);
		while($rep = mysql_fetch_array($query)){
			if ($statut == $rep["id"]) {
				$selected = ' selected="selected"';
			}else{
				$selected = '';
			}
			$aff_users .= '
				<option value="'.$rep["id"].'"'.$selected.'>'.$rep["nom_statut"].'</option>';
		}

		$aff_users .= '
			</select>
		</form>
		</td></tr>';

		$i++;

	}



?>
<!-- Début de la page sur les statut privés -->

<br />

<p class="bold">
<a href="index.php?mode=personnels"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>

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
		<tr><td colspan="4"><?php echo $msg3; ?></td><td>-</td><td colspan="4"><input type="submit" name="modifier" value="Enregistrer et mettre &agrave; jour" /></td></tr>
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

	<p style="text-align: right; font-style: italic; color: grey; background-color: lightblue;">Gestion des statuts priv&eacute;s</p>

	<table>

		<?php echo $aff_users; ?>
	</table>
		<?php echo $msg2; ?>
</div>

<?php
require("../lib/footer.inc.php");