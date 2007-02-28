<?php
/*
* Last modification  : 15/11/2006
*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
function recherche_enfant($id_parent_tmp){
	global $current_group, $periode_num, $id_racine;
	$sql="SELECT * FROM cn_conteneurs WHERE parent='$id_parent_tmp'";
	//echo "<!-- $sql -->\n";
	$res_enfant=mysql_query($sql);
	if(mysql_num_rows($res_enfant)>0){
		while($lig_conteneur_enfant=mysql_fetch_object($res_enfant)){
			recherche_enfant($lig_conteneur_enfant->id);
		}
	}
	else{
		$arret = 'no';
		$id_conteneur_enfant=$id_parent_tmp;
		// Mise_a_jour_moyennes_conteneurs pour un enfant non parent...
		mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_conteneur_enfant,$arret);
		//echo "<!-- ========================================== -->\n";
	}
}
//recherche_enfant($id_racine);


//**************** EN-TETE *****************
$titre_page = "Carnet de notes - Correction des moyennes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<div class='norme'><p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <a href='index.php'>Retour page précédente</a></p></div>\n";

if(!isset($_POST['recalculer'])){
	echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method='post'>\n";
	echo "<p class='bold'>Cette page est destinée à effectuer le recalcul des moyennes de conteneurs.<br />Un bug pouvait provoquer une erreur lors de déplacement de devoirs/conteneurs(boites) d'un conteneur(boite) à un autre.<br />\n";
	echo "<input type=\"submit\" name='recalculer' value=\"Recalculer\" style=\"font-variant: small-caps;\" /></p>\n";
	echo "</form>\n";
}
else{
	//$periode_num=1;
	$sql="SELECT DISTINCT num_periode FROM periodes ORDER BY num_periode";
	$res_per=mysql_query($sql);
	if(mysql_num_rows($res_per)==0){
		echo "<p>Il semble qu'aucune période ne soit encore définie.</p>\n";
	}
	else{
		echo "<h2>Recalcul des moyennes des conteneurs</h2>\n";

		echo "<p><a href=\"javascript:affiche_lig('affiche')\">Afficher toutes les lignes</a><br />\nOu <a 	href=\"javascript:affiche_lig('cache')\">n'afficher que les changements</a>.</p>\n";

		$numdiff=0;
		$i=0;
		while($lig_per=mysql_fetch_object($res_per)){
			$periode_num=$lig_per->num_periode;
			//$sql="SELECT ccn.*,c.classe,g.description FROM cn_cahier_notes ccn,groupes g,j_groupes_classes jgc,classes c WHERE
			$sql="SELECT DISTINCT ccn.id_cahier_notes,ccn.id_groupe FROM cn_cahier_notes ccn,groupes g,j_groupes_classes jgc,classes c WHERE
				ccn.id_groupe=g.id AND
				jgc.id_groupe=g.id AND
				c.id=jgc.id_classe AND
				ccn.periode='$periode_num'
				ORDER BY c.classe,g.description";
			//echo "$sql";
			$resultat=mysql_query($sql);
			if(mysql_num_rows($resultat)==0){
				echo "<p>Il semble qu'aucun carnet de notes ne soit encore défini.</p>\n";
			}
			else{
				echo "<p style='font-weight:bold;'>Recalcul des moyennes pour la période $periode_num:</p>\n";
				echo "<table border='1'>\n";
				//echo "<tr style='display:block;'>\n";
				echo "<tr>\n";
				echo "<td style='font-weight:bold; text-align:center;' width='33%'>Classe(s)</td>\n";
				echo "<td style='font-weight:bold; text-align:center;' width='33%'>Groupe</td>\n";
				//echo "<td>Moyenne initiale</td>\n";
				//echo "<td>Moyenne recalculée</td>\n";
				echo "<td style='font-weight:bold; text-align:center;' width='34%'>Différences</td>\n";
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
	}
}
require("../lib/footer.inc.php");
?>