<?php
/*
 * Last modification  : 28/09/2006
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


if (isset($_POST['isposted'])) {
    $ok = 'yes';
    if (isset($_POST['reg_current_matiere'])) {
        // On vérifie d'abord que l'identifiant est constitué uniquement de lettres et de chiffres :
        $matiere_name = $_POST['reg_current_matiere'];
        if (!is_numeric($_POST['matiere_categorie'])) {
            // On empêche les mise à jour globale automatiques, car on n'est pas sûr de ce qui s'est passé si l'ID n'est pas numérique...
            $ok = "no";
            $matiere_categorie = "0";
        } else {
            $matiere_categorie = $_POST['matiere_categorie'];
        }
        if (ereg ("^[a-zA-Z_]{1}[a-zA-Z0-9_]{1,19}$", $matiere_name)) {
            $verify_query = mysql_query("SELECT * from matieres WHERE matiere='$matiere_name'");
            $verify = mysql_num_rows($verify_query);
            if ($verify == 0) {
                //========================
        // MODIF: boireaus
        // Quand on poste un &, c'est un &amp; qui est reçu.
        //$matiere_nom_complet = $_POST['matiere_nom_complet'];
                $matiere_nom_complet = html_entity_decode_all_version($_POST['matiere_nom_complet']);
                //========================
                $matiere_priorite = $_POST['matiere_priorite'];
                $register_matiere = mysql_query("INSERT INTO matieres SET matiere='".$matiere_name."', nom_complet='".$matiere_nom_complet."', priority='".$matiere_priorite."', categorie_id = '" . $matiere_categorie . "'");
                if (!$register_matiere) {
                    $msg = rawurlencode("Une erreur s'est produite lors de l'enregistrement de la nouvelle matière.");
                    $ok = 'no';
                } else {
                    $msg = rawurlencode("La nouvelle matière a bien été enregistrée.");
                }
            } else {
                $msg = rawurlencode("Cette matière existe déjà !!");
                $ok = 'no';
            }
        } else {
            $msg = "L'identifiant de matière doit être constitué uniquement de lettres et de chiffres !";
            $ok = 'no';
        }
    } else {

        $matiere_nom_complet = $_POST['matiere_nom_complet'];
        $matiere_priorite = $_POST['matiere_priorite'];
        $matiere_name = $_POST['matiere_name'];
        if (!is_numeric($_POST['matiere_categorie'])) {
            $matiere_categorie = "0";
        } else {
            $matiere_categorie = $_POST['matiere_categorie'];
        }

        $register_matiere = mysql_query("UPDATE matieres SET nom_complet='".$matiere_nom_complet."', priority='".$matiere_priorite."', categorie_id = '" . $matiere_categorie . "' WHERE matiere='".$matiere_name."'");

        if (!$register_matiere) {
            $msg = rawurlencode("Une erreur s'est produite lors de la modification de la matière");
            $ok = 'no';
        } else {
            $msg = rawurlencode("Les modifications ont été enregistrées ! ");
        }
    }
    if ((isset($_POST['force_defaut'])) and ($ok == 'yes'))
        $req = mysql_query("UPDATE j_groupes_matieres jgm, j_groupes_classes jgc SET jgc.priorite='".$matiere_priorite."'
        WHERE (jgc.id_groupe = jgm.id_groupe AND jgm.id_matiere='".$matiere_name."'");
    if ((isset($_POST['force_defaut_categorie'])) and ($ok == 'yes'))
        $req = mysql_query("UPDATE j_groupes_classes jgc, j_groupes_matieres jgm SET jgc.categorie_id='".$matiere_categorie."'
        WHERE (jgc.id_groupe = jgm.id_groupe AND jgm.id_matiere='".$matiere_name."')");
    header("location: index.php?msg=$msg");
    die();

}
//**************** EN-TETE *******************************
$titre_page = "Gestion des matières | Modifier une matière";
require_once("../lib/header.inc");
//**************** FIN EN-TETE ****************************
?>
<form enctype="multipart/form-data" action="modify_matiere.php" method=post>
<p class=bold>
|<a href="index.php">Retour</a>|<input type=submit value=Enregistrer></input>

</p>
<?php
// On va chercher les infos de la matière que l'on souhaite modifier
if (isset($_GET['current_matiere'])) {
    $call_data = mysql_query("SELECT nom_complet, priority, categorie_id from matieres WHERE matiere='".$_GET['current_matiere']."'");
    $matiere_nom_complet = mysql_result($call_data, 0, "nom_complet");
    $matiere_priorite = mysql_result($call_data, 0, "priority");
    $matiere_cat_id = mysql_result($call_data, 0, "categorie_id");
    $current_matiere = $_GET['current_matiere'];
} else {
    $matiere_nom_complet = "";
    $matiere_priorite = "0";
    $current_matiere = "";
    $matiere_cat_id = "0";
}
?>


<table><tr>
<td>Nom de matière : </td>
<td>
<?php
if (!isset($_GET['current_matiere'])) {
    echo "<input type=text size=15 name=reg_current_matiere />";
} else {
    echo "<input type=hidden name=matiere_name value=\"".$current_matiere."\" />".$current_matiere;
}
?>
</td></tr>
<tr>
<td>Nom complet : </td>
<td><input type=text name=matiere_nom_complet value="<?php echo $matiere_nom_complet;?>" /></td>
</tr>
<tr>
<td>Priorité d'affichage par défaut</td>
<td>
<?php
echo "<select size=1 name=matiere_priorite>\n";
$k = '0';
echo "<option value=0>0</option>\n";
$k='11';
$j = '1';
while ($k < '51'){
    echo "<option value=$k"; if ($matiere_priorite == $k) {echo " SELECTED";} echo ">$j</option>\n";
    $k++;
    $j = $k - 10;
}
echo "</select></td>";
?>
<tr>
<td>Catégorie par défaut</td>
<td>
<?php
echo "<select size=1 name=matiere_categorie>\n";
$get_cat = mysql_query("SELECT id, nom_court FROM matieres_categories");
$test = mysql_num_rows($get_cat);

if ($test == 0) {
    echo "<option disabled>Aucune catégorie définie</option>";
} else {
    while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
        echo "<option value='".$row["id"]."'";
        if ($matiere_cat_id == $row["id"]) echo " SELECTED";
        echo ">".html_entity_decode_all_version($row["nom_court"])."</option>";
    }
}
echo "</select>";

?>
</td>
</table>
<p>
<b>Pour toutes les classes, forcer la valeur de la priorité d'affichage à la valeur par défaut ci-dessus :</b>
<input type="checkbox" name="force_defaut" checked />
</p>
<p>
<b>Pour toutes les classes, forcer la valeur de la catégorie de matière à la valeur par défaut ci-dessus :</b>
<input type="checkbox" name="force_defaut_categorie" checked />
</p>
<input type="hidden" name="isposted" value="yes" />
</form>
<hr />
<p><b>Aide :</b></p>
<ul>
<li><b>Nom de matière</b>
<br /><br />Il s'agit de l'identifiant de la matière. Il est constitué au maximum de 20 caractères : lettres, chiffres ou "_" et ne doit pas commencer par un chiffre.
Une fois enregistré, il n'est plus possible de le modifier.
</li>
<li><b>Nom complet</b>
<br /><br />Il s'agit de l'intitulé de la matière, tel qu'il apparaît aux utilisateurs sur les bulletins, les relevés de notes, etc.
Une fois enregistré, il est toujours possible de le modifier.
</li>
<li><b>Priorité d'affichage par défaut</b>
<br /><br />Permet de définir l'ordre d'affichage par défaut des matières dans le bulletin scolaire et dans les tableaux récapitulatifs des moyennes.
<br /><b>Remarques :</b>
<ul>
<li>Lors de la gestion des matières dans une classe, c'est cette valeur qui est enregistrée par défaut. Il est alors possible de changer la valeur pour une classe donnée.</li>
<li>Il est possible d'attribuer le même poids à plusieurs matières n'apparaissant pas sur un même bulletin. Par exemple, toutes les LV1 peuvent avoir le même poids, etc.</li>
<li>Si deux matières apparaissant sur un même bulletin ont la même priorité, GEPI affiche la première matière extraite de la base.</li>
</ul>
</ul>
<!--/li>
</ul-->

</body>
</html>