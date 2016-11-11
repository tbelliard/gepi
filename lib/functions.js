/*
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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

// fonction permettant l'utilisation des touches flèche vers le haut ou vers le bas
// pour passer d'un champ à un autre
function clavier(n,e){
     touche= e.keyCode ;

          if (touche == '38') {
              id="n";
              id=id.concat((parseInt(n.substr(1))-1).toString(10));
              if (document.getElementById(id)) document.getElementById(id).focus();
          }
          if (touche == '40') {
              id="n";
              id=id.concat((parseInt(n.substr(1))+1).toString(10));
              if (document.getElementById(id)) document.getElementById(id).focus();
          }
   }



function clavier_2(n,e,vmin,vmax){
	// Fonction destinée à incrémenter/décrémenter le champ courant entre 0 et 255 (pour des composantes de couleurs)
	// Modifié pour aller de vmin à vmax
	touche= e.keyCode ;
	//alert('touche='+touche);
	if (touche == '40') {
		valeur=document.getElementById(n).value;
		if(valeur!='') {
			if(valeur>vmin){
				valeur--;
				document.getElementById(n).value=valeur;
			}
		}
		else {
			valeur=vmax;
			document.getElementById(n).value=valeur;
		}
	}
	else{
		if (touche == '38') {
			valeur=document.getElementById(n).value;
			if(valeur!='') {
				if(valeur<vmax){
					valeur++;
					document.getElementById(n).value=valeur;
				}
			}
			else {
				valeur=vmin;
				document.getElementById(n).value=valeur;
			}
		}
		else{
			if(touche == '34'){
				valeur=document.getElementById(n).value;
				if(valeur>vmin+10){
					valeur=valeur-10;
				}
				else{
					valeur=vmin;
				}
				document.getElementById(n).value=valeur;
			}
			else{
				if(touche == '33'){
					valeur=document.getElementById(n).value;
					if(valeur<vmax-10){
						//valeur=valeur+10;
						//valeur+=10;
						valeur=eval(valeur)+10;
					}
					else{
						valeur=vmax;
					}
					document.getElementById(n).value=valeur;
				}
			}
		}
	}
}


function clavier_3(n,e,vmin,vmax,increment){
	// Fonction destinée à incrémenter/décrémenter le champ courant entre 1 et 0 (pour des pourcentages)
	// Modifié pour aller de vmin à vmax
	touche= e.keyCode ;
	//alert('touche='+touche);
	if (touche == '40') {
		valeur=document.getElementById(n).value;
		if(valeur>vmin){
			//valeur=eval(eval(valeur)-eval(increment));
			valeur=Math.round(((Math.round(valeur*100)/100)-(Math.round(increment*100)/100))*100)/100;
			document.getElementById(n).value=valeur;
		}
	}
	else{
		if (touche == '38') {
			valeur=document.getElementById(n).value;
			if(valeur<vmax){
				valeur=Math.round(((Math.round(valeur*100)/100)+(Math.round(increment*100)/100))*100)/100;
				document.getElementById(n).value=valeur;
			}
		}
		else{
			if(touche == '34'){
				valeur=document.getElementById(n).value;
				if(valeur>vmin+10*increment){
						valeur=Math.round(((Math.round(valeur*100)/100)-10*(Math.round(increment*100)/100))*100)/100;
				}
				else{
					valeur=vmin;
				}
				document.getElementById(n).value=valeur;
			}
			else{
				if(touche == '33'){
					valeur=document.getElementById(n).value;
					if(valeur<vmax-10*increment){
						//valeur=valeur+10;
						//valeur+=10;
						valeur=Math.round(((Math.round(valeur*100)/100)+10*(Math.round(increment*100)/100))*100)/100;
					}
					else{
						valeur=vmax;
					}
					document.getElementById(n).value=valeur;
				}
			}
		}
	}
}

function minutes2HHMM(nb_minutes) {
	h=Math.floor(nb_minutes/60);
	m=nb_minutes-h*60;
	if(h<10) {h='0'+h;}
	if(m<10) {m='0'+m;}
	return h+':'+m;
}

function HHMM(heure,op,nb) {
	// heure est au format HH:MM, op est '-' ou '+' et nb un entier

	if(heure=="") {
		tmp_date = new Date()
		heure=tmp_date.getHours()+":"+tmp_date.getMinutes();
	}
	heure=heure.replace("h", ":");
	heure=heure.replace("H", ":");

	var exp=new RegExp("^[0-9]{1,2}:[0-9]{0,2}$","g");
	if (exp.test(heure)) {
		tab=heure.split(':');
		nb_minutes=eval(tab[0])*60+eval(tab[1]);
	
		if(op=='-') {
			if(nb_minutes-nb<0) {
				nb_minutes=1440+nb_minutes;
			}
			nb_minutes=(nb_minutes-nb)%1440;
			// 24*60=1440
		}
		else {
			nb_minutes=(nb_minutes+nb)%1440;
		}
	
		return minutes2HHMM(nb_minutes);
	}
	else {
		//alert("Le format de l'heure "+heure+" est invalide");
		return heure;
	}
}

function clavier_heure(n,e) {
	// Fonction destinée à incrémenter/décrémenter le champ courant au format HH:MM
	touche= e.keyCode ;
	//alert('touche='+touche);
	// Flèche BAS
	if (touche == '40') {
		valeur=document.getElementById(n).value;
		document.getElementById(n).value=HHMM(valeur,'-',1);
		changement();
	}
	else{
		// Flèche HAUT
		if (touche == '38') {
			valeur=document.getElementById(n).value;
			document.getElementById(n).value=HHMM(valeur,'+',1);
			changement();
		}
		else{
			// Flèche PageDOWN
			if(touche == '34'){
				valeur=document.getElementById(n).value;
				document.getElementById(n).value=HHMM(valeur,'-',10);
				changement();
			}
			else{
				// Flèche PageUP
				if(touche == '33'){
					valeur=document.getElementById(n).value;
					document.getElementById(n).value=HHMM(valeur,'+',10);
					changement();
				}
			}
		}
	}
}

function clavier_heure2(n,e,delta1,delta2) {
	// Fonction destinée à incrémenter/décrémenter le champ courant au format HH:MM
	touche= e.keyCode ;
	//alert('touche='+touche);
	// Flèche BAS
	if (touche == '40') {
		valeur=document.getElementById(n).value;
		document.getElementById(n).value=HHMM(valeur,'-',delta1);
		changement();
	}
	else{
		// Flèche HAUT
		if (touche == '38') {
			valeur=document.getElementById(n).value;
			document.getElementById(n).value=HHMM(valeur,'+',delta1);
			changement();
		}
		else{
			// Flèche PageDOWN
			if(touche == '34'){
				valeur=document.getElementById(n).value;
				document.getElementById(n).value=HHMM(valeur,'-',delta2);
				changement();
			}
			else{
				// Flèche PageUP
				if(touche == '33'){
					valeur=document.getElementById(n).value;
					document.getElementById(n).value=HHMM(valeur,'+',delta2);
					changement();
				}
			}
		}
	}
}

function jour_precedent(cur_an,cur_mois,cur_jour) {
	var jour;
	var mois;
	var an;
	var cur_jour = parseInt(cur_jour);
	var cur_mois = parseInt(cur_mois);
	var cur_an = parseInt(cur_an);
	var fev;
	if (cur_an%4 == 0 && cur_an%100 !=0 || cur_an%400 == 0) {
		fev = 29;
	} else {
		fev = 28;
	}
	// Nombre de jours pour chaque mois
	var nbJours = new Array(31,fev,31,30,31,30,31,31,30,31,30,31);
	if (cur_mois == 1) {
		if (cur_jour == 1) {
			an = cur_an - 1;
			mois = 12;
			jour = nbJours[mois-1]; // mois en cours
		} else {
			//alert(cur_mois+'-2='+eval(cur_mois-2)+' soit nbJours[cur_mois-2]='+nbJours[cur_mois-2])
			an = cur_an;
			//jour = nbJours[cur_mois-2]; // mois précédent
			jour = cur_jour-1; // mois précédent
			mois = cur_mois;
		}
	} else {
		if (cur_jour == 1) {
			an = cur_an;
			mois = cur_mois - 1;
			jour = nbJours[cur_mois-2]; // mois précédent
		} else {
			jour = cur_jour -1 ;
			mois = cur_mois;
			an = cur_an;
		}
	}

	//alert('Traitement jour_precedent('+cur_an+','+cur_mois+','+cur_jour+')='+jour+'/'+mois+'/'+an);

	var tab=new Array(an,mois,jour);
	return tab;
}

function jour_suivant(cur_an,cur_mois,cur_jour) {
	var jour;
	var mois;
	var an;
	var cur_jour = parseInt(cur_jour);
	var cur_mois = parseInt(cur_mois);
	var cur_an = parseInt(cur_an);
	var fev;
	if (cur_an%4 == 0 && cur_an%100 !=0 || cur_an%400 == 0) {
		fev = 29;
	} else {
		fev = 28;
	}
	var nbJours = new Array(31,fev,31,30,31,30,31,31,30,31,30,31);
	if (cur_mois == 12) { //décembre
		if (cur_jour == nbJours[cur_mois-1]) { // dernier jour du mois
			an = cur_an + 1;
			mois = 1;
			jour = 1;
		} else {
			an = cur_an;
			jour = cur_jour + 1;
			mois = cur_mois;
		}
	} else {
		if (cur_jour == nbJours[cur_mois-1]) {
			an = cur_an;
			mois = cur_mois + 1;
			jour = 1; // mois précédent
		} else {
			jour = cur_jour + 1;
			mois = cur_mois;
			an = cur_an;
		}
	}

	var tab=new Array(an,mois,jour);
	return tab;
}

function decalage_date(date,sens,nbj) {
	/*
	cur_date=new Date();
	cur_jour=cur_date.getDate();
	cur_mois=eval(cur_date.getMonth()+1);
	cur_an=cur_date.getYear();
	if(cur_an<1900) {cur_an=eval(cur_an+1900);}
	*/
	if(date=="") {
		tmp_date=new Date();
		date=tmp_date.getDate()+"/"+(tmp_date.getMonth()+1)+"/"+tmp_date.getFullYear();
	}

	cur_date=date.split('/');
	cur_jour=cur_date[0];
	if(cur_jour.substr(0,1)=='0') {cur_jour=cur_jour.substr(1);}
	cur_mois=cur_date[1];
	if(cur_mois.substr(0,1)=='0') {cur_mois=cur_mois.substr(1);}
	cur_an=cur_date[2];

	if(!checkdate(cur_mois, cur_jour, cur_an)) {
		cur_date=new Date();
		cur_jour=cur_date.getDate();
		cur_mois=eval(cur_date.getMonth()+1);
		cur_an=cur_date.getYear();
		if(cur_an<1900) {cur_an=eval(cur_an+1900);}
	}

	//alert(cur_jour+'/'+cur_mois+'/'+cur_an)
	if(sens=='-') {
		tab=new Array();
		//alert('jour_precedent('+cur_an+','+cur_mois+','+cur_jour+');')
		tab=jour_precedent(cur_an,cur_mois,cur_jour);
		//alert(tab[2]+'/'+tab[1]+'/'+tab[0])

		if(tab[1]<10) {tab[1]='0'+tab[1];}
		if(tab[2]<10) {tab[2]='0'+tab[2];}
		var date_modifiee=tab[2]+'/'+tab[1]+'/'+tab[0]

		return date_modifiee;
	}
	else {
		tab=new Array();
		tab=jour_suivant(cur_an,cur_mois,cur_jour);

		if(tab[1]<10) {tab[1]='0'+tab[1];}
		if(tab[2]<10) {tab[2]='0'+tab[2];}
		var date_modifiee=tab[2]+'/'+tab[1]+'/'+tab[0]

		return date_modifiee;
	}
}

function clavier_date(n,e) {
	// Fonction destinée à incrémenter/décrémenter le champ courant au format JJ/MM/YYYY
	touche= e.keyCode ;
	//alert('touche='+touche);
	// Flèche BAS
	if (touche == '40') {
		valeur=document.getElementById(n).value;
		document.getElementById(n).value=decalage_date(valeur,'-',1);
		changement();
	}
	else{
		// Flèche HAUT
		if (touche == '38') {
			valeur=document.getElementById(n).value;
			document.getElementById(n).value=decalage_date(valeur,'+',1);
			changement();
		}
		/*
		else{
			// Flèche PageDOWN
			if(touche == '34'){
				valeur=document.getElementById(n).value;
				document.getElementById(n).value=decalage_date(valeur,'-',7);
			}
			else{
				// Flèche PageUP
				if(touche == '33'){
					valeur=document.getElementById(n).value;
					document.getElementById(n).value=decalage_date(valeur,'+',7);
				}
			}
		}
		*/
	}
}

function clavier_date2(n,e) {
	// Fonction destinée à incrémenter/décrémenter le champ courant au format JJ/MM/YYYY HH:MM
	touche= e.keyCode ;
	//alert('touche='+touche);

	date_heure_courante=document.getElementById(n).value;

	if(date_heure_courante=="") {
		cur_date="";
		cur_heure="";
	}
	else {
		if(date_heure_courante.indexOf(" ")!=-1) {
			tab_date_heure=date_heure_courante.split(' ');
			cur_date=tab_date_heure[0];
			cur_heure=tab_date_heure[1];
		}
		else {
			cur_date=date_heure_courante;
			cur_heure="";
		}
	}

	if(cur_date=="") {
		tmp_date=new Date();
		cur_date=tmp_date.getDate()+"/"+(tmp_date.getMonth()+1)+"/"+tmp_date.getFullYear();
	}

	if(cur_heure=="") {
		tmp_date=new Date();
		cur_heure=tmp_date.getHours()+":"+tmp_date.getMinutes();
	}

	// Flèche BAS: -30min
	if (touche == '40') {
		document.getElementById(n).value=cur_date+" "+HHMM(cur_heure,'-',30);
		changement();
	}
	else {
		// Flèche HAUT: +30min
		if (touche == '38') {
			document.getElementById(n).value=cur_date+" "+HHMM(cur_heure,'+',30);
			changement();
		}
		else {
			// Flèche PageDOWN: -1 jour
			if(touche == '34'){
				document.getElementById(n).value=decalage_date(cur_date,'-',1)+" "+cur_heure;
				changement();
			}
			else {
				// Flèche PageUP: +1 jour
				if(touche == '33'){
					document.getElementById(n).value=decalage_date(cur_date,'+',1)+" "+cur_heure;
					changement();
				}
			}
		}
	}
}

function clicMenu(num)
{
  var fermer;
  var ouvrir;
  //Booléen reconnaissant le navigateur
  isIE = (document.all)
  isNN6 = (!isIE) && (document.getElementById)
  //Compatibilité : l'objet menu est détecté selon le navigateur

  if (isIE) menu = document.all['menu' + num];
  if (isNN6) menu = document.getElementById('menu' + num);
  if ((isIE) && (document.all['fermer'])) fermer = document.all['fermer'];
  if ((isNN6) && (document.getElementById('fermer')))  fermer = document.getElementById('fermer');
  if ((isIE) && (document.all['ouvrir'])) ouvrir = document.all['ouvrir'];
  if ((isNN6) && (document.getElementById('ouvrir'))) ouvrir = document.getElementById('ouvrir');

  // On ouvre ou ferme
  if (menu.style.display == "none")
  {
      // Cas ou le tableau est caché
    menu.style.display = ""
  }
  else
  {
      // On le cache
    menu.style.display = "none"
   }

  if (fermer)
  if (fermer.style.display == "none")
  {
      // Cas ou le tableau est caché
    fermer.style.display = ""
  }
  else
  {
      // On le cache
    fermer.style.display = "none"
   }

   if (ouvrir)
     if (ouvrir.style.display == "none")
  {
      // Cas ou le tableau est caché
    ouvrir.style.display = ""
  }
  else
  {
      // On le cache
    ouvrir.style.display = "none"
   }


}


function VerifChargement() {
    if (chargement == false) {
        alert("Veuillez attendre la fin du chargement de la page pour valider");
        return false;
    } else {
        return true;
    }
}


/**
 * Displays an confirmation box beforme to submit a query
 * This function is called while clicking links
 *
 * @param   object   the link
 * @param   object   the sql query to submit
 * @param   object   the message to display
 *
 * @return  boolean  whether to run the query or not
 */
function confirmlink(theLink, theSqlQuery, themessage)
{

    var is_confirmed = confirm(themessage + ' :\n' + theSqlQuery);
    if (is_confirmed) {
        theLink.href += '&js_confirmed=1';
    }
    return is_confirmed;
} // end of the 'confirmLink()' function


function centrerpopup(page,largeur,hauteur,options)
{
// les options :
//    * left=100 : Position de la fenêtre par rapport au bord gauche de l'écran.
//    * top=50 : Position de la fenêtre par rapport au haut de l'écran.
//    * resizable=x : Indique si la fenêtre est redimensionnable.
//    * scrollbars=x : Indique si les barres de navigations sont visibles.
//    * menubar=x : Indique si la barre des menus est visible.
//    * toolbar=x : Indique si la barre d'outils est visible.
//    * directories=x : Indique si la barre d'outils personnelle est visible.
//    * location=x : Indique si la barre d'adresse est visible.
//    * status=x : Indique si la barre des status est visible.
//
// x = yes ou 1 si l'affirmation est vrai ; no ou 0 si elle est fausse.

var top=(screen.height-hauteur)/2;
var left=(screen.width-largeur)/2;
window.open(page,"","top="+top+",left="+left+",width="+largeur+",height="+hauteur+",directories=no,toolbar=no,menubar=no,location=no,"+options);
}

// Fonction récupéré dans le header avec <!-- christian référencement de GEPI -->
function ouvre_popup_reference(url){
	eval("window.open(url,'fen','width=500,height=600,menubar=no,scrollbars=yes')");
	fen.focus();
}

function confirm_abandon(theLink, thechange, themessage)
{
    if (!(thechange)) thechange='no';
    // Confirmation is not required in the configuration file
    if (thechange != 'yes') {
        return true;
    // Si la variable confirmMsg est vide, alors in n'y a pas de demande de confirmation
    }
    var is_confirmed = confirm(themessage);
    return is_confirmed;
} // end of the 'confirmLink()' function

/**
 * Displays an error message if an element of a form hasn't been completed and should be
 *
 * @param   object   the form
 * @param   string   the name of the form field to put the focus on
 *
 * @return  boolean  whether the form field is empty or not
 */
function emptyFormElements(theForm, theFieldName)
{
    var isEmpty  = 1;
    var theField = theForm.elements[theFieldName];
    // Whether the replace function (js1.2) is supported or not
    var isRegExp = (typeof(theField.value.replace) != 'undefined');

    if (!isRegExp) {
        isEmpty      = (theField.value == '') ? 1 : 0;
    } else {
        var space_re = new RegExp('\\s+');
        isEmpty      = (theField.value.replace(space_re, '') == '') ? 1 : 0;
    }
    if (isEmpty) {
        theForm.reset();
        theField.select();
        alert(errorMsg0);
        theField.focus();
        return false;
    }

    return true;
} // end of the 'emptyFormElements()' function

/**
 * Ensures a value submitted in a form is numeric and is in a range
 *
 * @param   object   the form
 * @param   string   the name of the form field to check
 * @param   integer  the minimum authorized value
 * @param   integer  the maximum authorized value
 *
 * @return  boolean  whether a valid number has been submitted or not
 */

function checkFormElementInRange(theForm, theFieldName, min, max)
{
    var theField         = theForm.elements[theFieldName];
    var val              = parseInt(theField.value);

    if (typeof(min) == 'undefined') {
        min = 0;
    }
    if (typeof(max) == 'undefined') {
        max = Number.MAX_VALUE;
    }

    // It's not a number
    if (isNaN(val)) {
        theField.select();
        alert(errorMsg1);
        theField.focus();
        return false;
    }
    // It's a number but it is not between min and max
    else if (val < min || val > max) {
        theField.select();
        alert(val + errorMsg2);
        theField.focus();
        return false;
    }
    // It's a valid number
    else {
        theField.value = val;
    }

    return true;
} // end of the 'checkFormElementInRange()' function

function checkCapsLock( e ) {
	var myKeyCode=0;
	var myShiftKey=false;
	var myMsg='Le verrouillage des majuscules est activé.\n\nPour éviter toute erreur lors de la saisie du mot de passe, vous devriez le désactiver en pressant à nouveau la touche "caps lock" (ou "ver. maj") à gauche sur votre clavier.';

	// Internet Explorer 4+
	if ( document.all ) {
		myKeyCode=e.keyCode;
		myShiftKey=e.shiftKey;

	// Netscape 4
	} else if ( document.layers ) {
		myKeyCode=e.which;
		myShiftKey=( myKeyCode == 16 ) ? true : false;

	// Netscape 6
	} else if ( document.getElementById ) {
		myKeyCode=e.which;
		myShiftKey=( myKeyCode == 16 ) ? true : false;

	}

	// Upper case letters are seen without depressing the Shift key, therefore Caps Lock is on
	if ( ( myKeyCode >= 65 && myKeyCode <= 90 ) && !myShiftKey ) {
		alert( myMsg );

	// Lower case letters are seen while depressing the Shift key, therefore Caps Lock is on
	} else if ( ( myKeyCode >= 97 && myKeyCode <= 122 ) && myShiftKey ) {
		alert( myMsg );

	}
}

// The two functions below have been taken from http://www.howtocreate.co.uk/jslibs/htmlhigh/capsDetect.html
// Feel free to visit this site if you want more information or more free javascripts

var capsError = 'Le verrouillage des majuscules est activé.\n\nPour éviter toute erreur lors de la saisie du mot de passe, vous devriez le désactiver en pressant à nouveau la touche "caps lock" (ou "ver. maj") à gauche sur votre clavier.';

function capsDetect( e ) {
	if( !e ) { e = window.event; } if( !e ) { MWJ_say_Caps( false ); return; }
	//what (case sensitive in good browsers) key was pressed
	var theKey = e.which ? e.which : ( e.keyCode ? e.keyCode : ( e.charCode ? e.charCode : 0 ) );
	//was the shift key was pressed
	var theShift = e.shiftKey || ( e.modifiers && ( e.modifiers & 4 ) ); //bitWise AND
	//if upper case, check if shift is not pressed. if lower case, check if shift is pressed
	MWJ_say_Caps( ( theKey > 64 && theKey < 91 && !theShift ) || ( theKey > 96 && theKey < 123 && theShift ) );
}

function MWJ_say_Caps( oC ) {
	if( typeof( capsError ) == 'string' ) { if( oC ) { alert( capsError ); } } else { capsError( oC ); }
}

function capsDetect2( e ) {
	if( !e ) { e = window.event; } if( !e ) { MWJ_say_Caps2( false ); return; }
	//what (case sensitive in good browsers) key was pressed
	var theKey = e.which ? e.which : ( e.keyCode ? e.keyCode : ( e.charCode ? e.charCode : 0 ) );
	//was the shift key was pressed
	var theShift = e.shiftKey || ( e.modifiers && ( e.modifiers & 4 ) ); //bitWise AND
	//if upper case, check if shift is not pressed. if lower case, check if shift is pressed
	MWJ_say_Caps2( ( theKey > 64 && theKey < 91 && !theShift ) || ( theKey > 96 && theKey < 123 && theShift ) );
}


function MWJ_say_Caps2( oC ) {
	if( typeof( capsError ) == 'string' ) {
		if( oC ) { 
			if(document.getElementById('div_mdp_page_login')) {
				document.getElementById('div_mdp_page_login').style.display='';
				document.getElementById('div_mdp_page_login').innerHTML=capsError;
				//alert('plop')
			}
			else {
				alert( capsError );
			}
		}
		else { 
			if(document.getElementById('div_mdp_page_login')) {
				document.getElementById('div_mdp_page_login').style.display='none';
				document.getElementById('div_mdp_page_login').innerHTML='';
				//alert('plip')
			}
		}
	}
	else { 
		//alert('plip')
		capsError( oC ); 
	}	
}

function capsDetect3( e ) {
	// On ne fait rien.
	// Test de majuscules complètement désactivé
}

// Fonction qui permet de changer la hauteur du header en utilisant ajax et prototype
	function change_mode_header(mode, path) {
		if(mode!='y'){
			mode='n';
		}
	var url = path+'/lib/change_mode_header.php';

	// Comme les options sont multiples (voir TRAC à ce sujet), on précise que c'est un Object()
	o_options = new Object();
	// Par défaut, la méthode Ajax.Request est en post et ses paramètres dans postbody
	o_options = {postBody: 'cacher_header='+mode};
		// Et c'est prototype qui s'occupe de tester le navigateur, le type de requête et même le 'send' d'envoi...
		var laRequete = new Ajax.Request(url,o_options);
	}

	// Tant qu'on y est, la même utilisation de prototype pour le changement du display
	function changementDisplay(id1, id2){
		Element.toggle(id1);
		if (id2) {
			Element.toggle(id2);
		}
	}


/* ===== Ajout Régis =====
	Ouvre une nouvelle fenêtre à partir du bouton submit d'un formulaire
	appelé par onsubmit="ouvre_fenetre('id_de_la_form');"
	remplace target='_blank'
*/

function ouvre_fenetre(id) {
	document.getElementById(id).target = 'formulaire';
	window.open('', 'formulaire');
}

/* =====	Gestion de l'affichage des bandeaux avec prototype.js =====
	taille des bandeaux : bascule d'une taille à l'autre
	fond des bandeaux :$couleur="degrade1" --> dégradé choisi par l'administrateur (gepi_stylesheet="style" utiliser_degrade="y")
	                     $couleur="darkfade" --> gris d'origine (gepi_stylesheet="style" utiliser_degrade n'est pas à "y")
	                    $couleur="no_style" --> les styles sont désactivés (gepi_stylesheet n'est pas à "style")
*/
function modifier_taille_bandeau(){
	// Modification de la taille du bandeau
	if(Element.hasClassName("bandeau","pt_bandeau")){
		$taille_bandeau_actuelle = "pt_bandeau";
		$taille_bandeau_future = "gd_bandeau";
	}else {
		$taille_bandeau_actuelle = "gd_bandeau";
		$taille_bandeau_future = "pt_bandeau";
	}
	// On efface la classe de taille et on attribue la nouvelle
	Element.removeClassName("bandeau",$taille_bandeau_actuelle);
	Element.addClassName("bandeau", $taille_bandeau_future);

	// Modification de l'image de fond du bandeau
	if(Element.hasClassName("bandeau","no_style")){
		if(Element.hasClassName("bandeau","pt_bandeau_no_style")){
			$image_actuelle = "pt_bandeau_no_style";
		}else {
			$image_actuelle = "gd_bandeau_no_style";
		}
		$image_future=$taille_bandeau_future+"_no_style";
	}else if(Element.hasClassName("bandeau","degrade1")){
		if(Element.hasClassName("bandeau","pt_bandeau_degrade1")){
			$image_actuelle = "pt_bandeau_degrade1";
		}else {
			$image_actuelle = "gd_bandeau_degrade1";
		}
		$image_future=$taille_bandeau_future+"_degrade1";
	}else {
		if(Element.hasClassName("bandeau","pt_bandeau_darkfade")){
			$image_actuelle = "pt_bandeau_darkfade";
		}else {
			$image_actuelle = "gd_bandeau_darkfade";
		}
		$image_future=$taille_bandeau_future+"_darkfade";
	}
	// On efface la classe d'image et on attribue la nouvelle
	Element.removeClassName("bandeau",$image_actuelle);
	Element.addClassName("bandeau", $image_future);
}

function modifier_couleur_bandeau($couleur){
	// On détermine la taille actuelle
	if(Element.hasClassName("bandeau","pt_bandeau")){
		$taille_bandeau_header="pt_bandeau_";
	}else {
		$taille_bandeau_header="gd_bandeau_";
	}
	// On détermine la couleur et l'image actuelle
	if(Element.hasClassName("bandeau","no_style")){
		$couleur_actuelle="no_style";
		$image_actuelle=$taille_bandeau_header+"no_style";
	}else if(Element.hasClassName("bandeau","degrade1")){
		$couleur_actuelle="degrade1";
		$image_actuelle=$taille_bandeau_header+"degrade1";
	}else {
		$couleur_actuelle="darkfade";
		$image_actuelle=$taille_bandeau_header+"darkfade";
	}
	$couleur_future=$couleur;
	$image_future=$taille_bandeau_header+$couleur;

	// On efface les anciennes classes et on attribue les nouvelles
	Element.removeClassName("bandeau",$couleur_actuelle);
	Element.addClassName("bandeau", $couleur_future);
	Element.removeClassName("bandeau",$image_actuelle);
	Element.addClassName("bandeau", $image_future);
}

/* Fin des modifications des bandeaux */
function clic_edt(heure, jour) {
window.opener.document.getElementById('heure_debut').value=heure;
window.opener.document.getElementById('date_retenue').value=jour;
self.close();
}

// http://phpjs.org/functions/checkdate/
// http://phpjs.org/pages/license -> GPL et MIT
function checkdate( m, d, y ) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Pyerre
  // +   improved by: Theriault
  // *     example 1: checkdate(12, 31, 2000);
  // *     returns 1: true
  // *     example 2: checkdate(2, 29, 2001);
  // *     returns 2: false
  // *     example 3: checkdate(3, 31, 2008);
  // *     returns 3: true
  // *     example 4: checkdate(1, 390, 2000);
  // *     returns 4: false
  return m > 0 && m < 13 && y > 0 && y < 32768 && d > 0 && d <= (new Date(y, m, 0)).getDate();
}

// fonction permettant d'augmenter/réduire une date via onKeyDown
// Pour relever les keyCode: http://www.asquare.net/javascript/tests/KeyCode.html
function clavier_date_plus_moins(id,e){
	// Un truc bizarre: cela bloque à 26/10/2008, on n'arrive plus à augmenter???

	if(document.getElementById(id)) {
		var touche=e.keyCode;

		//if((touche == '61')||(touche == '109')) {
		if((touche == '40')||(touche == '38')) {
			var ladate=document.getElementById(id).value;

			//var pattern = new RegExp([0-3][0-9]-(0|1)[0-9]-(19|20)[0-9]{2});
			var pattern = new RegExp("[0-3][0-9]/[0-1][0-9]/[1-2][0-9][0-9][0-9]");
			if(ladate.match(pattern)) {
				var tmp_tab=ladate.split('/');
				var j=tmp_tab[0];
				var m=tmp_tab[1];
				var a=tmp_tab[2];

				if(checkdate(m,j,a)) {
					var LaDate=new Date(a,m-1,j);
					var nbmillisec=LaDate.getTime();
		
					var NewDate=new Date();
		
					// Touche + -> PB: Le + est écrit quand même
					//if (touche == '61') {
					// Touche Flèche Haut
					if (touche == '40') {
						NewDate.setTime(eval(nbmillisec+86400000));
					}
					// Touche -
					//if (touche == '109') {
					// Touche Flèche Bas
					if (touche == '38') {
						NewDate.setTime(eval(nbmillisec-86400000));
					}
		
					var j=NewDate.getDate();
					var t='_'+j;
					if(t.length==2) {j='0'+j;}
					var m=eval(NewDate.getMonth()+1);
					//var m=NewDate.getMonth();
					//alert(m.length)
					// m est un nombre, la méthode length porte sur des chaines
					//if(m.length==1) {m='0'+m;}
					var t='_'+m;
					//alert(t.length)
					if(t.length==2) {m='0'+m;}
					var a=NewDate.getYear();
					if(a<999) {a+=1900;}
		
					document.getElementById(id).value=j+'/'+m+'/'+a;
					changement();
				}
				/*
				else {
					alert('Date non valide')
				}
				*/
			}
			/*
			else {
				alert('Date mal formatée')
			}
			*/
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


function info_form(id_div){
	forms=document.getElementsByTagName('form');

	if(forms.length==0) {
		chaine='<p>Aucun formulaire dans cette page.</p>';
	}
	else {
		chaine='<p class=bold align=center>Informations sur les formulaires de cette page</p>';
		for(i=0;i<forms.length;i++) {
			name=forms[i].getAttribute('name');
			action=forms[i].getAttribute('action');
			// Pas forcément affecté...
		
			var champs_input=forms[i].getElementsByTagName('input');
			var champs_textarea=forms[i].getElementsByTagName('textarea');
	
			chaine=chaine+'<p><span class=bold>Formulaire n°'+i+' (<i>'+name+'</i>) à destination de '+action+'</span><br />';
			chaine=chaine+'&nbsp;&nbsp;&nbsp;Nombre de champs INPUT: '+champs_input.length+'<br />';
			chaine=chaine+'&nbsp;&nbsp;&nbsp;Nombre de champs TEXTAREA: '+champs_textarea.length+'</p>';
	
			//alert('Nombre de champs INPUT: '+champs_input.length+'\nNombre de champs TEXTAREA');
		}
	}
	document.getElementById(id_div).innerHTML=chaine;
}

//==============================================================================
//http://www.developpez.net/forums/d1027291/webmasters-developpement-web/general-conception-web/contribuez/regexp-corriger-ponctuation-espaces/
// Ca ne corrige pas tout
function corriger_espaces_ponctuation(chaine) {
	re1=/([.?!]|^)\s*./gi;
	re2=/[.?!](\S|$)/g;

	chaine2=chaine.replace(re1,function(w){return w.toUpperCase()}).replace(re2,". $1").replace(/(\s)+/g,"$1");

	//re3=/\s*.([.?!])/g;
	//chaine1=chaine.replace(re1,function(w){return w.toUpperCase()}).replace(re2,". $1").replace(/(\s)+/g,"$1");
	//chaine2=chaine1.replace(re3,function(w){return w.replace(/(\s)*./g,"$1.")});

	return chaine2;
}
//==============================================================================
// Fonctions adaptées de fonctions Java: 
// http://www.developpez.net/forums/d541621/java/general-java/debuter/corriger-erreurs-d-espace-commises-phrase/
function normalize_ponctuation(string_in,
			noSpaceBefore,
			spaceBefore,
			spaceAfter) {
	out = "";

	spaceNeeded = false;

	len=string_in.length;

	for (i = 0; i < len; i++) {
		c = string_in.charAt(i);
		if (c == ' ') {
			spaceNeeded = true;
		} else {
			if ((spaceNeeded && noSpaceBefore.indexOf(c) < 0)
				|| spaceBefore.indexOf(c) >= 0) {
				out += " ";
			}
			out += c;
			spaceNeeded = spaceAfter.indexOf(c) >= 0;
		}
	}
	return out.trim();
}
function corriger_espaces_ponctuation2(chaine) {
	return normalize_ponctuation(chaine, ",.", ";:?!", ",.;:?!");
}
//==============================================================================
function majuscule_apres_point(chaine) {
	retour='';

	majNeeded=false;

	// Caractère précédent
	p="";

	for(i=0;i<chaine.length;i++) {
		c=chaine.charAt(i);
		if((c==".")&&(p!=".")) {
			majNeeded=true;
			retour+=c;
		}
		else {
			if(c=='!') {
				majNeeded=true;
				retour+=c;
			}
			else {
				if(c==" ") {
					retour+=c;
				}
				else {
					if(c=="?") {
						majNeeded=true;
						retour+=c;
					}
					else {
						if(majNeeded) {
							retour+=c.toUpperCase();
						}
						else {
							retour+=c;
						}
						majNeeded=false;
					}
				}
			}
		}


		// Le caractère précédent pour le tour suivant sera
		if(majNeeded && c==" ") {
			// On ne compte pas l'espace.
		}
		else {
			p=c;
		}
	}

	return retour.trim();
}

function corriger_espaces_et_casse_ponctuation(chaine) {

	chaine2=majuscule_apres_point(chaine);
	//document.getElementById('texte2').value=chaine2;
	//alert('plop');
	chaine3=corriger_espaces_ponctuation2(chaine2);

	return chaine3;
}

