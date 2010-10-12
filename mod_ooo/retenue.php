<?php
/*
 * $Id: index.php 2554 2008-10-12 14:49:29Z crob $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// SQL : INSERT INTO droits VALUES ( '/mod_ooo/retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : retenue', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : Retenue', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}


include_once('./lib/lib_mod_ooo.php');

include_once('./lib/tbs_class.php');
include_once('./lib/tbsooo_class.php');
define( 'PCLZIP_TEMPORARY_DIR', '../mod_ooo/tmp/' );
include_once('../lib/pclzip.lib.php');

include_once('../mod_discipline/sanctions_func_lib.php'); // la librairie de fonction du module discipline pour la fonction p_nom , u_p_nom

//debug_var();

//
// Zone de traitement des données qui seront fusionnées au modèle
// Chacune correspond à une variable définie dans le modèle
// ATTENTION S'il y a des TABLEAUX à TRAITER Voir en BAS DU FICHIER PARTIE TABLEAU (Merge)
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
 
 
// recupération des parametres
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL); // Les informations viennent d'où ? si mode = module_discipline ==> du module discipline
$id_incident=isset($_POST['id_incident']) ? $_POST['id_incident'] : (isset($_GET['id_incident']) ? $_GET['id_incident'] : NULL); 
$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL); 
$id_sanction=isset($_POST['id_sanction']) ? $_POST['id_sanction'] : (isset($_GET['id_sanction']) ? $_GET['id_sanction'] : NULL); 

//Initialisation des données du modèle Ooo Retenue
$date ='';
$nom_prenom_eleve ='';
$classe ='';
$motif = '';
$travail ='';
$nom_resp ='';
$fct_resp ='';
$date_retenue ='';
$lieu = '';
$duree ='';
$h_deb ='';
$num_incident = '';

//echo "\$mode=$mode<br />";
//echo "\$id_incident=$id_incident<br />";

// mode = module_discipline, on vient de la page saisie incident du module discipline
// mode = module_retenue, on vient de la partie sanction du module discipline et de la sanction : retenue
if (($mode=='module_discipline')||($mode=='module_retenue')) {
	// on récupère les données à transmettre au modèle de retenue open office.
	$sql_incident="SELECT * FROM `s_incidents` WHERE `id_incident`=$id_incident";
	$res_incident=mysql_query($sql_incident);
	if(mysql_num_rows($res_incident)>0) {
		$lig_incident=mysql_fetch_object($res_incident);
		
		//traitement de la date mysql
		$date=datemysql_to_jj_mois_aaaa($lig_incident->date,'-','o');
		
		//traitement du motif et du travail
		$motif = $lig_incident->description;
		$travail ='Donné sur place'; // texte par défaut, c'est un enseignant qui rédige l'incident, il n'y a pas de possibilité de saisir le travail.

		$nature_incident=$lig_incident->nature;

		// le nom et le prénom de l'élève
		$nom_prenom_eleve =p_nom($ele_login,"Pn");
		
		// la classe de l'élève
		$tmp_tab=get_class_from_ele_login($ele_login);
		if(isset($tmp_tab['liste'])) {
			$classe= $tmp_tab['liste'];
		} else {
		    $classe = '';
		}

		$sql="SELECT * FROM resp_pers rp, resp_adr ra, responsables2 r, eleves e WHERE rp.pers_id=r.pers_id AND rp.adr_id=ra.adr_id AND r.ele_id=e.ele_id AND e.login='$ele_login' AND (r.resp_legal='1' OR r.resp_legal='2') ORDER BY r.resp_legal;";
		$res_resp=mysql_query($sql);
		if(mysql_num_rows($res_resp)==0) {
			$ad_nom_resp="";
			$adr1_resp="";
			$adr2_resp="";
			$adr3_resp="";
			$cp_resp="";
			$commune_resp="";
		}
		else {
			$lig_resp=mysql_fetch_object($res_resp);
			$ad_nom_resp=$lig_resp->civilite." ".$lig_resp->nom." ".$lig_resp->prenom;
			$adr1_resp=$lig_resp->adr1;
			$adr2_resp=$lig_resp->adr2;
			$adr3_resp=$lig_resp->adr3;
			$cp_resp=$lig_resp->cp;
			$commune_resp=$lig_resp->commune;
		}

		//le déclarant On récupère le nom et le prénom (et la qualité)
		$sql="SELECT nom,prenom,civilite,statut FROM utilisateurs WHERE login='$lig_incident->declarant';";
		//echo "$sql<br />\n";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);
			//var retenue
			$nom_resp = $lig->civilite." ".strtoupper($lig->nom)." ".ucfirst(substr($lig->prenom,0,1)).".";
		}
		else {
			echo "ERREUR: Login $lig_incident->declarant";
		}

		if($lig->statut=='autre') {

			$sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
											WHERE du.login_user = '".$lig_incident->declarant."'
											AND du.id_statut = ds.id;";
			$query = mysql_query($sql);
			$result = mysql_fetch_array($query);
	        
			//var retenue
			$fct_resp = $result['nom_statut'] ;
		}
		else {
			$fct_resp = $lig->statut ;
		}

	$fct_resp = ucfirst($fct_resp);
		
	} else {
		//$nature_incident="";
		return "INCIDENT INCONNU";
	}
	
	//var retenue
	$num_incident = $id_incident;

	//On Traite ici la date et l'heure de la retenue posée
	if ($mode=='module_retenue') {	
	     $sql_sanction = "SELECT * FROM `s_retenues` WHERE `id_sanction`=$id_sanction";
	     $res_sanction=mysql_query($sql_sanction);
	    if(mysql_num_rows($res_sanction)>0) {
			$lig_sanction=mysql_fetch_object($res_sanction);
			
			$date_retenue = datemysql_to_jj_mois_aaaa($lig_sanction->date,'-','o');
			
			if ($lig_sanction->duree>1) {
			  $duree = $lig_sanction->duree." heures";
			} else {
			  $duree = $lig_sanction->duree." heure";
			}
			
			$travail = $lig_sanction->travail;
			$lieu = $lig_sanction->lieu;
			
			//recherche de l'heure de début. C'est le crénaux qui est enregistré.
			$sql_heure = "SELECT * FROM `edt_creneaux` WHERE `nom_definie_periode`='$lig_sanction->heure_debut'";
			//echo $sql_heure;
			$res_heure = mysql_query($sql_heure);
			if(mysql_num_rows($res_heure)>0) {
			    $lig_heure=mysql_fetch_object($res_heure); 
				$h_deb = $lig_heure->heuredebut_definie_periode;
				//on affiche que les 5 1er caratèeres de l'heure
				$h_deb=substr($h_deb,0,5);
				//remplacement des : par H dans la chaine
				$h_deb=str_replace(":","H", $h_deb);
			} else {
			  
			  // LE CRENEAU EST INCONNU on se retrouve dans le cas d'une heure saisie à la main.
			  $h_deb = $lig_sanction->heure_debut;
			}	
	    } else {
			return "LA RETENUE EST INCONNUE";
		}     
	} // mode = module retenue	
} //if mode = module discipline  

if ($mode=='formulaire_retenue') { //les donnée provenant du formulaire 
    if (isset($_SESSION['retenue_date'])) {
        $date = datemysql_to_jj_mois_aaaa($_SESSION['retenue_date'],'/','n');
	    //session_unregister("retenue_date");
	    unset($_SESSION['retenue_date']);
	}
	if (isset($_SESSION['retenue_nom_prenom_elv'])) {
		$nom_prenom_eleve =$_SESSION['retenue_nom_prenom_elv'];
		//session_unregister("retenue_nom_prenom_elv");
	    unset($_SESSION['retenue_nom_prenom_elv']);
	}
	if (isset($_SESSION['retenue_classe_elv'])) {
		$classe = $_SESSION['retenue_classe_elv'];
		//session_unregister("retenue_classe_elv");
	    unset($_SESSION['retenue_classe_elv']);
	}
	if (isset($_SESSION['retenue_motif'])) {
		$motif = $_SESSION['retenue_motif'];
		$motif=traitement_magic_quotes(corriger_caracteres($motif));
		// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
		$motif=my_ereg_replace('(\\\r\\\n)+',"\r\n",$motif);
		//session_unregister("retenue_motif");
	    unset($_SESSION['retenue_motif']);
	}
	if (isset($_SESSION['retenue_travail'])) {
		$travail = $_SESSION['retenue_travail'];
		$travail=traitement_magic_quotes(corriger_caracteres($travail));
		// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
		$travail=my_ereg_replace('(\\\r\\\n)+',"\r\n",$travail);
		//session_unregister("retenue_travail");
	    unset($_SESSION['retenue_travail']);
	}
	if (isset($_SESSION['retenue_nom_resp'])) {
	$nom_resp = $_SESSION['retenue_nom_resp'];
	//session_unregister("retenue_nom_resp");
	    unset($_SESSION['retenue_nom_resp']);
	}
	if (isset($_SESSION['retenue_fct_resp'])) {
		$fct_resp = $_SESSION['retenue_fct_resp'];
		//session_unregister("retenue_fct_resp");
	    unset($_SESSION['retenue_fct_resp']);
	}

	if (isset($_SESSION['retenue_nature_incident'])) {
		$nature_incident = $_SESSION['retenue_nature_incident'];
		$nature_incident=traitement_magic_quotes(corriger_caracteres($nature_incident));
		// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
		$nature_incident=my_ereg_replace('(\\\r\\\n)+',"\r\n",$nature_incident);
		// session_unregister("retenue_nature_incident");
	    unset($_SESSION['retenue_nature_incident']);
	}

	$date_retenue ='';
	$duree ='';
	$h_deb ='';
	$num_incident = '';

} // formulaire_retenue

//$nature_incident='Scrogneugneu';

// Quand on génère la retenue par le module Modèle OpenOffice, la $nature_incident n'est pas récupérée.
// Elle ne l'est que si on génère la retenue depuis le module Discipline
if(!isset($nature_incident)) {$nature_incident="";}

//
// Fin zone de traitement Les données qui seront fusionnées au modèle
//

//
//Les variables à modifier pour le traitement  du modèle ooo
//
//Le chemin et le nom du fichier ooo à traiter (le modèle de document)
$nom_fichier_modele_ooo ='retenue.odt';
// Par defaut tmp
$nom_dossier_temporaire ='tmp';
//par defaut content.xml
$nom_fichier_xml_a_traiter ='content.xml';


//Procédure du traitement à effectuer
//les chemins contenant les données
include_once ("./lib/chemin.inc.php");


// instantiate a TBS OOo class
$OOo = new clsTinyButStrongOOo;
// setting the object
$OOo->SetProcessDir($nom_dossier_temporaire ); //dossier où se fait le traitement (décompression / traitement / compression)
// create a new openoffice document from the template with an unique id 
$OOo->NewDocFromTpl($nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo); // le chemin du fichier est indiqué à partir de l'emplacement de ce fichier
// merge data with openoffice file named 'content.xml'
$OOo->LoadXmlFromDoc($nom_fichier_xml_a_traiter); //Le fichier qui contient les variables et doit être parsé (il sera extrait)

// Traitement des tableaux si necessaire
//$OOo->MergeBlock('blk1',$array_type1) ;

// Fin de traitement des tableaux


$OOo->SaveXmlToDoc(); //traitement du fichier extrait


//Génération du nom du fichier
$now = gmdate('d_M_Y_H:i:s');
$nom_fichier_modele = explode('.',$nom_fichier_modele_ooo);
//$nom_fic = $nom_fichier_modele[0]."_".$classe."_".$nom_prenom_eleve."_généré_le_".$now.".".$nom_fichier_modele[1];
$nom_fic = remplace_accents($nom_fichier_modele[0]."_".$classe."_".$nom_prenom_eleve."_généré_le_".$now.".".$nom_fichier_modele[1],'all');
header('Expires: ' . $now);
// lem9 & loic1: IE need specific headers

if (my_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
    header('Content-Disposition: inline; filename="' . $nom_fic . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
} else {
    header('Content-Disposition: attachment; filename="' . $nom_fic . '"');
    header('Pragma: no-cache');
}

// display
header('Content-type: '.$OOo->GetMimetypeDoc());
header('Content-Length: '.filesize($OOo->GetPathnameDoc()));
$OOo->FlushDoc(); //envoi du fichier traité
$OOo->RemoveDoc(); //suppression des fichiers de travail
// Fin de traitement des tableaux

?>