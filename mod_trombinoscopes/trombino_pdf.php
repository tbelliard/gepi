<?php
/* $Id*/
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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_trombinoscopes/trombino_pdf.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_trombinoscopes/trombino_pdf.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Génération d une grille PDF pour les trombinoscopes',
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

$generer_pdf=isset($_POST['generer_pdf']) ? $_POST['generer_pdf'] : NULL;
$parametrer_pdf=isset($_POST['parametrer_pdf']) ? $_POST['parametrer_pdf'] : NULL;

$msg="";
if(isset($parametrer_pdf)) {
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

$mysql_collate=getSettingValue("mysql_collate") ? getSettingValue("mysql_collate") : "";
$chaine_mysql_collate="";
if($mysql_collate!="") {$chaine_mysql_collate="COLLATE $mysql_collate";}

$sql="CREATE TABLE IF NOT EXISTS trombino_decoupe (
	classe VARCHAR(100) NOT NULL default '',
	elenoet VARCHAR(50) $chaine_mysql_collate NOT NULL default '',
	x TINYINT(1) NOT NULL,
	y TINYINT(1) NOT NULL,
	page TINYINT(1) NOT NULL,
	page_global SMALLINT(6) NOT NULL,
	PRIMARY KEY (elenoet));";
$create_table=mysql_query($sql);



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

	$nb_cell=$trombino_pdf_nb_lig*$trombino_pdf_nb_col;

	// Espace entre deux photos
	$dx=2;
	$dy=2;

	// Hauteur classe
	$hauteur_classe=10;

	// Hauteur d'un cadre
	$haut_cadre=Floor($hauteur_page-$MargeHaut-$MargeBas-$hauteur_classe-($trombino_pdf_nb_lig-1)*$dy)/$trombino_pdf_nb_lig;

	// Largeur d'un cadre
	$larg_cadre=Floor($largeur_page-$MargeDroite-$MargeGauche-($trombino_pdf_nb_col-1)*$dx)/$trombino_pdf_nb_col;

	// Espace pour Nom et prénom dans le cadre
	$hauteur_info_eleve=5;

	// Pour pouvoir ne pas imprimer le Footer
	$no_footer="n";



if(isset($_POST['upload_scan'])) {
	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		$msg="Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?<br />\n";
		// Il ne faut pas aller plus loin...
	}
	else {
		$sql="SELECT page_global FROM trombino_decoupe ORDER BY page_global DESC LIMIT 1;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);
			for($i=0;$i<=$lig->page_global;$i++) {
				if($_FILES["image_".$i]['type']!='') {
	
					//$image=isset($_FILES["image_".$i]) ? $_FILES["image_".$i] : NULL;
					$image=$_FILES["image_".$i];

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
	
						//echo "<p>Le fichier a été uploadé.</p>\n";

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

							$sql="SELECT * FROM trombino_decoupe WHERE page_global='$i';";
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
									$y=round(($y0+$lig2->y*($haut_cadre+$dy)+$hauteur_classe)*$ratio);

									$img=imagecreatetruecolor($larg_cadre_img,$haut_cadre_img);
									imagecopy($img,$img_source,0,0,$x,$y,$larg_cadre_img,$haut_cadre_img);

									imagejpeg($img, "../photos/eleves/".$lig2->elenoet.'.jpg');
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

if(!isset($generer_pdf)) {

	//**************** EN-TETE *****************
	$titre_page = "Grille PDF pour les trombinoscopes";
	require_once("../lib/header.inc");
	//**************** FIN EN-TETE *****************

	debug_var();

	echo "<p class=bold><a href='trombinoscopes.php'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	echo "</p>\n";

	$sql="SELECT classe, id FROM classes ORDER BY classe;";
	$call_classes=mysql_query($sql);
	$nb_classes=mysql_num_rows($call_classes);
	if($nb_classes==0) {
		echo "<p style='color:red'>ERREUR&nbsp;: Il n'existe encore aucune classe.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$trombino_pdf_nb_col=getSettingValue("trombino_pdf_nb_col");
	if($trombino_pdf_nb_col=="") {$trombino_pdf_nb_col=4;}

	$trombino_pdf_nb_lig=getSettingValue("trombino_pdf_nb_lig");
	if($trombino_pdf_nb_lig=="") {$trombino_pdf_nb_lig=5;}

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
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

	//echo "<input type='' name='' value='' />\n";
	echo "<input type='hidden' name='parametrer_pdf' value='yes' />\n";
	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</fieldset>\n";
	echo "</form>\n";
	echo "<br />\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' target='_blank'>\n";
	echo "<fieldset>\n";
	echo "<p>Générer le PDF pour&nbsp;:</p>\n";

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

	$sql="SELECT DISTINCT classe, page, page_global FROM trombino_decoupe ORDER BY page_global, page;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo "<fieldset>\n";
		echo "<p>Une grille a été éditée.<br />Vous avez la possibilité de d'uploader les pages scannées.</p>\n";
		echo "<table style='margin-left:2em;' class='boireaus' summary='Upload des pages du trombinoscope'>\n";
		echo "<tr>\n";
		echo "<th>Classe</th>\n";
		echo "<th>Page</th>\n";
		echo "<th>Fichier scanné</th>\n";
		echo "</tr>\n";
		$alt=1;
		while($lig=mysql_fetch_object($test)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
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
		echo "<input type='hidden' name='upload_scan' value='yes' />\n";
		echo "<p><input type='submit' value='Uploader' /></p>\n";
		echo "</fieldset>\n";
		echo "</form>\n";

		echo "<br />\n";
	}

	echo "<p><i>NOTES</i>&nbsp;:</p>\n";
	echo "<ul>\n";
	echo "<li>Le mode d'utilisation de cette page est le suivant&nbsp;:<br />L'administrateur choisit le nombre de colonnes et de lignes.<br />Il génère et imprime les grilles PDF.<br />Les photos sont collées sur les grilles.<br />Les grilles sont scannées en veillant à ce que les bords de chaque image scannée coïncident avec les bords de la page.<br />Il uploade ensuite les images scannées.<br />Le dispositif se charge de découper les grilles pour placer les photos individuelles renommées en ../photos/eleves/ELENOET.jpg</li>\n";
	echo "<li><span style='color:red;'>A REVOIR&nbsp;:</span> Il ne faut pas changer les paramètres entre l'édition de grilles et l'upload des scans correspondants.</li>\n";
	echo "<li><span style='color:red;'>A REVOIR&nbsp;:</span> D'autres paramètres devraient être proposés et enregistrés dans la base... et liés à une édition de grille particulière.</li>\n";
	//echo "<li></li>\n";
	echo "</ul>\n";
}
else {
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

	$nb_cell=$trombino_pdf_nb_lig*$trombino_pdf_nb_col;

	// Espace entre deux photos
	$dx=2;
	$dy=2;

	// Hauteur classe
	$hauteur_classe=10;

	// Hauteur d'un cadre
	$haut_cadre=Floor($hauteur_page-$MargeHaut-$MargeBas-$hauteur_classe-($trombino_pdf_nb_lig-1)*$dy)/$trombino_pdf_nb_lig;

	// Largeur d'un cadre
	$larg_cadre=Floor($largeur_page-$MargeDroite-$MargeGauche-($trombino_pdf_nb_col-1)*$dx)/$trombino_pdf_nb_col;

	// Espace pour Nom et prénom dans le cadre
	$hauteur_info_eleve=5;

	// Pour pouvoir ne pas imprimer le Footer
	$no_footer="n";
*/
	$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

	if(!isset($id_classe)) {
		//**************** EN-TETE *****************
		$titre_page = "Grille PDF pour les trombinoscopes";
		require_once("../lib/header.inc");
		//**************** FIN EN-TETE *****************
	
		echo "<p class=bold><a href='trombinoscopes.php'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
		echo "</p>\n";

		echo "<p style='color:red'>ERREUR&nbsp;: Aucune classe n'a été sélectionnée.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

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
		function Footer()
		{
			global $no_footer;
			global $hauteur_page;

			if($no_footer=='n') {

				$this->SetXY(5,$hauteur_page-10);
				$this->SetFont('arial','',7.5);

				$texte=getSettingValue("gepiSchoolName")."  ";
				//$texte="";
				$lg_text=$this->GetStringWidth($texte);
				$this->SetXY(10,$hauteur_page-10);
				$this->Cell(0,5,$texte,0,0,'L');

				$this->Cell(0,5,'Page '.$this->PageNo(),"0",1,'C');
				//$this->Cell(0,5,'Page '.($this->PageNo()-$decompte_page),"0",1,'C');
				//$this->Cell(0,5,'Page '.$this->PageNo().'-'.$decompte_page.'='.($this->PageNo()-$decompte_page),"0",1,'C');
				//$this->Cell(0,5,'Page '.$num_page,"0",1,'C');
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

	$sql="TRUNCATE trombino_decoupe;";
	$menage=mysql_query($sql);

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

				//$bordure='LRBT';
				$bordure='';
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
						$y=$y0+$m*($haut_cadre+$dy)+$hauteur_classe;
						$pdf->SetXY($x,$y);
						// Cadre de la photo
						$texte="";
						$pdf->Cell($larg_cadre,$haut_cadre,$texte,'LRBT',1,'L');

						$y=$y0+$m*($haut_cadre+$dy)+($haut_cadre-$hauteur_info_eleve)+$hauteur_classe;
						$pdf->SetXY($x,$y);

						$texte="";
						if(isset($tab_ele[$cpt])) {
							//$texte=$tab_ele[$cpt]['login'];
							$texte=strtoupper($tab_ele[$cpt]['nom'])." ".casse_mot($tab_ele[$cpt]['prenom'],'majf2');

							$sql="INSERT INTO trombino_decoupe SET classe='$classe', elenoet='".$tab_ele[$cpt]['elenoet']."', x='$k', y='$m', page='$j', page_global='$nb_total_pages';";
							$insert=mysql_query($sql);
						}

						//cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align);

						$largeur_texte=$pdf->GetStringWidth($texte);
						$hauteur_temp=$fonte_size;

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
require("../lib/footer.inc.php");
?>
