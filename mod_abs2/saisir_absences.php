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

// L'utilisation d'un observeur javascript
$use_observeur = 'ok';

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
$test = array();
$_SESSION["type_aff_abs"] = isset($_SESSION["type_aff_abs"]) ? $_SESSION["type_aff_abs"] : 'alpha';
$_SESSION["type_aff_abs"] = (isset($_GET["type_aff_abs"]) AND ($_GET["type_aff_abs"] == 'alpha' OR $_GET["type_aff_abs"] == 'classe'))
                            ? $_GET["type_aff_abs"] : $_SESSION["type_aff_abs"];
$_selected_liste_eleve    = isset($_SESSION["_eleveSaisi"]) ? $_SESSION["_eleveSaisi"] : NULL;
$_SESSION["_eleveSaisi"]  = NULL;


// ============== Code métier ===============================
include("lib/erreurs.php");
include("../orm/helpers/EleveHelper.php");


try{

// ************************************************************************************ //
// *************************** Liste des élèves de l'établissement ******************** //

  if ($_SESSION["type_aff_abs"] == "alpha"){
    // La liste de tous les élèves de l'établissement
    $liste_eleves = ElevePeer::FindAllElevesOrderByNomPrenom();
  }else{
    // La même liste mais enrichie par Propel (classe, ...)
    $liste_eleves = ElevePeer::FindAllElevesAvecCLasse();
  }

// ******************** fin de la liste des élèves de l'établissement ***************** //
// ************************************************************************************ //
// ******* Liste des groupes (enseignements) et des classes de l'établissement ******** //

  $c_grp = new Criteria();
  $c_grp->addDescendingOrderByColumn(ClassePeer::NOM_COMPLET);
  $liste_classes = ClassePeer::doSelect($c_grp);

  $afficheHtmlSelectListeGroupes = '<label for="ListeGroupeId">Les enseignements</label>
  <select id="ListeGroupeId" name="choix_groupe" onchange="gestionaffAbs(\'aff_result\',\'ListeGroupeId\',\'saisir_ajax.php\');">
    <option value="rien">-- -- --</option>';
  $afficheHtmlSelectListeClasses = '<label for="ListeClasseId">Les classes</label>
  <select id="ListeClasseId" name="choix_classe" onchange="gestionaffAbs(\'aff_result\',\'ListeClasseId\',\'saisir_ajax.php\');">
    <option value="rien">-- -- --</option>';
  foreach($liste_classes as $classes){

    $afficheHtmlSelectListeClasses .= '<option value="'.$classes->getId().'">'.$classes->getNomComplet().'</option>' . "\n";

    foreach($classes->getGroupes() as $groupes_de_la_classe){
      $afficheHtmlSelectListeGroupes .= '<option value="' . $groupes_de_la_classe->getId() . '">' . $classes->getNomComplet() . ' ' . $groupes_de_la_classe->getDescription() . '</option>'."\n";
    }

  }
  $afficheHtmlSelectListeGroupes .= '</select>';
  $afficheHtmlSelectListeClasses .= '</select>';

// ******************* fin de la liste des groupes de l'établissement ***************** //
// ************************************************************************************ //
// *************************** Liste des AID de l'établissement *********************** //

  $c_aid = new Criteria();
  $liste_aids = AidDetailsPeer::doSelect($c_aid);

  $afficheHtmlSelectListeAids = '<label for="ListeAidId" title="Activit&eacute; inter-disciplinaires">Les A.I.D.</label>
  <select id="ListeAidId" name="choix_aid">
    <option value="rien">-- -- --</option>';
  foreach($liste_aids as $aids){

      $afficheHtmlSelectListeAids .= '<option value="' . $aids->getId() . '">' . $aids->getNom() . '   (' . $aids->getAidConfiguration()->getNomComplet() . ')</option>'."\n";

  }
  $afficheHtmlSelectListeAids .= '</select>';

// ******************* fin de la liste des AID de l'établissement ********************* //
// ************************************************************************************ //

}catch(exception $e){
  // Cette fonction est présente dans /lib/erreurs.php
  affExceptions($e);
}

//**************** EN-TETE *****************
$javascript_specifique = "mod_abs2/lib/absences_ajax";
$style_specifique = "mod_abs2/lib/abs_style";
$titre_page = "Saisir les absences";
require_once("../lib/header.inc");
$menu = 'saisir';
require("lib/abs_menu.php");
//**************** FIN EN-TETE *****************
//debug_var();

?>
<div id="idAidAbs" style="display: none; position: absolute; background-color: gray; color: white; width: 500px;">
  Projet d'aide sur le module absence accessible par la touche [F2]. On pourrait imaginer travailler sur une aide dynamique avec des tables et des infos particulières</div>
<p><a href="saisir_absences.php?type_aff_abs=alpha">Afficher tous les &eacute;l&egrave;ves</a> -
<a href="saisir_absences.php?type_aff_abs=classe">Afficher par classe</a> - aide [F2]</p>

<div id="saisie_abs" style="border: 2px solid silver; background-color: lightblue; padding: 10px 10px 10px 10px;">


  <form action="saisir_absences.php" method="post">
  <!-- Selects des classes, enseignements et AID -->
  <div style="position: absolute; padding: 5px 5px 5px 5px; width: 500px; margin-left: 520px;">
    <fieldset id="espace_saisie2" style="border: 1px solid grey;">
      <legend> - Afficher une liste pr&eacute;cise - </legend>
      <p style="text-align: right;">
        <?php echo $afficheHtmlSelectListeGroupes; ?>
      </p>
      <p style="text-align: right;">
        <?php echo $afficheHtmlSelectListeAids; ?>
      </p>
      <p style="text-align: right;">
        <?php echo $afficheHtmlSelectListeClasses; ?>
      </p>

      <p style="position: relative; margin-left: 1%;">
        <input type="submit" name="valider" value="&nbsp;>>&nbsp;" />
      </p>

    </fieldset>
    <?php
    if (isset($_SESSION['msg_abs'])){
      //echo $_SESSION['msg_abs'];
      aff_debug($_SESSION['msg_abs']);
      $_SESSION['msg_abs'] = NULL; // On efface le message après l'avoir affiché
    }
    ?>
  </div>

  <!-- select de la liste des élèves de l'établissement -->
  <fieldset id="espace_saisie" style="border: 1px solid grey; padding: 5px 5px 5px 5px; width: 500px;">
    <legend> - Saisir un ou plusieurs &eacute;l&egrave;ves - </legend>

      <p>
        <?php echo EleveHelper::afficheHtmlSelectListeEleves(array ('label'=>'Elève (nom)', 'size'=>10, 'multiple'=>'on', 'event'=>'change', 'method_event'=>'gestionaffAbs', 'url'=>'saisir_ajax.php', 'selected'=>$_selected_liste_eleve), $liste_eleves); ?>
      </p>
  </fieldset>


  </form>

</div>

<div id="aff_result" style="display: none;">
  <!-- Affichage des données AJAX sur les élèves sélectionnés -->
</div>


<?php require("../lib/footer.inc.php"); ?>