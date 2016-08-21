function classeEdtAjax(id, cla, action){
	elementHTML = $(id);
	if (action == 'nom_en' || action == 'nom_en2') { var cla = $F(cla); }
	var url = "edt_ajax_win.php";
	o_options = new Object();
	o_options = {method: 'get', parameters: 'id_gr='+id+'&classe='+cla+'&action='+action};
	var laRequete = new Ajax.Updater(elementHTML,url,o_options);
}
function changerDisplayDiv(nomDiv) {
	Element.toggle(nomDiv);
}
function versShow(nomDiv) {
	Element.show(nomDiv);
}
function versHide(nomDiv) {
	Element.hide(nomDiv);
}