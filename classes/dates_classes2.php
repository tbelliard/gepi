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

include("../ckeditor/ckeditor.php") ;

$sql="SELECT 1=1 FROM droits WHERE id='/classes/dates_classes2.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/classes/dates_classes2.php',
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

$mode = isset($_POST["mode"]) ? $_POST["mode"] :(isset($_GET["mode"]) ? $_GET["mode"] :NULL);

$periode=isset($_POST['periode']) ? $_POST['periode'] : 0;

//debug_var();

if (isset($id_ev)) {
	// Si on n'a pas fait le ménage dans les événements lors de l'initialisation
	$sql="DELETE FROM d_dates_evenements_classes WHERE id_ev='$id_ev' AND id_classe NOT IN (SELECT id FROM classes);";
	//echo "$sql<br />";
	$menage=mysqli_query($GLOBALS['mysqli'], $sql);
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
				$msg_OK="Enregistrement effectué pour l'événement n°".$id_ev."<br />";

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
				$msg_OK="Enregistrement effectué pour l'événement n°".$id_ev."<br />";

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

	}
}

if((isset($mode))&&($mode=="enregistrer")&&(isset($id_ev))) {
	check_token();

	$id_classe_ev=isset($_POST['id_classe_ev']) ? $_POST['id_classe_ev'] : array();

	$reg_id_classe_ev=isset($_POST['reg_id_classe_ev']) ? $_POST['reg_id_classe_ev'] : NULL;
	if(!isset($reg_id_classe_ev)) {
		$msg_erreur.="Aucun positionnement classe/créneau n'a été choisi.<br />";
		$mode="positionner";
		$sql="DELETE FROM d_dates_evenements_classes WHERE id_ev='$id_ev';";
		//echo "$sql<br />";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$del) {
			$msg_erreur.="Erreur lors de la suppression d'éventuels positionnements de classes sur des créneaux.<br />";
		}
	}
	else {
		$sql="SELECT * FROM d_dates_evenements WHERE id_ev='$id_ev';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$msg_erreur.="L'événement $id_ev n'existe pas.<br />";
		}
		else {
			$lig_ev=mysqli_fetch_object($res);
			$type=$lig_ev->type;
			$periode=$lig_ev->periode;

			$nb_insert=0;
			$nb_update=0;
			$nb_suppr=0;
			$nb_err=0;
			$tab_id_classe_placees=array();
			for($loop=0;$loop<count($reg_id_classe_ev);$loop++) {
				$tab=explode("|", $reg_id_classe_ev[$loop]);
				$current_id_classe=$tab[0];
				$tab_id_classe_placees[]=$current_id_classe;
				$current_ts=$tab[1];
				$current_mysql_date=strftime("%Y-%m-%d %H:%M:%S", $current_ts);
				$current_id_salle=$tab[2];

				$sql="SELECT * FROM d_dates_evenements_classes WHERE id_ev='$id_ev' AND id_classe='".$current_id_classe."';";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)==0) {
					$sql="INSERT INTO d_dates_evenements_classes SET id_ev='$id_ev', id_classe='".$current_id_classe."', date_evenement='".$current_mysql_date."', id_salle='".$current_id_salle."';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if($insert) {
						$nb_insert++;

						if(($type=="conseil_de_classe")&&(preg_match("/^[0-9]{1,}$/", $periode))&&($periode>=1)) {
							$sql="UPDATE periodes SET date_conseil_classe='".$current_mysql_date."' WHERE id_classe='".$current_id_classe."' AND num_periode='".$periode."' ";
							//echo "$sql<br />";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$update) {
								$msg_erreur.="Erreur lors de la mise à jour de la date du conseil de classe pour la classe de ".get_nom_classe($current_id_classe)." en période ".$periode."<br />";
							}
						}
					}
					else {
						$nb_err++;
					}
				}
				else {
					$lig=mysqli_fetch_object($res);
					if(($lig->date_evenement!=$current_mysql_date)||
					($lig->id_salle!=$current_id_salle)) {
						$sql="UPDATE d_dates_evenements_classes SET date_evenement='".$current_mysql_date."', 
												id_salle='".$current_id_salle."' 
											WHERE id_ev='$id_ev' AND 
												id_classe='".$current_id_classe."';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if($update) {
							$nb_update++;

							if(($type=="conseil_de_classe")&&(preg_match("/^[0-9]{1,}$/", $periode))&&($periode>=1)) {
								$sql="UPDATE periodes SET date_conseil_classe='".$current_mysql_date."' WHERE id_classe='".$current_id_classe."' AND num_periode='".$periode."' ";
								//echo "$sql<br />";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$update) {
									$msg_erreur.="Erreur lors de la mise à jour de la date du conseil de classe pour la classe de ".get_nom_classe($current_id_classe)." en période ".$periode."<br />";
								}
							}
						}
						else {
							$nb_err++;
						}
					}
				}
			}

			for($loop=0;$loop<count($id_classe_ev);$loop++) {
				if(!in_array($id_classe_ev[$loop], $tab_id_classe_placees)) {
					$sql="SELECT * FROM d_dates_evenements_classes WHERE id_ev='$id_ev' AND id_classe='".$id_classe_ev[$loop]."';";
					//echo "$sql<br />";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)>0) {
						$sql="DELETE FROM d_dates_evenements_classes WHERE id_ev='$id_ev' AND 
													id_classe='".$id_classe_ev[$loop]."';";
						//echo "$sql<br />";
						$del=mysqli_query($GLOBALS["mysqli"], $sql);
						if($del) {
							$nb_suppr++;
						}
						else {
							$nb_err++;
						}
					}
				}
			}

			if($nb_insert>0) {
				$msg_OK.=$nb_insert." enregistrement(s) effectué(s).<br />";
			}
			if($nb_update>0) {
				$msg_OK.=$nb_update." enregistrement(s) mis à jour.<br />";
			}
			if($nb_suppr>0) {
				$msg_OK.=$nb_suppr." enregistrement(s) supprimés.<br />";
			}
			if($nb_err>0) {
				$msg_erreur.=$nb_err." erreurs lors des enregistrements !<br />";
			}

			if(($nb_insert==0)&&($nb_update==0)&&($nb_suppr==0)&&($nb_err==0)) {
				$msg_erreur.="Pas de modification.<br />";
			}

			$mode="positionner";

		}
	}
}

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_suppression = "Confirmation de suppression";
//**************** EN-TETE *****************
$titre_page = "Ajout événement";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

$evenement_sans_lien_mail="y";
$evenement_sans_lien_ics="y";
//debug_var();

echo "<a name=\"debut_de_page\"></a>";

echo "<div style='color: #FF0000; text-align: center; padding: 0.5%;'>";
if ($msg_erreur!="") echo "<p style='color: #FF0000; font-variant: small-caps;'>".$msg_erreur."</p>";
if ($msg_OK!="") echo "<p style='color: #0000FF; font-variant: small-caps;'>".$msg_OK."</p>";
echo "</div>";


echo "<script type=\"text/javascript\" language=\"JavaScript\" SRC=\"../lib/clock_fr.js\"></SCRIPT>\n";
//-----------------------------------------------------------------------------------
echo "<p class='bold'><a href='../accueil.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Nouvel événement</a></p>\n";
echo "<table width=\"98%\" cellspacing=0 align=\"center\">\n";
echo "<tr>\n";
echo "<td valign='top'>\n";
echo "<p>Nous sommes le&nbsp;:&nbsp;\n";
echo "<script type=\"text/javascript\" language=\"javascript\">\n";
echo "<!--\n";
echo "new LiveClock();\n";
echo "//-->\n";
echo "</SCRIPT></p>\n";
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

if((!isset($id_ev))||
((isset($mode))&&($mode=="modif_ev"))) {

	// Initialisation: Valeurs par défaut
	$titre_mess = "Nouvel événement";
	$date_debut=strftime("%Y-%m-%d %H:%M:%S");
	$heure_courante=strftime("%H:%M");
	$periode=0;
	$texte_avant="";
	$texte_apres="";
	$texte_apres_ele_resp="";
	if (isset($id_ev)) {
		$tab_ev=get_tab_infos_evenement($id_ev);
		if(count($tab_ev)==0) {
			echo "<p style='color:red'>L'événément n°$id_ev n'existe pas.</p>\n";
		}
		else {
			// Modification des valeurs
			$titre_mess = "Modification de l'événement n°".$id_ev;

			$type=$tab_ev['type'];
			$periode=$tab_ev['periode'];
			$date_debut=$tab_ev['date_debut'];
			$texte_avant=$tab_ev['texte_avant'];
			$texte_apres=$tab_ev['texte_apres'];
			$texte_apres_ele_resp=$tab_ev['texte_apres_ele_resp'];

			if(in_array("professeur", $tab_ev['statuts'])) {
				$destinataire_prof="y";
			}
			else {
				$destinataire_prof="n";
			}
			if(in_array("cpe", $tab_ev['statuts'])) {
				$destinataire_cpe="y";
			}
			else {
				$destinataire_cpe="n";
			}
			if(in_array("scolarite", $tab_ev['statuts'])) {
				$destinataire_scol="y";
			}
			else {
				$destinataire_scol="n";
			}

			if(in_array("responsable", $tab_ev['statuts'])) {
				$destinataire_resp="y";
			}
			else {
				$destinataire_resp="n";
			}

			if(in_array("eleve", $tab_ev['statuts'])) {
				$destinataire_ele="y";
			}
			else {
				$destinataire_ele="n";
			}
		}
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


	$ligne_input_id_ev="";
	if (isset($id_ev)) {
		$ligne_input_id_ev="
					<input type=\"hidden\" name=\"id_ev\" value=\"$id_ev\" />";
	}

	echo "
			<form action=\"".$_SERVER['PHP_SELF']."#debut_de_page\" method=\"post\" style=\"width: 100%;\" name=\"formulaire_saisie_evenement\">
				<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
					".add_token_field().$ligne_input_id_ev."

					<input type=\"hidden\" name=\"action\" value=\"evenement\" />

					<table border=\"0\" width = \"100%\" cellspacing=\"1\" cellpadding=\"1\" >
						<tr>
							<td colspan=\"4\">
								<span class='grand'>".$titre_mess." 
								<!--a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('aide','y',100,100);\" onmouseout=\"cacher_div('aide');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a-->
								</span>
							</td>
						</tr>
						<tr>
							<td colspan=\"4\">
								<i>Type de l'événement&nbsp;:</i>
							</td>
						</tr>
						<tr>
							<td style='vertical-align:top;'>
								<p style='margin-left:2em;text-indent:-2em;'>
								<input type='radio' name='type' id='type_conseil_de_classe' value='conseil_de_classe' onchange=\"checkbox_change('type_conseil_de_classe');checkbox_change('type_autre');changement();\" ".($type=="conseil_de_classe" ? "checked " : "")."/><label for='type_conseil_de_classe' id='texte_type_conseil_de_classe'>Conseil de classe</label><br />
								Période <select name='periode' id='periode'>
									<option value='0'>---</option>
									$chaine_options_periodes
								</select>
								<!--a href='#' onclick=\"maj_date_conseil_classe();return false;\" title=\"Prendre pour dates de conseils de classe les dates définies dans la page de Verrouillage des périodes.\"><img src='../images/icons/wizard.png' class='icone16' alt='Màj' /></a-->
								</p>
							</td>
							<td style='vertical-align:top;'>
								<input type='radio' name='type' id='type_autre' value='autre' onchange=\"checkbox_change('type_conseil_de_classe');checkbox_change('type_autre');changement();\" ".($type!="conseil_de_classe" ? "checked " : "")."/><label for='type_autre' id='texte_type_autre'>Autre</label>
							</td>
						</tr>
						<tr>
							<td colspan=\"4\">
								<p><i>L'événement sera affiché à compter de la date&nbsp;: 
								<input type='text' name='display_date_debut' id='display_date_debut' size='10' value=\"".$display_date_debut."\" onKeyDown=\"clavier_date(this.id,event);\" onchange='changement()' AutoComplete=\"off\" />
								".img_calendrier_js("display_date_debut", "img_bouton_display_date_debut")."<br />
								(<span style='font-size:small'>Respectez le format jj/mm/aaaa</span>)</p>
							</td>
						</tr>
						<tr>
							<td colspan=\"4\">
								<i>Statut(s) des destinataires de l'événement&nbsp;:</i>
							</td>
						</tr>
						<tr style='vertical-align:top'>
							<td>
								<input type=\"checkbox\" id=\"destinataire_prof\" name=\"destinataire_prof\" value=\"y\" ".(($destinataire_prof=="y") ? " checked" : "")." onchange=\"checkbox_change('destinataire_prof');changement();\" /><label for='destinataire_prof' id='texte_destinataire_prof' style='cursor: pointer;'>Professeurs de la classe</label>
							</td>
							<td>
								<input type=\"checkbox\" id=\"destinataire_cpe\" name=\"destinataire_cpe\" value=\"y\" ".(($destinataire_cpe=="y") ? " checked" : "")." onchange=\"checkbox_change('destinataire_cpe');changement();\" /><label for='destinataire_cpe' id='texte_destinataire_cpe' style='cursor: pointer;'>CPE de la classe</label>
							</td>
							<td>
								<input type=\"checkbox\" id=\"destinataire_scol\" name=\"destinataire_scol\" value=\"y\" ".(($destinataire_scol=="y") ? " checked" : "")." onchange=\"checkbox_change('destinataire_scol');changement();\" /><label for='destinataire_scol' id='texte_destinataire_scol' style='cursor: pointer;'>Comptes scolarité associés à la classe</label>
							</td>
							<td>
								<input type=\"checkbox\" id=\"destinataire_resp\" name=\"destinataire_resp\" value=\"y\" ".(($destinataire_resp=="y") ? " checked" : "")." onchange=\"checkbox_change('destinataire_resp');changement();\" /><label for='destinataire_resp' id='texte_destinataire_resp' style='cursor: pointer;'>Responsables d'élèves de la classe</label>
							</td>
							<td>
								<input type=\"checkbox\" id=\"destinataire_ele\" name=\"destinataire_ele\" value=\"y\" ".(($destinataire_ele=="y") ? " checked" : "")." onchange=\"checkbox_change('destinataire_ele');changement();\" /><label for='destinataire_ele' id='texte_destinataire_ele' style='cursor: pointer;'>Élèves de la classe</label>
							</td>
						</tr>
						<tr>
							<td colspan='4'>
								<p style='margin-top:1em;'>
															<i>Texte affiché avant les dates :</i>";
$oCKeditor = new CKeditor('../ckeditor/');
$oCKeditor->editor('texte_avant',$texte_avant);
echo "
							</td>
						</tr>
						<tr>
							<td colspan=\"4\">
								<i>Texte affiché après les dates :</i>";
$oCKeditor2 = new CKeditor('../ckeditor/');
$oCKeditor2->editor('texte_apres',$texte_apres);
	echo "
							</td>
						</tr>
						<tr>
							<td colspan=\"4\">
								<i>Texte affiché après les dates pour les élèves et responsables<br />(<em>sous réserve qu'ils soient concernés par cet événement</em>)&nbsp;:</i>";
$oCKeditor3 = new CKeditor('../ckeditor/');
$oCKeditor3->editor('texte_apres_ele_resp',$texte_apres_ele_resp);
echo "
							</td>
						</tr>
					</table>

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
				</fieldset>
			</form>";

}
elseif((isset($mode))&&($mode=="ajouts")) {
	// Liens d'ajouts
	echo "
	<p><a href='".$_SERVER['PHP_SELF']."?id_ev=$id_ev&amp;mode=modif_ev'>Modifier la date de début, les destinataires</a><br />
		<a href='".$_SERVER['PHP_SELF']."?id_ev=$id_ev&amp;mode=positionner'>Positionner les classes sur les dates et lieux.</a>
	</p>

	<form action=\"".$_SERVER['PHP_SELF']."#debut_de_page\" method=\"post\" style=\"width: 100%;\" name=\"formulaire_saisie_evenement\">
		<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
			".add_token_field()."

			<input type=\"hidden\" name=\"id_ev\" value=\"$id_ev\" />
			<input type=\"hidden\" name=\"mode\" value=\"positionner\" />";

	echo affiche_details_evenement($id_ev, "y");

	echo "<p class='bold' style='margin-top:1em;'>Ajout de dates, classes et lieux pour  l'événement n°".$id_ev."</p>";

	// Il faudrait pointer les dates déjà définies, avec classe associée... pour en interdire la suppression.
	// Idem pour les lieux

	$chaine_js_classes_deja="";
	$sql="SELECT DISTINCT d.id_classe, c.classe FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND c.id=d.id_classe ORDER BY c.classe;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "<p>La ou les classes suivantes sont déjà associées à l'événement n°$id_ev&nbsp;: <strong>";
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			if($cpt>0) {
				echo ", ";
			}
			echo $lig->classe."<input type='hidden' name='id_classe_ev[]' value='".$lig->id_classe."' />";
			$chaine_js_classes_deja.="document.getElementById('lien_ajout_classe_'+".$lig->id_classe.").style.display='none';\n";
			$cpt++;
		}
		echo "</strong><br /><span style='font-size:x-small'>(<em>il sera possible de supprimer certaines de ces classes en ne les associant pas à une date à l'étape suivante</em>)</span></p>";
	}

	$chaine_js_salles_deja="";
	$sql="SELECT DISTINCT d.id_salle, sc.nom_salle, sc.numero_salle FROM d_dates_evenements_classes d, salle_cours sc WHERE id_ev='$id_ev' AND sc.id_salle=d.id_salle ORDER BY sc.nom_salle, sc.numero_salle;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "<p>La ou les salles suivantes sont déjà associées à l'événement n°$id_ev&nbsp;: <strong>";
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			if($cpt>0) {
				echo ", ";
			}
			echo $lig->nom_salle." (".$lig->numero_salle.")<input type='hidden' name='id_salle_ev[]' value='".$lig->id_salle."' />";
			$chaine_js_salles_deja.="document.getElementById('lien_ajout_salle_'+".$lig->id_salle.").style.display='none';\n";
			$cpt++;
		}
		echo "</strong><br /><span style='font-size:x-small'>(<em>il sera possible de supprimer certaines de ces salles en ne les associant pas à une date à l'étape suivante</em>)</span></p>";
	}

	$sql="SELECT DISTINCT d.date_evenement FROM d_dates_evenements_classes d WHERE id_ev='$id_ev' ORDER BY date_evenement;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "<p>La ou les dates suivantes sont déjà associées à l'événement n°$id_ev&nbsp;: <strong>";
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			if($cpt>0) {
				echo ", ";
			}
			echo formate_date($lig->date_evenement, "y", "court")."<input type='hidden' name='date_heure_ev[]' value='".$lig->date_evenement."' />";
			$cpt++;
		}
		echo "</strong><br /><span style='font-size:x-small'>(<em>il sera possible de supprimer certaines de ces salles en ne les associant pas à une date à l'étape suivante</em>)</span></p>";
	}

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++

	$titre_infobulle="Ajout de dates";
	$texte_infobulle="<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" target=\"_blank\">
	<p>
		<input type='text' name='date_ev' id='date_ev' size='10' value=\"".strftime("%d/%m/%Y")."\" onKeyDown=\"clavier_date(this.id,event);\" onchange='changement()' AutoComplete=\"off\" />
		".img_calendrier_js("date_ev", "img_bouton_date_ev")." 
		<input type='text' name='heure_ev' id='heure_ev' size='5' value=\"".strftime("%H:%M")."\" onKeyDown=\"clavier_heure(this.id,event);\" onchange='changement()' AutoComplete=\"off\" />
		<img src='../images/icons/ico_ampoule.png' class='icone16' alt='Aide' title=\"Vous pouvez utiliser les flèches Haut/Bas du clavier pour modifier les dates et heures.\n\nAttention : Seules les dates et heures correctement formatées\n                  seront validées:\n                  Dates au format jj/mm/aaaa et heures au format hh:mm\">
		<br />
		<input type='button' value=\"Ajouter\" onclick=\"ajouter_date_ev()\" />
	</p>
</form>";
	$tabdiv_infobulle[]=creer_div_infobulle('div_ajout_date',$titre_infobulle,"",$texte_infobulle,"",18,0,'y','y','n','n');

	echo "<p><a href=\"javascript:afficher_div('div_ajout_date','y',100,100);\">Ajouter des dates</a></p><div id='div_dates' style='margin-left:3em;'></div>";



	//+++++++++++++++++++++++++++++++++++++++++++++++++++++
	echo "<style type='text/css'>
.div_3_colonnes {
	-webkit-columns: 3;
	-moz-columns: 3;
	columns: 3;

	-webkit-column-gap: 3em;
	-moz-column-gap: 3em;
	column-gap: 3em;
}
</style>";

	$tab_salle=get_tab_salle_cours();

	$titre_infobulle="Ajout de lieux";

	$texte_infobulle="<p>Choisissez le ou les lieux à ajouter</p>";
	$texte_infobulle.="<div class='div_3_colonnes'>";
	for($loop=0;$loop<count($tab_salle['list']);$loop++) {
		$texte_infobulle.="<p id='p_lien_ajout_salle_".$tab_salle['list'][$loop]['id_salle']."'><a href=\"javascript:ajouter_salle_ev(".$tab_salle['list'][$loop]['id_salle'].")\" id='lien_ajout_salle_".$tab_salle['list'][$loop]['id_salle']."'>".$tab_salle['list'][$loop]['designation_complete']."</a></p>";
	}
	//$texte_infobulle.="</td></tr></table>";
	$texte_infobulle.="</div>";


	$tabdiv_infobulle[]=creer_div_infobulle('div_ajout_lieu',$titre_infobulle,"",$texte_infobulle,"",25,0,'y','y','n','n');

	echo "<p><a href=\"javascript:afficher_div('div_ajout_lieu','y',100,100);\">Ajouter des lieux</a></p><div id='div_lieux' style='margin-left:3em;'></div>";

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++


	$titre_infobulle="Ajout de classes";

	//retourne_sql_mes_classes()
	$sql="SELECT DISTINCT c.id, c.id as id_classe, c.classe FROM classes c ORDER BY classe";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);

	$texte_infobulle="<p>Choisissez la ou les classes à ajouter</p>";
	$texte_infobulle.="<div class='div_3_colonnes'>";
	if(mysqli_num_rows($res)==0) {
		$texte_infobulle.="<p style='color:red'>Aucune classe n'a été trouvée.</p>";
	}
	else {
		while($lig=mysqli_fetch_object($res)) {
			$texte_infobulle.="<p id='p_lien_ajout_classe_".$lig->id_classe."'><a href=\"javascript:ajouter_classe_ev(".$lig->id_classe.")\" id='lien_ajout_classe_".$lig->id_classe."'>".$lig->classe."</a></p>";
		}
	}
	$texte_infobulle.="</div>";


	$tabdiv_infobulle[]=creer_div_infobulle('div_ajout_classe',$titre_infobulle,"",$texte_infobulle,"",25,0,'y','y','n','n');

	echo "<p><a href=\"javascript:afficher_div('div_ajout_classe','y',100,100);\">Ajouter des classes</a></p><div id='div_classes' style='margin-left:3em;'></div>";

	echo "
			<p><input type='submit' value='Valider' /></p>
		</fieldset>
	</form>";

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++

echo "
<script type='text/javascript'>
	function masquage_deja() {
	$chaine_js_classes_deja
	$chaine_js_salles_deja
	}
	// Les items à masquer sont dans des infobulles qui ne seront chargées que dans le footer
	setTimeout('masquage_deja()', 3000);

	function ajouter_date_ev() {
		//alert('plip');

		//document.getElementById('div_dates').innerHTML+=document.getElementById('date_ev').value+' '+document.getElementById('heure_ev').value+':00<br />';

		// Il faudrait tester le format du jour
		date_ev=document.getElementById('date_ev').value;
		jour=date_ev.substr(0,2);
		mois=date_ev.substr(3,2);
		annee=date_ev.substr(6,4);

		// Il faudrait tester le format de l'heure
		heure_ev=document.getElementById('heure_ev').value;

		tmp_date=new Date().getTime();
		//alert(tmp_date);
		ts=Math.floor(tmp_date/1000);
		//alert(ts);

		document.getElementById('div_dates').innerHTML+=\"<p id='p_\"+ts+\"'>\"+date_ev+\" \"+heure_ev+\":00<input type='hidden' name='date_heure_ev[]' value='\"+annee+\"-\"+mois+\"-\"+jour+\" \"+heure_ev+\":00' /><a href=\\\"javascript:removeElement('p_\"+ts+\"')\\\" title='Supprimer cette date/heure'><img src='../images/icons/remove.png' class='icone16' alt='Supprimer' /></a></p>\";

	}

	function ajouter_salle_ev(id_salle) {
		document.getElementById('div_lieux').innerHTML+=\"<p id='p_salle_\"+id_salle+\"'>\"+document.getElementById('lien_ajout_salle_'+id_salle).innerHTML+\"<input type='hidden' name='id_salle_ev[]' value='\"+id_salle+\"' /><a href=\\\"javascript:enlever_salle_ev(\"+id_salle+\")\\\" title='Supprimer cette salle'><img src='../images/icons/remove.png' class='icone16' alt='Supprimer' /></a></p>\";

		document.getElementById('p_lien_ajout_salle_'+id_salle).style.display='none';
	}

	function enlever_salle_ev(id_salle) {
		document.getElementById('p_lien_ajout_salle_'+id_salle).style.display='';
		removeElement('p_salle_'+id_salle);
	}

	function ajouter_classe_ev(id_classe) {
		document.getElementById('div_classes').innerHTML+=\"<p id='p_classe_\"+id_classe+\"'>\"+document.getElementById('lien_ajout_classe_'+id_classe).innerHTML+\"<input type='hidden' name='id_classe_ev[]' value='\"+id_classe+\"' /><a href=\\\"javascript:enlever_classe_ev(\"+id_classe+\")\\\" title='Supprimer cette classe'><img src='../images/icons/remove.png' class='icone16' alt='Supprimer' /></a></p>\";

		document.getElementById('p_lien_ajout_classe_'+id_classe).style.display='none';
	}

	function enlever_classe_ev(id_classe) {
		document.getElementById('p_lien_ajout_classe_'+id_classe).style.display='';
		removeElement('p_classe_'+id_classe);
	}

	function removeElement(id) {
		element = document.getElementById(id);
		element.parentNode.removeChild(element);
	}

</script>";

}
elseif((isset($mode))&&($mode=="positionner")) {

	echo "<p class='bold'>Rappel des données saisies pour l'événement n°$id_ev&nbsp;:</p>
<div style='margin-left:3em;'>".affiche_details_evenement($id_ev, "y")."
	<p><a href='".$_SERVER['PHP_SELF']."?id_ev=$id_ev&amp;mode=modif_ev'>Modifier la date de début, les destinataires</a><br />
		<a href='".$_SERVER['PHP_SELF']."?id_ev=$id_ev&amp;mode=ajouts'>Ajouter des dates, lieux et classes</a><br />
	</p>
</div>
<p class='bold' style='margin-top:1em;'>Cliquez sur les icones <img src='../images/icons/add.png' class='icone16' alt='Ajouter' /> dans le tableau ci-dessous pour placer les classes.</p>
<div style='margin-left:3em;'>";

	//===========================================
	$temoin_au_moins_un_enregistrement=0;
	$date_heure_ev=isset($_POST['date_heure_ev']) ? $_POST['date_heure_ev'] : array();
	$sql="SELECT DISTINCT d.date_evenement FROM d_dates_evenements_classes d WHERE id_ev='$id_ev' ORDER BY date_evenement;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if(!in_array($lig->date_evenement, $date_heure_ev)) {
				$date_heure_ev[]=$lig->date_evenement;
			}
			$temoin_au_moins_un_enregistrement++;
		}
	}
	if(count($date_heure_ev)==0) {
		echo "<p style='color:red'>Aucune date n'a été choisie.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	//===========================================
	$tab_nom_classe_deja=array();
	$id_classe_ev=isset($_POST['id_classe_ev']) ? $_POST['id_classe_ev'] : array();
	$sql="SELECT DISTINCT d.id_classe, c.classe FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND c.id=d.id_classe;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if(!in_array($lig->id_classe, $id_classe_ev)) {
				$id_classe_ev[]=$lig->id_classe;
			}
			$tab_nom_classe_deja[$lig->id_classe]=$lig->classe;
			$temoin_au_moins_un_enregistrement++;
		}
	}
	if(count($id_classe_ev)==0) {
		echo "<p style='color:red'>Aucune classe n'a été choisie.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	/*
	echo "\$tab_nom_classe_deja<pre>";
	print_r($tab_nom_classe_deja);
	echo "</pre>";
	*/
	//===========================================
	$id_salle_ev=isset($_POST['id_salle_ev']) ? $_POST['id_salle_ev'] : array();
	$sql="SELECT DISTINCT d.id_salle, sc.nom_salle, sc.numero_salle FROM d_dates_evenements_classes d, salle_cours sc WHERE id_ev='$id_ev' AND sc.id_salle=d.id_salle ORDER BY sc.nom_salle, sc.numero_salle;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if(!in_array($lig->id_salle, $id_salle_ev)) {
				$id_salle_ev[]=$lig->id_salle;
			}
		}
	}
	elseif($temoin_au_moins_un_enregistrement>0) {
		$id_salle_ev[]=0;
	}
	if(count($id_salle_ev)==0) {
		echo "<p style='color:red'>Aucun lieu n'a été choisi.</p>";
		$id_salle_ev[0]="";
	}
	//===========================================

	//===========================================
	$tab_deja=array();
	$sql="SELECT * FROM d_dates_evenements_classes d WHERE id_ev='$id_ev';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$ts=mysql_date_to_unix_timestamp($lig->date_evenement);
			$tab_deja[$ts][$lig->id_salle][]=$lig->id_classe;
		}
	}
	/*
	echo "<pre>";
	print_r($tab_deja);
	echo "</pre>";
	*/
	//===========================================

	$tab_salles=get_tab_salle_cours();

	//===========================================
	$titre_infobulle="Placer une classe";
	$texte_infobulle="<p>Choisissez la ou les classes à placer</p>
<form>
<input type='hidden' name='id_salle_ev' id='id_salle_ev' value='' />
<input type='hidden' name='date_heure_ev' id='date_heure_ev' value='' />";

	$texte_infobulle.="<div class='div_3_colonnes'>";
	for($loop=0;$loop<count($id_classe_ev);$loop++) {
		$current_classe=get_nom_classe($id_classe_ev[$loop]);
		$texte_infobulle.="<p id='p_lien_ajout_classe_".$id_classe_ev[$loop]."'><a href=\"javascript:placer_classe_ev(".$id_classe_ev[$loop].")\" id='lien_ajout_classe_".$id_classe_ev[$loop]."'>".$current_classe."</a></p>";
	}
	$texte_infobulle.="</div>
</form>";

	$tabdiv_infobulle[]=creer_div_infobulle('div_ajout_classe',$titre_infobulle,"",$texte_infobulle,"",25,0,'y','y','n','n');
	//===========================================

	$tab_ts=array();
	for($loop=0;$loop<count($date_heure_ev);$loop++) {
		$ts=mysql_date_to_unix_timestamp($date_heure_ev[$loop]);
		$tab_ts[$ts]=$date_heure_ev[$loop];
	}
	ksort($tab_ts);

	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>
".add_token_field()."
<input type='hidden' name='id_ev' value='$id_ev' />
<input type='hidden' name='mode' value='enregistrer' />
<table class='boireaus boireaus_alt'>
	<thead>
		<tr>
			<th></th>";
	foreach($tab_ts as $ts => $mysql_date) {
		echo "
			<th>".strftime("%a %d/%m/%Y à %H:%M", $ts)."</th>";

	}
		echo "
		</tr>
	</thead>
	<tbody>";

	$chaine_js_classes_deja="";
	for($loop=0;$loop<count($id_salle_ev);$loop++) {
		$lieu="";
		if(($id_salle_ev[$loop]!="")&&(isset($tab_salles['indice'][$id_salle_ev[$loop]]['designation_complete']))) {
			$lieu=$tab_salles['indice'][$id_salle_ev[$loop]]['designation_complete'];
		}

		$current_id_salle=$id_salle_ev[$loop];
		if($id_salle_ev[$loop]=="") {
			$current_id_salle=0;
		}

		echo "
		<tr>
			<th>".$lieu."</th>";
		foreach($tab_ts as $ts => $mysql_date) {
			echo "
			<td>
				<div style='float:right; width:16px;'>
					<a href=\"javascript:afficher_div_placer_classe_ev($ts, $current_id_salle);\" title=\"Choisir des classes pour ce créneau.\"><img src='../images/icons/add.png' class='icone16' alt='Ajouter' /></a>
				</div>
				<div id='div_".$ts."_".$current_id_salle."'>";

			if(isset($tab_deja[$ts][$current_id_salle])) {
				for($loop2=0;$loop2<count($tab_deja[$ts][$current_id_salle]);$loop2++) {
					$current_id_classe=$tab_deja[$ts][$current_id_salle][$loop2];
					echo "
					<p id='p_classe_".$current_id_classe."_".$ts."_".$current_id_salle."'><input type='hidden' name='reg_id_classe_ev[]' value='".$current_id_classe."|".$ts."|".$current_id_salle."' />".$tab_nom_classe_deja[$current_id_classe]."<a href=\"javascript:enlever_classe_ev($current_id_classe,$ts,$current_id_salle)\" title='Supprimer cette classe'><img src='../images/icons/remove.png' class='icone16' alt='Supprimer' /></a></p>";
					$chaine_js_classes_deja.="document.getElementById('p_lien_ajout_classe_".$current_id_classe."').style.display='none';\n";
				}
			}

			echo "
				</div>
			</td>";
		}
		echo "
		</tr>";
	}

	echo "
	</tbody>
</table>
<p><input type='submit' value='Enregistrer' /></p>
<p style='margin-top:1em;'><em>NOTE&nbsp;:</em> Seuls les dates et lieux associés à une classe seront conservés lors de l'enregistrement.</p>
</div>";

	// Pour reproposer les choix après validation:
	for($loop=0;$loop<count($id_classe_ev);$loop++) {
		echo "
	<input type='hidden' name='id_classe_ev[]' value='".$id_classe_ev[$loop]."' />";
	}
	for($loop=0;$loop<count($date_heure_ev);$loop++) {
		echo "
	<input type='hidden' name='date_heure_ev[]' value='".$date_heure_ev[$loop]."' />";
	}
	for($loop=0;$loop<count($id_salle_ev);$loop++) {
		echo "
	<input type='hidden' name='id_salle_ev[]' value='".$id_salle_ev[$loop]."' />";
	}

	echo "
</form>

<script type='text/javascript'>
	function masquage_deja() {
	$chaine_js_classes_deja
	}
	// Les items à masquer sont dans des infobulles qui ne seront chargées que dans le footer
	setTimeout('masquage_deja()', 3000);


	function afficher_div_placer_classe_ev(date_heure_ev, id_salle_ev) {
		document.getElementById('id_salle_ev').value=id_salle_ev;
		document.getElementById('date_heure_ev').value=date_heure_ev;

		afficher_div('div_ajout_classe','y',100,100);
	}

	function placer_classe_ev(id_classe) {
		id_salle_ev=document.getElementById('id_salle_ev').value;
		date_heure_ev=document.getElementById('date_heure_ev').value;

		document.getElementById('div_'+date_heure_ev+'_'+id_salle_ev).innerHTML+=\"<p id='p_classe_\"+id_classe+\"_\"+date_heure_ev+\"_\"+id_salle_ev+\"'>\"+\"<input type='hidden' name='reg_id_classe_ev[]' value='\"+id_classe+\"|\"+date_heure_ev+\"|\"+id_salle_ev+\"' />\"+document.getElementById('lien_ajout_classe_'+id_classe).innerHTML+\"<a href=\\\"javascript:enlever_classe_ev(\"+id_classe+\",\"+date_heure_ev+\",\"+id_salle_ev+\")\\\" title='Supprimer cette classe'><img src='../images/icons/remove.png' class='icone16' alt='Supprimer' /></a></p>\";

		document.getElementById('p_lien_ajout_classe_'+id_classe).style.display='none';
	}

	function enlever_classe_ev(id_classe, date_heure_ev, id_salle_ev) {
		document.getElementById('p_lien_ajout_classe_'+id_classe).style.display='';
		removeElement('p_classe_'+id_classe+'_'+date_heure_ev+'_'+id_salle_ev);
	}

	function removeElement(id) {
		element = document.getElementById(id);
		element.parentNode.removeChild(element);
	}

</script>";


}
else {
	// Rappel des choix effectués:
	echo affiche_details_evenement($id_ev, "y");

	// Liens d'ajouts
	echo "<p><a href='".$_SERVER['PHP_SELF']."?id_ev=$id_ev&amp;mode=modif_ev'>Modifier la date de début, les destinataires</a><br />
	<a href='".$_SERVER['PHP_SELF']."?id_ev=$id_ev&amp;mode=ajouts'>Ajouter des dates, lieux et classes</a><br />
	<a href='".$_SERVER['PHP_SELF']."?id_ev=$id_ev&amp;mode=positionner'>Positionner les classes sur les dates et lieux.</a>
	</p>";

}

require("../lib/footer.inc.php");
?>
