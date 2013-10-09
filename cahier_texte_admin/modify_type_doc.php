<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


// Enregistrement d'une modification ou d'un ajout
if (isset($_POST['modif'])) {
	check_token();
  if ($_POST['id'] !='ajout') {
     $req = sql_query("UPDATE ct_types_documents SET extension='".$_POST['ext']."', titre='".$_POST['description']."', upload='".$_POST['upload']."' WHERE id_type='".$_POST['id']."'");
     if ($req) $msg = "Les modifications ont été enregistrées."; else $msg = "Il y a eu un problème lors de l'enregistrement.";
  } else {
     $ext = $_POST['ext'];
     if ($ext != '') {
        $req = sql_query("INSERT INTO ct_types_documents SET extension='".$ext."', titre='".$_POST['description']."', upload='".$_POST['upload']."'");
        if ($req) $msg = "L'enregistrement a bien été effectué."; else $msg = "Il y a eu un problème lors de l'enregistrement.";
     } else {
        $msg = "Enregistrement impossible. Veuillez définir une extension correcte.";
     }
  }
}

// Suppression des types selectionnés
if (isset($_POST['bouton_sup'])) {
	check_token();
  $query = "SELECT id_type FROM ct_types_documents";
  $result = sql_query($query);
  $nb_sup = "0";
  $ok_sup = 'yes';
  for ($i=0; ($row=sql_row($result,$i)); $i++) {
      $id = $row[0];
      $temp = "sup_".$id;
      if (isset($_POST[$temp])) {
        $req = sql_query("DELETE FROM ct_types_documents WHERE id_type='".$id."'");
        $nb_sup++;
        if (!($req)) $ok_sup = 'no';
      }
  }
  if ($nb_sup == "0") {
     $msg = "Aucune suppression n'a été effectuée.";
  } else if ($nb_sup == "1") {
     if ($ok_sup=='yes') $msg = "La suppression a été effectuée avec succès."; else $msg = "Il y a eu un problème lors de la suppression.";
  } else {
     if ($ok_sup=='yes') $msg = "Les suppressions ont été effectuées avec succès."; else $msg = "Il y a eu un problème lors de la suppression.";
  }

}

if (isset($_POST['reinit_assoc_fichiers'])) {
	$msg="";

	check_token();
	$tab_sql=array();
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Adobe Illustrator', extension='ai', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='AIFF', extension='aiff', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Windows Media', extension='asf', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Windows Media', extension='avi', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='BMP', extension='bmp', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='BZip', extension='bz2', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='C source', extension='c', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Debian', extension='deb', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Word', extension='doc', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Word', extension='docx', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='LaTeX DVI', extension='dvi', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='PostScript', extension='eps', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='GeoGebra', extension='ggb', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='GIF', extension='gif', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='gr', extension='gr', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='GZ', extension='gz', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='C header', extension='h', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='HTML', extension='html', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='JPEG', extension='jpg', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Midi', extension='mid', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='QuickTime', extension='mov', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='MP3', extension='mp3', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='MPEG', extension='mpg', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Base de données OpenDocument', extension='odb', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Dessin OpenDocument', extension='odg', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Présentation OpenDocument', extension='odp', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Classeur OpenDocument', extension='ods', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Texte OpenDocument', extension='odt', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Ogg', extension='ogg', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Pascal', extension='pas', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='PDF', extension='pdf', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='PNG', extension='png', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='PowerPoint', extension='ppt', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='PowerPoint', extension='pptx', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='PostScript', extension='ps', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Photoshop', extension='psd', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='QuickTime', extension='qt', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='RealAudio', extension='ra', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='RealAudio', extension='ram', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='RealAudio', extension='rm', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='RTF', extension='rtf', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='StarOffice', extension='sdd', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='StarOffice', extension='sdw', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Stuffit', extension='sit', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Flash', extension='swf', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='OpenOffice Calc', extension='sxc', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='OpenOffice Impress', extension='sxi', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='OpenOffice', extension='sxw', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='LaTeX', extension='tex', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='TGZ', extension='tgz', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='TIFF', extension='tif', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='texte', extension='txt', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='WAV', extension='wav', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Windows Media', extension='wmv', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='GIMP multi-layer', extension='xcf', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Excel', extension='xls', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Excel', extension='xlsx', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='XML', extension='xml', upload='oui';";
	$tab_sql[]="INSERT INTO ct_types_documents SET titre='Zip', extension='zip', upload='oui';";

	$sql="TRUNCATE ct_types_documents;";
	$menage=mysql_query($sql);

	$nb_reg=0;
	for($loop=0;$loop<count($tab_sql);$loop++) {
		$insert=mysql_query($tab_sql[$loop]);
		if(!$insert) {$msg.="Erreur lors de l'insertion : <br />".$tab_sql[$loop]."<br />";} else {$nb_reg++;}
	}

	if($nb_reg>0) {$msg.="$nb_reg enregistrement(s) effectué(s).<br />";}
}

//===========================================================
// header
$titre_page = "Types de fichiers autorisés en téléchargement";
require_once("../lib/header.inc.php");
//===========================================================
//debug_var();

if (isset($_GET['id'])) {
	check_token(false);
  // Ajout ou modification d'un type de fichier
  ?>
  <p class="bold"><a href="modify_type_doc.php?a=a<?php echo add_token_in_url();?>"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
  <?php
  if ($_GET['id']=='ajout') {
     echo "<h2>Type de fichier autorisé en téléchargement - Ajout d'un type de fichier</h2>";
     $ext = '';
     $description = '';
     $upload = 'oui';
  } else {
     echo "<h2>Type de fichier autorisé en téléchargement - Modification</h2>";
     $query = "SELECT extension, titre, upload  FROM ct_types_documents WHERE id_type='".$_GET['id']."' ORDER BY extension";
     $result = sql_query($query);
     $row=sql_row($result,0);
     $ext = $row[0];
     $description = $row[1];
     $upload = $row[2];
  }

  ?>
  <form action="modify_type_doc.php" name="formulaire1" method="post">
<?php
	echo add_token_field();
?>
  <table>
  <tr><td>Extension : </td><td><input type="text" name="ext" value="<?php echo $ext; ?>" size="20" /></td></tr>
  <tr><td>Type/Description : </td><td><input type="text" name="description" value="<?php echo $description; ?>" size="20" /></td></tr>
  <tr><td>Autorisé : </td><td><select name="upload" size="1">
  <option <?php if ($upload=='oui') echo "selected"; ?>>oui</option>
  <option <?php if ($upload=='non') echo "selected"; ?>>non</option>
  </select></td></tr>
  <tr><td></td><td><input type="submit" name="modif" value="Enregistrer" /></td></tr>
  </table>
  <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
  </form>
  <?php
} else {
  // Affichage du tableau complet
  ?>

  <form action="modify_type_doc.php" name="formulaire_reinit" method="post">
<?php
	echo add_token_field();
?>
  <p class='bold'><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>|<a href="modify_type_doc.php?id=ajout<?php echo add_token_in_url();?>"> Ajouter un type de fichier </a>
  | <input type='submit' name='reinit_assoc_fichiers' value='Réinitialiser les extensions autorisées' />
  </p>
  </form>

  <h2>Types de fichiers autorisés en téléchargement</h2>
  <form action="modify_type_doc.php" name="formulaire2" method="post">
<?php
	echo add_token_field();
?>
  <table border="1" class='boireaus' summary='Choix des extensions'>
<tr>
<th><b>Extension</b></th>
<th><b>Type/Description</b></th>
<th><b>Autorisé</b></th>
<th><input type="submit" name="bouton_sup" value="Supprimer" onclick="return confirmlink(this, '', 'Confirmation de la suppression')" /></th>
</tr>
  <?php
  $alt=1;
  $query = "SELECT id_type, extension, titre, upload  FROM ct_types_documents ORDER BY extension";
  $result = sql_query($query);
  for ($i=0; ($row=sql_row($result,$i)); $i++) {
      $alt=$alt*(-1);
      $id = $row[0];
      $ext = $row[1];
      ($row[2]!='') ? $description = $row[2]:$description="-";
      $upload = $row[3];
      echo "<tr class='lig$alt white_hover'><td><a href='modify_type_doc.php?id=".$id.add_token_in_url()."'>".$ext."</a></td><td>".$description."</td><td>".$upload."</td><td><input type=\"checkbox\" name=\"sup_".$id."\" /></td></tr>";
  }
  echo "</table></form>";
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
