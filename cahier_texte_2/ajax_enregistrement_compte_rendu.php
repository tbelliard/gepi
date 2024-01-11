<?php
/*
 *
 * Copyright 2009-2024 Josselin Jacquard, Stephane Boireau
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

//Attention, la sortie standard de ce script (echo), doit etre soit une erreur soit l'id de la notice. La sortie est utilisée dans un javascript

header('Content-Type: text/html; charset=utf-8');

$filtrage_extensions_fichiers_table_ct_types_documents='y';

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Dans le cas ou on poste une notice ou un devoir, pas de traitement anti_inject
// Pour ne pas interférer avec l'échappement mysql déjà géré par propel
$traite_anti_inject = 'no';

require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
require_once("../lib/traitement_data.inc.php");

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (!acces_cdt()) {
	die("Le module n'est pas activé.");
}

check_token();

//debug_var();
//récupération des paramètres de la requète
$id_ct = isset($_POST["id_ct"]) ? $_POST["id_ct"] :(isset($_GET["id_ct"]) ? $_GET["id_ct"] :NULL);
$date_ct = isset($_POST["date_ct"]) ? $_POST["date_ct"] :(isset($_GET["date_ct"]) ? $_GET["date_ct"] :NULL);
$date_ct_cours_suivant = isset($_POST["date_ct_cours_suivant"]) ? $_POST["date_ct_cours_suivant"] :(isset($_GET["date_ct_cours_suivant"]) ? $_GET["date_ct_cours_suivant"] :NULL);
$contenu = isset($_POST["contenu"]) ? $_POST["contenu"] :NULL;
$heure_entry = isset($_POST["heure_entry"]) ? $_POST["heure_entry"] :(isset($_GET["heure_entry"]) ? $_GET["heure_entry"] :NULL);
$uid_post = isset($_POST["uid_post"]) ? $_POST["uid_post"] :(isset($_GET["uid_post"]) ? $_GET["uid_post"] :0);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);

//parametre d'enregistrement de fichiers joints
if (empty($_FILES['doc_file'])) { $doc_file=''; } else { $doc_file=$_FILES['doc_file'];}
$doc_name = isset($_POST["doc_name"]) ? $_POST["doc_name"] :(isset($_GET["doc_name"]) ? $_GET["doc_name"] :NULL);
$doc_masque = isset($_POST["doc_masque"]) ? $_POST["doc_masque"] :(isset($_GET["doc_masque"]) ? $_GET["doc_masque"] :NULL);

//parametre de changement de titre de fichier joint.
$doc_name_modif = isset($_POST["doc_name_modif"]) ? $_POST["doc_name_modif"] :(isset($_GET["doc_name_modif"]) ? $_GET["doc_name_modif"] :NULL);
$id_document = isset($_POST["id_document"]) ? $_POST["id_document"] :(isset($_GET["id_document"]) ? $_GET["id_document"] :NULL);


// 20210126:
if (empty($_FILES['doc_file2'])) { $doc_file2=''; } else { $doc_file2=$_FILES['doc_file2'];}
$doc_masque2 = isset($_POST["doc_masque2"]) ? $_POST["doc_masque2"] :(isset($_GET["doc_masque2"]) ? $_GET["doc_masque2"] :NULL);


// uid de pour ne pas refaire renvoyer plusieurs fois le même formulaire
// autoriser la validation de formulaire $uid_post==$_SESSION['uid_prime']
$uid_prime = isset($_SESSION['uid_prime']) ? $_SESSION['uid_prime'] : 1;
// Pour tester la mise en place d'une copie de sauvegarde, décommenter la ligne ci-dessous:
//$uid_post=$uid_prime;
if ($uid_post==$uid_prime) {
	if(getSettingValue('cdt2_desactiver_copie_svg')!='y') {
		$contenu_cor = traitement_magic_quotes(corriger_caracteres($contenu),'');
		$contenu_cor = str_replace("\\r","",$contenu_cor);
		$contenu_cor = str_replace("\\n","",$contenu_cor);
		//$contenu_cor = stripslashes($contenu_cor);

		$contenu_cor=a_href_target_blank($contenu_cor);
		$contenu_cor=cdt_corrige_chemin_archive($contenu_cor);

		if ($contenu_cor == "" or $contenu_cor == "<br>") {$contenu_cor = "...";}
	
		$sql="INSERT INTO ct_private_entry SET date_ct='$date_ct', heure_entry='".strftime("%H:%M:%S")."', id_login='".$_SESSION['login']."', id_groupe='$id_groupe', contenu='<b>COPIE DE SAUVEGARDE</b><br />$contenu_cor';";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);

		echo("Erreur enregistrement de compte rendu : formulaire déjà posté précédemment.\nUne copie de sauvegarde a été créée en notice privée.");
	}
	else {
		echo("Erreur enregistrement de compte rendu : formulaire déjà posté précédemment.");
	}

	die();
}
$_SESSION['uid_prime'] = $uid_post;

//récupération du compte rendu
//$ctCompteRendu = new CahierTexteCompteRendu();
if ($id_ct != null) {
	//$criteria = new Criteria();
	//$criteria->add(CahierTexteCompteRenduPeer::ID_CT, $id_ct, "=");
	//$ctCompteRendus = $utilisateur->getCahierTexteCompteRendus($criteria);
	//$ctCompteRendu = $ctCompteRendus[0];
	$ctCompteRendu = CahierTexteCompteRenduQuery::create()->findPk($id_ct);
	if ($ctCompteRendu == null) {
		echo "Erreur enregistrement de compte rendu : Compte rendu non trouvé";
		die();
	}
	$groupe = $ctCompteRendu->getGroupe();

	if (!$groupe->belongsTo($utilisateur)) {
		echo "Erreur edition de compte rendu : le groupe n'appartient pas au professeur";
		die();
	}
} else {
	//si pas  du compte rendu précisé, récupération du groupe dans la requete et création d'un nouvel objet CahierTexteCompteRendu
	foreach ($utilisateur->getGroupes() as $group) {
		if ($id_groupe == $group->getId()) {
			$groupe = $group;
			break;
		}
	}// cela economise un acces db par rapport à  $current_group = GroupePeer::retrieveByPK($id_groupe), et permet de ne pas avoir a nettoyer les references de utilisateurs.
	if ($groupe == null) {
		echo("Erreur enregistrement de compte rendu : pas de groupe ou mauvais groupe spécifié");
		die;
	}

	//pas de notices, on lance une création de notice
	$ctCompteRendu = new CahierTexteCompteRendu();
	$ctCompteRendu->setIdGroupe($groupe->getId());
	$ctCompteRendu->setIdLogin($utilisateur->getLogin());
}

// interdire la modification d'un visa par le prof si c'est un visa
if ($ctCompteRendu->getVise() == 'y') {
	echo("Erreur enregistrement de compte rendu : Notice signée, edition impossible/");
	die();
}

if((isset($_POST['get_devoirs_du_jour']))&&($_POST['get_devoirs_du_jour']=='y')) {
	$sql="SELECT id_ct, contenu FROM ct_devoirs_entry WHERE id_groupe='$id_groupe' AND date_ct='".$date_ct."';";
	$res_ct=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ct)==0) {
		echo "Erreur : Aucun travail à importer.";
		die();
	}
	elseif(mysqli_num_rows($res_ct)==1) {
		$lig_ct=mysqli_fetch_object($res_ct);
		$contenu.=$lig_ct->contenu;
	}
	else {
		/*
		echo "Erreur : Plusieurs Travaux pour ce jour.";
		die();
		*/
		// Ou tout importer:
		while($lig_ct=mysqli_fetch_object($res_ct)) {
			$contenu.=$lig_ct->contenu;
		}
	}
}


//affectation des parametres de la requete à l'objet ctCompteRendu
$contenu_cor = traitement_magic_quotes(corriger_caracteres($contenu),'');
$contenu_cor = str_replace("\\r","",$contenu_cor);
$contenu_cor = str_replace("\\n","",$contenu_cor);
$contenu_cor = stripslashes($contenu_cor);
if ($contenu_cor == "" or $contenu_cor == "<br>") {$contenu_cor = "...";}

// Recuperation des images de formules mathematiques:
//if(getSettingValue('get_img_formules_math')=='y') {
	$contenu_cor=get_img_formules_math($contenu_cor, $id_groupe, "c");
//}

//=============================
// Corriger en chemins relatifs les chemins absolus débutant par getSettingValue('url_racine_gepi')...
// pas seulement: on peut avoir le nom DNS et l'IP dans le cas d'un gepi en DMZ ou plus généralement atteint en IP ou en nom DNS.
$url_absolues_gepi=getSettingValue("url_absolues_gepi");
if($url_absolues_gepi!="") {
	$contenu_cor=cdt_changer_chemin_absolu_en_relatif($contenu_cor);
}
//=============================
/*
$f=fopen("../backup/contenu_cdt.txt", "a+");
fwrite($f, "=======================================\n");
fwrite($f, strftime('%d/%M:%Y à %H:%M:%S')."\n");
fwrite($f, "\$contenu_cor=$contenu_cor\n\n");
$contenu_cor=cdt_copie_fichiers_archive_vers_cdt_courant($contenu_cor, "compte_rendu", $id_groupe);
fwrite($f, "\$contenu_cor=$contenu_cor\n\n");
fclose($f);
*/
$contenu_cor=cdt_copie_fichiers_archive_vers_cdt_courant($contenu_cor, "compte_rendu", $id_groupe);

$contenu_cor=a_href_target_blank($contenu_cor);

$contenu_cor=cdt_corrige_chemin_archive($contenu_cor);

//INSERT INTO setting SET name='url_visionneur_instrumentpoche', value='https://127.0.0.1/steph/gepi_git_trunk/cahier_texte_2/visionneur_instrumenpoche.php';
$url_visionneur_instrumentpoche=getSettingValue('url_visionneur_instrumentpoche');
if($url_visionneur_instrumentpoche!='') {
	$contenu_cor=preg_replace("#='visionneur_instrumenpoche.php#", "='$url_visionneur_instrumentpoche", $contenu_cor);
	$contenu_cor=preg_replace('#="visionneur_instrumenpoche.php#', '="'.$url_visionneur_instrumentpoche, $contenu_cor);
}

$ctCompteRendu->setContenu($contenu_cor);
$ctCompteRendu->setDateCt($date_ct);
$ctCompteRendu->setGroupe($groupe);
$ctCompteRendu->setHeureEntry($heure_entry);

//enregistrement de l'objet
$ctCompteRendu->save();

//==================================================
// Lors de l'enregistrement d'une nouvelle notice, on n'a pas encore de $id_ct
$id_ct=$ctCompteRendu->getIdCt();

$tag=isset($_POST['tag']) ? $_POST['tag'] : array();
// 20240111
$tag_commentaire=isset($_POST['tag_commentaire']) ? $_POST['tag_commentaire'] : array();
$tag_deja=array();

$sql="SELECT * FROM ct_tag WHERE id_ct='".$id_ct."' AND type_ct='c';";
//echo "$sql<br />";
/*
$f=fopen("../temp/gepi_debug_ct_dev.txt", "a+");
fwrite($f, "================================"."\n");
fwrite($f, $sql."\n");
fclose($f);
*/
$res_tag_existants=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_tag_existants)>0) {
	while($lig_tag=mysqli_fetch_object($res_tag_existants)) {
		if(!in_array($lig_tag->id_tag, $tag)) {
			$sql="DELETE FROM ct_tag WHERE id='".$lig_tag->id."';";
			//echo "$sql<br />";
			/*
			$f=fopen("../temp/gepi_debug_ct_dev.txt", "a+");
			fwrite($f, $sql."\n");
			fclose($f);
			*/
			$delete=mysqli_query($GLOBALS["mysqli"], $sql);
		}
		else {
			//$tag_deja[]=$lig_tag->id;
			$tag_deja['id'][]=$lig_tag->id;
			$tag_deja['id_tag'][]=$lig_tag->id_tag;
			// 20240111
			/*
			$f=fopen("../temp/gepi_debug_ct_dev.txt", "a+");
			fwrite($f, "\$tag_deja['id'][]=".$lig_tag->id."\n");
			fwrite($f, "\$tag_deja['id_tag'][]=".$lig_tag->id_tag."\n");
			fclose($f);
			*/
		}
	}
}
for($loop=0;$loop<count($tag);$loop++) {
	if((!isset($tag_deja['id_tag']))||(!in_array($tag[$loop], $tag_deja['id_tag']))) {
		$sql="INSERT INTO ct_tag SET id_ct='".$id_ct."', type_ct='c', id_tag='".$tag[$loop]."'";
		// 20240111
		if(isset($tag_commentaire[$tag[$loop]])) {
			$sql.=", commentaire='".mysqli_real_escape_string($GLOBALS["mysqli"], preg_replace('/"/', " ", $tag_commentaire[$tag[$loop]]))."'";
		}
		$sql.=";";
		//echo "$sql<br />";
		/*
		$f=fopen("../temp/gepi_debug_ct_dev.txt", "a+");
		fwrite($f, $sql."\n");
		fclose($f);
		*/
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	}
	// 20240111
	else {
		//$sql="UPDATE ct_tag SET id_ct='".$id_ct."', type_ct='c', id_tag='".$tag[$loop]."'";
		$sql="UPDATE ct_tag SET ";
		if(isset($tag_commentaire[$tag[$loop]])) {
			$sql.=" commentaire='".mysqli_real_escape_string($GLOBALS["mysqli"], preg_replace('/"/', " ", $tag_commentaire[$tag[$loop]]))."'";
		}
		else {
			$sql.=" commentaire=''";
		}
		$key_tag=array_search($tag[$loop], $tag_deja['id_tag']);
		if(is_integer($key_tag)) {
			$sql.=" WHERE id='".$tag_deja['id'][$key_tag]."';";
			/*
			//echo "$sql<br />";
			$f=fopen("../temp/gepi_debug_ct_dev.txt", "a+");
			fwrite($f, $sql."\n");
			fclose($f);
			*/
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
		}
	}
	/*
	$f=fopen("../temp/gepi_debug_ct_dev.txt", "a+");
	fwrite($f, "\$tag[$loop]=".$tag[$loop]."\n");
	fwrite($f, $sql."\n");
	fclose($f);
	*/
}
//==================================================
$suppr_doc_joint=isset($_POST['suppr_doc_joint']) ? $_POST['suppr_doc_joint'] : array();
foreach($suppr_doc_joint as $key => $id_document_a_supprimer) {
	$sql="SELECT * FROM ct_documents WHERE id_ct='".$id_ct."' AND id='".$id_document_a_supprimer."';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		$lig_suppr=mysqli_fetch_object($test);
		$emplacement_document=$lig_suppr->emplacement;
		if(file_exists($emplacement_document)) {
			@unlink($emplacement_document);
		}
		$sql="DELETE FROM ct_documents WHERE id_ct='".$id_ct."' AND id='".$id_document_a_supprimer."';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
	}
}

//==================================================
// 20210126:
if((getSettingAOui('cdt2_input_file_multiple'))&&(!empty($doc_file2['name'][0]))) {
	require_once("traite_doc.php");
	$total_max_size = getSettingValue("total_max_size");
	$max_size = getSettingValue("max_size");
	$multi = (isset($multisite) && $multisite == 'y') ? $_COOKIE['RNE'].'/' : NULL;
	if ((isset($multisite) && $multisite == 'y') && is_dir('../documents/'.$multi) === false){
		mkdir('../documents/'.$multi);
	}
	$dest_dir = '../documents/'.$multi.'cl'.$ctCompteRendu->getIdCt();

	// il y avait au plus trois documents joints dans l'interface de saisie
	$nb_doc_choisi='3';
	if(preg_match('/[0-9]{1,}/', getSettingValue('cdt_nb_doc_joints'))) {
		$nb_doc_choisi=getSettingValue('cdt_nb_doc_joints');
	}
	for ($index_doc=0; $index_doc < $nb_doc_choisi; $index_doc++) {
		if(!empty($doc_file2['tmp_name'][$index_doc])) {
			$file_path = ajout_fichier($doc_file2, $dest_dir, $index_doc, $id_groupe);
			if ($file_path != null) {
				//création de l'objet ctDocument
				$ctDocument = new CahierTexteCompteRenduFichierJoint();
				$ctDocument->setIdCt($ctCompteRendu->getIdCt());
				$ctDocument->setTaille($doc_file2['size'][$index_doc]);
				$ctDocument->setEmplacement($file_path);
				$ctDocument->setTitre(basename($file_path));

				if(isset($doc_masque2)) {
					$ctDocument->setVisibleEleveParent(false);
				}
				else {
					$ctDocument->setVisibleEleveParent(true);
				}

				$ctDocument->save();
				$ctCompteRendu->addCahierTexteCompteRenduFichierJoint($ctDocument);
				$ctCompteRendu->save();
			}
		}
	}
}


//==================================================
//traitement de telechargement de documents joints

$temoin_documents_joints=false;

$nb_doc_choisi='3';
if(preg_match('/[0-9]{1,}/', getSettingValue('cdt_nb_doc_joints'))) {
	$nb_doc_choisi=getSettingValue('cdt_nb_doc_joints');
}

for ($index_doc=0; $index_doc < $nb_doc_choisi; $index_doc++) {
	if (!empty($doc_file['name'][$index_doc])) {
		$temoin_documents_joints=true;
		break;
	}
}

if($temoin_documents_joints) {
	require_once("traite_doc.php");
	$total_max_size = getSettingValue("total_max_size");
	$max_size = getSettingValue("max_size");
        $multi = (isset($multisite) && $multisite == 'y') ? $_COOKIE['RNE'].'/' : NULL;
        if ((isset($multisite) && $multisite == 'y') && is_dir('../documents/'.$multi) === false){
            mkdir('../documents/'.$multi);
        }
	$dest_dir = '../documents/'.$multi.'cl'.$ctCompteRendu->getIdCt();

	for ($index_doc=0; $index_doc < $nb_doc_choisi; $index_doc++) {
		if(!empty($doc_file['tmp_name'][$index_doc])) {
			$file_path = ajout_fichier($doc_file, $dest_dir, $index_doc, $id_groupe);
			if ($file_path != null) {
				//création de l'objet ctDocument
				$ctDocument = new CahierTexteCompteRenduFichierJoint();
				$ctDocument->setIdCt($ctCompteRendu->getIdCt());
				$ctDocument->setTaille($doc_file['size'][$index_doc]);
				$ctDocument->setEmplacement($file_path);
				if ($doc_name[$index_doc] != null) {
					$ctDocument->setTitre(corriger_caracteres($doc_name[$index_doc]));
				} else {
					$ctDocument->setTitre(basename($file_path));
				}
				if(isset($doc_masque[$index_doc])) {
					$ctDocument->setVisibleEleveParent(false);
				}
				else {
					$ctDocument->setVisibleEleveParent(true);
				}
				$ctDocument->save();
				$ctCompteRendu->addCahierTexteCompteRenduFichierJoint($ctDocument);
				$ctCompteRendu->save();
			}
		}
	}
}

//traitement de changement de nom de fichiers joint
// Changement de nom
if (!empty($doc_name_modif) && (trim($doc_name_modif)) != '' && !empty($id_document)) {
	$titre = corriger_caracteres($doc_name_modif);
	$criteria = new Criteria(CahierTexteCompteRenduFichierJointPeer::DATABASE_NAME);
	$criteria->add(CahierTexteCompteRenduFichierJointPeer::ID, $id_document, '=');
	$documents = $ctCompteRendu->getCahierTexteCompteRenduFichierJoints($criteria);

	if (empty($documents)) {
		echo "Erreur enregistrement de compte rendu : document non trouvé.";
		die();
	}
	$document = $documents[0];
	if ($document == null) {
		echo "Erreur enregistrement de compte rendu : document non trouvé.";
		die();
	}
	$document->setTitre(corriger_caracteres($doc_name_modif));
	$document->save();
}

//traitement de la copie de fichier joint
if (isset($_REQUEST['ct_a_importer_class']) && isset($_REQUEST['id_ct_a_importer'])) {
        $classname = $_REQUEST["ct_a_importer_class"].'Query';
        if (class_exists($classname)) {
            $notice = call_user_func($classname .'::create')->findOneByPrimaryKey($_REQUEST["id_ct_a_importer"]);
            if ($notice != null && $ctCompteRendu!= null 
                    && $notice != $ctCompteRendu) {//pour la dernière condition, on évite de copier les fichiers joints d'une notice sur elle même
                $method = 'get'.$_REQUEST["ct_a_importer_class"].'FichierJoints';
                foreach($notice->$method() as $fichier_joint_modele) {
                    $fj = new CahierTexteCompteRenduFichierJoint();
                    $fj->setEmplacement($fichier_joint_modele->getEmplacement());
                    $fj->setTitre($fichier_joint_modele->getTitre());
                    $fj->setTaille($fichier_joint_modele->getTaille());
                    $fj->save();
                    $ctCompteRendu->addCahierTexteCompteRenduFichierJoint($fj);
                }
                $ctCompteRendu->save();
            }
        } 
}

echo ($ctCompteRendu->getIdCt());
$utilisateur->clearAllReferences();
?>
