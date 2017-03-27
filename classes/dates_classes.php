<?php
/*
 * $Id$
 *
 * Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$action = isset($_POST["action"]) ? $_POST["action"] :(isset($_GET["action"]) ? $_GET["action"] :NULL);

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Dans le cas ou on poste un message, pas de traitement anti_inject
// Pour ne pas interférer avec fckeditor
if ((isset($action)) and ($action == 'evenement') and 
(isset($_POST['texte_avant']) || isset($_POST['texte_apres']) || isset($_POST['texte_apres_ele_resp'])) and 
isset($_POST['ok'])) {$traite_anti_inject = 'no';}


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
}

$sql="SELECT 1=1 FROM droits WHERE id='/classes/dates_classes.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/classes/dates_classes.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Définition de dates pour les classes',
statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// Configuration du calendrier
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

// initialisation des notifications
$msg_erreur="";
$msg_OK="";

// initialisation des variables
$order_by = isset($_POST["order_by"]) ? $_POST["order_by"] :(isset($_GET["order_by"]) ? $_GET["order_by"] :"date_debut");
if ($order_by != "date_debut" and $order_by != "date_fin" and $order_by != "id") {
	$order_by = "date_debut";
}

$id_ev = isset($_POST["id_ev"]) ? $_POST["id_ev"] :(isset($_GET["id_ev"]) ? $_GET["id_ev"] :NULL);
$destinataire_prof=isset($_POST['destinataire_prof']) ? $_POST['destinataire_prof'] : "n";
$destinataire_cpe=isset($_POST['destinataire_cpe']) ? $_POST['destinataire_cpe'] : "n";
$destinataire_scol=isset($_POST['destinataire_scol']) ? $_POST['destinataire_scol'] : "n";
$destinataire_resp=isset($_POST['destinataire_resp']) ? $_POST['destinataire_resp'] : "n";
$destinataire_ele=isset($_POST['destinataire_ele']) ? $_POST['destinataire_ele'] : "n";
$type=isset($_POST['type']) ? $_POST['type'] : "autre";
$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : "";
$texte_avant=isset($_POST['texte_avant']) ? $_POST['texte_avant'] : "";
$texte_apres=isset($_POST['texte_apres']) ? $_POST['texte_apres'] : "";
$texte_apres_ele_resp=isset($_POST['texte_apres_ele_resp']) ? $_POST['texte_apres_ele_resp'] : "";

//debug_var();

if (isset($id_ev)) {
	// Si on n'a pas fait le ménage dans les événements lors de l'initialisation
	$sql="DELETE FROM d_dates_evenements_classes WHERE id_ev='$id_ev' AND id_classe NOT IN (SELECT id FROM classes);";
	//echo "$sql<br />";
	$menage=mysqli_query($GLOBALS['mysqli'], $sql);
}

//
// Purge des événements
//
if (isset($_POST['purger'])) {
	check_token();

	$sql="SELECT * FROM d_dates_evenements_classes WHERE date_evenement<='".strftime("%Y-%m-%d %H:%M:%S", time()-86400)."';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$nb_ev=0;
		$nb_suppr=0;
		while($lig=mysqli_fetch_object($res)) {
			$sql="DELETE FROM d_dates_evenements_classes WHERE id_ev_classe='".$lig->id_ev_classe."';";
			//echo "$sql<br />";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$del) {
				$msg_erreur.="Erreur lors de la suppression de la date de la classe de ".get_nom_classe($lig->id_classe)." associées à l'événement n°".$lig->id_ev.".";
				$nb_err++;
			}
			else {
				// On vérifie s'il y a encore des classes associées.
				$sql="SELECT * FROM d_dates_evenements_classes WHERE id_ev='".$lig->id_ev."';";
				//echo "$sql<br />";
				$res2=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res2)==0) {
					$sql="DELETE FROM d_dates_evenements WHERE id_ev='".$lig->id_ev."';";
					//echo "$sql<br />";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if (!$del) {
						$msg_erreur.="Erreur lors de la suppression de l'événement n°".$lig->id_ev.".<br />";
						$nb_err++;
					}
					else {
						$nb_suppr++;
					}
				}
			}
		}
	}
	else {
		$msg_ok="Aucune date d'événement n'est antérieure d'un jour à la date du jour.";
	}
}

//
// Insertion ou modification d'un événement
//
if ((isset($action)) and ($action == 'evenement') and isset($_POST['ok']) and !isset($_POST['cancel'])) {
	check_token();
	$record = 'yes';
	//$contenu_cor = traitement_magic_quotes(corriger_caracteres($texte_avant));
	$contenu_cor=html_entity_decode($texte_avant);
	$contenu_cor2=html_entity_decode($texte_apres);
	$contenu_cor3=html_entity_decode($texte_apres_ele_resp);

	if ($destinataire_prof=="" && $destinataire_cpe=="" && $destinataire_scol=="" && $destinataire_resp=="" && $destinataire_ele=="") {
		$msg_erreur = "ATTENTION : aucun destinataire saisi.<br />(événement non enregitré)<br />";
		$record = 'no';
	}

	if ($contenu_cor == '') {
		$msg_erreur = "ATTENTION : aucun texte saisi.<br />(événement non enregitré)<br />";
		$record = 'no';
	}

	// par sécurité les rédacteurs d'un message ne peuvent y insérer la variable _CSRF_ALEA_
	$pos_crsf_alea=strpos($contenu_cor,"_CSRF_ALEA_");
	if($pos_crsf_alea!==false) {
		$contenu_cor=preg_replace("/_CSRF_ALEA_/","",$contenu_cor);
		$msg_erreur = "Contenu interdit.";
		$record = 'no';
	}

	if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $display_date_debut)) {
		$anneed = mb_substr($display_date_debut,6,4);
		$moisd = mb_substr($display_date_debut,3,2);
		$jourd = mb_substr($display_date_debut,0,2);
		while ((!checkdate($moisd, $jourd, $anneed)) and ($jourd > 0)) $jourd--;
		$date_debut=$anneed."-".$moisd."-".$jourd." 00:00:00";
	} else {
		$msg_erreur = "ATTENTION : La date de début d'affichage n'est pas valide.<br />(événement non enregitré)<br />";
		$record = 'no';
	}


	$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : array();
	$display_date_id_classe=isset($_POST['display_date_id_classe']) ? $_POST['display_date_id_classe'] : array();
	$display_heure_id_classe=isset($_POST['display_heure_id_classe']) ? $_POST['display_heure_id_classe'] : array();
	$salle_id_classe=isset($_POST['salle_id_classe']) ? $_POST['salle_id_classe'] : array();
	$periode=isset($_POST['periode']) ? $_POST['periode'] : 0;

	if(count($id_classe)==0) {
		$msg_erreur = "ATTENTION : Aucune classe n'est choisie.<br />";
		$record = 'no';
	}

	if ($record == 'yes') {

		$sql="SELECT 1=1 FROM d_dates_evenements WHERE id_ev='$id_ev';";
		//echo "$sql<br />";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			$sql="INSERT d_dates_evenements SET type='$type', 
									periode='$periode', 
									texte_avant='$contenu_cor', 
									texte_apres='$contenu_cor2', 
									texte_apres_ele_resp='$contenu_cor3', 
									date_debut='".get_mysql_date_from_slash_date($display_date_debut)."';";
			//echo "$sql<br />";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if($insert) {
				$id_ev=mysqli_insert_id($GLOBALS["mysqli"]);

				$tab_u=array();
				$sql="SELECT * FROM d_dates_evenements_utilisateurs WHERE id_ev='$id_ev';";
				$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_u)>0) {
					while($lig_u=mysqli_fetch_object($res_u)) {
						$tab_u[]=$lig_u->statut;
					}
				}

				if(($destinataire_prof=="y")&&(!in_array("professeur", $tab_u))) {
					$sql="INSERT INTO d_dates_evenements_utilisateurs SET id_ev='$id_ev', statut='professeur';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg_erreur="Erreur lors de l'enregistrement pour les professeurs de la classe.<br />";
						$record="no";
					}
				}

				if(($destinataire_cpe=="y")&&(!in_array("cpe", $tab_u))) {
					$sql="INSERT INTO d_dates_evenements_utilisateurs SET id_ev='$id_ev', statut='cpe';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg_erreur="Erreur lors de l'enregistrement pour les CPE de la classe.<br />";
						$record="no";
					}
				}

				if(($destinataire_scol=="y")&&(!in_array("scolarite", $tab_u))) {
					$sql="INSERT INTO d_dates_evenements_utilisateurs SET id_ev='$id_ev', statut='scolarite';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg_erreur="Erreur lors de l'enregistrement pour les comptes scolarité associés à la classe.<br />";
						$record="no";
					}
				}

				if(($destinataire_ele=="y")&&(!in_array("eleve", $tab_u))) {
					$sql="INSERT INTO d_dates_evenements_utilisateurs SET id_ev='$id_ev', statut='eleve';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg_erreur="Erreur lors de l'enregistrement pour les élèves de la classe.<br />";
						$record="no";
					}
				}

				if(($destinataire_resp=="y")&&(!in_array("responsable", $tab_u))) {
					$sql="INSERT INTO d_dates_evenements_utilisateurs SET id_ev='$id_ev', statut='responsable';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg_erreur="Erreur lors de l'enregistrement pour les responsables d'élèves de la classe.<br />";
						$record="no";
					}
				}


				if(($destinataire_prof=="n")&&(in_array("professeur", $tab_u))) {
					$sql="DELETE FROM d_dates_evenements_utilisateurs WHERE id_ev='$id_ev' AND statut='professeur';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
				}

				if(($destinataire_cpe=="n")&&(in_array("cpe", $tab_u))) {
					$sql="DELETE FROM d_dates_evenements_utilisateurs WHERE id_ev='$id_ev' AND statut='cpe';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
				}

				if(($destinataire_scol=="n")&&(in_array("scolarite", $tab_u))) {
					$sql="DELETE FROM d_dates_evenements_utilisateurs WHERE id_ev='$id_ev' AND statut='scolarite';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
				}

				if(($destinataire_ele=="n")&&(in_array("eleve", $tab_u))) {
					$sql="DELETE FROM d_dates_evenements_utilisateurs WHERE id_ev='$id_ev' AND statut='eleve';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
				}

				if(($destinataire_resp=="n")&&(in_array("responsable", $tab_u))) {
					$sql="DELETE FROM d_dates_evenements_utilisateurs WHERE id_ev='$id_ev' AND statut='responsable';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
				}

			}
			else {
				$msg_erreur="Erreur lors de l'enregistrement de l'événement.<br />";
				$record="no";
			}
		}
		else {
			$sql="UPDATE d_dates_evenements SET type='$type', 
									periode='$periode', 
									texte_avant='$contenu_cor', 
									texte_apres='$contenu_cor2', 
									texte_apres_ele_resp='$contenu_cor3', 
									date_debut='".get_mysql_date_from_slash_date($display_date_debut)."'
								WHERE id_ev='$id_ev';";
			//echo "$sql<br />";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$update) {
				$msg_erreur="Erreur lors de la mise à jour de l'événement.<br />";
				$record="no";
			}
			else {
				$tab_u=array();
				$sql="SELECT * FROM d_dates_evenements_utilisateurs WHERE id_ev='$id_ev';";
				$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_u)>0) {
					while($lig_u=mysqli_fetch_object($res_u)) {
						$tab_u[]=$lig_u->statut;
					}
				}

				if(($destinataire_prof=="y")&&(!in_array("professeur", $tab_u))) {
					$sql="INSERT INTO d_dates_evenements_utilisateurs SET id_ev='$id_ev', statut='professeur';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg_erreur="Erreur lors de l'enregistrement pour les professeurs de la classe.<br />";
						$record="no";
					}
				}

				if(($destinataire_cpe=="y")&&(!in_array("cpe", $tab_u))) {
					$sql="INSERT INTO d_dates_evenements_utilisateurs SET id_ev='$id_ev', statut='cpe';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg_erreur="Erreur lors de l'enregistrement pour les CPE de la classe.<br />";
						$record="no";
					}
				}

				if(($destinataire_scol=="y")&&(!in_array("scolarite", $tab_u))) {
					$sql="INSERT INTO d_dates_evenements_utilisateurs SET id_ev='$id_ev', statut='scolarite';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg_erreur="Erreur lors de l'enregistrement pour les comptes scolarité associés à la classe.<br />";
						$record="no";
					}
				}

				if(($destinataire_ele=="y")&&(!in_array("eleve", $tab_u))) {
					$sql="INSERT INTO d_dates_evenements_utilisateurs SET id_ev='$id_ev', statut='eleve';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg_erreur="Erreur lors de l'enregistrement pour les élèves de la classe.<br />";
						$record="no";
					}
				}

				if(($destinataire_resp=="y")&&(!in_array("responsable", $tab_u))) {
					$sql="INSERT INTO d_dates_evenements_utilisateurs SET id_ev='$id_ev', statut='responsable';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg_erreur="Erreur lors de l'enregistrement pour les responsables d'élèves de la classe.<br />";
						$record="no";
					}
				}


				if(($destinataire_prof=="n")&&(in_array("professeur", $tab_u))) {
					$sql="DELETE FROM d_dates_evenements_utilisateurs WHERE id_ev='$id_ev' AND statut='professeur';";
					//echo "$sql<br />";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
				}

				if(($destinataire_cpe=="n")&&(in_array("cpe", $tab_u))) {
					$sql="DELETE FROM d_dates_evenements_utilisateurs WHERE id_ev='$id_ev' AND statut='cpe';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
				}

				if(($destinataire_scol=="n")&&(in_array("scolarite", $tab_u))) {
					$sql="DELETE FROM d_dates_evenements_utilisateurs WHERE id_ev='$id_ev' AND statut='scolarite';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
				}

				if(($destinataire_ele=="n")&&(in_array("eleve", $tab_u))) {
					$sql="DELETE FROM d_dates_evenements_utilisateurs WHERE id_ev='$id_ev' AND statut='eleve';";
					//echo "$sql<br />";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
				}

				if(($destinataire_resp=="n")&&(in_array("responsable", $tab_u))) {
					$sql="DELETE FROM d_dates_evenements_utilisateurs WHERE id_ev='$id_ev' AND statut='responsable';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
				}
			}
		}

		if ($record == 'yes') {
			$tab_infos_prec=array();
			$sql="SELECT * FROM d_dates_evenements_classes WHERE id_ev='$id_ev';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				while($lig=mysqli_fetch_object($res)) {
					$tab_infos_prec[$lig->id_classe]['id_ev_classe']=$lig->id_ev_classe;
					$tab_infos_prec[$lig->id_classe]['date_evenement']=$lig->date_evenement;
				}
			}

			$sql="DELETE FROM d_dates_evenements_classes WHERE id_ev='$id_ev';";
			//echo "$sql<br />";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$del) {
				$msg_erreur = "Erreur lors du nettoyage des classes/dates.<br />";
			}
			else {
				$nb_classes_reg=0;
				$nb_classes_err=0;
				//for($loop=0;$loop<count($id_classe);$loop++) {
				//for($loop=0;$loop<count($id_classe);$loop++) {
				foreach($id_classe as $loop => $id_classe_courant) {
					unset($date_evenement);
					if((isset($display_date_id_classe[$loop]))&&(preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $display_date_id_classe[$loop]))) {
						$anneed = mb_substr($display_date_id_classe[$loop],6,4);
						$moisd = mb_substr($display_date_id_classe[$loop],3,2);
						$jourd = mb_substr($display_date_id_classe[$loop],0,2);
						while ((!checkdate($moisd, $jourd, $anneed)) and ($jourd > 0)) {
							$jourd--;
						}
						$date_evenement=$anneed."-".$moisd."-".$jourd;

						if((isset($display_heure_id_classe[$loop]))&&(preg_match("/([0-9]{1,2}):([0-9]{0,2})/", str_ireplace('h',':',$display_heure_id_classe[$loop])))) {
							$heured = mb_substr($display_heure_id_classe[$loop],0,2);
							$minuted = mb_substr($display_heure_id_classe[$loop],3,2);
							$date_evenement=$date_evenement." ".$heured.":".$minuted.":00";
						} elseif(isset($tab_infos_prec[$id_classe[$loop]]['date_evenement'])) {
							$msg_erreur = "ATTENTION : L'heure de l'événement pour la classe de ".get_nom_classe($id_classe[$loop])." n'est pas valide.<br />La date antérieure a été utilisée.<br />";
							$date_evenement=$tab_infos_prec[$id_classe[$loop]]['date_evenement'];
							$nb_classes_err++;
						} else {
							$msg_erreur = "ATTENTION : L'heure de l'événement pour la classe de ".get_nom_classe($id_classe[$loop])." n'est pas valide.<br />";
							$nb_classes_err++;
						}
					} elseif(isset($tab_infos_prec[$id_classe[$loop]]['date_evenement'])) {
						$msg_erreur = "ATTENTION : La date de l'événement pour la classe de ".get_nom_classe($id_classe[$loop])." n'est pas valide.<br />";
						$date_evenement=$tab_infos_prec[$id_classe[$loop]]['date_evenement'];
						$nb_classes_err++;
					} else {
						$msg_erreur = "ATTENTION : La date de l'événement pour la classe de ".get_nom_classe($id_classe[$loop])." n'est pas valide.<br />La date antérieure a été utilisée.<br />";
						$nb_classes_err++;
					}

					if(isset($date_evenement)) {
						$id_salle_courante="";
						if(isset($salle_id_classe[$loop])) {
							$id_salle_courante=$salle_id_classe[$loop];
						}
						$sql="INSERT INTO d_dates_evenements_classes SET id_ev='$id_ev', id_classe='".$id_classe[$loop]."', date_evenement='$date_evenement', id_salle='".$id_salle_courante."';";
						//echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if($insert) {
							$nb_classes_reg++;
							if(($type=="conseil_de_classe")&&(preg_match("/^[0-9]{1,}$/", $periode))&&($periode>=1)) {
								$sql="UPDATE periodes SET date_conseil_classe='".$date_evenement."' WHERE id_classe='".$id_classe[$loop]."' AND num_periode='".$periode."' ";
								//echo "$sql<br />";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$update) {
									$msg_erreur.="Erreur lors de la mise à jour de la date du conseil de classe pour la classe de ".get_nom_classe($id_classe[$loop])." en période ".$periode."<br />";
								}
							}
						}
						else {
							$msg_erreur = "Erreur lors de l'enregistrement de la date pour la classe ".get_nom_classe($id_classe[$loop]).".<br />";
							$nb_classes_err++;
						}
					}
				}

				if($nb_classes_err>0) {
					$msg_erreur .= "$nb_classes_err erreur(s) lors de l'enregistrement de dates pour une ou des classes.<br />";
				}
				else {
					if($nb_classes_reg>0) {
						$msg_OK="Enregistrement effectué pour $nb_classes_reg classe(s) <em>(".strftime("%d/%m/%Y à %H:%M:%S").")</em>.<br />";
					}
					else {
						$msg_OK="Enregistrement effectué... mais sans classe&nbsp;? <em>(".strftime("%d/%m/%Y à %H:%M:%S").")</em>.<br />";
					}
				}
			}
		}

	}
}

//
// Suppression d'un événement
//
if ((isset($action)) and ($action == 'sup_entry')) {
	check_token();
	$sql="DELETE FROM d_dates_evenements_classes WHERE id_ev='".$_GET['id_del']."';";
	//echo "$sql<br />";
	$del=mysqli_query($GLOBALS["mysqli"], $sql);
	if($del) {
		$sql="DELETE FROM d_dates_evenements_utilisateurs WHERE id_ev='".$_GET['id_del']."';";
		//echo "$sql<br />";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
		if($del) {
			$sql="DELETE FROM d_dates_evenements WHERE id_ev='".$_GET['id_del']."';";
			//echo "$sql<br />";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
			if ($del) {
				$msg_OK = "Suppression réussie";
			}
			else {
				$msg_erreur="Erreur lors de la suppression de l'événement.";
			}
		}
		else {
			$msg_erreur="Erreur lors de la suppression des statuts associés à l'événement.";
		}
	}
	else {
		$msg_erreur="Erreur lors de la suppression des dates des classes associées à l'événement.";
	}
}

/*
//
// Annulation des modifs
//
if ((isset($action)) and ($action == 'message') and (isset($_POST['cancel']))) {
	unset ($id_ev);
}
*/

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_suppression = "Confirmation de suppression";
//**************** EN-TETE *****************
$titre_page = "Gestion des dates";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

?>
<script src="../ckeditor/ckeditor.js"></script>
<?php

echo "<a name=\"debut_de_page\"></a>";

//debug_var();
echo "<div style='color: #FF0000; text-align: center; padding: 0.5%;'>";
if ($msg_erreur!="") echo "<p style='color: #FF0000; font-variant: small-caps;'>".$msg_erreur."</p>";
if ($msg_OK!="") echo "<p style='color: #0000FF; font-variant: small-caps;'>".$msg_OK."</p>";
echo "</div>";


echo "<script type=\"text/javascript\" language=\"JavaScript\" SRC=\"../lib/clock_fr.js\"></SCRIPT>\n";
//-----------------------------------------------------------------------------------
echo "<p class='bold'><a href='../accueil.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Nouvel événement</a><span id='span_lien_js_nouvel_evenement' style='display:none'> | <a href='dates_classes2.php' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Créer l'événement avec la nouvelle interface de saisie (nécessitant JavaScript).\">Nouvel événement (2)</a></span></p>\n";
echo "<table width=\"98%\" cellspacing=0 align=\"center\">\n";
echo "<tr>\n";
echo "<td valign='top'>\n";
echo "<p>Nous sommes le&nbsp;:&nbsp;\n";
echo "<script type=\"text/javascript\" language=\"javascript\">\n";
echo "<!--\n";
echo "new LiveClock();\n";
echo "//-->\n";
echo "</script></p>\n";
echo "</td>\n";

echo "</tr></table><hr />\n";

//==========================================================
// Liste des classes de l'établissement
$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE c.id=p.id_classe ORDER BY classe;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p style='color:red'>Aucune classe n'est encore définie.</p>";
	require("../lib/footer.inc.php");
	die();
}
else {
	$tab_classe=array();
	while($obj_classe=mysqli_fetch_object($res)) {
		$tab_classe[$obj_classe->id]['classe']=$obj_classe->classe;
		$tab_classe[$obj_classe->id]['nom_complet']=$obj_classe->nom_complet;
	}
}
//==========================================================

echo "<table  border = \"0\" cellpadding=\"10\">\n";
echo "<tr>";
echo "<td width = \"350px\" valign=\"top\">\n";

echo "<span class='grand'>Purge des événements</span><br />\n";
echo "<p>La purge des événements consiste à supprimer tous les événements dont la date est antérieure de plus de 24 h. à la date actuelle.</p>";
echo "<form align=\"center\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" style=\"width: 100%;\">\n";
echo "<p align=\"center\"><input type=\"submit\" name=\"purger\" value=\" Purger les événements \"></p>";
echo add_token_field();
echo "</form>";
echo "<br /><br />";

//
// Affichage des événements éditables
//
$sql="SELECT * FROM d_dates_evenements order by ".$order_by." DESC";
//echo "$sql<br />";
$res = mysqli_query($GLOBALS["mysqli"], $sql);
$nb_messages = mysqli_num_rows($res);

if ($nb_messages>0) {
	echo "<p><span class='grand'>Événements pouvant être modifiés&nbsp;:</span></p>\n";
	$ind = 0;
	while ($lig=mysqli_fetch_object($res)) {
		echo "<div style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");padding: 3px;margin: 3px 3px 1em 3px; width: 350px; overflow: auto;'>";
		echo "<p><strong><i>Affichage</i></strong> à compter du <strong>".get_date_slash_from_mysql_date($lig->date_debut)."</strong> d'un événement de type <strong>".$lig->type."</strong>";

		$tab_u=array();
		$sql="SELECT * FROM d_dates_evenements_utilisateurs WHERE id_ev='$lig->id_ev';";
		$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_u)>0) {
			while($lig_u=mysqli_fetch_object($res_u)) {
				$tab_u[]=$lig_u->statut;
			}
		}

		echo "<br /><b><i>Statut(s) destinataire(s) </i></b> : <br />";
		if(in_array("professeur", $tab_u)) {
			echo " professeurs de la classe<br />";
		}
		if(in_array("cpe", $tab_u)) {
			echo " CPE de la classe<br />";
		}
		if(in_array("scolarite", $tab_u)) {
			echo " comptes scolarité associés à la classe<br />";
		}
		if(in_array("responsable", $tab_u)) {
			echo " responsables d'élèves de la classe<br />";
		}
		if(in_array("eleve", $tab_u)) {
			echo " élèves de la classe<br />";
		}
		echo "</p>\n";

		echo "<p>";
		echo "<b><i>Classe(s) </i></b> : <br />";
		$sql="SELECT * FROM d_dates_evenements_classes d, classes c WHERE d.id_ev='$lig->id_ev' AND d.id_classe=c.id ORDER BY date_evenement, classe;";
		$res2=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res2)==0) {
			echo "Aucune classe n'est associée.";
		}
		else {
			while ($lig2=mysqli_fetch_object($res2)) {
				echo $lig2->classe."&nbsp;: ".get_date_heure_from_mysql_date($lig2->date_evenement)."<br />";
			}
		}
		echo "</p>\n";

		//echo "<br /><b><i>Login du destinataire </i></b> : ".$login_destinataire1;
		echo "<br /><a href='".$_SERVER['PHP_SELF']."?id_ev=$lig->id_ev' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/edit16.png' class='icone16' alt='Modifier' /> modifier</a>
		<span id='span_lien_js_evenement_$ind' style='display:none;'>-<a href='dates_classes2.php?id_ev=$lig->id_ev' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Modifier l'événement avec la nouvelle interface de saisie\n(nécessitant Javascript)\"><img src='../images/edit16.png' class='icone16' alt='Modifier' /> 2 </a></span>
		- <a href='".$_SERVER['PHP_SELF']."?id_del=$lig->id_ev&amp;action=sup_entry".add_token_in_url()."' onclick=\"return confirmlink(this, 'Etes-vous sûr de vouloir supprimer cet événement ?', '".$message_suppression."')\"><img src='../images/delete16.png' class='icone16' alt='Supprimer' /> supprimer</a>
		<div style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");padding: 3px; margin: 3px;'>".affiche_evenement($lig->id_ev, "y")."</div>
		</div>\n";
		$ind++;
	}
}

// Fin de la colonne de gauche
echo "</td>\n";

//====================================================================

/*
// Aide
$titre_infobulle="AIDE\n";
$texte_infobulle="Un message peut être adressé à :<br />- tous les utilisateurs ayant le(s) même(s) statut(s) ;<br />- ou un utilisateur particulier ;<br />- ou tous les professeurs enseignant dans une même classe.<br /><br />Attention : seuls les messages adressés uniquement à des utilisateurs de même(s) statut(s) peuvent être modifiés après enregistrement.<br /><br />\n";
//$texte_infobulle.="\n";
$tabdiv_infobulle[]=creer_div_infobulle('aide',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');
*/

// Début de la colonne de droite
echo "<td valign=\"top\">\n";

//
// Affichage de l'événement en modification
//
// Initialisation: Valeurs par défaut
$titre_mess = "Nouvel événement";
$date_debut=strftime("%Y-%m-%d %H:%M:%S");
$heure_courante=strftime("%H:%M");
$periode="0";
$texte_avant="";
$texte_apres="";
$texte_apres_ele_resp="";
$tab_classe_ev=array();
if (isset($id_ev)) {
	$sql="SELECT * FROM d_dates_evenements WHERE id_ev='$id_ev';";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>L'événément n°$id_ev n'existe pas.</p>\n";
	}
	else {
		// Modification des valeurs
		$titre_mess = "Modification d'un événement";
		$obj_ev=mysqli_fetch_object($res);

		$type=$obj_ev->type;
		$periode=$obj_ev->periode;
		$date_debut=$obj_ev->date_debut;
		$texte_avant=$obj_ev->texte_avant;
		$texte_apres=$obj_ev->texte_apres;
		$texte_apres_ele_resp=$obj_ev->texte_apres_ele_resp;

		$tab_u=array();
		$sql="SELECT * FROM d_dates_evenements_utilisateurs WHERE id_ev='$obj_ev->id_ev';";
		$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_u)>0) {
			while($lig_u=mysqli_fetch_object($res_u)) {
				$tab_u[]=$lig_u->statut;
			}
		}

		if(in_array("professeur", $tab_u)) {
			$destinataire_prof="y";
		}
		else {
			$destinataire_prof="n";
		}
		if(in_array("cpe", $tab_u)) {
			$destinataire_cpe="y";
		}
		else {
			$destinataire_cpe="n";
		}
		if(in_array("scolarite", $tab_u)) {
			$destinataire_scol="y";
		}
		else {
			$destinataire_scol="n";
		}

		if(in_array("responsable", $tab_u)) {
			$destinataire_resp="y";
		}
		else {
			$destinataire_resp="n";
		}

		if(in_array("eleve", $tab_u)) {
			$destinataire_ele="y";
		}
		else {
			$destinataire_ele="n";
		}

		$sql="SELECT * FROM d_dates_evenements_classes d, classes c WHERE d.id_ev='$id_ev' AND d.id_classe=c.id ORDER BY date_evenement, classe;";
		//echo "$sql<br />";
		$res2=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res2)>0) {
			while($obj_ev_classe=mysqli_fetch_object($res2)) {
				$tab_classe_ev[$obj_ev_classe->id_classe]["classe"]=$obj_ev_classe->classe;
				$tab_classe_ev[$obj_ev_classe->id_classe]["date_evenement"]=$obj_ev_classe->date_evenement;
				$tab_classe_ev[$obj_ev_classe->id_classe]["date_evenement_formatee"]=formate_date($obj_ev_classe->date_evenement);
				$tab_classe_ev[$obj_ev_classe->id_classe]["heure_evenement"]=get_heure_2pt_minute_from_mysql_date($obj_ev_classe->date_evenement);
				$tab_classe_ev[$obj_ev_classe->id_classe]["id_salle"]=$obj_ev_classe->id_salle;
			}
		}
	}
}
elseif((isset($record))&&($record=="no")) {
	$texte_avant=isset($_POST['texte_avant']) ? $_POST['texte_avant'] : "";
	$texte_apres=isset($_POST['texte_apres']) ? $_POST['texte_apres'] : "";
	$texte_apres_ele_resp=isset($_POST['texte_apres_ele_resp']) ? $_POST['texte_apres_ele_resp'] : "";
	$periode=isset($_POST['periode']) ? $_POST['periode'] : "0";

	//$texte_avant=html_entity_decode($texte_avant);
	//$texte_apres=html_entity_decode($texte_apres);
}
$display_date_debut=formate_date($date_debut);

$max_per=0;
$chaine_options_periodes="";
$sql="SELECT num_periode FROM periodes ORDER BY num_periode DESC LIMIT 1;";
$res_max_per=mysqli_query($GLOBALS['mysqli'], $sql);
if(mysqli_num_rows($res_max_per)>0) {
	$lig_max_per=mysqli_fetch_object($res_max_per);
	$max_per=$lig_max_per->num_periode;
	for($loop=1;$loop<=$max_per;$loop++) {
		$checked_periode="";
		if($periode==$loop) {
			$checked_periode=" selected='selected'";
		}
		$chaine_options_periodes.="
									<option value='$loop'".$checked_periode.">$loop</option>";
	}
}

$lignes_js_dates_conseils_classes="";
$sql="SELECT * FROM periodes;";
$res_per=mysqli_query($GLOBALS['mysqli'], $sql);
if(mysqli_num_rows($res_per)>0) {
	$tab_cdc=array();
	while($lig_per=mysqli_fetch_object($res_per)) {
		//$tab_cdc[$lig_per->num_periode]="var date_conseil".$lig_per->num_periode."[".$lig->id_classe."]='".formate_date($lig_per->date_conseil_classe)."'\n";
		if(!isset($tab_cdc[$lig_per->num_periode])) {$tab_cdc[$lig_per->num_periode]="";}
		$tab_cdc[$lig_per->num_periode].="
			if(document.getElementById('id_classe_".$lig_per->id_classe."')) {
				document.getElementById('id_classe_".$lig_per->id_classe."').checked=true;
				modif_affichage_ligne_classe(".$lig_per->id_classe.");
				if(document.getElementById('display_date_id_classe_".$lig_per->id_classe."')) {
					document.getElementById('display_date_id_classe_".$lig_per->id_classe."').value='".formate_date($lig_per->date_conseil_classe)."';
				}
			}\n";
	}

	foreach($tab_cdc as $key => $value) {
		$lignes_js_dates_conseils_classes.="
		if(periode==$key) {".$value."
		}";
	}
}

echo "<table style=\"border:1px solid black\" cellpadding=\"5\" cellspacing=\"0\">
	<tr>
		<td>
			<form action=\"".$_SERVER['PHP_SELF']."#debut_de_page\" method=\"post\" style=\"width: 100%;\" name=\"formulaire_saisie_evenement\">
				<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
					".add_token_field();

if (isset($id_ev)) {
	echo "
					<input type=\"hidden\" name=\"id_ev\" value=\"$id_ev\" />\n";
}

echo "
					<input type=\"hidden\" name=\"action\" value=\"evenement\" />

					<table border=\"0\" width = \"100%\" cellspacing=\"1\" cellpadding=\"1\" >
						<tr>
							<td colspan=\"5\">
								<div style='float:right; width:7em;'>
									<noscript>
										<input type=\"submit\" value=\"Enregistrer\" style=\"font-variant: small-caps;\" name=\"button_ok_sans_javascript1\" />
									</noscript>
									<input type=\"button\" value=\"Enregistrer\" style=\"font-variant: small-caps;\" name=\"button_ok_avec_javascript1\" onclick=\"check_et_valide_form()\" />
								</div>
								<span class='grand'>".$titre_mess." <span style='font-weight:bold; font-style:italic;'>(Interface 1)</span> - 
								<!--a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('aide','y',100,100);\" onmouseout=\"cacher_div('aide');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a-->
								<span id='span_lien_js_evenement_modif' style='display:none'><a href='dates_classes2.php";
if (isset($id_ev)) {
	echo "?id_ev=$id_ev' title=\"Modifier l'événement avec la nouvelle interface de saisie\n(nécessitant Javascript)\"";
}
else {
	echo "' title=\"Créer l'événement avec la nouvelle interface de saisie\n(nécessitant Javascript)\"";
}
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/edit16.png' class='icone16' alt='Modifier' /> 2 </a></span>
								</span>
							</td>
						</tr>
						<tr>
							<td colspan=\"5\">
								<i>Type de l'événement&nbsp;:</i>
							</td>
						</tr>
						<tr>
							<td style='vertical-align:top;'>
								<p style='margin-left:2em;text-indent:-2em;'>
									<input type='radio' name='type' id='type_conseil_de_classe' value='conseil_de_classe' onchange=\"checkbox_change('type_conseil_de_classe');checkbox_change('type_autre');changement2();\" ".($type=="conseil_de_classe" ? "checked " : "")."/><label for='type_conseil_de_classe' id='texte_type_conseil_de_classe'>Conseil de classe</label><br />
									Période <select name='periode' id='periode'>
										<option value='0'>---</option>
										$chaine_options_periodes
									</select>
									<a href='#' onclick=\"maj_date_conseil_classe();return false;\" title=\"Prendre pour dates de conseils de classe les dates définies dans la page de Verrouillage des périodes.\"><img src='../images/icons/wizard.png' class='icone16' alt='Màj' /></a>
								</p>
							</td>
							<td style='vertical-align:top;'>
								<input type='radio' name='type' id='type_autre' value='autre' onchange=\"checkbox_change('type_conseil_de_classe');checkbox_change('type_autre');changement2();\" ".($type!="conseil_de_classe" ? "checked " : "")."/><label for='type_autre' id='texte_type_autre'>Autre</label>
							</td>
						</tr>
						<tr>
							<td colspan=\"5\">
								<p><i>L'événement sera affiché à compter de la date&nbsp;: 
								<input type='text' name='display_date_debut' id='display_date_debut' size='10' value=\"".$display_date_debut."\" onKeyDown=\"clavier_date(this.id,event);\" onchange='changement2()' AutoComplete=\"off\" />
								".img_calendrier_js("display_date_debut", "img_bouton_display_date_debut")."<br />
								(<span style='font-size:small'>Respectez le format jj/mm/aaaa</span>)</p>
							</td>
						</tr>
						<tr>
							<td colspan=\"5\">
								<i>Statut(s) des destinataires de l'événement&nbsp;:</i>
							</td>
						</tr>
						<tr style='vertical-align:top'>
							<td>
								<input type=\"checkbox\" id=\"destinataire_prof\" name=\"destinataire_prof\" value=\"y\" ".(($destinataire_prof=="y") ? " checked" : "")." onchange=\"checkbox_change('destinataire_prof');changement2();\" /><label for='destinataire_prof' id='texte_destinataire_prof' style='cursor: pointer;'>Professeurs de la classe</label>
							</td>
							<td>
								<input type=\"checkbox\" id=\"destinataire_cpe\" name=\"destinataire_cpe\" value=\"y\" ".(($destinataire_cpe=="y") ? " checked" : "")." onchange=\"checkbox_change('destinataire_cpe');changement2();\" /><label for='destinataire_cpe' id='texte_destinataire_cpe' style='cursor: pointer;'>CPE de la classe</label>
							</td>
							<td>
								<input type=\"checkbox\" id=\"destinataire_scol\" name=\"destinataire_scol\" value=\"y\" ".(($destinataire_scol=="y") ? " checked" : "")." onchange=\"checkbox_change('destinataire_scol');changement2();\" /><label for='destinataire_scol' id='texte_destinataire_scol' style='cursor: pointer;'>Comptes scolarité associés à la classe</label>
							</td>
							<td>
								<input type=\"checkbox\" id=\"destinataire_resp\" name=\"destinataire_resp\" value=\"y\" ".(($destinataire_resp=="y") ? " checked" : "")." onchange=\"checkbox_change('destinataire_resp');changement2();\" /><label for='destinataire_resp' id='texte_destinataire_resp' style='cursor: pointer;'>Responsables d'élèves de la classe</label>
							</td>
							<td>
								<input type=\"checkbox\" id=\"destinataire_ele\" name=\"destinataire_ele\" value=\"y\" ".(($destinataire_ele=="y") ? " checked" : "")." onchange=\"checkbox_change('destinataire_ele');changement2();\" /><label for='destinataire_ele' id='texte_destinataire_ele' style='cursor: pointer;'>Élèves de la classe</label>
							</td>
						</tr>
						<tr>
							<td colspan=\"5\">
								<i>Classes concernées par l'événement&nbsp;:</i>
							</td>
						</tr>
						<tr>
							<td colspan=\"5\">";

$tab_salle=get_tab_salle_cours();

function ev_classe_options_salle($id_classe) {
	global $tab_classe_ev, $tab_salle;

	$retour="
													<option value=''>---</option>";
	for($loop=0;$loop<count($tab_salle['list']);$loop++) {
		$selected="";
		if((isset($tab_classe_ev[$id_classe]['id_salle']))&&($tab_salle['list'][$loop]['id_salle']==$tab_classe_ev[$id_classe]['id_salle'])) {
			$selected=" selected";
		}
		$retour.="
													<option value='".$tab_salle['list'][$loop]['id_salle']."'$selected>";
		//=============================
		// Debug:
		//$retour.="$loop : ";
		//=============================
		$retour.=$tab_salle['list'][$loop]['designation_complete'];
		//=============================
		// Debug:
		/*
		$retour.=" (id_salle=".$tab_salle['list'][$loop]['id_salle'].")";
		$retour.=" (id_classe=".$id_classe.")";
		*/
		//=============================
		$retour.="</option>";
	}

	return $retour;
}

echo "
								<table class='boireaus boireaus_alt sortable resizable' summary=\"Tableau de choix des classes et du paramétrage des dates\">
									<thead>
										<tr>
											<th class='text' title='Cliquez pour trier par nom de classe.'>Classe</th>
											<th class='text' title='Cliquez pour trier par date.\nLe tri fonctionne avec les dates validées/enregistrées.'>Date</th>
											<th title=\"Choisissez la ligne modèle pour copier une date.\">D</th>
											<th><img src='../images/icons/coller_23x24.png' class='icone16' title=\"Coller la date sélectionnée.\"/></th>
											<th></th>
											<th>Heure</th>
											<th title=\"Choisissez la ligne modèle pour copier une heure.\">H</th>
											<th><img src='../images/icons/coller_23x24.png' class='icone16' title=\"Coller l'heure sélectionnée.\"/></th>

											<th></th>
											<th>Salle <a href='../edt_organisation/ajouter_salle.php' target='_blank' title=\"Ajouter ou modifier une salle.\n\nSi vous ajoutez une salle, il faudra enregistrer l'événement et le modifier ensuite pour voir apparaître la nouvelle salle dans la liste proposée.\"><img src='../images/edit16.png' class='icone16' alt='Saisir' /></a></th>
											<th title=\"Choisissez la ligne modèle pour copier une salle.\">S</th>
											<th><img src='../images/icons/coller_23x24.png' class='icone16' title=\"Coller la salle sélectionnée.\"/></th>

										</tr>
									</thead>
									<tbody>";

$cpt=0;
$tab_champs_date_a_cacher=array();
foreach($tab_classe as $id_classe => $classe) {
	if(isset($tab_classe_ev[$id_classe]["heure_evenement"])) {
		$display_heure=$tab_classe_ev[$id_classe]["heure_evenement"];
	}
	else {
		// Il est plus facile de voir ce qui n'est pas encore rempli/défini.
		//$display_heure=$heure_courante;
		$display_heure="";
	}

	echo "
										<tr id='div_ligne_$id_classe' onmouseover=\"this.style.backgroundColor='white'\" onmouseout=\"this.style.backgroundColor=''\">
										<td>
										<span style='display:none' title='Pour le tri.'>".$classe['classe']."</span>
										<input type=\"checkbox\" id=\"id_classe_".$id_classe."\" name=\"id_classe[$cpt]\" value=\"$id_classe\" ".((array_key_exists($id_classe, $tab_classe_ev)) ? " checked" : "")." onchange=\"modif_affichage_ligne_classe($id_classe);changement2();\" /><label for='id_classe_".$id_classe."' id='texte_id_classe_".$id_classe."' style='cursor: pointer;'>".$classe['classe']."</label>
										</td>

										<td>
											<span style='display:none' title='Pour le tri.'>".(isset($tab_classe_ev[$id_classe]['date_evenement']) ? $tab_classe_ev[$id_classe]['date_evenement'] : "")."</span>
											<span id='span_date_id_classe_".$id_classe."'>
												&nbsp;
												<input type='text' name='display_date_id_classe[$cpt]' id='display_date_id_classe_".$id_classe."' size='10' value=\"".(isset($tab_classe_ev[$id_classe]['date_evenement_formatee']) ? $tab_classe_ev[$id_classe]['date_evenement_formatee'] : "")."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />
												".img_calendrier_js("display_date_id_classe_".$id_classe, "img_bouton_display_date_id_classe_".$id_classe)."

											</span>
										</td>

										<td>
											<span id='js_copier_date_".$id_classe."' style='display:none;'>
												<input type='radio' name = 'copier_date' id= 'copier_date_".$id_classe."' value = \"".$id_classe."\" /><label for='copier_date_".$id_classe."'><img src='../images/icons/copy-16.png' class='icone16' title=\"Copier la date associée à cette classe.\"/></label>
											</span>
										</td>

										<td>
											<span id='js_coller_date_".$id_classe."' style='display:none;'>
												<a href='#' onclick=\"coller_date($id_classe);return false;\" id='js_coller_$cpt'>
													<img src='../images/icons/coller_23x24.png' class='icone16' title=\"Coller la date sélectionnée.\"/>
												</a>
											</span>
										</td>


										<td>
											&nbsp;à&nbsp;
										</td>
										<td>
											<span id='span_heure_id_classe_".$id_classe."'>
												<input type='text' name = 'display_heure_id_classe[".$cpt."]' id= 'display_heure_id_classe_".$id_classe."' size='5' value = \"".$display_heure."\" onKeyDown=\"clavier_heure(this.id,event);\" AutoComplete=\"off\" />
											</span>
										</td>

										<td>
											<span id='js_copier_heure_".$id_classe."' style='display:none;'>
												<input type='radio' name = 'copier_heure' id= 'copier_heure_".$id_classe."' value = \"".$id_classe."\" /><label for='copier_heure_".$id_classe."'><img src='../images/icons/copy-16.png' class='icone16' title=\"Copier l'heure associée à cette classe.\"/></label>
											</span>
										</td>
										<td>
											<span id='js_coller_heure_".$id_classe."' style='display:none;'>
												&nbsp;
												<a href='#' onclick=\"coller_heure($id_classe);return false;\" id='js_coller_$cpt'>
													<img src='../images/icons/coller_23x24.png' class='icone16' title=\"Coller l'heure sélectionnée.\"/>
												</a>
											</span>
										</td>


										<td>
											&nbsp;en salle&nbsp;
										</td>
										<td>
											<span id='span_salle_id_classe_".$id_classe."'>
												<select name='salle_id_classe[".$cpt."]' id='salle_id_classe_$id_classe' onchange='changement2()'>
													".ev_classe_options_salle($id_classe)."
												</select>
											</span>
										</td>

										<td>
											<span id='js_copier_salle_".$id_classe."' style='display:none;'>
												<input type='radio' name = 'copier_salle' id= 'copier_salle_".$id_classe."' value = \"".$id_classe."\" /><label for='copier_salle_".$id_classe."'><img src='../images/icons/copy-16.png' class='icone16' title=\"Copier la salle associée à cette classe.\"/></label>
											</span>
										</td>
										<td>
											<span id='js_coller_salle_".$id_classe."' style='display:none;'>
												&nbsp;
												<a href='#' onclick=\"coller_salle($id_classe);return false;\" id='js_coller_$cpt'>
													<img src='../images/icons/coller_23x24.png' class='icone16' title=\"Coller la salle sélectionnée.\"/>
												</a>
											</span>
										</td>
										</tr>";

	if(!array_key_exists($id_classe, $tab_classe_ev)) {
		$tab_champs_date_a_cacher[]=$id_classe;
	}

	$cpt++;
}

echo "
									</tbody>
								</table>";

echo "
							</td>
						</tr>
						<tr>
							<td colspan=\"5\">
								<i>Texte affiché avant les dates&nbsp;:</i>";

?>

<textarea name="texte_avant" id ="texte_avant" style="border: 1px solid gray; width: 600px; height: 250px;"><?php echo $texte_avant; ?></textarea>
<script type='text/javascript'>
// Configuration via JavaScript
CKEDITOR.replace('texte_avant',{
    customConfig: '../lib/ckeditor_gepi_config_mini.js'
});
</script>

<?php
echo "
							</td>
						</tr>
						<tr>
							<td colspan=\"5\">
								<i>Texte affiché après les dates pour les personnels&nbsp;:</i>";

?>

<textarea name="texte_apres" id ="texte_apres" style="border: 1px solid gray; width: 600px; height: 250px;"><?php echo $texte_apres; ?></textarea>
<script type='text/javascript'>
// Configuration via JavaScript
CKEDITOR.replace('texte_apres',{
    customConfig: '../lib/ckeditor_gepi_config_mini.js'
});
</script>

<?php
echo "
							</td>
						</tr>
						<tr>
							<td colspan=\"5\">
								<i>Texte affiché après les dates pour les élèves et responsables&nbsp;:<br />
								(<em>sous réserve qu'ils soient concernés par cet événement</em>)</i>";

?>

<textarea name="texte_apres_ele_resp" id ="texte_apres_ele_resp" style="border: 1px solid gray; width: 600px; height: 250px;"><?php echo $texte_apres_ele_resp; ?></textarea>
<script type='text/javascript'>
// Configuration via JavaScript
CKEDITOR.replace('texte_apres_ele_resp',{
    customConfig: '../lib/ckeditor_gepi_config_mini.js'
});
</script>

<?php
echo "
							</td>
						</tr>
						<tr>
							<td colspan=\"5\" align=\"center\"> 
								<input type='hidden' name='ok' value='y' />
								<noscript>
									<input type=\"submit\" value=\"Enregistrer\" style=\"font-variant: small-caps;\" name=\"button_ok_sans_javascript\" />
								</noscript>
								<input type=\"button\" value=\"Enregistrer\" style=\"font-variant: small-caps;\" name=\"button_ok_avec_javascript\" onclick=\"check_et_valide_form()\" />
								<script type='text/javascript'>
									function checkdate (m, d, y) {
							  // Returns true(1) if it is a valid date in gregorian calendar  
							  // 
							  // version: 1109.2015
							  // discuss at: http://phpjs.org/functions/checkdate    
							  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
							  // +   improved by: Pyerre
							  // +   improved by: Theriault
							  // *     example 1: checkdate(12, 31, 2000);
							  // *     returns 1: true    // *     example 2: checkdate(2, 29, 2001);
							  // *     returns 2: false
							  // *     example 3: checkdate(3, 31, 2008);
							  // *     returns 3: true
							  // *     example 4: checkdate(1, 390, 2000);    
							  // *     returns 4: false
							  return m > 0 && m < 13 && y > 2000 && y < 32768 && d > 0 && d <= (new Date(y, m, 0)).getDate();
									}

									function check_et_valide_form() {
										valider_le_submit='y';

										display_date_debut=document.getElementById('display_date_debut').value;

										tmp=display_date_debut.split('/');
										jour_debut=tmp[0];
										mois_debut=tmp[1];
										annee_debut=tmp[2];
										if(!checkdate(mois_debut,jour_debut,annee_debut)) {
											alert('La date de début d\'affichage est invalide.');
											valider_le_submit='n';
										}

										if((document.getElementById('destinataire_cpe').checked==false)&&
										(document.getElementById('destinataire_prof').checked==false)&&
										(document.getElementById('destinataire_scol').checked==false)&&
										(document.getElementById('destinataire_ele').checked==false)&&
										(document.getElementById('destinataire_resp').checked==false))
										 {
											alert('Aucun destinataire n\'a été coché.');
											valider_le_submit='n';
										}


										/*
										// JE NE TROUVE PAS COMMENT CONTROLER QUE LE CONTENU DU TEXTAREA CKEDITOR EST NON VIDE
										if(document.getElementById('texte_avant').value=='') {
											alert('Le texte_avant ne peut pas être vide.');
											valider_le_submit='n';
										}

										alert(CKEDITOR.instances['texte_avant'].name);

										alert(CKEDITOR.instances['texte_avant'].getValue());

										alert(CKEDITOR.instances['texte_avant'].value);
										CKEDITOR.instances['texte_avant'].updateElement();
										alert(CKEDITOR.instances['texte_avant'].value);
										alert(CKEDITOR.instances['texte_avant'].getData());
										*/

										if(valider_le_submit=='y') {
											document.formulaire_saisie_evenement.submit();
										}
									}
								</script>
								".(isset($id_ev) ? "<input type=\"submit\" value=\"Annuler\" style=\"font-variant: small-caps;\" name=\"cancel\" />" : "")."
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</td>
	</tr>
</table>

<script type='text/javascript'>

if(document.getElementById('span_lien_js_nouvel_evenement')) {
	document.getElementById('span_lien_js_nouvel_evenement').style.display='';
}

if(document.getElementById('span_lien_js_evenement_modif')) {
	document.getElementById('span_lien_js_evenement_modif').style.display='';
}

for(i=0;i<$nb_messages;i++) {
	if(document.getElementById('span_lien_js_evenement_'+i)) {
		document.getElementById('span_lien_js_evenement_'+i).style.display='';
	}
}

function changement2() {
	changement();
	if(document.getElementById('span_lien_js_evenement_modif')) {
		document.getElementById('span_lien_js_evenement_modif').style.display='none';
	}
}

".js_checkbox_change_style('checkbox_change', 'texte_', 'n')."

	checkbox_change('type_autre');
	checkbox_change('type_conseil_de_classe');
	checkbox_change('destinataire_prof');
	checkbox_change('destinataire_cpe');
	checkbox_change('destinataire_scol');
	checkbox_change('destinataire_resp');
	checkbox_change('destinataire_ele');

	function modif_affichage_ligne_classe(id_classe) {
		checkbox_change('id_classe_'+id_classe);

		if(document.getElementById('id_classe_'+id_classe).checked==true) {
			document.getElementById('span_date_id_classe_'+id_classe).style.display=''
			document.getElementById('span_heure_id_classe_'+id_classe).style.display=''
			document.getElementById('js_copier_date_'+id_classe).style.display='';
			document.getElementById('js_coller_date_'+id_classe).style.display='';
			document.getElementById('js_copier_heure_'+id_classe).style.display='';
			document.getElementById('js_coller_heure_'+id_classe).style.display='';
			document.getElementById('js_copier_salle_'+id_classe).style.display='';
			document.getElementById('js_coller_salle_'+id_classe).style.display='';
		}
		else {
			document.getElementById('span_date_id_classe_'+id_classe).style.display='none'
			document.getElementById('span_heure_id_classe_'+id_classe).style.display='none'
			document.getElementById('js_copier_date_'+id_classe).style.display='none';
			document.getElementById('js_coller_date_'+id_classe).style.display='none';
			document.getElementById('js_copier_heure_'+id_classe).style.display='none';
			document.getElementById('js_coller_heure_'+id_classe).style.display='none';
			document.getElementById('js_copier_salle_'+id_classe).style.display='none';
			document.getElementById('js_coller_salle_'+id_classe).style.display='none';
		}
		//changement();
	}
";

foreach($tab_classe as $id_classe => $classe) {
	echo "
	checkbox_change('id_classe_".$id_classe."');
	modif_affichage_ligne_classe($id_classe);";
}

for($loop=0;$loop<count($tab_champs_date_a_cacher);$loop++) {
	echo "
	document.getElementById('span_date_id_classe_".$tab_champs_date_a_cacher[$loop]."').style.display='none';
	document.getElementById('span_heure_id_classe_".$tab_champs_date_a_cacher[$loop]."').style.display='none';
	document.getElementById('js_copier_date_".$tab_champs_date_a_cacher[$loop]."').style.display='none';
	document.getElementById('js_coller_date_".$tab_champs_date_a_cacher[$loop]."').style.display='none';
	document.getElementById('js_copier_heure_".$tab_champs_date_a_cacher[$loop]."').style.display='none';
	document.getElementById('js_coller_heure_".$tab_champs_date_a_cacher[$loop]."').style.display='none';
	document.getElementById('js_copier_salle_".$tab_champs_date_a_cacher[$loop]."').style.display='none';
	document.getElementById('js_coller_salle_".$tab_champs_date_a_cacher[$loop]."').style.display='none';
	";
}

echo "

	function coller_date(id_classe) {
		radio_copier_date=document.formulaire_saisie_evenement.copier_date;
		for(i=0;i<radio_copier_date.length;i++) {
			if (radio_copier_date[i].checked) {
				document.getElementById('display_date_id_classe_'+id_classe).value=document.getElementById('display_date_id_classe_'+radio_copier_date[i].value).value;
			}
		}
	}

	function coller_heure(id_classe) {
		radio_copier_heure=document.formulaire_saisie_evenement.copier_heure;
		for(i=0;i<radio_copier_heure.length;i++) {
			if (radio_copier_heure[i].checked) {
				document.getElementById('display_heure_id_classe_'+id_classe).value=document.getElementById('display_heure_id_classe_'+radio_copier_heure[i].value).value;
			}
		}
	}

	function coller_salle(id_classe) {
		radio_copier_salle=document.formulaire_saisie_evenement.copier_salle;
		for(i=0;i<radio_copier_salle.length;i++) {
			if(radio_copier_salle[i].checked) {
				id_classe_modele=radio_copier_salle[i].value;

				indice_select_salle=document.getElementById('salle_id_classe_'+id_classe_modele).options.selectedIndex;
				document.getElementById('salle_id_classe_'+id_classe).options.selectedIndex=indice_select_salle;
			}
		}
	}

	function maj_date_conseil_classe() {
		periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;

$lignes_js_dates_conseils_classes
	}
</script>\n";

// Fin de la colonne de droite

echo "</td></tr></table>\n";

echo "<p style='color:red'>A FAIRE : Pouvoir dupliquer un événement (par exemple pour un affichage un peu différent selon les statuts destinataires.)</p>";

require("../lib/footer.inc.php");
?>
