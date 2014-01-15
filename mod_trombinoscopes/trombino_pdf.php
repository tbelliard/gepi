<?php
/*
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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
require_once("../lib/initialisations.inc.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);


// Resume session

$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_trombinoscopes/trombino_pdf.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/mod_trombinoscopes/trombino_pdf.php',
	administrateur='V',
	professeur='V',
	cpe='V',
	scolarite='V',
	eleve='F',
	responsable='F',
	secours='F',
	autre='V',
	description='Trombinoscopes PDF',
	statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


if (empty($_GET['page']) and empty($_POST['page'])) { $page = ''; }
	else { if (isset($_GET['page'])) {$page=$_GET['page'];} if (isset($_POST['page'])) {$page=$_POST['page'];} }
if (empty($_GET['id'])) { $id = ''; } else { $id=$_GET['id']; }

if (empty($_GET['classe']) and empty($_POST['classe'])) { $classe = ''; }
else { if (isset($_GET['classe'])) { $classe = $_GET['classe']; } if (isset($_POST['classe'])) { $classe = $_POST['classe']; } }
if (empty($_GET['groupe']) and empty($_POST['groupe'])) { $groupe = ''; }
else { if (isset($_GET['groupe'])) { $groupe = $_GET['groupe']; } if (isset($_POST['groupe'])) { $groupe = $_POST['groupe']; } }

$aid = isset($_POST['aid']) ? $_POST['aid'] : ( isset($_GET['aid']) ? $_GET['aid'] : '' );

if (empty($_GET['equipepeda']) and empty($_POST['equipepeda'])) { $equipepeda = ''; }
else { if (isset($_GET['equipepeda'])) { $equipepeda = $_GET['equipepeda']; } if (isset($_POST['equipepeda'])) { $equipepeda = $_POST['equipepeda']; } }
if (empty($_GET['discipline']) and empty($_POST['discipline'])) { $discipline = ''; }
else { if (isset($_GET['discipline'])) { $discipline = $_GET['discipline']; } if (isset($_POST['discipline'])) { $discipline = $_POST['discipline']; } }
if (empty($_GET['statusgepi']) and empty($_POST['statusgepi'])) { $statusgepi = ''; }
else { if (isset($_GET['statusgepi'])) { $statusgepi = $_GET['statusgepi']; } if (isset($_POST['statusgepi'])) { $statusgepi = $_POST['statusgepi']; } }
if (empty($_GET['affdiscipline']) and empty($_POST['affdiscipline'])) { $affdiscipline = ''; }
else { if (isset($_GET['affdiscipline'])) { $affdiscipline = $_GET['affdiscipline']; } if (isset($_POST['affdiscipline'])) { $affdiscipline = $_POST['affdiscipline']; } }



if ( $classe != 'toutes' and $groupe != 'toutes' and $discipline != 'toutes' and $equipepeda != 'toutes' and ( $classe != '' or $groupe != '' or $aid != '' or $equipepeda != '' or $discipline != '' or $statusgepi != '' ) ) {
	// on regarde ce qui a été choisi
	// c'est une classe
	if ( $classe != '' and $groupe === '' and $equipepeda === '' and $discipline === '' and $statusgepi === '' ) { $action_affiche = 'classe'; }
	// c'est un groupe
	if ( $classe === '' and $groupe != '' and $equipepeda === '' and $discipline === '' and $statusgepi === '' ) { $action_affiche = 'groupe'; }
	// c'est un aid
	if ( $classe === '' and $groupe === '' and $aid != '' and $equipepeda === '' and $discipline === '' and $statusgepi === '' ) { $action_affiche = 'aid'; }
	// c'est une équipe pédagogique
	if ( $classe === '' and $groupe === '' and $equipepeda != '' and $discipline === '' and $statusgepi === '' ) { $action_affiche = 'equipepeda'; }
	// c'est une discipline
	if ( $classe === '' and $groupe === '' and $equipepeda === '' and $discipline != '' and $statusgepi === '' ) { $action_affiche = 'discipline'; }
	// c'est un statut de gepi
	if ( $classe === '' and $groupe === '' and $equipepeda === '' and $discipline === '' and $statusgepi != '' ) { $action_affiche = 'statusgepi'; }

	if ( $action_affiche === 'classe' ) { $requete_qui = 'SELECT c.id, c.nom_complet, c.classe FROM '.$prefix_base.'classes c WHERE c.id = "'.$classe.'"'; }
	if ( $action_affiche === 'groupe' ) { $requete_qui = 'SELECT g.id, g.name FROM '.$prefix_base.'groupes g WHERE g.id = "'.$groupe.'"'; }
	if ( $action_affiche === 'aid' ) { $requete_qui = "SELECT id , nom FROM aid WHERE id = '".$aid."'"; }
	if ( $action_affiche === 'equipepeda' ) { $requete_qui = 'SELECT c.id, c.nom_complet, c.classe FROM '.$prefix_base.'classes c WHERE c.id = "'.$equipepeda.'"'; }
	if ( $action_affiche === 'discipline' ) { $requete_qui = 'SELECT m.matiere, m.nom_complet FROM '.$prefix_base.'matieres m WHERE m.matiere = "'.$discipline.'"'; }

	//if ( $action_affiche === 'statusgepi' ) { $requete_qui = 'SELECT statut FROM '.$prefix_base.'utilisateurs u WHERE u.statut = "'.$statusgepi.'"'; }
	if ( $action_affiche === 'statusgepi' ) { $requete_qui = 'SELECT statut FROM '.$prefix_base.'utilisateurs u WHERE u.statut = "'.$statusgepi.'" AND etat="actif";'; }

	$execute_qui = mysqli_query($GLOBALS["mysqli"], $requete_qui) or die('Erreur SQL !'.$requete_qui.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$donnees_qui = mysqli_fetch_array($execute_qui) or die('Erreur SQL !'.$execute_qui.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));


	if ( $action_affiche === 'classe' ) { $entete = "Classe : ".$donnees_qui['nom_complet']." (".$donnees_qui['classe'].")";}
	if ( $action_affiche === 'groupe' ) {
		//$entete = "Groupe : ".$donnees_qui['name'];
		$current_group=get_group($groupe);
		$entete = "Groupe : ".$donnees_qui['name']." (".$current_group['classlist_string'].")";
	}
	if ( $action_affiche === 'aid' ) {$entete = "AID : ".$donnees_qui['nom'];}
	if ( $action_affiche === 'equipepeda' ) { $entete = "Équipe pédagogique : ".$donnees_qui['nom_complet']." (".$donnees_qui['classe'].")"; }
	if ( $action_affiche === 'discipline' ) { $entete = "Discipline : ".$donnees_qui['nom_complet']." (".$donnees_qui['matiere'].")"; }
	if ( $action_affiche === 'statusgepi' ) { $entete = "Statut : ".my_ereg_replace("scolarite","scolarité",$statusgepi); }


	// choix du répertoire ou chercher les photos entre professeur ou élève
	if ( $action_affiche === 'classe' ) { $repertoire = 'eleves'; }
	if ( $action_affiche === 'groupe' ) { $repertoire = 'eleves'; }
	if ( $action_affiche === 'aid' ) { $repertoire = 'eleves'; }
	if ( $action_affiche === 'equipepeda' ) { $repertoire = 'personnels'; }
	if ( $action_affiche === 'discipline' ) { $repertoire = 'personnels'; }
	if ( $action_affiche === 'statusgepi' ) { $repertoire = 'personnels'; }

	//je recherche les personnes concernées pour la sélection effectuée
	// élève d'une classe
	if ( $action_affiche === 'classe' ) { 
		$requete_trombi = "SELECT e.login, e.nom, e.prenom, e.elenoet, jec.login, jec.id_classe, jec.periode, c.classe, c.id, c.nom_complet
									FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c
									WHERE e.login = jec.login
									AND jec.id_classe = c.id
									AND id = '".$classe."'
									AND (e.date_sortie is NULL OR e.date_sortie NOT LIKE '20%')
									GROUP BY nom, prenom"; 
	}

	// élève d'un groupe
	if ( $action_affiche === 'groupe' ) { 
		/*
		$requete_trombi = "SELECT jeg.login, jeg.id_groupe, jeg.periode, e.login, e.nom, e.prenom, e.elenoet, g.id, g.name, g.description
								FROM ".$prefix_base."eleves e, ".$prefix_base."groupes g, ".$prefix_base."j_eleves_groupes jeg
								WHERE jeg.login = e.login
								AND jeg.id_groupe = g.id
								AND g.id = '".$groupe."'
								GROUP BY nom, prenom"; 
		*/
		if((isset($_GET['order_by']))&&($_GET['order_by']=='classe')) {
			$grp_order_by="c.classe, e.nom, e.prenom";
			$requete_trombi = "SELECT jeg.login, jeg.id_groupe, jeg.periode, e.login, e.nom, e.prenom, e.elenoet, g.id, g.name, g.description, c.classe
									FROM ".$prefix_base."eleves e, ".$prefix_base."groupes g, ".$prefix_base."j_eleves_groupes jeg, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c
									WHERE jeg.login = e.login
									AND jec.login = e.login
									AND jec.id_classe=c.id
									AND jeg.id_groupe = g.id
									AND g.id = '".$groupe."'
									AND (e.date_sortie is NULL OR e.date_sortie NOT LIKE '20%')
									GROUP BY nom, prenom
									ORDER BY $grp_order_by;";
		}
		else {
			$grp_order_by="nom, prenom";
			$requete_trombi = "SELECT jeg.login, jeg.id_groupe, jeg.periode, e.login, e.nom, e.prenom, e.elenoet, g.id, g.name, g.description, c.classe
									FROM ".$prefix_base."eleves e, ".$prefix_base."groupes g, ".$prefix_base."j_eleves_groupes jeg, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c
									WHERE jeg.login = e.login
									AND jec.login = e.login
									AND jec.id_classe=c.id
									AND jeg.id_groupe = g.id
									AND g.id = '".$groupe."'
									AND (e.date_sortie is NULL OR e.date_sortie NOT LIKE '20%')
									GROUP BY nom, prenom
									ORDER BY $grp_order_by;";
		}
	}
		
	// élève d'un AID
		if ( $action_affiche === 'aid' ) {
			if (((isset($_POST['order_by']))&&($_POST['order_by']=='classe')) || ((isset($_GET['order_by']))&&($_GET['order_by']=='classe'))) {
			 
				$grp_order_by="c.classe, e.nom, e.prenom";
				$requete_trombi = "SELECT e.login , e.nom, e.prenom , e.elenoet , a.id , a.nom nom_complet
										FROM eleves e, aid a, j_aid_eleves j , j_eleves_classes jec , classes c
										WHERE j.login = e.login
										AND  e.login = jec.login
										AND jec.id_classe = c.id
										AND j.id_aid = a.id
										AND a.id = '".$aid."'
										AND (e.date_sortie is NULL OR e.date_sortie NOT LIKE '20%')
										GROUP BY e.login , e.nom , e.prenom
										ORDER BY $grp_order_by;";	

			}
			else {
				$grp_order_by="e.nom, e.prenom";
				$requete_trombi = "SELECT e.login, e.nom, e.prenom, e.elenoet, a.id, a.nom nom_complet
										FROM eleves e , aid a , j_aid_eleves j , classes c
										WHERE j.login = e.login
										AND j.id_aid = a.id
										AND a.id = '".$aid."'
										AND (e.date_sortie is NULL OR e.date_sortie NOT LIKE '20%')
										GROUP BY e.nom, e.prenom
										ORDER BY $grp_order_by;";			
			}
		}


	// professeurs d'une équipe pédagogique
	if ( $action_affiche === 'equipepeda' ) { $requete_trombi = 'SELECT * FROM '.$prefix_base.'utilisateurs u, '.$prefix_base.'j_groupes_professeurs jgp, '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'classes c
										WHERE jgp.id_groupe = jgc.id_groupe
									AND jgc.id_classe = c.id
									AND u.login = jgp.login
										AND c.id = "'.$equipepeda.'"
										AND u.etat="actif"
										GROUP BY u.nom, u.prenom
										ORDER BY nom ASC, prenom ASC'; }

	// professeurs par discipline
	if ( $action_affiche === 'discipline' ) { $requete_trombi = 'SELECT * FROM '.$prefix_base.'utilisateurs u, '.$prefix_base.'j_professeurs_matieres jpm, '.$prefix_base.'matieres m
										WHERE u.login = jpm.id_professeur
									AND m.matiere = jpm.id_matiere
										AND m.matiere = "'.$discipline.'"
										AND u.etat="actif"
										GROUP BY u.nom, u.prenom
										ORDER BY nom ASC, prenom ASC'; }

	// par statut cpe ou professeur
	if ( $action_affiche === 'statusgepi' ) { $requete_trombi = 'SELECT * FROM '.$prefix_base.'utilisateurs u
										WHERE u.statut = "'.$statusgepi.'"
										AND u.etat="actif"
										GROUP BY u.nom, u.prenom
										ORDER BY nom ASC, prenom ASC'; }


	function matiereprof($prof, $equipepeda) {
		global $prefix_base;

		$prof_de = '';
		if ( $prof != '' ) {
			$requete_matiere = 'SELECT * FROM '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'j_groupes_matieres jgm, '.$prefix_base.'j_groupes_professeurs jgp, '.$prefix_base.'matieres m
						WHERE jgc.id_classe = "'.$equipepeda.'"
						AND jgc.id_groupe = jgp.id_groupe
						AND jgm.id_matiere = m.matiere
						AND jgp.id_groupe = jgm.id_groupe
						AND jgp.login = "'.$prof.'"';
			$execution_matiere = mysqli_query($GLOBALS["mysqli"], $requete_matiere) or die('Erreur SQL !'.$requete_matiere.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				while ($donnee_matiere = mysqli_fetch_array($execution_matiere))
				{
					//$prof_de = $prof_de."\n".$donnee_matiere['nom_complet'].' ';
					$prof_de = $prof_de." ".$donnee_matiere['nom_complet'].' ';
			}
		}
		return ($prof_de);
	}

	$execution_trombi = mysqli_query($GLOBALS["mysqli"], $requete_trombi) or die('Erreur SQL !'.$requete_trombi.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	//$cpt_photo = 1;
	$tab_classes=array();
	$cpt_photo = 0;
	while ($donnee_trombi = mysqli_fetch_array($execution_trombi))
	{
		//insertion de l'élève dans la varibale $eleve_absent
		$login_trombinoscope[$cpt_photo] = $donnee_trombi['login'];
		$nom_trombinoscope[$cpt_photo] = $donnee_trombi['nom'];
		$prenom_trombinoscope[$cpt_photo] = $donnee_trombi['prenom'];
		$classe_trombinoscope[$cpt_photo] = "";

		if ( $action_affiche === 'classe' ) { $id_photo_trombinoscope[$cpt_photo] = strtolower($donnee_trombi['elenoet']); }
		if ( $action_affiche === 'groupe' ) { 
			$id_photo_trombinoscope[$cpt_photo] = strtolower($donnee_trombi['elenoet']);
			$classe_trombinoscope[$cpt_photo] = $donnee_trombi['classe'];
			if(!in_array($donnee_trombi['classe'],$tab_classes)) {$tab_classes[]=$donnee_trombi['classe'];}
		}
		if ( $action_affiche === 'aid' ) { $id_photo_trombinoscope[$cpt_photo] = strtolower($donnee_trombi['elenoet']); }
			
		if ( $action_affiche === 'equipepeda' ) { $id_photo_trombinoscope[$cpt_photo] = $donnee_trombi['login']; }
		if ( $action_affiche === 'discipline' ) { $id_photo_trombinoscope[$cpt_photo] = $donnee_trombi['login']; }
		if ( $action_affiche === 'statusgepi' ) { $id_photo_trombinoscope[$cpt_photo] = $donnee_trombi['login']; }
	
		$matiere_prof[$cpt_photo] = '';
		if ( $action_affiche === 'equipepeda' and $affdiscipline === 'oui' ) {
			$matiere_prof[$cpt_photo] = matiereprof($login_trombinoscope[$cpt_photo], $equipepeda);
			//echo "\$nom_trombinoscope[$cpt_photo]=".$nom_trombinoscope[$cpt_photo]."<br />\n";
			//echo "\$matiere_prof[$cpt_photo]=".$matiere_prof[$cpt_photo]."<br />\n<br />\n";
		}

		$cpt_photo = $cpt_photo + 1;
	}
	$total = $cpt_photo;


	//debug_var();
	//die();
	//==========================================================================

	// Paramètres de dimensions
	include('trombino.inc.php');
	
	$gepiYear=getSettingValue('gepiYear');

	//======================================
	header('Content-Type: application/pdf');
	Header('Pragma: public');
	require('../fpdf/fpdf.php');
	
	
	define('LargeurPage',$largeur_page);
	define('HauteurPage',$hauteur_page);
	session_cache_limiter('private');
	//======================================
	class trombino_PDF extends FPDF
	//class rel_PDF extends FPDF
	{
		function Header() {
			global $MargeHaut, $MargeBas, $MargeGauche, $MargeDroite, $largeur_utile_page;
	
			$this->SetXY($MargeGauche,5);
			$this->SetFont('DejaVu','',7.5);
			$texte=getSettingValue("gepiSchoolName")."  ";
			$this->Cell($largeur_utile_page,5,$texte,0,0,'L');
	
			
			$texte=strftime("%d/%m/%Y - %H:%M:%S");
			$lg_text=$this->GetStringWidth($texte);
			$this->SetXY($MargeGauche,5);
			$this->Cell($largeur_utile_page,5,$texte,0,0,'R');
		}
	
		function Footer()
		{
			global $no_footer;
			global $hauteur_page;
			global $MargeHaut, $MargeBas, $MargeGauche, $MargeDroite, $largeur_utile_page;
	
			if($no_footer=='n') {
				$this->SetFont('DejaVu','',7.5);
				$this->SetXY($MargeGauche, $hauteur_page-$MargeBas);
				$this->Cell($largeur_utile_page, 5, 'Page '.$this->PageNo(), "0", 1, 'R');
			}
		}
	}
	//======================================
	$pdf=new trombino_PDF("P","mm","A4");
	$pdf->SetTopMargin($MargeHaut);
	$pdf->SetRightMargin($MargeDroite);
	$pdf->SetLeftMargin($MargeGauche);
	$pdf->SetAutoPageBreak(true, $MargeBas);
	
	// Couleur des traits
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetLineWidth(0.2);
	
	$fonte='DejaVu';
	$fonte_size=10;
	$fonte_size_classe=14;
	$sc_interligne=1.3;
	//======================================


	$nb_total_pages=0;

	$nb_pages=Ceil($total/$nb_cell);
	//echo "\$nb_pages=$nb_pages<br />";
	$cpt=0;
	for($j=0;$j<$nb_pages;$j++) {

		$pdf->AddPage("P");

		$pdf->SetXY($x0,$y0);

		$bordure='LRBT';
		//$bordure='';
		$pdf->SetFont('DejaVu','B',$fonte_size_classe);
		//$texte="Trombinoscope ".$gepiYear." - Classe : $classe";
		$texte="Trombinoscope ".$gepiYear." - $entete";
		$pdf->Cell($largeur_utile_page,$hauteur_classe,$texte,$bordure,1,'C');

		$pdf->SetFont('DejaVu','',$fonte_size);

		// Paramètres pour cell_ajustee()
		// On n'arrive pas à centrer avec cell_ajustee()
		// Difficulté avec le mode de remplissage avec myWriteHTML()
		$largeur_dispo=$larg_cadre;

		for($m=0;$m<$trombino_pdf_nb_lig;$m++) {
			for($k=0;$k<$trombino_pdf_nb_col;$k++) {
				$x=$x0+$k*($larg_cadre+$dx);
				$y=$y0+$m*($haut_cadre+$dy)+$hauteur_classe+$ecart_sous_classe;
				$pdf->SetXY($x,$y);
				// Cadre de la photo
				$texte="";
				$pdf->Cell($larg_cadre,$haut_cadre,$texte,'LRBT',1,'L');

				//photo de l'élève ou du prof
				//if(isset($tab_ele[$cpt]['elenoet'])) {
				if(isset($id_photo_trombinoscope[$cpt])) {
					//$photo=nom_photo($tab_ele[$cpt]['elenoet'],"eleves");
					$photo=nom_photo($id_photo_trombinoscope[$cpt],$repertoire);
					if(file_exists($photo)) {
						$info_image = getimagesize($photo);
						// largeur et hauteur de l'image d'origine
						$largeur = $info_image[0];
						$hauteur = $info_image[1];
					
						$taille_max_hauteur=$haut_cadre-$hauteur_info_eleve-2;
						$taille_max_largeur=$larg_cadre-2;
					
						$ratio_l = $largeur / $taille_max_largeur;
						$ratio_h = $hauteur / $taille_max_hauteur;
						$ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;
					
						// définit largeur et hauteur pour la nouvelle image
						$nouvelle_largeur = $largeur / $ratio;
						$nouvelle_hauteur = $hauteur / $ratio;
					
						//$X_photo=$x+1;
						$X_photo=round($x+($larg_cadre-$nouvelle_largeur)/2);
						$Y_photo=$y+1;
						$pdf->Image($photo, $X_photo, $Y_photo, $nouvelle_largeur, $nouvelle_hauteur);
					}
				}


				// Informations sous la photo
				$y=$y0+$m*($haut_cadre+$dy)+($haut_cadre-$hauteur_info_eleve)+$hauteur_classe+$ecart_sous_classe;
				$pdf->SetXY($x,$y);

				$texte="";
				//if(isset($tab_ele[$cpt])) {
				if(isset($nom_trombinoscope[$cpt])) {
					$texte=my_strtoupper($nom_trombinoscope[$cpt])." ".casse_mot($prenom_trombinoscope[$cpt],'majf2');

					if(count($tab_classes)>1) {
						$texte.=" (".$classe_trombinoscope[$cpt].")";
					}

					//Elève:
					if($repertoire=="eleves") {
						$largeur_texte=$pdf->GetStringWidth($texte);
						$hauteur_temp=$fonte_size;

						$test_taille_texte='test';
						while($test_taille_texte!='ok') {
							if($largeur_texte>$largeur_dispo)
							{
								$hauteur_temp=$hauteur_temp-0.3;
								//$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.1;
								$pdf->SetFont('DejaVu','',$hauteur_temp);
								$largeur_texte=$pdf->GetStringWidth($texte);
							}
							else {
								$test_taille_texte='ok';
							}
						}
						$pdf->Cell($largeur_dispo,$hauteur_info_eleve,$texte,'',1,'C');
					}
					else {
						if((!isset($matiere_prof[$cpt]))||($matiere_prof[$cpt]=="")) {
							$hauteur_temp=$fonte_size;
							$pdf->SetFont('DejaVu','',$hauteur_temp);
							$largeur_texte=$pdf->GetStringWidth($texte);

							$test_taille_texte='test';
							while($test_taille_texte!='ok') {
								if($largeur_texte>$largeur_dispo)
								{
									$hauteur_temp=$hauteur_temp-0.3;
									//$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.1;
									$pdf->SetFont('DejaVu','',$hauteur_temp);
									$largeur_texte=$pdf->GetStringWidth($texte);
								}
								else {
									$test_taille_texte='ok';
								}
							}
							$pdf->Cell($largeur_dispo,$hauteur_info_eleve,$texte,'',1,'C');
						}
						else {
							// Affichage du nom/prénom
							$hauteur_temp=$fonte_size;
							$pdf->SetFont('DejaVu','',$hauteur_temp);
							$largeur_texte=$pdf->GetStringWidth($texte);

							$test_taille_texte='test';
							while($test_taille_texte!='ok') {
								if($largeur_texte>$largeur_dispo)
								{
									$hauteur_temp=$hauteur_temp-0.3;
									//$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.1;
									$pdf->SetFont('DejaVu','',$hauteur_temp);
									$largeur_texte=$pdf->GetStringWidth($texte);
								}
								else {
									$test_taille_texte='ok';
								}
							}
							$h_temp=floor($hauteur_info_eleve/2.5);
							$pdf->Cell($largeur_dispo,$h_temp,$texte,'',1,'C');

							// Affichage de la matière
							$y=round($y0+$m*($haut_cadre+$dy)+($haut_cadre-$hauteur_info_eleve)+$hauteur_classe+$ecart_sous_classe+$h_temp+0.2*$hauteur_temp);
							$pdf->SetXY($x,$y);

							$hauteur_restante=$h_temp-0.2*$hauteur_temp;

							$texte=$matiere_prof[$cpt];
							$hauteur_temp=$fonte_size*0.7;
							$pdf->SetFont('DejaVu','',$hauteur_temp);
							$largeur_texte=$pdf->GetStringWidth($texte);

							$test_taille_texte='test';
							while($test_taille_texte!='ok') {
								if($largeur_texte>$largeur_dispo)
								{
									$hauteur_temp=$hauteur_temp-0.3;
									//$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.1;
									$pdf->SetFont('DejaVu','',$hauteur_temp);
									$largeur_texte=$pdf->GetStringWidth($texte);
								}
								else {
									$test_taille_texte='ok';
								}
							}

							$pdf->Cell($largeur_dispo,$hauteur_restante,$texte,'',1,'C');

						}
					}
					$cpt++;
				}
			}

			$nb_total_pages++;
		}
	}

	$pref_output_mode_pdf=get_output_mode_pdf();

	$date=date("Ymd_Hi");
	$nom_fich='Trombino_';
	if((isset($groupe))&&($groupe!=0)) {
		$tab_champs=array('matieres', 'classes');
		$tmp_group=get_group($groupe, $tab_champs);
		$nom_fich.=$tmp_group['name']."_-_";
		$nom_fich.=$tmp_group['description']."_-_";
		$nom_fich.=$tmp_group['matiere']['matiere']."_-_";
		$nom_fich.=$tmp_group['classlist_string']."_";
	}
	elseif(isset($classe)) {
		$nom_fich.=get_class_from_id($classe);
	}
	$nom_fich=remplace_accents($nom_fich, "all");
	$nom_fich.=$date.'.pdf';
	header('Content-Type: application/pdf');
	$pdf->Output($nom_fich, $pref_output_mode_pdf);
	die();
}
else {
echo "Choix invalide.";
}
?>
