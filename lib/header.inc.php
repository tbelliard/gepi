<!DOCTYPE html>
<?php
/**
 * header.inc.php
 * 
 * 
 */

/**
 * Variables à initialiser
 * 
 * $titre_page
 * $racineGepi → chemin relatif vers la racine de GEPI
 * $tbs_last_connection
 */
$tbs_last_connection = isset ($tbs_last_connection) ? $tbs_last_connection : '';
//$titre_page = isset ($titre_page) ? $titre_page : 'GEPI';
$titre_page = isset ($titre_page) ? $titre_page : NULL;
$mode_header_reduit="y";
if (!isset ($racineGepi)) {
	switch  ($niveau_arbo) {
	case '0':
		$racineGepi = '.';
		break;
	case '1':
		$racineGepi = '..';
		break;
	case '2':
		$racineGepi = '../..';
		break;
	case '3':
		$racineGepi = '../../..';
		break;
	case '4':
		$racineGepi = '../../../..';
		break;
	default :
		$racineGepi = '..';
		break;
	}
}

$tbs_bouton_taille = isset ($tbs_bouton_taille) ? $tbs_bouton_taille : $racineGepi ;

if(isset($_SESSION['statut'])) {
	switch ($_SESSION['statut']) {
		case 'professeur':
			include_once $racineGepi.'/edt_organisation/fonctions_calendrier.php';
			break;
		default :
			break;
	}
}

include_once 'header_template.inc.php';
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<!-- on inclut l'entête -->
	<?php include($racineGepi.'/templates/origine/header_template.php'); ?>


	<link rel="stylesheet" type="text/css" href="<?php echo $racineGepi;?>/templates/origine/css/accueil.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo $racineGepi;?>/templates/origine/css/bandeau.css" media="screen" />
	

<!-- corrections internet Exploreur -->
	<!--[if lte IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/accueil_ie.css' media='screen' />
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/bandeau_ie.css' media='screen' />
	<![endif]-->
	<!--[if lte IE 6]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/accueil_ie6.css' media='screen' />
	<![endif]-->
	<!--[if IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/accueil_ie7.css' media='screen' />
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
	
<?php 
// Utilisation de dojo
if (isset($dojo)) {
    echo '<script type="text/javascript" src="'.$gepiPath.'/lib/dojo/dojo/dojo.js" djConfig="parseOnLoad: true"></script>'."\n";
    echo '<link rel="stylesheet" href="'.$gepiPath.'/lib/dojo/dijit/themes/claro/claro.css" />';
}
?>

<!-- Fin des styles -->


</head>

<!-- ******************************************** -->
<!-- Appelle les sous-modèles                     -->
<!-- templates/origine/header_template.php        -->
<!-- templates/origine/accueil_menu_template.php  -->
<!-- templates/origine/bandeau_template.php      -->
<!-- ******************************************** -->

<!-- ************************* -->
<!-- Début du corps de la page -->
<!-- ************************* -->
<body onload="show_message_deconnexion();<?php if($tbs_charger_observeur) echo $tbs_charger_observeur;?>" <?php if (isset($dojo)) {echo 'class=" claro "';}?> >


<!-- on inclut le bandeau -->
	<?php
		if(isset($titre_page)) {
			// On met le maintien_de_la_session() dans le templates/origine/bandeau_template.php
			// pour qu'il soit aussi pris en compte dans les pages template.
			//maintien_de_la_session();
			include($racineGepi.'/templates/origine/bandeau_template.php');
		}
	?>

<!-- fin bandeau_template.html      -->

<div id='container'>

<a id='haut_de_page'></a>
	
	
	
	
	
	
