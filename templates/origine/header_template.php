<?php
/*
 * $Id$
*/
?>
 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<!-- <meta http-equiv="refresh" content="[tbs_refresh.tempsmax]; URL=[tbs_refresh.lien]/logout.php?auto=3&amp;debut_session=[tbs_refresh.debut]&amp;session_id=[tbs_refresh.id_session]" /> -->

	<!-- déclaration par défaut pour les scripts et les mises en page -->
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

	<title><?php echo "$titre_page : $tbs_gepiSchoolName" ?></title>
	
	
<!-- ================= Affichage du favicon =================== -->
	<link rel="SHORTCUT ICON" href="<?php echo $tbs_gepiPath?>/favicon.ico" />

<!-- Début des styles -->
	<?php
		if (count($tbs_CSS)) {
			foreach ($tbs_CSS as $value) {
				if ($value!="") {
					echo "<link rel=\"$value[rel]\" type=\"$value[type]\" href=\"$value[fichier]\" media=\"$value[media]\" />\n";
					echo "<link rel=\"$value[rel]\" type=\"$value[type]\" href=\"$value[fichier]\" media=\"$value[media] and (max-width: 800px)\" />\n";
				}
			}
			unset($value);
		}
		
		if (isset($CSS_smartphone)) {
	?>
			<link rel="stylesheet" type="text/css" href="<?php echo $gepiPath.'/'.$CSS_smartphone; ?>.css" media="screen and (max-width: 800px)" />
	<?php
		}
		
	?>
	
<!-- Fin des styles -->

<!-- Début des fichiers en javascript -->
	<!-- christian -->
	<script type="text/javascript">
		//<![CDATA[ 
		function ouvre_popup(url) {
				eval("window.open('/mod_miseajour/utilisateur/fenetre.php','fen','width=600,height=500,menubar=no,scrollbars=yes')");
				fen.focus();
			}
		//]]>
	</script>


	<script type="text/javascript" src="<?php echo $tbs_gepiPath ?>/lib/functions.js"></script>
	<?php
		if (count($tbs_librairies)) {
			foreach ($tbs_librairies as $value) {
				if ($value!="") {
					echo "<script type=\"text/javascript\" src=\"$value\"></script>\n";
				}
			}
			unset($value);
		}
	?>

	<!-- Variable passée à 'ok' en fin de page via le /lib/footer.inc.php -->
	<script type='text/javascript'>
		//<![CDATA[ 
			temporisation_chargement='n';
		//]]>
	</script>	
<!-- fin des fichiers en javascript -->


<?php
	if ($tbs_message_enregistrement!="") {
		echo "
			<script type='text/javascript'>
				//<![CDATA[ 
					alert(\"$tbs_message_enregistrement\");
				//]]>
			</script>
		";
	}

	//maintien_de_la_session();
?>
	<script type="text/javascript" src="<?php echo $tbs_gepiPath?>/lib/cookieClass.js"></script>
	<script type="text/javascript">
		//<![CDATA[ 
		function changement() {
			change = 'yes';
		}
		//]]>
	</script>

	<!-- Gestion de l'expiration des session - Patrick Duthilleul -->
	<script type="text/javascript">
		//<![CDATA[
		
			debut_alert = new Date()
			warn_msg1_already_displayed = false;
			warn_msg2_already_displayed = false;
			gepi_start_session = new Cookies();
			if (gepi_start_session.get('GEPI_start_session')) {
				gepi_start_session.clear('GEPI_start_session');
			}
			gepi_start_session.set('GEPI_start_session', debut_alert.getTime())
			/* =================================================
			 =
			 =
			 =
			 =================================================== */
			function display_alert(message) {
				if ($('alert_message')) {
					$('alert_message').update(message);

					if (Prototype.Browser.IE) {
						//document.documentElement.scroll = "no";
						//document.documentElement.style.overflow = 'hidden';
					}
					else {
						//document.body.scroll = "no";
						//document.body.style.overflow = 'hidden';				
					}					
					var viewport = document.viewport.getDimensions(); // Gets the viewport as an object literal
					var width = viewport.width; // Usable window width
					var height = viewport.height; // Usable window height
					if( typeof( window.pageYOffset ) == 'number' ) 
						{y = window.pageYOffset;}
					else if (typeof(document.documentElement.scrollTop) == 'number') {
						y=document.documentElement.scrollTop;
					}
					//$('alert_cache').setStyle({width: "100%"});
					//$('alert_cache').setStyle({height: height+"px"});
					//$('alert_cache').setStyle({top: y+"px"});
					//$('alert_cache').setStyle({display: 'block'});
					//$('alert_cache').setOpacity(0.5);
					play_footer_sound();
					$('alert_entete').setStyle({top: y+2+"px"});
					$('alert_entete').setStyle({left: Math.abs((width-640)/2)+"px"});
					$('alert_entete').setOpacity(1);
					$('alert_entete').setStyle({display: 'block'});
					$('alert_popup').setStyle({top: y+50+"px"});
					$('alert_popup').setStyle({left: Math.abs((width-640)/2)+"px"});
					$('alert_popup').setOpacity(1);
					$('alert_popup').setStyle({display: 'block'});
					$('alert_bouton_ok').observe('click', function(event) {
						$('alert_popup').setStyle({display: 'none'});	
						$('alert_cache').setStyle({display: 'none'});
						$('alert_entete').setStyle({display: 'none'});
						if (Prototype.Browser.IE) {
							//document.documentElement.scroll = "yes";
							//document.documentElement.style.overflow = 'scroll';
						}
						else {
							//document.body.scroll = "yes";
							//document.body.style.overflow = 'scroll';				
						}						
					
					});
					$('alert_popup').observe('mouseover', function(event) {
						//$('alert_entete').setOpacity(0.3);
						//$('alert_popup').setOpacity(0.3);						
					});
					$('alert_popup').observe('mouseout', function(event) {
						//$('alert_entete').setOpacity(1);
						//$('alert_popup').setOpacity(1);					
					});
					//$('alert_bouton_reload').observe('click', function(event) {
					//	location.reload(true); 				
					//
					//});	
				}
				else {
					alert(message);
				}
			
			}
			/* =================================================
			 =
			 =
			 =
			 =================================================== */			
			function show_message_deconnexion() {
				var seconds_before_alert = 180;
				var seconds_int_betweenn_2_msg = 30;

				<?php
					$sessionMaxLength=getSettingValue("sessionMaxLength");

					/*
					// Avec le dispositif maintien_de_la_session() de lib/share.inc.php pointant vers lib/echo.php, on devrait pouvoir ne tenir compte que de la variable Gepi: sessionMaxLength
					$session_gc_maxlifetime=ini_get("session.gc_maxlifetime");
					if($session_gc_maxlifetime!=FALSE) {
						$session_gc_maxlifetime_minutes=$session_gc_maxlifetime/60;

						if((getSettingValue("sessionMaxLength")!="")&&($session_gc_maxlifetime_minutes<getSettingValue("sessionMaxLength"))) {
							$sessionMaxLength=$session_gc_maxlifetime_minutes;
						}
					}
					*/
				?>

				if (gepi_start_session.get('GEPI_start_session')) {
					debut_alert.setTime(parseInt(gepi_start_session.get('GEPI_start_session'),10));
				}
				digital=new Date()
				seconds=(digital-debut_alert)/1000
				//if (1==1) {
				  if (seconds>=<?php echo $sessionMaxLength*60; ?>) {
				  	if (!warn_msg2_already_displayed) {
						var message = "vous avez été probablement déconnecté du serveur, votre travail ne pourra pas être enregistré dans gepi depuis cette page, merci de le sauvegarder dans un bloc note.";
						display_alert(message);				  
						warn_msg2_already_displayed = true;
					}
				  }
				else if (seconds><?php echo $sessionMaxLength*60; ?> - seconds_before_alert) {
					if (!warn_msg1_already_displayed) {
						var seconds_reste = Math.floor(<?php echo $sessionMaxLength*60; ?> - seconds);
						now=new Date()
						var hrs=now.getHours();
						var mins=now.getMinutes();
						var secs=now.getSeconds();

						var heure = hrs + " H " + mins + "' " + secs + "'' ";
						var message = "A "+ heure + ", il vous reste moins de 3 minutes avant d'être déconnecté ! \nPour éviter cela, rechargez cette page en ayant pris soin d'enregistrer votre travail !";
						display_alert(message);
						warn_msg1_already_displayed = true;
					}
				}

				setTimeout("show_message_deconnexion()",seconds_int_betweenn_2_msg*1000)
			}
		//]]>
	</script>





