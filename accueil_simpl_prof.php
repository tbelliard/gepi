<?php
/*
* $Id: accueil_simpl_prof.php 7414 2011-07-13 12:57:47Z crob $
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
//require_once("../lib/initialisations.inc.php");
$niveau_arbo = 0;
require_once("./lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	//header("Location: ../logout.php?auto=1");
	header("Location: ./logout.php?auto=1");
	die();
} else if ($resultat_session == '0') {
	//header("Location: ../logout.php?auto=1");
	header("Location: ./logout.php?auto=1");
	die();
}



// INSERT INTO `droits` VALUES ('/cahier_notes/verif_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Vérification des notes/appréciations saisies sur le bulletin', '');
// INSERT INTO `droits` VALUES ('/accueil_simpl_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Page d accueil simplifiée pour les profs', '');
if (!checkAccess()) {
	//header("Location: ../logout.php?auto=1");
	header("Location: ./logout.php?auto=1");
	die();
}


/*
//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
	die("Le module n'est pas activé.");
}
*/

$active_carnets_notes=getSettingValue("active_carnets_notes");
$active_cahiers_texte=getSettingValue("active_cahiers_texte");
$active_module_trombinoscopes=getSettingValue("active_module_trombinoscopes");

if(($active_carnets_notes!='y')&&($active_cahiers_texte!='y')&&($active_module_trombinoscopes!='y')) {
	//die("Le module n'est pas activé.");
	$sql="UPDATE preferences SET value='n' WHERE name='accueil_simpl' AND login='".$_SESSION['login']."';";
	$res=mysql_query($sql);

	header("Location: ./accueil.php");
	die();
}


// Préférences des profs à récupérer par la suite dans la table 'preferences':
/*
$pref_accueil_ct="y";
$pref_accueil_trombino="y";
// Préférences jouant sur les colspan de période:
$pref_accueil_cn="y";
$pref_accueil_bull="y";
// Le bulletin simplifié est inclus dans la partie Visualisation
//$pref_accueil_bullsimp="y";
$pref_accueil_visu="y";
$pref_accueil_liste_pdf="y";
*/

/*
function getPref($login,$item,$default){
	$sql="SELECT value FROM preferences WHERE login='$login' AND name='$item'";
	$res_prefs=mysql_query($sql);

	if(mysql_num_rows($res_prefs)>0){
		$ligne=mysql_fetch_object($res_prefs);
		return $ligne->value;
	}
	else{
		return $default;
	}
}
*/

$pref_accueil_ct=getPref($_SESSION['login'],'accueil_ct',"y");
$pref_accueil_trombino=getPref($_SESSION['login'],'accueil_trombino',"y");

// Préférences jouant sur les colspan de période:
$pref_accueil_cn=getPref($_SESSION['login'],'accueil_cn',"y");
$pref_accueil_bull=getPref($_SESSION['login'],'accueil_bull',"y");
$pref_accueil_visu=getPref($_SESSION['login'],'accueil_visu',"y");
$pref_accueil_liste_pdf=getPref($_SESSION['login'],'accueil_liste_pdf',"y");

$pref_accueil_infobulles=getPref($_SESSION['login'],'accueil_infobulles',"y");


$colspan=0;
if($pref_accueil_cn=="y"){$colspan++;}
if($pref_accueil_bull=="y"){$colspan+=2;}
//if($pref_accueil_bullsimp=="y"){$colspan++;}
if($pref_accueil_visu=="y"){
	$colspan+=2;
}
if($pref_accueil_liste_pdf=="y"){$colspan++;}


// Préférences des profs à récupérer par la suite dans la table 'preferences':
// 1: icones
// 2: textes
// 3: icones et textes
//$accueil_aff_txt_icon=3;
//$accueil_aff_txt_icon=isset($_GET['txtico']) ? $_GET['txtico'] : 1;
//$accueil_aff_txt_icon=getPref($_SESSION['login'],'accueil_aff_txt_icon',"1");
$accueil_aff_txt_icon=1;
// CELA A ETE DESACTIVE... PARCE QUE LISIBLE UNIQUEMENT EN MODE icones seuls


// Styles spacifiques à la page avec chemin relatif à la racine du Gepi:
//$style_specifique="accueil_simpl_prof.css";
$style_specifique="accueil_simpl_prof";


//**************** EN-TETE *****************
$titre_page = "Accueil GEPI";
//require_once("../lib/header.inc");
require_once("./lib/header.inc");
//**************** FIN EN-TETE *************

//echo "\$colspan=$colspan<br />";

echo "<div class='norme'><p class='bold'>\n";
//echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a> | \n";
//echo "<a href=\"./accueil.php?accueil_simpl=n\"> Accès à l'interface complète </a>";
echo "<a href=\"./accueil.php?accueil_simpl=n\">Accès au menu d'accueil</a>";
//echo " | \n";
//echo "<a href='index.php'> Carnet de notes </a> | \n";
echo " | \n";
//echo "<a href='./gestion/config_prefs.php'> Paramétrer mes interfaces simplifiées </a>\n";
echo "<a href='./gestion/config_prefs.php'> Paramétrer mon interface </a>\n";
echo "</p>\n";
echo "</div>\n";

// Liste des Accès ouverts en consultation à vos CDT
affiche_acces_cdt();

echo "<center>\n";

//Affichage des messages
include("affichage_des_messages.inc.php");

//================================
$invisibilite_groupe=array();
$invisibilite_groupe['bulletins']=array();
$invisibilite_groupe['cahier_notes']=array();
$sql="SELECT jgv.* FROM j_groupes_visibilite jgv, j_groupes_professeurs jgp WHERE jgv.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' AND jgv.visible='n';";
$res_jgv=mysql_query($sql);
if(mysql_num_rows($res_jgv)>0) {
	while($lig_jgv=mysql_fetch_object($res_jgv)) {
		$invisibilite_groupe[$lig_jgv->domaine][]=$lig_jgv->id_groupe;
	}
}
//================================


// Récupérer le nombre max de périodes
$groups=get_groups_for_prof($_SESSION["login"]);
$maxper=0;
$tab_noms_periodes=array();
$tab_num_periodes_ouvertes=array();
for($i=0;$i<count($groups);$i++){
	if($maxper<count($groups[$i]["periodes"])) {
		$maxper=count($groups[$i]["periodes"]);
	}

	for($j=1;$j<=count($groups[$i]["periodes"]);$j++){
		if(!in_array($groups[$i]["periodes"][$j]["nom_periode"],$tab_noms_periodes)){
			$tab_noms_periodes[]=$groups[$i]["periodes"][$j]["nom_periode"];
		}
	}

	foreach($groups[$i]["classes"]["classes"] as $classe){
		for($j=1;$j<=count($groups[$i]["classe"]["ver_periode"][$classe['id']]);$j++){
			if($groups[$i]["classe"]["ver_periode"][$classe['id']][$j]=="N"){
				if(!in_array($j,$tab_num_periodes_ouvertes)) {
					$tab_num_periodes_ouvertes[]=$j;
				}
			}
		}
	}
}

if(count($tab_num_periodes_ouvertes)>0){
	sort($tab_num_periodes_ouvertes);

	$affiche_periode=array();
	for($i=1;$i<=$maxper;$i++){
		$affiche_periode[$i]="n";
	}

	for($i=0;$i<count($tab_num_periodes_ouvertes);$i++){
		//echo "\$tab_num_periodes_ouvertes[$i]=$tab_num_periodes_ouvertes[$i]<br />";
		//$j=$i+1;
		//$affiche_periode[$j]="y";
		$affiche_periode[$tab_num_periodes_ouvertes[$i]]="y";
	}
}
else{
	$affiche_periode=array();
	for($i=1;$i<=$maxper;$i++){
		$affiche_periode[$i]="y";
	}
}

/*
for($i=0;$i<count($tab_noms_periodes);$i++){
	echo "\$tab_noms_periodes[$i]=$tab_noms_periodes[$i]<br />";
}
*/

$nb_groupes=count($groups);


echo "<script type='text/javascript'>
	function valide_bull_simpl(id_classe,num_periode){
		document.getElementById('id_classe').value=id_classe;
		document.getElementById('periode1').value=num_periode;
		document.getElementById('periode2').value=num_periode;
		document.form_choix_edit.submit();
	}

	/*
	function valide_trombino(id_classe){
		document.getElementById('classe').value='c-'+id_classe;
		document.form_trombino.submit();
	}
	*/

	function valide_trombino(id_groupe){
		//document.getElementById('classe').value='g-'+id_groupe;
		document.getElementById('groupe').value=id_groupe;
		document.form_trombino.submit();
	}

	function valide_liste_pdf(id_groupe,num_periode){
		document.getElementById('id_groupes').value=id_groupe;
		document.getElementById('id_periode').value=num_periode;
		document.form_liste_pdf.submit();
	}



	function modif_col(num_periode,mode){
		if(mode=='affiche'){
			if(document.getElementById('h_lien_affiche_'+num_periode)){
				document.getElementById('h_lien_affiche_'+num_periode).style.display='none';
			}
			if(document.getElementById('h_lien_cache_'+num_periode)){
				document.getElementById('h_lien_cache_'+num_periode).style.display='';
			}

			if(document.getElementById('h_cn_'+num_periode)){
				document.getElementById('h_cn_'+num_periode).style.display='';
			}
			if(document.getElementById('h_b_'+num_periode)){
				document.getElementById('h_b_'+num_periode).style.display='';
			}
			if(document.getElementById('h_v_'+num_periode)){
				document.getElementById('h_v_'+num_periode).style.display='';
			}

			if(document.getElementById('h_bn_'+num_periode)){
				document.getElementById('h_bn_'+num_periode).style.display='';
			}
			if(document.getElementById('h_ba_'+num_periode)){
				document.getElementById('h_ba_'+num_periode).style.display='';
			}
			if(document.getElementById('h_g_'+num_periode)){
				document.getElementById('h_g_'+num_periode).style.display='';
			}
			if(document.getElementById('h_bs_'+num_periode)){
				document.getElementById('h_bs_'+num_periode).style.display='';
			}

			if(document.getElementById('h_liste_pdf_'+num_periode)){
				document.getElementById('h_liste_pdf_'+num_periode).style.display='';
			}

			// Pour afficher/cacher les lignes du tableau, évaluer count($groups)=$nb_groupes
			for(i=0;i<=$nb_groupes;i++){
				if(document.getElementById('h_cn_'+i+'_'+num_periode)){
					document.getElementById('h_cn_'+i+'_'+num_periode).style.display='';
				}
				if(document.getElementById('h_bn_'+i+'_'+num_periode)){
					document.getElementById('h_bn_'+i+'_'+num_periode).style.display='';
				}
				if(document.getElementById('h_ba_'+i+'_'+num_periode)){
					document.getElementById('h_ba_'+i+'_'+num_periode).style.display='';
				}
				if(document.getElementById('h_g_'+i+'_'+num_periode)){
					document.getElementById('h_g_'+i+'_'+num_periode).style.display='';
				}
				if(document.getElementById('h_bs_'+i+'_'+num_periode)){
					document.getElementById('h_bs_'+i+'_'+num_periode).style.display='';
				}
				if(document.getElementById('h_listes_'+i+'_'+num_periode)){
					document.getElementById('h_listes_'+i+'_'+num_periode).style.display='';
				}
			}
		}
		else{
			if(document.getElementById('h_lien_affiche_'+num_periode)){
				document.getElementById('h_lien_affiche_'+num_periode).style.display='';
			}

			if(document.getElementById('h_lien_cache_'+num_periode)){
				document.getElementById('h_lien_cache_'+num_periode).style.display='none';
			}

			if(document.getElementById('h_cn_'+num_periode)){
				document.getElementById('h_cn_'+num_periode).style.display='none';
			}
			if(document.getElementById('h_b_'+num_periode)){
				document.getElementById('h_b_'+num_periode).style.display='none';
			}
			if(document.getElementById('h_v_'+num_periode)){
				document.getElementById('h_v_'+num_periode).style.display='none';
			}

			if(document.getElementById('h_bn_'+num_periode)){
				document.getElementById('h_bn_'+num_periode).style.display='none';
			}
			if(document.getElementById('h_ba_'+num_periode)){
				document.getElementById('h_ba_'+num_periode).style.display='none';
			}
			if(document.getElementById('h_g_'+num_periode)){
				document.getElementById('h_g_'+num_periode).style.display='none';
			}
			if(document.getElementById('h_bs_'+num_periode)){
				document.getElementById('h_bs_'+num_periode).style.display='none';
			}

			if(document.getElementById('h_liste_pdf_'+num_periode)){
				document.getElementById('h_liste_pdf_'+num_periode).style.display='none';
			}

			for(i=0;i<=$nb_groupes;i++){
				if(document.getElementById('h_cn_'+i+'_'+num_periode)){
					document.getElementById('h_cn_'+i+'_'+num_periode).style.display='none';
				}
				if(document.getElementById('h_bn_'+i+'_'+num_periode)){
					document.getElementById('h_bn_'+i+'_'+num_periode).style.display='none';
				}
				if(document.getElementById('h_ba_'+i+'_'+num_periode)){
					document.getElementById('h_ba_'+i+'_'+num_periode).style.display='none';
				}
				if(document.getElementById('h_g_'+i+'_'+num_periode)){
					document.getElementById('h_g_'+i+'_'+num_periode).style.display='none';
				}
				if(document.getElementById('h_bs_'+i+'_'+num_periode)){
					document.getElementById('h_bs_'+i+'_'+num_periode).style.display='none';
				}
				if(document.getElementById('h_listes_'+i+'_'+num_periode)){
					document.getElementById('h_listes_'+i+'_'+num_periode).style.display='none';
				}
			}
		}
	}

	var fen;
	function ouvre_popup_visu_groupe(id_groupe,id_classe){
		//eval(\"fen=window.open('../groupes/popup.php?id_groupe=\"+id_groupe+\"&id_classe=\"+id_classe+\"','','width=400,height=400,menubar=yes,scrollbars=yes')\");
		eval(\"fen=window.open('groupes/popup.php?id_groupe=\"+id_groupe+\"&id_classe=\"+id_classe+\"','','width=400,height=400,menubar=yes,scrollbars=yes')\");
		setTimeout('fen.focus()',500);
	}

</script>\n";

echo "<script type='text/javascript' src='lib/brainjar_drag.js'></script>\n";
echo "<script type='text/javascript' src='lib/position.js'></script>\n";





if($colspan>0){
	for($i=1;$i<=$maxper;$i++){
		$colspan_per[$i]=$colspan;

		$test_acces_bull_simp[$i]="n";
		for($k=0;$k<count($groups);$k++){
			if ((getSettingValue("GepiAccesBulletinSimpleProf") == "yes")||(getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes")) {
				$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
										j_groupes_professeurs jgp
								WHERE jeg.id_groupe= jgp.id_groupe AND
										jeg.periode='$i' AND
										jgp.login='".$_SESSION['login']."' AND
										jeg.id_groupe='".$groups[$k]['id']."' LIMIT 1;";
				$res_test_acces_bull_simp=mysql_num_rows(mysql_query($sql));
				if($res_test_acces_bull_simp>0) {$test_acces_bull_simp[$i]="y";break;}
			}

			if((getSettingValue("GepiAccesBulletinSimplePP") == "yes")&&($test_acces_bull_simp[$i]=="n")) {
				$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
												j_eleves_professeurs jep
										WHERE jep.login=jeg.login AND
												jeg.periode='$i' AND
												jep.professeur='".$_SESSION['login']."' AND
												jeg.id_groupe='".$groups[$k]['id']."' LIMIT 1;";
				//echo "$sql<br />";
				$res_test_acces_bull_simp=mysql_num_rows(mysql_query($sql));
				if($res_test_acces_bull_simp>0) {$test_acces_bull_simp[$i]="y";break;}
			}
		}

		if($test_acces_bull_simp[$i]=="n") {
			$colspan_per[$i]--;
		}
	}
}





//echo "<table border='1'>\n";
echo "<table class='contenu' summary=\"Tableau de la liste des enseignements avec les liens vers le Carnet de notes, les bulletins, les graphes,...\">\n";
echo "<tr>\n";
echo "<th";
//if($colspan>0){echo " rowspan='3'";}
if(($active_carnets_notes=="y")&&($pref_accueil_cn=="y")&&($colspan>0)){echo " rowspan='3'";}
echo ">\n";
echo "Groupe</th>\n";

echo "<th";
//if($colspan>0){echo " rowspan='3'";}
if(($active_carnets_notes=="y")&&($pref_accueil_cn=="y")&&($colspan>0)){echo " rowspan='3'";}
echo ">\n";
echo "Classes</th>\n";

//if($active_cahiers_texte=="y"){
if(($active_cahiers_texte=="y")&&($pref_accueil_ct=="y")){
	echo "<th";
	//if($colspan>0){echo " rowspan='3'";}
	if(($active_carnets_notes=="y")&&($pref_accueil_cn=="y")&&($colspan>0)){echo " rowspan='3'";}
	echo ">\n";
	echo "Cahier de Textes\n";
	echo "</th>\n";
}


//if($active_module_trombinoscopes=="y"){
if(($active_module_trombinoscopes=="y")&&($pref_accueil_trombino=="y")){
	echo "<th";
	//if($colspan>0){echo " rowspan='3'";}
	if(($active_carnets_notes=="y")&&($pref_accueil_cn=="y")&&($colspan>0)){echo " rowspan='3'";}
	echo ">\n";
	echo "Trombino<br />scope\n";
	echo "</th>\n";
}

//echo "count(\$tab_noms_periodes)=".count($tab_noms_periodes)."<br />";
//echo "\$maxper=$maxper<br />";

if(($active_carnets_notes=="y")&&($pref_accueil_cn=="y")){
	if($colspan>0){
		if(count($tab_noms_periodes)!=$maxper) {
			for($i=1;$i<=$maxper;$i++){
				//echo "<th colspan='$colspan'>";
				echo "<th colspan='".$colspan_per[$i]."'>";
				/*
				if($affiche_periode[$i]=="y"){
					echo "Période $i";
				}
				else{
				*/
					echo "<span id='h_lien_affiche_$i'>";
					echo "<a href='#' onClick=\"modif_col($i,'affiche');return false;\">";
					echo "P$i";
					echo "</a>";
					echo "</span>\n";
	
					echo "<span id='h_lien_cache_$i'>";
					echo "<a href='#' onClick=\"modif_col($i,'cache');return false;\">";
					echo "Période $i";
					echo "</a>";
					echo "</span>\n";
				//}
				echo "</th>\n";
			}
		}
		else{
			for($i=0;$i<count($tab_noms_periodes);$i++){
				$j=$i+1;
				//echo "<th colspan='$colspan'>";
				echo "<th colspan='".$colspan_per[$j]."'>";
				/*
				if($affiche_periode[$j]=="y"){
					echo "$tab_noms_periodes[$i]";
				}
				else{
					echo "P$j\n";
				}
				*/
					echo "<span id='h_lien_affiche_$j'>";
					echo "<a href='#' onClick=\"modif_col($j,'affiche');return false;\">";
					echo "P$j";
					echo "</a>";
					echo "</span>\n";
	
					echo "<span id='h_lien_cache_$j'>";
					echo "<a href='#' onClick=\"modif_col($j,'cache');return false;\">";
					echo $tab_noms_periodes[$i];
					echo "</a>";
					echo "</span>\n";
				echo "</th>\n";
			}
		}
	}
}
echo "</tr>\n";


if(($active_carnets_notes=="y")&&($pref_accueil_cn=="y")){
	//$test_acces_bull_simp=array();
	if($colspan>0){
		echo "<tr>\n";
		for($i=1;$i<=$maxper;$i++){
			//if($affiche_periode[$i]=="y"){
			if($pref_accueil_cn=="y"){
				echo "<th rowspan='2'>\n";
				echo "<span id='h_cn_$i'>Carnet de notes</span>\n";
				echo "</th>\n";
			}
	
			if($pref_accueil_bull=="y"){
				echo "<th colspan='2'>\n";
				echo "<span id='h_b_$i'>Bulletin</span>\n";
				echo "</th>\n";
			}
	
			if($pref_accueil_visu=="y"){
				/*
				$test_acces_bull_simp[$i]="n";
				for($k=0;$k<count($groups);$k++){
					if ((getSettingValue("GepiAccesBulletinSimpleProf") == "yes")||(getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes")) {
						$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
												j_groupes_professeurs jgp
										WHERE jeg.id_groupe= jgp.id_groupe AND
												jeg.periode='$j' AND
												jgp.login='".$_SESSION['login']."';";
						$res_test_acces_bull_simp=mysql_num_rows(mysql_query($sql));
						if($res_test_acces_bull_simp>0) {$test_acces_bull_simp[$i]="y";break;}
					}
	
					if((getSettingValue("GepiAccesBulletinSimplePP") == "yes")&&($test_acces_bull_simp[$i]=="n")) {
						$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
														j_eleves_professeurs jep
												WHERE jep.login=jeg.login AND
														jeg.periode='$i' AND
														jep.professeur='".$_SESSION['login']."';";
						$res_test_acces_bull_simp=mysql_num_rows(mysql_query($sql));
						if($res_test_acces_bull_simp>0) {$test_acces_bull_simp[$i]="y";break;}
					}
				}
				*/
	
				if($test_acces_bull_simp[$i]=="y") {
					echo "<th colspan='2'>\n";
				}
				else {
					echo "<th>\n";
				}
				echo "<span id='h_v_$i'>Visualisation</span>\n";
				echo "</th>\n";
			}
	
			if($pref_accueil_liste_pdf=="y"){
				echo "<th rowspan='2'>\n";
				echo "<span id='h_liste_pdf_$i'>Liste PDF</span>\n";
				echo "</th>\n";
			}
		}
		echo "</tr>\n";
	}
	
	//$test_acces_bull_simp=array();
	if($colspan>0){
		echo "<tr>\n";
		for($i=1;$i<=$maxper;$i++){
			//if($affiche_periode[$i]=="y"){
			if($pref_accueil_bull=="y"){
				echo "<th>\n";
				echo "<span id='h_bn_$i'>Notes</span>";
				echo "</th>\n";
	
				echo "<th>\n";
				echo "<span id='h_ba_$i'>Appr.</span>";
				echo "</th>\n";
			}
	
			if($pref_accueil_visu=="y"){
				echo "<th>\n";
				echo "<span id='h_g_$i'>Graphe</span>";
				echo "</th>\n";
	
				/*
				$test_acces_bull_simp[$i]="n";
				for($k=0;$k<count($groups);$k++){
					if ((getSettingValue("GepiAccesBulletinSimpleProf") == "yes")||(getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes")) {
						$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
												j_groupes_professeurs jgp
										WHERE jeg.id_groupe= jgp.id_groupe AND
												jeg.periode='$j' AND
												jgp.login='".$_SESSION['login']."';";
						$res_test_acces_bull_simp=mysql_num_rows(mysql_query($sql));
						if($res_test_acces_bull_simp>0) {$test_acces_bull_simp[$i]="y";break;}
					}
	
					if((getSettingValue("GepiAccesBulletinSimplePP") == "yes")&&($test_acces_bull_simp[$i]=="n")) {
						$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
														j_eleves_professeurs jep
												WHERE jep.login=jeg.login AND
														jeg.periode='$i' AND
														jep.professeur='".$_SESSION['login']."';";
						$res_test_acces_bull_simp=mysql_num_rows(mysql_query($sql));
						if($res_test_acces_bull_simp>0) {$test_acces_bull_simp[$i]="y";break;}
					}
				}
				*/
	
				if($test_acces_bull_simp[$i]=="y") {
					echo "<th>\n";
					echo "<span id='h_bs_$i'>Bull.Simp</span>";
					echo "</th>\n";
				}
	
			}
			//}
		}
		echo "</tr>\n";
	}
}

// https://127.0.0.1/steph/gepi-trunk/cahier_texte/index.php?id_groupe=29&year=2007&month=6&day=30&edit_devoir=
$day=date("d");
$month=date("m");
$year=date("Y");

/*
$chaine_mouse="onMouseOver=\"document.getElementById('ct_detail').style.display='';";
$chaine_mouse.=" document.getElementById('ct').style.display='none';\"";
$chaine_mouse.=" onMouseOut=\"document.getElementById('ct').style.display='';";
$chaine_mouse.=" document.getElementById('ct_detail').style.display='none';\"";
echo "<th $chaine_mouse>";
echo "<span id='ct'>\n";
echo "CT";
echo "</span>\n";

echo "<span id='ct_detail'>\n";
echo "Cahier de Textes\n";
echo "</span>\n";
echo "</th>\n";
*/



// On positionne le témoin de chargement pour éviter que les infobulles ne se ferment pas lors du chargement.
// Il faudrait plutôt remplir un tableau des infobulles existantes et faire une boucle de cacher_div() en fin de page.
/*
echo "<script type='text/javascript'>
	temporisation_chargement='ok';
</script>\n";
*/
$tab_liste_infobulles=array();



for($i=0;$i<count($groups);$i++){

	echo "<tr valign='top'>\n";
	echo "<td>".htmlentities($groups[$i]['description'])."</td>\n";

	//echo "<td>".htmlentities($groups[$i]['classlist_string'])."</td>\n";
	echo "<td>\n";
	$cpt=0;
	$liste_classes_du_groupe="";
	foreach($groups[$i]["classes"]["classes"] as $classe){
		if($cpt>0){
			echo ", ";
			$liste_classes_du_groupe.=", ";
		}
		//echo "<a href='../groupes/popup.php?id_groupe=".$groups[$i]['id']."&amp;id_classe=".$classe['id']."' onClick=\"ouvre_popup_visu_groupe('".$groups[$i]['id']."','".$classe['id']."');return false;\" target='_blank'>";
		echo "<a href='groupes/popup.php?id_groupe=".$groups[$i]['id']."&amp;id_classe=".$classe['id']."' onClick=\"ouvre_popup_visu_groupe('".$groups[$i]['id']."','".$classe['id']."');return false;\" target='_blank'";

		if($pref_accueil_infobulles=="y"){
			echo " onmouseover=\"afficher_div('info_popup_".$i."_".$cpt."','y',10,10);\" onmouseout=\"cacher_div('info_popup_".$i."_".$cpt."');\"";
		}

		echo ">";
		echo " ".$classe['classe'];
		echo "</a>\n";
		$liste_classes_du_groupe.=" ".$classe['classe'];

		if($pref_accueil_infobulles=="y"){
			//echo "<div id='info_popup_".$i."_".$cpt."' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 300px;' onmouseout=\"cacher_div('info_popup_".$i."_".$cpt."');\">Cet outil vous permet de visualiser la composition du groupe ".htmlentities($groups[$i]['description'])."(<i>".$classe['classe']."</i>).</div>\n";
			echo "<div id='info_popup_".$i."_".$cpt."' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 18em;' onmouseout=\"cacher_div('info_popup_".$i."_".$cpt."');\">Liste des élèves de ".htmlentities($groups[$i]['description'])." (<i>".preg_replace("/ /","&nbsp;",$classe['classe'])."</i>).</div>\n";

			$tab_liste_infobulles[]='info_popup_'.$i.'_'.$cpt;
		}
		/*
		echo "<script type='text/javascript'>
	cacher_div('info_popup_".$i."_".$cpt."');
</script>\n";
		*/
		$cpt++;
	}
	echo "</td>\n";

	//$liste_classes_du_groupe=trim($liste_classes_du_groupe);
	$liste_classes_du_groupe=preg_replace("/ /","&nbsp;",trim($liste_classes_du_groupe));


	//if($active_cahiers_texte=="y"){
	if(($active_cahiers_texte=="y")&&($pref_accueil_ct=="y")){
		// https://127.0.0.1/steph/gepi-trunk/cahier_texte/index.php?id_groupe=29&year=2007&month=6&day=30&edit_devoir=
		// Cahier de textes:
		echo "<td>";
		//echo "<a href='../cahier_texte/index.php?id_groupe=".$groups[$i]['id']."&amp;year=$year&amp;month=$month&amp;day=$day&amp;edit_devoir='>";
		//echo "<a href='cahier_texte/index.php?id_groupe=".$groups[$i]['id']."&amp;year=$year&amp;month=$month&amp;day=$day&amp;edit_devoir='>";
		echo "<a href='cahier_texte/index.php?id_groupe=".$groups[$i]['id']."&amp;year=$year&amp;month=$month&amp;day=$day&amp;edit_devoir='";
		if($pref_accueil_infobulles=="y"){
			echo " onmouseover=\"afficher_div('info_ct_$i','y',10,10);\" onmouseout=\"cacher_div('info_ct_$i');\"";
		}
		echo ">";

		//if(($accueil_aff_txt_icon==1)||($accueil_aff_txt_icon==3)){
			//echo "<img src='../images/icons/cahier_textes.png' width='32' height='32' alt='Cahier de textes' border='0' />";
			echo "<img src='images/icons/cahier_textes.png' width='32' height='32' alt='Cahier de textes' border='0' />";
		/*
		}
		if($accueil_aff_txt_icon==3){
			echo "<br />";
		}
		if($accueil_aff_txt_icon>=2){
			echo "Cahier de textes";
		}
		*/
		echo "</a>";

		if($pref_accueil_infobulles=="y"){
			//echo "<div id='info_ct_$i' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 300px;' onmouseout=\"cacher_div('info_ct_$i');\">Cet outil vous permet de constituer un cahier de textes pour le groupe ".htmlentities($groups[$i]['description'])."(<i>$liste_classes_du_groupe</i>).</div>\n";
			echo "<div id='info_ct_$i' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 18em;' onmouseout=\"cacher_div('info_ct_$i');\">Cahier de textes de ".htmlentities($groups[$i]['description'])." (<i>$liste_classes_du_groupe</i>).</div>\n";

			$tab_liste_infobulles[]='info_ct_'.$i;
		}

		/*
		echo "<script type='text/javascript'>
	cacher_div('info_ct_$i');
</script>\n";
		*/
		echo "</td>\n";
	}

	//if($active_module_trombinoscopes=="y"){
	if(($active_module_trombinoscopes=="y")&&($pref_accueil_trombino=="y")){
		echo "<td>\n";
		//echo "<a href='../mod_trombinoscopes/trombinoscopes.php' onClick=\"valide_trombino('".$groups[$i]['id']."'); return false;\">";
		echo "<a href='mod_trombinoscopes/trombinoscopes.php' onClick=\"valide_trombino('".$groups[$i]['id']."'); return false;\"";

		if($pref_accueil_infobulles=="y"){
			echo " onmouseover=\"afficher_div('info_trombino_$i','y',10,10);\" onmouseout=\"cacher_div('info_trombino_$i');\"";
		}
		echo ">";

		//if(($accueil_aff_txt_icon==1)||($accueil_aff_txt_icon==3)){
			//echo "<img src='../images/icons/trombino.png' width='32' height='32' alt='Trombinoscope' border='0' />";
			echo "<img src='images/icons/trombino.png' width='32' height='32' alt='Trombinoscope' border='0' />";
		/*
		}
		if($accueil_aff_txt_icon==3){
			echo "<br />";
		}
		if($accueil_aff_txt_icon>=2){
			echo "Trombinoscope";
		}
		*/
		echo "</a>\n";


		if($pref_accueil_infobulles=="y"){
			//echo "<div id='info_trombino_$i' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 300px;' onmouseout=\"cacher_div('info_trombino_$i');\">Cet outil vous permet de visualiser le trombinoscope du groupe ".htmlentities($groups[$i]['description'])."(<i>$liste_classes_du_groupe</i>).</div>\n";
			echo "<div id='info_trombino_$i' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 18em;' onmouseout=\"cacher_div('info_trombino_$i');\">Trombinoscope de ".htmlentities($groups[$i]['description'])." (<i>$liste_classes_du_groupe</i>).</div>\n";

			$tab_liste_infobulles[]='info_trombino_'.$i;
		}
		/*
		echo "<script type='text/javascript'>
	cacher_div('info_trombino_$i');
</script>\n";
		*/
		echo "</td>\n";
	}



	if(($active_carnets_notes=="y")&&($pref_accueil_cn=="y")){
		if($colspan>0){
			for($j=1;$j<=count($groups[$i]["periodes"]);$j++){
	
				//if($affiche_periode[$j]=="y"){
					$statut_verrouillage=$groups[$i]["classe"]["ver_periode"]["all"][$j];
					if($statut_verrouillage==0){
						//$couleur=" bgcolor='gray'";
						$class_style="verrouillagetot";
					}
					elseif($statut_verrouillage==3){
						//$couleur=" bgcolor='lightgreen'";
						$class_style="deverrouille";
					}
					else{
						//$couleur=" bgcolor='silver'";
						$class_style="verrouillagepart";
					}
	
					// Saisie de notes dans le carnet de notes:
					//echo "<td class='$class_style'><a href='index.php?id_groupe=".$groups[$i]['id']."&amp;periode_num=".$groups[$i]['periodes'][$j]['num_periode']."'><img src='../images/icons/notes1.png' width='16' height='16' alt='Saisie de notes' border='0' /></a></td>\n";
					if($pref_accueil_cn=="y"){
						echo "<td class='$class_style'>\n";

						if(!in_array($groups[$i]['id'],$invisibilite_groupe['cahier_notes'])) {
							echo "<div id='h_cn_".$i."_".$j."'>";
							//echo "<a href='index.php?id_groupe=".$groups[$i]['id']."&amp;periode_num=".$groups[$i]['periodes'][$j]['num_periode']."'>";
							echo "<a href='cahier_notes/index.php?id_groupe=".$groups[$i]['id']."&amp;periode_num=".$groups[$i]['periodes'][$j]['num_periode']."'";
							if($pref_accueil_infobulles=="y"){
								echo " onmouseover=\"afficher_div('info_cn_".$i."_".$j."','y',10,10);\" onmouseout=\"cacher_div('info_cn_".$i."_".$j."');\"";
							}
							echo ">";
		
							//if(($accueil_aff_txt_icon==1)||($accueil_aff_txt_icon==3)){
								//echo "<img src='../images/icons/carnet_notes.png' width='32' height='32' alt='Saisie de notes' border='0' />";
								echo "<img src='images/icons/carnet_notes.png' width='32' height='32' alt='Saisie de notes' border='0' />";
							/*
							}
							if($accueil_aff_txt_icon==3){
								echo "<br />";
							}
							if($accueil_aff_txt_icon>=2){
								echo "Saisie de notes sur le carnet de notes";
							}
							*/
							echo "</a>";
		
							if($pref_accueil_infobulles=="y"){
								//echo "<div id='info_cn_".$i."_".$j."' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 300px;' onmouseout=\"cacher_div('info_cn_".$i."_".$j."');\">Cet outil vous permet de constituer un carnet de notes pour saisir les notes de toutes vos évaluations du groupe ".htmlentities($groups[$i]['description'])."(<i>$liste_classes_du_groupe</i>) pour la période ".$groups[$i]["periodes"][$j]["nom_periode"].".</div>\n";
								echo "<div id='info_cn_".$i."_".$j."' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 18em;' onmouseout=\"cacher_div('info_cn_".$i."_".$j."');\">Carnet de notes de ".htmlentities($groups[$i]['description'])." (<i>$liste_classes_du_groupe</i>)<br />".$groups[$i]["periodes"][$j]["nom_periode"].".</div>\n";
		
								$tab_liste_infobulles[]='info_cn_'.$i.'_'.$j;
							}
							/*
							echo "<script type='text/javascript'>
			cacher_div('info_cn_".$i."_".$j."');
		</script>\n";
							*/
							echo "</div>\n";
						}
						else {echo "&nbsp;";}
						echo "</td>\n";
					}
	
	
					if($pref_accueil_bull=="y"){
						// Calcul du nombre de notes et du nombre d'appréciations présentes sur le bulletin
						$sql="SELECT 1=1 FROM matieres_notes WHERE id_groupe='".$groups[$i]['id']."' AND periode='".$groups[$i]['periodes'][$j]['num_periode']."';";
						// AND statut='' ?
						$test=mysql_query($sql);
						$nb_notes_bulletin=mysql_num_rows($test);
	
						$sql="SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='".$groups[$i]['id']."' AND periode='".$groups[$i]['periodes'][$j]['num_periode']."';";
						// AND statut='' ?
						$test=mysql_query($sql);
						$nb_app_bulletin=mysql_num_rows($test);
	
						$effectif_groupe=count($groups[$i]["eleves"][$groups[$i]['periodes'][$j]['num_periode']]["users"]);
	
	
						// Note sur le bulletin:
						//echo "<td class='$class_style'><a href='../saisie/saisie_notes.php?id_groupe=".$groups[$i]['id']."&amp;periode_num=".$groups[$i]['periodes'][$j]['num_periode']."&amp;retour_cn=yes'><img src='../images/icons/bulletin.png' width='32' height='34' alt='Notes' border='0' /></a>";
						echo "<td class='$class_style'>\n";
						if(!in_array($groups[$i]['id'],$invisibilite_groupe['bulletins'])) {
							echo "<div id='h_bn_".$i."_".$j."'>";
							//echo "<a href='../saisie/saisie_notes.php?id_groupe=".$groups[$i]['id']."&amp;periode_cn=".$groups[$i]['periodes'][$j]['num_periode']."&amp;retour_cn=yes'>";
							//echo "<a href='saisie/saisie_notes.php?id_groupe=".$groups[$i]['id']."&amp;periode_cn=".$groups[$i]['periodes'][$j]['num_periode']."&amp;retour_cn=yes'";
							echo "<a href='saisie/saisie_notes.php?id_groupe=".$groups[$i]['id']."&amp;periode_cn=".$groups[$i]['periodes'][$j]['num_periode']."'";
							if($pref_accueil_infobulles=="y"){
								echo " onmouseover=\"afficher_div('info_bn_".$i."_".$j."','y',10,10);\" onmouseout=\"cacher_div('info_bn_".$i."_".$j."');\"";
							}
							echo ">";
		
							//if(($accueil_aff_txt_icon==1)||($accueil_aff_txt_icon==3)){
								//echo "<img src='../images/icons/bulletin.png' width='32' height='34' alt='Notes' border='0' />";
								echo "<img src='images/icons/bulletin.png' width='32' height='34' alt='Notes' border='0' />";
							/*
							}
							if($accueil_aff_txt_icon==3){
								echo "<br />";
							}
							if($accueil_aff_txt_icon>=2){
								echo "Saisie de notes sur le bulletin";
							}
							*/
							echo "</a>";
		
							echo "<br />\n";
							echo "<span style='font-size: xx-small;'>";
							//if($nb_notes_bulletin==$effectif_groupe){echo "<font color='green'>";}else{echo "<font color='red'>";}
							if($nb_notes_bulletin==$effectif_groupe){echo "<span class='saisies_effectuees'>";}else{echo "<span class='saisies_manquantes'>";}
							echo "($nb_notes_bulletin/$effectif_groupe)";
							//echo "</font>";
							echo "</span>";
							echo "</span>";
		
							if($pref_accueil_infobulles=="y"){
								//echo "<div id='info_bn_".$i."_".$j."' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 300px;' onmouseout=\"cacher_div('info_bn_".$i."_".$j."');\">Cet outil permet de saisir les moyennes du bulletin du groupe ".htmlentities($groups[$i]['description'])."(<i>$liste_classes_du_groupe</i>) pour la période ".$groups[$i]["periodes"][$j]["nom_periode"].".</div>\n";
								echo "<div id='info_bn_".$i."_".$j."' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 15em;' onmouseout=\"cacher_div('info_bn_".$i."_".$j."');\">Saisie des moyennes ".htmlentities($groups[$i]['description'])." (<i>$liste_classes_du_groupe</i>)<br />".$groups[$i]["periodes"][$j]["nom_periode"].".</div>\n";
		
								$tab_liste_infobulles[]='info_bn_'.$i.'_'.$j;
							}
		
							/*
							echo "<script type='text/javascript'>
			cacher_div('info_bn_".$i."_".$j."');
		</script>\n";
							*/
							echo "</div>\n";
						}
						else {echo "&nbsp;";}
						echo "</td>\n";
	
	
						// Appréciation sur le bulletin:
						//echo "<td class='$class_style'><a href='../saisie/saisie_appreciations.php?id_groupe=".$groups[$i]['id']."&amp;periode_num=".$groups[$i]['periodes'][$j]['num_periode']."&amp;retour_cn=yes'><img src='../images/icons/bulletin.png' width='32' height='34' alt='Appréciations' border='0' /></a>";
						echo "<td class='$class_style'>\n";
						echo "<div id='h_ba_".$i."_".$j."'>";
						if(!in_array($groups[$i]['id'],$invisibilite_groupe['bulletins'])) {
							//echo "<a href='../saisie/saisie_appreciations.php?id_groupe=".$groups[$i]['id']."&amp;periode_cn=".$groups[$i]['periodes'][$j]['num_periode']."&amp;retour_cn=yes'>";
							//echo "<a href='saisie/saisie_appreciations.php?id_groupe=".$groups[$i]['id']."&amp;periode_cn=".$groups[$i]['periodes'][$j]['num_periode']."&amp;retour_cn=yes'";
							echo "<a href='saisie/saisie_appreciations.php?id_groupe=".$groups[$i]['id']."&amp;periode_cn=".$groups[$i]['periodes'][$j]['num_periode']."'";
							if($pref_accueil_infobulles=="y"){
								echo " onmouseover=\"afficher_div('info_ba_".$i."_".$j."','y',10,10);\" onmouseout=\"cacher_div('info_ba_".$i."_".$j."');\"";
							}
							echo ">";
		
							//if(($accueil_aff_txt_icon==1)||($accueil_aff_txt_icon==3)){
								//echo "<img src='../images/icons/bulletin.png' width='32' height='34' alt='Appréciations' border='0' />";
								echo "<img src='images/icons/bulletin.png' width='32' height='34' alt='Appréciations' border='0' />";
							/*
							}
							if($accueil_aff_txt_icon==3){
								echo "<br />";
							}
							if($accueil_aff_txt_icon>=2){
								echo "Saisie des appréciations sur le bulletin";
							}
							*/
							echo "</a>";
							echo "<br />\n";
		
							echo "<span style='font-size: xx-small;'>";
							//if($nb_app_bulletin==$effectif_groupe){echo "<font color='green'>";}else{echo "<font color='red'>";}
							if($nb_app_bulletin==$effectif_groupe){echo "<span class='saisies_effectuees'>";}else{echo "<span class='saisies_manquantes'>";}
							echo "($nb_app_bulletin/$effectif_groupe)";
							//echo "</font>";
							echo "</span>";
							echo "</span>";
		
		
							if($pref_accueil_infobulles=="y"){
								//echo "<div id='info_ba_".$i."_".$j."' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 300px;' onmouseout=\"cacher_div('info_ba_".$i."_".$j."');\">Cet outil permet de saisir les appréciations du bulletin du groupe ".htmlentities($groups[$i]['description'])."(<i>$liste_classes_du_groupe</i>) pour la période ".$groups[$i]["periodes"][$j]["nom_periode"].".</div>\n";
								echo "<div id='info_ba_".$i."_".$j."' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 15em;' onmouseout=\"cacher_div('info_ba_".$i."_".$j."');\">Saisie des appréciations ".htmlentities($groups[$i]['description'])." (<i>$liste_classes_du_groupe</i>)<br />".$groups[$i]["periodes"][$j]["nom_periode"].".</div>\n";
		
								$tab_liste_infobulles[]='info_ba_'.$i.'_'.$j;
							}
		
							/*
							echo "<script type='text/javascript'>
			cacher_div('info_ba_".$i."_".$j."');
		</script>\n";
							*/
							echo "</div>\n";
						}
						else {echo "&nbsp;";}
						echo "</td>\n";
					}
	
	
					if($pref_accueil_visu=="y"){
						// Graphe:
						echo "<td class='$class_style'>\n";
						echo "<div id='h_g_".$i."_".$j."'>";
						$cpt=0;
						foreach($groups[$i]["classes"]["classes"] as $classe){
							if($cpt>0){echo "<br />\n";}
							//echo "<a href='../visualisation/affiche_eleve.php?id_classe=".$classe['id']."'>";
							echo "<a href='visualisation/affiche_eleve.php?id_classe=".$classe['id']."'";
							if($pref_accueil_infobulles=="y"){
								echo " onmouseover=\"afficher_div('info_graphe_".$i."_".$j."_".$cpt."','y',10,10);\" onmouseout=\"cacher_div('info_graphe_".$i."_".$j."_".$cpt."');\"";
							}
							echo ">";
	
							//if(($accueil_aff_txt_icon==1)||($accueil_aff_txt_icon==3)){
								//echo "<img src='../images/icons/graphes.png' width='32' height='32' alt='Graphe' border='0' />";
								echo "<img src='images/icons/graphes.png' width='32' height='32' alt='Graphe' border='0' />";
							/*
							}
							if($accueil_aff_txt_icon==3){
								echo "<br />";
							}
							if($accueil_aff_txt_icon>=2){
								echo "Visualisation graphique";
								if(count($groups[$i]["classes"]["classes"])>1){echo " de";}
							}
							*/
							if(count($groups[$i]["classes"]["classes"])>1){echo " ".$classe['classe'];}
	
							echo "</a>\n";
	
	
							if($pref_accueil_infobulles=="y"){
								//echo "<div id='info_graphe_".$i."_".$j."_".$cpt."' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 300px;' onmouseout=\"cacher_div('info_graphe_".$i."_".$j."_".$cpt."');\">Visualisation graphique des résultats des élèves de la classe de ".$classe['classe']." pour la période ".$groups[$i]["periodes"][$j]["nom_periode"].".</div>\n";
								echo "<div id='info_graphe_".$i."_".$j."_".$cpt."' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 10em;' onmouseout=\"cacher_div('info_graphe_".$i."_".$j."_".$cpt."');\">Outil graphique<br />".$classe['classe']."<br />".$groups[$i]["periodes"][$j]["nom_periode"].".</div>\n";
	
								$tab_liste_infobulles[]='info_graphe_'.$i.'_'.$j.'_'.$cpt;
							}
	
							/*
							echo "<script type='text/javascript'>
		cacher_div('info_graphe_".$i."_".$j."_".$cpt."');
	</script>\n";
							*/
							$cpt++;
						}
						echo "</div>\n";
						echo "</td>\n";
	
	
						// Bulletin simplifié:
						// https://127.0.0.1/steph/gepi-trunk/prepa_conseil/index3.php?id_classe=4
						// https://127.0.0.1/steph/gepi-trunk/prepa_conseil/edit_limite.php
						// <input type=\"radio\" name=\"choix_edit\" value=\"1\" checked />
						// <select onchange=\"change_periode()\" size=1 name=\"periode1\">
						// <select size=1 name=\"periode2\">
						// <input type=hidden name=id_classe value=$id_classe />
	
						if($test_acces_bull_simp[$j]=="y") {
							echo "<td class='$class_style'>\n";
							echo "<div id='h_bs_".$i."_".$j."'>";
							$cpt=0;
							foreach($groups[$i]["classes"]["classes"] as $classe){
								if($cpt>0){echo "<br />\n";}
	
								$affiche_bull_simp_cette_classe="n";
	
								if ((getSettingValue("GepiAccesBulletinSimpleProf") == "yes")||(getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes")) {
									$affiche_bull_simp_cette_classe="y";
								}
								elseif(getSettingValue("GepiAccesBulletinSimplePP") == "yes") {
									$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
																j_eleves_professeurs jep,
																j_eleves_classes jec
															WHERE jep.login=jeg.login AND
																	jec.login=jeg.login AND
																	jec.periode=jeg.periode AND
																	jeg.periode='$j' AND
																	jec.id_classe='".$classe['id']."' AND
																	jep.professeur='".$_SESSION['login']."';";
									$res_test_affiche_bull_simp_cette_classe=mysql_num_rows(mysql_query($sql));
									//echo "$sql";
									if($res_test_affiche_bull_simp_cette_classe>0) {$affiche_bull_simp_cette_classe="y";}
								}
	
								if($affiche_bull_simp_cette_classe=="y") {
									//echo "<a href='../prepa_conseil/index3.php?id_classe=".$classe['id']."' onClick=\"valide_bull_simpl('".$classe['id']."','".$j."'); return false;\"><img src='../images/icons/bulletin_simpl.png' width='37' height='34' alt='Bulletin simplifié' border='0' />";
									//echo "<a href='../prepa_conseil/index3.php?id_classe=".$classe['id']."' onClick=\"valide_bull_simpl('".$classe['id']."','".$j."'); return false;\">";
									echo "<a href='prepa_conseil/index3.php?id_classe=".$classe['id']."' onClick=\"valide_bull_simpl('".$classe['id']."','".$j."'); return false;\"";
	
									if($pref_accueil_infobulles=="y"){
										echo " onmouseover=\"afficher_div('info_bs_".$i."_".$j."_".$cpt."','y',10,10);\" onmouseout=\"cacher_div('info_bs_".$i."_".$j."_".$cpt."');\"";
									}
									echo ">";
	
									//if(($accueil_aff_txt_icon==1)||($accueil_aff_txt_icon==3)){
										//echo "<img src='../images/icons/bulletin_simp.png' width='34' height='34' alt='Bulletin simplifié' border='0' />";
										echo "<img src='images/icons/bulletin_simp.png' width='34' height='34' alt='Bulletin simplifié' border='0' />";
									/*
									}
									if($accueil_aff_txt_icon==3){
										echo "<br />";
									}
									if($accueil_aff_txt_icon>=2){
										echo "Visualisation graphique";
										if(count($groups[$i]["classes"]["classes"])>1){echo " de";}
									}
									*/
									if(count($groups[$i]["classes"]["classes"])>1){echo " ".$classe['classe'];}
	
									echo "</a>\n";
	
									if($pref_accueil_infobulles=="y"){
										//echo "<div id='info_bs_".$i."_".$j."_".$cpt."' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 300px;' onmouseout=\"cacher_div('info_bs_".$i."_".$j."_".$cpt."');\">Ceci vous permet de visulaliser les bulletins simplifiés des élèves de la classe de ".$classe['classe']." pour la période ".$groups[$i]["periodes"][$j]["nom_periode"].".</div>\n";
										echo "<div id='info_bs_".$i."_".$j."_".$cpt."' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 10em;' onmouseout=\"cacher_div('info_bs_".$i."_".$j."_".$cpt."');\">Bulletins simplifiés<br />".$classe['classe']."<br />".$groups[$i]["periodes"][$j]["nom_periode"].".</div>\n";
	
										$tab_liste_infobulles[]='info_bs_'.$i.'_'.$j.'_'.$cpt;
									}
	
									/*
									echo "<script type='text/javascript'>
				cacher_div('info_bs_".$i."_".$j."_".$cpt."');
			</script>\n";
									*/
									$cpt++;
								}
	
							}
							echo "</div>\n";
							echo "</td>\n";
						}
					}
	
	
					if($pref_accueil_liste_pdf=="y"){
						echo "<td class='$class_style'>\n";
						echo "<div id='h_listes_".$i."_".$j."'>";
						// ?id_groupe=".$groups[$i]['id']."&amp;periode_num=".$groups[$i]['periodes'][$j]['num_periode']."
						//echo "<a href='../impression/liste_pdf.php' onClick=\"valide_liste_pdf('".$groups[$i]['id']."','".$groups[$i]['periodes'][$j]['num_periode']."'); return false;\" target='_blank'>";
						echo "<a href='impression/liste_pdf.php' onClick=\"valide_liste_pdf('".$groups[$i]['id']."','".$groups[$i]['periodes'][$j]['num_periode']."'); return false;\" target='_blank'";
						if($pref_accueil_infobulles=="y"){
							echo " onmouseover=\"afficher_div('info_liste_pdf_".$i."_".$j."','y',10,10);\" onmouseout=\"cacher_div('info_liste_pdf_".$i."_".$j."');\"";
						}
						echo ">";
	
						//if(($accueil_aff_txt_icon==1)||($accueil_aff_txt_icon==3)){
							//echo "<img src='../images/icons/bulletin_simp.png' width='34' height='34' alt='Listes PDF' border='0' />";
							echo "<img src='images/icons/bulletin_simp.png' width='34' height='34' alt='Listes PDF' border='0' />";
						/*
						}
						if($accueil_aff_txt_icon==3){
							echo "<br />";
						}
						if($accueil_aff_txt_icon>=2){
							echo "Liste PDF des élèves";
						}
						*/
						echo "</a>";
	
						if($pref_accueil_infobulles=="y"){
							//echo "<div id='info_liste_pdf_".$i."_".$j."' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 300px;' onmouseout=\"cacher_div('info_liste_pdf_".$i."_".$j."');\">Ceci vous permet d'imprimer en PDF des listes d'élèves de la classe de ".$classe['classe']." pour la période ".$groups[$i]["periodes"][$j]["nom_periode"].".</div>\n";
							echo "<div id='info_liste_pdf_".$i."_".$j."' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; width: 12em;' onmouseout=\"cacher_div('info_liste_pdf_".$i."_".$j."');\">Listes PDF des élèves<br />".$classe['classe']."<br />".$groups[$i]["periodes"][$j]["nom_periode"].".</div>\n";
	
							$tab_liste_infobulles[]='info_liste_pdf_'.$i.'_'.$j;
						}
	
						/*
						echo "<script type='text/javascript'>
		cacher_div('info_liste_pdf_".$i."_".$j."');
	</script>\n";
						*/
						echo "</div>\n";
						echo "</td>\n";
					}
	
				/*
				}
				else{
					echo "<td colspan='5'>X</td>\n";
				}
				*/
			}
	
			// On complète les colonnes à laisser vides si jamais, par exemple, on traite une ligne à deux périodes alors que d'autres groupes ont trois périodes donc trois colonnes.
			for($k=$j;$k<=$maxper;$k++){
				for($n=0;$n<$colspan;$n++){
					echo "<td>-</td>\n";
				}
				/*
				echo "<td>-</td>\n";
				echo "<td>-</td>\n";
				echo "<td>-</td>\n";
				*/
			}
		}
	}

	echo "</tr>\n";
	flush();
	/*
	echo "<p>\n";
	echo "\$groups[$i]['id']=".$groups[$i]['id']."<br />\n";
	echo "\$groups[$i]['name']=".$groups[$i]['name']."<br />\n";
	echo "\$groups[$i]['description']=".$groups[$i]['description']."<br />\n";
	echo "\$groups[$i]['matiere']['nom_complet']=".$groups[$i]['matiere']['nom_complet']."<br />\n";
	echo "\$groups[$i]['classlist_string']=".$groups[$i]['classlist_string']."<br />\n";
	echo "</p>\n";
	*/
}
echo "</table>\n";

// Formulaire validé via JavaScript pour afficher les bulletins simplifiés
//echo "<form enctype=\"multipart/form-data\" action=\"../prepa_conseil/edit_limite.php\" method=\"post\" name=\"form_choix_edit\" target=\"_blank\">";
echo "<form enctype=\"multipart/form-data\" action=\"prepa_conseil/edit_limite.php\" method=\"post\" name=\"form_choix_edit\" target=\"_blank\">\n";
echo "<input type=\"hidden\" name=\"choix_edit\" value=\"1\" />\n";
echo "<input type=\"hidden\" name=\"periode1\" id=\"periode1\" value='1' />\n";
echo "<input type=\"hidden\" name=\"periode2\" id=\"periode2\" value='1' />\n";
echo "<input type=\"hidden\" name=\"id_classe\" id=\"id_classe\" value='' />\n";
echo "</form>\n";


// Formulaire validé via JavaScript pour afficher le trombinoscope
//echo "<form enctype=\"multipart/form-data\" action=\"../mod_trombinoscopes/trombinoscopes.php\" method=\"post\" name=\"form_trombino\" target=\"_blank\">";
echo "<form enctype=\"multipart/form-data\" action=\"mod_trombinoscopes/trombinoscopes.php\" method=\"post\" name=\"form_trombino\" target=\"_blank\">\n";
echo "<input type=\"hidden\" name=\"etape\" value=\"2\" />\n";
//echo "<input type=\"hidden\" name=\"classe\" id=\"classe\" value='' />\n";
echo "<input type=\"hidden\" name=\"groupe\" id=\"groupe\" value='' />\n";
echo "</form>\n";


// Formulaire validé via JavaScript pour afficher le trombinoscope
//echo "<form enctype=\"multipart/form-data\" action=\"../impression/liste_pdf.php\" method=\"post\" name=\"form_liste_pdf\" target=\"_blank\">";
echo "<form enctype=\"multipart/form-data\" action=\"impression/liste_pdf.php\" method=\"post\" name=\"form_liste_pdf\" target=\"_blank\">\n";
echo "<input type=\"hidden\" name=\"id_periode\" id=\"id_periode\" value=\"\" />\n";
echo "<input type=\"hidden\" name=\"id_liste_groupes[]\" id=\"id_groupes\" value='' />\n";
echo "</form>\n";



echo "</center>\n";



echo "<script type='text/javascript'>
	temporisation_chargement='ok';
";

if($pref_accueil_infobulles=="y"){
	for($i=0;$i<count($tab_liste_infobulles);$i++){
		echo "cacher_div('$tab_liste_infobulles[$i]');\n";
	}
}
echo "</script>\n";



if(count($tab_num_periodes_ouvertes)>0){
	echo "<script type='text/javascript'>\n";
	for($i=1;$i<=$maxper;$i++){
		if($affiche_periode[$i]=="n"){
			echo "if(document.getElementById('h_lien_cache_'+$i)){
	document.getElementById('h_lien_cache_'+$i).style.display='none';
}

if(document.getElementById('h_cn_'+$i)){
	document.getElementById('h_cn_'+$i).style.display='none';
}
if(document.getElementById('h_b_'+$i)){
	document.getElementById('h_b_'+$i).style.display='none';
}
if(document.getElementById('h_v_'+$i)){
	document.getElementById('h_v_'+$i).style.display='none';
}

if(document.getElementById('h_bn_'+$i)){
	document.getElementById('h_bn_'+$i).style.display='none';
}
if(document.getElementById('h_ba_'+$i)){
	document.getElementById('h_ba_'+$i).style.display='none';
}
if(document.getElementById('h_g_'+$i)){
	document.getElementById('h_g_'+$i).style.display='none';
}
if(document.getElementById('h_bs_'+$i)){
	document.getElementById('h_bs_'+$i).style.display='none';
}

if(document.getElementById('h_liste_pdf_'+$i)){
	document.getElementById('h_liste_pdf_'+$i).style.display='none';
}\n";

			echo "for(i=0;i<=$nb_groupes;i++){
				if(document.getElementById('h_cn_'+i+'_'+$i)){
					document.getElementById('h_cn_'+i+'_'+$i).style.display='none';
				}
				if(document.getElementById('h_bn_'+i+'_'+$i)){
					document.getElementById('h_bn_'+i+'_'+$i).style.display='none';
				}
				if(document.getElementById('h_ba_'+i+'_'+$i)){
					document.getElementById('h_ba_'+i+'_'+$i).style.display='none';
				}
				if(document.getElementById('h_g_'+i+'_'+$i)){
					document.getElementById('h_g_'+i+'_'+$i).style.display='none';
				}
				if(document.getElementById('h_bs_'+i+'_'+$i)){
					document.getElementById('h_bs_'+i+'_'+$i).style.display='none';
				}
				if(document.getElementById('h_listes_'+i+'_'+$i)){
					document.getElementById('h_listes_'+i+'_'+$i).style.display='none';
				}
			}\n";
		}
		else{
			echo "if(document.getElementById('h_lien_affiche_'+$i)){
	document.getElementById('h_lien_affiche_'+$i).style.display='none';
}\n";
		}
	}
	echo "</script>\n";
}

//require("../lib/footer.inc.php");
echo "<p><br /></p>\n";
require("lib/footer.inc.php");
?>
