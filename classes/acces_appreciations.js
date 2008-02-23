// <![CDATA[
function f_date(id_div,date) {
	//new Ajax.Updater($('id_div'),'acces_appreciations_ajax.php?date=date',{method: 'get'});
	new Ajax.Updater($(id_div),'acces_appreciations_ajax.php?date='+date,{method: 'get'});
}

/*
function f_manuel(id_div,classe_periode,accessible) {
	new Ajax.Updater($(id_div),'acces_appreciations_ajax.php?classe_periode='+classe_periode+'accessible='+accessible,{method: 'get'});
}
*/

/*
function f_manuel(id_div,classe_periode) {
	new Ajax.Updater($(id_div),'acces_appreciations_ajax.php?classe_periode='+classe_periode,{method: 'get'});
}
*/

function f_manuel(id_div,classe_periode,statut) {
	new Ajax.Updater($(id_div),'acces_appreciations_ajax.php?classe_periode='+classe_periode+'&statut='+statut,{method: 'get'});
}

function modif_couleur(id_check,id_span) {
	if(document.getElementById(id_check)) {
		if(document.getElementById(id_span)) {
			if(document.getElementById(id_check).checked==true) {
				document.getElementById(id_span).style.color='green';
			}
			else {
				document.getElementById(id_span).style.color='red';
			}
		}
	}
}
//]]>