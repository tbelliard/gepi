<?php
/*
 * Last modification  : 22/08/2006
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

// Initialisation des variables utilisées dans le formulaire

$reg_nom_groupe = '';
$reg_nom_complet = '';
$reg_matiere = isset($_GET['matiere']) ? $_GET['matiere'] : (isset($_POST['matiere']) ? $_POST['matiere'] : null);
if ($reg_matiere == "null") $reg_matiere = null;
$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST['id_classe'] : null);
$reg_id_classe = $id_classe;
$mode = isset($_GET['mode']) ? $_GET['mode'] : (isset($_POST['mode']) ? $_POST['mode'] : "groupe");
if(isset($reg_matiere)){
    if($reg_matiere!="" && $reg_matiere != "null"){
        $sql="SELECT * FROM matieres WHERE matiere='$reg_matiere'";
        $resultat_recup_matiere=mysql_query($sql);

        $ligne=mysql_fetch_object($resultat_recup_matiere);
        $reg_nom_groupe=$ligne->matiere;
        $reg_nom_complet=$ligne->nom_complet;
        $matiere_categorie = $ligne->categorie_id;
    } else {
        $matiere_categorie = 1;
    }
} else {
    $matiere_categorie = 1;
}

$reg_clazz = array();

if (isset($_POST['is_posted'])) {
    $error = false;
    $reg_nom_groupe = html_entity_decode_all_version($_POST['groupe_nom_court']);
    $reg_nom_complet = html_entity_decode_all_version($_POST['groupe_nom_complet']);
    $reg_matiere = $_POST['matiere'];
    $reg_categorie = $_POST['categorie'];

    if (empty($reg_nom_groupe)) {
        $error = true;
        $msg .= "Vous devez donner un nom court au groupe.<br/>";
    }

    if (empty($reg_nom_groupe)) {
        $error = true;
        $msg .= "Vous devez donner un nom complet au groupe.<br/>";
    }

    $clazz = array();

    if ($_POST['mode'] == "groupe") {
        $clazz[] = $_POST['id_classe'];
        $reg_id_classe = $_POST['id_classe'];
        $mode = "groupe";
    } else if ($_POST['mode'] == "regroupement") {
        $mode = "regroupement";
        foreach ($_POST as $key => $value) {
            if (preg_match("/^classe\_/", $key)) {
                $temp = explode("_", $key);
                $classe_id = $temp[1];
                $clazz[] = $classe_id;
            }
        }
    }

    $reg_clazz = $clazz;

    if (empty($reg_clazz)) {
        $error = true;
        $msg .= "Vous devez sélectionner au moins une classe.<br/>";
    }

    if (!is_numeric($reg_categorie)) {
        $reg_categorie = 1;
    }

    if (!$error) {
        // pas d'erreur : on continue avec la création du groupe
        $create = create_group($reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_categorie);
        if (!$create) {
            $msg .= "Erreur lors de la création du groupe. ";
        } else {
            $msg = "L'enseignement a bien été créé. ";
            $msg = urlencode($msg);

            // On s'occupe des profs, s'il y en a.
                $reg_professeurs = array();
                foreach ($_POST as $key => $value) {
                    if (preg_match("/^prof\_/", $key)) {
                        $id = preg_replace("/^prof\_/", "", $key);
                        $proflogin = $_POST["proflogin_".$id];
                        $reg_professeurs[] = $proflogin;
                    }
                }

                if (count($reg_professeurs) == 0) {
                    header("Location: ./edit_group.php?id_groupe=$create&msg=$msg&id_classe=$id_classe&mode=$mode");
                } else {
                    $res = update_group($create, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, array());
					if($res){$msg.="Mise à jour du groupe effectuée. ";}else{$msg.="Echec de la mise à jour du groupe. ";}
                    //header("Location: ./edit_class.php?id_classe=$id_classe");
                    header("Location: ./edit_class.php?id_classe=$id_classe&msg=$msg");
                }
        }

    }

}

//**************** EN-TETE **************************************
$titre_page = "Gestion des groupes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************
?>
<p class=bold>
<a href="edit_class.php?id_classe=<?php echo $id_classe;?>"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>
<?php
if ($mode == "groupe") {
    echo "<h3>Ajouter un groupe à une classe</h3>\n";
} elseif ($mode == "regroupement") {
    echo "<h3>Ajouter un regroupement d'élèves de différentes classes</h3>";
}

?>
<form enctype="multipart/form-data" action="add_group.php" method=post>
<div style="width: 95%;">
<div style="width: 45%; float: left;">
<p>Nom court : <input type=text size=30 name=groupe_nom_court value = "<?php echo $reg_nom_groupe; ?>" /></p>

<p>Nom complet : <input type=text size=30 name=groupe_nom_complet value = "<?php echo $reg_nom_complet; ?>" /></p>

<p>Matière enseignée à ce groupe :
<?php

$query = mysql_query("SELECT matiere, nom_complet FROM matieres ORDER BY matiere");
$nb_mat = mysql_num_rows($query);

echo "<select name='matiere' size='1'>";

for ($i=0;$i<$nb_mat;$i++) {
    $matiere = mysql_result($query, $i, "matiere");
    $nom_matiere = mysql_result($query, $i, "nom_complet");
    echo "<option value='" . $matiere . "'";
    if ($reg_matiere == $matiere) echo " SELECTED";
    //echo ">" . $nom_matiere . "</option>\n";
    echo ">" . htmlentities($nom_matiere) . "</option>\n";
}
echo "</select>\n";
echo "</p>\n";

if ($mode == "groupe") {
    echo "<p>Classe à laquelle appartient le nouvel enseignement :\n";
    echo "<select name='id_classe' size='1'>\n";

    $call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
    $nombre_lignes = mysql_num_rows($call_data);
    if ($nombre_lignes != 0) {
        $i = 0;
        while ($i < $nombre_lignes){
            $id_classe = mysql_result($call_data, $i, "id");
            $classe = mysql_result($call_data, $i, "classe");
            if (get_period_number($id_classe) != "0") {
                echo "<option value='" . $id_classe . "'";
                if ($reg_id_classe == $id_classe) echo " SELECTED";
                echo ">$classe</option>\n";
            }
        $i++;
        }
    } else {
        echo "<option value='false'>Aucune classe définie !</option>\n";
    }
    echo "</select>\n";
    echo "</p>\n";

} else if ($mode == "regroupement") {
    echo "<input type='hidden' name='id_classe' value='" . $id_classe . "' />";
    echo "<p>Sélectionnez les classes auxquelles appartient le nouvel enseignement :<br />";
    echo "<span style='color: red;'>Note : n'apparaissent que les classes ayant le même nombre de périodes.</span>";
    $current_classe_period_num = get_period_number($id_classe);
    $call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
    $nombre_lignes = mysql_num_rows($call_data);
    if ($nombre_lignes != 0) {
        $i = 0;
        while ($i < $nombre_lignes){
            $_id_classe = mysql_result($call_data, $i, "id");
            $classe = mysql_result($call_data, $i, "classe");
            if (get_period_number($_id_classe) == $current_classe_period_num) {
                echo "<br/><input type='checkbox' name='classe_" . $_id_classe . "' value='yes'";
                if (in_array($_id_classe, $reg_clazz) OR $_id_classe == $id_classe) echo " CHECKED";
                echo " />$classe\n";
                //echo ">$classe</option>";
            }
        $i++;
        }
        echo "</p>\n";
    } else {
        echo "<p>Aucune classe définie !</p>\n";
    }
}
echo "<p>Catégorie de matière à laquelle appartient l'enseignement : ";
echo "<select size=1 name=categorie>\n";
$get_cat = mysql_query("SELECT id, nom_court FROM matieres_categories");
$test = mysql_num_rows($get_cat);

while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
    echo "<option value='".$row["id"]."'";
    if ($matiere_categorie == $row["id"]) echo " SELECTED";
    echo ">".html_entity_decode_all_version($row["nom_court"])."</option>";
}
echo "</select>";

echo "</div>";
// On affiche une sélection des profs si la matière a été choisie

if ($reg_matiere != null) {
    echo "<div style='width: 45%; float: right;'>";
    echo "<p>Cochez les professeurs qui participent à cet enseignement : </p>\n";

    $calldata = mysql_query("SELECT u.login, u.nom, u.prenom, u.civilite FROM utilisateurs u, j_professeurs_matieres j WHERE (j.id_matiere = '$reg_matiere' and j.id_professeur = u.login and u.etat!='inactif') ORDER BY u.nom");
    $nb = mysql_num_rows($calldata);
    $prof_list = array();
    $prof_list["list"] = array();
    for ($i=0;$i<$nb;$i++) {
        $prof_login = mysql_result($calldata, $i, "login");
        $prof_nom = mysql_result($calldata, $i, "nom");
        $prof_prenom = mysql_result($calldata, $i, "prenom");
        $civilite = mysql_result($calldata, $i, "civilite");
        $prof_list["list"][] = $prof_login;
        $prof_list["users"][$prof_login] = array("login" => $prof_login, "nom" => $prof_nom, "prenom" => $prof_prenom, "civilite" => $civilite);
    }

    if (count($prof_list["list"]) == "0") {
        echo "<p><font color=red>ERREUR !</font> Aucun professeur n'a été défini comme compétent dans la matière considérée.</p>";
    } else {
        $total_profs = array_unique($prof_list["list"]);
        $p = 0;
        foreach($total_profs as $prof_login) {
            echo "<input type='hidden' name='proflogin_".$p."' value='".$prof_login."' />\n";
            echo "<input type='checkbox' name='prof_".$p."' />";
            echo " " . $prof_list["users"][$prof_login]["nom"] . " " . $prof_list["users"][$prof_login]["prenom"]. "<br/>";
            $p++;
        }
    }
    echo "</div>";
}
// Fin : professeurs

echo "<div style='float: left; width: 100%'>";
echo "<input type='hidden' name='is_posted' value='1' />\n";
echo "<input type='hidden' name='mode' value='" . $mode . "' />\n";
echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
echo "</div>";
echo "</div>";



?>
</form>
<?php require("../lib/footer.inc.php");?>