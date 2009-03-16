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
$var  = isset($_GET["var"]) ? $_GET["var"] : NULL;
$var2  = isset($_GET["var2"]) ? $_GET["var2"] : NULL;

// +++++++++++++++++++++ Code Métier ++++++++++++++++++++++++++++
include("lib/erreurs.php");

if (is_numeric($var2) AND substr($var, 0, 6) == "winAbs"){
  // On a donc notre élève
  $_id_eleve = $var2;
  $eleve = ElevePeer::retrieveByPK($_id_eleve);
}

 /*
  * A partir d'ici, on affiche la fiche de l'élève si on la demande (uniquement dans le cas où un seul élève est demandé
  */

foreach ($eleve->getResponsableInformations() as $responsables):

  //aff_debug($responsables);
  $responsable  = $responsables->getResponsableEleve();
  $adresse      = $responsable->getResponsableEleveAdresse();
  $resp_legal = ($responsables->getRespLegal() == '0') ? '<span style="color: red;">simple contact</span>' : $responsables->getRespLegal();
  echo '<table style="border: 1px solid gray;">
          <tr><td>Responsable ' . $resp_legal . '</td></tr>
          <tr><td>' . $responsable->getNom() . ' ' . $responsable->getPrenom() . '</td></tr>
          <tr><td>' . $responsable->getTelPers() . '</td></tr>
          <tr><td>' . $responsable->getTelPort() . '</td></tr>
          <tr><td>' . $responsable->getTelProf() . '</td></tr>
          <tr><td>' . $adresse->getAdr1() . '</td></tr>
          <tr><td>' . $adresse->getAdr2() . '</td></tr>
          <tr><td>' . $adresse->getCp() . ' ' . $adresse->getCommune() . '</td></tr>
        </table>
      <hr />';

endforeach;
/*
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
