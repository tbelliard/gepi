<?php
/* fichier pour visionner l'EdT d'un élève */

$login_edt=isset($_POST['login_edt']) ? $_POST['login_edt'] : NULL;

if ($_SESSION['statut'] != "eleve") {
		// On affiche un formulaire alphabétique
	echo "</center>\n<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n<tr>\n<td>\n";
	echo "<span style='font-size: small; font-weight:normal;'>Rechercher tous les noms commençant par : </span>\n</td>\n<td>\n";
	echo "<form action=\"index_edt.php\" name=\"liste_lettres\" method=\"POST\">\n";
	echo "<select name=\"alphabet_eleves\" onchange='document.liste_lettres.submit();'>\n";

	echo "<OPTION value=\"rien\">la lettre suivante</OPTION>\n";
	echo "<OPTION value=\"A\">A</OPTION>\n";
	echo "<OPTION value=\"B\">B</OPTION>\n";
	echo "<OPTION value=\"C\">C</OPTION>\n";
	echo "<OPTION value=\"D\">D</OPTION>\n";
	echo "<OPTION value=\"E\">E</OPTION>\n";
	echo "<OPTION value=\"F\">F</OPTION>\n";
	echo "<OPTION value=\"G\">G</OPTION>\n";
	echo "<OPTION value=\"H\">H</OPTION>\n";
	echo "<OPTION value=\"I\">I</OPTION>\n";
	echo "<OPTION value=\"J\">J</OPTION>\n";
	echo "<OPTION value=\"K\">K</OPTION>\n";
	echo "<OPTION value=\"L\">L</OPTION>\n";
	echo "<OPTION value=\"M\">M</OPTION>\n";
	echo "<OPTION value=\"N\">N</OPTION>\n";
	echo "<OPTION value=\"O\">O</OPTION>\n";
	echo "<OPTION value=\"P\">P</OPTION>\n";
	echo "<OPTION value=\"Q\">Q</OPTION>\n";
	echo "<OPTION value=\"R\">R</OPTION>\n";
	echo "<OPTION value=\"S\">S</OPTION>\n";
	echo "<OPTION value=\"T\">T</OPTION>\n";
	echo "<OPTION value=\"U\">U</OPTION>\n";
	echo "<OPTION value=\"V\">V</OPTION>\n";
	echo "<OPTION value=\"W\">W</OPTION>\n";
	echo "<OPTION value=\"X\">X</OPTION>\n";
	echo "<OPTION value=\"Y\">Y</OPTION>\n";
	echo "<OPTION value=\"Z\">Z</OPTION>\n";
	echo "<input type=\"hidden\" name=\"choix_lettre\" value=\"ok\" />\n";
	echo "<input type=\"hidden\" name=\"type_edt_2\" value=\"eleve\" />\n";
	echo "<input type=\"hidden\" name=\"visioedt\" value=\"eleve1\" />\n";
	echo "</select>\n";
	echo "</form>\n</td>\n";

		/*/ On peut aussi faire un affichage par classe (à revoir)
	echo "<td>\n ou par classe : </td>";
	echo "<td><form action=\"index_edt.php\" name=\"liste_classe\" method=\"POST\">\n";
	echo "<select name=\"login_edt\" onchange='document.liste_classe.submit();'>\n";
	echo "<OPTION value=\"rien\">Choix de la classe</OPTION>\n";

		$tab_select = renvoie_liste("classe");

		for($i=0;$i<count($tab_select);$i++) {

	echo ("<OPTION value='".$tab_select[$i]["id"]."'>".$tab_select[$i]["classe"]."</OPTION>\n");
		}
	echo "<input type=hidden name=\"choix_classe\" value=\"ok\" />\n";
	echo "<input type=hidden name=\"type_edt_2\" value=\"eleve\" />\n";
	echo "<input type=hidden name=\"visioedt\" value=\"eleve1\" />\n";

	echo "</SELECT>\n</FORM>\n";*/
	echo "</td>\n</tr>\n</table>\n<center>\n";

		// puis un formulaire des élèves alphabétique aussi
	if (isset($_POST["alphabet_eleves"]) OR isset($_POST["choix_classe"])) {


		echo "<form action=\"index_edt.php\" name=\"liste_eleves\" method=\"POST\">\n";
		echo "<select name='login_edt' onchange='document.liste_eleves.submit();'>\n";
		echo "<OPTION value=\"rien\">Choix de l'élève</OPTION>\n";

			if (isset($_POST["alphabet_eleves"])) {
				$tab_select = renvoie_liste_a("eleve", $_POST["alphabet_eleves"]);
			}
			elseif (isset($_POST["choix_classe"])) {
				$tab_select = renvoie_liste_classe($_POST["choix_classe"]);
			}
			else $tab_select = NULL;

			for($i=0;$i<count($tab_select);$i++) {

		echo ("<OPTION value='".$tab_select[$i]["login"]."'>".$tab_select[$i]["nom"].' '.$tab_select[$i]["prenom"].' '.aff_nom_classe($tab_select[$i]["login"])."</OPTION>\n");

			}
		echo "<input type=hidden name=\"type_edt_2\" value=\"eleve\" />\n";
		echo "<input type=hidden name=\"visioedt\" value=\"eleve1\" />\n";

		echo "</SELECT>\n";
		echo "</FORM>\n";

	}
}

if ((isset($_POST['visioedt'])) AND isset($_POST["login_edt"])) {

	$aff_nom_edt = renvoie_nom_long(($_POST["login_edt"]), "eleve");
	echo "<span style='font-size: small; font-weight:normal;'>L'emploi du temps de ".$aff_nom_edt."</span>\n";
}
	echo "<br /><br />\n";

$req_type_login=(isset($_POST['login_edt']) ? $_POST['login_edt'] : NULL) OR (isset($_SESSION["login"]) ? $_SESSION["login"] : NULL);

if ($_SESSION["statut"] == "eleve") {
	$req_type_login = $_SESSION["login"];
}
else $req_type_login = $login_edt;


if (isset($_POST["login_edt"])) {


	$type_edt = $_POST["type_edt_2"];
		premiere_ligne_tab_edt();

//Cas où le nom des créneaux sont inscrits à gauche
// La gestion des edt_settings est à revoir
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

// Cas où les heures sont inscrites à gauche au lieu du nom des créneaux
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


/*
	$tab_creneaux = retourne_creneaux();
		$i=0;
	while($i<count($tab_creneaux)){

	$tab_id_creneaux = retourne_id_creneaux();
		$c=0;
		while($c<count($tab_id_creneaux)){

		print('<tr><th><br />'.$tab_creneaux[$i].'<br /><br /></th>'.(construction_tab_edt($tab_id_creneaux[$c])));
		$i ++;
		$c ++;
		}
	}*/
}
	echo '</tbody></table>';

	require("../lib/footer.inc.php");

?>