<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id$
 * *
 * Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
			unset ($value);
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
	
	<h2>Gestion des accès à l'emploi du temps</h2>
	
	<p>
	  (Tous les comptes sauf élève et responsable)
	</p>
	
	<hr />
	
	<form action="edt.php" method="post" id="autorise_edt">
		<fieldset class="no_bordure fieldset_opacite50">
<?php
echo add_token_field();
?>
		  <legend class="invisible">Activation de l'EDT</legend>
		  <em>
			La désactivation des emplois du temps n'entraîne aucune suppression des données. Lorsque le module
			est désactivé, personne n'a accès au module et la consultation des emplois du temps est impossible.
		  </em>
		  <br />
		  
		  <input name="activ_tous"
				 id="activTous"
				 value="y"
				 type="radio"<?php echo eval_checked("autorise_edt_tous", "y"); ?>
				 onclick="document.getElementById('autorise_edt').submit();"
				 />
		  <label for="activTous">
			Activer les emplois du temps pour tous les utilisateurs
		  </label>
		  <br />
		  <input name="activ_tous"
				 id="activPas"
				 value="n"
				 type="radio"<?php echo eval_checked("autorise_edt_tous", "n"); ?>
				 onclick="document.getElementById('autorise_edt').submit();"
				 />
		  <label for="activPas">
			Désactiver les emplois du temps pour tous les utilisateurs
		  </label>
		  <br />
		  <span class="block center">
			<input type="submit" value="Enregistrer" id="btn_active" />
		  </span>
		</fieldset>

	</form>

	<script type="text/javascript">
		//<![CDATA[
	  document.getElementById('btn_active').className = 'invisible';

		//]]>
	</script>

	<form action="edt.php" method="post" id="autorise_prof">
		<fieldset class="no_bordure grandEspaceHaut fieldset_opacite50">
<?php
echo add_token_field();
?>
		  <legend class="invisible">Activation pour les enseignants</legend>
		  <input type="radio"
				 name="autorise_saisir_prof"
				 id="autoProf"
				 value="y"<?php echo eval_checked("edt_remplir_prof", "y"); ?>
				 onclick="document.getElementById('autorise_prof').submit();"
				 />
		  <label for="autoProf">
			Autoriser le professeur à saisir son emploi du temps
		  </label>
		  <br />

		  <input type="radio"
				 name="autorise_saisir_prof"
				 id="autoProfNon"
				 value="n"<?php echo eval_checked("edt_remplir_prof", "n"); ?>
				 onclick="document.getElementById('autorise_prof').submit();"
				 />
		  <label for="autoProfNon">
			Interdire au professeur de saisir son emploi du temps
		  </label>
		  <br />
		  <span class="block center">
			<input type="submit" value="Enregistrer" id="btn_prof" />
		  </span>
		</fieldset>
	</form>

	<script type="text/javascript">
		//<![CDATA[
	  document.getElementById('btn_prof').className = 'invisible';
		//]]>
	</script>
	
	<form action="edt.php" method="post" id="autorise_admin">
		<fieldset class="no_bordure grandEspaceHaut fieldset_opacite50">
<?php
echo add_token_field();
?>
		  <legend class="invisible">Activation pour les administrateurs</legend>
		  <em>Les comptes </em>administrateur<em> ont accès aux emplois du temps si celui-ci est activé pour eux.
		  Si vous avez désactivé; l'accès pour tous, vous pouvez quand même autoriser les comptes
		  </em>administrateur<em> à y avoir accès.</em>
		  <br />
		  <input name="activ_ad"
				 id="activAdY"
				 value="y"
				 type="radio"<?php echo eval_checked("autorise_edt_admin", "y"); ?>
				 onclick="document.getElementById('autorise_admin').submit();"
				 class="grandEspaceHaut"
				 />
		  <label for="activAdY">
			Activer les emplois du temps pour les administrateurs
		  </label>

		  <br />
		  <input name="activ_ad"
				 id="activAdN"
				 value="n"
				 type="radio"<?php echo eval_checked("autorise_edt_admin", "n"); ?>
				 onclick="document.getElementById('autorise_admin').submit();"
				 />
		  <label for="activAdN">
			Désactiver les emplois du temps pour les administrateurs
		  </label>
		  <br />
		  <span class="block center">
			<input type="submit" value="Enregistrer" id="btn_admin" />
		  </span>
		</fieldset>
	</form>

	<script type="text/javascript">
		//<![CDATA[
	  document.getElementById('btn_admin').className = 'invisible';
		//]]>
	</script>
	
	<hr />

	<h2>Gestion de l'accès pour les élèves et leurs responsables</h2>

	<form action="edt.php" method="post" id="autorise_ele">
	  <p>
<?php
echo add_token_field();
?>
			<em>
				Si vous souhaitez rendre accessible leur emploi du temps aux élèves et à leurs responsables,
				il faut impérativement l'autoriser ici.
			</em>
	  </p>

		<fieldset class="no_bordure grandEspaceHaut fieldset_opacite50">
		  <legend class="invisible">Activation pour les élèves et leurs responsables</legend>
		  <input name="activ_ele"
				 id="activEleY"
				 value="yes"
				 type="radio"<?php echo eval_checked("autorise_edt_eleve", "yes"); ?>
				 onclick="document.getElementById('autorise_ele').submit();"
				 />
		  <label for="activEleY">
			Activer les emplois du temps pour les élèves et leurs responsables
		  </label>

		  <br />
		  <input name="activ_ele"
				 id="activEleN"
				 value="no"
				 type="radio"<?php echo eval_checked("autorise_edt_eleve", "no"); ?>
				 onclick="document.getElementById('autorise_ele').submit();"
				 />
		  <label for="activEleN">
			Désactiver les emplois du temps pour les élèves et leurs responsables
		  </label>
		  <br />
		  <span class="block center">
			<input type="submit" value="Enregistrer" id="btn_eleve" />
		  </span>
		</fieldset>
	</form>

	<hr />

	<h2>Autres paramètres</h2>

	<form action="edt.php" method="post" id="edt_autres_parametres">
<?php
echo add_token_field();
?>
	  <!--p>
			<em>
				.
			</em>
	  </p-->

		<fieldset class="no_bordure grandEspaceHaut fieldset_opacite50">
		  <legend class="invisible">Affichage des jours fériés et vacances</legend>

		  <p>Professeurs&nbsp;:<br />
		  <input name="affiche_vacances_prof"
				 id="affiche_vacances_profY"
				 value="yes"
				 type="radio"<?php echo eval_checked("affiche_vacances_prof", "yes"); ?>
				 onclick="document.getElementById('edt_autres_parametres').submit();"
				 />
		  <label for="affiche_vacances_profY">
			Afficher dans l'interface professeur les jours fériés et vacances à venir <em>(sous réserve que ces informations soient saisies dans le Calendrier EDT)</em>
		  </label>

		  <br />
		  <input name="affiche_vacances_prof"
				 id="affiche_vacances_profN"
				 value="no"
				 type="radio"<?php echo eval_checked("affiche_vacances_prof", "no"); ?>
				 onclick="document.getElementById('edt_autres_parametres').submit();"
				 />
		  <label for="affiche_vacances_profN">
			Ne pas afficher dans l'interface professeur les jours fériés et vacances à venir <em>(qu'ils soient saisis ou non)</em>
		  </label>
		  <br />
		  </p>

		  <p>Élèves, responsables&nbsp;:<br />
		  <input name="affiche_vacances_eleresp"
				 id="affiche_vacances_elerespY"
				 value="yes"
				 type="radio"<?php echo eval_checked("affiche_vacances_eleresp", "yes"); ?>
				 onclick="document.getElementById('edt_autres_parametres').submit();"
				 />
		  <label for="affiche_vacances_elerespY">
			Afficher dans l'interface simplifiée/résumé élève/responsable les jours fériés et vacances à venir <em>(sous réserve que ces informations soient saisies dans le Calendrier EDT)</em>
		  </label>

		  <br />
		  <input name="affiche_vacances_eleresp"
				 id="affiche_vacances_elerespN"
				 value="no"
				 type="radio"<?php echo eval_checked("affiche_vacances_eleresp", "no"); ?>
				 onclick="document.getElementById('edt_autres_parametres').submit();"
				 />
		  <label for="affiche_vacances_elerespN">
			Ne pas afficher dans l'interface simplifiée/résumé élève/responsable les jours fériés et vacances à venir <em>(qu'ils soient saisis ou non)</em>
		  </label>
		  </p>


		  <p><input name="edt_version_defaut"
				 id="edt_version_defaut_1"
				 value="1"
				 type="radio"<?php echo eval_checked("edt_version_defaut", "1", ' checked="checked"'); ?>
				 onclick="document.getElementById('edt_autres_parametres').submit();"
				 />
		  <label for="edt_version_defaut_1">
			Afficher par défaut la version 1 du module Emploi du temps.</em>
		  </label>
		  <br />
		  <input name="edt_version_defaut"
				 id="edt_version_defaut_2"
				 value="2"
				 type="radio"<?php echo eval_checked("edt_version_defaut", "2"); ?>
				 onclick="document.getElementById('edt_autres_parametres').submit();"
				 />
		  <label for="edt_version_defaut_2">
			Afficher par défaut la version 2 du module Emploi du temps.</em><br />
			Cette deuxième version ne supporte pas encore toutes les fonctionnalités de l'EDT1, mais elle apporte néanmoins des améliorations.<br />
			<em>(inconvénients&nbsp;: il faut basculer vers la version 1 pour saisir l'emploi du temps, et l'emploi du temps de salle n'est pas encore implémenté)</em>
		  </label>
		  </p>


		  <br />
		  <span class="block center">
			<input type="submit" value="Enregistrer" id="btn_autres_param" />
		  </span>
		</fieldset>
	</form>

	<script type="text/javascript">
		//<![CDATA[
	  document.getElementById('btn_eleve').className = 'invisible';
	  document.getElementById('btn_autres_param').className = 'invisible';
		//]]>
	</script>
	
	
	
	
	
	<?php
		if(((getSettingAOui('autorise_edt_tous'))||(getSettingAOui('autorise_edt_admin')))&&
		(acces("/edt_organisation/index_edt.php", $_SESSION['statut']))) {
			echo "<hr /><p style='margin-top:1em;'><a href='index_edt.php'>Accéder au module EDT</a></p>";
		}
	?>


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

