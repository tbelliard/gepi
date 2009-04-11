<?php
/*
 * $Id: saisie_avis.php 2147 2008-07-23 09:01:04Z tbelliard $
 *
 * Copyright 2001, 2009 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
//**************** EN-TETE *****************
$titre_page = "Saisie des crédits ECTS";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// On teste si un professeur principal peut effectuer la saisie
if (($_SESSION['statut'] == 'professeur') and $gepiSettings["GepiAccesSaisieEctsPP"] !='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

// On teste si le service scolarité peut effectuer la saisie
if (($_SESSION['statut'] == 'scolarite') and $gepiSettings["GepiAccesSaisieEctsScolarite"] !='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {

    // On ne sélectionne que les classes qui ont au moins un enseignement ouvrant à crédits ECTS
	if($_SESSION['statut']=='scolarite'){
		$call_classe = mysql_query("SELECT DISTINCT c.*
                                    FROM classes c, periodes p, j_scol_classes jsc, j_groupes_classes jgc
                                    WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' AND c.id=jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
	}
	else{
		$call_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc WHERE p.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
	}

    $nombre_classe = mysql_num_rows($call_classe);
	if($nombre_classe==0){
		echo "<p>Aucune classe avec paramétrage ECTS ne vous est attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
	}
	else{

		$j = "0";
		$alt=1;
		while ($j < $nombre_classe) {
			$id_classe = mysql_result($call_classe, $j, "id");
			$classe_suivi = mysql_result($call_classe, $j, "classe");

			echo "<br /><b>$classe_suivi</b> --- <a href='saisie_ects.php?id_classe=$id_classe'>Saisir les crédits, élève par élève, avec visualisation des résultats de l'élève.</a><br />";


			$j++;
		}

	}
} else {
    $call_prof_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc, j_groupes_classes jgc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE)");
    $nombre_classe = mysql_num_rows($call_prof_classe);
    if ($nombre_classe == "0") {
        echo "Vous n'êtes pas ".$gepiSettings['gepi_prof_suivi']." dans des classes ayant des enseignements ouvrant droits à des ECTS.";
    } else {
        $j = "0";
        echo "<p>Vous êtes ".$gepiSettings['gepi_prof_suivi']." dans la classe de :</p>";
        while ($j < $nombre_classe) {
            $id_classe = mysql_result($call_prof_classe, $j, "id");
            $classe_suivi = mysql_result($call_prof_classe, $j, "classe");
            echo "<br /><b>$classe_suivi</b> --- <a href='saisie_ects.php?id_classe=$id_classe'>Saisir les crédits, élève par élève, avec visualisation des résultats de l'élève.</a><br />";
            $j++;
        }
    }
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>