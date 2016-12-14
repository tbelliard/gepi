<?php
/*
 *
 *
 * Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_engagements/imprimer_documents.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_engagements/imprimer_documents.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='F',
autre='F',
description='Imprimer documents',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$tab_engagements=get_tab_engagements();

if(count($tab_engagements['indice'])==0) {
	header("Location: ../accueil.php?msg=Aucun type d engagement n est actuellement défini.");
	die();
}

if(isset($_GET['page_origine'])) {
	$_SESSION['page_origine']=$_GET['page_origine'];
}

$nb_engagements=count($tab_engagements['indice']);

// Restreindre a ses propres documents
if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {

	if(!isset($id_classe)) {
		header("Location: ../accueil.php?msg=Classe non choisie.");
		die();
	}

	if(!is_delegue_conseil_classe($_SESSION['login'], $id_classe)) {
		header("Location: ../accueil.php?msg=Accès non autorisé.");
		die();
	}


	include_once('../mod_ooo/lib/tinyButStrong.class.php');
	include_once('../mod_ooo/lib/tinyDoc.class.php');

	$acad = $gepiSettings['gepiSchoolAcademie'];
	$etab_anne_scol = $gepiSettings['gepiSchoolName'];
	$etab_nom = $gepiSettings['gepiSchoolName'];
	$etab_adr1 = $gepiSettings['gepiSchoolAdress1'];
	$etab_adr2 = $gepiSettings['gepiSchoolAdress2'];
	$etab_cp = $gepiSettings['gepiSchoolZipCode'];
	$etab_ville = $gepiSettings['gepiSchoolCity'];
	$etab_tel = $gepiSettings['gepiSchoolTel'];
	$etab_fax = $gepiSettings['gepiSchoolFax'];
	$etab_email = $gepiSettings['gepiSchoolEmail'];

	$imprimer=isset($_POST['imprimer']) ? $_POST['imprimer'] : (isset($_GET['imprimer']) ? $_GET['imprimer'] : "");

	$classe=get_nom_classe($id_classe);

	if($imprimer=="liste_eleves") {

		$tab_OOo=array();

		$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='".$id_classe."' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$cpt=0;
			while($lig=mysqli_fetch_object($res)) {
				$tab_OOo[$cpt]['designation_eleve']=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
				$cpt++;
			}
		}

		// Load the template
		$nom_fichier_modele_ooo ='liste_eleve_conseil_classe.odt';

		$prefixe_generation_hors_dossier_mod_ooo="../mod_ooo/";

		//Procédure du traitement à effectuer
		//les chemins contenant les données
		include_once ("../mod_ooo/lib/chemin.inc.php");

		$nom_fichier = "../mod_ooo/".$nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo;

		//Génération du nom du fichier
		$now = gmdate('d_M_Y_H:i:s');
		$nom_fichier_modele = explode('.',$nom_fichier_modele_ooo);
		$nom_fic = remplace_accents($nom_fichier_modele[0]."_".$classe."_"."_généré_le_".$now.".".$nom_fichier_modele[1],'all');
		// Je n'arrive pas à générer un fichier à ce nom.
		// Problème de syntaxe tinyButStrong?


		// Création d'une classe tinyDoc
		$OOo = new tinyDoc();

		// Choix du module de dézippage
		$dezippeur=getSettingValue("fb_dezip_ooo");
		if ($dezippeur==1){
			$OOo->setZipMethod('shell');
			$OOo->setZipBinary('zip');
			$OOo->setUnzipBinary('unzip');
		}
		else{
			$OOo->setZipMethod('ziparchive');
		}

		$tempdir=get_user_temp_directory();
		$nom_dossier_temporaire = "../temp/".$tempdir;
		$nom_fichier_xml_a_traiter ='content.xml';

		$OOo->SetProcessDir($nom_dossier_temporaire);
		$OOo->createFrom($nom_fichier);
		//$OOo->createFrom($nom_fichier);
		$OOo->loadXml($nom_fichier_xml_a_traiter);

		// Traitement des tableaux
		$OOo->mergeXml(
		array(
		'name'      => 'var',
		'type'      => 'block',
		'data_type' => 'array',
		'charset'   => 'UTF-8'
		),$tab_OOo);

		$OOo->SaveXml(); //traitement du fichier extrait


		$OOo->sendResponse(); //envoi du fichier traité
		$OOo->remove(); //suppression des fichiers de travail
		// Fin de traitement des tableaux
		$OOo->close();

		die();
	}
	elseif($imprimer=="convocation") {

		$lieu="Salle des conseils";
		// Lieu à mettre dans d_dates_evenements_classes

		$tab_OOo=array();

		$cpt=0;

		$tab_OOo[$cpt]['acad']=$acad;
		$tab_OOo[$cpt]['etab_nom']=$etab_nom;
		$tab_OOo[$cpt]['etab_adr1']=$etab_adr1;
		$tab_OOo[$cpt]['etab_adr2']=$etab_adr2;
		$tab_OOo[$cpt]['etab_cp']=$etab_cp;
		$tab_OOo[$cpt]['etab_ville']=$etab_ville;

		$tab_OOo[$cpt]['classe']=$classe;
		$tab_OOo[$cpt]['lieu']=$lieu;


		$date_limite=strftime("%Y-%m-%d")." 00:00:00";
		// Pour debug/devel
		//$date_limite="2014-06-01 00:00:00";

		// Chercher s'il y a des conseils de classe à venir
		$sql="SELECT * FROM d_dates_evenements dde, 
					d_dates_evenements_classes ddec 
				WHERE dde.id_ev=ddec.id_ev AND 
					ddec.id_classe='".$id_classe."' AND 
					date_evenement>='".$date_limite."' 
					ORDER BY ddec.date_evenement;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$tmp_date_conseil=formate_date($lig->date_evenement,"y2","complet");
		}
		else {
			$tmp_date_conseil="DATE INCONNUE";
		}
		$tab_OOo[$cpt]['date_conseil']=$tmp_date_conseil;

		$tmp_tab=get_info_user($_SESSION['login']);
		if(count($tmp_tab)==0) {
			$tmp_tab['nom']="NOM_INCONNU";
			$tmp_tab['prenom']="PRENOM_INCONNU";
			$tmp_tab['adresse']['adr1']="ADR1";
			$tmp_tab['adresse']['adr2']="";
			$tmp_tab['adresse']['adr3']="";
			$tmp_tab['adresse']['cp']="";
			$tmp_tab['adresse']['commune']="VILLE";
		}
		elseif($tmp_tab['statut']=='eleve') {
			// Récupérer l'adresse du 1er parent

			$tmp_tab['adresse']['adr1']="ADR1_PARENT";
			$tmp_tab['adresse']['adr2']="";
			$tmp_tab['adr3']="";
			$tmp_tab['adresse']['cp']="";
			$tmp_tab['adresse']['commune']="VILLE_PARENT";

			$sql="SELECT ra.* FROM resp_adr ra, resp_pers rp, responsables2 r WHERE ra.adr_id=rp.adr_id AND rp.pers_id=r.pers_id AND r.ele_id='".$tmp_tab['ele_id']."' AND r.resp_legal!='0' ORDER BY r.resp_legal;";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);

				$tmp_tab['adresse']['adr1']=$lig->adr1;
				$tmp_tab['adresse']['adr2']=$lig->adr2;
				$tmp_tab['adresse']['adr3']=$lig->adr3;
				$tmp_tab['adresse']['cp']=$lig->cp;
				$tmp_tab['adresse']['commune']=$lig->commune;
			}
		}

		$tab_OOo[$cpt]['dest_civilite']=$tmp_tab['civilite'];
		$tab_OOo[$cpt]['dest_nom']=$tmp_tab['nom'];
		$tab_OOo[$cpt]['dest_prenom']=$tmp_tab['prenom'];
		$tab_OOo[$cpt]['dest_adr1']=$tmp_tab['adresse']['adr1'];
		$tab_OOo[$cpt]['dest_adr2']=$tmp_tab['adresse']['adr2'];
		$tab_OOo[$cpt]['dest_adr3']=$tmp_tab['adresse']['adr3'];
		$tab_OOo[$cpt]['dest_cp']=$tmp_tab['adresse']['cp'];
		$tab_OOo[$cpt]['dest_ville']=$tmp_tab['adresse']['commune'];

		// Load the template
		$nom_fichier_modele_ooo ='convocation_conseil_classe.odt';
		$prefixe_generation_hors_dossier_mod_ooo="../mod_ooo/";

		//Procédure du traitement à effectuer
		//les chemins contenant les données
		include_once ("../mod_ooo/lib/chemin.inc.php");

		$nom_fichier = "../mod_ooo/".$nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo;

		//Génération du nom du fichier
		$now = gmdate('d_M_Y_H:i:s');
		$nom_fichier_modele = explode('.',$nom_fichier_modele_ooo);
		$nom_fic = remplace_accents($nom_fichier_modele[0]."_".$classe."_"."_généré_le_".$now.".".$nom_fichier_modele[1],'all');
		// Je n'arrive pas à générer un fichier à ce nom.
		// Problème de syntaxe tinyButStrong?


		// Création d'une classe tinyDoc
		$OOo = new tinyDoc();

		// Choix du module de dézippage
		$dezippeur=getSettingValue("fb_dezip_ooo");
		if ($dezippeur==1){
			$OOo->setZipMethod('shell');
			$OOo->setZipBinary('zip');
			$OOo->setUnzipBinary('unzip');
		}
		else{
			$OOo->setZipMethod('ziparchive');
		}

		$tempdir=get_user_temp_directory();
		$nom_dossier_temporaire = "../temp/".$tempdir;
		$nom_fichier_xml_a_traiter ='content.xml';

		$OOo->SetProcessDir($nom_dossier_temporaire);
		$OOo->createFrom($nom_fichier);
		//$OOo->createFrom($nom_fichier);
		$OOo->loadXml($nom_fichier_xml_a_traiter);

		// Traitement des tableaux
		$OOo->mergeXml(
		array(
		'name'      => 'var',
		'type'      => 'block',
		'data_type' => 'array',
		'charset'   => 'UTF-8'
		),$tab_OOo);

		$OOo->SaveXml(); //traitement du fichier extrait

		$OOo->sendResponse(); //envoi du fichier traité
		$OOo->remove(); //suppression des fichiers de travail
		// Fin de traitement des tableaux
		$OOo->close();

		/*
		$TBS->LoadTemplate($nom_fichier, OPENTBS_ALREADY_UTF8);

		$TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, $nom_fic);
		*/
		die();

	}
	else {
		header("Location: ../accueil.php?msg=Document non choisi.");
		die();
	}
}


if((isset($id_classe))&&(isset($_POST['is_posted']))&&($_POST['is_posted']==2)) {
	check_token();

	if(($_SESSION['statut']=="professeur")&&(!getSettingAOui('imprimerConvocationConseilClassePP'))) {
		header("Location: imprimer_documents.php?msg=Impression non autorisée.");
		die();
	}
	elseif(($_SESSION['statut']=="cpe")&&(!getSettingAOui('imprimerConvocationConseilClasseCpe'))) {
		header("Location: imprimer_documents.php?msg=Impression non autorisée.");
		die();
	}

	$msg="";

	$date_conseil=isset($_POST['date_conseil']) ? $_POST['date_conseil'] : array();

	/*
	include_once('../mod_ooo/lib/lib_mod_ooo.php');
	include_once('../tbs/tbs_class.php');
	include_once('../tbs/plugins/tbs_plugin_opentbs.php');
	*/
	include_once('../mod_ooo/lib/tinyButStrong.class.php');
	include_once('../mod_ooo/lib/tinyDoc.class.php');

	$acad = $gepiSettings['gepiSchoolAcademie'];
	$etab_anne_scol = $gepiSettings['gepiSchoolName'];
	$etab_nom = $gepiSettings['gepiSchoolName'];
	$etab_adr1 = $gepiSettings['gepiSchoolAdress1'];
	$etab_adr2 = $gepiSettings['gepiSchoolAdress2'];
	$etab_cp = $gepiSettings['gepiSchoolZipCode'];
	$etab_ville = $gepiSettings['gepiSchoolCity'];
	$etab_tel = $gepiSettings['gepiSchoolTel'];
	$etab_fax = $gepiSettings['gepiSchoolFax'];
	$etab_email = $gepiSettings['gepiSchoolEmail'];

	// Envoi des mails
	$nom_fichier_modele_ooo="mail_convocation_conseil_classe.txt";
	if ($_SESSION['rne']!='') {
		$rne=$_SESSION['rne']."/";
	} else {
		$rne='';
	}
	if(!isset($prefixe_generation_hors_dossier_mod_ooo)) {
		$prefixe_generation_hors_dossier_mod_ooo="";
	}
	$nom_dossier_modeles_ooo_par_defaut='modeles_gepi/'; 
	$nom_dossier_modeles_ooo_mes_modeles='mes_modeles/';
	if (file_exists($prefixe_generation_hors_dossier_mod_ooo.$nom_dossier_modeles_ooo_mes_modeles.$rne.$nom_fichier_modele_ooo))   {
		$nom_dossier_modele_a_utiliser = $prefixe_generation_hors_dossier_mod_ooo.$nom_dossier_modeles_ooo_mes_modeles.$rne;
	} else {
		$nom_dossier_modele_a_utiliser = $prefixe_generation_hors_dossier_mod_ooo.$nom_dossier_modeles_ooo_par_defaut;
	}
	$nom_fichier = "../mod_ooo/".$nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo;

	$contenu_modele_mail=file_get_contents($nom_fichier);

/*
__DESIGNATION_DESTINATAIRE__
__CLASSE__
__DATE_CONSEIL__
__LIEU_CONSEIL__
__NOM_ETAB__
__ADR_ETAB__
*/
	$lieu="Salle des conseils";
	// Lieu à mettre dans d_dates_evenements_classes

	$mail_erreur="";
	for($loop=0;$loop<count($id_classe);$loop++) {
		$mail=isset($_POST['mail_'.$id_classe[$loop]]) ? $_POST['mail_'.$id_classe[$loop]] : array();

		$classe=get_nom_classe($id_classe[$loop]);
		for($i=0;$i<count($mail);$i++) {
			$tmp_tab=get_info_user($mail[$i]);
			if(count($tmp_tab)==0) {
				$mail_erreur.="Erreur ($classe) : Le destinataire ".$mail[$i]." n'a pas été trouvé.\n";
			}
			elseif(!check_mail($tmp_tab['email'])) {
				$mail_erreur.="Erreur ($classe) : Email invalide (".$tmp_tab['email'].") pour le destinataire ".$mail[$i].".\n";
			}
			else {
				if(isset($date_conseil[$id_classe[$loop]])) {
					$tmp_date_conseil=formate_date($date_conseil[$id_classe[$loop]],"y2","complet");

					$contenu_mail=preg_replace("/__NOM_ETAB__/", $etab_nom, $contenu_modele_mail);
					$contenu_mail=preg_replace("/__ADR_ETAB__/", $etab_adr1." ".$etab_adr2."\n".$etab_cp." ".$etab_ville, $contenu_mail);
					$contenu_mail=preg_replace("/__CLASSE__/", $classe, $contenu_mail);
					$contenu_mail=preg_replace("/__DATE_CONSEIL__/", $tmp_date_conseil, $contenu_mail);
					$contenu_mail=preg_replace("/__LIEU_CONSEIL__/", $lieu, $contenu_mail);
					$contenu_mail=preg_replace("/__DESIGNATION_DESTINATAIRE__/", $tmp_tab['civ_denomination'], $contenu_mail);

					$subject = "[GEPI]: Convocation conseil de classe de ".$classe;

					$headers = "";
					if(check_mail($etab_email)) {
						$headers.="Reply-to:".$etab_email."\r\n";
						$tab_param_mail['replyto']=$etab_email;
					}

					if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
						//$headers.="Reply-to:".$_SESSION['email']."\r\n";
						$headers.="Bcc:".$_SESSION['email']."\r\n";
						$tab_param_mail['bcc']=$_SESSION['email'];
					}

					$tab_param_mail['destinataire']=$tmp_tab['email'];

					$message_id='convoc_conseil_classe_'.$id_classe[$loop]."_".time();
					if(isset($message_id)) {$headers .= "Message-id: $message_id\r\n";}
					//if(isset($references_mail)) {$headers .= "References: $references_mail\r\n";}

					// On envoie le mail
					$envoi = envoi_mail($subject, $contenu_mail, $tmp_tab['email'], $headers, "plain", $tab_param_mail);
					if(!$envoi) {
						$mail_erreur.="Erreur ($classe) : Erreur lors de l'envoi du mail pour le destinataire ".$mail[$i].".\n";
					}
				}
				else {
					$tmp_date_conseil="DATE INCONNUE";

					$mail_erreur.="Erreur ($classe) : Date du conseil de classe non trouvée. Destinataire non prévenu ".$mail[$i].".\n";
				}
			}
		}
	}
	if($mail_erreur!="") {

		$subject = "[GEPI]: Erreur lors de l envoi des convocations aux conseils de classe";

		$headers = "";
		if(check_mail($etab_email)) {
			$headers.="Reply-to:".$etab_email."\r\n";
			$tab_param_mail['replyto']=$etab_email;
		}

		$mail_dest="";
		if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
			$mail_dest=$_SESSION['email'];
		}
		elseif(check_mail($etab_email)) {
			$mail_dest=$etab_email;
		}
		$tab_param_mail['destinataire']=$mail_dest;

		if($mail_dest!="") {
			$message_id='erreurs_convoc_conseil_classe_'.time();
			if(isset($message_id)) {$headers .= "Message-id: $message_id\r\n";}
			//if(isset($references_mail)) {$headers .= "References: $references_mail\r\n";}

			// On envoie le mail
			$envoi = envoi_mail($subject, $mail_erreur, $mail_dest, $headers, "plain", $tab_param_mail);
		}
	}


	/*
	$_POST['id_classe']=	Array (*)
	$_POST[id_classe]['0']=	33
	$_POST[id_classe]['1']=	34
	$_POST['date_conseil']=	Array (*)
	$_POST[date_conseil]['33']=	2014-06-02 16:45:00
	$_POST[date_conseil]['34']=	2014-06-03 18:00:00
	$_POST['convocation_33']=	Array (*)
	$_POST[convocation_33]['0']=	gaazert
	$_POST[convocation_33]['1']=	beazerty
	$_POST['convocation_34']=	Array (*)
	$_POST[convocation_34]['0']=	biazert
	$_POST[convocation_34]['1']=	deazert
	*/

	// Convocations ODT
	/*
	$dest_civilite="";
	$dest_nom="";
	$dest_prenom="";
	$dest_adr1="";
	$dest_adr2="";
	$dest_adr3="";
	$dest_cp="";
	$dest_ville="";

	$classe="";
	//$date_conseil="";
	*/
	$lieu="Salle des conseils";
	// Lieu à mettre dans d_dates_evenements_classes

	$tab_OOo=array();

	$cpt=0;
	for($loop=0;$loop<count($id_classe);$loop++) {
		$convocation=isset($_POST['convocation_'.$id_classe[$loop]]) ? $_POST['convocation_'.$id_classe[$loop]] : array();

		$classe=get_nom_classe($id_classe[$loop]);
		for($i=0;$i<count($convocation);$i++) {
			$tab_OOo[$cpt]['acad']=$acad;
			$tab_OOo[$cpt]['etab_nom']=$etab_nom;
			$tab_OOo[$cpt]['etab_adr1']=$etab_adr1;
			$tab_OOo[$cpt]['etab_adr2']=$etab_adr2;
			$tab_OOo[$cpt]['etab_cp']=$etab_cp;
			$tab_OOo[$cpt]['etab_ville']=$etab_ville;

			$tab_OOo[$cpt]['classe']=$classe;
			$tab_OOo[$cpt]['lieu']=$lieu;

			//echo "<p>Test \$date_conseil[".$id_classe[$loop]."]</p>";
			if(isset($date_conseil[$id_classe[$loop]])) {
				$tmp_date_conseil=formate_date($date_conseil[$id_classe[$loop]],"y2","complet");
			}
			else {
				$tmp_date_conseil="DATE INCONNUE";
			}
			$tab_OOo[$cpt]['date_conseil']=$tmp_date_conseil;

			$tmp_tab=get_info_user($convocation[$i]);
			if(count($tmp_tab)==0) {
				$tmp_tab['nom']="NOM_INCONNU";
				$tmp_tab['prenom']="PRENOM_INCONNU";
				$tmp_tab['adresse']['adr1']="ADR1";
				$tmp_tab['adresse']['adr2']="";
				$tmp_tab['adresse']['adr3']="";
				$tmp_tab['adresse']['cp']="";
				$tmp_tab['adresse']['commune']="VILLE";
			}
			elseif($tmp_tab['statut']=='eleve') {
				// Récupérer l'adresse du 1er parent

				$tmp_tab['adresse']['adr1']="ADR1_PARENT";
				$tmp_tab['adresse']['adr2']="";
				$tmp_tab['adr3']="";
				$tmp_tab['adresse']['cp']="";
				$tmp_tab['adresse']['commune']="VILLE_PARENT";

				$sql="SELECT ra.* FROM resp_adr ra, resp_pers rp, responsables2 r WHERE ra.adr_id=rp.adr_id AND rp.pers_id=r.pers_id AND r.ele_id='".$tmp_tab['ele_id']."' AND r.resp_legal!='0' ORDER BY r.resp_legal;";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					$lig=mysqli_fetch_object($res);

					$tmp_tab['adresse']['adr1']=$lig->adr1;
					$tmp_tab['adresse']['adr2']=$lig->adr2;
					$tmp_tab['adresse']['adr3']=$lig->adr3;
					$tmp_tab['adresse']['cp']=$lig->cp;
					$tmp_tab['adresse']['commune']=$lig->commune;
				}
			}

			$tab_OOo[$cpt]['dest_civilite']=$tmp_tab['civilite'];
			$tab_OOo[$cpt]['dest_nom']=$tmp_tab['nom'];
			$tab_OOo[$cpt]['dest_prenom']=$tmp_tab['prenom'];
			$tab_OOo[$cpt]['dest_adr1']=$tmp_tab['adresse']['adr1'];
			$tab_OOo[$cpt]['dest_adr2']=$tmp_tab['adresse']['adr2'];
			$tab_OOo[$cpt]['dest_adr3']=$tmp_tab['adresse']['adr3'];
			$tab_OOo[$cpt]['dest_cp']=$tmp_tab['adresse']['cp'];
			$tab_OOo[$cpt]['dest_ville']=$tmp_tab['adresse']['commune'];

			$cpt++;
		}
	}

	/*
	$TBS = new clsTinyButStrong; // new instance of TBS
	$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin
	*/

	// Load the template
	$nom_fichier_modele_ooo ='convocation_conseil_classe.odt';
	$prefixe_generation_hors_dossier_mod_ooo="../mod_ooo/";

	//Procédure du traitement à effectuer
	//les chemins contenant les données
	include_once ("../mod_ooo/lib/chemin.inc.php");

	$nom_fichier = "../mod_ooo/".$nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo;

	//Génération du nom du fichier
	$now = gmdate('d_M_Y_H:i:s');
	$nom_fichier_modele = explode('.',$nom_fichier_modele_ooo);
	$nom_fic = remplace_accents($nom_fichier_modele[0]."_".$classe."_"."_généré_le_".$now.".".$nom_fichier_modele[1],'all');
	// Je n'arrive pas à générer un fichier à ce nom.
	// Problème de syntaxe tinyButStrong?


	// Création d'une classe tinyDoc
	$OOo = new tinyDoc();

	// Choix du module de dézippage
	$dezippeur=getSettingValue("fb_dezip_ooo");
	if ($dezippeur==1){
		$OOo->setZipMethod('shell');
		$OOo->setZipBinary('zip');
		$OOo->setUnzipBinary('unzip');
	}
	else{
		$OOo->setZipMethod('ziparchive');
	}

	$tempdir=get_user_temp_directory();
	$nom_dossier_temporaire = "../temp/".$tempdir;
	$nom_fichier_xml_a_traiter ='content.xml';

	$OOo->SetProcessDir($nom_dossier_temporaire);
	$OOo->createFrom($nom_fichier);
	//$OOo->createFrom($nom_fichier);
	$OOo->loadXml($nom_fichier_xml_a_traiter);

	// Traitement des tableaux
	$OOo->mergeXml(
	array(
	'name'      => 'var',
	'type'      => 'block',
	'data_type' => 'array',
	'charset'   => 'UTF-8'
	),$tab_OOo);

	$OOo->SaveXml(); //traitement du fichier extrait

	$OOo->sendResponse(); //envoi du fichier traité
	$OOo->remove(); //suppression des fichiers de travail
	// Fin de traitement des tableaux
	$OOo->close();

	/*
	$TBS->LoadTemplate($nom_fichier, OPENTBS_ALREADY_UTF8);

	$TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, $nom_fic);
	*/
	die();
}

if((isset($id_classe[0]))&&(isset($_GET['imprimer_liste_eleve']))&&(isset($_GET['destinataire']))) {
	check_token();

	$msg="";

	include_once('../mod_ooo/lib/tinyButStrong.class.php');
	include_once('../mod_ooo/lib/tinyDoc.class.php');

	/*
	$acad = $gepiSettings['gepiSchoolAcademie'];
	$etab_anne_scol = $gepiSettings['gepiSchoolName'];
	$etab_nom = $gepiSettings['gepiSchoolName'];
	$etab_adr1 = $gepiSettings['gepiSchoolAdress1'];
	$etab_adr2 = $gepiSettings['gepiSchoolAdress2'];
	$etab_cp = $gepiSettings['gepiSchoolZipCode'];
	$etab_ville = $gepiSettings['gepiSchoolCity'];
	$etab_tel = $gepiSettings['gepiSchoolTel'];
	$etab_fax = $gepiSettings['gepiSchoolFax'];
	$etab_email = $gepiSettings['gepiSchoolEmail'];
	*/

	$classe=get_nom_classe($id_classe[0]);

	// Listes élèves

	$tab_OOo=array();

	$ajout_sql="";
	if(isset($_GET['periode'])) {
		$ajout_sql=" AND jec.periode='".$_GET['periode']."'";
	}

	$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='".$id_classe[0]."' AND jec.login=e.login".$ajout_sql." ORDER BY e.nom, e.prenom;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			$tab_OOo[$cpt]['designation_eleve']=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
			$cpt++;
		}
	}

	// Load the template
	$nom_fichier_modele_ooo ='liste_eleve_conseil_classe.odt';
	$prefixe_generation_hors_dossier_mod_ooo="../mod_ooo/";

	//Procédure du traitement à effectuer
	//les chemins contenant les données
	include_once ("../mod_ooo/lib/chemin.inc.php");

	$nom_fichier = "../mod_ooo/".$nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo;

	//Génération du nom du fichier
	$now = gmdate('d_M_Y_H:i:s');
	$nom_fichier_modele = explode('.',$nom_fichier_modele_ooo);
	$nom_fic = remplace_accents($nom_fichier_modele[0]."_".$classe."_"."_généré_le_".$now.".".$nom_fichier_modele[1],'all');
	// Je n'arrive pas à générer un fichier à ce nom.
	// Problème de syntaxe tinyButStrong?


	// Création d'une classe tinyDoc
	$OOo = new tinyDoc();

	// Choix du module de dézippage
	$dezippeur=getSettingValue("fb_dezip_ooo");
	if ($dezippeur==1){
		$OOo->setZipMethod('shell');
		$OOo->setZipBinary('zip');
		$OOo->setUnzipBinary('unzip');
	}
	else{
		$OOo->setZipMethod('ziparchive');
	}

	$tempdir=get_user_temp_directory();
	$nom_dossier_temporaire = "../temp/".$tempdir;
	$nom_fichier_xml_a_traiter ='content.xml';

	$OOo->SetProcessDir($nom_dossier_temporaire);
	$OOo->createFrom($nom_fichier);
	//$OOo->createFrom($nom_fichier);
	$OOo->loadXml($nom_fichier_xml_a_traiter);

	// Traitement des tableaux
	$OOo->mergeXml(
	array(
	'name'      => 'var',
	'type'      => 'block',
	'data_type' => 'array',
	'charset'   => 'UTF-8'
	),$tab_OOo);

	$OOo->SaveXml(); //traitement du fichier extrait


	$OOo->sendResponse(); //envoi du fichier traité
	$OOo->remove(); //suppression des fichiers de travail
	// Fin de traitement des tableaux
	$OOo->close();

	die();
}

// ===================== entete Gepi ======================================//
$titre_page = "Imprimer documents";
require_once("../lib/header.inc.php");
// ===================== fin entete =======================================//

//debug_var();

echo "<p class='bold'><a href='";
if((isset($_SESSION['page_origine']))&&($_SESSION['page_origine']=="bulletins")&&(acces_impression_bulletin("",""))) {
	echo "../bulletin/bull_index.php";
}
else {
	echo "../accueil.php";
}
echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if(acces("/mod_engagements/index_admin.php", $_SESSION['statut'])) {
	echo " | <a href='index_admin.php'>Définir les types d'engagements</a>";
}

if(acces("/mod_engagements/saisie_engagements.php", $_SESSION['statut'])) {
	echo " | <a href='saisie_engagements.php'>Saisir les engagements</a>";
}
elseif(($_SESSION['statut']=='professeur')&&(is_pp($_SESSION['login']))) {
	// Tester s'il y a des droits de saisie pour les PP
	$sql="SELECT 1=1 FROM engagements WHERE SaisiePP='yes';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		echo " | <a href='saisie_engagements.php'>Saisir les engagements</a>";
	}
}

$acces_imprimerConvocationConseilClasse=true;
if(($_SESSION['statut']=="professeur")&&(!getSettingAOui('imprimerConvocationConseilClassePP'))) {
	$acces_imprimerConvocationConseilClasse=false;
}
elseif(($_SESSION['statut']=="cpe")&&(!getSettingAOui('imprimerConvocationConseilClasseCpe'))) {
	$acces_imprimerConvocationConseilClasse=false;
}

if($_SESSION['statut']=='professeur') {
	$tab_pp=get_tab_ele_clas_pp($_SESSION['login']);
	if(count($tab_pp['id_classe'])==0) {
		echo "</p>

<p>Vous n'êtes ".getSettingValue('gepi_prof_suivi')." d'aucune classe.</p>";
		require_once("../lib/footer.inc.php");
		die();
	}
	elseif(count($tab_pp['id_classe'])==1) {
		$id_classe=array();
		$id_classe[0]=$tab_pp['id_classe'][0];
	}
	else {
		if(!isset($id_classe)) {
			$suite="n";
		}
		else {
			$suite="y";
			for($loop=0;$loop<count($id_classe);$loop++) {
				if(!in_array($id_classe[$loop], $tab_pp['id_classe'])) {
					echo "</p>\n";
					$gepi_prof_suivi=ucfirst(retourne_denomination_pp($id_classe[$loop]));
					echo "<p style='color:red'>Vous n'êtes pas ".$gepi_prof_suivi." de la classe de ".get_nom_classe($id_classe[$loop]);
					$suite="n";
					break;
				}
			}
		}

		if($suite=="n") {
			echo "</p>\n";

			echo "<p class='bold'>Choix des classes&nbsp;:</p>\n";

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

			for($loop=0;$loop<count($tab_pp['id_classe']);$loop++) {
				echo "<label id='label_tab_id_classe_$loop' for='tab_id_classe_$loop' style='cursor: pointer;'><input type='checkbox' name='id_classe[]' id='tab_id_classe_$loop' value='".$tab_pp['id_classe'][$loop]."' onchange='change_style_classe($loop)' /> ".$tab_pp['classe'][$loop]."</label>";
				echo "<br />\n";
			}

			echo "<p><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";

			echo "<p><br /></p>";
			require_once("../lib/footer.inc.php");
			die();
		}
	}
}

if(!isset($id_classe)) {
	// On n'arrive dans cette partie qu'en administrateur, cpe ou scolarite
	echo "</p>\n";

	echo "<p class='bold'>Choix des classes&nbsp;:</p>\n";

	// Liste des classes avec élève:
	$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) ORDER BY c.classe;";
	$call_classes=mysqli_query($GLOBALS["mysqli"], $sql);

	$nb_classes=mysqli_num_rows($call_classes);
	if($nb_classes==0){
		echo "<p>Aucune classe avec élève affecté n'a été trouvée.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	// Affichage sur 3 colonnes
	$nb_classes_par_colonne=round($nb_classes/3);

	echo "<table width='100%' summary='Choix des classes'>\n";
	echo "<tr valign='top' align='center'>\n";

	$cpt = 0;

	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";

	while($lig_clas=mysqli_fetch_object($call_classes)) {

		//affichage 2 colonnes
		if(($cpt>0)&&(round($cpt/$nb_classes_par_colonne)==$cpt/$nb_classes_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='id_classe[]' id='tab_id_classe_$cpt' value='$lig_clas->id' onchange='change_style_classe($cpt)' /> $lig_clas->classe</label>";
		echo "<br />\n";
		$cpt++;
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p><br /></p>

<p style='text-indent:-4em;margin-left:4em;'><em>NOTE&nbsp;:</em> Pour le moment, seuls les documents concernant les conseils de classe sont proposés ici.</p>

<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('tab_id_classe_'+k)){
				document.getElementById('tab_id_classe_'+k).checked = mode;
				change_style_classe(k);
			}
		}
	}

	function change_style_classe(num) {
		if(document.getElementById('tab_id_classe_'+num)) {
			if(document.getElementById('tab_id_classe_'+num).checked) {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";
	require("../lib/footer.inc.php");
	die();
}

//==============================================================================

echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>";

$acces_saisie_engagements="n";
if($_SESSION['statut']=='administrateur') {
	$acces_saisie_engagements="y";
}
elseif($_SESSION['statut']=='cpe') {
	$tab_engagements_saisie_cpe=get_tab_engagements("", "cpe");
	if(count($tab_engagements_saisie_cpe['indice'])>0) {
		$acces_saisie_engagements="y";
	}
}
elseif($_SESSION['statut']=='scolarite') {
	$tab_engagements_saisie_scol=get_tab_engagements("", "scolarite");
	if(count($tab_engagements_saisie_scol['indice'])>0) {
		$acces_saisie_engagements="y";
	}
}
if($acces_saisie_engagements=="y") {
	echo " | <a href='saisie_engagements.php'>Saisie des engagements</a>";
}

if(acces("/mod_ooo/gerer_modeles_ooo.php", $_SESSION['statut'])) {
	echo " | <a href='../mod_ooo/gerer_modeles_ooo.php#MODULE_Engagements'>Modifier les modèles de documents</a>";
}

echo "</p>\n";

// On ne va pas imprimer des documents pour des conseil_de_classe passés
//$date_limite=strftime("%Y-%m-%d %H:%M:%S");
$date_limite=strftime("%Y-%m-%d")." 00:00:00";
// Pour debug/devel
//$date_limite="2014-06-01 00:00:00";

// Chercher s'il y a des conseils de classe à venir
for($i=0;$i<count($id_classe);$i++) {
	$dates_conseils[$id_classe[$i]]=array();
	$sql="SELECT * FROM d_dates_evenements dde, 
				d_dates_evenements_classes ddec 
			WHERE dde.id_ev=ddec.id_ev AND 
				ddec.id_classe='".$id_classe[$i]."' AND 
				date_evenement>='".$date_limite."' 
				ORDER BY ddec.date_evenement;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$dates_conseils[$id_classe[$i]][]=$lig->date_evenement;
		}
	}
}
/*
echo "<pre>";
print_r($dates_conseils);
echo "</pre>";
*/

$msg_scorie="";
echo "<p class='bold'>Choisissez&nbsp;:</p>\n";

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire' target='_blank'>
	<fieldset class='fieldset_opacite50'>
		<input type='hidden' name='is_posted' value='2' />
		".add_token_field();

$cpt1=0;
$cpt2=0;
for($i=0;$i<count($id_classe);$i++) {
	$tab_engagements_classe=get_tab_engagements_user("", $id_classe[$i]);
	/*
	echo "<pre>";
	print_r($tab_engagements_classe);
	echo "</pre>";
	*/
	echo "
		<p class='bold'>Classe de ".get_nom_classe($id_classe[$i])."</p>
		<input type='hidden' name='id_classe[]' value='".$id_classe[$i]."' />
		<div style='margin-left:3em; margin-bottom:0.5em; padding:0.5em;' class='fieldset_opacite50'>

		<p>Prochain conseil de classe&nbsp;:<br />";
	if(count($dates_conseils[$id_classe[$i]])==0) {
		echo "<span style='style='color:red'>Aucune date de conseil de classe n'est définie.</span>";
		if(acces("/classes/dates_classes.php" ,$_SESSION['statut'])) {
			echo "<br /><a href='../classes/dates_classes.php'>Définir les dates de conseils de classe</a>";
		}
		else {
			echo "<br />Un utilisateur autorisé à saisir les événements classe doit définir la date des conseils de classe.";
		}
	}
	else {
		// On ne devrait avoir qu'un conseil de classe programmé précisément dans le futur pour chaque classe
		for($j=0;$j<count($dates_conseils[$id_classe[$i]]);$j++) {
			$checked="";
			if($j==0) {
				$checked=" checked";
			}
			$ts=mysql_date_to_unix_timestamp($dates_conseils[$id_classe[$i]][$j]);
			echo "
		<input type='radio' name='date_conseil[".$id_classe[$i]."]' id='date_conseil_".$id_classe[$i]."_".$ts."' value='".$dates_conseils[$id_classe[$i]][$j]."'$checked /><label for='date_conseil_".$id_classe[$i]."_".$ts."' id='texte_date_conseil_".$id_classe[$i]."_".$ts."'>".formate_date($dates_conseils[$id_classe[$i]][$j], "y", "complet")."</label><br />";
		}
	}
	echo "</p>";

	for($j=0;$j<count($tab_engagements['indice']);$j++) {
		if($tab_engagements['indice'][$j]['conseil_de_classe']=='yes') {
			$current_id_engagement=$tab_engagements['indice'][$j]['id'];
			if((!isset($tab_engagements_classe['id_engagement_user'][$current_id_engagement]))||(count($tab_engagements_classe['id_engagement_user'][$current_id_engagement])==0)) {
				echo "
		<p><strong>".$tab_engagements['indice'][$j]['nom']."&nbsp;:</strong> Aucun n'est choisi.<br />";
			}
			else {
				echo "
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th>".$tab_engagements['indice'][$j]['nom']."</th>
					<th>Convocation</th>
					<th>Mail</th>
					<th>Listes élèves pour prise de notes</th>
					<th>Informations</th>
				</tr>
			</thead>
			<tbody>";
				foreach($tab_engagements_classe['id_engagement_user'][$current_id_engagement] as $key => $value) {
					$current_user=get_info_user($value);
					/*
					echo "\$value=$value<br />";
					echo "<pre>";
					print_r($current_user);
					echo "</pre>";
					*/
					if(count($current_user)>0) {
						echo "
				<tr>
					<td>".$current_user['civ_denomination']."</td>
					<td>";
						if(count($dates_conseils[$id_classe[$i]])>0) {
							if($acces_imprimerConvocationConseilClasse) {
								echo "<input type='checkbox' name='convocation_".$id_classe[$i]."[]' id='convocation_$cpt1' value=\"$value\" />";
							}
							else {
								echo "<img src='../images/disabled.png' class='icone20' alt='Non autorisé' title=\"Les comptes '".$_SESSION['statut']."s' ne sont pas autorisés à imprimer les convocations.\" >";
							}
						}
						else {
							echo "<img src='../images/disabled.png' class='icone20' alt='Pas de date' title=\"Aucune date de conseil de classe n'est saisie.\" >";
						}
						echo "</td>
					<td>";
						if(count($dates_conseils[$id_classe[$i]])>0) {
							$current_mail=get_mail_user($value);
							if($acces_imprimerConvocationConseilClasse) {
								if(check_mail($current_mail)) {
									echo "<input type='checkbox' name='mail_".$id_classe[$i]."[]' id='mail_$cpt1' value=\"$value\" />";
								}
								else {
									echo "<img src='../images/disabled.png' class='icone20' alt='Mail non valide' title=\"Mail non valide : '".$current_mail."'\" >";
								}
							}
							else {
								echo "<img src='../images/disabled.png' class='icone20' alt='Non autorisé' title=\"Le comptes '".$_SESSION['statut']."s' ne sont pas autorisés à envoyer les convocations par mail.\" >";
							}
						}
						else {
							echo "<img src='../images/disabled.png' class='icone20' alt='Pas de date' title=\"Aucune date de conseil de classe n'est saisie.\" >";
						}

						$infos_supplementaires="";
						if($current_user['statut']=='responsable') {
							$infos_supplementaires=affiche_infos_adresse_et_tel("", $current_user);
						}
						//get_info_responsable($login_resp)
						//get_info_user($login_resp);
						//GepiAccesGestElevesProfP
						//GepiAccesGestElevesProf
						//GepiAccesGestElevesProfesseur n'existe pas mais serait testé par AccesInfoResp()
						// AccesInfoResp("GepiAccesGestEleves", $value)

						echo "</td>
					<td>
						<!-- Je ne vois pas comment générer d'un coup un fichier ODT/ODS avec les listes d'élèves à distribuer aux différents délégués -->
						<!--input type='checkbox' name='liste_eleve_".$id_classe[$i]."' id='liste_eleve_$cpt2' value=\"$value\" /-->
						<a href='".$_SERVER['PHP_SELF']."?id_classe[0]=".$id_classe[$i]."&amp;imprimer_liste_eleve=y&amp;destinataire=$value".add_token_in_url()."' target='_blank'><img src='../images/icons/print.png' class='icone16' alt='Imprimer' /></a>
					</td>
					<td>
						$infos_supplementaires
					</td>
				</tr>";
						// Ajouter un lien vers une page avec les infos dans le cas resp pour les PP avec GepiAccesGestElevesProfP, pour les scol,...
						if(count($dates_conseils[$id_classe[$i]])>0) {
							$cpt1++;
						}
						$cpt2++;
					}
					else {
						$msg_scorie.="Il semble qu'il reste des scories concernant le login $value (<em>vous devriez effectuer un nettoyage des tables</em>).<br />";
					}
				}
				echo "
			</tbody>
		</table>";

			}
			echo "
		<br />";
		}

	}
	echo "
		</div>";
}

if($msg_scorie!="") {
	echo "
<p style='color:red'>$msg_scorie</p>";
}

if($acces_imprimerConvocationConseilClasse) {
	echo "
		<p>
			<a href='#' onClick=\"ModifCase('convocation',true);return false;\">Cocher toutes les convocations</a> / <a href='#' onClick=\"ModifCase('convocation',false);return false;\">décocher toutes les convocations</a><br />
			<a href='#' onClick=\"ModifCase('mail',true);return false;\">Cocher toutes les mails</a> / <a href='#' onClick=\"ModifCase('mail',false);return false;\">décocher toutes les mails</a><br />
			<!--a href='#' onClick=\"ModifCase('liste_eleve',true);return false;\">Cocher toutes les listes d'élèves</a> / <a href='#' onClick=\"ModifCase('liste_eleve',false);return false;\">décocher toutes les listes d'élèves</a><br /-->
		</p>

		<p><input type='submit' value='Valider' /></p>
		<div id='fixe'><input type='submit' value='Valider' /></div>
	</fieldset>
</form>

<script type='text/javascript'>
	function ModifCase(categorie, mode) {
		for (var k=0;k<$cpt2;k++) {
			if(document.getElementById(categorie+'_'+k)){
				document.getElementById(categorie+'_'+k).checked = mode;
			}
		}
	}
</script>";
}
else {
	echo "	</fieldset>
</form>";
}

require_once("../lib/footer.inc.php");
?>
