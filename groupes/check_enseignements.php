<?php
/**
 * Verifications sur les catégories, coef, visibilité des groupes,...
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
	if((isset($id_groupe))&&(is_numeric($id_groupe))) {
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
	if((isset($id_groupe))&&(is_numeric($id_groupe))) {
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

if(isset($_GET['modif_visu_cdt'])) {
	check_token();
	if($_GET['modif_visu_cdt']=='afficher') {
		$valeur="y";
	}
	else {
		$valeur="n";
	}

	$id_groupe=isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL;
	if((isset($id_groupe))&&(is_numeric($id_groupe))) {
		$sql="SELECT visible FROM j_groupes_visibilite WHERE id_groupe='$id_groupe' AND domaine='cahier_texte';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$sql="INSERT INTO j_groupes_visibilite SET id_groupe='$id_groupe', domaine='cahier_texte', visible='$valeur';";
			$insert=mysql_query($sql);
		}
		else {
			$sql="UPDATE j_groupes_visibilite SET visible='$valeur' WHERE id_groupe='$id_groupe' AND domaine='cahier_texte';";
			$update=mysql_query($sql);
		}
	}
}

if(isset($_GET['modif_coef'])) {
	check_token();

	$coef=$_GET['modif_coef'];

	$id_groupe=isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL;
	$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;
	if((isset($id_groupe))&&(isset($id_classe))&&(is_numeric($id_groupe))&&(is_numeric($id_classe))) {
		$sql="SELECT coef FROM j_groupes_classes WHERE id_groupe='$id_groupe' AND id_classe='$id_classe';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$msg="Anomalie&nbsp;: Le groupe n'existe pas dans j_groupes_classes???<br />";

			if(isset($_GET['modif_ajax'])) {
				echo "<span style='color:red' title=\"Anomalie : Le groupe n'existe pas dans j_groupes_classes ???\n$sql\">Anomalie</span>";
				die();
			}
		}
		else {
			$sql="UPDATE j_groupes_classes SET coef='$coef' WHERE id_groupe='$id_groupe' AND id_classe='$id_classe';";
			$update=mysql_query($sql);

			if(($update)&&(isset($_GET['modif_ajax']))) {
				echo $coef;
				die();
			}
		}
	}
}

if((isset($_GET['modif_ajax']))&&(isset($_GET['domaine']))&&(isset($_GET['id_groupe']))&&(isset($_GET['passer_a']))) {
	check_token();

	$domaine=$_GET['domaine'];
	$passer_a=$_GET['passer_a'];
	if(!in_array($domaine, $tab_domaines)) {
		echo "<span style='color:red' title=\"Erreur : Le domaine choisi est inconnu : $domaine\">Erreur</span>";
	}
	elseif(($passer_a!='y')&&($passer_a!='n')) {
		echo "<span style='color:red' title=\"Le mode choisi est inconnu : $passer_a\">Erreur</span>";
	}
	else {
		$id_groupe=isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL;
		if((isset($id_groupe))&&(is_numeric($id_groupe))) {
			$sql="SELECT visible FROM j_groupes_visibilite WHERE id_groupe='$id_groupe' AND domaine='$domaine';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0) {
				$sql="INSERT INTO j_groupes_visibilite SET id_groupe='$id_groupe', domaine='$domaine', visible='$passer_a';";
				$insert_ou_update=mysql_query($sql);
			}
			else {
				$sql="UPDATE j_groupes_visibilite SET visible='$passer_a' WHERE id_groupe='$id_groupe' AND domaine='$domaine';";
				$insert_ou_update=mysql_query($sql);
			}

			if($insert_ou_update) {
				if($passer_a=='y') {
					echo "<span style='color:blue'>OUI</span>";
				}
				else {
					echo "<span style='color:red'>NON</span>";
				}
			}
			else {
				echo "<span style='color:red' title=\"Erreur lors de $sql\">Erreur</span>";
			}
		}
		else {
			echo "<span style='color:red' title=\"Le groupe n'est pas choisi ou non valable.\">Erreur</span>";
		}
	}
	die();
}

$themessage  = 'Des paramètres ont été modifiés. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Enseignements";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class='bold'>\n";
echo "<a href=\"../classes/index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>";
echo "</p>\n";
/*
$sql="SELECT display_mat_cat FROM classes WHERE id='".$_id_classe."';";
$res=mysql_query($sql);
if(mysql_num_rows($res)>0) {
	$display_mat_cat=mysql_result($res,0,"display_mat_cat");
}
*/
if(!isset($_GET['tri'])) {
	//$tri='priorite';
	//$d_apres_categories=$display_mat_cat;
	$d_apres_categories="auto";
	$option_tri="";
	echo "<p class='bold'>Les enseignements sont triés pour chaque classe individuellement selon le paramétrage de la classe<br />(<em style='font-size:x-small'>par catégorie si vous avez choisi d'utiliser les catégories de matières pour la classe (dans Gestion des classes/Telle_classe Paramètres),<br />et sinon par priorités (telles que définies dans Gestion des classes/Telle_classe Enseignements)</em>).</p>";
}
elseif($_GET['tri']=='priorite') {
	$tri='priorite';
	$d_apres_categories="n";
	$option_tri="&amp;tri=$tri";
	echo "<p class='bold'>Vous avez demandé à afficher pour toutes les classes, les enseignements d'après leurs priorités<br />(<em style='font-size:x-small'>telles que définies dans Gestion des classes/Telle_classe Enseignements</em>).</p>";
}
else {
	$tri='categorie';
	$d_apres_categories="y";
	$option_tri="&amp;tri=$tri";
	echo "<p class='bold'>Vous avez demandé à afficher pour toutes les classes, les enseignements d'après leurs catégories<br />(<em style='font-size:x-small'>telles que définies dans Gestion des classes/Telle_classe Enseignements</em>).</p>";
}

echo "<p>Pour toutes les classes, afficher les enseignements <a href='".$_SERVER['PHP_SELF']."?tri=priorite' title='Les priorités sont définies dans Gestion des classes/Telle_classe Enseignements'>par ordre de priorité</a>,<br />
<a href='".$_SERVER['PHP_SELF']."?tri=categorie' title='ATTENTION : Les enseignements qui ne sont dans aucune catégorie ne seront pas affichés.'>par catégorie</a><br />
ou <a href='".$_SERVER['PHP_SELF']."'>utiliser le paramétrage par défaut de la classe</a>.</p>\n";

echo "<br />";

echo "<p><a href='".$_SERVER['PHP_SELF']."?'>Ne rien griser</a>,<br />
<a href='".$_SERVER['PHP_SELF']."?griser_cn=y$option_tri'>Griser</a> ou <a href='".$_SERVER['PHP_SELF']."?masquer_cn=y$option_tri'>masquer</a> les lignes d'enseignements n'apparaissant pas dans les carnets de notes,<br />
<a href='".$_SERVER['PHP_SELF']."?griser_bull=y$option_tri'>Griser</a> ou <a href='".$_SERVER['PHP_SELF']."?masquer_bull=y$option_tri'>masquer</a> les lignes d'enseignements n'apparaissant pas dans les bulletins,<br />
<a href='".$_SERVER['PHP_SELF']."?griser_cn=y&amp;griser_bull=y$option_tri'>Griser</a> ou <a href='".$_SERVER['PHP_SELF']."?masquer_cn=y&amp;masquer_bull=y$option_tri'>masquer</a> les lignes d'enseignements n'apparaissant pas dans un des domaines (<em>carnets de notes, bulletins et/ou cahiers de textes</em>).</p>\n";

$griser_cn=isset($_GET['griser_cn']) ? $_GET['griser_cn'] : "n";
$griser_bull=isset($_GET['griser_bull']) ? $_GET['griser_bull'] : "n";
$griser_cdt=isset($_GET['griser_cdt']) ? $_GET['griser_cdt'] : "n";
$masquer_cn=isset($_GET['masquer_cn']) ? $_GET['masquer_cn'] : "n";
$masquer_bull=isset($_GET['masquer_bull']) ? $_GET['masquer_bull'] : "n";
$masquer_cdt=isset($_GET['masquer_cdt']) ? $_GET['masquer_cdt'] : "n";

$option_grisage="&amp;griser_cn=$griser_cn&amp;griser_bull=$griser_bull&amp;griser_cdt=$griser_cdt$option_tri";
$option_masquage="&amp;masquer_cn=$masquer_cn&amp;masquer_bull=$masquer_bull&amp;masquer_cdt=$masquer_cdt$option_tri";

$sql="SELECT id, classe FROM classes ORDER BY classe;";
$res_classe=mysql_query($sql);
if(mysql_num_rows($res_classe)==0) {
	echo "<p style='color:red'>Aucune classe n'a été trouvée.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

echo "<script type='text/javascript'>
var griser_cn='$griser_cn';
var griser_bull='$griser_bull';
var griser_cdt='$griser_cdt';
var masquer_cn='$masquer_cn';
var masquer_bull='$masquer_bull';
var masquer_cdt='$masquer_cdt';

function modif_coef(id, delta, id_groupe, id_classe) {
	if(document.getElementById(id)) {
		coef_actuel=document.getElementById(id).innerHTML;
		coef_modifie=eval(eval(coef_actuel)+delta);
		//alert('coef_actuel='+coef_actuel);

		if(coef_modifie>=0) {
			//alert(eval(eval(coef_actuel)+delta));

			new Ajax.Updater($(id),'".$_SERVER['PHP_SELF']."?modif_ajax=y&modif_coef='+coef_modifie+'&id_groupe='+id_groupe+'&id_classe='+id_classe+'".add_token_in_url(false)."',{method: 'get'});
		}
	}
}

function modif_visibilite(domaine, id, id_groupe) {
	if(document.getElementById(id)) {
		contenu_actuel=document.getElementById(id).innerHTML;
		passer_a='';
		/*
		alert(contenu_actuel)
		alert(contenu_actuel.search('/OUI/'))
		if(contenu_actuel.search('/OUI/')!=-1) {
			passer_a='n';
		}
		else {
			if(contenu_actuel.search('/NON/')!=-1) {
				passer_a='y';
			}
		}
		*/
		var reg1=new RegExp(\"OUI\",\"g\");
		if (contenu_actuel.match(reg1)) {
			passer_a='n';
		}
		else {
			var reg2=new RegExp(\"NON\",\"g\");
			if(contenu_actuel.match(reg2)) {
				passer_a='y';
			}
		}

		//alert(passer_a);
		if((passer_a=='y')||(passer_a=='n')) {

			new Ajax.Updater($(id),'".$_SERVER['PHP_SELF']."?modif_ajax=y&domaine='+domaine+'&passer_a='+passer_a+'&id_groupe='+id_groupe+'".add_token_in_url(false)."',{method: 'get'});

			// On ne fait que griser même si on a demandé à masquer: précaution pour voir une éventuelle erreur de la requête ajax
			if(((griser_cn=='y')&&(domaine=='cahier_notes'))||
				((griser_bull=='y')&&(domaine=='bulletins'))||
				((griser_cdt=='y')&&(domaine=='cahier_texte'))||
				((masquer_cn=='y')&&(domaine=='cahier_notes'))||
				((masquer_bull=='y')&&(domaine=='bulletins'))||
				((masquer_cdt=='y')&&(domaine=='cahier_texte'))
				) {

				if(passer_a=='y') {
					document.getElementById('tr_'+id_groupe).style.backgroundColor='';
				}
				else {
					document.getElementById('tr_'+id_groupe).style.backgroundColor='grey';
				}
			}
		}
	}
}
</script>\n";

while($lig_classe=mysql_fetch_object($res_classe)) {
	echo "<a name='classe_".$lig_classe->classe."'></a>\n";
	echo "<p class='bold'>Classe de $lig_classe->classe</p>\n";
	echo "<div style='margin-left:2em;'>\n";
	//$groups = get_groups_for_class($lig_classe->id,"","n");
	$groups = get_groups_for_class($lig_classe->id,"",$d_apres_categories);
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
		echo "<th>Visu.CDT</th>\n";
		echo "</tr>\n";
		$alt=1;
		foreach ($groups as $group) {
			$current_group=get_group($group["id"]);

			$afficher_ligne="y";
			if(($masquer_cn=="y")&&(isset($current_group["visibilite"]["cahier_notes"]))&&($current_group["visibilite"]["cahier_notes"]=="n")) {$afficher_ligne="n";}
			if(($masquer_bull=="y")&&(isset($current_group["visibilite"]["bulletins"]))&&($current_group["visibilite"]["bulletins"]=="n")) {$afficher_ligne="n";}
			if(($masquer_cdt=="y")&&(isset($current_group["visibilite"]["cahier_texte"]))&&($current_group["visibilite"]["cahier_texte"]=="n")) {$afficher_ligne="n";}

			if($afficher_ligne=="y") {
				$alt=$alt*(-1);
				if(($griser_cn=='y')&&(isset($current_group["visibilite"]["cahier_notes"]))&&($current_group["visibilite"]["cahier_notes"]=="n")) {
					echo "<tr id='tr_".$current_group["id"]."' class='lig$alt white_hover' style='background-color: grey;'>\n";
				}
				elseif(($griser_bull=='y')&&(isset($current_group["visibilite"]["bulletins"]))&&($current_group["visibilite"]["bulletins"]=="n")) {
					echo "<tr id='tr_".$current_group["id"]."' class='lig$alt white_hover' style='background-color: grey;'>\n";
				}
				elseif(($griser_cdt=='y')&&(isset($current_group["visibilite"]["cahier_texte"]))&&($current_group["visibilite"]["cahier_texte"]=="n")) {
					echo "<tr id='tr_".$current_group["id"]."' class='lig$alt white_hover' style='background-color: grey;'>\n";
				}
				else {
					//$alt=$alt*(-1);
					echo "<tr id='tr_".$current_group["id"]."' class='lig$alt white_hover'>\n";
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
				echo "<a href='".$_SERVER['PHP_SELF']."?modif_coef=$coef_up&amp;id_groupe=".$current_group["id"]."&amp;id_classe=$lig_classe->id".$option_grisage.$option_masquage.add_token_in_url()."#classe_$lig_classe->classe' onclick=\"modif_coef('coef_".$current_group["id"]."', 1, ".$current_group["id"].", ".$lig_classe->id.");return false;\">";
				echo "<img src='../images/icons/add.png' /> ";
				echo "</a>";
				echo "<span id='coef_".$current_group["id"]."'>";
				echo $coef;
				echo "</span>";
				echo " <a href='".$_SERVER['PHP_SELF']."?modif_coef=$coef_down&amp;id_groupe=".$current_group["id"]."&amp;id_classe=$lig_classe->id".$option_grisage.$option_masquage.add_token_in_url()."#classe_$lig_classe->classe' onclick=\"modif_coef('coef_".$current_group["id"]."', -1, ".$current_group["id"].", ".$lig_classe->id.");return false;\">";
				echo "<img src='../images/icons/remove.png' />";
				echo "</a>";
				echo "</td>\n";
	
				echo "<td>";
				if((!isset($current_group["visibilite"]["cahier_notes"]))||($current_group["visibilite"]["cahier_notes"]=="y")) {
					echo "<a href='".$_SERVER['PHP_SELF']."?modif_visu_cn=cacher&amp;id_groupe=".$current_group["id"].$option_grisage.$option_masquage.add_token_in_url()."#classe_$lig_classe->classe' onclick=\"modif_visibilite('cahier_notes', 'visibilite_cn_".$current_group["id"]."', ".$current_group["id"].");return false;\">";
					echo "<span id='visibilite_cn_".$current_group["id"]."'>";
					echo "<span style='color:blue;'>OUI</span>";
					echo "</span>";
					echo "</a>";
				}
				else {
					echo "<a href='".$_SERVER['PHP_SELF']."?modif_visu_cn=afficher&amp;id_groupe=".$current_group["id"].$option_grisage.$option_masquage.add_token_in_url()."#classe_$lig_classe->classe' onclick=\"modif_visibilite('cahier_notes', 'visibilite_cn_".$current_group["id"]."', ".$current_group["id"].");return false;\">";
					echo "<span id='visibilite_cn_".$current_group["id"]."'>";
					echo "<span style='color:red;'>NON</span>";
					echo "</span>";
					echo "</a>";
				}
				echo "</td>\n";
				echo "<td>";
				if((!isset($current_group["visibilite"]["bulletins"]))||($current_group["visibilite"]["bulletins"]=="y")) {
					echo "<a href='".$_SERVER['PHP_SELF']."?modif_visu_bull=cacher&amp;id_groupe=".$current_group["id"].$option_grisage.$option_masquage.add_token_in_url()."#classe_$lig_classe->classe' onclick=\"modif_visibilite('bulletins', 'visibilite_bull_".$current_group["id"]."', ".$current_group["id"].");return false;\">";
					echo "<span id='visibilite_bull_".$current_group["id"]."'>";
					echo "<span style='color:blue;'>OUI</span>";
					echo "</span>";
					echo "</a>";
				}
				else {
					echo "<a href='".$_SERVER['PHP_SELF']."?modif_visu_bull=afficher&amp;id_groupe=".$current_group["id"].$option_grisage.$option_masquage.add_token_in_url()."#classe_$lig_classe->classe' onclick=\"modif_visibilite('bulletins', 'visibilite_bull_".$current_group["id"]."', ".$current_group["id"].");return false;\">";
					echo "<span id='visibilite_bull_".$current_group["id"]."'>";
					echo "<span style='color:red;'>NON</span>";
					echo "</span>";
					echo "</a>";
				}
				echo "</td>\n";

				echo "<td>";
				if((!isset($current_group["visibilite"]["cahier_texte"]))||($current_group["visibilite"]["cahier_texte"]=="y")) {
					echo "<a href='".$_SERVER['PHP_SELF']."?modif_visu_cdt=cacher&amp;id_groupe=".$current_group["id"].$option_grisage.$option_masquage.add_token_in_url()."#classe_$lig_classe->classe' onclick=\"modif_visibilite('cahier_texte', 'visibilite_cdt_".$current_group["id"]."', ".$current_group["id"].");return false;\">";
					echo "<span id='visibilite_cdt_".$current_group["id"]."'>";
					echo "<span style='color:blue;'>OUI</span>";
					echo "</span>";
					echo "</a>";
				}
				else {
					echo "<a href='".$_SERVER['PHP_SELF']."?modif_visu_cdt=afficher&amp;id_groupe=".$current_group["id"].$option_grisage.$option_masquage.add_token_in_url()."#classe_$lig_classe->classe' onclick=\"modif_visibilite('cahier_texte', 'visibilite_cdt_".$current_group["id"]."', ".$current_group["id"].");return false;\">";
					echo "<span id='visibilite_cdt_".$current_group["id"]."'>";
					echo "<span style='color:red;'>NON</span>";
					echo "</span>";
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

echo "<p><br /></p>\n";
echo "<p style='margin-left:3.5em; text-indent:-3.5em;'><em>NOTE&nbsp;</em> Cette page permet de contrôler des paramètres (<em>catégorie, coefficient, visibilité</em>) et de modifier un coefficient ou la visibilité d'un enseignement, mais si vous devez modifier de nombreux paramètres, vous gagnerez du temps à revenir au choix <strong>&lt;Telle_classe&gt;/Enseignements</strong> dans la page de <a href='../classes/index.php'>Gestion des classes</a>.</p>\n";

/**
 * Pied de page
 */
require("../lib/footer.inc.php");
?>
