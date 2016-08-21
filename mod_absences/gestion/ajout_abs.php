<?php
/*
*
*$Id$
*
 * Copyright 2001, 2002 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../lib/functions.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
	die();
};

if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
	die();
}


if (empty($_GET['mode']) and empty($_POST['mode'])) {$mode="";}
    else { if (isset($_GET['mode'])) {$mode=$_GET['mode'];} if (isset($_POST['mode'])) {$mode=$_POST['mode'];} }
$mode_cop = $mode;
if (empty($_GET['page']) AND empty($_POST['page'])) {$page="";}
    else { if (isset($_GET['page'])) {$page=$_GET['page'];} if (isset($_POST['page'])) {$page=$_POST['page'];} }
if (empty($_POST['eleve_absent'])) {$eleve_absent = ''; } else {$eleve_absent=$_POST['eleve_absent']; }
if (empty($_POST['classe_absent'])) {$classe_absent = ''; } else {$classe_absent=$_POST['classe_absent']; }
if (empty($_POST['classe_choix'])) {$classe_choix = ''; } else {$classe_choix=$_POST['classe_choix']; }
if (empty($_POST['groupe_absent'])) {$groupe_absent = ''; } else {$groupe_absent=$_POST['groupe_absent']; }

if (empty($_POST['id_absence_eleve'])) {$id_absence_eleve = ''; } else {$id_absence_eleve=$_POST['id_absence_eleve']; }
if (empty($_POST['d_heure_absence_eleve'])) {$d_heure_absence_eleve = ''; } else {$d_heure_absence_eleve=$_POST['d_heure_absence_eleve']; }
if (empty($_POST['a_heure_absence_eleve'])) {$a_heure_absence_eleve = ''; } else {$a_heure_absence_eleve=$_POST['a_heure_absence_eleve']; }
if (empty($_POST['d_heure_absence_eleve_ins'])) {$d_heure_absence_eleve_ins = ''; } else {$d_heure_absence_eleve_ins=$_POST['d_heure_absence_eleve_ins']; }
if (empty($_POST['a_heure_absence_eleve_ins'])) {$a_heure_absence_eleve_ins = ''; } else {$a_heure_absence_eleve_ins=$_POST['a_heure_absence_eleve_ins']; }

// ============ Réécriture progressive de l'initialisation des variables ==================== //
$nb_i = isset($_POST['nb_i']) ? $_POST['nb_i'] : '1';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$action_sql = isset($_POST['action_sql']) ? $_POST['action_sql'] : '';
$fiche = isset($_POST['fiche']) ? $_POST['fiche'] : (isset($_GET['fiche']) ? $_GET['fiche'] : '');

// si pas de sélection on retourne à la sélection
if((empty($classe_choix) or $classe_choix === 'tous') and empty($eleve_absent[0]) and empty($id) and $action_sql === '') {
	header("Location:select.php?type=$type");
}

//if(empty($eleve_absent[0])==true and $action_sql === '' and $mode != 'eleve') { $mode="classe"; } else { $mode="eleve"; }
if(!isset($eleve_absent[0]) and empty($eleve_absent[0]) and $mode !='eleve')
{
	if (empty($_POST['classe_choix'])) {
		$classe_absent = '';
	} else {
		$classe_absent=$_POST['classe_choix'];
	}
	$classe_choix_eleve = $classe_absent;
	$mode = 'classe'; $mode_init = 'classe';
} else {
	$mode = 'eleve';
	$mode_init = 'eleve';
}

$verification = '0';
$id_absence_eleve = $id;
$total = '0';
$erreur = '0';
$nb = '0';

//requête pour liste les motif d'absence
$requete_liste_motif = "SELECT init_motif_absence, def_motif_absence
													FROM ".$prefix_base."absences_motifs
													ORDER BY init_motif_absence ASC";

//si c'est une classe qui est sélectionné on sélectionne tous les élèves de cette classe.
if($mode === 'classe')
{
	//je compte les élève si = 0 alors on redirige
	$cpt_eleves = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*)
							FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
							WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
							AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
							AND id = '".$classe_choix_eleve."'"),0);

	// christian modif du 15/01/2007 if($cpt_eleves === '0') { header("Location:select.php?type=$type"); }
	//je recherche tous les élèves de la classe sélectionnée
	/*
	$requete_eleve ="SELECT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.classe, ".$prefix_base."classes.id, ".$prefix_base."classes.nom_complet
									FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
									WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
									AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
									AND id = '".$classe_choix_eleve."'
								GROUP BY nom, prenom";
	*/
	$requete_eleve ="SELECT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.classe, ".$prefix_base."classes.id, ".$prefix_base."classes.nom_complet
									FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
									WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
									AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
									AND id = '".$classe_choix_eleve."'
								GROUP BY eleves.login, nom, prenom"; // 20100430
	$execution_eleve = mysqli_query($GLOBALS["mysqli"], $requete_eleve) or die('Erreur SQL !'.$requete_eleve.'<br />'.mysqli_error($GLOBALS["mysqli"]));
	$cpt_eleve = 0;
	while ($data_eleve = mysqli_fetch_array($execution_eleve))
	{
		//insertion de l'élève dans la varibale $eleve_absent
		$eleve_absent[$cpt_eleve] = $data_eleve['login'];
		$cpt_eleve = $cpt_eleve + 1;
	}
}

// On fait le rapport avec la table horaires_etablissement pour éviter de vérifier le dimanche s'il est ouvert
$sql_h = "SELECT jour_horaire_etablissement, ouvert_horaire_etablissement FROM horaires_etablissement LIMIT 7";
$query_h = mysqli_query($GLOBALS["mysqli"], $sql_h);
$test_jour_dimanche = 'non';
while($rep = mysqli_fetch_array($query_h)){
	if ($rep["jour_horaire_etablissement"] == 'dimanche' AND $rep["ouvert_horaire_etablissement"] == 1) {
		$test_jour_dimanche = 'oui';
	}
} // while


/* ========== On commence à traiter la saisie des absences, les demandes ont été faites =============== */

if($action_sql == "ajouter" or $action_sql == "modifier")
{

	include "../lib/function_abs.php";

    $total = '0';
    $j = '0';

	// Ici commence le traitement à proprement parler des saisies
    while ($total < $nb_i) // $nb_i étant envoyé par le navigateur et représentant le nombre d'absences envoyées par le navigateur
    {
		$erreur = '0';
		$verification = '0';

		$type_absence_eleve = $_POST['type_absence_eleve'];
		if(isset($_POST['active'][$total]) and !empty($_POST['active'][$total])) {
			$active_absence_eleve = $_POST['active'][$total]; // si c'est oui, alors c'est que cette demande doit être traitée
		} else {
			$active_absence_eleve = 'non';
		}

		$eleve_absence_eleve = $_POST['eleve_absence_eleve'][$total]; // le login de l'élève analysé

	    if($mode != 'classe')
		{
			$justify_absence_eleve = $_POST['justify_absence_eleve'][$total]; // N = non, T = par téléphone et O = oui
            $info_justify_absence_eleve = $_POST['info_justify_absence_eleve'][$total]; // le texte de justif (saisie à la main)
            $motif_absence_eleve_ins = $_POST['motif_absence_eleve'][$total]; // Voir liste des motifs d'absences
            $d_date_absence_eleve_ins = date_sql($_POST['d_date_absence_eleve'][$total]); // début de l'absence jj/mm/aaaa
            $a_date_absence_eleve_ins = date_sql($_POST['a_date_absence_eleve'][$total]); // fin de l'absence jj/mm/aaaa
            $d_heure_absence_eleve = $_POST['d_heure_absence_eleve'][$total]; // heuredébut hh:mm
            $a_heure_absence_eleve = $_POST['a_heure_absence_eleve'][$total]; // heure fin hh:mm
        	$dp_absence_eleve = $_POST['dp_absence_eleve'][$total];
            $ap_absence_eleve = $_POST['ap_absence_eleve'][$total];
		} else {
            $justify_absence_eleve = $_POST['justify_absence_eleve'][0];
            $info_justify_absence_eleve = $_POST['info_justify_absence_eleve'][0];
			$motif_absence_eleve_ins = $_POST['motif_absence_eleve'][0];
			$d_date_absence_eleve_ins = date_sql($_POST['d_date_absence_eleve'][0]);
			$a_date_absence_eleve_ins = date_sql($_POST['a_date_absence_eleve'][0]);
			$d_heure_absence_eleve = $_POST['d_heure_absence_eleve'][0];
			$a_heure_absence_eleve = $_POST['a_heure_absence_eleve'][0];
            $dp_absence_eleve = $_POST['dp_absence_eleve'][0];
			$ap_absence_eleve = $_POST['ap_absence_eleve'][0];
		}

		$eleve_absent[$total] = $eleve_absence_eleve ;


		if($active_absence_eleve === 'oui')
		{
			if ($d_heure_absence_eleve=="00:00" or $d_heure_absence_eleve=="") {
				$d_heure_absence_eleve = "";
			}
			if ($a_heure_absence_eleve=="00:00" or $a_heure_absence_eleve=="") {
				$a_heure_absence_eleve = "";
			}
			if ($a_date_absence_eleve_ins == "AAAA-MM-JJ"
					or $a_date_absence_eleve_ins == ""
					or $a_date_absence_eleve_ins == "JJ/MM/AAAA"
					or $a_date_absence_eleve_ins == "--") {

				// La date de début et de fin sont alors identiques si la fin n'est pas  renseignée
				$a_date_absence_eleve_ins = $d_date_absence_eleve_ins;

			}
			if ($d_heure_absence_eleve != "") {
				$d_heure_absence_eleve_ins = $d_heure_absence_eleve.":00";
			}
			if ($a_heure_absence_eleve != "") {
				$a_heure_absence_eleve_ins = $a_heure_absence_eleve.":00";
			}

			//mettre les heures par rapport à une période si période sélectionné
			if ($dp_absence_eleve != "")
			{
				$requete_recherche_periode = 'SELECT * FROM '.$prefix_base.'edt_creneaux
														WHERE id_definie_periode="'.$dp_absence_eleve.'"';

                $resultat_recherche_periode = mysqli_query($GLOBALS["mysqli"], $requete_recherche_periode) or die('Erreur SQL !'.$requete_recherche_periode.'<br />'.mysqli_error($GLOBALS["mysqli"]));
                $data_recherche_periode = mysqli_fetch_array($resultat_recherche_periode);
                $d_heure_absence_eleve_ins = $data_recherche_periode['heuredebut_definie_periode'];
                if (empty($ap_absence_eleve) === true)
                {
					$a_heure_absence_eleve_ins = $data_recherche_periode['heurefin_definie_periode'];
                } else {
					$requete_recherche_periode = 'SELECT * FROM '.$prefix_base.'edt_creneaux
														WHERE id_definie_periode="'.$ap_absence_eleve.'"';
					$resultat_recherche_periode = mysqli_query($GLOBALS["mysqli"], $requete_recherche_periode) or die('Erreur SQL !'.$requete_recherche_periode.'<br />'.mysqli_error($GLOBALS["mysqli"]));
					$data_recherche_periode = mysqli_fetch_array($resultat_recherche_periode);
					$a_heure_absence_eleve_ins = $data_recherche_periode['heurefin_definie_periode'];
				}
			}

			//Vérification

			$d_date_absence_eleve_verif = explode('-',$d_date_absence_eleve_ins);
			$a_date_absence_eleve_verif = explode('-',$a_date_absence_eleve_ins);

			if (verif_date($d_date_absence_eleve_ins) === "pass")
			{
				$verification = '1';
                if (verif_date($a_date_absence_eleve_ins) === "pass")
                {
                   	$verification = '1';
					if ($d_date_absence_eleve_ins <= $a_date_absence_eleve_ins)
					{
						$verification = '1';
						if (date("w", mktime(0, 0, 0, $d_date_absence_eleve_verif[1], $d_date_absence_eleve_verif[2], $d_date_absence_eleve_verif[0])) != '0' OR $test_jour_dimanche == 'oui')
                        {
							$verification = '1';
							if (date("w", mktime(0, 0, 0, $a_date_absence_eleve_verif[1], $a_date_absence_eleve_verif[2], $a_date_absence_eleve_verif[0])) != '0' OR $test_jour_dimanche == 'oui')
							{
                                $verification = '1';
                                if(( $d_heure_absence_eleve != "" and $a_heure_absence_eleve != "") or ( $dp_absence_eleve != '' or $ap_absence_eleve != '' ))
                                {
                                	$verification = '1';
                                    if ($a_heure_absence_eleve_ins > $d_heure_absence_eleve_ins)
                                    {
                                        $verification = '1';
                                    } else {
                                        if ($d_date_absence_eleve_ins == $a_date_absence_eleve_ins)
                                        {
                                            $verification = '11';
											$erreur = '1';
											$texte_erreur="L'heure de d&eacute;but ne peut pas être plus grande ou égale à celle de fin";
                                        } else {
											$verification = '1';
										}
                                    }
                                } else {
                                    if(( ($d_heure_absence_eleve === '' || '00:00') and ($a_heure_absence_eleve === '' || '00:00')) and ( $dp_absence_eleve === '' and $ap_absence_eleve === '' ))
									{
										$verification = '2';
										$texte_erreur = "il n'y a pas d'horaire ou de créneaux horaire de saisie";
									}
									if(( $d_heure_absence_eleve != '' and $a_heure_absence_eleve != '' ) and ( $dp_absence_eleve != '' or $ap_absence_eleve != '' ))
									{
										$verification = '3';
										$texte_erreur = "vous ne pouvez pas saisir une période et une heure";
									}
									$erreur = '1';
                                }
							} else {
								$verification = '7';
								$erreur = '1';
								$texte_erreur = "la date de fin tombe un dimanche";
							}
						} else {
							$verification = '6';
							$erreur = '1';
							$texte_erreur = "la date de debut tombe un dimanche";
						}
                    } else {
						$verification = '8';
						$erreur='1';
						$texte_erreur = "La date de debut doit &ecirc;tre plus petite que la date de fin...";
					}
                } else {
					$verification = '5';
					$erreur = '1';
					$texte_erreur = "la date de fin n'est pas correcte";
				}
			} else {
				$verification = '4';
				$erreur = '1';
				$texte_erreur = "La date de debut n'est pas correcte";
			}



        	/* ******************************************** */
        	/* gestion de l'ajout dans la table absences_rb */
        	/* gerer_absence($id,$eleve_id,$retard_absence,$groupe_id='',$edt_id='',$jour_semaine='',$creneau_id='',$debut_ts,$fin_ts,$date_saisie,$login_saisie) */

			$explode_heuredeb = explode(":", $d_heure_absence_eleve_ins);
			$explode_heurefin = explode(":", $a_heure_absence_eleve_ins);
			$explode_date_debut = explode("/", date_fr($d_date_absence_eleve_ins));
			$explode_date_fin = explode("/", date_fr($a_date_absence_eleve_ins));
			$debut_ts = mktime($explode_heuredeb[0], $explode_heuredeb[1], 0, $explode_date_debut[1], $explode_date_debut[0], $explode_date_debut[2]);
			$fin_ts = mktime($explode_heurefin[0], $explode_heurefin[1], 0, $explode_date_fin[1], $explode_date_fin[0], $explode_date_fin[2]);
			$date_saisie = mktime(date("H"), date("i"), 0, date("m"), date("d"), date("Y"));
			$login_saisie = $_SESSION['login'];
			$action = 'ajouter';

			if ( $action_sql === "ajouter" )
			{

				gerer_absence('',$eleve_absence_eleve,'A','','','','',$debut_ts,$fin_ts,$date_saisie,$login_saisie,$action);

			}
			elseif ( $action_sql === "modifier" )
			{

				modifier_absences_rb($id,$debut_ts,$fin_ts);

			}

        	/*                                              */
        	/* ******************************************** */


			// on vérifie si une absences est déja définie

			//requete dans la base absence eleve
			if ( $action_sql === "ajouter" ) {
				$requete = "SELECT * FROM ".$prefix_base."absences_eleves
									WHERE eleve_absence_eleve = '".$eleve_absence_eleve."'
									AND d_date_absence_eleve <= '".$d_date_absence_eleve_ins."'
									AND  a_date_absence_eleve >= '".$d_date_absence_eleve_ins."'";
			} elseif ( $action_sql === "modifier" ) {
				$requete = "SELECT * FROM ".$prefix_base."absences_eleves
									WHERE eleve_absence_eleve='".$eleve_absence_eleve."'
									AND d_date_absence_eleve <= '".$d_date_absence_eleve_ins."'
									AND a_date_absence_eleve >= '".$d_date_absence_eleve_ins."'
									AND id_absence_eleve <> '".$id."'";
			}
			$resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));

			if($d_date_absence_eleve_ins === $a_date_absence_eleve_ins)
			{

        		//on prend les données pour les vérifier
        		while ($data = mysqli_fetch_array($resultat))
				{

                	if ($d_heure_absence_eleve_ins <= $data['d_heure_absence_eleve']
						and ($a_heure_absence_eleve_ins <= $data['a_heure_absence_eleve']
						and $a_heure_absence_eleve_ins >=  $data['d_heure_absence_eleve']))
                	{
                    	$id_abs = $data['id_absence_eleve'];

                    	// rédéfinie l'heure de fin
                    	$a_heure_absence_eleve_ins = $data['a_heure_absence_eleve'];
                    	// supprime l'absences dans la base
                    	$req_delete = "DELETE FROM ".$prefix_base."absences_eleves WHERE id_absence_eleve ='".$id_abs."'";
                    	$req_sql2 = mysqli_query($GLOBALS["mysqli"], $req_delete);
						// vérification du courrier lettre de justificatif
						modif_suivi_du_courrier($id_abs, $eleve_absence_eleve);

					} else {
                    	if (($d_heure_absence_eleve_ins >= $data['d_heure_absence_eleve'] and $d_heure_absence_eleve_ins <= $data['a_heure_absence_eleve']) and $a_heure_absence_eleve_ins >= $data['a_heure_absence_eleve'])
                        {
                        	$id_abs = $data['id_absence_eleve'];

							// rédéfinie l'heure de debut
							$d_heure_absence_eleve_ins = $data['d_heure_absence_eleve'];
							// supprime l'absences dans la base
							$req_delete = "DELETE FROM ".$prefix_base."absences_eleves WHERE id_absence_eleve ='".$id_abs."'";
							$req_sql2 = mysqli_query($GLOBALS["mysqli"], $req_delete);
							// vérification du courrier lettre de justificatif
							modif_suivi_du_courrier($id_abs, $eleve_absence_eleve);
						} else {
							if ($d_heure_absence_eleve_ins >= $data['d_heure_absence_eleve'] and $a_heure_absence_eleve_ins <= $data['a_heure_absence_eleve'])
							{
                            	$erreur='1';
                                $verification = '10';
                                $texte_erreur="une absence est déja enregistré dans cette horaire";
                                $erreur_aff_d_date_absence_eleve = date_fr($data['d_date_absence_eleve']);
                                $erreur_aff_a_date_absence_eleve = date_fr($data['a_date_absence_eleve']);
                                $erreur_aff_d_heure_absence_eleve = $data['d_heure_absence_eleve'];
                                $erreur_aff_a_heure_absence_eleve= $data['a_heure_absence_eleve'];
                            } else {
                                if ($d_heure_absence_eleve_ins <= $data['d_heure_absence_eleve'] and $a_heure_absence_eleve_ins >= $data['a_heure_absence_eleve'])
                                {
                                	$id_abs = $data['id_absence_eleve'];

                                    $erreur='1';
                                    $verification = '10';
                                    $texte_erreur="une absence est déja enregistré dans cette horaire";
                                    $erreur_aff_d_date_absence_eleve = date_fr($data['d_date_absence_eleve']);
                                    $erreur_aff_a_date_absence_eleve = date_fr($data['a_date_absence_eleve']);
                                    $erreur_aff_d_heure_absence_eleve = $data['d_heure_absence_eleve'];
                                    $erreur_aff_a_heure_absence_eleve= $data['a_heure_absence_eleve'];
                                }
							}
						}
					}
				}
			}

			//requete dans la base absence eleve
			if ( $action_sql === 'ajouter' ) {
				$requete = "SELECT * FROM ".$prefix_base."absences_eleves
									WHERE eleve_absence_eleve='".$eleve_absence_eleve."' ";
			}
			if ( $action_sql === 'modifier' ) {
				$requete = "SELECT * FROM ".$prefix_base."absences_eleves
									WHERE eleve_absence_eleve='".$eleve_absence_eleve."'
									AND id_absence_eleve <> '".$id."'";
			}
			$resultat_m = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));

			if($d_date_absence_eleve_ins != $a_date_absence_eleve_ins)
			{
				//on prend les donnée pour les vérifier
				while ($data = mysqli_fetch_array($resultat_m))
				{

					if($d_date_absence_eleve_ins < $data['d_date_absence_eleve'] and ($a_date_absence_eleve_ins <= $data['a_date_absence_eleve'] and $a_date_absence_eleve_ins >= $data['d_date_absence_eleve']))
					{
						$id_abs = $data['id_absence_eleve'];

						// rédéfinie la date de fin
						$a_date_absence_eleve_ins = $data['a_date_absence_eleve'];
                        $a_heure_absence_eleve_ins = $data['a_heure_absence_eleve'];
                        // supprime l'absences dans la base
                        $req_delete = "DELETE FROM ".$prefix_base."absences_eleves WHERE id_absence_eleve ='".$id_abs."'";
                        $req_sql2 = mysqli_query($GLOBALS["mysqli"], $req_delete);
						// vérification du courrier lettre de justificatif
						modif_suivi_du_courrier($id_abs, $eleve_absence_eleve);
					} elseif(($d_date_absence_eleve_ins >= $data['d_date_absence_eleve'] and $d_date_absence_eleve_ins <= $data['a_date_absence_eleve']) and $a_date_absence_eleve_ins > $data['a_date_absence_eleve'])
                    {
                    	$id_abs = $data['id_absence_eleve'];

						// rédéfinie la date de debut
                        $d_date_absence_eleve_ins = $data['d_date_absence_eleve'];
                        $d_heure_absence_eleve_ins = $data['d_heure_absence_eleve'];
                        // supprime l'absences dans la base
                        $req_delete = "DELETE FROM ".$prefix_base."absences_eleves WHERE id_absence_eleve ='".$id_abs."'";
                        $req_sql2 = mysqli_query($GLOBALS["mysqli"], $req_delete);
						// vérification du courrier lettre de justificatif
						modif_suivi_du_courrier($id_abs, $eleve_absence_eleve);
					} elseif($d_date_absence_eleve_ins < $data['d_date_absence_eleve'] and $a_date_absence_eleve_ins > $data['a_date_absence_eleve'])
					{
                    	$erreur = '1';
                        $verification = '10';
                        $texte_erreur="vous essayez d'enregistrer une absence dans un intervale de temps d'absence déja saisie";
                        $id_abs = $data['id_absence_eleve'];
                        $erreur_aff_d_date_absence_eleve = date_fr($data['d_date_absence_eleve']);
                        $erreur_aff_a_date_absence_eleve = date_fr($data['a_date_absence_eleve']);
                        $erreur_aff_d_heure_absence_eleve = $data['d_heure_absence_eleve'];
                        $erreur_aff_a_heure_absence_eleve= $data['a_heure_absence_eleve'];
						/*
						$id_abs = $data['id_absence_eleve'];

                        // supprime l'absences dans la base
                        	$req_delete = "DELETE FROM ".$prefix_base."absences_eleves WHERE id_absence_eleve ='".$id_abs."'";
                            $req_sql2 = mysql_query($req_delete);
						*/
					} elseif($d_date_absence_eleve_ins >= $data['d_date_absence_eleve'] and $a_date_absence_eleve_ins <= $data['a_date_absence_eleve'])
                    {
						$erreur = '1';
						$verification = '10';
                        $texte_erreur="une absence est déja enregistrée dans cet intervale de date";
						$id_abs = $data['id_absence_eleve'];
						$erreur_aff_d_date_absence_eleve = date_fr($data['d_date_absence_eleve']);
						$erreur_aff_a_date_absence_eleve = date_fr($data['a_date_absence_eleve']);
						$erreur_aff_d_heure_absence_eleve = $data['d_heure_absence_eleve'];
						$erreur_aff_a_heure_absence_eleve= $data['a_heure_absence_eleve'];
					}
				}
			}

   			// fin de la vérifiation
			if ($erreur === '1'){

				if($verification != '1') {

					$type_absence_eleve_erreur[$j] = $type_absence_eleve;
					$eleve_absence_eleve_erreur[$j] = $eleve_absence_eleve;
					$justify_absence_eleve_erreur[$j] = $justify_absence_eleve;
					$info_justify_absence_eleve_erreur[$j] = $info_justify_absence_eleve;
					$motif_absence_eleve_erreur[$j] = $motif_absence_eleve_ins;
					$d_date_absence_eleve_erreur[$j] = date_fr($d_date_absence_eleve_ins);
					$a_date_absence_eleve_erreur[$j] = date_fr($a_date_absence_eleve_ins);
					$d_heure_absence_eleve_erreur[$j] = $d_heure_absence_eleve;
					$a_heure_absence_eleve_erreur[$j] = $a_heure_absence_eleve;
					$dp_absence_eleve_erreur[$j] = $dp_absence_eleve;
					$ap_absence_eleve_erreur[$j] = $ap_absence_eleve;
					$verification_erreur[$j] = $verification;

					$j = $j + 1;

				}
			} else {
				if ( $action_sql === "ajouter" ) {
					$requete="INSERT INTO ".$prefix_base."absences_eleves
									(type_absence_eleve,
									eleve_absence_eleve,
									justify_absence_eleve,
									info_justify_absence_eleve,
									motif_absence_eleve,
									d_date_absence_eleve,
									a_date_absence_eleve,
									d_heure_absence_eleve,
									a_heure_absence_eleve,
									saisie_absence_eleve)
								values (
									'$type_absence_eleve',
									'$eleve_absence_eleve',
									'$justify_absence_eleve',
									'$info_justify_absence_eleve',
									'$motif_absence_eleve_ins',
									'$d_date_absence_eleve_ins',
									'$a_date_absence_eleve_ins',
									'$d_heure_absence_eleve_ins',
									'$a_heure_absence_eleve_ins',
									'".$_SESSION['login']."')";
				}
				if ( $action_sql === "modifier" ) {
					$requete="UPDATE ".$prefix_base."absences_eleves SET
                                                    justify_absence_eleve = '$justify_absence_eleve',
                                                    info_justify_absence_eleve = '$info_justify_absence_eleve',
                                                    motif_absence_eleve = '$motif_absence_eleve_ins',
                                                    d_date_absence_eleve = '$d_date_absence_eleve_ins',
                                                    a_date_absence_eleve = '$a_date_absence_eleve_ins',
                                                    d_heure_absence_eleve = '$d_heure_absence_eleve_ins',
                                                    a_heure_absence_eleve = '$a_heure_absence_eleve_ins',
                                                    saisie_absence_eleve = '".$_SESSION['login']."'
                                                WHERE
													id_absence_eleve = '".$id."'";
				}
				$resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
				// connaitre l'id de l'enregistrement
				if ( $action_sql === 'ajouter' ) {
					$num_id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
				}
				if ( $action_sql === 'modifier' ) {
					$num_id = $id;
				}

				// ++++++++ gestion du courrier ++++++++ //

				if ( $justify_absence_eleve === 'N' and $motif_absence_eleve_ins != 'RE') {
					//envoie d'une lettre de justification
					$date_emis = date('Y-m-d');
					$heure_emis = date('H:i:s');
					$cpt_lettre_suivi = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*)
														FROM ".$prefix_base."lettres_suivis
														WHERE quirecois_lettre_suivi = '".$eleve_absence_eleve."'
														AND emis_date_lettre_suivi = '".$date_emis."'
														AND partde_lettre_suivi = 'absences_eleves'"),0);

					if( $cpt_lettre_suivi == 0 ) {
						//si aucune lettre n'a encore été demandée alors on en crée une
						$requete = "INSERT INTO ".$prefix_base."lettres_suivis
									(quirecois_lettre_suivi,
									partde_lettre_suivi,
									partdenum_lettre_suivi,
									quiemet_lettre_suivi,
									emis_date_lettre_suivi,
									emis_heure_lettre_suivi,
									type_lettre_suivi,
									statu_lettre_suivi)
								VALUES (
									'".$eleve_absence_eleve."',
									'absences_eleves',
									',".$num_id.",',
									'".$_SESSION['login']."',
									'".$date_emis."',
									'".$heure_emis."',
									'6',
									'en attente')";
						mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
					} else {
						// si une lettre a déja été demandée alors on la modifie
						// on cherche la lettre concernée et on prend les id déja disponibles
						// puis on y ajoute le nouvel id
						$requete_info ="SELECT * FROM ".$prefix_base."lettres_suivis
									WHERE emis_date_lettre_suivi = '".$date_emis."'
									AND partde_lettre_suivi = 'absences_eleves'";
						$requete_info ="SELECT * FROM ".$prefix_base."lettres_suivis
									WHERE emis_date_lettre_suivi = '".$date_emis."'
									AND partde_lettre_suivi = 'absences_eleves'
									AND quirecois_lettre_suivi= '".$eleve_absence_eleve."'";
						$execution_info = mysqli_query($GLOBALS["mysqli"], $requete_info) or die('Erreur SQL !'.$requete_info.'<br />'.mysqli_error($GLOBALS["mysqli"]));
						while ( $donne_info = mysqli_fetch_array($execution_info))
						{
							$id_lettre_suivi = $donne_info['id_lettre_suivi'];
							$id_deja_present = $donne_info['partdenum_lettre_suivi'];
						}
						$tableau_deja_existe = explode(',', $id_deja_present);

						if ( in_array($num_id, $tableau_deja_existe) ) {
							$id_ajout = $id_deja_present;
						} else {
							$id_ajout = $id_deja_present.$num_id.',';
						}
						$requete = "UPDATE ".$prefix_base."lettres_suivis
										SET partdenum_lettre_suivi = '".$id_ajout."',
										quiemet_lettre_suivi = '".$_SESSION['login']."',
										type_lettre_suivi = '6'
										WHERE id_lettre_suivi = '".$id_lettre_suivi."'";
						mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
					}
				}

				// si on modifie un absence et que la lettre n'a pas encore était envoyé
				// alors on l'enlève des lettres de suivi
				if ( ($justify_absence_eleve === 'O' or $justify_absence_eleve === 'T') and $motif_absence_eleve_ins != 'RE') {
					//envoie d'une lettre de justification
					$date_emis = date('Y-m-d');
					$heure_emis = date('H:i:s');
					$cpt_lettre_suivi = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE quirecois_lettre_suivi = '".$eleve_absence_eleve."' AND emis_date_lettre_suivi = '".$date_emis."' AND partde_lettre_suivi = 'absences_eleves'"),0);

					if( $cpt_lettre_suivi == 1 ) {
						//si une lettre a déjas été demandé alors on la modifi
						// on cherche la lettre concerné et on prend les id déjas disponible puis on y ajout le nouvelle id
						$requete_info ="SELECT * FROM ".$prefix_base."lettres_suivis
										WHERE emis_date_lettre_suivi = '".$date_emis."'
										AND partde_lettre_suivi = 'absences_eleves'";
						$requete_info ="SELECT * FROM ".$prefix_base."lettres_suivis
										WHERE emis_date_lettre_suivi = '".$date_emis."'
										AND partde_lettre_suivi = 'absences_eleves'
										AND quirecois_lettre_suivi= '".$eleve_absence_eleve."'";
						$execution_info = mysqli_query($GLOBALS["mysqli"], $requete_info) or die('Erreur SQL !'.$requete_info.'<br />'.mysqli_error($GLOBALS["mysqli"]));
						while ( $donne_info = mysqli_fetch_array($execution_info))
						{
							$id_lettre_suivi = $donne_info['id_lettre_suivi'];
							$id_deja_present = $donne_info['partdenum_lettre_suivi'];
						}
						$tableau_deja_existe = explode(',', $id_deja_present);
						$cpt_tab = '1'; // pas 0 car vide on commence par ,
						$id_ajout = '';

						//echo '<pre>';
						//print_r($tableau_deja_existe);
						//echo '</pre>';

						while ( !empty($tableau_deja_existe[$cpt_tab]) )
						{
							if ( $tableau_deja_existe[$cpt_tab] != $num_id and empty($id_ajout) ) {
								$id_ajout =  ','.$tableau_deja_existe[$cpt_tab].',';
							} elseif ($tableau_deja_existe[$cpt_tab] != $num_id and !empty($id_ajout) ) {
								$id_ajout =  $id_ajout.$tableau_deja_existe[$cpt_tab].',';
							}
							$cpt_tab = $cpt_tab + 1;
						}
						if ( !empty($tableau_deja_existe[2]) ) {
							// s'il reste d'autre id alors on modifie sinon on supprime
							$requete = "UPDATE ".$prefix_base."lettres_suivis SET partdenum_lettre_suivi = '".$id_ajout."', quiemet_lettre_suivi = '".$_SESSION['login']."', type_lettre_suivi = '6' WHERE id_lettre_suivi = '".$id_lettre_suivi."'";
							mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
						} else {
							$requete = "DELETE FROM ".$prefix_base."lettres_suivis WHERE id_lettre_suivi = '".$id_lettre_suivi."'";
							mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
						}
					}
				}

				if ( $motif_absence_eleve_ins === 'RE') {
					//envoie d'une lettre de renvoi
					$date_emis = date('Y-m-d');
					$heure_emis = date('H:i:s');
					$cpt_lettre_suivi = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE quirecois_lettre_suivi = '".$eleve_absence_eleve."' AND emis_date_lettre_suivi = '".$date_emis."' AND partde_lettre_suivi = 'absences_eleves'"),0);
					if( $cpt_lettre_suivi == 0 ) {
						//si aucune lettre n'a encore été demandé alors on en créer une
						$requete = "INSERT INTO ".$prefix_base."lettres_suivis (quirecois_lettre_suivi, partde_lettre_suivi, partdenum_lettre_suivi, quiemet_lettre_suivi, emis_date_lettre_suivi, emis_heure_lettre_suivi, type_lettre_suivi, statu_lettre_suivi) VALUES ('".$eleve_absence_eleve."', 'absences_eleves', ',".$num_id.",', '".$_SESSION['login']."', '".$date_emis."', '".$heure_emis."', '4', 'en attente')";
						mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
					} else {
						//si une lettre a déjas été demandé alors on la modifi
						// on cherche la lettre concerné et on prend les id déjas disponible puis on y ajout le nouvelle id
						$requete_info ="SELECT * FROM ".$prefix_base."lettres_suivis
											WHERE emis_date_lettre_suivi = '".$date_emis."'
											AND partde_lettre_suivi = 'absences_eleves'";
						$requete_info ="SELECT * FROM ".$prefix_base."lettres_suivis
											WHERE emis_date_lettre_suivi = '".$date_emis."'
											AND partde_lettre_suivi = 'absences_eleves'
											AND quirecois_lettre_suivi= '".$eleve_absence_eleve."'";
						$execution_info = mysqli_query($GLOBALS["mysqli"], $requete_info) or die('Erreur SQL !'.$requete_info.'<br />'.mysqli_error($GLOBALS["mysqli"]));
						while ( $donne_info = mysqli_fetch_array($execution_info))
						{
							$id_lettre_suivi = $donne_info['id_lettre_suivi'];
							$id_deja_present = $donne_info['partdenum_lettre_suivi'];
						}
						$id_ajout = $id_deja_present.$num_id.',';
						$requete = "UPDATE ".$prefix_base."lettres_suivis SET partdenum_lettre_suivi = '".$id_ajout."', quiemet_lettre_suivi = '".$_SESSION['login']."', type_lettre_suivi = '4' WHERE id_lettre_suivi = '".$id_lettre_suivi."'";
						mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
					}
				}
			}
		} //fermer le oui si active = non
		$total = $total + 1;

	} //while ($total < $nb_i)

	// si tout est ok on redirige
	if(!isset($eleve_absence_eleve_erreur[0]) and empty($eleve_absence_eleve_erreur[0]))
	{
		if ( $action_sql === "ajouter" ) {
			header("Location:select.php?type=A");
		}
		if ( $action_sql === "modifier" ) {
			if($fiche === 'oui') {
				header("Location:gestion_absences.php?type=A&select_fiche_eleve=$eleve_absence[0]&aff_fiche=abseleve#abseleve");
			} else {
				header("Location:gestion_absences.php?type=A");
			}
        }
	}
} // if($action_sql == "ajouter" or $action_sql == "modifier")
//echo $erreur." ab ".$verification." ";

//$annee_en_cours_t=annee_en_cours_t($datej);
//$datejour = date('d/m/Y');
//$datej = $_SESSION['datej'];
//$datejour=$_SESSION['datejour'];
//$annee_en_cours_t=annee_en_cours_t($datej);

if ($action === 'supprimer')
{
	include "../lib/function_abs.php";

	if (empty($_GET['date_ce_jour']) and empty($_POST['date_ce_jour'])) {
		$date_ce_jour = '';
	} else {
		if (isset($_GET['date_ce_jour'])) {
			$date_ce_jour = $_GET['date_ce_jour'];
		}
		if (isset($_POST['date_ce_jour'])) {
			$date_ce_jour = $_POST['date_ce_jour'];
		}
	}

    $id_absence_eleve = $_GET['id'];
    $requete_sup = "SELECT eleve_absence_eleve FROM ".$prefix_base."absences_eleves
								WHERE id_absence_eleve ='$id_absence_eleve'";
	$resultat_sup = mysqli_query($GLOBALS["mysqli"], $requete_sup) or die('Erreur SQL !'.$requete_sup.'<br />'.mysqli_error($GLOBALS["mysqli"]));
	$login_eleve = mysqli_fetch_array($resultat_sup);
	 // si une réponse à un courrier expédié à était reçus alors on ne peut supprimer l'absences
    $cpt_lettre_recus = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi LIKE '%,".$id_absence_eleve.",%' AND (statu_lettre_suivi = 'recus' OR envoye_date_lettre_suivi != '0000-00-00')"),0);

    if( $cpt_lettre_recus === '0' ) {

		// Vérification des champs
		if ( $id_absence_eleve != '' )
		{

			// suppression dans la table absence_rb
       		suppr_absences_rb($id_absence_eleve);


            //Requete d'insertion MYSQL
            $requete = "DELETE FROM ".$prefix_base."absences_eleves WHERE id_absence_eleve ='".$id_absence_eleve."'";
            // Execution de cette requete
            mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));

			// on vérify s'il y a un courrier si oui on le supprime s'il fait parti d'un ensemble de courrier alors on le modifi.
			// première option il existe une lettre qui fait seulement référence à cette id donc suppression
			$cpt_lettre_suivi = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi = ',".$id_absence_eleve.",'"),0);
			if( $cpt_lettre_suivi == 1 ) {
	              $requete = "DELETE FROM ".$prefix_base."lettres_suivis
				  					WHERE partde_lettre_suivi = 'absences_eleves'
									  AND type_lettre_suivi = '6'
									  AND partdenum_lettre_suivi = ',".$id_absence_eleve.",'";
	              mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
			}
			// deuxième option il existe une lettre qui fait référence à cette id mais à d'autre aussi donc modification
			$cpt_lettre_suivi = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi LIKE '%,".$id_absence_eleve.",%'"),0);
			if( $cpt_lettre_suivi == 1 ) {
				$requete = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM ".$prefix_base."lettres_suivis WHERE partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi LIKE '%,".$id_absence_eleve.",%'");
				$donnee = mysqli_fetch_array($requete);
				$remplace_sa = ','.$id_absence_eleve.',';
				$modifier_par = my_ereg_replace($remplace_sa,',',$donnee['partdenum_lettre_suivi']);
				$requete = "UPDATE ".$prefix_base."lettres_suivis
									SET partdenum_lettre_suivi = '".$modifier_par."'
									WHERE partde_lettre_suivi = 'absences_eleves'
									AND type_lettre_suivi = '6'
									AND partdenum_lettre_suivi LIKE '%,".$id_absence_eleve.",%'";

				mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
			}
		    if($fiche === 'oui') {
		 	header("Location:gestion_absences.php?type=A&select_fiche_eleve=$login_eleve[0]&aff_fiche=abseleve#abseleve");
			} else {
				header("Location:gestion_absences.php?type=A");
			}
		}
	} else {
	        if($fiche === 'oui') {
		 	header("Location:gestion_absences.php?type=A&select_fiche_eleve=$login_eleve[0]&aff_fiche=abseleve#abseleve");
			} else {
				header("Location:gestion_absences.php?type=A");
			}
		/* manque gestion d'erreur impossible de supprimer car il existe une lettre reçus sur cette absences */ }
}


$i = '0';
if ($action === "modifier")
{
	$requete_modif = "SELECT * FROM ".$prefix_base."absences_eleves
								WHERE id_absence_eleve ='$id_absence_eleve'";
	$resultat_modif = mysqli_query($GLOBALS["mysqli"], $requete_modif) or die('Erreur SQL !'.$requete_modif.'<br />'.mysqli_error($GLOBALS["mysqli"]));

	while ($data_modif = mysqli_fetch_array($resultat_modif))
	{
		$type_absence_eleve[$i] = $data_modif['type_absence_eleve'];
		$eleve_absent[$i] = $data_modif['eleve_absence_eleve'];
        $justify_absence_eleve[$i] = $data_modif['justify_absence_eleve'];
        $info_justify_absence_eleve[$i] = $data_modif['info_justify_absence_eleve'];
        $motif_absence_eleve[$i] = $data_modif['motif_absence_eleve'];
        $d_date_absence_eleve[$i] = date_fr($data_modif['d_date_absence_eleve']);
        $a_date_absence_eleve[$i] = date_fr($data_modif['a_date_absence_eleve']);
        $d_heure_absence_eleve[$i] = $data_modif['d_heure_absence_eleve'];
        $a_heure_absence_eleve[$i] = $data_modif['a_heure_absence_eleve'];
        $i = $i + 1;
    }
}
// s'il y a eu un problème alors on réaffecte le donnée au nom des variables du formulaire
$i = '0';
if(isset($eleve_absence_eleve_erreur[0]) and !empty($eleve_absence_eleve_erreur[0]))
{
	unset($type_absence_eleve);
	unset($eleve_absent);
	unset($justify_absence_eleve);
	unset($info_justify_absence_eleve);
	unset($motif_absence_eleve);
	unset($d_date_absence_eleve);
	unset($a_date_absence_eleve);
	unset($d_heure_absence_eleve);
	unset($a_heure_absence_eleve);
	unset($dp_absence_eleve);
	unset($ap_absence_eleve);

    while (isset($eleve_absence_eleve_erreur[$i]))
    {

		$type_absence_eleve[$i] = $type_absence_eleve_erreur[$i];
		$eleve_absent[$i] = $eleve_absence_eleve_erreur[$i];
		$justify_absence_eleve[$i] = $justify_absence_eleve_erreur[$i];
		$info_justify_absence_eleve[$i] = $info_justify_absence_eleve_erreur[$i];
		$motif_absence_eleve[$i] = $motif_absence_eleve_erreur[$i];
		$d_date_absence_eleve[$i] = $d_date_absence_eleve_erreur[$i];
		$a_date_absence_eleve[$i] = $a_date_absence_eleve_erreur[$i];
		$d_heure_absence_eleve[$i] = $d_heure_absence_eleve_erreur[$i];
		$a_heure_absence_eleve[$i] = $a_heure_absence_eleve_erreur[$i];
	    $dp_absence_eleve[$i] = $dp_absence_eleve_erreur[$i];
		$ap_absence_eleve[$i] = $ap_absence_eleve_erreur[$i];
	    if(isset($id) and !empty($id)) {
			$action = 'modifier';
		}
 	    $mode = $mode_cop;
		$i = $i + 1;
    }
}

//$datej = $_SESSION['datej'];
//$datejour=$_SESSION['datejour'];
//$datej = date('Y-m-d');
$datejour = date('d/m/Y');
//recuperation date veille et date modification didier
if ($action === "modifier"){
$daterecup=explode('/',$d_date_absence_eleve[0]);
}else{
$daterecup=explode('-',date('d-m-Y'));	
}
$day=$daterecup[0];
$month=$daterecup[1];
$year=$daterecup[2];
$datefab=getdate(mktime(0, 0, 0, $month , $day -1, $year)); 
$datehier=$datefab['year']."-".$datefab['mon']."-".$datefab['mday'];
$datej=$year."-".$month."-".$day;
$annee_en_cours_t=annee_en_cours_t($datej);
//**************** EN-TETE *****************
$titre_page = "Gestion des absences";
require_once("../../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();
?>
<script type="text/javascript" language="javascript">
<!--
function getHeure(input_check,input_go,form_choix){
 var date_select=new Date();
 var heures=date_select.getHours();if(heures<10){heures="0"+heures;}
 var minutes=date_select.getMinutes();if(minutes<10){minutes="0"+minutes;}
// nom du formulaire
  var form_action = form_choix;
// id des élèments
  var input_go_id = input_go.id;
  var input_check_id = input_check.id;
// modifie le contenue de l'élèment
if(document.forms[form_action].elements[input_check_id].checked) { document.forms[form_action].elements[input_go_id].value=heures+":"+minutes; } else { document.forms[form_action].elements[input_go_id].value=''; }
}
 // -->
</script>

<?php // on gère le retour en fonction du statut
if ($_SESSION["statut"] == 'autre') {
	$retour = './select.php?type=A';
}else{
	if($fiche === 'oui') {
		$aff_fiche = '&amp;select_fiche_eleve='.$eleve_absent[0].'&aff_fiche=abseleve#abseleve';
	}else{
		$aff_fiche = '';
	}
	$retour = 'gestion_absences.php?type='.$type.$aff_fiche;
}

echo "<p class=\"bold\"><a href=\"".$retour."\"><img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

//si un élève est sélectionné ou modifié
if (!isset($eleve_absent[1]) and empty($eleve_absent[1]) and $mode != "classe")
{

    $i = '0';

	//Configuration du calendrier
	//include("../../lib/calendrier/calendrier.class.php");
	include("../../lib/calendrier/calendrier_id.class.php");
	//$cal_1 = new Calendrier("form1", "d_date_absence_eleve[0]");
	//$cal_2 = new Calendrier("form1", "a_date_absence_eleve[0]");
	$cal_1 = new Calendrier("form1", "d_date_absence_eleve_0");
	$cal_2 = new Calendrier("form1", "a_date_absence_eleve_0");

/* div de centrage du tableau pour ie5 */
?><div style="text-align:center"><?php
    //affichage des messages d'erreur
        if ($erreur === '1') { ?>
            <table class="table_erreur" border="0" cellpadding="4" cellspacing="2">
              <tr>
                <td><img src="../images/attention.png" width="28" height="28" alt="" /></td>
                <td class="erreur"><strong>Erreur : <?php echo $texte_erreur; ?></strong></td>
              </tr>
            </table>
        <?php } ?>
        <?php if($verification === '10' or $verification === '9') { ?>
                       <table style="margin: auto; width: 500px;" border="0" cellspacing="2" cellpadding="0">
                         <tr class="fond_rouge">
                           <td class="norme_absence_blanc_min"><strong>Du</strong></td>
                           <td class="norme_absence_blanc_min"><strong>Au</strong></td>
                           <td class="norme_absence_blanc_min"><strong>De</strong></td>
                           <td class="norme_absence_blanc_min"><strong>A</strong></td>
                         </tr>
                         <tr class="fond_rouge_2">
                           <td class="norme_absence_min"><?php echo $erreur_aff_d_date_absence_eleve; ?></td>
                           <td class="norme_absence_min"><?php echo $erreur_aff_a_date_absence_eleve; ?></td>
                           <td class="norme_absence_min"><?php echo $erreur_aff_d_heure_absence_eleve; ?></td>
                           <td class="norme_absence_min"><?php echo $erreur_aff_a_heure_absence_eleve; ?></td>
                         </tr>
                        </table>
        <?php } ?>
    <form method="post" action="ajout_abs.php?type=<?php echo $type; ?>&amp;id=<?php echo $id; ?>&amp;mode=<?php echo $mode; ?>" name="form1" id="form1">
     <fieldset class="fieldset_efface">
      <table class="entete_tableau_absence" border="0" cellspacing="0" cellpadding="1">
        <tr>
          <td class="titre_tableau_absence" nowrap><strong>Absence de l'&eacute;l&egrave;ve</strong></td>
          <td class="titre_tableau_absence_valider"><input type="submit" name="submit" value="Valider" /></td>
        </tr>
        <tr class="tr_tableau_absence_titre">
          <td class="centre">Identit&eacute; de l'&eacute;l&egrave;ve</td>
          <td class="centre">Information sur l'absence</td>
        </tr>
        <tr class="td_tableau_absence_1">
          <td class="centre">
        <?php
		$mode='eleve';
		if ($mode==="eleve") {
			$requete_id="SELECT * FROM ".$prefix_base."eleves WHERE login='".$eleve_absent[$i]."'";
		}
		if ($mode==="classe") {
			$requete_id="SELECT * FROM ".$prefix_base."classes WHERE id='".$classe_absent[$i]."'";
		}
		/* if ($mode=="groupe") { $requete_id="SELECT * FROM ".$prefix_base."groupes WHERE id_groupe='".$groupe_absent[$i]."'"; } */
		$resultat_id = mysqli_query($GLOBALS["mysqli"], $requete_id) or die('Erreur SQL !'.$requete_id.'<br />'.mysqli_error($GLOBALS["mysqli"]));
		while($data_id = mysqli_fetch_array($resultat_id))
		{
			if ($mode==="eleve") { ?>
				<strong><?php echo strtoupper($data_id['nom']); ?></strong>
				<br />
				<?php echo ucfirst($data_id['prenom']); $id_eleve = $data_id['login']; $id_eleve_photo = $data_id['elenoet']; ?>
				<br />
				<div class="norme_absence_bleu">
					<strong><?php echo classe_de($data_id['login']);
			}?>
					</strong>
				</div>
		<?php
			if ($mode==="classe") { ?>
				<strong><?php echo $data_id['classe']; ?></strong>
				<br /><?php $id_classe = $data_id['id']; ?><br />
		<?php }
			/* if ($mode=="groupe") { ?><strong><?php echo $data_id['nom_groupe']; ?></strong><br /><?php $id_groupe = $data_id['id_groupe']; ?><br /><?php } */
		}
		if (getSettingValue("active_module_trombinoscopes")=='y') {
			$photo = '';
			$photo = nom_photo($id_eleve_photo,"eleves",2);
			if ( $photo === NULL or !file_exists($photo) ) {
				$photo = "../../mod_trombinoscopes/images/trombivide.jpg";
			}
			$valeur=redimensionne_image($photo);
        ?>
			<img src="<?php echo $photo; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" />
			<br />
		<?php
		}
		if ($mode==="eleve")
		{
			$test_dispense = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$id_eleve."' AND type_absence_eleve='D'"),0);
			if ($test_dispense != '0')
			{  ?>
				<table class="tableau_info_compt" border="0" cellspacing="0" cellpadding="2">
					<tr>
                        <td class="tableau_info_disp"><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $id_eleve; ?>&amp;type=D',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="voir les dispenses">Dispense détecté</a></td>
                    </tr>
                </table>
			<?php
			} ?>
                <table class="tableau_info_compt" border="0" cellspacing="0" cellpadding="2">
                    <tr>
                        <td class="tableau_info_compt"><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $id_eleve; ?>&amp;type=<?php echo $type; ?>',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="voir les absences">voir absences</a></td>
                    </tr>
                </table>
		<?php
		} ?>
          </td>
          <td>
              <table class="tableau_100" border="0" cellspacing="1" cellpadding="2">
                <tr class="tr_tableau_absence_titre">
                  <td><strong>Date</strong></td>
                  <td><strong>Heure</strong></td>
                  <td><strong>P&eacute;riode</strong></td>
                </tr>
                <tr class="td_tableau_absence_1">
					<td>du&nbsp;<input name="d_date_absence_eleve[<?php echo $i; ?>]" id="d_date_absence_eleve_<?php echo $i; ?>" onfocus="javascript:this.select()" type="text" value="<?php if(isset($d_date_absence_eleve) and !empty($d_date_absence_eleve)) { echo $d_date_absence_eleve[$i]; } else { echo $datejour; } ?>" size="10" maxlength="10" /><a href="#calend" onClick="<?php
	//echo $cal_1->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170);
	echo $cal_1->get_strPopup('../../lib/calendrier/pop.calendrier_id.php', 350, 170);
?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a></td>
					<td>de&nbsp;<input name="d_heure_absence_eleve[<?php echo $i; ?>]" onfocus="javascript:this.select()" type="text" value="<?php if (isset($d_heure_absence_eleve[$i]) and !empty($d_heure_absence_eleve[$i])) { echo $d_heure_absence_eleve[$i]; } else { ?>00:00<?php } ?>" size="5" maxlength="5" /></td>
					<td>
		
						en
                     	<select name="dp_absence_eleve[<?php echo $i; ?>]">
		<?php
				$requete_pe = ('SELECT * FROM '.$prefix_base.'edt_creneaux ORDER BY heuredebut_definie_periode, nom_definie_periode ASC');
				$resultat_pe = mysqli_query($GLOBALS["mysqli"], $requete_pe) or die('Erreur SQL !'.$requete_pe.'<br />'.mysqli_error($GLOBALS["mysqli"]));
		?>
							<option value="">pas de s&eacute;lection</option>
		<?php
					while($data_pe = mysqli_fetch_array($resultat_pe)) { ?>
							<option value="<?php echo $data_pe['id_definie_periode']; ?>" <?php if(isset($dp_absence_eleve_erreur[$i]) and $dp_absence_eleve_erreur[$i] == $data_pe['id_definie_periode']) { ?>selected<?php } else { } ?>><?php echo $data_pe['nom_definie_periode']." ".heure_court($data_pe['heuredebut_definie_periode'])."-".heure_court($data_pe['heurefin_definie_periode']); ?></option>
		<?php
                	} ?>
						</select>
		           </td>
                </tr>
                <tr>
					<td>au&nbsp;<input name="a_date_absence_eleve[<?php echo $i; ?>]" id="a_date_absence_eleve_<?php echo $i; ?>"  onfocus="javascript:this.select()" type="text" value="<?php if (isset($a_date_absence_eleve[$i]) and !empty($a_date_absence_eleve[$i])) { echo $a_date_absence_eleve[$i]; } else { ?>JJ/MM/AAAA<?php } ?>" size="10" maxlength="10" /><a href="#calend" onClick="<?php
	//echo $cal_2->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170);
	echo $cal_2->get_strPopup('../../lib/calendrier/pop.calendrier_id.php', 350, 170);
?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a></td>
					<td>&agrave;&nbsp;&nbsp;<input name="a_heure_absence_eleve[<?php echo $i; ?>]"  onfocus="javascript:this.select()" type="text" value="<?php if (isset($a_heure_absence_eleve[$i]) and !empty($a_heure_absence_eleve[$i])) { echo $a_heure_absence_eleve[$i]; } else { ?>00:00<?php } ?>" size="5" maxlength="5" /></td>
					<td>
		
						en
                    	<select name="ap_absence_eleve[<?php echo $i; ?>]">
        <?php
            	$requete_pe = ('SELECT * FROM '.$prefix_base.'edt_creneaux ORDER BY heuredebut_definie_periode, nom_definie_periode ASC');
                $resultat_pe = mysqli_query($GLOBALS["mysqli"], $requete_pe) or die('Erreur SQL !'.$requete_pe.'<br />'.mysqli_error($GLOBALS["mysqli"])); ?>

							<option value="">pas de s&eacute;lection</option><?php

				while ( $data_pe = mysqli_fetch_array($resultat_pe)) { ?>
							<option value="<?php echo $data_pe['id_definie_periode']; ?>" <?php if(isset($ap_absence_eleve[$i]) and $ap_absence_eleve[$i] === $data_pe['id_definie_periode']) { ?>selected="selected"<?php } ?>><?php echo $data_pe['nom_definie_periode']." ".heure_court($data_pe['heuredebut_definie_periode'])."-".heure_court($data_pe['heurefin_definie_periode']); ?></option><?php
                                  } ?>
						</select>        
                    </td>
                </tr>
        <?php if($action!="modifier" and $erreur!='1') { ?>
                <tr>
					<td colspan="3">
						<span class="norme_absence_bleu">
							<strong>!</strong>Si la date du et au sont identiques ne renseignez que "du"
						</span>
					</td>
                </tr>
        <?php } ?>
                <tr class="tr_tableau_absence_titre">
                  <td colspan="2"><strong>Motif</strong></td>
                  <td><strong>Justification</strong></td>
                </tr>
                <tr>
                  <td colspan="2">
                    <select name="motif_absence_eleve[<?php echo $i; ?>]">
		<?php
			$resultat_liste_motif = mysqli_query($GLOBALS["mysqli"], $requete_liste_motif) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysqli_error($GLOBALS["mysqli"]));
            while ( $data_liste_motif = mysqli_fetch_array($resultat_liste_motif)) { ?>
						<option value="<?php echo $data_liste_motif['init_motif_absence']; ?>" <?php if(isset($motif_absence_eleve[$i]) and $motif_absence_eleve[$i] === $data_liste_motif['init_motif_absence']) { ?>selected="selected"<?php } ?>><?php echo $data_liste_motif['init_motif_absence']." - ".$data_liste_motif['def_motif_absence']; ?></option>
		<?php
			} ?>
                    </select>
                    <br />
           <?php /* <strong>Action</strong><br />
                    <select name="action_absence_eleve[<?php echo $i; ?>]" id="action_absence_eleve">
                      <option selected>aucune action</option>
                      <option value="AJ">AJ - attente de justification</option>
                      <option value="CL">CL - mot sur le carnet de liaison</option>
                      <option value="CO">CO - convocation de l'&eacute;l&egrave;ve</option>
                      <option value="LR">LR - lettre de rappel &agrave; la famille</option>
                      <option value="RE">RE - lettre recommander</option>
                    </select> */ ?>
                   </td>
                   <td>
                    <select name="justify_absence_eleve[<?php echo $i; ?>]">
                      <option value="N" <?php if (($action === "modifier" and $justify_absence_eleve[$i] === "N") or ( isset($justify_absence_eleve_erreur[$i]) and $justify_absence_eleve_erreur[$i] === "N")) { ?>selected<?php } else { ?>selected<?php } ?>>Aucune</option>
                      <option value="O" <?php if (($action === "modifier" and $justify_absence_eleve[$i] === "O") or ( isset($justify_absence_eleve_erreur[$i]) and $justify_absence_eleve_erreur[$i] === "O")) { ?>selected<?php } ?>>Oui</option>
                      <option value="T" <?php if (($action === "modifier" and $justify_absence_eleve[$i] === "T") or ( isset($justify_absence_eleve_erreur[$i]) and $justify_absence_eleve_erreur[$i] === "T")) { ?>selected<?php } ?>>Par t&eacute;l&eacute;phone</option>
                    </select><br />Plus d'info<br /><textarea name="info_justify_absence_eleve[<?php echo $i; ?>]" cols="20" rows="2"><?php if ($action == "modifier") { echo $info_justify_absence_eleve[$i]; } if (isset($info_justify_absence_eleve_erreur[$i])) { echo $info_justify_absence_eleve_erreur[$i]; } ?></textarea>
                    </td>
                 </tr>
            </table>
          </td>
        </tr>
      </table>
            <input name="active[<?php echo $i; ?>]" type="hidden" value="oui" />
            <input type="hidden" name="eleve_absence_eleve[<?php echo $i; ?>]" <?php  if ($action == "modifier") {?>value="<?php echo $eleve_absent[0]; ?>"<?php } else {?>value="<?php echo $eleve_absent[0]; ?>"<?php } ?> />
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <input type="hidden" name="eleve_absence[<?php echo $i; ?>]" value="<?php echo $eleve_absent[$i]; ?>" />
            <input type="hidden" name="fiche" value="<?php echo $fiche; ?>" />
            <input type="hidden" name="type_absence_eleve" <?php  if ($action === 'modifier') {?>value="<?php echo $type; ?>"<?php } else {?>value="<?php echo $type; ?>"<?php } ?> />
            <input type="hidden" name="action_sql" <?php  if ($action === 'modifier') {?>value="modifier"<?php } else {?>value="ajouter"<?php } ?> />
            <?php if ($mode==="classe") { ?><input type="hidden" name="classe_absent[<?php echo $i; ?>]" value="<?php echo $id_classe; ?>" /><?php } else { ?><input type="hidden" name="classe_choix" value="" /><?php } ?>
            <?php /* if($mode=="groupe") { ?><input type="hidden" name="groupe_absent[<?php echo $i; ?>]" value="<?php echo $id_groupe; ?>" /><?php } */ ?>
            <input type="hidden" name="nb_i" value="<?php echo $i+1; ?>" />
     </fieldset>
    </form>
    <?php
            $requete_t = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$eleve_absent[$i]."' AND (d_date_absence_eleve = '".$datehier."' OR (d_date_absence_eleve <= '".$datehier."' AND  a_date_absence_eleve >= '".$datehier."')) AND type_absence_eleve = 'A'";
            if ( $action === "ajouter" and $erreur === '1') { $requete_t = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$eleve_absence."' AND d_date_absence_eleve <= '".$d_date_absence_eleve."' AND  a_date_absence_eleve >= '".$d_date_absence_eleve."'  AND type_absence_eleve = 'A'"; }
            if ( $action === "modifier" and $erreur === '1') { $requete_t = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$eleve_absence."' AND d_date_absence_eleve <= '".$d_date_absence_eleve."' AND  a_date_absence_eleve >= '".$d_date_absence_eleve."' AND id_absence_eleve <> '".$id."'  AND type_absence_eleve = 'A'"; }
           $resultat = mysqli_query($GLOBALS["mysqli"], $requete_t) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
    ?>

                    <div class="norme_absence_orange";><strong>Absences de l'élève la veille</strong></div>
                    <table style="margin: auto; width: 500px;" border="0" cellspacing="2" cellpadding="0">
                        <tr class="fond_orange2">
                          <td class="norme_absence_blanc_min"><strong>Du</strong></td>
                          <td class="norme_absence_blanc_min"><strong>Au</strong></td>
                          <td class="norme_absence_blanc_min"><strong>De</strong></td>
                          <td class="norme_absence_blanc_min"><strong>A</strong></td>
                        </tr>
             <?php while ($data = mysqli_fetch_array($resultat))
                    { ?>
                        <tr class="fond_orange">
                          <td class="norme_absence_min"><?php echo date_fr($data['d_date_absence_eleve']); ?></td>
                          <td class="norme_absence_min"><?php echo date_fr($data['a_date_absence_eleve']); ?></td>
                          <td class="norme_absence_min"><?php echo $data['d_heure_absence_eleve']; ?></td>
                          <td class="norme_absence_min"><?php echo $data['a_heure_absence_eleve']; ?></td>
                        </tr>
             <?php } ?>
                    </table>
    <?php
            $requete_t = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$eleve_absent[$i]."' AND (d_date_absence_eleve = '".$datej."' OR (d_date_absence_eleve <= '".$datej."' AND  a_date_absence_eleve >= '".$datej."')) AND type_absence_eleve = 'A'";
            if ( $action === "ajouter" and $erreur === '1') { $requete_t = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$eleve_absence."' AND d_date_absence_eleve <= '".$d_date_absence_eleve."' AND  a_date_absence_eleve >= '".$d_date_absence_eleve."'  AND type_absence_eleve = 'A'"; }
            if ( $action === "modifier" and $erreur === '1') { $requete_t = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$eleve_absence."' AND d_date_absence_eleve <= '".$d_date_absence_eleve."' AND  a_date_absence_eleve >= '".$d_date_absence_eleve."' AND id_absence_eleve <> '".$id."'  AND type_absence_eleve = 'A'"; }
           $resultat = mysqli_query($GLOBALS["mysqli"], $requete_t) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
    ?>

                    <div class="norme_absence_rouge"><strong>liste des absences déja enregistrées pour cette date</strong></div>
                    <table style="margin: auto; width: 500px;" border="0" cellspacing="2" cellpadding="0">
                        <tr class="fond_rouge">
                          <td class="norme_absence_blanc_min"><strong>Du</strong></td>
                          <td class="norme_absence_blanc_min"><strong>Au</strong></td>
                          <td class="norme_absence_blanc_min"><strong>De</strong></td>
                          <td class="norme_absence_blanc_min"><strong>A</strong></td>
                        </tr>
             <?php while ($data = mysqli_fetch_array($resultat))
                    { ?>
                        <tr class="fond_rouge_2">
                          <td class="norme_absence_min"><?php echo date_fr($data['d_date_absence_eleve']); ?></td>
                          <td class="norme_absence_min"><?php echo date_fr($data['a_date_absence_eleve']); ?></td>
                          <td class="norme_absence_min"><?php echo $data['d_heure_absence_eleve']; ?></td>
                          <td class="norme_absence_min"><?php echo $data['a_heure_absence_eleve']; ?></td>
                        </tr>
             <?php } ?>
                    </table>
                    
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php }
// fin du formulaire pour un élève ou une modification


//forumulaire si plusieurs élèves sélectionné ou une classe.
$i = '0';
if (isset($eleve_absent[1]) and !empty($eleve_absent[1]) or $mode === 'classe') {

       //Configuration du calendrier
         //include("../../lib/calendrier/calendrier.class.php");
         include("../../lib/calendrier/calendrier_id.class.php");
         while(empty($eleve_absent[$i])== false)
           {
               //$cal_a[$i] = new Calendrier("form1", "d_date_absence_eleve[$i]");
               //$cal_b[$i] = new Calendrier("form1", "a_date_absence_eleve[$i]");
               $cal_a[$i] = new Calendrier("form1", "d_date_absence_eleve_$i");
               $cal_b[$i] = new Calendrier("form1", "a_date_absence_eleve_$i");
               $i = $i+1;
           }
    ?>
<?php /* div de centrage du tableau pour ie5 */ ?>
  <div style="text-align:center">
<?php $entete_absence_1 = 'Ajout un ou plusieurs absences'; ?>
  <form method="post" action="ajout_abs.php?type=<?php echo $type; ?>" name="form1" id="form1">
   <fieldset class="fieldset_efface">
    <table class="entete_tableau_absence" border="0" cellspacing="0" cellpadding="1">
        <tr>
            <td colspan="4" class="titre_tableau_absence" nowrap><?php echo $entete_absence_1; ?></td>
            <td class="titre_tableau_absence_valider"><input type="submit" name="submit" value="Valider" /></td>
        </tr>
        <tr class="tr_tableau_absence_titre">
            <td class="centre">Identit&eacute;</td>
            <td class="centre">Date</td>
            <td class="centre">Horaire</td>
            <td class="centre">Créneaux</td>
            <td>Indication</td>
        </tr>
    <?php   $i = '0'; $ic = '1';
       if ($erreur === '0') { $nb = count($eleve_absent); }
       if ($erreur === '1') { $nb = $j; }
       while($i < $nb) {
                            if ($ic==='1') { $ic='2'; $couleur_cellule="td_tableau_absence_1"; } else { $couleur_cellule="td_tableau_absence_2"; $ic='1'; }
       ?>
            <?php if (isset($verification_erreur[$i]) and $verification_erreur[$i] != '1') { ?>
             <tr class="table_erreur">
              <td class="centre"><img src="../images/attention.png" width="28" height="28" alt="" /></td>
              <td colspan="4" class="erreur"><strong>Erreur:
              <?php if ($verification_erreur[$i] === '2') { ?>il n'y a pas d'horaire saisi<?php } ?>
              <?php if ($verification_erreur[$i] === '3') { ?>vous ne pouvez pas saisir une période et une heure<?php } ?>
              <?php if ($verification_erreur[$i] === '4') { ?>La date de debut n'est pas correcte<?php } ?>
              <?php if ($verification_erreur[$i] === '5') { ?>la date de fin n'est pas correcte<?php } ?>
              <?php if ($verification_erreur[$i] === '6') { ?>la date de debut tombe un dimanche<?php } ?>
              <?php if ($verification_erreur[$i] === '7') { ?>la date de fin tombe un dimanche<?php } ?>
              <?php if ($verification_erreur[$i] === '8') { ?>La date  du debut doit &ecirc;tre plus petit que la date de fin<?php } ?>
              <?php if ($verification_erreur[$i] === '9') { ?>une absence est déja enregistrée pour cette date dans cette horaire défini<?php } ?>
              <?php if ($verification_erreur[$i] === '10') { ?>une absence est déja enregistrée pour cette date dans cette horaire défini<?php } ?>
              <?php if ($verification_erreur[$i] === '11') { ?>L'heure de debut ne peut pas être plus grande que celle de fin<?php } ?>
              </strong></td>
             </tr>
            <?php } ?>
        <tr class="<?php echo $couleur_cellule; ?>">
            <td>
              <?php
                  if ($mode != "classe") { ?><input name="active[<?php echo $i; ?>]" type="hidden" value="oui" /><?php } else { ?><input name="active[<?php echo $i; ?>]" type="checkbox" value="oui" checked="checked" /><?php } ?><br />
              <?php
                  if ($erreur === '0') { $requete_id="SELECT * FROM ".$prefix_base."eleves WHERE login='".$eleve_absent[$i]."'"; }
                  if ($erreur === '1') { $requete_id="SELECT * FROM ".$prefix_base."eleves WHERE login='".$eleve_absence_eleve_erreur[$i]."'"; }
                  $resultat_id = mysqli_query($GLOBALS["mysqli"], $requete_id) or die('Erreur SQL !'.$requete_id.'<br />'.mysqli_error($GLOBALS["mysqli"]));
                  $data_id = mysqli_fetch_array($resultat_id);
              ?>
              <strong><?php echo strtoupper($data_id['nom']); ?></strong><br /><?php echo ucfirst($data_id['prenom']); ?><br /><br /><?php echo classe_de($data_id['login']); $id_eleve = $data_id['login']; ?><input type="hidden" name="eleve_absence_eleve[<?php echo $i; ?>]" value="<?php echo $id_eleve; ?>" />
            <?php
            $test_dispense = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$id_eleve."' AND type_absence_eleve='D'"),0);
			if ($test_dispense != '0')
			{  ?>
				<div class="tableau_info_compt"><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $id_eleve; ?>&amp;type=D',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="voir les dispenses">Dispense détecté</a></div>
                  
			<?php
			} ?>
                
             <div class="tableau_info_compt"><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $id_eleve; ?>&amp;type=<?php echo $type; ?>',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="voir les absences">voir absences</a></div>
              </td>     
            
            <td><?php if(($mode === "classe" and $i === '0') or $mode != "classe") { ?>
              du&nbsp;&nbsp;<a href="#calend" onClick="<?php
	//echo $cal_a[$i]->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170);
	echo $cal_a[$i]->get_strPopup('../../lib/calendrier/pop.calendrier_id.php', 350, 170);
?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a><br /><input name="d_date_absence_eleve[<?php echo $i; ?>]" id="d_date_absence_eleve_<?php echo $i; ?>" onfocus="javascript:this.select()" type="text" value="<?php if(empty($d_date_absence_eleve)) { echo $datejour; } else { if($action == "modifier" ) { echo $d_date_absence_eleve[$i]; } else { if(isset($d_date_absence_eleve_erreur[$i])) { echo $d_date_absence_eleve_erreur[$i]; } else { echo date_fr($d_date_absence_eleve); } } } ?>" size="10" maxlength="10" /><br />
              au&nbsp;&nbsp;<a href="#calend" onClick="<?php
	//echo $cal_b[$i]->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170);
	echo $cal_b[$i]->get_strPopup('../../lib/calendrier/pop.calendrier_id.php', 350, 170);
?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a><br /><input name="a_date_absence_eleve[<?php echo $i; ?>]" id="a_date_absence_eleve_<?php echo $i; ?>" onfocus="javascript:this.select()" type="text" value="<?php if(isset($a_date_absence_eleve_erreur[$i])) { echo $a_date_absence_eleve_erreur[$i]; } else { ?>JJ/MM/AAAA<?php } ?>" size="10" maxlength="10" />
           </td><?php } ?>
            <td><?php if(($mode === "classe" and $i === '0') or $mode != "classe") { ?>
              de<br /><input name="d_heure_absence_eleve[<?php echo $i; ?>]" onfocus="javascript:this.select()" type="text" value="<?php if(isset($d_heure_absence_eleve_erreur[$i]) and $dp_absence_eleve == "") { echo $d_heure_absence_eleve_erreur[$i]; } else { ?>00:00<?php } ?>" size="5" maxlength="5" /><br />
              a<br /><input name="a_heure_absence_eleve[<?php echo $i; ?>]" onfocus="javascript:this.select()" type="text" value="<?php if(isset($a_heure_absence_eleve_erreur[$i]) and $dp_absence_eleve == "") { echo $a_heure_absence_eleve_erreur[$i]; } else { ?>00:00<?php } ?>" size="5" maxlength="5" />
            </td><?php } ?>
            <td><?php if(($mode === "classe" and $i === '0') or $mode != "classe") { ?>
              de<br />
              <select name="dp_absence_eleve[<?php echo $i; ?>]">
                <option value="">pas de s&eacute;lection</option>
                   <?php
                      $requete_pe = ('SELECT * FROM '.$prefix_base.'edt_creneaux ORDER BY heuredebut_definie_periode, nom_definie_periode ASC');
                      $resultat_pe = mysqli_query($GLOBALS["mysqli"], $requete_pe) or die('Erreur SQL !'.$requete_pe.'<br />'.mysqli_error($GLOBALS["mysqli"]));
                    while ( $data_pe = mysqli_fetch_array($resultat_pe)) { ?>
                              <option value="<?php echo $data_pe['id_definie_periode']; ?>" <?php if(isset($dp_absence_eleve_erreur[$i]) and $dp_absence_eleve_erreur[$i] == $data_pe['id_definie_periode']) { ?>selected<?php } else { } ?>><?php echo $data_pe['nom_definie_periode']." ".heure_court($data_pe['heuredebut_definie_periode'])."-".heure_court($data_pe['heurefin_definie_periode']); ?></option><?php
                           } ?>
              </select><br />
              a<br />
              <select name="ap_absence_eleve[<?php echo $i; ?>]">
                <option value="">pas de s&eacute;lection</option>
                    <?php
                        $requete_pe = ('SELECT * FROM '.$prefix_base.'edt_creneaux ORDER BY heuredebut_definie_periode, nom_definie_periode ASC');
                        $resultat_pe = mysqli_query($GLOBALS["mysqli"], $requete_pe) or die('Erreur SQL !'.$requete_pe.'<br />'.mysqli_error($GLOBALS["mysqli"]));
                    while ( $data_pe = mysqli_fetch_array($resultat_pe)) { ?>
                               <option value="<?php echo $data_pe['id_definie_periode']; ?>" <?php if(isset($dp_absence_eleve_erreur[$i]) and $dp_absence_eleve_erreur[$i] == $data_pe['id_definie_periode']) { ?>selected<?php } else { } ?>><?php echo $data_pe['nom_definie_periode']." ".heure_court($data_pe['heuredebut_definie_periode'])."-".heure_court($data_pe['heurefin_definie_periode']); ?></option><?php
                           } ?>
              </select>
            </td><?php } else { ?> IDEM <?php } ?>
            <td><?php if(($mode === "classe" and $i === '0') or $mode != "classe") { ?>
              motif<br />
              <select name="motif_absence_eleve[<?php echo $i; ?>]">
                  <?php
                      $resultat_liste_motif = mysqli_query($GLOBALS["mysqli"], $requete_liste_motif) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysqli_error($GLOBALS["mysqli"]));
                      while ( $data_liste_motif = mysqli_fetch_array($resultat_liste_motif))
                        { ?>
                            <option value="<?php echo $data_liste_motif['init_motif_absence']; ?>" <?php if(isset($motif_absence_eleve[$i]) and $motif_absence_eleve[$i] === $data_liste_motif['init_motif_absence']) { ?>selected="selected"<?php } ?>><?php echo $data_liste_motif['init_motif_absence']." - ".$data_liste_motif['def_motif_absence']; ?></option>
                  <?php } ?>
              </select><br />
              justification<br />
              <select name="justify_absence_eleve[<?php echo $i; ?>]">
                <option value="N" <?php if(isset($justify_absence_eleve[$i]) and $justify_absence_eleve[$i] === 'N') { ?>selected="selected"<?php } ?>>Aucune</option>
                <option value="T" <?php if(isset($justify_absence_eleve[$i]) and $justify_absence_eleve[$i] === 'T') { ?>selected="selected"<?php } ?>>Par t&eacute;l&eacute;phone</option>
                <option value="O" <?php if(isset($justify_absence_eleve[$i]) and $justify_absence_eleve[$i] === 'O') { ?>selected="selected"<?php } ?>>Oui</option>
              </select>
              <br />plus d'info<br />
              <input name="info_justify_absence_eleve[<?php echo $i; ?>]" type="text" <?php if(isset($info_justify_absence_eleve[$i]) and !empty($info_justify_absence_eleve[$i])) { ?>value="<?php echo $info_justify_absence_eleve[$i]; ?>"<?php } ?> />
            </td><?php } ?>
        </tr>
    <?php $i = $i+1; } ?>
    </table>
        <input type="hidden" name="mode" value="<?php echo $mode_init; ?>" />
        <input type="hidden" name="type_absence_eleve" <?php  if ($action === "modifier") {?>value="<?php echo $type; ?>"<?php } else {?>value="<?php echo $type; ?>"<?php } ?> />
        <input type="hidden" name="fiche" value="<?php echo $fiche; ?>" />
        <input type="hidden" name="action_sql" <?php  if ($action === "modifier") {?>value="modifier"<?php } else {?>value="ajouter"<?php } ?> />
        <input type="hidden" name="nb_i" value="<?php echo $i; ?>" />
        <?php if($mode==="classe") { ?><input type="hidden" name="classe_choix" value="<?php echo $classe_choix; ?>" /><?php } else { ?><input type="hidden" name="classe_choix" value="" /><?php } ?>
  </fieldset>
</form>
    <?php /* fin du div de centrage du tableau pour ie5 */ ?>
    </div>
<?php }
require("../../lib/footer.inc.php");
((is_null($___mysqli_res = mysqli_close($GLOBALS["mysqli"]))) ? false : $___mysqli_res); ?>

