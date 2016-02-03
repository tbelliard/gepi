<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/* $Id: $
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
 */

/**
 * Appelle les sous-modèles
 * templates/origine/header_template.php
 * templates/origine/bandeau_template.php
 *
 * @author regis
 */
interface discipline_admin {
    //put your code here
}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
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

<?php
	echo "
  <p class='bold'><a href='";
	if(isset($_SESSION['chgt_annee'])) {
		echo "../gestion/changement_d_annee.php";
	}
	else {
		echo "../accueil.php";
	}
	echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	if((getSettingAOui('active_mod_discipline'))&&(acces("/mod_discipline/index.php", $_SESSION['statut']))) {
		echo " | <a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accéder au module Discipline</a>";
	}
	if(acces("/mod_discipline/param_pointages.php", $_SESSION['statut'])) {
		echo " | <a href='param_pointages.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Paramétrer le pointage des ".$mod_disc_terme_menus_incidents."</a>";
	}
	"</p>";
?>
  <!-- Fin haut de page -->
  <div class='fieldset_opacite50'>
  <h2>Configuration générale</h2>
  <p>
	<em>
	  La désactivation du module Discipline n'entraîne aucune suppression des données.
	  Lorsque le module est désactivé, les utilisateurs n'ont pas accès au module.
	</em>
  </p>

  <form action="discipline_admin.php" id="form1" method="post">
	<fieldset class="no_bordure">
<?php
echo add_token_field();

$mod_disc_terme_incident=getSettingValue('mod_disc_terme_incident');
if($mod_disc_terme_incident=="") {$mod_disc_terme_incident="incident";}
$mod_disc_terme_sanction=getSettingValue('mod_disc_terme_sanction');
if($mod_disc_terme_sanction=="") {$mod_disc_terme_sanction="sanction";}
$mod_disc_terme_avertissement_fin_periode=getSettingValue('mod_disc_terme_avertissement_fin_periode');
if($mod_disc_terme_avertissement_fin_periode=="") {$mod_disc_terme_avertissement_fin_periode="avertissement de fin de période";}

if(preg_match("/^[AEIOUY]/i", ensure_ascii($mod_disc_terme_avertissement_fin_periode))) {
	$prefixe_mod_disc_terme_avertissement_fin_periode_de="d'";
	$prefixe_mod_disc_terme_avertissement_fin_periode_le="l'";
}
else {
	$prefixe_mod_disc_terme_avertissement_fin_periode_de="de ";
	$prefixe_mod_disc_terme_avertissement_fin_periode_le="le ";
}
?>
	  <legend class="invisible">Activation</legend>
	  <input type="radio"
			 name="activer"
			 id='activer_y'
			 value="y"
			 onchange='changement()'
			<?php if (getSettingValue("active_mod_discipline")=='y') echo " checked='checked'"; ?> />
	  <label for='activer_y' style='cursor: pointer;'>
		Activer le module Discipline
	  </label>
	  <br />
	  <input type="radio"
			 name="activer"
			 id='activer_n'
			 value="n"
			 onchange='changement()'
			<?php if (getSettingValue("active_mod_discipline")=='n') echo " checked='checked'"; ?> />
	  <label for='activer_n' style='cursor: pointer;'>
		Désactiver le module Discipline
	  </label>
	</fieldset>

	<fieldset class="no_bordure">
	  <legend class="invisible">Choix de termes personnalisés</legend>

	  Terme à utiliser à la place du terme '<strong>incident</strong>' dans le module Discipline&nbsp;: 
	  <input type="text"
			 name="mod_disc_terme_incident"
			 id='mod_disc_terme_incident'
			 onchange='changement()'
			 value="<?php echo $mod_disc_terme_incident; ?>" />
	  <br />

	  Terme à utiliser à la place du terme '<strong>sanction</strong>' dans le module Discipline&nbsp;: 
	  <input type="text"
			 name="mod_disc_terme_sanction"
			 id='mod_disc_terme_sanction'
			 onchange='changement()'
			 value="<?php echo $mod_disc_terme_sanction; ?>" />
	  <br />

	  Terme à utiliser à la place du terme '<strong>avertissement de fin de période</strong>' dans le module Discipline&nbsp;: 
	  <input type="text"
			 name="mod_disc_terme_avertissement_fin_periode"
			 id='mod_disc_terme_avertissement_fin_periode'
			 size='30'
			 onchange='changement()'
			 value="<?php echo $mod_disc_terme_avertissement_fin_periode; ?>" />
	  <br />

	</fieldset>

	<fieldset class="no_bordure">
	  <legend class="invisible">Accès aux <?php echo $mod_disc_terme_avertissement_fin_periode."s";?></legend>
	  <input type="radio"
			 name="mod_disc_acces_avertissements"
			 id='mod_disc_acces_avertissements_y'
			 value="y"
			 onchange='changement()'
			<?php if (getSettingValue("mod_disc_acces_avertissements")!='n') echo " checked='checked'"; ?> />
	  <label for='mod_disc_acces_avertissements_y' style='cursor: pointer;'>
		Permettre l'accès à la saisie <?php echo $prefixe_mod_disc_terme_avertissement_fin_periode_de.$mod_disc_terme_avertissement_fin_periode;?>
	  </label>
	  <br />
	  <input type="radio"
			 name="mod_disc_acces_avertissements"
			 id='mod_disc_acces_avertissements_n'
			 value="n"
			 onchange='changement()'
			<?php if (getSettingValue("mod_disc_acces_avertissements")=='n') echo " checked='checked'"; ?> />
	  <label for='mod_disc_acces_avertissements_n' style='cursor: pointer;'>
		Interdire l'accès à la saisie <?php echo $prefixe_mod_disc_terme_avertissement_fin_periode_de.$mod_disc_terme_avertissement_fin_periode;?>
	  </label>

	  <br />&nbsp;<br />
	  <input type='checkbox'
			 name='mod_disc_avertissement_impression_parents_separes'
			 id='mod_disc_avertissement_impression_parents_separes'
			 value='y'
		 onchange='changement();'
		   <?php if (getSettingAOui("mod_disc_avertissement_impression_parents_separes")) echo " checked='checked'";?> />
	  <label for='mod_disc_avertissement_impression_parents_separes' style='cursor: pointer;'>
	  Pour les parents séparés, imprimer deux documents lors des impressions <?php echo $prefixe_mod_disc_terme_avertissement_fin_periode_de.$mod_disc_terme_avertissement_fin_periode;?>.
	  </label>

	  <p style='text-indent:-4em;margin-left:4em; margin-top:1em;'><em>NOTES&nbsp;:</em></p>
	  <ul>
	  	<li><p>Les <?php echo $mod_disc_terme_avertissement_fin_periode."s";?> sont faits pour être saisis indépendamment du bulletin.<br />
	  Ils n'apparaitront pas sur les bulletins, mais vous pourrez les imprimer depuis le module Discipline, ou depuis la page d'impression des bulletins.<br />
	  La saisie se fait depuis le module Discipline, ou depuis les pages permettant la saisie des avis de conseil de classe.<br />
	  Si vous ne souhaitez pas utiliser les <?php echo $mod_disc_terme_avertissement_fin_periode."s";?> vous pouvez interdire l'accès à la saisie ici.</p></li>
	  <li>
	  	<p>Le modèle openDocument (<em title="Fichier OpenOffice.org ou LibreOffice">ODT/ODS</em>) est normalement mis en place pour tous les utilisateurs autorisés à imprimer les <?php echo $mod_disc_terme_avertissement_fin_periode."s";?> via le module <a href='../mod_ooo/gerer_modeles_ooo.php' target='_blank'>Modèles openDocument</a><br />
		  Il est néanmoins possible d'utiliser des modèles différents pour tel ou tel utilisateur (<em>par exemple pour y inclure l'image d'une signature</em>) en déposant un fichier nommé <strong>avertissement_fin_periode.odt</strong> pour l'utilisateur, via le module <a href='../mod_ooo/publipostage_ooo.php' target='_blank'>Publipostage OOo</a></p>
		  <p>Les champs autorisés dans le fichier modèle openDocument sont les suivants&nbsp;:</p>
		  <ul>
			<li>[eleves.nom]</li>
			<li>[eleves.prenom]</li>
			<li>[eleves.sexe]</li>
			<li>[eleves.civilite]</li>
			<li>[eleves.date_nais]</li>
			<li>[eleves.login]</li>
			<li>[eleves.classe]</li>
			<li>[eleves.ine]</li>
			<li>[eleves.doublant]</li>
			<li>[eleves.regime]</li>
			<li>[eleves.date_conseil_de_classe]</li>
			<li>[eleves.suivi_par]</li>
			<li>[eleves.suivi_par_alt]</li>
			<li>[eleves.suivi_par_alt_fonction]</li>
			<li>[eleves.titre_pp]</li>
			<li>[eleves.pp]</li>
			<li>[eleves.annee]</li>
			<li>[eleves.etab]</li>
			<li>[eleves.adr1]</li>
			<li>[eleves.adr2]</li>
			<li>[eleves.cp]</li>
			<li>[eleves.ville]</li>
			<li>[eleves.tel]</li>
			<li>[eleves.fax]</li>
			<li>[eleves.email]</li>
			<li>[eleves.rne]</li>
			<li>[eleves.acad]</li>
			<li>[eleves.designation_resp]</li>
			<li>[eleves.resp_adr1]</li>
			<li>[eleves.resp_adr2]</li>
			<li>[eleves.resp_adr3]</li>
			<li>[eleves.resp_cp]</li>
			<li>[eleves.resp_commune]</li>
			<li>[eleves.resp_pays]</li>
			<li>[eleves.periode]</li>
			<li>[eleves.nom_per]</li>
			<li>[eleves.per.1], [eleves.per.2], [eleves.per.3] contient un X si le numéro de période correspond à la période choisie.<br />
			Utile pour les cases à cocher.</li>
			<li>[eleves.ita.1; if [val]='X'; then X ; else ''] où 1 est l'identifiant du type de <?php echo $mod_disc_terme_avertissement_fin_periode;?></li>
		  </ul>
		  <em>Remarque&nbsp;:</em> [eleves.#] permet de numéroter les élèves extraits.<br />
		  Pour plus de détails, voir <a href='http://www.tinybutstrong.com/manual.php' target='_blank'>http://www.tinybutstrong.com/manual.php</a> et notamment <a href='http://www.tinybutstrong.com/manual.php#php_mergeblock_counting' target='_blank'>http://www.tinybutstrong.com/manual.php#php_mergeblock_counting</a>
	  </li>
	  </ul>
	</fieldset>

	<h2>Autoriser l'utilisation d'une zone commentaire dans la gestion des incidents</h2>
	  <fieldset class="no_bordure">
		<legend class="invisible">Zone de dialogue</legend>
		<p>
		  <input type='radio'
				 name='autorise_commentaires_mod_disc'
				 id='autorise_commentaires_mod_disc_y'
				 value='yes'
			 onchange='changement();'
			   <?php if (getSettingValue("autorise_commentaires_mod_disc") == "yes") echo " checked='checked'";?> />
		<label for='autorise_commentaires_mod_disc_y' style='cursor: pointer;'>
		  Activer une zone de dialogue relative à chaque incident. <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cette zone permet de commenter l'évolution du traitement de l'incident, de formuler une demande au CPE, ... 
		</label>
	  <br />
		  <input type='radio'
				 name='autorise_commentaires_mod_disc'
				 id='autorise_commentaires_mod_disc_n'
				 value='no'
			 onchange='changement();'
			   <?php if (getSettingValue("autorise_commentaires_mod_disc") == "no") echo " checked='checked'";?> />
		<label for='autorise_commentaires_mod_disc_n' style='cursor: pointer;'>
		  Désactiver la zone de dialogue relative à chaque incident.
		</label>
		</p>
	  <br />
	  <p>
		  Dans le cas où la zone de dialogue est activée, les parents et élèves n'ont par défaut pas accès aux échanges.<br />
		  <input type='checkbox'
				 name='commentaires_mod_disc_visible_parent'
				 id='commentaires_mod_disc_visible_parent'
				 value='y'
			 onchange='changement();'
			   <?php if (getSettingAOui("commentaires_mod_disc_visible_parent")) echo " checked='checked'";?> />
		<label for='commentaires_mod_disc_visible_parent' style='cursor: pointer;'>
		  Rendre ces échanges visibles des parents<br />(<em>sous réserve que le droit d'accès aux incidents ait été donné dans <a href='../gestion/droits_acces.php#responsable' target='_blank'>Gestion générale/Droits d'accès</a></em>).
		</label>
	  <br />
		  <input type='checkbox'
				 name='commentaires_mod_disc_visible_eleve'
				 id='commentaires_mod_disc_visible_eleve'
				 value='y'
			 onchange='changement();'
			   <?php if (getSettingAOui("commentaires_mod_disc_visible_eleve")) echo " checked='checked'";?> />
		<label for='commentaires_mod_disc_visible_eleve' style='cursor: pointer;'>
		  Rendre ces échanges visibles des élèves<br />(<em>sous réserve que le droit d'accès aux incidents ait été donné dans <a href='../gestion/droits_acces.php#eleve' target='_blank'>Gestion générale/Droits d'accès</a></em>).
		</label>
	  </p>

	  </fieldset>
	
	<p class="center">
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="submit" value="Enregistrer"/>
	</p>
  </form>
  </div>

<?php
if($nombre_de_dossiers_de_documents_discipline>0) {
?>
  <div class='fieldset_opacite50' style='margin-top:1em;'>
  <form action="discipline_admin.php" id="form1" method="post">
<?php
echo add_token_field();
?>
	<a name='suppr_docs_joints'></a>
	<h2>Suppression des documents joints aux <?php echo $mod_disc_terme_sanction;?>s</h2>
	  <legend class="invisible">Suppression des documents joints aux <?php echo $mod_disc_terme_sanction;?>s</legend>
	  <?php
	  	echo "<p>$nombre_de_dossiers_de_documents_discipline dossier(s) de documents joints à des ".$mod_disc_terme_sanction."s est(sont) présent(s).<br />
	  	Une fois la $mod_disc_terme_sanction effectuée, ces dossiers n'ont plus d'utilité.</p>";
	  ?>

	<p class="center">
	  <input type="hidden" name="suppr_doc_joints" value="y" />
	  <input type="submit" value="Supprimer ces dossiers"/>
	</p>
  </form>
  </div>

	<?php
}
else {
	echo "<p style='margin-top:1em;'>Pour information, aucun dossier de document joint à une $mod_disc_terme_sanction n'existe actuellement.</p>";
}
		/*
		if((getSettingAOui('active_mod_discipline'))&&(acces("/mod_discipline/index.php", $_SESSION['statut']))) {
			echo "<p style='margin-top:1em;'><a href='index.php'>Accéder au module Discipline</a></p>";
		}
		*/
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

