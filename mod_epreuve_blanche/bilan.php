<?php
/*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/bilan.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/bilan.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Epreuve blanche: Bilan',
statut='';";
$insert=mysql_query($sql);
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
$avec_n_anonymat=isset($_POST['avec_n_anonymat']) ? $_POST['avec_n_anonymat'] : (isset($_GET['avec_n_anonymat']) ? $_GET['avec_n_anonymat'] : 'n');
$avec_correcteur=isset($_POST['avec_correcteur']) ? $_POST['avec_correcteur'] : (isset($_GET['avec_correcteur']) ? $_GET['avec_correcteur'] : 'n');
$avec_salle=isset($_POST['avec_salle']) ? $_POST['avec_salle'] : (isset($_GET['avec_salle']) ? $_GET['avec_salle'] : 'n');
$avec_sexe=isset($_POST['avec_sexe']) ? $_POST['avec_sexe'] : (isset($_GET['avec_sexe']) ? $_GET['avec_sexe'] : 'n');

include('lib_eb.php');

//debug_var();

if(isset($imprime)) {
	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$msg="L'épreuve n°$id_epreuve n'existe pas.";
	}
	else {
		$lig_ep=mysql_fetch_object($res);
		$intitule_epreuve=$lig_ep->intitule;
		$date_epreuve=formate_date("$lig_ep->date");
	
		$sql="SELECT * FROM eb_salles WHERE id_epreuve='$id_epreuve' ORDER BY salle;";
		//echo "$sql<br />";
		$res_salle=mysql_query($sql);
		while($lig_salle=mysql_fetch_object($res_salle)) {
			$salle[]=$lig_salle->salle;
			$id_salle[]=$lig_salle->id;
		}

		$sql="SELECT g.* FROM eb_groupes eg, groupes g WHERE eg.id_epreuve='$id_epreuve' AND eg.id_groupe=g.id ORDER BY g.name,g.description;";
		$res_groupe=mysql_query($sql);
		while($lig_groupe=mysql_fetch_object($res_groupe)) {
			$groupe_name[]=$lig_groupe->name;
			$groupe_desc[]=$lig_groupe->description;
			$id_groupe[]=$lig_groupe->id;

			// Récupérer la liste des classes associées
			$sql="SELECT DISTINCT c.classe FROM classes c, j_groupes_classes jgc WHERE c.id=jgc.id_classe AND jgc.id_groupe='$lig_groupe->id' ORDER BY c.classe;";
			//echo "$sql<br />";
			$res_clas=mysql_query($sql);
			$clas_list="";
			$cpt2=0;
			while($lig_clas=mysql_fetch_object($res_clas)) {
				if($cpt2>0) {$clas_list.=", ";}
				$clas_list.=$lig_clas->classe;
				$cpt2++;
			}
			$groupe_classes[]=$clas_list;
		}


		if($mode=='pdf') {


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
					//global $salle_courante;
					global $current_group;
					//global $num_page;
					//global $decompte_page;

					$this->SetXY(5,287);
					$this->SetFont('DejaVu','',7.5);

					//$texte=getSettingValue("gepiSchoolName")."  ";
					//$texte=$intitule_epreuve." ($date_epreuve) - ".$salle_courante;
					$texte=$intitule_epreuve." ($date_epreuve) - ".$current_group;
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
			}

			// Définition de la page
			$pdf=new rel_PDF("P","mm","A4");
			//$pdf=new FPDF("P","mm","A4");
			$pdf->SetTopMargin($MargeHaut);
			$pdf->SetRightMargin($MargeDroite);
			$pdf->SetLeftMargin($MargeGauche);
			$pdf->SetAutoPageBreak(true, $MargeBas);

			// Couleur des traits
			$pdf->SetDrawColor(0,0,0);
			$pdf->SetLineWidth(0.2);

			$sc_interligne=1.3;

			$num_page=0;

			$compteur=0;
			//for($i=0;$i<count($id_salle);$i++) {
			for($i=0;$i<count($id_groupe);$i++) {
				$decompte_page=$num_page;

				//$sql="SELECT e.nom, e.prenom, e.login, ec.n_anonymat, ec.note, ec.statut, ec.login_prof,  FROM eb_copies ec, eleves e WHERE e.login=ec.login_ele AND ec.id_salle='$id_salle[$i]' AND ec.id_epreuve='$id_epreuve' ORDER BY e.nom,e.prenom;";

				$sql="SELECT DISTINCT e.nom, e.prenom, e.login, ec.n_anonymat, ec.note, ec.statut, ec.login_prof, es.salle FROM eb_copies ec, eb_salles es, eb_groupes eg, eleves e, j_eleves_groupes jeg WHERE e.login=ec.login_ele AND ec.id_salle=es.id AND ec.id_epreuve='$id_epreuve' AND es.id_epreuve='$id_epreuve' AND eg.id_epreuve='$id_epreuve' AND eg.id_groupe='$id_groupe[$i]' AND jeg.login=e.login AND jeg.id_groupe=eg.id_groupe ORDER BY e.nom,e.prenom;";
				//echo "$sql<br />";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {

					//if($compteur>0) {$pdf->Footer();}
					$num_page++;

					$current_group=$groupe_name[$i]." (".$groupe_classes[$i].")";
					$pdf->AddPage("P");
					//$salle_courante=$salle[$i];

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
					//$texte="Salle $salle[$i]";
					$texte=$current_group;
					$larg_tmp=$sc_interligne*($pdf->GetStringWidth($texte));
					$pdf->SetXY($largeur_page-$larg_tmp-$MargeDroite,$y1+($y2-$y1)/4);
					$pdf->Cell($larg_tmp,$pdf->FontSize*$sc_interligne,$texte,'LRBT',1,'C');

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
					$tab_note=array();
					$tab_correcteur=array();
					$tab_salle=array();
					$tab_distinct_correcteur=array();
					$cpt=0;
					$larg_max=0;
					while($lig=mysql_fetch_object($res)) {
						$tab_nom[$cpt]=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
						$tab_n_anonymat[$cpt]=$lig->n_anonymat;

						$tab_salle[$cpt]=$lig->salle;

						if($lig->statut=='v') {
							$tab_note[$cpt]="";
						}
						elseif($lig->statut!='') {
							$tab_note[$cpt]=$lig->statut;
						}
						else {
							$tab_note[$cpt]=$lig->note;
						}

						$tab_correcteur[$cpt]=$lig->login_prof;
						if(!in_array($lig->login_prof,$tab_distinct_correcteur)) {
							$tab_distinct_correcteur["$lig->login_prof"]=get_denomination_prof($lig->login_prof);
						}

						$larg_tmp=$pdf->GetStringWidth($tab_nom[$cpt]);
						if($larg_tmp>$larg_max) {$larg_max=$larg_tmp;}
						$cpt++;
					}

					$h_cell=$pdf->FontSize*$sc_interligne;

					$larg_col1=0;
					if($avec_n_anonymat=='y') {
						$texte='Num.anonymat';
						$larg_col1=$pdf->GetStringWidth($texte)+4;
						$pdf->Cell($larg_col1,$h_cell,$texte,'LRBT',0,'C');
					}

					$texte='Nom prénom';
					//$larg_col1=$pdf->GetStringWidth($texte);
					$larg_col2=$larg_max+4;
					$pdf->Cell($larg_col2,$h_cell,$texte,'LRBT',0,'C');

					$texte='Note';
					$larg_tmp=$pdf->GetStringWidth($texte);
					$larg_col3=$larg_tmp+4;
					if(($avec_correcteur=='y')||($avec_salle=='y')) {
						$pdf->Cell($larg_col3,$h_cell,$texte,'LRBT',0,'C');
					}
					else {
						$pdf->Cell($larg_col3,$h_cell,$texte,'LRBT',1,'C');
					}

					if($avec_correcteur=='y') {
						$texte='Correcteur';
						$larg_tmp=$pdf->GetStringWidth($texte);
						$larg_tmp_max=$larg_tmp;
						//for($j=0;$j<count($tab_distinct_correcteur);$j++) {
						//	$texte_test=$tab_distinct_correcteur[$j];
						foreach($tab_distinct_correcteur as $current_login => $current_denomination) {
							$texte_test=$current_denomination;
							$larg_tmp=$pdf->GetStringWidth($texte_test);
							if($larg_tmp>$larg_tmp_max) {$larg_tmp_max=$larg_tmp;}
						}
						$larg_col4=$larg_tmp_max+4;
						if($avec_salle=='y') {
							$pdf->Cell($larg_col4,$h_cell,$texte,'LRBT',0,'C');
						}
						else {
							$pdf->Cell($larg_col4,$h_cell,$texte,'LRBT',1,'C');
						}
					}

					if($avec_salle=='y') {
						$texte='Salle';
						$larg_tmp=$pdf->GetStringWidth($texte);
						$larg_tmp_max=$larg_tmp;
						for($j=0;$j<count($salle);$j++) {
							$texte_test=$salle[$j];
							$larg_tmp=$pdf->GetStringWidth($texte_test);
							if($larg_tmp>$larg_tmp_max) {$larg_tmp_max=$larg_tmp;}
						}
						$larg_col5=$larg_tmp_max+4;
						$pdf->Cell($larg_col5,$h_cell,$texte,'LRBT',1,'C');
					}

					//$larg_col3=$largeur_page-$MargeDroite-$MargeGauche-$larg_col1-$larg_col2;
					//$pdf->Cell($larg_col3,$h_cell,$texte,'LRBT',1,'C');

					$pdf->SetFont('DejaVu','',10);
					/*
					while($lig=mysql_fetch_object($res)) {
						$texte=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
						$pdf->Cell($larg_col1,$h_cell,$texte,'LRBT',0,'C');
						if($imprime=='avec_num_anonymat') {
							$texte=$lig->n_anonymat;
							$pdf->Cell($larg_col2,$h_cell,$texte,'LRBT',0,'C');
						}
						$pdf->Cell($larg_col3,$h_cell,'','LRBT',1,'C');
					}
					*/
					for($j=0;$j<count($tab_nom);$j++) {
						if($avec_n_anonymat=='y') {
							$texte=$tab_n_anonymat[$j];
							$pdf->Cell($larg_col1,$h_cell,$texte,'LRBT',0,'C');
						}

						$texte=$tab_nom[$j];
						$pdf->Cell($larg_col2,$h_cell,$texte,'LRBT',0,'L');

						if(($avec_correcteur=='y')||($avec_salle=='y')) {
							$pdf->Cell($larg_col3,$h_cell,$tab_note[$j],'LRBT',0,'C');
						}
						else {
							$pdf->Cell($larg_col3,$h_cell,$tab_note[$j],'LRBT',1,'C');
						}

						if($avec_correcteur=='y') {
							if($avec_salle=='y') {
								$pdf->Cell($larg_col4,$h_cell,$tab_distinct_correcteur[$tab_correcteur[$j]],'LRBT',0,'C');
							}
							else {
								$pdf->Cell($larg_col4,$h_cell,$tab_distinct_correcteur[$tab_correcteur[$j]],'LRBT',1,'C');
							}
						}

						if($avec_salle=='y') {
							$pdf->Cell($larg_col5,$h_cell,$tab_salle[$j],'LRBT',1,'C');
						}
					}

					//===========================================================

					$pdf->SetY($pdf->GetY()+$h_cell);
					$pdf->SetFont('DejaVu','B',10);
					$pdf->Cell($largeur_page,$h_cell,"Statistiques :",'',1,'L');

					$pdf->SetFont('DejaVu','',10);
					$tab_stat=calcul_moy_med($tab_note);

					$pdf->SetFont('DejaVu','B',10);
					$texte="1er quartile";
					$larg_col1=$pdf->GetStringWidth($texte)+4;
					$larg_col2=$pdf->GetStringWidth("20.0")+4;

					$pdf->SetFont('DejaVu','B',10);
					$texte="Moyenne";
					$pdf->Cell($larg_col1,$h_cell,$texte,'LRBT',0,'L');
					$pdf->SetFont('DejaVu','',10);
					$texte=$tab_stat['moyenne'];
					$pdf->Cell($larg_col2,$h_cell,$texte,'LRBT',1,'C');

					$pdf->SetFont('DejaVu','B',10);
					$texte="1er quartile";
					$pdf->Cell($larg_col1,$h_cell,$texte,'LRBT',0,'L');
					$pdf->SetFont('DejaVu','',10);
					$texte=$tab_stat['q1'];
					$pdf->Cell($larg_col2,$h_cell,$texte,'LRBT',1,'C');

					$pdf->SetFont('DejaVu','B',10);
					$texte="Médiane";
					$pdf->Cell($larg_col1,$h_cell,$texte,'LRBT',0,'L');
					$pdf->SetFont('DejaVu','',10);
					$texte=$tab_stat['mediane'];
					$pdf->Cell($larg_col2,$h_cell,$texte,'LRBT',1,'C');

					$pdf->SetFont('DejaVu','B',10);
					$texte="3è quartile";
					$pdf->Cell($larg_col1,$h_cell,$texte,'LRBT',0,'L');
					$pdf->SetFont('DejaVu','',10);
					$texte=$tab_stat['q3'];
					$pdf->Cell($larg_col2,$h_cell,$texte,'LRBT',1,'C');

					$pdf->SetFont('DejaVu','B',10);
					$texte="Min";
					$pdf->Cell($larg_col1,$h_cell,$texte,'LRBT',0,'L');
					$pdf->SetFont('DejaVu','',10);
					$texte=$tab_stat['min'];
					$pdf->Cell($larg_col2,$h_cell,$texte,'LRBT',1,'C');

					$pdf->SetFont('DejaVu','B',10);
					$texte="Max";
					$pdf->Cell($larg_col1,$h_cell,$texte,'LRBT',0,'L');
					$pdf->SetFont('DejaVu','',10);
					$texte=$tab_stat['max'];
					$pdf->Cell($larg_col2,$h_cell,$texte,'LRBT',1,'C');

					//===========================================================

					$compteur++;
				}
			}

			$pref_output_mode_pdf=get_output_mode_pdf();

			$date=date("Ymd_Hi");
			$nom_fich='Bilan_'.$id_epreuve.'_'.$date.'.pdf';
			//send_file_download_headers('application/pdf',$nom_fic);
			//$pdf->Output($nom_fich,'I');
			$pdf->Output($nom_fich,$pref_output_mode_pdf);
			die();

		}
		elseif($mode=="csv") {

			$csv="";
			if($avec_n_anonymat=='y') {
				$csv.='Num.anonymat;';
			}

			$csv.='Nom prénom;';
			
			if($avec_sexe=='y') {
				$csv.='Sexe;';
			}

			$csv.='Note;';

			if($avec_correcteur=='y') {
				$csv.='Correcteur;';
			}

			if($avec_salle=='y') {
				$csv.='Salle;';
			}

			if($imprime=='etendu') {
				$csv.="Classe;";
				$csv.="Professeur habituel;";
	
				$tab_prof_habituel=array();
				$maxper=0;
				for($i=0;$i<count($id_groupe);$i++) {
					$sql="SELECT periode FROM matieres_notes WHERE id_groupe='$id_groupe[$i]' ORDER BY periode DESC LIMIT 1;";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)>0) {
						$lig=mysql_fetch_object($res);
						if($lig->periode>$maxper) {$maxper=$lig->periode;}
					}
	
					$tab_prof_habituel[$i]="";
					$tab_champs=array('profs');
					$tmp_group=get_group($id_groupe[$i]);
					for($k=0;$k<count($tmp_group["profs"]["list"]);$k++) {
						if($k>0) {$tab_prof_habituel[$i].=", ";}
						$tab_prof_habituel[$i].=get_denomination_prof($tmp_group["profs"]["list"][$k]);
					}
				}
				for($i=0;$i<$maxper;$i++) {
					$j=$i+1;
					$csv.="Période $j;";
				}
			}
			$csv.="\n";


			$compteur=0;
			//for($i=0;$i<count($id_salle);$i++) {
			for($i=0;$i<count($id_groupe);$i++) {

				//$sql="SELECT e.nom, e.prenom, e.login, ec.n_anonymat, ec.note, ec.statut, ec.login_prof,  FROM eb_copies ec, eleves e WHERE e.login=ec.login_ele AND ec.id_salle='$id_salle[$i]' AND ec.id_epreuve='$id_epreuve' ORDER BY e.nom,e.prenom;";

				if($imprime=='etendu') {
					$tab_note_per=array();
					$sql="SELECT * FROM matieres_notes WHERE id_groupe='$id_groupe[$i]' ORDER BY periode, login;";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)>0) {
						while($lig=mysql_fetch_object($res)) {
							if($lig->statut=='') {
								$tab_note_per[$lig->periode][$lig->login]=$lig->note;
							}
							else {
								$tab_note_per[$lig->periode][$lig->login]=$lig->statut;
							}
						}
					}
				}

				$sql="SELECT DISTINCT e.nom, e.prenom, e.sexe, e.login, ec.n_anonymat, ec.note, ec.statut, ec.login_prof, es.salle FROM eb_copies ec, eb_salles es, eb_groupes eg, eleves e, j_eleves_groupes jeg WHERE e.login=ec.login_ele AND ec.id_salle=es.id AND ec.id_epreuve='$id_epreuve' AND es.id_epreuve='$id_epreuve' AND eg.id_epreuve='$id_epreuve' AND eg.id_groupe='$id_groupe[$i]' AND jeg.login=e.login AND jeg.id_groupe=eg.id_groupe ORDER BY e.nom,e.prenom;";
				//echo "$sql<br />";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {

					$current_group=$groupe_name[$i]." (".$groupe_classes[$i].")";

					$tab_ele_login=array();
					$tab_nom=array();
					$tab_sexe=array();
					$tab_n_anonymat=array();
					$tab_note=array();
					$tab_correcteur=array();
					$tab_salle=array();
					$tab_distinct_correcteur=array();
					$cpt=0;
					$larg_max=0;
					while($lig=mysql_fetch_object($res)) {
						$tab_ele_login[$cpt]=$lig->login;
						$tab_nom[$cpt]=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
						$tab_sexe[$cpt]=$lig->sexe;
						$tab_n_anonymat[$cpt]=$lig->n_anonymat;

						$tab_salle[$cpt]=$lig->salle;

						if($lig->statut=='v') {
							$tab_note[$cpt]="";
						}
						elseif($lig->statut!='') {
							$tab_note[$cpt]=$lig->statut;
						}
						else {
							$tab_note[$cpt]=$lig->note;
						}

						$tab_correcteur[$cpt]=$lig->login_prof;
						if(!in_array($lig->login_prof,$tab_distinct_correcteur)) {
							$tab_distinct_correcteur["$lig->login_prof"]=get_denomination_prof($lig->login_prof);
						}

						$cpt++;
					}

					for($j=0;$j<count($tab_nom);$j++) {
						if($avec_n_anonymat=='y') {
							$csv.=$tab_n_anonymat[$j].";";
						}

						$csv.=$tab_nom[$j].";";
						
						if($avec_sexe=='y') {
							$csv.=$tab_sexe[$j].';';
						}
						
						$csv.=$tab_note[$j].";";

						if($avec_correcteur=='y') {
							$csv.=$tab_distinct_correcteur[$tab_correcteur[$j]].";";
						}

						if($avec_salle=='y') {
							$csv.=$tab_salle[$j].";";
						}

						if($imprime=='etendu') {
							$tmp_tab=get_class_from_ele_login($tab_ele_login[$j]);
							$csv.=$tmp_tab['liste'].";";

							$csv.=$tab_prof_habituel[$i].";";
							for($k=1;$k<=$maxper;$k++) {
								if(isset($tab_note_per[$k][$tab_ele_login[$j]])) {
									$csv.=$tab_note_per[$k][$tab_ele_login[$j]];
								}
								$csv.=";";
							}
						}

						$csv.="\n";
					}

					//===========================================================

					$compteur++;
				}
			}


			$nom_fic="bilan_epreuve_$id_epreuve.csv";
	
			$now = gmdate('D, d M Y H:i:s') . ' GMT';
			send_file_download_headers('text/x-csv',$nom_fic);	
			//echo $csv;
			echo echo_csv_encoded($csv);
			die();

		}
	}
}


//**************** EN-TETE *****************
$titre_page = "Epreuve blanche: Bilan";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

//echo "<div class='noprint'>\n";
//echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
echo "<p class='bold'><a href='index.php?id_epreuve=$id_epreuve&amp;mode=modif_epreuve'>Retour</a>";
echo " | <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;mode=csv&amp;imprime=standard&amp;avec_n_anonymat=y&amp;avec_correcteur=y&amp;avec_salle=y'>Export CSV</a>";
echo " | <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;mode=csv&amp;imprime=etendu&amp;avec_n_anonymat=y&amp;avec_correcteur=y&amp;avec_salle=y&amp;avec_sexe=y''>Export CSV étendu</a>";
//echo "</p>\n";
//echo "</div>\n";

if(!isset($imprime)) {
	echo "</p>\n";

	// Générer des fiches par salles

	echo "<p class='bold'>Epreuve n°$id_epreuve</p>\n";
	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>L'épreuve choisie (<i>$id_epreuve</i>) n'existe pas.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	
	$lig=mysql_fetch_object($res);
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
	$test1=mysql_query($sql);
	
	$sql="SELECT DISTINCT n_anonymat FROM eb_copies WHERE id_epreuve='$id_epreuve';";
	$test2=mysql_query($sql);
	if(mysql_num_rows($test1)!=mysql_num_rows($test2)) {
		echo "<p style='color:red;'>Les numéros anonymats ne sont pas uniques sur l'épreuve (<i>cela ne devrait pas arriver</i>).</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT login_ele FROM eb_copies WHERE n_anonymat='' AND id_epreuve='$id_epreuve';";
	$test3=mysql_query($sql);
	if(mysql_num_rows($test3)>0) {
		echo "<p style='color:red;'>Un ou des numéros anonymats ne sont pas valides sur l'épreuve&nbsp;: ";
		$cpt=0;
		while($lig=mysql_fetch_object($test3)) {
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
	$test=mysql_query($sql);
	$nb_tmp=mysql_num_rows($test);
	if($nb_tmp==1) {
		echo "<p style='color:red;'>$nb_tmp élève n'est pas affecté dans une salle.</p>\n";
	}
	elseif($nb_tmp>1) {
		echo "<p style='color:red;'>$nb_tmp élèves n'ont pas été affectés dans des salles.</p>\n";
	}
	//========================================================

	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";

	echo "<p class='bold'>Bilan de l'épreuve&nbsp;:</p>\n";

	echo "<p>Choisissez les informations à faire apparaître en plus du nom/prénom de l'élève et de la note&nbsp;:</p>\n";
	echo "<blockquote>\n";
	echo "<input type='checkbox' name='avec_n_anonymat' id='avec_n_anonymat' value='y' /><label for='avec_n_anonymat'>Numéro anonymat</label><br />\n";
	echo "<input type='checkbox' name='avec_correcteur' id='avec_correcteur' value='y' /><label for='avec_correcteur'>Correcteur</label><br />\n";
	echo "<input type='checkbox' name='avec_salle' id='avec_salle' value='y' /><label for='avec_salle'>Salle</label>\n";
	//echo "<input type='checkbox' name='avec_sexe' id='avec_sexe' value='y' /><label for='avec_sexe'>Sexe</label>\n";
	
	echo "</blockquote>\n";

	echo "<input type='hidden' name='mode' value='pdf' />\n";
	echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
	echo "<p><input type='submit' name='imprime' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";
	echo "<p><i>NOTES&nbsp;:</i></p>\n";
	echo "<ul>\n";
	echo "<li style='color:red;'>A FAIRE&nbsp;: Calculer les largeurs dans le PDF pour contrôler que cela tient.<br />Avec des noms longs, on pourrait dépasser.</li>\n";
	echo "</ul>\n";
}

require("../lib/footer.inc.php");
?>
