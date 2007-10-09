<?php
/* fichier pour visionner l'EdT d'un élève */

$login_edt=isset($_POST['login_edt']) ? $_POST['login_edt'] : NULL;

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

/*	/ On peut aussi faire un affichage par classe (à revoir)
	echo "<td>\n ou par classe : </td>";
	echo "<td><form action=\"index_edt.php\" name=\"liste_classe\" method=\"post\">\n";
	echo "<select name=\"login_edt\" onchange='document.liste_classe.submit();'>\n";
	echo "<option value=\"rien\">Choix de la classe</option>\n";

		$tab_select = renvoie_liste("classe");

		for($i=0;$i<count($tab_select);$i++) {

	echo ("<option value='".$tab_select[$i]["id"]."'>".$tab_select[$i]["classe"]."</option>\n");
		}

	echo "</select>\n";
	echo "<input type=hidden name=\"choix_classe\" value=\"ok\" />\n";
	echo "<input type=hidden name=\"type_edt_2\" value=\"eleve\" />\n";
	echo "<input type=hidden name=\"visioedt\" value=\"eleve1\" />\n";
	echo "</form>\n";
	echo "</td>\n";
*/
	echo "</tr>\n</table>\n";

		// puis un formulaire des élèves alphabétique aussi
	if (isset($_POST["alphabet_eleves"]) OR isset($_POST["choix_classe"])) {

		echo "<center>\n";
		echo "<form action=\"index_edt.php\" name=\"liste_eleves\" method=\"post\">\n";
		echo "<select name='login_edt' onchange='document.liste_eleves.submit();'>\n";
		echo "<option value=\"rien\">Choix de l'élève</option>\n";

			if (isset($_POST["alphabet_eleves"])) {
				$tab_select = renvoie_liste_a("eleve", $_POST["alphabet_eleves"]);
			}
			elseif (isset($_POST["choix_classe"])) {
				$tab_select = renvoie_liste_classe($_POST["choix_classe"]);
			}
			else $tab_select = NULL;

			for($i=0;$i<count($tab_select);$i++) {

		echo ("<option value='".$tab_select[$i]["login"]."'>".$tab_select[$i]["nom"].' '.$tab_select[$i]["prenom"].' '.aff_nom_classe($tab_select[$i]["login"])."</option>\n");

			}

		echo "</select>\n";
		echo "<input type=hidden name=\"type_edt_2\" value=\"eleve\" />\n";
		echo "<input type=hidden name=\"visioedt\" value=\"eleve1\" />\n";
		echo "</form>\n";
		echo "</center>\n";
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
	echo '</table>';
}


	//require("../lib/footer.inc.php");

?>