<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id$
 *
 * Copyright 2001, 2014 Thomas Belliard, Eric Lebrun, Regis Bouguin, Stephane Boireau
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

  <form action="index_admin.php" id="form1" method="post" style='border: 1px solid grey; background-image: url("../images/background/opacite50.png")'>
<?php
	echo add_token_field();
?>
	
	<h2 class="colleHaut">Configuration générale</h2>
	<p class="italic">
	  La désactivation du module Absences et remplacements de professeurs n'entraîne aucune suppression des données. 
	  Lorsque le module est désactivé, les utilisateurs n'ont pas accès au module.
	</p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Activé ou non</legend>
	  <input type="radio" 
			 name="activer" 
			 id='activer_y' 
			 value="y" 
			<?php if (getSettingAOui("active_mod_abs_prof")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_y' style='cursor: pointer;'>
		Activer le module absences et remplacements de professeurs
	  </label>
	<br />
	  <input type="radio" 
			 name="activer" 
			 id='activer_n' 
			 value="n" 
			<?php if (!getSettingAOui("active_mod_abs_prof")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_n' style='cursor: pointer;'>
		Désactiver le module absences et remplacements de professeurs
	  </label>
	</fieldset>

	<p class="center">
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="submit" value="Enregistrer" />
	</p>

</form>

<br />

  <form action="index_admin.php" id="form2" method="post" style='border: 1px solid grey; background-image: url("../images/background/opacite50.png")'>
<?php
	echo add_token_field();
?>
	<input type="hidden" name="is_posted" value="2" />

	<h2 class="colleHaut">Propositions et validations</h2>

	<p>
	  <input type="checkbox" 
			 name="AbsProfSaisieAbsScol" 
			 id='AbsProfSaisieAbsScol' 
			 value="yes" 
			<?php if (getSettingAOui("AbsProfSaisieAbsScol")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='AbsProfSaisieAbsScol' style='cursor: pointer;'>
		Les comptes scolarité peuvent saisir les absences de professeurs.
	  </label>
	  <br />
	  <input type="checkbox" 
			 name="AbsProfProposerRemplacementScol" 
			 id='AbsProfProposerRemplacementScol' 
			 value="yes" 
			<?php if (getSettingAOui("AbsProfProposerRemplacementScol")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='AbsProfProposerRemplacementScol' style='cursor: pointer;'>
		Les comptes scolarité peuvent proposer des remplacements aux professeurs.
	  </label>
	  <br />
	  <input type="checkbox" 
			 name="AbsProfAttribuerRemplacementScol" 
			 id='AbsProfAttribuerRemplacementScol' 
			 value="yes" 
			<?php if (getSettingAOui("AbsProfAttribuerRemplacementScol")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='AbsProfAttribuerRemplacementScol' style='cursor: pointer;'>
		Les comptes scolarité peuvent choisir parmi les réponses favorables de professeurs lequel effectuera le remplacement.
	  </label>
	</p>

	<p>
	  <input type="checkbox" 
			 name="AbsProfSaisieAbsCpe" 
			 id='AbsProfSaisieAbsCpe' 
			 value="yes" 
			<?php if (getSettingAOui("AbsProfSaisieAbsCpe")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='AbsProfSaisieAbsCpe' style='cursor: pointer;'>
		Les comptes Cpe peuvent saisir les absences de professeurs.
	  </label>
	  <br />
	  <input type="checkbox" 
			 name="AbsProfProposerRemplacementCpe" 
			 id='AbsProfProposerRemplacementCpe' 
			 value="yes" 
			<?php if (getSettingAOui("AbsProfProposerRemplacementCpe")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='AbsProfProposerRemplacementCpe' style='cursor: pointer;'>
		Les comptes Cpe peuvent proposer des remplacements aux professeurs.
	  </label>
	  <br />
	  <input type="checkbox" 
			 name="AbsProfAttribuerRemplacementCpe" 
			 id='AbsProfAttribuerRemplacementCpe' 
			 value="yes" 
			<?php if (getSettingAOui("AbsProfAttribuerRemplacementCpe")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='AbsProfAttribuerRemplacementCpe' style='cursor: pointer;'>
		Les comptes Cpe peuvent choisir parmi les réponses favorables de professeurs lequel effectuera le remplacement.
	  </label>
	</p>

	<p>
	  <input type="checkbox" 
			 name="AbsProfGroupesClasseSeulement" 
			 id='AbsProfGroupesClasseSeulement' 
			 value="yes" 
			<?php if (getSettingAOui("AbsProfGroupesClasseSeulement")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='AbsProfGroupesClasseSeulement' style='cursor: pointer;'>
		Ne pas proposer de remplacement pour des groupes/enseignements qui ne sont pas des groupes classe.
	  </label>
	</p>

	<p>
	  <input type="checkbox" 
			 name="AbsProfAutoriserProfPasApparaitre" 
			 id='AbsProfAutoriserProfPasApparaitre' 
			 value="yes" 
			<?php if (getSettingAOui("AbsProfAutoriserProfPasApparaitre")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='AbsProfAutoriserProfPasApparaitre' style='cursor: pointer;'>
		Autoriser les professeurs choisir de ne pas apparaître du tout dans la liste des propositions de remplacement.<br />
		&nbsp;&nbsp;&nbsp;&nbsp;(<em>choix (pour le professeur) à effectuer dans 'Gérer mon compte'</em>)
	  </label>
	</p>

	<p>
	  <input type="checkbox" 
			 name="AbsProfAfficherSurEDT2" 
			 id='AbsProfAfficherSurEDT2' 
			 value="yes" 
			<?php if (getSettingAOui("AbsProfAfficherSurEDT2")) echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='AbsProfAfficherSurEDT2' style='cursor: pointer;'>
		Afficher les remplacements sur l'EDT version 2 <em style='color:red'>(expérimental)</em>.
	  </label>
	</p>

	<p class="center">
	  <input type="submit" value="Enregistrer" />
	</p>
</form>

<!-- ================================================ -->

<h2>Comptes exclus des propositions de remplacements</h2>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id='form3'>
  <fieldset class='fieldset_opacite50'>
<?php
	echo add_token_field();
?>
	<legend class="invisible">Comptes_exclus</legend>

<?php
	$tab_statuts=array('professeur');
	$tab_user_preselectionnes=array();

	$tab_user_preselectionnes=get_tab_profs_exclus_des_propositions_de_remplacement();

	echo liste_checkbox_utilisateurs($tab_statuts, $tab_user_preselectionnes);
?>

	<input type='hidden' name='is_posted' value='3' />

	<p class="center">
		<input type='submit' name='valider' value='Valider' />
	</p>

	<p style='text-indent:-4em;margin-left:4em;'><em>NOTE&nbsp;:</em> Si vous disposez de comptes génériques (<em style='font-size:x-small'>par exemple pour l'équipe d'EPS qui fait les saisies d'absences en début d'heure sur un seul poste et qui a autre chose à faire que de se connecter, saisir les absences, se déconnecter pour que le collègue suivant fasse ses saisies</em>), vous pouvez souhaiter exclure certains comptes des propositions de remplacements.</p>

  </fieldset>
</form>

<!-- ================================================ -->

<h2>Matières exclues des propositions de remplacements</h2>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id='form4'>
  <fieldset class='fieldset_opacite50'>
<?php
	echo add_token_field();
?>
	<legend class="invisible">Matieres_exclues</legend>

<?php
	$tab_matieres_preselectionnees=array();

	$tab_matieres_preselectionnees=get_tab_matieres_exclues_des_propositions_de_remplacement();

	echo liste_checkbox_matieres($tab_matieres_preselectionnees, "matiere_exclue", "cocher_decocher_matieres");

	echo js_checkbox_change_style('checkbox_change', 'texte_', "y");
?>

	<input type='hidden' name='is_posted' value='4' />

	<p class="center">
		<input type='submit' name='valider' value='Valider' />
	</p>

	<p style='text-indent:-4em;margin-left:4em;'><em>NOTE&nbsp;:</em> Vous pouvez souhaiter ne pas remplacer les enseignements de certaines matières comme la Vie de classe, l'aide aux devoirs,...</p>

  </fieldset>
</form>

<!-- ================================================ -->

<h2>Messages</h2>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id='form5'>
  <fieldset class='fieldset_opacite50'>
<?php
	echo add_token_field();
?>
	<legend class="invisible">Messages</legend>

<?php
	$abs_prof_modele_message_eleve=getSettingValue('abs_prof_modele_message_eleve');
	if($abs_prof_modele_message_eleve=="") {
		$abs_prof_modele_message_eleve="En raison de l'absence de __PROF_ABSENT__, le cours __COURS__ du __DATE_HEURE__ sera remplacé par un cours avec __PROF_REMPLACANT__ en salle __SALLE__.";
		if(getSettingAOui('active_edt_ical')) {
			$abs_prof_modele_message_eleve.="\n__LIEN_EDT_ICAL__";
		}
		saveSetting('abs_prof_modele_message_eleve', $abs_prof_modele_message_eleve);
	}
	echo "<p>Modèle de message en page d'accueil pour informer les parents et élèves des remplacements validés&nbsp;:</p>
	<textarea name='abs_prof_modele_message_eleve' cols='60' rows='5'>".stripslashes(preg_replace('/(\\\n)+/',"\n", $abs_prof_modele_message_eleve))."</textarea>";
?>

	<input type='hidden' name='is_posted' value='5' />

	<p class="center">
		<input type='submit' name='valider' value='Valider' />
	</p>

	<p style='text-indent:-4em;margin-left:4em;'><em>NOTE&nbsp;:</em> Les chaines __PROF_ABSENT__, __COURS__, __DATE_HEURE__, __PROF_REMPLACANT__ et __SALLE__ seront remplacées par les valeurs/textes appropriés.<br />
	Si le module EDT ICAL/ICS, est actif, vous pouvez aussi insérer la chaine __LIEN_EDT_ICAL__ qui sera remplacée par un lien vers la semaine appropriée dans l'emploi du temps de la classe.</p>

  </fieldset>
</form>

<!-- ================================================ -->

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


