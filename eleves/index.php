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

function log_debug($texte){
	$fich=fopen("/tmp/debug.txt","a+");
	fwrite($fich,$texte."\n");
	fclose($fich);
}

//log_debug('Avant initialisations');

// Initialisations files
require_once("../lib/initialisations.inc.php");

//log_debug('Après initialisations');

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
};

//log_debug('Après $session_gepi->security_check()');

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];

//log_debug('Après checkAccess()');

//log_debug(debug_var());

$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');
if($_SESSION['statut']=="professeur") {
	if(getSettingValue('GepiAccesGestElevesProfP')!='yes') {
		tentative_intrusion("2", "Tentative d'accès par un prof à des fiches élèves, sans en avoir l'autorisation.");
		echo "Vous ne pouvez pas accéder à cette page car l'accès professeur n'est pas autorisé !";
		require ("../lib/footer.inc.php");
		die();
	}
	else{
		// Le professeur est-il professeur principal dans une classe au moins.
		$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."';";
		$test=mysql_query($sql);
		if (mysql_num_rows($test)==0) {
			tentative_intrusion("2", "Tentative d'accès par un prof qui n'est pas $gepi_prof_suivi à des fiches élèves, sans en avoir l'autorisation.");
			echo "Vous ne pouvez pas accéder à cette page car vous n'êtes pas $gepi_prof_suivi !";
			require ("../lib/footer.inc.php");
			die();
		}
	}
}

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

if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
	if (isset($is_posted) and ($is_posted == '1')) {
		$calldata = mysql_query("SELECT * FROM eleves");
		$nombreligne = mysql_num_rows($calldata);
		$i = 0;
		$liste_cible = '';
		$liste_cible2 = '';
		while ($i < $nombreligne){
			$eleve_login = mysql_result($calldata, $i, "login");
			$eleve_elenoet = mysql_result($calldata, $i, "elenoet");
			$delete_login = 'delete_'.$eleve_login;
			$del_eleve = isset($_POST[$delete_login])?$_POST[$delete_login]:NULL;
			if ($del_eleve == 'yes') {
				$liste_cible = $liste_cible.$eleve_login.";";
				$liste_cible2 = $liste_cible2.$eleve_elenoet.";";
			}
		$i++;
		}
		//header("Location: ../lib/confirm_query.php?liste_cible=$liste_cible&amp;action=del_eleve");
		if($liste_cible!=''){
			header("Location: ../lib/confirm_query.php?liste_cible=$liste_cible&liste_cible2=$liste_cible2&action=del_eleve");
		}
	}
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
			} else if ((!preg_match('/jpg$/i',$sav_photo['name'][$cpt_photo])) and $sav_photo['type'][$cpt_photo] == "image/jpeg"){
					$msg = "Erreur : seuls les fichiers ayant l'extension .jpg sont autorisés.";
			} else {
					$dest = "../photos/eleves/";
				$n = 0;
				if (!deplacer_fichier_upload($sav_photo['tmp_name'][$cpt_photo], "../photos/eleves/".$quiestce[$cpt_photo].".jpg")) {
					$msg = "Problème de transfert : le fichier n'a pas pu être transféré sur le répertoire photos/eleves/";
				} else {
						$msg = "Téléchargement réussi.";
				if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
					// si le redimensionnement des photos est activé on redimenssionne
					$source = imagecreatefromjpeg("../photos/eleves/".$quiestce[$cpt_photo].".jpg"); // La photo est la source
					if (getSettingValue("active_module_trombinoscopes_rt")=='') { $destination = imagecreatetruecolor(getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes")); } // On crée la miniature vide
					if (getSettingValue("active_module_trombinoscopes_rt")!='') { $destination = imagecreatetruecolor(getSettingValue("h_resize_trombinoscopes"), getSettingValue("l_resize_trombinoscopes")); } // On crée la miniature vide

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
	<?php
		// Test d'existence de PMV... utilisé ici pour le file_exists() des photos
		/*
		if(getSettingValue("gepi_pmv")!="n"){
			echo "document.formulaire.quelles_classes[8].checked = true;";
		}
		else{
			echo "document.formulaire.quelles_classes[7].checked = true;";
		}
		*/
		echo "document.getElementById('quelles_classes_certaines').checked = true;";
	?>
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

	function verif3(){
		document.getElementById('quelles_classes_recherche').checked=true;
		verif2();
	}
</script>

<?php
if ($_SESSION['statut'] == 'administrateur') {
	$retour = "../accueil_admin.php";
}
else{
	$retour = "../accueil.php";
}
if (isset($quelles_classes)) {
	$retour = "index.php";
}
echo "<p class=bold><a href=\"".$retour."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>\n";



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

if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
	echo " | <a href='add_eleve.php?mode=unique'>Ajouter un élève à la base (simple)</a>\n";
	echo " | <a href='add_eleve.php?mode=multiple'>Ajouter des élèves à la base (à la chaîne)</a>\n";

	$droits = @sql_query1("SELECT ".$_SESSION['statut']." FROM droits WHERE id='/eleves/import_eleves_csv.php'");
	if ($droits == "V") {
		echo " | <a href=\"import_eleves_csv.php\" title=\"Télécharger le fichier des noms, prénoms, identifiants GEPI et classes\">Télécharger le fichier des élèves au format csv.</a>\n";

		if(getSettingValue("import_maj_xml_sconet")==1){
			echo " | <a href=\"../responsables/maj_import.php\">Mettre à jour depuis Sconet</a>\n";
		}
	}
}
if(($_SESSION['statut']=="administrateur")&&(getSettingValue('exp_imp_chgt_etab')=='yes')) {
	// Pour activer le dispositif:
	// DELETE FROM setting WHERE name='exp_imp_chgt_etab';INSERT INTO setting SET name='exp_imp_chgt_etab', value='yes';
	//echo " | ";
	echo "<br />";
	echo "Changement d'établissement: <a href='export_bull_eleve.php'>Export des bulletins</a>\n";
	echo " et <a href='import_bull_eleve.php'>Import des bulletins</a>\n";
}
echo "</p>\n";

echo "<center><p class='grand'>Visualiser \ modifier une fiche élève</p></center>\n";

$req = mysql_query("SELECT login FROM eleves");
$test = mysql_num_rows($req);
if ($test == '0') {
	echo "<p class='grand'>Attention : il n'y a aucun élève dans la base GEPI !</p>\n";
	if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
		echo "<p>Vous pouvez ajouter des élèves à la base en cliquant sur l'un des liens ci-dessus";
		if($_SESSION['statut']=="administrateur") {
			echo ", ou bien directement <br /><a href='../initialisation/index.php'>importer les élèves et les classes à partir de fichiers GEP</a></p>\n";
		}
		require("../lib/footer.inc.php");
		die();
	}
}

if (!isset($quelles_classes)) {

	if($_SESSION['statut'] == 'professeur') {
		$sql="SELECT DISTINCT c.* FROM j_eleves_professeurs jep, classes c WHERE c.id=jep.id_classe AND jep.professeur='".$_SESSION['login']."' ORDER BY c.classe;";
		$call_classes=mysql_query($sql);

		$nb_classes=mysql_num_rows($call_classes);
		if($nb_classes==0){
			echo "<p>Vous n'êtes pas $gepi_prof_suivi</p>\n";
			// AJOUTER UN RENSEIGNEMENT test_intrusion... (normalement c'est fait plus haut)
			require("../lib/footer.inc.php");
			die();
		}
		elseif($nb_classes==1){
			$lig_clas=mysql_fetch_object($call_classes);
			$quelles_classes=$lig_clas->id;
		}
		else{
			// Choix de la classe...

			// Affichage sur 3 colonnes
			$nb_classes_par_colonne=round($nb_classes/2);

			echo "<table width='100%' summary='Choix des classes'>\n";
			echo "<tr valign='top' align='center'>\n";

			$cpt_i = 0;

			echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
			echo "<td align='left'>\n";

			while($lig_clas=mysql_fetch_object($call_classes)) {

				//affichage 2 colonnes
				if(($cpt_i>0)&&(round($cpt_i/$nb_classes_par_colonne)==$cpt_i/$nb_classes_par_colonne)){
					echo "</td>\n";
					echo "<td align='left'>\n";
				}

				echo "<a href='".$_SERVER['PHP_SELF']."?quelles_classes=$lig_clas->id'>$lig_clas->classe</a>";
				echo "<br />\n";
				$cpt_i++;
			}

			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

		}
	}
	else{


		echo "<form enctype='multipart/form-data' action='index.php' method='post' name='formulaire'>\n";
		echo "<table cellpadding='5' width='100%' border='0' summary='Choix du mode'>\n";

		echo "<tr>\n";
		echo "<td>\n";
		echo "<input type='radio' name='quelles_classes' id='quelles_classes_toutes' value='toutes' onclick='verif2()' checked />\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<label for='quelles_classes_toutes' style='cursor: pointer;'>\n";
		echo "<span class='norme'>Tous les élèves.</span><br />";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";

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
			echo "<input type='radio' name='quelles_classes' id='quelles_classes_na' value='na' onclick='verif2()' />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='quelles_classes_na' style='cursor: pointer;'>\n";
			echo "<span class='norme'>Les élèves non affectés à une classe (<i>".mysql_num_rows($test_na)."</i>).</span><br />\n";
			echo "</label>\n";
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
			echo "<input type='radio' name='quelles_classes' id='quelles_classes_incomplet' value='incomplet' onclick='verif2()' />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='quelles_classes_incomplet' style='cursor: pointer;'>\n";
			echo "<span class='norme'>Les élèves dont l'Elenoet ou le Numéro national (INE) n'est pas renseigné (<i>".mysql_num_rows($test_incomplet)."</i>).</span><br />\n";
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}

		// =====================================================
		// Les photos
		if (getSettingValue("active_module_trombinoscopes")=='y') {
			$sql="SELECT elenoet FROM eleves WHERE elenoet!='';";
			$test_elenoet_ok=mysql_query($sql);
			if(mysql_num_rows($test_elenoet_ok)!=0){
				$cpt_photo_manquante=0;
				while($lig_tmp=mysql_fetch_object($test_elenoet_ok)){
					$test_photo=nom_photo($lig_tmp->elenoet);
					//if((!file_exists("../photos/eleves/".$lig_tmp->elenoet.".jpg"))&&(!file_exists("../photos/eleves/0".$lig_tmp->elenoet.".jpg"))){
					if($test_photo==""){
						$cpt_photo_manquante++;
					}
				}
				if($cpt_photo_manquante>0){
					echo "<tr>\n";
					echo "<td>\n";
					echo "<input type='radio' name='quelles_classes' id='quelles_classes_photo' value='photo' onclick='verif2()' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo "<label for='quelles_classes_photo' style='cursor: pointer;'>\n";
					echo "<span class='norme'>Parmi les élèves dont l'Elenoet est renseigné, $cpt_photo_manquante n'ont pas leur photo.</span><br />\n";
					echo "</label>\n";
					echo "</td>\n";
					echo "</tr>\n";
				}
				else{
					echo "<tr>\n";
					echo "<td>\n";
					echo "&nbsp;\n";
					echo "</td>\n";
					echo "<td>\n";

					echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='photo' onclick='verif2()' /></span>\n";

					echo "<span class='norme'>Tous les élèves, dont l'Elenoet est renseigné, ont leur photo.</span><br />\n";
					echo "</td>\n";
					echo "</tr>\n";
				}
			}
			else{
				echo "<tr>\n";
				echo "<td>\n";
				echo "&nbsp;\n";
				echo "</td>\n";
				echo "<td>\n";

				echo "<span style='display:none;'><input type='radio' name='quelles_classes' id='quelles_classes_photo' value='photo' onclick='verif2()' /></span>\n";

				echo "<label for='quelles_classes_photo' style='cursor: pointer;'>\n";
				echo "<span class='norme'>Aucun élève n'a son Elenoet renseigné.<br />L'affichage des photos n'est donc pas fonctionnel.</span><br />\n";
				echo "</label>\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
		}
		// =====================================================

		/*
		$sql="SELECT 1=1 FROM eleves e
			LEFT JOIN j_eleves_cpe jec ON jec.e_login=e.login
			where jec.e_login is NULL;";
		*/
		$sql="SELECT DISTINCT login FROM j_eleves_classes jecl
			LEFT JOIN j_eleves_cpe jec ON jecl.login=jec.e_login
			WHERE jec.e_login is null;";
		$test_no_cpe=mysql_query($sql);
		//$test_no_cpe_effectif=mysql_num_rows($test_no_cpe)-mysql_num_rows($test_na);
		$test_no_cpe_effectif=mysql_num_rows($test_no_cpe);
		/*
		$sql="SELECT 1=1 FROM eleves e
			LEFT JOIN j_eleves_cpe jec ON jec.e_login=e.login
			WHERE jec.e_login is NULL
			AND e_login IN (SELECT DISTINCT login FROM j_eleves_classes);";
		$test_no_cpe_effectif=mysql_query($sql);
		*/
		//if(mysql_num_rows($test_no_cpe)==0){
		if($test_no_cpe_effectif==0){
			echo "<tr>\n";
			echo "<td>\n";
			echo "&nbsp;\n";
			echo "</td>\n";
			echo "<td>\n";

			echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='no_cpe' onclick='verif2()' /></span>\n";

			echo "<span class='norme'>Tous les élèves (<i>affectés dans des classes</i>) ont un CPE associé.</span><br />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		else{
			echo "<tr>\n";
			echo "<td>\n";
			echo "<input type='radio' name='quelles_classes' id='quelles_classes_no_cpe' value='no_cpe' onclick='verif2()' />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='quelles_classes_no_cpe' style='cursor: pointer;'>\n";
			//echo "<span class='norme'>Les élèves sans CPE (<i>".mysql_num_rows($test_no_cpe)."</i>).</span><br />\n";
			echo "<span class='norme'>Les élèves (<i>affectés dans des classes</i>) sans CPE (<i>".$test_no_cpe_effectif."</i>).</span><br />\n";
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}


		// =====================================================
		$sql="SELECT 1=1 FROM eleves e
			LEFT JOIN j_eleves_professeurs jep ON jep.login=e.login
			where jep.login is NULL;";
		$test_no_pp=mysql_query($sql);
		$test_no_pp_effectif=mysql_num_rows($test_no_pp)-mysql_num_rows($test_na);
		//if(mysql_num_rows($test_no_pp)==0){
		if($test_no_pp_effectif==0){
			echo "<tr>\n";
			echo "<td>\n";
			echo "&nbsp;\n";
			echo "</td>\n";
			echo "<td>\n";

			echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='no_pp' onclick='verif2()' /></span>\n";

			echo "<span class='norme'>Tous les élèves ont un ".getSettingValue('gepi_prof_suivi')." associé.</span><br />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		else{
			echo "<tr>\n";
			echo "<td>\n";
			echo "<input type='radio' name='quelles_classes' id='quelles_classes_no_pp' value='no_pp' onclick='verif2()' />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='quelles_classes_no_pp' style='cursor: pointer;'>\n";
			echo "<span class='norme'>Les élèves sans ".getSettingValue('gepi_prof_suivi')." (<i>".$test_no_pp_effectif."</i>).</span><br />\n";
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		// =====================================================



		// A FAIRE:
		// Liste des élèves dont le nom commence par/contient...

		echo "<tr>\n";
		echo "<td>\n";
		echo "<input type='radio' name='quelles_classes' id='quelles_classes_recherche' value='recherche' onclick='verif2()' />\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<label for='' style='cursor: pointer;'>\n";
		echo "<span class='norme'>Elève dont le nom commence par: \n";
		echo "<input type='text' name='motif_rech' value='' onclick='verif3()' size='5' />\n";
		echo "</span><br />\n";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";

		// =====================================================

		$classes_list = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
		$nb = mysql_num_rows($classes_list);
		if ($nb !=0) {
			echo "<tr>\n";
			echo "<td valign='top'>\n";

			echo "<input type=\"radio\" name=\"quelles_classes\" id=\"quelles_classes_certaines\" value=\"certaines\" />";

			echo "</td>\n";
			echo "<td valign='top'>\n";

			echo "<label for='quelles_classes_certaines' style='cursor: pointer;'>\n";
			echo "<span class = \"norme\">Seulement les élèves des classes sélectionnées ci-dessous : </span>";
			echo "</label>\n";
			echo "<br />\n";

				$nb_class_par_colonne=round($nb/3);
				//echo "<table width='100%' border='1'>\n";
				echo "<table width='100%' summary='Choix des classes'>\n";
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
				echo "<label for='$temp' style='cursor: pointer;'>";
				echo "<input type='checkbox' name='$temp' id='$temp' value='yes' onclick=\"verif1()\" />";
				echo "Classe : $classe</label><br />\n";
				$i++;
				}
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";

			echo "</td>\n";
			echo "</tr>\n";
		}

		echo "</table>\n";

		echo "<input type='hidden' name='is_posted' value='2' />\n";

		echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
		echo "</form>\n";
	}
//} else {
}

if(isset($quelles_classes)) {
	//echo "$quelles_classes<br />";

	echo "<p class='small'>Remarque : l'identifiant mentionné ici ne permet pas aux élèves de se connecter à Gepi, il sert simplement d'identifiant unique.";
	//if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
	if($_SESSION['statut']=="administrateur") {
		echo " Pour permettre aux élèves de se connecter à Gepi, vous devez leur créer des comptes d'accès, en passant par la page Gestion des bases -> Gestion des comptes d'accès utilisateurs -> <a href='../utilisateurs/edit_eleve.php'>Elèves</a>.";
	}
	elseif($_SESSION['statut']=="scolarite") {
		echo " Pour permettre aux élèves de se connecter à Gepi, vous devez vous connecter en 'administrateur' et leur créer des comptes d'accès, en passant par la page Gestion des bases -> Gestion des comptes d'accès utilisateurs -> Elèves.";
	}
	echo "</p>\n";

	echo "<form enctype=\"multipart/form-data\" action=\"index.php\" method=\"post\">\n";
	if (!isset($order_type)) { $order_type='nom,prenom';}

	/*
	echo "<table border='1' cellpadding='2' class='boireaus'>\n";
	echo "<tr>\n";
	echo "<td><p>Identifiant</p></td>\n";
	echo "<td><p><a href='index.php?order_type=nom,prenom&amp;quelles_classes=$quelles_classes";
	if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
	echo "'>Nom Prénom</a></p></td>\n";
	echo "<td><p><a href='index.php?order_type=sexe,nom,prenom&amp;quelles_classes=$quelles_classes";
	if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
	echo "'>Sexe</a></p></td>\n";
	echo "<td><p><a href='index.php?order_type=naissance,nom,prenom&amp;quelles_classes=$quelles_classes";
	if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
	echo "'>Date de naissance</a></p></td>\n";
	if ($quelles_classes == 'na') {
		echo "<td><p>Classe</p></td>\n";
	} else {
		echo "<td><p><a href='index.php?order_type=classe,nom,prenom&amp;quelles_classes=$quelles_classes";
		if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
		echo "'>Classe</a></p></td>\n";
	}
//    echo "<td><p>Classe</p></td>";
	echo "<td><p>".ucfirst(getSettingValue("gepi_prof_suivi"))."</p></td>\n";
	echo "<td><p><input type='submit' value='Supprimer' onclick=\"return confirmlink(this, 'La suppression d\'un élève est irréversible et entraîne l\'effacement complet de toutes ses données (notes, appréciations, ...). Etes-vous sûr de vouloir continuer ?', 'Confirmation de la suppression')\" /></p></td>\n";
	if (getSettingValue("active_module_trombinoscopes")=='y') {
		echo "<td><p><input type='submit' value='Télécharger les photos' name='bouton1' /></td>\n";
	}
	echo "</tr>\n";
	*/

	if($_SESSION['statut'] == 'professeur') {
		$calldata = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_professeurs jep
		WHERE (
		jep.login=e.login AND
		jep.professeur='".$_SESSION['login']."' AND
		jep.id_classe='$quelles_classes'
		)
		ORDER BY $order_type");

		echo "<p align='center'>Liste des élèves de la classe choisie.</p>\n";
	}
	else{
		if ($quelles_classes == 'certaines') {
			$calldata = mysql_query("SELECT DISTINCT e.* FROM eleves e, tempo t, j_eleves_classes j, classes cl
			WHERE (t.num = '".SESSION_ID()."' AND
				t.id_classe = j.id_classe and
				j.login = e.login AND
				cl.id=t.id_classe and
				j.periode=t.max_periode
				)
			ORDER BY $order_type");

			echo "<p align='center'>Liste des élèves de la ou des classes choisies.</p>\n";

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

			echo "<p align='center'>Liste de tous les élèves.</p>\n";

		} else if ($quelles_classes == 'na') {
			$calldata = mysql_query("select e.* from eleves e
			LEFT JOIN j_eleves_classes c ON c.login=e.login
			where c.login is NULL
			ORDER BY $order_type
			");

			echo "<p align='center'>Liste des élèves non affectés dans une classe.</p>\n";

		} else if ($quelles_classes == 'incomplet') {
			/*
			$calldata = mysql_query("SELECT e.* FROM eleves e WHERE elenoet='' OR no_gep=''
			ORDER BY $order_type
			");
			*/
			if(ereg('classe',$order_type)){
				$sql="SELECT DISTINCT e.* FROM eleves e, classes c, j_eleves_classes jec
					WHERE (e.elenoet='' OR e.no_gep='') AND
							jec.login=e.login AND
							c.id=jec.id_classe
					ORDER BY $order_type";
			}
			else{
				$sql="SELECT e.* FROM eleves e WHERE elenoet='' OR no_gep=''
												ORDER BY $order_type";
			}
			//echo "$sql<br />\n";
			$calldata = mysql_query($sql);

			echo "<p align='center'>Liste des élèves dont l'Elenoet ou le Numéro national (INE) n'est pas renseigné.</p>\n";

		} else if ($quelles_classes == 'photo') {
			//$sql="SELECT elenoet FROM eleves WHERE elenoet!='';";
			$sql="SELECT * FROM eleves WHERE elenoet!='';";
			$test_elenoet_ok=mysql_query($sql);
			if(mysql_num_rows($test_elenoet_ok)!=0){
				//$chaine_photo_manquante="";
				$tab_eleve=array();
				$i=0;
				while($lig_tmp=mysql_fetch_object($test_elenoet_ok)) {
					$test_photo=nom_photo($lig_tmp->elenoet);
					//if((!file_exists("../photos/eleves/".$lig_tmp->elenoet.".jpg"))&&(!file_exists("../photos/eleves/0".$lig_tmp->elenoet.".jpg"))){
					if($test_photo==""){
						//if($chaine_photo_manquante!=""){$chaine_photo_manquante.=" OR ";}
						//$chaine_photo_manquante.="elenoet='$lig_tmp->elenoet'";
						$tab_eleve[$i]=array();
						$tab_eleve[$i]['login']=$lig_tmp->login;
						$tab_eleve[$i]['nom']=$lig_tmp->nom;
						$tab_eleve[$i]['prenom']=$lig_tmp->prenom;
						$tab_eleve[$i]['sexe']=$lig_tmp->sexe;
						$tab_eleve[$i]['naissance']=$lig_tmp->naissance;
						$tab_eleve[$i]['elenoet']=$lig_tmp->elenoet;
						$i++;
					}
				}
				/*
				$calldata = mysql_query("SELECT e.* FROM eleves e WHERE $chaine_photo_manquante
				ORDER BY $order_type
				");
				*/
			}

			echo "<p align='center'>Liste des élèves sans photo.</p>\n";

		} else if ($quelles_classes == 'no_cpe') {
			if(ereg('classe',$order_type)){
				/*
				$sql="SELECT DISTINCT e.* FROM eleves e, classes c, j_eleves_classes jec
					WHERE jec.id_classe=c.id AND
							jec.login=e.login
					ORDER BY $order_type;";
				//	ORDER BY c.classe, e.nom, e.prenom;";
				//echo "DEBUG: $sql<br />";
				$calldata = mysql_query($sql);

				if(mysql_num_rows($calldata)!=0){
					$tab_eleve=array();
					$i=0;
					while($lig_tmp=mysql_fetch_object($calldata)) {
						$sql="SELECT 1=1 FROM j_eleves_cpe
									WHERE e_login='$lig_tmp->login';";
						$test_eleve_cpe=mysql_query($sql);
						if(mysql_num_rows($test_eleve_cpe)==0){
							$tab_eleve[$i]=array();
							$tab_eleve[$i]['login']=$lig_tmp->login;
							$tab_eleve[$i]['nom']=$lig_tmp->nom;
							$tab_eleve[$i]['prenom']=$lig_tmp->prenom;
							$tab_eleve[$i]['sexe']=$lig_tmp->sexe;
							$tab_eleve[$i]['naissance']=$lig_tmp->naissance;
							$tab_eleve[$i]['elenoet']=$lig_tmp->elenoet;
							//$tab_eleve[$i]['classe']=$lig_tmp->classe;
							$i++;
						}
					}
				}
				*/
				$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec, classes c
						WHERE e.login=jec.login AND
							jec.id_classe=c.id AND
							e.login NOT IN (SELECT e_login FROM j_eleves_cpe) ORDER BY $order_type;";
				$calldata=mysql_query($sql);
			}
			else{
				/*
				$sql="SELECT e.* FROM eleves e
					LEFT JOIN j_eleves_cpe jec ON jec.e_login=e.login
					WHERE jec.e_login is NULL
					ORDER BY $order_type;";
				$calldata=mysql_query($sql);

				if(mysql_num_rows($calldata)!=0){
					$tab_eleve=array();
					$i=0;
					while($lig_tmp=mysql_fetch_object($calldata)) {
						$sql="SELECT 1=1 FROM j_eleves_classes
									WHERE login='$lig_tmp->login';";
						$test_eleve_classe=mysql_query($sql);
						if(mysql_num_rows($test_eleve_classe)>0){
							$tab_eleve[$i]=array();
							$tab_eleve[$i]['login']=$lig_tmp->login;
							$tab_eleve[$i]['nom']=$lig_tmp->nom;
							$tab_eleve[$i]['prenom']=$lig_tmp->prenom;
							$tab_eleve[$i]['sexe']=$lig_tmp->sexe;
							$tab_eleve[$i]['naissance']=$lig_tmp->naissance;
							$tab_eleve[$i]['elenoet']=$lig_tmp->elenoet;
							$i++;
						}
					}
				}
				*/
				$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec
						WHERE e.login=jec.login AND
							e.login NOT IN (SELECT e_login FROM j_eleves_cpe) ORDER BY $order_type;";
				$calldata=mysql_query($sql);
			}

			echo "<p align='center'>Liste des élèves sans CPE.</p>\n";

		} else if ($quelles_classes == 'no_pp') {
			if(ereg('classe',$order_type)){
				//echo "DEBUG: 1<br />";
				/*
				//$sql="SELECT e.*,c.classe FROM eleves e, classes c, j_eleves_classes jec
				$sql="SELECT DISTINCT e.* FROM eleves e, classes c, j_eleves_classes jec
					WHERE jec.id_classe=c.id AND
							jec.login=e.login
					ORDER BY $order_type;";
				//	ORDER BY c.classe, e.nom, e.prenom;";
				//echo "DEBUG: $sql<br />";
				$calldata = mysql_query($sql);

				if(mysql_num_rows($calldata)!=0){
					$tab_eleve=array();
					$i=0;
					while($lig_tmp=mysql_fetch_object($calldata)) {
						$sql="SELECT 1=1 FROM j_eleves_professeurs
									WHERE login='$lig_tmp->login';";
						$test_eleve_pp=mysql_query($sql);
						if(mysql_num_rows($test_eleve_pp)==0){
							$tab_eleve[$i]=array();
							$tab_eleve[$i]['login']=$lig_tmp->login;
							$tab_eleve[$i]['nom']=$lig_tmp->nom;
							$tab_eleve[$i]['prenom']=$lig_tmp->prenom;
							$tab_eleve[$i]['sexe']=$lig_tmp->sexe;
							$tab_eleve[$i]['naissance']=$lig_tmp->naissance;
							$tab_eleve[$i]['elenoet']=$lig_tmp->elenoet;
							//$tab_eleve[$i]['classe']=$lig_tmp->classe;
							$i++;
						}
					}
				}
				*/

				$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec, classes c
						WHERE e.login=jec.login AND
							jec.id_classe=c.id AND
							e.login NOT IN (SELECT login FROM j_eleves_professeurs) ORDER BY $order_type;";
				$calldata=mysql_query($sql);

			}
			else{
				/*
				//echo "DEBUG: 2<br />";
				$sql="SELECT e.* FROM eleves e
					LEFT JOIN j_eleves_professeurs jep ON jep.login=e.login
					WHERE jep.login is NULL
					ORDER BY $order_type;";
				//echo "DEBUG: $sql<br />";
				$calldata = mysql_query($sql);

				if(mysql_num_rows($calldata)!=0){
					$tab_eleve=array();
					$i=0;
					while($lig_tmp=mysql_fetch_object($calldata)) {
						$sql="SELECT 1=1 FROM j_eleves_classes
									WHERE login='$lig_tmp->login';";
						$test_eleve_classe=mysql_query($sql);
						if(mysql_num_rows($test_eleve_classe)>0){
							$tab_eleve[$i]=array();
							$tab_eleve[$i]['login']=$lig_tmp->login;
							$tab_eleve[$i]['nom']=$lig_tmp->nom;
							$tab_eleve[$i]['prenom']=$lig_tmp->prenom;
							$tab_eleve[$i]['sexe']=$lig_tmp->sexe;
							$tab_eleve[$i]['naissance']=$lig_tmp->naissance;
							$tab_eleve[$i]['elenoet']=$lig_tmp->elenoet;
							//$tab_eleve[$i]['classe']=$lig_tmp->classe;
							$i++;
						}
					}
				}
				*/

				$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec
						WHERE e.login=jec.login AND
							e.login NOT IN (SELECT login FROM j_eleves_professeurs) ORDER BY $order_type;";
				$calldata=mysql_query($sql);

			}

			echo "<p align='center'>Liste des élèves sans ".getSettingValue('gepi_prof_suivi')."</p>\n";

		} else if ($quelles_classes == 'recherche') {
			/*
			$calldata = mysql_query("SELECT e.* FROM eleves e WHERE nom like '".$motif_rech."%'
			ORDER BY $order_type
			");
			*/
			if(ereg('classe',$order_type)){
				$sql="SELECT DISTINCT e.* FROM eleves e, classes c, j_eleves_classes jec
					WHERE nom like '".$motif_rech."%' AND
							jec.login=e.login AND
							c.id=jec.id_classe
					ORDER BY $order_type";
			}
			else{
				$sql="SELECT e.* FROM eleves e WHERE nom like '".$motif_rech."%'
												ORDER BY $order_type";
			}
			//echo "$sql<br />\n";
			$calldata = mysql_query($sql);

			echo "<p align='center'>Liste des élèves dont le nom commence par <b>$motif_rech</b></p>\n";
		}
	}

	echo "<table border='1' cellpadding='2' class='boireaus'  summary='Tableau des élèves de la classe'>\n";
	echo "<tr>\n";
	echo "<td><p>Identifiant</p></td>\n";
	echo "<td><p><a href='index.php?order_type=nom,prenom&amp;quelles_classes=$quelles_classes";
	if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
	echo "'>Nom Prénom</a></p></td>\n";
	echo "<td><p><a href='index.php?order_type=sexe,nom,prenom&amp;quelles_classes=$quelles_classes";
	if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
	echo "'>Sexe</a></p></td>\n";
	echo "<td><p><a href='index.php?order_type=naissance,nom,prenom&amp;quelles_classes=$quelles_classes";
	if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
	echo "'>Date de naissance</a></p></td>\n";
	if ($quelles_classes == 'na') {
		echo "<td><p>Classe</p></td>\n";
	} else {
		echo "<td><p>";
		if($_SESSION['statut'] != 'professeur') {
			echo "<a href='index.php?order_type=classe,nom,prenom&amp;quelles_classes=$quelles_classes";
			if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
			echo "'>Classe</a>";
		}
		else{
			echo "Classe";
		}
		echo "</p></td>\n";
	}
//    echo "<td><p>Classe</p></td>";
	echo "<td><p>".ucfirst(getSettingValue("gepi_prof_suivi"))."</p></td>\n";

	if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
		echo "<td><p><input type='submit' value='Supprimer' onclick=\"return confirmlink(this, 'La suppression d\'un élève est irréversible et entraîne l\'effacement complet de toutes ses données (notes, appréciations, ...). Etes-vous sûr de vouloir continuer ?', 'Confirmation de la suppression')\" /></p></td>\n";
	}

	if (getSettingValue("active_module_trombinoscopes")=='y') {
		if($_SESSION['statut']=="professeur") {
			if (getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes') {
				echo "<td><p><input type='submit' value='Télécharger les photos' name='bouton1' /></td>\n";
			}
		}
		else{
			echo "<td><p><input type='submit' value='Télécharger les photos' name='bouton1' /></td>\n";
		}
	}
	echo "</tr>\n";

	if(!isset($tab_eleve)){
		$nombreligne = mysql_num_rows($calldata);
	}
	else{
		$nombreligne = count($tab_eleve);
	}
/*
	echo "<p>Total : $nombreligne éleves</p>\n";
	echo "<p>Remarque : le login ne permet pas aux élèves de se connecter à Gepi. Il sert simplement d'identifiant unique.</p>\n";
*/

	$i = 0;
	$alt=1;
	while ($i < $nombreligne){
		if(!isset($tab_eleve[$i])){
			$eleve_login = mysql_result($calldata, $i, "login");
			$eleve_nom = mysql_result($calldata, $i, "nom");
			$eleve_prenom = mysql_result($calldata, $i, "prenom");
			$eleve_sexe = mysql_result($calldata, $i, "sexe");
			$eleve_naissance = mysql_result($calldata, $i, "naissance");
			$elenoet =  mysql_result($calldata, $i, "elenoet");
		}
		else{
			$eleve_login = $tab_eleve[$i]["login"];
			$eleve_nom = $tab_eleve[$i]["nom"];
			$eleve_prenom = $tab_eleve[$i]["prenom"];
			$eleve_sexe = $tab_eleve[$i]["sexe"];
			$eleve_naissance = $tab_eleve[$i]["naissance"];
			$elenoet =  $tab_eleve[$i]["elenoet"];
		}

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
		echo "<td><p><a href='modify_eleve.php?eleve_login=$eleve_login&amp;quelles_classes=$quelles_classes&amp;order_type=$order_type";
		if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
		echo "'>$eleve_nom $eleve_prenom</a></p></td>\n";
		echo "<td><p>$eleve_sexe</p></td>\n";
		echo "<td><p>".affiche_date_naissance($eleve_naissance)."</p></td>\n";
		echo "<td><p>$eleve_classe</p></td>\n";
		echo "<td><p>$eleve_profsuivi_nom $eleve_profsuivi_prenom</p></td>\n";

		if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
			//echo "<td><p><center><INPUT TYPE=CHECKBOX NAME='$delete_login' VALUE='yes' /></center></p></td></tr>\n";
			echo "<td><p align='center'><input type='checkbox' name='$delete_login' value='yes' /></p></td>\n";
		}

		if ((getSettingValue("active_module_trombinoscopes")=='y')&&
			((($_SESSION['statut']=="professeur")&&(getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes'))||
				($_SESSION['statut']!="professeur"))) {

			//echo "<td style='white-space: nowrap;'><input name='photo[$i]' type='file' />\n";
			echo "<td style='white-space: nowrap; text-align:left;'><input name='photo[$i]' type='file' />\n";

			echo "<input type='hidden' name='quiestce[$i]' value=\"$elenoet\" />\n";

			$photo=nom_photo($elenoet);
			$temoin_photo="";
			if("$photo"!=""){
				$titre="$eleve_nom $eleve_prenom";

				$texte="<div align='center'>\n";
				$texte.="<img src='../photos/eleves/".$photo."' width='150' alt=\"$eleve_nom $eleve_prenom\" />";
				$texte.="<br />\n";
				$texte.="</div>\n";

				$temoin_photo="y";

				$tabdiv_infobulle[]=creer_div_infobulle('photo_'.$eleve_login,$titre,"",$texte,"",14,0,'y','y','n','n');

				echo "<a href='../photos/eleves/$photo' target='_blank' onmouseover=\"afficher_div('photo_$eleve_login','y',-20,20);\">";
				echo "<img src='../mod_trombinoscopes/images/";
				if($eleve_sexe=="F") {
					echo "photo_f.png";
				}
				else{
					echo "photo_g.png";
				}
				echo "' width='32' height='32'  align='middle' border='0' alt='photo présente' title='photo présente' />";
				echo "</a>";
			}

			echo "</td>\n";
		}

		echo "</tr>\n";
		$i++;
	}
	echo "</table>\n";
	echo "<p>Total : $nombreligne élève";
	if($nombreligne>1) {echo "s";}
	echo "</p>\n";
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