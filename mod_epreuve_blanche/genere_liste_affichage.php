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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/genere_liste_affichage.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/genere_liste_affichage.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Epreuve blanche: Génération liste affichage',
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
					$csv.="Liste d'affichage;Salle $salle[$i];\n";
					
					switch ($imprime) {
						case "sans_num_anonymat":
							$csv.="NOM;PRENOM;NAISSANCE;CLASSE;SALLE;\n";
						break;
						case "avec_num_anonymat":
							$csv.="NUM_ANONYMAT;NOM;PRENOM;NAISSANCE;CLASSE;SALLE\n";
						break;
					}
					
					while($lig=mysqli_fetch_object($res)) {
						
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

			session_cache_limiter('private');

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

				function EnteteListe()
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
					$texte="Salle $salle[$i]";
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

			// hauteur de chaque ligne d'information
			$hauteur_ligne = 10;
			
			$fonte='DejaVu';
			$sc_interligne=1.3;

			$num_page=0;

			$compteur=0;
			for($i=0;$i<count($id_salle);$i++) {

				$decompte_page=$num_page;

				$sql="SELECT DISTINCT e.nom, e.prenom, e.login, e.naissance, c.classe, ec.n_anonymat FROM eb_copies ec, eleves e, j_eleves_classes jec, classes c WHERE e.login=ec.login_ele AND ec.id_salle='$id_salle[$i]' AND ec.id_epreuve='$id_epreuve' AND jec.id_classe=c.id AND jec.login=e.login ORDER BY e.nom,e.prenom,e.naissance;";
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

					$pdf->EnteteListe();
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
					$tab_naissance=array();
					$tab_classe=array();
					$tab_n_anonymat=array();
					$cpt=0;
					$larg_max=0;
					while($lig=mysqli_fetch_object($res)) {
						$tab_nom[$cpt]=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
						$tab_n_anonymat[$cpt]=$lig->n_anonymat;

						$tab_naissance[$cpt]=formate_date($lig->naissance);
						$tab_classe[$cpt]=$lig->classe;

						$larg_tmp=$pdf->GetStringWidth($tab_nom[$cpt]);
						if($larg_tmp>$larg_max) {$larg_max=$larg_tmp;}
						$cpt++;
					}

					$larg_col2=0;
					if($imprime=='avec_num_anonymat') {
						$texte='Numéro';
						$larg_col2=$pdf->GetStringWidth($texte)+4;
						$pdf->Cell($larg_col2,10,$texte,'LRBT',0,'C');
					}

					//$pdf->SetFont('DejaVu','B',10);
					$texte='Nom prénom';
					//$larg_col1=$pdf->GetStringWidth($texte);
					$larg_col1=$larg_max+4;
					$pdf->Cell($larg_col1,10,$texte,'LRBT',0,'C');

					$texte='Naissance';
					$larg_col3=round(($largeur_page-$MargeDroite-$MargeGauche-$larg_col1-$larg_col2)/3);
					$pdf->Cell($larg_col3,10,$texte,'LRBT',0,'C');

					$texte='Classe';
					$pdf->Cell($larg_col3,10,$texte,'LRBT',0,'C');

					$texte='Salle';
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
							$pdf->EnteteListe();
							$pdf->SetXY($x1,$y2);

							if($imprime=='avec_num_anonymat') {
								$texte='Numéro';
								$pdf->Cell($larg_col2,$hauteur_ligne,$texte,'LRBT',0,'C');
							}
		
							$texte='Nom prénom';
							$pdf->Cell($larg_col1,$hauteur_ligne,$texte,'LRBT',0,'C');
		
							$texte='Naissance';
							$pdf->Cell($larg_col3,$hauteur_ligne,$texte,'LRBT',0,'C');
		
							$texte='Classe';
							$pdf->Cell($larg_col3,$hauteur_ligne,$texte,'LRBT',0,'C');
		
							$texte='Salle';
							$pdf->Cell($larg_col3,$hauteur_ligne,$texte,'LRBT',1,'C');
						}

						$pdf->SetFont('DejaVu','B',10);

						$largeur_dispo=$larg_col1;
						$h_cell=$hauteur_ligne;
						$hauteur_max_font=10;
						$hauteur_min_font=4;
						$bordure='LRBT';
						$v_align='C';
						$align='L';

						//$pdf->Cell($larg_col1,10,$texte,'LRBT',0,'C');
						$x=$pdf->GetX();
						$y=$pdf->GetY();

						if($imprime=='avec_num_anonymat') {
							$texte=$tab_n_anonymat[$j];
							$pdf->Cell($larg_col2,$hauteur_ligne,$texte,'LRBT',0,'C');
						}

						$x=$pdf->GetX();
						$y=$pdf->GetY();
						$texte=$tab_nom[$j];
						cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align);
						//$pdf->Cell($largeur_dispo,10,$texte,'LRBT',0,'C');
						$pdf->SetXY($x+$largeur_dispo,$y);

						$x=$pdf->GetX();
						$y=$pdf->GetY();
						$texte=$tab_naissance[$j];
						//cell_ajustee($texte,$x,$y,$larg_col3,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,'C');
						$pdf->Cell($larg_col3,$hauteur_ligne,$texte,'LRBT',0,'C');
						$pdf->SetXY($x+$larg_col3,$y);

						$x=$pdf->GetX();
						$y=$pdf->GetY();
						$texte=$tab_classe[$j];
						//cell_ajustee($texte,$x,$y,$larg_col3,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,'C');
						$pdf->Cell($larg_col3,$hauteur_ligne,$texte,'LRBT',0,'C');
						$pdf->SetXY($x+$larg_col3,$y);

						$x=$pdf->GetX();
						$y=$pdf->GetY();
						$texte=$salle_courante;
						//cell_ajustee($texte,$x,$y,$larg_col3,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,'C');
						$pdf->Cell($larg_col3,$hauteur_ligne,$texte,'LRBT',0,'C');

						$pdf->SetXY($x1,$y+$h_cell);
					}

					$compteur++;

				}
			}

			//$pdf->Footer();

			$pref_output_mode_pdf=get_output_mode_pdf();

			$date=date("Ymd_Hi");
			$nom_fich='Liste_affichage_'.$id_epreuve.'_'.$date.'.pdf';
			send_file_download_headers('application/pdf',$nom_fich);
			$pdf->Output($nom_fich,$pref_output_mode_pdf);
			die();

		}
	}
}


//**************** EN-TETE *****************
$titre_page = "Epreuve blanche: Affichage";
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
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=sans_num_anonymat&amp;mode=csv".add_token_in_url()."' target='_blank'>Avec les colonnes 'NOM;PRENOM;NAISSANCE;CLASSE;SALLE'</a></li>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=avec_num_anonymat&amp;mode=csv".add_token_in_url()."' target='_blank'>Avec les colonnes 'NUM_ANONYMAT;NOM;PRENOM;NAISSANCE;CLASSE;SALLE'</a></li>\n";
		echo "</ul>\n";
	echo "</li>\n";
	echo "<li><b>PDF</b>&nbsp;:\n";
	 	echo "<ul>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=sans_num_anonymat&amp;mode=pdf".add_token_in_url()."' target='_blank'>Avec les colonnes 'NOM;PRENOM;NAISSANCE;CLASSE;SALLE'</a></li>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=avec_num_anonymat&amp;mode=pdf".add_token_in_url()."' target='_blank'>Avec les colonnes 'NUM_ANONYMAT;NOM;PRENOM;NAISSANCE;CLASSE;SALLE'</a></li>\n";
		echo "</ul>\n";
	echo "</li>\n";
	echo "</ul>\n";
}

require("../lib/footer.inc.php");
?>
