<?php
if(getSettingAOui("cookie_afficher_acceptation")) {
	$cookie_url_explication=getSettingValue("cookie_url_explication");
	if($cookie_url_explication=="") {
		$cookie_url_explication="$gepiPath/gestion/info_vie_privee.php#cookies";
	}
	echo "
	<script type='text/javascript' src='$gepiPath/lib/cookiechoices.js'></script>
	<script type='text/javascript'>
		document.addEventListener('DOMContentLoaded',function(event) {cookieChoices.showCookieConsentBar(\"En poursuivant votre navigation, vous acceptez l’utilisation des cookies indispensables au fonctionnement de ce site.\", \"J’accepte\", \"En savoir plus\", \"$cookie_url_explication\");});
	</script>";
}
?>
