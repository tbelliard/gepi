<?php
/*
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//**************** EN-TETE **************************************
$titre_page = "Gestion des classes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];
?>
<p class=bold>
<a href="../accueil_admin.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>
| <a href="modify_nom_class.php">Ajouter une classe</a>
<!--|<a href='duplicate_class.php'>Scinder une classe</a> ## Cette fonction n'est plus utile ##-->
 | <a href='classes_param.php'>Paramétrage de plusieurs classes par lots</a>
 | <a href='cpe_resp.php'>Paramétrage rapide CPE Responsable</a>
 | <a href='scol_resp.php'>Paramétrage scolarité</a>|


</p>
<?php
// On va chercher les classes déjà existantes, et on les affiche.
$call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
$nombre_lignes = mysql_num_rows($call_data);
if ($nombre_lignes != 0) {
    $flag = 1;
    echo "<table cellpadding=3 cellspacing=0 style='border: none; border-collapse: collapse;'>";
    $i = 0;
    while ($i < $nombre_lignes){
        $id_classe = mysql_result($call_data, $i, "id");
        $classe = mysql_result($call_data, $i, "classe");
        echo "<tr";
        if ($flag==1) { echo " class='fond_sombre'"; $flag = 0;} else {$flag=1;};
        echo "><td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB; border-right: 1px solid #BBBBBB;'>";
		echo "<b>$classe</b> ";
        echo "</td>";
		echo "<td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB; border-right: 1px solid #BBBBBB;'>";
		echo "<a href='periodes.php?id_classe=$id_classe'><img src='../images/icons/date.png' alt=''> Périodes</a></td>";
		//echo "<td>|<a href='modify_class.php?id_classe=$id_classe'>Gérer les matières</a></td>";
		echo "<td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB; border-right: 1px solid #BBBBBB;'>";
        $nb_per = mysql_num_rows(mysql_query("select id_classe from periodes where id_classe = '$id_classe'"));
        if ($nb_per != 0) {
            echo "<a href='classes_const.php?id_classe=$id_classe'><img src='../images/icons/edit_user.png' alt=''> Élèves</a></td><td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB;'>";
        } else {
            echo "&nbsp;</td><td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB;'>";
        }
        if ($nb_per != 0) {
            echo "<a href='../groupes/edit_class.php?id_classe=$id_classe'> <img src='../images/icons/document.png' alt=''> Enseignements</a></td>";
			echo "<td style='padding: 5px; padding-right: 10px; border-right: 1px solid #BBBBBB;'>";
        } else {
            echo "&nbsp;</td><td>";
        }

	//=======================================
	// MODIF: boireaus
	// Ajout d'un choix pour définir des blocs scientifiques, littéraires,...
/*
        echo "<a href='modify_nom_class.php?id_classe=$id_classe'>Modifier les paramètres</a></td><td>|
        <a href='../lib/confirm_query.php?liste_cible=$id_classe&amp;action=del_classe'>Supprimer la classe</a> |
        </td><td>";
*/
        echo "[<a href='../groupes/edit_class_grp_lot.php?id_classe=$id_classe'>config. simplifiée</a>]</td>";
		echo "<td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB; border-right: 1px solid #BBBBBB;'>
	<a href='modify_nom_class.php?id_classe=$id_classe'><img src='../images/icons/configure.png' alt=''> Paramètres</a></td><td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB;'>
        <a href='../lib/confirm_query.php?liste_cible=$id_classe&amp;action=del_classe'><img src='../images/icons/delete.png' alt=''> Supprimer</a>
        </td><td style='padding: 5px; padding-right: 10px; padding-left: 10px; border-left: 1px solid #BBBBBB; border-right: 1px solid #BBBBBB;'>";
	//=======================================


        if ($nb_per == 0) echo " <b>(Classe virtuelle)</b> "; else echo "&nbsp;";

        echo "</td></tr>";
    $i++;
    }

    echo "</table>";
} else {
    echo "<p class='grand'>Attention : aucune classe n'a été définie dans la base GEPI !</p>";
    echo "<p>Vous pouvez ajouter des classes à la base en cliquant sur le lien ci-dessus, ou bien directement <br /><a href='../initialisation/index.php'>importer les élèves et les classes à partir de fichiers GEP.</a></p>";
}
require("../lib/footer.inc.php");
?>