<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id: $
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
	<link rel="stylesheet" type="text/css" href="../templates/origine/css/mod_abs2/mod_abs2.css" media="screen" />

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
			unset ($value);
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
	<p class="ariane">
	  <a href="../accueil.php" title="retour à l'accueil">accueil</a>
	</p>

<!-- Onglets -->

<div class="onglets">
<?php
include('../templates/origine/mod_abs2/menu_abs2_template.php');
?>
</div>
<!-- Fin Onglets -->

<div class="contenu">

<?php
  if ($affiche_visu_saisie->get_non_trouvee()){
?>
  <p>
	saisie non trouvée
  </p>
<?php
  }else{

  if (!$affiche_visu_saisie->get_modifiable()){
?>
  <p>
	La saisie n'est pas modifiable
  </p>
<?php
  }
?>

<?php
  if ($affiche_visu_saisie->get_message_enregistrement()!=""){
?>
  <p>
<?php
	echo $affiche_visu_saisie->get_message_enregistrement();
?>
  </p>
<?php
  }
?>

  <form method="post" action="enregistrement_modif_saisie.php">
	<p>
	  <input type="hidden" title="id saisie" name="id_saisie" value="<?php echo $affiche_visu_saisie->get_cle() ; ?>" />
	</p>
	<p class="tableau">
	  <span class="colonne2">N° de saisie : </span><span class="colonne2"><?php echo $affiche_visu_saisie->get_cle() ; ?></span>
	</p>
<?php
  foreach ($affiche_visu_saisie->get_tableau_eleve() as $ligneEleve){
?>
	<p class="tableau">
<?php
	if ($ligneEleve['intitule'] =="Eleve"){
	// cas particulier de la ligne avec le nom
	  echo "<span class='colonne2'>".$ligneEleve['intitule']." : </span>" ;
	  echo "<span class='colonne2'>".$ligneEleve['contenu'] ;
?>
	  <img src="<?php echo $affiche_visu_saisie->get_photo($affiche_visu_saisie->get_cle()) ;?>" class="photopetite" alt="photo de l'élève" />
<?php
	  if($affiche_visu_saisie->get_voir_fiche()){
?>
	  <a href='../eleves/visu_eleve.php?ele_login=<?php echo $affiche_visu_saisie->get_voir_login() ;?>&amp;onglet=absences'>
		(voir fiche)
	  </a>
<?php
	  }
	  echo "</span>" ;
	} else if ($ligneEleve['intitule'] =="Debut"){
?>
	  <span class='colonne2'>
		Début : 
	  </span>
	  <span class='colonne2'>
<?php
	  if (!$affiche_visu_saisie->get_modifiable()){
		echo strftime("%a %d %b %Y %H:%M", $ligneEleve['contenu'])." : " ;
	  } else {
?>
		<input name="heure_debut" title="heure début" value="<?php echo strftime("%H:%M", $ligneEleve['contenu']) ;?>" type="text" maxlength="5" size="4"/>
<?php
		if(!$affiche_visu_saisie->get_prof_decale()){
		  echo strftime(" %a %d %b %Y",$ligneEleve['contenu']) ;
		  echo '<input name="date_debut" title="date début" value="'.strftime("%d/%m/%Y",$ligneEleve['contenu']).'" type="hidden"/> ';
		}else{
		  echo '<input id="trigger_calendrier_debut" title="calendrier début" name="date_debut" value="'.strftime("%d/%m/%Y",$ligneEleve['contenu']).'" type="text" maxlength="10" size="8"/> ';
		  
?>
	<img id="trigger_date_debut" src="../images/icons/calendrier.gif" alt="" />
	<script type="text/javascript">
		  Calendar.setup({
		  inputField     :    "trigger_calendrier_debut",     // id of the input field
		  ifFormat       :    "%d/%m/%Y",      // format of the input field
		  button         :    "trigger_date_debut",  // trigger for the calendar (button ID)
		  align          :    "Tl",           // alignment (defaults to "Bl")
		  singleClick    :    true
		  });
	</script>
<?php
		}
	  }
?>
	  </span>
<?php

	} else if ($ligneEleve['intitule'] =="Fin"){
?>
	  <span class='colonne2'>
		Fin : 
	  </span>
	  <span class='colonne2'>
<?php
	  if (!$affiche_visu_saisie->get_modifiable()){
		echo strftime("%a %d %b %Y %H:%M", $ligneEleve['contenu']) ;
	  } else {
?>
	  
		<input name="heure_fin" title="heure fin" value="<?php echo strftime("%H:%M", $ligneEleve['contenu']) ;?>" type="text" maxlength="5" size="4"/>
	
<?php
		if(!$affiche_visu_saisie->get_prof_decale()){
		  echo strftime(" %a %d %b %Y",$ligneEleve['contenu']) ;
		  echo '<input name="date_fin" title="date fin" value="'.strftime("%d/%m/%Y",$ligneEleve['contenu']).'" type="hidden"/> ';
		}else{
		  echo '<input id="trigger_calendrier_fin" title="calendrier fin" name="date_fin" value="'.strftime("%d/%m/%Y",$ligneEleve['contenu']).'" type="text" maxlength="10" size="8"/> ';
?>
	<img id="trigger_date_fin" src="../images/icons/calendrier.gif" alt="" />
	<script type="text/javascript">
	    Calendar.setup({
		inputField     :    "trigger_calendrier_fin",     // id of the input field
		ifFormat       :    "%d/%m/%Y",      // format of the input field
		button         :    "trigger_date_fin",  // trigger for the calendar (button ID)
		align          :    "Tl",           // alignment (defaults to "Bl")
		singleClick    :    true
	    });
	</script>
<?php
		}

	  }

	  ?>
  </span>
<?php

	} else if ($ligneEleve['intitule'] =="Traitement"){
	  echo "<span class='colonne2'>".$ligneEleve['intitule']." : </span><span class='colonne2'>" ;
	  foreach ($affiche_visu_saisie->get_traitement_Non_modifiable() as $traite_Non_modif){
?>
		<a href='visu_traitement.php?id_traitement=<?php echo $traite_Non_modif['id'] ; ?>' style='display: block; height: 100%;'>
		  <?php echo $traite_Non_modif['description'] ; ?>
		</a>
<?php
	  }
	  unset ($traite_Non_modif) ;

	  if (count($affiche_visu_saisie->get_traitement_autorise())){
		foreach ($affiche_visu_saisie->get_traitement_autorise() as $key=>$traite_autorise){
		  if ($traite_autorise=="ajout_type_absence"){
			$selectName="ajout_type_absence";
?>
	  <input type="hidden" title="total traitement" name="total_traitements" value="0"/>
<?php
		  }else{
			$selectName="type_traitement[".$key."]";
?>
	  <input type="hidden" title="id traitement" name="id_traitement[<?php echo $key ;?>]" value="<?php echo $traite_autorise ;?>" />
		  			
<?php
		  }
?>
	  <select title="Modifier le traitement" name="<?php echo $selectName ;?>">
		  <option value='-1'></option>
<?php
		  if (count($affiche_visu_saisie->get_traitement_modifiable())){
			foreach ($affiche_visu_saisie->get_traitement_modifiable() as $traite_modif){	
?>
			  <option value='<?php echo $traite_modif['id'] ;?>' <?php if ($traite_modif['selection']){ echo " selected='selected'";} ?> >
				<?php echo $traite_modif['description'] ;?>
			  </option>
<?php
			}
			unset ($traite_modif);
		  }	
?>
		  </select>
		  <br />
<?php
	  // fin boucle 1
		}
		unset ($key,$traite_autorise);
	  }
	  // types d'absences
	  if ($affiche_visu_saisie->get_type_absence()){
		foreach ($affiche_visu_saisie->get_type_absence() as $typeAbsence){
?>
		  <select title="Type d'absence" name="ajout_type_absence">
			<option value='-1'></option>
			<option value='<?php echo $typeAbsence['id'] ; ?>'>
			  <?php echo $typeAbsence['description'] ; ?>
			</option>

		  </select>
<?php
		}

	  }
	  echo "</span>";
	} else if($ligneEleve['intitule'] =="Commentaire") {
   //  Commentaires
	  echo "<span class='colonne2'>".$ligneEleve['intitule']." : </span><span class='colonne2'>" ;
	  if (!$affiche_visu_saisie->get_modifiable()) {
		echo $ligneEleve['contenu'] ;
	  } else {
?>
		  <input name="commentaire" title="commentaire" value="<?php echo $ligneEleve['contenu'] ; ?> " type="text" maxlength="150" size="25"/>

<?php
	  }
	  echo "</span>";
	  
	} else if($ligneEleve['intitule'] =="Incidents") {
   //  Discipline
?>
		<span class='colonne2'>
		  Discipline :
		</span>
		<span class='colonne2'>
		<a href='../mod_discipline/saisie_incident.php?id_incident=<?php echo $ligneEleve['contenu'] ; ?>&step=2&return_url=no_return'>
		  Visualiser l'incident
		</a>
		</span>
<?php


	} else if($ligneEleve['intitule'] =="Discipline") {
   //  Discipline
?>
		<span class='colonne2'>
		  Discipline :
		</span>
		<span class='colonne2'>
		<a href='../mod_discipline/saisie_incident_abs2.php?id_absence_eleve_saisie=<?php echo $ligneEleve['contenu'] ; ?>&return_url=no_return'>
		  Saisir un incident disciplinaire
		</a>
		</span>
<?php

	} else{
	  echo "<span class='colonne2'>".$ligneEleve['intitule']." : </span>";
	  echo "<span class='colonne2'>".$ligneEleve['contenu']."</span>" ;
	}
		
?>
	</p>
<?php
  }
  unset($ligneEleve);
  if ($affiche_visu_saisie->get_modifiable()){
?>
	<p class="center">
	  <button type="submit">Enregistrer les modifications</button>
	</p>
<?php
  }
  if ($affiche_visu_saisie->get_peut_traiter()) {
?>
	<p class="center">
	  <button type="submit" name="creation_traitement" value="oui">Traiter la saisie</button>
	</p>
<?php
  }
?>

  </form>
<?php
  }
?>

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


