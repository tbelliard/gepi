<?php
/*
 * Last modification  : 15/03/2005
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$resultat_session = resumeSession();
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
$titre_page = "Saisie des avis du conseil de classe";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// On teste si un professeur peut saisir les avis
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiRubConseilProf")!='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

// On teste si le service scolarité peut saisir les avis
if (($_SESSION['statut'] == 'scolarite') and getSettingValue("GepiRubConseilScol")!='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {
    //$call_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");

	if($_SESSION['statut']=='scolarite'){
		$call_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	}
	else{
		$call_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	}

    $nombre_classe = mysql_num_rows($call_classe);
    $j = "0";
    while ($j < $nombre_classe) {
        $id_classe = mysql_result($call_classe, $j, "id");
        $classe_suivi = mysql_result($call_classe, $j, "classe");
        echo "<br /><b>$classe_suivi</b> --- <a href='saisie_avis1.php?id_classe=$id_classe'>Saisir les avis, pour toute la classe, avec rappel des avis des autres périodes.</a>";
        echo "<br /><b>$classe_suivi</b> --- <a href='saisie_avis2.php?id_classe=$id_classe'>Saisir les avis, élève par élève, avec visualisation des résultats de l'élève.</a><br />";
        include "../lib/periodes.inc.php";
        $k="1";
        while ($k < $nb_periode) {
           if ($ver_periode[$k] != "O") {
               echo "<b>$classe_suivi</b> --- ".ucfirst($nom_periode[$k]);
               echo " --- <a href='import_app_cons.php?id_classe=$id_classe&amp;periode_num=$k'>Importer un fichier d'appréciations (format csv)</a><br />";
           }
           $k++;
        }
        $j++;
    }
} else {
    $call_prof_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)");
    $nombre_classe = mysql_num_rows($call_prof_classe);
    if ($nombre_classe == "0") {
        echo "Vous n'êtes pas ".getSettingValue("gepi_prof_suivi")." ! Il ne vous revient donc pas de saisir les avis de conseil de classe.";
    } else {
        $j = "0";
        echo "<p>Vous êtes ".getSettingValue("gepi_prof_suivi")." dans la classe de :</p>";
        while ($j < $nombre_classe) {
            $id_classe = mysql_result($call_prof_classe, $j, "id");
            $classe_suivi = mysql_result($call_prof_classe, $j, "classe");
            echo "<br />$classe_suivi --- <a href='saisie_avis1.php?id_classe=$id_classe'>Saisir les avis pour mon groupe.</a>";
            echo "<br />$classe_suivi --- <a href='saisie_avis2.php?id_classe=$id_classe'>Saisir les avis pour mon groupe, avec visualisation des résultats.</a>";
            include "../lib/periodes.inc.php";
            $k="1";
            while ($k < $nb_periode) {
               if ($ver_periode[$k] != "O") {
                   echo "<br />$classe_suivi --- ".ucfirst($nom_periode[$k]);
                   echo " --- <a href='import_app_cons.php?id_classe=$id_classe&amp;periode_num=$k'>Importer un fichier d'appréciations (format csv)</a>";
               }
               $k++;
            }
            echo "<br />";
            $j++;
        }
    }
}
require("../lib/footer.inc.php");
?>