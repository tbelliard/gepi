<?php
/*
 * $Id$
*/
?>
 
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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


<!-- Début des fichiers en javascript -->

<?php
	if ($tbs_message_enregistrement!="") {
		echo "
			<script type='text/javascript'>
				//<![CDATA[ 
					alert($tbs_message_enregistrement);
				//]]>
			</script>
		";
	}
?>

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
			var debut=new Date()
			function show_message_deconnexion() {
				var seconds_before_alert = 180;
				var seconds_int_betweenn_2_msg = 30;

				var digital=new Date()
				var seconds=(digital-debut)/1000
				if (seconds>1800 - seconds_before_alert) {
					var seconds_reste = Math.floor(1800 - seconds);
					now=new Date()
					var hrs=now.getHours();
					var mins=now.getMinutes();
					var secs=now.getSeconds();

					var heure = hrs + " H " + mins + "' " + secs + "'' ";
					alert("A "+ heure + ", il vous reste moins de 3 minutes avant d'être déconnecté ! \nPour éviter cela, rechargez cette page en ayant pris soin d'enregistrer votre travail !");
				}
				setTimeout("show_message_deconnexion()",seconds_int_betweenn_2_msg*1000)
			}
		//]]>
	</script>

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
		}
	?>

	<!-- Variable passée à 'ok' en fin de page via le /lib/footer.inc.php -->
	<script type='text/javascript'>
		//<![CDATA[ 
			temporisation_chargement='n';
		//]]>
	</script>	
<!-- fin des fichiers en javascript -->

<!-- Début des styles -->
	<?php
		if (count($tbs_CSS)) {
			foreach ($tbs_CSS as $value) {
				if ($value!="") {
					echo "<link rel=\"$value[rel]\" type=\"$value[type]\" href=\"$value[fichier]\" media=\"$value[media]\" />\n";
		// [tbs_CSS.title;att=title]
				}
			}
		}
	?>
	
<!-- Fin des styles -->



