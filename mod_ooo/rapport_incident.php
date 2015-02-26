<?php
/*
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
/*
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

// SQL : INSERT INTO droits VALUES ( '/mod_ooo/rapport_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : Rapport incident', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/rapport_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : Rapport Incident', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}
*/

include_once('./lib/lib_mod_ooo.php');

include_once('../tbs/tbs_class.php');
include_once('../tbs/plugins/tbs_plugin_opentbs.php');

include_once('../mod_discipline/sanctions_func_lib.php'); // la librairie de fonction du module discipline pour la fonction p_nom , u_p_nom

//debug_var();

//
// Zone de traitement des données qui seront fusionnées au modèle
// ATTENTION S'il y a des TABLEAUX à TRAITER Voir en BAS DU FICHIER PARTIE TABLEAU (Merge)
// Chacune correspond à une variable définie dans le modèle
//
//On récupère les coordonnées du collège dans Gepi ==> $gepiSettings['nom_setting']
$ets_anne_scol = $gepiSettings['gepiSchoolName'];
$ets_nom = $gepiSettings['gepiSchoolName'];
$ets_adr1 = $gepiSettings['gepiSchoolAdress1'];
$ets_adr2 = $gepiSettings['gepiSchoolAdress2'];
$ets_cp = $gepiSettings['gepiSchoolZipCode'];
$ets_ville = $gepiSettings['gepiSchoolCity'];
$ets_tel = $gepiSettings['gepiSchoolTel'];
$ets_fax = $gepiSettings['gepiSchoolFax'];
$ets_email = $gepiSettings['gepiSchoolEmail'];
$gepiyear =  $gepiSettings['gepiYear'];
 
 
// recupération des parametres
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL); // Les informations viennent d'où ? si mode = module_discipline ==> du module discipline
$id_incident=isset($_POST['id_incident']) ? $_POST['id_incident'] : (isset($_GET['id_incident']) ? $_GET['id_incident'] : NULL); 
//$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL); 
//$id_sanction=isset($_POST['id_sanction']) ? $_POST['id_sanction'] : (isset($_GET['id_sanction']) ? $_GET['id_sanction'] : NULL); 

// Identifiant du responsable pour lequel faire l'impression
$pers_id=isset($_POST['pers_id']) ? $_POST['pers_id'] : (isset($_GET['pers_id']) ? $_GET['pers_id'] : NULL); 
$ele_login_pour_adresse=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : ""); 

//Initialisation des données
$date ='';
$objet_rapport ='';
$motif = '';
$nom_resp ='';
$fct_resp ='';
$num_incident ='';
$creneau ='';
$lieu_incident ='';
$mesures_demandees = '';
$mesures_prises = '';
$autres_mesures_prises = '';
$incident_clos = '';

$ad_nom_resp="";
$adr1_resp="";
$adr2_resp="";
$adr3_resp="";
$cp_resp="";
$commune_resp="";

// mode = module_discipline, on vient de la page saisie incident du module discipline
// mode = module_retenue, on vient de la partie sanction du module discipline et de la sanction : retenue
if ($mode=='module_discipline') {
	//
	//les protagonistes d'un incident
	//
	$sql="SELECT * FROM s_protagonistes WHERE id_incident='$id_incident' ORDER BY qualite,statut,login;";
	$res2=mysqli_query($GLOBALS["mysqli"], $sql);
	$cpt=0;
	$tab_protagonistes=array(); //le tableau des login
	$donnee_tab_protagonistes=array(); //le tableau des données
	$nb_eleves=0; // nombre d'élèves concernés par l'incident

	while($lig2=mysqli_fetch_object($res2)) {
		$nb_eleves++;
		$tab_protagonistes[]=$lig2->login;

		if($lig2->statut=='eleve') {
			$sql="SELECT nom,prenom FROM eleves WHERE login='$lig2->login';";
			//echo "$sql<br />\n";
			$res3=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res3)>0) {
				$lig3=mysqli_fetch_object($res3);
				$donnee_tab_protagonistes[$cpt]['nom']=ucfirst(mb_strtolower($lig3->prenom))." ".strtoupper($lig3->nom);
			}
			else {
				echo "ERREUR: Login $lig2->login inconnu";
			}

			$tmp_tab=get_class_from_ele_login($lig2->login);
			if(isset($tmp_tab['liste'])) {
				$donnee_tab_protagonistes[$cpt]['statut']=$tmp_tab['liste'];
			}

			// Il peut y avoir plusieurs protagonistes à l'incident.
			// Si on ne précise pas de pers_id, on ne met pas d'adresse
			if((isset($pers_id))&&($lig2->login==$ele_login_pour_adresse)) {
					$sql="SELECT rp.civilite,rp.nom,rp.prenom,ra.adr1,ra.adr2,ra.adr3,ra.cp,ra.commune FROM resp_pers rp, resp_adr ra, responsables2 r, eleves e WHERE rp.pers_id=r.pers_id AND rp.adr_id=ra.adr_id AND r.ele_id=e.ele_id AND e.login='$lig2->login' AND r.pers_id='$pers_id' ORDER BY r.resp_legal;";
				$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_resp)==0) {
					$ad_nom_resp="";
					$adr1_resp="";
					$adr2_resp="";
					$adr3_resp="";
					$cp_resp="";
					$commune_resp="";
				}
				else {
					$lig_resp=mysqli_fetch_object($res_resp);
					$ad_nom_resp=$lig_resp->civilite." ".$lig_resp->nom." ".$lig_resp->prenom;
					$adr1_resp=$lig_resp->adr1;
					$adr2_resp=$lig_resp->adr2;
					$adr3_resp=$lig_resp->adr3;
					$cp_resp=$lig_resp->cp;
					$commune_resp=$lig_resp->commune;

					if(!responsables_adresses_separees($lig2->login)) {
						$tmp_tab_resp=get_resp_from_ele_login($lig2->login);
						$tmp_nom_precedent="";
						$nb_nom_parents=0;
						for($loop_resp=0;$loop_resp<count($tmp_tab_resp);$loop_resp++) {
							if($tmp_nom_precedent!=$tmp_tab_resp[$loop_resp]['nom']){$nb_nom_parents++;}
							$tmp_nom_precedent=$tmp_tab_resp[$loop_resp]['nom'];
						}

						if($nb_nom_parents==1) {
							$ad_nom_resp="";
							$nom_resp="";
							$prenom_resp="";
							$civ_nom_resp="";
							for($loop_resp=0;$loop_resp<count($tmp_tab_resp);$loop_resp++) {
								if($loop_resp>0) {
									$civ_nom_resp.=" et ";
									$prenom_resp.=" et ";
								}
								else {
									$nom_resp.=$tmp_tab_resp[$loop_resp]['nom'];
								}
								$civ_nom_resp.=$tmp_tab_resp[$loop_resp]['civilite'];
								$prenom_resp.=$tmp_tab_resp[$loop_resp]['prenom'];
							}
							$ad_nom_resp=$civ_nom_resp." ".$nom_resp." ".$prenom_resp;
						}
						else {
							$ad_nom_resp="";
							for($loop_resp=0;$loop_resp<count($tmp_tab_resp);$loop_resp++) {
								if($loop_resp>0) {
									$ad_nom_resp.=" et ";
								}
								$ad_nom_resp.=$tmp_tab_resp[$loop_resp]['designation'];
							}
						}
					}
				}
			}
		}
		else {
			$sql="SELECT nom,prenom,civilite,statut FROM utilisateurs WHERE login='$lig2->login';";
			$res3=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res3)>0) {
				$lig3=mysqli_fetch_object($res3);
				$donnee_tab_protagonistes[$cpt]['nom']=$lig3->civilite." ".strtoupper($lig3->nom)." ".ucfirst(mb_substr($lig3->prenom,0,1));
			}
			else {
				echo "ERREUR: Login $lig2->login inconnu";
			}

			if($lig3->statut=='autre') {
				//echo " (<i>".$_SESSION['statut_special']."</i>)\n";
				$sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
												WHERE du.login_user = '".$lig2->login."'
												AND du.id_statut = ds.id;";
				$query = mysqli_query($GLOBALS["mysqli"], $sql);
				$result = mysqli_fetch_array($query);

				$donnee_tab_protagonistes[$cpt]['statut']=$result['nom_statut'];
			}
			else {
				$donnee_tab_protagonistes[$cpt]['statut']=$lig3->statut;
			}
		}
		if($lig2->qualite!='') {
			$donnee_tab_protagonistes[$cpt]['qualite']=$lig2->qualite;
		}
		$cpt++;
	}
	// on inverse les clés
	$r_tab_protagonistes=array_flip($tab_protagonistes);
	//affichage des donnée pour débug
	/*
				echo "<pre>";
				print_r($donnee_tab_protagonistes);
				echo "</pre>";
				echo "<pre>";
				print_r($array_type2);
				echo "</pre>";
	*/


	// on récupère les données à transmettre au modèle de retenue open office.
	$sql_incident="SELECT * FROM s_incidents WHERE id_incident='$id_incident';";
	$res_incident=mysqli_query($GLOBALS["mysqli"], $sql_incident);
	if(mysqli_num_rows($res_incident)>0) {
		$lig_incident=mysqli_fetch_object($res_incident);
		
		//traitement de la date mysql
		$date=datemysql_to_jj_mois_aaaa($lig_incident->date,'-','o');
		
		// Créneau horaire
		$creneau = $lig_incident->heure;
		
		//traitement du motif
		$motif = $lig_incident->description;
		
		//traitement de l'objet
		$objet_rapport = $lig_incident->nature;
		
		// incident clos
		$incident_clos = ($lig_incident->etat=="clos")?"L'incident est clos.":"";

		//recherche des mesures prises dans la table s_traitement_incident
		$sql_mesures_prises="SELECT s_mesures.mesure,s_traitement_incident.login_ele FROM s_traitement_incident,s_mesures WHERE s_traitement_incident.id_incident='$id_incident' AND s_traitement_incident.id_mesure=s_mesures.id AND s_mesures.type='prise' ORDER BY s_traitement_incident.login_ele";
		//echo "$sql_lieu<br />\n";
		$res_mesures_prises=mysqli_query($GLOBALS["mysqli"], $sql_mesures_prises);
		if(mysqli_num_rows($res_mesures_prises)>0) {
			while ($lig_mesure_prise=mysqli_fetch_object($res_mesures_prises)) {
				if ($mesures_prises!="") {$mesures_prises .= "\n";}
				if ($nb_eleves>1) {$mesures_prises .=$donnee_tab_protagonistes[$r_tab_protagonistes[$lig_mesure_prise->login_ele]]['nom']." : ";}
				$mesures_prises .= $lig_mesure_prise->mesure;
			}
		}

		//recherche des mesures demandées dans la table s_traitement_incident
		$travail_demande="";
		$sql_mesures_demandees="SELECT s_mesures.mesure,s_travail_mesure.login_ele,s_travail_mesure.travail FROM s_traitement_incident,s_mesures,s_travail_mesure WHERE s_traitement_incident.id_incident='$id_incident' AND s_traitement_incident.login_ele=s_travail_mesure.login_ele AND s_traitement_incident.id_mesure=s_mesures.id AND s_mesures.type='demandee' AND s_travail_mesure.id_incident='$id_incident'  ORDER BY s_traitement_incident.login_ele";
		//echo "$sql_lieu<br />\n";
		$res_mesures_demandees=mysqli_query($GLOBALS["mysqli"], $sql_mesures_demandees);
		if(mysqli_num_rows($res_mesures_demandees)>0) {
			while ($lig_mesure_demandee=mysqli_fetch_object($res_mesures_demandees)) {
				if ($nb_eleves>1) {$mesures_demandees .=$donnee_tab_protagonistes[$r_tab_protagonistes[$lig_mesure_demandee->login_ele]]['nom']." : ";}
				$mesures_demandees .= $lig_mesure_demandee->mesure."\n";
				// quelque soit le nombre de mesures demandées il ne peut y avoir qu'un seul travail et/ou document
				$travail_demande = " ° Travail : ".$lig_mesure_demandee->travail;
			}
			if ($travail_demande!="") {$mesures_demandees .= $travail_demande."\n";}
		}

		// recherche des suites données à l'incident
		$sql_autres_mesures_prises="SELECT nature,id_sanction,login FROM s_sanctions WHERE s_sanctions.id_incident = '".$id_incident."' ORDER BY login";
		$res_autres_mesures_prises=mysqli_query($GLOBALS["mysqli"], $sql_autres_mesures_prises);
		if(mysqli_num_rows($res_autres_mesures_prises)>0) {
			while ($lig_autre_sanction=mysqli_fetch_object($res_autres_mesures_prises)) {
				switch($lig_autre_sanction->nature)
				{
					case "travail" :
						if ($nb_eleves>1) {$autres_mesures_prises .=$donnee_tab_protagonistes[$r_tab_protagonistes[$lig_autre_sanction->login]]['nom']." : ";}
						$autres_mesures_prises .= "Travail\n";
						$r_sql="SELECT travail FROM s_travail WHERE id_sanction='".$lig_autre_sanction->id_sanction."'";
						$R=mysqli_query($GLOBALS["mysqli"], $r_sql);
						if ($R) {$autres_mesures_prises .= " ° ".old_mysql_result($R,0)."\n";}
						break;
					case "retenue" :
						if ($nb_eleves>1) {$autres_mesures_prises .=$donnee_tab_protagonistes[$r_tab_protagonistes[$lig_autre_sanction->login]]['nom']." : ";}
						$autres_mesures_prises .= "Retenue\n";
						$r_sql="SELECT travail FROM s_retenues WHERE id_sanction='".$lig_autre_sanction->id_sanction."'";
						$R=mysqli_query($GLOBALS["mysqli"], $r_sql);
						if ($R) {$autres_mesures_prises .= " ° Travail : ".old_mysql_result($R,0)."\n";}
						break;
					case "exclusion" :
						if ($nb_eleves>1) {$autres_mesures_prises .=$donnee_tab_protagonistes[$r_tab_protagonistes[$lig_autre_sanction->login]]['nom']." : ";}
						$autres_mesures_prises .= "Exclusion\n";
						$r_sql="SELECT travail,type_exclusion,qualification_faits FROM s_exclusions WHERE id_sanction='".$lig_autre_sanction->id_sanction."'";
						$R=mysqli_query($GLOBALS["mysqli"], $r_sql);
						if ($R) {$autres_mesures_prises .= " ° Type : ".old_mysql_result($R,0,1)."\n ° Travail : ".old_mysql_result($R,0,0)."\n ° Qualifications des faits : ".old_mysql_result($R,0,2)."\n";}
						break;
				}
			}
		}
		//$sql_autres_mesures_prises="SELECT s_autres_sanctions.description,s_types_sanctions.nature,s_sanctions.login FROM s_sanctions ,s_autres_sanctions,s_types_sanctions WHERE s_sanctions.id_incident = '".$id_incident."'AND s_sanctions.id_sanction = s_autres_sanctions.id_sanction AND s_autres_sanctions.id_nature=s_types_sanctions.id_nature ORDER BY s_sanctions.login";
		$sql_autres_mesures_prises="SELECT s_autres_sanctions.description,sts.nature,s_sanctions.login FROM s_sanctions ,s_autres_sanctions,s_types_sanctions2 sts WHERE s_sanctions.id_incident = '".$id_incident."'AND s_sanctions.id_sanction = s_autres_sanctions.id_sanction AND s_autres_sanctions.id_nature=sts.id_nature ORDER BY s_sanctions.login";
		$res_autres_mesures_prises=mysqli_query($GLOBALS["mysqli"], $sql_autres_mesures_prises);
		if(mysqli_num_rows($res_autres_mesures_prises)>0) {
			while ($lig_autre_sanction=mysqli_fetch_object($res_autres_mesures_prises)) {
				if ($nb_eleves>1) {$autres_mesures_prises .=$donnee_tab_protagonistes[$r_tab_protagonistes[$lig_autre_sanction->login]]['nom']." : ";}
				$autres_mesures_prises .= $lig_autre_sanction->nature."\n";
				if ($lig_autre_sanction->description!="") {$autres_mesures_prises .= " ° ".$lig_autre_sanction->description."\n";}
			}
		}

		//recherche du lieu dans la table s_lieux_incidents
		$sql_lieu="SELECT * FROM s_lieux_incidents WHERE id='$lig_incident->id_lieu';";
		//echo "$sql_lieu<br />\n";
		$res_lieu=mysqli_query($GLOBALS["mysqli"], $sql_lieu);
		if(mysqli_num_rows($res_lieu)>0) {
			$lig_lieu=mysqli_fetch_object($res_lieu);
			$lieu_incident = $lig_lieu->lieu;
		}

		//le déclarant On récupère le nom et le prénom (et la qualité)
		$sql="SELECT nom,prenom,civilite,statut FROM utilisateurs WHERE login='$lig_incident->declarant';";
		//echo "$sql<br />\n";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			//var modèle
			$nom_resp = $lig->civilite." ".strtoupper($lig->nom)." ".ucfirst(mb_substr($lig->prenom,0,1)).".";
		}
		else {
			echo "ERREUR: Login $lig_incident->declarant";
		}
		if($lig->statut=='autre') {
			$sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
											WHERE du.login_user = '".$lig_incident->declarant."'
											AND du.id_statut = ds.id;";
			$query = mysqli_query($GLOBALS["mysqli"], $sql);
			$result = mysqli_fetch_array($query);
			//var modèle
			$fct_resp = $result['nom_statut'] ;
		}
		else {
			$fct_resp = $lig->statut ;
		}
	$fct_resp = ucfirst($fct_resp);
		
	} else {
		return "INCIDENT INCONNU";
	}
	
	//var modèle
	$num_incident = $id_incident;

} //if mode = module discipline  

//
// Fin zone de traitement Les données qui seront fusionnées au modèle
//


$TBS = new clsTinyButStrong; // new instance of TBS
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin

// Load the template
$nom_fichier_modele_ooo ='rapport_incident.odt';

//Procédure du traitement à effectuer
//les chemins contenant les données
include_once ("./lib/chemin.inc.php");

$nom_fichier = $nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo;

$TBS->LoadTemplate($nom_fichier, OPENTBS_ALREADY_UTF8);

//Génération du nom du fichier
$now = gmdate('d_M_Y_H:i:s');
$nom_fichier_modele = explode('.',$nom_fichier_modele_ooo);
$nom_fic = remplace_accents($nom_fichier_modele[0]."_N°_".$num_incident."_généré_le_".$now.".".$nom_fichier_modele[1]);

$TBS->MergeBlock('blk1',$donnee_tab_protagonistes) ; 

$TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, $nom_fic);



?>
