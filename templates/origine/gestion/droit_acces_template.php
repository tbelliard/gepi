<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id$
*/
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<!-- on inclut l'entête -->
	<?php include('../templates/origine/header_template.php');?>

  <script type="text/javascript" src="../templates/origine/lib/fonction_change_ordre_menu.js"></script>

  <link rel="stylesheet" type="text/css" href="../templates/origine/css/accueil.css" media="screen" />
  <link rel="stylesheet" type="text/css" href="../templates/origine/css/bandeau.css" media="screen" />
  <link rel="stylesheet" type="text/css" href="../templates/origine/css/param_ordre_item.css" media="screen" />

<!-- corrections internet Exploreur -->
	<!--[if lte IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/accueil_ie.css' media='screen' />
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/bandeau_ie.css' media='screen' />
	<![endif]-->
	<!--[if lte IE 6]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/accueil_ie6.css' media='screen' />
	<![endif]-->
	<!--[if IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/accueil_ie7.css' media='screen' />
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

<!-- ******************************************** -->
<!-- Appelle les sous-modèles                     -->
<!-- templates/origine/header_template.php        -->
<!-- templates/origine/bandeau_template.php      -->
<!-- ******************************************** -->

<!-- ************************* -->
<!-- Début du corps de la page -->
<!-- ************************* -->
<body onload="show_message_deconnexion();<?php echo $tbs_charger_observeur;?>">

<!-- on inclut le bandeau -->
	<?php include('../templates/origine/bandeau_template.php');?>

	<?php //debug_var();?>

<!-- fin bandeau_template.html      -->

<div  id='container'>

  <a name="contenu" class="invisible">
	Début de la page
  </a>
  
<!-- début corps -->

  <form action="droits_acces.php" method="post" id="form1">
	<input type='hidden' name='onglet_courant' id='onglet_courant' value='' />
	<p class="center">
		<?php
			echo add_token_field();
		?>
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="submit" name = "OK" value="Enregistrer" />
	</p>

    <div class="systeme_onglets">
	  <div class="onglets">
	<?php 
	  foreach (array('Enseignant', 'Professeur_principal', 'Scolarite', 'CPE', 'Administrateur', 'eleve', 'responsable') as $StatutItem){
    ?>
	  <a class="onglet_0 onglet" id='onglet_<?php echo $StatutItem ;?>' href="#<?php echo $StatutItem ;?>" title="section <?php echo $StatutItem ;?>" onclick="javascript:change_onglet('<?php echo $StatutItem; ?>');document.getElementById('onglet_courant').value='<?php echo $StatutItem; ?>';return false;">
	<?php
		if (my_strtolower($StatutItem) =='responsable') echo ucfirst($gepiSettings['denomination_responsable']);
		elseif (my_strtolower($StatutItem) =='eleve') echo ucfirst($gepiSettings['denomination_eleve']);
		elseif (my_strtolower($StatutItem) =='professeur_principal') echo ucfirst(getSettingValue("gepi_prof_suivi"));
		elseif (my_strtolower($StatutItem) =='scolarite') echo "Scolarité";
		else echo $StatutItem ;
	 ?>
	  </a>
	
	<?php
	  }
	  unset($StatutItem);
	?>

	</div>

    <div class="contenu_onglets">

	<?php foreach (array('Enseignant', 'Professeur_principal', 'Scolarite', 'CPE', 'Administrateur', 'eleve', 'responsable') as $StatutItem){ ?>
	  <div class="contenu_onglet2" id="contenu_onglet_<?php echo $StatutItem ;?>">

<script type="text/javascript">
//<!--
			document.getElementById('contenu_onglet_<?php echo $StatutItem ;?>').className = 'contenu_onglet';
//-->
</script>
<noscript></noscript>

	  <h2 class="center">
	  <a name="<?php echo $StatutItem ;?>" href="#container" title="retour début de page depuis <?php echo $StatutItem ;?>" >

	  <?php
		if (my_strtolower($StatutItem) =='responsable') echo $gepiSettings['denomination_responsable'];
		elseif (my_strtolower($StatutItem) =='eleve') echo $gepiSettings['denomination_eleve'];
		elseif (my_strtolower($StatutItem) =='professeur_principal') echo getSettingValue("gepi_prof_suivi");
		elseif (my_strtolower($StatutItem) =='scolarite') echo "Scolarité";
		else echo $StatutItem ;
	  ?>
	  </a>
	</h2>
	<h3 class="accueil">
	  Paramétrage des droits d'accès
	</h3>
	<ul class='div_tableau'>
	<?php foreach ($droitAffiche->get_item() as $AfficheItem){ 
	  if(my_strtolower($AfficheItem['statut']) == my_strtolower($StatutItem)){

	?>
	  <li style='margin-left:2em;text-indent:-2em;'>
		<input type="checkbox" name="<?php echo $AfficheItem['name'] ; ?>"
			   id="<?php echo $AfficheItem['name'] ; ?>"
			   value="yes" <?php if (getSettingValue($AfficheItem['name'])=='yes') echo 'checked="checked"'; ?>
			   onchange='changement();' />
		<label for='<?php echo $AfficheItem['name'] ; ?>'
			   style='cursor: pointer;'>
		<?php echo $AfficheItem['texte'] ; ?>
		</label>
	  </li>
	  <?php
	  }
	}
	unset ($AfficheItem);
	?>
	</ul>
	</div>
	<?php
	  }
	  unset ($StatutItem);
	?>

	</div>
    </div>
  </form>
<script type="text/javascript">
//<!--
//document.getElementByClassNames('contenu_onglet2').ClassNames = 'contenu_onglet';
	<?php
		$anc_onglet=isset($_POST['onglet_courant']) ? $_POST['onglet_courant'] : 'Enseignant';
		if($anc_onglet=='') {$anc_onglet='Enseignant';}
	?>

	var anc_onglet = '<?php echo $anc_onglet;?>';
	change_onglet(anc_onglet);
//-->
</script>


<!-- Début du pied -->
	<div id='EmSize' style='visibility:hidden; position:absolute; left:1em; top:1em;'></div>

	<script type='text/javascript'>
		var ele=document.getElementById('EmSize');
		var em2px=ele.offsetLeft
		//alert('1em == '+em2px+'px');
	</script>
<noscript></noscript>

	<script type='text/javascript'>
		temporisation_chargement='ok';
	</script>
<noscript></noscript>


</div>

<?php
  if ($tbs_microtime!="") {
?>
<p class='microtime'>Page générée en
<?php
  echo $tbs_microtime;
?>
  sec
</p>

<?php
  }

 if ($tbs_pmv!="") {
?>
  <script type='text/javascript'>
  //<![CDATA[
<?php
	echo $tbs_pmv;
?>
	//]]>
  </script>
<noscript></noscript>
<?php
  }
?>

</body>
</html>

