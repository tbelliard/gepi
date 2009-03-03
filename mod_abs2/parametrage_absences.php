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
//include("../lib/initialisationsPropel.inc.php");
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


// ============== Code métier ===============================
include("lib/erreurs.php");

try{



}catch(exception $e){
  affExceptions($e);
}
//**************** EN-TETE *****************
$titre_page = "Paramétrer le module absences";
$javascript_specifique = "mod_abs2/lib/absences_ajax";
$style_specifique = "mod_abs2/lib/abs_style";
require_once("../lib/header.inc");
$menu = 'parametrer';
require("lib/abs_menu.php");
//**************** FIN EN-TETE *****************


?>
<p class="abs_menu">
  <span onclick="utiliseAjaxAbs('aff_result', 'types', 'parametrage_ajax.php');">Les types</span>
  <span onclick="utiliseAjaxAbs('aff_result', 'motifs', 'parametrage_ajax.php');">Les motifs</span>
  <span onclick="utiliseAjaxAbs('aff_result', 'actions', 'parametrage_ajax.php');">Les actions</span>
  <span onclick="utiliseAjaxAbs('aff_result', 'justifications', 'parametrage_ajax.php');">Les justifications</span>
  <span onclick="utiliseAjaxAbs('aff_result', 'creneaux', 'parametrage_creneaux_ajax.php');">Les cr&eacute;neaux</span>
</p>

<div id="aff_result" style="display: none;">
  <!-- Affichage des données AJAX -->
</div>



<?php require_once("../lib/footer.inc.php"); ?>