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
// ============== traitement des variables ==================
$ordre      = isset($_GET["ordre"]) ? $_GET["ordre"] : NULL;
$tab_absents = array();

// ============== Code métier ===============================
include("lib/erreurs.php");
include("../orm/helpers/CreneauHelper.php");


try{


  // On récupère la liste des absences (brutes pour le moment)
  $c = new Criteria();
  $tab_ordre = array ('ELEVE_ID', 'DEBUT_ABS', 'FIN_ABS', 'UTILISATEUR_ID');
  if (in_array($ordre, $tab_ordre)){

    switch ($ordre){
      case 'ELEVE_ID':
        //$c->addAscendingOrderByColumn(AbsenceSaisiePeer::ELEVE_ID);
        $c->addJoin(AbsenceSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, Criteria::INNER_JOIN);
        $c->addAscendingOrderByColumn(ElevePeer::NOM);
        break;
      case 'DEBUT_ABS':
        $c->addAscendingOrderByColumn(AbsenceSaisiePeer::DEBUT_ABS);
        break;
      case 'FIN_ABS':
        $c->addAscendingOrderByColumn(AbsenceSaisiePeer::FIN_ABS);
        break;
      case 'UTILISATEUR_ID':
        $c->addAscendingOrderByColumn(AbsenceSaisiePeer::UTILISATEUR_ID);
        break;
    }

  }else{
    $c->addAscendingOrderByColumn(AbsenceSaisiePeer::CREATED_ON);
  }

  // On ne veut que les absences qui concernent le jour d'aujourd'hui :
  $deb_creneau  = CreneauPeer::getFirstCreneau();
  $_ts          = $deb_creneau->getDebutCreneau() + mktime(0, 0, 0, date("m"), date("d"), date("Y")) - 3600; // on conserve une marge de 1 heure avant le premier creneau
  $c->add(AbsenceSaisiePeer::FIN_ABS, $_ts, Criteria::GREATER_EQUAL);
  
  $liste_absents_brute = AbsenceSaisiePeer::doSelect($c);
  //aff_debug($liste_absents_brute[3]->getJTraitementSaisies());exit();

  foreach ($liste_absents_brute as $absents){
     if (!in_array($absents->getEleve()->getIdEleve(), $tab_absents)){
       $tab_absents[$absents->getEleve()->getIdEleve()][] = $absents;
     }else{
       $place = array_key($tab_absents, $absents->getEleve()->getIdEleve());
       $tab_absents[$place][] = $absents;
     }
  }


  /***************** On élabore un petit tableau du suivi créneau par créneau *******************/
  $aff_creneaux = CreneauHelper::afficherPetitTableauDesCreneaux();
  $tab_creneaux = CreneauPeer::getAllCreneauxOrderByTime();
  /********************* Fin du petit tableau des créneaux **************************************/


}catch(exception $e){
  affExceptions($e);
}
//**************** EN-TETE *****************
$javascript_specifique = "mod_abs2/lib/absences_ajax";
$style_specifique = "mod_abs2/lib/abs_style";
$utilisation_win = 'oui';
$titre_page = "Le suivi des saisies";
require_once("../lib/header.inc");
require("lib/abs_menu.php");
//**************** FIN EN-TETE *****************
//aff_debug($liste_absents_brute[0]->getEleve()->getAbsenceSaisies());
//debug_var();

  /******************************** AIDE AIDE *****************************************/
  echo '
    <div id="idAidAbs" style="display: none; position: absolute; background-color: gray; color: white; width: 600px;">
    Pour fusionner des absences entre elles, il faut cocher ensemble les saisies du m&ecirc;me &eacute;l&egrave;ve. Si vous souhaitez
    fusionner une saisie avec l\'absence la plus r&eacute;cente, il suffit de faire un double-clic sur la premi&egrave;re saisie de cet
    &eacute;l&egrave;ve dans la colonne [<b>Fus.</b>].
    </div>
    <p> - aide [F2] - </p>
  ';

if (isset($_SESSION["msg_fusions"])){
  echo $_SESSION["msg_fusions"];
  $_SESSION["msg_fusions"] = NULL;
}
?>

<form id="fusionner" action="suivi_absences_fusionner.php" method="post">
  <input type="hidden" name="ordre" value="<?php echo $ordre; ?>" />
  <table id="table_liste_absents">
    <tr>
      <th><a href="suivi_absences.php?ordre=UTILISATEUR_ID">Saisie :</a></th>
      <th><a href="suivi_absences.php?ordre=ELEVE_ID">Eleve absent</a></th>
      <th>Saisie effectuée le</th>
      <th><a href="suivi_absences.php?ordre=DEBUT_ABS">Heure de d&eacute;but</a></th>
      <th><a href="suivi_absences.php?ordre=FIN_ABS">Heure de fin</a></th>
      <th>Fus.</th>
      <th>Suivi :</th>
    </tr>
    <tr>
      <td colspan="6">
        <p><input type="submit" name="fusionner" value="Fusionner les saisies sélectionnées" />
        <input type="hidden" name="fusionner" value="ok" /></p>
      </td>
      <td>
        <?php echo $aff_creneaux; ?>
      </td>
    </tr>
<?php $increment = 0;
  foreach($liste_absents_brute as $absents):

    $test2 = $absents->getJTraitementSaisies();

    if (empty($test2)){
      // On n'affiche que les saisies qui ne font pas déjà l'objet d'un traitement
 
      /******* On construit une petite fiche de l'élève en question (téléphone du parent 1) *****/
      // On ne garde que les responsables 1 et 2 qui sont en principe les premier du tableau envoyé
      $_responsables = $absents->getEleve()->getResponsableInformations();

      $_id_fiche = 'fiche' . $absents->getId();
      $fiche_eleve = '<div id="' . $_id_fiche . '" style="display: none; position: absolute; border: 2px solid yellow; background-color: lightblue;">';

      foreach ($_responsables as $responsable){
        if ($responsable->getRespLegal() != 0){

          $fiche_eleve .=
                      '<p>' . $responsable->getResponsableEleve()->getCivilite() . '
                      ' . $responsable->getResponsableEleve()->getNom() . ' ' . $responsable->getResponsableEleve()->getPrenom() . '(resp.leg. '.$responsable->getRespLegal().')</p>
                      Tel. pers. :' . $responsable->getResponsableEleve()->getTelPers() . '<br />
                      Tel. port. :' . $responsable->getResponsableEleve()->getTelPort() . '<br />
                      Tel. prof. :' . $responsable->getResponsableEleve()->getTelProf() . '<br /><hr />';
        }
      }
      $fiche_eleve .= '</div>';
      /******* On construit une petite fiche de l'élève en question (historique des saisies de ses absences) *****/
      $_id_recap = 'recap' . $absents->getId();
      $_fiche_recap_abs = '<div id="' . $_id_recap . '" style="display: none; position: absolute; border: 2px solid pink; background-color: lightblue;">
                  Absences de ' . $absents->getEleve()->getNom() . ' ' . $absents->getEleve()->getPrenom() . '<br />';
      foreach($absents->getEleve()->getAbsenceSaisies() as $absences){
        $_fiche_recap_abs .= '--> Saisie le ' . date("d/m/Y H:i", $absences->getCreatedOn()) .
      ' (de '.date("d/m/Y H:i", $absences->getDebutAbs()).' à '.date("d/m/Y H:i", $absences->getFinAbs()).' )<br />';
      }
      $_fiche_recap_abs .= '</div>';



      /*********************************** Gestion du suivi sur les créneaux ************************************/
      $aff_suivi_creneaux = '<table><tr>';
      foreach ($tab_creneaux as $creneaux){
        if ($creneaux->getTypeCreneau() == 'cours'){
          $duree_cren_d = mktime(0, 0, 0, date("m"), date("d"), date("Y")) + $creneaux->getDebutCreneau();
          $duree_cren_f = mktime(0, 0, 0, date("m"), date("d"), date("Y")) + $creneaux->getFinCreneau();
          if ($duree_cren_d >= $absents->getDebutAbs() AND $duree_cren_f <= $absents->getFinAbs()){
            $color = 'red';
            $title = 'Absence saisie par '.$absents->getUtilisateurProfessionnel()->getNom() . ' ' . $absents->getUtilisateurProfessionnel()->getPrenom();
          }else{
            $color = 'silver';
            $title = 'Aucune saisie';
          }

          $aff_suivi_creneaux .= '
          <td style="font-weight: bold; background-color: '.$color.';" title="'.$title.'">'.$creneaux->getNomCreneau().'</td>';
        }
      }
      $aff_suivi_creneaux .= '</tr></table>';
      /************************** Fin de la gestion du suivi sur les créneaux ***********************************/

      if (is_integer($increment/2)){

        $class_couleur = 'lig1';

      }else{
        $class_couleur = 'lig2';
      }
?>

    <tr class="<?php echo $class_couleur; ?>">
      <td><?php echo $absents->getUtilisateurProfessionnel()->getNom() . ' ' . $absents->getUtilisateurProfessionnel()->getPrenom(); ?></td>
      <td id="winAbs<?php echo $absents->getEleve()->getIdEleve(); ?>" onmouseover="afficherDiv('<?php echo $_id_fiche; ?>');" onmouseout="cacherDiv('<?php echo $_id_fiche; ?>');"><?php echo $absents->getEleve()->getNom() . ' ' . $absents->getEleve()->getPrenom(); ?></td>
      <td ondblclick="afficherDiv('<?php echo $_id_recap; ?>');" onmouseout="cacherDiv('<?php echo $_id_recap; ?>');"><?php echo date("d/m/Y H:i", $absents->getCreatedOn()) . $fiche_eleve; ?></td>
      <td><?php echo date("d/m/Y", $absents->getDebutAbs()).' <span class="gras">'.date("H:i", $absents->getDebutAbs()).'</span>'. $_fiche_recap_abs; ?></td>
      <td><?php echo date("d/m/Y", $absents->getFinAbs()).' <span class="gras">'.date("H:i", $absents->getFinAbs()).'</span>'; ?></td>
      <td ondblclick="afficherDiv('_fusHier<?php echo $increment; ?>');" title="Un double-clic vous permet de fusionner avec la saisie pr&eacute;c&eacute;dente la plus r&eacute;cente" >
        <input type="checkbox" name="fusion[<?php echo $increment; ?>]" value="<?php echo $absents->getId(); ?>" title="Attention vous allez fusionner des absences !" />
        <div id="_fusHier<?php echo $increment; ?>" style="display: none;">
          h<input type="checkbox" name="fusionHier[<?php echo $increment; ?>]" value="ok" title="Cochez pour fusionner avec la pr&eacute;c&eacute;dente." />
        </div>
      </td>
      <td><?php echo $aff_suivi_creneaux; ?></td>
    </tr>

<?php
    $increment++;
    } // fin du if qui détermine si la saisie fait l'objet d'un traitement ou pas
  endforeach;
?>


  </table>
</form>


<div id="aff_result"></div>

<?php require_once("../lib/footer.inc.php"); ?>