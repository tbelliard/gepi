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

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/gestion/changement_d_annee.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/gestion/changement_d_annee.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Changement d\'année.',
statut='';";
$insert=mysql_query($sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg = '';

if (isset($_POST['is_posted'])) {
	if ($_POST['is_posted']=='1') {
		check_token();

		if (isset($_POST['gepiYear'])) {
			if (!saveSetting("gepiYear", $_POST['gepiYear'])) {
				$msg .= "Erreur lors de l'enregistrement de l'année scolaire !";
			}
		}

		if (isset($_POST['begin_day']) and isset($_POST['begin_month']) and isset($_POST['begin_year'])) {
			$begin_bookings = mktime(0,0,0,$_POST['begin_month'],$_POST['begin_day'],$_POST['begin_year']);
			if (!saveSetting("begin_bookings", $begin_bookings))
					$msg .= "Erreur lors de l'enregistrement de begin_bookings !";
		}
		if (isset($_POST['end_day']) and isset($_POST['end_month']) and isset($_POST['end_year'])) {
			$end_bookings = mktime(0,0,0,$_POST['end_month'],$_POST['end_day'],$_POST['end_year']);
			if (!saveSetting("end_bookings", $end_bookings))
					$msg .= "Erreur lors de l'enregistrement de end_bookings !";
		}

	}
}

// Load settings
if (!loadSettings()) {
	die("Erreur chargement settings");
}
if (isset($_POST['is_posted']) and ($msg=='')) $msg = "Les modifications ont été enregistrées !";


//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
// End standart header
$titre_page = "Changement d'année";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php#chgt_annee' ".insert_confirm_abandon()."><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

echo "<p>Au changement d'année, avant d'initialiser la nouvelle année scolaire, il convient d'effectuer quelques opérations.<br />Elles sont en principe détaillées (<i>peut-être même plus à jour si des ajouts y ont été apportés après la sortie de votre version de GEPI</i>) sur le <a href='https://www.sylogix.org/projects/gepi/wiki/GuideAdministrateur' target='_blank'>Wiki</a>.</p>\n";

echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='form1' style='width: 100%;'>\n";
echo add_token_field();

$msg_svg="Il est recommandé de faire une copie de sauvegarde sur un périphérique externe (à stocker au coffre par exemple)";
$lien_svg="<a href='#svg_ext' ".insert_confirm_abandon()."><img src='../images/icons/ico_ampoule.png' width='15' height='25' title='$msg_svg' alt='$msg_svg' /></a>";

echo "<p>Les points sont les suivants&nbsp;:</p>\n";
echo "<p>La partie archivage de fin d'année&nbsp;:</p>\n";
echo "<ol>\n";
echo "<li><p><a href='accueil_sauve.php'>Sauvegarder la base</a> $lien_svg</p></li>\n";
if(strtolower(substr(getSettingValue('active_cahiers_texte'),0,1))=='y') {
	echo "<li><p>Eventuellement, faire un <a href='../cahier_texte_2/export_cdt.php'>export des cahiers de textes</a><br />et une <a href='accueil_sauve.php'>sauvegarde des documents du Cahier de textes</a> $lien_svg</p></li>\n";
	echo "<li><p><a href='../cahier_texte_2/archivage_cdt.php'>Archiver les cahiers de textes</a> pour permettre aux professeurs une consultation de leurs CDT passés.</p></li>\n";
}
if(getSettingValue('active_module_absence')=='2') {
	echo "<li><p><a href='../mod_abs2/extraction_saisies.php'>Effectuer une extraction CSV des absences</a>.</p></li>\n";
}
echo "<li><p>Sauvegarder l'arborescence Gepi (<em>par ftp, sftp,...</em>) $lien_svg</p></li>\n";
echo "<li><p>Conserver les données de l'année passée via le module Années antérieures.</p></li>\n";
echo "</ol>\n";

echo "<p>La partie concernant la nouvelle année&nbsp;:</p>\n";
echo "<ol>\n";
echo "<li><p>Modifier l'année scolaire&nbsp;: <input type='text' name='gepiYear' size='20' value='".getSettingValue('gepiYear')."' onchange='changement()' /></li>\n";
echo "<li><p>Modifier les dates de début et de fin des cahiers de textes&nbsp;:<br />";
?>

<table>
     <tr>
        <td>
        Date de début des cahiers de textes :
        </td>
        <td><?php
        $bday = strftime("%d", getSettingValue("begin_bookings"));
        $bmonth = strftime("%m", getSettingValue("begin_bookings"));
        $byear = strftime("%Y", getSettingValue("begin_bookings"));
        genDateSelector("begin_", $bday, $bmonth, $byear,"more_years") ?>
        </td>
    </tr>
    <tr>
        <td>
        Date de fin des cahiers de textes :
        </td>
        <td><?php
        $eday = strftime("%d", getSettingValue("end_bookings"));
        $emonth = strftime("%m", getSettingValue("end_bookings"));
        $eyear= strftime("%Y", getSettingValue("end_bookings"));
        genDateSelector("end_",$eday,$emonth,$eyear,"more_years") ?>
        </td>
    </tr>
</table>

<?php
echo "</li>\n";

echo "</ol>\n";

echo "<input type='hidden' name='is_posted' value='1' />\n";
echo "<input type='submit' name='Valider' value='Valider' />\n";
echo "</form>\n";

echo "<p><br /></p>\n";

echo "<a name='svg_ext'></a>";
echo "<p><em>NOTES&nbsp;:</em></p>\n";
echo "<p style='margin-left:3em;'>La sauvegarde sur périphérique externe permet de remettre en place un GEPI si jamais votre GEPI en ligne subit des dégats (<em>crash du disque dur hébergeant votre GEPI, incendie du local serveur,...</em>).<br />Vous n'aurez normalement jamais besoin de ces sauvegardes, mais mieux vaut prendre des précautions.</p>\n";

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
