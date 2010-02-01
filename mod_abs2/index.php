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


$use_observeur = 'ok';

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
};
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

try{

  // Il faudrait pouvoir après un header Location permettre de revenir au bon module...


}catch(exception $e){
  affExceptions($e);
}
//**************** EN-TETE *****************
$javascript_specifique = "mod_abs2/lib/absences_ajax";
$style_specifique = "mod_abs2/lib/abs_style";
$titre_page = "Les absences";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>

<div id="aidmenu" style="display: none;"><?php echo $aff_aide; ?></div>

<ul class="css-tabs" id="menutabs">
  <li><a href="ajax.php?mod=abs" title="Salsie des absences et des retards">Saisie</a></li>
  <li><a href="suivi_absences.php" title="Traitement et suivi des absences et des retards">Suivi</a></li>
  <li><a href="ajax.php?mod=bil" title="Bilans">Bilans</a></li>
  <li><a href="ajax.php?mod=sta" title="Statistiques">Statistiques</a></li>
  <li><a href="ajax.php?mod=cou" title="Gestion du courrier">Courrier</a></li>
  <li><a href="ajax_eleve.php" title="Informations sur les élèves">Fiches élève</a></li>
  <li><a href="parametrage_absences.php" title="Paramètres : types, actions, motifs, justifications, créneaux">Paramètres</a></li>
  <?php echo $_discipline; ?>
</ul>


<div class="css-panes" id="containDiv">
  <div style="display:block"></div>
</div>


<?php require_once("../lib/footer.inc.php"); ?>