<?php
/*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/genere_bordereaux.php';";
$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/genere_bordereaux.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Epreuve blanche: Génération des bordereaux professeurs',
statut='';";
$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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

//debug_var();

if(isset($imprime)) {
	check_token();

	$avec_num_anonymat=isset($_POST['avec_num_anonymat']) ? $_POST['avec_num_anonymat'] : "n";

	$avec_colonne_vide_1=isset($_POST['avec_colonne_vide_1']) ? $_POST['avec_colonne_vide_1'] : "n";
	$titre_colonne_vide_1=isset($_POST['titre_colonne_vide_1']) ? $_POST['titre_colonne_vide_1'] : "";
	$avec_colonne_vide_2=isset($_POST['avec_colonne_vide_2']) ? $_POST['avec_colonne_vide_2'] : "n";
	$titre_colonne_vide_2=isset($_POST['titre_colonne_vide_2']) ? $_POST['titre_colonne_vide_2'] : "";
	$avec_colonne_vide_3=isset($_POST['avec_colonne_vide_3']) ? $_POST['avec_colonne_vide_3'] : "n";
	$titre_colonne_vide_3=isset($_POST['titre_colonne_vide_3']) ? $_POST['titre_colonne_vide_3'] : "";

	$avec_nom_prenom=isset($_POST['avec_nom_prenom']) ? $_POST['avec_nom_prenom'] : "n";
	$avec_naissance=isset($_POST['avec_naissance']) ? $_POST['avec_naissance'] : "n";
	$avec_classe=isset($_POST['avec_classe']) ? $_POST['avec_classe'] : "n";
	$avec_salle=isset($_POST['avec_salle']) ? $_POST['avec_salle'] : "n";

	$titre_colonne_vide_1=preg_replace('/;/','_',$titre_colonne_vide_1);
	$titre_colonne_vide_2=preg_replace('/;/','_',$titre_colonne_vide_2);
	$titre_colonne_vide_3=preg_replace('/;/','_',$titre_colonne_vide_3);

	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	//echo "$sql<br />\n";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)==0) {
		$msg="L'épreuve n°$id_epreuve n'existe pas.";
	}
	else {
		$lig_ep=mysqli_fetch_object($res);
		$intitule_epreuve=$lig_ep->intitule;
		$date_epreuve=formate_date("$lig_ep->date");
	
		$salle=array();
		$sql="SELECT * FROM eb_salles WHERE id_epreuve='$id_epreuve' ORDER BY salle;";
		$res_salle=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		while($lig_salle=mysqli_fetch_object($res_salle)) {
			$salle[$lig_salle->id]=$lig_salle->salle;
		}

		$profs=array();
		$cpt=0;
		$profs[$cpt]['login']="";
		$profs[$cpt]['civ_n_p']="Copie(s) non attribuée(s)";
		$cpt++;

		$sql="SELECT u.login, u.nom, u.prenom, u.civilite FROM eb_profs ep, utilisateurs u WHERE u.login=ep.login_prof AND ep.id_epreuve='$id_epreuve' ORDER BY u.nom, u.prenom;";
		//echo "$sql<br />\n";
		$res_prof=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		
		while($lig=mysqli_fetch_object($res_prof)) {
			$profs[$cpt]['login']=$lig->login;
			$profs[$cpt]['civ_n_p']=$lig->civilite." ".casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
			//echo "\$profs[$cpt]['civ_n_p']=".$profs[$cpt]['civ_n_p']."<br />\n";
			$cpt++;
		}

		if($mode=='csv') {
			$csv="";
			for($i=0;$i<count($profs);$i++) {

				$sql="SELECT DISTINCT e.nom, e.prenom, e.login, e.naissance, c.classe, ec.n_anonymat, es.id 
						FROM j_eleves_classes jec, 
								eb_copies ec, 
								eleves e, 
								classes c, 
								eb_salles es 
						WHERE e.login=ec.login_ele AND 
								ec.login_prof='".$profs[$i]['login']."' AND 
								ec.id_epreuve='$id_epreuve' AND 
								ec.id_salle=es.id AND 
								es.id_epreuve='$id_epreuve' AND 
								jec.id_classe=c.id AND 
								jec.login=e.login 
						ORDER BY ec.n_anonymat, e.nom, e.prenom;";
				//echo "$sql<br />\n";
				$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res)>0) {
					//$csv.="Epreuve:;$intitule_epreuve ($date_epreuve);\n";
					$csv.="Epreuve:;$intitule_epreuve;\n";
					$csv.="Date:;$date_epreuve;\n";
					$csv.="Bordereau de ".$profs[$i]['civ_n_p'].";\n";

					$csv.="NUM_COPIE;";
					if($avec_num_anonymat=='y') {$csv.="NUM_ANONYMAT;";}
					if($avec_colonne_vide_1=='y') {$csv.="$titre_colonne_vide_1;";}
					if($avec_colonne_vide_2=='y') {$csv.="$titre_colonne_vide_2;";}
					if($avec_colonne_vide_3=='y') {$csv.="$titre_colonne_vide_3;";}
					if($avec_nom_prenom=='y') {$csv.="NOM_PRENOM;";}
					if($avec_naissance=='y') {$csv.="NAISSANCE;";}
					if($avec_classe=='y') {$csv.="CLASSE;";}
					if($avec_salle=='y') {$csv.="SALLE;";}
					$csv.="\n";

					$cpt=0;
					while($lig=mysqli_fetch_object($res)) {
						$csv.="$cpt;";
						if($avec_num_anonymat=='y') {$csv.="$lig->n_anonymat;";}
						if($avec_colonne_vide_1=='y') {$csv.=";";}
						if($avec_colonne_vide_2=='y') {$csv.=";";}
						if($avec_colonne_vide_3=='y') {$csv.=";";}
						if($avec_nom_prenom=='y') {$csv.=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2').";";}
						if($avec_naissance=='y') {$csv.=formate_date($lig->naissance).";";}
						if($avec_classe=='y') {$csv.="$lig->classe;";}
						if($avec_salle=='y') {if(isset($salle[$lig->id])) {$csv.=$salle[$lig->id].";";} else {$csv.="???;";}}
						$csv.="\n";
						$cpt++;
					}
				}
			}
			$nom_fic="bordereaux_$id_epreuve.csv";
	
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

			if (!defined('FPDF_VERSION')) {
				require_once('../fpdf/fpdf.php');
			}

			define('LargeurPage','210');
			define('HauteurPage','297');

			$largeur_page=210;

			session_cache_limiter('private');

			$MargeHaut=10;
			$MargeDroite=10;
			$MargeGauche=10;
			$MargeBas=10;

			class rel_PDF extends FPDF
			{
				function Footer()
				{
					global $intitule_epreuve;
					global $date_epreuve;
					global $professeur_courant;
					//global $num_page;
					//global $decompte_page;
					//echo "Footer: $professeur_courant<br />\n";

					$this->SetXY(5,287);
					$this->SetFont('DejaVu','',7.5);

					//$texte=getSettingValue("gepiSchoolName")."  ";
					$texte=$intitule_epreuve." ($date_epreuve) - ".$professeur_courant;
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

				function EnteteListe()
				{
					global $intitule_epreuve;
					global $date_epreuve;
					global $professeur_courant;
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
					$texte=$professeur_courant;
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

			$h_cell=10;
			$hauteur_max_font=10;
			$hauteur_min_font=4;
			$bordure='LRBT';
			$v_align='C';
			$align='L';

			$num_page=0;

			$compteur=0;

			for($i=0;$i<count($profs);$i++) {
				//$num_page++;
				//$pdf->AddPage("P");

				// Initialisation:
				$x1="";
				$y1="";
				$y2="";

				//$professeur_courant=$profs[$i]['civ_n_p'];
				//echo "Prof $i : $professeur_courant<br />\n";
				//$pdf->EnteteListe();

				$sql="SELECT DISTINCT e.nom, e.prenom, e.login, e.naissance, c.classe, ec.n_anonymat, es.id 
						FROM j_eleves_classes jec, 
								eb_copies ec, 
								eleves e, 
								classes c, 
								eb_salles es 
						WHERE e.login=ec.login_ele AND 
								ec.login_prof='".$profs[$i]['login']."' AND 
								ec.id_epreuve='$id_epreuve' AND 
								ec.id_salle=es.id AND 
								es.id_epreuve='$id_epreuve' AND 
								jec.id_classe=c.id AND 
								jec.login=e.login 
						ORDER BY ec.n_anonymat, e.nom, e.prenom;";
				//echo "$sql<br />\n";
				$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res)>0) {
					//echo "Retour non vide<br />\n",

					$num_page++;
					$pdf->AddPage("P");
					$professeur_courant=$profs[$i]['civ_n_p'];
					//echo "Prof $i : $professeur_courant<br />\n";
					$pdf->EnteteListe();

					$cpt_col=0;

					$x0=10;
					$x1=$x0;
					$y1=30;
					$y2=41;

					$pdf->SetFont('DejaVu','B',10);
					$tab_nom=array();
					$tab_naissance=array();
					$tab_classe=array();
					$tab_salle=array();
					$tab_n_anonymat=array();
					$cpt=0;
					$larg_max=0;
					while($lig=mysqli_fetch_object($res)) {
						$tab_nom[$cpt]=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
						$tab_n_anonymat[$cpt]=$lig->n_anonymat;

						$tab_naissance[$cpt]=formate_date($lig->naissance);
						$tab_classe[$cpt]=$lig->classe;
						$tab_salle[$cpt]=isset($salle[$lig->id]) ? $salle[$lig->id] : "???";

						$larg_tmp=$pdf->GetStringWidth($tab_nom[$cpt]);
						if($larg_tmp>$larg_max) {$larg_max=$larg_tmp;}
						$cpt++;
					}

					$larg_col=array();

					$pdf->SetXY($x1,$y2);

					$larg_col[$cpt_col]=0;
					$texte='Num.';
					$larg_col[$cpt_col]=$pdf->GetStringWidth($texte)+4;
					$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
					$cpt_col++;

					$larg_col[$cpt_col]=0;
					if($avec_num_anonymat=='y') {
						$texte='Numéro copie';
						$larg_col[$cpt_col]=$pdf->GetStringWidth($texte)+4;
						$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
					}
					$cpt_col++;

					$larg_col[$cpt_col]=0;
					if($avec_colonne_vide_1=='y') {
						$texte=$titre_colonne_vide_1;
						$larg_col[$cpt_col]=$pdf->GetStringWidth($texte)+4;
						$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
					}
					$cpt_col++;

					$larg_col[$cpt_col]=0;
					if($avec_colonne_vide_2=='y') {
						$texte=$titre_colonne_vide_2;
						$larg_col[$cpt_col]=$pdf->GetStringWidth($texte)+4;
						$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
					}
					$cpt_col++;

					$larg_col[$cpt_col]=0;
					if($avec_colonne_vide_3=='y') {
						$texte=$titre_colonne_vide_3;
						$larg_col[$cpt_col]=$pdf->GetStringWidth($texte)+4;
						$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
					}
					$cpt_col++;

					$larg_col[$cpt_col]=0;
					if($avec_nom_prenom=='y') {
						$texte='Nom prénom';
						$larg_col[$cpt_col]=$larg_max+4;
						$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
					}
					$cpt_col++;

					$larg_col[$cpt_col]=0;
					if($avec_naissance=='y') {
						$texte='Naissance';
						$larg_col[$cpt_col]=$pdf->GetStringWidth($texte)+4;
						$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
					}
					$cpt_col++;

					$larg_col[$cpt_col]=0;
					if($avec_classe=='y') {
						$texte='Classe';
						$larg_col[$cpt_col]=$pdf->GetStringWidth($texte)+4;
						$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
					}
					$cpt_col++;

					$larg_col[$cpt_col]=0;
					if($avec_salle=='y') {
						$texte='Salle';
						$larg_col[$cpt_col]=$pdf->GetStringWidth($texte)+4;
						$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
					}
					$cpt_col++;

					$largeur_totale=0;
					for($loop=0;$loop<count($larg_col);$loop++) {
						$largeur_totale+=$larg_col[$loop];
					}
					$nb_colonnes=floor(($largeur_page-$MargeDroite-$MargeGauche-5)/$largeur_totale);

					$x=$pdf->GetX();
					$y=$pdf->GetY();
					$pdf->SetXY($x1,$y+$h_cell);

					$pdf->SetFont('DejaVu','B',10);

					$x_col=$x1;

					$num_colonne=1;
					for($j=0;$j<count($tab_nom);$j++) {
						if($pdf->GetY()>270) {
							if($num_colonne<$nb_colonnes) {
								$x_col=$x1+$num_colonne*($largeur_totale+5);
								$pdf->SetXY($x_col,$y2);

								$num_colonne++;
							}
							else {
								$pdf->AddPage("P");
								$pdf->EnteteListe();
								$x1=$x0;
								$x_col=$x1;
								$num_colonne=1;
								$pdf->SetXY($x1,$y2);
							}

							$pdf->SetFont('DejaVu','B',10);

							$cpt_col=0;
							$texte='Num.';
							$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
							$cpt_col++;

							if($avec_num_anonymat=='y') {
								$texte='Numéro copie';
								$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
							}
							$cpt_col++;

							if($avec_colonne_vide_1=='y') {
								$texte=$titre_colonne_vide_1;
								$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
							}
							$cpt_col++;

							if($avec_colonne_vide_2=='y') {
								$texte=$titre_colonne_vide_2;
								$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
							}
							$cpt_col++;

							if($avec_colonne_vide_3=='y') {
								$texte=$titre_colonne_vide_3;
								$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
							}
							$cpt_col++;
		
							if($avec_nom_prenom=='y') {
								$texte='Nom prénom';
								$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
							}
							$cpt_col++;
		
							if($avec_naissance=='y') {
								$texte='Naissance';
								$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
							}
							$cpt_col++;
		
							if($avec_classe=='y') {
								$texte='Classe';
								$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
							}
							$cpt_col++;
		
							if($avec_salle=='y') {
								$texte='Salle';
								$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
							}
							$cpt_col++;

							$x=$pdf->GetX();
							$y=$pdf->GetY();
							$pdf->SetXY($x_col,$y+$h_cell);
						}

						$pdf->SetFont('DejaVu','B',10);

						//$largeur_dispo=$larg_col[2];
						$largeur_dispo=$larg_max+4;
						$h_cell=10;
						$hauteur_max_font=10;
						$hauteur_min_font=4;
						$bordure='LRBT';
						$v_align='C';
						$align='L';

						//$pdf->Cell($larg_col1,10,$texte,'LRBT',0,'C');
						$x=$pdf->GetX();
						$y=$pdf->GetY();

						$cpt_col=0;
						$texte=$j+1;
						$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
						$cpt_col++;

						if($avec_num_anonymat=='y') {
							$texte=$tab_n_anonymat[$j];
							$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
						}
						$cpt_col++;

						if($avec_colonne_vide_1=='y') {
							$texte='';
							$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
						}
						$cpt_col++;

						if($avec_colonne_vide_2=='y') {
							$texte='';
							$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
						}
						$cpt_col++;

						if($avec_colonne_vide_3=='y') {
							$texte='';
							$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
						}
						$cpt_col++;

						if($avec_nom_prenom=='y') {
							$x=$pdf->GetX();
							$y=$pdf->GetY();
							$texte=$tab_nom[$j];
							cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align);
							//$pdf->Cell($largeur_dispo,10,$texte,'LRBT',0,'C');
							$pdf->SetXY($x+$largeur_dispo,$y);
						}
						$cpt_col++;
	
						if($avec_naissance=='y') {
							$texte=$tab_naissance[$j];
							$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
						}
						$cpt_col++;
	
						if($avec_classe=='y') {
							$texte=$tab_classe[$j];
							$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
						}
						$cpt_col++;
	
						if($avec_salle=='y') {
							$texte=$tab_salle[$j];
							$pdf->Cell($larg_col[$cpt_col],10,$texte,'LRBT',0,'C');
						}
						$cpt_col++;

						$x=$pdf->GetX();
						$y=$pdf->GetY();
						$pdf->SetXY($x_col,$y+$h_cell);
					}

				}
			}


/*



					$compteur++;
				}
			}
*/
			//$pdf->Footer();

			$pref_output_mode_pdf=get_output_mode_pdf();

			$date=date("Ymd_Hi");
			$nom_fich='bordereaux_'.$id_epreuve.'_'.$date.'.pdf';
			send_file_download_headers('application/pdf',$nom_fich);
			$pdf->Output($nom_fich,$pref_output_mode_pdf);
			die();

		}
	}
}


//**************** EN-TETE *****************
$titre_page = "Epreuve blanche: Bordereaux";
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

	// Générer des fiches par professeur

	echo "<p class='bold'>Epreuve n°$id_epreuve</p>\n";
	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
	$note_sur=$lig->note_sur;
	echo "</blockquote>\n";

	//========================================================
	$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve';";
	$test1=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	
	$sql="SELECT DISTINCT n_anonymat FROM eb_copies WHERE id_epreuve='$id_epreuve';";
	$test2=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($test1)!=mysqli_num_rows($test2)) {
		echo "<p style='color:red;'>Les numéros anonymats ne sont pas uniques sur l'épreuve (<i>cela ne devrait pas arriver</i>).</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT login_ele FROM eb_copies WHERE n_anonymat='' AND id_epreuve='$id_epreuve';";
	$test3=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
	//echo "$sql<br />\n";
	$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	$nb_tmp=mysqli_num_rows($test);
	if($nb_tmp==1) {
		echo "<p style='color:red;'>$nb_tmp élève n'est pas affecté dans une salle.</p>\n";
	}
	elseif($nb_tmp>1) {
		echo "<p style='color:red;'>$nb_tmp élèves n'ont pas été affectés dans des salles.</p>\n";
	}
	//========================================================

	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1' target='_blank'>\n";

	echo "<p>Choisissez le type de bordereaux à imprimer&nbsp;:<br />\n";
	echo "<input type='radio' name='mode' id='mode_csv' value='csv' /><label for='mode_csv'>CSV</label><br />";
	echo "<input type='radio' name='mode' id='mode_pdf' value='pdf' checked /><label for='mode_pdf'>PDF</label><br />";
	echo "<p>Informations à inclure&nbsp;:<br />";
	echo "<input type='checkbox' name='avec_num_anonymat' id='avec_num_anonymat' value='y' checked /><label for='avec_num_anonymat'>Avec le numéro anonymat</label><br />";
	//echo "<input type='checkbox' name='avec_colonne_vide' id='avec_colonne_vide' value='y' checked/><label for='avec_colonne_vide'>Avec une colonne vide dont l'intitulé soit </label><input type='text' name='titre_colonne_vide' value='Note sur $note_sur' /><br />";
	echo "<input type='checkbox' name='avec_colonne_vide_1' id='avec_colonne_vide_1' value='y' checked /><label for='avec_colonne_vide_1'> Avec une colonne vide dont l'intitulé soit </label><input type='text' name='titre_colonne_vide_1' value='Note sur $note_sur' /><br />";
	echo "<input type='checkbox' name='avec_colonne_vide_2' id='avec_colonne_vide_2' value='y' /><label for='avec_colonne_vide_2'> Avec une deuxième colonne vide dont l'intitulé soit </label><input type='text' name='titre_colonne_vide_2' value='Pointage départ' /><br />";
	echo "<input type='checkbox' name='avec_colonne_vide_3' id='avec_colonne_vide_3' value='y' /><label for='avec_colonne_vide_3'> Avec une troisième colonne vide dont l'intitulé soit </label><input type='text' name='titre_colonne_vide_3' value='Pointage retour' /><br />";
	echo "Normalement, les champs suivants ne devraient pas apparaitre sur des bordereaux professeurs, mais vous imaginerez peut-être des usages auxquels les développeurs n'avaient pas pensé.<br />";
	echo "<input type='checkbox' name='avec_nom_prenom' id='avec_nom_prenom' value='y' /><label for='avec_nom_prenom'>Avec les nom/prénom des élèves</label><br />";
	echo "<input type='checkbox' name='avec_naissance' id='avec_naissance' value='y' /><label for='avec_naissance'>Avec la date de naissance de l'élève</label><br />";
	echo "<input type='checkbox' name='avec_classe' id='avec_classe' value='y' /><label for='avec_classe'>Avec la classe de l'élève</label><br />";
	echo "<input type='checkbox' name='avec_salle' id='avec_salle' value='y' /><label for='avec_salle'>Avec le nom de salle dans laquelle l'élève a passé l'épreuve</label><br />";

	echo add_token_field();

	echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />";
	echo "<input type='hidden' name='imprime' value='y' />";
	echo "<input type='submit' name='valider' value='Valider' /><br />";
	echo "</p>\n";
	echo "</form>\n";
}
require("../lib/footer.inc.php");
?>
