<?php
/*
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
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

$javascript_specifique="prepa_conseil/colorisation_visu_toutes_notes";

//**************** EN-TETE *****************
$titre_page = "Visualisation des notes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();
?>
<!--p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a-->
<?php
if (isset($id_classe)) {

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>";

	$current_eleve_classe = sql_query1("SELECT classe FROM classes WHERE id='$id_classe'");
	//echo " | <a href=\"index2.php\">Choisir une autre classe</a>";


	// ===========================================
	// Ajout lien classe précédente / classe suivante
	if($_SESSION['statut']=='scolarite'){
		$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
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

	$res_class_tmp=mysql_query($sql);
	if(mysql_num_rows($res_class_tmp)>0){
		$id_class_prec=0;
		$id_class_suiv=0;
		$temoin_tmp=0;
		while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
			if($lig_class_tmp->id==$id_classe){
				$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
				$temoin_tmp=1;
				if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
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
		$get_pref=mysql_query($sql);
		if(mysql_num_rows($get_pref)>0) {
			while($lig_pref=mysql_fetch_object($get_pref)) {
				$_SESSION[$lig_pref->name]=$lig_pref->value;
			}
		}
	}

	echo "<form target=\"_blank\" name=\"visu_toutes_notes\" method=\"post\" action=\"visu_toutes_notes.php\">\n";
	echo add_token_field();
	echo "<table border=\"1\" cellspacing=\"1\" cellpadding=\"10\" summary=\"Choix de la période\"><tr>";
	echo "<td valign=\"top\"><strong>Choisissez&nbsp;la&nbsp;période&nbsp;:&nbsp;</strong><br />\n";
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

	echo "<p><input type='checkbox' name='avec_moy_gen_periodes_precedentes' id='avec_moy_gen_periodes_precedentes' value='y' ";
	if((isset($_SESSION['vtn_pref_avec_moy_gen_periodes_precedentes']))&&($_SESSION['vtn_pref_avec_moy_gen_periodes_precedentes']=='y')) {echo "checked ";}
	echo "/><label for='avec_moy_gen_periodes_precedentes'> Avec les moyennes de périodes précédentes</label></p>\n";

	echo "</td>\n";

	echo "<td valign=\"top\">\n";
	echo "<strong>Paramètres d'affichage</strong><br />\n";
	echo "<input type=\"hidden\" name=\"id_classe\" value=\"".$id_classe."\" />";

	echo "<table border='0' width='100%' summary=\"Paramètres du tableau\">\n";
	echo "<tr>\n";
	echo "<td>\n";

		echo "<table border='0' summary=\"Paramètres\">\n";
		echo "<tr>\n";
		echo "<td>Largeur en pixel du tableau : </td>\n";
		echo "<td><input type='text' name='larg_tab' id='larg_tab' size='3' ";
		if(isset($_SESSION['vtn_pref_larg_tab'])) {
			echo "value=\"".$_SESSION['vtn_pref_larg_tab']."\"";
		}
		else {
			echo "value=\"680\"";
		}
		echo "onkeydown=\"clavier_2(this.id,event,0,2000);\" autocomplete=\"off\" ";
		echo " /></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>Bords en pixel du tableau : </td>\n";
		echo "<td><input type='text' name='bord' id='bord' size='3' ";
		if(isset($_SESSION['vtn_pref_bord'])) {
			echo "value=\"".$_SESSION['vtn_pref_bord']."\"";
		}
		else {
			echo "value=\"1\"";
		}
		echo "onkeydown=\"clavier_2(this.id,event,0,10);\" autocomplete=\"off\" ";
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

		echo "<tr>\n";
		echo "<td>\n";
		echo "<label for='vtn_pref_marges' style='cursor:pointer;'>\n";
		echo "Marge ajoutée : \n";
		echo "</label>\n";
		echo "</td>\n";
		$vtn_pref_marges=getPref($_SESSION['login'],'vtn_pref_marges','');
		echo "<td><input type=\"text\" size=\"2\" name=\"vtn_pref_marges\" id=\"vtn_pref_marges\" ";
		echo "value='";
		if(isset($_SESSION['vtn_pref_marges'])) {
			$vtn_pref_marges=preg_replace('/[^0-9]/','',$_SESSION['vtn_pref_marges']);
			// Pour permettre de ne pas inserer de margin et memoriser ce choix, on accepte le champ vide:
			//if($vtn_pref_marges!='') {
				echo $vtn_pref_marges;
			/*
			}
			else {
				echo "0";
			}
			*/
		}
		elseif($vtn_pref_marges!='') {
			echo $vtn_pref_marges;
		}
		// On n'impose pas de forcer le margin
		/*
		else {
			echo "0";
		}
		*/
		echo "' ";
		echo "onkeydown=\"clavier_2(this.id,event,0,100);\" autocomplete=\"off\" ";
		echo " />px</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "<td>\n";

		echo "<table border='0' summary=\"Affichages supplémentaires\">\n";
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

		$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."';");
		// On teste la présence d'au moins un coeff pour afficher la colonne des coef
		$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));

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
		echo "<td valign='top'><input type=\"checkbox\" name=\"aff_date_naiss\" id=\"aff_date_naiss\" value='y' ";
		if(isset($_SESSION['vtn_pref_aff_date_naiss'])) {
			if($_SESSION['vtn_pref_aff_date_naiss']=='y') {
				echo "checked";
			}
		}
		else {
			echo "checked";
		}
		echo " /></td>\n";
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
	echo "<br />\nLargeur en pixel du tableau : <input type=text name=larg_tab size=3 value=\"680\" />";
	echo "<br />\nBords en pixel du tableau : <input type=text name=bord size=3 value=\"1\" />";
	echo "<br />\nCouleurs de fond des lignes alternées : <input type=\"checkbox\" name=\"couleur_alterne\" checked />";
	echo "<br /><br /><table cellpadding=\"3\"><tr><td>\n<input type=\"checkbox\" name=\"aff_abs\" checked />Afficher les absences</td>
	<td><input type=\"checkbox\" name=\"aff_reg\" checked /> Afficher le régime</td>
	<td><input type=\"checkbox\" name=\"aff_doub\" checked />Afficher la mention doublant</td>";
	$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
	// On teste la présence d'au moins un coeff pour afficher la colonne des coef
	$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));


	if (($affiche_rang == 'y') and ($test_coef != 0)) {
	echo "<td><input type=\"checkbox\" name=\"aff_rang\" checked />Afficher le rang des élèves</td>";
	}
	echo "</tr></table>";
*/
	echo "<br />\n<center><input type=\"submit\" name=\"ok\" value=\"Valider\" /></center>";
	echo "<br />\n<span class='small'>Remarque : le tableau des notes s'affiche sans en-tête et dans une nouvelle page. Pour revenir à cet écran, il vous suffit de fermer la fenêtre du tableau des notes.</span>";
	if ($_SESSION['statut'] == "professeur"
	AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes"
	AND getSettingValue("GepiAccesMoyennesProfToutesTousEleves") != "yes") {
		echo "<br />\n<span class='small'>Si vous n'enseignez pas à des classes entières, seuls les élèves auxquels vous enseignez apparaîtront dans la liste, et les moyennes calculés ne prendront en compte que les élèves affichés.</span>";
	}
	echo "</td></tr>\n</table>\n";

	//$sql="SELECT DISTINCT jgc.id_groupe, g.name, g.description, jgc.coef FROM groupes g, j_groupes_classes jgc WHERE id_classe='$id_classe' AND g.id=jgc.id_groupe ORDER BY g.name;";
	$sql="SELECT DISTINCT jgc.id_groupe, g.name, g.description, jgc.coef, jgc.mode_moy FROM groupes g, j_groupes_classes jgc WHERE id_classe='$id_classe' AND g.id=jgc.id_groupe ORDER BY g.name;";
	//echo "$sql<br />";
	$res_coef_grp=mysql_query($sql);

	echo "<input type='checkbox' id='utiliser_coef_perso' name='utiliser_coef_perso' value='y' onchange=\"display_div_coef_perso()\" /><label for='utiliser_coef_perso'> Utiliser des coefficients personnalisés.</label><br />\n";

	echo "<div id='div_coef_perso'>\n";
	echo "<table class='boireaus' summary='Coefficients personnalisés'>\n";
	echo "<tr>\n";
	echo "<th>Identifiant</th>\n";
	echo "<th>Nom de l'enseignement</th>\n";
	echo "<th>Description de l'enseignement</th>\n";
	echo "<th>Coefficient</th>\n";
	echo "<th>Standard</th>\n";
	echo "<th>Note&gt;10</th>\n";
	echo "<th>Bonus</th>\n";
	echo "</tr>\n";

	$alt=1;
	$num_id=1;
	while ($lig_cg=mysql_fetch_object($res_coef_grp)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td>$lig_cg->id_groupe</td>\n";
		echo "<td>".htmlspecialchars($lig_cg->name)."</td>\n";
		echo "<td>".htmlspecialchars($lig_cg->description)."</td>\n";
		echo "<td>\n";
		$val_coef_perso=$lig_cg->coef;
		$nom_matiere='';

		$sql="SELECT id_matiere FROM j_groupes_matieres WHERE id_groupe='$lig_cg->id_groupe';";
		$res_nom_matiere=mysql_query($sql);
		if(mysql_num_rows($res_nom_matiere)>0) {
			$lig_nom_matiere=mysql_fetch_object($res_nom_matiere);
			$nom_matiere=$lig_nom_matiere->id_matiere;
			//if(isset($_SESSION['coef_perso_'.$nom_matiere])) {
			if(isset($_SESSION['coef_perso_'.$lig_cg->id_groupe])) {
				//$val_coef_perso=$_SESSION['coef_perso_'.$nom_matiere];
				$val_coef_perso=$_SESSION['coef_perso_'.$lig_cg->id_groupe];
			}
		}
		echo "<input type='text' id=\"n".$num_id."\" onKeyDown=\"clavier(this.id,event);\" onfocus=\"javascript:this.select()\" name='coef_perso[$lig_cg->id_groupe]' value='\n";
		echo $val_coef_perso;
		echo "' size='3' autocomplete='off' />\n";
		echo "</td>\n";

		echo "<td>\n";
		echo "<input type='radio' name='mode_moy_perso[$lig_cg->id_groupe]' value='-' ";
		//if(($nom_matiere!='')&&(isset($_SESSION['mode_moy_'.$nom_matiere]))&&($_SESSION['mode_moy_'.$nom_matiere]=='-')) {
		if(($nom_matiere!='')&&(isset($_SESSION['mode_moy_'.$nom_matiere]))&&($_SESSION['mode_moy_'.$lig_cg->id_groupe]=='-')) {
			echo "checked ";
		}
		elseif($lig_cg->mode_moy=='-') {echo "checked ";}
		echo "/>\n";
		echo "</td>\n";


		echo "<td>\n";
		echo "<input type='radio' name='mode_moy_perso[$lig_cg->id_groupe]' value='sup10' ";
		//if(($nom_matiere!='')&&(isset($_SESSION['mode_moy_'.$nom_matiere]))&&($_SESSION['mode_moy_'.$nom_matiere]=='sup10')) {
		if(($nom_matiere!='')&&(isset($_SESSION['mode_moy_'.$nom_matiere]))&&($_SESSION['mode_moy_'.$lig_cg->id_groupe]=='sup10')) {
			echo "checked ";
		}
		elseif($lig_cg->mode_moy=='sup10') {echo "checked ";}
		echo "/>\n";
		echo "</td>\n";


		echo "<td>\n";
		echo "<input type='radio' name='mode_moy_perso[$lig_cg->id_groupe]' value='bonus' ";
		//if(($nom_matiere!='')&&(isset($_SESSION['mode_moy_'.$nom_matiere]))&&($_SESSION['mode_moy_'.$nom_matiere]=='bonus')) {
		if(($nom_matiere!='')&&(isset($_SESSION['mode_moy_'.$nom_matiere]))&&($_SESSION['mode_moy_'.$lig_cg->id_groupe]=='bonus')) {
			echo "checked ";
		}
		elseif($lig_cg->mode_moy=='bonus') {echo "checked ";}
		echo "/>\n";
		echo "</td>\n";

		echo "</tr>\n";
		$num_id++;
	}
	echo "</table>\n";

	echo "<p><i>Remarque:</i> Si des coefficients spécifiques ont été mis en place pour certains élèves (<i>voir en compte administrateur Gestion des bases/Gestion des classes/Enseignements/&lt;ENSEIGNEMENT&gt;/Eleves inscrits</i>), ils ne seront pas écrasés par les valeurs saisies ici.</p>\n";
	echo "</div>\n";

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
	//$tabcouleur=array("red","darkcyan","gold","green","blue");

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
	$res_pref=mysql_query($sql);
	if(mysql_num_rows($res_pref)>0) {
		while($lig_pref=mysql_fetch_object($res_pref)) {
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

		//$cpt_couleur=0;
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

/*
	$tab_couleur_texte_defaut=array('red', 'orange', 'green');
	$tab_couleur_cellule_defaut=array('', '', '');

	$j=0;
	$alt=1;
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td><input type='checkbox' name='utiliser_couleur[$j]' value='y' checked /></td>\n";
	echo "<td>Jusqu'à <input type='text' name='borne_couleur[$j]' value='8' size='2' /></td>\n";
	echo "<td>";
	echo "<select name=\"couleur_texte[]\">\n";
	for($i=0;$i<count($tabcouleur);$i++){
		if($tabcouleur[$i]=="$tab_couleur_texte_defaut[$j]"){
			$checked=" selected=\"true\"";
		}
		else{
			$checked="";
		}
		echo "<option style=\"background-color: $tabcouleur[$i]\" value=\"$tabcouleur[$i]\"$checked>$tabcouleur[$i]</option>\n";
	}
	echo "</select>\n";
	echo "</td>\n";

	echo "<td>";
	echo "<select name=\"couleur_cellule[]\">\n";
	echo "<option value=''>---</option>\n";
	for($i=0;$i<count($tabcouleur);$i++){
		if($tabcouleur[$i]=="$tab_couleur_cellule_defaut[$j]"){
			$checked=" selected=\"true\"";
		}
		else{
			$checked="";
		}
		echo "<option style=\"background-color: $tabcouleur[$i]\" value=\"$tabcouleur[$i]\"$checked>$tabcouleur[$i]</option>\n";
	}
	echo "</select>\n";
	echo "</td>\n";
	echo "</tr>\n";

	$j=1;
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td><input type='checkbox' name='utiliser_couleur[$j]' value='y' checked /></td>\n";
	echo "<td>Jusqu'à <input type='text' name='borne_couleur[$j]' value='12' size='2' /></td>\n";
	echo "<td>";
	echo "<select name=\"couleur_texte[]\">\n";
	for($i=0;$i<count($tabcouleur);$i++){
		if($tabcouleur[$i]=="$tab_couleur_texte_defaut[$j]"){
			$checked=" selected=\"true\"";
		}
		else{
			$checked="";
		}
		echo "<option style=\"background-color: $tabcouleur[$i]\" value=\"$tabcouleur[$i]\"$checked>$tabcouleur[$i]</option>\n";
	}
	echo "</select>\n";
	echo "</td>\n";

	echo "<td>";
	echo "<select name=\"couleur_cellule[]\">\n";
	echo "<option value=''>---</option>\n";
	for($i=0;$i<count($tabcouleur);$i++){
		if($tabcouleur[$i]=="$tab_couleur_cellule_defaut[$j]"){
			$checked=" selected=\"true\"";
		}
		else{
			$checked="";
		}
		echo "<option style=\"background-color: $tabcouleur[$i]\" value=\"$tabcouleur[$i]\"$checked>$tabcouleur[$i]</option>\n";
	}
	echo "</select>\n";
	echo "</td>\n";
	echo "</tr>\n";


	$j=2;
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td><input type='checkbox' name='utiliser_couleur[$j]' value='y' checked /></td>\n";
	echo "<td>Jusqu'à <input type='text' name='borne_couleur[$j]' value='20' size='2' /></td>\n";
	echo "<td>";
	echo "<select name=\"couleur_texte[]\">\n";
	for($i=0;$i<count($tabcouleur);$i++){
		if($tabcouleur[$i]=="$tab_couleur_texte_defaut[$j]"){
			$checked=" selected=\"true\"";
		}
		else{
			$checked="";
		}
		echo "<option style=\"background-color: $tabcouleur[$i]\" value=\"$tabcouleur[$i]\"$checked>$tabcouleur[$i]</option>\n";
	}
	echo "</select>\n";
	echo "</td>\n";

	echo "<td>";
	echo "<select name=\"couleur_cellule[]\">\n";
	echo "<option value=''>---</option>\n";
	for($i=0;$i<count($tabcouleur);$i++){
		if($tabcouleur[$i]=="$tab_couleur_cellule_defaut[$j]"){
			$checked=" selected=\"true\"";
		}
		else{
			$checked="";
		}
		echo "<option style=\"background-color: $tabcouleur[$i]\" value=\"$tabcouleur[$i]\"$checked>$tabcouleur[$i]</option>\n";
	}
	echo "</select>\n";
	echo "</td>\n";
	echo "</tr>\n";


	echo "</table>\n";
*/
	//echo "<p><i>Remarque:</i> ...</p>\n";
	echo "<p><br /></p>\n";
	echo "</div>\n";

	echo "<script type='text/javascript'>
function display_div_coef_perso() {
//alert('grrrr');
if(document.getElementById('utiliser_coef_perso').checked==true) {
document.getElementById('div_coef_perso').style.display='';
}
else {
document.getElementById('div_coef_perso').style.display='none';
}
}
display_div_coef_perso();

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

	//echo "</p><strong>Visualiser les notes par classe :</strong><br />";
	echo "</p>\n";
	echo "<strong>Visualiser les moyennes par classe :</strong><br />";
	//$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	//$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	if($_SESSION['statut']=='scolarite'){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	}
	elseif($_SESSION['statut'] == 'professeur' and getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes"){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe");
	}
	elseif($_SESSION['statut'] == 'professeur' and getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes") {
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c  ORDER BY c.classe");
	}
	elseif($_SESSION['statut']=='cpe'){
		$appel_donnees=mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe");
	}
	$lignes = mysql_num_rows($appel_donnees);

	if($lignes==0){
		echo "<p>Aucune classe ne vous est attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
	}
	else{
		$i = 0;
		$nb_class_par_colonne=round($lignes/3);
			//echo "<table width='100%' border='1'>\n";
			echo "<table width='100%' summary=\"Choix de la classe\">\n";
			echo "<tr valign='top' align='center'>\n";
			echo "<td align='left'>\n";
		while($i < $lignes){
		$id_classe = mysql_result($appel_donnees, $i, "id");
		$display_class = mysql_result($appel_donnees, $i, "classe");
		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td align='left'>\n";
		}
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>".ucfirst($display_class)."</a><br />\n";
		$i++;
		}
		echo "</table>\n";
	}
}
require("../lib/footer.inc.php");
?>
