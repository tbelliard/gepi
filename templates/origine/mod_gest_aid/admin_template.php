<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id: $
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
*/

/**
* Appelle les sous-modèles
* templates/origine/header_template.php
* templates/origine/bandeau_template.php
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

	<h2>Activation du module</h2>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id='form1'>
	  <p>
<?php
echo add_token_field();
?>
      En activant le module, l'administrateur peut définir :
	  </p>
      <ul>
      <li> pour chaque catégorie d'AID, un ou plusieurs "super-gestionnaires" parmi les utilisateurs. Ces utilisateurs auront alors dans leur menu une rubrique supplémentaire leur permettant d'ajouter, supprimer ou modifier des AID, ainsi que des  élèves ou professeurs appartenant à ces AID.</li>
      <li> pour chaque AID un ou plusieurs gestionnaires parmi les utilisateurs. Ces gestionnaires auront alors dans leur menu une rubrique supplémentaire leur permettant d'ajouter ou de supprimer des élèves.</li>
      </ul>

	  <fieldset class="no_bordure">
		<legend class="invisible">Activation</legend>
		<input type='radio'
			   name='activer'
			   id='activer_y'
			   value='y'
			   <?php if (getSettingValue('active_mod_gest_aid')=='y') echo ' checked="checked"'; ?>/>
		<label for='activer_y'>
		  Activer la possibilité de définir des gestionnaires d'AID ou des "super-gestionnaires" de cat&eacute;gorie d'AID
		</label>
		<br />
		<input type='radio'
			   name='activer'
			   id='activer_n'
			   value='n'
			   <?php if (getSettingValue('active_mod_gest_aid')=='n') echo ' checked="checked"'; ?>/>
		<label for='activer_n'>
		  Désactiver la possibilité de définir des gestionnaires d'AID ou des "super-gestionnaires" de cat&eacute;gorie d'AID
		</label>
	  </fieldset>
	  <p class="center">
		<input type='submit' name='valider' value='Valider' />
	  </p>
	</form>



<!-- Début du pied -->
	<div id='EmSize' style='visibility:hidden; position:absolute; left:1em; top:1em;'></div>

	<script type='text/javascript'>
	  //<![CDATA[
		var ele=document.getElementById('EmSize');
		var em2px=ele.offsetLeft
	  //]]>
	</script>


	<script type='text/javascript'>
	  //<![CDATA[
		temporisation_chargement='ok';
	  //]]>
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

