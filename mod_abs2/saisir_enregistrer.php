<?php
/**
 *
 *
 * @version $Id: saisir_absences.php 2800 2008-12-22 20:22:11Z jjocal $
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

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
include("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// ============== traitement des variables ==================
# Enregistrement de nouvelles absences
$action               = isset($_POST['action']) ? $_POST['action'] : NULL;
$_eleve               = isset($_POST['_eleve']) ? $_POST['_eleve'] : NULL;
$_jourentier          = isset($_POST["_jourentier"]) ? $_POST["_jourentier"] : NULL;
$_deb                 = isset($_POST['_deb']) ? $_POST['_deb'] : NULL;
$_fin                 = isset($_POST['_fin']) ? $_POST['_fin'] : NULL;
$_types               = isset($_POST["_types"]) ? $_POST["_types"] : NULL;
$_justifications      = isset($_POST['_justifications']) ? $_POST['_justifications'] : NULL;
$_motifs              = isset($_POST['_motifs']) ? $_POST['_motifs'] : NULL;
$nombre               = isset($_POST['nombre']) ? $_POST['nombre'] : NULL;
$enregistrer_absences = isset($_POST['enregistrer_absences']) ? $_POST['enregistrer_absences'] : NULL;

$_SESSION['msg_abs']  = isset($_SESSION['msg_abs']) ? $_SESSION['msg_abs'] : NULL;

//debug_var();//exit();

// ============== Code métier ===============================
include("lib/erreurs.php");
include('../orm/helpers/CreneauHelper.php');


try{

  /* @@@@@@@@@@@@@@@@@@@@@@@@@@ Une demande d'enregistrement des absences est lancée @@@@@@@@@@@@@@@@@@@@@@@ */

  if ($action == 'eleves' AND $enregistrer_absences == 'Enregistrer'){

    $nbre = count($_fin); // On compte le nombre de ligne qui ont été envoyées
    $nbre_el = count($_eleve); // On compte le nombre d'élèves à enregistrer

    $increment = 0; // utile pour itérer

    for($a = 0 ; $a < $nbre ; $a++){

      if (isset ($_eleve[$a])){

        // Alors on propose d'enregistrer l'absence pour garder une trace de la saisie (raisons légales et vérification)...
        $saisie = new AbsenceSaisie();
        $saisie->setUtilisateurId($_SESSION["login"]);
        $saisie->setEleveId($_eleve[$a]);
        $_SESSION["_eleveSaisi"] = $_eleve[$a];

        // Si on demande la journée entière ...
        if (isset($_jourentier[$a]) AND $_jourentier[$a] != ''){

          // ... On indique le premier et le dernier créneau de la journée
          $_deb = CreneauPeer::getFirstCreneau()->getDebutCreneau();
          $deb  = $_deb + mktime(0, 0, 0, date("m"), date("d"), date("Y"));
          $_fin = CreneauPeer::getLastCreneau()->getFinCreneau();
          $fin  = $_fin + mktime(0, 0, 0, date("m"), date("d"), date("Y"));

        }else{

          $t_deb  = CreneauPeer::retrieveByPK($_deb[$a]);
          $deb    = mktime(0, 0, 0, date("m"), date("d"), date("Y")) + $t_deb->getDebutCreneau();
          $t_fin  = CreneauPeer::retrieveByPK($_fin[$a]);
          $fin    = mktime(0, 0, 0, date("m"), date("d"), date("Y")) + $t_fin->getFinCreneau();

        }

        $saisie->setDebutAbs($deb);
        $saisie->setFinAbs($fin);

        if ($_last_id = $saisie->save()){
          // Si l'enregistrement est bon et que l'utilisateur a demandé un motif et/ou une justification, on crée un traitement également
          if (($_motifs[$a] != "0") OR ($_justifications[$a] != "0")){
            $traitement = new AbsenceTraitement();
            $traitement->setAMotifId($_motifs[$a]);
            $traitement->setAJustificationId($_justifications[$a]);
            $traitement->setATypeId($_types[$a]);
            $traitement->save();

            $join = new JTraitementSaisie();
            $join->setASaisieId($saisie->getId());
            $join->setATraitementId($traitement->getId());
            $join->save();

          }
          // $_last_id est donc l'id de l'enregistrement qui vient d'avoir lieu => A vérifier, il semblerait que non
          $increment++;
        }else{
          $_SESSION['msg_abs'] .= '||' . $_last_id;
        }

      } // fin du if (isset ($_eleve[$a])){
    } // fin de la boucle for

    // Si tout est bon, on renvoie vers l'interface de saisie, sinon on renvoie une exception
    if ($increment == $nbre_el){

      $_SESSION['msg_abs'] .= '<h3 class="ok">Saisies enregistr&eacute;es.</h3>';
      header("Location: saisir_absences.php");
      die();

    }else{

      throw new Exception('Il y a une erreur dans la saisie des absences, au moins une d\'entre elles ne peut pas être enregistrée.||' . $increment . '+' . $nbre);

    }



  } // fin du if ($action == 'eleves'...

}catch(exception $e){
  // Cette fonction est présente dans /lib/erreurs.php
  affExceptions($e);
}
?>
