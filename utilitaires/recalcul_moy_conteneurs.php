<?php
/** Calcule les moyennes d'un conteneur
 * 
 * $Id: recalcul_moy_conteneurs.php 7748 2011-08-14 14:10:02Z regis $
 *
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @package Notes
 * @subpackage Conteneur
 * @license GNU/GPL
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

// INSERT INTO droits VALUES ('/cahier_notes/correction_bug_maj_moy_conteneurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Correction des moyennes des conteneurs', '');
/*
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
*/

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
	die("Le module n'est pas activé.");
}




// Fonction de recherche des conteneurs derniers enfants (sans enfants (non parents, en somme))
// avec recalcul des moyennes lancé...

//recherche_enfant($id_racine);


//**************** EN-TETE *****************
$titre_page = "Carnet de notes - Correction des moyennes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : NULL;
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

echo "<div class='norme'><p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <a href='clean_tables.php'>Retour page précédente</a>";

if(!isset($_POST['recalculer'])){

	if(!isset($num_periode)) {
		echo "</p></div>\n";

		echo "<p class='bold'>Cette page est destinée à effectuer le recalcul des moyennes de conteneurs.<br />Un bug pouvait provoquer une erreur lors de déplacement de devoirs/conteneurs(boites) d'un conteneur(boite) à un autre.</p>\n";

		$sql="SELECT DISTINCT num_periode FROM periodes ORDER BY num_periode";
		$res_per=mysql_query($sql);
		if(mysql_num_rows($res_per)==0){
			echo "<p>Il semble qu'aucune période ne soit encore définie.</p>\n";
		}
		else{
			echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method='post'>\n";

			echo "<p>Choisissez la ou les périodes pour lesquelles provoquer le recalcul.</p>\n";

			while ($lig_per=mysql_fetch_object($res_per)) {
				echo "<label for='per_".$lig_per->num_periode."' style='cursor: pointer;'>";
				echo "<input type='checkbox' name='num_periode[]' id='per_".$lig_per->num_periode."' value='".$lig_per->num_periode."' />";
				echo " Période $lig_per->num_periode</label><br />\n";
			}

			echo "<p><input type=\"submit\" name='suite' value=\"suite\" style=\"font-variant: small-caps;\" /></p>\n";
			echo "</form>\n";
		}
	}
	else {
		echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix des périodes</a>";
		echo "</p></div>\n";

		$sql="SELECT id,classe FROM classes ORDER BY classe;";
		$res_clas=mysql_query($sql);
		$nb=mysql_num_rows($res_clas);
		if($nb==0) {
			echo "<p>Aucune classe n'a été trouvée.</p>\n";
		}
		else {
			echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method='post'>\n";
			echo add_token_field();

			for($i=0;$i<count($num_periode);$i++) {
				echo "<input type='hidden' name='num_periode[]' value='".$num_periode[$i]."' />\n";
			}

			echo "<p>Choisissez la ou les classes pour lesquelles provoquer le recalcul.</p>\n";

			$nb_class_par_colonne=round($nb/3);
			//echo "<table width='100%' border='1'>\n";
			echo "<table width='100%'>\n";
			echo "<tr valign='top' align='center'>\n";

			$i = '0';

			echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
			echo "<td align='left'>\n";

			while ($lig_clas=mysql_fetch_object($res_clas)) {
				$id_classe = $lig_clas->id;
				$temp = "case_".$id_classe;
				$classe = $lig_clas->classe;

				if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
					echo "</td>\n";
					//echo "<td style='padding: 0 10px 0 10px'>\n";
					echo "<td align='left'>\n";
				}

				//echo "<span class = \"norme\"><input type='checkbox' name='$temp' value='yes' onclick=\"verif1()\" />";
				//echo "Classe : $classe </span><br />\n";
				echo "<label for='tab_id_classe_".$i."' style='cursor: pointer;'>";
				echo "<input type='checkbox' name='id_classe[]' id='tab_id_classe_".$i."' value='$id_classe' />";
				echo "Classe : $classe</label><br />\n";
				$i++;
			}
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			echo "<p><input type=\"submit\" name='recalculer' value=\"Recalculer\" style=\"font-variant: small-caps;\" /></p>\n";
			echo "</form>\n";

			echo "<p><a href='#' onClick='ModifCase(true)'>Cocher toutes les classes</a> / <a href='#' onClick='ModifCase(false)'>Décocher toutes les classes</a></p>\n";

			echo "<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$i;k++) {
			if(document.getElementById('tab_id_classe_'+k)){
				document.getElementById('tab_id_classe_'+k).checked = mode;
			}
		}
	}
</script>\n";

		}
	}
}
else{
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix des périodes</a>";
	echo "</p></div>\n";

	/*
	//$periode_num=1;
	$sql="SELECT DISTINCT num_periode FROM periodes ORDER BY num_periode";
	$res_per=mysql_query($sql);
	if(mysql_num_rows($res_per)==0){
		echo "<p>Il semble qu'aucune période ne soit encore définie.</p>\n";
	}
	else{
	*/
		echo "<h2>Recalcul des moyennes des conteneurs</h2>\n";

		check_token(false);

		echo "<p><a href=\"javascript:affiche_lig('affiche')\">Afficher toutes les lignes</a><br />\nOu <a 	href=\"javascript:affiche_lig('cache')\">n'afficher que les changements</a>.</p>\n";

		$numdiff=0;
		$i=0;
		$alt=1;
		//while($lig_per=mysql_fetch_object($res_per)){
		for($loop=0;$loop<count($num_periode);$loop++) {
			//$periode_num=$lig_per->num_periode;
			$periode_num=$num_periode[$loop];

			for($lloop=0;$lloop<count($id_classe);$lloop++) {
				$classe=get_class_from_id($id_classe[$lloop]);

				//$sql="SELECT ccn.*,c.classe,g.description FROM cn_cahier_notes ccn,groupes g,j_groupes_classes jgc,classes c WHERE
				$sql="SELECT DISTINCT ccn.id_cahier_notes,ccn.id_groupe FROM cn_cahier_notes ccn,groupes g,j_groupes_classes jgc,classes c WHERE
					ccn.id_groupe=g.id AND
					jgc.id_groupe=g.id AND
					c.id=jgc.id_classe AND
					ccn.periode='$periode_num' AND
					c.id='".$id_classe[$lloop]."'
					ORDER BY c.classe,g.description";
				//echo "$sql";
				$resultat=mysql_query($sql);
				if(mysql_num_rows($resultat)==0){
					//echo "<p>Il semble qu'aucun carnet de notes ne soit encore défini pour la période $periode_num.</p>\n";
					echo "<p>Il semble qu'aucun carnet de notes ne soit encore défini pour la période $periode_num et pour la classe $classe.</p>\n";
				}
				else{
					echo "<p style='font-weight:bold;'>Recalcul des moyennes pour la classe $classe sur la période $periode_num:</p>\n";
					echo "<table class='boireaus' border='1'>\n";
					//echo "<tr style='display:block;'>\n";
					echo "<tr>\n";
					/*
					echo "<td style='font-weight:bold; text-align:center;' width='33%'>Classe(s)</td>\n";
					echo "<td style='font-weight:bold; text-align:center;' width='33%'>Groupe</td>\n";
					//echo "<td>Moyenne initiale</td>\n";
					//echo "<td>Moyenne recalculée</td>\n";
					echo "<td style='font-weight:bold; text-align:center;' width='34%'>Différences</td>\n";
					*/
					echo "<th width='33%'>Classe(s)</th>\n";
					echo "<th width='33%'>Groupe</th>\n";
					//echo "<td>Moyenne initiale</td>\n";
					//echo "<td>Moyenne recalculée</td>\n";
					echo "<th width='34%'>Différences</th>\n";
					echo "</tr>\n";
					while($ligne=mysql_fetch_object($resultat)){
						$id_groupe=$ligne->id_groupe;
						$id_cahier_notes=$ligne->id_cahier_notes;
						$id_racine=$id_cahier_notes;

						//$sql="";
						//$res_classes=mysql_query();
						$current_group=get_group($id_groupe);

						/*
						echo "<tr>\n";
						echo "<td align='center'>".htmlentities($current_group["classlist_string"])."</td>\n";
						echo "<td align='center'>".htmlentities($current_group['description'])."</td>\n";
						echo "<td align='center'>\n";
						*/

						// Récupération de la liste des moyennes de conteneurs de ce groupe:
						unset($tabmoy1);
						unset($tabmoy2);
						$tabmoy1=array();
						$tabmoy2=array();
						$sql="SELECT cnc.login,cnc.note,cnc.statut,cnc.id_conteneur FROM cn_cahier_notes ccn,
											cn_conteneurs cc,
											cn_notes_conteneurs cnc WHERE
								ccn.id_cahier_notes=cc.id_racine AND
								cc.id=cnc.id_conteneur AND
								ccn.id_groupe='$id_groupe' AND
								ccn.periode='$periode_num'";
						$res_moy=mysql_query($sql);
						while($lig_moy=mysql_fetch_object($res_moy)){
							if($lig_moy->statut=="y"){
								//$tabmoy1["$lig_moy->login"]=$lig_moy->note;
								$tabmoy1["$lig_moy->login"]["$lig_moy->id_conteneur"]=array();
								$tabmoy1["$lig_moy->login"]["$lig_moy->id_conteneur"]=$lig_moy->note;
							}
						}


						//$current_group, $periode_num, $id_racine;
						// Recalcul:
						recherche_enfant($id_racine);


						$sql="SELECT cnc.login,cnc.note,cnc.statut,cnc.id_conteneur FROM cn_cahier_notes ccn,
											cn_conteneurs cc,
											cn_notes_conteneurs cnc WHERE
								ccn.id_cahier_notes=cc.id_racine AND
								cc.id=cnc.id_conteneur AND
								ccn.id_groupe='$id_groupe' AND
								ccn.periode='$periode_num'";
						$res_moy=mysql_query($sql);
						$chaine="";
						while($lig_moy=mysql_fetch_object($res_moy)){
							//$tabmoy2["$lig_moy->login"]=$lig_moy->note;
							$tabmoy2["$lig_moy->login"]["$lig_moy->id_conteneur"]=array();
							$tabmoy2["$lig_moy->login"]["$lig_moy->id_conteneur"]=$lig_moy->note;
							if(!isset($tabmoy1["$lig_moy->login"]["$lig_moy->id_conteneur"])){
								if($lig_moy->statut=="y"){
									//echo "$lig_moy->login (X -> ".$tabmoy2["$lig_moy->login"]["$lig_moy->id_conteneur"].")<br />\n";
									$chaine.="$lig_moy->login (X -> ".$tabmoy2["$lig_moy->login"]["$lig_moy->id_conteneur"].")<br />\n";
								}
							}
							else{
								if($tabmoy2["$lig_moy->login"]["$lig_moy->id_conteneur"]!=$tabmoy1["$lig_moy->login"]["$lig_moy->id_conteneur"]){
									//echo "$lig_moy->login (".$tabmoy1["$lig_moy->login"]["$lig_moy->id_conteneur"]." -> ".$tabmoy2["$lig_moy->login"]["$lig_moy->id_conteneur"].")<br />\n";
									$chaine.="$lig_moy->login (".$tabmoy1["$lig_moy->login"]["$lig_moy->id_conteneur"]." -> ".$tabmoy2["$lig_moy->login"]["$lig_moy->id_conteneur"].")<br />\n";
								}
							}
						}

						echo "<tr";
						if($chaine==""){
							echo " id='lig_".$i."'";
						}
						else{
							$numdiff++;
						}
						$alt=$alt*(-1);
						echo " class='lig$alt'";
						echo ">\n";
						echo "<td align='center' width='33%'>".htmlentities($current_group["classlist_string"])."</td>\n";
						echo "<td align='center' width='33%'>".htmlentities($current_group['description'])."</td>\n";
						echo "<td align='center' width='34%'>\n";
						echo $chaine."&nbsp;";
						echo "</td>\n";

			/*
						$sql="SELECT cnc.note FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
								ccn.id_cahier_notes=cc.id_racine AND
								cc.parent='0' AND
								cc.id=cnc.id_conteneur";
						$res_moy=mysql_query($sql);
						if(mysql_num_rows($res_moy)==0){
							echo "<td>-</td>\n";
						}
						else{
							$tab=mysql_fetch_array($res_moy));
						}
						echo "<td>".."</td>\n";
			*/
						echo "</tr>\n";
						$i++;
						flush();
					}
					echo "</table>\n";
					echo "<p>Terminé.</p>\n";
				}
			}
			//echo "<p>Terminé.</p>\n";
		}



		echo "<script type='text/javascript'>
	function affiche_lig(mode){
		for(i=0;i<$i;i++){
			//ligne=eval(\"document.getElementById('lig_\"+i+\"')\");
			chaine='lig_'+i
			if(eval(\"document.getElementById('\"+chaine+\"')\")){
				if(mode=='affiche'){
					//eval(\"document.getElementById('lig_\"+i+\"').display\")='block';
					//ligne.display='block';
					//document.getElementById(chaine).display='block';
					//eval(\"document.getElementById('\"+chaine+\"').display\")='block';
					//eval(\"document.getElementById('\"+chaine+\"').display='block'\");
					//document.getElementById('lig_'+i).style.display='block';
					document.getElementById('lig_'+i).style.display='';
				}
				else{
					//document.getElementById('lig_'+i).display='none';
					//eval(\"document.getElementById('lig_\"+i+\"').display\")='none';
					//ligne.display='none';
					//document.getElementById(chaine).display='none';
					//eval(\"document.getElementById('\"+chaine+\"').display\")='none';
					//eval(\"document.getElementById('\"+chaine+\"').display='none'\");
					document.getElementById('lig_'+i).style.display='none';
				}
			}
			else{
				if(i<10){
					alert('La ligne '+i+' n existe pas.');
				}
			}
		}
	}
</script>\n";

		if($numdiff==0){
			echo "<p>Aucune différence n'a été relevée.<br />\nVos moyennes de conteneurs étaient correctes.</p>";
		}
		else{
			echo "<p>Des différences ont été relevées et les moyennes ont été recalculées.<br />\nVos moyennes de conteneurs sont maitnenant correctes.</p>";
		}

		echo "<p><a href=\"javascript:affiche_lig('affiche')\">Afficher toutes les lignes</a><br />\nOu <a href=\"javascript:affiche_lig('cache')\">n'afficher que les changements</a>.</p>\n";
	//}
}
require("../lib/footer.inc.php");
?>