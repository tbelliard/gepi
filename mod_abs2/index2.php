<?php
/**
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
}


//==============================================
/* Ajout des droits pour la page dans la table droits */
$fich_php="index2.php";
$sql="SELECT 1=1 FROM droits WHERE id='/mod_abs2/$fich_php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_abs2/$fich_php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='V',
autre='V',
description='Absences2: Index',
statut='';";
$insert=mysql_query($sql);
}
//==============================================
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
//==============================================


//debug_var();
// ============== traitement des variables ==================
$aff_aide = NULL;
//$_module_a_afficher = isset($_GET['mod']) ? $_GET['mod'] : NULL;


// ============== Code métier ===============================
include("lib/erreurs.php");
//include("../orm/helpers/CreneauHelper.php");
include("../orm/helpers/EdtCreneauHelper.php");

try{

  // Il faudrait pouvoir après un header Location permettre de revenir au bon module...


}catch(exception $e){
  affExceptions($e);
}


$style_specifique=array();
//======================================================
// Pour l'EDT:
require_once("../edt_organisation/fonctions_edt.php");            // --- fonctions de basecommunes à tous les emplois du temps
require_once("../edt_organisation/fonctions_edt_prof.php");       // --- edtprof
require_once("../edt_organisation/fonctions_edt_classe.php");     // --- edtclasse
require_once("../edt_organisation/fonctions_edt_salle.php");      // --- edtsalle
require_once("../edt_organisation/fonctions_edt_eleve.php");      // --- edteleve
require_once("../edt_organisation/fonctions_affichage.php");
require_once("../edt_organisation/req_database.php");

$style_specifique[] = "templates/DefaultEDT/css/style_edt";
$style_specifique[]="templates/DefaultEDT/css/small_edt";

$scriptaculous='ok';
//======================================================

//include("crob_func.lib.php");

//**************** EN-TETE *****************
//$javascript_specifique = "mod_abs2/lib/absences_ajax";
$javascript_specifique = "mod_abs2/lib/mod_abs2";
$style_specifique[] = "mod_abs2/lib/abs_style";
$titre_page = "Les absences";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<span style='color:red'>index2</span>";

//===========================
// Etiquettes des onglets:
$onglet_abs='index2';
include('menu_abs2.inc.php');
//===========================

echo "<div class='css-panes' id='containDiv'>\n";
    echo "<div style=display:block'>\n";
        echo "<p>Petit texte de présentation du module...</p>\n";
    echo "</div>\n";
echo "</div>\n";

require_once("../lib/footer.inc.php");
?>