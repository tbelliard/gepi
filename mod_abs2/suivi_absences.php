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
$ordre      = isset($_POST["ordre"]) ? $_POST["ordre"] : NULL;
$fusionner  = isset($_POST["fusionner"]) ? $_POST["fusionner"] : NULL;
$_fusion    = isset($_POST["fusion"]) ? $_POST["fusion"] : NULL;
$aff_fusion = NULL;

// ============== Code métier ===============================
include("lib/erreurs.php");


try{

  // Une demande de fusion est lancée
  if ($fusionner == 'ok' AND $_fusion !== NULL){
    $aff_fusion = 'Une demande de fusion a été effectuée pour les a_saisie:id ';
    foreach ($_fusion as $a_fuse){
      $aff_fusion .= ' - ' . $a_fuse;
    }
  }

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

  // On ne veut que les absences qui concerne le jour d'aujourd'hui :
  $deb_creneau  = CreneauPeer::getFirstCreneau();
  $_ts          = $deb_creneau->getDebutCreneau() + mktime(0, 0, 0, date("m"), date("d"), date("Y")) - 3600; // onconserve une marge de 1 heure avant le premier creneau
  $c->add(AbsenceSaisiePeer::FIN_ABS, $_ts, Criteria::GREATER_EQUAL);

  $liste_absents_brute = AbsenceSaisiePeer::doSelect($c);
//  aff_debug($liste_absents_brute);exit();
}catch(exception $e){
  affExceptions($e);
}
//**************** EN-TETE *****************
$javascript_specifique = "mod_abs2/lib/absences_ajax";
$style_specifique = "mod_abs2/lib/abs_style";
$utilisation_win = 'oui';
$titre_page = "Les absences";
require_once("../lib/header.inc");
require("lib/abs_menu.php");
//**************** FIN EN-TETE *****************
//aff_debug($liste_absents_brute[0]->getEleve()->getAbsenceSaisies());
//debug_var();

?>

<table id="table_liste_absents">
  <tr>
    <th><form method="post" action="suivi_absences.php"><input type="hidden" name="ordre" value="UTILISATEUR_ID" /><input type="submit" name="enr" value="Absence saisie par :" /></form></th>
    <th><form method="post" action="suivi_absences.php"><input type="hidden" name="ordre" value="ELEVE_ID" /><input type="submit" name="enr" value="Eleve absent" /></form></th>
    <th>Saisie effectuée le</th>
    <th><form method="post" action="suivi_absences.php"><input type="hidden" name="ordre" value="DEBUT_ABS" /><input type="submit" name="enr" value="Heure de d&eacute;but de l'absence" /></form></th>
    <th><form method="post" action="suivi_absences.php"><input type="hidden" name="ordre" value="FIN_ABS" /><input type="submit" name="enr" value="Heure de fin de l'absence" /></form></th>
    <th>Fus.</th>
  </tr>
  <tr>
    <td colspan="6">
      <form id="fusionner" action="suivi_absences.php" method="post">
      <p><input type="submit" name="fusionner" value="Fusionner les saisies sélectionnées" />
      <input type="hidden" name="fusionner" value="ok" /><?php echo $aff_fusion; ?></p>
  </td>
  </tr>
  <?php $increment = 0;
      foreach($liste_absents_brute as $absents):

          /******* On construit une petite fiche de l'élève en question (téléphone du parent 1) *****/
          // On ne garde que le responsable 1 qui est en principe le premier du tableau envoyé
          $_responsable = $absents->getEleve()->getResponsableInformations();
          $responsable = $_responsable[0];

          $_id_fiche = 'fiche' . $absents->getId();
          $fiche_eleve = '<div id="' . $_id_fiche . '" style="display: none; position: absolute; border: 2px solid yellow; background-color: lightblue;">
                          ' . $responsable->getResponsableEleve()->getCivilite() . '
                          ' . $responsable->getResponsableEleve()->getNom() . ' ' . $responsable->getResponsableEleve()->getPrenom() . '(resp.leg. '.$responsable->getRespLegal().')<br />
                          Tel. pers. :' . $responsable->getResponsableEleve()->getTelPers() . '<br />
                          Tel. port. :' . $responsable->getResponsableEleve()->getTelPort() . '<br />
                          Tel. prof. :' . $responsable->getResponsableEleve()->getTelProf() . '<br />

                          </div>';
          /******* On construit une petite fiche de l'élève en question (hsitorique des saisies de ses absences) *****/
          $_id_recap = 'recap' . $absents->getId();
          $_fiche_recap_abs = '<div id="' . $_id_recap . '" style="display: none; position: absolute; border: 2px solid pink; background-color: green;">
                        Absences de ' . $absents->getEleve()->getNom() . ' ' . $absents->getEleve()->getPrenom() . '<br />';
          foreach($absents->getEleve()->getAbsenceSaisies() as $absences){
            $_fiche_recap_abs .= '--> Saisie le ' . date("d/m/Y H:i", $absences->getCreatedOn()) . ' (de '.date("d/m/Y H:i", $absences->getDebutAbs()).' à '.date("d/m/Y H:i", $absences->getFinAbs()).' )<br />';
          }
          $_fiche_recap_abs .= '</div>';

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
    <td><input type="checkbox" name="fusion[<?php echo $increment; ?>]" value="<?php echo $absents->getId(); ?>"></td>
  </tr>

  <?php $increment++; endforeach; ?>

</table>
</form>

<div id="aff_result"></div>


<?php require_once("../lib/footer.inc.php"); ?>