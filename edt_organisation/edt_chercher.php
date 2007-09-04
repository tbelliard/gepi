<?php
// Fichier qui permet de faire des recherches dans l'EdT

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

if ($cherch_salle == "ok") {
	if ($ch_heure != "rien") {
		$auto_aff_21 = 1;
	}
	else
		echo ("<font color = \"red\">Vous devez choisir un créneau !</font>\n<br />\n");

	if ($ch_jour_semaine != "rien") {
		$auto_aff_22 = 1;
	}
	else
		echo ("<font color=\"red\">Vous devez choisir un jour de la semaine !</font>\n<br />\n");

	if (($auto_aff_21 == 1) AND ($auto_aff_22 == 1)) {
		$auto_aff_2 = 1;
	}
}

?>

Cette page sert &agrave; trouver les salles de cours occup&eacute;es et libres &agrave; un horaire de la semaine.

Pour cela, veuillez choisir un cr&eacute;neau et un jour de la semaine :
<br />
<br />

<?php

if ($auto_aff_1 === 1) {

echo "</center>\n<fieldset id=\"cherchersalle\">\n<legend>Chercher une salle libre</legend>\n";

echo "<form action=\"index_edt.php\" name=\"chercher\" id=\"chercher\" method=\"POST\">\n";
echo "<INPUT type='hidden' name='salleslibres' value='ok'>\n";
echo "<INPUT type='hidden' name='cherch_salle' value='ok'>\n";

	// choix de l'horaire

	$req_heure = mysql_query("SELECT id_definie_periode, nom_definie_periode, heuredebut_definie_periode, heurefin_definie_periode FROM absences_creneaux ORDER BY heuredebut_definie_periode");
	$rep_heure = mysql_fetch_array($req_heure);

echo "<SELECT name=\"ch_heure\">\n";
echo ("<OPTION value='rien'>Horaire</OPTION>\n");
	$tab_select_heure = array();

	for($b=0;$b<count($rep_heure);$b++) {
		$tab_select_heure[$b]["id_heure"] = mysql_result($req_heure, $b, "id_definie_periode");
		$tab_select_heure[$b]["creneaux"] = mysql_result($req_heure, $b, "nom_definie_periode");
		$tab_select_heure[$b]["heure_debut"] = mysql_result($req_heure, $b, "heuredebut_definie_periode");
		$tab_select_heure[$b]["heure_fin"] = mysql_result($req_heure, $b, "heurefin_definie_periode");
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
		echo ("<OPTION value='".$tab_select_heure[$b]["id_heure"]."'".$selected.">".$tab_select_heure[$b]["creneaux"]." : ".$tab_select_heure[$b]["heure_debut"]." - ".$tab_select_heure[$b]["heure_fin"]."</OPTION>\n");

	}
echo "</SELECT>\n<i> *</i>\n<br />\n";

	// choix du jour

	$req_jour = mysql_query("SELECT id_horaire_etablissement, jour_horaire_etablissement FROM horaires_etablissement");
	$rep_jour = mysql_fetch_array($req_jour);

echo "<SELECT name=\"ch_jour_semaine\">\n";
echo "<OPTION value='rien'>Jour</OPTION>\n";
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
		echo ("<OPTION value='".$tab_select_jour[$a]["jour_sem"]."'".$selected.">".$tab_select_jour[$a]["jour_sem"]."</OPTION>\n");
	}
echo "</SELECT>\n<i> *</i>\n<br />\n";

	// choix de la semaine

	$req_semaine = mysql_query("SELECT * FROM edt_semaines");
	$rep_semaine = mysql_fetch_array($req_semaine);

echo "<SELECT name=\"semaine\">\n";
echo "<OPTION value='rien'>Semaine</OPTION>\n";
	$tab_select_semaine = array();

	for($d=0;$d<52;$d++) {
		$tab_select_semaine[$d]["id_semaine"] = mysql_result($req_semaine, $d, "id_edt_semaine");
		$tab_select_semaine[$d]["num_semaine"] = mysql_result($req_semaine, $d, "num_edt_semaine");
		$tab_select_semaine[$d]["type_semaine"] = mysql_result($req_semaine, $d, "type_edt_semaine");

		echo "<OPTION value='".$tab_select_semaine[$d]["id_semaine"]."'>Semaine n° ".$tab_select_semaine[$d]["num_semaine"]." (".$tab_select_semaine[$d]["type_semaine"].") </OPTION>\n";

	}
echo "</SELECT>\n<br />\n<em><font size=\"2\"> * champs obligatoires  </font></em>\n";
echo "<input type=\"submit\" name=\"Valider\" value=\"Valider\" />\n<br />\n";
echo "</FORM>\n</fieldset>\n";

}

if ($auto_aff_2 === 1) {
		// On reprend les infos sur les horaires demandés
		$requete_creneaux = mysql_query("SELECT nom_definie_periode, heuredebut_definie_periode, heurefin_definie_periode FROM absences_creneaux WHERE id_definie_periode = '".$ch_heure."'");
		$reponse_tab_creneaux = mysql_fetch_array($requete_creneaux);
	echo"<fieldset>\n<legend>Résultats</legend>\n";
	echo "Les salles libres le <font color=\"green\">".$ch_jour_semaine."</font> de <font color=\"green\">".$reponse_tab_creneaux["heuredebut_definie_periode"]." à ".$reponse_tab_creneaux["heurefin_definie_periode"]." ( ".$reponse_tab_creneaux["nom_definie_periode"]." )</font> sont :\n";
	echo "<br />\n";
		// On cherche les identifiants des salles où l'EdT est vide
	$salles_libres = aff_salles_vides($ch_heure, $ch_jour_semaine);
		// On affiche le nom des salles vides
		foreach($salles_libres as $tab_salib){
			echo("".nom_salle($tab_salib)."<br />\n");
		}
}
	echo "</fieldset>\n";
?>