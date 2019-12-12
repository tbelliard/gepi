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

<?php
//debug_var();
?>

  <form action="index_admin.php" id="form1" method="post" style='border: 1px solid grey; background-image: url("../images/background/opacite50.png")'>
<?php
	echo add_token_field();
?>
	
	<h2 class="colleHaut">Configuration générale</h2>
	<p class="italic">
	  La désactivation du module Bulletins n'entraîne aucune suppression des données. 
	  Lorsque le module est désactivé, les professeurs n'ont pas accès au module.
	</p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Activé ou non</legend>
	  <input type="radio" 
			 name="activer" 
			 id='activer_y' 
			 value="y" 
			<?php if (getSettingValue("active_bulletins")=='y') echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_y' style='cursor: pointer;'>
		Activer le module bulletins
	  </label>
	<br />
	  <input type="radio" 
			 name="activer" 
			 id='activer_n' 
			 value="n" 
			<?php if (getSettingValue("active_bulletins")=='n') echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_n' style='cursor: pointer;'>
		Désactiver le module bulletins
	  </label>
	</fieldset>

	<p class="center">
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="submit" value="Enregistrer" />
	</p>

</form>

<br />

  <form action="index_admin.php" id="form_acces_moy" method="post" style='border: 1px solid grey; background-image: url("../images/background/opacite50.png")'>
<?php
	echo add_token_field();
?>
	
	<h2 class="colleHaut">Accès aux moyennes et appréciations</h2>

	<p>
	  L'accès élève/parent aux appréciations des bulletins et avis du conseil de classe définis d'une des façons suivantes&nbsp;:<br />
<?php
$acces_app_ele_resp=getSettingValue("acces_app_ele_resp");
if($acces_app_ele_resp=="") {$acces_app_ele_resp='manuel';}
?>
		<input type='radio' 
			   name='acces_app_ele_resp' 
			   id='acces_app_ele_resp_manuel' 
			   value='manuel' 
			   onchange='changement()'
			   <?php if($acces_app_ele_resp=='manuel') {echo "checked='checked'";} ?>
			   />
		<label for='acces_app_ele_resp_manuel'>
			manuel (<em>ouvert par la scolarité, classe par classe</em>)
		</label>
		<br />
		<input type='radio' 
			   name='acces_app_ele_resp' 
			   id='acces_app_ele_resp_manuel_individuel' 
			   value='manuel_individuel' 
			   onchange='changement()'
			   <?php if($acces_app_ele_resp=='manuel_individuel') {echo "checked='checked'";} ?>
			   />
		<label for='acces_app_ele_resp_manuel_individuel' title="Ce choix, s'il est fastidieux permet de traiter la situation suivante:
Si les bulletins sont remis en mains propres aux familles par le professeur principal, on peut souhaiter ne pas donner acccès aux appréciations et avis tant que la famille ne s'est pas déplacée.
Il peut en effet être intéressant de voir les familles des élèves pour lesquels une réaction est attendue.">
			manuel élève par élève (<em>ouvert par la scolarité</em>) <img src='../images/icons/ico_question_petit.png' class='icone16' alt='Info' />
		</label>
		<br />
		<input type='radio' 
			   name='acces_app_ele_resp' 
			   id='acces_app_ele_resp_date' 
			   value='date' 
			   <?php if($acces_app_ele_resp=='date') {echo "checked='checked'";} ?>
			   onchange='changement()' />
		<label for='acces_app_ele_resp_date'>à une date choisie (<em>par la scolarité</em>)</label>
		<br />
<?php 
$delais_apres_cloture=getSettingValue("delais_apres_cloture");
if($delais_apres_cloture=="") {$delais_apres_cloture=0;}
?>
		<input type='radio' 
			   name='acces_app_ele_resp' 
			   id='acces_app_ele_resp_periode_close' 
			   value='periode_close' 
			   onchange='changement()' 
			   <?php if($acces_app_ele_resp=='periode_close') {echo "checked='checked'";} ?>
			    />
		<input type='text' 
			   name='delais_apres_cloture' 
			   id='delais_apres_cloture' 
			   value='<?php echo $delais_apres_cloture; ?>'
			   size='1' 
			   onchange="changement();document.getElementById('acces_app_ele_resp_periode_close').checked=true;" onkeydown="clavier_2(this.id,event,1,600);document.getElementById('acces_app_ele_resp_periode_close').checked=true;" />
		<label for='acces_app_ele_resp_periode_close'>
			jours après la clôture de la période
		</label>
	</p>

	<br />

	<p>Par défaut, l'accès parent/élève aux moyennes des enseignements est possible dès que le professeur a rempli les bulletins.<br />
	Si vous souhaitez restreindre l'accès, vous pouvez opter pour une des alternatives&nbsp;:<br />
	</p>

<?php
$acces_moy_ele_resp=getSettingValue("acces_moy_ele_resp");
if($acces_moy_ele_resp=="") {$acces_moy_ele_resp='immediat';}
$acces_moy_ele_resp_cn=getSettingValue("acces_moy_ele_resp_cn");
if($acces_moy_ele_resp_cn=="") {$acces_moy_ele_resp_cn='immediat';}
?>
	<p>
		<input type='radio' 
			   name='acces_moy_ele_resp' 
			   id='acces_moy_ele_resp_immediat' 
			   value='immediat' 
			   onchange='changement()'
			   <?php if($acces_moy_ele_resp=='immediat') {echo "checked='checked'";} ?>
			   />
		<label for='acces_moy_ele_resp_immediat'>
			immédiat (<em>dès que la moyenne est renseignée par le professeur sur le bulletin</em>)
		</label>
		<br />
		<input type='radio' 
			   name='acces_moy_ele_resp' 
			   id='acces_moy_ele_resp_comme_app' 
			   value='comme_app' 
			   onchange='changement()'
			   <?php if($acces_moy_ele_resp=='comme_app') {echo "checked='checked'";} ?>
			   />
		<label for='acces_moy_ele_resp_comme_app'>
			comme l'accès aux appréciations (<em>dans ce cas, l'accès aux moyennes est donné lors de l'ouverture de l'accès aux appréciations comme paramétré ci-dessus</em>)
		</label>
		<br />
	</p>

	<br />

	<p>Si les élèves/parents ont accès aux moyennes des carnets de notes, ils peuvent connaitre les moyennes même si l'accès aux moyennes des bulletins est bloqué.<br />
	<em>(les moyennes des bulletins sont en effet généralement un simple transfert/recopie des moyennes des carnets de notes vers les bulletins).</em><br />
	<br />
	Vous pouvez conditionner l'accès aux moyennes des carnets de notes à l'accès aux moyennes des bulletins&nbsp;:<br />
		<input type='radio' 
			   name='acces_moy_ele_resp_cn' 
			   id='acces_moy_ele_resp_cn_immediat' 
			   value='immediat' 
			   onchange='changement()'
			   <?php if($acces_moy_ele_resp_cn=='immediat') {echo "checked='checked'";} ?>
			   />
		<label for='acces_moy_ele_resp_cn_immediat'>
			donner l'accès aux moyennes des carnets de notes même si les moyennes des bulletins ne sont pas encore accessibles.
		</label>
		<br />
		<input type='radio' 
			   name='acces_moy_ele_resp_cn' 
			   id='acces_moy_ele_resp_cn_comme_bull' 
			   value='comme_bull' 
			   onchange='changement()'
			   <?php if($acces_moy_ele_resp_cn=='comme_bull') {echo "checked='checked'";} ?>
			   />
		<label for='acces_moy_ele_resp_cn_comme_bull'>
			Ne pas donner l'accès aux moyennes des carnets de notes si les moyennes des bulletins ne sont pas encore accessibles.
		</label>
	</p>

	<br />

	<p><em>NOTES&nbsp;:</em></p>
	<ul>
<?php
	$GepiAccesColMoyReleveEleve=getSettingAOui('GepiAccesColMoyReleveEleve');
	$GepiAccesColMoyReleveParent=getSettingAOui('GepiAccesColMoyReleveParent');

	$cn_affiche_moy_gen=getSettingAOui('cn_affiche_moy_gen');
	if($cn_affiche_moy_gen) {
		echo "
		<li>L'affichage des moyennes d'enseignements sur les carnets de notes est autorisé.<br />
			Voir 
			<a href='../cahier_notes_admin/index.php'
			   onclick=\"return confirm_abandon (this, change, '".$themessage."')\">
				Droits d'accès
			</a></li>";
	}
	else {
		echo "
		<li>L'affichage des moyennes d'enseignements sur les carnets de notes est globalement désactivé.<br />
			Le paramétrage ci-dessus est donc sans effet.<br />
			Voir 
			<a href='../cahier_notes_admin/index.php'
			   onclick=\"return confirm_abandon (this, change, '".$themessage."')\">
				Droits d'accès
			</a>
		</li>";
	}

	if($GepiAccesColMoyReleveEleve) {
		echo "
		<li>Les élèves sont autorisés à afficher les moyennes d'enseignements sur les carnets de notes.<br />
			Voir 
			<a href='../droits_acces.php#eleve'
			   onclick=\"return confirm_abandon (this, change, '".$themessage."')\">
				Droits d'accès
			</a>
		</li>";
	}
	else {
		echo "
		<li>Les élèves ne sont pas autorisés à afficher les moyennes d'enseignements sur les carnets de notes.<br />
			Le paramétrage ci-dessus est donc sans effet.<br />
			Voir 
			<a href='../droits_acces.php#eleve'
			   onclick=\"return confirm_abandon (this, change, '".$themessage."')\">
				Droits d'accès
			</a>
		</li>";
	}

	if($GepiAccesColMoyReleveParent) {
		echo "
		<li>Les responsables (parents) sont autorisés à afficher les moyennes d'enseignements sur les carnets de notes.<br />
			Voir 
			<a href='../droits_acces.php#responsable'
			   onclick=\"return confirm_abandon (this, change, '".$themessage."')\">
				Droits d'accès
			</a>
		</li>";
	}
	else {
		echo "
		<li>Les responsables (parents) ne sont pas autorisés à afficher les moyennes d'enseignements sur les carnets de notes.<br />
			Le paramétrage ci-dessus est donc sans effet.<br />
			Voir 
			<a href='../droits_acces.php#responsable'
			   onclick=\"return confirm_abandon (this, change, '".$themessage."')\">
				Droits d'accès
			</a>
		</li>";
	}
?>
		<li>Les accès paramétrés ci-dessus sont possibles, sous réserve&nbsp;:<br />
			<ul style='font-variant: normal; font-style: italic; font-size: small;'>
				<li style='font-variant: normal; font-style: italic; font-size: small;'>
					de créer des comptes pour les responsables et élèves,
				</li>
				<li style='font-variant: normal; font-style: italic; font-size: small;'>
					d'autoriser l'accès aux bulletins simplifiés ou aux graphes dans 
					<a href='droits_acces.php#bull_simp_ele'
					   onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
						Droits d'accès
					</a>
				</li>
			</ul>
		</li>
	</ul>

	<p style='text-align:center'><input type="submit" value="Enregistrer" /></p>

</form>

<br />

  <form action="index_admin.php" id="form3" method="post" style='border: 1px solid grey; background-image: url("../images/background/opacite50.png")'>
<?php
	echo add_token_field();
?>
	
	<h2 class="colleHaut">Paramètres divers</h2>

	<p style='margin-top:1em;'>Si vous ne souhaitez pas afficher la moyenne générale en conseil de classe, mais que vous souhaitez permettre un calcul de moyenne générale pour les personnels, il ne faut pas afficher les moyennes générales par défaut sur les bulletins simplifiés.<br />
	Vous pouvez effectuer ce choix ici&nbsp;:<br />
	<input type='checkbox' name='bullNoMoyGenParDefaut' id='bullNoMoyGenParDefaut' value='yes' <?php
	if(getSettingAOui("bullNoMoyGenParDefaut")) {echo "checked ";}
	?>/><label for='bullNoMoyGenParDefaut'> Ne pas afficher la ligne des moyennes générales par défaut</label></p>

	<p><input type='checkbox' name='bullNoMoyCatParDefaut' id='bullNoMoyCatParDefaut' value='yes' <?php
	if(getSettingAOui("bullNoMoyCatParDefaut")) {echo "checked ";}
	?>/><label for='bullNoMoyCatParDefaut'> Ne pas afficher la ligne des moyennes de catégories par défaut</label></p>

	<p><input type='checkbox' name='bullNoSaisieElementsProgrammes' id='bullNoSaisieElementsProgrammes' value='yes' <?php
	if(getSettingAOui("bullNoSaisieElementsProgrammes")) {echo "checked ";}
	?>/><label for='bullNoSaisieElementsProgrammes'> Ne pas afficher la colonne de saisie des éléments de programmes dans la saisie d'appréciations, et donc dans les bulletins</label></p>

	<p style='margin-top:1em;'><em>NOTES&nbsp;: à propos des moyennes générales</em></p>
	<ul>
		<li><p>N'oubliez pas de paramétrer l'autorisation/interdiction d'accès pour les élèves/responsables dans Gestion générale/Droits d'accès.</p></li>
		<li><p>Pour les graphes, vous pouvez choisir de ne pas afficher la moyenne générale via le Paramétrage des graphes.</p></li>
	</ul>

	<br />

	<p style='margin-left:2em;text-indent:-2em;'><input type='checkbox' name='insert_mass_appreciation_type' id='insert_mass_appreciation_type' value='y' <?php
	if(getSettingAOui("insert_mass_appreciation_type")) {echo "checked ";}
	?>/><label for='insert_mass_appreciation_type'> Permettre l'insertion d'appréciations par lots pour les comptes de statut <strong>secours</strong>.<br />
	Cela permet d'insérer par exemple un tiret pour tous les élèves dans le cas d'une absence longue sur un enseignement.<br />
	L'appréciation une fois remplie avec un tiret, le test de remplissage alertant que les bulletins ne sont pas remplis ne posera plus de problème.</label><br />
	<?php
		$sql="CREATE TABLE IF NOT EXISTS b_droits_divers (login varchar(50) NOT NULL default '', nom_droit varchar(50) NOT NULL default '', valeur_droit varchar(50) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

		echo "Donner également ce droit aux utilisateurs suivants&nbsp;:<br />";
		echo "<div style='margin-left:5em;'>";
		$tab_user_preselectionnes=array();
		$sql="SELECT * FROM b_droits_divers WHERE nom_droit='insert_mass_appreciation_type' AND valeur_droit='y';";
		$res_mass=mysqli_query($mysqli, $sql);
		while($lig_mass=mysqli_fetch_object($res_mass)) {
			$tab_user_preselectionnes[]=$lig_mass->login;
		}
		/*
		echo "<pre>";
		print_r($tab_user_preselectionnes);
		echo "</pre>";
		*/
		echo liste_checkbox_utilisateurs(array("professeur", "scolarite", "cpe"), $tab_user_preselectionnes, 'login_user_mass_app', 'cocher_decocher', 'y', '', 'checkbox_change', 'y');
		echo "</div>";
	?>

	<br />
	
	<p>Permettre l'insertion automatique d'appréciations type d'après la moyenne de l'élève.<br />
	Ce dispositif n'est pas recommandé.<br />
	Il convient d'individualiser les appréciations sans se limiter à statuer sur une moyenne obtenue.<br />
	Le dispositif peut néanmoins présenter un intérêt pour des tests.</p>
	<?php
		$sql="CREATE TABLE IF NOT EXISTS b_droits_divers (login varchar(50) NOT NULL default '', nom_droit varchar(50) NOT NULL default '', valeur_droit varchar(50) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

		echo "<p>Donner le droit correspondant aux utilisateurs suivants&nbsp;:</p>";
		echo "<div style='margin-left:5em;'>";
		$tab_user_preselectionnes=array();
		$sql="SELECT * FROM b_droits_divers WHERE nom_droit='insert_mass_appreciation_type_d_apres_moyenne' AND valeur_droit='y';";
		$res_mass=mysqli_query($mysqli, $sql);
		while($lig_mass=mysqli_fetch_object($res_mass)) {
			$tab_user_preselectionnes[]=$lig_mass->login;
		}
		/*
		echo "<pre>";
		print_r($tab_user_preselectionnes);
		echo "</pre>";
		*/
		echo liste_checkbox_utilisateurs(array("professeur", "secours"), $tab_user_preselectionnes, 'login_user_mass_app_moy', 'cocher_decocher', 'y', '', 'checkbox_change', 'y');
		echo "</div>";
	?>

	<p class="center">
	  <input type="hidden" name="is_posted" value="param_divers" />
	  <input type="submit" value="Enregistrer" />
	</p>

	<script type='text/javascript'>
	<?php
		echo js_checkbox_change_style();
	?>
	</script>
</form>

<br />

  <form action="index_admin.php" id="form2" method="post" style='border: 1px solid grey; background-image: url("../images/background/opacite50.png")'>
<?php
	echo add_token_field();
?>
	<input type="hidden" name="is_posted" value="1" />

	<h2 class="colleHaut">Absences sur les bulletins</h2>
	<p>
	  Vous pouvez souhaiter vider les enregistrements d'absences (*) réalisés pour les bulletins de façon à refaire un remplissage des absences par la suite.<br />
	  Cela ne supprime pas les enregistrements effectués dans les modules Absence de Gepi.<br />
	<?php
		if(getSettingValue("active_module_absence") == '2') {
			echo "
	  Dans le cas où vous utilisez le module Absences 2, le \"vidage\" ne fonctionne que si vous avez opté pour un import manuel des absences.";
		}
	?>
	</p>

	<p class="center">
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="hidden" name="vider_absences_bulletins" value="y" />
	  <input type="submit" value="Vider les saisies absences des bulletins" />
	</p>
	<p style='color:red; text-align:center;'>ATTENTION : L'opération est irréversible.<br />
	De plus, elle concerne toutes les classes et ce pour toutes les périodes.</p>

	<br />
	<p>(*) Les nombre de demi-journées d'absences, nombre d'absences non justifiées, nombre de retards et observation du CPE seront supprimées.</p>
</form>

	<br />


  <form action="index_admin.php" id="form_classes_exclues" method="post" style='border: 1px solid grey; background-image: url("../images/background/opacite50.png")'>
<?php
	echo add_token_field();
?>
	<input type="hidden" name="is_posted" value="classes_exclues" />

	<h2 class="colleHaut">Classes exclues</h2>
	<p>
	Vous pouvez souhaiter ne pas utiliser les bulletins Gepi pour certaines classes.<br />
	Choisissez ici les classes pour lesquelles les menus bulletins ne seront pas proposés.</p>
	<?php
		$sql="CREATE TABLE IF NOT EXISTS modules_restrictions 
		(id int(11) NOT NULL auto_increment, 
		module varchar(50) NOT NULL DEFAULT '', 
		name varchar(50) NOT NULL DEFAULT '', 
		value varchar(50) NOT NULL DEFAULT '', 
		PRIMARY KEY (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$create=mysqli_query($mysqli, $sql);

		$tab_classes_exclues=array();
		$sql="SELECT * FROM modules_restrictions WHERE module='bulletins' AND name='id_classe';";
		$res=mysqli_query($mysqli, $sql);
		while($lig=mysqli_fetch_object($res)) {
			$tab_classes_exclues[]=$lig->value;
		}

		echo liste_checkbox_classes($tab_classes_exclues, 'id_classe', 'cocher_decocher', '', "checkbox_change", 'y');

	?>
	</p>

	<p class="center">
	  <input type="submit" value="Exclure les classes cochées" />
	</p>
</form>

	<br />

	<div style='border: 1px solid grey; background-image: url("../images/background/opacite50.png")'>
	<h2 class="colleHaut">Divers</h2>

	<ul>
		<li>
			<p><a href='../gestion/param_gen.php#mode_ouverture_acces_appreciations'>Définir le mode d'ouverture de l'accès parents/élèves aux appréciations et avis du conseil de classe</a><br />
			et <a href='../classes/acces_appreciations.php'>consulter/modifier l'accès pour les différentes classes et périodes</a></p>
		</li>
<?php
	if(acces("/gestion/gestion_signature.php", $_SESSION['statut'])) {
		echo "
		<li>
			<p style='margin-top:2em;'><a href='../gestion/gestion_signature.php'>Définir, modifier ou supprimer un ou des fichiers de signature pour les bulletins.</a></p>
		</li>\n";
	}
?>
		<li>
			<p><a href='../mod_engagements/index_admin.php'>Définir les engagements</a> (<em>délégués de classe, délégués de parents,...</em>)</p>
		</li>
		<li>
			<p><a href='../mod_ooo/gerer_modeles_ooo.php#MODULE_Engagements'>Modifier les modèles de documents liés aux engagements</a></p>
		</li>
	</ul>
	</div>

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


