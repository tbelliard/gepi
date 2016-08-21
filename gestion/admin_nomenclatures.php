<?php
/*
 *
 * Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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

$niveau_arbo = 1;
// Initialisations files
//include("../lib/initialisationsPropel.inc.php");
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

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

/*
if (empty($_GET['action']) and empty($_POST['action'])) { $action="";}
    else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
if (empty($_GET['id']) and empty($_POST['id'])) { $id="";}
    else { if (isset($_GET['id'])) {$id=$_GET['id'];} if (isset($_POST['id'])) {$id=$_POST['id'];} }
if (empty($_GET['EXT_ID']) and empty($_POST['EXT_ID'])) { $EXT_ID="";}
    else { if (isset($_GET['EXT_ID'])) {$EXT_ID=$_GET['EXT_ID'];} if (isset($_POST['EXT_ID'])) {$EXT_ID=$_POST['EXT_ID'];} }
if (empty($_GET['LIBELLE_COURT']) and empty($_POST['LIBELLE_COURT'])) { $LIBELLE_COURT="";}
    else { if (isset($_GET['LIBELLE_COURT'])) {$LIBELLE_COURT=$_GET['LIBELLE_COURT'];} if (isset($_POST['LIBELLE_COURT'])) {$LIBELLE_COURT=$_POST['LIBELLE_COURT'];} }
if (empty($_GET['LIBELLE_LONG']) and empty($_POST['LIBELLE_LONG'])) { $LIBELLE_LONG="";}
    else { if (isset($_GET['LIBELLE_LONG'])) {$LIBELLE_LONG=$_GET['LIBELLE_LONG'];} if (isset($_POST['LIBELLE_LONG'])) {$LIBELLE_LONG=$_POST['LIBELLE_LONG'];} }
if (empty($_GET['LIBELLE_EDITION']) and empty($_POST['LIBELLE_EDITION'])) { $LIBELLE_EDITION="";}
    else { if (isset($_GET['LIBELLE_EDITION'])) {$LIBELLE_EDITION=$_GET['LIBELLE_EDITION'];} if (isset($_POST['LIBELLE_EDITION'])) {$LIBELLE_EDITION=$_POST['LIBELLE_EDITION'];} }
*/

$action=isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : "");

if ($action == 'supprimer_toutes_nomenclatures') {
	check_token();

	$sql="TRUNCATE nomenclatures;";
	$menage=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="TRUNCATE nomenclatures_valeurs;";
	$menage=mysqli_query($GLOBALS["mysqli"], $sql);

	$msg="Les tables nomenclatures et nomenclatures_valeurs ont été vidées.<br />";

	$action="";
}
if(($action == 'supprimer')&&(isset($_GET['id']))) {
	check_token();

	$id=$_GET['id'];
	if(!preg_match("/^[0-9]{1,}$/", $id)) {
		$msg="Identifiant de nomenclature à supprimer invalide.<br />";
	}
	else {
		$sql="SELECT * FROM nomenclatures WHERE id='$id';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$msg="Nomenclature n°$id non trouvée.<br />";
		}
		else {
			$lig=mysqli_fetch_object($res);

			// Faut-il supprimer l'association matieres.code_matiere?

			$sql="DELETE FROM nomenclatures_valeurs WHERE code='$lig->code' AND type='$lig->type';";
			$menage=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$menage) {
				$msg="Erreur lors de la suppression des valeurs associées à la nomenclature n°$id.<br />";
			}
			else {
				$sql="DELETE FROM nomenclatures WHERE id='$id';";
				$menage=mysqli_query($GLOBALS["mysqli"], $sql);

				$msg="Suppression de la nomenclature n°$id effectuée.<br />";
			}
		}
	}

	$action="consulter";
}

if(isset($_GET['imposer_codes_matieres_vides'])) {
	check_token();

	$nb_update=0;

	$sql="SELECT m.matiere, nv.code FROM matieres m,
	nomenclatures_valeurs nv 
	WHERE m.code_matiere='' AND 
	m.matiere=nv.valeur AND 
	nv.nom='code_gestion';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$sql="UPDATE matieres SET code_matiere='".$lig->code."' WHERE matiere='".$lig->matiere."';";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			if($res2) {
				$nb_update++;
			}
			else {
				$msg="Erreur lors de $sql.<br />";
			}
		}
	}

	$msg=$nb_update." code_matiere imposés.<br />";
}

//type=matiere&amp;code=$lig->code&amp;option_sconet_saisie
if((isset($_GET['type']))&&($_GET['type']=="matiere")&&(isset($_GET['code']))&&($_GET['option_sconet_saisie'])) {
	check_token();

	$sql="SELECT 1=1 FROM nomenclatures WHERE type='matiere' AND code='".$_GET['code']."';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		$sql="SELECT 1=1 FROM nomenclatures_valeurs WHERE type='matiere' AND code='".$_GET['code']."' AND nom='option_sconet_saisie';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0) {
			$sql="UPDATE nomenclatures_valeurs SET valeur='".$_GET['option_sconet_saisie']."' WHERE type='matiere' AND code='".$_GET['code']."' AND nom='option_sconet_saisie';";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$update) {
				$msg="Erreur lors de la mise à jour de option_sconet_saisie pour la matière.<br />";
			}
		}
		else {
			$sql="INSERT INTO nomenclatures_valeurs SET valeur='".$_GET['option_sconet_saisie']."', type='matiere', code='".$_GET['code']."', nom='option_sconet_saisie';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$insert) {
				$msg="Erreur lors de l'enregistrement de option_sconet_saisie pour la matière.<br />";
			}
		}
	}
	else {
		$msg="La matière choisie est inconnue dans la table nomenclatures.<br />";
	}
	$action="consulter";
}

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";
//====================================
// header
$titre_page = "Nomenclatures";
require_once("../lib/header.inc.php");
//====================================

echo "<p class='bold'>";
if($action=="") {
	echo "<a href=\"../accueil_admin.php\">";
}
else {
	echo "<a href=\"".$_SERVER['PHP_SELF']."\">";
}
echo "<img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
 | <a href=\"../matieres/index.php\">associer les nomenclatures aux matières</a>
 | <a href=\"../mef/associer_eleve_mef.php\">associer les MEF aux élèves</a>
</p>";
// | <a href=\"associer_nomenclatures_classes.php\">associer les nomenclatures aux classes</a>
// Les MEFS sont associées aux élèves et non aux classes

if($action=="") {
	echo "
<h2>Nomenclatures</h2>

<p>Choisissez parmi les actions suivantes&nbsp;:</p>
<ul>
	<li><a href='".$_SERVER['PHP_SELF']."?action=consulter'>Consulter les enregistrements</a></li>
	<li><a href='".$_SERVER['PHP_SELF']."?action=importnomenclature'>Importer les nomenclatures MATIERES et MEF depuis un XML de Sconet/Siècle.</a></li>
	<li><a href='".$_SERVER['PHP_SELF']."?action=supprimer_toutes_nomenclatures".add_token_in_url()."' onclick=\"return confirm('Etes-vous sûr de vouloir supprimer toutes les nomenclatures ?')\"><img src='../images/icons/delete.png' class='icone20' title='Supprimer toutes les nomenclatures' alt='Supprimer tout' /> Supprimer toutes les nomenclatures enregistrées.
</a></li>
</ul>";

	require("../lib/footer.inc.php");
	die();
}

//======================================

if ($action=="consulter") {
	echo "
	<h2>Consultation des nomenclatures</h2>";

	$sql="SELECT * FROM nomenclatures WHERE type='matiere';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "
	<p>Aucune nomenclature de matières n'est enregistrée.</p>";
	}
	else {
		echo "
	<h3>Matières</h3>
	<p>".mysqli_num_rows($res)." matières enregistrées&nbsp;:</p>
	<table class='sortable resizable boireaus boireaus_alt'>
		<tr>
			<th class='text' title='Cliquez pour trier suivant cette colonne'>Code_matiere</th>
			<th class='text' title='Cliquez pour trier suivant cette colonne'>Code_gestion</th>
			<th class='text' title='Cliquez pour trier suivant cette colonne'>Libelle_court</th>
			<th class='text' title='Cliquez pour trier suivant cette colonne'>Libelle_long</th>
			<th class='text' title='Cliquez pour trier suivant cette colonne'>Libelle_edition</th>
			<th class='number' title='Cliquez pour trier suivant cette colonne'>Matiere_ETP</th>
			<th colspan='4'>Action</th>
		</tr>";
		while($lig=mysqli_fetch_object($res)) {
			$code_gestion="";
			$libelle_court="";
			$libelle_long="";
			$libelle_edition="";
			$matiere_etp="";
			$option_sconet_saisie="n";

			$sql="SELECT * FROM nomenclatures_valeurs WHERE type='matiere' AND code='$lig->code';";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res2)>0) {
				while($lig2=mysqli_fetch_object($res2)) {
					if($lig2->nom=='code_gestion') {
						$code_gestion=$lig2->valeur;
					}
					elseif($lig2->nom=='libelle_court') {
						$libelle_court=$lig2->valeur;
					}
					elseif($lig2->nom=='libelle_long') {
						$libelle_long=$lig2->valeur;
					}
					elseif($lig2->nom=='libelle_edition') {
						$libelle_edition=$lig2->valeur;
					}
					elseif($lig2->nom=='matiere_etp') {
						$matiere_etp=$lig2->valeur;
					}
					elseif($lig2->nom=='option_sconet_saisie') {
						$option_sconet_saisie=$lig2->valeur;
					}
				}
			}
			echo "
		<tr>
			<td>$lig->code</td>
			<td>$code_gestion</td>
			<td>$libelle_court</td>
			<td>$libelle_long</td>
			<td>$libelle_edition</td>
			<td>$matiere_etp</td>
			<td><img src='../images/edit16.png' class='icone16' alt='Editer' title=\"L'édition n'est pas encore implémentée.\" /></td>
			<td><a name='ligne_matiere_$lig->code'></a>";
			if($option_sconet_saisie!="y") {
				echo "<a href='".$_SERVER['PHP_SELF']."?type=matiere&amp;code=$lig->code&amp;option_sconet_saisie=y".add_token_in_url()."#ligne_matiere_$lig->code' title=\"Dans Sconet, les options ou *des* options des élèves sont pointées.\n\nActuellement, lors de la mise à jour d'après Sconet, si des élèves sont ajoutés, Gepi ne teste pas si l'élève a la matière/option $code_gestion pour affecter l'élève dans les enseignements de cette matière.\nIl sera donc inscrit par défaut.\n\nCliquez si le fait que l'élève suive cette matière est pointé dans Sconet.\nLes mises à jour d'après Sconet n'en seront que plus fiables.\"><img src='../images/icons/flag.png' width='17' height='18' alt='Non' /></a>";
			}
			else {
				echo "<a href='".$_SERVER['PHP_SELF']."?type=matiere&amp;code=$lig->code&amp;option_sconet_saisie=n".add_token_in_url()."#ligne_matiere_$lig->code' title=\"Dans Sconet, les options ou *des* options des élèves sont pointées.\n\nActuellement, lors de la mise à jour d'après Sconet, si des élèves sont ajoutés, Gepi teste si l'élève a la matière/option $code_gestion pour affecter l'élève dans les enseignements de cette matière.\nIl ne sera pré-coché à l'inscription que si l'option lui est affectée dans Sconet.\n\nCliquez si le fait que l'élève suive cette matière n'est pas pointé dans Sconet.\nLes mises à jour d'après Sconet n'en seront que plus fiables.\"><img src='../images/icons/flag_green.png' width='17' height='18' alt='Oui' /></a>";
			}

			echo "</td>
			<td><a href='".$_SERVER['PHP_SELF']."?action=supprimer&amp;id=$lig->id".add_token_in_url()."' onclick=\"return confirm('Etes-vous sûr de vouloir supprimer cet enregistrement ?')\" title=\"Supprimer\"><img src='../images/icons/delete.png' class='icone16' alt='Supprimer' /></a></td>
		</tr>";
		}
		echo "
	</table>

	<p>Dans la page d'<a href='../matieres/index.php'>association des code_matiere aux matières</a>, il se peut qu'aucune association ne soit encore faite.<br />
	Vous pouvez, ici, pour les matières dont le code_matiere n'est pas renseigné, renseigner les codes d'un clic si le code_gestion coïncide avec le nom de matière.</p>";

		$sql="SELECT m.matiere, nv.code FROM matieres m,
		nomenclatures_valeurs nv 
		WHERE m.code_matiere='' AND 
		m.matiere=nv.valeur AND 
		nv.nom='code_gestion';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);

		echo "
	<p>".mysqli_num_rows($test)." matières sans code_gestion trouvent une correspondance dans le tableau ci-dessus.</p>";

		echo "
	<p><a href='".$_SERVER['PHP_SELF']."?imposer_codes_matieres_vides=y".add_token_in_url()."'>Imposer les codes matières aux matières dont le code est actuellement vide</a>.</p>

	<p style='color:red'>A FAIRE&nbsp;: Pouvoir modifier une information, pouvoir associer une matière depuis ce tableau.</p>";
	}


	$tab_champs_mef=array("CODE_MEF",
	"FORMATION",
	"LIBELLE_LONG",
	"LIBELLE_EDITION",
	"CODE_MEFSTAT",
	"MEF_RATTACHEMENT"
	);


	$sql="SELECT * FROM nomenclatures WHERE type='mef';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "
	<p>Aucune nomenclature de MEF n'est enregistrée.</p>";
	}
	else {
		echo "
	<h3>MEF</h3>
	<p>".mysqli_num_rows($res)." MEF enregistrées&nbsp;:</p>
	<table class='sortable resizable boireaus boireaus_alt'>
		<tr>
			<th class='text' title='Cliquez pour trier suivant cette colonne'>Code_mef</th>
			<th class='text' title='Cliquez pour trier suivant cette colonne'>Formation</th>
			<th class='text' title='Cliquez pour trier suivant cette colonne'>Libelle_long</th>
			<th class='text' title='Cliquez pour trier suivant cette colonne'>Libelle_edition</th>
			<th class='number' title='Cliquez pour trier suivant cette colonne'>Code_mefstat</th>
			<th class='text' title='Cliquez pour trier suivant cette colonne'>MEF_rattachement</th>
			<th colspan='3'>Action</th>
		</tr>";
		while($lig=mysqli_fetch_object($res)) {
			$code_mef="";
			$formation="";
			$libelle_long="";
			$libelle_edition="";
			$code_mefstat="";
			$mef_rattachement="";

			$sql="SELECT * FROM nomenclatures_valeurs WHERE type='mef' AND code='$lig->code';";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res2)>0) {
				while($lig2=mysqli_fetch_object($res2)) {
					if($lig2->nom=='code_mef') {
						$code_mef=$lig2->valeur;
					}
					elseif($lig2->nom=='formation') {
						$formation=$lig2->valeur;
					}
					elseif($lig2->nom=='libelle_long') {
						$libelle_long=$lig2->valeur;
					}
					elseif($lig2->nom=='libelle_edition') {
						$libelle_edition=$lig2->valeur;
					}
					elseif($lig2->nom=='code_mefstat') {
						$code_mefstat=$lig2->valeur;
					}
					elseif($lig2->nom=='mef_rattachement') {
						$mef_rattachement=$lig2->valeur;
					}
				}
			}
			echo "
		<tr>
			<td>$lig->code</td>
			<td>$formation</td>
			<td>$libelle_long</td>
			<td>$libelle_edition</td>
			<td>$code_mefstat</td>
			<td>$mef_rattachement</td>
			<td><img src='../images/edit16.png' class='icone16' alt='Editer' title=\"L'édition n'est pas encore implémentée.\" /></td>
			<td><a href='".$_SERVER['PHP_SELF']."?action=supprimer&amp;id=$lig->id".add_token_in_url()."' onclick=\"return confirm('Etes-vous sûr de vouloir supprimer cet enregistrement ?')\" title=\"Supprimer\"><img src='../images/icons/delete.png' class='icone16' alt='Supprimer' /></a></td>
		</tr>";
		}
		echo "
	</table>

	<p style='color:red'>A FAIRE&nbsp;: Pouvoir modifier une information, pouvoir associer une matière depuis ce tableau.</p>";
	}

	require("../lib/footer.inc.php");
	die();
}

//======================================

if ($action=="importnomenclature") {
	echo "<h2>Importer les nomenclatures</h2>";

	if(!isset($_POST['is_posted'])) {
		$tempdir=get_user_temp_directory();
		if(!$tempdir){
			echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
		}
		else {
			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>
".add_token_field()."
	<p>Veuillez fournir le fichier Nomenclature.xml:<br />
	<input type=\"file\" size=\"65\" name=\"nomenclature_xml_file\" /></p>\n";
			if ($gepiSettings['unzipped_max_filesize']>=0) {
				echo "	<p style=\"font-size:small; color: red;\"><em>REMARQUE&nbsp;:</em> Vous pouvez fournir à Gepi le fichier compressé issu directement de SCONET. (<em>Ex&nbsp;: Nomenclature.zip</em>)</p>";
			}
			echo "
	<input type='checkbox' name='remplacer' id='remplacer' value='y' /><label for='remplacer'> Remplacer les enregistrements déjà présents dans Gepi.</label><br />
	<input type='hidden' name='action' value='importnomenclature' />
	<input type='hidden' name='is_posted' value='yes' />
	<p><input type='submit' value='Valider' /></p>
</form>

<p style='color:red'>Pour le moment, on a un doublon au niveau des MEF dans les tables 'mef' et 'nomenclatures'.<br />
Il faut pour le moment faire les imports dans les pages d'import des nomenclatures et dans celle des mef, mais il faudra sans doute supprimer la table 'mef'.</p>";
		}
	}
	else {
		$tempdir=get_user_temp_directory();
		$xml_file = isset($_FILES["nomenclature_xml_file"]) ? $_FILES["nomenclature_xml_file"] : NULL;

		if(!is_uploaded_file($xml_file['tmp_name'])) {
			echo "<p style='color:red;'>L'upload du fichier a échoué.</p>\n";

			echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
			echo "post_max_size=$post_max_size<br />\n";
			echo "upload_max_filesize=$upload_max_filesize<br />\n";
			echo "</p>\n";
		}
		else {
			if(!file_exists($xml_file['tmp_name'])){
				echo "<p style='color:red;'>Le fichier aurait été uploadé... mais ne serait pas présent/conservé.</p>\n";

				echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
				echo "post_max_size=$post_max_size<br />\n";
				echo "upload_max_filesize=$upload_max_filesize<br />\n";
				echo "et le volume de ".$xml_file['name']." serait<br />\n";
				echo "\$xml_file['size']=".volume_human($xml_file['size'])."<br />\n";
				echo "</p>\n";

				echo "<p>Il semblerait que l'absence d'extension .XML ou .ZIP puisse aussi provoquer ce genre de symptômes.<br />Dans ce cas, ajoutez l'extension et ré-essayez.</p>\n";
			}
			else {
				echo "<p>Le fichier a été uploadé.</p>\n";

				//$source_file=stripslashes($xml_file['tmp_name']);
				$source_file=$xml_file['tmp_name'];
				$dest_file="../temp/".$tempdir."/nomenclature.xml";
				$res_copy=copy("$source_file" , "$dest_file");

				//===============================================================
				// ajout prise en compte des fichiers ZIP: Marc Leygnac

				$unzipped_max_filesize=getSettingValue('unzipped_max_filesize')*1024*1024;
				// $unzipped_max_filesize = 0    pas de limite de taille pour les fichiers extraits
				// $unzipped_max_filesize < 0    extraction zip désactivée
				if($unzipped_max_filesize>=0) {
					$fichier_emis=$xml_file['name'];
					$extension_fichier_emis=my_strtolower(mb_strrchr($fichier_emis,"."));
					if (($extension_fichier_emis==".zip")||($xml_file['type']=="application/zip"))
						{
						require_once('../lib/pclzip.lib.php');
						$archive = new PclZip($dest_file);

						if (($list_file_zip = $archive->listContent()) == 0) {
							echo "<p style='color:red;'>Erreur : ".$archive->errorInfo(true)."</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						if(sizeof($list_file_zip)!=1) {
							echo "<p style='color:red;'>Erreur : L'archive contient plus d'un fichier.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						if(($list_file_zip[0]['size']>$unzipped_max_filesize)&&($unzipped_max_filesize>0)) {
							echo "<p style='color:red;'>Erreur : La taille du fichier extrait (<em>".$list_file_zip[0]['size']." octets</em>) dépasse la limite paramétrée (<em>$unzipped_max_filesize octets</em>).</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						$res_extract=$archive->extract(PCLZIP_OPT_PATH, "../temp/".$tempdir);
						if ($res_extract != 0) {
							echo "<p>Le fichier uploadé a été dézippé.</p>\n";
							$fichier_extrait=$res_extract[0]['filename'];
							unlink("$dest_file"); // Pour Wamp...
							$res_copy=rename("$fichier_extrait" , "$dest_file");
						}
						else {
							echo "<p style='color:red'>Echec de l'extraction de l'archive ZIP.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}
					}
				}
				//fin  ajout prise en compte des fichiers ZIP
				//===============================================================

				if(!$res_copy) {
					echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
					// Il ne faut pas aller plus loin...
					require("../lib/footer.inc.php");
					die();
				}
				else{
					// Lecture du fichier Nomenclature... pour changer les codes numériques d'options dans 'temp_gep_import2' en leur code gestion

					$dest_file="../temp/".$tempdir."/nomenclature.xml";

					$nomenclature_xml=simplexml_load_file($dest_file);
					if(!$nomenclature_xml) {
						echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
						require("../lib/footer.inc.php");
						die();
					}

					$nom_racine=$nomenclature_xml->getName();
					if(my_strtoupper($nom_racine)!='BEE_NOMENCLATURES') {
						echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML Nomenclatures.<br />Sa racine devrait être 'BEE_NOMENCLATURES'.</p>\n";
						require("../lib/footer.inc.php");
						die();
					}

					/*
					<MATIERE CODE_MATIERE="030201">
						<CODE_GESTION>AGL1 </CODE_GESTION>
						<LIBELLE_COURT>ANGLAIS LV1         </LIBELLE_COURT>
						<LIBELLE_LONG>ANGLAIS LV1                             </LIBELLE_LONG>
						<LIBELLE_EDITION>Anglais lv1                                                 </LIBELLE_EDITION>
						<MATIERE_ETP>0</MATIERE_ETP>
					</MATIERE>
					*/
					$tab_champs_matiere=array("CODE_MATIERE",
					"CODE_GESTION",
					"LIBELLE_COURT",
					"LIBELLE_LONG",
					"LIBELLE_EDITION",
					"MATIERE_ETP"
					);

					echo "<p>";
					echo "Analyse du fichier...<br />\n";

					$tab_matiere=array();
					$i=-1;

					$objet_matieres=($nomenclature_xml->DONNEES->MATIERES);
					foreach ($objet_matieres->children() as $matiere) {
						$i++;
			
						$tab_matiere[$i]=array();
			
						foreach($matiere->attributes() as $key => $value) {
							$tab_matiere[$i][mb_strtolower($key)]=trim($value);
						}

						foreach($matiere->children() as $key => $value) {
							if(in_array(my_strtoupper($key),$tab_champs_matiere)) {
								$tab_matiere[$i][mb_strtolower($key)]=preg_replace('/"/','',trim($value));
							}
						}
					}
					/*
					echo "<pre>";
					print_r($tab_matiere);
					echo "</pre>";
					*/
					$nb_matiere_deja=0;
					$nb_matiere_reg=0;
					for($loop=0;$loop<count($tab_matiere);$loop++) {
						$sql="SELECT 1=1 FROM nomenclatures WHERE code='".$tab_matiere[$loop]['code_matiere']."';";
						$test=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test)==0) {

							$sql="INSERT INTO nomenclatures SET code='".$tab_matiere[$loop]['code_matiere']."',
														type='matiere';";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if($insert) {
								foreach($tab_champs_matiere as $key => $value) {
									$tmp_value=strtolower($value);
									if(isset($tab_matiere[$loop][$tmp_value])) {
										$sql="INSERT INTO nomenclatures_valeurs SET type='matiere',
														code='".$tab_matiere[$loop]['code_matiere']."',
														nom='".mysqli_real_escape_string($GLOBALS["mysqli"], $tmp_value)."',
														valeur='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_matiere[$loop][$tmp_value])."';";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$insert) {
											echo "<span style='color:red'>ERREUR&nbsp;:</span> Erreur lors de l'enregistrement suivant&nbsp;:<br />$sql<br />";
										}
									}
								}
								$nb_matiere_reg++;
							}
							else {
								echo "<span style='color:red'>ERREUR&nbsp;:</span> Erreur lors de l'import suivant&nbsp;:<br />$sql<br />";
							}

						}
						else {
							$nb_matiere_deja++;
						}
					}

					if($nb_matiere_deja>0) {
						echo "<p>$nb_matiere_deja matières déjà présente(s) dans Gepi a(ont) été trouvée(s) dans le XML.</p>";
					}
					if($nb_matiere_reg>0) {
						echo "<p>$nb_matiere_reg matières a(ont) été importée(s) depuis le XML.</p>";
					}


					/*
						<MEF CODE_MEF="10310019110">
							<FORMATION>3EME</FORMATION>
							<LIBELLE_LONG>3EME</LIBELLE_LONG>
							<LIBELLE_EDITION>3eme</LIBELLE_EDITION>
							<STATUT_MEF>1</STATUT_MEF>
							<MEF_RATTACHEMENT>10310019110</MEF_RATTACHEMENT>
							<CODE_MEFSTAT>21160010019</CODE_MEFSTAT>
							<NB_OPT_OBLIG>2</NB_OPT_OBLIG>
							<NB_OPT_MINI>2</NB_OPT_MINI>
							<RENFORCEMENT_LANGUES>0</RENFORCEMENT_LANGUES>
							<INSCRIPTION_ETAB>1</INSCRIPTION_ETAB>
							<MEF_ORIGINE>1</MEF_ORIGINE>
							<MEF_SELECTIONNE>1</MEF_SELECTIONNE>
							<MEF_SELORIG>1</MEF_SELORIG>
							<DATE_OUVERTURE>01/09/2002</DATE_OUVERTURE>
							<DATE_FERMETURE>31/12/9999</DATE_FERMETURE>
						</MEF>
					*/

					$tab_champs_mef=array("CODE_MEF",
					"FORMATION",
					"LIBELLE_LONG",
					"LIBELLE_EDITION",
					"CODE_MEFSTAT",
					"MEF_RATTACHEMENT"
					);

					echo "<p>";
					echo "Analyse du fichier...<br />\n";

					$tab_mef=array();
					$i=-1;

					$objet_mefs=($nomenclature_xml->DONNEES->MEFS);
					foreach ($objet_mefs->children() as $mef) {
						$i++;
			
						$tab_mef[$i]=array();
			
						foreach($mef->attributes() as $key => $value) {
							$tab_mef[$i][mb_strtolower($key)]=trim($value);
						}

						foreach($mef->children() as $key => $value) {
							if(in_array(my_strtoupper($key),$tab_champs_mef)) {
								$tab_mef[$i][mb_strtolower($key)]=preg_replace('/"/','',trim($value));
							}
						}
					}
					/*
					echo "<pre>";
					print_r($tab_mef);
					echo "</pre>";
					*/
					$nb_mef_deja=0;
					$nb_mef_reg=0;
					for($loop=0;$loop<count($tab_mef);$loop++) {
						$sql="SELECT 1=1 FROM nomenclatures WHERE code='".$tab_mef[$loop]['code_mef']."';";
						$test=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test)==0) {

							$sql="INSERT INTO nomenclatures SET code='".$tab_mef[$loop]['code_mef']."',
														type='mef';";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if($insert) {
								foreach($tab_champs_mef as $key => $value) {
									$tmp_value=strtolower($value);
									if(isset($tab_mef[$loop][$tmp_value])) {
										$sql="INSERT INTO nomenclatures_valeurs SET type='mef',
														code='".$tab_mef[$loop]['code_mef']."',
														nom='".mysqli_real_escape_string($GLOBALS["mysqli"], $tmp_value)."',
														valeur='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_mef[$loop][$tmp_value])."';";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$insert) {
											echo "<span style='color:red'>ERREUR&nbsp;:</span> Erreur lors de l'enregistrement suivant&nbsp;:<br />$sql<br />";
										}
									}
								}
								$nb_mef_reg++;
							}
							else {
								echo "<span style='color:red'>ERREUR&nbsp;:</span> Erreur lors de l'import suivant&nbsp;:<br />$sql<br />";
							}

						}
						else {
							$nb_mef_deja++;
						}
					}

					if($nb_mef_deja>0) {
						echo "<p>$nb_mef_deja MEF déjà présente(s) dans Gepi a(ont) été trouvée(s) dans le XML.</p>";
					}
					if($nb_mef_reg>0) {
						echo "<p>$nb_mef_reg MEF a(ont) été importée(s) depuis le XML.</p>";
					}

					//=======================================================
					// 20160415

					echo "<p>";
					echo "Analyse du fichier pour extraire les associations MEF/MATIERE/MODALITE_ELECTION...<br />\n";

					$tab_champs_programme=array("CODE_MEF",
					"CODE_MATIERE", 
					"CODE_MODALITE_ELECT");

					$programmes=array();
					$i=-1;

					$objet_programmes=($nomenclature_xml->DONNEES->PROGRAMMES);
					foreach ($objet_programmes->children() as $programme) {
						$i++;
						//echo "<p><b>Matière $i</b><br />";

						$programmes[$i]=array();

						/*
						<PROGRAMME>
							<CODE_MEF>1001000B11A</CODE_MEF>
							<CODE_MATIERE>005400</CODE_MATIERE>
							<CODE_MODALITE_ELECT>S</CODE_MODALITE_ELECT>
							<HORAIRE>0.00</HORAIRE>
						</PROGRAMME>

						foreach($programme->attributes() as $key => $value) {
							// <PROGRAMME>
							//echo "$key=".$value."<br />";
				
							$programmes[$i][my_strtolower($key)]=trim($value);
						}
						*/

						foreach($programme->children() as $key => $value) {
							if(in_array(my_strtoupper($key),$tab_champs_programme)) {
								$programmes[$i][my_strtolower($key)]=preg_replace('/"/','',trim($value));
								//echo "\$programme->$key=".$value."<br />";
							}
						}
					}

					$nb_insert_prog=0;

					// Faut-il supprimer les associations qui ne sont plus dans le XML?
					$tab_mef_mat=array();
					$sql="SELECT * FROM mef_matieres;";
					$res_mm=mysqli_query($GLOBALS["mysqli"], $sql);
					while($lig_mm=mysqli_fetch_object($res_mm)) {
						$tab_mef_mat[$lig_mm->mef_code][$lig_mm->code_matiere][]=$lig_mm->code_modalite_elect;
					}

					for($loop=0;$loop<count($programmes);$loop++) {
						if((isset($programmes[$loop]['code_mef']))&&
						(isset($programmes[$loop]['code_matiere']))&&
						(isset($programmes[$loop]['code_modalite_elect']))) {
							if((!isset($tab_mef_mat[$programmes[$loop]['code_mef']][$programmes[$loop]['code_matiere']]))||
							(!in_array($programmes[$loop]['code_modalite_elect'], $tab_mef_mat[$programmes[$loop]['code_mef']][$programmes[$loop]['code_matiere']]))) {
								$sql="INSERT INTO mef_matieres SET mef_code='".$programmes[$loop]['code_mef']."',
								code_matiere='".$programmes[$loop]['code_matiere']."',
								code_modalite_elect='".$programmes[$loop]['code_modalite_elect']."';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {
									$nb_insert_prog++;
								}
							}
						}
					}

					if($nb_insert_prog>0) {
						echo "<p>$nb_insert_prog association(s) MEF/Matière/Modalité élection ont été importées.</p>";
					}
					else {
						echo "<p>Aucune association MEF/Matière/Modalité élection n'a été ajoutée.</p>";
					}
					//=======================================================

					//=======================================================
					// 20160417

					echo "<p>";
					echo "Analyse du fichier pour extraire les MODALITE_ELECTION...<br />\n";

					$tab_champs_modalites=array("LIBELLE_COURT",
					"LIBELLE_LONG");

					$modalites=array();
					$i=-1;

					$objet_modalites=($nomenclature_xml->DONNEES->MODALITES_ELECTION);
					foreach ($objet_modalites->children() as $modalite) {
						$i++;
						//echo "<p><b>Matière $i</b><br />";

						$modalites[$i]=array();

						/*
						<MODALITE_ELECTION CODE_MODALITE_ELECT="S">
							<LIBELLE_COURT>TRONC COMM</LIBELLE_COURT>
							<LIBELLE_LONG>MATIERE ENSEIGNEE EN TRONC COMMUN</LIBELLE_LONG>
						</MODALITE_ELECTION>
						*/

						foreach($modalite->attributes() as $key => $value) {
							$modalites[$i][my_strtolower($key)]=trim($value);
						}

						foreach($modalite->children() as $key => $value) {
							if(in_array(my_strtoupper($key),$tab_champs_modalites)) {
								$modalites[$i][my_strtolower($key)]=preg_replace('/"/','',trim($value));
								//echo "\$modalite->$key=".$value."<br />";
							}
						}
					}

					/*
					echo "<pre>";
					print_r($modalites);
					echo "</pre>";
					*/

					$nb_insert_mod=0;

					// Faut-il supprimer les associations qui ne sont plus dans le XML?
					$tab_modalites=array();
					$sql="SELECT * FROM nomenclature_modalites_election;";
					$res_mm=mysqli_query($GLOBALS["mysqli"], $sql);
					while($lig_mm=mysqli_fetch_object($res_mm)) {
						$tab_modalites[$lig_mm->code_modalite_elect]=$lig_mm->libelle_court;
					}

					$sql="TRUNCATE nomenclature_modalites_election;";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);

					for($loop=0;$loop<count($modalites);$loop++) {
						if((isset($modalites[$loop]['code_modalite_elect']))&&
						(isset($modalites[$loop]['libelle_court']))&&
						(isset($modalites[$loop]['libelle_long']))) {
							if(!array_key_exists($modalites[$loop]['code_modalite_elect'], $tab_modalites)) {
								$sql="INSERT INTO nomenclature_modalites_election SET code_modalite_elect='".$modalites[$loop]['code_modalite_elect']."',
								libelle_court='".mysqli_real_escape_string($mysqli, $modalites[$loop]['libelle_court'])."',
								libelle_long='".mysqli_real_escape_string($mysqli, $modalites[$loop]['libelle_long'])."';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {
									$nb_insert_mod++;
								}
							}
							else {
								$sql="UPDATE nomenclature_modalites_election SET libelle_court='".mysqli_real_escape_string($mysqli, $modalites[$loop]['libelle_court'])."',
								libelle_long='".mysqli_real_escape_string($mysqli, $modalites[$loop]['libelle_long'])."' WHERE  code_modalite_elect='".$modalites[$loop]['code_modalite_elect']."';";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
							}
						}
					}

					if($nb_insert_mod>0) {
						echo "<p>$nb_insert_mod modalités élection ont été importées.</p>";
					}
					else {
						echo "<p>Aucune modalité élection n'a été enregistrée.</p>";
					}
					//=======================================================

				}
			}
		}

	}
	echo "
<p><a href='".$_SERVER['PHP_SELF']."'>Retour à l'index de la gestion des nomenclatures</a></p>
<br />";
}

require("../lib/footer.inc.php");

