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
};
//debug_var();
// ============== traitement des variables ==================
$fusionner  = isset($_POST["fusionner"]) ? $_POST["fusionner"] : NULL;
$_fusion    = isset($_POST["fusion"]) ? $_POST["fusion"] : NULL;
$aff_fusion = NULL;

// ============== Code métier ===============================
include("lib/erreurs.php");


try{

  // Une demande de fusion est lancée
  if ($fusionner == 'ok'){
    $aff_fusion = 'Une demande de fusion a été effectuée pour les a_saisie:id ';
    foreach ($_fusion as $a_fuse){
      $aff_fusion .= ' - ' . $a_fuse;
    }
  }

  // On récupère la liste des absences (brutes pour le moment)
  $c = new Criteria();
  $c->addAscendingOrderByColumn(AbsenceSaisiePeer::CREATED_ON);
  $liste_absents_brute = AbsenceSaisiePeer::doSelect($c);

}catch(exception $e){
  affExceptions($e);
}
//**************** EN-TETE *****************
$javascript_specifique = "mod_abs2/lib/absences_ajax";
$style_specifique = "mod_abs2/lib/abs_style";
$titre_page = "Les absences";
require_once("../lib/header.inc");
require("lib/abs_menu.php");
//**************** FIN EN-TETE *****************
//aff_debug($liste_absents_brute[0]->getEleve()->getAbsenceSaisies());
//debug_var();

?>
<form id="fusionner" action="suivi_absences.php" method="post">
  <p><input type="submit" name="fusionner" value="Fusionner les saisies sélectionnées" />
  <input type="hidden" name="fusionner" value="ok" /><?php echo $aff_fusion; ?></p>
<table id="table_liste_absents">
  <tr>
    <th>Absence saisie par :</th>
    <th>Eleve absent</th>
    <th>Saisie effectuée le</th>
    <th>Heure de début de l'absence</th>
    <th>Heure de fin de l'absence</th>
    <th>Fus.</th>
  </tr>
  <?php $increment = 0;
      foreach($liste_absents_brute as $absents):

          $creneau_debut  = CreneauPeer::retrieveByPK($absents->getDebutAbs()); // On enlèvera 3600 secondes car en 1970, il n'y avait pas d'heure d'hiver/ete
          $creneau_fin    = CreneauPeer::retrieveByPK($absents->getFinAbs());

          /******* On construit une petite fiche de l'élève en question (téléphone du parent 1) *****/
          // On ne garde que le responsable 1 qui est en principe le premier du tableau envoyé
          $_responsable = $absents->getEleve()->getResponsableInformations();
          $responsable = $_responsable[0];

          $_id_fiche = 'fiche' . $absents->getId();
          $fiche_eleve = '<div id="' . $_id_fiche . '" style="display: none; position: absolute; border: 2px solid yellow; background-color: lightblue;">
                          ' . $responsable->getResponsableEleve()->getCivilite() . '
                          ' . $responsable->getResponsableEleve()->getNom() . ' ' . $responsable->getResponsableEleve()->getPrenom() . '<br />
                          Tel. pers. :' . $responsable->getResponsableEleve()->getTelPers() . '<br />
                          Tel. port. :' . $responsable->getResponsableEleve()->getTelPort() . '<br />
                          Tel. prof. :' . $responsable->getResponsableEleve()->getTelProf() . '<br />

                          </div>';
          /******* On construit une petite fiche de l'élève en question (hsitorique des saisies de ses absences) *****/
          $_id_recap = 'recap' . $absents->getId();
          $_fiche_recap_abs = '<div id="' . $_id_recap . '" style="display: none; position: absolute; border: 2px solid pink; background-color: green;">
                        Absences de ' . $absents->getEleve()->getNom() . ' ' . $absents->getEleve()->getPrenom() . '<br />';
          foreach($absents->getEleve()->getAbsenceSaisies() as $absences){
            $_fiche_recap_abs .= '--> Saisie le ' . date("d-m-Y H:i", $absences->getCreatedOn()) . ' (de '.$absences->getDebutAbs().' à '.$absences->getFinAbs().' )<br />';
          }
          $_fiche_recap_abs .= '</div>';


?>

  <tr>
    <td><?php echo $absents->getUtilisateurProfessionnel()->getNom() . ' ' . $absents->getUtilisateurProfessionnel()->getPrenom(); ?></td>
    <td onmouseover="afficherDiv('<?php echo $_id_fiche; ?>');" onmouseout="cacherDiv('<?php echo $_id_fiche; ?>');"><?php echo $absents->getEleve()->getNom() . ' ' . $absents->getEleve()->getPrenom(); ?></td>
    <td ondblclick="afficherDiv('<?php echo $_id_recap; ?>');" onmouseout="cacherDiv('<?php echo $_id_recap; ?>');"><?php echo date("d/m/Y H:i", $absents->getCreatedOn()) . $fiche_eleve; ?></td>
    <td><?php echo date("H:i", ($creneau_debut->getDebutCreneau() - 3600)) . $_fiche_recap_abs; ?></td>
    <td><?php echo date("H:i", ($creneau_fin->getFinCreneau() - 3600)); ?></td>
    <td><input type="checkbox" name="fusion[<?php echo $increment; ?>]" value="<?php echo $absents->getId(); ?>"></td>
  </tr>

  <?php $increment++; endforeach; ?>

</table>
</form>



<?php require_once("../lib/footer.inc.php"); ?>