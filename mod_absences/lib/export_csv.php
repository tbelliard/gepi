<?php
/*
 * Copyright 2001, 2002 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../lib/functions.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

// Global configuration file
// Quand on est en SSL, IE n'arrive pas à ouvrir le PDF.
//Le problème peut être résolu en ajoutant la ligne suivante :
Header('Pragma: public');

// Lorsque qu'on utilise une session PHP, parfois, IE n'affiche pas le PDF
// C'est un problème qui affecte certaines versions d'IE.
// Pour le contourner, on ajoutez la ligne suivante avant session_start() :
session_cache_limiter('private');

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
die();
};

if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
die();
}

// initialisation des variables d'entrée
   if (empty($_GET['type']) and empty($_POST['type'])) { $type = ''; }
    else { if (isset($_GET['type'])) { $type = $_GET['type']; } if (isset($_POST['type'])) { $type = $_POST['type']; } }
   if (empty($_GET['justifie']) and empty($_POST['justifie'])) { $justifie = ''; }
    else { if (isset($_GET['justifie'])) { $justifie = $_GET['justifie']; } if (isset($_POST['justifie'])) { $justifie = $_POST['justifie']; } }
   if (empty($_GET['nonjustifie']) and empty($_POST['nonjustifie'])) { $nonjustifie = ''; }
    else { if (isset($_GET['nonjustifie'])) { $nonjustifie = $_GET['nonjustifie']; } if (isset($_POST['nonjustifie'])) { $nonjustifie = $_POST['nonjustifie']; } }
   if (empty($_GET['motif']) and empty($_POST['motif'])) { $motif = ''; }
    else { if (isset($_GET['motif'])) { $motif = $_GET['motif']; } if (isset($_POST['motif'])) { $motif = $_POST['motif']; } }
   if (empty($_GET['classe_choix']) and empty($_POST['classe_choix'])) { $classe_choix = ''; }
    else { if (isset($_GET['classe_choix'])) { $classe_choix = $_GET['classe_choix']; } if (isset($_POST['classe_choix'])) { $classe_choix = $_POST['classe_choix']; } }
   if (empty($_GET['eleve_choix']) and empty($_POST['eleve_choix'])) { $eleve_choix = ''; }
    else { if (isset($_GET['eleve_choix'])) { $eleve_choix = $_GET['eleve_choix']; } if (isset($_POST['eleve_choix'])) { $eleve_choix = $_POST['eleve_choix']; } }

	$du = isset($_GET["du"]) ? $_GET["du"] : (isset($_POST["du"]) ? $_POST["du"] : '');
	$au = isset($_GET["au"]) ? $_GET["au"] : (isset($_POST["au"]) ? $_POST["au"] : '');


// prépation de la requête
	if(!empty($type)) {
		$requete_recherche = 'type_absence_eleve = \''.$type.'\'';
	}
	if(!empty($justifie) and $justifie === '1') {
		if(!empty($requete_recherche) and $requete_recherche != '') {
			$requete_recherche = $requete_recherche.' AND ';
		}
		$requete_recherche = $requete_recherche.'( justify_absence_eleve = \'O\' ';
	}
	if(!empty($nonjustifie) and $nonjustifie === '1') {
		if(!empty($requete_recherche) and $requete_recherche != '') {
			if(!empty($justifie)) {
				$requete_recherche = $requete_recherche.' OR ';
			} else {
				$requete_recherche = $requete_recherche.' AND (';
			}
		}
		$requete_recherche = $requete_recherche.'justify_absence_eleve = \'N\' OR justify_absence_eleve = \'T\')';
	}
	if(!empty($justifie) and empty($nonjustifie)) {
		$requete_recherche = $requete_recherche.')';
	}
	if(!empty($motif) and $motif != 'tous') {
		if(!empty($requete_recherche) and $requete_recherche != '') {
			$requete_recherche = $requete_recherche.' AND ';
		} $requete_recherche = $requete_recherche.'motif_absence_eleve = \''.$motif.'\'';
	}
	if(!empty($classe_choix) and $classe_choix != 'tous') {
		if(!empty($requete_recherche) and $requete_recherche != '') {
			$requete_recherche = $requete_recherche.' AND ';
		}
		$requete_recherche = $requete_recherche.'c.id = \''.$classe_choix.'\'';
	}
	if(!empty($eleve_choix) and $eleve_choix != 'tous') {
		if(!empty($requete_recherche) and $requete_recherche != '') {
			$requete_recherche = $requete_recherche.' AND ';
		}
		$requete_recherche = $requete_recherche.'e.login = \''.$eleve_choix.'\'';
	}

	// Pour les dates, on ajoute modif didier pour couvrir les basences incluses dans une période
	$complement_requete_du = '';
	if ($du != '') {
		$test = explode("/", $du);
		$date1 = $test[2] . '-' . $test[1] . '-' . $test[0];
		$complement_requete_du = " AND ((d_date_absence_eleve >= '" . $date1 . "' ";
	}
    $complement_requete_au = '';
	if ($au != '') {
		$test = explode("/", $au);
		$date2 = $test[2] . '-' . $test[1] . '-' . $test[0];
		$complement_requete_au = " AND d_date_absence_eleve <= '" . $date2 . "' )";
	}
	
				
	$complement_requete_dateincluse = " OR (d_date_absence_eleve <= '" . $date1 . "' AND a_date_absence_eleve >= '" . $date2 . "'))";//modif didier

	$requete = "SELECT * FROM
					".$prefix_base."classes c,
					".$prefix_base."eleves e,
					".$prefix_base."j_eleves_classes ec,
					".$prefix_base."absences_eleves
					WHERE eleve_absence_eleve = e.login
					AND e.login = ec.login
					AND c.id = ec.id_classe
					AND ".$requete_recherche.$complement_requete_du.$complement_requete_au.$complement_requete_dateincluse. //modif didier
					"GROUP BY id_absence_eleve ORDER BY nom, prenom ASC";


// Entête du fichier CSV
header("Content-Type: application/csv-tab-delimited-table");
header("Content-disposition: filename=exportation.csv");

	$executer = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br>'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

    echo "NOM;PRENOM;CLASSE;TYPE ABS;JUSTIFIE;MOTIF;DATE DU;HEURE DE;DATE AU;HEURE A;\n";

	while ( $donner = mysqli_fetch_array( $executer ) )
   {
		echo $donner['nom'].";".$donner['prenom'].";".$donner['nom_complet'].";".$donner['type_absence_eleve'].";".$donner['justify_absence_eleve'].";".$donner['motif_absence_eleve'].";".$donner['d_date_absence_eleve'].";".$donner['d_heure_absence_eleve'].";".$donner['a_date_absence_eleve'].";".$donner['a_heure_absence_eleve'].";\n";
   }
?>
