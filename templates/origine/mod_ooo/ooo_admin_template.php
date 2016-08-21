<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id: $
* Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

  <h2>Configuration générale</h2>
  
  <p>
	<em>
	  La désactivation du module modèle Open Office n'entraîne aucune suppression des données. 
	  Lorsque le module est désactivé, il n'est plus possible de gérer ses propres modèles.
	</em>
  </p>
  <form action="ooo_admin.php" id="form1" method="post">
	<fieldset class="fieldset_opacite50">
<?php
echo add_token_field();
?>
	  <legend class="invisible">Activation</legend>
	  <p class='bold'>Activation du module&nbsp;:</p>
	  <p style='margin-left:3em;'>
	  <input type="radio" 
			 name="activer" 
			 id='activer_y' 
			 value="y" 
			 onchange="changement();change_style_radio();" 
			<?php if (getSettingValue("active_mod_ooo")=='y') echo " checked='checked'"; ?> />
	  <label for='activer_y' id='texte_activer_y'<?php
	  if(getSettingAOui('active_mod_ooo')) {echo " style='font-weight:bold'";}
	  ?>>
		Activer le module modèle Open Office
	  </label>
	  <br />
	  <input type="radio" 
			 name="activer" 
			 id='activer_n' 
			 value="n" 
			 onchange="changement();change_style_radio();" 
			<?php if (getSettingValue("active_mod_ooo")=='n') echo " checked='checked'"; ?> />
	  <label for='activer_n' id='texte_activer_n'<?php
	  if(!getSettingAOui('active_mod_ooo')) {echo " style='font-weight:bold'";}
	  ?>>
		Désactiver le module modèle Open Office
	  </label>
	  </p>

<?php

echo "<p style='margin-top:1em;'><span class='bold'>Décompresseur d'archive&nbsp;:</span> <br /><em>Gepi a besoin d'un décompresseur d'archive pour créer les documents OOo.</em></p>\n";

echo "<p style='margin-left:3em;'>";
$fb_dezip_ooo=getSettingValue("fb_dezip_ooo");
echo "<input type='radio' name='fb_dezip_ooo' id='fb_dezip_ooo_0' value='0' onchange='changement();change_style_radio();' ";
if($fb_dezip_ooo=="0"){
	echo "checked='checked' />";
	$style_tmp=" style='font-weight:bold;'";
}
else{
	echo "/>";
	$style_tmp="";
}
echo "<label for='fb_dezip_ooo_0' id='texte_fb_dezip_ooo_0'".$style_tmp."> ZIPARCHIVE et TinyDoc : le choix par défaut mais peut créer des fichiers corrompus si votre version de PHP est inférieur à 5.2.8 (<em>utiliser OOo 3.2 pour réparer les fichiers</em>) </label><br />\n";

echo "<input type='radio' name='fb_dezip_ooo' id='fb_dezip_ooo_1' value='1' onchange='changement();change_style_radio();' ";
if($fb_dezip_ooo=="1"){
	echo "checked='checked' />";
	$style_tmp=" style='font-weight:bold;'";
}
else{
	echo "/>";
	$style_tmp="";
}
echo "<label for='fb_dezip_ooo_1' id='texte_fb_dezip_ooo_1'".$style_tmp."> ZIP-UNZIP et TinyDoc : nécessite que ZIP et UNZIP soient installés sur le serveur et que leurs chemins soient définis dans la variable d'environnement PATH </label><br />\n";

echo "<input type='radio' name='fb_dezip_ooo' id='fb_dezip_ooo_2' value='2' onchange='changement();change_style_radio();' ";
if($fb_dezip_ooo=="2"){
	echo "checked='checked' />";
	$style_tmp=" style='font-weight:bold;'";
}
else{
	echo "/>";
	$style_tmp="";
}
echo "<label for='fb_dezip_ooo_2' id='texte_fb_dezip_ooo_2'".$style_tmp."> PCLZIP et TBSooo : classe plus ancienne, toutes les fonctionnalités de TinyDoc ne sont pas disponible dans les gabarits mais fonctionne avec PHP 5.2 </label><br />\n";

echo "</p>";
?>

	
	<p class="center">
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="submit" value="Enregistrer"/>
	</p>
	</fieldset>
</form>

<?php
  if (count($droitRepertoire)){
	foreach ($droitRepertoire as $droit){
	  echo "<p class='grandEspaceHaut rouge bold'>".$droit."</p>";
	}
	unset($droit);
  }

?>

<form action="ooo_admin.php" id="form_publipostage" method="post" style="margin-top:1em;">
	<fieldset class="fieldset_opacite50">
<?php
echo add_token_field();
?>
	<p class='bold'>Publipostage OOo&nbsp;:</p>

	<p>Droit à l'upload de modèles openDocument pour tel ou tel statut&nbsp;:</p>
	<p style='margin-left:3em;'>
	<input type="checkbox" name="OOoUploadProf" id="OOoUploadProf" value="yes" onchange="changement(); checkbox_change(this.id);" 
	<?php
		if(getSettingAOui("OOoUploadProf")) {
			echo "checked ";
		}
	?>/><label for="OOoUploadProf" id="texte_OOoUploadProf"
	<?php
		if(getSettingAOui("OOoUploadProf")) {
			echo " style='font-weight:bold;'";
		}
	?>>Autoriser les professeurs à uploader leurs propres fichiers modèles openDocument.</label><br />

	<input type="checkbox" name="OOoUploadCpe" id="OOoUploadCpe" value="yes" onchange="changement(); checkbox_change(this.id);" 
	<?php
		if(getSettingAOui("OOoUploadCpe")) {
			echo "checked ";
		}
	?>/><label for="OOoUploadCpe" id="texte_OOoUploadCpe"
	<?php
		if(getSettingAOui("OOoUploadCpe")) {
			echo " style='font-weight:bold;'";
		}
	?>>Autoriser les cpe à uploader leurs propres fichiers modèles openDocument.</label><br />

	<input type="checkbox" name="OOoUploadScol" id="OOoUploadScol" value="yes" onchange="changement(); checkbox_change(this.id);" 
	<?php
		if(getSettingAOui("OOoUploadScol")) {
			echo "checked ";
		}
	?>/><label for="OOoUploadScol" id="texte_OOoUploadScol"
	<?php
		if(getSettingAOui("OOoUploadScol")) {
			echo " style='font-weight:bold;'";
		}
	?>>Autoriser les comptes scolarité à uploader leurs propres fichiers modèles openDocument.</label><br />
	</p>

	<p style="margin-top:1em;">Si vous ne souhaitez pas donner un droit global pour tel ou tel statut, vous pouvez autoriser certains utilisateurs en particulier à uploader leurs propres modèles openDocument&nbsp;:</p>
	<div style='margin-left:3em;'>
	<?php
		$tab_autorise=array();
		$sql="SELECT login FROM preferences WHERE name='AccesOOoUpload' AND value LIKE 'y%';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				$tab_autorise[]=$lig->login;
			}
		}
		echo liste_checkbox_utilisateurs(array('administrateur', 'scolarite', 'cpe', 'professeur'), $tab_autorise);
	?>
	</div>

	<p class="center">
	  <input type="hidden" name="is_posted" value="2" />
	  <input type="submit" value="Enregistrer"/>
	</p>
</fieldset>
</form>

<?php
echo js_change_style_radio("change_style_radio", "y", "y", 'checkbox_change', 'texte_');
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

