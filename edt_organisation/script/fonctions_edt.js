// javascript pour les menus deroulants
window.onload = montre;
function montre(id) {
var d = document.getElementById(id);
	for (var i = 1; i<=5; i++) {
		if (document.getElementById('sEdTmenu'+i)) {document.getElementById('sEdTmenu'+i).style.display='none';}
	}
if (d) {d.style.display='block';}
}
//Utilisation de Prototype et de méthode toogle de Element qui permet de changer le display d'un id
function changerDisplayDiv(nomDiv) {
	Element.toggle(nomDiv);
}
// Fonction qui permet de cocher / décocher tous les checkbox de l'élément dont le name = nouvelle_periode
// pour le calendrier (création et modification des périodes pédagogiques)
function CocheCase(boul) {

	nbelements = document.nouvelle_periode.elements.length;
		for (i = 0 ; i < nbelements ; i++) {
			if (document.nouvelle_periode.elements[i].type =='checkbox')
			document.nouvelle_periode.elements[i].checked = boul ;
		}
}
// Fonctions Ajax se basant sur Prototype
function couleursEdtAjax(id, reglage){
		elementHTML = $(id);
	var url = "ajax_edtcouleurs.php";
	o_options = new Object();
	o_options = {method: 'get', parameters: 'var1='+id+'&var2='+reglage};
	var laRequete = new Ajax.Updater(elementHTML,url,o_options);
}