// javascript pour les menus deroulants

window.onload = montre;
function montre(id) {
var d = document.getElementById(id);
	for (var i = 1; i<=4; i++) {
		if (document.getElementById('sEdTmenu'+i)) {document.getElementById('sEdTmenu'+i).style.display='none';}
	}
if (d) {d.style.display='block';}
}

//Utilisation de Prototype et de méthode toogle de Element qui permet de changer le display d'un id
function changerDisplayDiv(nomDiv) {
	Element.toggle(nomDiv);
}

// Dans le cas où on n'utilise pas le /ilb/functions.js, on a besoin de cette fonction :
function centrerpopup(page,largeur,hauteur,options) {
	var top=(screen.height-hauteur)/2;
	var left=(screen.width-largeur)/2;
	window.open(page,"","top="+top+",left="+left+",width="+largeur+",height="+hauteur+",directories=no,toolbar=no,menubar=no,location=no,"+options);
}