<?php
/** Outils de manipulation des impressions PDF
 * 
 * $Id: share-pdf.inc.php 7692 2011-08-11 00:26:10Z regis $
 * 
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Initialisation
 * @subpackage pdf
 *
*/


/**
 * Classe pour préparer une impression PDF
 *
 * @global class $GLOBALS['pdf']
 * @name $pdf
 * @see PDF
 */
$GLOBALS['pdf'] = NULL;


/**
 * Ajuste la taille de la police
 * 
 * En cas de pb avec cell_ajustee1(), effectuer:
 * - INSERT INTO setting SET name='cell_ajustee_old_way', value='y';
 * - UPDATE setting SET value='y' WHERE name='cell_ajustee_old_way';
 *
 * @global class
 * @param type $texte
 * @param type $x
 * @param type $y
 * @param type $largeur_dispo
 * @param type $h_cell
 * @param type $hauteur_max_font
 * @param type $hauteur_min_font
 * @param type $bordure LRBT
 * @param type $v_align C(enter) ou T(op)
 * @param type $align
 * @param type $increment nombre dont on réduit la police à chaque essai
 * @param type $r_interligne proportion de la taille de police pour les interlignes
 * @see PDF
 * @see getSettingValue()
 * @see cell_ajustee0()
 * @see cell_ajustee1()
 */
function cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align='C',$align='L',$increment=0.3,$r_interligne=0.3) {
	global $pdf;

	if(getSettingValue('cell_ajustee_old_way')=='y') {
		// On vire les balises en utilisant l'ancienne fonction qui ne gérait pas les balises
		$texte=preg_replace('/<(.*)>/U','',$texte);
		cell_ajustee0($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align,$increment,$r_interligne);
	}
	else {
		cell_ajustee1($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align,$increment,$r_interligne);
	}
}


/**
 * Ajuste la taille de la police
 * 
 * @global class 
 * @global int 
 * @global string 
 * @param type $texte
 * @param type $x
 * @param type $y
 * @param type $largeur_dispo
 * @param type $h_cell
 * @param type $hauteur_max_font
 * @param type $hauteur_min_font
 * @param type $bordure LRBT
 * @param type $v_align C(enter) ou T(op)
 * @param type $align
 * @param type $increment nombre dont on réduit la police à chaque essai
 * @param type $r_interligne  proportion de la taille de police pour les interlignes
 * @see PDF
 */
function cell_ajustee1($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align='C',$align='L',$increment=0.3,$r_interligne=0.3) {
	
	// Pour que la variable puisse être récupérée dans la fonction my_echo_debug(), il faut la déclarer comme globale:
	global $pdf, $my_echo_debug, $mode_my_echo_debug;


	$texte=trim($texte);
	$hauteur_texte=$hauteur_max_font;

	//================================
	// Options de debug
	// Passer à 1 pour débugger
	$my_echo_debug=0;
	//$my_echo_debug=1;

	// Les modes sont 'fichier' ou n'importe quoi d'autre qui provoque des echo... donc un échec de la génération de PDF... à ouvrir avec un bloc-notes, pas avec un lecteur PDF
	// Voir la fonction my_echo_debug() pour l'emplacement du fichier généré
	$mode_my_echo_debug='fichier';
	//$mode_my_echo_debug='';
	//================================

	if($my_echo_debug==1) my_echo_debug("\n\n=========================================================\n");
	if($my_echo_debug==1) my_echo_debug("Lancement de\nmy_cell_ajustee(\$texte=$texte,\n\$x=$x,\n\$y=$y,\n\$largeur_dispo=$largeur_dispo,\n\$h_cell=$h_cell,\n\$hauteur_max_font=$hauteur_max_font,\n\$hauteur_min_font=$hauteur_min_font,\n\$bordure=$bordure,\n\$v_align=$v_align,\n\$align=$align,\n\$increment=$increment,\n\$r_interligne=$r_interligne)\n\n");

	if($my_echo_debug==1) my_echo_debug("\$texte=\"$texte\"\n");

	// Pour réduire la taille du texte, il se peut qu'il faille supprimer les balises,...
	$supprimer_balises="n";
	$supprimer_retours_a_la_ligne="n";
	$tronquer="n";

	// On commence par essayer de remplir la cellule avec la taille de police proposée
	// Et réduire la taille de police si cela ne tient pas.
	// Si on arrive à une taille de police trop faible, on va supprimer des retours à la ligne, des balises ou même tronquer.

	// Pour forcer en debug:
	//$tronquer='y';

	while($tronquer!='y') {
		// On (re)démarre un essai avec une taille de police

		$pdf->SetFontSize($hauteur_texte);

		// Nombre max de lignes avec la hauteur courante de police
		// Il manque l'interligne de bas de cellule
		$nb_max_lig=max(1,floor(($h_cell-$r_interligne*($hauteur_texte*26/100))/((1+$r_interligne)*($hauteur_texte*26/100))));
		
		if($my_echo_debug==1) my_echo_debug("\nOn lance un tour avec la taille de police:\n\$hauteur_texte=$hauteur_texte\n");
		if($my_echo_debug==1) my_echo_debug("\$nb_max_lig=$nb_max_lig\n");

		// Lignes dans la cellule
		unset($ligne);
		$ligne=array();

		// Compteur des lignes
		$cpt=0;

		// On prévoit deux... trois espaces de marge en gras pour s'assurer que la ligne ne débordera pas
		$pdf->SetFont('','B');
		$un_espace_gras=$pdf->GetStringWidth(' ');
		if($my_echo_debug==1) my_echo_debug("Un espace en gras mesure $un_espace_gras\n");
		$marge_espaces=3*$un_espace_gras;
		if($my_echo_debug==1) my_echo_debug("On compte trois espaces de marge, soit $marge_espaces\n");
		$largeur_utile=$largeur_dispo-$marge_espaces;
		if($my_echo_debug==1) my_echo_debug("D'où \$largeur_utile=$largeur_utile\n");

		// CONTROLER QUE \$largeur_utile>0
		if($largeur_utile<=0) {
			// On se laisse une chance que cela tienne en tronquant
			$tronquer="y";
			break;
		}

		$style_courant='';
		$pdf->SetFont('',$style_courant);

		// (Ré-)initialisation du témoin
		$temoin_reduire_police="n";

		if($supprimer_retours_a_la_ligne=="y") {
			$texte=trim(preg_replace("/\n/"," ",$texte));
		}

		$chaine_longueur_ligne_courante="0";

		$tab=preg_split('/<(.*)>/U',$texte,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($tab as $i=>$valeur) {
			// Avec $i pair on a le texte et les indices impairs correspondent aux balises (b et /b,...)

			// On initialise la ligne courante si nécessaire pour le cas où on aurait $texte="<b>Blabla..."
			// Il faut que la ligne soit initialisée pour pouvoir ajouter le <b> dans $i%2!=0
			if(!isset($ligne[$cpt])) {
				$ligne[$cpt]='';
				$longueur_ligne_courante=0;
				$chaine_longueur_ligne_courante="0";
			}

			if($i%2==0) {
				if($my_echo_debug==1) my_echo_debug("\nParcours avec l'élément \$i=$i: \"$tab[$i]\"\n");

				$tab2=explode(" ",$tab[$i]);
				// Si on gère aussi les virgules et tirets, il y a une difficulté supplémentaire à gérer pour re-concaténer (normalement après une virgule, on doit avoir un espace)... donc on ne gère que les espaces

				if($my_echo_debug==1) my_echo_debug("_____________________________________________\n");
				for($j=0;$j<count($tab2);$j++) {
					if($my_echo_debug==1) my_echo_debug("Mot \$tab2[$j]=\"$tab2[$j]\"\n");
				}
				if($my_echo_debug==1) my_echo_debug("_____________________________________________\n");

				for($j=0;$j<count($tab2);$j++) {
					if($my_echo_debug==1) my_echo_debug("Mot \$tab2[$j]=\"$tab2[$j]\"\n");

					// Si un des mots dépasse $largeur_dispo, il faut réduire la police (et si avec la police minimale, ça dépasse $largeur_dispo, il faudra couper n'importe où...)
					if($pdf->GetStringWidth($tab2[$j])>$largeur_utile) {
						$temoin_reduire_police="y";
						break;
					}

					if($j>0) {
						// Il ne faut ajouter un espace que si on a augmenté $j... (on n'est plus au premier mot de la ligne ~ voire... pb avec les découpes suivant les balises HTML)
						$largeur_espace=$pdf->GetStringWidth(' ');
						$longueur_ligne_courante+=$largeur_espace;
						$chaine_longueur_ligne_courante.="+".$largeur_espace;

						if($my_echo_debug==1) my_echo_debug("\$longueur_ligne_courante=$longueur_ligne_courante et \$largeur_utile=$largeur_utile\n");
						if($my_echo_debug==1) my_echo_debug("\$chaine_longueur_ligne_courante=$chaine_longueur_ligne_courante\n");

						if($longueur_ligne_courante>$largeur_utile) {
							// En ajoutant un espace, on dépasse la largeur_dispo
							$cpt++;
							if($cpt+1>$nb_max_lig) {
								// On dépasse le nombre max de lignes avec la taille de police courante
								$temoin_reduire_police="y";
								// On quitte la boucle sur les \n (boucle sur $tab3)
								break;
							}

							$ligne[$cpt]='';
							$longueur_ligne_courante=0;
							$chaine_longueur_ligne_courante="0";
						}
						else {
							$ligne[$cpt].=' ';
							if($my_echo_debug==1) my_echo_debug("On a ajouté un espace dans la longueur qui précède.\n");
							if($my_echo_debug==1) my_echo_debug("Longueur calculée sans gérer les balises ".$pdf->GetStringWidth($ligne[$cpt])."\n");
						}
					}

					// Il n'y a pas d'espace dans $tab2[$j]
					// Si on scinde avec des \n, on aura un mot par indice de $tab3
					unset($tab3);
					$tab3=array();

					if($my_echo_debug==1) my_echo_debug("\$supprimer_retours_a_la_ligne=$supprimer_retours_a_la_ligne\n");
					// Prendre en compte à ce niveau les \n
					if($supprimer_retours_a_la_ligne=="n") {
						if($my_echo_debug==1) my_echo_debug("On découpe si nécessaire les retours à la ligne\n");
						$tab3=explode("\n",$tab2[$j]);
						for($loop=0;$loop<count($tab3);$loop++) {if($my_echo_debug==1) my_echo_debug("   \$tab3[$loop]=\"$tab3[$loop]\"\n");}
					}
					else {
						$tab3[0]=$tab2[$j];
					}

					// Si supprimer_retours_a_la_ligne=='y', on ne fait qu'un tour dans la boucle
					for($k=0;$k<count($tab3);$k++) {
						if($k>0) {
							// On change de ligne

							if($my_echo_debug==1) my_echo_debug("\$ligne[$cpt]=\"$ligne[$cpt]\"\n");
							if($my_echo_debug==1) my_echo_debug("\$longueur_ligne_courante=$longueur_ligne_courante\n");
							if($my_echo_debug==1) my_echo_debug("\$chaine_longueur_ligne_courante=$chaine_longueur_ligne_courante\n");

							$cpt++;
							if($cpt+1>$nb_max_lig) {
								// On dépasse le nombre max de lignes avec la taille de police courante
								$temoin_reduire_police="y";
								// On quitte la boucle sur les \n (boucle sur $tab3)
								break;
							}
							$ligne[$cpt]='';
							$longueur_ligne_courante=0;
							$chaine_longueur_ligne_courante="0";
						}
						$test_longueur_ligne_courante=$longueur_ligne_courante+$pdf->GetStringWidth($tab3[$k]);
						if($my_echo_debug==1) my_echo_debug("La longueur du mot \$tab3[$k]=\"$tab3[$k]\" est ".$pdf->GetStringWidth($tab3[$k])."\n");

						if($test_longueur_ligne_courante>$largeur_utile) {
							$cpt++;
							if($cpt+1>$nb_max_lig) {
								// On dépasse le nombre max de lignes avec la taille de police courante
								$temoin_reduire_police="y";
								// On quitte la boucle sur les \n (boucle sur $tab3)
								break;
							}
							$ligne[$cpt]=$tab3[$k];
							$longueur_mot=$pdf->GetStringWidth($tab3[$k]);
							$longueur_ligne_courante=$longueur_mot;
							$chaine_longueur_ligne_courante=$longueur_mot;
						}
						else {
							// Ca tient encore sur la ligne courante
							$ligne[$cpt].=$tab3[$k];
							$longueur_mot=$pdf->GetStringWidth($tab3[$k]);
							$longueur_ligne_courante+=$longueur_mot;
							$chaine_longueur_ligne_courante.="+".$longueur_mot;
						}
						if($my_echo_debug==1) my_echo_debug("\$ligne[$cpt]=\"$ligne[$cpt]\"\n");
						if($my_echo_debug==1) my_echo_debug("\$longueur_ligne_courante=$longueur_ligne_courante\n");
						if($my_echo_debug==1) my_echo_debug("\$chaine_longueur_ligne_courante=$chaine_longueur_ligne_courante\n");
					}

					if($temoin_reduire_police=="y") {
						// On quitte la boucle sur les mots (boucle sur $tab2)
						break;
					}
				}
			}
			elseif($supprimer_balises=="n") {
				// On tient compte des balises
				if($valeur{0}=='/') {
					// On referme une balise
					if(strtoupper($valeur)=='/B') {
						$style_courant=preg_replace("/B/i","",$style_courant);
						$pdf->SetFont('',$style_courant);
						$ligne[$cpt].="</B>";
					}
					elseif(strtoupper($valeur)=='/I') {
						$style_courant=preg_replace("/I/i","",$style_courant);
						$pdf->SetFont('',$style_courant);
						$ligne[$cpt].="</I>";
					}
					elseif(strtoupper($valeur)=='/U') {
						$style_courant=preg_replace("/U/i","",$style_courant);
						$pdf->SetFont('',$style_courant);
						$ligne[$cpt].="</U>";
					}
				}
				else {
					// On ouvre une balise
					if(strtoupper($valeur)=='B') {
						$style_courant=$style_courant.'B';
						$pdf->SetFont('',$style_courant);
						$ligne[$cpt].="<B>";
					}
					elseif(strtoupper($valeur)=='I') {
						$style_courant=$style_courant.'I';
						$pdf->SetFont('',$style_courant);
						$ligne[$cpt].="<I>";
					}
					elseif(strtoupper($valeur)=='U') {
						$style_courant=$style_courant.'U';
						$pdf->SetFont('',$style_courant);
						$ligne[$cpt].="<U>";
					}
				}
				if($my_echo_debug==1) my_echo_debug("\$ligne[$cpt]=\"$ligne[$cpt]\"\n");
				if($my_echo_debug==1) my_echo_debug("\$longueur_ligne_courante=$longueur_ligne_courante\n");
				if($my_echo_debug==1) my_echo_debug("\$style_courant=$style_courant\n");
			}

			if($temoin_reduire_police=="y") {
				$hauteur_texte-=$increment;
				//if($hauteur_texte<=0) {
				if(($hauteur_texte<=0)||($hauteur_texte<$hauteur_min_font)) {
					// Problème... il va falloir:
					// - ne pas prendre en compte les \n
					// - ne pas prendre en compte les balises
					// - tronquer

					if($supprimer_retours_a_la_ligne=='n') {
						// On va virer les \n en les remplaçant par des espaces
						$supprimer_retours_a_la_ligne='y';
						if($my_echo_debug==1) my_echo_debug("+++ On va supprimer les retours à la ligne.\n");
					}
					elseif($supprimer_balises=='n') {
						// On va un cran plus loin... en virant les balises... on ne gagnera que sur les mots en gras qui sont plus larges
						$supprimer_balises='y';
						if($my_echo_debug==1) my_echo_debug("+++ On va supprimer les balises.\n");
					}
					else {
						// Il va falloir tronquer... pas cool!

						// A FAIRE
						$tronquer="y";

						if($my_echo_debug==1) my_echo_debug("+++ On va tronquer.\n");
					}

					// Réinitialiser la taille de police:
					$hauteur_texte=$hauteur_max_font;
				}
				else {
					if($my_echo_debug==1) my_echo_debug("+++++++++++++++\n");
					if($my_echo_debug==1) my_echo_debug("\nOn réduit la taille de police:\n");
					if($my_echo_debug==1) my_echo_debug("\$hauteur_texte=".$hauteur_texte."\n");
				}

				// On quitte la boucle sur le tableau des découpages de balises HTML (boucle sur $tab)
				break;
			}
		}

		if($my_echo_debug==1) my_echo_debug("\$temoin_reduire_police=$temoin_reduire_police\n");

		if($temoin_reduire_police!="y") {
			// On a fini par trouver une taille  de police convenable

			if($my_echo_debug==1) my_echo_debug("\nOn a trouvé la bonne la taille de police:\n");

			// On quitte la boucle pour procéder à l'affichage du contenu de $ligne plus bas
			break;
		}
	}

	if($tronquer=='y') {
		// A FAIRE: On va remplir en coupant n'importe où dans les mots sans chercher à conserver des mots entiers
		//          Faut-il faire la boucle sur la taille de police?
		//          Ou prendre directement la taille minimale?

		if($my_echo_debug==1) my_echo_debug("---------------------------------\n");
		if($my_echo_debug==1) my_echo_debug("--- On va remplir en tronquant...\n");

		$hauteur_texte=$hauteur_min_font;

		$pdf->SetFontSize($hauteur_texte);

		// Nombre max de lignes avec la hauteur courante de police
		$nb_max_lig=max(1,floor(($h_cell-$r_interligne*($hauteur_texte*26/100))/((1+$r_interligne)*($hauteur_texte*26/100))));

		if($my_echo_debug==1) my_echo_debug("\$hauteur_texte=$hauteur_texte\n");
		if($my_echo_debug==1) my_echo_debug("\$nb_max_lig=$nb_max_lig\n");

		// Lignes dans la cellule
		unset($ligne);
		$ligne=array();

		// Compteur des lignes
		$cpt=0;

		$longueur_max_atteinte="n";

		// On prévoit deux... trois espaces de marge en gras pour s'assurer que la ligne ne débordera pas
		$pdf->SetFont('','B');
		$marge_espaces=3*$pdf->GetStringWidth(' ');
		$largeur_utile=$largeur_dispo-$marge_espaces;

		// CONTROLER QUE \$largeur_utile>0
		if($largeur_utile>0) {
			$style_courant='';
			$pdf->SetFont('',$style_courant);

			// On va supprimer les retours à la ligne
			$texte=trim(preg_replace("/\n/"," ",$texte));
			if($my_echo_debug==1) my_echo_debug("\$texte=$texte\n");

			// On supprime les balises
			$texte=preg_replace('/<(.*)>/U','',$texte);
			if($my_echo_debug==1) my_echo_debug("\$texte=$texte\n");
			for($j=0;$j<strlen($texte);$j++) {

				if(!isset($ligne[$cpt])) {
					$ligne[$cpt]='';
				}
				if($my_echo_debug==1) my_echo_debug("\$ligne[$cpt]=\"$ligne[$cpt]\"\n");

				$chaine=$ligne[$cpt].substr($texte,$j,1);
				if($my_echo_debug==1) my_echo_debug("\$chaine=\"$chaine\"\n");

				if($pdf->GetStringWidth($chaine)>$largeur_utile) {

					if($my_echo_debug==1) my_echo_debug("Avec \$chaine, ça dépasse.\n");

					if($cpt+1>$nb_max_lig) {
						$longueur_max_atteinte="y";

						if($my_echo_debug==1) my_echo_debug("\$cpt=$cpt et \$nb_max_lig=$nb_max_lig.\nOn ne peut plus ajouter une ligne.\n");

						break;
					}

					$cpt++;
					$ligne[$cpt]=substr($texte,$j,1);
					if($my_echo_debug==1) my_echo_debug("On commence une nouvelle ligne avec le dernier caractère: \"".substr($texte,$j-1,1)."\"\n");
					if($my_echo_debug==1) my_echo_debug("\$ligne[$cpt]=\"$ligne[$cpt]\"\n");
				}
				else {
					$ligne[$cpt].=substr($texte,$j,1);
					if($my_echo_debug==1) my_echo_debug("\$ligne[$cpt]=\"$ligne[$cpt]\"\n");
				}
			}

			if($my_echo_debug==1) my_echo_debug("On a fini le texte... ou atteint une limite\n");

		}
	}

	// On va afficher le texte

	// Hauteur de la police en mm
	$hauteur_texte_mm=$hauteur_texte*26/100;
	// Hauteur de la police en pt
	$taille_police=$hauteur_texte;
	// Hauteur totale du texte
	$hauteur_totale=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
	// Marge verticale en mm entre les lignes
	$marge_verticale=$hauteur_texte_mm*$r_interligne;


	if($my_echo_debug==1) my_echo_debug("\$hauteur_texte=".$hauteur_texte."\n");
	if($my_echo_debug==1) my_echo_debug("\$hauteur_texte_mm=".$hauteur_texte_mm."\n");
	if($my_echo_debug==1) my_echo_debug("\$hauteur_totale=".$hauteur_totale."\n");
	if($my_echo_debug==1) my_echo_debug("\$marge_verticale=".$marge_verticale."\n");


	// On trace le rectangle (vide) du cadre:
	$pdf->SetXY($x,$y);
	$pdf->Cell($largeur_dispo,$h_cell, '',$bordure,2,'');

	// On va écrire les lignes avec la taille de police optimale déterminée (cf. $ifmax)
	$nb_lig=count($ligne);
	$h=$nb_lig*$hauteur_texte_mm*(1+$r_interligne);
	$t=$h_cell-$h;
	if($my_echo_debug==1) my_echo_debug("\$t=".$t."\n");
	$bord_debug='';
	//$bord_debug='LRBT';

	// On ne gère que les v_align Top et Center... et ajout d'un mode aéré
	if($v_align=='E') {
		// Mode aéré
		$espace_v=($h_cell-4*$marge_verticale-$nb_lig*$hauteur_texte_mm)/max(1,$nb_lig-1);
	}
	elseif($v_align!='T') {
		// Par défaut c'est Center
		//$decalage_v_top=($h_cell-$nb_lig*$hauteur_texte_mm-($nb_lig-1)*$marge_verticale)/2;
		$decalage_v_top=($h_cell-($nb_lig+1)*$hauteur_texte_mm-$nb_lig*$marge_verticale)/2;
	}

	for($i=0;$i<count($ligne);$i++) {

		if($v_align=='T') {
			$pdf->SetXY($x,$y+$i*($hauteur_texte_mm+$marge_verticale));

			// Pour pouvoir afficher le $bord_debug
			$pdf->Cell($largeur_dispo,$hauteur_texte_mm+2*$marge_verticale, '',$bord_debug,1,$align);

			$y_courant=$y+$i*($hauteur_texte_mm+$marge_verticale)-$marge_verticale;
			$pdf->SetXY($x,$y_courant);
			if($my_echo_debug==1) {
				$pdf->myWriteHTML($ligne[$i]." ".$i." ".round($y_courant));
			}
			else {
				$pdf->myWriteHTML($ligne[$i]);
			}
		}
		elseif($v_align=='E') {
			$y_courant=$y+$marge_verticale+$i*($hauteur_texte_mm+$espace_v);
			$pdf->SetXY($x,$y_courant);

			// Pour pouvoir afficher le $bord_debug
			$pdf->Cell($largeur_dispo,$h_cell/$nb_lig, '',$bord_debug,1,$align);

			$pdf->SetXY($x,$y_courant);
			$pdf->myWriteHTML($ligne[$i]);
		}
		else {
			$y_courant=$y+$decalage_v_top+$i*($hauteur_texte_mm+$marge_verticale);

			// Pour pouvoir afficher le $bord_debug A REFAIRE
			
			$pdf->SetXY($x,$y_courant);
			
			$pdf->myWriteHTML($ligne[$i]);
		}
	}
}


/**
 * Ancienne fonction cell_ajustee() ne gérant pas les balises HTML B,I et U
 *
 * @global class
 * @param type $texte
 * @param type $x
 * @param type $y
 * @param type $largeur_dispo
 * @param type $h_cell
 * @param type $hauteur_max_font
 * @param type $hauteur_min_font
 * @param type $bordure LRBT
 * @param string $v_align C(enter) ou T(op)
 * @param type $align
 * @param int $increment nombre dont on réduit la police à chaque essai
 * @param type $r_interligne proportion de la taille de police pour les interlignes
 * @see PDF
 */
function cell_ajustee0($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align='C',$align='L',$increment=0.3,$r_interligne=0.3) {
	global $pdf;

	$texte=trim($texte);
	$hauteur_texte=$hauteur_max_font;
	$pdf->SetFontSize($hauteur_texte);
	$taille_texte_total=$pdf->GetStringWidth($texte);

	// Ca nous donne le nombre max de lignes en hauteur avec la taille de police maxi
	// Il faudrait plutôt déterminer ce nombre d'après une taille minimale acceptable de police
	$nb_max_lig=max(1,floor($h_cell/((1+$r_interligne)*($hauteur_min_font*26/100))));
	
	$fmax=0;

	$tab_lig=array();
	for($j=1;$j<=$nb_max_lig;$j++) {
		$hauteur_texte=$hauteur_max_font;

		unset($ligne);
		$ligne=array();

		$tab=explode(" ",$texte);
		$cpt=0;
		$i=0;
		while(TRUE) {
			if(isset($ligne[$cpt])) {$ligne[$cpt].=" ";} else {$ligne[$cpt]="";}

			if(preg_match("/\n/",$tab[$i])) {
				$tmp_tab=explode("\n",$tab[$i]);

				for($k=0;$k<count($tmp_tab)-1;$k++) {
					if(!isset($ligne[$cpt])) {$ligne[$cpt]="";}
					$ligne[$cpt].=$tmp_tab[$k];
					$cpt++;
				}
				if(!isset($ligne[$cpt])) {$ligne[$cpt]="";}
				$ligne[$cpt].=$tmp_tab[$k];
			}
			else {
				if($pdf->GetStringWidth($ligne[$cpt].$tab[$i])>=$largeur_dispo) {
					$cpt++;
					$ligne[$cpt]=$tab[$i];
				}
				else {
					$ligne[$cpt].=$tab[$i];
				}
			}
			$i++;
			if(!isset($tab[$i])) {break;}
		}

		// Recherche de la plus longue ligne:
		$taille_texte_ligne=0;
		$num=0;
		for($i=0;$i<count($ligne);$i++) {
			$l=$pdf->GetStringWidth($ligne[$i]);
			if($taille_texte_ligne<$l) {$taille_texte_ligne=$l;$num=$i;}
		}

		// On calcule la hauteur en mm de la police (proportionnalité: 100pt -> 26mm)
		$hauteur_texte_mm=$hauteur_texte*26/100;
		// Hauteur totale: Nombre de lignes multiplié par la hauteur de police avec les marges verticales
		$hauteur_totale=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);

		// echo "On calcule la taille de la police d'après \$ligne[$num]=".$ligne[$num]."<br/>";
		// On ajuste la taille de police avec la plus grande ligne pour que cela tienne en largeur
		// et on contrôle aussi que cela tient en hauteur, sinon on continue à réduire la police.
		$grandeur_texte='test';
		while($grandeur_texte!='ok') {
			if(($largeur_dispo<$taille_texte_ligne)||($hauteur_totale>$h_cell)) {
				$hauteur_texte=$hauteur_texte-$increment;
				if($hauteur_texte<$hauteur_min_font) {break;}
				$hauteur_texte_mm=$hauteur_texte*26/100;
				$hauteur_totale=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
				$pdf->SetFontSize($hauteur_texte);
				$taille_texte_ligne=$pdf->GetStringWidth($ligne[$num]);
			}
			else {
				$grandeur_texte='ok';
			}
		}

		if($grandeur_texte=='ok') {
			// Hauteur de la police en mm
			$hauteur_texte_mm=$hauteur_texte*26/100;
			$tab_lig[$j]['hauteur_texte_mm']=$hauteur_texte_mm;
			// Hauteur de la police en pt
			$tab_lig[$j]['taille_police']=$hauteur_texte;
			// Hauteur totale du texte
			$tab_lig[$j]['hauteur_totale']=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
			// Marge verticale en mm entre les lignes
			$marge_verticale=$hauteur_texte_mm*$r_interligne;
			$tab_lig[$j]['marge_verticale']=$marge_verticale;
			// Tableau des lignes
			$tab_lig[$j]['lignes']=$ligne;

			// On choisit la hauteur de police la plus grande possible pour laquelle les lignes tiennent en hauteur
			// (la largeur a déjà été utilisée pour découper en lignes).
			if(($hauteur_texte>$fmax)&&($tab_lig[$j]['hauteur_totale']<=$h_cell)) {
				$ifmax=$j;
			}
		}
	}

	if((!isset($ifmax))||($tab_lig[$ifmax]['taille_police']<$hauteur_min_font)) {
		// On relance en remplaçant les retours forcés à la ligne (\n) par des espaces.

		$fmax=0;

		$tab_lig=array();
		for($j=1;$j<=$nb_max_lig;$j++) {
			$hauteur_texte=$hauteur_max_font;

			unset($ligne);
			$ligne=array();

			$tab=explode(" ",trim(preg_replace("/\n/"," ",$texte)));
			$cpt=0;
			$i=0;
			while(TRUE) {
				if(isset($ligne[$cpt])) {$ligne[$cpt].=" ";} else {$ligne[$cpt]="";}

				if($pdf->GetStringWidth($ligne[$cpt].$tab[$i])>=$largeur_dispo) {
					$cpt++;
					$ligne[$cpt]=$tab[$i];
				}
				else {
					$ligne[$cpt].=$tab[$i];
				}
				$i++;
				if(!isset($tab[$i])) {break;}
			}

			// Recherche de la plus longue ligne:
			$taille_texte_ligne=0;
			$num=0;
			for($i=0;$i<count($ligne);$i++) {
				// echo "\$ligne[$i]=$ligne[$i]<br />";
				$l=$pdf->GetStringWidth($ligne[$i]);
				if($taille_texte_ligne<$l) {$taille_texte_ligne=$l;$num=$i;}
			}

			// On calcule la hauteur en mm de la police (proportionnalité: 100pt -> 26mm)
			$hauteur_texte_mm=$hauteur_texte*26/100;
			// Hauteur totale: Nombre de lignes multiplié par la hauteur de police avec les marges verticales
			$hauteur_totale=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);

			// echo "On calcule la taille de la police d'après \$ligne[$num]=".$ligne[$num]."<br/>";
			// On ajuste la taille de police avec la plus grande ligne pour que cela tienne en largeur
			// et on contrôle aussi que cela tient en hauteur, sinon on continue à réduire la police.
			$grandeur_texte='test';
			while($grandeur_texte!='ok') {
				if(($largeur_dispo<$taille_texte_ligne)||($hauteur_totale>$h_cell)) {
					$hauteur_texte=$hauteur_texte-$increment;
					if($hauteur_texte<$hauteur_min_font) {break;}
					$hauteur_texte_mm=$hauteur_texte*26/100;
					$hauteur_totale=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
					$pdf->SetFontSize($hauteur_texte);
					$taille_texte_ligne=$pdf->GetStringWidth($ligne[$num]);
				}
				else {
					$grandeur_texte='ok';
				}
			}

			if($grandeur_texte=='ok') {
				// Hauteur de la police en mm
				$hauteur_texte_mm=$hauteur_texte*26/100;
				$tab_lig[$j]['hauteur_texte_mm']=$hauteur_texte_mm;
				// Hauteur de la police en pt
				$tab_lig[$j]['taille_police']=$hauteur_texte;
				// Hauteur totale du texte
				$tab_lig[$j]['hauteur_totale']=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
				// Marge verticale en mm entre les lignes
				$marge_verticale=$hauteur_texte_mm*$r_interligne;
				$tab_lig[$j]['marge_verticale']=$marge_verticale;
				// Tableau des lignes
				$tab_lig[$j]['lignes']=$ligne;

				// On choisit la hauteur de police la plus grande possible pour laquelle les lignes tiennent en hauteur
				// (la largeur a déjà été utilisée pour découper en lignes).
				if(($hauteur_texte>$fmax)&&($tab_lig[$j]['hauteur_totale']<=$h_cell)) {
					$ifmax=$j;
				}
			}
		}


		// Si ça ne passe toujours pas, on prend $hauteur_min_font sans retours à la ligne et on tronque
		if(!isset($ifmax)) {
			
			$fmax=0;

			$tab_lig=array();
			$hauteur_texte=$hauteur_min_font;
			unset($ligne);
			$ligne=array();

			$tab=explode(" ",trim(preg_replace("/\n/"," ",$texte)));
			$cpt=0;
			$i=0;
			while(TRUE) {
				if(isset($ligne[$cpt])) {$ligne[$cpt].=" ";} else {$ligne[$cpt]="";}

				if($pdf->GetStringWidth($ligne[$cpt].$tab[$i])>=$largeur_dispo) {

					if(($cpt+2)*$hauteur_texte*(1+$r_interligne)*26/100>$h_cell) {
						$d=1;
						while(($pdf->GetStringWidth(substr($ligne[$cpt],0,strlen($ligne[$cpt])-$d)."...")>=$largeur_dispo)&&($d<strlen($ligne[$cpt]))) {
							$d++;
						}
						$ligne[$cpt]=substr($ligne[$cpt],0,strlen($ligne[$cpt])-$d)."...";
						break;
					}

					$cpt++;
					$ligne[$cpt]=$tab[$i];
				}
				else {
					$ligne[$cpt].=$tab[$i];
				}
				$i++;
				if(!isset($tab[$i])) {break;} // On ne devrait pas quitter sur ça puisque le texte va être trop long
			}

			$j=1;
			$ifmax=$j;
			$hauteur_texte_mm=$hauteur_texte*26/100;
			$tab_lig[$j]['hauteur_texte_mm']=$hauteur_texte_mm;
			// Hauteur de la police en pt
			$tab_lig[$j]['taille_police']=$hauteur_texte;
			// Hauteur totale du texte
			$tab_lig[$j]['hauteur_totale']=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
			// Marge verticale en mm entre les lignes
			$marge_verticale=$hauteur_texte_mm*$r_interligne;
			$tab_lig[$j]['marge_verticale']=$marge_verticale;
			// Tableau des lignes
			$tab_lig[$j]['lignes']=$ligne;

		}
	}

	// On trace le rectangle (vide) du cadre:
	$pdf->SetXY($x,$y);
	$pdf->Cell($largeur_dispo,$h_cell, '',$bordure,2,'');

	// On va écrire les lignes avec la taille de police optimale déterminée (cf. $ifmax)
	//$marge_h=round(($h_cell-(count($ligne)*$hauteur_texte_mm+(count($ligne)-1)*$marge_verticale))/2);
	//$marge_h=round(($h_cell-$tab_lig[$ifmax]['hauteur_totale'])/2);
	$nb_lig=count($tab_lig[$ifmax]['lignes']);
	$h=count($tab_lig[$ifmax]['lignes'])*$tab_lig[$ifmax]['hauteur_texte_mm']*(1+$r_interligne);
	$t=$h_cell-$h;
	$bord_debug='';
	for($i=0;$i<count($tab_lig[$ifmax]['lignes']);$i++) {

		$pdf->SetXY($x,$y+$i*($tab_lig[$ifmax]['hauteur_texte_mm']+$tab_lig[$ifmax]['marge_verticale']));

		if($v_align=='T') {
			$pdf->Cell($largeur_dispo,$tab_lig[$ifmax]['hauteur_texte_mm']+2*$tab_lig[$ifmax]['marge_verticale'], $tab_lig[$ifmax]['lignes'][$i],$bord_debug,1,$align);
		}
		else {
			$pdf->Cell($largeur_dispo,$h_cell/count($tab_lig[$ifmax]['lignes']), $tab_lig[$ifmax]['lignes'][$i],$bord_debug,1,$align);
		}
	}
	
}

/**
 *
 * @global class
 * @param type $texte
 * @param type $x
 * @param type $y
 * @param type $largeur_dispo
 * @param type $h_ligne
 * @param type $hauteur_caractere
 * @param type $fonte
 * @param type $graisse
 * @param type $alignement
 * @param type $bordure 
 * @see PDF
 */
function cell_ajustee_une_ligne($texte,$x,$y,$largeur_dispo,$h_ligne,$hauteur_caractere,$fonte,$graisse,$alignement,$bordure) {
	global $pdf;

	$pdf->SetFont($fonte,$graisse,$hauteur_caractere);
	$val = $pdf->GetStringWidth($texte);
	$temoin='';
	while($temoin != 'ok') {
		if($largeur_dispo < $val){
			$hauteur_caractere = $hauteur_caractere-0.3;
			$pdf->SetFont($fonte,$graisse,$hauteur_caractere);
			$val = $pdf->GetStringWidth($texte);
		} else {
			$temoin = 'ok';
		}
	}

	$pdf->SetXY($x,$y);
	$pdf->Cell($largeur_dispo,$h_ligne, $texte,$bordure,2,$alignement);
}





?>
