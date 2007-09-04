<?php

/**
 * Fichier voir_edt.php pour visionner les différents EdT (classes ou professeurs)
 *
 * @version $Id$
 * @copyright 2007
 */

// Définir dés le début le type d'EdT qu'on veut voir (prof, classe, salle)

require_once("./fonctions_edt.php");

//===========================
// AJOUT: boireaus
$visioedt=isset($_GET['visioedt']) ? $_GET['visioedt'] : (isset($_POST['visioedt']) ? $_POST['visioedt'] : NULL);
$login_edt=isset($_GET['login_edt']) ? $_GET['login_edt'] : (isset($_POST['login_edt']) ? $_POST['login_edt'] : NULL);
$classe=isset($_GET['classe']) ? $_GET['classe'] : (isset($_POST['classe']) ? $_POST['classe'] : NULL);
$salle=isset($_GET['salle']) ? $_GET['salle'] : (isset($_POST['salle']) ? $_POST['salle'] : NULL);
//===========================

if ($visioedt == 'prof1') $type_edt = $login_edt;
elseif ($visioedt == 'classe1') $type_edt = $classe;
elseif ($visioedt == 'salle1') $type_edt = $salle;

echo "<span class=\"legende\">L'emploi du temps de :</span>\n";

if (isset($visioedt) AND $visioedt == "prof1") {
	require_once("./voir_edt_prof.php");
}

elseif (isset($visioedt) AND $visioedt == "salle1") {
	require_once("./voir_edt_salle.php");
}

elseif (isset($visioedt) AND $visioedt == "classe1") {
	require_once("./voir_edt_classe.php");
}

if ((isset($visioedt)) AND isset($login_edt) AND $visioedt == "prof1") {
	$aff_nom_edt = renvoie_nom_long(($login_edt), "prof");
}
elseif ((isset($visioedt)) AND isset($login_edt) AND $visioedt == "salle1") {
	$aff_nom_edt = renvoie_nom_long(($login_edt), "salle");
}
elseif ((isset($visioedt)) AND isset($login_edt) AND $visioedt == "classe1") {
	$aff_nom_edt = renvoie_nom_long(($login_edt), "classe");
}

if(isset($aff_nom_edt)){

	echo "<br />\n";

	$reglages_creneaux = GetSettingEdt("edt_aff_creneaux");

	$req_type_login = $login_edt;
	$type_edt = isset($_GET["type_edt_2"]) ? $_GET["type_edt_2"] : (isset($_POST["type_edt_2"]) ? $_POST["type_edt_2"] : NULL);
	premiere_ligne_tab_edt();

	//Cas où le nom des créneaux sont inscrits à gauche

	if ($reglages_creneaux == "noms") {
		$tab_creneaux = retourne_creneaux();
		$i=0;
		while($i<count($tab_creneaux)){
			$tab_id_creneaux = retourne_id_creneaux();
			$c=0;
			while($c<count($tab_id_creneaux)){
				echo("<tr><th rowspan=\"2\"><br />".$tab_creneaux[$i]."<br /><br /></th>\n".(construction_tab_edt($tab_id_creneaux[$c], "0"))."");
				echo("<tr>".(construction_tab_edt($tab_id_creneaux[$c], "0.5"))."\n");
				$i ++;
				$c ++;
			}
		}
	}

	// Cas où les heures sont inscrites à gauche au lieu du nom des créneaux
	elseif ($reglages_creneaux == "heures") {
		$tab_horaire = retourne_horaire();

		for($i=0; $i<count($tab_horaire); ) {

		$tab_id_creneaux = retourne_id_creneaux();
			$c=0;
			while($c<count($tab_id_creneaux)){
				echo("<tr><th rowspan=\"2\"><br />".$tab_horaire[$i]["heure_debut"]."<br />".$tab_horaire[$i]["heure_fin"]."<br /><br /></th>\n".(construction_tab_edt($tab_id_creneaux[$c], "0"))."");
				echo("<tr>".(construction_tab_edt($tab_id_creneaux[$c], "0.5"))."\n");
				$i++;
				$c ++;
			}
		}
	}

	echo "</tbody></table>\n";
}
?>