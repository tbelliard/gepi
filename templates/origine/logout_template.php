<?php
/*
 * $Id: logout_template.php 7960 2011-08-25 09:41:05Z crob $
 */
?>
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<title>Déconnexion</title>
<link rel="stylesheet" type="text/css" href="<?php echo $gepiPath;?>/css/style.css" />
<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
<link rel="icon" type="image/ico" href="./favicon.ico" />
<?php
	// Styles paramétrables depuis l'interface:
	if($style_screen_ajout=='y'){
		// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
		// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/templates/origine/css/logout.css' />";
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />";
	}
?>
</head>
<body>
<div class="center">
	<h1 class='gepi'><?php echo $titre;?></h1>
	<img src="<?php echo $gepiPath;?>/images/icons/lock-open.png" alt='lock-open' />
	<br/><br/>
<?php
	echo $message;
?>
	<br />
	<a href="<?php echo $gepiPath;?>/login.php<?php if((isset($rne_courant))&&($rne_courant!='')) {echo "?rne=$rne_courant";}?>">Ouvrir une nouvelle session</a>

<?php
$agent = $_SERVER['HTTP_USER_AGENT'];
if (my_eregi("msie",$agent) && !my_eregi("opera",$agent)) {

	echo "<div style='width: 70%; margin: auto;'>";
	echo "<p><b>Note aux utilisateurs de Microsoft Internet Explorer :</b>";
	echo "<br/>Si vous subissez des déconnexions intempestives, si vous n'arrivez pas à vous connecter à Gepi, " .
			"ou bien s'il vous faut répéter plusieurs fois la procédure de connexion avant de pouvoir accéder aux outils de Gepi, " .
			"il est possible que votre navigateur en soit la cause. Nous vous recommandons de télécharger gratuitement et d'installer <a href='http://www.mozilla-europe.org/fr/products/firefox/'>Mozilla Firefox</a>, " .
			"qui vous garantira les meilleures conditions d'utilisation de Gepi.</p>";
	echo "</div>";
}
?>


</div>
</body>
</html>
