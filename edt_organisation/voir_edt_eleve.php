<?php
/* fichier pour visionner l'EdT d'un élève */

$login_edt = isset($_POST["login_edt"]) ? $_POST["login_edt"] : (isset($_GET["login_edt"]) ? $_GET["login_edt"] : NULL);
$choix_classe = isset($_POST["choix_classe"]) ? $_POST["choix_classe"] : (isset($_GET["choix_classe"]) ? $_GET["choix_classe"] : NULL);
$choix_classe1 = isset($_POST["choix_classe1"]) ? $_POST["choix_classe1"] : (isset($_GET["choix_classe1"]) ? $_GET["choix_classe1"] : NULL);
$alphabet_eleves = isset($_POST["alphabet_eleves"]) ? $_POST["alphabet_eleves"] : (isset($_GET["alphabet_eleves"]) ? $_GET["alphabet_eleves"] : NULL);
$choix_lettre = isset($_POST["choix_lettre"]) ? $_POST["choix_lettre"] : (isset($_GET["choix_lettre"]) ? $_GET["choix_lettre"] : NULL);
$login_edt = isset($_POST["login_edt"]) ? $_POST["login_edt"] : (isset($_GET["login_edt"]) ? $_GET["login_edt"] : NULL);

if ($_SESSION["statut"] != "eleve" AND $_SESSION["statut"] != "responsable") {
		// On affiche un formulaire alphabétique
	echo '
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<span style="font-size: small; font-weight:normal;">Rechercher tous les noms commençant par : </span>
		</td>
		<td>
			<form action="index_edt.php" name="liste_lettres" method="post">
				<select name="alphabet_eleves" onchange=\'document.liste_lettres.submit();\'>
		';

	echo "<option value=\"rien\">la lettre suivante</option>\n";
	echo "<option value=\"A\">A</option>\n";
	echo "<option value=\"B\">B</option>\n";
	echo "<option value=\"C\">C</option>\n";
	echo "<option value=\"D\">D</option>\n";
	echo "<option value=\"E\">E</option>\n";
	echo "<option value=\"F\">F</option>\n";
	echo "<option value=\"G\">G</option>\n";
	echo "<option value=\"H\">H</option>\n";
	echo "<option value=\"I\">I</option>\n";
	echo "<option value=\"J\">J</option>\n";
	echo "<option value=\"K\">K</option>\n";
	echo "<option value=\"L\">L</option>\n";
	echo "<option value=\"M\">M</option>\n";
	echo "<option value=\"N\">N</option>\n";
	echo "<option value=\"O\">O</option>\n";
	echo "<option value=\"P\">P</option>\n";
	echo "<option value=\"Q\">Q</option>\n";
	echo "<option value=\"R\">R</option>\n";
	echo "<option value=\"S\">S</option>\n";
	echo "<option value=\"T\">T</option>\n";
	echo "<option value=\"U\">U</option>\n";
	echo "<option value=\"V\">V</option>\n";
	echo "<option value=\"W\">W</option>\n";
	echo "<option value=\"X\">X</option>\n";
	echo "<option value=\"Y\">Y</option>\n";
	echo "<option value=\"Z\">Z</option>\n";
	echo "</select>\n";
	echo "<input type=\"hidden\" name=\"choix_lettre\" value=\"ok\" />\n";
	echo "<input type=\"hidden\" name=\"type_edt_2\" value=\"eleve\" />\n";
	echo "<input type=\"hidden\" name=\"visioedt\" value=\"eleve1\" />\n";
	echo "</form>\n</td>\n";

	// On peut aussi faire un affichage par classe (à revoir)
	echo "<td>\n ou la liste des élèves de </td>";
	echo "<td><form action=\"index_edt.php\" name=\"liste_classe\" method=\"post\">\n";
	echo "<select name=\"choix_classe1\" onchange='document.liste_classe.submit();'>\n";
	echo "<option value=\"rien\">cette classe</option>\n";

		$tab_select = renvoie_liste("classe");

		for($i=0;$i<count($tab_select);$i++) {

	echo ("<option value='".$tab_select[$i]["id"]."'>".$tab_select[$i]["classe"]."</option>\n");
		}

	echo "</select>\n";
	echo "<input type=\"hidden\" name=\"choix_classe\" value=\"ok\" />\n";
	echo "<input type=\"hidden\" name=\"type_edt_2\" value=\"eleve\" />\n";
	echo "<input type=\"hidden\" name=\"visioedt\" value=\"eleve1\" />\n";
	echo "</form>\n";
	echo "</td>\n";

	echo "</tr>\n</table>\n";

		// puis un formulaire des élèves alphabétique aussi
	if (isset($alphabet_eleves) OR isset($choix_classe)) {

			if (isset($alphabet_eleves) AND $alphabet_eleves != "rien") {
				$tab_select = renvoie_liste_a("eleve", $alphabet_eleves);
			}
			elseif (isset($choix_classe) AND $choix_classe == "ok") {
				$tab_select = renvoie_liste_classe($choix_classe1);
			}
			else $tab_select = NULL;

		echo "<form action=\"index_edt.php\" name=\"liste_eleves\" method=\"post\">\n";

// Eleve suivant
$indice_eleve_select = -1;
if(isset($login_edt)){
	for($i=0; $i<count($tab_select); $i++) {
		if($login_edt == $tab_select[$i]["login"]){
			$indice_eleve_select=$i;
			break;
		}
	}
}

//if(isset($login_edt)){
if($indice_eleve_select != -1){
	if($indice_eleve_select != 0){
		$precedent = $indice_eleve_select-1;
		echo "
		<span class=\"edt_suivant\">
		";
			if ($choix_classe == "ok") {
				echo "<a href='index_edt.php?choix_classe=ok&amp;choix_classe1=".$choix_classe1."&amp;visioedt=eleve1&amp;login_edt=".$tab_select[$precedent]["login"]."&amp;type_edt_2=eleve'>Eleve précédent</a>";
			}
			else if ($choix_lettre == "ok") {
				echo "<a href='index_edt.php?choix_lettre=ok&amp;alphabet_eleves=".$alphabet_eleves."&amp;visioedt=eleve1&amp;login_edt=".$tab_select[$precedent]["login"]."&amp;type_edt_2=eleve'>Eleve précédent</a>";
			}
			else {
				echo "";
			}
		echo "
		</span>
			";
	}
}

		echo "<select name='login_edt' onchange='document.liste_eleves.submit();'>\n";
		echo "<option value=\"rien\">Choix de l'élève</option>\n";

			for($i=0; $i<count($tab_select); $i++) {

				$req_nom = mysql_fetch_array(mysql_query("SELECT nom, prenom FROM eleves WHERE login = '".$tab_select[$i]["login"]."'"));

				// On conserve à l'affichage le nom de l'élève
				if(isset($login_edt)){
					if($login_edt == $tab_select[$i]["login"]){
						$selected = " selected='selected'";
					}
					else{
						$selected = "";
					}
				}
				else{
					$selected = "";
				}

		echo ("<option value='".$tab_select[$i]["login"]."'".$selected.">".$req_nom["nom"].' '.$req_nom["prenom"].' '.aff_nom_classe($tab_select[$i]["login"])."</option>\n");

			} //for$i=0...

		echo "</select>\n";
		echo "<input type=hidden name=\"type_edt_2\" value=\"eleve\" />\n";
		echo "<input type=hidden name=\"visioedt\" value=\"eleve1\" />\n";

		// On garde en mémoire la lettre ou la classe
		if ($choix_classe == "ok") {
			echo "<input type=\"hidden\" name=\"choix_classe\" value=\"ok\" />\n";
			echo "<input type=\"hidden\" name=\"choix_classe1\" value=\"".$choix_classe1."\" />\n";
		}
		else if ($choix_lettre == "ok") {
			echo "<input type=\"hidden\" name=\"alphabet_eleves\" value=\"".$alphabet_eleves."\" />\n";
			echo "<input type=\"hidden\" name=\"choix_lettre\" value=\"ok\" />\n";
		}

if($indice_eleve_select != -1){
	$suivant = $indice_eleve_select+1;
	if($suivant<count($tab_select)){

		echo "
		<span class=\"edt_suivant\">
		";
			if ($choix_classe == "ok") {
				echo "<a href='index_edt.php?choix_classe=ok&amp;choix_classe1=".$choix_classe1."&amp;visioedt=eleve1&amp;login_edt=".$tab_select[$suivant]["login"]."&amp;type_edt_2=eleve'>Eleve suivant</a>";
			}
			else if ($choix_lettre == "ok") {
				echo "<a href='index_edt.php?choix_lettre=ok&amp;alphabet_eleves=".$alphabet_eleves."&amp;visioedt=eleve1&amp;login_edt=".$tab_select[$suivant]["login"]."&amp;type_edt_2=eleve'>Eleve suivant</a>";
			}
			else {
				echo "";
			}

		echo "
		</span>
			";
	}
}

		echo "</form>\n";

	} //if (isset($alphabet_eleves) OR isset($choix_classe))
} //if ($_SESSION["statut"] != "eleve" AND $_SESSION["statut"] != "responsable")

//===============================================================
//=============== AFFICHAGE du tableau EdT ======================
//===============================================================

	echo "<br />\n";

$req_type_login = (isset($_POST['login_edt']) ? $_POST['login_edt'] : NULL) OR (isset($_SESSION["login"]) ? $_SESSION["login"] : NULL);

if ($_SESSION["statut"] == "eleve") {
	$req_type_login = $_SESSION["login"];
}
else $req_type_login = $login_edt;


if (isset($login_edt)) {


	$type_edt = isset($_POST["type_edt_2"]) ? $_POST["type_edt_2"] : NULL;
		premiere_ligne_tab_edt();

// On récupère le choix de l'admin sur l'affichage à gauche
$reglages_creneaux = GetSettingEdt("edt_aff_creneaux");

	// affichage par nom de creneaux
if ($reglages_creneaux == "noms") {
	$tab_creneaux = retourne_creneaux();
		$i=0;
	while($i<count($tab_creneaux)){

		$tab_id_creneaux = retourne_id_creneaux();
		$c=0;

		while($c<count($tab_id_creneaux)){

		echo("<tr><th rowspan=\"2\"><br />".$tab_creneaux[$i]."<br /><br /></th>".(construction_tab_edt($tab_id_creneaux[$c], "0"))."\n");
		echo("<tr>".(construction_tab_edt($tab_id_creneaux[$c], "0.5"))."\n");
		$i ++;
		$c ++;
		}
	}
}

	// Affichage par les heures des créneaux
elseif ($reglages_creneaux == "heures") {
	$tab_horaire = retourne_horaire();

	for($i=0; $i<count($tab_horaire); ) {

		$tab_id_creneaux = retourne_id_creneaux();
		$c=0;

		while($c<count($tab_id_creneaux)){

		echo("<tr><th rowspan=\"2\"><br />".$tab_horaire[$i]["heure_debut"]."<br />".$tab_horaire[$i]["heure_fin"]."<br /><br /></th>".(construction_tab_edt($tab_id_creneaux[$c], "0"))."\n");
		echo("<tr>".(construction_tab_edt($tab_id_creneaux[$c], "0.5"))."\n");
		$i++;
		$c ++;
		}
	}
}
	echo '</table>';

} //if (isset($login_edt)) {

?>