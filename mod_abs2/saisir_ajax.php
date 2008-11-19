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

$utiliser_pdo = 'on';
//error_reporting(0);
// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

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
}

// ==================== VARIABLES ===============================
$_id = isset($_POST["_id"]) ? $_POST["_id"] : NULL;
$type = isset($_POST["type"]) ? $_POST["type"] : NULL;
$_ok = 'oui';

// +++++++++++++++++++++ Code métier ++++++++++++++++++++++++++++
include("absences.class.php");
include("helpers/aff_listes_utilisateurs.inc.php");
include("lib/erreurs.php");

try{

  $test_type = substr($type, 7); // permet de savoir quel type d'info il faut renvoyer
  switch($test_type){
    case 'Aid':
      $liste = 'AID|' . $_id;
      break;
    case 'Groupes':
      $liste = $_id;
      break;
    case 'Eleve':
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
    $aff_liste = ListeEleves(array('classes'=>$liste));
  }else{
    // Il s'agit d'afficher les infos sur un élève
    $aff_liste = infosEleve($liste);
  }
//print_r($aff_liste);echo'fraise';exit();


}catch(exception $e){
  // Cette fonction est présente dans /lib/erreurs.php
  affExceptions($e);
}
// On précise l'entête HTML pour que le navigateur ne se perde pas .
header('Content-Type: text/html; charset:utf-8');
?>
<br />
<div style="border: 1px solid grey; height: 100%; padding: 10px; background-color: #99FFFF;">

  <form method="post" action="saisir_absences.php">

    <p><input type="hidden" name="action" value="eleves" /></p>
    <p><input type="submit" name="enregistrer_absences" value="Enregistrer" /></p>
    <table>
      <tr><th>id_eleve</th><th>Nom Pr&eacute;nom</th><th>Saisie de l'absence</th></tr>

      <?php foreach($aff_liste as $tab): ?>
        <tr>
          <td><?php echo $tab->id_eleve; ?></td>
          <td><label for="el<?php echo $tab->id_eleve; ?>"><?php echo utf8_encode($tab->nom) . ' ' . utf8_encode($tab->prenom); ?></label></td>
          <td>
              <table><tr>
                  <td><input type="checkbox" name="_eleve[]" id="el<?php echo $tab->id_eleve; ?>" value="<?php echo $tab->id_eleve; ?>" /></td>
              </tr></table>
          </td>
        </tr>
      <?php endforeach; ?>

    </table>

    <p><input type="submit" name="enregistrer_absences" value="Enregistrer" /></p>

  </form>
<?php /* A partir d'ici, on affiche la fiche de l'élève si on la demande */
if (isset($aff_liste[0]->fiche_eleve)) { ?>
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


<?php } // Fin de la fiche de l'élève?>
</div>

