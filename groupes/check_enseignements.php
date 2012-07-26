<?php
/**
 * saisie des Notes
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @license GNU/GPL

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

$sql="SELECT 1=1 FROM droits WHERE id='/groupes/check_enseignements.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/groupes/check_enseignements.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Controle des enseignements',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(isset($_GET['modif_visu_cn'])) {
	check_token();
	if($_GET['modif_visu_cn']=='afficher') {
		$valeur="y";
	}
	else {
		$valeur="n";
	}

	$id_groupe=isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL;
	if(isset($id_groupe)) {
		$sql="SELECT visible FROM j_groupes_visibilite WHERE id_groupe='$id_groupe' AND domaine='cahier_notes';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$sql="INSERT INTO j_groupes_visibilite SET id_groupe='$id_groupe', domaine='cahier_notes', visible='$valeur';";
			$insert=mysql_query($sql);
		}
		else {
			$sql="UPDATE j_groupes_visibilite SET visible='$valeur' WHERE id_groupe='$id_groupe' AND domaine='cahier_notes';";
			$update=mysql_query($sql);
		}
	}
}

if(isset($_GET['modif_visu_bull'])) {
	check_token();
	if($_GET['modif_visu_bull']=='afficher') {
		$valeur="y";
	}
	else {
		$valeur="n";
	}

	$id_groupe=isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL;
	if(isset($id_groupe)) {
		$sql="SELECT visible FROM j_groupes_visibilite WHERE id_groupe='$id_groupe' AND domaine='bulletins';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$sql="INSERT INTO j_groupes_visibilite SET id_groupe='$id_groupe', domaine='bulletins', visible='$valeur';";
			$insert=mysql_query($sql);
		}
		else {
			$sql="UPDATE j_groupes_visibilite SET visible='$valeur' WHERE id_groupe='$id_groupe' AND domaine='bulletins';";
			$update=mysql_query($sql);
		}
	}
}

if(isset($_GET['modif_coef'])) {
	check_token();

	$coef=$_GET['modif_coef'];

	$id_groupe=isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL;
	$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;
	if((isset($id_groupe))&&(isset($id_classe))) {
		$sql="SELECT coef FROM j_groupes_classes WHERE id_groupe='$id_groupe' AND id_classe='$id_classe';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$msg="Anomalie&nbsp;: Le groupe n'existe pas dans j_groupes_classes???<br />";
		}
		else {
			$sql="UPDATE j_groupes_classes SET coef='$coef' WHERE id_groupe='$id_groupe' AND id_classe='$id_classe';";
			$update=mysql_query($sql);
		}
	}
}

$themessage  = 'Des paramètres ont été modifiés. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Enseignements";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class='bold'>\n";
echo "<a href=\"../classes/index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>";

echo "</p>\n";

echo "<p><a href='".$_SERVER['PHP_SELF']."?'>Ne rien griser</a>,<br />
<a href='".$_SERVER['PHP_SELF']."?griser_cn=y'>Griser</a> ou <a href='".$_SERVER['PHP_SELF']."?masquer_cn=y'>masquer</a> les lignes d'enseignements n'apparaissant pas dans les carnets de notes,<br />
<a href='".$_SERVER['PHP_SELF']."?griser_bull=y'>Griser</a> ou <a href='".$_SERVER['PHP_SELF']."?masquer_bull=y'>masquer</a> les lignes d'enseignements n'apparaissant pas dans les bulletins,<br />
<a href='".$_SERVER['PHP_SELF']."?griser_cn=y&amp;griser_bull=y'>Griser</a> ou <a href='".$_SERVER['PHP_SELF']."?masquer_cn=y&amp;masquer_bull=y'>masquer</a> les lignes d'enseignements n'apparaissant pas dans un des domaines (<em>carnets de notes et/ou bulletins</em>).</p>\n";

$griser_cn=isset($_GET['griser_cn']) ? $_GET['griser_cn'] : "n";
$griser_bull=isset($_GET['griser_bull']) ? $_GET['griser_bull'] : "n";
$masquer_cn=isset($_GET['masquer_cn']) ? $_GET['masquer_cn'] : "n";
$masquer_bull=isset($_GET['masquer_bull']) ? $_GET['masquer_bull'] : "n";

$option_grisage="&amp;griser_cn=$griser_cn&amp;griser_bull=$griser_bull";
$option_masquage="&amp;masquer_cn=$masquer_cn&amp;masquer_bull=$masquer_bull";

$sql="SELECT id, classe FROM classes ORDER BY classe;";
$res_classe=mysql_query($sql);
if(mysql_num_rows($res_classe)==0) {
	echo "<p style='color:red'>Aucune classe n'a été trouvée.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

while($lig_classe=mysql_fetch_object($res_classe)) {
	echo "<a name='classe_".$lig_classe->classe."'></a>\n";
	echo "<p class='bold'>Classe de $lig_classe->classe</p>\n";
	echo "<div style='margin-left:2em;'>\n";
	$groups = get_groups_for_class($lig_classe->id,"","n");
	if(count($groups)==0){
		echo "<p style='color:red'>Aucun enseignement n'a été trouvé.</p>\n";
	}
	else {
		echo "<table class='boireaus'>\n";
		echo "<tr>\n";
		echo "<th colspan='2'>Enseignement</th>\n";
		echo "<th>Classes</th>\n";
		echo "<th>Catégorie</th>\n";
		echo "<th>Coefficient</th>\n";
		echo "<th>Visu.CN</th>\n";
		echo "<th>Visu.Bull</th>\n";
		echo "</tr>\n";
		$alt=1;
		foreach ($groups as $group) {
			$current_group=get_group($group["id"]);

			$afficher_ligne="y";
			if(($masquer_cn=="y")&&(isset($current_group["visibilite"]["cahier_notes"]))&&($current_group["visibilite"]["cahier_notes"]=="n")) {$afficher_ligne="n";}
			if(($masquer_bull=="y")&&(isset($current_group["visibilite"]["bulletins"]))&&($current_group["visibilite"]["bulletins"]=="n")) {$afficher_ligne="n";}

			if($afficher_ligne=="y") {
				if((($griser_cn=='y')||($griser_bull=='y'))&&(isset($current_group["visibilite"]["cahier_notes"]))&&($current_group["visibilite"]["cahier_notes"]=="n")) {
					echo "<tr class='white_hover' style='background-color: grey;'>\n";
				}
				elseif((($griser_cn=='y')||($griser_bull=='y'))&&(isset($current_group["visibilite"]["bulletins"]))&&($current_group["visibilite"]["bulletins"]=="n")) {
					echo "<tr class='white_hover' style='background-color: grey;'>\n";
				}
				else {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt white_hover'>\n";
				}
				echo "<td>".$current_group['name']."</td>\n";
				echo "<td>".$current_group['description']."</td>\n";
				echo "<td>".$current_group['classlist_string']."</td>\n";
				echo "<td>";
				$sql="SELECT mc.nom_court FROM j_groupes_classes jgc, matieres_categories mc WHERE jgc.categorie_id=mc.id AND jgc.id_groupe='".$current_group["id"]."' AND jgc.id_classe='$lig_classe->id';";
				$res_cat=mysql_query($sql);
				if(mysql_num_rows($res_cat)>0) {
					$lig_cat=mysql_fetch_object($res_cat);
					echo $lig_cat->nom_court;
				}
				echo "</td>\n";
	
				echo "<td>";
				$coef=$current_group["classes"]["classes"][$lig_classe->id]['coef'];
				$coef_up=$current_group["classes"]["classes"][$lig_classe->id]['coef']+1;
				$coef_down=max($current_group["classes"]["classes"][$lig_classe->id]['coef']-1,0);
				echo "<a href='".$_SERVER['PHP_SELF']."?modif_coef=$coef_up&amp;id_groupe=".$current_group["id"]."&amp;id_classe=$lig_classe->id".$option_grisage.$option_masquage.add_token_in_url()."#classe_$lig_classe->classe'>";
				echo "<img src='../images/icons/add.png' /> ";
				echo "</a>";
				echo $coef;
				echo " <a href='".$_SERVER['PHP_SELF']."?modif_coef=$coef_down&amp;id_groupe=".$current_group["id"]."&amp;id_classe=$lig_classe->id".$option_grisage.$option_masquage.add_token_in_url()."#classe_$lig_classe->classe'>";
				echo "<img src='../images/icons/remove.png' />";
				echo "</a>";
				echo "</td>\n";
	
				echo "<td>";
				if((!isset($current_group["visibilite"]["cahier_notes"]))||($current_group["visibilite"]["cahier_notes"]=="y")) {
					echo "<a href='".$_SERVER['PHP_SELF']."?modif_visu_cn=cacher&amp;id_groupe=".$current_group["id"].$option_grisage.$option_masquage.add_token_in_url()."#classe_$lig_classe->classe'>";
					echo "<span style='color:blue;'>OUI</span>";
					echo "</a>";
				}
				else {
					echo "<a href='".$_SERVER['PHP_SELF']."?modif_visu_cn=afficher&amp;id_groupe=".$current_group["id"].$option_grisage.$option_masquage.add_token_in_url()."#classe_$lig_classe->classe'>";
					echo "<span style='color:red;'>NON</span>";
					echo "</a>";
				}
				echo "</td>\n";
				echo "<td>";
				if((!isset($current_group["visibilite"]["bulletins"]))||($current_group["visibilite"]["bulletins"]=="y")) {
					echo "<a href='".$_SERVER['PHP_SELF']."?modif_visu_bull=cacher&amp;id_groupe=".$current_group["id"].$option_grisage.$option_masquage.add_token_in_url()."#classe_$lig_classe->classe'>";
					echo "<span style='color:blue;'>OUI</span>";
					echo "</a>";
				}
				else {
					echo "<a href='".$_SERVER['PHP_SELF']."?modif_visu_bull=afficher&amp;id_groupe=".$current_group["id"].$option_grisage.$option_masquage.add_token_in_url()."#classe_$lig_classe->classe'>";
					echo "<span style='color:red;'>NON</span>";
					echo "</a>";
				}
				echo "</td>\n";
				echo "</tr>\n";
			}
		}
		echo "</table>\n";
	}
	echo "</div>\n";
	flush();
}

/**
 * Pied de page
 */
require("../lib/footer.inc.php");
?>
