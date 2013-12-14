<?php
@set_time_limit(0);
/*
 * $Id$
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

// MODIF A FAIRE:
// ALTER TABLE `eleves` ADD `ele_id` VARCHAR( 10 ) NOT NULL ;


//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des élèves - Etape 3";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();
// Passer à 'y' pour afficher les requêtes
$debug_ele="n";

//==================================
// RNE de l'établissement pour comparer avec le RNE de l'établissement de l'année précédente
$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
//==================================

echo "<center><h3 class='gepi'>Première phase d'initialisation<br />Importation des élèves,  constitution des classes et affectation des élèves dans les classes</h3></center>";
echo "<center><h3 class='gepi'>Troisième étape : Enregistrement des élèves et affectation des élèves dans les classes</h3></center>";

if (isset($is_posted) and ($is_posted == "yes")) {
    $call_data = mysqli_query($GLOBALS["mysqli"], "SELECT ID_TEMPO,ELENOM,ELEPRE,ELENOET,ELE_ID,ELESEXE,ELEDATNAIS,ELEDOUBL,ELENONAT,ELEREG,DIVCOD,ETOCOD_EP FROM temp_gep_import2 ORDER BY DIVCOD,ELENOM,ELEPRE");
    $nb = mysqli_num_rows($call_data);
    $i = "0";
    while ($i < $nb) {
        //$req = mysql_query("select col2 from tempo2 where col1 = '$i'");
        //$reg_login = @old_mysql_result($req, 0, 'col2');

		if($debug_ele=='y') {echo "<br /><br />";}

        $id_tempo = @old_mysql_result($call_data, $i, "ID_TEMPO");

        $req = mysqli_query($GLOBALS["mysqli"], "select col2 from tempo2 where col1 = '$id_tempo'");
        $reg_login = @old_mysql_result($req, 0, 'col2');

        $no_gep = @old_mysql_result($call_data, $i, "ELENONAT");
        $reg_nom = ((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], nettoyer_caracteres_nom(@old_mysql_result($call_data, $i, "ELENOM", "an", " '_-",""))) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
        $reg_prenom = @old_mysql_result($call_data, $i, "ELEPRE");
        $reg_elenoet = @old_mysql_result($call_data, $i, "ELENOET");
        //$reg_ereno = @old_mysql_result($call_data, $i, "ERENO");
        $reg_ele_id = @old_mysql_result($call_data, $i, "ELE_ID");
        $reg_sexe = @old_mysql_result($call_data, $i, "ELESEXE");
        $reg_naissance = @old_mysql_result($call_data, $i, "ELEDATNAIS");
        $reg_doublant = @old_mysql_result($call_data, $i, "ELEDOUBL");
        $reg_classe = @old_mysql_result($call_data, $i, "DIVCOD");
        $reg_etab = @old_mysql_result($call_data, $i, "ETOCOD_EP");
        $tab_prenom = explode(" ",$reg_prenom);
        $reg_prenom = ((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], nettoyer_caracteres_nom($tab_prenom[0], "an", " '_-","")) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
        $reg_regime = old_mysql_result($call_data, $i, "ELEREG");
        if (($reg_sexe != "M") and ($reg_sexe != "F")) {$reg_sexe = "M";}
        if ($reg_naissance == '') {$reg_naissance = "19000101";}

        $maj_tempo = mysqli_query($GLOBALS["mysqli"], "UPDATE temp_gep_import2 SET LOGIN='$reg_login' WHERE ID_TEMPO='$id_tempo'");

		$sql="INSERT INTO eleves SET no_gep='$no_gep',login='$reg_login',nom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $reg_nom) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',prenom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $reg_prenom) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',sexe='$reg_sexe',naissance='$reg_naissance',elenoet='$reg_elenoet',ele_id='$reg_ele_id'";
		if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
        $reg_eleve = mysqli_query($GLOBALS["mysqli"], $sql);
        if (!$reg_eleve) {
				echo "<p style='color:red'>Erreur lors de l'enregistrement de l'élève $reg_nom $reg_prenom.</p>";
		}
		else {
			$sql="SELECT * FROM tempo_utilisateurs WHERE identifiant1='".$reg_ele_id."' AND statut='eleve';";
			if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
			$res_tmp_u=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_tmp_u)>0) {
				$lig_tmp_u=mysqli_fetch_object($res_tmp_u);

				$sql="INSERT INTO utilisateurs SET login='".$lig_tmp_u->login."', nom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $reg_nom) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', prenom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $reg_prenom) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', ";
				if($reg_sexe=='M') {
					$sql.="civilite='M', ";
				}
				else {
					$sql.="civilite='MLLE', ";
				}
				$sql.="password='".$lig_tmp_u->password."', salt='".$lig_tmp_u->salt."', email='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig_tmp_u->email) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', statut='eleve', etat='inactif', change_mdp='n', auth_mode='".$lig_tmp_u->auth_mode."';";
				if($debug_ele=='y') {echo "<span style='color:blue;'>$sql</span><br />";}
				$insert_u=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert_u) {
					echo "<span style='color:red'>Erreur lors de la re-création du compte utilisateur pour ".$reg_nom." ".$reg_prenom.".</span><br />\n";
				}
			}
		}

		$regime=traite_regime_sconet($reg_regime);
		if("$regime"=="ERR") {$regime="d/p";}
		//=========================

        if ($reg_doublant == "O") {$doublant = 'R';}
        if ($reg_doublant != "O") {$doublant = '-';}

        $register = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_regime SET login='$reg_login',regime='$regime',doublant='$doublant'");
        if (!$register) {echo "<p style='color:red'>Erreur lors de l'enregistrement des infos de régime pour l'élève $reg_nom $reg_prenom.";}

        $call_classes = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes");
        $nb_classes = mysqli_num_rows($call_classes);
        $j = 0;
        while ($j < $nb_classes) {
            $classe = old_mysql_result($call_classes, $j, "classe");
            if ($reg_classe == $classe) {
                $id_classe = old_mysql_result($call_classes, $j, "id");
                $number_periodes = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM periodes WHERE id_classe='$id_classe'"),0);
                $u = 1;
                while ($u <= $number_periodes) {
                    $reg = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_classes SET login='$reg_login',id_classe='$id_classe',periode='$u', rang='0'");
                    if (!$reg) {echo "<p style='color:red'>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à la classe $classe pour la période $u";}
                    $u++;
                }
            }
            $j++;
        }

        if (($reg_etab != '')&&($reg_elenoet != '')) {

			if($gepiSchoolRne!="") {
				if($gepiSchoolRne!=$reg_etab) {
					$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$reg_elenoet';";
					$test_etab=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test_etab)==0){
						$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$reg_elenoet', id_etablissement='$reg_etab';";
						$insert_etab=mysqli_query($GLOBALS["mysqli"], $sql);
						if (!$insert_etab) {
							echo "<p style='color:red'>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à l'établissement $reg_etab.</p>\n";
						}
					}
					else {
						$sql="UPDATE j_eleves_etablissements SET id_etablissement='$reg_etab' WHERE id_eleve='$reg_elenoet';";
						$update_etab=mysqli_query($GLOBALS["mysqli"], $sql);
						if (!$update_etab) {
							echo "<p style='color:red'>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à l'établissement $reg_etab.</p>\n";
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
						echo "<p style='color:red'>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à l'établissement $reg_etab.</p>\n";
					}
				}
			}

        }

        $i++;
    }
    // on vide la table tempo2 qui nous a servi à stocker les login temporaires des élèves
    $del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM tempo2");

	// On renseigne le témoin: La mise à jour à partir de sconet sera possible.
	saveSetting("import_maj_xml_sconet", 1);


    //echo "<p>L'importation des données de <b>GEP</b> concernant la constitution des classes est terminée.</p>";
    echo "<p>L'importation des données concernant la constitution des classes est terminée.</p>";
    echo "<p align='center'><a href='responsables.php'>Procéder à la deuxième phase d'importation des responsables</a></p>";
	echo "<p><br /></p>\n";
    require("../lib/footer.inc.php");
	die();
}
else {
    // on vide la table tempo2 qui va nous servir à stocker les login temporaires des élèves
    $del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM tempo2");

	//if(getSettingValue('use_sso')=="lcs") {
	if(getSettingValue('auth_sso')=="lcs") {
		// On va récupérer les logins du LCS
		require_once("../lib/lcs.inc.php");
		$ds = connect_ldap($lcs_ldap_host,$lcs_ldap_port,"","");
	}

    //$call_data = mysql_query("SELECT ELENOM,ELEPRE,ELENOET,ELE_ID,ELESEXE,ELEDATNAIS,ELEDOUBL,ELENONAT,ELEREG,DIVCOD,ETOCOD_EP FROM temp_gep_import2 ORDER BY DIVCOD,ELENOM,ELEPRE");
    //$nb = mysql_num_rows($call_data);
    //$i = "0";

    $tab_sql=array();
	$tab_sql[]="SELECT ID_TEMPO,ELENOM,ELEPRE,ELENOET,ELE_ID,ELESEXE,ELEDATNAIS,ELEDOUBL,ELENONAT,ELEREG,DIVCOD,ETOCOD_EP FROM temp_gep_import2 t, tempo_utilisateurs tu WHERE t.ELE_ID=tu.identifiant1 ORDER BY DIVCOD,ELENOM,ELEPRE;";
	$tab_sql[]="SELECT ID_TEMPO,ELENOM,ELEPRE,ELENOET,ELE_ID,ELESEXE,ELEDATNAIS,ELEDOUBL,ELENONAT,ELEREG,DIVCOD,ETOCOD_EP FROM temp_gep_import2 WHERE ELE_ID NOT IN (SELECT identifiant1 FROM tempo_utilisateurs) ORDER BY DIVCOD,ELENOM,ELEPRE;";

    echo "<p>Le tableau suivant affiche les données qui vont être enregistrées dans la base de donnée GEPI lorsque vous aurez confirmé ce choix tout en bas de la page.<br /><b>Tant que vous n'avez pas validé en bas de la page, aucune donnée n'est enregistrée !</b></p>";

    echo "<p>Les valeurs en rouge signalent d'éventuelles données manquantes (ND pour \"non défini\") dans le fichier <b>eleves.csv</b> fourni ! Ceci n'est pas gênant pour l'enregistrement dans la base <b>GEPI</b>. Vous aurez en effet la possibilité de compléter les données manquantes avec les outils fournis dans <b>GEPI</b></p>";
    echo "<p>Une fois cette page entièrement chargée, ce qui peut prendre un peu de temps, <b>veuillez lire attentivement les remarques en bas de la page </b>avant de procéder à l'enregistrement définitif des données</p>\n";
    echo "<table class='boireaus' border=1 cellpadding=2 cellspacing=2>";
    echo "<tr><th><p class=\"small\">N° INE</p></th><th><p class=\"small\">Identifiant</p></th><th><p class=\"small\">Nom</p></th><th><p class=\"small\">Prénom</p></th><th><p class=\"small\">Sexe</p></th><th><p class=\"small\">Date de naiss.</p></th><th><p class=\"small\">Régime</p></th><th><p class=\"small\">Doublant</p></th><th><p class=\"small\">Classe</p></th><th><p class=\"small\">Etablissement d'origine ou précédent</p></th></tr>\n";

	$alt=1;
    $max_lignes_pb = 0;

    $ii = "0";

	for($loop=0;$loop<count($tab_sql);$loop++) {
		$call_data = mysqli_query($GLOBALS["mysqli"], $tab_sql[$loop]);
		if($debug_ele=='y') {
			echo "<tr>\n";
			echo "<td colspan='10'>\n";
			echo $tab_sql[$loop];
			echo "</td>\n";
			echo "</tr>\n";
		}
		$nb = mysqli_num_rows($call_data);
	    $i = "0";

		while ($i < $nb) {
			if($debug_ele=='y') {echo "<br /><br />";}

			$lcs_eleve_en_erreur="n";

			$alt=$alt*(-1);
			$ligne_pb = 'no';
			$id_tempo = old_mysql_result($call_data, $i, "ID_TEMPO");
			$no_gep = old_mysql_result($call_data, $i, "ELENONAT");

			$reg_nom = old_mysql_result($call_data, $i, "ELENOM");
			$reg_nom = nettoyer_caracteres_nom($reg_nom, "a", " '_-", "");
			$reg_nom = trim(preg_replace("/'/", " ", $reg_nom));

			$reg_prenom = old_mysql_result($call_data, $i, "ELEPRE");
			$tab_prenom = explode(" ",$reg_prenom);
			$reg_prenom = $tab_prenom[0];
			$reg_prenom = nettoyer_caracteres_nom($tab_prenom[0], "a", " '_-", "");
			$reg_prenom = preg_replace("/'/", "", $tab_prenom[0]);

			$reg_elenoet = old_mysql_result($call_data, $i, "ELENOET");
			//$reg_ereno = old_mysql_result($call_data, $i, "ERENO");
			$reg_ele_id = old_mysql_result($call_data, $i, "ELE_ID");
			$reg_sexe = old_mysql_result($call_data, $i, "ELESEXE");
			$reg_naissance = old_mysql_result($call_data, $i, "ELEDATNAIS");
			$reg_doublant = old_mysql_result($call_data, $i, "ELEDOUBL");
			$reg_classe = old_mysql_result($call_data, $i, "DIVCOD");
			$reg_etab = old_mysql_result($call_data, $i, "ETOCOD_EP");
			$tab_prenom = explode(" ",$reg_prenom);
			$reg_prenom = $tab_prenom[0];
			$reg_regime = old_mysql_result($call_data, $i, "ELEREG");
			if ($no_gep != '') {
				$no_gep_aff = $no_gep;
			} else {
				$no_gep_aff = "<font color = 'red'>ND</font>";
				$ligne_pb = 'yes';
			}
	
			// On teste pour savoir s'il faut créer un login
			$nouv_login='no';
			if ($no_gep != '') {
					$nouv_login = 'yes';
			}
			// S'il s'agit d'un élève ne figurant pas déjà dans une des bases élève des années passées,
			// on crée un login !
	
			if (($no_gep == '') or ($nouv_login=='yes')) {
				$login_eleve="";

				//$reg_nom = remplace_accents($reg_nom);
				//$reg_prenom = remplace_accents($reg_prenom);

				if($reg_ele_id!='') {
					$sql="SELECT * FROM tempo_utilisateurs WHERE identifiant1='".$reg_ele_id."' AND statut='eleve';";
					if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
					$res_tmp_u=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_tmp_u)>0) {
						$lig_tmp_u=mysqli_fetch_object($res_tmp_u);
						$login_eleve=$lig_tmp_u->login;
						if($debug_ele=='y') {echo "<span style='color:green;'>Login récupéré de la table 'tempo_utilisateurs' : '$login_eleve'</span><br />";}
					}
				}
	
				if($login_eleve=="") {
					$default_login_gen_type=getSettingValue('mode_generation_login_eleve');
					if(($default_login_gen_type=='')||(!check_format_login($default_login_gen_type))) {$default_login_gen_type='nnnnnnnnn_p';}

					$default_login_gen_type_casse=getSettingValue('mode_generation_login_eleve_casse');
					if(($default_login_gen_type_casse!='min')&&($default_login_gen_type_casse!='maj')) {$default_login_gen_type_casse='min';}

					$login_eleve=generate_unique_login($reg_nom, $reg_prenom, $default_login_gen_type, $default_login_gen_type_casse);
					if($debug_ele=='y') {echo "<span style='color:blue;'>Login nouvellement généré pour '$reg_nom $reg_prenom' : '$login_eleve'</span><br />";}
				}
	
				// Dans le cas où Gepi est intégré à un ENT, il ne doit pas générer de login mais récupérer celui qui existe déjà
				// A MODIFIER : Pouvoir gérer use_ent et NetCollege ITOP hors 27:
				if ((getSettingValue("use_ent") == 'y')&&(!preg_match("/^027/", getSettingValue('gepiSchoolRne')))) {
					// On a récupéré les informations dans la table ldap_bx
					// voir aussi les explications de la ligne 710 du fichiers professeurs.php
					$sql_p = "SELECT login_u FROM ldap_bx
											WHERE identite_u = '".$no_gep."'";
					$query_p = mysqli_query($GLOBALS["mysqli"], $sql_p);
					$nbre = mysqli_num_rows($query_p);
					if ($nbre >= 1) {
						// On considère que l'information est bonne puisqu'elle a été construite avec la même source sconet
						$login_eleve = old_mysql_result($query_p, 0,"login_u");
					}else{
						// Il faudra trouver une solution dans ce cas là (même s'il ne doit pas être très fréquent
						//$login_eleve = "erreur_".$i;
						$login_eleve = "erreur_".$id_tempo;
					}
					if($debug_ele=='y') {echo "<span style='color:lime;'>Login récupéré de ldap_bx : $login_eleve</span><br />";}
				}

				//echo "Avant auth_sso<br />";
				//if(getSettingValue('use_sso')=="lcs") {
				if(getSettingValue('auth_sso')=="lcs") {
					$lcs_eleve_en_erreur="y";
					if($reg_elenoet!='') {
						$login_eleve=get_lcs_login($reg_elenoet, 'eleve');
						//echo "get_lcs_login($reg_elenoet, 'eleve')=".$login_eleve."<br />";
						if($login_eleve!='') {
							$test_tempo2 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT col2 FROM tempo2 WHERE (col2='$login_eleve' or col2='".strtoupper($login_eleve)."')"));
							if ($test_tempo2 != "0") {
								$ligne_pb = 'yes';

								if($debug_ele=='y') {echo "<span style='color:red;'>PROBLEME avec le login récupéré du LDAP LCS ($login_eleve) : déjà présent dans la table 'tempo2'</span><br />";}
							} else {
								//$reg = mysql_query("INSERT INTO tempo2 VALUES ('$i', '$login_eleve')");
								$reg = mysqli_query($GLOBALS["mysqli"], "INSERT INTO tempo2 VALUES ('$id_tempo', '$login_eleve')");
								//return 'yes';
								$lcs_eleve_en_erreur="n";

								if($debug_ele=='y') {echo "<span style='color:orange;'>Login récupéré sans problème du LDAP LCS : $login_eleve</span><br />";}

							}
						}
						else {
							$ligne_pb = 'yes';
						}
					}
					else {
						$ligne_pb = 'yes';
					}
				}
				else {
					// On teste l'unicité du login que l'on vient de créer
					$k = 2;
					$test_unicite = 'no';
					$temp = $login_eleve;
					while ($test_unicite != 'yes') {
						$test_unicite = test_unique_e_login($login_eleve,$id_tempo);
						if ($test_unicite != 'yes') {
							$login_eleve = $temp.$k;
							$k++;
						}
					}
					if($debug_ele=='y') {echo "<span style='color:coral;'>Login après contrôle d'unicité : $login_eleve</span><br />";}
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

			$reg_regime_aff=traite_regime_sconet($reg_regime);
			if($reg_regime_aff=="ERR"){
				$reg_regime_aff="<font color = 'red'>ND</font>";
				$ligne_pb = 'yes';
			}
			//=========================
	
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
				$classe = old_mysql_result($call_classes, $j, "classe");
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
					$etab_nom = @old_mysql_result($calletab, 0, "nom");
					$etab_cp = @old_mysql_result($calletab, 0, "cp");
					$etab_ville = @old_mysql_result($calletab, 0, "ville");
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
				echo "<tr class='lig$alt white_hover'><td><p class=\"small\">$no_gep_aff</p></td>";
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
			$ii++;	
		}
	}
    echo "</table>";
    //echo "<p><b>Nombre total de lignes : $nb</b><br />\n";
    echo "<p><b>Nombre total de lignes : $ii</b><br />\n";
    if ($max_lignes_pb == 0) {
        echo "Aucune erreur n'a été détectée !</p>";
    } else {
        echo "Des données manquantes ou incomplètes ont été détectées dans <b>$max_lignes_pb lignes</b> : Elles apparaissent dans le tableau ci-dessus en rouge !";
        if ($affiche != 'partiel') {
            echo "<p>--> Pour n'afficher que les lignes ou des problèmes ont été détectés, cliquez sur le bouton \"Affichage partiel\" :</p>";
            echo "<form enctype='multipart/form-data' action='step3.php' method=post>";
			echo add_token_field();
            echo "<input type=hidden name='is_posted' value='no' />";
            echo "<input type=hidden name='affiche' value='partiel' />";
            echo "<center><input type='submit' value='Affichage partiel' /></center>";
            echo "</form>";
        } else {
            echo "<p>--> Pour afficher toutes les lignes, cliquez sur le bouton \"Afficher tout\" :</p>";
            echo "<form enctype='multipart/form-data' action='step3.php' method=post>";
			echo add_token_field();
            echo "<input type=hidden name='is_posted' value='no' />";
            echo "<input type=hidden name='affiche' value='tout' />";
            echo "<center><input type='submit' value='Afficher tout' /></center>";
            echo "</form>";
        }
    }

    // A MODIFIER : Pouvoir gérer use_ent et NetCollege ITOP hors 27:
    if ((getSettingValue("use_ent") == 'y')&&(!preg_match("/^027/", getSettingValue('gepiSchoolRne')))) {
    	// Dans le cas d'un ent on renvoie l'admin pour qu'il vérifie tous les logins de la forme erreur_xx
    	echo '
			<p>--&gt; Avant d\'enregistrer, vous allez vérifier tous les logins potentiellement erronés.</p>
			<p><a href="../mod_ent/gestion_ent_eleves.php">Vérifier les logins</a></p>
		';
    } else {
	    echo "<p>--&gt; Pour Enregistrer toutes les données dans la base <b>GEPI</b>, cliquez sur le bouton \"Enregistrer\" !</p>\n";
    	echo "<form enctype='multipart/form-data' action='step3.php' method='post'>\n";

	    //echo "<p>Si vous disposez d'un fichier ELEVE_ETABLISSEMENT.CSV, vous pouvez le fournir maintenant:<br />";
    	//echo "<input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";

		echo add_token_field();
	    echo "<input type='hidden' name='is_posted' value='yes' />\n";
    	echo "<p style='text-align: center;'><input type='submit' value='Enregistrer' /></p>\n";
    	echo "</form>\n";
    }
    //echo "</div>";
	echo "<p><br /></p>\n";
    require("../lib/footer.inc.php");
}
?>
