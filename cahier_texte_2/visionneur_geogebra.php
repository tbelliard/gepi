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

	<?php

		//debug_var();

		$url_ggb=isset($_GET['url']) ? $_GET['url'] : NULL;

		//echo "url_ggb=$url_ggb<br />";

		if(!isset($url_ggb)) {
			echo "<p style='color:red'>Aucun fichier à afficher.</p>\n";
			echo "</body>\n";
			echo "</html>\n";
			die();
		}

		if(!preg_match("/ggb/i", $url_ggb)) {
			echo "<p style='color:red'>Le type du fichier est incorrect.</p>\n";
			echo "</body>\n";
			echo "</html>\n";
			die();
		}

		//$url_ggb=$gepiPath.preg_replace("#\.\.#", "", $url_ggb);

		$debut_url=preg_replace("#cahier_texte_2/.*#","",$_SERVER['HTTP_REFERER']);
		$url_ggb=$debut_url.preg_replace("#\.\.#", "", $url_ggb);
		//echo "url_ggb=$url_ggb<br />";
	?>

	<applet code="geogebra.GeoGebraApplet"  archive="geogebra.jar" 
	  codebase="http://jars.geogebra.org/webstart/" 
	  width="800" height="400">
		  <param name="filename" value="<?php echo $url_ggb;?>" />
		  <param name="framePossible" value="false" />
	Il faut installer un <a href='http://www.java.com'>plugin Java</a> (<em>1.5 ou plus récent</em>) pour visionner cette page.
	</applet>
</div>
</body>
</html>
