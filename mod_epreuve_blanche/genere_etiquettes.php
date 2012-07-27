<?php
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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/genere_etiquettes.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/genere_etiquettes.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Epreuve blanche: Génération étiquettes',
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
//$imprime=isset($_POST['imprime']) ? $_POST['imprime'] : (isset($_GET['imprime']) ? $_GET['imprime'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

$sql="CREATE TABLE IF NOT EXISTS eb_param (
id int(11) unsigned NOT NULL auto_increment,
type VARCHAR( 255 ) NOT NULL ,
nom VARCHAR( 255 ) NOT NULL ,
valeur smallint(6) unsigned NOT NULL ,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysql_query($sql);

// Enregistrement des valeurs
if((isset($mode))&&($mode=='parametrer')) {
	check_token();

	$tab_param=array('MargeHaut',
'MargeDroite',
'MargeGauche',
'MargeBas',
'haut_etq',
'larg_etq',
'dy');

	$cpt=0;
	for($i=0;$i<count($tab_param);$i++) {
		$nom=$tab_param[$i];
		$$nom=isset($_POST["$nom"]) ? $_POST["$nom"] : NULL;

		//echo "$nom -&gt; ".$$nom;
		//if(ereg("^[0-9]*$",$$nom)) {
		if((isset($$nom))&&(preg_match("/^[0-9]*$/",$$nom))) {
			//echo " valide";
			//echo "<br />";
			$sql="DELETE FROM eb_param WHERE type='etiquette' AND nom='$nom';";
			//echo "$sql<br />";
			$del=mysql_query($sql);
			$sql="INSERT INTO eb_param SET valeur='".$$nom."', type='etiquette', nom='$nom';";
			//echo "$sql<br />";
			$insert=mysql_query($sql);
			$cpt++;
		}
		//echo "<br />";
	}

	if($cpt>0) {$msg="$cpt enregistrements effectués.";}
}

// Initialisation des valeurs
$largeur_page=210;
$hauteur_page=297;

$MargeHaut=10;
$MargeDroite=10;
$MargeGauche=10;
$MargeBas=10;

$x0=$MargeGauche;
$y0=$MargeHaut;

// Hauteur de l'étiquette
$haut_etq=30;

// Largeur de l'étiquette
$larg_etq=80;

// Espace vertical entre deux étiquettes
$dy=10;

// Récupération des valeurs enregistrées
$sql="SELECT * FROM eb_param WHERE type='etiquette';";
$res=mysql_query($sql);
while($lig=mysql_fetch_object($res)) {
	$nom=$lig->nom;
	$$nom=$lig->valeur;
}
// Espace horizontal entre deux étiquettes
$dx=$largeur_page-$MargeDroite-$MargeGauche-2*$larg_etq;
// AJOUTER UN TEST
// Si $dx est négatif:
// - réduire la largeur de l'étiquette
// ou 
// - ne mettre qu'une étiquette par largeur de page

// Abscisse de l'étiquette de la colonne de droite
$x1=$x0+$larg_etq+$dx;

// Pour pouvoir ne pas imprimer le Footer
$no_footer="n";


if((isset($mode))&&($mode=='imprime')) {
	check_token();

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
		$res_salle=mysql_query($sql);
		while($lig_salle=mysql_fetch_object($res_salle)) {
			$salle[]=$lig_salle->salle;
			$id_salle[]=$lig_salle->id;
		}

		if (!defined('FPDF_VERSION')) {
		  require_once('../fpdf/fpdf.php');
		}
		
		
		//define('LargeurPage','210');
		//define('HauteurPage','297');
		define('LargeurPage',$largeur_page);
		define('HauteurPage',$hauteur_page);

		//$largeur_page=210;
		//$MargeHaut=10;
		//$MargeDroite=10;
		//$MargeGauche=10;
		//$MargeBas=10;

		session_cache_limiter('private');

		class rel_PDF extends FPDF
		{
			function Footer()
			{
				global $intitule_epreuve;
				global $date_epreuve;
				global $salle_courante;
				//global $num_page;
				//global $decompte_page;
				global $no_footer;
				global $hauteur_page;

				if($no_footer=='n') {

					$this->SetXY(5,$hauteur_page-10);
					$this->SetFont('DejaVu','',7.5);
	
					//$texte=getSettingValue("gepiSchoolName")."  ";
					$texte=$intitule_epreuve." ($date_epreuve) - ".$salle_courante;
					$lg_text=$this->GetStringWidth($texte);
					$this->SetXY(10,$hauteur_page-10);
					$this->Cell(0,5,$texte,0,0,'L');
	
					//$this->SetY(287);
					$this->Cell(0,5,'Page '.$this->PageNo(),"0",1,'C');
					//$this->Cell(0,5,'Page '.($this->PageNo()-$decompte_page),"0",1,'C');
					//$this->Cell(0,5,'Page '.$this->PageNo().'-'.$decompte_page.'='.($this->PageNo()-$decompte_page),"0",1,'C');
					//$this->Cell(0,5,'Page '.$num_page,"0",1,'C');
				}
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

		$fonte='DejaVu';
		$fs=10;
		$sc_interligne=1.3;

		$num_page=0;

		$compteur=0;
		for($i=0;$i<count($id_salle);$i++) {
			$decompte_page=$num_page;

			$sql="SELECT e.nom, e.prenom, e.naissance, e.login, ec.n_anonymat FROM eb_copies ec, eleves e WHERE e.login=ec.login_ele AND ec.id_salle='$id_salle[$i]' AND ec.id_epreuve='$id_epreuve' ORDER BY e.nom, e.prenom;";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {

				//if($compteur>0) {$pdf->Footer();}
				$num_page++;
				$pdf->AddPage("P");
				$salle_courante=$salle[$i];

				//Entête du PDF
				//$pdf->SetLineWidth(0.7);
				/*
				$pdf->SetFont('DejaVu','B',14);
				$pdf->Setxy(10,10);
				$pdf->Cell($largeur_page-$MargeDroite-$MargeGauche,20,getSettingValue('gepiSchoolName').' - Année scolaire '.getSettingValue('gepiYear'),'LRBT',1,'C');

				$x1=$pdf->GetX();
				$y1=$pdf->GetY();
				*/

				$pdf->SetFont('DejaVu','',10);
				$pdf->SetXY($x0,$y0);

				// Paramètres pour cell_ajustee()
				$largeur_dispo=$larg_etq/2;
				$h_cell=$haut_etq/2;
				$hauteur_max_font=10;
				$hauteur_min_font=4;
				$bordure='R';
				$v_align='C';
				$align='L';

				$cpt=0;
				while($lig=mysql_fetch_object($res)) {

					$y=$y0+floor($cpt/2)*($haut_etq+$dy);

					$texte='';
					if($cpt/2==round($cpt/2)) {

						$ajout_test="";
						if($y+$haut_etq>$hauteur_page-$MargeBas) {
							$num_page++;
							$pdf->AddPage("P");
							$y=$y0;
							//$ajout_test="Chgt page";
							$cpt=0;
						}

						$x=$x0;

						$texte.=$ajout_test;
					}
					else {
						$x=$x1;
					}
					// Cadre de l'étiquette
					$pdf->SetXY($x,$y);
					$pdf->Cell($larg_etq,$haut_etq,$texte,'LRBT',1,'L');
					$pdf->SetXY($x,$y);

					// Partie haut/gauche de l'étiquette
					$texte="Epreuve $id_epreuve:\n";
					$texte.="$intitule_epreuve ($date_epreuve)\n";
					//cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align='C',$align='L',$increment=0.3,$r_interligne=0.3) {
					cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align);

					// Partie haut/droite de l'étiquette
					$x+=$largeur_dispo;
					$texte="Epreuve $id_epreuve:\n";
					$texte.="$intitule_epreuve ($date_epreuve)\n";
					cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align);


					// Partie bas/gauche de l'étiquette
					$x-=$largeur_dispo;
					$y=$y+$h_cell;
					$pdf->SetXY($x,$y);
					$texte=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2')."\n";
					$texte.="Naissance: ".formate_date($lig->naissance)."\n";
					$texte.="Numéro: ".$lig->n_anonymat;
					//cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align='C',$align='L',$increment=0.3,$r_interligne=0.3) {
					cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align);

					// Partie bas/droite de l'étiquette
					$x+=$largeur_dispo;
					$texte="Numéro: ".$lig->n_anonymat;
					//cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align='C',$align='L',$increment=0.3,$r_interligne=0.3) {
					cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align);

					$cpt++;
				}

				$compteur++;
			}
		}

		//$pdf->Footer();

		$date=date("Ymd_Hi");
		$nom_fich='Etiquettes_'.$id_epreuve.'_'.$date.'.pdf';
		send_file_download_headers('application/pdf',$nom_fich);	
		$pdf->Output($nom_fich,'I');
		die();

	}
}

include('lib_eb.php');

//**************** EN-TETE *****************
$titre_page = "Epreuve blanche: Etiquettes";
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

if((!isset($mode))||($mode!='parametrer')) {
	echo "</p>\n";

	//========================================================

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


	echo "<p class='bold'>Etiquettes&nbsp;:</p>\n";
	echo "<ul>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;mode=parametrer".add_token_in_url()."'>Paramétrer</a> les étiquettes</li>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;mode=imprime".add_token_in_url()."'>Imprimer</a> les étiquettes</li>\n";
	echo "</ul>\n";
}
else {
	// Paramétrage des étiquettes
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve'>Etiquettes</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;mode=imprime".add_token_in_url()."'>Imprimer</a>";
	echo "</p>\n";

	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
	echo add_token_field();

	echo "<p>L'impression est prévue au format A4 avec deux colonnes d'étiquettes.<br />Vous pouvez ajuster quelques paramètres concernant les étiquettes.</p>\n";

	echo "<div style='float:right; border: 1px solid black; background-color: white;'>\n";
	echo "<table class='boireaus' summary='Page' style='height:270px;'>\n";

	echo "<tr style='height:20px;'>\n";
	echo "<td id='cell_1_1' style='width:20px;'></td>\n";
	echo "<td id='cell_1_2' style='width:100px;'></td>\n";
	echo "<td id='cell_1_3' style='width:10px;'></td>\n";
	echo "<td id='cell_1_4' style='width:100px;'></td>\n";
	echo "<td id='cell_1_5' style='width:20px;'></td>\n";
	echo "</tr>\n";

	echo "<tr style='height:50px;'>\n";
	echo "<td id='cell_2_1' style='width:20px;'></td>\n";
	echo "<td id='cell_2_2' style='width:100px; background-color: plum;'></td>\n";
	echo "<td id='cell_2_3' style='width:10px;'></td>\n";
	echo "<td id='cell_2_4' style='width:100px; background-color: plum;'></td>\n";
	echo "<td id='cell_2_5' style='width:20px;'></td>\n";
	echo "</tr>\n";

	echo "<tr style='height:10px;'>\n";
	echo "<td id='cell_3_1' style='width:20px;'></td>\n";
	echo "<td id='cell_3_2' style='width:100px;'></td>\n";
	echo "<td id='cell_3_3' style='width:10px;'></td>\n";
	echo "<td id='cell_3_4' style='width:100px;'></td>\n";
	echo "<td id='cell_3_5' style='width:20px;'></td>\n";
	echo "</tr>\n";

	echo "<tr style='height:50px;'>\n";
	echo "<td id='cell_4_1' style='width:20px;'></td>\n";
	echo "<td id='cell_4_2' style='width:100px; background-color: plum;'></td>\n";
	echo "<td id='cell_4_3' style='width:10px;'></td>\n";
	echo "<td id='cell_4_4' style='width:100px; background-color: plum;'></td>\n";
	echo "<td id='cell_4_5' style='width:20px;'></td>\n";
	echo "</tr>\n";

	echo "<tr style='height:10px;'>\n";
	echo "<td id='cell_5_1' style='width:20px;'></td>\n";
	echo "<td id='cell_5_2' style='width:100px;'></td>\n";
	echo "<td id='cell_5_3' style='width:10px;'></td>\n";
	echo "<td id='cell_5_4' style='width:100px;'></td>\n";
	echo "<td id='cell_5_5' style='width:20px;'></td>\n";
	echo "</tr>\n";

	echo "<tr style='height:50px;'>\n";
	echo "<td id='cell_6_1' style='width:20px;'></td>\n";
	echo "<td id='cell_6_2' style='width:100px; background-color: plum;'></td>\n";
	echo "<td id='cell_6_3' style='width:10px;'></td>\n";
	echo "<td id='cell_6_4' style='width:100px; background-color: plum;'></td>\n";
	echo "<td id='cell_6_5' style='width:20px;'></td>\n";
	echo "</tr>\n";

	echo "<tr style='height:10px;'>\n";
	echo "<td id='cell_7_1' style='width:20px;'></td>\n";
	echo "<td id='cell_7_2' style='width:100px;'></td>\n";
	echo "<td id='cell_7_3' style='width:10px;'></td>\n";
	echo "<td id='cell_7_4' style='width:100px;'></td>\n";
	echo "<td id='cell_7_5' style='width:20px;'></td>\n";
	echo "</tr>\n";

	echo "<tr style='height:50px;'>\n";
	echo "<td id='cell_8_1' style='width:20px;'></td>\n";
	echo "<td id='cell_8_2' style='width:100px; background-color: plum;'></td>\n";
	echo "<td id='cell_8_3' style='width:10px;'></td>\n";
	echo "<td id='cell_8_4' style='width:100px; background-color: plum;'></td>\n";
	echo "<td id='cell_8_5' style='width:20px;'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td id='cell_9_1' style='width:20px;'></td>\n";
	echo "<td id='cell_9_2' style='width:100px;'></td>\n";
	echo "<td id='cell_9_3' style='width:10px;'></td>\n";
	echo "<td id='cell_9_4' style='width:100px;'></td>\n";
	echo "<td id='cell_9_5' style='width:20px;'></td>\n";
	echo "</tr>\n";

	/*
	echo "<tr>\n";
	echo "<td></td>\n";
	echo "<td></td>\n";
	echo "<td></td>\n";
	echo "<td></td>\n";
	echo "<td></td>\n";
	echo "</tr>\n";
	*/

	echo "</table>\n";
	echo "</div>\n";

	echo "<table class='boireaus' summary='Tableau des paramètres'>\n";

	echo "<tr style='background-color:gray;'>\n";
	echo "<td colspan='2'>\n";
	echo "Page\n";
	echo "</td>\n";
	echo "</tr>\n";

	$cpt=0;
	$alt=1;
	echo "<tr class='lig$alt'>\n";
	echo "<td style='text-align:left;'>\n";
	echo "Marge haute&nbsp;:\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='text' autocomplete='off' name='MargeHaut' value='$MargeHaut' ";
	echo "onfocus=\"this.select();colorise('MargeHaut','affiche')\" onblur=\"colorise('MargeHaut','')\" ";
	echo "id=\"n".$cpt."\" onKeyDown=\"clavier(this.id,event);\" ";
	echo "/>\n";
	echo "</td>\n";
	echo "</tr>\n";

	$cpt++;
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='text-align:left;'>\n";
	echo "Marge basse&nbsp;:\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='text' autocomplete='off' name='MargeBas' value='$MargeBas' ";
	echo "onfocus=\"this.select();colorise('MargeBas','affiche')\" onblur=\"colorise('MargeBas','')\" ";
	echo "id=\"n".$cpt."\" onKeyDown=\"clavier(this.id,event);\" ";
	echo "/>\n";
	echo "</td>\n";
	echo "</tr>\n";

	$cpt++;
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='text-align:left;'>\n";
	echo "Marge gauche&nbsp;:\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='text' autocomplete='off' name='MargeGauche' value='$MargeGauche' ";
	echo "onfocus=\"this.select();colorise('MargeGauche','affiche')\" onblur=\"colorise('MargeGauche','')\" ";
	echo "id=\"n".$cpt."\" onKeyDown=\"clavier(this.id,event);\" ";
	echo "/>\n";
	echo "</td>\n";
	echo "</tr>\n";

	$cpt++;
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='text-align:left;'>\n";
	echo "Marge droite&nbsp;:\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='text' autocomplete='off' name='MargeDroite' value='$MargeDroite' ";
	echo "onfocus=\"this.select();colorise('MargeDroite','affiche')\" onblur=\"colorise('MargeDroite','')\" ";
	echo "id=\"n".$cpt."\" onKeyDown=\"clavier(this.id,event);\" ";
	echo "/>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr style='background-color:gray;'>\n";
	echo "<td colspan='2'>\n";
	echo "Etiquette\n";
	echo "</td>\n";
	echo "</tr>\n";

	$cpt++;
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='text-align:left;'>\n";
	echo "Largeur de l'étiquette&nbsp;:\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='text' autocomplete='off' name='larg_etq' value='$larg_etq' ";
	echo "onfocus=\"this.select();colorise('larg_etq','affiche')\" onblur=\"colorise('larg_etq','')\" ";
	echo "id=\"n".$cpt."\" onKeyDown=\"clavier(this.id,event);\" ";
	echo "/>\n";
	echo "</td>\n";
	echo "</tr>\n";

	$cpt++;
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='text-align:left;'>\n";
	echo "Hauteur de l'étiquette&nbsp;:\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='text' autocomplete='off' name='haut_etq' value='$haut_etq' \n";
	echo "onfocus=\"this.select();colorise('haut_etq','affiche')\" onblur=\"colorise('haut_etq','')\" ";
	echo "id=\"n".$cpt."\" onKeyDown=\"clavier(this.id,event);\" ";
	echo "/>\n";
	echo "</td>\n";
	echo "</tr>\n";

	$cpt++;
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='text-align:left;'>\n";
	echo "Espace vertical entre deux étiquettes&nbsp;:\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='text' autocomplete='off' name='dy' value='$dy' ";
	echo "onfocus=\"this.select();colorise('dy','affiche')\" onblur=\"colorise('dy','')\" ";
	echo "id=\"n".$cpt."\" onKeyDown=\"clavier(this.id,event);\" ";
	echo "/>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";

	echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
	echo "<input type='hidden' name='mode' value='parametrer' />\n";
	echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	function colorise(champ,mode) {
		if(champ=='MargeHaut') {
			if(mode=='affiche') {
				for(i=1;i<=5;i++) {
					if(document.getElementById('cell_1_'+i)) {
						document.getElementById('cell_1_'+i).style.backgroundColor='red';
					}
				}
			}
			else {
				for(i=1;i<=5;i++) {
					if(document.getElementById('cell_1_'+i)) {
						document.getElementById('cell_1_'+i).style.backgroundColor='white';
					}
				}
			}
		}

		if(champ=='MargeBas') {
			if(mode=='affiche') {
				for(i=1;i<=5;i++) {
					if(document.getElementById('cell_9_'+i)) {
						document.getElementById('cell_9_'+i).style.backgroundColor='red';
					}
				}
			}
			else {
				for(i=1;i<=5;i++) {
					if(document.getElementById('cell_9_'+i)) {
						document.getElementById('cell_9_'+i).style.backgroundColor='white';
					}
				}
			}
		}

		if(champ=='MargeGauche') {
			if(mode=='affiche') {
				for(i=1;i<=9;i++) {
					if(document.getElementById('cell_'+i+'_1')) {
						document.getElementById('cell_'+i+'_1').style.backgroundColor='red';
					}
				}
			}
			else {
				for(i=1;i<=9;i++) {
					if(document.getElementById('cell_'+i+'_1')) {
						document.getElementById('cell_'+i+'_1').style.backgroundColor='white';
					}
				}
			}
		}

		if(champ=='MargeDroite') {
			if(mode=='affiche') {
				for(i=1;i<=9;i++) {
					if(document.getElementById('cell_'+i+'_5')) {
						document.getElementById('cell_'+i+'_5').style.backgroundColor='red';
					}
				}
			}
			else {
				for(i=1;i<=9;i++) {
					if(document.getElementById('cell_'+i+'_5')) {
						document.getElementById('cell_'+i+'_5').style.backgroundColor='white';
					}
				}
			}
		}

		if(champ=='larg_etq') {
			if(mode=='affiche') {
				document.getElementById('cell_1_2').style.backgroundColor='red';
				document.getElementById('cell_1_4').style.backgroundColor='red';
			}
			else {
				document.getElementById('cell_1_2').style.backgroundColor='white';
				document.getElementById('cell_1_4').style.backgroundColor='white';
			}
		}

		if(champ=='haut_etq') {
			if(mode=='affiche') {
				document.getElementById('cell_2_1').style.backgroundColor='red';
				document.getElementById('cell_4_1').style.backgroundColor='red';
				document.getElementById('cell_6_1').style.backgroundColor='red';
				document.getElementById('cell_8_1').style.backgroundColor='red';
			}
			else {
				document.getElementById('cell_2_1').style.backgroundColor='white';
				document.getElementById('cell_4_1').style.backgroundColor='white';
				document.getElementById('cell_6_1').style.backgroundColor='white';
				document.getElementById('cell_8_1').style.backgroundColor='white';
			}
		}

		if(champ=='dy') {
			if(mode=='affiche') {
				document.getElementById('cell_3_1').style.backgroundColor='red';
				document.getElementById('cell_5_1').style.backgroundColor='red';
				document.getElementById('cell_7_1').style.backgroundColor='red';
			}
			else {
				document.getElementById('cell_3_1').style.backgroundColor='white';
				document.getElementById('cell_5_1').style.backgroundColor='white';
				document.getElementById('cell_7_1').style.backgroundColor='white';
			}
		}
	}

	document.getElementById('n0').focus();
</script>\n";

}

require("../lib/footer.inc.php");
?>
