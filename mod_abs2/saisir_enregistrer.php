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
$_justifications      = isset($_POST['_justifications']) ? $_POST['_justifications'] : NULL;
$_motifs              = isset($_POST['_motifs']) ? $_POST['_motifs'] : NULL;
$nombre               = isset($_POST['nombre']) ? $_POST['nombre'] : NULL;
$enregistrer_absences = isset($_POST['enregistrer_absences']) ? $_POST['enregistrer_absences'] : NULL;

$_SESSION['msg_abs']  = isset($_SESSION['msg_abs']) ? $_SESSION['msg_abs'] : NULL;

//debug_var();

// ============== Code métier ===============================
include("lib/erreurs.php");


try{

  // Une demande d'enregistrement des absences est lancée
  if ($action == 'eleves' AND $enregistrer_absences == 'Enregistrer'){

    $nbre = count($_eleve);
    $creneau = new Creneau();
    $increment = 0;

    for($a = 0 ; $a < $nbre ; $a++){

      $saisie = new AbsenceSaisie();
      $saisie->setUtilisateurId($_SESSION["login"]);
      $saisie->setEleveId($_eleve[$a]);
      // SI on demande la journée entière ...
      if (isset($_jourentier[$a]) AND $_jourentier[$a] != ''){

        // ... On indique le premier et le dernier créneau de la journée
        $saisie->setDebutAbs(CreneauPeer::getFirstCreneau()->getId());
        $saisie->setFinAbs(CreneauPeer::getLastCreneau()->getId());

      }else{
        //echo '<pre>';print_r($_deb);echo'</pre>';
        $saisie->setDebutAbs($_deb[$a]);
        $saisie->setFinAbs($_fin[$a]);

      }
      //$saisie->setCreatedOn(date("U"));
      

      if ($_last_id = $saisie->save()){
        // $_last_id est donc l'id de l'enregistrement qui vient d'avoir lieu
        $increment++;
      }else{
        $_SESSION['msg_abs'] .= '||' . $_last_id;
      }
    }

    if ($increment == $nbre){
      header("Location: saisir_absences.php");
    }else{
      throw new Exception('Il y a une erreur dans la saisie des absences, au moins une d\'entre elles ne peut pas être enregistrée.||' . $increment . '+' . $nbre);
    }



  }

}catch(exception $e){
  // Cette fonction est présente dans /lib/erreurs.php
  affExceptions($e);
}
?>
