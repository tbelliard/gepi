<?php
/*
* Last modification  : 30/08/2006
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
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : (isset($_POST['orderby']) ? $_POST["orderby"] : 'priority,nom_complet');
if ($orderby != "nom_court" AND $orderby != "nom_complet" AND $orderby != "priority, nom_court") {
    $orderby = "priority,nom_complet";
}

$msg = null;

if (isset($_POST['action'])) {
    $error = false;
    if ($_POST['action'] == "add") {
        // On enregistre une nouvelle catégorie
        // On filtre un peu
        if (!is_numeric($_POST['priority'])) $_POST['priority'] = "0";
        // Le reste passera sans soucis, mais on coupe quand même si jamais c'est trop long
        if (strlen($_POST['nom_court']) > 250) $_POST['nom_court'] = substr($_POST['nom_court'], 0, 250);
        if (strlen($_POST['nom_complet']) > 250) $_POST['nom_complet'] = substr($_POST['nom_complet'], 0, 250);
        // On enregistre
        if ($_POST['nom_court'] == '') {
            $msg .= "Le nom court ne peut pas être vide.<br/>";
            $error = true;
            $res = false;
        }
        if ($_POST['nom_complet'] == '') {
            $msg .= "L'intitulé ne peut pas être vide.<br/>";
            $error = true;
            $res = false;
        }

        if (!$error) {
            $res = mysql_query("INSERT INTO matieres_categories SET nom_court = '" . htmlentities($_POST['nom_court']) . "', nom_complet = '" . htmlentities($_POST['nom_complet']) . "', priority = '" . $_POST["priority"] . "'");
        }
        if (!$res) {
            $msg .= "Erreur lors de l'enregistrement de la nouvelle catégorie.</br>";
            echo mysql_error();
        }
    } elseif ($_POST['action'] == "edit") {
        // On met à jour une catégorie
        // On filtre un peu
        if (!is_numeric($_POST['priority'])) $_POST['priority'] = "0";
        if (!is_numeric($_POST['categorie_id'])) $_POST['categorie_id'] = "0";
        // Le reste passera sans soucis, mais on coupe quand même si jamais c'est trop long
        if (strlen($_POST['nom_court']) > 250) $_POST['nom_court'] = substr($_POST['nom_court'], 0, 250);
        if (strlen($_POST['nom_complet']) > 250) $_POST['nom_complet'] = substr($_POST['nom_complet'], 0, 250);

        if ($_POST['nom_court'] == '') {
            $msg .= "Le nom court ne peut pas être vide.<br/>";
            $error = true;
            $res = false;
        }
        if ($_POST['nom_complet'] == '') {
            $msg .= "L'intitulé ne peut pas être vide.<br/>";
            $error = true;
            $res = false;
        }

        if (!$error) {
            // On enregistre
            $res = mysql_query("UPDATE matieres_categories SET nom_court = '" . htmlentities($_POST['nom_court']) . "', nom_complet = '" . htmlentities($_POST['nom_complet']) . "', priority = '" . $_POST["priority"] . "' WHERE id = '".$_POST['categorie_id']."'");
        }

        if (!$res) $msg .= "Erreur lors de la mise à jour de la catégorie.";
    } elseif ($_POST['action'] == "delete") {
        // On teste d'abord l'ID
        if (!is_numeric($_POST['categorie_id'])) {
            // Inutile d'en dire plus...
            $msg .= "Erreur.";
        } else {
            // On a un ID valide.
            // Si c'est l'ID 1, on ne supprime pas. C'est la catégorie par défaut
            if ($_POST['categorie_id'] == 1) {
                $msg .= "Vous ne pouvez pas supprimer la catégorie par défaut !";
            } else {

                // On teste l'utilisation de cette catégorie
                $test = mysql_result(mysql_query("SELECT count(matiere) FROM matieres WHERE categorie_id = '" . $_POST['categorie_id'] ."'"), 0);
                if ($test > "0") {
                    // On a des entrées... la catégorie a déjà été associée à des matières, donc on ne la supprime pas.
                    $msg .= "La catégorie n'a pas pu être supprimée, car elle a déjà été associée à des matières.<br/>";
                } else {
                    $res = mysql_query("DELETE FROM matieres_categories WHERE id = '" . $_POST['categorie_id']."'");
                    if (!$res) {
                        $msg .= "Erreur lors de la suppression de la catégorie.<br/>";
                    } else {
                        $msg .= "La catégorie a bien été supprimée.<br/>";
                    }
                }
            }
        }
    }
}

//**************** EN-TETE **************************************
$titre_page = "Gestion des catégories de matières";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

if (isset($_GET['action'])) {
    // On a une action : soit on ajoute soit on édite soit on delete
    ?>
    <p class=bold><a href="matieres_categories.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
    <?php
    if ($_GET['action'] == "add") {
        // On ajoute une catégorie
        // On affiche le formulaire d'ajout
        echo "<form enctype='multipart/form-data' action='matieres_categories.php' name='formulaire' method=post>";
        echo "<input type='hidden' name='action' value='add'>";
        echo "<p>Nom court (utilisé dans les outils de configuration) : <input type='text' name='nom_court'></p>";
        echo "<p>Intitulé complet (utilisé sur les documents officiels) : <input type='text' name='nom_complet'></p>";
        echo "<p>Priorité d'affichage par défaut : ";
        echo "<select name='priority' size='1'>";
        for ($i=0;$i<11;$i++) {
            echo "<option value='$i'>$i</option>";
        }
        echo "</select>";

        echo "<p>";
        echo "<input type='submit' value='Enregistrer'>";
        echo "</p>";
        echo "</form>";
    } elseif ($_GET['action'] == "edit") {
        // On édite la catégorie existante
        if (!is_numeric($_GET['categorie_id'])) $_GET['categorie_id'] == 0;

        $res = mysql_query("SELECT id, nom_court, nom_complet, priority FROM matieres_categories WHERE id = '" . $_GET['categorie_id'] . "'");
        $current_cat = mysql_fetch_array($res, MYSQL_ASSOC);

        if ($current_cat) {
            echo "<form enctype='multipart/form-data' action='matieres_categories.php' name='formulaire' method=post>";
            echo "<input type='hidden' name='action' value='edit'>";
            echo "<input type='hidden' name='categorie_id' value='".$current_cat["id"] . "'>";
            echo "<p>Nom court (utilisé dans les outils de configuration) : <input type='text' name='nom_court' value='".html_entity_decode_all_version($current_cat["nom_court"]) ."' /></p>";
            echo "<p>Intitulé complet (utilisé sur les documents officiels) : <input type='text' name='nom_complet' value='".html_entity_decode_all_version($current_cat["nom_complet"]) ."' /></p>";
            echo "<p>Priorité d'affichage par défaut : ";
            echo "<select name='priority' size='1'>";
            for ($i=0;$i<11;$i++) {
                echo "<option value='$i'";
                if ($current_cat["priority"] == $i) echo " SELECTED";
                echo ">$i</option>";
            }
            echo "</select>";

            echo "<p>";
            echo "<input type='submit' value='Enregistrer'>";
            echo "</p>";
            echo "</form>";
        }

    }



} else {
    // Pas d'action. On affiche la liste des rubriques
    ?>
    <p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href="matieres_categories.php?action=add">Ajouter une catégorie</a></p>
    <p>Remarque : la catégorie par défaut ne peut pas être supprimée. Elle est automatiquement associée aux matières existantes et aux nouvelles matières, et pour tous les groupes. Vous pouvez la renommer (Autres, Hors catégories, etc.), mais laissez toujours un nom générique.</p>

    <table width = '100%' border= '1' cellpadding = '5'>
<tr>
    <td><p class='bold'><a href='./matieres_categories.php?orderby=nom_court'>Nom court</a></p></td>
    <td><p class='bold'><a href='./matieres_categories.php?orderby=m.nom_complet'>Intitulé complet</a></p></td>
    <td><p class='bold'><a href='./matieres_categories.php?orderby=m.priority,m.nom_complet'>Ordre d'affichage<br />par défaut</a></p></td>
    <td><p class='bold'>Supprimer</p></td>
</tr>
    <?php

    $res = mysql_query("SELECT id, nom_court, nom_complet, priority FROM matieres_categories ORDER BY $orderby");
    while ($current_cat = mysql_fetch_array($res, MYSQL_ASSOC)) {
        echo "<tr>";
        echo "<td><a href='matieres_categories.php?action=edit&categorie_id=".$current_cat["id"]."'>".html_entity_decode_all_version($current_cat["nom_court"])."</a></td>";
        echo "<td>".html_entity_decode_all_version($current_cat["nom_complet"])."</td>";
        echo "<td>".$current_cat["priority"]."</td>";
        echo "<td>";
        if ($current_cat["id"] != "1") {
            echo "<form enctype='multipart/form-data' action='matieres_categories.php' name='formulaire' method=post>";
            echo "<input type='hidden' name='action' value='delete'>";
            echo "<input type='hidden' name='categorie_id' value='".$current_cat["id"]."'>";
            echo "<input type='submit' value='Supprimer'></form>";
        } else {
            echo "Catégorie par défaut (suppression impossible)";
            echo"</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}
require("../lib/footer.inc.php");
?>