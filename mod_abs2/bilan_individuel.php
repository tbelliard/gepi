<?php
/**
 *
 * @version $Id$
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
// mise à jour des droits dans la table droits
// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
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
if (getSettingValue("active_module_absence") != '2') {
    die("Le module n'est pas activé.");
}

if ($utilisateur->getStatut() != "cpe" && $utilisateur->getStatut() != "scolarite" && $utilisateur->getStatut() != "professeur" && $utilisateur->getStatut() != "autre" ) {
    die("acces interdit");
}
if($utilisateur->getStatut() == "professeur" && $utilisateur->getClasses()->isEmpty()){
    die("acces interdit");
}

include_once 'lib/function.php';

// Initialisation des variables
//récupération des paramètres de la requète
$nom_eleve = isset($_POST["nom_eleve"]) ? $_POST["nom_eleve"] : (isset($_GET["nom_eleve"]) ? $_GET["nom_eleve"] : (isset($_SESSION["nom_eleve"]) ? $_SESSION["nom_eleve"] : NULL));
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : (isset($_GET["id_classe"]) ? $_GET["id_classe"] : (isset($_SESSION["id_classe_abs"]) ? $_SESSION["id_classe_abs"] : NULL));
$id_eleve = isset($_POST["id_eleve"]) ? $_POST["id_eleve"] : (isset($_GET["id_eleve"]) ? $_GET["id_eleve"] : NULL);
$date_absence_eleve_debut = isset($_POST["date_absence_eleve_debut"]) ? $_POST["date_absence_eleve_debut"] : (isset($_GET["date_absence_eleve_debut"]) ? $_GET["date_absence_eleve_debut"] : (isset($_SESSION["date_absence_eleve_debut"]) ? $_SESSION["date_absence_eleve_debut"] : NULL));
$date_absence_eleve_fin = isset($_POST["date_absence_eleve_fin"]) ? $_POST["date_absence_eleve_fin"] : (isset($_GET["date_absence_eleve_fin"]) ? $_GET["date_absence_eleve_fin"] : (isset($_SESSION["date_absence_eleve_fin"]) ? $_SESSION["date_absence_eleve_fin"] : NULL));
$type_extrait = isset($_POST["type_extrait"]) ? $_POST["type_extrait"] : (isset($_GET["type_extrait"]) ? $_GET["type_extrait"] :(isset($_SESSION["type_extrait"]) ? $_SESSION["type_extrait"] : NULL));
$affichage = isset($_POST["affichage"]) ? $_POST["affichage"] : (isset($_GET["affichage"]) ? $_GET["affichage"] : NULL);
$tri = isset($_POST["tri"]) ? $_POST["tri"] : (isset($_GET["tri"]) ? $_GET["tri"] : NULL);
$sans_commentaire = isset($_POST["sans_commentaire"]) ? $_POST["sans_commentaire"] : (isset($_GET["sans_commentaire"]) ? $_GET["sans_commentaire"] : Null);
$non_traitees = isset($_POST["non_traitees"]) ? $_POST["non_traitees"] : (isset($_GET["non_traitees"]) ? $_GET["non_traitees"] : Null);
$ods2 = isset($_POST["ods2"]) ? $_POST["ods2"] : (isset($_GET["ods2"]) ? $_GET["ods2"] : Null);
$cpt_classe = isset($_POST["cpt_classe"]) ? $_POST["cpt_classe"] : (isset($_GET["cpt_classe"]) ? $_GET["cpt_classe"] : null);

if (isset($id_classe) && $id_classe != null)
    $_SESSION['id_classe_abs'] = $id_classe;
if (isset($date_absence_eleve_debut) && $date_absence_eleve_debut != null)
    $_SESSION['date_absence_eleve_debut'] = $date_absence_eleve_debut;
if (isset($date_absence_eleve_fin) && $date_absence_eleve_fin != null)
    $_SESSION['date_absence_eleve_fin'] = $date_absence_eleve_fin;
if (isset($type_extrait) && $type_extrait != null)
    $_SESSION['type_extrait'] = $type_extrait;

if ($date_absence_eleve_debut != null) {
    $dt_date_absence_eleve_debut = new DateTime(str_replace("/", ".", $date_absence_eleve_debut));
} else {
    $dt_date_absence_eleve_debut = new DateTime('now');
    $dt_date_absence_eleve_debut->setDate($dt_date_absence_eleve_debut->format('Y'), $dt_date_absence_eleve_debut->format('m') - 1, $dt_date_absence_eleve_debut->format('d'));
}
if ($date_absence_eleve_fin != null) {
    $dt_date_absence_eleve_fin = new DateTime(str_replace("/", ".", $date_absence_eleve_fin));
} else {
    $dt_date_absence_eleve_fin = new DateTime('now');
}
$dt_date_absence_eleve_debut->setTime(0, 0, 0);
$dt_date_absence_eleve_fin->setTime(23, 59, 59);
$inverse_date=false;
if($dt_date_absence_eleve_debut->format("U")>$dt_date_absence_eleve_fin->format("U")){
    $date2=clone $dt_date_absence_eleve_fin;
    $dt_date_absence_eleve_fin= $dt_date_absence_eleve_debut;
    $dt_date_absence_eleve_debut= $date2;
    $inverse_date=true;
}
// fonction de formatage des dates de debut et de fin
function getDateDescription($date_debut,$date_fin) {
	    $message = '';
	    if (strftime("%a %d/%m/%Y", $date_debut)==strftime("%a %d/%m/%Y", $date_fin)) {
		$message .= 'le ';
		$message .= (strftime("%a %d/%m/%Y", $date_debut));
		$message .= ' entre  ';
		$message .= (strftime("%H:%M", $date_debut));
		$message .= ' et ';
		$message .= (strftime("%H:%M", $date_fin));

	    } else {
		$message .= ' entre le ';
		$message .= (strftime("%a %d/%m/%Y %H:%M", $date_debut));
		$message .= ' et ';
		$message .= (strftime("%a %d/%m/%Y %H:%M", $date_fin));
	    }
	    return $message;
	}
//paramétrage des options affichées en fonction du statut
$affichage_liens=true;
$affichage_commentaires_html=true;
if ($utilisateur->getStatut() == "professeur" || $utilisateur->getStatut() == "autre"){
    $affichage_liens=false;
    $affichage_commentaires_html=false;
    if($affichage != null && $affichage != '' && $affichage != 'html' ){
        $affichage == 'html'; //on empeche l'export odt et ods pour les autres statuts
    }
}

$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
//**************** EN-TETE *****************
$titre_page = "Les absences";

//suppression des données en session (sauf dans le cas d'un export html et odt)
if(isset($_SESSION['donnees_bilan']) && (is_null($affichage) || ($affichage=='html'&& is_null($cpt_classe)))){
    unset($_SESSION['donnees_bilan']);
}

//gestion des passages classe par classe
//On lance les calculs classe par classe pour toutes les classes si :
// -la durée est inférieure à 7 jours 
// - si on est dans un affichage html seulement
if(is_null($cpt_classe)) $cpt_classe=0;
$limite_temps=true;
$boucle=false;
$fin_boucle=false;
if(($id_classe=='-1' && $affichage=='html') && (is_null($id_eleve) || $id_eleve=='')  &&  $cpt_classe<=count( $_SESSION['classes_bilan'])){
    if($limite_temps && ($dt_date_absence_eleve_fin->format('U')-$dt_date_absence_eleve_debut->format('U'))>(7*24*3600) ){
        $message=' L\'intervalle de temps choisi pour toutes les classes doit être inférieur à 7 jours ';
        $affichage='';
    }else{
        if($cpt_classe==(count($_SESSION['classes_bilan']))){
        $fin_boucle=true;
        }
        if($cpt_classe<count($_SESSION['classes_bilan'])){
            require_once("../lib/header.inc");
            echo'<div id="contain_div" class="css-panes">Veuillez patienter... calculs par classe en cours...<br />
            Classes traitées  : '.$cpt_classe.' sur '.count($_SESSION['classes_bilan']).'</div>';
            $boucle=true;
       }
    }    
}

// pas de header ou menu dans le cas de l'export odt ou si on est dans une boucle classe par classe sauf pour le dernier passage
// début de l'affichage des options
if ($affichage != 'ods' && $affichage != 'odt' && (!$boucle || $fin_boucle) ) {
    require_once("../lib/header.inc");
    include('menu_abs2.inc.php');
    include('menu_bilans.inc.php');
?>
    <div id="contain_div" class="css-panes">
        <?php if (isset($message)){
          echo'<h2 class="no">'.$message.'</h2>';
        }?>
         <p>
             <strong>La recherche sur toutes les classes n'est permise que pour une durée de 7 jours maximum.</strong>

        <p>
            Cette page permet de regrouper jour par jour les saises du même type (non traitées ou ayant le même traitement) et les informations du traitement.<br />
            Pour des saisies ayant des traitements multiples , le décompte des demi-journées correspondantes peut donc apparaitre plusieurs fois. 
            Le total réel des demi-journées calculé par le module s'affiche sous le nom de l'élève.
        </p>
        <p>
            Toute modification doit être validée pour être prise en compte.
        </p>
        <?php if ($inverse_date) :?>
        <h3 class="no">Les dates de début et de fin ont été inversés.</h3>
        <?php endif; ?>
        <form name="bilan_individuel" action="bilan_individuel.php" method="post">
            <fieldset>
              <legend>Paramétrage de l'export (dates, classes, tri...) et affichage</legend>
            <h3>Bilan individuel du
                <input size="10" id="date_absence_eleve_1" name="date_absence_eleve_debut" value="<?php echo $dt_date_absence_eleve_debut->format('d/m/Y') ?>" />
                <script type="text/javascript">
                    Calendar.setup({
                        inputField     :    "date_absence_eleve_1",     // id of the input field
                        ifFormat       :    "%d/%m/%Y",      // format of the input field
                        button         :    "date_absence_eleve_1",  // trigger for the calendar (button ID)
                        align          :    "Bl",           // alignment (defaults to "Bl")
                        singleClick    :    true
                    });
                </script>
                                        	au
                <input size="10" id="date_absence_eleve_2" name="date_absence_eleve_fin" value="<?php echo $dt_date_absence_eleve_fin->format('d/m/Y') ?>" />
            <script type="text/javascript">
                Calendar.setup({
                    inputField     :    "date_absence_eleve_2",     // id of the input field
                    ifFormat       :    "%d/%m/%Y",      // format of the input field
                    button         :    "date_absence_eleve_2",  // trigger for the calendar (button ID)
                    align          :    "Bl",           // alignment (defaults to "Bl")
                    singleClick    :    true
                });
            </script>
        </h3>
          <?php
            if ($id_eleve!==null && $id_eleve!=''){
                $eleve=EleveQuery::create()->filterByIdEleve($id_eleve)->findOne();
                $nom_eleve=$eleve->getNom();
                $id_classe=$eleve->getClasse()->getId();
            }
            ?>
            Nom (facultatif) : <input type="text" name="nom_eleve" size="10" value="<?php echo $nom_eleve ?>" onChange="document.bilan_individuel.id_eleve.value='';"/>
            <input type="hidden" name="id_eleve" value="<?php echo $id_eleve ?>"/>
            <input type="hidden" name="affichage" value="<?php echo $affichage ?>"/>
           
            <?php
            //on affiche une boite de selection avec les classe
            if ((getSettingValue("GepiAccesAbsTouteClasseCpe") == 'yes' && $utilisateur->getStatut() == "cpe") || $utilisateur->getStatut() == "autre" ) {
                $classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
            } else {
                $classe_col = $utilisateur->getClasses();
            }
            if (!$classe_col->isEmpty()) {
                if(isset($_SESSION['classes_bilan'])) unset($_SESSION['classes_bilan']);
                echo ("Classe : <select name=\"id_classe\" onChange='document.bilan_individuel.id_eleve.value=\"\";'>");
                if($utilisateur->getStatut() != "autre" && $utilisateur->getStatut() != "professeur" ){
                    echo "<option value='-1'>Toutes les classes</option>\n";
                }
                foreach ($classe_col as $classe) {
                    echo "<option value='" . $classe->getId() . "'";
                    if ($id_classe == $classe->getId())
                        echo " selected='selected' ";
                    echo ">";
                    echo $classe->getNom();
                    echo "</option>\n";
                    $_SESSION['classes_bilan'][]=$classe->getId();
                }                
                echo "</select> ";                
            } else {
                echo 'Aucune classe avec élève affecté n\'a été trouvée';
            }
            ?>
            Type :
            <select style="width:200px" name="type_extrait">
                <option value='1' <?php
            if ($type_extrait == '1') {
                echo 'selected';
            }
            ?>
			>Données occasionnant un manquement aux obligations de présence</option>
                <option value='2' <?php
                        if ($type_extrait == '2') {
                            echo 'selected';
                        }
            ?>>Liste de toutes les données</option>
            </select><br />            
            
            <input type="checkbox" name="tri" value="tri"  <?php
            if($tri=='tri') {
                echo'checked';
            }            
            ?>
			> Tri des données par type (Manquement aux obligations de présence, retard)
			<br />
            <?php if($utilisateur->getStatut() == "cpe" || $utilisateur->getStatut() == "scolarite"):?>
            <input type="checkbox" name="ods2" value="ods2"  <?php
            if($ods2) {
                echo'checked';
            } ?> 
			> Ne pas répéter les informations globales de l'élève par ligne dans l'export tableur (pour totaux par colonne)
            <br />
            <input type="checkbox" name="sans_commentaire" value="no"  <?php
            if($sans_commentaire) {
                echo'checked';
            } ?>
			> Ne pas afficher les commentaires dans l'export ods et odt
            <?php endif; ?>
            <?php if($utilisateur->getStatut() == "cpe"):?>
            <br />
            <input type="checkbox" name="non_traitees" value="non_traitees"  <?php
            if($non_traitees) {
                echo'checked';
            } ?>
			> N'afficher que les saisies non traitées ou sans type (non défini et non couverte par un autre traitement)
            <br />            
            <?php endif; ?>
            <button type="submit" name="affichage" value="html">Valider les modifications et afficher à l'écran</button>
        </fieldset>
		<br />
        <?php if($affichage_liens):?>
        <fieldset style="width:600px;">
            <legend>Choix du mode de sortie des données</legend>            
            <button type="submit" name="affichage" value="ods" <?php
                 if($affichage==Null || $affichage=='') echo'disabled';?>>Exporter dans un tableur (ods)</button>
            <button type="submit" name="affichage" value="odt" <?php
                 if($affichage==Null || $affichage=='') echo'disabled';?>>Exporter dans un traitement de texte (odt)</button>
        </fieldset>
         <?php endif; ?>
    </form>
    <?php
}
// fin de l'affichage des options
// début de la mise en session des données extraites
//if ($affichage != null && $affichage != '' && !$fin_boucle) {
if ($affichage =='html' && !$fin_boucle) {
$eleve_query = EleveQuery::create();
if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
    } else {
	$eleve_query->filterByUtilisateurProfessionnel($utilisateur);
    }
if ($id_classe !== null && $id_classe != -1 ) {
    $eleve_query->useJEleveClasseQuery()->filterByIdClasse($id_classe)->endUse();
}
if($boucle){
    $eleve_query->useJEleveClasseQuery()->filterByIdClasse($_SESSION['classes_bilan'][$cpt_classe])->endUse();
}
if ($nom_eleve !== null && $nom_eleve != '') {
    $eleve_query->filterByNom('%'.$nom_eleve.'%');
}
if ($id_eleve !== null && $id_eleve != '') {
    $eleve_query->filterByIdEleve($id_eleve);
}
$eleve_col = $eleve_query->orderByNom()->orderByPrenom()->distinct()->find();
if ($eleve_col->isEmpty()) {
    if ($boucle) {
        $cpt_classe++;
        echo"<script type='text/javascript'>refresh('$cpt_classe','$affichage','$tri','$sans_commentaire','$ods2','$non_traitees','$nom_eleve');</script>";
        die();
    }
    echo"<h2 class='no'>Aucun élève avec les paramètres sélectionnés n'a été trouvé.</h2>";
    die();
}
$precedent_eleve_id = null;
if (isset($_SESSION['donnees_bilan'])){
    $donnees = unserialize($_SESSION['donnees_bilan']);
}
foreach ($eleve_col as $eleve) {    
    $eleve_id = $eleve->getIdEleve();
    //on initialise les donnees pour le nouvel eleve
    if ($precedent_eleve_id != $eleve_id) {
        $donnees[$eleve_id]['nom'] = $eleve->getNom();
        $donnees[$eleve_id]['prenom'] = $eleve->getPrenom();
        $donnees[$eleve_id]['classe'] = $eleve->getClasseNom();        
        $donnees[$eleve_id]['nbre_lignes_total'] = 0;
    }
    // on récupère les saisies de l'élève
    $saisie_query = AbsenceEleveSaisieQuery::create()
                    ->filterByPlageTemps($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)
                    ->filterByEleveId($eleve->getIdEleve());

    if ($type_extrait == '1') {
        $saisie_query->filterByManquementObligationPresence(true);
    }
    $saisie_query->orderByDebutAbs();    
    $saisie_col = $saisie_query->find();

    // on traite les saisies et on stocke les informations dans un tableau
    foreach ($saisie_col as $saisie) {
        if ($type_extrait == '1' && !$saisie->getManquementObligationPresence()) {
            continue;
        }
        if (!is_null($non_traitees) && $non_traitees != '' && $saisie->getTraitee() && $saisie->hasTypeSaisie()) {
            continue;
        }
        if ($saisie->getRetard()) {
            if ($tri != null && $tri != '') {
                $type_tab = 'retard';
            } else {
                $type_tab = 'sans';
            }
            $type_css = 'couleur_retard';
        } elseif ($saisie->getManquementObligationPresence()) {
            if ($tri != null && $tri != '') {
                $type_tab = 'manquement';
            } else {
                $type_tab = 'sans';
            }
            $type_css = 'couleur_manquement';
        } else {
            if ($tri != null && $tri != '') {
                $type_tab = 'sans_manquement';
            } else {
                $type_tab = 'sans';
            }
            $type_css = '';
        }
        if ($saisie->getTraitee()) {
            foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {               
                if (!isset($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()])) {
                    $donnees[$eleve_id]['nbre_lignes_total']++;
                }
                $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['saisies'][] = $saisie->getId();               
                if (isset($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates'])) {
                    if ($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates']['debut'] > $saisie->getDebutAbs('U')) {
                        $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates']['debut'] = $saisie->getDebutAbs('U');
                    }
                    if ($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates']['fin'] < $saisie->getFinAbs('U')) {
                        $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates']['fin'] = $saisie->getFinAbs('U');
                    }
                } else {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates'] = Array('debut' => $saisie->getDebutAbs('U'), 'fin' => $saisie->getFinAbs('U'));
                }
                if ($traitement->getAbsenceEleveType() != Null) {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['type'] = $traitement->getAbsenceEleveType()->getNom();
                } else {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['type'] = 'Non défini';
                }
                if ($traitement->getAbsenceEleveMotif() != Null) {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['motif'] = $traitement->getAbsenceEleveMotif()->getNom();
                } else {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['motif'] = '-';
                }
                if ($traitement->getAbsenceEleveJustification() != Null) {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['justification'] = $traitement->getAbsenceEleveJustification()->getNom();
                } else {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['justification'] = '-';
                }
                if ($saisie->getCommentaire() !== '') {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['commentaires'][] = $saisie->getCommentaire();
                }
                $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['type_css'] = $type_css;
            }
        } else {
            if (!isset($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees'])) {
                $donnees[$eleve_id]['nbre_lignes_total']++;
            }
            $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['saisies'][] = $saisie->getId();            
            $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['type'] = 'Non traitée(s)';
            $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['motif'] = '-';
            $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['justification'] = '-';
            if (isset($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates'])) {
                if ($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates']['debut'] > $saisie->getDebutAbs('U')) {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates']['debut'] = $saisie->getDebutAbs('U');
                }
                if ($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates']['fin'] < $saisie->getFinAbs('U')) {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates']['fin'] = $saisie->getFinAbs('U');
                }
            } else {
                $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates'] = Array('debut' => $saisie->getDebutAbs('U'), 'fin' => $saisie->getFinAbs('U'));
            }
            if ($saisie->getCommentaire() !== '') {
                $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['commentaires'][] = $saisie->getCommentaire();
            }
            $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['type_css'] = $type_css;
        }        
    }    
    $precedent_eleve_id = $eleve->getIdEleve();    
}
//on récupère les demi-journées globales et par ligne
foreach ($donnees as $id => &$eleve) {
    if(!isset($eleve['infos_saisies'])) continue;
    $propel_eleve = EleveQuery::create()->filterByIdEleve($id)->findOne();
    $eleve['demi_journees'] = $propel_eleve->getDemiJourneesAbsence($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)->count();
    $eleve['non_justifiees'] = $propel_eleve->getDemiJourneesNonJustifieesAbsence($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)->count();
    $eleve['retards'] = $propel_eleve->getRetards($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)->count();    
    foreach ($eleve['infos_saisies'] as $type_tab => &$value2) {
        foreach ($value2 as &$journee) {
            foreach ($journee as $key => &$value) {
            $abs_col = AbsenceEleveSaisieQuery::create()->filterById($value['saisies'])->orderByDebutAbs()->find();
            $value['demi_journees'] = $propel_eleve->getDemiJourneesAbsenceParCollection($abs_col)->count();
            $value['demi_journees_non_justifiees'] = $propel_eleve->getDemiJourneesNonJustifieesAbsenceParCollection($abs_col)->count();
            $value['demi_journees_justifiees'] = $value['demi_journees'] - $value['demi_journees_non_justifiees'];            
            }
        }
    }
}
//on met toutes les donnees en session
$_SESSION['donnees_bilan']=serialize($donnees);
//en cas de bouclage par classe on recharge la page pour passer à la classe suivante
if($boucle){   
    $cpt_classe++;    
    echo"<script type='text/javascript'>refresh('$cpt_classe','$affichage','$tri','$sans_commentaire','$ods2','$non_traitees','$nom_eleve');</script>";die();
}
}
// fin de la mise en session des données extraites

// début des export
  //export html
if ($affichage == 'html') {    
echo '<table border="1" cellspacing="0" align="center">';
echo '<tr >';
echo '<td align="center">';
echo 'Informations sur l\'élève';
echo '</td>';
echo '<td align="center">';
echo 'Saisies ';
echo '</td>';
echo '<td align="center">';
echo 'Décompte J';
echo '</td>';
echo '<td align="center">';
echo 'Décompte NJ';
echo '</td>';
echo '<td align="center">';
echo 'Type';
echo '</td>';
echo '<td align="center">';
echo 'Motif';
echo '</td>';
echo '<td align="center">';
echo 'Justification';
echo '</td>';
if($affichage_commentaires_html){
   echo '<td align="center">';
   echo 'Commentaire(s)';
   echo '</td>';
}
echo '</tr>';
$precedent_eleve_id = Null;
if(isset($_SESSION['donnees_bilan'])) $donnees=unserialize($_SESSION['donnees_bilan']);
foreach ($donnees as $id => $eleve) {
    if(!isset($eleve['infos_saisies'])){
        continue;
    }
    if($tri!=null && $tri!='') {
        ksort($eleve['infos_saisies']);
    }
    foreach ($eleve['infos_saisies'] as $type_tab=>$value2) {
        foreach ($value2 as $journee) {
            foreach ($journee as $key => $value) {                
                $style=$value['type_css'];
                echo'<tr>';
                if ($precedent_eleve_id != $id) {                    
                    echo '<td rowspan=' . $eleve['nbre_lignes_total'] . '>';
                    echo '<a href="bilan_individuel.php?id_eleve=' . $id . '&affichage=html&tri='.$tri.'&sans_commentaire='.$sans_commentaire.'">';
                    echo '<b>' . $eleve['nom'] . ' ' . $eleve['prenom'] . '</b></a><br/> (' . $eleve['classe'] . ')';
                    if($affichage_liens){
                      echo'<a href="bilan_individuel.php?id_eleve=' . $id . '&affichage=ods&tri='.$tri.'&sans_commentaire='.$sans_commentaire.'"><img src="../images/icons/ods.png" title="export ods"></a>
                      <a href="bilan_individuel.php?id_eleve=' . $id . '&affichage=odt&tri='.$tri.'&sans_commentaire='.$sans_commentaire.'"><img src="../images/icons/odt.png" title="export odt"></a><br/><br/>';
                    }else{
                        echo'<br />';
                    }
                    echo '<u><i>Absences :</i></u> <br />';
                    if (strval($eleve['demi_journees']) == 0) {
                        echo 'Aucune demi-journée';
                    } else {
                        echo '<b>' . $eleve['demi_journees'] . '</b> demi-journée';
                        if (strval($eleve['demi_journees']) > 1)
                            echo's';

                        if (strval($eleve['demi_journees'] - $eleve['non_justifiees']) != 0) {
                            echo' <br /> ';
                            echo 'dont ' . strval($eleve['demi_journees'] - $eleve['non_justifiees']) . ' justifiée';
                            if (strval($eleve['demi_journees'] - $eleve['non_justifiees']) > 1)
                                echo's';
                        }

                        if (strval($eleve['non_justifiees']) != 0) {
                            echo'<br />';
                            echo 'dont <b>' . $eleve['non_justifiees'] . ' non justifiée</b>';
                            if (strval($eleve['non_justifiees']) > 1)
                                echo's';
                        }
                    }
                    echo'<br /><br />';
                    echo '<u><i>Retards :</i></u><br />';
                    if (strval($eleve['retards']) == 0) {
                        echo 'Aucun retard';
                    } else {
                        echo $eleve['retards'] . ' retard';
                        if (strval($eleve['retards']) > 1)
                            echo's';
                    }
                    echo '</td>';
                }
                echo '<td class="'.$style.'">';
                if($affichage_liens){
                    echo '<a href="./liste_saisies_selection_traitement.php?saisies=' . serialize($value['saisies']) . '" target="_blank">' . getDateDescription($value['dates']['debut'], $value['dates']['fin']) . '<a>';
                }else{
                    echo getDateDescription($value['dates']['debut'], $value['dates']['fin']) ;
                }
                echo '</td>';                
                echo '<td align="center" class="'.$style.'">';
                if (!0 == $value['demi_journees_justifiees'])
                    echo '<font class="ok">' . $value['demi_journees_justifiees'] . '</font>';
                echo '</td>';
                echo '<td align="center" class="'.$style.'">';
                if (!0 == $value['demi_journees_non_justifiees'])
                    echo '<font class="no">' . $value['demi_journees_non_justifiees'] . '</font>';
                echo '</td>';
                echo '<td class="'.$style.'">';               
                if ($value['type'] !== 'Non traitée(s)') {
                    $class = '';
                    if ($value['type'] == 'Non défini') {
                        $class = 'orange';
                    }
                    if($affichage_liens){
                        echo'<a class="' . $class . '" href="./visu_traitement.php?id_traitement=' . $key . '" target="_blank">' . $value['type'] . '</a>';
                    }else{
                        echo'<font class="' . $class . '">' . $value['type'] . '</font>';
                    }
                } else {
                    echo '<font class="orange">' . $value['type'] . '</font>';
                }
                echo '</td>';                
                echo '<td class="'.$style.'">';
                echo $value['motif'];
                echo '</td>';
                echo '<td class="'.$style.'">';
                echo $value['justification'];
                echo '</td>';
                if($affichage_commentaires_html){
                    echo '<td class="'.$style.'">';
                    if (isset($value['commentaires'])) {
                        $besoin_echo_virgule = false;
                        foreach ($value['commentaires'] as $commentaire) {
                            if ($besoin_echo_virgule) {
                                echo ', ';
                            }
                            echo $commentaire;
                            $besoin_echo_virgule = true;
                            }
                      }
                      echo '</td>';
                }
                echo '</tr>';                
                $precedent_eleve_id = $id;
            }
        }
    }
}
echo '<h5>Extraction réalisée le '.date("d/m/Y - H:i").'</h5>';
//fin export html; debut export odt et ods
} else if ($affichage == 'ods' || $affichage == 'odt') {
include_once '../orm/helpers/AbsencesNotificationHelper.php';
if(isset($_SESSION['donnees_bilan'])){
    $donnees=unserialize($_SESSION['donnees_bilan']);
}
if ($affichage == 'ods') {
    $extension='ods';
    $export = array();
    foreach ($donnees as $id => $eleve) {
        if($id_eleve!=null && $id_eleve !='' && $id!=$id_eleve ){
            continue;
        }
        $indice=TRUE;
        if($tri!=null && $tri!='') {
            ksort($eleve['infos_saisies']);
        }
        foreach ($eleve['infos_saisies'] as $type_tab) {
            foreach ($type_tab as $journee) {
                foreach ($journee as $key => $value) {
                    if($indice){
                        $nom = $eleve['nom'];
                        $prenom = $eleve['prenom'];
                        $classe = $eleve['classe'];
                        $total_demi_journees = strval($eleve['demi_journees']);
                        $total_demi_journees_justifiees = strval($eleve['demi_journees'] - $eleve['non_justifiees']);
                        $total_demi_journees_non_justifiees = strval($eleve['non_justifiees']);
                        $retards = $eleve['retards'];                       
                    }else{
                        $nom = '';
                        $prenom = '';
                        $classe = '';
                        $total_demi_journees = '';
                        $total_demi_journees_justifiees = '';
                        $total_demi_journees_non_justifiees = '';
                        $retards = '';
                    }
                    if(!is_null($ods2)){                        
                        $indice=FALSE;
                    }
                    $dates = getDateDescription($value['dates']['debut'], $value['dates']['fin']);                    
                    $ligne_demi_journees_non_justifiees=$value['demi_journees_non_justifiees'];
                    $ligne_demi_journees_justifiees=$value['demi_journees_justifiees'];
                    $type = $value['type'];
                    $motif = $value['motif'];
                    $justification = $value['justification'];
                    $export_commentaire = '';
                    if (isset($value['commentaires']) && (is_null($sans_commentaire) || $sans_commentaire=='')) {
                        $besoin_echo_virgule = false;
                        foreach ($value['commentaires'] as $commentaire) {
                            if ($besoin_echo_virgule) {
                                $export_commentaire.= ', ';
                            }
                            $export_commentaire.=$commentaire;
                            $besoin_echo_virgule = true;
                        }
                    }
                    $export[] = Array('nom' => $nom, 'prenom' => $prenom, 'classe' => $classe,
                        'total_demi_journees' => $total_demi_journees,
                        'total_demi_journees_justifiees' => $total_demi_journees_justifiees,
                        'total_demi_journees_non_justifiees' => $total_demi_journees_non_justifiees,
                        'retards' => $retards,
                        'dates' => $dates,
                        'ligne_demi_journees_non_justifiees' => $ligne_demi_journees_non_justifiees,
                        'ligne_demi_journees_justifiees' => $ligne_demi_journees_justifiees,
                        'type' => $type,
                        'motif' => $motif,
                        'justification' => $justification,
                        'export_commentaire' => $export_commentaire);
                    $eleve_current =Null;
                    $abs_col = Null;
                    $ligne_demi_journees=Null;
                    $ligne_demi_journees_non_justifiees=Null;
                    $ligne_demi_journees_justifiees=Null;
                }
            }
        }
    }    
} else {
    $extension = 'odt';
    $export = array();
    foreach ($donnees as $id => $eleve) {        
        if($id_eleve!=null && $id_eleve !='' && $id!=$id_eleve ){
            continue;
        }
        if($tri!=null && $tri!='') {
            ksort($eleve['infos_saisies']);
        }
        foreach ($eleve['infos_saisies'] as $type_tab) {
            foreach ($type_tab as $journee) {
                foreach ($journee as $key => $value) {
                    $nom = $eleve['nom'];
                    $prenom = $eleve['prenom'];
                    $classe = $eleve['classe'];
                    $total_demi_journees = strval($eleve['demi_journees']);
                    $total_demi_journees_justifiees = strval($eleve['demi_journees'] - $eleve['non_justifiees']);
                    $total_demi_journees_non_justifiees = strval($eleve['non_justifiees']);
                    $retards = $eleve['retards'];
                    $dates = getDateDescription($value['dates']['debut'], $value['dates']['fin']);                    
                    $ligne_demi_journees =$value['demi_journees'];
                    if($ligne_demi_journees >0){
                        $ligne_demi_journees_non_justifiees = $value['demi_journees_non_justifiees'];
                        if($ligne_demi_journees_non_justifiees==0){
                            $ligne_demi_journees_non_justifiees='';
                        }
                        $ligne_demi_journees_justifiees = $value['demi_journees_justifiees'];;
                        if($ligne_demi_journees_justifiees==0){
                            $ligne_demi_journees_justifiees='';
                        }
                    }else{
                        $ligne_demi_journees_non_justifiees = '-';
                        $ligne_demi_journees_justifiees = '-';
                    }                    
                    $type = $value['type'];
                    $motif = $value['motif'];
                    $justification = $value['justification'];
                    $export_commentaire = '';
                    if (isset($value['commentaires']) && (is_null($sans_commentaire) || $sans_commentaire=='')) {
                        $besoin_echo_virgule = false;
                        foreach ($value['commentaires'] as $commentaire) {
                            if ($besoin_echo_virgule) {
                                $export_commentaire.= ', ';
                            }
                            $export_commentaire.=$commentaire;
                            $besoin_echo_virgule = true;
                        }
                    }
                    if (!isset($export[$id])) {
                        $export[$id] = Array('nom' => $nom, 'prenom' => $prenom, 'classe' => $classe,
                            'total_demi_journees' => $total_demi_journees,
                            'total_demi_journees_justifiees' => $total_demi_journees_justifiees,
                            'total_demi_journees_non_justifiees' => $total_demi_journees_non_justifiees,
                            'retards' => $retards);
                    }
                    $export[$id]['lignes'][] = Array('dates' => $dates,
                        'ligne_demi_journees_non_justifiees' => $ligne_demi_journees_non_justifiees,
                        'ligne_demi_journees_justifiees' => $ligne_demi_journees_justifiees,
                        'type' => $type,
                        'motif' => $motif,
                        'justification' => $justification,
                        'export_commentaire' => $export_commentaire);
                }
            }
        }
    }    
}
$extraction_bilans = repertoire_modeles('absence_extraction_bilan.'.$extension);
$TBS = AbsencesNotificationHelper::MergeInfosEtab($extraction_bilans);
$titre = 'Bilan individuel du ' . $dt_date_absence_eleve_debut->format('d/m/Y') . ' au ' . $dt_date_absence_eleve_fin->format('d/m/Y');
$classe = null;
if ($id_classe != null && $id_classe != '' && $id_eleve == null) {
    $classe = ClasseQuery::create()->findOneById($id_classe);
    if ($classe != null) {
        $titre .= ' pour la classe ' . $classe->getNom();
    }
}
if ($nom_eleve != null && $nom_eleve != '') {
    $titre .= ' pour les élèves dont le nom ou le prénom contient ' . $nom_eleve;
}
if ($id_eleve != null && $id_eleve != '') {
    $eleve_current=  EleveQuery::create()->filterByIdEleve($id_eleve)->findOne();
    $titre .= ' pour ' . $eleve_current->getPrenom() . ' ' . $eleve_current->getNom();
}
$TBS->MergeField('titre', $titre);
$TBS->MergeField('date_debut', $dt_date_absence_eleve_debut->format("d/m/Y"));
$TBS->MergeField('date_fin', $dt_date_absence_eleve_fin->format("d/m/Y"));
$TBS->MergeBlock('export', $export);
// Output as a download file (some automatic fields are merged here)
$nom_fichier = 'extrait_bilan_';
if ($classe != null) {
    $nom_fichier .= $classe->getNom() . '_';
}
$nom_fichier .= $dt_date_absence_eleve_fin->format("d_m_Y") . '.'.$extension ;
$TBS->Show(OPENTBS_DOWNLOAD + TBS_EXIT, $nom_fichier);
}
?>
	</div>
<?php
require("../lib/footer.inc.php");
?>