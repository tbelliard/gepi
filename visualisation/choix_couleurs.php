<?php
/*
 * $Id$
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// INSERT INTO droits VALUES ('/visualisation/choix_couleurs.php', 'V', 'F', 'F', 'V', 'F', 'F', 'Choix des couleurs des graphiques des résultats scolaires', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


$tab=array('Fond','Bande_1','Bande_2','Axes','Eleve_1','Eleve_2','Moyenne_classe','Periode_1','Periode_2','Periode_3');
//$tabdef=array('white','moccasin','white','black','royalblue','green','dimgray');
$comp=array('R','V','B');


if(isset($ok)) {
	check_token();

	$msg="";

	$err_no=0;
	for($i=0;$i<count($tab);$i++){
		for($j=0;$j<count($comp);$j++){
			$chaine=$tab[$i]."_".$comp[$j];
			if(isset($$chaine)){
				//echo "$chaine<br />";
				if((ctype_digit($$chaine))&&($$chaine>=0)&&($$chaine<=255)){
					//echo "$chaine: ".$$chaine." OK<br />";
					if(!saveSetting("couleur_".$chaine,$$chaine)){
						$msg.="Erreur lors de l'enregistrement de ".$chaine." dans la table setting !";
					}
				}
				else{
					//echo "$chaine: ".$$chaine." PROBLEME<br />";
					$msg.="Valeur erronée pour $chaine<br />\n";
					$err_no++;
				}
			}
		}
	}

	for($i=4;$i<$max_per;$i++){
		for($j=0;$j<count($comp);$j++){
			$chaine="Periode_".$i."_".$comp[$j];
			if(isset($$chaine)){
				//echo "$chaine<br />";
				if((ctype_digit($$chaine))&&($$chaine>=0)&&($$chaine<=255)){
					//echo "$chaine: ".$$chaine." OK<br />";
					if(!saveSetting("couleur_".$chaine,$$chaine)){
						$msg.="Erreur lors de l'enregistrement de ".$chaine." dans la table setting !";
					}
				}
				else{
					//echo "$chaine: ".$$chaine." PROBLEME<br />";
					$msg.="Valeur erronée pour $chaine<br />\n";
					$err_no++;
				}
			}
		}
	}

	if($err_no==0){
		$msg.="Enregistrement effectué.";
	}
}

//**************** EN-TETE *********************
$titre_page = "Choix des couleurs de graphes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

/*
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiProfImprBul")!='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}
*/

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

?>

<script type='text/javascript'>

var hexa=new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");

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


var liste=new Array('Fond','Bande_1','Bande_2','Axes','Eleve_1','Eleve_2','Moyenne_classe','Periode_1','Periode_2','Periode_3');

function init(){
	for(i=0;i<liste.length;i++){
		eval("affichecouleur('"+liste[i]+"')");
	}
}


</script>

<?php

/*
// Tableau des couleurs HTML:
$tabcouleur=Array("aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen");
*/


//if(!isset($ok)){

/*
	$tab=array('Fond','Bande_1','Bande_2','Axes','Eleve_1','Eleve_2','Moyenne_classe','Periode_1','Periode_2','Periode_3');
	//$tabdef=array('white','moccasin','white','black','royalblue','green','dimgray');
	$comp=array('R','V','B');
*/

	$tabcouleurs=array();
	$tabcouleurs['Fond']=array();
	$tabcouleurs['Fond']['R']=255;
	$tabcouleurs['Fond']['V']=255;
	$tabcouleurs['Fond']['B']=255;

	$tabcouleurs['Bande_1']=array();
	$tabcouleurs['Bande_1']['R']=255;
	$tabcouleurs['Bande_1']['V']=255;
	$tabcouleurs['Bande_1']['B']=255;

	$tabcouleurs['Bande_2']=array();
	$tabcouleurs['Bande_2']['R']=255;
	$tabcouleurs['Bande_2']['V']=255;
	$tabcouleurs['Bande_2']['B']=133;

	$tabcouleurs['Axes']=array();
	$tabcouleurs['Axes']['R']=0;
	$tabcouleurs['Axes']['V']=0;
	$tabcouleurs['Axes']['B']=0;

	$tabcouleurs['Eleve_1']=array();
	$tabcouleurs['Eleve_1']['R']=0;
	$tabcouleurs['Eleve_1']['V']=100;
	$tabcouleurs['Eleve_1']['B']=255;

	$tabcouleurs['Eleve_2']=array();
	$tabcouleurs['Eleve_2']['R']=0;
	$tabcouleurs['Eleve_2']['V']=255;
	$tabcouleurs['Eleve_2']['B']=0;

	$tabcouleurs['Moyenne_classe']=array();
	$tabcouleurs['Moyenne_classe']['R']=100;
	$tabcouleurs['Moyenne_classe']['V']=100;
	$tabcouleurs['Moyenne_classe']['B']=100;

	$tabcouleurs['Periode_1']=array();
	$tabcouleurs['Periode_1']['R']=0;
	$tabcouleurs['Periode_1']['V']=100;
	$tabcouleurs['Periode_1']['B']=255;

	$tabcouleurs['Periode_2']=array();
	$tabcouleurs['Periode_2']['R']=255;
	$tabcouleurs['Periode_2']['V']=0;
	$tabcouleurs['Periode_2']['B']=0;

	$tabcouleurs['Periode_3']=array();
	$tabcouleurs['Periode_3']['R']=255;
	$tabcouleurs['Periode_3']['V']=0;
	$tabcouleurs['Periode_3']['B']=0;


	echo "<form name='tab' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	echo "<table border='0'>\n";

	echo "<tr>\n";
	echo "<td style='font-weight:bold;'>Motif</td>\n";
	for($j=0;$j<count($comp);$j++){
		echo "<td style='font-weight:bold; text-align:center;'>$comp[$j]</td>\n";
	}
	echo "<td>&nbsp;&nbsp;&nbsp;</td>\n";
	echo "</tr>\n";

	for($i=0;$i<count($tab);$i++){
		echo "<tr>\n";
		//echo "<td>$tab[$i]</td>\n";
		echo "<td><a href='couleur.php?objet=".$tab[$i]."'>$tab[$i]</a></td>\n";

		for($j=0;$j<count($comp);$j++){
			$sql="SELECT value FROM setting WHERE name='couleur_".$tab[$i]."_".$comp[$j]."'";
			$res_couleur=mysql_query($sql);
			if(mysql_num_rows($res_couleur)>0){
				$tmp=mysql_fetch_object($res_couleur);
				$tabcouleurs[$tab[$i]][$comp[$j]]=$tmp->value;
			}
			echo "<td style='text-align:center;'>\n";
			//echo "$sql<br />";
			echo "<input type='text' name='".$tab[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab[$i]][$comp[$j]]."' size='3' onBlur='affichecouleur(\"".$tab[$i]."\")' />\n";
			echo "</td>\n";
		}
		echo "<td id='".$tab[$i]."'>&nbsp;&nbsp;&nbsp;</td>\n";
		echo "</tr>\n";
	}


/*
	for($i=0;$i<count($tab);$i++){
		echo "<tr>\n";
		echo "<td>$tab[$i]</td>\n";
		echo "<td>";
		$sql="SELECT value FROM setting WHERE name='couleur_".$tab[$i]."'";
		$res_couleur=mysql_query($sql);
		if(mysql_num_rows($res_couleur)>0){
			$tmp=mysql_fetch_object($res_couleur);
			$defaut=$tmp->value;
		}
		else{
			$defaut=$tabdef[$i];
		}

		echo "<select name=''";
		for($j=0;$j<count($tabcouleur);$j++){

		}
		echo "</td>\n";
		echo "</tr>\n";
	}
*/

	$sql="SELECT max(num_periode) maxper FROM periodes";
	$res_max_per=mysql_query($sql);
	$max_per=mysql_result($res_max_per, 0, "maxper");

	for($i=4;$i<$max_per+1;$i++){
		echo "<tr>\n";
		echo "<td>Periode_$i</td>\n";
		for($j=0;$j<count($comp);$j++){
			$sql="SELECT value FROM setting WHERE name='couleur_Periode_".$i."_".$comp[$j]."'";
			$res_couleur=mysql_query($sql);
			if(mysql_num_rows($res_couleur)>0){
				$tmp=mysql_fetch_object($res_couleur);
				$composante=$tmp->value;
			}
			else{
				$composante=0;
			}
			echo "<td style='text-align:center;'><input type='text' name='Periode_".$i."_".$comp[$j]."' value='$composante' size='3' /></td>\n";
		}
		echo "</tr>\n";
	}

	echo "</table>\n";
	echo "<input type='hidden' name='max_per' value='$max_per' />\n";
	echo "<p style='text-align:center;'><input type='submit' name='ok' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
		setTimeout('init()',500);
	</script>\n";
/*
}
else{
	// On valide les saisies...
	// ... le faire au-dessus serait mieux.
	echo "VALIDé";
}
*/
require("../lib/footer.inc.php");
?>