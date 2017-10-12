<?php

/**
 * EdT Gepi : le menu pour les includes require_once().
 *
 *
 * Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Stephane Boireau
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

// Sécurité : éviter que quelqu'un appelle ce fichier seul
$serveur_script = $_SERVER["SCRIPT_NAME"];
//$analyse = explode("/", $serveur_script);
//if ($analyse[3] == "menu.inc.php") {
if (preg_match("#/menu.inc.php#", $serveur_script)) {
	die("Accès non autorisé");
}

// ========================= Récupérer le bon fichier de langue

require_once('../edt_organisation/choix_langue.php');


require_once('../edt_organisation/fonctions_calendrier.php');
//===========================INITIALISATION DES VARIABLES=======

// Pour éviter d'avoir un décalage dans les infobulles
$pas_de_decalage_infobulle = "oui";


//===========================

// Fonction qui gère le fonctionnement du menu
function menuEdtJs($numero){
	$aff_menu_edt = "";
	// On récupère la valeur du réglage "param_menu_edt"
	$reglage = mysqli_fetch_array(mysqli_query($GLOBALS["mysqli"], "SELECT valeur FROM edt_setting WHERE reglage = 'param_menu_edt'"));
	if ($reglage["valeur"] == "mouseover") {
		$aff_menu_edt = " onmouseover=\"javascript:montre('sEdTmenu".$numero."');\"";
	} elseif ($reglage["valeur"] == "click") {
		$aff_menu_edt = " onclick=\"javascript:montre('sEdTmenu".$numero."');\"";
	} else {
		$aff_menu_edt = "";
	}
	return $aff_menu_edt;
}
function displaydd($numero){
		// On récupère la valeur du réglage "param_menu_edt"
	$reglage = mysqli_fetch_array(mysqli_query($GLOBALS["mysqli"], "SELECT valeur FROM edt_setting WHERE reglage = 'param_menu_edt'"));
	if ($reglage["valeur"] == "rien") {
		return " style=\"display: block;\"";
	}else {
		return "style=\"display: none;\"";
		//return "";
	}
}
function statutAutreSetting(){
	// On cherche quel est le droit dont dispose cet utilisateur 'autre'
	if ($_SESSION["statut"] == 'autre') {
		$query = mysqli_query($GLOBALS["mysqli"], "SELECT autorisation FROM droits_speciaux WHERE id_statut = '".$_SESSION["statut_special_id"]."' AND nom_fichier = '/tous_les_edt'");
		$rep = old_mysql_result($query, 0,"autorisation");

		if ($rep["autorisation"] == 'V') {
			return 'oui';
		}else{
			return 'non';
		}
	}else{
		return 'oui';
	}
}



?>
<!-- On affiche le menu edt -->

	<div id="agauche">
<?php

if (getSettingValue("use_only_cdt") != 'y' OR $_SESSION["statut"] != 'professeur') 
{ ?>
        <div class="dates_header"></div>
        <div class="dates" title="Semaine courante">
  		<p>
		<?php echo (WEEK_NUMBER.date("W")); ?>
		</p>

        <p class="dates_text">
        <?php AfficheDatesDebutFinSemaine() ?>
		</p>

		<p>
		<?php AffichePeriode(time()); ?>
		</p>

		<p>
		<?php 
			$semActu = typeSemaineActu();
			//echo "\$semActu=$semActu<br />";
			if ($semActu != NULL) {
				echo "Semaine ".typeSemaineActu();
			}
		?>
		</p>
        </div>

        
        <?php
        // =================== On active ce menu uniquement pour IE6 ==================
        $ua = getenv("HTTP_USER_AGENT");
        if (strstr($ua, "MSIE 6.0")) {
        ?>

		<dl id="menu_edt">

		<dt<?php echo menuEdtJs("1"); ?>><?php echo VIEWS ?></dt>

			<dd id="sEdTmenu1" <?php echo displaydd("1"); ?>>	<!-- Régis -->
				<ul>
					<?php if (statutAutreSetting() == 'oui') {
						echo '
					<li><a href="index_edt.php?visioedt=prof1">'.TEACHERS.'</a></li>';
					}?>
					<li><a href="index_edt.php?visioedt=classe1"><?php echo CLASSES ?></a></li>
					<?php if (statutAutreSetting() == 'oui') {
						echo '
					<li><a href="index_edt.php?visioedt=salle1">'.CLASSROOMS.'</a></li>';
					}?>
					<li><a href="index_edt.php?visioedt=eleve1"><?php echo STUDENTS ?></a></li>
				</ul>
			</dd>
<?php /* 
if ($_SESSION['statut'] == "administrateur") {
echo '
		<dt'.menuEdtJs("2").'>'.MODIFY.'</dt>

			<dd id="sEdTmenu2"'.displaydd("2").'>
				<ul>
					<li><a href="modif_edt_tempo.php">temporairement</a></li>
				</ul>
			</dd>';
} */

	// La fonction chercher_salle est paramétrable
$aff_cherche_salle = GetSettingEdt("aff_cherche_salle");
	if ($aff_cherche_salle == "tous") {
		$aff_ok = "oui";
	}
	else if ($aff_cherche_salle == "admin") {
		$aff_ok = "administrateur";
	}
	else
	$aff_ok = "non";
    
	// En fonction du résultat, on propose l'affichage ou non
	if ($aff_ok == "oui" OR $_SESSION["statut"] == $aff_ok) {
		echo '
		<dt'.menuEdtJs("3").'>'.LOOKFOR.'</dt>

			<dd id="sEdTmenu3" '.displaydd("3").'>
				<ul>
					<li><a href="index_edt.php?salleslibres=ok">'.FREE_CLASSROOMS.'</a></li>
				</ul>
			</dd>
			';
	}

if ($_SESSION['statut'] == "administrateur") {
	echo '
		<dt'.menuEdtJs("4").'>'.ADMINISTRATOR.'</dt>

			<dd id="sEdTmenu4" '.displaydd("4").'>
				<ul>
					<li style="font-size: 0.9em;"><a href="voir_groupe.php">'.LESSONS.'</a></li>';

if (getSettingValue("mod_edt_gr") == "y") {
	echo '
	<li><a href="../edt_gestion_gr/edt_aff_gr.php">'.GROUPS.'</a></li>';
}

	echo '
					<li><a href="ajouter_salle.php">'.CLASSROOMS.'</a></li>
					<li><a href="edt_initialiser.php">'.INITIALIZE.'</a></li>
					<li><a href="edt_parametrer.php">'.PARAMETER.'</a></li>
					<li><a href="edt_param_couleurs.php">'.COLORS.'</a></li>
				</ul>
			</dd>

		<dt'.menuEdtJs("5").'>'.CALENDAR.'</dt>

			<dd id="sEdTmenu5" '.displaydd("5").'>
				<ul>
					<li><a href="edt_calendrier.php">'.PERIODS.'</a></li>
					<li><a href="./admin_config_semaines.php?action=visualiser">'.WEEKS.'</a></li>
				</ul>
			</dd>
		';
}
?>
		</dl>
        <?php } ?>
<br />
<?php
}
?>

	</div>
