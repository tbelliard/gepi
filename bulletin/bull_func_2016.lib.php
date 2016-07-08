<?php
function fich_debug_bull($texte){
	$fichier_debug="/tmp/bulletin_pdf.txt";

	// Passer la variable à "y" pour activer le remplissage du fichier de debug
	$local_debug="n";
	if($local_debug=="y") {
		$fich=fopen($fichier_debug,"a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}

function bulletin_pdf($tab_bull,$i,$tab_rel) {
	//echo "DEBUG";
	global
		//==============
		//Ajout J.Etheve
		//$coefficients_a_1,
		//==============
		//============================================
		// Paramètres généraux:
		// En admin, dans Gestion générale/Configuration générale
		//$gepi_prof_suivi,
		$gepi_cpe_suivi,
		$RneEtablissement,
		$gepiSchoolAcademie,
		$gepiSchoolName,
		$gepiSchoolAdress1,
		$gepiSchoolAdress2,
		$gepiSchoolZipCode,
		$gepiSchoolCity,
		$gepiSchoolPays,
		$gepiSchoolTel,
		$gepiSchoolFax,
		$gepiSchoolEmail,
		$gepiYear,

		$logo_etab,

		$bull_intitule_app,

		$bull_formule_bas,

		// Paramètre transmis depuis la page d'impression des bulletins
		$un_seul_bull_par_famille,

		$compteur_bulletins,

		// Datation du bulletin (paramètre initié dans l'entête du bulletin PDF)
		$date_bulletin,

		// Paramètres du modèle PDF
		$tab_modele_pdf,

		$use_cell_ajustee,

		// Pour permettre de récupérer via global dans releve_pdf() le numéro du parent dont on imprime le bulletin avec au verso le relevé de notes:
		$num_resp_bull,

		// Pour récupérer le 1 relevé par page en verso du bulletin... variable récupérée via 'global' dans la fonction releve_pdf()
		$nb_releve_par_page,

		//20100615
		//$moyennes_periodes_precedentes,
		//$evolution_moyenne_periode_precedente,

		//$avec_coches_mentions,
		$gepi_denom_mention,

		$signature_bull,

		// Objet PDF initié hors de la présente fonction donnant la page du bulletin pour un élève
		$pdf;

	global $intercaler_app_classe;

	global $param_bull2016;
	global $gepiPath;
	global $tab_mef;

	/*
	echo "<pre>";
	print_r($tab_bull);
	echo "</pre>";
	*/

	//=========================================

	// Inutile dans le bulletin 2016
	if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		// On récupère le RNE de l'établissement
		$rep_photos="../photos/".$_COOKIE['RNE']."/eleves/";
	}else{
		$rep_photos="../photos/eleves/";
	}

	//=====================================
	/*
	// NE PAS SUPPRIMER CETTE SECTION... c'est pour le debug

	// Règles en rouge:
	// Selon ce que l'on souhaite débugger, décommenter une des deux règles
	$pdf->SetDrawColor(255,0,0);
	//=====================================
	// Règle 1: horizontale
	$tmp_marge_gauche=5;
	$tmp_marge_haut=5;
	$x=$tmp_marge_gauche;
	$y=$tmp_marge_haut;

	$pdf->SetXY($x,$y);
	$pdf->Cell(200,1,'','T',0,'C',0);

	for($loop=0;$loop<19;$loop++) {
		$x=$tmp_marge_gauche+$loop*10;
		$pdf->SetXY($x,$y);
		$pdf->Cell(5,20,''.$loop,'',0,'L',0);
		$pdf->SetXY($x,$y);
		$pdf->Cell(10,270,'','L',0,'C',0);

		for($loop2=0;$loop2<10;$loop2++) {
			$pdf->SetXY($x+$loop2,$y);
			$pdf->Cell(10,5,'','L',0,'C',0);
		}
	}
	//=====================================
	// Règle 2: verticale
	$tmp_marge_gauche=1;
	$tmp_marge_haut=0;
	$x=$tmp_marge_gauche;
	$y=$tmp_marge_haut;

	$pdf->SetFont('DejaVu','',5);

	// Ligne verticale
	$pdf->SetXY($x,$y);
	$pdf->Cell(1,280,'','L',0,'C',0);

	for($loop=1;$loop<29;$loop++) {
		// Repère numérique en cm
		$y=$tmp_marge_haut+$loop*10-3;
		$pdf->SetXY($x,$y);
		$pdf->Cell(10,5,''.$loop,'',0,'L',0);

		// Ligne tous les centimètres
		$y=$tmp_marge_haut+$loop*10;
		$pdf->SetXY($x,$y);
		$pdf->Cell(200,10,'','T',0,'C',0);

		// Les millimètres
		for($loop2=0;$loop2<10;$loop2++) {
			$pdf->SetXY($x,$y-10+$loop2);
			$pdf->Cell(2,10,'','T',0,'C',0);
		}
	}
	//=====================================
	// Retour au noir pour les tracés qui suivent:
	$pdf->SetDrawColor(0,0,0);
	*/
	//=====================================
	$nb_bulletins=1;
	//=====================================

	//=========================================
	//nombre de matieres à afficher
	$nb_matiere=0;
	//$fich=fopen("/tmp/infos_matieres_eleve.txt","a+");
	//fwrite($fich,"\$tab_bull['eleve'][$i]['nom']=".$tab_bull['eleve'][$i]['nom']."\n");
	//$tab_bull['eleve'][$i]['cat_id']=array();
	for($j=0;$j<count($tab_bull['groupe']);$j++) {
		if(isset($tab_bull['note'][$j][$i])) {
			// Si l'élève suit l'option, sa note est affectée (éventuellement vide)
			//fwrite($fich,"\$tab_bull['groupe'][$j]['matiere']['matiere']=".$tab_bull['groupe'][$j]['matiere']['matiere']." ");
			//fwrite($fich,"\$tab_bull['note'][$j][$i]=".$tab_bull['note'][$j][$i]."\n");
			$nb_matiere++;

			/*
			if(isset($tab_bull['cat_id'][$j])) {
				if(!in_array($tab_bull['cat_id'][$j], $tab_bull['eleve'][$i]['cat_id'])) {
					$tab_bull['eleve'][$i]['cat_id'][]=$tab_bull['cat_id'][$j];
				}
			}
			*/
		}
	}
	//$nb_categories_eleve_courant=count($tab_bull['eleve'][$i]['cat_id']);

	// DEBUG 20160220
	/*
	echo "\$tab_bull['groupe'][0]<pre>";
	print_r($tab_bull['groupe'][0]);
	echo "</pre>";
	*/


	// A REVOIR : Certains AID devraient pouvoir être tagués pour apparaitre en page 2 dans les AP, EPI, Parcours personnalisés
	if(isset($tab_bull['eleve'][$i]['aid_b'])) {
		$nb_matiere+=count($tab_bull['eleve'][$i]['aid_b']);
	}

	if(isset($tab_bull['eleve'][$i]['aid_e'])) {
		$nb_matiere+=count($tab_bull['eleve'][$i]['aid_e']);
	}

	/*
	$nb_categories_select=0;
	//$categorie_passe_for='';

	if($tab_modele_pdf["active_regroupement_cote"][$classe_id]==='1' or $tab_modele_pdf["active_entete_regroupement"][$classe_id]==='1') {
		$nb_categories_select=count(array_count_values($tab_bull['cat_id']));
	}
	*/
	//=========================================

	/*
		$val_defaut_champ_bull_pdf["arrondie_choix"]=0.01;
		$val_defaut_champ_bull_pdf["nb_chiffre_virgule"]=2;
		$val_defaut_champ_bull_pdf["chiffre_avec_zero"]=0;
	*/
	$arrondi=0.01;
	$nb_chiffre_virgule=1;
	$chiffre_avec_zero=0;
	$evolution_moyenne_periode_precedente="y";

	//+++++++++++++++++++++++++++++++++++++++++++
	// A FAIRE
	// Mettre ici une boucle pour $nb_bulletins
	// Et tenir compte par la suite de la demande d'intercaler le relevé de notes ou non
	//+++++++++++++++++++++++++++++++++++++++++++

	for($num_resp_bull=0;$num_resp_bull<$nb_bulletins;$num_resp_bull++) {
		$pdf->AddPage(); //ajout d'une page au document
		$pdf->SetFont('DejaVu');

		//================================
		/*
		// On insère le footer dès que la page est créée:
		//Positionnement à 1 cm du bas et 0,5cm + 0,5cm du coté gauche
		$pdf->SetXY(5,-10);
		//Police DejaVu Gras 6
		$pdf->SetFont('DejaVu','B',8);
		// $fomule = 'Bulletin à conserver précieusement. Aucun duplicata ne sera délivré. - GEPI : solution libre de gestion et de suivi des résultats scolaires.'
		$pdf->Cell(0,4.5, ($bull_formule_bas),0,0,'C');
		*/
		//================================

		$hauteur_pris=0;

		//=========================================

		// Récupération de l'identifiant de la classe:
		$classe_id=$tab_bull['eleve'][$i]['id_classe'];

		//=========================================

		/*
		// FILIGRANE PERIODE NON CLOSE
		if($tab_bull['verouiller']=="N") {
			$pdf->SetFont('DejaVu','B',40);
			$pdf->SetTextColor(255,192,203);
			//$pdf->TextWithRotation(40,190,$texte_filigrame[$classe_id],45);
			$pdf->TextWithRotation(40,210,"ATTENTION : Période non close",45);
			$pdf->SetTextColor(0,0,0);
		}
		elseif($tab_modele_pdf["affiche_filigrame"][$classe_id]==='1'){
			$pdf->SetFont('DejaVu','B',50);
			$pdf->SetTextColor(255,192,203);
			//$pdf->TextWithRotation(40,190,$texte_filigrame[$classe_id],45);
			$pdf->TextWithRotation(40,190,$tab_modele_pdf["texte_filigrame"][$classe_id],45);
			$pdf->SetTextColor(0,0,0);
		}
		*/

		//=========================================

		// A FAIRE : $num_cycle à identifier d'après le MEF
		$num_cycle=3;

		//=========================================

		// Cadre Logo RF

		$pdf->Rect($param_bull2016["x_cadre_logo_RF"], $param_bull2016["y_cadre_logo_RF"], $param_bull2016["largeur_cadre_logo_RF"], $param_bull2016["hauteur_cadre_logo_RF"], 'D');

		//$logo_RF=$gepiPath."/images/logo_RF.jpg";
		$logo_RF="../images/logo_RF.jpg";
		$valeur=redimensionne_image($logo_RF, 18*3.2, 10*3.2);
		//$X_logo = $param_bull2016["x_logo_RF"];
		//$Y_logo = $param_bull2016["y_logo_RF"];
		$L_logo = $valeur[0];
		$H_logo = $valeur[1];

		$X_logo=$param_bull2016["x_cadre_logo_RF"]+($param_bull2016["largeur_cadre_logo_RF"]-$L_logo)/2;
		$Y_logo=$param_bull2016["y_cadre_logo_RF"]+($param_bull2016["hauteur_cadre_logo_RF"]-$H_logo)/2;

		// centrage du logo
		//$centre_du_logo = ( $H_logo / 2 );
		//$Y_logo = $tab_modele_pdf["Y_centre_logo"][$classe_id] - $centre_du_logo;

		//logo
		$tmp_dim_photo=getimagesize($logo_RF);
		if((isset($tmp_dim_photo[2]))&&($tmp_dim_photo[2]==2)) {
			$pdf->Image($logo_RF, $X_logo, $Y_logo, $L_logo, $H_logo);
		}

		//=========================================

		// Cadre EN

		$pdf->Rect($param_bull2016["x_cadre_EN"], $param_bull2016["y_cadre_EN"], $param_bull2016["largeur_cadre_logo_RF"], $param_bull2016["hauteur_cadre_logo_RF"], 'D');

		$pdf->SetXY($param_bull2016["x_cadre_EN"]+2.5, $param_bull2016["y_cadre_EN"]+1);
		$pdf->SetFont('DejaVu','',6);
		$pdf->SetTextColor(0,0,0);
		$texte="MINISTÈRE\nDE L'ÉDUCATION\nNATIONALE, DE\nL'ENSEIGNEMENT\nSUPÉRIEUR ET DE\nLA RECHERCHE";
		//$pdf->Cell($param_bull2016["largeur_cadre_logo_RF"]-8, $param_bull2016["hauteur_cadre_EN"]-6, $texte,0,2,'C');
		$pdf->drawTextBox($texte, $param_bull2016["largeur_cadre_logo_RF"]-5, $param_bull2016["hauteur_cadre_EN"]-4, 'C', 'M', 0);

		//=========================================

		// Section Académie, établissement, adresse étab

		$pdf->SetXY($param_bull2016["x_cadre_etab"], $param_bull2016["y_cadre_etab_academie"]);
		$pdf->SetFont('DejaVu','',9);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell($param_bull2016["largeur_cadre_etab"],7, "Académie de ".$gepiSchoolAcademie,0,2,'L');

		$pdf->SetXY($param_bull2016["x_cadre_etab"], $param_bull2016["y_cadre_etab_college"]);
		$pdf->SetFont('DejaVu','B',10);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell($param_bull2016["largeur_cadre_etab"],7, $gepiSchoolName,0,2,'L');

		$pdf->SetXY($param_bull2016["x_cadre_etab"], $param_bull2016["y_cadre_etab_adresse_college"]);
		$pdf->SetFont('DejaVu','',7);
		$pdf->SetTextColor(0,0,0);
		$adresse_etab="";
		if($gepiSchoolAdress1!="") {
			$adresse_etab=$gepiSchoolAdress1;
			if($gepiSchoolAdress2!="") {
				$adresse_etab.=", ".$gepiSchoolAdress2;
			}
		}
		elseif($gepiSchoolAdress2!="") {
			$adresse_etab=$gepiSchoolAdress2;
		}
		$pdf->Cell($param_bull2016["largeur_cadre_etab"], 7, $adresse_etab, 0, 2, 'L');

		$pdf->SetXY($param_bull2016["x_cadre_etab"], $param_bull2016["y_cadre_etab_cp_commune_college"]);
		$pdf->SetFont('DejaVu','',7);
		$pdf->SetTextColor(0,0,0);
		$cp_commune_etab="";
		if($gepiSchoolZipCode!="") {
			$cp_commune_etab=$gepiSchoolZipCode;
			if($gepiSchoolCity!="") {
				$cp_commune_etab.=" ".$gepiSchoolCity;
			}
		}
		elseif($gepiSchoolCity!="") {
			$cp_commune_etab=$gepiSchoolCity;
		}
		$pdf->Cell($param_bull2016["largeur_cadre_etab"], 7, $cp_commune_etab, 0, 2, 'L');

		$pdf->SetXY($param_bull2016["x_cadre_etab"], $param_bull2016["y_cadre_etab_tel_college"]);
		$pdf->SetFont('DejaVu','',7);
		$pdf->SetTextColor(0,0,0);
		$tel_etab="";
		if($gepiSchoolTel!="") {
			$tel_etab=$gepiSchoolTel;
			if($gepiSchoolFax!="") {
				$tel_etab.=" (fax:".$gepiSchoolFax.")";
			}
		}
		elseif($gepiSchoolFax!="") {
			$tel_etab="(fax:".$gepiSchoolFax.")";
		}
		$pdf->Cell($param_bull2016["largeur_cadre_etab"], 7, $tel_etab, 0, 2, 'L');

		$pdf->SetXY($param_bull2016["x_cadre_etab"], $param_bull2016["y_cadre_etab_email_college"]);
		$pdf->SetFont('DejaVu','',7);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell($param_bull2016["largeur_cadre_etab"], 7, $gepiSchoolEmail, 0, 2, 'L');

		//=========================================

		// Section cycle et niveau

		$mef_code_ele=$tab_bull['eleve'][$i]['mef_code'];
		if(isset($tab_mef[$mef_code_ele]["mef_rattachement"])) {
			if($tab_mef[$mef_code_ele]["mef_rattachement"]=="10010012110") {
				// C'est une classe de 6ème
				$cycle=3;
				$niveau=6;
			}
			elseif($tab_mef[$mef_code_ele]["mef_rattachement"]=="10110001110") {
				$cycle=4;
				$niveau=5;
			}
			elseif($tab_mef[$mef_code_ele]["mef_rattachement"]=="10210001110") {
				$cycle=4;
				$niveau=4;
			}
			elseif($tab_mef[$mef_code_ele]["mef_rattachement"]=="10310019110") {
				$cycle=4;
				$niveau=3;
			}
			else {
				// Pour le moment, on suppose que c'est un cycle 4 et même un élève de 3ème
				// On verra plus tard le cas d'un Gepi en Lycée
				$cycle=4;
				$niveau=3;
			}
		}
		else {
			// Pour le moment, on suppose que c'est un cycle 4 et même un élève de 3ème
			// On verra plus tard le cas d'un Gepi en Lycée
			$cycle=4;
			$niveau=3;
		}

		// Colonne cycle:
		for($loop_cycle=2;$loop_cycle<=4;$loop_cycle++) {
			if($loop_cycle==$cycle) {
				$pdf->SetFillColor($param_bull2016["couleur_cycle_courant"]["cycle"]["R"], $param_bull2016["couleur_cycle_courant"]["cycle"]["V"], $param_bull2016["couleur_cycle_courant"]["cycle"]["B"]);
			}
			else {
				$pdf->SetFillColor($param_bull2016["couleur_cycle_autre"]["R"], $param_bull2016["couleur_cycle_autre"]["V"], $param_bull2016["couleur_cycle_autre"]["B"]);
			}
			$pdf->Rect($param_bull2016["x_colonne_cycle"], $param_bull2016["y_colonne_cycle"]+($loop_cycle-2)*($param_bull2016["cote_carre_cycle"]+$param_bull2016["ecart_carres_cycle"]), $param_bull2016["cote_carre_cycle"], $param_bull2016["cote_carre_cycle"], 'F');
			$pdf->SetFillColor(0, 0, 0);

			$pdf->SetXY($param_bull2016["x_colonne_cycle"], $param_bull2016["y_colonne_cycle"]+($loop_cycle-2)*($param_bull2016["cote_carre_cycle"]+$param_bull2016["ecart_carres_cycle"]));
			$pdf->SetFont('DejaVu','',14);
			$pdf->SetTextColor(255,255,255);
			$pdf->Cell($param_bull2016["cote_carre_cycle"],$param_bull2016["cote_carre_cycle"], $loop_cycle,0,2,'C');

		}

		// Colonnes niveau:
		for($loop_cycle=2;$loop_cycle<=4;$loop_cycle++) {
			for($loop_niveau=0;$loop_niveau<3;$loop_niveau++) {
				if(($loop_cycle==$cycle)&&(isset($param_bull2016["cycles_et_niveaux"][$loop_cycle][$loop_niveau]["texte"]))&&($param_bull2016["cycles_et_niveaux"][$loop_cycle][$loop_niveau]["texte"]==$niveau)) {
					$pdf->SetFillColor($param_bull2016["couleur_cycle_courant"]["niveau"]["R"], $param_bull2016["couleur_cycle_courant"]["niveau"]["V"], $param_bull2016["couleur_cycle_courant"]["niveau"]["B"]);
				}
				else {
					$pdf->SetFillColor($param_bull2016["couleur_cycle_autre"]["R"], $param_bull2016["couleur_cycle_autre"]["V"], $param_bull2016["couleur_cycle_autre"]["B"]);
				}
				$pdf->Rect($param_bull2016["x_colonne_cycle"]+($loop_niveau+1)*($param_bull2016["cote_carre_cycle"]+$param_bull2016["ecart_carres_cycle"]), $param_bull2016["y_colonne_cycle"]+($loop_cycle-2)*($param_bull2016["cote_carre_cycle"]+$param_bull2016["ecart_carres_cycle"]), $param_bull2016["cote_carre_cycle"], $param_bull2016["cote_carre_cycle"], 'F');
				$pdf->SetFillColor(0, 0, 0);

				$pdf->SetXY($param_bull2016["x_colonne_cycle"]+($loop_niveau+1)*($param_bull2016["cote_carre_cycle"]+$param_bull2016["ecart_carres_cycle"]), $param_bull2016["y_colonne_cycle"]+($loop_cycle-2)*($param_bull2016["cote_carre_cycle"]+$param_bull2016["ecart_carres_cycle"]));
				$pdf->SetFont('DejaVu','',14);
				$pdf->SetTextColor(255,255,255);
				$pdf->Cell($param_bull2016["cote_carre_cycle"],$param_bull2016["cote_carre_cycle"], $param_bull2016["cycles_et_niveaux"][$loop_cycle][$loop_niveau]["texte"],0,2,'C');
			}
		}


		//=========================================

		// Cadre Logo établissement

		$logo = '../images/'.getSettingValue('logo_etab');
		$format_du_logo = mb_strtolower(str_replace('.','',strstr(getSettingValue('logo_etab'), '.')));

		// Logo
		//if($tab_modele_pdf["affiche_logo_etab"][$classe_id]==='1' and file_exists($logo) and getSettingValue('logo_etab') != '' and ($format_du_logo==='jpg' or $format_du_logo==='png')) {
		if($param_bull2016["affiche_logo_etab"]==1 and file_exists($logo) and getSettingValue('logo_etab') != '' and ($format_du_logo==='jpg' or $format_du_logo==='png')) {
			$valeur=redimensionne_image($logo, ($param_bull2016["largeur_max_logo_etab"]*2.8), ($param_bull2016["hauteur_max_logo_etab"]*2.8));
			$X_logo = $param_bull2016["x_logo_etab"];
			$Y_logo = $param_bull2016["y_logo_etab"];
			$L_logo = $valeur[0];
			$H_logo = $valeur[1];
			//$X_etab = $X_logo + $L_logo + 1;
			//$Y_etab = $Y_logo;

			/*
			if ( !isset($tab_modele_pdf["centrage_logo"][$classe_id]) or empty($tab_modele_pdf["centrage_logo"][$classe_id]) ) {
				$tab_modele_pdf["centrage_logo"][$classe_id] = '0';
			}

			if ( $tab_modele_pdf["centrage_logo"][$classe_id] === '1' ) {
				// centrage du logo
				$centre_du_logo = ( $H_logo / 2 );
				$Y_logo = $tab_modele_pdf["Y_centre_logo"][$classe_id] - $centre_du_logo;
			}
			*/

			//logo
			$tmp_dim_photo=getimagesize($logo);
			if((isset($tmp_dim_photo[2]))&&($tmp_dim_photo[2]==2)) {
				$pdf->Image($logo, $X_logo, $Y_logo, $L_logo, $H_logo);
			}
		}

		//=========================================

		// Cadre Année, période, identité élève, pp et classe
		$pdf->SetFillColor($param_bull2016["couleur_cadre_identite"]["R"], $param_bull2016["couleur_cadre_identite"]["V"], $param_bull2016["couleur_cadre_identite"]["B"]);

		$pdf->Rect($param_bull2016["x_cadre_eleve"], $param_bull2016["y_cadre_eleve"], $param_bull2016["largeur_cadre_eleve"], $param_bull2016["hauteur_cadre_eleve"], 'F');

		$pdf->SetFillColor(0, 0, 0);

		$pdf->SetXY($param_bull2016["x_cadre_eleve"], $param_bull2016["y_annee_scolaire"]);
		$pdf->SetFont('DejaVu','B',10);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell($param_bull2016["largeur_cadre_eleve"],7, "Année scolaire ".$gepiYear,0,2,'C');

		$pdf->SetXY($param_bull2016["x_cadre_eleve"], $param_bull2016["y_periode"]);
		$pdf->SetFont('DejaVu','',8);
		$pdf->SetTextColor(0,0,0);
		//$pdf->Cell($param_bull2016["largeur_cadre_eleve"],7, "Bilan trimestriel du cycle ".$num_cycle." - ".$tab_bull['num_periode']." trimestre",0,2,'C');
		if($tab_bull['nb_periodes']==2) {
			$trimestriel_ou_semestriel="semestriel";
		}
		else {
			$trimestriel_ou_semestriel="trimestriel";
		}
		$pdf->Cell($param_bull2016["largeur_cadre_eleve"],7, "Bilan ".$trimestriel_ou_semestriel." du cycle ".$num_cycle." - ".$tab_bull['nom_periode'],0,2,'C');

		$pdf->SetXY($param_bull2016["x_cadre_eleve"], $param_bull2016["y_nom_prenom_eleve"]);
		$pdf->SetFont('DejaVu','B',12);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell($param_bull2016["largeur_cadre_eleve"],7, $tab_bull['eleve'][$i]['prenom']." ".$tab_bull['eleve'][$i]['nom'],0,2,'C');

		$info_naissance="Né";
		if($tab_bull['eleve'][$i]['sexe']=="F") {$info_naissance.="e";}
		$info_naissance.=" le ".$tab_bull['eleve'][$i]['naissance'];
		/*
		if((getSettingValue('ele_lieu_naissance')=='y')&&($tab_modele_pdf["affiche_lieu_naissance"][$classe_id]==='1')) {
			$info_naissance.=" à ".$tab_bull['eleve'][$i]['lieu_naissance'];
		}
		*/
		$pdf->SetXY($param_bull2016["x_cadre_eleve"], $param_bull2016["y_naissance_eleve"]);
		$pdf->SetFont('DejaVu','',8);
		$pdf->Cell($param_bull2016["largeur_cadre_eleve"],7, $info_naissance,0,2,'C');


		//if($tab_modele_pdf["afficher_tous_profprincipaux"][$classe_id]==1) {
			$index_pp='pp_classe';
		/*
		}
		else {
			$index_pp='pp';
		}
		*/
		if(isset($tab_bull['eleve'][$i][$index_pp][0]['login'])) {
			$pp_classe[$i]=ucfirst($tab_bull['gepi_prof_suivi'])." : ";
			$pp_classe[$i].=affiche_utilisateur($tab_bull['eleve'][$i][$index_pp][0]['login'],$tab_bull['eleve'][$i]['id_classe']);
			for($i_pp=1;$i_pp<count($tab_bull['eleve'][$i][$index_pp]);$i_pp++) {
				$pp_classe[$i].=", ";
				$pp_classe[$i].=affiche_utilisateur($tab_bull['eleve'][$i][$index_pp][$i_pp]['login'],$tab_bull['eleve'][$i]['id_classe']);
			}
		}
		else {
			$pp_classe[$i]="";
		}
		$pdf->SetXY($param_bull2016["x_cadre_eleve"], $param_bull2016["y_pp"]);
		$pdf->Cell($param_bull2016["largeur_cadre_eleve"],7, $pp_classe[$i],0,2,'C');

		$pdf->SetXY($param_bull2016["x_cadre_eleve"], $param_bull2016["y_classe"]);
		$pdf->SetFont('DejaVu','',11);
		$pdf->Cell($param_bull2016["largeur_cadre_eleve"],7, "Classe de ".unhtmlentities($tab_bull['eleve'][$i]['classe']),0,2,'C');

		//=========================================

		// Bandeau Suivi des acquis

		$pdf->SetFillColor($param_bull2016["couleur_bandeau_suivi_acquis"]["R"], $param_bull2016["couleur_bandeau_suivi_acquis"]["V"], $param_bull2016["couleur_bandeau_suivi_acquis"]["B"]);

		$pdf->Rect($param_bull2016["x_bandeau_suivi_acquis"], $param_bull2016["y_bandeau_suivi_acquis"], $param_bull2016["largeur_bandeau_suivi_acquis"], $param_bull2016["hauteur_bandeau_suivi_acquis"], 'F');

		$pdf->SetFillColor(0, 0, 0);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->SetXY($param_bull2016["x_bandeau_suivi_acquis"], $param_bull2016["y_bandeau_suivi_acquis"]+1);
		$pdf->SetFont('DejaVu','B',12);
		$pdf->Cell($param_bull2016["largeur_bandeau_suivi_acquis"],7, "Suivi des acquis scolaires de l'élève",0,2,'C');

		//=========================================

		// Tableau des acquis
		// Ligne de titre

		// Colonne 2 : Éléments de programmes
		$pdf->SetFillColor($param_bull2016["couleur_acquis_ligne_entete"]["R"], $param_bull2016["couleur_acquis_ligne_entete"]["V"], $param_bull2016["couleur_acquis_ligne_entete"]["B"]);
		$pdf->Rect($param_bull2016["x_acquis_col_2"], $param_bull2016["y_acquis_ligne_entete"], $param_bull2016["largeur_acquis_col_2"], $param_bull2016["hauteur_acquis_ligne_entete"], 'F');
		$pdf->SetFillColor(0, 0, 0);

		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetXY($param_bull2016["x_acquis_col_2"], $param_bull2016["y_acquis_ligne_entete"]);
		$pdf->SetFont('DejaVu','',7);
		//$pdf->Cell($param_bull2016["largeur_bandeau_suivi_acquis"],7, "Suivi des acquis scolaires de l'élève",0,2,'C');
		$texte="Éléments du programme travaillés durant\nla période (connaissances/compétences)";
		//$pdf->Cell($param_bull2016["largeur_cadre_logo_RF"]-8, $param_bull2016["hauteur_cadre_EN"]-6, $texte,0,2,'C');
		$pdf->drawTextBox($texte, $param_bull2016["largeur_acquis_col_2"], $param_bull2016["hauteur_acquis_ligne_entete"], 'L', 'M', 0);

		// Colonne 3 : Appréciation
		$pdf->SetFillColor($param_bull2016["couleur_acquis_ligne_entete"]["R"], $param_bull2016["couleur_acquis_ligne_entete"]["V"], $param_bull2016["couleur_acquis_ligne_entete"]["B"]);
		$pdf->Rect($param_bull2016["x_acquis_col_3"], $param_bull2016["y_acquis_ligne_entete"], $param_bull2016["largeur_acquis_col_3"], $param_bull2016["hauteur_acquis_ligne_entete"], 'F');
		$pdf->SetFillColor(0, 0, 0);

		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetXY($param_bull2016["x_acquis_col_3"], $param_bull2016["y_acquis_ligne_entete"]);
		$pdf->SetFont('DejaVu','',7);
		//$pdf->Cell($param_bull2016["largeur_bandeau_suivi_acquis"],7, "Suivi des acquis scolaires de l'élève",0,2,'C');
		$texte="Acquisitions, progrès et difficultés éventuelles";
		//$pdf->Cell($param_bull2016["largeur_cadre_logo_RF"]-8, $param_bull2016["hauteur_cadre_EN"]-6, $texte,0,2,'C');
		$pdf->drawTextBox($texte, $param_bull2016["largeur_acquis_col_3"], $param_bull2016["hauteur_acquis_ligne_entete"], 'L', 'M', 0);

		// Colonne 4 : Moyenne élève
		$pdf->SetFillColor($param_bull2016["couleur_acquis_colonne_moyenne_ligne_entete"]["R"], $param_bull2016["couleur_acquis_colonne_moyenne_ligne_entete"]["V"], $param_bull2016["couleur_acquis_colonne_moyenne_ligne_entete"]["B"]);
		$pdf->Rect($param_bull2016["x_acquis_col_moy"], $param_bull2016["y_acquis_ligne_entete"], $param_bull2016["largeur_acquis_col_moy"], $param_bull2016["hauteur_acquis_ligne_entete"], 'F');
		$pdf->SetFillColor(0, 0, 0);

		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetXY($param_bull2016["x_acquis_col_moy"], $param_bull2016["y_acquis_ligne_entete"]);
		$pdf->SetFont('DejaVu','',7);
		//$pdf->Cell($param_bull2016["largeur_bandeau_suivi_acquis"],7, "Suivi des acquis scolaires de l'élève",0,2,'C');
		$texte="Moyenne\nde l'élève";
		//$pdf->Cell($param_bull2016["largeur_cadre_logo_RF"]-8, $param_bull2016["hauteur_cadre_EN"]-6, $texte,0,2,'C');
		$pdf->drawTextBox($texte, $param_bull2016["largeur_acquis_col_moy"], $param_bull2016["hauteur_acquis_ligne_entete"], 'C', 'M', 0);

		// Colonne 5 : Moyenne classe
		$pdf->SetFillColor($param_bull2016["couleur_acquis_ligne_entete"]["R"], $param_bull2016["couleur_acquis_ligne_entete"]["V"], $param_bull2016["couleur_acquis_ligne_entete"]["B"]);
		$pdf->Rect($param_bull2016["x_acquis_col_moyclasse"], $param_bull2016["y_acquis_ligne_entete"], $param_bull2016["largeur_acquis_col_moyclasse"], $param_bull2016["hauteur_acquis_ligne_entete"], 'F');
		$pdf->SetFillColor(0, 0, 0);

		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetXY($param_bull2016["x_acquis_col_moyclasse"], $param_bull2016["y_acquis_ligne_entete"]);
		$pdf->SetFont('DejaVu','',7);
		//$pdf->Cell($param_bull2016["largeur_bandeau_suivi_acquis"],7, "Suivi des acquis scolaires de l'élève",0,2,'C');
		$texte="Moyenne\nde classe";
		//$pdf->Cell($param_bull2016["largeur_cadre_logo_RF"]-8, $param_bull2016["hauteur_cadre_EN"]-6, $texte,0,2,'C');
		$pdf->drawTextBox($texte, $param_bull2016["largeur_acquis_col_moyclasse"], $param_bull2016["hauteur_acquis_ligne_entete"], 'C', 'M', 0);

		// +++++++++++++++++

		// Lignes matières

		// Calcul de la hauteur des lignes matières (18mm par défaut, mais si ça ne tient pas, on réduit
		if($nb_matiere>0) {
			$y0=$param_bull2016["y_acquis_ligne_entete"]+$param_bull2016["hauteur_acquis_ligne_entete"];
			$ymax=$y0+$nb_matiere*(18+0.5);
			if($ymax<HauteurPage-$y0-10) {
			//if($ymax<HauteurPage-$y0-5) {
				$hauteur_matiere=18;
			}
			else {
				//$hauteur_matiere=(HauteurPage-$y0-5-($nb_matiere-1)*0.5)/$nb_matiere;
				$hauteur_matiere=(HauteurPage-$y0-10-($nb_matiere-1)*0.5)/$nb_matiere;
			}
		}

		$cpt_matiere=0;
		$y_courant=$y0+0.5;
		for($m=0;$m<count($tab_bull['groupe']);$m++) {
			if(isset($tab_bull['note'][$m][$i])) {
				// Colonne 1 : Matière, prof

				$pdf->SetFillColor($param_bull2016["couleur_acquis_ligne_alt".($cpt_matiere%2+1)]["R"], $param_bull2016["couleur_acquis_ligne_alt".($cpt_matiere%2+1)]["V"], $param_bull2016["couleur_acquis_ligne_alt".($cpt_matiere%2+1)]["B"]);
				$pdf->Rect($param_bull2016["x_acquis_col_1"], $y_courant, $param_bull2016["largeur_acquis_col_1"], $hauteur_matiere, 'F');
				$pdf->SetFillColor(0, 0, 0);

				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetXY($param_bull2016["x_acquis_col_1"], $y_courant);
				$pdf->SetFont('DejaVu','',8);
				/*
				//$pdf->Cell($param_bull2016["largeur_bandeau_suivi_acquis"],7, "Suivi des acquis scolaires de l'élève",0,2,'C');
				$texte="Matiere $m ".$hauteur_matiere;
				$pdf->drawTextBox($texte, $param_bull2016["largeur_acquis_col_1"], $hauteur_matiere, 'L', 'M', 0);
				*/

				if(getSettingValue('bul_rel_nom_matieres')=='nom_groupe') {
					$info_nom_matiere=$tab_bull['groupe'][$m]['name'];
				}
				elseif(getSettingValue('bul_rel_nom_matieres')=='description_groupe') {
					$info_nom_matiere=$tab_bull['groupe'][$m]['description'];
				}
				else {
					// Pour parer au bug sur la suppression de matière alors que des groupes sont conservés:
					if(isset($tab_bull['groupe'][$m]['matiere']['nom_complet'])) {
						$info_nom_matiere=$tab_bull['groupe'][$m]['matiere']['nom_complet'];
					}
					else {
						$info_nom_matiere=$tab_bull['groupe'][$m]['name']." (".$tab_bull['groupe'][$m]['id'].")";
					}
				}

				$hauteur_caractere_matiere=8;
				$cell_ajustee_texte_matiere_ratio_min_max=3;

				// 20130927 : cell_ajustee() ou pas sur le nom de matière/enseignement
				$cell_ajustee_texte_matiere=1;
				if((isset($cell_ajustee_texte_matiere))&&($cell_ajustee_texte_matiere==1)) {
					// Encadrement
					//$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_entre_matier, "",'LRBT',1,'L');

					// cell_ajustee() ne centre pas verticalement le texte.
					// On met un décalage pour ne pas coller le texte à la bordure
					$Y_decal_cell_ajustee=2;
					// On repositionne et on inscrit le nom de matière sur la moitié de la hauteur de la cellule
					//$pdf->SetXY($X_bloc_matiere, $Y_decal+$Y_decal_cell_ajustee);
					//$pdf->SetXY($param_bull2016["x_acquis_col_1"], $y_courant);

					$pdf->SetXY($param_bull2016["x_acquis_col_1"], $y_courant+$Y_decal_cell_ajustee);

					$texte=$info_nom_matiere;
					$taille_max_police=$hauteur_caractere_matiere;
					//$taille_min_police=ceil($taille_max_police/$tab_modele_pdf["cell_ajustee_texte_matiere_ratio_min_max"][$classe_id]);
					$taille_min_police=ceil($taille_max_police/$cell_ajustee_texte_matiere_ratio_min_max);

					$largeur_dispo=$param_bull2016["largeur_acquis_col_1"];
					//$h_cell=$espace_entre_matier/2-$Y_decal_cell_ajustee;
					$h_cell=$hauteur_matiere/2-$Y_decal_cell_ajustee;

					//cell_ajustee("<b>".$texte."</b>",$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');
					cell_ajustee($texte,$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');
				
				}
				else {
					$val = $pdf->GetStringWidth($info_nom_matiere);
					$taille_texte = $param_bull2016["largeur_acquis_col_1"]-2;
					$grandeur_texte='test';
					while($grandeur_texte!='ok') {
						if($taille_texte<$val)
						{
							$hauteur_caractere_matiere = $hauteur_caractere_matiere-0.3;
							$pdf->SetFont('DejaVu','B',$hauteur_caractere_matiere);
							$val = $pdf->GetStringWidth($info_nom_matiere);
						}
						else {
							$grandeur_texte='ok';
						}
					}
					$grandeur_texte='test';
					$Y_decal=$y_courant;
					$pdf->SetXY($param_bull2016["x_acquis_col_1"], $Y_decal);
					$pdf->Cell($param_bull2016["largeur_acquis_col_1"], $hauteur_matiere/2, ($info_nom_matiere),'',1,'L');
				}
				

				// On note l'ordonnée pour le nom des professeurs
				$Y_decal = $y_courant+($hauteur_matiere/2);
				$pdf->SetXY($param_bull2016["x_acquis_col_1"], $Y_decal);
				$pdf->SetFont('DejaVu','',8);

				//fich_debug_bull("\$info_nom_matiere=$info_nom_matiere\n");
				//fich_debug_bull("Le nom de matière est écrit; on est à mi-hauteur de la cellule pour écrire le nom du prof:\n");
				//fich_debug_bull("\$Y_decal=$Y_decal\n");

				// nom des professeurs

				if ( isset($tab_bull['groupe'][$m]["profs"]["list"]) )
				{
					/*
					if($tab_modele_pdf["presentation_proflist"][$classe_id]!="2") {
						// Présentation en colonne des profs
						$nb_prof_matiere = count($tab_bull['groupe'][$m]["profs"]["list"]);
						$espace_matiere_prof = $espace_entre_matier/2;
						if($nb_prof_matiere>0){
							$espace_matiere_prof = $espace_matiere_prof/$nb_prof_matiere;
						}
						$nb_pass_count = '0';
						$text_prof = '';
						while ($nb_prof_matiere > $nb_pass_count)
						{
							$tmp_login_prof=$tab_bull['groupe'][$m]["profs"]["list"][$nb_pass_count];
							$text_prof=affiche_utilisateur($tmp_login_prof,$tab_bull['eleve'][$i]['id_classe']);

							if ( $nb_prof_matiere <= 2 ) { $hauteur_caractere_prof = 8; }
							elseif ( $nb_prof_matiere == 3) { $hauteur_caractere_prof = 5; }
							elseif ( $nb_prof_matiere > 3) { $hauteur_caractere_prof = 2; }
							$pdf->SetFont('DejaVu','',$hauteur_caractere_prof);
							$val = $pdf->GetStringWidth($text_prof);
							$taille_texte = ($tab_modele_pdf["largeur_matiere"][$classe_id]);
							$grandeur_texte='test';
							while($grandeur_texte!='ok') {
								if($taille_texte<$val)
								{
									$hauteur_caractere_prof = $hauteur_caractere_prof-0.3;
									$pdf->SetFont('DejaVu','',$hauteur_caractere_prof);
									$val = $pdf->GetStringWidth($text_prof);
								}
								else {
									$grandeur_texte='ok';
								}
							}
							$grandeur_texte='test';
							$pdf->SetX($X_bloc_matiere);
							if( empty($tab_bull['groupe'][$m]["profs"]["list"][$nb_pass_count+1]) ) {
								$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_matiere_prof, ($text_prof),'LRB',1,'L');
							}
							if( !empty($tab_bull['groupe'][$m]["profs"]["list"][$nb_pass_count+1]) ) {
								$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_matiere_prof, ($text_prof),'LR',1,'L');
							}
							$nb_pass_count = $nb_pass_count + 1;
						}
					}
					else {
					*/
						// Présentation en ligne des profs
						// On n'a pas forcément le formatage choisi pour la classe...
						//$text_prof=$tab_bull['groupe'][$m]["profs"]["proflist_string"]."  ";
						$text_prof="";
						for($loop_prof_grp=0;$loop_prof_grp<count($tab_bull['groupe'][$m]["profs"]["list"]);$loop_prof_grp++) {
							$tmp_login_prof=$tab_bull['groupe'][$m]["profs"]["list"][$loop_prof_grp];
							if($loop_prof_grp>0) {$text_prof.=", ";}
							$text_prof.=affiche_utilisateur($tmp_login_prof,$tab_bull['eleve'][$i]['id_classe']);
						}

						if($text_prof!="") {
							//$espace_matiere_prof = $espace_entre_matier/2;
							$espace_matiere_prof = $hauteur_matiere/2;
							$hauteur_caractere_prof = 7;

							/*
							if($use_cell_ajustee=="n") {
								$pdf->SetFont('DejaVu','',$hauteur_caractere_prof);
								$val = $pdf->GetStringWidth($text_prof);
								$taille_texte = ($tab_modele_pdf["largeur_matiere"][$classe_id]);
								$grandeur_texte='test';
								while($grandeur_texte!='ok') {
									if($taille_texte<$val)
									{
										$hauteur_caractere_prof = $hauteur_caractere_prof-0.3;
										$pdf->SetFont('DejaVu','',$hauteur_caractere_prof);
										$val = $pdf->GetStringWidth($text_prof);
									}
									else {
										$grandeur_texte='ok';
									}
								}
								$grandeur_texte='test';
								$pdf->SetX($X_bloc_matiere);
								$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_matiere_prof, ($text_prof),'LR',1,'L');
							}
							else {
							*/
								$texte=$text_prof;
								$taille_max_police=$hauteur_caractere_prof;
								$taille_min_police=ceil($hauteur_caractere_prof/3);

								$largeur_dispo=$param_bull2016["largeur_acquis_col_1"];
								$h_cell=$espace_matiere_prof;

								$pdf->SetX($param_bull2016["x_acquis_col_1"]);

								cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');
							//}
						}
					//}
				}

				// Colonne 2 : Éléments de programmes
				$pdf->SetFillColor($param_bull2016["couleur_acquis_ligne_alt".($cpt_matiere%2+1)]["R"], $param_bull2016["couleur_acquis_ligne_alt".($cpt_matiere%2+1)]["V"], $param_bull2016["couleur_acquis_ligne_alt".($cpt_matiere%2+1)]["B"]);
				$pdf->Rect($param_bull2016["x_acquis_col_2"], $y_courant, $param_bull2016["largeur_acquis_col_2"], $hauteur_matiere, 'F');
				$pdf->SetFillColor(0, 0, 0);

				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetXY($param_bull2016["x_acquis_col_2"], $y_courant);
				$pdf->SetFont('DejaVu','',7);
				/*
				//$pdf->Cell($param_bull2016["largeur_bandeau_suivi_acquis"],7, "Suivi des acquis scolaires de l'élève",0,2,'C');
				$texte="Éléments du programme travaillés durant\nla période (connaissances/compétences)";
				//$pdf->Cell($param_bull2016["largeur_cadre_logo_RF"]-8, $param_bull2016["hauteur_cadre_EN"]-6, $texte,0,2,'C');
				$pdf->drawTextBox($texte, $param_bull2016["largeur_acquis_col_2"], $param_bull2016["hauteur_acquis_ligne_entete"], 'L', 'M', 0);
				*/

				//if((!getSettingAOui('bullNoSaisieElementsProgrammes'))&&($tab_modele_pdf["active_colonne_Elements_Programmes"][$classe_id]==='1')) {
					//$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));

					$hauteur_caractere_appreciation = 9;
					$pdf->SetFont('DejaVu','',$hauteur_caractere_appreciation);

					$texte_Elements_Programmes="";

					if((isset($tab_bull['ElementsProgrammes']['ele'][$tab_bull['eleve'][$i]['login']][$tab_bull['groupe'][$m]['id']]))&&(is_array($tab_bull['ElementsProgrammes']['ele'][$tab_bull['eleve'][$i]['login']][$tab_bull['groupe'][$m]['id']]))) {
						for($loop_mep=0;$loop_mep<count($tab_bull['ElementsProgrammes']['ele'][$tab_bull['eleve'][$i]['login']][$tab_bull['groupe'][$m]['id']]);$loop_mep++) {
							if($texte_Elements_Programmes!="") {
								$texte_Elements_Programmes.="\n";
							}
							$texte_Elements_Programmes.=$tab_bull['ElementsProgrammes']['ele'][$tab_bull['eleve'][$i]['login']][$tab_bull['groupe'][$m]['id']][$loop_mep];
						}
					}

					if($texte_Elements_Programmes=="") {
						//$pdf->Cell($tab_modele_pdf["largeur_Elements_Programmes"][$classe_id], $espace_entre_matier, ($texte_Elements_Programmes),'LRB',0,'C');
					}
					else {
						// DEBUT AJUSTEMENT TAILLE ELEMENTS PROGRAMME
						$taille_texte_total = $pdf->GetStringWidth($texte_Elements_Programmes);
						//$largeur_dispo=$tab_modele_pdf["largeur_Elements_Programmes"][$classe_id];
						$largeur_dispo=$param_bull2016["largeur_acquis_col_2"];

						if($use_cell_ajustee=="n") {
							$nb_ligne_app = '2.8';
							$taille_texte_max = $nb_ligne_app * ($largeur_dispo-4);
							$grandeur_texte='test';	

							while($grandeur_texte!='ok') {
								if($taille_texte_max < $taille_texte_total)
								{
									$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.3;
									$pdf->SetFont('DejaVu','',$hauteur_caractere_appreciation);
									$taille_texte_total = $pdf->GetStringWidth($texte_Elements_Programmes);
								}
								else {
									$grandeur_texte='ok';
								}
							}
							$grandeur_texte='test';
							$pdf->drawTextBox(($texte_Elements_Programmes), $largeur_dispo, $hauteur_matiere, 'J', 'M', 1);
						}
						else {
							$texte=$texte_Elements_Programmes;
							$taille_max_police=$hauteur_caractere_appreciation;
							$taille_min_police=ceil($taille_max_police/3);

							$h_cell=$hauteur_matiere;

							if(getSettingValue('suppr_balises_app_prof')=='y') {$texte=preg_replace('/<(.*)>/U','',$texte);}
							cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');
						}

					}

					//$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_Elements_Programmes"][$classe_id];
				//}



				// Colonne 3 : Appréciation
				$pdf->SetFillColor($param_bull2016["couleur_acquis_ligne_alt".($cpt_matiere%2+1)]["R"], $param_bull2016["couleur_acquis_ligne_alt".($cpt_matiere%2+1)]["V"], $param_bull2016["couleur_acquis_ligne_alt".($cpt_matiere%2+1)]["B"]);
				$pdf->Rect($param_bull2016["x_acquis_col_3"], $y_courant, $param_bull2016["largeur_acquis_col_3"], $hauteur_matiere, 'F');

				$pdf->SetFillColor(0, 0, 0);
				$pdf->SetXY($param_bull2016["x_acquis_col_3"], $y_courant);

				/*
				// si on autorise l'affichage des sous matière et s'il y en a alors on les affiche
				$id_groupe_select = $tab_bull['groupe'][$m]['id'];
				$pdf->SetXY($param_bull2016["x_acquis_col_3"], $Y_decal-($espace_entre_matier/2));
				$X_sous_matiere = 0; $largeur_sous_matiere=0;

				if($tab_modele_pdf["autorise_sous_matiere"][$classe_id]==='1' and !empty($tab_bull['groupe'][$m][$i]['cn_nom'])) {
					$X_sous_matiere = $X_note_moy_app+$largeur_utilise;
					$Y_sous_matiere = $Y_decal-($espace_entre_matier/2);
					$n=0;
					$largeur_texte_sousmatiere=0; $largeur_sous_matiere=0;
					while( !empty($tab_bull['groupe'][$m][$i]['cn_nom'][$n]) )
					{
						$pdf->SetFont('DejaVu','',8);
						$largeur_texte_sousmatiere = $pdf->GetStringWidth($tab_bull['groupe'][$m][$i]['cn_nom'][$n].': '.$tab_bull['groupe'][$m][$i]['cn_note'][$n]);
						if($largeur_sous_matiere<$largeur_texte_sousmatiere) { $largeur_sous_matiere=$largeur_texte_sousmatiere; }
						$n = $n + 1;
					}
					if($largeur_sous_matiere!='0') { $largeur_sous_matiere = $largeur_sous_matiere + 2; }
					$n=0;
					while( !empty($tab_bull['groupe'][$m][$i]['cn_nom'][$n]) )
					{
						$pdf->SetXY($X_sous_matiere, $Y_sous_matiere);
						$pdf->SetFont('DejaVu','',8);
						$pdf->Cell($largeur_sous_matiere, $espace_entre_matier/count($tab_bull['groupe'][$m][$i]['cn_nom']), ($tab_bull['groupe'][$m][$i]['cn_nom'][$n].': '.$tab_bull['groupe'][$m][$i]['cn_note'][$n]),1,0,'L');
						$Y_sous_matiere = $Y_sous_matiere+$espace_entre_matier/count($tab_bull['groupe'][$m][$i]['cn_nom']);
						$n = $n + 1;
					}
					$largeur_utilise = $largeur_utilise+$largeur_sous_matiere;
				}
				$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
				*/

				// calcul de la taille du texte des appréciations
				$hauteur_caractere_appreciation = 8;
				$pdf->SetFont('DejaVu','',$hauteur_caractere_appreciation);

				//suppression des espaces en début et en fin
				$app_aff = trim($tab_bull['app'][$m][$i]);

				fich_debug_bull("__________________________________________\n");
				fich_debug_bull("$app_aff\n");
				fich_debug_bull("__________________________________________\n");

				// DEBUT AJUSTEMENT TAILLE APPRECIATION
				$taille_texte_total = $pdf->GetStringWidth($app_aff);
				//$largeur_appreciation2 = $largeur_appreciation - $largeur_sous_matiere;
				$largeur_appreciation2=$param_bull2016["largeur_acquis_col_3"];

				if($use_cell_ajustee=="n") {
					//$taille_texte = (($espace_entre_matier/3)*$largeur_appreciation2);
					$nb_ligne_app = '2.8';
					//$nb_ligne_app = '3.8';
					//$nb_ligne_app = '4.8';
					$taille_texte_max = $nb_ligne_app * ($largeur_appreciation2-4);
					//$taille_texte_max = $nb_ligne_app * ($largeur_appreciation2);
					$grandeur_texte='test';

					fich_debug_bull("\$taille_texte_total=$taille_texte_total\n");
					fich_debug_bull("\$largeur_appreciation2=$largeur_appreciation2\n");
					fich_debug_bull("\$nb_ligne_app=$nb_ligne_app\n");
					//fich_debug_bull("\$taille_texte_max = \$nb_ligne_app * (\$largeur_appreciation2-4)=$nb_ligne_app * ($largeur_appreciation2-4)=$taille_texte_max\n");
					fich_debug_bull("\$taille_texte_max = \$nb_ligne_app * (\$largeur_appreciation2)=$nb_ligne_app * ($largeur_appreciation2)=$taille_texte_max\n");

					while($grandeur_texte!='ok') {
						if($taille_texte_max < $taille_texte_total)
						{
							$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.3;
							//$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.1;
							$pdf->SetFont('DejaVu','',$hauteur_caractere_appreciation);
							$taille_texte_total = $pdf->GetStringWidth($app_aff);
						}
						else {
							$grandeur_texte='ok';
						}
					}
					$grandeur_texte='test';
					$pdf->drawTextBox(($app_aff), $largeur_appreciation2, $hauteur_matiere, 'J', 'M', 1);
				}
				else {
					$texte=$app_aff;
					//$texte="Bla bla\nbli ".$app_aff;
					$taille_max_police=$hauteur_caractere_appreciation;
					$taille_min_police=ceil($taille_max_police/3);

					$largeur_dispo=$largeur_appreciation2;
					$h_cell=$hauteur_matiere;

					if(getSettingValue('suppr_balises_app_prof')=='y') {$texte=preg_replace('/<(.*)>/U','',$texte);}
					cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');
				}


				// Colonne 4 : Moyenne élève
				$pdf->SetFillColor($param_bull2016["couleur_acquis_colonne_moyenne_ligne_alt".($cpt_matiere%2+1)]["R"], $param_bull2016["couleur_acquis_colonne_moyenne_ligne_alt".($cpt_matiere%2+1)]["V"], $param_bull2016["couleur_acquis_colonne_moyenne_ligne_alt".($cpt_matiere%2+1)]["B"]);
				$pdf->Rect($param_bull2016["x_acquis_col_moy"], $y_courant, $param_bull2016["largeur_acquis_col_moy"], $hauteur_matiere, 'F');

				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetXY($param_bull2016["x_acquis_col_moy"], $y_courant);
				$pdf->SetFont('DejaVu', 'B', 8);
				//$pdf->Cell($param_bull2016["largeur_acquis_col_moy"], $hauteur_matiere, $texte,0,2,'C');
				//$pdf->drawTextBox($texte, $param_bull2016["largeur_acquis_col_moy"], $param_bull2016["hauteur_acquis_ligne_entete"], 'C', 'M', 0);

				$fleche_evolution="";

				// On filtre si la moyenne est vide, on affiche seulement un tiret
				if ($tab_bull['note'][$m][$i]=="-") {
					$valeur = "-";
				}
				elseif ($tab_bull['statut'][$m][$i]!="") {
					$valeur=$tab_bull['statut'][$m][$i];
				}
				else {
					$valeur = present_nombre($tab_bull['note'][$m][$i], $arrondi, $nb_chiffre_virgule, $chiffre_avec_zero);

					//if((isset($evolution_moyenne_periode_precedente))&&($evolution_moyenne_periode_precedente=="y")) {
					if (($evolution_moyenne_periode_precedente=='y')&&(isset($tab_bull['login_prec']))) {
						//$fleche_evolution="";

						foreach($tab_bull['login_prec'] as $key => $value) {
							// Il faut récupérer l'id_groupe et l'indice de l'élève... dans les tableaux récupérés de calcul_moy_gen.inc.php
							// Tableaux d'indices [$j][$i] (groupe, élève)
							$indice_eleve=-1;
							for($loop_l=0;$loop_l<count($tab_bull['login_prec'][$key]);$loop_l++) {
								//echo "\$tab_bull['login_prec'][$key][$loop_l]=".$tab_bull['login_prec'][$key][$loop_l]." et \$tab_bull['eleve'][$i]['login']=".$tab_bull['eleve'][$i]['login']."<br />\n";
								if($tab_bull['login_prec'][$key][$loop_l]==$tab_bull['eleve'][$i]['login']) {$indice_eleve=$loop_l;break;}
							}
							//echo "\$indice_eleve=$indice_eleve<br />\n";

							if($indice_eleve!=-1) {
								// Recherche du groupe
								$indice_grp=-1;
								for($loop_l=0;$loop_l<count($tab_bull['group_prec'][$key]);$loop_l++) {
									//echo "\$tab_bull['group_prec'][$key][$loop_l]['id']=".$tab_bull['group_prec'][$key][$loop_l]['id']." et \$tab_bull['groupe'][$m]['id']=".$tab_bull['groupe'][$m]['id']."<br />\n";
									if($tab_bull['group_prec'][$key][$loop_l]['id']==$tab_bull['groupe'][$m]['id']) {$indice_grp=$loop_l;break;}
								}
								//echo "\$indice_grp=$indice_grp<br />\n";

								if($indice_grp!=-1) {
									if(isset($tab_bull['statut_prec'][$key][$indice_grp][$indice_eleve])) {
										if ($tab_bull['statut_prec'][$key][$indice_grp][$indice_eleve]=="") {
											//echo "\$tab_bull['note'][$m][$i]=".$tab_bull['note'][$m][$i]."<br />\n";
											//echo "\$tab_bull['note_prec'][$key][$indice_grp][$indice_eleve]=".$tab_bull['note_prec'][$key][$indice_grp][$indice_eleve]."<br />\n";
											// 20151201: METTRE UN SEUIL POUR L'EVOLUTION DE LA MOYENNE
											if($tab_bull['note'][$m][$i]>=$tab_bull['note_prec'][$key][$indice_grp][$indice_eleve]+$tab_modele_pdf["evolution_moyenne_periode_precedente_seuil"][$classe_id]) {
												$fleche_evolution="+";
											}
											elseif($tab_bull['note'][$m][$i]<=$tab_bull['note_prec'][$key][$indice_grp][$indice_eleve]-$tab_modele_pdf["evolution_moyenne_periode_precedente_seuil"][$classe_id]) {
												$fleche_evolution="-";
											}
											else {
												$fleche_evolution="";
											}
											//echo "\$fleche_evolution=".$fleche_evolution."<br />\n";

											//$valeur = present_nombre($tab_bull['note_prec'][$key][$indice_grp][$indice_eleve], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
										}

									}
								}
							}

						}


					}
				}
				if($fleche_evolution!="") {$fleche_evolution=" ".$fleche_evolution;}
				//$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $hauteur_matiere/$nb_sousaffichage, $valeur.$fleche_evolution,1,2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
				$pdf->Cell($param_bull2016["largeur_acquis_col_moy"], $hauteur_matiere, $valeur.$fleche_evolution, 0, 2, 'C', 1);
				$valeur = "";


				// Colonne 5 : Moyenne classe
				$pdf->SetFillColor($param_bull2016["couleur_acquis_ligne_alt".($cpt_matiere%2+1)]["R"], $param_bull2016["couleur_acquis_ligne_alt".($cpt_matiere%2+1)]["V"], $param_bull2016["couleur_acquis_ligne_alt".($cpt_matiere%2+1)]["B"]);
				$pdf->Rect($param_bull2016["x_acquis_col_moyclasse"], $y_courant, $param_bull2016["largeur_acquis_col_moyclasse"], $hauteur_matiere, 'F');

				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetXY($param_bull2016["x_acquis_col_moyclasse"], $y_courant);
				$pdf->SetFont('DejaVu','',7);
				if (($tab_bull['moy_classe_grp'][$m]=="-")||($tab_bull['moy_classe_grp'][$m]=="")) {
					$valeur = "-";
				} else {
					$valeur = present_nombre($tab_bull['moy_classe_grp'][$m], $arrondi, $nb_chiffre_virgule, $chiffre_avec_zero);
				}
				$pdf->Cell($param_bull2016["largeur_acquis_col_moyclasse"], $hauteur_matiere, $valeur,'',0,'C');



				$y_courant+=$hauteur_matiere+0.5;

				$cpt_matiere++;
			}
		}

		// FILIGRANE PERIODE NON CLOSE
		if($tab_bull['verouiller']=="N") {
			$pdf->SetFont('DejaVu','B',40);
			$pdf->SetTextColor(255,192,203);
			//$pdf->TextWithRotation(40,190,$texte_filigrame[$classe_id],45);
			$pdf->TextWithRotation(40,210,"ATTENTION : Période non close",45);
			$pdf->SetTextColor(0,0,0);
		}
		elseif($tab_modele_pdf["affiche_filigrame"][$classe_id]==='1'){
			$pdf->SetFont('DejaVu','B',50);
			$pdf->SetTextColor(255,192,203);
			//$pdf->TextWithRotation(40,190,$texte_filigrame[$classe_id],45);
			$pdf->TextWithRotation(40,190,$tab_modele_pdf["texte_filigrame"][$classe_id],45);
			$pdf->SetTextColor(0,0,0);
		}

		//=========================================
		//=========================================
		//=========================================

		// Deuxième page:
		$pdf->AddPage(); //ajout d'une page au document
		// En fin de cycle (fin d'année de l'année de fin de cycle), il faut même une autre page... prévoir le recto/verso 

		// AP, EPI, Parcours... (ajouter un tag sur des enseignements pour ne pas les faire apparaitre dans le tableau précédent mais dans les AP, EPI,...) et permettre aussi une gestion des AP via des AID (ajouter un tag AP/EPI/Parcours aux AID pour les faire apparaitre ici)
		// Que fait-on des AID dans le nouveau bulletin s'il ne s'agit ni d'AP, ni d'EPI, ni d'un Parcours...

		if($cycle==4) {
			$y_bandeau_bilan_acquisitions=$param_bull2016["y_bandeau_bilan_acquisitions_cycle_4"];
			$y_bilan_acquisitions=$param_bull2016["y_bilan_acquisitions_cycle_4"];
			$hauteur_bilan_acquisitions=$param_bull2016["hauteur_bilan_acquisitions_cycle_4"];

			//=========================================

			// EPI en cycle 4

			//=========================================

			// AP

			//=========================================

			// Parcours éducatifs

			//=========================================

			/*
			// Bandeau Bilan de l'acquisition des connaissances et compétences

			$pdf->SetFillColor($param_bull2016["couleur_bandeau_bilan_acquisitions"]["R"], $param_bull2016["couleur_bandeau_bilan_acquisitions"]["V"], $param_bull2016["couleur_bandeau_bilan_acquisitions"]["B"]);

			$pdf->Rect($param_bull2016["x_bandeau_bilan_acquisitions"], $param_bull2016["y_bandeau_bilan_acquisitions_cycle_4"], $param_bull2016["largeur_bandeau_bilan_acquisitions"], $param_bull2016["hauteur_bandeau_bilan_acquisitions"], 'F');

			$pdf->SetFillColor(0, 0, 0);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->SetXY($param_bull2016["x_bandeau_bilan_acquisitions"], $param_bull2016["y_bandeau_bilan_acquisitions_cycle_4"]+1);
			$pdf->SetFont('DejaVu','B',12);
			$pdf->Cell($param_bull2016["largeur_bandeau_bilan_acquisitions"],7, "Bilan de l'acquisition des connaissances et compétences",0,2,'C');

			// Cadre synthèse de l'évolution des acquis...

			$pdf->SetFillColor($param_bull2016["couleur_bilan_acquisitions"]["R"], $param_bull2016["couleur_bilan_acquisitions"]["V"], $param_bull2016["couleur_bilan_acquisitions"]["B"]);

			$pdf->Rect($param_bull2016["x_bilan_acquisitions"], $param_bull2016["y_bilan_acquisitions_cycle_4"], $param_bull2016["largeur_bilan_acquisitions"], $param_bull2016["hauteur_bilan_acquisitions_cycle_4"], 'F');

			$pdf->SetFillColor(0, 0, 0);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY($param_bull2016["x_bilan_acquisitions"], $param_bull2016["y_bilan_acquisitions_cycle_4"]+1);
			$pdf->SetFont('DejaVu','',9);
			//$pdf->Cell($param_bull2016["largeur_bilan_acquisitions"],$param_bull2016["y_bilan_acquisitions_cycle_4"], "Synthèse de l'évolution des acquis scolaires et conseils pour progresser :",0,2,'L');
			$pdf->Cell($param_bull2016["largeur_bilan_acquisitions"],7, "Synthèse de l'évolution des acquis scolaires et conseils pour progresser :",0,2,'L');

			// A FAIRE : Insérer l'avis du conseil de classe
			*/

			//=========================================

		}
		else {
			$y_bandeau_bilan_acquisitions=$param_bull2016["y_bandeau_bilan_acquisitions_cycle_3"];
			$y_bilan_acquisitions=$param_bull2016["y_bilan_acquisitions_cycle_3"];
			$hauteur_bilan_acquisitions=$param_bull2016["hauteur_bilan_acquisitions_cycle_3"];

			//=========================================

			// AP

			//=========================================

			// Parcours éducatifs

		}

		//=========================================

		// Bandeau Bilan de l'acquisition des connaissances et compétences

		$pdf->SetFillColor($param_bull2016["couleur_bandeau_bilan_acquisitions"]["R"], $param_bull2016["couleur_bandeau_bilan_acquisitions"]["V"], $param_bull2016["couleur_bandeau_bilan_acquisitions"]["B"]);

		$pdf->Rect($param_bull2016["x_bandeau_bilan_acquisitions"], $y_bandeau_bilan_acquisitions, $param_bull2016["largeur_bandeau_bilan_acquisitions"], $param_bull2016["hauteur_bandeau_bilan_acquisitions"], 'F');

		$pdf->SetFillColor(0, 0, 0);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->SetXY($param_bull2016["x_bandeau_bilan_acquisitions"], $y_bandeau_bilan_acquisitions+1);
		$pdf->SetFont('DejaVu','B',12);
		$pdf->Cell($param_bull2016["largeur_bandeau_bilan_acquisitions"],7, "Bilan de l'acquisition des connaissances et compétences",0,2,'C');



		// Cadre synthèse de l'évolution des acquis...

		$pdf->SetFillColor($param_bull2016["couleur_bilan_acquisitions"]["R"], $param_bull2016["couleur_bilan_acquisitions"]["V"], $param_bull2016["couleur_bilan_acquisitions"]["B"]);
		$pdf->Rect($param_bull2016["x_bilan_acquisitions"], $y_bilan_acquisitions, $param_bull2016["largeur_bilan_acquisitions"], $hauteur_bilan_acquisitions, 'F');

		$pdf->SetFillColor(0, 0, 0);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetXY($param_bull2016["x_bilan_acquisitions"], $y_bilan_acquisitions+1);
		$pdf->SetFont('DejaVu','',9);
		$pdf->Cell($param_bull2016["largeur_bilan_acquisitions"],7, "Synthèse de l'évolution des acquis scolaires et conseils pour progresser :",0,2,'L');


		// Avis du conseil de classe

		$pdf->SetXY($param_bull2016["x_bilan_acquisitions"]+2.5, $y_bilan_acquisitions+5);

		$marge_droite_avis_cons=5;
		$pdf->SetFont('DejaVu','',10);
		$texteavis = $tab_bull['avis'][$i];

		if(($param_bull2016["affich_mentions"]=="y")||($param_bull2016["avec_coches_mentions"]=="y")) {
			if((!isset($tableau_des_mentions_sur_le_bulletin))||(!is_array($tableau_des_mentions_sur_le_bulletin))||(count($tableau_des_mentions_sur_le_bulletin)==0)) {
				$tableau_des_mentions_sur_le_bulletin=get_mentions($classe_id);
			}
			if(isset($tableau_des_mentions_sur_le_bulletin[$tab_bull['id_mention'][$i]])) {
				$textmention=$tableau_des_mentions_sur_le_bulletin[$tab_bull['id_mention'][$i]];
			}
			else {$textmention="-";}

			if($param_bull2016["avec_coches_mentions"]=="y") {
				if(count($tableau_des_mentions_sur_le_bulletin)>0) {
					$marge_droite_avis_cons=40;
				}
				else {
					$marge_droite_avis_cons=5;
				}
			}
			else {
				$marge_droite_avis_cons=5;
				if(($param_bull2016["affich_mentions"]!="n")&&($textmention!="")&&($textmention!="-")) {
					if($use_cell_ajustee=="n") {
						if($param_bull2016["affich_intitule_mentions"]!="n") {
							$texteavis.="\n".ucfirst($gepi_denom_mention)." : ";
						}
						$texteavis.=$textmention;
					}
					else {
						if($param_bull2016["affich_intitule_mentions"]!="n") {
							$texteavis.="\n"."<b>".ucfirst($gepi_denom_mention)." :</b> ";
						}
						$texteavis.=$textmention;
					}
				}
			}
		}

		if($use_cell_ajustee=="n") {
			$pdf->drawTextBox(($texteavis), $param_bull2016["largeur_bilan_acquisitions"]-$marge_droite_avis_cons, $hauteur_bilan_acquisitions-10, 'J', 'M', 0);
		}
		else {
			$texte=$texteavis;
			$taille_max_police=10;
			$taille_min_police=ceil($taille_max_police/3);

			$largeur_dispo=$param_bull2016["largeur_bilan_acquisitions"]-$marge_droite_avis_cons;
			$h_cell=$hauteur_bilan_acquisitions-10;

			cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');
		}


		/*
		$X_pp_aff=$param_bull2016["x_bilan_acquisitions"];

		//$Y_pp_aff=$tab_modele_pdf["Y_avis_cons"][$classe_id]+$tab_modele_pdf["hauteur_avis_cons"][$classe_id]-5;
		$Y_pp_aff=$y_bilan_acquisitions+$hauteur_avis_cons_init-5;

		$pdf->SetXY($X_pp_aff,$Y_pp_aff);
		if ( $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id] != '' and is_numeric($tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id]) and $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id]>0 and $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id] < '15' ) {
			$taille = $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id];
		} else {
			$taille = '10';
		}
		$pdf->SetFont('DejaVu','I',$taille);
		// Le nom du professeur principal
		$pp_classe[$i]="";
		//if(isset($tab_bull['eleve'][$i]['pp']['login'])) {
		if($tab_modele_pdf["afficher_tous_profprincipaux"][$classe_id]==1) {
			$index_pp='pp_classe';
		}
		else {
			$index_pp='pp';
		}
		if(isset($tab_bull['eleve'][$i][$index_pp][0]['login'])) {
			$pp_classe[$i]="<b>".ucfirst($tab_bull['gepi_prof_suivi'])."</b> : ";
			$pp_classe[$i].="<i>".affiche_utilisateur($tab_bull['eleve'][$i][$index_pp][0]['login'],$tab_bull['eleve'][$i]['id_classe'])."</i>";
			for($i_pp=1;$i_pp<count($tab_bull['eleve'][$i][$index_pp]);$i_pp++) {
				$pp_classe[$i].=", ";
				$pp_classe[$i].="<i>".affiche_utilisateur($tab_bull['eleve'][$i][$index_pp][$i_pp]['login'],$tab_bull['eleve'][$i]['id_classe'])."</i>";
			}
		}
		else {
			$pp_classe[$i]="";
		}
		//$pdf->MultiCellTag(200, 5, ($pp_classe[$i]), '', 'J', '');
		//$pdf->ext_MultiCellTag(200, 5, ($pp_classe[$i]), '', 'J', '');

		$taille_max_police=$taille;
		$taille_min_police=ceil($taille_max_police/3);
		//$largeur_dispo=200;
		$largeur_dispo=$tab_modele_pdf["longeur_avis_cons"][$classe_id];
		$h_cell=5;
		cell_ajustee($pp_classe[$i],$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');
		*/


		if($param_bull2016["avec_coches_mentions"]=="y") {
			$pdf->SetFont('DejaVu','',9);
			$X_pp_aff=$param_bull2016["x_bilan_acquisitions"]+$param_bull2016["largeur_bilan_acquisitions"]-35;
			$Y_pp_aff=$y_bilan_acquisitions+5;
			$pdf->SetXY($X_pp_aff,$Y_pp_aff);

			if((!isset($tableau_des_mentions_sur_le_bulletin))||(!is_array($tableau_des_mentions_sur_le_bulletin))||(count($tableau_des_mentions_sur_le_bulletin)==0)) {
				$tableau_des_mentions_sur_le_bulletin=get_mentions($classe_id);
			}

			$loop_mention=0;
			foreach($tableau_des_mentions_sur_le_bulletin as $key_mention => $value_mention) {
				$pdf->Cell(35,4, $value_mention,0,2,'L');
				$loop_mention++;
			}

			$loop_mention=0;
			foreach($tableau_des_mentions_sur_le_bulletin as $key_mention => $value_mention) {
				$pdf->Rect($X_pp_aff+30, $Y_pp_aff+4*$loop_mention+0.3, 2.4, 3);

				if($key_mention==$tab_bull['id_mention'][$i]) {
					$pdf->SetXY($X_pp_aff-1.73,$Y_pp_aff+$loop_mention*4);
					$pdf->Cell(35,4, 'X',0,2,'R');
				}
				$loop_mention++;
			}
			$pdf->Rect($X_pp_aff, $Y_pp_aff+0.1, 0.01, $loop_mention*4);
		}


		//=========================================

		// Bandeau Communication avec la famille

		$pdf->SetFillColor($param_bull2016["couleur_bandeau_communication_famille"]["R"], $param_bull2016["couleur_bandeau_communication_famille"]["V"], $param_bull2016["couleur_bandeau_communication_famille"]["B"]);

		$pdf->Rect($param_bull2016["x_bandeau_communication_famille"], $param_bull2016["y_bandeau_communication_famille"], $param_bull2016["largeur_bandeau_communication_famille"], $param_bull2016["hauteur_bandeau_communication_famille"], 'F');

		$pdf->SetFillColor(0, 0, 0);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->SetXY($param_bull2016["x_bandeau_communication_famille"], $param_bull2016["y_bandeau_communication_famille"]+1);
		$pdf->SetFont('DejaVu','B',12);
		$pdf->Cell($param_bull2016["largeur_bandeau_communication_famille"],7, "Communication avec la famille",0,2,'C');

		// Cadre Vie scolaire

		$pdf->SetFillColor($param_bull2016["couleur_communication_famille"]["R"], $param_bull2016["couleur_communication_famille"]["V"], $param_bull2016["couleur_communication_famille"]["B"]);

		$pdf->Rect($param_bull2016["x_communication_famille"], $param_bull2016["y_communication_famille"], $param_bull2016["largeur_communication_famille"], $param_bull2016["hauteur_communication_famille"], 'F');

		$pdf->SetFillColor(0, 0, 0);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetXY($param_bull2016["x_communication_famille"], $param_bull2016["y_communication_famille"]);
		$pdf->SetFont('DejaVu','',9);
		//$pdf->Cell($param_bull2016["largeur_bilan_acquisitions"],$param_bull2016["y_bilan_acquisitions_cycle_3"], "Synthèse de l'évolution des acquis scolaires et conseils pour progresser :",0,2,'L');
		//$pdf->Cell($param_bull2016["largeur_communication_famille"],10, "Vie scolaire (assiduité, ponctualité ; respect du règlement intérieur ; pariticipation à la vie de l'établissement) :",0,2,'L');
		//$pdf->Cell(90, 10, "Vie scolaire (assiduité, ponctualité ; respect du règlement intérieur ; participation à la vie de l'établissement) :",0,2,'L');
		$texte="Vie scolaire (assiduité, ponctualité ; respect du règlement intérieur ;\n participation à la vie de l'établissement) :";
		$taille_max_police=9;
		$taille_min_police=ceil($taille_max_police/3);
		$largeur_dispo=$param_bull2016["largeur_communication_famille"];
		$h_cell=10;
		//if(getSettingValue('suppr_balises_app_prof')=='y') {$texte=preg_replace('/<(.*)>/U','',$texte);}
		cell_ajustee($texte,$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');

		if($tab_bull['eleve'][$i]['appreciation_absences'] != "")
		{
			// supprimer les espaces
			//$text_absences_appreciation = trim(str_replace(array("\r\n","\r","\n"), ' ', unhtmlentities($tab_bull['eleve'][$i]['appreciation_absences'])));
			$text_absences_appreciation = trim(unhtmlentities($tab_bull['eleve'][$i]['appreciation_absences']));
			$info_absence_appreciation=$text_absences_appreciation;

			$pdf->SetXY($param_bull2016["x_communication_famille"], $param_bull2016["y_communication_famille"]+10);
			$pdf->SetFont('DejaVu','',8);
			$val = $pdf->GetStringWidth($info_absence_appreciation);
			// nombre de lignes que prend la remarque cpe
			//Arrondi à l'entier supérieur : ceil()
			$nb_ligne = 1;
			$nb_ligne = ceil($val / 200);
			$hauteur_pris = $nb_ligne * 3;

			$taille_max_police=8;
			$taille_min_police=ceil($taille_max_police/3);
			$largeur_dispo=$param_bull2016["largeur_communication_famille"];
			$h_cell=22; // A ajuster selon ce qu'on affiche des retards, nj, j,...
			cell_ajustee($info_absence_appreciation,$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');
		}

		/*
		// Identité CPE
			if(($tab_modele_pdf["afficher_abs_cpe"][$classe_id]=='1')&&(isset($tab_bull['eleve'][$i]['cperesp_civilite']))&&(isset($tab_bull['eleve'][$i]['cperesp_login']))) {
				$pdf->SetFont('DejaVu','',8);
				// C.P.E.
				$info_absence = $info_absence." (".ucfirst($gepi_cpe_suivi)." chargé";
				if($tab_bull['eleve'][$i]['cperesp_civilite']!="M.") {
					$info_absence = $info_absence."e";
				}
				$info_absence = $info_absence." du suivi : <i>".affiche_utilisateur($tab_bull['eleve'][$i]['cperesp_login'],$tab_bull['id_classe'])."</i>)";
			}
			//$pdf->MultiCellTag($tab_modele_pdf["largeur_cadre_absences"][$classe_id], 5, ($info_absence), '', 'J', '');
			//$pdf->ext_MultiCellTag($tab_modele_pdf["largeur_cadre_absences"][$classe_id], 5, $info_absence, '', 'J', '');

			$taille_max_police=8;
			$taille_min_police=ceil($taille_max_police/3);
			$largeur_dispo=$tab_modele_pdf["largeur_cadre_absences"][$classe_id];
			$h_cell=5;
			cell_ajustee($info_absence,$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');
		*/

		// A FAIRE : Pouvoir ne pas faire apparaitre certaines lignes

		$h_ligne_retard_abs=3.5;
		$decal=3.5*3;
		// Heures perdues
		$afficher_nb_heures_perdues="n";
		if($afficher_nb_heures_perdues=="y") {
			$pdf->SetXY($param_bull2016["x_communication_famille"], $param_bull2016["y_communication_famille"]+34+$decal);
			$pdf->Cell($param_bull2016["largeur_communication_famille"],7, "Nombre d'heures de cours manquées du fait de ses absences, justifiées ou non justifiées : "."       heure(s)",0,2,'L');
			$decal-=3.5;
		}

		// Non justifiées
		$afficher_non_justifiees="y";
		if($afficher_non_justifiees=="y") {
			$nb_nj=$tab_bull['eleve'][$i]['eleve_nj'];
			$s="";
			if("$nb_nj"=="?") {
				$s="s";
			}
			elseif($nb_nj<=1) {
				$s="";
			}
			elseif($nb_nj>=1) {
				$s="s";
			}
			$pdf->SetXY($param_bull2016["x_communication_famille"], $param_bull2016["y_communication_famille"]+34+$decal);
			$pdf->Cell($param_bull2016["largeur_communication_famille"],7, "Absences non justifiées par les responsables légaux : ".$nb_nj." demi-journée".$s,0,2,'L');
			$decal-=3.5;
		}

		// Justifiées
		$afficher_justifiees="y";
		$afficher_total_abs="n";
		if($afficher_justifiees=="y") {
			$nb_j=$tab_bull['eleve'][$i]['eleve_absences']-$tab_bull['eleve'][$i]['eleve_nj'];
			$s="";
			if("$nb_j"=="?") {
				$s="s";
			}
			elseif($nb_j<=1) {
				$s="";
			}
			elseif($nb_j>=1) {
				$s="s";
			}
			$pdf->SetXY($param_bull2016["x_communication_famille"], $param_bull2016["y_communication_famille"]+34+$decal);
			$pdf->Cell($param_bull2016["largeur_communication_famille"],7, "Absences justifiées par les responsables légaux : ".$nb_j." demi-journée".$s,0,2,'L');
			$decal-=3.5;
		}
		elseif($afficher_total_abs=="y") {
			$nb_j=$tab_bull['eleve'][$i]['eleve_absences'];
			$s="";
			if("$nb_j"=="?") {
				$s="s";
			}
			elseif($nb_j<=1) {
				$s="";
			}
			elseif($nb_j>=1) {
				$s="s";
			}
			$pdf->SetXY($param_bull2016["x_communication_famille"], $param_bull2016["y_communication_famille"]+34+$decal);
			$pdf->Cell($param_bull2016["largeur_communication_famille"],7, "Total des absences : ".$nb_j." demi-journée".$s,0,2,'L');
			$decal-=3.5;
		}

		$afficher_retards="y";
		if($afficher_retards=="y") {
			$pdf->SetXY($param_bull2016["x_communication_famille"], $param_bull2016["y_communication_famille"]+34+$decal);
			$pdf->SetFont('DejaVu','',8);
			$pdf->Cell($param_bull2016["largeur_communication_famille"],7, "Retards : ".$tab_bull['eleve'][$i]['eleve_retards'],0,2,'L');
		}



		// Cadre chef étab

		$pdf->SetFillColor($param_bull2016["couleur_communication_famille"]["R"], $param_bull2016["couleur_communication_famille"]["V"], $param_bull2016["couleur_communication_famille"]["B"]);
		$pdf->Rect($param_bull2016["x_signature_chef"], $param_bull2016["y_signature_chef"], $param_bull2016["largeur_signature_chef"], $param_bull2016["hauteur_signature_chef"], 'F');

		$pdf->SetXY($param_bull2016["x_signature_chef"], $param_bull2016["y_signature_chef"]);
		$pdf->SetFillColor(0, 0, 0);
		$pdf->SetTextColor(0, 0, 0);
		//$pdf->Cell($param_bull2016["largeur_signature_chef"],7, "Date, nom et signature du chef d'établissement",0,2,'L');
		$pdf->drawTextBox("Date, nom et signature\ndu chef de l'établissement", $param_bull2016["largeur_signature_chef"], $param_bull2016["hauteur_signature_chef"], 'R', 'T', 0);


		$pdf->SetXY($param_bull2016["x_signature_chef"], $param_bull2016["y_signature_chef"]);

		if((isset($signature_bull[$tab_bull['id_classe']]))&&($signature_bull[$tab_bull['id_classe']]!="")&&(file_exists($signature_bull[$tab_bull['id_classe']]))) {
			$fich_sign=$signature_bull[$tab_bull['id_classe']];

			$X_sign = $param_bull2016["x_signature_chef"];
			$Y_sign = $param_bull2016["y_signature_chef"];

			$largeur_dispo=$param_bull2016["largeur_signature_chef"]-10;
			// On ajuste mieux la hauteur de l'image, quitte à ce que le tampon/signature soit en surimpression (ou plutôt sous-impression) avec le Nom du chef en première ligne du cadre.
			$hauteur_dispo=$param_bull2016["hauteur_signature_chef"]-2;

			$tmp_dim_photo=getimagesize($fich_sign);
			$ratio_l=$tmp_dim_photo[0]/$largeur_dispo;
			$ratio_h=$tmp_dim_photo[1]/$hauteur_dispo;
			if($ratio_l>$ratio_h) {
				$L_sign = $largeur_dispo;
				$H_sign = $largeur_dispo*$tmp_dim_photo[1]/$tmp_dim_photo[0];
			}
			else {
				$H_sign = $hauteur_dispo;
				$L_sign = $hauteur_dispo*$tmp_dim_photo[0]/$tmp_dim_photo[1];
			}

			/*
			echo "\$X_sign=$X_sign<br />\n";
			echo "\$Y_sign=$Y_sign<br />\n";
			echo "\$L_sign=$L_sign<br />\n";
			echo "\$H_sign=$H_sign<br />\n";
			*/

			$X_sign += ($param_bull2016["largeur_signature_chef"]-$L_sign) / 2;
			$Y_sign += ($param_bull2016["hauteur_signature_chef"]-$H_sign) / 2;

			$tmp_dim_photo=getimagesize($fich_sign);

			if((isset($tmp_dim_photo[2]))&&($tmp_dim_photo[2]==2)) {
				//$pdf->Image($fich_sign, $X_sign, $Y_sign, $L_sign, $H_sign);
				$pdf->Image($fich_sign, round($X_sign), round($Y_sign), round($L_sign), round($H_sign));
			}
		}

		$pdf->SetFont('DejaVu','',10);
		if($param_bull2016["affichage_haut_responsable"]=='y') {
			/*
			$pdf->SetXY($param_bull2016["x_signature_chef"], $param_bull2016["y_signature_chef"]+7);
			if($param_bull2016["affiche_fonction_chef"]=='y') {
				if($param_bull2016["taille_texte_fonction_chef"]!= '' and $param_bull2016["taille_texte_fonction_chef"]!='0' and $param_bull2016["taille_texte_fonction_chef"]<'15') {
					$taille=$param_bull2016["taille_texte_fonction_chef"];
				} else {
					$taille='9';
				}
				$pdf->SetFont('DejaVu','B',$taille);
				$pdf->MultiCell($param_bull2016["largeur_signature_chef"], 5, ($tab_bull['formule']),0,2,'');
				$pdf->SetX($param_bull2016["x_signature_chef"]);
			}
			else {
				// Date seule
			}
			*/

			if($param_bull2016["taille_texte_identite_chef"]!='' and $param_bull2016["taille_texte_identite_chef"]!='0' and $param_bull2016["taille_texte_identite_chef"]<'15') {
				$taille = $param_bull2016["taille_texte_identite_chef"];
			} else {
				$taille='8';
			}
			$pdf->SetXY($param_bull2016["x_signature_chef"], $param_bull2016["y_signature_chef"]+7);
			$pdf->SetFont('DejaVu','I',$taille);
			$pdf->MultiCell($param_bull2016["largeur_signature_chef"], 5, "Le ".strftime("%d/%m/%Y").", ".$tab_bull['suivi_par'], 0, 2, '');
		} else {
			$pdf->MultiCell($param_bull2016["largeur_signature_chef"], 5, ("Visa du Chef d'établissement\nou de son délégué"),0,2,'');
		}


		// Cadre Visa famille

		$pdf->SetFillColor($param_bull2016["couleur_communication_famille"]["R"], $param_bull2016["couleur_communication_famille"]["V"], $param_bull2016["couleur_communication_famille"]["B"]);
		$pdf->Rect($param_bull2016["x_visa_famille"], $param_bull2016["y_visa_famille"], $param_bull2016["largeur_visa_famille"], $param_bull2016["hauteur_visa_famille"], 'F');

		$pdf->SetXY($param_bull2016["x_visa_famille"], $param_bull2016["y_visa_famille"]);
		$pdf->SetFillColor(0, 0, 0);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Cell($param_bull2016["largeur_visa_famille"],7, "Visa de la famille",0,2,'L');

		$pdf->SetXY($param_bull2016["x_visa_famille"], $param_bull2016["y_visa_famille"]);
		$pdf->Cell($param_bull2016["largeur_visa_famille"], 7, "Date, nom et signature des responsables légaux",0,2,'R');

		//=========================================
		//=========================================
		//=========================================
		//=========================================

		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


		// Pour ne pas afficher la suite:
		$bidon_reserve=0;
		if($bidon_reserve==1) {
			// ============= DEBUT BLOC ETABLISSEMENT ==========================

			// Bloc identification etablissement
			$logo = '../images/'.getSettingValue('logo_etab');
			$format_du_logo = my_strtolower(str_replace('.','',strstr(getSettingValue('logo_etab'), '.')));

			// Logo
			//if($tab_modele_pdf["affiche_logo_etab"][$classe_id]==='1' and file_exists($logo) and getSettingValue('logo_etab') != '' and ($format_du_logo==='jpg' or $format_du_logo==='png'))
			if($tab_modele_pdf["affiche_logo_etab"][$classe_id]==='1' and file_exists($logo) and getSettingValue('logo_etab') != '' and ($format_du_logo==='jpg' or $format_du_logo==='png'))
			{
				$valeur=redimensionne_image($logo, $tab_modele_pdf["L_max_logo"][$classe_id], $tab_modele_pdf["H_max_logo"][$classe_id]);
				$X_logo = 5;
				$Y_logo = 5;
				$L_logo = $valeur[0];
				$H_logo = $valeur[1];
				$X_etab = $X_logo + $L_logo + 1;
				$Y_etab = $Y_logo;

				if ( !isset($tab_modele_pdf["centrage_logo"][$classe_id]) or empty($tab_modele_pdf["centrage_logo"][$classe_id]) ) {
					$tab_modele_pdf["centrage_logo"][$classe_id] = '0';
				}

				if ( $tab_modele_pdf["centrage_logo"][$classe_id] === '1' ) {
					// centrage du logo
					$centre_du_logo = ( $H_logo / 2 );
					$Y_logo = $tab_modele_pdf["Y_centre_logo"][$classe_id] - $centre_du_logo;
				}

				//logo
				$tmp_dim_photo=getimagesize($logo);
				if((isset($tmp_dim_photo[2]))&&($tmp_dim_photo[2]==2)) {
					$pdf->Image($logo, $X_logo, $Y_logo, $L_logo, $H_logo);
				}
			}

			//=========================================

			// Adresse établissement
			if ( !isset($X_etab) or empty($X_etab) ) {
				$X_etab = '5';
				$Y_etab = '5';
			}
			$pdf->SetXY($X_etab,$Y_etab);
			$pdf->SetFont('DejaVu','',14);

			//=========================
			// AJOUT: boireaus 20081224
			//        Ajout du test $tab_modele_pdf["affiche_nom_etab"][$classe_id] et $tab_modele_pdf["affiche_adresse_etab"][$classe_id]
			//=========================
			//$tab_modele_pdf["affiche_nom_etab"][$classe_id]=0;
			if(((isset($tab_modele_pdf["affiche_nom_etab"][$classe_id]))&&($tab_modele_pdf["affiche_nom_etab"][$classe_id]!="0"))||
				(!isset($tab_modele_pdf["affiche_nom_etab"][$classe_id]))) {
				// mettre en gras le nom de l'établissement si $nom_etab_gras = 1
				if ( $tab_modele_pdf["nom_etab_gras"][$classe_id] === '1' ) {
					$pdf->SetFont('DejaVu','B',14);
				}
				$pdf->Cell(90,7, ($gepiSchoolName),0,2,'');
			}

			//$tab_modele_pdf["affiche_adresse_etab"][$classe_id]=0;
			if(((isset($tab_modele_pdf["affiche_adresse_etab"][$classe_id]))&&($tab_modele_pdf["affiche_adresse_etab"][$classe_id]!="0"))||
				(!isset($tab_modele_pdf["affiche_adresse_etab"][$classe_id]))) {
				$pdf->SetFont('DejaVu','',10);

				if ( $gepiSchoolAdress1 != '' ) {
					$pdf->Cell(90,5, ($gepiSchoolAdress1),0,2,'');
				}
				if ( $gepiSchoolAdress2 != '' ) {
					$pdf->Cell(90,5, ($gepiSchoolAdress2),0,2,'');
				}

				$pdf->Cell(90,5, ($gepiSchoolZipCode." ".$gepiSchoolCity),0,2,'');
			}

			$passealaligne = '0';
			// entête téléphone
			// emplacement du cadre télécom
			$x_telecom = $pdf->GetX();
			$y_telecom = $pdf->GetY();

			// Affichage du tel de l'établissement
			if( $tab_modele_pdf["entente_tel"][$classe_id]==='1' ) {
				$grandeur = ''; 
				$text_tel = '';
				if ( $tab_modele_pdf["tel_image"][$classe_id] != '' ) {
					$a = $pdf->GetX();
					$b = $pdf->GetY();
					$ima = '../images/imabulle/'.$tab_modele_pdf["tel_image"][$classe_id].'.jpg';
					$valeurima=redimensionne_image($ima, 15, 15);
					$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
					$text_tel = '      '.$gepiSchoolTel;
					$grandeur = $pdf->GetStringWidth($text_tel);
					$grandeur = $grandeur + 2;
				}
				else {
					if ($tab_modele_pdf["tel_texte"][$classe_id]!= '') {
						$text_tel = $tab_modele_pdf["tel_texte"][$classe_id];
					}
					$text_tel .= $gepiSchoolTel;
					$grandeur = $pdf->GetStringWidth($text_tel);
				}
				/*
				if ( $tab_modele_pdf["tel_texte"][$classe_id] != '' and $tab_modele_pdf["tel_image"][$classe_id] === '' ) {
					$text_tel = $tab_modele_pdf["tel_texte"][$classe_id].''.$gepiSchoolTel;
					$grandeur = $pdf->GetStringWidth($text_tel);
				}
				*/

				$pdf->Cell($grandeur,5, $text_tel,0,$passealaligne,'');
			}

			$passealaligne = '2';
			// entête fax
			if( $tab_modele_pdf["entente_fax"][$classe_id]==='1' ) {
				$text_fax = '';
				if ( $tab_modele_pdf["fax_image"][$classe_id] != '' ) {
					$a = $pdf->GetX();
					$b = $pdf->GetY();
					$ima = '../images/imabulle/'.$tab_modele_pdf["fax_image"][$classe_id].'.jpg';
					$valeurima=redimensionne_image($ima, 15, 15);
					$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
					$text_fax = '      '.$gepiSchoolFax;
				}
				else {
					if ( $tab_modele_pdf["fax_texte"][$classe_id] != '' ) {
						$text_fax = $tab_modele_pdf["fax_texte"][$classe_id];
					}
					$text_fax .= $gepiSchoolFax;

				}
				/*
				if ( $tab_modele_pdf["fax_texte"][$classe_id] != '' and $tab_modele_pdf["fax_image"][$classe_id] === '' ) {
					$text_fax = $tab_modele_pdf["fax_texte"][$classe_id].''.$gepiSchoolFax;
				}
				*/

				if( $tab_modele_pdf["entente_tel"][$classe_id]==='1' ) {
					$text_fax=" ".$text_fax;
				}
				$pdf->Cell(90,5, $text_fax,0,$passealaligne,'');
			}

			if($tab_modele_pdf["entente_mel"][$classe_id]==='1') {
				$text_mel = '';
				$y_telecom = $y_telecom + 5;
				$pdf->SetXY($x_telecom,$y_telecom);

				//$text_mel = $gepiSchoolEmail;
				if ( $tab_modele_pdf["courrier_image"][$classe_id] != '' ) {
					$a = $pdf->GetX();
					$b = $pdf->GetY();
					$ima = '../images/imabulle/'.$tab_modele_pdf["courrier_image"][$classe_id].'.jpg';
					$valeurima=redimensionne_image($ima, 15, 15);
					$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
					$text_mel = '      '.$gepiSchoolEmail;
				}
				else {
					if ( $tab_modele_pdf["courrier_texte"][$classe_id] != '' ) {
						$text_mel = $tab_modele_pdf["courrier_texte"][$classe_id];
					}
					$text_mel .= $gepiSchoolEmail;
				}
				/*
				if ( $tab_modele_pdf["courrier_texte"][$classe_id] != '' and $tab_modele_pdf["courrier_image"][$classe_id] === '' ) {
					$text_mel = $tab_modele_pdf["courrier_texte"][$classe_id].' '.$gepiSchoolEmail;
				}
				*/
				$pdf->Cell(90,5, $text_mel,0,2,'');
			}

			// Lignes supplémentaires à prendre en compte...
			if(($tab_modele_pdf["entete_info_etab_suppl"][$classe_id]=='y')&&(($tab_modele_pdf["entete_info_etab_suppl_texte"][$classe_id]!='')||($tab_modele_pdf["entete_info_etab_suppl_valeur"][$classe_id]!=''))) {

				$y_telecom = $y_telecom + 5;
				$pdf->SetXY($x_telecom,$y_telecom);

				$texte = $tab_modele_pdf["entete_info_etab_suppl_texte"][$classe_id]." : ".$tab_modele_pdf["entete_info_etab_suppl_valeur"][$classe_id];

				$pdf->Cell(90,5, $texte,0,2,'');
			}

			// ============= FIN BLOC ETABLISSEMENT ==========================

			//=========================================

			// A VOIR: REMPLACER LE $i PAR AUTRE CHOSE POUR EVITER LA COLLISION AVEC L'INDICE $i passé à la fonction
			//$i = $nb_eleve_aff;

			//$id_periode = $periode_classe[$id_classe_selection][$cpt_info_periode];
			$id_periode = $tab_bull['num_periode'];

			// AJOUT ERIC
			//$classe_id=$id_classe_selection;

			$pdf->SetFont('DejaVu','B',12);

			// gestion des styles
			$pdf->SetStyle2("b","DejaVu","B",8,"0,0,0");
			$pdf->SetStyle2("i","DejaVu","I",8,"0,0,0");
			$pdf->SetStyle2("u","DejaVu","U",8,"0,0,0");

			// style pour la case appréciation générale
			// identité du professeur principal
			if ( $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id] != '' and $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id] < '15' ) {
				$taille = $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id];
			} else {
				$taille = '10';
			}
			$pdf->SetStyle2("bppc","DejaVu","B",$taille,"0,0,0");
			$pdf->SetStyle2("ippc","DejaVu","I",$taille,"0,0,0");

			// ============= DEBUT BLOC ADRESSE PARENTS ==========================

			// bloc affichage de l'adresse des parents
			if($tab_modele_pdf["active_bloc_adresse_parent"][$classe_id]==='1') {
				$pdf->SetXY($tab_modele_pdf["X_parent"][$classe_id],$tab_modele_pdf["Y_parent"][$classe_id]);
				// définition des Largeur - hauteur
				if ( $tab_modele_pdf["largeur_bloc_adresse"][$classe_id] != '' and $tab_modele_pdf["largeur_bloc_adresse"][$classe_id] != '0' ) {
					$longeur_cadre_adresse = $tab_modele_pdf["largeur_bloc_adresse"][$classe_id];
				} else {
					$longeur_cadre_adresse = '90';
				}
				if ( $tab_modele_pdf["hauteur_bloc_adresse"][$classe_id] != '' and $tab_modele_pdf["hauteur_bloc_adresse"][$classe_id] != '0' ) {
					$hauteur_cadre_adresse = $tab_modele_pdf["hauteur_bloc_adresse"][$classe_id];
				} else {
					$hauteur_cadre_adresse = '1';
				}

				//=========================
				// Pour le moment, on fait une croix sur cell_ajustee() si la hauteur du cadre n'est pas saisie
				if(($hauteur_cadre_adresse==1)||($use_cell_ajustee=="n")) {
					$texte_1_responsable = trim($tab_adr_ligne1[$num_resp_bull]);
					//$hauteur_caractere=12;
					//$hauteur_caractere=$tab_modele_pdf["adresse_resp_fontsize_ligne_1"][$classe_id];
					$hauteur_caractere=$tab_modele_pdf["adresse_resp_fontsize"][$classe_id];
					$pdf->SetFont('DejaVu','B',$hauteur_caractere);
					$val = $pdf->GetStringWidth($texte_1_responsable);
					$taille_texte = $longeur_cadre_adresse;
					$grandeur_texte='test';
					while($grandeur_texte != 'ok') {
						if($taille_texte < $val){
							$hauteur_caractere = $hauteur_caractere-0.3;
							$pdf->SetFont('DejaVu','B',$hauteur_caractere);
							$val = $pdf->GetStringWidth($texte_1_responsable);
						} else {
							$grandeur_texte = 'ok';
						}
					}
					$pdf->Cell(90,7, ($texte_1_responsable),0,2,'');
			
					// ERIC
					if ($tab_modele_pdf["affiche_numero_responsable"][$classe_id] == '1') {
						//Ajout Eric le 13-11-2010 Num du Resp légal sur le bulletin
						$pdf->SetXY($tab_modele_pdf["X_parent"][$classe_id]+90-8,$tab_modele_pdf["Y_parent"][$classe_id]-3);
						$pdf->SetFont('DejaVu','',6); //6==> hauteur de caractère
						$num=$num_resp_bull+1; // on se base sur le nombre de bulletin à imprimer
						$num_legal= "(Resp ".$num.")";
						$pdf->Cell(90,7,$num_legal,0,2,'');
						// On remet le curseur à la bonne position pour la suite de l'adresse
						$pdf->SetXY($tab_modele_pdf["X_parent"][$classe_id],$tab_modele_pdf["Y_parent"][$classe_id]+7);
						// Fin modif Eric
				  }   


					$texte_1_responsable = $tab_adr_ligne2[$num_resp_bull];
					if($texte_1_responsable!="") {
						//$hauteur_caractere=10;
						$hauteur_caractere=$tab_modele_pdf["adresse_resp_fontsize"][$classe_id];
						$pdf->SetFont('DejaVu','',$hauteur_caractere);
						$val = $pdf->GetStringWidth($texte_1_responsable);
						$taille_texte = $longeur_cadre_adresse;
						$grandeur_texte='test';
						while($grandeur_texte!='ok') {
							if($taille_texte<$val){
								$hauteur_caractere = $hauteur_caractere-0.3;
								$pdf->SetFont('DejaVu','',$hauteur_caractere);
								$val = $pdf->GetStringWidth($texte_1_responsable);
							} else {
								$grandeur_texte='ok';
							}
						}
						$pdf->Cell(90,5, ($texte_1_responsable),0,2,'');
					}

					$texte_1_responsable = $tab_adr_ligne3[$num_resp_bull];
					if($texte_1_responsable!="") {
						//$hauteur_caractere=10;
						$hauteur_caractere=$tab_modele_pdf["adresse_resp_fontsize"][$classe_id];
						$pdf->SetFont('DejaVu','',$hauteur_caractere);
						$val = $pdf->GetStringWidth($texte_1_responsable);
						$taille_texte = $longeur_cadre_adresse;
						$grandeur_texte='test';
						while($grandeur_texte!='ok') {
							if($taille_texte<$val){
								$hauteur_caractere = $hauteur_caractere-0.3;
								$pdf->SetFont('DejaVu','',$hauteur_caractere);
								$val = $pdf->GetStringWidth($texte_1_responsable);
							} else {
								$grandeur_texte='ok';
							}
						}
						$pdf->Cell(90,5, ($texte_1_responsable),0,2,'');
					}

					// Suppression du saut de ligne pour mettre la ligne 3 de l'adresse
					//$pdf->Cell(90,5, '',0,2,'');

					$texte_1_responsable = $tab_adr_ligne4[$num_resp_bull];
					if($texte_1_responsable!="") {
						//$hauteur_caractere=10;
						$hauteur_caractere=$tab_modele_pdf["adresse_resp_fontsize"][$classe_id];
						$pdf->SetFont('DejaVu','',$hauteur_caractere);
						$val = $pdf->GetStringWidth($texte_1_responsable);
						$taille_texte = $longeur_cadre_adresse;
						$grandeur_texte='test';
						while($grandeur_texte!='ok') {
							if($taille_texte<$val){
								$hauteur_caractere = $hauteur_caractere-0.3;
								$pdf->SetFont('DejaVu','',$hauteur_caractere);
								$val = $pdf->GetStringWidth($texte_1_responsable);
							} else {
								$grandeur_texte='ok';
							}
						}
						$pdf->Cell(90,5, ($texte_1_responsable),0,2,'');
					}

					$texte_1_responsable = $tab_adr_ligne5[$num_resp_bull];
					if($texte_1_responsable!="") {
						//$hauteur_caractere=10;
						$hauteur_caractere=$tab_modele_pdf["adresse_resp_fontsize"][$classe_id];
						$pdf->SetFont('DejaVu','',$hauteur_caractere);
						$val = $pdf->GetStringWidth($texte_1_responsable);
						$taille_texte = $longeur_cadre_adresse;
						$grandeur_texte='test';
						while($grandeur_texte!='ok') {
							if($taille_texte<$val){
								$hauteur_caractere = $hauteur_caractere-0.3;
								$pdf->SetFont('DejaVu','',$hauteur_caractere);
								$val = $pdf->GetStringWidth($texte_1_responsable);
							} else {
								$grandeur_texte='ok';
							}
						}
						$pdf->Cell(90,5, ($texte_1_responsable),0,2,'');
					}

					//$texte_1_responsable = $cp_parents[$ident_eleve_aff][$responsable_place]." ".$ville_parents[$ident_eleve_aff][$responsable_place];
					$texte_1_responsable = $tab_adr_ligne6[$num_resp_bull];
					if($texte_1_responsable!="") {
						//$hauteur_caractere=10;
						$hauteur_caractere=$tab_modele_pdf["adresse_resp_fontsize"][$classe_id];
						$pdf->SetFont('DejaVu','',$hauteur_caractere);
						$val = $pdf->GetStringWidth($texte_1_responsable);
						$taille_texte = $longeur_cadre_adresse;
						$grandeur_texte='test';
						while($grandeur_texte!='ok') {
							if($taille_texte<$val){
								$hauteur_caractere = $hauteur_caractere-0.3;
								$pdf->SetFont('DejaVu','',$hauteur_caractere);
								$val = $pdf->GetStringWidth($texte_1_responsable);
							} else {
								$grandeur_texte='ok';
							}
						}
						$pdf->Cell(90,5, ($texte_1_responsable),0,2,'');
					}

					//============================
					//if((my_strtolower($gepiSchoolPays)!=my_strtolower($pays_parents[$ident_eleve_aff][$responsable_place]))&&($pays_parents[$ident_eleve_aff][$responsable_place]!="")) {
					if(isset($tab_adr_ligne7[$num_resp_bull])) {
						$texte_1_responsable = $tab_adr_ligne7[$num_resp_bull];
						if($texte_1_responsable!="") {
							//$hauteur_caractere=10;
							$hauteur_caractere=$tab_modele_pdf["adresse_resp_fontsize"][$classe_id];
							$pdf->SetFont('DejaVu','',$hauteur_caractere);
							$val = $pdf->GetStringWidth($texte_1_responsable);
							$taille_texte = $longeur_cadre_adresse;
							$grandeur_texte='test';
							while($grandeur_texte!='ok') {
								if($taille_texte<$val){
									$hauteur_caractere = $hauteur_caractere-0.3;
									$pdf->SetFont('DejaVu','',$hauteur_caractere);
									$val = $pdf->GetStringWidth($texte_1_responsable);
								} else {
									$grandeur_texte='ok';
								}
							}
							$pdf->Cell(90,5, ($texte_1_responsable),0,2,'');
						}
					}
					//============================

					$texte_1_responsable = '';
					if ( $tab_modele_pdf["cadre_adresse"][$classe_id] != 0 ) {
						if($hauteur_cadre_adresse=='1') {
							// Patch tout pourri... pour faire un encadrement à peu près correct, même si la hauteur du cadre adresse parent n'a pas été saisie dans le modèle
							$h_tmp=$pdf->GetY()-$tab_modele_pdf["Y_parent"][$classe_id]-2;

							$pdf->Rect($tab_modele_pdf["X_parent"][$classe_id], $tab_modele_pdf["Y_parent"][$classe_id], $longeur_cadre_adresse, $h_tmp, 'D');
						}
						else {
							$pdf->Rect($tab_modele_pdf["X_parent"][$classe_id], $tab_modele_pdf["Y_parent"][$classe_id], $longeur_cadre_adresse, $hauteur_cadre_adresse, 'D');
						}

						// Remarque: L'encadrement réalisé ne tient pas compte du texte saisi.
						//           Si on met des valeurs trop réduites, ça ne diminue pas la taille du texte de l'adresse
						//           On ne fait ici que mettre une déco qui ne va pas nécessairement coïncider
						//           A REVOIR...
					}
				}
				else {

					$texte=$tab_adr_lignes[$num_resp_bull];
					//$taille_max_police=10;
					$taille_max_police=$tab_modele_pdf["adresse_resp_fontsize"][$classe_id];
					$taille_min_police=ceil($taille_max_police/3);

					$largeur_dispo=$longeur_cadre_adresse;
					$h_cell=$hauteur_cadre_adresse;

					if ( $tab_modele_pdf["cadre_adresse"][$classe_id] != 0 ) {
						$bordure_cadre_adresse_resp='LRBT';
					}
					else {
						$bordure_cadre_adresse_resp='';
					}

					cell_ajustee($texte,$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,$bordure_cadre_adresse_resp,'C','L',0.3,1);
				}
			}

			// ============= FIN BLOC ADRESSE PARENTS ==========================

			//=========================================

			// ============= DEBUT BLOC ELEVE ==========================

			// Bloc affichage information sur l'élève
			if($tab_modele_pdf["active_bloc_eleve"][$classe_id]==='1') {
				$pdf->SetXY($tab_modele_pdf["X_eleve"][$classe_id],$tab_modele_pdf["Y_eleve"][$classe_id]);
				// définition des Lageur - hauteur
				if ( $tab_modele_pdf["largeur_bloc_eleve"][$classe_id] != '' and $tab_modele_pdf["largeur_bloc_eleve"][$classe_id] != '0' ) {
					$longeur_cadre_eleve = $tab_modele_pdf["largeur_bloc_eleve"][$classe_id];
				} else {
					$longeur_cadre_eleve = $pdf->GetStringWidth($tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom']);
					$rajout_cadre_eleve = 100-$longeur_cadre_eleve;
					$longeur_cadre_eleve = $longeur_cadre_eleve + $rajout_cadre_eleve;
				}
				if ( $tab_modele_pdf["hauteur_bloc_eleve"][$classe_id] != '' and $tab_modele_pdf["hauteur_bloc_eleve"][$classe_id] != '0' ) {
					$hauteur_cadre_eleve = $tab_modele_pdf["hauteur_bloc_eleve"][$classe_id];
				} else {
					$nb_ligne = 5;
					$hauteur_ligne = 6;
					$hauteur_cadre_eleve = $nb_ligne*$hauteur_ligne;
				}

				$pdf->SetFont('DejaVu','B',14);

				if($tab_modele_pdf["cadre_eleve"][$classe_id]!=0) {
					$pdf->Rect($tab_modele_pdf["X_eleve"][$classe_id], $tab_modele_pdf["Y_eleve"][$classe_id], $longeur_cadre_eleve, $hauteur_cadre_eleve, 'D');
				}

				$X_eleve_2 = $tab_modele_pdf["X_eleve"][$classe_id]; $Y_eleve_2=$tab_modele_pdf["Y_eleve"][$classe_id];

				//photo de l'élève
				if ( !isset($tab_modele_pdf["ajout_cadre_blanc_photo"][$classe_id]) or empty($tab_modele_pdf["ajout_cadre_blanc_photo"][$classe_id]) ) {
					$tab_modele_pdf["ajout_cadre_blanc_photo"][$classe_id] = '0';
				}
				if ( $tab_modele_pdf["ajout_cadre_blanc_photo"][$classe_id] === '1' ) {
					$ajouter = '1';
				} else {
					$ajouter = '0';
				}

				$photo[$i]=nom_photo($tab_bull['eleve'][$i]['elenoet']);

				if($tab_modele_pdf["active_photo"][$classe_id]==='1' and $photo[$i]!='' and file_exists($photo[$i])) {
					$L_photo_max = ($hauteur_cadre_eleve - ( $ajouter * 2 )) * 2.8;
					$H_photo_max = ($hauteur_cadre_eleve - ( $ajouter * 2 )) * 2.8;
					$valeur=redimensionne_image($photo[$i], $L_photo_max, $H_photo_max);
					$X_photo = $tab_modele_pdf["X_eleve"][$classe_id]+ 0.20 + $ajouter;
					$Y_photo = $tab_modele_pdf["Y_eleve"][$classe_id]+ 0.25 + $ajouter;
					$L_photo = $valeur[0];
					$H_photo = $valeur[1];
					$X_eleve_2 = $tab_modele_pdf["X_eleve"][$classe_id] + $L_photo + $ajouter + 1;
					$Y_eleve_2 = $Y_photo;

					// Seules les images JPEG ont l'air acceptées... et on ne peut pas se fier à l'extension...
					$tmp_dim_photo=getimagesize($photo[$i]);
					//if((!isset($tmp_dim_photo['mime']))||(preg_match("/jpeg/i",$tmp_dim_photo['mime']))) {
					if((isset($tmp_dim_photo[2]))&&($tmp_dim_photo[2]==2)) {
						$pdf->Image($photo[$i], $X_photo, $Y_photo, $L_photo, $H_photo);
						$longeur_cadre_eleve = $longeur_cadre_eleve - ( $valeur[0] + $ajouter );
					}
				}


				$pdf->SetXY($X_eleve_2,$Y_eleve_2);

				//$pdf->Cell(90,7, ($tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom']),0,2,'');
				$nom_prenom=affiche_eleve($tab_bull['eleve'][$i]['nom'],$tab_bull['eleve'][$i]['prenom'],$tab_bull['id_classe']);

				$hauteur_caractere_nom_prenom=14;
				$pdf->SetFont('DejaVu','B',$hauteur_caractere_nom_prenom);
				$val = $pdf->GetStringWidth($nom_prenom);
				if($tab_modele_pdf["active_photo"][$classe_id]==='1' and $photo[$i]!='' and file_exists($photo[$i])) {
					$taille_texte = 90-$L_photo; // Espace max
				}
				else {
					$taille_texte = 90; // Espace max
				}
				if($taille_texte<10) {$taille_texte=90;} // Sécurité pour ne pas risquer une boucle infinie
				$grandeur_texte='test';
				while($grandeur_texte!='ok') {
					if($taille_texte<$val) {
						$hauteur_caractere_nom_prenom=$hauteur_caractere_nom_prenom-0.3;
						$pdf->SetFont('DejaVu','B',$hauteur_caractere_nom_prenom);
						$val = $pdf->GetStringWidth($nom_prenom);
					} else {
						$grandeur_texte='ok';
					}
				}
				$grandeur_texte='test';
				$pdf->Cell(90,7, $nom_prenom,0,2,'');


				$pdf->SetFont('DejaVu','',10);
				if($tab_modele_pdf["affiche_date_naissance"][$classe_id]==='1') {
					if($tab_bull['eleve'][$i]['naissance']!="") {
						$info_naissance="Né";
						if($tab_bull['eleve'][$i]['sexe']=="F") {$info_naissance.="e";}
						$info_naissance.=" le ".$tab_bull['eleve'][$i]['naissance'];

						if((getSettingValue('ele_lieu_naissance')=='y')&&($tab_modele_pdf["affiche_lieu_naissance"][$classe_id]==='1')) {
							$info_naissance.=" à ".$tab_bull['eleve'][$i]['lieu_naissance'];
						}

						$pdf->Cell(90,5, ($info_naissance),0,2,'');
					}
				}


				$rdbt = '';
				if($tab_modele_pdf["affiche_dp"][$classe_id]==='1') {
					if($tab_modele_pdf["affiche_doublement"][$classe_id]==='1') {
						//if($tab_bull['eleve'][$i]['doublant']!="") {
						if($tab_bull['eleve'][$i]['doublant']=="R") {
							//$rdbt=" ; ".$doublement[$i];
							//$rdbt=" ; redoublant";
							$rdbt="redoublant";
							if($tab_bull['eleve'][$i]['sexe']=="F") {
								$rdbt.="e";
							}
						}
						//if(isset($tab_bull['eleve'][$i]['regime'])) {
						if((isset($tab_bull['eleve'][$i]['regime']))&&($tab_bull['eleve'][$i]['regime']!="")) {
							if($rdbt=="") {
								$pdf->Cell(90,4, (regime($tab_bull['eleve'][$i]['regime'])),0,2,'');
							}
							else {
								$pdf->Cell(90,4, (regime($tab_bull['eleve'][$i]['regime'])."; ".$rdbt),0,2,'');
							}
						} else {
							$pdf->Cell(90,4,($rdbt),0,2,'');
						}
					}
				} else {
					if($tab_modele_pdf["affiche_doublement"][$classe_id]==='1') {
						//if($tab_bull['eleve'][$i]['doublant']!="") {
						if($tab_bull['eleve'][$i]['doublant']=="R") {
							//$pdf->Cell(90,4.5, $doublement[$i],0,2,'');
							//$rdbt=" ; redoublant";
							$rdbt="redoublant";
							if($tab_bull['eleve'][$i]['sexe']=="F") {
								$rdbt.="e";
							}
							$pdf->Cell(90,4.5, ($rdbt),0,2,'');
						}
					}
				}

				// affiche le nom court de la classe
				if ( $tab_modele_pdf["affiche_nom_court"][$classe_id] === '1' )
				{
					if($tab_bull['eleve'][$i]['classe']!="")
					{
						// si l'affichage du numéro INE est activé alors on ne passe pas
						$passe_a_la_ligne = 0;
						//if ( $tab_modele_pdf["affiche_ine"][$classe_id] != '1' or $tab_modele_pdf["INE_eleve"][$i] == '' )
						if ( $tab_modele_pdf["affiche_ine"][$classe_id] != '1' or $tab_bull['eleve'][$i]['no_gep'] == '' )
						{
							$passe_a_la_ligne = 1;
						}
						$pdf->Cell(45,4.5, (unhtmlentities($tab_bull['eleve'][$i]['classe'])),0, $passe_a_la_ligne,'');
					}
				}

				// affiche l'INE de l'élève
				if ( $tab_modele_pdf["affiche_ine"][$classe_id] === '1' )
				{
					if ( $tab_bull['eleve'][$i]['no_gep'] != '' )
					{
						$pdf->Cell(45,4.5, 'INE: '.$tab_bull['eleve'][$i]['no_gep'], 0, 1,'');
					}
				}

				// Affichage du numéro d'impression
				$pdf->SetX($X_eleve_2);

				if($tab_modele_pdf["affiche_effectif_classe"][$classe_id]==='1') {
					if($tab_modele_pdf["affiche_numero_impression"][$classe_id]==='1') {
						$pass_ligne = '0';
					} else {
						$pass_ligne = '2';
					}
					if($tab_bull['eff_classe']!="") {
						$pdf->Cell(45,4.5, ('Effectif : '.$tab_bull['eff_classe'].' élèves'),0,$pass_ligne,'');
					}
				}
				if($tab_modele_pdf["affiche_numero_impression"][$classe_id]==='1') {
					//+++++++++++++++++++
					//+++++++++++++++++++
					// A VOIR... CE $i...
					// Si on n'imprime que certains bulletins, on récupère le numéro d'ordre (alphabétique) de l'élève dans la classe.
					//+++++++++++++++++++
					//+++++++++++++++++++
					//$num_ordre = $i;
					$num_ordre = $i+1;
					$pdf->Cell(45,4, 'Bulletin N° '.$num_ordre,0,2,'');
				}

				// Affichage de l'établissement d'origine
				// On n'affiche pas l'établissement d'origine si c'est le même que l'établissement actuel: $RneEtablissement
				//if($tab_modele_pdf["affiche_etab_origine"][$classe_id]==='1' and !empty($etablissement_origine[$i]) ) {
				//if($tab_modele_pdf["affiche_etab_origine"][$classe_id]==='1' and isset($tab_bull['eleve'][$i]['etab_id']) and !empty($tab_bull['eleve'][$i]['etab_id']) ) {
				if(($tab_modele_pdf["affiche_etab_origine"][$classe_id]==='1')&&(isset($tab_bull['eleve'][$i]['etab_id']))&&(!empty($tab_bull['eleve'][$i]['etab_id']))&&(my_strtolower($tab_bull['eleve'][$i]['etab_id'])!=my_strtolower($RneEtablissement))) {
					$pdf->SetX($X_eleve_2);
					$hauteur_caractere_etaborigine = '10';
					$pdf->SetFont('DejaVu','',$hauteur_caractere_etaborigine);

					$chaine_etab_origine='Etab. Origine : '.$tab_bull['eleve'][$i]['etab_niveau_nom']." ".$tab_bull['eleve'][$i]['etab_nom']." (".$tab_bull['eleve'][$i]['etab_ville'].")";
					//$chaine_etab_origine='Etab. Origine : '.$tab_bull['eleve'][$i]['etab_niveau_nom']." ".$tab_bull['eleve'][$i]['etab_type'].." ".$tab_bull['eleve'][$i]['etab_nom']." (".$tab_bull['eleve'][$i]['etab_ville'].")";
					$val = $pdf->GetStringWidth($chaine_etab_origine);

					$taille_texte = $longeur_cadre_eleve-3;
					$grandeur_texte='test';
					while($grandeur_texte!='ok') {
						if($taille_texte<$val) {
							$hauteur_caractere_etaborigine = $hauteur_caractere_etaborigine-0.3;
							$pdf->SetFont('DejaVu','',$hauteur_caractere_etaborigine);
							$val = $pdf->GetStringWidth($chaine_etab_origine);
						} else {
							$grandeur_texte='ok';
						}
					}
					$grandeur_texte='test';
					$pdf->Cell(90,4, $chaine_etab_origine,0,2);
					$pdf->SetFont('DejaVu','',10);
				}
			} // fin du bloc affichage information sur l'élèves

			// ============= FIN BLOC ELEVE ==========================

			//=========================================

			// ============= DEBUT BLOC CLASSE/PERIODE/DATATION ==========================

			// Bloc affichage datation du bulletin:
			// Classe, période,...
			if($tab_modele_pdf["active_bloc_datation"][$classe_id]==='1') {
				$pdf->SetXY($tab_modele_pdf["X_datation_bul"][$classe_id], $tab_modele_pdf["Y_datation_bul"][$classe_id]);

				// définition des Largeur - hauteur
				if ( $tab_modele_pdf["largeur_bloc_datation"][$classe_id] != '' and $tab_modele_pdf["largeur_bloc_datation"][$classe_id] != '0' ) {
					$longeur_cadre_datation_bul = $tab_modele_pdf["largeur_bloc_datation"][$classe_id];
				} else {
					$longeur_cadre_datation_bul = '95';
				}
				if ( $tab_modele_pdf["hauteur_bloc_datation"][$classe_id] != '' and $tab_modele_pdf["hauteur_bloc_datation"][$classe_id] != '0' ) {
					$hauteur_cadre_datation_bul = $tab_modele_pdf["hauteur_bloc_datation"][$classe_id];
				} else {
					$nb_ligne_datation_bul = 3;
					$hauteur_ligne_datation_bul = 6;
					$hauteur_cadre_datation_bul = $nb_ligne_datation_bul*$hauteur_ligne_datation_bul;
				}

				if($tab_modele_pdf["cadre_datation_bul"][$classe_id]!=0) {
					$pdf->Rect($tab_modele_pdf["X_datation_bul"][$classe_id], $tab_modele_pdf["Y_datation_bul"][$classe_id], $longeur_cadre_datation_bul, $hauteur_cadre_datation_bul, 'D');
				}
				$taille_texte = '14'; $type_texte = 'B';
				if ( $tab_modele_pdf["taille_texte_classe"][$classe_id] != '' and $tab_modele_pdf["taille_texte_classe"][$classe_id] != '0' ) {
					$taille_texte = $tab_modele_pdf["taille_texte_classe"][$classe_id];
				} else {
					$taille_texte = '14';
				}
				if ( $tab_modele_pdf["type_texte_classe"][$classe_id] != '' ) {
					if ( $tab_modele_pdf["type_texte_classe"][$classe_id] === 'N' ) {
						$type_texte = '';
					} else {
						$type_texte = $tab_modele_pdf["type_texte_classe"][$classe_id];
					}
				} else {
					$type_texte = 'B';
				}
				$pdf->SetFont('DejaVu', $type_texte, $taille_texte);
				$pdf->Cell(90,7, ("Classe de ".unhtmlentities($tab_bull['classe_nom_complet'])),0,2,'C');
				$taille_texte = '12'; $type_texte = '';
				if ( $tab_modele_pdf["taille_texte_annee"][$classe_id] != '' and $tab_modele_pdf["taille_texte_annee"][$classe_id] != '0') {
					$taille_texte = $tab_modele_pdf["taille_texte_annee"][$classe_id];
				} else {
					$taille_texte = '12';
				}

				if ( $tab_modele_pdf["type_texte_annee"][$classe_id] != '' ) {
					if ( $tab_modele_pdf["type_texte_annee"][$classe_id] === 'N' ) {
						$type_texte = '';
					}
					else {
						$type_texte = $tab_modele_pdf["type_texte_annee"][$classe_id];
					}
				}
				else {
					$type_texte = '';
				}
				$pdf->SetFont('DejaVu', $type_texte, $taille_texte);
				$annee_scolaire = $gepiYear;
				$pdf->Cell(90,5, ("Année scolaire ".$annee_scolaire),0,2,'C');
				$taille_texte = '10'; $type_texte = '';
				if ( $tab_modele_pdf["taille_texte_periode"][$classe_id] != '' and $tab_modele_pdf["taille_texte_periode"][$classe_id] != '0' ) {
					$taille_texte = $tab_modele_pdf["taille_texte_periode"][$classe_id];
				}
				else {
					$taille_texte = '10';
				}
				if ( $tab_modele_pdf["type_texte_periode"][$classe_id] != '' ) {
					if ( $tab_modele_pdf["type_texte_periode"][$classe_id] === 'N' ) {
						$type_texte = '';
					}
					else {
						$type_texte = $tab_modele_pdf["type_texte_periode"][$classe_id];
					}
				}
				else {
					$type_texte = '';
				}
				$pdf->SetFont('DejaVu', $type_texte, $taille_texte);
				$pdf->Cell(90,5, ($tab_bull['bull_prefixe_periode'].unhtmlentities($tab_bull['nom_periode'])),0,2,'C');
				$taille_texte = '8';
				$type_texte = '';

				if ( $tab_modele_pdf["affiche_date_edition"][$classe_id] === '1' ) {
					if ( $tab_modele_pdf["taille_texte_date_edition"][$classe_id] != '' and $tab_modele_pdf["taille_texte_date_edition"][$classe_id] != '0' ) {
						$taille_texte = $tab_modele_pdf["taille_texte_date_edition"][$classe_id];
					}
					else {
						$taille_texte = '8';
					}
					if ( $tab_modele_pdf["type_texte_date_datation"][$classe_id] != '' ) {
						if ( $tab_modele_pdf["type_texte_date_datation"][$classe_id] === 'N' ) {
							$type_texte = '';
						}
						else {
							$type_texte = $tab_modele_pdf["type_texte_date_datation"][$classe_id];
						}
					}
					else {
						$type_texte = '';
					}
					$pdf->SetFont('DejaVu', $type_texte, $taille_texte);
					$pdf->Cell(95,7, ($date_bulletin),0,2,'R');
				}

				$pdf->SetFont('DejaVu','',10);
			}

			// ============= FIN BLOC CLASSE/PERIODE/DATATION ==========================

			//=========================================

			// ============= DEBUT BLOC NOTES ET APPRECIATIONS ==========================

			// Bloc notes et appréciations

			// 20130927
			if((!isset($tab_modele_pdf["cell_ajustee_texte_matiere_ratio_min_max"][$classe_id]))||(!is_numeric($tab_modele_pdf["cell_ajustee_texte_matiere_ratio_min_max"][$classe_id]))||($tab_modele_pdf["cell_ajustee_texte_matiere_ratio_min_max"][$classe_id]<=0)) {
				$tab_modele_pdf["cell_ajustee_texte_matiere_ratio_min_max"][$classe_id]=3;
			}

			//=================================================================
			// 20151129: REMONTé pour ne pas faire le calcul du nombre de matière autant de fois qu'il y a de parent séparé
			/*
			//nombre de matieres à afficher
			//$nb_matiere = $info_bulletin[$ident_eleve_aff][$id_periode]['nb_matiere'];
			$nb_matiere=0;
			//$fich=fopen("/tmp/infos_matieres_eleve.txt","a+");
			//fwrite($fich,"\$tab_bull['eleve'][$i]['nom']=".$tab_bull['eleve'][$i]['nom']."\n");
			for($j=0;$j<count($tab_bull['groupe']);$j++) {
				if(isset($tab_bull['note'][$j][$i])) {
					// Si l'élève suit l'option, sa note est affectée (éventuellement vide)
					//fwrite($fich,"\$tab_bull['groupe'][$j]['matiere']['matiere']=".$tab_bull['groupe'][$j]['matiere']['matiere']." ");
					//fwrite($fich,"\$tab_bull['note'][$j][$i]=".$tab_bull['note'][$j][$i]."\n");
					$nb_matiere++;


					// Si les catégories doivent être affichées, il faut réordonner les matières pour ne pas avoir:
					//     LIT
					//     SCI
					//     AUT
					//     LIT à nouveau
					// Sinon, les calculs de hauteur sont faussés et ça ne permet pas d'avoir des ordres très différents avec et sans catégories
					// Il vaudrait mieux refaire ce tri au départ, avant d'être dans les boucles élèves


				}
			}

			if(isset($tab_bull['eleve'][$i]['aid_b'])) {
				$nb_matiere+=count($tab_bull['eleve'][$i]['aid_b']);
			}

			if(isset($tab_bull['eleve'][$i]['aid_e'])) {
				$nb_matiere+=count($tab_bull['eleve'][$i]['aid_e']);
			}
			//fclose($fich);
			*/

			/*
			//++++++++++++++++
			// Pour debug:
			$pdf->SetXY(100, 25);
			$pdf->SetFont('DejaVu','',10);
			$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], 8, "nb_matiere=$nb_matiere",1,0,'C');
			//++++++++++++++++
			*/

			// 20160408

			if($tab_modele_pdf["active_bloc_note_appreciation"][$classe_id]==='1' and $nb_matiere!='0') {
				$pdf->Rect($tab_modele_pdf["X_note_app"][$classe_id], $tab_modele_pdf["Y_note_app"][$classe_id], $tab_modele_pdf["longeur_note_app"][$classe_id], $tab_modele_pdf["hauteur_note_app"][$classe_id], 'D');
				//entête du tableau des notes et app
				$nb_entete_moyenne = $tab_modele_pdf["active_moyenne_eleve"][$classe_id]+$tab_modele_pdf["active_moyenne_classe"][$classe_id]+$tab_modele_pdf["active_moyenne_min"][$classe_id]+$tab_modele_pdf["active_moyenne_max"][$classe_id]; //min max classe eleve
				$hauteur_entete = 8;
				$hauteur_entete_pardeux = $hauteur_entete/2;
				$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id], $tab_modele_pdf["Y_note_app"][$classe_id]);
				$pdf->SetFont('DejaVu','',10);
				$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $hauteur_entete, ($tab_modele_pdf["titre_entete_matiere"][$classe_id]),1,0,'C');
				$largeur_utilise = $tab_modele_pdf["largeur_matiere"][$classe_id];

				// coefficient matière
				if($tab_modele_pdf["active_coef_moyenne"][$classe_id]==='1') {
					$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]);
					$pdf->SetFont('DejaVu','',8);
					$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $hauteur_entete, ($tab_modele_pdf["titre_entete_coef"][$classe_id]),'LRB',0,'C');
					$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_coef_moyenne"][$classe_id];
				}

				// nombre de notes
				// 20081118
				//if($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1') {
				if(($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1')&&($tab_modele_pdf["active_nombre_note"][$classe_id]!='1')) {
					$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]);
					$pdf->SetFont('DejaVu','',8);
					$pdf->Cell($tab_modele_pdf["largeur_nombre_note"][$classe_id], $hauteur_entete, ($tab_modele_pdf["titre_entete_nbnote"][$classe_id]),'LRB',0,'C');
					$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_nombre_note"][$classe_id];
				}

				// Pour forcer la valeur à tester (lors de l'ajout d'un mode):
				//$tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id]='7';

				// eleve | min | classe | max | rang | niveau | appreciation |
				if ( $tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] === '1' ) {
					$ordre_moyenne[0] = 'eleve';
					$ordre_moyenne[1] = 'min';
					$ordre_moyenne[2] = 'classe';
					$ordre_moyenne[3] = 'max';
					$ordre_moyenne[4] = 'rang';
					$ordre_moyenne[5] = 'niveau';
					$ordre_moyenne[6] = 'appreciation';
				}

				// min | classe | max | eleve | niveau | rang | appreciation |
				if ( $tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] === '2' ) {
					$ordre_moyenne[0] = 'min';
					$ordre_moyenne[1] = 'classe';
					$ordre_moyenne[2] = 'max';
					$ordre_moyenne[3] = 'eleve';
					$ordre_moyenne[4] = 'niveau';
					$ordre_moyenne[5] = 'rang';
					$ordre_moyenne[6] = 'appreciation';
				}

				// eleve | niveau | rang | appreciation | min | classe | max
				if ( $tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] === '3' ) {
					$ordre_moyenne[0] = 'eleve';
					$ordre_moyenne[1] = 'niveau';
					$ordre_moyenne[2] = 'rang';
					$ordre_moyenne[3] = 'appreciation';
					$ordre_moyenne[4] = 'min';
					$ordre_moyenne[5] = 'classe';
					$ordre_moyenne[6] = 'max';
				}

				// eleve | classe | min | max | rang | niveau | appreciation |
				if ( $tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] === '4' ) {
					$ordre_moyenne[0] = 'eleve';
					$ordre_moyenne[1] = 'classe';
					$ordre_moyenne[2] = 'min';
					$ordre_moyenne[3] = 'max';
					$ordre_moyenne[4] = 'rang';
					$ordre_moyenne[5] = 'niveau';
					$ordre_moyenne[6] = 'appreciation';
				}

				// eleve | min | classe | max | niveau | rang | appreciation |
				if ( $tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] === '5' ) {
					$ordre_moyenne[0] = 'eleve';
					$ordre_moyenne[1] = 'min';
					$ordre_moyenne[2] = 'classe';
					$ordre_moyenne[3] = 'max';
					$ordre_moyenne[4] = 'niveau';
					$ordre_moyenne[5] = 'rang';
					$ordre_moyenne[6] = 'appreciation';
				}

				// min | classe | max | eleve | rang | niveau | appreciation |
				//if ( $ordre_entete_model_bulletin[$classe_id] === '6' ) {
				if ( $tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] === '6' ) {
					$ordre_moyenne[0] = 'min';
					$ordre_moyenne[1] = 'classe';
					$ordre_moyenne[2] = 'max';
					$ordre_moyenne[3] = 'eleve';
					$ordre_moyenne[4] = 'rang';
					$ordre_moyenne[5] = 'niveau';
					$ordre_moyenne[6] = 'appreciation';
				}

				// appreciation | eleve | rang | niveau | min | classe | max | 
				//if ( $ordre_entete_model_bulletin[$classe_id] === '6' ) {
				if ( $tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] === '7' ) {
					$ordre_moyenne[0] = 'appreciation';
					$ordre_moyenne[1] = 'eleve';
					$ordre_moyenne[2] = 'niveau';
					$ordre_moyenne[3] = 'rang';
					$ordre_moyenne[4] = 'min';
					$ordre_moyenne[5] = 'classe';
					$ordre_moyenne[6] = 'max';
				}

				$cpt_ordre = 0;
				$chapeau_moyenne = 'non';
				while ( !empty($ordre_moyenne[$cpt_ordre]) ) {

					// Je ne saisis pas pourquoi cette variable est initialisée à ce niveau???
					//$categorie_passe_count = 0;

					// le chapeau des moyennes
					$ajout_espace_au_dessus = 4;
					if ( $tab_modele_pdf["entete_model_bulletin"][$classe_id] === '1' and
					     $nb_entete_moyenne > 1 and
					     ( $ordre_moyenne[$cpt_ordre] === 'classe' or 
						 $ordre_moyenne[$cpt_ordre] === 'min' or 
						 $ordre_moyenne[$cpt_ordre] === 'max' or 
						 $ordre_moyenne[$cpt_ordre] === 'eleve' ) and
					     $chapeau_moyenne === 'non' and 
					     $tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] != '3' and 
					     $tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] != '7' )
					{
						$largeur_moyenne = $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id] * $nb_entete_moyenne;
						$text_entete_moyenne = 'Moyenne';
						$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]);
						$pdf->Cell($largeur_moyenne, $hauteur_entete_pardeux, ($text_entete_moyenne),1,0,'C');
						$chapeau_moyenne = 'oui';
					}

					//if ( ($tab_modele_pdf["entete_model_bulletin"][$classe_id] === '2' and $nb_entete_moyenne > 1 and ( $ordre_moyenne[$cpt_ordre] === 'classe' or $ordre_moyenne[$cpt_ordre] === 'min' or $ordre_moyenne[$cpt_ordre] === 'max' ) and $chapeau_moyenne === 'non' ) or ( $tab_modele_pdf["entete_model_bulletin"][$classe_id] === '1' and $tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] === '3' and $chapeau_moyenne === 'non' and ( $ordre_moyenne[$cpt_ordre] === 'classe' or $ordre_moyenne[$cpt_ordre] === 'min' or $ordre_moyenne[$cpt_ordre] === 'max' )  ) )
					if (($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&
						( ($tab_modele_pdf["entete_model_bulletin"][$classe_id] === '2' and 
						   $nb_entete_moyenne > 1 and 
						   ( $ordre_moyenne[$cpt_ordre] === 'classe' or 
						     $ordre_moyenne[$cpt_ordre] === 'min' or 
						     $ordre_moyenne[$cpt_ordre] === 'max' ) and 
						   $chapeau_moyenne === 'non' ) or 
						( $tab_modele_pdf["entete_model_bulletin"][$classe_id] === '1' and 
						  ($tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] === '3' or $tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] === '7') and 
						  $chapeau_moyenne === 'non' and 
						  ( $ordre_moyenne[$cpt_ordre] === 'classe' or 
						    $ordre_moyenne[$cpt_ordre] === 'min' or 
						    $ordre_moyenne[$cpt_ordre] === 'max' )  ) )
					) {
						$largeur_moyenne = $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id] * ( $nb_entete_moyenne - 1 );
						$text_entete_moyenne = 'Pour la classe';
						$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]);
						$hauteur_caractere=10;
						$pdf->SetFont('DejaVu','',$hauteur_caractere);
						$val = $pdf->GetStringWidth($text_entete_moyenne);
						$taille_texte = $largeur_moyenne;
						$grandeur_texte='test';
						while($grandeur_texte!='ok') {
							if($taille_texte<$val)
							{
								$hauteur_caractere = $hauteur_caractere-0.3;
								$pdf->SetFont('DejaVu','',$hauteur_caractere);
								$val = $pdf->GetStringWidth($text_entete_moyenne);
							}
							else {
								$grandeur_texte='ok';
							}
						}
						$pdf->Cell($largeur_moyenne, $hauteur_entete_pardeux, ($text_entete_moyenne),1,0,'C');
						$chapeau_moyenne = 'oui';
					}

					//eleve
					if($tab_modele_pdf["active_moyenne_eleve"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'eleve' ) {
						$ajout_espace_au_dessus = 4;
						$hauteur_de_la_cellule = $hauteur_entete_pardeux;
						if ( $tab_modele_pdf["entete_model_bulletin"][$classe_id] === '2' and $tab_modele_pdf["active_moyenne_eleve"][$classe_id] === '1' and $nb_entete_moyenne > 1 )
						{
							$hauteur_de_la_cellule = $hauteur_entete;
							$ajout_espace_au_dessus = 0;
						}
						$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]+$ajout_espace_au_dessus);
						$pdf->SetFillColor($tab_modele_pdf["couleur_reperage_eleve1"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve2"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve3"][$classe_id]);
						$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $hauteur_de_la_cellule, ("Elève"),1,0,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
						$pdf->SetFillColor(0, 0, 0);
						$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
					}

					//classe
					//if($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' ) {
					if (($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' )) {
						$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]+4);
						$hauteur_caractere = '8.5';

						$pdf->SetFont('DejaVu','',$hauteur_caractere);
						$text_moy_classe = 'Classe';
						if ( $tab_modele_pdf["entete_model_bulletin"][$classe_id] === '2' ) {
							$text_moy_classe = 'Moy.';
						}
						$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $hauteur_entete_pardeux, ($text_moy_classe),1,0,'C');
						$X_moyenne_classe = $tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise;
						$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
					}
					//min
					//if($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' ) {
					if(($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' )) {

						$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]+4);
						$hauteur_caractere = '8.5';
						$pdf->SetFont('DejaVu','',$hauteur_caractere);
						$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $hauteur_entete_pardeux, "Min.",1,0,'C');
						$X_min_classe = $tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise;
						$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
					}
					//max
					//if($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' ) {
					if(($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' )) {
						$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]+4);
						$hauteur_caractere = '8.5';
						$pdf->SetFont('DejaVu','',$hauteur_caractere);
						$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $hauteur_entete_pardeux, "Max.",1,0,'C');
						$X_max_classe = $tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise;
						$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
					}

					$pdf->SetFont('DejaVu','',8);

					// rang de l'élève
					if( $tab_modele_pdf["active_rang"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'rang' ) {
						$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]);
						$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $hauteur_entete, ($tab_modele_pdf["titre_entete_rang"][$classe_id]),'LRB',0,'C');
						//$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $hauteur_entete, $tab_modele_pdf["titre_entete_rang"][$classe_id],'LRB',0,'C');
						$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_rang"][$classe_id];
					}

					// graphique de niveau
					if( $tab_modele_pdf["active_graphique_niveau"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'niveau' ) {
						$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]);
						$hauteur_caractere = '10';
						$pdf->SetFont('DejaVu','',$hauteur_caractere);
						$pdf->Cell($tab_modele_pdf["largeur_niveau"][$classe_id], $hauteur_entete_pardeux, "Niveau",'LR',0,'C');
						$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]+4);
						$pdf->SetFont('DejaVu','',8);
						$pdf->Cell($tab_modele_pdf["largeur_niveau"][$classe_id], $hauteur_entete_pardeux, "ABC+C-DE",'LRB',0,'C');
						$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_niveau"][$classe_id];
					}
	/*
	$f=fopen("/tmp/largeurs_bull.txt", "a+");
	fwrite($f, "\n");
	fclose($f);
	*/
					//appreciation
					$hauteur_caractere = '10';
					$pdf->SetFont('DejaVu','',$hauteur_caractere);
					if($tab_modele_pdf["active_appreciation"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'appreciation' ) {
						// 20160623
						$largeur_Elements_Programmes_si_affiche=0;
						if((!getSettingAOui('bullNoSaisieElementsProgrammes'))&&($tab_modele_pdf["active_colonne_Elements_Programmes"][$classe_id]==='1')) {
							$largeur_Elements_Programmes_si_affiche=$tab_modele_pdf["largeur_Elements_Programmes"][$classe_id];
						}

						$X_col_app=$tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise;
						$pdf->SetXY($X_col_app, $tab_modele_pdf["Y_note_app"][$classe_id]);
						if ( !empty($ordre_moyenne[$cpt_ordre+1]) ) {
							$cpt_ordre_sous = $cpt_ordre + 1;
							$largeur_appret = 0;
							while ( !empty($ordre_moyenne[$cpt_ordre_sous]) ) {
								if ( $ordre_moyenne[$cpt_ordre_sous] === 'eleve' ) { $largeur_appret = $largeur_appret + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id]; }
								if ( $tab_modele_pdf["active_rang"][$classe_id] === '1' and 
								     $ordre_moyenne[$cpt_ordre_sous] === 'rang' ) { $largeur_appret = $largeur_appret + $tab_modele_pdf["largeur_rang"][$classe_id]; }
								if ( $tab_modele_pdf["active_graphique_niveau"][$classe_id] === '1' and 
								     $ordre_moyenne[$cpt_ordre_sous] === 'niveau' ) { $largeur_appret = $largeur_appret + $tab_modele_pdf["largeur_niveau"][$classe_id]; }
								if ( $tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1 and 
								     $tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and 
								     $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and 
								     $ordre_moyenne[$cpt_ordre_sous] === 'min' ) { $largeur_appret = $largeur_appret + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id]; }
								if ( $tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1 and 
								     $tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1' and 
								     $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and 
								     $ordre_moyenne[$cpt_ordre_sous] === 'classe' ) { $largeur_appret = $largeur_appret + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id]; }
								if ( $tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1 and 
								     $tab_modele_pdf["active_moyenne_max"][$classe_id]==='1' and 
								     $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and 
								     $ordre_moyenne[$cpt_ordre_sous] === 'max' ) { $largeur_appret = $largeur_appret + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id]; }
								$cpt_ordre_sous = $cpt_ordre_sous + 1;
							}
							$largeur_appreciation = $tab_modele_pdf["longeur_note_app"][$classe_id]-$largeur_utilise-$largeur_appret-$largeur_Elements_Programmes_si_affiche;
						} else {
							$largeur_appreciation = $tab_modele_pdf["longeur_note_app"][$classe_id]-$largeur_utilise-$largeur_Elements_Programmes_si_affiche;
						}

						// 20160623
						if((!getSettingAOui('bullNoSaisieElementsProgrammes'))&&($tab_modele_pdf["active_colonne_Elements_Programmes"][$classe_id]==='1')) {
							$pdf->SetFont('DejaVu','',10);

							//$titre_entete_Elements_Programmes=$tab_modele_pdf['titre_entete_Elements_Programmes'][$classe_id];
							$titre_entete_Elements_Programmes="Éléments de programmes";

							//$pdf->Cell($tab_modele_pdf["largeur_Elements_Programmes"][$classe_id], $hauteur_entete, ($titre_entete_Elements_Programmes),'LRB',0,'C');
							$pdf->Cell($tab_modele_pdf["largeur_Elements_Programmes"][$classe_id], $hauteur_entete, ($titre_entete_Elements_Programmes),'LR',0,'C');
							$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_Elements_Programmes"][$classe_id];
						}

						$pdf->SetFont('DejaVu','',10);

						//$titre_entete_appreciation=$bull_intitule_app;
						$titre_entete_appreciation=$tab_modele_pdf['titre_entete_appreciation'][$classe_id];

						$pdf->Cell($largeur_appreciation, $hauteur_entete, ($titre_entete_appreciation),'LRB',0,'C');
						$largeur_utilise = $largeur_utilise + $largeur_appreciation;
					}
					$cpt_ordre = $cpt_ordre + 1;
				}
				$largeur_utilise = 0;
				// fin de boucle d'ordre

				//===============================================

				//emplacement des blocs matière et note et appréciation

				//===============================================
				// 20151129
				//si catégorie activé il faut compter le nombre de catégories
				$nb_categories_select=0;
				//$categorie_passe_for='';

				if($tab_modele_pdf["active_regroupement_cote"][$classe_id]==='1' or $tab_modele_pdf["active_entete_regroupement"][$classe_id]==='1') {
					/* A CONVERTIR AVEC $tab_bull
					for($x=0;$x<$nb_matiere;$x++) {
						if($matiere[$ident_eleve_aff][$id_periode][$x]['categorie']!=$categorie_passe_for) {
							$nb_categories_select=$nb_categories_select+1;
						}
						$categorie_passe_for=$matiere[$ident_eleve_aff][$id_periode][$x]['categorie'];
					}
					*/
					//$nb_categories_select=count($tab_bull['cat_id']);
					$nb_categories_select=count(array_count_values($tab_bull['cat_id']));
				}

				fich_debug_bull("======================\n");
				fich_debug_bull("Elève ".$tab_bull['eleve'][$i]['login']."\n");
				fich_debug_bull("\$nb_categories_select=$nb_categories_select\n");
				//===============================================

				//$nb_matiere=count($tab_bull['eleve'][$i][]);
				/*
				$nb_matiere=0;
				for($j=0;$j<count($tab_bull['groupe']);$j++) {
					if(isset($tab_bull['note'][$j][$i])) {
						// Si l'élève suit l'option, sa note est affectée (éventuellement vide)
						$nb_matiere++;
					}
				}
				*/

				$X_bloc_matiere=$tab_modele_pdf["X_note_app"][$classe_id]; $Y_bloc_matiere=$tab_modele_pdf["Y_note_app"][$classe_id]+$hauteur_entete;
				$longeur_bloc_matiere=$tab_modele_pdf["longeur_note_app"][$classe_id];
				// calcul de la hauteur totale que peut prendre le cadre matière dans sa globalité
				if ( $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne_general"][$classe_id] === '1' )
				{
					// si les moyennes et la moyenne générale sont activées alors on les ajoute à ce qu'il faudra soustraire au cadre global matiere
					$hauteur_toute_entete = $hauteur_entete + $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id];

					if($affiche_deux_moy_gen==1) {
						$hauteur_toute_entete+=$tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id];
					}
				}
				else {
					$hauteur_toute_entete = $hauteur_entete;
				}

				$hauteur_bloc_matiere=$tab_modele_pdf["hauteur_note_app"][$classe_id]-$hauteur_toute_entete;
				$X_note_moy_app = $tab_modele_pdf["X_note_app"][$classe_id];
				$Y_note_moy_app = $tab_modele_pdf["Y_note_app"][$classe_id]+$tab_modele_pdf["hauteur_note_app"][$classe_id]-$hauteur_entete;

				if($tab_modele_pdf["active_entete_regroupement"][$classe_id]==='1') {
					// 20151129
					//$espace_entre_matier = ($hauteur_bloc_matiere-($nb_categories_select*5))/$nb_matiere;
					$espace_entre_matier = ($hauteur_bloc_matiere-($nb_categories_eleve_courant*5))/$nb_matiere;
				}
				else {
					$espace_entre_matier = $hauteur_bloc_matiere/$nb_matiere;
				}

				fich_debug_bull("\$hauteur_bloc_matiere=$hauteur_bloc_matiere\n");
				fich_debug_bull("\$nb_matiere=$nb_matiere\n");
				fich_debug_bull("\$espace_entre_matier=$espace_entre_matier\n");

				/*
				//++++++++++++++++
				// Pour debug:
				$pdf->SetXY(100, 30);
				$pdf->SetFont('DejaVu','',10);
				$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], 8, "espace_entre_matier=$espace_entre_matier",1,0,'C');
				//++++++++++++++++
				*/

				$pdf->SetXY($X_bloc_matiere, $Y_bloc_matiere);
				$Y_decal = $Y_bloc_matiere;

				fich_debug_bull("Avant les AID_B: \$Y_decal=$Y_decal\n");


				//======================================================
				// DEBUT DES AID AFFICHéS AVANT LES MATIERES
				if(isset($tab_bull['eleve'][$i]['aid_b'])) {
					//echo "count(\$tab_bull['eleve'][$i]['aid_b']=".count($tab_bull['eleve'][$i]['aid_b'])."<br />";
					for($m=0;$m<count($tab_bull['eleve'][$i]['aid_b']);$m++) {
						$pdf->SetXY($X_bloc_matiere, $Y_decal);

						// Si c'est une matière suivie par l'élève
						//if(isset($tab_bull['eleve'][$i]['note'][$m][$i])) {

							// calcul la taille du titre de la matière
							$hauteur_caractere_matiere=10;
							if ( $tab_modele_pdf["taille_texte_matiere"][$classe_id] != '' and $tab_modele_pdf["taille_texte_matiere"][$classe_id] != '0' and $tab_modele_pdf["taille_texte_matiere"][$classe_id] < '11' )
							{
								$hauteur_caractere_matiere = $tab_modele_pdf["taille_texte_matiere"][$classe_id];
							}
							$pdf->SetFont('DejaVu','B',$hauteur_caractere_matiere);

							// Pour parer au bug sur la suppression de matière alors que des groupes sont conservés:
							if(isset($tab_bull['eleve'][$i]['aid_b'][$m]['nom_complet'])) {
								$info_nom_matiere=$tab_bull['eleve'][$i]['aid_b'][$m]['nom_complet'];
							}
							else {
								$info_nom_matiere=$tab_bull['eleve'][$i]['aid_b'][$m]['nom'];
							}

							// 20130927 : cell_ajustee() ou pas sur le nom de matière/enseignement
							if((isset($tab_modele_pdf["cell_ajustee_texte_matiere"][$classe_id]))&&($tab_modele_pdf["cell_ajustee_texte_matiere"][$classe_id]==1)) {
								// Encadrement
								$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_entre_matier, "",'LRBT',1,'L');

								// cell_ajustee() ne centre pas verticalement le texte.
								// On met un décalage pour ne pas coller le texte à la bordure
								$Y_decal_cell_ajustee=1;
								// On repositionne et on inscrit le nom de matière sur la moitié de la hauteur de la cellule
								$pdf->SetXY($X_bloc_matiere, $Y_decal+$Y_decal_cell_ajustee);

								$texte=$info_nom_matiere;
								$taille_max_police=$hauteur_caractere_matiere;
								$taille_min_police=ceil($taille_max_police/$tab_modele_pdf["cell_ajustee_texte_matiere_ratio_min_max"][$classe_id]);

								$largeur_dispo=$tab_modele_pdf["largeur_matiere"][$classe_id]-2;
								$h_cell=$espace_entre_matier/2-$Y_decal_cell_ajustee;

								cell_ajustee("<b>".$texte."</b>",$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');

							}
							else {
								$val = $pdf->GetStringWidth($info_nom_matiere);
								$taille_texte = $tab_modele_pdf["largeur_matiere"][$classe_id] - 2;
								$grandeur_texte='test';
								while($grandeur_texte!='ok') {
									if($taille_texte<$val)
									{
										$hauteur_caractere_matiere = $hauteur_caractere_matiere-0.3;
										$pdf->SetFont('DejaVu','B',$hauteur_caractere_matiere);
										$val = $pdf->GetStringWidth($info_nom_matiere);
									}
									else {
										$grandeur_texte='ok';
									}
								}
								$grandeur_texte='test';
								$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_entre_matier/2, ($info_nom_matiere),'LR',1,'L');
							}
							// On note l'ordonnée pour le nom des professeurs
							$Y_decal = $Y_decal+($espace_entre_matier/2);
							$pdf->SetXY($X_bloc_matiere, $Y_decal);
							$pdf->SetFont('DejaVu','',8);

							fich_debug_bull("\$info_nom_matiere=$info_nom_matiere\n");
							fich_debug_bull("\$Y_decal=$Y_decal\n");

							// nom des professeurs

							if ( isset($tab_bull['eleve'][$i]['aid_b'][$m]['aid_prof_resp_login'][0]) )
							{

								$nb_prof_matiere = count($tab_bull['eleve'][$i]['aid_b'][$m]['aid_prof_resp_login']);
								$espace_matiere_prof = $espace_entre_matier/2;
								if($nb_prof_matiere>0){
									$espace_matiere_prof = $espace_matiere_prof/$nb_prof_matiere;
								}
								$nb_pass_count = '0';
								$text_prof = '';
								while ($nb_prof_matiere > $nb_pass_count)
								{
									$tmp_login_prof=$tab_bull['eleve'][$i]['aid_b'][$m]['aid_prof_resp_login'][$nb_pass_count];
									$text_prof=affiche_utilisateur($tmp_login_prof,$tab_bull['eleve'][$i]['id_classe']);

									if ( $nb_prof_matiere <= 2 ) { $hauteur_caractere_prof = 8; }
									elseif ( $nb_prof_matiere == 3) { $hauteur_caractere_prof = 5; }
									elseif ( $nb_prof_matiere > 3) { $hauteur_caractere_prof = 2; }
									$pdf->SetFont('DejaVu','',$hauteur_caractere_prof);
									$val = $pdf->GetStringWidth($text_prof);
									$taille_texte = ($tab_modele_pdf["largeur_matiere"][$classe_id]);
									$grandeur_texte='test';
									while($grandeur_texte!='ok') {
										if($taille_texte<$val)
										{
											$hauteur_caractere_prof = $hauteur_caractere_prof-0.3;
											$pdf->SetFont('DejaVu','',$hauteur_caractere_prof);
											$val = $pdf->GetStringWidth($text_prof);
										}
										else {
											$grandeur_texte='ok';
										}
									}
									$grandeur_texte='test';
									$pdf->SetX($X_bloc_matiere);
									if( empty($tab_bull['eleve'][$i]['aid_b'][$m]['aid_prof_resp_login'][$nb_pass_count+1]) ) {
										$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_matiere_prof, ($text_prof),'LRB',1,'L');
									}
									if( !empty($tab_bull['eleve'][$i]['aid_b'][$m]['aid_prof_resp_login'][$nb_pass_count+1]) ) {
										$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_matiere_prof, ($text_prof),'LR',1,'L');
									}
									$nb_pass_count = $nb_pass_count + 1;
								}
							}
							$largeur_utilise = $tab_modele_pdf["largeur_matiere"][$classe_id];

							// coefficient matière
							if($tab_modele_pdf["active_coef_moyenne"][$classe_id]==='1') {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
								$pdf->SetFont('DejaVu','',10);
								//$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $espace_entre_matier, $tab_bull['eleve'][$i]['coef_eleve'][$i][$m],1,0,'C');
								$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $espace_entre_matier, '',1,0,'C');
								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_coef_moyenne"][$classe_id];
							}

							//permet le calcul total des coefficients
							// if(empty($moyenne_min[$id_classe][$id_periode])) {
								//$total_coef_en_calcul=$total_coef_en_calcul+$tab_bull['eleve'][$i]['coef_eleve'][$i][$m];
							//}

							// nombre de note
							// 20081118
							//if($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1') {
							if(($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1')&&($tab_modele_pdf["active_nombre_note"][$classe_id]!='1')) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
								$pdf->SetFont('DejaVu','',10);
								//$valeur = $tab_bull['eleve'][$i]['nbct'][$m][$i] . "/" . $tab_bull['eleve'][$i]['groupe'][$m]['nbct'];
								$valeur = '';
								$pdf->Cell($tab_modele_pdf["largeur_nombre_note"][$classe_id], $espace_entre_matier, $valeur,1,0,'C');
								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_nombre_note"][$classe_id];
							}

							// les moyennes eleve, classe, min, max
							$cpt_ordre = 0;
							while (!empty($ordre_moyenne[$cpt_ordre]) ) {
								//eleve
								if($tab_modele_pdf["active_moyenne_eleve"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'eleve' ) {
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									$pdf->SetFont('DejaVu','B',10);
									$pdf->SetFillColor($tab_modele_pdf["couleur_reperage_eleve1"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve2"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve3"][$classe_id]);

									// calcul nombre de sous affichage

									$nb_sousaffichage='1';
									//20090908 if(empty($active_coef_sousmoyene)) { $active_coef_sousmoyene = ''; }

									//if($active_coef_sousmoyene==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; }
									if($tab_modele_pdf["active_coef_sousmoyene"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; }
									if($tab_modele_pdf["active_nombre_note"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; }
									if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') { if($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }
									if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') { if($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }
									if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') { if($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }

									// On filtre si la moyenne est vide, on affiche seulement un tiret
									if ($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note']=="-") {
										$valeur = "-";
									}
									elseif ($tab_bull['eleve'][$i]['aid_b'][$m]['aid_statut']!="") {
										if($tab_bull['eleve'][$i]['aid_b'][$m]['aid_statut']=="other") {
											$valeur = "-";
										}
										else {
											$valeur=$tab_bull['eleve'][$i]['aid_b'][$m]['aid_statut'];
										}
									}
									else {
										$valeur = present_nombre($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note'], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
									}
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, $valeur,1,2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
									$valeur = "";

									if($tab_modele_pdf["active_coef_sousmoyene"][$classe_id]==='1') {
										$pdf->SetFont('DejaVu','I',7);
										//$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'coef. '.$tab_bull['eleve'][$i]['coef_eleve'][$i][$m],'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, '','LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
									}

									if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') {
										// On affiche toutes les moyennes dans la même colonne
										$pdf->SetFont('DejaVu','I',7);
										if($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1') {
											//if ($tab_bull['eleve'][$i]['moy_classe_grp'][$m]=="-") {
											if (($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_moyenne']=="-")||($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_moyenne']=="")) {
												$valeur = "-";
											}
											else {
												$valeur = present_nombre($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_moyenne'], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
											}
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'cla.'.$valeur,'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										}
										if($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1') {
											//if ($tab_bull['eleve'][$i]['moy_min_classe_grp'][$m]=="-") {
											if (($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_min']=="-")||($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_min']=="")) {
												$valeur = "-";
											} else {
												$valeur = present_nombre($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_min'], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
											}
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'min.'.$valeur,'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										}
										if($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1') {
											//if ($tab_bull['eleve'][$i]['moy_max_classe_grp'][$m]=="-") {
											if (($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_max']=="-")||($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_max']=="")) {
												$valeur = "-";
											} else {
												$valeur = present_nombre($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_max'], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
											}
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'max.'.$valeur,'LRD',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
											$valeur = ''; // on remet à vide.
										}
									}

									if($tab_modele_pdf["active_nombre_note"][$classe_id]==='1') {
										$pdf->SetFont('DejaVu','I',7);
										$espace_pour_nb_note = $espace_entre_matier/$nb_sousaffichage;
										$espace_pour_nb_note = $espace_pour_nb_note / 2;
										$valeur1 = ''; $valeur2 = '';
										/*
										if ($tab_bull['eleve'][$i]['nbct'][$m][$i]!= 0 ) {
											$valeur1 = $tab_bull['eleve'][$i]['nbct'][$m][$i].' note';
											if($tab_bull['eleve'][$i]['nbct'][$m][$i]>1){$valeur1.='s';}
											$valeur2 = 'sur '.$tab_bull['eleve'][$i]['groupe'][$m]['nbct'];
										}
										*/
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_pour_nb_note, $valeur1, 'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_pour_nb_note, $valeur2, 'LRB',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										$valeur1 = ''; $valeur2 = '';
									}
									$pdf->SetFont('DejaVu','',10);
									$pdf->SetFillColor(0, 0, 0);
									$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];

								} // Fin affichage élève

								//classe
								//if( $tab_modele_pdf["active_moyenne_classe"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' ) {
								if(($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&( $tab_modele_pdf["active_moyenne_classe"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' )) {
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									//if ($tab_bull['eleve'][$i]['moy_classe_grp'][$m]=="-") {
									if (($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_moyenne']=="-")||($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_moyenne']=="")) {
										$valeur = "-";
									} else {
										$valeur = present_nombre($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_moyenne'], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
									}
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
									$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
								}
								//min
								//if( $tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' ) {
								if(($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&( $tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' )) {
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									$pdf->SetFont('DejaVu','',8);
									//if ($tab_bull['eleve'][$i]['moy_min_classe_grp'][$m]=="-") {
									if (($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_min']=="-")||($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_min']=="")) {
										$valeur = "-";
									} else {
										$valeur = present_nombre($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_min'], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
									}
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
									$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
								}
								//max
								//if( $tab_modele_pdf["active_moyenne_max"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' ) {
								if(($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&( $tab_modele_pdf["active_moyenne_max"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' )) {
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									//if ($tab_bull['eleve'][$i]['moy_max_classe_grp'][$m]== "-") {
									if (($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_max']=="-")||($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_max']=="")) {
										$valeur = "-";
									} else {
										$valeur = present_nombre($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note_max'], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
									}
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
									$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
								}
								//$largeur_utilise = $largeur_utilise+$largeur_moyenne;


								// rang de l'élève
								if($tab_modele_pdf["active_rang"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'rang' ) {
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									$pdf->SetFont('DejaVu','',8);
									// A REVOIR: J'AI l'EFFECTIF DU GROUPE, mais faut-il compter les élèves ABS, DISP,...?
									//if((isset($tab_bull['eleve'][$i]['rang'][$i][$m]))&&(isset($tab_bull['eleve'][$i]['groupe'][$m]['effectif']))) {
										//$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $espace_entre_matier, $tab_bull['eleve'][$i]['rang'][$i][$m].'/'.$tab_bull['eleve'][$i]['groupe'][$m]['effectif'],1,0,'C');

									//if((isset($tab_bull['eleve'][$i]['rang'][$m][$i]))&&(isset($tab_bull['eleve'][$i]['groupe'][$m]['effectif']))) {
									//	$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $espace_entre_matier, $tab_bull['eleve'][$i]['rang'][$m][$i].'/'.$tab_bull['eleve'][$i]['groupe'][$m]['effectif_avec_note'],1,0,'C');
									//}
									//else {
										$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $espace_entre_matier, '',1,0,'C');
									//}
									$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_rang"][$classe_id];
								}

								// graphique de niveau
								if($tab_modele_pdf["active_graphique_niveau"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'niveau' ) {
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									$pdf->SetFont('DejaVu','',10);
									//$id_groupe_graph = $tab_bull['eleve'][$i]['groupe'][$m]['id'];
									// placement de l'élève dans le graphique de niveau

									// AJOUT: La variable n'était pas initialisée dans le bulletin_pdf_avec_modele...
									$place_eleve='';

									if ($tab_bull['eleve'][$i]['aid_b'][$m]['aid_note']!="") {
										/*
										if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<5) { $place_eleve=5;}
										if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=5) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<8))  { $place_eleve=4;}
										if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=8) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<10)) { $place_eleve=3;}
										if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=10) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<12)) {$place_eleve=2;}
										if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=12) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<15)) { $place_eleve=1;}
										if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=15) { $place_eleve=0;}
										*/
										if(isset($tab_bull['eleve'][$i]['aid_b'][$m]['place_eleve'])) {
											//$place_eleve=$tab_bull['eleve'][$i]['aid_b'][$m]['place_eleve'];
											$place_eleve=$tab_bull['eleve'][$i]['aid_b'][$m]['place_eleve']-1;
										}
									}
									$data_grap=array();
									if(isset($tab_bull['eleve'][$i]['aid_b'][$m]['quartile1_classe'])) {$data_grap[0]=$tab_bull['eleve'][$i]['aid_b'][$m]['quartile1_classe'];}
									if(isset($tab_bull['eleve'][$i]['aid_b'][$m]['quartile2_classe'])) {$data_grap[1]=$tab_bull['eleve'][$i]['aid_b'][$m]['quartile2_classe'];}
									if(isset($tab_bull['eleve'][$i]['aid_b'][$m]['quartile3_classe'])) {$data_grap[2]=$tab_bull['eleve'][$i]['aid_b'][$m]['quartile3_classe'];}
									if(isset($tab_bull['eleve'][$i]['aid_b'][$m]['quartile4_classe'])) {$data_grap[3]=$tab_bull['eleve'][$i]['aid_b'][$m]['quartile4_classe'];}
									if(isset($tab_bull['eleve'][$i]['aid_b'][$m]['quartile5_classe'])) {$data_grap[4]=$tab_bull['eleve'][$i]['aid_b'][$m]['quartile5_classe'];}
									if(isset($tab_bull['eleve'][$i]['aid_b'][$m]['quartile6_classe'])) {$data_grap[5]=$tab_bull['eleve'][$i]['aid_b'][$m]['quartile6_classe'];}
									//if (array_sum($data_grap[$id_periode][$id_groupe_graph]) != 0) {
									if (array_sum($data_grap) != 0) {
										//$pdf->DiagBarre($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2), $tab_modele_pdf["largeur_niveau"][$classe_id], $espace_entre_matier, $data_grap[$id_periode][$id_groupe_graph], $place_eleve);
										$pdf->DiagBarre($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2), $tab_modele_pdf["largeur_niveau"][$classe_id], $espace_entre_matier, $data_grap, $place_eleve);
									}
									$place_eleve=''; // on vide la variable
									$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_niveau"][$classe_id];
								}

								// appréciation AID B
								if($tab_modele_pdf["active_appreciation"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'appreciation' ) {
									// si on autorise l'affichage des sous matière et s'il y en a alors on les affiche
									//$id_groupe_select = $tab_bull['eleve'][$i]['groupe'][$m]['id'];

									// 20160623
									if((!getSettingAOui('bullNoSaisieElementsProgrammes'))&&($tab_modele_pdf["active_colonne_Elements_Programmes"][$classe_id]==='1')) {
										$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
										$pdf->SetFont('DejaVu','',10);

										$texte_Elements_Programmes="";

										//$pdf->Cell($tab_modele_pdf["largeur_Elements_Programmes"][$classe_id], $espace_entre_matier, ($texte_Elements_Programmes),'LRB',0,'C');
										$pdf->Cell($tab_modele_pdf["largeur_Elements_Programmes"][$classe_id], $espace_entre_matier, ($texte_Elements_Programmes),'LR',0,'C');
										$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_Elements_Programmes"][$classe_id];
									}

									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									$X_sous_matiere = 0;
									$largeur_sous_matiere=0;

									/*
									if($tab_modele_pdf["autorise_sous_matiere"][$classe_id]==='1' and !empty($tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_nom'])) {
										$X_sous_matiere = $X_note_moy_app+$largeur_utilise;
										$Y_sous_matiere = $Y_decal-($espace_entre_matier/2);
										$n=0;
										$largeur_texte_sousmatiere=0; $largeur_sous_matiere=0;
										while( !empty($tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_nom'][$n]) )
										{
											$pdf->SetFont('DejaVu','',8);
											$largeur_texte_sousmatiere = $pdf->GetStringWidth($tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_nom'][$n].': '.$tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_note'][$n]);
											if($largeur_sous_matiere<$largeur_texte_sousmatiere) { $largeur_sous_matiere=$largeur_texte_sousmatiere; }
											$n = $n + 1;
										}
										if($largeur_sous_matiere!='0') { $largeur_sous_matiere = $largeur_sous_matiere + 2; }
										$n=0;
										while( !empty($tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_nom'][$n]) )
										{
											$pdf->SetXY($X_sous_matiere, $Y_sous_matiere);
											$pdf->SetFont('DejaVu','',8);
											$pdf->Cell($largeur_sous_matiere, $espace_entre_matier/count($tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_nom']), ($tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_nom'][$n].': '.$tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_note'][$n]),1,0,'L');
											$Y_sous_matiere = $Y_sous_matiere+$espace_entre_matier/count($tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_nom']);
											$n = $n + 1;
										}
										$largeur_utilise = $largeur_utilise+$largeur_sous_matiere;
									}
									*/
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									// calcul de la taille du texte des appréciation
									$hauteur_caractere_appreciation = 9;
									$pdf->SetFont('DejaVu','',$hauteur_caractere_appreciation);

									//suppression des espace en début et en fin
									//$app_aff = trim($tab_bull['eleve'][$i]['aid_b'][$m]['aid_appreciation']);
									$app_aff="";
									if($tab_bull['eleve'][$i]['aid_b'][$m]['message']!='') {
										$app_aff.=$tab_bull['eleve'][$i]['aid_b'][$m]['message'];
									}
									//if($app_aff!='') {$app_aff.=" ";}
									if(($tab_bull['eleve'][$i]['aid_b'][$m]['display_nom']=='y')&&($tab_bull['eleve'][$i]['aid_b'][$m]['aid_nom']!='')) {
										if($app_aff!='') {$app_aff.=" ";}
										$app_aff.=$tab_bull['eleve'][$i]['aid_b'][$m]['aid_nom'];
									}
									if($app_aff!='') {$app_aff.="\n";}
									$app_aff.=trim($tab_bull['eleve'][$i]['aid_b'][$m]['aid_appreciation']);

									fich_debug_bull("__________________________________________\n");
									fich_debug_bull("$app_aff\n");
									fich_debug_bull("__________________________________________\n");

									// DEBUT AJUSTEMENT TAILLE APPRECIATION
									$taille_texte_total = $pdf->GetStringWidth($app_aff);
									$largeur_appreciation2 = $largeur_appreciation - $largeur_sous_matiere;

									if($use_cell_ajustee=="n") {
										//$taille_texte = (($espace_entre_matier/3)*$largeur_appreciation2);
										$nb_ligne_app = '2.8';
										//$nb_ligne_app = '3.8';
										//$nb_ligne_app = '4.8';
										$taille_texte_max = $nb_ligne_app * ($largeur_appreciation2-4);
										//$taille_texte_max = $nb_ligne_app * ($largeur_appreciation2);
										$grandeur_texte='test';
	
										fich_debug_bull("\$taille_texte_total=$taille_texte_total\n");
										fich_debug_bull("\$largeur_appreciation2=$largeur_appreciation2\n");
										fich_debug_bull("\$nb_ligne_app=$nb_ligne_app\n");
										//fich_debug_bull("\$taille_texte_max = \$nb_ligne_app * (\$largeur_appreciation2-4)=$nb_ligne_app * ($largeur_appreciation2-4)=$taille_texte_max\n");
										fich_debug_bull("\$taille_texte_max = \$nb_ligne_app * (\$largeur_appreciation2)=$nb_ligne_app * ($largeur_appreciation2)=$taille_texte_max\n");
	
										while($grandeur_texte!='ok') {
											if($taille_texte_max < $taille_texte_total)
											{
												$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.3;
												//$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.1;
												$pdf->SetFont('DejaVu','',$hauteur_caractere_appreciation);
												$taille_texte_total = $pdf->GetStringWidth($app_aff);
											}
											else {
												$grandeur_texte='ok';
											}
										}
										$grandeur_texte='test';
										$pdf->drawTextBox(($app_aff), $largeur_appreciation2, $espace_entre_matier, 'J', 'M', 1);
									}
									else {
										//$texte="Bla bla\nbli ".$app_aff;
										$texte=$app_aff;
										$taille_max_police=$hauteur_caractere_appreciation;
										$taille_min_police=ceil($taille_max_police/3);

										$largeur_dispo=$largeur_appreciation2;
										$h_cell=$espace_entre_matier;

										if(getSettingValue('suppr_balises_app_prof')=='y') {$texte=preg_replace('/<(.*)>/U','',$texte);}
										cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'LRBT');
									}

									$pdf->SetFont('DejaVu','',10);
									$largeur_utilise = $largeur_utilise + $largeur_appreciation2;
									//$largeur_utilise = 0;
								}

								$cpt_ordre = $cpt_ordre + 1;
							}
							$largeur_utilise = 0;
							// fin de boucle d'ordre
							$Y_decal = $Y_decal+($espace_entre_matier/2);
						//}
					}
				}
				// FIN DES AID AFFICHéS AVANT LES MATIERES
				//======================================================
				fich_debug_bull("Apres les AID_B: \$Y_decal=$Y_decal\n");

				//++++++++++++++++++++++++++++++
				// DEBUG 20160220 categories de l eleve
				/*
				echo "\$tab_bull['eleve'][$i]['cat_id']<br /><pre>";
				print_r($tab_bull['eleve'][$i]['cat_id']);
				echo "</pre>";
				*/
				//++++++++++++++++++++++++++++++

				// Compteur du nombre de matières dans la catégorie
				$categorie_passe_count=0;

				//for($m=0; $m<$nb_matiere; $m++)
				for($m=0; $m<count($tab_bull['groupe']); $m++)
				{
					$pdf->SetXY($X_bloc_matiere, $Y_decal);

					fich_debug_bull("\n");
					fich_debug_bull("Catégorie précédente: \$categorie_passe=$categorie_passe\n");
					//fich_debug_bull("Catégorie courante :  \$tab_bull['nom_cat_complet'][$m]=".$tab_bull['nom_cat_complet'][$m]."\n");
					if(isset($tab_bull['nom_cat_complet'][$m])) {
						fich_debug_bull("Catégorie courante :  \$tab_bull['nom_cat_complet'][$m]=".$tab_bull['nom_cat_complet'][$m]."\n");
					}
					else {
						fich_debug_bull("Catégorie courante :  \$tab_bull['nom_cat_complet'][$m] non affectée.\n");
					}
					fich_debug_bull("\$tab_bull['groupe'][$m]['matiere']['matiere']=".$tab_bull['groupe'][$m]['matiere']['matiere']."\n");
					fich_debug_bull("\$X_bloc_matiere=$X_bloc_matiere\n");
					fich_debug_bull("\$Y_decal=$Y_decal\n");

					// si on affiche les catégories
					if($tab_modele_pdf["active_entete_regroupement"][$classe_id]==='1') {
						//si on affiche les moyennes des catégories
						//if($matiere[$ident_eleve_aff][$id_periode][$m]['categorie']!=$categorie_passe)
						//if($tab_bull['cat_id'][$m]!=$categorie_passe)

						// Si on change de catégorie
						// 20141122 : A FAIRE : Il faudrait faire un premier tour des catégories avant pour voir celles qui concernent l'élève
						//if($tab_bull['nom_cat_complet'][$m]!=$categorie_passe)
						// 20151129
						if(($tab_bull['nom_cat_complet'][$m]!=$categorie_passe)&&(in_array($tab_bull['cat_id'][$m], $tab_bull['eleve'][$i]['cat_id'])))
						{
							// Nom de la catégorie dans la première cellule (à l'horizontale)
							$hauteur_caractere_catego = '10';
							if ( $tab_modele_pdf["taille_texte_categorie"][$classe_id] != '' and $tab_modele_pdf["taille_texte_categorie"][$classe_id] != '0' ) {
								$hauteur_caractere_catego = $tab_modele_pdf["taille_texte_categorie"][$classe_id];
							}
							else {
								$hauteur_caractere_catego = '10';
							}
							$pdf->SetFont('DejaVu','',$hauteur_caractere_catego);
							$tt_catego = unhtmlentities($tab_bull['nom_cat_complet'][$m]);
							$val = $pdf->GetStringWidth($tt_catego);
							$taille_texte = ($tab_modele_pdf["largeur_matiere"][$classe_id]);
							$grandeur_texte='test';
							while($grandeur_texte!='ok') {
								if($taille_texte<$val)
								{
									$hauteur_caractere_catego = $hauteur_caractere_catego-0.3;
									$pdf->SetFont('DejaVu','',$hauteur_caractere_catego);
									$val = $pdf->GetStringWidth($tt_catego);
								}
								else {
									$grandeur_texte='ok';
								}
							}
							$grandeur_texte='test';
							$pdf->SetFillColor($tab_modele_pdf["couleur_categorie_entete1"][$classe_id], $tab_modele_pdf["couleur_categorie_entete2"][$classe_id], $tab_modele_pdf["couleur_categorie_entete3"][$classe_id]);

							fich_debug_bull("On écrit $tt_catego à \$Y_decal=$Y_decal\n");

							$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], (unhtmlentities($tt_catego)),'TLB',0,'L',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
							$largeur_utilise = $tab_modele_pdf["largeur_matiere"][$classe_id];

							// coefficient matière (affiché sans bordure gauche/droite)
							if($tab_modele_pdf["active_coef_moyenne"][$classe_id]==='1') {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
								$pdf->SetFont('DejaVu','',10);
								$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TB',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_coef_moyenne"][$classe_id];
							}

							// nombre de notes (affiché sans bordure gauche/droite)
							if(($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1')&&($tab_modele_pdf["active_nombre_note"][$classe_id]!='1')) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
								$pdf->SetFont('DejaVu','',10);
								$pdf->Cell($tab_modele_pdf["largeur_nombre_note"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TB',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_nombre_note"][$classe_id];
							}
							$pdf->SetFillColor(0, 0, 0);

							if($tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] == '7') {
								$largeur_utilise+=$largeur_appreciation;
							}

							// les moyennes eleve, classe, min, max par catégorie
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);

							$cpt_ordre = 0;
							$chapeau_moyenne = 'non';
							while ( !empty($ordre_moyenne[$cpt_ordre]) ) {
								// Moyenne de l'élève dans la catégorie
								if($tab_modele_pdf["active_moyenne_eleve"][$classe_id]==='1' and 
								   $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and 
								   $ordre_moyenne[$cpt_ordre] === 'eleve' ) {
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);

									if($tab_modele_pdf["active_moyenne_regroupement"][$classe_id]==='1') {
										//$categorie_passage=$matiere[$ident_eleve_aff][$id_periode][$m]['categorie'];
										$categorie_passage=$tab_bull['nom_cat_complet'][$m];
										//if($matiere[$ident_eleve_aff][$id_periode][$m]['affiche_moyenne']==='1')
										if(isset($tab_bull['moy_cat_eleve'][$i][$tab_bull['cat_id'][$m]]))
										{
											// On va afficher la moyenne de l'élève pour la catégorie
											if (($tab_bull['moy_cat_eleve'][$i][$tab_bull['cat_id'][$m]]=="")||($tab_bull['moy_cat_eleve'][$i][$tab_bull['cat_id'][$m]]=="-")) {
												$valeur = "-";
											} else {
												//$calcule_moyenne_eleve_categorie[$categorie_passage]=$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['moy_eleve']/$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego'];
												$valeur = present_nombre(preg_replace("/,/",".",$tab_bull['moy_cat_eleve'][$i][$tab_bull['cat_id'][$m]]), $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
												//$valeur =$tab_bull['moy_cat_eleve'][$i][$tab_bull['cat_id'][$m]];
											}
											$pdf->SetFont('DejaVu','B',8);
											$pdf->SetFillColor($tab_modele_pdf["couleur_reperage_eleve1"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve2"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve3"][$classe_id]);
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id],$valeur,1,0,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
											$pdf->SetFillColor(0, 0, 0);
											$valeur = "";
										} else {
											$pdf->SetFillColor(255, 255, 255);
											//$pdf->SetFillColor($tab_modele_pdf["couleur_reperage_eleve1"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve2"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve3"][$classe_id]);
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TLB',0,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										}
									} else {
										//$pdf->SetFillColor(0, 0, 0);
										//$pdf->SetFillColor(255, 255, 255);
										$pdf->SetFillColor($tab_modele_pdf["couleur_categorie_entete1"][$classe_id], $tab_modele_pdf["couleur_categorie_entete2"][$classe_id], $tab_modele_pdf["couleur_categorie_entete3"][$classe_id]);
										//$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TB',0,'C');
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TB',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
									}

									$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
								}

								// Moyenne de la classe dans la catégorie
								//if($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' ) {
								if(($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' )) {
									$pdf->SetXY($X_moyenne_classe, $Y_decal);

									$pdf->SetFillColor($tab_modele_pdf["couleur_categorie_entete1"][$classe_id], $tab_modele_pdf["couleur_categorie_entete2"][$classe_id], $tab_modele_pdf["couleur_categorie_entete3"][$classe_id]);

									if($tab_modele_pdf["active_moyenne_regroupement"][$classe_id]==='1') {
										//$categorie_passage=$matiere[$ident_eleve_aff][$id_periode][$m]['categorie'];
										$categorie_passage=$tab_bull['nom_cat_complet'][$m];
										//if($matiere[$ident_eleve_aff][$id_periode][$m]['affiche_moyenne']==='1')
										if(isset($tab_bull['moy_cat_classe'][$i][$tab_bull['cat_id'][$m]]))
										{
											// On va afficher la moyenne de la classe pour la catégorie
											$pdf->SetFont('DejaVu','',8);
											// 20141122
											if (($tab_bull['moy_cat_classe'][$i][$tab_bull['cat_id'][$m]]=="")||($tab_bull['moy_cat_classe'][$i][$tab_bull['cat_id'][$m]]=="-")) {
												$valeur = "-";
											} else {
												$valeur=present_nombre($tab_bull['moy_cat_classe'][$i][$tab_bull['cat_id'][$m]], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
											}
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], $valeur,'TLRB',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
										} else {
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TB',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
										}
									} else {
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TB',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
									}
									$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
								}

								$pdf->SetFont('DejaVu','',10);
								// Moyenne minimale de la classe dans la catégorie
								if(($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' )) {
									$pdf->SetXY($X_min_classe, $Y_decal);

									$pdf->SetFillColor($tab_modele_pdf["couleur_categorie_entete1"][$classe_id], $tab_modele_pdf["couleur_categorie_entete2"][$classe_id], $tab_modele_pdf["couleur_categorie_entete3"][$classe_id]);

									if($tab_modele_pdf["active_moyenne_regroupement"][$classe_id]==='1') {
										$categorie_passage=$tab_bull['nom_cat_complet'][$m];

										if(isset($tab_bull['moy_cat_min'][$i][$tab_bull['cat_id'][$m]]))
										{
											// On va afficher la moyenne min de la classe pour la catégorie
											$pdf->SetFont('DejaVu','',8);
											if (($tab_bull['moy_cat_min'][$i][$tab_bull['cat_id'][$m]]=="")||($tab_bull['moy_cat_min'][$i][$tab_bull['cat_id'][$m]]=="-")) {
												$valeur = "-";
											} else {
												$calcule_moyenne_classe_categorie[$categorie_passage]=preg_replace("/,/",".",$tab_bull['moy_cat_min'][$i][$tab_bull['cat_id'][$m]]);
												$valeur=present_nombre($calcule_moyenne_classe_categorie[$categorie_passage], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
											}
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], $valeur,'TLRB',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
										} else {
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TB',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
										}
									} else {
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TB',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
									}
									$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
								}

								// Moyenne maximale de la classe dans la catégorie
								if(($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' )) {
									$pdf->SetXY($X_max_classe, $Y_decal);

									$pdf->SetFillColor($tab_modele_pdf["couleur_categorie_entete1"][$classe_id], $tab_modele_pdf["couleur_categorie_entete2"][$classe_id], $tab_modele_pdf["couleur_categorie_entete3"][$classe_id]);

									if($tab_modele_pdf["active_moyenne_regroupement"][$classe_id]==='1') {
										$categorie_passage=$tab_bull['nom_cat_complet'][$m];

										if(isset($tab_bull['moy_cat_max'][$i][$tab_bull['cat_id'][$m]]))
										{
											// On va afficher la moyenne max de la classe pour la catégorie
											$pdf->SetFont('DejaVu','',8);
											if (($tab_bull['moy_cat_max'][$i][$tab_bull['cat_id'][$m]]=="")||($tab_bull['moy_cat_max'][$i][$tab_bull['cat_id'][$m]]=="-")) {
												$valeur = "-";
											} else {
												$calcule_moyenne_classe_categorie[$categorie_passage]=preg_replace("/,/",".",$tab_bull['moy_cat_max'][$i][$tab_bull['cat_id'][$m]]);
												$valeur=present_nombre($calcule_moyenne_classe_categorie[$categorie_passage], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
											}
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], $valeur,'TLRB',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
										} else {
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TB',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
										}
									} else {
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TB',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
									}
									$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
								}
								$cpt_ordre = $cpt_ordre + 1;
							}
							//$largeur_utilise = 0;
							// fin de boucle d'ordre

							// Rang de l'élève
							if($tab_modele_pdf["active_rang"][$classe_id]==='1') {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
								$pdf->SetFont('DejaVu','',10);

								$pdf->SetFillColor($tab_modele_pdf["couleur_categorie_entete1"][$classe_id], $tab_modele_pdf["couleur_categorie_entete2"][$classe_id], $tab_modele_pdf["couleur_categorie_entete3"][$classe_id]);

								//$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','T',0,'C');
								$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','T',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_rang"][$classe_id];
							}
							// Graphique de niveau
							if($tab_modele_pdf["active_graphique_niveau"][$classe_id]==='1') {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
								$pdf->SetFillColor($tab_modele_pdf["couleur_categorie_entete1"][$classe_id], $tab_modele_pdf["couleur_categorie_entete2"][$classe_id], $tab_modele_pdf["couleur_categorie_entete3"][$classe_id]);
								//$pdf->Cell($tab_modele_pdf["largeur_niveau"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','T',0,'C');
								$pdf->Cell($tab_modele_pdf["largeur_niveau"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TB',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_niveau"][$classe_id];
							}
							// Appreciation Ligne Catégorie de matière (vide)
							if($tab_modele_pdf["active_appreciation"][$classe_id]==='1') {

								// On enlève 0.1 pour éviter que le gris ne couvre en partie/éclaircisse la bordure verticale de droite.
								$largeur_app_et_elements_prog=$largeur_appreciation-0.1;
								// 20160623
								if((!getSettingAOui('bullNoSaisieElementsProgrammes'))&&($tab_modele_pdf["active_colonne_Elements_Programmes"][$classe_id]==='1')) {
									// On n'affiche rien... c'est la ligne catégorie de matières; il faut juste tenir compte de la largeur de colonne
									/*
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									$pdf->SetFont('DejaVu','',10);

									$texte_Elements_Programmes="";

									//$pdf->Cell($tab_modele_pdf["largeur_Elements_Programmes"][$classe_id], $espace_entre_matier, ($texte_Elements_Programmes),'LRB',0,'C');
									$pdf->Cell($tab_modele_pdf["largeur_Elements_Programmes"][$classe_id], $espace_entre_matier, ($texte_Elements_Programmes),'LR',0,'C');
									$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_Elements_Programmes"][$classe_id];
									*/
									$largeur_app_et_elements_prog=$largeur_appreciation+$tab_modele_pdf["largeur_Elements_Programmes"][$classe_id]-0.1;
								}


								// Problème de coordonnées si on met l'appréciation en première position...
								//$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
								$pdf->SetXY($X_col_app, $Y_decal);

								$pdf->SetFillColor($tab_modele_pdf["couleur_categorie_entete1"][$classe_id], $tab_modele_pdf["couleur_categorie_entete2"][$classe_id], $tab_modele_pdf["couleur_categorie_entete3"][$classe_id]);
								//$pdf->Cell($largeur_appreciation, $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TB',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
								$pdf->Cell($largeur_app_et_elements_prog, $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TBL',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
								//$pdf->Cell($largeur_appreciation, $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TB',0,'C');
								$largeur_utilise=0;
							}
							$Y_decal = $Y_decal + 5;

							$pdf->SetFillColor(0, 0, 0);
						}
					}

					fich_debug_bull("Après les catégories en entête\n");
					fich_debug_bull("\$Y_decal=$Y_decal\n");
					fich_debug_bull("\$categorie_passe_count=$categorie_passe_count\n");

					//============================
					// Modif: boireaus 20070828
					//$Y_categ_cote=$Y_decal;
					fich_debug_bull("\$tab_modele_pdf['active_regroupement_cote'][$classe_id]=".$tab_modele_pdf["active_regroupement_cote"][$classe_id]."\n");
					fich_debug_bull("\$tab_modele_pdf['active_entete_regroupement'][$classe_id]=".$tab_modele_pdf["active_entete_regroupement"][$classe_id]."\n");
					if($tab_modele_pdf["active_regroupement_cote"][$classe_id]==='1' or $tab_modele_pdf["active_entete_regroupement"][$classe_id]==='1') {
						//if($matiere[$ident_eleve_aff][$id_periode][$m]['categorie']===$categorie_passe) {
						/*
						if(isset($tab_bull['note'][$m][$i])) {
							if($tab_bull['nom_cat_complet'][$m]===$categorie_passe) {
								$categorie_passe_count=$categorie_passe_count+1;
							}
							else {
								$categorie_passe_count=0;
							}

							//if($matiere[$ident_eleve_aff][$id_periode][$m]['categorie']!=$categorie_passe) {
							if($tab_bull['nom_cat_complet'][$m]!=$categorie_passe) {
								$categorie_passe_count=$categorie_passe_count+1;
							}
						}
						*/
						fich_debug_bull("\n");
						fich_debug_bull("\$tab_bull['nom_cat_complet'][$m]=".$tab_bull['nom_cat_complet'][$m]."\n");
						fich_debug_bull("\$tab_bull['cat_id'][$m]=".$tab_bull['cat_id'][$m]."\n");
						fich_debug_bull("\$categorie_passe=$categorie_passe\n");

						//if($tab_bull['nom_cat_complet'][$m]!=$categorie_passe) {
						// 20151129
						if(($tab_bull['nom_cat_complet'][$m]!=$categorie_passe)&&(in_array($tab_bull['cat_id'][$m], $tab_bull['eleve'][$i]['cat_id'])))
						{
							$categorie_passe_count=0;

							$Y_categ_cote=$Y_decal;
							fich_debug_bull("\$Y_categ_cote=\$Y_decal=".$Y_decal."\n");
						}
						/*
						else {
							$categorie_passe_count++;
						}
						if(!isset($tab_bull['note'][$m][$i])) {
							$categorie_passe_count--;
						}
						*/

						if(isset($tab_bull['note'][$m][$i])) {
							$categorie_passe_count++;
						}
						// fin des moyen par catégorie
					}
					fich_debug_bull("Après un test sur le changement de catégorie\n");
					fich_debug_bull("\$categorie_passe_count=$categorie_passe_count\n");

					//============================

					// si on affiche les catégories sur le côté

					if(!isset($tab_bull['nom_cat_complet'][$m+1])) {
						//$matiere[$ident_eleve_aff][$id_periode][$m+1]['categorie']='';
						$tab_bull['nom_cat_complet'][$m+1]='';
					}

					if($tab_modele_pdf["active_regroupement_cote"][$classe_id]==='1') {
						// On dessine/écrit la catégorie sur le côté quand la catégorie suivante change
						//if($tab_bull['nom_cat_complet'][$m]!=$tab_bull['nom_cat_complet'][$m+1] and $categorie_passe!='')
						if(($tab_bull['nom_cat_complet'][$m]!=$tab_bull['nom_cat_complet'][$m+1])&&($categorie_passe!='')&&
						(in_array($tab_bull['cat_id'][$m], $tab_bull['eleve'][$i]['cat_id'])))
						{
							//hauteur du regroupement hauteur des matier * nombre de matier de la catégorie
							//$hauteur_regroupement=$espace_entre_matier*($categorie_passe_count+1);
							$hauteur_regroupement=$espace_entre_matier*$categorie_passe_count;

							fich_debug_bull("\$espace_entre_matier=$espace_entre_matier\n");
							fich_debug_bull("\$categorie_passe_count=$categorie_passe_count\n");
							fich_debug_bull("\$hauteur_regroupement=$hauteur_regroupement\n");

							//placement du cadre
							//if($nb_eleve_aff===0) { $enplus = 5; }
							//if($nb_eleve_aff!=0) { $enplus = 0; }
							//if($compteur_bulletins===0) { $enplus = 5; }
							//if($compteur_bulletins!=0) { $enplus = 0; }

							fich_debug_bull("Position du cadre $categorie_passe\n");
							$tmp_val=$Y_decal-$hauteur_regroupement+$espace_entre_matier;
							fich_debug_bull("\$Y_decal-\$hauteur_regroupement+\$espace_entre_matier=".$Y_decal."-".$hauteur_regroupement."+".$espace_entre_matier."=".$tmp_val."\n");

							//$pdf->SetXY($X_bloc_matiere-5,$Y_decal-$hauteur_regroupement+$espace_entre_matier);
							$pdf->SetXY($X_bloc_matiere-5,$Y_categ_cote);


							$pdf->SetFillColor($tab_modele_pdf["couleur_categorie_cote1"][$classe_id], $tab_modele_pdf["couleur_categorie_cote2"][$classe_id], $tab_modele_pdf["couleur_categorie_cote3"][$classe_id]);
							if($tab_modele_pdf["couleur_categorie_cote"][$classe_id] === '1') {
								$mode_choix_c = '2';
							}
							else {
								$mode_choix_c = '1';
							}
							$pdf->drawTextBox("", 5, $hauteur_regroupement, 'C', 'T', $mode_choix_c);
							//texte à afficher
							$hauteur_caractere_vertical = '8';
							if ( $tab_modele_pdf["taille_texte_categorie_cote"][$classe_id] != '' and $tab_modele_pdf["taille_texte_categorie_cote"][$classe_id] != '0') {
								$hauteur_caractere_vertical = $tab_modele_pdf["taille_texte_categorie_cote"][$classe_id];
							}
							else {
								$hauteur_caractere_vertical = '8';
							}
							$pdf->SetFont('DejaVu','',$hauteur_caractere_vertical);
							$text_s = unhtmlentities($tab_bull['nom_cat_complet'][$m]);
							//$text_s = $tab_bull['nom_cat_complet'][$m];
							$longeur_test_s = $pdf->GetStringWidth($text_s);

							// gestion de la taille du texte vertical
							$taille_texte = $hauteur_regroupement;
							$grandeur_texte = 'test';
							while($grandeur_texte != 'ok') {
								if($taille_texte < $longeur_test_s)
								{
									$hauteur_caractere_vertical = $hauteur_caractere_vertical-0.3;
									$pdf->SetFont('DejaVu','',$hauteur_caractere_vertical);
									$longeur_test_s = $pdf->GetStringWidth($text_s);
								}
								else {
									$grandeur_texte = 'ok';
								}
							}


							//décalage pour centrer le texte
							$deca = ($hauteur_regroupement-$longeur_test_s)/2;

							//place le texte dans le cadre
							//$placement = $Y_decal+$espace_entre_matier-$deca;
							$placement = $Y_categ_cote+$hauteur_regroupement-$deca;
							$pdf->SetFont('DejaVu','',$hauteur_caractere_vertical);
							//$pdf->TextWithDirection($X_bloc_matiere-1,$placement,(unhtmlentities($text_s)),'U');
							$pdf->TextWithDirection($X_bloc_matiere-1,$placement,$text_s,'U');
							$pdf->SetFont('DejaVu','',10);
							$pdf->SetFillColor(0, 0, 0);

							fich_debug_bull("On a écrit la catégorie sur le côté ".$text_s."\n");
						}
					}

					fich_debug_bull("Après les catégories sur le côté\n");
					fich_debug_bull("\$Y_decal=$Y_decal\n");

	// 20151129 A VOIR
					if($tab_modele_pdf["active_regroupement_cote"][$classe_id]==='1' or $tab_modele_pdf["active_entete_regroupement"][$classe_id]==='1') {
						// fin d'affichage catégorie sur le coté
						$categorie_passe=$tab_bull['nom_cat_complet'][$m];
						// fin de gestion de catégorie
					}
					//============================

					// Lignes de Matière, Note, Rang,... Appréciation
					fich_debug_bull("Avant isset(\$tab_bull['note'][$m][$i]: \$Y_decal=$Y_decal\n");
					$pdf->SetXY($X_bloc_matiere, $Y_decal);

					// Si c'est une matière suivie par l'élève
					if(isset($tab_bull['note'][$m][$i])) {
						//echo "\$tab_bull['eleve'][$i]['nom']=".$tab_bull['eleve'][$i]['nom']."<br />\n";

						// calcul la taille du titre de la matière
						$hauteur_caractere_matiere=10;
						if ( $tab_modele_pdf["taille_texte_matiere"][$classe_id] != '' and $tab_modele_pdf["taille_texte_matiere"][$classe_id] != '0' and $tab_modele_pdf["taille_texte_matiere"][$classe_id] < '11' )
						{
							$hauteur_caractere_matiere = $tab_modele_pdf["taille_texte_matiere"][$classe_id];
						}
						$pdf->SetFont('DejaVu','B',$hauteur_caractere_matiere);



						if(getSettingValue('bul_rel_nom_matieres')=='nom_groupe') {
							$info_nom_matiere=$tab_bull['groupe'][$m]['name'];
						}
						elseif(getSettingValue('bul_rel_nom_matieres')=='description_groupe') {
							$info_nom_matiere=$tab_bull['groupe'][$m]['description'];
						}
						else {
							// Pour parer au bug sur la suppression de matière alors que des groupes sont conservés:
							if(isset($tab_bull['groupe'][$m]['matiere']['nom_complet'])) {
								$info_nom_matiere=$tab_bull['groupe'][$m]['matiere']['nom_complet'];
							}
							else {
								$info_nom_matiere=$tab_bull['groupe'][$m]['name']." (".$tab_bull['groupe'][$m]['id'].")";
							}
						}

						// 20130927 : cell_ajustee() ou pas sur le nom de matière/enseignement
						if((isset($tab_modele_pdf["cell_ajustee_texte_matiere"][$classe_id]))&&($tab_modele_pdf["cell_ajustee_texte_matiere"][$classe_id]==1)) {
							// Encadrement
							$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_entre_matier, "",'LRBT',1,'L');

							// cell_ajustee() ne centre pas verticalement le texte.
							// On met un décalage pour ne pas coller le texte à la bordure
							$Y_decal_cell_ajustee=1;
							// On repositionne et on inscrit le nom de matière sur la moitié de la hauteur de la cellule
							$pdf->SetXY($X_bloc_matiere, $Y_decal+$Y_decal_cell_ajustee);

							$texte=$info_nom_matiere;
							$taille_max_police=$hauteur_caractere_matiere;
							$taille_min_police=ceil($taille_max_police/$tab_modele_pdf["cell_ajustee_texte_matiere_ratio_min_max"][$classe_id]);

							$largeur_dispo=$tab_modele_pdf["largeur_matiere"][$classe_id]-2;
							$h_cell=$espace_entre_matier/2-$Y_decal_cell_ajustee;

							cell_ajustee("<b>".$texte."</b>",$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');

						}
						else {
							$val = $pdf->GetStringWidth($info_nom_matiere);
							$taille_texte = $tab_modele_pdf["largeur_matiere"][$classe_id] - 2;
							$grandeur_texte='test';
							while($grandeur_texte!='ok') {
								if($taille_texte<$val)
								{
									$hauteur_caractere_matiere = $hauteur_caractere_matiere-0.3;
									$pdf->SetFont('DejaVu','B',$hauteur_caractere_matiere);
									$val = $pdf->GetStringWidth($info_nom_matiere);
								}
								else {
									$grandeur_texte='ok';
								}
							}
							$grandeur_texte='test';
							// Encadrement
							$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_entre_matier, "",'LRBT',1,'L');
							// On repositionne et on inscrit le nom de matière sur la moitié de la hauteur de la cellule
							$pdf->SetXY($X_bloc_matiere, $Y_decal);
							$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_entre_matier/2, ($info_nom_matiere),'LR',1,'L');
						}
						// On note l'ordonnée pour le nom des professeurs
						$Y_decal = $Y_decal+($espace_entre_matier/2);
						$pdf->SetXY($X_bloc_matiere, $Y_decal);
						$pdf->SetFont('DejaVu','',8);

						fich_debug_bull("\$info_nom_matiere=$info_nom_matiere\n");
						fich_debug_bull("Le nom de matière est écrit; on est à mi-hauteur de la cellule pour écrire le nom du prof:\n");
						fich_debug_bull("\$Y_decal=$Y_decal\n");

						// nom des professeurs

						if ( isset($tab_bull['groupe'][$m]["profs"]["list"]) )
						{
							if($tab_modele_pdf["presentation_proflist"][$classe_id]!="2") {
								// Présentation en colonne des profs
								$nb_prof_matiere = count($tab_bull['groupe'][$m]["profs"]["list"]);
								$espace_matiere_prof = $espace_entre_matier/2;
								if($nb_prof_matiere>0){
									$espace_matiere_prof = $espace_matiere_prof/$nb_prof_matiere;
								}
								$nb_pass_count = '0';
								$text_prof = '';
								while ($nb_prof_matiere > $nb_pass_count)
								{
									//$text_prof="";
									//for($loop_prof_grp=0;$loop_prof_grp<$nb_prof_par_ligne;$loop_prof_grp++) {
										// calcul de la hauteur du caractère du prof
										//$tmp_login_prof=$tab_bull['groupe'][$m]["profs"]["list"][$nb_pass_count+$loop_prof_grp];
										$tmp_login_prof=$tab_bull['groupe'][$m]["profs"]["list"][$nb_pass_count];
										/*
										$text_prof=$tab_bull['groupe'][$m]["profs"]["users"]["$tmp_login_prof"]["civilite"];
										$text_prof.=" ".$tab_bull['groupe'][$m]["profs"]["users"]["$tmp_login_prof"]["nom"];
										$text_prof.=" ".mb_substr($tab_bull['groupe'][$m]["profs"]["users"]["$tmp_login_prof"]["prenom"],0,1);
										*/
										//if($loop_prof_grp>0) {$text_prof.=", ";}
										$text_prof=affiche_utilisateur($tmp_login_prof,$tab_bull['eleve'][$i]['id_classe']);
									//}
	
									if ( $nb_prof_matiere <= 2 ) { $hauteur_caractere_prof = 8; }
									elseif ( $nb_prof_matiere == 3) { $hauteur_caractere_prof = 5; }
									elseif ( $nb_prof_matiere > 3) { $hauteur_caractere_prof = 2; }
									$pdf->SetFont('DejaVu','',$hauteur_caractere_prof);
									$val = $pdf->GetStringWidth($text_prof);
									$taille_texte = ($tab_modele_pdf["largeur_matiere"][$classe_id]);
									$grandeur_texte='test';
									while($grandeur_texte!='ok') {
										if($taille_texte<$val)
										{
											$hauteur_caractere_prof = $hauteur_caractere_prof-0.3;
											$pdf->SetFont('DejaVu','',$hauteur_caractere_prof);
											$val = $pdf->GetStringWidth($text_prof);
										}
										else {
											$grandeur_texte='ok';
										}
									}
									$grandeur_texte='test';
									$pdf->SetX($X_bloc_matiere);
									if( empty($tab_bull['groupe'][$m]["profs"]["list"][$nb_pass_count+1]) ) {
										$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_matiere_prof, ($text_prof),'LRB',1,'L');
									}
									if( !empty($tab_bull['groupe'][$m]["profs"]["list"][$nb_pass_count+1]) ) {
										$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_matiere_prof, ($text_prof),'LR',1,'L');
									}
									$nb_pass_count = $nb_pass_count + 1;
								}
							}
							else {
								// Présentation en ligne des profs
								// On n'a pas forcément le formatage choisi pour la classe...
								//$text_prof=$tab_bull['groupe'][$m]["profs"]["proflist_string"]."  ";
								$text_prof="";
								for($loop_prof_grp=0;$loop_prof_grp<count($tab_bull['groupe'][$m]["profs"]["list"]);$loop_prof_grp++) {
									$tmp_login_prof=$tab_bull['groupe'][$m]["profs"]["list"][$loop_prof_grp];
									if($loop_prof_grp>0) {$text_prof.=", ";}
									$text_prof.=affiche_utilisateur($tmp_login_prof,$tab_bull['eleve'][$i]['id_classe']);
								}

								if($text_prof!="") {
									$espace_matiere_prof = $espace_entre_matier/2;
									$hauteur_caractere_prof = 8;

									if($use_cell_ajustee=="n") {
										$pdf->SetFont('DejaVu','',$hauteur_caractere_prof);
										$val = $pdf->GetStringWidth($text_prof);
										$taille_texte = ($tab_modele_pdf["largeur_matiere"][$classe_id]);
										$grandeur_texte='test';
										while($grandeur_texte!='ok') {
											if($taille_texte<$val)
											{
												$hauteur_caractere_prof = $hauteur_caractere_prof-0.3;
												$pdf->SetFont('DejaVu','',$hauteur_caractere_prof);
												$val = $pdf->GetStringWidth($text_prof);
											}
											else {
												$grandeur_texte='ok';
											}
										}
										$grandeur_texte='test';
										$pdf->SetX($X_bloc_matiere);
										$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_matiere_prof, ($text_prof),'LR',1,'L');
									}
									else {
										$texte=$text_prof;
										$taille_max_police=$hauteur_caractere_prof;
										$taille_min_police=ceil($hauteur_caractere_prof/3);
	
										$largeur_dispo=$tab_modele_pdf["largeur_matiere"][$classe_id];
										$h_cell=$espace_matiere_prof;

										$pdf->SetX($X_bloc_matiere);
	
										cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');
									}
								}
							}
						}
						$largeur_utilise = $tab_modele_pdf["largeur_matiere"][$classe_id];

						// coefficient matière
						if($tab_modele_pdf["active_coef_moyenne"][$classe_id]==='1') {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
							$pdf->SetFont('DejaVu','',10);
							$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $espace_entre_matier, $tab_bull['coef_eleve'][$i][$m],1,0,'C');
							$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_coef_moyenne"][$classe_id];
						}

						//permet le calcul total des coefficients
						// if(empty($moyenne_min[$id_classe][$id_periode])) {
							$total_coef_en_calcul=$total_coef_en_calcul+$tab_bull['coef_eleve'][$i][$m];
						//}

						// nombre de note
						// 20081118
						//if($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1') {
						if(($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1')&&($tab_modele_pdf["active_nombre_note"][$classe_id]!='1')) {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
							$pdf->SetFont('DejaVu','',10);
							$valeur = $tab_bull['nbct'][$m][$i] . "/" . $tab_bull['groupe'][$m]['nbct'];
							$pdf->Cell($tab_modele_pdf["largeur_nombre_note"][$classe_id], $espace_entre_matier, $valeur,1,0,'C');
							$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_nombre_note"][$classe_id];
						}

						// les moyennes eleve, classe, min, max
						$cpt_ordre = 0;
						while (!empty($ordre_moyenne[$cpt_ordre]) ) {
							//eleve
							if($tab_modele_pdf["active_moyenne_eleve"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'eleve' ) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
								$pdf->SetFillColor($tab_modele_pdf["couleur_reperage_eleve1"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve2"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve3"][$classe_id]);

								// calcul nombre de sous affichage

								$nb_sousaffichage='1';

								if(($tab_modele_pdf["moyennes_periodes_precedentes"][$classe_id]=='y')&&(isset($tab_bull['login_prec']))) {
									$nb_sousaffichage+=count($tab_bull['login_prec']); // Il faut récupérer le nombre de périodes...
								}

								// 20130520
								if((isset($tab_modele_pdf["moyennes_annee"][$classe_id]))&&($tab_modele_pdf["moyennes_annee"][$classe_id]=='y')) { $nb_sousaffichage = $nb_sousaffichage + 1; }

								if($tab_modele_pdf["active_coef_sousmoyene"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; }

								//20100615
								//if((!isset($moyennes_periodes_precedentes))||($moyennes_periodes_precedentes!="y")) {
								if($tab_modele_pdf["moyennes_periodes_precedentes"][$classe_id]!='y') {
									// On n'affiche pas tout ce qui suit en plus, si on affiche les moyennes de toutes les périodes déjà
									if($tab_modele_pdf["active_nombre_note"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; }
									if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') { if($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }
									if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') { if($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }
									if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') { if($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }
								}


								if(($tab_modele_pdf["moyennes_periodes_precedentes"][$classe_id]!='y')&&
								($tab_modele_pdf["moyennes_annee"][$classe_id]!='y')) {
									// On ne va pas afficher les moyennes des périodes précédentes dans la même cellule
									if($tab_modele_pdf["evolution_moyenne_periode_precedente"][$classe_id]=='y') {
										$pdf->SetFont('DejaVu','B',8);
									}
									else {
										$pdf->SetFont('DejaVu','B',10);
									}

									$fleche_evolution="";

									// On filtre si la moyenne est vide, on affiche seulement un tiret
									if ($tab_bull['note'][$m][$i]=="-") {
										$valeur = "-";
									}
									elseif ($tab_bull['statut'][$m][$i]!="") {
										$valeur=$tab_bull['statut'][$m][$i];
									}
									else {
										$valeur = present_nombre($tab_bull['note'][$m][$i], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);

										//if((isset($evolution_moyenne_periode_precedente))&&($evolution_moyenne_periode_precedente=="y")) {
										if (($tab_modele_pdf["evolution_moyenne_periode_precedente"][$classe_id]=='y')&&(isset($tab_bull['login_prec']))) {
											//$fleche_evolution="";

											foreach($tab_bull['login_prec'] as $key => $value) {
												// Il faut récupérer l'id_groupe et l'indice de l'élève... dans les tableaux récupérés de calcul_moy_gen.inc.php
												// Tableaux d'indices [$j][$i] (groupe, élève)
												$indice_eleve=-1;
												for($loop_l=0;$loop_l<count($tab_bull['login_prec'][$key]);$loop_l++) {
													//echo "\$tab_bull['login_prec'][$key][$loop_l]=".$tab_bull['login_prec'][$key][$loop_l]." et \$tab_bull['eleve'][$i]['login']=".$tab_bull['eleve'][$i]['login']."<br />\n";
													if($tab_bull['login_prec'][$key][$loop_l]==$tab_bull['eleve'][$i]['login']) {$indice_eleve=$loop_l;break;}
												}
												//echo "\$indice_eleve=$indice_eleve<br />\n";
		
												if($indice_eleve!=-1) {
													// Recherche du groupe
													$indice_grp=-1;
													for($loop_l=0;$loop_l<count($tab_bull['group_prec'][$key]);$loop_l++) {
														//echo "\$tab_bull['group_prec'][$key][$loop_l]['id']=".$tab_bull['group_prec'][$key][$loop_l]['id']." et \$tab_bull['groupe'][$m]['id']=".$tab_bull['groupe'][$m]['id']."<br />\n";
														if($tab_bull['group_prec'][$key][$loop_l]['id']==$tab_bull['groupe'][$m]['id']) {$indice_grp=$loop_l;break;}
													}
													//echo "\$indice_grp=$indice_grp<br />\n";
		
													if($indice_grp!=-1) {
														if(isset($tab_bull['statut_prec'][$key][$indice_grp][$indice_eleve])) {
															if ($tab_bull['statut_prec'][$key][$indice_grp][$indice_eleve]=="") {
																//echo "\$tab_bull['note'][$m][$i]=".$tab_bull['note'][$m][$i]."<br />\n";
																//echo "\$tab_bull['note_prec'][$key][$indice_grp][$indice_eleve]=".$tab_bull['note_prec'][$key][$indice_grp][$indice_eleve]."<br />\n";
																// 20151201: METTRE UN SEUIL POUR L'EVOLUTION DE LA MOYENNE
																if($tab_bull['note'][$m][$i]>=$tab_bull['note_prec'][$key][$indice_grp][$indice_eleve]+$tab_modele_pdf["evolution_moyenne_periode_precedente_seuil"][$classe_id]) {
																	$fleche_evolution="+";
																}
																elseif($tab_bull['note'][$m][$i]<=$tab_bull['note_prec'][$key][$indice_grp][$indice_eleve]-$tab_modele_pdf["evolution_moyenne_periode_precedente_seuil"][$classe_id]) {
																	$fleche_evolution="-";
																}
																else {
																	$fleche_evolution="";
																}
																//echo "\$fleche_evolution=".$fleche_evolution."<br />\n";

																//$valeur = present_nombre($tab_bull['note_prec'][$key][$indice_grp][$indice_eleve], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
															}

														}
													}
												}
		
											}


										}
									}
									if($fleche_evolution!="") {$fleche_evolution=" ".$fleche_evolution;}
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, $valeur.$fleche_evolution,1,2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
									$valeur = "";

									if($tab_modele_pdf["active_coef_sousmoyene"][$classe_id]==='1') {
										$pdf->SetFont('DejaVu','I',7);
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'coef. '.$tab_bull['coef_eleve'][$i][$m],'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
									}

									if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') {
										// On affiche toutes les moyennes dans la même colonne
										$pdf->SetFont('DejaVu','I',7);
										if($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1') {
											//if ($tab_bull['moy_classe_grp'][$m]=="-") {
											if (($tab_bull['moy_classe_grp'][$m]=="-")||($tab_bull['moy_classe_grp'][$m]=="")) {
												$valeur = "-";
											}
											else {
												$valeur = present_nombre($tab_bull['moy_classe_grp'][$m], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
											}
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'cla.'.$valeur,'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										}
										if($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1') {
											//if ($tab_bull['moy_min_classe_grp'][$m]=="-") {
											if (($tab_bull['moy_min_classe_grp'][$m]=="-")||($tab_bull['moy_min_classe_grp'][$m]=="")) {
												$valeur = "-";
											} else {
												$valeur = present_nombre($tab_bull['moy_min_classe_grp'][$m], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
											}
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'min.'.$valeur,'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										}
										if($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1') {
											//if ($tab_bull['moy_max_classe_grp'][$m]=="-") {
											if (($tab_bull['moy_max_classe_grp'][$m]=="-")||($tab_bull['moy_max_classe_grp'][$m]=="")) {
												$valeur = "-";
											} else {
												$valeur = present_nombre($tab_bull['moy_max_classe_grp'][$m], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
											}
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'max.'.$valeur,'LRD',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
											$valeur = ''; // on remet à vide.
										}
									}

									if($tab_modele_pdf["active_nombre_note"][$classe_id]==='1') {
										$pdf->SetFont('DejaVu','I',7);
										$espace_pour_nb_note = $espace_entre_matier/$nb_sousaffichage;
										$espace_pour_nb_note = $espace_pour_nb_note / 2;
										$valeur1 = ''; $valeur2 = '';
										if ($tab_bull['nbct'][$m][$i]!= 0 ) {
											$valeur1 = $tab_bull['nbct'][$m][$i].' note';
											if($tab_bull['nbct'][$m][$i]>1){$valeur1.='s';}
											$valeur2 = 'sur '.$tab_bull['groupe'][$m]['nbct'];
										}
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_pour_nb_note, $valeur1, 'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_pour_nb_note, $valeur2, 'LRB',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										$valeur1 = ''; $valeur2 = '';
									}

								}
								else {
									// On affiche les moyennes de l'élève pour les autres périodes dans la même colonne
									// Il faut récupérer l'indice de l'élève... dans les tableaux récupérés de calcul_moy_gen.inc.php
									$pdf->SetFont('DejaVu','I',6);

									$fleche_evolution="";

									if($tab_modele_pdf["moyennes_periodes_precedentes"][$classe_id]=='y') {
										if(isset($tab_bull['login_prec'])) {

											//for($loop_p=1;$loop_p<count($tab_bull['login_prec']);$loop_p++) {
											foreach($tab_bull['login_prec'] as $key => $value) {
												// Il faut récupérer l'id_groupe et l'indice de l'élève... dans les tableaux récupérés de calcul_moy_gen.inc.php
												// Tableaux d'indices [$j][$i] (groupe, élève)
												//		$tab_bull['note_prec'][$loop_p]=$current_eleve_note;
												//		$tab_bull['statut_prec'][$loop_p]=$current_eleve_statut;
												$indice_eleve=-1;
												//for($loop_l=0;$loop_l<count($tab_bull['login_prec'][$loop_p]);$loop_l++) {
												for($loop_l=0;$loop_l<count($tab_bull['login_prec'][$key]);$loop_l++) {
													//echo "\$tab_bull['login_prec'][$key][$loop_l]=".$tab_bull['login_prec'][$key][$loop_l]." et \$tab_bull['eleve'][$i]['login']=".$tab_bull['eleve'][$i]['login']."<br />\n";
													if($tab_bull['login_prec'][$key][$loop_l]==$tab_bull['eleve'][$i]['login']) {$indice_eleve=$loop_l;break;}
												}
												//echo "\$indice_eleve=$indice_eleve<br />\n";
	
												if($indice_eleve!=-1) {
													// Recherche du groupe
													$indice_grp=-1;
													for($loop_l=0;$loop_l<count($tab_bull['group_prec'][$key]);$loop_l++) {
														//echo "\$tab_bull['group_prec'][$key][$loop_l]['id']=".$tab_bull['group_prec'][$key][$loop_l]['id']." et \$tab_bull['groupe'][$m]['id']=".$tab_bull['groupe'][$m]['id']."<br />\n";
														if($tab_bull['group_prec'][$key][$loop_l]['id']==$tab_bull['groupe'][$m]['id']) {$indice_grp=$loop_l;break;}
													}
													//echo "\$indice_grp=$indice_grp<br />\n";
	
													if($indice_grp!=-1) {
														if(isset($tab_bull['statut_prec'][$key][$indice_grp][$indice_eleve])) {
															if ($tab_bull['statut_prec'][$key][$indice_grp][$indice_eleve]!="") {
																$valeur = $tab_bull['statut_prec'][$key][$indice_grp][$indice_eleve];
															}
															else {
																$valeur = present_nombre($tab_bull['note_prec'][$key][$indice_grp][$indice_eleve], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
																//===============================
																if($tab_bull['note'][$m][$i]>=$tab_bull['note_prec'][$key][$indice_grp][$indice_eleve]+$tab_modele_pdf["evolution_moyenne_periode_precedente_seuil"][$classe_id]) {
																	$fleche_evolution="+";
																}
																elseif($tab_bull['note'][$m][$i]<=$tab_bull['note_prec'][$key][$indice_grp][$indice_eleve]-$tab_modele_pdf["evolution_moyenne_periode_precedente_seuil"][$classe_id]) {
																	$fleche_evolution="-";
																}
																else {
																	$fleche_evolution="";
																}
																//===============================
															}
															if($key==1) {$bordure_top='T';} else {$bordure_top='';}
															$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'P'.$key.': '.$valeur,'LR'.$bordure_top,2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
														}
													}
												}
	
											}
										}
									}
									if($fleche_evolution!="") {$fleche_evolution=" ".$fleche_evolution;}

									if($tab_modele_pdf["evolution_moyenne_periode_precedente"][$classe_id]=='y') {
										$pdf->SetFont('DejaVu','B',8);
									}
									else {
										$pdf->SetFont('DejaVu','B',10);
									}

									// On filtre si la moyenne est vide, on affiche seulement un tiret
									if ($tab_bull['note'][$m][$i]=="-") {
										$valeur = "-";
									}
									elseif ($tab_bull['statut'][$m][$i]!="") {
										$valeur=$tab_bull['statut'][$m][$i];
									}
									else {
										$valeur = present_nombre($tab_bull['note'][$m][$i], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
									}
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, $valeur.$fleche_evolution,1,2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
									// Réinitialisation
									$valeur = "";


									// 20130520
									//echo "\$tab_modele_pdf[\"moyennes_annee\"][$classe_id]=".$tab_modele_pdf["moyennes_annee"][$classe_id]."<br />\n";
									//echo "\$tab_bull['moy_annee'][$m][$i]=".$tab_bull['moy_annee'][$m][$i]."<br />\n";
									if((isset($tab_modele_pdf["moyennes_annee"][$classe_id]))&&
									($tab_modele_pdf["moyennes_annee"][$classe_id]=='y')&&
									(isset($tab_bull['moy_annee'][$m][$i]))) {
										$pdf->SetFont('DejaVu','I',6);
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, "An: ".$tab_bull['moy_annee'][$m][$i],1,2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
									}


									// On affiche éventuellement le coef
									if($tab_modele_pdf["active_coef_sousmoyene"][$classe_id]==='1') {
										$pdf->SetFont('DejaVu','I',6);
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'coef. '.$tab_bull['coef_eleve'][$i][$m],'LRB',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
									}

								}

								$pdf->SetFont('DejaVu','',10);
								$pdf->SetFillColor(0, 0, 0);
								$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];

							} // Fin affichage élève


							//classe
							//if( $tab_modele_pdf["active_moyenne_classe"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' ) {
							if(($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&( $tab_modele_pdf["active_moyenne_classe"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' )) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
								//if ($tab_bull['moy_classe_grp'][$m]=="-") {
								if (($tab_bull['moy_classe_grp'][$m]=="-")||($tab_bull['moy_classe_grp'][$m]=="")) {
									$valeur = "-";
								} else {
									$valeur = present_nombre($tab_bull['moy_classe_grp'][$m], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
								}
								$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
								$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
							}
							//min
							//if( $tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' ) {
							if(($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&( $tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' )) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
								$pdf->SetFont('DejaVu','',8);
								//if ($tab_bull['moy_min_classe_grp'][$m]=="-") {
								if (($tab_bull['moy_min_classe_grp'][$m]=="-")||($tab_bull['moy_min_classe_grp'][$m]=="")) {
									$valeur = "-";
								} else {
									$valeur = present_nombre($tab_bull['moy_min_classe_grp'][$m], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
								}
								$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
								$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
							}
							//max
							//if( $tab_modele_pdf["active_moyenne_max"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' ) {
							if(($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&( $tab_modele_pdf["active_moyenne_max"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' )) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
								//if ($tab_bull['moy_max_classe_grp'][$m]== "-") {
								if (($tab_bull['moy_max_classe_grp'][$m]=="-")||($tab_bull['moy_max_classe_grp'][$m]=="")) {
									$valeur = "-";
								} else {
									$valeur = present_nombre($tab_bull['moy_max_classe_grp'][$m], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
								}
								$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
								$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
							}
							//$largeur_utilise = $largeur_utilise+$largeur_moyenne;


							// rang de l'élève
							if($tab_modele_pdf["active_rang"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'rang' ) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
								$pdf->SetFont('DejaVu','',8);
								// A REVOIR: J'AI l'EFFECTIF DU GROUPE, mais faut-il compter les élèves ABS, DISP,...?
								//if((isset($tab_bull['rang'][$i][$m]))&&(isset($tab_bull['groupe'][$m]['effectif']))) {
									//$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $espace_entre_matier, $tab_bull['rang'][$i][$m].'/'.$tab_bull['groupe'][$m]['effectif'],1,0,'C');
								if((isset($tab_bull['groupe'][$m]['effectif_avec_note']))&&($tab_bull['groupe'][$m]['effectif_avec_note']==0)) {
									$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $espace_entre_matier, "-",1,0,'C');
								}
								//elseif((isset($tab_bull['rang'][$m][$i]))&&(isset($tab_bull['groupe'][$m]['effectif']))) {
								elseif((isset($tab_bull['rang'][$m][$i]))&&(isset($tab_bull['groupe'][$m]['effectif_avec_note']))) {
									$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $espace_entre_matier, $tab_bull['rang'][$m][$i].'/'.$tab_bull['groupe'][$m]['effectif_avec_note'],1,0,'C');
								}
								else {
									$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $espace_entre_matier, '',1,0,'C');
								}
								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_rang"][$classe_id];
							}

							// graphique de niveau
							if($tab_modele_pdf["active_graphique_niveau"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'niveau' ) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
								$pdf->SetFont('DejaVu','',10);
								$id_groupe_graph = $tab_bull['groupe'][$m]['id'];
								// placement de l'élève dans le graphique de niveau

								// AJOUT: La variable n'était pas initialisée dans le bulletin_pdf_avec_modele...
								$place_eleve='';

								if ($tab_bull['note'][$m][$i]!="") {
									/*
									if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<5) { $place_eleve=5;}
									if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=5) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<8))  { $place_eleve=4;}
									if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=8) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<10)) { $place_eleve=3;}
									if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=10) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<12)) {$place_eleve=2;}
									if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=12) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<15)) { $place_eleve=1;}
									if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=15) { $place_eleve=0;}
									*/
									if(isset($tab_bull['place_eleve'][$m][$i])) {
										//$place_eleve=$tab_bull['place_eleve'][$m][$i];
										$place_eleve=$tab_bull['place_eleve'][$m][$i]-1;
									}
								}
								$data_grap[0]=$tab_bull['quartile1_grp'][$m];
								$data_grap[1]=$tab_bull['quartile2_grp'][$m];
								$data_grap[2]=$tab_bull['quartile3_grp'][$m];
								$data_grap[3]=$tab_bull['quartile4_grp'][$m];
								$data_grap[4]=$tab_bull['quartile5_grp'][$m];
								$data_grap[5]=$tab_bull['quartile6_grp'][$m];
								//if (array_sum($data_grap[$id_periode][$id_groupe_graph]) != 0) {
								if (array_sum($data_grap) != 0) {
									//$pdf->DiagBarre($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2), $tab_modele_pdf["largeur_niveau"][$classe_id], $espace_entre_matier, $data_grap[$id_periode][$id_groupe_graph], $place_eleve);
									$pdf->DiagBarre($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2), $tab_modele_pdf["largeur_niveau"][$classe_id], $espace_entre_matier, $data_grap, $place_eleve);
								}
								$place_eleve=''; // on vide la variable
								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_niveau"][$classe_id];
							}

							//appréciation
							if($tab_modele_pdf["active_appreciation"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'appreciation' ) {

								// 20160623
								if((!getSettingAOui('bullNoSaisieElementsProgrammes'))&&($tab_modele_pdf["active_colonne_Elements_Programmes"][$classe_id]==='1')) {
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));

									$hauteur_caractere_appreciation = 9;
									$pdf->SetFont('DejaVu','',$hauteur_caractere_appreciation);

									$texte_Elements_Programmes="";

									if((isset($tab_bull['ElementsProgrammes']['ele'][$tab_bull['eleve'][$i]['login']][$tab_bull['groupe'][$m]['id']]))&&(is_array($tab_bull['ElementsProgrammes']['ele'][$tab_bull['eleve'][$i]['login']][$tab_bull['groupe'][$m]['id']]))) {
										for($loop_mep=0;$loop_mep<count($tab_bull['ElementsProgrammes']['ele'][$tab_bull['eleve'][$i]['login']][$tab_bull['groupe'][$m]['id']]);$loop_mep++) {
											if($texte_Elements_Programmes!="") {
												$texte_Elements_Programmes.="\n";
											}
											$texte_Elements_Programmes.=$tab_bull['ElementsProgrammes']['ele'][$tab_bull['eleve'][$i]['login']][$tab_bull['groupe'][$m]['id']][$loop_mep];
										}
									}

									if($texte_Elements_Programmes=="") {
										$pdf->Cell($tab_modele_pdf["largeur_Elements_Programmes"][$classe_id], $espace_entre_matier, ($texte_Elements_Programmes),'LRB',0,'C');
									}
									else {
										// DEBUT AJUSTEMENT TAILLE ELEMENTS PROGRAMME
										$taille_texte_total = $pdf->GetStringWidth($texte_Elements_Programmes);
										$largeur_dispo=$tab_modele_pdf["largeur_Elements_Programmes"][$classe_id];

										if($use_cell_ajustee=="n") {
											$nb_ligne_app = '2.8';
											$taille_texte_max = $nb_ligne_app * ($largeur_dispo-4);
											$grandeur_texte='test';	

											while($grandeur_texte!='ok') {
												if($taille_texte_max < $taille_texte_total)
												{
													$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.3;
													$pdf->SetFont('DejaVu','',$hauteur_caractere_appreciation);
													$taille_texte_total = $pdf->GetStringWidth($texte_Elements_Programmes);
												}
												else {
													$grandeur_texte='ok';
												}
											}
											$grandeur_texte='test';
											$pdf->drawTextBox(($texte_Elements_Programmes), $largeur_dispo, $espace_entre_matier, 'J', 'M', 1);
										}
										else {
											$texte=$texte_Elements_Programmes;
											$taille_max_police=$hauteur_caractere_appreciation;
											$taille_min_police=ceil($taille_max_police/3);

											$h_cell=$espace_entre_matier;

											if(getSettingValue('suppr_balises_app_prof')=='y') {$texte=preg_replace('/<(.*)>/U','',$texte);}
											cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'LRBT');
										}

									}

									$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_Elements_Programmes"][$classe_id];
								}


								// si on autorise l'affichage des sous matière et s'il y en a alors on les affiche
								$id_groupe_select = $tab_bull['groupe'][$m]['id'];
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
								$X_sous_matiere = 0; $largeur_sous_matiere=0;

								if($tab_modele_pdf["autorise_sous_matiere"][$classe_id]==='1' and !empty($tab_bull['groupe'][$m][$i]['cn_nom'])) {
									$X_sous_matiere = $X_note_moy_app+$largeur_utilise;
									$Y_sous_matiere = $Y_decal-($espace_entre_matier/2);
									$n=0;
									$largeur_texte_sousmatiere=0; $largeur_sous_matiere=0;
									while( !empty($tab_bull['groupe'][$m][$i]['cn_nom'][$n]) )
									{
										$pdf->SetFont('DejaVu','',8);
										$largeur_texte_sousmatiere = $pdf->GetStringWidth($tab_bull['groupe'][$m][$i]['cn_nom'][$n].': '.$tab_bull['groupe'][$m][$i]['cn_note'][$n]);
										if($largeur_sous_matiere<$largeur_texte_sousmatiere) { $largeur_sous_matiere=$largeur_texte_sousmatiere; }
										$n = $n + 1;
									}
									if($largeur_sous_matiere!='0') { $largeur_sous_matiere = $largeur_sous_matiere + 2; }
									$n=0;
									while( !empty($tab_bull['groupe'][$m][$i]['cn_nom'][$n]) )
									{
										$pdf->SetXY($X_sous_matiere, $Y_sous_matiere);
										$pdf->SetFont('DejaVu','',8);
										$pdf->Cell($largeur_sous_matiere, $espace_entre_matier/count($tab_bull['groupe'][$m][$i]['cn_nom']), ($tab_bull['groupe'][$m][$i]['cn_nom'][$n].': '.$tab_bull['groupe'][$m][$i]['cn_note'][$n]),1,0,'L');
										$Y_sous_matiere = $Y_sous_matiere+$espace_entre_matier/count($tab_bull['groupe'][$m][$i]['cn_nom']);
										$n = $n + 1;
									}
									$largeur_utilise = $largeur_utilise+$largeur_sous_matiere;
								}
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
								// calcul de la taille du texte des appréciation
								$hauteur_caractere_appreciation = 9;
								$pdf->SetFont('DejaVu','',$hauteur_caractere_appreciation);

								//suppression des espace en début et en fin
								$app_aff = trim($tab_bull['app'][$m][$i]);

								fich_debug_bull("__________________________________________\n");
								fich_debug_bull("$app_aff\n");
								fich_debug_bull("__________________________________________\n");

								// DEBUT AJUSTEMENT TAILLE APPRECIATION
								$taille_texte_total = $pdf->GetStringWidth($app_aff);
								$largeur_appreciation2 = $largeur_appreciation - $largeur_sous_matiere;

								if($use_cell_ajustee=="n") {
									//$taille_texte = (($espace_entre_matier/3)*$largeur_appreciation2);
									$nb_ligne_app = '2.8';
									//$nb_ligne_app = '3.8';
									//$nb_ligne_app = '4.8';
									$taille_texte_max = $nb_ligne_app * ($largeur_appreciation2-4);
									//$taille_texte_max = $nb_ligne_app * ($largeur_appreciation2);
									$grandeur_texte='test';
	
									fich_debug_bull("\$taille_texte_total=$taille_texte_total\n");
									fich_debug_bull("\$largeur_appreciation2=$largeur_appreciation2\n");
									fich_debug_bull("\$nb_ligne_app=$nb_ligne_app\n");
									//fich_debug_bull("\$taille_texte_max = \$nb_ligne_app * (\$largeur_appreciation2-4)=$nb_ligne_app * ($largeur_appreciation2-4)=$taille_texte_max\n");
									fich_debug_bull("\$taille_texte_max = \$nb_ligne_app * (\$largeur_appreciation2)=$nb_ligne_app * ($largeur_appreciation2)=$taille_texte_max\n");
	
									while($grandeur_texte!='ok') {
										if($taille_texte_max < $taille_texte_total)
										{
											$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.3;
											//$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.1;
											$pdf->SetFont('DejaVu','',$hauteur_caractere_appreciation);
											$taille_texte_total = $pdf->GetStringWidth($app_aff);
										}
										else {
											$grandeur_texte='ok';
										}
									}
									$grandeur_texte='test';
									$pdf->drawTextBox(($app_aff), $largeur_appreciation2, $espace_entre_matier, 'J', 'M', 1);
								}
								else {
									$texte=$app_aff;
									//$texte="Bla bla\nbli ".$app_aff;
									$taille_max_police=$hauteur_caractere_appreciation;
									$taille_min_police=ceil($taille_max_police/3);

									$largeur_dispo=$largeur_appreciation2;
									$h_cell=$espace_entre_matier;

									if(getSettingValue('suppr_balises_app_prof')=='y') {$texte=preg_replace('/<(.*)>/U','',$texte);}
									cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'LRBT');
								}

								$pdf->SetFont('DejaVu','',10);
								$largeur_utilise = $largeur_utilise + $largeur_appreciation2;
								//$largeur_utilise = 0;
							}

							$cpt_ordre = $cpt_ordre + 1;
						}
						$largeur_utilise = 0;
						// fin de boucle d'ordre
						$Y_decal = $Y_decal+($espace_entre_matier/2);
						fich_debug_bull("Apres affichage de l'appreciation: \$Y_decal=$Y_decal\n");
					}
				}

				fich_debug_bull("Avant les AID_E: \$Y_decal=$Y_decal\n");

				//======================================================
				// DEBUT DES AID AFFICHéS APRES LES MATIERES
				if(isset($tab_bull['eleve'][$i]['aid_e'])) {
					//echo "count(\$tab_bull['eleve'][$i]['aid_e']=".count($tab_bull['eleve'][$i]['aid_e'])."<br />";
					for($m=0;$m<count($tab_bull['eleve'][$i]['aid_e']);$m++) {
						$pdf->SetXY($X_bloc_matiere, $Y_decal);

						// Si c'est une matière suivie par l'élève
						//if(isset($tab_bull['eleve'][$i]['note'][$m][$i])) {

							// calcul la taille du titre de la matière
							$hauteur_caractere_matiere=10;
							if ( $tab_modele_pdf["taille_texte_matiere"][$classe_id] != '' and $tab_modele_pdf["taille_texte_matiere"][$classe_id] != '0' and $tab_modele_pdf["taille_texte_matiere"][$classe_id] < '11' )
							{
								$hauteur_caractere_matiere = $tab_modele_pdf["taille_texte_matiere"][$classe_id];
							}
							$pdf->SetFont('DejaVu','B',$hauteur_caractere_matiere);

							// Pour parer au bug sur la suppression de matière alors que des groupes sont conservés:
							if(isset($tab_bull['eleve'][$i]['aid_e'][$m]['nom_complet'])) {
								$info_nom_matiere=$tab_bull['eleve'][$i]['aid_e'][$m]['nom_complet'];
							}
							else {
								$info_nom_matiere=$tab_bull['eleve'][$i]['aid_e'][$m]['nom'];
							}

							// 20130927 : cell_ajustee() ou pas sur le nom de matière/enseignement
							if((isset($tab_modele_pdf["cell_ajustee_texte_matiere"][$classe_id]))&&($tab_modele_pdf["cell_ajustee_texte_matiere"][$classe_id]==1)) {
								// Encadrement
								$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_entre_matier, "",'LRBT',1,'L');

								// cell_ajustee() ne centre pas verticalement le texte.
								// On met un décalage pour ne pas coller le texte à la bordure
								$Y_decal_cell_ajustee=1;
								// On repositionne et on inscrit le nom de matière sur la moitié de la hauteur de la cellule
								$pdf->SetXY($X_bloc_matiere, $Y_decal+$Y_decal_cell_ajustee);

								$texte=$info_nom_matiere;
								$taille_max_police=$hauteur_caractere_matiere;
								$taille_min_police=ceil($taille_max_police/$tab_modele_pdf["cell_ajustee_texte_matiere_ratio_min_max"][$classe_id]);

								$largeur_dispo=$tab_modele_pdf["largeur_matiere"][$classe_id]-2;
								$h_cell=$espace_entre_matier/2-$Y_decal_cell_ajustee;

								cell_ajustee("<b>".$texte."</b>",$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');

							}
							else {
								$val = $pdf->GetStringWidth($info_nom_matiere);
								$taille_texte = $tab_modele_pdf["largeur_matiere"][$classe_id] - 2;
								$grandeur_texte='test';
								while($grandeur_texte!='ok') {
									if($taille_texte<$val)
									{
										$hauteur_caractere_matiere = $hauteur_caractere_matiere-0.3;
										$pdf->SetFont('DejaVu','B',$hauteur_caractere_matiere);
										$val = $pdf->GetStringWidth($info_nom_matiere);
									}
									else {
										$grandeur_texte='ok';
									}
								}
								$grandeur_texte='test';
								$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_entre_matier/2, ($info_nom_matiere),'LR',1,'L');
							}
							$Y_decal = $Y_decal+($espace_entre_matier/2);
							$pdf->SetXY($X_bloc_matiere, $Y_decal);
							$pdf->SetFont('DejaVu','',8);

							fich_debug_bull("\$info_nom_matiere=$info_nom_matiere\n");
							fich_debug_bull("\$Y_decal=$Y_decal\n");

							// nom des professeurs

							if ( isset($tab_bull['eleve'][$i]['aid_e'][$m]['aid_prof_resp_login'][0]) )
							{

								$nb_prof_matiere = count($tab_bull['eleve'][$i]['aid_e'][$m]['aid_prof_resp_login']);
								$espace_matiere_prof = $espace_entre_matier/2;
								if($nb_prof_matiere>0){
									$espace_matiere_prof = $espace_matiere_prof/$nb_prof_matiere;
								}
								$nb_pass_count = '0';
								$text_prof = '';
								while ($nb_prof_matiere > $nb_pass_count)
								{
									$tmp_login_prof=$tab_bull['eleve'][$i]['aid_e'][$m]['aid_prof_resp_login'][$nb_pass_count];
									$text_prof=affiche_utilisateur($tmp_login_prof,$tab_bull['eleve'][$i]['id_classe']);

									if ( $nb_prof_matiere <= 2 ) { $hauteur_caractere_prof = 8; }
									elseif ( $nb_prof_matiere == 3) { $hauteur_caractere_prof = 5; }
									elseif ( $nb_prof_matiere > 3) { $hauteur_caractere_prof = 2; }
									$pdf->SetFont('DejaVu','',$hauteur_caractere_prof);
									$val = $pdf->GetStringWidth($text_prof);
									$taille_texte = ($tab_modele_pdf["largeur_matiere"][$classe_id]);
									$grandeur_texte='test';
									while($grandeur_texte!='ok') {
										if($taille_texte<$val)
										{
											$hauteur_caractere_prof = $hauteur_caractere_prof-0.3;
											$pdf->SetFont('DejaVu','',$hauteur_caractere_prof);
											$val = $pdf->GetStringWidth($text_prof);
										}
										else {
											$grandeur_texte='ok';
										}
									}
									$grandeur_texte='test';
									$pdf->SetX($X_bloc_matiere);
									if( empty($tab_bull['eleve'][$i]['aid_e'][$m]['aid_prof_resp_login'][$nb_pass_count+1]) ) {
										$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_matiere_prof, ($text_prof),'LRB',1,'L');
									}
									if( !empty($tab_bull['eleve'][$i]['aid_e'][$m]['aid_prof_resp_login'][$nb_pass_count+1]) ) {
										$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_matiere_prof, ($text_prof),'LR',1,'L');
									}
									$nb_pass_count = $nb_pass_count + 1;
								}
							}
							$largeur_utilise = $tab_modele_pdf["largeur_matiere"][$classe_id];

							// coefficient matière
							if($tab_modele_pdf["active_coef_moyenne"][$classe_id]==='1') {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
								$pdf->SetFont('DejaVu','',10);
								//$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $espace_entre_matier, $tab_bull['eleve'][$i]['coef_eleve'][$i][$m],1,0,'C');
								$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $espace_entre_matier, '',1,0,'C');
								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_coef_moyenne"][$classe_id];
							}

							//permet le calcul total des coefficients
							// if(empty($moyenne_min[$id_classe][$id_periode])) {
								//$total_coef_en_calcul=$total_coef_en_calcul+$tab_bull['eleve'][$i]['coef_eleve'][$i][$m];
							//}

							// nombre de note
							// 20081118
							//if($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1') {
							if(($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1')&&($tab_modele_pdf["active_nombre_note"][$classe_id]!='1')) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
								$pdf->SetFont('DejaVu','',10);
								//$valeur = $tab_bull['eleve'][$i]['nbct'][$m][$i] . "/" . $tab_bull['eleve'][$i]['groupe'][$m]['nbct'];
								$valeur = '';
								$pdf->Cell($tab_modele_pdf["largeur_nombre_note"][$classe_id], $espace_entre_matier, $valeur,1,0,'C');
								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_nombre_note"][$classe_id];
							}

							// les moyennes eleve, classe, min, max
							$cpt_ordre = 0;
							while (!empty($ordre_moyenne[$cpt_ordre]) ) {
								//eleve
								if($tab_modele_pdf["active_moyenne_eleve"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'eleve' ) {
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									$pdf->SetFont('DejaVu','B',10);
									$pdf->SetFillColor($tab_modele_pdf["couleur_reperage_eleve1"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve2"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve3"][$classe_id]);

									// calcul nombre de sous affichage

									$nb_sousaffichage='1';
									//20090908 if(empty($active_coef_sousmoyene)) { $active_coef_sousmoyene = ''; }

									if($tab_modele_pdf["active_coef_sousmoyene"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; }
									if($tab_modele_pdf["active_nombre_note"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; }
									if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') { if($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }
									if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') { if($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }
									if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') { if($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }

									// On filtre si la moyenne est vide, on affiche seulement un tiret
									if ($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note']=="-") {
										$valeur = "-";
									}
									elseif ($tab_bull['eleve'][$i]['aid_e'][$m]['aid_statut']!="") {
										if($tab_bull['eleve'][$i]['aid_e'][$m]['aid_statut']=="other") {
											$valeur = "-";
										}
										else {
											$valeur=$tab_bull['eleve'][$i]['aid_e'][$m]['aid_statut'];
										}
									}
									else {
										$valeur = present_nombre($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note'], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
									}
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, $valeur,1,2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
									$valeur = "";

									if($tab_modele_pdf["active_coef_sousmoyene"][$classe_id]==='1') {
										$pdf->SetFont('DejaVu','I',7);
										//$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'coef. '.$tab_bull['eleve'][$i]['coef_eleve'][$i][$m],'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, '','LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
									}

									if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') {
										// On affiche toutes les moyennes dans la même colonne
										$pdf->SetFont('DejaVu','I',7);
										if($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1') {
											//if ($tab_bull['eleve'][$i]['moy_classe_grp'][$m]=="-") {
											if (($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_moyenne']=="-")||($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_moyenne']=="")) {
												$valeur = "-";
											}
											else {
												$valeur = present_nombre($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_moyenne'], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
											}
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'cla.'.$valeur,'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										}

										if($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1') {
											//if ($tab_bull['eleve'][$i]['moy_min_classe_grp'][$m]=="-") {
											if (($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_min']=="-")||($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_min']=="")) {
												$valeur = "-";
											} else {
												$valeur = present_nombre($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_min'], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
											}
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'min.'.$valeur,'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										}

										if($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1') {
											//if ($tab_bull['eleve'][$i]['moy_max_classe_grp'][$m]=="-") {
											if (($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_max']=="-")||($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_max']=="")) {
												$valeur = "-";
											} else {
												$valeur = present_nombre($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_max'], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
											}
											$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'max.'.$valeur,'LRD',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
											$valeur = ''; // on remet à vide.
										}
									}

									if($tab_modele_pdf["active_nombre_note"][$classe_id]==='1') {
										$pdf->SetFont('DejaVu','I',7);
										$espace_pour_nb_note = $espace_entre_matier/$nb_sousaffichage;
										$espace_pour_nb_note = $espace_pour_nb_note / 2;
										$valeur1 = ''; $valeur2 = '';
										/*
										if ($tab_bull['eleve'][$i]['nbct'][$m][$i]!= 0 ) {
											$valeur1 = $tab_bull['eleve'][$i]['nbct'][$m][$i].' note';
											if($tab_bull['eleve'][$i]['nbct'][$m][$i]>1){$valeur1.='s';}
											$valeur2 = 'sur '.$tab_bull['eleve'][$i]['groupe'][$m]['nbct'];
										}
										*/
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_pour_nb_note, $valeur1, 'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_pour_nb_note, $valeur2, 'LRB',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										$valeur1 = ''; $valeur2 = '';
									}
									$pdf->SetFont('DejaVu','',10);
									$pdf->SetFillColor(0, 0, 0);
									$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];

								} // Fin affichage élève

								//classe
								if( $tab_modele_pdf["active_moyenne_classe"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' ) {
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									//if ($tab_bull['eleve'][$i]['moy_classe_grp'][$m]=="-") {
									if (($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_moyenne']=="-")||($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_moyenne']=="")) {
										$valeur = "-";
									} else {
										$valeur = present_nombre($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_moyenne'], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
									}
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
									$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
								}
								//min
								if( $tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' ) {
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									$pdf->SetFont('DejaVu','',8);
									//if ($tab_bull['eleve'][$i]['moy_min_classe_grp'][$m]=="-") {
									if (($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_min']=="-")||($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_min']=="")) {
										$valeur = "-";
									} else {
										$valeur = present_nombre($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_min'], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
									}
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
									$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
								}
								//max
								if( $tab_modele_pdf["active_moyenne_max"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' ) {
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									//if ($tab_bull['eleve'][$i]['moy_max_classe_grp'][$m]== "-") {
									if (($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_max']=="-")||($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_max']=="")) {
										$valeur = "-";
									} else {
										$valeur = present_nombre($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note_max'], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
									}
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
									$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
								}
								//$largeur_utilise = $largeur_utilise+$largeur_moyenne;


								// rang de l'élève
								if($tab_modele_pdf["active_rang"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'rang' ) {
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									$pdf->SetFont('DejaVu','',8);
									// A REVOIR: J'AI l'EFFECTIF DU GROUPE, mais faut-il compter les élèves ABS, DISP,...?
									//if((isset($tab_bull['eleve'][$i]['rang'][$i][$m]))&&(isset($tab_bull['eleve'][$i]['groupe'][$m]['effectif']))) {
										//$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $espace_entre_matier, $tab_bull['eleve'][$i]['rang'][$i][$m].'/'.$tab_bull['eleve'][$i]['groupe'][$m]['effectif'],1,0,'C');

									//if((isset($tab_bull['eleve'][$i]['rang'][$m][$i]))&&(isset($tab_bull['eleve'][$i]['groupe'][$m]['effectif']))) {
									//	$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $espace_entre_matier, $tab_bull['eleve'][$i]['rang'][$m][$i].'/'.$tab_bull['eleve'][$i]['groupe'][$m]['effectif_avec_note'],1,0,'C');
									//}
									//else {
										$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $espace_entre_matier, '',1,0,'C');
									//}
									$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_rang"][$classe_id];
								}

								// graphique de niveau
								if($tab_modele_pdf["active_graphique_niveau"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'niveau' ) {
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									$pdf->SetFont('DejaVu','',10);
									//$id_groupe_graph = $tab_bull['eleve'][$i]['groupe'][$m]['id'];
									// placement de l'élève dans le graphique de niveau

									// AJOUT: La variable n'était pas initialisée dans le bulletin_pdf_avec_modele...
									$place_eleve='';

									//if ($tab_bull['eleve'][$i]['note'][$m][$i]!="") {
									if ($tab_bull['eleve'][$i]['aid_e'][$m]['aid_note']!="") {
										/*
										if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<5) { $place_eleve=5;}
										if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=5) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<8))  { $place_eleve=4;}
										if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=8) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<10)) { $place_eleve=3;}
										if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=10) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<12)) {$place_eleve=2;}
										if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=12) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<15)) { $place_eleve=1;}
										if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=15) { $place_eleve=0;}
										*/
										if(isset($tab_bull['eleve'][$i]['aid_e'][$m]['place_eleve'])) {
											//$place_eleve=$tab_bull['eleve'][$i]['aid_e'][$m]['place_eleve'];
											$place_eleve=$tab_bull['eleve'][$i]['aid_e'][$m]['place_eleve']-1;
										}
									}
									$data_grap=array();
									if(isset($tab_bull['eleve'][$i]['aid_e'][$m]['quartile1_classe'])) {$data_grap[0]=$tab_bull['eleve'][$i]['aid_e'][$m]['quartile1_classe'];}
									if(isset($tab_bull['eleve'][$i]['aid_e'][$m]['quartile2_classe'])) {$data_grap[1]=$tab_bull['eleve'][$i]['aid_e'][$m]['quartile2_classe'];}
									if(isset($tab_bull['eleve'][$i]['aid_e'][$m]['quartile3_classe'])) {$data_grap[2]=$tab_bull['eleve'][$i]['aid_e'][$m]['quartile3_classe'];}
									if(isset($tab_bull['eleve'][$i]['aid_e'][$m]['quartile4_classe'])) {$data_grap[3]=$tab_bull['eleve'][$i]['aid_e'][$m]['quartile4_classe'];}
									if(isset($tab_bull['eleve'][$i]['aid_e'][$m]['quartile5_classe'])) {$data_grap[4]=$tab_bull['eleve'][$i]['aid_e'][$m]['quartile5_classe'];}
									if(isset($tab_bull['eleve'][$i]['aid_e'][$m]['quartile6_classe'])) {$data_grap[5]=$tab_bull['eleve'][$i]['aid_e'][$m]['quartile6_classe'];}								//if (array_sum($data_grap[$id_periode][$id_groupe_graph]) != 0) {
									if (array_sum($data_grap) != 0) {
										//$pdf->DiagBarre($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2), $tab_modele_pdf["largeur_niveau"][$classe_id], $espace_entre_matier, $data_grap[$id_periode][$id_groupe_graph], $place_eleve);
										$pdf->DiagBarre($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2), $tab_modele_pdf["largeur_niveau"][$classe_id], $espace_entre_matier, $data_grap, $place_eleve);
									}
									$place_eleve=''; // on vide la variable
									$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_niveau"][$classe_id];
								}

								//appréciation
								if($tab_modele_pdf["active_appreciation"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'appreciation' ) {
									// 20160623
									if((!getSettingAOui('bullNoSaisieElementsProgrammes'))&&($tab_modele_pdf["active_colonne_Elements_Programmes"][$classe_id]==='1')) {
										$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
										$pdf->SetFont('DejaVu','',10);

										$texte_Elements_Programmes="";

										$pdf->Cell($tab_modele_pdf["largeur_Elements_Programmes"][$classe_id], $espace_entre_matier, ($texte_Elements_Programmes),'LRB',0,'C');
										$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_Elements_Programmes"][$classe_id];
									}

									// si on autorise l'affichage des sous matière et s'il y en a alors on les affiche
									//$id_groupe_select = $tab_bull['eleve'][$i]['groupe'][$m]['id'];
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									$X_sous_matiere = 0;
									$largeur_sous_matiere=0;

									/*
									if($tab_modele_pdf["autorise_sous_matiere"][$classe_id]==='1' and !empty($tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_nom'])) {
										$X_sous_matiere = $X_note_moy_app+$largeur_utilise;
										$Y_sous_matiere = $Y_decal-($espace_entre_matier/2);
										$n=0;
										$largeur_texte_sousmatiere=0; $largeur_sous_matiere=0;
										while( !empty($tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_nom'][$n]) )
										{
											$pdf->SetFont('DejaVu','',8);
											$largeur_texte_sousmatiere = $pdf->GetStringWidth($tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_nom'][$n].': '.$tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_note'][$n]);
											if($largeur_sous_matiere<$largeur_texte_sousmatiere) { $largeur_sous_matiere=$largeur_texte_sousmatiere; }
											$n = $n + 1;
										}
										if($largeur_sous_matiere!='0') { $largeur_sous_matiere = $largeur_sous_matiere + 2; }
										$n=0;
										while( !empty($tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_nom'][$n]) )
										{
											$pdf->SetXY($X_sous_matiere, $Y_sous_matiere);
											$pdf->SetFont('DejaVu','',8);
											$pdf->Cell($largeur_sous_matiere, $espace_entre_matier/count($tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_nom']), ($tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_nom'][$n].': '.$tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_note'][$n]),1,0,'L');
											$Y_sous_matiere = $Y_sous_matiere+$espace_entre_matier/count($tab_bull['eleve'][$i]['groupe'][$m][$i]['cn_nom']);
											$n = $n + 1;
										}
										$largeur_utilise = $largeur_utilise+$largeur_sous_matiere;
									}
									*/
									$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
									// calcul de la taille du texte des appréciation
									$hauteur_caractere_appreciation = 9;
									$pdf->SetFont('DejaVu','',$hauteur_caractere_appreciation);

									//suppression des espace en début et en fin
									//$app_aff = trim($tab_bull['eleve'][$i]['aid_e'][$m]['aid_appreciation']);
									$app_aff="";
									if($tab_bull['eleve'][$i]['aid_e'][$m]['message']!='') {
										$app_aff.=$tab_bull['eleve'][$i]['aid_e'][$m]['message'];
									}
									//if($app_aff!='') {$app_aff.=" ";}
									//if($tab_bull['eleve'][$i]['aid_e'][$m]['aid_nom']!='') {
									if(($tab_bull['eleve'][$i]['aid_e'][$m]['display_nom']=='y')&&($tab_bull['eleve'][$i]['aid_e'][$m]['aid_nom']!='')) {
										if($app_aff!='') {$app_aff.=" ";}
										$app_aff.=$tab_bull['eleve'][$i]['aid_e'][$m]['aid_nom'];
									}
									if($app_aff!='') {$app_aff.="\n";}
									$app_aff.=trim($tab_bull['eleve'][$i]['aid_e'][$m]['aid_appreciation']);

									fich_debug_bull("__________________________________________\n");
									fich_debug_bull("$app_aff\n");
									fich_debug_bull("__________________________________________\n");

									// DEBUT AJUSTEMENT TAILLE APPRECIATION
									$taille_texte_total = $pdf->GetStringWidth($app_aff);
									$largeur_appreciation2 = $largeur_appreciation - $largeur_sous_matiere;

									if($use_cell_ajustee=="n") {
										//$taille_texte = (($espace_entre_matier/3)*$largeur_appreciation2);
										$nb_ligne_app = '2.8';
										//$nb_ligne_app = '3.8';
										//$nb_ligne_app = '4.8';
										$taille_texte_max = $nb_ligne_app * ($largeur_appreciation2-4);
										//$taille_texte_max = $nb_ligne_app * ($largeur_appreciation2);
										$grandeur_texte='test';
	
										fich_debug_bull("\$taille_texte_total=$taille_texte_total\n");
										fich_debug_bull("\$largeur_appreciation2=$largeur_appreciation2\n");
										fich_debug_bull("\$nb_ligne_app=$nb_ligne_app\n");
										//fich_debug_bull("\$taille_texte_max = \$nb_ligne_app * (\$largeur_appreciation2-4)=$nb_ligne_app * ($largeur_appreciation2-4)=$taille_texte_max\n");
										fich_debug_bull("\$taille_texte_max = \$nb_ligne_app * (\$largeur_appreciation2)=$nb_ligne_app * ($largeur_appreciation2)=$taille_texte_max\n");
	
										while($grandeur_texte!='ok') {
											if($taille_texte_max < $taille_texte_total)
											{
												$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.3;
												//$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.1;
												$pdf->SetFont('DejaVu','',$hauteur_caractere_appreciation);
												$taille_texte_total = $pdf->GetStringWidth($app_aff);
											}
											else {
												$grandeur_texte='ok';
											}
										}
										$grandeur_texte='test';
										$pdf->drawTextBox(($app_aff), $largeur_appreciation2, $espace_entre_matier, 'J', 'M', 1);
									}
									else {
										$texte=$app_aff;
										//$texte="Bla bla\nbli ".$app_aff;
										$taille_max_police=$hauteur_caractere_appreciation;
										$taille_min_police=ceil($taille_max_police/3);
	
										$largeur_dispo=$largeur_appreciation2;
										$h_cell=$espace_entre_matier;

										if(getSettingValue('suppr_balises_app_prof')=='y') {$texte=preg_replace('/<(.*)>/U','',$texte);}
										cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'LRBT');
									}

									$pdf->SetFont('DejaVu','',10);
									$largeur_utilise = $largeur_utilise + $largeur_appreciation2;
									//$largeur_utilise = 0;
								}

								$cpt_ordre = $cpt_ordre + 1;
							}
							$largeur_utilise = 0;
							// fin de boucle d'ordre
							$Y_decal = $Y_decal+($espace_entre_matier/2);
						//}
					}
				}
				// FIN DES AID AFFICHéS APRES LES MATIERES
				//======================================================

				fich_debug_bull("Apres les AID_E: \$Y_decal=$Y_decal\n");


				//echo "\$tab_modele_pdf['active_moyenne'][$classe_id]=".$tab_modele_pdf["active_moyenne"][$classe_id]."<br />";

				// Ligne moyenne générale
				// bas du tableau des notes et app si les moyennes générales ne sont pas affichées, le bas du tableau ne sera pas affiché
				if ( $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne_general"][$classe_id] === '1' ) {
					$X_note_moy_app = $tab_modele_pdf["X_note_app"][$classe_id];
					$Y_note_moy_app = $tab_modele_pdf["Y_note_app"][$classe_id]+$tab_modele_pdf["hauteur_note_app"][$classe_id]-$tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id];

					if ($affiche_deux_moy_gen==1) {
						// On a réservé une ligne de plus pour la moyenne générale avec coefficients 1
						$Y_note_moy_app-=$tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id];
					}

					$pdf->SetXY($X_note_moy_app, $Y_note_moy_app);
					$pdf->SetFont('DejaVu','',10);
					$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
					//================
					// Ajout: J.Etheve
					if ($affiche_deux_moy_gen==1) {
						$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], ("Moy.gén.coef."),1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
					}
					else {
						$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], ("Moyenne générale"),1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
					}
					//================
					$largeur_utilise = $tab_modele_pdf["largeur_matiere"][$classe_id];

					// coefficient matière
					//echo "\$tab_modele_pdf['active_coef_moyenne'][$classe_id]=".$tab_modele_pdf["active_coef_moyenne"][$classe_id]."<br />";
					if($tab_modele_pdf["active_coef_moyenne"][$classe_id]==='1') {
						$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
						$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);

						//$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], "",1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);

						$pdf->SetFont('DejaVu','',10);
						//echo "\$tab_modele_pdf['affiche_totalpoints_sur_totalcoefs'][$classe_id]=".$tab_modele_pdf["affiche_totalpoints_sur_totalcoefs"][$classe_id]."<br />\n";
						if($tab_modele_pdf["affiche_totalpoints_sur_totalcoefs"][$classe_id]=='1') {
							$info_tot_et_coef=$tab_bull['tot_points_eleve'][$i]."/".$tab_bull['total_coef_eleve'][$i];
						}
						elseif($tab_modele_pdf["affiche_totalpoints_sur_totalcoefs"][$classe_id]=='2') {
							$info_tot_et_coef=$tab_bull['total_coef_eleve'][$i];
						}
						else {
							$info_tot_et_coef='-';
						}

						$val = $pdf->GetStringWidth($info_tot_et_coef);
						$taille_texte = $tab_modele_pdf["largeur_coef_moyenne"][$classe_id];
						$grandeur_texte='test';
						$hauteur_tmp=10;
						while($grandeur_texte!='ok') {
							if($taille_texte<$val)
							{
								$hauteur_tmp=$hauteur_tmp-0.3;
								$pdf->SetFont('DejaVu','',$hauteur_tmp);
								$val = $pdf->GetStringWidth($info_tot_et_coef);
							}
							else {
								$grandeur_texte='ok';
							}
						}
						$grandeur_texte='test';

						$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $info_tot_et_coef,1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);

						$pdf->SetFont('DejaVu','',10);

						$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_coef_moyenne"][$classe_id];
					}

					// nombre de notes
					if(($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1')&&($tab_modele_pdf["active_nombre_note"][$classe_id]!='1')) {
						$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
						$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
						$pdf->Cell($tab_modele_pdf["largeur_nombre_note"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], "",1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
						$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_nombre_note"][$classe_id];
					}

					$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);

					$cpt_ordre = 0;
					while ( !empty($ordre_moyenne[$cpt_ordre]) ) {
						// eleve
						if($tab_modele_pdf["active_moyenne_eleve"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'eleve' ) {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
							$pdf->SetFont('DejaVu','B',10);
							$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);

							// On a deux paramètres de couleur qui se croisent. On utilise une variable tierce.
							$utilise_couleur = $tab_modele_pdf["couleur_moy_general"][$classe_id];
							if($tab_modele_pdf["active_reperage_eleve"][$classe_id]==='1') {
								// Si on affiche une couleur spécifique pour les moyennes de l'élève,
								// on utilise cette couleur ici aussi, quoi qu'il arrive
								$pdf->SetFillColor($tab_modele_pdf["couleur_reperage_eleve1"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve2"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve3"][$classe_id]);
								$utilise_couleur = 1;
							}

							if(($tab_bull['moy_gen_eleve'][$i]=="")||($tab_bull['moy_gen_eleve'][$i]=="-")) {
								$val_tmp="-";
							}
							else {
								$val_tmp=present_nombre(preg_replace("/,/",'.',$tab_bull['moy_gen_eleve'][$i]), $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
							}
							$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $val_tmp,1,0,'C',$utilise_couleur);

							$pdf->SetFont('DejaVu','',10);
							$pdf->SetFillColor(0, 0, 0);
							$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
						}

						// classe
						// 20141122
						if(($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' )) {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
							$pdf->SetFont('DejaVu','',8);
							$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);

							/*
							if( $total_coef_en_calcul != 0){
								$moyenne_classe = $total_moyenne_classe_en_calcul / $total_coef_en_calcul;
							}
							else{
								$moyenne_classe = '-';
							}
							*/
							if(($tab_bull['moy_generale_classe']=="")||($tab_bull['moy_generale_classe']=="-")) {
								$moyenne_classe = '-';
							}
							else{
								$moyenne_classe = present_nombre(preg_replace("/,/",'.',$tab_bull['moy_generale_classe']), $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
							}

							if ( $moyenne_classe != '-' ) {
								//$moyenne_classe=$tab_bull['moy_generale_classe'];
								$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $moyenne_classe,1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
							} else {
								$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], '-',1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
							}
							$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
						}

						// min
						if(($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' )) {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
							$pdf->SetFont('DejaVu','',8);
							$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);

							/*
							if($total_coef_en_calcul != 0 and $tab_modele_pdf["affiche_moyenne_mini_general"][$classe_id] === '1' ){
								$moyenne_min = $total_moyenne_min_en_calcul / $total_coef_en_calcul;
							}
							else{
								$moyenne_min = '-';
							}
							*/

							if ($tab_bull['moy_min_classe']!='-') {
								//$moyenne_min=$tab_moy_min_classe[$classe_id][$id_periode];
								$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], present_nombre(preg_replace("/,/",'.',$tab_bull['moy_min_classe']), $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]),1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
							} else {
								$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], '-',1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
							}
							$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
						}

						// max
						if(($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]!=1)&&($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' )) {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
							$pdf->SetFont('DejaVu','',8);
							$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);

							/*
							if($total_coef_en_calcul != 0 and $tab_modele_pdf["affiche_moyenne_maxi_general"][$classe_id] === '1' ){
								$moyenne_max = $total_moyenne_max_en_calcul / $total_coef_en_calcul;
							} else {
								$moyenne_max = '-';
							}
							*/

							if ($tab_bull['moy_max_classe']!='-') {
								$moyenne_max=$tab_bull['moy_max_classe'];
								$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], present_nombre(preg_replace("/,/",'.',$tab_bull['moy_max_classe']), $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]),1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
							} else {
								$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], '-',1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
							}
							$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
						}

						// rang de l'élève (pour la ligne Moyenne générale)
						if($tab_modele_pdf["active_rang"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'rang') {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
							$pdf->SetFont('DejaVu','',8);
							$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
							if ($tab_bull['rang_classe'][$i]!= 0) {
								$rang_a_afficher=$tab_bull['rang_classe'][$i].'/'.$tab_bull['eff_classe'];
							} else {
								$rang_a_afficher = "";
							}
							$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $rang_a_afficher ,'TLRB',0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
							$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_rang"][$classe_id];
						}

						// graphique de niveau
						if($tab_modele_pdf["active_graphique_niveau"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'niveau' ) {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
							$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
							// placement de l'élève dans le graphique de niveau
							//if ($tab_bull['moy_gen_eleve'][$i]!="") {
							if (($tab_bull['moy_gen_eleve'][$i]!="")&&($tab_bull['moy_gen_eleve'][$i]!="-")) {
								/*
								if ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<5) { $place_eleve=5;}
								if (($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=5) and ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<8))  { $place_eleve=4;}
								if (($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=8) and ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<10)) { $place_eleve=3;}
								if (($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=10) and ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<12)) {$place_eleve=2;}
								if (($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=12) and ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<15)) { $place_eleve=1;}
								if ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=15) { $place_eleve=0;}
								*/
								//$place_eleve=$tab_bull['place_eleve_classe'][$i];
								$place_eleve=$tab_bull['place_eleve_classe'][$i]-1;
							}
							$data_grap_classe[0]=$tab_bull['quartile1_classe_gen'];
							$data_grap_classe[1]=$tab_bull['quartile2_classe_gen'];
							$data_grap_classe[2]=$tab_bull['quartile3_classe_gen'];
							$data_grap_classe[3]=$tab_bull['quartile4_classe_gen'];
							$data_grap_classe[4]=$tab_bull['quartile5_classe_gen'];
							$data_grap_classe[5]=$tab_bull['quartile6_classe_gen'];

							if (array_sum($data_grap_classe) != 0) {
								//$pdf->DiagBarre($X_note_moy_app+$largeur_utilise, $Y_note_moy_app, $tab_modele_pdf["largeur_niveau"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $data_grap_classe[$id_periode][$id_classe_selection], $place_eleve);
								$pdf->DiagBarre($X_note_moy_app+$largeur_utilise, $Y_note_moy_app, $tab_modele_pdf["largeur_niveau"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $data_grap_classe, $place_eleve);
							}
							$place_eleve=''; // on vide la variable
							$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_niveau"][$classe_id];
						}
						//appréciation
						if($tab_modele_pdf["active_appreciation"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'appreciation' ) {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
							$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
							$pdf->Cell($largeur_appreciation, $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], '','TLRB',0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
							$largeur_utilise = $largeur_utilise + $largeur_appreciation;
						}
						$cpt_ordre = $cpt_ordre + 1;
					}
					$largeur_utilise = 0;
					// fin de boucle d'ordre
					$pdf->SetFillColor(0, 0, 0);
				}

				//================
				// Ajout: J.Etheve
				// *****------------------------------------ ajout moyenne générale non coefficientée
				if ($affiche_deux_moy_gen==1) {
					// Ligne moyenne générale coefficientée
					//bas du tableau des note et app si les affichage des moyennes ne sont pas affiché le bas du tableau ne seras pas affiché
				
					if ( $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne_general"][$classe_id] === '1' ) {
						$X_note_moy_app = $tab_modele_pdf["X_note_app"][$classe_id];
						$Y_note_moy_app = $tab_modele_pdf["Y_note_app"][$classe_id]+$tab_modele_pdf["hauteur_note_app"][$classe_id];//-$tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id];

						// On a remonté d'une ligne la moyenne générale classique
						$Y_note_moy_app-=$tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id];

						$pdf->SetXY($X_note_moy_app, $Y_note_moy_app);
						$pdf->SetFont('DejaVu','',10);
						$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
						$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], ("Moy.gén.non coef."),1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
						$largeur_utilise = $tab_modele_pdf["largeur_matiere"][$classe_id];
	
						// coefficient matière
						if($tab_modele_pdf["active_coef_moyenne"][$classe_id]==='1') {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
							$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);

							//$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], "",1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);

							$pdf->SetFont('DejaVu','',10);
							$info_tot_et_coef=$tab_bull['tot_points_eleve1'][$i]."/".$tab_bull['total_coef_eleve1'][$i];
							$val = $pdf->GetStringWidth($info_tot_et_coef);
							$taille_texte = $tab_modele_pdf["largeur_coef_moyenne"][$classe_id];
							$grandeur_texte='test';
							$hauteur_tmp=10;
							while($grandeur_texte!='ok') {
								if($taille_texte<$val)
								{
									$hauteur_tmp=$hauteur_tmp-0.3;
									$pdf->SetFont('DejaVu','',$hauteur_tmp);
									$val = $pdf->GetStringWidth($info_tot_et_coef);
								}
								else {
									$grandeur_texte='ok';
								}
							}
							$grandeur_texte='test';
	
							$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $info_tot_et_coef,1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
	
							$pdf->SetFont('DejaVu','',10);


							$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_coef_moyenne"][$classe_id];
						}
	
						// nombre de note
						// 20081118
						//if($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1') {
						if(($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1')&&($tab_modele_pdf["active_nombre_note"][$classe_id]!='1')) {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
							$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
							$pdf->Cell($tab_modele_pdf["largeur_nombre_note"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], "",1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
							$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_nombre_note"][$classe_id];
						}
	
						$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
	
						$cpt_ordre = 0;
						while ( !empty($ordre_moyenne[$cpt_ordre]) ) {
							//eleve
							if($tab_modele_pdf["active_moyenne_eleve"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'eleve' ) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
								$pdf->SetFont('DejaVu','B',10);
								$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
	
								// On a deux paramètres de couleur qui se croisent. On utilise une variable tierce.
								$utilise_couleur = $tab_modele_pdf["couleur_moy_general"][$classe_id];
								if($tab_modele_pdf["active_reperage_eleve"][$classe_id]==='1') {
									// Si on affiche une couleur spécifique pour les moyennes de l'élève,
									// on utilise cette couleur ici aussi, quoi qu'il arrive
									$pdf->SetFillColor($tab_modele_pdf["couleur_reperage_eleve1"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve2"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve3"][$classe_id]);
									$utilise_couleur = 1;
								}
	
								if(($tab_bull['moy_gen_eleve_noncoef'][$i]=="")||($tab_bull['moy_gen_eleve_noncoef'][$i]=="-")) {
									$val_tmp="-";
								}
								else {
									//$val_tmp=present_nombre($tab_bull['moy_gen_eleve'][$i], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
									//$val_tmp=$tab_bull['moy_gen_eleve'][$i];
									$val_tmp=present_nombre(preg_replace("/,/",'.',$tab_bull['moy_gen_eleve_noncoef'][$i]), $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
									/*
									$tmp_fich=fopen("/tmp/test_moy_gen.txt","a+");
									fwrite($tmp_fich,$tab_bull['eleve'][$i]['login']." present_nombre(\$tab_bull['moy_gen_eleve'][$i], \$tab_modele_pdf[\"arrondie_choix\"][$classe_id], \$tab_modele_pdf[\"nb_chiffre_virgule\"][$classe_id], \$tab_modele_pdf[\"chiffre_avec_zero\"][$classe_id])=present_nombre(".$tab_bull['moy_gen_eleve'][$i].", ".$tab_modele_pdf["arrondie_choix"][$classe_id].", ".$tab_modele_pdf["nb_chiffre_virgule"][$classe_id].",". $tab_modele_pdf["chiffre_avec_zero"][$classe_id].")=".present_nombre($tab_bull['moy_gen_eleve'][$i], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id])."\n");
	
									fwrite($tmp_fich,$tab_bull['eleve'][$i]['login']." present_nombre(preg_replace("/,/",'.',\$tab_bull['moy_gen_eleve'][$i]), \$tab_modele_pdf[\"arrondie_choix\"][$classe_id], \$tab_modele_pdf[\"nb_chiffre_virgule\"][$classe_id], \$tab_modele_pdf[\"chiffre_avec_zero\"][$classe_id])=".present_nombre(preg_replace("/,/",'.',$tab_bull['moy_gen_eleve'][$i]), $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id])."\n");
	
									fclose($tmp_fich);
									*/
								}
	
								//$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], present_nombre($tab_bull['moy_gen_eleve'][$i], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]),1,0,'C',$utilise_couleur);
								$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $val_tmp,1,0,'C',$utilise_couleur);
	
								$pdf->SetFont('DejaVu','',10);
								$pdf->SetFillColor(0, 0, 0);
								$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
							}
	
							//classe
							if($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' ) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
								$pdf->SetFont('DejaVu','',8);
								$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
	
								/*
								if( $total_coef_en_calcul != 0){
									$moyenne_classe = $total_moyenne_classe_en_calcul / $total_coef_en_calcul;
								}
								else{
									$moyenne_classe = '-';
								}
								*/
								if(($tab_bull['moy_generale_classe_noncoef']=="")||($tab_bull['moy_generale_classe_noncoef']=="-")) {
									$moyenne_classe = '-';
								}
								else{
									$moyenne_classe = present_nombre(preg_replace("/,/",'.',$tab_bull['moy_generale_classe_noncoef']), $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
								}
	
								if ( $moyenne_classe != '-' ) {
									//$moyenne_classe=$tab_bull['moy_generale_classe'];
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $moyenne_classe,1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
								} else {
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], '-',1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
								}
								$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
							}
	
							//min
							if($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' ) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
								$pdf->SetFont('DejaVu','',8);
								$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
	
								/*
								if($total_coef_en_calcul != 0 and $tab_modele_pdf["affiche_moyenne_mini_general"][$classe_id] === '1' ){
									$moyenne_min = $total_moyenne_min_en_calcul / $total_coef_en_calcul;
								}
								else{
									$moyenne_min = '-';
								}
								*/
	
								if ($tab_bull['moy_min_classe']!='-') {
									//$moyenne_min=$tab_moy_min_classe[$classe_id][$id_periode];
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], present_nombre(preg_replace("/,/",'.',$tab_bull['moy_min_classe_noncoef']), $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]),1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
								} else {
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], '-',1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
								}
								$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
							}
	
							//max
							if($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' ) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
								$pdf->SetFont('DejaVu','',8);
								$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
	
								/*
								if($total_coef_en_calcul != 0 and $tab_modele_pdf["affiche_moyenne_maxi_general"][$classe_id] === '1' ){
									$moyenne_max = $total_moyenne_max_en_calcul / $total_coef_en_calcul;
								} else {
									$moyenne_max = '-';
								}
								*/
	
								if ($tab_bull['moy_max_classe_noncoef']!='-') {
									$moyenne_max=$tab_bull['moy_max_classe_noncoef'];
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], present_nombre(preg_replace("/,/",'.',$tab_bull['moy_max_classe_noncoef']), $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]),1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
								} else {
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], '-',1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
								}
								$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
							}
	
							// rang de l'élève
							if($tab_modele_pdf["active_rang"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'rang') {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
								$pdf->SetFont('DejaVu','',8);
								$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
								if ($tab_bull['rang_classe'][$i]!= 0) {
									$rang_a_afficher=$tab_bull['rang_classe'][$i].'/'.$tab_bull['eff_classe'];
								} else {
									$rang_a_afficher = "";
								}
								$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $rang_a_afficher ,'TLRB',0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
								$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_rang"][$classe_id];
							}
	
							// graphique de niveau
							if($tab_modele_pdf["active_graphique_niveau"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'niveau' ) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
								$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
								// placement de l'élève dans le graphique de niveau
								//if ($tab_bull['moy_gen_eleve'][$i]!="") {
								if (($tab_bull['moy_gen_eleve'][$i]!="")&&($tab_bull['moy_gen_eleve'][$i]!="-")) {
									/*
									if ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<5) { $place_eleve=5;}
									if (($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=5) and ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<8))  { $place_eleve=4;}
									if (($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=8) and ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<10)) { $place_eleve=3;}
									if (($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=10) and ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<12)) {$place_eleve=2;}
									if (($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=12) and ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<15)) { $place_eleve=1;}
									if ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=15) { $place_eleve=0;}
									*/
									//$place_eleve=$tab_bull['place_eleve_classe'][$i];
									$place_eleve=$tab_bull['place_eleve_classe'][$i]-1;
								}
								$data_grap_classe[0]=$tab_bull['quartile1_classe_gen'];
								$data_grap_classe[1]=$tab_bull['quartile2_classe_gen'];
								$data_grap_classe[2]=$tab_bull['quartile3_classe_gen'];
								$data_grap_classe[3]=$tab_bull['quartile4_classe_gen'];
								$data_grap_classe[4]=$tab_bull['quartile5_classe_gen'];
								$data_grap_classe[5]=$tab_bull['quartile6_classe_gen'];
	
								if (array_sum($data_grap_classe) != 0) {
									//$pdf->DiagBarre($X_note_moy_app+$largeur_utilise, $Y_note_moy_app, $tab_modele_pdf["largeur_niveau"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $data_grap_classe[$id_periode][$id_classe_selection], $place_eleve);
									$pdf->DiagBarre($X_note_moy_app+$largeur_utilise, $Y_note_moy_app, $tab_modele_pdf["largeur_niveau"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $data_grap_classe, $place_eleve);
								}
								$place_eleve=''; // on vide la variable
								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_niveau"][$classe_id];
							}
							//appréciation
							if($tab_modele_pdf["active_appreciation"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'appreciation' ) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
								$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
								$pdf->Cell($largeur_appreciation, $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], '','TLRB',0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
								$largeur_utilise = $largeur_utilise + $largeur_appreciation;
							}
							$cpt_ordre = $cpt_ordre + 1;
						}
						$largeur_utilise = 0;
						// fin de boucle d'ordre
						$pdf->SetFillColor(0, 0, 0);
					}
				}
				// *****-------------------------------------fin moyenne générale non coefficientée ---
				//================



			}


				// =============== bloc absence ==================
				if($tab_modele_pdf["active_bloc_absence"][$classe_id]==='1') {
					$pdf->SetXY($tab_modele_pdf["X_absence"][$classe_id], $tab_modele_pdf["Y_absence"][$classe_id]);
					$origine_Y_absence = $tab_modele_pdf["Y_absence"][$classe_id];
					$pdf->SetFont('DejaVu','I',8);
					$info_absence='';

					if($tab_modele_pdf["afficher_abs_tot"][$classe_id]=='1') {
						if($tab_bull['eleve'][$i]['eleve_absences'] != '?') {
							if($tab_bull['eleve'][$i]['eleve_absences'] == '0')
							{
								$info_absence="<i>Aucune demi-journée d'absence</i>.";
							} else {
								$info_absence="<i>Nombre de demi-journées d'absence ";

								if($tab_modele_pdf["afficher_abs_nj"][$classe_id]=='1') {
									if ($tab_bull['eleve'][$i]['eleve_nj'] == '0' or $tab_bull['eleve'][$i]['eleve_nj'] == '?') {
										$info_absence = $info_absence."justifiées ";
									}

									$info_absence = $info_absence.": </i><b>".$tab_bull['eleve'][$i]['eleve_absences']."</b>";
									if ($tab_bull['eleve'][$i]['eleve_nj'] != '0' and $tab_bull['eleve'][$i]['eleve_nj'] != '?')
									{
										$info_absence = $info_absence." (dont <b>".$tab_bull['eleve'][$i]['eleve_nj']."</b> non justifiée";
										if ($tab_bull['eleve'][$i]['eleve_nj'] != '1') { $info_absence = $info_absence."s"; }
										$info_absence = $info_absence.")";
									}
								}
								else {
									$info_absence = $info_absence.": </i><b>".$tab_bull['eleve'][$i]['eleve_absences']."</b>";
								}
								$info_absence = $info_absence.".";
							}
						}
					}
					elseif($tab_modele_pdf["afficher_abs_nj"][$classe_id]=='1') {
						if($tab_bull['eleve'][$i]['eleve_nj'] == '0')
						{
							$info_absence="<i>Aucune absence non justifiée</i>.";
						} else {
							$info_absence="<i>Nombre de demi-journées d'absence non justifiées ";
							$info_absence.=": </i><b>".$tab_bull['eleve'][$i]['eleve_nj'].".</b>";
						}
					}

					if($tab_modele_pdf["afficher_abs_ret"][$classe_id]=='1') {
						if($tab_bull['eleve'][$i]['eleve_retards'] != '0' and $tab_bull['eleve'][$i]['eleve_retards'] != '?')
						{
							$info_absence = $info_absence."<i> Nombre de retards : </i><b>".$tab_bull['eleve'][$i]['eleve_retards'].".</b>";
						}
					}
					if(($tab_modele_pdf["afficher_abs_cpe"][$classe_id]=='1')&&(isset($tab_bull['eleve'][$i]['cperesp_civilite']))&&(isset($tab_bull['eleve'][$i]['cperesp_login']))) {
						$pdf->SetFont('DejaVu','',8);
						// C.P.E.
						$info_absence = $info_absence." (".ucfirst($gepi_cpe_suivi)." chargé";
						if($tab_bull['eleve'][$i]['cperesp_civilite']!="M.") {
							$info_absence = $info_absence."e";
						}
						/*
						$sql="SELECT civilite FROM utilisateurs WHERE login='".$cperesp_login[$i]."'";
						$res_civi=mysql_query($sql);
						if(mysql_num_rows($res_civi)>0){
							$lig_civi=mysql_fetch_object($res_civi);
							if($lig_civi->civilite!="M."){
								$info_absence = $info_absence."e";
							}
						}
						*/
						$info_absence = $info_absence." du suivi : <i>".affiche_utilisateur($tab_bull['eleve'][$i]['cperesp_login'],$tab_bull['id_classe'])."</i>)";
					}
					//$pdf->MultiCellTag($tab_modele_pdf["largeur_cadre_absences"][$classe_id], 5, ($info_absence), '', 'J', '');
					//$pdf->ext_MultiCellTag($tab_modele_pdf["largeur_cadre_absences"][$classe_id], 5, $info_absence, '', 'J', '');

					$taille_max_police=8;
					$taille_min_police=ceil($taille_max_police/3);
					$largeur_dispo=$tab_modele_pdf["largeur_cadre_absences"][$classe_id];
					$h_cell=5;
					cell_ajustee($info_absence,$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');

					//=========================
					// MODIF: boireaus 20081220
					// Désactivation de ce qui provoquait un décalage progressif du bloc Avis du conseil,...
					//if ( isset($Y_avis_cons_init) ) { $tab_modele_pdf["Y_avis_cons"][$classe_id] = $Y_avis_cons_init; }
					//if ( isset($Y_sign_chef_init) ) { $tab_modele_pdf["Y_sign_chef"][$classe_id] = $Y_sign_chef_init; }
					//=========================
					// Il y a une ligne pour les absences, retards,... donc on compte une ligne (+0.5) de décalage pour le bloc Avis du conseil et celui de la signature du chefetab
					//if ( !isset($Y_avis_cons_init) ) { $Y_avis_cons_init = $tab_modele_pdf["Y_avis_cons"][$classe_id] + 0.5; }
					//if ( !isset($Y_sign_chef_init) ) { $Y_sign_chef_init = $tab_modele_pdf["Y_sign_chef"][$classe_id] + 0.5; }
					$Y_avis_cons_init = $tab_modele_pdf["Y_avis_cons"][$classe_id] + 0.5;
					$Y_sign_chef_init = $tab_modele_pdf["Y_sign_chef"][$classe_id] + 0.5;

					//=========================
					// MODIF: boireaus 20081220
					// Désactivation de ce qui provoquait une réduction progressive de la hauteur du bloc Avis du conseil,...
					//if ( isset($hauteur_avis_cons_init) ) { $tab_modele_pdf["hauteur_avis_cons"][$classe_id] = $hauteur_avis_cons_init; }
					//if ( isset($hauteur_sign_chef_init) ) { $tab_modele_pdf["hauteur_sign_chef"][$classe_id] = $hauteur_sign_chef_init; }
					//=========================
					//if ( !isset($hauteur_avis_cons_init) ) { $hauteur_avis_cons_init = $tab_modele_pdf["hauteur_avis_cons"][$classe_id] - 0.5; }
					//if ( !isset($hauteur_sign_chef_init) ) { $hauteur_sign_chef_init = $tab_modele_pdf["hauteur_sign_chef"][$classe_id] - 0.5; }
					$hauteur_avis_cons_init = $tab_modele_pdf["hauteur_avis_cons"][$classe_id] - 0.5;
					$hauteur_sign_chef_init = $tab_modele_pdf["hauteur_sign_chef"][$classe_id] - 0.5;

	$hauteur_pris_app_abs=0;

					if($tab_bull['eleve'][$i]['appreciation_absences'] != "")
					{
						// supprimer les espaces
						$text_absences_appreciation = trim(str_replace(array("\r\n","\r","\n"), ' ', unhtmlentities($tab_bull['eleve'][$i]['appreciation_absences'])));
						$info_absence_appreciation = "<i>Avis ".ucfirst($gepi_cpe_suivi)." :</i> <b>".$text_absences_appreciation."</b>";
						$text_absences_appreciation = '';
						$pdf->SetXY($tab_modele_pdf["X_absence"][$classe_id], $tab_modele_pdf["Y_absence"][$classe_id]+4);
						$pdf->SetFont('DejaVu','',8);
						//$pdf->MultiCellTag(200, 3, ($info_absence_appreciation), '', 'J', '');
						//$pdf->MultiCellTag($tab_modele_pdf["largeur_cadre_absences"][$classe_id], 3, ($info_absence_appreciation), '', 'J', '');
						//$pdf->ext_MultiCellTag($tab_modele_pdf["largeur_cadre_absences"][$classe_id], 3, ($info_absence_appreciation), '', 'J', '');
						$val = $pdf->GetStringWidth($info_absence_appreciation);
						// nombre de lignes que prend la remarque cpe
						//Arrondi à l'entier supérieur : ceil()
						$nb_ligne = 1;
						$nb_ligne = ceil($val / 200);
						$hauteur_pris = $nb_ligne * 3;

						$taille_max_police=8;
						$taille_min_police=ceil($taille_max_police/3);
						$largeur_dispo=$tab_modele_pdf["largeur_cadre_absences"][$classe_id];
						$h_cell=$hauteur_pris;
						cell_ajustee($info_absence_appreciation,$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');

						$hauteur_pris_app_abs=$hauteur_pris;

						//$tab_modele_pdf["Y_avis_cons"][$classe_id] = $tab_modele_pdf["Y_avis_cons"][$classe_id] + $hauteur_pris;
						$Y_avis_cons_init=$Y_avis_cons_init+$hauteur_pris;

						//$tab_modele_pdf["hauteur_avis_cons"][$classe_id] = $tab_modele_pdf["hauteur_avis_cons"][$classe_id] - ( $hauteur_pris + 0.5 );
						$hauteur_avis_cons_init=$hauteur_avis_cons_init - ( $hauteur_pris + 0.5 );

						//$tab_modele_pdf["Y_sign_chef"][$classe_id] = $tab_modele_pdf["Y_sign_chef"][$classe_id] + $hauteur_pris;
						$Y_sign_chef_init=$Y_sign_chef_init+$hauteur_pris;

						//$tab_modele_pdf["hauteur_sign_chef"][$classe_id] = $tab_modele_pdf["hauteur_sign_chef"][$classe_id] - ( $hauteur_pris + 0.5 );
						$hauteur_sign_chef_init=$hauteur_sign_chef_init - ( $hauteur_pris + 0.5 );

						$hauteur_pris = 0;
					}
					else {
						if($Y_avis_cons_init!=$tab_modele_pdf["Y_avis_cons"][$classe_id])
						{
							//$tab_modele_pdf["Y_avis_cons"][$classe_id] = $tab_modele_pdf["Y_avis_cons"][$classe_id] - $hauteur_pris;
							$Y_avis_cons_init=$Y_avis_cons_init-$hauteur_pris;

							//$tab_modele_pdf["hauteur_avis_cons"][$classe_id] = $tab_modele_pdf["hauteur_avis_cons"][$classe_id] + $hauteur_pris;
							$hauteur_avis_cons_init=$hauteur_avis_cons_init + $hauteur_pris;

							//$tab_modele_pdf["Y_sign_chef"][$classe_id] = $tab_modele_pdf["Y_sign_chef"][$classe_id] - $hauteur_pris;
							$Y_sign_chef_init=$Y_sign_chef_init-$hauteur_pris;

							//$tab_modele_pdf["hauteur_sign_chef"][$classe_id] = $tab_modele_pdf["hauteur_sign_chef"][$classe_id] + $hauteur_pris;
							$hauteur_sign_chef_init=$hauteur_sign_chef_init + $hauteur_pris;

							$hauteur_pris = 0;
						}
					}
					$info_absence = '';
					$info_absence_appreciation = '';
					$pdf->SetFont('DejaVu','',10);
				}


				// sinon, si le bloc absence n'est pas activé
				if($tab_modele_pdf["active_bloc_absence"][$classe_id] != '1') {
					//=========================
					// MODIF: boireaus 20081220
					// Désactivation de ce qui provoquait un décalage progressif du bloc Avis du conseil,...
					//if ( isset($Y_avis_cons_init) ) { $tab_modele_pdf["Y_avis_cons"][$classe_id] = $Y_avis_cons_init; }
					//if ( isset($Y_sign_chef_init) ) { $tab_modele_pdf["Y_sign_chef"][$classe_id] = $Y_sign_chef_init; }
					//=========================
					//if ( !isset($Y_avis_cons_init) ) { $Y_avis_cons_init = $tab_modele_pdf["Y_avis_cons"][$classe_id]; }
					//if ( !isset($Y_sign_chef_init) ) { $Y_sign_chef_init = $tab_modele_pdf["Y_sign_chef"][$classe_id]; }
					$Y_avis_cons_init = $tab_modele_pdf["Y_avis_cons"][$classe_id];
					$Y_sign_chef_init = $tab_modele_pdf["Y_sign_chef"][$classe_id];

					$hauteur_avis_cons_init = $tab_modele_pdf["hauteur_avis_cons"][$classe_id] - 0.5;
					$hauteur_sign_chef_init = $tab_modele_pdf["hauteur_sign_chef"][$classe_id] - 0.5;
				}
				// fin

				//=========================
				// MODIF: boireaus 20081220
				/*
				if($Y_avis_cons_init!=$tab_modele_pdf["Y_avis_cons"][$classe_id]) {
					$Y_avis_cons[$classe_id] = $tab_modele_pdf["Y_avis_cons"][$classe_id] + 0.5;
					$Y_sign_chef[$classe_id] = $tab_modele_pdf["Y_sign_chef"][$classe_id] + 0.5;
				}
				*/
				//=========================

			// 20160408
				//if($tab_modele_pdf["active_bloc_orientation"][$classe_id]==='1') {
				//if($tab_modele_pdf["active_bloc_orientation"][$classe_id]==1) {
				$tmp_tab_periode_orientation=explode(";", $tab_modele_pdf["orientation_periodes"][$classe_id]);
				//if(in_array($tab_bull['num_periode'], $tmp_tab_periode_orientation)) {
				if((in_array($tab_bull['num_periode'], $tmp_tab_periode_orientation))&&(isset($tab_bull['orientation']['mef_avec_orientation']))&&(in_array($tab_bull['eleve'][$i]['mef_code'], $tab_bull['orientation']['mef_avec_orientation']))) {
					//$tab_bulletin[$id_classe][$periode_num]['orientation']['mef_avec_orientation']=get_tab_mef_avec_proposition_orientation();

					// LE TEMPS DU DEVELOPPEMENT, pour ne pas devoir adapter un modèle PDF, on prend la hauteur orientation sur la hauteur avis en mettant
					// INSERT INTO setting SET name='bull_pdf_orientation_pris_sur_avis', value='y';
	//				if((!getSettingAOui('bull_pdf_orientation_pris_sur_avis'))||
	//				($hauteur_avis_cons_init-$tab_modele_pdf["hauteur_cadre_orientation"][$classe_id]>10)) {
						//$tab_modele_pdf["active_bloc_avis_conseil"][$classe_id]

						//===========================
						// Pour prendre la hauteur du cadre orientation sur la hauteur du cadre avis
	//					if(getSettingAOui('bull_pdf_orientation_pris_sur_avis')) {
							$Y_avis_cons_init+=$tab_modele_pdf["hauteur_cadre_orientation"][$classe_id];
							$Y_sign_chef_init+=$tab_modele_pdf["hauteur_cadre_orientation"][$classe_id];

							$hauteur_avis_cons_init-=$tab_modele_pdf["hauteur_cadre_orientation"][$classe_id];
							$hauteur_sign_chef_init-=$tab_modele_pdf["hauteur_cadre_orientation"][$classe_id];
	//					}
						//===========================

						// Pour avoir une petite marge haute sur les listes de voeux/orientations dans le cadre orientation
						$padding_haut_orientation=1;

						$pdf->Rect($tab_modele_pdf["X_cadre_orientation"][$classe_id], $tab_modele_pdf["Y_cadre_orientation"][$classe_id], $tab_modele_pdf["largeur_cadre_orientation"][$classe_id], $tab_modele_pdf["hauteur_cadre_orientation"][$classe_id], 'D');

						if($tab_modele_pdf["cadre_voeux_orientation"][$classe_id]!=0) {

							$largeur_cadre_voeux=$tab_modele_pdf["largeur_cadre_orientation"][$classe_id];
							if($tab_modele_pdf["cadre_orientation_proposee"][$classe_id]!=0) {

								if($tab_modele_pdf["X_cadre_orientation_proposee"][$classe_id]>$tab_modele_pdf["X_cadre_voeux_orientation"][$classe_id]) {
									$largeur_cadre_voeux=$tab_modele_pdf["X_cadre_orientation_proposee"][$classe_id]-$tab_modele_pdf["X_cadre_voeux_orientation"][$classe_id];
								}
								else {
									$largeur_cadre_voeux=$tab_modele_pdf["largeur_cadre_orientation"][$classe_id]-($tab_modele_pdf["X_cadre_voeux_orientation"][$classe_id]-$tab_modele_pdf["X_cadre_orientation_proposee"][$classe_id]);
								}
							}

							$pdf->Rect($tab_modele_pdf["X_cadre_voeux_orientation"][$classe_id], $tab_modele_pdf["Y_cadre_orientation"][$classe_id], $largeur_cadre_voeux, $tab_modele_pdf["hauteur_cadre_orientation"][$classe_id], 'D');

							$pdf->SetXY($tab_modele_pdf["X_cadre_voeux_orientation"][$classe_id],$tab_modele_pdf["Y_cadre_orientation"][$classe_id]);
							$pdf->SetFont('DejaVu','B',10);
							$chaine_titre_voeux=$tab_modele_pdf["titre_voeux_orientation"][$classe_id]." : ";
							$largeur_chaine_titre_voeux=$pdf->GetStringWidth($chaine_titre_voeux);
							$pdf->Cell($largeur_chaine_titre_voeux,5, $chaine_titre_voeux,0,2,'');

							// Liste des voeux (pouvoir limiter aux N premiers voeux)
							$pdf->SetXY($tab_modele_pdf["X_cadre_voeux_orientation"][$classe_id]+$largeur_chaine_titre_voeux, $tab_modele_pdf["Y_cadre_orientation"][$classe_id]+$padding_haut_orientation);

							if($use_cell_ajustee=="n") {
								$texte_voeux="";
								if(isset($tab_bull['orientation']['voeux'][$tab_bull['eleve'][$i]['login']])) {
									for($loop_voeu=1;$loop_voeu<=count($tab_bull['orientation']['voeux'][$tab_bull['eleve'][$i]['login']]);$loop_voeu++) {
										$texte_voeux.=$loop_voeu.". ".$tab_bull['orientation']['voeux'][$tab_bull['eleve'][$i]['login']][$loop_voeu]['designation'];
										if(($tab_bull['orientation']['voeux'][$tab_bull['eleve'][$i]['login']][$loop_voeu]['commentaire']!="")&&($tab_bull['orientation']['voeux'][$tab_bull['eleve'][$i]['login']][$loop_voeu]['commentaire']!=$tab_bull['orientation']['voeux'][$tab_bull['eleve'][$i]['login']][$loop_voeu]['designation'])) {
											$texte_voeux.=" (".$tab_bull['orientation']['voeux'][$tab_bull['eleve'][$i]['login']][$loop_voeu]['commentaire'].")";
										}
										$texte_voeux.="\n";
									}
								}

								//$pdf->drawTextBox(($texte_voeux), $largeur_cadre_voeux-($pdf->GetX()-$tab_modele_pdf["X_cadre_voeux_orientation"][$classe_id]), $tab_modele_pdf["hauteur_cadre_orientation"][$classe_id], 'J', 'M', 0);
								$pdf->drawTextBox(($texte_voeux), $largeur_cadre_voeux-$largeur_chaine_titre_voeux, $tab_modele_pdf["hauteur_cadre_orientation"][$classe_id], 'J', 'M', 0);
							}
							else {
								$texte_voeux="";
								if(isset($tab_bull['orientation']['voeux'][$tab_bull['eleve'][$i]['login']])) {
									for($loop_voeu=1;$loop_voeu<=count($tab_bull['orientation']['voeux'][$tab_bull['eleve'][$i]['login']]);$loop_voeu++) {
										$texte_voeux.="<b>".$loop_voeu.".</b> ".$tab_bull['orientation']['voeux'][$tab_bull['eleve'][$i]['login']][$loop_voeu]['designation'];
										if(($tab_bull['orientation']['voeux'][$tab_bull['eleve'][$i]['login']][$loop_voeu]['commentaire']!="")&&($tab_bull['orientation']['voeux'][$tab_bull['eleve'][$i]['login']][$loop_voeu]['commentaire']!=$tab_bull['orientation']['voeux'][$tab_bull['eleve'][$i]['login']][$loop_voeu]['designation'])) {
											$texte_voeux.=" (".$tab_bull['orientation']['voeux'][$tab_bull['eleve'][$i]['login']][$loop_voeu]['commentaire'].")";
										}
										$texte_voeux.="\n";
									}
								}

								$texte=$texte_voeux;
								$taille_max_police=10;
								$taille_min_police=ceil($taille_max_police/3);

								//$largeur_dispo=$largeur_cadre_voeux-($pdf->GetX()-$tab_modele_pdf["X_cadre_voeux_orientation"][$classe_id]);
								$largeur_dispo=$largeur_cadre_voeux-$largeur_chaine_titre_voeux;
								$h_cell=$tab_modele_pdf["hauteur_cadre_orientation"][$classe_id]-$padding_haut_orientation;

								cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'','T');
							}
						}

						// Orientations proposées
						if($tab_modele_pdf["cadre_orientation_proposee"][$classe_id]!=0) {
							$largeur_cadre_orientation_proposee=$tab_modele_pdf["largeur_cadre_orientation"][$classe_id];
							if($tab_modele_pdf["cadre_voeux_orientation"][$classe_id]!=0) {

								if($tab_modele_pdf["X_cadre_orientation_proposee"][$classe_id]>$tab_modele_pdf["X_cadre_voeux_orientation"][$classe_id]) {
									$largeur_cadre_orientation_proposee=$tab_modele_pdf["largeur_cadre_orientation"][$classe_id]-($tab_modele_pdf["X_cadre_orientation_proposee"][$classe_id]-$tab_modele_pdf["X_cadre_voeux_orientation"][$classe_id]);
								}
								else {
									$largeur_cadre_orientation_proposee=$tab_modele_pdf["X_cadre_voeux_orientation"][$classe_id]-$tab_modele_pdf["X_cadre_orientation_proposee"][$classe_id];
								}
							}

							$pdf->Rect($tab_modele_pdf["X_cadre_orientation_proposee"][$classe_id], $tab_modele_pdf["Y_cadre_orientation"][$classe_id], $largeur_cadre_orientation_proposee, $tab_modele_pdf["hauteur_cadre_orientation"][$classe_id], 'D');

							$pdf->SetXY($tab_modele_pdf["X_cadre_orientation_proposee"][$classe_id],$tab_modele_pdf["Y_cadre_orientation"][$classe_id]);
							$pdf->SetFont('DejaVu','B',10);
							//$pdf->Cell(50,5, $tab_modele_pdf["titre_orientation_proposee"][$classe_id]." : ",0,2,'');
							$chaine_titre_orientations_proposees=$tab_modele_pdf["titre_orientation_proposee"][$classe_id]." : ";
							$largeur_chaine_titre_orientations_proposees=$pdf->GetStringWidth($chaine_titre_orientations_proposees);
							$pdf->Cell($chaine_titre_orientations_proposees,5, $chaine_titre_orientations_proposees,0,2,'');

							$chaine_titre_avis_orientations_proposees=$tab_modele_pdf["titre_avis_orientation_proposee"][$classe_id];

							// Liste des orientations proposées (pouvoir limiter aux N premières)
							$pdf->SetXY($tab_modele_pdf["X_cadre_orientation_proposee"][$classe_id]+$largeur_chaine_titre_orientations_proposees, $tab_modele_pdf["Y_cadre_orientation"][$classe_id]+$padding_haut_orientation);

							if($use_cell_ajustee=="n") {
								$texte_orientations_proposees="";
								if(isset($tab_bull['orientation']['orientation_proposee'][$tab_bull['eleve'][$i]['login']])) {
									for($loop_op=1;$loop_op<=count($tab_bull['orientation']['orientation_proposee'][$tab_bull['eleve'][$i]['login']]);$loop_op++) {
										$texte_orientations_proposees.=$loop_op.". ".$tab_bull['orientation']['orientation_proposee'][$tab_bull['eleve'][$i]['login']][$loop_op]['designation'];
										if(($tab_bull['orientation']['orientation_proposee'][$tab_bull['eleve'][$i]['login']][$loop_op]['commentaire']!="")&&($tab_bull['orientation']['orientation_proposee'][$tab_bull['eleve'][$i]['login']][$loop_op]['commentaire']!=$tab_bull['orientation']['orientation_proposee'][$tab_bull['eleve'][$i]['login']][$loop_op]['designation'])) {
											$texte_orientations_proposees.=" (".$tab_bull['orientation']['orientation_proposee'][$tab_bull['eleve'][$i]['login']][$loop_op]['commentaire'].")";
										}
										$texte_orientations_proposees.="\n";
									}
								}

								if((isset($tab_bull['orientation']['avis'][$tab_bull['eleve'][$i]['login']]))&&($tab_bull['orientation']['avis'][$tab_bull['eleve'][$i]['login']]!="")) {
									$texte_orientations_proposees.=$chaine_titre_avis_orientations_proposees." : ".preg_replace("#<br />#i", "", $tab_bull['orientation']['avis'][$tab_bull['eleve'][$i]['login']]);
								}

								$pdf->drawTextBox(($texte_orientations_proposees), $largeur_cadre_orientation_proposee-$largeur_chaine_titre_orientations_proposees, $tab_modele_pdf["hauteur_cadre_orientation"][$classe_id], 'J', 'M', 0);
							}
							else {
								$texte_orientations_proposees="";
								if(isset($tab_bull['orientation']['orientation_proposee'][$tab_bull['eleve'][$i]['login']])) {
									for($loop_op=1;$loop_op<=count($tab_bull['orientation']['orientation_proposee'][$tab_bull['eleve'][$i]['login']]);$loop_op++) {
										$texte_orientations_proposees.="<b>".$loop_op.".</b> ".$tab_bull['orientation']['orientation_proposee'][$tab_bull['eleve'][$i]['login']][$loop_op]['designation'];
										if(($tab_bull['orientation']['orientation_proposee'][$tab_bull['eleve'][$i]['login']][$loop_op]['commentaire']!="")&&($tab_bull['orientation']['orientation_proposee'][$tab_bull['eleve'][$i]['login']][$loop_op]['commentaire']!=$tab_bull['orientation']['orientation_proposee'][$tab_bull['eleve'][$i]['login']][$loop_op]['designation'])) {
											$texte_orientations_proposees.=" (".$tab_bull['orientation']['orientation_proposee'][$tab_bull['eleve'][$i]['login']][$loop_op]['commentaire'].")";
										}
										$texte_orientations_proposees.="\n";
									}
								}

								if((isset($tab_bull['orientation']['avis'][$tab_bull['eleve'][$i]['login']]))&&($tab_bull['orientation']['avis'][$tab_bull['eleve'][$i]['login']]!="")) {
									$texte_orientations_proposees.="<b>".$chaine_titre_avis_orientations_proposees." :</b> ".preg_replace("#<br />#i", "", $tab_bull['orientation']['avis'][$tab_bull['eleve'][$i]['login']]);
								}

								$texte=$texte_orientations_proposees;
								$taille_max_police=10;
								$taille_min_police=ceil($taille_max_police/3);

								//$largeur_dispo=$largeur_cadre_orientation_proposee-($pdf->GetX()-$tab_modele_pdf["X_cadre_orientation_proposee"][$classe_id]);
								$largeur_dispo=$largeur_cadre_orientation_proposee-$largeur_chaine_titre_orientations_proposees;
								$h_cell=$tab_modele_pdf["hauteur_cadre_orientation"][$classe_id]-$padding_haut_orientation;

								cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'','T');
							}

						}

						//$pdf->SetXY($tab_modele_pdf["X_avis_cons"][$classe_id],$Y_avis_cons_init);
					/*
					}
					else {
						$pdf->SetXY(5,$Y_avis_cons_init);
						$pdf->SetFont('DejaVu','',5);
						$pdf->Cell(150,5, "Manque de place pour faire tenir avis et orientation",0,2,'');
					}
					*/
				}

				// ================ bloc avis du conseil de classe =================
				if($tab_modele_pdf["active_bloc_avis_conseil"][$classe_id]==='1') {
					if($tab_modele_pdf["cadre_avis_cons"][$classe_id]!=0) {
						//$pdf->Rect($tab_modele_pdf["X_avis_cons"][$classe_id], $tab_modele_pdf["Y_avis_cons"][$classe_id], $tab_modele_pdf["longeur_avis_cons"][$classe_id], $tab_modele_pdf["hauteur_avis_cons"][$classe_id], 'D');
						$pdf->Rect($tab_modele_pdf["X_avis_cons"][$classe_id], $Y_avis_cons_init, $tab_modele_pdf["longeur_avis_cons"][$classe_id], $hauteur_avis_cons_init, 'D');
					}
					//$pdf->SetXY($tab_modele_pdf["X_avis_cons"][$classe_id],$tab_modele_pdf["Y_avis_cons"][$classe_id]);
					$pdf->SetXY($tab_modele_pdf["X_avis_cons"][$classe_id],$Y_avis_cons_init);

					if ( $tab_modele_pdf["taille_titre_bloc_avis_conseil"][$classe_id] != '' and $tab_modele_pdf["taille_titre_bloc_avis_conseil"][$classe_id] < '15' ) {
						$taille = $tab_modele_pdf["taille_titre_bloc_avis_conseil"][$classe_id];
					} else {
						$taille = '10';
					}
					$pdf->SetFont('DejaVu','I',$taille);
					if ( $tab_modele_pdf["titre_bloc_avis_conseil"][$classe_id] != '' ) {
						$tt_avis = $tab_modele_pdf["titre_bloc_avis_conseil"][$classe_id];
					} else {
						$tt_avis = 'Avis du Conseil de classe :';
					}
					$pdf->Cell($tab_modele_pdf["longeur_avis_cons"][$classe_id],5, $tt_avis,0,2,'');

					//$pdf->SetXY($tab_modele_pdf["X_avis_cons"][$classe_id]+2.5,$tab_modele_pdf["Y_avis_cons"][$classe_id]+5);
					$pdf->SetXY($tab_modele_pdf["X_avis_cons"][$classe_id]+2.5,$Y_avis_cons_init+5);

					$pdf->SetFont('DejaVu','',10);
					$texteavis = $tab_bull['avis'][$i];
					// ***** AJOUT POUR LES MENTIONS *****
					//$textmention = $tab_bull['id_mention'][$i];

					if((!isset($tableau_des_mentions_sur_le_bulletin))||(!is_array($tableau_des_mentions_sur_le_bulletin))||(count($tableau_des_mentions_sur_le_bulletin)==0)) {
						$tableau_des_mentions_sur_le_bulletin=get_mentions($classe_id);
					}

					if(isset($tableau_des_mentions_sur_le_bulletin[$tab_bull['id_mention'][$i]])) {
						$textmention=$tableau_des_mentions_sur_le_bulletin[$tab_bull['id_mention'][$i]];
					}
					else {$textmention="-";}
					// ***** FIN DE L'AJOUT POUR LES MENTIONS *****

					//$avec_coches_mentions="y";
					//if($avec_coches_mentions=="y") {
					if($tab_modele_pdf["affich_coches_mentions"][$classe_id]!="n") {
						//$marge_droite_avis_cons=40;

						if(count($tableau_des_mentions_sur_le_bulletin)>0) {
							$marge_droite_avis_cons=40;
						}
						else {
							$marge_droite_avis_cons=5;
						}
					}
					else {
						$marge_droite_avis_cons=5;
						//if(($textmention!="")&&($textmention!="-")) {
						if(($tab_modele_pdf["affich_mentions"][$classe_id]!="n")&&($textmention!="")&&($textmention!="-")) {
							//$texteavis.="\n".traduction_mention($textmention);
							if($use_cell_ajustee=="n") {
								if($tab_modele_pdf["affich_intitule_mentions"][$classe_id]!="n") {
									$texteavis.="\n".ucfirst($gepi_denom_mention)." : ";
								}
								$texteavis.=$textmention;
							}
							else {
								if($tab_modele_pdf["affich_intitule_mentions"][$classe_id]!="n") {
									$texteavis.="\n"."<b>".ucfirst($gepi_denom_mention)." :</b> ";
								}
								$texteavis.=$textmention;
							}
						}
					}

					if($use_cell_ajustee=="n") {
						$pdf->drawTextBox(($texteavis), $tab_modele_pdf["longeur_avis_cons"][$classe_id]-$marge_droite_avis_cons, $hauteur_avis_cons_init-10, 'J', 'M', 0);
					}
					else {
						$texte=$texteavis;
						$taille_max_police=10;
						$taille_min_police=ceil($taille_max_police/3);

						$largeur_dispo=$tab_modele_pdf["longeur_avis_cons"][$classe_id]-$marge_droite_avis_cons;
						$h_cell=$hauteur_avis_cons_init-10;

						cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');
					}

					$X_pp_aff=$tab_modele_pdf["X_avis_cons"][$classe_id];

					//$Y_pp_aff=$tab_modele_pdf["Y_avis_cons"][$classe_id]+$tab_modele_pdf["hauteur_avis_cons"][$classe_id]-5;
					$Y_pp_aff=$Y_avis_cons_init+$hauteur_avis_cons_init-5;

					$pdf->SetXY($X_pp_aff,$Y_pp_aff);
					if ( $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id] != '' and is_numeric($tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id]) and $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id]>0 and $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id] < '15' ) {
						$taille = $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id];
					} else {
						$taille = '10';
					}
					$pdf->SetFont('DejaVu','I',$taille);
					// Le nom du professeur principal
					$pp_classe[$i]="";
					//if(isset($tab_bull['eleve'][$i]['pp']['login'])) {
					if($tab_modele_pdf["afficher_tous_profprincipaux"][$classe_id]==1) {
						$index_pp='pp_classe';
					}
					else {
						$index_pp='pp';
					}
					if(isset($tab_bull['eleve'][$i][$index_pp][0]['login'])) {
						$pp_classe[$i]="<b>".ucfirst($tab_bull['gepi_prof_suivi'])."</b> : ";
						$pp_classe[$i].="<i>".affiche_utilisateur($tab_bull['eleve'][$i][$index_pp][0]['login'],$tab_bull['eleve'][$i]['id_classe'])."</i>";
						for($i_pp=1;$i_pp<count($tab_bull['eleve'][$i][$index_pp]);$i_pp++) {
							$pp_classe[$i].=", ";
							$pp_classe[$i].="<i>".affiche_utilisateur($tab_bull['eleve'][$i][$index_pp][$i_pp]['login'],$tab_bull['eleve'][$i]['id_classe'])."</i>";
						}
					}
					else {
						$pp_classe[$i]="";
					}
					//$pdf->MultiCellTag(200, 5, ($pp_classe[$i]), '', 'J', '');
					//$pdf->ext_MultiCellTag(200, 5, ($pp_classe[$i]), '', 'J', '');

					$taille_max_police=$taille;
					$taille_min_police=ceil($taille_max_police/3);
					//$largeur_dispo=200;
					$largeur_dispo=$tab_modele_pdf["longeur_avis_cons"][$classe_id];
					$h_cell=5;
					cell_ajustee($pp_classe[$i],$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'');

				}

				//if($avec_coches_mentions=="y") {
				if($tab_modele_pdf["affich_coches_mentions"][$classe_id]!="n") {
					// ***** AJOUT POUR LES MENTIONS *****
					// Essai pour ajouter un bloc renseignant les mentions du CC
					// A COMPLETER...
					$pdf->SetFont('DejaVu','',9);
					$X_pp_aff=$tab_modele_pdf["X_avis_cons"][$classe_id]+$tab_modele_pdf["longeur_avis_cons"][$classe_id]-35;
					//$Y_pp_aff=$tab_modele_pdf["Y_avis_cons"][$classe_id]+5;
					$Y_pp_aff=$Y_avis_cons_init+5;
					$pdf->SetXY($X_pp_aff,$Y_pp_aff);

					/*
					$pdf->Cell(35,4, 'Félicitations      ',0,2,'R');
					$pdf->Cell(35,4, 'Mention honorable      ',0,2,'R');
					$pdf->Cell(35,4, 'Encouragements      ',0,2,'R');
					*/
					if((!isset($tableau_des_mentions_sur_le_bulletin))||(!is_array($tableau_des_mentions_sur_le_bulletin))||(count($tableau_des_mentions_sur_le_bulletin)==0)) {
						$tableau_des_mentions_sur_le_bulletin=get_mentions($classe_id);
					}

					//for($loop_mention=0;$loop_mention<count($tableau_des_mentions_sur_le_bulletin);$loop_mention++) {
					$loop_mention=0;
					foreach($tableau_des_mentions_sur_le_bulletin as $key_mention => $value_mention) {
						//$pdf->Cell(35,4, $value_mention,0,2,'R');
						$pdf->Cell(35,4, $value_mention,0,2,'L');
						$loop_mention++;
					}

					/*
					$pdf->Rect($X_pp_aff+30, $Y_pp_aff+0.3, 2.4, 3);
					$pdf->Rect($X_pp_aff+30, $Y_pp_aff+4.3, 2.4, 3);
					$pdf->Rect($X_pp_aff+30, $Y_pp_aff+8.3, 2.4, 3);
					$pdf->Rect($X_pp_aff, $Y_pp_aff+0.1, 0.01, 12);
					*/
					//for($loop_mention=0;$loop_mention<count($tableau_des_mentions_sur_le_bulletin);$loop_mention++) {
					$loop_mention=0;
					foreach($tableau_des_mentions_sur_le_bulletin as $key_mention => $value_mention) {
						$pdf->Rect($X_pp_aff+30, $Y_pp_aff+4*$loop_mention+0.3, 2.4, 3);

						if($key_mention==$tab_bull['id_mention'][$i]) {
							$pdf->SetXY($X_pp_aff-1.73,$Y_pp_aff+$loop_mention*4);
							$pdf->Cell(35,4, 'X',0,2,'R');
						}
						$loop_mention++;
					}
					$pdf->Rect($X_pp_aff, $Y_pp_aff+0.1, 0.01, $loop_mention*4);
	
					/*
					// Si félicitations (à modifier...)
					if($textmention=="F") {
						$pdf->SetXY($X_pp_aff-1.73,$Y_pp_aff);
						$pdf->Cell(35,4, 'X',0,2,'R');
					}
					// Si mention honorable (à modifier...)
					if($textmention=="M") {
						$pdf->SetXY($X_pp_aff-1.73,$Y_pp_aff+4);
						$pdf->Cell(35,4, 'X',0,2,'R');
					}
					// Si encouragements (à modifier...)
					if($textmention=="E") {
						$pdf->SetXY($X_pp_aff-1.73,$Y_pp_aff+8);
						$pdf->Cell(35,4, 'X',0,2,'R');
					}
					*/
					// Fin de l'essai
					// ***** FIN DE L'AJOUT POUR LES MENTIONS *****
				}

				// ======================= bloc du président du conseil de classe ================
				if( $tab_modele_pdf["active_bloc_chef"][$classe_id] === '1' ) {
					if( $tab_modele_pdf["cadre_sign_chef"][$classe_id] != 0 ) {
						//$pdf->Rect($tab_modele_pdf["X_sign_chef"][$classe_id], $tab_modele_pdf["Y_sign_chef"][$classe_id], $tab_modele_pdf["longeur_sign_chef"][$classe_id], $tab_modele_pdf["hauteur_sign_chef"][$classe_id], 'D');
						$pdf->Rect($tab_modele_pdf["X_sign_chef"][$classe_id], $Y_sign_chef_init, $tab_modele_pdf["longeur_sign_chef"][$classe_id], $hauteur_sign_chef_init, 'D');
					}
					//$pdf->SetXY($tab_modele_pdf["X_sign_chef"][$classe_id],$tab_modele_pdf["Y_sign_chef"][$classe_id]);
					$pdf->SetXY($tab_modele_pdf["X_sign_chef"][$classe_id],$Y_sign_chef_init);

					/*
					// 20120715
					// Si une image de signature doit être insérée...
					$tmp_fich=getSettingValue('fichier_signature');
					$fich_sign = '../backup/'.getSettingValue('backup_directory').'/'.$tmp_fich;
					//echo "\$fich_sign=$fich_sign<br />\n";
					if($tab_modele_pdf["signature_img"][$classe_id]==='1' and ($tmp_fich!='') and file_exists($fich_sign))
					{
						$sql="SELECT 1=1 FROM droits_acces_fichiers WHERE fichier='signature_img' AND ((identite='".$_SESSION['statut']."' AND type='statut') OR (identite='".$_SESSION['login']."' AND type='individu'))";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)>0) {
					*/

					if((isset($signature_bull[$tab_bull['id_classe']]))&&($signature_bull[$tab_bull['id_classe']]!="")&&(file_exists($signature_bull[$tab_bull['id_classe']]))) {
							$fich_sign=$signature_bull[$tab_bull['id_classe']];

							$X_sign = $tab_modele_pdf["X_sign_chef"][$classe_id];
							$Y_sign = $Y_sign_chef_init;

							$largeur_dispo=$tab_modele_pdf["longeur_sign_chef"][$classe_id]-10;
							//$hauteur_dispo=$hauteur_sign_chef_init-10;
							// On ajuste mieux la hauteur de l'image, quitte à ce que le tampon/signature soit en surimpression (ou plutôt sous-impression) avec le Nom du chef en première ligne du cadre.
							$hauteur_dispo=$hauteur_sign_chef_init-2;
							/*
							echo "\$tab_modele_pdf[\"longeur_sign_chef\"][$classe_id]=".$tab_modele_pdf["longeur_sign_chef"][$classe_id]."<br />\n";
							echo "\$hauteur_sign_chef_init=".$hauteur_sign_chef_init."<br />\n";

							$valeur=redimensionne_image($fich_sign, $largeur_dispo, );
							$L_sign = $valeur[0];
							$H_sign = $valeur[1];
							*/

							$tmp_dim_photo=getimagesize($fich_sign);
							$ratio_l=$tmp_dim_photo[0]/$largeur_dispo;
							$ratio_h=$tmp_dim_photo[1]/$hauteur_dispo;
							if($ratio_l>$ratio_h) {
								$L_sign = $largeur_dispo;
								$H_sign = $largeur_dispo*$tmp_dim_photo[1]/$tmp_dim_photo[0];
							}
							else {
								$H_sign = $hauteur_dispo;
								$L_sign = $hauteur_dispo*$tmp_dim_photo[0]/$tmp_dim_photo[1];
							}
							/*
							echo "\$X_sign=$X_sign<br />\n";
							echo "\$Y_sign=$Y_sign<br />\n";
							echo "\$L_sign=$L_sign<br />\n";
							echo "\$H_sign=$H_sign<br />\n";
							*/
							$X_sign += ($tab_modele_pdf["longeur_sign_chef"][$classe_id]-$L_sign) / 2;
							$Y_sign += ($hauteur_sign_chef_init-$H_sign) / 2;

							$tmp_dim_photo=getimagesize($fich_sign);

							if((isset($tmp_dim_photo[2]))&&($tmp_dim_photo[2]==2)) {
								//$pdf->Image($fich_sign, $X_sign, $Y_sign, $L_sign, $H_sign);
								$pdf->Image($fich_sign, round($X_sign), round($Y_sign), round($L_sign), round($H_sign));
							}
						//}
					}

					$pdf->SetFont('DejaVu','',10);
					if( $tab_modele_pdf["affichage_haut_responsable"][$classe_id] === '1' ) {
						if ( $tab_modele_pdf["affiche_fonction_chef"][$classe_id] === '1' ){
							if ( $tab_modele_pdf["taille_texte_fonction_chef"][$classe_id] != '' and $tab_modele_pdf["taille_texte_fonction_chef"][$classe_id] != '0' and $tab_modele_pdf["taille_texte_fonction_chef"][$classe_id] < '15' ) {
								$taille = $tab_modele_pdf["taille_texte_fonction_chef"][$classe_id];
							} else {
								$taille = '10';
							}
							$pdf->SetFont('DejaVu','B',$taille);
							$pdf->MultiCell($tab_modele_pdf["longeur_sign_chef"][$classe_id],5, ($tab_bull['formule']),0,2,'');
		                  $pdf->SetX($tab_modele_pdf["X_sign_chef"][$classe_id]);
						}
						if ( $tab_modele_pdf["taille_texte_identitee_chef"][$classe_id] != '' and $tab_modele_pdf["taille_texte_identitee_chef"][$classe_id] != '0' and $tab_modele_pdf["taille_texte_identitee_chef"][$classe_id] < '15' ) {
							$taille = $tab_modele_pdf["taille_texte_identitee_chef"][$classe_id];
						} else {
							$taille='8';
						}
						$pdf->SetFont('DejaVu','I',$taille);
						$pdf->MultiCell($tab_modele_pdf["longeur_sign_chef"][$classe_id],5, ($tab_bull['suivi_par']),0,2,'');
					} else {
						//$pdf->MultiCell($longeur_sign_chef[$classe_id],5, "Visa du Chef d'établissement\nou de son délégué",0,2,'');
						$pdf->MultiCell($tab_modele_pdf["longeur_sign_chef"][$classe_id],5, ("Visa du Chef d'établissement\nou de son délégué"),0,2,'');

						//$pdf->ext_MultiCell($tab_modele_pdf["longeur_sign_chef"][$classe_id],5, ("Visa du Chef d'établissement\nou de son délégué"),0,2,'');
					}
				}

	//		}

			// Insertion du relevé de notes si réclamé:
			/*
			if(count($tab_rel)!=0) {
				releve_pdf($tab_rel,$i);
			}
			*/
			if(count($tab_rel)!=0) {
				$temoin_releve_trouve='n';
				if(isset($tab_rel['eleve'])) {
					//echo "\$tab_bull['eleve'][$i]['login']=".$tab_bull['eleve'][$i]['login']."<br />";
					//for($k=0;$k<count($tab_rel['eleve']);$k++) {
					//for($k=0;$k<$tab_bull['eff_total_classe'];$k++) {
					for($k=0;$k<count($tab_rel['eleve']);$k++) {
						//echo "\$tab_rel['eleve'][$k]['login']=".$tab_rel['eleve'][$k]['login']."<br />";
						if(isset($tab_rel['eleve'][$k]['login'])) {
							if($tab_rel['eleve'][$k]['login']==$tab_bull['eleve'][$i]['login']) {
								releve_pdf($tab_rel,$k);
								$temoin_releve_trouve='y';
								break;
							}
						}
					}
				}
				/*
				else {
					echo "<p style='color:red;'>Il semble que le tableau des relevés de notes soit vide.</p>\n";
				}
				*/

				if($temoin_releve_trouve=='n') {
					$pdf->AddPage("P");
					$pdf->SetFontSize(10);
					$pdf->SetXY(20,20);
					$pdf->SetFont('DejaVu','B',14);
					$pdf->Cell(90,7,"Relevé de notes non trouvé pour ".my_strtoupper($tab_bull['eleve'][$i]['nom'])." ".casse_mot($tab_bull['eleve'][$i]['prenom'],'majf2'),0,2,'');

				}
			}

			if((isset($intercaler_app_classe))&&($intercaler_app_classe=="y")) {
				bulletin_pdf_classe($tab_bull, $i);
			}
		}
	}
}

?>
