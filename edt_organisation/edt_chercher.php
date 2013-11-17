<?php
/**
 *
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

/* Fichier qui permet de faire des recherches dans l'EdT*/

// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die(ASK_AUTHORIZATION_TO_ADMIN);
}

// Sécurité, on vérifie le paramétrage de cette fonctionnalité
$aff_cherche_salle = GetSettingEdt("aff_cherche_salle");
	if ($aff_cherche_salle == "tous") {
		$aff_ok = "oui";
	}
	else if ($aff_cherche_salle == "admin") {
		if ($_SESSION["statut"] != "administrateur") {
			die();
		}
	}
	else {
	$aff_ok = "non";
	die();
	}


$auto_aff_1 ="";
$auto_aff_2 ="";
$auto_aff_21 ="";
$auto_aff_22 ="";
$cherch_salle=isset($_GET["cherch_salle"]) ? $_GET["cherch_salle"] : (isset($_POST["cherch_salle"]) ? $_POST["cherch_salle"] : NULL);
$salle_libre = isset($_GET["salleslibres"]) ? $_GET["salleslibres"] : (isset($_POST["salleslibres"]) ? $_POST["salleslibres"] : NULL);
$ch_heure = isset($_GET["ch_heure"]) ? $_GET["ch_heure"] : (isset($_POST["ch_heure"]) ? $_POST["ch_heure"] : NULL);
$ch_jour_semaine = isset($_GET["ch_jour_semaine"]) ? $_GET["ch_jour_semaine"] : (isset($_POST["ch_jour_semaine"]) ? $_POST["ch_jour_semaine"] : NULL);
if ($salle_libre == "ok") {
	$auto_aff_1 = 1;
}

// On insère l'entête de Gepi
$ua = getenv("HTTP_USER_AGENT");
if (strstr($ua, "MSIE 6.0")) {
	$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_ie6_param";
}
else if (strstr($ua, "MSIE 7")) {
	$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_ie7_param";
}
$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_param";
require_once("../lib/header.inc.php");


if (strstr($ua, "MSIE 6.0")) {
	echo "<div class=\"cadreInformation\">Votre navigateur (Internet Explorer 6) est obsolète et se comporte mal vis à vis de l'affichage des emplois du temps. Faites absolument une mise à jour vers les versions 7 ou 8 ou changez de navigateur (FireFox, Chrome, Opera, Safari)</div>";
}


// On ajoute le menu EdT
require_once("./menu.inc.php");


?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php
    require_once("./menu.inc.new.php");



if ($cherch_salle == "ok") {
	if ($ch_heure != "rien") {
		$auto_aff_21 = 1;
	}
	else
		echo ("<div class=\"cadreInformation\">Vous devez choisir un créneau !</div>");

	if ($ch_jour_semaine != "rien") {
		$auto_aff_22 = 1;
	}
	else
		echo ("<div class=\"cadreInformation\">Vous devez choisir un jour de la semaine !</div>");

	if (($auto_aff_21 == 1) AND ($auto_aff_22 == 1)) {
		$auto_aff_2 = 1;
	}
}

?>

	<div id="art-main">
        <div class="art-sheet">
            <div class="art-sheet-tl"></div>
            <div class="art-sheet-tr"></div>
            <div class="art-sheet-bl"></div>
            <div class="art-sheet-br"></div>
            <div class="art-sheet-tc"></div>
            <div class="art-sheet-bc"></div>
            <div class="art-sheet-cl"></div>
            <div class="art-sheet-cr"></div>
            <div class="art-sheet-cc"></div>
            <div class="art-sheet-body">
                <div class="art-nav">
                	<div class="l"></div>
                	<div class="r"></div>
                </div>
                        <div class="art-layout-cell art-sidebar1">
                        </div>
                        <div class="art-layout-cell art-content">

<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                           <div class="art-post">
                                <div class="art-post-tl"></div>
                                <div class="art-post-tr"></div>
                                <div class="art-post-bl"></div>
                                <div class="art-post-br"></div>
                                <div class="art-post-tc"></div>
                                <div class="art-post-bc"></div>
                                <div class="art-post-cl"></div>
                                <div class="art-post-cr"></div>
                                <div class="art-post-cc"></div>
                                <div class="art-post-body">
									<div class="art-post-inner art-article">
										<div class="art-postmetadataheader">
											<h2 class="art-postheader">
												Rechercher une salle libre
											</h2>
										</div>
										<div class="art-postcontent">
											<!-- article-content -->
<?php

if ($auto_aff_1 === 1) {

echo '
		<form action="index_edt.php" name="chercher" id="chercher" method="post">
	';

	// choix de l'horaire

	$sql="SELECT id_definie_periode, nom_definie_periode, heuredebut_definie_periode, heurefin_definie_periode, type_creneaux FROM edt_creneaux ORDER BY heuredebut_definie_periode";
	$req_heure = mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_heure=mysqli_num_rows($req_heure);
	$rep_heure = mysqli_fetch_array($req_heure);

/*
echo "<p>".count($rep_heure)."</p>";
echo "<pre>";
print_r($rep_heure);
echo "</pre>";
*/

echo '
			<input type="hidden" name="salleslibres" value="ok" />
			<input type="hidden" name="cherch_salle" value="ok" />

			<select name="ch_heure">
				<option value="rien">Horaire</option>
	';

	$tab_select_heure = array();

	$cpt_cours=0;
	//for($b=0;$b<count($rep_heure);$b++) {
	for($b=0;$b<$nb_heure;$b++) {
		$tab_select_heure[$b]["id_heure"] = mysql_result($req_heure, $b, "id_definie_periode");
		$tab_select_heure[$b]["creneaux"] = mysql_result($req_heure, $b, "nom_definie_periode");
		$tab_select_heure[$b]["heure_debut"] = mysql_result($req_heure, $b, "heuredebut_definie_periode");
		$tab_select_heure[$b]["heure_fin"] = mysql_result($req_heure, $b, "heurefin_definie_periode");
		$tab_select_heure[$b]["type_creneaux"] = mysql_result($req_heure, $b, "type_creneaux");
		if(isset($ch_heure)){
			if($ch_heure==$tab_select_heure[$b]["id_heure"]){
				$selected=" selected='true'";
			}
			else{
				$selected="";
			}
		}
		else{
			$selected="";
		}
		echo "<option value='".$tab_select_heure[$b]["id_heure"]."'".$selected;
		if($tab_select_heure[$b]["type_creneaux"]=='cours') {
			if($cpt_cours%2==0) {
				echo " style='background-color:#88C7FF;'";
			}
			else {
				echo " style='background-color:#B7DDFF;'";
			}
			$cpt_cours++;
		}
		elseif($tab_select_heure[$b]["type_creneaux"]=='repas') {
			echo " style='background-color:grey;'";
		}
		elseif($tab_select_heure[$b]["type_creneaux"]=='pause') {
			echo " style='background-color:lightgrey;'";
		}
		echo ">".$tab_select_heure[$b]["creneaux"]." : ".$tab_select_heure[$b]["heure_debut"]." - ".$tab_select_heure[$b]["heure_fin"]."</option>\n";

	}
echo "</select>\n<i> *</i>\n<br />\n";

	// choix du jour

	$req_jour = mysqli_query($GLOBALS["mysqli"], "SELECT id_horaire_etablissement, jour_horaire_etablissement FROM horaires_etablissement");
	$rep_jour = mysqli_fetch_array($req_jour);

echo "<select name=\"ch_jour_semaine\">\n";
echo "<option value='rien'>Jour</option>\n";
	$tab_select_jour = array();

	for($a=0;$a<=count($rep_jour);$a++) {

		$tab_select_jour[$a]["id"] = mysql_result($req_jour, $a, "id_horaire_etablissement");
		$tab_select_jour[$a]["jour_sem"] = mysql_result($req_jour, $a, "jour_horaire_etablissement");
	if(isset($ch_jour_semaine)){
		if($ch_jour_semaine==$tab_select_jour[$a]["jour_sem"]){
			$selected=" selected='true'";
		}
		else{
			$selected="";
		}
	}
	else{
		$selected="";
	}
		echo ("<option value='".$tab_select_jour[$a]["jour_sem"]."'".$selected.">".$tab_select_jour[$a]["jour_sem"]."</option>\n");
	}
echo "</select>\n<i> *</i>\n<br />\n";

	// choix de la semaine

	$req_semaine = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM edt_semaines");
	$rep_semaine = mysqli_fetch_array($req_semaine);

echo "<select name=\"semaine\">\n";
echo "<option value='rien'>Semaine</option>\n";
	$tab_select_semaine = array();

    $tab_select_semaine = RecupereLundisVendredis();

	for($d=0;$d<52;$d++) {
		$tab_select_semaine[$d]["id_semaine"] = mysql_result($req_semaine, $d, "id_edt_semaine");
		$tab_select_semaine[$d]["num_semaine"] = mysql_result($req_semaine, $d, "num_edt_semaine");
		$tab_select_semaine[$d]["type_semaine"] = mysql_result($req_semaine, $d, "type_edt_semaine");


		echo "<option value='".$tab_select_semaine[$d]["id_semaine"]."'>Semaine n° ".$tab_select_semaine[$d]["num_semaine"]." (".$tab_select_semaine[$d]["type_semaine"].") : ".$tab_select_semaine[$d]["lundis"]." - ".$tab_select_semaine[$d]["vendredis"]." </option>\n";

	}
echo "</select>\n<br />\n<em><font size=\"2\"> * champs obligatoires  </font></em>\n";
echo "<input type=\"submit\" name=\"Valider\" value=\"Valider\" />\n<br />\n";
echo "</form>\n";

}

if ($auto_aff_2 === 1) {
		// On reprend les infos sur les horaires demandés
		$requete_creneaux = mysqli_query($GLOBALS["mysqli"], "SELECT nom_definie_periode, heuredebut_definie_periode, heurefin_definie_periode FROM edt_creneaux WHERE id_definie_periode = '".$ch_heure."'");
		$reponse_tab_creneaux = mysqli_fetch_array($requete_creneaux);
	echo"<fieldset>\n<legend>Résultats</legend>\n";
	echo "Les salles libres le <font color=\"green\">".$ch_jour_semaine."</font> de <font color=\"green\">".$reponse_tab_creneaux["heuredebut_definie_periode"]." à ".$reponse_tab_creneaux["heurefin_definie_periode"]." ( ".$reponse_tab_creneaux["nom_definie_periode"]." )</font> sont :\n";
	echo "<br />\n";
		// On cherche les identifiants des salles où l'EdT est vide
	$salles_libres = aff_salles_vides($ch_heure, $ch_jour_semaine);
		// On affiche le nom des salles vides
		foreach($salles_libres as $tab_salib){
			if (nom_salle($tab_salib) == '') {
				echo("".numero_salle($tab_salib)."<br />\n");
			}else{
				echo("".nom_salle($tab_salib)."<br />\n");
			}
		}
	echo "</fieldset>\n";
}
    echo '</div>';
	
?>
											
											<div class="cleared"></div>
											<!-- /article-content -->
										</div>
										<div class="cleared"></div>
									</div>
								</div>
							</div>	
<br />
<br />



						</div>
			</div>
		</div>
	</div>
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>
