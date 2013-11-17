<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
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
<!-- Fin haut de page -->

  <p class="bold">
	Cet outil permet d'autoriser la génération de flux 
	<acronym title="Really Simple Syndication">RSS</acronym> 
	2.0 des cahiers de textes de Gepi. 
  </p>

<?php 
if (count($lienFlux)){
  foreach($lienFlux as $lien){
 ?>
  <p class="bold vert">
	<?php if($lien['confirme']){ ?>
		La table existe et les URI sont en place.&nbsp;&nbsp;
	<?php } ?>
	<a href="<?php echo $lien['lien']; echo add_token_in_url(); ?>" <?php if($lien['confirme']) echo "onclick=\"return confirm_abandon(this, 'yes', '$themessage')\""; ?>>
	  <?php echo $lien['texte'] ?>
	</a>
  </p>
<?php 
  }
}
 ?>
 
  <form id="form_rss" action="rss_cdt_admin.php" method="post">
	<fieldset style='border: 1px solid grey; margin-bottom:0.5em; background-image: url("../images/background/opacite50.png");'>
		<legend style='border: 1px solid grey; margin-bottom:0.5em; background-image: url("../images/background/opacite50.png");'>Accès</legend>
	<p>
<?php
echo add_token_field();
?>
	  <input type="hidden" name="action" value="modifier" />
	  <input type="checkbox"
			 id="autoRssCdt"
			 name="rss_cdt_ele"
			 value="y"
			 onclick="changementDisplay('accesEle', '');"
			 onchange='document.getElementById("form_rss").submit();'
			<?php echo $checked_ele; ?> />
	  <label for="autoRssCdt">
		Les élèves peuvent utiliser le flux RSS de leur cahier de textes.
	  </label>
	  <br />
	  <input type="checkbox"
			 id="autoRssCdtResp"
			 name="rss_cdt_responsable"
			 value="y"
			 onclick="changementDisplay('accesResp', '');"
			 onchange='document.getElementById("form_rss").submit();'
			<?php echo $checked_resp; ?> />
	  <label for="autoRssCdtResp">
		Les responsables peuvent utiliser le flux RSS des cahiers de textes des élèves dont ils sont responsables.
	  </label>

	  <br />
	  <input type="checkbox"
			 id="autoRssCdtScol"
			 name="rss_cdt_scol"
			 value="y"
			 onclick="changementDisplay('accesScol', '');"
			 onchange='document.getElementById("form_rss").submit();'
			<?php echo $checked_scol; ?> />
	  <label for="autoRssCdtScol">
		Les comptes scolarité peuvent voir (<em>et donc consulter</em>) le flux RSS des cahiers de textes des élèves (*).
	  </label>

	  <br />
	  <input type="checkbox"
			 id="autoRssCdtCpe"
			 name="rss_cdt_cpe"
			 value="y"
			 onclick="changementDisplay('accesCpe', '');"
			 onchange='document.getElementById("form_rss").submit();'
			<?php echo $checked_cpe; ?> />
	  <label for="autoRssCdtCpe">
		Les comptes cpe peuvent voir (<em>et donc consulter</em>) le flux RSS des cahiers de textes des élèves (*).
	  </label>

	  <br />
	  <input type="checkbox"
			 id="autoRssCdtPP"
			 name="rss_cdt_pp"
			 value="y"
			 onclick="changementDisplay('accesPP', '');"
			 onchange='document.getElementById("form_rss").submit();'
			<?php echo $checked_pp; ?> />
	  <label for="autoRssCdtPP">
		Les comptes <?php echo getSettingValue('gepi_prof_suivi');?> peuvent voir (<em>et donc consulter</em>) le flux RSS des cahiers de textes des élèves (*).
	  </label>
	  <br />
	  <br />
	  (*)<em> depuis la page de <a href='../eleves/visu_eleve.php' target='_blank'>consultation des fiches élèves</a>, pour par exemple transmettre l'adresse du flux à un élève/responsable qui aurait du mal à se connecter</em>.
	</p>
	</fieldset>
  </form>
  <br />

  <div id="accesEle"<?php echo $style_ele; ?>>
	<form id="form_rss_ele" action="rss_cdt_admin.php" method="post">
	  <fieldset style='border: 1px solid grey; margin-bottom:0.5em; background-image: url("../images/background/opacite50.png");'>
		<legend style='border: 1px solid grey; margin-bottom:0.5em; background-image: url("../images/background/opacite50.png");'>Mode de récupération</legend>
<?php
echo add_token_field();
?>
		<input type="radio"
			   id="rssAccesEle"
			   name="rss_acces_ele"
			   value="direct"
			   onchange='document.getElementById("form_rss_ele").submit();'
			  <?php echo $style_ele_dir; ?> />
		<label for="rssAccesEle">
		  Les élèves (<em>et/ou responsables selon ce qui est coché ci-dessus</em>) récupèrent l'adresse (<em>url</em>) d'abonnement directement par leur accès à Gepi
		</label>
		<br />

		<input type="radio"
			   id="rssAccesEle2"
			   name="rss_acces_ele"
			   value="csv"
			   onchange='document.getElementById("form_rss_ele").submit();'
			  <?php echo $style_ele_csv; ?> />
		<label for="rssAccesEle2">
		  L'admin récupère un fichier csv de ces adresses (<em>une par élève</em>)
		</label>
	  </fieldset>
	</form>
  </div>

  <br />

  <div id="emailRSS"<?php echo $style_ele; ?>>
	<form id="form_rss_email" action="rss_cdt_admin.php" method="post">
	  <fieldset style='border: 1px solid grey; margin-bottom:0.5em; background-image: url("../images/background/opacite50.png");'>
		<legend style='border: 1px solid grey; margin-bottom:0.5em; background-image: url("../images/background/opacite50.png");'>Adresse email</legend>
<?php
echo add_token_field();
?>
		Adresse email à utiliser par défaut dans les flux RSS&nbsp;:<br />
		<input type="radio"
			   id="rss_email_mode_adm"
			   name="rss_email_mode"
			   value="email_admin"
			   onchange='document.getElementById("form_rss_email").submit();'
			  <?php echo $style_email_adm; ?> />
		<label for="rss_email_mode_adm">
			Utiliser l'email de l'administrateur GEPI (<em><?php echo getSettingValue('gepiAdminAdress');?></em>)
		</label>
		<br />

		<input type="radio"
			   id="rss_email_mode_etab"
			   name="rss_email_mode"
			   value="email_etab"
			   onchange='document.getElementById("form_rss_email").submit();'
			  <?php echo $style_email_etab; ?> />
		<label for="rss_email_mode_etab">
			Utiliser l'email de l'établissement (<em><?php echo getSettingValue('gepiSchoolEmail');?></em>)
		</label>
		<br />

		<input type="checkbox"
			   id="rss_email_prof"
			   name="rss_email_prof"
			   value="y"
			   onchange='document.getElementById("form_rss_email").submit();'
			  <?php echo $style_email_prof; ?> />
		<label for="rss_email_prof">
			Si le professeur autorise la présentation de son adresse email aux utilisateurs non personnels de l'établissement, utiliser son adresse email dans les notices concernant ses enseignements plutôt que celle ci-dessus utilisée par défaut (<em>pour les professeurs n'acceptant pas la présentation de leur adresse email</em>).
		</label>
		<br />

	  </fieldset>
	</form>
  </div>

  <a name='rss_initialisation_cas_par_cas'></a>
  <div id="selectEleRSS"<?php echo $style_ele; ?>>
	<form id="form_rss_selection_ele" action="rss_cdt_admin.php" method="post">
	  <fieldset style='border: 1px solid grey; margin-bottom:0.5em; background-image: url("../images/background/opacite50.png");'>
		<legend style='border: 1px solid grey; margin-bottom:0.5em; background-image: url("../images/background/opacite50.png");'>Initialisation au cas par cas</legend>
<?php
	echo add_token_field();

	// Proposer de réinitialiser un flux pour un élève en particulier, pour une classe,...
	// Proposer de créer le flux pour les élèves manquants
	$sql="SELECT DISTINCT jec.login FROM j_eleves_classes jec
	LEFT JOIN rss_users ru ON jec.login=ru.user_login
	WHERE ru.user_login IS NULL;";
	//echo "$sql<br />";
	$res_ele_sans_flux=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res_ele_sans_flux)==0) {
		echo "
		<p>Tous les élèves ont un flux RSS initialisé.</p>\n";
	}
	else {
		echo "
		<p>".mysqli_num_rows($res_ele_sans_flux)." élève(s) n'a(ont) pas leur flux RSS initialisé.</p>
		<table class='boireaus'>
			<tr>
				<th><a href=\"javascript:ToutCocher();changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:ToutDecocher();changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a></th>
				<th>Élève</th>
			</tr>";
		$cpt=0;
		$alt=1;
		while($lig_ele_sans_flux=mysqli_fetch_object($res_ele_sans_flux)) {
			$alt=$alt*(-1);
			echo "
			<tr class='lig$alt white_hover'>
				<td><input type='checkbox' name='rss_ele_a_initialiser[]' id='rss_ele_a_initialiser_$cpt' value='$lig_ele_sans_flux->login' /></td>
				<td><label for='rss_ele_a_initialiser_$cpt'>".get_nom_prenom_eleve($lig_ele_sans_flux->login, 'avec_classe')."</label></td>
			</tr>";
			$cpt++;
		}
		echo "
		</table>
		
		<script type='text/javascript'>

			function ToutCocher(i) {
				for (var ki=0;ki<$cpt;ki++) {
					if(document.getElementById('rss_ele_a_initialiser_'+ki)){
						document.getElementById('rss_ele_a_initialiser_'+ki).checked = true;
					}
				}
			}

			function ToutDecocher(i) {
				for (var ki=0;ki<$cpt;ki++) {
					if(document.getElementById('rss_ele_a_initialiser_'+ki)){
						document.getElementById('rss_ele_a_initialiser_'+ki).checked = false;
					}
				}
			}

		</script>
";
?>
		<br />
		<input type='hidden' name='form_rss_selection_ele_is_posted' value='y' />
		<p align='center'><input type='submit' value="Créer le(s) flux pour l(es) élève(s) coché(s)" /></p>
	  </fieldset>
	</form>
  </div>

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


