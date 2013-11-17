<?php
/*
*
* $Id$
*
* Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau, Christian Chapel
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
//================================
// REMONTé: boireaus 20080102
// Initialisations files
require_once('../../lib/initialisations.inc.php');

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
}


if (!checkAccess()) {
//	header("Location: ../logout.php?auto=1");
//	die();
}
//================================

//================================
// AJOUT: boireaus 20080102
if(!isset($_SESSION['pdf_debug']))
{

	header('Content-Type: application/pdf');

	// Global configuration file
	// Quand on est en SSL, IE n'arrive pas à ouvrir le PDF.
	//Le problème peut être résolu en ajoutant la ligne suivante :
	Header('Pragma: public');

}
else
{

	echo "<p style='color:red'>DEBUG:<br />
	      La génération du PDF va échouer parce qu'on affiche ces informations de debuggage,<br />
              mais il se peut que vous ayez des précisions sur ce qui pose problème.<br />
              </p>\n";

}
//================================

require('../../fpdf/fpdf.php');

include("../../edt_organisation/fonctions_edt.php");
include("../../edt_organisation/fonctions_calendrier.php");

include("../lib/functions.php");

define('FPDF_FONTPATH','../../fpdf/font/');

// définition des marge
	define('TopMargin','5');
	define('RightMargin','2');
	define('LeftMargin','2');
	define('BottomMargin','5');
	define('LargeurPage','210');
	define('HauteurPage','297');


/* ************************* */
/*    fonction spécifique    */
/* ************************* */
function redimensionne_image_logo($photo, $L_max, $H_max)
{
	// prendre les informations sur l'image
	$info_image = getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur = $info_image[0];
	$hauteur = $info_image[1];
	// largeur et/ou hauteur maximum à afficher en pixel
	$taille_max_largeur = $L_max;
	$taille_max_hauteur = $H_max;

	// calcule le ratio de redimensionnement
	$ratio_l = $largeur / $taille_max_largeur;
	$ratio_h = $hauteur / $taille_max_hauteur;
	$ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

	// définit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur = $largeur / $ratio;
	$nouvelle_hauteur = $hauteur / $ratio;

	// des Pixels vers Millimetres
	$nouvelle_largeur = $nouvelle_largeur / 2.8346;
	$nouvelle_hauteur = $nouvelle_hauteur / 2.8346;

	return array($nouvelle_largeur, $nouvelle_hauteur);
}

/* ************************* */


/* ******************************************** */
/*     initialisation des variable d'entré      */
/* ******************************************** */

	// si aucune date de demandé alors on met celle du jour au format jj/mm/aaaa
	if (empty($_GET['date_choisie']) and empty($_POST['date_choisie'])) { $date_choisie = date("d/m/Y"); }
	  else { if (isset($_GET['date_choisie'])) { $date_choisie = $_GET['date_choisie']; } if (isset($_POST['date_choisie'])) { $date_choisie = $_POST['date_choisie']; } }
	if (empty($_GET['du']) and empty($_POST['du'])) { $du = ''; }
	  else { if (isset($_GET['du'])) { $du = $_GET['du']; } if (isset($_POST['du'])) { $du = $_POST['du']; } }

	if ( $du != '' ) { $date_choisie = $du; }



/* ******************************************** */

/* *********************************************/
/* information sur la présentation du document */
/* *********************************************/

	$caractere_utilse = 'DejaVu'; // caractère utilisé
	$affiche_logo_etab = '1';
	$nom_etab_gras = '0';
	$entente_mel = '1'; // afficher l'adresse mel dans l'entête
		$courrier_image = '';
		$courrier_texte = '';
	$entente_tel = '1'; // afficher le numéro de téléphone dans l'entête
		$tel_image = '';
		$tel_texte = '';
	$entente_fax = '1'; // afficher le numéro de fax dans l'entête
		$fax_image = '';
		$fax_texte = '';
	$L_max_logo = 75; $H_max_logo=75; //dimension du logo
	$centrage_logo = '1'; // centrer le logo de l'établissement
	$Y_centre_logo = '18'; // centre du logo sur la page
	$affiche_date_edition = '1'; // affiche la date d'édition
	$taille_texte_date_edition = '8'; // définit la taille de la date d'édition

	// point de commencement du tableau sur la page
	$x_tab = '5';
	$y_tab = '40';

	// hauteur de l'entête
	$hau_entete = '6';

	// largeur de la colonne de l'intitulé de la classe
	$lar_col_classe = '20';

	// largeur de la colonne du nom de l'élève
	$lar_col_eleve = '50';

	// largeur de la colonne du nom des créneaux
	$lar_col_creneaux = '130';

	// nombre de ligne à affiché sur 1 page
	$nb_ligne_parpage = '25';

	// largeur total du tableau
	$lar_total_tableau = $lar_col_classe + $lar_col_eleve + $lar_col_creneaux;

	// hauteur de la cellule des données
	$hau_donnee = '5';

	// avec couleur ou sans
	$couleur_fond = '1';


/* *********************************************/


/* ******************************************** */
/*     construction du tableau des données      */
/* ******************************************** */
// chargement des information de la base de données

	// les données concernerons la journée du (date au format timestamp)
	$choix_date = explode("/",$date_choisie);
	$date_choisie_ts = mktime(0,0,0, $choix_date[1], $choix_date[0], $choix_date[2]);

	// On récupère le nom des créneaux
	$creneaux = retourne_creneaux();

	// on compte le nombre de créneaux
	$nb_creneaux = count($creneaux);

	// On détermine le jour au format texte en Français actuel
	$jour_choisi = retourneJour(date("w", $date_choisie_ts));

	// on recherche l'horaire d'ouverture et de fermetture de l'établissement
	$requete = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT ouverture_horaire_etablissement, fermeture_horaire_etablissement
				FROM horaires_etablissement
				WHERE jour_horaire_etablissement = '" . $jour_choisi . "'");
	$nbre_rep = mysqli_num_rows($requete);
	if ($nbre_rep >= 1)
	{

		// Avec le résultat, on calcule les timestamps UNIX
		$req = mysqli_fetch_array($requete);
		$rep_deb = explode(":", $req["ouverture_horaire_etablissement"]);
		$rep_fin = explode(":", $req["fermeture_horaire_etablissement"]);
		$time_actu_deb = mktime($rep_deb[0], $rep_deb[1], 0, $choix_date[1], $choix_date[0], $choix_date[2]);
		$time_actu_fin = mktime($rep_fin[0], $rep_fin[1], 0, $choix_date[1], $choix_date[0], $choix_date[2]);

	}
	else
	{

		// Si on ne récupère rien, on donne par défaut les ts du jour actuel
		$time_actu_deb = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$time_actu_fin = mktime(23, 59, 0, date("m"), date("d"), date("Y"));

	}

	// nous recherchons tout les élèves absence le jour choisie
	$requete = "SELECT ar.id, ar.eleve_id, ar.retard_absence, ar.creneau_id, ar.debut_ts, ar.fin_ts, jec.login, jec.id_classe, e.login, e.nom, e.prenom, c.id, c.nom_complet, c.classe
		    FROM " . $prefix_base . "absences_rb ar, " . $prefix_base . "j_eleves_classes jec, " . $prefix_base . "eleves e, " . $prefix_base . "classes c
		    WHERE ( jec.login = ar.eleve_id
		      AND jec.id_classe = c.id
		      AND jec.login = e.login
		      AND eleve_id != 'appel'
		      AND
		      (
		      (
				debut_ts BETWEEN '" . $time_actu_deb . "' AND '" . $time_actu_fin . "'
				AND fin_ts BETWEEN '".$time_actu_deb . "' AND '" . $time_actu_fin . "'
       		  )
       		  OR
       		  (
				'" . $time_actu_deb . "' BETWEEN debut_ts AND fin_ts
				OR '" . $time_actu_fin . "' BETWEEN debut_ts AND fin_ts
       		  )
       		  AND debut_ts != '" . $time_actu_fin . "'
         	  AND fin_ts != '" . $time_actu_deb . "'
         	  )
		    )
		    GROUP BY ar.id
		    ORDER BY c.classe ASC, eleve_id ASC";

	// on insère toute les classes dans un tableau
	// initialisation du tableau
	$tab_classe = '';

	// requete de liste des classes présente dans la base de donnée
	$requete_classes = "SELECT nom_complet, classe
		    			FROM " . $prefix_base . "classes
		    			ORDER BY classe ASC";

	// compteur de classe temporaire
	$cpt_classe = 0;

	$execution_classes = mysqli_query($GLOBALS["___mysqli_ston"], $requete_classes) or die('Erreur SQL !'.$requete_classes.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while ( $donnee_classes = mysqli_fetch_array($execution_classes) )
	{

		$tab_classe[$cpt_classe] = $donnee_classes['classe'];

		// incrémentation du compteur
		$cpt_classe = $cpt_classe + 1;

	}


	// compteur temporaire pour la boucle ci-dessous
	// compteur élève et classe
	$i = 0;
	// compteur des classes passé
	$ic = 0;

	// nom de l'élève précédent
	$eleve_precedent = '';
	$classe_precedent = '';

	$execution = mysqli_query($GLOBALS["___mysqli_ston"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	while ( $donnee = mysqli_fetch_array($execution))
	{

		$passe = 0;
		// si l'enregistrement sélectionner correspond ne correspond pas à la $ic classe
		while ( $tab_classe[$ic] != $donnee['classe'] )
		{

			$tab_donnee[$i]['classe'] = $tab_classe[$ic];
			$tab_donnee[$i]['ident_eleve'] = '';

			// type A ou R -- absence ou retard
			$type = 'entete_classe';

			$i = $i + 1;
			$ic = $ic + 1;
			$passe = 1;

		}

		// si l'élève précedent et différent de celui que nous traiton et que la variable de l'élève précédent
		// est différent de rien alors on incrément notre tableau de donnée
		if ( $eleve_precedent != $donnee['login'] and $eleve_precedent != '' and $passe != 1)
		{
			$i = $i + 1;
		}

		if ( $eleve_precedent != $donnee['login'] )
		{

			if ( $classe_precedent != $donnee['classe'] )
			{

				// on insère les informations sur la classe de l'élève
				$tab_donnee[$i]['classe'] = $donnee['classe'];
				$tab_donnee[$i]['ident_eleve'] = '';

				$classe_precedent = $donnee['classe'];

				// on incrémente le tableau principal
				$i = $i + 1;

			}

			// on incrémente le compteur de classe
			//$ic = $ic + 1;

			// nom de l'élève et prénom
			$tab_donnee[$i]['classe'] = $donnee['classe'];
			$tab_donnee[$i]['ident_eleve'] = strtoupper($donnee['nom']) . ' ' . ucfirst($donnee['prenom']);

			// type A ou R -- absence ou retard
			$type = $donnee['retard_absence'];

			// fonction qui décode un timestamps en date et heure un tableau $donnee['heure'] $donnee['date']
			$heure_debut = timestamps_decode($donnee['debut_ts'], 'fr');
				$heure_debut = $heure_debut['heure'];
			$heure_fin = timestamps_decode($donnee['fin_ts'], 'fr');
				$heure_fin = $heure_fin['heure'];

			// fonction permettant de savoir dans quelle période nous nous trouvons par rapport à une heur donnée
			$periode = '';
			$periode = creneau_absence_du_jour($donnee['login'],$date_choisie,$type);

			// si des période existe
			if ( $periode != '' )
			{

				// apprés la récupération des périodes sur lesquelle l'absence ce tient on l'explose en talbeau
				$periode_tab = explode(';',$periode);

				// compteur temporaire de période
				$compteur_periode = 0;

				while ( !empty($periode_tab[$compteur_periode]) )
				{

					// nom de la période sélectionné
					$periode_select = $periode_tab[$compteur_periode];

					// définition des donnée de $tab_donnee_sup
					$tab_donnee_sup[$i][$periode_select][$type] = '1';

					// compteur des passages
					$compteur_periode = $compteur_periode + 1;

				}

			}

			$eleve_precedent = $donnee['login'];
			$classe_precedent = $donnee['classe'];

		}
		else
		{

			// type A ou R -- absence ou retard
			$type = $donnee['retard_absence'];

			// fonction qui décode un timestamps en date et heure un tableau $donnee['heure'] $donnee['date']
			$heure_debut = timestamps_decode($donnee['debut_ts'], 'fr');
				$heure_debut = $heure_debut['heure'];
			$heure_fin = timestamps_decode($donnee['fin_ts'], 'fr');
				$heure_fin = $heure_fin['heure'];

			// fonction permettant de savoir dans quelle période nous nous trouvons par rapport à une heur donnée
			//$periode = periode_active_nom($heure_debut, $heure_fin);
			$periode = '';
			$periode = creneau_absence_du_jour($donnee['login'],$date_choisie,$type);

			// si des période existe
			if ( $periode != '' )
			{

				// apprés la récupération des périodes sur lesquelle l'absence ce tient on l'explose en talbeau
				$periode_tab = explode(';',$periode);

				// compteur temporaire de période
				$compteur_periode = 0;

				while ( !empty($periode_tab[$compteur_periode]) )
				{

					// nom de la période sélectionné
					$periode_select = $periode_tab[$compteur_periode];

					// définition des donnée de $tab_donnee_sup
					$tab_donnee_sup[$i][$periode_select][$type] = '1';

					// compteur des passages
					$compteur_periode = $compteur_periode + 1;

				}

			}

		}

	}

		// si l'enregistrement sélectionner correspond ne correspond pas à la $ic classe
		$ic = $ic + 1;

		// on fait une boucle s'il reste des classes
		while ( !empty($tab_classe[$ic]) )
		{

			// on incrément le compteur du tableau général
			$i = $i + 1;

			$tab_donnee[$i]['classe'] = $tab_classe[$ic];
			$tab_donnee[$i]['ident_eleve'] = '';

			// type A ou R -- absence ou retard
			$type = 'entete_classe';

			// on incrémente le compteur de classe
			$ic = $ic + 1;

		}


	// nombre d'entrée total
	$nb_d_entree_total = $i;

	// si le compteur de classe = 0 alors on lui indique 1
	if ( $ic == 0 )
	{

		$ic = 1;

	};

	// nombre de page à créer, arrondit au nombre supérieur
	$nb_page_total = ceil( $nb_d_entree_total / $nb_ligne_parpage );


/* ******************************************** */


/* **************************** */
/*     variable invariable      */
/* **************************** */

	$gepiYear = getSettingValue('gepiYear');
	$RneEtablissement = getSettingValue("gepiSchoolRne");
	$annee_scolaire = $gepiYear;
	$datation_fichier = date("Ymd_Hi");

/* **************************** */


// définition d'une variable
	$hauteur_pris = 0;

/* début de la génération du fichier PDF */

	//création du PDF en mode Portrait, unitée de mesure en mm, de taille A4
	$pdf=new FPDF('p', 'mm', 'A4');

	// compteur pour le nombre d'élève à affiché
	$nb_eleve_aff = 1;

	// si la variable $gepiSchoolName est vide alors on cherche les informations dans la base
	if ( empty($gepiSchoolName) )
	{

		$gepiSchoolName=getSettingValue('gepiSchoolName');

	}

	// création du document
	$pdf->SetCreator($gepiSchoolName);
	// auteur du document
	$pdf->SetAuthor($gepiSchoolName);
	// mots clé
	$pdf->SetKeywords('');
	// sujet du document
	$pdf->SetSubject('Bilan journalier des absences');
	// titre du document
	$pdf->SetTitle('Bilan journalier des absences');
	// méthode d'affichage du document à son ouverture
	$pdf->SetDisplayMode('fullwidth', 'single');
	// compression du document
	$pdf->SetCompression(TRUE);
	// change automatiquement de page à 5mm du bas
	$pdf->SetAutoPageBreak(TRUE, 5);


/* **************************** */
/* début de la boucle des pages */

// comptage du nombre de page traité
$nb_page_traite = 0;

// initialiser la variable compteur de ligne passé pour le tableau
$nb_ligne_passe = 0;

// initialiser un compteur temporaire autre que i
// il serviras pour savoir à quelle endroit de la liste nous somme rendus
$j = 0;

// boucle des page
while ($nb_page_traite < $nb_page_total)
{

	// ajout de l'initialisation d'une nouvelle page dans le document
	$pdf->AddPage();

	// police de caractère utilisé
	$pdf->SetFont('DejaVu');

/* ENTETE - DEBUT */

	//bloc identification etablissement
	$logo = '../../images/'.getSettingValue('logo_etab');
	$format_du_logo = str_replace('.','',strstr(getSettingValue('logo_etab'), '.'));

	if($affiche_logo_etab==='1' and file_exists($logo) and getSettingValue('logo_etab') != '' and ($format_du_logo==='jpg' or $format_du_logo==='png'))
	{

		$valeur=redimensionne_image_logo($logo, $L_max_logo, $H_max_logo);
		$X_logo = 5;
		$Y_logo = 5;
		$L_logo = $valeur[0];
		$H_logo = $valeur[1];
		$X_etab = $X_logo + $L_logo + 1;
		$Y_etab = $Y_logo;

		if ( !isset($centrage_logo) or empty($centrage_logo) )
		{

			$centrage_logo = '0';

		}
		if ( $centrage_logo === '1' )
		{

			// centrage du logo
			$centre_du_logo = ( $H_logo / 2 );
			$Y_logo = $Y_centre_logo - $centre_du_logo;

		}

		//logo
		$pdf->Image($logo, $X_logo, $Y_logo, $L_logo, $H_logo);

	}

	//adresse
	if ( !isset($X_etab) or empty($X_etab) )
	{

		$X_etab = '5';
		$Y_etab = '5';

	}
	$pdf->SetXY($X_etab,$Y_etab);
	$pdf->SetFont('DejaVu','',14);
	$gepiSchoolName = getSettingValue('gepiSchoolName');

	// mettre en gras le nom de l'établissement si $nom_etab_gras = 1
	if ( $nom_etab_gras === '1' )
	{

		$pdf->SetFont('DejaVu','B',14);

	}
	$pdf->Cell(90,7, ($gepiSchoolName),0,2,'');

	$pdf->SetFont('DejaVu','',10);
	$gepiSchoolAdress1 = getSettingValue('gepiSchoolAdress1');

	if ( $gepiSchoolAdress1 != '' )
	{

		$pdf->Cell(90,5, ($gepiSchoolAdress1),0,2,'');

	}
	$gepiSchoolAdress2 = getSettingValue('gepiSchoolAdress2');

	if ( $gepiSchoolAdress2 != '' )
	{

		$pdf->Cell(90,5, ($gepiSchoolAdress2),0,2,'');

	}

	$gepiSchoolZipCode = getSettingValue('gepiSchoolZipCode');
	$gepiSchoolCity = getSettingValue('gepiSchoolCity');
	$pdf->Cell(90,5, ($gepiSchoolZipCode." ".$gepiSchoolCity),0,2,'');
	$gepiSchoolTel = getSettingValue('gepiSchoolTel');
	$gepiSchoolFax = getSettingValue('gepiSchoolFax');

	$passealaligne = '0';
	// entête téléphone
			// emplacement du cadre télécome
			$x_telecom = $pdf->GetX();
			$y_telecom = $pdf->GetY();

	if( $entente_tel==='1' )
	{

		$grandeur = '';
		$text_tel = '';

		if ( $tel_image != '' )
		{

			$a = $pdf->GetX();
			$b = $pdf->GetY();
			$ima = '../../images/imabulle/'.$tel_image.'.jpg';
			$valeurima=redimensionne_image($ima, 15, 15);
			$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
			$text_tel = '      '.$gepiSchoolTel;
			$grandeur = $pdf->GetStringWidth($text_tel);
			$grandeur = $grandeur + 2;

		}
		if ( $tel_texte != '' and $tel_image === '' )
		{

			$text_tel = $tel_texte.''.$gepiSchoolTel;
			$grandeur = $pdf->GetStringWidth($text_tel);

		}

		$pdf->Cell($grandeur,5, $text_tel,0,$passealaligne,'');

	}

	$passealaligne = '2';
	// entête fax
	if( $entente_fax==='1' )
	{

		$text_fax = '';

		if ( $fax_image != '' )
		{

			$a = $pdf->GetX();
			$b = $pdf->GetY();
			$ima = '../../images/imabulle/'.$fax_image.'.jpg';
			$valeurima=redimensionne_image($ima, 15, 15);
			$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
			$text_fax = '      '.$gepiSchoolFax;

		}
		if ( $fax_texte != '' and $fax_image === '' )
		{

			$text_fax = $fax_texte.''.$gepiSchoolFax;

		}
		$pdf->Cell(90,5, $text_fax,0,$passealaligne,'');

	}


	if($entente_mel==='1')
	{

		$text_mel = '';
		$y_telecom = $y_telecom + 5;
		$pdf->SetXY($x_telecom,$y_telecom);
		$gepiSchoolEmail = getSettingValue('gepiSchoolEmail');
		$text_mel = $gepiSchoolEmail;

		if ( $courrier_image != '' )
		{

			$a = $pdf->GetX();
			$b = $pdf->GetY();
			$ima = '../../images/imabulle/'.$courrier_image.'.jpg';
			$valeurima=redimensionne_image($ima, 15, 15);
			$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
			$text_mel = '      '.$gepiSchoolEmail;

		}
		if ( $courrier_texte != '' and $courrier_image === '' )
		{

			$text_mel = $courrier_texte.' '.$gepiSchoolEmail;

		}
		$pdf->Cell(90,5, $text_mel,0,2,'');

	}

/* ENTETE - FIN */


/* ENTETE TITRE - DEBUT */

		$pdf->SetFont('DejaVu','B',18);

		$pdf->SetXY(85, 10);

		$pdf->Cell(120, 6, 'Bilan journalier des absences', 0, 1, 'C');

		$pdf->SetFont('DejaVu','',12);

		$pdf->SetX(85);
		$pdf->Cell(120, 8, 'du '.$date_choisie, 0, 1, 'C');

		$pdf->SetX(85);
		$pdf->Cell(120, 5, ('année'), 0, 1, 'C');

		$pdf->SetX(85);
		$pdf->Cell(120, 5, ($annee_scolaire), 0, 1, 'C');


/* ENTETE TITRE - FIN */


/* ENTETE TABLEAU - DEBUT */

	//Sélection de la police
	$pdf->SetFont('DejaVu', 'B', 10);

	// placement du point de commencement du tableau
	$pdf->SetXY($x_tab, $y_tab);

	// Cellule entête classe
	$pdf->Cell($lar_col_classe, $hau_entete, 'Classe', 1, 0, 'C');

	// Cellule identité
	$pdf->Cell($lar_col_eleve, $hau_entete, ('Nom Prénom'), 1, 0, 'C');

	// Cellule créneaux
	// un divisie l'espace réserver à la colonne des créneaux par le nombre de créneaux
	$largeur_1_creneau = $lar_col_creneaux / $nb_creneaux;

	//compteur temporaire pour la boucle ci-dessous
	$i = 0;

	// boucle des cellule de créneau
	while ( $nb_creneaux > $i )
	{

		$pdf->Cell($largeur_1_creneau, $hau_entete, $creneaux[$i], 1, 0, 'C');
		$i = $i + 1;

	}

	// variable qui contient le point Y suivant pour la ligne suivante
	$y_dernier = $y_tab + $hau_entete;


/* ENTETE TABLEAU - FIN */



	/* ***************************************** */
	/* début de la boucle du tableau des données */

	// initialisation des classe passé
	$classe_pass = '';

	// initialiser la variable compteur de ligne pour la page actuel
	$nb_ligne_passe_reel = 0;

	// tant qu'on a pas atteind le nombre de ligne maximum par page on fait la boucle
	while ( $nb_ligne_passe_reel <= $nb_ligne_parpage )
	{

	/* TABLEAU DONNEES - DEBUT */

		// s'il reste des données alors on les affiches
		if ( !empty($tab_donnee[$nb_ligne_passe]) )
		{

			// Si c'est un typdes $tab_donnee[$nb_ligne_passe]['ident_eleve'] vide
			// alors on n'affiche l'entête de la classe
			if ( $tab_donnee[$nb_ligne_passe]['ident_eleve'] === '' )
			{

				// initialisation du point X et Y de la ligne du nom des classes
				$pdf->SetXY($x_tab, $y_dernier);

				$pdf->Cell($lar_total_tableau, $hau_donnee, ($tab_donnee[$nb_ligne_passe]['classe']), 0, 1, '');
				$classe_pass = $tab_donnee[$nb_ligne_passe]['classe'];

				// variable qui contient le point Y suivant pour la ligne suivante
				$y_dernier = $y_dernier + $hau_donnee;

				// on incrémente le nombre de ligne passé sur la page
				$nb_ligne_passe_reel = $nb_ligne_passe_reel + 1;

			}
			else
			{

				// initialisation du point X et Y de la ligne des données
				$pdf->SetXY($x_tab, $y_dernier);

				// colonne vide pour le décalage des classes
				$pdf->Cell($lar_col_classe, $hau_donnee, '', 0, 0, '');

				// colonne du nom et prénom de l'élève
				$pdf->Cell($lar_col_eleve, $hau_donnee, ($tab_donnee[$nb_ligne_passe]['ident_eleve']), 1, 0, '');

				// variable qui contient le point Y suivant pour la ligne suivante
				$y_dernier = $y_dernier + $hau_donnee;

				// compteur temporaire pour la boucle ci-dessous
				$k = 0;

				// passage des creneaux en revus
				while ( $nb_creneaux > $k )
				{

					// nom du creneau sur lequelle nous travaillons actuellement
					$nom_creneau = $creneaux[$k];

					if ( !empty($tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['A']) and $tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['A'] === '1' and empty($tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['R']))
					{

						// si la couleur à était demandé alors on l'initialise
						if ( $couleur_fond === '1' )
						{

							// couleur de caractère
							$pdf->SetTextColor(255, 0, 0);
							// couleur du fond de cellule
							$pdf->SetFillColor(255, 223, 223);

						}

						// construction de la cellule du tableau
						$pdf->Cell($largeur_1_creneau, $hau_donnee, 'A', 1, 0, 'C', $couleur_fond);

						// remise de la couleur du caractère à noir
						$pdf->SetTextColor(0, 0, 0);

					}
					elseif ( !empty($tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['R']) and $tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['R'] === '1'  and empty($tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['A']) )
					{

						// si la couleur à était demandé alors on l'initialise
						if ( $couleur_fond === '1' )
						{

							// couleur de caractère
							$pdf->SetTextColor(33, 223, 0);
							// couleur du fond de cellule
							$pdf->SetFillColor(228, 255, 223);

						}

						// construction de la cellule du tableau
						$pdf->Cell($largeur_1_creneau, $hau_donnee, 'R', 1, 0, 'C', $couleur_fond);

						// remise de la couleur du caractère à noir
						$pdf->SetTextColor(0, 0, 0);

					}
					elseif ( !empty($tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['R']) and $tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['R'] === '1'  and !empty($tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['A']) and $tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['A'] === '1' )
					{

						// si la couleur à était demandé alors on l'initialise
						if ( $couleur_fond === '1' )
						{

							// couleur de caractère
							$pdf->SetTextColor(255, 0, 0);
							// couleur du fond de cellule
							$pdf->SetFillColor(255, 223, 223);

						}

						// construction de la cellule du tableau pour l'absence
						$pdf->Cell($largeur_1_creneau/2, $hau_donnee, 'A', 1, 0, 'C', $couleur_fond);

						// si la couleur à était demandé alors on l'initialise
						if ( $couleur_fond === '1' )
						{

							// couleur de caractère
							$pdf->SetTextColor(33, 223, 0);
							// couleur du fond de cellule
							$pdf->SetFillColor(228, 255, 223);

						}

						// construction de la cellule du tableau pour le retard
						$pdf->Cell($largeur_1_creneau/2, $hau_donnee, 'R', 1, 0, 'C', $couleur_fond);

						// remise de la couleur du caractère à noir
						$pdf->SetTextColor(0, 0, 0);

					}
					else
					{

						$pdf->Cell($largeur_1_creneau, $hau_donnee, '', 1, 0, 'C');

					}

					// compteur de passage pour les créneaux
					$k = $k + 1;

				}

				// on incrémente le nombre de ligne passé sur la page
				$nb_ligne_passe_reel = $nb_ligne_passe_reel + 1;

			}

				// on incrémente le nombre de ligne traité dans le tableau des données
				$nb_ligne_passe = $nb_ligne_passe + 1;

		}
		else
		{

			// s'il n'y a plus de donnée à afficher alors on lui dit que le
			// maximum de ligne à était atteint pour qu'il termine la boucle
			$nb_ligne_passe_reel = $nb_ligne_parpage + 1;

		}

	/* TABLEAU DONNEES - FIN */

	}
	/* fin de la boucle du tableau des données */
	/* *************************************** */

/* PIED DE PAGE - DEBUT */

	//Positionnement à 1 cm du bas et 0,5cm + 0,5cm du coté gauche
	$pdf->SetXY(5,-10);

	//Police DejaVu Gras 6
	$pdf->SetFont('DejaVu','B',8);

	// formule du pied de page
	$fomule = 'Bilan journalier du ' . date("d/m/Y H:i:s") . ' - page ' . $nb_page_traite . '/' . $nb_page_total;

	// cellule de pied de page
	$pdf->Cell(0, 4.5, $fomule, 0, 0, 'C');

/* PIED DE PAGE - FIN */

	// on incrément le nombre d'entrée passé
	$nb_d_entree_passe = $nb_ligne_passe;

	// on incrément le nombre de page traité
	$nb_page_traite = $nb_page_traite + 1;
}
/* fin de la boucle des pages */
/* ************************** */

// fermeture du fichier pdf et lecture dans le navigateur 'nom', 'I/D'

	$pref_output_mode_pdf=get_output_mode_pdf();

	// génération du nom du document
	$nom_fichier = 'bilan_journalier_'.$datation_fichier.'.pdf';

	// génération du document
	$pdf->Output($nom_fichier,$pref_output_mode_pdf);

?>
