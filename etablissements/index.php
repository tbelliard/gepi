<?php
/*
 * $Id$
 *
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

//**************** EN-TETE *****************
$titre_page = "Gestion des établissements";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>

<p class=bold>
<a href="../accueil_admin.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
 | <a href="modify_etab.php">Ajouter un établissement</a>
 | <a href="import_etab_csv.php">Importer un fichier d'établissements</a>
</p>
<p>Les données de la base établissement servent à l'affichage de l'établissement d'origine des <?php echo $gepiSettings['denomination_eleves'];?>
 sur les documents tels que les bulletins simplifiés.</p>
<?php
// On va chercher les établissements déjà existant, et on les affiche.
if (!isset($order_by)) {$order_by = "id";}
$call_data = mysql_query("SELECT * FROM etablissements ORDER BY $order_by");
$nombre_lignes = mysql_num_rows($call_data);

$res='';
if ($nombre_lignes == 1) $res = sql_query1("SELECT id FROM etablissements WHERE id='999'");
if (($nombre_lignes == 0) or ($res=='999')) {
    echo "<p><b>Actuellement aucun établissement n'est présent dans la base.</b>
    <br /><br />Avant de procéder à l'importation des fichiers GEP ou à l'ajout manuel d'élèves dans la base, il est conseillé
    de constituer la base des établissements d'où proviennent en majorité vos élèves.
    <br />Vous pouvez pour cela <a href=\"import_etab_csv.php\">importer directement le fichier d'établissements</a> de votre académie.
    <hr /><br />\n";
}

if ($nombre_lignes == 0) {
	require("../lib/footer.inc.php");
    die();
}

?>
<table width='100%' class='boireaus' cellpadding='5' summary='Etablissements'>
<tr>
    <?php
    echo "<th><p class='bold'><a href='index.php?order_by=id'>Identifiant</a></p></th>\n";
    echo "<th><p class='bold'><a href='index.php?order_by=nom'>Nom</a></p></th>\n";
    echo "<th><p class='bold'><a href='index.php?order_by=niveau'>Niveau</a></p></th>\n";
    echo "<th><p class='bold'><a href='index.php?order_by=type'>type</a></p></th>\n";
    echo "<th><p class='bold'><a href='index.php?order_by=cp'>Code postal</a></p></th>\n";
    echo "<th><p class='bold'><a href='index.php?order_by=ville'>Ville</a></p></th>\n";
    echo "<th><p class='bold'>Supprimer</p></th>\n";
    ?>
</tr>
<?php

$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];
$i = 0;
$alt=1;
while ($i < $nombre_lignes){
	$alt=$alt*(-1);

    $current_id = mysql_result($call_data, $i, "id");
    $current_nom = mysql_result($call_data, $i, "nom");
    $current_niveau = mysql_result($call_data, $i, "niveau");
    foreach ($type_etablissement as $type_etab => $nom_etablissement) {
        if ($current_niveau == $type_etab) {$current_niveau_nom = $nom_etablissement;}
    }
    $current_type = mysql_result($call_data, $i, "type");
    if ($current_type == 'aucun'){
        $current_type = '';
	}
    else{
        if(isset($type_etablissement2[$current_type][$current_niveau])){
			$current_type = $type_etablissement2[$current_type][$current_niveau];
		}
		else{
			$current_type = '';
		}
    }
	$current_cp = mysql_result($call_data, $i, "cp");
    $current_ville = mysql_result($call_data, $i, "ville");
    echo "<tr class='lig$alt white_hover'><td><a href='modify_etab.php?id=$current_id'>$current_id</a></td>\n";
    echo "<td>$current_nom</td>\n";
    echo "<td>$current_niveau_nom</td>\n";
    echo "<td>$current_type</td>\n";
    echo "<td>";
	if($current_cp!='999') {echo sprintf("%05d",$current_cp);} else {echo $current_cp;}
	echo "</td>\n";
    echo "<td>$current_ville</td>\n";
    echo "<td><a href='../lib/confirm_query.php?liste_cible=$current_id&amp;action=del_etab".add_token_in_url()."'>Supprimer</a></td></tr>\n";
	$i++;
}
echo "</table>\n";
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");

?>