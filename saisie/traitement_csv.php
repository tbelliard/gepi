<?php
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

$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
	$current_group = get_group($id_groupe);
} else {
	$current_group = false;
}

include "../lib/periodes.inc.php";

if ($_SESSION['statut'] != "secours") {
    if (!(check_prof_groupe($_SESSION['login'],$current_group["id"]))) {
        $mess=rawurlencode("Vous n'êtes pas professeur de cet enseignement !");
        header("Location: index.php?msg=$mess");
        die();
    }
}

//**************** EN-TETE *****************
$titre_page = "Saisie des moyennes et appréciations | Importation";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil saisie</a>";

$acces_exceptionnel_saisie=false;
if($_SESSION['statut']=='professeur') {
	$acces_exceptionnel_saisie=acces_exceptionnel_saisie_bull_note_groupe_periode($id_groupe, $periode_num);
}

if((($_SESSION['statut']!='secours')&&($current_group["classe"]["ver_periode"]['all'][$periode_num]<2)&&(!$acces_exceptionnel_saisie))||
(($_SESSION['statut']=='secours')&&($current_group["classe"]["ver_periode"]['all'][$periode_num]==0))) {
	echo "<p class = 'grand'>Importation de moyennes et appréciations - $nom_periode[$periode_num]</p>";
	echo "<p class = 'bold'>Groupe : " . $current_group["description"] . " " . $current_group["classlist_string"] . " | Matière : " . $current_group["matiere"]["nom_complet"]."</p>\n";

	echo "<p>La période est close.</p>\n";
	require("../lib/footer.inc.php");
	die();
}


//====================================
if($_SESSION['statut']=='professeur'){
	//$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";


	$tab_groups = get_groups_for_prof($_SESSION["login"],"classe puis matière");
	//$tab_groups = get_groups_for_prof($_SESSION["login"]);

	if(!empty($tab_groups)) {
		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		//foreach($tab_groups as $tmp_group) {
		for($loop=0;$loop<count($tab_groups);$loop++) {
			if($tab_groups[$loop]['id']==$id_groupe){
				$temoin_tmp=1;
				if(isset($tab_groups[$loop+1])){
					$id_grp_suiv=$tab_groups[$loop+1]['id'];
				}
				else{
					$id_grp_suiv=0;
				}
			}
			if($temoin_tmp==0){
				$id_grp_prec=$tab_groups[$loop]['id'];
			}
		}
		// =================================

		if(isset($id_grp_prec)){
			if($id_grp_prec!=0){
				echo " | <a href='import_note_app.php?id_groupe=$id_grp_prec&amp;periode_num=$periode_num";
				echo "'>Enseignement précédent</a>";
			}
		}
		if(isset($id_grp_suiv)){
			if($id_grp_suiv!=0){
				echo " | <a href='import_note_app.php?id_groupe=$id_grp_suiv&amp;periode_num=$periode_num";
				echo "'>Enseignement suivant</a>";
				}
		}
	}
	// =================================
}
//====================================
echo "</p>\n";

echo "<p class = 'grand'>Importation de moyennes et appréciations - ".$nom_periode[$periode_num]."</p>";
echo "<p class = 'bold'>Groupe : " . $current_group["description"] . " " . $current_group["classlist_string"] . " | Matière : " . $current_group["matiere"]["nom_complet"];
echo "<p>";
$modif = 'no';
$nb_row++;
for ($row=1; $row<$nb_row; $row++) {
    $enregistrement_note = 'yes';
    $temp = "reg_".$row."_login";
    if (isset($$temp)) {
        $reg_login = $$temp;
        $reg_login = urldecode($reg_login);
    } else {
        $reg_login = '';
    }
    $temp = "reg_".$row."_note";
    $reg_note = $$temp;
    if (isset($$temp)) {
        $reg_note = urldecode($reg_note);
    } else {
        $reg_note = '';
    }
    $temp = "reg_".$row."_app";
    if (isset($$temp)) {
        $reg_app = $$temp;
        $reg_app = urldecode($reg_app);
        $reg_app = traitement_magic_quotes(corriger_caracteres($reg_app));
    } else {
        $reg_app = '';
    }

	$temoin_periode_close="n";

    $call_login = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM eleves WHERE login='$reg_login';");
    $test = mysqli_num_rows($call_login);
    if ($test != 0) {
        //
        // Si l'élève ne suit pas l'enseignement, échec
        //
        if (in_array($reg_login, $current_group["eleves"][$periode_num]["list"]))  {
			$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$periode_num]["users"][$reg_login]["classe"]]["id"];
			if (($current_group["classe"]["ver_periode"][$eleve_id_classe][$periode_num]=="N")||
			($acces_exceptionnel_saisie)||
			(($current_group["classe"]["ver_periode"][$eleve_id_classe][$periode_num]!="O")&&($_SESSION['statut']=='secours'))) {
				$reg_note_min = my_strtolower($reg_note);
				if (preg_match ("/^[0-9\.\,]{1,}$/", $reg_note)) {
					$reg_note = str_replace(",", ".", "$reg_note");
					//$test_num = settype($reg_note,"double");
					if (($reg_note >= 0) and ($reg_note <= 20)) {
						$elev_statut = '';
					} else {
						$reg_note = '0';
						$elev_statut = '-';
					}
				} elseif ($reg_note_min == '-') {
					$reg_note = '0';
					$elev_statut = '-';
				} elseif ($reg_note_min == "disp") {
					$reg_note = '0';
					$elev_statut = 'disp';
				} elseif ($reg_note_min == "abs") {
					$reg_note = '0';
					$elev_statut = 'abs';
				} elseif ($reg_note == "") {
					$enregistrement_note = 'no';
				} else {
					$reg_note = '0';
					$elev_statut = '-';
				}

				if ($enregistrement_note != "no") {
					$sql="SELECT * FROM matieres_notes WHERE (login='$reg_login' AND id_groupe='" . $id_groupe . "' AND periode='$periode_num')";
					//echo "$sql<br />";
					$test_eleve_note_query = mysqli_query($GLOBALS["mysqli"], $sql);
					$test = mysqli_num_rows($test_eleve_note_query);
					if ($test != "0") {
						$sql="UPDATE matieres_notes SET note='$reg_note',statut='$elev_statut', rang='0' WHERE (login='$reg_login' AND id_groupe='" . $id_groupe . "' AND periode='$periode_num')";
						//echo "$sql<br />";
						$reg_data1 = mysqli_query($GLOBALS["mysqli"], $sql);
						$modif = 'yes';
					} else {
						$sql="INSERT INTO matieres_notes SET login='$reg_login', id_groupe='" . $id_groupe . "',periode='$periode_num',note='$reg_note',statut='$elev_statut', rang='0'";
						//echo "$sql<br />";
						$reg_data1 = mysqli_query($GLOBALS["mysqli"], $sql);
						$modif = 'yes';
					}
				} else {
					$reg_data1 ='ok';
				}

				if ($reg_app != "") {
					$sql="SELECT * FROM matieres_appreciations WHERE (login='$reg_login' AND id_groupe='" . $id_groupe . "' AND periode='$periode_num')";
					//echo "$sql<br />";
					$test_eleve_app_query = mysqli_query($GLOBALS["mysqli"], $sql);
					$test = mysqli_num_rows($test_eleve_app_query);
					if ($test != 0) {
						$sql="UPDATE matieres_appreciations SET appreciation='" . $reg_app . "' WHERE (login='$reg_login' AND id_groupe='" . $current_group["id"] . "' AND periode='$periode_num')";
						//echo "$sql<br />";
						$reg_data2 = mysqli_query($GLOBALS["mysqli"], $sql);
					} else {
						$sql="INSERT INTO matieres_appreciations set login = '" . $reg_login . "', id_groupe = '" . $id_groupe . "', periode = '" . $periode_num . "', appreciation = '" . $reg_app . "'";
						//echo "$sql<br />";
						$reg_data2 = mysqli_query($GLOBALS["mysqli"], $sql);
						echo mysqli_error($GLOBALS["mysqli"]);
					}
				} else {
					$reg_data2 = 'ok';
				}
			}
			else {
				$temoin_periode_close="y";
			}
        }
    }
	if($temoin_periode_close=="y") {
		echo "<font color='red'>La période est close pour l'utilisateur $reg_login !</font><br />\n";
	}
	else {
		if ((!$reg_data1) or (!$reg_data2)) {
				echo "<font color='red'>Erreur lors de la modification de données de l'utilisateur $reg_login !</font><br />\n";
		} else {
			echo "Les données de l'utilisateur $reg_login ont été modifiées avec succès !<br />\n";
		}
	}
}

// on indique que qu'il faut le cas échéant procéder à un recalcul du rang des élèves
if ($modif == 'yes') {
    $recalcul_rang = sql_query1("select recalcul_rang from groupes
    where id='".$id_groupe."' limit 1 ");
    $long = mb_strlen($recalcul_rang);
    if ($long >= $periode_num) {
        $recalcul_rang = substr_replace ( $recalcul_rang, "y", $periode_num-1, $periode_num);
    } else {
       for ($l = $long; $l<$periode_num; $l++) {
           $recalcul_rang = $recalcul_rang.'y';
       }
    }
    $req = mysqli_query($GLOBALS["mysqli"], "update groupes set recalcul_rang = '".$recalcul_rang."'
    where id='".$id_groupe."'");
}

echo "</p>\n";
echo "<p><a href='saisie_notes.php?id_groupe=$id_groupe&amp;order_by=nom'>Accéder à la page de saisie des moyennes pour vérification</a>";
echo "<br /><a href='saisie_appreciations.php?id_groupe=$id_groupe&amp;order_by=nom'>Accéder à la page de saisie des appréciations pour vérification</a></p>\n";
require("../lib/footer.inc.php");
?>
