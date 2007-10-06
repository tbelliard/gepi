<?php
/*
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];

if (isset($is_posted) and ($is_posted == '2')) {
    if ($quelles_classes == 'certaines') {
        //
        // On efface les enregistrements liés à la session en cours
        //
        mysql_query("DELETE FROM tempo WHERE num = '".SESSION_ID()."'");
        //
        // On efface les enregistrements obsolètes
        //
        $call_data = mysql_query("SELECT * FROM tempo");
        $nb_enr = mysql_num_rows($call_data);
        $nb = 0;
        while ($nb < $nb_enr) {
            $num = mysql_result($call_data, $nb, 'num');
            $test = mysql_query("SELECT * FROM log WHERE SESSION_ID = '$num'");
            $nb_en = mysql_num_rows($test);
            if ($nb_en == 0) {
                mysql_query("DELETE FROM tempo WHERE num = '$num'");
            }
        $nb++;
        }

        $classes_list = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
        $nb = mysql_num_rows($classes_list);
        $i ='0';
        while ($i < $nb) {
            $id_classe = mysql_result($classes_list, $i, 'id');
            $tempo = "case_".$id_classe;
            $temp = isset($_POST[$tempo])?$_POST[$tempo]:NULL;
            if ($temp == 'yes') {
                $periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
                $nb_periode = mysql_num_rows($periode_query);
                $call_reg = mysql_query("insert into tempo Values('$id_classe','$nb_periode', '".SESSION_ID()."')");
            }
        $i++;
        }
    }
}

//if (isset($is_posted) and ($is_posted == '1') and empty($_FILES["photo"])) {
if (isset($is_posted) and ($is_posted == '1')) {
    $calldata = mysql_query("SELECT * FROM eleves");
    $nombreligne = mysql_num_rows($calldata);
    $i = 0;
    $liste_cible = '';
    while ($i < $nombreligne){
        $eleve_login = mysql_result($calldata, $i, "login");
        $delete_login = 'delete_'.$eleve_login;
        $del_eleve = isset($_POST[$delete_login])?$_POST[$delete_login]:NULL;
        if ($del_eleve == 'yes') {
            $liste_cible = $liste_cible.$eleve_login.";";
        }
    $i++;
    }
    //header("Location: ../lib/confirm_query.php?liste_cible=$liste_cible&amp;action=del_eleve");
    header("Location: ../lib/confirm_query.php?liste_cible=$liste_cible&action=del_eleve");
}
// pour l'envoi des photos du trombinoscope

 if (empty($_POST['action']) and empty($_GET['action'])) { $action = ""; }
    else { if (empty($_POST['action'])){$action = ""; } if (empty($_GET['action'])){$action = $_POST['action'];} }
 if (empty($_POST['total_photo']) and empty($_GET['total_photo'])) { $total_photo = ""; }
    else { if (empty($_POST['total_photo'])){$total_photo = ""; } if (empty($_GET['total_photo'])){$total_photo = $_POST['total_photo'];} }
 if (empty($_FILES['photo'])) { $photo = ""; } else { $photo = $_FILES['photo']; }
 if (empty($_POST['quiestce'])) { $quiestce = ""; } else { $quiestce = $_POST['quiestce']; }

function ImageFlip($imgsrc, $type)
	{
	  //source de cette fonction : http://www.developpez.net/forums/showthread.php?t=54169
	   $width = imagesx($imgsrc);
	   $height = imagesy($imgsrc);

	   $imgdest = imagecreatetruecolor($width, $height);

	   switch( $type )
		   {
		   // mirror wzgl. osi
		   case IMAGE_FLIP_HORIZONTAL:
			   for( $y=0 ; $y<$height ; $y++ )
				   imagecopy($imgdest, $imgsrc, 0, $height-$y-1, 0, $y, $width, 1);
			   break;

		   case IMAGE_FLIP_VERTICAL:
			   for( $x=0 ; $x<$width ; $x++ )
				   imagecopy($imgdest, $imgsrc, $width-$x-1, 0, $x, 0, 1, $height);
			   break;

		   case IMAGE_FLIP_BOTH:
			   for( $x=0 ; $x<$width ; $x++ )
				   imagecopy($imgdest, $imgsrc, $width-$x-1, 0, $x, 0, 1, $height);

			   $rowBuffer = imagecreatetruecolor($width, 1);
			   for( $y=0 ; $y<($height/2) ; $y++ )
				   {
				   imagecopy($rowBuffer, $imgdest  , 0, 0, 0, $height-$y-1, $width, 1);
				   imagecopy($imgdest  , $imgdest  , 0, $height-$y-1, 0, $y, $width, 1);
				   imagecopy($imgdest  , $rowBuffer, 0, $y, 0, 0, $width, 1);
				   }

			   imagedestroy( $rowBuffer );
			   break;
		   }

	   return( $imgdest );
	}

function ImageRotateRightAngle( $imgSrc, $angle )
{
	//source de cette fonction : http://www.developpez.net/forums/showthread.php?t=54169
	$angle = min( ( (int)(($angle+45) / 90) * 90), 270 );
	if( $angle == 0 )
	return( $imgSrc );
	$srcX = imagesx( $imgSrc );
	$srcY = imagesy( $imgSrc );

	switch( $angle )
	{
		case 90:
		$imgDest = imagecreatetruecolor( $srcY, $srcX );
		for( $x=0; $x<$srcX; $x++ )
		for( $y=0; $y<$srcY; $y++ )
		imagecopy($imgDest, $imgSrc, $srcY-$y-1, $x, $x, $y, 1, 1);
		break;

		case 180:
		$imgDest = ImageFlip( $imgSrc, IMAGE_FLIP_BOTH );
		break;

		case 270:
		$imgDest = imagecreatetruecolor( $srcY, $srcX );
		for( $x=0; $x<$srcX; $x++ )
		for( $y=0; $y<$srcY; $y++ )
		imagecopy($imgDest, $imgSrc, $y, $srcX-$x-1, $x, $y, 1, 1);
		break;
	}

		return( $imgDest );
}


function deplacer_fichier_upload($source, $dest) {
    $ok = @copy($source, $dest);
    if (!$ok) $ok = @move_uploaded_file($source, $dest);
    return $ok;
}


function test_ecriture_backup() {
    $ok = 'no';
    if ($f = @fopen("../photos/eleves/test", "w")) {
        @fputs($f, '<'.'?php $ok = "yes"; ?'.'>');
        @fclose($f);
        include("../photos/eleves/test");
        $del = @unlink("../photos/eleves/test");
    }
    return $ok;
}

if (isset($action) and ($action == 'depot_photo') and $total_photo != 0)  {
 $cpt_photo = 0;
 while($cpt_photo < $total_photo)
  {
	if($_FILES['photo']['type'][$cpt_photo] != "")
	{
    		$sav_photo = isset($_FILES["photo"]) ? $_FILES["photo"] : NULL;
		if (!isset($sav_photo['tmp_name'][$cpt_photo]) or ($sav_photo['tmp_name'][$cpt_photo] =='')) {
			$msg = "Erreur de téléchargement niveau 1.";
		} else if (!file_exists($sav_photo['tmp_name'][$cpt_photo])) {
        		$msg = "Erreur de téléchargement niveau 2.";
		} else if ((!preg_match('/jpg$/',$sav_photo['name'][$cpt_photo])) and $sav_photo['type'][$cpt_photo] == "image/jpeg"){
		        $msg = "Erreur : seuls les fichiers ayant l'extension .jpg sont autorisés.";
		} else {
		        $dest = "../photos/eleves/";
			$n = 0;
		        //$nom_corrige = ereg_replace("[^.a-zA-Z0-9_=-]+", "_", $sav_photo['name'][$cpt_photo]);
		        if (!deplacer_fichier_upload($sav_photo['tmp_name'][$cpt_photo], "../photos/eleves/".$quiestce[$cpt_photo].".jpg")) {
		            $msg = "Problème de transfert : le fichier n'a pas pu être transféré sur le répertoire photos/eleves/";
		        } else {
		        	    $msg = "Téléchargement réussi.";
				if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
					// si le redimensionnement des photos est activé on redimenssionne
					$source = imagecreatefromjpeg("../photos/eleves/".$quiestce[$cpt_photo].".jpg"); // La photo est la source
					if (getSettingValue("active_module_trombinoscopes_rt")=='') { $destination = imagecreatetruecolor(120, 160); } // On crée la miniature vide
					if (getSettingValue("active_module_trombinoscopes_rt")!='') { $destination = imagecreatetruecolor(160, 120); } // On crée la miniature vide

					//rotation de l'image si choix différent de rien
					//if (getSettingValue("active_module_trombinoscopes_rt")!='') { $degrees = getSettingValue("active_module_trombinoscopes_rt"); /* $destination = imagerotate($destination,$degrees); */$destination = ImageRotateRightAngle($destination,$degrees); }

					// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
					$largeur_source = imagesx($source);
					$hauteur_source = imagesy($source);
					$largeur_destination = imagesx($destination);
					$hauteur_destination = imagesy($destination);

					// On crée la miniature
					imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);
					if (getSettingValue("active_module_trombinoscopes_rt")!='') { $degrees = getSettingValue("active_module_trombinoscopes_rt"); /* $destination = imagerotate($destination,$degrees); */$destination = ImageRotateRightAngle($destination,$degrees); }
					// On enregistre la miniature sous le nom "mini_couchersoleil.jpg"
					imagejpeg($destination, "../photos/eleves/".$quiestce[$cpt_photo].".jpg",100);
					}
			       }
       }
    }
    $cpt_photo = $cpt_photo + 1;
  }
}
// fin de l'envoi des photos du trombinoscope

//**************** EN-TETE *****************
$titre_page = "Gestion des élèves";
require_once("../lib/header.inc");
//************** FIN EN-TETE *****************
?>

<script type='text/javascript' language="JavaScript">
    function verif1() {
		//document.formulaire.quelles_classes[2].checked = true;
		document.formulaire.quelles_classes[3].checked = true;
    }
    function verif2() {
    <?php
		$classes_list = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
		$nb = mysql_num_rows($classes_list);
		$k = '0';
		while ($k < $nb) {
			$id_classe = mysql_result($classes_list, $k, 'id');
			?>
				document.formulaire.case_<?php echo $id_classe; ?>.checked = false;
			<?php
		$k++;
		}
    ?>
    }
</script>

<?php
if ($_SESSION['statut'] == 'administrateur')
    $retour = "../accueil_admin.php";
else
    $retour = "../accueil.php";
if (isset($quelles_classes)) $retour = "index.php";
echo "<p class=bold><a href=\"".$retour."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a> | ";



if(!getSettingValue('conv_new_resp_table')){
	$sql="SELECT 1=1 FROM responsables";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0){
		echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";

		if($_SESSION['statut']=="administrateur"){
			echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
		}
		else{
			echo "<p><a href=\"javascript:centrerpopup('../gestion/contacter_admin.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">Contactez l'administrateur</a></p>\n";
		}

		require("../lib/footer.inc.php");
		die();
	}

	$sql="SHOW COLUMNS FROM eleves LIKE 'ele_id'";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0){
		echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";

		if($_SESSION['statut']=="administrateur"){
			echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
		}
		else{
			echo "<p><a href=\"javascript:centrerpopup('../gestion/contacter_admin.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">Contactez l'administrateur</a></p>\n";
		}

		require("../lib/footer.inc.php");
		die();
	}
	else{
		$sql="SELECT 1=1 FROM eleves WHERE ele_id=''";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";

		if($_SESSION['statut']=="administrateur"){
			echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
		}
		else{
			echo "<p><a href=\"javascript:centrerpopup('../gestion/contacter_admin.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">Contactez l'administrateur</a></p>\n";
		}

			require("../lib/footer.inc.php");
			die();
		}
	}
}


?>
<!--a href="modify_eleve.php?mode=unique">Ajouter un élève à la base (simple)</a> |
 <a href="modify_eleve.php?mode=multiple">Ajouter des élèves à la base (à la chaîne)</a-->
<a href="add_eleve.php?mode=unique">Ajouter un élève à la base (simple)</a> |
 <a href="add_eleve.php?mode=multiple">Ajouter des élèves à la base (à la chaîne)</a>
<?php
$droits = @sql_query1("SELECT ".$_SESSION['statut']." FROM droits WHERE id='/eleves/import_eleves_csv.php'");
if ($droits == "V") {
   echo " | <a href=\"import_eleves_csv.php\" title=\"Télécharger le fichier des noms, prénoms, identifiants GEPI et classes\">Télécharger le fichier des élèves au format csv.</a>\n";

	if(getSettingValue("import_maj_xml_sconet")==1){
		echo " | <a href=\"../responsables/maj_import.php\">Mettre à jour depuis Sconet</a>\n";
	}
}
?>
</p>
<?php
echo "<center><p class='grand'>Visualiser \ modifier une fiche élève</p></center>\n";
$req = mysql_query("SELECT login FROM eleves");
$test = mysql_num_rows($req);
if ($test == '0') {
    echo "<p class='grand'>Attention : il n'y a aucun élève dans la base GEPI !</p>\n";
    echo "<p>Vous pouvez ajouter des élèves à la base en cliquant sur l'un des liens ci-dessus, ou bien directement <br /><a href='../initialisation/index.php'>importer les élèves et les classes à partir de fichiers GEP</a></p>\n";
	require("../lib/footer.inc.php");
    die();
}

if (!isset($quelles_classes)) {
    ?>
    <form enctype="multipart/form-data" action="index.php" method="post" name="formulaire">
    <!--table cellpadding="5"-->
    <table cellpadding="5" width="100%" border='0'>
    <!--tr><td-->

	<?php

	echo "<tr>\n";
	echo "<td>\n";
	echo "<input type='radio' name='quelles_classes' value='toutes' onclick='verif2()' checked />\n";
	echo "</td>\n";
	echo "<td>\n";
    echo "<span class='norme'>Tous les élèves.</span><br />";
	echo "</td>\n";
	echo "</tr>\n";

/*
        $calldata = mysql_query("select e.* from eleves e
        LEFT JOIN j_eleves_classes c ON c.login=e.login
        where c.login is NULL
        ORDER BY $order_type
        ");
*/


	$sql="SELECT 1=1 FROM eleves e
        LEFT JOIN j_eleves_classes c ON c.login=e.login
        where c.login is NULL;";
	$test_na=mysql_query($sql);
	//if($test_na){
	if(mysql_num_rows($test_na)==0){
		echo "<tr>\n";
		echo "<td>\n";
		echo "&nbsp;\n";
		echo "</td>\n";
		echo "<td>\n";

	    echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='na' onclick='verif2()' /></span>\n";

		echo "<span class='norme'>Tous les élèves sont affectés dans une classe.</span><br />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr>\n";
		echo "<td>\n";
	    echo "<input type='radio' name='quelles_classes' value='na' onclick='verif2()' />\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<span class='norme'>Les élèves non affectés à une classe (<i>".mysql_num_rows($test_na)."</i>).</span><br />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}

	$sql="SELECT 1=1 FROM eleves WHERE elenoet='' OR no_gep='';";
	$test_incomplet=mysql_query($sql);
	if(mysql_num_rows($test_incomplet)==0){
		echo "<tr>\n";
		echo "<td>\n";
		echo "&nbsp;\n";
		echo "</td>\n";
		echo "<td>\n";

	    echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='incomplet' onclick='verif2()' /></span>\n";

		echo "<span class='norme'>Tous les élèves ont leur Elenoet et leur Numéro national (INE) renseigné.</span><br />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr>\n";
		echo "<td>\n";
	    echo "<input type='radio' name='quelles_classes' value='incomplet' onclick='verif2()' />\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<span class='norme'>Les élèves dont l'Elenoet ou le Numéro national (INE) n'est pas renseigné (<i>".mysql_num_rows($test_incomplet)."</i>).</span><br />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}

    $classes_list = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
    $nb = mysql_num_rows($classes_list);
    if ($nb !=0) {
		echo "<tr>\n";
		echo "<td valign='top'>\n";

        echo "<input type=\"radio\" name=\"quelles_classes\" value=\"certaines\" />";

		echo "</td>\n";
		echo "<td valign='top'>\n";

        echo "<span class = \"norme\">Seulement les élèves des classes sélectionnées ci-dessous : </span><br />\n";

			$nb_class_par_colonne=round($nb/3);
			//echo "<table width='100%' border='1'>\n";
			echo "<table width='100%'>\n";
			echo "<tr valign='top' align='center'>\n";

			$i = '0';

			echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
			echo "<td align='left'>\n";

			while ($i < $nb) {
			$id_classe = mysql_result($classes_list, $i, 'id');
			$temp = "case_".$id_classe;
			$classe = mysql_result($classes_list, $i, 'classe');

			if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
				echo "</td>\n";
				//echo "<td style='padding: 0 10px 0 10px'>\n";
				echo "<td align='left'>\n";
			}

			//echo "<span class = \"norme\"><input type='checkbox' name='$temp' value='yes' onclick=\"verif1()\" />";
			//echo "Classe : $classe </span><br />\n";
			echo "<input type='checkbox' name='$temp' value='yes' onclick=\"verif1()\" />";
			echo "Classe : $classe<br />\n";
			$i++;
			}
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

		echo "</td>\n";
		echo "</tr>\n";
    }

    ?>
	</table>
    <input type="hidden" name="is_posted" value="2" />
    <!--/td-->
    <!--td valign="top" align="center">
    <input type=submit value=Valider />
    </td-->
    <!--/tr></table-->
    <p align='center'><input type="submit" value="Valider" /></p>
    </form>
    <?php
} else {

    echo "<p class='small'>Remarque : l'identifiant mentionné ici ne permet pas aux élèves de se connecter à Gepi, il sert simplement d'identifiant unique. Pour permettre aux élèves de se connecter à Gepi, vous devez leur créer des comptes d'accès, en passant par la page Gestion des bases -> Gestion des comptes d'accès utilisateurs -> <a href='../utilisateurs/edit_eleve.php'>Elèves</a>.</p>\n";
    echo "<form enctype=\"multipart/form-data\" action=\"index.php\" method=\"post\">\n";
    if (!isset($order_type)) { $order_type='nom,prenom';}
    echo "<table border='1' cellpadding='2' class='boireaus'>\n";
    echo "<tr>\n";
    echo "<td><p>Identifiant</p></td>\n";
    echo "<td><p><a href='index.php?order_type=nom,prenom&amp;quelles_classes=$quelles_classes'>Nom Prénom</a></p></td>\n";
    echo "<td><p><a href='index.php?order_type=sexe,nom,prenom&amp;quelles_classes=$quelles_classes'>Sexe</a></p></td>\n";
    echo "<td><p><a href='index.php?order_type=naissance,nom,prenom&amp;quelles_classes=$quelles_classes'>Date de naissance</a></p></td>\n";
    if ($quelles_classes == 'na') {
        echo "<td><p>Classe</p></td>\n";
    } else {
        echo "<td><p><a href='index.php?order_type=classe,nom,prenom&amp;quelles_classes=$quelles_classes'>Classe</a></p></td>\n";
    }
//    echo "<td><p>Classe</p></td>";
    echo "<td><p>".ucfirst(getSettingValue("gepi_prof_suivi"))."</p></td>\n";
    echo "<td><p><input type='submit' value='Supprimer' onclick=\"return confirmlink(this, 'La suppression d\'un élève est irréversible et entraîne l\'effacement complet de toutes ses données (notes, appréciations, ...). Etes-vous sûr de vouloir continuer ?', 'Confirmation de la suppression')\" /></p></td>\n";
    if (getSettingValue("active_module_trombinoscopes")=='y') {
    	echo "<td><p><input type='submit' value='Télécharger les photos' name='bouton1' /></td>\n";
    }
	echo "</tr>\n";

    if ($quelles_classes == 'certaines') {
        $calldata = mysql_query("SELECT DISTINCT e.* FROM eleves e, tempo t, j_eleves_classes j, classes cl
        WHERE (t.num = '".SESSION_ID()."' AND
               t.id_classe = j.id_classe and
               j.login = e.login AND
               cl.id=t.id_classe and
               j.periode=t.max_periode
               )
        ORDER BY $order_type");
    } else if ($quelles_classes == 'toutes') {
        if ($order_type == "classe,nom,prenom") {
            $calldata = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j, classes cl
            WHERE (
               j.login = e.login AND
               j.id_classe =cl.id
               )
        ORDER BY $order_type");
        } else {
            $calldata = mysql_query("SELECT * FROM eleves ORDER BY $order_type");
        }
    } else if ($quelles_classes == 'na') {
        $calldata = mysql_query("select e.* from eleves e
        LEFT JOIN j_eleves_classes c ON c.login=e.login
        where c.login is NULL
        ORDER BY $order_type
        ");
    } else if ($quelles_classes == 'incomplet') {
        $calldata = mysql_query("SELECT e.* FROM eleves e WHERE elenoet='' OR no_gep=''
        ORDER BY $order_type
        ");
    }

    $nombreligne = mysql_num_rows($calldata);

/*
    echo "<p>Total : $nombreligne éleves</p>\n";
    echo "<p>Remarque : le login ne permet pas aux élèves de se connecter à Gepi. Il sert simplement d'identifiant unique.</p>\n";
*/

    $i = 0;
	$alt=1;
    while ($i < $nombreligne){
        $eleve_login = mysql_result($calldata, $i, "login");
        $eleve_nom = mysql_result($calldata, $i, "nom");
        $eleve_prenom = mysql_result($calldata, $i, "prenom");
        $eleve_sexe = mysql_result($calldata, $i, "sexe");
        $eleve_naissance = mysql_result($calldata, $i, "naissance");
	$elenoet =  mysql_result($calldata, $i, "elenoet");
        $call_classe = mysql_query("SELECT n.classe, n.id FROM j_eleves_classes c, classes n WHERE (c.login ='$eleve_login' and c.id_classe = n.id) order by c.periode DESC");
        $eleve_classe = @mysql_result($call_classe, 0, "classe");
        $eleve_id_classe = @mysql_result($call_classe, 0, "id");
        if ($eleve_classe == '') {$eleve_classe = "<font color='red'>N/A</font>";}
        $call_suivi = mysql_query("SELECT u.* FROM utilisateurs u, j_eleves_professeurs s WHERE (s.login ='$eleve_login' and s.professeur = u.login and s.id_classe='$eleve_id_classe')");
		if(mysql_num_rows($call_suivi)==0){
			$eleve_profsuivi_nom = "";
			$eleve_profsuivi_prenom = "";
		}
		else{
			$eleve_profsuivi_nom = @mysql_result($call_suivi, 0, "nom");
			$eleve_profsuivi_prenom = @mysql_result($call_suivi, 0, "prenom");
		}
        if ($eleve_profsuivi_nom == '') {$eleve_profsuivi_nom = "<font color='red'>N/A</font>";}
        $delete_login = 'delete_'.$eleve_login;
		$alt=$alt*(-1);
        echo "<tr class='lig$alt'>\n";
        echo "<td><p>" . $eleve_login . "</p></td>\n";
        echo "<td><p><a href='modify_eleve.php?eleve_login=$eleve_login&amp;quelles_classes=$quelles_classes&amp;order_type=$order_type'>$eleve_nom $eleve_prenom</a></p></td>\n";
        echo "<td><p>$eleve_sexe</p></td>\n";
        echo "<td><p>".affiche_date_naissance($eleve_naissance)."</p></td>\n";
        echo "<td><p>$eleve_classe</p></td>\n";
        echo "<td><p>$eleve_profsuivi_nom $eleve_profsuivi_prenom</p></td>\n";
        //echo "<td><p><center><INPUT TYPE=CHECKBOX NAME='$delete_login' VALUE='yes' /></center></p></td></tr>\n";
        echo "<td><p align='center'><input type='checkbox' name='$delete_login' value='yes' /></p></td>\n";

		if (getSettingValue("active_module_trombinoscopes")=='y') {
        	?><td style="white-space: nowrap;"><input name="photo[<?php echo $i; ?>]" type="file" /><input type="hidden" name="quiestce[<?php echo $i; ?>]" value="<?php echo $elenoet; ?>" /><?php $photo = "../photos/eleves/".$elenoet.".jpg"; if(file_exists($photo)) { ?><a href="<?php echo $photo; ?>" target="_blank"><img src="../mod_trombinoscopes/images/<?php if($eleve_sexe=="F") { ?>photo_f.png<?php } else { ?>photo_g.png<?php } ?>" width="32" height="32"  align="middle" border="0" alt="photo présente" title="photo présente" /></a><?php } ?></td>
        <?php
		}

        echo "</tr>";
    $i++;
    }
    echo "</table>\n";
    echo "<p>Total : $nombreligne éleves</p>\n";
    ?>
    <!--/table-->
    <input type="hidden" name="is_posted" value="1" />
<?php
  // pour le trombinoscope on met la taille maximale d'une photo
?>
	<input type="hidden" name="MAX_FILE_SIZE" value="150000" />
	<input type="hidden" name="action" value="depot_photo" />
	<input type="hidden" name="total_photo" value="<?php echo $nombreligne; ?>" />
    </form>
    <?php
}
require("../lib/footer.inc.php");
?>