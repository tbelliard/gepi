//groupe en cours de modification
id_groupe = '';
//compte rendu en cours de modification
id_ct_en_cours = '';
//devoir en cours de modification
id_devoir_en_cours = '';
//objet en cours de modification (devoir ou compte rendu)
object_en_cours_edition = 'compte_rendu';

function suppressionCompteRendu(message, id_ct_a_supprimer) {
	if (confirmlink(this,'suppression de la notice du ' + message + ' ?','Confirmez vous ')) {
    	new Ajax.Request('./ajax_suppression_compte_rendu.php?id_ct='+id_ct_a_supprimer, 
    		{ onComplete: 
    			function(transport) {
    				if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
    					alert(transport.responseText);
      				} else {
      					if (object_en_cours_edition == 'devoir') {
      						getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), 
      							{ onComplete: 
      								function(transport) {
										new nicEditor({iconsPath : 'nicEdit/nicEditorIcons.gif'}).panelInstance('contenu');
				      					getWinListeNotices().setAjaxContent('./ajax_affichages_liste_notices.php?id_groupe=' + id_groupe);
									} 
      							}
      						);
      					} else {
      						getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), 
      							{ onComplete: 
      								function(transport) {
      									getWinEditionNotice().updateWidth();
      			      					getWinListeNotices().setAjaxContent('./ajax_affichages_liste_notices.php?id_groupe=' + id_groupe);
      								}
      							}
      						);
      					}
      				}
    			}
			}
    	);
	}
}

function suppressionDevoir(message, id_devoir_a_supprimer, id_groupe) {
	if (confirmlink(this,'suppression du travail à faire pour le ' + message + ' ?','Confirmez vous ')) {
    	new Ajax.Request('./ajax_suppression_devoir.php?id_devoir='+id_devoir_a_supprimer, 
    		{ onComplete: 
    			function(transport) {
  					if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
      					alert(transport.responseText);
      				} else {
      					if (object_en_cours_edition == 'devoir') {
      						getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), 
      							{ onComplete: 
      								function(transport) {
										new nicEditor({iconsPath : 'nicEdit/nicEditorIcons.gif'}).panelInstance('contenu');
										getWinListeNotices().setAjaxContent('./ajax_affichages_liste_notices.php?id_groupe=' + id_groupe);
									}
      							}
      						);
      					} else {
      						getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(),
      							{ onComplete: 
      								function(transport) {
      									getWinEditionNotice().updateWidth();
										getWinListeNotices().setAjaxContent('./ajax_affichages_liste_notices.php?id_groupe=' + id_groupe);
									}
      							}
      						);
      					}
      				}
    			}
    		}
    	);
	}
}

function suppressionDocument(message, id_document_a_supprimer, id_ct) {
	if (confirmlink(this,message,'Confirmez vous ')) {
    	new Ajax.Request('./ajax_suppression_document.php?id_document='+id_document_a_supprimer,
    		{ onComplete: 
    			function(transport) {
					if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
						alert(transport.responseText);
					} else {
	      				getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_ct=' + id_ct, 
	      					{ onComplete: 
	      						function(transport) {
	      							getWinEditionNotice().updateWidth();
				  					getWinListeNotices().setAjaxContent('./ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,
				  						{ onComplete: 
				  							function() {
				  								compte_rendu_en_cours_de_modification('compte_rendu_' + id_ct);
				  							}
				  						}
				  					);									
								}
	      					}
	      				);
					}
    			}
			}
    	);
	}
}

function suppressionDevoirDocument(message, id_document_a_supprimer, id_devoir, id_groupe) {
	if (confirmlink(this,message,'Confirmez vous ')) {
    	new Ajax.Request('./ajax_suppression_devoir_document.php?id_document='+id_document_a_supprimer,
    		{ onComplete: 
    			function(transport) {
					if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
						alert(transport.responseText);
					} else {
	      				getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?id_devoir=' + id_devoir, 
	      					{ onComplete: 
	      						function(transport) {
									new nicEditor({iconsPath : 'nicEdit/nicEditorIcons.gif'}).panelInstance('contenu');
				  					getWinListeNotices().setAjaxContent('./ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,
				  						{ onComplete: 
				  							function() {
				  								compte_rendu_en_cours_de_modification('devoir_' + id_devoir);
				  							}
				  						}
				  					);									
								}
	      					}
	      				);
					}
    			}
			}
    	);
	}
}

//script pour afficher une notification de modification pour les compte rendu
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

//webtoolkit aim (ajax iframe method for file uploading)
function completeEnregistrementCompteRenduCallback(response) {
	if (response.match('Erreur') || response.match('error')) {
		alert(response);
		getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), 
			{ onComplete:
				function(transport) {
					getWinEditionNotice().updateWidth();
				}
			}
		);
	} else {
		//si response ne contient pas le mot erreur, il contient l'id du compte rendu
		id_ct_en_cours = response;
		if ($('passer_a').value == 'passer_devoir') {
			object_en_cours_edition = 'devoir';
			getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?today=' + getTomorrowCalendarUnixDate() +'&id_groupe=' + id_groupe, 
				{ onComplete:
					function(transport) {
						new nicEditor({iconsPath : 'nicEdit/nicEditorIcons.gif'}).panelInstance('contenu');
				      	getWinListeNotices().setAjaxContent('./ajax_affichages_liste_notices.php?id_groupe=' + id_groupe);
					}
				}
			);
			updateCalendarWithUnixDate(getTomorrowCalendarUnixDate());
      	} else {
      		getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?succes_modification=oui&id_ct=' + id_ct_en_cours,
      			{ onComplete: 
      				function(transport) {
      					getWinEditionNotice().uptdateWidth();
				      	getWinListeNotices().setAjaxContent('./ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,
				          	{ onComplete:
				          		function(transport) {
				      				compte_rendu_en_cours_de_modification('compte_rendu_'+id_ct_en_cours);
				      			}
				          	}
				      	);
					}
      			}
      		);
      	}

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
		getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?id_groupe=' + id_groupe + '&today=' + getCalendarUnixDate(), 
			{ onComplete:
				function(transport) {
					new nicEditor({iconsPath : 'nicEdit/nicEditorIcons.gif'}).panelInstance('contenu');
				}
			}
		);
	} else {
		//si response ne contient pas le mot erreur, il contient l'id du compte rendu
		id_ct_en_cours = response;
		if ($('passer_a').value == 'passer_compte_rendu') {
			object_en_cours_edition = 'compte_rendu';
			getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?today=' + getTomorrowCalendarUnixDate() +'&id_groupe=' + id_groupe, 
				{ onComplete:
					function(transport) {
						getWinEditionNotice().updateWidth();
				      	getWinListeNotices().setAjaxContent('./ajax_affichages_liste_notices.php?id_groupe=' + id_groupe);
					}
				}
			);
			updateCalendarWithUnixDate(getTomorrowCalendarUnixDate());
      	} else {
      		getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?succes_modification=oui&id_devoir=' + id_ct_en_cours,
      			{ onComplete: 
      				function(transport) {
						new nicEditor({iconsPath : 'nicEdit/nicEditorIcons.gif'}).panelInstance('contenu');
				      	getWinListeNotices().setAjaxContent('./ajax_affichages_liste_notices.php?id_groupe=' + id_groupe,
				          	{ onComplete:
				          		function(transport) {
				      				compte_rendu_en_cours_de_modification('devoir_'+id_ct_en_cours);
				      			}
				          	}
				      	);
					}
      			}
      		);
      	}

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
	if (object_en_cours_edition == 'compte_rendu') {
		getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?&id_groupe='+ id_groupe + '&today='+unixdate, 
  			{ onComplete: 
  				function(transport) {
				getWinEditionNotice().updateWidth();
  				}
  			}
  		);
	} else {
		getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?&id_groupe='+ id_groupe + '&today='+unixdate,
  			{ onComplete: 
  				function(transport) {
  					new nicEditor({iconsPath : 'nicEdit/nicEditorIcons.gif'}).panelInstance('contenu');
  				}
  			}
  		);
	}
};

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