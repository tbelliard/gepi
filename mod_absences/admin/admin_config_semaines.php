<?php
/*
 *
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../lib/functions.php");

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
};

// Check access
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}
// header
$titre_page = "Définition des types de semaine de l'établissement";
require_once("../../lib/header.inc");



	if (empty($_GET['action_sql']) and empty($_POST['action_sql'])) {$action_sql="";}
	   else { if (isset($_GET['action_sql'])) {$action_sql=$_GET['action_sql'];} if (isset($_POST['action_sql'])) {$action_sql=$_POST['action_sql'];} }
	if (empty($_GET['action']) and empty($_POST['action'])) {exit();}
	   else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
	if (empty($_GET['num_semaine']) and empty($_POST['num_semaine'])) { $num_semaine = ''; }
	   else { if (isset($_GET['num_semaine'])) { $num_semaine = $_GET['num_semaine']; } if (isset($_POST['num_semaine'])) { $num_semaine = $_POST['num_semaine']; } }
	if (empty($_GET['type_semaine']) and empty($_POST['type_semaine'])) { $type_semaine = ''; }
	   else { if (isset($_GET['type_semaine'])) { $type_semaine = $_GET['type_semaine']; } if (isset($_POST['type_semaine'])) { $type_semaine = $_POST['type_semaine']; } }

// ajout et mise à jour de la base
if ( $action_sql === 'ajouter' or $action_sql === 'modifier' )
{
	$i = '0';
	while ( $i < '52' )
	{
		if( isset($num_semaine[$i]) and !empty($num_semaine[$i]) )
		{
        	        $test_num_semaine = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."edt_semaines WHERE num_edt_semaine = '".$num_semaine[$i]."'"),0);
			$num_edt_semaine = $num_semaine[$i];
			$type_edt_semaine = $type_semaine[$i];

			if ( $test_num_semaine === '0' ) { $requete = "INSERT INTO ".$prefix_base."edt_semaines (num_edt_semaine, type_edt_semaine) VALUES ('".$num_edt_semaine."', '".$type_edt_semaine."')"; }
			if ( $test_num_semaine != '0' ) { $requete = "UPDATE ".$prefix_base."edt_semaines SET type_edt_semaine = '".$type_edt_semaine."' WHERE num_edt_semaine = '".$num_edt_semaine."'"; }
	                mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
		}

	$i = $i + 1;
	}

}


// prendre les donnees de la base
if ( $action === 'visualiser' )
{
        $i = '0';
        $requete = "SELECT * FROM ".$prefix_base."edt_semaines";
        $resultat = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
        while ( $donnee = mysql_fetch_array ($resultat))
	{
		$num_semaine[$i] = $donnee['num_edt_semaine'];
		$type_semaine[$i] = $donnee['type_edt_semaine'];
		$i = $i + 1;
        }
}

// Gestion propre des retours vers absences ou EdT
	if (isset($_SESSION["retour"]) AND $_SESSION["retour"] !== "") {
		$retour = "<a href=\"../../edt_organisation/".$_SESSION["retour"].".php\">";
	}else{
		$retour = "<a href=\"./index.php\">";
	}

echo "<p class=bold>".$retour."<img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
?>

<?php if ($action === "visualiser") { ?>
<? /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align: center;">

<h2>Définition des types de semaines</h2>

	<form method="post" action="admin_config_semaines.php?action=<?php echo $action; ?>" name="form1">
		<input type="submit" name="submit" value="Enregistrer" />

<br /><br />

<?php /* gestion des horaire d'ouverture */
// On considère que la 32e semaine commence le 6 août 2007
// En timestamp Unix GMT, cette date vaut 1186358400 secondes
// RAPPEL : une journée a 86400 secondes et une semaine en a 604800

function trouverDates($numero_semaine){
	// fonction qui permet de déterminer la date de début de la semaine (lundi)
	$ts_depart = 1186358400;
	if ($numero_semaine == 32) {
		$ts = $ts_depart;
	}elseif ($numero_semaine > 32 AND $numero_semaine <= 52) {
		$coef_multi = $numero_semaine - 32;
		$ts = $ts_depart + ($coef_multi * 604800);
	}elseif ($numero_semaine < 32 AND $numero_semaine >= 1) {
		$coef_multi = (52 - 32) + $numero_semaine;
		$ts = $ts_depart + ($coef_multi * 604800);
	}else {
		$ts = "";
	}
	return $ts;
}

?>
	<table cellpadding="0" cellspacing="1" class="tab_table">
	<tbody>
		<tr>
			<th class="tab_th" style="width: 100px;">Semaine n° (officiel)</th>
			<th class="tab_th" style="width: 100px;">Type</th>
			<th class="tab_th" style="width: 200px;">Du</th>
			<th class="tab_th" style="width: 200px;">au</th>
		</tr>
    <?php
		$i = '31';
		$ic = '1';
		$fin = '52';
	    while ( $i < $fin ) {
			if ($ic === '1') {
				$ic = '2';
				$couleur_cellule = 'couleur_ligne_1';
			} else {
				$couleur_cellule = 'couleur_ligne_2';
				$ic = '1';
			}
	?>
		<tr class="<?php echo $couleur_cellule; ?>">
			<td><input type="hidden" name="num_semaine[<?php echo $i; ?>]" value="<?php echo $num_semaine[$i]; ?>" /><strong><?php echo $num_semaine[$i]; ?></strong></td>
			<td><input name="type_semaine[<?php echo $i; ?>]" size="3" maxlength="10"  value="<?php if ( isset($type_semaine[$i]) and !empty($type_semaine[$i]) ) { echo $type_semaine[$i]; } ?>" class="input_sans_bord" /></td>
			<td> lundi <?php echo gmdate("d-m-Y", trouverDates($i+1)); ?> </td>
			<td> samedi <?php echo gmdate("d-m-Y", (trouverDates($i+1) + 6*86400)); ?> </td>

		</tr>
	<?php
			if ($i == '51') {
				$i = '0';
				$fin = '31';
			} else {
				$i = $i + 1;
			}
		} // fin du while ( $i < '52'...
	?>
	</tbody>
	</table>
<br />
			<input type="hidden" name="action_sql" value="modifier" />
			<input type="submit" name="submit" value="Enregistrer" />
	</form>
<br /><br />
<?php /* fin de gestion des horaire d'ouverture */ ?>

<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php mysql_close(); }

require("../../lib/footer.inc.php");

?>

