<?php
/**
 * Ajouter, modifier une évaluation cumule
 * 
 * 
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 *
 * @package Carnet_de_notes
 * @subpackage Evaluation_cumule
 * @license GNU/GPL
 * @see add_token_field()
 * @see Calendrier::get_strPopup()
 * @see checkAccess()
 * @see get_group()
 * @see getSettingValue()
 * @see Session::security_check()
 * @see traitement_magic_quotes()
 * @see Verif_prof_cahier_notes()
 */

/* This file is part of GEPI.
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

/**
 * Fichiers d'initialisation
 */
// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session=$session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
	die("Le module n'est pas activé.");
}
/**
 * Calcul des arrondis
 */
require('cc_lib.php');

$id_racine=isset($_POST["id_racine"]) ? $_POST["id_racine"] : (isset($_GET["id_racine"]) ? $_GET["id_racine"] : NULL);

if(!isset($id_racine)) {
	$mess="Racine non précisée pour $nom_cc.<br />";
	header("Location: index.php?msg=$mess");
	die();
}

// On teste si le carnet de notes appartient bien à la personne connectée
if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
    header("Location: index.php?msg=$mess");
    die();
}

$appel_cahier_notes=mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$id_groupe=old_mysql_result($appel_cahier_notes, 0, 'id_groupe');
$current_group=get_group($id_groupe);
$periode_num=old_mysql_result($appel_cahier_notes, 0, 'periode');

/**
 * Gestion des périodes
 */
include "../lib/periodes.inc.php";

$id_dev=isset($_POST["id_dev"]) ? $_POST["id_dev"] : (isset($_GET["id_dev"]) ? $_GET["id_dev"] : NULL);
if(!isset($id_dev)) {
	$mess="$nom_cc non précisé.<br />";
	header("Location: index_cc.php?id_racine=$id_racine&msg=$mess");
	die();
}

$sql="SELECT * FROM cc_dev WHERE id='$id_dev' AND id_groupe='$id_groupe';";
$query=mysqli_query($GLOBALS["mysqli"], $sql);
if($query) {
	$id_cn_dev=old_mysql_result($query, 0, 'id_cn_dev');
	$nom_court_dev=stripslashes(old_mysql_result($query, 0, 'nom_court'));
	$nom_complet_dev=stripslashes(old_mysql_result($query, 0, 'nom_complet'));
	$description_dev=stripslashes(old_mysql_result($query, 0, 'description'));
}
else {
	header("Location: index.php?msg=".rawurlencode("Le numéro de devoir n est pas associé à ce groupe."));
	die();
}

$id_eval=isset($_POST["id_eval"]) ? $_POST["id_eval"] : (isset($_GET["id_eval"]) ? $_GET["id_eval"] : NULL);
if(isset($id_eval))  {
	$sql="SELECT * FROM cc_eval WHERE id='$id_eval';";
	//echo "$sql<br />";
	$query=mysqli_query($GLOBALS["mysqli"], $sql);
	if($query) {
		// Vérifier que l'évaluation est bien associée au CC.
		$sql="SELECT * FROM cc_eval WHERE id='$id_eval' AND id_dev='$id_dev';";
		//echo "$sql<br />";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			$mess="L'évaluation n°$id_eval n'est pas associée au $nom_cc n°$id_dev.<br />";
			header("Location: index_cc.php?id_racine=$id_racine&msg=$mess");
			die();
		}

		//$id_cn_dev=old_mysql_result($query, 0, 'id_cn_dev');
		$nom_court=stripslashes(old_mysql_result($query, 0, 'nom_court'));
		$nom_complet=stripslashes(old_mysql_result($query, 0, 'nom_complet'));
		$description=nettoyage_retours_ligne_surnumeraires(stripslashes(old_mysql_result($query, 0, 'description')));

		$display_date=old_mysql_result($query, 0, 'date');
		$vision_famille =old_mysql_result($query, 0, 'vision_famille');
		$note_sur=old_mysql_result($query, 0, 'note_sur');

		$display_date=formate_date($display_date);
		$vision_famille=formate_date($vision_famille);
	}
	else {
		header("Location: index.php?msg=".rawurlencode("L évaluation n°$id_eval n'existe pas."));
		die();
	}
}
else {

	$nom_court="Ev";
	$nom_complet="Evaluation n°";
	$description="";
	$display_date=strftime('%d/%m/%Y');
	$vision_famille =strftime('%d/%m/%Y');
	$note_sur=5;
}

$matiere_nom=$current_group["matiere"]["nom_complet"];
$matiere_nom_court=$current_group["matiere"]["matiere"];
$nom_classe=$current_group["classlist_string"];

// enregistrement des données
if (isset($_POST['ok'])) {
	check_token();

	$nom_court=traitement_magic_quotes($_POST['nom_court']);
	$nom_complet=traitement_magic_quotes($_POST['nom_complet']);
	$description=traitement_magic_quotes($_POST['description']);

	$note_sur=preg_replace('/[^0-9]/','',$_POST['note_sur']);

	if($nom_court=='') {
		$msg="Le nom_court de l'évaluation ne peut pas être vide.";
		header("Location: index_cc.php?id_racine=$id_racine&msg=$msg");
		die();
	}
	elseif($note_sur=='') {
		$msg="La valeur de note_sur n'est pas valide.";
		header("Location: index_cc.php?id_racine=$id_racine&msg=$msg");
		die();
	}
	else {
		if(!isset($id_eval)) {
			$sql="INSERT INTO cc_eval SET id_dev='$id_dev';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$insert) {
				$msg="Erreur lors de la création de l'évaluation associée au $nom_cc n°$id_dev.";
				header("Location: index_cc.php?id_racine=$id_racine&msg=$msg");
				die();
			}
			else {
				$id_eval=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
			}
		}

		if ($_POST['display_date']) {
			if (my_ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $_POST['display_date'])) {
				$annee=mb_substr($_POST['display_date'],6,4);
				$mois=mb_substr($_POST['display_date'],3,2);
				$jour=mb_substr($_POST['display_date'],0,2);
			} else {
				$annee=strftime("%Y");
				$mois=strftime("%m");
				$jour=strftime("%d");
			}
		} else {
			$annee=strftime("%Y");
			$mois=strftime("%m");
			$jour=strftime("%d");
		}
		$date=$annee."-".$mois."-".$jour." 00:00:00";


		if ($_POST['vision_famille ']) {
			if (my_ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $_POST['vision_famille '])) {
				$annee=mb_substr($_POST['vision_famille '],6,4);
				$mois=mb_substr($_POST['vision_famille '],3,2);
				$jour=mb_substr($_POST['vision_famille '],0,2);
			} else {
				$annee=strftime("%Y");
				$mois=strftime("%m");
				$jour=strftime("%d");
			}
		} else {
			$annee=strftime("%Y");
			$mois=strftime("%m");
			$jour=strftime("%d");
		}
		$vision_famille  = $annee."-".$mois."-".$jour." 00:00:00";

		$sql="UPDATE cc_eval SET nom_court='$nom_court', nom_complet='$nom_complet', description='$description', note_sur='$note_sur', date='".$date."', vision_famille ='".$vision_famille ."' WHERE id='$id_eval';";
		$update=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$update) {
			$msg="Erreur lors de la création ou mise à jour de l'évaluation associée au ".stripslashes($nom_cc)." n°$id_dev. ".stripslashes($sql);
		}
		else {
			$msg="Création ou mise à jour de l'évaluation associée au $nom_cc n°$id_dev effectuée.";
		}
		header("Location: index_cc.php?id_racine=$id_racine&msg=$msg");
		die();
	}

}

/**
 * Configuration du calendrier
 */
 /*
include("../lib/calendrier/calendrier.class.php");
$cal = new Calendrier("formulaire", "display_date");
*/
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

//**************** EN-TETE *****************
$titre_page="Carnet de notes - Ajout/modification d'un $nom_cc";
/**
 * Entête de la page
 */
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
insere_lien_calendrier_crob("right");

echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
echo add_token_field();

echo "<div class='norme'>\n";
echo "<p class='bold'>\n";
echo "<a href='index_cc.php?id_racine=$id_racine'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "</p>\n";
echo "</div>\n";

echo "<h2 class='gepi'>Configuration d'une évaluation de $nom_court_dev (<i>$nom_complet_dev</i>)&nbsp;:</h2>\n";

$aff_nom_court="y";
$aff_nom_complet="y";
$aff_description="y";
$aff_date="y";
$aff_note_sur="y";

echo "<div align='center'>\n";
echo "<table class='boireaus' border='1' summary='Parametres de l évaluation'>\n";

if($aff_nom_court=='y'){
	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Nom court&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='text' name='nom_court' id='nom_court' size='40' value=\"".$nom_court."\" autocomplete='off' onfocus=\"javascript:this.select()\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}
else{
	echo "<tr style='display:none;'>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Nom court&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='hidden' name='nom_court' id='nom_court' size='40' value=\"".$nom_court."\" onfocus=\"javascript:this.select()\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}


if($aff_nom_complet=='y'){
	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Nom complet&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='text' name='nom_complet' id='nom_complet' size='40' value=\"".$nom_complet."\" autocomplete='off' onfocus=\"javascript:this.select()\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}
else{
	echo "<tr style='display:none;'>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Nom complet&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='hidden' name='nom_complet' id='nom_complet' size='40' value=\"".$nom_complet."\" onfocus=\"javascript:this.select()\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}

if($aff_description=='y'){
	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Description&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<textarea name='description' id='description' rows='2' cols='40' >".$description."</textarea>\n";
	echo "</td>\n";
	echo "</tr>\n";
}
else{
	echo "<tr style='display:none;'>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Description&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='hidden' name='description' id='description' value=\"$description\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}

if($aff_note_sur=='y'){
	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Note sur&nbsp;:</td>\n";
	echo "<td>\n";
	echo "<input type='text' name='note_sur' id='note_sur' size='4' onfocus=\"javascript:this.select()\" value=\"".$note_sur."\" onkeydown=\"clavier_2(this.id,event,1,100);\" autocomplete=\"off\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}
else{
	echo "<tr style='display:none;'>\n";
	echo "<td>Note sur:</td>\n";
	echo "<td>\n";
	echo "<input type='hidden' name='note_sur' id='note_sur' size='4' value=\"".$coef."\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}

if($aff_date=='y'){
	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Date:</td>\n";
	echo "<td>\n";
	echo "<input type='text' name='display_date' id='display_date' size='10' value=\"".$display_date."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
	/*
	echo "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"";
	echo "><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
	*/
	echo img_calendrier_js("display_date", "img_bouton_display_date");
	echo "</td>\n";
	echo "</tr>\n";
}
else{
	echo "<tr style='display:none;'>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Date:</td>\n";
	echo "<td>\n";
	echo "<input type='hidden' name='display_date' id='display_date' size='10' autocomplete='off' onfocus=\"javascript:this.select()\" value=\"".$display_date."\" onKeyDown=\"clavier_date(this.id,event);\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
}


	echo "<tr>\n";
	echo "<td style='background-color: #aae6aa; font-weight: bold;'>Date de visibilité (si le droit est ouvert) :</td>\n";
	echo "<td>\n";
	echo "<input type='text' name='vision_famille' id='vision_famille' size='10' value=\"".$vision_famille ."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
	echo img_calendrier_js("vision_famille ", "img_bouton_vision_famille ");
	echo "</td>\n";
	echo "</tr>\n";


echo "</table>\n";
echo "</div>\n";
echo "<input type='hidden' name='is_posted' value='1' />\n";
echo "<input type='hidden' name='id_racine' value='$id_racine' />\n";
echo "<input type='hidden' name='id_dev' value='$id_dev' />\n";

if(isset($id_eval)) {
	echo "<input type='hidden' name='id_eval' value='$id_eval' />\n";
}

if($aff_nom_court=='y'){
	echo "<script type='text/javascript'>
	document.formulaire.nom_court.focus();
</script>\n";
}

echo "<p style='text-align:center;'><input type=\"submit\" name='ok' value=\"Enregistrer\" style=\"font-variant: small-caps;\" /></p>\n";

echo "</form>\n";


$sql="SELECT * FROM cc_eval WHERE id_dev='$id_dev'";
if(isset($id_eval)) {
	$sql.=" AND id!='$id_eval'";
}
$sql.=" ORDER BY date;";
//echo "$sql<br />";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	echo "<div align='center'>
<br />
<p>D'autres évaluations sont saisies dans ce(tte) $nom_cc&nbsp;:</p>
<table class='boireaus boireaus_alt'>
	<tr>
		<th>Nom_court</th>
		<th>Nom_complet</th>
		<th>Description</th>
		<th>Note_sur</th>
		<th>Date</th>
		<th>Date de visibilité</th>
		<th>Copie</th>
	</tr>";
	while($lig=mysqli_fetch_object($res)) {
		echo "
	<tr>
		<td id='cc_".$lig->id."_nom_court' onclick=\"document.getElementById('nom_court').value=this.innerHTML\">".$lig->nom_court."</td>
		<td id='cc_".$lig->id."_nom_complet' onclick=\"document.getElementById('nom_complet').value=this.innerHTML\">".$lig->nom_complet."</td>
		<td id='cc_".$lig->id."_description' onclick=\"document.getElementById('description').value=this.innerHTML\">".nl2br($lig->description)."</td>
		<td id='cc_".$lig->id."_note_sur' onclick=\"document.getElementById('note_sur').value=this.innerHTML\">".$lig->note_sur."</td>
		<td id='cc_".$lig->id."_display_date' onclick=\"document.getElementById('display_date').value=this.innerHTML\">".formate_date($lig->date)."</td>
		<td id='cc_".$lig->id."_vision_famille' onclick=\"document.getElementById('vision_famille').value=this.innerHTML\">".formate_date($lig->vision_famille)."</td>
		<td id='cc_".$lig->id."_nom_court' onclick=\"
				document.getElementById('nom_court').value=document.getElementById('cc_".$lig->id."_nom_court').innerHTML;
				document.getElementById('nom_complet').value=document.getElementById('cc_".$lig->id."_nom_complet').innerHTML;
				document.getElementById('description').value=document.getElementById('cc_".$lig->id."_description').innerHTML;
				document.getElementById('note_sur').value=document.getElementById('cc_".$lig->id."_note_sur').innerHTML;
				document.getElementById('display_date').value=document.getElementById('cc_".$lig->id."_display_date').innerHTML;
				document.getElementById('vision_famille').value=document.getElementById('cc_".$lig->id."_vision_famille').innerHTML;
				\"><img src='../images/icons/copy-16.png' class='icone16' alt='Copier' /></td>
	</tr>";
	}
	echo "
</table>
<p>En cliquant sur une cellule du tableau ci-dessus, vous copiez les paramètres vers l'évaluation courante.</p>
</div>";
}

echo "<br />\n";
/**
 * Pied de page
 */
require("../lib/footer.inc.php");
?>
