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

  <form action="index_admin.php" id="form1" method="post" class='fieldset_opacite50'>
<?php
	echo add_token_field();
?>
	
	<h2 class="colleHaut">Configuration générale</h2>
	<p class="italic">
	  La désactivation du module Engagements n'entraîne aucune suppression des données. 
	  Lorsque le module est désactivé, les utilisateurs n'ont pas accès au module.
	</p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Activé ou non</legend>
	  <input type="radio" 
			 name="activer" 
			 id='activer_y' 
			 value="y" 
			<?php if (getSettingAOui("active_mod_engagements")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_y' style='cursor: pointer;'>
		Activer le module Engagements
	  </label>
	<br />
	  <input type="radio" 
			 name="activer" 
			 id='activer_n' 
			 value="n" 
			<?php if (!getSettingAOui("active_mod_engagements")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_n' style='cursor: pointer;'>
		Désactiver le module Engagements
	  </label>
	</fieldset>

	<p class="center">
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="submit" value="Enregistrer" />
	</p>

</form>

<br />

  <form action="index_admin.php" id="form2" method="post" class='fieldset_opacite50'>
<?php
	echo add_token_field();
?>
	<input type="hidden" name="is_posted" value="2" />

	<h2 class="colleHaut">Engagements</h2>

	<p class='bold' style='margin-top:1em;'>Engagements existants&nbsp;:</p>
	<table align='left' class='boireaus boireaus_alt' style='margin-left:1em;'>
		<thead>
			<tr>
				<th style='color:black;' rowspan='2'>Id</th>
				<th style='color:black;' rowspan='2'>Nom</th>
				<th style='color:black;' rowspan='2'>Description</th>
				<th style='color:black;' rowspan='2'>Conseil de classe</th>
				<th style='color:black;' colspan='2'>Statuts visés</th>
				<th style='color:black;' colspan='2'>Statuts saisie</th>
				<th style='color:black;' rowspan='2'>Supprimer cet engagement</th>
			</tr>
			<tr>
				<th style='color:black;'>Élève</th>
				<th style='color:black;'>Responsable</th>
				<th style='color:black;'>Scolarité</th>
				<th style='color:black;'>Cpe</th>
			</tr>
		</thead>
		<tbody>
			<?php
				for($loop=0;$loop<count($tab_engagements['indice']);$loop++) {
					$checked_conseil_de_classe="";
					if($tab_engagements['indice'][$loop]['conseil_de_classe']=="yes") {
						$checked_conseil_de_classe=" checked";
					}
					$checked_ConcerneEleve="";
					if($tab_engagements['indice'][$loop]['ConcerneEleve']=="yes") {
						$checked_ConcerneEleve=" checked";
					}
					$checked_ConcerneResponsable="";
					if($tab_engagements['indice'][$loop]['ConcerneResponsable']=="yes") {
						$checked_ConcerneResponsable=" checked";
					}
					$checked_SaisieScol="";
					if($tab_engagements['indice'][$loop]['SaisieScol']=="yes") {
						$checked_SaisieScol=" checked";
					}
					$checked_SaisieCpe="";
					if($tab_engagements['indice'][$loop]['SaisieCpe']=="yes") {
						$checked_SaisieCpe=" checked";
					}

					echo "
			<tr>
				<td>".$tab_engagements['indice'][$loop]['id']."</td>
				<td><input type='text' name='nom[".$tab_engagements['indice'][$loop]['id']."]' value=\"".$tab_engagements['indice'][$loop]['nom']."\" /></td>
				<td><textarea name='description[".$tab_engagements['indice'][$loop]['id']."]'>".$tab_engagements['indice'][$loop]['description']."</textarea></td>
				<td><input type='checkbox' name='conseil_de_classe[".$tab_engagements['indice'][$loop]['id']."]' value=\"yes\"$checked_conseil_de_classe /></td>
				<td><input type='checkbox' name='ConcerneEleve[".$tab_engagements['indice'][$loop]['id']."]' value=\"yes\"$checked_ConcerneEleve /></td>
				<td><input type='checkbox' name='ConcerneResponsable[".$tab_engagements['indice'][$loop]['id']."]' value=\"yes\"$checked_ConcerneResponsable /></td>
				<td><input type='checkbox' name='SaisieScol[".$tab_engagements['indice'][$loop]['id']."]' value=\"yes\"$checked_SaisieScol /></td>
				<td><input type='checkbox' name='SaisieCpe[".$tab_engagements['indice'][$loop]['id']."]' value=\"yes\"$checked_SaisieCpe /></td>
				<td><input type='checkbox' name='suppr[]' value=\"".$tab_engagements['indice'][$loop]['id']."\" /></td>
			</tr>";
				}
			?>
		</tbody>
	</table>

	<div style='clear:both;'></div>

	<p class='bold' style='margin-top:1em;'>Ajouter un type d'engagement&nbsp;:</p>
	<table style='margin-left:1em;'>
		<tr>
			<td>Nom&nbsp;:</td>
			<td><input type='text' name='AjoutEngagementNom' value='' /></td>
		</tr>
		<tr>
			<td>Description&nbsp;:</td>
			<td><textarea name='AjoutEngagementDescription'></textarea></td>
		</tr>
		<tr>
			<td>Conseil de classe&nbsp;:<br />
			(<em>assiste au conseil de classe</em>)
			</td>
			<td><input type='checkbox' name='AjoutEngagementConseilClasse' id='AjoutEngagementConseilClasse' value='yes' /><label for='AjoutEngagementConseilClasse'>Oui</label></td>
		</tr>
		<tr>
			<td>Statuts visés/concernés&nbsp;:</td>
			<td>
				<input type="checkbox" 
				name="AjoutEngagementEle" 
				id='EngagementEle' 
				value="yes" 
				onchange='changement();' />
				<label for='EngagementEle'>Élève</label>
				<br />

				<input type="checkbox" 
				name="AjoutEngagementResp" 
				id='AjoutEngagementResp' 
				value="yes" 
				onchange='changement();' />
				<label for='AjoutEngagementResp'>Responsable</label>
			</td>
		</tr>
		<tr>
			<td>Statuts effectuant la saisie/désignation&nbsp;:</td>
			<td>
				<input type="checkbox" 
				name="AjoutEngagementSaisieScol" 
				id='EngagementSaisieScol' 
				value="yes" 
				onchange='changement();' />
				<label for='EngagementSaisieScol'>Scolarité</label>
				<br />

				<input type="checkbox" 
				name="AjoutEngagementSaisieCpe" 
				id='EngagementSaisieCpe' 
				value="yes" 
				onchange='changement();' />
				<label for='EngagementSaisieCpe'>Cpe</label>
			</td>
		</tr>
	</table>

	<p class="center">
	  <input type="submit" value="Ajouter/Valider" />
	</p>

	<p style='text-indent:-4em; margin-left:4em; margin-top:1em;'><em>NOTE&nbsp;:</em> En supprimant un engagement, vous supprimez aussi l'association avec les élèves, responsables éventuellement engagés.</p>
</form>

<!-- ================================================ -->

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


