<?php
/*
* $Id$
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$datay1 = array();
$datay2 = array();
$etiquette = array();
$graph_title = "";
$v_legend1 = "";
$v_legend2 = "";


//**************** EN-TETE *****************
$titre_page = "Outil de visualisation | Eleve vis à vis d'un autre élève";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$id_classe2 = isset($_POST['id_classe2']) ? $_POST['id_classe2'] : (isset($_GET['id_classe2']) ? $_GET['id_classe2'] : NULL);
$periode = isset($_POST['periode']) ? $_POST['periode'] : (isset($_GET['periode']) ? $_GET['periode'] : NULL);
$suiv = isset($_GET['suiv']) ? $_GET['suiv'] : 'no';
$prec = isset($_GET['prec']) ? $_GET['prec'] : 'no';
$v_eleve1 = isset($_POST['v_eleve1']) ? $_POST['v_eleve1'] : (isset($_GET['v_eleve1']) ? $_GET['v_eleve1'] : NULL);
$v_eleve2 = isset($_POST['v_eleve2']) ? $_POST['v_eleve2'] : (isset($_GET['v_eleve2']) ? $_GET['v_eleve2'] : NULL);

include "../lib/periodes.inc.php";

?>
<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <a href='index.php'>Autre outil de visualisation</a>
<?php

if ((!isset($id_classe)) or (!isset($id_classe2))) {
	echo "</p><form enctype='multipart/form-data' action='eleve_eleve.php' method='post'>\n";
	echo "<p>Sélectionnez la classe du premier élève :</p>\n";
	echo "<select size='1' name='id_classe'>\n";
	//$call_classes = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	//$call_classes = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");

	if($_SESSION['statut']=='scolarite'){
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur'){
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
	}
	elseif($_SESSION['statut']=='cpe'){
		/*
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
		*/
		// Les cpe ont accès à tous les bulletins, donc aussi aux courbes
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}

	if(((getSettingValue("GepiAccesReleveProfToutesClasses")=="yes")&&($_SESSION['statut']=='professeur'))||
		((getSettingValue("GepiAccesReleveScol")=='yes')&&($_SESSION['statut']=='scolarite'))) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}
	/*
	if(((getSettingValue("GepiAccesReleveProfToutesClasses")=="yes")&&($_SESSION['statut']=='professeur'))||
		((getSettingValue("GepiAccesReleveScol")=='yes')&&($_SESSION['statut']=='scolarite'))||
		((getSettingValue("GepiAccesReleveCpeTousEleves")=='yes')&&($_SESSION['statut']=='cpe'))) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}
	elseif((getSettingValue("GepiAccesReleveCpe")=='yes')&&($_SESSION['statut']=='cpe')) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
	}
	*/

	$call_classes=mysql_query($sql);

	$nombreligne = mysql_num_rows($call_classes);
	$i = "0" ;
	while ($i < $nombreligne) {
		$ide_classe = mysql_result($call_classes, $i, "id");
		$nom_classe = mysql_result($call_classes, $i, "classe");
		echo "<option value='$ide_classe'>$nom_classe</option>\n";
	$i++;
	}
	echo "</select>\n";

	echo "<p>Sélectionnez la classe du deuxième élève :</p>\n";
	echo "<select size='1' name='id_classe2'>\n";
	$i = "0" ;
	while ($i < $nombreligne) {
		$ide_classe = mysql_result($call_classes, $i, "id");
		$nom_classe = mysql_result($call_classes, $i, "classe");
		echo "<option value='$ide_classe'>$nom_classe</option>\n";
	$i++;
	}
	echo "</select><br /><br /><input type='submit' value='Valider' /></form>\n";

} else {
	$call_classe = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");
	$classe = mysql_result($call_classe, "0", "classe");
	$call_classe = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe2'");
	$classe2 = mysql_result($call_classe, "0", "classe");

	if ((!isset($v_eleve1)) OR (!isset($v_eleve2))) {
		if (($v_eleve1) AND (!$v_eleve2)) { $msg="Vous devez entrer le nom d'un second élève...";}
		?>
		| <a href="eleve_eleve.php">Choix des classes</a></p>
		<form enctype="multipart/form-data" action="eleve_eleve.php#graph" method=post>
		<table><tr><td>
		<p>Classe : <?php echo $classe; ?><br />Veuillez sélectionner l'élève n°1:<br />
		<select size='1' name='v_eleve1'>
		<?php
		$call_eleve = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' and c.login=e.login) order by nom");
		$nombreligne = mysql_num_rows($call_eleve);
		$i = "0" ;
		while ($i < $nombreligne) {
			$eleve = mysql_result($call_eleve, $i, 'login');
			$nom_el = mysql_result($call_eleve, $i, 'nom');
			$prenom_el = mysql_result($call_eleve, $i, 'prenom');
			echo "<option value='$eleve'>$nom_el  $prenom_el </option>\n";
			$i++;
		}
		?>
		</select>
		</td><td>
		<p>Classe : <?php echo $classe2; ?><br /> Veuillez sélectionner l'élève n°2:<br />
		<select size='1' name='v_eleve2'>
		<?php
		$call_eleve = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe2' and c.login=e.login) order by nom");
		$nombreligne = mysql_num_rows($call_eleve);
		$i = "0" ;
		while ($i < $nombreligne) {
			$eleve = mysql_result($call_eleve, $i, 'login');
			$nom_el = mysql_result($call_eleve, $i, 'nom');
			$prenom_el = mysql_result($call_eleve, $i, 'prenom');
			echo "<option value='$eleve'>$nom_el  $prenom_el  </option>\n";
		$i++;
		}
		?>
		</select></td></tr></table>

		<p>Choisissez quelle période vous souhaitez visualiser :<br />
		<?php
		$periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
		$nb_periode = mysql_num_rows($periode_query) + 1 ;
		$i = "1";
		while ($i < $nb_periode) {
			$nom_periode[$i] = mysql_result($periode_query, $i-1, "nom_periode");
			echo "<input type='radio' name='periode' value='$i' "; if ($i == '1') { echo "CHECKED";} echo " />$nom_periode[$i]<br />";
		$i++;
		}
		?>
		<input type='radio' name='periode' value='annee' />Année complète</p>
		<input type='hidden' name='id_classe' value='<?php echo $id_classe; ?>' />
		<input type='hidden' name='id_classe2' value='<?php echo $id_classe2; ?>' />
		<input type='submit' value='Visualiser' />
		</form>

	<?php } else {

		?> | <a href="eleve_eleve.php">Choix des classes</a> | <a href="eleve_eleve.php?id_classe=<?php echo $id_classe;?>&amp;id_classe2=<?php echo $id_classe2;?>">Choix des élèves</a></p><?php
		// On appelle les informations de l'utilisateur pour les afficher :
		//$call_eleve1_info = mysql_query("SELECT login,nom,prenom FROM eleves WHERE login='$v_eleve1'");
		$call_eleve1_info = mysql_query("SELECT * FROM eleves WHERE login='$v_eleve1'");
		$eleve1_nom = mysql_result($call_eleve1_info, "0", "nom");
		$eleve1_prenom = mysql_result($call_eleve1_info, "0", "prenom");
		//$call_eleve2_info = mysql_query("SELECT login,nom,prenom FROM eleves WHERE login='$v_eleve2'");
		$call_eleve2_info = mysql_query("SELECT * FROM eleves WHERE login='$v_eleve2'");
		$eleve2_nom = mysql_result($call_eleve2_info, "0", "nom");
		$eleve2_prenom = mysql_result($call_eleve2_info, "0", "prenom");
		$v_legend1 = "";
		$v_legend2 = "";
		$v_legend1 = $eleve1_nom." ".$eleve1_prenom;
		$v_legend2 = $eleve2_nom." ".$eleve2_prenom;


		// On récupère des infos sur l'élève 1:
		$v_elenoet1=mysql_result($call_eleve1_info, "0", 'elenoet');
		$v_naissance1=mysql_result($call_eleve1_info, "0", 'naissance');
		$tmp_tab_naissance=explode("-",$v_naissance1);
		$v_naissance1=$tmp_tab_naissance[2]."/".$tmp_tab_naissance[1]."/".$tmp_tab_naissance[0];
		$v_sexe1=mysql_result($call_eleve1_info, "0", 'sexe');
		$v_eleve_nom_prenom1=$v_legend1;

		// On récupère des infos sur l'élève 2:
		$v_elenoet2=mysql_result($call_eleve2_info, "0", 'elenoet');
		$v_naissance2=mysql_result($call_eleve2_info, "0", 'naissance');
		unset($tmp_tab_naissance);
		$tmp_tab_naissance=explode("-",$v_naissance2);
		$v_naissance2=$tmp_tab_naissance[2]."/".$tmp_tab_naissance[1]."/".$tmp_tab_naissance[0];
		$v_sexe2=mysql_result($call_eleve2_info, "0", 'sexe');
		$v_eleve_nom_prenom2=$v_legend2;


		if ($periode != 'annee') {
			$temp = my_strtolower($nom_periode[$periode]);
		} else {
			$temp = 'Année complète';
		}
		$graph_title = $eleve1_nom." ".$eleve1_prenom." ".$classe." et ".$eleve2_nom." ".$eleve2_prenom." ".$classe2."  | ".$temp;
		echo "<p class='bold'>$eleve1_nom  $eleve1_prenom ($classe) et $eleve2_nom $eleve2_prenom ($classe2)   |  $temp</p>\n";
		echo "<table  border='1' cellspacing='2' cellpadding='5'>\n";
		echo "<tr><td width='100'><p>Matière</p></td><td width='100'><p>$eleve1_nom $eleve1_prenom</p></td><td width='100'><p>$eleve2_nom $eleve2_prenom</p></td><td width='100'><p>Différence</p></td></tr>\n";
		//$call_classe_infos = mysql_query("SELECT DISTINCT  m.* FROM matieres m,j_classes_matieres_professeurs j WHERE (m.matiere = j.id_matiere AND j.id_classe='$id_classe') ORDER BY j.priorite");
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
			"jeg.login = '" . $v_eleve1 ."' AND " .
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
			"jeg.login = '" . $v_eleve1 . "' AND " .
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
		while ($i < $nombre_lignes) {
			$inserligne="no";
			$group_id = mysql_result($call_groupes, $i, "id_groupe");
			$current_group = get_group($group_id);
			// On essaie maintenant de récupérer un groupe avec la même matière, auquel participerait l'élève 2
			$call_group2 = mysql_query("SELECT distinct(jeg.id_groupe) id_groupe FROM j_eleves_groupes jeg, j_groupes_matieres jgm WHERE (" .
					"jeg.login = '" . $v_eleve2 . "' and " .
					"jeg.id_groupe = jgm.id_groupe and " .
					"jgm.id_matiere = '" . $current_group["matiere"]["matiere"] . "')");

			if (mysql_num_rows($call_group2) == 1) {
				$group2_id = mysql_result($call_group2, 0, "id_groupe");
				$current_group2 = get_group($group2_id);
			} else {
				$current_group2 = false;
			}

			if ($current_group2) {
				if ($periode != 'annee') {
					if (in_array($v_eleve1, $current_group["eleves"][$periode]["list"]) AND in_array($v_eleve2, $current_group2["eleves"][$periode]["list"])) {
						$inserligne="yes";
						$note_eleve1_query=mysql_query("SELECT * FROM matieres_notes WHERE (login='$v_eleve1' AND periode='$periode' AND id_groupe='" . $current_group["id"] . "')");
						$note_eleve2_query=mysql_query("SELECT * FROM matieres_notes WHERE (login='$v_eleve2' AND periode='$periode' AND id_groupe='" . $current_group2["id"] . "')");
						$eleve1_matiere_statut = @mysql_result($note_eleve1_query, 0, "statut");
						$note_eleve1 = @mysql_result($note_eleve1_query, 0, "note");
						if ($eleve1_matiere_statut != "") { $note_eleve1 = $eleve1_matiere_statut;}
						if ($note_eleve1 == '') {$note_eleve1 = '-';}
						$eleve2_matiere_statut = @mysql_result($note_eleve2_query, 0, "statut");
						$note_eleve2 = @mysql_result($note_eleve2_query, 0, "note");
						if ($eleve2_matiere_statut != "") { $note_eleve2 = $eleve2_matiere_statut;}
						if ($note_eleve2 == '') {$note_eleve2 = '-';}
					}
				} else {

					if (in_array($v_eleve1, $current_group["eleves"]["all"]["list"]) AND in_array($v_eleve2, $current_group2["eleves"]["all"]["list"])) {
						$inserligne="yes";
						$note_eleve1_query=mysql_query("SELECT round(avg(note),1) as moyenne FROM matieres_notes WHERE (login='$v_eleve1' AND id_groupe='" . $current_group["id"] . "' AND statut ='')");
						$note_eleve2_query=mysql_query("SELECT round(avg(note),1) as moyenne FROM matieres_notes WHERE (login='$v_eleve2' AND id_groupe='" . $current_group2["id"] . "' AND statut ='')");
						$note_eleve1 = @mysql_result($note_eleve1_query, 0, "moyenne");
						if ($note_eleve1 == '') {$note_eleve1 = '-';}
						$note_eleve2 = @mysql_result($note_eleve2_query, 0, "moyenne");
						if ($note_eleve2 == '') {$note_eleve2 = '-';}
					}
				}
				if ($inserligne == "yes") {

					if ($affiche_categories) {
					// On regarde si on change de catégorie de matière
						if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat_id) {
							$prev_cat_id = $current_group["classes"]["classes"][$id_classe]["categorie_id"];
							// On est dans une nouvelle catégorie
							// On récupère les infos nécessaires, et on affiche une ligne
							//$cat_name = html_entity_decode(mysql_result(mysql_query("SELECT nom_complet FROM matieres_categories WHERE id = '" . $current_group["classes"]["classes"][$id_classe]["categorie_id"] . "'"), 0));
							$cat_name = mysql_result(mysql_query("SELECT nom_complet FROM matieres_categories WHERE id = '" . $current_group["classes"]["classes"][$id_classe]["categorie_id"] . "'"), 0);
							// On détermine le nombre de colonnes pour le colspan
							$nb_total_cols = 4;

							// On a toutes les infos. On affiche !
							echo "<tr>";
							echo "<td colspan='" . $nb_total_cols . "'>";
							echo "<p style='padding: 5; margin:0; font-size: 15px;'>".$cat_name."</p></td>";
							echo "</tr>";
						}
					}

					//echo "<tr><td><p>" . $current_group["matiere"]["nom_complet"] . "</p></td><td><p>";
					echo "<tr><td><p>" . htmlspecialchars($current_group["matiere"]["nom_complet"]) . "</p></td><td><p>";
					echo "$note_eleve1";
					echo "</p></td><td><p>";
					echo "$note_eleve2";
					if (($note_eleve1 == "-") or ($note_eleve2 == "-")) {$difference = '-';} else {$difference = $note_eleve1-$note_eleve2;}
					echo "</p></td><td><p>$difference</p></td></tr>\n";
					(preg_match("/^[0-9\.\,]{1,}$/", $note_eleve1)) ? array_push($datay1,"$note_eleve1") : array_push($datay1,"0");
					(preg_match("/^[0-9\.\,]{1,}$/", $note_eleve2)) ? array_push($datay2,"$note_eleve2") : array_push($datay2,"0");
					//array_push($etiquette,$current_group["matiere"]["nom_complet"]);
					array_push($etiquette,rawurlencode($current_group["matiere"]["nom_complet"]));
					$compteur++;
				}
			}
			$i++;
		}
		echo "</table>\n";


		echo "<a name=\"graph\"></a>";
		$temp1=implode("|", $datay1);
		if ( empty($temp1) ) { $temp1 = 0; }
		$temp2=implode("|", $datay2);
		if ( empty($temp2) ) { $temp2 = 0; }
		$etiq = implode("|", $etiquette);
		$graph_title = urlencode($graph_title);
		$v_legend1 = urlencode($v_legend1);
		$v_legend2 = urlencode($v_legend2);

		echo "<table border='0'>\n";
		echo "<tr>\n";
		echo "<td>\n";
		//echo "<img src='./draw_artichow1.php?temp1=$temp1&temp2=$temp2&etiquette=$etiq&titre=$graph_title&v_legend1=$v_legend1&v_legend2=$v_legend2&compteur=$compteur&nb_data=3' alt='Graphes comparés de $v_legend1 et $v_legend2' />\n";
		echo "<img src='./draw_artichow1.php?temp1=$temp1&amp;temp2=$temp2&amp;etiquette=$etiq&amp;titre=$graph_title&amp;v_legend1=$v_legend1&amp;v_legend2=$v_legend2&amp;compteur=$compteur&amp;nb_data=3' alt='Graphes comparés de $v_legend1 et $v_legend2' />\n";
		echo "<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />&nbsp;\n";
		echo "</td>\n";
		echo "<td valign='top'>\n";

		// ============================================
		// Création de l'infobulle1:

		$titre=$v_eleve_nom_prenom1;
		//$texte="<table border='0'>\n";
		$texte="<div align='center'>\n";
		//$texte.="<tr>\n";
		if($v_elenoet1!=""){
			$photo=nom_photo($v_elenoet1);
			//if("$photo"!=""){
			if($photo) {
				$texte.="<img src='".$photo."' width='150' alt=\"$v_eleve_nom_prenom1\" />";
				$texte.="<br />\n";
			}
		}
		//$texte.="<td>\n";
		//$texte.="\$v_elenoet1=$v_elenoet1<br />\n";
		$texte.="Né";
		if($v_sexe1=="F"){
			$texte.="e";
		}
		$texte.=" le $v_naissance1\n";
		//$texte.="</td>\n";
		//$texte.="</tr>\n";
		//$texte.="</table>\n";
		$texte.="</div>\n";

		//echo creer_div_infobulle('info_popup_eleve',$titre,"",$texte,"",30,0,'y','y','n','n');
		$tabdiv_infobulle[]=creer_div_infobulle('info_popup_eleve1',$titre,"",$texte,"",14,0,'y','y','n','n');

		// ============================================

		// Insertion du lien permettant l'affichage de l'infobulle:
		echo "<a href='#' onmouseover=\"afficher_div('info_popup_eleve1','y',-100,20);\"";
		//echo " onmouseout=\"cacher_div('info_popup_eleve');\"";
		echo ">";
		echo "<img src='../images/icons/buddy.png' alt='Informations élève 1' />";
		echo "</a>";
		echo "<br />";

		// ============================================
		// Création de l'infobulle2:

		$titre=$v_eleve_nom_prenom2;
		//$texte="<table border='0'>\n";
		$texte="<div align='center'>\n";
		//$texte.="<tr>\n";
		if($v_elenoet2!=""){
			$photo=nom_photo($v_elenoet2);
			//if("$photo"!=""){
			if($photo) {
				$texte.="<img src='".$photo."' width='150' alt=\"$v_eleve_nom_prenom2\" />";
				$texte.="<br />\n";
			}
		}
		//$texte.="<td>\n";
		$texte.="<br />\n";
		$texte.="Né";
		if($v_sexe2=="F"){
			$texte.="e";
		}
		$texte.=" le $v_naissance2\n";
		//$texte.="</td>\n";
		//$texte.="</tr>\n";
		//$texte.="</table>\n";
		$texte.="</div>\n";

		//echo creer_div_infobulle('info_popup_eleve',$titre,"",$texte,"",30,0,'y','y','n','n');
		$tabdiv_infobulle[]=creer_div_infobulle('info_popup_eleve2',$titre,"",$texte,"",14,0,'y','y','n','n');

		// ============================================

		// Insertion du lien permettant l'affichage de l'infobulle:
		echo "<a href='#' onmouseover=\"afficher_div('info_popup_eleve2','y',-100,20);\"";
		//echo " onmouseout=\"cacher_div('info_popup_eleve');\"";
		echo ">";
		echo "<img src='../images/icons/buddy.png' alt='Informations élève 2' />";
		echo "</a>";


		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
}

//===========================================================
echo "<p><em>NOTE&nbsp;:</em></p>\n";
require("../lib/textes.inc.php");
echo "<p style='margin-left: 3em;'>$explication_bulletin_ou_graphe_vide</p>\n";
//===========================================================

require("../lib/footer.inc.php");
?>
