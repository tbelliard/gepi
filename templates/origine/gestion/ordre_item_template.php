<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id: ordre_item_template.php 6744 2011-04-03 22:29:54Z regis $
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

  <script type="text/javascript" src="../templates/origine/lib/fonction_change_ordre_menu.js"></script>

	<link rel="stylesheet" type="text/css" href="../templates/origine/css/accueil.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../templates/origine/css/bandeau.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../templates/origine/css/param_ordre_item.css" media="screen" />

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
			unset($value);
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

	<form method="post" action="#">
	<p class="center">
	  Ce module permet de modifier l'ordre des menus de la page accueil ainsi que les intitulés.
  	  <br /><span class="small">Remarque : si le nouveau nom du bloc est "bloc_invisible", ce dernier n'apparaitra pas dans le menu (ceci ne fonctionne pas pour les plugins).</span>
	</p>
	<p>
	  <input type="submit" value="Enregistrer" name="btn_enregistrer" />
	  <span class="ecarte">  </span>
	  <input type="submit" value="Réinitialiser" name="btn_reinitialiser" title="Supprime toutes les entrées relatives à l'ordre des menus" />
	  <input type="submit" value="Optimiser" name="btn_optimiser" title="Élimine les trous dans la numérotation des titres de menus" />
	  <br /><br />
	</p>

<!-- début corps menu	-->

<?php
  if (count($menuAffiche)) {
?>
        <div class="systeme_onglets">
	<div class="onglets">
<?php
	foreach ($menuAffiche as $menuAfficheAdministrateur){
?>
	  <a class="onglet_0 onglet" id='onglet_<?php echo $menuAfficheAdministrateur->statutUtilisateur ;?>' href="#<?php echo $menuAfficheAdministrateur->statutUtilisateur ;?>" title="section <?php echo $menuAfficheAdministrateur->statutUtilisateur ;?>" onclick="javascript:change_onglet('<?php echo $menuAfficheAdministrateur->statutUtilisateur; ?>');return false;">
		<?php echo $menuAfficheAdministrateur->statutUtilisateur ;?>
	  </a>
<?php
	}
?>
	</div>
        <div class="contenu_onglets">
<?php
	foreach ($menuAffiche as $menuAfficheAdministrateur){
?>
            <div class="contenu_onglet2" id="contenu_onglet_<?php echo $menuAfficheAdministrateur->statutUtilisateur ;?>">

<script type="text/javascript">
//<!--
			document.getElementById('contenu_onglet_<?php echo $menuAfficheAdministrateur->statutUtilisateur ;?>').className = 'contenu_onglet';
//-->
</script>

	<h2 class="center bold">
	  <a name="<?php echo $menuAfficheAdministrateur->statutUtilisateur ;?>" href="#container" title="retour début de page depuis <?php echo $menuAfficheAdministrateur->statutUtilisateur ;?>" >
			<?php echo $menuAfficheAdministrateur->statutUtilisateur ;?>
	  </a>
	</h2>
<?php
	  if (count($menuAfficheAdministrateur->titre_Menu)) {
		foreach ($menuAfficheAdministrateur->titre_Menu as $newEntreeMenu) {
?>
	  <h3 class="<?php echo $newEntreeMenu->classe ?>">
		<input type="hidden" value="menu"
			   id="type_<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>_<?php echo $newEntreeMenu->indexMenu ?>"
			   name="type_<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>_<?php echo $newEntreeMenu->indexMenu ?>" />

		<input type="hidden" value="<?php echo $newEntreeMenu->bloc ?>"
			   id="bloc_<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>_<?php echo $newEntreeMenu->indexMenu ?>"
			   name="bloc_<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>_<?php echo $newEntreeMenu->indexMenu ?>" />

		<input type="hidden" value="<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>"
			   id="statut_<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>_<?php echo $newEntreeMenu->indexMenu ?>"
			   name="statut_<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>_<?php echo $newEntreeMenu->indexMenu ?>" />
		<input type="hidden" value="<?php echo $newEntreeMenu->indexMenu ?>"
			   id="indexMenu_<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>_<?php echo $newEntreeMenu->indexMenu ?>"
			   name="indexMenu_<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>_<?php echo $newEntreeMenu->indexMenu ?>" />
		<label for="nouveau_<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>_<?php echo $newEntreeMenu->indexMenu ?>">menu : </label>
		<input type="text" value="<?php echo $newEntreeMenu->indexMenu ?>"
			   id="nouveau_<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>_<?php echo $newEntreeMenu->indexMenu ?>"
			   name="nouveau_<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>_<?php echo $newEntreeMenu->indexMenu ?>"
			   onchange='changement();'
			   size="3" />
		<img src="<?php echo $newEntreeMenu->icone['chemin'] ?>" alt="<?php echo $newEntreeMenu->icone['alt'] ?>" />
		-
		<?php echo $newEntreeMenu->texte ?>
		<br />
		<label for="nouveauNom_<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>_<?php echo $newEntreeMenu->indexMenu ?>">nouveau nom : </label>
		<input type="text" value="<?php echo $newEntreeMenu->nouveauNom ?>"
			   id="nouveauNom_<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>_<?php echo $newEntreeMenu->indexMenu ?>"
			   name="nouveauNom_<?php echo $menuAfficheAdministrateur->statutUtilisateur ?>_<?php echo $newEntreeMenu->indexMenu ?>"
			   onchange='changement();' />

	  </h3>
<?php
		  if (count($menuAfficheAdministrateur->menu_item)) {
			foreach ($menuAfficheAdministrateur->menu_item as $newentree) {
			  if ($newentree->indexMenu==$newEntreeMenu->indexMenu) {

?>
		  <div class='div_tableau'>
			<h4 class="colonne ie_gauche">
				menu : <?php echo $newentree->indexMenu ?> - item : <?php echo $newentree->indexItem ?>
				<p class='bold'>
				<!-- <a href="" title="menu <?php echo $menuAfficheAdministrateur->statutUtilisateur ?> <?php echo $newentree->titre ?>"> -->
					<?php echo $newentree->titre ?>
				<!-- </a> -->
				</p>
			</h4>
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
	</div>
<?php
	}
?>
	</div>
    </div>
	</form>
<script type="text/javascript">
        //<!--
		//document.getElementByClassNames('contenu_onglet2').ClassNames = 'contenu_onglet';
                var anc_onglet = 'administrateur';
                change_onglet(anc_onglet);
        //-->
        </script>
<?php
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


