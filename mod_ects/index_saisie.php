<?php
/*
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
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

// On va initialiser des marqueurs qui simplifieront les conditions par la suite
$acces_prof_suivi = false;
$acces_prof = false;
$acces_scol = false;

$prof_suivi = sql_count(sql_query("SELECT professeur FROM j_eleves_professeurs  WHERE professeur = '".$_SESSION['login']."'")) != "0" ? true : false;

if (($_SESSION['statut'] == 'professeur') && $gepiSettings["GepiAccesSaisieEctsPP"] =='yes' && $prof_suivi) {
  $acces_prof_suivi = true;
}
if (($_SESSION['statut'] == 'professeur') && $gepiSettings["GepiAccesSaisieEctsProf"] =='yes') {
  $acces_prof = true;
}
if ((($_SESSION['statut'] == 'scolarite') and $gepiSettings["GepiAccesSaisieEctsScolarite"] =='yes') or $_SESSION['statut'] == 'secours') {
  $acces_scol = true;
}

if (!$acces_prof_suivi && !$acces_prof && !$acces_scol) {
  die("Droits insuffisants pour accéder à cette page.");
}


if ($acces_scol) {

  echo "<p>Accès pour saisie complètes de crédits ECTS. Sélectionnez la classe pour laquelle vous souhaitez réaliser la saisie :</p>";
    // On ne sélectionne que les classes qui ont au moins un enseignement ouvrant à crédits ECTS
	if($_SESSION['statut']=='scolarite'){
		$call_classe = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT c.*
                                    FROM classes c, periodes p, j_scol_classes jsc, j_groupes_classes jgc
                                    WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' AND c.id=jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
	}
	else{
		$call_classe = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc WHERE p.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
	}

    $nombre_classe = mysqli_num_rows($call_classe);
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
}

if ($acces_prof_suivi) {
    echo "<br/>";
    echo "<p>Accès pour saisie définitive et complète des crédits ECTS. Sélectionnez la classe pour laquelle vous souhaitez réaliser la saisie :</p>";
    $call_prof_classe = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc, j_groupes_classes jgc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE)");
    $nombre_classe = mysqli_num_rows($call_prof_classe);
    
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

if ($acces_prof) {
    echo "<br/>";
    echo "<p>Accès à l'interface de pré-saisie des crédits ECTS pour les enseignements dont vous êtes responsable :</p>";
    $call_prof_classe = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT c.* FROM classes c, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE
        (jgp.login = '" . $_SESSION['login'] . "' AND jgc.id_groupe = jgp.id_groupe AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE)");
    $nombre_classe = mysqli_num_rows($call_prof_classe);
    if ($nombre_classe == "0") {
        echo "<p>Aucun enseignement dont vous êtes responsables n'ouvre droit à crédits ECTS.</p>";
    } else {
        $j = "0";
        while ($j < $nombre_classe) {
            $id_classe = mysql_result($call_prof_classe, $j, "id");
            $classe_suivi = mysql_result($call_prof_classe, $j, "classe");
            echo "<br /><b>$classe_suivi</b> --- <a href='saisie_ects.php?mode=presaisie&id_classe=$id_classe'>Saisir les crédits, élève par élève, avec visualisation des résultats de l'élève.</a><br />";
            $j++;
        }
    }
}



echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
