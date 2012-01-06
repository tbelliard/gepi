<?php
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

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_discipline/mod_discipline_extraction_ooo.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_discipline/mod_discipline_extraction_ooo.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Discipline : Extrait OOo des incidents',
statut='';";
$insert=mysql_query($sql);
}

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/mod_discipline_extraction_ooo.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline : Extrait OOo des incidents', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/mod_discipline_extraction_ooo.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline : Extrait OOo des incidents', '');";
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

include_once('../mod_ooo/lib/lib_mod_ooo.php'); //les fonctions
$nom_fichier_modele_ooo =''; //variable a initialiser a blanc pour inclure le fichier suivant et eviter une notice. Pour les autres inclusions, cela est inutile.
include_once('../mod_ooo/lib/chemin.inc.php'); // le chemin des dossiers contenant les  modèles

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

$path='../mod_ooo/'.$nom_dossier_modele_a_utiliser;

require_once("../mod_discipline/sanctions_func_lib.php");

$id_classe_incident="";
$chaine_criteres="";
$date_incident="";
$heure_incident="";
$nature_incident="---";
$protagoniste_incident="";
$declarant_incident="---";
$incidents_clos="y";

if((!isset($id_classe_incident))||($id_classe_incident=="")) {
	$sql="(SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp WHERE sp.id_incident=si.id_incident";
}
else {
	$sql="(SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp, j_eleves_classes jec WHERE sp.id_incident=si.id_incident AND jec.id_classe='$id_classe_incident' AND jec.login=sp.login";
}

$ajout_sql="";
if($date_incident!="") {$ajout_sql.=" AND si.date='$date_incident'";$chaine_criteres.="&amp;date_incident=$date_incident";}
if($heure_incident!="") {$ajout_sql.=" AND si.heure='$heure_incident'";$chaine_criteres.="&amp;heure_incident=$heure_incident";}
if($nature_incident!="---") {$ajout_sql.=" AND si.nature='$nature_incident'";$chaine_criteres.="&amp;nature_incident=$nature_incident";}
if($protagoniste_incident!="") {$ajout_sql.=" AND sp.login='$protagoniste_incident'";$chaine_criteres.="&amp;protagoniste_incident=$protagoniste_incident";}

//echo "\$declarant_incident=$declarant_incident<br />";

if($declarant_incident!="---") {$ajout_sql.=" AND si.declarant='$declarant_incident'";$chaine_criteres.="&amp;declarant_incident=$declarant_incident";}

if($id_classe_incident!="") {
	$chaine_criteres.="&amp;id_classe_incident=$id_classe_incident";
}

$sql.=$ajout_sql;
$sql2=$sql;
if($incidents_clos!="y") {$sql.=" AND si.etat!='clos'";}

$sql.=")";
$sql2.=")";

$sql.=" ORDER BY date DESC, heure DESC;";
$sql2.=" ORDER BY date DESC, heure DESC;";

//echo "$sql<br />";
//echo "$sql2<br />";

$tab_lignes_OOo=array();

$nb_ligne=0;
$res_incident=mysql_query($sql);
while($lig_incident=mysql_fetch_object($res_incident)) {
	$tab_lignes_OOo[$nb_ligne]=array();
	
	$tab_lignes_OOo[$nb_ligne]['id_incident']=$lig_incident->id_incident;
	$tab_lignes_OOo[$nb_ligne]['declarant']=civ_nom_prenom($lig_incident->declarant,'');
	$tab_lignes_OOo[$nb_ligne]['date']=formate_date($lig_incident->date);
	$tab_lignes_OOo[$nb_ligne]['heure']=$lig_incident->heure;
	$tab_lignes_OOo[$nb_ligne]['nature']=$lig_incident->nature;
	$tab_lignes_OOo[$nb_ligne]['description']=$lig_incident->description;
	$tab_lignes_OOo[$nb_ligne]['etat']=$lig_incident->etat;

	// Lieu
	$tab_lignes_OOo[$nb_ligne]['lieu']=get_lieu_from_id($lig_incident->id_lieu);

	// Protagonistes
	$tab_protagonistes_eleves=array();
	$sql="SELECT * FROM s_protagonistes WHERE id_incident='$lig_incident->id_incident' ORDER BY statut,qualite,login;";
	$res2=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$tab_lignes_OOo[$nb_ligne]['protagonistes']="Aucun";
	}
	else {
		$liste_protagonistes="";
		while($lig2=mysql_fetch_object($res2)) {
			if($liste_protagonistes!="") {$liste_protagonistes.=", ";}
			if($lig2->statut=='eleve') {
				$liste_protagonistes.=get_nom_prenom_eleve($lig2->login,'avec_classe');
				$tab_protagonistes_eleves[]=$lig2->login;
			}
			else {
				$liste_protagonistes.=civ_nom_prenom($lig2->login,'',"y");
			}

			if($lig2->qualite!='') {
				$liste_protagonistes.=" $lig2->qualite";
			}
		}
	}
	$tab_lignes_OOo[$nb_ligne]['protagonistes']=$liste_protagonistes;

	$id_incident_courant=$lig_incident->id_incident;

	// Mesures prises
	$texte="";
	$sql="SELECT DISTINCT sti.login_ele FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='prise'";
	//$texte.="<br />$sql";
	$res_t_incident=mysql_query($sql);
	$nb_login_ele_mesure_prise=mysql_num_rows($res_t_incident);

	if($nb_login_ele_mesure_prise>0) {
		while($lig_t_incident=mysql_fetch_object($res_t_incident)) {
			$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='prise' AND login_ele='$lig_t_incident->login_ele' ORDER BY s.mesure;";
			$res_mes_ele=mysql_query($sql);
			$nb_mes_ele=mysql_num_rows($res_mes_ele);

			$texte.=civ_nom_prenom($lig_t_incident->login_ele,'')." :";
			while($lig_mes_ele=mysql_fetch_object($res_mes_ele)) {
				$texte.=" ".$lig_mes_ele->mesure;
			}
			$texte.="\n";
		}
	}
	$tab_lignes_OOo[$nb_ligne]['mesures_prises']=$texte;

	// Mesures demandees
	$texte="";
	$sql="SELECT DISTINCT sti.login_ele FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='demandee' ORDER BY login_ele";
	//$texte.="<br />$sql";
	$res_t_incident2=mysql_query($sql);
	$nb_login_ele_mesure_demandee=mysql_num_rows($res_t_incident2);

	if($nb_login_ele_mesure_demandee>0) {
		while($lig_t_incident=mysql_fetch_object($res_t_incident2)) {
			$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='demandee' AND login_ele='$lig_t_incident->login_ele' ORDER BY s.mesure;";
			$res_mes_ele=mysql_query($sql);
			$nb_mes_ele=mysql_num_rows($res_mes_ele);

			$texte.=civ_nom_prenom($lig_t_incident->login_ele,'')." :";
			while($lig_mes_ele=mysql_fetch_object($res_mes_ele)) {
				$texte.=" ".$lig_mes_ele->mesure;
			}
			$texte.="\n";
		}
	}
	$tab_lignes_OOo[$nb_ligne]['mesures_demandees']=$texte;


	// Sanctions
	$texte_sanctions="";
	for($i=0;$i<count($tab_protagonistes_eleves);$i++) {
		$ele_login=$tab_protagonistes_eleves[$i];

		$designation_eleve=civ_nom_prenom($ele_login,'');

		// Retenues
		$sql="SELECT * FROM s_sanctions s, s_retenues sr WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND sr.id_sanction=s.id_sanction ORDER BY sr.date, sr.heure_debut;";
		//$retour.="$sql<br />\n";
		$res_sanction=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
			$texte_sanctions.=$designation_eleve;

			while($lig_sanction=mysql_fetch_object($res_sanction)) {
				$texte_sanctions.=" : Retenue ";

				$nombre_de_report=nombre_reports($lig_sanction->id_sanction,0);
				if($nombre_de_report!=0) {$texte_sanctions.=" ($nombre_de_report reports)";}

				$texte_sanctions.=formate_date($lig_sanction->date);
				$texte_sanctions.=" $lig_sanction->heure_debut";
				$texte_sanctions.=" (".$lig_sanction->duree."H)";
				$texte_sanctions.=" $lig_sanction->lieu";
				//$texte_sanctions.="<td>".nl2br($lig_sanction->travail)."</td>\n";
	
				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
					$texte="Aucun travail";
				}
				else {
					$texte=$lig_sanction->travail;
					if($tmp_doc_joints!="") {
						if($texte!="") {$texte.="\n";}
						$texte.=$tmp_doc_joints;
					}
				}

				$texte_sanctions.=" : ".$texte."\n";
			}
		}
	
		// Exclusions
		$sql="SELECT * FROM s_sanctions s, s_exclusions se WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND se.id_sanction=s.id_sanction ORDER BY se.date_debut, se.heure_debut;";
		//$retour.="$sql<br />\n";
		$res_sanction=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
			$texte_sanctions.=$designation_eleve;

			while($lig_sanction=mysql_fetch_object($res_sanction)) {
				$texte_sanctions.=" : Exclusion ";

				$texte_sanctions.=" ".formate_date($lig_sanction->date_debut);
				$texte_sanctions.=" ".$lig_sanction->heure_debut;
				$texte_sanctions.=" - ".formate_date($lig_sanction->date_fin);
				$texte_sanctions.=" ".$lig_sanction->heure_fin;
				$texte_sanctions.=" (".$lig_sanction->lieu.")";
	
				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
					$texte="Aucun travail";
				}
				else {
					$texte=$lig_sanction->travail;
					if($tmp_doc_joints!="") {
						if($texte!="") {$texte.="\n";}
						$texte.=$tmp_doc_joints;
					}
				}
				$texte_sanctions.=" : ".$texte;
			}
		}
	
		// Simple travail
		$sql="SELECT * FROM s_sanctions s, s_travail st WHERE s.id_incident=$id_incident_courant AND s.login='".$ele_login."' AND st.id_sanction=s.id_sanction ORDER BY st.date_retour;";
		//$retour.="$sql<br />\n";
		$res_sanction=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
			$texte_sanctions.=$designation_eleve;

			while($lig_sanction=mysql_fetch_object($res_sanction)) {
				$texte_sanctions.=" : Travail pour le ";
				$texte_sanctions.=formate_date($lig_sanction->date_retour);
	
				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
					$texte="Aucun travail";
				}
				else {
					$texte=$lig_sanction->travail;
					if($tmp_doc_joints!="") {
						if($texte!="") {$texte.="\n";}
						$texte.=$tmp_doc_joints;
					}
				}
				$texte_sanctions.=" : ".$texte;
			}
		}
	
		// Autres sanctions
		$sql="SELECT * FROM s_sanctions s, s_autres_sanctions sa, s_types_sanctions sts WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND sa.id_sanction=s.id_sanction AND sa.id_nature=sts.id_nature ORDER BY sts.nature;";
		//echo "$sql<br />\n";
		$res_sanction=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
			$texte_sanctions.=$designation_eleve;

			while($lig_sanction=mysql_fetch_object($res_sanction)) {
				$texte_sanctions.=" : $lig_sanction->description ";

				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if($tmp_doc_joints!="") {
					$texte_sanctions.=$tmp_doc_joints;
				}
				$texte_sanctions.="\n";
			}
		}
	}

	$tab_lignes_OOo[$nb_ligne]['sanctions']=$texte_sanctions;


	$nb_ligne++;
}

/*
	echo "<pre>";
	print_r($tab_lignes_OOo);
	echo "</pre>";

	die();
*/

$mode_ooo="imprime";

include_once('../mod_ooo/lib/tinyButStrong.class.php');
include_once('../mod_ooo/lib/tinyDoc.class.php');

$tempdir=get_user_temp_directory();

$fb_dezip_ooo=getSettingValue("fb_dezip_ooo");

if($fb_dezip_ooo==2) {
	$msg="Mode \$fb_dezip_ooo=$fb_dezip_ooo non géré pour le moment... désolé.<br />";
}
else {
	$tempdirOOo="../temp/".$tempdir;

	$nom_dossier_temporaire = $tempdirOOo;
	//par defaut content.xml
	$nom_fichier_xml_a_traiter ='content.xml';

	// Creation d'une classe tinyDoc
	$OOo = new tinyDoc();
	
	// Choix du module de dezippage
	$dezippeur=getSettingValue("fb_dezip_ooo");
	if ($dezippeur==1){
		$OOo->setZipMethod('shell');
		$OOo->setZipBinary('zip');
		$OOo->setUnzipBinary('unzip');
	}
	else{
		$OOo->setZipMethod('ziparchive');
	}

	
	// setting the object
	$OOo->SetProcessDir($nom_dossier_temporaire ); //dossier ou se fait le traitement (decompression / traitement / compression)
	// create a new openoffice document from the template with an unique id
	//$OOo->createFrom($path."/".$tab_file[$num_fich]); // le chemin du fichier est indique a partir de l'emplacement de ce fichier
	$OOo->createFrom($path."/mod_discipline_liste_incidents.odt"); // le chemin du fichier est indique a partir de l'emplacement de ce fichier
	// merge data with openoffice file named 'content.xml'
	$OOo->loadXml($nom_fichier_xml_a_traiter); //Le fichier qui contient les variables et doit etre parse (il sera extrait)
	
	
	// Traitement des tableaux
	// On insere ici les lignes concernant la gestion des tableaux
	
	// $OOo->mergeXmlBlock('eleves',$tab_eleves_OOo);
	
	$OOo->mergeXml(
		array(
			'name'      => 'incident',
			'type'      => 'block',
			'data_type' => 'array',
			'charset'   => 'UTF-8'
		),$tab_lignes_OOo);
	
	$OOo->SaveXml(); //traitement du fichier extrait
	
	$OOo->sendResponse(); //envoi du fichier traite
	$OOo->remove(); //suppression des fichiers de travail
	// Fin de traitement des tableaux
	$OOo->close();
	
	die();
}

?>
