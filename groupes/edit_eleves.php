<?php
/*
 * Last modification  : 17/10/2006
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

$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL);
$id_groupe = isset($_GET['id_groupe']) ? $_GET['id_groupe'] : (isset($_POST['id_groupe']) ? $_POST["id_groupe"] : NULL);
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "classe");


if (!is_numeric($id_groupe)) $id_groupe = 0;
$current_group = get_group($id_groupe);
$reg_nom_groupe = $current_group["name"];
$reg_nom_complet = $current_group["description"];
$reg_matiere = $current_group["matiere"]["matiere"];
$reg_id_classe = $id_classe;
$reg_clazz = $current_group["classes"]["list"];
$reg_professeurs = (array)$current_group["profs"]["list"];
$mode = isset($_GET['mode']) ? $_GET['mode'] : "groupe";

if (count($current_group["classes"]["list"]) > 1) {
	$multiclasses = true;
} else {
	$multiclasses = false;
}

$reg_eleves = array();
foreach ($current_group["periodes"] as $period) {
	//echo '$period["num_periode"]='.$period["num_periode"]."<br />";
	if($period["num_periode"]!=""){
		$reg_eleves[$period["num_periode"]] = $current_group["eleves"][$period["num_periode"]]["list"];
		//$msg.="\$reg_eleves[\$period[\"num_periode\"]]=\$reg_eleves[".$period["num_periode"]."]=".$reg_eleves[$period["num_periode"]]."<br />";
	}
}
$msg = null;
if (isset($_POST['is_posted'])) {
	$error = false;

	// Elèves
	$sql="SELECT DISTINCT login FROM j_eleves_groupes WHERE id_groupe='$id_groupe' ORDER BY login";
	$result_liste_eleves_du_grp=mysql_query($sql);
	while($lig_eleve=mysql_fetch_object($result_liste_eleves_du_grp)){
		$temoin_nettoyage="";
		foreach($current_group["periodes"] as $period) {
			$sql="SELECT * FROM matieres_notes WHERE login='$lig_eleve->login' AND id_groupe='$id_groupe' AND periode='$period'";
			$res_liste_notes=mysql_query($sql);
			$sql="SELECT * FROM matieres_appreciations WHERE login='$lig_eleve->login' AND id_groupe='$id_groupe' AND periode='$period'";
			$res_liste_appreciations=mysql_query($sql);
			if((mysql_num_rows($res_liste_notes)==0)&&(mysql_num_rows($res_liste_appreciations)==0)){
				//$sql="DELETE FROM j_eleves_groupes WHERE id_groupe='$id_groupe' AND login='$lig_eleve->login'";
				$sql="DELETE FROM j_eleves_groupes WHERE id_groupe='$id_groupe' AND login='$lig_eleve->login' AND periode='$period'";
				//echo "$sql<br />\n";
				$resultat_nettoyage_initial=mysql_query($sql);
			}
		}
	}

	$reg_eleves = array();

	// On travaille période par période

	$flag = array();
	foreach($current_group["periodes"] as $period) {
		foreach ($_POST as $key => $value) {
			$pattern = "/^eleve\_" . $period["num_periode"] . "\_/";
			if (preg_match($pattern, $key)) {
				$id = preg_replace($pattern, "", $key);
				$reg_eleves[$period["num_periode"]][] = $id;
				// Settings spécifiques
				$coef = array();
				if (!in_array($id, $flag)) {
					$coef[] = $_POST["setting_coef_".$id];
					$res = set_eleve_groupe_setting($id, $id_groupe, "coef", $coef);
					$flag[] = $id;
				}
			}
		}
	}
	$flag = null;

	if (!$error) {
		// pas d'erreur : on continue avec la mise à jour du groupe
		/*
		$msg.="count(\$reg_eleves)=count($reg_eleves)=".count($reg_eleves)."<br />";
		$msg.="count(\$reg_clazz)=count($reg_clazz)=".count($reg_clazz)."<br />";
		$msg.="$reg_clazz[0]=".$reg_clazz[0]."<br />";
		$msg.="update_group($id_groupe, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves);<br />";
		*/
		//==========================================
		// MODIF: boireaus
		if(count($reg_eleves)!=0){
			$create = update_group($id_groupe, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves);
			if (!$create) {
				$msg .= "Erreur lors de la mise à jour du groupe.";
			} else {
				$msg .= "Le groupe a bien été mis à jour.";
			}
		}
		else{
			$login_eleve=$_POST['login_eleve'];
			foreach($current_group["periodes"] as $period) {
				//echo "<!-- \$period[\"num_periode\"]=".$period["num_periode"]." -->\n";
				for($i=0;$i<count($login_eleve);$i++) {
					if (test_before_eleve_removal($login_eleve[$i], $id_groupe, $period["num_periode"])) {
						//$res = mysql_query("delete from j_eleves_groupes where (id_groupe = '" . $_id_groupe . "' and login = '" . $login_eleve[$i] . "' and periode = '" . $period["num_periode"] . "')");
						$sql="delete from j_eleves_groupes where (id_groupe = '" . $id_groupe . "' and login = '" . $login_eleve[$i] . "' and periode = '" . $period["num_periode"] . "')";
						//echo "<!-- sql=$sql -->\n";
						$res = mysql_query("delete from j_eleves_groupes where (id_groupe = '" . $id_groupe . "' and login = '" . $login_eleve[$i] . "' and periode = '" . $period["num_periode"] . "')");
						if (!$res) $errors = true;
					} else {
						$msg .= "Erreur lors de la suppression de l'élève ayant le login '" . $login_eleve[$i] . "', pour la période '" . $period["num_periode"] . " (des notes ou appréciations existent).<br/>";
					}
				}
			}
		}
		//==========================================
	}

	$current_group = get_group($id_groupe);
	// On réinitialise $reg_eleves
	$reg_eleves = array();
	foreach ($current_group["periodes"] as $period) {
		if($period["num_periode"]!=""){
			$reg_eleves[$period["num_periode"]] = $current_group["eleves"][$period["num_periode"]]["list"];
		}
	}
}

//**************** EN-TETE **************************************
$titre_page = "Gestion des groupes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

?>
<script type='text/javascript' language='javascript'>

function CocheCase(boul) {

 nbelements = document.formulaire.elements.length;
 for (i = 0 ; i < nbelements ; i++) {
   if (document.formulaire.elements[i].type =='checkbox')
      document.formulaire.elements[i].checked = boul ;
 }

}



function CochePeriode() {

    nbParams = CochePeriode.arguments.length;

    for (var i=0;i<nbParams;i++) {

        theElement = CochePeriode.arguments[i];

        if (document.formulaire.elements[theElement])

            document.formulaire.elements[theElement].checked = true;

    }

}



function DecochePeriode() {

    nbParams = DecochePeriode.arguments.length;

    for (var i=0;i<nbParams;i++) {

        theElement = DecochePeriode.arguments[i];

        if (document.formulaire.elements[theElement])

            document.formulaire.elements[theElement].checked = false;

    }

}

</script>

<?php


$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];

?>
<p class=bold>
|<a href="edit_class.php?id_classe=<?php echo $id_classe;?>">Retour</a>
<h3>Gérer les élèves de l'enseignement : <?php echo htmlentities($current_group["description"]) . " (" . $current_group["classlist_string"] . ")";?></h3>
<p>
<b><a href="javascript:CocheCase(true)">Tout cocher</a> - <a href="javascript:CocheCase(false)">Tout décocher</a></b>
</p>
<form enctype="multipart/form-data" action="edit_eleves.php" name="formulaire" method=post>
<p><input type='submit' value='Enregistrer' /></p>
<?php

// Edition des élèves

echo "<p>Cochez les élèves qui suivent cet enseignement, pour chaque période : </p>\n";

echo "<table border=1>\n";
echo "<tr>";
echo "<td><a href='edit_eleves.php?id_groupe=$id_groupe&amp;id_classe=$id_classe&amp;order_by=nom'>Nom/Prénom</a></td>";
if ($multiclasses) {
	echo "<td><a href='edit_eleves.php?id_groupe=$id_groupe&amp;id_classe=$id_classe&amp;order_by=classe'>Classe</a></td>";
}
foreach ($current_group["periodes"] as $period) {
	if($period["num_periode"]!=""){
		echo "<td>" . $period["nom_periode"] . "</td>";
	}
}
echo "<td>&nbsp;</td>";
echo "<td>Coef</td>";
echo "</tr>\n";

$conditions = "e.login = j.login and (";
foreach ($current_group["classes"]["list"] as $query_id_classe) {
	$conditions .= "j.id_classe = '" . $query_id_classe . "' or ";
}
$conditions = substr($conditions, 0, -4);
$conditions .= ") and c.id = j.id_classe";

// Définition de l'ordre de la liste
if ($order_by == "classe") {
	// Classement par classe puis nom puis prénom
	$order_conditions = "j.id_classe, e.nom, e.prenom";
} elseif ($order_by == "nom") {
	$order_conditions = "e.nom, e.prenom";
}

//=============================
// AJOUT: boireaus
echo "<tr><td>";
//=============================

$calldata = mysql_query("SELECT distinct(j.login), j.id_classe, c.classe, e.nom, e.prenom FROM eleves e, j_eleves_classes j, classes c WHERE (" . $conditions . ") ORDER BY ".$order_conditions);
$nb = mysql_num_rows($calldata);
$eleves_list = array();
for ($i=0;$i<$nb;$i++) {
	$e_login = mysql_result($calldata, $i, "login");
	//================================
	// AJOUT: boireaus
	echo "<input type='hidden' name='login_eleve[$i]' value='$e_login' />\n";
	//================================
	$e_nom = mysql_result($calldata, $i, "nom");
	$e_prenom = mysql_result($calldata, $i, "prenom");
	$e_id_classe = mysql_result($calldata, $i, "id_classe");
	$classe = mysql_result($calldata, $i, "classe");
	$eleves_list["list"][] = $e_login;
	$eleves_list["users"][$e_login] = array("login" => $e_login, "nom" => $e_nom, "prenom" => $e_prenom, "classe" => $classe, "id_classe" => $e_id_classe);
}

$total_eleves = $eleves_list["list"];
foreach ($current_group["periodes"] as $period) {
	if($period["num_periode"]!=""){
		$total_eleves = array_merge($total_eleves, (array)$reg_eleves[$period["num_periode"]]);
	}
}
$total_eleves = array_unique($total_eleves);

$elements = array();
foreach ($current_group["periodes"] as $period) {
	$elements[$period["num_periode"]] = null;
	foreach($total_eleves as $e_login) {
		$elements[$period["num_periode"]] .= "'eleve_" . $period["num_periode"] . "_"  . $e_login  . "',";
	}
    $elements[$period["num_periode"]] = substr($elements[$period["num_periode"]], 0, -1);
}

//=============================
// MODIF: boireaus
//echo "<tr><td>&nbsp;</td>";
echo "&nbsp;</td>\n";
//=============================

if ($multiclasses) { echo "<td>&nbsp;</td>"; }
echo "\n";
foreach ($current_group["periodes"] as $period) {
	if($period["num_periode"]!=""){
		//echo "<td>";
		//echo "<a href=\"javascript:CochePeriode(" . $elements[$period["num_periode"]] . ")\">Tout</a> <br/> <a href=\"javascript:DecochePeriode(" . $elements[$period["num_periode"]] . ")\">Aucun</a>";
		echo "<td align='center'>";
		echo "<a href=\"javascript:CochePeriode(" . $elements[$period["num_periode"]] . ")\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecochePeriode(" . $elements[$period["num_periode"]] . ")\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
		echo "<br/>Inscrits : " . count($current_group["eleves"][$period["num_periode"]]["list"]);
		echo "</td>\n";
	}
}
echo "<td>&nbsp;</td><td>&nbsp;</td>\n";
echo "</tr>\n";

// Marqueurs pour identifier quand on change de classe dans la liste
$prev_classe = 0;
$new_classe = 0;
$empty_td = false;

foreach($total_eleves as $e_login) {

	//$new_classe = $eleves_list["users"][$e_login]["id_classe"];
	if(isset($eleves_list["users"][$e_login])){
		$new_classe = $eleves_list["users"][$e_login]["id_classe"];
	}
	else{
		$new_classe="BIZARRE";
	}

	if ($new_classe != $prev_classe and $order_by == "classe" and $multiclasses) {
	echo "<tr style='background-color: #CCCCCC;'>\n";
		echo "<td colspan=3 style='padding: 5px; font-weight: bold;'>";
		echo "Classe de : " . $eleves_list["users"][$e_login]["classe"];
		echo "</td>\n";
		foreach ($current_group["periodes"] as $period) {
			echo "<td>&nbsp;</td>\n";
		}
		echo "<td>&nbsp;</td>\n";
		echo "</tr>\n";
		$prev_classe = $new_classe;
	}

	echo "<tr>";
	if (array_key_exists($e_login, $eleves_list["users"])){
			echo "<td>" . $eleves_list["users"][$e_login]["prenom"] . " " .
				$eleves_list["users"][$e_login]["nom"] .
				"</td>";
			if ($multiclasses) echo "<td>" . $eleves_list["users"][$e_login]["classe"] . "</td>";
			echo "\n";
	} else {
		/*
		echo "<td>" . $e_login . "</td>" .
			"<td>" . $current_group["eleves"]["users"][$e_login]["prenom"] . " " .
			$current_group["eleves"]["users"][$e_login]["nom"] .
			"</td>";
		*/
		echo "<td>";
		if($new_classe=="BIZARRE"){
			echo "<font color='red'>$e_login</font>";
		}
		else{
			echo "$e_login";
		}
		echo "</td>";
		if ($multiclasses) echo "<td>" . $current_group["eleves"]["users"][$e_login]["classe"] . "</td>";
		echo "\n";
	}


	foreach ($current_group["periodes"] as $period) {
		if($period["num_periode"]!=""){
			echo "<td align='center'>";

			$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$e_login' AND id_classe='".$new_classe."' AND periode='".$period["num_periode"]."'";
			$res_test=mysql_query($sql);
			if(mysql_num_rows($res_test)>0){
				echo "<input type='checkbox' name='eleve_".$period["num_periode"] . "_" . $e_login."' ";
				if (in_array($e_login, (array)$current_group["eleves"][$period["num_periode"]]["list"])) {
						echo " CHECKED />";
				} else {
					echo " />";
				}
			}
			else{
				echo "&nbsp;\n";
				//echo "<input type='hidden' name='eleve_".$period["num_periode"] . "_" . $e_login."' />\n";
			}
			echo "</td>\n";
		}
	}

	$elementlist = null;
	foreach ($current_group["periodes"] as $period) {
		if($period["num_periode"]!=""){
			$elementlist .= "'eleve_" . $period["num_periode"] . "_" . $e_login . "',";
		}
	}
	$elementlist = substr($elementlist, 0, -1);

	//echo "<td><a href=\"javascript:CochePeriode($elementlist)\">Tout</a> // <a href=\"javascript:DecochePeriode($elementlist)\">Aucun</a></td>\n";
	echo "<td><a href=\"javascript:CochePeriode($elementlist)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecochePeriode($elementlist)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a></td>\n";
	$setting = get_eleve_groupe_setting($e_login, $id_groupe, "coef");
	if (!$setting) $setting = array(null);
	echo "<td><input type='text' size='3' name='setting_coef_" . $e_login . "' value='" . $setting[0] . "' /></td>\n";
	echo "</tr>\n";

}
echo "</table>\n";
echo "<input type='hidden' name='is_posted' value='1' />";
echo "<input type='hidden' name='mode' value='" . $mode . "' />";
echo "<input type='hidden' name='id_groupe' value='" . $id_groupe . "' />";
echo "<input type='hidden' name='id_classe' value='" . $id_classe . "' />";
echo "<p align='center'><input type='submit' value='Enregistrer' /></p>";

?>
</form>
</body>
</html>
