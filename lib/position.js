function position(e) {
	// Acquisition de la position de la souris et affectation des variables xMousePos et yMousePos
	if (navigator.appName.substring(0,3) == "Net") {
		xMousePos = e.pageX;
		yMousePos = e.pageY;
	}
	else {
		xMousePos = event.x+document.body.scrollLeft;
		yMousePos = event.y+document.body.scrollTop;
	}
	//temporisation=true;
}

if(navigator.appName.substring(0,3) == "Net") document.captureEvents(Event.MOUSEMOVE);

document.onmousemove = position;

function afficher_div(id,positionner,dx,dy) {
	// id: identifiant du DIV
	// positionner: 'y' ou 'n'
	//              Avec 'y', le DIV est positionné d'après la position de la souris.
	//              Avec 'n', le DIV est affiché à sa position initiale indiquée dans le style
	// dx: décalage en abscisse par rapport à la position de la souris
	// dy: décalage en ordonnée par rapport à la position de la souris

	if(temporisation_chargement=="ok"){
		if(positionner=='y'){
			if(browser.isIE){
				document.getElementById(id).style.top=yMousePos+dy;
				document.getElementById(id).style.left=xMousePos+dx;
			}
			else{
				document.getElementById(id).style.top=yMousePos+dy+'px';
				document.getElementById(id).style.left=xMousePos+dx+'px';
			}
		}
		document.getElementById(id).style.display='';
	}
}

function cacher_div(id) {
	// id: identifiant du DIV

	if(temporisation_chargement=="ok"){
		document.getElementById(id).style.display='none';
	}
}
