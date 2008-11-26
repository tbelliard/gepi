function gestionaffAbs(id, reglage, url){
	elementHTML = $(id);
	var info = $F(reglage);
	o_options = new Object();
	o_options = {postBody: '_id='+info+'&type='+reglage, onComplete:afficherDiv(id)};
	var laRequete = new Ajax.Updater(elementHTML,url,o_options);
}
function afficherDiv(id){
  Element.show(id);
}
function utiliseAjaxAbs(id, reglage, url){
	elementHTML = $(id);
	var info = reglage;
	o_options = new Object();
	o_options = {postBody: '_id='+info+'&type='+reglage, onComplete:afficherDiv(id)};
	var laRequete = new Ajax.Updater(elementHTML,url,o_options);
}