function observeur(){
  Event.observe(document, 'click', traiterEvenementClick);
  Event.observe(document, 'change', traiterEvenementChange);
  Event.observe(document, 'keydown', func_KeyDown);
}
function traiterEvenementChange(event){
  var elementCliquer = Event.element(event);
  var premierSelectCliquer = Event.findElement(event,"select") ? Event.findElement(event,"select") : null;
  var affPremierSelectCliquer = premierSelectCliquer ? premierSelectCliquer.name : 'aucun';
  if (elementCliquer.value != 'rien'){
    //alert("coucou\n"+elementCliquer.value+"\nselect\n"+premierSelectCliquer.name);
    var insertion = new Insertion.After('aff_result', '<br />On cherche &agrave; appeler '+elementCliquer.value+' CR pour la s&eacute;quence');
  }
}
function func_KeyDown(event, script, type){
  TouchKeyDown = (window.Event) ? event.which : event.keyDown;
  if(TouchKeyDown == 17){
    var insertion = new Insertion.After('aff_result', '<br />On appuie sur le touche CTRL, argh !!');
  }
}
function traiterEvenementClick(event){
  //alert("CLICK --> "+Event.element(event).value);
}
function ajaxCdt2(id, type, info, url){
	elementHTML = $(id);
	o_options = new Object();
	o_options = {postBody: '_id='+info+'&select='+type, onComplete:afficherDiv(id)};
	var laRequete = new Ajax.Updater(elementHTML,url,o_options);
}