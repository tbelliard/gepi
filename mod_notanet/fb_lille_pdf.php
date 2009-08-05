<?php

	/* $Id: fb_rouen_pdf.php 3157 2009-05-25 14:32:18Z crob $ */

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

	$fb_academie=getSettingValue("fb_academie");
	$fb_departement=getSettingValue("fb_departement");
	$fb_session=getSettingValue("fb_session");
	// ****************************************************************************
	// MODE DE CALCUL POUR LES MOYENNES DES REGROUPEMENTS DE MATIERES:
	// - LV1: on présente pour chaque élève, la moyenne qui correspond à sa LV1: ALL1 s'il fait ALL1,...
	// ou
	// - LV1: on fait la moyenne de toutes les LV1 (AGL1, ALL1)
	// ****************************************************************************
	$fb_mode_moyenne=getSettingValue("fb_mode_moyenne");
	if(($fb_mode_moyenne!=1)&&($fb_mode_moyenne!=2)) {$fb_mode_moyenne=1;}
	$ele_lieu_naissance=getSettingValue("ele_lieu_naissance") ? getSettingValue("ele_lieu_naissance") : "n";

	// Choix du type de brevet à imprimer
	//$type_brevet=0;



/*
	unset($id_classe);
	$id_classe=array();
	$id_classe[]=3;
	$id_classe[]=4;
	$id_classe[]=22;
*/


	if((!isset($_POST['id_classe']))||(!isset($_POST['type_brevet']))) {
		//**************** EN-TETE *****************
		$titre_page = "Fiches Brevet<br />Modèle Lille";
		//echo "<div class='noprint'>\n";
		require_once("../lib/header.inc");
		//echo "</div>\n";
		//**************** FIN EN-TETE *****************
	}
	else {
		require('../fpdf/fpdf.php');
		require('../fpdf/ex_fpdf.php');
		require_once("../fpdf/class.multicelltag.php");
	
		// Fichier d'extension de fpdf pour le bulletin
		require_once("../class_php/gepi_pdf.class.php");
	}



	$type_brevet = isset($_POST['type_brevet']) ? $_POST['type_brevet'] : (isset($_GET['type_brevet']) ? $_GET['type_brevet'] : NULL);
	if(isset($type_brevet)) {
		if((!ereg("[0-9]",$type_brevet))||(strlen(my_ereg_replace("[0-9]","",$type_brevet))!=0)) {
			$type_brevet=NULL;
		}
	}
	// Liste des classes
	$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;
	// Avec ou sans appréciations
	$avec_app=isset($_POST['avec_app']) ? $_POST['avec_app'] : "n";

	// Utiliser ma fonction cell_ajustee() pour faire tenir au mieux (*) les textes longs dans les cases
	// ((*) mieux si possible que drawTextBox())
	$use_cell_ajustee=isset($_POST['use_cell_ajustee']) ? $_POST['use_cell_ajustee'] : "y";



	//===================================================================
	// Vérification préalable
	$sql="SELECT DISTINCT type_brevet FROM notanet_corresp ORDER BY type_brevet;";
	$res=mysql_query($sql);
	$nb_type_brevet=mysql_num_rows($res);
	//if(mysql_num_rows($res)==0) {
	if($nb_type_brevet==0) {
		echo "</p>\n";
		echo "</div>\n";
	
		echo "<p>Aucune association matières/type de brevet n'a encore été réalisée.<br />Commencez par <a href='../select_matieres.php'>sélectionner les matières</a></p>\n";
	
		require("../lib/footer.inc.php");
		die();
	}
	//===================================================================
	include("lib_brevets.php");
	//===================================================================
	if(!isset($type_brevet)) {
		// Choix du type de brevet
		echo "<div class='noprint'>\n";
		echo "<p class='bold'><a href='../accueil.php'>Accueil</a> | <a href='index.php'>Accueil Notanet</a>";
		echo "</p>\n";
		echo "</div>\n";
	
		echo "<ul>\n";
		while($lig=mysql_fetch_object($res)) {
			echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>Générer les fiches brevet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
		}
		echo "</ul>\n";
	
		require("../lib/footer.inc.php");
		die();
	}
	//===================================================================
	
	// Le type_brevet est choisi
	$tabmatieres=array();
	for($j=101;$j<=122;$j++){
		$tabmatieres[$j]=array();
	}
	
	$tabmatieres=tabmatieres($type_brevet);
	$num_fb_col=$tabmatieres["num_fb_col"];
	
	for($j=101;$j<=122;$j++) {
		if($tabmatieres[$j][0]!=''){
			//$sql="SELECT * FROM notanet_corresp WHERE notanet_mat='".$tabmatieres[$j][0]."' LIMIT 1";
			$sql="SELECT * FROM notanet_corresp WHERE notanet_mat='".$tabmatieres[$j][0]."' AND type_brevet='$type_brevet' LIMIT 1";
			//echo "<p>$sql</p>";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0){
				$lig=mysql_fetch_object($res);
				$tabmatieres[$j][-4]=$lig->statut;
				$tabmatieres[$j][-5]=$lig->matiere;
			}
			else{
				$tabmatieres[$j][-4]="";
				$tabmatieres[$j][-5]="";
			}
		}
	}
	//===================================================================
	if (!isset($id_classe)) {
		// Choix de la classe:
		echo "<div class='noprint'>\n";
		echo "<p class='bold'><a href='../accueil.php'>Accueil</a>";
		echo " | <a href='index.php'>Accueil Notanet</a>";
		//echo " | <a href='".$_SERVER['PHP_SELF']."?parametrer=y'>Paramètrer</a>";

		$sql="SELECT DISTINCT type_brevet FROM notanet_ele_type LIMIT 2;";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>1) {
			echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir un autre type de brevet</a>";
		}

		echo "</p>\n";
		echo "</div>\n";
	
	
		//$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, notanet n WHERE p.id_classe = c.id AND c.id=n.id_classe ORDER BY classe");
		$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, notanet n,notanet_ele_type net WHERE p.id_classe = c.id AND c.id=n.id_classe AND n.login=net.login ORDER BY classe");
		if(!$call_data){
			//echo "<p><font color='red'>Attention:</font> Il semble que vous n'ayez pas mené la procédure notanet à son terme.<br />Cette procédure renseigne des tables requises pour générer les fiches brevet.<br />Effectuez la <a href='notanet.php'>procédure notanet</a>.</p>\n";
			echo "<p><font color='red'>Attention:</font> Il semble que vous n'ayez pas mené la procédure notanet à son terme.<br />Cette procédure renseigne des tables requises pour générer les fiches brevet.<br />Effectuez la <a href='../index.php'>procédure notanet</a>.</p>\n";
	
			require("../lib/footer.inc.php");
			die();
		}
		$nombre_lignes = mysql_num_rows($call_data);
	
	
		echo "<p>Choisissez les classes pour lesquelles vous souhaitez générer les fiches brevet série <b>".$tab_type_brevet[$type_brevet]."</b>&nbsp;:</p>\n";
	
		echo "<form action='".$_SERVER['PHP_SELF']."' name='form_choix_classe' method='post'>\n";
		echo "<input type='hidden' name='type_brevet' value='$type_brevet' />\n";
		//echo "<input type='hidden' name='choix1' value='export' />\n";
		//echo "<input type='hidden' name='type_brevet' value='".$type_brevet."' />\n";
		echo "<p>Sélectionnez les classes : </p>\n";
		echo "<blockquote>\n";
		//$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	
		/*
		$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, notanet n WHERE p.id_classe = c.id AND c.id=n.id_classe ORDER BY classe");
		$nombre_lignes = mysql_num_rows($call_data);
		*/
	
		$size=min(10,$nombre_lignes);
		echo "<select name='id_classe[]' multiple='true' size='$size'>\n";
		$i = 0;
		while ($i < $nombre_lignes){
			$classe = mysql_result($call_data, $i, "classe");
			$ide_classe = mysql_result($call_data, $i, "id");
			echo "<option value='$ide_classe'>$classe</option>\n";
			$i++;
		}
		echo "</select><br />\n";
		echo "<label for='avec_app' style='cursor: pointer;'><input type='checkbox' name='avec_app' id='avec_app' value='y' checked /> Avec les appréciations</label><br />\n";
		echo "<label for='use_cell_ajustee' style='cursor: pointer;'><input type='checkbox' name='use_cell_ajustee' id='use_cell_ajustee' value='n' /> Ne pas utiliser la nouvelle fonction use_cell_ajustee() pour l'écriture des appréciations.</label><br />\n";
		echo "<input type='submit' name='choix_classe' value='Envoyer' />\n";
		echo "</blockquote>\n";
		//echo "</p>\n";
		echo "</form>\n";
		// FIN DU FORMULAIRE DE CHOIX DES CLASSES
	
		require("../lib/footer.inc.php");
		die();
	}
	//===================================================================

	// Génération proprement dite des fiches brevet

	// BOUCLE SUR LA LISTE DES CLASSES
	for($i=0;$i<count($id_classe);$i++) {
		// Calcul des moyennes de classes... pb avec le statut...
		$moy_classe=array();
		for($j=101;$j<=122;$j++) {
			if($tabmatieres[$j][0]!='') {
				//$somme=0;
				// Dans la table 'notanet', matiere='PREMIERE LANGUE VIVANTE'
				//                       et mat='AGL1'
				//                       ou mat='ALL1'
				// ... avec une seule ligne/enregistrement par élève pour la matière (aucun élève ne suit à la fois ALL1 et AGL1)
				// Dans la table 'notanet_corresp', notanet_mat='PREMIERE LANGUE VIVANTE'
				//                       et matiere='AGL1'
				//                       ou matiere='ALL1'
				// ... avec plusieurs lignes/enregistrements pour une même notanet_mat
				//$sql="SELECT ROUND(AVG(note),1) moyenne FROM notanet WHERE note!='DI' AND note!='AB' AND note!='NN' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
				$sql="SELECT ROUND(AVG(note),1) moyenne FROM notanet WHERE note!='DI' AND note!='AB' AND note!='NN' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
				//$sql="SELECT ROUND(AVG(note),1) moyenne FROM notanet n,notanet_ele_type net WHERE n.note!='DI' AND n.note!='AB' AND n.note!='NN' AND n.id_classe='$id_classe[$i]' AND n.matiere='".$tabmatieres[$j][0]."' AND n.login=net.login AND net.type_brevet='$type_brevet';";
				//echo "$sql<br />";
				$res_moy=mysql_query($sql);
				if(mysql_num_rows($res_moy)>0) {
					$lig_moy=mysql_fetch_object($res_moy);
					$moy_classe[$j]=$lig_moy->moyenne;
					//echo "\$moy_classe[$j]=$moy_classe[$j]<br />";
					// Là on fait la moyenne de l'ALL1 et de l'AGL1 ensemble car one ne fait pas la différence:
					// $tabmatieres[$j][0]='PREMIERE LANGUE VIVANTE'
				}
				else {
					$moy_classe[$j]="";
				}
			}
		}


		// Pourcentages d'acquisition sur les socles
		$sql="SELECT DISTINCT ns.login FROM notanet_socles ns, j_eleves_classes jec WHERE ns.login=jec.login AND id_classe='$id_classe[$i]';";
		$res_eff=mysql_query($sql);
		$eff_classe_ns=mysql_num_rows($res_eff);

		$sql="SELECT DISTINCT ns.login FROM notanet_socles ns, j_eleves_classes jec WHERE ns.login=jec.login AND id_classe='$id_classe[$i]' AND b2i='MS';";
		$res_eff=mysql_query($sql);
		$eff_b2i_ms=mysql_num_rows($res_eff);

		$sql="SELECT DISTINCT ns.login FROM notanet_socles ns, j_eleves_classes jec WHERE ns.login=jec.login AND id_classe='$id_classe[$i]' AND a2='MS';";
		$res_eff=mysql_query($sql);
		$eff_a2_ms=mysql_num_rows($res_eff);

		if($eff_classe_ns>0) {
			$p_b2i=round(($eff_b2i_ms*100/$eff_classe_ns)*10)/10;
			$p_a2=round(($eff_a2_ms*100/$eff_classe_ns)*10)/10;
		}
		else {
			$p_b2i="NaN";
			$p_a2="NaN";
		}

		$p_socle[114]=$p_b2i;
		$p_socle[115]=$p_a2;

	}

	define('FPDF_FONTPATH','../fpdf/font/');
	define('TopMargin','5');
	define('RightMargin','2');
	define('LeftMargin','2');
	define('BottomMargin','5');
	define('LargeurPage','210');
	define('HauteurPage','297');
	session_cache_limiter('private');

	$RneEtablissement=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
	$gepiSchoolName=getSettingValue("gepiSchoolName") ? getSettingValue("gepiSchoolName") : "gepiSchoolName";
	$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1") ? getSettingValue("gepiSchoolAdress1") : "";
	$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2") ? getSettingValue("gepiSchoolAdress2") : "";
	$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode") ? getSettingValue("gepiSchoolZipCode") : "";
	$gepiSchoolCity=getSettingValue("gepiSchoolCity") ? getSettingValue("gepiSchoolCity") : "";
	$gepiSchoolPays=getSettingValue("gepiSchoolPays") ? getSettingValue("gepiSchoolPays") : "";

	$adresse_etab=$gepiSchoolAdress1.", ".$gepiSchoolAdress2.", ".$gepiSchoolZipCode." ".$gepiSchoolCity;

	$gepiYear=getSettingValue("gepiYear") ? getSettingValue("gepiYear") : ((strftime("%m")>7) ? ((strftime("%Y")-1)."-".strftime("%Y")) : (strftime("%Y")."-".strftime("%Y")+1));

	$logo_etab=getSettingValue("logo_etab") ? getSettingValue("logo_etab") : "";

	$X1 = 0; $Y1 = 0; $X2 = 0; $Y2 = 0;
	$X3 = 0; $Y3 = 0; $X4 = 0; $Y4 = 0;
	$X5 = 0; $Y5 = 0; $X6 = 0; $Y6 = 0;

	//variables invariables
	$annee_scolaire = $gepiYear;
	$date_fb = date("d/m/Y H:i");
	$date_fichier = date("Ymd_Hi");

	$pdf=new bul_PDF('l', 'mm', 'A4');
	$pdf->SetCreator($gepiSchoolName);
	$pdf->SetAuthor($gepiSchoolName);
	$pdf->SetKeywords('');
	$pdf->SetSubject('Bulletin');
	$pdf->SetTitle('Bulletin');
	$pdf->SetDisplayMode('fullwidth', 'single');
	$pdf->SetCompression(TRUE);
	$pdf->SetAutoPageBreak(TRUE, 5);

	// gestion des styles
	$pdf->SetStyle("b","arial","B",8,"0,0,0");
	$pdf->SetStyle("i","arial","I",8,"0,0,0");
	$pdf->SetStyle("u","arial","U",8,"0,0,0");

	$taille=10;
	$pdf->SetStyle("bppc","arial","B",$taille,"0,0,0");
	$pdf->SetStyle("ippc","arial","I",$taille,"0,0,0");


	/*
	$pdf->AddPage(); //ajout d'une page au document

	$pdf->SetFont('Arial');
	$pdf->SetXY(10,20);
	$pdf->SetFontSize(100);
	$pdf->Cell(90,25, "ACADEMIE DE Rouen",'',2,'');
	$pdf->SetXY(10,17);
	$pdf->Cell(90,26, "",'LRBT',2,'');

	// 100 points pour 26 mm
	*/

	// Utiliser ma fonction cell_ajustee() pour faire tenir au mieux (*) les textes longs dans les cases
	// ((*) mieux si possible que drawTextBox())
	//$use_cell_ajustee="y";

	// Taille des textes
	$fs_titre=11;
	$fs_titre_mm=fs_pt2mm($fs_titre);
	$fs_txt=10;
	$fs_txt_mm=fs_pt2mm($fs_txt);

	// Ratio de l'interligne par rapport à la taille de police
	$r_interligne=0.3;
	$sc_interligne=1+$r_interligne;

	$l_page=297;
	$h_page=210;

	$marge=10;

	// Bloc disciplines
	$larg_col_disc=65;
	$x_col_note_mc=$marge+$larg_col_disc;
	$larg_col_note_classe=29;
	$x_col_note_glob=$marge+$larg_col_disc+$larg_col_note_classe;

	$larg_col_note_glob=33;

	$x_col_app=$x_col_note_glob+2*$larg_col_note_glob;
	//$larg_col_app=$l_page-2*$marge-$x_col_app;
	$larg_col_app=$l_page-$marge-$x_col_app;

	//=======================================

	// Hauteur cadre du bas (avis du chef d'établissement,...)
	$h_cadre_bas=41;
	$h_cadre_bas_hors_notes_pdp=22;
	//$y_cadre_bas=$h_page-$marge-$h_cadre_bas;
	$y_cadre_bas=$h_page-$h_cadre_bas;

	$bord_debug='';
	//$bord_debug='LRBT';

	for($i=0;$i<count($id_classe);$i++) {
		$sql="SELECT DISTINCT e.* FROM eleves e,
										notanet n,
										notanet_ele_type net
								WHERE n.id_classe='$id_classe[$i]' AND
										n.login=e.login AND
										net.login=n.login AND
										net.type_brevet='$type_brevet'
								ORDER BY e.login;";
		$res1=mysql_query($sql);
		if(mysql_num_rows($res1)>0) {
			// Boucle sur la liste des élèves
			while($lig1=mysql_fetch_object($res1)) {

				$pdf->AddPage('L'); //ajout d'une page au document
			
				//================================================
				// ENTETE DE PAGE
				//$pdf->SetFont('Arial');
				$pdf->SetFont('Arial','',$fs_titre);
				$pdf->SetXY($marge,$marge);
				$texte="NOMS et PRENOMS : ".strtoupper($lig1->nom)." ".ucfirst(strtolower($lig1->prenom));
				$pdf->Cell($pdf->GetStringWidth($texte),$pdf->FontSize*$sc_interligne, $texte,0,0,'L');

				$texte1="FICHE SCOLAIRE BREVET - ";
				$l1=$pdf->GetStringWidth($texte1);

				$texte3="   SESSION : ".$fb_session;
				$l3=$pdf->GetStringWidth($texte3);

				$pdf->SetFont('Arial','B',$fs_titre);
				$texte2="SERIE ".$tab_type_brevet[$type_brevet];
				$l2=$pdf->GetStringWidth($texte2);

				$x=$pdf->GetX()+($l_page-$pdf->GetX()-($l1+$l2+$l3))/2;
				$pdf->SetFont('Arial','',$fs_titre);
				$pdf->SetXY($x,$pdf->GetY());
				$pdf->Cell($pdf->GetStringWidth($texte1),$pdf->FontSize*$sc_interligne, $texte1,0,0,'L');
				$pdf->SetFont('Arial','B',$fs_titre);
				$pdf->Cell($pdf->GetStringWidth($texte2),$pdf->FontSize*$sc_interligne, $texte2,0,0,'L');
				$pdf->SetFont('Arial','',$fs_titre);
				$pdf->Cell($pdf->GetStringWidth($texte3),$pdf->FontSize*$sc_interligne, $texte3,0,1,'L');

				// Problème potentiel de largeur

				// Deuxième ligne
				if($lig1->sexe=='F') {
					$texte="Née le :";
				}
				else {
					$texte="Né le :";
				}
				$texte.=formate_date($lig1->naissance)." à : ";
				if($ele_lieu_naissance=='y') {
					$texte.=get_commune($lig1->lieu_naissance, 2);
				}
				$pdf->Cell($pdf->GetStringWidth($texte),$pdf->FontSize*$sc_interligne, $texte,0,0,'L');

				$pdf->SetFont('Arial','B',$fs_txt);
				//$texte="ACADÉMIE DE ".strtoupper($fb_academie);
				$texte="ACADÉMIE DE ".casse_mot($fb_academie);
				$x=$l_page-$marge-$pdf->GetStringWidth($texte);
				$pdf->setXY($x,$pdf->GetY());
				$pdf->Cell($pdf->GetStringWidth($texte),$pdf->FontSize*$sc_interligne, $texte,0,1,'R');

				// Il n'y a pas le département sur le modèle Lille
				//$pdf->SetXY($marge,$pdf->GetY()+$pdf->FontSize*$r_interligne);
				//$pdf->Cell($larg_acad,$pdf->FontSize*$sc_interligne, "Département: $fb_departement",0,2,'');
			
				//================================================
				// TABLEAU ADRESSE ETAB
				$y=$pdf->GetY();
				$pdf->SetFont('Arial','',$fs_txt);
				// Cadre
				$pdf->Cell($l_page-2*$marge,3*$pdf->FontSize*$sc_interligne, '','LRBT',1,'');
				$y2=$pdf->GetY();

				$y_etab=$y+$pdf->FontSize*$sc_interligne;
				$pdf->setXY($marge+15,$y_etab);
				$pdf->SetFont('Arial','B',$fs_txt);
				$texte="Établissement fréquenté : ";
				$pdf->Cell($pdf->GetStringWidth($texte),$pdf->FontSize*$sc_interligne, $texte,0,0,'L');

				$pdf->SetFont('Arial','',$fs_txt);
				$texte=$gepiSchoolName." ".$adresse_etab;
				$font_size=adjust_size_font($texte,$l_page-$pdf->GetX()-$marge,$fs_txt,0.1);
				$pdf->SetFont('Arial','',$font_size);
				$pdf->Cell($l_page-$pdf->GetX()-$marge,$pdf->FontSize*$sc_interligne, $texte,'',1,'L');

				//================================================
				// TABLEAU DES DISCIPLINES
				// LIGNES DE TITRE DU TABLEAU DES DISCIPLINES

				$pdf->SetXY($marge+$larg_col_disc,$y2+$pdf->FontSize*$sc_interligne);
				$pdf->SetFont('Arial','B',$fs_txt);
				$texte='   Note Globale affectée du coefficient   ';
				$font_size=adjust_size_font($texte,$l_page-$pdf->GetX()-$marge,$fs_txt,0.1);
				$pdf->SetFont('Arial','B',$font_size);
				$pdf->Cell($larg_col_note_classe+2*$larg_col_note_glob,3*$pdf->FontSize*$sc_interligne, $texte,'LRBT',1,'C');
				//================================================

				$pdf->SetFont('Arial','B',$fs_txt);
				//$pdf->SetXY($marge,80);
				$x=$marge;
				$y_disc=$pdf->GetY();
				$y_lignes_disc=$y_disc+3*$pdf->FontSize*$sc_interligne;
				// On trace le cadre d'entête du tableau
				$pdf->Cell($l_page-2*$marge,3*$pdf->FontSize*$sc_interligne, "",'LRBT',0,'C');
				$pdf->SetXY($marge,$y_disc);
				$h_titre_disc=3*$pdf->FontSize*$sc_interligne;
				$pdf->Cell($larg_col_disc,$h_titre_disc, "DISCIPLINES",'LRBT',0,'C');

				$h_texte_titre_disc=$pdf->FontSize*$sc_interligne;

				$pdf->SetFontSize(9);
				$x_tmp=$pdf->GetX();
				//$pdf->drawTextBox('Note moyenne de la classe', $larg_col_note_classe, 2*$pdf->FontSize*$sc_interligne, 'C', 'M', 0);
				$pdf->drawTextBox('Note moyenne de la classe', $larg_col_note_classe, 2*$h_texte_titre_disc, 'C', 'M', 0);
				$pdf->SetXY($x_tmp,$pdf->GetY());
				//$pdf->Cell($larg_col_note_classe,$pdf->FontSize*$sc_interligne, "(0 à 20)",'',0,'C');
				$pdf->Cell($larg_col_note_classe,$h_texte_titre_disc, "(0 à 20)",'',0,'C');

				$x_tmp=$pdf->GetX();
				$pdf->SetXY($x_tmp,$y_disc);
				//$pdf->Cell(2*$larg_col_note_glob,$pdf->FontSize*$sc_interligne, "3ème",'LRBT',1,'C');
				$pdf->Cell(2*$larg_col_note_glob,$h_texte_titre_disc, "3ème",'LRBT',1,'C');
				$pdf->SetXY($x_tmp,$pdf->GetY());
				//$pdf->Cell($larg_col_note_glob,2*$pdf->FontSize*$sc_interligne, "LV2",'LRBT',0,'C');
				$pdf->Cell($larg_col_note_glob,2*$h_texte_titre_disc, "LV2",'LRBT',0,'C');
				$texte="Module déc.Prof. 6 heures";
				//$pdf->drawTextBox('Note moyenne de la classe', $larg_col_note_glob, 2*$pdf->FontSize*$sc_interligne, 'C', 'M', 1);
				$pdf->drawTextBox($texte, $larg_col_note_glob, 2*$h_texte_titre_disc, 'C', 'M', 1);

				$texte="Appréciations des professeurs";
				$font_size=adjust_size_font($texte,$larg_col_app,$fs_txt,0.3);
				$pdf->SetFontSize($font_size);
				//$pdf->SetXY($x_tmp+2*$larg_col_note_glob,$y_disc);
				$pdf->SetXY($x_col_app,$y_disc);
				//$pdf->SetFillColor(200,200,200);
				//$pdf->Cell($larg_col_app,3*$h_texte_titre_disc, $texte." ".$x_col_app." ".$larg_col_app,'LRBT',2,'C',true);
				//$pdf->Cell($l_page-$marge-$x_col_app,3*$h_texte_titre_disc, $texte." ".$x_col_app." ".$larg_col_app,'LRBT',2,'C',true);
				$pdf->Cell($l_page-$marge-$x_col_app,3*$h_texte_titre_disc, $texte,'LRBT',2,'C');

				//====================================================
				// LIGNES DE MATIERES DU TABLEAU DES DISCIPLINES

				//====================================================

				// Calcul du nombre de matières à faire apparaitre:
				//$y=100;
				$nb_mat=0;
				$nb_mat_notnonca=0;
				for($j=101;$j<=122;$j++) {
					if($tabmatieres[$j][0]!='') {
						$nb_mat++;
						if($tabmatieres[$j][-1]=='NOTNONCA') {
							$nb_mat_notnonca++;
						}
					}
				}

				//====================================================

				$y=$y_lignes_disc;

				//$h_ligne_a_titre_indicatif=10;
				//$h_ligne_a_titre_indicatif=fs_pt2mm($fs_txt)*$sc_interligne; // INUTILE A VIRER ********************************************************

				// Hauteur pour chaque matière:
				$hauteur_toutes_matieres=$y_cadre_bas-$y;
				$h_par_matiere=$hauteur_toutes_matieres/($nb_mat+1); // La ligne TOTAL compte comme une matière

				// Boucle sur les matières
				$TOTAL=0;
				$SUR_TOTAL=array();
				$SUR_TOTAL[1]=0;
				$SUR_TOTAL[2]=0;
				// Les notes con calculées (à titre indicatif) sont en bas de tableau
				$temoin_notnonca=0;
				$cpt=0;
				for($j=101;$j<=122;$j++) {
					$temoin_note_non_numerique="n";

					//$hauteur_texte=$fs_txt;
					$pdf->SetFontSize($fs_txt);
					if($tabmatieres[$j][0]!='') {
						if($tabmatieres[$j][-4]!='non dispensee dans l etablissement') {
							//$pdf->SetXY($marge,100+($j-101)*$h_par_matiere);
							$y=$y_lignes_disc+$cpt*$h_par_matiere;
							$pdf->SetXY($marge,$y);
	
							if(($temoin_notnonca==0)&&($tabmatieres[$j][-1]=='NOTNONCA')) {
								// Insertion de la ligne 'A titre indicatif'
								$pdf->SetFont('Arial','B',$fs_txt);
								$pdf->Cell($larg_col_disc,$h_par_matiere, 'TOTAL DES POINTS','LRBT',0,'L');

								$pdf->SetFont('Arial','',$fs_txt);

								// Colonne Moyenne classe
								$pdf->Cell($larg_col_note_classe,$h_par_matiere, '','LRBT',0,'L');
				
								// Colonnes LV2 et DP6
								$pdf->SetXY($x_col_note_glob,$y);

								if($num_fb_col==1) {
									$t_col1=$TOTAL."/".$SUR_TOTAL[1];
									$t_col2="    /".$SUR_TOTAL[2];
								}
								else {
									$t_col1="    /".$SUR_TOTAL[1];
									$t_col2=$TOTAL."/".$SUR_TOTAL[2];
								}

								// Colonne LV2
								$pdf->Cell($larg_col_note_glob,$h_par_matiere, $t_col1,'LRBT',0,'C');
					
								// Colonne DP6h
								$pdf->Cell($larg_col_note_glob,$h_par_matiere, $t_col2,'LRBT',0,'C');

								// Appréciation
								$pdf->Cell($larg_col_app,$h_par_matiere, '','LRBT',1,'C');

								$cpt++;
								$y=$y_lignes_disc+$cpt*$h_par_matiere;

								/*
								$texte='TOTAL DES POINTS';
								$font_size=adjust_size_font($texte,$larg_col_note_classe,$fs_txt,0.3);
								$pdf->SetFontSize($font_size);
								$pdf->Cell($larg_col_note_classe,$h_ligne_a_titre_indicatif, $texte,'LRBT',2,'C');
					
								// Colonne DP6h
								$pdf->SetXY($x_col_note_glob+$larg_col_note_classe,$y);
								$pdf->Cell($larg_col_note_classe,$h_ligne_a_titre_indicatif, $texte,'LRBT',2,'C');
								*/

								//$temoin_notnonca++;
							}
	
							//$hauteur_texte=12;
							$pdf->SetFontSize($fs_txt);
				
							if($tabmatieres[$j][-1]=='NOTNONCA') {
								// Correctif pour le décalage dû à la ligne 'A titre indicatif' -> INUTILE SUR LE MODELE LILLE
								//$y+=$h_ligne_a_titre_indicatif;
								$temoin_notnonca++;
							}
							$pdf->SetXY($marge,$y);
				
							$pdf->SetFont('Arial','B',$fs_txt);

							// Colonne Disciplines
							$texte=ucfirst(accent_min(strtolower($tabmatieres[$j][0])));
	
							//if($tabmatieres[$j][0]=="OPTION FACULTATIVE (1)"){
							if($tabmatieres[$j][0]=="OPTION FACULTATIVE") {
								// recherche de la matière facultative pour l'élève
								$sql_mat_fac="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
								$res_mat_fac=mysql_query($sql_mat_fac);
								if(mysql_num_rows($res_mat_fac)>0){
									$lig_mat_fac=mysql_fetch_object($res_mat_fac);
									$texte.=": ".$lig_mat_fac->matiere;
								}
							}
							$font_size=adjust_size_font($texte,$larg_col_disc,$fs_txt,0.3);
							$pdf->SetFontSize($font_size);
							$pdf->Cell($larg_col_disc,$h_par_matiere, $texte,'LRBT',0,'L');
							// A REVOIR: Si la taille de police descend en dessous d'une valeur à choisir, mettre sur deux lignes

							$pdf->SetFont('Arial','',$fs_txt);
							//$pdf->SetFontSize($fs_txt);
							$x=$x_col_note_mc;
							$largeur_colonnes_moy=0;
							if($tabmatieres[$j]['socle']!='y') {
								// Moyenne classe
								//$pdf->SetXY($x,$y);
								//$tmp="-";
								//if($tabmatieres[$j]['socle']!='y') {$tmp=strtr($moy_classe[$j],".",",");}
								//$tmp=strtr($moy_classe[$j],".",",");
								$tmp="-";
								if($fb_mode_moyenne==1) {
									$tmp=strtr($moy_classe[$j],".",",");
								}
								else {
									$sql="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
									$res_mat=mysql_query($sql);
									if(mysql_num_rows($res_mat)>0){
										$lig_mat=mysql_fetch_object($res_mat);

										$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
										//echo "$sql<br />";
										$res_moy=mysql_query($sql);
										if(mysql_num_rows($res_moy)>0){
											$lig_moy=mysql_fetch_object($res_moy);
											$tmp=strtr($lig_moy->moyenne_mat,".",",");
										}
									}
								}
								$pdf->Cell($larg_col_note_classe,$h_par_matiere, $tmp,'LRBT',0,'C');
								$x+=$larg_col_note_classe;
								$largeur_colonnes_moy+=$larg_col_note_classe;
					
								// Moyenne élève
								//$pdf->SetXY($x,$y);
								$tmp_note="";
								$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
								$res_note=mysql_query($sql);
								if(mysql_num_rows($res_note)>0){
									$lig_note=mysql_fetch_object($res_note);
									$tmp_note=strtr($lig_note->note,".",",");
								}
								//$pdf->Cell($larg_col_note_classe,$h_par_matiere, $tmp,'LRBT',2,'C');
								//$pdf->Cell($larg_col_note_classe,$h_par_matiere, $tmp." ".$y,'LRBT',2,'C');
								//$x+=$larg_col_note_classe;
								//$largeur_colonnes_moy+=$larg_col_note_classe;
							}
							else {
								$pdf->Cell($larg_col_note_classe,$h_par_matiere, $p_socle[$j]."%",'LRBT',0,'C');
							}




							/*
							// Appréciation
							//$pdf->SetXY($x,$y);
							$texte="";
							if($avec_app=="y") {
								$sql="SELECT appreciation FROM notanet_app na,
																notanet_corresp nc
															WHERE na.login='$lig1->login' AND
																nc.notanet_mat='".$tabmatieres[$j][0]."' AND
																nc.matiere=na.matiere;";
								//echo "$sql<br />";
								$res_app=mysql_query($sql);
								if(mysql_num_rows($res_app)>0){
									$lig_app=mysql_fetch_object($res_app);
									$texte=trim($lig_app->appreciation);
								}
							}

							//if($tabmatieres[$j]['socle']!='y') {
								//$largeur_dispo=100-$largeur_colonnes_moy;
								$largeur_dispo=$larg_col_app;
							//}
							//else {
							//	$largeur_dispo=$larg_col_app+2*$larg_col_note_classe;
							//}

							$h_cell=$h_par_matiere;
							// Par précaution, si ma fonction cell_ajustee() posait pb:
							if($use_cell_ajustee=="n") {
								$font_size=adjust_size_font($texte,100-$largeur_colonnes_moy,$fs_txt,0.1);
								$pdf->SetFontSize($font_size);
								$pdf->drawTextBox(traite_accents_utf8($texte), $largeur_dispo, $h_cell, 'J', 'M', 1);
							}
							else {
								$taille_max_police=$fs_txt;
								$taille_min_police=ceil($fs_txt/3);
								cell_ajustee(traite_accents_utf8($texte),$x,$y,$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'LRBT');
							}
							*/

							//=========================================================
							// Colonnes de droite: Moyennes et totaux Notanet
							if($temoin_notnonca==0) {
								$t_col1="";
								$t_col2="";
								if($tabmatieres[$j]['socle']!='y') {
									$t_col1="    /".$tabmatieres[$j]['fb_col'][1];
									$t_col2="    /".$tabmatieres[$j]['fb_col'][2];

									$valeur_tmp="";
	
									// On traite le cas des notes non numériques AB, DI,... plus pour décrémenter les SUR_TOTAUX
									if((strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][1]))==0)&&($tabmatieres[$j][-1]!='PTSUP')&&($tabmatieres[$j]['socle']=='n')){
										$SUR_TOTAL[1]+=$tabmatieres[$j]['fb_col'][1];
									}
									if((strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][2]))==0)&&($tabmatieres[$j][-1]!='PTSUP')&&($tabmatieres[$j]['socle']=='n')){
										$SUR_TOTAL[2]+=$tabmatieres[$j]['fb_col'][2];
									}
			
									//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."';";
									$valeur_notanet_tmp="";
									$sql="SELECT note,note_notanet FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."';";
									//echo "$sql<br />\n";
									$res_note=mysql_query($sql);
									if(mysql_num_rows($res_note)){
										//echo "1<br />\n";
										$lig_note=mysql_fetch_object($res_note);
										if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')&&($tabmatieres[$j]['socle']=='n')){
											$valeur_tmp=$lig_note->note*$tabmatieres[$j][-2];
	
											$valeur_notanet_tmp=$lig_note->note_notanet;
	
											// Le cas PTSUP est calculé plus loin
											if($tabmatieres[$j][-1]!='PTSUP'){
												$TOTAL+=$valeur_tmp;
											}
										}
										else{
											$valeur_tmp=$lig_note->note;
											$temoin_note_non_numerique="y";
											
											if(($tabmatieres[$j][-1]!='PTSUP')){
												if($num_fb_col==1){
													$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
												}
												else{
													$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
												}
											}
											
										}
									}
									else{
										//echo "2<br />\n";
										// FAUT-IL UN TEMOIN POUR DECREMENTER LE SUR_TOTAL ?
										if($tabmatieres[$j][-1]!='PTSUP'){
											if($num_fb_col==1){
												$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
											}
											else{
												$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
											}
										}
									}
									//echo "\$valeur_tmp=$valeur_tmp<br />\n";
			
									if($num_fb_col==1) {
										if($temoin_note_non_numerique=="n") {
											if($tabmatieres[$j][-1]!='PTSUP') {
												$t_col1=$valeur_tmp."/".$tabmatieres[$j]['fb_col'][1];
											}
											else {
												// Cas points>10
												$t_col1=$valeur_notanet_tmp;
												$t_col2="";

												$TOTAL+=$valeur_notanet_tmp;
											}
										}
										else {
											$t_col1=$valeur_tmp;
										}
									}
									elseif($num_fb_col==2) {
										if($temoin_note_non_numerique=="n") {
											if($tabmatieres[$j][-1]!='PTSUP') {
												$t_col2=$valeur_tmp."/".$tabmatieres[$j]['fb_col'][2];
											}
											else {
												// Cas points>10
												$t_col2=$valeur_notanet_tmp;
												$t_col1="";

												$TOTAL+=$valeur_notanet_tmp;
											}
										}
										else {
											$t_col2=$valeur_tmp;
										}
									}
								}
								else {
									//$sql="SELECT note,note_notanet FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."';";
									$sql="SELECT * FROM notanet_socles WHERE login='$lig1->login';";
									//echo "$sql<br />";
									$res_socles=mysql_query($sql);
									if(mysql_num_rows($res_socles)==0) {
										$note_b2i="";
										$note_a2="";
									}
									else {
										$lig_socle=mysql_fetch_object($res_socles);
										$note_b2i=$lig_socle->b2i;
										$note_a2=$lig_socle->a2;
										$lv_a2=$lig_socle->lv;
									}

									if($j==114) {
										if($num_fb_col==1) {
											$t_col1=$note_b2i;
											$t_col2="";
										}
										elseif($num_fb_col==2) {
											$t_col1="";
											$t_col2=$note_b2i;
										}
									}
									elseif($j==115) {
										if($num_fb_col==1) {
											$t_col1=$note_a2;
											$t_col2="";
										}
										elseif($num_fb_col==2) {
											$t_col1="";
											$t_col2=$note_a2;
										}
									}
								}

								$pdf->SetFontSize($fs_txt);
								if($tabmatieres[$j][-1]!='PTSUP') {
									// Colonne LV2
									//$pdf->SetXY($x_col_note_glob,$y);
									$pdf->Cell($larg_col_note_glob,$h_par_matiere, $t_col1,'LRBT',0,'C');
						
									// Colonne DP6h
									//$pdf->SetXY($x_col_note_glob+$larg_col_note_glob,$y);
									$pdf->Cell($larg_col_note_glob,$h_par_matiere, $t_col2,'LRBT',0,'C');
								}
								else {
									// Colonne LV2
									//$y_tmp=$pdf->GetY();
									$pdf->SetXY($x_col_note_glob,$y);
									$pdf->SetFont('Arial','',$fs_txt);
									$texte="Points supplémentaires";
									$font_size=adjust_size_font($texte,$larg_col_note_glob-4,$fs_txt,0.3);
									$pdf->Cell($larg_col_note_glob,$h_par_matiere/2, $texte,'LRT',0,'C');

									$pdf->SetXY($x_col_note_glob,$y+($h_par_matiere/2));
									$pdf->Cell($larg_col_note_glob,$h_par_matiere/2, $t_col1,'LRB',0,'C');

									// Colonne DP6h
									$pdf->SetXY($x_col_note_glob+$larg_col_note_glob,$y);
									$pdf->SetFont('Arial','',$fs_txt);
									$texte="Points supplémentaires";
									$font_size=adjust_size_font($texte,$larg_col_note_glob-4,$fs_txt,0.3);
									$pdf->Cell($larg_col_note_glob,$h_par_matiere/2, $texte,'LRT',2,'C');
									$pdf->SetXY($x_col_note_glob+$larg_col_note_glob,$y+($h_par_matiere/2));
									$pdf->Cell($larg_col_note_glob,$h_par_matiere/2, $t_col2,'LRB',0,'C');
								}


							}
							elseif($tabmatieres[$j][-1]=="NOTNONCA") {

								if($num_fb_col==1) {
									$t_col1=$tmp_note;
									$t_col2="";

									if($temoin_note_non_numerique=="n") {
										$t_col1.="/".$tabmatieres[$j]['fb_col'][1];
									}
								}
								elseif($num_fb_col==2) {
									$t_col1="";
									$t_col2=$tmp_note;

									if($temoin_note_non_numerique=="n") {
										$t_col2.="/".$tabmatieres[$j]['fb_col'][2];
									}
								}

								// Colonne LV2
								$pdf->Cell($larg_col_note_glob,$h_par_matiere, $t_col1,'LRBT',0,'C');
					
								// Colonne DP6h
								$pdf->Cell($larg_col_note_glob,$h_par_matiere, $t_col2,'LRBT',0,'C');
							}
							/*
							elseif($temoin_notnonca==1) {
								// LIGNES TOTAUX
								$pdf->SetFont('Arial','',$fs_txt);
								//$pdf->SetFontSize($fs_txt);

								if($num_fb_col==1) {
									$t_col1=$TOTAL."/".$SUR_TOTAL[1];
									$t_col2="    /".$SUR_TOTAL[2];
								}
								else {
									$t_col1="    /".$SUR_TOTAL[1];
									$t_col2=$TOTAL."/".$SUR_TOTAL[2];
								}

								// Colonne LV2
								//$pdf->SetXY($x_col_note_glob,$y);
								$pdf->Cell($larg_col_note_glob,$nb_mat_notnonca*$h_par_matiere, $t_col1,'LRBT',0,'C');
					
								// Colonne DP6h
								//$pdf->SetXY($x_col_note_glob+$larg_col_note_glob,$y);
								$pdf->Cell($larg_col_note_glob,$nb_mat_notnonca*$h_par_matiere, $t_col2,'LRBT',0,'C');

							}
							*/




							$pdf->SetXY($x_col_app,$y);

							// Appréciation
							//$pdf->SetXY($x,$y);
							$texte="";
							if($avec_app=="y") {
								$sql="SELECT appreciation FROM notanet_app na,
																notanet_corresp nc
															WHERE na.login='$lig1->login' AND
																nc.notanet_mat='".$tabmatieres[$j][0]."' AND
																nc.matiere=na.matiere;";
								//echo "$sql<br />";
								$res_app=mysql_query($sql);
								if(mysql_num_rows($res_app)>0){
									$lig_app=mysql_fetch_object($res_app);
									$texte=trim($lig_app->appreciation);
								}
							}

							//if($tabmatieres[$j]['socle']!='y') {
								//$largeur_dispo=100-$largeur_colonnes_moy;
								$largeur_dispo=$larg_col_app;
							//}
							//else {
							//	$largeur_dispo=$larg_col_app+2*$larg_col_note_classe;
							//}

							$h_cell=$h_par_matiere;
							// Par précaution, si ma fonction cell_ajustee() posait pb:
							if($use_cell_ajustee=="n") {
								$font_size=adjust_size_font($texte,100-$largeur_colonnes_moy,$fs_txt,0.1);
								$pdf->SetFontSize($font_size);
								$pdf->drawTextBox(traite_accents_utf8($texte), $largeur_dispo, $h_cell, 'J', 'M', 1);
							}
							else {
								$taille_max_police=$fs_txt;
								$taille_min_police=ceil($fs_txt/3);
								cell_ajustee(traite_accents_utf8($texte),$x_col_app,$y,$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'LRBT');
							}

							if($tabmatieres[$j]['socle']=='y') {
								$pdf->SetXY($marge+$larg_col_disc,$y);
								$pdf->Cell($larg_col_note_glob,$h_par_matiere, "(2)",'',0,'L');
								$pdf->SetXY($marge+$larg_col_disc+$larg_col_note_classe,$y);
								$pdf->Cell($larg_col_note_glob,$h_par_matiere, "(3)",'',0,'L');
							}

							$cpt++;
						}
					}
				}
				//******************************************
				//$pdf->AddPage('L');

				//$larg_intitule_avis=80;
				//$larg_intitule_avis=$larg_col_disc+$larg_col_note_classe;
				$pdf->SetXY($marge,$y_cadre_bas);
				$pdf->Cell($l_page-2*$marge, $h_cadre_bas_hors_notes_pdp, "",'LRBT',2,'C');
				$pdf->SetXY($marge,$y_cadre_bas);
				$pdf->SetFont('Arial','B',$fs_txt);
				$texte="Avis et signature du Chef d'établissement : ";
				$pdf->Cell($pdf->GetStringWidth($texte), $pdf->FontSize*$sc_interligne, $texte,'',0,'L');
				$pdf->SetFont('Arial','',$fs_txt);
				$x=$pdf->GetX();
				$avis="";
				$sql="SELECT * FROM notanet_avis WHERE login='$lig1->login';";
				$res_avis=mysql_query($sql);
				if(mysql_num_rows($res_avis)>0) {
					$lig_avis=mysql_fetch_object($res_avis);
					if($lig_avis->favorable=="O") {$avis="Avis favorable.\n";}
					elseif($lig_avis->favorable=="N") {$avis="Avis défavorable.\n";}
					$avis.=$lig_avis->avis;
				}
				//$pdf->Cell(100, $h_cadre_bas, $avis,'',0,'C');
				$largeur_dispo=$l_page-$marge-$x;
				$h_cell=$h_cadre_bas_hors_notes_pdp;
				if($use_cell_ajustee=='n') {
					$pdf->drawTextBox(traite_accents_utf8($avis), $largeur_dispo, $h_cell, 'L', 'T', 0);
				}
				else {
					$taille_max_police=$fs_txt;
					$taille_min_police=ceil($fs_txt/3);
					cell_ajustee(traite_accents_utf8($avis),$x,$y_cadre_bas,$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'','T');
				}

				$pdf->SetXY($marge,$y_cadre_bas+$h_cadre_bas_hors_notes_pdp);
				$pdf->SetFont('Arial','',$fs_txt);
				$texte="(1) Latin, Grec ou découverte professionnelle 3H";
				$pdf->Cell($pdf->GetStringWidth($texte), $pdf->FontSize*$sc_interligne, $texte,'',1,'L');
				$texte="(2) % acquisition dans la classe";
				$pdf->Cell($pdf->GetStringWidth($texte), $pdf->FontSize*$sc_interligne, $texte,'',1,'L');
				$texte="(3) AB (candidat absent) - MS (maîtrise du socle) - MN (maîtrise du socle non évaluée) - ME (maîtrise éléments du socle)";
				$pdf->Cell($pdf->GetStringWidth($texte), $pdf->FontSize*$sc_interligne, $texte,'',1,'L');
				/*
				// Décision
				$pdf->SetXY(210-$marge-2*$larg_col_note_classe,$y_cadre_bas);
				$pdf->SetFont('Arial','B',$fs_txt);
				$pdf->Cell(2*$larg_col_note_classe, $pdf->FontSize*$sc_interligne, "DÉCISION",'LRBT',1,'C');
				$pdf->SetFont('Arial','',$fs_txt);
				//$pdf->SetXY(210-$marge-2*$larg_col_note_classe,$pdf->GetY());
				$pdf->SetXY(210-$marge-2*$larg_col_note_classe,$y_cadre_bas+$pdf->FontSize*$sc_interligne);
				$pdf->Cell($larg_col_note_classe, $h_cadre_bas-$pdf->FontSize*$sc_interligne, "",'LRBT',0,'C');
				$pdf->Cell($larg_col_note_classe, $h_cadre_bas-$pdf->FontSize*$sc_interligne, "",'LRBT',0,'C');
				*/
			}
		}
	}

	$nom_bulletin='Fiches_brevet_'.$date_fichier.'.pdf';

	header('Content-Type: application/pdf');
	$pdf->Output($nom_bulletin,'I');
	die();


		/*
			//$pdf->Cell(20,20, "Note moy",'LRBT',2,'C');
			//$pdf->MultiCell(20,20, "Note moy",'LRBT',2,'C');

			$pdf->SetFontSize(10);
			//$pdf->drawTextBox('"Note moyenne de la classe 0 à 20"', 20, 20, 'C', 'M', 1);
			//$pdf->Cell(20,20, "Note moy",'LRBT',2,'C');
			//$pdf->WriteHTML("<center>Note moyenne de la classe<br />0 à 20</center>");
			$pdf->drawTextBox('Note moyenne de la classe', 20, 15, 'C', 'M', 1);
			$pdf->SetXY(60,100);
			$pdf->drawTextBox('0 à 20', 20, 5, 'C', 'M', 1);
		
			$pdf->SetXY(80,85);
			$pdf->drawTextBox("Note moyenne de l'élève", 20, 15, 'C', 'M', 1);
			$pdf->SetXY(80,100);
			$pdf->drawTextBox('0 à 20', 20, 5, 'C', 'M', 1);
		*/
?>
