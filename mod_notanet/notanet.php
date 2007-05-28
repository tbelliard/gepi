<?php
/* $Id$ */
/*
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
	die();
};





//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
// Pour GEPI 1.4.3 à 1.4.4
// INSERT INTO droits VALUES('/mod_notanet/notanet.php','V','F','F','F','F','F','Accès à l export NOTANET','');
// Pour GEPI 1.5.x
// INSERT INTO droits VALUES('/mod_notanet/notanet.php','V','F','F','F','F','F','F','Accès à l export NOTANET','');
// Pour décommenter le passage, il suffit de supprimer le 'slash-etoile' ci-dessus et l'étoile-slash' ci-dessous.
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================





//echo '<link rel="stylesheet" type="text/css" media="print" href="impression.css">';

//**************** EN-TETE *****************
$titre_page = "Export NOTANET";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************



// Récupération des variables:
// Tableau des classes:
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
// Vérifier s'il peut y avoir des accents dans un id_classe.
//if((strlen(ereg_replace("[0-9a-zA-Z_ ]","",$id_classe))!=0)||($id_classe=="")){$id_classe=NULL;}
// Type de brevet:
$type_brevet = isset($_POST['type_brevet']) ? $_POST['type_brevet'] : NULL;




//include "../lib/periodes.inc.php";
// Cette bibliothèque permet de récupérer des tableaux de $nom_periode et $ver_periode (et $nb_periode)
// pour la classe considérée (valeur courante de $id_classe).

//echo "<p>$id_classe</p>\n";











//$tabmatieres=array('FRANCAIS','MATHEMATIQUES','PREMIERE LANGUE VIVANTE','SCIENCES DE LA VIE ET DE LA TERRE');

$tabmatieres=array();
for($j=101;$j<=122;$j++){
	$tabmatieres[$j]=array();
}
/*
$tabmatieres[101][0]='FRANCAIS';
$tabmatieres[102][0]='MATHEMATIQUES';
$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
$tabmatieres[104][0]='SCIENCES DE LA VIE ET DE LA TERRE';
$tabmatieres[105][0]='PHYSIQUE-CHIMIE';
$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
$tabmatieres[107][0]='ARTS PLASTIQUES';
$tabmatieres[108][0]='EDUCATION MUSICALE';
$tabmatieres[109][0]='TECHNOLOGIE';
$tabmatieres[110][0]='DEUXIEME LANGUE VIVANTE';
$tabmatieres[111][0]='';
$tabmatieres[112][0]='';
$tabmatieres[113][0]='OPTION FACULTATIVE (1)';
$tabmatieres[114][0]='';
$tabmatieres[115][0]='';
$tabmatieres[116][0]='';
$tabmatieres[117][0]='';
$tabmatieres[118][0]='';
$tabmatieres[119][0]='';
$tabmatieres[120][0]='';
$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
$tabmatieres[122][0]='EDUCATION CIVIQUE';

for($j=101;$j<=122;$j++){
	$tabmatieres[$j][-1]='POINTS';
}
$tabmatieres[113][-1]='PTSUP';
$tabmatieres[121][-1]='NOTNONCA';
$tabmatieres[122][-1]='NOTNONCA';
*/

function colore_ligne_notanet($chaine){
	$tabchaine=explode("|",$chaine);
	/*
	echo "<!--\n$chaine\n";
	for($loop=0;$loop<count($tabchaine);$loop++){
		echo "\$tabchaine[$loop]=$tabchaine[$loop]\n";
	}
	echo "-->\n";
	*/
	//$color1="red";
	$color1="blue";
	$color2="green";
	$color3="blue";
	return "<span style='color: $color1;'>$tabchaine[0]</span>|<span style='color: $color2;'>$tabchaine[1]</span>|<span style='color: $color3;'>$tabchaine[2]</span>|";
}

function formate_note_notanet($chaine){
	// Arrondir au demi-point:
	$chaine_tmp=round($chaine*2)/2;
	// Formater en AA.BB:
	//return str_pad(sprintf("%02.2f",$chaine_tmp),5,"0",STR_PAD_LEFT);
	return sprintf("%05.2f",$chaine_tmp);
}

function get_classe_from_id($id){
	//$sql="SELECT * FROM classes WHERE id='$id_classe[0]'";
	$sql="SELECT * FROM classes WHERE id='$id'";
	$resultat_classe=mysql_query($sql);
	if(mysql_num_rows($resultat_classe)!=1){
		//echo "<p>ERREUR! La classe d'identifiant '$id_classe[0]' n'a pas pu être identifiée.</p>";
		echo "<p>ERREUR! La classe d'identifiant '$id' n'a pas pu être identifiée.</p>";
	}
	else{
		$ligne_classe=mysql_fetch_object($resultat_classe);
		$classe=$ligne_classe->classe;
		return $classe;
	}
}

// Bibliothèque pour Notanet et Fiches brevet
include("lib_brevets.php");
/*
$tab_type_brevet=array();
$tab_type_brevet[0]="COLLEGE, option de série LV2";
$tab_type_brevet[1]="COLLEGE, option de série TECHNOLOGIE";
$tab_type_brevet[2]="PROFESSIONNELLE, sans option de série";
$tab_type_brevet[3]="PROFESSIONNELLE, option de série AGRICOLE";
$tab_type_brevet[4]="TECHNOLOGIQUE, sans option de série";
$tab_type_brevet[5]="TECHNOLOGIQUE, option de série AGRICOLE";
*/


echo "<div class='noprint'>\n";
echo "<p class='bold'>| <a href='../accueil.php'>Accueil</a> | ";
//echo "<a href='".$_SERVER['PHP_SELF']."'>Retour à Notanet</a>|";
//echo "</div>\n";

$sql="SELECT value FROM setting WHERE name='backup_directory'";
$resultat_bd=mysql_query($sql);
if(mysql_num_rows($resultat_bd)>0){
	$ligne_bd=mysql_fetch_object($resultat_bd);
	//$dossier_notanet='../backup/'.$ligne_bd->value.'/notanet';
	$dossier_notanet='../backup/'.$ligne_bd->value.'/csv';

	if(!file_exists($dossier_notanet)){
		mkdir($dossier_notanet);
	}
}

if(file_exists($dossier_notanet)){

	if(!file_exists("$dossier_notanet/index.html")){
		$fich=fopen("$dossier_notanet/index.html","w+");
		fwrite($fich,'<script type="text/javascript" language="JavaScript">
    document.location.replace("../../../login.php")
</script>');
		fclose($fich);
	}


	$handle=opendir($dossier_notanet);
	$tab_file = array();
	$n=0;
	while ($file = readdir($handle)) {
		if (($file != '.') and ($file != '..') and ($file != '.htaccess') and ($file != '.htpasswd') and ($file != 'index.html')) {
			//$tab_file[] = $file;
			if(substr($file,0,8)=='notanet_'){
				$tab_file[] = $file;
				$n++;
			}
			//$n++;
		}
	}
	closedir($handle);
	arsort($tab_file);

	if($n>0){
		if(!isset($_POST['choix1'])){
			echo "</div>\n";
			echo "<form action='".$_SERVER['PHP_SELF']."' name='form_choix1' method='post'>\n";
			echo "<p><input type='radio' name='export_ou_nettoyage' value='export' checked='true' /> Effectuer un export,<br />\n";
			echo "ou<br />\n";
			echo "<input type='radio' name='export_ou_nettoyage' value='nettoyage' /> supprimer les fichiers existants.<br />\n";
			echo "<input type='submit' name='choix1' value='Envoyer' /></p>\n";
			echo "</form>\n";

			require("../lib/footer.inc.php");
			die();
		}
		else{
			//if($_POST['export_ou_nettoyage']=="nettoyage"){
			$export_ou_nettoyage=isset($_POST['export_ou_nettoyage']) ? $_POST['export_ou_nettoyage'] : "";
			if($export_ou_nettoyage=="nettoyage"){
				echo "<a href='".$_SERVER['PHP_SELF']."'>Retour à l'accueil Notanet</a> | ";
				echo "</div>\n";

				echo "<h3>Suppression des fichiers d'export NOTANET</h3>\n";

				if(!isset($_POST['supprimer_fichnotanet'])){

					echo "<p>Voici la liste des fichiers:</p>\n";
					echo "<ul>\n";
					for($i=0;$i<count($tab_file);$i++){
						echo "<li><a href='$dossier_notanet/$tab_file[$i]'>$tab_file[$i]</a></li>\n";
					}
					echo "</ul>\n";

					echo "<form action='".$_SERVER['PHP_SELF']."' name='form_nettoyage' method='post'>\n";
					echo "<input type='hidden' name='choix1' value='choix_effectue' />\n";
					echo "<input type='hidden' name='export_ou_nettoyage' value='nettoyage' />\n";
					echo "<input type='submit' name='supprimer_fichnotanet' value='Supprimer tous ces fichiers' />\n";
					echo "</form>\n";
				}
				else{
					// Nettoyage des fichiers...
					echo "<p>";
					for($i=0;$i<count($tab_file);$i++){
						echo "Suppression de $tab_file[$i]<br />\n";
						unlink($dossier_notanet."/".$tab_file[$i]);
					}
					echo "</p>\n";

					//echo "<p><a href='./notanet.php'>Effectuer un export NOTANET</a></p>\n";
				}

				require("../lib/footer.inc.php");
				die();
			}
		}
	}
}

// Choix du type de Brevet:
if (!isset($type_brevet)) {
	echo "</div>\n";
	echo "<h3>Choix du type de brevet</h3>\n";
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form_choix_type_brevet' method='post'>\n";
	echo "<input type='hidden' name='choix1' value='export' />\n";
	echo "<table border='0'>\n";
	echo "<tr valign='top'>\n";
	echo "<td>Série</td><td><select name='type_brevet' size='6'>\n";
	for($i=0;$i<count($tab_type_brevet);$i++){
		echo "<option value='$i'";
		//if(getSettingValue("type_brevet")==$tab_type_brevet[$i]){ echo " selected='true'";}
		if(getSettingValue("type_brevet")==$i){ echo " selected='true'";}
		echo ">$tab_type_brevet[$i]</option>\n";
	}
	echo "</select></td>\n";
	echo "<td><input type='submit' name='choix_type_brevet' value='Envoyer' /></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>\n";
}
else{
	echo "<a href='".$_SERVER['PHP_SELF']."'>Retour à l'accueil Notanet</a> | ";

	//if (!saveSetting("type_brevet", $tab_type_brevet[$type_brevet])) {
	if (!saveSetting("type_brevet", $type_brevet)) {
		echo "<p><b style='color:red;'>ERREUR</b> lors de l'enregistrement du type de Brevet.</p>\n";
	}

	$sql="CREATE TABLE IF NOT EXISTS `notanet_corresp` (
						`id` INT NOT NULL AUTO_INCREMENT ,
						`notanet_mat` VARCHAR( 255 ) NOT NULL ,
						`matiere` VARCHAR( 50 ) NOT NULL ,
						`statut` enum('imposee','optionnelle','non dispensee dans l etablissement') NOT NULL ,
						PRIMARY KEY  (`id`)
						)";
	$res_creation_table=mysql_query($sql);
	if(!$res_creation_table){
		echo "<p><b style='color:red;'>ERREUR</b> lors de la création de la table 'notanet_corresp'.</p>\n";
	}

	// Fonction définie dans lib_brevets.php
	$tabmatieres=tabmatieres($type_brevet);
/*
	switch($type_brevet){
		case 0:
			// COLLEGE, option de série LV2
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[104][0]='SCIENCES DE LA VIE ET DE LA TERRE';
			$tabmatieres[105][0]='PHYSIQUE-CHIMIE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='ARTS PLASTIQUES';
			$tabmatieres[108][0]='EDUCATION MUSICALE';
			$tabmatieres[109][0]='TECHNOLOGIE';
			$tabmatieres[110][0]='DEUXIEME LANGUE VIVANTE';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='';
			$tabmatieres[113][0]='OPTION FACULTATIVE (1)';
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			$tabmatieres[122][0]='EDUCATION CIVIQUE';

			// Mode de calcul:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[113][-1]='PTSUP';
			$tabmatieres[121][-1]='NOTNONCA';
			$tabmatieres[122][-1]='NOTNONCA';

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			$tabmatieres[121][-2]=0;
			$tabmatieres[122][-2]=0;

			// Notes spéciales autorisées:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-3]='AB';
			}
			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI NN';
			$tabmatieres[108][-3]='AB DI NN';
			$tabmatieres[109][-3]='AB DI';
			$tabmatieres[110][-3]='AB DI';
			$tabmatieres[113][-3]='AB DI';
			break;
		case 1:
			// COLLEGE, option de série TECHNOLOGIE
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[104][0]='SCIENCES DE LA VIE ET DE LA TERRE';
			$tabmatieres[105][0]='PHYSIQUE-CHIMIE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='ARTS PLASTIQUES';
			$tabmatieres[108][0]='EDUCATION MUSICALE';
			$tabmatieres[109][0]='TECHNOLOGIE';
			$tabmatieres[110][0]='';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='';
			$tabmatieres[113][0]='OPTION FACULTATIVE (1)';
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			$tabmatieres[122][0]='EDUCATION CIVIQUE';

			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[113][-1]='PTSUP';
			$tabmatieres[121][-1]='NOTNONCA';
			$tabmatieres[122][-1]='NOTNONCA';

			// PROBLEME: TECHNOLOGIE POINTS /40
			//           GEPI ne doit donner que des notes sur 20.
			//           Il faudrait donc multiplier par deux...

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			$tabmatieres[109][-2]=2;
			$tabmatieres[121][-2]=0;
			$tabmatieres[122][-2]=0;

			// Notes spéciales autorisées:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-3]='AB';
			}
			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI NN';
			$tabmatieres[108][-3]='AB DI NN';
			$tabmatieres[109][-3]='AB DI';
			$tabmatieres[113][-3]='AB DI';

			break;
		case 2:
			// PROFESSIONNELLE, sans option de série
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			$tabmatieres[105][0]='VIE SOCIALE ET PROFESSIONNELLE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION ARTISTIQUE';
			$tabmatieres[108][0]='TECHNOLOGIE';
			$tabmatieres[109][0]='';
			$tabmatieres[110][0]='';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='';
			$tabmatieres[113][0]='';
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE EDUCATION CIVIQUE';
			$tabmatieres[122][0]='';

			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[121][-1]='NOTNONCA';

			// PROBLEME: TECHNOLOGIE POINTS /60
			//           GEPI ne doit donner que des notes sur 20.
			//           Il faudrait donc multiplier par trois...

			// Par ailleurs, les candidats sont inscrits soit en LV1 soit en Sciences-physiques
			// Il faudrait donc considérer les deux matières commme optionnelles et on a alors un problème pour relever une note manquante...

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			$tabmatieres[108][-2]=3;
			$tabmatieres[121][-2]=0;

			// Notes spéciales autorisées:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-3]='AB';
			}
			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI NN';
			$tabmatieres[108][-3]='AB DI';

			break;
		case 3:
			// PROFESSIONNELLE, option de série AGRICOLE
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[104][0]='';
			$tabmatieres[105][0]='ECONOMIE FAMILIALE ET SOCIALE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION SOCIO-CULTURELLE';
			$tabmatieres[108][0]='TECHNOLOGIE';
			$tabmatieres[109][0]='SCIENCES BIOLOGIQUES';
			$tabmatieres[110][0]='SCIENCES PHYSIQUES';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='';
			$tabmatieres[113][0]='';
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE EDUCATION CIVIQUE';
			$tabmatieres[122][0]='';

			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[121][-1]='NOTNONCA';

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			$tabmatieres[121][-2]=0;

			// Notes spéciales autorisées:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-3]='AB';
			}
			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI';
			$tabmatieres[108][-3]='AB DI';
			$tabmatieres[109][-3]='AB DI';
			$tabmatieres[110][-3]='AB DI';

			break;
		case 4:
			// TECHNOLOGIQUE, sans option de série
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			$tabmatieres[105][0]='ECONOMIE FAMILIALE ET SOCIALE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION ARTISTIQUE';
			$tabmatieres[108][0]='TECHNOLOGIE';
			$tabmatieres[109][0]='';
			$tabmatieres[110][0]='';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='';
			$tabmatieres[113][0]='';
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE EDUCATION CIVIQUE';
			$tabmatieres[122][0]='';

			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[121][-1]='NOTNONCA';

			// PROBLEME: TECHNOLOGIE POINTS /40
			//           GEPI ne doit donner que des notes sur 20.
			//           Il faudrait donc multiplier par deux...

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			$tabmatieres[108][-2]=2;
			$tabmatieres[121][-2]=0;

			// Notes spéciales autorisées:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-3]='AB';
			}
			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI NN';
			$tabmatieres[108][-3]='AB DI';

			break;
		case 5:
			// TECHNOLOGIQUE, option de série AGRICOLE
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			$tabmatieres[105][0]='ECONOMIE FAMILIALE ET SOCIALE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION SOCIOCULTURELLE';
			$tabmatieres[108][0]='SCIENCES BIOLOGIQUES';
			$tabmatieres[109][0]='TECHNO SECTEUR TECHNIQUES AGRICOLES, ACTIVITES TERTIAIRES';
			$tabmatieres[110][0]='';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='';
			$tabmatieres[113][0]='';
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE EDUCATION CIVIQUE';
			$tabmatieres[122][0]='';

			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[121][-1]='NOTNONCA';

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			$tabmatieres[121][-2]=0;

			// Notes spéciales autorisées:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-3]='AB';
			}
			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI';
			$tabmatieres[108][-3]='AB DI';
			$tabmatieres[109][-3]='AB DI';

			break;
	}
*/

	// Choix de la classe:
	if (!isset($id_classe)) {
		//echo "<div class='noprint'>\n";
		//echo "<p class='bold'>|<a href='../accueil.php'>Accueil</a>|</p>\n";
		echo "</div>\n";

		echo "<p>L'exportation pour NOTANET se déroule en plusieurs étapes:</p>\n";
		echo "<ul>\n";
		echo "<li>Choix de la série</li>\n";
		echo "<li>Choix des classes de 3ème</li>\n";
		echo "<li>Identification des matières (<i>imposées et options</i>)</li>\n";
		echo "<li>Extraction des moyennes</li>\n";
		echo "<li>Affichage</li>\n";
		echo "<li>Traitement des cas particuliers</li>\n";
		echo "<li>...</li>\n";
		echo "<li>Génération des fiches Jury: <i>A FAIRE: dès que j'aurai récupéré un modèle...</i></li>\n";
		echo "</ul>\n";

		echo "<form action='".$_SERVER['PHP_SELF']."' name='form_choix_classe' method='post'>\n";
		echo "<input type='hidden' name='choix1' value='export' />\n";
		echo "<input type='hidden' name='type_brevet' value='$type_brevet' />\n";
		echo "<p>Sélectionnez les classes : </p>\n";
		echo "<blockquote>\n";
		$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
		$nombre_lignes = mysql_num_rows($call_data);
		echo "<select name='id_classe[]' multiple='true' size='10'>\n";
		$i = 0;
		while ($i < $nombre_lignes){
			$classe = mysql_result($call_data, $i, "classe");
			$ide_classe = mysql_result($call_data, $i, "id");
			//echo "<a href='eleve_classe.php?id_classe=$ide_classe'>$classe</a><br />\n";
			echo "<option value='$ide_classe'>$classe</option>\n";
			$i++;
		}
		echo "</select><br />\n";
		echo "<input type='submit' name='choix_classe' value='Envoyer' />\n";
		echo "</blockquote>\n";
		//echo "</p>\n";
		echo "</form>\n";
	}
	else {

		// Récupération des variables:
		// Tableau des matières:
		//$id_matiere=isset($_POST['id_matiere']) ? $_POST['id_matiere'] : NULL;
	/*
		if(isset($_POST['id_matiere'])){
			for($j=101;$j<=122;$j++){
				$id_matiere[$j]=$_POST['id_matiere['.$j.']'];
			}
		}
		else{
			$id_matiere=NULL;
		}
	*/
		$id_matiere=array();
		for($j=101;$j<=122;$j++){
			if(isset($_POST['id_matiere'.$j])){
				//echo "---<br />";
				$id_matiere[$j]=$_POST['id_matiere'.$j];

			}
	/*
			else{
				$id_matiere=NULL;
			}
	*/
		}
		$statut_matiere=isset($_POST['statut_matiere']) ? $_POST['statut_matiere'] : NULL;


		//if(!isset($id_matiere)){
		if(!isset($_POST['choix_matieres'])){
			//echo "<div class='noprint'>\n";
			//echo "<p class='bold'>|<a href='../accueil.php'>Accueil</a>|";
			// Les classes sont choisies.
			// On ajoute l'accès/retour à d'autres classes:
			echo "<a href=\"".$_SERVER['PHP_SELF']."\">Choisir d'autres classes</a> | ";
			echo "</div>\n";

		/*
			//$tabmatieres=array('FRANCAIS','MATHEMATIQUES','PREMIERE LANGUE VIVANTE','SCIENCES DE LA VIE ET DE LA TERRE');

			$tabmatieres=array();
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j]=array();
			}
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[104][0]='SCIENCES DE LA VIE ET DE LA TERRE';
			$tabmatieres[105][0]='PHYSIQUE-CHIMIE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='ARTS PLASTIQUES';
			$tabmatieres[108][0]='EDUCATION MUSICALE';
			$tabmatieres[109][0]='TECHNOLOGIE';
			$tabmatieres[110][0]='DEUXIEME LANGUE VIVANTE';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='';
			$tabmatieres[113][0]='OPTION FACULTATIVE (1)';
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			$tabmatieres[122][0]='EDUCATION CIVIQUE';

			function get_classe_from_id($id){
				//$sql="SELECT * FROM classes WHERE id='$id_classe[0]'";
				$sql="SELECT * FROM classes WHERE id='$id'";
				$resultat_classe=mysql_query($sql);
				if(mysql_num_rows($resultat_classe)!=1){
					//echo "<p>ERREUR! La classe d'identifiant '$id_classe[0]' n'a pas pu être identifiée.</p>";
					echo "<p>ERREUR! La classe d'identifiant '$id' n'a pas pu être identifiée.</p>";
				}
				else{
					$ligne_classe=mysql_fetch_object($resultat_classe);
					$classe=$ligne_classe->classe;
					return $classe;
				}
			}
		*/

			echo "<form action='".$_SERVER['PHP_SELF']."' name='form_choix_matieres' method='post'>\n";
			echo "<input type='hidden' name='choix1' value='export' />\n";
			echo "<input type='hidden' name='type_brevet' value='$type_brevet' />\n";
			for($i=0;$i<count($id_classe);$i++){
				echo "<input type='hidden' name='id_classe[$i]' value='$id_classe[$i]' />\n";
			}

			//$conditions="j.id_classe='$id_classe[0]'";
			$conditions="id_classe='$id_classe[0]'";
			//echo "<p>Les classes choisies sont $id_classe[0]";
			echo "<p>Les classes choisies sont ".get_classe_from_id($id_classe[0]);
			for($i=1;$i<count($id_classe);$i++){
				//$conditions=$conditions." OR j.id_classe='$id_classe[$i]'";
				$conditions=$conditions." OR id_classe='$id_classe[$i]'";
				//echo ", $id_classe[$i]";
				echo ", ".get_classe_from_id($id_classe[$i]);
				// Récupération des noms courts/longs et priorités des matières de la classe (dans l'ordre de priorité)
				//$call_classe_infos = mysql_query("SELECT DISTINCT  m.* FROM matieres m,j_classes_matieres_professeurs j WHERE (m.matiere = j.id_matiere AND j.id_classe='$id_classe[$i]') ORDER BY j.priorite");

		/*
				$call_classe_infos = mysql_query("SELECT DISTINCT  m.* FROM matieres m,j_classes_matieres_professeurs j WHERE (m.matiere = j.id_matiere AND j.id_classe='$id_classe[$i]') ORDER BY id_matiere");
				$nombre_lignes = mysql_num_rows($call_classe_infos);
				while($ligne=mysql_fetch_object($call_classe_infos)){
					$tab
				}

				for($j=101;$j<=122;$j++){

				}
		*/
			}
			echo ".</p>\n";

			//$call_classe_infos = mysql_query("SELECT DISTINCT  m.* FROM matieres m,j_classes_matieres_professeurs j WHERE (m.matiere = j.id_matiere AND $conditions) ORDER BY matiere");

			//$sql="SELECT DISTINCT id_matiere FROM j_classes_matieres_professeurs WHERE $conditions ORDER BY id_matiere";
			// PROBLEME: Cette table n'est plus remplie avec la version 1.4.3
			// Tester avec:
			$sql="SELECT DISTINCT j_groupes_matieres.id_matiere FROM j_groupes_matieres,j_groupes_classes WHERE j_groupes_matieres.id_groupe=j_groupes_classes.id_groupe AND $conditions ORDER BY id_matiere";
			$call_classe_infos = mysql_query($sql);

			//echo "<p>SELECT DISTINCT  m.* FROM matieres m,j_classes_matieres_professeurs j WHERE (m.matiere = j.id_matiere AND $conditions) ORDER BY matiere</p>\n";
			$nombre_lignes = mysql_num_rows($call_classe_infos);
			$cpt=0;
			while($ligne=mysql_fetch_object($call_classe_infos)){
				//$tab_mat_classes[$cpt]="$ligne->matiere";
				$tab_mat_classes[$cpt]="$ligne->id_matiere";
				//echo "$tab_mat_classes[$cpt]<br />\n";
				$cpt++;
			}

			echo "<table border='1'>\n";
			echo "<tr style='font-weight:bold; text-align:center'>\n";
			echo "<td>&nbsp;</td>\n";
			echo "<td colspan='3'>Matière</td>\n";
			echo "<td>&nbsp;</td>\n";

			echo "<tr style='font-weight:bold; text-align:center'>\n";
			echo "<td>Intitulé</td>\n";
			echo "<td>Imposée</td>\n";
			echo "<td>Optionnelle</td>\n";
			echo "<td>Non dispensée dans l'établissement</td>\n";
			echo "<td>Matière GEPI</td>\n";

			echo "</tr>\n";

			for($j=101;$j<=122;$j++){
				//if($tabmatieres[$j]!=''){
				if($tabmatieres[$j][0]!=''){
					echo "<tr>\n";
					//echo "<td>$tabmatieres[$j]</td>\n";
					echo "<td>".$tabmatieres[$j][0]."</td>\n";

					$sql="SELECT * FROM notanet_corresp WHERE notanet_mat='".$tabmatieres[$j][0]."'";
					$res_notanet_corresp=mysql_query($sql);
					if(mysql_num_rows($res_notanet_corresp)>0){
						$lig_notanet_corresp=mysql_fetch_object($res_notanet_corresp);
						echo "<td style='text-align:center'><input type='radio' name='statut_matiere[$j]' value='imposee'";
						if($lig_notanet_corresp->statut=='imposee'){
							echo " checked='true'";
						}
						echo " /></td>\n";

						echo "<td style='text-align:center'><input type='radio' name='statut_matiere[$j]' value='optionnelle'";
						if($lig_notanet_corresp->statut=='optionnelle'){
							echo " checked='true'";
						}
						echo " /></td>\n";

						echo "<td style='text-align:center'><input type='radio' name='statut_matiere[$j]' value='non dispensee dans l etablissement'";
						if($lig_notanet_corresp->statut=='non dispensee dans l etablissement'){
							echo " checked='true'";
						}
						echo " /></td>\n";
					}
					else{
						echo "<td style='text-align:center'><input type='radio' name='statut_matiere[$j]' value='imposee'";
						echo " checked='true'";
						echo " /></td>\n";
						echo "<td style='text-align:center'><input type='radio' name='statut_matiere[$j]' value='optionnelle' /></td>\n";
						echo "<td style='text-align:center'><input type='radio' name='statut_matiere[$j]' value='non dispensee dans l etablissement' /></td>\n";
					}

					/*
					echo "<td>\n";
					//echo "<select name='id_matiere[$j]'>\n";
					//echo "<select multiple='true' size='4' name='id_matiere[$j]'>\n";
					//echo "<select multiple='true' size='4' name='id_matiere[$j][]'>\n";
					echo "<select multiple='true' size='4' name='id_matiere".$j."[]'>\n";
					echo "<option value=''>&nbsp;</option>\n";
					for($k=0;$k<$cpt;$k++){
						echo "<option value='$tab_mat_classes[$k]'>$tab_mat_classes[$k]</option>\n";
					}
					echo "</select>\n";
					echo "</td>\n";
					*/

					echo "<td>\n";
					echo "<select multiple='true' size='4' name='id_matiere".$j."[]'>\n";
					echo "<option value=''>&nbsp;</option>\n";
					for($k=0;$k<$cpt;$k++){
						echo "<option value='$tab_mat_classes[$k]'";
						$sql="SELECT * FROM notanet_corresp WHERE notanet_mat='".$tabmatieres[$j][0]."' AND matiere='".$tab_mat_classes[$k]."'";
						$res_test=mysql_query($sql);
						if(mysql_num_rows($res_test)>0){
							echo " selected='true'";
						}
						echo ">$tab_mat_classes[$k]</option>\n";
					}
					echo "</select>\n";
					echo "</td>\n";

					echo "</tr>\n";
				}
			}
			echo "</table>\n";

			echo "<p>Le fichier d'export Notanet doit-il avoir des fins de lignes Unix ou Dos?<br /><input type='radio' name='finsdelignes' value='dos' checked /> Fins de lignes DOS<br /><input type='radio' name='finsdelignes' value='unix' /> Fins de lignes UNIX</p>\n";

			echo "<input type='submit' name='choix_matieres' value='Envoyer' />\n";
			echo "</form>\n";

			echo "<p><i>NOTES:</i></p>\n";
			echo "<ul>\n";
			echo "<li><p>La désignation comme optionnelle de certaines matières ci-dessus ne correspond pas nécessairement au caractère optionnel d'une matière dans NOTANET, mais au fait que l'on ne considère pas comme une erreur le fait qu'un élève n'ait pas de moyenne saisie dans cette matière (<i>qu'on ne trouve pas de moyenne dans la table 'matiere_notes'</i>).</p></li>\n";
			echo "<li><p>Certaines erreurs seront sans doute signalées parce que certains élèves sont dispensés, absents,... sur certaines matières.<br />Il sera alors possible de saisir les valeurs autorisées DI, AB,... avant de générer un fichier CSV complet.</p></li>\n";
			//echo "<li><p></p></li>\n";
			echo "<li><p>Il est possible de sélectionner plusieurs matières pour une option (<i>ex.: AGL1 et ALL1 pour la Langue vivante 1</i>) en utilisant CTRL+clic avec la souris.<br />
			(<i>on parle de sélection multiple</i>)</p></li>\n";
			echo "</ul>\n";

			if($type_brevet==2){
				echo "<p><b>ATTENTION:</b></p>\n";
				echo "<blockquote>\n";
				echo "<p>Pour le Brevet de série PROFESSIONNELLE, sans option de série, il faut cocher 'optionnelle' la LV1 et les Sciences-Physiques, puisque chaque élève n'a de notes que dans l'une ou l'autre.<br />Ne pas cocher cette case conduirait à considérer qu'il manque une moyenne qui en LV1, qui en Sciences-Physiques pour chaque élève et une erreur serait affichée sans production des lignes de l'export NOTANET.</p>\n";
				echo "<p>L'inconvénient: si un élève n'a de moyenne ni en LV1, ni en Sciences-physiques, cela ne sera pas signalé comme une erreur alors que cela devrait l'être...<br />En attendant une éventuelle amélioration du dispositif, il convient de contrôler manuellement (de visu) de tels manques.</p>\n";
				echo "<p><br /></p>";
				echo "<p><b>GROS DOUTE:</b> Est-ce qu'un élève peut suivre les deux (LV1 et Sc-Phy) et choisir la matière à retenir pour le Brevet?<br />Si oui, je n'ai pas géré ce cas... il faut corriger (vider) la matière non souhaitée pour chaque élève dans le prochain formulaire.</p>";
				echo "</blockquote>\n";
			}
		}
		else {
			echo "</div>\n";

			$choix_corrections=isset($_POST['choix_corrections']) ? $_POST['choix_corrections'] : NULL;
			$choix_matieres=isset($_POST['choix_matieres']) ? $_POST['choix_matieres'] : NULL;

			$finsdelignes=isset($_POST['finsdelignes']) ? $_POST['finsdelignes'] : "dos";
			if($finsdelignes=="dos"){$eol="\r\n";}else{$eol="\n";}

			if(!isset($choix_corrections)){


				// Nettoyage des choix de matières dans 'notanet_corresp'
				$sql="DELETE FROM notanet_corresp";
				$res_nettoyage=mysql_query($sql);
				if(!$res_nettoyage){
					echo "<p><b style='color:red;'>ERREUR</b> lors du nettoyage de la table 'notanet_corresp'.</p>\n";
				}

				// Enregistrement des choix de matières dans 'notanet_corresp'
				for($j=101;$j<=122;$j++){
					if($tabmatieres[$j][0]!=''){
						//$tabmatieres[$j][0]

						//if(count($id_matiere[$j])>0){
						if(isset($id_matiere[$j])){
							for($i=0;$i<count($id_matiere[$j]);$i++){
								$sql="INSERT INTO notanet_corresp SET notanet_mat='".$tabmatieres[$j][0]."',
																		matiere='".$id_matiere[$j][$i]."',
																		statut='".$statut_matiere[$j]."'";
								$res_insert=mysql_query($sql);
							}
						}
						else{
							// Cas de matières non dispensées...
							$sql="INSERT INTO notanet_corresp SET notanet_mat='".$tabmatieres[$j][0]."',
																	matiere='',
																	statut='".$statut_matiere[$j]."'";
							$res_insert=mysql_query($sql);
						}
					}
				}


				//$dossier_notanet='../backup/notanet';
				$sql="SELECT value FROM setting WHERE name='backup_directory'";
				$resultat_bd=mysql_query($sql);
				if(mysql_num_rows($resultat_bd)>0){
					$ligne_bd=mysql_fetch_object($resultat_bd);
					//$dossier_notanet='../backup/'.$ligne_bd->value.'/notanet';
					$dossier_notanet='../backup/'.$ligne_bd->value.'/csv';
				}
				else{
					//$dossier_notanet='../backup/notanet';
					$dossier_notanet='../backup/csv';
				}
				if(!file_exists($dossier_notanet)){
					mkdir($dossier_notanet);
					$fichtmp=fopen($dossier_notanet."/index.html","w");
					fwrite($fichtmp,"<script language='JavaScript'>\n");
					fwrite($fichtmp,"    document.location.replace('../../../login.php')\n");
					fwrite($fichtmp,"</script>\n");
					fclose ($fichtmp);
				}
				$fich_notanet=$dossier_notanet."/notanet_".date('Y.m.d_H.i.s_').ereg_replace(" ","_",microtime()).".csv";

				$tabnotanet=array();

				//echo ereg_replace(" ","_",microtime())."<br />";

				echo "<form action='".$_SERVER['PHP_SELF']."' name='form_corrections' method='post'>\n";

				echo "<input type='hidden' name='finsdelignes' value='$finsdelignes' />\n";

				echo "<input type='hidden' name='choix1' value='export' />\n";
				echo "<input type='hidden' name='type_brevet' value='$type_brevet' />\n";
				echo "<input type='hidden' name='choix_matieres' value='$choix_matieres' />\n";
				echo "<input type='hidden' name='fich_notanet' value='$fich_notanet' />\n";
				for($i=0;$i<count($id_classe);$i++){
					echo "<input type='hidden' name='id_classe[$i]' value='$id_classe[$i]' />\n";
				}
				for($j=101;$j<=122;$j++){
					if($tabmatieres[$j][0]!=''){
						echo "<input type='hidden' name='statut_matiere[$j]' value='$statut_matiere[$j]' />\n";
					}
				}

				echo "<h3>Associations de matières</h3>\n";
				echo "<blockquote>\n";
				for($j=101;$j<=122;$j++){
					if($tabmatieres[$j][0]!=''){
						$temoin_erreur="";

						//echo "<p><b>".$tabmatieres[$j][0]."</b><br />\n";
						echo "<p><b>".$tabmatieres[$j][0]."</b>: \n";
						if((isset($id_matiere[$j]))&&(isset($id_matiere[$j][0]))){
							if((count($id_matiere[$j])>0)&&($id_matiere[$j][0]!="")){
								echo $id_matiere[$j][0];
								//echo "<input type='hidden' name='id_matiere.".$j."[0]' value='".$id_matiere[$j][0]."'>\n";
								echo "<input type='hidden' name='id_matiere_".$j."[0]' value='".$id_matiere[$j][0]."' />\n";
								for($k=1;$k<count($id_matiere[$j]);$k++){
									//echo $id_matiere[$j][$k]."<br />\n";
									echo ", ".$id_matiere[$j][$k];
									//echo "<input type='hidden' name='id_matiere.".$j."[$k]' value='".$id_matiere[$j][$k]."'>\n";
									echo "<input type='hidden' name='id_matiere_".$j."[$k]' value='".$id_matiere[$j][$k]."' />\n";
								}
							}
							else{
								echo "Pas de matière sélectionnée.";
								if($statut_matiere[$j]=="imposee"){$temoin_erreur="oui";}
							}
						}
						else{
							echo "Pas de matière sélectionnée.";
							if($statut_matiere[$j]=="imposee"){$temoin_erreur="oui";}
						}
						echo " - (<i>$statut_matiere[$j]</i>)";
						echo "</p>\n";

						if($temoin_erreur=="oui"){
							echo "<p style='color:red;'><b>Erreur:</b> Une matière au statut 'imposee' n'a pas été sélectionnée.</p>\n";
							echo "</blockquote>\n";
							echo "</form>\n";
							echo "</body>\n";
							echo "</html>\n";
							die();
						}
					}
				}
				echo "</blockquote>\n";


				echo "<h3>Extraction des moyennes</h3>\n";
				// Boucle élèves:
				$num_eleve=0;
				for($i=0;$i<count($id_classe);$i++){
					$classe=get_classe_from_id($id_classe[$i]);
					echo "<h4>Classe de ".$classe."</h4>\n";
					echo "<blockquote>\n";
					/*
					$conditions="c.id_classe='$id_classe[0]'";
					for($i=1;$i<count($id_classe);$i++){
						$conditions=$conditions." OR c.id_classe='$id_classe[$i]'";
					}
					*/
					//$call_eleve = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' and e.login = c.login) order by nom,prenom");
					//$call_eleve = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (($conditions) and e.login = c.login) order by nom,prenom");
					//$call_eleve = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (($conditions) and e.login = c.login) order by c.id_classe,nom,prenom");
					$call_eleve = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe='$id_classe[$i]' and e.login = c.login) order by c.id_classe,nom,prenom");
					$nombreligne = mysql_num_rows($call_eleve);
					while($ligne=mysql_fetch_object($call_eleve)){
						// Témoin destiné à signaler les élèves pour lesquels une erreur se produit.
						$temoin_notanet_eleve="";
						$info_erreur="";

						echo "<p>\n";
						if($ligne->no_gep==""){
							echo "<b style='color:red;'>ERREUR:</b> Numéro INE non attribué: $ligne->nom $ligne->prenom<br />";
							$temoin_notanet_eleve="ERREUR";
							$info_erreur="Pas de numéro INE";
							echo "INE: <input type='text' name='INE[$num_eleve]' value='' />\n";
						}
						else{
							echo "$ligne->nom $ligne->prenom $ligne->no_gep<br />\n";
							$INE=$ligne->no_gep;
							echo "INE: <input type='text' name='INE[$num_eleve]' value='$INE' />\n";
						}
						// Guillemets sur la valeur à cause des apostrophes dans des noms...
						echo "<input type='hidden' name='nom_eleve[$num_eleve]' value=\"$ligne->nom $ligne->prenom ($classe)\" />\n";
						echo "</p>\n";


						// Tableau destiné à présenter à gauche, le tableau des notes, moyennes,... et à droite les commentaires/erreurs et éventuellement les lignes du fichier d'export.
						echo "<table border='1'>\n";
						echo "<tr>\n";
						echo "<td valign='top'>\n";

						//$TOT=0;
						echo "<table border='1'>\n";
						$sql="SELECT DISTINCT num_periode FROM periodes WHERE id_classe='$id_classe[$i]' ORDER BY num_periode";
						//echo "<td>$sql</td>";
						$resultat_periodes=mysql_query($sql);
						echo "<tr style='font-weight: bold; text-align:center;'>\n";
						echo "<td>Matière</td>\n";
						echo "<td>Moyenne</td>\n";
						while($ligne_periodes=mysql_fetch_object($resultat_periodes)){
							echo "<td>T $ligne_periodes->num_periode</td>\n";
						}
						echo "<td>Moyenne</td>\n";
						echo "<td>Correction</td>\n";
						echo "</tr>\n";
						for($j=101;$j<=122;$j++){


							// Initialisation de la moyenne pour la matière NOTANET courante.
							$moy_NOTANET[$j]="";


							// Compteur destiné à repérer des matières pour lesquelles l'élève aurait des notes dans plus d'une option.
							// On ne sait alors pas quelle valeur retenir
							$cpt=0;
							//if($tabmatieres[$j][0]!=''){
							if(($tabmatieres[$j][0]!='')&&($statut_matiere[$j]!='non dispensee dans l etablissement')){

								//$ligne_NOTANET="$INE|$j";

								$moyenne=NULL;
								//echo "<p><b>".$tabmatieres[$j][0]."</b><br />\n";
								for($k=0;$k<count($id_matiere[$j]);$k++){
									echo "<tr>\n";
									//echo $id_matiere[$j][$k]."<br />\n";
									// A FAIRE: REQUETE moyenne pour la matière... si non vide... (test si note!="-" aussi?)

									//$sql="SELECT round(avg(n.note),1) as moyenne FROM matieres_notes n, j_eleves_classes c WHERE (n.periode='$num_periode' AND n.matiere='$matiere[$j]' AND c.id_classe='$id_classe' AND c.login = n.login AND n.statut =''  AND c.periode='$num_periode')";

									echo "<td>".$id_matiere[$j][$k]."</td>\n";

									$temoin_moyenne="";
									//======================================================================
									//$sql="SELECT round(avg(note),1) as moyenne FROM matieres_notes WHERE (matiere='".$id_matiere[$j][$k]."' AND login='$ligne->login' AND statut ='')";
									$sql="SELECT round(avg(mn.note),1) as moyenne FROM matieres_notes mn, j_groupes_matieres jgm WHERE (jgm.id_matiere='".$id_matiere[$j][$k]."' AND mn.login='$ligne->login' AND mn.statut ='' AND mn.id_groupe=jgm.id_groupe)";
									//echo "$sql<br />\n";
									$resultat_moy=mysql_query($sql);
									if(mysql_num_rows($resultat_moy)>0){
										$ligne_moy=mysql_fetch_object($resultat_moy);
										//echo "$ligne_moy->moyenne<br />";
										echo "<td style='font-weight:bold; text-align:center;'>$ligne_moy->moyenne</td>\n";
										//$cpt++;
										if($ligne_moy->moyenne!=""){
											$temoin_moyenne="oui";
										}
									}
									else{
										//echo "X<br />\n";
										// On ne passe jamais par là.
										// Le calcul de la moyenne avec $resultat_moy retourne NULL et on a toujours mysql_num_rows($resultat_moy)=1
										echo "<td style='font-weight:bold; text-align:center;'>X</td>\n";
									}
									echo "<!--\$temoin_moyenne=$temoin_moyenne-->\n";
									// Cette solution donne les infos, mais ne permet pas de contrôler si tout est OK...
									//======================================================================

									$total=0;
									$nbnotes=0;
									$sql="SELECT DISTINCT num_periode FROM periodes WHERE id_classe='$id_classe[$i]' ORDER BY num_periode";
									//echo "<td>$sql</td>";
									$resultat_periodes=mysql_query($sql);
									while($ligne_periodes=mysql_fetch_object($resultat_periodes)){
										//$sql="SELECT * FROM matieres_notes WHERE (matiere='".$id_matiere[$j][$k]."' AND login='$ligne->login' AND statut ='') ORDER BY periode";
										//$sql="SELECT * FROM matieres_notes WHERE (matiere='".$id_matiere[$j][$k]."' AND login='$ligne->login' AND statut ='' AND periode='$ligne_periodes->num_periode')";

										//===================================================================
										// SUR LE STATUT... IL FAUDRAIT VOIR CE QUE DONNENT LES dispensés,...
										// POUR POUVOIR LES CODER DANS L'EXPORT NOTANET
										//===================================================================
										//$sql="SELECT * FROM matieres_notes WHERE (matiere='".$id_matiere[$j][$k]."' AND login='$ligne->login' AND statut ='' AND periode='$ligne_periodes->num_periode')";
										$sql="SELECT mn.* FROM matieres_notes mn, j_groupes_matieres jgm WHERE (jgm.id_matiere='".$id_matiere[$j][$k]."' AND mn.login='$ligne->login' AND mn.statut ='' AND mn.periode='$ligne_periodes->num_periode' AND mn.id_groupe=jgm.id_groupe)";

										//echo "<!-- $sql -->\n";
										//echo "$sql<br />\n";
										$resultat_notes=mysql_query($sql);
										//echo "<!-- mysql_num_rows(\$resultat_notes)=".mysql_num_rows($resultat_notes)." -->\n";
										if(mysql_num_rows($resultat_notes)>0){
											if(mysql_num_rows($resultat_notes)>1){
												$infos="Erreur? Il y a plusieurs notes/moyennes pour une même période! ";
												$temoin_notanet_eleve="ERREUR";
												if($info_erreur==""){
													$info_erreur="Plusieurs notes/moyennes pour une même période.";
												}
												else{
													$info_erreur=$info_erreur." - Plusieurs notes/moyennes pour une même période.";
												}
												$chaine_couleur=" bgcolor='red'";
											}
											else{
												$infos="";
												$chaine_couleur="";
											}
											// Il ne devrait y avoir qu'une seule valeur:
											echo "<td$chaine_couleur style='text-align: center;'>\n";
											//echo "<!-- ... -->\n";
											while($ligne_notes=mysql_fetch_object($resultat_notes)){
												//echo "<td>".$infos.$ligne_notes->note."</td>\n";
												echo $infos.$ligne_notes->note." ";
												// Le test devrait toujours être vrai puisqu'on a exclu les moyennes avec un statut non vide
												if(($ligne_notes->note!="")&&($ligne_notes->note!="-")){
													// PROBLEME: S'il y a plusieurs notes pour une même période, le total est faussé et la moyenne itou...
													// ... mais cela ne devrait pas arriver, ou alors la base GEPI n'est pas nette.
													$total=$total+$ligne_notes->note;
													$nbnotes++;
													//echo "<!-- \$total=$total\n \$nbnotes=$nbnotes-->\n";
												}
											}
											echo "</td>\n";
										}
										else{

											if($temoin_moyenne=="oui"){
												$chaine_couleur=" bgcolor='yellow'";
											}
											else{
												$chaine_couleur="";
											}

											//echo "<td>X</td>\n";
											// S'il n'y a pas de moyenne avec statut vide, on cherche si un statut dispensé ou autre est dans la table 'matieres_notes':
											//$sql="SELECT * FROM matieres_notes WHERE (matiere='".$id_matiere[$j][$k]."' AND login='$ligne->login' AND periode='$ligne_periodes->num_periode')";
											$sql="SELECT mn.* FROM matieres_notes mn, j_groupes_matieres jgm WHERE (jgm.id_matiere='".$id_matiere[$j][$k]."' AND mn.login='$ligne->login' AND mn.periode='$ligne_periodes->num_periode' AND mn.id_groupe=jgm.id_groupe)";
											$resultat_notes=mysql_query($sql);
											if(mysql_num_rows($resultat_notes)>0){
												$ligne_notes=mysql_fetch_object($resultat_notes);
												if($ligne_notes->statut!=""){
													$chaine_couleur=" bgcolor='red'";
												}
												echo "<td$chaine_couleur style='text-align:center;'>".$ligne_notes->note." - ".$ligne_notes->statut."</td>\n";
											}
											else{
												echo "<td$chaine_couleur style='text-align:center;'>X</td>\n";
											}
										}
									}
									if($nbnotes>0){
										$cpt++;
										$moyenne=round($total/$nbnotes,1);
										echo "<td style='font-weight:bold; text-align:center;'>$moyenne</td>\n";
										//echo "<td><input type='text' name='' value='$moyenne'></td>\n";

										/*
										//if($tabmatieres[$j][-1]=="POINTS"){
										//if(($tabmatieres[$j][-1]=="POINTS")||($tabmatieres[$j][-1]=="NOTNONCA")){
										if($tabmatieres[$j][-1]=="POINTS"){
											$ligne_NOTANET=$ligne_NOTANET."|$moyenne|";
											$TOT=$TOT+$moyenne;
										}
										else{
											if($tabmatieres[$j][-1]=="PTSUP"){
												$ptsup=$moyenne-10;
												if($ptsup>0){
													$ligne_NOTANET=$ligne_NOTANET."|$ptsup|";
													$TOT=$TOT+$ptsup;
												}
											}
											else{
												//$tabmatieres[$j][-1]="NOTNONCA";
												// On ne modifie pas... euh si... une ligne est insérée, mais elle n'intervient pas dans le calcul du TOTal.
												if($tabmatieres[$j][-1]=="NOTNONCA"){
													$ligne_NOTANET=$ligne_NOTANET."|$moyenne|";
												}
											}
										}
										*/

										//$moy_NOTANET[$j]="$moyenne";

										//echo "<td><input type='text' name='moy.$j.$k[$num_eleve]' value='$moyenne' size='6'></td>\n";
										//echo "<td><input type='text' name='moy_$j"."_"."$k[$num_eleve]' value='$moyenne' size='6'></td>\n";
										//echo "<td><input type='text' name='moy_$j"."_".$k."[$num_eleve]' value='$moyenne' size='6'></td>\n";

										//$moyenne_arrondie=round($moyenne*2)/2;
										//La note globale attribuée aux élèves dans chaque discipline, à l'issue des deux classes, est calculée sur la base de la moyenne des deux notes attribuées en quatrième et en troisième. Chaque note globale est affectée du coefficient défini par l'arrêté du 18 août 1999. Les notes globales, arrondies au demi point supérieur, sont arrêtées par le conseil des professeurs du troisième trimestre.
										$moyenne_arrondie=ceil($moyenne*2)/2;
										//echo "<td><input type='text' name='moy_$j"."_".$k."[$num_eleve]' value='".$moyenne_arrondie."' size='6' /></td>\n";
										echo "<td><input type='text' name='moy_$j"."_".$k."[$num_eleve]' value='".$moyenne_arrondie."' size='6' />";
										//echo "<input type='hidden' name='matiere_".$j."_[$num_eleve]' value='".$id_matiere[$j][$k]."' size='6' />";
										echo "</td>\n";

										//$moy_NOTANET[$j]="$moyenne";
										$moy_NOTANET[$j]="$moyenne_arrondie";

									}
									else{
										echo "<td style='font-weight:bold; text-align:center;'>X</td>\n";
										//echo "<td><input type='text' name='moy.$j.$k[$num_eleve]' value='' size='6'></td>\n";
										//echo "<td><input type='text' name='moy_$j"."_"."$k[$num_eleve]' value='' size='6'></td>\n";
										echo "<td><input type='text' name='moy_$j"."_".$k."[$num_eleve]' value='' size='6' /></td>\n";
										//echo "<td></td>\n";
									}
		/*
									else{
										if($statut_matiere[$j]=='imposee'){
											$temoin_notanet_eleve="ERREUR";
											if($info_erreur==""){
												$info_erreur="Pas de moyenne à une matière non optionnelle.";
											}
											else{
												$info_erreur=$info_erreur." - Pas de moyenne à une matière non optionnelle.";
											}
										}
									}
		*/


		/*
									//if($temoin_notanet_eleve!="ERREUR"){
									if(($temoin_notanet_eleve!="ERREUR")&&($moyenne!="")){
										echo "<td>$ligne_NOTANET</td>\n";
									}
		*/

									echo "</tr>\n";
								}
		/*
								if($temoin_notanet_eleve!="ERREUR"){
									echo "<tr><td>$ligne_NOTANET</td></tr>\n";
								}
		*/
								//echo "</p>\n";
								if($cpt==0){
									// Pas de moyenne trouvée pour cet élève.
									if($statut_matiere[$j]=='imposee'){
										// Si la matière est imposée, alors il y a un problème à régler...
										$temoin_notanet_eleve="ERREUR";
										if($info_erreur==""){
											//$info_erreur="Pas de moyenne à une matière non optionnelle: ".$id_matiere[$j][0];
											$info_erreur="Pas de moyenne à une matière non optionnelle: ".$id_matiere[$j][0]."<br />(<i>valeurs non numériques autorisées: ".$tabmatieres[$j][-3]."</i>)<br />";
											//$tabmatieres[$j][-3]
										}
										else{
											//$info_erreur=$info_erreur." - Pas de moyenne à une matière non optionnelle: ".$id_matiere[$j][0];
											$info_erreur=$info_erreur."Pas de moyenne à une matière non optionnelle: ".$id_matiere[$j][0]."<br />(<i>valeurs non numériques autorisées: ".$tabmatieres[$j][-3]."</i>)<br />";
										}
									}
								}
							}
							if($cpt>1){
								$temoin_notanet_eleve="ERREUR";
								// Un élève a des notes dans deux options d'un même choix NOTANET (par exemple AGL1 et ALL1)
								if($info_erreur==""){
									//$info_erreur="Plusieurs options d'une même matière.";
									$info_erreur="Plusieurs options d'une même matière.<br />";
								}
								else{
									//$info_erreur=$info_erreur." - Plusieurs options d'une même matière.";
									$info_erreur=$info_erreur."Plusieurs options d'une même matière.<br />";
								}
							}
						}
						echo "</table>\n";


						// Pour présenter à côté, le résultat:
						echo "</td>\n";
						echo "<td valign='top'>\n";
						if($temoin_notanet_eleve=="ERREUR"){
							echo "<b style='color:red;'>ERREUR:</b> $info_erreur";
						}
						else{
							//echo "$INE|TOT|$TOT|<br />\n";
							//echo "---";
							$TOT=0;
							echo "<p>\n";
							echo "Portion de fichier générée:<br />";
							for($j=101;$j<=122;$j++){
								// Pour les matières NOTANET existantes:
								if($tabmatieres[$j][0]!=''){
									// Si une moyenne a été extraite
									// (c'est-à-dire si l'élève a la matière et que l'extraction a réussi (donc pas d'ERREUR))
									//echo "\$tabmatieres[$j][-1]=".$tabmatieres[$j][-1]."<br />\n";
									//echo "\$moy_NOTANET[$j]=".$moy_NOTANET[$j]."<br />\n";
									if($moy_NOTANET[$j]!=""){
										$ligne_NOTANET="$INE|$j";
										if($tabmatieres[$j][-1]=="POINTS"){
											//$ligne_NOTANET=$ligne_NOTANET."|$moy_NOTANET[$j]|";
											//$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($moy_NOTANET[$j])."|";
											// Pour les brevets dans lesquels certaines notes sont sur 40 ou 60 au lieu de 20:
											$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($moy_NOTANET[$j]*$tabmatieres[$j][-2])."|";
											//$TOT=$TOT+$moy_NOTANET[$j];
											$TOT=$TOT+round($moy_NOTANET[$j]*2)/2;
										}
										else{
											if($tabmatieres[$j][-1]=="PTSUP"){
												$ptsup=$moy_NOTANET[$j]-10;
												if($ptsup>0){
													//$ligne_NOTANET=$ligne_NOTANET."|$ptsup|";
													$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($ptsup)."|";
													//$TOT=$TOT+$ptsup;
													$TOT=$TOT+round($ptsup*2)/2;
												}
												else{
													$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet(0)."|";
												}
											}
											else{
												//$tabmatieres[$j][-1]="NOTNONCA";
												// On ne modifie pas... euh si... une ligne est insérée, mais elle n'intervient pas dans le calcul du TOTal.
												if($tabmatieres[$j][-1]=="NOTNONCA"){
													//$ligne_NOTANET=$ligne_NOTANET."|$moy_NOTANET[$j]|";
													$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($moy_NOTANET[$j])."|";
												}
											}
										}
										echo colore_ligne_notanet($ligne_NOTANET)."<br />\n";
										$tabnotanet[]=$ligne_NOTANET;
										//$fichtmp=fopen($fich_notanet,"a+");
										//fwrite($fichtmp,$ligne_NOTANET."\n");
										//fclose($fichtmp);
									}
								}
							}
							//echo "$INE|TOT|$TOT|<br />\n";
							echo colore_ligne_notanet("$INE|TOT|".sprintf("%02.2f",$TOT)."|")."<br />\n";
							$tabnotanet[]="$INE|TOT|".sprintf("%02.2f",$TOT)."|";
							//$fichtmp=fopen($fich_notanet,"a+");
							// PROBLEME: $TOT peut dépasser 100... quel doit être le formatage à gauche quand on est en dessous de 100?
							//fwrite($fichtmp,"$INE|TOT|$TOT|\n");
							//fwrite($fichtmp,"$INE|TOT|".formate_note_notanet($TOT)."|\n");
							//fwrite($fichtmp,"$INE|TOT|".sprintf("%02.2f",$TOT)."|\n");
							//fclose($fichtmp);
							echo "</p>\n";
						}
						echo "</td>\n";
						echo "</tr>\n";
						echo "</table>\n";
						//echo "<hr width='200px'>\n";
						$num_eleve++;
					}
					echo "</blockquote>\n";

	/*
					if(file_exists($fich_notanet)){
						echo "<p>Pour télécharger le fichier export de NOTANET: <a href='$fich_notanet'>$fich_notanet</a></p>\n";
						echo "<p><i>Rappel:</i> Seules les élèves pour lesquels aucune erreur/indétermination n'est signalée ont leur exportation réalisée.</p>\n";
					}

					echo "<p><font color='red'>Un problème à régler:</font> Le fichier n'est pas trié par ordre de numéro INE...<br />A FAIRE...</p>\n";



					// BIZARRE... On dirait qu'il arrive là avant d'avoir atteint la fin des élèves... avant que le fichier ne soit créé.
					// TROUVé: J'étais à l'intérieur de la boucle classe.
					$cpt=0;
					unset($ligne);
					$fichtmp=fopen($fich_notanet,"r");
					while(!feof($fichtmp)) {
						$ligne[$cpt]=fgets($fichtmp, 4096);
						echo $ligne[$cpt]."<br />\n";
					}
					fclose($fichtmp);
					sort($ligne);

					$fich=fopen($fich_notanet."_tri","w");
					for($i=0;$i<count($ligne);$i++){
						//fwrite($fich,$ligne[$i]."\n");
						fwrite($fich,$ligne[$i]);
					}
					fclose ($fich);


					sort($tabnotanet);
					$fichtmp=fopen($fich_notanet."_tri","w");
					for($i=0;$i<count($tabnotanet);$i++){
						fwrite($fichtmp,$tabnotanet[$i]);
					}
					fclose ($fichtmp);
	*/

		/*
			$current_eleve_use_matiere_query = mysql_query("SELECT * FROM j_eleves_matieres
			WHERE (login='".$current_eleve_login[$i]."' AND matiere='".$current_matiere[$j]."' AND periode='$periode_num')");
			$count[$j][$i] = mysql_num_rows($current_eleve_use_matiere_query);
			if ($count[$j][$i] == "0") {
				$current_eleve_note_query = mysql_query("SELECT distinct * FROM matieres_notes
				WHERE (
				login='".$current_eleve_login[$i]."' AND
				periode='$periode_num' AND
				matiere='".$current_matiere[$j]."'
				)");
				$current_eleve_note[$j][$i] = @mysql_result($current_eleve_note_query, 0, "note");
				$current_eleve_statut[$j][$i] = @mysql_result($current_eleve_note_query, 0, "statut");
			}
			$i++;
		*/





				}

	/*
				if(file_exists($fich_notanet)){
					echo "<p>Pour télécharger le fichier export de NOTANET: <a href='$fich_notanet'>$fich_notanet</a></p>\n";
					echo "<p><i>Rappel:</i> Seules les élèves pour lesquels aucune erreur/indétermination n'est signalée ont leur exportation réalisée.</p>\n";
				}

				echo "<p><font color='red'>Un problème à régler:</font> Le fichier n'est pas trié par ordre de numéro INE...<br />A FAIRE...</p>\n";
	*/

				sort($tabnotanet);
				//$fichtmp=fopen($fich_notanet."_tri","w");
				$fichtmp=fopen($fich_notanet,"w");
				for($i=0;$i<count($tabnotanet);$i++){
					//fwrite($fichtmp,$tabnotanet[$i]."\n");
					fwrite($fichtmp,$tabnotanet[$i].$eol);
				}
				fclose ($fichtmp);
				//if(file_exists($fich_notanet."_tri")){
				if(file_exists($fich_notanet)){
					//echo "<p>Pour télécharger le fichier export de NOTANET: <a href='".$fich_notanet."_tri'>".$fich_notanet."_tri</a></p>\n";
					echo "<p>Pour télécharger le fichier export de NOTANET: <a href='".$fich_notanet."'>".$fich_notanet."</a><br />\n";
					echo "<b>Attention:</b> Ce fichier ne tient pas compte des éventuelles corrections apportées dans les champs de formulaire ci-dessus.<br />\n";
					echo "Si vous avez effectué des modifications, validez à l'aide du bouton ci-dessous et un nouveau fichier CSV sera généré.\n";
					echo "</p>\n";
					echo "<p><i>Rappel:</i> Seuls les élèves pour lesquels aucune erreur/indétermination n'est signalée ont leur exportation réalisée.</p>\n";
				}

				echo "<input type='submit' name='choix_corrections' value='Valider les corrections' />\n";
				echo "<p>Valider les corrections ci-dessus permet de générer un nouveau fichier d'export tenant compte de vos modifications.</p>";
				echo "</form>\n";

				echo "<p><i>NOTE:</i></p>\n";
				echo "<blockquote>\n";
				echo "<p>Si pour une raison ou une autre (<i>départ en cours d'année,...</i>), vous souhaitez ne pas effectuer l'export pour un/des élève(s) particulier(s), il suffit de vider la moyenne dans une matière non optionnelle.</p>\n";
				echo "</blockquote>\n";
			}
			else{
				// ******************************************************************************************
				// ******************************************************************************************
				// ******************************************************************************************
				// ******************************************************************************************

				//echo "</div>\n";
				// Génération d'un nouveau fichier d'export NOTANET avec les corrections...
				//echo "\$type_brevet=$type_brevet<br />\n";
				echo "<h3>Génération d'un fichier d'export pour un brevet série ".$tab_type_brevet[$type_brevet]."</h3>\n";

				// Création d'une table pour stocker les résultats... et permettre une exploitation en générant des fiches brevet.
				$sql="CREATE TABLE IF NOT EXISTS `notanet` (
					`login` VARCHAR( 50 ) NOT NULL ,
					`ine` TEXT NOT NULL ,
					`matiere` VARCHAR( 255 ) NOT NULL ,
					`mat` VARCHAR( 50 ) NOT NULL ,
					`note` VARCHAR( 4 ) NOT NULL,
					`id_classe` smallint(6) NOT NULL
					)";
				$res_creation_table=mysql_query($sql);
				if(!$res_creation_table){
					echo "<p><b style='color:red;'>ERREUR</b> lors de la cration de la table 'notanet'.<br />\nLes fiches brevet ne pourront pas être générées.</p>\n";
				}



				//         $tab_req[] = "ALTER TABLE temp_gep_import ADD ELENOET VARCHAR( 40 ) NOT NULL AFTER ELEDATNAIS ;";
/*
        $result .= "&nbsp;->Ajout du champ categorie_id à la table matieres<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM matieres LIKE 'categorie_id'"));
        if ($test1 == 0) {
            $query3 = mysql_query("ALTER TABLE `matieres` ADD `categorie_id` INT NOT NULL default '1' AFTER `priority`");
            if ($query3) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
        }
*/
				$test1=mysql_num_rows(mysql_query("SHOW COLUMNS FROM notanet LIKE 'mat'"));
				if($test1==0) {
					$sql="ALTER TABLE `notanet` ADD `mat` VARCHAR( 50 ) NOT NULL AFTER `matiere`";
					$res_update_table1=mysql_query($sql);
					$sql="ALTER TABLE `notanet` CHANGE `matiere` `matiere` VARCHAR( 255 )";
					$res_update_table2=mysql_query($sql);
					$sql="ALTER TABLE `notanet_corresp` CHANGE `notanet_mat` `notanet_mat` VARCHAR( 255 )";
					$res_update_table3=mysql_query($sql);
				}

				/*
				// Inutile pour la suite...
				$choix_matieres=isset($_POST['choix_matieres']) ? $_POST['choix_matieres'] : NULL;
				echo "\$choix_matieres=$choix_matieres<br />\n";
				*/
				echo "<p>Les classes prises en compte sont:</p>\n";
				echo "<ul>\n";
				for($i=0;$i<count($id_classe);$i++){
					//echo "id_classe[$i]=$id_classe[$i]<br />\n";
					echo "<li>".get_classe_from_id($id_classe[$i]);
					if($res_creation_table){
						$sql="DELETE FROM notanet WHERE id_classe='$id_classe[$i]'";
						$res_suppr=mysql_query($sql);
						if(!$res_suppr){
							echo "<br />\n";
							echo "<b style='color:red;'>ERREUR</b> lors de la suppression des entrées existantes pour la classe dans la table 'notanet'.\n";
						}
					}
					echo "</li>\n";
				}
				echo "</ul>\n";

				echo "<table border='1'>\n";
				$id_matiere=array();
				//echo "<tr style='font-weight:bold; text-align:center;'>\n";
				echo "<tr style='text-align:center;'>\n";
				echo "<td style='font-weight:bold;'>Matières NOTANET</td>\n";
				echo "<td style='font-weight:bold;'>Mode de calcul</td>\n";
				echo "<td style='font-weight:bold;'>Coefficient</td>\n";
				echo "<td style='font-weight:bold;'>Valeurs spéciales autorisées</td>\n";
				echo "<td style='font-weight:bold;'>Statut de la matière dans l'établissement</td>\n";
				echo "<td style='font-weight:bold;'>Matières GEPI</td>\n";
				echo "</tr>\n";
				for($j=101;$j<=122;$j++){
					if($tabmatieres[$j][0]!=''){
						echo "<tr style='text-align:center;'>\n";
/*
						echo "tabmatieres[$j][0]=".$tabmatieres[$j][0]."<br />\n";
						echo "tabmatieres[$j][-1]=".$tabmatieres[$j][-1]."<br />\n";
						echo "tabmatieres[$j][-2]=".$tabmatieres[$j][-2]."<br />\n";
						echo "statut_matiere[$j]=$statut_matiere[$j]<br />\n";
*/
						echo "<td>".$tabmatieres[$j][0]."</td>\n";
						echo "<td>".$tabmatieres[$j][-1]."</td>\n";
						echo "<td>".$tabmatieres[$j][-2]."</td>\n";
						echo "<td>".$tabmatieres[$j][-3]."</td>\n";
						echo "<td>".$statut_matiere[$j]."</td>\n";

						if($statut_matiere[$j]!='non dispensee dans l etablissement'){
							echo "<td>";
							//$id_matiere[$j]=$_POST['id_matiere.'.$j];
							$id_matiere[$j]=$_POST['id_matiere_'.$j];
							//echo "\$id_matiere[$j]=".$id_matiere[$j]."<br />";
							for($k=0;$k<count($id_matiere[$j]);$k++){
								//echo "<input type='hidden' name='id_matiere.".$j."[$k]' value='$id_matiere[$j][$k]'>\n";
								//echo "\$id_matiere[$j][$k]=".$id_matiere[$j][$k]."<br />";
								echo $id_matiere[$j][$k]." ";
								$moy[$j][$k]=$_POST['moy_'.$j.'_'.$k];
							}
							echo "</td>\n";
						}
						else{
							echo "<td>&nbsp;</td>\n";
						}

						// Récupération du tableau des matières par élève
						//$matiere_eleve[$j]=$_POST['matiere_'.$j.'_'];

						echo "</tr>\n";
					}
				}
				echo "</table>\n";

				$INE=$_POST['INE'];
				$nom_eleve=$_POST['nom_eleve'];
				$login_eleve="";
				$id_classe_eleve=0;
				$fich_notanet=$_POST['fich_notanet'];

				// Boucle sur la liste des élèves...
				for($m=0;$m<count($INE);$m++){
					unset($moy_NOTANET);
					$erreur="";
					//echo "INE[$m]=$INE[$m]<br />";
					echo "<p><b>$nom_eleve[$m]</b><br />\n";
					if($INE[$m]==""){
						echo "<span style='color:red'>ERREUR</span>: Pas de numéro INE pour cet élève.<br />\n";
						$erreur="oui";
					}
					else{
						$sql="SELECT login FROM eleves WHERE no_gep='".$INE[$m]."'";
						$res_login_ele=mysql_query($sql);
						if(mysql_num_rows($res_login_ele)>0){
							$lig_login_ele=mysql_fetch_object($res_login_ele);
							$login_eleve=$lig_login_ele->login;

							$sql="SELECT id_classe FROM j_eleves_classes WHERE login='$login_eleve' ORDER BY periode DESC";
							$res_classe_ele=mysql_query($sql);
							if(mysql_num_rows($res_classe_ele)>0){
								$lig_classe_ele=mysql_fetch_object($res_classe_ele);
								$id_classe_eleve=$lig_classe_ele->id_classe;
							}
							else{
								echo "<span style='color:red'>ERREUR</span>: La classe de l'élève n'a pas été récupéré.<br />Sa fiche brevet ne sera pas générée.<br />\n";
							}
						}
						else{
							echo "<span style='color:red'>ERREUR</span>: Le LOGIN de l'élève n'a pas été récupéré.<br />Sa fiche brevet ne sera pas générée.<br />\n";
						}
					}


					unset($tab_opt_matiere_eleve);
					$tab_opt_matiere_eleve=array();
					for($j=101;$j<=122;$j++){
						//if($tabmatieres[$j][0]!=''){
						if(($tabmatieres[$j][0]!='')&&($statut_matiere[$j]!='non dispensee dans l etablissement')){
							// Liste des valeurs spéciales autorisées pour la matière courante:
							unset($tabvalautorisees);
							$tabvalautorisees=explode(" ",$tabmatieres[$j][-3]);

							//$ligne_NOTANET=$INE[$m]."|$j";
							$temoin_moyenne=0;
							//for($k=0;$k<count($id_matiere[$j]);$k++){
							// Problème: Il faut tester la première valeur aussi...
							//echo $id_matiere[$j][0]."=".$moy[$j][0][$m];

							// On passe en revue les différentes options d'une même matière (LV1($j): AGL1 ou ALL1($k))
							for($k=0;$k<count($id_matiere[$j]);$k++){
								//echo " - ".$id_matiere[$j][$k]."=".$moy[$j][$k][$m];

								//$ligne_NOTANET="$INE|$j";
								//$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($moy_NOTANET[$j])."|";
								//$tabnotanet[]=$ligne_NOTANET;
								//$tabnotanet[]="$INE|TOT|".sprintf("%02.2f",$TOT)."|";

								if($moy[$j][$k][$m]!=""){
									$temoin_moyenne++;


									// L'élève fait-il ALL1 ou AGL1 parmi les options de LV1
									$tab_opt_matiere_eleve[$j]=$id_matiere[$j][$k];


									// A EFFECTUER: Contrôle des valeurs
									//...
									//if(($moy[$j][$k][$m]!="AB")&&($moy[$j][$k][$m]!="DI")&&($moy[$j][$k][$m]!="NN")){
									// Il faudrait pour chaque matière ($j) contrôler les valeurs autorisées pour la matière...
									$test_valeur_speciale_autorisee="non";
									for($n=0;$n<count($tabvalautorisees);$n++){
										if($moy[$j][$k][$m]==$tabvalautorisees[$n]){
											$test_valeur_speciale_autorisee="oui";
										}
									}
									if($test_valeur_speciale_autorisee!="oui"){
										if(strlen(ereg_replace("[0-9.]","",$moy[$j][$k][$m]))!=0){
											echo "<br /><span style='color:red'>ERREUR</span>: La valeur saisie n'est pas valide: ";
											echo $id_matiere[$j][$k]."=".$moy[$j][$k][$m];
											echo "<br />\n";
											$erreur="oui";
										}
										else{
											// Le test ci-dessous convient parce que la première matière n'est pas optionnelle...
											if(($j!=101)||($k!=0)){
												echo " - ";
											}
											// On affiche la correspondance AGL1=12.0,...
											echo $id_matiere[$j][$k]."=".$moy[$j][$k][$m];
											$moy_NOTANET[$j]=round($moy[$j][$k][$m]*2)/2;
										}
									}
									else{
										// Le test ci-dessous convient parce que la première matière n'est pas optionnelle...
										if(($j!=101)||($k!=0)){
											echo " - ";
										}
										echo "<span style='color:purple;'>".$id_matiere[$j][$k]."=".$moy[$j][$k][$m]."</span>";
										$moy_NOTANET[$j]=$moy[$j][$k][$m];
									}
								}
							}

							if($temoin_moyenne==0){
								if($statut_matiere[$j]=="imposee"){
									//echo "<br /><span style='color:red'>ERREUR</span>: Pas de moyenne à une matière non optionnelle.";
									echo "<br /><span style='color:red'>ERREUR</span>: Pas de moyenne à une matière non optionnelle: ".$id_matiere[$j][0]."<br />(<i>valeurs non numériques autorisées: ".$tabmatieres[$j][-3]."</i>)";
									echo "<br />\n";
									$erreur="oui";
								}
							}
							else{
								if($temoin_moyenne==1){
									// OK!
									// On n'a pas d'erreur jusque là...
								}
								else{
									echo "<br /><span style='color:red'>ERREUR</span>: Il y a plus d'une moyenne à deux options d'une même matière: ";
									for($k=0;$k<count($id_matiere[$j]);$k++){
										if($moy[$j][$k][$m]!=""){
											echo $id_matiere[$j][$k]."=".$moy[$j][$k][$m]." -\n";
										}
									}
									echo "<br />\n";
									$erreur="oui";
								}
							}
						}
					}
					echo "<br />\n";
					if($erreur!="oui"){
						// On génère l'export pour cet élève:
						$TOT=0;
						for($j=101;$j<=122;$j++){
							//if(isset($tabmatieres[$j][0])){
							//if(isset($statut_matiere[$j])){
							if(isset($moy_NOTANET[$j])){
								if(($tabmatieres[$j][0]!='')&&($statut_matiere[$j]!='non dispensee dans l etablissement')&&($moy_NOTANET[$j]!="")){
									$ligne_NOTANET=$INE[$m]."|$j";
									//$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($moy_NOTANET[$j])."|";
									switch($tabmatieres[$j][-1]){
										case "POINTS":
											if(($moy_NOTANET[$j]!="AB")&&($moy_NOTANET[$j]!="DI")&&($moy_NOTANET[$j]!="NN")){
												$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($moy_NOTANET[$j]*$tabmatieres[$j][-2])."|";
												$TOT=$TOT+round($moy_NOTANET[$j]*2)/2;
											}
											else{
												$ligne_NOTANET=$ligne_NOTANET."|".$moy_NOTANET[$j]."|";
											}
											break;
										case "PTSUP":
											$ptsup=$moy_NOTANET[$j]-10;
											if($ptsup>0){
												$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($ptsup)."|";
												//$TOT=$TOT+$ptsup;
												$TOT=$TOT+round($ptsup*2)/2;
											}
											else{
												$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet(0)."|";
											}
											break;
										case "NOTNONCA":
											$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($moy_NOTANET[$j])."|";
											break;
									}
									echo colore_ligne_notanet($ligne_NOTANET)."<br />\n";
									$tabnotanet[]=$ligne_NOTANET;

									if(($id_classe_eleve!=0)&&($login_eleve!="")){
										$sql="INSERT INTO notanet SET login='$login_eleve',
																	ine='".$INE[$m]."',
																	matiere='".$tabmatieres[$j][0]."',";
										if(isset($tab_opt_matiere_eleve[$j])){
											$sql.="mat='".$tab_opt_matiere_eleve[$j]."',";
										}
										if(($moy_NOTANET[$j]!="AB")&&($moy_NOTANET[$j]!="DI")&&($moy_NOTANET[$j]!="NN")){
											$sql.="note='".formate_note_notanet($moy_NOTANET[$j])."',";
										}
										else{
											$sql.="note='".$moy_NOTANET[$j]."',";
										}
										$sql.="id_classe='$id_classe_eleve'";
										$res_insert=mysql_query($sql);
										if(!$res_insert){
											echo "<span style='color:red'>ERREUR</span> lors de l'insertion des informations dans la table 'notanet'.<br />La fiche brevet ne pourra pas être générée.<br />\n";
										}
									}
								}
							}
						}
						echo colore_ligne_notanet($INE[$m]."|TOT|".sprintf("%02.2f",$TOT)."|")."<br />\n";
						$tabnotanet[]=$INE[$m]."|TOT|".sprintf("%02.2f",$TOT)."|";
						// Pour afficher 95 sous la forme 095.00:
						//echo colore_ligne_notanet($INE[$m]."|TOT|".sprintf("%06.2f",$TOT)."|")."<br />\n";
						//$tabnotanet[]=$INE[$m]."|TOT|".sprintf("%06.2f",$TOT)."|";
					}
					echo "=========================</p>\n";
				}

				sort($tabnotanet);
				$fichtmp=fopen($fich_notanet."_corrige.csv","w");
				for($i=0;$i<count($tabnotanet);$i++){
					//fwrite($fichtmp,$tabnotanet[$i]."\n");
					fwrite($fichtmp,$tabnotanet[$i].$eol);
				}
				fclose ($fichtmp);
				if(file_exists($fich_notanet."_corrige.csv")){
					echo "<p>Pour télécharger le fichier export de NOTANET: <a href='".$fich_notanet."_corrige.csv'>".$fich_notanet."_corrige.csv</a></p>\n";
					echo "<p><i>Rappel:</i> Seuls les élèves pour lesquels aucune erreur/indétermination n'est signalée ont leur exportation réalisée.</p>\n";
				}

				echo "<p><a href='notanet.php'>Retour au menu NOTANET</a>.</p>\n";
				echo "<p><a href='fiches_brevet.php'>Passer à l'édition des Fiches Brevet</a>.</p>\n";
				//echo "<td><input type='text' name='moy.$j.$k[$num_eleve]' value='$moyenne' size='6'></td>\n";
			}
		}

	/*
		$call_eleve = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' and e.login = c.login) order by nom,prenom");
		$nombreligne = mysql_num_rows($call_eleve);
	*/

	}
}

require("../lib/footer.inc.php");
?>
