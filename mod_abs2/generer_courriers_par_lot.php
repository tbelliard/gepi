<?php
/**
 *
 * @version $Id$
 *
 * Copyright 2010 Josselin Jacquard, Régis Bouguin
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
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

/*
$sql="INSERT INTO `droits` (`id`, `administrateur`, `professeur`, `cpe`, `scolarite`, `eleve`, `responsable`, `secours`, `autre`, `description`, `statut`) 
      VALUES ('/mod_abs2/generer_notification_par_lot.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Génération groupée des courriers', '')
      ON DUPLICATE KEY UPDATE `cpe` = 'V'";

$result = mysql_query($sql);

if (!$result){
  echo "Echec ouverture des droits sur la page generer_courriers_par_lot.php";
    die();
}
*/

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activé.");
}

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite") {
    die("acces interdit");
}

include_once 'lib/function.php';



//récupération des courriers non éditer
$sql="SELECT `id` FROM `a_notifications` 
        WHERE (`type_notification`='".AbsenceEleveNotification::$TYPE_COURRIER."'
		  OR `type_notification`='".AbsenceEleveNotification::$TYPE_COURRIER_PAR_LOT."')
		AND `statut_envoi`='".AbsenceEleveNotification::$STATUT_INITIAL."'";

$result = mysql_query($sql);

if (!$result){
  echo "Echec recherche des courriers à imprimer<br/>";
    die();
}

if (!mysql_num_rows($result)){
  $message_enregistrement = "il n'y a pas de courrier à imprimer";
	include('liste_notifications.php');
  die();
}
if (mysql_num_rows($result)){
  //echo "il y a ".mysql_num_rows($result)." courriers à imprimer<br />";
}


// load the TinyButStrong libraries
if (version_compare(PHP_VERSION,'5')<0) {
    include_once('../tbs/tbs_class.php'); // TinyButStrong template engine for PHP 4
} else {
    include_once('../tbs/tbs_class_php5.php'); // TinyButStrong template engine
}
include_once('../tbs/plugins/tbsdb_php.php');

$TBS = new clsTinyButStrong; // new instance of TBS

include_once('../tbs/plugins/tbs_plugin_opentbs.php');
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin

// Load the template
$modele_lettre_parents=repertoire_modeles("modele_lettre_parents_groupe.odt");
$TBS->LoadTemplate($modele_lettre_parents);

$tab_eleves_OOo=array();
$nb_eleve=0;

while ($row = mysql_fetch_array($result)) {
  $id_notification=$row[0];
  //récupération des paramètres de la requète

  $message_enregistrement = '';
  $notification = AbsenceEleveNotificationQuery::create()->findPk($id_notification);
  if ($notification == null && !isset($_POST["creation_notification"])) {
	  $message_enregistrement .= 'Generation impossible : notification non trouvée. ';
	  include("visu_notification.php");
	  die();
  }
  if (($notification->getTypeNotification() != AbsenceEleveNotification::$TYPE_COURRIER) && $notification->getTypeNotification() != AbsenceEleveNotification::$TYPE_COURRIER_PAR_LOT) {
	  $message_enregistrement .= 'Génération impossible : type de notification incompatible. ';
	  include("visu_notification.php");
	  die();

  }
  //on va mettre les champs dans un tableau
  $tab_eleves_OOo[]=array();  
  
  if ($notification->getResponsableEleveAdresse() != null && $notification->getResponsableEleveAdresse()->getResponsableEleves()->count() == 1) {
	//echo 'dest1';
	$responsable = $notification->getResponsableEleveAdresse()->getResponsableEleves()->getFirst();
	$destinataire = $responsable->getCivilite().' '.strtoupper($responsable->getNom()).' '.strtoupper($responsable->getPrenom());
  } elseif ($notification->getResponsableEleveAdresse() != null) {
	//echo 'dest2';
	$responsable = $notification->getResponsableEleveAdresse()->getResponsableEleves()->getFirst();
	$destinataire = $responsable->getCivilite().' '.strtoupper($responsable->getNom());
	$responsable = $notification->getResponsableEleveAdresse()->getResponsableEleves()->getNext();
	$destinataire .= '  '.strtoupper($responsable->getCivilite()).' '.strtoupper($responsable->getNom());;
  } else {
	$destinataire = '';
  }
  $tab_eleves_OOo[$nb_eleve]['destinataire'] = $destinataire;

  $adr = $notification->getResponsableEleveAdresse();
  if ($adr == null) {
	$adr = new ResponsableEleveAdresse();
  }
  $tab_eleves_OOo[$nb_eleve]['adr'] = $adr;
	
  $tab_eleves_OOo[$nb_eleve]['nom_etab'] = getSettingValue("gepiSchoolName");
  
  $adr_etablissement = new ResponsableEleveAdresse();
  $adr_etablissement->setAdr1(getSettingValue("gepiSchoolAdress1"));
  $adr_etablissement->setAdr2(getSettingValue("gepiSchoolAdress2"));
  $adr_etablissement->setCp(getSettingValue("gepiSchoolZipCode"));
  $adr_etablissement->setCommune(getSettingValue("gepiSchoolCity"));
  $tab_eleves_OOo[$nb_eleve]['adr_etab'] = $adr_etablissement;
  
  
    //telephone fax mail
  $tab_eleves_OOo[$nb_eleve]['tel_etab'] = getSettingValue("gepiSchoolTel");
  $tab_eleves_OOo[$nb_eleve]['fax_etab'] = getSettingValue("gepiSchoolFax");

    $email_abs_etab = getSettingValue("gepiAbsenceEmail");
    if ($email_abs_etab == null || $email_abs_etab == '') {
	$email_abs_etab = getSettingValue("gepiSchoolEmail");
    }
  $tab_eleves_OOo[$nb_eleve]['mail_etab'] = $email_abs_etab;

  $tab_eleves_OOo[$nb_eleve]['notif_id'] = $notification->getId();

    //on récupère la liste des noms d'eleves
  $eleve_col = new PropelCollection();
  foreach ($notification->getAbsenceEleveTraitement()->getAbsenceEleveSaisies() as $saisie) {
	$eleve_col->add($saisie->getEleve());
  }

  $tab_eleves_OOo[$nb_eleve]['el_col'] = $eleve_col;
  
  $tab_eleves_OOo[$nb_eleve]['id_trait']=$notification->getAbsenceEleveTraitement()->getId();
	
  $nb_eleve=$nb_eleve+1;
  
	// On met les notifications à jour
  $notification->setTypeNotification(AbsenceEleveNotification::$TYPE_COURRIER);
  $notification->setDateEnvoi('now');
  $notification->setStatutEnvoi(AbsenceEleveNotification::$STATUT_EN_COURS);
  $notification->save();
} 


$TBS->MergeBlock('eleves',$tab_eleves_OOo);

  $query_string = 'AbsenceEleveSaisieQuery::create()->filterByEleveId(%p1%)
  ->useJTraitementSaisieEleveQuery()
  ->filterByATraitementId(%p2%)->endUse()
	  ->orderBy("DebutAbs", Criteria::ASC)
	  ->find()';
  
   $TBS->MergeBlock('saisies', 'php', $query_string);

    // Output as a download file (some automatic fields are merged here)
    $TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, 'abs_notif.'.date("d_m_y.H_i").'.odt');

	// On met les notifications à jour après la sortie du fichier .odt

	
include('liste_notifications.php');

die();


// utiliser pour formater certain champs dans les modele tbs
function tbs_str($FieldName,&$CurrRec) {
    $CurrRec = html_entity_decode($CurrRec,ENT_QUOTES);
    $CurrRec = str_replace('\"','"',str_replace("\'","'",$CurrRec));
    $CurrRec = str_replace('\\'.htmlspecialchars('"',ENT_QUOTES),htmlspecialchars('"',ENT_QUOTES),str_replace("\\".htmlspecialchars("'",ENT_QUOTES),htmlspecialchars("'",ENT_QUOTES),$CurrRec));

    $CurrRec = stripslashes($CurrRec);
}
?>
