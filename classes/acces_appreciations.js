// <![CDATA[
/*
function f_date(id_div,date) {
	//new Ajax.Updater($('id_div'),'acces_appreciations_ajax.php?date=date',{method: 'get'});
	new Ajax.Updater($(id_div),'acces_appreciations_ajax.php?date='+date,{method: 'get'});
}
*/

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

/*
function f_manuel(id_div,classe_periode,statut) {
	new Ajax.Updater($(id_div),'acces_appreciations_ajax.php?classe_periode='+classe_periode+'&statut='+statut,{method: 'get'});
}
*/

function g_manuel(id_div,id_classe,periode,accessible,statut) {
	csrf_alea=document.getElementById('csrf_alea').value;
	new Ajax.Updater($(id_div),'acces_appreciations_ajax.php?id_div='+id_div+'&id_classe='+id_classe+'&periode='+periode+'&mode=manuel&accessible='+accessible+'&statut='+statut+'&csrf_alea='+csrf_alea,{method: 'get'});
}

function g_manuel_individuel(id_div,id_classe,periode) {
	csrf_alea=document.getElementById('csrf_alea').value;
	new Ajax.Updater($(id_div),'acces_appreciations_ajax.php?id_div='+id_div+'&id_classe='+id_classe+'&periode='+periode+'&mode=manuel_individuel&csrf_alea='+csrf_alea,{method: 'get'});
}

function g_date() {
	csrf_alea=document.getElementById('csrf_alea').value;
	id_div=$('choix_date_id_div').value;
	id_classe=$('choix_date_id_classe').value;
	periode=$('choix_date_periode').value;
	statut=$('choix_date_statut').value;
	choix_date=$('choix_date').value;
	new Ajax.Updater($(id_div),'acces_appreciations_ajax.php?id_div='+id_div+'&id_classe='+id_classe+'&periode='+periode+'&mode=date&choix_date='+choix_date+'&statut='+statut+'&csrf_alea='+csrf_alea,{method: 'get'});
	$('infobulle_choix_date').style.display='none';
}

function g_periode_close(id_div,id_classe,periode,statut) {
	csrf_alea=document.getElementById('csrf_alea').value;
	new Ajax.Updater($(id_div),'acces_appreciations_ajax.php?id_div='+id_div+'&id_classe='+id_classe+'&periode='+periode+'&mode=d&statut='+statut+'&csrf_alea='+csrf_alea,{method: 'get'});
}

/*
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
*/
//]]>
