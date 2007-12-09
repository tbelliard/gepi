/**
 * Fonction qui récupère l'appréciation d'un textarea précis pour le sauvegarder
 **/
function ajaxAppreciations(eleveperiode, enseignement, textId){
	var essai = $(textId);
	// On récupère le contenu du textarea dont l'id est textId
	var contenu = $F(textId);
	// On définit le nom du fichier qui va traiter la requête
	var url = "ajax_appreciations.php";
	o_options = new Object();
	o_options = {postBody: 'var1='+eleveperiode+'&var2='+enseignement+'&var3='+contenu};
	// On construit la requête ajax
	var laRequete = new Ajax.Request(url,o_options);
	// Il faudra envisager d'utiliser Ajax.Updater pour renvoyer une phrase de confirmation
	//  ou alors résupérer un retour par Ajax.Request avec onSuccess ou onFailure
	//alert(enseignement+' \n'+eleveperiode+' \n'+textId+' \n Essai = ' +essai+' \nContenu = '+contenu);
}