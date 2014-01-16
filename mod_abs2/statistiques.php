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
// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
//mes fonctions
include("../edt_organisation/fonctions_calendrier.php");
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

if ($utilisateur->getStatut() != "cpe" && $utilisateur->getStatut() != "scolarite") {
    die("acces interdit");
}
// Initialisation des variables
$date_absence_eleve_debut = isset($_POST["date_absence_eleve_debut"]) ? $_POST["date_absence_eleve_debut"] : (isset($_GET["date_absence_eleve_debut"]) ? $_GET["date_absence_eleve_debut"] : (isset($_SESSION["date_absence_eleve_debut"]) ? $_SESSION["date_absence_eleve_debut"] : NULL));
$date_absence_eleve_fin = isset($_POST["date_absence_eleve_fin"]) ? $_POST["date_absence_eleve_fin"] : (isset($_GET["date_absence_eleve_fin"]) ? $_GET["date_absence_eleve_fin"] : (isset($_SESSION["date_absence_eleve_fin"]) ? $_SESSION["date_absence_eleve_fin"] : NULL));
$nom_eleve = isset($_POST["nom_eleve"]) ? $_POST["nom_eleve"] : (isset($_GET["nom_eleve"]) ? $_GET["nom_eleve"] : (isset($_SESSION["nom_eleve"]) ? $_SESSION["nom_eleve"] : NULL));
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : (isset($_GET["id_classe"]) ? $_GET["id_classe"] : (isset($_SESSION["id_classe_abs"]) ? $_SESSION["id_classe_abs"] : NULL));
$id_eleve = isset($_POST["id_eleve"]) ? $_POST["id_eleve"] : (isset($_GET["id_eleve"]) ? $_GET["id_eleve"] : NULL);
$affichage = isset($_POST["affichage"]) ? $_POST["affichage"] : (isset($_GET["affichage"]) ? $_GET["affichage"] : NULL);
$affichage_motifs = isset($_POST["affichage_motifs"]) ? $_POST["affichage_motifs"] : (isset($_GET["affichage_motifs"]) ? $_GET["affichage_motifs"] : NULL);

if (isset($date_absence_eleve_debut) && $date_absence_eleve_debut != null)
    $_SESSION['date_absence_eleve_debut'] = $date_absence_eleve_debut;
if (isset($date_absence_eleve_fin) && $date_absence_eleve_fin != null)
    $_SESSION['date_absence_eleve_fin'] = $date_absence_eleve_fin;

if ($date_absence_eleve_debut != null) {
    $dt_date_absence_eleve_debut = new DateTime(str_replace("/", ".", $date_absence_eleve_debut));
} else {
    $dt_date_absence_eleve_debut = new DateTime('now');
    $dt_date_absence_eleve_debut->setDate($dt_date_absence_eleve_debut->format('Y'), $dt_date_absence_eleve_debut->format('m'), $dt_date_absence_eleve_debut->format('d'));
}
if ($date_absence_eleve_fin != null) {
    $dt_date_absence_eleve_fin = new DateTime(str_replace("/", ".", $date_absence_eleve_fin));
} else {
    $dt_date_absence_eleve_fin = new DateTime('now');
}
$dt_date_absence_eleve_debut->setTime(0, 0, 0);
$dt_date_absence_eleve_fin->setTime(23, 59, 59);

//gestion de l'ordre des dates
$inverse_date = false;
if ($dt_date_absence_eleve_debut->format("U") > $dt_date_absence_eleve_fin->format("U")) {
    $date2 = clone $dt_date_absence_eleve_fin;
    $dt_date_absence_eleve_fin = $dt_date_absence_eleve_debut;
    $dt_date_absence_eleve_debut = $date2;
    $inverse_date = true;
}

$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
$utilisation_tablekit="ok";
$dojo = true;
$javascript_specifique[] = "mod_abs2/lib/include";
//
//**************** EN-TETE *****************
$titre_page = "Les absences";
if ($affichage != 'ods') {
    require_once("../lib/header.inc.php");
    include('menu_abs2.inc.php');
    include('menu_bilans.inc.php');
    ?>
         <div id="contain_div" class="css-panes">
        <?php
        if (ob_get_contents()) {
            ob_flush();
        }
        flush();
    }
//recupération des élèves   
    require_once("../orm/helpers/EdtHelper.php");
    $nbre_demi_journees = EdtHelper::getNbreDemiJourneesEtabOuvert($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin);

    $eleve_query = EleveQuery::create();
    if (getSettingValue("GepiAccesAbsTouteClasseCpe") == 'yes' && $utilisateur->getStatut() == "cpe") {
        
    } else {
        $eleve_query->filterByUtilisateurProfessionnel($utilisateur);
    }
    if ($id_classe !== null && $id_classe != -1) {
        $eleve_query->useJEleveClasseQuery()->filterByIdClasse($id_classe)->endUse();
    }
    if ($nom_eleve !== null && $nom_eleve != '') {
        $eleve_query->filterByNom('%' . $nom_eleve . '%');
    }
    if ($id_eleve !== null && $id_eleve != '') {
        $eleve_query->filterById($id_eleve);
    }
    $eleve_query->where('Eleve.DateSortie<?','0')
                ->orWhere('Eleve.DateSortie is NULL')
                ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve_debut->format('U'));
    $eleve_col = $eleve_query->orderByNom()->orderByPrenom()->distinct()->find();
    if ($eleve_col->isEmpty()) {
        echo"<h2 class='no'>Aucun élève avec les paramètres sélectionnés n'a été trouvé.</h2>";
        die();
    }
//recuperation des demi journéees d'absence
    if ($affichage_motifs) {
        $motifs_col = AbsenceEleveMotifQuery::create()->find();
        $nbre_motifs = $motifs_col->count();
    }
    if ($affichage != null && $affichage != '') {
        if ($affichage == 'html') {
            ?>        
            <div  style="width:300px"  id="chargement" >
            </div>
            <?php
        }
        $compteur = 0;
        $k = 0;
        $nombre_eleve_requete = $eleve_col->count();

	    $table_synchro_ok = AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable($dt_date_absence_eleve_debut,$dt_date_absence_eleve_fin);
	    if (!$table_synchro_ok) {//la table n'est pas synchronisée. On va vérifier individuellement les élèves qui se sont pas synchronisés
			$eleve_col = $eleve_query->find();
			if ($eleve_col->count()>150) {
				echo 'Il semble que vous demandez des statistiques sur trop d\'élèves et votre table de statistiques n\'est pas synchronisée. Veuillez faire une demande pour moins d\'élèves ou demander à votre administrateur de remplir la table d\'agrégation.';
				if (ob_get_contents()) {
					ob_flush();
				}
				flush();
			}
			foreach ($eleve_col as $eleve) {
				$eleve->checkAndUpdateSynchroAbsenceAgregationTable($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin);
			}
		}
        $nb_demijournees = 0;
        $nb_justifiees = 0;
        $nb_nonjustifiees = 0;
        $nb_retards = 0;
        $demi_journees_decompte=0;
        if($affichage_motifs){
            foreach ($motifs_col as $motif) {
                    $nom_variable = 'nb_demijourneesMotif' . $motif->getId();
                    $$nom_variable = 0;
                }  
        }
        foreach ($eleve_col as $eleve) {            
            $nbre_demi_journees_calcul=$nbre_demi_journees;
            if($eleve->getDateSortie('U')!=Null && $eleve->getDateSortie('U')>0 && $eleve->getDateSortie('U')<$dt_date_absence_eleve_fin->format('U')){
                $date_sortie=new DateTime('@'.$eleve->getDateSortie('U'));
                $nbre_demi_journees_calcul = EdtHelper::getNbreDemiJourneesEtabOuvert($dt_date_absence_eleve_debut, $date_sortie);
                $eleve->setVirtualColumn('NbreDemiJourneesCalcul',$nbre_demi_journees_calcul);
                
            }
            $demi_journees_decompte=$demi_journees_decompte+$nbre_demi_journees_calcul;
            if (($compteur % ceil($nombre_eleve_requete / 5) == 0) && ($id_classe == null || $id_classe == -1) && ($nom_eleve == null || $nom_eleve == '' ) && $affichage == 'html') {
                $pourcent = 20 * $k;
                echo '<script type="text/javascript">
                    dojo.xhrGet({
                        // The URL of the request
                        url: "include_chargement.php?compteur=' . $pourcent . '",   
                        load: function(newContent) {                        
                            dojo.byId("chargement").innerHTML = newContent;                            
                        },                       
                        error: function() {
                            // Do nothing -- keep old content there
                        }
                    });
                    </script>';
                if (ob_get_contents()) {
                    ob_flush();
                }
                flush();
                $k++;
            }
            $eleve->setVirtualColumn('DemiJourneesAbsencePreRempli', AbsenceAgregationDecompteQuery::create()
                            ->filterByEleve($eleve)
                            ->filterByDateIntervalle($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)
                            ->filterByManquementObligationPresence(true)
                            ->count());
            $nb_demijournees = $nb_demijournees + $eleve->getDemiJourneesAbsencePreRempli();
            $eleve->setVirtualColumn('TauxDemiJourneesAbsence',getTauxAbsenteisme($eleve->getDemiJourneesAbsencePreRempli(), $nbre_demi_journees_calcul));
    
            $eleve->setVirtualColumn('DemiJourneesNonJustifieesPreRempli', AbsenceAgregationDecompteQuery::create()
                            ->filterByEleve($eleve)
                            ->filterByDateIntervalle($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)
                            ->filterByManquementObligationPresence(true)
                            ->filterByNonJustifiee(true)
                            ->count());
            $nb_nonjustifiees = $nb_nonjustifiees + $eleve->getDemiJourneesNonJustifieesPreRempli();
            $nb_justifiees = $nb_justifiees + $eleve->getDemiJourneesAbsencePreRempli() - $eleve->getDemiJourneesNonJustifieesPreRempli();
            $eleve->setVirtualColumn('TauxDemiJourneesNonJustifiees',getTauxAbsenteisme($eleve->getDemiJourneesNonJustifieesPreRempli(), $nbre_demi_journees_calcul));
            $eleve->setVirtualColumn('TauxDemiJourneesJustifiees',getTauxAbsenteisme(($eleve->getDemiJourneesAbsencePreRempli()-$eleve->getDemiJourneesNonJustifieesPreRempli()), $nbre_demi_journees_calcul));            
            if ($affichage_motifs) { 
                foreach ($motifs_col as $motif) {
                    $total_motif_eleve=AbsenceAgregationDecompteQuery::create()
                                    ->filterByEleve($eleve)
                                    ->filterByMotifsAbsence($motif->getId())
                                    ->filterByDateIntervalle($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)
                                    ->filterByManquementObligationPresence(true)
                                    ->count();
                    $eleve->setVirtualColumn('DemiJourneesAbsencePreRempliMotif' . $motif->getId(), $total_motif_eleve);
                    $eleve->setVirtualColumn('TauxDemiJourneesAbsenceMotif' . $motif->getId(), getTauxAbsenteisme($total_motif_eleve, $nbre_demi_journees_calcul));
                    $nom_variable = 'nb_demijourneesMotif' . $motif->getId();
                    $$nom_variable = $$nom_variable + $total_motif_eleve;
                }
            }
            $compteur++;
        }
    }
    if ($affichage != 'ods') {
        ?>
    <?php if ($inverse_date) : ?>
            <h3 class="no">Les dates de début et de fin ont été inversés.</h3>
    <?php endif; ?>
        <p>
        La formule utilisée pour le calcul du taux d'absentéisme est le pourcentage du nombre de demi-journées d'absences par rapport au nombre de demi-journées ouvrées.<br />
        Pour les élèves ayant quitté l'établissement durant la période choisie, le nombre de demi-journées ouvrées utilisé pour le calcul est indiqué entre parenthèses.<br /><br />                  
        </p>
        <form dojoType="dijit.form.Form" name="abs_statistiques" id="abs_statistiques" action="statistiques.php" method="post">
            <fieldset>
                <legend>Paramétrage de l'export (dates, classes...) et affichage</legend>  
                <h2>Taux d'absentéisme  
                    du  
                    <input style="width : 8em;font-size:14px;" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve_debut" name="date_absence_eleve_debut" value="<?php echo $dt_date_absence_eleve_debut->format('Y-m-d') ?>" />
                    au               
                    <input style="width : 8em;font-size:14px;" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve_fin" name="date_absence_eleve_fin" value="<?php echo $dt_date_absence_eleve_fin->format('Y-m-d') ?>" />
                    soit <?php echo $nbre_demi_journees;?> demi-journées ouvrées.
                </h2>                 
                <?php
                if ($id_eleve !== null && $id_eleve != '') {
                    $eleve = EleveQuery::create()->filterById($id_eleve)->findOne();
                    $nom_eleve = $eleve->getNom();
                    $id_classe = $eleve->getClasse()->getId();
                }
                ?>
                Nom (facultatif) : <input dojoType="dijit.form.TextBox" type="text" style="width : 10em" name="nom_eleve" size="10" value="<?php echo $nom_eleve ?>" onChange="document.bilan_individuel.id_eleve.value='';"/>
                <input type="hidden" name="id_eleve" value="<?php echo $id_eleve ?>"/>
                <input type="hidden" name="affichage" value="<?php echo $affichage ?>"/>


                <?php
                //on affiche une boite de selection avec les classe
                if ((getSettingValue("GepiAccesAbsTouteClasseCpe") == 'yes' && $utilisateur->getStatut() == "cpe") || $utilisateur->getStatut() == "autre") {
                    $classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
                } else {
                    $classe_col = $utilisateur->getClasses();
                }
                if (!$classe_col->isEmpty()) {
                    if (isset($_SESSION['classes_bilan']))
                        unset($_SESSION['classes_bilan']);
                    echo ("Classe : <select dojoType=\"dijit.form.Select\" style=\"width :12em;font-size:12px;\" name=\"id_classe\" onChange='document.abs_statistiques.id_eleve.value=\"\";'>");
                    if ($utilisateur->getStatut() != "autre") {
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
                <br />    
                <input type="checkbox" dojoType="dijit.form.CheckBox" name="affichage_motifs" value="ok" <?php
            if ($affichage_motifs) {
                echo'checked';
            }
            ?> > Afficher le détail par motif (Traitement plus long sur toutes les classes). 
                <br />
                <button type="submit"  style="font-size:12px" dojoType="dijit.form.Button" name="affichage" value="html">Valider les modifications et afficher à l'écran</button>   
                <button type="submit" style="font-size:12px" dojoType="dijit.form.Button" name="affichage" value="ods" >Enregistrer au format ods</button>     
            </fieldset>
            <br />       
            <?php
        }
        if ($affichage == 'html') {
            echo 'Total élèves : ' . $eleve_col->count();
            echo '<table class="boireaus boireaus_alt sortable resizable"  border="1" cellspacing="0">';
            echo '<thead>';
            echo '<tr style="text-align:center;">';

            echo '<th colspan="2" >';
            echo 'Informations sur l\'élève';
            echo '</th>';

            echo '<th colspan="3" >';
            echo 'Taux d\'absentéisme(%)';
            echo '</th>';

            if ($affichage_motifs) {
                echo '<th colspan="' . $nbre_motifs . '" >';
                echo 'Taux d\'absentéisme total par motifs(%)';
                echo '</th>';
            }          

            echo '</tr>';

            echo '<tr>';

            echo '<th  class="text" title="cliquez pour trier sur la colonne">';
            echo 'Nom Prénom';
            echo '</th>';

            echo '<th class="text" title="cliquez pour trier sur la colonne">';
            echo 'Classe';
            echo '</th>';

            echo '<th class="number" title="cliquez pour trier sur la colonne">';
            echo 'total';
            echo '</th>';

            echo '<th class="number" title="cliquez pour trier sur la colonne">';
            echo 'non justifié';
            echo '</th>';

            echo '<th class="number" title="cliquez pour trier sur la colonne">';
            echo 'justifié';
            echo '</th>';

            if ($affichage_motifs) {
                foreach ($motifs_col as $motif) {
                    echo '<th class="number" title="cliquez pour trier sur la colonne">';
                    echo $motif->getNom();
                    echo '</th>';
                }
            }
            echo '</tr>';          
            echo '</thead>';
            echo '<tbody>';
            
            foreach ($eleve_col as $eleve) {                
                echo '<tr>';

                echo '<td>';
                echo $eleve->getNom() . ' ' . $eleve->getPrenom();
                if($eleve->hasVirtualColumn('NbreDemiJourneesCalcul')){
                    echo '<br ><strong>(calcul sur '.$eleve->getNbreDemiJourneesCalcul().' demi-journées)</strong>';
                }
                echo '</td>';

                echo '<td>';
                echo $eleve->getClasseNom();
                echo '</td>';

                echo '<td>';
                echo str_replace(",",".",$eleve->getTauxDemiJourneesAbsence());              
                echo '</td>';


                echo '<td>';
                echo str_replace(",",".",$eleve->getTauxDemiJourneesNonJustifiees());                
                echo '</td>';

                echo '<td>';
                echo str_replace(",",".",$eleve->getTauxDemiJourneesJustifiees());                
                echo '</td>';

                if ($affichage_motifs) {
                    foreach ($motifs_col as $motif) {
                        echo '<td>';
                        $nom_colonne = 'getTauxDemiJourneesAbsenceMotif' . $motif->getId();
                        echo str_replace(",",".",$eleve->$nom_colonne());                        
                        echo '</td>';
                    }
                }              

                echo '</tr>';
            }
            echo '</tbody>';
            echo '<tfoot>';
            echo '<tr>';

            echo '<th>';
            echo 'Nombre d\'élèves : ';
            echo $eleve_col->count();
            echo '</th>';

            echo '<th>';
            echo 'Taux moyen  ';
            echo '</th>';

            echo '<th>';
            echo str_replace(",",".",getTauxAbsenteisme($nb_demijournees, $demi_journees_decompte));
            echo '</th>';

            echo '<th>';
            echo str_replace(",",".",getTauxAbsenteisme($nb_nonjustifiees, $demi_journees_decompte));
            echo '</th>';

            echo '<th>';
            echo str_replace(",",".",getTauxAbsenteisme($nb_justifiees, $demi_journees_decompte));
            echo '</th>';

            if ($affichage_motifs) {
                foreach ($motifs_col as $motif) {
                    echo '<th>';
                    $test = 'nb_demijourneesMotif' . $motif->getId();
                    echo str_replace(",",".",getTauxAbsenteisme($$test, $demi_journees_decompte));
                    echo '</th>';
                }
            }            
            echo '</tr>';
            echo '</tfoot>';
            echo '<h5>Extraction faite le ' . date("d/m/Y - h:i") . '</h5>';
        } else if ($affichage == 'ods') {
            include_once 'lib/function.php';
            // load the TinyButStrong libraries
            include_once('../tbs/tbs_class.php'); // TinyButStrong template engine            
            //include_once('../tbs/plugins/tbsdb_php.php');
            $TBS = new clsTinyButStrong; // new instance of TBS
            include_once('../tbs/plugins/tbs_plugin_opentbs.php');
            $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin
            // Load the template
            $extraction_taux_absenteisme = repertoire_modeles('absence_taux_absenteisme.ods');
            $TBS->LoadTemplate($extraction_taux_absenteisme, OPENTBS_ALREADY_UTF8);

            $titre = 'Extrait du Taux d\'absentéisme d\'absences du ' . $dt_date_absence_eleve_debut->format('d/m/Y') . ' au ' . $dt_date_absence_eleve_fin->format('d/m/Y');
            $classe = null;
            if ($id_classe != null && $id_classe != '') {
                $classe = ClasseQuery::create()->findOneById($id_classe);
                if ($classe != null) {
                    $titre .= ' pour la classe ' . $classe->getNom();
                }
            }
            if ($nom_eleve != null && $nom_eleve != '') {
                $titre .= ' pour les élèves dont le nom ou le prénom contient ' . $nom_eleve;
            }
            $TBS->MergeField('titre', $titre);
            $TBS->MergeField('dj_ouvrees', $nbre_demi_journees);

            if ($affichage_motifs) {
                $nbre_colonnes = 3 + $motifs_col->count();
            } else {
                $nbre_colonnes = 3;
            }
            //colonnes toujours présentes
            $colonnes_individu = array();
            $colonnes_individu[1] = 'nom';
            $colonnes_individu[2] = 'prenom';
            $colonnes_individu[3] = 'classe';
            $colonnes_donnes = Array();
            $colonnes_donnes[1] = 'taux_1';
            $colonnes_donnes[2] = 'taux_2';
            $colonnes_donnes[3] = 'taux_3';

            $libelle = Array();
            $libelle[] = 'Total';
            $libelle[] = 'Non justifié';
            $libelle[] = 'Justifié';
            //partie si l'affichage par motif est coché
            if ($affichage_motifs) {
                $j = 4;
                foreach ($motifs_col as $motif) {                    
                    $libelle[] = $motif->getNom();
                    $colonnes_donnes[] = 'taux_' . $j;
                    $j++;
                }
            }

            // Remplissage du tableau de données individuelles
            $donnees_indiv = array();
            foreach ($eleve_col as $eleve) {               
                $enregistrements = array();                
                if($eleve->hasVirtualColumn('NbreDemiJourneesCalcul')){
                    $enregistrements[$colonnes_individu[1]] = $eleve->getNom().' ('.$eleve->getNbreDemiJourneesCalcul().' demi-journées)';
                }else{
                    $enregistrements[$colonnes_individu[1]] = $eleve->getNom();
                }
                $enregistrements[$colonnes_individu[2]] = $eleve->getPrenom();
                $enregistrements[$colonnes_individu[3]] = $eleve->getClasseNom();
                $enregistrements[$colonnes_donnes[1]] = str_replace(",",".",$eleve->getTauxDemiJourneesAbsence());
                $enregistrements[$colonnes_donnes[2]] = str_replace(",",".",$eleve->getTauxDemiJourneesNonJustifiees());
                $enregistrements[$colonnes_donnes[3]] = str_replace(",",".",$eleve->getTauxDemiJourneesJustifiees());
                if ($affichage_motifs) {
                    $indice = 4;
                    foreach ($motifs_col as $motif) {
                        $nom_colonne = 'getTauxDemiJourneesAbsenceMotif' . $motif->getId();
                        $enregistrements[$colonnes_donnes[$indice]] = str_replace(",",".",$eleve->$nom_colonne());
                        $indice++;                        
                    }
                }
                $donnees_indiv[$eleve->getId()] = $enregistrements;                
            }
            //remplissage du tableau de donnes moyennes
            $enregistrements_moy = Array();
            $enregistrements_moy[$colonnes_donnes[1]] = str_replace(",",".",getTauxAbsenteisme($nb_demijournees, $demi_journees_decompte));
            $enregistrements_moy[$colonnes_donnes[2]] = str_replace(",",".",getTauxAbsenteisme($nb_nonjustifiees, $demi_journees_decompte));
            $enregistrements_moy[$colonnes_donnes[3]] = str_replace(",",".",getTauxAbsenteisme($nb_justifiees, $demi_journees_decompte));
            if ($affichage_motifs) {
                $indice = 4;
                foreach ($motifs_col as $motif) {
                    $test = 'nb_demijourneesMotif' . $motif->getId();
                    $enregistrements_moy[$colonnes_donnes[$indice]] = str_replace(",",".",getTauxAbsenteisme($$test, $demi_journees_decompte));
                    $indice++;
                }
            }
            $donnees_moy = Array();
            $donnees_moy[] = $enregistrements_moy;

            //colonnes dynamiques (nbre=$nbre_colonnes)
            $TBS->MergeBlock('c1,c2', 'num', $nbre_colonnes);
            $TBS->MergeBlock('a,b', $libelle);
            //données individuelles et moyennes
            $TBS->MergeBlock('a2', $donnees_moy);
            $TBS->MergeBlock('b2', $donnees_indiv);
            $TBS->MergeField('eleve_count', $eleve_col->count());

            // Output as a download file (some automatic fields are merged here)
            $nom_fichier = 'extrait_taux_absenteisme_';
            if ($classe != null) {
                $nom_fichier .= $classe->getNom() . '_';
            }
            $nom_fichier .= $dt_date_absence_eleve_fin->format("d_m_Y") . '.ods';
            $TBS->Show(OPENTBS_DOWNLOAD + TBS_EXIT, $nom_fichier);
        }
        ?>

</div>
<?php
$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dojo.parser");
    dojo.require("dijit.form.Button");    
    dojo.require("dijit.form.Form");
    dojo.require("dijit.form.CheckBox");
    dojo.require("dijit.form.DateTextBox");    
    dojo.require("dijit.form.Select");  
    dojo.addOnLoad(function() {
     dojo.byId(\'chargement\').hide();
     });
    </script>';
require_once("../lib/footer.inc.php");

function getTauxAbsenteisme($nbreDemiJourneesAbsences, $nbreTotalDemiJournees) {
    if ($nbreTotalDemiJournees == 0) { //l'établissement est fermé sur toute la période
        return 0;
    } else {
        return(round(100 * ($nbreDemiJourneesAbsences / $nbreTotalDemiJournees) , 2));
    }    
}
?>
