<?php
/*
 * Last modification  : 26/09/2006
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

$chemin_retour=isset($_GET['chemin_retour']) ? $_GET['chemin_retour'] : (isset($_POST['chemin_retour']) ? $_POST["chemin_retour"] : NULL);

$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL);
$id_groupe = isset($_GET['id_groupe']) ? $_GET['id_groupe'] : (isset($_POST['id_groupe']) ? $_POST["id_groupe"] : NULL);

if (!is_numeric($id_groupe)) $id_groupe = 0;
$current_group = get_group($id_groupe);
$reg_nom_groupe = $current_group["name"];
$reg_nom_complet = $current_group["description"];
$reg_matiere = $current_group["matiere"]["matiere"];
$reg_id_classe = $current_group["classes"]["list"][0];
$reg_clazz = $current_group["classes"]["list"];
$reg_professeurs = (array)$current_group["profs"]["list"];

$mode = isset($_GET['mode']) ? $_GET['mode'] : (isset($_POST['mode']) ? $_POST["mode"] : null);
if ($mode == null and $id_classe == null) {
	$mode = "groupe";
} else if ($mode == null and $current_group) {
	if (count($current_group["classes"]["list"]) > 1) {
		$mode = "regroupement";
	} else {
		$mode = "groupe";
	}
}

foreach ($current_group["periodes"] as $period) {
    $reg_eleves[$period["num_periode"]] = $current_group["eleves"][$period["num_periode"]]["list"];
}

if (isset($_POST['is_posted'])) {
    $msg="";
    $error = false;
    //=======================================
    // MODIF: boireaus
    /*
    $reg_nom_groupe = $_POST['groupe_nom_court'];
    $reg_nom_complet = $_POST['groupe_nom_complet'];
    */
    $reg_nom_groupe = html_entity_decode_all_version($_POST['groupe_nom_court']);
    $reg_nom_complet = html_entity_decode_all_version($_POST['groupe_nom_complet']);
    //=======================================
    $reg_matiere = $_POST['matiere'];

    if (empty($reg_nom_groupe)) {
        $error = true;
        $msg .= "Vous devez donner un nom court au groupe.<br />";
    }

    if (empty($reg_nom_groupe)) {
        $error = true;
        $msg .= "Vous devez donner un nom complet au groupe.<br />";
    }

    $clazz = array();

    // Classes

    if ($_POST['mode'] == "groupe") {
        $clazz[] = $id_classe;
        $reg_id_classe = $id_classe;
        $mode = "groupe";
    } else if ($_POST['mode'] == "regroupement") {
        $mode = "regroupement";
        foreach ($_POST as $key => $value) {
            if (preg_match("/^classe\_/", $key)) {
                $temp = explode("_", $key);
                $id = $temp[1];
                $clazz[] = $id;
            }
        }



        foreach ($_POST as $key => $value) {
            if (preg_match("/^precclasse\_/", $key)) {
                $temp = explode("_", $key);
                $tmpid = $temp[1];
		// On vérifie si la classe a été décochée:
		if(!isset($_POST['classe_'.$tmpid])){
			// On vérifie si l'identifiant de classe $tmpid peut être décoché.

			unset($tabtmp);
			$tabtmp=array();
			$test=0;
			$test2=0;
			$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$tmpid'";
			$res_tmp=mysql_query($sql);
			while($lig_tmp=mysql_fetch_object($res_tmp)){
				$sql="SELECT 1=1 FROM matieres_notes WHERE id_groupe='$id_groupe' AND login='$lig_tmp->login'";
				//echo "$sql<br />";
				$res_test=mysql_query($sql);
				if(mysql_num_rows($res_test)>0){
					//echo "$lig_tmp->login<br />";
					if(!in_array($lig_tmp->login,$tabtmp)){$tabtmp[]=$lig_tmp->login;}
					$test++;
				}
				$sql="SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='$id_groupe' AND login='$lig_tmp->login'";
				//echo "$sql<br />";
				$res_test=mysql_query($sql);
				if(mysql_num_rows($res_test)>0){
					//echo "$lig_tmp->login<br />";
					if(!in_array($lig_tmp->login,$tabtmp)){$tabtmp[]=$lig_tmp->login;}
					$test2++;
				}
			}

			$sql="SELECT classe FROM classes WHERE id='$tmpid'";
			$res_tmp=mysql_query($sql);
			$lig_tmp=mysql_fetch_object($res_tmp);
			$clas_tmp=$lig_tmp->classe;

			//if(!$verify){
			if(($test>0)||($test2>0)){
				/*
				$sql="SELECT classe FROM classes WHERE id='$tmpid'";
				$res_tmp=mysql_query($sql);
				$lig_tmp=mysql_fetch_object($res_tmp);
				$clas_tmp=$lig_tmp->classe;
				*/

				$error = true;
				$msg .= "Des données existantes bloquent la suppression de la classe $clas_tmp du groupe.<br />\nAucune note ni appréciation du bulletin ne doit avoir été saisie pour les élèves de ce groupe pour permettre la suppression du groupe.<br />\n";
				if(count($tabtmp)==1){
					$msg.="L'élève ayant des moyennes ou appréciations saisies est $tabtmp[0].<br />\n";
				}
				else{
					$msg.="Les élèves ayant des moyennes ou appréciations saisies sont $tabtmp[0]";
					for($i=1;$i<count($tabtmp);$i++){
						$msg.=", $tabtmp[$i]";
					}
					$msg.=".<br />\n";
				}
				// Et on remet la classe dans la liste des classes:
		                $clazz[] = $tmpid;
			}
			else{
				// On teste aussi si il y a des élèves de la classe dans le groupe.
				$sql="SELECT jeg.login FROM j_eleves_groupes jeg, j_eleves_classes jec WHERE
							jeg.login=jec.login AND
							jeg.periode=jec.periode AND
							jeg.id_groupe='$id_groupe' AND
							jec.id_classe='$tmpid'";
				//echo "$sql<br />\n";
				$res_ele_clas_grp=mysql_query($sql);
				if(mysql_num_rows($res_ele_clas_grp)>0){
					$error = true;
					$msg .= "Des données existantes bloquent la suppression de la classe $clas_tmp du groupe.<br />\nAucun élève de la classe ne doit être inscrit dans le groupe.<br />\n<a href='edit_eleves.php?id_groupe=$id_groupe&id_classe=$tmpid'>Enlevez les élèves du groupe</a> avant.<br />\n";
					// Et on remet la classe dans la liste des classes:
					$clazz[] = $tmpid;
				}
			}
		}
            }
        }


    }

    // On ajoute un test pour s'assurer qu'on n'a pas un tableau vide pour les classes
    if (count($clazz) == 0) {
    	$clazz[0] = $id_classe;
    }

    // Professeurs
        $reg_professeurs = array();
        foreach ($_POST as $key => $value) {
            if (preg_match("/^prof\_/", $key)) {
                $id = preg_replace("/^prof\_/", "", $key);
                $proflogin = $_POST["proflogin_".$id];
                // Normalement on a un traitement anti-injection sur $_POST, donc pas de soucis.
                // Mais ça serait bien de faire un test quand même. Si un dev passe par là...
                $reg_professeurs[] = $proflogin;
            }
        }

    $reg_clazz = $clazz;

    if (empty($reg_clazz)) {
        $error = true;
        $msg .= "Vous devez sélectionner au moins une classe.<br />";
    }

    if (!$error) {
        // pas d'erreur : on continue avec la mise à jour du groupe
        $create = update_group($id_groupe, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves);
        if (!$create) {
            $msg .= "Erreur lors de la mise à jour du groupe.";
        } else {
            //======================================
            // MODIF: boireaus
            //$msg = "Le groupe a bien été mis à jour.";
            $msg = "L'enseignement ". stripslashes($reg_nom_complet) . " a bien été mis à jour.";
            $msg = urlencode($msg);
	    if(isset($chemin_retour)){
	            header("Location: $chemin_retour?&msg=$msg");
		}
		else{
	            header("Location: ./edit_class.php?id_classe=$id_classe&msg=$msg");
		}
            //======================================
        }
        $current_group = get_group($id_groupe);
    }
}
/* DEBUG
echo "<pre>";
print_r($_POST);
echo "</pre>";
echo html_entity_decode_all_version("prof_ERIC_ALARY");

echo "<pre>";
print_r($current_group);
echo "</pre>";

echo "<pre>";
print_r($reg_professeurs);
echo "</pre>";
*/
//**************** EN-TETE **************************************
$titre_page = "Gestion des groupes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

//echo "\$_SERVER['HTTP_REFERER']=".$_SERVER['HTTP_REFERER']."<br />\n";
?>
<p class=bold>
<?php
//============================
// MODIF: boireaus
//if(isset($_GET['chemin_retour'])){
if(isset($chemin_retour)){
	echo "|<a href=\"".$_GET['chemin_retour']."\">Retour</a> |";
}
else{
	echo "|<a href=\"edit_class.php?id_classe=$id_classe\">Retour</a> |";
}
//============================
?>
<a href="edit_class.php?id_classe=<?php echo $id_classe;?>&amp;action=delete_group&amp;id_groupe=<?php echo $id_groupe;?>" onclick="return confirmlink(this, 'ATTENTION !!! LISEZ CET AVERTISSEMENT : La suppression d\'un enseignement est irréversible. Une telle suppression ne devrait pas avoir lieu en cours d\'année. Si c\'est le cas, cela peut entraîner la présence de données orphelines dans la base. Si des données officielles (notes et appréciations du bulletin) sont présentes, la suppression sera bloquée. Dans le cas contraire, toutes les données liées au groupe seront supprimées, incluant les notes saisies par les professeurs dans le carnet de notes ainsi que les données présentes dans le cahier de texte. Etes-vous *VRAIMENT SÛR* de vouloir continuer ?', 'Confirmation de la suppression')">Supprimer le groupe</a> |
<?php
if ($mode == "groupe") {
    echo "<h3>Modifier le groupe</h3>\n";
} elseif ($mode == "regroupement") {
    echo "<h3>Modifier le regroupement</h3>\n";
}
?>
<form enctype="multipart/form-data" action="edit_group.php" method="post">
<div style="width: 95%;">
<div style="width: 45%; float: left;">
<p>Nom court : <input type=text size=30 name=groupe_nom_court value = "<?php echo $reg_nom_groupe; ?>" /></p>

<p>Nom complet : <input type=text size=50 name=groupe_nom_complet value = "<?php echo $reg_nom_complet; ?>" /></p>

<?php


// Classes

if ($mode == "groupe") {
    echo "<p>Sélectionnez la classe à laquelle appartient le groupe :\n";
    echo "<select name='id_classe' size='1'>\n";

    $call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
    $nombre_lignes = mysql_num_rows($call_data);
    if ($nombre_lignes != 0) {
        $i = 0;
        while ($i < $nombre_lignes){
            $id_classe2 = mysql_result($call_data, $i, "id");
            $classe = mysql_result($call_data, $i, "classe");
            if (get_period_number($id_classe2) != "0") {
                echo "<option value='" . $id_classe2 . "'";
                if (in_array($id_classe2, $reg_clazz)) echo " SELECTED";
                echo ">$classe</option>\n";
            }
        $i++;
        }
    } else {
        echo "<option value='false'>Aucune classe définie !</option>\n";
    }
    echo "</select>";
    //echo "<br />[-> <a href='edit_group.php?id_classe=".$id_classe."&id_groupe=".$id_groupe."&mode=regroupement'>sélectionner plusieurs classes</a>]</p>";
    echo "<br />[-> <a href='edit_group.php?id_classe=".$id_classe."&amp;id_groupe=".$id_groupe."&amp;mode=regroupement'>sélectionner plusieurs classes</a>]</p>";
} else if ($mode == "regroupement") {
    echo "<input type='hidden' name='id_classe' value='".$id_classe."' />";
    echo "<p>Sélectionnez les classes auxquelles appartient le regroupement :";

    $call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
    $nombre_lignes = mysql_num_rows($call_data);
    if ($nombre_lignes != 0) {
        $i = 0;
        while ($i < $nombre_lignes){
            $id_classe_temp = mysql_result($call_data, $i, "id");
            $classe = mysql_result($call_data, $i, "classe");
            if (get_period_number($id_classe_temp) == get_period_number($id_classe)) {
                echo "<br /><input type='checkbox' name='classe_" . $id_classe_temp . "' value='yes'";
                if (in_array($id_classe_temp, $reg_clazz)){
			echo " CHECKED";
		}
                //echo " />$classe</option>";
                echo " />$classe\n";
                if (in_array($id_classe_temp, $reg_clazz)){
			// Pour contrôler les suppressions de classes.
			// On conserve la liste des classes précédemment cochées:
			echo "<input type='hidden' name='precclasse_".$id_classe_temp."' value='y' />\n";
		}
            }
        $i++;
        }
        echo "</p>\n";
    } else {
        echo "<p>Aucune classe définie !</p>\n";
    }
}

//-- Fin classes


?>



<!--p>Sélectionnez la matière enseignée à ce groupe :-->
<?php
/*
$query = mysql_query("SELECT matiere, nom_complet FROM matieres ORDER BY matiere");
$nb_mat = mysql_num_rows($query);

echo "<select name='matiere' size='1'>\n";

for ($i=0;$i<$nb_mat;$i++) {
    $matiere = mysql_result($query, $i, "matiere");
    $nom_matiere = mysql_result($query, $i, "nom_complet");
    echo "<option value='" . $matiere . "'";
    if ($reg_matiere == $matiere) echo " SELECTED";
    //echo ">" . $nom_matiere . "</option>\n";
    echo ">" . htmlentities($nom_matiere) . "</option>\n";
}
echo "</select>\n";
//echo "</p>\n";
*/
echo "</div>";
// Edition des professeurs
echo "<div style='width: 45%; float: right;'>";

//=================================================
echo "<p>Sélectionnez la matière enseignée à ce groupe : ";

$query = mysql_query("SELECT matiere, nom_complet FROM matieres ORDER BY matiere");
$nb_mat = mysql_num_rows($query);

echo "<select name='matiere' size='1'>\n";

for ($i=0;$i<$nb_mat;$i++) {
    $matiere = mysql_result($query, $i, "matiere");
    $nom_matiere = mysql_result($query, $i, "nom_complet");
    echo "<option value='" . $matiere . "'";
    if ($reg_matiere == $matiere) echo " SELECTED";
    //echo ">" . $nom_matiere . "</option>\n";
    //echo ">" . html_entity_decode($nom_matiere) . "</option>\n";
    echo ">" . htmlentities($nom_matiere) . "</option>\n";
}
echo "</select>\n";
echo "</p>\n";
//=================================================

echo "<p>Cochez les professeurs qui participent à cet enseignement : </p>\n";


$calldata = mysql_query("SELECT u.login, u.nom, u.prenom, u.civilite FROM utilisateurs u, j_professeurs_matieres j WHERE (j.id_matiere = '$reg_matiere' and j.id_professeur = u.login and u.etat!='inactif') ORDER BY u.login");
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
	echo "<p><font color='red'>ERREUR !</font> Aucun professeur n'a été défini comme compétent dans la matière considérée.</p>";
} else {
	$total_profs = array_merge($prof_list["list"], $reg_professeurs);
	$total_profs = array_unique($total_profs);

	$p = 0;
	foreach($total_profs as $prof_login) {
	    echo "<input type='hidden' name='proflogin_".$p."' value='".$prof_login."' />\n";
	    echo "<input type='checkbox' name='prof_".$p."' ";
	    if (in_array($prof_login, $reg_professeurs)) {
	        if (array_key_exists($prof_login, $current_group["profs"]["users"])){
	            echo " CHECKED />". $current_group["profs"]["users"][$prof_login]["civilite"] . " " .
	                $current_group["profs"]["users"][$prof_login]["prenom"] . " " .
	                $current_group["profs"]["users"][$prof_login]["nom"] . "<br />\n";
	        } else {
	            echo " CHECKED />". $prof_list["users"][$prof_login]["civilite"] . " " .
	                $prof_list["users"][$prof_login]["prenom"] . " " .
	                $prof_list["users"][$prof_login]["nom"] . "<br />\n";
	        }
	    } else {
	        echo " />". $prof_list["users"][$prof_login]["civilite"] . " " .
	                $prof_list["users"][$prof_login]["prenom"] . " " .
	                $prof_list["users"][$prof_login]["nom"] . "<br />\n";
	    }
	    $p++;
	}
}

echo "</div>";
echo "<div style='float: left; width: 100%'>";
echo "<input type='hidden' name='is_posted' value='1' />\n";
echo "<input type='hidden' name='mode' value='" . $mode . "' />\n";
echo "<input type='hidden' name='id_groupe' value='" . $id_groupe . "' />\n";
//============================
// MODIF: boireaus
if(isset($chemin_retour)){
	echo "<input type='hidden' name='chemin_retour' value='$chemin_retour' />\n";
}
echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
echo "</div>";
echo "</div>";
?>
</form>
</body>
</html>