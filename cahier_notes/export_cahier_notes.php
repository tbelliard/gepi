<?php
/*
 * Last modification  : 18/04/2007
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
    die();
};

// INSERT INTO `droits` VALUES ('/cahier_notes/export_cahier_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'Export CSV/ODS du cahier de notes', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
    die("Le module n'est pas activé.");
}



unset($id_racine);
$id_racine=isset($_POST["id_racine"]) ? $_POST["id_racine"] : (isset($_GET["id_racine"]) ? $_GET["id_racine"] : NULL);
//$is_posted=isset($_POST["is_posted"]) ? $_POST["is_posted"] : NULL;
$type_export=isset($_POST["type_export"]) ? $_POST["type_export"] : NULL;

$nettoyage=isset($_GET["nettoyage"]) ? $_GET["nettoyage"] : NULL;


if($_SESSION['user_temp_directory']!='y'){
	$type_export="CSV";
}
else{
	if(getSettingValue("export_cn_ods")=='y') {
		$user_tmp=get_user_temp_directory();
		if(!$user_tmp){
			/*
			$msg="Votre dossier temporaire n'est pas accessible.";
			header("Location: index.php?msg=".rawurlencode($msg));
			die();
			*/
			// L'export ODS n'est pas possible.
			$msg="Votre dossier temporaire n'est pas accessible. Seul l'export CSV est possible.";
			$type_export="CSV";
		}

		//$chemin_temp="../temp/".getSettingValue("temp_directory");
		$chemin_temp="../temp/".$user_tmp;


		$chemin_modele_ods="export_cn_modele_ods";

		if(isset($nettoyage)){
			if(!ereg(".ods$",$nettoyage)){
				$msg="Le fichier n'est pas d'extension ODS.";
			}
			elseif(!ereg("^".$_SESSION['login'],$nettoyage)){
				$msg="Vous tentez de supprimer des fichiers qui ne vous appartiennent pas.";
			}
			else{
				if(strlen(ereg_replace("[a-zA-Z0-9_.]","",strtr($nettoyage,"-","_")))!=0){
					$msg="Le fichier proposé n'est pas valide: '".ereg_replace("[a-zA-Z0-9_.]","",strtr($nettoyage,"-","_"))."'";
				}
				else{
					if(!file_exists("$chemin_temp/$nettoyage")){
						$msg="Le fichier choisi n'existe pas.";
					}
					else{
						unlink("$chemin_temp/$nettoyage");
						$msg=rawurlencode("Suppression réussie!");
					}
				}
			}

			header("Location: index.php?msg=$msg");
			die();
		}
	}
}

// On teste si le carnet de notes appartient bien à la personne connectée
if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
    header("Location: index.php?msg=$mess");
    die();
}

//
// On dispose donc pour la suite des trois variables :
// id_racine
// id_conteneur
// id_devoir

$appel_cahier_notes = mysql_query("SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$id_groupe = mysql_result($appel_cahier_notes, 0, 'id_groupe');
$current_group = get_group($id_groupe);
$id_classe = $current_group["classes"]["list"][0];
$periode_num = mysql_result($appel_cahier_notes, 0, 'periode');

if (count($current_group["classes"]["list"]) > 1) {
    $multiclasses = true;
} else {
    $multiclasses = false;
    $order_by = "nom";
}


include "../lib/periodes.inc.php";

// On teste si la periode est vérouillée !
if (($current_group["classe"]["ver_periode"]["all"][$periode_num] <= 1) and (isset($id_devoir)) and ($id_devoir!='') ) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes dont la période est bloquée !");
    header("Location: index.php?msg=$mess");
    die();
}


$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];

$periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
$nom_periode = mysql_result($periode_query, $periode_num-1, "nom_periode");


//echo "\$is_posted=$is_posted<br />";
//echo "\$type_export=$type_export<br />";

//if((!isset($is_posted))||(!isset($type_export))){
if(!isset($type_export)){
	//**************** EN-TETE *****************
	$titre_page = "Export des notes";
	require_once("../lib/header.inc");
	//**************** FIN EN-TETE *****************

	//$titre=htmlentities($current_group['description'])." (".$nom_periode.")";
	$titre=htmlentities($current_group['name'])." ".$current_group["classlist_string"]." (".$nom_periode.")";
	$titre.=" - EXPORT";

	// Mettre la ligne de liens de retour,...
    echo "<div class='norme'><p class='bold'>\n";
    echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a>|\n";
    echo "<a href='index.php?id_groupe=".$current_group["id"]."&amp;periode_num=$periode_num'> ".htmlentities($current_group['name'])." ".$current_group["classlist_string"]." (".$nom_periode.")"." </a>|\n";
	echo "</div>";


	echo "<h2>$titre</h2>\n";
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<p>Vous pouvez effectuer un export:<br />\n";
	echo "<input type='hidden' name='id_racine' value='$id_racine' />\n";
	//echo "<input type='hidden' name='is_posted' value='yes' />\n";
	echo "<input type='radio' name='type_export' value='CSV' checked /> fichier CSV<br />\n";

	if(getSettingValue("export_cn_ods")=='y') {
		echo "<input type='radio' name='type_export' value='ODS' /> feuille de tableur ODS<br />\n";
	}
	echo "<input type='submit' name='envoyer' value='Valider' /></p>\n";
	echo "</form>\n";

	// On pourrait ajouter le ménage des fichiers de $chemin_ods ici en virant tout ce qui commence par $_SESSION['login'] et se termine par '.ods'...

	require("../lib/footer.inc.php");
	die();
}


$nom_fic=$_SESSION['login'];
$nom_fic.="_cn";
$nom_fic.="_".ereg_replace("[^a-zA-Z0-9_. - ]","",remplace_accents($current_group['description'],'all'));
$nom_fic.="_".ereg_replace("[^a-zA-Z0-9_. - ]","",remplace_accents($current_group["classlist_string"],'all'));
$nom_fic.="_".ereg_replace("[^a-zA-Z0-9_. - ]","",remplace_accents($nom_periode,'all'));

if($type_export=="CSV"){

	// Génération du CSV

	$nom_fic.=".csv";

	$now=gmdate('D, d M Y H:i:s').' GMT';
	header('Content-Type: text/x-csv');
	header('Expires: ' . $now);
	// lem9 & loic1: IE need specific headers
	if(ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
		header('Content-Disposition: inline; filename="'.$nom_fic.'"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	}
	else {
		header('Content-Disposition: attachment; filename="'.$nom_fic.'"');
		header('Pragma: no-cache');
	}

	// Initialisation du contenu du fichier:
	$fd='';


	// On fait la liste des devoirs de ce carnet de notes
	$appel_dev = mysql_query("select * from cn_devoirs where (id_racine='$id_racine') order by id_conteneur,date");
	$nb_dev  = mysql_num_rows($appel_dev);

	$ligne_entete="GEPI_INFOS;GEPI_LOGIN_ELEVE;NOM;PRENOM;CLASSE;MOYENNE;GEPI_COL_1ER_DEVOIR";
	//echo "$ligne_entete<br />\n";
	$fd.="$ligne_entete\n";

	unset($id_dev);
	$id_dev=array();

	unset($ligne_info_dev);
	$ligne_info_dev=array();

	$ligne_info_dev[]="GEPI_DEV_NOM_COURT;;;;;Nom court du devoir:";
	$ligne_info_dev[]="GEPI_DEV_COEF;;;;;Coefficient:";
	$ligne_info_dev[]="GEPI_DEV_DATE;;;;;Date:";
	$ligne_info_dev[]="INFOS;Login;Nom;Prénom;Classe;Moyennes:";

	// Pour mettre dans un tableur et améliorer... voir http://christianwtd.free.fr/index.php?rubrique=LecNotesClasse
	// On peut faire plus simple: La fonction MOYENNE de OpenOffice Calc tient compte des valeurs non numériques.

	$cpt=0;
	while($lig_dev=mysql_fetch_object($appel_dev)){

		$id_dev[$cpt]=$lig_dev->id;
		$nomc_dev[$cpt]=$lig_dev->nom_court;
		// Problème avec les 17.5 qui sont convertis en dates
		//$coef_dev[$cpt]=$lig_dev->coef;
		$coef_dev[$cpt]=strtr($lig_dev->coef,".",",");
		// Problème avec le format DATETIME
		//$date_dev[$cpt]=$lig_dev->date;
		$tmptab=explode(" ",$lig_dev->date);
		//$date_dev[$cpt]=$tmptab[0];
		$tmptab2=explode("-",$tmptab[0]);
		$date_dev[$cpt]=$tmptab2[2]."/".$tmptab2[1]."/".$tmptab2[0];

		$ligne_info_dev[0].=";".$nomc_dev[$cpt];
		$ligne_info_dev[1].=";".$coef_dev[$cpt];
		$ligne_info_dev[2].=";".$date_dev[$cpt];
		// A améliorer par la suite: calculer la moyenne de la classe:
		//$ligne_info_dev[3].="Moyenne_classe;";
		//$ligne_info_dev[3].=";";

		//$sql="SELECT SUM(note) AS somme,COUNT(note) AS nb FROM cn_devoirs WHERE (id='$id_dev[$cpt]' AND statut='')";
		$sql="SELECT SUM(note) AS somme,COUNT(note) AS nb FROM cn_notes_devoirs WHERE (id_devoir='$id_dev[$cpt]' AND statut='')";
		$res_moy=mysql_query($sql);
		if($res_moy){
			$lig_moy=mysql_fetch_array($res_moy);
			if($lig_moy[1]!=0){
				$moy=strtr(round(10*$lig_moy[0]/$lig_moy[1])/10,".",",");
				//echo "$lig_moy[0]/$lig_moy[1]=".$moy."<br />";
				$ligne_info_dev[3].=";".$moy;
			}
			else{
				$ligne_info_dev[3].=";";
			}
		}
		else{
			$ligne_info_dev[3].=";";
		}

		$cpt++;
	}

	for($i=0;$i<count($ligne_info_dev);$i++){
		//echo "$ligne_info_dev[$i]<br />\n";
		$fd.="$ligne_info_dev[$i]\n";
	}



	$i = 0;
	//$num_id=10;
	//$current_displayed_line = 0;

	$order_by="";

	// On commence par mettre la liste dans l'ordre souhaité
	if ($order_by != "classe") {
		$liste_eleves = $current_group["eleves"][$periode_num]["users"];
	} else {
		// Ici, on tri par classe
		// On va juste créer une liste des élèves pour chaque classe
		$tab_classes = array();
		foreach($current_group["classes"]["list"] as $classe_id) {
			$tab_classes[$classe_id] = array();
		}
		// On passe maintenant élève par élève et on les met dans la bonne liste selon leur classe
		foreach($current_group["eleves"][$periode_num]["list"] as $e_login) {
			$classe = $current_group["eleves"][$periode_num]["users"][$e_login]["classe"];
			$tab_classes[$classe][$e_login] = $current_group["eleves"][$periode_num]["users"][$e_login];
		}
		// On met tout ça à la suite
		$liste_eleves = array();
		foreach($current_group["classes"]["list"] as $classe_id) {
			$liste_eleves = array_merge($liste_eleves, $tab_classes[$classe_id]);
		}
	}



	$prev_classe = null;

	foreach ($liste_eleves as $eleve) {
		$eleve_login[$i] = $eleve["login"];
		$eleve_nom[$i] = $eleve["nom"];
		$eleve_prenom[$i] = $eleve["prenom"];
		$eleve_classe[$i] = $current_group["classes"]["classes"][$eleve["classe"]]["classe"];
		$eleve_id_classe[$i] = $current_group["classes"]["classes"][$eleve["classe"]]["id"];
		$somme_coef = 0;

		$k=0;

		$ligne_eleve="GEPI_LOGIN_ELEVE;".$eleve_login[$i].";".$eleve_nom[$i].";".$eleve_prenom[$i].";".$eleve_classe[$i];

		// A améliorer par la suite: Récupérer/calculer la moyenne de l'élève
		//$ligne_eleve.="Moyenne_calculée;";
		$ligne_eleve.=";";
		// Calculer la moyenne de l'élève est assez illusoire si on ne gère pas les boites et leurs coefficients...

		while ($k < $nb_dev) {
			//echo "<p>id_dev[$k]=$id_dev[$k]<br />\n";

			$note_query = mysql_query("SELECT * FROM cn_notes_devoirs WHERE (login='$eleve_login[$i]' AND id_devoir='$id_dev[$k]')");

			if(mysql_num_rows($note_query)==0){
				$eleve_note='';
				$eleve_statut='-';
			}
			else{
				$eleve_statut = @mysql_result($note_query, 0, "statut");
				$eleve_note = @mysql_result($note_query, 0, "note");
			}

			//$eleve_login_note = $eleve_login[$i]."_note";

			//echo "$eleve_login[$i]: $eleve_note - $eleve_statut<br />";

			// Problème avec les 17.5 qui sont convertis en dates -> 17/05/07
			$eleve_note=strtr($eleve_note,".",",");

			if($eleve_statut!=''){
				$eleve_note=$eleve_statut;
			}

			$ligne_eleve.=";".$eleve_note;

			$k++;
		}

		//echo "$ligne_eleve<br />\n";
		$fd.="$ligne_eleve\n";

		$i++;
	}

	// On renvoye le fichier vers le navigateur:
	echo $fd;
	die();
}
//elseif($type_export=="ODS"){
elseif(($type_export=="ODS")&&(getSettingValue("export_cn_ods")=='y')){

	// Génération d'un fichier tableur...
	// ... il faudra prévoir un nettoyage.
	// Il faudrait que l'option générer des ODS soit activable/désactivable par l'admin

	// On fait la liste des devoirs de ce carnet de notes
	$appel_dev = mysql_query("select * from cn_devoirs where (id_racine='$id_racine') order by id_conteneur,date");
	$nb_dev  = mysql_num_rows($appel_dev);

	unset($id_dev);
	$id_dev=array();

	$cpt=0;
	while($lig_dev=mysql_fetch_object($appel_dev)){

		$id_dev[$cpt]=$lig_dev->id;
		// Certains caractères comme le '°' que l'on met par exemple dans 'Devoir n°2' posent pb...
		$nomc_dev[$cpt]=ereg_replace("[^a-zA-Z0-9_. - ]","",remplace_accents($lig_dev->nom_court,'all'));

		// Problème avec les 17.5 qui sont convertis en dates
		//$coef_dev[$cpt]=$lig_dev->coef;
		$coef_dev[$cpt]=strtr($lig_dev->coef,".",",");

		// Problème avec le format DATETIME
		//$date_dev[$cpt]=$lig_dev->date;
		$tmptab=explode(" ",$lig_dev->date);
		$date_dev[$cpt]=$tmptab[0];
		// Pour le fichier ODS, on veut des dates au format aaaa-mm-jj
		$tmptab2=explode("-",$tmptab[0]);
		if(strlen($tmptab2[0])==4){$tmptab2[0]=substr($tmptab2[0],2,2);}
		$date_dev_fr[$cpt]=$tmptab2[2]."/".$tmptab2[1]."/".$tmptab2[0];


		// Moyenne
		$moy="";
		$sql="SELECT SUM(note) AS somme,COUNT(note) AS nb FROM cn_notes_devoirs WHERE (id_devoir='$id_dev[$cpt]' AND statut='')";
		$res_moy=mysql_query($sql);
		if($res_moy){
			$lig_moy=mysql_fetch_array($res_moy);
			if($lig_moy[1]!=0){
				$moy=strtr(round(10*$lig_moy[0]/$lig_moy[1])/10,".",",");
			}
		}
		$moy_dev[$cpt]=$moy;

		$cpt++;
	}


	// Génération du fichier ODS
	//$nom_fic.=".ods";

	$instant=getdate();
	$heure=$instant['hours'];
	$minute=$instant['minutes'];
	$seconde=$instant['seconds'];
	$mois=$instant['mon'];
	$jour=$instant['mday'];
	$annee=$instant['year'];
	$chaine_tmp="$annee-".sprintf("%02d",$mois)."-".sprintf("%02d",$jour)."-".sprintf("%02d",$heure)."-".sprintf("%02d",$minute)."-".sprintf("%02d",$seconde);

	$nom_fic.="_".$chaine_tmp.".ods";



	// Fichier content.xml
	//echo "\$tmp_fich=$tmp_fich<br />\n";
	$tmp_fich="content_".$_SESSION['login'];
	//echo "\$tmp_fich=$tmp_fich<br />\n";
	$tmp_fich.="_".strtr(microtime()," ","_");
	//echo "\$tmp_fich=$tmp_fich<br />\n";
	$tmp_fich.=".xml";
	//echo "\$tmp_fich=$tmp_fich<br />\n";

	$tmp_fich=$chemin_temp."/".$tmp_fich;

	$fichier_tmp_xml=fopen("$tmp_fich","w+");
	$ecriture=fwrite($fichier_tmp_xml,'<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" office:version="1.0"><office:scripts/><office:font-face-decls><style:font-face style:name="Courier 10 Pitch" svg:font-family="&apos;Courier 10 Pitch&apos;" style:font-pitch="fixed"/><style:font-face style:name="DejaVu Sans" svg:font-family="&apos;DejaVu Sans&apos;" style:font-family-generic="swiss" style:font-pitch="variable"/><style:font-face style:name="DejaVu Sans1" svg:font-family="&apos;DejaVu Sans&apos;" style:font-family-generic="system" style:font-pitch="variable"/></office:font-face-decls><office:automatic-styles><style:style style:name="co1" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="0.245cm"/></style:style><style:style style:name="co2" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="2.267cm"/></style:style><style:style style:name="co3" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="3.166cm"/></style:style><style:style style:name="co4" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="4.394cm"/></style:style><style:style style:name="ro1" style:family="table-row"><style:table-row-properties style:row-height="0.254cm" fo:break-before="auto" style:use-optimal-row-height="false"/></style:style><style:style style:name="ro2" style:family="table-row"><style:table-row-properties style:row-height="0.51cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ro3" style:family="table-row"><style:table-row-properties style:row-height="0.436cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ro4" style:family="table-row"><style:table-row-properties style:row-height="0.459cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ta1" style:family="table" style:master-page-name="Default"><style:table-properties table:display="true" style:writing-mode="lr-tb"/></style:style><number:date-style style:name="N37" number:automatic-order="true"><number:day number:style="long"/><number:text>/</number:text><number:month number:style="long"/><number:text>/</number:text><number:year/></number:date-style><style:style style:name="ce1" style:family="table-cell" style:parent-style-name="Default"><style:text-properties style:font-name="Courier 10 Pitch"/></style:style><style:style style:name="ce2" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border="0.002cm solid #000000"/><style:text-properties style:font-name="Courier 10 Pitch" fo:font-weight="bold"/></style:style><style:style style:name="ce3" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border="none"/><style:text-properties style:font-name="Courier 10 Pitch"/><style:map style:condition="is-true-formula(OR(ISBLANK([.C6]);ISBLANK([.$C6])))" style:apply-style-name="Default" style:base-cell-address="Notes.C6"/><style:map style:condition="is-true-formula(ISODD(ROW([.$C6])))" style:apply-style-name="Impair" style:base-cell-address="Notes.C6"/><style:map style:condition="is-true-formula(ISEVEN(ROW([.$C6])))" style:apply-style-name="Pair" style:base-cell-address="Notes.C6"/></style:style><style:style style:name="ce4" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:border="0.002cm solid #000000"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties style:font-name="Courier 10 Pitch" fo:font-weight="bold"/></style:style><style:style style:name="ce5" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:border="none"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties style:font-name="Courier 10 Pitch"/><style:map style:condition="is-true-formula(OR(ISBLANK([.C6]);ISBLANK([.$C6])))" style:apply-style-name="Default" style:base-cell-address="Notes.C6"/><style:map style:condition="is-true-formula(ISODD(ROW([.$C6])))" style:apply-style-name="Impair" style:base-cell-address="Notes.C6"/><style:map style:condition="is-true-formula(ISEVEN(ROW([.$C6])))" style:apply-style-name="Pair" style:base-cell-address="Notes.C6"/></style:style><style:style style:name="ce6" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:border="none"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties style:font-name="Courier 10 Pitch"/><style:map style:condition="is-true-formula(NOT(ISBLANK([.G2])))" style:apply-style-name="Entete" style:base-cell-address="Notes.G2"/></style:style><style:style style:name="ce7" style:family="table-cell" style:parent-style-name="Default" style:data-style-name="N37"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:border="none"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties style:font-name="Courier 10 Pitch"/><style:map style:condition="is-true-formula(NOT(ISBLANK([.G2])))" style:apply-style-name="Entete" style:base-cell-address="Notes.G2"/></style:style><style:style style:name="ce8" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:border="none"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties style:font-name="Courier 10 Pitch"/><style:map style:condition="is-true-formula(NOT(ISBLANK([.G2])))" style:apply-style-name="Entete" style:base-cell-address="Notes.G5"/></style:style>');

	//$ecriture=fwrite($fichier_tmp_xml,'</office:automatic-styles><office:body><office:spreadsheet><table:table table:name="Notes" table:style-name="ta1" table:print="false">');

	$ecriture=fwrite($fichier_tmp_xml,'<style:style style:name="P1" style:family="paragraph"><style:paragraph-properties fo:text-align="center"/></style:style>');

	$ecriture=fwrite($fichier_tmp_xml,'</office:automatic-styles><office:body><office:spreadsheet><table:table table:name="Notes" table:style-name="ta1" table:print="false">');

	$ecriture=fwrite($fichier_tmp_xml,'<office:forms form:automatic-focus="false" form:apply-design-mode="false"><form:form form:name="Standard" form:apply-filter="true" form:command-type="table" form:control-implementation="ooo:com.sun.star.form.component.Form" office:target-frame="" xlink:href=""><form:properties><form:property form:property-name="GroupBy" office:value-type="string" office:string-value=""/><form:property form:property-name="HavingClause" office:value-type="string" office:string-value=""/><form:property form:property-name="MaxRows" office:value-type="float" office:value="0"/><form:property form:property-name="UpdateCatalogName" office:value-type="string" office:string-value=""/><form:property form:property-name="UpdateSchemaName" office:value-type="string" office:string-value=""/><form:property form:property-name="UpdateTableName" office:value-type="string" office:string-value=""/></form:properties><form:button form:name="PushButton" form:control-implementation="ooo:com.sun.star.form.component.CommandButton" form:id="control1" form:label="Export_CSV" office:target-frame="" xlink:href="" form:image-data="" form:delay-for-repeat="PT0.50S" form:image-position="center"><form:properties><form:property form:property-name="DefaultControl" office:value-type="string" office:string-value="com.sun.star.form.control.CommandButton"/></form:properties><office:event-listeners><script:event-listener script:language="ooo:script" script:event-name="form:performaction" xlink:href="vnd.sun.star.script:Standard.Module1.Export_CSV?language=Basic&amp;location=document"/></office:event-listeners></form:button></form:form></office:forms>');


	// ===================

	$ecriture=fwrite($fichier_tmp_xml,'<table:table-column table:style-name="co1" table:visibility="collapse" table:default-cell-style-name="Default"/><table:table-column table:style-name="co2" table:visibility="collapse" table:default-cell-style-name="Default"/><table:table-column table:style-name="co3" table:default-cell-style-name="ce3"/><table:table-column table:style-name="co2" table:default-cell-style-name="ce3"/><table:table-column table:style-name="co2" table:default-cell-style-name="ce5"/><table:table-column table:style-name="co4" table:default-cell-style-name="ce5"/><table:table-column table:style-name="co2" table:number-columns-repeated="46" table:default-cell-style-name="ce5"/>');

	// ===================
	// Ligne 1

	$ecriture=fwrite($fichier_tmp_xml,'<table:table-row table:style-name="ro1" table:visibility="collapse">');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell office:value-type="string"><text:p>GEPI_INFOS</text:p></table:table-cell>');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell office:value-type="string"><text:p>GEPI_LOGIN_ELEVE</text:p></table:table-cell>');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="Default" office:value-type="string"><text:p>NOM</text:p></table:table-cell>');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="Default" office:value-type="string"><text:p>PRENOM</text:p></table:table-cell>');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="Default" office:value-type="string"><text:p>CLASSE</text:p></table:table-cell>');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="Default" office:value-type="string"><text:p>MOYENNE</text:p></table:table-cell>');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="Default" office:value-type="string"><text:p>GEPI_COL_1ER_DEVOIR</text:p></table:table-cell>');

	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="Default" table:number-columns-repeated="45"/>');
	$ecriture=fwrite($fichier_tmp_xml,'</table:table-row>');

	// ===================
	// Noms courts:

	$ecriture=fwrite($fichier_tmp_xml,'<table:table-row table:style-name="ro2">');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell office:value-type="string"><text:p>GEPI_DEV_NOM_COURT</text:p></table:table-cell>');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell/>');


	//$ecriture=fwrite($fichier_tmp_xml,'');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce1"><draw:control table:end-cell-address="Notes.E4" table:end-x="0.039cm" table:end-y="0.022cm" draw:z-index="0" draw:text-style-name="P1" svg:width="2.406cm" svg:height="0.622cm" svg:x="3.044cm" svg:y="0.414cm" draw:control="control1"/></table:table-cell>');

	//$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce1" table:number-columns-repeated="3"/>');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce1" table:number-columns-repeated="2"/>');

	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce4" office:value-type="string"><text:p>Nom court du devoir</text:p></table:table-cell>');


	//$ecriture=fwrite($fichier_tmp_xml,'');

	//for($i=0;$i<count($nomc_dev);$i++){
	for($i=0;$i<$nb_dev;$i++){
		$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce6" office:value-type="string"><text:p>'.$nomc_dev[$i].'</text:p></table:table-cell>');
	}

	// PB: J'ai prévu un maximum de 46 colonnes de devoirs...
	//$nb_vide=46-count($nomc_dev);
	$nb_vide=46-$nb_dev;
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce6" table:number-columns-repeated="'.$nb_vide.'"/>');
	$ecriture=fwrite($fichier_tmp_xml,'</table:table-row>');

	// ===================
	// Coefficients:

	$ecriture=fwrite($fichier_tmp_xml,'<table:table-row table:style-name="ro2">');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell office:value-type="string"><text:p>GEPI_DEV_COEF</text:p></table:table-cell>');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell/>');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce1" table:number-columns-repeated="3"/>');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce4" office:value-type="string"><text:p>Coefficient</text:p></table:table-cell>');

	//for($i=0;$i<count($coef_dev);$i++){
	for($i=0;$i<$nb_dev;$i++){
		$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce6" office:value-type="float" office:value="'.strtr($coef_dev[$i],",",".").'"><text:p>'.$coef_dev[$i].'</text:p></table:table-cell>');
	}

	// PB: J'ai prévu un maximum de 46 colonnes de devoirs...
	//$nb_vide=46-count($coef_dev);
	$nb_vide=46-$nb_dev;
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:number-columns-repeated="'.$nb_vide.'" table:style-name="ce6"/>');
	$ecriture=fwrite($fichier_tmp_xml,'</table:table-row>');

	// ===================
	// Dates:

	$ecriture=fwrite($fichier_tmp_xml,'<table:table-row table:style-name="ro2">');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell office:value-type="string"><text:p>GEPI_DEV_DATE</text:p></table:table-cell>');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell/>');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce1" table:number-columns-repeated="3"/>');
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce4" office:value-type="string"><text:p>Date</text:p></table:table-cell>');

	//for($i=0;$i<count($date_dev);$i++){
	for($i=0;$i<$nb_dev;$i++){
		$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce7" office:value-type="date" office:date-value="'.$date_dev[$i].'"><text:p>'.$date_dev_fr[$i].'</text:p></table:table-cell>');
	}

	// PB: J'ai prévu un maximum de 46 colonnes de devoirs...
	//$nb_vide=46-count($date_dev);
	$nb_vide=46-$nb_dev;
	$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce7" table:number-columns-repeated="'.$nb_vide.'"/>');
	$ecriture=fwrite($fichier_tmp_xml,'</table:table-row>');

	// ===================

	$ecriture=fwrite($fichier_tmp_xml,'<table:table-row table:style-name="ro2"><table:table-cell office:value-type="string"><text:p>INFOS</text:p></table:table-cell><table:table-cell office:value-type="string"><text:p>Login</text:p></table:table-cell><table:table-cell table:style-name="ce2" office:value-type="string"><text:p>Nom</text:p></table:table-cell><table:table-cell table:style-name="ce2" office:value-type="string"><text:p>Prenom</text:p></table:table-cell><table:table-cell table:style-name="ce4" office:value-type="string"><text:p>Classe</text:p></table:table-cell><table:table-cell table:style-name="ce4" office:value-type="string"><text:p>Moyennes</text:p></table:table-cell>');

	$alphabet="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$tabcol=array();
	for($i=0;$i<strlen($alphabet);$i++){
		$tabcol[$i]=substr($alphabet,$i,1);
	}
	for($i=strlen($alphabet);$i<2*strlen($alphabet);$i++){
		$tabcol[$i]="A".substr($alphabet,$i-strlen($alphabet),1);
	}

	/*
	for($i=0;$i<count($tabcol);$i++){
		echo $tabcol[$i]." - ";
	}
	*/

	// OpenOffice recalcule les valeurs lors de l'ouverture du document...
	$valeur_defaut=0;
	// On pourrait ne pas mettre de valeur...

	$num_col=6;

	//for($i=0;$i<count($moy_dev);$i++){
	for($i=0;$i<$nb_dev;$i++){
		//$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce8" table:formula="oooc:=IF([.'.$tabcol[$num_col].'3]=&quot;&quot;;&quot;&quot;;IF(ISERROR(AVERAGE([.'.$tabcol[$num_col].'6:.'.$tabcol[$num_col].'100]));&quot;&quot;;AVERAGE([.'.$tabcol[$num_col].'6:.'.$tabcol[$num_col].'100])))" office:value-type="float" office:value="'.$valeur_defaut.'"><text:p>'.$valeur_defaut.'</text:p></table:table-cell>');

		$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce8" table:formula="oooc:=IF([.'.$tabcol[$num_col].'3]=&quot;&quot;;&quot;&quot;;IF(ISERROR(AVERAGE([.'.$tabcol[$num_col].'6:.'.$tabcol[$num_col].'100]));&quot;&quot;;AVERAGE([.'.$tabcol[$num_col].'6:.'.$tabcol[$num_col].'100])))" office:value-type="float" office:value="'.strtr($moy_dev[$i],',','.').'"><text:p>'.$moy_dev[$i].'</text:p></table:table-cell>');
		//echo "$num_col - ";
		$num_col++;
	}

	for($i=$num_col;$i<count($tabcol);$i++){
		$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce8" table:formula="oooc:=IF([.'.$tabcol[$num_col].'3]=&quot;&quot;;&quot;&quot;;IF(ISERROR(AVERAGE([.'.$tabcol[$num_col].'6:.'.$tabcol[$num_col].'100]));&quot;&quot;;AVERAGE([.'.$tabcol[$num_col].'6:.'.$tabcol[$num_col].'100])))"><text:p/></table:table-cell>');
		//echo "$num_col - ";
		$num_col++;
	}

	$ecriture=fwrite($fichier_tmp_xml,'</table:table-row>');

	// ===================

	$i = 0;

	$order_by="";

	// On commence par mettre la liste dans l'ordre souhaité
	if($order_by != "classe"){
		$liste_eleves = $current_group["eleves"][$periode_num]["users"];
	}
	else{
		// Ici, on tri par classe
		// On va juste créer une liste des élèves pour chaque classe
		$tab_classes = array();
		foreach($current_group["classes"]["list"] as $classe_id) {
			$tab_classes[$classe_id] = array();
		}
		// On passe maintenant élève par élève et on les met dans la bonne liste selon leur classe
		foreach($current_group["eleves"][$periode_num]["list"] as $e_login) {
			$classe = $current_group["eleves"][$periode_num]["users"][$e_login]["classe"];
			$tab_classes[$classe][$e_login] = $current_group["eleves"][$periode_num]["users"][$e_login];
		}
		// On met tout ça à la suite
		$liste_eleves = array();
		foreach($current_group["classes"]["list"] as $classe_id) {
			$liste_eleves = array_merge($liste_eleves, $tab_classes[$classe_id]);
		}
	}



	$prev_classe = null;

	$num_lig=6;
	foreach ($liste_eleves as $eleve) {
		$eleve_login[$i]=$eleve["login"];
		$eleve_nom[$i]=$eleve["nom"];
		$eleve_prenom[$i]=$eleve["prenom"];
		$eleve_classe[$i]=$current_group["classes"]["classes"][$eleve["classe"]]["classe"];
		$eleve_id_classe[$i]=$current_group["classes"]["classes"][$eleve["classe"]]["id"];
		$somme_coef=0;

		$k=0;

		//$ligne_eleve="GEPI_LOGIN_ELEVE;".$eleve_login[$i].";".$eleve_nom[$i].";".$eleve_prenom[$i].";".$eleve_classe[$i];

		$ecriture=fwrite($fichier_tmp_xml,'<table:table-row table:style-name="ro2">');
		$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell office:value-type="string"><text:p>GEPI_LOGIN_ELEVE</text:p></table:table-cell>');

		$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell office:value-type="string"><text:p>'.$eleve_login[$i].'</text:p></table:table-cell><table:table-cell office:value-type="string"><text:p>'.caract_ooo($eleve_nom[$i]).'</text:p></table:table-cell><table:table-cell office:value-type="string"><text:p>'.caract_ooo($eleve_prenom[$i]).'</text:p></table:table-cell><table:table-cell office:value-type="string"><text:p>'.caract_ooo($eleve_classe[$i]).'</text:p></table:table-cell>');


		// OpenOffice recalcule les valeurs lors de l'ouverture du document...
		$valeur_defaut=0;

		$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:formula="oooc:=IF([.B'.$num_lig.']=&quot;&quot;;&quot;&quot;;IF(ISERROR(ROUND(SUMPRODUCT([.G'.$num_lig.':.AZ'.$num_lig.'];[.G$3:.AZ$3])/(SUM([.G$3:.AZ$3])-SUMIF([.G'.$num_lig.':.AZ'.$num_lig.'];&quot;abs&quot;;[.G$3:.AZ$3])-SUMIF([.G'.$num_lig.':.AZ'.$num_lig.'];&quot;disp&quot;;[.G$3:.AZ$3])-SUMIF([.G'.$num_lig.':.AZ'.$num_lig.'];&quot;-&quot;;[.G$3:.AZ$3])-SUMIF([.G'.$num_lig.':.AZ'.$num_lig.'];&quot;&quot;;[.G$3:.AZ$3]));1));&quot;-&quot;;ROUND(SUMPRODUCT([.G'.$num_lig.':.AZ'.$num_lig.'];[.G$3:.AZ$3])/(SUM([.G$3:.AZ$3])-SUMIF([.G'.$num_lig.':.AZ'.$num_lig.'];&quot;abs&quot;;[.G$3:.AZ$3])-SUMIF([.G'.$num_lig.':.AZ'.$num_lig.'];&quot;disp&quot;;[.G$3:.AZ$3])-SUMIF([.G'.$num_lig.':.AZ'.$num_lig.'];&quot;-&quot;;[.G$3:.AZ$3])-SUMIF([.G'.$num_lig.':.AZ'.$num_lig.'];&quot;&quot;;[.G$3:.AZ$3]));1)))" office:value-type="float" office:value="'.$valeur_defaut.'"><text:p>'.$valeur_defaut.'</text:p></table:table-cell>');

		$num_col=6;
		while ($k < $nb_dev) {
			$note_query=mysql_query("SELECT * FROM cn_notes_devoirs WHERE (login='$eleve_login[$i]' AND id_devoir='$id_dev[$k]')");

			if(mysql_num_rows($note_query)==0){
				$eleve_note='';
				$eleve_statut='-';
			}
			else{
				$eleve_statut = @mysql_result($note_query, 0, "statut");
				$eleve_note = @mysql_result($note_query, 0, "note");
			}

			//$eleve_login_note=$eleve_login[$i]."_note";

			if($eleve_statut!=''){
				$eleve_note=$eleve_statut;

				$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell office:value-type="string"><text:p>'.$eleve_note.'</text:p></table:table-cell>');
			}
			else{
				// Problème avec les 17.5 qui sont convertis en dates -> 17/05/07
				$eleve_note_virg=strtr($eleve_note,".",",");

				$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell office:value-type="float" office:value="'.$eleve_note.'"><text:p>'.$eleve_note_virg.'</text:p></table:table-cell>');
			}

			//$ligne_eleve.=";".$eleve_note;
			$k++;
			$num_col++;
		}

		$nb_vide=46-$num_col+6;
		$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:number-columns-repeated="'.$nb_vide.'"/>');

		$ecriture=fwrite($fichier_tmp_xml,'</table:table-row>');

		$i++;
		$num_lig++;
	}

	// ===================

	for($i=$num_lig;$i<=100;$i++){
		//'.$tabcol[$num_col].'

		$ecriture=fwrite($fichier_tmp_xml,'<table:table-row table:style-name="ro3"><table:table-cell/><table:table-cell/><table:table-cell table:number-columns-repeated="3"/>');

		$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:formula="oooc:=IF([.B'.$i.']=&quot;&quot;;&quot;&quot;;IF(ISERROR(ROUND(SUMPRODUCT([.G'.$i.':.AZ'.$i.'];[.G$3:.AZ$3])/(SUM([.G$3:.AZ$3])-SUMIF([.G'.$i.':.AZ'.$i.'];&quot;abs&quot;;[.G$3:.AZ$3])-SUMIF([.G'.$i.':.AZ'.$i.'];&quot;disp&quot;;[.G$3:.AZ$3])-SUMIF([.G'.$i.':.AZ'.$i.'];&quot;-&quot;;[.G$3:.AZ$3])-SUMIF([.G'.$i.':.AZ'.$i.'];&quot;&quot;;[.G$3:.AZ$3]));1));&quot;-&quot;;ROUND(SUMPRODUCT([.G'.$i.':.AZ'.$i.'];[.G$3:.AZ$3])/(SUM([.G$3:.AZ$3])-SUMIF([.G'.$i.':.AZ'.$i.'];&quot;abs&quot;;[.G$3:.AZ$3])-SUMIF([.G'.$i.':.AZ'.$i.'];&quot;disp&quot;;[.G$3:.AZ$3])-SUMIF([.G'.$i.':.AZ'.$i.'];&quot;-&quot;;[.G$3:.AZ$3])-SUMIF([.G'.$i.':.AZ'.$i.'];&quot;&quot;;[.G$3:.AZ$3]));1)))"><text:p/></table:table-cell>');

		$nb_max_dev=46;
		$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:number-columns-repeated="'.$nb_max_dev.'"/>');

		$ecriture=fwrite($fichier_tmp_xml,'</table:table-row>');
	}

	// ===================

	//$ecriture=fwrite($fichier_tmp_xml,'');

	$ecriture=fwrite($fichier_tmp_xml,'</table:table>');

	// ===================

	$ecriture=fwrite($fichier_tmp_xml,'<table:table table:name="Infos" table:style-name="ta1" table:print="false"><table:table-column table:style-name="co2" table:number-columns-repeated="6" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro3"><table:table-cell table:number-columns-repeated="6"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell/><table:table-cell office:value-type="string"><text:p>Feuille de calcul destinÃ©e Ã :</text:p></table:table-cell><table:table-cell table:number-columns-repeated="4"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell/><table:table-cell office:value-type="string"><text:p>. une saisie hors ligne des notes</text:p></table:table-cell><table:table-cell table:number-columns-repeated="4"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell/><table:table-cell office:value-type="string"><text:p>. une conservation des rÃ©sultats dans un tableur,...</text:p></table:table-cell><table:table-cell table:number-columns-repeated="4"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell table:number-columns-repeated="6"/></table:table-row><table:table-row table:style-name="ro4"><table:table-cell/><table:table-cell office:value-type="string"><text:p>L&apos;import des notes dans GEPI se fait ainsi:</text:p></table:table-cell><table:table-cell table:number-columns-repeated="3"/><table:table-cell office:value-type="string"><text:p><text:s/></text:p></table:table-cell></table:table-row><table:table-row table:style-name="ro3"><table:table-cell/><table:table-cell office:value-type="string"><text:p>. dans le feuillet &apos;Notes&apos;, cliquer sur le menu &apos;Fichier/Enregistrer sous&apos;</text:p></table:table-cell><table:table-cell table:number-columns-repeated="4"/></table:table-row><table:table-row table:style-name="ro4"><table:table-cell/><table:table-cell office:value-type="string"><text:p>. choisir &apos;Fichier CSV&apos; dans &apos;Type de fichiers&apos; et cocher &apos;Editer les paramÃ¨tres du filtre&apos;</text:p></table:table-cell><table:table-cell table:number-columns-repeated="4"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell/><table:table-cell office:value-type="string"><text:p>. cliquer sur &apos;Enregistrer&apos;</text:p></table:table-cell><table:table-cell table:number-columns-repeated="4"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell/><table:table-cell office:value-type="string"><text:p>. valider &apos;Oui&apos; l&apos;avertissement comme quoi seul le feuillet actif va Ãªtre enregistrÃ©</text:p></table:table-cell><table:table-cell table:number-columns-repeated="4"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell/><table:table-cell office:value-type="string"><text:p>. dans les paramÃ¨tres CSV, choisir le point-virgule comme sÃ©parateur de champs et supprimer le sÃ©parateur de texte</text:p></table:table-cell><table:table-cell table:number-columns-repeated="4"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell table:number-columns-repeated="6"/></table:table-row><table:table-row table:style-name="ro4"><table:table-cell/><table:table-cell office:value-type="string"><text:p>Quelques conseils et remarques pour permettre un import des notes dans GEPI:</text:p></table:table-cell><table:table-cell table:number-columns-repeated="4"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell/><table:table-cell office:value-type="string"><text:p>. Les champs importÃ©s sont repÃ©rÃ©s par le contenu de la colonne A et de la ligne 1 avec un prÃ©fixe GEPI_</text:p></table:table-cell><table:table-cell table:number-columns-repeated="4"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell/><table:table-cell office:value-type="string"><text:p>. Les colonnes moyennes ne sont pas prises en compte dans GEPI; elles sont recalculÃ©es d&apos;aprÃ¨s les notes importÃ©es.</text:p></table:table-cell><table:table-cell table:number-columns-repeated="4"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell/><table:table-cell office:value-type="string"><text:p>. Ne pas fusionner de cellules sans quoi le format CSV sera perturbÃ©.</text:p></table:table-cell><table:table-cell table:number-columns-repeated="4"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell/><table:table-cell office:value-type="string"><text:p>. Les formules permettent des calculs jusqu&apos;Ã  la ligne 100 et jusqu&apos;Ã  la colonne AZ.</text:p></table:table-cell><table:table-cell table:number-columns-repeated="4"/></table:table-row></table:table></office:spreadsheet></office:body></office:document-content>');



	$fermeture=fclose($fichier_tmp_xml);




	//set_time_limit(3000);
	require_once("../lib/ss_zip.class.php");

	$zip= new ss_zip('',6);
	//$zip->add_file('sxc/content.xml');
	$zip->add_file("$tmp_fich",'content.xml');

	// On n'ajoute pas les dossiers, ni les fichiers vides... ss_zip ne le supporte pas...
	// ... et OpenOffice a l'air de supporter l'absence de ces dossiers/fichiers.

	$zip->add_file($chemin_modele_ods.'/Basic/script-lc.xml', 'Basic/script-lc.xml');
	$zip->add_file($chemin_modele_ods.'/Basic/Standard/script-lb.xml', 'Basic/Standard/script-lb.xml');
	$zip->add_file($chemin_modele_ods.'/Basic/Standard/Module1.xml', 'Basic/Standard/Module1.xml');

	// On ne met pas ce fichier parce que sa longueur vide fait une blague pour ss_zip.
	//$zip->add_file($chemin_modele_ods.'/Configurations2/accelerator/current.xml', 'Configurations2/accelerator/current.xml');

	//$zip->add_file($chemin_modele_ods.'/Configurations2', 'Configurations2');
	//$zip->add_file($chemin_modele_ods.'/Configurations2/floater', 'Configurations2/floater');
	//$zip->add_file($chemin_modele_ods.'/Configurations2/images', 'Configurations2/images');
	//$zip->add_file($chemin_modele_ods.'/Configurations2/images/Bitmaps', 'Configurations2/images/Bitmaps');
	//$zip->add_file($chemin_modele_ods.'/Configurations2/accelerator', 'Configurations2/accelerator');
	//$zip->add_file($chemin_modele_ods.'/Configurations2/accelerator/current.xml', 'Configurations2/accelerator/current.xml');
	//$zip->add_file($chemin_modele_ods.'/Configurations2/statusbar', 'Configurations2/statusbar');
	//$zip->add_file($chemin_modele_ods.'/Configurations2/progressbar', 'Configurations2/progressbar');
	//$zip->add_file($chemin_modele_ods.'/Configurations2/toolbar', 'Configurations2/toolbar');
	//$zip->add_file($chemin_modele_ods.'/Configurations2/popupmenu', 'Configurations2/popupmenu');
	//$zip->add_file($chemin_modele_ods.'/Configurations2/menubar', 'Configurations2/menubar');
	//$zip->add_file($chemin_modele_ods.'/META-INF', 'META-INF');
	$zip->add_file($chemin_modele_ods.'/META-INF/manifest.xml', 'META-INF/manifest.xml');
	$zip->add_file($chemin_modele_ods.'/settings.xml', 'settings.xml');
	$zip->add_file($chemin_modele_ods.'/meta.xml', 'meta.xml');
	//$zip->add_file($chemin_modele_ods.'/Thumbnails', 'Thumbnails');
	$zip->add_file($chemin_modele_ods.'/Thumbnails/thumbnail.png', 'Thumbnails/thumbnail.png');
	$zip->add_file($chemin_modele_ods.'/mimetype', 'mimetype');
	$zip->add_file($chemin_modele_ods.'/styles.xml', 'styles.xml');

	$zip->save("$tmp_fich.zip");


	if(file_exists("$chemin_temp/$nom_fic")){unlink("$chemin_temp/$nom_fic");}
	//rename("$tmp_fich.zip","$chemin_ods/$chaine_tmp.$nom_fic");
	rename("$tmp_fich.zip","$chemin_temp/$nom_fic");

	// Suppression du fichier content...xml
	unlink($tmp_fich);

	//**************** EN-TETE *****************
	$titre_page = "Export des notes";
	require_once("../lib/header.inc");
	//**************** FIN EN-TETE *****************

	//$titre=htmlentities($current_group['description'])." (".$nom_periode.")";
	$titre=htmlentities($current_group['name'])." ".$current_group["classlist_string"]." (".$nom_periode.")";
	$titre.=" - EXPORT";

	// Mettre la ligne de liens de retour,...
    echo "<div class='norme'><p class='bold'>\n";
    echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a>|\n";
    echo "<a href='index.php?id_groupe=".$current_group["id"]."&amp;periode_num=$periode_num'> ".htmlentities($current_group['name'])." ".$current_group["classlist_string"]." (".$nom_periode.")"." </a>|\n";
	echo "</div>\n";


	echo "<h2>$titre</h2>\n";

	echo "<p>Télécharger: <a href='$chemin_temp/$nom_fic'>$nom_fic</a></p>\n";

	/*
	echo "filetype($chemin_ods/$nom_fic)=".filetype("$chemin_ods/$nom_fic")."<br />\n";

	$fp=fopen("$chemin_ods/$nom_fic","r");
	$ligne=fgets($fp, 4096);
	fclose($fp);

	echo "<pre>$ligne</pre>";
	*/

	// AJOUTER UN LIEN POUR FAIRE LE MENAGE... et permettre à l'admin de faire le ménage.
	echo "<p>Pour ne pas encombrer inutilement le serveur et par soucis de confidentialité, il est recommandé de supprimer le fichier du serveur après récupération du fichier ci-dessus.<br />\n";
	echo "<a href='".$_SERVER['PHP_SELF']."?nettoyage=$nom_fic'>Supprimer le fichier</a>.";
	//echo "<a href='".$_SERVER['PHP_SELF']."?nettoyage=".$nom_fic."_truc'>Supprimer le fichier 2</a>.";
	//echo "<a href='".$_SERVER['PHP_SELF']."?nettoyage=_truc".$nom_fic."'>Supprimer le fichier 3</a>.";
	echo "</p>\n";


	require("../lib/footer.inc.php");
	die();
}
else{
	echo "<p>Tiens, c'est bizarre! Ce cas ne devrait pas arriver.</p>\n";
}
require("../lib/footer.inc.php");
?>
