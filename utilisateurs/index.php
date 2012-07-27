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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";

// pour l'envoi des photos du trombinoscope

 if (empty($_POST['action']) and empty($_GET['action'])) { $action = ''; }
    else { if (empty($_POST['action'])){$action = ''; } if (empty($_GET['action'])){$action = $_POST['action'];} }
 if (empty($_POST['total_photo']) and empty($_GET['total_photo'])) { $total_photo = ''; }
    else { if (empty($_POST['total_photo'])){$total_photo = ""; } if (empty($_GET['total_photo'])){$total_photo = $_POST['total_photo'];} }
 if (empty($_FILES['photo'])) { $photo = ''; } else { $photo = $_FILES['photo']; }
 if (empty($_POST['quiestce'])) { $quiestce = ''; } else { $quiestce = $_POST['quiestce']; }

 //répertoire des photos

// En multisite, on ajoute le répertoire RNE
if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
	  // On récupère le RNE de l'établissement
  $rep_photos='../photos/'.$_COOKIE['RNE'].'/personnels/';
}else{
  $rep_photos='../photos/personnels/';
}


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
    if ($f = @fopen($rep_photos."test", "w")) {
        @fputs($f, '<'.'?php $ok = "yes"; ?'.'>');
        @fclose($f);
        include($rep_photos."test");
        $del = @unlink($rep_photos."test");
    }
    return $ok;
}

// fonction de sécurité
// uid de pour ne pas refaire renvoyer plusieurs fois le même formulaire
// autoriser la validation de formulaire $uid_post===$_SESSION['uid_prime']
 if(empty($_SESSION['uid_prime'])) { $_SESSION['uid_prime']=''; }
 if (empty($_GET['uid_post']) and empty($_POST['uid_post'])) {$uid_post='';}
    else { if (isset($_GET['uid_post'])) {$uid_post=$_GET['uid_post'];} if (isset($_POST['uid_post'])) {$uid_post=$_POST['uid_post'];} }
	$uid = md5(uniqid(microtime(), 1));
	   // on remplace les %20 par des espaces
	    $uid_post = my_eregi_replace('%20',' ',$uid_post);
	if($uid_post===$_SESSION['uid_prime']) { $valide_form = 'oui'; } else { $valide_form = 'non'; }
	$_SESSION['uid_prime'] = $uid;
// fin de la fonction de sécurité
	

//debug_var();
	
if (isset($action) and ($action == 'depot_photo') and $total_photo != 0 and $valide_form === 'oui' )  {
	check_token();
	$nb_succes_photos=0;
	$nb_photos_proposees=0;
	$cpt_photo = 0;
	while($cpt_photo < $total_photo) {
		if(isset($_FILES['photo']['type'][$cpt_photo])){
			if($_FILES['photo']['type'][$cpt_photo] != "") {
				$sav_photo = isset($_FILES["photo"]) ? $_FILES["photo"] : NULL;

				$nb_photos_proposees++;

				/*
				echo "\$sav_photo['name'][$cpt_photo]=".$sav_photo['name'][$cpt_photo]."<br />\n";
				echo "preg_match('/jpg$/',\$sav_photo['name'][$cpt_photo])=".preg_match('/jpg$/',$sav_photo['name'][$cpt_photo])."<br />\n";
				echo "\$sav_photo['type'][$cpt_photo]=".$sav_photo['type'][$cpt_photo]."<br />\n";
				*/

				if (!isset($sav_photo['tmp_name'][$cpt_photo]) or ($sav_photo['tmp_name'][$cpt_photo] =='')) {
					//$msg = "Erreur de téléchargement niveau 1.";
					$msg .= "Erreur de téléchargement niveau 1 pour la photo $cpt_photo: '".$sav_photo['name'][$cpt_photo]."'<br />\n";
				} else if (!file_exists($sav_photo['tmp_name'][$cpt_photo])) {
					//$msg = "Erreur de téléchargement niveau 2.";
					$msg .= "Erreur de téléchargement niveau 2 pour la photo $cpt_photo: '".$sav_photo['name'][$cpt_photo]."'<br />\n";
				//} else if ((!preg_match('/jpg$/',$sav_photo['name'][$cpt_photo])) and $sav_photo['type'][$cpt_photo] == "image/jpeg"){
				} else if (!(preg_match('/\.jpg/i',$sav_photo['name'][$cpt_photo]) || preg_match('/\.jpeg/i',$sav_photo['name'][$cpt_photo])) || $sav_photo['type'][$cpt_photo] != "image/jpeg"){
					//$msg = "Erreur : seuls les fichiers ayant l'extension .jpg sont autorisés.";
					$msg .= "Erreur : seuls les fichiers ayant l'extension .jpg ou .jpeg sont autorisés: '".$sav_photo['name'][$cpt_photo]."'<br />\n";
				} else {
					$dest = $rep_photos;
					$n = 0;
					//$nom_corrige = my_ereg_replace("[^.a-zA-Z0-9_=-]+", "_", $sav_photo['name'][$cpt_photo]);
					if (!deplacer_fichier_upload($sav_photo['tmp_name'][$cpt_photo], $rep_photos.$quiestce[$cpt_photo].".jpg")) {
						//$msg = "Problème de transfert : le fichier n'a pas pu être transféré sur le répertoire photos/personnels/";
						$msg = "Problème de transfert : le fichier '".$sav_photo['name'][$cpt_photo]."' n'a pas pu être transféré sur le répertoire photos/personnels/<br />\n";
					} else {
						//$msg = "Téléchargement réussi.";
						$nb_succes_photos++;
						if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
							// si le redimensionnement des photos est activé on redimenssionne
							$source = imagecreatefromjpeg($rep_photos.$quiestce[$cpt_photo].".jpg"); // La photo est la source
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
							imagejpeg($destination, $rep_photos.$quiestce[$cpt_photo].".jpg",100);
						}
					}
				}
			}
		}
		$cpt_photo = $cpt_photo + 1;
	}

	if(($nb_photos_proposees==$nb_succes_photos)&&($nb_photos_proposees>0)) {
		if($nb_succes_photos==1){
			$msg.="Téléchargement réussi.";
		}
		else{
			$msg.="Téléchargements réussis.";
		}
	}
}
// fin de l'envoi des photos du trombinoscope

unset($mode);
$mode = isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : '');

$tab_statuts=array('administrateur','cpe','professeur','scolarite','secours','autre');
$afficher_statut=isset($_POST['afficher_statut']) ? $_POST['afficher_statut'] : (isset($_GET['afficher_statut']) ? $_GET['afficher_statut'] : "");
$afficher_auth_mode=isset($_POST['afficher_auth_mode']) ? $_POST['afficher_auth_mode'] : (isset($_GET['afficher_auth_mode']) ? $_GET['afficher_auth_mode'] : "");
$tab_auth_mode=array('gepi', 'ldap', 'sso');

//**************** EN-TETE *****************************
if($mode=='personnels') {
	$titre_page = "Gestion des personnels";
}
/*
elseif($mode=='eleves') {
	$titre_page = "Gestion des comptes élèves";
}
elseif($mode=='responsables') {
	$titre_page = "Gestion des comptes responsables";
}
*/
else {
	$titre_page = "Gestion des utilisateurs";
}
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************************

//echo "\$total_photo=$total_photo<br />\$nb_succes_photos=$nb_succes_photos<br />\$nb_photos_proposees=$nb_photos_proposees<br />";

unset($display);
$display = isset($_POST["display"]) ? $_POST["display"] : (isset($_GET["display"]) ? $_GET["display"] : (getSettingValue("display_users")!='' ? getSettingValue("display_users"): 'tous'));
// on sauve le choix par défaut
saveSetting("display_users", $display);

unset($order_by);
$order_by = isset($_POST["order_by"]) ? $_POST["order_by"] : (isset($_GET["order_by"]) ? $_GET["order_by"] : 'nom,prenom');
$chemin_retour = urlencode($_SERVER['REQUEST_URI']);
$_SESSION['chemin_retour'] = "../utilisateurs/index.php";

//unset($mode);
//$mode = isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : '');

if ($mode != "personnels") {
?>
<p class="bold">
<a href="../accueil_admin.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
<br/><br/>
<p>Sur cette page, vous pouvez gérer les comptes d'accès des utilisateurs ayant accès à Gepi grâce à un identifiant et un mot de passe.</p>
<p>Cliquez sur le type d'utilisateurs que vous souhaitez gérer :</p>
<p style='padding-left: 10%; margin-top: 15px;'><a href="index.php?mode=personnels"><img src='../images/icons/forward.png' alt='Personnels' class='back_link' /> Personnels de l'établissement (professeurs, scolarité, CPE, administrateurs)</a></p>
<p style='padding-left: 10%; margin-top: 15px;'><a href="edit_responsable.php"><img src='../images/icons/forward.png' alt='Responsables' class='back_link' /> Responsables d'élèves (parents)</a></p>
<p style='padding-left: 10%; margin-top: 15px;'><a href="edit_eleve.php"><img src='../images/icons/forward.png' alt='Eleves' class='back_link' /> Élèves</a></p>
<?php
} else {
?>
<p class="bold">
<a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
 | <a href="modify_user.php">Ajouter un personnel</a>
<?php

if ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon" and getSettingValue('use_sso') != "lcs" and getSettingValue("use_sso") != "ldap_scribe") OR $block_sso) {
    /*
	echo " | Réinitialiser mots de passe : " .
    		"<a href=\"reset_passwords.php\" onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir effectuer cette opération ?\\n Celle-ci est irréversible, et réinitialisera les mots de passe de tous les utilisateurs marqués actifs, avec un mot de passe alpha-numérique généré aléatoirement.\\n En cliquant sur OK, vous lancerez la procédure, qui génèrera une page contenant les fiches-bienvenue à imprimer immédiatement pour distribution aux utilisateurs concernés.')\" target='_blank'>HTML</a>" .
    		" - <a href=\"reset_passwords.php?mode=csv\" onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir effectuer cette opération ?\\n Celle-ci est irréversible, et réinitialisera les mots de passe de tous les utilisateurs marqués actifs, avec un mot de passe alpha-numérique généré aléatoirement.\\n En cliquant sur OK, vous lancerez la procédure, qui génèrera un fichier CSV contenant les informations nécessaires à un traitement automatisé.')\" target='_blank'>CSV</a>";
	*/
    echo " | Réinitialiser mots de passe : " .
    		"<a href=\"reset_passwords.php?mode=html".add_token_in_url()."\" onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir effectuer cette opération ?\\n Celle-ci est irréversible, et réinitialisera les mots de passe de tous les utilisateurs marqués actifs, avec un mot de passe alpha-numérique généré aléatoirement.\\n En cliquant sur OK, vous lancerez la procédure, qui génèrera une page contenant les fiches-bienvenue à imprimer immédiatement pour distribution aux utilisateurs concernés.')\" target='_blank'>HTML</a>" .
    		" - <a href=\"reset_passwords.php?mode=csv".add_token_in_url()."\" onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir effectuer cette opération ?\\n Celle-ci est irréversible, et réinitialisera les mots de passe de tous les utilisateurs marqués actifs, avec un mot de passe alpha-numérique généré aléatoirement.\\n En cliquant sur OK, vous lancerez la procédure, qui génèrera un fichier CSV contenant les informations nécessaires à un traitement automatisé.')\" target='_blank'>CSV</a>";

	echo " | <a href='impression_bienvenue.php?mode=personnels'>Fiches bienvenue</a>";
}
?>
 | Affecter les matières aux professeurs&nbsp;: <a href="tab_profs_matieres.php">Mode 1</a>
 - <a href='../init_xml2/init_alternatif.php?cat=profs'>Mode 2</a>

 | <a href="javascript:centrerpopup('help.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')">Aide</a>
 <?php
if (getSettingValue("statuts_prives") == "y") {
	echo '
	&nbsp;|&nbsp;<a href="./creer_statut.php">Statuts personnalis&eacute;s</a>';
}
?>
</p>
<!--p class='small'><a href="import_prof_csv.php">Télécharger le fichier des professeurs au format csv</a>  (nom - prénom - identifiant GEPI)</p-->
<p class='small'>Télécharger au format csv (<i>nom - prénom - identifiant GEPI</i>) le fichier des <a href="import_prof_csv.php?export_statut=professeur">professeurs</a>, <a href="import_prof_csv.php?export_statut=scolarite">"scolarité"</a>, <a href="import_prof_csv.php?export_statut=cpe">cpe</a>, <a href="import_prof_csv.php?export_statut=secours">secours</a>, <a href="import_prof_csv.php?export_statut=administrateur">administrateurs</a>, <a href="import_prof_csv.php?export_statut=autre">autres</a>, <a href="import_prof_csv.php?export_statut=personnels">personnels</a></p>

<form enctype="multipart/form-data" action="index.php" name="form1" method="post">
<?php
//echo add_token_field();
?>
<table border='0' summary='Tableau de choix'>
<tr>
<td><p>Afficher : </p></td>
<td><p><label for='display_tous' style='cursor: pointer;'>tous les utilisateurs</label> <input type="radio" name="display" id='display_tous' value='tous' <?php if ($display=='tous') {echo " checked";} ?> onchange="document.forms['form1'].submit();" /></p></td>
<td><p>
 &nbsp;&nbsp;<label for='display_actifs' style='cursor: pointer;'>les utilisateurs actifs</label> <input type="radio" id='display_actifs' name="display" value='actifs' <?php if ($display=='actifs') {echo " checked";} ?> onchange="document.forms['form1'].submit();" /></p></td>
 <td><p>
 &nbsp;&nbsp;<label for='display_inactifs' style='cursor: pointer;'>les utilisateurs inactifs</label> <input type="radio" name="display" id='display_inactifs' value='inactifs' <?php if ($display=='inactifs') {echo " checked";} ?> onchange="document.forms['form1'].submit();" /></p></td>


 <td>
<p>
 &nbsp;&nbsp;<select name='afficher_statut' onchange="document.forms['form1'].submit();">
<option value=''>Tout statut</option>
<?php
echo "<option value='administrateur'\n";
if($afficher_statut=="administrateur") {echo " selected='true'";}
echo ">Administrateurs</option>\n";
echo "<option value='cpe'\n";
if($afficher_statut=="cpe") {echo " selected='true'";}
echo ">Cpe</option>\n";
echo "<option value='professeur'\n";
if($afficher_statut=="professeur") {echo " selected='true'";}
echo ">Professeurs</option>\n";
echo "<option value='scolarite'\n";
if($afficher_statut=="scolarite") {echo " selected='true'";}
echo ">Scolarité</option>\n";
echo "<option value='secours'\n";
if($afficher_statut=="secours") {echo " selected='true'";}
echo ">Secours</option>\n";
echo "<option value='autre'\n";
if($afficher_statut=="autre") {echo " selected='true'";}
echo ">Autre</option>\n";

?>
</select>
</p>
</td>

<?php
$sql="SELECT DISTINCT auth_mode FROM utilisateurs ORDER BY auth_mode;";
$test_auth_mode=mysql_query($sql);
if(mysql_num_rows($test_auth_mode)==1) {
	$lig_auth_mode=mysql_fetch_object($test_auth_mode);
	echo "<input type='hidden' name='afficher_auth_mode' value='$lig_auth_mode->auth_mode' />\n";
}
else {
	echo "<td>\n";
	echo "<p>\n";
	echo "&nbsp;&nbsp;<select name='afficher_auth_mode' onchange=\"document.forms['form1'].submit();\">
	<option value=''>Tout auth_mode</option>\n";
	while($lig_auth_mode=mysql_fetch_object($test_auth_mode)) {
		echo "<option value='$lig_auth_mode->auth_mode'\n";
		if($afficher_auth_mode=="$lig_auth_mode->auth_mode") {echo " selected='true'";}
		echo ">$lig_auth_mode->auth_mode</option>\n";
	}
	echo "</select>\n";
	echo "</p>\n";
	echo "</td>\n";
}
?>

 <td>
	<p><input type='submit' id='bouton_valider' value='Valider' /></p>
	<script type='text/javascript'>
		document.getElementById('bouton_valider').style.display='none';
	</script>
</td>
 </tr>
 </table>

<input type='hidden' name='mode' value='<?php echo $mode; ?>' />
<input type='hidden' name='order_by' value='<?php echo $order_by; ?>' />
</form>



<form enctype="multipart/form-data" action="index.php" name="form2" method="post">
<?php
echo add_token_field();

echo "<input type='hidden' name='display' value='$display' />
<input type='hidden' name='afficher_auth_mode' value='$afficher_auth_mode' />
<input type='hidden' name='afficher_statut' value='$afficher_statut' />
<input type='hidden' name='mode' value='$mode' />
<input type='hidden' name='order_by' value='$order_by' />\n";

?>

<?php
// Affichage du tableau
//echo "<table border=1 cellpadding=3>\n";
echo "<table class='boireaus' cellpadding='3' summary='Tableau des utilisateurs'>\n";
echo "<tr><th><p class=small><b><a href='index.php?mode=$mode&amp;order_by=login&amp;display=$display";
if($afficher_statut!="") {echo "&amp;afficher_statut=$afficher_statut";}
if($afficher_auth_mode!="") {echo "&amp;afficher_auth_mode=$afficher_auth_mode";}
echo "'>Nom de login</a></b></p></th>\n";
echo "<th><p class=small><b><a href='index.php?mode=$mode&amp;order_by=nom,prenom&amp;display=$display";
if($afficher_statut!="") {echo "&amp;afficher_statut=$afficher_statut";}
if($afficher_auth_mode!="") {echo "&amp;afficher_auth_mode=$afficher_auth_mode";}
echo "'>Nom et prénom</a></b></p></th>\n";
echo "<th><p class=small><b><a href='index.php?mode=$mode&amp;order_by=statut,nom,prenom&amp;display=$display";
if($afficher_statut!="") {echo "&amp;afficher_statut=$afficher_statut";}
if($afficher_auth_mode!="") {echo "&amp;afficher_auth_mode=$afficher_auth_mode";}
echo "'>Statut</a></b></p></th>\n";
echo "<th><p class=small><b>matière(s) si professeur</b></p></th>\n";
echo "<th><p class=small><b>classe(s)</b></p></th>\n";
echo "<th><p class=small><b>".getSettingValue('gepi_prof_suivi')."</b></p></th>\n";
echo "<th><p class=small><b>supprimer</b></p></th>\n";
echo "<th><p class=small><b>imprimer fiche bienvenue</b></p></th>\n";
    if (getSettingValue("active_module_trombinoscopes")=='y') {
    	echo "<th><p><input type='submit' value='Télécharger les photos' name='bouton1' /></th>\n";
    }
echo "</tr>\n";
if(($afficher_statut!="")&&(in_array($afficher_statut,$tab_statuts))) {
	if(($afficher_auth_mode!="")&&(in_array($afficher_auth_mode,$tab_auth_mode))) {
		$sql="SELECT * FROM utilisateurs WHERE (statut = '$afficher_statut' AND auth_mode='$afficher_auth_mode')
			ORDER BY $order_by;";
	}
	else {
		$sql="SELECT * FROM utilisateurs WHERE (statut = '$afficher_statut') 
			ORDER BY $order_by;";
	}
}
else {
	if(($afficher_auth_mode!="")&&(in_array($afficher_auth_mode,$tab_auth_mode))) {
		$sql="SELECT * FROM utilisateurs WHERE ((
			statut = 'administrateur' OR 
			statut = 'professeur' OR 
			statut = 'scolarite' OR 
			statut = 'cpe' OR 
			statut = 'secours' OR 
			statut = 'autre')
			AND auth_mode='$afficher_auth_mode')
			ORDER BY $order_by;";
	}
	else {
		$sql="SELECT * FROM utilisateurs WHERE (
			statut = 'administrateur' OR 
			statut = 'professeur' OR 
			statut = 'scolarite' OR 
			statut = 'cpe' OR 
			statut = 'secours' OR 
			statut = 'autre') 
			ORDER BY $order_by;";
	}
}
$calldata = mysql_query($sql);
$nombreligne = mysql_num_rows($calldata);
$i = 0;
$alt=1;
while ($i < $nombreligne){
    $user_nom = mysql_result($calldata, $i, "nom");
    $user_prenom = mysql_result($calldata, $i, "prenom");
    // rajout trombinoscope
    $user_civilite = mysql_result($calldata, $i, "civilite");
    // fin de rajout trombinoscope
    $user_statut = mysql_result($calldata, $i, "statut");
    $user_login = mysql_result($calldata, $i, "login");
    $user_pwd = mysql_result($calldata, $i, "password");
    $user_etat[$i] = mysql_result($calldata, $i, "etat");
//    $date_verrouillage[$i] = mysql_result($calldata, $i, "date_verrouillage");
    if (($user_etat[$i] == 'actif') and (($display == 'tous') or ($display == 'actifs'))) {
        $affiche = 'yes';
    } else if (($user_etat[$i] != 'actif') and (($display == 'tous') or ($display == 'inactifs'))) {
        $affiche = 'yes';
    } else {
        $affiche = 'no';
    }
    if ($affiche == 'yes') {
    // Affichage des login, noms et prénoms
    $col[$i][1] = $user_login;
    $col[$i][2] = "$user_nom $user_prenom";
    $col[$i][2] .= "<a name='$user_login'></a>";
    // ajout pour le trombinoscope
    $col[$i]['civ'] = $user_civilite;
    // fin ajout

	//echo "<p>Contrôle des matières de $user_login: <br />\n";
    $call_matieres = mysql_query("SELECT * FROM j_professeurs_matieres j WHERE j.id_professeur = '$user_login' ORDER BY ordre_matieres");
    $nb_mat = mysql_num_rows($call_matieres);
    $k = 0;
	$kk=0;
    while ($k < $nb_mat) {
        $user_matiere_id = mysql_result($call_matieres, $k, "id_matiere");
		//echo "SELECT matiere FROM matieres WHERE matiere='$user_matiere_id'<br />\n";
        //$user_matiere[$k] = mysql_result(mysql_query("SELECT matiere FROM matieres WHERE matiere='$user_matiere_id'"),0);
		$sql="SELECT matiere FROM matieres WHERE matiere='$user_matiere_id';";
		$res_test_matiere=mysql_query($sql);
		if(mysql_num_rows($res_test_matiere)>0) {
			$user_matiere[$kk] = mysql_result($res_test_matiere,0);
			$kk++;
		}
		else {
			echo "<span style='color:red;'>Anomalie:</span> La matière '$user_matiere_id' n'existe plus mais reste asociée à '$user_login'.<br />Recréez la matière (<i>puis supprimez la proprement si nécessaire</i>)<br />\n";
		}
		$k++;
    }

    // Affichage du statut
    $col[$i][3]=$user_statut;
	$col[$i][7]=$user_statut; // le status de de la personne
    if ($user_statut == "administrateur") { $color_='red';}
    if ($user_statut == "secours") { $color_='red';}
    if ($user_statut == "professeur") { $color_='green'; }
    if ($user_statut != "administrateur" AND $user_statut != "professeur" AND $user_statut != "secours") { $color_='blue';}
    $col[$i][3] = "<font color=".$color_.">".$col[$i][3]."</font>";

    // Cas LCS : on précise le type d'utilisateur (local ou LCS)
    if (getSettingValue("use_sso") == "lcs")
        if ($user_pwd != "")
            $col[$i][3] .= '<br />(utilisateur local)';
        else
            $col[$i][3] .= '<br />(utilisateur LCS)';
    //if (($display == 'tous') and ($user_etat[$i]=='inactif')) $col[$i][3] .= '<br />(inactif)';
    if (($display == 'tous') and ($user_etat[$i]=='inactif')) $col[$i][3] .= '<br />(<span style="color:red;">inactif</span>)';

    // Affichage des enseignements
    $k = 0;
    $col[$i][4] = '';
    while ($k < $nb_mat) {
        //$col[$i][4]=$col[$i][4]." $user_matiere[$k] - ";
        if(isset($user_matiere[$k])) {$col[$i][4]=$col[$i][4]." $user_matiere[$k] - ";}
        $k++;
    }
    if ($col[$i][4]=='') {$col[$i][4] = "&nbsp;";}


	$col[$i][5] = '';
	// Pour les professeurs
	if ($user_statut == "professeur") {
		// Affichage des classes/enseignements
		$sql="SELECT g.id group_id, g.name name, c.classe classe, c.id classe_id " .
				"FROM j_groupes_professeurs jgp, j_groupes_classes jgc, groupes g, classes c WHERE (" .
				"jgp.login = '$user_login' and " .
				"g.id = jgp.id_groupe and " .
				"jgc.id_groupe = jgp.id_groupe and " .
				"c.id = jgc.id_classe) order by c.classe;";
		$call_classes = mysql_query($sql);
		$nb_classes = mysql_num_rows($call_classes);
		$k = 0;
		while ($k < $nb_classes) {
			$user_classe['classe_nom_court'] = mysql_result($call_classes, $k, "classe");
			$user_classe['matiere_nom_court'] = mysql_result($call_classes, $k, "name");
			$user_classe['classe_id'] = mysql_result($call_classes, $k, "classe_id");
			$user_classe['group_id'] = mysql_result($call_classes, $k, "group_id");
	
			$col[$i][5] .= "<a href='../groupes/edit_group.php?id_classe=".$user_classe["classe_id"] . "&amp;id_groupe=".$user_classe["group_id"] . "&amp;chemin_retour=$chemin_retour&amp;ancre=$user_login'>" . $user_classe['classe_nom_court']." (".$user_classe['matiere_nom_court'].")</a>\n";
	
			// Génération d'un CSV du groupe
			//$col[$i][5] .= "<a href='../groupes/mes_listes.php?id_groupe=".$user_classe["group_id"] . "' target='_blank'><img src='../images/icons/document.png' width='16' height='16' /></a>\n";
	
			$col[$i][5] .= "<br />\n";
	
			$k++;
		}
	}

	// Pour les CPE
	if ($user_statut == "cpe") {
		$sql="SELECT DISTINCT c.id, c.classe " .
				"FROM j_eleves_cpe jecpe, j_eleves_classes jec, classes c WHERE (" .
				"jecpe.cpe_login = '$user_login' and " .
				"jecpe.e_login = jec.login and " .
				"jec.id_classe = c.id) order by c.classe;";
		//echo "$sql<br />";
		$call_classes = mysql_query($sql);
		$nb_classes = mysql_num_rows($call_classes);
		$k = 0;
		$col[$i][5] = '';
		while ($k < $nb_classes) {
			$user_classe['classe_nom_court'] = mysql_result($call_classes, $k, "classe");
			$user_classe['classe_id'] = mysql_result($call_classes, $k, "id");
	
			//$col[$i][5] .= "<a href='../groupes/edit_group.php?id_classe=".$user_classe["classe_id"] . "&amp;id_groupe=".$user_classe["group_id"] . "&amp;chemin_retour=$chemin_retour&amp;ancre=$user_login'>" . $user_classe['classe_nom_court']." (".$user_classe['matiere_nom_court'].")</a>\n";
			$col[$i][5] .= $user_classe['classe_nom_court'];
	
			$col[$i][5] .= "<br />\n";
	
			$k++;
		}
	}

	// Pour les comptes scolarité
	if ($user_statut == "scolarite") {
		$sql="SELECT DISTINCT c.id, c.classe " .
				"FROM j_scol_classes jsc, classes c WHERE (" .
				"jsc.login = '$user_login' and " .
				"jsc.id_classe = c.id) order by c.classe;";
		//echo "$sql<br />";
		$call_classes = mysql_query($sql);
		$nb_classes = mysql_num_rows($call_classes);
		$k = 0;
		$col[$i][5] = '';
		while ($k < $nb_classes) {
			$user_classe['classe_nom_court'] = mysql_result($call_classes, $k, "classe");
			$user_classe['classe_id'] = mysql_result($call_classes, $k, "id");
	
			//$col[$i][5] .= "<a href='../groupes/edit_group.php?id_classe=".$user_classe["classe_id"] . "&amp;id_groupe=".$user_classe["group_id"] . "&amp;chemin_retour=$chemin_retour&amp;ancre=$user_login'>" . $user_classe['classe_nom_court']." (".$user_classe['matiere_nom_court'].")</a>\n";
			$col[$i][5] .= $user_classe['classe_nom_court'];
	
			$col[$i][5] .= "<br />\n";
	
			$k++;
		}
	}

    if ($col[$i][5]=='') {$col[$i][5] = "&nbsp;";}

    // Affichage de la classe suivie
    $call_suivi = mysql_query("SELECT distinct(id_classe) FROM j_eleves_professeurs j WHERE j.professeur = '$user_login'");
    $nb_classes_suivies = mysql_num_rows($call_suivi);
    $k = 0;
    $col[$i][6] = '';
    while ($k < $nb_classes_suivies) {
        $user_classe_suivie_id = mysql_result($call_suivi, $k, "id_classe");
        $user_classe_suivie = mysql_result(mysql_query("SELECT classe FROM classes WHERE id='$user_classe_suivie_id'"),0);
        $col[$i][6]=$col[$i][6]."$user_classe_suivie<br />\n";
        $k++;
    }
    if ($col[$i][6]=='') {$col[$i][6] = "&nbsp;";}

	/*
    if ($user_etat[$i] == 'actif') {
        $bgcolor = '#E9E9E4';
    } else {
        //$bgcolor = 'darkgrey';
        //$bgcolor = 'darkgray';
        $bgcolor = '#A9A9A9';
    }
    echo "<tr><td bgcolor='$bgcolor'><p class=small><span class=bold>{$col[$i][1]}</span></p></td>\n";
	if ($col[$i][7] == "professeur") {
		echo "<td bgcolor='$bgcolor'><p class=small><span class=bold><a href='modify_user.php?user_login=$user_login'>{$col[$i][2]}</a></span></p>\n";
		echo "<br /><a href='creer_remplacant.php?login_prof_remplace=$user_login'>Créer un remplaçant</a>";
		echo "</td>\n";
	} else {
	  echo "<td bgcolor='$bgcolor'><p class=small><span class=bold><a href='modify_user.php?user_login=$user_login'>{$col[$i][2]}</a></span></p></td>\n";
	}
    echo "<td bgcolor='$bgcolor'><p class=small><span class=bold>{$col[$i][3]}</span></p></td>\n";
    echo "<td bgcolor='$bgcolor'><p class=small><span class=bold>{$col[$i][4]}</span></p></td>\n";
    echo "<td bgcolor='$bgcolor'><p class=small><span class=bold>{$col[$i][5]}</span></p></td>\n";
    // Affichage de la classe suivie
    echo "<td bgcolor='$bgcolor'><p class=small><span class=bold>{$col[$i][6]}</span></p></td>\n";
    // Affichage du lien 'supprimer'
    echo "<td bgcolor='$bgcolor'><p class=small><span class=bold><a href='../lib/confirm_query.php?liste_cible={$col[$i][1]}&amp;action=del_utilisateur&amp;chemin_retour=$chemin_retour'>supprimer</a></span></p></td>\n";
    // Affichage du lien pour l'impression des paramètres
    echo "<td bgcolor='$bgcolor'><p class=small><span class=bold><a target=\"_blank\" href='impression_bienvenue.php?user_login={$col[$i][1]}'>imprimer la 'fiche bienvenue'</a></span></p></td>\n";
	*/

	$alt=$alt*(-1);
	if($user_etat[$i] == 'actif'){
	    echo "<tr class='lig$alt'>\n";
	}
	else{
	    echo "<tr class='lig$alt' style='background-color: slategray'>\n";
	}

	echo "<td><p class='small'><span class='bold'>{$col[$i][1]}</span></p></td>\n";
	if ($col[$i][7] == "professeur") {
		echo "<td><p class='small'><span class='bold'><a href='modify_user.php?user_login=$user_login'>{$col[$i][2]}</a></span></p>\n";
		//echo "<br /><a href='creer_remplacant.php?login_prof_remplace=$user_login'>Créer un remplaçant</a>";
		echo "<br /><a href='creer_remplacant.php?login_prof_remplace=$user_login'><img src='../images/remplacant.png' width='29' height='16' alt='Créer un remplaçant' title='Créer un remplaçant' /></a>";
		echo "</td>\n";
	} else {
	  echo "<td><p class='small'><span class='bold'><a href='modify_user.php?user_login=$user_login'>{$col[$i][2]}</a></span></p></td>\n";
	}
    echo "<td><p class='small'><span class='bold'>{$col[$i][3]}</span></p></td>\n";
    // Si c'est un professeur : matières si c'est un "autre" alors on affiche son statut personnalisé
    if ($col[$i][7] == "autre" AND getSettingValue("statuts_prives") == "y") {
    	// On récupère son statut personnalisé
		$query_s = mysql_query("SELECT nom_statut FROM droits_statut ds, droits_utilisateurs du WHERE login_user = '".$user_login."' AND id_statut = ds.id");
		if ($query_s) {

			$special = mysql_fetch_array($query_s);

		}else{

			$special = '';

		}

		if ($special["nom_statut"] == '') {

			$special["nom_statut"] = '<span style="color: red; font-style: italic;">non d&eacute;fini</span>';

		}

		echo "<td><p class='small'><span class='bold'>Statut pers. : ".$special["nom_statut"]."</span></p></td>\n";

    }else{
	    echo "<td><p class='small'><span class='bold'>{$col[$i][4]}</span></p></td>\n";
	}
	// Liste des enseignements auxquels est associé le professeur
    echo "<td><p class='small'><span class='bold'>{$col[$i][5]}</span></p></td>\n";
    // Affichage de la classe suivie
    echo "<td><p class='small'><span class='bold'>{$col[$i][6]}</span></p></td>\n";
    // Affichage du lien 'supprimer'
    echo "<td><p class='small'><span class='bold'><a href='../lib/confirm_query.php?liste_cible={$col[$i][1]}&amp;action=del_utilisateur&amp;chemin_retour=$chemin_retour".add_token_in_url()."'>supprimer</a></span></p></td>\n";
    // Affichage du lien pour l'impression des paramètres
    echo "<td><p class='small'><span class='bold'><a target=\"_blank\" href='impression_bienvenue.php?user_login={$col[$i][1]}'>imprimer la 'fiche bienvenue'</a></span></p></td>\n";

    // Affichage du téléchargement pour la photo si le module trombi est activé
	if (getSettingValue("active_module_trombinoscopes")=='y') {


        	echo "<td style='white-space: nowrap;'><input name='photo[$i]' type='file' />\n";
			echo "<input type='hidden' name='quiestce[$i]' value='";
			$codephoto = md5(mb_strtolower($col[$i][1]));
			echo $codephoto;
			echo "' />\n";
			$photo = $rep_photos.$codephoto.'.jpg';
			if(file_exists($photo)) {
				echo "<a href='$photo' target='_blank'><img src='../mod_trombinoscopes/images/";
				if($col[$i]['civ'] == 'Mme' or $col[$i]['civ'] == 'Mlle') {
					echo "photo_f.png";
				}
				else {
					echo "photo_g.png";
				}
				echo "' width='32' height='32'  align='middle' border='0' alt='photo présente' title='photo présente' /></a>\n";
			}
			echo "</td>\n";
		}
    // Fin de la ligne courante
    echo "</tr>\n";
    }
    $i++;
}
echo "</table>\n";
// pour le module trombinoscope
   // pour le trombinoscope on met la taille maximal d'une photos
   ?><input type="hidden" name="MAX_FILE_SIZE" value="150000" />
	<input type="hidden" name="action" value="depot_photo" />
	<input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
	<input type="hidden" name="total_photo" value="<?php echo $nombreligne; ?>" /><?php
echo  "</form>\n";
// fin module trombinoscope

} // Fin : si $mode == personnels
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
