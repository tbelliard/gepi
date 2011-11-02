/**
 *
 * @version $Id: include.js 7541 2011-07-31 16:26:31Z dblanqui $
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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
function SetAllCheckBoxes(FormName, FieldName, IdMatchString, CheckValue)
{
	if(!document.forms[FormName])
		return;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes) {
		return;
	}
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes) {
		if (objCheckBoxes.id.match(IdMatchString)) {
			objCheckBoxes.checked = CheckValue;
		}
	} else {
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++) {
			if (objCheckBoxes[i].id.match(IdMatchString)) {
			    objCheckBoxes[i].checked = CheckValue;
			}
		}
	}
}

function SetAllTextFields(FormName, FieldName, IdMatchString, StringValue)
{
	if(!document.forms[FormName])
		return;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes) {
		objCheckBoxes = document.forms[FormName].elements;
	}
	if(!objCheckBoxes) {
		return;
	}
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes) {
		if (objCheckBoxes.id.match(IdMatchString) && objCheckBoxes.name.match(FieldName)) {
			objCheckBoxes.value = StringValue;
		}
	} else {
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++) {
			if (objCheckBoxes[i].id.match(IdMatchString) && objCheckBoxes[i].name.match(FieldName)) {
			    objCheckBoxes[i].value = StringValue;
			}
		}
	}
}

function pop_it(the_form) {
   my_form = eval(the_form)
   window.open("./index.php", "popup");
   my_form.target = "popup";
   my_form.submit();
}

function refresh(compteur,affichage,tri,sans_commentaire,ods2,non_traitees,nom_eleve,texte_conditionnel,filtrage,type_filtrage,ndj,ndjnj,nr) {
    window.location.href = './bilan_individuel.php?cpt_classe='+compteur+'&affichage='+affichage+'&tri='+tri+'&sans_commentaire='+sans_commentaire+'&ods2='+ods2+'&non_traitees='+non_traitees+'&nom_eleve='+nom_eleve
    +'&texte_conditionnel='+texte_conditionnel+'&filtrage='+filtrage+'&type_filtrage='+type_filtrage+'&ndj='+ndj+'&ndjnj='+ndjnj+'&nr='+nr;
}
function showwindow(url,title){
 
 var Height=document.documentElement.clientHeight-75;
 var Width=document.documentElement.clientWidth-75;
 var win = new Window({title: title, width:Width , height:Height, url: url, showEffectOptions: {duration:0.5}}); 
 win.showCenter(); 
 myObserver={
     onClose:function(){
         Windows.removeObserver(this);
         window.document.forms['absences_du_jour'].submit();
     }
 }
 Windows.addObserver(myObserver); 
}
function postwindow(the_form,title){
 my_form = eval(the_form)
 var Height=document.documentElement.clientHeight-75;
 var Width=document.documentElement.clientWidth-75;
 var element = document.createElement('input');
		element.setAttribute('type', 'hidden');
		element.setAttribute('name', 'menu');
		element.setAttribute('value', 'false');
		my_form.appendChild(element);
 var win = new Window({className:"dialog", title: title, url:'./index.php',width:Width , height:Height, showEffectOptions: {duration:0.5}});
 win.showCenter();
 win.name=win.getId()+'_content';
 my_form.target=win.name;
 my_form.submit(); 
 
 myObserver={     
     onClose:function(){
         Windows.removeObserver(this);
         window.document.forms['absences_du_jour'].submit();
     }    
     
 }
 Windows.addObserver(myObserver); 
}

function postform(the_form){ 
 my_form = eval(the_form)
 my_form.submit();
}