<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id: accueil_template.php $
*/
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<!-- on inclut l'entête -->
	<?php include('templates/origine/header_template.php');?>
	
	<link rel="stylesheet" type="text/css" href="./templates/origine/css/accueil.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="./templates/origine/css/bandeau.css" media="screen" />
	
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
	
	
<!-- Fin des styles -->


</head>

<!-- ******************************************** -->
<!-- Appelle les sous-modèles                     -->
<!-- templates/origine/header_template.php        -->
<!-- templates/origine/accueil_menu_template.php  -->
<!-- templates/origine/bandeau_template.php      -->
<!-- ******************************************** -->

<!-- ************************* -->
<!-- Début du corps de la page -->
<!-- ************************* -->
<body onload="show_message_deconnexion(); <?php echo $tbs_charger_observeur;?>">

<!-- on inclut le bandeau -->
	<?php include('templates/origine/bandeau_template.php');?>
	
<!-- fin bandeau_template.html      -->

<div id='container'>
	
<!-- droits dossiers -->
			
<?php
	if (count($tbs_message_admin)) {
		$message_admin = array_values($tbs_message_admin);
		if ("$message_admin[0]"!="") {
			foreach ($message_admin as $value) {
				if ("$value"!="") {
					echo "
<p class=\"rouge center\">
	$value
</p>
";
				}
			}
		}
	}
?>

<!-- messages connections -->
	<div>		

<!-- Connexions	-->			
<?php
	if ($tbs_gere_connect==1) {
		echo "
		<p>
			Nombre de personnes actuellement connectées : 
			<a href='$tbs_nb_connect_lien' onmouseover=\"delais_afficher_div('personnes_connectees','y',-10,20,500,20,20);\">
				$tbs_nb_connect
			</a>
			(
			<a href = 'gestion/gestion_connect.php?mode_navig=accueil'>
				Gestion des connexions		
			</a>
			)
		</p>
		";
	}
?>	

<!-- Alertes sécurités	-->		
<?php
	if ($tbs_alert_sums>0) {
		echo "
		<p>
			Alertes sécurité (niveaux cumulés) : $tbs_alert_sums (
			<a href='gestion/security_panel.php'>Panneau de contrôle</a>)
		</p>
		";
	}
?>	

<!-- Référencement	-->

		<?php		
			if (count($tbs_referencement)) {
				$referencement = array_values($tbs_referencement);
				if ($referencement[0]['texte']!="") {
					foreach ($referencement as $value) {
						echo "
		<p class='referencement'>
		Votre établissement n'est pas référencé parmi les utilisateurs de Gepi.
		<span>
			<br />
			<a href=\"javascript:ouvre_popup_reference('$value[lien]')\" title='$value[titre]'>
				$value[titre]
			</a>
		</span>
		</p>
					";
					}
				}
			}
		?>	

<!-- messages de sécurité -->
		<?php		
			if (count($tbs_probleme_dir)) {
			$probleme_dir=array_values($tbs_probleme_dir);
				if ("$probleme_dir[0]"!="") {
					foreach ($probleme_dir as $value) {
			echo "
		<p  class=\"rouge center\">
			$value
		</p>

			";
					}
				}
			}
		?>	
				
</div>
<a name="contenu" class="invisible">Début de la page</a>	

<!-- messagerie -->	
		<?php	
			if (count($tbs_message)) {
				$message=array_values($tbs_message);
				if ($message[0]['message']!="") {
						echo "
	<div id='messagerie'>
						";
					foreach ($message as $value) {
						echo "
		<div>	
						";
						if ($value['suite']=='') {
							echo "";
						}else{
							echo "<hr>";
						}
						echo "
		$value[message]
						";
						if ($value['suite']=='') {
							echo "";
						}else{
							echo "</hr>";
						}
						echo "
		</div>
						";
					}
						echo "
	</div>
						";
				}
			}
		?>	
	

		<?php
			if ($tbs_statut_utilisateur=="professeur") {	
				echo "
	<p class='bold'>
		<a href='accueil_simpl_prof.php'>Interface graphique</a>
	</p>
				";
			}
		?>	

<!-- début corps menu	-->
	
	<!-- menu	général -->
	
		<?php		
			if (count($tbs_menu)) {
				$menu=array_values($tbs_menu);
				if ($menu[0]['texte']!="") {
					foreach ($tbs_menu as $value) {
						echo "
	<h2 class='$value[classe]' style='margin-bottom:0;'> 
		<img src='$value[image]' alt='' /> - $value[texte]
	</h2>
				";
						if ($value['texte']=="Administration") {
							echo "
<!-- sauvegarde -->	
	<div class=\"div_tableau cellule_1\">
		<form enctype=\"multipart/form-data\" action=\"gestion/accueil_sauve.php\" method=\"post\" id=\"formulaire\" >
			<p>
				<input type='hidden' name='action' value='system_dump' />
				<input type=\"submit\" value=\"Lancer une sauvegarde de la base de données\" />
			</p>
		</form>
		<p class='small'>
			Les répertoires \"documents\" (<em>contenant les documents joints aux cahiers de texte</em>) et \"photos\" (<em>contenant les photos du trombinoscope</em>) ne seront pas sauvegardés.<br />
			Un outil de sauvegarde spécifique se trouve en bas de la page <a href='./gestion/accueil_sauve.php#zip'>gestion des sauvegardes</a>.
		</p>
	</div>	
				";
						}
							echo "
<!-- autres menus -->		
<!-- accueil_menu_template.php -->
				";
						if (count($value['entree'])) {
							foreach ($value['entree'] as $newentree) {
								include('./templates/origine/accueil_menu_template.php');
							}
						}
							echo "
<!-- Fin menu	général -->
				";
					}
				}
			}
		?>	
	

<!-- début RSS	-->	
		<?php		

	
			if ($tbs_canal_rss_flux==1) {
							echo "
	<div>
		<h2 class='accueil'>
			<img src='./images/icons/rss.png' alt=''/> - Votre flux rss			
		</h2>
				";
/*
		<table class='menu' summary="Tableau des flux RSS. Colonne de gauche : lien vers les pages, colonne de droite : rapide description">
			<tr>
				<td class="menu_gauche" title="A utiliser avec un lecteur de flux rss" style="cursor: pointer; color: blue;" onclick="changementDisplay('divuri', 'divexpli');">
				[tbs_canal_rss;block=tr; when [tbs_canal_rss.mode]=1]
					Votre uri pour les cahiers de textes
				</td>
				<td style="color:green">
					<div id="divuri" style="display: none;">
						<a onclick="window.open(this.href, '_blank'); return false;" href="[tbs_canal_rss.lien]">
							[tbs_canal_rss.texte]
						</a>
					</div>
					<div id="divexpli" style="display: block;">
						[tbs_canal_rss.expli]
					</div>
				</td>
			</tr>
			<tr>
				<td class="menu_gauche">
					[tbs_canal_rss;block=(tr); when [tbs_canal_rss.mode]=2]
					Votre uri pour les cahiers de textes
				</td>
				<td class="vert">
					Veuillez la demander à l'administration de votre établissement.
				</td>
			</tr>
		</table>
*/
		echo "
<div class='div_tableau'>
			";
		if ($tbs_canal_rss[0]["mode"]==1) {
			echo "
	<h3 class=\"colonne ie_gauche flux_rss\" title=\"A utiliser avec un lecteur de flux rss\" onclick=\"changementDisplay('divuri', 'divexpli')\" >
		Votre uri pour les cahiers de textes
	</h3>
	<p class=\"colonne ie_droite vert\">
		<span id=\"divexpli\" style=\"display: block;\">
				";
				echo $tbs_canal_rss[0]['expli'];
				echo "
		</span>
		<span id=\"divuri\" style=\"display: none;\">
	<a onclick=\"window.open(this.href, '_blank'); return false;\" href=\""; echo $tbs_canal_rss[0]['lien']; echo";\">
							"; echo $tbs_canal_rss[0]['texte']; echo"
	</a>
		</span>
	</p>
	
</div>
	</div>
				";
		}else if ($tbs_canal_rss[0]["mode"]==2){
			echo "
	<h3 class=\"colonne ie_gauche\">
			Votre uri pour les cahiers de textes
	</h3>
	<p class=\"colonne ie_droite vert\">
					Veuillez la demander à l'administration de votre établissement.
	</p>
				";
		}
	}
?>	
<!-- fin RSS	-->
					
<!-- Début du pied -->
	<div id='EmSize' style='visibility:hidden; position:absolute; left:1em; top:1em;'></div>

	<script type='text/javascript'>
		var ele=document.getElementById('EmSize');
		var em2px=ele.offsetLeft
		//alert('1em == '+em2px+'px');
	</script>


<?php
	if (count($tbs_nom_connecte)) {
		echo "
	<div id='personnes_connectees' class='infobulle_corps' style='color: #000000; border: 1px solid #000000; padding: 0px; position: absolute; z-index:1; width: 20em; left:0em;'>
		<div class='infobulle_entete' style='color: #ffffff; cursor: move; font-weight: bold; padding: 0px; width: 20em;' onmousedown=\"dragStart(event, 'personnes_connectees')\">
			<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>
				<a href='#' onclick=\"cacher_div('personnes_connectees');return false;\">
					<img src='./images/icons/close16.png' width='16' height='16' alt='Fermer' />
				</a>
			</div>
			<span style=\"padding-left: 1px;\">
				Personnes connectées
			</span>
		</div>
		<div>
			<div style=\"padding-left: 1px;\">
				<div style=\"text-align:center;\">	
					<table class='boireaus'>
						<tr>
							<th>Personne</th>
							<th>Statut</th>
						</tr>
   			";

		foreach ($tbs_nom_connecte as $newentree) {
		echo "
						<tr  class='$newentree[style]'>
							<td>
								<a href='mailto:$newentree[courriel]'>
									$newentree[texte]
								</a>
							</td>
							<td>
								$newentree[statut]
							</td>
						</tr>
   			";
		}
		echo "
						
					</table>
				</div>
			</div>
		</div>
	</div>
   			";
	}
?>

	<script type='text/javascript'>
		temporisation_chargement='ok';
	</script>

	<script type='text/javascript'>
	cacher_div('personnes_connectees');
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

