<?php

/*
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Didier Blanqui
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

//$variables_non_protegees = 'yes';
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
$sql = "SELECT 1=1 FROM `droits` WHERE id='/mod_discipline/definir_natures.php';";
$test = mysqli_query($GLOBALS["mysqli"], $sql);
if (mysqli_num_rows($test) == 0) {
	$sql = "INSERT INTO droits VALUES ( '/mod_discipline/definir_natures.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir les natures', '')";
	$test = mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(mb_strtolower(mb_substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');

$acces_ok="n";
if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiDiscDefinirNaturesCpe')))||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('GepiDiscDefinirNaturesScol')))) {
	$acces_ok="y";
}
else {
	$msg="Vous n'avez pas le droit de définir les natures d'".$mod_disc_terme_incident."s.";
	header("Location: ./index.php?msg=$msg");
	die();
}

$msg = "";

$suppr_nature = isset($_POST['suppr_nature']) ? $_POST['suppr_nature'] : NULL;

$id_nature= isset($_POST['id_nature']) ? $_POST['id_nature'] : NULL;
$id_categorie= isset($_POST['id_categorie']) ? $_POST['id_categorie'] : NULL;
$cpt = isset($_POST['cpt']) ? $_POST['cpt'] : 0;

$nature = isset($_POST['nature']) ? $_POST['nature'] : NULL;
$id_categorie_nature_nouvelle= isset($_POST['id_categorie_nature_nouvelle']) ? $_POST['id_categorie_nature_nouvelle'] : 0;

if (isset($suppr_nature)) {
	check_token();

	for ($i = 0; $i < $cpt; $i++) {
		if (isset($suppr_nature[$i])) {
			$sql = "DELETE FROM s_natures WHERE id='$suppr_nature[$i]';";
			//echo "$sql<br />";
			$suppr = mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$suppr) {
				//$msg.="ERREUR lors de la suppression de la qualité n°".$suppr_lieu[$i].".<br />\n";
				$msg.="ERREUR lors de la suppression de la nature n°" . $suppr_nature[$i] . ".<br />\n";
			} else {
				$msg.="Suppression de la nature n°" . $suppr_nature[$i] . ".<br />\n";
			}
		}
	}
}

$tab_categorie=array();
$sql = "SELECT * FROM s_categories ORDER BY categorie;";
//echo "$sql<br />";
$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res2)>0) {
	while ($lig2=mysqli_fetch_object($res2)) {
		$tab_categorie[$lig2->id]=$lig2->categorie;
	}
}

if ((isset($nature))&&($nature != '')) {
	check_token();

	$a_enregistrer = 'y';

	$sql = "SELECT nature FROM s_natures ORDER BY nature;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$tab_nature = array();
		while ($lig = mysqli_fetch_object($res)) {
			$tab_nature[] = $lig->nature;
		}

		if (in_array($nature, $tab_nature)) {
			$a_enregistrer = 'n';
			$msg.="La nature proposée existe déjà.<br />";
		}
	}

	if ($a_enregistrer == 'y') {
		$nature=suppression_sauts_de_lignes_surnumeraires($nature);

		if(!array_key_exists($id_categorie_nature_nouvelle,$tab_categorie)) {
			$id_categorie_nature_nouvelle=0;
			$msg.="La catégorie choisie pour la nouvelle nature n'existe pas.<br />";
		}

		$sql = "INSERT INTO s_natures SET nature='" . ((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $nature) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "', id_categorie='".$id_categorie_nature_nouvelle."';";
		//echo "$sql<br />";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		if (!$res) {
			$msg.="ERREUR lors de l'enregistrement de " . $nature . "<br />\n";
		} else {
			$msg.="Enregistrement de " . $nature . "<br />\n";
			//echo "Ajout de la nouvelle nature avec l'id ".mysql_insert_id()."<br />";
		}
	}
}


if((isset($id_nature))&&(count($id_nature)>0)&&(isset($id_categorie))&&(count($id_categorie)>0)) {
	check_token();

	for($i=0;$i<count($id_nature);$i++) {
		if(($id_categorie[$i]==0)||(array_key_exists($id_categorie[$i],$tab_categorie))) {
			$sql="UPDATE s_natures SET id_categorie='$id_categorie[$i]' WHERE id='$id_nature[$i]';";
			//echo "$sql<br />";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$update) {
				//$msg.="Erreur lors de la mise à jour de la catégorie pour la nature ".$tab_nature[$id_nature[$i]]['nature']."<br />";
				$msg.="Erreur lors de la mise à jour de la catégorie pour la nature n°".$id_nature[$i]."<br />";
			}
		}
	}
}

$tab_nature=array();
//$sql = "(SELECT sn.* FROM s_natures sn, s_categories sc WHERE sn.id_categorie=sc.id ORDER BY sc.categorie) UNION (SELECT * FROM s_natures WHERE id_categorie NOT IN (SELECT id FROM s_categories) ORDER BY nature);";
//$sql = "(SELECT * FROM s_natures WHERE id_categorie NOT IN (SELECT id FROM s_categories) ORDER BY nature) UNION (SELECT sn.* FROM s_natures sn, s_categories sc WHERE sn.id_categorie=sc.id ORDER BY sc.categorie);";
// Il y a un problème de tri avec UNION SELECT... je passe à deux requêtes
$sql = "SELECT sn.* FROM s_natures sn, s_categories sc WHERE sn.id_categorie=sc.id ORDER BY sc.categorie;";
//echo "$sql<br />";
$res = mysqli_query($GLOBALS["mysqli"], $sql);

$sql = "SELECT * FROM s_natures WHERE id_categorie NOT IN (SELECT id FROM s_categories) ORDER BY nature;";
//echo "$sql<br />";
$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
if((mysqli_num_rows($res)>0)||(mysqli_num_rows($res2)>0)) {
	$cpt=0;
	while ($lig=mysqli_fetch_object($res)) {
		$tab_nature[$cpt]['id']=$lig->id;
		$tab_nature[$cpt]['nature']=$lig->nature;
		$tab_nature[$cpt]['id_categorie']=$lig->id_categorie;
		$cpt++;
	}
	while ($lig2=mysqli_fetch_object($res2)) {
		$tab_nature[$cpt]['id']=$lig2->id;
		$tab_nature[$cpt]['nature']=$lig2->nature;
		$tab_nature[$cpt]['id_categorie']=$lig2->id_categorie;
		$cpt++;
	}
}
else {
	$tab_natures_par_defaut=array('Refus de travail', 'Travail non fait', 'Degradation', 'Retards Répétés', 'Oubli de matériel', 'Insolence et comportement', 'Violence verbale', 'Violence physique', 'Violence verbale et physique', 'Bavardages répétés');

	for($i=0;$i<count($tab_natures_par_defaut);$i++) {
		$sql="INSERT INTO s_natures SET nature='".$tab_natures_par_defaut[$i]."';";
		//echo "$sql<br />";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	}

	$sql = "SELECT * FROM s_natures ORDER BY nature;";
	//echo "$sql<br />";
	$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res2)>0) {
		$cpt=0;
		while ($lig2=mysqli_fetch_object($res2)) {
			$tab_nature[$cpt]['id']=$lig2->id;
			$tab_nature[$cpt]['nature']=$lig2->nature;
			$tab_nature[$cpt]['id_categorie']=$lig2->id_categorie;
			$cpt++;
		}
	}
}

/*
if((isset($id_nature))&&(count($id_nature)>0)&&(isset($id_categorie))&&(count($id_categorie)>0)) {
	check_token();

	for($i=0;$i<count($id_nature);$i++) {
		if(($id_categorie[$i]==0)||(array_key_exists($id_categorie[$i],$tab_categorie))) {
			$sql="UPDATE s_natures SET id_categorie='$id_categorie[$i]' WHERE id='$id_nature[$i]';";
			//echo "$sql<br />";
			$update=mysql_query($sql);
			if (!$update) {
				$msg.="Erreur lors de la mise à jour de la catégorie pour la nature ".$tab_nature[$id_nature[$i]]['nature']."<br />";
			}
		}
	}
}
*/

if(isset($_POST['DisciplineNaturesRestreintes'])) {
	check_token();

	$DisciplineNaturesRestreintes=$_POST['DisciplineNaturesRestreintes'];

	$reg_DisciplineNaturesRestreintes=saveSetting("DisciplineNaturesRestreintes", $DisciplineNaturesRestreintes);

	if(!$reg_DisciplineNaturesRestreintes) {
		$msg.="Erreur lors de l'enregistrement de 'DisciplineNaturesRestreintes' avec la valeur '$DisciplineNaturesRestreintes'<br />\n";
	}
	else {
		$msg.="Enregistrement de 'DisciplineNaturesRestreintes' avec la valeur '$DisciplineNaturesRestreintes' effectué.<br />\n";
	}
}

$DisciplineNaturesRestreintes=getSettingValue('DisciplineNaturesRestreintes');
if($DisciplineNaturesRestreintes=='') {
	$DisciplineNaturesRestreintes=1;
}

$themessage = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Discipline: Définition des natures";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "</p>\n";

echo "<form enctype='multipart/form-data' action='" . $_SERVER['PHP_SELF'] . "' method='post' name='formulaire'>\n";
echo add_token_field();

echo "<p class='bold'>Saisie des natures d'".$mod_disc_terme_incident."s&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt = 0;

echo "<p>Natures existantes&nbsp;:</p>\n";
echo "<table class='boireaus' border='1' summary='Tableau des natures existantes'>\n";
echo "<tr>\n";
echo "<th>Nature</th>\n";
echo "<th>Catégorie</th>\n";
echo "<th>Supprimer</th>\n";
echo "</tr>\n";
$alt = 1;
for($i=0;$i<count($tab_nature);$i++) {
	$alt = $alt * (-1);
	echo "<tr class='lig$alt'>\n";

	echo "<td>\n";
	echo "<label for='suppr_nature_$cpt' style='cursor:pointer;'>";
	echo $tab_nature[$i]['nature'];
	echo "</label>";
	echo "</td>\n";

	echo "<td>\n";
	echo "<input type='hidden' name='id_nature[$cpt]' value='".$tab_nature[$i]['id']."' />\n";
	echo "<select name='id_categorie[$cpt]'>\n";
	echo "<option value='0'";
	if($tab_nature[$i]['id_categorie']==0) {echo " selected='true'";}
	echo ">---</option>\n";
	foreach($tab_categorie as $key => $value) {
		echo "<option value='$key'";
		if($tab_nature[$i]['id_categorie']==$key) {echo " selected='true'";}
		echo ">$value</option>\n";
	}
	echo "</select>";
	echo "</td>\n";

	echo "<td><input type='checkbox' name='suppr_nature[]' id='suppr_nature_$cpt' value=\"".$tab_nature[$i]['id']."\" onchange='changement();' /></td>\n";
	echo "</tr>\n";

	$cpt++;
}

echo "</table>\n";

echo "</blockquote>\n";

echo "<table border='0'>\n";
echo "<tr><td>Nouvelle nature&nbsp;: </td><td><input type='text' name='nature' value='' onchange='changement();' /></td></tr>\n";
echo "<tr><td>Catégorie&nbsp;: </td><td>";
echo "<select name='id_categorie_nature_nouvelle'>\n";
echo "<option value='0' selected='true'>---</option>\n";
foreach($tab_categorie as $key => $value) {
	echo "<option value='$key'";
	echo ">$value</option>\n";
}
echo "</select>";
echo "</td></tr>\n";
echo "</table>\n";

echo "<input type='hidden' name='cpt' value='$cpt' />\n";

echo "<p>\n";
echo "<input type='radio' name='DisciplineNaturesRestreintes' id='DisciplineNaturesRestreintes_0' value='0' ";
if($DisciplineNaturesRestreintes=="0") {echo "checked ";}
echo "/><label for='DisciplineNaturesRestreintes_0'> Ne pas utiliser la liste de natures proposées ici.<br />Les utilisateurs pourront saisir des natures d'".$mod_disc_terme_incident." librement et ne se verront proposer que des natures parmi celles saisies précédemment lors d'autres ".$mod_disc_terme_incident."s.</label><br />\n";

echo "<input type='radio' name='DisciplineNaturesRestreintes' id='DisciplineNaturesRestreintes_1' value='1' ";
if($DisciplineNaturesRestreintes=="1") {echo "checked ";}
echo "/><label for='DisciplineNaturesRestreintes_1'> Les utilisateurs pourront saisir des natures d'".$mod_disc_terme_incident." librement, mais ne se verront proposer que les natures de la liste ci-dessus.</label><br />\n";

echo "<input type='radio' name='DisciplineNaturesRestreintes' id='DisciplineNaturesRestreintes_2' value='2' ";
if($DisciplineNaturesRestreintes=="2") {echo "checked ";}
echo "/><label for='DisciplineNaturesRestreintes_2'> Restreindre les natures d'".$mod_disc_terme_incident."s pouvant être sélectionnées aux seules natures ci-dessus.<br />Les utilisateurs devront choisir une des natures de la liste ci-dessus.</label><br />\n";
echo "</p>\n";

echo "<p><br /><input type='hidden' name='is_posted' value='y' /></p>\n";
echo "<p class='center'><input type='submit' name='valider' value='Valider' /></p>\n";
echo "</form>\n";

echo "<p><br /></p>\n";

echo "<p><em>NOTES&nbsp;:</em></p>
<ul>
	<li>
		<p>Restreindre les natures d'".$mod_disc_terme_incident."s pouvant être sélectionnées aux seules natures ci-dessus permet d'éviter une trop grande dispersion des natures (<i>on peut sinon avoir 'Insolence', 'Comportement insolent', 'insolent',...</i>).<br />
		Cependant, trop les restreindre peut gêner les utilisateurs.</p>
	</li>
</ul>\n";

require("../lib/footer.inc.php");
?>
