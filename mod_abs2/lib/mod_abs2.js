function observeur(){
  //Event.observe(document, 'click', traiterEvenement);
  Event.observe(document, 'keydown', func_KeyDown);
}

function voirElementTouch(event){
  TouchKeyDown = (window.Event) ? event.which : event.keyDown;
  alert(TouchKeyDown);
}

function afficherDiv(id){Element.show(id);}
function cacherDiv(id){Element.hide(id);}
function inverserDiv(id){Element.toggle(id);}

function func_KeyDown(event, script, type){
  TouchKeyDown = (window.Event) ? event.which : event.keyDown;
  if(TouchKeyDown == 113){
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

/*
function traiterEvenement(event){
  var elementCliquer = Event.element(event);
  var premierSelectCliquer = Event.findElement(event,"select") ? Event.findElement(event,"select") : null;
  var ulCliquer = Event.findElement(event,"ul") ? Event.findElement(event,"ul") : null;
  var affPremierSelectCliquer = premierSelectCliquer ? premierSelectCliquer.name : 'aucun';

  if (ulCliquer !== null && ulCliquer.id == 'menutabs'){
    teste(event);
  }else
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
*/