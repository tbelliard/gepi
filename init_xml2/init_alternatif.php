<?php
@set_time_limit(0);
/*
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
}

// INSERT INTO droits VALUES ('/init_xml2/init_alternatif.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

require_once("init_xml_lib.php");

$msg="";

$cat=isset($_POST['cat']) ? $_POST['cat'] : (isset($_GET['cat']) ? $_GET['cat'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);
$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : '';
$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : '';

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : array();

if(isset($_POST['is_posted'])) {
	check_token();

	if(($cat=='profs')&&($mode=='prof')) {
		if($login_prof=='') {
			$msg.="Aucun login professeur n'a été proposé.<br />\n";
		}
		else {
			$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : array();

			$max_ordre_matiere=0;
			$tab_matieres_profs=array();
			$sql="SELECT id_matiere, ordre_matieres FROM j_professeurs_matieres WHERE id_professeur='".$login_prof."';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				while($lig=mysqli_fetch_object($res)) {
					$tab_matieres_profs[]=$lig->id_matiere;
					if($lig->ordre_matieres>$max_ordre_matiere) {$max_ordre_matiere=$lig->ordre_matieres;}
				}
			}

			for($i=0;$i<count($tab_matieres_profs);$i++) {
				if(!in_array($tab_matieres_profs[$i], $matiere)) {
					$sql="DELETE FROM j_professeurs_matieres WHERE id_professeur='$login_prof' AND id_matiere='$tab_matieres_profs[$i]';";
					$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
				}
			}

			for($i=0;$i<count($matiere);$i++) {
				if(!in_array($matiere[$i], $tab_matieres_profs)) {
					$max_ordre_matiere++;
					$sql="INSERT INTO j_professeurs_matieres SET id_professeur='$login_prof', id_matiere='".$matiere[$i]."', ordre_matieres='$max_ordre_matiere';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				}
			}

			echo reordonner_matieres($login_prof);
		}
	}
	elseif(($cat=='profs')&&($mode=='matiere')) {
		if($matiere=='') {
			$msg.="Aucune matière n'a été proposée.<br />\n";
		}
		else {
			$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : array();

			for($k=0;$k<count($login_prof);$k++) {
				$max_ordre_matiere=0;
				$tab_matieres_profs=array();
				$sql="SELECT id_matiere, ordre_matieres FROM j_professeurs_matieres WHERE id_professeur='".$login_prof[$k]."';";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					while($lig=mysqli_fetch_object($res)) {
						$tab_matieres_profs[]=$lig->id_matiere;
						if($lig->ordre_matieres>$max_ordre_matiere) {$max_ordre_matiere=$lig->ordre_matieres;}
					}
				}

				if(!in_array($matiere, $tab_matieres_profs)) {
					$max_ordre_matiere++;
					$sql="INSERT INTO j_professeurs_matieres SET id_professeur='$login_prof[$k]', id_matiere='".$matiere."', ordre_matieres='$max_ordre_matiere';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				}

				echo reordonner_matieres($login_prof);
			}

			$sql="SELECT id_professeur FROM j_professeurs_matieres WHERE id_matiere='".$matiere."';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				while($lig=mysqli_fetch_object($res)) {
					if(!in_array($lig->id_professeur,$login_prof)) {
						$sql="DELETE FROM j_professeurs_matieres WHERE id_professeur='$lig->id_professeur' AND id_matiere='$matiere';";
						$suppr=mysqli_query($GLOBALS["mysqli"], $sql);

						echo reordonner_matieres($lig->id_professeur);
					}
				}
			}
		}
	}
}

$login_prof_passage_autre_prof=isset($_POST['login_prof_passage_autre_prof']) ? $_POST['login_prof_passage_autre_prof'] : NULL;
if(isset($login_prof_passage_autre_prof)) {$login_prof=$login_prof_passage_autre_prof;}

if((isset($_POST['login_prof_inactif']))&&($_POST['login_prof_inactif']!="")) {
	check_token();

	$sql="UPDATE utilisateurs SET etat='actif' WHERE login='".$_POST['login_prof_inactif']."';";
	$update=mysqli_query($GLOBALS["mysqli"], $sql);
	$msg.=civ_nom_prenom($_POST['login_prof_inactif'])." a été activé(e).<br />";

	if($mode=='prof') {
		$login_prof=$_POST['login_prof_inactif'];
	}
}

if(isset($_POST['update_profs_des_groupes'])) {
	check_token();

	$tab_champs=array('profs');

	$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : array();
	//for($i=0;$i<count($id_groupe);$i++) {
	//$i=0;
	foreach($id_groupe as $i => $value) {
		$nb_err=0;

		$current_group=get_group($id_groupe[$i], $tab_champs);

		$tab_login_prof=isset($_POST['login_prof_'.$i]) ? $_POST['login_prof_'.$i] : array();
		for($j=0;$j<count($tab_login_prof);$j++) {
			if(!in_array($tab_login_prof[$j], $current_group['profs']['list'])) {
				$sql="INSERT INTO j_groupes_professeurs SET id_groupe='".$id_groupe[$i]."', login='".$tab_login_prof[$j]."';";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$update) {
					$msg.="Erreur lors de l'association de ".$tab_login_prof[$j]." avec le groupe n°".$id_groupe[$i]."<br />\n";
					$nb_err++;
				}
			}
		}

		for($j=0;$j<count($current_group['profs']['list']);$j++) {
			if(!in_array($current_group['profs']['list'][$j], $tab_login_prof)) {
				$sql="DELETE FROM j_groupes_professeurs WHERE id_groupe='".$id_groupe[$i]."' AND login='".$current_group['profs']['list'][$j]."';";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$update) {
					$msg.="Erreur lors de la désinscription de ".$current_group['profs']['list'][$j]." du groupe n°".$id_groupe[$i]."<br />\n";
					$nb_err++;
				}
			}
		}

		if($nb_err==0) {
			$msg.="Mise à jour du groupe n°".$id_groupe[$i]." effectuée.<br />\n";
		}

		//$i++;
	}
}

if(isset($_GET['suppr_groupe'])) {
	check_token();

	if(!preg_match('/^[0-9]*$/', $_GET['suppr_groupe'])) {
		$msg.="Le groupe n°".$_GET['suppr_groupe']." n'existe pas.<br />\n";
	}
	else {
		if(test_before_group_deletion($_GET['suppr_groupe'])) {
			$sql="SELECT 1=1 FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_devoirs cd, cn_notes_devoirs cnd WHERE ccn.id_cahier_notes=cc.id_racine AND cc.id=cd.id_conteneur AND cd.id=cnd.id_devoir AND ccn.id_groupe='".$_GET['suppr_groupe']."';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0) {
				$msg.="Le groupe n°".$_GET['suppr_groupe']." ne peut pas être supprimé car des notes de devoirs ont été saisies.<br />\n";
			}
			else {

				$delete=delete_group($_GET['suppr_groupe']);
				if($delete) {
					$msg.="Le groupe n°".$_GET['suppr_groupe']." a été supprimé.<br />\n";
				}
				else {
					$msg.="Erreur lors de la suppression du groupe n°".$_GET['suppr_groupe'].".<br />\n";
				}
			}
		}
		else {
			$msg.="Le groupe n°".$_GET['suppr_groupe']." ne peut pas être supprimé car des bulletins ne sont pas vides.<br />\n";
		}
	}
}

if(isset($_POST['add_groupes_classes'])) {
	check_token();

	$nb_reg=0;

	$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : array();

	// On ne met aucun prof au départ
	$tab_profs=array();
	for($i=0;$i<count($id_classe);$i++) {
		$tab_eleves=array();

		$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe[$i]' ORDER BY num_periode DESC LIMIT 1";
		$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_per)>0) {
			$nb_per=mysql_result($res_per, 0);

			$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe[$i]';";
			$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_ele)>0) {
				while($lig_ele=mysqli_fetch_object($res_ele)) {
					for($j=1;$j<=$nb_per;$j++) {
						$tab_eleves[$j][]=$lig_ele->login;
					}
				}
			}

			for($j=0;$j<count($matiere);$j++) {
				if(!isset($tab_profs[$j])) {
					$tab_profs[$j]=array();
					$sql="SELECT id_professeur FROM j_professeurs_matieres WHERE id_matiere='$matiere[$j]';";
					//echo "$sql<br />";
					$res_prof_mat=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_prof_mat)==1) {
						$tab_profs[$j][]=mysql_result($res_prof_mat, 0);
					}
				}

				$description=$matiere[$j];
				$sql="SELECT nom_complet FROM matieres WHERE matiere='$matiere[$j]';";
				$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_mat)>0) {
					$description=mysql_result($res_mat, 0);
				}

				//echo "<br /><p>\$matiere[$j]=".$matiere[$j]."<br />";
				//echo "\$description=".$description."<br />";
				//echo "\$id_classe[$i]=".$id_classe[$i]." (".get_nom_classe($id_classe[$i]).")<br />";

				$creation=create_group($matiere[$j], $description, $matiere[$j], array($id_classe[$i]), -1);
				//echo "create_group($matiere[$j], $description, $matiere[$j], array($id_classe[$i]), -1)<br />";
				if((!$creation)||(!preg_match('/^[0-9]*$/',$creation))) {
					$msg.="Erreur lors de la création d'un groupe de $matiere[$j] en ".get_nom_classe($id_classe[$i]).".<br />\n";
				}
				else {
					/*
					echo "Profs:<br />";
					echo "<pre>";
					print_r($tab_profs[$j]);
					echo "</pre><br />";
					
					echo "Eleves:<br />";
					echo "<pre>";
					print_r($tab_eleves);
					echo "</pre><br />";
					*/
					//$id_groupe=mysql_insert_id();
					$id_groupe=$creation;
					$update=update_group($id_groupe, $matiere[$j], $description, $matiere[$j], array($id_classe[$i]), $tab_profs[$j], $tab_eleves);
					//echo "update_group($id_groupe, $matiere[$j], $description, $matiere[$j], array($id_classe[$i]), $tab_profs[$j], $tab_eleves)<br />";
					if(!$update) {
						$msg.="Erreur lors du remplissage de l'enseignement de $matiere[$j] en ".get_nom_classe($id_classe[$i]).".<br />\n";
					}
					else {
						$nb_reg++;
					}
				}
			}
		}
	}

	if(($msg=="")&&($nb_reg>0)) {
		$msg="Création des groupes effectuée.<br />\n";
	}

	unset($id_classe);
}


$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='form1'>\n";
echo "<p class='bold'>";

if(!isset($cat)) {
	echo "<a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a>";
	echo "</p>\n";
	echo "</form>\n";

	echo "<center><h3 class='gepi'>Quatrième phase d'initialisation<br />Initialisation alternative</h3></center>\n";
	
	echo "<p>Si l'emploi du temps n'est pas encore remonté vers STS, vous disposez d'un fichier <b>sts_emp_RNE_ANNEE.xml</b> incomplet.<br />\n";
	echo "Si vous ne disposez pas non plus d'un export CSV d'UnDeuxTemps pour initialiser les enseignements, vous pouvez effectuer la création ici.<br />Il convient de suivre les liens dans l'ordre&nbsp;:</p>\n";
	
	echo "<ol>\n";
	echo "<li><p><a href='".$_SERVER['PHP_SELF']."?cat=profs'>Associer les matières aux professeurs</a></p></li>\n";
	echo "<li><p><a href='".$_SERVER['PHP_SELF']."?cat=classes'>Créer des enseignements dans des sélections de classes</a></p></li>\n";
	// style='color:red'
	echo "<li><p>Une fois les enseignements créés, il faut poursuivre ici pour faire le ménage des affectations d'élèves dans les enseignements en tenant compte des saisies d'options dans Sconet.<br /><a href='init_options.php?a=a".add_token_in_url()."'>Prise en compte des options</a><br /><b style='color:red'>ATTENTION&nbsp;:</b> Cette étape ne doit être réalisée que lors de l'initialisation de l'année (<em>pas en cours d'année</em>).</p></li>\n";
	echo "</ol>\n";


	echo "<p style='text-indent:-4em; margin-left:4em'><em>NOTE&nbsp;:</em><br />\n";
	echo "Dans cette phase, aucune table n'est vidée.<br />Il est donc possible de se servir de cette page pour ajouter des associations en cours d'année.</p>\n";

}
//===================================================================================
elseif($cat=='profs') {
	//=========================================
	// AFFECTATION DES MATIERES AUX PROFESSEURS
	//echo " | ";
	echo "<a href='".$_SERVER['PHP_SELF']."'>Retour au choix professeurs ou classes</a>";

	if(!isset($mode)) {
		echo "</p>\n";
		echo "</form>\n";

		echo "<center><h3 class='gepi'>Quatrième phase d'initialisation<br />Initialisation alternative</h3></center>\n";
		echo "<center><h4 class='gepi'>Professeurs</h4></center>\n";

		echo "<p>Choisir&nbsp;:</p>\n";
		echo "<ul>\n";
		//echo "<li><a href='".$_SERVER['PHP_SELF']."?cat=profs&amp;mode=prof'>un professeur, puis ses matières</a></li>\n";
		//echo "<li><a href='".$_SERVER['PHP_SELF']."?cat=profs&amp;mode=matiere'>une matière, puis les professeurs à y associer</a></li>\n";

		// Professeur puis matières
		echo "<li>\n";
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' name='form2' method='post'>\n";
		echo "<p>un professeur, puis ses matières&nbsp;: \n";

		echo champ_select_prof('', 'y', 'form2');

		echo "<input type='hidden' name='cat' value='profs' />\n";
		echo "<input type='hidden' name='mode' value='prof' />\n";
		echo "<input type='submit' name='confirm' value='Valider' />\n";
		echo "</p>\n";
		echo "</form>\n";
		echo "</li>\n";

		//=========================================

		// Matière puis professeurs
		echo "<li>\n";
		//echo "<a href='".$_SERVER['PHP_SELF']."?cat=profs&amp;mode=matiere'>une matière, puis les professeurs à y associer</a>\n";
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' name='form3' method='post'>\n";
		echo "<p>une matière, puis les professeurs à y associer&nbsp;: \n";

		echo champ_select_matiere('', 'y', 'form3');

		echo "<input type='hidden' name='cat' value='profs' />\n";
		echo "<input type='hidden' name='mode' value='matiere' />\n";
		echo "<input type='submit' name='confirm' value='Valider' />\n";
		echo "</p>\n";
		echo "</form>\n";
		echo "</li>\n";

		//=========================================

		// Activer un professeur
		echo "<li>\n";
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' name='form4' method='post'>\n";
		echo "<p>Activer un professeur inactif&nbsp;: \n";

		echo add_token_field();
		echo champ_select_prof('', 'y', 'form4', 'login_prof_inactif', 'inactif');

		echo "<input type='hidden' name='cat' value='profs' />\n";
		echo "<input type='hidden' name='mode' value='prof' />\n";
		echo "<input type='submit' name='confirm' value='Valider' />\n";
		echo "</p>\n";
		echo "</form>\n";
		echo "</li>\n";

		echo "</ul>\n";

		echo "<script type='text/javascript'>
	var change='no';

	function confirm_changement_matiere(thechange, formulaire, themessage) {
		document.forms[formulaire].submit();
	}

	function confirm_changement_prof(thechange, formulaire, themessage) {
		document.forms[formulaire].submit();
	}
</script>\n";
	}
	elseif($mode=='prof') {
		//=========================================
		// ON CHOISIT LE PROFESSEUR PUIS LES MATIERES A ASSOCIER A CE PROFESSEUR
		echo " | <a href='".$_SERVER['PHP_SELF']."?cat=profs'>Retour au choix du mode d'affectation des matières aux professeurs</a>";

		// FORMULAIRE DE CHOIX DU PROF
		$indice_login_prof=0;
		echo " | ".champ_select_prof($login_prof, 'y', 'form1', 'login_prof_passage_autre_prof');
		// ****************************************
		// AJOUTER DES LIENS PROF SUIVANT/PRECEDENT
		// ****************************************

		echo "</p>\n";
		echo "<input type='hidden' name='cat' value='profs' />\n";
		echo "<input type='hidden' name='mode' value='prof' />\n";
		echo "</form>\n";

		echo js_confirm_changement_prof('form1', $indice_login_prof);
	
		echo "<center><h3 class='gepi'>Quatrième phase d'initialisation<br />Initialisation alternative</h3></center>\n";
		echo "<center><h4 class='gepi'>Professeurs</h4></center>\n";

		if($login_prof=='') {
			echo "<p style='color:red;'>Aucun professeur n'a été choisi.<br /><a href='".$_SERVER['PHP_SELF']."?cat=profs'>Retour</a></p>\n";

			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$civ_nom_prenom_prof=civ_nom_prenom($login_prof);
		if($civ_nom_prenom_prof=='') {
			echo "<p style='color:red;'>Aucun professeur ne correspond au login '$login_prof'.<br /><a href='".$_SERVER['PHP_SELF']."?cat=profs'>Retour</a></p>\n";

			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$tab_matieres_profs=array();
		$sql="SELECT id_matiere FROM j_professeurs_matieres WHERE id_professeur='".$login_prof."';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				$tab_matieres_profs[]=$lig->id_matiere;
			}
		}

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo "<p>Quelles matières associer à <b>".$civ_nom_prenom_prof."</b>&nbsp;: \n";
		$sql="SELECT * FROM matieres ORDER BY matiere;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);

		echo add_token_field();

		$nb_matieres=mysqli_num_rows($res);
		$nb_mat_par_colonne=round($nb_matieres/3);
		echo "<table width='100%' summary='Choix des matières'>\n";
		echo "<tr valign='top' align='center'>\n";

		$i=0;
		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while($lig=mysqli_fetch_object($res)) {

			if(($i>0)&&(round($i/$nb_mat_par_colonne)==$i/$nb_mat_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}

			echo "<input type='checkbox' name='matiere[]' id='matiere_$i' value='$lig->matiere' ";
			echo "onchange=\"checkbox_change($i); changement();\" ";
			if(in_array($lig->matiere,$tab_matieres_profs)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
			echo "/><label for='matiere_$i'><span id='texte_matiere_$i'$temp_style title=\"$lig->matiere ($lig->nom_complet)\">".$lig->nom_complet."</span></label><br />\n";
			$i++;
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('matiere_'+cpt)) {
		if(document.getElementById('matiere_'+cpt).checked) {
			document.getElementById('texte_matiere_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_matiere_'+cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";

		echo "<input type='hidden' name='login_prof' value='$login_prof' />\n";
		echo "<input type='hidden' name='cat' value='profs' />\n";
		echo "<input type='hidden' name='mode' value='prof' />\n";
		echo "<input type='hidden' name='is_posted' value='y' />\n";
		echo "<p style='text-align:center;'><input type='submit' name='confirm' value='Valider' /></p>\n";
		echo "</p>\n";
		echo "</form>\n";

	}
	else {
		// mode=matiere
		//=========================================
		// ON CHOISIT LA MATIERE, PUIS LES PROFESSEURS A ASSOCIER A CETTE MATIERE
		echo " | <a href='".$_SERVER['PHP_SELF']."?cat=profs'>Retour au choix du mode d'affectation des matières aux professeurs</a>";

		// FORMULAIRE DE CHOIX DU PROF
		$indice_matiere=0;
		echo " | ".champ_select_matiere($matiere, 'y', 'form1', 'matiere_passage_autre_matiere');
		// ****************************************
		// AJOUTER DES LIENS PROF SUIVANT/PRECEDENT
		// ****************************************

		echo "</p>\n";
		echo "<input type='hidden' name='cat' value='profs' />\n";
		echo "<input type='hidden' name='mode' value='matiere' />\n";
		echo "</form>\n";

		echo js_confirm_changement_matiere('form1', $indice_matiere);
	
		echo "<center><h3 class='gepi'>Quatrième phase d'initialisation<br />Initialisation alternative</h3></center>\n";
		echo "<center><h4 class='gepi'>Matières</h4></center>\n";

		if($matiere=='') {
			echo "<p style='color:red;'>Aucun matière n'a été choisie.<br /><a href='".$_SERVER['PHP_SELF']."?cat=profs'>Retour</a></p>\n";

			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$sql="SELECT 1=1 FROM matieres WHERE matiere='$matiere';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			echo "<p style='color:red;'>La matière <b>$matiere</b> n'existe pas.<br /><a href='".$_SERVER['PHP_SELF']."?cat=profs'>Retour</a></p>\n";

			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$tab_profs_matiere=array();
		$sql="SELECT id_professeur FROM j_professeurs_matieres WHERE id_matiere='".$matiere."';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				$tab_profs_matiere[]=$lig->id_professeur;
			}
		}

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo "<p>Quels professeurs associer à <b>".$matiere."</b>&nbsp;: \n";
		$sql="SELECT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u WHERE etat='actif' AND statut='professeur' ORDER BY nom, prenom;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);

		echo add_token_field();

		$nb_matieres=mysqli_num_rows($res);
		$nb_mat_par_colonne=round($nb_matieres/3);
		echo "<table width='100%' summary='Choix des professeurs'>\n";
		echo "<tr valign='top' align='center'>\n";

		$i=0;
		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while($lig=mysqli_fetch_object($res)) {

			if(($i>0)&&(round($i/$nb_mat_par_colonne)==$i/$nb_mat_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}

			echo "<input type='checkbox' name='login_prof[]' id='login_prof_$i' value='$lig->login' ";
			echo "onchange=\"checkbox_change($i); changement();\" ";
			if(in_array($lig->login,$tab_profs_matiere)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
			echo "/><label for='login_prof_$i'><span id='texte_login_prof_$i'$temp_style>".$lig->civilite." ".$lig->nom." ".casse_mot($lig->prenom, 'majf2').".</span></label><br />\n";
			$i++;
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('login_prof_'+cpt)) {
		if(document.getElementById('login_prof_'+cpt).checked) {
			document.getElementById('texte_login_prof_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_login_prof_'+cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";

		echo "<input type='hidden' name='matiere' value='$matiere' />\n";
		echo "<input type='hidden' name='cat' value='profs' />\n";
		echo "<input type='hidden' name='mode' value='matiere' />\n";
		echo "<input type='hidden' name='is_posted' value='y' />\n";
		echo "<p style='text-align:center;'><input type='submit' name='confirm' value='Valider' /></p>\n";
		echo "</p>\n";
		echo "</form>\n";

		echo "<p><br /></p>\n";

		//echo "<p style='color:red'>A FAIRE: Activer un prof inactif</p>";
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' name='form3' method='post'>\n";
		echo "<p>Activer un professeur inactif&nbsp;: \n";

		echo add_token_field();
		echo champ_select_prof('', 'y', 'form3', 'login_prof_inactif', 'inactif');

		echo "<input type='hidden' name='cat' value='profs' />\n";
		echo "<input type='hidden' name='mode' value='matiere' />\n";
		echo "<input type='hidden' name='matiere' value='$matiere' />\n";
		echo "<input type='submit' name='confirm' value='Valider' />\n";
		echo "</p>\n";
		echo "</form>\n";
		echo js_confirm_changement_prof('form3', 0);

		echo "<p><br /></p>\n";
	}
}
//===================================================================================
elseif($cat=='classes') {
	//echo " | ";
	echo "<a href='".$_SERVER['PHP_SELF']."'>Retour au choix professeurs ou classes</a>";

	//==========================================
	// AFFECTATION DES ENSEIGNEMENTS AUX CLASSES

	//if(!isset($id_classe)) {
	if((!isset($id_classe))||(count($id_classe)==0)) {
		echo "</p>\n";
		echo "</form>\n";
		$tab_classe=array();

		$js_var_classes="";

		// Choisir les classes
		$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_classes jec WHERE c.id=jec.id_classe ORDER BY classe;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb=mysqli_num_rows($res);
		if($nb==0) {
			echo "<p style='color:red'>Aucune classe avec élèves n'existe encore.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		// Choix des classes dont il faudra lister les groupes
		echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form2'>\n";
		echo add_token_field();

		echo "<p class='bold'>Dans quelles classes souhaitez-vous créer des enseignements&nbsp;?</p>\n";

		$nb_class_par_colonne=round($nb/3);
		echo "<table width='100%' summary='Choix des classes'>\n";
		echo "<tr valign='top' align='center'>\n";

		$i=0;
		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while($lig=mysqli_fetch_object($res)) {
			if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}

			echo "<input type='checkbox' name='id_classe[]' id='id_classe_$i' value='$lig->id' ";
			echo "onchange=\"checkbox_change($i)\" ";
			echo "/><label for='id_classe_$i'><span id='texte_id_classe_$i'>Classe : ".$lig->classe.".</span></label>";
			// Ajouter en infobulle les enseignements associés ?
			echo "<br />\n";

			$tab_classe[$i]['id_classe']=$lig->id;
			$tab_classe[$i]['classe']=$lig->classe;

			$js_var_classes.="var classe_$lig->id='".$lig->classe."';\n";

			$i++;
		}

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<input type='hidden' name='cat' value='classes' />\n";
		echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
		echo "</form>\n";

	echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";

	echo "<script type='text/javascript'>
function ModifCase(mode) {
	for (var k=0;k<$i;k++) {
		if(document.getElementById('id_classe_'+k)){
			document.getElementById('id_classe_'+k).checked = mode;
			checkbox_change(k);
		}
	}
}

function checkbox_change(cpt) {
	if(document.getElementById('id_classe_'+cpt)) {
		if(document.getElementById('id_classe_'+cpt).checked) {
			document.getElementById('texte_id_classe_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_id_classe_'+cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";
/*
		echo "<p><em>NOTES</em>&nbsp;:</p>
<ul>
	<li>
		<p>Il est recommandé pour gagner du temps de commencer par sélectionner plusieurs classes et d'y créer les enseignements classiques (<i>MATHS, FRANC, HIGEO,...</i>) et de n'ajouter que par la suite les enseignements n'existant que dans certaines classes.</p>
		<p>Vous pouvez si vous préférez créer tous les enseignements d'une sélection de classe et supprimer ensuite les enseignements n'existant pas dans certaines de ces classes.</p>
	</li>
	<li>
		<p>Dans ce dispositif aucun enseignement regroupement de plusieurs classes n'est créé.<br />
		Vous pourrez cependant fusionner par la suite les enseignements créés dans plusieurs classes.<br />
		Pour cela, la procédure est <b>Gestion des bases/Gestion des classes/&lt;Une_des_classes&gt;/Enseignements/&lt;Le_nom_de_l_enseignement&gt;/fusionner le groupe avec un ou des groupes existants</b></p>
	</li>
</ul>\n";
*/
		$cpt_groupe=0;
		//echo "<p style='color:red;'>Afficher ici les associations déjà effectuées et permettre d'associer les profs.</p>";
		echo "<p class='bold'>Voici les associations déjà effectuées.<br />Vous pouvez y effectuer les associations enseignements/professeurs.</p>";
		for($i=0;$i<count($tab_classe);$i++) {
			echo "<div class='infobulle_corps' style='float: left; width: 20em; border: 1px solid black; margin: 3px;'>\n";
			echo "<a name='profs_grp_id_classe_".$tab_classe[$i]['id_classe']."'></a>\n";
			echo "<p class='bold'>".$tab_classe[$i]['classe']."</p>\n";
			//$groups=get_groups_for_class($tab_classe[$i]['id_classe']);

			/*
			$sql="SELECT DISTINCT g.name, g.id, g.description
					FROM j_groupes_classes jgc, 
						j_groupes_matieres jgm, 
						j_matieres_categories_classes jmcc, 
						matieres m, 
						matieres_categories mc,
						groupes g
					WHERE ( mc.id=jmcc.categorie_id AND 
						jgc.categorie_id = jmcc.categorie_id AND 
						jgc.id_classe=jmcc.classe_id AND 
						jgc.id_classe='".$tab_classe[$i]['id_classe']."' AND 
						jgm.id_groupe=jgc.id_groupe AND 
						m.matiere = jgm.id_matiere AND
						g.id=jgc.id_groupe)
					ORDER BY jmcc.priority,mc.priority,jgc.priorite,m.nom_complet, g.name;";
			*/
			$sql="select g.name, g.id, g.description FROM groupes g, 
					j_groupes_classes jgc,
					j_groupes_matieres jgm
				WHERE (
					jgc.id_classe='".$tab_classe[$i]['id_classe']."' AND
					jgm.id_groupe=jgc.id_groupe
					AND jgc.id_groupe=g.id
					)
				ORDER BY jgc.priorite,jgm.id_matiere, g.name;";
			//echo "$sql<br />";
			$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
			$groups=array();
			while($lig=mysqli_fetch_object($res_grp)) {
				//echo "Groupe n°$lig->id<br />";
				$groups[]=get_group($lig->id);
				//echo "<br />";
			}
			echo "<form method=\"post\" class='boireaus' action=\"".$_SERVER['PHP_SELF']."#profs_grp_id_classe_".$tab_classe[$i]['id_classe']."\" name='form3'>\n";

			$alt=1;
			foreach($groups as $current_group) {
				$alt=$alt*(-1);
				echo "<div class='lig$alt'>\n";
				echo "<p>";
				echo "<input type='hidden' name='id_groupe[$cpt_groupe]' value='".$current_group['id']."' />\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?cat=classes&amp;suppr_groupe=".$current_group['id'].add_token_in_url()."#profs_grp_id_classe_".$tab_classe[$i]['id_classe']."'";
				echo " onclick=\"return confirm_suppr_groupe (this, change, '$themessage')\"";
				echo "><img src='../images/delete16.png' width='16' height='16' alt='Supprimer cet enseignement' title='Supprimer cet enseignement' /></a> \n";
				echo $current_group['name']." (<em>".$current_group['description']."</em>)\n";
				if((isset($current_group['profs']['proflist_string']))&&($current_group['profs']['proflist_string']!="")) {
					echo "<br />\n";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<span style='font-size:x-small;'>".$current_group['profs']['proflist_string']."<span>\n";
				}


				$sql="SELECT u.login, u.nom, u.prenom FROM utilisateurs u, j_professeurs_matieres jpm WHERE u.login=jpm.id_professeur AND jpm.id_matiere='".$current_group['matiere']['matiere']."' ORDER BY nom, prenom;";
				$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_prof)==0) {
					echo "<span style='color:red'>Aucun professeur pour cette matière</span>\n";
				}
				else {
					echo " <a id='lien_profs_$cpt_groupe' style='display:none' href=\"javascript:affiche_choix_prof($cpt_groupe);affiche_info_ajout_prof()\"><img src='../images/icons/add_user.png' width='16' height='16' alt='Ajouter un ou des professeurs' title='Ajouter un ou des professeurs' /></a>\n";
	
					echo "<div id='choix_prof_$cpt_groupe'>\n";

					$cpt_prof=0;
					while($lig_prof=mysqli_fetch_object($res_prof)) {
						echo "<input type='checkbox' name='login_prof_".$cpt_groupe."[]' id='login_prof_".$cpt_groupe."_".$cpt_prof."' value='".$lig_prof->login."' ";
						echo "onchange='test_form_classe(".$tab_classe[$i]['id_classe'].");checkbox_change_prof($cpt_groupe, $cpt_prof); changement();' ";
						if(in_array($lig_prof->login,$current_group['profs']['list'])) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
						echo "/><label for='login_prof_".$cpt_groupe."_".$cpt_prof."' id='texte_login_prof_".$cpt_groupe."_".$cpt_prof."'$temp_style> $lig_prof->nom ".casse_mot($lig_prof->prenom,"majf2")."</label><br />\n";
						$cpt_prof++;
					}
	
					echo "</div>\n";
				}
				echo "</div>\n";
				//echo "</p>\n";
				$cpt_groupe++;
			}

			echo add_token_field();
			echo "<input type='hidden' name='update_profs_des_groupes' value='y' />\n";
			echo "<input type='hidden' name='cat' value='classes' />\n";
			echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";

			echo "</div>\n";
		}

		echo "<input type='hidden' name='id_classe_form_prof_en_cours' id='id_classe_form_prof_en_cours' value='' />\n";

		$titre_infobulle="Ajout de professeurs";
		$texte_infobulle="Il n'est possible d'associer les professeurs aux enseignements que classe par classe (<em>un formulaire par classe</em>).<br />Pensez à valider le formulaire de la classe avant d'associer des professeurs à des enseignements d'une autre classe.";
		$tabdiv_infobulle[]=creer_div_infobulle('div_info_ajout_prof',$titre_infobulle,"",$texte_infobulle,"",18,0,'y','y','n','n');

		echo "<script type='text/javascript'>
var change='no';

for(i=0;i<$cpt_groupe;i++) {
	if(document.getElementById('lien_profs_'+i)) {
		document.getElementById('lien_profs_'+i).style.display='';

		if(document.getElementById('choix_prof_'+i)) {
			document.getElementById('choix_prof_'+i).style.display='none';
		}
	}
}

function affiche_info_ajout_prof() {
	afficher_div('div_info_ajout_prof','y',80,20);
	setTimeout(\"cacher_div('div_info_ajout_prof')\",5000);
}

function affiche_choix_prof(i) {
	if(document.getElementById('choix_prof_'+i)) {
		if(document.getElementById('choix_prof_'+i).style.display=='none') {
			document.getElementById('choix_prof_'+i).style.display='';
		}
		else {
			document.getElementById('choix_prof_'+i).style.display='none';
		}
	}
}

function checkbox_change_prof(cpt_groupe, cpt_prof) {
	if(document.getElementById('login_prof_'+cpt_groupe+'_'+cpt_prof)) {
		if(document.getElementById('login_prof_'+cpt_groupe+'_'+cpt_prof).checked) {
			document.getElementById('texte_login_prof_'+cpt_groupe+'_'+cpt_prof).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_login_prof_'+cpt_groupe+'_'+cpt_prof).style.fontWeight='normal';
		}
	}
}

function confirm_suppr_groupe(theLink, thechange, themessage) {
	if (!(thechange)) {thechange='no';}
	// Confirmation is not required in the configuration file
	if (thechange != 'yes') {
		//return true;
		// Si la variable confirmMsg est vide, alors in n'y a pas de demande de confirmation
		var is_confirmed = confirm('Etes-vous sur de vouloir supprimer ce groupe ?');
		return is_confirmed;
	}
	else {
		var is_confirmed = confirm(themessage);
		return is_confirmed;
	}
}

$js_var_classes
function test_form_classe(id_classe) {
	id_classe_form_prof_en_cours=document.getElementById('id_classe_form_prof_en_cours').value;
	if((id_classe_form_prof_en_cours!='')&&(id_classe_form_prof_en_cours!=id_classe)) {
		alert('Vous avez modifié la liste des professeurs pour une autre classe ('+eval('classe_'+id_classe_form_prof_en_cours)+'), sans valider les modifications. Vous ne pouvez éditer la liste des professeurs que d\'une seule classe à la fois.');
	}
	document.getElementById('id_classe_form_prof_en_cours').value=id_classe;
}
</script>\n";

		echo "<div style='clear:both;'>&nbsp;</div>\n";
		echo "<p><em>NOTES</em>&nbsp;:</p>
<ul>
	<li>
		<p>Il est recommandé pour gagner du temps de commencer par sélectionner plusieurs classes et d'y créer les enseignements classiques (<i>MATHS, FRANC, HIGEO,...</i>) et de n'ajouter que par la suite les enseignements n'existant que dans certaines classes.</p>
		<p>Vous pouvez si vous préférez créer tous les enseignements d'une sélection de classe et supprimer ensuite les enseignements n'existant pas dans certaines de ces classes.</p>
	</li>
	<li>
		<p>Dans ce dispositif aucun enseignement regroupement de plusieurs classes n'est créé.<br />
		Vous pourrez cependant fusionner par la suite les enseignements créés dans plusieurs classes.<br />
		Pour cela, la procédure est <b>Gestion des bases/Gestion des classes/&lt;Une_des_classes&gt;/Enseignements/&lt;Le_nom_de_l_enseignement&gt;/fusionner le groupe avec un ou des groupes existants</b></p>
	</li>
	<li>
		<p>A ce stade, les enseignements créés contiennent tous les élèves de la classe correspondante.<br />La table 'temp_gep_import2' contient les options suivies par les élèves comme saisi dans Sconet.<br />Une fois les enseignements créés, il faut poursuivre ici pour faire le ménage des affectations d'élèves dans les enseignements en tenant compte des saisies d'options dans Sconet.<br /><b><a href='init_options.php?a=a".add_token_in_url()."'>Prise en compte des options</a></b><br /><b style='color:red'>ATTENTION&nbsp;:</b> Cette étape ne doit être réalisée que lors de l'initialisation de l'année (<em>pas en cours d'année</em>).</p>
	</li>
</ul>\n";

		require("../lib/footer.inc.php");
		die();
	}

	//===================================================================================
	// Choix des matières/enseignements à créer dans les classes choisies
	echo " | <a href='".$_SERVER['PHP_SELF']."?cat=classes'>Choix des classes</a>";
	echo "</p>\n";
	echo "</form>\n";

	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form2'>\n";
	echo add_token_field();

	echo "<p>Choix des enseignements à créer dans les classes de&nbsp;: <b>\n";
	for($i=0;$i<count($id_classe);$i++) {
		if($i>0) {echo ", ";}
		echo get_nom_classe($id_classe[$i]);
		echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
	}
	echo "</b>";
	echo "</p>\n";

	$sql="SELECT * FROM matieres ORDER BY matiere;";
	$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_mat=mysqli_num_rows($res_mat);

	$nb_par_colonne=round($nb_mat/3);
	echo "<table width='100%' summary='Choix des matières'>\n";
	echo "<tr valign='top' align='center'>\n";

	$cpt_mat=0;
	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";

	while($lig=mysqli_fetch_object($res_mat)) {
		if(($cpt_mat>0)&&(round($cpt_mat/$nb_par_colonne)==$cpt_mat/$nb_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		echo "<input type='checkbox' name='matiere[]' id='matiere_$cpt_mat' value='$lig->matiere' ";
		echo "onchange=\"checkbox_change_matiere($cpt_mat)\" ";
		echo "/><label for='matiere_$cpt_mat'><span id='texte_matiere_$cpt_mat' title=\"$lig->matiere ($lig->nom_complet)\">".$lig->nom_complet."</span></label>";
		echo "<br />\n";

		$cpt_mat++;
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<input type='hidden' name='cat' value='classes' />\n";
	echo "<input type='hidden' name='add_groupes_classes' value='y' />\n";
	echo "<p align='center'><input type='submit' value='Ajouter des enseignements de ces matières' /></p>\n";

	echo "</form>\n";

	echo "<script type='text/javascript'>
function checkbox_change_matiere(cpt) {
	if(document.getElementById('matiere_'+cpt)) {
		if(document.getElementById('matiere_'+cpt).checked) {
			document.getElementById('texte_matiere_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_matiere_'+cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";

	echo "<p><em>NOTES</em>&nbsp;:</p>\n";
	echo "<ul>\n";
	echo "<li>Si un enseignement n'existe que dans une partie des classes choisies, vous pouvez l'ajouter maintenant, et supprimer par la suite les groupes excédentaires.</li>\n";
	echo "<li>Vous pourrez fusionner des groupes par la suite s'ils doivent être à cheval sur plusieurs classes, mais pour que les options saisies pour les élèves dans Sconet soient prises en compte, il vaut mieux créer maintenant les groupes sur les différentes classes.</li>\n";
	echo "<li>L'association des professeurs sera faite après l'ajout des enseignements.</li>\n";
	echo "</ul>\n";
/*
	// Choisir les professeurs
		echo "</p>\n";
		echo "</form>\n";
*/
}

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
