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

			// 20241019
			$GLOBALS['dont_get_modalite_elect']=true;

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
	<?php 
		include($racineGepi.'/templates/origine/header_template.php'); 

		if(isset($meta_visionneur_geogebra)) {
			echo "
	<meta name=viewport content=\"width=device-width,initial-scale=1\">";
		}

		if(isset($meta_visionneur_instrumentpoche)) {
			echo "
	<meta http-equiv=\"X-UA-Compatible\" content=\"IE=9\">";
		}

		// Pour améliorer les choses sur un smartphone:
		// https://openweb.eu.org/articles/adapter_site_smartphones
		echo '<meta name="viewport" content="width=device-width, height=device-height" />';

	?>

	<link rel="stylesheet" type="text/css" href="<?php echo $racineGepi;?>/templates/origine/css/accueil.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo $racineGepi;?>/templates/origine/css/bandeau.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo $racineGepi;?>/templates/origine/css/imprimante.css" media="print" />
	
	<?php
	if (isset($tbs_CSS_spe) && count($tbs_CSS_spe)) {
			foreach ($tbs_CSS_spe as $value) {
				if ($value!="") {
					echo "<link rel=\"$value[rel]\" type=\"$value[type]\" href=\"$value[fichier]\" media=\"$value[media]\" />\n";
					// echo "<link rel=\"$value[rel]\" type=\"$value[type]\" href=\"$value[fichier]\" media=\"$value[media] and (max-width: 800px)\" />\n";
				}
			}
			unset($value);
		}
	?>

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
		if ((isset($Style_CSS))&&(is_array($Style_CSS))&&(count($Style_CSS)>0)) {
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
if((isset($charger_js_dragresize))&&($charger_js_dragresize=="y")) {
    echo '<script type="text/javascript" src="'.$gepiPath.'/lib/twinhelix_dragresize.js"></script>'."\n";
    echo '<link rel="stylesheet" href="'.$gepiPath.'/css/twinhelix_dragresize.css" />'."\n";
    echo js_dragresize();
}

if(isset($meta_visionneur_instrumentpoche)) {
	echo "
    <script src=\"iepjsmax.js\"></script>
    <script type=\"text/x-mathjax-config\">
      MathJax.Hub.Config({
        tex2jax: {";
	echo '
          inlineMath: [["$","$"],["\\(","\\)"]]';
	echo "
        },
        jax: [\"input/TeX\",\"output/SVG\"],
        TeX: {extensions: [\"color.js\"]},
        messageStyle:'none'
      });
    </script>
    <script src=\"https://www.mathgraph32.org/js/MathJax/MathJax.js?config=TeX-AMS-MML_SVG-full\"></script>
    <script type=\"text/javascript\">
      function go() {
        try {
          var url = window.location.search.substring(1);
          if (!url) {
            console.error('Absence d’adresse passée dans l’url.');
            return
          }
          var connect = new XMLHttpRequest();
          connect.open(\"GET\", url, true);
          connect.onreadystatechange = function (aEvt) {
            if ((connect.readyState == 4) && (connect.status == 200)) {
              var figure = connect.responseText;
              var svg = document.getElementById(\"svg\");
              var autostart = true;
              var iepapp = new iep.iepApp();
              iepapp.addDoc(svg,figure,autostart);
              window.addEventListener(\"unload\",function() {
                iepapp.closeAllXMLWindows()
              });
            }
          }
          connect.send(null);
        } catch (error) {
          console.error('Plantage : '+error);
        }
      }
    </script>";
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
	
	
	
	
	
	
