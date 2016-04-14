<?php
/*
 *
 * Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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
 
// Global configuration file
// Quand on est en SSL, IE n'arrive pas à ouvrir le PDF.
//Le problème peut être résolu en ajoutant la ligne suivante :

//=============================
// Initialisations files
require_once("../lib/initialisations.inc.php");
//=============================

// Resume session
$resultat_session = $session_gepi->security_check();

if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_orientation/export_pdf.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_orientation/export_pdf.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Export PDF des voeux et orientations proposées',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if (!defined('FPDF_VERSION')) {
	require_once('../fpdf/fpdf.php');
}

define('LargeurPage','210');
define('HauteurPage','297');

require_once("../impression/class_pdf.php");
//require_once ("./liste.inc.php"); //fonction qui retourne le nombre d'élèves par classe (ou groupe) pour une période donnée.

// Lorsque qu'on utilise une session PHP, parfois, IE n'affiche pas le PDF
// C'est un problème qui affecte certaines versions d'IE.
// Pour le contourner, on ajoutez la ligne suivante avant session_start() :
session_cache_limiter('private');

//debug_var();

// LES OPTIONS DEBUT

$MargeHaut=getPref($_SESSION['login'],'orientation_pdf_marge_gauche',10);
$MargeDroite=getPref($_SESSION['login'],'orientation_pdf_marge_droite',10);
$MargeGauche=getPref($_SESSION['login'],'orientation_pdf_marge_gauche',10);
$MargeBas=getPref($_SESSION['login'],'orientation_pdf_marge_bas',10);
$avec_reliure=getPref($_SESSION['login'],'orientation_pdf_marge_reliure',1);
$avec_emplacement_trous=getPref($_SESSION['login'],'orientation_pdf_avec_emplacement_trous',1);

//Gestion de la marge à gauche pour une reliure éventuelle ou des feuilles perforées.
if ($avec_reliure==1) {
  if ($MargeGauche < 18) {$MargeGauche = 18;}
}

//Calcul de la Zone disponible
$EspaceX = LargeurPage - $MargeDroite - $MargeGauche ;
$EspaceY = HauteurPage - $MargeHaut - $MargeBas;
$X_tableau = $MargeGauche;

//entête classe et année scolaire
$L_entete_classe = 65;
$H_entete_classe = 14;
$X_entete_classe = $EspaceX - $L_entete_classe + $MargeGauche;
$Y_entete_classe = $MargeHaut;

$X_entete_matiere = $MargeGauche;
$Y_entete_matiere = $MargeHaut;
$L_entete_discipline = 65;
$H_entete_discipline = 14;

// LES OPTIONS suite
$h_cell=getPref($_SESSION['login'],'orientation_pdf_h_ligne',8);
$l_cell_nom=getPref($_SESSION['login'],'orientation_pdf_l_nomprenom',40);
$l_cell_voeux=getPref($_SESSION['login'],'orientation_pdf_l_voeux',70);
$option_affiche_pp=getPref($_SESSION['login'],'orientation_pdf_affiche_pp',1);
$option_tout_une_page=getPref($_SESSION['login'],'orientation_pdf_une_seule_page',1);

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : "voeux_et_orientations");
if($mode=="voeux") {
	$ligne_texte = "Voeux d'orientation" ;
}
elseif($mode=="orientations") {
	$ligne_texte = "Orientations proposées" ;
}
else {
	$ligne_texte = "Voeux et orientations proposées" ;
}
$texte = '';

// Définition de la page

$pdf=new rel_PDF("P","mm","A4");
$pdf->SetTopMargin($MargeHaut);
$pdf->SetRightMargin($MargeDroite);
$pdf->SetLeftMargin($MargeGauche);
$pdf->SetAutoPageBreak(true, $MargeBas);

//On recupère les variables pour l'affichage et on traite leur existence.
$id_classe=isset($_POST['id_classe']) ? $_POST["id_classe"] : (isset($_GET['id_classe']) ? $_GET["id_classe"] : NULL);

if((!isset($id_classe))||(!preg_match("/^[0-9]{1,}$/", $id_classe))) {
	header("Location: index.php?msg=Classe non valide");
	die();
}

$classe=get_nom_classe($id_classe);
if((!$classe)||($classe=='')) {
	header("Location: index.php?msg=Classe non valide");
	die();
}

/*
//On recupère les variables pour l'affichage
// DE  IMPRIME_SERIE.PHP
// les tableaux contienent la liste des id.
$id_liste_classes=isset($_POST['id_liste_classes']) ? $_POST["id_liste_classes"] : NULL;
*/

$nb_pages = 0;
$nb_eleves = 0;

// DEFINIR LE NOMBRE DE BOUCLES A FAIRE
// Impressions RAPIDES
if ($id_classe!=NULL) { // C'est une classe
	$nb_pages=1;
} //fin c'est une classe

/*
//IMPRESSION A LA CHAINE
if ($id_liste_classes!=NULL) {
    $nb_pages = sizeof($id_liste_classes);
//echo $nb_pages;
}
*/

//echo " ".$nb_pages;

$tab_orientation_classe_courante=get_tab_voeux_orientations_classe($id_classe);
$affiche='n';
if ($affiche=='y') {
	echo "<pre>";
	print_r($tab_orientation_classe_courante);
	echo "</pre>";
	//die();
}

// Cette boucle crée les différentes pages du PDF (page = un entête et des lignes par élèves.
for ($i_pdf=0; $i_pdf<$nb_pages ; $i_pdf++) {

	$nb_eleves=0;
	// Impressions RAPIDES
	$donnees_eleves=array();
	// traite_donnees_classe($id_classe,$id_liste_periodes,$nb_eleves);

	//include("../lib/periodes.inc.php");
	require("../lib/periodes.inc.php");
	$sql="SELECT e.login, e.nom, e.prenom, e.mef_code, e.id_eleve FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='$id_classe' AND jec.periode='".($nb_periode-1)."' AND (e.date_sortie IS NULL OR e.date_sortie='0000-00-00 00:00:00' OR e.date_sortie>'".strftime("%Y-%m-%d %H:%M:%S")."') ORDER BY e.nom, e.prenom;";
	//echo "$sql<br />";
	$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ele)==0) {
		header("Location: index.php?msg=Aucun élève dans la classe de&nbsp;: ".get_nom_classe($id_classe));
		die();
	}

	$cpt=0;
	while($lig_ele=mysqli_fetch_assoc($res_ele)) {
		$donnees_eleves[$cpt]=$lig_ele;
		$cpt++;
	}
	$nb_eleves=count($donnees_eleves);

	/*
	//IMPRESSION A LA CHAINE
	if ($id_liste_classes!=NULL) {
		$donnees_eleves = traite_donnees_classe($id_liste_classes[$i_pdf],$id_liste_periodes,$nb_eleves);
		$id_classe=$id_liste_classes[$i_pdf];
		//$id_classe=$donnees_eleves[0]['id_classe'];
	}
	*/

	//Info pour le debug.
	$affiche='n';
	if ($affiche=='y') {
		echo "<pre>";
		print_r($donnees_eleves);
		echo "</pre>";
		//die();
	}

	// CALCUL de VARIABLES
	// Calcul de la hauteur de la ligne dans le cas de l'option tout sur une ligne
	if ($option_tout_une_page == 1) {
		$hauteur_disponible = HauteurPage - $MargeHaut - $MargeBas - $H_entete_classe - 5 - 2.5; //2.5 ==> avant le pied de page

		$hauteur_disponible = $hauteur_disponible - 14.5;
		
		// le nombre de lignes demandées.
		//$nb_ligne_demande = $nb_eleves;
		// Pour tenir compte de la ligne de synthèse
		$nb_ligne_demande = ($nb_eleves+1);

		$h_cell = $hauteur_disponible / $nb_ligne_demande ;

		/*
		$f=fopen("/tmp/debug_orientation_pdf.txt","w+");
		fwrite($f, "\$hauteur_disponible=$hauteur_disponible\n");
		fwrite($f, "\$nb_ligne_demande=$nb_ligne_demande\n");
		fwrite($f, "\$h_cell=$h_cell\n");
		fclose($f);
		*/
	}

	$pdf->AddPage("P");
	// Couleur des traits
	$pdf->SetDrawColor(0,0,0);

	// caractère utilisé dans le document
	$caractere_utilise = 'DejaVu';

	// on appelle une nouvelle page pdf
	$nb_eleves_i = 0;

	//Entête du PDF
	$pdf->SetLineWidth(0.7);
	$pdf->SetFont('DejaVu','B',14);
	$pdf->Setxy($X_entete_classe,$Y_entete_classe);

	$current_classe=get_nom_classe($id_classe);

	if (($option_affiche_pp==1)) {
		$pdf->CellFitScale($L_entete_classe,$H_entete_classe / 2,'Classe de '.$current_classe,'LTR',2,'C');
		$pdf->SetFont('DejaVu','I',8.5);

		//PP de la classe
		if ($id_groupe != NULL) {
			$id_classe=$donnees_eleves['id_classe'][0];
		}
		// On récupère le PP du premier élève de la classe... si c'est un nouvel arrivant avec oubli de saisie du PP, on aura une info erronée.
		// Si il y a plusieurs PP dans la classe, on n'aura qu'un seul des PP.
		//$sql = "SELECT professeur FROM j_eleves_professeurs WHERE (login = '".$donnees_eleves['login'][0]."' and id_classe='$id_classe')";
		$sql = "SELECT professeur FROM j_eleves_professeurs WHERE (login = '".$donnees_eleves[0]['login']."' and id_classe='$id_classe')";
		//echo "$sql<br />\n";
		$call_profsuivi_eleve = mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($call_profsuivi_eleve)==0) {
			$current_eleve_profsuivi_login="";
			$current_eleve_profsuivi_identite="- Aucun -";
		}
		else {
			$lig_current_eleve_profsuivi=mysqli_fetch_object($call_profsuivi_eleve);
			$current_eleve_profsuivi_login=$lig_current_eleve_profsuivi->professeur;
			$current_eleve_profsuivi_identite=affiche_utilisateur($current_eleve_profsuivi_login,$id_classe);
		}

		$gepi_prof_suivi=getParamClasse($id_classe, 'gepi_prof_suivi', getSettingValue('gepi_prof_suivi'));
		$pdf->CellFitScale($L_entete_classe,$H_entete_classe / 2,casse_mot($gepi_prof_suivi,'majf2').' : '.$current_eleve_profsuivi_identite,'LRB',0,'L'); //'Année scolaire '.getSettingValue('gepiYear')
	} else {
		$pdf->CellFitScale($L_entete_classe,$H_entete_classe,'Classe de '.$current_classe,'LTRB',2,'C');
	}

	$pdf->Setxy($X_entete_matiere,$Y_entete_matiere);
	$pdf->SetFont('DejaVu','B',14);


	// On demande une classe ==> on ajoute la période.
	$pdf->SetFont('DejaVu','I',11);
	$pdf->Cell($L_entete_classe,$H_entete_classe ,'Année scolaire '.getSettingValue('gepiYear'),'LTRB',2,'C');

	$Y_courant=$pdf->GetY()+2.5;
	$pdf->Setxy($MargeGauche,$Y_courant);

	//La ligne de texte après les entêtes
	$pdf->CellFitScale(0,10,$ligne_texte,'',2,'C');
	$Y_courant=$pdf->GetY()+2.5;

	// requete à faire pour récupérer les Orientations pour la classe
	//debut tableau;
	$pdf->SetLineWidth(0.3);
	$pdf->SetFont('DejaVu','',9);
	$y_tmp = $Y_courant;
	$y_tmp_ini = $y_tmp;

	// Le tableau

	// Haut du tableau pour la premiere page de la classe (tenant compte de l'entete)
	$y_top_tableau=$y_tmp;

	$X_nom_prenom=$X_tableau;

	// Largeur de la colonne Orientations:
	if($mode=="voeux") {
		$l_cell_voeux=$EspaceX-$l_cell_nom;
		$X_voeux=$X_tableau+$l_cell_nom;
	}
	elseif($mode=="orientations") {
		$l_cell_orientations=$EspaceX-$l_cell_nom;
		$X_orientations=$X_tableau+$l_cell_nom;
	}
	else {
		$l_cell_orientations=$EspaceX-$l_cell_nom-$l_cell_voeux;
		$X_voeux=$X_tableau+$l_cell_nom;
		$X_orientations=$X_tableau+$l_cell_nom+$l_cell_voeux;
	}

	// Boucle sur les eleves de la classe courante:
	$compteur_eleves_page=0;
	while($nb_eleves_i < $nb_eleves) {
		if(strtr($y_tmp,",",".")+strtr($h_cell,",",".")>297-$MargeBas-$MargeHaut-2) {
			/*
			$f=fopen("/tmp/debug_orientation_pdf.txt","a+");
			fwrite($f, "\$y_tmp+\$h_cell=$y_tmp+$h_cell=".(strtr($y_tmp,",",".")+strtr($h_cell,",","."))."\n");
			fwrite($f, "297-\$MargeBas-\$MargeHaut-5=".(297-$MargeBas-$MargeHaut-5)."\n");
			fclose($f);
			*/

			// Haut du tableau pour la deuxieme, troisieme,... page de la classe
			// Pour la deuxieme, troisieme,... page d'une classe, on n'a pas d'entete:
			$y_top_tableau=$MargeHaut;

			$pdf->AddPage("P");
			$pdf->Setxy($X_tableau,$y_top_tableau);
			$compteur_eleves_page=0;
		}

		// Ordonnee courante pour l'eleve n°$compteur_eleves_page de la page:
		$y_tmp = $y_top_tableau+$compteur_eleves_page*$h_cell;

		// Colonne Nom_Prenom
		$pdf->SetXY($X_nom_prenom,$y_tmp);
		$pdf->SetFont('DejaVu','B',9);		
		$texte = my_strtoupper($donnees_eleves[$nb_eleves_i]['nom'])." ".casse_mot($donnees_eleves[$nb_eleves_i]['prenom'],'majf2');

		$taille_max_police=9;
		$taille_min_police=ceil($taille_max_police/3);
		$largeur_dispo=$l_cell_nom;
		//$info_debug=$y_tmp;
		$info_debug="";
		//cell_ajustee("<b>".$texte."</b>".$info_debug,$pdf->GetX(),$y_tmp,$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'LRBT');
		cell_ajustee("<b>".$texte."</b>".$info_debug,$X_nom_prenom,$y_tmp,$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'LRBT');

		//================================
		// Colonne Voeux:
		if(($mode=="voeux")||($mode=="voeux_et_orientations")) {
			// On reforce l'ordonnee pour la colonne Voeux
			$y_tmp = $y_top_tableau+$compteur_eleves_page*$h_cell;

			$pdf->Setxy($X_voeux,$y_tmp);
			$pdf->SetFont('DejaVu','',7.5);

			$texte_voeux="";
			if(isset($tab_orientation_classe_courante['voeux'][$donnees_eleves[$nb_eleves_i]['login']])) {
				$texte_voeux=get_liste_voeux_orientation($donnees_eleves[$nb_eleves_i]['login'], "pdf_cell_ajustee");
			}

			$hauteur_caractere_appreciation=9;
			$taille_max_police=$hauteur_caractere_appreciation;
			$taille_min_police=ceil($taille_max_police/3);
			$largeur_dispo=$l_cell_voeux;
			//cell_ajustee($avis,$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'LRBT');
			cell_ajustee($texte_voeux,$pdf->GetX(),$y_tmp,$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'LRBT');
		}
		//================================
		// Colonne Orientations:
		if(($mode=="orientations")||($mode=="voeux_et_orientations")) {
			// On reforce l'ordonnee pour la colonne Voeux
			$y_tmp = $y_top_tableau+$compteur_eleves_page*$h_cell;

			$pdf->Setxy($X_orientations,$y_tmp);
			$pdf->SetFont('DejaVu','',7.5);

			$texte_orientations_proposees="";
			if(isset($tab_orientation_classe_courante['voeux'][$donnees_eleves[$nb_eleves_i]['login']])) {
				$texte_orientations_proposees=get_liste_orientations_proposees($donnees_eleves[$nb_eleves_i]['login'], "pdf_cell_ajustee");
			}

			$hauteur_caractere_appreciation=9;
			$taille_max_police=$hauteur_caractere_appreciation;
			$taille_min_police=ceil($taille_max_police/3);
			$largeur_dispo=$l_cell_orientations;
			//cell_ajustee($avis,$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'LRBT');
			cell_ajustee($texte_orientations_proposees,$pdf->GetX(),$y_tmp,$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'LRBT');
		}

		$pdf->SetFont('DejaVu','',7.5);

		//$pdf->Setxy($X_tableau+$l_cell_nom,$y_tmp+$h_cell);

		$nb_eleves_i++;
		$compteur_eleves_page++;
	}
	$y_tmp = $pdf->GetY();

} // FOR (boucle classe)

$pref_output_mode_pdf=get_output_mode_pdf();

// sortie PDF sur écran
$nom_releve=remplace_accents($current_classe, "all")."_".date("Ymd_Hi");
if($mode=="voeux") {
	$nom_releve = 'Voeux_d_orientation_'.$nom_releve.'.pdf';
}
elseif($mode=="orientations") {
	$nom_releve = 'Orientations_proposees_'.$nom_releve.'.pdf';
}
else {
	$nom_releve = 'Voeux_et_Orientations_proposees_'.$nom_releve.'.pdf';
}
$pdf->Output($nom_releve,$pref_output_mode_pdf);
?>
