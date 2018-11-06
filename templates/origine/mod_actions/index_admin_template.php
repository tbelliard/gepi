<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id$
 *
 * Copyright 2001, 2018 Thomas Belliard, Eric Lebrun, Regis Bouguin, Stephane Boireau
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
	  La désactivation du module <?php echo $terme_mod_action;?>s n'entraîne aucune suppression des données. 
	  Lorsque le module est désactivé, les utilisateurs n'ont pas accès au module.
	</p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Activé ou non</legend>
	  <input type="radio" 
			 name="activer" 
			 id='activer_y' 
			 value="y" 
			<?php if (getSettingAOui("active_mod_actions")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_y' style='cursor: pointer;'>
		Activer le module <?php echo $terme_mod_action;?>s
	  </label>
	<br />
	  <input type="radio" 
			 name="activer" 
			 id='activer_n' 
			 value="n" 
			<?php if (!getSettingAOui("active_mod_actions")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_n' style='cursor: pointer;'>
		Désactiver le module <?php echo $terme_mod_action;?>s
	  </label>
	<br />
	  Terme pour désigner une action&nbsp;: <input type='text' name='terme_mod_action' value="<?php echo $terme_mod_action;?>" />
	<br />
	  <input type="radio" 
			 name="mod_actions_affichage_familles" 
			 id='mod_actions_affichage_familles_y' 
			 value="y" 
			<?php if (getSettingAOui("mod_actions_affichage_familles")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='mod_actions_affichage_familles_y' style='cursor: pointer;'>
		Lorsque la présence/absence d'un enfant est pointée, rendre la présence/absence visible des élèves/familles.
	  </label>
	<br />
	  <input type="radio" 
			 name="mod_actions_affichage_familles" 
			 id='mod_actions_affichage_familles_n' 
			 value="n" 
			<?php if (!getSettingAOui("mod_actions_affichage_familles")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='mod_actions_affichage_familles_n' style='cursor: pointer;'>
		Lorsque la présence/absence d'un enfant est pointée, ne pas rendre la présence/absence visible des élèves/familles.
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

	<h2 class="colleHaut">Catégories de "<?php echo $terme_mod_action;?>"</h2>

	<?php
		$sql="SELECT * FROM mod_actions_categories ORDER BY nom;";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<p>Aucune catégorie n'est encore définie.</p>";
		}
		else {
			echo "<p>Liste des catégories existantes&nbsp;:</p>
			<table class='boireaus boireaus_alt'>
				<thead>
					<tr>
						<th>Id</th>
						<th>Nom</th>
						<th>Description</th>
						<th>Gestionnaire(s)</th>
					</tr>
				</thead>
				<tbody>";
			while($lig=mysqli_fetch_object($res)) {
				echo "
					<tr>
						<td>".$lig->id."</td>
						<td><input type=text' name='nom[".$lig->id."]' value=\"".$lig->nom."\" /></td>
						<td><textarea name='description[".$lig->id."]'>".nl2br($lig->description)."</textarea></td>
						<td>";
				// Récupérer la liste des gestionnaires
				$tab_deja=array();
				$sql="SELECT u.nom, u.prenom, u.statut, mag.* FROM mod_actions_gestionnaires mag, 
												utilisateurs u 
											WHERE u.login=mag.login_user AND 
												u.statut IN ('administrateur', 'cpe', 'scolarite', 'professeur', 'autre') 
											ORDER BY u.nom, u.prenom, u.login;";
				//echo "$sql<br />";
				$res2=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res2)==0) {
					echo "Aucun gestionnaire";
				}
				else {
					$cpt=0;
					while($lig2=mysqli_fetch_object($res2)) {
						echo "
							<input type='checkbox' name='login_user_".$lig->id."[]' id='login_user_".$lig->id."_".$cpt."' value=\"".$lig2->login_user."\" onchange=\"changement();checkbox_change(this.id)\" checked /><label for='login_user_".$lig->id."_".$cpt."' id='texte_login_user_".$lig->id."_".$cpt."' title=\"".$lig2->login_user." (".$lig2->statut.")\" style='font-weight:bold;'>".$lig2->nom." ".$lig2->prenom."</label><br />";
						$cpt++;
						$tab_deja[]=$lig2->login_user;
					}
				}

				$sql="SELECT u.login, u.nom, u.prenom, u.statut FROM utilisateurs u 
											WHERE u.statut IN ('administrateur', 'cpe', 'scolarite', 'professeur', 'autre') 
											ORDER BY u.statut, u.nom, u.prenom, u.login;";
				//echo "$sql<br />";
				$res2=mysqli_query($mysqli, $sql);
				echo "
							<select name='login_user_".$lig->id."[]' id='login_user_".$lig->id."_".$cpt."'>
								<option value=''>-- Ajouter un gestionnaire --</option>";
				if(mysqli_num_rows($res2)>0) {
					$statut_prec='';
					$cpt=0;
					while($lig2=mysqli_fetch_object($res2)) {
						if(!in_array($lig2->login, $tab_deja)) {
							if($lig2->statut!=$statut_prec) {
								if($statut_prec!='') {
									echo "
								</optgroup>";
								}
								echo "
								<optgroup label='".ucfirst($lig2->statut)."'>";
								$statut_prec=$lig2->statut;
							}
							echo "
								<option value=\"".$lig2->login."\" title=\"".$lig2->login."\">".$lig2->nom." ".$lig2->prenom."</option>";
							$cpt++;
						}
					}
					if($cpt>0) {
						echo "
								</optgroup>";
					}
				}
				echo "
							</select>
						</td>
					</tr>";
			}
			echo "
				</tbody>
			</table>";
		}

		// Définir une nouvelle catégorie
		$tab_statuts=array('administrateur', 'cpe', 'scolarite', 'professeur', 'autre');
		echo "
		<p class='bold'>Définir une nouvelle catégorie&nbsp;:</p>
		<table>
			<tr>
				<th>Nom&nbsp;: </th>
				<td><input type=text' name='nouvelle_categorie_nom' value=\"\" /></td>
			</tr>
			<tr>
				<th style='vertical-align:top'>Description&nbsp;: </th>
				<td><textarea name='nouvelle_categorie_description'></textarea></td>
			</tr>
			<tr>
				<th style='vertical-align:top'>Gestionnaires&nbsp;: </th>
				<td>".liste_checkbox_utilisateurs($tab_statuts, array(), 'login_user', 'cocher_decocher', "y", "", "checkbox_change", 'y')."</td>
			</tr>
		</table>";

		echo js_checkbox_change_style('checkbox_change', 'texte_', "y");
	?>

	<p class="center">
	  <input type="submit" value="Enregistrer" />
	</p>
</form>

<!-- ================================================ -->

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


