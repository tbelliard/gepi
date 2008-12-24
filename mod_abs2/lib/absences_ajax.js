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
  alert(info);
	o_options = new Object();
	o_options = {postBody: '_id='+info+'&type='+reglage, onComplete:afficherDiv(id)};
	var laRequete = new Ajax.Updater(elementHTML,url,o_options);
}
function afficherDiv(id){
  Element.show(id);
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