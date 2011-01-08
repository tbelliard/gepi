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
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activé.");
}
//initialisation des variables
$date_absence_eleve = isset($_POST["date_absence_eleve"]) ? $_POST["date_absence_eleve"] :(isset($_GET["date_absence_eleve"]) ? $_GET["date_absence_eleve"] :(isset($_SESSION["date_absence_eleve"]) ? $_SESSION["date_absence_eleve"] : NULL));
$nav_date=isset($_POST["nav_date"]) ? $_POST["nav_date"] :(isset($_GET["nav_date"]) ? $_GET["nav_date"] :Null);

if ($date_absence_eleve != null) {$_SESSION["date_absence_eleve"] = $date_absence_eleve;}
if ($date_absence_eleve != null) {
    try {
	$dt_date_absence_eleve = new DateTime(str_replace("/",".",$date_absence_eleve));
    } catch (Exception $x) {
	try {
	    $dt_date_absence_eleve = new DateTime($date_absence_eleve);
	} catch (Exception $x) {
	   $dt_date_absence_eleve = new DateTime('now');
	}
    }
} else {
    $dt_date_absence_eleve = new DateTime('now');
}
if($nav_date=="precedent"){
    date_date_set($dt_date_absence_eleve, $dt_date_absence_eleve->format('Y'), $dt_date_absence_eleve->format('m'), $dt_date_absence_eleve->format('d') - 1);
}
if($nav_date=="suivant"){
    date_date_set($dt_date_absence_eleve, $dt_date_absence_eleve->format('Y'), $dt_date_absence_eleve->format('m'), $dt_date_absence_eleve->format('d') + 1);
}

//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
$javascript_specifique[] = "mod_abs2/lib/include";
$titre_page = "Absences du jour";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
$dojo = true;
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

include('menu_abs2.inc.php');
include('menu_bilans.inc.php');
//===========================
//afichage des eleves.
$eleve_col = new PropelCollection();
//on fait une requete pour recuperer les eleves qui sont absents aujourd'hui
$dt_debut = clone $dt_date_absence_eleve;
$dt_debut->setTime(0,0,0);
$dt_fin = clone $dt_date_absence_eleve;
$dt_fin->setTime(23,59,59);
//on récupere les saisies avant puis on va filtrer avec les ids car filterManquementObligationPresence bug un peu avec les requetes imbriquées
$saisie_query = AbsenceEleveSaisieQuery::create()->filterByPlageTemps($dt_debut, $dt_fin)->setFormatter(ModelCriteria::FORMAT_ARRAY);
//On filtre les manquement à l'obligation de présence
$saisie_query->filterByManquementObligationPresence();
$saisie_col = $saisie_query->find();
$query = EleveQuery::create()->orderBy('Nom', Criteria::ASC)->orderBy('Prenom', Criteria::ASC)
    ->innerJoinWith('Eleve.EleveRegimeDoublant')
	->useAbsenceEleveSaisieQuery()
	->filterById($saisie_col->toKeyValue('Id', 'Id'))
	->endUse();
$eleve_col = $query->distinct()->find();
?>
<div class='css-panes' id='containDiv'>
    <p>
        Cette page affiche par créneau le nombre d'élèves ayant un manquement aux obligations de présence.<br />
        Les saisies renseignées en retard ne sont pas comptabilisées.<br />
    </p>
    <form action="./totaux_du_jour.php" name="totaux_du_jour" id="totaux_du_jour" method="post" style="width: 100%;">
        <fieldset style="width:380px;">
            <legend>Choix de la date</legend>
            <p class="expli_page choix_fin">
                <input type="hidden" name="date_absence_eleve" value="<?php echo $date_absence_eleve?>"/>
                <button dojoType="dijit.form.Button"  name="nav_date" type="submit"  value="precedent">Jour précédent</button>
                <input onchange="document.totaux_du_jour.submit()" style="width : 7em" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve" name="date_absence_eleve" value="<?php echo $dt_date_absence_eleve->format('Y-m-d')?>" />
                <button dojoType="dijit.form.Button"  name="nav_date" type="submit"  value="suivant">Jour suivant</button>
            </p>
        </fieldset>
    </form
    <?php
    $col_creneaux = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();
    echo'<table border="1" >';
    echo'<tr align="center">
        <th style="border: 1px solid black; background-color: grey;">Créneau</th>
        <th style="border: 1px solid black; background-color: grey;">Heure</th>
        <th style="border: 1px solid black; background-color: grey;">Nombre d\'élèves absents</th>
        <th style="border: 1px solid black; background-color: grey;">Nombre de demi_pensionnaires </th>
        <th style="border: 1px solid black; background-color: grey;">Nombre d\'internes</th>
        <th style="border: 1px solid black; background-color: grey;">Nombre d\'externes</th>
        </tr>';
    
    $nbre_total_retards=0;
    $eleves_absents=array ();
    foreach($col_creneaux as $creneau){        
        $absences_du_creneau =0;
        $nb_dp =0;
        $nb_int =0;
        $nb_ext =0;
        foreach($eleve_col as $eleve){
            $regime=$eleve->getEleveRegimeDoublant()->getRegime();
            $saisies_du_creneau=$eleve->getAbsenceEleveSaisiesManquementObligationPresenceDuCreneau($creneau, $dt_date_absence_eleve);
            $retard=false;
            foreach($saisies_du_creneau as $saisie){
                if ($saisie->getRetard()) {
                    $retard=true;
                    $nbre_total_retards++;
                    break;
                }
            }            
            if(!$retard && !$saisies_du_creneau->isEmpty()){
               $absences_du_creneau++; 
               switch($regime) {
                   case 'd/p':
                       $nb_dp++;
                       break;
                   case 'int.':
                       $nb_int++;
                       break;
                   case'ext.':
                       $nb_ext++; 
                }
                $eleves_absents[$eleve->getIdEleve()]=$eleve->getIdEleve();
            }           
        }        
        echo'<tr align="center">
            <td  style="border: 1px solid black; background-color: grey;">'.$creneau->getNomDefiniePeriode().'</td>
            <td>De '.$creneau->getHeureDebutDefiniePeriode().' à '.$creneau->getHeureFinDefiniePeriode().'</td>
            <td>'.$absences_du_creneau.'</td>
            <td>'.$nb_dp.'</td>
            <td>'.$nb_int.'</td>
            <td>'.$nb_ext.'</td>
           </tr>';
    }
    echo'</table>';    
    echo'<br />';
    echo'<table border="1" >';
    echo'<tr><td style="border: 1px solid black; background-color: grey;">Nombre d\'élèves présentant un manquement aux obligations de présences (hors retards) </td><td>'.count($eleves_absents).'</td></tr>';
    echo'<tr><td style="border: 1px solid black; background-color: grey;">Nombre de retards sur la journée</td><td>'.$nbre_total_retards.'</td></tr>';
    echo'</table>';
    ?>
   
</div>
<?php
$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dijit.form.Button");   
    dojo.require("dijit.form.Form");    
    dojo.require("dijit.form.DateTextBox");    
</script>';

require_once("../lib/footer.inc.php");
?>