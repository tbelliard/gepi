function observeur(){
	//Event.observe(document, 'click', voirElementHTML);
  Event.observe(document, 'click', traiterEvenement);
  //Event.observe(document, 'keydown', voirElementTouch);
  Event.observe(document, 'keydown', func_KeyDown);
}
/* Fonction utilisee pour debugguer 
function voirElementHTML(event)	{
  var elementCliquer = Event.element(event);
  var premierSelectCliquer = Event.findElement(event,"select") ? Event.findElement(event,"select") : null;
  var affPremierSelectCliquer = premierSelectCliquer ? premierSelectCliquer.name : 'aucun';
  var aff = ('<br />'+elementCliquer.tagName+' et la valeur --> '+elementCliquer.value+' et le name --> '+elementCliquer.name+' et le select --> '+affPremierSelectCliquer);
  var insertion = new Insertion.After("aff_result",aff);
}*/
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
    }else if(elementCliquer.id.substr(0, 6) == 'winAbs'){
      var _id = elementCliquer.id;
      var var2 = elementCliquer.id.substr(6, 30);
      ouvrirResp(_id, var2);
    }else if(elementCliquer.id.substr(0, 5) == 'decod' || elementCliquer.id.substr(0, 5) == 'decof'){
      var _id1 = 'j'+elementCliquer.id.substr(5, 10);
      var _id2 = 'el'+elementCliquer.id.substr(5, 10);
      decoche(_id1);
      coche(_id2);
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
  var ordre='';
  if (nbre > 1){
    for(i=0 ; i<nbre ; i++){
      info = info+$F(test[i])+'||';
    }
  }else{
    info = $F(reglage);
  }
  if (url == 'parametrage_ajax.php'){
    var ordre = $F('idOrder');
  }else{
    var ordre = '0';
  }
	o_options = new Object();
	o_options = {postBody: '_id='+info+'&type='+reglage+'&ordre='+ordre, onComplete:afficherDiv(id)};
	var laRequete = new Ajax.Updater(elementHTML,url,o_options);
}
function afficherDiv(id){Element.show(id);}
function cacherDiv(id){Element.hide(id);}
function inverserDiv(id){Element.toggle(id);
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
  }else if(TouchKeyDown == 113){
    //alert("ok");
    inverserDiv('idAidAbs');
  }else if(TouchKeyDown == 17){
    var insertion = new Insertion.After('aff_result', '<br />On appuie sur le touche CTRL, argh !!');
  }
}
function decoche(id){
  var radio = document.getElementById(id);
  radio.checked = false;
}
function coche(id){
  var radio = document.getElementById(id);
  radio.checked = true;
}
function ouvrirResp(id, var2){
	var win = new Window({className: "alphacube", title: "Tests sur la fiche responsables", top:170, left:100, width:700, height:500, url: "ajax_responsable.php?var="+id+"&var2="+var2, showEffectOptions: {duration:0.1}, opacity:0.98});
	win.show();
}