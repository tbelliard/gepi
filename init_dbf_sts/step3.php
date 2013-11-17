<?php
@set_time_limit(0);
/*
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

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des élèves - Etape 3";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

// On vérifie si l'extension d_base est active
verif_active_dbase();

//==================================
// RNE de l'établissement pour comparer avec le RNE de l'établissement de l'année précédente
$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
//==================================

echo "<center><h3 class='gepi'>Première phase d'initialisation<br />Importation des élèves,  constitution des classes et affectation des élèves dans les classes</h3></center>";
echo "<center><h3 class='gepi'>Troisième étape : Enregistrement des élèves et affectation des élèves dans les classes</h3></center>";

if (isset($is_posted) and ($is_posted == "yes")) {
    $call_data = mysqli_query($GLOBALS["mysqli"], "SELECT ID_TEMPO,ELENOM,ELEPRE,ELENOET,ERENO,ELESEXE,ELEDATNAIS,ELEDOUBL,ELENONAT,ELEREG,DIVCOD,ETOCOD_EP FROM temp_gep_import ORDER BY DIVCOD,ELENOM,ELEPRE");
    $nb = mysqli_num_rows($call_data);
    $i = "0";
    while ($i < $nb) {
        $req = mysqli_query($GLOBALS["mysqli"], "select col2 from tempo2 where col1 = '$i'");
        $reg_login = @mysql_result($req, 0, 'col2');


        $id_tempo = @mysql_result($call_data, $i, "ID_TEMPO");
        $no_gep = @mysql_result($call_data, $i, "ELENONAT");
        $reg_nom = traitement_magic_quotes(corriger_caracteres(@mysql_result($call_data, $i, "ELENOM")));
        $reg_prenom = @mysql_result($call_data, $i, "ELEPRE");
        $reg_elenoet = @mysql_result($call_data, $i, "ELENOET");
        $reg_ereno = @mysql_result($call_data, $i, "ERENO");
        $reg_sexe = @mysql_result($call_data, $i, "ELESEXE");
        $reg_naissance = @mysql_result($call_data, $i, "ELEDATNAIS");
        $reg_doublant = @mysql_result($call_data, $i, "ELEDOUBL");
        $reg_classe = @mysql_result($call_data, $i, "DIVCOD");
        $reg_etab = @mysql_result($call_data, $i, "ETOCOD_EP");
        $tab_prenom = explode(" ",$reg_prenom);
        $reg_prenom = traitement_magic_quotes(corriger_caracteres($tab_prenom[0]));
        $reg_regime = mysql_result($call_data, $i, "ELEREG");
        if (($reg_sexe != "M") and ($reg_sexe != "F")) {$reg_sexe = "M";}
        if ($reg_naissance == '') {$reg_naissance = "19000101";}
        $maj_tempo = mysqli_query($GLOBALS["mysqli"], "UPDATE temp_gep_import SET LOGIN='$reg_login' WHERE ID_TEMPO='$id_tempo'");
        $reg_eleve = mysqli_query($GLOBALS["mysqli"], "INSERT INTO eleves SET no_gep='$no_gep',login='$reg_login',nom='$reg_nom',prenom='$reg_prenom',sexe='$reg_sexe',naissance='$reg_naissance',elenoet='$reg_elenoet',ereno='$reg_ereno'");
        if (!$reg_eleve) echo "<p>Erreur lors de l'enregistrement de l'élève $reg_nom $reg_prenom.";

        if ($reg_regime == "0") {$regime = "ext.";}
        if ($reg_regime == "2") {$regime = "d/p";}
        if ($reg_regime == "3") {$regime = "int.";}
        if ($reg_regime == "4") {$regime = "i-e";}
        if (($reg_regime != "0") and ($reg_regime != "4") and ($reg_regime != "2") and ($reg_regime != "3")) {$regime = "d/p";}
        if ($reg_doublant == "O") {$doublant = 'R';}
        if ($reg_doublant != "O") {$doublant = '-';}

        $register = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_regime SET login='$reg_login',regime='$regime',doublant='$doublant'");
        if (!$register) echo "<p>Erreur lors de l'enregistrement des infos de régime pour l'élève $reg_nom $reg_prenom.";

        $call_classes = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes");
        $nb_classes = mysqli_num_rows($call_classes);
        $j = 0;
        while ($j < $nb_classes) {
            $classe = mysql_result($call_classes, $j, "classe");
            if ($reg_classe == $classe) {
                $id_classe = mysql_result($call_classes, $j, "id");
                $number_periodes = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM periodes WHERE id_classe='$id_classe'"),0);
                $u = 1;
                while ($u <= $number_periodes) {
                    $reg = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_classes SET login='$reg_login',id_classe='$id_classe',periode='$u', rang='0'");
                    if (!$reg) echo "<p>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à la classe $classe pour la période $u";
                    $u++;
                }
            }
            $j++;
        }

        //if ($reg_etab != '') {
        if (($reg_etab != '')&&($reg_elenoet != '')) {
            //$register = mysql_query("INSERT INTO j_eleves_etablissements SET id_eleve='$reg_login',id_etablissement='$reg_etab'");
            //if (!$register) echo "<p>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à l'établissement $reg_etab.</p>\n";

			if($gepiSchoolRne!="") {
				if($gepiSchoolRne!=$reg_etab) {
					$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$reg_elenoet';";
					$test_etab=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test_etab)==0){
						$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$reg_elenoet', id_etablissement='$reg_etab';";
						$insert_etab=mysqli_query($GLOBALS["mysqli"], $sql);
						if (!$insert_etab) {
							echo "<p>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à l'établissement $reg_etab.</p>\n";
						}
					}
					else {
						$sql="UPDATE j_eleves_etablissements SET id_etablissement='$reg_etab' WHERE id_eleve='$reg_elenoet';";
						$update_etab=mysqli_query($GLOBALS["mysqli"], $sql);
						if (!$update_etab) {
							echo "<p>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à l'établissement $reg_etab.</p>\n";
						}
					}
				}
			}
			else {
				// Si le RNE de l'établissement courant (celui du GEPI) n'est pas renseigné, on insère les nouveaux enregistrements, mais on ne met pas à jour au risque d'écraser un enregistrement correct avec l'info que l'élève de 1ère était en 2nde dans le même établissement.
				// Il suffira de faire un
				//       DELETE FROM j_eleves_etablissements WHERE id_etablissement='$gepiSchoolRne';
				// une fois le RNE renseigné.
				$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$reg_elenoet';";
				$test_etab=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test_etab)==0){
					$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$reg_elenoet', id_etablissement='$reg_etab';";
					$insert_etab=mysqli_query($GLOBALS["mysqli"], $sql);
					if (!$insert_etab) {
						echo "<p>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à l'établissement $reg_etab.</p>\n";
					}
				}
			}

        }

        $i++;
    }
    // on vide la table tempo2 qui nous a servi à stocker les login temporaires des élèves
    $del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM tempo2");

    echo "<p>L'importation des données de <b>GEP</b> concernant la constitution des classes est terminée.</p>";
    echo "<center><p><a href='responsables.php'>Procéder à la deuxième phase d'importation des responsables</a></p></center>";

} else {
    // on vide la table tempo2 qui va nous servir à stocker les login temporaires des élèves
    $del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM tempo2");

    $call_data = mysqli_query($GLOBALS["mysqli"], "SELECT ELENOM,ELEPRE,ELENOET,ERENO,ELESEXE,ELEDATNAIS,ELEDOUBL,ELENONAT,ELEREG,DIVCOD,ETOCOD_EP FROM temp_gep_import ORDER BY DIVCOD,ELENOM,ELEPRE");
    $nb = mysqli_num_rows($call_data);
    $i = "0";
    echo "<p>Le tableau suivant affiche les données qui vont être enregistrées dans la base de donnée GEPI lorsque vous aurez confirmé ce choix tout en bas de la page.<br /><b>Tant que vous n'avez pas validé en bas de la page, aucune donnée n'est enregistrée !</b></p>";
    echo "<p>Les valeurs en rouge signalent d'éventuelles données manquantes (ND pour \"non défini\") dans le fichier <b>F_ELE.DBF</b> ! Ceci n'est pas génant pour l'enregistrement dans la base <b>GEPI</b>. Vous aurez en effet la possibilité de compléter les données manquantes avec les outils fournis dans <b>GEPI</b></p>";
    echo "<p>Une fois cette page entièrement chargée, ce qui peut prendre un peu de temps, <b>veuillez lire attentivement les remarques en bas de la page </b>avant de procéder à l'enregistrement définitif des données</p>";
    echo "<table border=1 cellpadding=2 cellspacing=2>";
    echo "<tr><td><p class=\"small\">N° GEP</p></td><td><p class=\"small\">Identifiant</p></td><td><p class=\"small\">Nom</p></td><td><p class=\"small\">Prénom</p></td><td><p class=\"small\">Sexe</p></td><td><p class=\"small\">Date de naiss.</p></td><td><p class=\"small\">Régime</p></td><td><p class=\"small\">Doublant</p></td><td><p class=\"small\">Classe</p></td><td><p class=\"small\">Etablissement d'origine</p></td></tr>";
    $max_lignes_pb = 0;
    while ($i < $nb) {
        $ligne_pb = 'no';
        $no_gep = mysql_result($call_data, $i, "ELENONAT");
        $reg_nom = mysql_result($call_data, $i, "ELENOM");
        $reg_prenom = mysql_result($call_data, $i, "ELEPRE");
        $reg_elenoet = mysql_result($call_data, $i, "ELENOET");
        $reg_ereno = mysql_result($call_data, $i, "ERENO");
        $reg_sexe = mysql_result($call_data, $i, "ELESEXE");
        $reg_naissance = mysql_result($call_data, $i, "ELEDATNAIS");
        $reg_doublant = mysql_result($call_data, $i, "ELEDOUBL");
        $reg_classe = mysql_result($call_data, $i, "DIVCOD");
        $reg_etab = mysql_result($call_data, $i, "ETOCOD_EP");
        $tab_prenom = explode(" ",$reg_prenom);
        $reg_prenom = $tab_prenom[0];
        $reg_regime = mysql_result($call_data, $i, "ELEREG");
        if ($no_gep != '') {
            $no_gep_aff = $no_gep;
        } else {
            $no_gep_aff = "<font color = 'red'>ND</font>";
            $ligne_pb = 'yes';
        }

        // On teste pour savoir s'il faut créer un login
        $nouv_login='no';
        if ($no_gep != '') {
/*            $test1 = mysql_num_rows(mysql_query("SELECT login FROM a1_eleves WHERE (no_gep='$no_gep')"));
            $test2 = mysql_num_rows(mysql_query("SELECT login FROM a2_eleves WHERE (no_gep='$no_gep')"));
            $test3 = mysql_num_rows(mysql_query("SELECT login FROM a3_eleves WHERE (no_gep='$no_gep')"));
            $test4 = mysql_num_rows(mysql_query("SELECT login FROM a4_eleves WHERE (no_gep='$no_gep')"));
            $test5 = mysql_num_rows(mysql_query("SELECT login FROM a5_eleves WHERE (no_gep='$no_gep')"));
            $test6 = mysql_num_rows(mysql_query("SELECT login FROM a6_eleves WHERE (no_gep='$no_gep')"));
            if (($test1 == "0") and ($test2 == "0") and ($test3 == "0") and ($test4 == "0") and ($test5 == "0") and ($test6 == "0")) {
*/
                $nouv_login = 'yes';
/*
            } else {
                if ($test1 != "0") {
                    $query_login = mysql_query("SELECT login FROM a1_eleves WHERE (no_gep='$no_gep')");
                    $login_eleve = mysql_result($query_login, 0, 'login');
                } else if ($test2 != "0") {
                    $query_login = mysql_query("SELECT login FROM a2_eleves WHERE (no_gep='$no_gep')");
                    $login_eleve = mysql_result($query_login, 0, 'login');
                } else if ($test3 != "0") {
                    $query_login = mysql_query("SELECT login FROM a3_eleves WHERE (no_gep='$no_gep')");
                    $login_eleve = mysql_result($query_login, 0, 'login');
                } else if ($test4 != "0") {
                    $query_login = mysql_query("SELECT login FROM a4_eleves WHERE (no_gep='$no_gep')");
                    $login_eleve = mysql_result($query_login, 0, 'login');
                } else if ($test5 != "0") {
                    $query_login = mysql_query("SELECT login FROM a5_eleves WHERE (no_gep='$no_gep')");
                    $login_eleve = mysql_result($query_login, 0, 'login');
                } else {
                    $query_login = mysql_query("SELECT login FROM a6_eleves WHERE (no_gep='$no_gep')");
                    $login_eleve = mysql_result($query_login, 0, 'login');
                }
                // Il s'agit d'un élève figurant déjà dans une des bases élève des années passées.
                // Dans ce cas, on utilise le login existant
            }
*/
        }
        // S'il s'agit d'un élève ne figurant pas déjà dans une des bases élève des années passées,
        // on crée un login !

        if (($no_gep == '') or ($nouv_login=='yes')) {
            $temp1 = strtoupper($reg_nom);
            $temp1 = strtr($temp1, " '-", "___");
            $temp1 = mb_substr($temp1,0,7);
            $temp2 = strtoupper($reg_prenom);
            $temp2 = strtr($temp2, " '-", "___");
            $temp2 = mb_substr($temp2,0,1);
            $login_eleve = $temp1.'_'.$temp2;

            // On teste l'unicité du login que l'on vient de créer
            $k = 2;
            $test_unicite = 'no';
            $temp = $login_eleve;
            while ($test_unicite != 'yes') {
                $test_unicite = test_unique_e_login($login_eleve,$i);
                if ($test_unicite != 'yes') {
                    $login_eleve = $temp.$k;
                    $k++;
                }
            }
        }

        if ($reg_nom != '') {
            $reg_nom_aff = $reg_nom;
        } else {
            $reg_nom_aff = "<font color = 'red'>ND</font>";
            $ligne_pb = 'yes';
        }
        if ($reg_prenom != '') {
            $reg_prenom_aff = $reg_prenom;
        } else {
            $reg_prenom_aff = "<font color = 'red'>ND</font>";
            $ligne_pb = 'yes';
        }
        if (($reg_sexe == "M") or ($reg_sexe == "F")) {
            $reg_sexe_aff = $reg_sexe;
        } else {
            $reg_sexe_aff = "<font color = 'red'>ND</font>";
            $ligne_pb = 'yes';
        }
        if ($reg_naissance != '') {
            $eleve_naissance_annee = mb_substr($reg_naissance, 0, 4);
            $eleve_naissance_mois = mb_substr($reg_naissance, 4, 2);
            $eleve_naissance_jour = mb_substr($reg_naissance, 6, 2);
            $naissance = $eleve_naissance_jour."/".$eleve_naissance_mois."/".$eleve_naissance_annee;
        } else {
            $naissance = 'non définie';
        }

        if ($reg_regime == "0") {
            $reg_regime_aff = "ext.";
        } else if ($reg_regime == "4") {
            $reg_regime_aff = "i-e";
        } else if ($reg_regime == "2") {
            $reg_regime_aff = "d/p";
        } else if ($reg_regime == "3") {
            $reg_regime_aff = "int.";
        } else {
            $reg_regime_aff = "<font color = 'red'>ND</font>";
            $ligne_pb = 'yes';
        }

        if ($reg_doublant == "N") {
            $reg_doublant_aff = "N";
        } else if ($reg_doublant == "O") {
            $reg_doublant_aff = "O";
        } else {
            $reg_doublant_aff = "<font color = 'red'>ND</font>";
            $ligne_pb = 'yes';
        }

        $call_classes = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes");
        $nb_classes = mysqli_num_rows($call_classes);
        $j = 0;
        $classe_error = 'yes';
        while ($j < $nb_classes) {
            $classe = mysql_result($call_classes, $j, "classe");
            if ($reg_classe == $classe) {
                $classe_aff = $classe;
                $classe_error = 'no';
            }
            $j++;
        }
        if ($classe_error == 'yes') {
            $classe_aff = "<font color = 'red'>ND</font>";
            $ligne_pb = 'yes';
        }
        if ($reg_etab != '') {
            $calletab = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM etablissements WHERE (id = '$reg_etab')");
            $result_etab = mysqli_num_rows($calletab);
            if ($result_etab != 0) {
                $etab_nom = @mysql_result($calletab, 0, "nom");
                $etab_cp = @mysql_result($calletab, 0, "cp");
                $etab_ville = @mysql_result($calletab, 0, "ville");
                $reg_etab_aff = "$etab_nom, $etab_cp $etab_ville";
            } else {
                $reg_etab_aff = "<font color = 'red'>RNE : $reg_etab, étab. non répertorié</font>";
                $ligne_pb = 'yes';
            }
        } else {
            $reg_etab_aff = "<font color = 'red'>ND</font>";
            $ligne_pb = 'yes';
        }
        if (!isset($affiche)) $affiche = 'tout';
        // On affiche la ligne du tableau
        if (($affiche != 'partiel') or (($affiche == 'partiel') and ($ligne_pb == 'yes'))) {
            echo "<tr><td><p class=\"small\">$no_gep_aff</p></td>";
            echo "<td><p class=\"small\">$login_eleve</p></td>";
            echo "<td><p class=\"small\">$reg_nom_aff</p></td>";
            echo "<td><p class=\"small\">$reg_prenom_aff</p></td>";
            echo "<td><p class=\"small\">$reg_sexe_aff</p></td>";
            echo "<td><p class=\"small\">$naissance</p></td>";
            echo "<td><p class=\"small\">$reg_regime_aff</p></td>";
            echo "<td><p class=\"small\">$reg_doublant_aff</p></td>";
            echo "<td><p class=\"small\">$classe_aff</p></td>";
            echo "<td><p class=\"small\">$reg_etab_aff</p></td></tr>";
        }

        // Si la ligne comportait un problème, on incrémente max_lignes_pb
        if ($ligne_pb == 'yes') {
            $max_lignes_pb++;
        }
        $i++;

    }
    echo "</table>";
    echo "<p><b>Nombre total de lignes : $nb</b><br />";
    if ($max_lignes_pb == 0) {
        echo "Aucune erreur n'a été détectée !</p>";
    } else {
        echo "Des données manquantes ou incomplètes ont été détectées dans <b>$max_lignes_pb lignes</b> : Elles apparaissent dans le tableau ci-dessus en rouge !";
        if ($affiche != 'partiel') {
            echo "<p>--> Pour n'afficher que les lignes ou des problèmes ont été détectés, cliquez sur le bouton \"Affichage partiel\" :</p>";
            echo "<form enctype='multipart/form-data' action='step3.php' method=post>";
            echo "<input type=hidden name='is_posted' value='no'>";
            echo "<input type=hidden name='affiche' value='partiel'>";
            echo "<center><input type='submit' value='Affichage partiel'></center>";
            echo "</form>";
        } else {
            echo "<p>--> Pour afficher toutes les lignes, cliquez sur le bouton \"Afficher tout\" :</p>";
            echo "<form enctype='multipart/form-data' action='step3.php' method=post>";
            echo "<input type=hidden name='is_posted' value='no'>";
            echo "<input type=hidden name='affiche' value='tout'>";
            echo "<center><input type='submit' value='Afficher tout'></center>";
            echo "</form>";
        }
    }
    echo "<p>--> Pour Enregistrer toutes les données dans la base <b>GEPI</b>, cliquez sur le bouton \"Enregistrer\" !</p>";
    echo "<form enctype='multipart/form-data' action='step3.php' method=post>";
    echo "<input type=hidden name='is_posted' value='yes'>";
    echo "<center><input type='submit' value='Enregistrer'></center>";
    echo "</form>";
	require("../lib/footer.inc.php");
}
?>