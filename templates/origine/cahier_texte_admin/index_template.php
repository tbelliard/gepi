<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id$
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

<!-- fil d'ariane -->
<?php
  affiche_ariane(TRUE,"Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?")
?>
<!-- fin fil d'ariane -->

	<form action="index.php" id="form1" method="post">
	  <p class="center">
		<input type="submit" value="Enregistrer" />
	  </p>
	<h2>Activation des cahiers de textes</h2>
	  <p class="italic">
		  La désactivation des cahiers de textes n'entraîne aucune suppression des données.
		  Lorsque le module est désactivé, les professeurs n'ont pas accès au module et la consultation
		  publique des cahiers de textes est impossible.
	  </p>
	  <p>
		<label for='activer_y' style='cursor: pointer;'>
		  <input type="radio"
				 name="activer"
				 id="activer_y"
				 value="y"
				<?php if (getSettingValue("active_cahiers_texte")=='y') echo " checked='checked'"; ?> />
		  Activer les cahiers de textes (consultation et édition)
		</label>
	  </p>
	  <p>
		<label for='activer_n' style='cursor: pointer;'>
		  <input type="radio" 
				 name="activer" 
				 id="activer_n" 
				 value="n" 
				<?php if (getSettingValue("active_cahiers_texte")=='n') echo " checked='checked'"; ?> />
		  Désactiver les cahiers de textes (consultation et édition)
		</label>
	  </p>
	  
	  
	  <h2>Version des cahiers de textes</h2>
<?php $extensions = get_loaded_extensions();
  if(!in_array('pdo_mysql',$extensions)) {
?>
	  <p>
		<span style='color:red'>
		  ATTENTION
		</span>
	  Il semble que l'extension php 'pdo_mysql' ne soit pas présente.
	  <br />
	  Cela risque de rendre impossible l'utilisation de la version 2 du cahier de texte";
	  </p>
<?php
  }
  ?>
	  <p class="italic">
		La version 2 du cahier de texte necessite php 5.2.x minimum
	  </p>
	  <p>
		<label for='version_1' style='cursor: pointer;'>
		  <input type="radio"
				 name="version"
				 id="version_1"
				 value="1"
				<?php if (getSettingValue("GepiCahierTexteVersion")=='1') echo " checked='checked'"; ?> />
		  Cahier de texte version 1
		</label>
		(<span class="italic">
		  le cahier de texte version 1 ne sera plus supporté dans la future version 1.5.3
		</span>)
		<br />
		<label for='version_2' style='cursor: pointer;'>
		  <input type="radio"
				 name="version"
				 id="version_2"
				 value="2"
				<?php if (getSettingValue("GepiCahierTexteVersion")=='2') echo " checked='checked'"; ?> />
		  Cahier de texte version 2
		</label>
	  </p>
	  
	  <h2>Début et fin des cahiers de textes</h2>
	  <p class="italic">
		Seules les rubriques dont la date est comprise entre la date de début et la date de fin des cahiers
		de textes sont visibles dans l'interface de consultation publique.
		<br />
		L'édition (modification/suppression/ajout) des cahiers de textes par les utilisateurs de GEPI
		n'est pas affectée par ces dates.
	  </p>
	  <p>
        Date de début des cahiers de textes :
<?php
        $bday = strftime("%d", getSettingValue("begin_bookings"));
        $bmonth = strftime("%m", getSettingValue("begin_bookings"));
        $byear = strftime("%Y", getSettingValue("begin_bookings"));
        genDateSelector("begin_", $bday, $bmonth, $byear,"more_years")
?>
	  </p>
	  <p>
        Date de fin des cahiers de textes :
<?php
        $eday = strftime("%d", getSettingValue("end_bookings"));
        $emonth = strftime("%m", getSettingValue("end_bookings"));
        $eyear= strftime("%Y", getSettingValue("end_bookings"));
        genDateSelector("end_",$eday,$emonth,$eyear,"more_years")
?>
		<input type="hidden" name="is_posted" value="1" />
	  </p>

	  <h2>Accès public</h2>
	  <p>
		<label for='cahier_texte_acces_public_n' style='cursor: pointer;'>
		  <input type='radio' 
				 name='cahier_texte_acces_public' 
				 id='cahier_texte_acces_public_n' 
				 value='no'
				<?php if (getSettingValue("cahier_texte_acces_public") == "no") echo " checked='checked'";?> /> 
		  Désactiver la consultation publique des cahiers de textes 
		  (seuls des utilisateurs logués pourront y avoir accès en consultation, s'ils y sont autorisés)
		</label>
	  </p>
	  <p>
		<label for='cahier_texte_acces_public_y' style='cursor: pointer;'>
		  <input type='radio' 
				 name='cahier_texte_acces_public' 
				 id='cahier_texte_acces_public_y' 
				 value='yes'
				<?php if (getSettingValue("cahier_texte_acces_public") == "yes") echo " checked='checked'";?> /> 
		  Activer la consultation publique des cahiers de textes 
		  (tous les cahiers de textes visibles directement, ou par la saisie d'un login/mdp global)
		</label>
	  </p>
	  <p>
		-> Accès à l'<a href='../public/index.php?id_classe=-1'>interface publique de consultation des cahiers de textes</a>
	  </p>
	  <p class="italic">
		En l'absence de mot de passe et d'identifiant, l'accès à l'interface publique de consultation 
		des cahiers de textes est totalement libre.
	  </p>
	  <p>
		Identifiant :
		<input type="text" 
			   name="cahiers_texte_login_pub" 
			   value="<?php echo getSettingValue("cahiers_texte_login_pub"); ?>" 
			   size="20" />
	  </p>
	  <p>
		Mot de passe :
		<input type="text" 
			   name="cahiers_texte_passwd_pub" 
			   value="<?php echo getSettingValue("cahiers_texte_passwd_pub"); ?>" 
			   size="20" />
	  </p>
	  
	  <h2>Délai de visualisation des devoirs</h2>
	  <p class="italic">
		Indiquez ici le délai en jours pendant lequel les devoirs seront visibles, à compter du jour de
		visualisation sélectionné, dans l'interface publique de consulation des cahiers de textes.
		<br />
		Mettre la valeur 0 si vous ne souhaitez pas activer le module de remplissage des devoirs.
		Dans ce cas, les professeurs font figurer les devoirs à faire dans la même case que le contenu des
		séances.
	  </p>
	  <p>
		Délai :
		<input type="text"
			   name="delai_devoirs"
			   value="<?php echo getSettingValue("delai_devoirs"); ?>"
			   size="2" />
		jours
	  </p>

	  <h2>Visa des cahiers de texte</h2>
	  <p>
		<label for='visa_cdt_inter_modif_notices_visees_y' style='cursor: pointer;'>
		  <input type='radio'
				 name='visa_cdt_inter_modif_notices_visees'
				 id='visa_cdt_inter_modif_notices_visees_y'
				 value='yes'
			   <?php if (getSettingValue("visa_cdt_inter_modif_notices_visees") == "yes") echo " checked='checked'";?> />
		  Activer l'interdiction pour les enseignants de modifier une notice après la signature des
		  cahiers de textes
		</label>
	  </p>
	  <p>
		<label for='visa_cdt_inter_modif_notices_visees_n' style='cursor: pointer;'>
		  <input type='radio'
				 name='visa_cdt_inter_modif_notices_visees'
				 id='visa_cdt_inter_modif_notices_visees_n'
				 value='no'
			   <?php if (getSettingValue("visa_cdt_inter_modif_notices_visees") == "no") echo " checked='checked'";?> />
		  Désactiver l'interdiction pour les enseignants de modifier une notice après la signature
		  des cahiers de textes
		</label>
	  </p>
	  <p class="center">
		<input type="submit" value="Enregistrer" />
	  </p>
	</form>

	<hr />
	
	<h2>Gestion des cahiers de textes</h2>
	<ul>
	  <li><a href='modify_limites.php'>Espace disque maximal, taille maximale d'un fichier</a></li>
	  <li><a href='modify_type_doc.php'>Types de fichiers autorisés en téléchargement</a></li>
	  <li><a href='admin_ct.php'>Administration des cahiers de textes</a> (recherche des incohérences, modifications, suppressions)</li>
	  <li><a href='visa_ct.php'>Viser les cahiers de textes</a> (Signer les cahiers de textes)</li>
	</ul>
	
	<hr />
	
	<h2>Astuce</h2>
	<p>
	  Si vous souhaitez n'utiliser que le module Cahier de textes dans Gepi, consultez la page suivante&nbsp;:
	  <br />
	  <a href='https://www.sylogix.org/wiki/gepi/Use_only_cdt'>
		https://www.sylogix.org/wiki/gepi/Use_only_cdt
	  </a>
	</p>





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


