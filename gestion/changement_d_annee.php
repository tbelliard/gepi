<?php
/*
*
* Copyright 2001-2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/gestion/changement_d_annee.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/gestion/changement_d_annee.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Changement d\'année.',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg = '';

if(isset($_GET['suppr_comptes_resp_en_reserve_et_collision_eleve'])) {
	check_token();

	$sql="DELETE FROM tempo_utilisateurs WHERE statut='responsable' AND login IN (SELECT login FROM eleves);";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if($res) {
		$msg.="Ménage effectué.<br />Refaites maintenant une mise en réserve des comptes élèves<br />";
	}
	else {
		$msg.="Erreur lors de la suppression de logins responsables mis en réserve.<br />";
	}
}

if(isset($_GET['suppr_comptes_ele_en_reserve_et_collision_resp'])) {
	check_token();

	$sql="DELETE FROM tempo_utilisateurs WHERE statut='eleve' AND login IN (SELECT u.login FROM utilisateurs u, resp_pers rp WHERE u.statut='responsable' AND u.login=rp.login);";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if($res) {
		$msg.="Ménage effectué.<br />Refaites maintenant une mise en réserve des comptes responsables<br />";
	}
	else {
		$msg.="Erreur lors de la suppression de logins élèves mis en réserve.<br />";
	}
}

if (isset($_POST['is_posted'])) {
	if ($_POST['is_posted']=='1') {
		check_token();

		$temoin_changement=0;
		if (isset($_POST['gepiYear'])) {
			if($_POST['gepiYear']!=getSettingValue("gepiYear")) {
				$temoin_changement++;
			}
			if (!saveSetting("gepiYear", $_POST['gepiYear'])) {
				$msg .= "Erreur lors de l'enregistrement de l'année scolaire !";
			}
			else {
				$msg .= "Enregistrement de l'année scolaire effectué.<br />";
			}
		}

		if (isset($_POST['begin_day']) and isset($_POST['begin_month']) and isset($_POST['begin_year'])) {
			$begin_bookings = mktime(0,0,0,$_POST['begin_month'],$_POST['begin_day'],$_POST['begin_year']);
			if($begin_bookings!=getSettingValue("begin_bookings")) {
				$temoin_changement++;
			}
			if (!saveSetting("begin_bookings", $begin_bookings)) {
				$msg .= "Erreur lors de l'enregistrement de begin_bookings !";
			}
			else {
				$msg .= "Enregistrement de begin_bookings effectué.<br />";
			}
		}
		if (isset($_POST['end_day']) and isset($_POST['end_month']) and isset($_POST['end_year'])) {
			$end_bookings = mktime(0,0,0,$_POST['end_month'],$_POST['end_day'],$_POST['end_year']);
			if($end_bookings!=getSettingValue("end_bookings")) {
				$temoin_changement++;
			}
			if (!saveSetting("end_bookings", $end_bookings)) {
					$msg .= "Erreur lors de l'enregistrement de end_bookings !";
			}
			else {
				$msg .= "Enregistrement de end_bookings effectué.<br />";
			}
		}

		if($temoin_changement>0) {
			$info_action_titre="Dates des vacances et jours fériés";
			$info_action_texte="L'année scolaire a changé.<br />Pensez à mettre à jour les dates de vacances <a href='edt/import_vacances_ics.php'>Importer les dates depuis education.gouv.fr</a>.";
			$info_action_destinataire=array("administrateur");
			$info_action_mode="statut";
			enregistre_infos_actions($info_action_titre,$info_action_texte,$info_action_destinataire,$info_action_mode);
		}

		// 20150810
		$maj_dates_per=isset($_POST['maj_dates_per']) ? $_POST['maj_dates_per'] : array();
		if(count($maj_dates_per)>0) {
			$nb_update_per=0;
			// Il faudrait vérifier que les dates sont cohérentes
			$ts_prec=0;
			for($loop=0;$loop<count($maj_dates_per);$loop++) {
				$debut_per=isset($_POST["debut_per_".$maj_dates_per[$loop]]) ? $_POST["debut_per_".$maj_dates_per[$loop]] : NULL;
				$fin_per=isset($_POST["fin_per_".$maj_dates_per[$loop]]) ? $_POST["fin_per_".$maj_dates_per[$loop]] : NULL;

				$sql="SELECT * FROM edt_calendrier WHERE id_calendrier='".$maj_dates_per[$loop]."';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)==0) {
					$msg.="La période dont l'identifiant id_calendrier est n°".$maj_dates_per[$loop]." n'existe pas.<br />";
				}
				else {
					$lig_cal=mysqli_fetch_object($res);
	
					unset($ts_debut);
					unset($ts_fin);

					if(!preg_match("#[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}#", $debut_per)) {
						$msg.="La date de début de période $debut_per est invalide.<br />";
					}
					else {
						$tmp_tab=explode("/", $debut_per);
						$jour_debut=$tmp_tab[0];
						$mois_debut=$tmp_tab[1];
						$annee_debut=$tmp_tab[2];

						if(!checkdate($mois_debut,$jour_debut,$annee_debut)) {
							$msg.="La date de début de période $debut_per est invalide.<br />";
						}
						else {
							$ts_debut=gmmktime(0, 0, 0, $mois_debut, $jour_debut, $annee_debut);
							if(!$ts_debut) {
								$msg.="La date de début de période $debut_per est invalide.<br />";
							}
						}
					}

					if(!preg_match("#[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}#", $fin_per)) {
						$msg.="La date de fin de période $fin_per est invalide.<br />";
					}
					else {
						$tmp_tab=explode("/", $fin_per);
						$jour_fin=$tmp_tab[0];
						$mois_fin=$tmp_tab[1];
						$annee_fin=$tmp_tab[2];

						if(!checkdate($mois_fin,$jour_fin,$annee_fin)) {
							$msg.="La date de fin de période $fin_per est invalide.<br />";
						}
						else {
							$ts_fin=gmmktime(23, 59, 0, $mois_fin, $jour_fin, $annee_fin);
							if(!$ts_fin) {
								$msg.="La date de fin de période $fin_per est invalide.<br />";
							}
						}
					}

					if((isset($ts_debut))&&(isset($ts_fin))) {
						// Ca ne convient pas: Les timestamp dans edt_calendrier sont en GMT
						//$sql="UPDATE edt_calendrier SET debut_calendrier_ts='$ts_debut', fin_calendrier_ts='$ts_fin', jourdebut_calendrier='".strftime("%Y-%m-%d", $ts_debut)."', heuredebut_calendrier='00:00:00', jourfin_calendrier='".strftime("%Y-%m-%d", $ts_fin)."', heurefin_calendrier='23:59:00' WHERE id_calendrier='".$maj_dates_per[$loop]."';";
						//$sql="UPDATE edt_calendrier SET debut_calendrier_ts='$ts_debut', fin_calendrier_ts='$ts_fin', jourdebut_calendrier='".$annee_debut."-".$mois_debut."-".$jour_debut."', heuredebut_calendrier='00:00:00', jourfin_calendrier='".$annee_fin."-".$mois_fin."-".$jour_fin."', heurefin_calendrier='23:59:00' WHERE id_calendrier='".$maj_dates_per[$loop]."';";
						$sql="UPDATE edt_calendrier SET debut_calendrier_ts='$ts_debut', fin_calendrier_ts='$ts_fin', jourdebut_calendrier='".gmstrftime("%Y-%m-%d", $ts_debut)."', heuredebut_calendrier='00:00:00', jourfin_calendrier='".gmstrftime("%Y-%m-%d", $ts_fin)."', heurefin_calendrier='23:59:00' WHERE id_calendrier='".$maj_dates_per[$loop]."';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if($update) {

							$tmp_tab=explode(";", $lig_cal->classe_concerne_calendrier);
							for($loop2=0;$loop2<count($tmp_tab);$loop2++) {
								if($tmp_tab[$loop2]!="") {
									//$sql="UPDATE periodes SET date_fin='".strftime("%Y-%m-%d", $ts_fin+3600*2)." 00:00:00' WHERE id_classe='".$tmp_tab[$loop2]."' AND num_periode='".$lig_cal->numero_periode."'";
									$sql="UPDATE periodes SET date_fin='".gmstrftime("%Y-%m-%d", $ts_fin+3600*2)." 00:00:00' WHERE id_classe='".$tmp_tab[$loop2]."' AND num_periode='".$lig_cal->numero_periode."'";
									//echo "$sql<br />";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$update) {
										$msg.="Erreur lors de la mise à jour de la date de fin pour la classe ".get_nom_classe($tmp_tab[$loop2])."<br />";
									}
								}
							}

							$nb_update_per++;
						}
						else {
							$msg.="Erreur lors de la mise à jour des dates de début et fin de la période ".$lig_cal->nom_calendrier.".<br />";
						}
					}
				}
			}
			if($nb_update_per>0) {
				$msg.=$nb_update_per." périodes mises à jour.<br />";
			}
		}

		if((isset($_POST['reserve_comptes_eleves']))&&($_POST['reserve_comptes_eleves']=='y')) {
			$sql="DELETE FROM tempo_utilisateurs WHERE statut='eleve';";
			//echo "<span style='color:green;'>$sql</span><br />";
			$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);

			$sql="INSERT INTO tempo_utilisateurs SELECT u.login,u.password,u.salt,u.email,e.ele_id,e.elenoet,u.nom,u.prenom,u.statut,u.auth_mode,NOW(),u.statut FROM utilisateurs u, eleves e WHERE u.login=e.login AND u.statut='eleve';";
			//echo "<span style='color:green;'>$sql</span><br />";
			$svg_insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if($svg_insert) {
				$msg.="Mise en réserve des comptes élèves effectuée.<br />";
			}
			else {
				$msg.="Erreur lors de la mise en réserve des comptes élèves.<br />";

				$sql="SELECT * FROM tempo_utilisateurs WHERE statut='responsable' AND login IN (SELECT login FROM eleves);";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					$msg.="Anomalie&nbsp;: Un ou des comptes responsables ont été mis en réserve avec un login correspondant à un compte élève.<br />Liste des comptes&nbsp;: ";
					$cpt=0;
					while($lig=mysqli_fetch_object($res)) {
						if($cpt>0) {$msg.=", ";}
						$msg.=$lig->login;
						$tmp_tab=get_info_responsable($lig->login);
						if(count($tmp_tab)>0) {$msg.=" (<em><a href='../responsables/modify_resp.php?pers_id=".$tmp_tab['pers_id']."' target='_blank'>".$tmp_tab['nom']." ".$tmp_tab['prenom']."</a></em>)";}
						$cpt++;
					}
					$msg.="Ces comptes peuvent correspondre à une mise en réserve de l'année précédente... pour des parents dont les élèves ont quitté l'établissement.<br /><a href='".$_SERVER['PHP_SELF']."?suppr_comptes_resp_en_reserve_et_collision_eleve=y".add_token_in_url()."'>Supprimer de la mise en réserve les comptes correspondants</a><br />Vous devrez par la suite refaire une mise en réserve des comptes élèves.<br /><br />Vous pouvez aussi, plus simplement supprimer les comptes mis en réserve à l'aide des liens plus bas dans la page, et ensuite refaire la mise en réserve pour ne conserver que les comptes de cette année.<br />";
				}
			}
		}

		if((isset($_POST['reserve_comptes_responsables']))&&($_POST['reserve_comptes_responsables']=='y')) {
			$sql="DELETE FROM tempo_utilisateurs WHERE statut='responsable';";
			//echo "<span style='color:green;'>$sql</span><br />";
			$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);

			$sql="INSERT INTO tempo_utilisateurs SELECT u.login,u.password,u.salt,u.email,rp.pers_id,rp.pers_id,u.nom,u.prenom,u.statut,u.auth_mode,NOW(),u.statut FROM utilisateurs u, resp_pers rp WHERE u.login=rp.login AND u.statut='responsable';";
			//echo "<span style='color:green;'>$sql</span><br />";
			$svg_insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if($svg_insert) {
				$msg.="Mise en réserve des comptes responsables effectuée.<br />";
				// Remplacement de l'identifiant2 par la liste des elenoet associés pour le cas d'une initialisation CSV.
				$sql="SELECT identifiant1 FROM tempo_utilisateurs WHERE statut='responsable';";
				$res_t_u=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_t_u=mysqli_fetch_object($res_t_u)) {
					$sql="SELECT e.elenoet FROM eleves e, responsables2 r WHERE e.ele_id=r.ele_id AND (r.resp_legal='1' OR r.resp_legal='2') AND r.pers_id='".$lig_t_u->identifiant1."';";
					$res_elenoet=mysqli_query($GLOBALS["mysqli"], $sql);
					$chaine_elenoet="";
					while($lig_elenoet=mysqli_fetch_object($res_elenoet)) {
						$chaine_elenoet.="|".$lig_elenoet->elenoet;
					}
					if($chaine_elenoet!='') {
						$sql="UPDATE tempo_utilisateurs SET identifiant2='".$chaine_elenoet."|' WHERE identifiant1='".$lig_t_u->identifiant1."' AND statut='responsable';";
						$update_t_u=mysqli_query($GLOBALS["mysqli"], $sql);
					}
				}
			}
			else {
				$msg.="Erreur lors de la mise en réserve des comptes responsables.<br />";

				$sql="SELECT * FROM tempo_utilisateurs WHERE statut='eleve' AND login IN (SELECT u.login FROM utilisateurs u, resp_pers rp WHERE u.statut='responsable' AND u.login=rp.login);";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					$msg.="Anomalie&nbsp;: Un ou des comptes élèves ont été mis en réserve avec un login correspondant à un compte responsable.<br />Liste des comptes&nbsp;: ";
					$cpt=0;
					while($lig=mysqli_fetch_object($res)) {
						if($cpt>0) {$msg.=", ";}
						$msg.=$lig->login;
						$tmp_tab=get_info_eleve($lig->login);
						if(count($tmp_tab)>0) {$msg.=" (<em><a href='../eleves/modify_eleve.php?eleve_login=".$lig->login."' target='_blank'>".$tmp_tab['nom']." ".$tmp_tab['prenom']."</a></em>)";}
						$cpt++;
					}
					$msg.="Ces comptes peuvent correspondre à une mise en réserve de l'année précédente... pour des élèves qui ont quitté l'établissement.<br /><a href='".$_SERVER['PHP_SELF']."?suppr_comptes_ele_en_reserve_et_collision_resp=y".add_token_in_url()."'>Supprimer de la mise en réserve les comptes correspondants</a><br />Vous devrez par la suite refaire une mise en réserve des comptes responsables.<br /><br />Vous pouvez aussi, plus simplement supprimer les comptes mis en réserve à l'aide des liens plus bas dans la page, et ensuite refaire la mise en réserve pour ne conserver que les comptes de cette année.<br />";
				}
			}
		}


		$sql="SELECT 1=1 FROM preferences WHERE name LIKE 'accueil_simpl_id_groupe_order_%';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0) {
			$sql="DELETE FROM preferences WHERE name LIKE 'accueil_simpl_id_groupe_order_%';";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
			if($del) {
				$msg.="La suppression des préférences d'affichage ou non des enseignements en page d'accueil simplifiée est effectuée.<br />";
			}
			else {
				$msg.="Erreur lors de la suppression des préférences d'affichage ou non des enseignements en page d'accueil simplifiée.<br />";
			}
		}

	}
	elseif ($_POST['is_posted']=='2') {
		check_token();

		if (isset($_POST['clean_log']) and isset($_POST['log_day']) and isset($_POST['log_month']) and isset($_POST['log_year'])) {
			$msg.="Nettoyage des logs de connexion antérieurs à ".$_POST['log_day']."/".$_POST['log_month']."/".$_POST['log_year']." : ".clean_table_log($_POST['log_day']."/".$_POST['log_month']."/".$_POST['log_year'])."<br />";
		}

		if (isset($_POST['clean_tentative_intrusion']) and isset($_POST['ti_day']) and isset($_POST['ti_month']) and isset($_POST['ti_year'])) {
			$msg.="Nettoyage des logs de tentatives d'intrusion antérieurs à ".$_POST['ti_day']."/".$_POST['ti_month']."/".$_POST['ti_year']." : ".clean_table_tentative_intrusion($_POST['ti_day']."/".$_POST['ti_month']."/".$_POST['ti_year'])."<br />";
		}
	}
}

if (isset($_GET['reinit_dates_verrouillage_periode'])) {
	check_token();
	$sql="update periodes set date_verrouillage='0000-00-00 00:00:00';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if($res) {
		$msg.="Réinitialisation des dates de verrouillage de périodes effectuée.<br />";
	}
	else {
		$msg.="Erreur lors de la réinitialisation des dates de verrouillage de périodes.<br />";
	}
}

if (isset($_GET['suppr_reserve_eleve'])) {
	check_token();
	$sql="DELETE FROM tempo_utilisateurs WHERE statut='eleve';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if($res) {
		$msg.="Suppression de la réserve sur les comptes élèves effectuée.<br />";
	}
	else {
		$msg.="Erreur lors de la suppression de la réserve sur les comptes élèves.<br />";
	}
}

if (isset($_GET['suppr_reserve_resp'])) {
	check_token();
	$sql="DELETE FROM tempo_utilisateurs WHERE statut='responsable';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if($res) {
		$msg.="Suppression de la réserve sur les comptes responsables effectuée.<br />";
	}
	else {
		$msg.="Erreur lors de la suppression de la réserve sur les comptes responsables.<br />";
	}
}

// Load settings
if (!loadSettings()) {
	die("Erreur chargement settings");
}
if (isset($_POST['is_posted']) and ($msg=='')) $msg = "Les modifications ont été enregistrées !";

if(isset($_SESSION['chgt_annee'])) {
	unset($_SESSION['chgt_annee']);
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
// End standart header
$titre_page = "Changement d'année";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();
$debug_ele="n";
$debug_resp="n";

echo "<p class='bold'><a href='index.php#chgt_annee' ".insert_confirm_abandon()."><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

echo "<p>Au changement d'année, avant d'initialiser la nouvelle année scolaire, il convient d'effectuer quelques opérations.<br />Elles sont en principe détaillées (<i>peut-être même plus à jour si des ajouts y ont été apportés après la sortie de votre version de GEPI</i>) sur le <a href='https://www.sylogix.org/projects/gepi/wiki/GuideAdministrateur' target='_blank'>Wiki</a>.</p>\n";

echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='form1' id='form1' style='width: 100%;'>\n";
echo "<fieldset style='border: 1px solid grey;";
echo "background-image: url(\"../images/background/opacite50.png\"); ";
echo "'>\n";
echo add_token_field();

$msg_svg="Il est recommandé de faire une copie de sauvegarde sur un périphérique externe (à stocker au coffre par exemple)";
$lien_svg="<a href='#svg_ext' ".insert_confirm_abandon()."><img src='../images/icons/ico_ampoule.png' width='15' height='25' title='$msg_svg' alt='$msg_svg' /></a>";

echo "<p>Les points sont les suivants&nbsp;:</p>\n";
echo "<p>La partie archivage de fin d'année&nbsp;:</p>\n";
echo "<ol>\n";
echo "<li><p><a href='accueil_sauve.php?chgt_annee=y'>Sauvegarder la base</a> $lien_svg</p></li>\n";
if((getSettingAOui('active_cahiers_texte'))||(getSettingAOui('acces_cdt_prof'))) {
	echo "<li><p>Eventuellement, faire un <a href='../cahier_texte_2/export_cdt.php?chgt_annee=y'>export des cahiers de textes</a><br />et une <a href='accueil_sauve.php?chgt_annee=y#zip'>sauvegarde des documents du Cahier de textes</a> $lien_svg</p></li>\n";
	echo "<li><p><a href='../cahier_texte_2/archivage_cdt.php?chgt_annee=y'>Archiver les cahiers de textes</a> pour permettre aux professeurs une consultation de leurs CDT passés.</p></li>\n";
	echo "<li><p>Si l'archivage des CDT est fait, vous pouvez aussi <a href='../cahier_texte_admin/suppr_docs_joints_cdt.php?chgt_annee=y'>supprimer les documents joints aux cahiers de textes</a> de l'année qui se termine.</p></li>\n";
}
echo "<li><p>Sauvegarder l'arborescence Gepi (<em>par ftp, sftp,...</em>) $lien_svg</p></li>\n";
if(my_strtolower(mb_substr(getSettingValue('active_annees_anterieures'),0,1))=='y') {
	echo "<li><p>Conserver les données de l'année passée via le <a href='../mod_annees_anterieures/conservation_annee_anterieure.php?chgt_annee=y'>module Années antérieures</a>.</p></li>\n";
	echo "<li><p>Éventuellement, générer, pour chaque élève, un bulletin PDF des N périodes via le <a href='../mod_annees_anterieures/archivage_bull_pdf.php?chgt_annee=y'>module Années antérieures</a>.</p></li>\n";
}
else {
	echo "<li><p>Conserver les données de l'année passée via le <strong>module Années antérieures</strong>.<br /><span style='color:red'>Le module est inactif</span>.<br />Vous devriez <a href='../mod_annees_anterieures/admin.php' target='_blank'>activer le module</a>, quitte à limiter la liste des statuts d'utilisateurs autorisés à accéder à ces informations via la page <a href='../gestion/droits_acces.php' target='_blank'>Droits d'accès</a>.</p></li>\n";
	echo "<li><p>Éventuellement, une fois le module activé (<em>et après rafraichissement de la page</em>) générer, pour chaque élève, un bulletin PDF des N périodes via le module Années antérieures.</p></li>\n";
}
if(file_exists("../mod_plugins/archivageAPB/index.php")) {
	echo "<li><a href='../mod_plugins/archivageAPB/index.php'>Archiver les données de l'année qui se termine pour le plugin APB</a>.</li>\n";
}
if(getSettingValue('active_module_absence')=='2') {
	echo "<li><p><a href='../mod_abs2/extraction_saisies.php?date_absence_eleve_debut=".(date('Y')-1)."-08-01&date_absence_eleve_fin=".date('Y')."-08-01&type_extrait=1&retour=../gestion/changement_d_annee.php'>Effectuer une extraction CSV des absences</a>,\n";
	echo " puis <a onclick=\"return(confirm('Voulez vous vider les tables d\'absences ?'));\" href='../utilitaires/clean_tables.php?action=clean_absences&amp;date_limite=31/07/".date('Y')."&amp;chgt_annee=y".add_token_in_url()."'/>purger les tables absences pour les absences antérieures au 31/07/".date('Y')."</a></p></li>";
}
if((getSettingAOui('active_mod_discipline'))&&(nombre_de_dossiers_docs_joints_a_des_sanctions()>0)) {
	echo "<li><a href='../mod_discipline/discipline_admin.php?chgt_annee=y#suppr_docs_joints'>Supprimer les dossier(s) de documents joints à des ".getSettingValue('mod_disc_terme_sanction')."s</a>.</li>\n";
}
echo "</ol>\n";

echo "<p>La partie concernant la nouvelle année&nbsp;:</p>\n";
echo "<ol>\n";
echo "<li style='margin-top:1em;'><p>Modifier l'année scolaire&nbsp; (<em>actuellement ".getSettingValue('gepiYear')."</em>) : <input type='text' name='gepiYear' size='20' value='".date('Y')."/".(date('Y')+1)."' onchange='changement()' /></li>\n";
echo "<li style='margin-top:1em;'><p>Modifier les dates de début et de fin des cahiers de textes&nbsp;:<br />";
?>

<table>
	<tr>
		<td>
		Date de début de d'année scolaire <em>(utilisée notamment dans les cahiers de textes)</em> <em>(actuellement <?php echo strftime("%d/%m/%Y", getSettingValue("begin_bookings")); ?>)</em>&nbsp;:
		</td>
		<td><?php
		$bday = strftime("%d", getSettingValue("begin_bookings"));
		$bmonth = strftime("%m", getSettingValue("begin_bookings"));
		$byear = date('Y');
		genDateSelector("begin_", $bday, $bmonth, $byear,"more_years");
		?>
		</td>
	</tr>
	<tr>
		<td>
		Date de fin d'année scolaire <em>(utilisée notamment dans les cahiers de textes)</em> <em>(actuellement <?php echo strftime("%d/%m/%Y", getSettingValue("end_bookings")); ?>)</em>&nbsp;:
		</td>
		<td><?php
		$eday = strftime("%d", getSettingValue("end_bookings"));
		$emonth = strftime("%m", getSettingValue("end_bookings"));
		$eyear = date('Y')+1;
		genDateSelector("end_",$eday,$emonth,$eyear,"more_years");
		?>
		</td>
	</tr>
</table>

<?php
echo "</li>\n";

// 20150810
$sql="SELECT * FROM edt_calendrier WHERE numero_periode!='0' AND etabferme_calendrier='1' ORDER BY numero_periode;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
$cpt_per=0;
if(mysqli_num_rows($res)>0) {
	echo "<li style='margin-top:1em;'><p>Vous pouvez définir les dates de début et fin de périodes&nbsp;:<br />
	<a href='javascript:ajout_un_an_dates_per()'>Ajouter un an aux dates et fins de périodes ci-dessous</a></p>
	<ul>\n";
	while($lig=mysqli_fetch_object($res)) {
		echo "
		<li>
			<p style='text-indent:-3em;margin-left:3em;'>
				<input type='checkbox' name='maj_dates_per[]' id='maj_dates_per_".$cpt_per."' value='".$lig->id_calendrier."' onchange=\"checkbox_change(this.id)\" /><label for='maj_dates_per_".$cpt_per."' id='texte_maj_dates_per_".$cpt_per."'> ".$lig->nom_calendrier."</label>&nbsp;:<br />
				Début&nbsp;:<input type='text' name='debut_per_".$lig->id_calendrier."' id='debut_per_".$cpt_per."' value='".gmstrftime("%d/%m/%Y", $lig->debut_calendrier_ts)."' onchange=\"document.getElementById('maj_dates_per_".$cpt_per."').checked=true;checkbox_change('maj_dates_per_".$cpt_per."');\" size='8' onkeydown=\"clavier_date_plus_moins(this.id,event);document.getElementById('maj_dates_per_".$cpt_per."').checked=true;checkbox_change('maj_dates_per_".$cpt_per."');\" AutoComplete='off' /> à 00h00 ".img_calendrier_js("debut_per_".$cpt_per, "img_bouton_debut_per_".$cpt_per)."<br />
				Fin&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:<input type='text' name='fin_per_".$lig->id_calendrier."' id='fin_per_".$cpt_per."' value='".gmstrftime("%d/%m/%Y", $lig->fin_calendrier_ts)."' onchange=\"document.getElementById('maj_dates_per_".$cpt_per."').checked=true;checkbox_change('maj_dates_per_".$cpt_per."');\" size='8' onkeydown=\"clavier_date_plus_moins(this.id,event);document.getElementById('maj_dates_per_".$cpt_per."').checked=true;checkbox_change('maj_dates_per_".$cpt_per."');\" AutoComplete='off' /> à 23h59 ".img_calendrier_js("fin_per_".$cpt_per, "img_bouton_fin_per_".$cpt_per)."
			</p>
			<p>
				".strftime("%Y-%m-%d %H:%M:%S", $lig->debut_calendrier_ts)." à 
				".strftime("%Y-%m-%d %H:%M:%S", $lig->fin_calendrier_ts)."
			</p>
		</li>";
		$cpt_per++;
	}
	echo "
	</ul>
	<p style='margin-top:1em;'>NOTES&nbsp;:</p>
	<ul>
		<li>Vous pourrez modifier ces dates par la suite s'il faut affiner ou corriger.</li>
		<li>Seules les périodes cochées verront leurs dates de début et fin modifiées.</li>
	</ul>
</li>\n";
}

echo "<li style='margin-top:1em;'>\n";

echo "<script type='text/javascript'>
	/*
	function maj_dates_periodes() {
		for(i=0;i<$cpt_per;i++) {

		}
	}
	*/

	function ajout_un_an_dates_per() {
		for(i=0;i<$cpt_per;i++) {
			if(document.getElementById('maj_dates_per_'+i)) {
				document.getElementById('maj_dates_per_'+i).checked=true;
				checkbox_change('maj_dates_per_'+i);
			}
			if(document.getElementById('debut_per_'+i)) {
				tmp_date=document.getElementById('debut_per_'+i).value;
				tab=tmp_date.split('/');
				document.getElementById('debut_per_'+i).value=tab[0]+'/'+tab[1]+'/'+eval(eval(tab[2])+1);
			}
			if(document.getElementById('fin_per_'+i)) {
				tmp_date=document.getElementById('fin_per_'+i).value;
				tab=tmp_date.split('/');
				document.getElementById('fin_per_'+i).value=tab[0]+'/'+tab[1]+'/'+eval(eval(tab[2])+1);
			}
		}
	}

	".js_checkbox_change_style()."
</script>";


// Sauvegarde temporaire:
$sql="CREATE TABLE IF NOT EXISTS tempo_utilisateurs
(login VARCHAR( 50 ) NOT NULL PRIMARY KEY,
password VARCHAR(128) NOT NULL,
salt VARCHAR(128) NOT NULL,
email VARCHAR(50) NOT NULL,
identifiant1 VARCHAR( 10 ) NOT NULL ,
identifiant2 VARCHAR( 50 ) NOT NULL ,
nom VARCHAR( 50 ) NOT NULL ,
prenom VARCHAR( 50 ) NOT NULL ,
statut VARCHAR( 20 ) NOT NULL ,
auth_mode ENUM('gepi','ldap','sso') NOT NULL default 'gepi',
date_reserve DATE DEFAULT '0000-00-00',
temoin VARCHAR( 50 ) NOT NULL
);";
$creation_table=mysqli_query($GLOBALS["mysqli"], $sql);

echo "<p>Pour pouvoir imposer les mêmes comptes parents et/ou élèves d'une année sur l'autre (<em>pour se connecter dans Gepi, consulter les cahiers de textes, les notes,...</em>), il convient avant d'initialiser la nouvelle année (<em>opération qui vide/nettoye un certain nombre de tables</em>) de mettre en réserve dans une table temporaire les login, mot de passe, email et statut des parents/élèves de façon à leur redonner le même login et restaurer l'accès lors de l'initialisation.</p>\n";

echo "<p>";
$sql="SELECT 1=1 FROM utilisateurs WHERE statut='eleve';";
if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)>0) {
	echo "Il existe actuellement ".mysqli_num_rows($test)." comptes élèves.<br />";
	$temoin_compte_ele="y";
}
else {
	echo "Il n'existe actuellement aucun compte élève.<br />";
	$temoin_compte_ele="n";
}
$sql="SELECT 1=1 FROM tempo_utilisateurs WHERE statut='eleve';";
if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)>0) {
	echo mysqli_num_rows($test)." comptes élèves sont actuellement mis en réserve";
	$sql="SELECT DISTINCT date_reserve FROM tempo_utilisateurs WHERE statut='eleve' ORDER BY date_reserve;";
	if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		echo " (<em>date de mise en réserve&nbsp;: ";
		$cpt=0;
		while($lig_res=mysqli_fetch_object($test)) {
			if($cpt>0) {echo ", ";}
			echo formate_date($lig_res->date_reserve);
			$cpt++;
		}
		echo "</em>)";
	}
	echo " - <a href='".$_SERVER['PHP_SELF']."?suppr_reserve_eleve=y".add_token_in_url()."' title=\"Cela supprime de la table 'tempo_utilisateurs', les comptes élèves. Cela ne supprime pas les comptes élèves actuellement enregistrés dans la table 'utilisateurs'. Vous pourrez donc refaire une mise en réserve des actuels comptes élèves tant que vous n'aurez pas lancé l'initialisation de la nouvelle année.\">Supprimer les comptes élèves mis en réserve</a>";
	$temoin_reserve_compte_ele="faite";
}
else {
	echo "Aucun compte élève n'est actuellement mis en réserve.<br />";
	$temoin_reserve_compte_ele="non_faite";
}
echo "</p>\n";

echo "<p>";
$sql="SELECT 1=1 FROM utilisateurs WHERE statut='responsable';";
if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)>0) {
	echo "Il existe actuellement ".mysqli_num_rows($test)." comptes responsables.<br />";
	$temoin_compte_resp="y";
}
else {
	echo "Il n'existe actuellement aucun compte responsable.<br />";
	$temoin_compte_resp="n";
}
$sql="SELECT 1=1 FROM tempo_utilisateurs WHERE statut='responsable';";
if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)>0) {
	echo mysqli_num_rows($test)." comptes responsables sont actuellement mis en réserve";
	$sql="SELECT DISTINCT date_reserve FROM tempo_utilisateurs WHERE statut='responsable' ORDER BY date_reserve;";
	if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		echo " (<em>date de mise en réserve&nbsp;: ";
		$cpt=0;
		while($lig_res=mysqli_fetch_object($test)) {
			if($cpt>0) {echo ", ";}
			echo formate_date($lig_res->date_reserve);
			$cpt++;
		}
		echo "</em>)";
	}
	echo " - <a href='".$_SERVER['PHP_SELF']."?suppr_reserve_resp=y".add_token_in_url()."' title=\"Cela supprime de la table 'tempo_utilisateurs', les comptes responsables. Cela ne supprime pas les comptes responsables actuellement enregistrés dans la table 'utilisateurs'. Vous pourrez donc refaire une mise en réserve des actuels comptes responsables tant que vous n'aurez pas lancé l'initialisation de la nouvelle année.\">Supprimer les comptes responsables mis en réserve</a>";
	$temoin_reserve_compte_resp="faite";
}
else {
	echo "Aucun compte responsable n'est actuellement mis en réserve.<br />";
	$temoin_reserve_compte_resp="non_faite";
}
echo "</p>\n";

echo "<p><input type='checkbox' name='reserve_comptes_eleves' id='reserve_comptes_eleves' value='y' ";
if(($temoin_compte_ele=='y')&&($temoin_reserve_compte_ele=='non_faite')) {echo "checked ";}
echo "/><label for='reserve_comptes_eleves'>Mettre en réserve une copie des comptes élèves.<br />
<input type='checkbox' name='reserve_comptes_responsables' id='reserve_comptes_responsables' value='y' ";
if(($temoin_compte_resp=='y')&&($temoin_reserve_compte_resp=='non_faite')) {echo "checked ";}
echo "/><label for='reserve_comptes_responsables'>Mettre en réserve une copie des comptes responsables.</label></label></p>\n";

echo "<p><em>NOTE&nbsp;:</em> En cochant les cases ci-dessus, on commence par vider les comptes précédemment mis en réserve avant d'insérer les comptes actuellement présents dans la table 'utilisateurs'.</p>\n";
echo "</li>\n";
echo "</ol>\n";

$sql="SELECT 1=1 FROM preferences WHERE name LIKE 'accueil_simpl_id_groupe_order_%';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)>0) {
	echo "<p style='margin-bottom:1em;'>Un ou des professeurs ont paramétré l'ordre d'affichage de leurs enseignements ou le non affichage de certains enseignements en page d'accueil simplifiée.<br />
	Les nouveaux enseignements créés avec l'année qui va commencer ne devraient pas avoir les mêmes identifiants (<em>id_groupe</em>), mais par précaution, ces préférences seront supprimées lors de la validation de ce formulaire.</p>";
}

echo "<input type='hidden' name='is_posted' value='1' />\n";
echo "<input type='submit' name='Valider' id='Valider' value='Valider' />\n";
echo "<input type='button' name='Valider2' id='Valider2' value='Valider' style='display:none;' onclick=\"check_et_valide_submit_form()\" />\n";
echo "</fieldset>\n";
echo "</form>
<script type='text/javascript'>
	if(document.getElementById('Valider2')) {
		document.getElementById('Valider2').style.display='';
		if(document.getElementById('Valider')) {
			document.getElementById('Valider').style.display='none';
		}
	}

	function check_et_valide_submit_form() {
		if('$cpt_per'=='0') {
			document.getElementById('form1').submit();
		}
		else {
			var m=0;
			var n=0;
			for(i=0;i<$cpt_per;i++) {
				if(document.getElementById('maj_dates_per_'+i)) {
					if(document.getElementById('maj_dates_per_'+i).checked==true) {
						n++;
					}
				}
				m++;
			}
			if(n==0) {
				var is_confirmed = confirm(\"Vous avez choisi de ne modifier aucune date de période.\\nSi il s'agit d'une erreur, répondez ANNULER ci-dessous,\\nsinon confirmez la validation sans modification des dates de périodes par un OUI.\\nValider le formulaire?\");
				if(is_confirmed){
					document.getElementById('form1').submit();
				}
			}
			else {
				if(n!=m) {
					var is_confirmed = confirm(\"Vous avez choisi de ne pas modifier certaines dates de pérides.\\nSi il s'agit d'une erreur, répondez ANNULER ci-dessous,\\nsinon confirmez la validation par un OUI.\\nValider le formulaire?\");
					if(is_confirmed){
						document.getElementById('form1').submit();
					}
				}
				else {
					document.getElementById('form1').submit();
				}
			}
		}
	}
</script>\n";

echo "<br />\n";

$lday = strftime("%d", getSettingValue("end_bookings"));
$lmonth = strftime("%m", getSettingValue("end_bookings"));
$lyear = date('Y')-1;

echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='form2' style='width: 100%;'>
	<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); '>
		".add_token_field()."
		<p>
			<em>Optionnel&nbsp;:</em> Nettoyer les tables 'log' et 'tentative_intrusion'.<br />
			Cette table contient les dates de connexion/déconnexion des utilisateurs.<br />
			Conserver ces informations au-delà d'une année n'a pas vraiment d'intérêt.<br >
			Au besoin, si vous avez pris soin d'effectuer une sauvegarde de la base, les informations y sont.
		</p>
		<p><input type='checkbox' id='clean_log' name='clean_log' value='y' checked /><label for='clean_log'>Nettoyer les logs de connexion antérieurs au</label>&nbsp;:&nbsp;";
genDateSelector("log_",$lday,$lmonth,$lyear,"more_years");
echo "<br />
			<input type='checkbox' id='clean_tentative_intrusion' name='clean_tentative_intrusion' value='y' checked /><label for='clean_tentative_intrusion'>Nettoyer les logs de tentatives d'intrusion antérieurs au</label>&nbsp;:&nbsp;";
genDateSelector("ti_",$lday,$lmonth,$lyear,"more_years");
echo "</p>
		<input type='hidden' name='is_posted' value='2' />
		<input type='submit' name='Valider' value='Valider' />

		<p><em>NOTE&nbsp;:</em> La CNIL recommande de ne pas conserver plus de 6 mois de journaux de connexion.</p>
	</fieldset>
</form>\n";

echo "<p><br /></p>\n";

echo "<p style='text-indent:-11em; margin-left:11em;'><em>Optionnel également&nbsp;:</em> Vous pouvez vider les absences de l'année passée, l'emploi du temps, les incidents/sanctions du module discipline en consultant la page de <a href='../utilitaires/clean_tables.php#nettoyage_par_le_vide'>Nettoyage de la base</a>.</p>\n";

echo "<p><br /></p>\n";

echo "<a name='svg_ext'></a>";
echo "<p><em>NOTES&nbsp;:</em></p>\n";
echo "<ul>\n";
echo "<li>\n";
echo "<p>La sauvegarde sur périphérique externe permet de remettre en place un GEPI si jamais votre GEPI en ligne subit des dégats (<em>crash du disque dur hébergeant votre GEPI, incendie du local serveur,...</em>).<br />Vous n'aurez normalement jamais besoin de ces sauvegardes, mais mieux vaut prendre des précautions.</p>\n";
echo "</li>\n";
echo "<li>\n";
echo "<p>Lors de l'initialisation de l'année, la date à laquelle une période a été close pour telle classe sera réinitialisée.<br />Ce n'était pas le cas pour une initialisation faite avant le 17/09/2012.<br />Pour forcer cette réinitialisation, <a href='".$_SERVER['PHP_SELF']."?reinit_dates_verrouillage_periode=y".add_token_in_url()."'>cliquer ici</a>.<br />Cette date de verrouillage présente un intérêt pour l'accès des responsables et élèves aux appréciations des bulletins dans le cas où vous avez choisi un accès automatique N jours après la clôture de la période.</p>\n";
if(getSettingValue("active_module_absence")=="2"){
	echo "<p>Ces dates de verrouillage, indiquant à quelle date la période de notes a été close, n'ont rien à voir avec les dates déclarées pour les fins de périodes d'absences dans la page de Verrouillage.<br />
	Les dates de fin de période affichées dans la page de Verrouillage concernent la liste des élèves qui seront présentés dans vos groupes/classes pour la saisie des absences (<em>tel élève arrivé au 2è trimestre ou ayant changé de classe,... doit ou ne doit pas apparaître sur telle période dans tel groupe/classe</em>).</p>\n";
}
echo "</li>
	<li>
		<p class='bold'>Rappel des opérations à effectuer après l'initialisation</p>
		<ul>
			<li>
				<p>Si vous avez plusieurs types de classes (<em>classes à trimestres et classes à semestre</em>), ou si vous avez de nouvelles classes par rapport à l'année précédente (<em>un nom de classe qui n'existait pas l'année précédente</em>), il faudra contrôler les dates de périodes pour vérifier que rien n'a été oublié.<br />
				Dans tous les cas, les dates de vacances devront être mises à jour (<em>il est possible de les importer d'après le fichier iCal officiel Education Nationale</em>).<br />
				<a href='../edt_organisation/edt_calendrier.php'>Changer les dates dans le calendrier EDT</a> et forcer les dates pour les absences (<em>Lien Mettre à jour les dates de fin de périodes pour le module Absences, d'après les dates de périodes de cours ci-dessous.</em>).<br />
				Les dates de périodes de notes et les dates de vacances doivent être mises à jour sans quoi les compositions des groupes risquent d'être erronées lors de la saisie des absences et les totaux d'absences également.</p>
			</li>
			<li>
				<p>Créer les comptes élèves/responsables manquants.</p>
			</li>
			<li>
				<p>Activer les comptes élèves/responsables.</p>
			</li>
			<li>
				<p>Effectuer l'association SSO pour les nouveaux comptes si vous êtes dans un environnement ENT.</p>
			</li>
			<li>
				<p>Si vous utilisez un compte générique pour les professeurs d'EPS pour la saisie des absences<br />(<em>il arrive que tous les professeurs d'EPS fassent les saisies sur la même machine; se déconnecter/reconnecter pour ces saisies est très malcommode</em>),<br /><a href='../utilisateurs/index.php?mode=personnels'>ré-activer le compte Equipe EPS</a>,<br />créer un  enseignement (<em>le nommer \"EPS_abs\" par exemple</em>) par classe invisible sur B, CN, CDT via <a href='../classes/classes_param.php#creer_enseignement'>Paramétrage par lots</a>.</p>
			</li>
			<li>
				<p>Si vous utilisez le module Flux RSS pour les cahiers de textes, <a href='../cahier_texte_admin/rss_cdt_admin.php#rss_initialisation_cas_par_cas'>Créer les flux RSS manquants</a>.</p>
			</li>
		</ul>
	</li>
</ul>\n";
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
