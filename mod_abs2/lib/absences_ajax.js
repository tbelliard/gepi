function observeur(){
	Event.observe(document, 'click', voirElementHTML);
  Event.observe(document, 'click', traiterEvenement);
  Event.observe(document, 'keydown', voirElementTouch);
}
/* Fonction utilisee pour debugguer */
function voirElementHTML(event)	{
  var elementCliquer = Event.element(event);
  var premierSelectCliquer = Event.findElement(event,"select") ? Event.findElement(event,"select") : null;
  var affPremierSelectCliquer = premierSelectCliquer ? premierSelectCliquer.name : 'aucun';
  var aff = ('<br />'+elementCliquer.tagName+' et la valeur --> '+elementCliquer.value+' et le name --> '+elementCliquer.name+' et le select --> '+affPremierSelectCliquer);
  var insertion = new Insertion.After("aff_result",aff);
}
function voirElementTouch(event){
  TouchKeyDown = (window.Event) ? event.which : event.keyDown;
  alert(TouchKeyDown);
}
function traiterEvenement(event){
  var elementCliquer = Event.element(event);
  var premierSelectCliquer = Event.findElement(event,"select") ? Event.findElement(event,"select") : null;
  var affPremierSelectCliquer = premierSelectCliquer ? premierSelectCliquer.name : 'aucun';
  if (elementCliquer.value != 'rien'){
    var tabChoix = new Array();
    tabChoix = ["choix_classe", "choix_aid", "choix_groupe"];
    if (tabChoix.indexOf(affPremierSelectCliquer) != '-1'){
      ajaxAbsence('aff_result', affPremierSelectCliquer, elementCliquer.value, 'saisir_ajax.php');
    }
  }
}
function ajaxAbsence(id, type, info, url){
	elementHTML = $(id);
	o_options = new Object();
	o_options = {postBody: '_id='+info+'&type='+type, onComplete:afficherDiv(id)};
	var laRequete = new Ajax.Updater(elementHTML,url,o_options);
}
function gestionaffAbs(id, reglage, url){
	elementHTML = $(id);
  var test = reglage.split("||");
  var nbre = test.length;
  var info='';
  if (nbre > 1){
    for(i=0 ; i<nbre ; i++){
      var info = info+$F(test[i])+'||';
    }
  }else{
    var info = $F(reglage);
  }
	o_options = new Object();
	o_options = {postBody: '_id='+info+'&type='+reglage, onComplete:afficherDiv(id)};
	var laRequete = new Ajax.Updater(elementHTML,url,o_options);
}
function afficherDiv(id){
  Element.show(id);
}
function cacherDiv(id){
  Element.hide(id);
}
function utiliseAjaxAbs(id, reglage, url){
	elementHTML = $(id);
	var info = reglage;
	o_options = new Object();
	o_options = {postBody: '_id='+info+'&type='+reglage, onComplete:afficherDiv(id)};
	var laRequete = new Ajax.Updater(elementHTML,url,o_options);
}
function func_KeyDown(event, script, type){
  TouchKeyDown = (window.Event) ? event.which : event.keyDown;
  if (TouchKeyDown == 13) {
    if (script == 1) {
      gestionaffAbs('aff_result', type, 'parametrage_ajax.php');
    }
  }
}
function decoche(id){
  var radio = document.getElementById(id);
  radio.checked = false;
}