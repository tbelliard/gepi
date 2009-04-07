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
$action = isset($_POST['action']) ? $_POST['action'] : NULL;

// ============== Code métier ===============================
include("classes/odtphp_0_3/odf.php");
include("lib/erreurs.php");
include("helpers/aff_listes_utilisateurs.inc.php");


try{

    // On teste Propel pour récupérer la liste des élèves
    $criteria = new Criteria();
    $criteria->setLimit(1);
    $eleves = ElevePeer::doSelect($criteria);



  //if ($action == "odt") {
    $odf = new odf("modeles/test.odt");
    $odf->setVars('titre','premier titre de remplacement');
    $odf->setVars('contenu',"texte sur plusieurs lignes mais va comprendre");
    // On exporte le fichier
    $odf->exportAsAttachedFile('un_nom_c_est_mieux.odt');
  //}


}catch(exception $e){
  affExceptions($e);
}
//**************** EN-TETE *****************
$titre_page = "Les absences";
require_once("../lib/header.inc");
require("lib/abs_menu.php");
//**************** FIN EN-TETE *****************

//aff_debug($eleves[0]->getResponsableInformations());
$responsable = $eleves[0]->getResponsableInformations();
//aff_debug($responsable[0]->getResponsableEleve()->getResponsableEleveAdresse());
echo '
    L\'élève ' . $eleves[0]->getNom() . ' ' . $eleves[0]->getPrenom() . '<br />
Dont le responsable 1 est : ' . $responsable[0]->getResponsableEleve()->getNom() . ' ' . $responsable[0]->getResponsableEleve()->getPrenom() . '
<br />
Adresse : ' . $responsable[0]->getResponsableEleve()->getResponsableEleveAdresse()->getAdr1() . '<br />
          ' . $responsable[0]->getResponsableEleve()->getResponsableEleveAdresse()->getAdr2() . '<br />
          ' . $responsable[0]->getResponsableEleve()->getResponsableEleveAdresse()->getCp() . ' ' . $responsable[0]->getResponsableEleve()->getResponsableEleveAdresse()->getCommune() . '<br />
';
?>




<?php require_once("../lib/footer.inc.php"); ?>