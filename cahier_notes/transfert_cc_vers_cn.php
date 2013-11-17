<?php
/**
 * 
 * @copyright Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @package Carnet_de_notes
 * @subpackage affichage
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

//$variables_non_protegees = 'yes';

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



$sql="SELECT 1=1 FROM droits WHERE id='/cahier_notes/transfert_cc_vers_cn.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_notes/transfert_cc_vers_cn.php',
administrateur='F',
professeur='V',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Transfert des evaluations-cumul vers le carnet de notes',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

//======================================================================================
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$msg="";

$id_racine=isset($_POST['id_racine']) ? $_POST['id_racine'] : (isset($_GET['id_racine']) ? $_GET['id_racine'] : NULL);
$id_dev_cc=isset($_POST['id_dev_cc']) ? $_POST['id_dev_cc'] : (isset($_GET['id_dev_cc']) ? $_GET['id_dev_cc'] : NULL);

$sql="SELECT * FROM cn_cahier_notes WHERE id_cahier_notes='$id_racine';";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<span style='color:red'>Le cahier de notes choisi est invalide.</span>\n";
	die();
}

if(!Verif_prof_cahier_notes ($_SESSION['login'],$id_racine)) {
	echo "<span style='color:red'>Le cahier de notes ne vous appartient pas.</span>\n";
	// AJOUTER tentative_intrusion()
	die();
}

$lig=mysqli_fetch_object($res);
$periode_num=$lig->periode;
$id_groupe=$lig->id_groupe;


@setlocale(LC_NUMERIC,'C');

require('cc_lib.php');



$sql="SELECT * FROM cc_dev WHERE id='$id_dev_cc';";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<span style='color:red'>Le $nom_cc choisi ($id_dev_cc) n'existe pas.</span>\n";
	die();
}
$lig=mysqli_fetch_object($res);
if($lig->id_groupe!=$id_groupe) {
	echo "<span style='color:red'>Le $nom_cc n°$id_dev_cc n'est pas associé au groupe n°$id_groupe.</span>\n";
	die();
}



// Le devoir du CN associé à l'évaluation-cumul
$id_cn_dev=$lig->id_cn_dev;
$nom_court_cc_dev=$lig->nom_court;
$nom_complet_cc_dev=$lig->nom_complet;
$description_cc_dev=$lig->description;
$precision_cc_dev=$lig->arrondir;

$current_group=get_group($id_groupe);
$designation_groupe=$current_group['name']." (".$current_group['description'].") en ".$current_group['classlist_string'];

//echo "\$current_group['classe']['ver_periode']['all'][$periode_num]=".$current_group["classe"]["ver_periode"]["all"][$periode_num]."<br />";
if($current_group["classe"]["ver_periode"]["all"][$periode_num] != 3) {
	echo "<span style='color:red'>La période $periode_num n'est pas ouverte en saisie pour une des classes associées au groupe.</span>\n";
	die();
}

if((isset($_GET['creer_dev']))||(isset($_GET['ecraser_contenu_dev']))) {
	check_token();
	$id_conteneur=$_GET['id_conteneur'];

	$reg_ok="yes";

	if(isset($_GET['ecraser_contenu_dev'])) {
		$id_devoir_cn=$_GET['id_devoir_cn'];
		// Contrôler que le devoir appartient bien au prof, que le cn est bien $id_racine et que la période est ouverte

		$sql="SELECT * FROM cn_cahier_notes ccn, cn_devoirs cd WHERE cd.id_racine=ccn.id_cahier_notes AND cd.id='$id_devoir_cn';";
		$res_dev=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_dev)==0) {
			echo "<span style='color:red'>Le devoir n°$id_devoir_cn n'existe pas.</span>\n";
			die();
		}
		$lig_dev=mysqli_fetch_object($res_dev);
		if($lig_dev->id_groupe!=$id_groupe) {
			echo "<span style='color:red'>Le devoir n°$id_devoir_cn n'est pas associé au groupe n°$id_groupe (".$designation_groupe.").</span>\n";
			die();
		}
	}
	else {
		// Créer un nouveau dev...
		// Prévoir par la suite de pouvoir définir ici les paramètres
		$sql="INSERT INTO cn_devoirs SET id_racine='$id_racine', id_conteneur='$id_conteneur', nom_court='nouveau', ramener_sur_referentiel='F', note_sur='20';";
		//echo "$sql<br />";
		$reg=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$reg) {
			$msg.="Erreur lors de la création du devoir pour l'enseignement associé au cahier de notes n°$current_id_cn.<br />";
			$reg_ok="no";
		}
		else {
			$id_devoir_cn=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

			$sql="UPDATE cn_devoirs SET nom_court='".corriger_caracteres($nom_court_cc_dev)."' WHERE id='$id_devoir_cn'";
			$reg=mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$reg) {$reg_ok = "no";}
	
			$sql="UPDATE cn_devoirs SET nom_complet='".corriger_caracteres($nom_complet_cc_dev)."' WHERE id='$id_devoir_cn'";
			$reg=mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$reg) {$reg_ok = "no";}
	
			if($description_cc_dev!='')  {
				$sql="UPDATE cn_devoirs SET nom_complet='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $description_cc_dev) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' WHERE id='$id_devoir_cn'";
				$reg=mysqli_query($GLOBALS["mysqli"], $sql);
				if (!$reg) {$reg_ok = "no";}
			}
	
			$tmp_coef=1;
			$sql="UPDATE cn_devoirs SET coef='$tmp_coef' WHERE id='$id_devoir_cn'";
			$reg=mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$reg) {$reg_ok = "no";}

			$sql="UPDATE cn_devoirs SET date='".strftime('%Y-%m-%d 00:00:00')."' WHERE id='$id_devoir_cn'";
			$reg=mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$reg) {$reg_ok = "no";}

			$sql="UPDATE cn_devoirs SET date_ele_resp='".strftime('%Y-%m-%d 00:00:00')."' WHERE id='$id_devoir_cn'";
			$reg=mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$reg) {$reg_ok = "no";}
	
			$sql="UPDATE cn_devoirs SET facultatif='O' WHERE id='$id_devoir_cn'";
			$reg=mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$reg) {$reg_ok = "no";}
	
			$sql="UPDATE cn_devoirs SET display_parents='1' WHERE id='$id_devoir_cn'";
			$reg=mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$reg) {$reg_ok = "no";}
	
		}
	}

	if($reg_ok=="yes") {
		$succes_insert_note=0;
		$erreur_insert_note=0;

		// Transférer les notes
		$sql="SELECT * FROM cc_eval WHERE id_dev='$id_dev_cc' ORDER BY date, nom_court, nom_complet;";
		//echo "$sql<br />";
		$res_eval=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_eval)==0) {
			$msg="Aucune évaluation n'est associée au $nom_cc n°$id_dev_cc<br />";
		}
		else {
			$cpt=0;
			$tab_eval=array();
			$tab_ele=array();
	
			while($lig_eval=mysqli_fetch_object($res_eval)) {
	
				$tab_eval[$cpt]['id_eval']=$lig_eval->id;
				$tab_eval[$cpt]['note_sur']=$lig_eval->note_sur;
	
				$sql="SELECT cc.* FROM cc_notes_eval cc WHERE cc.id_eval='$lig_eval->id' ORDER BY cc.login;";
				//echo "$sql<br />";
				$res_en=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_en)>0) {
					while($lig_en=mysqli_fetch_object($res_en)) {
	
						if($lig_en->statut=='v') {
							$tab_ele[$lig_en->login]['eval'][$lig_eval->id]="";
						}
						elseif($lig_en->statut!='') {
							$tab_ele[$lig_en->login]['eval'][$lig_eval->id]=$lig_en->statut;
						}
						else {
							$tab_ele[$lig_en->login]['eval'][$lig_eval->id]=$lig_en->note;
						}
					}
				}
	
				$cpt++;
			}
	
	
			foreach($tab_ele as $ele_login => $tmp_tab) {
				$total=0;
				$total_sur=0;
				$chaine_commentaire="";
				$chaine_commentaire_part1="";
				$chaine_commentaire_part2="";

				$sql="DELETE FROM cn_notes_devoirs WHERE id_devoir='$id_devoir_cn' AND login='$ele_login';";
				$menage=mysqli_query($GLOBALS["mysqli"], $sql);

				$sql="INSERT INTO cn_notes_devoirs SET id_devoir='$id_devoir_cn', login='$ele_login'";

				for($i=0;$i<count($tab_eval);$i++) {
					if(isset($tmp_tab['eval'][$tab_eval[$i]['id_eval']])) {
/*
						if($chaine_commentaire=="") {
							$chaine_commentaire="(";
						}
						else {
						}
*/

						if($tmp_tab['eval'][$tab_eval[$i]['id_eval']]!='') {
							if(($tmp_tab['eval'][$tab_eval[$i]['id_eval']]!='')&&(preg_match('/^[0-9.]*$/',$tmp_tab['eval'][$tab_eval[$i]['id_eval']]))) {
								$total+=$tmp_tab['eval'][$tab_eval[$i]['id_eval']];
								$chaine_commentaire_part1.="+".$tmp_tab['eval'][$tab_eval[$i]['id_eval']];

								$total_sur+=$tab_eval[$i]['note_sur'];
								$chaine_commentaire_part2.="+".$tab_eval[$i]['note_sur'];
							}
							else {
								$chaine_commentaire_part1.="+".$tmp_tab['eval'][$tab_eval[$i]['id_eval']];
								$chaine_commentaire_part2.="+".$tab_eval[$i]['note_sur'];
							}
						}
					}
				}

				if($chaine_commentaire_part1!='') {
					//if(preg_match("/\+/", $chaine_commentaire_part1)) {
					if(strstr(substr($chaine_commentaire_part1,1),'+')!='') {
						$chaine_commentaire="(".substr($chaine_commentaire_part1,1).")/";
					}
					else {
						$chaine_commentaire=substr($chaine_commentaire_part1,1)."/";
					}
				}
				if($chaine_commentaire_part2!='') {
					if(preg_match("/\+/", substr($chaine_commentaire_part2,1))) {
						$chaine_commentaire.="(".substr($chaine_commentaire_part2,1).")";
					}
					else {
						$chaine_commentaire.=substr($chaine_commentaire_part2,1);
					}
				}

				//$csv.=strtr($total,'.',',').";".strtr($total_sur,'.',',').";";
				if($total_sur>0) {
					$moy=precision_arrondi(20*$total/$total_sur,$precision_cc_dev);
					/*
					echo "\$total=$total<br />";
					echo "\$total_sur=$total_sur<br />";
					echo "$precision_cc_dev<br />";
					$tmp_moy=20*$total/$total_sur;
					echo "20*$total/$total_sur=".$tmp_moy." arrondi à $moy<br />";
					*/
					$sql.=", note='$moy', comment='$chaine_commentaire', statut='';";
				}
				else {
					$moy='-';

					$sql.=", note='', comment='$chaine_commentaire', statut='$moy';";
				}
				//$csv.="$moy;\r\n";
				//echo "$sql<br />";
				//echo "<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if($insert) {
					$succes_insert_note++;
				}
				else {
					$erreur_insert_note++;
				}
			}

		}
		// Mise à jour des moyennes de conteneurs
		recherche_enfant($id_racine);


		if($erreur_insert_note>0) {
			$msg.="Erreur lors de l'enregistrement dans le carnet de notes.<br />";
		}
		elseif($succes_insert_note>0) {
			$msg.="Enregistrement dans le carnet de notes effectué.<br />";
		}

		$sql="UPDATE cc_dev SET id_cn_dev='$id_devoir_cn' WHERE id='$id_dev_cc';";
		$update=mysqli_query($GLOBALS["mysqli"], $sql);

		$id_cn_dev=$id_devoir_cn;

		$msg.="$nom_cc rattachée à l'évaluation ".get_infos_devoir($id_devoir_cn)." (n°$id_devoir_cn) du carnet de notes.<br />";
	}
}



/**
 * Affichage de la liste des conteneurs
 *
 * @global array 
 * @global text 
 * @global int 
 * @global int 
 * @param int $id_conteneur Id du conteneur
 * @param int $periode_num Numéro de la période
 * @return text no si le conteneur contient des notes, yes sinon
 * @see getSettingValue()
 * @see add_token_in_url()
 */
function liste_devoirs_conteneurs($id_dev_cc, $id_conteneur, $periode_num) {
	global $gepiClosedPeriodLabel, $id_groupe, $id_cn_dev, $id_racine;

	// A FAIRE: Tester si la période est ouverte
	//          Si ce n'est pas le cas, refuser le transfert.

	//
	// Cas particulier de la racine
	$gepi_denom_boite=getSettingValue("gepi_denom_boite");

	$sql="SELECT * FROM cn_conteneurs WHERE (parent='0' and id_racine='$id_conteneur')";
	$appel_conteneurs = mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_cont = mysqli_num_rows($appel_conteneurs);
	if ($nb_cont != 0) {
		echo "<ul>\n";
		$id_cont = mysql_result($appel_conteneurs, 0, 'id');
		$id_parent = mysql_result($appel_conteneurs, 0, 'parent');
		//$id_racine = mysql_result($appel_conteneurs, 0, 'id_racine');
		$nom_conteneur = mysql_result($appel_conteneurs, 0, 'nom_court');
		echo "<li>\n";
		echo "$nom_conteneur ";
		/*
		if ($ver_periode <= 1) {
			echo " (<strong>".$gepiClosedPeriodLabel."</strong>) ";
		}
		*/

		echo "- <a href='transfert_cc_vers_cn.php?id_dev_cc=$id_dev_cc&amp;id_racine=$id_racine&amp;id_conteneur=$id_cont&amp;creer_dev=y".add_token_in_url()."'>Créer une nouvelle évaluation dans ce conteneur</a>\n";
		$appel_dev = mysqli_query($GLOBALS["mysqli"], "select * from cn_devoirs where id_conteneur='$id_cont' order by date");
		$nb_dev  = mysqli_num_rows($appel_dev);
		if ($nb_dev != 0) {$empty = 'no';}
		//if ($ver_periode >= 2) {
			$j = 0;
			if($nb_dev>0){
				echo "<ul>\n";
				while ($j < $nb_dev) {

					$nom_devoir_cn = mysql_result($appel_dev, $j, 'nom_court');
					$id_devoir_cn = mysql_result($appel_dev, $j, 'id');
					echo "<li>\n";
					echo "<font color='green'>$nom_devoir_cn</font>";
					echo " - <a href='transfert_cc_vers_cn.php?id_dev_cc=$id_dev_cc&amp;id_racine=$id_racine&amp;id_conteneur=$id_cont&amp;id_devoir_cn=$id_devoir_cn&amp;ecraser_contenu_dev=y".add_token_in_url()."' onclick=\"return confirm('Vous allez remplacer le contenu de cette évaluation. Etes-vous sûr?')\">Utiliser ce devoir</a>";

					echo " (n°$id_devoir_cn)";

					if($id_devoir_cn==$id_cn_dev) {
						echo " <img src='../images/enabled.png' width='20' height='20' title='Devoir précédemment associé' />";
					}

					echo " - <a href='saisie_notes.php?id_conteneur=$id_cont&amp;id_devoir=$id_devoir_cn'><img src='../images/icons/chercher.png' width='16' height='16' title='Voir cette évaluation du carnet de notes' alt='Voir cette évaluation du carnet de notes' /></a>";

					// A FAIRE: Ajouter une infobulle avec le détail de ce devoir... avec le contenu (les notes et commentaires?)

					/*
					$sql="SELECT 1=1 FROM cn_notes_devoirs cnd, j_eleves_classes jec WHERE cnd.id_devoir='$id_dev' AND cnd.statut!='v' AND jec.login=cnd.login AND jec.periode='$periode_num';";
					$res_eff_dev=mysql_query($sql);
					$eff_dev=mysql_num_rows($res_eff_dev);
					echo " <span title=\"Effectif des notes saisies/effectif total de l'enseignement\" style='font-size:small;";
					if(isset($eff_groupe)) {if($eff_dev==$eff_groupe) {echo "color:green;";} else {echo "color:red;";}}
					echo "'>($eff_dev";
					if(isset($eff_groupe)) {echo "/$eff_groupe";}
					echo ")</span>";
					*/
					$j++;
				}
				echo "</ul>\n";
			}
		//}
	}

	//if ($ver_periode >= 2) {
		$appel_conteneurs = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_conteneurs WHERE (parent='$id_conteneur') order by nom_court");
		$nb_cont = mysqli_num_rows($appel_conteneurs);
		if($nb_cont>0) {
			echo "<ul>\n";
			$i = 0;
			while ($i < $nb_cont) {
				$id_cont = mysql_result($appel_conteneurs, $i, 'id');
				$id_parent = mysql_result($appel_conteneurs, $i, 'parent');
				//$id_racine = mysql_result($appel_conteneurs, $i, 'id_racine');
				$nom_conteneur = mysql_result($appel_conteneurs, $i, 'nom_court');
				if ($id_cont != $id_parent) {
					echo "<li>\n";
					echo "$nom_conteneur - <a href='transfert_cc_vers_cn.php?id_dev_cc=$id_dev_cc&amp;id_racine=$id_racine&amp;id_conteneur=$id_cont&amp;creer_dev=y".add_token_in_url()."'>Créer une nouvelle évaluation dans ce conteneur</a>\n";

					$display_bulletin=mysql_result($appel_conteneurs, $i, 'display_bulletin');
					$coef=mysql_result($appel_conteneurs, $i, 'coef');
					echo " (<i><span title='Coefficient $coef'>$coef</span> ";
					if($display_bulletin==1) {echo "<img src='../images/icons/visible.png' width='19' height='16' title='$gepi_denom_boite visible sur le bulletin' alt='$gepi_denom_boite visible sur le bulletin' />";}
					else {echo " <img src='../images/icons/invisible.png' width='19' height='16' title='$gepi_denom_boite non visible sur le bulletin' alt='$gepi_denom_boite non visible sur le bulletin' />\n";}
					echo "</i>)";

					$appel_dev = mysqli_query($GLOBALS["mysqli"], "select * from cn_devoirs where id_conteneur='$id_cont' order by date");
					$nb_dev  = mysqli_num_rows($appel_dev);
					if ($nb_dev != 0) {$empty = 'no';}

					// Existe-t-il des sous-conteneurs?
					$sql="SELECT 1=1 FROM cn_conteneurs WHERE (parent='$id_cont')";
					$test_sous_cont=mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_sous_cont=mysqli_num_rows($test_sous_cont);

					$j = 0;
					if($nb_dev>0) {
						echo "<ul>\n";
						while ($j < $nb_dev) {
							$nom_devoir_cn = mysql_result($appel_dev, $j, 'nom_court');
							$id_devoir_cn = mysql_result($appel_dev, $j, 'id');
							echo "<li>\n";
							echo "<font color='green'>$nom_devoir_cn</font>";
							echo " - <a href='transfert_cc_vers_cn.php?id_dev_cc=$id_dev_cc&amp;id_racine=$id_racine&amp;id_conteneur=$id_cont&amp;id_devoir=$id_devoir_cn&amp;ecraser_contenu_dev=y".add_token_in_url()."' onclick=\"return confirmlink(this, 'Vous allez remplacer le contenu de cette évaluation. Etes-vous sûr?')\">Utiliser ce devoir</a>";
							echo " (n°$id_devoir_cn)";
							/*
							$sql="SELECT 1=1 FROM cn_notes_devoirs cnd, j_eleves_classes jec WHERE cnd.id_devoir='$id_dev' AND cnd.statut!='-' AND cnd.statut!='v' AND jec.login=cnd.login AND jec.periode='$periode_num';";
							$res_eff_dev=mysql_query($sql);
							$eff_dev=mysql_num_rows($res_eff_dev);
							echo " <span title=\"Effectif des notes saisies/effectif total de l'enseignement\" style='font-size:small;";
							if(isset($eff_groupe)) {if($eff_dev==$eff_groupe) {echo "color:green;";} else {echo "color:red;";}}
							echo "'>($eff_dev";
							if(isset($eff_groupe)) {echo "/$eff_groupe";}
							echo ")</span>";
							*/
							echo " - <a href='saisie_notes.php?id_conteneur=$id_cont&amp;id_devoir=$id_devoir_cn'><img src='../images/icons/chercher.png' width='16' height='16' title='Voir cette évaluation du carnet de notes' alt='Voir cette évaluation du carnet de notes' /></a>";
							echo "</li>\n";
							$j++;
						}
						echo "</ul>\n";
					}
				}
				if ($id_conteneur != $id_cont) {liste_devoirs_conteneurs($id_dev_cc, $id_cont,$periode_num);}
				if ($id_cont != $id_parent) {
					echo "</li>\n";
				}
				$i++;
			}
			echo "</ul>\n";
		}
	//}
	if (isset ($empty) && $empty != 'no') {return 'yes';}
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Carnet de notes - Transfert de $nom_cc";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

echo "<a href=\"index_cc.php?id_racine=$id_racine\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>";

if(!isset($id_dev_cc)) {
	echo "</p>\n";

	echo "<p>Aucun $nom_cc n'a été choisi.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//==================================================================

echo "</p>\n";

echo "<p class='bold'>$nom_cc n°$id_dev_cc</p>\n";

$sql="SELECT * FROM cc_dev WHERE id='$id_dev_cc';";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>Le $nom_cc choisi (<i>$id_dev_cc</i>) n'existe pas.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

$lig=mysqli_fetch_object($res);

echo "<blockquote>\n";
echo "<p><b>".$lig->nom_court."</b> (<em>".$lig->nom_complet."</em>)<br />\n";
if($lig->description!='') {
	echo nl2br(trim($lig->description))."<br />\n";
}
else {
	echo "Pas de description saisie.<br />\n";
}
echo "</blockquote>\n";

echo "<p>Sélectionnez où vous souhaitez créer une évaluation dans votre carnet de notes et y transférer votre $nom_cc.</p>\n";


liste_devoirs_conteneurs($id_dev_cc, $id_racine, $periode_num);


require("../lib/footer.inc.php");
die();
?>
