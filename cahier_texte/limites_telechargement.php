<?php
/*
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//On vérifie si le module est activé
if (!acces_cdt()) {
	die("Le module n'est pas activé.");
}

//**************** EN-TETE *****************
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************
$max_size_ko = getSettingValue("max_size")/1024;
$total_max_size_ko = getSettingValue("total_max_size")/1024;

$current_group = get_group($_GET['id_groupe']);

?>
<h1 class='gepi'>GEPI - Limites et restrictions concernant le téléchargement de fichiers</h1>

<h2>Taille maximale d'un fichier</h2>
<p>La taille maximale autorisée pour un fichier est : <b><?php echo $max_size_ko." Ko</b>"; ?></p>
<h2>Espace disque autorisé</h2>
<?php
$query = "SELECT DISTINCT sum(taille) somme FROM ct_documents d, ct_entry e WHERE (e.id_groupe='".$_GET['id_groupe']."' and e.id_ct = d.id_ct)";
$result = round(sql_query1($query)/1024,1);
echo "<p>L'espace disque maximal autorisé pour le groupe <i>".$current_group["description"]."</i> est : <b>".$total_max_size_ko." Ko</b>.";

echo "<p>La taille totale des fichiers actuellement stockés pour le groupe <i>".$current_group["description"]."</i> est : <b>".$result." Ko</b>.</p>";

?>

<H2>Types de fichiers autorisés en téléchargement</h2>
<p>Ci-dessous le tableau des extensions autorisées. Si vous souhaitez faire ajouter une ou plusieurs extensions,
<?php
         echo("<a href=\"javascript:centrerpopup('../gestion/contacter_admin.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">contactez l'administrateur</a>");
?>
</p>
<center>
<table style="border-style:solid; border-width:1px; border-color: #6F6968;" width="400"><tr style="background-color:#BFBFBF;"><td style="text-align: center; font-weight: bold; width: 100px">Extension</td><td style="text-align: center; font-weight: bold; width: 300px">Type</td></tr>
<?php
$query = "SELECT extension, titre FROM ct_types_documents WHERE upload='oui' ORDER BY extension";
$result = sql_query($query);
    $ic='1';
for ($i=0; ($row=sql_row($result,$i)); $i++) {
    $ext = $row[0];
    $titre = $row[1];
    if ($ic=='1') { $ic='2'; $couleur_cellule_="#EFEFEF"; } else { $couleur_cellule_="#DFDFDF"; $ic='1'; }
    echo "<tr style=\"background-color: $couleur_cellule_;\"><td>".$ext."</td><td>".$titre."</td></tr>";
}
?>
</table>
</center>
<?php require("../lib/footer.inc.php");?>
