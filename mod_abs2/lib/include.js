/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
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