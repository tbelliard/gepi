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

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/visionneur_instrumenpoche.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_2/visionneur_instrumenpoche.php',
administrateur='F',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='F',
autre='F',
description='Visionneur Instrumenpoche',
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

	<h1>Visionneur Instrumenpoche</h1>

	<?php
		if(!file_exists('../lib/iep.swf')) {
			if(!isset($_SERVER['HTTP_REFERER'])) {
				echo "<p class='bold'><a href='../accueil.php'>Retour à l'accueil</a></p>\n";
			}
			echo "
	<p style='color:red'>Le fichier iep.swf n'est pas en place.</p>
</div>
</body>
</html>";
			die();

		}

		//debug_var();

		$url_xml=isset($_GET['url']) ? $_GET['url'] : NULL;

		//echo "url_xml=$url_xml<br />";
		// https://serveur/gepi/cahier_texte_2/visionneur_geogebra.php?url=../documents/cl35082/somme_des_mesures_des_angles_d_un_triangle.ggb
		if(!isset($url_xml)) {
			echo "<p style='color:red'>Aucun fichier à afficher.</p>\n";
			echo "</body>\n";
			echo "</html>\n";
			die();
		}

		if(!preg_match("/\.xml$/i", $url_xml)) {
			echo "<p style='color:red'>Le type du fichier est incorrect.</p>\n";
			echo "</body>\n";
			echo "</html>\n";
			die();
		}

		//echo "\$url_xml=$url_xml<br />";
		$url_racine_gepi=preg_replace("#/*$#", "", getSettingValue('url_racine_gepi'));
		if($url_racine_gepi!='') {
			if(preg_match("#^$url_racine_gepi/documents/#", $url_xml)) {
				$url_xml=preg_replace("#^$url_racine_gepi/documents/#", "../documents/", $url_xml);
			}
		}
		//echo "\$url_xml=$url_xml<br />";

		$multi = (isset($multisite) && $multisite == 'y') ? $_COOKIE['RNE'].'/' : NULL;
		if(isset($multisite) && $multisite == 'y') {
			if((!preg_match("#^\.\./documents/$multi/cl[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.xml$#i", $url_xml))&&
			(!preg_match("#^\.\./documents/$multi/cl_dev[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.xml$#i", $url_xml))&&
			(!preg_match("#^\.\./documents/archives/$multi/[A-Za-z0-9_-]{1,}/documents/cl[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.xml$#i", $url_xml))&&
			(!preg_match("#^\.\./documents/archives/$multi/[A-Za-z0-9_-]{1,}/documents/cl_dev[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.xml$#i", $url_xml))) {
				echo "<p style='color:red'>Le chemin du fichier est incorrect.</p>\n";
				echo "</body>\n";
				echo "</html>\n";
				die();
			}
		}
		else {
			if((!preg_match("#^\.\./documents/cl[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.xml$#i", $url_xml))&&
			(!preg_match("#^\.\./documents/cl_dev[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.xml$#i", $url_xml))&&
			(!preg_match("#^\.\./documents/archives/etablissement/[A-Za-z0-9_-]{1,}/documents/cl[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.xml$#i", $url_xml))&&
			(!preg_match("#^\.\./documents/archives/etablissement/[A-Za-z0-9_-]{1,}/documents/cl_dev[0-9]{1,}/[A-Za-z0-9_=-]{1,}\.xml$#i", $url_xml))) {
				echo "<p style='color:red'>Le chemin du fichier est incorrect.</p>\n";
				echo "</body>\n";
				echo "</html>\n";
				die();
			}
		}

		//$url_xml=$gepiPath.preg_replace("#\.\.#", "", $url_xml);
		$url_xml0=$url_xml;
		//echo "url_xml0=$url_xml0<br />";

		/*
		// Cela provoque une erreur:
		// Le url_xml=https://CHEMIN_CLG/gepi/documents/cl1234/fichier.xml est changé en url_xml=http://CHEMIN_ENT/sg.dodocuments/cl1234/fichier.xml
		if(isset($_SERVER['HTTP_REFERER'])) {
			$debut_url=preg_replace("#cahier_texte_2/.*#","",$_SERVER['HTTP_REFERER']);
			$url_xml=$debut_url.preg_replace("#\.\./#", "", $url_xml);
			//echo "url_xml=$url_xml<br />";
		}
		*/

		//$basename_url_xml=basename($url_xml);
		//echo "basename_url_xml=$basename_url_xml<br />";
	?>

	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" id="instrumenpoche" width="1700" height="800" align="middle">
		<param name="allowScriptAccess" value="sameDomain" />
		<param name="movie" value="../lib/iep.swf?anim=<?php echo $url_xml;?>&config=config_lecture" />
		<param name="quality" value="high" />
		<param name="bgcolor" value="#ffffff" />
		<embed src="../lib/iep.swf?anim=<?php echo $url_xml;?>&config=config_lecture" loop="false" quality="high" bgcolor="#ffffff" width="1700" height="800" swLiveConnect=true id="instrumenpoche" name="instrumenpoche" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>

</div>
</body>
</html>
