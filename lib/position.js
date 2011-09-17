// Déclaration de la variable testée par la suite dans afficher_div()
var desactivation_infobulle;

// On initialise les variables de capture de la position de la souris au cas où la souris n'aurait pas bougé depuis le début du chargement de la page lorsque l'on appelle afficher_div()
var xMousePos=0;
var yMousePos=0;

//function position(e) {
function crob_position(e) {
	// Acquisition de la position de la souris et affectation des variables xMousePos et yMousePos
	//if (navigator.appName.substring(0,3) == "Net") {
	if ((navigator.appName.substring(0,3) == "Net")||(navigator.appName.substring(0,3) == "Kon")) {
		xMousePos = e.pageX;
		yMousePos = e.pageY;
	}
	else {
		xMousePos = event.x+document.body.scrollLeft;
		yMousePos = event.y+document.body.scrollTop;
	}

	/*
	if(temporisation_chargement=="ok"){
		window.status="xMousePos="+xMousePos+" yMousePos="+yMousePos+" pageYOffset="+pageYOffset+" pageXOffset="+pageXOffset
		//window.status="em2px="+em2px
	}
	*/
	//temporisation=true;
}

//alert(navigator.appName.substring(0,3))

// Apparemment, ce n'est pas utile pour Firefox, konqueror,...
// Cela ne devait concerner que Netscape 4.x
//if(navigator.appName.substring(0,3) == "Net") document.captureEvents(Event.MOUSEMOVE);

//document.onmousemove = position;
document.onmousemove = crob_position;

function afficher_div(id,positionner,dx,dy) {
	// id: identifiant du DIV
	// positionner: 'y' ou 'n'
	//              Avec 'y', le DIV est positionné d'après la position de la souris.
	//              Avec 'n', le DIV est affiché à sa position initiale indiquée dans le style
	// dx: décalage en abscisse par rapport à la position de la souris
	// dy: décalage en ordonnée par rapport à la position de la souris

	/*
	if((typeof(xMousePos)=='undefined')||(typeof(yMousePos)=='undefined')) {
		document.onmousemove = crob_position;
		//alert('navigator.appName.substring(0,3)='+navigator.appName.substring(0,3));
	}
	else {
		alert('2');
	}
	*/

	//if(desactivation_infobulle!="y"){
		if(temporisation_chargement=="ok"){
			if(document.getElementById(id)){
				if(positionner=='y'){

					//document.onmousemove = position;
					//position;

					tmp_x=xMousePos
					tmp_y=yMousePos

					// Correction de la position horizontale si le DIV sort de la fenêtre:
					largeur_div=document.getElementById(id).style.width
					//alert("largeur_div="+largeur_div)
					// PROBLEME: La largeur du DIV est donnée en 'em' et ici il faudrait des 'px'...
					unite_width_div="em"
					if(browser.isIE){
						// Rien à faire...
						// PB pour identifier l'unité...
					}
					else{
						unite_width_div=largeur_div.substring(largeur_div.length-2)

						// On supprime le suffixe 'px' ou 'em'
						largeur_div=largeur_div.substring(0,largeur_div.length-2)

						largeur_div=eval(largeur_div)
					}
					// em2px est calculée dans le footer.inc.php
					if(unite_width_div=='em'){
						largeur_div=largeur_div*em2px
					}
					//alert("unite_width_div="+unite_width_div)

					//alert("tmp_x="+tmp_x+" largeur_div="+largeur_div+" pageXOffset="+pageXOffset)



					//if(eval(largeur_div+tmp_x)>window.innerWidth){
					if(!browser.isIE){
						if(eval(largeur_div+tmp_x-pageXOffset)>window.innerWidth){
							tmp_x=Math.max(xMousePos-2*dx-largeur_div,0)
						}
					}
					else{
						// TROUVER LA SYNTAXE POUR IE...
					}


					// Correction de la position verticale si le DIV sort de la fenêtre:
					hauteur_div=document.getElementById(id).style.height
					// Problème, on ne récupère pas la hauteur du DIV si la propriété n'a pas été fixée dans la partie style='' (souvent on ne fixe pas cette valeur pour laisser le DIV s'ajuster/réduire jusqu'au minimum requis en hauteur)
					if(hauteur_div==0){
						hauteur_div=100
					}

					//if(eval(tmp_y+hauteur_div+dx)>window.innerHeight){
					if(!browser.isIE){
						if(eval(tmp_y+hauteur_div+dx-pageYOffset)>window.innerHeight){
							tmp_y=Math.max(yMousePos-2*dy-hauteur_div,0)
						}
					}
					else{
						// TROUVER LA SYNTAXE POUR IE...
					}

					//alert("tmp_x="+tmp_x+" tmp_y="+tmp_y)

					if(browser.isIE){
						//document.getElementById(id).style.top=yMousePos+dy;
						//document.getElementById(id).style.left=xMousePos+dx;
						document.getElementById(id).style.top=Math.max(tmp_y+dy,0);
						document.getElementById(id).style.left=Math.max(tmp_x+dx,0);
					}
					else{
						//document.getElementById(id).style.top=yMousePos+dy+'px';
						//document.getElementById(id).style.left=xMousePos+dx+'px';
						document.getElementById(id).style.top=Math.max(tmp_y+dy,0)+'px';
						document.getElementById(id).style.left=Math.max(tmp_x+dx,0)+'px';
					}

					//window.status='top='+document.getElementById(id).style.top+' et left='+document.getElementById(id).style.left;
					//alert('top='+document.getElementById(id).style.top+' et left='+document.getElementById(id).style.left);

				}
				document.getElementById(id).style.display='';
			}
		}
	//}
}

function cacher_div(id) {
	// id: identifiant du DIV

	if(temporisation_chargement=="ok"){
		if(document.getElementById(id)){
			document.getElementById(id).style.display='none';
		}
	}
}

function delais_afficher_div(id,positionner,dx,dy,delais,DX,DY) {
	if(temporisation_chargement=="ok"){
		setTimeout("controle_afficher_div('"+id+"','"+positionner+"',"+dx+","+dy+","+xMousePos+","+yMousePos+","+DX+","+DY+")",delais);
		//if(document.getElementById('debug_fixe')) {document.getElementById('debug_fixe').innerHTML="Lancement du setTimeout sur '"+id+"' avec dx="+dx+" dy="+dy+" DX="+DX+" DY="+DY;}
	}
}

function controle_afficher_div(id,positionner,dx,dy,x0,y0,DX,DY) {
	//alert("x0="+x0)
	if((Math.abs(x0-xMousePos)<DX)&&(Math.abs(y0-yMousePos)<DY)) {
		afficher_div(id,positionner,dx,dy);
	}

	//if(document.getElementById('debug_fixe')) {document.getElementById('debug_fixe').innerHTML="xMousePos="+xMousePos+" yMousePos="+yMousePos+" Math.abs(x0-xMousePos)="+Math.abs(x0-xMousePos)+" Math.abs(y0-yMousePos)="+Math.abs(y0-yMousePos);}
}
