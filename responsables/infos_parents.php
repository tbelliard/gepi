<?php
/*
 *
 * @version $Id$
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions complémentaires et/ou librairies utiles

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../logout.php?auto=1");
    die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/responsables/infos_parents.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/responsables/infos_parents.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Grille élèves/parents',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

$mode=isset($_GET['mode']) ? $_GET['mode'] : NULL;

$sql="SELECT DISTINCT id, classe FROM classes ORDER BY classe;";
//echo "$sql<br />\n";
$res_classes=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_classes=mysqli_num_rows($res_classes);
if($nb_classes>0) {
	$tab_classe=array();
	$cpt=0;
	while($lig_classe=mysqli_fetch_object($res_classes)) {
		$tab_classe[$cpt]=array();
		$tab_classe[$cpt]['id']=$lig_classe->id;
		$tab_classe[$cpt]['classe']=$lig_classe->classe;
		$cpt++;
	}
}

if(isset($_GET['export_csv'])) {
	if((!isset($mode))||($mode==1)) {
		$mode_csv=isset($_GET['mode_csv']) ? $_GET['mode_csv'] : "a";

		if($_GET['export_csv']=="export_infos_parents_1") {

			$nom_fic = "export_infos_parents_1_".date("Ymd_His").".csv";

			if($mode_csv=="b") {
				$csv="Classe;Nom;Prenom;Sexe;Naissance;login_ele;ele_id;Responsable;Resp_civ;Resp_nom;Resp_prenom;Tel_pers;Tel_port;Tel_prof;Email;Adresse;Code postal;Commune;login_resp;pers_id\r\n";
			}
			else {
				$csv="Classe;Nom;Prenom;Sexe;Naissance;login_ele;ele_id;Responsable;Resp_civ;Resp_nom;Resp_prenom;Tel_pers;Tel_port;Tel_prof;Email;Adresse;login_resp;pers_id\r\n";
			}

			for($i=0;$i<count($tab_classe);$i++) {
				//$csv.=$tab_classe[$i]['classe'].";";
	
				$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='".$tab_classe[$i]['id']."' ORDER BY e.nom, e.prenom;";
				$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_ele=mysqli_fetch_object($res_ele)) {
					$sql="SELECT rp.* FROM resp_pers rp, responsables2 r WHERE (r.resp_legal='1' OR r.resp_legal='2') AND r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' ORDER BY r.resp_legal;";
					//echo "$sql<br />";
					$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
	
					while($lig_resp=mysqli_fetch_object($res_resp)) {
	
						$csv.=$tab_classe[$i]['classe'].";";
						$csv.=mb_strtoupper($lig_ele->nom).";";
						$csv.=casse_mot($lig_ele->prenom,'majf2').";";
						$csv.=$lig_ele->sexe.";";
						$csv.=formate_date($lig_ele->naissance).";";
						$csv.=$lig_ele->login.";";
						$csv.=$lig_ele->ele_id.";";

						$csv.=$lig_resp->civilite." ".mb_strtoupper($lig_resp->nom)." ".casse_mot($lig_resp->prenom,'majf2').";";
						$csv.=$lig_resp->civilite.";".mb_strtoupper($lig_resp->nom).";".casse_mot($lig_resp->prenom,'majf2').";";
						$csv.=affiche_numero_tel_sous_forme_classique($lig_resp->tel_pers).";";
						$csv.=affiche_numero_tel_sous_forme_classique($lig_resp->tel_port).";";
						$csv.=affiche_numero_tel_sous_forme_classique($lig_resp->tel_prof).";";
						$csv.=$lig_resp->mel.";";
	
						$sql="SELECT * FROM resp_adr WHERE adr_id='".$lig_resp->adr_id."';";
						//echo "$sql<br />";
						$res_adr=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_adr)==1) {
							$adresse="";
							$lig_adr=mysqli_fetch_object($res_adr);
							$adresse.=$lig_adr->adr1;
							if($lig_adr->adr1!="") {
								$adresse.=" ";
							}
		
							$adresse.=$lig_adr->adr2;
							if($lig_adr->adr2!="") {
								$adresse.=" ";
							}
		
							$adresse.=$lig_adr->adr3;
							if($lig_adr->adr3!="") {
								$adresse.=" ";
							}
		
							$adresse.=$lig_adr->adr4;
							if($lig_adr->adr4!="") {
								$adresse.=" ";
							}

							if($mode_csv=="b") {
								$csv.=trim($adresse).";".$lig_adr->cp.";".$lig_adr->commune.";";
							}
							else {
								if(trim($adresse)!="") {$adresse=trim($adresse).", ";}
								$adresse.=$lig_adr->cp." ".$lig_adr->commune;

								$csv.=$adresse.";";
							}

						}
						else {
							$csv.=";";
						}
						$csv.=$lig_resp->login.";";
						$csv.=$lig_resp->pers_id.";";

						$csv.="\r\n";
					}
				}
			}
		}
		else {
			// Format Ariane

			if($mode_csv=="resp1") {
				$nom_fic = "export_infos_parents_1_Ariane_Responsables_legaux_1_".date("Ymd_His").".csv";
				$sql_choix_resp="r.resp_legal='1'";
				$csv="Classe;Nom de famille;Prénom;Date de naissance;Lieu naissance;Nom Responsable légal 1;Prénom responsable légal 1;Adresse ;CP;Commune;Tel portable resp. légal 1;\r\n";
			}
			else {
				$nom_fic = "export_infos_parents_1_Ariane_Tous_les_Responsables_legaux_".date("Ymd_His").".csv";
				$sql_choix_resp="r.resp_legal='1' OR r.resp_legal='2'";
				$csv="Classe;Nom de famille;Prénom;Date de naissance;Lieu naissance;Nom Responsable légal;Prénom responsable légal;Adresse ;CP;Commune;Tel portable resp. légal;Numéro responsable légal;\r\n";
			}


			for($i=0;$i<count($tab_classe);$i++) {
				//$csv.=$tab_classe[$i]['classe'].";";
	
				$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='".$tab_classe[$i]['id']."' ORDER BY e.nom, e.prenom;";
				$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_ele=mysqli_fetch_object($res_ele)) {
					$sql="SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r WHERE ($sql_choix_resp) AND r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' ORDER BY r.resp_legal;";
					//echo "$sql<br />";
					$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
	
					while($lig_resp=mysqli_fetch_object($res_resp)) {
	
						$csv.=$tab_classe[$i]['classe'].";";
						$csv.=mb_strtoupper($lig_ele->nom).";";
						$csv.=casse_mot($lig_ele->prenom,'majf2').";";
						$csv.=formate_date($lig_ele->naissance).";";
						$csv.=get_commune($lig_ele->lieu_naissance, 2).";";

						$csv.=mb_strtoupper($lig_resp->nom).";".casse_mot($lig_resp->prenom,'majf2').";";

						$sql="SELECT * FROM resp_adr WHERE adr_id='".$lig_resp->adr_id."';";
						//echo "$sql<br />";
						$res_adr=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_adr)==1) {
							$adresse="";
							$lig_adr=mysqli_fetch_object($res_adr);
							$adresse.=$lig_adr->adr1;
							if($lig_adr->adr1!="") {
								$adresse.=" ";
							}
		
							$adresse.=$lig_adr->adr2;
							if($lig_adr->adr2!="") {
								$adresse.=" ";
							}
		
							$adresse.=$lig_adr->adr3;
							if($lig_adr->adr3!="") {
								$adresse.=" ";
							}
		
							$adresse.=$lig_adr->adr4;
							if($lig_adr->adr4!="") {
								$adresse.=" ";
							}

							$csv.=trim($adresse).";".$lig_adr->cp.";".$lig_adr->commune.";";

						}
						else {
							$csv.=";";
						}

						if($lig_resp->tel_port!="") {
							//$csv.='"'.preg_replace("/ /","",affiche_numero_tel_sous_forme_classique($lig_resp->tel_port)).'"';
							$csv.=affiche_numero_tel_sous_forme_classique($lig_resp->tel_port);
						}
						elseif($lig_resp->tel_pers!="") {
							//$csv.='"'.preg_replace("/ /","",affiche_numero_tel_sous_forme_classique($lig_resp->tel_pers)).'"';
							$csv.=affiche_numero_tel_sous_forme_classique($lig_resp->tel_pers);
						}
						$csv.=";";
	
						if($mode_csv!="resp1") {
							$csv.=$lig_resp->resp_legal.";";
						}

						$csv.="\r\n";
					}
				}
			}
		}

		send_file_download_headers('text/x-csv',$nom_fic);
		//echo $csv;
		echo echo_csv_encoded($csv);
		die();
	}
	elseif($mode=='2') {
		$nom_fic = "export_infos_parents_eleves_".date("Ymd_His").".csv";

		$sql="SELECT DISTINCT ra.* FROM resp_adr ra, resp_pers rp, responsables2 r WHERE ra.adr_id=rp.adr_id AND rp.pers_id=r.pers_id AND (r.resp_legal='1' OR r.resp_legal='2') ORDER BY rp.nom, rp.prenom;";
		$res_adr=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_adr)==0) {
			$msg="Aucune association responsable/adresse n'a été trouvée.<br />";
		}
		else {
			if(isset($_GET['ancien_mode'])) {
				$csv="DESIGNATION;ADRESSE;ADR_1;ADR_2;ADR_3;ADR_4;CP;COMMUNE;PAYS;NOM_RESP_1;PRENOM_RESP_1;TEL_PERS_1;TEL_PROF_1;TEL_PORT_1;MEL_1;NOM_RESP_2;PRENOM_RESP_2;TEL_PERS_2;TEL_PROF_2;TEL_PORT_2;MEL_2;ELEVE_1;ELEVE_2;ELEVE_3;ELEVE_4;ELEVE_5;ELEVE_6;ELEVE_7;ELEVE_8;ELEVE_9;ELEVE_10\r\n";
			}
			else {
				$csv="DESIGNATION;ADRESSE;ADR_1;ADR_2;ADR_3;ADR_4;CP;COMMUNE;PAYS;NOM_RESP_1;PRENOM_RESP_1;TEL_PERS_1;TEL_PROF_1;TEL_PORT_1;MEL_1;NOM_RESP_2;PRENOM_RESP_2;TEL_PERS_2;TEL_PROF_2;TEL_PORT_2;MEL_2;ELEVE_1;ELEVE_1_LOGIN;ELEVE_1_PRENOM_NOM;ELEVE_1_CLASSES;ELEVE_2;ELEVE_2_LOGIN;ELEVE_2_PRENOM_NOM;ELEVE_2_CLASSES;ELEVE_3;ELEVE_3_LOGIN;ELEVE_3_PRENOM_NOM;ELEVE_3_CLASSES;ELEVE_4;ELEVE_4_LOGIN;ELEVE_4_PRENOM_NOM;ELEVE_4_CLASSES;ELEVE_5;ELEVE_5_LOGIN;ELEVE_5_PRENOM_NOM;ELEVE_5_CLASSES;ELEVE_6;ELEVE_6_LOGIN;ELEVE_6_PRENOM_NOM;ELEVE_6_CLASSES;ELEVE_7;ELEVE_7_LOGIN;ELEVE_7_PRENOM_NOM;ELEVE_7_CLASSES;ELEVE_8;ELEVE_8_LOGIN;ELEVE_8_PRENOM_NOM;ELEVE_8_CLASSES;ELEVE_9;ELEVE_9_LOGIN;ELEVE_9_PRENOM_NOM;ELEVE_9_CLASSES;ELEVE_10;ELEVE_10_LOGIN;ELEVE_10_PRENOM_NOM;ELEVE_10_CLASSES;\r\n";
			}
			while($lig_adr=mysqli_fetch_object($res_adr)) {
				$resp=array();
				$tab_ele=array();
				$sql="SELECT DISTINCT rp.* FROM resp_pers rp, responsables2 r WHERE rp.pers_id=r.pers_id AND r.resp_legal='1' AND rp.adr_id='$lig_adr->adr_id';";
				$res_rp=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_rp)>0) {
					// On recherche alors aussi les élèves.
					while($lig_rp=mysqli_fetch_object($res_rp)) {
						//$tab_ele_tmp=get_enfants_from_resp_login($lig_rp->login,'avec_classe');
						if(isset($_GET['ancien_mode'])) {
							$tab_ele_tmp=get_enfants_from_pers_id($lig_rp->pers_id,'avec_classe');
						}
						else {
							$tab_ele_tmp=get_enfants_from_pers_id($lig_rp->pers_id,'csv');
						}
						for($loop=1;$loop<count($tab_ele_tmp);$loop+=2) {
							if(!in_array($tab_ele_tmp[$loop], $tab_ele)) {
								$tab_ele[]=$tab_ele_tmp[$loop];
							}
						}
						$resp[1]['civilite']=$lig_rp->civilite;
						$resp[1]['nom']=$lig_rp->nom;
						$resp[1]['prenom']=$lig_rp->prenom;
						$resp[1]['tel_pers']=$lig_rp->tel_pers;
						$resp[1]['tel_port']=$lig_rp->tel_port;
						$resp[1]['tel_prof']=$lig_rp->tel_prof;
						$resp[1]['mel']=$lig_rp->mel;
					}
				}
		
				$sql="SELECT DISTINCT rp.* FROM resp_pers rp, responsables2 r WHERE rp.pers_id=r.pers_id AND r.resp_legal='2' AND rp.adr_id='$lig_adr->adr_id';";
				$res_rp=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_rp)>0) {
					// On recherche alors aussi les élèves.
					while($lig_rp=mysqli_fetch_object($res_rp)) {
						//$tab_ele_tmp=get_enfants_from_resp_login($lig_rp->login,'avec_classe');
						if(isset($_GET['ancien_mode'])) {
							$tab_ele_tmp=get_enfants_from_pers_id($lig_rp->pers_id,'avec_classe');
						}
						else {
							$tab_ele_tmp=get_enfants_from_pers_id($lig_rp->pers_id,'csv');
						}
						for($loop=1;$loop<count($tab_ele_tmp);$loop+=2) {
							if(!in_array($tab_ele_tmp[$loop], $tab_ele)) {
								$tab_ele[]=$tab_ele_tmp[$loop];
							}
						}
						$resp[2]['civilite']=$lig_rp->civilite;
						$resp[2]['nom']=$lig_rp->nom;
						$resp[2]['prenom']=$lig_rp->prenom;
						$resp[2]['tel_pers']=$lig_rp->tel_pers;
						$resp[2]['tel_port']=$lig_rp->tel_port;
						$resp[2]['tel_prof']=$lig_rp->tel_prof;
						$resp[2]['mel']=$lig_rp->mel;
					}
				}
		
				if(count($tab_ele)>0) {
					$designation="";
					if((isset($resp[1]['nom']))&&(isset($resp[2]['nom']))) {
						if(mb_strtoupper($resp[1]['nom'])==mb_strtoupper($resp[2]['nom'])) {
							if($resp[1]['civilite']!="") {
								if($resp[2]['civilite']!="") {
									$designation=$resp[1]['civilite']." et ".$resp[2]['civilite']." ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2')." et ".casse_mot($resp[2]['prenom'],'majf2');
								}
								else {
									$designation="M et Mme ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2')." et ".casse_mot($resp[2]['prenom'],'majf2');
								}
							}
							else {
								$designation="M et Mme ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2')." et ".casse_mot($resp[2]['prenom'],'majf2');
							}
						}
						else {
							if($resp[1]['civilite']!="") {
								if($resp[2]['civilite']!="") {
									$designation=$resp[1]['civilite']." ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2')." et ".$resp[2]['civilite']." ".mb_strtoupper($resp[2]['nom'])." ".casse_mot($resp[2]['prenom'],'majf2');
								}
								else {
									$designation="M et Mme ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2')." et ".mb_strtoupper($resp[2]['nom'])." ".casse_mot($resp[2]['prenom'],'majf2');
								}
							}
							else {
								$designation="M et Mme ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2')." et ".mb_strtoupper($resp[2]['nom'])." ".casse_mot($resp[2]['prenom'],'majf2');
							}
						}
					}
					else {
						if(isset($resp[1]['nom'])) {
							if($resp[1]['civilite']!="") {
								$designation=$resp[1]['civilite']." ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2');
							}
							else {
								$designation="M ou Mme ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2');
							}
						}
						elseif(isset($resp[2]['nom'])) {
							if($resp[2]['civilite']!="") {
								$designation=$resp[2]['civilite']." ".mb_strtoupper($resp[2]['nom'])." ".casse_mot($resp[2]['prenom'],'majf2');
							}
							else {
								$designation="M ou Mme ".mb_strtoupper($resp[2]['nom'])." ".casse_mot($resp[2]['prenom'],'majf2');
							}
						}
					}

					$adresse="";
					if($lig_adr->adr1!="") {
						$adresse.=$lig_adr->adr1.", ";
					}
					if($lig_adr->adr2!="") {
						$adresse.=$lig_adr->adr2.", ";
					}
					if($lig_adr->adr3!="") {
						$adresse.=$lig_adr->adr3.", ";
					}
					if($lig_adr->adr4!="") {
						$adresse.=$lig_adr->adr4.", ";
					}
					if($lig_adr->cp!="") {
						$adresse.=$lig_adr->cp." ";
		
						if($lig_adr->commune!="") {
							$adresse.=$lig_adr->commune;
						}
		
						if(($lig_adr->pays!="")&&(mb_strtoupper($lig_adr->pays)!=mb_strtoupper(getSettingValue('gepiSchoolPays')))) {
							$adresse.=", ";
							$adresse.=$lig_adr->pays;
						}
					}
					else {
						if($lig_adr->commune!="") {
							$adresse.=$lig_adr->commune;
						}
		
						if(($lig_adr->pays!="")&&(mb_strtoupper($lig_adr->pays)!=mb_strtoupper(getSettingValue('gepiSchoolPays')))) {
							$adresse.=", ";
							$adresse.=$lig_adr->pays;
						}
					}

					$csv.="$designation;$adresse;$lig_adr->adr1;$lig_adr->adr2;$lig_adr->adr3;$lig_adr->adr4;$lig_adr->cp;$lig_adr->commune;$lig_adr->pays;";
					if(isset($resp[1]['nom'])) {
						$csv.=$resp[1]['nom'].";".$resp[1]['prenom'].";".affiche_numero_tel_sous_forme_classique($resp[1]['tel_pers']).";".affiche_numero_tel_sous_forme_classique($resp[1]['tel_prof']).";".affiche_numero_tel_sous_forme_classique($resp[1]['tel_port']).";".$resp[1]['mel'].";";
					}
					else {
						$csv.=";;;;;;";
					}

					if(isset($resp[2]['nom'])) {
						$csv.=$resp[2]['nom'].";".$resp[2]['prenom'].";".affiche_numero_tel_sous_forme_classique($resp[2]['tel_pers']).";".affiche_numero_tel_sous_forme_classique($resp[2]['tel_prof']).";".affiche_numero_tel_sous_forme_classique($resp[2]['tel_port']).";".$resp[2]['mel'];
					}
					else {
						$csv.=";;;;;";
					}
					for($loop=0;$loop<count($tab_ele);$loop++) {
						$csv.=";".$tab_ele[$loop];
					}
					$csv.=";\r\n";
				}
			}
	
			send_file_download_headers('text/x-csv',$nom_fic);
			//echo $csv;
			echo echo_csv_encoded($csv);
			die();
		}
	}
}


// ===================== entete Gepi ======================================//
$titre_page = "Grille élèves/parents";
require_once("../lib/header.inc.php");
// ===================== fin entete =======================================//

//debug_var();

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if($nb_classes==0) {
	echo "</p>\n";

	echo "<p style='color:red'>Aucune classe n'existe encore.</p>\n";

	require_once("../lib/footer.inc.php");
	die();
}

if((!isset($mode))||($mode==1)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."?mode=2'>Grille 2</a>";
	echo "</p>\n";
	
	echo "<p>
	<strong>Grille 1&nbsp;:</strong> Informations élèves/parents&nbsp;:
	 <a href='".$_SERVER['PHP_SELF']."?export_csv=export_infos_parents_1'>Export CSV</a>
	 - <a href='".$_SERVER['PHP_SELF']."?export_csv=export_infos_parents_1&mode_csv=b'>Export CSV <em>(adresse avec colonnes séparées pour adresse, code postal, commune)</em></a>
	 - <a href='".$_SERVER['PHP_SELF']."?export_csv=export_infos_parents_1b&mode_csv=resp1'>CSV Ariane <em>(pour les voyages à l'étranger; responsable légal 1 seulement)</em></a>
	 - <a href='".$_SERVER['PHP_SELF']."?export_csv=export_infos_parents_1b&mode_csv=resp1_et_2'>CSV Ariane avec les responsables légaux 1 et 2</a>
	</p>\n";
	echo "<table class='boireaus'>\n";
	echo "<tr>\n";
	echo "<th rowspan='2'>Classe</th>\n";
	echo "<th colspan='6'>Elève</th>\n";
	echo "<th colspan='8'>Responsable</th>\n";
	echo "</tr>\n";
	
	echo "<tr>\n";
	//echo "<th>Classe</th>\n";
	echo "<th>Nom</th>\n";
	echo "<th>Prénom</th>\n";
	echo "<th>Sexe</th>\n";
	echo "<th>Naissance</th>\n";
	echo "<th>Login</th>\n";
	echo "<th>Ele_id</th>\n";
	echo "<th>Responsable</th>\n";
	echo "<th>Tel.pers</th>\n";
	echo "<th>Tel.port</th>\n";
	echo "<th>Tel.prof</th>\n";
	echo "<th>Email</th>\n";
	echo "<th>Adresse</th>\n";
	echo "<th>Login</th>\n";
	echo "<th>Pers_id</th>\n";
	echo "</tr>\n";
	$alt=1;
	for($i=0;$i<count($tab_classe);$i++) {
		$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='".$tab_classe[$i]['id']."' ORDER BY e.nom, e.prenom;";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig_ele=mysqli_fetch_object($res_ele)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
	
			$rowspan="";
			$sql="SELECT rp.* FROM resp_pers rp, responsables2 r WHERE (r.resp_legal='1' OR r.resp_legal='2') AND r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' ORDER BY r.resp_legal;";
			//echo "$sql<br />";
			$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_resp)>1) {
				$rowspan=" rowspan='".mysqli_num_rows($res_resp)."'";
			}
			echo "<td$rowspan>".$tab_classe[$i]['classe']."</td>\n";
			echo "<td$rowspan>".mb_strtoupper($lig_ele->nom)."</td>\n";
			echo "<td$rowspan>".casse_mot($lig_ele->prenom,'majf2')."</td>\n";
			echo "<td$rowspan>".$lig_ele->sexe."</td>\n";
			echo "<td$rowspan>".formate_date($lig_ele->naissance)."</td>\n";
			echo "<td$rowspan>".$lig_ele->login."</td>\n";
			echo "<td$rowspan>".$lig_ele->ele_id."</td>\n";
			$cpt=0;
			while($lig_resp=mysqli_fetch_object($res_resp)) {
				if($cpt>0) {
					echo "</tr>\n";
					echo "<tr class='lig$alt white_hover'>\n";
				}
				echo "<td>";
				echo $lig_resp->civilite." ".mb_strtoupper($lig_resp->nom)." ".casse_mot($lig_resp->prenom,'majf2');
				echo "</td>\n";
				echo "<td>".affiche_numero_tel_sous_forme_classique($lig_resp->tel_pers)."</td>\n";
				echo "<td>".affiche_numero_tel_sous_forme_classique($lig_resp->tel_port)."</td>\n";
				echo "<td>".affiche_numero_tel_sous_forme_classique($lig_resp->tel_prof)."</td>\n";
				echo "<td>$lig_resp->mel</td>\n";
				echo "<td>";
				$sql="SELECT * FROM resp_adr WHERE adr_id='".$lig_resp->adr_id."';";
				//echo "$sql<br />";
				$res_adr=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_resp)>1) {
					$adresse="";
					$lig_adr=mysqli_fetch_object($res_adr);
					$adresse.=$lig_adr->adr1;
					if($lig_adr->adr1!="") {
						$adresse.="<br />\n";
					}
	
					$adresse.=$lig_adr->adr2;
					if($lig_adr->adr2!="") {
						$adresse.="<br />\n";
					}
	
					$adresse.=$lig_adr->adr3;
					if($lig_adr->adr3!="") {
						$adresse.="<br />\n";
					}
	
					$adresse.=$lig_adr->cp." ".$lig_adr->commune;
	
					echo $adresse;
				}
				echo "</td>\n";
				echo "<td>$lig_resp->login</td>\n";
				echo "<td>$lig_resp->pers_id</td>\n";
				$cpt++;
			}
			echo "</tr>\n";
		}	
	}
	echo "</table>\n";
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."?mode=1'>Grille 1</a>";
	echo "</p>\n";
	
	echo "<p><strong>Grille 2&nbsp;:</strong> Informations parents/élèves&nbsp;: <a href='".$_SERVER['PHP_SELF']."?export_csv=export_infos_parents_eleves&amp;mode=2'>Export CSV</a> (<em><a href='".$_SERVER['PHP_SELF']."?export_csv=export_infos_parents_eleves&amp;mode=2&amp;ancien_mode=y' title=\"Avec une seule colonne par élève\">ancien mode</a></em>)</p>\n";

	$sql="SELECT DISTINCT ra.* FROM resp_adr ra, resp_pers rp, responsables2 r WHERE ra.adr_id=rp.adr_id AND rp.pers_id=r.pers_id AND (r.resp_legal='1' OR r.resp_legal='2') ORDER BY rp.nom, rp.prenom;";
	$res_adr=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_adr)==0) {
		echo "<p style='color:red'>Aucune association responsable/adresse n'a été trouvée.</p>\n";

		require_once("../lib/footer.inc.php");
		die();
	}

	echo "<table class='boireaus'>\n";

	echo "<tr>\n";
	echo "<th>Compteur</th>\n";
	echo "<th>Désignation</th>\n";
	echo "<th>Adresse</th>\n";
	echo "<th>Responsable 1</th>\n";
	echo "<th>Coordonnées 1</th>\n";
	echo "<th>Responsable 2</th>\n";
	echo "<th>Coordonnées 2</th>\n";
	echo "<th>Elèves</th>\n";
	echo "</tr>\n";

	$alt=1;
	$cpt=1;
	//$csv="DESIGNATION;ADR_1;ADR_2;ADR_3;ADR_4;CP;COMMUNE;PAYS;NOM_RESP_1;PRENOM_RESP_1;TEL_PERS_1;TEL_PROF_1;TEL_PORT_1;MEL_1;NOM_RESP_2;PRENOM_RESP_2;TEL_PERS_2;TEL_PROF_2;TEL_PORT_2;MEL_2;ELEVE_1;ELEVE_2;ELEVE_3;ELEVE_4;ELEVE_5;ELEVE_6;ELEVE_7;ELEVE_8;ELEVE_9;ELEVE_10\r\n";
	while($lig_adr=mysqli_fetch_object($res_adr)) {
		$resp=array();
		$tab_ele=array();
		$sql="SELECT DISTINCT rp.* FROM resp_pers rp, responsables2 r WHERE rp.pers_id=r.pers_id AND r.resp_legal='1' AND rp.adr_id='$lig_adr->adr_id';";
		$res_rp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_rp)>0) {
			// On recherche alors aussi les élèves.
			while($lig_rp=mysqli_fetch_object($res_rp)) {
				//$tab_ele_tmp=get_enfants_from_resp_login($lig_rp->login,'avec_classe');
				$tab_ele_tmp=get_enfants_from_pers_id($lig_rp->pers_id,'avec_classe');
				for($loop=1;$loop<count($tab_ele_tmp);$loop+=2) {
					if(!in_array($tab_ele_tmp[$loop], $tab_ele)) {
						$tab_ele[]=$tab_ele_tmp[$loop];
					}
				}

				$resp[1]['civilite']=$lig_rp->civilite;
				$resp[1]['nom']=$lig_rp->nom;
				$resp[1]['prenom']=$lig_rp->prenom;
				$resp[1]['tel_pers']=$lig_rp->tel_pers;
				$resp[1]['tel_port']=$lig_rp->tel_port;
				$resp[1]['tel_prof']=$lig_rp->tel_prof;
				$resp[1]['mel']=$lig_rp->mel;
			}
		}

		$sql="SELECT DISTINCT rp.* FROM resp_pers rp, responsables2 r WHERE rp.pers_id=r.pers_id AND r.resp_legal='2' AND rp.adr_id='$lig_adr->adr_id';";
		$res_rp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_rp)>0) {
			// On recherche alors aussi les élèves.
			while($lig_rp=mysqli_fetch_object($res_rp)) {
				//$tab_ele_tmp=get_enfants_from_resp_login($lig_rp->login,'avec_classe');
				$tab_ele_tmp=get_enfants_from_pers_id($lig_rp->pers_id,'avec_classe');
				for($loop=1;$loop<count($tab_ele_tmp);$loop+=2) {
					if(!in_array($tab_ele_tmp[$loop], $tab_ele)) {
						$tab_ele[]=$tab_ele_tmp[$loop];
					}
				}

				$resp[2]['civilite']=$lig_rp->civilite;
				$resp[2]['nom']=$lig_rp->nom;
				$resp[2]['prenom']=$lig_rp->prenom;
				$resp[2]['tel_pers']=$lig_rp->tel_pers;
				$resp[2]['tel_port']=$lig_rp->tel_port;
				$resp[2]['tel_prof']=$lig_rp->tel_prof;
				$resp[2]['mel']=$lig_rp->mel;
			}
		}

		if(count($tab_ele)>0) {
			//$csv="DESIGNATION;ADR_1;ADR_2;ADR_3;ADR_4;CP;COMMUNE;PAYS;NOM_RESP_1;PRENOM_RESP_1;TEL_PERS_1;TEL_PROF_1;TEL_PORT_1;MEL_1;NOM_RESP_2;PRENOM_RESP_2;TEL_PERS_2;TEL_PROF_2;TEL_PORT_2;MEL_2;ELEVE_1;ELEVE_2;ELEVE_3;ELEVE_4;ELEVE_5;ELEVE_6;ELEVE_7;ELEVE_8;ELEVE_9;ELEVE_10\r\n";

			$designation="";
			if((isset($resp[1]['nom']))&&(isset($resp[2]['nom']))) {
				if(mb_strtoupper($resp[1]['nom'])==mb_strtoupper($resp[2]['nom'])) {
					if($resp[1]['civilite']!="") {
						if($resp[2]['civilite']!="") {
							$designation=$resp[1]['civilite']." et ".$resp[2]['civilite']." ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2')." et ".casse_mot($resp[2]['prenom'],'majf2');
						}
						else {
							$designation="M et Mme ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2')." et ".casse_mot($resp[2]['prenom'],'majf2');
						}
					}
					else {
						$designation="M et Mme ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2')." et ".casse_mot($resp[2]['prenom'],'majf2');
					}
				}
				else {
					if($resp[1]['civilite']!="") {
						if($resp[2]['civilite']!="") {
							$designation=$resp[1]['civilite']." ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2')." et ".$resp[2]['civilite']." ".mb_strtoupper($resp[2]['nom'])." ".casse_mot($resp[2]['prenom'],'majf2');
						}
						else {
							$designation="M et Mme ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2')." et ".mb_strtoupper($resp[2]['nom'])." ".casse_mot($resp[2]['prenom'],'majf2');
						}
					}
					else {
						$designation="M et Mme ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2')." et ".mb_strtoupper($resp[2]['nom'])." ".casse_mot($resp[2]['prenom'],'majf2');
					}
				}
			}
			elseif(isset($resp[1]['nom'])) {
				if($resp[1]['civilite']!="") {
					$designation=$resp[1]['civilite']." ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2');
				}
				else {
					$designation=mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'],'majf2');
				}
			}
			elseif(isset($resp[2]['nom'])) {
				if($resp[2]['civilite']!="") {
					$designation=$resp[2]['civilite']." ".mb_strtoupper($resp[2]['nom'])." ".casse_mot($resp[2]['prenom'],'majf2');
				}
				else {
					$designation=mb_strtoupper($resp[2]['nom'])." ".casse_mot($resp[2]['prenom'],'majf2');
				}
			}
			else {
				$designation="???";
			}

			//$csv="DESIGNATION;ADR_1;ADR_2;ADR_3;ADR_4;CP;COMMUNE;PAYS;NOM_RESP_1;PRENOM_RESP_1;TEL_PERS_1;TEL_PROF_1;TEL_PORT_1;MEL_1;NOM_RESP_2;PRENOM_RESP_2;TEL_PERS_2;TEL_PROF_2;TEL_PORT_2;MEL_2;ELEVE_1;ELEVE_2;ELEVE_3;ELEVE_4;ELEVE_5;ELEVE_6;ELEVE_7;ELEVE_8;ELEVE_9;ELEVE_10\r\n";

			/*
			$csv.="$designation;$lig_adr->adr1;$lig_adr->adr2;$lig_adr->adr3;$lig_adr->adr4;$lig_adr->cp;$lig_adr->commune;".$resp[1]['nom'].";".$resp[1]['prenom'].";".$resp[1]['tel_pers'].";".$resp[1]['tel_prof'].";".$resp[1]['tel_port'].";".$resp[1]['mel'].";".$resp[2]['nom'].";".$resp[2]['prenom'].";".$resp[2]['tel_pers'].";".$resp[2]['tel_prof'].";".$resp[2]['tel_port'].";".$resp[2]['mel'];
			for($loop=0;$loop<count($tab_ele);$loop+++) {
				$csv.=";".$tab_ele[$loop];
			}
			$csv.=";\r\n";
			*/

			$adresse="";
			if($lig_adr->adr1!="") {
				$adresse.=$lig_adr->adr1."<br />";
			}
			if($lig_adr->adr2!="") {
				$adresse.=$lig_adr->adr2."<br />";
			}
			if($lig_adr->adr3!="") {
				$adresse.=$lig_adr->adr3."<br />";
			}
			if($lig_adr->adr4!="") {
				$adresse.=$lig_adr->adr4."<br />";
			}
			if($lig_adr->cp!="") {
				$adresse.=$lig_adr->cp." ";

				if($lig_adr->commune!="") {
					$adresse.=$lig_adr->commune;
				}

				if(($lig_adr->pays!="")&&(mb_strtoupper($lig_adr->pays)!=mb_strtoupper(getSettingValue('gepiSchoolPays')))) {
					$adresse.="<br />";
					$adresse.=$lig_adr->pays;
				}
			}
			else {
				if($lig_adr->commune!="") {
					$adresse.=$lig_adr->commune;
				}

				if(($lig_adr->pays!="")&&(mb_strtoupper($lig_adr->pays)!=mb_strtoupper(getSettingValue('gepiSchoolPays')))) {
					$adresse.="<br />";
					$adresse.=$lig_adr->pays;
				}
			}

			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
			echo "<td>$cpt</td>\n";
			echo "<td>$designation</td>\n";
			echo "<td>$adresse</td>\n";
			echo "<td>";
			if(isset($resp[1]['nom'])) {
				echo $resp[1]['civilite']." ".mb_strtoupper($resp[1]['nom'])." ".casse_mot($resp[1]['prenom'], 'majf2');
			}
			else {
				echo "&nbsp;";
			}
			echo "</td>\n";
			echo "<td>";
			if(isset($resp[1]['nom'])) {
				echo "<table class='boireaus'>\n";
				$alt2=1;
				if($resp[1]['tel_pers']!='') {
					$alt2=$alt2*(-1);
					echo "<tr class='lig$alt2'><td>Tel.pers</td><td>".affiche_numero_tel_sous_forme_classique($resp[1]['tel_pers'])."</td></tr>\n";
				}
				if($resp[1]['tel_port']!='') {
					$alt2=$alt2*(-1);
					echo "<tr class='lig$alt2'><td>Tel.port</td><td>".affiche_numero_tel_sous_forme_classique($resp[1]['tel_port'])."</td></tr>\n";
				}
				if($resp[1]['tel_prof']!='') {
					$alt2=$alt2*(-1);
					echo "<tr class='lig$alt2'><td>Tel.prof</td><td>".affiche_numero_tel_sous_forme_classique($resp[1]['tel_prof'])."</td></tr>\n";
				}
				if($resp[1]['mel']!='') {
					$alt2=$alt2*(-1);
					echo "<tr class='lig$alt2'><td>Email</td><td>".$resp[1]['mel']."</td></tr>\n";
				}
				echo "</table>\n";
			}
			else {
				echo "&nbsp;";
			}
			echo "</td>\n";
			echo "<td>";
			if(isset($resp[2]['nom'])) {
				echo $resp[2]['civilite']." ".mb_strtoupper($resp[2]['nom'])." ".casse_mot($resp[2]['prenom'], 'majf2');
			}
			else {
				echo "&nbsp;";
			}
			echo "</td>\n";
			echo "<td>";
			if(isset($resp[2]['nom'])) {
				echo "<table class='boireaus'>\n";
				$alt2=1;
				if($resp[2]['tel_pers']!='') {
					$alt2=$alt2*(-1);
					echo "<tr class='lig$alt2'><td>Tel.pers</td><td>".affiche_numero_tel_sous_forme_classique($resp[2]['tel_pers'])."</td></tr>\n";
				}
				if($resp[2]['tel_port']!='') {
					$alt2=$alt2*(-1);
					echo "<tr class='lig$alt2'><td>Tel.port</td><td>".affiche_numero_tel_sous_forme_classique($resp[2]['tel_port'])."</td></tr>\n";
				}
				if($resp[2]['tel_prof']!='') {
					$alt2=$alt2*(-1);
					echo "<tr class='lig$alt2'><td>Tel.prof</td><td>".affiche_numero_tel_sous_forme_classique($resp[2]['tel_prof'])."</td></tr>\n";
				}
				if($resp[2]['mel']!='') {
					$alt2=$alt2*(-1);
					echo "<tr class='lig$alt2'><td>Email</td><td>".$resp[2]['mel']."</td></tr>\n";
				}
				echo "</table>\n";
			}
			else {
				echo "&nbsp;";
			}
			echo "</td>\n";
			echo "<td>\n";
			for($loop=0;$loop<count($tab_ele);$loop++) {
				if($loop>0) {echo "<br />\n";}
				echo $tab_ele[$loop];
			}
			echo "</td>\n";
			echo "</tr>\n";
			$cpt++;
		}
	}
	echo "</table>\n";

	echo "<p><em>NOTES&nbsp;</em> Pour donner des résultats corrects, cette page nécessite que vous ayez effectué un ";
	if($_SESSION['statut']=='administrateur') {
		echo "<a href='dedoublonnage_adresses.php'>dédoublonnage des adresses responsables</a>";
	}
	else {
		echo "dédoublonnage des adresses responsables (<em>contactez un administrateur</em>)";
	}
	echo ".</p>\n";

}

echo "<p><br /></p>\n";

//echo "<p><em>NOTES&nbsp;:</em></p>\n";

require_once("../lib/footer.inc.php");
?>
