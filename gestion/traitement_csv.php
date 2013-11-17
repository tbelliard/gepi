<?php

/*


 *

 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun

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

extract($_GET, EXTR_OVERWRITE);
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
$titre_page = "Outil de gestion | Traitement Importation";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

$nb_row++;
for ($row=1; $row<$nb_row; $row++) {
    $temp = "reg_".$row."_etab";
    $reg_etab = isset($_POST[$temp])?$_POST[$temp]:NULL;
    $temp = "reg_".$row."_statut";
    $reg_statut = isset($_POST[$temp])?$_POST[$temp]:NULL;
    $temp = "reg_".$row."_login";
    $reg_login = isset($_POST[$temp])?$_POST[$temp]:NULL;
    $reg_login = urldecode($reg_login);
    $temp = "reg_".$row."_nom";
    $reg_nom = isset($_POST[$temp])?$_POST[$temp]:NULL;
    $reg_nom = urldecode($reg_nom);
    $temp = "reg_".$row."_prenom";
    $reg_prenom = isset($_POST[$temp])?$_POST[$temp]:NULL;
    $reg_prenom = urldecode($reg_prenom);
    $temp = "reg_".$row."_sexe";
    $reg_sexe = isset($_POST[$temp])?$_POST[$temp]:NULL;
    $temp = "reg_".$row."_naissance";
    $reg_naiss = isset($_POST[$temp])?$_POST[$temp]:NULL;
    $reg_naiss2 = explode("/",$reg_naiss);
    $reg_naissance = $reg_naiss2[2].$reg_naiss2[1].$reg_naiss2[0];
    $temp = "reg_".$row."_classe";
    $reg_classe = isset($_POST[$temp])?$_POST[$temp]:NULL;
    $temp = "reg_".$row."_regime";
    $reg_regime = isset($_POST[$temp])?$_POST[$temp]:NULL;
    $temp = "reg_".$row."_doublant";
    $reg_doublant = isset($_POST[$temp])?$_POST[$temp]:NULL;
    $temp = "reg_".$row."_prof_suivi";
    $reg_prof_suivi = isset($_POST[$temp])?$_POST[$temp]:NULL;
    $reg_prof_suivi = urldecode($reg_prof_suivi);

    $call_test = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM eleves WHERE login='$reg_login'");
    $test = mysqli_num_rows($call_test);
    if ($test == 0) {
        $reg_data1 = mysqli_query($GLOBALS["mysqli"], "INSERT INTO eleves SET nom='$reg_nom',prenom='$reg_prenom',login='$reg_login',sexe='$reg_sexe',naissance='$reg_naissance',elenoet='',ereno=''");
    } else {
        $reg_data1 = mysqli_query($GLOBALS["mysqli"], "UPDATE eleves SET nom='$reg_nom',prenom='$reg_prenom',sexe='$reg_sexe',naissance='$reg_naissance' WHERE login='$reg_login'");
    }

    $call_classe = mysqli_query($GLOBALS["mysqli"], "SELECT id FROM classes WHERE classe='$reg_classe'");
    $id_classe = @old_mysql_result($call_classe, 0, 'id');
    $call_test = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM j_eleves_classes WHERE login='$reg_login'");
    $test = mysqli_num_rows($call_test);
    if ($test == 0) {
        if ($reg_classe != '-') {
            $periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$id_classe'");
            $nb_periode = mysqli_num_rows($periode_query) + 1 ;
            $i = "1";
            while ($i < $nb_periode) {
                $reg_data3 = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_classes SET login='$reg_login', id_classe='$id_classe', periode='$i', rang='0'");
                $i++;
            }
        } else {
            $reg_data3 = 'ok';
        }
    } else {
        $reg_data3 = 'ok';
    }

    $call_test = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM j_eleves_regime WHERE login='$reg_login'");
    $test = mysqli_num_rows($call_test);
    if ($test == 0) {
        $reg_data5 = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_regime SET login='$reg_login',     doublant='$reg_doublant', regime='$reg_regime'");
    } else {
        $reg_data5 = mysqli_query($GLOBALS["mysqli"], "UPDATE j_eleves_regime SET doublant='$reg_doublant', regime='$reg_regime' WHERE login='$reg_login'");
    }

    if ($id_classe != '') {
        $call_test = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM j_eleves_professeurs WHERE     login='$reg_login'");
        $test = mysqli_num_rows($call_test);
        if ($test == 0) {
            if ($reg_prof_suivi != '-') {
                $reg_data2 = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_professeurs SET login='$reg_login',professeur= '$reg_prof_suivi',id_classe='$id_classe'");
            } else {
                $reg_data2 = 'ok';
            }
        } else {
            if ($reg_prof_suivi != '-') {
                $reg_data2 = mysqli_query($GLOBALS["mysqli"], "UPDATE j_eleves_professeurs SET professeur= '$reg_prof_suivi' WHERE (login='$reg_login' and id_classe='$id_classe')");
            } else {
                $reg_data2 = mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_eleves_professeurs WHERE (login='$reg_login' and id_classe='$id_classe')");
            }
        }
    } else {
        $reg_data2 = 'ok';
    }


    $call_elenoet = mysqli_query($GLOBALS["mysqli"], "SELECT elenoet FROM eleves WHERE login='$reg_login';");
    if(mysqli_num_rows($call_test)>0) {
		$lig_elenoet=mysqli_fetch_object($call_elenoet);
		if($lig_elenoet->elenoet=='') {
			// On initialise à OK $reg_data4 pour ne pas provoquer une erreur sous prétexte qu'il n'y a pas d'elenoet
			$reg_data4 = 'ok';
		}
		else {
			//$call_test = mysql_query("SELECT * FROM j_eleves_etablissements WHERE id_eleve ='$reg_login'");
			$call_test = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM j_eleves_etablissements WHERE id_eleve ='$lig_elenoet->elenoet';");
			$test = mysqli_num_rows($call_test);
			if ($test == 0) {
				if ($reg_etab != '-') {
					//$reg_data4 = mysql_query("INSERT INTO j_eleves_etablissements SET id_eleve ='$reg_login',id_etablissement= '$reg_etab'");
					$reg_data4 = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_etablissements SET id_eleve ='$lig_elenoet->elenoet',id_etablissement= '$reg_etab';");
				} else {
					$reg_data4 = 'ok';
				}

			} else {
				if ($reg_etab != '-') {
					//$reg_data4 = mysql_query("UPDATE j_eleves_etablissements SET id_etablissement = '$reg_etab' WHERE id_eleve='$reg_login'");
					$reg_data4 = mysqli_query($GLOBALS["mysqli"], "UPDATE j_eleves_etablissements SET id_etablissement = '$reg_etab' WHERE id_eleve='$lig_elenoet->elenoet';");
				} else {
					//$reg_data4 = mysql_query("DELETE FROM j_eleves_etablissements WHERE id_eleve='$reg_login'");
					$reg_data4 = mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_eleves_etablissements WHERE id_eleve='$lig_elenoet->elenoet';");
				}
			}
		}
	}



    if ((!isset($reg_data1)) or (!isset($reg_data2)) or (!isset($reg_data3)) or (!isset($reg_data4)) or (!isset($reg_data5))) {
        if ($reg_statut == "nouveau") {
            echo "<p><font color=red>Erreur lors de l'enregistrement de l'utilisateur $reg_login !</font></p>";
        } else {
            echo "<p><font color=red>Erreur lors de la modification de l'utilisateur $reg_login !</font></p>";
        }
    } else {
        if ($reg_statut == "nouveau") {
            echo "<p>L'utilisateur $reg_login a été enregistré avec succès !</p>";
        } else {
            echo "<p>L'utilisateur $reg_login a été modifié avec succès !</p>";
        }
    }
}
require("../lib/footer.inc.php");
?>