<?php
/**
 * Visualisation des moyennes des carnets de notes
 * 
 *
 * @copyright Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL, 
 * @package Carnet_de_notes
 * @subpackage affichage
 * @see add_token_field()
 * @see checkAccess()
 * @see getSettingValue()
 * @see Session::security_check()
 * @see sql_query1()
 * @see tentative_intrusion()
 */

/*
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

/**
 * Fichiers d'initialisation
 */
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

// INSERT INTO `droits` VALUES ('/cahier_notes/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation des moyennes des carnets de notes', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

// On fait quelques tests si le statut est 'prof', pour vérifier les restrictions d'accès
if ($_SESSION['statut'] == "professeur") {

	if ( (getSettingValue("GepiAccesMoyennesProf") != "yes") AND
	(getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes") AND
	(getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes")
	) {

		if((getSettingAOui('GepiAccesReleveProfP'))&&(is_pp($_SESSION['login']))) {
			if(isset($id_classe)) {
				if(!is_pp($_SESSION['login'], $id_classe)) {
					$classe=get_nom_classe($id_classe);
					tentative_intrusion("1","Tentative d'accès par un ".getSettingValue('gepi_prof_suivi')." aux moyennes des carnets de notes pour une classe qu'il n'a pas en responsabilité (".$classe.").");
					header("Location: ../accueil.php?msg=Vous n'êtes pas ".getSettingValue('gepi_prof_suivi')." de la classe de $classe.");
					//echo "Vous n'êtes pas autorisé à être ici.";
					//require ("../lib/footer.inc.php");
					die();
				}
			}
			else {
				$sql="SELECT DISTINCT id_classe FROM j_eleves_professeurs jep, classes c WHERE jep.id_classe=c.id AND jep.professeur='".$_SESSION['login']."';";
				$res_clas_pp=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_clas_pp)==1) {
					$id_classe=old_mysql_result($res_clas_pp, 0, "id_classe");
				}
			}
		}
		else {
			tentative_intrusion("1","Tentative d'accès par un prof aux moyennes des carnets de notes sans avoir les autorisations nécessaires.");
			header("Location: ../accueil.php?msg=Vous n'êtes pas autorisé à être ici.");
			//echo "Vous n'êtes pas autorisé à être ici.";
			//require ("../lib/footer.inc.php");
			die();
		}
	}
}


if (isset($id_classe)) {
	// On regarde si le type est correct :
	if (!is_numeric($id_classe)) {
		tentative_intrusion("2", "Changement de la valeur de id_classe pour un type non numérique.");
		echo "Erreur.";
/**
 * inclusion du pied de page
 */
		require ("../lib/footer.inc.php");
		die();
	}
	// On teste si le professeur a le droit d'accéder à cette classe
	if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
		$test = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')"));
		if ($test == "0") {
			tentative_intrusion("2", "Tentative d'accès par un prof à une classe dans laquelle il n'enseigne pas, sans en avoir l'autorisation.");
			echo "Vous ne pouvez pas accéder à cette classe car vous n'y êtes pas professeur !";
            /**
             * inclusion du pied de page
             */
			require ("../lib/footer.inc.php");
			die();
		}
	}
}

$javascript_specifique="prepa_conseil/colorisation_visu_toutes_notes";
//**************** EN-TETE *****************
$titre_page = "Visualisation des moyennes des carnets de notes";

/**
 * Entête de la page
 */
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>

<?php
if (isset($id_classe)) {
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>";

	$current_eleve_classe = sql_query1("SELECT classe FROM classes WHERE id='$id_classe'");

	//echo "<a href=\"index2.php\">Choisir une autre classe</a> | Classe : ".$current_eleve_classe." |</p>\n";

	// ===========================================
	// Ajout lien classe précédente / classe suivante
	if($_SESSION['statut']=='scolarite'){
		$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur'){
		//$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";

		if ( (getSettingValue("GepiAccesMoyennesProf") != "yes") AND
		(getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes") AND
		(getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes")
		) {
			$sql="SELECT DISTINCT c.id, c.classe FROM j_eleves_professeurs jep, classes c WHERE jep.id_classe=c.id AND jep.professeur='".$_SESSION['login']."';";
		}
		else {
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
		}

	}
	elseif($_SESSION['statut']=='cpe'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
	}
	$chaine_options_classes="";

	$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_class_tmp)>0){
		$id_class_prec=0;
		$id_class_suiv=0;
		$temoin_tmp=0;
		while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
			if($lig_class_tmp->id==$id_classe){
				$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
				$temoin_tmp=1;
				if($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
					$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
					$id_class_suiv=$lig_class_tmp->id;
				}
				else{
					$id_class_suiv=0;
				}
			}
			else {
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
			}
			if($temoin_tmp==0){
				$id_class_prec=$lig_class_tmp->id;
			}
		}
	}
	// =================================
	if(isset($id_class_prec)){
		if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec'>Classe précédente</a>";}
	}
	if($chaine_options_classes!="") {
		echo " | Classe : <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
		echo $chaine_options_classes;
		echo "</select>\n";
	}
	if(isset($id_class_suiv)){
		if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv'>Classe suivante</a>";}
	}
	//fin ajout lien classe précédente / classe suivante
	// ===========================================
	//echo " | Classe : ".$current_eleve_classe."</p>\n";
	echo "</p>\n";
	echo "</form>\n";

	if(!isset($_SESSION['vtn_pref_num_periode'])) {
		$sql="SELECT * FROM preferences WHERE login='".$_SESSION['login']."' AND name LIKE 'vtn_pref_%';";
		$get_pref=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($get_pref)>0) {
			while($lig_pref=mysqli_fetch_object($get_pref)) {
				$_SESSION[$lig_pref->name]=$lig_pref->value;
			}
		}
	}

	echo "<form target=\"_blank\" name=\"visu_toutes_notes\" method=\"post\" action=\"visu_toutes_notes2.php\">\n";
	echo add_token_field();
	echo "<table border=\"1\" cellspacing=\"1\" cellpadding=\"10\" summary='Choix de la période'><tr>";
	echo "<td valign=\"top\"><b>Choisissez&nbsp;la&nbsp;période&nbsp;:&nbsp;</b><br />\n";
    /**
     * Gestion des périodes
     */
	include "../lib/periodes.inc.php";
	$i="1";
	while ($i < $nb_periode) {
		echo "<br />\n<input type=\"radio\" name=\"num_periode\" id='num_periode_$i' value=\"$i\" ";
		if(isset($_SESSION['vtn_pref_num_periode'])) {
			if($_SESSION['vtn_pref_num_periode']==$i) {echo "checked ";}
		}
		elseif ($i == 1) {echo "checked ";}
		echo "/>&nbsp;";
		echo "<label for='num_periode_$i' style='cursor:pointer;'>\n";
		echo ucfirst($nom_periode[$i]);
		echo "</label>\n";
		$i++;
	}
	echo "<br />\n<input type=\"radio\" name=\"num_periode\" id='num_periode_annee' value=\"annee\" ";
	if((isset($_SESSION['vtn_pref_num_periode']))&&($_SESSION['vtn_pref_num_periode']=='annee')) {echo "checked ";}
	echo "/>&nbsp;";
	echo "<label for='num_periode_annee' style='cursor:pointer;'>\n";
	echo "Année entière";
	echo "</label>\n";
	echo "</td>\n";

	echo "<td valign=\"top\">\n";
    echo "<b>Paramètres d'affichage</b><br />\n";
	echo "<input type=\"hidden\" name=\"id_classe\" value=\"".$id_classe."\" />";

	echo "<table border='0' width='100%' summary='Paramètres'>\n";
	echo "<tr>\n";
	echo "<td>\n";

		echo "<table border='0' summary='Paramètres'>\n";
		echo "<tr>\n";
		echo "<td>Largeur en pixel du tableau : </td>\n";
		echo "<td><input type=text name=larg_tab size=3 ";
		if(isset($_SESSION['vtn_pref_larg_tab'])) {
			echo "value=\"".$_SESSION['vtn_pref_larg_tab']."\"";
		}
		else {
			echo "value=\"680\"";
		}
		echo " /></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>Bords en pixel du tableau : </td>\n";
		echo "<td><input type=text name=bord size=3 ";
		if(isset($_SESSION['vtn_pref_bord'])) {
			echo "value=\"".$_SESSION['vtn_pref_bord']."\"";
		}
		else {
			echo "value=\"1\"";
		}
		echo " /></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>\n";
		echo "<label for='couleur_alterne' style='cursor:pointer;'>\n";
		echo "Couleurs de fond des lignes alternées : \n";
		echo "</label>\n";
		echo "</td>\n";
		echo "<td><input type=\"checkbox\" name=\"couleur_alterne\" id=\"couleur_alterne\" value='y' ";
		if(isset($_SESSION['vtn_pref_couleur_alterne'])) {
			if($_SESSION['vtn_pref_couleur_alterne']=='y') {
				echo "checked";
			}
		}
		else {
			echo "checked";
		}
		echo " /></td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "<td>\n";

		echo "<table border='0' summary='Champs'>\n";
		echo "<tr>\n";
		echo "<td><input type=\"checkbox\" name=\"aff_abs\" id=\"aff_abs\" value='y' ";
		if(isset($_SESSION['vtn_pref_aff_abs'])) {
			if($_SESSION['vtn_pref_aff_abs']=='y') {
				echo "checked";
			}
		}
		else {
			echo "checked";
		}
		echo " /></td>\n";
		echo "<td>\n";
		echo "<label for='aff_abs' style='cursor:pointer;'>\n";
		echo "Afficher les absences";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td><input type=\"checkbox\" name=\"aff_reg\" id=\"aff_reg\" value='y' ";
		if(isset($_SESSION['vtn_pref_aff_reg'])) {
			if($_SESSION['vtn_pref_aff_reg']=='y') {
				echo "checked ";
			}
		}
		else {
			echo "checked ";
		}
		echo "/></td>\n";
		echo "<td>\n";
		echo "<label for='aff_reg' style='cursor:pointer;'>\n";
		echo "Afficher le régime\n";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td><input type=\"checkbox\" name=\"aff_doub\" id=\"aff_doub\" value='y' ";
		if(isset($_SESSION['vtn_pref_aff_doub'])) {
			if($_SESSION['vtn_pref_aff_doub']=='y') {
				echo "checked";
			}
		}
		else {
			echo "checked";
		}
		echo " /></td>\n";
		echo "<td>\n";
		echo "<label for='aff_doub' style='cursor:pointer;'>\n";
		echo "Afficher la mention doublant\n";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";

		$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
		// On teste la présence d'au moins un coeff pour afficher la colonne des coef
		$test_coef = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));

		if (($affiche_rang == 'y') and ($test_coef != 0)) {
			echo "<tr>\n";
			echo "<td><input type=\"checkbox\" name=\"aff_rang\" id=\"aff_rang\" value='y' ";
			if(isset($_SESSION['vtn_pref_aff_rang'])) {
				if($_SESSION['vtn_pref_aff_rang']=='y') {
					echo "checked";
				}
			}
			else {
				echo "checked";
			}
			echo " /></td>\n";
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


	echo "<br />\n<center><input type=\"submit\" name=\"ok\" value=\"Valider\" /></center>\n";
	echo "<br />\n<span class='small'>Remarque : le tableau des notes s'affiche sans en-tête et dans une nouvelle page. Pour revenir à cet écran, il vous suffit de fermer la fenêtre du tableau des notes.</span>\n";
	echo "</td></tr>\n</table>\n";

	//============================================
	// Colorisation des résultats
	echo "<input type='checkbox' id='vtn_coloriser_resultats' name='vtn_coloriser_resultats' value='y' onchange=\"display_div_coloriser()\" ";
	if(isset($_SESSION['vtn_pref_coloriser_resultats'])) {
		if($_SESSION['vtn_pref_coloriser_resultats']=='y') {
			echo "checked";
		}
	}
	else {
		echo "checked";
	}
	echo "/><label for='vtn_coloriser_resultats'> Coloriser les résultats.</label><br />\n";
	

	// Tableau des couleurs HTML:
	$chaine_couleurs='"aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen"';

	$tabcouleur=Array("aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen");
	
	echo "<div id='div_coloriser'>\n";
	echo "<table id='table_couleur' class='boireaus' summary='Coloriser les résultats'>\n";
	echo "<thead>\n";
		echo "<tr>\n";
		echo "<th><a href='#colorisation_resultats' onclick='add_tr_couleur();return false;'>Borne<br />supérieure</a></th>\n";
		echo "<th>Couleur texte</th>\n";
		echo "<th>Couleur cellule</th>\n";
		echo "<th>Supprimer</th>\n";
		echo "</tr>\n";
	echo "</thead>\n";
	echo "<tbody id='table_body_couleur'>\n";

	$vtn_borne_couleur=array();
	$sql="SELECT * FROM preferences WHERE login='".$_SESSION['login']."' AND name LIKE 'vtn_%' ORDER BY name;";
	$res_pref=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_pref)>0) {
		while($lig_pref=mysqli_fetch_object($res_pref)) {
			if(mb_substr($lig_pref->name,0,17)=='vtn_couleur_texte') {
				$vtn_couleur_texte[]=$lig_pref->value;
			}
			elseif(mb_substr($lig_pref->name,0,19)=='vtn_couleur_cellule') {
				$vtn_couleur_cellule[]=$lig_pref->value;
			}
			elseif(mb_substr($lig_pref->name,0,17)=='vtn_borne_couleur') {
				$vtn_borne_couleur[]=$lig_pref->value;
			}
		}

		/**
         *
         * @global type $cpt_couleur 
         * @global array $tabcouleur
         * @global type $vtn_borne_couleur
         * @global type $vtn_couleur_texte
         * @global type $vtn_couleur_cellule 
         */
		function add_tr_couleur() {
			global $cpt_couleur, $tabcouleur, $vtn_borne_couleur, $vtn_couleur_texte, $vtn_couleur_cellule;

			$cpt_tmp=$cpt_couleur+1;

			$alt=pow((-1),$cpt_couleur);
			echo "<tr id='tr_couleur_$cpt_tmp' class='lig$alt'>\n";
			echo "<td>\n";
			echo "<input size='2' value='".$vtn_borne_couleur[$cpt_couleur]."' id='vtn_borne_couleur_$cpt_tmp' name='vtn_borne_couleur[]' type='text'>\n";
			echo "</td>\n";

			echo "<td>\n";
			echo "<select id='vtn_couleur_texte_$cpt_tmp' name='vtn_couleur_texte[]'>\n";
			echo "<option value=''>---</option>\n";
			for($i=0;$i<count($tabcouleur);$i++) {
				echo "<option style='background-color: $tabcouleur[$i];' value='$tabcouleur[$i]'";
				if($tabcouleur[$i]==$vtn_couleur_texte[$cpt_couleur]) {echo " selected='true'";}
				echo ">$tabcouleur[$i]</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";

			echo "<td>\n";
			echo "<select id='vtn_couleur_cellule_$cpt_tmp' name='vtn_couleur_cellule[]'>\n";
			echo "<option value=''>---</option>\n";
			for($i=0;$i<count($tabcouleur);$i++) {
				echo "<option style='background-color: $tabcouleur[$i];' value='$tabcouleur[$i]'";
				if($tabcouleur[$i]==$vtn_couleur_cellule[$cpt_couleur]) {echo " selected='true'";}
				echo ">$tabcouleur[$i]</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";

			echo "<td>\n";
			echo "<a href='#colorisation_resultats' onclick='suppr_ligne_couleur($cpt_tmp);return false;'><img src='../images/delete16.png' height='16' width='16' alt='Supprimer la ligne' /></a>\n";
			echo "</td>\n";

			echo "</tr>\n";
		}

		for($cpt_couleur=0;$cpt_couleur<count($vtn_borne_couleur);$cpt_couleur++) {add_tr_couleur();}
		$cpt_couleur++;

		echo "</tbody>\n";
		echo "</table>\n";
		echo "<a name='colorisation_resultats'></a>\n";
	
		echo "<script type='text/javascript'>
		// Couleurs prises en compte dans colorisation_visu_toutes_notes.js
		var tab_couleur=new Array($chaine_couleurs);

		var cpt_couleur=$cpt_couleur;

		//retouches_tab_couleur();
\n";

	}
	else {
		echo "</tbody>\n";
		echo "</table>\n";
		echo "<a name='colorisation_resultats'></a>\n";
	
		echo "<script type='text/javascript'>
		// Couleurs prises en compte dans colorisation_visu_toutes_notes.js
		var tab_couleur=new Array($chaine_couleurs);\n";

		echo "	// Pour démarrer avec trois lignes:
	add_tr_couleur();
	add_tr_couleur();
	add_tr_couleur();
	vtn_couleurs_par_defaut();\n";
	}
	echo "</script>\n";
//}

	// 20140331
	echo "<br /><p><strong>Paramètres de l'export PDF&nbsp;:</strong><br /><input type='checkbox' id='forcer_hauteur_ligne_pdf' name='forcer_hauteur_ligne_pdf' value='y' ";
	if(getPref($_SESSION["login"], "visu_toutes_notes_forcer_h_cell_pdf", "n")=="y") {
		echo "checked ";
	}
	echo "/><label for='forcer_hauteur_ligne_pdf'> Forcer la hauteur des lignes du tableau PDF à </label><input type='text' name='visu_toutes_notes_h_cell_pdf' size='2' value='".getPref($_SESSION["login"], "visu_toutes_notes_h_cell_pdf", 10)."' onkeydown=\"clavier_2(this.id,event,1,20);\" autocomplete=\"off\" /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<em>sinon Gepi calcule au mieux la hauteur de ligne</em>)</p>\n";

	echo "<p><br /></p>\n";
	echo "</div>\n";

	echo "<script type='text/javascript'>
function display_div_coloriser() {
if(document.getElementById('vtn_coloriser_resultats').checked==true) {
document.getElementById('div_coloriser').style.display='';
}
else {
document.getElementById('div_coloriser').style.display='none';
}
}
display_div_coloriser();
</script>\n";



	echo "</form>\n";
} else {
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>";

	echo "</p>\n";
	echo "<p><b>Visualiser les moyennes des carnets de notes par classe :</b><br />\n";

	if($_SESSION['statut'] == 'scolarite'){
		$appel_donnees = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	}
	elseif($_SESSION['statut'] == 'professeur' and getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes"){
		$appel_donnees = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe");
	}
	elseif($_SESSION['statut'] == 'professeur' and getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes") {
		$appel_donnees = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c  ORDER BY c.classe");
	}
	elseif($_SESSION['statut'] == 'cpe'){
		$appel_donnees = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	}

	$lignes = mysqli_num_rows($appel_donnees);

	if($lignes==0){
		echo "<p>Aucune classe ne vous est attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
	}
	else{
		$i = 0;
		unset($tab_lien);
		unset($tab_txt);
		while ($i < $lignes){
			$tab_lien[$i] = $_SERVER['PHP_SELF']."?id_classe=".old_mysql_result($appel_donnees, $i, "id");
			$tab_txt[$i] = old_mysql_result($appel_donnees, $i, "classe");
			$i++;

		}
		tab_liste($tab_txt,$tab_lien,3);
	}
}
echo "<p><i>Remarque:</i> Les moyennes visualisées ici sont des photos à un instant t de ce qui a été saisi par les professeurs.<br />\n";
echo "Cela ne correspond pas nécessairement à ce qui apparaitra sur le bulletin après saisie d'autres résultats et ajustements éventuels des coefficients.</p>\n";
if ($_SESSION['statut'] == "professeur"
	AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes"
	AND getSettingValue("GepiAccesMoyennesProfToutesTousEleves") != "yes") {
		echo "<p>Si vous n'enseignez pas à des classes entières, seuls les élèves auxquels vous enseignez apparaîtront dans la liste, et les moyennes calculés ne prendront en compte que les élèves affichés.</p>";
	}

/**
 * inclusion du pied de page
 */
require ("../lib/footer.inc.php");
?>
