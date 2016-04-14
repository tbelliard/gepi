<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id: $
* Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

	<h2>Activation et paramétrage du module</h2>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id='form1'>
	  <fieldset style='border:1px solid grey; background-image: url("../images/background/opacite50.png");'>
<?php
echo add_token_field();
?>
		<legend class="invisible">Activation</legend>
		<p style='text-indent:-3em; margin-left:3em;'><strong>Activation</strong><br />
		<input type='radio'
			   name='activer'
			   id='activer_y'
			   value='y'
			   onchange="change_style_radio();changement();"
			   <?php if (getSettingValue('active_mod_orientation')=='y') echo ' checked="checked"'; ?>/>
		<label for='activer_y' id='label_activer_y'>
		  Activer le module Orientation
		</label>
		<br />
		<input type='radio'
			   name='activer'
			   id='activer_n'
			   value='n'
			   onchange="change_style_radio();changement();"
			   <?php if (getSettingValue('active_mod_orientation')=='n') echo ' checked="checked"'; ?>/>
		<label for='activer_n' id='label_activer_n'>
		  Désactiver le module orientation
		</label>
		</p>

		<br />

		<p style='text-indent:-3em; margin-left:3em;'><strong>Voeux</strong>
		<br />
		<input type='hidden'
			   name='OrientationSaisieVoeuxAdministrateur'
			   value='y'
			   />

		<input type='checkbox'
			   name='OrientationSaisieVoeuxPP'
			   id='OrientationSaisieVoeuxPP'
			   value='y'
			   onchange="checkbox_change(this.id);changement();"
			   <?php if (getSettingAOui('OrientationSaisieVoeuxPP')) echo ' checked="checked"'; ?>/>
		<label for='OrientationSaisieVoeuxPP' id='label_OrientationSaisieVoeuxPP'>
		  Les comptes professeurs principal peuvent saisir les voeux formulés par les élèves dont ils sont professeur principal.
		</label>
		<br />

		<input type='checkbox'
			   name='OrientationSaisieVoeuxScolarite'
			   id='OrientationSaisieVoeuxScolarite'
			   value='y'
			   onchange="checkbox_change(this.id);changement();"
			   <?php if (getSettingAOui('OrientationSaisieVoeuxScolarite')) echo ' checked="checked"'; ?>/>
		<label for='OrientationSaisieVoeuxScolarite' id='label_OrientationSaisieVoeuxScolarite'>
		  Les comptes scolarité peuvent saisir les voeux d'orientation des élèves des classes qu'ils suivent.
		</label>
		<br />

		<input type='checkbox'
			   name='OrientationSaisieVoeuxCpe'
			   id='OrientationSaisieVoeuxCpe'
			   value='y'
			   onchange="checkbox_change(this.id);changement();"
			   <?php if (getSettingAOui('OrientationSaisieVoeuxCpe')) echo ' checked="checked"'; ?>/>
		<label for='OrientationSaisieVoeuxCpe' id='label_OrientationSaisieVoeuxCpe'>
		  Les comptes cpe peuvent saisir les voeux d'orientation des élèves qu'ils suivent.
		</label>
		<br />

		<label for='OrientationNbMaxVoeux'>
		  Nombre maximum de voeux pouvant être saisis pour un élève donné&nbsp;: 
		</label>
		<input type='text'
			   name='OrientationNbMaxVoeux'
			   id='OrientationNbMaxVoeux'
			   onchange="changement();"
			   value='<?php 
			   $OrientationNbMaxVoeux=getSettingValue('OrientationNbMaxVoeux');
			   if((!preg_match("/^[0-9]{1,}$/", $OrientationNbMaxVoeux))||($OrientationNbMaxVoeux==0)) {
			      $OrientationNbMaxVoeux=3;
			   }
			   echo $OrientationNbMaxVoeux;
			   ?>'
			   size='2'
			   onkeydown='clavier_2(this.id,event,1,10);' autocomplete='off' 
			    />
		</p>

		<p style='text-indent:-3em; margin-left:3em;'><strong>Orientation</strong>
		<br />
		<input type='hidden'
			   name='OrientationSaisieOrientationAdministrateur'
			   value='y'
			   />

		<input type='checkbox'
			   name='OrientationSaisieOrientationPP'
			   id='OrientationSaisieOrientationPP'
			   value='y'
			   onchange="checkbox_change(this.id);changement();"
			   <?php if (getSettingAOui('OrientationSaisieOrientationPP')) echo ' checked="checked"'; ?>/>
		<label for='OrientationSaisieOrientationPP' id='label_OrientationSaisieOrientationPP'>
		  Les comptes professeurs principal peuvent saisir l'orientation proposée par le conseil de classe pour les élèves dont ils sont professeur principal.
		</label>
		<br />

		<input type='checkbox'
			   name='OrientationSaisieOrientationScolarite'
			   id='OrientationSaisieOrientationScolarite'
			   value='y'
			   onchange="checkbox_change(this.id);changement();"
			   <?php if (getSettingAOui('OrientationSaisieOrientationScolarite')) echo ' checked="checked"'; ?>/>
		<label for='OrientationSaisieOrientationScolarite' id='label_OrientationSaisieOrientationScolarite'>
		  Les comptes scolarité peuvent saisir l'orientation proposée par le conseil de classe pour les élèves des classes qu'ils suivent.
		</label>
		<br />

		<input type='checkbox'
			   name='OrientationSaisieOrientationCpe'
			   id='OrientationSaisieOrientationCpe'
			   value='y'
			   onchange="checkbox_change(this.id);changement();"
			   <?php if (getSettingAOui('OrientationSaisieOrientationCpe')) echo ' checked="checked"'; ?>/>
		<label for='OrientationSaisieOrientationCpe' id='label_OrientationSaisieOrientationCpe'>
		  Les comptes cpe peuvent saisir l'orientation proposée par le conseil de classe pour les élèves qu'ils suivent.
		</label>
		<br />

		<label for='OrientationNbMaxOrientation'>
		  Nombre maximum d'orientations pouvant être proposées pour un élève donné&nbsp;: 
		</label>
		<input type='text'
			   name='OrientationNbMaxOrientation'
			   id='OrientationNbMaxOrientation'
			   onchange="changement();"
			   value='<?php 
			   $OrientationNbMaxOrientation=getSettingValue('OrientationNbMaxOrientation');
			   if((!preg_match("/^[0-9]{1,}$/", $OrientationNbMaxOrientation))||($OrientationNbMaxOrientation==0)) {
			      $OrientationNbMaxOrientation=3;
			   }
			   echo $OrientationNbMaxOrientation;
			   ?>'
			   size='2'
			   onkeydown='clavier_2(this.id,event,1,10);' autocomplete='off' 
			    />
		</p>

		<p style='text-indent:-3em; margin-left:3em;'><strong>Types d'orientation</strong>
		<br />
		<input type='hidden'
			   name='OrientationSaisieTypeAdministrateur'
			   value='y'
			   />

		<input type='checkbox'
			   name='OrientationSaisieTypePP'
			   id='OrientationSaisieTypePP'
			   value='y'
			   onchange="checkbox_change(this.id);changement();"
			   <?php if (getSettingAOui('OrientationSaisieTypePP')) echo ' checked="checked"'; ?>/>
		<label for='OrientationSaisieTypePP' id='label_OrientationSaisieTypePP'>
		  Les comptes professeurs principal peuvent saisir de nouveaux types d'orientation et modifier les types existants.
		</label>
		<br />

		<input type='checkbox'
			   name='OrientationSaisieTypeScolarite'
			   id='OrientationSaisieTypeScolarite'
			   value='y'
			   onchange="checkbox_change(this.id);changement();"
			   <?php if (getSettingAOui('OrientationSaisieTypeScolarite')) echo ' checked="checked"'; ?>/>
		<label for='OrientationSaisieTypeScolarite' id='label_OrientationSaisieTypeScolarite'>
		  Les comptes scolarité peuvent saisir de nouveaux types d'orientation et modifier les types existants.
		</label>
		<br />

		<input type='checkbox'
			   name='OrientationSaisieTypeCpe'
			   id='OrientationSaisieTypeCpe'
			   value='y'
			   onchange="checkbox_change(this.id);changement();"
			   <?php if (getSettingAOui('OrientationSaisieTypeCpe')) echo ' checked="checked"'; ?>/>
		<label for='OrientationSaisieTypeCpe' id='label_OrientationSaisieTypeCpe'>
		  Les comptes cpe peuvent saisir de nouveaux types d'orientation et modifier les types existants.
		</label>
		</p>

		<p><strong>MEFs concernés</strong></p>
		<p style='text-indent:-3em; margin-left:3em;'>
		Vous pouvez restreindre l'affichage des consultation et saisie et des voeux et orientations proposées à certains MEFs.<br />
		<?php
			$tab_mef_af=array();
			$sql="SELECT m.* FROM o_mef om, mef m WHERE om.mef_code=m.mef_code AND om.affichage='y' ORDER BY libelle_edition, libelle_long, libelle_court;";
			$res_mef=mysqli_query($mysqli, $sql);
			while($lig_mef=mysqli_fetch_object($res_mef)) {
				$tab_mef_af[]=$lig_mef->mef_code;
			}

			$cpt=0;
			$tab_mef=get_tab_mef();
			foreach($tab_mef as $mef_code => $current_mef) {
				$checked="";
				if(in_array($mef_code, $tab_mef_af)) {
					$checked=" checked";
				}
				echo "<input type='checkbox' name='mef_code_af[]' id='mef_code_af_".$cpt."' value='$mef_code'".$checked." onchange=\"checkbox_change(this.id);changement();\" /><label for='mef_code_af_".$cpt."' id='label_mef_code_af_".$cpt."'>".$current_mef['designation_courte']."</label><br />";
				$cpt++;
			}
		?>
		</p>
		<p><a href='#' onclick="cocher_toutes_mefs();return false;">Tout cocher</a> / <a href='#' onclick="decocher_toutes_mefs();return false;">tout décocher</a></p>

		<script type='text/javascript'>
			function cocher_toutes_mefs() {
				for(i=0;i<<?php echo $cpt;?>;i++) {
					if(document.getElementById('mef_code_af_'+i)) {
						document.getElementById('mef_code_af_'+i).checked=true;
						checkbox_change('mef_code_af_'+i);
					}
				}
				changement();
			}

			function decocher_toutes_mefs() {
				for(i=0;i<<?php echo $cpt;?>;i++) {
					if(document.getElementById('mef_code_af_'+i)) {
						document.getElementById('mef_code_af_'+i).checked=false;
						checkbox_change('mef_code_af_'+i);
					}
				}
				changement();
			}
			<?php
				echo js_checkbox_change_style("checkbox_change", 'label_', "n");
				echo js_change_style_radio();
				echo js_change_style_all_checkbox();
			?>
			change_style_radio();
		</script>

		<input type='hidden' name='is_posted' value='y' />

		<p class="center">
			<input type='submit' name='valider' value='Valider' />
		</p>

		<p style='margin-top:1em;'><em>NOTES&nbsp;:</em> Il faudra aussi permettre dans le futur la saisie des voeux par les responsables et élèves.</p>
	  </fieldset>
	</form>

	<!-- ================================================ -->

	<h2>Suppression des voeux et orientations</h2>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id='form2'>
	  <fieldset style='border:1px solid grey; background-image: url("../images/background/opacite50.png");'>
<?php
echo add_token_field();
?>
		<legend class="invisible">Ménage</legend>
		<p>
		<input type='checkbox' name='suppr_voeux' id='suppr_voeux' value="y" />
		<label for='suppr_voeux'>Supprimer les voeux saisis</label><br />

		<input type='checkbox' name='suppr_orientation' id='suppr_orientation' value="y" />
		<label for='suppr_orientation'>Supprimer les orientations saisis</label><br />
		</p>

		<input type='hidden' name='is_posted2' value='y' />

		<p class="center">
			<input type='submit' name='valider' value='Valider' />
		</p>

		<p style='margin-top:1em;'><em>NOTES&nbsp;:</em> Les voeux et orientations saisies sont aussi supprimés lors de l'initialisation.</p>
	  </fieldset>
	</form>

	<!-- ================================================ -->

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

