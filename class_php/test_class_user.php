<?php
$niveau_arbo = 1;
	// Initialisations files
	require_once("../lib/initialisations.inc.php");
		// Resume session
	$resultat_session = resumeSession();
	if ($resultat_session == 'c') {
		header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
		die();
	} else if ($resultat_session == '0') {
		header("Location: ../logout.php?auto=1");
		die();
	};

include("./edt_cours.class.php");
include("./utilisateurs.class.php");


$utilisateur = new prof($_SESSION["login"]);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr"><head>
	<title>Essais edt</title>

	<link rel="stylesheet" type="text/css" href="./style_essai.css" />
</head>

<body>
<?php
/*echo '<p>'.$utilisateur->vraiStatut().'</p>';

echo '<p>fraise</p>';

echo '<hr />';

$cours = new edt(1);

echo $cours->edt_prof;
$rep = $cours->infos();

echo '<p>'.$cours->edt_prof.'</p>';
$sem = $cours->semaine_actu();
echo '<p>'.$sem["type"].'</p>';
*/
$cours = new edtAfficher();
//$edt2 = $cours->edt_jour('lundi', $_SESSION["login"]);


echo '
<div style="width: 880px; height: 60px; border-top: 2px dotted silver;">

<div class="creneau" class="prem">&nbsp;</div>
<div class="creneau" style="margin-left: 80px;">M1</div>
<div class="creneau" style="margin-left: 160px;">M2</div>
<div class="creneau" style="margin-left: 240px;">M3</div>
<div class="creneau" style="margin-left: 320px;">M4</div>
<div class="creneau" style="margin-left: 400px;">M5</div>
<div class="creneau" style="margin-left: 480px;">M6</div>
<div class="creneau" style="margin-left: 560px;">M7</div>
<div class="creneau" style="margin-left: 640px;">M8</div>
<div class="creneau" style="margin-left: 720px;">M9</div>
<div class="creneau_d" style="margin-left: 800px;">M10</div>

</div>

<div style="width: 880px; height: 100px; border-bottom: 2px dotted silver;">';

	$aff = $cours->afficher_cours('lundi', $_SESSION["login"]);

echo '</div>
<div style="width: 880px; height: 100px; border-bottom: 2px dotted silver;">';

	$aff = $cours->afficher_cours('mardi', $_SESSION["login"]);

echo '</div>
<div style="width: 880px; height: 100px; border-bottom: 2px dotted silver;">';

	$aff = $cours->afficher_cours('mercredi', $_SESSION["login"]);

echo '</div>
<div style="width: 880px; height: 100px; border-bottom: 2px dotted silver;">';

	$aff = $cours->afficher_cours('jeudi', $_SESSION["login"]);

echo '</div>
<div style="width: 880px; height: 100px; border-bottom: 2px solid grey;">';

	$aff = $cours->afficher_cours('vendredi', $_SESSION["login"]);

echo '</div>

<div style="width: 880px; height: 60px; border-top: 2px dotted silver;">

<div class="creneauB" class="prem">&nbsp;</div>
<div class="creneauB" style="margin-left: 80px;">M1</div>
<div class="creneauB" style="margin-left: 160px;">M2</div>
<div class="creneauB" style="margin-left: 240px;">M3</div>
<div class="creneauB" style="margin-left: 320px;">M4</div>
<div class="creneauB" style="margin-left: 400px;">M5</div>
<div class="creneauB" style="margin-left: 480px;">M6</div>
<div class="creneauB" style="margin-left: 560px;">M7</div>
<div class="creneauB" style="margin-left: 640px;">M8</div>
<div class="creneauB" style="margin-left: 720px;">M9</div>
<div class="creneau_dB" style="margin-left: 800px;">M10</div>

</div>';

?>

</body></html>