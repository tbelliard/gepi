/**
 * Fonction qui récupère l'appréciation d'un textarea précis pour le sauvegarder
 **/
function ajaxAppreciations(eleveperiode, enseignement, textId){
	var csrf_alea=document.getElementById('csrf_alea').value;

	var essai = $(textId);
	// On récupère le contenu du textarea dont l'id est textId
	var contenu = $F(textId);
	// On définit le nom du fichier qui va traiter la requête
	var url = "ajax_appreciations.php";
	o_options = new Object();
	o_options = {postBody: 'var1='+eleveperiode+'&var2='+enseignement+'&var3='+contenu+'&csrf_alea='+csrf_alea};

	// On construit la requête ajax
	//var laRequete = new Ajax.Request(url,o_options);
	// Il faudra envisager d'utiliser Ajax.Updater pour renvoyer une phrase de confirmation
	//  ou alors résupérer un retour par Ajax.Request avec onSuccess ou onFailure
	//alert(enseignement+' \n'+eleveperiode+' \n'+textId+' \n Essai = ' +essai+' \nContenu = '+contenu);

	mise_en_exergue_textarea_vide(textId);

	document.getElementById('div_verif_'+textId).innerHTML="<img src='../images/spinner.gif' class='icone16' alt='Enregistrement temp...' title='Enregistrement de l appréciation dans une table temporaire.' />";
	new Ajax.Updater($('div_verif_'+textId),url,o_options);
}

function ajaxVerifAppreciations(eleveperiode, enseignement, textId){
	var csrf_alea=document.getElementById('csrf_alea').value;


	var essai = $(textId);
	// On récupère le contenu du textarea dont l'id est textId
	var contenu = $F(textId);
	// On définit le nom du fichier qui va traiter la requête
	var url = "../saisie/ajax_appreciations.php";
	o_options = new Object();
	o_options = {postBody: 'mode=verif&var1='+eleveperiode+'&var2='+enseignement+'&var3='+contenu+'&csrf_alea='+csrf_alea};
	// On construit la requête ajax
	//var laRequete = new Ajax.Request(url,o_options);
	new Ajax.Updater($('div_verif_'+textId),url,o_options);


	// Il faudra envisager d'utiliser Ajax.Updater pour renvoyer une phrase de confirmation
	//  ou alors résupérer un retour par Ajax.Request avec onSuccess ou onFailure
	//alert(enseignement+' \n'+eleveperiode+' \n'+textId+' \n Essai = ' +essai+' \nContenu = '+contenu);
}

function ajaxVerifAvis(eleveperiode, id_classe, textId){
	var csrf_alea=document.getElementById('csrf_alea').value;

	//alert('plop');

	var essai = $(textId);
	// On récupère le contenu du textarea dont l'id est textId
	var contenu = $F(textId);
	//alert(contenu);

	// On définit le nom du fichier qui va traiter la requête
	var url = "../saisie/ajax_appreciations.php";
	o_options = new Object();
	o_options = {postBody: 'mode=verif_avis&var1='+eleveperiode+'&var2='+id_classe+'&var3='+contenu+'&csrf_alea='+csrf_alea};
	// On construit la requête ajax
	//var laRequete = new Ajax.Request(url,o_options);
	new Ajax.Updater($('div_verif_'+textId),url,o_options);


	// Il faudra envisager d'utiliser Ajax.Updater pour renvoyer une phrase de confirmation
	//  ou alors résupérer un retour par Ajax.Request avec onSuccess ou onFailure
	//alert(enseignement+' \n'+eleveperiode+' \n'+textId+' \n Essai = ' +essai+' \nContenu = '+contenu);
}


function ajaxVerifAppAid(eleveperiode, id_aid, textId){
	var csrf_alea=document.getElementById('csrf_alea').value;

	var essai = $(textId);
	// On récupère le contenu du textarea dont l'id est textId
	var contenu = $F(textId);
	// On définit le nom du fichier qui va traiter la requête
	var url = "../saisie/ajax_appreciations.php";
	o_options = new Object();
	o_options = {postBody: 'mode=verif_aid&var1='+eleveperiode+'&var2='+id_aid+'&var3='+contenu+'&csrf_alea='+csrf_alea};
	// On construit la requête ajax
	//var laRequete = new Ajax.Request(url,o_options);
	new Ajax.Updater($('div_verif_'+textId),url,o_options);

	// Il faudra envisager d'utiliser Ajax.Updater pour renvoyer une phrase de confirmation
	//  ou alors résupérer un retour par Ajax.Request avec onSuccess ou onFailure
	//alert(enseignement+' \n'+eleveperiode+' \n'+textId+' \n Essai = ' +essai+' \nContenu = '+contenu);
}

/*
function verifAppVide(textId) {
	//alert('plip');

	var essai = $(textId);
	// On récupère le contenu du textarea dont l'id est textId
	var contenu = $F(textId);

	if(contenu=='') {
		document.getElementById(textId).title="L'appréciation ne devrait pas être vide sauf si cet élève n'est pas affecté dans votre enseignement. Vous devriez alors signaler l'erreur d'inscription à l'administrateur.";
		document.getElementById(textId).style.borderWidth='10px';
		document.getElementById(textId).style.borderStyle='solid';
		document.getElementById(textId).style.borderColor='red';
		//alert('plop');
	}
	else {
		document.getElementById(textId).title="";
		//document.getElementById(textId).style.border='';
		document.getElementById(textId).style.borderWidth='0px';
		document.getElementById(textId).style.borderStyle='solid';
		document.getElementById(textId).style.borderColor='';
	}
}
*/
