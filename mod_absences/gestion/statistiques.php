<?php
/*
* $Id$
*
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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

//**************** EN-TETE *****************
$titre_page = "Gestion des absences";
require_once("../../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

        // voir numero d'erreur = 2047 toutes les erreurs
        //echo(error_reporting());

// Configuration du calendrier
	include("../../lib/calendrier/calendrier.class.php");
	$cal_1 = new Calendrier("form1", "du");
	$cal_2 = new Calendrier("form1", "au");

// Mes fonctions
	include("../lib/functions.php");

// fonction affiche les moyennes avec les arrondies et le nombre de chiffre après la virgule
// precision '0.01' '0.1' '0.25' '0.5' '1'
function present_nombre($nombre, $precision, $nb_chiffre_virgule, $chiffre_avec_zero)
 {
	if ( $precision === '' or $precision === '0.0' or $precision === '0' ) { $precision = '0.01'; }
	$nombre=number_format(round($nombre/$precision)*$precision, $nb_chiffre_virgule, ',', '');
        $nombre_explose = explode(",",$nombre);
	if($nombre_explose[1]==='0' and $chiffre_avec_zero==='1') { $nombre=$nombre_explose[0]; }
        return($nombre);
 }

// Variable prédéfinie
	$date_ce_jour = date('d/m/Y');
	$date_ce_jour_sql = date('Y-m-d');
	$annee_scolaire = annee_en_cours_t($date_ce_jour_sql);
	// donnée de l'axe X
	$donnee_x = '';
	// donnée de l'axe Y
	$donnee_y = '';
	// texte des labels
	$donnee_label = '';
	// titre du graphique
	$donnee_titre = '';

	$type_selectionne = '';
	$classe_selectionne = '';
	$eleve_selectionne = '';
	$type_selectionne_titre = '';
	$cpt_donnees = '0';
	$donnee_select = '';

// Variable non définie
	if (empty($_GET['classe']) and empty($_POST['classe'])) { $classe[0] = ''; }
	   else { if (isset($_GET['classe'])) { $classe = $_GET['classe']; } if (isset($_POST['classe'])) { $classe = $_POST['classe']; } }
	if (empty($_GET['eleve']) and empty($_POST['eleve'])) { $eleve[0] = ''; }
	   else { if (isset($_GET['eleve'])) { $eleve = $_GET['eleve']; } if (isset($_POST['eleve'])) { $eleve = $_POST['eleve']; } }
	if (empty($_GET['type']) and empty($_POST['type'])) { $type[0] = 'A'; }
	   else { if (isset($_GET['type'])) { $type = $_GET['type']; } if (isset($_POST['type'])) { $type = $_POST['type']; } }
	if (empty($_GET['justification']) and empty($_POST['justification'])) { $justification = 'T'; }
	   else { if (isset($_GET['justification'])) { $justification = $_GET['justification']; } if (isset($_POST['justification'])) { $justification = $_POST['justification']; } }

	// gestion des dates
	if (empty($_GET['du']) and empty($_POST['du'])) {$du = '';}
	 else { if (isset($_GET['du'])) {$du=$_GET['du'];} if (isset($_POST['du'])) {$du=$_POST['du'];} }
	if (empty($_GET['au']) and empty($_POST['au'])) {$au="JJ/MM/AAAA";}
	 else { if (isset($_GET['au'])) {$au=$_GET['au'];} if (isset($_POST['au'])) {$au=$_POST['au'];} }

		if (empty($_GET['day']) and empty($_POST['day'])) {$day=date("d");}
	    	 else { if (isset($_GET['day'])) {$day=$_GET['day'];} if (isset($_POST['day'])) {$day=$_POST['day'];} }
		if (empty($_GET['month']) and empty($_POST['month'])) {$month=date("m");}
		 else { if (isset($_GET['month'])) {$month=$_GET['month'];} if (isset($_POST['month'])) {$month=$_POST['month'];} }
		if (empty($_GET['year']) and empty($_POST['year'])) {$year=date("Y");}
		 else { if (isset($_GET['year'])) {$year=$_GET['year'];} if (isset($_POST['year'])) {$year=$_POST['year'];} }
	      	if ( !empty($du) ) {
		  $ou_est_on = explode('/',$du);
		  $year = $ou_est_on[2]; $month = $ou_est_on[1]; $day =  $ou_est_on[0];
	        } else { $du = $day."/".$month.'/'.$year; }

	if (empty($_GET['echelle_x']) and empty($_POST['echelle_x'])) { $echelle_x = 'M'; }
	   else { if (isset($_GET['echelle_x'])) { $echelle_x = $_GET['echelle_x']; } if (isset($_POST['echelle_x'])) { $echelle_x = $_POST['echelle_x']; } }
	if (empty($_GET['echelle_y']) and empty($_POST['echelle_y'])) { $echelle_y = 'D'; }
	   else { if (isset($_GET['echelle_y'])) { $echelle_y = $_GET['echelle_y']; } if (isset($_POST['echelle_y'])) { $echelle_y = $_POST['echelle_y']; } }
	if (empty($_GET['type_graphique']) and empty($_POST['type_graphique'])) { $type_graphique = 'ligne'; }
	   else { if (isset($_GET['type_graphique'])) { $type_graphique = $_GET['type_graphique']; } if (isset($_POST['type_graphique'])) { $type_graphique = $_POST['type_graphique']; } }
	if (empty($_GET['long_absence']) and empty($_POST['long_absence'])) { $long_absence = ''; }
	   else { if (isset($_GET['long_absence'])) { $long_absence = $_GET['long_absence']; } if (isset($_POST['long_absence'])) { $long_absence = $_POST['long_absence']; } }
	if (empty($_GET['doublon_journee']) and empty($_POST['doublon_journee'])) { $doublon_journee = ''; }
	   else { if (isset($_GET['doublon_journee'])) { $doublon_journee = $_GET['doublon_journee']; } if (isset($_POST['doublon_journee'])) { $doublon_journee = $_POST['doublon_journee']; } }

	// si au n'est pas défini alors on prend le premier jour du mois suivant
	if ( $au === '' or $au === 'JJ/MM/AAAA' or $du > $au ) { $au = mois_suivant($du); }

// Préparation des requêtes
	// pour la sélection des type A/R/I/D
	if(!empty($type[0])) {
		$i = '0';
		while ( !empty($type[$i]) )
		{
			if( $i === '0' ) { $type_selectionne = "(type_absence_eleve = '".$type[$i]."'"; }
			if( $i != '0' ) { $type_selectionne = $type_selectionne." OR type_absence_eleve = '".$type[$i]."'"; }
				if(empty($type[$i+1])) { $type_selectionne = $type_selectionne.")"; }
		$i = $i + 1;
		}
	}
	// pour la sélection de la justification ou non justification
	 // affiché tout
	 if ( $justification === 'T' ) { $justification_selectionne = "(justify_absence_eleve = 'O' OR justify_absence_eleve = 'T' OR justify_absence_eleve = 'N')";  }
	 // affiché ceux qui sont justifiée par lettre ou par téléphone
	 if ( $justification === 'O' ) { $justification_selectionne = "(justify_absence_eleve = 'O' OR justify_absence_eleve = 'T')";  }
	 // affiché ceux qui ne sont pas jsutifiée
	 if ( $justification === 'N' ) { $justification_selectionne = "(justify_absence_eleve = 'N')";  }

	// pour la sélection classe
	if(!empty($classe[0])) {
		$i = '0';
		while ( !empty($classe[$i]) )
		{
			if( $i === '0' ) { $classe_selectionne = "(c.id = '".$classe[$i]."'"; }
			if( $i != '0' ) { $classe_selectionne = $classe_selectionne." OR c.id = '".$classe[$i]."'"; }
				if(empty($classe[$i+1])) { $classe_selectionne = $classe_selectionne.")"; }
		$i = $i + 1;
		}
	}
	// pour la sélection élève
	if(!empty($eleve[0])) {
		$i = '0';
		while ( !empty($eleve[$i]) )
		{
			if( $i === '0' ) { $eleve_selectionne = "(eleve_absence_eleve = '".$eleve[$i]."'"; }
			if( $i != '0' ) { $eleve_selectionne = $eleve_selectionne." OR eleve_absence_eleve = '".$eleve[$i]."'"; }
				if(empty($eleve[$i+1])) { $eleve_selectionne = $eleve_selectionne.")"; }
		$i = $i + 1;
		}
	}

	// si on demande d'incorporer les longues absences
	if(!empty($long_absence) and $long_absence === '1')
	 { $long_absence_cocher = "(d_date_absence_eleve != a_date_absence_eleve OR d_date_absence_eleve = a_date_absence_eleve)"; }
	// si on ne demande pas d'incorporer les longues absences
	if(empty($long_absence) and $long_absence != '1')
	 { $long_absence_cocher = "d_date_absence_eleve = a_date_absence_eleve"; }

// Requete SQL
	// élève non sélectionné, classe non sélectionnée
	 if($eleve_selectionne === '' and $classe_selectionne === '')
	 { $requete_komenti = "SELECT * FROM ".$prefix_base."absences_eleves ae, ".$prefix_base."j_eleves_classes ec, ".$prefix_base."classes c WHERE ".$type_selectionne." AND ".$justification_selectionne." AND ".$long_absence_cocher." AND c.id = ec.id_classe AND ec.login = ae.eleve_absence_eleve GROUP BY id_absence_eleve ORDER BY d_date_absence_eleve ASC, d_heure_absence_eleve DESC"; }
	
	// Classe sélectionnée, élève non sélectionné
	 if($eleve_selectionne === '' and $classe_selectionne != '')
	 { $requete_komenti = "SELECT * FROM ".$prefix_base."absences_eleves ae, ".$prefix_base."j_eleves_classes ec, ".$prefix_base."classes c WHERE ".$classe_selectionne." AND ".$type_selectionne." AND ".$justification_selectionne." AND ".$long_absence_cocher." AND c.id = ec.id_classe AND ec.login = ae.eleve_absence_eleve GROUP BY id_absence_eleve ORDER BY d_date_absence_eleve ASC, d_heure_absence_eleve DESC"; }

	// élèves sélectionné, classe sélectionnée
	 if($eleve_selectionne != '' and $classe_selectionne != '')
	 { $requete_komenti = "SELECT * FROM ".$prefix_base."absences_eleves ae, ".$prefix_base."j_eleves_classes ec, ".$prefix_base."classes c WHERE ".$eleve_selectionne." AND ".$type_selectionne." AND ".$justification_selectionne." AND ".$long_absence_cocher." AND c.id = ec.id_classe AND ec.login = ae.eleve_absence_eleve GROUP BY id_absence_eleve ORDER BY d_date_absence_eleve ASC, d_heure_absence_eleve DESC"; }

	$i = '0';
	$execution_komenti = mysqli_query($GLOBALS["___mysqli_ston"], $requete_komenti) or die('Erreur SQL !'.$requete_komenti.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	// compte les données
	$cpt_donnees = mysqli_num_rows($execution_komenti);

	// vérification s'il contient des données
	if ( $cpt_donnees != '0' ) {
	    // si oui on charge les informations
             while ( $donnee_base = mysqli_fetch_array($execution_komenti))
                { 
			$tableau[$i]['id'] = $i;
			$tableau[$i]['login'] = $donnee_base['eleve_absence_eleve'];
			$tableau[$i]['classe'] = $donnee_base['nom_complet'];
			$tableau[$i]['id_classe'] = $donnee_base['id'];
			$tableau[$i]['date_debut'] = $donnee_base['d_date_absence_eleve'];
			$tableau[$i]['date_fin'] = $donnee_base['a_date_absence_eleve'];
			$tableau[$i]['heure_debut'] = $donnee_base['d_heure_absence_eleve'];
			$tableau[$i]['heure_fin'] = $donnee_base['a_heure_absence_eleve'];
			$i = $i + 1;
		} 

		/*
		echo "<pre>";
		echo "\$cpt_donnees=$cpt_donnees<br />";
		print_r($tableau);
		echo "</pre>";
		*/
		
	    $tab = crer_tableau_jaj($tableau);
		// en cas d'erreur pour affiché les informations
		/*
		echo '<pre>';
		print_r($tab);
		echo '</pre>';
		*/
	}


// si on désire les comptes en demi-journée
if($echelle_y === 'D') {
	$i = '0';
	$jour_temp_passe = '';
	$mois_temp_passe = '';
	$annee_temp_passe = '';
	$jour_nbenregistrement_passe = '';
	$mois_nbenregistrement_passe = '';
	$annee_nbenregistrement_passe = '';

	while ( !empty($tab[$i]) ) {
			$onprend = 'oui';
			// si on ne prend pas les doublons dans une même journée
			if( $doublon_journee != '')
			   {
				$eleve_passe = $tab[$i]['login'];
				$date_passe = $tab[$i]['date'];
				// on vérifie si l'élève à déjas une absence prise en compte dans une journée donnée
				if ( !isset($eleve_passe_tab[$date_passe][$eleve_passe]) )
				{
				   $onprend = 'oui';
				   $eleve_passe_tab[$date_passe][$eleve_passe] = 'oui';
				} else { $onprend = 'non'; }
			   }

			// on vérifie la sélection par rapport à une date si une date de début ou de fin on était donnée
			if( !empty($du) and $du != 'JJ/MM/AAAA' ) { $du_sql = date_sql($du); } else { $du_sql = ''; }
			if( !empty($au) and $au != 'JJ/MM/AAAA' ) { $au_sql = date_sql($au); } else { $au_sql = ''; }
			   if( ($du_sql != '' or $au_sql != '') and $onprend === 'oui' )
			   {
				// on vérifie si la date de début rentre dans l'information
				if ( $tab[$i]['date'] >= $du_sql )
				{
				   $onprend = 'oui';
				   if( $au_sql != '' )
				   {
					if ( $tab[$i]['date'] <= $au_sql ) {
						$onprend = 'oui';
					} else { $onprend = 'non'; }
				   }
				} else { $onprend = 'non'; }
			   }

	   if ( $onprend === 'oui' )
	   {
		// on explose la date en jour, mois, annee
		$tab_date = explode('-', $tab[$i]['date']);
			$jour = $tab_date[2];
			$mois = $tab_date[1];
			$annee = $tab_date[0];

		//$classe_eleve = $tab[$i]['classe'];
		$classe_eleve = $tab[$i]['id_classe'];
		$eleve_eleve = qui_eleve($tab[$i]['login']);

		/*
		echo "<br /><p>\$eleve_eleve=$eleve_eleve le ".$tab[$i]['date']."<br />
		\$echelle_x=$echelle_x<br />";
		*/

		// les données
			// par mois
			if($echelle_x === 'M') {
				if(empty($donnee_select[$annee.'-'.$mois])) { $donnee_select[$annee.'-'.$mois] = '0'; }
				$donnee_select[$annee.'-'.$mois] = $donnee_select[$annee.'-'.$mois] + nb_total_demijournee_absence($tab[$i]['login'], date_fr($tab[$i]['date']), date_fr($tab[$i]['date']), $classe_eleve);
				/*
				//DEBUG
				echo "nb_total_demijournee_absence(".$tab[$i]['login'].", date_fr(".$tab[$i]['date']."), date_fr(".$tab[$i]['date']."), $classe_eleve)=nb_total_demijournee_absence(".$tab[$i]['login'].", ".date_fr($tab[$i]['date']).", ".date_fr($tab[$i]['date']).", $classe_eleve)=".nb_total_demijournee_absence($tab[$i]['login'], date_fr($tab[$i]['date']), date_fr($tab[$i]['date']), $classe_eleve)."<br />";
				echo "\$donnee_select[$annee.'-'.$mois]=".$donnee_select[$annee.'-'.$mois]."<br />";
				*/
			}
			// par jour
			if($echelle_x === 'J') {
				if(empty($donnee_select[$annee.'-'.$mois.'-'.$jour])) { $donnee_select[$annee.'-'.$mois.'-'.$jour] = '0'; }
				$donnee_select[$annee.'-'.$mois.'-'.$jour] = $donnee_select[$annee.'-'.$mois.'-'.$jour] + nb_total_demijournee_absence($tab[$i]['login'], date_fr($tab[$i]['date']), date_fr($tab[$i]['date']), $classe_eleve);
				/*
				//DEBUG
				echo "\$donnee_select[$annee.'-'.$mois.'-'.$jour]=".$donnee_select[$annee.'-'.$mois.'-'.$jour]."<br />";
				*/
			}
			// par heure (période)
			if($echelle_x === 'P') {
			}
			// par classe
			if($echelle_x === 'C') {
				if(empty($donnee_select[$classe_eleve])) { $donnee_select[$classe_eleve] = '0'; }
				$donnee_select[$classe_eleve] = $donnee_select[$classe_eleve] + nb_total_demijournee_absence($tab[$i]['login'], date_fr($tab[$i]['date']), date_fr($tab[$i]['date']), $classe_eleve);
				/*
				//DEBUG
				echo "\$donnee_select[$classe_eleve]=".$donnee_select[$classe_eleve]."<br />";
				*/
			}
			// par élève
			if($echelle_x === 'E') {
				if(empty($donnee_select[$eleve_eleve])) { $donnee_select[$eleve_eleve] = '0'; }
				$donnee_select[$eleve_eleve] = $donnee_select[$eleve_eleve] + nb_total_demijournee_absence($tab[$i]['login'], date_fr($tab[$i]['date']), date_fr($tab[$i]['date']), $classe_eleve);
				/*
				//DEBUG
				echo "\$donnee_select[$eleve_eleve]=".$donnee_select[$eleve_eleve]."<br />";
				*/
			}
	   }		
	$i = $i + 1;
	}
}


// si on désire les comptes en horaire
if($echelle_y === 'H') {
	$i = '0';
	$jour_temp_passe = '';
	$mois_temp_passe = '';
	$annee_temp_passe = '';

	while ( !empty($tab[$i]) ) {
		$onprend = 'oui';
		// on vérifie la sélection par rapport à une date si une date de début ou de fin on était donnée
		if( !empty($du) and $du != 'JJ/MM/AAAA' ) { $du_sql = date_sql($du); } else { $du_sql = ''; }
		if( !empty($au) and $au != 'JJ/MM/AAAA' ) { $au_sql = date_sql($au); } else { $au_sql = ''; }
			   if( $du_sql != '' or $au_sql != '')
			   {
				// on vérifie si la date de début rentre dans l'information
				if ( $tab[$i]['date'] >= $du_sql )
				{
				   $onprend = 'oui';
				   if( $au_sql != '' )
				   {
					if ( $tab[$i]['date'] <= $au_sql ) {
						$onprend = 'oui';
					} else { $onprend = 'non'; }
				   }
				} else { $onprend = 'non'; }
			   }

	   if ( $onprend === 'oui' )
	   {
		// on explose la date en jour, mois, annee
		$tab_date = explode('-', $tab[$i]['date']);
			$jour = $tab_date[2];
			$mois = $tab_date[1];
			$annee = $tab_date[0];

		$classe_eleve = $tab[$i]['classe'];
		$eleve_eleve = qui_eleve($tab[$i]['login']);

		// total en minute de l'heure du début
		$total_minute_de = convert_heures_minutes($tab[$i]['heure_debut']);

		// total en minute de l'heure de fin
		$total_minute_a = convert_heures_minutes($tab[$i]['heure_fin']);

		// total en minute de l'absence
		$total_minute = $total_minute_a - $total_minute_de;
			// si suppérieur à 8h00 alors on prend 480 minutes au lieu de ce que l'on trouve
			if ( $total_minute > '480' ) { $total_minute = '480'; }
			// le jour tombe un dimanche donc = 0 on remet le compteur à 0
			if ( $tab[$i]['jour'] === '0' ) { $total_minute = '0'; }

		// les données
			// par mois
			if($echelle_x === 'M') {
				if(empty($donnee_select[$annee.'-'.$mois])) { $donnee_select[$annee.'-'.$mois] = '0'; }
				$donnee_select[$annee.'-'.$mois] = $donnee_select[$annee.'-'.$mois] + $total_minute;
			}
			// par jour
			if($echelle_x === 'J') {
				if(empty($donnee_select[$annee.'-'.$mois.'-'.$jour])) { $donnee_select[$annee.'-'.$mois.'-'.$jour] = '0'; }
				$donnee_select[$annee.'-'.$mois.'-'.$jour] = $donnee_select[$annee.'-'.$mois.'-'.$jour] + $total_minute;
			}
			// par heure (période)
			if($echelle_x === 'P') {
			}
			// par classe
			if($echelle_x === 'C') {
				if(empty($donnee_select[$classe_eleve])) { $donnee_select[$classe_eleve] = '0'; }
				$donnee_select[$classe_eleve] = $donnee_select[$classe_eleve] + $total_minute;
			}
			// par élève
			if($echelle_x === 'E') {
				if(empty($donnee_select[$eleve_eleve])) { $donnee_select[$eleve_eleve] = '0'; }
				$donnee_select[$eleve_eleve] = $donnee_select[$eleve_eleve] + $total_minute;
			}
	   }	
	$i = $i + 1;
	}
}

// si on désire les comptes en nombre d'enregistrement
if($echelle_y === 'E') {
	$i = '0';
	$jour_temp_passe = '';
	$mois_temp_passe = '';
	$annee_temp_passe = '';
	$jour_nbenregistrement_passe = '';
	$mois_nbenregistrement_passe = '';
	$annee_nbenregistrement_passe = '';

	while ( !empty($tab[$i]) ) {
			$onprend = 'oui';
			// si on ne prend pas les doublons dans une même journée
			if( $doublon_journee != '')
			   {
				$eleve_passe = $tab[$i]['login'];
				$date_passe = $tab[$i]['date'];
				// on vérifie si l'élève à déjas une absence prise en compte dans une journée donnée
				if ( !isset($eleve_passe_tab[$date_passe][$eleve_passe]) )
				{
				   $onprend = 'oui';
				   $eleve_passe_tab[$date_passe][$eleve_passe] = 'oui';
				} else { $onprend = 'non'; }
			   }

			// on vérifie la sélection par rapport à une date si une date de début ou de fin on était donnée
			if( !empty($du) and $du != 'JJ/MM/AAAA' ) { $du_sql = date_sql($du); } else { $du_sql = ''; }
			if( !empty($au) and $au != 'JJ/MM/AAAA' ) { $au_sql = date_sql($au); } else { $au_sql = ''; }
			   if( ($du_sql != '' or $au_sql != '') and $onprend === 'oui' )
			   {
				// on vérifie si la date de début rentre dans l'information
				if ( $tab[$i]['date'] >= $du_sql )
				{
				   $onprend = 'oui';
				   if( $au_sql != '' )
				   {
					if ( $tab[$i]['date'] <= $au_sql ) {
						$onprend = 'oui';
					} else { $onprend = 'non'; }
				   }
				} else { $onprend = 'non'; }
			   }

	   if ( $onprend === 'oui' )
	   {
		// on explose la date en jour, mois, annee
		$tab_date = explode('-', $tab[$i]['date']);
			$jour = $tab_date[2];
			$mois = $tab_date[1];
			$annee = $tab_date[0];

		$classe_eleve = $tab[$i]['classe'];
		$eleve_eleve = qui_eleve($tab[$i]['login']);

		// les données
			// par mois
			if($echelle_x === 'M') {
				if(empty($donnee_select[$annee.'-'.$mois])) { $donnee_select[$annee.'-'.$mois] = '0'; }
				$donnee_select[$annee.'-'.$mois] = $donnee_select[$annee.'-'.$mois] + 1;
			}
			// par jour
			if($echelle_x === 'J') {
				if(empty($donnee_select[$annee.'-'.$mois.'-'.$jour])) { $donnee_select[$annee.'-'.$mois.'-'.$jour] = '0'; }
				$donnee_select[$annee.'-'.$mois.'-'.$jour] = $donnee_select[$annee.'-'.$mois.'-'.$jour] + 1;
			}
			// par heure (période)
			if($echelle_x === 'P') {
			}
			// par classe
			if($echelle_x === 'C') {
				if(empty($donnee_select[$classe_eleve])) { $donnee_select[$classe_eleve] = '0'; }
				$donnee_select[$classe_eleve] = $donnee_select[$classe_eleve] + 1;
			}
			// par élève
			if($echelle_x === 'E') {
				if(empty($donnee_select[$eleve_eleve])) { $donnee_select[$eleve_eleve] = '0'; }
				$donnee_select[$eleve_eleve] = $donnee_select[$eleve_eleve] + 1;
			}
	   }		
	$i = $i + 1;
	}
}

		// en cas d'erreur pour affiché les informations
		//	echo '<pre>';
		//	print_r($donnee_select);
		//	echo '</pre>';


// Gestion du titre du graphique
	if(!empty($type[0])) {
		$i = '0';
		while ( !empty($type[$i]) )
		{
			if( $type[$i] === 'A' ) { $type_selectionne_titre = $type_selectionne_titre."d'absences"; }
			if( $type[$i] === 'R' ) { $type_selectionne_titre = $type_selectionne_titre."de retards"; }
			if( $type[$i] === 'D' ) { $type_selectionne_titre = $type_selectionne_titre."de dispences"; }
			if( $type[$i] === 'I' ) { $type_selectionne_titre = $type_selectionne_titre."de passage à l'infirmerie"; }
				if(!empty($type[$i+1]) and !empty($type[$i+2])) { $type_selectionne_titre = $type_selectionne_titre.", "; }
				if(!empty($type[$i+1]) and empty($type[$i+2])) { $type_selectionne_titre = $type_selectionne_titre." et "; }
		$i = $i + 1;
		}
	}

	if ( $type_graphique === 'ligne' ) {
		if ( $echelle_y === 'D' ) {
			if($echelle_x === 'M') { $donnee_titre[0] = "Demi-journée ".$type_selectionne_titre." par mois"; }
			if($echelle_x === 'J') { $donnee_titre[0] = "Demi-journée ".$type_selectionne_titre." par jour"; }
			if($echelle_x === 'P') { $donnee_titre[0] = "Demi-journée ".$type_selectionne_titre." pour heure (période)"; }
			if($echelle_x === 'C') { $donnee_titre[0] = "Demi-journée ".$type_selectionne_titre." par classe"; }
			if($echelle_x === 'E') { $donnee_titre[0] = "Demi-journée ".$type_selectionne_titre." par élève"; }
		}
		if ( $echelle_y === 'E' ) {
			if($echelle_x === 'M') { $donnee_titre[0] = "Total ".$type_selectionne_titre." par mois"; }
			if($echelle_x === 'J') { $donnee_titre[0] = "Total ".$type_selectionne_titre." par jour"; }
			if($echelle_x === 'P') { $donnee_titre[0] = "Total ".$type_selectionne_titre." pour heure (période)"; }
			if($echelle_x === 'C') { $donnee_titre[0] = "Total ".$type_selectionne_titre." par classe"; }
			if($echelle_x === 'E') { $donnee_titre[0] = "Total ".$type_selectionne_titre." par élève"; }
		}
		if ( $echelle_y === 'H' ) {
			if($echelle_x === 'M') { $donnee_titre[0] = "Heure ".$type_selectionne_titre." par mois"; }
			if($echelle_x === 'J') { $donnee_titre[0] = "Heure ".$type_selectionne_titre." par jour"; }
			if($echelle_x === 'P') { $donnee_titre[0] = "Heure ".$type_selectionne_titre." pour heure (période)"; }
			if($echelle_x === 'C') { $donnee_titre[0] = "Heure ".$type_selectionne_titre." par classe"; }
			if($echelle_x === 'E') { $donnee_titre[0] = "Heure ".$type_selectionne_titre." par élève"; }
		}
	}
	if ( $type_graphique === 'camembert' ) {
		if ( $echelle_y === 'D' ) {
			if($echelle_x === 'M') { $donnee_titre[0] = "Demi-journée ".$type_selectionne_titre." par mois"; }
			if($echelle_x === 'J') { $donnee_titre[0] = "Demi-journée ".$type_selectionne_titre." par jour"; }
			if($echelle_x === 'P') { $donnee_titre[0] = "Demi-journée ".$type_selectionne_titre." pour heure (période)"; }
			if($echelle_x === 'C') { $donnee_titre[0] = "Demi-journée ".$type_selectionne_titre." par classe"; }
			if($echelle_x === 'E') { $donnee_titre[0] = "Demi-journée ".$type_selectionne_titre." par élève"; }
		}
		if ( $echelle_y === 'E' ) {
			if($echelle_x === 'M') { $donnee_titre[0] = "Pourcentage du total ".$type_selectionne_titre." par mois"; }
			if($echelle_x === 'J') { $donnee_titre[0] = "Pourcentage du total ".$type_selectionne_titre." par jour"; }
			if($echelle_x === 'P') { $donnee_titre[0] = "Pourcentage du total ".$type_selectionne_titre." pour heure (période)"; }
			if($echelle_x === 'C') { $donnee_titre[0] = "Pourcentage du total ".$type_selectionne_titre." par classe"; }
			if($echelle_x === 'E') { $donnee_titre[0] = "Pourcentage du total ".$type_selectionne_titre." par élève"; }
		}
		if ( $echelle_y === 'H' ) {
			if($echelle_x === 'M') { $donnee_titre[0] = "Pourcentage d'heure ".$type_selectionne_titre." par mois"; }
			if($echelle_x === 'J') { $donnee_titre[0] = "Pourcentage d'heure ".$type_selectionne_titre." par jour"; }
			if($echelle_x === 'P') { $donnee_titre[0] = "Pourcentage d'heure ".$type_selectionne_titre." pour heure (période)"; }
			if($echelle_x === 'C') { $donnee_titre[0] = "Pourcentage d'heure ".$type_selectionne_titre." par classe"; }
			if($echelle_x === 'E') { $donnee_titre[0] = "Pourcentage d'heure ".$type_selectionne_titre." par élève"; }
		}
	}



?>
<p class=bold><a href='gestion_absences.php?year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>'><img src="../../images/icons/back.png" alt="Retour" title="Retour" class="back_link" />&nbsp;Retour</a> |
<a href="impression_absences.php?year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Impression</a> | 
<a href="statistiques.php?year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Statistiques</a> | 
<a href="gestion_absences.php?choix=lemessager&amp;year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Le messager</a> | 
<a href="alert_suivi.php?choix=alert&amp;year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Système d'alerte</a>
</p>

<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align: center; width: 760px; margin: auto;">

<?php /* DIV contenant le formulaire de recherche */ ?>
  <div style="border: 2px solid #D9EF1D; width: 210px; height: 360px; float: left; margin-top: 17px; text-align: center;">
	<div class="entete_stats"><b>Système d'alerte</b></div>
	<div>
		<form name="form1" method="post" action="statistiques.php">
	               <select name="type[]" id="type" multiple="multiple" size="3" tabindex="1" style="width: 200px; border : 1px solid #000000; margin-top: 5px;">
      			    <optgroup label="Les types">
		               <option value="A" <?php if(!empty($type) and in_array('A', $type)) { ?>selected="selected"<?php } ?>>Les absences</option>
        		       <option value="R" <?php if(!empty($type) and in_array('R', $type)) { ?>selected="selected"<?php } ?>>Les retards</option>
                	       <option value="D" <?php if(!empty($type) and in_array('D', $type)) { ?>selected="selected"<?php } ?>>Les dispenses</option>
	                       <option value="I" <?php if(!empty($type) and in_array('I', $type)) { ?>selected="selected"<?php } ?>>Les passages à l'infirmerie</option>
			    </optgroup>
	               </select><br />

			<input name="justification" id="jus1" value="T" tabindex="8" type="radio" <?php if(!empty($justification) and $justification === 'T') { ?>checked="checked"<?php } ?> /><label for="jus1" title="Justifié et non justifié" style="cursor: pointer;">Tous</label>
			<input name="justification" id="jus2" value="O" tabindex="8" type="radio" <?php if(!empty($justification) and $justification === 'O') { ?>checked="checked"<?php } ?> /><label for="jus2" title="Justifié" style="cursor: pointer;">Justi.</label>
			<input name="justification" id="jus3" value="N" tabindex="9" type="radio" <?php if(!empty($justification) and $justification === 'N') { ?>checked="checked"<?php } ?> /><label for="jus3" title="Non justifié" style="cursor: pointer;">Non justi.</label><br />

	                <select name="classe[]" id="classe" multiple="multiple" size="3" tabindex="2" style="width: 200px; border : 1px solid #000000; margin-top: 5px;">
			    <optgroup label="Les classes">
          		        <option value="" <?php if($classe[0] === '') { ?>selected="selected"<?php } ?>>toutes</option>
                 		<?php
				$requete_liste_classe = "SELECT id, classe, nom_complet FROM ".$prefix_base."classes ORDER BY nom_complet ASC";
	                    	$resultat_liste_classe = mysqli_query($GLOBALS["___mysqli_ston"], $requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	                    	while ( $data_liste_classe = mysqli_fetch_array($resultat_liste_classe)) 
				{ ?>
	                          <option value="<?php echo $data_liste_classe['id']; ?>" <?php if(!empty($classe) and in_array($data_liste_classe['id'], $classe)) { ?>selected="selected"<?php } ?>><?php echo $data_liste_classe['nom_complet']." (".$data_liste_classe['classe'].")"; ?></option>
        	          <?php } ?>
			     </optgroup>
	                </select><br />

			<input name="long_absence" id="lon1" value="1" tabindex="4" type="checkbox" <?php if(!empty($long_absence) and $long_absence === '1') { ?>checked="checked"<?php } ?> /><label for="lon1" title="Comptabiliser les longues absences" style="cursor: pointer;">Longues absences</label><br />
			<input name="doublon_journee" id="dou1" value="1" tabindex="5" type="checkbox" <?php if(!empty($doublon_journee) and $doublon_journee === '1') { ?>checked="checked"<?php } ?> /><label for="dou1" title="Comptabiliser un élève q'une fois par jour" style="cursor: pointer;">Compte une fois</label><br />

		<?php if($classe[0] != '') { ?>
            		<select name="eleve[]" id="eleve" multiple="multiple" size="3" tabindex="3" style="width: 200px; border : 1px solid #000000; margin-top: 5px;">
			    <optgroup label="Les élèves">
		                <option value="">tous</option>
	                        <?php
  				if ($classe[0] === '') { $requete_liste_eleve = "SELECT login, nom, prenom FROM ".$prefix_base."eleves ORDER BY nom, prenom ASC";
				  } else { $requete_liste_eleve = "SELECT e.login, e.nom, e.prenom, ec.login, ec.id_classe, ec.periode, c.id, c.classe, c.nom_complet FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_classes ec, ".$prefix_base."classes c WHERE ".$classe_selectionne." AND e.login=ec.login AND ec.id_classe=c.id GROUP BY e.login ORDER BY e.nom, e.prenom ASC"; }
                    		$resultat_liste_eleve = mysqli_query($GLOBALS["___mysqli_ston"], $requete_liste_eleve) or die('Erreur SQL !'.$requete_liste_eleve.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
                    		while ( $data_liste_eleve = mysqli_fetch_array($resultat_liste_eleve))
				{ ?>
                  	          <option value="<?php echo $data_liste_eleve['login']; ?>" <?php if(!empty($eleve) and in_array($data_liste_eleve['login'], $eleve)) { ?>selected="selected"<?php } ?>><?php echo strtoupper($data_liste_eleve['nom'])." ".ucfirst($data_liste_eleve['prenom']); ?></option>
        	          <?php } ?>
			    </optgroup>
	            	</select><br />
	        <?php } else { ?><input type="hidden" name="eleve[0]" value="" /><?php } ?>


			du&nbsp;<input name="du" onfocus="javascript:this.select()" type="text" tabindex="4" value="<?php if(isset($du) and !empty($du)) { echo $du; } else { echo $date_ce_jour; } ?>" size="10" maxlength="10" style="border: 1px solid #000000;" /><a href="#calend" onClick="<?php echo $cal_1->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a><br />

			au&nbsp;<input name="au" onfocus="javascript:this.select()" type="text" tabindex="5" value="<?php if (isset($au) and !empty($au)) { echo $au; } else { ?>JJ/MM/AAAA<?php } ?>" size="10" maxlength="10" style="border: 1px solid #000000;" /><a href="#calend" onClick="<?php echo $cal_2->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a><br />

	               <select name="echelle_x" id="echelle_x" size="1" tabindex="6" style="width: 200px; border : 1px solid #000000; margin-top: 5px;">
      			    <optgroup label="Les échelles (X)">
		               <option value="M" <?php if(!empty($echelle_x) and $echelle_x === 'M') { ?>selected="selected"<?php } ?>>Par mois</option>
        		       <option value="J" <?php if(!empty($echelle_x) and $echelle_x === 'J') { ?>selected="selected"<?php } ?>>Par jour</option>
                	       <?php /* <option value="P" <?php if(!empty($echelle_x) and $echelle_x === 'P') { ?>selected="selected"<?php } ?>>Par heure (période)</option> */ ?>
                	       <option value="C" <?php if(!empty($echelle_x) and $echelle_x === 'C') { ?>selected="selected"<?php } ?>>Par classe</option>
                	       <option value="E" <?php if(!empty($echelle_x) and $echelle_x === 'E') { ?>selected="selected"<?php } ?>>Par élève</option>
			    </optgroup>
	               </select><br />

	               <select name="echelle_y" id="echelle_y" size="1" tabindex="7" style="width: 200px; border : 1px solid #000000; margin-top: 5px;">
      			    <optgroup label="Les échelles (Y)">
		               <option value="D" <?php if(!empty($echelle_y) and $echelle_y === 'D') { ?>selected="selected"<?php } ?>>Demi-journée</option>
		               <option value="E" <?php if(!empty($echelle_y) and $echelle_y === 'E') { ?>selected="selected"<?php } ?>>Nombre d'enregistrements</option>
		               <option value="H" <?php if(!empty($echelle_y) and $echelle_y === 'H') { ?>selected="selected"<?php } ?>>Nombre d'heures</option>
			    </optgroup>
	               </select><br />

			<input name="type_graphique" id="gra1" value="ligne" tabindex="8" type="radio" <?php if(!empty($type_graphique) and $type_graphique === 'ligne') { ?>checked="checked"<?php } ?> /><label for="gra1" style="cursor: pointer;">Ligne</label>
			<input name="type_graphique" id="gra2" value="camembert" tabindex="9" type="radio" <?php if(!empty($type_graphique) and $type_graphique === 'camembert') { ?>checked="checked"<?php } ?> /><label for="gra2" style="cursor: pointer;">Camembert</label><br />
			<br />
			<input type="submit" name="submit1" value="Valider" tabindex="10" /><br />
		</form>
	</div>
  </div>

  <div style="margin-left: 210px; width: 555px;">
	<div class="entete_stats_message">Graphique des statistiques</div>
	<div style="background-color: #EFEFEF; border-left: 4px solid #D9EF1D;">

	<?php /* DIV contenant le graphique et le tableau des données */ ?>
	<div style="width: 550px; border : 0px solid #0061BD; text-align: center; /* background-color: #0061BD; */">
		<?php /* DIV contenant le graphique */ ?>
		<div>

	<?php
		if ( $cpt_donnees != '0' and $donnee_select != '') {
			// DEBUG
			/*
			echo "<pre>";
			print_r($donnee_select);
			echo "</pre>";
			*/
			if($echelle_x=="C") {
				$tmp_tab=$donnee_select;
				unset($donnee_select);
				foreach($tmp_tab as $key => $value) {
					$donnee_select[get_class_from_id($key)]=$value;
				}
			}

			$_SESSION['donnee_e'] = '';
			$_SESSION['donnee_e'] = $donnee_select;
		?>
			<img src="../lib/graph_<?php echo $type_graphique; ?>.php?echelle_x=<?php echo $echelle_x; ?>&amp;echelle_y=<?php echo $echelle_y; ?>&amp;donnee_label=<?php echo $donnee_label; ?>&amp;donnee_titre[0]=<?php echo $donnee_titre[0]; ?>" alt="Graphique" style="border: 0px; margin: 0px; padding: 0px;"/>
			<?php /* <a href="graph_<?php echo $type_graphique; ?>.php?echelle_x=<?php echo $echelle_x; ?>&amp;echelle_y=<?php echo $echelle_y; ?>&amp;donnee_label=<?php echo $donnee_label; ?>&amp;donnee_titre[0]=<?php echo $donnee_titre[0]; ?>" alt="Graphique" style="border: 0px; margin: 0px; padding: 0px;"/>fdfdfdf</a> */ ?>
	<?php } else { ?>Aucune donnée correspondant à votre recherche n'a été trouvée<?php } ?>
		</div>

	<?php if ( $cpt_donnees != '0' and $donnee_select != '') { ?>
		<?php /* DIV contenant le tableau des données */ ?>
		<div>
			<?php 
				// donner d'entête du tableau
				$entete_tableau = array_keys($_SESSION['donnee_e']);
				/*
				$tmp_entete_tableau=$entete_tableau;
				unset($entete_tableau);
				foreach($tmp_entete_tableau as $key => $value) {
					$entete_tableau[get_class_from_id($key)]=$value;
				}
				*/
					if ( $echelle_x === 'M' ) {
						$entete_tableau_recharge = $entete_tableau;
						$i = 0;
						while ( !empty($entete_tableau_recharge[$i]) )
						{
							$valeur = explode('-',$entete_tableau_recharge[$i]);
							$entete_tableau[$i] = convert_num_mois_court($valeur[1]).' '.$valeur[0];
							$valeur = '';
							$i = $i + 1;
						}
					}
					if ( $echelle_x === 'J') {
						$i = 0;
						$entete_tableau_recharge = $entete_tableau;
						while ( !empty($entete_tableau_recharge[$i]) )
						{
							$entete_tableau[$i] = date_fr($entete_tableau_recharge[$i]);
							$i = $i + 1;
						}
					}

				// valeur du tableau
				$donnee_tableau = array_values($_SESSION['donnee_e']);
					if ( $echelle_y === 'H' ) {
						$donnee_tableau_recharge = $donnee_tableau;
						$i = 0;
						while ( !empty($donnee_tableau_recharge[$i]) )
						{
							$donnee_tableau[$i] = convert_minutes_heures($donnee_tableau_recharge[$i]);
							$i = $i + 1;
						}
					}

			// pour l'affichage des données en pourcentage
			if ( $type_graphique === 'camembert' )
			{
			// calcule du pourcentage des données
				// calcule du total des valeurs
				$donnee_tableau = array_values($_SESSION['donnee_e']);
				$i = 0; $total_des_valeurs = 0; $donnee_tableau_pourcentage = '';
				while ( !empty($donnee_tableau[$i]) )
				{	
					$total_des_valeurs = $total_des_valeurs + $donnee_tableau[$i];
					$donnee_tableau_pourcentage[$i] = $donnee_tableau[$i];
				$i = $i + 1;
				}
				// remise des informations en pourcentage
				$i = 0;
				while ( !empty($donnee_tableau[$i]) )
				{	
					$donnee_tableau[$i] = ( $donnee_tableau_pourcentage[$i] * 100 ) / $total_des_valeurs;
					$donnee_tableau[$i] = present_nombre($donnee_tableau[$i], '0.1', 1, 1).'%';
				$i = $i + 1;
				}
			}

				// compte le total d'entrée du tableau
				$cpt_total_entree = count($entete_tableau);
				// nombre d'entrée à affiché par ligne
				$cpt_total_par_ligne = '3'; 
				// compte le nombre de ligne qu'il faut affciher
				$cpt_ligne = $cpt_total_entree / $cpt_total_par_ligne;
				// on explose la valeur pour savoir s'il y a des chiffre après la virgule
				$valeur = explode('.',$cpt_ligne);
				// s'il y a des chiffres après la virgule alors on rajoute une ligne
				if(!empty($valeur[1]) and $valeur[1] != '0') { $cpt_tableau = $valeur[0] + 1; } else { $cpt_tableau = $valeur[0]; }

			$i_tableau = '0';
			$ia_passe = '0';
			$ib_passe = '0';
			while ($i_tableau < $cpt_tableau) { ?>
			<table style="width: 550px;  border: 1px solid #000000;" border="0" cellpadding="0" cellspacing="1">
			   <tr class="entete_tableau_absence">
				<?php
					$ia = '0';
					while ($ia < $cpt_total_par_ligne) { 
						if(!empty($entete_tableau[$ia_passe])) {
							echo "<td style=\"width: 25%; color: #FFFFFF;\">";
							echo $entete_tableau[$ia_passe];
							//echo get_class_from_id($entete_tableau[$ia_passe]);
							echo "</td>";
						}
						if(empty($entete_tableau[$ia_passe])) {
							echo "<td style=\"width: 25%; color: #FFFFFF;\">&nbsp;</td>\n";
						}
						$ia = $ia + 1;
						$ia_passe = $ia_passe + 1;
					}
				?>
			   </tr>
			   <tr>
				<?php   $ib = '0';
					$ic = '1';
					while ($ib < $cpt_total_par_ligne) {
				              if ($ic === '1') { $ic = '2'; $couleur_cellule = 'couleur_ligne_3'; } else { $couleur_cellule = 'couleur_ligne_4'; $ic = '1'; } ?>
						<?php if(!empty($donnee_tableau[$ib_passe])) { ?><td class="<?php echo $couleur_cellule; ?>"><?php echo $donnee_tableau[$ib_passe]; ?></td><?php } ?>
						<?php if(empty($donnee_tableau[$ib_passe])) { ?><td class="<?php echo $couleur_cellule; ?>">&nbsp;</td><?php } ?>
				<?php $ib = $ib + 1; $ib_passe = $ib_passe + 1; } ?>
			    </tr>
			</table>
			<?php $i_tableau = $i_tableau + 1; } ?>
		</div>
		<?php } ?>
<br />
	</div>
</div></div>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>

<?php require("../../lib/footer.inc.php"); ?>
