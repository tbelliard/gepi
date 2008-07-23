<?php

/**
 * Fichiers qui permet de paramétrer les couleurs de chaque matière des emplois du temps
 *
 * @version $Id$
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

$titre_page = "Paramétrer les couleurs des matières";
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

/*/ Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}*/
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "edt_organisation/style_edt";
$utilisation_jsdivdrag = "";
//==============PROTOTYPE===============
$utilisation_prototype = "ok";
//============fin PROTOTYPE=============
// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php");
?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<h3 class="gepi">Cliquez sur la couleur pour la modifier.</h3>
<p>Pour voir ces couleurs dans les emplois du temps, il faut modifier les param&egrave;tres.</p>

<table id="edt_table_couleurs">
	<thead>
	<tr><th>Mati&egrave;re</th><th>nom court</th><th>Couleur</th></tr>
	</thead>

	<tbody>

<?php
// On affiche la liste des matières
$req_sql = mysql_query("SELECT matiere, nom_complet FROM matieres ORDER BY nom_complet");
$nbre_matieres = mysql_num_rows($req_sql);

	for($i=0; $i < $nbre_matieres; $i++){
	$aff_matiere[$i]["court"] = mysql_result($req_sql, $i, "matiere");
	$aff_matiere[$i]["long"] = mysql_result($req_sql, $i, "nom_complet");
	// On détermine la couleur choisie
	$recher_couleur = "M_".$aff_matiere[$i]["court"];
	$color = GetSettingEdt($recher_couleur);
		if ($color == "") {
			$color = "none";
		}
		// On construit le tableau
		echo '
		<tr id="M_'.$aff_matiere[$i]["court"].'">
			<td>'.$aff_matiere[$i]["long"].'</td>
			<td>'.$aff_matiere[$i]["court"].'</td>
			<td style="background-color: '.$color.';">
				<p onclick="couleursEdtAjax(\'M_'.$aff_matiere[$i]["court"].'\', \'non\');">Modifier</p>
			</td>
		</tr>
		';

	}
?>

	</tbody>

</table>
<br /><br />
	</div>
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>