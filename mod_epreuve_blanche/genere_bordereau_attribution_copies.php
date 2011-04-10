<?php
/* $Id: genere_liste_affichage.php 6719 2011-03-28 17:31:25Z crob $ */
/*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/genere_bordereau_attribution_copies.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/genere_bordereau_attribution_copies.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Epreuve blanche: Génération des bordereaux d''atribution des copies aux correcteurs',
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

include('lib_eb.php');

if(isset($imprime)) {
	check_token();

	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$msg="L'épreuve n°$id_epreuve n'existe pas.";
	}
	else {
		$lig_ep=mysql_fetch_object($res);
		$intitule_epreuve=$lig_ep->intitule;
		$date_epreuve=formate_date("$lig_ep->date");
	
		$sql="SELECT DISTINCT login_prof FROM eb_copies WHERE id_epreuve='$id_epreuve' ORDER BY login_prof;";
		$res_login_prof=mysql_query($sql);
		while($lig_login_prof=mysql_fetch_object($res_login_prof)) {
			$login_profs[]=$lig_login_prof->login_prof;
		}

	
		if($mode=='csv') {
/*			$csv="";
			for($i=0;$i<count($id_salle);$i++) {
				//$sql="SELECT e.nom, e.prenom, e.login, ec.n_anonymat FROM eb_copies ec, eleves e WHERE e.login=ec.login_ele AND ec.id_salle='$id_salle[$i]' AND ec.id_epreuve='$id_epreuve' ORDER BY e.nom,e.prenom;";
				$sql="SELECT DISTINCT e.nom, e.prenom, e.login, e.naissance, c.classe, ec.n_anonymat FROM j_eleves_classes jec, eb_copies ec, eleves e, classes c WHERE e.login=ec.login_ele AND ec.id_salle='$id_salle[$i]' AND ec.id_epreuve='$id_epreuve' AND jec.id_classe=c.id AND jec.login=e.login ORDER BY e.nom, e.prenom;";
				//echo "$sql<br />";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					//$csv.="Epreuve:;$intitule_epreuve ($date_epreuve);\n";
					$csv.="Epreuve:;$intitule_epreuve;\n";
					$csv.="Date:;$date_epreuve;\n";
					$csv.="Liste d'affichage;Salle $salle[$i];\n";
					
					switch ($imprime) {
						case "sans_num_anonymat":
							$csv.="NOM;PRENOM;NAISSANCE;CLASSE;SALLE;\n";
						break;
						case "avec_num_anonymat":
							$csv.="NUM_ANONYMAT;NOM;PRENOM;NAISSANCE;CLASSE;SALLE\n";
						break;
					}
					
					while($lig=mysql_fetch_object($res)) {
						
						switch ($imprime) {
							case "sans_num_anonymat":
								// PROBLEME: ON PEUT AVOIR DES HOMONYMES DANS UNE MÊME SALLE...
								$csv.=casse_mot($lig->nom).";".casse_mot($lig->prenom,'majf2').";".formate_date($lig->naissance).";".$lig->classe.";".$salle[$i].";\n";
							break;
							case "avec_num_anonymat":
								$csv.=$lig->n_anonymat.";".casse_mot($lig->nom).";".casse_mot($lig->prenom,'majf2').";".formate_date($lig->naissance).";".$lig->classe.";".$salle[$i].";\n";
							break;
						}
					}
				}
			}
			$nom_fic="liste_affichage_$id_epreuve.csv";
	
			$now = gmdate('D, d M Y H:i:s') . ' GMT';
			send_file_download_headers('text/x-csv',$nom_fic);
			echo $csv;
			die();
*/	
		}
		elseif($mode=='pdf') {

			require('../fpdf/fpdf.php');
			require('../fpdf/ex_fpdf.php');
			
			define('FPDF_FONTPATH','../fpdf/font/');
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
					global $prof_courant;
					//global $num_page;
					//global $decompte_page;

					$this->SetXY(5,287);
					$this->SetFont('arial','',7.5);

					//$texte=getSettingValue("gepiSchoolName")."  ";
					if ($prof_courant=='') {
						$nom_complet = 'de la (des) copie(s) non attribuée(s)';
					} else {
						$nom_complet = "des copies de ".civ_nom_prenom($prof_courant,'');
					}
					
					$texte=$intitule_epreuve." ($date_epreuve) - Bordereau de répartition ".$nom_complet;
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
					global $prof_courant;
					global $fonte, $MargeDroite, $largeur_page, $MargeGauche, $sc_interligne, $salle, $i;
					//global $num_page;
					//global $decompte_page;

					$this->SetFont($fonte,'B',14);
					$this->Setxy(10,10);
					$this->Cell($largeur_page-$MargeDroite-$MargeGauche,20,getSettingValue('gepiSchoolName').' - Année scolaire '.getSettingValue('gepiYear'),'LRBT',1,'C');

					$x1=$this->GetX();
					$y1=$this->GetY();

					$this->SetFont($fonte,'B',12);
					$texte='Epreuve : ';
					$largeur_tmp=$this->GetStringWidth($texte);
					$this->Cell($largeur_tmp,$this->FontSize*$sc_interligne,$texte,'',0,'L');
					$this->SetFont($fonte,'',12);
					$texte=$intitule_epreuve;
					$this->Cell($this->GetStringWidth($texte),$this->FontSize*$sc_interligne,$texte,'',1,'L');

					$this->SetFont($fonte,'B',12);
					$texte='Date : ';
					$this->Cell($largeur_tmp,$this->FontSize*$sc_interligne,$texte,'',0,'L');
					$this->SetFont($fonte,'',12);
					$texte=$date_epreuve;
					$this->Cell($this->GetStringWidth($texte),$this->FontSize*$sc_interligne,$texte,'',1,'L');

					//$x2=$this->GetX();
					$y2=$this->GetY();

					$this->SetFont($fonte,'B',12);
					if ($prof_courant=='') {
						$nom_complet = 'Copie(s) non attribuée(s)';
					} else {
						$nom_complet = "Copies de ".civ_nom_prenom($prof_courant,'');
					}
					$texte=$nom_complet;
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

			$fonte='arial';
			$sc_interligne=1.3;

			$num_page=0;

			$compteur=0;
			for($i=0;$i<sizeof($login_profs);$i++) { //nombre de correcteurs sur l'épreuve
				$decompte_page=$num_page;

				$sql="SELECT n_anonymat FROM eb_copies WHERE id_epreuve='$id_epreuve' AND login_prof='$login_profs[$i]' ORDER BY n_anonymat";
				//echo "$sql<br />";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {

					//if($compteur>0) {$pdf->Footer();}
					$num_page++;
					$pdf->AddPage("P");
					$prof_courant=$login_profs[$i];

					// Initialisation:
					$x1="";
					$y1="";
					$y2="";

					//entete du PDF
					$pdf->EnteteListe();

					$x1=10;
					$y1=30;
					$y2=41;

					$pdf->SetXY($x1,$y2);

					$pdf->SetFont($fonte,'B',10);
					$tab_n_anonymat=array();
					$cpt=0;
					$larg_max=0;
					while($lig=mysql_fetch_object($res)) {       //Les numéros d'anonymat pour le professeur en cours
						$tab_n_anonymat[$cpt]=$lig->n_anonymat;
						$cpt++;
					}
					
					$pdf->SetFont($fonte,'B',10);
					$texte='Num.';
					$larg_col0=$pdf->GetStringWidth($texte);
					$larg_col1=$larg_col0+4;
					$pdf->Cell($larg_col1,8,$texte,'LRBT',0,'C');
				
					$pdf->SetFont($fonte,'B',10);
					$texte='Numero Copie';
					$larg_col1=$pdf->GetStringWidth($texte);
					$larg_col1=$larg_col1+4;
					$pdf->Cell($larg_col1,8,$texte,'LRBT',0,'C');

					$texte='Pointage Départ';
					$larg_col3=round(($largeur_page-$MargeDroite-$MargeGauche-$larg_col1-$larg_col0)/2);
					$pdf->Cell($larg_col3,8,$texte,'LRBT',0,'C');

					$texte='Pointage Retour';
					$pdf->Cell($larg_col3,8,$texte,'LRBT',1,'C');

					$pdf->SetFont($fonte,'B',10);
				
					for($j=0;$j<count($tab_n_anonymat);$j++) {
						if($pdf->GetY()>270) {
							$pdf->AddPage("P");
							$pdf->EnteteListe();
							$pdf->SetXY($x1,$y2);
							
							$pdf->SetFont($fonte,'B',10);
							$texte='Num.';
							$pdf->Cell($larg_col0+4,8,$texte,'LRBT',0,'C');
		
							$texte='Numéro copie';
							$pdf->Cell($larg_col1,8,$texte,'LRBT',0,'C');
		
							$texte='Pointage départ';
							$pdf->Cell($larg_col3,8,$texte,'LRBT',0,'C');
		
							$texte='Pointage Retour';
							$pdf->Cell($larg_col3,8,$texte,'LRBT',1,'C');
						}

						$pdf->SetFont($fonte,'B',10);
						
						$x=$pdf->GetX();
						$y=$pdf->GetY();
						
						$largeur_dispo=$larg_col0+4;
						$h_cell=5;
						$hauteur_max_font=10;
						$hauteur_min_font=4;
						$bordure='LRBT';
						$v_align='C';
						$align='L';
						$texte=$j+1; //le numéro d'ordre de la copie
						cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align);
						//$pdf->Cell($largeur_dispo,10,$texte,'LRBT',0,'C');
						$pdf->SetXY($x+$largeur_dispo,$y);

						
						$largeur_dispo=$larg_col1;
						$h_cell=5;
						$hauteur_max_font=10;
						$hauteur_min_font=4;
						$bordure='LRBT';
						$v_align='C';
						$align='L';

						//$pdf->Cell($larg_col1,10,$texte,'LRBT',0,'C');
						
						$x=$pdf->GetX();
						$y=$pdf->GetY();
						$texte=$tab_n_anonymat[$j];
						cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align);
						//$pdf->Cell($largeur_dispo,10,$texte,'LRBT',0,'C');
						$pdf->SetXY($x+$largeur_dispo,$y);

						$x=$pdf->GetX();
						$y=$pdf->GetY();
						$texte='';
						cell_ajustee($texte,$x,$y,$larg_col3,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,'C');
						//$pdf->Cell($larg_col3,10,$texte,'LRBT',0,'C');
						$pdf->SetXY($x+$larg_col3,$y);

						$x=$pdf->GetX();
						$y=$pdf->GetY();
						$texte='';
						cell_ajustee($texte,$x,$y,$larg_col3,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,'C');
						//$pdf->Cell($larg_col3,10,$texte,'LRBT',0,'C');
						$pdf->SetXY($x+$larg_col3,$y);

						$pdf->SetXY($x1,$y+$h_cell);
					}
					$compteur++;
				}
			}

			//$pdf->Footer();

			$date=date("Ymd_Hi");
			$nom_fich='Liste_affichage_'.$id_epreuve.'_'.$date.'.pdf';
			send_file_download_headers('application/pdf',$nom_fich);
			$pdf->Output($nom_fich,'I');
			die();

		}
	}
}


//**************** EN-TETE *****************
$titre_page = "Epreuve blanche: Génération des bordereaux de répartition des copies";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
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
		echo "Aucune description de saisie.<br />\n";
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

	echo "<p>Choisissez le type de bordereau à imprimer&nbsp;:</p>\n";
	echo "<ul>\n";
/*
	echo "<li><b>CSV</b>&nbsp;:\n";
	 	echo "<ul>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=sans_num_anonymat&amp;mode=csv".add_token_in_url()."'>Avec les colonnes 'NOM;PRENOM;NAISSANCE;CLASSE;SALLE'</a></li>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=avec_num_anonymat&amp;mode=csv".add_token_in_url()."'>Avec les colonnes 'NUM_ANONYMAT;NOM;PRENOM;NAISSANCE;CLASSE;SALLE'</a></li>\n";
		echo "</ul>\n";
	echo "</li>\n";
*/
	echo "<li><b>PDF</b>&nbsp;:\n";
	 	echo "<ul>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=sans_num_anonymat&amp;mode=pdf".add_token_in_url()."'>Avec des colonnes pour faciliter le pointage (départ et retour)</a></li>\n";
		//echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=avec_num_anonymat&amp;mode=pdf".add_token_in_url()."'>Avec les colonnes 'NUM_ANONYMAT;NOM;PRENOM;NAISSANCE;CLASSE;SALLE'</a></li>\n";
		echo "</ul>\n";
	echo "</li>\n";
	echo "</ul>\n";
}

require("../lib/footer.inc.php");
?>
