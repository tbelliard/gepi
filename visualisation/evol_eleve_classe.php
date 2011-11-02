<?php
/*
* $Id: evol_eleve_classe.php 7120 2011-06-05 08:59:33Z crob $
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//**************** EN-TETE *****************
$titre_page = "Outil de visualisation | Evolution de l'élève et de la classe sur l'année";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$periode = isset($_POST['periode']) ? $_POST['periode'] : (isset($_GET['periode']) ? $_GET['periode'] : NULL);
$suiv = isset($_GET['suiv']) ? $_GET['suiv'] : 'no';
$prec = isset($_GET['prec']) ? $_GET['prec'] : 'no';
$v_eleve = isset($_POST['v_eleve']) ? $_POST['v_eleve'] : (isset($_GET['v_eleve']) ? $_GET['v_eleve'] : NULL);
include "../lib/periodes.inc.php";

if (!isset($id_classe)) {

	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/>Retour accueil</a> | <a href='index.php'>Autre outil de visualisation</a></p>\n";

	echo "<p>Sélectionnez la classe :<br />\n";
	//$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	//$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	if($_SESSION['statut']=='scolarite'){
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur'){
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
	}
	elseif($_SESSION['statut']=='cpe'){
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
	}

	if(((getSettingValue("GepiAccesReleveProfToutesClasses")=="yes")&&($_SESSION['statut']=='professeur'))||
		((getSettingValue("GepiAccesReleveScol")=='yes')&&($_SESSION['statut']=='scolarite'))||
		((getSettingValue("GepiAccesReleveCpe")=='yes')&&($_SESSION['statut']=='cpe'))) {
		$sql="SELECT DISTINCT c.* FROM classes c ORDER BY classe";
	}

	$call_data=mysql_query($sql);

	$nombre_lignes = mysql_num_rows($call_data);
	$i = 0;
	$nb_class_par_colonne=round($nombre_lignes/3);
		//echo "<table width='100%' border='1'>\n";
		echo "<table width='100%' summary='Choix de la classe'>\n";
		echo "<tr valign='top' align='center'>\n";
		echo "<td align='left'>\n";
	while ($i < $nombre_lignes){
		$id_classe = mysql_result($call_data, $i, "id");
		$classe = mysql_result($call_data, $i, "classe");
		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td align='left'>\n";
		}
		echo "<a href='evol_eleve_classe.php?id_classe=$id_classe#graph'>$classe</a><br />\n";
		$i++;
	}
	//echo "</p>";
	echo "</table>\n";
} else {
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/>Retour accueil</a> | <a href='index.php'>Autre outil de visualisation</a>\n";

	$k="1";
	while ($k < $nb_periode) {
	$datay1[$k] = array();
	$datay2[$k] = array();
	$k++;
	}
	$etiquette = array();
	$graph_title = "";

	$call_data = mysql_query("SELECT classe FROM classes WHERE id = $id_classe");
	$classe = mysql_result($call_data, 0, "classe");
	$call_eleve = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' AND e.login = c.login) order by e.nom");
	$nombreligne = mysql_num_rows($call_eleve);

	if (!$v_eleve) {$v_eleve = @mysql_result($call_eleve, 0, 'login');}

	if ($suiv == 'yes') {
		$i = "0" ;
		while ($i < $nombreligne) {
			if ($v_eleve == mysql_result($call_eleve, $i, 'login') and ($i < $nombreligne-1)) {$v_eleve = mysql_result($call_eleve, $i+1, 'login');$i = $nombreligne;}
		$i++;
		}
	}
	if ($prec == 'yes') {
		$i = "0" ;
		while ($i < $nombreligne) {
			if ($v_eleve == mysql_result($call_eleve, $i, 'login') and ($i > '0')) {$v_eleve = mysql_result($call_eleve, $i-1, 'login');$i = $nombreligne;}
		$i++;
		}
	}

	echo "<table summary='Choix'>\n";
	echo "<tr><td>\n";

	echo "<form action='".$_SERVER['PHP_SELF']."#graph' name='form1' method='post'>\n";

	echo "<p class='bold'>\n";

	//echo "<a href='evol_eleve_classe.php'>Choisir une autre classe</a> | \n";


	if($_SESSION['statut']=='scolarite'){
		//$sql="SELECT id,classe FROM classes ORDER BY classe";
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	if($_SESSION['statut']=='professeur'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc,j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgc.id_groupe=jgp.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
	}
	if($_SESSION['statut']=='cpe'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe=c.id ORDER BY c.classe";
	}
	if($_SESSION['statut']=='administrateur'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	/*
	if(($_SESSION['statut']=='scolarite')&&(getSettingValue("GepiAccesVisuToutesEquipScol") =="yes")){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	if(($_SESSION['statut']=='cpe')&&(getSettingValue("GepiAccesVisuToutesEquipCpe") =="yes")){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	if(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesVisuToutesEquipProf") =="yes")){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	*/
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

	if($id_class_prec!=0){
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec";
		echo "#graph'>Classe précédente</a> | ";
	}
	if($chaine_options_classes!="") {
		echo "<select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
		echo $chaine_options_classes;
		echo "</select> | \n";
	}
	if($id_class_suiv!=0){
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv";
		echo "#graph'>Classe suivante</a> | ";
	}

	echo "<a href='evol_eleve_classe.php?id_classe=$id_classe&amp;prec=yes&amp;v_eleve=$v_eleve'>Elève précédent</a> | \n";
	echo "<a href='evol_eleve_classe.php?id_classe=$id_classe&amp;suiv=yes&amp;v_eleve=$v_eleve'>Elève suivant</a>|\n";

	echo "</p>\n";

	echo "</form>\n";

	echo "</td>\n";
	echo "<td>\n";

	echo "<form enctype='multipart/form-data' action='evol_eleve_classe.php' method='post'>\n";
	echo "<select size='1' name='v_eleve' onchange='this.form.submit()'>\n";

	$i = "0" ;
	while ($i < $nombreligne) {
		$eleve = mysql_result($call_eleve, $i, 'login');
		$nom_el = mysql_result($call_eleve, $i, 'nom');
		$prenom_el = mysql_result($call_eleve, $i, 'prenom');
		echo "<option value='$eleve'";
		if ($v_eleve == $eleve) {echo " selected ";}
		echo ">$nom_el  $prenom_el</option>\n";
		$i++;
	}
	echo "</select>\n";
	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
	echo "</form>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	// On appelle les informations de l'utilisateur pour les afficher :
	$call_eleve_info = mysql_query("SELECT login,nom,prenom FROM eleves WHERE     login='$v_eleve'");
	$eleve_nom = mysql_result($call_eleve_info, "0", "nom");
	$eleve_prenom = mysql_result($call_eleve_info, "0", "prenom");
	$graph_title = $eleve_nom." ".$eleve_prenom.", ".$classe.", évolution sur l'année";
	$v_legend1 = $eleve_nom." ".$eleve_prenom ;
	$v_legend2 = "Moy. ".$classe ;
	echo "<p>$eleve_nom  $eleve_prenom, classe de $classe   |  Evolution sur l'année</p>\n";
	echo "<table class='boireaus' border='1' cellspacing='2' cellpadding='5' summary='Matières/Notes'>\n";
	echo "<tr><th width='100'><p>Matière</p></th>\n";
	$k = '1';
	while ($k < $nb_periode) {
		echo "<th width='100'><p>$nom_periode[$k]</p></th><th width='100'><p>classe</p></th>\n";
	$k++;
	}
	echo "</tr>\n";


	$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
	if ($affiche_categories == "y") {
		$affiche_categories = true;
	} else {
		$affiche_categories = false;
	}

	if ($affiche_categories) {
		// On utilise les valeurs spécifiées pour la classe en question
		$call_groupes = mysql_query("SELECT DISTINCT jgc.id_groupe ".
		"FROM j_eleves_groupes jeg, j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
		"WHERE ( " .
		"jeg.login = '" . $v_eleve ."' AND " .
		"jgc.id_groupe = jeg.id_groupe AND " .
		"jgc.categorie_id = jmcc.categorie_id AND " .
		"jgc.id_classe = '".$id_classe."' AND " .
		"jgm.id_groupe = jgc.id_groupe AND " .
		"m.matiere = jgm.id_matiere" .
		" AND jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n')".
		") " .
		"ORDER BY jmcc.priority,jgc.priorite,m.nom_complet");
	} else {
		$call_groupes = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef " .
		"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_eleves_groupes jeg " .
		"WHERE ( " .
		"jeg.login = '" . $v_eleve . "' AND " .
		"jgc.id_groupe = jeg.id_groupe AND " .
		"jgc.id_classe = '".$id_classe."' AND " .
		"jgm.id_groupe = jgc.id_groupe" .
		" AND jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n')".
		") " .
		"ORDER BY jgc.priorite,jgm.id_matiere");
	}


	$nombre_lignes = mysql_num_rows($call_groupes);
	$i = 0;
	$compteur = 0;
	$prev_cat_id = null;
	$alt=1;
	while ($i < $nombre_lignes) {
		$group_id = mysql_result($call_groupes, $i, "id_groupe");
		$current_group = get_group($group_id);
		if ($affiche_categories) {
		// On regarde si on change de catégorie de matière
			if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat_id) {
				$prev_cat_id = $current_group["classes"]["classes"][$id_classe]["categorie_id"];
				// On est dans une nouvelle catégorie
				// On récupère les infos nécessaires, et on affiche une ligne
				$cat_name = html_entity_decode_all_version(mysql_result(mysql_query("SELECT nom_complet FROM matieres_categories WHERE id = '" . $current_group["classes"]["classes"][$id_classe]["categorie_id"] . "'"), 0));
				// On détermine le nombre de colonnes pour le colspan
				$nb_total_cols = 1;
				$k = '1';
				while ($k < $nb_periode) {
					$nb_total_cols++;
					$nb_total_cols++;
					$k++;
				}
				// On a toutes les infos. On affiche !
				echo "<tr>\n";
				echo "<td colspan='" . $nb_total_cols . "'>";
				echo "<p style='padding: 5; margin:0; font-size: 15px;'>".$cat_name."</p></td>\n";
				echo "</tr>\n";
			}
		}

			$alt=$alt*(-1);
			echo "<tr class='lig$alt'><td><p>" . htmlentities($current_group["description"]) . "</p></td>\n";
			$k = '1';
			while ($k < $nb_periode) {
				$note_eleve_query=mysql_query("SELECT * FROM matieres_notes WHERE (login='$v_eleve' AND periode='$k' AND id_groupe='" . $current_group["id"] . "')");
				$eleve_matiere_statut = @mysql_result($note_eleve_query, 0, "statut");
				$note_eleve = @mysql_result($note_eleve_query, 0, "note");
				if ($eleve_matiere_statut != "") { $note_eleve = $eleve_matiere_statut;}
				if ($note_eleve == '') {$note_eleve = '-';}
				$moyenne_classe_query = mysql_query("SELECT round(avg(note),1) as moyenne FROM matieres_notes WHERE (periode='$k' AND id_groupe='" . $current_group["id"] . "' AND statut ='')");
				$moyenne_classe = mysql_result($moyenne_classe_query, 0, "moyenne");
				if ($moyenne_classe == '') {$moyenne_classe = '-';}
				echo "<td><p>$note_eleve";
				echo "</p></td><td><p>$moyenne_classe</p></td>\n";
				(preg_match("/^[0-9\.\,]{1,}$/", $moyenne_classe)) ? array_push($datay1[$k],"$moyenne_classe") : array_push($datay1[$k],"0");
				(preg_match("/^[0-9\.\,]{1,}$/", $note_eleve)) ? array_push($datay2[$k],"$note_eleve") : array_push($datay2[$k],"0");

				if ($k == '1') {
					//array_push($etiquette,$current_group["description"]);
					array_push($etiquette,rawurlencode($current_group["description"]));
				}
				$compteur++;
			$k++;
			}
		echo "</tr>\n";
		$compteur++;
	$i++;
	}
	echo "</table>\n";


	echo "<a name='graph'></a>\n";
	echo "<table summary='Choix'>\n";
	echo "<tr>\n";
	echo "<td>\n";

	echo "<form action='".$_SERVER['PHP_SELF']."#graph' name='form2' method='post'>\n";
	echo "<p class='bold'>Classe \n";

	if($id_class_prec!=0){
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec";
		echo "#graph'>préc.</a> | ";
	}
	if($chaine_options_classes!="") {
		echo "<select name='id_classe' onchange=\"document.forms['form2'].submit();\">\n";
		echo $chaine_options_classes;
		echo "</select> | \n";
	}
	if($id_class_suiv!=0){
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv";
		echo "#graph'>suiv.</a> | ";
	}

	?>
	<a href="evol_eleve_classe.php?id_classe=<?php echo $id_classe; ?>&amp;prec=yes&amp;v_eleve=<?php echo $v_eleve; ?>#graph">Elève précédent</a> |
	<a href="evol_eleve_classe.php?id_classe=<?php echo $id_classe; ?>&amp;suiv=yes&amp;v_eleve=<?php echo $v_eleve; ?>#graph">Elève suivant</a> |
	<?php
	echo "</p>\n";
	echo "</form>\n";
	?>
	</td>
<td><form enctype="multipart/form-data" action="evol_eleve_classe.php?temp=0#graph" method=post>
<select size='1' name='v_eleve' onchange="this.form.submit()">
	<?php
	$i = "0" ;
	while ($i < $nombreligne) {
		$eleve = mysql_result($call_eleve, $i, 'login');
		$nom_el = mysql_result($call_eleve, $i, 'nom');
		$prenom_el = mysql_result($call_eleve, $i, 'prenom');
		echo "<option value=$eleve";
		if ($v_eleve == $eleve) {
			echo " selected ";
			// On récupère des infos sur l'élève courant:
			$v_elenoet=mysql_result($call_eleve, $i, 'elenoet');
			$v_naissance=mysql_result($call_eleve, $i, 'naissance');
			$tmp_tab_naissance=explode("-",$v_naissance);
			$v_naissance=$tmp_tab_naissance[2]."/".$tmp_tab_naissance[1]."/".$tmp_tab_naissance[0];
			$v_sexe=mysql_result($call_eleve, $i, 'sexe');
			$v_eleve_nom_prenom="$nom_el  $prenom_el";
		}
		echo ">$nom_el  $prenom_el</option>\n";
		$i++;
	}
	?>
	</select>
	<input type='hidden' name='id_classe' value='<?php echo $id_classe; ?>' />
	</form></td>

	<?php

	echo "<td>\n";
	//echo $v_eleve;

	// ============================================
	// Création de l'infobulle:

	$titre=$v_eleve_nom_prenom;
	//$texte="<table border='0'>\n";
	$texte="<div align='center'>\n";
	//$texte.="<tr>\n";
	//if($v_elenoet!=""){
	if($v_elenoet){
	  $photo=nom_photo($v_elenoet);
	  if("$photo"!=""){
		//$texte.="<img src='../photos/eleves/".$photo."' width='150' alt=\"$v_eleve_nom_prenom\" />";
		$texte.="<img src='".$photo."' width='150' alt=\"$v_eleve_nom_prenom\" />";
		$texte.="<br />\n";
	  }
	}
	//$texte.="<td>\n";
	$texte.="Né";
	if($v_sexe=="F"){
		$texte.="e";
	}
	$texte.=" le $v_naissance\n";
	//$texte.="</td>\n";
	//$texte.="</tr>\n";
	//$texte.="</table>\n";
	$texte.="</div>\n";

	//echo creer_div_infobulle('info_popup_eleve',$titre,"",$texte,"",30,0,'y','y','n','n');
	$tabdiv_infobulle[]=creer_div_infobulle('info_popup_eleve',$titre,"",$texte,"",14,0,'y','y','n','n');

	// ============================================

	// Insertion du lien permettant l'affichage de l'infobulle:
	echo "<a href='#' onmouseover=\"afficher_div('info_popup_eleve','y',-100,20);\"";
	//echo " onmouseout=\"cacher_div('info_popup_eleve');\"";
	echo ">";
	echo "<img src='../images/icons/buddy.png' alt='Informations élève' />";
	echo "</a>";

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";


	$etiq = implode("|", $etiquette);
	$graph_title = urlencode($graph_title);
	$v_legend1 = urlencode($classe);
	$v_legend2 = urlencode($eleve_nom . " " . $eleve_prenom);
	/*
	echo "<a href='../".$ver_jpgraph."/view_jpgraph2.php?";
	$k = "1";
	while ($k < $nb_periode) {
	$temp1=implode("|", $datay1[$k]);
	$temp2=implode("|", $datay2[$k]);
	echo "temp1".$k."=".$temp1."&temp2".$k."=".$temp2."&";
	$k++;
	}
	echo "&v_legend1=".$v_legend1."&v_legend2=".$v_legend2."&etiquette=$etiq&titre=$graph_title&compteur=$compteur&nb_data=$nb_periode'>debug</a>";
	*/
//    echo "<img src='../".$ver_jpgraph."/view_jpgraph2.php?";

	echo "<img src='./draw_artichow2.php?";
	$k = "1";
	while ($k < $nb_periode) {
	$temp1=implode("|", $datay1[$k]);
	$temp2=implode("|", $datay2[$k]);
	echo "temp1".$k."=".$temp1."&amp;temp2".$k."=".$temp2."&amp;";
	$k++;
	}
	echo "&amp;v_legend1=".$v_legend1."&amp;v_legend2=".$v_legend2."&amp;etiquette=$etiq&amp;titre=$graph_title&amp;compteur=$compteur&amp;nb_data=$nb_periode' alt='Graphe de l évolution de l élève et de la classe sur l année' />\n";

	echo "<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />\n";
}

//===========================================================
echo "<p><em>NOTE&nbsp;:</em></p>\n";
require("../lib/textes.inc.php");
echo "<p style='margin-left: 3em;'>$explication_bulletin_ou_graphe_vide</p>\n";
//===========================================================

require("../lib/footer.inc.php");
?>