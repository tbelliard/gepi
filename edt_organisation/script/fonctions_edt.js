// javascript pour les menus deroulants
window.onload = montre;
var xmlhttp;
function montre(id) {
var d = document.getElementById(id);
	for (var i = 1; i<=5; i++) {
		if (document.getElementById('sEdTmenu'+i)) {document.getElementById('sEdTmenu'+i).style.display='none';}
	}
if (d) {d.style.display='block';}
}
//Utilisation de Prototype et de méthode toggle de Element qui permet de changer le display d'un id
function changerDisplayDiv(nomDiv) {
	Element.toggle(nomDiv);
}
// ===================================================
//
//      Utiliser AJAX pour éviter des requêtes inutiles
//      sur l'affichage des EDTs 
//      utilisé dans vie_scolaire_absences.php
//
// ===================================================

function stateChanged(divname)
{
if (xmlhttp.readyState==4)
{
document.getElementById(divname).innerHTML=xmlhttp.responseText;
}
}

function GetXmlHttpObject()
{
if (window.XMLHttpRequest)
  {
  // code for IE7+, Firefox, Chrome, Opera, Safari
  return new XMLHttpRequest();
  }
if (window.ActiveXObject)
  {
  // code for IE6, IE5
  return new ActiveXObject("Microsoft.XMLHTTP");
  }
return null;
}

function AfficheEdtClasseDuJour(idClasse, nomDiv, niveau_arbo) {

    if (document.getElementById(nomDiv).style.display == 'none') {
        xmlhttp=GetXmlHttpObject();
        if (xmlhttp==null)
          {
          alert ("Browser does not support HTTP Request");
          return;
          }
        if (niveau_arbo == 0) {
            var url="./edt_organisation/helpers/";
        }
        else if (niveau_arbo == 1) {
            var url="../edt_organisation/helpers/";
        }
        else if (niveau_arbo == 2) {
            var url="../../edt_organisation/helpers/";
        }
        else if (niveau_arbo == 3) {
            var url="../../../edt_organisation/helpers/";
        }
        url=url+"construire_edt_classe_jour.php";
        url=url+"?classe="+idClasse;
        url=url+"&contenu_creneaux_edt_avec_span_title=n";
        url=url+"&sid="+Math.random();
        xmlhttp.onreadystatechange= function () {
                                        stateChanged(nomDiv);
                                    }
        xmlhttp.open("GET",url,true);
        xmlhttp.send(null);
    }
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
