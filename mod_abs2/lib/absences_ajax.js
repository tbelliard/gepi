function gestionaffAbs(id, reglage){
		elementHTML = $(id);
	var url = "saisir_ajax.php";
	o_options = new Object();
	o_options = {postBody: 'var1='+id+'var2='+reglage, onComplete:afficherDiv(id)};
	var laRequete = new Ajax.Updater(elementHTML,url,o_options);
}
function afficherDiv(id){
  Element.show(id);
}