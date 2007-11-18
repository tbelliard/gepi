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

function CocheCase(boul) {

	nbelements = document.nouvelle_periode.elements.length;
		for (i = 0 ; i < nbelements ; i++) {
			if (document.nouvelle_periode.elements[i].type =='checkbox')
			document.nouvelle_periode.elements[i].checked = boul ;
		}
}
