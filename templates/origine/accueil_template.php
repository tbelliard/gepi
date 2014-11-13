<!DOCTYPE html>
<?php
/*
 * $Id$
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
<!-- templates/origine/accueil_menu_template.php  -->
<!-- templates/origine/bandeau_template.php      -->
<!-- ******************************************** -->

<!-- ************************* -->
<!-- Début du corps de la page -->
<!-- ************************* -->
<body onload="show_message_deconnexion();<?php if($tbs_charger_observeur) echo $tbs_charger_observeur;?>">


<!-- on inclut le bandeau -->
	<?php include('templates/origine/bandeau_template.php');?>

<!-- fin bandeau_template.html      -->

<div id='container'>

<a id='haut_de_page'></a>

<div class='fixeMilieuDroit'>
	<a href='#haut_de_page'><img src='images/up.png' width='18' height='18' alt="haut de la page" title="Remonter en haut de la page" /></a>
	<br />
	<a href='#bas_de_page'><img src='images/down.png' width='18' height='18' alt="bas de la page" title="Descendre en bas de la page" /></a>
</div>

<!-- droits dossiers -->

<?php
	if (count($afficheAccueil->message_admin)){
		foreach ($afficheAccueil->message_admin as $value) {
			if ($value != "") {
?>
	<p class="rouge center">
		<?php echo $value; ?>
	</p>
<?php
			}
		}
		unset ($value);
	}
?>

<!-- messages connections -->
	<div>

<!-- Connexions	-->
<?php
	if ($afficheAccueil->gere_connect==1) {
?>
	  <p>
		Nombre de personnes actuellement connectées :
		<?php
			if($afficheAccueil->nb_connect>1) {
				echo "<a style='font-weight:bold;' href='$afficheAccueil->nb_connect_lien' onmouseover=\"delais_afficher_div('personnes_connectees','y',-10,20,500,20,20);\" onclick=\"alterner_affichage_div('personnes_connectees','y',-10,20);return false;\">$afficheAccueil->nb_connect</a>";
			}
			else {
				echo "<b>".$afficheAccueil->nb_connect."</b>";
			}
		?>
		(
		<a href = 'gestion/gestion_connect.php?mode_navig=accueil'>
		  Gestion des connexions
		</a>
		)
	  </p>
<?php
	}
?>

<!-- Alertes sécurité -->
<?php
	if ($afficheAccueil->alert_sums>0) {
?>
	  <p>
		Alertes sécurité (niveaux cumulés) : <?php echo "<b>".$afficheAccueil->alert_sums."</b>"; ?> (
		<a href='gestion/security_panel.php'>Panneau de contrôle</a>)
	  </p>
<?php
	}
?>

<!-- Référencement	-->

<?php
	if (count($afficheAccueil->referencement)) {
	  foreach ($afficheAccueil->referencement as $value) {
?>
		<p class='referencement'>
		Votre établissement n'est pas référencé parmi les utilisateurs de Gepi.
		<span>
			<br />
			<a href="javascript:ouvre_popup_reference('<?php echo $value['lien'];?>')" title="<?php echo $value['titre'];?>">
				<?php echo $value['titre']; ?>
			</a>
		</span>
		</p>
<?php
	  }
	  unset($value);
	}
?>

<!-- messages de sécurité -->
<?php
	if (count($afficheAccueil->probleme_dir)) {
	
	  foreach ($afficheAccueil->probleme_dir as $value) {
?>
		<p  class="rouge center">
			<?php echo $value; ?>
		</p>

<?php
	  }
	  unset($value);
	}
?>
	
<!-- erreurs d'affectation d'élèves -->


	</div>
	<a id="contenu" class="invisible">Début de la page</a>

<!-- Signalements d'erreurs d'affectations -->
<?php
	if((isset($afficheAccueil->signalement))&&($afficheAccueil->signalement!="")) {
?>
	  <div class='infobulle_corps' style='text-align:center; margin: 3em; padding:0.5em; color:red; border: 1px dashed red;'>
		<?php echo $afficheAccueil->signalement; ?>
	  </div>

<?php
	}
?>

<!-- Actions à effectuer -->
<?php

	if((getSettingValue('active_cahiers_texte')=='y')&&(getSettingValue('GepiCahierTexteVersion')=='2')) {
        if(!file_exists("./temp/info_jours.js")) {
			creer_info_jours_js();
			if(!file_exists("./temp/info_jours.js")) {
                $sql="SELECT * FROM infos_actions WHERE titre='Fichier info_jours.js absent'";
                $test_info_jours = mysqli_query($mysqli, $sql);
                if($test_info_jours->num_rows == 0) {
                    enregistre_infos_actions("Fichier info_jours.js absent","Le fichier info_jours.js destiné à tenir compte des jours ouvrés dans les saisies du cahier de textes n'est pas renseigné.\nVous pouvez le renseigner en <a href='$gepiPath/edt_organisation/admin_horaire_ouverture.php?action=visualiser'>saisissant ou re-validant les horaires d'ouverture</a> de l'établissement.","administrateur",'statut');
                }
            }
        } else {
            $sql="SELECT * FROM infos_actions WHERE titre='Fichier info_jours.js absent'";
            $test_info_jours = mysqli_query($mysqli, $sql);
            if($test_info_jours->num_rows > 0) {
				while($lig_action=$test_info_jours->fetch_object($test_info_jours)) {
					del_info_action($lig_action->id);
				}
            }
        }
    }

	affiche_infos_actions();
?>

<!-- Accès CDT ouverts -->
<?php
	affiche_acces_cdt();
?>

<!-- messagerie -->
<?php
	if(in_array($_SESSION['statut'], array('professeur', 'cpe', 'scolarite', 'responsable', 'eleve'))) {
		//echo "<div align='center'>".afficher_les_evenements()."</div>";
		$liste_evenements=afficher_les_evenements();
	}

	if(($_SESSION['statut']=='professeur')&&(getSettingAOui('active_mod_abs_prof'))) {
		$message_remplacements_confirmes=affiche_remplacements_confirmes($_SESSION['login']);
		$message_remplacements_proposes=affiche_remplacements_en_attente_de_reponse($_SESSION['login']);
	}

	if((getSettingAOui('active_mod_abs_prof'))&&
		((($_SESSION['statut']=="administrateur")||
		(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AbsProfAttribuerRemplacementScol')))||
		(($_SESSION['statut']=="cpe")&&(getSettingAOui('AbsProfAttribuerRemplacementCpe')))))) {
		$message_remplacements_a_valider=test_reponses_favorables_propositions_remplacement();
	}

	if((getSettingAOui('active_mod_abs_prof'))&&
		($_SESSION['statut']=="eleve")) {
		$message_remplacements=affiche_remplacements_eleve($_SESSION['login']);
	}

	if((getSettingAOui('active_mod_abs_prof'))&&
		($_SESSION['statut']=="responsable")) {

		$message_remplacements="";
		$tab_eleves_en_responsabilite=get_enfants_from_resp_login($_SESSION['login'], 'avec_classe', "yy");
		for($loop=0;$loop<count($tab_eleves_en_responsabilite);$loop+=2) {
			$tmp_remplacements=affiche_remplacements_eleve($tab_eleves_en_responsabilite[$loop]);
			if($tmp_remplacements!="") {
				$message_remplacements.="<p class='bold'>".$tab_eleves_en_responsabilite[$loop+1]."</p>".$tmp_remplacements;
			}
		}
	}

	if ((count($afficheAccueil->message))||((isset($liste_evenements))&&($liste_evenements!=""))) :
?>

	<div class="panneau_affichage">
		<div class="panneau_liege">
			<?php if ($_SESSION['statut'] == "administrateur"): ?>
			<div style="position:absolute;width:30px;">
				<a href="./messagerie/index.php"><img src="./images/add_message.png" alt="Ajouter un message" title="Ajouter un message"/></a>
			</div> 
			<?php endif ?>
			<div class="panneau_coingh"></div>
			<div class="panneau_coindh"></div>
			<div class="panneau_haut"></div>
			<div class="panneau_droite"></div>
			<div class="panneau_gauche"></div>
			<div class="panneau_coingb"></div>
			<div class="panneau_coindb"></div>
			<div class="panneau_bas"></div>
			<div class="panneau_centre">
				<?php 
				if((isset($liste_evenements))&&($liste_evenements!="")) {
					echo "<div class='postit' title=\"Événements à venir (définis) pour vos classes.\">".$liste_evenements."</div>";
				}

				if(isset($message_remplacements_confirmes)) {
					echo $message_remplacements_confirmes;
				}

				if(isset($message_remplacements_proposes)) {
					echo $message_remplacements_proposes;
				}

				if(isset($message_remplacements_a_valider)) {
					echo $message_remplacements_a_valider;
				}

				if((isset($message_remplacements))&&($message_remplacements!="")) {
					echo $message_remplacements;
				}

				if (count($afficheAccueil->message)) :
					foreach ($afficheAccueil->message as $value) : 
				?>
				<div class="postit"><?php
					if(acces_messagerie($_SESSION['statut'])) {
						if((isset($value['statuts_destinataires']))&&($value['statuts_destinataires']!="_")) {
							echo "<div style='float:right; width:16' title=\"Éditer/modifier le message.\"><a href='$gepiPath/messagerie/index.php?id_mess=".$value['id']."'><img src='images/edit16.png' class='icone16' /></a></div>";
						}
					}
					echo $value['message'];
				?></div>
				<?php
				endforeach;
				endif;
				?>
				<?php unset ($value); ?>
			</div>
		</div>
	</div>
	<div style="clear:both;"></div>

	<?php endif; ?>

	<!-- <div id='messagerie'> -->

	<!--	</div> -->
<?php /* } */ ?>
	
<?php

	if ($_SESSION['statut'] =="professeur") {
?>
		<p class='bold'>
		  <a href='accueil_simpl_prof.php'>
			Interface graphique
		  </a>
		</p>
<?php
	}
?>

<!-- début corps menu	-->


	<!-- menu	général -->

	<?php
	if (count($afficheAccueil->titre_Menu)) {
	  foreach ($afficheAccueil->titre_Menu as $newEntreeMenu) {
      if ($newEntreeMenu->texte!='bloc_invisible') {
?>
		<h2 class="<?php echo $newEntreeMenu->classe ?>">
			<img src="<?php echo $newEntreeMenu->icone['chemin'] ?>" alt="<?php echo $newEntreeMenu->icone['alt'] ?>" /> - <?php echo $newEntreeMenu->texte ?>
		</h2>


<?php

		if ($newEntreeMenu->texte=="Votre flux RSS") {
?>
		  <div class='div_tableau'>
<?php
		  if ($afficheAccueil->canal_rss["mode"]==1) {
?>
			<h3 class="colonne ie_gauche flux_rss" title="A utiliser avec un lecteur de flux rss" onclick="changementDisplay('divuri', 'divexpli')" >
			  Votre uri pour les cahiers de textes
			</h3>
			<p class="colonne ie_droite vert">
			  <span id="divexpli" style="display: block;">
				<?php echo $afficheAccueil->canal_rss['expli']; ?>
			  </span>
			  <span id="divuri" style="display: none;">
			  <?php
				if(!isset($afficheAccueil->canal_rss_plus)) {
			  ?>
				<a href="<?php echo $afficheAccueil->canal_rss['lien']; ?>" onclick="window.open(this.href, '_blank'); return false;" >
				  <?php echo $afficheAccueil->canal_rss['texte']; ?>
				</a>
			  <?php
				}
				else {
					echo $afficheAccueil->canal_rss_plus;
				}
			  ?>
			  </span>
			</p>

<?php
		  }else if ($afficheAccueil->canal_rss["mode"]==2){
?>
			<h3 class="colonne ie_gauche">
			  Votre uri pour les cahiers de textes
			</h3>
			<p class="colonne ie_droite vert">
			  Veuillez la demander à l'administration de votre établissement.
			</p>
<?php
		  }
?>
		  </div>
<?php
		}else{
		  if (count($afficheAccueil->menu_item)) {
			foreach ($afficheAccueil->menu_item as $newentree) {
			  if ($newentree->indexMenu==$newEntreeMenu->indexMenu) {
?>
				<div class='div_tableau'>

<?php
				  if ($newentree->titre=="Sauvegarde de la base") {
?>
	<div class="div_tableau cellule_1">
		<form enctype="multipart/form-data" action="gestion/accueil_sauve.php" method="post" id="formulaire" >
			<p>
				<?php
					echo add_token_field();
				?>
				<input type='hidden' name='action' value='system_dump' />
				<input type="submit" value="Lancer une sauvegarde de la base de données" />
			</p>
		</form>
		<p class='small'>
			Les répertoires "documents" (<em>contenant les documents joints aux cahiers de texte</em>) et "photos" (<em>contenant les photos du trombinoscope</em>) ne seront pas sauvegardés.<br />
			Un outil de sauvegarde spécifique se trouve en bas de la page <a href='./gestion/accueil_sauve.php#zip'>gestion des sauvegardes</a>.
		</p>
	</div>
<?php
			  }else{
?>

				  <h3 class="colonne ie_gauche">
					  <a href="<?php echo mb_substr($newentree->chemin,1) ?>">
						  <?php echo $newentree->titre ?>
					  </a>
				  </h3>
				  <p class="colonne ie_droite">
					  <?php echo $newentree->expli ?>
				  </p>
<?php
			  }
?>
				</div>
<?php
			  }
			}
			}
			unset($newentree);
		  }
		}
	  }
	  unset($newEntreeMenu);
	}
    
?>

<!-- début RSS	-->
		
<!-- fin RSS	-->

<!-- Début du pied -->
	<div id='EmSize' style='visibility:hidden; position:absolute; left:1em; top:1em;'></div>

	<script type='text/javascript'>
		var ele=document.getElementById('EmSize');
		var em2px=ele.offsetLeft
		//alert('1em == '+em2px+'px');
	</script>


<?php
	//if (count($tbs_nom_connecte)) {
	if (count($afficheAccueil->nom_connecte)) {
		//echo "
?>
	<div id='personnes_connectees' class='infobulle_corps' style='color: #000000; border: 1px solid #000000; padding: 0px; position: absolute; z-index:1; width: 35em; left:0em;'>
		<div class='infobulle_entete' style='color: #ffffff; cursor: move; font-weight: bold; padding: 0px; width: 35em;' onmousedown="dragStart(event, 'personnes_connectees')">
			<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>
				<a href='#' onclick="cacher_div('personnes_connectees');return false;">
					<img src='./images/icons/close16.png' width='16' height='16' alt='Fermer' />
				</a>
			</div>
			<span style="padding-left: 1px;">
				Personnes connectées
			</span>
		</div>
		<div>
			<div style="padding-left: 1px;">
				<div style="text-align:center;">
					<table class='boireaus boireaus_alt sortable resizable' style='margin: .5em auto;'>
						<tr>
							<th class='text'>Personne</th>
							<th class='text'>Statut</th>
							<th class='text'>Fin session</th>
						</tr>
<?php
		/*
		// A REVOIR: Pour pouvoir grouper les connexions multiples d'un même utilisateur
		$tab_personne_connectee=array();
		foreach ($afficheAccueil->nom_connecte as $newentree) {


		}
		*/

		foreach ($afficheAccueil->nom_connecte as $newentree) {
?>
						<!--tr class='<?php echo $newentree['style']; ?>'-->
						<tr>
							<td>
								<?php
									if((getSettingAOui('active_mod_alerte'))&&(in_array($newentree['statut'], array("administrateur", "scolarite", "cpe", "professeur", "secours", "autre")))) {
										if(check_mae($_SESSION['login'])) {
											echo "<div style='float:right; width:16px;'><a href='./mod_alerte/form_message.php?message_envoye=y&amp;login_dest=".$newentree['login'].add_token_in_url()."' title=\"Déposer un message d'alerte/information à destination de ".$newentree['texte']." .\" target='_blank'><img src='./images/icons/mail.png' width='16' height='16' alt='courriel' /></a></div>";
										}
									}

									if(($newentree['courriel']!="")&&(check_mail($newentree['courriel']))) {
										echo "<a href='mailto:".$newentree['courriel']."' title='Envoyer un mail'>";
										echo $newentree['texte'];
										echo "</a>";
									}
									else {
										echo $newentree['texte'];
									}
								?>
							</td>
							<td>
								<?php
									if(isset($newentree['login'])) {
										if($newentree['statut']=='responsable') {
											if(isset($newentree['pers_id'])) { ?>
												<a href='<?php echo $gepiPath; ?>/responsables/modify_resp.php?pers_id=<?php echo $newentree['pers_id']; ?>' title='Accéder à la fiche du responsable'>
												<?php echo $newentree['statut']; ?>
												</a>
								<?php
											}
											else {
												echo $newentree['statut'];
											}
										}
										elseif($newentree['statut']=='eleve') { ?>
											<a href='<?php echo $gepiPath; ?>/eleves/modify_eleve.php?eleve_login=<?php echo $newentree['login']; ?>' title="Accéder à la fiche de l'élève">
											<?php echo $newentree['statut']; ?>
											</a>
								<?php
										}
										else { ?>
											<a href='<?php echo $gepiPath; ?>/utilisateurs/modify_user.php?user_login=<?php echo $newentree['login']; ?>' title="Accéder à la fiche de l'utilisateur">
											<?php echo $newentree['statut']; ?>
											</a>
								<?php
										}
									}
									else {
										echo $newentree['statut'];
									}
								?>
							</td>
							<td>
								<?php
									$date_fin_session=formate_date($newentree['end'], 'y');
									echo "<span title=\"La session démarrée le ".formate_date($newentree['start'], 'y')." depuis ".$newentree['remote_addr']." devrait se terminer d'elle-même, si l'utilisateur n'agit plus, le ".$date_fin_session.".\">".$date_fin_session."</span>";
								?>
							</td>
						</tr>

<?php
		}
		unset($newentree);
?>

					</table>
				</div>
			</div>
		</div>
	</div>
<?php
//   			";
	}
?>

	<script type='text/javascript'>
		temporisation_chargement='ok';
	</script>

	<script type='text/javascript'>
	cacher_div('personnes_connectees');
	</script>


<a id='bas_de_page'></a>
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

<!-- Alarme sonore -->
<?php
	echo joueAlarme();
?>
<!-- Fin alarme sonore -->

<div id="alert_cache" style="z-index:2000;
							display:none;
							position:absolute;
							top:0px;
							left:0px;
							background-color:#000000;
							width:200px;
							height:200px;"> &nbsp;</div>
<div id="alert_entete" style="z-index:2000;
								display:none;
								position:absolute;"><img   src="./images/alerte_entete.png" alt="alerte" /></div>
<div id="alert_popup" style="z-index:2000;
								text-align:justify;
								width:600px;
								height:130px;
								border:1px solid black;
								background-color:white;
								padding-top:10px;
								padding-left:20px;
								padding-right:20px;
								display:none;
								position:absolute;
								background-image:url('./images/degrade_noir.png');
								background-repeat:repeat-x;
								background-position: left bottom;">
	<div id="alert_message"></div>
	<div id="alert_button" style="margin:5px auto;width:90px;">
		<div id="alert_bouton_ok" style="float:left;"><img src="./images/bouton_continue.png" alt="ok" /></div>
	</div>
</div>


</body>
</html>

