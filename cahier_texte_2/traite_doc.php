<?php
/*
 * @version: $Id: traite_doc.php 1360 2008-01-13 20:03:09Z jjocal $
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

function deplacer_fichier_upload($source, $dest) {
	$ok = @copy($source, $dest);
	if (!$ok) $ok = @move_uploaded_file($source, $dest);
	return $ok;
}


function corriger_extension($ext) {
	switch ($ext) {
		case 'htm':
			return 'html';
		case 'jpeg':
			return 'jpg';
		case 'tiff':
			return 'tif';
		default:
			return $ext;
	}
}

function creer_repertoire($path) {
	if (file_exists($path)) return true;

	@mkdir($path, 0777);
	@chmod($path, 0777);
	$ok = false;
	if ($f = @fopen("$path/.test", "w")) {
		@fputs($f, '<'.'?php $ok = true; ?'.'>');
		@fclose($f);
		include("$path/.test");
	}
	return $ok;
}


function ajout_fichier($doc_file, $dest, $cpt_doc, $id_groupe) {
	global $max_size, $total_max_size;
	/* Vérification du type de fichier */
	$ext = '';
	//if (my_ereg("\.([^.]+)$", $doc_file['name'][$cpt_doc], $match)) {
    if (((function_exists("mb_ereg"))&&(mb_ereg("\.([^.]+)$", $doc_file['name'][$cpt_doc], $match)))||((function_exists("ereg"))&&(ereg("\.([^.]+)$", $doc_file['name'][$cpt_doc], $match)))) {
		$ext = corriger_caracteres(strtolower($match[1]));
		$ext = corriger_extension($ext);
	}
	$query = "SELECT id_type FROM ct_types_documents WHERE extension='$ext' AND upload='oui'";
	$result = sql_query($query);
	if ($row = @sql_row($result,0)) {
		$id_type = $row[0];
	} else {
		echo ("Erreur : Ce type de fichier n'est pas autorisé en téléchargement");
		die();
	}

	/* Vérification de la taille du fichier */
	$max_size_ko = $max_size/1024;
	$taille = $doc_file['size'][$cpt_doc];
	if ($taille > $max_size) {
		echo "Erreur : Téléchargement impossible : taille maximale autorisée : ".$max_size_ko." Ko";
		die();
	}
	if ($taille == 0) {
		echo "Le fichier sélectionné semble vide : transfert impossible.";
		die();
	}
	$query = "SELECT DISTINCT sum(taille) somme FROM ct_documents d, ct_entry e WHERE (e.id_groupe='".$id_groupe."' and e.id_ct = d.id_ct)";
	$total = sql_query1($query);
	if (($total+$taille) > $total_max_size) {
		echo "Erreur : Téléchargement impossible : espace disque disponible (".(($total_max_size - $total)/1024)." Ko) insuffisant.";
		die();
	}

	/* Crétion du répertoire de destination */
	if (!creer_repertoire($dest)) {
		echo "Erreur : Problème d'écriture sur le répertoire. Veuillez signaler ce problème à l'administrateur du site";
		die();
	}
	
	/* Recopier le fichier */
	$nom_sans_ext = substr(basename($doc_file['name'][$cpt_doc]),0,strlen(basename($doc_file['name'][$cpt_doc]))-(strlen($ext)+1));
	$nom_sans_ext = my_ereg_replace("[^.a-zA-Z0-9_=-]+", "_", $nom_sans_ext);
	if (strstr($nom_sans_ext, "..")) {
		echo "Erreur : Problème de transfert : le fichier n'a pas pu être transféré sur le répertoire. Veuillez signaler ce problème à l'administrateur du site";
		die();
	}

	$n = 0;
	while (file_exists($newFile = $dest."/".$nom_sans_ext.($n++ ? '-'.$n : '').'.'.$ext));
	$dest_file_path = $newFile;

	if (!deplacer_fichier_upload($doc_file['tmp_name'][$cpt_doc], $dest_file_path)) {
		echo "Erreur : Problème de transfert : le fichier n'a pas pu être transféré sur le répertoire. Veuillez signaler ce problème à l'administrateur du site";
		die();
	}

	return $dest_file_path;
}

//// Ajout de un ou plusieurs documents
//if (!empty($doc_file['tmp_name'][0])) {
//	$cpt_doc='0';
//	if(empty($doc_file['tmp_name'][$cpt_doc])) {
//		$msg = ajout_doc($doc_file,$id_ct,$doc_name,$cpt_doc);
//	} else {
//		while(!empty($doc_file['tmp_name'][$cpt_doc])) {
//			$msg = ajout_doc($doc_file,$id_ct,$doc_name,$cpt_doc);
//			$cpt_doc=$cpt_doc+1;
//		}
//	}
//}
//
//// Suppression d'un document
//if ((isset($_GET["action"])) and ($_GET["action"] == 'del')) {
//	$id_document = $_GET['id_del'];
//	$sql = "select emplacement from ct_documents where (id = '$id_document' and id_ct='$id_ct')";
//	$empl = sql_query1($sql);
//	if ($empl == -1) {
//		$msg = "Il n' a pas de document à supprimer.";
//	} else {
//		$del = @unlink($empl);
//		if (file_exists($empl)) {
//			$msg = "Problème : le document n'a pa pu être supprimé. Contactez l'administrateur du site.";
//		} else {
//			if (sql_query("delete from ct_documents where id = '$id_document'")) {
//				$msg = "Supression réussie";
//			} else {
//				$msg = "Un problème est survenu lors de la suppression du document. Contactez l'administrateur du site.";
//			}
//		}
//	}
//}
//
//// Changement de nom
//if (!empty($doc_name_modif) and !empty($id_document)) {
//	if ((trim($doc_name_modif)) != '') {
//		$query = "UPDATE ct_documents SET titre='".corriger_caracteres($doc_name_modif)."' WHERE id='".$_POST['id_document']."'";
//		if (sql_query($query)) {
//			$msg = "Changement de nom réussi";
//		} else {
//			$msg = "Un problème est survenu lors du changement de nom du document. Contactez l'administrateur du site.";
//		}
//
//	} else {
//		$msg = "Veuillez choisir un nom valide.";
//	}
//}
?>