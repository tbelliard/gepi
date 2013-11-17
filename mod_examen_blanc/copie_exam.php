<?php
/*
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
}


$sql="SELECT 1=1 FROM droits WHERE id='/mod_examen_blanc/copie_exam.php';";
$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_examen_blanc/copie_exam.php',
administrateur='V',
professeur='V',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Examen blanc: Copie',
statut='';";
$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
}




//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

include('lib_exb.php');

//=========================================================

$id_exam=isset($_POST['id_exam']) ? $_POST['id_exam'] : (isset($_GET['id_exam']) ? $_GET['id_exam'] : NULL);
if(!preg_match('/^[0-9]+$/', $id_exam)) {
	header("Location: index.php?msg=".rawurlencode("Aucun id_exam n'a été choisi."));
	die();
}

//$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);
//$modif_exam=isset($_POST['modif_exam']) ? $_POST['modif_exam'] : (isset($_GET['modif_exam']) ? $_GET['modif_exam'] : NULL);

$acces_mod_exb_prof="n";
if($_SESSION['statut']=='professeur') {

	if(!is_pp($_SESSION['login'])) {
		// A FAIRE: AJOUTER UN tentative_intrusion()...
		header("Location: ../logout.php?auto=1");
		die();
	}

	if(getSettingValue('modExbPP')!='yes') {
		// A FAIRE: AJOUTER UN tentative_intrusion()...
		header("Location: ../logout.php?auto=1");
		die();
	}

	if((isset($id_exam))&&(!is_pp_proprio_exb($id_exam))) {
		header("Location: ../accueil.php?msg=".rawurlencode("Vous n'êtes pas propriétaire de l'examen blanc n°$id_exam."));
		die();
	}

	$acces_mod_exb_prof="y";
}

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($acces_mod_exb_prof=='y')) {
	$id_exam_modele=isset($_POST['id_exam_modele']) ? $_POST['id_exam_modele'] : NULL;

	if(!preg_match('/^[0-9]+$/', $id_exam_modele)) {
		unset($id_exam_modele);
	}

	if(isset($id_exam_modele)) {
		check_token();

		$msg="";
		$nb_err=0;
		$nb_reg=0;

		$copier_classes=isset($_POST['copier_classes']) ? $_POST['copier_classes'] : "n";
		$copier_matieres=isset($_POST['copier_matieres']) ? $_POST['copier_matieres'] : "n";
		$copier_groupes=isset($_POST['copier_groupes']) ? $_POST['copier_groupes'] : "n";
		$copier_coef=isset($_POST['copier_coef']) ? $_POST['copier_coef'] : "n";
		$vider_param_anterieurs=isset($_POST['vider_param_anterieurs']) ? $_POST['vider_param_anterieurs'] : "n";

		if($copier_groupes=="y") {$copier_classes="y";}

		$sql="SELECT * FROM ex_examens WHERE id='$id_exam_modele';";
		//echo "$sql<br />\n";
		$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res)==0) {
			$msg="L'examen modèle choisi (<i>$id_exam_modele</i>) n'existe pas.\n";
		}
		else {
			//$lig=mysql_fetch_object($res);

			if($copier_classes=="y") {
				if($vider_param_anterieurs=="y") {
					$sql="DELETE FROM ex_classes WHERE id_exam='$id_exam';";
					//echo "$sql<br />\n";
					$del=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(!$del) {
						$msg.="Erreur lors du nettoyage des classes.<br />\n";
						$nb_err++;
					}
				}

				$sql="SELECT * FROM ex_classes WHERE id_exam='$id_exam_modele';";
				//echo "$sql<br />\n";
				$res_modele=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_modele)>0) {
					while($lig_modele=mysqli_fetch_object($res_modele)) {
						$sql="SELECT 1=1 FROM ex_classes WHERE id_exam='$id_exam' AND id_classe='$lig_modele->id_classe';";
						//echo "$sql<br />\n";
						$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($test)==0) {
							$sql="INSERT INTO ex_classes SET id_exam='$id_exam', id_classe='$lig_modele->id_classe';";
							//echo "$sql<br />\n";
							$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
							if(!$insert) {
								$msg.="Erreur lors de l'association avec la classe ".get_nom_classe($lig_modele->id_classe).".<br />\n";
								$nb_err++;
							}
							else {
								$nb_reg++;
							}
						}
					}
				}
			}

			if($copier_matieres=="y") {
				if($vider_param_anterieurs=="y") {
					$sql="DELETE FROM ex_matieres WHERE id_exam='$id_exam';";
					//echo "$sql<br />\n";
					$del=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(!$del) {
						$msg.="Erreur lors du nettoyage des matières.<br />\n";
						$nb_err++;
					}
				}

				$sql="SELECT * FROM ex_matieres WHERE id_exam='$id_exam_modele';";
				//echo "$sql<br />\n";
				$res_modele=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_modele)>0) {
					while($lig_modele=mysqli_fetch_object($res_modele)) {
						$sql="SELECT 1=1 FROM ex_matieres WHERE id_exam='$id_exam' AND matiere='$lig_modele->matiere';";
						//echo "$sql<br />\n";
						$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($test)==0) {
							$sql="INSERT INTO ex_matieres SET id_exam='$id_exam', matiere='$lig_modele->matiere'";
							if($copier_coef=='y') {$sql.=", coef='$lig_modele->coef', bonus='$lig_modele->bonus'";}
							else {$sql.=", coef='1.0', bonus='n'";}
							$sql.=";";
							//echo "$sql<br />\n";
							$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
							if(!$insert) {
								$msg.="Erreur lors de l'association avec la matière ".$lig_modele->matiere."<br />\n";
								$nb_err++;
							}
							else {
								$nb_reg++;
							}
						}
					}
				}
			}

			if($copier_coef=="y") {
				$sql="SELECT * FROM ex_matieres WHERE id_exam='$id_exam_modele';";
				//echo "$sql<br />\n";
				$res_modele=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_modele)>0) {
					while($lig_modele=mysqli_fetch_object($res_modele)) {
						$sql="SELECT 1=1 FROM ex_matieres WHERE id_exam='$id_exam' AND matiere='$lig_modele->matiere';";
						//echo "$sql<br />\n";
						$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($test)>0) {
							$sql="UPDATE ex_matieres SET coef='$lig_modele->coef', bonus='$lig_modele->bonus' WHERE id_exam='$id_exam' AND matiere='$lig_modele->matiere';";
							//echo "$sql<br />\n";
							$update=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
							if(!$update) {
								$msg.="Erreur lors de la mise à jour des coefficients et bonus de la matière ".$lig_modele->matiere."<br />\n";
								$nb_err++;
							}
							else {
								$nb_reg++;
							}
						}
					}
				}
			}

			if($copier_groupes=="y") {
				if($vider_param_anterieurs=="y") {
					$sql="DELETE FROM ex_groupes WHERE id_exam='$id_exam';";
					//echo "$sql<br />\n";
					$del=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(!$del) {
						$msg.="Erreur lors du nettoyage des groupes.<br />\n";
						$nb_err++;
					}
				}

				$sql="SELECT * FROM ex_groupes WHERE id_exam='$id_exam_modele';";
				//echo "$sql<br />\n";
				$res_modele=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_modele)>0) {
					while($lig_modele=mysqli_fetch_object($res_modele)) {
						$sql="SELECT 1=1 FROM ex_groupes WHERE id_exam='$id_exam' AND id_groupe='$lig_modele->id_groupe';";
						//echo "$sql<br />\n";
						$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($test)==0) {
							$sql="INSERT INTO ex_groupes SET id_exam='$id_exam', id_groupe='$lig_modele->id_groupe', matiere='$lig_modele->matiere', type='$lig_modele->type', id_dev='$lig_modele->id_dev', valeur='$lig_modele->valeur';";
							//echo "$sql<br />\n";
							$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
							if(!$insert) {
								$msg.="Erreur lors de l'association avec le groupe n°".$lig_modele->id_groupe."<br />\n";
								$nb_err++;
							}
							else {
								$nb_reg++;
							}
						}
					}
				}
			}
		}
		if(($nb_reg>0)&&($nb_err==0)) {$msg.="Copie effectuée.";}
		header("Location: index.php?id_exam=$id_exam&mode=modif_exam&msg=".rawurlencode($msg));
		die();

	}
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$javascript_specifique='mod_examen_blanc/lib_exb';
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Examen blanc: Copie";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

//echo "<div class='noprint'>\n";
//echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
echo "<p class='bold'><a href='index.php?id_exam=$id_exam&amp;mode=modif_exam'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">Retour</a>";
//echo "</p>\n";
//echo "</div>\n";

//include("../lib/calendrier/calendrier.class.php");

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($acces_mod_exb_prof=="y")) {

	echo "</p>\n";

	$sql="SELECT * FROM ex_examens WHERE id!='$id_exam' ORDER BY date, intitule;";
	$res_autres_exam=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res_autres_exam)==0) {
		echo "<p style='color:red'>Aucun autre examen blanc n'est enregistré.</p>\n";

		echo "<p><br /></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$cpt=0;
	$tab_exam=array();
	while($lig_autres_exam=mysqli_fetch_object($res_autres_exam)) {
		if(($_SESSION['statut']!='professeur')||
			(($_SESSION['statut']=='professeur')&&(is_pp_proprio_exb($lig_autres_exam->id)))) {
			$tab_exam[$cpt]=array();
			$tab_exam[$cpt]['id']=$lig_autres_exam->id;
			$tab_exam[$cpt]['intitule']=$lig_autres_exam->intitule;
			$tab_exam[$cpt]['description']=$lig_autres_exam->description;
			$tab_exam[$cpt]['date']=$lig_autres_exam->date;
			$tab_exam[$cpt]['etat']=$lig_autres_exam->etat;
			$cpt++;
		}
	}

	if(count($tab_exam)==0) {
		echo "<p style='color:red'>Vous n'avez aucun autre examen blanc enregistré.</p>\n";

		echo "<p><br /></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
	echo "<p class='bold'>Copier les paramètres de l'examen&nbsp;:</p>\n";
	echo "<table class='boireaus' summary='Tableau des examens blancs'>\n";
	echo "<tr>\n";
	echo "<th>Choix</th>\n";
	echo "<th>Id</th>\n";
	echo "<th>Intitule</th>\n";
	echo "<th>Description</th>\n";
	echo "<th>Date</th>\n";
	echo "<th>Etat</th>\n";
	echo "<th>Détails</th>\n";
	echo "</tr>\n";
	$alt=1;
	$nb_exam=count($tab_exam);
	$chaine_tab_id_exam="";
	for($i=0;$i<$nb_exam;$i++) {
		$alt=$alt*(-1);
		if($chaine_tab_id_exam!="") {$chaine_tab_id_exam.=", ";}
		$chaine_tab_id_exam.="'".$tab_exam[$i]['id']."'";
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td><input type='radio' name='id_exam_modele' id='id_exam_modele_".$tab_exam[$i]['id']."' value='".$tab_exam[$i]['id']."' onchange=\"checkbox_change2('id_exam_modele_',".$tab_exam[$i]['id'].");changement()\" /></td>\n";
		echo "<td><label for='id_exam_modele_".$tab_exam[$i]['id']."'>".$tab_exam[$i]['id']."</label></td>\n";
		echo "<td><label for='id_exam_modele_".$tab_exam[$i]['id']."' id='texte_id_exam_modele_".$tab_exam[$i]['id']."'>".$tab_exam[$i]['intitule']."</label></td>\n";
		echo "<td><label for='id_exam_modele_".$tab_exam[$i]['id']."'>".$tab_exam[$i]['description']."</label></td>\n";
		echo "<td><label for='id_exam_modele_".$tab_exam[$i]['id']."'>".formate_date($tab_exam[$i]['date'])."</label></td>\n";
		echo "<td><label for='id_exam_modele_".$tab_exam[$i]['id']."'>".$tab_exam[$i]['etat']."</label></td>\n";
		echo "<td>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";

	echo "<p class='bold'>Quels paramètres copier&nbsp;?</p>\n";
	echo "<p>\n";
	echo "<input type='checkbox' name='copier_classes' id='copier_classes' value='y' onchange=\"checkbox_change('copier_classes');changement()\" /><label for='copier_classes' id='texte_copier_classes'>Classes</label><br />\n";
	echo "<input type='checkbox' name='copier_matieres' id='copier_matieres' value='y' onchange=\"checkbox_change('copier_matieres');changement()\" /><label for='copier_matieres' id='texte_copier_matieres'>Matières</label><br />\n";
	echo "<input type='checkbox' name='copier_groupes' id='copier_groupes' value='y' onchange=\"checkbox_change('copier_groupes');changement()\" /><label for='copier_groupes' id='texte_copier_groupes'>Enseignements (<i>groupes</i>)</label><br />\n";
	echo "<input type='checkbox' name='copier_coef' id='copier_coef' value='y' onchange=\"checkbox_change('copier_coef');changement()\" /><label for='copier_coef' id='texte_copier_coef'>Coefficients et bonus</label><br />\n";
	//echo "<input type='checkbox' name='' id='' value='y' /><label for=''></label><br />\n";
	echo "</p>\n";

	echo "<p><input type='checkbox' name='vider_param_anterieurs' id='vider_param_anterieurs' value='y' onchange=\"checkbox_change('vider_param_anterieurs');changement()\" /><label for='vider_param_anterieurs' id='texte_vider_param_anterieurs'>Vider les éventuelles sélections antérieures de classes, groupes, matières,... de l'examen blanc n°$id_exam</label></p>\n";

	echo "<input type='hidden' name='id_exam' value='$id_exam' />\n";
	echo "<p align='center'><input type='submit' name='copier_param_exam' value='Valider' /></p>\n";
	echo add_token_field();
	echo "</form>\n";

	echo js_checkbox_change_style('checkbox_change', 'texte_', "y");

	echo "<script type='text/javascript'>
function checkbox_change2(pref_id, id_exam) {
	var tab_id_exam=new Array($chaine_tab_id_exam);
	for(i=0;i<$nb_exam;i++) {
		id=pref_id+tab_id_exam[i];
		//alert(id)
		if(document.getElementById('texte_'+id)) {
			document.getElementById('texte_'+id).style.fontWeight='normal';
		}
	}

	id=pref_id+id_exam;
	if(document.getElementById(id)) {
		if(document.getElementById(id).checked) {
			document.getElementById('texte_'+id).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_'+id).style.fontWeight='normal';
		}
	}
}

</script>\n";

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	die();

}

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
