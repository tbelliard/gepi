<?php
/**
 *
 * $Id$
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
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

/*

Génération du fichier XML devant être transmis au système
"admission post-bac"

La structure de ce fichier est connue et documentée. Ce script génère
un fichier XML conforme aux spécifications.

Trois types de données sont requises, chacune nécessitant un traitement
spécifique :
- les données de l'année en cours : facilement accessibles, connues
- les données des années précédentes : plus difficilement accessible,
et pas nécessairement complètes pour tous les élèves. Ce script part du
principe qu'on intègre tout ce qu'on peut, et que l'absence de données ne
constitue pas en soit une cause de blocage de l'export. Il serait néanmoins
judicieux de vérifier que ce comportement est conforme à ce qui est attendu
par APB.
- les données de configuration pour chaque enseignement, permettant de
déterminer si l'enseignement est une LV1/2/3, s'il s'agit d'un enseignement
de spécialité ou non, etc. Ces données sont paramétrées directement
dans ce module, pour ne pas surcharger les pages de paramétrage des
groupes. L'absences de paramètres pour un groupe donné entraîne un blocage
de l'export, pour éviter les erreurs.


Ce script n'a besoin d'aucun paramètre particulier pour pouvoir fonctionner.
L'utilisation des informations de paramétrage pour chaque classe permet 
de déterminer ce qui devra être inclus ou non.

*/

$utiliser_pdo = 'on';

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
};

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

require_once("./helpers.inc.php");

/*
 * Liste des données requises :
 * - Etablissement : RNE, Nom, Code postal
 * - Classes : Code, année, nom, niveau [Premiere,Terminale], Prof principal (optionnel), decoupage des bulletins (en trimestre ou semestre)
 * - Services-notation (= enseignements évalués) : Code, année, trimestre, code-enseignants (lien vers l'enseignant), code-matiere (lien vers la matière), niveau langue-vivante [LV1, LV2, LV3], code langue vivante, nom groupe, moyenne classe, moyenne min, moyenne max, effectif
 * - Matières : Code, code-sconet, libellé, matiere-spécialité
 * - Langues vivantes : Code, libellé  // Cette distinction entre 'langue vivante' et 'services notation' est une abberration de modélisation...
 * - Enseignants : Code, nom, prénom
 * - Elèves : Code, INE, nom, prénom, date de naissance
 * 
 * Imbrication des données :
 * 'Etablissement' contient Classes, Services-notation, Matières, Langues vivantes, Enseignants, Eleves
 * Ensuite toutes les données d'évaluation sont sous 'Eleves'
 * Etablissement/Eleves/Annees-scolaires/Bulletins/Notes
 * 
 */


/*
 * Attributs utilisés spécifiquement par APB :
 * - classes : apb_niveau ['','premiere','terminale']
 * - j_groupes_classes : apb_langue_vivante ['LV1', 'LV2', 'LV3', '']
 * 
 */

if (!isset($_POST)) {
  echo "Vous ne pouvez pas accéder à ce script directement.";
  die();
}

// Initialisation des données de paramétrage de l'export

// Numéro de l'export
if (isset($_POST['num_export'])) {
  $numero_export = $_POST['num_export'];
  if (!is_numeric($numero_export)) {
    echo "Le numéro d'export n'est pas correct.";
    die();
  }
} else {
  echo "Impossible de déterminer le numéro de l'export.";
  die();
}

$req_classes = mysql_query("SELECT id, classe, nom_complet, MAX(p.num_periode) periodes, apb_niveau FROM classes c, periodes p WHERE c.apb_niveau = 'terminale' AND p.id_classe = c.id GROUP BY c.id");
$data_classes = array();
$data_groupes = array();

// Limites de périodes
$limites_periodes = array();

while ($classe = mysql_fetch_object($req_classes)) {
  // On initialise ce tableau pour plus tard, lorsque l'on traitera des groupes.
  $data_groupes[$classe->classe] = array();
  // On stocke les données relatives aux classes de terminale, qui sont la base de cet export
  $data_classes[$classe->classe] = array('code' => $classe->classe,
                        'classe' => $classe->classe,
                        'id' => $classe->id,
												'nom' => $classe->nom_complet,
												'annee' => strftime("%Y"), // L'année ici correspond toujours à l'année courante.
												'niveau' => $classe->apb_niveau,
												'decoupage' => $classe->periodes);
  // On s'occupe des limites de périodes, pour n'exporter que jusqu'à la dernière période saisie
  if (!array_key_exists($classe->periodes,$limites_periodes)) {
    if (!isset($_POST[$classe->periodes.'per'])) {
      echo "Impossible de trouver la limite de périodes.";
      die();
    } else if (!is_numeric($_POST[$classe->periodes.'per'])) {
      echo "Impossible de déterminer la limite de périodes.";
      die();
    }
    $limites_periodes[$classe->periodes] = $_POST[$classe->periodes.'per'];
  }
}


// On va initialiser toutes les données dans diverses tableaux. Ensuite
// on va parcourir les tableaux et utiliser les outils adéquats pour générer le XML
// Ce n'est pas vraiment le plus efficace d'un point de vue de gestion de la mémoire,
// mais certainement le plus simple et le plus lisible dans le script.
// Idéalement, il faudrait utiliser les classes Propel et faire proprement les
// requêtes à travers des méthodes spécifiques (ou génériques, selon les cas).
// Au pire, ce passage vers Propel pourra se faire ultérieurement. Ce fichier
// devrait $etre suffisamment lisible pour que la transition ne prenne que
// peu de temps, une fois que toutes les modèles Propel sont correctement construits.

$data_etablissement  = array();
$data_etablissement['rne'] = $gepiSettings['gepiSchoolRne'];
$data_etablissement['nom'] = utf8_encode($gepiSettings['gepiSchoolName']);
$data_etablissement['cp'] = $gepiSettings['gepiSchoolZipCode'];

// Liste des élèves

$data_eleves = array();

foreach($data_classes as $classe) {
  $req_eleves = mysql_query("SELECT e.login, e.no_gep, e.nom, e.prenom, e.naissance
                                FROM eleves e, j_eleves_classes jec
                                WHERE
                                  e.login = jec.login AND
                                  jec.id_classe = '".$classe['id']."' AND
                                  jec.periode = '".$limites_periodes[$classe['decoupage']]."'");
  while($eleve = mysql_fetch_object($req_eleves)) {
    $login_eleve = $eleve->login;
    $data_eleves[$login_eleve] = array();
    $data_eleves[$login_eleve]['code'] = $login_eleve;
    $data_eleves[$login_eleve]['login'] = $login_eleve;
    $data_eleves[$login_eleve]['ine'] = $eleve->no_gep;
    $data_eleves[$login_eleve]['nom'] = $eleve->nom;
    $data_eleves[$login_eleve]['prenom'] = $eleve->prenom;
    $data_eleves[$login_eleve]['date-naissance'] = $eleve->naissance;
    $data_eleves[$login_eleve]['code-classe'] = $classe['code'];
  }
}

// On traite maintenant les enseignements pour l'année en cours
// Pour cela, on prend élève par élève, et on récupère tout au fur et à mesure.
// Là encore, pas hyper optimisé au niveau des requêtes MySQL, puisqu'on boucle
// sur les élèves. Il pourrait être judicieux de tenter ultérieurement des optimisations.

// On boucle d'abord sur les élèves, puis sur les périodes, puis les enseignements.

// On va renseigner au fur et à mesure les information de 'services-notations' et 'enseignants'
$data_services_notations = array();
$data_enseignants = array();
$data_matieres = array();


foreach($data_eleves as &$eleve) {
    $annee = $data_classes[$eleve['code-classe']]['annee'];
    $eleve['annees-scolaires'] = array($annee => array(
                                                                'annee' => $annee,
                                                                'code-classe' => $eleve['code-classe'],
                                                                'bulletins' => array()));
    // Boucle sur les périodes de l'année en cours
    for($i=1;$i<=$limites_periodes[$data_classes[$eleve['code-classe']]['decoupage']];$i++) {
      $eleve['annees-scolaires'][$annee]['bulletins'][$i] = array('trimestre' => $i, 'notes' => array());
      
      // Maintenant on boucle sur les différentes notes pour la période considérée
      // On se base sur les mécanismes existants dans le bulletin
      $req_notes = mysql_query("SELECT n.id_groupe, n.note, n.statut, n.rang, a.appreciation
                                  FROM matieres_notes n LEFT JOIN matieres_appreciations a USING (login, id_groupe, periode)
                                  WHERE n.periode = '".$i."' AND n.login = '".$eleve['login']."'");
      
      // On passe les notes une par une
      while ($note = mysql_fetch_object($req_notes)) {
        
        // On enregistre ce groupe dans la liste des services_notations s'il n'y est pas déjà
        if (!array_key_exists($note->id_groupe.'_'.$i, $data_services_notations)) {
          $req_groupe = mysql_query("SELECT DISTINCT(g.id), g.name, g.description, m.nom_complet, m.matiere, jgp.login
                                        FROM groupes g
                                        LEFT JOIN j_groupes_matieres jgm ON g.id = jgm.id_groupe
                                        LEFT JOIN matieres m ON jgm.id_matiere = m.matiere
                                        LEFT JOIN j_groupes_professeurs jgp ON g.id = jgp.id_groupe AND ordre_prof = 0
                                        WHERE g.id = '".$note->id_groupe."'");
          $groupe = mysql_fetch_object($req_groupe);
          
          $req_stats1 = mysql_query("SELECT COUNT(DISTINCT(jeg.login)) effectif FROM j_eleves_groupes jeg
                                      WHERE jeg.id_groupe = '".$groupe->id."' AND
                                        jeg.periode = '".$i."'");

          $stats1 = mysql_fetch_object($req_stats1);

          $req_stats2 = mysql_query("SELECT MIN(n.note) note_min, MAX(n.note) note_max FROM matieres_notes n
                                      WHERE
                                        n.id_groupe = '".$groupe->id."' AND
                                        n.statut = '' AND
                                        n.periode = '".$i."'");

          $stats2 = mysql_fetch_object($req_stats2);
          
          $data_services_notations[$groupe->id.'_'.$i] = array('code' => $groupe->id,
                                                            'annee' => $annee,
                                                            'trimestre' => $i,
                                                            'code-enseignant' => $groupe->login,
                                                            'code-matiere' => $groupe->matiere,
                                                            'effectif' => $stats1->effectif,
                                                            'moyenne-haute' => $stats2->note_max,
                                                            'moyenne-basse' => $stats2->note_min);
                                                            
          // On regarde si l'enseignant est déjà répertorié
          if (!array_key_exists($groupe->login, $data_enseignants)) {
            // Non, alors on va le chercher...
            $req_enseignant = mysql_query("SELECT nom, prenom FROM utilisateurs WHERE login = '".$groupe->login."'");
            $enseignant = mysql_fetch_object($req_enseignant);
            $data_enseignants[$groupe->login] = array('code' => $groupe->login, 'nom' => $enseignant->nom, 'prenom' => $enseignant->prenom);            
          }
          
          
          // Idem pour la matière
          if (!array_key_exists($groupe->matiere, $data_matieres)) {
            $data_matieres[$groupe->matiere] = array('code' => $groupe->matiere, 'libelle' => $groupe->nom_complet);
          }          
        } 
        
        // On regarde si on a déjà enregistré les infos spécifiques à cette classe pour ce groupe dans le tableau (il s'agit du coef)
        if (!array_key_exists($note->id_groupe, $data_groupes[$eleve['code-classe']])) {
          // On récupère les informations nécessaires
          $coef = mysql_result(mysql_query("SELECT jgc.coef FROM j_groupes_classes jgc, classes c WHERE
                                                  jgc.id_groupe = '".$note->id_groupe."' AND
                                                  jgc.id_classe = c.id AND
                                                  c.classe = '".$eleve['code-classe']."'"), 0);

          $data_groupes[$eleve['code-classe']][$note->id_groupe] = array('coef' => $coef);
        }
        
        //
        // AJOUTER SUPPORT DES NOTES COMPOSANTES (oral/écrit, par exemple, basé sur les boîtes des carnets de notes)
        //
        
        
        // Statut de la notation :
        switch($note->statut) {
          case 'a':
            $statut_note = 'A';
            break;
          case 'n':
            $statut_note = 'N';
            break;
          case '-':
            $statut_note = 'N';
            break;
          case 'd':
            $statut_note = 'D';
            break;
          default:
            $statut_note = 'S';
          }
        
        $eleve['annees-scolaires'][$annee]['bulletins'][$i]['notes'][$note->id_groupe] = array(
            'code-service-notation' => $note->id_groupe.'_'.$i,
            'etat' => $statut_note,
            'moyenne' => $note->note,
            'rang' => $note->rang,
            'appreciation' => $note->appreciation,
            'coefficient' => $data_groupes[$eleve['code-classe']][$note->id_groupe]['coef']);
      }
    }
}


// Génération du fichier XML à partir des données rassemblées ci-dessus


$doc = new DOMDocument('1.0','iso-8859-15');

// La racine : <fichier>
$root = $doc->createElement('fichier');
$doc->appendChild($root);

// Fichier
$rootLogiciel = $doc->createAttribute('logiciel');
$rootVersion = $doc->createAttribute('version');
$rootDateCreation = $doc->createAttribute('date-creation');
$root->appendChild($rootLogiciel);
$root->appendChild($rootVersion);
$root->appendChild($rootDateCreation);

$rootLogicielValue = $doc->createTextNode('gepi');
$rootVersionValue = $doc->createTextNode($gepiVersion);
$rootDateValue = $doc->createTextNode(strftime("%Y-%m-%d"));

$rootLogiciel->appendChild($rootLogicielValue);
$rootVersion->appendChild($rootVersionValue);
$rootDateCreation->appendChild($rootDateValue);

// Etablissement
$etab = $doc->createElement('etablissement');
$root->appendChild($etab);

$etabRne = $doc->createAttribute('rne');
$etabNom = $doc->createAttribute('nom');
$etabCp = $doc->createAttribute('cp');
$etab->appendChild($etabRne);
$etab->appendChild($etabNom);
$etab->appendChild($etabCp);
$etabRneValue = $doc->createTextNode($data_etablissement['rne']);
$etabNomValue = $doc->createTextNode($data_etablissement['nom']);
$etabCpValue = $doc->createTextNode($data_etablissement['cp']);
$etabRne->appendChild($etabRneValue);
$etabCp->appendChild($etabCpValue);
$etabNom->appendChild($etabNomValue);

// Classes
$classes = $doc->createElement('classes');
$etab->appendChild($classes);

foreach($data_classes as $cl) {
  
  $current_classe = $doc->createElement('classe');
  $classes->appendChild($current_classe);
  
  $clCode = $doc->createAttribute('code');
  $clCodeValue = $doc->createTextNode(preg_replace('/ /','','C-'.utf8_encode($cl['code'])));
  $current_classe->appendChild($clCode);
  $clCode->appendChild($clCodeValue);
  
  $clAnnee = $doc->createAttribute('annee');
  $clAnneeValue = $doc->createTextNode($cl['annee']);
  $current_classe->appendChild($clAnnee);
  $clAnnee->appendChild($clAnneeValue);
  
  $clNom = $doc->createAttribute('nom');
  $clNomValue = $doc->createTextNode(utf8_encode($cl['nom']));
  $current_classe->appendChild($clNom);
  $clNom->appendChild($clNomValue);
  
  $clNiveau = $doc->createAttribute('niveau');
  $clNiveauValue = $doc->createTextNode(utf8_encode($cl['niveau']));
  $current_classe->appendChild($clNiveau);
  $clNiveau->appendChild($clNiveauValue);
  
  $clDecoupage = $doc->createAttribute('decoupage');
  $clDecoupageValue = $doc->createTextNode($cl['decoupage']);
  $current_classe->appendChild($clDecoupage);
  $clDecoupage->appendChild($clDecoupageValue);
}

// Services notations

$services_notations = $doc->createElement('services-notations');
$etab->appendChild($services_notations);

foreach($data_services_notations as $code => $sn) {
  // Note : csn pour current_service_notation
  $csn = $doc->createElement('service-notation');
  $services_notations->appendChild($csn);
  
  // On passe les attributs
  $snCode = $doc->createAttribute('code');
  $snCodeValue = $doc->createTextNode('S-'.utf8_encode($code));
  $csn->appendChild($snCode);
  $snCode->appendChild($snCodeValue);
  
  $snAnnee = $doc->createAttribute('annee');
  $snAnneeValue = $doc->createTextNode(utf8_encode($sn['annee']));
  $csn->appendChild($snAnnee);
  $snAnnee->appendChild($snAnneeValue);
  
  $snTrimestre = $doc->createAttribute('trimestre');
  $snTrimestreValue = $doc->createTextNode(utf8_encode($sn['trimestre']));
  $csn->appendChild($snTrimestre);
  $snTrimestre->appendChild($snTrimestreValue);
  
  $snCodeEnseignant = $doc->createAttribute('code-enseignant');
  $snCodeEnseignantValue = $doc->createTextNode('P-'.utf8_encode($sn['code-enseignant']));
  $csn->appendChild($snCodeEnseignant);
  $snCodeEnseignant->appendChild($snCodeEnseignantValue);
  
  $snCodeMatiere = $doc->createAttribute('code-matiere');
  $snCodeMatiereValue = $doc->createTextNode('M-'.utf8_encode($sn['code-matiere']));
  $csn->appendChild($snCodeMatiere);
  $snCodeMatiere->appendChild($snCodeMatiereValue);
  
  // Effectif
  if ($sn['effectif'] != '0') {
    $effectif = $doc->createElement('effectif');
    $csn->appendChild($effectif);
    $effectifValue = $doc->createTextNode($sn['effectif']);
    $effectif->appendChild($effectifValue);
  }
  
  // Moyenne haute
/*
  if ($sn['moyenne-haute'] != '') {
    $moyenneHaute = $doc->createElement('moyenne-haute');
    $csn->appendChild($moyenneHaute);
    $moyenneHauteValue = $doc->createTextNode($sn['moyenne-haute']);
    $moyenneHaute->appendChild($moyenneHauteValue);
  }
*/
  
  // Moyenne basse
/*
  if ($sn['moyenne-basse'] != '') {
    $moyenneBasse = $doc->createElement('moyenne-basse');
    $csn->appendChild($moyenneBasse);
    $moyenneBasseValue = $doc->createTextNode($sn['moyenne-basse']);
    $moyenneBasse->appendChild($moyenneBasseValue);
  }
*/
  
  // Moyenne classe : à déterminer !!!
  
}

// Les matières

$matieres = $doc->createElement('matieres');
$etab->appendChild($matieres);

foreach($data_matieres as $matiere) {
  $mat = $doc->createElement('matiere');
  $matieres->appendChild($mat);
  
  // On passe les attributs
  $matCode = $doc->createAttribute('code');
  $matCodeValue = $doc->createTextNode('M-'.utf8_encode($matiere['code']));
  $mat->appendChild($matCode);
  $matCode->appendChild($matCodeValue);

  $matCodeSconet = $doc->createAttribute('code-sconet');
  $matCodeSconetValue = $doc->createTextNode(utf8_encode($matiere['code']));
  $mat->appendChild($matCodeSconet);
  $matCodeSconet->appendChild($matCodeSconetValue);
  
  $matLibelle = $doc->createAttribute('libelle');
  $matLibelleValue = $doc->createTextNode(utf8_encode($matiere['libelle']));
  $mat->appendChild($matLibelle);
  $matLibelle->appendChild($matLibelleValue);
}

//
// LANGUES VIVANTES : il va faut trouver un moyen de les caractériser...
// Ca risque de devoir passer par une configuration manuelle, soit au moment
// de l'export, soit dans la configuration du module.
// Pour l'instant, on exporte une langue vivante vide et non utilisée, pour
// être conforme au schéma...


$langues_vivantes = $doc->createElement('langues-vivantes');
$etab->appendChild($langues_vivantes);

$lv1 = $doc->createElement('langue-vivante');
$langues_vivantes->appendChild($lv1);
$lv1Code = $doc->createAttribute('code');
$lv1CodeValue = $doc->createTextNode('LV-LV1-TEST');
$lv1Libelle = $doc->createAttribute('libelle');
$lv1LibelleValue = $doc->createTextNode('LV1 de Test');
$lv1->appendChild($lv1Code);
$lv1Code->appendChild($lv1CodeValue);
$lv1->appendChild($lv1Libelle);
$lv1Libelle->appendChild($lv1LibelleValue);

// Les enseignants
$enseignants = $doc->createElement('enseignants');
$etab->appendChild($enseignants);

foreach($data_enseignants as $prof) {
  $e = $doc->createElement('enseignant');
  $enseignants->appendChild($e);
  
  // On passe les attributs
  $eCode = $doc->createAttribute('code');
  $eCodeValue = $doc->createTextNode('P-'.utf8_encode($prof['code']));
  $e->appendChild($eCode);
  $eCode->appendChild($eCodeValue);

  $eNom = $doc->createAttribute('nom');
  $eNomValue = $doc->createTextNode(utf8_encode($prof['nom']));
  $e->appendChild($eNom);
  $eNom->appendChild($eNomValue);
  
  $ePrenom = $doc->createAttribute('prenom');
  $ePrenomValue = $doc->createTextNode(utf8_encode($prof['prenom']));
  $e->appendChild($ePrenom);
  $ePrenom->appendChild($ePrenomValue);
}


// Et enfin, les élèves

$eleves = $doc->createElement('eleves');
$etab->appendChild($eleves);

foreach($data_eleves as $el) {
  $e = $doc->createElement('eleve');
  $eleves->appendChild($e);
  
  // On passe les attributs
  $eCode = $doc->createAttribute('code');
  $eCodeValue = $doc->createTextNode('E-'.utf8_encode($el['code']));
  $e->appendChild($eCode);
  $eCode->appendChild($eCodeValue);

  $eINE = $doc->createAttribute('ine');
  $eINEValue = $doc->createTextNode(utf8_encode($el['ine']));
  $e->appendChild($eINE);
  $eINE->appendChild($eINEValue);

  $eNom = $doc->createAttribute('nom');
  $eNomValue = $doc->createTextNode(utf8_encode($el['nom']));
  $e->appendChild($eNom);
  $eNom->appendChild($eNomValue);
  
  $ePrenom = $doc->createAttribute('prenom');
  $ePrenomValue = $doc->createTextNode(utf8_encode($el['prenom']));
  $e->appendChild($ePrenom);
  $ePrenom->appendChild($ePrenomValue);
  
  $eNaissance = $doc->createAttribute('date-naissance');
  $eNaissanceValue = $doc->createTextNode(utf8_encode($el['date-naissance']));
  $e->appendChild($eNaissance);
  $eNaissance->appendChild($eNaissanceValue);
  
  // On passe les années scolaires
  $annees_scolaires = $doc->createElement('annees-scolaires');
  $e->appendChild($annees_scolaires);
  
  foreach($el['annees-scolaires'] as $an) {
    $annee = $doc->createElement('annee-scolaire');
    $annees_scolaires->appendChild($annee);
    
    $anneeDescription = $doc->createAttribute('annee');
    $anneeDescriptionValue = $doc->createTextNode(utf8_encode($an['annee']));
    $annee->appendChild($anneeDescription);
    $anneeDescription->appendChild($anneeDescriptionValue);
    
    $anneeCodeClasse = $doc->createAttribute('code-classe');
    $anneeCodeClasseValue = $doc->createTextNode(preg_replace('/ /','','C-'.utf8_encode($an['code-classe'])));
    $annee->appendChild($anneeCodeClasse);
    $anneeCodeClasse->appendChild($anneeCodeClasseValue);
    
    // Les bulletins pour l'année considérée
    $bulletins = $doc->createElement('bulletins');
    $annee->appendChild($bulletins);
    
    foreach($an['bulletins'] as $bulletin) {
      $b = $doc->createElement('bulletin');
      $bulletins->appendChild($b);
      
      $bTrimestre = $doc->createAttribute('trimestre');
      $bTrimestreValue = $doc->createTextNode(utf8_encode($bulletin['trimestre']));
      $b->appendChild($bTrimestre);
      $bTrimestre->appendChild($bTrimestreValue);

      // Les notes pour ce trimestre
      
      foreach($bulletin['notes'] as $note) {
        $n = $doc->createElement('notes');
        $b->appendChild($n);
        
        $nServiceNotation = $doc->createAttribute('code-service-notation');
        $nServiceNotationValue = $doc->createTextNode('S-'.utf8_encode($note['code-service-notation']));
        $n->appendChild($nServiceNotation);
        $nServiceNotation->appendChild($nServiceNotationValue);
        
        // Etat de la note
        $nEtat = $doc->createAttribute('etat');
        $nEtatValue = $doc->createTextNode(utf8_encode($note['etat']));
        $n->appendChild($nEtat);
        $nEtat->appendChild($nEtatValue);
        
        if ($note['etat'] == 'S') {
          $nMoyenne = $doc->createElement('moyenne');
          $nMoyenneValue = $doc->createTextNode(utf8_encode($note['moyenne']));
          $n->appendChild($nMoyenne);
          $nMoyenne->appendChild($nMoyenneValue);
        
          $nRang = $doc->createElement('rang');
          $nRangValue = $doc->createTextNode(utf8_encode($note['rang']));
          $n->appendChild($nRang);
          $nRang->appendChild($nRangValue);
        }
        
        if ($note['coefficient'] != '0.0') {
          $nCoefficient = $doc->createElement('coefficient');
          $nCoefficientValue = $doc->createTextNode(utf8_encode($note['coefficient']));
          $n->appendChild($nCoefficient);
          $nCoefficient->appendChild($nCoefficientValue);
        }
        $nAppreciation = $doc->createElement('appreciation');
        $nAppreciationValue = $doc->createTextNode(utf8_encode($note['appreciation']));
        $n->appendChild($nAppreciation);
        $nAppreciation->appendChild($nAppreciationValue);
        
      }
      
    }
    
  }
  
}

$export_filename = $data_etablissement['rne'].'_E'.$numero_export.'.xml';

header("Content-Type: text/xml");
header("Content-Disposition: attachment; filename=$export_filename"); 
echo $doc->saveXML();

?>
