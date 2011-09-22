<?php
$niveau_arbo = 1;
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
	};

include("./edt_cours.class.php");
include("./utilisateurs.class.php");


$utilisateur = new prof($_SESSION["login"]);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr"><head>
	<title>Essais edt</title>

	<link rel="stylesheet" type="text/css" href="<?php echo $gepiPath; ?>/edt_organisation/style_edt.css" />
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
	$cours->hauteur_creneau = 60;
	$cours->type_edt = 'classe';
	//$cours->type_edt = 'eleve';
	echo $cours->entete_creneaux('noms');
	//$cours->aff_jour = 'cache';


echo	$cours->afficher_cours_jour('lundi', '2DE12');
//$cours->type_edt = 'eleve';
echo	$aff = $cours->afficher_cours_jour('mardi', '2DE12');

echo	$aff = $cours->afficher_cours_jour('mercredi', '2DE12');

echo	$aff = $cours->afficher_cours_jour('jeudi', '2DE12');

echo	$aff = $cours->afficher_cours_jour('vendredi', '2DE12');

//echo $cours->entete_creneaux('noms');

?>
<br /><br />
</body></html>