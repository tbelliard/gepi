<?php
/* $Id$ */

$tabcouleur=Array("aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen");


//=====================================
$liste=array('palegoldenrod',
'mistyrose',
'palegreen',
'moccasin',
'lightsteelblue',
'darkseagreen',
'olive',
'mintcream',
'lightgray',
'gray');

$chaine_couleur_classe_fut="'$liste[0]'";
for($i=1;$i<count($classe_fut)-3;$i++) {
	if(isset($liste[$i])) {
		$chaine_couleur_classe_fut.=",'$liste[$i]'";
	}
	else {
		$chaine_couleur_classe_fut.=",'$tabcouleur[$i]'";
	}
}
$chaine_couleur_classe_fut.=",'lightgray','gray','white'";
//=====================================

//=====================================
$chaine_couleur_lv1="'palegoldenrod',
'mintcream',
'mistyrose',
'palegreen',
'moccasin',
'lightsteelblue',
'darkseagreen',
'olive',
'lightgray',
'gray'";
//=====================================

$chaine_couleur_lv2="'lightgreen','lightpink','lightblue','gold','lightgray','gray','olive'";
$chaine_couleur_lv3="'purple','greenyellow','violet','chartreuse','lightgray','gray','olive'";

// Les variables $chaine_couleur_* sont utilisées pour initialiser des tableaux javascript.


$tab_profil=array('GC','C','RAS','B','TB');
// Pour le moment les valeurs testées dans les scripts javascript et les couleurs associées sont en dur dans les pages.
// A modifier...
$chaine_couleur_profil="'red','orangered','gray','green','blue'";
$chaine_profil="'GC','C','RAS','B','TB'";

//$tab_profil=array($chaine_profil);
//$tab_couleur_profil=array($chaine_couleur_profil);
$tab_couleur_profil=array('red','orangered','gray','green','blue');


function colorise_abs($abs,$nj,$ret) {
	if($abs<=10) {
		echo "<span style='color:green;'>";
	}
	elseif(($abs>10)&&($abs<=30)) {
		echo "<span style='color:orange;'>";
	}
	elseif(($abs>30)&&($abs<=50)) {
		echo "<span style='color:orangered;'>";
	}
	else {
		echo "<span style='color:red;'>";
	}
	echo $abs;
	echo "</span>";

	echo "/";

	if(($nj==0)||($abs==0)) {
		echo "<span style='color:green;'>";
	}
	else{
		$p=100*$nj/$abs;
		if($p<=20) {
			echo "<span style='color:orange;'>";
		}
		elseif(($p>20)&&($p<=50)) {
			echo "<span style='color:orangered;'>";
		}
		else {
			echo "<span style='color:red;'>";
		}
	}
	echo $nj;
	echo "</span>";

	echo "/";

	if($ret<=10) {
		echo "<span style='color:green;'>";
	}
	elseif(($ret>10)&&($ret<=30)) {
		echo "<span style='color:orange;'>";
	}
	elseif(($ret>30)&&($ret<=50)) {
		echo "<span style='color:orangered;'>";
	}
	else {
		echo "<span style='color:red;'>";
	}
	echo $ret;
	echo "</span>";

}

function necessaire_bull_simple() {
	echo "<div id='div_bull_simp' style='position: absolute; top: 220px; right: 20px; width: 700px; text-align:center; color: black; padding: 0px; border:1px solid black; display:none;'>\n";
	
		echo "<div class='infobulle_entete' style='color: #ffffff; cursor: move; width: 700px; font-weight: bold; padding: 0px;' onmousedown=\"dragStart(event, 'div_bull_simp')\">\n";
			echo "<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>\n";
			echo "<a href='#' onClick=\"cacher_div('div_bull_simp');return false;\">\n";
			echo "<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />\n";
			echo "</a>\n";
			echo "</div>\n";
	
			echo "<div id='titre_entete_bull_simp'></div>\n";
		echo "</div>\n";
		
		echo "<div id='corps_bull_simp' class='infobulle_corps' style='color: #ffffff; cursor: move; font-weight: bold; padding: 0px; height: 15em; width: 700px; overflow: auto;'>";
		echo "</div>\n";
	
	echo "</div>\n";

	echo "<script type='text/javascript'>
	// <![CDATA[
	function affiche_bull_simp(login_eleve,id_classe,num_per1,num_per2) {
		document.getElementById('titre_entete_bull_simp').innerHTML='Bulletin simplifié de '+login_eleve+' période '+num_per1+' à '+num_per2;
		new Ajax.Updater($('corps_bull_simp'),'../saisie/ajax_edit_limite.php?choix_edit=2&login_eleve='+login_eleve+'&id_classe='+id_classe+'&periode1='+num_per1+'&periode2='+num_per2,{method: 'get'});
	}
	//]]>
</script>\n";
}

?>