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

// L'utilisation d'un observeur javascript
$use_observeur = 'ok';


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
};
//debug_var();

// ========================== traitement des variables ======================================
$_enregistrer = isset($_POST["enregistrer"]) ? $_POST["enregistrer"] : NULL;
$_traite      = isset($_POST["traite"]) ? $_POST["traite"] : NULL;
$_type        = isset($_POST["type"]) ? $_POST["type"] : NULL;
$_motif       = isset($_POST["motif"]) ? $_POST["motif"] : NULL;
$_justif      = isset($_POST["justif"]) ? $_POST["justif"] : NULL;
$_action      = isset($_POST["action"]) ? $_POST["action"] : NULL;
$_supprimer   = isset($_POST["supprimer"]) ? $_POST["supprimer"] : NULL;
$_periode_active = '1';


// ============================ Code métier =================================================
include("lib/erreurs.php");
include("../orm/helpers/CreneauHelper.php");
include("../orm/helpers/AbsencesParametresHelper.php");


try{

  // On détermine la période active


  if ($_enregistrer == 'Enregistrer'){
    // On revoit tous les traitements pour les mettre à jour
    $nbre = count($_traite);
    for($i = 0 ; $i < $nbre ; $i++){
      if (isset($_supprimer[$i]) AND $_supprimer[$i] != "0"){

        // On traite ce qui est demandé :
        // S = supprimer le traitement en faisant en sorte qu'il disparaisse (les saisies également)
        // D = Défusionner en faisant disparaitre le traitement (mais en conservant les saisies)
        if ($_supprimer[$i] == "S"){

          // Donc ces saisies seront toujours stockées dans la base, rattachées à un traitement qui n'existe plus
          // Donc elles ne sont plus visibles dans le suivi des saisies mais continuent d'être stockées dans la base
          $traitement = AbsenceTraitementPeer::retrieveByPK($_traite[$i]);
          $traitement->delete();

        }elseif($_supprimer[$i] == "D"){

          // On fait la même chose mais on supprime également les entrées dans la table j_traitements_saisies
          $criteria = new Criteria();
          $criteria->add(JTraitementSaisiePeer::A_TRAITEMENT_ID, $_traite[$i], Criteria::EQUAL);
          $joinTraitement = JTraitementSaisiePeer::doSelect($criteria);
          foreach ($joinTraitement as $join){
            $delete_join = $join->delete();
          }
          $traitement = AbsenceTraitementPeer::retrieveByPK($_traite[$i]);
          $traitement->delete();
        }

      }else{

        // On met à jour les traitements qui ne sont pas supprimés
        $traitement = AbsenceTraitementPeer::retrieveByPK($_traite[$i]);
        $traitement->setUtilisateurId($_SESSION["login"]);
        $traitement->setATypeId($_type[$i]);
        $traitement->setAMotifId($_motif[$i]);
        $traitement->setAJustificationId($_justif[$i]);
        $traitement->setAActionId($_action[$i]);
        $traitement->save();

      }
    } // fin du for($i...
  }

  // On récupère toutes les absences dont le traitement n'est pas clos
  $c = new Criteria();
/**
 * Le code qui suit devrait ordonner la liste par ordre alphabétique des noms d'élèves absents mais renvoie les absences en double.
  $c->addJoin(AbsenceTraitementPeer::ID, JTraitementSaisiePeer::A_TRAITEMENT_ID, Criteria::LEFT_JOIN);
  $c->addJoin(JTraitementSaisiePeer::A_SAISIE_ID, AbsenceSaisiePeer::ID, Criteria::LEFT_JOIN);
  $c->addJoin(AbsenceSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, Criteria::LEFT_JOIN);
  $c->addAscendingOrderByColumn(ElevePeer::NOM);
  $c->addAscendingOrderByColumn(ElevePeer::PRENOM);
*/
  $liste_traitements_en_cours = AbsenceTraitementPeer::doSelect($c);

}catch(exception $e){
  affExceptions($e);
}
//**************** EN-TETE *****************
$javascript_specifique = "mod_abs2/lib/absences_ajax";
$style_specifique = "mod_abs2/lib/abs_style";
$utilisation_win = 'oui';
$titre_page = "Le traitement des absences";
require_once("../lib/header.inc");
require("lib/abs_menu.php");
//**************** FIN EN-TETE *****************
//debug_var();
  echo '
    <div id="idAidAbs" style="display: none; position: absolute; background-color: gray; color: white; width: 600px;">
    Si vous voulez supprimer un traitement, les saisies li&eacute;es seront supprim&eacute;es également (mais elles seront conserv&eacute;es dans la base tout de m&ecirc;me).
     Si vous voulez d&eacute;fusionner un traitement, le traitement sera effac&eacute; mais les saisies seront disponibles dans le <a href="suivi_absences.php">suivi</a>.
    </div>
    <p>Liste des traitements en cours - aide [F2] - </p>
<form action="traitement_absences.php" method="post">
<div style="padding: 10px 10px 10px 10px; background-color: #AAEEAA;">
<table id="table_liste_absents">
  <tr>
    <th>Eleve</th>
    <th>Absence de - &agrave;</th>
    <th>Type</th>
    <th>Motif</th>
    <th>Justification</th>
    <th>Action</th>
    <th>Suppr.</th>
  </tr>';

$a = 0; // incrémenteur
foreach ($liste_traitements_en_cours as $traitements){

  if (substr($a, -1) == "0"){
    // Alors on afficher un "enregistrer" toutes les 10 lignes
    echo '
    <tr>
      <td colspan="5">Valider toutes les modifications</td>
      <td colspan="2" style="background-color: red;"><input type="submit" name="enregistrer" value="Enregistrer" /></td>
    </tr>';
  }

  // La couleur des lignes est alternée
  if (is_integer($a/2)){

    $class_couleur = 'lig1';

  }else{
    $class_couleur = 'lig2';
  }


  $_debut_abs     = 999999999999;
  $_fin_abs       = 0;
  $_id_traitement = $traitements->getId();

  $_type          = $traitements->getATypeId();
  $options_type   = array('id'=>'type'.$_id_traitement, 'name'=>'type['.$a.']', 'selected'=>$_type, 'class'=>$class_couleur);
  $aff_type       = AbsencesParametresHelper::AfficherListeDeroulanteTypes($options_type);

  $_motif         = $traitements->getAMotifId();
  $options_motif  = array('id'=>'motif'.$_id_traitement, 'name'=>'motif['.$a.']', 'selected'=>$_motif, 'class'=>$class_couleur);
  $aff_motif      = AbsencesParametresHelper::AfficherListeDeroulanteMotifs($options_motif);

  $_justification     = $traitements->getAJustificationId();
  $options_justif     = array('id'=>'justif'.$_id_traitement, 'name'=>'justif['.$a.']', 'selected'=>$_justification, 'class'=>$class_couleur);
  $aff_justification  = AbsencesParametresHelper::AfficherListeDeroulanteJustifications($options_justif);

  $_action        = $traitements->getAActionId();
  $options_action = array('id'=>'action'.$_id_traitement, 'name'=>'action['.$a.']', 'selected'=>$_action, 'class'=>$class_couleur);
  $aff_action     = AbsencesParametresHelper::AfficherListeDeroulanteActions($options_action);

  $aff_supprimer  = '
                    <select name="supprimer['.$a.']" class="'.$class_couleur.'">
                      <option value="0">---</option>
                      <option value="D" title="D&eacute;fusionner ce traitement">Défus.</option>
                      <option value="S" title="Supprimer ce traitement">Suppr.</option>
                    </select>';

  foreach($traitements->getJTraitementSaisies() as $saisies){

    // On teste sur le début de l'absence
    $getDebutAbs = $saisies->getAbsenceSaisie()->getDebutAbs();
    if ($getDebutAbs < $_debut_abs){
      $_debut_abs = $getDebutAbs;
    }
    // On teste également sur la fin de l'absence
    $getFinAbs = $saisies->getAbsenceSaisie()->getFinAbs();
    if ($getFinAbs > $_fin_abs){
      $_fin_abs = $getFinAbs;
    }

    // Peut-être faudrait-il vérifier qu'il s'agit toujours du même utilisateur sur toutes
    // les saisies liées à ce traitement
    $_nom     = $saisies->getAbsenceSaisie()->getEleve()->getNom();
    $_prenom  = $saisies->getAbsenceSaisie()->getEleve()->getPrenom();
    /**
     * @TODO : Il faudra s'assurer de renvoyer le nom de la classe de la bonne période
     * Ici, c'est la première qui est renvoyée
     */
    $classes  = $saisies->getAbsenceSaisie()->getEleve()->getJELeveClasses();
    $_classe = 'inc.';
    foreach ($classes as $classe){
      // On recherche la bonne classe par rapport à la periode
      if ($classe->getPeriode() == $_periode_active){
        $_classe  = $classe->getClasse()->getNomComplet();
      }
    }

  }

  echo'
    <tr class="'.$class_couleur.'">
      <td>'.$_nom . ' ' . $_prenom . ' ( '.$_classe.' )<input type="hidden" name="traite['.$a.']" value="'.$_id_traitement.'" /></td>
      <td>' . date("d/m/Y H:i", $_debut_abs) . ' - ' .date("d/m/Y H:i", $_fin_abs) . '</td>
      <td>'.$aff_type.'</td>
      <td>'.$aff_motif.'</td>
      <td>'.$aff_justification.'</td>
      <td>'.$aff_action.'</td>
      <td>'.$aff_supprimer.'</td>
    </tr>';

  $a++; // On incrémente


} // fin du foreach ($liste_traitements_en_cours as $traitements)


echo '
  </table>
</div>
</form>';

?>




<?php require_once("../lib/footer.inc.php"); ?>