<?php
/*
 * $Id : $
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
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// INSERT INTO droits VALUES ('/mod_annees_anterieures/popup_annee_anterieure.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Consultation des données d années antérieures', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}



/*
$prof=isset($_POST['prof']) ? $_POST['prof'] : NULL;
$page=isset($_POST['page']) ? $_POST['page'] : NULL;
$enregistrer=isset($_POST['enregistrer']) ? $_POST['enregistrer'] : NULL;
*/

//$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;
$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;

//$logineleve=isset($_POST['logineleve']) ? $_POST['logineleve'] : NULL;
$logineleve=isset($_GET['logineleve']) ? $_GET['logineleve'] : NULL;
// Faire un filtrage sur $logineleve:
// - Un élève ne doit accéder qu'à ses infos personnelles
// - Un responsable ne doit accéder qu'aux infos des enfants dont il est (actuellement) responsable
// - Un professeur ne doit accéder, selon le mode choisi:
//        . qu'aux données des élèves qu'il a en groupe
//        . qu'aux données de tous les élèves dont il a les classes
//        . à toutes les données élèves
// - Un CPE, un compte scolarité... comme pour les profs.
// Il faut rendre ces choix paramétrables dans Droits d'accès.

// (pour le moment le checkAcess() ne permet que l'accès Administrateur)


$annee_scolaire=isset($_GET['annee_scolaire']) ? $_GET['annee_scolaire'] : NULL;
$num_periode=isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL;

// Mode: 'bull_simp' ou 'avis_conseil'
$mode=isset($_GET['mode']) ? $_GET['mode'] : NULL;
// Faire un filtrage sur ces valeurs.







// Si le module n'est pas activé...
if(getSettingValue('active_annees_anterieures')!="y"){
	// A DEGAGER
	// A VOIR: Comment enregistrer une tentative d'accès illicite?
	tentative_intrusion(1, "Tentative d'un ".$_SESSION["statut"]." (".$_SESSION["login"].") d'accéder au module Années antérieures qui n'est pas activé.");

	header("Location: ../logout.php?auto=1");
	die();
}


// Il faut arriver sur cette page avec un $logineleve passé en paramètre.

// Faire les filtrages selon le statut à ce niveau en tenant compte:
// - du fait que le statut est autorisé à accéder dans Droits d'accès;
// - du login élève fourni.

$acces="n";
if($_SESSION['statut']=="administrateur"){
	$acces="y";
}
elseif($_SESSION['statut']=="professeur"){
	// $AAProfTout
	// $AAProfPrinc
	// $AAProfClasses
	// $AAProfGroupes

	$AAProfTout=getSettingValue('AAProfTout');
	$AAProfPrinc=getSettingValue('AAProfPrinc');
	$AAProfClasses=getSettingValue('AAProfClasses');
	$AAProfGroupes=getSettingValue('AAProfGroupes');

	//echo "\$AAProfTout=$AAProfTout<br />";
	//echo "\$AAProfPrinc=$AAProfPrinc<br />";
	//echo "\$AAProfClasses=$AAProfClasses<br />";
	//echo "\$AAProfGroupes=$AAProfGroupes<br />";

	if($AAProfTout=="yes"){
		// Le professeur a accès aux données antérieures de tous les élèves
		$acces="y";
	}
	elseif($AAProfClasses=="yes"){
		// Le professeur a accès aux données antérieures des élèves des classes pour lesquelles il fournit un enseignement (sans nécessairement avoir tous les élèves de la classe)
		/*
		$sql="SELECT 1=1 FROM j_eleves_groupes jeg, j_groupes_classes jgc, j_groupes_professeurs jgp
						WHERE jeg.login='$logineleve' AND
								jeg.id_groupe=jgc.id_groupe AND
								jgc.id_groupe=jgp.id_groupe AND
								jgp.login='".$_SESSION['login']."';";
		*/
		$sql="SELECT 1=1 FROM j_eleves_classes jec, j_groupes_classes jgc, j_groupes_professeurs jgp
						WHERE jec.login='$logineleve' AND
								jec.id_classe=jgc.id_classe AND
								jgc.id_groupe=jgp.id_groupe AND
								jgp.login='".$_SESSION['login']."';";
		//echo "$sql<br />";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			$acces="y";
		}
	}
	elseif($AAProfGroupes=="yes"){
		// Le professeur a accès aux données antérieures des élèves des groupes auxquels il enseigne
		$sql="SELECT 1=1 FROM j_eleves_groupes jeg, j_groupes_professeurs jgp
						WHERE jeg.login='$logineleve' AND
								jeg.id_groupe=jgp.id_groupe AND
								jgp.login='".$_SESSION['login']."';";
		//echo "$sql<br />";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			$acces="y";
		}
	}
	elseif($AAProfPrinc=="yes"){
		// Le professeur a accès aux données antérieures des élèves dont il est Professeur Principal
		$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."' AND
														login='$logineleve';";
		//echo "$sql<br />";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			$acces="y";
		}
	}
}
elseif($_SESSION['statut']=="cpe"){
	// $AACpeTout
	// $AACpeResp

	$AACpeTout=getSettingValue('AACpeTout');
	$AACpeResp=getSettingValue('AACpeResp');

	if($AACpeTout=="yes"){
		// Le CPE a accès aux données antérieures de tous les élèves
		$acces="y";
	}
	elseif($AACpeResp=="yes"){
		$sql="SELECT 1=1 FROM j_eleves_cpe WHERE cpe_login='".$_SESSION['login']."' AND
													e_login='$logineleve'";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			$acces="y";
		}
	}
}
elseif($_SESSION['statut']=="scolarite"){
	// $AAScolTout
	// $AAScolResp

	$AAScolTout=getSettingValue('AAScolTout');
	$AAScolResp=getSettingValue('AAScolResp');

	if($AAScolTout=="yes"){
		// Les comptes Scolarité ont accès aux données antérieures de tous les élèves
		$acces="y";
	}
	elseif($AAScolResp=="yes"){
		$sql="SELECT 1=1 FROM j_eleves_classes jec, j_scol_classes jsc
						WHERE jec.login='$logineleve' AND
								jec.id_classe=jsc.id_classe AND
								jsc.login='".$_SESSION['login']."';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			$acces="y";
		}
	}
}
elseif($_SESSION['statut']=="responsable"){
	$AAResponsable=getSettingValue('AAResponsable');

	if($AAResponsable=="yes"){
		// Est-ce que le $logineleve est bien celui d'un élève dont le responsable est responsable?
		$sql="SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e WHERE rp.login='".$_SESSION['login']."' AND
																			rp.pers_id=r.pers_id AND
																			r.ele_id=e.ele_id AND
																			e.login='$logineleve'";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==1){
			$acces="y";
		}
	}
}
elseif($_SESSION['statut']=="eleve"){
	$AAEleve=getSettingValue('AAEleve');

	if($AAEleve=="yes"){
		$logineleve=$_SESSION['login'];
		$acces="y";
	}
}
elseif($_SESSION['statut']=="autre"){
	//$sql="SELECT 1=1 FROM droits_speciaux ds, droits_utilisateurs du, droits_statut dst WHERE dst.id=ds.id_statut AND du.id_statut=dst.id AND du.login_user='".$_SESSION['login']."' AND ds.nom_fichier='/voir_anna' AND ds.autorisation='V';";
	//$sql="SELECT 1=1 FROM droits_speciaux ds WHERE ds.id_statut='".$_SESSION['statut_special_id']."' AND ds.nom_fichier='/voir_anna' AND ds.autorisation='V';";

	$sql="SELECT 1=1 FROM droits_speciaux ds WHERE ds.id_statut='".$_SESSION['statut_special_id']."' AND ds.nom_fichier='/mod_annees_anterieures/popup_annee_anterieure.php' AND ds.autorisation='V';";
	$res_acces=mysql_query($sql);

	if(mysql_num_rows($res_acces)>0){
		$acces="y";
	}
}

if($acces!="y"){
	// A DEGAGER
	// A VOIR: Comment enregistrer une tentative d'accès illicite?
	$ajout_info="";
	if(isset($logineleve)) {$ajout_info=" de $logineleve";}
	tentative_intrusion(1, "Tentative illicite d'un ".$_SESSION["statut"]." (".$_SESSION["login"].") d'accéder à des données d'Années antérieures".$ajout_info.".");
	//echo "DEBUG: Tentative illicite d'un ".$_SESSION["statut"]." (".$_SESSION["login"].") d'accéder à des données d'Années antérieures".$ajout_info.".";

	header("Location: ../logout.php?auto=1");
	die();
}






$msg="";

$style_specifique="mod_annees_anterieures/annees_anterieures";

// ============================================================================
// ============================================================================
// On va écrire la section HEAD
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
<meta HTTP-EQUIV="Content-Type" content="text/html; charset=utf-8" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
<META HTTP-EQUIV="Expires" CONTENT="0" />
<meta HTTP-EQUIV="refresh" content="<?php echo getSettingValue("sessionMaxLength")*60; ?>; URL=<?php echo($gepiPath); ?>/logout.php?auto=3&amp;debut_session=<?php echo urlencode($_SESSION['start']);?>&amp;sessionid=<?php echo session_id();?>" />
<title><?php echo getSettingValue("gepiSchoolName"); ?> : base de données élèves</title>
<?php $style = getSettingValue("gepi_stylesheet");
if (empty($style)) $style = "style";
?>
<link rel="stylesheet" type="text/css" href="<?php echo($gepiPath); ?>/<?php echo $style;?>.css" />

<!-- Gestion de l'expiration des session - Patrick Duthilleul -->
<script type="text/javascript" language="JavaScript">
<!--
var debut=new Date()
function show_message_deconnexion(){
  var seconds_before_alert = 180;
  var seconds_int_betweenn_2_msg = 30;

  var digital=new Date()
  var seconds=(digital-debut)/1000
  if (seconds><?php echo getSettingValue("sessionMaxLength")*60; ?> - seconds_before_alert) {
    var seconds_reste = Math.floor(<?php echo (getSettingValue("sessionMaxLength"))*60; ?> - seconds);
    now=new Date()
    var hrs=now.getHours();
    var mins=now.getMinutes();
    var secs=now.getSeconds();

    var heure = hrs + " H " + mins + "' " + secs + "'' ";
    alert("A "+ heure + ", il vous reste moins de 3 minutes avant d'être déconnecté ! \nPour éviter cela, rechargez cette page en ayant pris soin d'enregistrer votre travail !");
  }
  setTimeout("show_message_deconnexion()",seconds_int_betweenn_2_msg*1000)
}
//-->
</script>

<?php if (isset($niveau_arbo) and ($niveau_arbo == 0)) {
   echo "<script src=\"lib/functions.js\" type=\"text/javascript\" language=\"javascript\"></script>\n";
   echo "<LINK REL=\"SHORTCUT ICON\" href=\"./favicon.ico\" />\n";
} else if (isset($niveau_arbo) and ($niveau_arbo == 2)) {
   echo "<script src=\"../../lib/functions.js\" type=\"text/javascript\" language=\"javascript\"></script>\n";
   echo "<LINK REL=\"SHORTCUT ICON\" href=\"../../favicon.ico\" />\n";
} else {
   echo "<script src=\"../lib/functions.js\" type=\"text/javascript\" language=\"javascript\"></script>\n";
   echo "<LINK REL=\"SHORTCUT ICON\" href=\"../favicon.ico\" />\n";
}
// Couleur de fond des pages
if (!isset($titre_page)) $bgcouleur = "bgcolor= \"#FFFFFF\""; else $bgcouleur = "";


if(isset($style_specifique)){
	// Il faudrait filtrer le contenu de la variable...
	// ne doit contenir que certains types de caractères et se terminer par .css
	// Non... on ajoute le ".css" automatiquement et on exclus les "." qui pourrait permettre des ".." pour remonter dans l'arborescence
	if(mb_strlen(my_ereg_replace("[A-Za-z0-9_/]","",$style_specifique))==0){
		// Styles spécifiques à une page:
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/$style_specifique.css' />\n";
	}
}

if(isset($javascript_specifique)){
	// Il faudrait filtrer le contenu de la variable...
	// On ajoute le ".js" automatiquement et on exclus les "." qui pourrait permettre des ".." pour remonter dans l'arborescence
	if(mb_strlen(my_ereg_replace("[A-Za-z0-9_/]","",$javascript_specifique))==0){
		// Javascript spécifique à une page:
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/$javascript_specifique.js' />\n";
	}
}



if(isset($style_screen_ajout)){
	// Styles paramétrables depuis l'interface:
	if($style_screen_ajout=='y'){
		// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
		// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
	}
}


//===================================
// Pour aérer les infobulles si jamais Javascript n'est pas actif.
// Sinon, avec le position:absolute, les div se superposent.
$posDiv_infobulle=0;
// $posDiv_infobulle permet de fixer la position horizontale initiale du Div.

$tabdiv_infobulle=array();
$tabid_infobulle=array();

// Choix de l'unité pour les dimensions des DIV: em, px,...
$unite_div_infobulle="em";
// Pour l'overflow dans les DIV d'aide, il vaut mieux laisser 'em'.

echo "<script type='text/javascript' src='$gepiPath/lib/brainjar_drag.js'></script>\n";
echo "<script type='text/javascript' src='$gepiPath/lib/position.js'></script>\n";

// Variable passée à 'ok' en fin de page via le /lib/footer.inc.php
echo "<script type='text/javascript'>
	temporisation_chargement='n';
</script>\n";

//===================================

echo "</head>\n";

//**************** EN-TETE *****************
//$titre_page = "Consultation des données antérieures";
//**************** FIN EN-TETE *****************

//echo "<div class='norme'><p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour</a>\n";

echo "<!-- ************************* -->
<!-- Début du corps de la page -->
<!-- ************************* -->\n";

echo "<body onLoad='show_message_deconnexion()'>\n";
echo "<div id='container'>\n";

// ============================================================================
// ============================================================================

// - Choix de l'année/période et du mode 'bull_simp' ou 'avis_conseil'
// - Puis affichage correspondant

require("fonctions_annees_anterieures.inc.php");

//echo $_SERVER['HTTP_USER_AGENT']."<br />\n";
if(my_eregi("gecko",$_SERVER['HTTP_USER_AGENT'])){
	//echo "gecko=true<br />";
	$gecko=true;
}
else{
	//echo "gecko=false<br />";
	$gecko=false;
}

//if(!isset($logineleve)){
if((!isset($logineleve))||(($mode!='bull_simp')&&($mode!='avis_conseil'))) {
	echo "<h2 align='center'>Choix des informations antérieures</h2>\n";
	tab_choix_anterieure($logineleve);
}
else{
	//echo "<div style='float:right; width:3em; text-align:center;'><a href='".$_SERVER['PHP_SELF']."?logineleve=$logineleve'>Retour</a></div>\n";
	echo "<div style='float:left; width:5em; text-align:center;'><a href='".$_SERVER['PHP_SELF']."?logineleve=$logineleve'><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour</a></div>\n";

	if($mode=='bull_simp'){
		echo "<h2 align='center'>Bulletin simplifié d'une année antérieure</h2>\n";
		if(!isset($annee_scolaire)){
			echo "<p><b>ERREUR:</b> L'année scolaire antérieure ne semble pas avoir été choisie.</p>\n";
		}
		elseif(!isset($num_periode)){
			echo "<p><b>ERREUR:</b> La période ne semble pas avoir été choisie.</p>\n";
		}
		elseif(!isset($id_classe)){
			echo "<p><b>ERREUR:</b> L'identifiant de la classe actuelle de l'élève ne semble pas avoir été fourni.</p>\n";
		}
		else{
			/*
			if(!isset($num_periode)){
				$num_periode=1;
			}
			// Il n'est pas certain que GEPI ait été mis en place dès la période 1 cette année là.
			*/

			bull_simp_annee_anterieure($logineleve,$id_classe,$annee_scolaire,$num_periode);
		}
	}
	elseif($mode=='avis_conseil'){
		echo "<h2 align='center'>Avis des Conseils de classe d'une année antérieure</h2>\n";
		if(!isset($annee_scolaire)){
			echo "<p><b>ERREUR:</b> L'année scolaire antérieure ne semble pas avoir été choisie.</p>\n";
		}
		else{
			avis_conseils_de_classes_annee_anterieure($logineleve,$annee_scolaire);
		}
	}
}
echo "<br />\n";
require("../lib/footer.inc.php");
?>
