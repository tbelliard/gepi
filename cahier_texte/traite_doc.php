<?php
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
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
$doc_name = isset($_POST["doc_name"]) ? $_POST["doc_name"] :(isset($_GET["doc_name"]) ? $_GET["doc_name"] :NULL);
$doc_name_modif = isset($_POST["doc_name_modif"]) ? $_POST["doc_name_modif"] :(isset($_GET["doc_name_modif"]) ? $_GET["doc_name_modif"] :NULL);
$id_document = isset($_POST["id_document"]) ? $_POST["id_document"] :(isset($_GET["id_document"]) ? $_GET["id_document"] :NULL);
$edit_devoir = isset($_POST["edit_devoir"]) ? $_POST["edit_devoir"] :(isset($_GET["edit_devoir"]) ? $_GET["edit_devoir"] :NULL);
if ($edit_devoir  == '') {$edit_devoir = NULL;}
if (empty($_FILES['doc_file'])) { $doc_file=''; } else { $doc_file=$_FILES['doc_file'];}


function deplacer_fichier_upload($source, $dest) {
    
    $ok = copy($source, $dest);
    if (!$ok) $ok = move_uploaded_file($source, $dest);
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

function creer_repertoire($base, $subdir) {
    $path = $base.'/'.$subdir;
    if (file_exists($path)) return true;

    @mkdir($path, 0777);
    @chmod($path, 0777);
    $ok = false;
    if ($f = @fopen("$path/.test", "w")) {
        @fputs($f, '<'.'?php $ok = true; ?'.'>');
        @fclose($f);
        include("$path/.test");
        if($ok) {
          if ($f = @fopen("$path/index.html", "w")) {
            @fputs($f, '<script type="text/javascript">document.location.replace("../../login.php")</script>');
            @fclose($f);
          }
        }
    }
    return $ok;
}


function ajout_doc($doc_file,$id_ct,$doc_name,$cpt_doc) {
    global $max_size, $total_max_size, $edit_devoir, $multisite;
    /* Vérification du type de fichier */
    //if (my_ereg("\.([^.]+)$", $doc_file['name'][$cpt_doc], $match)) {
    if (((function_exists("mb_ereg"))&&(mb_ereg("\.([^.]+)$", $doc_file['name'][$cpt_doc], $match)))||((function_exists("ereg"))&&(ereg("\.([^.]+)$", $doc_file['name'][$cpt_doc], $match)))) {
        $ext = corriger_caracteres(my_strtolower($match[1]));
        $ext = corriger_extension($ext);
    } else {
        $ext = '';
    }
    $query = "SELECT id_type FROM ct_types_documents WHERE extension='$ext' AND upload='oui'";

    $result = sql_query($query);
    if ($row = @sql_row($result,0)) {
        $id_type = $row[0];
    }
    else {
        return "Erreur : Ce type de fichier n'est pas autorisé en téléchargement.<br />
Si vous trouvez cela regrettable, contactez l'administrateur.<br />
Il pourra modifier ce paramétrage dans<br />
*Gestion des modules/Cahiers de textes/Types de fichiers autorisés en téléchargement*.";
        die();
    }

    /* Vérification de la taille du fichier */

    $sql = "select id_groupe from ct_entry where id_ct='$id_ct'";
    $id_groupe = sql_query1($sql);
    $max_size_ko = $max_size/1024;
    $taille = $doc_file['size'][$cpt_doc];
    if ($taille > $max_size) {
        return "Téléchargement impossible : taille maximale autorisée : ".$max_size_ko." Ko";
        die();
    }
    if ($taille == 0) {
        return "Le fichier sélectionné semble vide : transfert impossible.";
        die();
    }
    $query = "SELECT DISTINCT sum(taille) somme FROM ct_documents d, ct_entry e WHERE (e.id_groupe='".$id_groupe."' and e.id_ct = d.id_ct)";
    $total = sql_query1($query);
    if (($total+$taille) > $total_max_size) {
        return "Téléchargement impossible : espace disque disponible (".(($total_max_size - $total)/1024)." Ko) insuffisant.";
        die();
    }

    /* Recopier le fichier */

    $dest = '../documents/';
    $dossier = '';
    $multi = (isset($multisite) && $multisite == 'y') ? $_COOKIE['RNE'].'/' : NULL;
    if ((isset($multisite) && $multisite == 'y') && is_dir('../documents/'.$multi) === false){
        @mkdir('../documents/'.$multi);
        $dest .= $multi;
    }elseif((isset($multisite) && $multisite == 'y')){
        $dest .= $multi;
    }
    if (isset($edit_devoir))
        $dossier = "cl_dev".$_POST['id_groupe'];
    else
        $dossier = "cl".$_POST['id_groupe'];
    if (creer_repertoire($dest, $dossier)) {
      $dest .= $dossier.'/';
    } else {
      return "Problème d'écriture sur le répertoire. Veuillez signaler ce problème à l'administrateur du site";
      die();
    }
    $nom_sans_ext = mb_substr(basename($doc_file['name'][$cpt_doc]),0,mb_strlen(basename($doc_file['name'][$cpt_doc]))-(mb_strlen($ext)+1));
    $nom_sans_ext = my_ereg_replace("[^.a-zA-Z0-9_=-]+", "_", $nom_sans_ext);
    if (strstr($nom_sans_ext, "..")) {
        return "Problème de transfert : le fichier n'a pas pu être transféré sur le répertoire. Veuillez signaler ce problème à l'administrateur du site";
        die();
    }

    $n = 0;
    while (file_exists($newFile = $dest.$nom_sans_ext.($n++ ? '-'.$n : '').'.'.$ext));
    $dest_path = $newFile;

    if (!deplacer_fichier_upload($doc_file['tmp_name'][$cpt_doc], $dest_path)) {
        return "Problème de transfert : le fichier n'a pas pu être transféré sur le répertoire. Veuillez signaler ce problème à l'administrateur du site";
        die();
    }

    if ($doc_name[$cpt_doc] == '') $doc_name[$cpt_doc] = basename($newFile);

    $nouveau = false;
    if (!isset($id_document)) {
	if (isset($edit_devoir))
	    $query = "INSERT INTO ct_devoirs_documents SET taille='$taille', emplacement='$dest_path', id_ct_devoir='$id_ct', titre='".corriger_caracteres($doc_name[$cpt_doc])."'";
	else
	    $query = "INSERT INTO ct_documents SET taille='$taille', emplacement='$dest_path', id_ct='$id_ct', titre='".corriger_caracteres($doc_name[$cpt_doc])."'";
        sql_query($query);
        $id_document = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
        $nouveau = true;
    } else {
	if (isset($edit_devoir))
	    $query = "UPDATE ct_devoirs_documents SET taille='$taille', emplacement='$dest_path', id_ct_devoir='$id_ct', titre='$titre' WHERE id_document=$id_document";
	else
	    $query = "UPDATE ct_documents SET taille='$taille', emplacement='$dest_path', id_ct='$id_ct', titre='$titre' WHERE id_document=$id_document";
        sql_query($query);
    }
    return "Téléchargement réussi !";
}
/* Vérification : est-ce que l'utilisateur a les droits suffisant ? */
if (isset($edit_devoir))
    $sql = "select id_groupe from ct_devoirs_entry where id_ct='$id_ct'";
else
    $sql = "select id_groupe from ct_entry where id_ct='$id_ct'";

$id_groupe = sql_query1($sql);

$total_max_size = getSettingValue("total_max_size");
$max_size = getSettingValue("max_size");

if (!check_prof_groupe($_SESSION['login'],$id_groupe)) {
    $msg = "Accès non autorisé à ce document.";
} else {

// Ajout de un ou plusieurs documents
if (!empty($doc_file['tmp_name'][0])) {
    $cpt_doc='0';
    if(empty($doc_file['tmp_name'][$cpt_doc])) {
        $msg = ajout_doc($doc_file,$id_ct,$doc_name,$cpt_doc);
    } else {
         while(!empty($doc_file['tmp_name'][$cpt_doc])) {
            $msg = ajout_doc($doc_file,$id_ct,$doc_name,$cpt_doc);
            $cpt_doc=$cpt_doc+1;
         }
    }
}

// Suppression d'un document
if ((isset($_GET["action"])) and ($_GET["action"] == 'del')) {
    $id_document = $_GET['id_del'];
    if (isset($edit_devoir))
	$sql = "select emplacement from ct_devoirs_documents where (id = '$id_document' and id_ct_devoir='$id_ct')";
    else
	$sql = "select emplacement from ct_documents where (id = '$id_document' and id_ct='$id_ct')";
    $empl = sql_query1($sql);
    if ($empl == -1) {
        $msg = "Il n' a pas de document à supprimer.";
    } else {
        $del = @unlink($empl);
        if (file_exists($empl)) {
            $msg = "Problème : le document n'a pa pu être supprimé. Contactez l'administrateur du site.";
        } else {
	    if (isset($edit_devoir))
		$sql_query = "delete from ct_devoirs_documents where id = '$id_document'";
	    else
		$sql_query = "delete from ct_documents where id = '$id_document'";
            if (sql_query($sql_query)) {
                $msg = "Supression réussie";
            } else {
                $msg = "Un problème est survenu lors de la suppression du document. Contactez l'administrateur du site.";
            }
        }
    }
}

// Changement de nom
if (!empty($doc_name_modif) and !empty($id_document)) {
    if ((trim($doc_name_modif)) != '') {
	if (isset($edit_devoir))
	    $query = "UPDATE ct_devoirs_documents SET titre='".corriger_caracteres($doc_name_modif)."' WHERE id='".$_POST['id_document']."'";
	else
	    $query = "UPDATE ct_documents SET titre='".corriger_caracteres($doc_name_modif)."' WHERE id='".$_POST['id_document']."'";
        if (sql_query($query)) {
            $msg = "Changement de nom réussi";
        } else {
            $msg = "Un problème est survenu lors du changement de nom du document. Contactez l'administrateur du site.";
        }

    } else {
        $msg = "Veuillez choisir un nom valide.";
    }
}

}
?>
