function alterne_affichage(id) {
	if(document.getElementById(id)) {
		if(document.getElementById(id).style.display=='none') {
			document.getElementById(id).style.display='';
		}
		else {
			document.getElementById(id).style.display='none';
		}
	}
}

function alterne_affichage_global(type, num_jour) {
	for(i=0;i<tab_grp.length;i++) {
		id='travail_jour_'+num_jour+'_groupe_'+tab_grp[i]+'_'+type;
		if(document.getElementById(id)) {
			if(document.getElementById(id).style.display=='none') {
				document.getElementById(id).style.display='';
			}
			else {
				document.getElementById(id).style.display='none';
			}
		}
	}
}

function affichage_travail_jour(num_jour) {
	if(document.getElementById('travail_jour_'+num_jour)) {
		// On masque les infobulles de tous les jours
		for(i=0;i<14;i++) {
			if(document.getElementById('travail_jour_'+i)) {
				document.getElementById('travail_jour_'+i).style.display='none';
			}
		}

		// On ajuste les dimensions de l'infobulle Jour
		document.getElementById('travail_jour_'+num_jour+'_contenu_corps').style.maxHeight='20em';
		document.getElementById('travail_jour_'+num_jour+'_contenu_corps').style.overflow='auto';

		// On affiche les témoins dans l'entête de l'infobulle du jour
		// Si les liens existent, c'est qu'il y a un groupe au moins avec le type de notice correspondant
		if(document.getElementById('lien_alterne_affichage_devoirs_jour_'+num_jour)) {
			document.getElementById('lien_alterne_affichage_devoirs_jour_'+num_jour).style.display='';
		}
		if(document.getElementById('lien_alterne_affichage_notice_privee_jour_'+num_jour)) {
			document.getElementById('lien_alterne_affichage_notice_privee_jour_'+num_jour).style.display='';
		}
		if(document.getElementById('lien_alterne_affichage_compte_rendu_jour_'+num_jour)) {
			document.getElementById('lien_alterne_affichage_compte_rendu_jour_'+num_jour).style.display='';
		}

		// On affiche tous les enseignements du jour:
		for(i=0;i<tab_grp.length;i++) {
			if(document.getElementById('travail_jour_'+num_jour+'_groupe_'+tab_grp[i])) {
				document.getElementById('travail_jour_'+num_jour+'_groupe_'+tab_grp[i]).style.display='';

				// On veille à ce que les sous-div de compte-rendu et devoirs soient visibles:
				if(document.getElementById('travail_jour_'+num_jour+'_groupe_'+tab_grp[i]+'_devoirs')) {
					document.getElementById('travail_jour_'+num_jour+'_groupe_'+tab_grp[i]+'_devoirs').style.display='';
				}
				if(document.getElementById('travail_jour_'+num_jour+'_groupe_'+tab_grp[i]+'_compte_rendu')) {
					document.getElementById('travail_jour_'+num_jour+'_groupe_'+tab_grp[i]+'_compte_rendu').style.display='';
				}
			}
		}

		// On affiche et positionne l'infobulle du jour:
		afficher_div('travail_jour_'+num_jour,'y',10,0)
	}
}

function affichage_notices_tel_groupe(num_jour, id_groupe) {

	if(document.getElementById('travail_jour_'+num_jour)) {
		// On limite la hauteur de l'infobulle avec apparition de la barre de scrolling vertical:
		if(document.getElementById('travail_jour_'+num_jour+'_contenu_corps')) {
			document.getElementById('travail_jour_'+num_jour+'_contenu_corps').style.maxHeight='20em';
			document.getElementById('travail_jour_'+num_jour+'_contenu_corps').style.overflow='auto';
		}

		/*
		if(document.getElementById('travail_jour_'+num_jour).style.display=='') {
			document.getElementById('travail_jour_'+num_jour).style.display='none';
		}
		else {
		*/
			// On masque les infobulles de tous les jours
			for(i=0;i<14;i++) {
				if(document.getElementById('travail_jour_'+i)) {
					document.getElementById('travail_jour_'+i).style.display='none';
				}
			}

			if(document.getElementById('travail_jour_'+num_jour)) {
				// On masque tous les enseignements
				for(i=0;i<tab_grp.length;i++) {
					if(document.getElementById('travail_jour_'+num_jour+'_groupe_'+tab_grp[i])) {
						document.getElementById('travail_jour_'+num_jour+'_groupe_'+tab_grp[i]).style.display='none';
					}
				}

				// On affiche l'enseignement demandé
				if(document.getElementById('travail_jour_'+num_jour+'_groupe_'+id_groupe)) {
					document.getElementById('travail_jour_'+num_jour+'_groupe_'+id_groupe).style.display='';

					// Initialisation: On affiche tous les témoins:
					if(document.getElementById('lien_alterne_affichage_devoirs_jour_'+num_jour)) {
						document.getElementById('lien_alterne_affichage_devoirs_jour_'+num_jour).style.display='';
					}
					if(document.getElementById('lien_alterne_affichage_notice_privee_jour_'+num_jour)) {
						document.getElementById('lien_alterne_affichage_notice_privee_jour_'+num_jour).style.display='';
					}
					if(document.getElementById('lien_alterne_affichage_compte_rendu_jour_'+num_jour)) {
						document.getElementById('lien_alterne_affichage_compte_rendu_jour_'+num_jour).style.display='';
					}

					// On masque les témoins dans l'entête de l'infobulle du jour si il n'y a pas le type de notice correspondant pour le groupe affiché
					if(!document.getElementById('travail_jour_'+num_jour+'_groupe_'+id_groupe+'_devoirs')) {
						if(document.getElementById('lien_alterne_affichage_devoirs_jour_'+num_jour)) {
							document.getElementById('lien_alterne_affichage_devoirs_jour_'+num_jour).style.display='none';
						}
					}

					if(!document.getElementById('travail_jour_'+num_jour+'_groupe_'+id_groupe+'_notice_privee')) {
						if(document.getElementById('lien_alterne_affichage_notice_privee_jour_'+num_jour)) {
							document.getElementById('lien_alterne_affichage_notice_privee_jour_'+num_jour).style.display='none';
						}
					}

					if(!document.getElementById('travail_jour_'+num_jour+'_groupe_'+id_groupe+'_compte_rendu')) {
						if(document.getElementById('lien_alterne_affichage_compte_rendu_jour_'+num_jour)) {
							document.getElementById('lien_alterne_affichage_compte_rendu_jour_'+num_jour).style.display='none';
						}
					}
				}

				// On affiche l'infobulle du jour
				//document.getElementById('travail_jour_'+num_jour).style.display='';
				afficher_div('travail_jour_'+num_jour,'y',10,0);
			}
		//}
	}
}

function cacher_div_motif(motif) {
	tab_div=document.getElementsByTagName('div');
	for(i=0;i<tab_div.length;i++) {
		div_courant=tab_div[i];

		if(div_courant.getAttribute('id')) {
			id_courant=div_courant.getAttribute('id');
			if(id_courant.substring(0,motif.length)==motif) {
				cacher_div(id_courant);
			}
		}
	}
}

