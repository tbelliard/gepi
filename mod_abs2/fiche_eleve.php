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
$fich_php="fiche_eleve.php";
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
description='Absences2: Fiches élèves',
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
$_module_a_afficher = isset($_GET['mod']) ? $_GET['mod'] : NULL;

if (getSettingValue("active_mod_discipline") == "y"){
  $_discipline = '<li><a href="../mod_discipline/index.php" title="Module sanction/discipline">Sanctions</a></li>';
}else{
  $_discipline = '';
}

// ============== Code métier ===============================
include("lib/erreurs.php");
//include("../orm/helpers/CreneauHelper.php");
include("../orm/helpers/EdtCreneauHelper.php");

/*
try{

  // Il faudrait pouvoir après un header Location permettre de revenir au bon module...


}catch(exception $e){
  affExceptions($e);
}
*/

$style_specifique=array();
/*
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
*/

$utilisation_win = "oui";
$utilisation_jsdivdrag = "oui";
//$javascript_specifique = ".js";
$style_specifique[] = "eleves/visu_eleve";

$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL);
$onglet=isset($_POST['onglet']) ? $_POST['onglet'] : (isset($_GET['onglet']) ? $_GET['onglet'] : NULL);
$onglet2=isset($_POST['onglet2']) ? $_POST['onglet2'] : (isset($_GET['onglet2']) ? $_GET['onglet2'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

//$date_debut_disc=isset($_POST['date_debut_disc']) ? $_POST['date_debut_disc'] : "";
//$date_fin_disc=isset($_POST['date_fin_disc']) ? $_POST['date_fin_disc'] : "";

$annee = strftime("%Y");
$mois = strftime("%m");
$jour = strftime("%d");

if($mois>7) {$date_debut_tmp="01/09/$annee";} else {$date_debut_tmp="01/09/".($annee-1);}

$date_debut_disc=isset($_POST['date_debut_disc']) ? $_POST['date_debut_disc'] : (isset($_SESSION['date_debut_disc']) ? $_SESSION['date_debut_disc'] : $date_debut_tmp);
$date_fin_disc=isset($_POST['date_fin_disc']) ? $_POST['date_fin_disc'] : (isset($_SESSION['date_fin_disc']) ? $_SESSION['date_fin_disc'] : "$jour/$mois/$annee");

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

//===========================
// Etiquettes des onglets:
$onglet_abs='fiche_eleve';
include('menu_abs2.inc.php');
//===========================


echo "<div class='css-panes' id='containDiv'>\n";
    echo "<div style=display:block'>\n";
        $page="fiche_eleve.php";
        include("../eleves/visu_eleve.inc.php");
    echo "</div>\n";
echo "</div>\n";

require_once("../lib/footer.inc.php");
?>