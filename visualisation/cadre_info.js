/*
* $Id$
*/
IE4 = (document.all) ? 1 : 0;
NS4 = (document.layers) ? 1 : 0;
moz = (document.getElementById) ? 1 : 0;

var temporisation=false;

function position(e) {
	if (navigator.appName.substring(0,3) == "Net") {
		x = e.pageX;
		y = e.pageY;
	}
	else {
		x = event.x+document.body.scrollLeft;
		y = event.y+document.body.scrollTop;
	}
	temporisation=true;
}

if(navigator.appName.substring(0,3) == "Net") document.captureEvents(Event.MOUSEMOVE);

document.onmousemove = position;

function div_info(div_pref,num,statut){

	if(document.getElementById(div_pref+num)){
		if(statut=='affiche'){
			//if(x!="undefined"){
			if(temporisation){
				document.getElementById(div_pref+num).style.display='';

				gauche=eval(x+10+300);
				if(gauche>window.innerWidth){
					xPos=eval(window.innerWidth-300-20);
				}
				else{
					xPos=eval(x+10);
				}

				if(moz){
					document.getElementById(div_pref+num).style.left=xPos+"px";
					document.getElementById(div_pref+num).style.top=eval(y+10)+"px";
				}
				else{
					document.getElementById(div_pref+num).style.left=xPos;
					document.getElementById(div_pref+num).style.top=eval(y+10);
				}

				//document.getElementById('id_truc').value='x='+x+' et y='+y;
				//document.getElementById('id_truc').value=window.innerWidth+' '+xPos;
			}
		}
		else{
			document.getElementById(div_pref+num).style.display='none';
		}
	}
}
