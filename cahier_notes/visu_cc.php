<?php
/*
*
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
//==============================
// PREPARATIFS boireaus 20080422
// Pour passer à no_anti_inject comme pour les autres saisies d'appréciations
// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$mode_commentaire_20080422="";
//$mode_commentaire_20080422="no_anti_inject";

if($mode_commentaire_20080422=="no_anti_inject") {
	$variables_non_protegees = 'yes';
}
//==============================

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


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
	die("Le module n'est pas activé.");
}

@setlocale(LC_NUMERIC,'C');

require('cc_lib.php');

unset($id_racine);
$id_racine = isset($_POST["id_racine"]) ? $_POST["id_racine"] : (isset($_GET["id_racine"]) ? $_GET["id_racine"] : NULL);
// On teste si le carnet de notes appartient bien à la personne connectée
if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
    header("Location: index.php?msg=$mess");
    die();
}

$appel_cahier_notes=mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$id_groupe=old_mysql_result($appel_cahier_notes, 0, 'id_groupe');
$current_group=get_group($id_groupe);
$periode_num=old_mysql_result($appel_cahier_notes, 0, 'periode');
include "../lib/periodes.inc.php";

unset($id_dev);
$id_dev = isset($_POST["id_dev"]) ? $_POST["id_dev"] : (isset($_GET["id_dev"]) ? $_GET["id_dev"] : NULL);
if(!isset($id_dev)) {
	$mess="$nom_cc non précisé.<br />";
	header("Location: index_cc.php?id_racine=$id_racine&msg=$mess");
	die();
}

$sql="SELECT * FROM cc_dev WHERE id='$id_dev' AND id_groupe='$id_groupe';";
$query=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($query)>0) {
	$id_cn_dev=old_mysql_result($query, 0, 'id_cn_dev');
	$nom_court_dev=old_mysql_result($query, 0, 'nom_court');
	$nom_complet_dev=old_mysql_result($query, 0, 'nom_complet');
	$description_dev=old_mysql_result($query, 0, 'description');
	$precision=old_mysql_result($query, 0, 'arrondir');
}
else {
	header("Location: index.php?msg=".rawurlencode("Le numéro de devoir n est pas associé à ce groupe."));
	die();
}

$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];


//debug_var();
//-------------------------------------------------------------------------------------------------------------------

if(isset($_GET['export_csv'])) {
	$csv="INFO_DEV;$id_dev;$nom_court_dev;$nom_complet_dev;$precision;;".";\r\n";

	$sql="SELECT * FROM cc_eval WHERE id_dev='$id_dev' ORDER BY date, nom_court, nom_complet;";
	//echo "$sql<br />";
	$res_eval=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_eval)==0) {
		$msg="Aucune évaluation n'est associée au $nom_cc n°$id_dev<br />";
	}
	else {
		$cpt=0;
		$tab_eval=array();
		$tab_ele=array();

		$ligne1="INFO_EV;NOM_COURT_EVAL;;;;";
		$ligne2="INFO_EV;DATE_EVAL;;;;";
		$ligne3="INFO_EV;NOTE_SUR_EVAL;;;;";
		$ligne4="INFO_EV;LOGIN;NOM;PRENOM;CLASSE;";

		while($lig_eval=mysqli_fetch_object($res_eval)) {
			$csv.="INFO_EVAL;$lig_eval->id;$lig_eval->nom_court;$lig_eval->nom_complet;".formate_date($lig_eval->date).";$lig_eval->note_sur;".";\r\n";

			$ligne1.=$lig_eval->nom_court.";";
			$ligne2.=formate_date($lig_eval->date).";";
			$ligne3.=strtr($lig_eval->note_sur,'.',',').";";
			$ligne4.=";";

			$tab_eval[$cpt]['id_eval']=$lig_eval->id;
			$tab_eval[$cpt]['note_sur']=$lig_eval->note_sur;

			$sql="SELECT cc.* FROM cc_notes_eval cc WHERE cc.id_eval='$lig_eval->id' ORDER BY cc.login;";
			//echo "$sql<br />";
			$res_en=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_en)>0) {
				while($lig_en=mysqli_fetch_object($res_en)) {

					//if(!in_array($lig_en->login,$tab_ele)) {
					if(!isset($tab_ele[$lig_en->login])) {
						$sql="SELECT c.classe, e.nom, e.prenom FROM classes c, eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe=c.id AND jec.periode='$periode_num' AND e.login='$lig_en->login';";
						//echo "$sql<br />";
						$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_ele)>0) {
							$lig_ele=mysqli_fetch_object($res_ele);
							$tab_ele[$lig_en->login]['classe']=$lig_ele->classe;
							$tab_ele[$lig_en->login]['nom']=$lig_ele->nom;
							$tab_ele[$lig_en->login]['prenom']=$lig_ele->prenom;
						}
						else {
							$tab_ele[$lig_en->login]['classe']='Classe_inconnue';
							$tab_ele[$lig_en->login]['nom']='Nom_inconnu';
							$tab_ele[$lig_en->login]['prenom']='Prenom_inconnu';
						}
					}

					if($lig_en->statut=='v') {
						$tab_ele[$lig_en->login]['eval'][$lig_eval->id]="";
					}
					elseif($lig_en->statut!='') {
						$tab_ele[$lig_en->login]['eval'][$lig_eval->id]=$lig_en->statut;
					}
					else {
						$tab_ele[$lig_en->login]['eval'][$lig_eval->id]=$lig_en->note;
					}
				}
			}

			$cpt++;
		}

		$ligne1.=";\r\n";
		$ligne2.=";\r\n";
		$ligne3.=";\r\n";
		$ligne4.="TOTAL;TOTAL_SUR;MOYENNE;\r\n";

		$csv.=$ligne1;
		$csv.=$ligne2;
		$csv.=$ligne3;
		$csv.=$ligne4;

		foreach($tab_ele as $ele_login => $tmp_tab) {
			$total=0;
			$total_sur=0;

			// Nombre de vraies notes (pas absent, disp, ou -)
			$nb_note=0;

			$csv.="ELEVE;".$ele_login.";".$tmp_tab['nom'].";".$tmp_tab['prenom'].";".$tmp_tab['classe'].";";
			for($i=0;$i<count($tab_eval);$i++) {
				if(isset($tmp_tab['eval'][$tab_eval[$i]['id_eval']])) {
					$csv.=strtr($tmp_tab['eval'][$tab_eval[$i]['id_eval']],'.',',');

					if(($tmp_tab['eval'][$tab_eval[$i]['id_eval']]!='')&&(preg_match('/^[0-9.]*$/',$tmp_tab['eval'][$tab_eval[$i]['id_eval']]))) {
						$total+=$tmp_tab['eval'][$tab_eval[$i]['id_eval']];
						$total_sur+=$tab_eval[$i]['note_sur'];

						$nb_note++;
					}
				}
				$csv.=";";
			}


			if($nb_note>0) {
				$total_aff=strtr($total,'.',',');
			}
			else {
				$total_aff="-";
			}

			$csv.=$total_aff.";".strtr($total_sur,'.',',').";";

			if($total_sur>0) {
				$moy=strtr(precision_arrondi(20*$total/$total_sur,$precision),'.',',');
			}
			else {
				$moy='-';
			}
			$csv.="$moy;\r\n";
		}

		$nom_fic="cc_dev_".$id_dev."_".date("dmY").".csv";
		send_file_download_headers('text/x-csv',$nom_fic);
		//echo $csv;
		echo echo_csv_encoded($csv);
		die();
	}
}
//debug_var();
if(isset($_GET['export_pdf'])) {

	$sql="SELECT * FROM cc_eval WHERE id_dev='$id_dev' ORDER BY date, nom_court, nom_complet;";
	//echo "$sql<br />";
	$res_eval=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_eval)==0) {
		$msg="Aucune évaluation n'est associée au $nom_cc n°$id_dev<br />";
	}
	else {

		//===============================
		// Extraction des infos

		$cpt=0;
		$tab_eval=array();
		$tab_ele=array();

		while($lig_eval=mysqli_fetch_object($res_eval)) {
			$tab_eval[$cpt]['nom_court']=$lig_eval->nom_court;
			$tab_eval[$cpt]['nom_complet']=$lig_eval->nom_complet;
			$tab_eval[$cpt]['date']=formate_date($lig_eval->date);
			$tab_eval[$cpt]['id_eval']=$lig_eval->id;
			$tab_eval[$cpt]['note_sur']=$lig_eval->note_sur;

			$sql="SELECT cc.* FROM cc_notes_eval cc WHERE cc.id_eval='$lig_eval->id' ORDER BY cc.login;";
			//echo "$sql<br />";
			$res_en=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_en)>0) {
				while($lig_en=mysqli_fetch_object($res_en)) {

					//if(!in_array($lig_en->login,$tab_ele)) {
					if(!isset($tab_ele[$lig_en->login])) {
						$sql="SELECT c.classe, e.nom, e.prenom FROM classes c, eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe=c.id AND jec.periode='$periode_num' AND e.login='$lig_en->login';";
						//echo "$sql<br />";
						$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_ele)>0) {
							$lig_ele=mysqli_fetch_object($res_ele);
							$tab_ele[$lig_en->login]['classe']=$lig_ele->classe;
							$tab_ele[$lig_en->login]['nom']=$lig_ele->nom;
							$tab_ele[$lig_en->login]['prenom']=$lig_ele->prenom;
						}
						else {
							$tab_ele[$lig_en->login]['classe']='Classe_inconnue';
							$tab_ele[$lig_en->login]['nom']='Nom_inconnu';
							$tab_ele[$lig_en->login]['prenom']='Prenom_inconnu';
						}
					}

					if($lig_en->statut=='v') {
						$tab_ele[$lig_en->login]['eval'][$lig_eval->id]="";
					}
					elseif($lig_en->statut!='') {
						$tab_ele[$lig_en->login]['eval'][$lig_eval->id]=$lig_en->statut;
					}
					else {
						$tab_ele[$lig_en->login]['eval'][$lig_eval->id]=$lig_en->note;
					}
				}
			}

			$cpt++;
		}

		//===============================

		$professeur_courant=casse_mot($_SESSION['nom'])." ".casse_mot($_SESSION['prenom'],'majf2');

		require_once('../fpdf/fpdf.php');
		
		
		define('LargeurPage','210');
		define('HauteurPage','297');

		$largeur_page=210;
		$hauteur_page=297;

		session_cache_limiter('private');

		$MargeHaut=10;
		$MargeDroite=10;
		$MargeGauche=10;
		$MargeBas=10;

		class rel_PDF extends FPDF
		{
			function Footer()
			{
				global $nom_cc;
				global $id_dev;
				global $professeur_courant;

				$this->SetXY(5,287);
				$this->SetFont('DejaVu','',7.5);

				//$texte=getSettingValue("gepiSchoolName")."  ";
				$texte=ucfirst($nom_cc)." n°$id_dev - ".$professeur_courant;
				$lg_text=$this->GetStringWidth($texte);
				$this->SetXY(10,287);
				$this->Cell(0,5,$texte,0,0,'L');

				$this->Cell(0,5,'Page '.$this->PageNo(),"0",1,'C');
			}

			function EnteteCC()
			{
				global $nom_cc;
				global $id_dev;
				global $professeur_courant;
				//global $fonte, $MargeDroite, $largeur_page, $MargeGauche, $sc_interligne, $salle, $i;
				global $MargeDroite, $largeur_page, $MargeGauche, $sc_interligne, $salle, $i;

				$this->SetFont('DejaVu','B',14);
				$this->SetXY(10,10);
				$this->Cell($largeur_page-$MargeDroite-$MargeGauche,10,getSettingValue('gepiSchoolName').' - Année scolaire '.getSettingValue('gepiYear'),'LRBT',1,'C');

				$x1=$this->GetX();
				$y1=$this->GetY();

				$this->SetFont('DejaVu','B',12);
				$texte=ucfirst($nom_cc)." n°".$id_dev;
				$largeur_tmp=$this->GetStringWidth($texte)+4;
				$this->Cell($largeur_tmp,$this->FontSize*$sc_interligne,$texte,'LRBT',0,'C');
				//$x2=$this->GetX();
				$y2=$this->GetY();

				$this->SetFont('DejaVu','B',12);
				$texte=$professeur_courant;
				$larg_tmp=$sc_interligne*($this->GetStringWidth($texte));
				$this->SetXY($largeur_page-$larg_tmp-$MargeDroite,$y1+($y2-$y1)/4);
				$this->Cell($larg_tmp,$this->FontSize*$sc_interligne,$texte,'LRBT',1,'C');
				//$this->Cell($larg_tmp,$this->FontSize*$sc_interligne,$this->GetY(),'LRBT',1,'C');
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

		$sc_interligne=1.3;

		$h_cell=10;
		$hauteur_max_font=10;
		$hauteur_min_font=4;
		$bordure='LRBT';
		$v_align='C';
		$align='L';

		// Initialisation:
		$x1=10;
		//$y1=30;
		$y1=25;
		//$y2=41;
		$y2=30;

		$Espace_dx=5;
		$Espace_dy=5;
		$largeur_tab=floor(($largeur_page-$MargeDroite-$MargeGauche-1*$Espace_dx)/2);
		$h_cell=8;

		$hauteur_par_eleve=(6+count($tab_eval))*$h_cell;

		$x2=$x1+$largeur_tab+$Espace_dx;

		$num_page=0;

		$compteur=0;

		$num_page++;
		$pdf->AddPage("P");
		$pdf->EnteteCC();
		$pdf->SetXY($x1,$y2);

//echo "plop";

		foreach($tab_ele as $ele_login => $tmp_tab) {
			$total=0;
			$total_sur=0;

			// Nombre de vraies notes (pas absent, disp, ou -)
			$nb_note=0;

			//if($pdf->GetY()+$h_cell+$hauteur_par_eleve>$hauteur_page-$MargeBas) {
			if($pdf->GetY()+$h_cell+$hauteur_par_eleve+$Espace_dx>$hauteur_page-$MargeBas) {
				$num_page++;
				$pdf->AddPage("P");
				$pdf->EnteteCC();
				$pdf->SetXY($x1,$y2);
			}

			$y_reserve=$pdf->GetY();

			if($compteur%2==0) {
				$x_courant=$x1;
			}
			else {
				$x_courant=$x2;
			}

			$pdf->SetFont('DejaVu','B',10);
			//$pdf->SetXY($x1,$y2);

			$texte=ucfirst($nom_cc).' : '.$nom_court_dev;
			$pdf->Cell($largeur_tab,$h_cell,$texte,'LRBT',0,'C');

			//$x=$pdf->GetX();
			$y=$pdf->GetY();
			$pdf->SetXY($x_courant,$y+$h_cell);

			$texte='Classe : '.$tmp_tab['classe'];
			$pdf->Cell($largeur_tab,$h_cell,$texte,'LRBT',0,'C');

			//$x=$pdf->GetX();
			$y=$pdf->GetY();
			$pdf->SetXY($x_courant,$y+$h_cell);

			$texte='Élève : '.$tmp_tab['nom']." ".$tmp_tab['prenom'];
			$pdf->Cell($largeur_tab,$h_cell,$texte,'LRBT',0,'C');

			//$x=$pdf->GetX();
			$y=$pdf->GetY();
			$pdf->SetXY($x_courant,$y+$h_cell);


			$texte='Nom';
			$pdf->Cell(floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
			$texte='Date';
			$pdf->Cell(floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
			$texte='Note';
			$pdf->Cell(floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
			$texte='Sur';
			$pdf->Cell($largeur_tab-3*floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');

			//$x=$pdf->GetX();
			$y=$pdf->GetY();
			$pdf->SetXY($x_courant,$y+$h_cell);

			$pdf->SetFont('DejaVu','',10);
			for($i=0;$i<count($tab_eval);$i++) {
				$nom_ev_courant=$tab_eval[$i]['nom_court'];
				$date_ev_courant=$tab_eval[$i]['date'];
				$note_sur_ev_courant=$tab_eval[$i]['note_sur'];
				if(isset($tmp_tab['eval'][$tab_eval[$i]['id_eval']])) {
					if(($tmp_tab['eval'][$tab_eval[$i]['id_eval']]!='')&&(preg_match('/^[0-9.]*$/',$tmp_tab['eval'][$tab_eval[$i]['id_eval']]))) {
						$total+=$tmp_tab['eval'][$tab_eval[$i]['id_eval']];
						$total_sur+=$tab_eval[$i]['note_sur'];

						$note_ev_courant=strtr($tmp_tab['eval'][$tab_eval[$i]['id_eval']],".",",");

						$nb_note++;
					}
					else {
						$note_ev_courant=$tmp_tab['eval'][$tab_eval[$i]['id_eval']];
					}
				}
				else {
					$note_ev_courant="-";
				}

				$texte=$nom_ev_courant;
				$pdf->Cell(floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
				$texte=$date_ev_courant;
				$pdf->Cell(floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
				$texte=$note_ev_courant;
				$pdf->Cell(floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
				$texte=$note_sur_ev_courant;
				$pdf->Cell($largeur_tab-3*floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
	
				//$x=$pdf->GetX();
				$y=$pdf->GetY();
				$pdf->SetXY($x_courant,$y+$h_cell);
			}


			if($nb_note>0) {
				$total_aff=strtr($total,'.',',');
			}
			else {
				$total_aff="-";
			}

			$pdf->SetFont('DejaVu','B',10);
			$texte='Total';
			$pdf->Cell(floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
			$texte='-';
			$pdf->Cell(floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
			$texte=$total_aff;
			$pdf->Cell(floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
			$texte=$total_sur;
			$pdf->Cell($largeur_tab-3*floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
			$y=$pdf->GetY();
			$pdf->SetXY($x_courant,$y+$h_cell);

			if($total_sur>0) {
				$moy=strtr(precision_arrondi(20*$total/$total_sur,$precision),'.',',');
				//$moy=precision_arrondi(20*$total/$total_sur,$precision);
			}
			else {
				$moy='-';
			}

			/*
			if($total_sur>0) {
				$info_tmp="20*$total/$total_sur";
				$tmp_moy=20*$total/$total_sur;
				echo "moy=$moy<br />\n$info_tmp=$tmp_moy<br />\n";
			}
			*/

			$texte='Moyenne';
			$pdf->Cell(floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
			$texte='-';
			$pdf->Cell(floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
			$texte=$moy;
			$pdf->Cell(floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
			$texte='20';
			$pdf->Cell($largeur_tab-3*floor($largeur_tab/4),$h_cell,$texte,'LRBT',0,'C');
			$y=$pdf->GetY();
			$pdf->SetXY($x_courant,$y+$h_cell);

			if($compteur%2==0) {
				$y=$y_reserve;
				$pdf->SetXY($x2,$y);
			}
			else {
				$y=$pdf->GetY();
				$pdf->SetXY($x1,$y+$Espace_dy);
			}

			$compteur++;
		}

		$pref_output_mode_pdf=get_output_mode_pdf();

		$date=date("Ymd_Hi");
		$nom_fich='evaluation_cumul_'.$id_dev.'_'.$date.'.pdf';
		send_file_download_headers('application/pdf',$nom_fich);
		$pdf->Output($nom_fich,$pref_output_mode_pdf);
		die();
	}
}

if(isset($_GET['export_pdf2'])) {

	define('TopMargin','15');
	define('RightMargin','15');
	define('LeftMargin','15');
	define('BottomMargin','15');
	define('LargeurPage','210');

	require_once('../fpdf/fpdf.php');
	require_once('../fpdf/ex_fpdf.php');

	$pdf=new Ex_FPDF("P","mm","A4");
	$pdf->SetTopMargin(TopMargin);
	$pdf->SetRightMargin(RightMargin);
	$pdf->SetLeftMargin(LeftMargin);
	$pdf->SetAutoPageBreak(true, BottomMargin);
	// Couleur des traits
	$pdf->SetDrawColor(0,0,0);

	// Pour les tests : permet de voir les bords des cadres
	$bord = 0;
	// on appelle une nouvelle page pdf
	$pdf->AddPage("P");
	$pdf->SetFontSize(10);

	$titre=$nom_court_dev." : ".$nom_complet_dev;

	//Positionnement du titre
	$w=$pdf->GetStringWidth($titre)+6;
	$pdf->SetX((LargeurPage-$w)/2);
	//Couleurs du cadre, du fond et du texte
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetTextColor(0,0,0);
	//Titre centré
	$pdf->Cell($w,9,$titre,$bord,1,'C',0);
	//Saut de ligne

	//Initialisation pour le pdf
	$w_pdf=array();
	$w1 = "i"; //largeur de la première colonne
	$w1b = "d"; //largeur de la colonne "classe" si présente
	$w2 = "n"; // largeur des colonnes "notes"
	$w3 = "c"; // largeur des colonnes "commentaires"

	$header_pdf=array();
	$data_pdf=array();

	$sql="SELECT * FROM cc_eval WHERE id_dev='$id_dev' ORDER BY date, nom_court, nom_complet;";
	//echo "$sql<br />";
	$res_eval=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_eval)==0) {
		$msg="Aucune évaluation n'est associée au $nom_cc n°$id_dev<br />";
	}
	else {

		$w_pdf[] = $w1;
		$header_pdf[] = "Evaluation : ";

		//$w_pdf[] = $w1b;
		$w_pdf[] = $w1;
		$header_pdf[] = "Classe";

		$cpt=0;
		$tab_eval=array();
		$tab_ele=array();

		while($lig_eval=mysqli_fetch_object($res_eval)) {
			$w_pdf[] = $w2;
			$header_pdf[] = ($lig_eval->nom_court." (".formate_date($lig_eval->date).")");

			$tab_eval[$cpt]['id_eval']=$lig_eval->id;
			$tab_eval[$cpt]['note_sur']=$lig_eval->note_sur;

			$cpt++;
		}

		$w_pdf[] = $w2;
		$header_pdf[] = "Total";

		$w_pdf[] = $w2;
		$header_pdf[] = "Sur_total";

		$w_pdf[] = $w2;
		$header_pdf[] = "Moyenne";

		//========================================
		$data_pdf[0][] = ("Nom Prénom /Note sur");
		$data_pdf[0][] = "";
		for($loop=0;$loop<count($tab_eval);$loop++) {
			$data_pdf[0][] = $tab_eval[$loop]['note_sur'];
		}
		$data_pdf[0][] = "";
		$data_pdf[0][] = "";
		$data_pdf[0][] = "";
		//========================================

		$tab_tot=array();
		$total_tot=0;
		$tab_moy=array();
		$total_moy=0;
		$nb_moy=0;

		$num_ligne=1;
		$sql="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, cc_eval cce, cc_notes_eval ccne WHERE cce.id_dev='$id_dev' AND cce.id=ccne.id_eval AND ccne.login=e.login ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ele)>0) {
			while($lig=mysqli_fetch_object($res_ele)) {

				$total_ele=0;
				$total_sur=0;

				$data_pdf[$num_ligne][] = $lig->nom." ".$lig->prenom;

				$sql="SELECT c.classe FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='$lig->login' ORDER BY periode DESC LIMIT 1;";
				$res_class=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_class)==0) {
					$data_pdf[$num_ligne][] = "???";
				}
				else {
					$lig_class=mysqli_fetch_object($res_class);
					$data_pdf[$num_ligne][] = $lig_class->classe;
				}

				for($loop=0;$loop<count($tab_eval);$loop++) {

					$sql="SELECT cc.* FROM cc_notes_eval cc WHERE cc.id_eval='".$tab_eval[$loop]['id_eval']."' AND login='$lig->login';";
					//echo "$sql<br />";
					$res_en=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_en)>0) {
						$lig_en=mysqli_fetch_object($res_en);

						if($lig_en->statut=='v') {
							$data_pdf[$num_ligne][]="";
						}
						elseif($lig_en->statut!='') {
							$data_pdf[$num_ligne][]=$lig_en->statut;
						}
						else {
							$data_pdf[$num_ligne][]=strtr($lig_en->note,'.',',');
							$total_ele+=$lig_en->note;
							$total_sur+=$tab_eval[$loop]['note_sur'];
						}
					}
					else {
						// Ca ne devrait pas arriver... sauf élève arrivé en cours d'année et en cours d'éval-cumul.
						$data_pdf[$num_ligne][]="";
					}
				}

				if($total_sur==0) {
					$data_pdf[$num_ligne][] = "-";
					$data_pdf[$num_ligne][] = "-";
					$data_pdf[$num_ligne][] = "-";
				}
				else {
					$data_pdf[$num_ligne][] = strtr($total_ele,'.',',');
					$data_pdf[$num_ligne][] = strtr($total_sur,'.',',');

					$moy=precision_arrondi(20*$total_ele/$total_sur,$precision);
					$data_pdf[$num_ligne][] = strtr($moy,'.',',');

					$total_tot+=$total_ele;
					$tab_tot[]=$total_ele;

					$total_moy+=$moy;
					$tab_moy[]=$moy;

					$nb_moy++;
				}
				$num_ligne++;
			}


			$data_pdf[$num_ligne][] = "Moyennes";
			$data_pdf[$num_ligne][] = "";

			for($loop=0;$loop<count($tab_eval);$loop++) {
				$sql="SELECT round(avg(cc.note),1) as moyenne FROM cc_notes_eval cc WHERE cc.id_eval='".$tab_eval[$loop]['id_eval']."' AND statut='';";
				$res_en=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_en)>0) {
					$lig_en=mysqli_fetch_object($res_en);
					$data_pdf[$num_ligne][]=$lig_en->moyenne;
				}
				else {
					$data_pdf[$num_ligne][]="";
				}
			}

			if($nb_moy==0) {
				$data_pdf[$num_ligne][] = "";
				$data_pdf[$num_ligne][] = "";
				$data_pdf[$num_ligne][] = "";
			}
			else {
				$moy=precision_arrondi($total_tot/$nb_moy,$precision);
				$data_pdf[$num_ligne][] = $moy;

				$data_pdf[$num_ligne][] = "";

				$moy=precision_arrondi($total_moy/$nb_moy,$precision);
				$data_pdf[$num_ligne][] = $moy;
			}
			$num_ligne++;

			//==========================================

			$data_pdf[$num_ligne][] = "Min.:";
			$data_pdf[$num_ligne][] = "";

			for($loop=0;$loop<count($tab_eval);$loop++) {
				$sql="SELECT min(cc.note) as min FROM cc_notes_eval cc WHERE cc.id_eval='".$tab_eval[$loop]['id_eval']."' AND statut='';";
				$res_en=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_en)>0) {
					$lig_en=mysqli_fetch_object($res_en);
					$data_pdf[$num_ligne][]=$lig_en->min;
				}
				else {
					$data_pdf[$num_ligne][]="";
				}
			}

			if($nb_moy==0) {
				$data_pdf[$num_ligne][] = "";
				$data_pdf[$num_ligne][] = "";
				$data_pdf[$num_ligne][] = "";
			}
			else {
				$data_pdf[$num_ligne][] = min($tab_tot);

				$data_pdf[$num_ligne][] = "";

				$data_pdf[$num_ligne][] = min($tab_moy);
			}
			$num_ligne++;

			//==========================================

			$data_pdf[$num_ligne][] = "Max.:";
			$data_pdf[$num_ligne][] = "";

			for($loop=0;$loop<count($tab_eval);$loop++) {
				$sql="SELECT max(cc.note) as max FROM cc_notes_eval cc WHERE cc.id_eval='".$tab_eval[$loop]['id_eval']."' AND statut='';";
				$res_en=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_en)>0) {
					$lig_en=mysqli_fetch_object($res_en);
					$data_pdf[$num_ligne][]=$lig_en->max;
				}
				else {
					$data_pdf[$num_ligne][]="";
				}
			}
			if($nb_moy==0) {
				$data_pdf[$num_ligne][] = "";
				$data_pdf[$num_ligne][] = "";
				$data_pdf[$num_ligne][] = "";
			}
			else {
				$data_pdf[$num_ligne][] = max($tab_tot);

				$data_pdf[$num_ligne][] = "";

				$data_pdf[$num_ligne][] = max($tab_moy);
			}
			$num_ligne++;


			/*
			echo "\$header_pdf<pre>";
			print_r($header_pdf);
			echo "</pre><hr />";
			echo "\$header_pdf<pre>";
			print_r($w_pdf);
			echo "</pre><hr />";
			echo "\$header_pdf<pre>";
			print_r($data_pdf);
			echo "</pre><hr />";
			*/

			$pdf->SetFont('DejaVu','',8);
			$pdf->FancyTable($w_pdf,$header_pdf,$data_pdf,"v","R");

			$pref_output_mode_pdf=get_output_mode_pdf();

			$date=date("Ymd_Hi");
			$nom_fich='evaluation_cumul_'.$id_dev.'_'.$date.'.pdf';
			send_file_download_headers('application/pdf',$nom_fich);
			$pdf->Output($nom_fich,$pref_output_mode_pdf);
			die();

		}
		else {
			$msg="Aucun élève.";
		}

	}
}
//$message_enregistrement = "Les modifications ont été enregistrées !";
//$themessage  = 'Des notes ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Visualisation des notes CC";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();

//unset($_SESSION['chemin_retour']);

?>
<script type="text/javascript" language=javascript>
chargement = false;
</script>

<?php
echo "<p id='LiensSousBandeau' class='bold'>\n";
echo "<a href=\"index_cc.php?id_racine=$id_racine\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>";
echo " | Export <a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine&amp;id_dev=$id_dev&amp;export_csv=y'>CSV</a>";
echo " | Export <a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine&amp;id_dev=$id_dev&amp;export_pdf=y'>PDF</a>";
echo " | Export <a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine&amp;id_dev=$id_dev&amp;export_pdf2=y'>PDF (2)</a>";
//echo "|";
echo "</p>\n";

echo "<h2 class='noprint'>$nom_cc n°$id_dev&nbsp;: $nom_court_dev (<i>$nom_complet_dev</i>)</h2>\n";

$cc_eval=array();
$sql="SELECT * FROM cc_eval WHERE id_dev='$id_dev' ORDER BY date, nom_court;";
$res2=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res2)>0) {
	$i=0;
	while($lig2=mysqli_fetch_object($res2)) {
		$cc_eval[$i]=array();
		$cc_eval[$i]['nom_court']=$lig2->nom_court;
		$cc_eval[$i]['nom_complet']=$lig2->nom_court;
		$cc_eval[$i]['description']=$lig2->description;
		$cc_eval[$i]['note_sur']=$lig2->note_sur;
		$cc_eval[$i]['date']=formate_date($lig2->date);

		$sql="SELECT * FROM cc_notes_eval WHERE id_eval='$lig2->id' ORDER BY login;";
		$res_note=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_note)>0) {
			while($lig_note=mysqli_fetch_object($res_note)) {
				if($lig_note->statut=='v') {
					$cc_eval[$i]['note'][$lig_note->login]='';
				}
				elseif($lig_note->statut!='') {
					$cc_eval[$i]['note'][$lig_note->login]=$lig_note->statut;
				}
				else {
					$cc_eval[$i]['note'][$lig_note->login]=$lig_note->note;
				}
			}
		}

		$i++;
	}
	echo "</ul>\n";
}
else {
	echo "<p>Aucune évaluation n'a encore été définie.</p>";
	require("../lib/footer.inc.php");
	die();
}

$nb_eval=$i;

$liste_eleves = $current_group["eleves"][$periode_num]["users"];

$i=0;
foreach ($liste_eleves as $eleve) {
	$alt=1;
	$eleve_login[$i] = $eleve["login"];
	$eleve_nom[$i] = $eleve["nom"];
	$eleve_prenom[$i] = $eleve["prenom"];
	$eleve_classe[$i] = $current_group["classes"]["classes"][$eleve["classe"]]["classe"];
	$eleve_id_classe[$i] = $current_group["classes"]["classes"][$eleve["classe"]]["id"];

	echo "<div style='float:left; width:30%; margin-left: 2em;; margin-bottom: 2em'>\n";
	echo "<table class='boireaus table_no_split' summary=\"$nom_cc de $eleve_nom[$i] $eleve_prenom[$i]\">\n";
	echo "<tr class='table_no_split'>\n";
	echo "<th colspan='4'><b>$nom_cc</b>&nbsp;: $nom_court_dev</th>\n";
	echo "</tr>\n";

	echo "<tr class='table_no_split'>\n";
	echo "<th colspan='4'><b>Classe</b>&nbsp;: $eleve_classe[$i]</th>\n";
	//echo "<th rowspan='2'><b>$nom_cc</b>&nbsp;: $nom_court_dev</th>\n";
	echo "</tr>\n";

	echo "<tr class='table_no_split'>\n";
	echo "<th colspan='4'><b>Elève</b>&nbsp;: $eleve_nom[$i] $eleve_prenom[$i]</th>\n";
	echo "</tr>\n";

	echo "<tr class='table_no_split'>\n";
	echo "<th>Nom</th>\n";
	echo "<th>Date</th>\n";
	echo "<th>Note</th>\n";
	echo "<th>Sur</th>\n";
	echo "</tr>\n";

	$total=0;
	$total_sur=0;

	// Nombre de vraies notes (pas absent, disp, ou -)
	$nb_note=0;
	for($j=0;$j<count($cc_eval);$j++) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover table_no_split'>\n";
		echo "<td>".$cc_eval[$j]['nom_court']."</td>\n";
		echo "<td>".$cc_eval[$j]['date']."</td>\n";
		echo "<td>";
		if(isset($cc_eval[$j]['note'][$eleve_login[$i]])) {
			echo $cc_eval[$j]['note'][$eleve_login[$i]];
			if(($cc_eval[$j]['note'][$eleve_login[$i]]!='')&&(preg_match('/^[0-9.]*$/',$cc_eval[$j]['note'][$eleve_login[$i]]))) {
				$total+=$cc_eval[$j]['note'][$eleve_login[$i]];
				$total_sur+=$cc_eval[$j]['note_sur'];

				$nb_note++;
			}
		}
		echo "</td>\n";
		echo "<td>";
		echo $cc_eval[$j]['note_sur'];
		echo "</td>\n";
		echo "</tr>\n";
	}

	echo "<tr class='table_no_split'>\n";
	echo "<th>Total</th>\n";
	echo "<th>-</th>\n";


	if($nb_note>0) {
		$total_aff=strtr($total,'.',',');
	}
	else {
		$total_aff="-";
	}


	echo "<th>$total_aff</th>\n";
	echo "<th>$total_sur</th>\n";
	echo "</tr>\n";

	echo "<tr class='table_no_split'>\n";
	echo "<th>Moyenne</th>\n";
	echo "<th>-</th>\n";
	if($total_sur!=0) {
		$moy=strtr(precision_arrondi(20*strtr($total,",",".")/strtr($total_sur,",","."),$precision),'.',',');

		//$info_tmp="20*$total/$total_sur";
		//$tmp_moy=20*$total/$total_sur;
	}
	else {
		$moy='-';
	}
	echo "<th>$moy";
	//echo "<br />$info_tmp<br />$tmp_moy";
	echo "</th>\n";
	echo "<th>20</th>\n";
	echo "</tr>\n";

	echo "</table>\n";
	//echo "<br />\n";
	echo "</div>\n";

	$i++;
}

echo "<script type='text/javascript'>
	document.getElementById('bandeau').className+=' noprint';
	if(document.getElementById('essaiMenu')) {document.getElementById('essaiMenu').className+=' noprint';}
	document.getElementById('LiensSousBandeau').className+=' noprint';
</script>

<style type='text/css'>
// Ca n'a pas l'air de fonctionner
.table_no_split {
	page-break-inside: avoid;
}
</style>\n";

require("../lib/footer.inc.php");
?>
