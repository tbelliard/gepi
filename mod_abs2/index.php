<?php
/**
 *
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activé.");
}

if ($utilisateur->getStatut()=="professeur" &&  getSettingValue("active_module_absence_professeur")!='y') {
    die("Le module n'est pas activé.");
}

//on va redirigé vers le bon onglet
if (isset($_SESSION['abs2_onglet']) && $_SESSION['abs2_onglet'] != 'index.php') {
    $param_saisie="";
    if(($utilisateur->getStatut()=="professeur")&&(($_SESSION['abs2_onglet']=="saisir_groupe.php")||($_SESSION['abs2_onglet']=="saisir_groupe_plan.php"))&&(isset($_GET['type_selection']))&&($_GET['type_selection']=='id_groupe')&&(isset($_GET['id_groupe']))&&(is_numeric($_GET['id_groupe']))) {
       $param_saisie.="?type_selection=id_groupe&id_groupe=".$_GET['id_groupe'];
    }
    elseif(($utilisateur->getStatut()=="professeur")&&(($_SESSION['abs2_onglet']=="saisir_groupe.php")||($_SESSION['abs2_onglet']=="saisir_groupe_plan.php"))&&(isset($_GET['type_selection']))&&($_GET['type_selection']=='id_aid')&&(isset($_GET['id_aid']))&&(is_numeric($_GET['id_aid']))) {
       $param_saisie.="?type_selection=id_aid&id_aid=".$_GET['id_aid'];
    }
    elseif(($utilisateur->getStatut()=="professeur")&&(($_SESSION['abs2_onglet']=="saisir_groupe.php")||($_SESSION['abs2_onglet']=="saisir_groupe_plan.php"))&&(isset($_GET['type_selection']))&&($_GET['type_selection']=='id_cours')&&(isset($_GET['id_cours']))&&(is_numeric($_GET['id_cours']))) {
       $param_saisie.="?type_selection=id_cours&id_cours=".$_GET['id_cours'];
    }
    header("Location: ./".$_SESSION['abs2_onglet'].$param_saisie);
    die();
}

if ($utilisateur->getStatut()=="cpe" || $utilisateur->getStatut()=="scolarite") {
    header("Location: ./absences_du_jour.php");
    die();
} else if ($utilisateur->getStatut()=="professeur") {
    $param_saisie="";
    if((isset($_GET['type_selection']))&&($_GET['type_selection']=='id_groupe')&&(isset($_GET['id_groupe']))&&(is_numeric($_GET['id_groupe']))) {
       $param_saisie.="?type_selection=id_groupe&id_groupe=".$_GET['id_groupe'];
    }
    elseif((isset($_GET['type_selection']))&&($_GET['type_selection']=='id_aid')&&(isset($_GET['id_aid']))&&(is_numeric($_GET['id_aid']))) {
       $param_saisie.="?type_selection=id_aid&id_aid=".$_GET['id_aid'];
    }

    header("Location: ./saisir_groupe.php".$param_saisie);
    die();
} else if ($utilisateur->getStatut()=="autre") {
    if(acces('/mod_abs2/saisir_eleve.php', 'autre')){
        header("Location: ./saisir_eleve.php");
        die();
    }else if(acces('/mod_abs2/bilan_individuel.php', 'autre')){
        header("Location: ./bilan_individuel.php");
        die();
    }else if(acces('/mod_abs2/totaux_du_jour.php', 'autre')){
        header("Location: ./totaux_du_jour.php");
        die();
    }else{
        echo"Vous n'avez les droits sur aucune action dans le module";
        die();
    }
}

//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
$titre_page = "Les absences";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

include('menu_abs2.inc.php');

echo "<div class='css-panes' id='containDiv'>\n";
    echo "<div style='display:block'>\n";
    //echo "<p>Petit texte de présentation du module...</p>\n";
    echo "</div>\n";
echo "</div>\n";

require_once("../lib/footer.inc.php");
?>
