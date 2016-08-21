<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id: $
* Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
		<p>
		<input type='radio'
			   name='activer'
			   id='activer_y'
			   value='y'
			   <?php if (getSettingValue('active_mod_alerte')=='y') echo ' checked="checked"'; ?>/>
		<label for='activer_y'>
		  Activer le dispositif d'alerte
		</label>
		<br />
		<input type='radio'
			   name='activer'
			   id='activer_n'
			   value='n'
			   <?php if (getSettingValue('active_mod_alerte')=='n') echo ' checked="checked"'; ?>/>
		<label for='activer_n'>
		  Désactiver le dispositif d'alerte
		</label>
		</p>

		<br />

		<p>
		Tester la présence d'alertes toutes les 
		<input type='text'
			   name='MessagerieDelaisTest'
			   id='MessagerieDelaisTest'
			   size='3'
			   value='<?php
			   $nb_min=getSettingValue('MessagerieDelaisTest');
			   if(($nb_min=='')||(!preg_match('/^[0-9]*$/', $nb_min))||($nb_min==0)) {
			      $nb_min=1;
			   }
			   	echo $nb_min;
			   	?>'
			   	onkeydown="clavier_2(this.id,event,1,300);"
			   	/> minute(s).
		</p>

		<p>
		Largeur en pixels de l'image signalant à un utilisateur qu'il a des alertes non lues
		<input type='text'
			   name='MessagerieLargeurImg'
			   id='MessagerieLargeurImg'
			   size='3'
			   value='<?php
			   $MessagerieLargeurImg=getSettingValue('MessagerieLargeurImg');
			   if(($MessagerieLargeurImg=='')||(!preg_match('/^[0-9]*$/', $MessagerieLargeurImg))||($MessagerieLargeurImg<0)) {
			      $MessagerieLargeurImg=16;
			   }
			   	echo $MessagerieLargeurImg;
			   	?>'
			   	onkeydown="clavier_2(this.id,event,16,500);"
			   	/> px
		<br />
		(<em>il peut être utile de faire apparaître une image de bonne taille pour qu'elle soit vue,<br />si, par exemple, votre CPE souhaite que des élèves lui soient renvoyés au bureau au plus vite</em>)
		</p>

		<br />

		<p>
		<input type='radio'
			   name='MessagerieAvecSon'
			   id='MessagerieAvecSon_y'
			   value='y'
			   <?php if (getSettingAOui('MessagerieAvecSon')) echo ' checked="checked"'; ?>/>
		<label for='MessagerieAvecSon_y'>
		  Emettre un son lorsqu'il y a des alertes non lues
		</label>
		<br />
		<input type='radio'
			   name='MessagerieAvecSon'
			   id='MessagerieAvecSon_n'
			   value='n'
			   <?php if (!getSettingAOui('MessagerieAvecSon')) echo ' checked="checked"'; ?>/>
		<label for='MessagerieAvecSon_n'>
		  Ne pas emettre de son lorsqu'il y a des alertes non lues
		</label>
		</p>

		<br />

		<p>
		<input type='hidden'
			   name='PeutPosterMessageAdministrateur'
			   value='y'
			   />
		<input type='checkbox'
			   name='PeutPosterMessageProfesseur'
			   id='PeutPosterMessageProfesseur'
			   value='y'
			   <?php if (getSettingAOui('PeutPosterMessageProfesseur')) echo ' checked="checked"'; ?>/>
		<label for='PeutPosterMessageProfesseur'>
		  Les comptes professeurs peuvent poster des alertes
		</label>
		<br />

		<input type='checkbox'
			   name='PeutPosterMessageScolarite'
			   id='PeutPosterMessageScolarite'
			   value='y'
			   <?php if (getSettingAOui('PeutPosterMessageScolarite')) echo ' checked="checked"'; ?>/>
		<label for='PeutPosterMessageScolarite'>
		  Les comptes scolarité peuvent poster des alertes
		</label>
		<br />

		<input type='checkbox'
			   name='PeutPosterMessageCpe'
			   id='PeutPosterMessageCpe'
			   value='y'
			   <?php if (getSettingAOui('PeutPosterMessageCpe')) echo ' checked="checked"'; ?>/>
		<label for='PeutPosterMessageCpe'>
		  Les comptes cpe peuvent poster des alertes
		</label>
		<br />

		<input type='checkbox'
			   name='PeutPosterMessageAutre'
			   id='PeutPosterMessageAutre'
			   value='y'
			   <?php if (getSettingAOui('PeutPosterMessageAutre')) echo ' checked="checked"'; ?>/>
		<label for='PeutPosterMessageAutre'>
		  Les comptes de statut personnalisé (<em>autre</em>) peuvent poster des alertes<br />
		  <span style='color:red'>Pour le moment ce sont tous les statuts personnalisés indifférement (<em>il faudra gérer plus finement dans la page des statuts personnalisés plus tard</em>).</span>
		</label>
		<br />

		<input type='hidden' name='is_posted' value='y' />

		<p class="center">
			<input type='submit' name='valider' value='Valider' />
		</p>
	  </fieldset>
	</form>

	<!-- ================================================ -->

	<h2>Suppression des alertes</h2>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id='form2'>
	  <fieldset style='border:1px solid grey; background-image: url("../images/background/opacite50.png");'>
<?php
echo add_token_field();
?>
		<legend class="invisible">Ménage</legend>
		<p>
		Supprimer les alertes antérieures au 
		<input type='text' name='date_limite' id='date_limite' size='10' value = "<?php echo $date_limite;?>" onKeyDown="clavier_date(this.id,event);" AutoComplete="off" title="Vous pouvez modifier la date à l'aide des flèches Up et Down du pavé de direction." />

		<?php
			echo img_calendrier_js("date_limite", "img_bouton_date_limite");
		?>

		</p>

		<input type='hidden' name='is_posted2' value='y' />

		<p class="center">
			<input type='submit' name='valider' value='Valider' />
		</p>

	  </fieldset>
	</form>

	<!-- ================================================ -->

	<h2>Comptes exclus du dispositif alertes</h2>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id='form2'>
	  <fieldset style='border:1px solid grey; background-image: url("../images/background/opacite50.png");'>
	<?php
		echo add_token_field();
	?>
		<legend class="invisible">Comptes_exclus</legend>

	<?php
		$tab_statuts=array('administrateur', 'cpe', 'scolarite', 'professeur', 'autre');
		$tab_user_preselectionnes=array();

		$sql="SELECT value FROM mod_alerte_divers WHERE name='login_exclus';";
		$res_mae=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_mae)>0) {
			while($lig_mae=mysqli_fetch_object($res_mae)) {
				$tab_user_preselectionnes[]=$lig_mae->value;
			}
		}

		echo liste_checkbox_utilisateurs($tab_statuts, $tab_user_preselectionnes);
	?>

		<input type='hidden' name='is_posted3' value='y' />

		<p class="center">
			<input type='submit' name='valider' value='Valider' />
		</p>

	  </fieldset>
	</form>
	<!-- ================================================ -->

	<h2>Matières exclues des sélections "Équipe de telle classe"</h2>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id='form2'>
	  <fieldset style='border:1px solid grey; background-image: url("../images/background/opacite50.png");'>
	<?php
		echo add_token_field();
	?>
		<legend class="invisible">Matieres_exclus</legend>

	<?php
		$nb_col=3;
		$colonne=1;
		echo "<div style='float:left; width:30%'>";
		for($loop=0;$loop<count($tab_mat);$loop++) {
			if($loop>=$colonne*ceil(count($tab_mat)/$nb_col)) {
				echo "</div>";
				echo "<div style='float:left; width:30%'>";
				$colonne++;
			}

			if(in_array($tab_mat[$loop]['matiere'], $tab_mat_exclue)) {
				$checked="checked ";
				$style=" style='font-weight:bold;'";
			}
			else {
				$checked="";
				$style="";
			}
			echo "
		<input type='checkbox' id='mat_exclue_$loop' name='mat_exclue[]' value='".$tab_mat[$loop]['matiere']."' onchange=\"checkbox_change(this.id); changement();\" $checked/><label for='mat_exclue_$loop' id='texte_mat_exclue_$loop'$style> ".$tab_mat[$loop]['matiere']." <span style='font-size:small'>(<em>".$tab_mat[$loop]['nom_complet']."</em>)</span></label><br />";
		}
		echo "</div>";
		echo js_checkbox_change_style('checkbox_change', 'texte_', "y");
	?>

		<input type='hidden' name='is_posted_matieres_exclues' value='y' />

		<p class="center">
			<input type='submit' name='valider' value='Valider' />
		</p>

		<div style='clear:both;'>&nbsp;</div>

		<p class="center">
			<input type='submit' name='valider' value='Valider' />
		</p>

		<p style='text-indent:-4em;margin-left:4em;'><em>NOTE&nbsp;:</em> Certaines matières, correspondant par exemple aux groupes d'aide ne sont pas nécessairement destinés à recevoir certaines alertes.<br />
		Vous pourrez les exclure des équipes pédagogiques "réduites".<br />
		La sélection de l'équipe complète reste possible dans le module Alertes.</p>

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

