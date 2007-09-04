<html>
<head><title>debuggage ESSAI</title>
	<script src="../lib/functions.js" type="text/javascript" language="javascript"></script>

</head>
<body>

<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2007
 */
$titre_page = "Emploi du temps";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt

require_once("./fonctions_edt.php");

$req_type_login = "VEVRARD";
$id_creneaux = 3;
$jour_semaine = "lundi";
$type_edt = "prof";
$heuredeb_dec = "0";

$essai = contenu_enseignement($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $heuredeb_dec);

	echo $essai;


$aff_rien = "non";
	$cherche_creneaux = array();
	$cherche_creneaux = retourne_id_creneaux();
	$ch_index = array_search($id_creneaux, $cherche_creneaux);
		if (isset($cherche_creneaux[$ch_index-1])) {
			$ens_precedent=cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-1], $jour_semaine, $type_edt, $heuredeb_dec);
			if (isset($ens_precedent[0])) {
				$aff_precedent = renvoie_duree($cherche_creneaux[$ch_index-1], $jour_semaine, $ens_precedent[0]);
				$nbre_ens_precedent = count($ens_precedent);
				if ($aff_precedent > "3") {
					$aff_rien = "oui3";
				}
			}
			else $aff_precedent = NULL;
		}
		if (isset($cherche_creneaux[$ch_index-2])) {
			$ens_precedent=cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-2], $jour_semaine, $type_edt, $heuredeb_dec);
			if (isset($ens_precedent[0])) {
				$aff_precedent = renvoie_duree($cherche_creneaux[$ch_index-2], $jour_semaine, $ens_precedent[0]);
				$nbre_ens_precedent = count($ens_precedent);
				if ($aff_precedent > "5") {
					$aff_rien = "oui5";
				}
			}
			else $aff_precedent = NULL;
		}

		echo $aff_rien." ".$aff_precedent;
?>
<a href='javascript:centrerpopup("zdebuggage.php",610,460,"scrollbars=no,statusbar=no,resizable=yes")'>CLIC</a>
<?php
echo '<br /><br />-------------------------------------------------------<br /><br />';

$heure_deb = "10:15:00";
$heure_fin = "11:05:00";
$heure_test = "14:49:59";

if ($heure_deb < $heure_test) {
	echo 'heure_deb > heure_test';
}
else
echo 'cerise<br />';

$req_creneau = mysql_query("SELECT nom_definie_periode FROM absences_creneaux WHERE heuredebut_definie_periode < '".$heure_test."' AND heurefin_definie_periode > '".$heure_test."'");
$rep_creneau = mysql_fetch_array($req_creneau);

	for ($a=0; $a<count($req_creneau); $a++) {
		echo "<br />".$rep_creneau[$a]."<br />";
	}

print_r($rep_creneau["nom_definie_periode"]);

echo '<br />======================================== = = = ===============================<br />';
	// Travail sur une fonction qui retourne un tableau des périodes actuelles
	$annee_actu = date("Y"); // année
	$mois_actu = date("m"); // mois sous la forme 01 à 12
	$jour_actu = date("d"); // jour sous la forme 01 à 31
	$date_actu = $annee_actu."-".$mois_actu."-".$jour_actu;
	echo $date_actu."<br />";
	//$date_actu = "2007-10-05";
	$req_calend = mysql_query("SELECT * FROM edt_calendrier WHERE jourdebut_calendrier < '".$date_actu."' AND jourfin_calendrier > '".$date_actu."' AND etabferme_calendrier = '1' AND etabvacances_calendrier = '0'");
	$nbre_calend = mysql_num_rows($req_calend);
	for ($d=0; $d<$nbre_calend; $d++) {
	$rep_calend[$d]["nom"] = mysql_result($req_calend, $d, "nom_calendrier");
	echo $rep_calend[$d]["nom"]."<br />";
	}
	/* OK ça marche en l'état */

?>
</body>
</html>