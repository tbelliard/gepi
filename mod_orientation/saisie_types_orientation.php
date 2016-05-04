<?php
/*
 *
 * Copyright 2001-2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
//$resultat_session = resumeSession();
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}


$sql="SELECT 1=1 FROM droits WHERE id='/mod_orientation/saisie_types_orientation.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_orientation/saisie_types_orientation.php',
administrateur='V',
professeur='V',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Saisie des types d orientation',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!acces_saisie_type_orientation()) {
	header("Location: index.php?msg=Accès non autorisé");
	die();
}

$msg="";

$tab_mef=get_tab_mef();

if(isset($_POST['nouvelle_orientation'])) {
	check_token();

	// Nouvelle orientation
	$nouvelle_orientation=$_POST['nouvelle_orientation'];
	$description_nouvelle_orientation=isset($NON_PROTECT["description_nouvelle_orientation"]) ? $NON_PROTECT["description_nouvelle_orientation"] : "";
	$ajout_mef_nouvelle_orientation=isset($_POST['ajout_mef_nouvelle_orientation']) ? $_POST['ajout_mef_nouvelle_orientation'] : array();
	$nb_reg=0;

	// Orientations existantes
	$orientation=isset($_POST['orientation']) ? $_POST['orientation'] : NULL;

	$nb_suppr_orientation=0;
	$suppr_orientation=isset($_POST['suppr_orientation']) ? $_POST['suppr_orientation'] : array();

	if(isset($orientation)) {
		foreach($orientation as $id_orientation => $current_orientation) {
			if(isset($suppr_orientation[$id_orientation])) {
				$temoin_erreur="n";
				$sql="SELECT 1=1 FROM o_voeux WHERE id_orientation='".$suppr_orientation[$id_orientation]."';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$sql="DELETE FROM o_voeux WHERE id_orientation='".$suppr_orientation[$id_orientation]."';";
					//echo "$sql<br />";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						$msg.="Erreur lors de la suppression des voeux associés à l'orientation n°".$suppr_orientation[$id_orientation]."<br />";
						$temoin_erreur="y";
					}
				}

				$sql="SELECT 1=1 FROM o_orientations_mefs WHERE id_orientation='".$suppr_orientation[$id_orientation]."';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$sql="DELETE FROM o_orientations_mefs WHERE id_orientation='".$suppr_orientation[$id_orientation]."';";
					//echo "$sql<br />";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						$msg.="Erreur lors de la suppression des associations orientation/mefs associées à l'orientation n°".$suppr_orientation[$id_orientation]."<br />";
						$temoin_erreur="y";
					}
				}

				$sql="SELECT 1=1 FROM o_orientations WHERE id_orientation='".$suppr_orientation[$id_orientation]."';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$sql="DELETE FROM o_orientations WHERE id_orientation='".$suppr_orientation[$id_orientation]."';";
					//echo "$sql<br />";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						$msg.="Erreur lors de la suppression des orientations élèves associées à l'orientation n°".$suppr_orientation[$id_orientation]."<br />";
						$temoin_erreur="y";
					}
				}

				if($temoin_erreur=="n") {
					$sql="SELECT 1=1 FROM o_orientations_base WHERE id='".$suppr_orientation[$id_orientation]."';";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)>0) {
						$sql="DELETE FROM o_orientations_base WHERE id='".$suppr_orientation[$id_orientation]."';";
						//echo "$sql<br />";
						$del=mysqli_query($GLOBALS["mysqli"], $sql);
						if($del) {
							$nb_suppr_orientation++;
						}
						else {
							$msg.="Erreur lors de la suppression des orientations élèves associées à l'orientation n°".$suppr_orientation[$id_orientation]."<br />";
						}
					}
				}
			}
			else {
				$sql="SELECT * FROM o_orientations_base WHERE id='".$id_orientation."';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)==0) {
					$msg.="Anomalie&nbsp;: L'orientation n°".$id_orientation." n'existe pas dans la table 'o_orientations_base'.<br />";
				}
				else {
					$current_orientation=trim($current_orientation);
					if(!preg_match("/^[A-Za-z0-9]{1,}/", $current_orientation)) {
						$msg.="Le titre de l'orientation doit commencer par un caractère alphanumérique non accentué ($current_orientation).<br />";
					}
					else {
						if(!ctype_alnum(preg_replace("/[_ '-]/", "", ensure_ascii($current_orientation)))) {
							$msg.="Le titre de l'orientation contient des caractères non alphanumériques ($current_orientation).<br />";
						}
						else {
							$lig_orientation=mysqli_fetch_object($res);
							if($lig_orientation->titre!=$current_orientation) {
								$sql="SELECT * FROM o_orientations_base WHERE titre='".mysqli_real_escape_string($mysqli, $current_orientation)."';";
								$test=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test)>0) {
									$msg.="Il existe déjà une orientation portant le titre \"".$current_orientation."\".<br />";
								}
								else {
									$sql="UPDATE o_orientations_base SET titre='".mysqli_real_escape_string($mysqli, preg_replace("/'/", " ", stripslashes($current_orientation)))."' ";

									if((isset($NON_PROTECT['description_'.$id_orientation]))&&($lig_orientation->description!=$NON_PROTECT['description_'.$id_orientation])) {
										$sql.=", description='".mysqli_real_escape_string($mysqli, $NON_PROTECT['description_'.$id_orientation])."' ";
									}

									$sql.=" WHERE id='".$id_orientation."';";
									//echo "$sql<br />";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									if($update) {
										$nb_reg++;
									}
									else {
										echo "Erreur lors de la mise à jour de l'orientation n°$id_orientation.<br />\n";
									}
								}
							}
							elseif((isset($NON_PROTECT['description_'.$id_orientation]))&&($lig_orientation->description!=$NON_PROTECT['description_'.$id_orientation])) {
								$sql="UPDATE o_orientations_base SET description='".mysqli_real_escape_string($mysqli, $NON_PROTECT['description_'.$id_orientation])."' WHERE id='".$id_orientation."';";
								//echo "$sql<br />";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
								if($update) {
									$nb_reg++;
								}
								else {
									echo "Erreur lors de la mise à jour de la description de l'orientation n°$id_orientation.<br />\n";
								}
							}
						}

						// Mise à jour des associations avec des MEFs.

						//assoc_mef_$id_orientation et ajout_assoc_mef_$id_orientation
						$assoc_mef=isset($_POST['assoc_mef_'.$id_orientation]) ? $_POST['assoc_mef_'.$id_orientation] : array();
						$ajout_assoc_mef=isset($_POST['ajout_assoc_mef_'.$id_orientation]) ? $_POST['ajout_assoc_mef_'.$id_orientation] : "";

						$tab_mef_associees=array();
						$sql="SELECT * FROM o_orientations_mefs WHERE id_orientation='$id_orientation';";
						$res_assoc=mysqli_query($mysqli, $sql);
						if(mysqli_num_rows($res_assoc)>0) {
							while($lig_assoc=mysqli_fetch_object($res_assoc)) {
								if(in_array($lig_assoc->mef_code, $assoc_mef)) {
									$tab_mef_associees[]=$lig_assoc->mef_code;
								}
								else {
									// Il faudrait virer les orientations saisies pour tel ou tel élève avec des MEF qui ne le permettent pas.
									$sql="DELETE FROM o_orientations_mefs WHERE id_orientation='".$id_orientation."' AND mef_code='".$lig_assoc->mef_code."';";
									$del=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$del) {
										$msg.="Erreur lors de la suppression de l'association de l'orientation n°$id_orientation avec le code MEF ".$lig_assoc->mef_code."<br />";
									}
								}
							}
						}

						if(($ajout_assoc_mef!="")&&(!in_array($ajout_assoc_mef, $tab_mef_associees))) {
							$sql="INSERT INTO o_orientations_mefs SET id_orientation='".$id_orientation."', mef_code='".$ajout_assoc_mef."';";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$insert) {
								$msg.="Erreur lors de l'insertion de l'association de l'orientation n°$id_orientation avec le code MEF ".$ajout_assoc_mef."<br />";
							}
						}
					}
				}
			}
		}
	}

	if(trim($nouvelle_orientation)!="") {
		if(!preg_match("/^[A-Za-z0-9]{1,}/", $nouvelle_orientation)) {
			$msg.="Le titre de l'orientation doit commencer par un caractère alphanumérique non accentué ($nouvelle_orientation).<br />";
		}
		elseif(!ctype_alnum(preg_replace("/[_ '-]/", "", ensure_ascii($nouvelle_orientation)))) {
			$msg.="Le titre de la nouvelle orientation contient des caractères non alphanumériques.<br />";
		}
		else {

			$sql="SELECT * FROM o_orientations_base WHERE titre='".mysqli_real_escape_string($mysqli, preg_replace("/'/", " ", stripslashes($nouvelle_orientation)))."';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0) {
				$msg.="Il existe déjà une orientation portant le titre \"".$nouvelle_orientation."\".<br />";
			}
			else {
				$sql="INSERT INTO o_orientations_base SET titre='".mysqli_real_escape_string($mysqli, $nouvelle_orientation)."'";
				if(isset($NON_PROTECT['description_nouvelle_orientation'])) {
					$sql.=", description='".mysqli_real_escape_string($mysqli, $NON_PROTECT['description_nouvelle_orientation'])."'";
				}
				$sql.=";";
				//echo "$sql<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if($insert) {
					$nb_reg++;

					$id_orientation=mysqli_insert_id($mysqli);

					for($loop=0;$loop<count($ajout_mef_nouvelle_orientation);$loop++) {
						if(!array_key_exists($ajout_mef_nouvelle_orientation[$loop], $tab_mef)) {
							$msg.="Le code MEF ".$ajout_mef_nouvelle_orientation[$loop]." est inconnu.<br />";
						}
						else {
							$sql="INSERT INTO o_orientations_mefs SET id_orientation='".$id_orientation."', mef_code='".$ajout_mef_nouvelle_orientation[$loop]."';";
							//echo "$sql<br />";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$insert) {
								$msg.="Erreur lors de l'association du code MEF ".$ajout_mef_nouvelle_orientation[$loop]." à la nouvelle orientation.<br />";
							}
						}
					}
				}
				else {
					echo "Erreur lors de l'enregistrement de la nouvelle orientation.<br />\n";
				}
			}
		}
	}

	if($nb_reg>0) {
		$msg.=$nb_reg." enregistrement(s) effectué(s).<br />";
	}

	if($nb_suppr_orientation>0) {
		$msg.=$nb_suppr_orientation." orientation(s) supprimée(s).<br />";
	}
}

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";
$themessage = 'Des modifications n ont pas été validées. Voulez-vous vraiment quitter sans enregistrer ?';
//================================
$titre_page = "Orientation";
require_once("../lib/header.inc.php");
//================================

//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
if($_SESSION['statut']=='administrateur') {
	echo "
 | <a href='".$_SERVER['PHP_SELF']."?import_orientation=y' onclick=\"return confirm_abandon (this, change, '$themessage')\">Importer un CSV</a>";
}

if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('OrientationSaisieVoeuxScolarite')))||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('OrientationSaisieVoeuxCpe')))||
(($_SESSION['statut']=='professeur')&&(getSettingAOui('OrientationSaisieVoeuxPP'))&&(is_pp($_SESSION['login'])))) {
	echo "
 | <a href='saisie_voeux.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les voeux</a>";
}

if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('OrientationSaisieOrientationScolarite')))||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('OrientationSaisieOrientationCpe')))||
(($_SESSION['statut']=='professeur')&&(getSettingAOui('OrientationSaisieOrientationPP'))&&(is_pp($_SESSION['login'])))) {
	echo "
 | <a href='saisie_orientation.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les orientations proposées</a>";
}
/*
if(acces_saisie_type_orientation()) {
	echo "
 | <a href='saisie_types_orientation.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les types d'orientations</a>";
}
*/
if(acces("/mod_orientation/consulter_orientation.php", $_SESSION['statut'])) {
	echo "
 | <a href='consulter_orientation.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Consulter les voeux et orientations proposées</a>";
}

echo "
</p>\n";

//==========================================================

$import_orientation=isset($_GET['import_orientation']) ? $_GET['import_orientation'] : NULL;
$valide_import_orientation=isset($_POST['valide_import_orientation']) ? $_POST['valide_import_orientation'] : NULL;

if($_SESSION['statut']=='administrateur') {
	if(isset($import_orientation)) {
		echo "<h3 class='gepi'>Import d'un fichier d'orientations</h3>

	<p>Le fichier doit avoir le format suivant&nbsp;:<br />
	Titre_orientation;Description_orientation;Mef_code_1;Mef_code_2;Mef_code_3;...
	</p>

	<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>
		<fieldset class='fieldset_opacite50'>
			".add_token_field()."
			<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>
			<p><input type='submit' name='valide_import_orientation' value='Valider' /></p>
		</fieldset>
	</form>

	<p><br /></p>\n";

		require("../lib/footer.inc.php");
		die();
	}
	elseif(isset($valide_import_orientation)) {
		check_token(false);

		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

		if (trim($csv_file['name'])=='') {
			echo "<p>Aucun fichier n'a été sélectionné !<br />\n";
			echo "<a href='".$_SERVER['PHP_SELF']."?import_orientation=y'>Cliquer ici</a> pour recommencer !</p>\n";
		}
		else{

			//echo "mime_content_type(".$csv_file['tmp_name'].")=".mime_content_type($csv_file['tmp_name'])."<br />";
			//die();
			if(mime_content_type($csv_file['tmp_name'])!="text/plain") {
				echo "<p style='color:red;'>Le type du fichier ne convient pas: ".mime_content_type($csv_file['tmp_name'])."<br />\n";
				echo "Vous devez fournir un fichier TXT (<i>type bloc-notes</i>), pas un fichier traitement de texte ou quoi que ce soit d'autre.</p>\n";
			}
			else {
				$fp=fopen($csv_file['tmp_name'],"r");

				if(!$fp){
					echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
					echo "<p><a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine'>Cliquer ici</a> pour recommencer !</center></p>\n";
				}
				else{

					$fp=fopen($csv_file['tmp_name'],"r");

					$nb_max_reg=100;

					$nb_reg=0;
					$temoin_erreur='n';

					while(!feof($fp)){
						$ligne = fgets($fp, 4096);
						if(trim($ligne)!="") {
							$ligne=trim($ligne);
							$tab=explode(";", $ligne);

							$temoin_erreur2='n';

							$sql="SELECT id FROM o_orientations_base WHERE titre='".mysqli_real_escape_string($mysqli, $tab[0])."';";
							//echo "$sql<br />";
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test)==0) {
								$sql="INSERT INTO o_orientations_base SET titre='".mysqli_real_escape_string($mysqli, $tab[0])."', description='".mysqli_real_escape_string($mysqli, $tab[1])."';";
								//echo "$sql<br />";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {
									$nb_reg++;
									$id_orientation=mysqli_insert_id($GLOBALS["mysqli"]);
								}
								else {
									echo "<span style='color:red;'><b>Erreur lors de l'insertion de l'orientation:</b> $ligne</span><br />\n";
									$temoin_erreur='y';
									$temoin_erreur2='y';
								}
							}
							else {
								$lig=mysqli_fetch_object($test);
								$id_orientation=$lig->id;
								$sql="UPDATE o_orientations_base SET titre='".mysqli_real_escape_string($mysqli, $tab[0])."', description='".mysqli_real_escape_string($mysqli, $tab[1])."' WHERE id='".$id_orientation."';";
								//echo "$sql<br />";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
								if($update) {
									$nb_reg++;
								}
								else {
									echo "<span style='color:red;'><b>Erreur lors de l'insertion de l'orientation:</b> $ligne</span><br />\n";
									$temoin_erreur='y';
									$temoin_erreur2='y';
								}
							}

							if($temoin_erreur2=='n') {
								for($loop=2;$loop<count($tab);$loop++) {
									$sql="SELECT id FROM o_orientations_mefs WHERE id_orientation='".$id_orientation."' AND mef_code='".$tab[$loop]."';";
									//echo "$sql<br />";
									$res=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res)==0) {
										$sql="INSERT INTO o_orientations_mefs SET id_orientation='".$id_orientation."', mef_code='".$tab[$loop]."';";
										//echo "$sql<br />";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$insert) {
											echo "<span style='color:red;'><b>Erreur lors de l'association de l'orientation avec le code MEF&nbsp;:</b> $sql</span><br />\n";
											$temoin_erreur='y';
										}
									}
								}
							}
						}

						/*
						if($nb_reg>=$nb_max_reg) {
							echo "<p style='color:red;'>On n'enregistre pas plus de $nb_max_reg appréciations lors d'un import.</p>";
							break;
						}
						*/
					}
					fclose($fp);

					if(($nb_reg>0)&&($temoin_erreur=='n')) {echo "<span style='color:red;'>Import effectué.</span><br />";}
				}
			}
		}
	}
}

//==========================================================
//==========================================================

echo "<form name='formulaire' action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		<center><input type='submit' name='ok' value='Valider' /></center>
		".add_token_field();

// Recherche des orientations déjà saisies:
$sql="SELECT * FROM o_orientations_base ORDER BY titre;";
//echo "$sql";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
$cpt=1;
$cpt_assoc_mef=0;
if(mysqli_num_rows($res)!=0) {
	echo "
		<p>Voici la liste de vos orientations&nbsp;:</p>
		<blockquote>
			<table class='boireaus boireaus_alt resizable sortable' border='1' summary='Orientations saisies'>
				<tr style='text-align:center;'>
					<th class='text'>Orientation</th>
					<th class='text'>Description</th>
					<th class='text'>MEF associées</th>
					<th class='nosort'>Supprimer</th>
				</tr>\n";
	while($lig=mysqli_fetch_object($res)) {
		//+++++++++++++++++++++++++++
		$lignes_mef="";
		$sql="SELECT * FROM o_orientations_mefs oom, mef m WHERE oom.id_orientation='".$lig->id."' AND oom.mef_code=m.mef_code ORDER BY m.libelle_court, m.libelle_long, m.libelle_edition;";
		$res_mef=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_mef)>0) {
			while($lig_mef=mysqli_fetch_object($res_mef)) {
				$lignes_mef.="<span style='display:none'>".$tab_mef[$lig_mef->mef_code]['designation_courte']."</span><input type='checkbox' name='assoc_mef_".$lig->id."[]' id='assoc_mef_".$cpt_assoc_mef."' value=\"".$lig_mef->mef_code."\" checked onchange=\"changement();\" /><label for='assoc_mef_".$cpt_assoc_mef."'>".$tab_mef[$lig_mef->mef_code]['designation_courte']."</label><br />\n";
				$cpt_assoc_mef++;
			}
		}
		$lignes_mef.="<select name='ajout_assoc_mef_".$lig->id."' onchange=\"changement();\">\n";
		$lignes_mef.="<option value=\"\">--- Ajouter un MEF ---</option>\n";
		foreach($tab_mef as $mef_code => $mef_courante) {
			$lignes_mef.="<option value=\"".$mef_code."\">".$mef_courante['designation_courte']."</option>\n";
		}
		$lignes_mef.="</select>\n";
		//+++++++++++++++++++++++++++

		echo "
				<tr style='text-align:center;'>
					<td style='text-align:left;'>
						<input type='text' name='orientation[$lig->id]' id='orientation_$lig->id' value=\"".preg_replace('/"/'," ", $lig->titre)."\" onchange='changement()' />
					</td>
					<td>
						<textarea name='no_anti_inject_description_".$lig->id."' cols='60' onchange='changement()'>".$lig->description."</textarea>
					</td>
					<td>
						$lignes_mef
					</td>
					<td><input type='checkbox' name='suppr_orientation[$lig->id]' value='$lig->id' onchange=\"changement();\" /></td>
				</tr>\n";
		$cpt++;
	}
	echo "</table>\n";
	echo "</blockquote>\n";
}

echo "
		<p>Saisie d'une nouvelle orientation&nbsp;:</p>
		<blockquote>
			<table class='boireaus boireaus_alt'>
				<tr>
					<td style='text-align:left'>Titre&nbsp;: </td><td style='text-align:left'><input type='text' name='nouvelle_orientation' id='nouvelle_orientation' value=\"\" onchange='changement()' size='30' /></td>
				</tr>
				<tr>
					<td style='text-align:left'>Description&nbsp;: </td><td style='text-align:left'><textarea name='no_anti_inject_description_nouvelle_orientation' id='no_anti_inject_description_nouvelle_orientation' cols='60' onchange='changement()'></textarea></td>
				</tr>
				<tr>
					<td style='text-align:left'>MEFs associées&nbsp;: </td><td style='text-align:left'>";

$compteur=0;
foreach($tab_mef as $mef_code => $mef_courante) {
	echo "
						<input type='checkbox' name='ajout_mef_nouvelle_orientation[]' id='ajout_mef_nouvelle_orientation_$compteur' value=\"".$mef_code."\" onchange=\"changement();\" /><label for='ajout_mef_nouvelle_orientation_$compteur'>".$mef_courante['designation_courte']."</label><br />";
	$compteur++;
}

echo "
					</td>
				</tr>
			</table>
			<input type='hidden' name='compteur_nb_orientations' value='$cpt' />
			<center><input type='submit' name='ok' value='Valider' /></center>
		</blockquote>
	</fieldset>
</form>
<p><br /></p>
<script type='text/javascript'>
	document.getElementById('nouvelle_orientation').focus();
</script>";

$sql="SELECT DISTINCT commentaire FROM o_voeux WHERE id_orientation='0' AND commentaire NOT IN (SELECT DISTINCT titre FROM o_orientations_base) ORDER BY commentaire;";
//echo "$sql";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	echo "
		<p><br /></p>
		<hr />

		<p>Voici la liste des commentaires saisis dans les voeux pour des orientations non proposées dans la liste ci-dessus&nbsp;:</p>
		<blockquote>
			<table class='boireaus boireaus_alt' border='1' summary='Orientations non listées'>
				<tr>
					<th>Orientation</th>
					<th>Ajouter</th>
				</tr>\n";
	$cpt=0;
	while($lig=mysqli_fetch_object($res)) {
		echo "
				<tr>
					<td id='td_$cpt'>".htmlentities(trim($lig->commentaire))."</td>
					<td title=\"Cliquez pour mettre à jour les champs du formulaire de Nouvelle Orientation ci-dessus.\nAdaptez ensuite le contenu, la liste des MEFS et validez l'ajout.\"><a href='#' onclick=\"document.getElementById('nouvelle_orientation').value=document.getElementById('td_$cpt').innerHTML;document.getElementById('no_anti_inject_description_nouvelle_orientation').value=document.getElementById('td_$cpt').innerHTML;changement();return false;\"><img src='../images/icons/wizard.png' class='icone16' alt='Ajouter' /></a></td>
				</tr>\n";
		$cpt++;
	}
	echo "
			</table>
		</blockquote>";
}

$sql="SELECT DISTINCT commentaire FROM o_orientations WHERE id_orientation='0' AND commentaire NOT IN (SELECT DISTINCT titre FROM o_orientations_base) ORDER BY commentaire;";
//echo "$sql";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {

	echo "
		<p><br /></p>
		<hr />

		<p>Voici la liste des commentaires saisis dans les orientations proposées pour des orientations non proposées dans la liste ci-dessus&nbsp;:</p>
		<blockquote>
			<table class='boireaus boireaus_alt' border='1' summary='Orientations non listées'>
				<tr>
					<th>Orientation</th>
					<th>Ajouter</th>
				</tr>\n";
	$cpt=0;
	while($lig=mysqli_fetch_object($res)) {
		echo "
				<tr>
					<td id='td_$cpt'>".htmlentities(trim($lig->commentaire))."</td>
					<td title=\"Cliquez pour mettre à jour les champs du formulaire de Nouvelle Orientation ci-dessus.\nAdaptez ensuite le contenu, la liste des MEFS et validez l'ajout.\"><a href='#' onclick=\"document.getElementById('nouvelle_orientation').value=document.getElementById('td_$cpt').innerHTML;document.getElementById('no_anti_inject_description_nouvelle_orientation').value=document.getElementById('td_$cpt').innerHTML;changement();return false;\"><img src='../images/icons/wizard.png' class='icone16' alt='Ajouter' /></a></td>
				</tr>\n";
		$cpt++;
	}
	echo "
			</table>
		</blockquote>";
}

require("../lib/footer.inc.php");
?>
