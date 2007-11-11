/*
 * Last modification  : 18/03/2005
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

// fonction permattant l'utilisation des les touches fl?che vers le haut ou vers le bas
// pour passer d'un champ ? un autre
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



function clavier_2(n,e){
	// Fonction destinée à incrémenter/décrémenter le champ courant entre 0 et 255 (pour des composantes de couleurs)
	touche= e.keyCode ;
	//alert('touche='+touche);
	if (touche == '40') {
		valeur=document.getElementById(n).value;
		if(valeur>0){
			valeur--;
			document.getElementById(n).value=valeur;
		}
	}
	else{
		if (touche == '38') {
			valeur=document.getElementById(n).value;
			if(valeur<255){
				valeur++;
				document.getElementById(n).value=valeur;
			}
		}
		else{
			if(touche == '34'){
				valeur=document.getElementById(n).value;
				if(valeur>10){
					valeur=valeur-10;
				}
				else{
					valeur=0;
				}
				document.getElementById(n).value=valeur;
			}
			else{
				if(touche == '33'){
					valeur=document.getElementById(n).value;
					if(valeur<245){
						//valeur=valeur+10;
						//valeur+=10;
						valeur=eval(valeur)+10;
					}
					else{
						valeur=255;
					}
					document.getElementById(n).value=valeur;
				}
			}
		}
	}
}

function clicMenu(num)
{
  var fermer;
  var ouvrir;
  //Bool?en reconnaissant le navigateur
  isIE = (document.all)
  isNN6 = (!isIE) && (document.getElementById)
  //Compatibilit? : l'objet menu est d?tect? selon le navigateur

  if (isIE) menu = document.all['menu' + num];
  if (isNN6) menu = document.getElementById('menu' + num);
  if ((isIE) && (document.all['fermer'])) fermer = document.all['fermer'];
  if ((isNN6) && (document.getElementById('fermer')))  fermer = document.getElementById('fermer');
  if ((isIE) && (document.all['ouvrir'])) ouvrir = document.all['ouvrir'];
  if ((isNN6) && (document.getElementById('ouvrir'))) ouvrir = document.getElementById('ouvrir');

  // On ouvre ou ferme
  if (menu.style.display == "none")
  {
      // Cas ou le tableau est cach?
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
      // Cas ou le tableau est cach?
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
      // Cas ou le tableau est cach?
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
//    * left=100 : Position de la fen?tre par rapport au bord gauche de l'?cran.
//    * top=50 : Position de la fen?tre par rapport au haut de l'?cran.
//    * resizable=x : Indique si la fen?tre est redimensionnable.
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
	var myMsg='Le verrouillage des majuscules est activ?.\n\nPour ?viter toute erreur lors de la saisie du mot de passe, vous devriez le d?sactiver en pressant ? nouveau la touche "caps lock" (ou "ver. maj") ? gauche sur votre clavier.';

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

var capsError = 'Le verrouillage des majuscules est activ?.\n\nPour ?viter toute erreur lors de la saisie du mot de passe, vous devriez le d?sactiver en pressant ? nouveau la touche "caps lock" (ou "ver. maj") ? gauche sur votre clavier.';

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
