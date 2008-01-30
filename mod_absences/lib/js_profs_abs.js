// Fonction qui donne l'heure des retards

function getHeure(input_check,input_go,form_choix){
	var date_select=new Date();
	var heures=date_select.getHours();if(heures<10){heures="0"+heures;}
	var minutes=date_select.getMinutes();if(minutes<10){minutes="0"+minutes;}
	// nom du formulaire
	var form_action = form_choix;
	// id des élèments
	var input_go_id = input_go.id;
	var input_check_id = input_check.id;
	// modifie le contenue de l'élèment
	if(document.forms[form_action].elements[input_check_id].checked) {
		document.forms[form_action].elements[input_go_id].value=heures+":"+minutes;
	} else {
		document.forms[form_action].elements[input_go_id].value='';
	}
} // getHeure