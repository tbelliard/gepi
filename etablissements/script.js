
function bascule(elem)
{
   alert(elem);
   etat=document.getElementById(elem).style.display;
   if(etat=="none"){
	 document.getElementById(elem).style.display="block";
   }
   else{
	 document.getElementById(elem).style.display="none";
   }
}