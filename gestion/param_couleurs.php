<?php
/*
 * $Id$
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();};

// INSERT INTO droits VALUES ('/gestion/param_couleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des couleurs pour Gepi', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


// Liste des composantes
$comp=array('R','V','B');



function hex2nb($carac){
	switch(strtoupper($carac)) {
		case "A":
			return 10;
			break;
		case "B":
			return 11;
			break;
		case "C":
			return 12;
			break;
		case "D":
			return 13;
			break;
		case "E":
			return 14;
			break;
		case "F":
			return 15;
			break;
		default:
			return $carac;
			break;
	}
}

function tab_rvb($couleur){
	$compR=substr($couleur,0,2);
	$compV=substr($couleur,2,2);
	$compB=substr($couleur,4,2);

	//echo "\$compR=$compR<br />";
	//echo "\$compV=$compV<br />";
	//echo "\$compB=$compB<br />";

	$tabcomp=array();

	$tabcomp['R']=hex2nb(substr($compR,0,1))*16+hex2nb(substr($compR,1,1));
	$tabcomp['V']=hex2nb(substr($compV,0,1))*16+hex2nb(substr($compV,1,1));
	$tabcomp['B']=hex2nb(substr($compB,0,1))*16+hex2nb(substr($compB,1,1));

	return $tabcomp;
}



function genere_degrade($couleur_haut,$couleur_bas,$hauteur,$chemin_img){
	//$hauteur=100;

	$im=imagecreate(1,$hauteur);

	$comp=array('R','V','B');

	$tab_haut=array();
	$tab_haut=tab_rvb($couleur_haut);

	$tab_bas=array();
	$tab_bas=tab_rvb($couleur_bas);

	for($x=0;$x<$hauteur;$x++){
		$ratio=array();
		for($i=0;$i<count($comp);$i++){
			$ratio[$comp[$i]]=$tab_haut[$comp[$i]]+$x*($tab_bas[$comp[$i]]-$tab_haut[$comp[$i]])/$hauteur;
		}
		$color=imagecolorallocate($im,$ratio['R'],$ratio['V'],$ratio['B']);
		imagesetpixel($im,0,$x,$color);
	}
	imagepng($im,$chemin_img);
}



// Liste des couleurs,... paramétrables
$tab=array();
$tab[0]='style_body_backgroundcolor';
// NOTE: Pour JavaScript, on n'a pas le droit au '-' dans un nom de variable



//if(isset($_POST['ok'])){
if(isset($_POST['is_posted'])) {
	$err_no=0;
	$msg="";

	//if(isset($_POST['style_body_backgroundcolor'])) {

	$reinitialiser="n";
	if(isset($_POST['secu'])){
		if($_POST['secu']=='y'){
			$reinitialiser='y';
		}
	}

	if($reinitialiser=='y'){
		if(saveSetting('style_body_backgroundcolor','')) {
			$fich=fopen("../style_screen_ajout.css","w+");
			fwrite($fich,"/*
Ce fichier est destiné à recevoir des paramètres définis depuis la page /gestion/param_couleurs.php
Chargé juste avant la section <body> dans le /lib/header.inc,
ses propriétés écrasent les propriétés définies auparavant dans le </head>.
*/
");
			fclose($fich);
			$msg.="Réinitialisation effectuée.";
		}
		else{
			$msg.="Erreur lors de la réinitialisation.";
		}
	}
	else{
		$temoin_modif=0;
		$temoin_fichier_regenere=0;
		$nb_err=0;

		if(isset($_POST['utiliser_couleurs_perso'])) {
			if(!saveSetting('utiliser_couleurs_perso','y')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_couleurs_perso'. ";
				$nb_err++;
			}

			if(isset($_POST['style_body_backgroundcolor'])){
				if(saveSetting('style_body_backgroundcolor',$_POST['style_body_backgroundcolor'])) {
					$fich=fopen("../style_screen_ajout.css","w+");
					fwrite($fich,"/*
Ce fichier est destiné à recevoir des paramètres définis depuis la page /gestion/param_couleurs.php
Chargé juste avant la section <body> dans le /lib/header.inc,
ses propriétés écrasent les propriétés définies auparavant dans le </head>.
*/

@media screen  {
    body {
        background: #".$_POST['style_body_backgroundcolor'].";
    }
}
");
					fclose($fich);
					//$msg.="Enregistrement effectué. ";
					$temoin_modif++;
					$temoin_fichier_regenere++;
				}
				else{
					$msg.="Erreur lors de la sauvegarde de 'style_body_backgroundcolor'. ";
					$nb_err++;
				}
			}
		}
		else{
			if(!saveSetting('utiliser_couleurs_perso','n')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_couleurs_perso'. ";
				$nb_err++;
			}

			$fich=fopen("../style_screen_ajout.css","w+");
			fwrite($fich,"/*
Ce fichier est destiné à recevoir des paramètres définis depuis la page /gestion/param_couleurs.php
Chargé juste avant la section <body> dans le /lib/header.inc,
ses propriétés écrasent les propriétés définies auparavant dans le </head>.
*/
");
			fclose($fich);
			//$msg.="Enregistrement effectué. ";
			$temoin_modif++;
			$temoin_fichier_regenere++;
		}

		if(isset($_POST['utiliser_degrade'])){
			if(!saveSetting('utiliser_degrade','y')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_degrade'. ";
				$nb_err++;
			}

			if(isset($_POST['degrade_haut'])){
				if((strlen(ereg_replace("[0-9A-F]","",strtoupper($_POST['degrade_haut'])))!=0)||(strlen($_POST['degrade_haut'])!=6)) {
					$degrade_haut="020202";
				}
				else{
					$degrade_haut=$_POST['degrade_haut'];
				}

				if(saveSetting('degrade_haut',$degrade_haut)) {
					//$msg.="Enregistrement effectué. ";
					$temoin_modif++;
				}
				else{
					$msg.="Erreur lors de la sauvegarde de 'degrade_haut'. ";
					$nb_err++;
				}
			}

			if(isset($_POST['degrade_bas'])){
				if((strlen(ereg_replace("[0-9A-F]","",strtoupper($_POST['degrade_bas'])))!=0)||(strlen($_POST['degrade_bas'])!=6)) {
					$degrade_bas="4A4A59";
				}
				else{
					$degrade_bas=$_POST['degrade_bas'];
				}

				if(saveSetting('degrade_bas',$_POST['degrade_bas'])) {
					//$msg.="Enregistrement effectué. ";
					$temoin_modif++;
				}
				else{
					$msg.="Erreur lors de la sauvegarde de 'degrade_bas'. ";
					$nb_err++;
				}
			}

			if($nb_err==0){
				/*
				if($temoin_fichier_regenere==0){
				}
				else{
				}
				*/


				// Générer l'image...

				genere_degrade($degrade_haut,$degrade_bas,100,"../images/background/degrade1.png");
				genere_degrade($degrade_haut,$degrade_bas,40,"../images/background/degrade1_small.png");


				$fich=fopen("../style_screen_ajout.css","a+");
				fwrite($fich,"

div#header {
        background-color: #$degrade_bas;
}

fieldset#login_box div#header {
    background: white;
    background-image: url(\"./images/background/degrade1_small.png\");
    background-repeat: repeat-x;
    color: white;
    text-align: center;
    height: 40px;
}

#table_header {
	color: white;
	/*
	Ca ne fonctionne pas! Pas pris en compte???
	J'ai remis cette partie directement dans la page si on est en gepi_style=style.
	*/
	background-image: url(\"./images/background/degrade1.png\");
    background-repeat: repeat-x;
	/*background-color: red;*/
	margin: 0;
	padding: 0;
}
");
				fclose($fich);
			}
		}
		else{
			if(file_exists("../images/background/degrade1.png")){unlink("../images/background/degrade1.png");}
			if(file_exists("../images/background/degrade1_small.png")){unlink("../images/background/degrade1_small.png");}
			if(!saveSetting('utiliser_degrade','n')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_degrade'. ";
				$nb_err++;
			}
		}


/*
//$temoin_fichier_regenere
				if($temoin_modif==0){
				}
				else{
				}
				fclose($fich);
*/
	}
}


//**************** EN-TETE *****************
$titre_page = "Choix des couleurs GEPI";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//echo "<div class='norme'><p class='bold'><a href='param_gen.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "<div class='norme'><p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "</p>\n";
echo "</div>\n";

/*
foreach($_POST as $post => $val){
	echo $post.' : '.$val."<br />\n";
}
*/

?>


<script type='text/javascript'>
	var hexa=new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");

	/*
	function affichecouleur(motif){
		compR=eval("document.forms['tab']."+motif+"_R.value");
		compV=eval("document.forms['tab']."+motif+"_V.value");
		compB=eval("document.forms['tab']."+motif+"_B.value");

		hex1=Math.floor(compR/16);
		hex2=compR-hex1*16;
		couleur=hexa[hex1]+""+hexa[hex2];

		hex1=Math.floor(compV/16);
		hex2=compV-hex1*16;
		couleur=couleur+""+hexa[hex1]+""+hexa[hex2];

		hex1=Math.floor(compB/16);
		hex2=compB-hex1*16;
		couleur=couleur+""+hexa[hex1]+""+hexa[hex2];

		//alert(couleur);

		document.getElementById(motif).style.backgroundColor="#"+couleur;
	}
	*/


	function calculecouleur(motif){
		compR=eval("document.forms['tab']."+motif+"_R.value");
		compV=eval("document.forms['tab']."+motif+"_V.value");
		compB=eval("document.forms['tab']."+motif+"_B.value");

		hex1=Math.floor(compR/16);
		hex2=compR-hex1*16;
		couleur=hexa[hex1]+""+hexa[hex2];

		hex1=Math.floor(compV/16);
		hex2=compV-hex1*16;
		couleur=couleur+""+hexa[hex1]+""+hexa[hex2];

		hex1=Math.floor(compB/16);
		hex2=compB-hex1*16;
		couleur=couleur+""+hexa[hex1]+""+hexa[hex2];

		return couleur;
	}

	function affichecouleur(motif){
		document.getElementById(motif).style.backgroundColor="#"+calculecouleur(motif);
	}

	function delai_affichecouleur(motif){
		//alert('motif='+motif);
		setTimeout("affichecouleur("+motif+")",1000);
	}


	//var liste=new Array('style_body_backgroundcolor');
	var liste=new Array('style_body_backgroundcolor','degrade_haut','degrade_bas');

	function init(){
		for(i=0;i<liste.length;i++){
			eval("affichecouleur('"+liste[i]+"')");
		}
	}

	function calcule_et_valide(){
		for(i=0;i<liste.length;i++){
			champ=eval("document.forms['tab']."+liste[i])
			champ.value=calculecouleur(liste[i]);
		}
		document.forms['tab'].submit();
	}

	//function reinitialiser(){
	function reinit(){
		document.forms['tab'].secu.value='y';
		document.forms['tab'].submit();
	}



	var tabmotif=new Array();
	// #EAEAEA
	// 14*16+10
	tabmotif['style_body_backgroundcolor_R']="234";
	tabmotif['style_body_backgroundcolor_V']="234";
	tabmotif['style_body_backgroundcolor_B']="234";
	//020202
	tabmotif['degrade_haut_R']="2";
	tabmotif['degrade_haut_V']="2";
	tabmotif['degrade_haut_B']="2";
	//4A4A59
	// 4*16+10 et 5*16+9
	tabmotif['degrade_bas_R']="74";
	tabmotif['degrade_bas_V']="74";
	tabmotif['degrade_bas_B']="89";

	function reinit_couleurs(motif){
		comp_motif=motif+"_R";
		champ_R=eval("document.forms['tab']."+comp_motif);
		champ_R.value=tabmotif[comp_motif];

		comp_motif=motif+"_V";
		champ_V=eval("document.forms['tab']."+comp_motif);
		champ_V.value=tabmotif[comp_motif];

		comp_motif=motif+"_B";
		champ_B=eval("document.forms['tab']."+comp_motif);
		champ_B.value=tabmotif[comp_motif];

		//calcule_et_valide();
		affichecouleur(motif);

		//return false;
	}

</script>

<p>Cette page est destinée à choisir les couleurs pour l'interface GEPI.<br />
Dans sa version actuelle, seule la couleur de fond de la page peut être paramétrée depuis cette page.</p>

<?php

/*
// Tableau des couleurs HTML:
$tab_html_couleurs=Array("aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen");
*/

	// Initialisation
	$tabcouleurs=array();
	$tabcouleurs['style_body_backgroundcolor']=array();
	$style_body_backgroundcolor=getSettingValue('style_body_backgroundcolor');
	//echo "\$style_body_backgroundcolor=$style_body_backgroundcolor<br />";
	if($style_body_backgroundcolor!=""){
/*
		$compR=substr($style_body_backgroundcolor,0,2);
		$compV=substr($style_body_backgroundcolor,2,2);
		$compB=substr($style_body_backgroundcolor,4,2);

		//echo "\$compR=$compR<br />";
		//echo "\$compV=$compV<br />";
		//echo "\$compB=$compB<br />";

		$nb_compR=hex2nb(substr($compR,0,1))*16+hex2nb(substr($compR,1,1));
		$nb_compV=hex2nb(substr($compV,0,1))*16+hex2nb(substr($compV,1,1));
		$nb_compB=hex2nb(substr($compB,0,1))*16+hex2nb(substr($compB,1,1));

		//echo "\$nb_compR=$nb_compR<br />";
		//echo "\$nb_compV=$nb_compV<br />";
		//echo "\$nb_compB=$nb_compB<br />";

		$tabcouleurs['style_body_backgroundcolor']['R']=$nb_compR;
		$tabcouleurs['style_body_backgroundcolor']['V']=$nb_compV;
		$tabcouleurs['style_body_backgroundcolor']['B']=$nb_compB;
*/

		$tabcouleurs['style_body_backgroundcolor']=tab_rvb($style_body_backgroundcolor);
	}
	else{
		// #EAEAEA
		// 14*16+10
		$tabcouleurs['style_body_backgroundcolor']['R']=234;
		$tabcouleurs['style_body_backgroundcolor']['V']=234;
		$tabcouleurs['style_body_backgroundcolor']['B']=234;
	}



	echo "<form name='tab' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<p><b>Couleurs:</b></p>\n";
	echo "<blockquote>\n";
	echo "<table border='0'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<input type='checkbox' name='utiliser_couleurs_perso' value='y' ";
	if(getSettingValue('utiliser_couleurs_perso')=='y'){
		echo "checked ";
	}
	echo "/> ";
	echo "</td>\n";
	echo "<td>\n";
	echo "Utiliser des couleurs personnalisées.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td>\n";
	echo "&nbsp;";
	echo "</td>\n";
	echo "<td>\n";
		echo "<table style='border: 1px solid black; border-collapse:collapse;'>\n";

		echo "<tr style='background-color:white;'>\n";
		echo "<td style='font-weight:bold; text-align:center; border: 1px solid black;'>Motif</td>\n";
		echo "<td style='font-weight:bold; text-align:center; border: 1px solid black;'>Propriété</td>\n";
		for($j=0;$j<count($comp);$j++){
			echo "<td style='font-weight:bold; text-align:center; border: 1px solid black;'>$comp[$j]</td>\n";
		}
		echo "<td style='font-weight:bold; text-align:center; border: 1px solid black;'>Aperçu</td>\n";
		echo "<td style='font-weight:bold; text-align:center; border: 1px solid black;'>Réinitialisation</td>\n";

		echo "</tr>\n";

		for($i=0;$i<count($tab);$i++){
			echo "<tr>\n";
			//echo "<td>$tab[$i]</td>\n";
			//echo "<td>Couleur de fond de page: <a href='couleur.php?objet=Fond'></a></td>\n";
			echo "<td style='text-align:center; border: 1px solid black;'>Couleur de fond de page";
			//echo "<a href='couleur.php?objet=".$tab[$i]."'></a>
			echo "</td>\n";
			echo "<td style='text-align:center; border: 1px solid black;'>body{background-color: #XXXXXX;}</td>\n";
			for($j=0;$j<count($comp);$j++){
				$sql="SELECT value FROM setting WHERE name='".$tab[$i]."_".$comp[$j]."'";
				$res_couleur=mysql_query($sql);
				if(mysql_num_rows($res_couleur)>0){
					$tmp=mysql_fetch_object($res_couleur);
					$tabcouleurs[$tab[$i]][$comp[$j]]=$tmp->value;
				}
				echo "<td style='text-align:center; border: 1px solid black;'>\n";
				//echo "$sql<br />";
				//echo "<input type='text' name='".$tab[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab[$i]][$comp[$j]]."' size='3' onBlur='affichecouleur(\"".$tab[$i]."\")' />\n";

				//echo "<input type='text' name='".$tab[$i]."_".$comp[$j]."' id='id_".$tab[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab[$i]][$comp[$j]]."' size='3' onChange='delai_affichecouleur(\"".$tab[$i]."\")' onKeyDown=\"clavier_2(this.id,event);\" />\n";

				echo "<input type='text' name='".$tab[$i]."_".$comp[$j]."' id='id_".$tab[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab[$i]][$comp[$j]]."' size='3' onBlur='affichecouleur(\"".$tab[$i]."\")' onKeyDown=\"clavier_2(this.id,event);\" />\n";
				echo "</td>\n";
			}
			echo "<td id='".$tab[$i]."' style='text-align:center; border: 1px solid black;'>\n";
			echo "<input type='hidden' name='$tab[$i]' value='$tab[$i]' />\n";
			echo "&nbsp;&nbsp;&nbsp;</td>\n";
			echo "<td style='text-align:center; border: 1px solid black;'>\n";
			//echo "<a href='#' onClick='reinit_couleurs(\"$tab[$i]\");return false;'>Réinitialiser</a>\n";
			echo "<a href='#' onClick='reinit_couleurs(\"$tab[$i]\");return false;'>Réinitialiser</a>\n";
			//echo "<a href='javascript:reinit_couleurs(\"$tab[$i]\");'>Réinitialiser</a>\n";
			//echo "<input type='button' name='reinit$i' value='Réinitialiser' onClick='javascript:reinit_couleurs(\"$tab[$i]\");' />\n";
			echo "</td>\n";


			echo "</tr>\n";
		}
		echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</blockquote>\n";





	echo "<p><b>Dégradé:</b></p>\n";
	echo "<blockquote>\n";
	echo "<table border='0'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<input type='checkbox' name='utiliser_degrade' value='y' ";
	if(getSettingValue('utiliser_degrade')=='y'){
		echo "checked ";
	}
	echo "/> ";
	echo "</td>\n";
	echo "<td>\n";
	echo "Générer/utiliser un dégradé personnalisé pour l'entête de page.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td>\n";
	echo "&nbsp;";
	echo "</td>\n";
	echo "<td>\n";
		echo "<table style='border: 1px solid black; border-collapse:collapse;'>\n";
		echo "<tr style='background-color:white;'>\n";
		echo "<td style='font-weight:bold; text-align:center; border: 1px solid black;'>Couleur</td>\n";
		for($j=0;$j<count($comp);$j++){
			echo "<td style='font-weight:bold; text-align:center; border: 1px solid black;'>$comp[$j]</td>\n";
		}
		echo "<td style='font-weight:bold; text-align:center; border: 1px solid black;'>Aperçu</td>\n";
		echo "<td style='font-weight:bold; text-align:center; border: 1px solid black;'>Réinitialisation</td>\n";
		echo "</tr>\n";

		$tab_degrade=array("degrade_haut","degrade_bas");

		$degrade_haut=getSettingValue('degrade_haut');
		if($degrade_haut!=""){
			$tabcouleurs['degrade_haut']=tab_rvb($degrade_haut);
		}
		else{
			$tabcouleurs['degrade_haut']['R']=2;
			$tabcouleurs['degrade_haut']['V']=2;
			$tabcouleurs['degrade_haut']['B']=2;
		}

		$degrade_bas=getSettingValue('degrade_bas');
		if($degrade_bas!=""){
			$tabcouleurs['degrade_bas']=tab_rvb($degrade_bas);
		}
		else{
			$tabcouleurs['degrade_bas']['R']=74;
			$tabcouleurs['degrade_bas']['V']=74;
			$tabcouleurs['degrade_bas']['B']=89;
		}

		for($i=0;$i<count($tab_degrade);$i++){
			echo "<tr>\n";

			echo "<td style='text-align:center; border: 1px solid black;'>$tab_degrade[$i]";
			echo "</td>\n";

			for($j=0;$j<count($comp);$j++){
				$sql="SELECT value FROM setting WHERE name='".$tab_degrade[$i]."_".$comp[$j]."'";
				$res_couleur=mysql_query($sql);
				if(mysql_num_rows($res_couleur)>0){
					$tmp=mysql_fetch_object($res_couleur);
					$tabcouleurs[$tab_degrade[$i]][$comp[$j]]=$tmp->value;
				}
				echo "<td style='text-align:center; border: 1px solid black;'>\n";
				echo "<input type='text' name='".$tab_degrade[$i]."_".$comp[$j]."' id='id_".$tab_degrade[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab_degrade[$i]][$comp[$j]]."' size='3' onBlur='affichecouleur(\"".$tab_degrade[$i]."\")' onKeyDown=\"clavier_2(this.id,event);\" />\n";

				echo "</td>\n";
			}
			echo "<td id='".$tab_degrade[$i]."' style='text-align:center; border: 1px solid black;'>\n";
			echo "<input type='hidden' name='$tab_degrade[$i]' value='$tab_degrade[$i]' />\n";
			echo "&nbsp;&nbsp;&nbsp;</td>\n";

			echo "<td style='text-align:center; border: 1px solid black;'>\n";
			echo "<a href='#' onClick='reinit_couleurs(\"$tab_degrade[$i]\");return false;'>Réinitialiser</a>\n";
			echo "</td>\n";


			echo "</tr>\n";
		}
		echo "</table>\n";
	echo "</table>\n";
	echo "</blockquote>\n";






	echo "<input type='hidden' name='is_posted' value='1' />\n";
	//echo "<p style='text-align:center;'><input type='submit' name='ok' value='Valider' /></p>\n";
	echo "<p style='text-align:center;'><input type='button' name='ok' value='Valider' onClick='calcule_et_valide()' /></p>\n";


	echo "<p><b>Remarque:</b></p>";
	echo "<blockquote>\n";
	echo "<p>Il peut arriver qu'il faille insister après validation pour que le navigateur recharge bien la page (<i>problème de cache du navigateur</i>).<br />Vous pouvez forcer le rechargement avec CTRL+MAJ+R.</p>\n";
	echo "</blockquote>\n";

/*
	//echo "<input type='text' name='truc' value='100' size='3' onkeypress='test_clavier(\"truc\")' />\n";
	//echo "<input type='text' name='truc' value='100' size='3' onkeypress='xKey(\"Event\")' />\n";
	//echo "<input type='text' name='truc' value='100' size='3' onkeypress='xKey(Event)' />\n";
	echo "<input type='text' name='truc' id='id_truc' value='100' size='3' onKeyDown=\"clavier_2(this.id,event);\" />\n";
	//echo "<input type='text' name='truc' id='id_truc' value='100' size='3' onKeyPress=\"clavier_2(this.id,event);\" />\n";
*/
	echo "<p><br /></p>\n";

	echo "<div align='center'>\n";
	echo "<div style='padding:1em; text-align:center; background-color:white; color:red; border: 1px solid black; width: 250px;'>Le bouton ci-dessous est une 'sécurité'<br />pour réinitialiser les couleurs<br />si jamais vous en arriviez à obtenir quelque chose<br />comme du texte noir sur un fond noir.<br />";
	echo "<input type='hidden' name='secu' value='n' />\n";
	//echo "<input type='button' name='reinitialiser' value='Réinitialiser' onClick='reinitialiser()' /></div>\n";
	echo "<input type='button' name='reinitialiser' value='Réinitialiser' onClick='reinit()' /></div>\n";
	echo "</div>\n";

	echo "<script type='text/javascript'>
		setTimeout('init()',500);
	</script>\n";



echo "</form>\n";

require("../lib/footer.inc.php");
?>