<?php
@set_time_limit(0);
/*
 * $Id: init_options.php 5936 2010-11-21 17:32:17Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

check_token();

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année | Initialisation  des options par GEP";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php
// On vérifie si l'extension d_base est active
//verif_active_dbase();

echo "<center><h3 class='gepi'>Cinquième phase d'initialisation<br />Affectation des matières à chaque professeur,<br />Affectation des professeurs dans chaque classe,<br />Importation des options suivies par les élèves</h3></center>";

echo "<h3 class='gepi'>Deuxième étape : importation des options suivies par les élèves.</h3>";

$test1 = mysql_result(mysql_query("SELECT count(*) FROM eleves"),0);
if ($test1 ==0) {
    echo "<p class='grand'>Aucun élève actuellement dans la base : la procédure d'initialisation des options ne peut continuer !</p>";
    die();
} else {
    $test2 = mysql_result(mysql_query("SELECT count(*) FROM j_eleves_classes"),0);
    if ($test2 ==0) {
        echo "<p class='grand'>Les classes n'ont pas encore été constituées : la procédure d'initialisation des options ne peut continuer !</p>";
        die();
    } else {

        //$test3 = mysql_result(mysql_query("SELECT count(*) FROM temp_gep_import WHERE LOGIN !=''"),0);
        $test3 = mysql_result(mysql_query("SELECT count(*) FROM temp_gep_import2 WHERE LOGIN !=''"),0);
        if ($test3 ==0) {
            echo "<p class='grand'>Afin de procéder à la phase de définition des options suivies par les élèves, vous devez d'abord effectuer la première phase d'importation des élèves à partir du fichier ELEVES.CSV</p>";
            die();
        }
    }
}
//$del = @mysql_query("delete from j_eleves_groupes");

$appel_donnees_classes = mysql_query("SELECT id, classe FROM classes");
$nb_classe = mysql_num_rows($appel_donnees_classes);
$z=0;

while ($classe_row = mysql_fetch_object($appel_donnees_classes)) {
    $id_classe = $classe_row->id;
    $classe = $classe_row->classe;

	// Initialisation de la variable pour indiquer qu'un groupe n'existe pas pour la matière indiquée en option
	$no_group = array();

    $nb_per = mysql_result(mysql_query("SELECT count(*) FROM periodes WHERE id_classe = '" . $id_classe . "'"), 0);

    $nb_options = 0;
    $i = 1;
    $tempo = null;
    while ($i < 13) {
        $tempo .= "ELEOPT".$i.", ";
        $i++;
    }
    $tempo = substr($tempo, 0, -2);

    //$call_data = mysql_query("SELECT $tempo FROM temp_gep_import WHERE DIVCOD = '$classe'");
    $call_data = mysql_query("SELECT $tempo FROM temp_gep_import2 WHERE DIVCOD = '$classe'");
    $tab_options = array();
    while ($row = mysql_fetch_object($call_data)) {
    	 $i = 1;
         while ($i < 13) {
         	$tempo = "ELEOPT".$i;
            $temp = $row->$tempo;
            if ($temp!='') {
                // On s'assure de ne pas ranger dans le tableau tab_options, plusieurs fois la même option
                $n = 0;
                $double = 'no';

                if (in_array($temp, $tab_options)) {$double = 'yes';}

                if ($double == 'no') {
                    $tab_options[$nb_options] = $temp;
                    $nb_options++;
                }
            }
            $i++;
        }
    }


    $appel_donnees_eleves = mysql_query("SELECT e.login FROM eleves e, j_eleves_classes j WHERE (e.login = j.login and j.id_classe = '$id_classe')");

    while($i = mysql_fetch_object($appel_donnees_eleves)) {
        $current_eleve_login = $i->login;
        //$call_data = mysql_query("SELECT ELEOPT1,ELEOPT2,ELEOPT3,ELEOPT4,ELEOPT5,ELEOPT6,ELEOPT7,ELEOPT8,ELEOPT9,ELEOPT10,ELEOPT11,ELEOPT12 FROM temp_gep_import WHERE LOGIN = '$current_eleve_login'");
        $call_data = mysql_query("SELECT ELEOPT1,ELEOPT2,ELEOPT3,ELEOPT4,ELEOPT5,ELEOPT6,ELEOPT7,ELEOPT8,ELEOPT9,ELEOPT10,ELEOPT11,ELEOPT12 FROM temp_gep_import2 WHERE LOGIN = '$current_eleve_login'");
        while ($row = mysql_fetch_array($call_data, MYSQL_NUM)) {
	        $j="0";
	        while ($j < $nb_options) {

	            $suit_option = 'no';
	            if (in_array($tab_options[$j], $row)) {
	                $suit_option = 'yes';
	            }

	            if ($suit_option == 'no') {

                	// On commence par récupérer l'ID du groupe concerné

                	if (!in_array($tab_options[$j], $no_group)) {
	                	$group_id = @mysql_result(mysql_query("SELECT g.id FROM groupes g, j_groupes_classes jgc, j_groupes_matieres jgm where (" .
	                			"g.id = jgm.id_groupe and " .
	                			"jgm.id_matiere = '" . $tab_options[$j] . "' and " .
	                			"jgm.id_groupe = jgc.id_groupe and " .
	                			"jgc.id_classe = '" . $id_classe . "')"), 0);
	                	if (is_numeric($group_id)) {
	                    	$reg = mysql_query("DELETE FROM j_eleves_groupes WHERE (id_groupe = '" . $group_id . "' and login='". $current_eleve_login . "')");
	                    	// DEBUG :: echo "<br/>DELETED FROM GROUPE : " . $group_id;
	                	} else {
	                		$no_group[] = $tab_options[$j];
	                	}
		            }

	            }
	            $j++;
	        }
        }
    }
}

//echo "<p>L'importation des options suivies par les élèves dans la base GEPI a été effectuée avec succès !<br />Vous pouvez procéder à l'étape suivante de nettoyage des tables GEPI.</p>\n";
//echo "<center><p><a href='clean_tables.php'>Suppression des données inutiles</a></p></center>\n";
echo "<p>L'importation des options suivies par les élèves dans la base GEPI a été effectuée avec succès !<br />Vous pouvez procéder à l'étape suivante d'import des professeurs principaux.</p>\n";
echo "<center><p><a href='init_pp.php'>Import des professeurs principaux</a></p></center>\n";
require("../lib/footer.inc.php");
?>