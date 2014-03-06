<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id$
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

  <form action="index.php" id="form1" method="post" style='border: 1px solid grey; background-image: url("../images/background/opacite50.png")'>
	
	<p class="center">
<?php
	echo add_token_field();
?>
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="submit" value="Enregistrer" />
	</p>
	
	<h2 class="colleHaut">Configuration générale</h2>
	<p class="italic">
	  La désactivation des carnets de notes n'entraîne aucune suppression des données. 
	  Lorsque le module est désactivé, les professeurs n'ont pas accès au module.
	</p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Activé ou non</legend>
	  <input type="radio" 
			 name="activer" 
			 id='activer_y' 
			 value="y" 
			<?php if (getSettingValue("active_carnets_notes")=='y') echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_y' style='cursor: pointer;'>
		Activer les carnets de notes
	  </label>
	<br />
	  <input type="radio" 
			 name="activer" 
			 id='activer_n' 
			 value="n" 
			<?php if (getSettingValue("active_carnets_notes")=='n') echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_n' style='cursor: pointer;'>
		Désactiver les carnets de notes
	  </label>
	</fieldset>
	
	<p class="grandEspaceHaut">
<?php
	//if(file_exists("../lib/ss_zip.class.php")){ 
?>
	  <input type='checkbox' 
			 name='export_cn_ods'
			 id='export_cn_ods'
			 value='y'
			 onchange='changement();'
<?php
	  if(getSettingValue('export_cn_ods')=='y'){
?>
		checked="checked"
<?php
	  }
?>
	  />
	  <label for='export_cn_ods' style='cursor: pointer;'>
		Permettre l'export des carnets de notes au format ODS.
	  </label>
	  <br />
	  (<em>si les professeurs ne font pas le ménage après génération des exports, ces fichiers peuvent prendre de la place sur le serveur</em>)<br />
<?php
	//}
	//else{
?>
	  <br /><p>La génération de fichiers ODS utilise maintenant la bibliothèque pclzip, présente par défaut dans Gepi.<br /><br />
	  Dans les versions précédentes de Gepi, en mettant en place la bibliothèque 'ss_zip.class.php' dans le dossier '/lib/', vous pouviez générer des fichiers tableur ODS pour permettre des saisies hors ligne, la conservation de données,...
	  <br />
	  Voir <a href='http://smiledsoft.com/demos/phpzip/'>http://smiledsoft.com/demos/phpzip/</a>
	</p>
	<p>
	  Une version limitée est disponible gratuitement.
	  <br />
	  Emplacement alternatif:
	  <a href='http://stephane.boireau.free.fr/informatique/gepi/ss_zip.class.php.zip'>
		http://stephane.boireau.free.fr/informatique/gepi/ss_zip.class.php.zip
	  </a>

<?php
	  // Comme la bibliothèque n'est pas présente, on force la valeur à 'n':
	  //$svg_param=saveSetting("export_cn_ods", 'n');
	//}
?>
	</p>

	<h2>
	  Référentiel des notes :
	</h2>
	<p>
	  Référentiel des notes par défaut : 
	  <input type="text" 
			 name="referentiel_note" 
			 size="8"
			 title="notes sur"
			 value="<?php echo(getSettingValue("referentiel_note")); ?>" />
	</p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Référentiel ou non</legend>
	  <input type="radio" 
			 name="note_autre_que_sur_referentiel" 
			 id="note_sur_referentiel" 
			 value="V" 
			 <?php if(getSettingValue("note_autre_que_sur_referentiel")=="V"){echo "checked='checked'";} ?> />
	  <label for='note_sur_referentiel'> 
		Autoriser les notes autre que sur le référentiel par défaut
	  </label>
	  <br />
	  <input type="radio" 
			 name="note_autre_que_sur_referentiel" 
			 id="note_autre_que_referentiel" 
			 value="F" 
			 <?php if(getSettingValue("note_autre_que_sur_referentiel")=="F"){echo "checked='checked'";} ?> />
	  <label for='note_autre_que_referentiel'> 
		Notes uniquement sur le référentiel par défaut
	  </label>
	</fieldset>

	<h2>
	  Mode de calcul de la moyenne du carnet de notes :
	</h2>
	<p>
	  Mode de calcul de la moyenne du CN dans le cas où des <?php echo getSettingValue('gepi_denom_boite');?>s sont créé<?php if(getSettingValue('gepi_denom_boite_genre')=='f') {echo "e";}?>s : 

<?php
		$cnBoitesModeMoy=getSettingValue('cnBoitesModeMoy');
		echo "<p><br /></p>
<p>Mode de calcul <strong title='Vous pourrez effectuer un autre choix pour certains carnets de notes en suivant le lien Configuration dans votre carnet de notes.'>par défaut</strong> des moyennes de carnets de notes dans le cas où vous créez des ".getSettingValue("gepi_denom_boite")."s&nbsp;:</p>
<div style='margin-left:3em;'>

<input type='radio' name='cnBoitesModeMoy' id='cnBoitesModeMoy_1' value='1' ";
		if($cnBoitesModeMoy=='1') {echo "checked ";}
		echo "/><label for='cnBoitesModeMoy_1'>la moyenne s'effectue sur toutes les notes contenues à la racine et dans les ".my_strtolower(getSettingValue("gepi_denom_boite"))."s sans tenir compte des options définies dans ces ".my_strtolower(getSettingValue("gepi_denom_boite"))."s.</label><br />

<input type='radio' name='cnBoitesModeMoy' id='cnBoitesModeMoy_2' value='2' ";
		if($cnBoitesModeMoy=='2') {echo "checked ";}
		echo "/><label for='cnBoitesModeMoy_2'>la moyenne s'effectue sur toutes les notes contenues à la racine et sur les moyennes des ".my_strtolower(getSettingValue("gepi_denom_boite"))."s en tenant compte des options dans ces ".my_strtolower(getSettingValue("gepi_denom_boite"))."s.</label><br />

<p style='margin-left:2em;'><em>Explication&nbsp;:</em></p>
<div style='margin-left:7em;'>";
		include("../cahier_notes/explication_moyenne_boites.php");
		echo "</div>
</div>

<p><br /></p>\n";

		echo "<p><em>Remarque&nbsp;:</em> Il est possible de ";
		if(getSettingAOui('active_carnets_notes')) {
			echo "<a href='../cahier_notes_admin/creation_conteneurs_par_lots.php'>créer des ".casse_mot(getSettingValue("gepi_denom_boite"), 'min')."s par lots</a></p>";
		}
		else {
			echo "créer des ".casse_mot(getSettingValue("gepi_denom_boite"), 'min')."s par lots (<em>lorsque le module est actif</em>)</p>";
		}
		echo "<p><br /></p>\n";

?>

	<h2>
	  Évaluation par compétence
	</h2>
	<p>
	  Utilisation d'un logiciel externe pour l'évaluation par compétence (beta)
	  <input type="checkbox" 
			 name="utiliser_sacoche" 
			 size="8"
			 title="utiliser_sacoche"
			 <?php if (getSettingValue("utiliser_sacoche") == 'yes') {echo 'checked="checked"';} ?> />
	 <br/>
	 <?php if (getSettingValue("utiliser_sacoche") == 'yes') {
	 	echo '<a href="'.getSettingValue("sacocheUrl").'?id='.getSettingValue('sacoche_base').'">Accéder à l\'administration de l\'Évaluation par compétence</a>';
	 } ?>
	 <br/>
	 <label for='sacocheUrl' style='cursor: pointer;'>Adresse du service d'évaluation par compétence si possible en https (exemple : https://localhost/panier) </label>
	 <input type='text' size='60' name='sacocheUrl' value='<?php echo(getSettingValue("sacocheUrl")); ?>' id='sacocheUrl' /><br/>
	 <label for='sacoche_base' style='cursor: pointer;'>Numéro technique de «base» (laisser vide si votre instalation du logiciel d'évaluation par compétence est mono établissement)</label>
	 <input type='text' size='5' name='sacoche_base' value='<?php echo(getSettingValue("sacoche_base")); ?>' id='sacoche_base' /><br/>
         Empreinte du certificat actuellement intallé dans gepi : <?php
         $file_path = dirname(__FILE__).'/../../../lib/simplesaml/cert/server.crt';
         if (file_exists($file_path)) {
            $cert = file_get_contents($file_path);
            $cert = str_replace('-----BEGIN CERTIFICATE-----', '', $cert);
            $cert = str_replace('-----END CERTIFICATE-----', '', $cert);
            echo sha1(base64_decode($cert));
         } else {
             echo "pas de certificat trouvé";
         }

        ?>
	</p>
	
	<p class="center">
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="submit" value="Enregistrer" />
	</p>

</form>

<p><br /></p>

<p style='margin-left:4em; text-indent:-4em;'><em>NOTE&nbsp;:</em> Les carnets de notes et relevés de notes ne sont pas accessibles aux comptes administrateurs.<br />
En revanche, ils sont accessibles, selon ce qui a été défini dans les <a href='../gestion/droits_acces.php'>Droits d'accès</a> aux profils suivants&nbsp;:<br />
<ul>
	<li>
		<p>
			<strong>Scolarité&nbsp;:</strong> pour l'impression des relevés de notes, des moyennes de carnets de notes, de l'état d'ouverture/verrouillage en saisie de telle période dans telle classe.
		</p>
	</li>
	<li>
		<p>
			<strong>Professeurs&nbsp;:</strong> pour créer des évaluations, saisir les notes,... dans leurs carnets de notes et, selon les droits donnés dans Droits d'accès, consulter les relevés de notes, moyennes de carnets de notes,...
		</p>
	</li>
	<li>
		<p>
			<strong>Élèves et parents&nbsp;:</strong> Selon les droits donnés... consulter leurs notes<br />
	(<em>certaines notes peuvent n'être visibibles qu'à partir d'une date définie par le professeur (par exemple pour éviter de donner aux élèves leurs notes avant que la correction ait eu lieu... pour qu'ils restent attentifs pendant la correction)</em>).
		</p>
	</li>
</ul>



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


