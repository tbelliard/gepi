<?php
/*
 * $Id$
*/
?>

<!-- ************************* -->
<!-- Début du corps de la page -->
<!-- ************************* -->

<!-- Début bandeau -->
<!-- Initialisation du bandeau à la bonne couleur -->
	<div id='bandeau' class="<?php echo $tbs_modif_bandeau.' '.$tbs_degrade_entete.' '.$tbs_modif_bandeau.'_'.$tbs_degrade_entete; ?>">
	
<a href="#contenu" class="invisible"> Aller au contenu </a>
		
	<!-- Page title, access rights -->
	<!-- User name, status, main matter, home, logout, account management -->

<div class="bandeau_colonne">
	<!-- Bouton rétrécir le bandeau -->
		<a class='change_taille_gd' href="#" onclick="modifier_taille_bandeau();change_mode_header('y', '<?php echo $tbs_bouton_taille;?>');return false;">
			<img src="<?php echo $tbs_bouton_taille;?>/images/up.png" alt='Cacher le bandeau' title='Cacher le bandeau' />
		</a>
	<!-- Bouton agrandir le bandeau -->
		<a class='change_taille_pt' href="#" onclick="modifier_taille_bandeau();change_mode_header('n', '<?php echo $tbs_bouton_taille;?>');return false;">
			<img src="<?php echo $tbs_bouton_taille;?>/images/down.png" alt='Afficher le bandeau' title='Afficher le bandeau' />
		</a>

		<?php
			//=====================================
			if($tbs_afficher_temoin_filtrage_html=='y') {
				if($filtrage_html=='htmlpurifier') {
					echo " <img src='$gepiPath/images/bulle_verte.png' width='9' height='9' alt='Filtrage HTML avec HTMLPurifier' title='Filtrage HTML avec HTMLPurifier' />\n";
				}
				elseif($filtrage_html=='inputfilter') {
					echo " <img src='$gepiPath/images/bulle_bleue.png' width='9' height='9' alt='Filtrage HTML avec InputFilter' title='Filtrage HTML avec InputFilter' />\n";
				}
				else {
					echo " <img src='$gepiPath/images/bulle_rouge.png' width='9' height='9' alt='Pas de filtrage HTML' title='Pas de filtrage HTML' />\n";
				}
			}
			//=====================================
		?>

	<!-- titre de la page -->	
		<h1><?php echo $titre_page; ?></h1>
		
	<!-- Dernière connexion -->
		<!-- <p id='dern_connect'> -->
		<?php
			if ($tbs_last_connection!=""){
				echo "
				<p class='colonne1'>
					$tbs_last_connection
				</p>
				";
			}
		?>
		
	<!-- numéro de version	 -->
		<p class="rouge">
			<?php echo $tbs_version_gepi; ?>
		</p>
</div>

<div class="bandeau_colonne" id="bd_colonne_droite">
	<!-- Nom prénom -->
		<p id='bd_nom'>
			<?php echo $tbs_nom_prenom; ?>
		</p>
	
	<!-- statut utilisateur -->
		<?php
			if (count($tbs_statut)) {
				foreach ($tbs_statut as $value) {	
					echo "
	<p>
		<span class='$value[classe]'>
			$value[texte]
					";
					if (count($donnees_enfant)) {
						foreach ($donnees_enfant as $value2) {	
							echo "
				
						$value2[nom] (<em>$value2[classe]</em>)
							";
						}
					}
					echo "
		</span>
	</p> 
					";
				}
			}
		?>
	
	<!-- On vérifie si le module de mise à jour est activé -->
		
		<?php
			if ($tbs_mise_a_jour !="") {
				echo "
	<a href='javascript:ouvre_popup()'>
		<img style='border: 0px; width: 15px; height: 15px;' src='$tbs_mise_a_jour/images/info.png' alt='info' title='info' align='top' />
	</a>
				";
			}
		?>
	
	
	<!-- 	christian -->
	<!-- 	menus de droite -->
	<!-- 	menu accueil -->
	<!-- <ol id='premier_menu'> -->
	<ol>
		<?php
			if (count($tbs_premier_menu)) {
				foreach ($tbs_premier_menu as $value) {
					if ("$value[texte]"!="") {
						echo "
	<li class='ligne_premier_menu'>
		<a href='$value[lien]'>
			<img src='$value[image]' alt='$value[alt]' title='$value[title]' />
			<span class='menu_bandeau'>
				&nbsp;$value[texte]
			</span>
		</a>
	</li>
						";
					}
				}
			}
		?>
	</ol>
		
	<!-- sépare les 2 menus -->
		<!-- <div class='spacer'> </div> -->
	
	<!-- menu contact	 -->
		<!-- <ol id='deux_menu'> -->
		<ol id="bandeau_menu_deux">
		<?php
			if (count($tbs_deux_menu)) {
				foreach ($tbs_deux_menu as $value) {
					if ("$value[texte]"!="") {
						echo "
	<li class='ligne_deux_menu'>
		<a href='$value[lien]' $value[onclick] title=\"Nouvelle fenêtre\">
			$value[texte]
		</a>
	</li>
						";
					}
				}
			}
		?>
		</ol>
	
</div>		

</div>
	
		
	
<!-- menu prof -->
		<?php
			if (count($tbs_menu_prof)) {
				$menu_prof = array_values($tbs_menu_prof);
				if ("$menu_prof[0][texte]"!="") {
					echo "
	<ol id='essaiMenu'>
		<li>
			<a href='$tbs_gepiPath/accueil.php'>&nbsp;Accueil</a>
		</li>
					";
							foreach ($tbs_menu_prof as $value) {
								if ("$value[texte]"!="") {
						echo "
		<li>
			<a href='".$tbs_gepiPath."$value[lien]'>&nbsp;$value[texte]</a>
		</li>
									";
								}
							}
					echo "
		<li>
			<a href='$tbs_gepiPath/utilisateurs/mon_compte.php'>&nbsp;Mon compte</a>
		</li>
	</ol>	
					";
				}
			}
		?>
<!-- fil d'ariane -->
<?php
  if (isset($messageEnregistrer) && $messageEnregistrer !="" ){
	affiche_ariane(TRUE,$messageEnregistrer);
  }else{
	if(isset($_SESSION['ariane']) && (count($_SESSION['ariane']['lien'])>1)){
	  affiche_ariane();
	}
  }
?>
<!-- fin fil d'ariane -->
	
	
<!-- message -->
<?php
			if ($tbs_msg !="") {
?>
	<p class='headerMessage bold<?php if($post_reussi) echo " vert" ;?>'>
<?php
		echo $tbs_msg;
?>

	</p>
<?php
			}
?>
