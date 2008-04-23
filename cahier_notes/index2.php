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

// INSERT INTO `droits` VALUES ('/cahier_notes/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation des moyennes des carnets de notes', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// On fait quelques tests si le statut est 'prof', pour vérifier les restrictions d'accès
if ($_SESSION['statut'] == "professeur") {
	if ( (getSettingValue("GepiAccesMoyennesProf") != "yes") AND
         (getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes") AND
         (getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes")
       ) {
       	tentative_intrusion("1","Tentative d'accès par un prof aux moyennes des carnets de notes sans avoir les autorisations nécessaires.");
       	echo "Vous n'êtes pas autorisé à être ici.";
		require ("../lib/footer.inc.php");
		die();
       }

}


$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

if (isset($id_classe)) {
	// On regarde si le type est correct :
	if (!is_numeric($id_classe)) {
		tentative_intrusion("2", "Changement de la valeur de id_classe pour un type non numérique.");
		echo "Erreur.";
		require ("../lib/footer.inc.php");
		die();
	}
	// On teste si le professeur a le droit d'accéder à cette classe
	if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
		$test = mysql_num_rows(mysql_query("SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')"));
		if ($test == "0") {
			tentative_intrusion("2", "Tentative d'accès par un prof à une classe dans laquelle il n'enseigne pas, sans en avoir l'autorisation.");
			echo "Vous ne pouvez pas accéder à cette classe car vous n'y êtes pas professeur !";
			require ("../lib/footer.inc.php");
			die();
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Visualisation des moyennes des carnets de notes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a>|
<?php
if (isset($id_classe)) {
	$current_eleve_classe = sql_query1("SELECT classe FROM classes WHERE id='$id_classe'");
	echo "<a href=\"index2.php\">Choisir une autre classe</a> | Classe : ".$current_eleve_classe." |</p>\n";
	echo "<form target=\"_blank\" name=\"visu_toutes_notes\" method=\"post\" action=\"visu_toutes_notes2.php\">\n";
	//echo "<form target=\"_blank\" name=\"visu_toutes_notes\" method=\"post\" action=\"visu_toutes_notes2.php\">\n";
	echo "<table border=\"1\" cellspacing=\"1\" cellpadding=\"10\"><tr>";
	echo "<td valign=\"top\"><b>Choisissez&nbsp;la&nbsp;période&nbsp;:&nbsp;</b><br />\n";
	include "../lib/periodes.inc.php";
	$i="1";
	while ($i < $nb_periode) {
		echo "<br />\n<input type=\"radio\" name=\"num_periode\" id='num_periode_$i' value=\"$i\" ";
		if ($i == 1) echo "checked ";
		echo "/>&nbsp;";
		echo "<label for='num_periode_$i' style='cursor:pointer;'>\n";
		echo ucfirst($nom_periode[$i]);
		echo "</label>\n";
		$i++;
	}
	echo "<br />\n<input type=\"radio\" name=\"num_periode\" id='num_periode_annee' value=\"annee\" />&nbsp;";
	echo "<label for='num_periode_annee' style='cursor:pointer;'>\n";
	echo "Année entière";
	echo "</label>\n";
	echo "</td>\n";

	echo "<td valign=\"top\">\n";
    echo "<b>Paramètres d'affichage</b><br />\n";
	echo "<input type=\"hidden\" name=\"id_classe\" value=\"".$id_classe."\" />";

	echo "<table border='0' width='100%'>\n";
	echo "<tr>\n";
	echo "<td>\n";

		echo "<table border='0'>\n";
		echo "<tr>\n";
		echo "<td>Largeur en pixel du tableau : </td>\n";
		echo "<td><input type=text name=larg_tab size=3 value=\"680\" /></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>Bords en pixel du tableau : </td>\n";
		echo "<td><input type=text name=bord size=3 value=\"1\" /></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>\n";
		echo "<label for='couleur_alterne' style='cursor:pointer;'>\n";
		echo "Couleurs de fond des lignes alternées : \n";
		echo "</label>\n";
		echo "</td>\n";
		echo "<td><input type=\"checkbox\" name=\"couleur_alterne\" id=\"couleur_alterne\" checked /></td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "<td>\n";

		echo "<table border='0'>\n";
		echo "<tr>\n";
		echo "<td><input type=\"checkbox\" name=\"aff_abs\" id=\"aff_abs\" checked /></td>\n";
		echo "<td>\n";
		echo "<label for='aff_abs' style='cursor:pointer;'>\n";
		echo "Afficher les absences";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td><input type=\"checkbox\" name=\"aff_reg\" id=\"aff_reg\" checked /></td>\n";
		echo "<td>\n";
		echo "<label for='aff_reg' style='cursor:pointer;'>\n";
		echo "Afficher le régime\n";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td><input type=\"checkbox\" name=\"aff_doub\" id=\"aff_doub\" checked /></td>\n";
		echo "<td>\n";
		echo "<label for='aff_doub' style='cursor:pointer;'>\n";
		echo "Afficher la mention doublant\n";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";

		$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
		// On teste la présence d'au moins un coeff pour afficher la colonne des coef
		$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));

		if (($affiche_rang == 'y') and ($test_coef != 0)) {
			echo "<tr>\n";
			echo "<td><input type=\"checkbox\" name=\"aff_rang\" id=\"aff_rang\" checked /></td>\n";
			echo "<td>\n";
			echo "<label for='aff_rang' style='cursor:pointer;'>\n";
			echo "Afficher le rang des élèves\n";
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}

		echo "<tr>\n";
		echo "<td valign='top'><input type=\"checkbox\" name=\"aff_date_naiss\" id=\"aff_date_naiss\" /></td>\n";
		echo "<td>\n";
		echo "<label for='aff_date_naiss' style='cursor:pointer;'>\n";
		echo "Afficher la date de naissance des élèves\n";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

/*
	echo "<b>Paramètres d'affichage</b><br />\n<input type=\"hidden\" name=\"id_classe\" value=\"".$id_classe."\" />\n";
	echo "<br />\nLargeur en pixel du tableau : <input type=text name=larg_tab size=3 value=\"680\" />\n";
	echo "<br />\nBords en pixel du tableau : <input type=text name=bord size=3 value=\"1\" />\n";
	echo "<br />\nCouleurs de fond des lignes alternées : <input type=\"checkbox\" name=\"couleur_alterne\" checked />\n";
	echo "<br /><br /><table cellpadding=\"3\"><tr><td>\n<input type=\"checkbox\" name=\"aff_abs\" checked />Afficher les absences</td>
	<td><input type=\"checkbox\" name=\"aff_reg\" checked /> Afficher le régime</td>
	<td><input type=\"checkbox\" name=\"aff_doub\" checked />Afficher la mention doublant</td>\n";
	$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
	// On teste la présence d'au moins un coeff pour afficher la colonne des coef
	$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));


	if (($affiche_rang == 'y') and ($test_coef != 0)) {
		echo "<td><input type=\"checkbox\" name=\"aff_rang\" checked />Afficher le rang des élèves</td>\n";
	}

	echo "</tr></table>\n";
*/



	echo "<br />\n<center><input type=\"submit\" name=\"ok\" value=\"Valider\" /></center>\n";
	echo "<br />\n<span class='small'>Remarque : le tableau des notes s'affiche sans en-tête et dans une nouvelle page. Pour revenir à cet écran, il vous suffit de fermer la fenêtre du tableau des notes.</span>\n";
	echo "</td></tr>\n</table>\n</form>\n";
} else {
	echo "</p>\n";
	echo "<p><b>Visualiser les moyennes des carnets de notes par classe :</b><br />\n";

	//$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");

	//if (($_SESSION['statut'] == 'scolarite') AND (getSettingValue("GepiAccesReleveScol") == "yes")) {
	if($_SESSION['statut'] == 'scolarite'){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	}
	//elseif(($_SESSION['statut'] == 'professeur')&&(getSettingValue("GepiAccesReleveProf")=='yes')){
	elseif($_SESSION['statut'] == 'professeur' and getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes"){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe");
	}
	elseif($_SESSION['statut'] == 'professeur' and getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes") {
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c  ORDER BY c.classe");
	}
	//elseif(($_SESSION['statut'] == 'cpe')&&(getSettingValue("GepiAccesReleveCpe")=='yes')){
	elseif($_SESSION['statut'] == 'cpe'){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	}

	/*
	// Si on interdit l'accès, il faudrait contrôler que isset($appel_donnees)
	if(){
	}
	*/

	$lignes = mysql_num_rows($appel_donnees);
	/*
	$nb_class_par_colonne=round($lignes/3);
        echo "<table width='100%'>\n";
        echo "<tr valign='top' align='center'>\n";
	$i = 0;
        echo "<td>\n";
	while($i < $lignes){
		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td>\n";
		}
		$id_classe = mysql_result($appel_donnees, $i, "id");
		$display_class = mysql_result($appel_donnees, $i, "classe");
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>".ucfirst($display_class)."</a><br />\n";
		$i++;
	}
        echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
	*/

	if($lignes==0){
		echo "<p>Aucune classe ne vous est attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
	}
	else{
		$i = 0;
		unset($tab_lien);
		unset($tab_txt);
		while ($i < $lignes){
			$tab_lien[$i] = $_SERVER['PHP_SELF']."?id_classe=".mysql_result($appel_donnees, $i, "id");
			$tab_txt[$i] = mysql_result($appel_donnees, $i, "classe");
			$i++;

		}
		tab_liste($tab_txt,$tab_lien,3);
	}
	//echo "</p>\n";
}
echo "<p><i>Remarque:</i> Les moyennes visualisées ici sont des photos à un instant t de ce qui a été saisi par les professeurs.<br />\n";
echo "Cela ne correspond pas nécessairement à ce qui apparaitra sur le bulletin après saisie d'autres résultats et ajustements éventuels des coefficients.</p>\n";
if ($_SESSION['statut'] == "professeur"
	AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes"
	AND getSettingValue("GepiAccesMoyennesProfToutesTousEleves") != "yes") {
		echo "<p>Si vous n'enseignez pas à des classes entières, seuls les élèves auxquels vous enseignez apparaîtront dans la liste, et les moyennes calculés ne prendront en compte que les élèves affichés.</p>";
	}
require ("../lib/footer.inc.php");
?>