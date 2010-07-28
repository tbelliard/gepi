<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/**
 * $Id$
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

	<h2 class="colleHaut">Configuration générale</h2>
	<p>
	  <em>
		La désactivation du module trombinoscope n'entraîne aucune suppression des données.
		Lorsque le module est désactivé, il n'y a pas d'accès au module.
	  </em>
	</p>
	<form action="trombinoscopes_admin.php" id="form1" method="post" title="Configuration générale">
	  <fieldset>
		<legend class="bold">Élèves :</legend>
		<input type="radio"
			   name="activer"
			   id='activer_y'
			   value="y"
			  <?php if (getSettingValue("active_module_trombinoscopes")=='y') echo " checked='checked'"; ?>
			   />
		<label for='activer_y' style='cursor:pointer'>
		  Activer le module trombinoscope
		</label>
		<br />
		<input type="radio"
			   name="activer"
			   id='activer_n'
			   value="n"
			  <?php if (getSettingValue("active_module_trombinoscopes")!='y') echo " checked='checked'"; ?>
			   />
		<label for='activer_n'
			   style='cursor:pointer'>
		  Désactiver le module trombinoscope
		</label>
		<input type="hidden" name="is_posted" value="1" />
	  </fieldset>
	  
	  <fieldset>
		<legend class="bold">Personnels :</legend>
		<input type="radio"
			   name="activer_personnels"
			   id='activer_personnels_y'
			   value="y"
			  <?php if (getSettingValue("active_module_trombino_pers")=='y') echo " checked='checked'"; ?>
			   />
		<label for='activer_personnels_y' style='cursor:pointer'>
		  Activer le module trombinoscope des personnels
		</label>
		<br />
		<input type="radio"
			   name="activer_personnels"
			   id='activer_personnels_n'
			   value="n"
			  <?php if (getSettingValue("active_module_trombino_pers")!='y')echo " checked='checked'"; ?>
			   />
		<label for='activer_personnels_n' style='cursor:pointer'>
		  Désactiver le module trombinoscope des personnels
		</label>
	  </fieldset>

	  <p class="center">
		<input type="submit"
			   value="Enregistrer"
			   style="font-variant: small-caps;" />
	  </p>

	  <h2>Configuration d'affichage et de stockage</h2>
	  <p>
		<em>
		  Les valeurs ci-dessous vous servent au paramétrage des valeurs maxi des largeurs et des hauteurs.
		</em>
	  </p>
	  <fieldset>
		<legend class="bold">Pour l'écran</legend>
		largeur maxi 
		<input type="text"
			   name="l_max_aff_trombinoscopes"
			   size="3" 
			   maxlength="3" 
			   value="<?php echo getSettingValue("l_max_aff_trombinoscopes"); ?>"
			   title="largeur maxi"
			   />
		hauteur maxi
		<input type="text"
			   name="h_max_aff_trombinoscopes"
			   size="3" 
			   maxlength="3" 
			   value="<?php echo getSettingValue("h_max_aff_trombinoscopes"); ?>"
			   title="hauteur maxi"
			   />
	  </fieldset>
	  
	  <fieldset>
		<legend class="bold">Pour l'impression</legend>
		largeur maxi
		<input type="text"
			   name="l_max_imp_trombinoscopes"
			   size="3" 
			   maxlength="3"
			   title="largeur maxi"
			   value="<?php echo getSettingValue("l_max_imp_trombinoscopes"); ?>" 
			   />
		hauteur maxi
		<input type="text"
			   name="h_max_imp_trombinoscopes"
			   size="3" 
			   maxlength="3"
			   title="hauteur maxi"
			   value="<?php echo getSettingValue("h_max_imp_trombinoscopes"); ?>" 
			   />
		Nombre de colonnes
		<input type="text"
			   name="nb_col_imp_trombinoscopes"
			   size="3" 
			   maxlength="3"
			   value="<?php echo getSettingValue("nb_col_imp_trombinoscopes"); ?>"
			   title="Nombre de colonnes"
			   />
	  </fieldset>

	  <fieldset>
		<legend class="bold">Pour le stockage sur le serveur</legend>
		largeur
		<input type="text"
			   name="l_resize_trombinoscopes"
			   size="3" 
			   maxlength="3"
			   title="largeur"
			   value="<?php echo getSettingValue("l_resize_trombinoscopes"); ?>" 
			   />
		hauteur
		<input type="text"
			   name="h_resize_trombinoscopes"
			   size="3" 
			   maxlength="3"
			   title="hauteur"
			   value="<?php echo getSettingValue("h_resize_trombinoscopes"); ?>" 
			   />
	  </fieldset>

	  <p class="center">
		<input type="submit"
			   value="Enregistrer"
			   style="font-variant: small-caps;" />
	  </p>
	  
	  <h2>Configuration du redimensionnement des photos</h2>
	  <p>
		<em>
		  La désactivation du redimensionnement des photos n'entraîne aucune suppression des données. 
		  Lorsque le système de redimensionnement est désactivé, les photos transferées sur le site 
		  ne seront pas réduites en 
		  <?php echo getSettingValue("l_resize_trombinoscopes");?>x<?php echo getSettingValue("h_resize_trombinoscopes");?>.
		</em>
	  </p>
	  <fieldset>
		<legend class="invisible">Activation</legend>
		<input type="radio" 
			   name="activer_redimensionne" 
			   id="activer_redimensionne_y" 
			   value="y" 
			  <?php if (getSettingValue("active_module_trombinoscopes_rd")=='y') echo " checked='checked'"; ?> 
			   />
		<label for='activer_redimensionne_y' style='cursor:pointer'>
		  Activer le redimensionnement des photos en 
		  <?php echo getSettingValue("l_resize_trombinoscopes");?>x<?php echo getSettingValue("h_resize_trombinoscopes");?>
		</label>
	  <br />
		<strong>Remarque</strong> attention GD doit être actif sur le serveur de GEPI pour utiliser 
		le redimensionnement.
	  <br />
		<input type="radio" 
			   name="activer_redimensionne" 
			   id="activer_redimensionne_n" 
			   value="n" 
			  <?php if (getSettingValue("active_module_trombinoscopes_rd")=='n') echo " checked='checked'"; ?> 
			   />
		<label for='activer_redimensionne_n' style='cursor:pointer'>
		  Désactiver le redimensionnement des photos
		</label>
	  </fieldset>

	  <fieldset>
		<legend class="bold">Rotation de l'image :</legend>
		<input name="activer_rotation"
			   value=""
			   type="radio"
			   title="Tourner de 0°"
			  <?php if (getSettingValue("active_module_trombinoscopes_rt")=='') echo "checked='checked'"; ?>
			   />
		0°
		<input name="activer_rotation"
			   value="90"
			   type="radio"
			   title="Tourner de 90°"
			  <?php if (getSettingValue("active_module_trombinoscopes_rt")=='90') echo "checked='checked'"; ?>
			   />
		90°
		<input name="activer_rotation"
			   value="180"
			   type="radio"
			   title="Tourner de 180°"
			  <?php if (getSettingValue("active_module_trombinoscopes_rt")=='180') echo "checked='checked'"; ?>
			  />
		180°
		<input name="activer_rotation"
			   value="270"
			   type="radio"
			   title="Tourner de 270°"
			  <?php if (getSettingValue("active_module_trombinoscopes_rt")=='270') echo "checked='checked'"; ?>
			   />
		270°
		Sélectionner une valeur si vous désirez une rotation de la photo originale
	  </fieldset>

	  <p class="center">
		<input type="submit"
			   value="Enregistrer"
			   style="font-variant: small-caps;" />
	  </p>

	  <h2>Gestion de l'accès des élèves</h2>
	  <p>
		Dans la page "Gestion générale"-&gt;"Droits d'accès", vous avez la possibilité de donner à
		<strong>tous les élèves</strong> le droit d'envoyer/modifier lui-même sa photo dans l'interface
		"Gérer mon compte".
	  </p>
	  <p>
		<strong>Si cette option est activée</strong>, vous pouvez, ci-dessous, gérer plus finement quels élèves
		ont le droit d'envoyer/modifier leur photo.
	  </p>
	  <p class="bold">
		Marche à suivre :
	  </p>
	  <ul id="expli_AID" class="colleHaut">
		<li>Créez une "catégorie d'AID" ayant par exemple pour intitulé "trombinoscope".</li>
		<li>
		  Configurez l'affichage de cette catégorie d'AID de sorte que :
		  <ul>
			<li>L'AID n'apparaîsse pas dans le bulletin officiel,</li>
			<li>L'AID n'apparaîsse pas dans le bulletin simplifié.</li>
			<li>Les autres paramètres n'ont pas d'importance.</li>
		  </ul>
		</li>
		<li>Dans la "Liste des aid de la catégorie", ajoutez une ou plusieurs AIDs.</li>
		<li>
		  Ci-dessous, sélectionner dans la liste des catégories d'AIDs, celle portant le nom que vous avez
		  donné ci-dessus. <em>(cette liste n'apparaît pas si vous n'avez pas donné la possibilité à tous
		  les élèves d'envoyer/modifier leur photo dans "Gestion générale"-&gt;"Droits d'accès")</em>.
		</li>
		<li>
		  Tous les élèves inscrits dans une des AIDs de la catégorie sus-nommée pourront alors
		  envoyer/modifier leur photo (<em>à l'exception des élèves sans numéro Sconet ou "elenoet"</em>).
		</li>
	  </ul>

<?php

if (!isset($aid_trouve)) {
?>
	  <p>
		<strong>
		  Vous devez créer une AID pour pouvoir limiter l'accès des élèves au trombinoscope
		</strong>
	  </p>

<?php
} else {
?>
	  <p>
		<strong>
		  Nom de la catégorie d'AID permettant de gérer l'accès des élèves :
		</strong>
		<select name="num_aid_trombinoscopes" size="1" title="Choisir une AID">
		  <option value="">
			(aucune)
		  </option>
<?php
  foreach ($aid_trouve as $aid_disponible){
?>
		  <option value="<?php echo $aid_disponible["indice"] ;?>"<?php if ($aid_disponible["selected"]){ ?> selected="selected"<?php ;} ?> >
			<?php echo $aid_disponible["nom"] ;?>
		  </option>
<?php
  }
  unset ($aid_disponible)
?>
		</select>
	  </p>
	  <p>
		<strong>Remarque :</strong> Si "aucune" AID n'est définie, <strong>tous les élèves</strong> peuvent
		envoyer/modifier leur photo (<em>sauf ceux sans elenoet</em>).
	  </p>
<?php
}
?>
	  <p class="center">
		<input type="hidden" 
			   name="is_posted" 
			   value="1" />
		<input type="submit" 
			   value="Enregistrer"
			   style="font-variant: small-caps;" />
	  </p>

	<hr/>

	<h2>Gestion des fichiers</h2>
<?php if(!file_exists('../photos/'.$repertoire.'eleves/') && !file_exists('../photos/'.$repertoire.'eleves/')) {?>
	  <p>
		Les dossiers photos n'existent pas
	  </p>
<?php } else { ?>
	  <fieldset>
		<legend class="bold">
		  <input type="checkbox"
				 name="supprime"
				 id='supprime'
				 value="yes"/>
		  <label for="supprime">Suppression</label>
		</legend>
		<em>Pour pouvoir supprimer des fichiers photos, vous devez cocher la case ci-dessus</em>
		<br/>
		Un fichier de sauvegarde sera créé, pensez à le récupérer puis le supprimer dans le module de gestion des sauvegardes
		<br/>
<?php if( file_exists('../photos/'.$repertoire.'personnels/') ) { ?>
		<input type="radio"
			   name="sousrub"
			   id='supprime_personnel'
			   value="dp"
			   />
		<label for="supprime_personnel" id='sup_pers'>
		  Vider le dossier photos des personnels
		</label>
<?php } if( file_exists('../photos/'.$repertoire.'eleves/') ) {  ?>
		<br/>
		<input type="radio"
			   name="sousrub"
			   id='supprime_eleve'
			   value="de" />
		<label for="supprime_eleve" id='sup_ele'>
		  Vider le dossier photos des élèves
		</label>
	  </fieldset>

<?php } ?>
	<fieldset>
		<legend class="bold">
		  Gestion
		</legend>
<?php if( file_exists('../photos/'.$repertoire.'personnels/') ) { ?>
		<input type="checkbox"
			   name="voirPerso"
			   id='voir_personnel'
			   value="yes" />
		<label for="voir_personnel">
		  Voir les personnels n'ayant pas de photos
		</label>
  <?php } if( file_exists('../photos/'.$repertoire.'eleves/') ) {?>
		<br/>
		<input type="checkbox"
			   name="voirEleve"
			   id='voir_eleve'
			   value="yes" />
		<label for="voir_eleve">
		  Voir les élèves n'ayant pas de photos
		</label>
  <?php } ?>
	  </fieldset>
	  <p class="center">
		<input type="submit" 
			   value="Enregistrer"
			   style="font-variant: small-caps;" />
	  </p>

	</form>

	<hr/>
	
	<form method="post" action="trombinoscopes_admin.php" id="formEnvoi" enctype="multipart/form-data">
	  <fieldset>
		<legend class="bold">
		  Restaurer les photos à partir d'un fichier Zip
		</legend>
		<input type="hidden" name="action" value="upload" />
		<input type="file" name="nom_du_fichier" title="Nom du fichier à télécharger"/>
		<input type="submit" value="Envoyer"/>
		<br />
		<input type="checkbox"
			   name="ecraser"
			   id='ecrase_photo'
			   value="yes" />
		<label for="ecrase_photo">
		  Ecraser les photos si les noms correspondent
		</label>
		<p>
		  <em>
			Si coché, les photos déjà présentent seront remplacées par les nouvelles.
			Sinon, les anciennes photos seront conservées
		  </em>
		</p>

	  </fieldset>
	</form>

	<hr/>

  <?php }
  if (isset ($eleves_sans_photo)){
  ?>
	<table class="boireaus">
	  <caption>Élèves sans photos</caption>
	  <tr>
		<th>Nom</th>
		<th>Prénom</th>
	  </tr>
  <?php
		$lig="lig1";
	foreach ($eleves_sans_photo as $pas_photo){
	  if ($lig=="lig1"){
		$lig="lig-1";
	  } else{
		$lig="lig1";
	  }
  ?>
	  <tr class="<?php echo $lig ;?>" >
		<td><?php echo $pas_photo->nom ;?></td>
		<td><?php echo $pas_photo->prenom ;?></td>
	  </tr>
  <?php
	}
	unset($pas_photo);
  ?>
	</table>
  <?php
  }

  if (isset ($personnel_sans_photo)){
  ?>
	<table class="boireaus">
	  <caption>Enseignants sans photos</caption>
	  <tr>
		<th>Nom</th>
		<th>Prénom</th>
	  </tr>
  <?php 
		$lig="lig1";
	foreach ($personnel_sans_photo as $pas_photo){
	  if ($lig=="lig1"){
		$lig="lig-1";
	  } else{
		$lig="lig1";
	  }
  ?>
	  <tr class="<?php echo $lig ;?>" >
		<td><?php echo $pas_photo->nom ;?></td>
		<td><?php echo $pas_photo->prenom ;?></td>
	  </tr>
  <?php 
	}
	unset($pas_photo);
  ?>
	</table>
  <?php 
  }
  ?>





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


