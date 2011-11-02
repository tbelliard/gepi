<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id: mod_discipline_template.php 4973 2010-07-31 16:50:27Z regis $
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
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<!-- on inclut l'entête -->
	<?php
	  $tbs_bouton_taille = "..";
	  include('../templates/origine/header_template.php');
	?>

	<link rel="stylesheet" type="text/css" href="../templates/origine/css/accueil.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../templates/origine/css/bandeau.css" media="screen" />


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


	<a name="contenu" class="invisible">Début de la page</a>
	
	<p class="center">
	  Ce module est destiné à saisir et suivre les incidents et sanctions.
	</p>

<!-- début corps menu	-->
<?php
	if (count($menuTitre)) {
		foreach ($menuTitre as $newEntreeMenu) {
?>
		<h2 class="<?php echo $newEntreeMenu->classe ?>">
			<img src="<?php echo $newEntreeMenu->icone['chemin'] ?>" alt="<?php echo $newEntreeMenu->icone['alt'] ?>" /> - <?php echo $newEntreeMenu->texte ?>
		</h2>


<?php
		if (count($menuPage)) {
			foreach ($menuPage as $newentree) {
			  if ($newentree->indexMenu==$newEntreeMenu->indexMenu) {

?>
				<div class='div_tableau'>
				  <h3 class="colonne ie_gauche">
					  <a href="<?php echo "../".substr($newentree->chemin,1) ?>">
						  <?php echo $newentree->titre ?>
					  </a>
				  </h3>
				  <p class="colonne ie_droite">
					  <?php echo $newentree->expli ?>
				  </p>
				</div>
<?php
			  }
			}
		}

	  }
	}
?>

<!-- Fin menu	général -->
<p>
  <em>NOTES&nbsp;</em>
</p>
<ul>
  <li>
	<p>
	  Une fois un incident clos, il ne peut plus être modifié et aucune sanction liée ne peut être ajoutée/modifiée/supprimée.
	</p>
  </li>
  <li>
	<p>
	  Le module ne conserve pas un historique des modifications d'un incident.<br />Si plusieurs personnes modifient un incident, elles doivent le faire en bonne intelligence.
	</p>
  </li>
  <li>
	<p>Un professeur peut saisir un incident, mais ne peut pas saisir les sanctions.<br />
Un professeur ne peut modifier que les incidents (<em>non clos</em>) qu'il a lui-même déclaré.<br />Il ne peut consulter que les incidents (<em>et leurs suites</em>) qu'il a déclarés, ou dont il est protagoniste, ou encore dont un des élèves, dont il est professeur principal, est protagoniste.
	</p>
  </li>
  <li>
	<p>
	  <em>A FAIRE:</em>
	  Ajouter des tests 'changement()' dans les pages de saisie pour ne pas quitter une étape sans enregistrer.
	</p>
  </li>
  <li>
	<p>
	  <em>A FAIRE:</em>
	  Permettre de consulter d'autres incidents que les siens propres.<br />Eventuellement avec limitation aux élèves de ses classes.
	</p>
  </li>
  <li>
	<p>
	  <em>A FAIRE ENCORE:</em>
	  Permettre d'archiver les incidents/sanctions d'une année et vider les tables incidents/sanctions lors de l'initialisation pour éviter des blagues avec les login élèves réattribués à de nouveaux élèves (<em>homonymie,...</em>)
	</p>
  </li>
</ul>

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


