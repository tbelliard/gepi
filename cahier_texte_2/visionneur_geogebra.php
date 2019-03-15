<?php
/*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<body>
*/

require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	//header("Location: ../logout.php?auto=1");
	header("Location: ../logout.php?auto=1");
	die();
} else if ($resultat_session == '0') {
	//header("Location: ../logout.php?auto=1");
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/visionneur_geogebra.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_2/visionneur_geogebra.php',
administrateur='F',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='F',
autre='F',
description='Visionneur GeoGebra',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$meta_visionneur_geogebra='y';
require_once("../lib/header.inc.php");
?>

<!--div id='fermeture_page' style='float:right; width:5em; display:none;'><a href='javascript:self.close()'>Fermer</a></div>
<script type='text/javascript'>
	document.getElementById('fermeture_page').style.display='';
</script-->

<?php
	if(isset($_SERVER['HTTP_REFERER'])) {
		echo "<div style='float:right; width:5em;'><a href='".$_SERVER['HTTP_REFERER']."'>Retour</a></div>\n";
	}
?>

<div style='text-align:center;'>

	<h1>Visionneur GeoGebra</h1>
	<script src="https://cdn.geogebra.org/apps/deployggb.js"></script>
	<?php

		//debug_var();

		$url_ggb=isset($_GET['url']) ? $_GET['url'] : NULL;

		//echo "url_ggb=$url_ggb<br />";
		// https://serveur/gepi/cahier_texte_2/visionneur_geogebra.php?url=../documents/cl35082/somme_des_mesures_des_angles_d_un_triangle.ggb
		if(!isset($url_ggb)) {
			echo "<p style='color:red'>Aucun fichier à afficher.</p>\n";
			echo "</body>\n";
			echo "</html>\n";
			die();
		}

		if(!preg_match("/\.ggb$/i", $url_ggb)) {
			echo "<p style='color:red'>Le type du fichier est incorrect.</p>\n";
			echo "</body>\n";
			echo "</html>\n";
			die();
		}

		$multi = (isset($multisite) && $multisite == 'y') ? $_COOKIE['RNE'].'/' : NULL;
		if(isset($multisite) && $multisite == 'y') {
			if((!preg_match("#^\.\./documents/$multi/cl[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.ggb$#i", $url_ggb))&&
			(!preg_match("#^\.\./documents/$multi/cl_dev[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.ggb$#i", $url_ggb))&&
			(!preg_match("#^\.\./documents/archives/$multi/[A-Za-z0-9_-]{1,}/documents/cl[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.ggb$#i", $url_ggb))&&
			(!preg_match("#^\.\./documents/archives/$multi/[A-Za-z0-9_-]{1,}/documents/cl_dev[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.ggb$#i", $url_ggb))) {
				echo "<p style='color:red'>Le chemin du fichier est incorrect.</p>\n";
				echo "</body>\n";
				echo "</html>\n";
				die();
			}
		}
		else {
			if((!preg_match("#^\.\./documents/cl[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.ggb$#i", $url_ggb))&&
			(!preg_match("#^\.\./documents/cl_dev[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.ggb$#i", $url_ggb))&&
			(!preg_match("#^\.\./documents/archives/etablissement/[A-Za-z0-9_-]{1,}/documents/cl[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.ggb$#i", $url_ggb))&&
			(!preg_match("#^\.\./documents/archives/etablissement/[A-Za-z0-9_-]{1,}/documents/cl_dev[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.ggb$#i", $url_ggb))) {
				echo "<p style='color:red'>Le chemin du fichier est incorrect.</p>\n";
				echo "</body>\n";
				echo "</html>\n";
				die();
			}
		}

		//$url_ggb=$gepiPath.preg_replace("#\.\.#", "", $url_ggb);
		$url_ggb0=$url_ggb;
		//echo "url_ggb0=$url_ggb0<br />";

		/*

		$debut_url=preg_replace("#cahier_texte_2/.*#","",$_SERVER['HTTP_REFERER']);
		$url_ggb=$debut_url.preg_replace("#\.\./#", "", $url_ggb);
		//echo "url_ggb=$url_ggb<br />";

		*/
	?>

	<div id="ggb-element" align='center'></div> 

	<script>
		var ggbApp = new GGBApplet({"filename": "<?php echo $url_ggb;?>", "width": 1000, "height": 600, "showToolBar": true, "showAlgebraInput": true, "showMenuBar": true }, true);
		window.addEventListener("load", function() { 
			ggbApp.inject('ggb-element');
		});
	</script>

	<p><em>NOTES&nbsp;:</em> Vous pouvez zoomer dans la fenêtre de la figure GeoGebra en faisant rouler la molette de la souris.</p>

</div>
</body>
</html>
