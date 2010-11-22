<?php
/* $Id */
/*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_trombinoscopes/trombino_decoupe.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_trombinoscopes/trombino_decoupe.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Génération d une grille PDF pour les trombinoscopes,...',
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

$msg="";

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

$generer_pdf=isset($_POST['generer_pdf']) ? $_POST['generer_pdf'] : NULL;
$parametrer_pdf=isset($_POST['parametrer_pdf']) ? $_POST['parametrer_pdf'] : NULL;

$id_grille=isset($_POST['id_grille']) ? $_POST['id_grille'] : (isset($_GET['id_grille']) ? $_GET['id_grille'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

//=================================================
$mysql_collate=getSettingValue("mysql_collate") ? getSettingValue("mysql_collate") : "";
$chaine_mysql_collate="";
if($mysql_collate!="") {$chaine_mysql_collate="COLLATE $mysql_collate";}

$sql="CREATE TABLE IF NOT EXISTS trombino_decoupe (
	id_grille INT(11) NOT NULL,
	classe VARCHAR(100) NOT NULL default '',
	elenoet VARCHAR(50) $chaine_mysql_collate NOT NULL default '',
	x TINYINT(1) NOT NULL,
	y TINYINT(1) NOT NULL,
	page TINYINT(1) NOT NULL,
	page_global SMALLINT(6) NOT NULL,
	PRIMARY KEY (id_grille, elenoet));";
$create_table=mysql_query($sql);

$test=mysql_query("SHOW COLUMNS FROM trombino_decoupe LIKE 'id_grille';");
if(mysql_num_rows($test)==0) {
	$query=mysql_query("ALTER TABLE trombino_decoupe ADD id_grille INT(11) NOT NULL;");
}

$test=mysql_query("SHOW index FROM trombino_decoupe WHERE Key_name='PRIMARY';");
if(mysql_num_rows($test)<2) {
	$query=mysql_query("ALTER TABLE trombino_decoupe DROP PRIMARY KEY, ADD PRIMARY KEY ( id_grille,elenoet );");
}

$sql="CREATE TABLE IF NOT EXISTS trombino_decoupe_param (
	id_grille INT(11) NOT NULL,
	nom VARCHAR(255) NOT NULL default '',
	valeur VARCHAR(255) NOT NULL default '',
	PRIMARY KEY (id_grille, nom));";
$create_table=mysql_query($sql);
//=================================================

//=================================================
if(isset($parametrer_pdf)) {
	check_token();
	$trombino_pdf_nb_lig=isset($_POST['trombino_pdf_nb_lig']) ? $_POST['trombino_pdf_nb_lig'] : 0;
	$trombino_pdf_nb_lig=my_ereg_replace("[^0-9]","",$trombino_pdf_nb_lig);
	$trombino_pdf_nb_col=isset($_POST['trombino_pdf_nb_col']) ? $_POST['trombino_pdf_nb_col'] : 0;
	$trombino_pdf_nb_col=my_ereg_replace("[^0-9]","",$trombino_pdf_nb_col);
	if(($trombino_pdf_nb_lig>0)&&($trombino_pdf_nb_col>0)) {
		if (!saveSetting("trombino_pdf_nb_col", $trombino_pdf_nb_col)) {
			$msg .= "Erreur lors de l'enregistrement de trombino_pdf_nb_col !";
		}

		if (!saveSetting("trombino_pdf_nb_lig", $trombino_pdf_nb_lig)) {
			$msg .= "Erreur lors de l'enregistrement de trombino_pdf_nb_lig !";
		}

		if($msg=="") {$msg="Enregistrement des paramètres effectué.";}
	}
}
//=================================================
if(isset($_POST['suppr_grille'])) {
	check_token();
	$suppr_grille=$_POST['suppr_grille'];
	for($i=0;$i<count($suppr_grille);$i++) {
		$sql="DELETE FROM trombino_decoupe WHERE id_grille='$suppr_grille[$i]';";
		$del=mysql_query($sql);
		if(!$del) {$msg.="Erreur lors de la suppression de la grille n°$suppr_grille[$i]<br />";}
		else {
			$sql="DELETE FROM trombino_decoupe_param WHERE id_grille='$suppr_grille[$i]';";
			$del=mysql_query($sql);
			if(!$del) {$msg.="Erreur lors de la suppression des paramètres de la grille n°$suppr_grille[$i]<br />";}
		}
	}
	if($msg=="") {$msg="Suppression effectuée.<br />";}
}
//=================================================
include('trombino.inc.php');
/*
// Initialisation des valeurs
$largeur_page=210;
$hauteur_page=297;

$MargeHaut=10;
$MargeDroite=10;
$MargeGauche=10;
$MargeBas=10;

$largeur_utile_page=$largeur_page-$MargeDroite-$MargeGauche;

$x0=$MargeGauche;
$y0=$MargeHaut;

$trombino_pdf_nb_col=getSettingValue("trombino_pdf_nb_col");
if($trombino_pdf_nb_col=="") {$trombino_pdf_nb_col=4;}

$trombino_pdf_nb_lig=getSettingValue("trombino_pdf_nb_lig");
if($trombino_pdf_nb_lig=="") {$trombino_pdf_nb_lig=5;}

// Espace entre deux photos
$dx=2;
$dy=2;

// Hauteur classe
$hauteur_classe=10;
// J'ai ajouté depuis un $ecart_sous_classe=2;

// Espace pour Nom et prénom dans le cadre
$hauteur_info_eleve=5;

// Pour pouvoir ne pas imprimer le Footer
$no_footer="n";

// Il arrive qu'il y ait un décalage vertical s'amplifiant ligne après ligne sur les découpes
// Par défaut, pas de décalage:
$correctif_vertical=1;
//===================
// Valeurs calculées:

// Nombre de cases par page
$nb_cell=$trombino_pdf_nb_lig*$trombino_pdf_nb_col;

// Hauteur d'un cadre
$haut_cadre=Floor($hauteur_page-$MargeHaut-$MargeBas-$hauteur_classe-($trombino_pdf_nb_lig-1)*$dy)/$trombino_pdf_nb_lig;

// Largeur d'un cadre
$larg_cadre=Floor($largeur_page-$MargeDroite-$MargeGauche-($trombino_pdf_nb_col-1)*$dx)/$trombino_pdf_nb_col;
*/
//=================================================

//=================================================
if(isset($_POST['upload_scan'])) {
	check_token();
	if((isset($_POST['correctif_vertical']))&&($_POST['correctif_vertical']!='')) {
		// A FAIRE: FILTRER...
		$test=my_ereg_replace("[^0-9.]","",$_POST['correctif_vertical']);
		if($test!="") {$correctif_vertical=$test;}
	}

	//echo "1";
	if(!isset($_POST['fin_form_upload_scan'])) {
		$msg="Le formulaire n'a pas été POSTé entièrement.<br />Vous avez peut-être été trop gourmand avec le nombre et le volume des photos proposées.<br />";
		// Ca ne fonctionne pas... si on est trop gourmand, on se retrouve avec $_POST vide.
	}

	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		$msg="Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?<br />\n";
		// Il ne faut pas aller plus loin...
	}
	elseif(!isset($id_grille)) {
		$msg="Aucun id_grille n'a été choisi.<br />\n";
	}
	else {
		//echo "2";
		$sql="SELECT page_global FROM trombino_decoupe WHERE id_grille='$id_grille' ORDER BY page_global DESC LIMIT 1;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			$msg="L'id_grille n°$id_grille ne correspond à aucun enregistrement dans la table 'trombino_decoupe'.<br />\n";
		}
		else {
			//echo "3";
			// Récuperer les paramètres et calculer les dimensions des cadres d'après les nombres de colonnes et de lignes
			$sql="SELECT * FROM trombino_decoupe_param WHERE id_grille='$id_grille';";
			//$msg.="$sql<br />";
			$res_param=mysql_query($sql);
			if(mysql_num_rows($res_param)==0) {
				$msg="Aucun paramètre n'a été trouvé pour l'id_grille n°$id_grille dans la table 'trombino_decoupe_param'.<br />\n";
			}
			else {
				while($lig_param=mysql_fetch_object($res_param)) {
					
					//echo "\$lig_param->nom=$lig_param->nom<br />";
					//echo "\$lig_param->valeur=$lig_param->valeur";
					$nom=$lig_param->nom;
					$$nom=$lig_param->valeur;
					
					//if($lig_param->nom=='trombino_pdf_nb_lig') {$trombino_pdf_nb_lig=$lig_param->value;}
					//elseif($lig_param->nom=='trombino_pdf_nb_col') {$trombino_pdf_nb_col=$lig_param->value;}
				}

				// Nombre de cases par page
				$nb_cell=$trombino_pdf_nb_lig*$trombino_pdf_nb_col;
				// Hauteur d'un cadre
				$haut_cadre=Floor($hauteur_page-$MargeHaut-$MargeBas-$hauteur_classe-$ecart_sous_classe-($trombino_pdf_nb_lig-1)*$dy)/$trombino_pdf_nb_lig;
				// Largeur d'un cadre
				$larg_cadre=Floor($largeur_page-$MargeDroite-$MargeGauche-($trombino_pdf_nb_col-1)*$dx)/$trombino_pdf_nb_col;

				/*
				$msg.="\$trombino_pdf_nb_lig=$trombino_pdf_nb_lig<br />";
				$msg.="\$trombino_pdf_nb_col=$trombino_pdf_nb_col<br />";
				$msg.="\$nb_cell=$nb_cell<br />";
				$msg.="\$haut_cadre=$haut_cadre<br />";
				$msg.="\$larg_cadre=$larg_cadre<br />";
				*/

				$msg.="Traitement des découpes avec une grille de $trombino_pdf_nb_col colonnes sur $trombino_pdf_nb_lig lignes (id_grille n°$id_grille).<br />";

				if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
					// On récupère le RNE de l'établissement
					$repertoire2=getSettingValue("gepiSchoolRne")."/";
				}
				else {
					$repertoire2="";
				}

				$lig=mysql_fetch_object($res);
				for($i=0;$i<=$lig->page_global;$i++) {
					if($_FILES["image_".$i]['type']!='') {
		
						//$image=isset($_FILES["image_".$i]) ? $_FILES["image_".$i] : NULL;
						$image=$_FILES["image_".$i];

						$post_max_size=ini_get('post_max_size');
						$upload_max_filesize=ini_get('upload_max_filesize');

						if(!is_uploaded_file($image['tmp_name'])) {
							$msg.="L'upload du fichier n°$i a échoué.<br />\n";
							$msg.="Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
							$msg.="post_max_size=$post_max_size<br />\n";
							$msg.="upload_max_filesize=$upload_max_filesize<br />\n";
						}
						else{
							if(!file_exists($image['tmp_name'])){
								$msg.="Le fichier n°$i aurait été uploadé... mais ne serait pas présent/conservé.<br />\n";
								$msg.="Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
								$msg.="post_max_size=$post_max_size<br />\n";
								$msg.="upload_max_filesize=$upload_max_filesize<br />\n";
								$msg.="et le volume de ".$image['name']." serait<br />\n";
								$msg.="\$image['size']=".volume_human($image['size'])."<br />\n";
							}
		
							//echo "<p>Le fichier ".$image['name']." sous ".$image['tmp_name']." a été uploadé.</p>\n";
							if($image['name']=="") {$msg.="Il s'est passé un problème lors de l'upload/traitement.<br />Le fichier uploadé était-il bien de type JPEG? (type trouvé&nbsp;: ".$image['type'].")<br />";}

							$source_file=$image['tmp_name'];
							$dest_file="../temp/".$tempdir."/image_$i.jpg";
							$res_copy=copy("$source_file" , "$dest_file");
							if(!$res_copy) {
								$msg.="Erreur lors du transfert de ".$image['name']." vers le dossier temporaire de l'utilisateur.<br />\n";
							}
							else {
								$num_page=$i+1;
								//$msg.="Traitement de la page n°$i<br />\n";
								$msg.="Traitement de la page n°$num_page<br />\n";
	
								if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
									$sql="SELECT t.*, e.login FROM trombino_decoupe t, eleves e WHERE t.page_global='$i' AND t.id_grille='$id_grille' AND t.elenoet=e.elenoet;";
								}
								else {
									$sql="SELECT * FROM trombino_decoupe WHERE page_global='$i' AND id_grille='$id_grille';";
								}
								$res2=mysql_query($sql);
								if(mysql_num_rows($res2)>0) {
									$img_source=imagecreatefromjpeg($dest_file);
									// Dimensions de l'image scannée
									$larg_img=imagesx($img_source);
									$haut_img=imagesy($img_source);
	
									// Il est indispensable que la découpe de l'image scannée soit aux bords de la page imprimée
									$ratio=$larg_img/$largeur_page;
	
									$larg_cadre_img=round($larg_cadre*$ratio);
									$haut_cadre_img=round($haut_cadre*$ratio);
	
									while($lig2=mysql_fetch_object($res2)) {
										// Coordonnées dans le PDF multipliées par le ratio
										$x=round(($x0+$lig2->x*($larg_cadre+$dx))*$ratio);
										$y=round(($y0+$lig2->y*($haut_cadre+$dy)+$hauteur_classe+$ecart_sous_classe)*$ratio)*$correctif_vertical;
	
										$img=imagecreatetruecolor($larg_cadre_img,$haut_cadre_img);
										imagecopy($img,$img_source,0,0,$x,$y,$larg_cadre_img,$haut_cadre_img);

										if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
											imagejpeg($img, "../photos/eleves/$repertoire2".$lig2->login.'.jpg');
										}
										else {
											imagejpeg($img, "../photos/eleves/".$lig2->elenoet.'.jpg');
										}
										imagedestroy($img);
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
//=================================================

//=================================================
if(isset($generer_pdf)) {
	check_token();
	if(!isset($id_classe)) {
		$msg="ERREUR&nbsp;: Aucune classe n'a été sélectionnée.<br />\n";
		unset($mode);
	}
	else {
		//trombino_pdf($id_classe);
		//die();

		//======================================
		header('Content-Type: application/pdf');
		Header('Pragma: public');
		require('../fpdf/fpdf.php');
		require('../fpdf/ex_fpdf.php');
		
		// Pour drawTextBox()
		//require_once("../fpdf/class.multicelltag.php");
		//require_once("../class_php/gepi_pdf.class.php");
		
		define('FPDF_FONTPATH','../fpdf/font/');
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
				$this->SetFont('arial','',7.5);
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
					$this->SetFont('arial','',7.5);
					$this->SetXY($MargeGauche, $hauteur_page-$MargeBas);
					$this->Cell($largeur_utile_page, 5, 'Page '.$this->PageNo(), "0", 1, 'R');
				}
			}
		}
		//======================================
		$pdf=new trombino_PDF("P","mm","A4");
		//$pdf=new rel_PDF("P","mm","A4");
		//$pdf=new FPDF("P","mm","A4");
		$pdf->SetTopMargin($MargeHaut);
		$pdf->SetRightMargin($MargeDroite);
		$pdf->SetLeftMargin($MargeGauche);
		$pdf->SetAutoPageBreak(true, $MargeBas);
		
		// Couleur des traits
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetLineWidth(0.2);
		
		$fonte='arial';
		$fonte_size=10;
		$fonte_size_classe=14;
		$sc_interligne=1.3;
		//======================================
		
		//$sql="TRUNCATE trombino_decoupe;";
		//$menage=mysql_query($sql);

		//======================================
		// Nouvel id_grille
		$sql="SELECT id_grille FROM trombino_decoupe ORDER BY id_grille DESC LIMIT 1;";
		$res_grille=mysql_query($sql);
		if(mysql_num_rows($res_grille)==0) {
			$id_grille=1;
		}
		else {
			$lig_grille=mysql_fetch_object($res_grille);
			$id_grille=$lig_grille->id_grille+1;
		}
		//======================================
		$sql="INSERT INTO trombino_decoupe_param SET id_grille='$id_grille', nom='trombino_pdf_nb_lig', valeur='$trombino_pdf_nb_lig';";
		$insert=mysql_query($sql);
		$sql="INSERT INTO trombino_decoupe_param SET id_grille='$id_grille', nom='trombino_pdf_nb_col', valeur='$trombino_pdf_nb_col';";
		$insert=mysql_query($sql);
		//======================================

		$nb_total_pages=0;
		
		for($i=0;$i<count($id_classe);$i++) {
			$sql="SELECT DISTINCT e.login, e.elenoet, e.nom, e.prenom FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='$id_classe[$i]' AND jec.login=e.login ORDER BY e.nom,e.prenom, e.login;";
			$res_ele=mysql_query($sql);
		
			$tab_ele=array();
			if(mysql_num_rows($res_ele)>0) {
				$cpt=0;
				while($lig_ele=mysql_fetch_object($res_ele)) {
					$tab_ele[$cpt]=array();
					$tab_ele[$cpt]['login']=$lig_ele->login;
					$tab_ele[$cpt]['elenoet']=$lig_ele->elenoet;
					$tab_ele[$cpt]['nom']=$lig_ele->nom;
					$tab_ele[$cpt]['prenom']=$lig_ele->prenom;
					$cpt++;
				}
		
				$classe=get_class_from_id($id_classe[$i]);
		
				$nb_pages=Ceil($cpt/$nb_cell);
				//echo "\$nb_pages=$nb_pages<br />";
				$cpt=0;
				for($j=0;$j<$nb_pages;$j++) {
		
					$pdf->AddPage("P");
		
					$pdf->SetXY($x0,$y0);
		
					$bordure='LRBT';
					//$bordure='';
					$pdf->SetFont($fonte,'B',$fonte_size_classe);
					$texte="Classe de $classe";
					$pdf->Cell($largeur_utile_page,$hauteur_classe,$texte,$bordure,1,'C');
		
					$pdf->SetFont($fonte,'',$fonte_size);
		
					// Paramètres pour cell_ajustee()
					// On n'arrive pas à centrer avec cell_ajustee()
					// Difficulté avec le mode de remplissage avec myWriteHTML()
					$largeur_dispo=$larg_cadre;
					/*
					$h_cell=$hauteur_info_eleve;
					$hauteur_max_font=$fonte_size;
					$hauteur_min_font=4;
					$bordure='';
					$v_align='C';
					$align='C';
					*/
		
					for($m=0;$m<$trombino_pdf_nb_lig;$m++) {
						for($k=0;$k<$trombino_pdf_nb_col;$k++) {
							$x=$x0+$k*($larg_cadre+$dx);
							$y=$y0+$m*($haut_cadre+$dy)+$hauteur_classe+$ecart_sous_classe;
							$pdf->SetXY($x,$y);
							// Cadre de la photo
							$texte="";
							$pdf->Cell($larg_cadre,$haut_cadre,$texte,'LRBT',1,'L');
		
							$y=$y0+$m*($haut_cadre+$dy)+($haut_cadre-$hauteur_info_eleve)+$hauteur_classe+$ecart_sous_classe;
							$pdf->SetXY($x,$y);
		
							$texte="";
							if(isset($tab_ele[$cpt])) {
								//$texte=$tab_ele[$cpt]['login'];
								$texte=strtoupper($tab_ele[$cpt]['nom'])." ".casse_mot($tab_ele[$cpt]['prenom'],'majf2');
		
								$sql="INSERT INTO trombino_decoupe SET id_grille='$id_grille', classe='$classe', elenoet='".$tab_ele[$cpt]['elenoet']."', x='$k', y='$m', page='$j', page_global='$nb_total_pages';";
								$insert=mysql_query($sql);
							}
		
							//cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align);

							$hauteur_temp=$fonte_size;
							$pdf->SetFont($fonte,'',$hauteur_temp);
							$largeur_texte=$pdf->GetStringWidth($texte);
							//$hauteur_temp=$fonte_size;

							$test_taille_texte='test';
							while($test_taille_texte!='ok') {
								if($largeur_texte>$largeur_dispo)
								{
									$hauteur_temp=$hauteur_temp-0.3;
									//$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.1;
									$pdf->SetFont($fonte,'',$hauteur_temp);
									$largeur_texte=$pdf->GetStringWidth($texte);
								}
								else {
									$test_taille_texte='ok';
								}
							}
							//$pdf->drawTextBox(traite_accents_utf8($texte), $largeur_dispo, $hauteur_info_eleve, 'C', 'M', 1);
							$pdf->Cell($largeur_dispo,$hauteur_info_eleve,$texte,'',1,'C');
							$cpt++;
						}
					}
		
					$nb_total_pages++;
				}
			}
			else {
				// Classe vide
			}
		}
		
		$date=date("Ymd_Hi");
		$nom_fich='Trombino_'.$date.'.pdf';
		header('Content-Type: application/pdf');
		$pdf->Output($nom_fich,'I');
		die();

	}
}
//=================================================

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
if(!isset($mode)) {
	//**************** EN-TETE *****************
	$titre_page = "Grille PDF pour les trombinoscopes";
	require_once("../lib/header.inc");
	//**************** FIN EN-TETE *****************
	//debug_var();

	echo "<p class=bold><a href='trombinoscopes.php'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	echo "</p>\n";

	echo "<p>Choisissez&nbsp;:</p>\n";
	echo "<ul>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=parametrer'>Paramétrer les grilles</a></li>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=generer_grille'>Générer des grilles</a></li>\n";
	$sql="SELECT 1=1 FROM trombino_decoupe;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=uploader'>Uploader les grilles scannées après collage des photos</a></li>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=suppr_grille'>Supprimer des grilles</a></li>\n";
	}
	echo "</ul>\n";

	echo "<p><i>NOTES</i>&nbsp;:</p>\n";
	echo "<ul>\n";
	echo "<li>Le mode d'utilisation de cette page est le suivant&nbsp;:<br />L'administrateur choisit le nombre de colonnes et de lignes.<br />Il génère et imprime les grilles PDF.<br />Les photos sont collées par vos soins sur les grilles avec un tube de colle tout ce qu'il y a de classique.<br />Les grilles sont scannées en veillant à ce que les bords de chaque image scannée coïncident avec les bords de la page.<br />Il uploade ensuite les images scannées.<br />Le dispositif se charge de découper les grilles pour placer les photos individuelles renommées en ../photos/eleves/ELENOET.jpg</li>\n";
	//echo "<li><span style='color:red;'>A REVOIR&nbsp;:</span> Il ne faut pas changer les paramètres entre l'édition de grilles et l'upload des scans correspondants.</li>\n";
	echo "<li><span style='color:red;'>A REVOIR&nbsp;:</span> D'autres paramètres devraient être proposés et enregistrés dans la base... et liés à une édition de grille particulière.</li>\n";
	//echo "<li></li>\n";
	echo "</ul>\n";

}
elseif($mode=='parametrer') {
	//**************** EN-TETE *****************
	$titre_page = "Grille PDF pour les trombinoscopes";
	require_once("../lib/header.inc");
	//**************** FIN EN-TETE *****************
	//debug_var();

	echo "<p class=bold><a href='trombinoscopes.php'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil trombinoscopes</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Accueil découpe</a>\n";
	echo "</p>\n";

	$trombino_pdf_nb_col=getSettingValue("trombino_pdf_nb_col");
	if($trombino_pdf_nb_col=="") {$trombino_pdf_nb_col=4;}

	$trombino_pdf_nb_lig=getSettingValue("trombino_pdf_nb_lig");
	if($trombino_pdf_nb_lig=="") {$trombino_pdf_nb_lig=5;}

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	echo "<fieldset>\n";
	echo "<p>Paramétrage&nbsp;:</p>\n";
	echo "<table style='margin-left:2em;' class='boireaus' summary='Paramètres du trombinoscope'>\n";
	echo "<tr class='lig1'>\n";
	echo "<td>\n";
	echo "Nombre de colonnes&nbsp;:\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<select name='trombino_pdf_nb_col'>\n";
	for($i=1;$i<=20;$i++) {
		echo "<option value='$i'";
		if($i==$trombino_pdf_nb_col) {echo " selected='true'";}
		echo ">$i</option>\n";
	}
	echo "</select>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	echo "<td>\n";
	echo "Nombre de lignes&nbsp;:\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<select name='trombino_pdf_nb_lig'>\n";
	for($i=1;$i<=20;$i++) {
		echo "<option value='$i'";
		if($i==$trombino_pdf_nb_lig) {echo " selected='true'";}
		echo ">$i</option>\n";
	}
	echo "</select>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";

	echo "<input type='hidden' name='parametrer_pdf' value='yes' />\n";
	echo "<input type='hidden' name='mode' value='parametrer' />\n";
	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</fieldset>\n";
	echo "</form>\n";
	echo "<br />\n";

}
elseif($mode=='generer_grille') {
	//**************** EN-TETE *****************
	$titre_page = "Grille PDF pour les trombinoscopes";
	require_once("../lib/header.inc");
	//**************** FIN EN-TETE *****************
	//debug_var();

	echo "<p class=bold><a href='trombinoscopes.php'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil trombinoscopes</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Accueil découpe</a>\n";
	echo "</p>\n";

	$sql="SELECT classe, id FROM classes ORDER BY classe;";
	$call_classes=mysql_query($sql);
	$nb_classes=mysql_num_rows($call_classes);
	if($nb_classes==0) {
		echo "<p style='color:red'>ERREUR&nbsp;: Il n'existe encore aucune classe.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' target='_blank'>\n";
	echo add_token_field();
	echo "<fieldset>\n";
	echo "<p>Générer les grilles PDF pour&nbsp;:</p>\n";

	// Affichage sur 3 colonnes
	$nb_classes_par_colonne=round($nb_classes/3);

	echo "<table width='100%' summary='Choix des classes'>\n";
	echo "<tr valign='top' align='center'>\n";

	$cpt = 0;

	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";

	while($lig_clas=mysql_fetch_object($call_classes)) {

		//affichage 2 colonnes
		if(($cpt>0)&&(round($cpt/$nb_classes_par_colonne)==$cpt/$nb_classes_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		echo "<label id='label_id_classe_$cpt' for='id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='id_classe[]' id='id_classe_$cpt' value='$lig_clas->id' onchange='change_style_classe($cpt)' /> $lig_clas->classe</label>";
		echo "<br />\n";
		$cpt++;
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";

	echo "<input type='hidden' name='generer_pdf' value='yes' />\n";
	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</fieldset>\n";
	echo "</form>\n";
	echo "<br />\n";

	echo "<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('id_classe_'+k)){
				document.getElementById('id_classe_'+k).checked = mode;
				change_style_classe(k);
			}
		}
	}

	function change_style_classe(num) {
		if(document.getElementById('id_classe_'+num)) {
			if(document.getElementById('id_classe_'+num).checked) {
				document.getElementById('label_id_classe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_id_classe_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";

}
elseif($mode=='uploader') {
	//**************** EN-TETE *****************
	$titre_page = "Grille PDF pour les trombinoscopes";
	require_once("../lib/header.inc");
	//**************** FIN EN-TETE *****************
	//debug_var();

	echo "<p class=bold><a href='trombinoscopes.php'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil trombinoscopes</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Accueil découpe</a>\n";
	echo "</p>\n";

	echo "<p>Les images uploadées doivent être de type JPEG.</p>\n";

	echo "<p>Les paramètres suivants peuvent influer sur le nombre de photos que vous pourrez uploader d'un coup&nbsp;:<br />\n";
	$post_max_size=ini_get('post_max_size');
	$upload_max_filesize=ini_get('upload_max_filesize');
	echo "&nbsp;&nbsp;&nbsp;\$post_max_size=$post_max_size<br />\n";
	echo "&nbsp;&nbsp;&nbsp;\$upload_max_filesize=$upload_max_filesize<br />\n";
	echo "</p>\n";

	if(!isset($id_grille)) {
		$sql="SELECT DISTINCT id_grille FROM trombino_decoupe ORDER BY id_grille;";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			echo "<p style='color:red'>ERREUR&nbsp;: Aucune grille n'a encore été générée.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
		elseif(mysql_num_rows($test)==1) {
			$lig=mysql_fetch_object($test);
			$id_grille=$lig->id_grille;
		}
		else {
			echo "<p>Pour quelle grille souhaitez-vous uploader des photos?</p>\n";
			echo "<ul>\n";
			while($lig=mysql_fetch_object($test)) {
				echo "<li>\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?mode=uploader&amp;id_grille=$lig->id_grille".add_token_in_url()."'>Grille n°$lig->id_grille</a>\n";

				$sql="SELECT DISTINCT classe FROM trombino_decoupe WHERE id_grille='$lig->id_grille' ORDER BY classe;";
				//echo "$sql<br />";
				$res_classes=mysql_query($sql);
				$lig_clas=mysql_fetch_object($res_classes);
				echo " (<i>".$lig_clas->classe;
				while($lig_clas=mysql_fetch_object($res_classes)) {
					echo ", ".$lig_clas->classe;
				}
				$sql="SELECT * FROM trombino_decoupe_param WHERE id_grille='$lig->id_grille' ORDER BY nom;";
				$res_param=mysql_query($sql);
				while($lig_param=mysql_fetch_object($res_param)) {
					$nom=$lig_param->nom;
					$$nom=$lig_param->valeur;
					//if($lig_param->nom=='trombino_pdf_nb_lig') {$trombino_pdf_nb_lig=$lig_param->value;}
					//elseif($lig_param->nom=='trombino_pdf_nb_col') {$trombino_pdf_nb_col=$lig_param->value;}
				}
				echo " ($trombino_pdf_nb_col colonnes X $trombino_pdf_nb_lig lignes)";
				echo "</i>)";
				echo "</li>\n";
			}
			echo "</ul>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}

	echo "<p class='bold'>Grille n°$id_grille</p>\n";

	$sql="SELECT DISTINCT classe, page, page_global FROM trombino_decoupe WHERE id_grille='$id_grille' ORDER BY page_global, page;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0) {
		echo "<p style='color:red'>ERREUR&nbsp;: Aucune classe n'est associée à la grille n°$id_grille.</p>\n";
	}
	else {

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo add_token_field();
		echo "<fieldset>\n";
		echo "<input type='hidden' name='id_grille' value='$id_grille' />\n";
		echo "<input type='hidden' name='upload_scan' value='yes' />\n";
		//echo "<p>Une grille a été éditée.<br />Vous avez la possibilité de d'uploader les pages scannées.</p>\n";
		echo "<table style='margin-left:2em;' class='boireaus' summary='Upload des pages du trombinoscope'>\n";
		echo "<tr>\n";
		echo "<th>Page de la grille</th>\n";
		echo "<th>Classe</th>\n";
		echo "<th>Page de la classe</th>\n";
		echo "<th>Fichier scanné</th>\n";
		echo "</tr>\n";
		$alt=1;
		while($lig=mysql_fetch_object($test)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
			$indice=$lig->page_global+1;
			echo "<td>$indice</td>\n";
			echo "<td>$lig->classe</td>\n";
			$num_page=$lig->page+1;
			echo "<td>".$num_page."</td>\n";
			echo "<td><input type='file' name='image_$lig->page_global' value='' /></td>\n";
			echo "</tr>\n";
		}
		echo "</select>\n";
		echo "</td>\n";
		echo "</tr>\n";
	
		echo "</table>\n";
	
		//echo "<input type='' name='' value='' />\n";
		//echo "<input type='hidden' name='id_grille' value='$id_grille' />\n";
		//echo "<input type='hidden' name='upload_scan' value='yes' />\n";
		echo "<p> Il arrive qu'il y ait un décalage vertical s'amplifiant ligne après ligne sur les découpes.<br />Par défaut, on ne décale pas&nbsp;: 
<input type='text' id='correctif_vertical' name='correctif_vertical' value='1' size='3' onkeydown=\"clavier_3(this.id,event,0.1,1.5,0.01);\" /><br />Si vos découpes sont un peu décalées vers le bas (<i>il manque le haut des cranes</i>), essayez de corriger avec 0.97<br />Aucun correctif n'est proposé pour la largeur.<br />Veillez à ce que vos images scannées aient les bords taillés à la largeur de la page.</p>\n";
		echo "<p><input type='submit' value='Uploader' /></p>\n";
		echo "<input type='hidden' name='fin_form_upload_scan' value='yes' />\n";
		echo "</fieldset>\n";
		echo "</form>\n";

		echo "<br />\n";

		$max_file_uploads=ini_get('max_file_uploads');
		if(($max_file_uploads!="")&&(strlen(my_ereg_replace("[^0-9]","",$max_file_uploads))==strlen($max_file_uploads))&&($max_file_uploads>0)) {
			echo "<p><i>Note</i>&nbsp;: L'upload des photos est limité à $max_file_uploads fichier(s) simultanément.</p>\n";
		}
	}

}
elseif($mode=='suppr_grille') {
	//**************** EN-TETE *****************
	$titre_page = "Grille PDF pour les trombinoscopes";
	require_once("../lib/header.inc");
	//**************** FIN EN-TETE *****************
	//debug_var();

	echo "<p class=bold><a href='trombinoscopes.php'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil trombinoscopes</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Accueil découpe</a>\n";
	echo "</p>\n";

	$sql="SELECT DISTINCT id_grille FROM trombino_decoupe ORDER BY id_grille;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0) {
		echo "<p style='color:red'>ERREUR&nbsp;: Aucune grille n'a encore été générée.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo add_token_field();
		echo "<fieldset>\n";
		echo "<p>Quelles grilles souhaitez-vous supprimer?</p>\n";
		while($lig=mysql_fetch_object($test)) {
			echo "<input type='checkbox' name='suppr_grille[]' id='suppr_$lig->id_grille' value='$lig->id_grille' /><label for='suppr_$lig->id_grille'> Grille n°$lig->id_grille";

			$sql="SELECT DISTINCT classe FROM trombino_decoupe WHERE id_grille='$lig->id_grille' ORDER BY classe;";
			//echo "$sql<br />";
			$res_classes=mysql_query($sql);
			$lig_clas=mysql_fetch_object($res_classes);
			echo " (<i>".$lig_clas->classe;
			while($lig_clas=mysql_fetch_object($res_classes)) {
				echo ", ".$lig_clas->classe;
			}
			$sql="SELECT * FROM trombino_decoupe_param WHERE id_grille='$lig->id_grille' ORDER BY nom;";
			$res_param=mysql_query($sql);
			while($lig_param=mysql_fetch_object($res_param)) {
				$nom=$lig_param->nom;
				$$nom=$lig_param->valeur;
				//if($lig_param->nom=='trombino_pdf_nb_lig') {$trombino_pdf_nb_lig=$lig_param->value;}
				//elseif($lig_param->nom=='trombino_pdf_nb_col') {$trombino_pdf_nb_col=$lig_param->value;}
			}
			echo " ($trombino_pdf_nb_col colonnes X $trombino_pdf_nb_lig lignes)";
			echo "</i>)";
			echo "</label><br />\n";
		}

		echo "<input type='hidden' name='supprimer_grille' value='y' />\n";
		echo "<p><input type='submit' value='Supprimer' /></p>\n";
		echo "</fieldset>\n";
		echo "</form>\n";

		echo "<br />\n";
		require("../lib/footer.inc.php");
		die();
	}
}
require("../lib/footer.inc.php");
?>
