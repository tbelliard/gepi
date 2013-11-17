<?php

/**
 *
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

$titre_page = "Emploi du temps - Groupes";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die('Vous devez demander à votre administrateur l\'autorisation de voir cette page.');
}
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";

//++++++++++ l'entête de Gepi +++++
require_once("../lib/header.inc.php");
//++++++++++ fin entête +++++++++++
//++++++++++ le menu EdT ++++++++++
require_once("./menu.inc.php");
//++++++++++ fin du menu ++++++++++
?>
<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php
    require_once("./menu.inc.new.php");
	echo'
	<h3>Voici la liste de tous les enseignements enregistrés dans la base de Gepi</h3>
	<table class="tab_edt">
	<tbody>
	<tr>
		<th>- id-groupe -</th>
		<th>- Nom -</th>
		<th>- Description -</th>
		<th>- classes -</th>
		<th>- professeurs -</th>
	</tr>';

$req_nbr_group = mysqli_query($GLOBALS["mysqli"], "SELECT id FROM groupes");
$aff_nbr_group = mysqli_num_rows($req_nbr_group);

	for($i=0; $i<$aff_nbr_group; $i++) {
		$gr[$i]["id"] = mysql_result($req_nbr_group, $i, "id");
		$groupe_complet = get_group($gr[$i]["id"]);
    		$get_classes = mysqli_query($GLOBALS["mysqli"], "SELECT c.id, c.classe, c.nom_complet, j.priorite, j.coef, j.categorie_id FROM classes c, j_groupes_classes j WHERE (" .
                                    "c.id = j.id_classe and j.id_groupe = '" . $gr[$i]["id"] . "')");
    		$nb_classes = mysqli_num_rows($get_classes);

    		$get_profs = mysqli_query($GLOBALS["mysqli"], "SELECT u.login, u.nom, u.prenom, u.civilite FROM utilisateurs u, j_groupes_professeurs j WHERE (" .
                                "u.login = j.login and j.id_groupe = '" . $gr[$i]["id"] . "') ORDER BY u.nom, u.prenom");

    		$nb = mysqli_num_rows($get_profs);

		for ($a=0; $a<$nb_classes; $a++) {
			$c_id = $groupe_complet["classes"]["list"][$a];

		for ($b=0; $b<$nb; $b++) {
			$p_login = $groupe_complet["profs"]["list"][$b];


echo '
<tr>
	<td>'.$groupe_complet["id"].'</td>
	<td>'.$groupe_complet["name"].'</td>
	<td>'.$groupe_complet["description"].'</td>
	<td>'.$groupe_complet["classes"]["classes"][$c_id]["classe"].'</td>
	<td>'.$groupe_complet["profs"]["users"][$p_login]["nom"].' '.$groupe_complet["profs"]["users"][$p_login]["prenom"].'</td>
</tr>'."\n";
		}
		}
}
	echo '
	</tbody>
	</table>
	';
/* Fonction qui permet de lire le contenu d'un tableau multidimentionnel
function show_array($array)
{
    for (reset($array); $key = key($array), $pos = pos($array); next($array))
    {
    if(is_array($pos))
    {
        echo "$key : <UL>";
        show_array($pos);
        echo "</UL>";
    }
    else
        echo "$key = $pos <br />";
    }
}
show_array($groupe_complet);*/
?>

	</div>
<br />
<br />
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>