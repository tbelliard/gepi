<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id: index_template.php 4900 2010-07-26 13:40:03Z regis $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
* ******************************************** *
* Appelle les sous-modèles                     *
* templates/origine/header_template.php        *
* templates/origine/bandeau_template.php       *
* ******************************************** *
*/

/**
 *
 * @author regis
 */

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<!-- on inclut l'entête -->
	<?php
	  $tbs_bouton_taille = "..";
	  include('../templates/origine/header_template.php');
	?>

  <script type="text/javascript" src="../templates/origine/lib/fonction_change_ordre_menu.js"></script>

	<link rel="stylesheet" type="text/css" href="../templates/origine/css/accueil.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../templates/origine/css/bandeau.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../templates/origine/css/gestion.css" media="screen" />

<!-- corrections internet Exploreur -->
	<!--[if lte IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/accueil_ie.css' media='screen' />
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/bandeau_ie.css' media='screen' />
	<![endif]-->
	<!--[if lte IE 6]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/accueil_ie6.css' media='screen' />
	<![endif]-->
	<!--[if IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/accueil_ie7.css' media='screen' />
	<![endif]-->


<!-- Style_screen_ajout.css -->
	<?php
		if (count($Style_CSS)) {
			foreach ($Style_CSS as $value) {
				if ($value!="") {
					echo "<link rel=\"$value[rel]\" type=\"$value[type]\" href=\"$value[fichier]\" media=\"$value[media]\" />\n";
				}
			}
		}
	?>

<!-- Fin des styles -->


</head>


<!-- ************************* -->
<!-- Début du corps de la page -->
<!-- ************************* -->
<body onload="show_message_deconnexion();<?php echo $tbs_charger_observeur;?>">

<!-- on inclut le bandeau -->
	<?php include('../templates/origine/bandeau_template.php');?>

<!-- fin bandeau_template.html      -->

  <div id='container'>
<!-- Fin haut de page -->

  <p class="bold">
	Cet outil permet d'autoriser la génération de flux 
	<acronym title="Really Simple Syndication">RSS</acronym> 
	2.0 des cahiers de textes de Gepi. 
  </p>

<?php 
if (count($lienFlux)){
  foreach($lienFlux as $lien){
 ?>
  <p class="bold vert">
	<?php if($lien['confirme']){ ?>
		La table existe et les URI sont en place.&nbsp;&nbsp;
	<?php } ?>
	<a href="<?php echo $lien['lien']; ?>"<?php if($lien['confirme']) echo "onclick=\"return confirm_abandon(this, 'yes', '$themessage')\""; ?>>
	  <?php echo $lien['texte'] ?>
	</a>
  </p>
<?php 
  }
}
 ?>
 
  <form id="form_rss" action="rss_cdt_admin.php" method="post">
	<p>
	  <input type="hidden" name="action" value="modifier" />
	  <input type="checkbox"
			 id="autoRssCdt"
			 name="rss_cdt_ele"
			 value="y"
			 onclick="changementDisplay('accesEle', '');"
			 onchange='document.getElementById("form_rss").submit();'
			<?php echo $checked_ele; ?> />
	  <label for="autoRssCdt">
		Les élèves peuvent utiliser le flux RSS de leur cahier de textes
	  </label>
	</p>
  </form>
  <br />
  
  <div id="accesEle"<?php echo $style_ele; ?>>
	<form id="form_rss_ele" action="rss_cdt_admin.php" method="post">
	  <fieldset>
		<legend>mode de récupération</legend>
		<input type="radio"
			   id="rssAccesEle"
			   name="rss_acces_ele"
			   value="direct"
			   onchange='document.getElementById("form_rss_ele").submit();'
			  <?php echo $style_ele_dir; ?> />
		<label for="rssAccesEle">
		  Les élèves récupèrent l'adresse (url) d'abonnement directement par leur accès à Gepi
		</label>
		<br />
		<input type="radio"
			   id="rssAccesEle2"
			   name="rss_acces_ele"
			   value="csv"
			   onchange='document.getElementById("form_rss_ele").submit();'
			  <?php echo $style_ele_csv; ?> />
		<label for="rssAccesEle2">
		  L'admin récupère un fichier csv de ces adresses (une par élève)
		</label>
	  </fieldset>
	</form>
  </div>






<!-- Début du pied -->
	<div id='EmSize' style='visibility:hidden; position:absolute; left:1em; top:1em;'></div>

	<script type='text/javascript'>
		var ele=document.getElementById('EmSize');
		var em2px=ele.offsetLeft
		//alert('1em == '+em2px+'px');
	</script>


	<script type='text/javascript'>
		temporisation_chargement='ok';
	</script>

</div>

		<?php
			if ($tbs_microtime!="") {
				echo "
   <p class='microtime'>Page générée en ";
   			echo $tbs_microtime;
				echo " sec</p>
   			";
	}
?>

		<?php
			if ($tbs_pmv!="") {
				echo "
	<script type='text/javascript'>
		//<![CDATA[
   			";
				echo $tbs_pmv;
				echo "
		//]]>
	</script>
   			";
		}
?>

</body>
</html>


