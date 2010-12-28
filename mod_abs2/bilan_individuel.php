<?php
/**
 *
 * @version $Id: bilan_du_jour.php 5267 2010-09-13 17:52:45Z jjacquard $
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
$sql = "INSERT INTO `droits` ( `id` , `administrateur` , `professeur` , `cpe` , `scolarite` , `eleve` , `responsable` , `secours` , `autre` , `description` , `statut` )
  VALUES ('/mod_abs2/bilan_individuel.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Bilan individuel des absences eleve', '')
  ON DUPLICATE KEY UPDATE `CPE` = 'V'";

$result = mysql_query($sql);
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
//$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
//**************** EN-TETE *****************
$titre_page = "Les absences";
if ($affichage != 'ods' && $affichage != 'odt') {// on n'affiche pas de html pour ods et odt
    require_once("../lib/header.inc");

    include('menu_abs2.inc.php');
    include('menu_bilans.inc.php');
?>
    <div id="contain_div" class="css-panes">
        <p>
            Cette page permet de regrouper jour par jour les saises du même type (non traitées ou ayant le même traitement) et les informations du traitement.<br />
            Pour des saisies ayant des traitements multiples , le décompte des demi-journées correspondantes peut donc apparaitre plusieurs fois. 
            Le total réel des demi-journées calculé par le module s'affiche sous le nom de l'élève.
        </p>
        <?php if ($inverse_date) :?>
        <h3 class="no">Les dates de début et de fin ont été inversés.</h3>
        <?php endif; ?>
        <form name="bilan_individuel" action="bilan_individuel.php" method="post">
            <fieldset>
              <legend>Paramétrage de l'export (dates, classes, tri...)</legend>
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
            
            <input type="checkbox" name="tri" value="tri" onChange="document.bilan_individuel.submit();" <?php
            if($tri=='tri') {
                echo'checked';
            }            
            ?>
			> Tri des données par type (Manquement aux obligations de présence, retard)
			<br />
            <?php if($utilisateur->getStatut() == "cpe" || $utilisateur->getStatut() == "scolarite"):?>
            <input type="checkbox" name="ods2" value="ods2" onChange="document.bilan_individuel.submit();" <?php
            if($ods2) {
                echo'checked';
            } ?> 
			> Ne pas répéter les informations globales de l'élève par ligne dans l'export tableur (pour totaux par colonne)
            <br />
            <input type="checkbox" name="sans_commentaire" value="no" onChange="document.bilan_individuel.submit();" <?php
            if($sans_commentaire) {
                echo'checked';
            } ?>
			> Ne pas afficher les commentaires dans l'export ods et odt
            <?php endif; ?>
            <?php if($utilisateur->getStatut() == "cpe"):?>
            <br />
            <input type="checkbox" name="non_traitees" value="non_traitees" onChange="document.bilan_individuel.submit();" <?php
            if($non_traitees) {
                echo'checked';
            } ?>
			> N'afficher que les saisies non traitées ou sans type défini
            <br />
            <?php endif; ?>
        </fieldset>
		<br />
        <fieldset style="width:600px;">
            <legend>Validation des modifications et choix du mode de sortie des données</legend>
            <button type="submit" name="affichage" value="html">Afficher à l'écran</button>
            <?php if($affichage_liens):?>
            <button type="submit" name="affichage" value="ods">Exporter dans un tableur (ods)</button>
            <button type="submit" name="affichage" value="odt">Exporter dans un traitement de texte (odt)</button>
            <?php endif; ?>
        </fieldset>
    </form>
    <?php
}
if ($affichage != null && $affichage != '') {
$eleve_query = EleveQuery::create();
if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
    } else {
	$eleve_query->filterByUtilisateurProfessionnel($utilisateur);
    }
if ($id_classe !== null && $id_classe != -1) {
    $eleve_query->useJEleveClasseQuery()->filterByIdClasse($id_classe)->endUse();
}
if ($nom_eleve !== null && $nom_eleve != '') {
    $eleve_query->filterByNom('%'.$nom_eleve.'%');
}
if ($id_eleve !== null && $id_eleve != '') {
    $eleve_query->filterByIdEleve($id_eleve);
}
$eleve_col = $eleve_query->distinct()->find();
if($eleve_col->isEmpty()){
    echo"<h2 class='no'>Aucun élève avec les paramètres sélectionnés n'a été trouvé.</h2>";
    die();
}
$saisie_query = AbsenceEleveSaisieQuery::create()
                ->filterByPlageTemps($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)
                ->filterByEleveId($eleve_col->toKeyValue('IdEleve', 'IdEleve'));

if ($type_extrait == '1') {
    $saisie_query->filterByManquementObligationPresence(true);
}

$saisie_query->useEleveQuery()->orderByNom()->orderByPrenom()->endUse();
$saisie_query->orderByDebutAbs();
$saisie_col = $saisie_query->find();
$eleve_id = Null;
$donnees = Array();
foreach ($saisie_col as $saisie) {
    if ($type_extrait == '1' && !$saisie->getManquementObligationPresence()) {
        continue;
    }
    if (!is_null($non_traitees) && $saisie->getTraitee() && $saisie->hasTypeSaisie()){
        continue;
    }
    $eleve_id = $saisie->getEleveId();
    if (!isset($donnees[$eleve_id])) {
        $donnees[$eleve_id]['nom'] = $saisie->getEleve()->getNom();
        $donnees[$eleve_id]['prenom'] = $saisie->getEleve()->getPrenom();
        $donnees[$eleve_id]['classe'] = $saisie->getEleve()->getClasseNom();
        $donnees[$eleve_id]['demi_journees'] = $saisie->getEleve()->getDemiJourneesAbsence($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)->count();
        $donnees[$eleve_id]['non_justifiees'] = $saisie->getEleve()->getDemiJourneesNonJustifieesAbsence($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)->count();
        $donnees[$eleve_id]['retards'] = $saisie->getEleve()->getRetards($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)->count();
        $donnees[$eleve_id]['nbre_lignes_total'] = 0;
    }
    
    if ($saisie->getRetard()) {
        if($tri!=null && $tri!=''){
            $type_tab = 'retard';
        }else{
            $type_tab='sans';
        }
        $type_css = 'couleur_retard';
    } elseif ($saisie->getManquementObligationPresence()) {
        if($tri!=null && $tri!=''){
            $type_tab = 'manquement';
        }else{
            $type_tab = 'sans';
        }
        $type_css = 'couleur_manquement';
    } else {
        if($tri!=null && $tri!=''){
            $type_tab = 'sans_manquement';
        }else{
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
            $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['type_css']=$type_css;            
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
        $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['type_css']=$type_css;            
    }    
}
}
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
foreach ($donnees as $id => $eleve) {
    if($tri!==null && $tri!='') {
        ksort($eleve['infos_saisies']);
    }
    foreach ($eleve['infos_saisies'] as $type_tab=>$value) {       
        foreach ($value as $journee) {
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
                $eleve_current = EleveQuery::create()->filterByIdEleve($id)->findOne();
                $abs_col = AbsenceEleveSaisieQuery::create()->filterById($value['saisies'])->orderByDebutAbs()->find();
                $demi_journees = $eleve_current->getDemiJourneesAbsenceParCollection($abs_col)->count();
                $demi_journees_non_justifiees = $eleve_current->getDemiJourneesNonJustifieesAbsenceParCollection($abs_col)->count();
                $demi_journees_justifiees = $demi_journees - $demi_journees_non_justifiees;
                echo '<td align="center" class="'.$style.'">';
                if (!0 == $demi_journees_justifiees)
                    echo '<font class="ok">' . $demi_journees_justifiees . '</font>';
                echo '</td>';
                echo '<td align="center" class="'.$style.'">';
                if (!0 == $demi_journees_non_justifiees)
                    echo '<font class="no">' . $demi_journees_non_justifiees . '</font>';
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
echo '<h5>Extraction faite le '.date("d/m/Y - H:i").'</h5>';
} else if ($affichage == 'ods' || $affichage == 'odt') {
include_once '../orm/helpers/AbsencesNotificationHelper.php';
if ($affichage == 'ods') {
    $extension='ods';
    $export = array();
    foreach ($donnees as $id => $eleve) {
        $indice=TRUE;
        if($tri!==null && $tri!='') {
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
                    $eleve_current = EleveQuery::create()->filterByIdEleve($id)->findOne();
                    $abs_col = AbsenceEleveSaisieQuery::create()->filterById($value['saisies'])->orderByDebutAbs()->find();
                    $ligne_demi_journees = $eleve_current->getDemiJourneesAbsenceParCollection($abs_col)->count();
                    $ligne_demi_journees_non_justifiees = $eleve_current->getDemiJourneesNonJustifieesAbsenceParCollection($abs_col)->count();
                    $ligne_demi_journees_justifiees = strval($ligne_demi_journees - $ligne_demi_journees_non_justifiees);
                    $type = $value['type'];
                    $motif = $value['motif'];
                    $justification = $value['justification'];
                    $export_commentaire = '';
                    if (isset($value['commentaires']) && is_null($sans_commentaire)) {
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
                }
            }
        }
    }
} else {
    $extension = 'odt';
    $export = array();
    foreach ($donnees as $id => $eleve) {
        if($tri!==null && $tri!='') {
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
                    $eleve_current = EleveQuery::create()->filterByIdEleve($id)->findOne();
                    $abs_col = AbsenceEleveSaisieQuery::create()->filterById($value['saisies'])->orderByDebutAbs()->find();
                    $ligne_demi_journees = $eleve_current->getDemiJourneesAbsenceParCollection($abs_col)->count();
                    if($ligne_demi_journees >0){
                        $ligne_demi_journees_non_justifiees = $eleve_current->getDemiJourneesNonJustifieesAbsenceParCollection($abs_col)->count();
                        if($ligne_demi_journees_non_justifiees==0){
                            $ligne_demi_journees_non_justifiees='';
                        }
                        $ligne_demi_journees_justifiees = strval($ligne_demi_journees - $ligne_demi_journees_non_justifiees);
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
                    if (isset($value['commentaires']) && is_null($sans_commentaire)) {
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