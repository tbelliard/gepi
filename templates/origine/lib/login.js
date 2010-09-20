/*
 * @version $Id: login.js $
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

 
function observeur(){
	document.getElementById('login').focus();
	if (document.getElementById('bt_gabarit') != null) {
	    document.getElementById('bt_gabarit').className="cache";
	}
	if (document.getElementById('titre_switcher') != null) {
	    document.getElementById('titre_switcher').className="cache";
	}

	
	new Event.observe("template", 'change', changetemplate,false);	
  //new Event.observe("info_vie_privee", 'click', traiterEvenement,false);
  // on stoppe l'évènement
  
}

function changetemplate(){
	document.getElementById('switcher').submit();
}

function traiterEvenement(){
	centrerpopup('gestion/info_vie_privee.php',700,480,'scrollbars=yes,statusbar=no,resizable=yes');
	return false;
}


