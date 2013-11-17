<?php
@set_time_limit(0);
/*
 * $Id$
 * MODIF: boireaus AFFICHAGE DE COMMENTAIRES...
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
};


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}

// Page bourrinée... la gestion du token n'est pas faite... et ne sera faite que si quelqu'un utilise encore ce mode d'initialisation et le manifeste sur la liste de diffusion gepi-users
check_token();

//=====================================
// AJOUT: boireaus
//$debug=1;
$debug=0;

function affiche_debug($texte){
	global $debug;
	if($debug==1){
		echo "$texte\n";
	}
}
//=====================================


//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des relations professeurs/classes/matières";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class=bold>|<a href="index.php">Retour accueil initialisation</a>|</p>
<?php

// On vérifie si l'extension d_base est active
verif_active_dbase();

echo "<center><h3 class='gepi'>Cinquième phase d'initialisation" .
		"<br />Affectation des matières à chaque professeur," .
		"<br />Affectation des professeurs dans chaque classe," .
		"<br />Importation des options suivies par les élèves" .
		"</h3></center>";

echo "<h3 class='gepi'>Première étape : affectation des matières à chaque professeur et affectation des professeurs dans chaque classe.</h3>";

if (!isset($step1)) {
    $test = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM j_groupes_professeurs"),0);
    if ($test != 0) {
        echo "<p><b>ATTENTION ...</b><br />";
        echo "Des données concernant l'affectation de professeurs dans des classes sont actuellement présentes dans la base GEPI<br /></p>";
        echo "<p>Si vous poursuivez la procédure ces données seront effacées.</p>";
        echo "<form enctype='multipart/form-data' action='prof_disc_classe.php' method=post>";
        echo "<input type=hidden name='step1' value='y' />";
        echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />";
        echo "</form>";
        die();
    }
}

if (!isset($is_posted)) {
    $del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_groupes_professeurs");
    $del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_professeurs_matieres");


    echo "<p>Importation des fichiers <b>F_men.dbf</b> et <b>F_gpd.dbf</b> contenant les données de relations entre professeurs, matière et classes.";
    echo "<form enctype='multipart/form-data' action='prof_disc_classe.php' method=post>";
    echo "<p>Veuillez préciser le nom complet du fichier <b>F_men.dbf</b>.";
    echo "<p><input type='file' size='80' name='dbf_file' />";
    echo "<p>Veuillez préciser le nom complet du fichier <b>F_gpd.dbf</b>.";
    echo "<p><input type='file' size='80' name='dbf_file2' />";
    echo "<input type='hidden' name='is_posted' value='yes' />";
    echo "<input type='hidden' name='step1' value='y' />";
    echo "<p><input type='submit' value='Valider' />";
    echo "</form>";

} else {
    $dbf_file = isset($_FILES["dbf_file"]) ? $_FILES["dbf_file"] : NULL;
    $dbf_file2 = isset($_FILES["dbf_file2"]) ? $_FILES["dbf_file2"] : NULL;
    if ((mb_strtoupper($dbf_file['name']) == "F_MEN.DBF") or (mb_strtoupper($dbf_file2['name']) == "F_GPD.DBF")) {

        $fp = @dbase_open($dbf_file['tmp_name'], 0);
        $fp2 = @dbase_open($dbf_file2['tmp_name'], 0);
        if (!$fp) {
            echo "<p>Impossible d'ouvrir le fichier F_MEN.DBF !</p>";
            @dbase_close($fp2);
            echo "<a href='prof_disc_classe.php'>Cliquer ici </a> pour recommencer !</center></p>";
        } else if (!$fp2) {
            echo "<p>Impossible d'ouvrir le fichier F_GPD.DBF !</p>";
            @dbase_close($fp);
            echo "<a href='prof_disc_classe.php'>Cliquer ici </a> pour recommencer !</center></p>";
        } else {
            // on constitue le tableau des champs à extraire dans $fp2
            $tabchamps2 = array("GROCOD","DIVCOD");
            $nblignes2 = dbase_numrecords($fp2); //number of rows
            if (@dbase_get_record_with_names($fp2,1)) {
                $temp = @dbase_get_record_with_names($fp2,1);
            } else {
                echo "<p>Le fichier F_GPD.DBF sélectionné n'est pas valide !<br />";
                echo "<a href='prof_disc_classe.php'>Cliquer ici </a> pour recommencer !</center></p>";
                die();
            }
            $nb = 0;
            foreach($temp as $key => $val){
                $en_tete[$nb] = "$key";
		affiche_debug("\$en_tete[$nb]=$en_tete[$nb]<br />\n");
                $nb++;
            }
	    affiche_debug("==========================<br />\n");
            // On range dans tabindice les indices des champs retenus
	    // On repère l'indice des colonnes GROCOD et DIVCOD
            for ($k = 0; $k < count($tabchamps2); $k++) {
                for ($i = 0; $i < count($en_tete); $i++) {
                    if ($en_tete[$i] == $tabchamps2[$k]) {
                        $tabindice2[] = $i;
			affiche_debug("\$tabindice2[]=$i<br />\n");
                    }
                }
            }
	    affiche_debug("==========================<br />\n");
            for($k = 1; ($k < $nblignes2+1); $k++){
	    	// Pour chaque ligne du fichier F_GPD, on récupère dans $affiche[0] le GROCOD et dans $affiche[1] le DIVCOD
                $ligne = dbase_get_record($fp2,$k);
                for($i = 0; $i < count($tabchamps2); $i++) {
                    $affiche[$i] = dbase_filter(trim($ligne[$tabindice2[$i]]));
		    affiche_debug("\$affiche[$i]=$affiche[$i]<br />\n");
                }
                $tab_groupe[$affiche[0]] = $affiche[1];
		affiche_debug("\$tab_groupe[\$affiche[0]]=\$tab_groupe[$affiche[0]]=".$tab_groupe[$affiche[0]]."<br />\n");
		//=======================================================
                // AJOUT: boireaus
		$tab_groupe2[$affiche[0]][] = $affiche[1];
		affiche_debug("\$tab_groupe2[\$affiche[0]][]=\$tab_groupe2[$affiche[0]][]=".$affiche[1]."<br />\n");
		//=======================================================
            }
            dbase_close($fp2);
	    // Jusque là, on s'est arrangé pour renseigner un tableau du type:
	    // $tab_groupe[GROCOD] = DIVCOD;
	    // Du coup, on ne récupère qu'une seule des classes... la dernière de la liste des classes/membres du groupe.
	    // Corrigé avec le tab_groupe2
	    affiche_debug("=======================================================<br />\n");

            // on range les classes existantes dans un tableau:
            $req = mysqli_query($GLOBALS["mysqli"], "select id, classe from classes");
            $nb_classes = mysqli_num_rows($req);
            $n = 0;

            // on constitue le tableau des champs à extraire
            $tabchamps = array("MATIMN","NUMIND","ELSTCO");
            $nblignes = dbase_numrecords($fp); //number of rows
            if (@dbase_get_record_with_names($fp,1)) {
                $temp = @dbase_get_record_with_names($fp,1);
            } else {
                echo "<p>Le fichier sélectionné n'est pas valide !<br />";
                echo "<a href='prof_disc_classe.php'>Cliquer ici </a> pour recommencer !</center></p>";
                die();
            }

            $nb = 0;
            foreach($temp as $key => $val){
                $en_tete[$nb] = "$key";
		affiche_debug("\$en_tete[$nb]=$en_tete[$nb]<br />\n");
                $nb++;
            }
     affiche_debug("==========================<br />\n");

            // On range dans tabindice les indices des champs retenus
            for ($k = 0; $k < count($tabchamps); $k++) {
                for ($i = 0; $i < count($en_tete); $i++) {
                    if ($en_tete[$i] == $tabchamps[$k]) {
                        $tabindice[] = $i;
			affiche_debug("\$tabindice[]=$i<br />\n");
                    }
                }
            }
     affiche_debug("==========================<br />\n");

            $nb_reg_no = 0;
            for($k = 1; ($k < $nblignes+1); $k++){
                $ligne = dbase_get_record($fp,$k);
                for($i = 0; $i < count($tabchamps); $i++) {
                    $affiche[$i] = dbase_filter(trim($ligne[$tabindice[$i]]));
		    affiche_debug("\$affiche[$i]=dbase_filter(trim(\$ligne[$tabindice[$i]]))=$affiche[$i]<br />\n");
                }
  affiche_debug("==========================<br />\n");
                $req = mysqli_query($GLOBALS["mysqli"], "select col1 from tempo2 where col2 = '$affiche[1]'");
		affiche_debug("On recherche si un prof assure le cours correspondant au groupe: select col1 from tempo2 where col2 = '$affiche[1]'<br />\n");
                $login_prof = @mysql_result($req, 0, 'col1');
                if ($login_prof != '') {
                    // On relie les profs aux matières
		    affiche_debug("Un (au moins) prof trouvé: $login_prof<br />\n");
                    $verif = mysqli_query($GLOBALS["mysqli"], "select id_professeur from j_professeurs_matieres where (id_matiere='$affiche[0]' and id_professeur='$login_prof')");
		    affiche_debug("select id_professeur from j_professeurs_matieres where (id_matiere='$affiche[0]' and id_professeur='$login_prof')<br />\n");
                    $resverif = mysqli_num_rows($verif);
                    if($resverif == 0) {
                        $req = mysqli_query($GLOBALS["mysqli"], "insert into j_professeurs_matieres set id_matiere='$affiche[0]', id_professeur='$login_prof', ordre_matieres=''");
			affiche_debug("insert into j_professeurs_matieres set id_matiere='$affiche[0]', id_professeur='$login_prof', ordre_matieres=''<br />\n");
                        if(!$req) $nb_reg_no++;
                    }

                    // On relie prof, matières et classes dans un nouveau groupe de Gepi

		    // On vide le tableau de la liste des classes associées au groupe:
		    unset($tabtmp);

                    $test = mysqli_query($GLOBALS["mysqli"], "select id from classes where classe='$affiche[2]'");
		    // On initialise le tableau pour que par défaut il contienne $affiche[2] au cas où ce serait une classe...
		    $tabtmp[0]=$affiche[2];
		    affiche_debug("select id from classes where classe='$affiche[2]'<br />\n");
                    $nb_test = mysqli_num_rows($test) ;
                    if ($nb_test == 0) {
                        // dans ce cas, $affiche[2] désigne un groupe
                        // on convertit le groupe en classe
/*
                        $affiche[2] = $tab_groupe[$affiche[2]];
			echo "\$affiche[2] = \$tab_groupe[\$affiche[2]] = \$tab_groupe[$affiche[2]] = $affiche[2];<br />\n";
                        $test = mysql_query("select id from classes where classe='$affiche[2]'");
			echo "select id from classes where classe='$affiche[2]'<br />\n";
*/
			// MODIF: boireaus
			// On modifie/remplit le tableau $tabtmp avec la liste des classes associées au groupe.
			for($i=0;$i<count($tab_groupe2[$affiche[2]]);$i++){
				$tabtmp[$i]=$tab_groupe2[$affiche[2]][$i];
				affiche_debug("\$tabtmp[$i]=$tabtmp[$i]<br />\n");
			}
                    }
		    // On boucle sur la liste des classes:
		    // On initialise un témoin pour ne pas recréer le groupe pour la deuxième, troisième,... classe:
		    $temoin_groupe_deja_cree="non";
		    for($i=0;$i<count($tabtmp);$i++){
	                $test = mysqli_query($GLOBALS["mysqli"], "select id from classes where classe='$tabtmp[$i]'");

			$id_classe = @mysql_result($test,0,'id');
			affiche_debug("select id from classes where classe='$tabtmp[$i]' donne \$id_classe=$id_classe<br />\n");

			if ($id_classe != '') {
				$verif = mysqli_query($GLOBALS["mysqli"], "select g.id from " .
						"groupes g, j_groupes_matieres jgm, j_groupes_professeurs jgp, j_groupes_classes jgc " .
						"where (" .
						"g.id = jgm.id_groupe and " .
						"jgm.id_matiere='$affiche[0]' and " .
						"jgm.id_groupe = jgp.id_groupe and " .
						"jgp.login = '$login_prof' and " .
						"jgp.id_groupe = jgc.id_groupe and " .
						"jgc.id_classe='$id_classe')");
				affiche_debug("select g.id from " .
						"groupes g, j_groupes_matieres jgm, j_groupes_professeurs jgp, j_groupes_classes jgc " .
						"where (" .
						"g.id = jgm.id_groupe and " .
						"jgm.id_matiere='$affiche[0]' and " .
						"jgm.id_groupe = jgp.id_groupe and " .
						"jgp.login = '$login_prof' and " .
						"jgp.id_groupe = jgc.id_groupe and " .
						"jgc.id_classe='$id_classe')<br />\n");
				$resverif = mysqli_num_rows($verif);
				if($resverif == 0) {

					// Avant d'enregistrer, il faut quand même vérifier si le groupe existe déjà ou pas
					// ... pour cette classe...
					$verif2 = mysqli_query($GLOBALS["mysqli"], "select g.id from " .
						"groupes g, j_groupes_matieres jgm, j_groupes_classes jgc " .
						"where (" .
						"g.id = jgm.id_groupe and " .
						"jgm.id_matiere='$affiche[0]' and " .
						"jgm.id_groupe = jgc.id_groupe and " .
						"jgc.id_classe='$id_classe')");
					affiche_debug("select g.id from " .
						"groupes g, j_groupes_matieres jgm, j_groupes_classes jgc " .
						"where (" .
						"g.id = jgm.id_groupe and " .
						"jgm.id_matiere='$affiche[0]' and " .
						"jgm.id_groupe = jgc.id_groupe and " .
						"jgc.id_classe='$id_classe')<br />\n");
					$resverif2 = mysqli_num_rows($verif2);

					if ($resverif2 == 0) {
						affiche_debug("Le groupe n'existe pas encore pour la classe \$id_classe=$id_classe<br />\n");

						// ordre d'affichage par défaut :
						$priority = sql_query("select priority from matieres where matiere='".$affiche[0]."'");
						if ($priority == "-1") $priority = "0";

						$matiere_nom = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT nom_complet FROM matieres WHERE matiere = '" . $affiche[0] . "'"), 0);
						if($temoin_groupe_deja_cree=="non"){
							$res = mysqli_query($GLOBALS["mysqli"], "insert into groupes set name = '" . $affiche[0] . "', description = '" . $matiere_nom . "', recalcul_rang = 'y'");
							affiche_debug("insert into groupes set name = '" . $affiche[0] . "', description = '" . $matiere_nom . "', recalcul_rang = 'y'<br />\n");
							$group_id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
							$temoin_groupe_deja_cree=$group_id;

							$res2 = mysqli_query($GLOBALS["mysqli"], "insert into j_groupes_matieres set id_groupe = '" . $group_id . "', id_matiere = '" . $affiche[0] . "'");
							affiche_debug("insert into j_groupes_matieres set id_groupe = '" . $group_id . "', id_matiere = '" . $affiche[0] . "'<br />\n");

							$res4 = mysqli_query($GLOBALS["mysqli"], "insert into j_groupes_professeurs set id_groupe = '" . $group_id . "', login ='" . $login_prof . "'");
							affiche_debug("insert into j_groupes_professeurs set id_groupe = '" . $group_id . "', login ='" . $login_prof . "'<br />\n");
						}
						else{
							$group_id=$temoin_groupe_deja_cree;
							affiche_debug("Groupe déjà créé avec \$group_id=$group_id<br />");
						}


						$res3 = mysqli_query($GLOBALS["mysqli"], "insert into j_groupes_classes set id_groupe = '" . $group_id . "', id_classe = '" . $id_classe . "', priorite = '" . $priority . "', coef = '0'");
						affiche_debug("insert into j_groupes_classes set id_groupe = '" . $group_id . "', id_classe = '" . $id_classe . "', priorite = '" . $priority . "', coef = '0'<br />\n");
						//$res4 = mysql_query("insert into j_groupes_professeurs set id_groupe = '" . $group_id . "', login ='" . $login_prof . "'");
						//echo "insert into j_groupes_professeurs set id_groupe = '" . $group_id . "', login ='" . $login_prof . "'<br />\n";

						// On ajoute tous les élèves de la classe considérée aux groupes. On enlèvera ceux qui ne suivent pas les enseignements
						// à la prochaine étape

						$get_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT distinct(login) FROM j_eleves_classes WHERE id_classe = '" . $id_classe . "'");
						$nb_eleves = mysqli_num_rows($get_eleves);
						affiche_debug("\$nb_eleves=$nb_eleves<br />\n");
						$nb_per = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM periodes WHERE id_classe = '" . $id_classe . "'"), 0);
						affiche_debug("\$nb_per=$nb_per<br />\n");

						// DEBUG :: echo "<br/>Classe : " . $id_classe . "<br/>Nb el. : " . $nb_eleves . "<br/>Nb per.: " . $nb_per . "<br/><br/>";
						for ($m=0;$m<$nb_eleves;$m++) {
							$e_login = mysql_result($get_eleves, $m, "login");
							for ($n=1;$n<=$nb_per;$n++) {
								$insert_e = mysqli_query($GLOBALS["mysqli"], "INSERT into j_eleves_groupes SET id_groupe = '" . $group_id . "', login = '" . $e_login . "', periode = '" . $n . "'");
								affiche_debug("INSERT into j_eleves_groupes SET id_groupe = '" . $group_id . "', login = '" . $e_login . "', periode = '" . $n . "'<br />\n");
							}
						}

					} else {
						// Si on est là, c'est que le groupe existe déjà, mais que le professeur que l'on
						// est en train de traiter n'est pas encore associé au groupe
						// C'est le cas de deux professeurs pour un même groupe/classe dans une matière.
						affiche_debug("Le groupe existe déjà pour la classe \$id_classe=$id_classe, on ajoute le professeur $login_prof au groupe:<br />\n");
						$group_id = mysql_result($verif2, 0);
						$res = mysqli_query($GLOBALS["mysqli"], "insert into j_groupes_professeurs set id_groupe = '" . $group_id . "', login ='" . $login_prof . "'");
						affiche_debug("insert into j_groupes_professeurs set id_groupe = '" . $group_id . "', login ='" . $login_prof . "'<br />\n");
					}
				}
			}
		    }
                }
		affiche_debug("===================================================<br />\n");
            }
            dbase_close($fp);

            if ($nb_reg_no != 0) {
                echo "<p>Lors de l'enregistrement des données il n'y a eu $nb_reg_no erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.";
            } else {
                echo "<p>L'importation des relations professeurs/matières et professeurs/classes dans la base GEPI a été effectuée avec succès !<br />Vous pouvez procéder à l'étape suivante d'importation des options suivies par les élèves.</p>";

            }
            echo "<center><p><a href='init_options.php'>Importer les options suivies par les élèves</a></p></center>";
        }
    } else if ((trim($dbf_file['name'])=='') or (trim($dbf_file2['name'])=='')) {
        echo "<p>Veuillez préciser les fichiers !<br />";
        echo "<a href='prof_disc_classe.php'>Cliquer ici </a> pour recommencer !</p>";

    } else {
        echo "<p>Fichier(s) sélectionné(s) non valide(s) !<br />";
        echo "<a href='prof_disc_classe.php'>Cliquer ici </a> pour recommencer !</p>";
    }
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
