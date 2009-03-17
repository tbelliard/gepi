//groupe en cours de modification
id_groupe = '';
//compte rendu en cours de modification
id_ct_en_cours = '';
//devoir en cours de modification
id_devoir_en_cours = '';
//objet en cours de modification (devoir ou compte rendu)
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
      if (tabdiv[i].identify().match('compte_rendu_en_cours_')) {
      	tabdiv[i].hide();
      }
    }

    divElement = document.getElementById('compte_rendu_en_cours_' + id_ct);
    if (divElement != null) {
    	divElement.update('en modification');
    	divElement.show();
    }
}


//Effectue les fonctions javascript necessaires apres la mise a jour de la fenetre edition de notice
function initWysiwyg() {
	updateDivModification();

	if ($('contenu') != null) {
		var editorHeight = winEditionNotice.getSize()['height'] - 390;
		if (editorHeight < 170) editorHeight = 170;

		//$('contenu').setStyle({width: '100%', height : editorHeight + 'px'});
		//new nicEditor({iconsPath : 'nicEdit/nicEditorIcons.gif'}).panelInstance('contenu');

		if (typeof oFCKeditor=="undefined") {
			oFCKeditor = new FCKeditor( 'contenu' ) ;
			oFCKeditor.BasePath = '../fckeditor/' ;
			oFCKeditor.StylesXmlPath = null ;
			oFCKeditor.Config['DefaultLanguage']  = 'fr' ;
			oFCKeditor.ToolbarSet = 'Basic' ;
			oFCKeditor.Width = '100%' ;
		}
		oFCKeditor.Height = editorHeight ;
		oFCKeditor.ReplaceTextarea() ;
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
				      	new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe, { onComplete: function() {updateDivModification();}});
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
				      	new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe, { onComplete: function() {updateDivModification();}});
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
				      	new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe, { onComplete: function() {updateDivModification();}});
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
	if (response.match('Erreur') || response.match('error')) {
		alert(response);
		getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), { onComplete:function() {iniWysiwyg();}});
	} else {
		//si response ne contient pas le mot erreur, il contient l'id du compte rendu
		id_ct_en_cours = response;
		var url;
		if ($F('passer_a') == 'passer_devoir') {
			url = './ajax_edition_devoir.php?today=' + getTomorrowCalendarUnixDate() +'&id_groupe=' + id_groupe;
			object_en_cours_edition = 'devoir';
			updateCalendarWithUnixDate(getTomorrowCalendarUnixDate());
		} else {
			url = './ajax_edition_compte_rendu.php?succes_modification=oui&id_ct=' + id_ct_en_cours;
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
 	if (response.match('Erreur') || response.match('error')) {
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
			url = './ajax_edition_devoir.php?succes_modification=oui&id_devoir=' + id_ct_en_cours;
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
	if (response.match('Erreur') || response.match('error')) {
		alert(response);
		getWinEditionNotice().setAjaxContent('./ajax_edition_notice_privee.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), { onComplete:function() {initWysiwyg();}});
	} else {
		//si response ne contient pas le mot erreur, il contient l'id du compte rendu
		id_ct_en_cours = response;
		getWinListeNotices();
		new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,{ onComplete:function() {updateDivModification();}});
		var url = './ajax_edition_notice_privee.php?succes_modification=oui&id_ct=' + id_ct_en_cours;
		getWinEditionNotice().setAjaxContent(url ,{ onComplete:	function() {initWysiwyg();	}});
		
		//on attend 5 secondes et on enleve les messages de confirmation d'enregistrement.
      	$('bouton_enregistrer_1');
      	$('bouton_enregistrer_2');
      	setTimeout("$('bouton_enregistrer_1').innerHTML = 'Enregistrer';",8000);
      	setTimeout("$('bouton_enregistrer_2').innerHTML = 'Enregistrer';",8000);
	}
	return true;
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
chaineActive = false;
function updateChaineIcones() {
	if ($('div_chaine_liste_notices') != null) {
		if (chaineActive == true) {
			$('div_chaine_liste_notices').innerHTML= "<img id=\"chaine_liste_notice\" onClick=\"toggleChaineIcones(); updateEditionNoticeChaine();\" style=\"border: 0px; vertical-align : middle\" src=\"../images/icons/chaine.png\" alt=\"Lier\" title=\"Lier la liste avec la fenetre edition de notices\" />";
		} else {
			$('div_chaine_liste_notices').innerHTML= "<img id=\"chaine_liste_notice\" onClick=\"toggleChaineIcones(); updateEditionNoticeChaine();\" style=\"border: 0px; vertical-align : middle\" src=\"../images/icons/chaine_brisee.png\" alt=\"Lier\" title=\"Delier la liste de la fenetre edition de notices\" />";
		}
	}
	if ($('div_chaine_edition_notice') != null) {
		if (chaineActive == true) {
			$('div_chaine_edition_notice').innerHTML= "<img id=\"chaine_liste_notice\" onClick=\"toggleChaineIcones(); updateListeNoticesChaine();\" style=\"border: 0px; vertical-align : middle\" src=\"../images/icons/chaine.png\" alt=\"Lier\" title=\"Lier la liste avec la fenetre edition de notices\" />";
		} else {
			$('div_chaine_edition_notice').innerHTML= "<img id=\"chaine_liste_notice\" onClick=\"toggleChaineIcones(); updateListeNoticesChaine();\" style=\"border: 0px; vertical-align : middle\" src=\"../images/icons/chaine_brisee.png\" alt=\"Lier\" title=\"Delier la liste de la fenetre edition de notices\" />";
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