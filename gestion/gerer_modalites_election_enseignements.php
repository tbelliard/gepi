<?php
/*
* $Id$
*
* Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/gestion/gerer_modalites_election_enseignements.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/gestion/gerer_modalites_election_enseignements.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Gérer les modalités d élection des enseignements.',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//$is_posted=isset($_POST['is_posted']) ? $_POST['is_posted'] : NULL;
//$etape=isset($_POST['etape']) ? $_POST['etape'] : (isset($_GET['etape']) ? $_GET['etape'] : NULL);

$msg="";

if(isset($_GET['ad_retour'])){
	$_SESSION['ad_retour']=$_GET['ad_retour'];
}

if(isset($_POST['forcer_modalites_options_sconet'])) {
	check_token();

	$cpt_insert=0;
	$cpt_update=0;
	$code_matiere=isset($_POST['code_matiere']) ? $_POST['code_matiere'] : array();
	for($loop=0;$loop<count($code_matiere);$loop++) {
		$sql="SELECT DISTINCT jeg.id_groupe, e.login, e.mef_code, seo.code_modalite_elect FROM j_groupes_matieres jgm, 
										matieres m, 
										j_eleves_groupes jeg, 
										eleves e, 
										sconet_ele_options seo 
									WHERE jgm.id_matiere=m.matiere AND 
										m.code_matiere='".$code_matiere[$loop]."' AND 
										jeg.id_groupe=jgm.id_groupe AND 
										jeg.login=e.login AND 
										seo.ele_id=e.ele_id AND 
										seo.code_matiere='".$code_matiere[$loop]."'
									ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS['mysqli'], $sql);
		while($lig=mysqli_fetch_object($res)) {
			$sql="SELECT * FROM j_groupes_eleves_modalites WHERE id_groupe='".$lig->id_groupe."' AND login='".$lig->login."';";
			$test=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($test)==0) {
				$sql="INSERT INTO j_groupes_eleves_modalites SET id_groupe='".$lig->id_groupe."', login='".$lig->login."', code_modalite_elect='".$lig->code_modalite_elect."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'enregistrement de la modalité d'élection pour ".$lig->login." dans le groupe n°".$lig->id_groupe."<br />";
				}
				else {
					$cpt_insert++;

					// Vérifier que l'association modalité/matière existe dans mef_matieres
					$sql="SELECT 1=1 FROM mef_matieres WHERE mef_code='".$lig->mef_code."' AND code_matiere='".$code_matiere[$loop]."' AND code_modalite_elect='".$lig->code_modalite_elect."';";
					$test_mm=mysqli_query($GLOBALS['mysqli'], $sql);
					if(mysqli_num_rows($test_mm)==0) {
						$sql="INSERT INTO mef_matieres SET mef_code='".$lig->mef_code."', code_matiere='".$code_matiere[$loop]."', code_modalite_elect='".$lig->code_modalite_elect."';";
						$insert=mysqli_query($GLOBALS['mysqli'], $sql);
						if(!$insert) {
							$msg.="Erreur lors de l'association mef_code/code_matiere/code_modalite_elect dans la table mef_matieres pour ".$lig->mef_code."|".$code_matiere[$loop]."|".$lig->code_modalite_elect."<br />";
						}
					}
				}
			}
			else {
				$sql="UPDATE j_groupes_eleves_modalites SET code_modalite_elect='".$lig->code_modalite_elect."' WHERE id_groupe='".$lig->id_groupe."' AND login='".$lig->login."';";
				$update=mysqli_query($GLOBALS['mysqli'], $sql);
				if(!$update) {
					$msg.="Erreur lors de la mise à jour de la modalité d'élection pour ".$lig->login." dans le groupe n°".$lig->id_groupe."<br />";
				}
				else {
					$cpt_update++;

					// Vérifier que l'association modalité/matière existe dans mef_matieres
					$sql="SELECT 1=1 FROM mef_matieres WHERE mef_code='".$lig->mef_code."' AND code_matiere='".$code_matiere[$loop]."' AND code_modalite_elect='".$lig->code_modalite_elect."';";
					$test_mm=mysqli_query($GLOBALS['mysqli'], $sql);
					if(mysqli_num_rows($test_mm)==0) {
						$sql="INSERT INTO mef_matieres SET mef_code='".$lig->mef_code."', code_matiere='".$code_matiere[$loop]."', code_modalite_elect='".$lig->code_modalite_elect."';";
						$insert=mysqli_query($GLOBALS['mysqli'], $sql);
						if(!$insert) {
							$msg.="Erreur lors de l'association mef_code/code_matiere/code_modalite_elect dans la table mef_matieres pour ".$lig->mef_code."|".$code_matiere[$loop]."|".$lig->code_modalite_elect."<br />";
						}
					}
				}
			}
		}
	}

	if($cpt_insert>0) {
		$msg.=$cpt_insert." modalités élèves enregistrées (".strftime("%d/%m/%Y à %H:%M:%S").").<br />";
	}
	if($cpt_update>0) {
		$msg.=$cpt_update." modalités élèves mises à jour (".strftime("%d/%m/%Y à %H:%M:%S").").<br />";
	}
}

if(isset($_POST['forcer_modalites_telles_matieres'])) {
	check_token();

	/*
	// matieres.code_matiere peut être vide
	// Changement d'indice pour le tableau transmis.

	$cpt_suppr=0;
	$cpt_insert=0;
	$cpt_update=0;
	$code_modalite_elect=isset($_POST['code_modalite_elect']) ? $_POST['code_modalite_elect'] : array();
	foreach($code_modalite_elect as $key => $value) {
		if($value=="VIDER") {
			$sql="SELECT DISTINCT jgem.id_groupe FROM j_groupes_eleves_modalites jgem,
										j_groupes_matieres jgm, 
										matieres m 
									WHERE jgm.id_matiere=m.matiere AND 
										m.code_matiere='".$key."' AND 
										jgem.id_groupe=jgm.id_groupe;";
			echo "$sql<br />";
			$res=mysqli_query($GLOBALS['mysqli'], $sql);
			while($lig=mysqli_fetch_object($res)) {
				$sql="DELETE FROM j_groupes_eleves_modalites 
									WHERE id_groupe='".$lig->id_groupe."';";
				echo "$sql<br />";
				$del=mysqli_query($GLOBALS['mysqli'], $sql);
				if(!$del) {
					$msg.="Erreur lors de la suppression des modalités pour les élèves du groupe n'°".$lig->id_groupe.".<br />";
				}
				else {
					$cpt_suppr++;
				}
			}
		}
		elseif($value!="") {
			$sql="SELECT DISTINCT jeg.id_groupe, jeg.login FROM j_groupes_matieres jgm, 
											matieres m, 
											j_eleves_groupes jeg 
										WHERE jgm.id_matiere=m.matiere AND 
											m.code_matiere='".$key."' AND 
											jeg.id_groupe=jgm.id_groupe;";
			echo "$sql<br />";
			$res=mysqli_query($GLOBALS['mysqli'], $sql);
			while($lig=mysqli_fetch_object($res)) {
				$sql="SELECT * FROM j_groupes_eleves_modalites WHERE id_groupe='".$lig->id_groupe."' AND login='".$lig->login."';";
				echo "$sql<br />";
				$test=mysqli_query($GLOBALS['mysqli'], $sql);
				if(mysqli_num_rows($test)==0) {
					$sql="INSERT INTO j_groupes_eleves_modalites SET id_groupe='".$lig->id_groupe."', login='".$lig->login."', code_modalite_elect='".$value."';";
					echo "$sql<br />";
					$insert=mysqli_query($GLOBALS['mysqli'], $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'enregistrement de la modalité d'élection pour ".$lig->login." dans le groupe n°".$lig->id_groupe."<br />";
					}
					else {
						$cpt_insert++;
					}
				}
				else {
					$sql="UPDATE j_groupes_eleves_modalites SET code_modalite_elect='".$value."' WHERE id_groupe='".$lig->id_groupe."' AND login='".$lig->login."';";
					echo "$sql<br />";
					$update=mysqli_query($GLOBALS['mysqli'], $sql);
					if(!$update) {
						$msg.="Erreur lors de la mise à jour de la modalité d'élection pour ".$lig->login." dans le groupe n°".$lig->id_groupe."<br />";
					}
					else {
						$cpt_update++;
					}
				}
			}
		}
	}
	*/

	$cpt_suppr=0;
	$cpt_insert=0;
	$cpt_update=0;
	$code_modalite_elect=isset($_POST['code_modalite_elect']) ? $_POST['code_modalite_elect'] : array();
	foreach($code_modalite_elect as $key => $value) {
		if($value=="VIDER") {
			$sql="SELECT DISTINCT jgem.id_groupe FROM j_groupes_eleves_modalites jgem,
										j_groupes_matieres jgm
									WHERE jgm.id_matiere='".$key."' AND 
										jgem.id_groupe=jgm.id_groupe;";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS['mysqli'], $sql);
			while($lig=mysqli_fetch_object($res)) {
				$sql="DELETE FROM j_groupes_eleves_modalites 
									WHERE id_groupe='".$lig->id_groupe."';";
				//echo "$sql<br />";
				$del=mysqli_query($GLOBALS['mysqli'], $sql);
				if(!$del) {
					$msg.="Erreur lors de la suppression des modalités pour les élèves du groupe n'°".$lig->id_groupe.".<br />";
				}
				else {
					$cpt_suppr++;
				}
			}
		}
		elseif($value!="") {
			$sql="SELECT DISTINCT jeg.id_groupe, jeg.login, e.mef_code, m.code_matiere FROM matieres m,
											j_groupes_matieres jgm, 
											j_eleves_groupes jeg, 
											eleves e
										WHERE m.matiere=jgm.id_matiere AND 
											e.login=jeg.login AND 
											jgm.id_matiere='".$key."' AND 
											jeg.id_groupe=jgm.id_groupe;";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS['mysqli'], $sql);
			while($lig=mysqli_fetch_object($res)) {
				$sql="SELECT * FROM j_groupes_eleves_modalites WHERE id_groupe='".$lig->id_groupe."' AND login='".$lig->login."';";
				//echo "$sql<br />";
				$test=mysqli_query($GLOBALS['mysqli'], $sql);
				if(mysqli_num_rows($test)==0) {
					$sql="INSERT INTO j_groupes_eleves_modalites SET id_groupe='".$lig->id_groupe."', login='".$lig->login."', code_modalite_elect='".$value."';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS['mysqli'], $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'enregistrement de la modalité d'élection pour ".$lig->login." dans le groupe n°".$lig->id_groupe."<br />";
					}
					else {
						$cpt_insert++;

						// Vérifier que l'association modalité/matière existe dans mef_matieres
						$sql="SELECT 1=1 FROM mef_matieres WHERE mef_code='".$lig->mef_code."' AND code_matiere='".$lig->code_matiere."' AND code_modalite_elect='".$value."';";
						$test_mm=mysqli_query($GLOBALS['mysqli'], $sql);
						if(mysqli_num_rows($test_mm)==0) {
							$sql="INSERT INTO mef_matieres SET mef_code='".$lig->mef_code."', code_matiere='".$lig->code_matiere."', code_modalite_elect='".$value."';";
							$insert=mysqli_query($GLOBALS['mysqli'], $sql);
							if(!$insert) {
								$msg.="Erreur lors de l'association mef_code/code_matiere/code_modalite_elect dans la table mef_matieres pour ".$lig->mef_code."|".$lig->code_matiere."|".$value."<br />";
							}
						}
					}
				}
				else {
					$sql="UPDATE j_groupes_eleves_modalites SET code_modalite_elect='".$value."' WHERE id_groupe='".$lig->id_groupe."' AND login='".$lig->login."';";
					//echo "$sql<br />";
					$update=mysqli_query($GLOBALS['mysqli'], $sql);
					if(!$update) {
						$msg.="Erreur lors de la mise à jour de la modalité d'élection pour ".$lig->login." dans le groupe n°".$lig->id_groupe."<br />";
					}
					else {
						$cpt_update++;

						// Vérifier que l'association modalité/matière existe dans mef_matieres
						$sql="SELECT 1=1 FROM mef_matieres WHERE mef_code='".$lig->mef_code."' AND code_matiere='".$lig->code_matiere."' AND code_modalite_elect='".$value."';";
						$test_mm=mysqli_query($GLOBALS['mysqli'], $sql);
						if(mysqli_num_rows($test_mm)==0) {
							$sql="INSERT INTO mef_matieres SET mef_code='".$lig->mef_code."', code_matiere='".$lig->code_matiere."', code_modalite_elect='".$value."';";
							$insert=mysqli_query($GLOBALS['mysqli'], $sql);
							if(!$insert) {
								$msg.="Erreur lors de l'association mef_code/code_matiere/code_modalite_elect dans la table mef_matieres pour ".$lig->mef_code."|".$lig->code_matiere."|".$value."<br />";
							}
						}
					}
				}
			}
		}
	}

	if($cpt_suppr>0) {
		$msg.="Modalités vidées pour ".$cpt_suppr." enseignement(s) (".strftime("%d/%m/%Y à %H:%M:%S").").<br />";
	}
	if($cpt_insert>0) {
		$msg.=$cpt_insert." modalités élèves enregistrées (".strftime("%d/%m/%Y à %H:%M:%S").").<br />";
	}
	if($cpt_update>0) {
		$msg.=$cpt_update." modalités élèves mises à jour (".strftime("%d/%m/%Y à %H:%M:%S").").<br />";
	}
}

//**************** EN-TETE *****************
$titre_page = "Modalités d'élection";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE ***************

//debug_var();

$acces_visu_eleve=acces("/eleves/visu_eleve.php", $_SESSION['statut']);
//Seuls les comptes administrateur et scolarité ont accès à /classes/eleve_options.php, mais pour les comptes scolarité, c'est avec restriction j_scol_classes
//$acces_eleve_options=acces("/classes/eleve_options.php", $_SESSION['statut']);
$acces_modify_matiere=acces("/matieres/modify_matiere.php", $_SESSION['statut']);

echo "<p class=bold><a href='";
if(isset($_SESSION['ad_retour'])){
	echo $_SESSION['ad_retour'];
}
else {
	echo "../accueil.php";
}
echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2 align='center'>Gérer les modalités d'élection des enseignements</h2>

<h3>Informations provenant de Sconet</h3>
<blockquote>
<p>Les informations provenant de Sconet sont remplies à l'intialisation de l'année et lors de la mise à jour d'après Sconet.</p>\n";

$options_modalites="";
$tab_indice_modalite=array();
$tab_modalites=get_tab_modalites_election("code");
$cpt=0;
foreach($tab_modalites as $key => $value) {
	$options_modalites.="
							<option value='$key'>".$value["libelle_long"]."</option>";
	$tab_indice_modalite[$key]=$cpt+2;
	$cpt++;
}

$sql="SELECT * FROM sconet_ele_options;";
$res=mysqli_query($GLOBALS['mysqli'], $sql);
if(mysqli_num_rows($res)==0) {
	echo "
<p>La table 'sconet_ele_options' est vide.<br />
Soit vous n'avez pas initialisé l'année avec Sconet,<br />
soit vous avez initialisé l'année avec une version antérieure à la 1.7.0,<br />
soit les options des élèves n'étaient pas renseignées dans Sconet.</p>";
}
else {
	$sql="SELECT DISTINCT m.matiere, m.nom_complet, m.code_matiere FROM sconet_ele_options seo, matieres m WHERE m.code_matiere=seo.code_matiere ORDER BY m.matiere, m.nom_complet;";
	$res=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($res)==0) {
		// On ne devrait pas arriver là.
		echo "
<p>Aucune matière n'a été trouvée dans la table 'sconet_ele_options'.</p>";
	}
	else {
		// Permettre de forcer les options
		echo "
<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='forcer_modalites_options_sconet' value='y' />
		<p>Des options ont été renseignées dans Sconet.<br />
		Vous pouvez forcer <em>(écraser d'éventuelles modifications individuelles manuelles de modalités associées aux élèves)</em> pour les élèves indiqués la modalité d'élection des enseignements de la matière.</p>
		<ul>";
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			$sql="SELECT DISTINCT id_groupe FROM j_groupes_matieres jgm, matieres m WHERE jgm.id_matiere=m.matiere AND m.code_matiere='".$lig->code_matiere."';";
			$res_grp=mysqli_query($GLOBALS['mysqli'], $sql);
			$nb_grp=mysqli_num_rows($res_grp);

			echo "
			<li style='margin-bottom:1em;'>
				<p>
					<input type='checkbox' name='code_matiere[]' id='code_matiere_$cpt' value=\"".$lig->code_matiere."\" onchange=\"checkbox_change('code_matiere_$cpt')\" />
					<label for='code_matiere_$cpt' id='texte_code_matiere_$cpt'> ".$lig->matiere." <em style='font-size:x-small;'>(".$lig->nom_complet.")</em></label>
					".($acces_modify_matiere ? "<a href='../matieres/modify_matiere.php?current_matiere=".$lig->matiere."' title=\"Voir la matière et la liste des enseignements dans un nouvel onglet.\" target='_blank'><img src='../images/icons/chercher.png' class='icone16' alt='Voir' /></a>" : "")."
					 <em>(il y a  actuellement $nb_grp enseignement(s) de cette matière)</em>
					".(($nb_grp==0) ? "<img src='../images/icons/flag.png' class='icone16' alt='Attention' title=\"Cocher cette matière ne présente pas d'intérêt puisqu'aucun enseignement n'y est associé.\nCependant, si la matière/option a été définie dans Sconet, vous avez peut-être opté pour une autre désignation de matière dans 'Gestion des matières' pour les enseignements de cette matière.\nPour prendre en compte les modalités ci-dessous, une solution consisterait à modifier le code matière associé à la désignation que vous avez choisie pour mettre le code matière '".$lig->matiere."'.\" />" : "")."
				</p>";
			$sql="SELECT DISTINCT e.login, e.nom, e.prenom, c.classe, c.id, seo.code_modalite_elect FROM classes c, 
													eleves e, 
													j_eleves_classes jec, 
													sconet_ele_options seo 
												WHERE c.id=jec.id_classe AND 
													e.login=jec.login AND 
													e.ele_id=seo.ele_id AND 
													seo.code_matiere='".$lig->code_matiere."'
												ORDER BY c.classe, e.nom, e.prenom;";
			//echo "$sql<br />";
			$res_ele=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($res_ele)>0) {
				echo "
				<table class='boireaus boireaus_alt'>
					<thead>
						<tr>
							<th>Classe</th>
							".(($_SESSION['statut']=="administrateur") ? "<th title=\"Enseignements suivis.\">Ens.suiv.</th>" : "")."
							".($acces_visu_eleve ? "<th>Voir</th>" : "")."
							<th>Nom</th>
							<th>Prénom</th>
							<th>Code</th>
							<th>Libellé court</th>
							<th>Libelle long</th>
						</tr>
					</thead>
					<tbody>";
				while($lig_ele=mysqli_fetch_object($res_ele)) {
					echo "
						<tr>
							<td>".$lig_ele->classe."</td>
							".(($_SESSION['statut']=="administrateur") ? "<td><a href='../classes/eleve_options.php?login_eleve=".$lig_ele->login."&id_classe=".$lig_ele->id."' target='_blank'><img src='../images/icons/tableau.png' class='icone16' alt='Enseignements' /></td>" : "")."
							".($acces_visu_eleve ? "<td><a href='../eleves/visu_eleve.php?ele_login=".$lig_ele->login."&onglet=enseignements' target='_blank'><img src='../images/icons/ele_onglets.png' class='icone16' alt='Onglets' /></td>" : "")."
							<td>".$lig_ele->nom."</td>
							<td>".$lig_ele->prenom."</td>
							<td>".$lig_ele->code_modalite_elect."</td>
							<td>".$tab_modalites[$lig_ele->code_modalite_elect]["libelle_court"]."</td>
							<td>".$tab_modalites[$lig_ele->code_modalite_elect]["libelle_long"]."</td>
						</tr";
				}
			}
			echo "
					</tbody>
				</table>
			</li>";
			$cpt++;
		}
		echo "
		</ul>
		<p><input type='submit' value='Forcer ces modalités pour les enseignements des matières cochées' /></p>
	</fieldset>
</form>";
	}
}
echo "
</blockquote>
<hr />
".js_checkbox_change_style("checkbox_change", "texte_", "y")."
<a name='forcer_modalites_telles_matieres'></a>
<h3>Divers</h3>
<blockquote>";

$sql="SELECT DISTINCT m.* FROM matieres m, j_groupes_matieres jgm WHERE m.matiere=jgm.id_matiere ORDER BY m.matiere, m.nom_complet;";
$res=mysqli_query($GLOBALS['mysqli'], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p style='color:red'>Aucune matière avec enseignement associé n'a été trouvée.</p>";
}
else {
	$chaine_selection_modalites="";
	echo "
<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='forcer_modalites_telles_matieres' value='y' />

		<p>Forcer les modalités élèves pour tous les enseignements des matières suivantes&nbsp;:</p>

		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th>Matière</th>
					<th>Nom complet</th>
					<th>
						Modalité
						<select name='code_modalite_elect_modele' id='code_modalite_elect_modele' onchange='changement()'>
							<option value=''>Ne pas modifier les modalités associées aux élèves</option>
							<option value='VIDER'>Vider les modalités associées aux élèves</option>".$options_modalites."
						</select>
						<a href='#' onclick=\"imposer_modalite();changement();return false\" title=\"Imposer cette modalité pour tous les enseignements.\"><img src='../images/icons/wizard.png' class='icone16' alt='Forcer' /></a>
					</th>
					<th>
						Modalités associées aux MEFS<span id='span_magic_modalites' style='display:none'><a href='#' onclick=\"selectionner_lignes_une_seule_modalite_sts_sconet();return false;\" title=\"Sélectionner les modalités dans le cas où une seule modalité est associée à la classe.\"><img src='../images/icons/wizard.png' class='icone16' alt='Choix' /></a></span>
					</th>
				</tr>
			</thead>
			<tbody>";
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			$liste_modalites="";
			//SELECT * FROM mef_matieres WHERE mef_code = '$mef_code' AND code_matiere = '$code_matiere'
			$sql="SELECT DISTINCT code_modalite_elect FROM mef_matieres WHERE code_matiere='".$lig->code_matiere."';";
			//$liste_modalites.="$sql";
			$res_mod=mysqli_query($mysqli, $sql);
			$nb_mod=mysqli_num_rows($res_mod);
			if($nb_mod>0) {
				while($lig_mod=mysqli_fetch_object($res_mod)) {
					if($liste_modalites!="") {
						$liste_modalites.="<br />";
					}
					$liste_modalites.=$lig_mod->code_modalite_elect." <em style='font-size:small'>(".$tab_modalites[$lig_mod->code_modalite_elect]["libelle_long"].")</em>";
					if($nb_mod>1) {
						// Faire la liste des MEFS
					}
					elseif(($nb_mod==1)&&(isset($tab_indice_modalite[$lig_mod->code_modalite_elect]))) {
						//alert('".$lig_mod->code_modalite_elect." ".$tab_indice_modalite[$lig_mod->code_modalite_elect]."');
						$liste_modalites.="<a href='#' onclick=\"document.getElementById('code_modalite_elect_$cpt').selectedIndex=".$tab_indice_modalite[$lig_mod->code_modalite_elect]."; return false;\" title=\"Prendre cette modalité ci-contre.\"><img src='../images/icons/wizard.png' class='icone16' alt='Choix' /></a>";
						$chaine_selection_modalites.="document.getElementById('code_modalite_elect_$cpt').selectedIndex=".$tab_indice_modalite[$lig_mod->code_modalite_elect].";\n";
					}
				}
			}
			echo "
				<tr>
					<td>".$lig->matiere."</td>
					<td>".$lig->nom_complet."</td>
					<td>
						<select name='code_modalite_elect[".$lig->matiere."]' id='code_modalite_elect_$cpt'>
							<option value=''>Ne pas modifier les modalités associées aux élèves</option>
							<option value='VIDER'>Vider les modalités associées aux élèves</option>".$options_modalites."
						</select>
					</td>
					<td>
						$liste_modalites
					</td>
				</tr>";
			$cpt++;
		}
		echo "
			</tbody>
		</table>

		<p><input type='submit' value='Forcer les modalités pour les enseignements des matières cochées' /></p>
	</fieldset>
</form>";
}


/*
$sql="SELECT DISTINCT m.* FROM matieres m, j_groupes_matieres jgm WHERE m.matiere=jgm.id_matiere ORDER BY m.matiere, m.nom_complet;";
$res=mysqli_query($GLOBALS['mysqli'], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p style='color:red'>Aucune matière avec enseignement associé n'a été trouvée.</p>";
}
else {
	echo "
<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='forcer_modalites_telles_matieres' value='y' />

		<p>Forcer les modalités élèves pour tous les enseignements des matières suivantes&nbsp;:</p>

		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th>Matière</th>
					<th>Nom complet</th>
					<th>
						Modalité
						<select name='code_modalite_elect_modele' id='code_modalite_elect_modele' onchange='changement()'>
							<option value=''>Ne pas modifier les modalités associées aux élèves</option>
							<option value='VIDER'>Vider les modalités associées aux élèves</option>".$options_modalites."
						</select>
						<a href='#' onclick=\"imposer_modalite();changement();return false\" title=\"Imposer cette modalité pour tous les enseignements.\"><img src='../images/icons/wizard.png' class='icone16' alt='Forcer' /></a>
					</th>
					<th>
						Modalités associées aux MEFS
					</th>
				</tr>
			</thead>
			<tbody>";
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			echo "
				<tr>
					<td>".$lig->matiere."</td>
					<td>".$lig->nom_complet."</td>
					<td>
						<select name='code_modalite_elect[".$lig->matiere."]' id='code_modalite_elect_$cpt'>
							<option value=''>Ne pas modifier les modalités associées aux élèves</option>
							<option value='VIDER'>Vider les modalités associées aux élèves</option>".$options_modalites."
						</select>
					</td>
					<td>";

			$liste_modalites="";
			//SELECT * FROM mef_matieres WHERE mef_code = '$mef_code' AND code_matiere = '$code_matiere'
			$sql="SELECT DISTINCT mm.code_modalite_elect, m.* FROM mef_matieres mm, 
												mef m 
											WHERE mm.code_matiere='".$lig->code_matiere."' AND 
												m.mef_code=mm.mef_code 
											ORDER BY m.libelle_edition;";
			//$liste_modalites.="$sql";
			$res_mod=mysqli_query($mysqli, $sql);
			$nb_mod=mysqli_num_rows($res_mod);
			if($nb_mod>0) {
				echo "
						<table class='boireaus boireaus_alt2'>";
				while($lig_mod=mysqli_fetch_object($res_mod)) {
					echo "
							<tr>
								<td>".$lig->code_matiere."</td>
								<td>".$lig_mod->code_modalite_elect."</td>
								<td>".$lig_mod->libelle_edition."</td>
							</tr>";
				}
				echo "
						</table>";
			}




			echo "
					</td>
				</tr>";
			$cpt++;
		}
		echo "
			</tbody>
		</table>

		<p><input type='submit' value='Forcer les modalités pour les enseignements des matières cochées' /></p>
	</fieldset>
</form>";
}
*/
echo "
</blockquote>

<script type='text/javascript'>
	function imposer_modalite() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('code_modalite_elect_'+i)) {
				document.getElementById('code_modalite_elect_'+i).selectedIndex=document.getElementById('code_modalite_elect_modele').selectedIndex;
			}
		}
	}

	".(($chaine_selection_modalites!="") ? "
	if(document.getElementById('span_magic_modalites')) {
		document.getElementById('span_magic_modalites').style.display='';
	}
	" : "")."
	function selectionner_lignes_une_seule_modalite_sts_sconet() {
		$chaine_selection_modalites
	}
</script>

<p><em style='color:red'>A FAIRE&nbsp;:</em> Afficher une liste des enseignements pour lesquels les modalités ne sont pas renseignées&nbsp;?</p>
<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
