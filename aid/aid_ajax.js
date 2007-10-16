function listeEleve(id_eleve) {
	var xmlhttp;
		// On cherche à créer un objet XMLHttpRequest pour le traitement en ajax
			if(window.XMLHttpRequest) // Firefox
				xmlhttp = new XMLHttpRequest();
			else if(window.ActiveXObject) // Internet Explorer
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			else { // XMLHttpRequest non supporté par le navigateur
				alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
				return;
			}

	xmlhttp.open("POST", "aid_ajax_eleves.php", true);

	xmlhttp.onreadystatechange = function() {
		try {
			if (xmlhttp.readyState == 4) {
				var resultat = xmlhttp.responseText;
				aid_droite.innerHTML = resultat;
			}
		} catch (erreur) {}
	}

	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	var data = "id_eleve="+id_eleve;
	xmlhttp.send(data);
}