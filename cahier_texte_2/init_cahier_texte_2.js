/*
 * $Id$
 *
 * Copyright 2009 Josselin Jacquard
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

//page initialisation
Event.observe(window, 'load', initPage);

function initPage () {
	getWinCalendar();

	//si id_group_init est renseigné on affiche le groupe concerné, sinon on affiche les dernieres notices
	var id_groupe_init = $('id_groupe_init').value;
	if (id_groupe_init != '') {
	    id_groupe = id_groupe_init;
	    getWinDernieresNotices().hide();
	    getWinListeNotices();
	    new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe_init, {encoding: 'ISO-8859-1'});
	    getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=' + id_groupe_init + '&today='+getCalendarUnixDate(), {
		    encoding: 'ISO-8859-1',
		    onComplete :
		    function() {
			    initWysiwyg();
				    }
			    }
	    );
	} else {
            getWinDernieresNotices().show();
	}
}


function include(filename)
{
	var head = document.getElementsByTagName('head')[0];
	//alert('test : ' + head);
	
	script = document.createElement('script');
	script.src = filename;
	script.type = 'text/javascript';
	
	head.appendChild(script);
}

//
//Les include specifique au cahier de texte 2 sont mis dans un seul fichier pour optimisation.
//include('./webtoolkit.aim.js');
//include('./ajax_functions.js');

include('../lib/DHTMLcalendar/calendar.js');
include('../lib/DHTMLcalendar/lang/calendar-fr.js');
include('../lib/DHTMLcalendar/calendar-setup.js');
include('../ckeditor/ckeditor.js');
include('../edt_effets/javascripts/window.js');

function getWinListeNotices() {
	if (typeof winListeNotices=="undefined") {
		winListeNotices = new Window(
				{id: 'win_liste_notices',
				title: 'Liste des Notices',
				showEffect: Element.show,
				hideEffect: Element.hide,
				top:160,
				left:0,
				width:300,
				height:GetHeight() - 220}
			);
		$('win_liste_notices_content').setStyle({	
			backgroundColor: '#d0d0d0',
			fontSize: '12px',
			color: '#000000'
		});
		winListeNotices.getContent().innerHTML= "<div id='affichage_liste_notice' style='padding-right: 2px; padding-left: 2px'><div>";
	}
	winListeNotices.show();
	winListeNotices.toFront();
	return winListeNotices;
}

function getWinEditionNotice() {
	if (typeof winEditionNotice=="undefined") {
		winEditionNotice = new Window(
				{id: 'win_edition_notice',
				title: 'Edition de Notice',
				showEffect: Element.show,
				hideEffect: Element.hide,
				top:160,
				left:304,
				width:GetWidth()-310,
				height:GetHeight() - 220}
			);
		$('win_edition_notice_content').setStyle({	
			backgroundColor: '#d0d0d0',
			fontSize: '14px',
			color: '#000000'
		});
	}
	winEditionNotice.show();
	winEditionNotice.toFront();
	return winEditionNotice;
}

function getWinDernieresNotices() {
	if (typeof winDernieresNotices=="undefined") {
		winDernieresNotices = new Window(
				{id: 'win_dernieres_notices',
				title: 'Dernières Notices',
				showEffect: Element.show,
				hideEffect: Element.hide,
				top:155,
				left:40,
				width:GetWidth()-100,
				height:GetHeight() - 230}
			);
		$('win_dernieres_notices_content').setStyle({	
			backgroundColor: '#d0d0d0',
			fontSize: '14px',
			color: '#000000'
		});
		winDernieresNotices.getContent().innerHTML= "<div id='affichage_derniere_notice'><div>";
		// Set up a windows observer to refresh the window when focused
		var  myObserver = { onFocus: function(eventName, win) {
			 	if (win == winDernieresNotices) {
			 		new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();}});
					updateDivModification();
			 	}
		 	}
		 }
		 Windows.addObserver(myObserver);
		 winDernieresNotices.show();
	}
	return winDernieresNotices;
}

function getWinCalendar() {
	if (typeof winCalendar=="undefined") {
		winCalendar = new Window(
				{id: 'win_calendar',
				title: 'Calendrier',
				closable: false,
				minimizable: false,
				maximizable: false,
				showEffect: Element.show,
				hideEffect: Element.hide,
				top:0, 
				right:85,
				width:155,
				height:170}
			);
		$('win_calendar_content').setStyle({	
			backgroundColor: '#d0d0d0',
			color: '#000000'
		});
		$('win_calendar_content').innerHTML = '<div id="calendar-container-2" onmouseover="winCalendar.toFront();">';
		calendarInstanciation = Calendar.setup(
				{
					flat         : "calendar-container-2", // ID of the parent element
					flatCallback : dateChanged,          // our callback function
					daFormat     : "%s",    			   //date format
					weekNumbers  : false,
					dayMouseOverCalendarToFront : true
				}
			);
	}
	winCalendar.show();
	winCalendar.toFront();
	return winCalendar;
}

function GetWidth()
{
        var x = 0;
        if (self.innerHeight)
        {
                x = self.innerWidth;
        }
        else if (document.documentElement && document.documentElement.clientHeight)
        {
                x = document.documentElement.clientWidth;
        }
        else if (document.body)
        {
                x = document.body.clientWidth;
        }
        return x;
}

function GetHeight()
{
        var y = 0;
        if (self.innerHeight)
        {
                y = self.innerHeight;
        }
        else if (document.documentElement && document.documentElement.clientHeight)
        {
                y = document.documentElement.clientHeight;
        }
        else if (document.body)
        {
                y = document.body.clientHeight;
        }
        return y;
}

/**
*
*  AJAX IFRAME METHOD (AIM)
*  http://www.webtoolkit.info/
*
**/

AIM = {

    frame : function(c) {

        var n = 'f' + Math.floor(Math.random() * 99999);
        var d = document.createElement('DIV');
        d.innerHTML = '<iframe style="display:none" src="about:blank" id="'+n+'" name="'+n+'" onload="AIM.loaded(\''+n+'\')"></iframe>';
        document.body.appendChild(d);

        var i = document.getElementById(n);
        if (c && typeof(c.onComplete) == 'function') {
            i.onComplete = c.onComplete;
        }

        return n;
    },

    form : function(f, name) {
        f.setAttribute('target', name);
    },

    submit : function(f, c) {
        AIM.form(f, AIM.frame(c));
        if (c && typeof(c.onStart) == 'function') {
            return c.onStart();
        } else {
            return true;
        }
    },

    loaded : function(id) {
        var i = document.getElementById(id);
        if (i.contentDocument) {
            var d = i.contentDocument;
        } else if (i.contentWindow) {
            var d = i.contentWindow.document;
        } else {
            var d = window.frames[id].document;
        }
        if (d.location.href == "about:blank") {
            return;
        }

        if (typeof(i.onComplete) == 'function') {
            i.onComplete(d.body.innerHTML);
        }
    }

}
/**
*
*  FIN AJAX IFRAME METHOD (AIM)
*  http://www.webtoolkit.info/
*
**/

/**
*
*  Fonctions ajax du cahier de texte
*
**/
//groupe en cours de modification
id_groupe = '';
//compte rendu en cours de modification
id_ct_en_cours = '';
//devoir en cours de modification
id_devoir_en_cours = '';
//objet en cours de modification (devoir ou compte rendu)
//object_en_cours_edition == 'notice_privee'
//object_en_cours_edition == 'devoir';
object_en_cours_edition = 'compte_rendu';

//update div modification dans la liste des notices
function updateDivModification() {
	if ($('div_id_ct') != null) {
		compte_rendu_en_cours_de_modification($F('div_id_ct'));
	}
}

function compte_rendu_en_cours_de_modification(id_ct) {
    var tabdiv=document.getElementsByTagName("div");
    for(var i=0; i < tabdiv.length; i++){
	if (tabdiv[i].id.match('compte_rendu_en_cours_')) {
	    Element.extend(tabdiv[i]);
	    tabdiv[i].hide();
	}
    }
    divElement = $('compte_rendu_en_cours_' + id_ct);
    if (divElement != null) {
    	divElement.update('en modification');
    	divElement.show();
    }
}

//Effectue les fonctions javascript necessaires apres la mise a jour de la fenetre edition de notice
function initWysiwyg() {
	updateDivModification();

	if ($('contenu') != null) {

		//destruction de l'instance precedente
		if (CKEDITOR.instances['contenu'] != null) {
		    CKEDITOR.remove(CKEDITOR.instances['contenu']);
		}
		//creation de l'instance
		//En fonction de la largeur, on change le menu pour eviter de le couper si la largeur est trop petite
		if (GetWidth() < 1100) {
		    CKEDITOR.replace( 'contenu', {
			language : 'fr',
			skin : 'kama',
			resize_enabled : false,
			startupFocus : true,
			toolbar :
			[
			    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
			    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
			    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
			    ['NumberedList','BulletedList'],
			    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			    ['Outdent','Indent'],
			    ['Link','Unlink','Table','HorizontalRule','Smiley','SpecialChar'],
			    ['Styles','Format','Font','FontSize'],
			    ['TextColor','BGColor'],
			    ['Maximize', 'About']
			]
		    } );
		} else {
		    CKEDITOR.replace( 'contenu', {
			language : 'fr',
			skin : 'kama',
			resize_enabled : false,
			startupFocus : true,
			toolbar :
			[
			    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
			    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
			    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
			    ['NumberedList','BulletedList'],
			    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			    '/',
			    ['Outdent','Indent'],
			    ['Link','Unlink','Table','HorizontalRule','Smiley','SpecialChar'],
			    ['Styles','Format','Font','FontSize'],
			    ['TextColor','BGColor'],
			    ['Maximize', 'About']
			]
		    } );
		}

		//hide the bottom bar of CKEditor
		$('cke_bottom_contenu').hide();
	}
}

function suppressionCompteRendu(message, id_ct_a_supprimer) {
	if (confirmlink(this,'suppression de la notice du ' + message + ' ?','Confirmez vous ')) {
    	new Ajax.Request('./ajax_suppression_notice.php?type=CahierTexteCompteRendu&id_objet='+id_ct_a_supprimer,
    		{ onComplete:
    			function(transport) {
    				if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
    					alert(transport.responseText);
      				} else {
      					if (object_en_cours_edition == 'compte_rendu' && $('id_ct') != null && $F('id_ct') == id_ct_a_supprimer) {
								getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), { onComplete:	function() {initWysiwyg();}});
						}
				      	new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + $F('id_groupe_colonne_gauche'), { onComplete: function() {updateDivModification();}});
						new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();}});
					}
				}
			}
		);
	}
}

function suppressionDevoir(message, id_devoir_a_supprimer, id_groupe) {
	if (confirmlink(this,'suppression du travail à faire pour le ' + message + ' ?','Confirmez vous ')) {
    	new Ajax.Request('./ajax_suppression_notice.php?type=CahierTexteTravailAFaire&id_objet='+id_devoir_a_supprimer,
    		{ onComplete:
    			function(transport) {
  					if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
      					alert(transport.responseText);
      				} else {
      					if (object_en_cours_edition == 'devoir' && $('id_devoir') != null && $F('id_devoir') == id_devoir_a_supprimer) {
							getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), { onComplete:	function() {initWysiwyg();}});
						}
				      	new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + $F('id_groupe_colonne_gauche'), { onComplete: function() {updateDivModification();}});
						new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();}});
      				}
    			}
    		}
    	);
	}
}

function suppressionNoticePrivee(message, id_notice_privee_a_supprimer, id_groupe) {
	if (confirmlink(this,'suppression de la notice privee du ' + message + ' ?','Confirmez vous ')) {
    	new Ajax.Request('./ajax_suppression_notice.php?type=CahierTexteNoticePrivee&id_objet='+id_notice_privee_a_supprimer,
    		{ onComplete:
    			function(transport) {
  					if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
      					alert(transport.responseText);
      				} else {
      					if (object_en_cours_edition == 'notice_privee' && $('id_ct') != null && $F('id_ct') == id_notice_privee_a_supprimer) {
							getWinEditionNotice().setAjaxContent('./ajax_edition_notice_privee.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), { onComplete:	function() {initWysiwyg();}});
						}
				      	new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + $F('id_groupe_colonne_gauche'), { onComplete: function() {updateDivModification();}});
						new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();}});
      				}
    			}
    		}
    	);
	}
}

function suppressionDocument(message, id_document_a_supprimer, id_ct) {
	if (confirmlink(this,message,'Confirmez vous ')) {
    	new Ajax.Request('./ajax_suppression_notice.php?type=CahierTexteCompteRenduFichierJoint&id_objet='+id_document_a_supprimer,
    		{ onComplete:
    			function(transport) {
					if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
						alert(transport.responseText);
					} else {
	      				getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_ct=' + id_ct, { onComplete: function() {initWysiwyg();}});
						new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();}});
						new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();}});
					}
    			}
			}
    	);
	}
}

function suppressionDevoirDocument(message, id_document_a_supprimer, id_devoir, id_groupe) {
	if (confirmlink(this,message,'Confirmez vous ')) {
    	new Ajax.Request('./ajax_suppression_notice.php?type=CahierTexteTravailAFaireFichierJoint&id_objet='+id_document_a_supprimer,
    		{ onComplete:
    			function(transport) {
					if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
						alert(transport.responseText);
					} else {
	      				getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?id_devoir=' + id_devoir, { onComplete: function(transport) {initWysiwyg();}});
						new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();}});
						new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();}});
					}
    			}
			}
    	);
	}
}

//webtoolkit aim (ajax iframe method for file uploading)
function completeEnregistrementCompteRenduCallback(response) {
	if (response.match('Erreur') || response.match('error') || response.match('Notice') || response.match('Warning')) {
		alert(response);
		getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), { onComplete:function() {initWysiwyg();}});
	} else {
		//si response ne contient pas le mot erreur, il contient l'id du compte rendu
		id_ct_en_cours = response;
		var url;
		if ($F('passer_a') == 'passer_devoir') {
			url = './ajax_edition_devoir.php?today=' + getTomorrowCalendarUnixDate() +'&id_groupe=' + id_groupe;
			object_en_cours_edition = 'devoir';
			updateCalendarWithUnixDate(getTomorrowCalendarUnixDate());
		} else {
			url = './ajax_edition_compte_rendu.php?succes_modification=oui&id_ct=' + id_ct_en_cours + '&id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate();
		}
		getWinListeNotices();
		new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();}});
		getWinEditionNotice().setAjaxContent(url ,{ onComplete:	function(transport) {initWysiwyg();	}});

		//on attend 5 secondes et on enleve les messages de confirmation d'enregistrement.
      	$('bouton_enregistrer_1');
      	$('bouton_enregistrer_2');
      	setTimeout("$('bouton_enregistrer_1').innerHTML = 'Enregistrer';",8000);
      	setTimeout("$('bouton_enregistrer_2').innerHTML = 'Enregistrer';",8000);
	}
	return true;
}

//webtoolkit aim (ajax iframe method for file uploading)
function completeEnregistrementDevoirCallback(response) {
	if (response.match('Erreur') || response.match('error') || response.match('Notice') || response.match('Warning')) {
 		alert(response);
		getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), { onComplete:function() {initWysiwyg();}});
 	} else {
 		//si response ne contient pas le mot erreur, il contient l'id du compte rendu
 		id_ct_en_cours = response;
 		var url;
		if ($F('passer_a') == 'passer_compte_rendu') {
			object_en_cours_edition = 'compte_rendu';
			url = './ajax_edition_compte_rendu.php?today=' + getCalendarUnixDate() +'&id_groupe=' + id_groupe;
 		} else {
			url = './ajax_edition_devoir.php?succes_modification=oui&id_devoir=' + id_ct_en_cours + '&id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate();
 		}
		getWinListeNotices();
 		new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();}});
 		getWinEditionNotice().setAjaxContent(url ,{ onComplete:	function(transport) {initWysiwyg();	}});

 		//on attend 5 secondes et on enleve les messages de confirmation d'enregistrement.
       	$('bouton_enregistrer_1');
      	$('bouton_enregistrer_2');
      	setTimeout("$('bouton_enregistrer_1').innerHTML = 'Enregistrer';",8000);
      	setTimeout("$('bouton_enregistrer_2').innerHTML = 'Enregistrer';",8000);
	}
	return true;
}

//webtoolkit aim (ajax iframe method for file uploading)
function completeEnregistrementNoticePriveeCallback(response) {
	if (response.match('Erreur') || response.match('error') || response.match('Notice') || response.match('Warning')) {
		alert(response);
		getWinEditionNotice().setAjaxContent('./ajax_edition_notice_privee.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), { onComplete:function() {initWysiwyg();}});
	} else {
		//si response ne contient pas le mot erreur, il contient l'id du compte rendu
		id_ct_en_cours = response;
		getWinListeNotices();
		new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();}});
		var url = './ajax_edition_notice_privee.php?succes_modification=oui&id_ct=' + id_ct_en_cours + '&id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate();
		getWinEditionNotice().setAjaxContent(url ,{ onComplete:	function() {initWysiwyg();	}});

		//on attend 5 secondes et on enleve les messages de confirmation d'enregistrement.
      	$('bouton_enregistrer_1');
      	$('bouton_enregistrer_2');
      	setTimeout("$('bouton_enregistrer_1').innerHTML = 'Enregistrer';",8000);
      	setTimeout("$('bouton_enregistrer_2').innerHTML = 'Enregistrer';",8000);
	}
	return true;
}

//webtoolkit aim (ajax iframe method for file uploading)
function completeDeplacementNoticeCallback(response) {
	//on etudie la reponse de l'enregistrement de la notice
	if (response.match('Erreur') || response.match('error') || response.match('Notice') || response.match('Warning')) {
		updateWindows(response);
	} else {
	    //pas d'erreur, on deplace la notice
	    $('id_ct').value = response;

	    if ($F('id_groupe_deplacement') == -1) {
		    updateWindows('Pas de groupe spécifié');
		    return false;
	    } else {
		    if (typeof calendarDeplacementInstanciation != 'undefined' && calendarDeplacementInstanciation != null) {
			    //get the unix date
			    calendarDeplacementInstanciation.date.setHours(0);
			    calendarDeplacementInstanciation.date.setMinutes(0);
			    calendarDeplacementInstanciation.date.setSeconds(0);
			    calendarDeplacementInstanciation.date.setMilliseconds(0);
			    $('date_deplacement').value = Math.round(calendarDeplacementInstanciation.date.getTime()/1000);
			    updateCalendarWithUnixDate($('date_deplacement').value);
		    } else {
			    $('date_deplacement').value = 0;
		    }
		    $('deplacement_notice_form').request({
			    //une fois le deplacement effectué en base, on mets à jour la fenetre d'edition puis la liste des notices'
			    onComplete: function (transport) {updateWindows(transport.responseText)}
		    });
	    }
	}
	return true;
}

//webtoolkit aim (ajax iframe method for file uploading)
function completeDuplicationNoticeCallback(response) {
	//on etudie la reponse de l'enregistrement de la notice
	if (response.match('Erreur') || response.match('error') || response.match('Notice') || response.match('Warning')) {
		updateWindows(response);
	} else {
	    //pas d'erreur, on deplace la notice
	    $('id_ct').value = response;

	    if ($F('id_groupe_duplication') == -1) {
		    updateWindows('Pas de groupe spécifié');
		    return false;
	    } else {
		    if (typeof calendarDuplicationInstanciation != 'undefined' && calendarDuplicationInstanciation != null) {
			    //get the unix date
			    calendarDuplicationInstanciation.date.setHours(0);
			    calendarDuplicationInstanciation.date.setMinutes(0);
			    calendarDuplicationInstanciation.date.setSeconds(0);
			    calendarDuplicationInstanciation.date.setMilliseconds(0);
			    $('date_duplication').value = Math.round(calendarDuplicationInstanciation.date.getTime()/1000);
			    updateCalendarWithUnixDate($('date_duplication').value);
		    } else {
			    $('date_duplication').value = 0;
		    }
		    $('duplication_notice_form').request({
			    //une fois le deplacement effectué en base, on mets à jour la fenetre d'edition puis la liste des notices'
			    onComplete: function (transport) {updateWindows(transport.responseText)}
		    });
	    }
	}
	return true;
}

function updateWindows(message){
	var url = null;
	var id_groupe = $('id_groupe').value;
	if (object_en_cours_edition == "compte_rendu") {
		url = 'ajax_edition_compte_rendu.php?id_ct=' + $('id_ct').value + '&id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate();
	} else if (object_en_cours_edition == "devoir") {
		url = 'ajax_edition_devoir.php?id_devoir=' + $('id_ct').value + '&id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate();
	} else if (object_en_cours_edition == 'notice_privee') {
		url = 'ajax_edition_notice_privee.php?id_ct=' + $('id_ct').value + '&id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate();
	}
	var id_groupe = $('id_groupe').value;
	getWinEditionNotice().setAjaxContent(url,
		{ onComplete: function() {
				new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();}});
				initWysiwyg();
			}
		});
	if (message != '') {
	    alert(message);
	}
}

function dateChanged(calendar) {
	// Beware that this function is called even if the end-user only
	// changed the month/year.  In order to determine if a date was
	// clicked you can use the dateClicked property of the calendar:

	//set time to zero for for Unix Date
	calendar.date.setHours(0);
	calendar.date.setMinutes(0);
	calendar.date.setSeconds(0);
	calendar.date.setMilliseconds(0);

  	unixdate = Math.round(calendar.date.getTime()/1000);

  	// redirect...
  	compte_rendu_en_cours_de_modification('aucun');
	var url;
	if (object_en_cours_edition == 'compte_rendu') {
		url = './ajax_edition_compte_rendu.php?';
	} else if (object_en_cours_edition == 'notice_privee') {
		url = './ajax_edition_notice_privee.php?';
	} else {
		url = './ajax_edition_devoir.php?';
	}
	getWinListeNotices().toFront();
	getWinEditionNotice().setAjaxContent(url + '&id_groupe='+ id_groupe + '&today='+unixdate,{ onComplete:	function() {initWysiwyg();}});
}

function updateCalendarWithUnixDate(dateStamp) {
	var nouvelleDate = new Date();
	nouvelleDate.setTime(dateStamp*1000);
	calendarInstanciation.setDate(nouvelleDate);
}

function getCalendarUnixDate() {
  calendarInstanciation.date.setHours(0);
  calendarInstanciation.date.setMinutes(0);
  calendarInstanciation.date.setSeconds(0);
  calendarInstanciation.date.setMilliseconds(0);
  return Math.round(calendarInstanciation.date.getTime()/1000);
}

function getTomorrowCalendarUnixDate() {
  calendarInstanciation.date.setHours(0);
  calendarInstanciation.date.setMinutes(0);
  calendarInstanciation.date.setSeconds(0);
  calendarInstanciation.date.setMilliseconds(0);
  return Math.round(calendarInstanciation.date.getTime()/1000 + 3600*24);
}

//gestion de la fonctionnalite chaine des fenetre liste notice et edition notice
chaineActive = true;
function updateChaineIcones() {
	if ($('div_chaine_liste_notices') != null) {
		if (chaineActive == true) {
			$('div_chaine_liste_notices').innerHTML= "<img id=\"chaine_liste_notice\" onClick=\"toggleChaineIcones(); updateEditionNoticeChaine();\" style=\"border: 0px; vertical-align : middle\" src=\"../images/icons/chaine.png\" alt=\"Lier\" title=\"Delier la liste avec la fenetre edition de notices\" />";
		} else {
			$('div_chaine_liste_notices').innerHTML= "<img id=\"chaine_liste_notice\" onClick=\"toggleChaineIcones(); updateEditionNoticeChaine();\" style=\"border: 0px; vertical-align : middle\" src=\"../images/icons/chaine_brisee.png\" alt=\"Lier\" title=\"Lier la liste de la fenetre edition de notices\" />";
		}
	}
	if ($('div_chaine_edition_notice') != null) {
		if (chaineActive == true) {
			$('div_chaine_edition_notice').innerHTML= "<img id=\"chaine_liste_notice\" onClick=\"toggleChaineIcones(); updateListeNoticesChaine();\" style=\"border: 0px; vertical-align : middle\" src=\"../images/icons/chaine.png\" alt=\"Lier\" title=\"Delier la liste avec la fenetre edition de notices\" />";
		} else {
			$('div_chaine_edition_notice').innerHTML= "<img id=\"chaine_liste_notice\" onClick=\"toggleChaineIcones(); updateListeNoticesChaine();\" style=\"border: 0px; vertical-align : middle\" src=\"../images/icons/chaine_brisee.png\" alt=\"Lier\" title=\"Lier la liste de la fenetre edition de notices\" />";
		}
	}
}
function toggleChaineIcones() {
	chaineActive = !chaineActive;
	updateChaineIcones();
}
function updateEditionNoticeChaine() {
	if (chaineActive == true) {
		if ($('id_groupe_colonne_droite') != null && $('id_groupe_colonne_gauche').selectedIndex != $('id_groupe_colonne_droite').selectedIndex) {
			$('id_groupe_colonne_droite').selectedIndex = $('id_groupe_colonne_gauche').selectedIndex;
			$('id_groupe_colonne_droite').onchange();
		}
	}
}
function updateListeNoticesChaine() {
	if (chaineActive == true) {
		if ($('id_groupe_colonne_gauche') != null && $('id_groupe_colonne_gauche').selectedIndex != $('id_groupe_colonne_droite').selectedIndex) {
			$('id_groupe_colonne_gauche').selectedIndex = $('id_groupe_colonne_droite').selectedIndex;
			$('id_groupe_colonne_gauche').onchange();
		}
	}
}
/**
*
*  Fin des fonctions ajax du cahier de texte
*
**/
