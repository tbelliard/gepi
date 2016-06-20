<?php
/*
*
* Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Bouguin Régis
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");
//include("../lib/initialisationsPropel.inc.php");
require_once("../orm/helpers/EdtHelper.php");

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

include_once 'scripts/fonctions.php';

$anneeScolaire = EdtHelper::getPremierJourAnneeScolaire()->format("Y");


$id_groupe = filter_input(INPUT_POST, 'id_groupe') ? filter_input(INPUT_POST, 'id_groupe') : filter_input(INPUT_GET, 'id_groupe');


$periode_cn = filter_input(INPUT_POST, 'periode_cn') ? filter_input(INPUT_POST, 'periode_cn') : filter_input(INPUT_GET, 'periode_cn');
$periode = filter_input(INPUT_POST, 'periode');

$quePerso = filter_input(INPUT_POST, 'quePerso');
$queMat = filter_input(INPUT_POST, 'queMat');



/* ===== Élément de programme pour le groupe =====*/
if ($id_groupe) {
	
	// on crée un nouvel élément programme pour le groupe
	$newElemGroupe = filter_input(INPUT_POST, 'newElemGroupe');
	if ($newElemGroupe != NULL) {
		saveNewElemGroupe($id_groupe, $newElemGroupe, $anneeScolaire, $periode);
	}
	
	// on associe un élément programme au groupe
	$elemGroupe = filter_input(INPUT_POST, 'Elem_groupe');
	if ($elemGroupe != NULL) {
		associeElemGroupe($id_groupe, $elemGroupe, $anneeScolaire, $periode);
	}
	
	// on récupère l'Id de l'élément à supprimer puis on le supprime
	$delElemGroupe =filter_input(INPUT_POST, 'delElemProgGroup', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	if ($delElemGroupe != NULL) {
		$delElemGroupe = key($delElemGroupe);
		dissocieElemGroupe($id_groupe, $delElemGroupe, $anneeScolaire, $periode);
	}
	
}

/* ===== Fin élément de programme pour le groupe =====*/

/* ===== Élément de programme pour un élève =====*/
$delElemProgElv = filter_input(INPUT_POST, 'delElemProgElv', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if ($delElemProgElv) {
	foreach ($delElemProgElv as $idElem=>$loginEleve) {
		supprimeElemProgElv($loginEleve, $idElem,$anneeScolaire, $periode, $id_groupe);
	}
}

$associeElem_Eleve = filter_input(INPUT_POST, 'Elem_Eleve', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if ($associeElem_Eleve) {
	foreach ($associeElem_Eleve as $loginEleve=>$idElem) {
		if ($idElem) {
			saveJointureEleveEP($loginEleve, $idElem, $anneeScolaire, $periode);
		}
	}
}

$newElem_Eleve = filter_input(INPUT_POST, 'newElemEleve', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if ($newElem_Eleve) {
	foreach ($newElem_Eleve as $loginEleve=>$texteElem) {
		if ($texteElem) {
			//echo "On crée l'élément ".$texteElem." pour ".$loginEleve." dans le groupe ".$id_groupe ;
			creeElementPourEleve($loginEleve, $id_groupe, $texteElem, $anneeScolaire, $periode);

		}
	}
}



/* ===== Fin élément de programme pour un élève =====*/

// Si le témoin temoin_check_srv() doit être affiché, on l'affichera dans la page à côté de Enregistrer.
$aff_temoin_serveur_hors_entete="y";

// Initialisation
//$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
	$current_group = get_group($id_groupe);
} else {
    $current_group = false;

    // Pourquoi poursuivre si le groupe n'est pas trouvé?
    $mess=rawurlencode("Anomalie: Vous arrivez sur cette page sans que l'enseignement soit sélectionné ! Si vous aviez bien sélectionné un enseignement, il se peut que vous ayez un module php du type 'suhosin' qui limite le nombre de variables pouvant être postées dans un formulaire.");
    header("Location: index.php?msg=$mess");
    die();
}

if (count($current_group["classes"]["list"]) > 1) {
    $multiclasses = true;
} else {
    $multiclasses = false;
}

$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "classe");

if ($_SESSION['statut'] != "secours") {
    if (!(check_prof_groupe($_SESSION['login'],$current_group["id"]))) {
        $mess=rawurlencode("Vous n'êtes pas professeur de cet enseignement !");
        header("Location: index.php?msg=$mess");
        die();
    }
}

include "../lib/periodes.inc.php";

$proposer_liens_enregistrement="n";
$i=1;
while ($i < $nb_periode) {
    if($_SESSION['statut']=='professeur') {
        if($current_group["classe"]["ver_periode"]["all"][$i] > 1) {
            // 0 : Toutes les classes sont closes
            // 1 : Toutes les classes sont partiellement closes
            // 2 : Au moins une classe est ouverte
            // 3 : Toutes les classes sont ouvertes
            $proposer_liens_enregistrement="y";
            break;
        }
    }
    elseif($_SESSION['statut']=='secours') {
        if($current_group["classe"]["ver_periode"]["all"][$i] > 0) {
            // 0 : Toutes les classes sont closes
            // 1 : Toutes les classes sont partiellement closes
            // 2 : Au moins une classe est ouverte
            // 3 : Toutes les classes sont ouvertes
            $proposer_liens_enregistrement="y";
            break;
        }
    }
    $i++;
}

//=====================================
// Tableau pour les autorisations exceptionnelles de saisie
// Il n'est pris en compte comme le getSettingValue('autoriser_correction_bulletin') que pour une période partiellement close
$une_autorisation_exceptionnelle_de_saisie_au_moins='n';
$tab_autorisation_exceptionnelle_de_saisie=array();
$date_courante=time();
//echo "\$id_groupe=$id_groupe<br />";
//echo "\$date_courante=$date_courante<br />";
$k=1;
while ($k < $nb_periode) {
    $tab_autorisation_exceptionnelle_de_saisie[$k]='n';
    $sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite, mode FROM matieres_app_delais WHERE id_groupe='$id_groupe' AND periode='$k';";
    $res=mysqli_query($GLOBALS["mysqli"], $sql);
    if(mysqli_num_rows($res)>0) {
        $lig=mysqli_fetch_object($res);
        $date_limite=$lig->date_limite;
        // 20131204
        //echo "\$date_limite=$date_limite en période $k.<br />";
        //echo "\$date_courante=$date_courante.<br />";

        if($date_courante<$date_limite) {
            $tab_autorisation_exceptionnelle_de_saisie[$k]='y';
            if($lig->mode=='acces_complet') {
                $tab_autorisation_exceptionnelle_de_saisie[$k]='yy';
                $proposer_liens_enregistrement="y";
            }
            $une_autorisation_exceptionnelle_de_saisie_au_moins='y';
        }
    }
    //echo "\$tab_autorisation_exceptionnelle_de_saisie[$k]=".$tab_autorisation_exceptionnelle_de_saisie[$k]."<br />";
    $k++;
}
//=====================================

$msg="";

function f_write_tmp($texte) {
    $debug="n";
    if($debug=="y") {
        $f=fopen("/tmp/debug_saisie_app.txt", "a+");
        fwrite($f, strftime("%Y-%m-%d %H:%M:%S")." : ".$texte."\n");
        fclose($f);
    }
}

/*
echo "<pre>";
print_r($current_group);
echo "</pre>";
*/

$prefixe_debug=strftime("%Y%m%d %H%M%S")." : ".$_SESSION['login'];

if (isset($_POST['is_posted'])) {
    check_token();

    $indice_max_log_eleve=$_POST['indice_max_log_eleve'];

    $tab_app_tempo=array();
    $sql="SELECT * FROM matieres_appreciations_tempo WHERE id_groupe = '".$id_groupe."';";
    $res_app_temp=mysqli_query($GLOBALS["mysqli"], $sql);
    while($lig_app_tempo=mysqli_fetch_object($res_app_temp)) {
        $tab_app_tempo[$lig_app_tempo->periode][$lig_app_tempo->login]=$lig_app_tempo->appreciation;
    }

    $k=1;
	while ($k < $nb_periode) {
            //=========================
            // AJOUT: boireaus 20071003
            unset($log_eleve);
            $log_eleve=isset($_POST['log_eleve_'.$k]) ? $_POST['log_eleve_'.$k] : NULL;
            //=========================

            // 20131204
            $acces_exceptionnel_complet="n";
            $sql="SELECT 1=1 FROM matieres_app_delais WHERE id_groupe='$id_groupe' AND periode='$k' AND mode='acces_complet' AND UNIX_TIMESTAMP(date_limite)>'".time()."';";
            //f_write_tmp($sql);
            $test_acces_exceptionnel=mysqli_query($GLOBALS["mysqli"], $sql);
            if(mysqli_num_rows($test_acces_exceptionnel)>0) {
                $acces_exceptionnel_complet="y";
            }
            //f_write_tmp($acces_exceptionnel_complet);

            //=================================================
            if(isset($_POST['app_grp_'.$k])){
                //f_write_tmp("\$_POST['app_grp_'.$k]=".$_POST['app_grp_'.$k]);
                //f_write_tmp("\$current_group[\"classe\"][\"ver_periode\"]['all'][$k]=".$current_group["classe"]["ver_periode"]['all'][$k]);
                if(($current_group["classe"]["ver_periode"]['all'][$k]>=2)||
                    (($current_group["classe"]["ver_periode"]['all'][$k]!=0)&&($_SESSION['statut']=='secours'))) {

                    if (isset($NON_PROTECT["app_grp_".$k])){
                        $app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT["app_grp_".$k]));
                    }
                    else{
                        $app = "";
                    }
                        //echo "<pre>$k: $app</pre>";
                        // Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
                        $app=suppression_sauts_de_lignes_surnumeraires($app);

                        $test_grp_app_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_appreciations_grp WHERE (id_groupe='" . $current_group["id"]."' AND periode='$k')");
                        $test = mysqli_num_rows($test_grp_app_query);
                        if ($test != "0") {
                            if ($app != "") {
                                $register = mysqli_query($GLOBALS["mysqli"], "UPDATE matieres_appreciations_grp SET appreciation='" . $app . "' WHERE (id_groupe='" . $current_group["id"]."' AND periode='$k')");
                            } else {
                                $register = mysqli_query($GLOBALS["mysqli"], "DELETE FROM matieres_appreciations_grp WHERE (id_groupe='" . $current_group["id"]."' AND periode='$k')");
                            }
                            if (!$register) {
                                $msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour le groupe/classe<br />";
                            }

                        } else {
                            if ($app != "") {
                                $register = mysqli_query($GLOBALS["mysqli"], "INSERT INTO matieres_appreciations_grp SET id_groupe='" . $current_group["id"]."',periode='$k',appreciation='" . $app . "'");
                                if (!$register) {
                                    $msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour le groupe/classe<br />";
                                }
                            }
                        }
                }
                else {
                        $msg.="Anomalie: Tentative de saisie d'une appréciation de classe alors que la période n'est pas ouverte en saisie.<br />";
                }
            }
		//=================================================

            if(isset($log_eleve)){
                    //for($i=0;$i<count($log_eleve);$i++){
                for($i=0;$i<$indice_max_log_eleve;$i++){

                    //echo "\$log_eleve[$i]=$log_eleve[$i]<br />\n";
                    if(isset($log_eleve[$i])) {
                        // On supprime le suffixe indiquant la période:
                        $reg_eleve_login=preg_replace("/_t".$k."$/","",$log_eleve[$i]);

                        //echo "\$i=$i<br />";
                        //echo "\$reg_eleve_login=$reg_eleve_login<br />";

                        // La période est-elle ouverte?
                        if (in_array($reg_eleve_login, $current_group["eleves"][$k]["list"])) {
                            $eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$reg_eleve_login]["classe"]]["id"];
                            //if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] == "N"){
                            if(($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]=="N")||
                                (($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]!="O")&&($_SESSION['statut']=='secours'))||
                                (($acces_exceptionnel_complet=="y")&&($_SESSION['statut']=='professeur'))) {

                                $nom_log = "app_eleve_".$k."_".$i;

                                //echo "\$nom_log=$nom_log<br />";

                                if (isset($NON_PROTECT[$nom_log])){
                                    $app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
                                }
                                else{
                                    $app = "";
                                }

                                //echo "\$app=$app<br />";
                                //echo "<pre style='color: red'>$reg_eleve_login: $app</pre>\n";

                                // Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
                                $app=suppression_sauts_de_lignes_surnumeraires($app);
                                //echo "<pre style='color: green'>$reg_eleve_login: $app</pre>\n";


                                //=========================
                                // 20100604
                                // Ménage: pour ne pas laisser une demande de validation de correction alors qu'on a rouvert la période en saisie... on risquerait d'écraser par la suite l'enregistrement fait après la rouverture de période.
                                $sql="DELETE FROM matieres_app_corrections WHERE (login='$reg_eleve_login' AND id_groupe='".$current_group["id"]."' AND periode='$k');";
                                $del=mysqli_query($GLOBALS["mysqli"], $sql);
                                //=========================


                                $test_eleve_app_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
                                $test = mysqli_num_rows($test_eleve_app_query);
                                if ($test != "0") {
                                    if ($app != "") {
                                        $register = mysqli_query($GLOBALS["mysqli"], "UPDATE matieres_appreciations SET appreciation='" . $app . "' WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
                                    } else {
                                        $register = mysqli_query($GLOBALS["mysqli"], "DELETE FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
                                    }
                                    if (!$register) {
                                        $msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour l'élève $reg_eleve_login<br />";
                                    }
                                } else {
                                    if ($app != "") {
                                        $register = mysqli_query($GLOBALS["mysqli"], "INSERT INTO matieres_appreciations SET login='$reg_eleve_login',id_groupe='" . $current_group["id"]."',periode='$k',appreciation='" . $app . "'");
                                        if (!$register) {
                                            $msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour l'élève $reg_eleve_login<br />";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $k++;
	}

	if($msg=='') {
            // On ne vide que si l'enregistrement s'est bien passé

            // A partir de là, toutes les appréciations ont été sauvegardées proprement, on vide la table tempo
            $effacer = mysqli_query($GLOBALS["mysqli"], "DELETE FROM matieres_appreciations_tempo WHERE id_groupe = '".$id_groupe."'")
            OR die('Erreur dans l\'effacement de la table temporaire (1) :'.mysqli_error($GLOBALS["mysqli"]));
	}

	if($msg=="") {
            $affiche_message = 'yes';
	}
}
elseif((isset($_POST['correction_login_eleve']))&&(isset($_POST['correction_periode']))&&(isset($_POST['no_anti_inject_correction_app_eleve']))) {
    check_token();

    // Dispositif pour proposer des corrections une fois la période close.
    $correction_login_eleve=$_POST['correction_login_eleve'];
    $correction_periode=$_POST['correction_periode'];
    /*
    f_write_tmp("\$correction_login_eleve=".$correction_login_eleve);
    f_write_tmp("\$correction_periode=".$correction_periode);
    f_write_tmp("\$id_classe=".$id_classe);
    f_write_tmp("\$ver_periode[$correction_periode]=".$ver_periode[$correction_periode]);
    */
    // La période est supposée complètement verrouillée.
    $ver_periode_classe_correction_eleve="O";
    foreach($current_group['eleves'][$correction_periode]['telle_classe'] as $tmp_id_classe => $tmp_tab_login_ele) {
        if(in_array($correction_login_eleve, $tmp_tab_login_ele)) {
            // On a trouvé la classe de l'élève
            $ver_periode_classe_correction_eleve=$current_group['classe']['ver_periode'][$tmp_id_classe][$correction_periode];
            break;
        }
    }

	// On n'utilise le dispositif que pour des périodes partiellement closes
	// Problème: $id_classe n'est pas défini si c'un un groupe multiclasse
	//if($ver_periode[$correction_periode]=='P') {
	if($ver_periode_classe_correction_eleve=='P') {

		$mode_app="proposition";
		$autorisation_exceptionnelle_de_saisie='n';
		$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite, mode FROM matieres_app_delais WHERE id_groupe='$id_groupe' AND periode='$correction_periode';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$date_limite=$lig->date_limite;
	
			$date_courante=time();

			if($date_courante<$date_limite) {
				$autorisation_exceptionnelle_de_saisie='y';
			}

			$mode_app="acces_complet";
		}

		$saisie_valide='n';

		if(mb_substr(getSettingValue('autoriser_correction_bulletin_hors_delais'),0,1)=='y') {
			// La proposition de correction est autorisée même si aucune appréciation n'était saisie avant fermeture de la période.
			$saisie_valide='y';
		}
		elseif($autorisation_exceptionnelle_de_saisie=='y') {
			// Il y a une autorisation exceptionnelle de saisie
			$saisie_valide='y';
		}
		else {
			// On contrôle s'il y avait une appréciation saisie avant la fermeture de période
			$sql="SELECT 1=1 FROM matieres_appreciations WHERE login='$correction_login_eleve' AND id_groupe='$id_groupe' AND periode='$correction_periode' AND appreciation!='';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				// Il y avait une appréciation saisie
				// Si l'autorisation de proposition de correction est donnée, c'est OK
				// Sinon, on contrôle quand même s'il y a une autorisation exceptionnelle
				if(mb_substr(getSettingValue('autoriser_correction_bulletin'),0,1)=='y') {
					$saisie_valide='y';
				}
			}
		}


		if($saisie_valide!='y') {
			$msg.="ERREUR: La saisie n'est pas autorisée.<br />";
		}
		else {
		
			//echo "BLABLA";
		
			// Un test check_prof_groupe($_SESSION['login'],$current_group["id"]) est fait plus haut pour contrôler que le prof est bien associé à ce groupe
		
			if (isset($NON_PROTECT["correction_app_eleve"])) {
				$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT["correction_app_eleve"]));
				// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
				$app=nettoyage_retours_ligne_surnumeraires($app);

				$texte_mail="";
		
				$correction_nom_prenom_eleve=get_nom_prenom_eleve($correction_login_eleve);
		
				if((mb_strlen(preg_replace('/[A-Za-z0-9._-]/','',$correction_login_eleve))!=0)||
				(mb_strlen(preg_replace('/[0-9]/','',$correction_periode))!=0)) {
					$msg.="Des caractères invalides sont proposés pour le login élève $correction_nom_prenom_eleve ou pour la période $correction_periode.<br />";
				}
				else {
		
					$sql="SELECT 1=1 FROM j_eleves_groupes WHERE login='$correction_login_eleve' AND periode='$correction_periode' AND id_groupe='$id_groupe';";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)==0) {
						$msg.="L'élève $correction_nom_prenom_eleve n'est pas associé au groupe n°$id_groupe pour la période $correction_periode.<br />";
					}
					else {

						// 20131204
						if($mode_app=="acces_complet") {
							// On valide la saisie



						}
						else {
							$sql="SELECT * FROM matieres_app_corrections WHERE (login='$correction_login_eleve' AND id_groupe='$id_groupe' AND periode='$correction_periode');";
							fich_debug_proposition_correction_app($prefixe_debug." : $sql\n");
							$test_correction=mysqli_query($GLOBALS["mysqli"], $sql);
							$test=mysqli_num_rows($test_correction);
							if ($test!="0") {
								if ($app!="") {
									$sql="UPDATE matieres_app_corrections SET appreciation='$app' WHERE (login='$correction_login_eleve' AND id_groupe='$id_groupe' AND periode='$correction_periode');";
									fich_debug_proposition_correction_app($prefixe_debug." : $sql\n");
									$register=mysqli_query($GLOBALS["mysqli"], $sql);
									if (!$register) {
										$msg = $msg."Erreur lors de l'enregistrement des corrections pour <a href='".$_SERVER['PHP_SELF']."#saisie_app_".$correction_login_eleve."' title=\"Aller à l'appréciation proposée pour élève.\">$correction_nom_prenom_eleve</a> sur la période $correction_periode.<br />";
										fich_debug_proposition_correction_app($prefixe_debug." : Erreur lors de l'enregistrement de la proposition de correction pour $correction_login_eleve\n");
									}
									else {
										$msg.="Enregistrement de la proposition de correction pour <a href='".$_SERVER['PHP_SELF']."#saisie_app_".$correction_login_eleve."' title=\"Aller à l'appréciation proposée pour élève.\">$correction_nom_prenom_eleve</a> sur la période $correction_periode effectué.<br />";
										$texte_mail.="Une correction proposée a été mise à jour par ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj')."\r\npour l'élève ".$correction_nom_prenom_eleve." sur la période $correction_periode\r\nen ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].").\r\n\r\nVous pouvez valider ou rejeter la proposition en vous connectant avec un compte de statut scolarité ou secours.\r\nVous trouverez en page d'accueil, dans la rubrique Saisie, un message en rouge concernant la Correction de bulletins.\r\n";
										fich_debug_proposition_correction_app($prefixe_debug." : Proposition de correction soumise.\nTexte du mail : \n".$texte_mail."\n");
									}
								} else {
									$sql="DELETE FROM matieres_app_corrections WHERE (login='$correction_login_eleve' AND id_groupe='$id_groupe' AND periode='$correction_periode');";
									fich_debug_proposition_correction_app($prefixe_debug." : $sql\n");
									$register=mysqli_query($GLOBALS["mysqli"], $sql);
									if (!$register) {
										$msg = $msg."Erreur lors de la suppression de la proposition de correction pour <a href='".$_SERVER['PHP_SELF']."#saisie_app_".$correction_login_eleve."' title=\"Aller à l'appréciation proposée pour élève.\">$correction_nom_prenom_eleve</a> sur la période $correction_periode.<br />";
										fich_debug_proposition_correction_app($prefixe_debug." : Erreur lors de la suppression de la proposition de correction pour $correction_login_eleve\n");
									}
									else {
										$msg.="Suppression de la proposition de correction pour <a href='".$_SERVER['PHP_SELF']."#saisie_app_".$correction_login_eleve."' title=\"Aller à l'appréciation proposée pour élève.\">$correction_nom_prenom_eleve</a> sur la période $correction_periode effectuée.<br />";
										$texte_mail.="Suppression de la proposition de correction pour l'élève $correction_nom_prenom_eleve\r\nsur la période $correction_periode en ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].")\r\npar ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj').".\n";
										fich_debug_proposition_correction_app($prefixe_debug." : Suppression de la proposition de correction.\nTexte du mail : \n".$texte_mail."\n");
									}
								}
				
							}
							else {
								if ($app != "") {
									$sql="INSERT INTO matieres_app_corrections SET login='$correction_login_eleve', id_groupe='$id_groupe', periode='$correction_periode', appreciation='".$app."';";
									fich_debug_proposition_correction_app($prefixe_debug." : $sql\n");
									$register=mysqli_query($GLOBALS["mysqli"], $sql);
									if (!$register) {
										$msg = $msg."Erreur lors de l'enregistrement de la proposition de correction pour <a href='".$_SERVER['PHP_SELF']."#saisie_app_".$correction_login_eleve."' title=\"Aller à l'appréciation proposée pour élève.\">$correction_nom_prenom_eleve</a> sur la période $correction_periode.<br />";
										fich_debug_proposition_correction_app($prefixe_debug." : Erreur lors de l'enregistrement de la proposition de correction pour $correction_login_eleve\n");
									}
									else {
										$msg.="Enregistrement de la proposition de correction pour <a href='".$_SERVER['PHP_SELF']."#saisie_app_".$correction_login_eleve."' title=\"Aller à l'appréciation proposée pour élève.\">$correction_nom_prenom_eleve</a> sur la période $correction_periode effectué.<br />";
										$texte_mail.="Une correction a été proposée par ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj')."\r\npour l'élève $correction_nom_prenom_eleve sur la période $correction_periode\r\nen ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].").\r\n\r\nVous pouvez valider ou rejeter la proposition en vous connectant avec un compte de statut scolarité ou secours.\r\nVous trouverez en page d'accueil, dans la rubrique Saisie, un message en rouge concernant la Correction de bulletins.\r\n";
										fich_debug_proposition_correction_app($prefixe_debug." : Proposition de correction soumise.\nTexte du mail : \n".$texte_mail."\n");
									}
								}
							}

							if($texte_mail!="") {
								$msg.=envoi_mail_proposition_correction($correction_login_eleve, $id_groupe, $correction_periode, $texte_mail);
							}
						}
					}
				}
			}
		}
	}
}
elseif((isset($_POST['correction_periode']))&&(isset($_POST['no_anti_inject_correction_app_groupe']))) {
	check_token();

	// Dispositif pour proposer des corrections une fois la période close.
	$correction_periode=$_POST['correction_periode'];

	// On n'utilise le dispositif que pour des périodes partiellement closes
	//if($ver_periode[$correction_periode]=='P') {
	if((($current_group["classe"]["ver_periode"]['all'][$correction_periode] != 3)&&($_SESSION['statut']!='secours'))||
	(($current_group["classe"]["ver_periode"]['all'][$correction_periode]==0)&&($_SESSION['statut']=='secours'))) {

		$app_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_appreciations_grp WHERE (id_groupe = '" . $current_group["id"] . "' AND periode='$correction_periode')");
		$app_grp[$correction_periode] = @old_mysql_result($app_query, 0, "appreciation");

		if(
			(
				($current_group["classe"]["ver_periode"]['all'][$correction_periode] == 1)&&

				(
					($app_grp[$correction_periode]!='')||
					(mb_substr(getSettingValue('autoriser_correction_bulletin_hors_delais'),0,1)=='y')
				)
				&&(mb_substr(getSettingValue('autoriser_correction_bulletin'),0,1)=='y')
			)||
			($tab_autorisation_exceptionnelle_de_saisie[$correction_periode]=='y')
		) {
			$autorisation_exceptionnelle_de_saisie='n';
			$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM matieres_app_delais WHERE id_groupe='$id_groupe' AND periode='$correction_periode';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);
				$date_limite=$lig->date_limite;
	
				$date_courante=time();
	
				if($date_courante<$date_limite) {
					$autorisation_exceptionnelle_de_saisie='y';
				}
			}
	
			$saisie_valide='n';

			if(mb_substr(getSettingValue('autoriser_correction_bulletin_hors_delais'),0,1)=='y') {
				// La proposition de correction est autorisée même si aucune appréciation n'était saisie avant fermeture de la période.
				$saisie_valide='y';
			}
			elseif($autorisation_exceptionnelle_de_saisie=='y') {
				// Il y a une autorisation exceptionnelle de saisie
				$saisie_valide='y';
			}
			else {
				// On contrôle s'il y avait une appréciation saisie avant la fermeture de période
				$sql="SELECT 1=1 FROM matieres_appreciations_grp WHERE id_groupe='$id_groupe' AND periode='$correction_periode' AND appreciation!='';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					// Il y avait une appréciation saisie
					// Si l'autorisation de proposition de correction est donnée, c'est OK
					// Sinon, on contrôle quand même s'il y a une autorisation exceptionnelle
					if(mb_substr(getSettingValue('autoriser_correction_bulletin'),0,1)=='y') {
						$saisie_valide='y';
					}
				}
			}

			if($saisie_valide!='y') {
				$msg.="ERREUR: La saisie n'est pas autorisée.<br />";
			}
			else {
		
				//echo "BLABLA";
		
				// Un test check_prof_groupe($_SESSION['login'],$current_group["id"]) est fait plus haut pour contrôler que le prof est bien associé à ce groupe
		
				if (isset($NON_PROTECT["correction_app_groupe"])) {
					$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT["correction_app_groupe"]));
					// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
					$app=nettoyage_retours_ligne_surnumeraires($app);

					$texte_mail="";

					$sql="SELECT * FROM matieres_app_corrections WHERE (login='' AND id_groupe='$id_groupe' AND periode='$correction_periode');";
					$test_correction=mysqli_query($GLOBALS["mysqli"], $sql);
					$test=mysqli_num_rows($test_correction);
					if ($test!="0") {
						if ($app!="") {
							$sql="UPDATE matieres_app_corrections SET appreciation='$app' WHERE (login='' AND id_groupe='$id_groupe' AND periode='$correction_periode');";
							$register=mysqli_query($GLOBALS["mysqli"], $sql);
							if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des corrections pour $correction_nom_prenom_eleve sur la période $correction_periode.<br />";} 
							else {
								$msg.="Enregistrement de la proposition de correction pour l'appréciation de groupe sur la période $correction_periode effectué.<br />";
								$texte_mail.="Une correction proposée a été mise à jour par ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj')."\r\npour l'appréciation de groupe sur la période $correction_periode\r\nen ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].").\r\n\r\nVous pouvez valider ou rejeter la proposition en vous connectant avec un compte de statut scolarité ou secours.\r\nVous trouverez en page d'accueil, dans la rubrique Saisie, un message en rouge concernant la Correction de bulletins.\r\n";
							}
						} else {
							$sql="DELETE FROM matieres_app_corrections WHERE (login='' AND id_groupe='$id_groupe' AND periode='$correction_periode');";
							$register=mysqli_query($GLOBALS["mysqli"], $sql);
							if (!$register) {$msg = $msg."Erreur lors de la suppression de la proposition de correction pour l'appréciation de groupe sur la période $correction_periode.<br />";} 
							else {
								$msg.="Suppression de la proposition de correction pour l'appréciation de groupe sur la période $correction_periode effectuée.<br />";
								$texte_mail.="Suppression de la proposition de correction pour l'appréciation de groupe\r\nsur la période $correction_periode en ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].")\r\npar ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj').".\r\n";
							}
						}
		
					}
					else {
						if ($app != "") {
							$sql="INSERT INTO matieres_app_corrections SET login='', id_groupe='$id_groupe', periode='$correction_periode', appreciation='".$app."';";
							$register=mysqli_query($GLOBALS["mysqli"], $sql);
							if (!$register) {$msg = $msg."Erreur lors de l'enregistrement de la proposition de correction pour l'appréciation de groupe sur la période $correction_periode.<br />";}
							else {
								$msg.="Enregistrement de la proposition de correction pour l'appréciation de groupe sur la période $correction_periode effectué.<br />";
								$texte_mail.="Une correction a été proposée par ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj')."\r\npour l'appréciation de groupe sur la période $correction_periode\r\nen ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].").\r\n\r\nVous pouvez valider ou rejeter la proposition en vous connectant avec un compte de statut scolarité ou secours.\r\nVous trouverez en page d'accueil, dans la rubrique Saisie, un message en rouge concernant la Correction de bulletins.\r\n";
							}
						}
					}

					if($texte_mail!="") {
						$envoi_mail_actif=getSettingValue('envoi_mail_actif');
						if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
							$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
						}
		
						if($envoi_mail_actif=='y') {
							$email_destinataires="";
							//$sql="select email from utilisateurs where statut='secours' AND email!='';";
							$sql="(select email from utilisateurs where statut='secours' AND email!='')";
							$sql.=" UNION (select email from utilisateurs u, j_scol_classes jsc, j_groupes_classes jgc where u.statut='scolarite' AND u.email!='' AND u.login=jsc.login AND jsc.id_classe=jgc.id_classe AND jgc.id_groupe='$id_groupe')";
							//echo "$sql<br />";
							$req=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($req)>0) {
								$lig_u=mysqli_fetch_object($req);
								$email_destinataires=$lig_u->email;
								$tab_param_mail['destinataire'][]=$lig_u->email;
								while($lig_u=mysqli_fetch_object($req)) {
									$email_destinataires=", ".$lig_u->email;
									$tab_param_mail['destinataire'][]=$lig_u->email;
								}
		
								$email_declarant="";
								$nom_declarant="";
								$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
								$req=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($req)>0) {
									$lig_u=mysqli_fetch_object($req);
									$nom_declarant=$lig_u->civilite." ".casse_mot($lig_u->nom,'maj')." ".casse_mot($lig_u->prenom,'majf');
									$email_declarant=$lig_u->email;
									$tab_param_mail['cc'][]=$lig_u->email;
									$tab_param_mail['cc_name'][]=$nom_declarant;
								}
		
								$email_autres_profs_grp="";
								// Recherche des autres profs du groupe
								$sql="SELECT DISTINCT u.email FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.id_groupe='$id_groupe' AND jgp.login=u.login AND u.login!='".$_SESSION['login']."' AND u.email!='';";
								//echo "$sql<br />";
								$req=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($req)>0) {
									$lig_u=mysqli_fetch_object($req);
									$email_autres_profs_grp.=$lig_u->email;
									$tab_param_mail['cc'][]=$lig_u->email;
									while($lig_u=mysqli_fetch_object($req)) {
										$email_autres_profs_grp.=",".$lig_u->email;
										$tab_param_mail['cc'][]=$lig_u->email;
									}
								}
		
								$sujet_mail="Demande de validation de correction d'appréciation";

								$ajout_header="";
								if($email_declarant!="") {
									$ajout_header.="Cc: $nom_declarant <".$email_declarant.">";
									if($email_autres_profs_grp!='') {
										$ajout_header.=", $email_autres_profs_grp";
									}
									$ajout_header.="\r\n";
									$ajout_header.="Reply-to: $nom_declarant <".$email_declarant.">\r\n";
									$tab_param_mail['replyto']=$email_declarant;
									$tab_param_mail['replyto_name']=$nom_declarant;

								}
								elseif($email_autres_profs_grp!='') {
									$ajout_header.="Cc: $email_autres_profs_grp\r\n";
								}

								$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
								$texte_mail=$salutation.",\n\n".$texte_mail."\nCordialement.\n-- \n".$nom_declarant;

								$envoi = envoi_mail($sujet_mail, $texte_mail, $email_destinataires, $ajout_header, "plain", $tab_param_mail);
							}
						}
					}
				}
			}
		}
	}
}

if (!isset($periode_cn)) $periode_cn = 0;

$themessage = 'Des appréciations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";
$utilisation_prototype = "ok";
$javascript_specifique = "saisie/scripts/js_saisie";
//**************** EN-TETE *****************
$titre_page = "Saisie des appréciations";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

$acces_visu_eleve=acces('/eleves/visu_eleve.php', $_SESSION['statut']);

$tmp_timeout=(getSettingValue("sessionMaxLength"))*60;

?>
<script type="text/javascript" >
change = 'no';

</script>
<?php

$matiere_nom = $current_group["matiere"]["nom_complet"];

echo "<form enctype=\"multipart/form-data\" action=\"saisie_appreciations.php\" name='form1' method=\"post\">\n";

echo "<p class='bold'>\n";
echo "<input type='hidden' name='id_groupe' value='".$id_groupe."' />";

if (($periode_cn != 0)&&($_SESSION['statut']!='secours')) {
	echo "<a href=\"../cahier_notes/index.php?id_groupe=$id_groupe&amp;periode_num=$periode_cn\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
} else {
	echo "<a href=\"index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil saisie</a>\n";
}
echo " | <a href='saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$periode_cn' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les moyennes</a>";
// enregistrement du chemin de retour pour la fonction imprimer
if($_SERVER['QUERY_STRING']!='') {
	$_SESSION['chemin_retour'] = $_SERVER['PHP_SELF']."?". $_SERVER['QUERY_STRING'];
}
else {
	$_SESSION['chemin_retour'] = $_SERVER['PHP_SELF']."?id_groupe=$id_groupe";
}
echo " | <a href='../prepa_conseil/index1.php?id_groupe=$id_groupe' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Accéder à la page 'Mes moyennes et appréciations'.
Vous pourrez y choisir ce que vous souhaitez extraire/imprimer parmi les moyennes, appréciations, rang,... des différentes périodes.\">Imprimer</a>\n";

//=========================
echo " | <a href='index.php?id_groupe=" . $current_group["id"] . "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Import/Export notes et appréciations</a> |";
//=========================

if(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='secours')) {
	$login_prof_groupe_courant="";
	$tab_groups=array();
	if($_SESSION['statut']=='professeur') {
		$login_prof_groupe_courant=$_SESSION["login"];
	}
	else {
		$tmp_current_group=get_group($id_groupe);

		if(isset($tmp_current_group["profs"]["list"][0])) {
			$login_prof_groupe_courant=$tmp_current_group["profs"]["list"][0];
		}
	}

	if($login_prof_groupe_courant!='') {
		$tab_groups = get_groups_for_prof($login_prof_groupe_courant,"classe puis matière");
	}

	if(!empty($tab_groups)) {

		// Pour s'assurer de ne pas avoir deux fois le même groupe...
		$tmp_group=array();

		$chaine_options_classes="";

		$tmp_groups=array();
		for($loop=0;$loop<count($tab_groups);$loop++) {
			if((!isset($tab_groups[$loop]["visibilite"]["bulletins"]))||($tab_groups[$loop]["visibilite"]["bulletins"]=='y')) {
				$tmp_groups[]=$tab_groups[$loop];
			}
		}

		$num_groupe=-1;
		$nb_groupes_suivies=count($tmp_groups);

		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		for($loop=0;$loop<count($tmp_groups);$loop++) {
			if((!isset($tmp_groups[$loop]["visibilite"]["bulletins"]))||($tmp_groups[$loop]["visibilite"]["bulletins"]=='y')) {
				if($tmp_groups[$loop]['id']==$id_groupe){
					$num_groupe=$loop;

					$chaine_options_classes.="<option value='".$tmp_groups[$loop]['id']."' selected='selected'>".$tmp_groups[$loop]['description']." (".$tmp_groups[$loop]['classlist_string'].")</option>\n";
	
					$temoin_tmp=1;
					if(isset($tmp_groups[$loop+1])){
						$id_grp_suiv=$tmp_groups[$loop+1]['id'];
					}
					else{
						$id_grp_suiv=0;
					}
				}
				else {
					$chaine_options_classes.="<option value='".$tmp_groups[$loop]['id']."'>".$tmp_groups[$loop]['description']." (".$tmp_groups[$loop]['classlist_string'].")</option>\n";
				}
	
				if($temoin_tmp==0){
					$id_grp_prec=$tmp_groups[$loop]['id'];
				}
			}
		}
		// =================================

		if(isset($id_grp_prec)){
			if($id_grp_prec!=0){
				echo " <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_prec&amp;periode_cn=$periode_cn";
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\" title='Enseignement précédent'><img src='../images/icons/back.png' class='icone16' alt='Enseignement précédent' /></a>";
			}
		}

		if(($chaine_options_classes!="")&&($nb_groupes_suivies>1)) {

			echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_groupe').selectedIndex=$num_groupe;
			}
		}
	}
</script>\n";

			echo " <select name='id_groupe' id='id_groupe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
			echo $chaine_options_classes;
			echo "</select>\n";
		}

		if(isset($id_grp_suiv)){
			if($id_grp_suiv!=0){
				echo " <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_suiv&amp;periode_cn=$periode_cn";
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\" title='Enseignement suivant'><img src='../images/icons/forward.png' class='icone16' alt='Enseignement suivant' /></a>";
				}
		}
	}
	// =================================
}

if($_SESSION['statut']=='professeur') {
	echo " | <a href=\"../groupes/signalement_eleves.php?id_groupe=$id_groupe&amp;chemin_retour=../cahier_notes/index.php?id_groupe=$id_groupe\" title=\"Si certains élèves sont affectés à tort dans cet enseignement, ou si il vous manque certains élèves, vous pouvez dans cette page signaler l'erreur à l'administrateur Gepi.\" onclick=\"return confirm_abandon (this, change, '$themessage')\"> Signaler des erreurs d'affectation <img src='../images/icons/ico_attention.png' class='icone16' alt='Erreur' /></a>";
}

echo "</p>\n";
if(isset($periode_cn)) {
	echo "<input type='hidden' name='periode_cn' value='$periode_cn' />\n";
}
echo "</form>\n";

// Largeur des textarea
$saisie_app_nb_cols_textarea=getPref($_SESSION["login"],'saisie_app_nb_cols_textarea',100);

// 20150316
if(getSettingValue('active_recherche_lapsus')!='n') {
	$tab_lapsus_et_correction=retourne_tableau_lapsus_et_correction();
}

?>
<form enctype="multipart/form-data" action="saisie_appreciations.php" method="post">
<?php
echo add_token_field(true);

//=========================
if($proposer_liens_enregistrement=="y") {
	$insert_mass_appreciation_type=getSettingValue("insert_mass_appreciation_type");
	if ($insert_mass_appreciation_type=="y") {
		// INSERT INTO setting SET name='insert_mass_appreciation_type', value='y';

		$sql="CREATE TABLE IF NOT EXISTS b_droits_divers (login varchar(50) NOT NULL default '', nom_droit varchar(50) NOT NULL default '', valeur_droit varchar(50) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

		// Pour tester:
		// INSERT INTO b_droits_divers SET login='toto', nom_droit='insert_mass_appreciation_type', valeur_droit='y';

		$sql="SELECT 1=1 FROM b_droits_divers WHERE login='".$_SESSION['login']."' AND nom_droit='insert_mass_appreciation_type' AND valeur_droit='y';";
		$res_droit=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_droit)>0) {
			$droit_insert_mass_appreciation_type="y";
		}
		else {
			$droit_insert_mass_appreciation_type="n";
		}

		if($droit_insert_mass_appreciation_type=="y") {
			echo "<div style='float:right; width:150px; border: 1px solid black; background-color: white; font-size: small; text-align:center;'>\n";
			echo "Insérer l'appréciation-type suivante pour toutes les appréciations vides: ";
			echo "<input type='text' name='ajout_a_textarea_vide' id='ajout_a_textarea_vide' value='-' size='10' /><br />\n";
			echo "<input type='button' name='ajouter_a_textarea_vide' value='Ajouter' onclick='ajoute_a_textarea_vide()' /><br />\n";
			echo "</div>\n";
		}
	}
}
//=========================
$chaine_date_conseil_classe=affiche_date_prochain_conseil_de_classe_groupe($id_groupe, $current_group);
echo $chaine_date_conseil_classe;
//=========================

if($proposer_liens_enregistrement=="y") {
	echo "<p class='center'><input type='submit' value='Enregistrer' /></p>\n";
}

//===========================================================
echo "<div id='div_photo_eleve' class='infobulle_corps' style='position: fixed; top: 220px; right: 20px; text-align:center; border:1px solid black; display:none;'></div>\n";
//echo "<div id='div_photo_eleve' class='infobulle_corps' style='position: fixed; top: 220px; right: 20px; text-align:center; background-color:white; border:1px solid black; display:none;'></div>\n";
//echo "<div id='div_photo_eleve' style='position: fixed; top: 220px; right: 200px; text-align:center; border:1px solid black;'>&nbsp;</div>\n";

//===========================================================
echo "<div id='div_bull_simp' style='position: absolute; top: 220px; right: 20px; width: 700px; text-align:center; color: black; padding: 0px; border:1px solid black; display:none;'>\n";

	echo "<div class='infobulle_entete' style='color: #ffffff; cursor: move; width: 700px; font-weight: bold; padding: 0px;' onmousedown=\"dragStart(event, 'div_bull_simp')\">\n";
		echo "<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>\n";
		echo "<a href='#' onClick=\"cacher_div('div_bull_simp');return false;\">\n";
		echo "<img src='../images/icons/close16.png' style=\"width:16px; height:16px\" alt='Fermer' />\n";
		echo "</a>\n";
		echo "</div>\n";

		echo "<div id='titre_entete_bull_simp'></div>\n";
	echo "</div>\n";
	
	echo "<div id='corps_bull_simp' class='infobulle_corps' style='color: #ffffff; cursor: move; font-weight: bold; padding: 0px; height: 15em; width: 700px; overflow: auto;'>";
	echo "</div>\n";

echo "</div>\n";
//===========================================================

echo "<h2 class='gepi'>Bulletin scolaire - Saisie des appréciations</h2>\n";

if($proposer_liens_enregistrement=="y") {
	echo "<p>Vous pouvez faire apparaître dans votre appréciation la liste des notes de l'élève pour la période en insérant la chaine de caractères <b>@@Notes</b><br />(<i>les notes apparaîtront alors lors de la visualisation/impression du bulletin</i>)<br />Insérer d'un clic @@Notes <a href=\"javascript:inserer_notes_dans_app('debut');changement()\">au début</a> ou <a href=\"javascript:inserer_notes_dans_app('fin');changement()\">à la fin</a> de toutes les appréciations.</p>\n";
}

//echo "<p><b>Groupe : " . $current_group["description"] ." | Matière : $matiere_nom</b></p>\n";
echo "<p><b>Groupe : " . htmlspecialchars($current_group["description"]) ." (".$current_group["classlist_string"].")</b></p>\n";

if ($multiclasses) {
	echo "<p>Affichage :";
	echo "<br/>-> <a href='saisie_appreciations.php?id_groupe=$id_groupe&amp;order_by=classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Regrouper les élèves par classe</a>";
	echo "<br/>-> <a href='saisie_appreciations.php?id_groupe=$id_groupe&amp;order_by=nom' onclick=\"return confirm_abandon (this, change, '$themessage')\">Afficher la liste par ordre alphabétique</a>";
	echo "</p>\n";
}

// On commence par mettre la liste dans l'ordre souhaité
if ($order_by != "classe") {
	$liste_eleves = $current_group["eleves"]["all"]["list"];
} else {
	// Ici, on tri par classe
	// On va juste créer une liste des élèves pour chaque classe
	$tab_classes = array();
	foreach($current_group["classes"]["list"] as $classe_id) {
		$tab_classes[$classe_id] = array();
	}
	// On passe maintenant élève par élève et on les met dans la bonne liste selon leur classe
	foreach($current_group["eleves"]["all"]["list"] as $eleve_login) {
		$classe = $current_group["eleves"]["all"]["users"][$eleve_login]["classe"];
		$tab_classes[$classe][] = $eleve_login;
	}
	// On met tout ça à la suite
	$liste_eleves = array();
	foreach($current_group["classes"]["list"] as $classe_id) {
		$liste_eleves = array_merge($liste_eleves, $tab_classes[$classe_id]);
	}
}


// Fonction de renseignement du champ qui doit obtenir le focus après validation
echo "<script type='text/javascript'>

function inserer_notes_dans_app(position) {
	champs_textarea=document.getElementsByTagName('textarea');
	//alert('champs_textarea.length='+champs_textarea.length);
	for(i=0;i<champs_textarea.length;i++){
		if(champs_textarea[i].name.substring(0,24)=='no_anti_inject_app_eleve') {
			if(champs_textarea[i].value.indexOf('@@Notes')=='-1') {
				if(position=='debut') {
					champs_textarea[i].value='@@Notes '+champs_textarea[i].value;
				}
				else {
					champs_textarea[i].value=champs_textarea[i].value+' @@Notes';
				}
			}
		}
	}
}

function focus_suivant(num){
	temoin='';
	// La variable 'dernier' peut dépasser de l'effectif de la classe... mais cela n'est pas dramatique
	dernier=num+".count($liste_eleves)."
	// On parcourt les champs à partir de celui de l'élève en cours jusqu'à rencontrer un champ existant
	// (pour réussir à passer un élève qui ne serait plus dans la période)
	// Après validation, c'est ce champ qui obtiendra le focus si on n'était pas à la fin de la liste.
	for(i=num;i<dernier;i++){
		suivant=i+1;
		if(temoin==''){
			if(document.getElementById('n'+suivant)){
				document.getElementById('info_focus').value=suivant;
				temoin=suivant;
			}
		}
	}

	document.getElementById('info_focus').value=temoin;
}

</script>\n";

// ====================== Modif pour la sauvegarde en ajax =================
	$restauration = isset($_GET["restauration"]) ? $_GET["restauration"] : NULL;


	if ($restauration == NULL) {
		// On supprime les appreciation_tempo qui sont identiques aux appreciations enregistrées dans matieres_appreciations
		$sql="SELECT mat.* FROM matieres_appreciations_tempo mat, matieres_appreciations ma WHERE
			(mat.id_groupe='".$current_group["id"]."' AND mat.id_groupe=ma.id_groupe AND mat.periode=ma.periode AND mat.login=ma.login AND mat.appreciation=ma.appreciation);";
		$res_app_identiques=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_app_identiques)>0) {
			while($lig_app_id=mysqli_fetch_object($res_app_identiques)) {
				$sql="DELETE FROM matieres_appreciations_tempo WHERE login='$lig_app_id->login' AND id_groupe='".$current_group["id"]."' AND periode='$lig_app_id->periode';";
				//echo "$sql<br />";
				$menage=mysqli_query($GLOBALS["mysqli"], $sql);
			}
		}
	}

	// On teste s'il existe des données dans la table matieres_appreciations_tempo
	$sql_test = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM matieres_appreciations_tempo WHERE id_groupe = '" . $current_group["id"] . "'");
	$test = mysqli_num_rows($sql_test);
	if ($test !== 0 AND $restauration == NULL) {
		// On envoie un message à l'utilisateur
		echo "
		<p class=\"red\">Certaines appréciations n'ont pas été enregistrées dans une table temporaire lors de votre dernière saisie.<br />
			Elles sont indiquées ci-dessous en rouge. Voulez-vous les restaurer ?
		</p>
		<p class=\"red\">
		<a href=\"./saisie_appreciations.php?id_groupe=".$current_group["id"]."&amp;restauration=oui".add_token_in_url()."\">OUI</a>
		<em>(elles remplaceront le contenu actuel des champs de saisie)</em>
			-
		<a href=\"./saisie_appreciations.php?id_groupe=".$current_group["id"]."&amp;restauration=non".add_token_in_url()."\">NON</a>
		<em>(elles seront alors définitivement perdues)</em>
		</p>
		";
	}

	// Dans tous les cas, si $restauration n'est pas NULL, il faut vider la table tempo des appréciations de ce groupe

//=================================
$chaine_champs_textarea_correction="";
$chaine_champs_input_correction="";
$cpt_correction=0;
//=================================
$k=1;
$num_id = 10;
while ($k < $nb_periode) {

    $app_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_appreciations_grp WHERE (id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
    $app_grp[$k] = @old_mysql_result($app_query, 0, "appreciation");

    $call_moyenne_t[$k] = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(n.note),1) moyenne FROM matieres_notes n, j_eleves_groupes j " .
                                                            "WHERE (" .
                                                            "n.id_groupe='" . $current_group["id"] ."' AND " .
                                                            "n.login = j.login AND " .
                                                            "n.statut='' AND " .
                                                            "j.id_groupe = n.id_groupe AND " .
                                                            "n.periode='$k' AND j.periode='$k'" .
                                                            ")");
    $moyenne_t[$k] = old_mysql_result($call_moyenne_t[$k], 0, "moyenne");

    if ($moyenne_t[$k]=='') {
            $moyenne_t[$k]="&nbsp;";
    }

    $mess[$k]="";
    $mess[$k].="<td>".$moyenne_t[$k]."</td>\n";
    $mess[$k].="<td>\n";
    if((($current_group["classe"]["ver_periode"]['all'][$k] != 3)&&($_SESSION['statut']!='secours'))||
    (($current_group["classe"]["ver_periode"]['all'][$k]==0)&&($_SESSION['statut']=='secours'))) {

        $mess[$k].=nl2br($app_grp[$k]);

        $sql="SELECT * FROM matieres_app_corrections WHERE (login='' AND id_groupe='".$current_group["id"]."' AND periode='$k');";
        $correct_app_query=mysqli_query($GLOBALS["mysqli"], $sql);
        if(mysqli_num_rows($correct_app_query)>0) {
            $lig_correct_app=mysqli_fetch_object($correct_app_query);
            $mess[$k].="<div style='color:darkgreen; border: 1px solid red;'><b>Proposition de correction en attente&nbsp;:</b><br />".nl2br($lig_correct_app->appreciation)."</div>\n";
        }

        if(
            (
                ($current_group["classe"]["ver_periode"]['all'][$k] == 1)&&
                (
                    ($app_grp[$k]!='')||
                    (mb_substr(getSettingValue('autoriser_correction_bulletin_hors_delais'),0,1)=='y')
                )
                &&(mb_substr(getSettingValue('autoriser_correction_bulletin'),0,1)=='y')
            )||
            ($tab_autorisation_exceptionnelle_de_saisie[$k]=='y')
        ) {
            $mess[$k].="<div style='float:right; width:2em; height:1em;'><a href='#' onclick=\"affiche_div_correction_groupe('$k');return false;\" alt='Proposer une correction' title='Proposer une correction'><img src='../images/edit16.png' style=\"width:16px; height:16px\" alt='Proposer une correction' title='Proposer une correction' /></a>";
            $chaine_champs_textarea_correction.="<textarea name='reserve_correction_app_grp_$k' id='reserve_correction_app_grp_$k'>".$app_grp[$k]."</textarea>\n";
            $mess[$k].="</div>\n";

            $cpt_correction++;
        }
        $mess[$k].="</td>\n<td style='text-align:left;'>\n";
		
		$elemProgramme = getGroupElemProg($current_group["id"], $anneeScolaire, $k);
		$cpt=FALSE;
		while($element = $elemProgramme->fetch_object()){
			if($cpt) {
				$mess[$k].="\n<br />\n";
			}
			$mess[$k].="- ".$element->libelle;
			$cpt = TRUE;
		}
		
    }
    else {
        if(!isset($id_premier_textarea)) {
            $id_premier_textarea=$k.$num_id;
        }

        $mess[$k].="<input type='hidden' name='app_grp_".$k."' value=\"".$app_grp[$k]."\" />\n";
        $mess[$k].="<textarea id=\"n".$k.$num_id."\" class='wrap' onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_grp_".$k."\" rows='2' cols='$saisie_app_nb_cols_textarea' onchange=\"changement()\"";
        $mess[$k].=" onfocus=\"focus_suivant(".$k.$num_id.");document.getElementById('focus_courant').value='".$k.$num_id."';";
        $mess[$k].="document.getElementById('div_photo_eleve').innerHTML='';";
        $mess[$k].="\"";
        $mess[$k].=">".$app_grp[$k]."</textarea>\n";
		// 20160617
		$mess[$k].="<div style='float:right; width:16px; margin-right:3px;' title=\"Corriger la ponctuation.\"><a href=\"#\" onclick=\"document.getElementById('n".$k.$num_id."').value=corriger_espaces_et_casse_ponctuation(document.getElementById('n".$k.$num_id."').value);changement();return false;\"><img src='../images/icons/wizard_ponctuation.png' class='icone16' alt='Ponctuation' /></a></div>";
	
        $mess[$k].="</td>\n";
        $mess[$k].="<td style='text-align:left;'>\n";
		
		$elemProgramme = getGroupElemProg($current_group["id"], $anneeScolaire, $k);
		$cpt=FALSE;
		while($element = $elemProgramme->fetch_object()){
			if($cpt) {
				$mess[$k].="\n<br />\n";
			}
			$mess[$k].="<input type='image' name='delElemProgGroup[$element->idEP]' src='../images/disabled.png' style =\"width:16px; height:16px \" alt=\"Supprimer l'élément\" title='Supprimer cet élément de programme pour tous les élèves' /> ";
			$mess[$k].="- ".$element->libelle;
			$cpt = TRUE;
		}
		
        $mess[$k].="<br />\n";
		$toutElemProgramme = getToutElemProg($quePerso, $queMat , getMatiere($id_groupe));
		
		$mess[$k].="<select name='Elem_groupe' id='Elem_groupe' style='margin-top:.5em'> \n";
		$mess[$k].="<option value=\"\">Ajouter un élément de programme</option> \n";
		while($element = $toutElemProgramme->fetch_object()){
			$mess[$k].="<option value=\"".$element->id."\" >".$element->libelle."</option> \n";
		}
		$mess[$k].="</select> \n";
		
        $mess[$k].="<br />\n";
        $mess[$k].="<input type='text' name='newElemGroupe' placeholder='Nouvel élément de programme' style='width:95%; margin-top:.3em' /> \n";

    }
    
    $k++;
}


?>
<table style ="width:100%; border-collapse: separate; border-spacing: 1px; " class='boireaus'>
    <tr>
       <th  style="width:70px;" ><div class="center">&nbsp;</div></th>
        <th style="width:30px;" ><div class="center"><strong>Moy.</strong></div></th>
        <th style="width:60%;" >
            <div style='float:right; width:16px;'>
                <a href='javascript:affichage_div_photo();'>
                    <img src='../images/icons/wizard.png' 
                         style ="width:16px; height:16px "
                         alt='Afficher les quartiles et éventuellement la photo élève' 
                         title='Afficher la photo élève pendant la saisie' />
                </a>
            </div>

<?php

if(getSettingAOui('GepiAccesBulletinSimpleClasseEleve')) {
	echo "<div style='float:right; width:16px;margin-right:5px;'><img src='../images/icons/trombinoscope.png' style=\"width:16px; height:16px\" title=\"L'appréciation sur le groupe-classe est visible des élèves\" alt=\"Appréciation sur le groupe-classe visible des élèves\" /></div>\n";
}
if(getSettingAOui('GepiAccesBulletinSimpleClasseResp')) {
	echo "<div style='float:right; width:16px;margin-right:5px;'><img src='../images/group16.png' style=\"width:16px; height:16px\" alt=\"Visibilité par les parents\" title=\"L'appréciation sur le groupe-classe est visible des parents\" /></div>\n";
}
?>
            <div class="center">
                <strong>Appréciation sur le groupe/classe</strong>
<?php

//===============================================
$tabdiv_infobulle[]=creer_div_infobulle('div_explication_cnil',"Saisies et CNIL","",$message_cnil_bons_usages,"",30,0,'y','y','n','n');
// Paramètres concernant le délai avant affichage d'une infobulle via delais_afficher_div()
// Hauteur de la bande testée pour la position de la souris:
$hauteur_survol_infobulle=20;
// Largeur de la bande testée pour la position de la souris:
$largeur_survol_infobulle=100;
// Délais en ms avant affichage:
$delais_affichage_infobulle=500;
//===============================================

// 20121101: Mettre une infobulle CNIL

?>
                <a href='#' 
                   onclick="afficher_div('div_explication_cnil','y',10,-40);return false;" 
                   onmouseover="delais_afficher_div('div_explication_cnil','y',10,-40, <?php echo $delais_affichage_infobulle ?>, <?php echo $largeur_survol_infobulle ?>, <?php echo $hauteur_survol_infobulle ?>);">
                    <img src='../images/info.png' style="width: 20px; height: 20px" title='CNIL : Règles de bon usage' alt="Information" />
                </a>
            </div>
        </th>
        <th>
            Éléments du programme
			<br />
			<input type="checkbox" name="quePerso" id="quePerso" <?php if ($quePerso) {echo "checked = 'checked'";} ?> />
			<label for="quePerso" title="Ne montrer que mes éléments de programme">Que mes éléments</label>
			<input type="checkbox" name="queMat" id="queMat" <?php if ($queMat) {echo "checked = 'checked'";} ?> />
			<label for="queMat" title="Ne montrer que les éléments de programme de la matière">Que cette matière</label>
        </th> 
    </tr>
<?php
//echo "</div></th>\n";
//echo "</tr>\n";
//=================================================


$num_id++;
$k=1;
$alt=1;
while ($k < $nb_periode) {
	$alt=$alt*(-1);
?>
    <tr class='lig<?php echo $alt; ?>'>
        <td>
<?php
	if ($current_group["classe"]["ver_periode"]["all"][$k] == 0) {
		//echo "<tr class='lig$alt'><td><span title=\"$gepiClosedPeriodLabel\">$nom_periode[$k]</span><span id='span_repartition_notes_$k'></span>1</td>\n";
            echo "<span title=\"$gepiClosedPeriodLabel\">$nom_periode[$k]</span><span id='span_repartition_notes_$k'></span>\n";
	} else {
		//echo "<tr class='lig$alt'><td>$nom_periode[$k]<span id='span_repartition_notes_$k'></span>2</td>\n";
            echo "$nom_periode[$k]<span id='span_repartition_notes_$k'></span>\n";
	}
	echo $mess[$k];
?>
			<input type="hidden" name="periode" value="<?php echo $k; ?>" />
        </td>
    </tr>
<?php
	$k++;
}
?>
</table>
    
    <br />
<?php
/*
echo "</tr>\n";
echo "</table>\n";
 * 
 */
//echo "\n";


//=================================


//=================================
$acces_bull_simp='n';
if(($_SESSION['statut']=="professeur") AND 
((getSettingValue("GepiAccesBulletinSimpleProf")=="yes")||
(getSettingValue("GepiAccesBulletinSimpleProfToutesClasses")=="yes")||
(getSettingValue("GepiAccesBulletinSimpleProfTousEleves")=="yes")
)) {
	$acces_bull_simp='y';
}
/*
if($_SESSION['statut']=="secours") {
	$acces_bull_simp='y';
}
*/
//=================================

//=================================
// 20121118
$date_du_jour=strftime("%d/%m/%Y");
// Si les parents ont accès aux bulletins ou graphes,... on va afficher un témoin
//$tab_acces_app_classe=array();
$tab_acces_app_classe2=array();
foreach($current_group["classes"]["list"] as $key => $value) {
	// L'accès est donné à la même date pour parents et responsables.
	// On teste seulement pour les parents
	$date_ouverture_acces_app_classe=array();
	//$tab_acces_app_classe[$value]=acces_appreciations(1, count($current_group["periodes"]), $value, 'responsable');
	$tab_acces_app_classe2[$value]=get_tab_acces_appreciations_ele(1, count($current_group["periodes"]), $value, 'responsable');
}


$acces_app_ele_resp=getSettingValue('acces_app_ele_resp');
if($acces_app_ele_resp=='manuel') {
	$msg_acces_app_ele_resp="Les appréciations seront visibles après une intervention manuelle d'un compte de statut 'scolarité'.";
}
elseif($acces_app_ele_resp=='date') {
	$chaine_date_ouverture_acces_app_classe="";
	for($loop=0;$loop<count($date_ouverture_acces_app_classe);$loop++) {
		if($loop>0) {
			$chaine_date_ouverture_acces_app_classe.=", ";
		}
		$chaine_date_ouverture_acces_app_classe.=$date_ouverture_acces_app_classe[$loop];
	}
	if($chaine_date_ouverture_acces_app_classe=="") {$chaine_date_ouverture_acces_app_classe="Aucune date n'est encore précisée.
Peut-être devriez-vous en poser la question à l'administration de l'établissement.";}
	$msg_acces_app_ele_resp="Les appréciations seront visibles soit à une date donnée (".$chaine_date_ouverture_acces_app_classe.").";
}
elseif($acces_app_ele_resp=='periode_close') {
	$delais_apres_cloture=getSettingValue('delais_apres_cloture');
	$msg_acces_app_ele_resp="Les appréciations seront visibles ".$delais_apres_cloture." jour(s) après la clôture de la période.";
}
else{
	$msg_acces_app_ele_resp="???";
}
//=================================

// Tableau des notes pour chaque période
$tab_per_notes=array();

$prev_classe = null;
//=========================
// Compteur pour les élèves
$i=0;
//=========================
// Pour permettre le remplacement de la chaine _PRENOM_ par le prénom de l'élève dans les commentaires types (ctp.php)
$chaine_champs_input_prenom="";
$chaine_champs_input_nom="";
$chaine_champs_input_login="";
//=========================
// 20150316: Désactivé parce que cela provoque une série de requêtes ajax au chargement de la page.
//$chaine_test_vocabulaire="";
//=========================
foreach ($liste_eleves as $eleve_login) {

	$k=1;
	$temoin_photo="";

	$enseignement_suivi_sur_une_des_periodes_ouvertes='n';
	while ($k < $nb_periode) {

		if (in_array($eleve_login, $current_group["eleves"][$k]["list"])) {
			//
			// si l'élève appartient au groupe pour cette période
			//
			$eleve_nom = $current_group["eleves"][$k]["users"][$eleve_login]["nom"];
			$eleve_prenom = $current_group["eleves"][$k]["users"][$eleve_login]["prenom"];
			$eleve_sexe = $current_group["eleves"][$k]["users"][$eleve_login]["sexe"];
			$eleve_classe = $current_group["classes"]["classes"][$current_group["eleves"]["all"]["users"][$eleve_login]["classe"]]["classe"];
			$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$eleve_login]["classe"]]["id"];

			//========================
			// AJOUT boireaus 20071115
			if($k==1){
				$sql="SELECT elenoet FROM eleves WHERE login='$eleve_login';";
				$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
				$lig_ele=mysqli_fetch_object($res_ele);
				$eleve_elenoet=$lig_ele->elenoet;

				// Photo...
				$photo=nom_photo($eleve_elenoet);
				//$temoin_photo="";
				if("$photo"!=""){
					$titre="$eleve_nom $eleve_prenom";

					$texte="<div class='center'>\n";
					$texte.="<img src='".$photo."' style=\"width:150px;\" alt=\"$eleve_nom $eleve_prenom\" />";
					$texte.="</div>\n";

					$temoin_photo="y";

					$tabdiv_infobulle[]=creer_div_infobulle('photo_'.$eleve_login,$titre,"",$texte,"",14,0,'y','y','n','n');
				}
			}
			//========================

			$suit_option[$k] = 'yes';
			//
			// si l'élève suit la matière
			//

			$notes_conteneurs="";
			// On contrôle s'il y a des boites avec moyennes à afficher
			$sql="SELECT DISTINCT id_cahier_notes FROM cn_cahier_notes WHERE id_groupe='" . $current_group["id"] . "' AND periode='$k';";
			//if($current_group["id"]==637) {echo "$sql<br />";}
			$test_cn=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test_cn)>0) {
				$lig_cn=mysqli_fetch_object($test_cn);
				/*
				$sql="SELECT cc.nom_court, cc.nom_complet, cnc.note, cnc.statut FROM cn_conteneurs cc, cn_notes_conteneurs cnc 
					WHERE cc.id_racine='$lig_cn->id_cahier_notes' AND 
						cc.display_bulletin='1' AND 
						cc.id_racine='$lig_cn->id_cahier_notes' AND 
						cc.parent!='0' AND
						cnc.id_conteneur=cc.id AND 
						cnc.login='$eleve_login';";
				*/
				$sql="SELECT cc.nom_court, cc.nom_complet, cnc.note, cnc.statut FROM cn_conteneurs cc, cn_notes_conteneurs cnc 
					WHERE cc.id_racine='$lig_cn->id_cahier_notes' AND 
						cc.id_racine='$lig_cn->id_cahier_notes' AND 
						cc.parent!='0' AND
						cnc.id_conteneur=cc.id AND 
						cnc.login='$eleve_login';";
				//if($current_group["id"]==637) {echo "$sql<br />";}
				$test_cn_moy=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test_cn_moy)>0) {
					$lig_cnc=mysqli_fetch_object($test_cn_moy);
					//$notes_conteneurs.="<center>\n";
					//$notes_conteneurs.="<b>".ucfirst(htmlspecialchars($lig_cnc->nom_complet))."&nbsp;:</b> ";
					$notes_conteneurs.="<b>".ucfirst(htmlspecialchars($lig_cnc->nom_court))."&nbsp;:</b> ";
					if($lig_cnc->statut=='y') {$notes_conteneurs.=$lig_cnc->note;} else {$notes_conteneurs.=$lig_cnc->statut;}

					$cpt_cnc=1;
					while($lig_cnc=mysqli_fetch_object($test_cn_moy)) {
						$notes_conteneurs.=", ";
						//$notes_conteneurs.="<b>".ucfirst(htmlspecialchars($lig_cnc->nom_complet))."&nbsp;:</b> ";
						$notes_conteneurs.="<b>".ucfirst(htmlspecialchars($lig_cnc->nom_court))."&nbsp;:</b> ";
						if($lig_cnc->statut=='y') {$notes_conteneurs.=$lig_cnc->note;} else {$notes_conteneurs.=$lig_cnc->statut;}
					}
					//$notes_conteneurs.="</center><br />\n";
					$notes_conteneurs.="<br />\n";
				}
			}


			if ($restauration != "oui" AND $restauration != "non") {
				// On récupère l'appréciation tempo pour la rajouter à $eleve_app
				$app_t_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_appreciations_tempo WHERE
					(login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
				$verif_t = mysqli_num_rows($app_t_query);
				if ($verif_t != 0) {
					$eleve_app_t = "\n".'<p><strong>Appréciation non enregistrée :</strong> <span style="color: red;">'.@old_mysql_result($app_t_query, 0, "appreciation").'</span></p>';
				} else {
					$eleve_app_t = '';
				}
			} else {
				$eleve_app_t = '';
			}

			// Appel des appréciations (en vérifiant si une restauration est demandée ou non)
			if ($restauration == "oui") {
				$app_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_appreciations_tempo WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
				// Si la sauvegarde ne donne rien pour cet élève, on va quand même voir dans la table définitive
				// (il se peut qu'il n'y ait pas d'enregistrement tempo pour cet élève)
				$verif = mysqli_num_rows($app_query);
				if ($verif == 0){
					$app_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_appreciations WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
				}
			} else {
				$app_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_appreciations WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
			}
			$eleve_app = @old_mysql_result($app_query, 0, "appreciation");
			// Appel des notes
			$note_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_notes WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
			$eleve_statut = @old_mysql_result($note_query, 0, "statut");
			$eleve_note = @old_mysql_result($note_query, 0, "note");
			// Formatage de la note
			$note ="";
			//$note .="<center>";
			$note.="<a href='saisie_notes.php?id_groupe=".$current_group["id"]."&amp;periode_cn=$k' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Accéder aux notes du bulletin en période $k\">";
			if ($eleve_statut != '') {
				$note .= $eleve_statut;
			} else {
				if ($eleve_note != '') {
					$note .= $eleve_note;
					$tab_per_notes[$k][]=$eleve_note;
				} else {
					$note .= "&nbsp;";
				}
			}
			$note.="</a>";
			//$note .= "</center>";

			// 20131204
			$acces_exceptionnel_complet="n";
			if(($_SESSION['statut']=='professeur')&&($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]=="P")&&($tab_autorisation_exceptionnelle_de_saisie[$k]=='yy')) {
				$acces_exceptionnel_complet="y";
			}

			$eleve_login_t[$k] = $eleve_login."_t".$k;
			//if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] != "N") {
			if (($acces_exceptionnel_complet=="n")&&
				((($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]!="N")&&($_SESSION['statut']!='secours'))||
				(($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]=="O")&&($_SESSION['statut']=='secours')))) {

				//
				// si la période est verrouillée
				//
				$mess[$k] = '';
				//$mess[$k] =$mess[$k]."<td>".$note."</td>\n<td>";
				$mess[$k] =$mess[$k]."<td>".$note."</td>\n<td>";

				$mess[$k].=$notes_conteneurs;

				//===============================
				if(($_SESSION['statut']=='professeur')&&($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]=="P")) {

					if(
						(
							(
								($eleve_app!='')||
								(mb_substr(getSettingValue('autoriser_correction_bulletin_hors_delais'),0,1)=='y')
							)&&
							(mb_substr(getSettingValue('autoriser_correction_bulletin'),0,1)=='y')
						)||
						($tab_autorisation_exceptionnelle_de_saisie[$k]=='y')
					) {

						$mess[$k].="<div style='float:right; widthheight:2em; height:1em;'><a href='#' onclick=\"affiche_div_correction('$eleve_login','$k','$cpt_correction');return false;\" alt='Proposer une correction' title='Proposer une correction'><img src='../images/edit16.png' style=\"width:16px; height:16px;\" alt='Proposer une correction' title='Proposer une correction' /></a>";
	
						$chaine_champs_textarea_correction.="<textarea name='reserve_correction_app_eleve_$cpt_correction' id='reserve_correction_app_eleve_$cpt_correction'>$eleve_app</textarea>\n";
						$chaine_champs_input_correction.="<input type='hidden' name='nom_prenom_eleve_$cpt_correction' id='nom_prenom_eleve_$cpt_correction' value=\"$eleve_nom $eleve_prenom\" />\n";
	
						$mess[$k].="</div>\n";
						$cpt_correction++;
					}
				}
				//===============================

				if ($eleve_app != '') {
					//$mess[$k] =$mess[$k].$eleve_app;
					if((strstr($eleve_app,">"))||(strstr($eleve_app,"<"))){
						$mess[$k] =$mess[$k].$eleve_app;
					}
					else{
						$mess[$k] =$mess[$k].nl2br($eleve_app);
					}
				} else {
					$mess[$k] =$mess[$k]."&nbsp;";
				}

				$sql="SELECT * FROM matieres_app_corrections WHERE (login='$eleve_login' AND id_groupe='".$current_group["id"]."' AND periode='$k');";
				$correct_app_query=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($correct_app_query)>0) {
					$lig_correct_app=mysqli_fetch_object($correct_app_query);
					$mess[$k].="<div style='color:darkgreen; border: 1px solid red;'><b>Proposition de correction en attente&nbsp;:</b><br />".nl2br($lig_correct_app->appreciation)."</div>\n";
				}
				
				$mess[$k] =$mess[$k]."</td>\n";
				
				
                $mess[$k].="<td style='text-align:left;'>-<br>";
				$elementEleve = getElementEleve($eleve_login , $anneeScolaire, $k);
				//$mess[$k].= var_dump($elementEleve);
				
		$cpt=FALSE;
		while($element = $elementEleve->fetch_object()){
			if($cpt) {
				$mess[$k].="\n<br />\n";
			}
			$mess[$k].="<input type='image' name='delElemProgElv['$element->idEP']['$eleve_login']' src='../images/disabled.png' style =\"width:16px; height:16px \" alt=\"Supprimer l'élément\" title='Supprimer cet élément de programme pour $eleve_login' /> ";
			$mess[$k].="- ".$element->libelle;
			$cpt = TRUE;
		}
				
				

			} else {

				// Ajout Eric affichage des notes au dessus de la saisie des appréciations
				$liste_notes ='';
				// Nombre de contrôles
				//$sql="SELECT cnd.note, cd.note_sur FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE cnd.login='".$eleve_login."' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group["id"]."' AND ccn.periode='$k' AND cnd.statut='';";

				$sql="SELECT cnd.note, cd.*, cc.nom_court AS nom_court_conteneur FROM 
						cn_notes_devoirs cnd, 
						cn_devoirs cd, 
						cn_cahier_notes ccn, 
						cn_conteneurs cc
					WHERE cnd.login='".$eleve_login."' AND 
						cnd.id_devoir=cd.id AND 
						cd.id_racine=ccn.id_cahier_notes AND 
						ccn.id_groupe='".$current_group["id"]."' AND 
						ccn.periode='$k' AND 
						cnd.statut='' AND
						cc.id=cd.id_conteneur
					ORDER BY cc.parent, cc.nom_court, cd.date;";

				//echo "\n<!--sql=$sql-->\n";
				$result_nbct=mysqli_query($GLOBALS["mysqli"], $sql);
				$current_eleve_nbct=mysqli_num_rows($result_nbct);

				// on prend les notes dans $string_notes
				$liste_notes='';
				$liste_notes_detaillees='';
				$conteneur_precedent='';
				if ($result_nbct) {
                                    while ($snnote=mysqli_fetch_assoc($result_nbct)) {
                                        if ($liste_notes != '') {$liste_notes .= ", ";}
                                        $liste_notes.=$snnote['note'];
                                        if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $snnote['note_sur']!=getSettingValue("referentiel_note")) {
                                            $liste_notes .= "/".$snnote['note_sur'];
                                        }

                                        if($conteneur_precedent!=$snnote['nom_court_conteneur']) {
                                            $liste_notes_detaillees.="<p><strong>".$snnote['nom_court_conteneur']."&nbsp;:</strong> <br />";
                                            $conteneur_precedent=$snnote['nom_court_conteneur'];
                                        }

                                        //if ($liste_notes_detaillees!='') {$liste_notes_detaillees.=", ";}
                                        $liste_notes_detaillees.=$snnote['nom_court']."&nbsp;: ";
                                        $liste_notes_detaillees.="<strong>".$snnote['note'];
                                        if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $snnote['note_sur']!=getSettingValue("referentiel_note")) {
                                                $liste_notes_detaillees.= "/".$snnote['note_sur'];
                                        }
                                        $liste_notes_detaillees.="</strong> (coef&nbsp;".$snnote['coef'].")";
                                        $liste_notes_detaillees.=" (".formate_date($snnote['date']).")<br />";
                                    }
				}

				if ($current_eleve_nbct ==0) {
					$liste_notes='Pas de note dans le carnet pour cette période.';
				}

				$mess[$k]="<td>".$note."</td>\n";
				$mess[$k].="<td>Contenu du carnet de notes : ";
				if($liste_notes_detaillees!='') {

					$titre="Notes de $eleve_nom $eleve_prenom sur la période $k";
					$texte="<div style='float:right; width:16px' title=\"Visualiser les notes du carnet de notes.\"><a href='../cahier_notes/saisie_notes.php?id_groupe=".$id_groupe."&amp;periode_num=$k' target='_blank'><img src='../images/icons/cn_16.png' class='icone16' alt='Visualiser CN' /></a></div>";
					$texte.="<div style='float:right; width:16px' title=\"Visualiser en infobulle les notes du carnet de notes.\"><a href='../cahier_notes/saisie_notes.php?id_groupe=".$id_groupe."&amp;periode_num=$k' onclick=\"affiche_div_notes_cn(".$current_group['id'].", '".$eleve_login."', $k, $num_id);return false;\" target='_blank'><img src='../images/icons/chercher.png' class='icone16' alt='Visualiser' /></a></div>";
					$texte.=$liste_notes_detaillees;
					$tabdiv_infobulle[]=creer_div_infobulle('notes_'.$eleve_login.'_'.$k,$titre,"",$texte,"",30,0,'y','y','n','n');

					//$mess[$k].="<a name='".$eleve_login."_".$k."'></a>";
					$mess[$k].="<a href='#".$eleve_login."_".$k."' onclick=\"fermer_div_notes();afficher_div('notes_".$eleve_login."_".$k."','y',-100,-10);return false;\" title=\"Afficher le détail des notes\">";
					$mess[$k].=$liste_notes;
					$mess[$k].="</a>";
				}
				else {
					$mess[$k].=$liste_notes;
				}
				if($notes_conteneurs!='') {
					$mess[$k].="<br />\n";
					$mess[$k].=$notes_conteneurs;
				}
				$mess[$k].="<input type='hidden' name='log_eleve_".$k."[$i]' value=\"".$eleve_login_t[$k]."\" />\n";

				$chaine_champs_input_prenom.="<input type='hidden' name='prenom_eleve_".$k."[$i]' id='prenom_eleve_".$k.$num_id."' value=\"".$eleve_prenom."\" />\n";
				$chaine_champs_input_nom.="<input type='hidden' name='nom_eleve_".$k."[$i]' id='nom_eleve_".$k.$num_id."' value=\"".$eleve_nom."\" />\n";

				$chaine_champs_input_login.="<input type='hidden' name='login_eleve_".$k."[$i]' id='login_eleve_".$k.$num_id."' value=\"".$eleve_login_t[$k]."\" />\n";

				$mess[$k].="<textarea id=\"n".$k.$num_id."\" class='wrap' onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_eleve_".$k."_".$i."\" rows='2' cols='$saisie_app_nb_cols_textarea' onchange=\"changement();";
				$mess[$k].="ajaxAppreciations('".$eleve_login_t[$k]."', '".$id_groupe."', 'n".$k.$num_id."');";
				// La vérification de fautes de frappe est maintenant faite dans la même requête ajax
				//$mess[$k].="ajaxVerifAppreciations('".$eleve_login_t[$k]."', '".$id_groupe."', 'n".$k.$num_id."');";

				// 20150316: Désactivé parce que ce la provoque une série de requêtes ajax au chargement de la page.
				//$chaine_test_vocabulaire.="ajaxVerifAppreciations('".$eleve_login_t[$k]."', '".$id_groupe."', 'n".$k.$num_id."');\n";

				$mess[$k].="\"";

				//==================================
				// Rétablissement: boireaus 20080219
				// Pour revenir au champ suivant après validation/enregistrement:
				// MODIF: boireaus 20080520
				//$mess[$k].=" onfocus=\"focus_suivant(".$k.$num_id.");\"";
				$mess[$k].=" onfocus=\"focus_suivant(".$k.$num_id.");document.getElementById('focus_courant').value='".$k.$num_id."';";
				$mess[$k].="repositionner_commtype(); afficher_positionner_div_notes('notes_".$eleve_login."_".$k."', '".$eleve_login."');";
				//================================================
				if(getSettingValue("gepi_pmv")!="n"){
					$sql="SELECT elenoet FROM eleves WHERE login='".$eleve_login."';";
					//echo "$sql<br />";
					$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_ele)>0) {
						$lig_ele=mysqli_fetch_object($res_ele);
						$_photo_eleve = nom_photo($lig_ele->elenoet);
						if(file_exists($_photo_eleve)) {
							$mess[$k].=";affiche_photo('".$_photo_eleve."','".addslashes(my_strtoupper($eleve_nom)." ".casse_mot($eleve_prenom,'majf2'))."')";
						}
						else {
							$mess[$k].="document.getElementById('div_photo_eleve').innerHTML='';";
						}
					}
					else {
						$mess[$k].="document.getElementById('div_photo_eleve').innerHTML='';";
					}
				}
				//================================================
				$mess[$k].="\"";
				//==================================

				$mess[$k].=">".$eleve_app."</textarea>\n";

				// 20160617
				$mess[$k].="<div style='float:right; width:16px; margin-right:3px;' title=\"Corriger la ponctuation.\"><a href=\"#\" onclick=\"document.getElementById('n".$k.$num_id."').value=corriger_espaces_et_casse_ponctuation(document.getElementById('n".$k.$num_id."').value);changement();return false;\"><img src='../images/icons/wizard_ponctuation.png' class='icone16' alt='Ponctuation' /></a></div>";

				// on affiche si besoin l'appréciation temporaire (en sauvegarde)
				$mess[$k].=$eleve_app_t;

				// Espace pour afficher les éventuelles fautes de frappe
				$mess[$k].="<div id='div_verif_n".$k.$num_id."' style='color:red;'>";
				// 20150316
				if(getSettingValue('active_recherche_lapsus')!='n') {
					$mess[$k].=teste_lapsus($eleve_app);
				}
				$mess[$k].="</div>\n";
				
                $mess[$k].="</td>\n<td style='text-align:left;'>";
				
				$elementEleve = getElementEleve($eleve_login , $anneeScolaire, $k);	
				$cpt=FALSE;
				while($element = $elementEleve->fetch_object()){
					if($cpt) {
						$mess[$k].="\n<br />\n";
					}
					//$mess[$k].="<input type='image' name=\"delElemProgElv_".$element->idEP."['$eleve_login']\"  src='../images/disabled.png' style =\"width:16px; height:16px \" alt=\"Supprimer l'élément\" title='Supprimer cet élément de programme pour $eleve_login' /> ";
					$mess[$k].="<button name=\"delElemProgElv[".$element->idEP."]\" value='$eleve_login' "
						. "title=\"Supprimer cette liaison\" style='margin=0; padding = 0;' >\n";
					$mess[$k].="<img src='../images/disabled.png' style =\"width:16px; height:16px \" alt=\"Supprimer l'élément\" title='Supprimer cet élément de programme pour $eleve_login' /> \n";
					$mess[$k].="</button>\n";
					$mess[$k].="- ".$element->libelle;
					$cpt = TRUE;
				}
		
				$toutElemProgramme->data_seek(0);

				$mess[$k].="<br />\n";
				$mess[$k].="<select name=\"Elem_Eleve[$eleve_login]\" id='Elem_Eleve$eleve_login' style='margin-top:.5em'> \n";
				$mess[$k].="<option value=\"\">Ajouter un élément de programme</option> \n";
				while($element = $toutElemProgramme->fetch_object()){
					$mess[$k].="<option value=\"".$element->id."\" >".$element->libelle."</option> \n";
				}
				$mess[$k].="</select> \n";

				$mess[$k].="<br />\n";
					
				$mess[$k].="<input type='text' name='newElemEleve[$eleve_login]' placeholder='Nouvel élément de programme' style='width:95%; margin-top:.3em' /> \n";

				//$mess[$k].= var_dump($elementEleve);
				
			
				
				//$mess[$k].= "</td>\n";

				//=========================

				$enseignement_suivi_sur_une_des_periodes_ouvertes='y';
			}
		}
		else {
                    //
                    // si l'élève n'appartient pas au groupe pour cette période.
                    //
                    $suit_option[$k] = 'no';
                    $mess[$k] = "<td>&nbsp;</td><td><p class='small' title=\"Enseignement non suivi sur cette période.\">non suivi</p></td>\n";
                    $mess[$k].="<td style='text-align:left;'>";

		}
		$k++;
	}


	//
	//Affichage de la ligne
	//
	$display_eleve='no';
	$k=1;
	while ($k < $nb_periode) {
		if ($suit_option[$k] != 'no') {$display_eleve='yes';}
		$k++;
	}
	if ($display_eleve=='yes') {

		if ($multiclasses && $prev_classe != $eleve_classe && $order_by == 'classe') {
			if ($prev_classe != null) {
				echo "<hr style='width: 95%;'/>\n";
			}
			echo "<h3>Classe de " . $eleve_classe . "</h3>\n";
		}
		$prev_classe = $eleve_classe;
		//echo "<a name='saisie_app_$eleve_login'></a>";
?>
    <table id='saisie_app_<?php echo $eleve_login;?>' style ="width:100%; border-collapse: separate; border-spacing: 1px; " class='boireaus'>
        <tr>
            <th class="center" style="width: 70px;">
<?php 

		$num_per1=0;
		$id_premiere_classe="";
		$nom_derniere_classe="";
		$current_id_classe=array();
		$sql="SELECT id_classe, classe, periode FROM j_eleves_classes jec, classes c WHERE jec.login='$eleve_login' AND jec.id_classe=c.id ORDER BY periode;";
		$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_classe)>0) {
			$lig_classe=mysqli_fetch_object($res_classe);
			$id_premiere_classe=$lig_classe->id_classe;
			$current_id_classe[$lig_classe->periode]=$lig_classe->id_classe;
			$nom_derniere_classe=$lig_classe->classe;
			$num_per1=$lig_classe->periode;
			$num_per2=$num_per1;
			while($lig_classe=mysqli_fetch_object($res_classe)) {
				$current_id_classe[$lig_classe->periode]=$lig_classe->id_classe;
				$num_per2=$lig_classe->periode;
				$nom_derniere_classe=$lig_classe->classe;
			}
		}

		$designation_eleve=preg_replace("/'/", " ", $eleve_nom." ".$eleve_prenom." (".$nom_derniere_classe.")");
		if(($id_premiere_classe!='')&&($acces_bull_simp=='y')) {
			//echo "<div style='float:right; width: 17px; margin-right: 1px;'>\n";
			echo "<a href=\"#\" onclick=\"afficher_div('div_bull_simp','y',-100,40); affiche_bull_simp('$eleve_login','$designation_eleve','$id_premiere_classe','$num_per1','$num_per2');return false;\">";
			echo "<img src='../images/icons/bulletin_simp.png' style=\"width:17px; height:17px\" alt='Bulletin simple toutes périodes en infobulle' title='Bulletin simple toutes périodes en infobulle' />";
			echo "</a>";
			//echo "</div>\n";
		}
?>
            </th>
            <th class="center" style="width:30px">
                <strong>Moy.</strong>
            </th>
            <th class="center" style="width:60%">
                    <strong>
<?php
		//echo "</th>\n";
		//echo "<th style=\"width:30px\"><div class=\"center\"><strong>Moy.</strong></div></th>\n";
		//echo "<th>\n";

		//echo "<div class=\"center\"><b>";
		if($acces_visu_eleve) {
			echo "<a href='../eleves/visu_eleve.php?ele_login=$eleve_login' target='_blank' title=\"Voir (dans un nouvel onglet) la fiche élève avec les onglets Élève, Enseignements, Bulletins, CDT, Absences,...\">";
			echo "$eleve_nom $eleve_prenom";
			echo "</a>";
		}
		else {
			echo "$eleve_nom $eleve_prenom";
		}
		//echo "</b>\n";
?>
                    </strong>
<?php
		//==========================
		// AJOUT: boireaus 20071115
		// Lien photo...
		if($temoin_photo=="y"){
			echo " <a href='#' onmouseover=\"delais_afficher_div('photo_$eleve_login','y',-100,20,1000,10,10);\"";
			echo " onclick=\"afficher_div('photo_$eleve_login','y',-100,20); return false;\" title=\"Afficher la photo de l'élève.\"";
			echo ">";
			echo "<img src='../mod_trombinoscopes/images/";
			if($eleve_sexe=="F") {
				echo "photo_f.png";
			}
			else{
				echo "photo_g.png";
			}
			echo "' class='icone20' alt='$eleve_nom $eleve_prenom' />";
			echo "</a>";
		}
		//==========================

		//echo "</div>\n";
		echo "\n";
                
?>
                       
            </th>
        <th>
            Éléments du programme
        </th>
        </tr>
    
<?php

		// Pour permettre de sauter dans la liste un élève qui est parti en cours d'année
		// Si plusieurs périodes sont ouvertes en saisie, cela peut ne pas fonctionner
		if($enseignement_suivi_sur_une_des_periodes_ouvertes=='y') {
			$num_id++;
		}

		$k=1;
		$alt=1;
		$designation_eleve=preg_replace("/'/", " ", $eleve_nom." ".$eleve_prenom." (".$nom_derniere_classe.")");
		while ($k < $nb_periode) {

			$alt=$alt*(-1);
			if ($current_group["classe"]["ver_periode"]["all"][$k] == 0) {
				echo "<tr class='lig$alt'><td><span title=\"$gepiClosedPeriodLabel\">\n";

				echo $nom_periode[$k];

				//if($current_id_classe!='') {
				if((isset($current_id_classe[$k]))&&($acces_bull_simp=='y')) {
					//echo "<a href=\"#\" onclick=\"afficher_div('div_bull_simp','y',-100,20); affiche_bull_simp('$eleve_login','$current_id_classe','$k','$k');return false;\">";
					echo " <a href=\"#\" onclick=\"afficher_div('div_bull_simp','y',-100,20); affiche_bull_simp('$eleve_login','$designation_eleve','$current_id_classe[$k]','$k','$k');return false;\" title='Bulletin simple en infobulle'>";
					//echo $nom_periode[$k];
					echo "<img src='../images/icons/bulletin_simp.png' style=\"width:17px; height:17px;\" alt='Bulletin simple de la période en infobulle' title='Bulletin simple de la période en infobulle' />";
					echo "</a>";
				}
				echo "</span>\n";

				// 20121118
				// Si les parents ont l'accès aux bulletins, graphes,... on affiche s'ils ont l'accès aux appréciations à ce jour
				if((getSettingAOui('GepiAccesBulletinSimpleParent'))||
				(getSettingAOui('GepiAccesGraphParent'))||
				(getSettingAOui('GepiAccesBulletinSimpleEleve'))||
				(getSettingAOui('GepiAccesGraphEleve'))) {
					//if($tab_acces_app_classe[$eleve_id_classe][$k]=="y") {
                                    if(isset($tab_acces_app_classe2[$eleve_id_classe][$k][$eleve_login])) {
                                    
					if($tab_acces_app_classe2[$eleve_id_classe][$k][$eleve_login]=="y") {
						echo " <img src='../images/icons/visible.png' style=\"width:19px; height:16px;\" alt='Appréciations visibles des parents/élèves.' title='A la date du jour (".$date_du_jour."), les appréciations de la période ".$k." sont visibles des parents/élèves.' />";
					}
					else {
						echo " <img src='../images/icons/invisible.png' style=\"width:19px; height:16px;\" alt='Appréciations non encore visibles des parents/élèves.' title=\"A la date du jour (".$date_du_jour."), les appréciations de la période ".$k." ne sont pas encore visibles des parents/élèves.
$msg_acces_app_ele_resp\" />";
					}
                                        
                                    }  
                                        
				}

				echo "</td>\n";
			}
			else {
				echo "<tr class='lig$alt'><td>\n";

				echo $nom_periode[$k];

				//if($current_id_classe!='') {
				if((isset($current_id_classe[$k]))&&($acces_bull_simp=='y')) {
					//echo "<a href=\"#\" onclick=\"afficher_div('div_bull_simp','y',-100,40); affiche_bull_simp('$eleve_login','$current_id_classe','$k','$k');return false;\">";
					echo "<a href=\"#\" onclick=\"afficher_div('div_bull_simp','y',-100,40); affiche_bull_simp('$eleve_login','$designation_eleve','$current_id_classe[$k]','$k','$k');return false;\" title='Bulletin simple en infobulle'>";
					echo " <img src='../images/icons/bulletin_simp.png' style=\"width:17px; height:17px;\" alt='Bulletin simple de la période en infobulle' title='Bulletin simple de la période en infobulle' />";
					echo "</a>";
				}

				// 20121118
				// Si les parents ont l'accès aux bulletins, graphes,... on affiche s'ils ont l'accès aux appréciations à ce jour
				if((getSettingAOui('GepiAccesBulletinSimpleParent'))||
				(getSettingAOui('GepiAccesGraphParent'))||
				(getSettingAOui('GepiAccesBulletinSimpleEleve'))||
				(getSettingAOui('GepiAccesGraphEleve'))) {
					if(isset($tab_acces_app_classe2[$eleve_id_classe][$k][$eleve_login])) {
						if($tab_acces_app_classe2[$eleve_id_classe][$k][$eleve_login]=="y") {
							echo "<img src='../images/icons/visible.png' style=\"width:19px; height:16px;\" alt='Appréciations visibles des parents/élèves.' title='A la date du jour (".$date_du_jour."), les appréciations de la période ".$k." sont visibles des parents/élèves.' />";
						}
						else {
							echo "<img src='../images/icons/invisible.png' style=\"width:19px; height:16px;\" alt='Appréciations non encore visibles des parents/élèves.' title=\"A la date du jour (".$date_du_jour."), les appréciations de la période ".$k." ne sont pas encore visibles des parents/élèves.
	$msg_acces_app_ele_resp\" />";
						}
					}
				}

				if(($_SESSION['statut']=='secours')&&($id_premiere_classe!='')) {
					echo " <a href='../saisie/saisie_secours_eleve.php?id_classe=$id_premiere_classe&periode_num=$k&ele_login=$eleve_login' title=\"Corriger les appréciations et notes de cet élève.\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/edit16.png' class='icone16' alt='Éditer' /></a>";
				}

				echo "</td>\n";
			}

			echo $mess[$k];
			$k++;
		}
		echo "</tr>\n";
		//echo"</table>\n<p></p>";";
?>
    </table>
<?php
		//echo"<p>&nbsp;</p>\n";
		//echo"<p></p>\n";
		echo "<br />\n";
	}
	$i++;

}

echo "<input type='hidden' name='indice_max_log_eleve' value='$i' />\n";
?>
<input type="hidden" name="is_posted" value="yes" />
<input type="hidden" name="id_groupe" value="<?php echo "$id_groupe";?>" />
<input type="hidden" name="periode_cn" value="<?php echo "$periode_cn";?>" />

    <div class="center" id="fixe">
	<?php
            echo $chaine_date_conseil_classe."<br />";
            if($proposer_liens_enregistrement=='y') {
                if(getSettingAOui('aff_temoin_check_serveur')) {
                        temoin_check_srv();
                }
                echo "
            <input type='submit' value='Enregistrer' /><br />

            <!-- DIV destiné à afficher un décompte du temps restant pour ne pas se faire piéger par la fin de session -->
            <div id='decompte' title=\"La session ne sera plus valide, si vous ne consultez pas une page
ou ne validez pas ce formulaire avant le nombre de secondes indiqué.\"></div>\n";
            }

            //============================================
            if(getSettingValue('appreciations_types_profs')=='y' || getSettingValue('appreciations_types_profs')=='yes') {include('ctp.php');}
            //============================================

            if($proposer_liens_enregistrement=="y") {
                echo "<a href='#' onclick=\"insere_notes();return false;\">";
                echo "<img src='../images/icons/wizard.png' style=\"width:16px; height:16px\" alt='Insérer les notes des devoirs' title='Insérer les notes des devoirs' />";
                echo "</a>\n";
            }
	?>

		<!-- Champ destiné à recevoir la valeur du champ suivant celui qui a le focus pour redonner le focus à ce champ après une validation -->
		<input type='hidden' id='info_focus' name='champ_info_focus' value='' />
		<input type='hidden' id='focus_courant' name='focus_courant' value='' />
    </div>
</form>

<?php

	$titre_infobulle="Notes <span id='span_titre_notes_ele'></span>";
	$texte_infobulle="<div id='div_notes_cn'></div>";
	$tabdiv_infobulle[]=creer_div_infobulle('div_infobulle_notes_cn',$titre_infobulle,"",$texte_infobulle,"",30,0,'y','y','n','n');
	echo "<script type='text/javascript'>
	function affiche_div_notes_cn(id_groupe, login_ele, num_per, num_id_ele) {
		designation_ele='';
		if(document.getElementById('nom_eleve_'+num_per+num_id_ele)) {
			designation_ele=document.getElementById('nom_eleve_'+num_per+num_id_ele).value;
		}
		if(document.getElementById('prenom_eleve_'+num_per+num_id_ele)) {
			designation_ele=designation_ele+' '+document.getElementById('prenom_eleve_'+num_per+num_id_ele).value;
		}

		document.getElementById('span_titre_notes_ele').innerHTML=designation_ele;

		new Ajax.Updater($('div_notes_cn'),'../lib/ajax_action.php?mode=notes_ele_grp_per&ele_login='+login_ele+'&id_groupe='+id_groupe,{method: 'get'});

		afficher_div('div_infobulle_notes_cn', 'y', 10, 10);

		document.getElementById('login_ele_courant').value=login_ele;
	}
</script>";

	for($loop=1;$loop<$nb_periode;$loop++) {
		$histogramme="";

		if((isset($tab_per_notes[$loop]))&&(count($tab_per_notes[$loop])>0)) {
			$histogramme=retourne_html_histogramme_svg($tab_per_notes[$loop], "Repartition P$loop", "repartition_p$loop");

			if($histogramme!="") {
				//echo $histogramme;
				echo "<script type='text/javascript'>
	if(document.getElementById('span_repartition_notes_$loop')) {document.getElementById('span_repartition_notes_$loop').innerHTML='<br />".addslashes($histogramme)."';}
</script>\n";
			}
		}
	}

echo "
<input type='hidden' name='login_ele_courant' id='login_ele_courant' value='' />

<script type='text/javascript'>

	function repositionner_commtype() {
		if(document.getElementById('div_commtype')) {
			if(document.getElementById('div_commtype').style.display!='none') {
				x=document.getElementById('div_commtype').style.left;
				afficher_div('div_commtype','y',20,20);
				document.getElementById('div_commtype').style.left=x;
			}
		}
	}

	function afficher_positionner_div_notes(id_div_notes, login_ele) {
		if(document.getElementById(id_div_notes)) {
			div_note_aff='n';

			tab_div=document.getElementsByTagName('div');
			for(i=0;i<tab_div.length;i++) {
				tmp_div=tab_div[i];
				tmp_id=tmp_div.getAttribute('id');
				if(tmp_id) {
					if((tmp_id.substr(0,6)=='notes_')&&(tmp_id.substr(tmp_id.length-14,14)!='_contenu_corps')) {
						if(tmp_div.style.display!='none') {
							div_note_aff='y';
							//alert(tmp_id);
							break;
						}
					}
				}
			}

			if(div_note_aff=='y') {
				fermer_div_notes();
				afficher_div(id_div_notes,'y',20,20);
				// A FAIRE: Ajouter un test: si le positionnement a échoué et qu'on est hors fenêtre repositionner.
			}

			if(document.getElementById('div_infobulle_notes_cn')) {
				if((document.getElementById('div_infobulle_notes_cn').style.display!='none')&&(document.getElementById('login_ele_courant').value!=login_ele)) {
					document.getElementById('div_infobulle_notes_cn').style.display='none';
				}
			}
		}
	}

	function signaler_une_faute(eleve_login, id_eleve, id_groupe, liste_profs_du_groupe, num_periode) {

		info_eleve=eleve_login;
		if(document.getElementById('nom_prenom_eleve_'+id_eleve)) {
			info_eleve=document.getElementById('nom_prenom_eleve_'+id_eleve).value;
		}

		document.getElementById('titre_entete_signaler_faute').innerHTML='Signaler un problème/faute pour '+info_eleve+' période '+num_periode;

		document.getElementById('signalement_login_eleve').value=eleve_login;
		document.getElementById('signalement_id_groupe').value=id_groupe;

		document.getElementById('signalement_id_eleve').value=id_eleve;
		document.getElementById('signalement_num_periode').value=num_periode;

		info_groupe=''
		if(document.getElementById('signalement_id_groupe_'+id_groupe)) {
			info_groupe=document.getElementById('signalement_id_groupe_'+id_groupe).value;
		}

		message='Bonjour,\\n\\nL\'appréciation de l\'élève '+info_eleve+' sur l\'enseignement n°'+id_groupe+' ('+info_groupe+') en période n°'+num_periode+' présente un problème ou une faute:\\n';
		message=message+'================================\\n';
		// Le champ textarea n'existe que si une appréciation a été enregistrée
		if(document.getElementById('appreciation_'+id_eleve+'_'+id_groupe+'_'+num_periode)) {
			//message=message+addslashes(document.getElementById('appreciation_'+id_eleve+'_'+id_groupe+'_'+num_periode).innerHTML);
			message=message+document.getElementById('appreciation_'+id_eleve+'_'+id_groupe+'_'+num_periode).innerHTML;
		}
		//alert('document.getElementById(\'appreciation_'+id_eleve+'_'+id_groupe+'_'+num_periode+').innerHTML');

		message=message+'\\n================================\\n'
";
		if(getSettingValue('url_racine_gepi')!="") {
			echo "		message=message+'Après connexion dans Gepi, l\'adresse pour corriger est \\n".getSettingValue('url_racine_gepi')."/saisie/saisie_appreciations.php?id_groupe='+id_groupe+'#saisie_app_'+eleve_login;\n";
			echo "		message=message+'\\n'";
		}
		echo "
		message=message+'\\n\\nCordialement\\n-- \\n".casse_mot($_SESSION['prenom'],'majf2')." ".$_SESSION['nom']."'

		//alert('message='+message);

		document.getElementById('div_signalement_message').innerHTML='<textarea name=\'signalement_message\' id=\'signalement_message\' cols=\'50\' rows=\'11\'></textarea>';

		document.getElementById('signalement_message').innerHTML=message;
		//afficher_div('div_signaler_faute','n',0,0);
		afficher_div('div_signaler_faute','n',0,-50);
		//afficher_div('div_signaler_faute','y',100,100);
	}

	function valider_signalement_faute() {
		signalement_id_groupe=document.getElementById('signalement_id_groupe').value;
		signalement_login_eleve=document.getElementById('signalement_login_eleve').value;

		//signalement_message=escape(document.getElementById('signalement_message').value);
		signalement_message=document.getElementById('signalement_message').value;

		//signalement_message=encodeURIComponent(document.getElementById('signalement_message').value);

		signalement_id_eleve=document.getElementById('signalement_id_eleve').value;
		signalement_num_periode=document.getElementById('signalement_num_periode').value;
		signalement_id_classe=document.getElementById('signalement_id_classe').value;

		//alert(signalement_message);

		//new Ajax.Updater($('signalement_effectue_'+signalement_id_eleve+'_'+signalement_id_groupe+'_'+signalement_num_periode),'../lib/ajax_signaler_faute.php?signalement_login_eleve='+signalement_login_eleve+'&signalement_id_groupe='+signalement_id_groupe+'&signalement_id_classe='+signalement_id_classe+'&signalement_num_periode='+signalement_num_periode+'&signalement_message='+signalement_message+'".add_token_in_url(false)."',{method: 'get'});

		new Ajax.Updater($('signalement_effectue_'+signalement_id_eleve+'_'+signalement_id_groupe+'_'+signalement_num_periode),'../lib/ajax_signaler_faute.php?a=a&".add_token_in_url(false)."',{method: 'post',
		parameters: {
			signalement_login_eleve: signalement_login_eleve,
			signalement_id_groupe: signalement_id_groupe,
			signalement_id_classe: signalement_id_classe,
			signalement_num_periode: signalement_num_periode,
			no_anti_inject_signalement_message: signalement_message,
			suppression_possible:'oui'
		}});

		cacher_div('div_signaler_faute');
		//document.getElementById('signalement_message').innerHTML='';

	}
\n";

/*
//20150316
if(getSettingValue('active_recherche_lapsus')!='n') {
	if((isset($chaine_test_vocabulaire))&&($chaine_test_vocabulaire!="")) {
		echo $chaine_test_vocabulaire;
	}
}
*/

echo "
	/*
	function get_div_size(id_div) {
		if(document.getElementById(id_div)) {
			alert(document.getElementById(id_div).style.top);
			alert(document.getElementById(id_div).style.height);
		}
	}
	*/

	// <![CDATA[
	function affiche_bull_simp(login_eleve,designation_eleve,id_classe,num_per1,num_per2) {
		document.getElementById('titre_entete_bull_simp').innerHTML='Bulletin simplifié de '+designation_eleve+' période '+num_per1+' à '+num_per2;
		new Ajax.Updater($('corps_bull_simp'),'ajax_edit_limite.php?choix_edit=2&login_eleve='+login_eleve+'&id_classe='+id_classe+'&periode1='+num_per1+'&periode2='+num_per2,{method: 'get'});
	}
	//]]>

	function insere_notes() {
		id_focus_courant=document.getElementById('focus_courant').value;
	
		if(document.getElementById('n'+id_focus_courant)) {
			app0=document.getElementById('n'+id_focus_courant).value;

			app1=app0+'@@Notes';
			document.getElementById('n'+id_focus_courant).value=app1;
			document.getElementById('n'+id_focus_courant).focus();
		}
	}

	function fermer_div_notes() {
		//var exp = new RegExp(\"^[0-9-.]*$\",\"g\");
		var exp = new RegExp(\"[0-9]$\",\"g\");

		chaine=''
		champs_div=document.getElementsByTagName('div');
		for(i=0;i<champs_div.length;i++) {
			if(champs_div[i].getAttribute('id')) {
				id_courant=champs_div[i].getAttribute('id');
				if(id_courant.substr(0,6)=='notes_') {
					// On teste si le id_courant se termine par un chiffre (numero de periode)
					// et ne pas recuperer les sous-div inclus dans les conteneurs de notes
					if(exp.test(id_courant)) {
						chaine=chaine+' '+id_courant;
						cacher_div(id_courant);
					}
				}
			}
		}
		//alert(chaine);
	}

</script>\n";


//===========================================================================

$aff_photo_par_defaut=getPref($_SESSION['login'],'aff_photo_saisie_app',"n");

echo "<script type='text/javascript'>
function affichage_div_photo() {
	if(document.getElementById('div_photo_eleve').style.display=='none') {
		document.getElementById('div_photo_eleve').style.display='';
	}
	else {
		document.getElementById('div_photo_eleve').style.display='none';
	}
}

function affiche_photo(photo,nom_prenom) {
	document.getElementById('div_photo_eleve').innerHTML='<img src=\"'+photo+'\"  style=\"width:150px\" alt=\"Photo\" /><br />'+nom_prenom;
}
";
if($aff_photo_par_defaut=='y') {
	echo "affichage_div_photo();\n";
}
//echo "affichage_div_photo();\n";
echo "</script>\n";


// =======================
if(($_SESSION['statut']=='professeur')&&
((mb_substr(getSettingValue('autoriser_correction_bulletin'),0,1)=='y')||($une_autorisation_exceptionnelle_de_saisie_au_moins=='y'))) {
	$titre="Correction d'une appréciation";
	$texte="<form enctype=\"multipart/form-data\" action=\"saisie_appreciations.php\" name='form_correction' method=\"post\">\n";
	$texte.=add_token_field();
	$texte.="Vous pouvez proposer une correction pour <span id='span_correction_login_eleve' class='bold'>...</span> sur la période <span id='span_correction_periode' class='bold'>...</span>&nbsp;: ";
	$texte.="<input type='hidden' name='correction_login_eleve' id='correction_login_eleve' value='' />\n";
	$texte.="<input type='hidden' name='correction_periode' id='correction_periode' value='' />\n";
	$texte.="<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
	$texte.="<textarea class='wrap' name=\"no_anti_inject_correction_app_eleve\" id='correction_app_eleve' rows='2' cols='70'></textarea><br />";
	$texte.="<input type='submit' name='soumettre_correction' value='Soumettre la correction' />\n";
	$texte.="</form>\n";
	$tabdiv_infobulle[]=creer_div_infobulle('div_correction',$titre,"",$texte,"",40,0,'y','y','n','n');

	$titre="Correction d'une appréciation";
	$texte="<form enctype=\"multipart/form-data\" action=\"saisie_appreciations.php\" name='form_correction2' method=\"post\">\n";
	$texte.=add_token_field();
	$texte.="Vous pouvez proposer une correction pour l'appréciation de groupe sur la période <span id='span_correction_periode2' class='bold'>...</span>&nbsp;: ";
	$texte.="<input type='hidden' name='correction_periode' id='correction_periode2' value='' />\n";
	$texte.="<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
	$texte.="<textarea class='wrap' name=\"no_anti_inject_correction_app_groupe\" id='correction_app_groupe' rows='2' cols='70'></textarea><br />";
	$texte.="<input type='submit' name='soumettre_correction' value='Soumettre la correction' />\n";
	$texte.="</form>\n";
	$tabdiv_infobulle[]=creer_div_infobulle('div_correction_grp',$titre,"",$texte,"",40,0,'y','y','n','n');

	echo "<script type='text/javascript'>

function affiche_div_correction(eleve_login,num_periode,num_eleve) {
	document.getElementById('correction_login_eleve').value=eleve_login;
	document.getElementById('span_correction_login_eleve').innerHTML=document.getElementById('nom_prenom_eleve_'+num_eleve).value;
	document.getElementById('correction_periode').value=num_periode;
	document.getElementById('span_correction_periode').innerHTML=num_periode;
	document.getElementById('correction_app_eleve').value=document.getElementById('reserve_correction_app_eleve_'+num_eleve).value;
	afficher_div('div_correction','y',-100,20)

	document.getElementById('correction_app_eleve').focus();

	if(change!='no') {
		alert(\"Des modifications n'ont pas été enregistrées. Si vous validez la proposition de correction sans d'abord enregistrer, les modifications seront perdues.\")
	}
}

//function affiche_div_correction_groupe(num_periode,num_eleve) {
function affiche_div_correction_groupe(num_periode) {
	document.getElementById('correction_periode2').value=num_periode;
	document.getElementById('span_correction_periode2').innerHTML=num_periode;

	//document.getElementById('correction_app_groupe').value=document.getElementById('reserve_correction_app_eleve_'+num_eleve).value;
	document.getElementById('correction_app_groupe').value=document.getElementById('reserve_correction_app_grp_'+num_periode).value;

	afficher_div('div_correction_grp','y',-100,20)

	if(change!='no') {
		alert(\"Des modifications n'ont pas été enregistrées. Si vous validez la proposition de correction sans d'abord enregistrer, les modifications seront perdues.\")
	}
}

/*
// Ca ne fonctionne pas correctement.
function addslashes (str) {
    // Escapes single quote, double quotes and backslash characters in a string with backslashes  
    // 
    // version: 1004.2314
    // discuss at: http://phpjs.org/functions/addslashes    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Ates Goral (http://magnetiq.com)
    // +   improved by: marrtins
    // +   improved by: Nate
    // +   improved by: Onno Marsman    // +   input by: Denny Wardhana
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Oskar Larsson Högfeldt (http://oskar-lh.name/)
    // *     example 1: addslashes(\"kevin's birthday\");
    // *     returns 1: 'kevin\'s birthday' 
    return (str+'').replace(/[\\\\\"']/g, '\\\\$&').replace(/\\u0000/g, '\\\\0');
}
*/
</script>\n";

	// Formulaire caché destiné à faire des copies vers no_anti_inject_correction_app_eleve
	echo "<form enctype=\"multipart/form-data\" action=\"saisie_appreciations.php\" name='form_correction_reserve' method=\"post\" style='display:none'>\n";
	echo $chaine_champs_textarea_correction;
	echo $chaine_champs_input_correction;
	echo "</form>\n";
}
// =======================

// ====================== DISPOSITIF CTP ========================
// Pour permettre le remplacement de la chaine _PRENOM_ par le prénom de l'élève dans les commentaires types (ctp.php)
// Les champs INPUT des prénoms sont insérés hors du formulaire principal pour éviter d'envoyer trop de champs lors du submit (problèmes avec suhosin qui limite le nombre de champs pouvant être POSTés)
echo "<form enctype=\"multipart/form-data\" action=\"saisie_appreciations.php\" name='form2' method=\"post\">\n";
echo $chaine_champs_input_prenom;
echo $chaine_champs_input_nom;
echo $chaine_champs_input_login;
echo "</form>\n";
// ==============================================================

// ====================== SYSTEME  DE SAUVEGARDE ========================
// Dans tous les cas, suite à une demande de restauration, et quelle que soit la réponse, les sauvegardes doivent être effacées
if ($restauration == "oui" OR $restauration == "non") {
	$effacer = mysqli_query($GLOBALS["mysqli"], "DELETE FROM matieres_appreciations_tempo WHERE id_groupe = '".$id_groupe."'")
	OR DIE('Erreur dans l\'effacement de la table temporaire (2) : '.mysqli_error($GLOBALS["mysqli"]));
}
// Il faudra permettre de n'afficher ce décompte que si l'administrateur le souhaite.

echo "<script type='text/javascript'>
cpt=".$tmp_timeout.";
compte_a_rebours='y';

id_groupe=$id_groupe;

function decompte(cpt){
	if(compte_a_rebours=='y'){
		document.getElementById('decompte').innerHTML=cpt;
		if(cpt>0){
			cpt--;
		}

		setTimeout(\"decompte(\"+cpt+\")\",1000);
	}
	else{
		document.getElementById('decompte').style.display='none';
	}
}

if(document.getElementById('decompte')) {
	decompte(cpt);
}
";

// Après validation, on donne le focus au champ qui suivait celui qui vient d'être rempli
if(isset($_POST['champ_info_focus'])){
	if($_POST['champ_info_focus']!=""){
		echo "// On positionne le focus...
	document.getElementById('n".$_POST['champ_info_focus']."').focus();
\n";
	}
}
elseif(isset($id_premier_textarea)) {
	echo "if(document.getElementById('n".$id_premier_textarea."')) {document.getElementById('n".$id_premier_textarea."').focus();}
if(document.getElementById('focus_courant')) {document.getElementById('focus_courant').value='$id_premier_textarea';}";
}

echo "</script>\n";

//=========================
if ((isset($insert_mass_appreciation_type))&&($insert_mass_appreciation_type=="y")&&(isset($droit_insert_mass_appreciation_type))&&($droit_insert_mass_appreciation_type=="y")) {
	echo "<script type='text/javascript'>
	function ajoute_a_textarea_vide() {
		champs_textarea=document.getElementsByTagName('textarea');
		//alert('champs_textarea.length='+champs_textarea.length);
		for(i=0;i<champs_textarea.length;i++){
			if(champs_textarea[i].value=='') {
				champs_textarea[i].value=document.getElementById('ajout_a_textarea_vide').value;
			}
		}
	}
</script>\n";
}
//=========================
require("../lib/footer.inc.php");
 
