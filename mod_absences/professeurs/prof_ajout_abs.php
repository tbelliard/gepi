<?php
/*
 *
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

// Resume session
$resultat_session = resumeSession();
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

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='y') {
    die("Le module n'est pas activé.");
}

// fonction de sécuritée
// uid de pour ne pas refaire renvoyer plusieurs fois le même formulaire
// autoriser la validation de formulaire $uid_post===$_SESSION['uid_prime']
if(empty($_SESSION['uid_prime'])) {
	$_SESSION['uid_prime']='';
}
if (empty($_GET['uid_post']) and empty($_POST['uid_post'])) {
	$uid_post='';
}else {
	if (isset($_GET['uid_post'])) {
		$uid_post=$_GET['uid_post'];
	}
	if (isset($_POST['uid_post'])) {
		$uid_post=$_POST['uid_post'];
	}
}
	$uid = md5(uniqid(microtime(), 1));
	   // on remplace les %20 par des espaces
	    $uid_post = eregi_replace('%20',' ',$uid_post);
if($uid_post===$_SESSION['uid_prime']) {
	$valide_form = 'yes';
} else {
	$valide_form = 'no';
}
	$_SESSION['uid_prime'] = $uid;
// fin de la fonction de sécuritée

// permet de supprimer un courrier s'il y a besoin par rapport à l'id de l'absence
function modif_suivi_du_courrier($id_absence_eleve, $eleve_absence_eleve) {
	global $prefix_base;
		// on vérify s'il y a un courrier si oui on le supprime s'il fait parti d'un ensemble de courrier alors on le modifi.
		// première option il existe une lettre qui fait seulement référence à cette id donc suppression
	$cpt_lettre_suivi = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE quirecois_lettre_suivi = '".$eleve_absence_eleve."' AND partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi = ',".$id_absence_eleve.",'"),0);
	if( $cpt_lettre_suivi == 1 ) {
		$requete = "DELETE FROM ".$prefix_base."lettres_suivis WHERE partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi = ',".$id_absence_eleve.",'";
		mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
	}
	// deuxième option il existe une lettre qui fait référence à cette id mais à d'autre aussi donc modification
	$cpt_lettre_suivi = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE quirecois_lettre_suivi = '".$eleve_absence_eleve."' AND partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi LIKE '%,".$id_absence_eleve.",%'"),0);
	if( $cpt_lettre_suivi == 1 ) {
		$requete = mysql_query("SELECT * FROM ".$prefix_base."lettres_suivis WHERE partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi LIKE '%,".$id_absence_eleve.",%'");
		$donnee = mysql_fetch_array($requete);
		$remplace_sa = ','.$id_absence_eleve.',';
		$modifier_par = ereg_replace($remplace_sa,',',$donnee['partdenum_lettre_suivi']);
		$requete = "UPDATE ".$prefix_base."lettres_suivis SET partdenum_lettre_suivi = '".$modifier_par."', envoye_date_lettre_suivi = '', envoye_heure_lettre_suivi = '', quienvoi_lettre_suivi = '' WHERE partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi LIKE '%,".$id_absence_eleve.",%'";
			mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
	}
}

if (empty($_POST['d_heure_absence_eleve'])) {
	$d_heure_absence_eleve = '';
} else {
	$d_heure_absence_eleve = $_POST['d_heure_absence_eleve'];
}
if (empty($_POST['a_heure_absence_eleve'])) {
	$a_heure_absence_eleve = '';
} else {
	$a_heure_absence_eleve = $_POST['a_heure_absence_eleve'];
}
if (empty($_POST['d_heure_absence_eleve_ins'])) {
	$d_heure_absence_eleve_ins = '';
} else {
	$d_heure_absence_eleve_ins = $_POST['d_heure_absence_eleve_ins'];
}
if (empty($_POST['a_heure_absence_eleve_ins'])) { $a_heure_absence_eleve_ins = ''; } else { $a_heure_absence_eleve_ins = $_POST['a_heure_absence_eleve_ins']; }

if (empty($_POST['heuredebut_definie_periode'])) {$heuredebut_definie_periode = ''; } else {$heuredebut_definie_periode=$_POST['heuredebut_definie_periode']; }
if (empty($_POST['heurefin_definie_periode'])) {$heurefin_definie_periode = ''; } else {$heurefin_definie_periode=$_POST['heurefin_definie_periode']; }

if(empty($etape)) { $etape = ''; }

if (empty($_POST['d_date_absence_eleve'])) { $d_date_absence_eleve = date('d/m/Y'); } else {$d_date_absence_eleve=$_POST['d_date_absence_eleve']; }
if (!empty($d_date_absence_eleve) AND $etape=='1' AND !empty($eleve_absent)) { $d_date_absence_eleve = date_fr($d_date_absence_eleve); }
if (getSettingValue("active_module_trombinoscopes")=='y')
	$photo = isset($_POST["photo"]) ? $_POST["photo"] :"";
$classe = isset($_POST["classe"]) ? $_POST["classe"] :"";
$eleve_initial = isset($_POST["eleve_initial"]) ? $_POST["eleve_initial"] :"";

// Si une classe et un élève sont définis en même temps, on réinitialise
if ($classe!="" and $eleve_initial!="") {
    $classe="";
    $eleve_initial="";
}
$etape = isset($_POST["etape"]) ? $_POST["etape"] :(isset($_GET["etape"]) ? $_GET["etape"] :1);
if (empty($_POST['action_sql'])) {$action_sql = ''; } else {$action_sql=$_POST['action_sql']; }


$id = isset($_POST["id"]) ? $_POST["id"] :"";

if (empty($_POST['saisie_absence_eleve'])) {$saisie_absence_eleve = ''; } else {$saisie_absence_eleve=$_POST['saisie_absence_eleve']; }
if (empty($_POST['eleve_absent'])) {$eleve_absent = ''; } else {$eleve_absent=$_POST['eleve_absent']; }
if (empty($_POST['active_absence_eleve'])) {$active_absence_eleve = ''; } else {$active_absence_eleve=$_POST['active_absence_eleve']; }
if (empty($_POST['active_retard_eleve'])) {$active_retard_eleve = ''; } else {$active_retard_eleve=$_POST['active_retard_eleve']; }
if (empty($_POST['heure_retard_eleve'])) {$heure_retard_eleve = ''; } else {$heure_retard_eleve=$_POST['heure_retard_eleve']; }

if (empty($_POST['edt_enregistrement'])) {$edt_enregistrement = ''; } else {$edt_enregistrement=$_POST['edt_enregistrement']; }

    if (empty($_GET['passage_form']) and empty($_POST['passage_form'])) {$passage_form='';}
     else { if (isset($_GET['passage_form'])) {$passage_form=$_GET['passage_form'];} if (isset($_POST['passage_form'])) {$passage_form=$_POST['passage_form'];} }
    $passage_auto='';

$heure_choix = date('G:i');
$num_periode = periode_actuel($heure_choix);
$datej = date('Y-m-d');
$annee_scolaire=annee_en_cours_t($datej);

$miseajour='';
$verification = '0';
$id_absence_eleve = $id;
$total = '0';
$erreur = '0';
$nb = '0';

if(($action_sql == "ajouter" or $action_sql == "modifier") and $valide_form==='yes') {
	$type_absence_eleve = $_POST['type_absence_eleve'];
	$d_date_absence_eleve_format_sql = date_sql($_POST['d_date_absence_eleve']);
	$a_date_absence_eleve_format_sql = $d_date_absence_eleve_format_sql;
	$justify_absence_eleve = "N";
	$motif_absence_eleve = "A";

	$nb_i = isset($_POST["nb_i"]) ? $_POST["nb_i"] :1;
	$total = '0';

	while ($total < $nb_i) {
		if(!empty($heure_retard_eleve[$total])) {
			$type_absence_eleve = "R";
			$heure_retard_eleve_ins = $_POST['heure_retard_eleve'][$total];
		} else {
			$type_absence_eleve = "A";
		}
		// Identifiant de l'élève
		if(empty($_POST['active_absence_eleve'][$total])) {
			$_POST['active_absence_eleve'][$total]='';
		}
		$eleve_absent_ins = $_POST['eleve_absent'][$total];
		$active_absence_eleve_ins = $_POST['active_absence_eleve'][$total];
		if($active_absence_eleve_ins == "1" or !empty($heure_retard_eleve[$total])) {
			// on vérifie si une absences est déja définie
			//requete dans la base absence eleve
			if ( $action_sql == "ajouter" ) {
				$requete = "SELECT * FROM absences_eleves
					WHERE eleve_absence_eleve='".$eleve_absent_ins."' AND
					d_date_absence_eleve <= '".$d_date_absence_eleve_format_sql."' AND
					a_date_absence_eleve >= '".$d_date_absence_eleve_format_sql."' AND
					type_absence_eleve = 'A'";
				$requete_retard = "SELECT * FROM absences_eleves
					WHERE eleve_absence_eleve='".$eleve_absent_ins."' AND
					d_date_absence_eleve = '".$d_date_absence_eleve_format_sql."' AND
					a_date_absence_eleve = '".$d_date_absence_eleve_format_sql."' AND
					type_absence_eleve = 'R'";
			}
			if ( $action_sql == "modifier" ) {
				$requete = "SELECT * FROM absences_eleves
					WHERE eleve_absence_eleve='".$eleve_absent_ins."' AND
					d_date_absence_eleve <= '".$d_date_absence_eleve_format_sql."' AND
					a_date_absence_eleve >= '".$d_date_absence_eleve_format_sql."' AND
					id_absence_eleve <> '".$id."'";
			}

			$resultat = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
			$resultat_retard = mysql_query($requete_retard) or die('Erreur SQL !'.$requete_retard.'<br />'.mysql_error());
			$heuredebut_definie_periode_ins = $d_heure_absence_eleve;
			$heurefin_definie_periode_ins = $a_heure_absence_eleve;

			if(!isset($active_retard_eleve[$total])) {
				$active_retard_eleve[$total]='0';
			}
			if($active_retard_eleve[$total]!='1') {
				//on prend les donnée pour les vérifier
				$miseajour='';
				while ($data = mysql_fetch_array($resultat)) {
					//id de la base sélectionné
					$id_abs = $data['id_absence_eleve'];
					//vérification
					if($data['d_heure_absence_eleve'] <= $heuredebut_definie_periode_ins and $data['a_heure_absence_eleve'] >= $heurefin_definie_periode_ins) {
						//on ne fait rien
					} else {
						if($data['d_heure_absence_eleve'] <= $heuredebut_definie_periode_ins and $data['a_heure_absence_eleve'] < $heurefin_definie_periode_ins) {
							//Update de Fin
							$id_abs = $data['id_absence_eleve'];
							$miseajour='fin';
							// vérification du courrier lettre de justificatif
							modif_suivi_du_courrier($id_abs, $eleve_absent_ins);
			  			}
						if($data['d_heure_absence_eleve'] >= $heuredebut_definie_periode_ins and $data['a_heure_absence_eleve'] > $heurefin_definie_periode_ins) {
							//Update de Début
                			$id_abs = $data['id_absence_eleve'];
							$miseajour='debut';
							// vérification du courrier lettre de justificatif
	  			  			modif_suivi_du_courrier($id_abs, $eleve_absent_ins);
			  			}
			  			if($data['d_heure_absence_eleve'] > $heuredebut_definie_periode_ins and $data['a_heure_absence_eleve'] < $heurefin_definie_periode_ins) {
							//Delete de l'enregistrement
	                        $req_delete = "DELETE FROM absences_eleves WHERE id_absence_eleve ='".$id_abs."'";
        	                $req_sql2 = mysql_query($req_delete);
			  			}
					}
				} // fin while ($data = mysql_fetch_array($resultat))

				while ($data_retard = mysql_fetch_array($resultat_retard)) {
					if ($data_retard['d_heure_absence_eleve'] >= $heuredebut_definie_periode_ins and $data_retard['d_heure_absence_eleve'] <= $heurefin_definie_periode_ins) {
						$id_ret = $data_retard['id_absence_eleve'];
						// supprime le retard de la base
						$req_delete = "DELETE FROM absences_eleves WHERE id_absence_eleve ='".$id_ret."'";
						$req_sql2 = mysql_query($req_delete);
                	}
				}
			} // if($active_retard_eleve[$total]!='1')

			if($active_retard_eleve[$total]==='1') {
				while ($data = mysql_fetch_array($resultat)) {
					if ($heure_retard_eleve[$total] >= $data['d_heure_absence_eleve'] and $heure_retard_eleve[$total] <= $data['a_heure_absence_eleve']) {
                    	$id_abs = $data['id_absence_eleve'];
						if($data['d_heure_absence_eleve']===$heuredebut_definie_periode_ins) {
							$req_delete = "DELETE FROM absences_eleves WHERE id_absence_eleve ='".$id_abs."'";
							$req_sql2 = mysql_query($req_delete);
		    			} else {
                    		// modifie l'absences
                    		$req_modifie = "UPDATE absences_eleves SET a_heure_absence_eleve = '$heuredebut_definie_periode_ins' WHERE id_absence_eleve ='".$id_abs."'";
                    		$req_sql2 = mysql_query($req_modifie);
			    		}
                	}
				}
			} // if($active_retard_eleve[$total]==='1')

			if(!empty($heure_retard_eleve[$total])) {
				$d_heure_absence_eleve_ins = $heure_retard_eleve[$total];
				$a_heure_absence_eleve_ins = '';
			} else {
				$d_heure_absence_eleve_ins = $d_heure_absence_eleve;
				$a_heure_absence_eleve_ins = $a_heure_absence_eleve;
			}

			if($erreur != 1) {
				if($miseajour==='debut' or $miseajour==='fin') {
					if($miseajour==='debut') {
						$requete="UPDATE ".$prefix_base."absences_eleves SET d_heure_absence_eleve = '$d_heure_absence_eleve_ins' WHERE id_absence_eleve = '".$id_abs."'";
					}
					if($miseajour==='fin') {
						$requete="UPDATE ".$prefix_base."absences_eleves SET a_heure_absence_eleve = '$a_heure_absence_eleve_ins' WHERE id_absence_eleve = '".$id_abs."'";
					}
					$resultat = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
				}
				if($miseajour!='debut' and $miseajour!='fin') {
					$requete="INSERT INTO ".$prefix_base."absences_eleves (type_absence_eleve,eleve_absence_eleve,justify_absence_eleve,motif_absence_eleve,d_date_absence_eleve,a_date_absence_eleve,d_heure_absence_eleve,a_heure_absence_eleve,saisie_absence_eleve) values ('$type_absence_eleve','$eleve_absent_ins','$justify_absence_eleve','$motif_absence_eleve','$d_date_absence_eleve_format_sql','$a_date_absence_eleve_format_sql','$d_heure_absence_eleve_ins','$a_heure_absence_eleve_ins','$saisie_absence_eleve')";
					$resultat = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
				}

				if ( $type_absence_eleve === 'A' ) {
					// connaitre l'id de l'enregistrement
					if ( $miseajour != 'debut' and $miseajour != 'fin' ) {
						$num_id = mysql_insert_id();
					}
					if ( $miseajour==='debut' or $miseajour === 'fin' ) {
						$num_id = $id_abs;
					}

					//envoie d'une lettre de justification
					$date_emis = date('Y-m-d');
					$heure_emis = date('H:i:s');
					$cpt_lettre_suivi = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE quirecois_lettre_suivi = '".$eleve_absent_ins."' AND emis_date_lettre_suivi = '".$date_emis."' AND partde_lettre_suivi = 'absences_eleves'"),0);
					if( $cpt_lettre_suivi == 0 ) {
						//si aucune lettre n'a encore été demandé alors on en créer une
						$requete = "INSERT INTO ".$prefix_base."lettres_suivis (quirecois_lettre_suivi, partde_lettre_suivi, partdenum_lettre_suivi, quiemet_lettre_suivi, emis_date_lettre_suivi, emis_heure_lettre_suivi, type_lettre_suivi, statu_lettre_suivi) VALUES ('".$eleve_absent_ins."', 'absences_eleves', ',".$num_id.",', '".$_SESSION['login']."', '".$date_emis."', '".$heure_emis."', '6', 'en attente')";
						mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
					} else {
						//si une lettre a déjas été demandé alors on la modifi
						// on cherche la lettre concerné et on prend les id déjas disponible puis on y ajout le nouvelle id
						$requete_info ="SELECT * FROM ".$prefix_base."lettres_suivis  WHERE emis_date_lettre_suivi = '".$date_emis."' AND partde_lettre_suivi = 'absences_eleves'";
						$execution_info = mysql_query($requete_info) or die('Erreur SQL !'.$requete_info.'<br />'.mysql_error());
						while ( $donne_info = mysql_fetch_array($execution_info)) {
							$id_lettre_suivi = $donne_info['id_lettre_suivi'];
							$id_deja_present = $donne_info['partdenum_lettre_suivi'];
						}
						$tableau_deja_existe = explode(',', $id_deja_present);
						if ( in_array($num_id, $tableau_deja_existe) ) {
							$id_ajout = $id_deja_present;
						} else {
							$id_ajout = $id_deja_present.$num_id.',';
						}
						$requete = "UPDATE ".$prefix_base."lettres_suivis SET partdenum_lettre_suivi = '".$id_ajout."', quiemet_lettre_suivi = '".$_SESSION['login']."', type_lettre_suivi = '6' WHERE id_lettre_suivi = '".$id_lettre_suivi."'";
						mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
					}
				}
			}
    	} // if($active_absence_eleve_ins == "1" or !empty($heure_retard_eleve[$total]))
    $total = $total + 1;
	} // while ($total < $nb_i)
}
// ==================== Fin de l'action ajouter ====================

// gestion des erreurs de saisi d'entre du formulaire de demande
$msg_erreur = '';
if ( $etape == '2' ) {
	if ( $a_heure_absence_eleve === '' ) { $msg_erreur = 'Attention il faut saisir un horaire de fin'; $$etape = ''; }
	if ( $d_heure_absence_eleve === '' ) { $msg_erreur = 'Attention il faut saisir un horaire de debut'; $$etape = ''; }
	if ( $d_date_absence_eleve === '' ) { $msg_erreur = 'Attention il faut saisir une date'; $$etape = ''; }
}

// si l'utilisateur demande l'enregistrement dans l'emploi du temps
if($edt_enregistrement==='1') {
	//connaitre le jour de la date sélectionné
	$jour_semaine = jour_semaine($d_date_absence_eleve);
	$matiere_du_groupe = matiere_du_groupe($classe);
	$type_de_semaine = semaine_type($d_date_absence_eleve);

	$test_existance = mysql_result(mysql_query('SELECT count(*) FROM edt_classes WHERE prof_edt_classe = "'.$_SESSION["login"].'" AND jour_edt_classe = "'.$jour_semaine['chiffre'].'" AND semaine_edt_classe = "'.$type_de_semaine.'" AND heuredebut_edt_classe <= "'.$d_heure_absence_eleve.'" AND heurefin_edt_classe >= "'.$a_heure_absence_eleve.'"'),0);
	$test_existance_groupe = mysql_result(mysql_query('SELECT count(*) FROM edt_classes WHERE groupe_edt_classe = "'.$classe.'" AND prof_edt_classe = "'.$_SESSION["login"].'" AND jour_edt_classe = "'.$jour_semaine['chiffre'].'" AND semaine_edt_classe = "'.$type_de_semaine.'" AND heuredebut_edt_classe <= "'.$d_heure_absence_eleve.'" AND heurefin_edt_classe >= "'.$a_heure_absence_eleve.'"'),0);
	if ($test_existance === '0') {
		$requete="INSERT INTO ".$prefix_base."edt_classes (groupe_edt_classe,prof_edt_classe,matiere_edt_classe,semaine_edt_classe,jour_edt_classe,datedebut_edt_classe,datefin_edt_classe,heuredebut_edt_classe,heurefin_edt_classe,salle_edt_classe) values ('".$classe."','".$_SESSION["login"]."','".$matiere_du_groupe['nomcourt']."','".semaine_type($d_date_absence_eleve)."','".$jour_semaine['chiffre']."','','','".$d_heure_absence_eleve."','".$a_heure_absence_eleve."','')";
		$resultat = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
	}
	if ( $test_existance === '1' and $test_existance_groupe === '0' ) {
		$requete = 'UPDATE '.$prefix_base.'edt_classes SET groupe_edt_classe = "'.$classe.'" WHERE prof_edt_classe = "'.$_SESSION["login"].'" AND jour_edt_classe = "'.$jour_semaine['chiffre'].'" AND semaine_edt_classe = "'.$type_de_semaine.'" AND heuredebut_edt_classe <= "'.$d_heure_absence_eleve.'" AND heurefin_edt_classe >= "'.$a_heure_absence_eleve.'"';
		$resultat = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
	}
}

	$datej = date('Y-m-d');
	$annee_en_cours_t=annee_en_cours_t($datej);
	$datejour = date('d/m/Y');
	$type_de_semaine = semaine_type($datejour);

$i = 0;


	$requete_modif = "SELECT * FROM absences_eleves WHERE id_absence_eleve ='$id_absence_eleve'";
	$resultat_modif = mysql_query($requete_modif) or die('Erreur SQL !'.$requete_modif.'<br />'.mysql_error());
	while ($data_modif = mysql_fetch_array($resultat_modif)) {
		$type_absence_eleve[$i] = $data_modif['type_absence_eleve'];
		$eleve_absent[$i] = $data_modif['eleve_absence_eleve'];
		$justify_absence_eleve[$i] = $data_modif['justify_absence_eleve'];
		$info_justify_absence_eleve[$i] = $data_modif['info_justify_absence_eleve'];
		$motif_absence_eleve[$i] = $data_modif['motif_absence_eleve'];
		$d_date_absence_eleve[$i] = date_fr($data_modif['d_date_absence_eleve']);
		$a_date_absence_eleve[$i] = date_fr($data_modif['a_date_absence_eleve']);
		$heuredebut_definie_periode[$i] = $data_modif['heuredebut_definie_periode'];
		$heurefin_definie_periode[$i] = $data_modif['heurefin_definie_periode'];
			$i = $i + 1;
	}

//Configuration du calendrier
include("../../lib/calendrier/calendrier.class.php");
$cal_1 = new Calendrier("absence", "d_date_absence_eleve");

// Style spécifique
$style_specifique = "mod_absences/styles/saisie_absences";
$javascript_specifique = "mod_absences/lib/js_profs_abs";

//**************** EN-TETE *****************
$titre_page = "Saisie des absences";
require_once("../../lib/header.inc");
//**************** FIN EN-TETE *****************
?>

<?php
echo "<p class=bold><a href=\"../../accueil.php\"><img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a> | ";
if($etape=="2" or $etape=="3") { echo "<a href='prof_ajout_abs.php?passage_form=manuel'>Retour étape 1/2</a> |";  }
echo "<a href=\"../lib/tableau.php?type=A&amp;pagedarriver=prof_ajout_abs\">Visualiser les absences</a>";
echo "</p>";
?>

<?php
// Première étape
    if($passage_form!='manuel') {
	// vérification de l'emploi du temps
	//horaire dans leqelle nous nous trouvons actuellement
	$horaire = periode_heure(periode_actuel(date('H:i:s')));
	// jour de la semaine au format chiffre
	$jour_aujourdhui = jour_semaine($datej);

	// on vérifie si un emploi du temps pour ce prof n'est pas disponible
//	$sql = 'SELECT * FROM edt_classes WHERE prof_edt_classe = "'.$_SESSION["login"].'" AND jour_edt_classe = "'.$jour_aujourdhui['chiffre'].'" AND datedebut_edt_classe <= "'.$datej.'" AND datefin_edt_classe >= "'.$datej.'" AND heuredebut_edt_classe <="'.date('H:i:s').'" AND heurefin_edt_classe >="'.date('H:i:s').'"';
	$sql = 'SELECT * FROM edt_classes WHERE prof_edt_classe = "'.$_SESSION["login"].'" AND jour_edt_classe = "'.$jour_aujourdhui['chiffre'].'" AND semaine_edt_classe = "'.$type_de_semaine.'" AND heuredebut_edt_classe <="'.date('H:i:s').'" AND heurefin_edt_classe >="'.date('H:i:s').'"';
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	// on fait une boucle qui va faire un tour pour chaque enregistrement
	while($data = mysql_fetch_array($req))
	{
		$d_heure_absence_eleve = $data['heuredebut_edt_classe'];
		$a_heure_absence_eleve = $data['heurefin_edt_classe'];
		$classe = $data['groupe_edt_classe'];
		$etape = '2';
		$passage_auto = 'oui';
	}
  }

if( ( $classe == 'toutes'  or ( $classe == '' and $eleve_initial == '' ) and $etape != '3' ) or $msg_erreur != '' ) { ?>
 <div style="text-align: center; margin: auto; width: 550px;">
	<h2>Saisie des absences : choix du cours</h2>
	<?php if ( $msg_erreur != '' ) { echo '<span style="color: #FF0000; font-weight: bold;">'.$msg_erreur.'</span>'; } ?>
       <form method="post" action="prof_ajout_abs.php" name="absence">
          Date
	  <?php if(empty($d_date_absence_eleve)) { $d_date_absence_eleve=date('d/m/Y'); } ?>
          <input size="10" name="d_date_absence_eleve" value="<?php echo $d_date_absence_eleve; ?>" /><a href="#calend" onClick="<?php echo $cal_1->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a><br />
          <br />
          De
          <select name="d_heure_absence_eleve">
          <?php
          $requete_pe = ('SELECT * FROM absences_creneaux ORDER BY heuredebut_definie_periode ASC');
          $resultat_pe = mysql_query($requete_pe) or die('Erreur SQL !'.$requete_pe.'<br />'.mysql_error());
          ?><option value="" <?php if(isset($dp_absence_eleve_erreur) and $dp_absence_eleve_erreur[$i] == "") { ?>selected<?php } else { } ?>>pas de s&eacute;lection</option><?php
          while($data_pe = mysql_fetch_array ($resultat_pe)) { ?>
              <option value="<?php echo $data_pe['heuredebut_definie_periode']; ?>" <?php if($data_pe['id_definie_periode']==periode_actuel($heure_choix)) { ?>selected="selected"<?php } ?>><?php echo $data_pe['nom_definie_periode']." ".heure_court($data_pe['heuredebut_definie_periode']); ?></option><?php
          } ?>
          </select>
          &nbsp;A&nbsp;
          <select name="a_heure_absence_eleve">
          <?php
          $requete_pe = ('SELECT * FROM absences_creneaux ORDER BY heuredebut_definie_periode ASC');
          $resultat_pe = mysql_query($requete_pe) or die('Erreur SQL !'.$requete_pe.'<br />'.mysql_error());
          ?><option value="" <?php if(isset($dp_absence_eleve_erreur[$i]) and $dp_absence_eleve_erreur[$i] == "") { ?>selected<?php } else { } ?>>pas de s&eacute;lection</option><?php
          while($data_pe = mysql_fetch_array ($resultat_pe)) { ?>
              <option value="<?php echo $data_pe['heurefin_definie_periode']; ?>" <?php if($data_pe['id_definie_periode']==periode_actuel($heure_choix)) { ?>selected="selected"<?php } ?>><?php echo $data_pe['nom_definie_periode']." ".heure_court($data_pe['heurefin_definie_periode']); ?></option><?php
          } ?>
          </select>
          <br />
          <br />
	 Groupe
         <select name="classe">
<?php
$groups = get_groups_for_prof($_SESSION["login"]);

foreach($groups as $group) {
           ?><option value="<?php echo $group["id"]; ?>" <?php if(!empty($classe) and $classe == $group["id"]) { ?>selected="selected"<?php } ?>>
            <?php
	    echo $group["description"]."&nbsp;-&nbsp;(";
            $str = null;
            foreach ($group["classes"]["classes"] as $classe) {
                $str .= $classe["classe"] . ", ";
            }
            $str = substr($str, 0, -2);
            echo $str . ")";
            ?>
	    </option>
	<?php } ?>
	</select>
<br />
          <?php
	  if ( $etape == '2' and $classe == '' and $eleve_initial == '' ) { ?><span class="erreur_rouge_jaune">Erreur de selection, n'oubliez pas de sélectionner une classe ou un élève</span><br /><?php } ?>
          <br />
          <?php
          if (getSettingValue("active_module_trombinoscopes")=='y')
              ?><input type="checkbox" name="photo" value="avec_photo" />Avec photo<br /><?php
          ?>
          <input type="checkbox" name="edt_enregistrement" value="1" />Mémoriser cette sélection<br />
          <input value="2" name="etape" type="hidden" />
          <input value="<?php echo $passage_form; ?>" name="passage_form" type="hidden" />
	  <input type="hidden" name="uid_post" value="<?php echo ereg_replace(' ','%20',$uid); ?>" />
          <br/>
          <input value="Afficher les élèves" name="Valider" type="submit" onClick="this.form.submit();this.disabled=true;this.value='En cours'" />
          <br /><br />
          Nous sommes le : <?php  echo date('d/m/Y') ?> et il est actuellement : <?php echo date('G:i')  ?>
   </form>
 </div>
<?php } ?>





<?php
// Deuxième étape
if ( $etape === '2' and $classe != 'toutes' and ( $classe != '' or $eleve_initial != '' ) and $msg_erreur === '') {
$current_groupe = get_group($classe);
?>

<div style="text-align: center; margin: auto; width: 550px;">
      <form method="post" action="prof_ajout_abs.php" name="liste_absence_eleve">
      <div style="text-align: center; font: normal 12pt verdana, sans-serif;">
      	Saisie des absences<br/>
      	du <strong><?php echo date_frl(date_sql($d_date_absence_eleve)); ?></strong> de <strong><?php echo heure_court($d_heure_absence_eleve); ?></strong> à <strong><?php echo heure_court($a_heure_absence_eleve); ?></strong><br/>
		<?php echo "<b>".$current_groupe["description"]."</b> (".$current_groupe["classlist_string"] .")";?>
      </div>
      <?php if($passage_auto==='oui' and $passage_form==='') { ?><div style="text-align: center; font: normal 10pt verdana, sans-serif;"><a href="prof_ajout_abs.php?passage_form=manuel">Ceci n'est pas la bonne liste d'appel ?</a></div><?php } ?>
      <br />
	   <div style="text-align: center; margin-bottom: 20px;">
	   	<input value="Enregistrer" name="Valider" type="submit"  onClick="this.form.submit();this.disabled=true;this.value='En cours'" />
	   </div>
      <table style="text-align: left; width: 500px;" border="0" cellpadding="0" cellspacing="1">
      <tbody>
        <tr class="titre_tableau_gestion" style="white-space: nowrap;">
          <td style="width: 300px; text-align: center;">Liste des &eacute;l&egrave;ves</td>
          <td style="width: 100px; text-align: center;">Absence</td>
          <td style="width: 100px; text-align: center;">Retard</td>
        </tr>
        <?php
        //if(empty($classe) and !empty($eleve_initial) and empty($eleve_absent)) {$requete_liste_eleve ="SELECT * FROM eleves WHERE eleves.nom  LIKE '".$eleve_initial."%' GROUP BY nom, prenom"; }
        //if(!empty($classe) and empty($eleve_initial) and empty($eleve_absent)) { $requete_liste_eleve ="SELECT * FROM eleves, groupes, j_eleves_groupes WHERE eleves.login=j_eleves_groupes.login AND j_eleves_groupes.id_groupe=groupes.id AND id = '".$classe."' GROUP BY nom, prenom"; }
        //if(empty($classe) and empty($eleve_initial) and !empty($eleve_absent)) { $requete_liste_eleve ="SELECT * FROM eleves, groupes, j_eleves_groupes WHERE eleves.login = '".$eleve_absent[0]."' AND eleves.login=j_eleves_groupes.login AND j_eleves_groupes.id_groupe=groupes.id AND id = '".$classe."' GROUP BY nom, prenom"; }
		$requete_liste_eleve ="SELECT * FROM eleves, groupes, j_eleves_groupes WHERE eleves.login=j_eleves_groupes.login AND j_eleves_groupes.id_groupe=groupes.id AND id = '".$classe."' GROUP BY nom, prenom";
        $execution_liste_eleve = mysql_query($requete_liste_eleve) or die('Erreur SQL !'.$requete_liste_eleve.'<br />'.mysql_error());
        $cpt_eleve = '0';
        $ic = '1';
        while ($data_liste_eleve = mysql_fetch_array($execution_liste_eleve))
        {
          if ($ic === '1') { $ic='2'; $couleur_cellule="td_tableau_absence_1"; $background_couleur="#E8F1F4"; } else { $couleur_cellule="td_tableau_absence_2"; $background_couleur="#C6DCE3"; $ic='1'; }
          ?>
          <tr bgcolor="<?php echo $background_couleur; ?>" onmouseover="this.bgColor='#F7F03A';" onmouseout="this.bgColor='<?php echo $background_couleur; ?>'">
            <td style="width: 400px; font-size: 14px; padding-left: 10px;"><input type="hidden" name="eleve_absent[<?php echo $cpt_eleve; ?>]" value="<?php echo $data_liste_eleve['login']; ?>" /><a href="javascript:centrerpopup('../lib/fiche_eleve.php?select_fiche_eleve=<?php echo $data_liste_eleve['login']; ?>',550,500,'scrollbars=yes,statusbar=no,resizable=yes');"><?php echo strtoupper($data_liste_eleve['nom'])." ".ucfirst($data_liste_eleve['prenom']); if($data_liste_eleve['sexe']=="M") { ?> (M.)<?php } elseif ($data_liste_eleve['sexe']=="F") { ?> (Mlle)<?php } $sexe=$data_liste_eleve['sexe']; ?></a></td>
            <td style="width: 100px; text-align: center;">
            <?php
            $pass='0';
            $requete = "SELECT * FROM absences_eleves
			         WHERE eleve_absence_eleve='".$data_liste_eleve['login']."'
			  	   AND type_absence_eleve = 'A'
				   AND
				   ( '".date_sql($d_date_absence_eleve)."' BETWEEN d_date_absence_eleve AND a_date_absence_eleve
				     OR d_date_absence_eleve BETWEEN '".date_sql($d_date_absence_eleve)."' AND '".date_sql($d_date_absence_eleve)."'
				     OR a_date_absence_eleve BETWEEN '".date_sql($d_date_absence_eleve)."' AND '".date_sql($d_date_absence_eleve)."'
				   )
				   AND
				   ( '".$d_heure_absence_eleve."' BETWEEN d_heure_absence_eleve AND a_heure_absence_eleve
				     AND '".$a_heure_absence_eleve."' BETWEEN d_heure_absence_eleve AND a_heure_absence_eleve
				     OR (d_heure_absence_eleve BETWEEN '".$d_heure_absence_eleve."' AND '".$a_heure_absence_eleve."'
				     AND a_heure_absence_eleve BETWEEN '".$d_heure_absence_eleve."' AND '".$a_heure_absence_eleve."')
				   )";
            $query = mysql_query($requete);
            $cpt_absences = mysql_num_rows($query);
            if($cpt_absences != '0') { $pass = '1'; }
            if ($pass === '0') { ?><input name="active_absence_eleve[<?php echo $cpt_eleve; ?>]" value="1" type="checkbox" /><?php } else { if($sexe=="M") { ?>Absent<?php } if($sexe=="F") { ?>Absente<?php } ?><input name="active_absence_eleve[<?php echo $cpt_eleve; ?>]" value="0" type="hidden" /><?php }
            $pass='0';
            ?>
            </td>
            <td style="width: 100px; text-align: center;">
            <?php
           $pass='0';
           $requete_retards = "SELECT count(*) FROM absences_eleves
						WHERE eleve_absence_eleve='".$data_liste_eleve['login']."'
						AND type_absence_eleve = 'R'
						AND
						( '".date_sql($d_date_absence_eleve)."' BETWEEN d_date_absence_eleve AND a_date_absence_eleve
					       	   OR d_date_absence_eleve BETWEEN '".date_sql($d_date_absence_eleve)."' AND '".date_sql($d_date_absence_eleve)."'
					       	   OR a_date_absence_eleve BETWEEN '".date_sql($d_date_absence_eleve)."' AND '".date_sql($d_date_absence_eleve)."'
						)
						AND
						( '".$d_heure_absence_eleve."' BETWEEN d_heure_absence_eleve AND a_heure_absence_eleve
					          OR '".$a_heure_absence_eleve."' BETWEEN d_heure_absence_eleve AND a_heure_absence_eleve
					          OR d_heure_absence_eleve BETWEEN '".$d_heure_absence_eleve."' AND '".$a_heure_absence_eleve."'
					          OR a_heure_absence_eleve BETWEEN '".$d_heure_absence_eleve."' AND '".$a_heure_absence_eleve."'
				      		)";
           $cpt_retards = mysql_result(mysql_query($requete_retards),0);
           if($cpt_retards != '0') { $pass = '1'; }
           if ($pass === '0') {
           ?><input type="checkbox" id="active_retard_eleve<?php echo $cpt_eleve; ?>" name="active_retard_eleve[<?php echo $cpt_eleve; ?>]" value="1" onClick="getHeure(active_retard_eleve<?php echo $cpt_eleve; ?>,heure_retard_eleve<?php echo $cpt_eleve; ?>,'liste_absence_eleve')" /> <input type="text" id="heure_retard_eleve<?php echo $cpt_eleve; ?>" name="heure_retard_eleve[<?php echo $cpt_eleve; ?>]" size="3" maxlength="8" value="<?php echo heure_court($heuredebut_definie_periode); ?>" />
           <?php } else { ?>En retard<input id="active_retard_eleve<?php echo $cpt_eleve; ?>" name="active_retard_eleve[<?php echo $cpt_eleve; ?>]" value="0" type="hidden" /><?php } ?>
           <?php
           // Avec ou sans photo
           if ((getSettingValue("active_module_trombinoscopes")=='y') and ($photo=="avec_photo")) {
               $photos = "../../photos/eleves/".$data_liste_eleve['elenoet'].".jpg";
               if (!(file_exists($photos))) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
	       $valeur=redimensionne_image_petit($photos);
               ?>
               <td>
               <img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" />
               </td>
           <?php } ?>

           </tr>
           <?php
           $type_saisie="A";
           $cpt_eleve = $cpt_eleve + 1; } ?>
           </tbody>
           </table>
           <input value="0" name="etape" type="hidden" />
           <input type="hidden" name="nb_i" value="<?php echo $cpt_eleve; ?>" />
           <input type="hidden" name="type_absence_eleve" value="<?php echo $type_saisie; ?>" />
           <input type="hidden" name="saisie_absence_eleve" value="<?php echo $_SESSION['login']; ?>" />
           <input type="hidden" name="classe" value="<?php echo $classe; ?>" />
           <input type="hidden" name="action_sql" value="ajouter" />
           <?php if (getSettingValue("active_module_trombinoscopes")=='y')
               ?><input type="hidden" name="photo" value="<?php echo $photo; ?>" /><?php
           ?>
           <input type="hidden" name="d_date_absence_eleve" value="<?php echo $d_date_absence_eleve; ?>" />
           <input type="hidden" name="d_heure_absence_eleve" value="<?php echo $d_heure_absence_eleve; ?>" />
           <input type="hidden" name="etape" value="2" />
           <input type="hidden" name="a_heure_absence_eleve" value="<?php echo $a_heure_absence_eleve; ?>" />
	   		<input type="hidden" name="uid_post" value="<?php echo ereg_replace(' ','%20',$uid); ?>" />
           <div style="text-align: center; margin: 20px;">
           	<input value="Enregistrer" name="Valider" type="submit"  onClick="this.form.submit();this.disabled=true;this.value='En cours'" />
           </div>
        </form>
</div>
<?php }
require("../../lib/footer.inc.php");
?>
