function gestionaffAbs(id, reglage){
	elementHTML = $(id);
	var info = $F(reglage);
	var url = "saisir_ajax.php";
	o_options = new Object();
	o_options = {postBody: '_id='+info+'&type='+reglage, onComplete:afficherDiv(id)};
	var laRequete = new Ajax.Updater(elementHTML,url,o_options);
}
function afficherDiv(id){
  Element.show(id);
}