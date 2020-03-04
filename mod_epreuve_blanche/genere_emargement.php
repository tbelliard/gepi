<?php
/*
* Copyright 2001, 2020 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

//$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}


$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/genere_emargement.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/genere_emargement.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Epreuve blanche: Génération émargement',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$id_epreuve=isset($_POST['id_epreuve']) ? $_POST['id_epreuve'] : (isset($_GET['id_epreuve']) ? $_GET['id_epreuve'] : NULL);
$imprime=isset($_POST['imprime']) ? $_POST['imprime'] : (isset($_GET['imprime']) ? $_GET['imprime'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

include('lib_eb.php');

if(isset($imprime)) {
	check_token();

	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		$msg="L'épreuve n°$id_epreuve n'existe pas.";
	}
	else {
		$lig_ep=mysqli_fetch_object($res);
		$intitule_epreuve=$lig_ep->intitule;
		// 20200305
		$mysql_date_epreuve=$lig_ep->date;
		$date_epreuve=formate_date("$lig_ep->date");
	
		$sql="SELECT * FROM eb_salles WHERE id_epreuve='$id_epreuve' ORDER BY salle;";
		$res_salle=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig_salle=mysqli_fetch_object($res_salle)) {
			$salle[]=$lig_salle->salle;
			$id_salle[]=$lig_salle->id;
		}
	
		if($mode=='csv') {
			$csv="";
			for($i=0;$i<count($id_salle);$i++) {
				//$sql="SELECT e.nom, e.prenom, e.login, ec.n_anonymat FROM eb_copies ec, eleves e WHERE e.login=ec.login_ele AND ec.id_salle='$id_salle[$i]' AND ec.id_epreuve='$id_epreuve' ORDER BY e.nom,e.prenom;";
				$sql="SELECT DISTINCT e.nom, e.prenom, e.login, e.naissance, c.classe, ec.n_anonymat FROM j_eleves_classes jec, eb_copies ec, eleves e, classes c WHERE e.login=ec.login_ele AND ec.id_salle='$id_salle[$i]' AND ec.id_epreuve='$id_epreuve' AND jec.id_classe=c.id AND jec.login=e.login ORDER BY e.nom, e.prenom;";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					//$csv.="Epreuve:;$intitule_epreuve ($date_epreuve);\n";
					$csv.="Epreuve:;$intitule_epreuve;\n";
					$csv.="Date:;$date_epreuve;\n";
					if(!preg_match("/^Salle /i", $salle[$i])) {
						$csv.="Liste d'émargement;Salle $salle[$i];\n";
					}
					else {
						$csv.="Liste d'émargement;$salle[$i];\n";
					}

					switch ($imprime) {
						case "sans_num_anonymat":
							$csv.="Nom;Prénom;Signature;\n";
						break;
						case "avec_num_anonymat":
							$csv.="Nom;Prénom;Numéro anonymat;Signature\n";
						break;
						case "tout":
							$csv.="Nom;Prénom;Classe;Date_naissance;Numéro anonymat;Signature\n";
						break;
					}
					
					while($lig=mysqli_fetch_object($res)) {
						
						switch ($imprime) {
							case "sans_num_anonymat":
								// PROBLEME: ON PEUT AVOIR DES HOMONYMES DANS UNE MÊME SALLE...
								$csv.=casse_mot($lig->nom).";".casse_mot($lig->prenom,'majf2').";;\n";
							break;
							case "avec_num_anonymat":
								$csv.=casse_mot($lig->nom).";".casse_mot($lig->prenom,'majf2').";$lig->n_anonymat;\n";
							break;
							case "tout":
								$csv.=casse_mot($lig->nom).";".casse_mot($lig->prenom,'majf2').";$lig->classe;"."$lig->naissance;"."$lig->n_anonymat;\n";
							break;
						}
					}
				}
			}
			$nom_fic="emargement_epreuve_$id_epreuve.csv";
	
			$now = gmdate('D, d M Y H:i:s') . ' GMT';
			send_file_download_headers('text/x-csv',$nom_fic);
			//echo $csv;
			echo echo_csv_encoded($csv);
			die();
	
		}
		elseif($mode=='pdf') {

			if (!defined('FPDF_VERSION')) {
				require_once('../fpdf/fpdf.php');
			}
			
			
			define('LargeurPage','210');
			define('HauteurPage','297');

			$largeur_page=210;

			// Pb avec php 7.2:
			$test = phpversion();
			$version = mb_substr($test, 0, 1);
			if ($version<7) {
				session_cache_limiter('private');
			}

			$MargeHaut=10;
			$MargeDroite=10;
			$MargeGauche=10;
			$MargeBas=10;

			$effectif_salle_courante="";

			class rel_PDF extends FPDF
			{
				function Footer()
				{
					global $intitule_epreuve;
					global $date_epreuve;
					global $salle_courante;
					global $effectif_salle_courante;
					//global $num_page;
					//global $decompte_page;

					$this->SetXY(5,287);
					$this->SetFont('DejaVu','',7.5);

					//$texte=getSettingValue("gepiSchoolName")."  ";
					//$texte=$intitule_epreuve." ($date_epreuve) - ".$salle_courante;
					$texte=$intitule_epreuve." ($date_epreuve) - ".$salle_courante." - (effectif : $effectif_salle_courante)";
					$lg_text=$this->GetStringWidth($texte);
					$this->SetXY(10,287);
					$this->Cell(0,5,$texte,0,0,'L');

					//$this->SetY(287);
					$this->Cell(0,5,'Page '.$this->PageNo(),"0",1,'C');
					//$this->Cell(0,5,'Page '.($this->PageNo()-$decompte_page),"0",1,'C');
					//$this->Cell(0,5,'Page '.$this->PageNo().'-'.$decompte_page.'='.($this->PageNo()-$decompte_page),"0",1,'C');
					//$this->Cell(0,5,'Page '.$num_page,"0",1,'C');

					// Je ne parviens pas à faire reprendre la numérotation à 1 lors d'un changement de salle
				}

				function EnteteEmargement()
				{
					global $intitule_epreuve;
					global $date_epreuve;
					global $salle_courante;
					global $fonte, $MargeDroite, $largeur_page, $MargeGauche, $sc_interligne, $salle, $i;
					//global $num_page;
					//global $decompte_page;

					$this->SetFont('DejaVu','B',14);
					$this->Setxy(10,10);
					$this->Cell($largeur_page-$MargeDroite-$MargeGauche,20,getSettingValue('gepiSchoolName').' - Année scolaire '.getSettingValue('gepiYear'),'LRBT',1,'C');

					$x1=$this->GetX();
					$y1=$this->GetY();

					$this->SetFont('DejaVu','B',12);
					$texte='Epreuve : ';
					$largeur_tmp=$this->GetStringWidth($texte);
					$this->Cell($largeur_tmp,$this->FontSize*$sc_interligne,$texte,'',0,'L');
					$this->SetFont('DejaVu','',12);
					$texte=$intitule_epreuve;
					$this->Cell($this->GetStringWidth($texte),$this->FontSize*$sc_interligne,$texte,'',1,'L');

					$this->SetFont('DejaVu','B',12);
					$texte='Date : ';
					$this->Cell($largeur_tmp,$this->FontSize*$sc_interligne,$texte,'',0,'L');
					$this->SetFont('DejaVu','',12);
					$texte=$date_epreuve;
					$this->Cell($this->GetStringWidth($texte),$this->FontSize*$sc_interligne,$texte,'',1,'L');

					//$x2=$this->GetX();
					$y2=$this->GetY();

					$this->SetFont('DejaVu','B',12);
					if(!preg_match("/^Salle /i", $salle[$i])) {
						$texte="Salle $salle[$i]";
					}
					else {
						$texte="$salle[$i]";
					}
					$larg_tmp=$sc_interligne*($this->GetStringWidth($texte));
					$this->SetXY($largeur_page-$larg_tmp-$MargeDroite,$y1+($y2-$y1)/4);
					$this->Cell($larg_tmp,$this->FontSize*$sc_interligne,$texte,'LRBT',1,'C');
				}
			}

			// Définition de la page
			$pdf=new rel_PDF("P","mm","A4");
			//$pdf=new FPDF("P","mm","A4");
			$pdf->SetTopMargin($MargeHaut);
			$pdf->SetRightMargin($MargeDroite);
			$pdf->SetLeftMargin($MargeGauche);
			//$pdf->SetAutoPageBreak(true, $MargeBas);

			// Couleur des traits
			$pdf->SetDrawColor(0,0,0);
			$pdf->SetLineWidth(0.2);

			$fonte='DejaVu';
			$sc_interligne=1.3;

			$num_page=0;

			$compteur=0;
			for($i=0;$i<count($id_salle);$i++) {
				$decompte_page=$num_page;

				$sql="SELECT e.nom, e.prenom, e.login, ec.n_anonymat FROM eb_copies ec, eleves e WHERE e.login=ec.login_ele AND ec.id_salle='$id_salle[$i]' AND ec.id_epreuve='$id_epreuve' ORDER BY e.nom,e.prenom;";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				// Si on initialise $effectif_salle_courante avant l'ajout de page PDF, on se retrouve avec un décalage: on a l'effectif de la classe suivante affiché en footer???
				//$effectif_salle_courante=mysql_num_rows($res);
				//if($effectif_salle_courante>0) {
				if(mysqli_num_rows($res)>0) {

					//if($compteur>0) {$pdf->Footer();}
					$num_page++;
					$pdf->AddPage("P");
					$salle_courante=$salle[$i];
					$effectif_salle_courante=mysqli_num_rows($res);

					// Initialisation:
					$x1="";
					$y1="";
					$y2="";

					$pdf->EnteteEmargement();
/*
					//Entête du PDF
					//$pdf->SetLineWidth(0.7);
					$pdf->SetFont('DejaVu','B',14);
					$pdf->Setxy(10,10);
					$pdf->Cell($largeur_page-$MargeDroite-$MargeGauche,20,getSettingValue('gepiSchoolName').' - Année scolaire '.getSettingValue('gepiYear'),'LRBT',1,'C');

					$x1=$pdf->GetX();
					$y1=$pdf->GetY();

					$pdf->SetFont('DejaVu','B',12);
					$texte='Epreuve : ';
					$largeur_tmp=$pdf->GetStringWidth($texte);
					$pdf->Cell($largeur_tmp,$pdf->FontSize*$sc_interligne,$texte,'',0,'L');
					$pdf->SetFont('DejaVu','',12);
					$texte=$intitule_epreuve;
					$pdf->Cell($pdf->GetStringWidth($texte),$pdf->FontSize*$sc_interligne,$texte,'',1,'L');

					$pdf->SetFont('DejaVu','B',12);
					$texte='Date : ';
					$pdf->Cell($largeur_tmp,$pdf->FontSize*$sc_interligne,$texte,'',0,'L');
					$pdf->SetFont('DejaVu','',12);
					$texte=$date_epreuve;
					$pdf->Cell($pdf->GetStringWidth($texte),$pdf->FontSize*$sc_interligne,$texte,'',1,'L');

					//$x2=$pdf->GetX();
					$y2=$pdf->GetY();

					$pdf->SetFont('DejaVu','B',12);
					$texte="Salle $salle[$i]";
					$larg_tmp=$sc_interligne*($pdf->GetStringWidth($texte));
					$pdf->SetXY($largeur_page-$larg_tmp-$MargeDroite,$y1+($y2-$y1)/4);
					$pdf->Cell($larg_tmp,$pdf->FontSize*$sc_interligne,$texte,'LRBT',1,'C');
*/

					$x1=10;
					$y1=30;
					$y2=41;

					$pdf->SetXY($x1,$y2);

					/*
					$x=$pdf->GetX();
					$y=$pdf->GetY();
					$pdf->Cell($largeur_page-$MargeDroite-$MargeGauche,10,'','LRBT',0,'L');
					$pdf->SetXY($x,$y);
					*/

					$pdf->SetFont('DejaVu','B',10);
					$tab_nom=array();
					$tab_n_anonymat=array();
					$cpt=0;
					$larg_max=0;
					while($lig=mysqli_fetch_object($res)) {
						$tab_nom[$cpt]=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
						$tab_n_anonymat[$cpt]=$lig->n_anonymat;

						$larg_tmp=$pdf->GetStringWidth($tab_nom[$cpt]);
						if($larg_tmp>$larg_max) {$larg_max=$larg_tmp;}
						$cpt++;
					}

					$texte='Nom prénom';
					//$larg_col1=$pdf->GetStringWidth($texte);
					$larg_col1=$larg_max+4;
					$pdf->Cell($larg_col1,10,$texte,'LRBT',0,'C');
					$larg_col2=0;
					if($imprime=='avec_num_anonymat') {
						$texte='Num.anonymat';
						$larg_col2=$pdf->GetStringWidth($texte)+4;
						$pdf->Cell($larg_col2,10,$texte,'LRBT',0,'C');
					}
					$texte='Signature';
					$larg_col3=$largeur_page-$MargeDroite-$MargeGauche-$larg_col1-$larg_col2;
					$pdf->Cell($larg_col3,10,$texte,'LRBT',1,'C');

					$pdf->SetFont('DejaVu','B',10);
					/*
					while($lig=mysql_fetch_object($res)) {
						$texte=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
						$pdf->Cell($larg_col1,10,$texte,'LRBT',0,'C');
						if($imprime=='avec_num_anonymat') {
							$texte=$lig->n_anonymat;
							$pdf->Cell($larg_col2,10,$texte,'LRBT',0,'C');
						}
						$pdf->Cell($larg_col3,10,'','LRBT',1,'C');
					}
					*/
					for($j=0;$j<count($tab_nom);$j++) {
						if($pdf->GetY()>270) {
							$pdf->AddPage("P");
							$pdf->EnteteEmargement();
							$pdf->SetXY($x1,$y2);

							$texte='Nom prénom';
							$larg_col1=$larg_max+4;
							$pdf->Cell($larg_col1,10,$texte,'LRBT',0,'C');
							$larg_col2=0;
							if($imprime=='avec_num_anonymat') {
								$texte='Num.anonymat';
								$larg_col2=$pdf->GetStringWidth($texte)+4;
								$pdf->Cell($larg_col2,10,$texte,'LRBT',0,'C');
							}
							$texte='Signature';
							$larg_col3=$largeur_page-$MargeDroite-$MargeGauche-$larg_col1-$larg_col2;
							$pdf->Cell($larg_col3,10,$texte,'LRBT',1,'C');

						}

						$pdf->SetFont('DejaVu','B',10);

						$largeur_dispo=$larg_col1;
						$h_cell=10;
						$hauteur_max_font=10;
						$hauteur_min_font=4;
						$bordure='LRBT';
						$v_align='C';
						$align='L';

						$texte=$tab_nom[$j];
						//$pdf->Cell($larg_col1,10,$texte,'LRBT',0,'C');
						$x=$pdf->GetX();
						$y=$pdf->GetY();
						cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align);
						$pdf->SetXY($x+$largeur_dispo,$y);

						if($imprime=='avec_num_anonymat') {
							$texte=$tab_n_anonymat[$j];
							$pdf->Cell($larg_col2,10,$texte,'LRBT',0,'C');
						}
						$pdf->Cell($larg_col3,10,'','LRBT',1,'C');
					}

					$compteur++;
				}
			}

			//$pdf->Footer();

			$pref_output_mode_pdf=get_output_mode_pdf();

			$date=date("Ymd_Hi");
			$nom_fich='Emargement_'.$id_epreuve.'_'.$date.'.pdf';
			send_file_download_headers('application/pdf',$nom_fich);
			$pdf->Output($nom_fich,$pref_output_mode_pdf);
			die();

		}
		elseif($mode=='odt') {
			// 20200305

			//$tab_lignes_OOo_eleve=array();
			$tab_lignes_OOo_salle=array();
			$tab_lignes_OOo=array();

			// Boucle par salle, puis par élève

			for($i=0;$i<count($id_salle);$i++) {
				$tab_lignes_OOo_salle=array();

				$tab_lignes_OOo_salle['epreuve']=$intitule_epreuve;
				$tab_lignes_OOo_salle['date']=$date_epreuve;
				$tab_lignes_OOo_salle['salle']=$salle[$i];

				$tab_lignes_OOo_salle['etab']=getSettingValue("gepiSchoolName");
				$tab_lignes_OOo_salle['acad']=getSettingValue("gepiSchoolAcademie");
				$tab_lignes_OOo_salle['adr1']=getSettingValue("gepiSchoolAdress1")." ".getSettingValue("gepiSchoolAdress2");
				$tab_lignes_OOo_salle['cp']=getSettingValue("gepiSchoolZipCode");
				$tab_lignes_OOo_salle['ville']=getSettingValue("gepiSchoolCity");
				$tab_lignes_OOo_salle['annee_scolaire']=getSettingValue("gepiYear");

				$tab_lignes_OOo_salle['eleve']=array();

				$sql="SELECT e.nom, e.prenom, e.login, e.naissance, ec.n_anonymat FROM eb_copies ec, eleves e WHERE e.login=ec.login_ele AND ec.id_salle='$id_salle[$i]' AND ec.id_epreuve='$id_epreuve' ORDER BY e.nom,e.prenom;";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					$cpt=0;
					$rang=1;
					while($lig=mysqli_fetch_object($res)) {
						$tab_lignes_OOo_salle['eleve'][$cpt]['nom']=casse_mot($lig->nom);
						$tab_lignes_OOo_salle['eleve'][$cpt]['prenom']=casse_mot($lig->prenom, 'majf2');
						$tab_lignes_OOo_salle['eleve'][$cpt]['naissance']=formate_date($lig->naissance);

						$tmp_tab_clas=get_clas_ele_telle_date($lig->login, $mysql_date_epreuve.' 00:00:00');
						if(isset($tmp_tab_clas['classe'])) {
							$tab_lignes_OOo_salle['eleve'][$cpt]['classe']=$tmp_tab_clas['classe'];
						}
						else {
							$tab_lignes_OOo_salle['eleve'][$cpt]['classe']=get_chaine_liste_noms_classes_from_ele_login($lig->login);
						}

						$tab_lignes_OOo_salle['eleve'][$cpt]['n_anonymat']=$lig->n_anonymat;

						$tab_lignes_OOo_salle['eleve'][$cpt]['salle']=$salle[$i];

						$tab_lignes_OOo_salle['eleve'][$cpt]['rang']=$rang;

						$cpt++;
						$rang++;
					}
				}
				$tab_lignes_OOo_salle['effectif']=$cpt;

				//$tab_lignes_OOo['salle'][]=$tab_lignes_OOo_salle;
				$tab_lignes_OOo[]=$tab_lignes_OOo_salle;
			}

			/*
			echo "<pre>";
			print_r($tab_lignes_OOo);
			echo "</pre>";
			*/

			$mode_ooo="imprime";
			
			include_once('../tbs/tbs_class.php');
			include_once('../tbs/plugins/tbs_plugin_opentbs.php');
			
			// Création d'une classe  TBS OOo class

			$OOo = new clsTinyButStrong;
			$OOo->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
			

			$fichier_a_utiliser="mod_epreuve_blanche_emargement.odt";

			$tableau_a_utiliser=$tab_lignes_OOo;
			// Nom à utiliser dans le fichier ODT:
			$nom_a_utiliser="salle";
			/*
				[salle;block=begin;sub1=eleve]
				[salle.etab] – Année scolaire [salle.annee_scolaire]
				Épreuve : [salle.epreuve]
				Date : [salle.date]
				Salle : [salle.salle]
				Effectif : [salle.effectif] candidats

				[salle_sub1.rang;block=table:table-row]
				[salle_sub1.nom]
				[salle_sub1.prenom]
				[salle_sub1.naissance]
				[salle_sub1.classe]
				[salle_sub1.salle]
				[salle_sub1.n_anonymat]

				[salle;block=end]


				$tab_lignes_OOo[$cpt_salle]['etab']
				$tab_lignes_OOo[$cpt_salle]['epreuve']
				$tab_lignes_OOo[$cpt_salle]['date']
				$tab_lignes_OOo[$cpt_salle]['salle']

				$tab_lignes_OOo[$cpt_salle]['eleve'][$cpt][]['nom']
				$tab_lignes_OOo[$cpt_salle]['eleve'][$cpt][]['prenom']
				$tab_lignes_OOo[$cpt_salle]['eleve'][$cpt][]['naissance']
				...
				$tab_lignes_OOo[$cpt_salle]['eleve'][$cpt][]['rang']
			*/


			$prefixe_generation_hors_dossier_mod_ooo="../mod_ooo/";
			include_once('../mod_ooo/lib/lib_mod_ooo.php'); // les fonctions
			$nom_fichier_modele_ooo = $fichier_a_utiliser;
			include_once('../mod_ooo/lib/chemin.inc.php'); // le chemin des dossiers contenant les  modèles

			$OOo->LoadTemplate($nom_dossier_modele_a_utiliser."/".$nom_fichier_modele_ooo, OPENTBS_ALREADY_UTF8);

			// $OOo->MergeBlock('eleves',$tab_eleves_OOo);
			$OOo->MergeBlock($nom_a_utiliser, $tableau_a_utiliser);
			
			$nom_fic = $fichier_a_utiliser;
			
			$OOo->Show(OPENTBS_DOWNLOAD, $nom_fic);
			
			$OOo->remove(); //suppression des fichiers de travail
			
			$OOo->close();

			die();
		}
	}
}


//**************** EN-TETE *****************
$titre_page = "Epreuve blanche: Emargement";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

//echo "<div class='noprint'>\n";
//echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
echo "<p class='bold'><a href='index.php?id_epreuve=$id_epreuve&amp;mode=modif_epreuve'>Retour</a>";
//echo "</p>\n";
//echo "</div>\n";

if(!isset($imprime)) {
	echo "</p>\n";

	// Générer des fiches par salles

	echo "<p class='bold'>Epreuve n°$id_epreuve</p>\n";
	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>L'épreuve choisie (<i>$id_epreuve</i>) n'existe pas.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	
	$lig=mysqli_fetch_object($res);
	echo "<blockquote>\n";
	echo "<p><b>".$lig->intitule."</b> (<i>".formate_date($lig->date)."</i>)<br />\n";
	if($lig->description!='') {
		echo nl2br(trim($lig->description))."<br />\n";
	}
	else {
		echo "Pas de description saisie.<br />\n";
	}
	echo "</blockquote>\n";

	//========================================================
	$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve';";
	$test1=mysqli_query($GLOBALS["mysqli"], $sql);
	
	$sql="SELECT DISTINCT n_anonymat FROM eb_copies WHERE id_epreuve='$id_epreuve';";
	$test2=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test1)!=mysqli_num_rows($test2)) {
		echo "<p style='color:red;'>Les numéros anonymats ne sont pas uniques sur l'épreuve (<i>cela ne devrait pas arriver</i>).</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT login_ele FROM eb_copies WHERE n_anonymat='' AND id_epreuve='$id_epreuve';";
	$test3=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test3)>0) {
		echo "<p style='color:red;'>Un ou des numéros anonymats ne sont pas valides sur l'épreuve&nbsp;: ";
		$cpt=0;
		while($lig=mysqli_fetch_object($test3)) {
			if($cpt>0) {echo ", ";}
			echo get_nom_prenom_eleve($lig->login_ele);
			$cpt++;
		}
		echo "<br />Cela ne devrait pas arriver.<br />La saisie n'est pas possible.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	//========================================================

	//========================================================
	//echo "<p style='color:red;'>A FAIRE&nbsp;: Contrôler si certains élèves n'ont pas été affectés dans des salles.</p>\n";
	$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND id_salle='-1';";
	//echo "$sql<br />";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_tmp=mysqli_num_rows($test);
	if($nb_tmp==1) {
		echo "<p style='color:red;'>$nb_tmp élève n'est pas affecté dans une salle.</p>\n";
	}
	elseif($nb_tmp>1) {
		echo "<p style='color:red;'>$nb_tmp élèves n'ont pas été affectés dans des salles.</p>\n";
	}
	//========================================================

	echo "<p>Choisissez le type de liste à imprimer&nbsp;:</p>\n";
	echo "<ul>\n";
	echo "<li><b>CSV</b>&nbsp;:\n";
	 	echo "<ul>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=sans_num_anonymat&amp;mode=csv".add_token_in_url()."' target='_blank'>Avec les colonnes 'NOM;PRENOM;SIGNATURE'</a></li>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=avec_num_anonymat&amp;mode=csv".add_token_in_url()."' target='_blank'>Avec les colonnes 'NOM;PRENOM;NUM_ANONYMAT;SIGNATURE'</a></li>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=tout&amp;mode=csv".add_token_in_url()."' target='_blank'>Avec les colonnes 'NOM;PRENOM;CLASSE;DATE_DE_NAISSANCE;NUM_ANONYMAT;SIGNATURE'</a></li>\n";
		echo "</ul>\n";
	echo "</li>\n";
	echo "<li><b>PDF</b>&nbsp;:\n";
	 	echo "<ul>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=sans_num_anonymat&amp;mode=pdf".add_token_in_url()."' target='_blank'>Avec les colonnes 'NOM_PRENOM;SIGNATURE'</a></li>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=avec_num_anonymat&amp;mode=pdf".add_token_in_url()."' target='_blank'>Avec les colonnes 'NOM_PRENOM;NUM_ANONYMAT;SIGNATURE'</a></li>\n";
		echo "</ul>\n";
	echo "</li>\n";

	// 20200305
	echo "<li><b>ODT</b>&nbsp;:\n";
	 	echo "<ul>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;mode=odt&amp;imprime=odt".add_token_in_url()."' target='_blank'>Fichier LibreOffice/OpenOffice.org</a></li>\n";
		echo "</ul>\n";
	echo "</li>\n";

	echo "</ul>\n";
}

require("../lib/footer.inc.php");
?>
