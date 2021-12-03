<?php
/*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<body>
*/

// boireaus :
// ___REPERE_POUR_CHEMIN_ALTERNATIF___

require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../logout.php?auto=1");
	die();
} else if ($resultat_session == '0') {

	// Nouvel essai pour essayer d'ouvrir une session SSO CAS:
	require_once("../lib/auth_sso.inc.php");

	if (($resultat_session == '0')||($resultat_session == 'c')) {
		//header("Location: ../logout.php?auto=1");
		header("Location: ../logout.php?auto=1");
		die();
	}
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

$meta_visionneur_instrumentpoche='y';

$style_specifique[]='cahier_texte_2/instrumenpoche.css';
require_once("../lib/header.inc.php");
?>
<?php
	if(isset($_SERVER['HTTP_REFERER'])) {
		echo "<div style='float:right; width:5em;'><a href='".$_SERVER['HTTP_REFERER']."'>Retour</a></div>\n";
	}
?>

<div style='text-align:center;'>

	<h1>Visionneur Instrumenpoche</h1>

	<script type="text/javascript">
		MathJax.Hub.Queue(function() {go()});
	</script>

	<div id="div_svg">
		<svg id="svg" width="1600px" height="800px" xmlns="http://www.w3.org/2000/svg" style='background-color:white'></svg>
	</div>

</div>
</body>
</html>
