<?php
/*
 *
 * Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/ajout_travail_sanction.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Ajout de travail pour une sanction', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/ajout_travail_sanction.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Ajout de travail pour une sanction', '');";
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('active_mod_discipline')) {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');

$msg="";

$id_sanction=isset($_POST['id_sanction']) ? $_POST['id_sanction'] : (isset($_GET['id_sanction']) ? $_GET['id_sanction'] : NULL);
if((!isset($id_sanction))||(!preg_match("/^[0-9]{1,}$/", $id_sanction))) {
	header("Location: ../accueil.php?msg=Identifiant de sanction invalide");
	die();
}

if($_SESSION['statut']!="professeur") {
	header("Location: ../accueil.php?msg=Mode non encore implémenté.");
	die();
}

//==================================
$sql="SELECT 1=1 FROM s_sanctions ss, s_incidents si WHERE ss.id_sanction='$id_sanction' AND ss.id_incident=si.id_incident AND si.declarant='".$_SESSION['login']."';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	header("Location: ../accueil.php?msg=Vous n êtes pas le déclarant de l'incident.");
	die();
}

$sql="SELECT ss.*, sts.nature, sts.type FROM s_sanctions ss, s_types_sanctions2 sts WHERE ss.id_sanction='$id_sanction' AND ss.id_nature_sanction=sts.id_nature;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	header("Location: ../accueil.php?msg=Type de sanction non identifié.");
	die();
}
$lig_sanction=mysqli_fetch_object($res);
if(($lig_sanction->type!="retenue")&&($lig_sanction->type!="exclusion")&&($lig_sanction->type!="travail")) {
	header("Location: ../accueil.php?msg=Type de sanction ne permettant pas la saisie de travail.");
	die();
}
$id_incident=$lig_sanction->id_incident;
$ele_login=$lig_sanction->login;
//==================================
if($lig_sanction->type=="retenue") {
	$sql="SELECT id_retenue AS id, travail FROM s_retenues WHERE id_sanction='".$id_sanction."';";
}
elseif($lig_sanction->type=="exclusion") {
	$sql="SELECT id_exclusion AS id, travail FROM s_exclusions WHERE id_sanction='".$id_sanction."';";
}
elseif($lig_sanction->type=="travail") {
	$sql="SELECT id_travail AS id, travail FROM s_travail WHERE id_sanction='".$id_sanction."';";
}
$res_travail=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_travail)==0) {
	$travail="";
	$msg.="Anomalie&nbsp;: Aucuns détails de sanction n'ont été trouvés.<br />";
}
else {
	$lig_travail=mysqli_fetch_object($res_travail);
	$travail=$lig_travail->travail;
}
//==================================

if(isset($_POST['ajout_travail'])) {
	check_token();


	if (isset($NON_PROTECT["travail"])){
		$travail_reg=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["travail"]));
		// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
		$travail_reg=suppression_sauts_de_lignes_surnumeraires($travail_reg);

		if($travail_reg!=$travail) {
			if($lig_sanction->type=="retenue") {
				$sql="UPDATE s_retenues SET travail='".$travail_reg."' WHERE id_sanction='".$id_sanction."';";
			}
			elseif($lig_sanction->type=="exclusion") {
				$sql="UPDATE s_exclusions SET travail='".$travail_reg."' WHERE id_sanction='".$id_sanction."';";
			}
			elseif($lig_sanction->type=="travail") {
				$sql="UPDATE s_travail SET travail='".$travail_reg."' WHERE id_sanction='".$id_sanction."';";
			}
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$update) {
				$msg.="Erreur lors de la mise à jour du texte saisi.<br />";
			}
			else {
				$msg.="Mise à jour du texte saisi effectuée.<br />";
				$travail=$travail_reg;
			}
		}
	}

	$temoin_modif_fichier=0;

	unset($suppr_doc_joint);
	$suppr_doc_joint=isset($_POST['suppr_doc_joint']) ? $_POST['suppr_doc_joint'] : array();
	for($loop=0;$loop<count($suppr_doc_joint);$loop++) {
		if((preg_match("/\.\./",$suppr_doc_joint[$loop]))||(preg_match("#/#",$suppr_doc_joint[$loop]))) {
			$msg.="Nom de fichier ".$suppr_doc_joint[$loop]." invalide<br />";
		}
		else {
			$fichier_courant="../$dossier_documents_discipline/incident_".$id_incident."/sanction_".$id_sanction."/".$suppr_doc_joint[$loop];
			if(!unlink($fichier_courant)) {
				$msg.="Erreur lors de la suppression de $fichier_courant<br />";
			}
		}
	}
	$temoin_modif_fichier+=count($suppr_doc_joint);




	$ajouter_doc_joint=isset($_POST['ajouter_doc_joint']) ? $_POST['ajouter_doc_joint'] : array();
	for($loop=0;$loop<count($ajouter_doc_joint);$loop++) {
		if((preg_match("/\.\./",$ajouter_doc_joint[$loop]))||(preg_match("#/#",$ajouter_doc_joint[$loop]))) {
			$msg.="Nom de fichier ".$ajouter_doc_joint[$loop]." invalide<br />";
		}
		else {
			$chemin_src="../$dossier_documents_discipline/incident_".$id_incident."/mesures/".$ele_login;
			$chemin_dest="../$dossier_documents_discipline/incident_".$id_incident."/sanction_".$id_sanction;

			$fichier_src=$chemin_src."/".$ajouter_doc_joint[$loop];
			$fichier_dest=$chemin_dest."/".$ajouter_doc_joint[$loop];
			if(file_exists($fichier_src)) {
				if($discipline_droits_mkdir=="") {
					@mkdir($chemin_dest,0770,true);
				}
				else {
					@mkdir("../$dossier_documents_discipline");
					@mkdir("../$dossier_documents_discipline/incident_".$id_incident);
					@mkdir($chemin_dest);
				}
				copy($fichier_src, $fichier_dest);
			}

			if((isset($tab_tmp_id_sanction))&&(count($tab_tmp_id_sanction)>0)) {
				for($loop2=0;$loop2<count($tab_tmp_id_sanction);$loop2++) {
					$chemin_dest="../$dossier_documents_discipline/incident_".$id_incident."/sanction_".$tab_tmp_id_sanction[$loop2];
					$fichier_dest=$chemin_dest."/".$ajouter_doc_joint[$loop];
					if(file_exists($fichier_src)) {
						if($discipline_droits_mkdir=="") {
							@mkdir($chemin_dest,0770,true);
						}
						else {
							@mkdir("../$dossier_documents_discipline");
							@mkdir("../$dossier_documents_discipline/incident_".$id_incident);
							@mkdir($chemin_dest);
						}
						copy($fichier_src, $fichier_dest);
					}
				}
			}
		}
	}
	$temoin_modif_fichier+=count($ajouter_doc_joint);




	unset($document_joint);
	$document_joint=isset($_FILES["document_joint"]) ? $_FILES["document_joint"] : NULL;
	if((isset($document_joint['tmp_name']))&&($document_joint['tmp_name']!="")) {
		//$msg.="\$document_joint['tmp_name']=".$document_joint['tmp_name']."<br />";
		if(!is_uploaded_file($document_joint['tmp_name'])) {
			$msg.="L'upload du fichier a échoué.<br />\n";
		}
		else{
			if(!file_exists($document_joint['tmp_name'])){
				$msg.="Le fichier aurait été uploadé... mais ne serait pas présent/conservé.<br />\n";
			}
			else {
				//echo "<p>Le fichier a été uploadé.</p>\n";

				$source_file=$document_joint['tmp_name'];
				$dossier_courant="../$dossier_documents_discipline/incident_".$id_incident."/sanction_".$id_sanction;
				if(!file_exists($dossier_courant)) {
					if($discipline_droits_mkdir=="") {
						mkdir($dossier_courant, 0770, true);
					}
					else {
						@mkdir("../$dossier_documents_discipline");
						@mkdir("../$dossier_documents_discipline/incident_".$id_incident);
						@mkdir($dossier_courant);
					}
				}

				if(strstr($document_joint['name'],".")) {
					$extension_fichier=substr(strrchr($document_joint['name'],'.'),1);
					$nom_fichier_sans_extension=preg_replace("/.$extension_fichier$/","",$document_joint['name']);

					$dest_file=$dossier_courant."/".remplace_accents($nom_fichier_sans_extension, "all").".".$extension_fichier;
				}
				else {
					// Pas d'extension dans le nom de fichier fourni
					$dest_file=$dossier_courant."/".remplace_accents($document_joint['name'], "all");
				}

				$res_copy=copy("$source_file" , "$dest_file");
				if(!$res_copy) {$msg.="Echec de la mise en place du fichier ".$document_joint['name']."<br />";}
			}
		}
		$temoin_modif_fichier++;
	}

	if($temoin_modif_fichier>0) {
		$msg.="Enregistrement des modifications pour ".$temoin_modif_fichier." fichier(s).<br />";
	}

}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Discipline: Ajout travail";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

if(acces("/mod_discipline/traiter_incident.php", $_SESSION['statut'])) {
	$page_retour="traiter_incident.php";
}
else {
	$page_retour="../accueil.php";
}

echo "<p class='bold'><a href='$page_retour'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

echo "<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='ajout_travail' value='y' />
		<input type='hidden' name='id_sanction' value='$id_sanction' />
		<p class='bold'>Incident n°".$lig_sanction->id_incident.", sanction n°".$id_sanction." concernant ".get_nom_prenom_eleve($lig_sanction->login, "avec_classe")."</p>
		<p>Travail associé à la ".$mod_disc_terme_sanction."&nbsp;:<br />
		<textarea name='no_anti_inject_travail' cols='60' rows='4'>".$travail."</textarea>
		".sanction_documents_joints($lig_sanction->id_incident, $lig_sanction->login)."
		<p><input type='submit' value='Valider' /></p>
	</fieldset>
</form>";
echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
