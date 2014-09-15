<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id$
 *
 * Copyright 2001, 2014 Thomas Belliard, Eric Lebrun, Regis Bouguin, Stephane Boireau
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

  <form action="index_admin.php" id="form1" method="post" style='border: 1px solid grey; background-image: url("../images/background/opacite50.png")'>
<?php
	echo add_token_field();
?>
	
	<h2 class="colleHaut">Configuration générale</h2>
	<p class="italic">
	  La désactivation du module EDT ICAL n'entraîne aucune suppression des données. 
	  Lorsque le module est désactivé, les utilisateurs n'ont pas accès au module.
	</p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Activé ou non</legend>
	  <input type="radio" 
			 name="activer" 
			 id='activer_y' 
			 value="y" 
			<?php if (getSettingAOui("active_edt_ical")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_y' style='cursor: pointer;'>
		Activer le module EDT ICAL
	  </label>
	<br />
	  <input type="radio" 
			 name="activer" 
			 id='activer_n' 
			 value="n" 
			<?php if (!getSettingAOui("active_edt_ical")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_n' style='cursor: pointer;'>
		Désactiver le module EDT ICAL
	  </label>
	</fieldset>

	<p class="center">
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="submit" value="Enregistrer" />
	</p>

</form>

<br />

  <form action="index_admin.php" id="form2" method="post" style='border: 1px solid grey; background-image: url("../images/background/opacite50.png")'>
<?php
	echo add_token_field();
?>
	<input type="hidden" name="is_posted" value="2" />

	<h2 class="colleHaut">Accès aux EDT</h2>

	<p>
	  <input type="checkbox" 
			 name="EdtIcalProf" 
			 id='EdtIcalProf' 
			 value="yes" 
			<?php if (getSettingAOui("EdtIcalProf")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='EdtIcalProf' style='cursor: pointer;'>
		Les professeurs ont accès à leur EDT ICAL ainsi qu'aux EDT de leurs classes.
	  </label>
	  <br />
	  <input type="checkbox" 
			 name="EdtIcalProfTous" 
			 id='EdtIcalProfTous' 
			 value="yes" 
			<?php if (getSettingAOui("EdtIcalProfTous")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='EdtIcalProfTous' style='cursor: pointer;'>
		Les professeurs ont accès aux EDT ICAL de toutes les classes et de tous les professeurs.
	  </label>
	  <br />
	</p>

	<p>
	  <input type="checkbox" 
			 name="EdtIcalEleve" 
			 id='EdtIcalEleve' 
			 value="yes" 
			<?php if (getSettingAOui("EdtIcalEleve")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='EdtIcalEleve' style='cursor: pointer;'>
		Les élèves ont accès à l'EDT ICAL de leur classe.
	  </label>
	  <br />
	  <input type="checkbox" 
			 name="EdtIcalResponsable" 
			 id='EdtIcalResponsable' 
			 value="yes" 
			<?php if (getSettingAOui("EdtIcalResponsable")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='EdtIcalResponsable' style='cursor: pointer;'>
		Les responsables (<em>parents,...</em>) ont accès aux EDT ICAL des élèves dont ils sont responsables.
	  </label>
	  <br />
	</p>

	<p class="center">
	  <input type="submit" value="Enregistrer" />
	</p>
</form>

<br />

  <form action="index_admin.php" id="form3" method="post" style='border: 1px solid grey; background-image: url("../images/background/opacite50.png")'>
<?php
	echo add_token_field();
?>
	<input type="hidden" name="is_posted" value="3" />

	<h2 class="colleHaut">Accès à la mise en place des EDT</h2>

	<p>Les EDT de ce module sont mis en place par envoi/upload de fichiers ICAL/ICS.</p>

	<p>
	  <input type="checkbox" 
			 name="EdtIcalUploadScolarite" 
			 id='EdtIcalUploadScolarite' 
			 value="yes" 
			<?php if (getSettingAOui("EdtIcalUploadScolarite")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='EdtIcalUploadScolarite' style='cursor: pointer;'>
		Les comptes scolarité ont accès à l'envoi/upload des fichiers ICAL/ICS pour mettre en place, mettre à jour les EDT.
	  </label>
	  <br />
	  <input type="checkbox" 
			 name="EdtIcalUploadCpe" 
			 id='EdtIcalUploadCpe' 
			 value="yes" 
			<?php if (getSettingAOui("EdtIcalUploadCpe")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='EdtIcalUploadCpe' style='cursor: pointer;'>
		Les comptes CPE ont accès à l'envoi/upload des fichiers ICAL/ICS pour mettre en place, mettre à jour les EDT.
	  </label>
	  <br />
	</p>

	<p class="center">
	  <input type="submit" value="Enregistrer" />
	</p>
</form>

<br />

  <form action="index_admin.php" id="form4" method="post" style='border: 1px solid grey; background-image: url("../images/background/opacite50.png")'>
<?php
	echo add_token_field();
?>
	<input type="hidden" name="is_posted" value="4" />

	<h2 class="colleHaut">Format des désignations professeurs et matirèes dans EDT</h2>

	<p>Lors de l'import, il vous est proposé d'effectuer les correspondances NOM_EDT/NOM_GEPI non encore enregistrées.<br />
	Le format choisi dans EDT peut varier.</p>

	<p>
	Noms des professeurs dans EDT&nbsp;:<br />
	  <input type="radio" 
			 name="EdtIcalFormatNomProf" 
			 id='EdtIcalFormatNomProf' 
			 value="civ nom prenom" 
			<?php if (getSettingValue("EdtIcalFormatNomProf")=="civ nom prenom") echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='EdtIcalFormatNomProf' style='cursor: pointer;'>
		Civilité Nom Prénom
	  </label>
	  <br />
	  <input type="radio" 
			 name="EdtIcalFormatNomProf" 
			 id='EdtIcalFormatNomProf_nom' 
			 value="nom" 
			<?php if (getSettingValue("EdtIcalFormatNomProf")=="nom") echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='EdtIcalFormatNomProf_nom' style='cursor: pointer;'>
		Nom
	  </label>
	  <br />
	</p>

	<p>
	Noms des matières dans EDT&nbsp;:<br />
	  <input type="radio" 
			 name="EdtIcalFormatNomMatière" 
			 id='EdtIcalFormatNomMatière_nom_court' 
			 value="nom_court" 
			<?php if (getSettingValue("EdtIcalFormatNomMatière")=="nom_court") echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='EdtIcalFormatNomMatière_nom_court' style='cursor: pointer;'>
		Nom court de matière
	  </label>
	  <br />
	  <input type="radio" 
			 name="EdtIcalFormatNomMatière" 
			 id='EdtIcalFormatNomMatière_nom_complet' 
			 value="nom_complet" 
			<?php if (getSettingValue("EdtIcalFormatNomMatière")=="nom_complet") echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='EdtIcalFormatNomMatière_nom_complet' style='cursor: pointer;'>
		Nom complet de matière
	  </label>
	  <br />
	  <input type="radio" 
			 name="EdtIcalFormatNomMatière" 
			 id='EdtIcalFormatNomMatière_nom_court_nom_complet' 
			 value="nom_court nom_complet" 
			<?php if (getSettingValue("EdtIcalFormatNomMatière")=="nom_court nom_complet") echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='EdtIcalFormatNomMatière_nom_court_nom_complet' style='cursor: pointer;'>
		Nom court de matière, suivi d'un espace et du Nom complet de matière
	  </label>
	  <br />
	</p>

	<p class="center">
	  <input type="submit" value="Enregistrer" />
	</p>
</form>

<br />

<p><a href='index.php'>Accéder au module EDT ICAL</a></p>

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


