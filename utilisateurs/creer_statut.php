<?php

/**
 *
 * Modif table `droits` : ALTER TABLE `droits` ADD `autre` VARCHAR( 1 ) NOT NULL DEFAULT 'F' AFTER `secours` ;
 * @copyright 2008-2013
 */
$affiche_connexion = 'yes';
$niveau_arbo = 1;

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

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

//debug_var();

if (getSettingValue("statuts_prives") != "y") {
	trigger_error('Impossible d\'accéder à ce module de Gepi.', E_USER_ERROR);
}


// ========================================= Variables ============================
$action = isset($_POST["action"]) ? $_POST["action"] : NULL;
$nouveau_statut = isset($_POST["news"]) ? $_POST["news"] : NULL;
$login_user = isset($_POST["userid"]) ? $_POST["userid"] : NULL;
$statut_user = isset($_POST["userstat"]) ? $_POST["userstat"] : NULL;
$msg = $msg2 = $msg3 = NULL;

// Ces tableaux définissent les différents fichiers à autoriser en fonction du statut
$values_b = '';
// La liste des fichiers à traiter
require_once('./creer_statut_autorisation.php');

//print_r($autorise);

// Fonction qui permet d'afficher  le selected de l'affichage
function verifChecked($id){

	// On utilise les variables définies dans /creer_statut_autorisation.php
	global $autorise;
	global $iter;

	for($i = 1 ; $i < $iter ; $i++){
		// On récupère les droits de ce statut privé
		$sql_ds = "SELECT autorisation FROM droits_speciaux WHERE id_statut = '".$id."' AND nom_fichier = '".$autorise[$i][0]."'";
		$query_ds = mysqli_query($GLOBALS["mysqli"], $sql_ds) OR trigger_error('Erreur dans la fonction verifChecked ', E_USER_ERROR);
		$count = mysqli_num_rows($query_ds);
		if ($count >= 1) {
			$rep = old_mysql_result($query_ds, 0,"autorisation");
		}else{
			$rep = 'F';
		}

			// echo $sql_ds.' '.$rep.'<br />'; // debug
		if ($rep == 'V') {
			$retour[$i] = ' checked="checked"';
		}else{
			$retour[$i] = '';
		}
	}

	return $retour;
}

if ($action == 'ajouter') {
	check_token();

	// on fait quelques vérifications sur le nom du statut (si il existe déjà, longueur du nom, enlever les ' et les ",...)
	// On ne garde que les 12 premières lettres
	$stat_1 = mb_substr(trim($nouveau_statut), 0, 12);
	// On enlève les accents, les apostrophes et les guillemets
	$stat_2 = str_replace("\\", "", $stat_1);
	$stat_2b = str_replace('"', '', $stat_2);
	$stat_3 = remplace_accents($stat_2b, "all");

	// On refait une ultime vérification
	$insert_statut = htmlspecialchars($stat_3, ENT_QUOTES);

	// On ajoute le statut privé après avoir vérifié qu'il n'existe pas déjà
	$query_v = mysqli_query($GLOBALS["mysqli"], "SELECT id FROM droits_statut WHERE nom_statut = '".$insert_statut."'");
	$verif = mysqli_num_rows($query_v);

	if ($verif >= 1) {

		$msg .= "<h3 class='red'>Ce statut priv&eacute; existe d&eacute;j&agrave; !</h3>";

	}else{

		$sql = "INSERT INTO droits_statut (id, nom_statut) VALUES ('', '".$insert_statut."')";
		$enregistre = mysqli_query($GLOBALS["mysqli"], $sql) OR trigger_error('Impossible d\'enregistrer ce nouveau statut', E_USER_WARNING);
		$cherche_id = mysqli_query($GLOBALS["mysqli"], "SELECT id FROM droits_statut WHERE nom_statut = '".$insert_statut."'");
		$last_id = old_mysql_result($cherche_id, 0,"id");

		if ($enregistre) {

			// On enregistre les droits généraux adéquats avec la virgule qui va bien entre chaque value
			// Chaque droit correspond à un ensemble d'autorisations sur un ou plusieurs fichiers
			// Pour ajouter des droits, il suffit d'ajouter des branches au tableau $autorise du fichier creer_statut_autorisation avec tous les fichiers utiles

			for($a = 0 ; $a < $iter ; $a++){ // $iter est définie dans creer_statut_autorisation.php
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
				if ($a < ($iter - 1)) {
					$values_b .= ', ';
				}
			}

 			$autorise_b = mysqli_query($GLOBALS["mysqli"], "INSERT INTO droits_speciaux (id, id_statut, nom_fichier, autorisation) VALUES ".$values_b."")
			 										OR trigger_error('Impossible d\'enregistrer : '.$values_b.' : '.mysqli_error($GLOBALS["mysqli"]), E_USER_WARNING);

			if ($autorise_b) {
				$msg .= '<h4 style="color: green;">Ce statut est enregistr&eacute !</h4>';
			}

		}

	}

} // if ($action == 'ajouter')

if ($action == 'modifier') {
	check_token();

	// On initialise toutes les variables envoyées
	$sql = "SELECT id, nom_statut FROM droits_statut ORDER BY nom_statut";
	//echo "$sql<br />";
	$query = mysqli_query($GLOBALS["mysqli"], $sql) OR trigger_error('Erreur '.$sql, E_USER_ERROR);
	$nbre = mysqli_num_rows($query);

	$test=array();
	for($a = 0; $a < $nbre; $a++){

		$b = old_mysql_result($query, $a, "id");

		$test[$a][0] = isset($_POST["suppr|".$b]) ? $_POST["suppr|".$b] : NULL;
		$test[$a][1] = isset($_POST["ne|".$b]) ? $_POST["ne|".$b] : NULL;
		$test[$a][2] = isset($_POST["bs|".$b]) ? $_POST["bs|".$b] : NULL;
		$test[$a][3] = isset($_POST["va|".$b]) ? $_POST["va|".$b] : NULL;
		$test[$a][4] = isset($_POST["sa|".$b]) ? $_POST["sa|".$b] : NULL;
		$test[$a][5] = isset($_POST["cdt|".$b]) ? $_POST["cdt|".$b] : NULL;
		$test[$a][6] = isset($_POST["cdt_visa|".$b]) ? $_POST["cdt_visa|".$b] : NULL;
		$test[$a][7] = isset($_POST["ee|".$b]) ? $_POST["ee|".$b] : NULL;
		$test[$a][8] = isset($_POST["te|".$b]) ? $_POST["te|".$b] : NULL;
		$test[$a][9] = isset($_POST["pa|".$b]) ? $_POST["pa|".$b] : NULL;
		$test[$a][10] = isset($_POST["ve|".$b]) ? $_POST["ve|".$b] : NULL;
		$test[$a][11] = isset($_POST["vre|".$b]) ? $_POST["vre|".$b] : NULL;
		$test[$a][12] = isset($_POST["vee|".$b]) ? $_POST["vee|".$b] : NULL;
		$test[$a][13] = isset($_POST["vne|".$b]) ? $_POST["vne|".$b] : NULL;
		$test[$a][14] = isset($_POST["vbe|".$b]) ? $_POST["vbe|".$b] : NULL;
		$test[$a][15] = isset($_POST["vae|".$b]) ? $_POST["vae|".$b] : NULL;
		$test[$a][16] = isset($_POST["anna|".$b]) ? $_POST["anna|".$b] : NULL;
		$test[$a][17] = isset($_POST["tr|".$b]) ? $_POST["tr|".$b] : NULL;
		$test[$a][18] = isset($_POST["dsi|".$b]) ? $_POST["dsi|".$b] : NULL;
		$test[$a][19] = isset($_POST["abs|".$b]) ? $_POST["abs|".$b] : NULL;
        $test[$a][20] = isset($_POST["abs_saisie|".$b]) ? $_POST["abs_saisie|".$b] : NULL;
        $test[$a][21] = isset($_POST["abs_bilan|".$b]) ? $_POST["abs_bilan|".$b] : NULL;
        $test[$a][22] = isset($_POST["abs_totaux|".$b]) ? $_POST["abs_totaux|".$b] : NULL;
        $test[$a][23] = isset($_POST["bul_print|".$b]) ? $_POST["bul_print|".$b] : NULL;
        $test[$a][24] = isset($_POST["visu_equipes_peda|".$b]) ? $_POST["visu_equipes_peda|".$b] : NULL;
        $test[$a][25] = isset($_POST["visu_listes_ele|".$b]) ? $_POST["visu_listes_ele|".$b] : NULL;
        $test[$a][26] = isset($_POST["listes_ele_csv|".$b]) ? $_POST["listes_ele_csv|".$b] : NULL;

		// On assure les différents traitements
		if ($test[$a][0] == 'on') {
			// On supprime le statut demandé
			$sql_d = "DELETE FROM droits_statut WHERE id = '".$b."'";
			$query_d = mysqli_query($GLOBALS["mysqli"], $sql_d) OR trigger_error('Impossible de supprimer ce statut : '.mysqli_error($GLOBALS["mysqli"]), E_USER_NOTICE);

			// Il faut aussi effacer toutes les références à ce statut dans les autres tables
			$sql_d = "DELETE FROM droits_utilisateurs WHERE id_statut = '".$b."'";
			$query_d = mysqli_query($GLOBALS["mysqli"], $sql_d) OR trigger_error('Impossible de supprimer ce statut du : '.mysqli_error($GLOBALS["mysqli"]), E_USER_NOTICE);

			$sql_d = "DELETE FROM droits_speciaux WHERE id_statut = '".$b."'";
			$query_d = mysqli_query($GLOBALS["mysqli"], $sql_d) OR trigger_error('Impossible de supprimer ce statut ds : '.mysqli_error($GLOBALS["mysqli"]), E_USER_NOTICE);

		}else{
                  // On va vérifier les droits un par un
                  // ne = notes élèves ; bs = bulletins simplifiés ; va = voir absences ; sa = saisir absences
                  // cdt = cahier de textes ; ee = emploi du temps des élèves ; te = tous les emplois du temps

                  for($m = 1 ; $m < $iter ; $m++){

                    $nbre2 = count($autorise[$m]);
                    // On vérifie si le droit est coché ou non
                    if ($test[$a][$m] == 'on') {
                      $vf = 'V';
                    }else{
                      $vf = 'F';
                    }
                    // On n'oublie pas de mettre à jour tous les fichiers adéquats
                    for($i = 0 ; $i < $nbre2 ; $i++){
                      //$sql_maj = "UPDATE droits_speciaux SET autorisation = '".$vf."' WHERE id_statut = '".$b."' AND nom_fichier = '".$autorise[$m][$i]."'";
                      //$query_maj = mysql_query($sql_maj) OR trigger_error("Mauvaise mise à jour  : ".mysql_error(), E_USER_WARNING);
                      $query_select = mysqli_query($GLOBALS["mysqli"], "SELECT id FROM droits_speciaux WHERE id_statut = '".$b."' AND nom_fichier = '".$autorise[$m][$i]."'");
                      $result = mysqli_fetch_array($query_select);
                      if (!empty ($result)){
                        $query_maj = mysqli_query($GLOBALS["mysqli"], "UPDATE droits_speciaux SET autorisation = '".$vf."' WHERE id_statut = '".$b."' AND nom_fichier = '".$autorise[$m][$i]."'");
                      }else{
                        $query_maj = mysqli_query($GLOBALS["mysqli"], "INSERT INTO `droits_speciaux` VALUES ('','".$b."','".$autorise[$m][$i]."','".$vf."')");
                      }

                      if (!$query_maj) {
			$msg3 .= '<span class="red">Erreur</span>';
                      }
                    }
                  } // for($m = 1 ; $m < $iter ; $m++){
		}
	}
//print_r($test);
	// On assure un message de confirmation si les modifications se sont bien passées
	if ($msg3 === NULL) {
		$msg3 .= '<p style="color: green">Les modifications sont bien enregistrées.</p>';
	}
}



// On récupère tous les statuts nouveaux qui existent
$aff_tableau = $aff_select = $aff_users = $selected = '';
$sql = "SELECT id, nom_statut FROM droits_statut ORDER BY nom_statut";
$query = mysqli_query($GLOBALS["mysqli"], $sql);
$nbre_statuts = mysqli_num_rows($query);

if ($query) {

	for($b = 0 ; $b <= $iter ; $b++){

		if ($b == 0) {

			$aff_tableau2[$b] = '<tr style="border: 1px solid white; text-align: center;"><td style="font-weight: bold;">Liste des droits</td>';

		}elseif($b == $iter){

			// On ajoute une ligne pour la suppression
			$aff_tableau2[$b] = '<tr style="border: 1px solid white; background-color: silver; text-align: center;"><td>Supprimer ce statut</td>';

		}else{

			$aff_tableau2[$b] = '<tr style="border: 1px solid white; text-align: center;"><td>'.$menu_accueil[$b][1].'</td>';

		}
	}

	while($rep = mysqli_fetch_array($query)){

		// On vérifie s'il faut le cocher par défaut ou pas
		$checked = verifChecked($rep["id"]);

	// On affiche les droits des statuts personnalisés verticalement

	for($b = 0 ; $b <= $iter ; $b++){

		if ($b == 0) {

			$aff_tableau2[$b] .= '<td style="font-weight: bold; color: red;">'.$rep["nom_statut"].'</td>';

		}elseif($b == $iter){

			// On ajoute une ligne pour la suppression
			$aff_tableau2[$b] .= '<td><input type="checkbox" name="suppr|'.$rep["id"].'" /></td>';

		}else{

			$aff_tableau2[$b] .= '<td><input type="checkbox" name="'.$menu_accueil[$b][2].'|'.$rep["id"].'"'.$checked[$b].' /></td>';

		}
	}

	}
	for($b = 0 ; $b <= $iter ; $b++){

		// $aff_tableau2[$b] .= '</tr>'."\n";
		$aff_tableau .= $aff_tableau2[$b].'</tr>
							<tr style="background-color: white;"><td colspan="'.($nbre_statuts + 1).'"></td></tr>'."\n";

	}
}

// On traite la partie sur les utilisateurs 'autre' pour leur définir le bon statut

	// On traite les demandes de l'admin sur la définition des statuts des utilisateurs 'autre'
	if ($action == "defStatut") {
		check_token();

		// On vérifie si cet utilisateur existe déjà
		$query_v2 = mysqli_query($GLOBALS["mysqli"], "SELECT id_statut FROM droits_utilisateurs WHERE login_user = '".$login_user."'")
									OR trigger_error('Impossible de vérifier le statut privé de cet utilisateur.', E_USER_WARNING);
		$verif_v2 = mysqli_num_rows($query_v2);
		if ($verif_v2 >= 1) {
			// alors le statut de cet utilisateur existe, on va donc le mettre à jour
			$sql_d = "UPDATE droits_utilisateurs SET id_statut = '".$statut_user."' WHERE login_user = '".$login_user."'";
		}else{
			$sql_d = "INSERT INTO droits_utilisateurs (id, id_statut, login_user) VALUES ('', '".$statut_user."', '".$login_user."')";
		}

		$query_statut = mysqli_query($GLOBALS["mysqli"], $sql_d) OR trigger_error('Impossible d\'enregistrer dans la base.'.mysqli_error($GLOBALS["mysqli"]), E_USER_WARNING);

		if ($query_statut) {
			$msg2 .= '<h4 style="color: green;">Modification enregistrée.</h4>';
		}

	}

	// On récupère les utilisateurs qui ont un statut 'autre'
	$sql_u = "SELECT nom, prenom, login  FROM utilisateurs
											WHERE statut = 'autre'
											AND etat = 'actif'
											ORDER BY nom, prenom";
	$query_u = mysqli_query($GLOBALS["mysqli"], $sql_u);

	// On affiche la liste des utilisateurs avec un select des statuts privés
	$i = 1;
	while($tab = mysqli_fetch_array($query_u)){

		// On récupère son statut s'il existe
		$query_s = mysqli_query($GLOBALS["mysqli"], "SELECT id_statut FROM droits_utilisateurs WHERE login_user = '".$tab["login"]."'");
		$statut = mysqli_fetch_array($query_s);

		$aff_users .= '
		<tr>
			<td><a href="modify_user.php?user_login='.$tab["login"].'" target="_blank" title="Voir/modifier dans un nouvel onglet/fenêtre le compte de l\'utilisateur.">'.$tab["nom"].' '.$tab["prenom"].'</a></td>
			<td>
		<form id="form'.$i.'" action="creer_statut.php" method="post">'."\n";

		$aff_users .= add_token_field();

		$aff_users .= '
			<p><input type="hidden" name="action" value="defStatut" />
			<input type="hidden" name="userid" value="'.$tab["login"].'" />

			<select name="userstat" onchange=\'document.getElementById("form'.$i.'").submit();\'>
				<option value="rien">Choix du statut</option>';

		$sql = "SELECT id, nom_statut FROM droits_statut ORDER BY nom_statut";
		$query = mysqli_query($GLOBALS["mysqli"], $sql);
		while($rep = mysqli_fetch_array($query)){
			if ($statut["id_statut"] == $rep["id"]) {
				$selected = ' selected="selected"';
			}else{
				$selected = '';
			}
			$aff_users .= '
				<option value="'.$rep["id"].'"'.$selected.'>'.$rep["nom_statut"].'</option>';
		}

		$aff_users .= '
			</select></p>
		</form>
		</td></tr>';

		$i++;

	}

//	include("utilisateurs.class.php");
//======================================================
$titre_page = 'Gestion des statuts personnalis&eacute;s';
$style_specifique = "utilisateurs/style_statut";
include("../lib/header.inc.php");
//======================================================

//debug_var();

?>
<!-- Début de la page sur les statut privés -->

<br />

<p class="bold">
<a href="index.php?mode=personnels"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>

<p>Vous pouvez définir des statuts personnalisés, ayant une combinaison particulière de droits.
 Pour pouvoir ensuite attribuer (ci-dessous) un statut personnalisé à un utilisateur, il faut d'abord l'enregistrer avec un statut générique "autre"
 (<a href="./modify_user.php">CREER un personnel</a> ou <a href="./index.php?mode=personnels">MODIFIER un personnel</a>).</p>

<!-- Quel statut pour quelle personne ? -->
<div style="width: 350px; -moz-border-radius: 20px; background-color: lightblue; padding: 5px; position: absolute; margin-left: 880px; margin-top: 10px;">

<div id="userStatut" style="border: 5px solid silver; width: 22em; margin: 5px 5px 5px 5px;">

	<p style="text-align: right; font-style: italic; color: grey; background-color: lightblue;">Gestion des statuts personnalis&eacute;s&nbsp;&nbsp;</p>

	<table>

		<?php echo $aff_users; ?>
	</table>
		<?php echo $msg2; ?>
</div>

</div>

<div style="background-color: lightblue; width: 850px; -moz-border-radius: 20px; padding: 5px;">
<p style="color: grey; text-align: right; font-style: italic;">Gestion des droits des statuts personnalis&eacute;s&nbsp;&nbsp;</p>

<form action="creer_statut.php" method="post">
<?php
echo add_token_field();
?>
	<p><input type="hidden" name="action" value="modifier" /></p>

<table style="border: 1px solid white; background: #CCFFFF;">
	<thead>
		<tr><th>&nbsp;</th><th colspan="<?php echo $nbre_statuts; ?>">&nbsp;</th></tr>
	</thead>
	<tfoot>
		<tr><td><?php echo $msg3; ?></td><td colspan="<?php echo $nbre_statuts; ?>"><input type="submit" name="modifier" value="Enregistrer et mettre &agrave; jour" /></td></tr>
	</tfoot>

	<tbody>

		<?php echo $aff_tableau; ?>
		<tr style="background-color: white;"><td colspan="<?php echo ($nbre_statuts + 1); ?>"></td></tr>
	</tbody>

</table>

</form>

</div>

<br />

<p class="ajoutSt" onclick="changementDisplay('ajoutStatut', '');">Ajouter un statut personnalis&eacute;</p>
<div id="ajoutStatut" style="display: none;">

	<form action="creer_statut.php" method="post">
<?php
echo add_token_field();
?>
		<p>
		<label for="new">Nom du nouveau statut</label>
		<input type="text" id="new" name="news" value="" />
		<input type="hidden" name="action" value="ajouter" />

		<input type="submit" name="Ajouter" value="Ajouter" />
		</p>

		<p style="color: grey; font-style: italic; margin-left: 10em;">Lettres et chiffres uniquement. Longueur maximum : 12 caract&egrave;res.</p>

	</form>

</div>

<br />
<hr />
<br />


<?php
require("../lib/footer.inc.php");
