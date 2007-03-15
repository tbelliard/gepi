<?php
/*
 * Last modification  : 14/03/2005
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


//**************** EN-TETE *********************
$titre_page = "Edition des bulletins";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiProfImprBul")!='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>";
if (!isset($id_classe)) {

    if ($_SESSION["statut"] == "scolarite") {
        //$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
        $calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
    } else {
        $calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)");
    }

    $nombreligne = mysql_num_rows($calldata);
    if ($nombreligne > "1") {
	  echo " | Total : $nombreligne ";
	  echo "classes";
	} else {
	  echo " | Total : $nombreligne ";
	  echo "classe";
    }
    echo "</p>\n";
    echo "<p>Cliquez sur la classe pour laquelle vous souhaitez extraire les bulletins.<br />\n";

	$nb_class_par_colonne=round($nombreligne/3);
        //echo "<table width='100%' border='1'>\n";
        echo "<table width='100%'>\n";
        echo "<tr valign='top' align='center'>\n";

    $i = 0;

        echo "<td align='left'>\n";

    while ($i < $nombreligne){

		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td align='left'>\n";
		}

        $ide_classe = mysql_result($calldata, $i, "id");
        $classe_liste = mysql_result($calldata, $i, "classe");
        echo "<br /><a href='index.php?id_classe=$ide_classe'>$classe_liste</a>\n";
        $i++;
    }
        echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
}
if (isset($id_classe)) {
	echo " | <a href=\"index.php\">Choisir une autre classe</a>";
/*
	// On choisit le periode :
	echo "<p><b>Choisissez la période : </b></p>\n";
	include "../lib/periodes.inc.php";
	$i="1";
	while ($i < $nb_periode) {
		if ($ver_periode[$i] == "N") {
			echo "<p><b>".ucfirst($nom_periode[$i])."</b> : édition Impossible ";
			echo " ($gepiOpenPeriodLabel)";
		} else {
			echo "<p><a href='edit.php?id_classe=$id_classe&amp;periode_num=$i' target='bull'><b>".ucfirst($nom_periode[$i])."</b></a>";
			if ($ver_periode[$i] == "P")  echo " (Période partiellement close, seule la saisie des avis du conseil de classe est possible)";
			if ($ver_periode[$i] == "O")  echo " (Période entièrement close, plus aucune saisie/modification n'est possible)";
		}
		echo "</p>\n";
		$i++;
	}
*/
	echo "<p><b>Choisissez la période : </b></p>\n";
	include "../lib/periodes.inc.php";
	$i="1";
	//echo "<form name='choix' action='edit.php' target='bull' method='post'>\n";
	echo "<form name='choix' action='edit.php' target='_blank' method='post'>\n";
	//echo "<form name='choix' action='edit.php' target='bull' method='get' >\n";
	echo "<input type='hidden' name='id_classe' value='$id_classe' /> \n";
	echo "<table border='0'>\n";
	$num_per_close=0;
	$nb_per_close=0;
	while ($i < $nb_periode) {
		echo "<tr>\n";
		if ($ver_periode[$i] == "N") {
			//echo "<td style='text-align:center; color:red;'>X</td>\n";
			//echo "<td style='text-align:center; color:red;'><img src='../images/disabled.png' alt='impossible' /></td>\n";
			echo "<td style='text-align:center; color:red;'>&nbsp;</td>\n";
			echo "<td><b>".ucfirst($nom_periode[$i])."</b> : édition impossible ";
			echo " (<i>$gepiOpenPeriodLabel</i>)</td>\n";
		} else {
			//echo "<td align='center'><input type='radio' name='periode_num' id='id_periode_num' value='$i' /> </td>\n";
			//echo "<td align='center'><input type='radio' name='periode_num' id='id_periode_num' value='$i'";
			echo "<td align='center'><input type='radio' name='periode_num' value='$i'";
			if($nb_per_close==0){
				echo " checked";
			}
			echo " /> </td>\n";
			echo "<td><b>".ucfirst($nom_periode[$i])."</b>";
			if ($ver_periode[$i] == "P"){echo " (<i>Période partiellement close, seule la saisie des avis du conseil de classe est possible</i>)";}
			if ($ver_periode[$i] == "O"){echo " (<i>Période entièrement close, plus aucune saisie/modification n'est possible</i>)";}
			echo "</td>\n";
			$num_per_close=$i;
			$nb_per_close++;
		}
		echo "</tr>\n";
		$i++;
	}
	echo "</table>\n";

/*
// Je ne parviens pas à cocher la dernière période close (si elle existe)...
	if($nb_per_close>0){
		echo "<script type='text/javascript' language='javascript'>
//document.getElementById('id_periode_num').element[$num_per_close].checked=true;
//document.forms[0].periode_num[$num_per_close].checked=true;
//document.elements['choix'].elements['periode_num'][$num_per_close].checked=true;
//document.forms[0].periode_num[$num_per_close].checked=true;
radio=document.getElementById('id_periode_num');
for(i=0;i<$nb_per_close;i++){
	alert('radio['+i+']='+radio[i].value);
}
</script>\n";
	}
*/

	// AJOUTER LES AUTRES PARAMETRES
	echo "<p><b>Et les bulletins à imprimer: </b></p>\n";
	$sql="SELECT DISTINCT e.login,e.nom,e.prenom FROM j_eleves_classes jec, eleves e WHERE jec.login=e.login AND jec.id_classe='$id_classe' ORDER BY e.nom,e.prenom";
	$res_ele=mysql_query($sql);
	if(mysql_num_rows($res_ele)==0){
		echo "<p style='color:red;'>ERREUR: La classe choisie ne compterait aucun élève?</p>\n";
		echo "</form>\n";
		echo "</body>\n";
		echo "</html>\n";
		die();
	}
	else{
		/*
		echo "<p>\n";
		echo "<select name='liste_login_ele'>\n";
		echo "<option value='_CLASSE_ENTIERE_' selected>Classe entière</option>\n";
		while($lig_ele=mysql_fetch_object($res_ele)){
			echo "<option value='$lig_ele->login'>".strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom))."</option>\n";
		}
		echo "</select>\n";
		//echo "<br />\n";
		echo "</p>\n";
		*/

		echo "<table border='0'>\n";
		echo "<tr>\n";
		echo "<td valign='top'><input type='radio' name='selection' value='_CLASSE_ENTIERE_' onchange=\"affiche_nb_ele_select();\" checked /></td>\n";
		echo "<td valign='top'>Classe entière</td>\n";
		echo "<td valign='top'> ou </td>\n";
		//echo "</tr>\n";
		//echo "<tr>\n";
		echo "<td valign='top'><input type='radio' name='selection' id='selection_ele' value='_SELECTION_' onchange=\"affiche_nb_ele_select();\" /></td>\n";
		echo "<td valign='top'>Sélection<br />\n";
		echo "<select id='liste_login_ele' name='liste_login_ele[]' multiple='yes' size='5' onchange=\"document.getElementById('selection_ele').checked=true;affiche_nb_ele_select();\">\n";
		//echo "<option value='_CLASSE_ENTIERE_' selected>Classe entière</option>\n";

		while($lig_ele=mysql_fetch_object($res_ele)){
			echo "<option value='$lig_ele->login'>".strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom))."</option>\n";
		}
		echo "</select>\n";
		echo "</td>\n";
		echo "<td valign='bottom'>\n";
		echo "<div id='nb_ele_select'>\n";
		echo "&nbsp;\n";
		echo "</div>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}

	echo "<script type='text/javascript'>
	function affiche_nb_ele_select(){
		if(document.getElementById('selection_ele').checked==true){
			num=0;
			//for(i=0;i<document.forms['choix'].selection.options.length;i++){
			//	if(document.forms['choix'].selection.options[i].selected){
			for(i=0;i<document.getElementById('liste_login_ele').options.length;i++){
				if(document.getElementById('liste_login_ele').options[i].selected){
					num++;
				}
			}
		}
		else{
			num=".mysql_num_rows($res_ele).";
		}
		document.getElementById('nb_ele_select').innerHTML=num+' élèves sélectionnés.';
	}
</script>\n";

	echo "<table border='0'>\n";
	echo "<tr><td valign='top'><input type='checkbox' name='un_seul_bull_par_famille' value='oui' /></td><td>Ne pas imprimer de bulletin pour le deuxième parent<br />(<i>même dans le cas de parents séparés</i>).</td></tr>\n";

// ERIC Ajout Choix du bulletin
    if(!getSettingValue("bull_intitule_app")){
		$bull_intitule_app="Appréciations/Conseils";
	}
	else{
		$bull_intitule_app=getSettingValue("bull_intitule_app");
	}
    //echo "<table border='0'>\n";
	    echo "<tr>\n";
			echo "<td colspan=\"2\"><br /><br /> <b>Et l'apparence du bulletin (Emplacement des différentes colonnes).<br /></b>";
			echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
			echo "<td align='center'><input type='radio' name='choix_bulletin' value='0' />";
			echo "</td>\n";
			echo "<td><b>Choix 1</b> : <i>Toutes les informations chiffrées sur la classe et l'élève sont avant la colonne \"".$bull_intitule_app.".\"</i>";
			echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
			echo "<td align='center'><input type='radio' name='choix_bulletin' value='1' checked />";
			echo "</td>\n";
			echo "<td><b>Choix 2</b> : <i>Idem choix 1. Les informations sur la classe sont regroupées en une catégorie \"Pour la classe\".</i>";
			echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
			echo "<td align='center'><input type='radio' name='choix_bulletin' value='2' />";
			echo "</td>\n";
			echo "<td><b>Choix 3</b> : <i>Idem choix 2. Les informations pour la classe sont situées après la colonne \"".$bull_intitule_app.".\" </i>";
			echo "</td>\n";
		echo "</tr>\n";
	//echo "</table>\n";
// Fin Ajout


/*
	echo "<tr><td valign='top'><input type='checkbox' name='ne_pas_afficher_moy_gen' value='oui' /><td><td>Ne pas afficher la moyenne générale (<i>même si l'affichage des coefficients de matières est activé</i>).<br /><i>Rappel:</i> La moyenne générale ne peut apparaitre que si un des coefficients de matière au moins est non nul.</td></tr>\n";
*/
	echo "<tr><td valign='top'><input type='checkbox' name='min_max_moyclas' value='1' /></td><td>Afficher les moyennes minimale, classe et maximale dans une seule colonne pour gagner de la place pour l'appréciation.</td></tr>\n";

	// A FAIRE:
	// Tester et ne pas afficher:
	// - si tous les coeff sont à 1
	$test_coef=mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef!='1.0')"));
	if($test_coef>0){
		echo "<tr>\n";
		echo "<td colspan=\"2\"><br /><br /><b>Calcul des moyennes générales";
		// Ne pas afficher la mention de catégorie, si on n'affiche pas les catégories dans cette classe.
		$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
		if ($affiche_categories == "y") {
			echo " et par catégorie";
		}
		echo ".<br /></b></td>\n";
		echo "</tr>\n";
		echo "<tr><td valign='top'><input type='checkbox' name='coefficients_a_1' value='oui' /></td><td>Forcer les coefficients des matières à 1, indépendamment des coefficients saisis dans les paramètres de la classe.</td></tr>\n";
	}
	echo "</table>\n";

	echo "<p style='text-align:center;'><input type='submit' name='Valider' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<br />\n<center><table border=\"1\" cellpadding=\"10\" width=\"80%\"><tr><td>";
	echo "<center><b>Avertissement</b></center><br /><br />La mise en page des bulletins est très différente à l'écran et à l'impression.
	Avant d'imprimer les bulletins :
	<ul>
	<li>Veillez à utiliser la fonction \"aperçu avant impression\" disponible sur la plupart des navigateurs.</li>
	<li>Veillez à régler les paramètres de marges, d'en-tête et de pied de page.</li>
	</ul>
	</td></tr></table></center>\n";
}
require("../lib/footer.inc.php");
?>