<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id$
*/
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>

<!-- on inclut l'entête -->
	<?php include('./../templates/origine/header_template.php');?>
	
	<link rel="stylesheet" type="text/css" href="<?php echo $gepiPath;?>/accessibilite.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo $gepiPath;?>/accessibilite_print.css" media="print" />

	<link rel="stylesheet" type="text/css" href="<?php echo $gepiPath;?>/templates/origine/css/accueil.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo $gepiPath;?>/templates/origine/css/bandeau.css" media="screen" />

<!-- corrections internet Exploreur -->	
	<!--[if lte IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='<?php echo $gepiPath;?>/templates/origine/css/accueil_ie.css' media='screen' />
		<link title='bandeau' rel='stylesheet' type='text/css' href='<?php echo $gepiPath;?>/templates/origine/css/bandeau_ie.css' media='screen' />
	<![endif]-->
	<!--[if lte IE 6]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='<?php echo $gepiPath;?>/templates/origine/css/accueil_ie6.css' media='screen' />
	<![endif]-->
	<!--[if IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='<?php echo $gepiPath;?>/templates/origine/css/accueil_ie7.css' media='screen' />
	<![endif]-->

<!-- Style_screen_ajout.css -->
	<?php
		if (count($Style_CSS)) {
			foreach ($Style_CSS as $value) {
				if ($value!="") {
					echo "<link rel=\"$value[rel]\" type=\"$value[type]\" href=\"$value[fichier]\" media=\"$value[media]\" />\n";
				}
			}
			unset($value);
		}
	?>

<!-- Fin des styles -->
</head>

<body onload="show_message_deconnexion();">	

<!-- on inclut le bandeau -->
	<?php include($gepiPath2.'/templates/origine/bandeau_template.php');?>
	
<!-- fin bandeau_template.html      -->

<div id='container'>

<a name='haut_de_page'></a>

<div class='fixeMilieuDroit'>
	<a href='#haut_de_page'><img src='../images/up.png' width='18' height='18' alt="haut de la page" title="Remonter en haut de la page" /></a>
	<br />
	<a href='#bas_de_page'><img src='../images/down.png' width='18' height='18' alt="bas de la page" title="Descendre en bas de la page" /></a>
</div>

<a name="contenu" class="invisible">Début de la page</a>	
	
<?php	
				if (count($tbs_menu)) {
				$menu=array_values($tbs_menu);
				if ($menu[0]['texte']!="") {
					foreach ($tbs_menu as $value) {
						echo "
	<h2 class='$value[classe]' style='margin-bottom:0;'> 
		<img src='$value[image]' alt='' /> - $value[texte]
	</h2>
				";
						
							echo "
<!-- autres menus -->		
<!-- accueil_menu_template.php -->
				";
						if (count($value['entree'])) {
							foreach ($value['entree'] as $newentree) {
								include($gepiPath2.'/templates/origine/accueil_menu_template.php');
							}
				unset($newentree);
						}
							echo "
<!-- Fin menu	général -->
				";
					}
					unset ($value);
				}
			}
	?>

<a name='bas_de_page'></a>

</div>
</body>
</html>
