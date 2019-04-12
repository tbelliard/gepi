<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id$
 *
 * Copyright 2001, 2019 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

	<h2>
	  Statuts "Autre"
	</h2>
	<p>
	  Si Gepi définit plusieurs statuts par défaut, il est possible d'en créer de nouveaux en passant 
	  par cet outil.
	</p>

	<form id="auth_statuts_perso" action="creer_statut_admin.php" method="post">
	  <p>
<?php
echo add_token_field();
?>
		<input type="hidden" name="action" value="valide" />
		<input type="checkbox" 
			   id="idAutorise" 
			   name="autorise" 
			   value="y" 
			  <?php if (getSettingValue('statuts_prives') == 'y') {echo " checked='checked'";} ?>
			   onchange='document.getElementById("auth_statuts_perso").submit();' />
		<label for="idAutorise">
		  Autoriser la création de nouveaux statuts personnalisés par l'admnistrateur.
		</label>
		<br/>
	  </p>
	  <p class="center">
		<input type="submit" value="Enregistrer" id="btn_submit" />
	<script type="text/javascript">
		//<![CDATA[
		document.getElementById("btn_submit").addClassName("invisible");
		//]]>
	</script>
	  </p>
	</form>

	<p style='margin-top:1em'><em>NOTES&nbsp;:</em></p>
	<ul>
		<li>Seul un administrateur peut créer des statuts personnalisés.</li>
		<li>
			<strong>Créer des statuts personnalisés</strong><br />
			[<a href='../accueil_admin.php'>gestion des bases</a>] -&gt; [<a href='index.php'>Gestion des comptes d'accès des utilisateurs</a>] -&gt; [<a href='index.php?mode=personnels'>Personnel de l'établissement</a>] -&gt; en haut, il y a un lien [<a href='creer_statut.php'>Statuts personnalisés</a>] : cliquons.<br />
			En bas à gauche, [Ajouter un statut personnalisé] et lui donner un nom.<br />
			Quand on clique sur [Ajouter], le tableau gagne une colonne avec des coches vides.<br />
			Il suffit de choisir les droits à donner à ce statut et de [Enregistrer et mettre à jour].
		</li>
		<li>
			<strong>Donner un statut personnalisé à un utilisateur</strong><br />
			Quand on crée un nouvel utilisateur <em>(personnel de l'établissement)</em>, on lui donne un statut <em>(Professeur, Administrateur, C.P.E., Scolarité, Secours, Autre)</em>.<br />
			Pour pouvoir lui donner le statut personnalisé, il faut qu'il soit "Autre".<br />
			Sur la page des statuts personnalisés, les utilisateurs "Autre" sont listés à droite en attente d'un statut personnalisé <em>(sans lequel ils ne peuvent pas se connecter)</em>.<br />
			Il faut alors leur donner le statut choisi, c'est terminé.
		</li>
		<li>
			<strong>Mettre à jour un statut personnalisé</strong><br />
			Pour mettre à jour un statut personnalisé <em>(ajout d'un droit par exemple)</em> il faut dans un premier temps détruire le statut et le recréer avec les droits voulus.<br />
			Il ne faut pas oublier de réaffecter le statut à chaque utilisateur.
		</li>
	</ul>

	
	
	</p>

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


