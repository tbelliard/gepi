<?php
/**
 *
 *
 * Copyright 2010-2016 Josselin Jacquard, Stephane Boireau
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

$affichage_final = isset($_POST["affichage_final"]) ? $_POST["affichage_final"] : (isset($_GET["affichage_final"]) ? $_GET["affichage_final"] : "n");

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
//if ($affichage != 'ods') {
if ($affichage_final=='n') {
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
        //if ($affichage == 'html') {
        if ($affichage_final=='n') {
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
				echo 'Il semble que vous demandez des statistiques sur trop d\'élèves et votre table de statistiques n\'est pas synchronisée.<br />Veuillez faire une demande pour moins d\'élèves ou demander à votre administrateur de remplir la table d\'agrégation.';

				if(($_SESSION['statut']=="cpe")&&(getSettingAOui('AccesCpeAgregationAbs2'))) {
					echo "<br />Le droit de <a href='$gepiPath/mod_abs2/admin/admin_table_agregation.php'>vider/remplir la table d'Agrégation des Absences</a> vous a été donné.<br />Notez que l'opération est lourde en ressources.<br />Il vaut mieux lancer cela le soir ou sur le temps du midi pour ne pas ralentir les connexions/usages.";
				}

				if (ob_get_contents()) {
					ob_flush();
				}
				flush();
			}
			foreach ($eleve_col as $eleve) {
				$eleve->checkAndUpdateSynchroAbsenceAgregationTable($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin);
			}
		}


		$sql="CREATE TABLE IF NOT EXISTS temp_abs_extract (
		id int(11) unsigned NOT NULL auto_increment, 
		login VARCHAR(50) NOT NULL DEFAULT '', 
		date_extract DATETIME NOT NULL default '0000-00-00 00:00:00', 
		login_ele VARCHAR(50) NOT NULL DEFAULT '', 
		item VARCHAR(100) NOT NULL DEFAULT '', 
		valeur VARCHAR(255) NULL DEFAULT '', 
		PRIMARY KEY id (id));";
		$create_table=mysqli_query($GLOBALS['mysqli'], $sql);

		// Menage
		$sql="DELETE FROM temp_abs_extract WHERE date_extract<'".strftime("%Y-%m-%d %H:%M:%S", time()-24*3600)."';";
		$del=mysqli_query($GLOBALS['mysqli'], $sql);

		$ts_extract=isset($_POST['ts_extract']) ? $_POST['ts_extract'] : strftime("%Y-%m-%d %H:%M:%S");

		$sql="SELECT DISTINCT login_ele FROM temp_abs_extract WHERE date_extract='".$ts_extract."' AND login='".$_SESSION['login']."';";
		$res_deja=mysqli_query($GLOBALS['mysqli'], $sql);
		$tab_deja=array();
		while($lig_deja=mysqli_fetch_object($res_deja)) {
			$tab_deja[]=$lig_deja->login_ele;
		}

		//$old_way="y";
		$cpt_ele_restants=0;

		$nb_demijournees = 0;
		$nb_justifiees = 0;
		$nb_nonjustifiees = 0;
		$nb_retards = 0;
		$demi_journees_decompte=0;
		if($affichage_motifs) {
			foreach ($motifs_col as $motif) {
				$nom_variable = 'nb_demijourneesMotif' . $motif->getId();
				$$nom_variable = 0;
			}
		}
		foreach ($eleve_col as $eleve) {
			if($compteur>0) {
				// On a fait le traitement pour un élève; on compte le nombre d'élèves restants
				$cpt_ele_restants++;
			}
			elseif(!in_array($eleve->getLogin(), $tab_deja)) {
				$nom=$eleve->getNom();
				$prenom=$eleve->getPrenom();
				$nom_prenom=$nom.' '.$prenom;
				//if($affichage == 'html') {
				if ($affichage_final=='n') {
					echo "<p id='p_eleve_en_cours_d_extraction'>Extraction des données pour ".$nom_prenom."...</p>";
					if (ob_get_contents()) {
						ob_flush();
					}
					flush();
				}
				$sql="INSERT INTO temp_abs_extract SET login='".$_SESSION['login']."',
						date_extract='".$ts_extract."',
						login_ele='".$eleve->getLogin()."',
						item='id_eleve',
						valeur='".$eleve->getId()."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);

				$sql="INSERT INTO temp_abs_extract SET login='".$_SESSION['login']."',
						date_extract='".$ts_extract."',
						login_ele='".$eleve->getLogin()."',
						item='nom_prenom',
						valeur='".mysqli_real_escape_string($GLOBALS['mysqli'], $nom_prenom)."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);

				$sql="INSERT INTO temp_abs_extract SET login='".$_SESSION['login']."',
						date_extract='".$ts_extract."',
						login_ele='".$eleve->getLogin()."',
						item='nom',
						valeur='".mysqli_real_escape_string($GLOBALS['mysqli'], $nom)."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);

				$sql="INSERT INTO temp_abs_extract SET login='".$_SESSION['login']."',
						date_extract='".$ts_extract."',
						login_ele='".$eleve->getLogin()."',
						item='prenom',
						valeur='".mysqli_real_escape_string($GLOBALS['mysqli'], $prenom)."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);

				$nbre_demi_journees_calcul=$nbre_demi_journees;
				if($eleve->getDateSortie('U')!=Null && $eleve->getDateSortie('U')>0 && $eleve->getDateSortie('U')<$dt_date_absence_eleve_fin->format('U')){
					$date_sortie=new DateTime('@'.$eleve->getDateSortie('U'));
					$nbre_demi_journees_calcul = EdtHelper::getNbreDemiJourneesEtabOuvert($dt_date_absence_eleve_debut, $date_sortie);
					$eleve->setVirtualColumn('NbreDemiJourneesCalcul',$nbre_demi_journees_calcul);

					$sql="INSERT INTO temp_abs_extract SET login='".$_SESSION['login']."',
							date_extract='".$ts_extract."',
							login_ele='".$eleve->getLogin()."',
							item='NbreDemiJourneesCalcul_reduit',
							valeur='".$nbre_demi_journees_calcul."';";
					$insert=mysqli_query($GLOBALS['mysqli'], $sql);
				}
				$sql="INSERT INTO temp_abs_extract SET login='".$_SESSION['login']."',
						date_extract='".$ts_extract."',
						login_ele='".$eleve->getLogin()."',
						item='NbreDemiJourneesCalcul',
						valeur='".$nbre_demi_journees_calcul."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);


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
				$tmp_taux_absenteisme=getTauxAbsenteisme($eleve->getDemiJourneesAbsencePreRempli(), $nbre_demi_journees_calcul);
				$eleve->setVirtualColumn('TauxDemiJourneesAbsence', $tmp_taux_absenteisme);

				$sql="INSERT INTO temp_abs_extract SET login='".$_SESSION['login']."',
						date_extract='".$ts_extract."',
						login_ele='".$eleve->getLogin()."',
						item='DemiJourneesAbsence',
						valeur='".$eleve->getDemiJourneesAbsencePreRempli()."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);

				$sql="INSERT INTO temp_abs_extract SET login='".$_SESSION['login']."',
						date_extract='".$ts_extract."',
						login_ele='".$eleve->getLogin()."',
						item='TauxDemiJourneesAbsence',
						valeur='".$tmp_taux_absenteisme."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);



				$eleve->setVirtualColumn('DemiJourneesNonJustifieesPreRempli', AbsenceAgregationDecompteQuery::create()
					->filterByEleve($eleve)
					->filterByDateIntervalle($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)
					->filterByManquementObligationPresence(true)
					->filterByNonJustifiee(true)
					->count());
				//$nb_nonjustifiees = $nb_nonjustifiees + $eleve->getDemiJourneesNonJustifieesPreRempli();
				$current_justifiees=$eleve->getDemiJourneesAbsencePreRempli() - $eleve->getDemiJourneesNonJustifieesPreRempli();
				//$nb_justifiees = $nb_justifiees + $current_justifiees;

				$sql="INSERT INTO temp_abs_extract SET login='".$_SESSION['login']."',
						date_extract='".$ts_extract."',
						login_ele='".$eleve->getLogin()."',
						item='DemiJourneesNonJustifiees',
						valeur='".$eleve->getDemiJourneesNonJustifieesPreRempli()."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);

				$sql="INSERT INTO temp_abs_extract SET login='".$_SESSION['login']."',
						date_extract='".$ts_extract."',
						login_ele='".$eleve->getLogin()."',
						item='DemiJourneesJustifiees',
						valeur='".$current_justifiees."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);

				$tmp_taux_absenteisme=getTauxAbsenteisme($eleve->getDemiJourneesNonJustifieesPreRempli(), $nbre_demi_journees_calcul);
				$eleve->setVirtualColumn('TauxDemiJourneesNonJustifiees', $tmp_taux_absenteisme);

				$sql="INSERT INTO temp_abs_extract SET login='".$_SESSION['login']."',
						date_extract='".$ts_extract."',
						login_ele='".$eleve->getLogin()."',
						item='TauxDemiJourneesNonJustifiees',
						valeur='".$tmp_taux_absenteisme."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);


				$tmp_taux_absenteisme=getTauxAbsenteisme(($eleve->getDemiJourneesAbsencePreRempli()-$eleve->getDemiJourneesNonJustifieesPreRempli()), $nbre_demi_journees_calcul);
				$eleve->setVirtualColumn('TauxDemiJourneesJustifiees', $tmp_taux_absenteisme);

				$sql="INSERT INTO temp_abs_extract SET login='".$_SESSION['login']."',
						date_extract='".$ts_extract."',
						login_ele='".$eleve->getLogin()."',
						item='TauxDemiJourneesJustifiees',
						valeur='".$tmp_taux_absenteisme."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);

				if ($affichage_motifs) { 
					foreach ($motifs_col as $motif) {
						$total_motif_eleve=AbsenceAgregationDecompteQuery::create()
							->filterByEleve($eleve)
							->filterByMotifsAbsence($motif->getId())
							->filterByDateIntervalle($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)
							->filterByManquementObligationPresence(true)
							->count();
						$eleve->setVirtualColumn('DemiJourneesAbsencePreRempliMotif' . $motif->getId(), $total_motif_eleve);

						$sql="INSERT INTO temp_abs_extract SET login='".$_SESSION['login']."',
								date_extract='".$ts_extract."',
								login_ele='".$eleve->getLogin()."',
								item='DemiJourneesAbsenceMotif".$motif->getId()."',
								valeur='".$total_motif_eleve."';";
						$insert=mysqli_query($GLOBALS['mysqli'], $sql);

						$tmp_taux_absenteisme=getTauxAbsenteisme($total_motif_eleve, $nbre_demi_journees_calcul);
						$eleve->setVirtualColumn('TauxDemiJourneesAbsenceMotif' . $motif->getId(), $tmp_taux_absenteisme);

						$sql="INSERT INTO temp_abs_extract SET login='".$_SESSION['login']."',
								date_extract='".$ts_extract."',
								login_ele='".$eleve->getLogin()."',
								item='TauxDemiJourneesAbsenceMotif".$motif->getId()."',
								valeur='".$tmp_taux_absenteisme."';";
						$insert=mysqli_query($GLOBALS['mysqli'], $sql);

						$nom_variable = 'nb_demijourneesMotif' . $motif->getId();
						$$nom_variable = $$nom_variable + $total_motif_eleve;
					}
				}
	$compteur++;
			}
		}


		if($cpt_ele_restants>0) {
			// On va soumettre le formulaire
			echo "<p>Il reste $cpt_ele_restants élève(s) à extraire.</p>
<form action='".$_SERVER['PHP_SELF']."' method='post' id='form_suite'>
	<input type='hidden' name='ts_extract' value='".$ts_extract."' />
	<input type='hidden' name='date_absence_eleve_debut' value='".$date_absence_eleve_debut."' />
	<input type='hidden' name='date_absence_eleve_fin' value='".$date_absence_eleve_fin."' />
	<input type='hidden' name='nom_eleve' value='".$nom_eleve."' />
	<input type='hidden' name='id_classe' value='".$id_classe."' />
	<input type='hidden' name='id_eleve' value='".$id_eleve."' />
	<input type='hidden' name='affichage' value='".$affichage."' />";
			if(isset($affichage_motifs)) {
				echo "
	<input type='hidden' name='affichage_motifs' value='".$affichage_motifs."' />";
			}
			echo "
	<input type='submit' id='bouton_poursuivre' value='Poursuivre' />
</form>
<script type='text/javascript'>
	document.getElementById('bouton_poursuivre').style.display='none';
	function soumettre_form_suite() {
		document.getElementById('form_suite').submit();
	}
	setTimeout('soumettre_form_suite()', 500);
</script>";

			die();
		}
		elseif(($affichage=="ods")&&($affichage_final=="n")) {
			// On va soumettre le formulaire vers un nouvel onglet
			echo "<p id='generation_ods'>Le fichier ODS va être généré.</p>
<form action='".$_SERVER['PHP_SELF']."' method='post' id='form_suite' target='_blank'>
	<input type='hidden' name='ts_extract' value='".$ts_extract."' />
	<input type='hidden' name='date_absence_eleve_debut' value='".$date_absence_eleve_debut."' />
	<input type='hidden' name='date_absence_eleve_fin' value='".$date_absence_eleve_fin."' />
	<input type='hidden' name='nom_eleve' value='".$nom_eleve."' />
	<input type='hidden' name='id_classe' value='".$id_classe."' />
	<input type='hidden' name='id_eleve' value='".$id_eleve."' />
	<input type='hidden' name='affichage' value='".$affichage."' />
	<input type='hidden' name='affichage_final' value='y' />";
			if(isset($affichage_motifs)) {
				echo "
	<input type='hidden' name='affichage_motifs' value='".$affichage_motifs."' />";
			}
			echo "
	<input type='submit' id='bouton_poursuivre' value='Poursuivre' />
</form>
<script type='text/javascript'>
	document.getElementById('bouton_poursuivre').style.display='none';
	function soumettre_form_suite() {
		document.getElementById('form_suite').submit();
	}
	setTimeout('soumettre_form_suite()', 500);

	function masquer_message_generation_ods() {
		document.getElementById('generation_ods').style.display='none';
	}
	setTimeout('masquer_message_generation_ods()', 2000);

</script>";

			//die();
			// Pour ne pas tenter de générer une deuxième fois l'ODS dans la page web qu'on a commencer à générer.
			$affichage="html";
		}
	}

    //if ($affichage != 'ods') {
    if ($affichage_final=='n') {
		echo "<script type='text/javascript'>
if(document.getElementById('p_eleve_en_cours_d_extraction')) {
	document.getElementById('p_eleve_en_cours_d_extraction').style.display='none';
}
</script>";

        ?>
    <?php if ($inverse_date) : ?>
            <h3 class="no">Les dates de début et de fin ont été inversées.</h3>
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
            </form>
            <br />       
            <?php
        }

        if (($affichage == 'html')||($affichage == 'ods')) {
		$sql="SELECT sum(valeur) AS total FROM temp_abs_extract WHERE item='NbreDemiJourneesCalcul' AND date_extract='".$ts_extract."' AND login='".$_SESSION['login']."';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($res)==0) {
			$demi_journees_decompte=0;
		}
		else {
			$lig=mysqli_fetch_object($res);
			$demi_journees_decompte=$lig->total;
		}
		//echo "\$demi_journees_decompte=$demi_journees_decompte<br />";

		$sql="SELECT sum(valeur) AS total FROM temp_abs_extract WHERE item='DemiJourneesAbsence' AND date_extract='".$ts_extract."' AND login='".$_SESSION['login']."';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($res)==0) {
			$nb_demijournees=0;
		}
		else {
			$lig=mysqli_fetch_object($res);
			$nb_demijournees=str_replace(",",".",getTauxAbsenteisme($lig->total, $demi_journees_decompte));
		}

		$sql="SELECT sum(valeur) AS total FROM temp_abs_extract WHERE item='DemiJourneesNonJustifiees' AND date_extract='".$ts_extract."' AND login='".$_SESSION['login']."';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($res)==0) {
			$nb_nonjustifiees=0;
		}
		else {
			$lig=mysqli_fetch_object($res);
			$nb_nonjustifiees=str_replace(",",".",getTauxAbsenteisme($lig->total, $demi_journees_decompte));
		}

		$sql="SELECT sum(valeur) AS total FROM temp_abs_extract WHERE item='DemiJourneesJustifiees' AND date_extract='".$ts_extract."' AND login='".$_SESSION['login']."';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($res)==0) {
			$nb_justifiees=0;
		}
		else {
			$lig=mysqli_fetch_object($res);
			$nb_justifiees=str_replace(",",".",getTauxAbsenteisme($lig->total, $demi_journees_decompte));
		}

		if ($affichage_motifs) {
			foreach ($motifs_col as $motif) {
				$sql="SELECT sum(valeur) AS total FROM temp_abs_extract WHERE item='DemiJourneesAbsenceMotif".$motif->getId()."' AND date_extract='".$ts_extract."' AND login='".$_SESSION['login']."';";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS['mysqli'], $sql);
				if(mysqli_num_rows($res)==0) {
					$total_motif[$motif->getId()]=0;
				}
				else {
					$lig=mysqli_fetch_object($res);
					$total_motif[$motif->getId()]=str_replace(",",".",getTauxAbsenteisme($lig->total, $demi_journees_decompte));
				}
			}
		}

		$sql="SELECT * FROM temp_abs_extract WHERE date_extract='".$ts_extract."' AND login='".$_SESSION['login']."';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS['mysqli'], $sql);
		$tab_ele=array();
		while($lig=mysqli_fetch_object($res)) {
			$tab_ele[$lig->login_ele][$lig->item]=$lig->valeur;
		}
		/*
		echo "<pre>";
		print_r($tab_ele);
		echo "</pre>";
		*/

        }

        if ($affichage == 'html') {

		echo "<div style='float:right; width:16px'>
<form action='".$_SERVER['PHP_SELF']."' method='post' id='form_suite2' target='_blank'>
	<input type='hidden' name='ts_extract' value='".$ts_extract."' />
	<input type='hidden' name='date_absence_eleve_debut' value='".$date_absence_eleve_debut."' />
	<input type='hidden' name='date_absence_eleve_fin' value='".$date_absence_eleve_fin."' />
	<input type='hidden' name='nom_eleve' value='".$nom_eleve."' />
	<input type='hidden' name='id_classe' value='".$id_classe."' />
	<input type='hidden' name='id_eleve' value='".$id_eleve."' />
	<input type='hidden' name='affichage' value='ods' />
	<input type='hidden' name='affichage_final' value='y' />";
		if(isset($affichage_motifs)) {
			echo "
<input type='hidden' name='affichage_motifs' value='".$affichage_motifs."' />";
		}
		echo "
	<!--input type='submit' id='bouton_poursuivre2' value='ODS' /-->
</form>
<a href=\"#\" onclick=\"document.getElementById('form_suite2').submit();return false;\" title=\"Générer un export tableur ODS\"><img src='../images/icons/ods.png' class='icone16' alt='ODS' /></a></div>";

            echo '<p>Total élèves : ' . $eleve_col->count()."</p>";

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

            foreach ($tab_ele as $login_eleve => $tab_ele_courant) {
                echo '<tr>';

                echo '<td>';
                echo $tab_ele_courant['nom_prenom'];
                if(isset($tab_ele_courant['NbreDemiJourneesCalcul_reduit'])) {
                    echo '<br ><strong>(calcul sur '.$tab_ele_courant['NbreDemiJourneesCalcul_reduit'].' demi-journées)</strong>';
                }
                echo '</td>';

                echo '<td>';
                echo get_liste_classes_eleve($login_eleve);
                echo '</td>';

                echo '<td>';
                if(isset($tab_ele_courant['TauxDemiJourneesAbsence'])) {
                    echo str_replace(",",".",$tab_ele_courant['TauxDemiJourneesAbsence']);
                }
                echo '</td>';


                echo '<td>';
                if(isset($tab_ele_courant['TauxDemiJourneesNonJustifiees'])) {
                    echo str_replace(",",".",$tab_ele_courant['TauxDemiJourneesNonJustifiees']);
                }
                echo '</td>';

                echo '<td>';
                if(isset($tab_ele_courant['TauxDemiJourneesJustifiees'])) {
                    echo str_replace(",",".",$tab_ele_courant['TauxDemiJourneesJustifiees']);
                }
                echo '</td>';

                if ($affichage_motifs) {
                    foreach ($motifs_col as $motif) {
                        echo '<td>';
                        if(isset($tab_ele_courant['TauxDemiJourneesAbsenceMotif'.$motif->getId()])) {
                            echo str_replace(",",".",$tab_ele_courant['TauxDemiJourneesAbsenceMotif'.$motif->getId()]);
                        }
                        echo '</td>';
                    }
                }

                echo '</tr>';
            }
            /*
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
		*/
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
				echo str_replace(",",".",getTauxAbsenteisme($total_motif[$motif->getId()], $demi_journees_decompte));
                    echo '</th>';
                }
            }
            echo '</tr>';
            echo '</tfoot>';
            echo "</table>";
            echo '<h5>Extraction faite le ' . date("d/m/Y - H:i") . '</h5>';

		$ts_courant=time();
		echo "<h6>Durée de l'extraction&nbsp;: ".($ts_courant-mysql_date_to_unix_timestamp($ts_extract))."s</h6>";


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
/*
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
*/

            foreach ($tab_ele as $login_eleve => $tab_ele_courant) {
                $enregistrements = array();
                if(isset($tab_ele_courant['NbreDemiJourneesCalcul_reduit'])) {
                    $enregistrements[$colonnes_individu[1]] = $tab_ele_courant['nom'].' ('.$tab_ele_courant['NbreDemiJourneesCalcul_reduit'].' demi-journées)';
                }else{
                    $enregistrements[$colonnes_individu[1]] = $tab_ele_courant['nom'];
                }
                $enregistrements[$colonnes_individu[2]] = $tab_ele_courant['prenom'];
                $enregistrements[$colonnes_individu[3]] = get_liste_classes_eleve($login_eleve);
                if(isset($tab_ele_courant['TauxDemiJourneesAbsence'])) {
                    $enregistrements[$colonnes_donnes[1]] = str_replace(",",".",$tab_ele_courant['TauxDemiJourneesAbsence']);
                }
                else {
                    $enregistrements[$colonnes_donnes[1]] = 0;
                }

                if(isset($tab_ele_courant['TauxDemiJourneesNonJustifiees'])) {
                    $enregistrements[$colonnes_donnes[2]] = str_replace(",",".",$tab_ele_courant['TauxDemiJourneesNonJustifiees']);
                }
                else {
                    $enregistrements[$colonnes_donnes[2]] = 0;
                }

                if(isset($tab_ele_courant['TauxDemiJourneesJustifiees'])) {
                    $enregistrements[$colonnes_donnes[3]] = str_replace(",",".",$tab_ele_courant['TauxDemiJourneesJustifiees']);
                }
                else {
                    $enregistrements[$colonnes_donnes[3]] = 0;
                }

                if ($affichage_motifs) {
                    $indice = 4;
                    foreach ($motifs_col as $motif) {
                        $enregistrements[$colonnes_donnes[$indice]]=0;
                        if(isset($tab_ele_courant['TauxDemiJourneesAbsenceMotif'.$motif->getId()])) {
                            $enregistrements[$colonnes_donnes[$indice]]=str_replace(",",".",$tab_ele_courant['TauxDemiJourneesAbsenceMotif'.$motif->getId()]);
                        }
                        $indice++;
                    }
                }
                $donnees_indiv[$tab_ele_courant['id_eleve']] = $enregistrements;
            }

            //remplissage du tableau de donnes moyennes
            /*
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
            */
            $enregistrements_moy[$colonnes_donnes[1]] = str_replace(",",".",getTauxAbsenteisme($nb_demijournees, $demi_journees_decompte));
            $enregistrements_moy[$colonnes_donnes[2]] = str_replace(",",".",getTauxAbsenteisme($nb_nonjustifiees, $demi_journees_decompte));
            $enregistrements_moy[$colonnes_donnes[3]] = str_replace(",",".",getTauxAbsenteisme($nb_justifiees, $demi_journees_decompte));
            if ($affichage_motifs) {
                $indice = 4;
                foreach ($motifs_col as $motif) {
                    $enregistrements_moy[$colonnes_donnes[$indice]] = str_replace(",",".",getTauxAbsenteisme($total_motif[$motif->getId()], $demi_journees_decompte));
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
