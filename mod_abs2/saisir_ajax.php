<?php
/**
 *
 *
 * @version $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
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
include("../lib/initialisationsPropel.inc.php");
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

// ==================== VARIABLES ===============================
$_id = isset($_POST["_id"]) ? $_POST["_id"] : NULL;
$type = isset($_POST["type"]) ? $_POST["type"] : NULL;
$_ok = 'oui';
$aff_coche = '';
$test_aff_fiche = "ok";
$aff_creneaux = NULL;
$aff_motifs = $aff_justifications = NULL;

// +++++++++++++++++++++ Code métier ++++++++++++++++++++++++++++
include("lib/erreurs.php");

try{

  // On teste $_id pour savoir s'il renvoie qu'un seul numéro d'élève ou plusieurs
  $test_id = explode(",", $_id);
  $test_nbre = count($test_id);
  if ($test_nbre >= 2) {
    $test_aff_fiche = 'no'; // Il y a plus d'un élève appelé, donc, pas de fiche élève
  }

  $test_type = substr($type, 7); // permet de savoir quel type d'info il faut renvoyer
  switch($test_type){
    case 'Aid':
      $liste = 'AID|' . $_id;
      break;
    case 'Groupes':
      $liste = $_id;
      break;
    case 'Eleves':
      $liste = $_id;
      $_ok = 'non';
      break;
    case 'Classes':
      $liste = 'CLA|'.$_id;
      break;
    default:
      $liste = '';
  } // switch

  if ($_ok == 'oui') {
    $aff_liste = GroupePeer::retrieveByPK($_id);
    //aff_debug();
    $nom_classe = $aff_liste->getClasses();
    $test_type = 'Classe : ';
    foreach($nom_classe as $classe){
      $test_type .= $classe->getNomComplet() . ' ';
    }
    //aff_debug($aff_liste); exit();
  }else{
    // On récupère les infos sur tous les élèves sélectionnés (qu'il y en ait un ou plusieurs)
    $aff_coche = ' checked="checked"';
    $aff_liste = ElevePeer::retrieveByPKs($test_id);
    
  }

  // *********************************************************************************** //
  // ************** CRENEAUX : On crée les options pour les selects des créneaux ******* //
  $critere = new Criteria();
  $critere->add(CreneauPeer::TYPE_CRENEAU, 'pause', Criteria::NOT_EQUAL);
  $liste_creneaux = CreneauPeer::doSelect($critere);

  foreach($liste_creneaux as $creneaux){
    $aff_creneaux .= '<option value="' . $creneaux->getId() . '">' . $creneaux->getNomCreneau() . '</option>'."\n";
  }
  // ****************************** Fin de la liste des créneaux *********************** //
  // *********************************************************************************** //
  // ************** MOTIFS : On crée les options pour les selects des motifs *********** //
  $liste_motifs = AbsenceMotifPeer::doSelect(new Criteria);

  foreach($liste_motifs as $motifs){
    $aff_motifs .= '<option value="' . $motifs->getId() . '">' . $motifs->getNom() . '</option>'."\n";
  }
  if (!isset($aff_motifs)){
    $aff_motifs = '<option value="rien">Aucun motif dans la base</option>';
  }
  // ****************************** Fin de la liste des motifs ************************* //
  // *********************************************************************************** //
  // **** JUSTIFICATIONS : On crée les options pour les selects des justifications ***** //
  $liste_justifications = AbsenceJustificationPeer::doSelect(new Criteria);

  foreach($liste_justifications as $justifications){
    $aff_justifications .= '<option value="' . $justifications->getId() . '">' . $justifications->getNom() . '</option>' . "\n";
  }
  if (!isset($aff_justifications)){
    $aff_justifications = '<option value="rien">Aucune justification dans la base</option>';
  }
  // ****************************** Fin de la liste des motifs ************************* //
  // *********************************************************************************** //


}catch(exception $e){
  // Cette fonction est présente dans /lib/erreurs.php
  affExceptions($e);
}
// On précise l'entête HTML pour que le navigateur ne se perde pas .
header('Content-Type: text/html; charset:utf-8');
?>

<div id="div_saisie_abs" style="border: 1px solid grey; height: 200%; margin: 5px 5px 5px 5px; padding: 5px 5px 5px 5px; background-color: #99FFFF;">

  <!--<form method="post" action="saisir_absences.php">-->
  <form method="post" action="saisir_enregistrer.php">

    <p><input type="hidden" name="action" value="eleves" /></p>
    <p><input type="submit" name="enregistrer_absences" value="Enregistrer" /> - <?php echo $test_type; ?></p>
    <table class="_center">
      <tr><th>Absents</th><th>Nom Pr&eacute;nom</th><th>Abs. Journ.</th><th>D&eacute;but</th><th>Fin</th><th>Justification</th><th>Motif</th></tr>

      <?php $a = 0; foreach($aff_liste as $eleve): ?>
        <?php ($classes = $eleve->getJEleveClasses()); $classe = $classes[0]->getClasse(); ?>
        
        <tr>

          <td><input type="checkbox" name="_eleve[<?php echo $a; ?>]" id="el<?php echo $a; ?>" value="<?php echo $eleve->getIdEleve(); ?>"<?php echo $aff_coche; ?> /></td>
          <td><label for="el<?php echo $a; ?>"><?php echo htmlentities($eleve->getNom()) . ' ' . htmlentities($eleve->getPrenom()); ?> (<?php  echo $classe->getClasse(); ?>)</label></td>
          <td><input type="checkbox" name="_jourentier[<?php echo $a; ?>]" id="j<?php echo $a; ?>" value="ok"<?php echo $aff_coche; ?> /></td>
          <td><select name="_deb[<?php echo $a; ?>]" onclick="decoche('j<?php echo $a; ?>');"><?php echo $aff_creneaux; ?></select></td>
          <td><select name="_fin[<?php echo $a; ?>]" onclick="decoche('j<?php echo $a; ?>');"><?php echo $aff_creneaux; ?></select></td>
          <td><select name="_justifications[<?php echo $a; ?>]"><?php echo $aff_justifications; ?></select></td>
          <td><select name="_motifs[<?php echo $a; ?>]"><?php echo $aff_motifs; ?></select></td>

        </tr>

      <?php $a++; endforeach; ?>

    </table>

    <p><input type="submit" name="enregistrer_absences" value="Enregistrer" /></p>

  </form>

<?php /* A partir d'ici, on affiche la fiche de l'élève si on la demande (uniquement dans le cas où un seul élève est demandé 
if (isset($aff_liste[0]->fiche_eleve) AND $test_aff_fiche == 'ok') { ?>
  <hr style="width: 1000px;" />
  <!-- fiche des responsables -->
  <div id="responsables_eleve" style="position: absolute; margin-left: 520px; width: 600px; background-color: lightblue;">
    <p style="background: silver;">-- @ -- Les responsables --</p>

    <table style="width: 100%;">
  <?php foreach($aff_liste[0]->responsables as $tab):
    $color_fond = (isset($tab->resp_legal) AND $tab->resp_legal != 0) ? ' style="background: silverlight;"' : '';?>
      <tr<?php echo $color_fond; ?>>
        <td style="text-decoration: underline;"><?php echo $tab->civilite . ' ' . $tab->nom .' '. $tab->prenom; ?></td>
        <td>L&eacute;gal <?php echo $tab->resp_legal; ?></td>
        <td><?php echo $tab->civilite; ?></td>
        <td>Dom. : <?php echo $tab->tel_pers; ?></td>
      </tr>
      <tr<?php echo $color_fond; ?>>
        <td><?php echo $tab->adr1; ?></td>
        <td></td>
        <td></td>
        <td>prof. : <?php echo $tab->tel_prof; ?></td>
      </tr>
      <tr<?php echo $color_fond; ?>>
        <td><?php echo $tab->adr2; ?></td>
        <td></td>
        <td></td>
        <td>port. : <?php echo $tab->tel_port; ?></td>
      </tr>
      <tr<?php echo $color_fond; ?>>
        <td><?php echo $tab->cp . ' ' . $tab->commune; ?></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td colspan="4"> - </td>
      </tr>

  <?php endforeach; ?>
    </table>
  </div>
  <!-- Fiche de l'eleve -->
  <div id="perso_eleve" style="position: absolute; height: 200px; width: 500px; background-color: lightblue;">
    <p>-- @ -- L'&eacute;l&egrave;ve --</p>
    <p style="font-weight: bold; background-color: #99AAEE;"><?php echo $aff_liste[0]->nom . ' ' . $aff_liste[0]->prenom . ' - ' . $aff_liste[0]->classe; ?></p>
    <p>&acirc;ge de l'&eacute;l&egrave;ve (<?php echo $aff_liste[0]->naissance; ?>) - <?php echo $aff_liste[0]->sexe; ?></p>
    <p>r&eacute;gime</p>
    <p>doublant : OUI / NON</p>
    <p>Signalement acad&eacute;mique</p>
    <p>Options</p>
    <p>Nb absences :  Nb de retards : Nb de demi-journ&eacute;es :</p>
  </div>


<?php }*/ // Fin de la fiche de l'élève ?>
</div>

