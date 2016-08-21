/*
 *
 * Copyright 2009-2011 Josselin Jacquard
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

// Initialisation... pour que le tableau existe, même si on ne définit pas le tableau dans ../temp/info_jours.js
var tab_jours_ouverture=new Array();

//page initialisation: Déplacé en fin de fichier pour que les fonctions soient chargées avant
//Event.observe(window, 'load', initPage);

function initPage () {
	// On ajoute un délais pour que Calendar et calendarInstanciation soient définies via les lib JS calendar.js et calendar-setup.js
	//getWinCalendar();
	setTimeout('getWinCalendar();', 500);


	// Si id_groupe_init, type_notice_init (cr|dev|np) et id_ct_init sont renseignés (non vides), on ouvre la notice indiquée
	var id_groupe_init = $('id_groupe_init').value;
	var type_notice_init = $('type_notice_init').value;
	var id_ct_init = $('id_ct_init').value;
	if ((id_groupe_init != '')&&(type_notice_init != '')&&(id_ct_init != '')) {
	    //id_ct = id_ct_init;
		id_groupe = id_groupe_init;
	    getWinDernieresNotices().hide();
	    getWinListeNotices();

		// On ajoute un délais pour que le calendrier soit chargé avant
		//alert('initFenetreNoticePrecise('+id_groupe_init+','+id_ct_init+',"'+type_notice_init+'")')
		setTimeout('initFenetreNoticePrecise('+id_groupe_init+','+id_ct_init+',"'+type_notice_init+'")',500);
	}
	else {
		// Si id_group_init est renseigné on affiche le groupe concerné, sinon on affiche les dernieres notices
		var id_groupe_init = $('id_groupe_init').value;
		if (id_groupe_init != '') {
			id_groupe = id_groupe_init;
			getWinDernieresNotices().hide();
			getWinListeNotices();
			/*
			new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe_init, {encoding: 'utf-8'});
			getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=' + id_groupe_init + '&today='+getCalendarUnixDate(), {
				encoding: 'utf-8',
				onComplete :
				function() {
					initWysiwyg();
					debut_alert = new Date();
						}
					}
			);
			*/
			// On ajoute un délais pour que le calendrier soit chargé avant
			setTimeout('initFenetreNotice('+id_groupe_init+',"'+type_notice_init+'")',500);
		} else {
			getWinDernieresNotices().hide();
		}
	}
}

function temporiser_init() {
	if(typeof(getWinCalendar)=='function') {
		if(typeof(Calendar)=='function') {
			if(typeof(Calendar.setup)=='function') {
				initPage ();
			}
			else {
				setTimeout("temporiser_init()", 500);
			}
		}
		else {
			setTimeout("temporiser_init()", 500);
		}
	}
	else {
		setTimeout("temporiser_init()", 500);
	}
}

function initFenetreNotice(id_groupe_init) {
	    new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe_init, {encoding: 'UTF-8'});
	    getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=' + id_groupe_init + '&today='+getCalendarUnixDate(), {
		    encoding: 'UTF-8',
		    onComplete :
		    function() {
			    initWysiwyg();
				debut_alert = new Date();
				    }
			    }
	    );
}

function initFenetreNoticePrecise(id_groupe_init,id_ct_init,type_notice_init) {

	new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe_init, {encoding: 'UTF-8'});
	if(type_notice_init=='cr') {
		page="ajax_edition_compte_rendu.php";
		getWinEditionNotice().setAjaxContent('./'+page+'?id_ct=' + id_ct_init + '&id_groupe=' + id_groupe_init + '&mettre_a_jour_cal=y', {
			encoding: 'UTF-8',
			onComplete :
			function() {
				initWysiwyg();
				debut_alert = new Date();
					}
				}
		);
			// Pb: On arrive à la bonne date dans la notice, mais le calendrier n'est pas à la bonne date.
			//     Du coup, Enr. et passer aux devoirs du jour suivant échoue.
	}
	else {
		if(type_notice_init=='dev') {
			page="ajax_edition_devoir.php";
			getWinEditionNotice().setAjaxContent('./'+page+'?id_devoir=' + id_ct_init + '&id_groupe=' + id_groupe_init + '&mettre_a_jour_cal=y', {
				encoding: 'UTF-8',
				onComplete :
				function() {
					initWysiwyg();
					debut_alert = new Date();
						}
					}
			);
			// Pb: On arrive à la bonne date dans la notice, mais le calendrier n'est pas à la bonne date.
		}
		else {
			page="ajax_edition_notice_privee.php";
			getWinEditionNotice().setAjaxContent('./'+page+'?id_ct=' + id_ct_init + '&id_groupe=' + id_groupe_init + '&mettre_a_jour_cal=y', {
				encoding: 'UTF-8',
				onComplete :
				function() {
					initWysiwyg();
					debut_alert = new Date();
						}
					}
			);
		}
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
// Les deux suivants sont insérés plus bas pour tenter d'éviter des erreurs du type:
//	Erreur : Calendar is not defined
//	Fichier Source : https://.../Gepi/lib/DHTMLcalendar/lang/calendar-fr.js
//	Ligne : 13
//	Erreur : Calendar is not defined
//	Fichier Source : https://.../Gepi/lib/DHTMLcalendar/calendar-setup.js
//	Ligne : 63
//include('../lib/DHTMLcalendar/lang/calendar-fr.js');
//include('../lib/DHTMLcalendar/calendar-setup.js');

function temporiser_chargement_js() {
	if(typeof(Calendar)=='function') {
		include('../lib/DHTMLcalendar/lang/calendar-fr.js');
		include('../lib/DHTMLcalendar/calendar-setup.js');
		setTimeout("temporiser_init()", 500);
	}
	else {
		setTimeout("temporiser_chargement_js()", 500);
	}
}

//setTimeout("include('../lib/DHTMLcalendar/calendar-setup.js')", 500);
include('../ckeditor/ckeditor.js');
include('../edt_effets/javascripts/window.js');
include('../temp/info_jours.js');

include('../lib/functions.js');

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
				title: 'Édition de Notice',
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
	/*
	// On ne recupere pas les variables id_groupe et today
	else {
		if(winEditionNotice!='compte_rendu') {
			getWinDevoirsDeLaClasse();
		}
	}
	*/
	if (typeof winDevoirsDeLaClasse!="undefined") {
		winDevoirsDeLaClasse.hide();
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
/*
function setWinCalendarContent() {
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
*/
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
/*
		// Pour ajouter un délais... on a parfois des pb avec l'ouverture des fenêtres
		// Web Developper signale des pb d'init avec Calendar...
		if(!$('win_calendar_content')) {
			setTimeout('setWinCalendarContent();winCalendar.show();winCalendar.toFront();return winCalendar;',500);
		}
		else {
			setWinCalendarContent();
*/
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
		//}
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

// Initialisation de la date du cours suivant:
//date_ct_cours_suivant = '';

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
                        removePlugins : 'elementspath',
			extraPlugins : 'equation',
			toolbar :
			[
			    ['Source','Cut','Copy','Paste','PasteText','PasteFromWord'],
			    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
			    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
			    ['NumberedList','BulletedList'],
			    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			    ['Outdent','Indent'],
			    ['Link','Unlink','Table','HorizontalRule','SpecialChar','Equation'],
			    ['Styles','Format','Font','FontSize'],
			    ['TextColor','BGColor'],
			    ['Maximize', 'About','-','Print']
			]
		    } );
		} else {
		    CKEDITOR.replace( 'contenu', {
			language : 'fr',
			skin : 'kama',
			resize_enabled : false,
			startupFocus : true,
		        removePlugins : 'elementspath',
			extraPlugins : 'equation',
			toolbar :
			[
			    ['Source','Cut','Copy','Paste','PasteText','PasteFromWord'],
			    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
			    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
			    ['NumberedList','BulletedList'],
			    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			    '/',
			    ['Outdent','Indent'],
			    ['Link','Unlink','Table','HorizontalRule','SpecialChar','Equation'],
			    ['Styles','Format','Font','FontSize'],
			    ['TextColor','BGColor'],
			    ['Maximize', 'About','-','Print']

			]
		    } );
		}
	}
}

function suppressionCompteRendu(message, id_ct_a_supprimer, csrf_alea) {
	if (confirmlink(this,'suppression de la notice du ' + message + ' ?','Confirmez vous ')) {
    	new Ajax.Request('./ajax_suppression_notice.php?type=CahierTexteCompteRendu&id_objet='+id_ct_a_supprimer+'&csrf_alea='+csrf_alea,
    		{ onComplete:
    			function(transport) {
					debut_alert = new Date();
    				if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
    					alert(transport.responseText);
      				} else {
      					if (object_en_cours_edition == 'compte_rendu' && $('id_ct') != null && $F('id_ct') == id_ct_a_supprimer) {
								getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), { onComplete:	function() {initWysiwyg();debut_alert = new Date();}});
						}
				      	new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + $F('id_groupe_colonne_gauche'), { onComplete: function() {updateDivModification();debut_alert = new Date();}});
						new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();debut_alert = new Date();}});
					}
				}
			}
		);
	}
}

function suppressionDevoir(message, id_devoir_a_supprimer, id_groupe, csrf_alea) {
	if (confirmlink(this,'suppression du travail à faire pour le ' + message + ' ?','Confirmez vous ')) {
    	new Ajax.Request('./ajax_suppression_notice.php?type=CahierTexteTravailAFaire&id_objet='+id_devoir_a_supprimer+'&csrf_alea='+csrf_alea,
    		{ onComplete:
    			function(transport) {
					debut_alert = new Date();
  					if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
      					alert(transport.responseText);
      				} else {
      					if (object_en_cours_edition == 'devoir' && $('id_devoir') != null && $F('id_devoir') == id_devoir_a_supprimer) {
							getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), { onComplete:	function() {initWysiwyg();debut_alert = new Date();}});
						}
				      	new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + $F('id_groupe_colonne_gauche'), { onComplete: function() {updateDivModification();debut_alert = new Date();}});
						new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();debut_alert = new Date();}});
      				}
    			}
    		}
    	);
	}
}

function suppressionNoticePrivee(message, id_notice_privee_a_supprimer, id_groupe, csrf_alea) {
	if (confirmlink(this,'suppression de la notice privee du ' + message + ' ?','Confirmez vous ')) {
    	new Ajax.Request('./ajax_suppression_notice.php?type=CahierTexteNoticePrivee&id_objet='+id_notice_privee_a_supprimer+'&csrf_alea='+csrf_alea,
    		{ onComplete:
    			function(transport) {
					debut_alert = new Date();
  					if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
      					alert(transport.responseText);
      				} else {
      					if (object_en_cours_edition == 'notice_privee' && $('id_ct') != null && $F('id_ct') == id_notice_privee_a_supprimer) {
							getWinEditionNotice().setAjaxContent('./ajax_edition_notice_privee.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), { onComplete:	function() {initWysiwyg();debut_alert = new Date();}});
						}
				      	new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + $F('id_groupe_colonne_gauche'), { onComplete: function() {updateDivModification();debut_alert = new Date();}});
						new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();debut_alert = new Date();}});
      				}
    			}
    		}
    	);
	}
}

function suppressionDocument(message, id_document_a_supprimer, id_ct, csrf_alea) {
	if (confirmlink(this,message,'Confirmez vous ')) {
    	new Ajax.Request('./ajax_suppression_notice.php?type=CahierTexteCompteRenduFichierJoint&id_objet='+id_document_a_supprimer+'&csrf_alea='+csrf_alea,
    		{ onComplete:
    			function(transport) {
					debut_alert = new Date();
					if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
						alert(transport.responseText);
					} else {
	      				getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_ct=' + id_ct, { onComplete: function() {initWysiwyg();debut_alert = new Date();}});
						new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();debut_alert = new Date();}});
						new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();debut_alert = new Date();}});
					}
    			}
			}
    	);
	}
}

function suppressionDevoirDocument(message, id_document_a_supprimer, id_devoir, id_groupe, csrf_alea) {
	if (confirmlink(this,message,'Confirmez vous ')) {
    	new Ajax.Request('./ajax_suppression_notice.php?type=CahierTexteTravailAFaireFichierJoint&id_objet='+id_document_a_supprimer+'&csrf_alea='+csrf_alea,
    		{ onComplete:
    			function(transport) {
					debut_alert = new Date();
					if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
						alert(transport.responseText);
					} else {
	      				getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?id_devoir=' + id_devoir, { onComplete: function(transport) {initWysiwyg();debut_alert = new Date();}});
						new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();debut_alert = new Date();}});
						new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();debut_alert = new Date();}});
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
		getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), { onComplete:function() {initWysiwyg();debut_alert = new Date();}});

		if((response.match('formulaire d'))&&(response.match('Une copie de sauvegarde a'))) {
			getWinListeNotices();
			new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();debut_alert = new Date();}});
		}
	} else {
		/*
		alert("$F('passer_a')="+$F('passer_a'));
		alert("$F('id_groupe')="+$F('id_groupe'));
		alert("id_groupe="+id_groupe);
		alert("date_ct_cours_suivant="+$('date_ct_cours_suivant').value);
		*/
		date_ct_cours_suivant=$('date_ct_cours_suivant').value;
		//si response ne contient pas le mot erreur, il contient l'id du compte rendu
		id_ct_en_cours = response;
		var url;
		if ($F('passer_a') == 'passer_devoir') {
			url = './ajax_edition_devoir.php?today=' + GetNextOpenDayUnixDate() +'&id_groupe=' + id_groupe;
			object_en_cours_edition = 'devoir';
			updateCalendarWithUnixDate(GetNextOpenDayUnixDate());
		}
		else {
			if ($F('passer_a') == 'passer_devoir2') {
				url = './ajax_edition_devoir.php?today=' + date_ct_cours_suivant +'&id_groupe=' + id_groupe;
				object_en_cours_edition = 'devoir';
				updateCalendarWithUnixDate(date_ct_cours_suivant);

			} else {
				url = './ajax_edition_compte_rendu.php?succes_modification=oui&id_ct=' + id_ct_en_cours + '&id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate();
			}
		}

		// Pour ne pas mettre à jour la fenêtre Liste des notices si on a délié la fenêtre Edition et la fenêtre Liste des notices:
		if(chaineActive==true) {
			getWinListeNotices();
			new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();debut_alert = new Date();}});
		}
		else {
			//alert('Pas de mise à jour de la fenêtre Liste des notices');
		}
		getWinEditionNotice().setAjaxContent(url ,{ onComplete:	function(transport) {initWysiwyg();debut_alert = new Date();	}});

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
		getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), { onComplete:function() {initWysiwyg();debut_alert = new Date();}});

		if((response.match('formulaire d'))&&(response.match('Une copie de sauvegarde a'))) {
			getWinListeNotices();
			new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();debut_alert = new Date();}});
		}
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

		// Pour ne pas mettre à jour la fenêtre Liste des notices si on a délié la fenêtre Edition et la fenêtre Liste des notices:
		if(chaineActive==true) {
			getWinListeNotices();
	 		new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();debut_alert = new Date();}});
		}
		else {
			//alert('Pas de mise à jour de la fenêtre Liste des notices');
		}
		getWinEditionNotice().setAjaxContent(url ,{ onComplete:	function(transport) {initWysiwyg();debut_alert = new Date();	}});

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
		getWinEditionNotice().setAjaxContent('./ajax_edition_notice_privee.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), { onComplete:function() {initWysiwyg();debut_alert = new Date();}});
	} else {
		//si response ne contient pas le mot erreur, il contient l'id du compte rendu
		id_ct_en_cours = response;

		var url = './ajax_edition_notice_privee.php?succes_modification=oui&id_ct=' + id_ct_en_cours + '&id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate();

		// Pour ne pas mettre à jour la fenêtre Liste des notices si on a délié la fenêtre Edition et la fenêtre Liste des notices:
		if(chaineActive==true) {
			getWinListeNotices();
			new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();debut_alert = new Date();}});
		}
		else {
			//alert('Pas de mise à jour de la fenêtre Liste des notices');
		}
		getWinEditionNotice().setAjaxContent(url ,{ onComplete:	function() {initWysiwyg();debut_alert = new Date();	}});

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
			    onComplete: function (transport) {updateWindows(transport.responseText);debut_alert = new Date();}
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
			    onComplete: function (transport) {updateWindows(transport.responseText);debut_alert = new Date();}
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

	//alert("id_groupe="+id_groupe+" et id_groupe_colonne_gauche="+$('id_groupe_colonne_gauche').value);

	// Pour ne pas mettre à jour la fenêtre Liste des notices si on a délié la fenêtre Edition et la fenêtre Liste des notices:
	if(chaineActive==true) {
		var id_groupe = $('id_groupe').value;
	}
	else {
		var id_groupe = $('id_groupe_colonne_gauche').value;
	}

	getWinEditionNotice().setAjaxContent(url,
		{ onComplete: function() {
				debut_alert = new Date();
				new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();debut_alert = new Date();}});
				initWysiwyg();
			}
		});

	if (message != 'undefined' && message != '') {
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
	getWinEditionNotice().setAjaxContent(url + '&id_groupe='+ id_groupe + '&today='+unixdate,{ onComplete:	function() {initWysiwyg();debut_alert = new Date();}});
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

//=====================================
//var tab_jours_ouverture=new Array('1','2','3','4','5');

function GetNextOpenDayUnixDate() {
	//var tab_jours_ouverture=new Array('1','2','3','4','5');

	calendarInstanciation.date.setHours(0);
	calendarInstanciation.date.setMinutes(0);
	calendarInstanciation.date.setSeconds(0);
	calendarInstanciation.date.setMilliseconds(0);

	//if(tab_jours_ouverture.length>0) {
	if((tab_jours_ouverture)&&(tab_jours_ouverture.length>0)) {
		// timestamp courant
		timestamp=Math.round(calendarInstanciation.date.getTime()/1000);

		jour=calendarInstanciation.date.getDate();
		mois=calendarInstanciation.date.getMonth()+1;
		annee=calendarInstanciation.date.getFullYear();
		//alert('Date='+jour+'/'+mois+'/'+annee);
		//alert('timestamp='+timestamp);

		// On crée une date de test
		var testDate = new Date();
		testDate.setTime(timestamp*1000);

		// Initialisation pour faire au moins un tour dans la boucle
		jour_ouvert='n';

		var cpt_tmp=0; // Sécurité pour éviter une boucle infinie
		while((jour_ouvert=='n')&&(cpt_tmp<7)) {

			timestamp+=3600*24;
			//alert('timestamp='+timestamp);

			testDate.setTime(timestamp*1000);
			//calendarInstanciation.setDate(testDate);

			for(i=0;i<tab_jours_ouverture.length;i++) {
				//alert("tab_jours_ouverture["+i+"]="+tab_jours_ouverture[i]+" et testDate.getDay()="+testDate.getDay())
				// testDate.getDay() donne le numéro du jour avec 0 pour dimanche
				if(tab_jours_ouverture[i]==testDate.getDay()) {
					jour_ouvert='y';
					break;
				}
			}
			cpt_tmp++;
		}
		// Il faut retourner timestamp (calculé d'après le jour en cours d'édition) et ne pas effectuer de setTime() modifiant la date courante parce qu'on appelle deux fois la fonction en cliquant sur Passer aux devoirs du lendemain... et on passerait alors deux jours au lieu d'un
		//calendarInstanciation.date.setTime(timestamp*1000);
		//return Math.round(calendarInstanciation.date.getTime()/1000);
		return timestamp;
	}
	else {
		// On n'a pas récupéré de jours ouverts dans la base
		return Math.round(calendarInstanciation.date.getTime()/1000 + 3600*24);
	}
}
//=====================================

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


function getWinDevoirsDeLaClasse() {
	if (typeof winDevoirsDeLaClasse=="undefined") {
		winDevoirsDeLaClasse = new Window(
				{id: 'win_dev_classe',
				title: 'Devoirs &agrave; faire',
				showEffect: Element.show,
				hideEffect: Element.hide,
				top:0, 
				left:304,
				width: 600,
				height: 120}
			);
		$('win_dev_classe_content').setStyle({	
			/*backgroundColor: '#d0d0d0',*/
			/*backgroundColor: 'plum',*/
			backgroundColor: '#dda0dd',
			color: '#000000'
		});
		$('win_dev_classe_content').innerHTML = '<div id="dev_classe_container" onmouseover="winDevoirsDeLaClasse.toFront();">';
	}
	winDevoirsDeLaClasse.show();
	winDevoirsDeLaClasse.toFront();
	return winDevoirsDeLaClasse;
}

function getWinListeNoticesPrivees() {
	if (typeof winListeNoticesPrivees=="undefined") {
		winListeNoticesPrivees = new Window(
				{id: 'win_liste_notices_privees',
				title: 'Liste des Notices priv&eacute;es',
				showEffect: Element.show,
				hideEffect: Element.hide,
				top:0, 
				left:100,
				width: 600,
				height: GetHeight() - 220}
			);
		$('win_liste_notices_privees_content').setStyle({
			backgroundColor: '#fffdbc',
			color: '#000000'
		});
		$('win_liste_notices_privees_content').innerHTML = '<div id="liste_notices_privees_container" onmouseover="winListeNoticesPrivees.toFront();">';
	}
	winListeNoticesPrivees.show();
	winListeNoticesPrivees.toFront();
	return winListeNoticesPrivees;
}

function modif_visibilite_doc_joint(notice, id_ct, id_document) {
	csrf_alea=document.getElementById('csrf_alea').value;

	if(notice=='compte_rendu') {
		new Ajax.Updater($('span_document_joint_'+id_document),'ajax_edition_compte_rendu.php?id_ct='+id_ct+'&id_document='+id_document+'&change_visibilite=y&csrf_alea='+csrf_alea,{method: 'get'});
	}
	else {
		if(notice=='devoir') {
			//new Ajax.Updater($('span_document_joint_'+id_document),'ajax_edition_devoir.php?id_ct_devoir='+id_ct+'&id_document='+id_document+'&change_visibilite=y&csrf_alea='+csrf_alea,{method: 'get'});
			new Ajax.Updater($('span_document_joint_'+id_document),'ajax_edition_devoir.php?id_devoir='+id_ct+'&id_document='+id_document+'&change_visibilite=y&csrf_alea='+csrf_alea,{method: 'get'});
		}
	}
}

function getWinBanqueTexte() {
	if (typeof winBanqueTexte=="undefined") {
		winBanqueTexte = new Window(
				{id: 'win_banque_texte',
				title: 'Banque de textes',
				showEffect: Element.show,
				hideEffect: Element.hide,
				top:10,
				left:40,
				width:300,
				height:200}
			);
		$('win_banque_texte_content').setStyle({
			backgroundColor: 'lightblue',
			fontSize: '14px',
			color: '#000000'
		});
	}

	winBanqueTexte.show();
	winBanqueTexte.toFront();
	return winBanqueTexte;
}
/*
function initFenetreBanque() {
	new Ajax.Updater('affichage_banque_texte', './ajax_affichage_banque_texte.php', {encoding: 'UTF-8'});
	getWinBanqueTexte().setAjaxContent('./ajax_affichage_banque_texte.php', {
		encoding: 'UTF-8',
		onComplete :
		function() {
			initWysiwyg();
			debut_alert = new Date();
				}
			}
	);
}
*/

function getWinArchives() {
	if (typeof winArchives=="undefined") {
		winArchives = new Window(
				{id: 'win_archives',
				title: 'Archives CDT',
				showEffect: Element.show,
				hideEffect: Element.hide,
				top:100,
				left:400,
				width:800,
				height:400}
			);
		$('win_archives_content').setStyle({
			backgroundColor: '#d0d0d0',
			fontSize: '14px',
			color: '#000000'
		});
	}

	winArchives.show();
	winArchives.toFront();
	return winArchives;
}


function getWinCarSpec() {
	if (typeof winCarSpec=="undefined") {
		winCarSpec = new Window(
				{id: 'win_car_spec',
				title: 'Caractères spéciaux',
				showEffect: Element.show,
				hideEffect: Element.hide,
				top:10,
				left:340,
				width:300,
				height:200}
			);
		$('win_car_spec_content').setStyle({
			backgroundColor: 'lightblue',
			fontSize: '14px',
			color: '#000000'
		});
	}

	winCarSpec.show();
	winCarSpec.toFront();
	return winCarSpec;
}

//include('../lib/DHTMLcalendar/lang/calendar-fr.js');
//include('../lib/DHTMLcalendar/calendar-setup.js');

//page initialisation
//Event.observe(window, 'load', initPage);
//Event.observe(window, 'load', temporiser_init);
Event.observe(window, 'load', temporiser_chargement_js);

//setTimeout('getWinCalendar();', 5000);


function insere_texte_dans_ckeditor(texte) {
	CKEDITOR.instances['contenu'].insertHtml(texte);
}

function insere_texte_dans_ckeditor_2(texte) {
	CKEDITOR.instances['contenu'].insertHtml(unescape(texte));
}

function insere_texte_type_dans_ckeditor(id) {
	if(document.getElementById(id)) {
		CKEDITOR.instances['contenu'].insertHtml(document.getElementById(id).innerHTML);
	}
	else {
		alert("L'identifiant de texte-type proposé ne correspond à aucun texte-type.");
	}
}

function insere_image_dans_ckeditor(url, largeur, hauteur) {
	texte="<img src='"+url+"'";
	if((largeur!='')&&(hauteur!='')) {
		texte=texte+" width='"+largeur+"' height='"+hauteur+"'";
	}
	texte=texte+" />";
	CKEDITOR.instances['contenu'].insertHtml(texte);
}

function insere_lien_ggb_dans_ckeditor(titre, url) {
	//texte="<a href='visionneur_geogebra.php?url="+url+"' target='_blank'>"+url.replace(/\\/g,'/').replace( /.*\//, '' )+"</a>";
	texte="<a href='visionneur_geogebra.php?url="+url+"' target='_blank'>"+titre+"</a>";
	CKEDITOR.instances['contenu'].insertHtml(texte);
}

var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}


/**
*
*  Fin des fonctions ajax du cahier de texte
*
**/
