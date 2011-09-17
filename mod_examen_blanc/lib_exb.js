/*
*/

// fonction permettant d'augmenter/réduire un nombre via onKeyDown
// Pour relever les keyCode: http://www.asquare.net/javascript/tests/KeyCode.html
function nombre_plus_moins(id,e){

	if(document.getElementById(id)) {
		var touche=e.keyCode;

		//if((touche == '61')||(touche == '109')) {
		if((touche == '40')||(touche == '38')) {
			var nombre=document.getElementById(id).value;

			// Touche + -> PB: Le + est écrit quand même
			//if (touche == '61') {
			// Touche Flèche Haut
			if (touche == '40') {
				nombre=eval(eval(nombre)+1);
			}
			// Touche -
			//if (touche == '109') {
			// Touche Flèche Bas
			if (touche == '38') {
				nombre=eval(eval(nombre)-1);
			}

			if(nombre<0) {nombre=0;}

			document.getElementById(id).value=nombre;
		}
		/*
		else {
			alert('Autre touche')
		}
		*/
	}
	/*
	else {
		alert('id '+id+' inexistant')
	}
	*/
}

