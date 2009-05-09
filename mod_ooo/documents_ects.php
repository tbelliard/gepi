<?php
/*
 * $Id: saisie_avis.php 2147 2008-07-23 09:01:04Z tbelliard $
 *
 * Copyright 2001, 2009 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

include_once('./lib/lib_mod_ooo.php');

include_once('./lib/tbs_class.php');
include_once('./lib/tbsooo_class.php');
define( 'PCLZIP_TEMPORARY_DIR', '../mod_ooo/tmp/' );
include_once('../lib/pclzip.lib.php');


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// On teste si un professeur principal peut effectuer l'édition
if (($_SESSION['statut'] == 'professeur') and $gepiSettings["GepiAccesEditionDocsEctsPP"] !='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

// On teste si le service scolarité peut effectuer la saisie
if (($_SESSION['statut'] == 'scolarite') and $gepiSettings["GepiAccesEditionDocsEctsScolarite"] !='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : false;
$choix_edit = isset($_POST["choix_edit"]) ? $_POST["choix_edit"] : false;
$login_eleve = isset($_POST["login_eleve"]) ? $_POST["login_eleve"] : false;
$releve = isset($_POST['releve']) ? true : false;
$attestation = isset($_POST['attestation']) ? true : false;
$description = isset($_POST['description']) ? true : false;
$annee_derniere = isset($_POST['annee_derniere']) ? $_POST['annee_derniere'] : false;

// On va générer un gros tableau avec toutes les données.

// Tableau global
$eleves = array();

if ($id_classe == 'all') {
    // On doit récupérer la totalité des élèves, pour les classes de l'utilisateur
    if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {
        // On ne sélectionne que les classes qui ont au moins un enseignement ouvrant à crédits ECTS
        if($_SESSION['statut']=='scolarite'){
            $call_classes = mysql_query("SELECT DISTINCT c.id
                                        FROM classes c, periodes p, j_scol_classes jsc, j_groupes_classes jgc
                                        WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' AND c.id=jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
        } else {
            $call_classes = mysql_query("SELECT DISTINCT c.id FROM classes c, periodes p, j_groupes_classes jgc WHERE p.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
        }
    } else {
        $call_classes = mysql_query("SELECT DISTINCT c.id FROM classes c, j_eleves_professeurs s, j_eleves_classes cc, j_groupes_classes jgc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE)");
    }
    $nb_classes = mysql_num_rows($call_classes);
    $Eleves = array();
    for($i=0;$i<$nb_classes;$i++) {
        $Classe = ClassePeer::retrieveByPK(mysql_result($call_classes, $i, 'id'));
        $Eleves = array_merge($Eleves,$Classe->getEleves('1'));
    }
} else {
    if ($choix_edit && $choix_edit == '2') {
        $Eleves = array();
        $Eleves[] = ElevePeer::retrieveByLOGIN($login_eleve);
    } else {
        $Classe = ClassePeer::retrieveByPK($id_classe);
        $Eleves = $Classe->getEleves('1');
    }
}

$i = 0;
foreach($Eleves as $Eleve) {
    // On est dans la boucle principale. Le premier tableau contient les informations relatives à l'élève.
    // C'est le premier bloc.
    $eleves[$i] = array('nom' => $Eleve->getNom(), 'prenom' => $Eleve->getPrenom(), 'ine' => $Eleve->getNoGep(), 'parcours' => 'A préciser');

    // Pour les semestre 1 et 2, on doit passer par les archives
    $semestre1[$i] = array();
    foreach($Eleve->getArchivedEctsCredits($annee_derniere, '1') as $Credit) {
        $valeur = $Credit ? $Credit->getValeur() : 'Non saisie';
        $mention = $Credit ? $Credit->getMention() : 'Non saisie';
        $semestre1[$i][] = array(
                            'discipline' => $Credit->getMatiere(),
                            'ects_credit' => $valeur,
                            'ects_mention' => $mention);
    }

    $semestre2[$i] = array();
    foreach($Eleve->getArchivedEctsCredits($annee_derniere, '2') as $Credit) {
        $valeur = $Credit ? $Credit->getValeur() : 'Non saisie';
        $mention = $Credit ? $Credit->getMention() : 'Non saisie';
        $semestre2[$i][] = array(
                            'discipline' => $Credit->getMatiere(),
                            'ects_credit' => $valeur,
                            'ects_mention' => $mention);
    }

    // On s'occupe des semestres 3 et 4:
    $semestre3[$i] = array();
    foreach($Eleve->getEctsGroupes('1') as $Group) {
        $Credit = $Eleve->getEctsCredit('1',$Group->getId());
        $valeur = $Credit ? $Credit->getValeur() : 'Non saisie';
        $mention = $Credit ? $Credit->getMention() : 'Non saisie';

        $semestre3[$i][] = array(
                            'discipline' => $Group->getDescription(),
                            'ects_credit' => $valeur,
                            'ects_mention' => $mention);
    }
    $semestre4[$i] = array();
    foreach($Eleve->getEctsGroupes('2') as $Group) {
        $Credit = $Eleve->getEctsCredit('2',$Group->getId());
        $valeur = $Credit ? $Credit->getValeur() : 'Non saisie';
        $mention = $Credit ? $Credit->getMention() : 'Non saisie';
        $semestre4[$i][] = array(
                            'discipline' => $Group->getDescription(),
                            'ects_credit' => $valeur,
                            'ects_mention' => $mention);
    }



    $i++;
}


// Et maintenant on s'occupe du fichier proprement dit

//
//Les variables à modifier pour le traitement  du modèle ooo
//
//Le chemin et le nom du fichier ooo à traiter (le modèle de document)
$nom_fichier_modele_ooo ='documents_ects.odt';
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



// Traitement des tableaux
// On insère ici les lignes concernant la gestion des tableaux
if (!$releve) {
    $OOo->MergeBlock('releve','clear');
} else {
    $OOo->MergeBlock('releve',array('fake')); // Juste pour que le bloc s'initialise correctement
}
if (!$attestation) {
    $OOo->MergeBlock('attestation','clear');
} else {
    $OOo->MergeBlock('attestation',array('fake')); // Juste pour que le bloc s'initialise correctement
}
if (!$description) {
    $OOo->MergeBlock('description','clear');
} else {
    $OOo->MergeBlock('description',array('fake')); // Juste pour que le bloc s'initialise correctement
}

$OOo->MergeBlock('eleves',$eleves);

// On insère les semestres
$OOo->MergeBlock('sem1','array','semestre1[%p1%]');
$OOo->MergeBlock('sem2','array','semestre2[%p1%]');
$OOo->MergeBlock('sem3','array','semestre3[%p1%]');
$OOo->MergeBlock('sem4','array','semestre4[%p1%]');
// Fin de traitement des tableaux


$OOo->SaveXmlToDoc(); //traitement du fichier extrait


//Génération du nom du fichier
$now = gmdate('d_M_Y_H:i:s');
$nom_fichier_modele = explode('.',$nom_fichier_modele_ooo);
$nom_fic = $nom_fichier_modele[0]."_généré_le_".$now.".".$nom_fichier_modele[1];
header('Expires: ' . $now);
if (ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
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