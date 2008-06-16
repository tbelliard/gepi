<?php

include("../cahier_notes/visu_releve_notes_func.lib.php");

function nbsp_au_lieu_de_vide($texte) {
	if($texte=="") {
		echo "&nbsp;";
	}
	else {
		echo $texte;
	}
}

/*
function decompte_debug($motif,$texte) {
	global $tab_instant, $debug;
	$instant=time();
	if(isset($tab_instant[$motif])) {
		$diff=$instant-$tab_instant[$motif];
		if($debug=="y") {
			echo "<p>$texte: ".$diff." s</p>\n";
		}
	}
	else {
		if($debug=="y") {
			echo "<p>$texte</p>\n";
		}
	}
	$tab_instant[$motif]=$instant;
}

function decompte_debug($motif,$texte) {
	global $tab_instant, $debug;
	if($debug=="y") {
		$instant=microtime();
		if(isset($tab_instant[$motif])) {
			$tmp_tab1=explode(" ",$instant);
			$tmp_tab2=explode(" ",$tab_instant[$motif]);
			if($tmp_tab1[1]!=$tmp_tab2[1]) {
				$diff=$tmp_tab1[1]-$tmp_tab2[1];
			}
			else {
				$diff=$tmp_tab1[0]-$tmp_tab2[0];
			}
			//if($debug=="y") {
				echo "<p style='color:green;'>$texte: ".$diff." s</p>\n";
			//}
		}
		else {
			//if($debug=="y") {
				echo "<p style='color:green;'>$texte</p>\n";
			//}
		}
		$tab_instant[$motif]=$instant;
	}
}
*/

function regime($id_reg) {
	switch($id_reg) {
		case "d/p":
			$regime="demi-pensionnaire";
			break;
		case "ext.":
			$regime="externe";
			break;
		case "int.":
			$regime="interne";
			break;
		case "i-e":
			$regime="interne-externé";
			break;
		default:
			$regime="Régime inconnu???";
			break;
	}

	return $regime;
}

function redimensionne_image_b($photo){
	global $bull_photo_largeur_max, $bull_photo_hauteur_max;

	// prendre les informations sur l'image
	$info_image=getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur=$info_image[0];
	$hauteur=$info_image[1];

	// calcule le ratio de redimensionnement
	$ratio_l=$largeur/$bull_photo_largeur_max;
	$ratio_h=$hauteur/$bull_photo_hauteur_max;
	$ratio=($ratio_l>$ratio_h)?$ratio_l:$ratio_h;

	// définit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur=round($largeur/$ratio);
	$nouvelle_hauteur=round($hauteur/$ratio);

	return array($nouvelle_largeur, $nouvelle_hauteur);
}

/*
function texte_html_ou_pas($texte){
	// Si le texte contient des < et >, on affiche tel quel
	if((strstr($texte,">"))||(strstr($texte,"<"))){
		$retour=$texte;
	}
	// Sinon, on transforme les retours à la ligne en <br />
	else{
		$retour=nl2br($texte);
	}
	return $retour;
}
*/

// $tab_bulletin[$id_classe][$periode_num]
// $i indice élève
function bulletin_html($tab_bull,$i,$tab_rel) {
	global
		//============================================
		// Paramètres généraux:
		// En admin, dans Gestion générale/Configuration générale
		$gepi_prof_suivi,

		$RneEtablissement,
		$gepiSchoolName,
		$gepiSchoolAdress1,
		$gepiSchoolAdress2,
		$gepiSchoolZipCode,
		$gepiSchoolCity,
		$gepiSchoolPays,
		$gepiSchoolTel,
		$gepiSchoolFax,
		$gepiYear,

		$logo_etab,
		//============================================
		// Paramètres d'impression des bulletins HTML:

		// Mise en page du bulletin scolaire
		$bull_body_marginleft,
		// $titlesize, $textsize, $p_bulletin_margin sont récupérés plus haut dans l'entête pour écrire les styles
		$largeurtableau,

		$col_matiere_largeur,
		$col_note_largeur,
		$col_boite_largeur,
		$col_hauteur,		// La hauteur minimale de ligne n'est exploitée que dans les boites/conteneurs
		$cellpadding,
		$cellspacing,
		$bull_ecart_entete,
		$bull_espace_avis,
		// $bull_bordure_classique permet de renseigner $class_bordure
		$class_bordure,

		$bull_categ_font_size,
		$bull_categ_bgcolor,
		//======================
		$bull_categ_font_size_avis,
		$bull_police_avis,
		$bull_font_style_avis,
		// Ils sont utilisés dans l'entête pour générer les styles
		//======================
		$genre_periode,
		$bull_affich_nom_etab,
		$bull_affich_adr_etab,

		// Informations devant figurer sur le bulletin scolaire
		$bull_mention_nom_court,
		$bull_mention_doublant,
		$bull_affiche_eleve_une_ligne,
		$bull_affiche_appreciations,
		$bull_affiche_absences,
		$bull_affiche_avis,
		$bull_affiche_aid,
		$bull_affiche_numero,		// affichage du numéro du bulletin
		// L'affichage des graphes devrait provenir des Paramètres d'impression des bulletins HTML, mais le paramètre a été stocké dans $tab_bull
		$bull_affiche_signature,	// affichage du nom du PP et du chef d'établissement
		$bull_affiche_etab,			// Etablissement d'origine


		$activer_photo_bulletin,
		// $bull_photo_largeur_max et $bull_photo_hauteur_max sont récupérées via global dans redimensionne_image()

		$bull_affiche_tel,
		$bull_affiche_fax,
		$bull_intitule_app,
		$bull_affiche_INE_eleve,
		$bull_affiche_formule,
		$bull_formule_bas,
		// Nom du fichier déterminé d'après le paramètre choix_bulletin
		$fichier_bulletin,
		$min_max_moyclas,

		// Bloc adresse responsable
		$addressblock_padding_right,
		$addressblock_padding_top,
		$addressblock_padding_text,
		$addressblock_length,
		$addressblock_font_size,
		//addressblock_logo_etab_prop correspond au pourcentage $largeur1 et $largeur2 est le complément à 100%
		$largeur1,
		$largeur2,
		// Pourcentage calculé par rapport au tableau contenant le bloc Classe, Année,...
		$addressblock_classe_annee2,
		// Nombre de sauts de ligne entre le bloc Logo+Etablissement et le bloc Nom, prénom,... de l'élève
		$bull_ecart_bloc_nom,
		$addressblock_debug,

		// Page de garde
		$page_garde_imprime,
		$affiche_page_garde,
		// Les autres paramètres de la page de garde sont récupérés directement dans page_garde.php
		// Il faudrait ramener ici les variables pour éviter de faire les requêtes autant de fois qu'il y a de bulletins

		//============================================
		// Paramètre transmis depuis la page d'impression des bulletins
		$un_seul_bull_par_famille,

		//============================================
		// Tableaux provenant de /lib/global.inc
		$type_etablissement,
		$type_etablissement2,

		//============================================
		// Paramètre du module trombinoscope
		// En admin, dans Gestion des modules
		$active_module_trombinoscopes
;

	// Récupérer avant le nombre de bulletins à imprimer
	// - que le premier resp
	// - tous les resp si adr différentes
	// et le passer via global
	//================================

	// Initialisation:
	$nb_bulletins=1;

	unset($tab_adr_ligne1);
	unset($tab_adr_ligne2);
	unset($tab_adr_ligne3);
	if ( $affiche_page_garde == 'yes' OR $tab_bull['affiche_adresse'] == 'y') {
		// Préparation des lignes adresse responsable
		if (!isset($tab_bull['eleve'][$i]['resp'][0])) {
			$tab_adr_ligne1[0]="<font color='red'><b>ADRESSE MANQUANTE</b></font>";
			$tab_adr_ligne2[0]="";
			$tab_adr_ligne3[0]="";
		}
		else {
			if (isset($tab_bull['eleve'][$i]['resp'][1])) {
				if((isset($tab_bull['eleve'][$i]['resp'][1]['adr1']))&&
					(isset($tab_bull['eleve'][$i]['resp'][1]['adr2']))&&
					(isset($tab_bull['eleve'][$i]['resp'][1]['adr3']))&&
					(isset($tab_bull['eleve'][$i]['resp'][1]['adr4']))&&
					(isset($tab_bull['eleve'][$i]['resp'][1]['cp']))&&
					(isset($tab_bull['eleve'][$i]['resp'][1]['commune']))
				) {
					// Le deuxième responsable existe et est renseigné
					if (($tab_bull['eleve'][$i]['resp'][0]['adr_id']==$tab_bull['eleve'][$i]['resp'][1]['adr_id']) OR
						(
							($tab_bull['eleve'][$i]['resp'][0]['adr1']==$tab_bull['eleve'][$i]['resp'][1]['adr1'])&&
							($tab_bull['eleve'][$i]['resp'][0]['adr2']==$tab_bull['eleve'][$i]['resp'][1]['adr2'])&&
							($tab_bull['eleve'][$i]['resp'][0]['adr3']==$tab_bull['eleve'][$i]['resp'][1]['adr3'])&&
							($tab_bull['eleve'][$i]['resp'][0]['adr4']==$tab_bull['eleve'][$i]['resp'][1]['adr4'])&&
							($tab_bull['eleve'][$i]['resp'][0]['cp']==$tab_bull['eleve'][$i]['resp'][1]['cp'])&&
							($tab_bull['eleve'][$i]['resp'][0]['commune']==$tab_bull['eleve'][$i]['resp'][1]['commune'])
						)
					) {
						// Les adresses sont identiques
						$nb_bulletins=1;

						if(($tab_bull['eleve'][$i]['resp'][0]['nom']!=$tab_bull['eleve'][$i]['resp'][1]['nom'])&&
							($tab_bull['eleve'][$i]['resp'][1]['nom']!="")) {
							// Les noms des responsables sont différents
							//$tab_adr_ligne1[0]=$tab_bull['eleve'][$i]['resp'][0]['civilite']." ".$tab_bull['eleve'][$i]['resp'][0]['nom']." ".$tab_bull['eleve'][$i]['resp'][0]['prenom']." et ".$tab_bull['eleve'][$i]['resp'][1]['civilite']." ".$tab_bull['eleve'][$i]['resp'][1]['nom']." ".$tab_bull['eleve'][$i]['resp'][1]['prenom'];
							$tab_adr_ligne1[0]=$tab_bull['eleve'][$i]['resp'][0]['civilite']." ".$tab_bull['eleve'][$i]['resp'][0]['nom']." ".$tab_bull['eleve'][$i]['resp'][0]['prenom'];
							//$tab_adr_ligne1[0].=" et ";
							$tab_adr_ligne1[0].="<br />\n";
							$tab_adr_ligne1[0].="et ";
							$tab_adr_ligne1[0].=$tab_bull['eleve'][$i]['resp'][1]['civilite']." ".$tab_bull['eleve'][$i]['resp'][1]['nom']." ".$tab_bull['eleve'][$i]['resp'][1]['prenom'];
						}
						else{
							if(($tab_bull['eleve'][$i]['resp'][0]['civilite']!="")&&($tab_bull['eleve'][$i]['resp'][1]['civilite']!="")) {
								$tab_adr_ligne1[0]=$tab_bull['eleve'][$i]['resp'][0]['civilite']." et ".$tab_bull['eleve'][$i]['resp'][1]['civilite']." ".$tab_bull['eleve'][$i]['resp'][0]['nom']." ".$tab_bull['eleve'][$i]['resp'][0]['prenom'];
							}
							else {
								$tab_adr_ligne1[0]="M. et Mme ".$tab_bull['eleve'][$i]['resp'][0]['nom']." ".$tab_bull['eleve'][$i]['resp'][0]['prenom'];
							}
						}

						$tab_adr_ligne2[0]=$tab_bull['eleve'][$i]['resp'][0]['adr1'];
						if($tab_bull['eleve'][$i]['resp'][0]['adr2']!=""){
							$tab_adr_ligne2[0].="<br />\n".$tab_bull['eleve'][$i]['resp'][0]['adr2'];
						}
						if($tab_bull['eleve'][$i]['resp'][0]['adr3']!=""){
							$tab_adr_ligne2[0].="<br />\n".$tab_bull['eleve'][$i]['resp'][0]['adr3'];
						}
						if($tab_bull['eleve'][$i]['resp'][0]['adr4']!=""){
							$tab_adr_ligne2[0].="<br />\n".$tab_bull['eleve'][$i]['resp'][0]['adr4'];
						}
						$tab_adr_ligne3[0]=$tab_bull['eleve'][$i]['resp'][0]['cp']." ".$tab_bull['eleve'][$i]['resp'][0]['commune'];

						if(($tab_bull['eleve'][$i]['resp'][0]['pays']!="")&&(strtolower($tab_bull['eleve'][$i]['resp'][0]['pays'])!=strtolower($gepiSchoolPays))) {
							if($tab_adr_ligne3[0]!=" "){
								$tab_adr_ligne3[0].="<br />";
							}
							$tab_adr_ligne3[0].=$tab_bull['eleve'][$i]['resp'][0]['pays'];
						}
					}
					else {
						// Les adresses sont différentes
						//if ($un_seul_bull_par_famille!="oui") {
						// On teste en plus si la deuxième adresse est valide
						if (($un_seul_bull_par_famille!="oui")&&
							($tab_bull['eleve'][$i]['resp'][1]['adr1']!="")&&
							($tab_bull['eleve'][$i]['resp'][1]['commune']!="")
						) {
							$nb_bulletins=2;
						}
						else {
							$nb_bulletins=1;
						}

						for($cpt=0;$cpt<$nb_bulletins;$cpt++) {
							if($tab_bull['eleve'][$i]['resp'][$cpt]['civilite']!="") {
								$tab_adr_ligne1[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['civilite']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['nom']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['prenom'];
							}
							else {
								$tab_adr_ligne1[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['nom']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['prenom'];
							}

							$tab_adr_ligne2[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['adr1'];
							if($tab_bull['eleve'][$i]['resp'][$cpt]['adr2']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_bull['eleve'][$i]['resp'][$cpt]['adr2'];
							}
							if($tab_bull['eleve'][$i]['resp'][$cpt]['adr3']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_bull['eleve'][$i]['resp'][$cpt]['adr3'];
							}
							if($tab_bull['eleve'][$i]['resp'][$cpt]['adr4']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_bull['eleve'][$i]['resp'][$cpt]['adr4'];
							}
							$tab_adr_ligne3[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['cp']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['commune'];

							if(($tab_bull['eleve'][$i]['resp'][$cpt]['pays']!="")&&(strtolower($tab_bull['eleve'][$i]['resp'][$cpt]['pays'])!=strtolower($gepiSchoolPays))) {
								if($tab_adr_ligne3[$cpt]!=" "){
									$tab_adr_ligne3[$cpt].="<br />";
								}
								$tab_adr_ligne3[$cpt].=$tab_bull['eleve'][$i]['resp'][$cpt]['pays'];
							}

						}

					}
				}
				else {
					// Il n'y a pas de deuxième adresse, mais il y aurait un deuxième responsable???
					// CA NE DEVRAIT PAS ARRIVER ETANT DONNé LA REQUETE EFFECTUEE QUI JOINT resp_pers ET resp_adr...
						if ($un_seul_bull_par_famille!="oui") {
							$nb_bulletins=2;
						}
						else {
							$nb_bulletins=1;
						}

						for($cpt=0;$cpt<$nb_bulletins;$cpt++) {
							if($tab_bull['eleve'][$i]['resp'][$cpt]['civilite']!="") {
								$tab_adr_ligne1[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['civilite']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['nom']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['prenom'];
							}
							else {
								$tab_adr_ligne1[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['nom']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['prenom'];
							}

							$tab_adr_ligne2[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['adr1'];
							if($tab_bull['eleve'][$i]['resp'][$cpt]['adr2']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_bull['eleve'][$i]['resp'][$cpt]['adr2'];
							}
							if($tab_bull['eleve'][$i]['resp'][$cpt]['adr3']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_bull['eleve'][$i]['resp'][$cpt]['adr3'];
							}
							if($tab_bull['eleve'][$i]['resp'][$cpt]['adr4']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_bull['eleve'][$i]['resp'][$cpt]['adr4'];
							}
							$tab_adr_ligne3[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['cp']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['commune'];

							if(($tab_bull['eleve'][$i]['resp'][$cpt]['pays']!="")&&(strtolower($tab_bull['eleve'][$i]['resp'][$cpt]['pays'])!=strtolower($gepiSchoolPays))) {
								if($tab_adr_ligne3[$cpt]!=" "){
									$tab_adr_ligne3[$cpt].="<br />";
								}
								$tab_adr_ligne3[$cpt].=$tab_bull['eleve'][$i]['resp'][$cpt]['pays'];
							}
						}
				}
			}
			else {
				// Il n'y a pas de deuxième responsable
				$nb_bulletins=1;

				if($tab_bull['eleve'][$i]['resp'][0]['civilite']!="") {
					$tab_adr_ligne1[0]=$tab_bull['eleve'][$i]['resp'][0]['civilite']." ".$tab_bull['eleve'][$i]['resp'][0]['nom']." ".$tab_bull['eleve'][$i]['resp'][0]['prenom'];
				}
				else {
					$tab_adr_ligne1[0]=$tab_bull['eleve'][$i]['resp'][0]['nom']." ".$tab_bull['eleve'][$i]['resp'][0]['prenom'];
				}

				$tab_adr_ligne2[0]=$tab_bull['eleve'][$i]['resp'][0]['adr1'];
				if($tab_bull['eleve'][$i]['resp'][0]['adr2']!=""){
					$tab_adr_ligne2[0].="<br />\n".$tab_bull['eleve'][$i]['resp'][0]['adr2'];
				}
				if($tab_bull['eleve'][$i]['resp'][0]['adr3']!=""){
					$tab_adr_ligne2[0].="<br />\n".$tab_bull['eleve'][$i]['resp'][0]['adr3'];
				}
				if($tab_bull['eleve'][$i]['resp'][0]['adr4']!=""){
					$tab_adr_ligne2[0].="<br />\n".$tab_bull['eleve'][$i]['resp'][0]['adr4'];
				}
				$tab_adr_ligne3[0]=$tab_bull['eleve'][$i]['resp'][0]['cp']." ".$tab_bull['eleve'][$i]['resp'][0]['commune'];

				if(($tab_bull['eleve'][$i]['resp'][0]['pays']!="")&&(strtolower($tab_bull['eleve'][$i]['resp'][0]['pays'])!=strtolower($gepiSchoolPays))) {
					if($tab_adr_ligne3[0]!=" "){
						$tab_adr_ligne3[0].="<br />";
					}
					$tab_adr_ligne3[0].=$tab_bull['eleve'][$i]['resp'][0]['pays'];
				}
			}
		}
	}
	// Fin de la préparation des lignes adresse responsable



	// Début des bulletins
	for ($bulletin=0; $bulletin<$nb_bulletins; $bulletin++) {
		echo "\n<!-- Début du bulletin n°$bulletin pour ".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom'].", ".$tab_bull['eleve'][$i]['classe']." -->\n\n";

		// Page de garde
		if ( $affiche_page_garde == 'yes' OR $tab_bull['affiche_adresse'] == 'y') {

			// Affectation des lignes adresse responsable avec les lignes correspondant au bulletin en cours
			$ligne1=$tab_adr_ligne1[$bulletin];
			$ligne2=$tab_adr_ligne2[$bulletin];
			$ligne3=$tab_adr_ligne3[$bulletin];

			// Info affichée en haut de la page de garde
			$info_eleve_page_garde="Elève: ".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom'].", ".$tab_bull['eleve'][$i]['classe'];

			if ($affiche_page_garde == "yes") {
				echo "\n<!-- Début de la page de garde -->\n\n";
				include "./page_garde.php";
				echo "\n<!-- Fin de la page de garde -->\n\n";
				// Saut de page
				echo "<p class='saut'>&nbsp;</p>\n";
			}
		}


		echo "\n<!-- Début de l'affichage de l'entête du bulletin n°$bulletin pour ".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom'].", ".$tab_bull['eleve'][$i]['classe']." -->\n";

		if($tab_bull['affiche_adresse'] == 'y') {
			//-------------------------------
			// Maintenant, on affiche l'en-tête : Les données de l'élève, le bloc adresse responsable et l'adresse du lycée.
			//-------------------------------

			echo "\n<!-- Début du cadre entête -->\n";
			echo "<div";
			if($addressblock_debug=="y"){echo " style='border:1px solid red;'";}
			echo ">\n";

			// Pour éviter que le cadre Adresse responsable ne vienne remonter sur la page précédente:
			echo "<div style='clear: both; font-size: xx-small;'>&nbsp;</div>\n";

			// Cadre adresse du responsable:
			echo "<div style='float:right;
width:".$addressblock_length."mm;
padding-top:".$addressblock_padding_top."mm;
padding-bottom:".$addressblock_padding_text."mm;
padding-right:".$addressblock_padding_right."mm;\n";
			if($addressblock_debug=="y"){echo "border: 1px solid blue;\n";}
			echo "font-size: ".$addressblock_font_size."pt;
'>
<div align='left'>
$ligne1<br />
$ligne2<br />
$ligne3
</div>
</div>\n";


			// Cadre contenant le tableau Logo+Ad_etab et le nom, prénom,... de l'élève:
			echo "<div style='float:left;
left:0px;
top:0px;
width:".$largeur1."%;\n";
			if($addressblock_debug=="y"){echo "border: 1px solid green;\n";}
			echo "'>\n";

			echo "<table";
			if($addressblock_debug=="y"){echo " border='1'";}
			echo ">\n";
			echo "<tr>\n";

			$nom_fic_logo = $logo_etab;
			$nom_fic_logo_c = "../images/".$nom_fic_logo;

			if (($nom_fic_logo != '') and (file_exists($nom_fic_logo_c))) {
				echo "<td style=\"text-align: left;\"><img src=\"".$nom_fic_logo_c."\" border=\"0\" alt=\"Logo\" /></td>\n";
			}
			echo "<td style='text-align: center;'>";
			echo "<p class='bulletin'>";
			if($bull_affich_nom_etab=="y"){
				echo "<span class=\"bgrand\">".$gepiSchoolName."</span>";
			}
			if($bull_affich_adr_etab=="y"){
				echo "<br />\n".$gepiSchoolAdress1."<br />\n".$gepiSchoolAdress2."<br />\n".$gepiSchoolZipCode." ".$gepiSchoolCity;
				if($bull_affiche_tel=="y"){echo "<br />\nTel: ".$gepiSchoolTel;}
				if($bull_affiche_fax=="y"){echo "<br />\nFax: ".$gepiSchoolFax;}
			}
			echo "</p>\n";

			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			echo "<br />";


			// On rajoute des lignes vides
			$n = 0;
			while ($n < $bull_ecart_bloc_nom) {
				echo "<br />";
				$n++;
			}

			if ($activer_photo_bulletin=='y' and $active_module_trombinoscopes=='y') {
				$photo=nom_photo($tab_bull['eleve'][$i]['elenoet']);
				//echo "$photo";
				if("$photo"!=""){
					$photo="../photos/eleves/".$photo;
					if(file_exists($photo)){
						$dimphoto=redimensionne_image($photo);
						echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />'."\n";
					}
				}
			}


			//affichage des données sur une seule ligne ou plusieurs
			if  ($bull_affiche_eleve_une_ligne == 'no') { // sur plusieurs lignes
				echo "<p class='bulletin'>\n";
				echo "<b><span class=\"bgrand\">".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom']."</span></b><br />";
				echo "Né";
				if (strtoupper($tab_bull['eleve'][$i]['sexe'])== "F") {echo "e";}
				echo "&nbsp;le&nbsp;".$tab_bull['eleve'][$i]['naissance'];
				//Eric Ajout
				echo "<br />";
				if ($tab_bull['eleve'][$i]['regime'] == "d/p") {echo "Demi-pensionnaire";}
				if ($tab_bull['eleve'][$i]['regime'] == "ext.") {echo "Externe";}
				if ($tab_bull['eleve'][$i]['regime'] == "int.") {echo "Interne";}
				if ($tab_bull['eleve'][$i]['regime'] == "i-e"){
					echo "Interne&nbsp;externé";
					if (strtoupper($tab_bull['eleve'][$i]['sexe'])!= "F") {echo "e";}
				}
				//Eric Ajout
				if ($bull_mention_doublant == 'yes'){
					if ($tab_bull['eleve'][$i]['doublant'] == 'R'){
					echo "<br />";
					echo "Redoublant";
					if (strtoupper($tab_bull['eleve'][$i]['sexe'])!= "F") {echo "e";}
					}
				}

				if ($bull_mention_nom_court == 'no') {
					//Eric Ajout et supp
					//echo "<BR />";
					//echo ", $current_classe";
				} else {
					echo "<br />";
					echo $tab_bull['eleve'][$i]['classe'];
				}
			}
			else { //sur une ligne
				echo "<p class='bulletin'>\n";
				echo "<b><span class=\"bgrand\">".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom']."</span></b><br />";
				echo "Né";
				if (strtoupper($tab_bull['eleve'][$i]['sexe'])== "F") {echo "e";}
				echo "&nbsp;le&nbsp;".$tab_bull['eleve'][$i]['naissance'];
				if ($tab_bull['eleve'][$i]['regime'] == "d/p") {echo ", Demi-pensionnaire";}
				if ($tab_bull['eleve'][$i]['regime'] == "ext.") {echo ", Externe";}
				if ($tab_bull['eleve'][$i]['regime'] == "int.") {echo ", Interne";}
				if ($tab_bull['eleve'][$i]['regime'] == "i-e"){
					echo ", Interne&nbsp;externé";
					if (strtoupper($tab_bull['eleve'][$i]['sexe'])!= "F") {echo "e";}
				}
				if ($bull_mention_doublant == 'yes'){
					if ($tab_bull['eleve'][$i]['doublant'] == 'R'){
						echo ", Redoublant";
						if (strtoupper($tab_bull['eleve'][$i]['sexe'])!= "F") {echo "e";}
					}
				}
				if ($bull_mention_nom_court == 'yes') {
					echo ", ".$tab_bull['eleve'][$i]['classe'];
				}
			}

			if($bull_affiche_INE_eleve=="y"){
				echo "<br />\n";
				echo "Numéro INE: ".$tab_bull['eleve'][$i]['no_gep'];
			}

			if($bull_affiche_etab=="y"){
				if ((isset($tab_bull['eleve'][$i]['etab_nom']))&&($tab_bull['eleve'][$i]['etab_nom']!='')) {
					echo "<br />\n";
					if ($tab_bull['eleve'][$i]['etab_id'] != '990') {
						if ($RneEtablissement != $tab_bull['eleve'][$i]['etab_id']) {
							echo "Etablissement d'origine : ";
							echo $tab_bull['eleve'][$i]['etab_niveau_nom']." ".$tab_bull['eleve'][$i]['etab_type']." ".$tab_bull['eleve'][$i]['etab_nom']." (".$tab_bull['eleve'][$i]['etab_cp']." ".$tab_bull['eleve'][$i]['etab_ville'].")\n";
						}
					} else {
						echo "Etablissement d'origine : ";
						echo "hors de France\n";
					}
				}
			}

			echo "</p>\n";

			echo "</div>\n";

			//echo "<spacer type='vertical' size='10'>";


			// Tableau contenant le nom de la classe, l'année et la période.
			echo "<table width='".$largeur2."%' ";
			if($addressblock_debug=="y"){echo "border='1' ";}
			echo "cellspacing='".$cellspacing."' cellpadding='".$cellpadding."'>\n";
			echo "<tr>\n";
			echo "<td class='empty'>\n";
			echo "&nbsp;\n";
			echo "</td>\n";
			echo "<td style='width:".$addressblock_classe_annee2."%;'>\n";
			echo "<p class='bulletin' align='center'><span class=\"bgrand\">Classe de ".$tab_bull['eleve'][$i]['classe_nom_complet']."<br />Année scolaire ".$gepiYear."</span><br />\n";
			$temp = strtolower($tab_bull["nom_periode"]);
			echo "Bulletin&nbsp;";
			if($genre_periode=="M"){
				echo "du ";
			}
			else{
				echo "de la ";
			}
			echo "$temp</p>";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			// Pour que le tableau des appréciations ne vienne pas s'encastrer dans les DIV float:
			echo "<div style='clear: both; font-size: xx-small;'>&nbsp;</div>\n";

			// Fin du cadre entête:
			echo "</div>\n";

		}
		else{
			//-------------------------------
			// Maintenant, on affiche l'en-tête : Les données de l'élève, et l'adresse du lycée.
			// sans bloc adresse responsable
			//-------------------------------

			echo "<table width='$largeurtableau' border='0' cellspacing='".$cellspacing."' cellpadding='".$cellpadding."'>\n";

			echo "<tr>\n";
			echo "<td style=\"width: 30%;\">\n";
			if ($activer_photo_bulletin=='y' and $active_module_trombinoscopes=='y') {
				$photo=nom_photo($tab_bull['eleve'][$i]['elenoet']);
				//echo "$photo";
				if("$photo"!=""){
					$photo="../photos/eleves/".$photo;
					if(file_exists($photo)){
						echo '<img src="'.$photo.'" style="width: 60px; height: 80px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />'."\n";
					}
				}
			}

				//affichage des données sur une seule ligne ou plusieurs
			if  ($bull_affiche_eleve_une_ligne == 'no') { // sur plusieurs lignes
				echo "<p class='bulletin'>\n";
				echo "<b><span class=\"bgrand\">".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom']."</span></b><br />";
				echo "Né";
				if (strtoupper($tab_bull['eleve'][$i]['sexe'])== "F") {echo "e";}
				echo "&nbsp;le&nbsp;".$tab_bull['eleve'][$i]['naissance'];
				//Eric Ajout
				echo "<br />";
				if ($tab_bull['eleve'][$i]['regime'] == "d/p") {echo "Demi-pensionnaire";}
				if ($tab_bull['eleve'][$i]['regime'] == "ext.") {echo "Externe";}
				if ($tab_bull['eleve'][$i]['regime'] == "int.") {echo "Interne";}
				if ($tab_bull['eleve'][$i]['regime'] == "i-e"){
					echo "Interne&nbsp;externé";
					if (strtoupper($tab_bull['eleve'][$i]['sexe'])!= "F") {echo "e";}
				}
				//Eric Ajout
				if ($bull_mention_doublant == 'yes'){
					if ($tab_bull['eleve'][$i]['doublant'] == 'R'){
					echo "<br />";
					echo "Redoublant";
					if (strtoupper($tab_bull['eleve'][$i]['sexe'])!= "F") {echo "e";}
					}
				}


				if ($bull_mention_nom_court == 'no') {
					//Eric Ajout et supp
					//echo "<BR />";
					//echo ", $current_classe";
				} else {
					echo "<br />";
					echo $tab_bull['eleve'][$i]['classe'];
				}

			} else { //sur une ligne
				echo "<p class='bulletin'>\n";
				echo "<b><span class=\"bgrand\">".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom']."</span></b><br />";
				echo "Né";
				if (strtoupper($tab_bull['eleve'][$i]['sexe'])== "F") {echo "e";}
				echo "&nbsp;le&nbsp;".$tab_bull['eleve'][$i]['naissance'];

				if ($tab_bull['eleve'][$i]['regime'] == "d/p") {echo ", Demi-pensionnaire";}
				if ($tab_bull['eleve'][$i]['regime'] == "ext.") {echo ", Externe";}
				if ($tab_bull['eleve'][$i]['regime'] == "int.") {echo ", Interne";}
				if ($tab_bull['eleve'][$i]['regime'] == "i-e"){
					echo ", Interne&nbsp;externé";
					if (strtoupper($tab_bull['eleve'][$i]['sexe'])!= "F") {echo "e";}
				}
				//Eric Ajout
				if ($bull_mention_doublant == 'yes'){
					if ($tab_bull['eleve'][$i]['doublant'] == 'R'){
					echo ", Redoublant";
					if (strtoupper($tab_bull['eleve'][$i]['sexe'])!= "F") {echo "e";}
					}
				}
				if ($bull_mention_nom_court == 'yes') {
					echo ", ".$tab_bull['eleve'][$i]['classe'];
				}
			}


			if($bull_affiche_etab=="y"){
				if ((isset($tab_bull['eleve'][$i]['etab_nom']))&&($tab_bull['eleve'][$i]['etab_nom']!='')) {
					echo "<br />\n";
					if ($tab_bull['eleve'][$i]['etab_id'] != '990') {
						if ($RneEtablissement != $tab_bull['eleve'][$i]['etab_id']) {
							echo "Etablissement d'origine : ";
							echo $tab_bull['eleve'][$i]['etab_niveau_nom']." ".$tab_bull['eleve'][$i]['etab_type']." ".$tab_bull['eleve'][$i]['etab_nom']." (".$tab_bull['eleve'][$i]['etab_cp']." ".$tab_bull['eleve'][$i]['etab_ville'].")\n";
						}
					} else {
						echo "Etablissement d'origine : ";
						echo "hors de France\n";
					}
				}
			}

			echo "</p></td>\n<td style=\"width: 40%;text-align: center;\">\n";

			if ($tab_bull['affiche_adresse'] != "y") {
				echo "<p class='bulletin'><span class=\"bgrand\">Classe de ".$tab_bull['eleve'][$i]['classe_nom_complet']."<br />Année scolaire ".$gepiYear."</span><br />\n";
				$temp = strtolower($tab_bull['nom_periode']);
				echo "Bulletin&nbsp;";
				if($genre_periode=="M"){
					echo "du ";
				}
				else{
					echo "de la ";
				}
				echo "$temp</p>\n";
			} else {
				echo "&nbsp;";
			}

			$nom_fic_logo = $logo_etab;
			$nom_fic_logo_c = "../images/".$nom_fic_logo;
			if (($nom_fic_logo != '') and (file_exists($nom_fic_logo_c))) {
				echo "</td>\n<td style=\"text-align: right;\"><img src=\"".$nom_fic_logo_c."\" border=\"0\" alt=\"Logo\" />";
			} else {
				echo "</td>\n<td>&nbsp;";
			}
			echo "</td>\n";
			echo "<td style=\"width: 20%;text-align: center;\">";
			echo "<p class='bulletin'>";
			if($bull_affich_nom_etab=="y"){
				echo "<span class=\"bgrand\">".$gepiSchoolName."</span>";
			}
			if($bull_affich_adr_etab=="y"){
				//echo "<span class=\"bgrand\">".$gepiSchoolName."</span>";
				if($bull_affich_nom_etab=="y"){echo "<br />\n";}
				echo $gepiSchoolAdress1."<br />\n";
				echo $gepiSchoolAdress2."<br />\n";
				echo $gepiSchoolZipCode." ".$gepiSchoolCity;

				if($bull_affiche_tel=="y"){echo "<br />\nTel: ".$gepiSchoolTel;}
				if($bull_affiche_fax=="y"){echo "<br />\nFax: ".$gepiSchoolFax;}
			}
			echo "</p>\n";

			echo "</td>\n</tr>\n</table>\n";
			//-------------------------------
			// Fin de l'en-tête
		}

		echo "\n<!-- Fin de l'affichage de l'entête du bulletin n°$bulletin pour ".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom'].", ".$tab_bull['eleve'][$i]['classe']." -->\n\n";

		// On rajoute des lignes vides
		$n = 0;
		while ($n < $bull_ecart_entete) {
			echo "<br />\n";
			$n++;
		}



		echo "\n<!-- Début de l'affichage du tableau des matières du bulletin n°$bulletin pour ".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom'].", ".$tab_bull['eleve'][$i]['classe']." -->\n\n";
        //=============================================

		// Tableau des matières/notes/appréciations

		include ($fichier_bulletin);

        //=============================================
		echo "\n<!-- Fin de l'affichage du tableau des matières du bulletin n°$bulletin pour ".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom'].", ".$tab_bull['eleve'][$i]['classe']." -->\n\n";


		// Absences et retards
		//if($tab_bull['affiche_absences']=='y') {
		if($bull_affiche_absences=='y') {
			echo "\n<!-- Début de l'affichage du tableau des absences du bulletin n°$bulletin pour ".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom'].", ".$tab_bull['eleve'][$i]['classe']." -->\n\n";

            echo "<table width='$largeurtableau' border='0' cellspacing='".$cellspacing."' cellpadding='".$cellpadding."'>\n";
			echo "<tr>\n";
			echo "<td style='vertical-align: top;'>\n";
			echo "<p class='bulletin'>";
			if ($tab_bull['eleve'][$i]['eleve_absences'] == '0') {
				echo "<i>Aucune demi-journée d'absence</i>.";
			} else {
				echo "<i>Nombre de demi-journées d'absence ";
				if ($tab_bull['eleve'][$i]['eleve_nj'] == '0') {echo "justifiées ";}
				echo ": </i><b>".$tab_bull['eleve'][$i]['eleve_absences']."</b>";
				if ($tab_bull['eleve'][$i]['eleve_nj'] != '0') {
					echo " (dont <b>".$tab_bull['eleve'][$i]['eleve_nj']."</b> non justifiée"; if ($tab_bull['eleve'][$i]['eleve_nj'] != '1') {echo "s";}
					echo ")";
				}
				echo ".";
			}
			if ($tab_bull['eleve'][$i]['eleve_retards'] != '0') {
				echo "<i> Nombre de retards : </i><b>".$tab_bull['eleve'][$i]['eleve_retards']."</b>";
			}
			echo "  (C.P.E. chargé";

			if($tab_bull['eleve'][$i]['cperesp_civilite']!="M.") {
				echo "e";
			}

			echo " du suivi : ". affiche_utilisateur($tab_bull['eleve'][$i]['cperesp_login'],$tab_bull['id_classe']) . ")";
			if ($tab_bull['eleve'][$i]['appreciation_absences']!="") {echo "<br />".texte_html_ou_pas($tab_bull['eleve'][$i]['appreciation_absences']);}
			echo "</p>\n";
			echo "</td>\n</tr>\n</table>\n";

			echo "\n<!-- Fin de l'affichage du tableau des absences du bulletin n°$bulletin pour ".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom'].", ".$tab_bull['eleve'][$i]['classe']." -->\n\n";

		}



		//=============================================


		// Avis du conseil de classe à ramener par là

		if (($bull_affiche_avis == 'y')||($bull_affiche_signature == 'y')) {

			echo "\n<!-- Début de l'affichage du tableau de l'avis du conseil/signature du bulletin n°$bulletin pour ".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom'].", ".$tab_bull['eleve'][$i]['classe']." -->\n\n";

			// Tableau de l'avis des conseil de classe
			echo "<table $class_bordure width='$largeurtableau' border='1' cellspacing='".$cellspacing."' cellpadding='".$cellpadding."'>\n";
			echo "<tr>\n";
		}

        if ($bull_affiche_avis == 'y') {
			//
			// Case de gauche : avis des conseils de classe
			//
			echo "<td style='vertical-align: top; text-align: left;'>\n";
			// 1) l'avis
			echo "<span class='bulletin'><i>Avis du conseil de classe:</i></span><br />\n";

			if($tab_bull['avis'][$i]!="") {
				echo "<span class='avis_bulletin'>";
				/*
				if((strstr($tab_bull['avis'][$i],">"))||(strstr($tab_bull['avis'][$i],"<"))){
					echo $tab_bull['avis'][$i];
				}
				else{
					echo nl2br($tab_bull['avis'][$i]);
				}
				*/
				echo texte_html_ou_pas($tab_bull['avis'][$i]);
				echo "</span>";
				if($bull_affiche_signature == 'y'){
					echo "<br />\n";
				}
			}
			else {
				echo "&nbsp;";
				// Si il n'y a pas d'avis, on rajoute des lignes vides selon les paramètres d'impression
				$n = 0;
				if ($bull_espace_avis >0){
					while ($n < $bull_espace_avis) {
						echo "<br />\n";
						$n++;
					}
				}
			}
		}
        elseif ($bull_affiche_signature == 'y') {
            echo "<td style=\"vertical-align: top;\">";
        }

        if ($bull_affiche_signature == 'y') {
            // 2) Le nom du professeur principal
			if(isset($tab_bull['eleve'][$i]['pp']['login'])) {
				echo "<b>".ucfirst($gepi_prof_suivi)."</b> ";
				echo "<i>".affiche_utilisateur($tab_bull['eleve'][$i]['pp']['login'],$tab_bull['eleve'][$i]['id_classe'])."</i>";
			}

			echo "</td>\n";
			//
			// Case de droite : paraphe du proviseur
			//
			echo "<td style='vertical-align: top; text-align: left;' width='30%'>\n";
			echo "<span class='bulletin'><b>".$tab_bull['formule']."</b>:</span><br />";
			echo "<span class='bulletin'><i>".$tab_bull['suivi_par']."</i></span>";
		}

        // Si une des deux variables 'bull_affiche_avis' ou 'bull_affiche_signature' est à 'y', il faut fermer le tableau
        if (($bull_affiche_avis == 'y')||($bull_affiche_signature == 'y')) {
            echo "</td>\n";
            // Fin du tableau
            echo "</tr>\n";
			echo "</table>\n";

			echo "\n<!-- Fin de l'affichage du tableau de l'avis du conseil/signature du bulletin n°$bulletin pour ".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom'].", ".$tab_bull['eleve'][$i]['classe']." -->\n\n";

        }
		//================================



		// Affichage de la formule de bas de page

		if (($bull_formule_bas != '') and ($bull_affiche_formule == 'y')) {
			// Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
			echo "<table width='$largeurtableau' style='margin-left:5px; margin-right:5px;' border='0' cellspacing='".$cellspacing."' cellpadding='".$cellpadding."'>\n";
			echo "<tr>";
			echo "<td><p align='center' class='bulletin'>".$bull_formule_bas."</p></td>\n";
			echo "</tr></table>";
		}


		echo "\n<!-- Fin du bulletin n°$bulletin pour ".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom'].", ".$tab_bull['eleve'][$i]['classe']." -->\n\n";


		// Insertion du relevé de notes si réclamé:
		if(count($tab_rel)!=0) {
			echo "<p class='saut'>&nbsp;</p>\n";
			// Il y a un décalage sur les indices dans le cas où on n'imprime pas la classe entière
			//releve_html($tab_rel,$i,$bulletin);
			if(isset($tab_rel['eleve'])) {
				for($k=0;$k<count($tab_rel['eleve']);$k++) {
					if($tab_rel['eleve'][$k]['login']==$tab_bull['eleve'][$i]['login']) {
						releve_html($tab_rel,$k,$bulletin);
						break;
					}
				}
			}
			else {
				echo "<p style='color:red;'>Il semble que le tableau des relevés de notes soit vide.</p>\n";
			}
		}

		if(($bulletin==0)&&($nb_bulletins==2)){
			echo "<p class='saut'>&nbsp;</p>\n";
		}

	}
}

//================================================================
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//================================================================
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//================================================================
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//================================================================
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//================================================================

// $tab_bulletin[$id_classe][$periode_num]
// $i indice élève
function bulletin_html_v0($tab_bull,$i) {
	global $gepi_prof_suivi,
		$bull_affiche_aid,
		$bull_affiche_absences,
		$bull_intitule_app,
		$genre_periode,
		$bull_affiche_tel,
		$gepiSchoolTel,
		$bull_affiche_fax,
		$gepiSchoolFax,
		$RneEtablissement,
		$gepiSchoolName,
		$gepiSchoolAdress1,
		$gepiSchoolAdress2,
		$gepiSchoolZipCode,
		$gepiSchoolCity,
		$gepiSchoolPays;

	//================================

	echo "<p style='border:1px solid black;'>Elève ".$tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom'];
	echo " (<i>né";
	if(strtoupper($tab_bull['eleve'][$i]['sexe'])=="F") {echo "e";}
	echo " le ".formate_date($tab_bull['eleve'][$i]['naissance'])."</i>).<br />\n";

	if (isset($tab_bull['eleve'][$i]['regime'])) {
		echo ucfirst(regime($tab_bull['eleve'][$i]['regime'])).".<br />\n";
	}

	if ((isset($tab_bull['eleve'][$i]['doublant']))&&($tab_bull['eleve'][$i]['doublant']!="-")) {
		echo "Redoublant";
		if(strtoupper($tab_bull['eleve'][$i]['sexe'])=="F") {echo "e";}
		echo ".<br />\n";
	}

	echo "INE: ".$tab_bull['eleve'][$i]['no_gep'].".</p>\n";

	//================================

	echo "<table class='boireaus'>\n";
	echo "<tr><td colspan='".count($tab_bull['eleve'][$i]['resp'])."'>Responsable(s):</td></tr>\n";
	echo "<tr>\n";
	for($k=0;$k<count($tab_bull['eleve'][$i]['resp']);$k++) {
		echo "<td>\n";
		echo "<p>".$tab_bull['eleve'][$i]['resp'][$k]['civilite']." ".$tab_bull['eleve'][$i]['resp'][$k]['nom']." ".$tab_bull['eleve'][$i]['resp'][$k]['prenom']."<br />\n";

		if((isset($tab_bull['eleve'][$i]['resp'][$k]['adr1']))&&($tab_bull['eleve'][$i]['resp'][$k]['adr1']!="")) {
			echo $tab_bull['eleve'][$i]['resp'][$k]['adr1']."<br />\n";
		}

		if((isset($tab_bull['eleve'][$i]['resp'][$k]['adr2']))&&($tab_bull['eleve'][$i]['resp'][$k]['adr2']!="")) {
			echo $tab_bull['eleve'][$i]['resp'][$k]['adr2']."<br />\n";
		}

		if((isset($tab_bull['eleve'][$i]['resp'][$k]['adr3']))&&($tab_bull['eleve'][$i]['resp'][$k]['adr3']!="")) {
			echo $tab_bull['eleve'][$i]['resp'][$k]['adr3']."<br />\n";
		}

		if((isset($tab_bull['eleve'][$i]['resp'][$k]['adr4']))&&($tab_bull['eleve'][$i]['resp'][$k]['adr4']!="")) {
			echo $tab_bull['eleve'][$i]['resp'][$k]['adr4']."<br />\n";
		}

		if((isset($tab_bull['eleve'][$i]['resp'][$k]['cp']))&&($tab_bull['eleve'][$i]['resp'][$k]['cp']!="")) {
			echo $tab_bull['eleve'][$i]['resp'][$k]['cp']." ";
		}

		if((isset($tab_bull['eleve'][$i]['resp'][$k]['commune']))&&($tab_bull['eleve'][$i]['resp'][$k]['commune']!="")) {
			echo $tab_bull['eleve'][$i]['resp'][$k]['commune']."<br />\n";
		}
		else {
			echo "-<br />\n";
		}

		if((isset($tab_bull['eleve'][$i]['resp'][$k]['pays']))&&($tab_bull['eleve'][$i]['resp'][$k]['pays']!="")) {
			echo $tab_bull['eleve'][$i]['resp'][$k]['pays'];
		}
		echo "</p>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";
	echo "</table>\n";

	// Comparer $tab_bull['eleve'][$i]['resp'][$k]['adr_id'] d'une part
	// puis si les adr_id sont différents, les $tab_bull['eleve'][$i]['resp'][$k]['adr1'],...

	//================================

	echo "<br />\n";

	//================================

	echo "<table class='boireaus'>\n";

	echo "<thead>\n";
	echo "<tr>\n";
	echo "<th>Matière</th>\n";
	echo "<th>Note</th>\n";
	echo "<th>min</th>\n";
	echo "<th>moy</th>\n";
	echo "<th>max</th>\n";
	if ($tab_bull['affiche_coef'] == 'y') {
		echo "<th>coef</th>\n";
	}
	if($tab_bull['affiche_rang']=='y') {
		echo "<th>rang</th>\n";
	}
	if($tab_bull['affiche_nbdev']=="y"){
		echo "<th>Nb.dev</th>\n";
	}

	if ($tab_bull['affiche_graph'] == 'y') {
		echo "<th>Niveaux<br />ABC</th>\n";
	}

	echo "<th>$bull_intitule_app</th>\n";
	echo "</tr>\n";
	echo "</thead>\n";
	echo "<tbody>\n";



	if($bull_affiche_aid=="y") {
		if(isset($tab_bull['eleve'][$i]['aid_b'])) {
			for($z=0;$z<count($tab_bull['eleve'][$i]['aid_b']);$z++) {
				echo "<tr>\n";


				echo "<td>".htmlentities($tab_bull['eleve'][$i]['aid_b'][$z]['nom_complet']);
				echo "<br />";
				$cpt=0;
				foreach($tab_bull['eleve'][$i]['aid_b'][$z]['aid_prof_resp_login'] as $current_aid_prof_login) {
					if($cpt>0) {echo ", ";}
					echo "<i>".affiche_utilisateur($current_aid_prof_login,$tab_bull['id_classe'])."</i>";
					$cpt++;
				}
				echo "</td>\n";

				echo "<td style='font-weight:bold;'>";
				if($tab_bull['eleve'][$i]['aid_b'][$z]['aid_statut']=="") {
					echo $tab_bull['eleve'][$i]['aid_b'][$z]['aid_note'];
				}
				else {
					echo $tab_bull['eleve'][$i]['aid_b'][$z]['aid_statut'];
				}
				echo "</td>\n";

				echo "<td>".$tab_bull['eleve'][$i]['aid_b'][$z]['aid_note_min']."</td>\n";
				echo "<td>".$tab_bull['eleve'][$i]['aid_b'][$z]['aid_note_moyenne']."</td>\n";
				echo "<td>".$tab_bull['eleve'][$i]['aid_b'][$z]['aid_note_max']."</td>\n";

				if($tab_bull['affiche_coef']=='y') {
					echo "<td>-</td>\n";
				}

				if($tab_bull['affiche_rang']=='y') {
					echo "<td>-</td>\n";
				}

				if($tab_bull['affiche_nbdev']=="y"){
					echo "<td>-</td>\n";
				}


				if ($tab_bull['affiche_graph'] == 'y') {
					echo "<td>";
					if((isset($tab_bull['eleve'][$i]['aid_b'][$z]['place_eleve']))&&($tab_bull['eleve'][$i]['aid_b'][$z]['place_eleve']!="")) {
						echo "<img height='40' width='40' src='../visualisation/draw_artichow4.php?place_eleve=".$tab_bull['eleve'][$i]['aid_b'][$z]['place_eleve'].
							"&amp;temp1=".$tab_bull['eleve'][$i]['aid_b'][$z]['quartile1_classe'].
							"&amp;temp2=".$tab_bull['eleve'][$i]['aid_b'][$z]['quartile2_classe'].
							"&amp;temp3=".$tab_bull['eleve'][$i]['aid_b'][$z]['quartile3_classe'].
							"&amp;temp4=".$tab_bull['eleve'][$i]['aid_b'][$z]['quartile4_classe'].
							"&amp;temp5=".$tab_bull['eleve'][$i]['aid_b'][$z]['quartile5_classe'].
							"&amp;temp6=".$tab_bull['eleve'][$i]['aid_b'][$z]['quartile6_classe'].
							"&amp;nb_data=7' alt='Quartiles' />\n";
					}
					else {
						echo "-";
					}
					echo "</td>\n";
				}


				echo "<td>";
				echo "<b>".$tab_bull['eleve'][$i]['aid_b'][$z]['aid_nom']."</b><br />";
				echo $tab_bull['eleve'][$i]['aid_b'][$z]['aid_appreciation'];
				echo "</td>\n";




				echo "</tr>\n";
			}
		}
	}


	// Calcul du rowspan pour les categories de matières
	// A FAIRE

	$alt=1;
	$categorie_precedente="";
	for($j=0;$j<count($tab_bull['groupe']);$j++) {
		// Si l'élève suit l'option, sa note est affectée (éventuellement vide)
		if(isset($tab_bull['note'][$j][$i])) {

			if($tab_bull['affiche_categories']) {
				if($categorie_precedente!=$tab_bull['cat_id'][$j]) {
					echo "<tr>\n";

					echo "<td>".$tab_bull['nom_cat_complet'][$j]."</td>\n";

					if($tab_bull['affiche_moyenne'][$j]==1) {
						// Moyenne catégorie élève
						echo "<td style='font-weight:bold;'>".$tab_bull['moy_cat_eleve'][$i][$tab_bull['cat_id'][$j]]."</td>\n";
						// Min
						echo "<td>-</td>\n";
						// Moyenne catégorie classe
						echo "<td>".$tab_bull['moy_cat_classe'][$i][$tab_bull['cat_id'][$j]]."</td>\n";
					}
					else {
						// Moyenne catégorie élève
						echo "<td>-</td>\n";
						// Min
						echo "<td>-</td>\n";
						// Moyenne catégorie classe
						echo "<td>-</td>\n";
					}

					// Max
					echo "<td>-</td>\n";

					if($tab_bull['affiche_coef']=='y') {
						echo "<td>-</td>\n";
					}

					if($tab_bull['affiche_rang']=='y') {
						echo "<td>-</td>\n";
					}

					if($tab_bull['affiche_nbdev']=="y"){
						echo "<td>-</td>\n";
					}

					// Niveaux/quartiles
					if ($tab_bull['affiche_graph'] == 'y') {
						echo "<td>-</td>\n";
					}

					// Appréciation
					echo "<td>-</td>\n";

					echo "</tr>\n";

					$categorie_precedente=$tab_bull['cat_id'][$j];
				}
			}

			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>".htmlentities($tab_bull['groupe'][$j]['matiere']['nom_complet']);
			echo "<br />";
			$cpt=0;
			foreach($tab_bull['groupe'][$j]["profs"]["list"] as $current_prof_login) {
				if($cpt>0) {echo ", ";}
				echo "<i>".affiche_utilisateur($current_prof_login,$tab_bull['id_classe'])."</i>";
				$cpt++;
			}
			echo "</td>\n";

			echo "<td style='font-weight:bold;'>";
			if($tab_bull['statut'][$j][$i]=="") {
				echo $tab_bull['note'][$j][$i];
			}
			else {
				echo $tab_bull['statut'][$j][$i];
			}
			echo "</td>\n";

			echo "<td>".$tab_bull['moy_min_classe_grp'][$j]."</td>\n";
			echo "<td>".$tab_bull['moy_classe_grp'][$j]."</td>\n";
			echo "<td>".$tab_bull['moy_max_classe_grp'][$j]."</td>\n";

			if($tab_bull['affiche_coef']=='y') {
				echo "<td>".$tab_bull['coef_eleve'][$i][$j]."</td>\n";
			}

			if($tab_bull['affiche_rang']=='y') {
				echo "<td>";
				if(isset($tab_bull['rang'][$i][$j])) {
					echo $tab_bull['rang'][$i][$j]."/".$tab_bull['groupe'][$j]['effectif'];
				}
				else {
					echo "-";
				}
				echo "</td>\n";
			}

			if($tab_bull['affiche_nbdev']=="y"){
				echo "<td>".$tab_bull['nbct'][$j][$i]."/".$tab_bull['groupe'][$j]['nbct']."</td>\n";
			}


			if ($tab_bull['affiche_graph'] == 'y') {
				echo "<td>";
				if((isset($tab_bull['place_eleve'][$j][$i]))&&($tab_bull['place_eleve'][$j][$i]!="")) {
					//echo $place_eleve_classe[$i]." ";
					echo "<img height='40' width='40' src='../visualisation/draw_artichow4.php?place_eleve=".$tab_bull['place_eleve'][$j][$i].
						"&amp;temp1=".$tab_bull['quartile1_grp'][$j].
						"&amp;temp2=".$tab_bull['quartile2_grp'][$j].
						"&amp;temp3=".$tab_bull['quartile3_grp'][$j].
						"&amp;temp4=".$tab_bull['quartile4_grp'][$j].
						"&amp;temp5=".$tab_bull['quartile5_grp'][$j].
						"&amp;temp6=".$tab_bull['quartile6_grp'][$j].
						"&amp;nb_data=7' alt='Quartiles' />\n";
				}
				else {
					echo "-";
				}
				echo "</td>\n";
			}


			echo "<td>";
			//=============================
			// Récupérer les sous-matières
			if(isset($tab_bull['groupe'][$j][$i]['cn_note'])) {
				$n = 0;
				while ($n < count($tab_bull['groupe'][$j][$i]['cn_note'])) {
					echo $tab_bull['groupe'][$j][$i]['cn_nom'][$n].":".$tab_bull['groupe'][$j][$i]['cn_note'][$n]." - ";
					$n++;
				}
				// Présentation avec rowspan à gérer dès le début de la ligne matière.
			}
			//=============================

			//echo "\$tab_bull['groupe'][$j][$i]['app']=<br />";
			//echo $tab_bull['groupe'][$j][$i]['app'];
			echo $tab_bull['app'][$j][$i];
			echo "</td>\n";



			echo "</tr>\n";
		}
	}

	if($bull_affiche_aid=="y") {
		if(isset($tab_bull['eleve'][$i]['aid_e'])) {
			for($z=0;$z<count($tab_bull['eleve'][$i]['aid_e']);$z++) {
				echo "<tr>\n";


				echo "<td>".htmlentities($tab_bull['eleve'][$i]['aid_e'][$z]['nom_complet']);
				echo "<br />";
				$cpt=0;
				foreach($tab_bull['eleve'][$i]['aid_e'][$z]['aid_prof_resp_login'] as $current_aid_prof_login) {
					if($cpt>0) {echo ", ";}
					echo "<i>".affiche_utilisateur($current_aid_prof_login,$tab_bull['id_classe'])."</i>";
					$cpt++;
				}
				echo "</td>\n";

				echo "<td style='font-weight:bold;'>";
				if($tab_bull['eleve'][$i]['aid_e'][$z]['aid_statut']=="") {
					echo $tab_bull['eleve'][$i]['aid_e'][$z]['aid_note'];
				}
				else {
					echo $tab_bull['eleve'][$i]['aid_e'][$z]['aid_statut'];
				}
				echo "</td>\n";

				echo "<td>".$tab_bull['eleve'][$i]['aid_e'][$z]['aid_note_min']."</td>\n";
				echo "<td>".$tab_bull['eleve'][$i]['aid_e'][$z]['aid_note_moyenne']."</td>\n";
				echo "<td>".$tab_bull['eleve'][$i]['aid_e'][$z]['aid_note_max']."</td>\n";

				if($tab_bull['affiche_coef']=='y') {
					echo "<td>-</td>\n";
				}

				if($tab_bull['affiche_rang']=='y') {
					echo "<td>-</td>\n";
				}

				if($tab_bull['affiche_nbdev']=="y"){
					echo "<td>-</td>\n";
				}


				if ($tab_bull['affiche_graph'] == 'y') {
					echo "<td>";
					if((isset($tab_bull['eleve'][$i]['aid_e'][$z]['place_eleve']))&&($tab_bull['eleve'][$i]['aid_e'][$z]['place_eleve']!="")) {
						echo "<img height='40' width='40' src='../visualisation/draw_artichow4.php?place_eleve=".$tab_bull['eleve'][$i]['aid_e'][$z]['place_eleve'].
							"&amp;temp1=".$tab_bull['eleve'][$i]['aid_e'][$z]['quartile1_classe'].
							"&amp;temp2=".$tab_bull['eleve'][$i]['aid_e'][$z]['quartile2_classe'].
							"&amp;temp3=".$tab_bull['eleve'][$i]['aid_e'][$z]['quartile3_classe'].
							"&amp;temp4=".$tab_bull['eleve'][$i]['aid_e'][$z]['quartile4_classe'].
							"&amp;temp5=".$tab_bull['eleve'][$i]['aid_e'][$z]['quartile5_classe'].
							"&amp;temp6=".$tab_bull['eleve'][$i]['aid_e'][$z]['quartile6_classe'].
							"&amp;nb_data=7' alt='Quartiles' />\n";
					}
					else {
						echo "-";
					}
					echo "</td>\n";
				}


				echo "<td>";
				echo "<b>".$tab_bull['eleve'][$i]['aid_e'][$z]['aid_nom']."</b><br />";
				echo $tab_bull['eleve'][$i]['aid_e'][$z]['aid_appreciation'];
				echo "</td>\n";




				echo "</tr>\n";
			}
		}
	}

	if($tab_bull['display_moy_gen']=='y') {
		// Moyenne générale
		echo "<tr>\n";
		echo "<th>Moyennes</th>\n";
		echo "<td style='font-weight:bold;'>".$tab_bull['moy_gen_eleve'][$i]."</td>\n";
		echo "<td>".$tab_bull['moy_min_classe']."</td>\n";
		echo "<td>".$tab_bull['moy_gen_classe'][$i]."<br /><b>".$tab_bull['moy_generale_classe']."</b></td>\n";
		echo "<td>".$tab_bull['moy_max_classe']."</td>\n";
		// Coef
		if($tab_bull['affiche_coef']=='y') {
			echo "<td>-</td>\n";
		}
		// Rang
		if($tab_bull['affiche_rang']=='y') {
			echo "<td>";
			if(isset($tab_bull['rang_classe'][$i])) {
				echo $tab_bull['rang_classe'][$i]."/".$tab_bull['eff_classe'];
			}
			else {
				echo "-";
			}
			echo "</td>\n";
		}

		// Nb dev
		if($tab_bull['affiche_nbdev']=='y') {
			echo "<td>-</td>\n";
		}

		if ($tab_bull['affiche_graph']=='y') {
			echo "<td>";

			if((isset($tab_bull['place_eleve_classe'][$i]))&&($tab_bull['place_eleve_classe'][$i]!="")) {
				//echo $place_eleve_classe[$i]." ";
				echo "<img height='40' width='40' src='../visualisation/draw_artichow4.php?place_eleve=".$tab_bull['place_eleve_classe'][$i].
					"&amp;temp1=".$tab_bull['quartile1_classe_gen'].
					"&amp;temp2=".$tab_bull['quartile2_classe_gen'].
					"&amp;temp3=".$tab_bull['quartile3_classe_gen'].
					"&amp;temp4=".$tab_bull['quartile4_classe_gen'].
					"&amp;temp5=".$tab_bull['quartile5_classe_gen'].
					"&amp;temp6=".$tab_bull['quartile6_classe_gen'].
					"&amp;nb_data=7' alt='Quartiles' />\n";
			}
			else {
				echo "-";
			}

			echo "</td>\n";
		}

		echo "<td>\n";
		//echo $tab_bull['avis'][$i];
		echo "-";
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</tbody>\n";

	echo "</table>\n";
	//================================

	// Absences et retards
	//if($tab_bull['affiche_absences']=='y') {
	if($bull_affiche_absences=='y') {
		echo "<table>\n";
		echo "<tr>\n";
		echo "<td style='vertical-align: top;'>\n";
		if ($tab_bull['eleve'][$i]['eleve_absences'] == '0') {
			echo "<i>Aucune demi-journée d'absence</i>.";
		} else {
			echo "<i>Nombre de demi-journées d'absence ";
			if ($tab_bull['eleve'][$i]['eleve_nj'] == '0') {echo "justifiées ";}
			echo ": </i><b>".$tab_bull['eleve'][$i]['eleve_absences']."</b>";
			if ($tab_bull['eleve'][$i]['eleve_nj'] != '0') {
				echo " (dont <b>".$tab_bull['eleve'][$i]['eleve_nj']."</b> non justifiée"; if ($tab_bull['eleve'][$i]['eleve_nj'] != '1') {echo "s";}
				echo ")";
			}
			echo ".";
		}
		if ($tab_bull['eleve'][$i]['eleve_retards'] != '0') {
			echo "<i> Nombre de retards : </i><b>".$tab_bull['eleve'][$i]['eleve_retards']."</b>";
		}
		echo "  (C.P.E. chargé";

		if($tab_bull['eleve'][$i]['cperesp_civilite']!="M.") {
			echo "e";
		}

		echo " du suivi : ". affiche_utilisateur($tab_bull['eleve'][$i]['cperesp_login'],$tab_bull['id_classe']) . ")";
		if ($tab_bull['eleve'][$i]['appreciation_absences']!="") {echo "<br />".$tab_bull['eleve'][$i]['appreciation_absences'];}
		echo "</td>\n</tr>\n</table>\n";
	}



	//=============================================


	// Avis du conseil de classe à ramener par là

	echo "<table class='boireaus'>\n";

	echo "<tr>\n";
	echo "<td style='vertical-align: top; text-align: left;'>\n";
	echo "<p><i>Avis du conseil de classe:</i><br />\n";

	echo $tab_bull['avis'][$i]."<br />\n";

	// Prof principal
	if(isset($tab_bull['eleve'][$i]['pp']['login'])) {
		echo "<b>".ucfirst($gepi_prof_suivi)."</b> ";
		echo "<i>".affiche_utilisateur($tab_bull['eleve'][$i]['pp']['login'],$tab_bull['eleve'][$i]['id_classe'])."</i>";
	}

	echo "</p>\n";
	echo "</td>\n";

	echo "<td style='vertical-align: top; text-align: left;'>\n";
	echo "<b>".$tab_bull['formule']."</b>:<br />";
	echo "<i>".$tab_bull['suivi_par']."</i>";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";
	//================================
}


function bulletin_pdf($tab_bull,$i,$tab_rel) {
	global
		//============================================
		// Paramètres généraux:
		// En admin, dans Gestion générale/Configuration générale
		$gepi_prof_suivi,

		$RneEtablissement,
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

		// Paramètre transmis depuis la page d'impression des bulletins
		$un_seul_bull_par_famille,

		// Datation du bulletin (paramètre initié dans l'entête du bulletin PDF)
		$date_bulletin,

		// Paramètres du modèle PDF
		$tab_modele_pdf,

		// Objet PDF initié hors de la présente fonction donnant la page du bulletin pour un élève
		$pdf;
		//=========================================

	// Préparation des lignes d'adresse

	// ON N'UTILISE PAS LE CHAMP adr4 DE L'ADRESSE DANS resp_adr
	// IL FAUDRA VOIR COMMENT LE RECUPERER

	if (!isset($tab_bull['eleve'][$i]['resp'][0])) {
		//$tab_adr_ligne1[0]="<font color='red'><b>ADRESSE MANQUANTE</b></font>";
		$tab_adr_ligne1[0]="ADRESSE MANQUANTE";
		$tab_adr_ligne2[0]="";
		$tab_adr_ligne3[0]="";
		$tab_adr_ligne4[0]="";
		$tab_adr_ligne5[0]="";
	}
	else {
		if (isset($tab_bull['eleve'][$i]['resp'][1])) {
			if((isset($tab_bull['eleve'][$i]['resp'][1]['adr1']))&&
				(isset($tab_bull['eleve'][$i]['resp'][1]['adr2']))&&
				(isset($tab_bull['eleve'][$i]['resp'][1]['adr3']))&&
				(isset($tab_bull['eleve'][$i]['resp'][1]['adr4']))&&
				(isset($tab_bull['eleve'][$i]['resp'][1]['cp']))&&
				(isset($tab_bull['eleve'][$i]['resp'][1]['commune']))
			) {
				// Le deuxième responsable existe et est renseigné
				if (($tab_bull['eleve'][$i]['resp'][0]['adr_id']==$tab_bull['eleve'][$i]['resp'][1]['adr_id']) OR
					(
						($tab_bull['eleve'][$i]['resp'][0]['adr1']==$tab_bull['eleve'][$i]['resp'][1]['adr1'])&&
						($tab_bull['eleve'][$i]['resp'][0]['adr2']==$tab_bull['eleve'][$i]['resp'][1]['adr2'])&&
						($tab_bull['eleve'][$i]['resp'][0]['adr3']==$tab_bull['eleve'][$i]['resp'][1]['adr3'])&&
						($tab_bull['eleve'][$i]['resp'][0]['adr4']==$tab_bull['eleve'][$i]['resp'][1]['adr4'])&&
						($tab_bull['eleve'][$i]['resp'][0]['cp']==$tab_bull['eleve'][$i]['resp'][1]['cp'])&&
						($tab_bull['eleve'][$i]['resp'][0]['commune']==$tab_bull['eleve'][$i]['resp'][1]['commune'])
					)
				) {
					// Les adresses sont identiques
					$nb_bulletins=1;

					if(($tab_bull['eleve'][$i]['resp'][0]['nom']!=$tab_bull['eleve'][$i]['resp'][1]['nom'])&&
						($tab_bull['eleve'][$i]['resp'][1]['nom']!="")) {
						// Les noms des responsables sont différents
						$tab_adr_ligne1[0]=$tab_bull['eleve'][$i]['resp'][0]['civilite']." ".$tab_bull['eleve'][$i]['resp'][0]['nom']." ".$tab_bull['eleve'][$i]['resp'][0]['prenom']." et ".$tab_bull['eleve'][$i]['resp'][1]['civilite']." ".$tab_bull['eleve'][$i]['resp'][1]['nom']." ".$tab_bull['eleve'][$i]['resp'][1]['prenom'];

						/*
						$tab_adr_ligne1[0]=$tab_bull['eleve'][$i]['resp'][0]['civilite']." ".$tab_bull['eleve'][$i]['resp'][0]['nom']." ".$tab_bull['eleve'][$i]['resp'][0]['prenom'];
						//$tab_adr_ligne1[0].=" et ";
						$tab_adr_ligne1[0].="<br />\n";
						$tab_adr_ligne1[0].="et ";
						$tab_adr_ligne1[0].=$tab_bull['eleve'][$i]['resp'][1]['civilite']." ".$tab_bull['eleve'][$i]['resp'][1]['nom']." ".$tab_bull['eleve'][$i]['resp'][1]['prenom'];
						*/
					}
					else{
						if(($tab_bull['eleve'][$i]['resp'][0]['civilite']!="")&&($tab_bull['eleve'][$i]['resp'][1]['civilite']!="")) {
							$tab_adr_ligne1[0]=$tab_bull['eleve'][$i]['resp'][0]['civilite']." et ".$tab_bull['eleve'][$i]['resp'][1]['civilite']." ".$tab_bull['eleve'][$i]['resp'][0]['nom']." ".$tab_bull['eleve'][$i]['resp'][0]['prenom'];
						}
						else {
							$tab_adr_ligne1[0]="M. et Mme ".$tab_bull['eleve'][$i]['resp'][0]['nom']." ".$tab_bull['eleve'][$i]['resp'][0]['prenom'];
						}
					}

					$tab_adr_ligne2[0]=$tab_bull['eleve'][$i]['resp'][0]['adr1'];
					if($tab_bull['eleve'][$i]['resp'][0]['adr2']!=""){
						$tab_adr_ligne3[0]=$tab_bull['eleve'][$i]['resp'][0]['adr2'];
					}
					if($tab_bull['eleve'][$i]['resp'][0]['adr3']!=""){
						$tab_adr_ligne4[0]=$tab_bull['eleve'][$i]['resp'][0]['adr3'];
					}
					//if($tab_bull['eleve'][$i]['resp'][0]['adr4']!=""){
					//	$tab_adr_ligne2[0]=$tab_bull['eleve'][$i]['resp'][0]['adr4'];
					//}
					$tab_adr_ligne5[0]=$tab_bull['eleve'][$i]['resp'][0]['cp']." ".$tab_bull['eleve'][$i]['resp'][0]['commune'];


					if(($tab_bull['eleve'][$i]['resp'][0]['pays']!="")&&(strtolower($tab_bull['eleve'][$i]['resp'][0]['pays'])!=strtolower($gepiSchoolPays))) {
						$tab_adr_ligne6[0]=$tab_bull['eleve'][$i]['resp'][0]['pays'];
					}

				}
				else {
					// Les adresses sont différentes
					//if ($un_seul_bull_par_famille!="oui") {
					// On teste en plus si la deuxième adresse est valide
					if (($un_seul_bull_par_famille!="oui")&&
						($tab_bull['eleve'][$i]['resp'][1]['adr1']!="")&&
						($tab_bull['eleve'][$i]['resp'][1]['commune']!="")
					) {
						$nb_bulletins=2;
					}
					else {
						$nb_bulletins=1;
					}

					for($cpt=0;$cpt<$nb_bulletins;$cpt++) {
						if($tab_bull['eleve'][$i]['resp'][$cpt]['civilite']!="") {
							$tab_adr_ligne1[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['civilite']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['nom']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['prenom'];
						}
						else {
							$tab_adr_ligne1[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['nom']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['prenom'];
						}

						$tab_adr_ligne2[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['adr1'];
						if($tab_bull['eleve'][$i]['resp'][$cpt]['adr2']!=""){
							$tab_adr_ligne3[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['adr2'];
						}
						if($tab_bull['eleve'][$i]['resp'][$cpt]['adr3']!=""){
							$tab_adr_ligne4[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['adr3'];
						}
						/*
						if($tab_bull['eleve'][$i]['resp'][$cpt]['adr4']!=""){
							$tab_adr_ligne2[$cpt].="<br />\n".$tab_bull['eleve'][$i]['resp'][$cpt]['adr4'];
						}
						*/
						$tab_adr_ligne5[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['cp']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['commune'];

						if(($tab_bull['eleve'][$i]['resp'][$cpt]['pays']!="")&&(strtolower($tab_bull['eleve'][$i]['resp'][$cpt]['pays'])!=strtolower($gepiSchoolPays))) {
							$tab_adr_ligne6[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['pays'];
						}
					}

				}
			}
			else {
				// Il n'y a pas de deuxième adresse, mais il y aurait un deuxième responsable???
				// CA NE DEVRAIT PAS ARRIVER ETANT DONNé LA REQUETE EFFECTUEE QUI JOINT resp_pers ET resp_adr...
				if ($un_seul_bull_par_famille!="oui") {
					$nb_bulletins=2;
				}
				else {
					$nb_bulletins=1;
				}

				for($cpt=0;$cpt<$nb_bulletins;$cpt++) {
					if($tab_bull['eleve'][$i]['resp'][$cpt]['civilite']!="") {
						$tab_adr_ligne1[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['civilite']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['nom']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['prenom'];
					}
					else {
						$tab_adr_ligne1[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['nom']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['prenom'];
					}

					$tab_adr_ligne2[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['adr1'];
					if($tab_bull['eleve'][$i]['resp'][$cpt]['adr2']!=""){
						$tab_adr_ligne3[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['adr2'];
					}
					if($tab_bull['eleve'][$i]['resp'][$cpt]['adr3']!=""){
						$tab_adr_ligne4[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['adr3'];
					}
					/*
					if($tab_bull['eleve'][$i]['resp'][$cpt]['adr4']!=""){
						$tab_adr_ligne2[$cpt].="<br />\n".$tab_bull['eleve'][$i]['resp'][$cpt]['adr4'];
					}
					*/
					$tab_adr_ligne5[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['cp']." ".$tab_bull['eleve'][$i]['resp'][$cpt]['commune'];

					if(($tab_bull['eleve'][$i]['resp'][$cpt]['pays']!="")&&(strtolower($tab_bull['eleve'][$i]['resp'][$cpt]['pays'])!=strtolower($gepiSchoolPays))) {
						$tab_adr_ligne6[$cpt]=$tab_bull['eleve'][$i]['resp'][$cpt]['pays'];
					}
				}
			}
		}
		else {
			// Il n'y a pas de deuxième responsable
			$nb_bulletins=1;

			if($tab_bull['eleve'][$i]['resp'][0]['civilite']!="") {
				$tab_adr_ligne1[0]=$tab_bull['eleve'][$i]['resp'][0]['civilite']." ".$tab_bull['eleve'][$i]['resp'][0]['nom']." ".$tab_bull['eleve'][$i]['resp'][0]['prenom'];
			}
			else {
				$tab_adr_ligne1[0]=$tab_bull['eleve'][$i]['resp'][0]['nom']." ".$tab_bull['eleve'][$i]['resp'][0]['prenom'];
			}

			$tab_adr_ligne2[0]=$tab_bull['eleve'][$i]['resp'][0]['adr1'];
			if($tab_bull['eleve'][$i]['resp'][0]['adr2']!=""){
				$tab_adr_ligne3[0]=$tab_bull['eleve'][$i]['resp'][0]['adr2'];
			}
			if($tab_bull['eleve'][$i]['resp'][0]['adr3']!=""){
				$tab_adr_ligne4[0]=$tab_bull['eleve'][$i]['resp'][0]['adr3'];
			}
			/*
			if($tab_bull['eleve'][$i]['resp'][0]['adr4']!=""){
				$tab_adr_ligne2[0].="<br />\n".$tab_bull['eleve'][$i]['resp'][0]['adr4'];
			}
			*/
			$tab_adr_ligne5[0]=$tab_bull['eleve'][$i]['resp'][0]['cp']." ".$tab_bull['eleve'][$i]['resp'][0]['commune'];

			if(($tab_bull['eleve'][$i]['resp'][0]['pays']!="")&&(strtolower($tab_bull['eleve'][$i]['resp'][0]['pays'])!=strtolower($gepiSchoolPays))) {
				$tab_adr_ligne6[0]=$tab_bull['eleve'][$i]['resp'][0]['pays'];
			}
		}
	}
	//=========================================

	//+++++++++++++++++++++++++++++++++++++++++++
	// A FAIRE
	// Mettre ici une boucle pour $nb_bulletins
	// Et tenir compte par la suite de la demande d'intercaler le relevé de notes ou non
	//+++++++++++++++++++++++++++++++++++++++++++

	for($num_resp_bull=0;$num_resp_bull<$nb_bulletins;$num_resp_bull++) {
		$pdf->AddPage(); //ajout d'une page au document
		$pdf->SetFont('Arial');

		//=========================================

		// Récupération de l'identifiant de la classe:
		$classe_id=$tab_bull['eleve'][$i]['id_classe'];

		//=========================================

		if($tab_modele_pdf["affiche_filigrame"][$classe_id]==='1'){
			$pdf->SetFont('Arial','B',50);
			$pdf->SetTextColor(255,192,203);
			//$pdf->TextWithRotation(40,190,$texte_filigrame[$classe_id],45);
			$pdf->TextWithRotation(40,190,$tab_modele_pdf["texte_filigrame"][$classe_id],45);
			$pdf->SetTextColor(0,0,0);
		}

		//=========================================

		// Bloc identification etablissement
		$logo = '../images/'.getSettingValue('logo_etab');
		$format_du_logo = str_replace('.','',strstr(getSettingValue('logo_etab'), '.'));

		// Logo
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
			$pdf->Image($logo, $X_logo, $Y_logo, $L_logo, $H_logo);
		}

		//=========================================

		// Adresse établissement
		if ( !isset($X_etab) or empty($X_etab) ) {
			$X_etab = '5';
			$Y_etab = '5';
		}
		$pdf->SetXY($X_etab,$Y_etab);
		$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',14);

		// mettre en gras le nom de l'établissement si $nom_etab_gras = 1
		if ( $tab_modele_pdf["nom_etab_gras"][$classe_id] === '1' ) {
			$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'B',14);
		}
		$pdf->Cell(90,7, $gepiSchoolName,0,2,'');

		$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);

		if ( $gepiSchoolAdress1 != '' ) {
			$pdf->Cell(90,5, $gepiSchoolAdress1,0,2,'');
		}
		if ( $gepiSchoolAdress2 != '' ) {
			$pdf->Cell(90,5, $gepiSchoolAdress2,0,2,'');
		}

		$pdf->Cell(90,5, $gepiSchoolZipCode." ".$gepiSchoolCity,0,2,'');

		$passealaligne = '0';
		// entête téléphone
		// emplacement du cadre télécom
		$x_telecom = $pdf->GetX();
		$y_telecom = $pdf->GetY();

		if( $tab_modele_pdf["entente_tel"][$classe_id]==='1' ) {
			$grandeur = ''; $text_tel = '';
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

			if ( $tab_modele_pdf["tel_texte"][$classe_id] != '' and $tab_modele_pdf["tel_image"][$classe_id] === '' ) {
				$text_tel = $tab_modele_pdf["tel_texte"][$classe_id].''.$gepiSchoolTel;
				$grandeur = $pdf->GetStringWidth($text_tel);
			}

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
			if ( $tab_modele_pdf["fax_texte"][$classe_id] != '' and $tab_modele_pdf["fax_image"][$classe_id] === '' ) {
				$text_fax = $tab_modele_pdf["fax_texte"][$classe_id].''.$gepiSchoolFax;
			}
			$pdf->Cell(90,5, $text_fax,0,$passealaligne,'');
		}

		if($tab_modele_pdf["entente_mel"][$classe_id]==='1') {
			$text_mel = '';
			$y_telecom = $y_telecom + 5;
			$pdf->SetXY($x_telecom,$y_telecom);

			$text_mel = $gepiSchoolEmail;
			if ( $tab_modele_pdf["courrier_image"][$classe_id] != '' ) {
				$a = $pdf->GetX();
				$b = $pdf->GetY();
				$ima = '../images/imabulle/'.$tab_modele_pdf["courrier_image"][$classe_id].'.jpg';
				$valeurima=redimensionne_image($ima, 15, 15);
				$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
				$text_mel = '      '.$gepiSchoolEmail;
			}
			if ( $tab_modele_pdf["courrier_texte"][$classe_id] != '' and $tab_modele_pdf["courrier_image"][$classe_id] === '' ) {
				$text_mel = $tab_modele_pdf["courrier_texte"][$classe_id].' '.$gepiSchoolEmail;
			}
			$pdf->Cell(90,5, $text_mel,0,2,'');
		}

		// ============= FIN ENTETE BULLETIN ==========================

		//=========================================

		// A VOIR: REMPLACER LE $i PAR AUTRE CHOSE POUR EVITER LA COLLISION AVEC L'INDICE $i passé à la fonction
		//$i = $nb_eleve_aff;

		//$id_periode = $periode_classe[$id_classe_selection][$cpt_info_periode];
		$id_periode = $tab_bull['num_periode'];

		// AJOUT ERIC
		//$classe_id=$id_classe_selection;

		$pdf->SetFont('Arial','B',12);

		// gestion des styles
		$pdf->SetStyle("b","arial","B",8,"0,0,0");
		$pdf->SetStyle("i","arial","I",8,"0,0,0");
		$pdf->SetStyle("u","arial","U",8,"0,0,0");

		// style pour la case appréciation générale
		// identité du professeur principal
		if ( $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id] != '' and $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id] < '15' ) {
			$taille = $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id];
		} else {
			$taille = '10';
		}
		$pdf->SetStyle("bppc","arial","B",$taille,"0,0,0");
		$pdf->SetStyle("ippc","arial","I",$taille,"0,0,0");

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

			$texte_1_responsable = trim($tab_adr_ligne1[$num_resp_bull]);
			$hauteur_caractere=12;
			$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'B',$hauteur_caractere);
			$val = $pdf->GetStringWidth($texte_1_responsable);
			$taille_texte = $longeur_cadre_adresse;
			$grandeur_texte='test';
			while($grandeur_texte != 'ok') {
				if($taille_texte < $val){
					$hauteur_caractere = $hauteur_caractere-0.3;
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'B',$hauteur_caractere);
					$val = $pdf->GetStringWidth($texte_1_responsable);
				} else {
					$grandeur_texte = 'ok';
				}
			}
			$pdf->Cell(90,7, $texte_1_responsable,0,2,'');

			$texte_1_responsable = $tab_adr_ligne2[$num_resp_bull];
			$hauteur_caractere=10;
			$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
			$val = $pdf->GetStringWidth($texte_1_responsable);
			$taille_texte = $longeur_cadre_adresse;
			$grandeur_texte='test';
			while($grandeur_texte!='ok') {
				if($taille_texte<$val){
					$hauteur_caractere = $hauteur_caractere-0.3;
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
					$val = $pdf->GetStringWidth($texte_1_responsable);
				} else {
					$grandeur_texte='ok';
				}
			}
			$pdf->Cell(90,5, $texte_1_responsable,0,2,'');

			$texte_1_responsable = $tab_adr_ligne3[$num_resp_bull];
			$hauteur_caractere=10;
			$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
			$val = $pdf->GetStringWidth($texte_1_responsable);
			$taille_texte = $longeur_cadre_adresse;
			$grandeur_texte='test';
			while($grandeur_texte!='ok') {
				if($taille_texte<$val){
					$hauteur_caractere = $hauteur_caractere-0.3;
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
					$val = $pdf->GetStringWidth($texte_1_responsable);
				} else {
					$grandeur_texte='ok';
				}
			}
			$pdf->Cell(90,5, $texte_1_responsable,0,2,'');

			// Suppression du saut de ligne pour mettre la ligne 3 de l'adresse
			//$pdf->Cell(90,5, '',0,2,'');

			$texte_1_responsable = $tab_adr_ligne4[$num_resp_bull];
			$hauteur_caractere=10;
			$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
			$val = $pdf->GetStringWidth($texte_1_responsable);
			$taille_texte = $longeur_cadre_adresse;
			$grandeur_texte='test';
			while($grandeur_texte!='ok') {
				if($taille_texte<$val){
					$hauteur_caractere = $hauteur_caractere-0.3;
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
					$val = $pdf->GetStringWidth($texte_1_responsable);
				} else {
					$grandeur_texte='ok';
				}
			}
			$pdf->Cell(90,5, $texte_1_responsable,0,2,'');

			//$texte_1_responsable = $cp_parents[$ident_eleve_aff][$responsable_place]." ".$ville_parents[$ident_eleve_aff][$responsable_place];
			$texte_1_responsable = $tab_adr_ligne5[$num_resp_bull];
			$hauteur_caractere=10;
			$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
			$val = $pdf->GetStringWidth($texte_1_responsable);
			$taille_texte = $longeur_cadre_adresse;
			$grandeur_texte='test';
			while($grandeur_texte!='ok') {
				if($taille_texte<$val){
					$hauteur_caractere = $hauteur_caractere-0.3;
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
					$val = $pdf->GetStringWidth($texte_1_responsable);
				} else {
					$grandeur_texte='ok';
				}
			}
			$pdf->Cell(90,5, $texte_1_responsable,0,2,'');


			//============================
			//if((strtolower($gepiSchoolPays)!=strtolower($pays_parents[$ident_eleve_aff][$responsable_place]))&&($pays_parents[$ident_eleve_aff][$responsable_place]!="")) {
			if(isset($tab_adr_ligne6[$num_resp_bull])) {
				$texte_1_responsable = $tab_adr_ligne6[$num_resp_bull];
				$hauteur_caractere=10;
				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
				$val = $pdf->GetStringWidth($texte_1_responsable);
				$taille_texte = $longeur_cadre_adresse;
				$grandeur_texte='test';
				while($grandeur_texte!='ok') {
					if($taille_texte<$val){
						$hauteur_caractere = $hauteur_caractere-0.3;
						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
						$val = $pdf->GetStringWidth($texte_1_responsable);
					} else {
						$grandeur_texte='ok';
					}
				}
				$pdf->Cell(90,5, $texte_1_responsable,0,2,'');
			}
			//============================

			$texte_1_responsable = '';
			if ( $tab_modele_pdf["cadre_adresse"][$classe_id] != 0 ) {
				$pdf->Rect($tab_modele_pdf["X_parent"][$classe_id], $tab_modele_pdf["Y_parent"][$classe_id], $longeur_cadre_adresse, $hauteur_cadre_adresse, 'D');
			}
		}

		//=========================================

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

			$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'B',14);

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


			$photo[$i]="../photos/eleves/".$tab_bull['eleve'][$i]['elenoet'].".jpg";
			if(!file_exists($photo[$i])) {
				$photo[$i]="../photos/eleves/0".$tab_bull['eleve'][$i]['elenoet'].".jpg";
			}

			if($tab_modele_pdf["active_photo"][$classe_id]==='1' and $photo[$i]!='' and file_exists($photo[$i])) {
				$L_photo_max = ($hauteur_cadre_eleve - ( $ajouter * 2 )) * 2.8;
				$H_photo_max = ($hauteur_cadre_eleve - ( $ajouter * 2 )) * 2.8;
				$valeur=redimensionne_image($photo[$i], $L_photo_max, $H_photo_max);
				$X_photo = $tab_modele_pdf["X_eleve"][$classe_id]+ 0.20 + $ajouter;
				$Y_photo = $tab_modele_pdf["Y_eleve"][$classe_id]+ 0.25 + $ajouter;
				$L_photo = $valeur[0]; $H_photo = $valeur[1];
				$X_eleve_2 = $tab_modele_pdf["X_eleve"][$classe_id] + $L_photo + $ajouter + 1;
				$Y_eleve_2 = $Y_photo;
				$pdf->Image($photo[$i], $X_photo, $Y_photo, $L_photo, $H_photo);
				$longeur_cadre_eleve = $longeur_cadre_eleve - ( $valeur[0] + $ajouter );
			}


			$pdf->SetXY($X_eleve_2,$Y_eleve_2);
			$pdf->Cell(90,7, $tab_bull['eleve'][$i]['nom']." ".$tab_bull['eleve'][$i]['prenom'],0,2,'');
			$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
			if($tab_modele_pdf["affiche_date_naissance"][$classe_id]==='1') {
				if($tab_bull['eleve'][$i]['naissance']!="") {
					$info_naissance="Né";
					if($tab_bull['eleve'][$i]['sexe']=="F") {$info_naissance.="e";}
					$info_naissance.=" le ".$tab_bull['eleve'][$i]['naissance'];
					$pdf->Cell(90,5, $info_naissance,0,2,'');
				}
			}

			$rdbt = '';
			if($tab_modele_pdf["affiche_dp"][$classe_id]==='1') {
				if($tab_modele_pdf["affiche_doublement"][$classe_id]==='1') {
					//if($tab_bull['eleve'][$i]['doublant']!="") {
					if($tab_bull['eleve'][$i]['doublant']=="R") {
						//$rdbt=" ; ".$doublement[$i];
						$rdbt=" ; redoublant";
						if($tab_bull['eleve'][$i]['sexe']=="F") {
							$rdbt.="e";
						}
					}
					if(isset($tab_bull['eleve'][$i]['regime'])) {
						$pdf->Cell(90,4, regime($tab_bull['eleve'][$i]['regime']).$rdbt,0,2,'');
					} else {
						$pdf->Cell(90,4,$rdbt,0,2,'');
					}
				}
			} else {
				if($tab_modele_pdf["affiche_doublement"][$classe_id]==='1') {
					//if($tab_bull['eleve'][$i]['doublant']!="") {
					if($tab_bull['eleve'][$i]['doublant']=="R") {
						//$pdf->Cell(90,4.5, $doublement[$i],0,2,'');
						$rdbt=" ; redoublant";
						if($tab_bull['eleve'][$i]['sexe']=="F") {
							$rdbt.="e";
						}
						$pdf->Cell(90,4.5, $rdbt,0,2,'');
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
					$pdf->Cell(45,4.5, unhtmlentities($tab_bull['eleve'][$i]['classe']),0, $passe_a_la_ligne,'');
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
					$pdf->Cell(45,4.5, 'Effectif : '.$tab_bull['eff_classe'].' élèves',0,$pass_ligne,'');
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
			//if($tab_modele_pdf["affiche_etab_origine"][$classe_id]==='1' and !empty($etablissement_origine[$i]) ) {
			if($tab_modele_pdf["affiche_etab_origine"][$classe_id]==='1' and isset($tab_bull['eleve'][$i]['etab_id']) and !empty($tab_bull['eleve'][$i]['etab_id']) ) {
				$pdf->SetX($X_eleve_2);
				$hauteur_caractere_etaborigine = '10';
				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere_etaborigine);
				$val = $pdf->GetStringWidth('Etab. Origine : '.$tab_bull['eleve'][$i]['etab_niveau']." ".$tab_bull['eleve'][$i]['etab_nom']." (".$tab_bull['eleve'][$i]['etab_ville'].")");
				$taille_texte = $longeur_cadre_eleve-3;
				$grandeur_texte='test';
				while($grandeur_texte!='ok') {
					if($taille_texte<$val) {
						$hauteur_caractere_etaborigine = $hauteur_caractere_etaborigine-0.3;
						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere_etaborigine);
						$val = $pdf->GetStringWidth('Etab. Origine : '.$tab_bull['eleve'][$i]['etab_niveau']." ".$tab_bull['eleve'][$i]['etab_nom']." (".$tab_bull['eleve'][$i]['etab_ville'].")");
					} else {
						$grandeur_texte='ok';
					}
				}
				$grandeur_texte='test';
				$pdf->Cell(90,4, 'Etab. Origine : '.$tab_bull['eleve'][$i]['etab_niveau']." ".$tab_bull['eleve'][$i]['etab_nom']." (".$tab_bull['eleve'][$i]['etab_ville'].")",0,2);
				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
			}
		} // fin du bloc affichage information sur l'élèves

		//=========================================

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
			$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id], $type_texte, $taille_texte);
			$pdf->Cell(90,7, "Classe de ".unhtmlentities($tab_bull['classe_nom_complet']),0,2,'C');
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
			$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id], $type_texte, $taille_texte);
			$annee_scolaire = $gepiYear;
			$pdf->Cell(90,5, "Année scolaire ".$annee_scolaire,0,2,'C');
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
			$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id], $type_texte, $taille_texte);
			$pdf->Cell(90,5, "Bulletin du ".unhtmlentities($tab_bull['nom_periode']),0,2,'C');
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
				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id], $type_texte, $taille_texte);
				$pdf->Cell(95,7, $date_bulletin,0,2,'R');
			}

			$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
		}

		//=========================================

		// Bloc notes et appréciations
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
			}
		}
		//fclose($fich);

		if($tab_modele_pdf["active_bloc_note_appreciation"][$classe_id]==='1' and $nb_matiere!='0') {
			$pdf->Rect($tab_modele_pdf["X_note_app"][$classe_id], $tab_modele_pdf["Y_note_app"][$classe_id], $tab_modele_pdf["longeur_note_app"][$classe_id], $tab_modele_pdf["hauteur_note_app"][$classe_id], 'D');
			//entête du tableau des notes et app
			$nb_entete_moyenne = $tab_modele_pdf["active_moyenne_eleve"][$classe_id]+$tab_modele_pdf["active_moyenne_classe"][$classe_id]+$tab_modele_pdf["active_moyenne_min"][$classe_id]+$tab_modele_pdf["active_moyenne_max"][$classe_id]; //min max classe eleve
			$hauteur_entete = 8;
			$hauteur_entete_pardeux = $hauteur_entete/2;
			$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id], $tab_modele_pdf["Y_note_app"][$classe_id]);
			$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
			$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $hauteur_entete, $tab_modele_pdf["titre_entete_matiere"][$classe_id],1,0,'C');
			$largeur_utilise = $tab_modele_pdf["largeur_matiere"][$classe_id];

			// coefficient matière
			if($tab_modele_pdf["active_coef_moyenne"][$classe_id]==='1') {
				$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]);
				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
				$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $hauteur_entete, $tab_modele_pdf["titre_entete_coef"][$classe_id],'LRB',0,'C');
				$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_coef_moyenne"][$classe_id];
			}

			// nombre de notes
			if($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1') {
				$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]);
				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
				$pdf->Cell($tab_modele_pdf["largeur_nombre_note"][$classe_id], $hauteur_entete, $tab_modele_pdf["titre_entete_nbnote"][$classe_id],'LRB',0,'C');
				$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_nombre_note"][$classe_id];
			}

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


			$cpt_ordre = 0;
			$chapeau_moyenne = 'non';
			while ( !empty($ordre_moyenne[$cpt_ordre]) ) {
				$categorie_passe_count = 0;
				// le chapeau des moyennes
				$ajout_espace_au_dessus = 4;
				if ( $tab_modele_pdf["entete_model_bulletin"][$classe_id] === '1' and $nb_entete_moyenne > 1 and ( $ordre_moyenne[$cpt_ordre] === 'classe' or $ordre_moyenne[$cpt_ordre] === 'min' or $ordre_moyenne[$cpt_ordre] === 'max' or $ordre_moyenne[$cpt_ordre] === 'eleve' ) and $chapeau_moyenne === 'non' and $tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] != '3' )
				{
					$largeur_moyenne = $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id] * $nb_entete_moyenne;
					$text_entete_moyenne = 'Moyenne';
					$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]);
					$pdf->Cell($largeur_moyenne, $hauteur_entete_pardeux, $text_entete_moyenne,1,0,'C');
					$chapeau_moyenne = 'oui';
				}

				if ( ($tab_modele_pdf["entete_model_bulletin"][$classe_id] === '2' and $nb_entete_moyenne > 1 and ( $ordre_moyenne[$cpt_ordre] === 'classe' or $ordre_moyenne[$cpt_ordre] === 'min' or $ordre_moyenne[$cpt_ordre] === 'max' ) and $chapeau_moyenne === 'non' ) or ( $tab_modele_pdf["entete_model_bulletin"][$classe_id] === '1' and $tab_modele_pdf["ordre_entete_model_bulletin"][$classe_id] === '3' and $chapeau_moyenne === 'non' and ( $ordre_moyenne[$cpt_ordre] === 'classe' or $ordre_moyenne[$cpt_ordre] === 'min' or $ordre_moyenne[$cpt_ordre] === 'max' )  ) )
				{
					$largeur_moyenne = $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id] * ( $nb_entete_moyenne - 1 );
					$text_entete_moyenne = 'Pour la classe';
					$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]);
					$hauteur_caractere=10;
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
					$val = $pdf->GetStringWidth($text_entete_moyenne);
					$taille_texte = $largeur_moyenne;
					$grandeur_texte='test';
					while($grandeur_texte!='ok') {
						if($taille_texte<$val)
						{
							$hauteur_caractere = $hauteur_caractere-0.3;
							$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
							$val = $pdf->GetStringWidth($text_entete_moyenne);
						}
						else {
							$grandeur_texte='ok';
						}
					}
					$pdf->Cell($largeur_moyenne, $hauteur_entete_pardeux, $text_entete_moyenne,1,0,'C');
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
					$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $hauteur_de_la_cellule, "Elève",1,0,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
					$pdf->SetFillColor(0, 0, 0);
					$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
				}

				//classe
				if($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' ) {
					$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]+4);
					$hauteur_caractere = '8.5';

					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
					$text_moy_classe = 'Classe';
					if ( $tab_modele_pdf["entete_model_bulletin"][$classe_id] === '2' ) {
						$text_moy_classe = 'Moy.';
					}
					$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $hauteur_entete_pardeux, $text_moy_classe,1,0,'C');
					$X_moyenne_classe = $tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise;
					$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
				}
				//min
				if($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' ) {
					$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]+4);
					$hauteur_caractere = '8.5';
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
					$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $hauteur_entete_pardeux, "Min.",1,0,'C');
					$X_min_classe = $tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise;
					$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
				}
				//max
				if($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' ) {
					$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]+4);
					$hauteur_caractere = '8.5';
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
					$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $hauteur_entete_pardeux, "Max.",1,0,'C');
					$X_max_classe = $tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise;
					$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
				}

				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);

				// rang de l'élève
				if( $tab_modele_pdf["active_rang"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'rang' ) {
					$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]);
					$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $hauteur_entete, $tab_modele_pdf["titre_entete_rang"][$classe_id],'LRB',0,'C');
					//$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $hauteur_entete, $tab_modele_pdf["titre_entete_rang"][$classe_id],'LRB',0,'C');
					$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_rang"][$classe_id];
				}

				// graphique de niveau
				if( $tab_modele_pdf["active_graphique_niveau"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'niveau' ) {
					$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]);
					$hauteur_caractere = '10';
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
					$pdf->Cell($tab_modele_pdf["largeur_niveau"][$classe_id], $hauteur_entete_pardeux, "Niveau",'LR',0,'C');
					$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]+4);
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
					$pdf->Cell($tab_modele_pdf["largeur_niveau"][$classe_id], $hauteur_entete_pardeux, "ABC+C-DE",'LRB',0,'C');
					$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_niveau"][$classe_id];
				}

				//appreciation
				$hauteur_caractere = '10';
				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere);
				if($tab_modele_pdf["active_appreciation"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'appreciation' ) {
					$pdf->SetXY($tab_modele_pdf["X_note_app"][$classe_id]+$largeur_utilise, $tab_modele_pdf["Y_note_app"][$classe_id]);
					if ( !empty($ordre_moyenne[$cpt_ordre+1]) ) {
						$cpt_ordre_sous = $cpt_ordre + 1;
						$largeur_appret = 0;
						while ( !empty($ordre_moyenne[$cpt_ordre_sous]) ) {
							if ( $ordre_moyenne[$cpt_ordre_sous] === 'eleve' ) { $largeur_appret = $largeur_appret + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id]; }
							if ( $ordre_moyenne[$cpt_ordre_sous] === 'rang' ) { $largeur_appret = $largeur_appret + $tab_modele_pdf["largeur_rang"][$classe_id]; }
							if ( $ordre_moyenne[$cpt_ordre_sous] === 'niveau' ) { $largeur_appret = $largeur_appret + $tab_modele_pdf["largeur_niveau"][$classe_id]; }
							if ( $ordre_moyenne[$cpt_ordre_sous] === 'min' ) { $largeur_appret = $largeur_appret + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id]; }
							if ( $ordre_moyenne[$cpt_ordre_sous] === 'classe' ) { $largeur_appret = $largeur_appret + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id]; }
							if ( $ordre_moyenne[$cpt_ordre_sous] === 'max' ) { $largeur_appret = $largeur_appret + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id]; }
							$cpt_ordre_sous = $cpt_ordre_sous + 1;
						}
						$largeur_appreciation = $tab_modele_pdf["longeur_note_app"][$classe_id] - $largeur_utilise - $largeur_appret;
					} else {
						$largeur_appreciation = $tab_modele_pdf["longeur_note_app"][$classe_id]-$largeur_utilise;
					}
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);

					$titre_entete_appreciation=$bull_intitule_app;

					$pdf->Cell($largeur_appreciation, $hauteur_entete, $titre_entete_appreciation,'LRB',0,'C');
					$largeur_utilise = $largeur_utilise + $largeur_appreciation;
				}
				$cpt_ordre = $cpt_ordre + 1;
			}
			$largeur_utilise = 0;
			// fin de boucle d'ordre

			//===============================================

			//emplacement des blocs matière et note et appréciation

			//si catégorie activé il faut compter le nombre de catégories
			$nb_categories_select=0;
			$categorie_passe_for='';

			if($tab_modele_pdf["active_regroupement_cote"][$classe_id]==='1' or $tab_modele_pdf["active_entete_regroupement"][$classe_id]==='1') {
				/* A CONVERTIR AVEC $tab_bull
				for($x=0;$x<$nb_matiere;$x++) {
					if($matiere[$ident_eleve_aff][$id_periode][$x]['categorie']!=$categorie_passe_for) {
						$nb_categories_select=$nb_categories_select+1;
					}
					$categorie_passe_for=$matiere[$ident_eleve_aff][$id_periode][$x]['categorie'];
				}
				*/
				$nb_categories_select=count($tab_bull['cat_id']);
			}


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
				// si les moyennes et la moyenne général sont activé alors on les ajoute à ceux qui vaudras soustraire au cadre global matiere
				$hauteur_toute_entete = $hauteur_entete + $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id];
			}
			else {
				$hauteur_toute_entete = $hauteur_entete;
			}

			$hauteur_bloc_matiere=$tab_modele_pdf["hauteur_note_app"][$classe_id]-$hauteur_toute_entete;
			$X_note_moy_app = $tab_modele_pdf["X_note_app"][$classe_id];
			$Y_note_moy_app = $tab_modele_pdf["Y_note_app"][$classe_id]+$tab_modele_pdf["hauteur_note_app"][$classe_id]-$hauteur_entete;

			if($tab_modele_pdf["active_entete_regroupement"][$classe_id]==='1') {
				$espace_entre_matier = ($hauteur_bloc_matiere-($nb_categories_select*5))/$nb_matiere;
			}
			else {
				$espace_entre_matier = $hauteur_bloc_matiere/$nb_matiere;
			}
			$pdf->SetXY($X_bloc_matiere, $Y_bloc_matiere);
			$Y_decal = $Y_bloc_matiere;

			//for($m=0; $m<$nb_matiere; $m++)
			for($m=0; $m<count($tab_bull['groupe']); $m++)
			{
				$pdf->SetXY($X_bloc_matiere, $Y_decal);

				// si on affiche les catégories
				if($tab_modele_pdf["active_entete_regroupement"][$classe_id]==='1') {
					//si on affiche les moyenne des catégorie
					//if($matiere[$ident_eleve_aff][$id_periode][$m]['categorie']!=$categorie_passe)
					//if($tab_bull['cat_id'][$m]!=$categorie_passe)
					if($tab_bull['nom_cat_complet'][$m]!=$categorie_passe)
					{
						$hauteur_caractere_catego = '10';
						if ( $tab_modele_pdf["taille_texte_categorie"][$classe_id] != '' and $tab_modele_pdf["taille_texte_categorie"][$classe_id] != '0' ) {
							$hauteur_caractere_catego = $tab_modele_pdf["taille_texte_categorie"][$classe_id];
						}
						else {
							$hauteur_caractere_catego = '10';
						}
						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere_catego);
						$tt_catego = unhtmlentities($tab_bull['nom_cat_complet'][$m]);
						$val = $pdf->GetStringWidth($tt_catego);
						$taille_texte = ($tab_modele_pdf["largeur_matiere"][$classe_id]);
						$grandeur_texte='test';
						while($grandeur_texte!='ok') {
							if($taille_texte<$val)
							{
								$hauteur_caractere_catego = $hauteur_caractere_catego-0.3;
								$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere_catego);
								$val = $pdf->GetStringWidth($tt_catego);
							}
							else {
								$grandeur_texte='ok';
							}
						}
						$grandeur_texte='test';
						$pdf->SetFillColor($tab_modele_pdf["couleur_categorie_entete1"][$classe_id], $tab_modele_pdf["couleur_categorie_entete2"][$classe_id], $tab_modele_pdf["couleur_categorie_entete3"][$classe_id]);
						$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], $tt_catego,'TLB',0,'L',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
						$largeur_utilise = $tab_modele_pdf["largeur_matiere"][$classe_id];

						// coefficient matière
						if($tab_modele_pdf["active_coef_moyenne"][$classe_id]==='1') {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
							$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
							$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','T',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
							$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_coef_moyenne"][$classe_id];
						}

						// nombre de note
						if($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1') {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
							$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
							$pdf->Cell($tab_modele_pdf["largeur_nombre_note"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','T',0,'C',$tab_modele_pdf["couleur_categorie_entete"][$classe_id]);
							$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_nombre_note"][$classe_id];
						}
						$pdf->SetFillColor(0, 0, 0);

						// les moyennes eleve, classe, min, max par catégorie
						$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);

						$cpt_ordre = 0;
						$chapeau_moyenne = 'non';
						while ( !empty($ordre_moyenne[$cpt_ordre]) ) {
							if($tab_modele_pdf["active_moyenne_eleve"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'eleve' ) {
								$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
								if($tab_modele_pdf["active_moyenne_regroupement"][$classe_id]==='1') {
									//$categorie_passage=$matiere[$ident_eleve_aff][$id_periode][$m]['categorie'];
									$categorie_passage=$tab_bull['nom_cat_complet'][$m];
									//if($matiere[$ident_eleve_aff][$id_periode][$m]['affiche_moyenne']==='1')
									if(isset($tab_bull['moy_cat_eleve'][$i][$tab_bull['cat_id'][$m]]))
									{
										// On va afficher la moyenne de l'élève pour la catégorie
										if ($tab_bull['moy_cat_eleve'][$i][$tab_bull['cat_id'][$m]]=="") {
											$valeur = "-";
										} else {
											//$calcule_moyenne_eleve_categorie[$categorie_passage]=$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['moy_eleve']/$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego'];
											$valeur = present_nombre($tab_bull['moy_cat_eleve'][$i][$tab_bull['cat_id'][$m]], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
										}
										$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'B',8);
										$pdf->SetFillColor($tab_modele_pdf["couleur_reperage_eleve1"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve2"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve3"][$classe_id]);
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id],$valeur,1,0,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
										$pdf->SetFillColor(0, 0, 0);
										$valeur = "";
									} else {
										$pdf->SetFillColor(255, 255, 255);
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TL',0,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
									}
								} else {
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','T',0,'C');
								}

								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
							}

							//classe
							if($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' ) {
								$pdf->SetXY($X_moyenne_classe, $Y_decal);
								if($tab_modele_pdf["active_moyenne_regroupement"][$classe_id]==='1') {
									//$categorie_passage=$matiere[$ident_eleve_aff][$id_periode][$m]['categorie'];
									$categorie_passage=$tab_bull['nom_cat_complet'][$m];
									//if($matiere[$ident_eleve_aff][$id_periode][$m]['affiche_moyenne']==='1')
									if(isset($tab_bull['moy_cat_classe'][$i][$tab_bull['cat_id'][$m]]))
									{
										// On va afficher la moyenne de la classe pour la catégorie
										/*
										if($matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego']!=0){
											$calcule_moyenne_classe_categorie[$categorie_passage]=$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['moy_classe']/$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego'];
										}
										else{
											$calcule_moyenne_classe_categorie[$categorie_passage]="";
										}
										$calcule_moyenne_classe_categorie[$categorie_passage]=$calcule_moyenne_classe_categorie[$categorie_passage];
										*/
										//================================================
										$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], present_nombre($tab_bull['moy_cat_classe'][$i][$tab_bull['cat_id'][$m]], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]),'TLR',0,'C');
									} else {
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','T',0,'C');
									}
								} else {
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','T',0,'C');
								}
								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
							}

							$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
							//min
							if($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' ) {
								$pdf->SetXY($X_min_classe, $Y_decal);
								if($tab_modele_pdf["active_moyenne_regroupement"][$classe_id]==='1') {
									$categorie_passage=$tab_bull['nom_cat_complet'][$m];
									/*
									//if($matiere[$ident_eleve_aff][$id_periode][$m]['affiche_moyenne']==='1')
									if(isset($tab_bull['moy_cat_classe'][$i][$tab_bull['cat_id'][$j]]))
									{
										// On va afficher la moyenne min de la classe pour la catégorie
										// JE NE L'AI PAS EXTRAITE...

										if($matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego']!=0){
											$calcule_moyenne_classe_categorie[$categorie_passage]=$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['moy_min']/$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego'];
										}
										else{
											$calcule_moyenne_classe_categorie[$categorie_passage]="";
										}
										//================================================

										$calcule_moyenne_classe_categorie[$categorie_passage]=$calcule_moyenne_classe_categorie[$categorie_passage];
										$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], present_nombre($calcule_moyenne_classe_categorie[$categorie_passage], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]),'TLR',0,'C');
									} else {
									*/
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','T',0,'C');
									//}
								} else {
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','T',0,'C');
								}
								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
							}

							//max
							if($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' ) {
								$pdf->SetXY($X_max_classe, $Y_decal);

								if($tab_modele_pdf["active_moyenne_regroupement"][$classe_id]==='1') {
									$categorie_passage=$tab_bull['nom_cat_complet'][$m];
									/*
									if($matiere[$ident_eleve_aff][$id_periode][$m]['affiche_moyenne']==='1')
									{
										// On va afficher la moyenne max de la classe pour la catégorie

										if($matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego']){
											$calcule_moyenne_classe_categorie[$categorie_passage]=$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['moy_max']/$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego'];
										}
										else{
											$calcule_moyenne_classe_categorie[$categorie_passage]="";
										}
										//================================================

										$calcule_moyenne_classe_categorie[$categorie_passage]=$calcule_moyenne_classe_categorie[$categorie_passage];
										//$pdf->SetFont($caractere_utilse[$classe_id],'',8);
										$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
										//$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_info_categorie[$classe_id], present_nombre($calcule_moyenne_classe_categorie[$categorie_passage], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]),'TLR',0,'C');
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], present_nombre($calcule_moyenne_classe_categorie[$categorie_passage], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]),'TLR',0,'C');
									} else {
									*/
										$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','T',0,'C');
									//}
								} else {
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','T',0,'C');
								}
								$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
							}
						$cpt_ordre = $cpt_ordre + 1;
						}
						$largeur_utilise = 0;
						// fin de boucle d'ordre

						// Rang de l'élève
						if($tab_modele_pdf["active_rang"][$classe_id]==='1') {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
							$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
							$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','T',0,'C');
							$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_rang"][$classe_id];
						}
						// Graphique de niveau
						if($tab_modele_pdf["active_graphique_niveau"][$classe_id]==='1') {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
							$pdf->Cell($tab_modele_pdf["largeur_niveau"][$classe_id], $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','T',0,'C');
							$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_niveau"][$classe_id];
						}
						// Appreciation
						if($tab_modele_pdf["active_appreciation"][$classe_id]==='1') {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
							$pdf->Cell($largeur_appreciation, $tab_modele_pdf["hauteur_info_categorie"][$classe_id], '','TB',0,'C');
							$largeur_utilise=0;
						}
						$Y_decal = $Y_decal + 5;

					}
				}


				//============================
				// Modif: boireaus 20070828
				if($tab_modele_pdf["active_regroupement_cote"][$classe_id]==='1' or $tab_modele_pdf["active_entete_regroupement"][$classe_id]==='1') {
					//if($matiere[$ident_eleve_aff][$id_periode][$m]['categorie']===$categorie_passe) {
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
					// fin des moyen par catégorie
				}
				//============================

				// si on affiche les catégories sur le côté

				if(!isset($tab_bull['nom_cat_complet'][$m+1])) {
					//$matiere[$ident_eleve_aff][$id_periode][$m+1]['categorie']='';
					$tab_bull['nom_cat_complet'][$m+1]='';
				}

				if($tab_modele_pdf["active_regroupement_cote"][$classe_id]==='1') {
					if($tab_bull['nom_cat_complet'][$m]!=$tab_bull['nom_cat_complet'][$m] and $categorie_passe!='')
					{
						//hauteur du regroupement hauteur des matier * nombre de matier de la catégorie
						$hauteur_regroupement=$espace_entre_matier*$categorie_passe_count;

						//placement du cadre
						if($nb_eleve_aff===0) { $enplus = 5; }
						if($nb_eleve_aff!=0) { $enplus = 0; }

						$pdf->SetXY($X_bloc_matiere-5,$Y_decal-$hauteur_regroupement+$espace_entre_matier);

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
						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere_vertical);
						$text_s = unhtmlentities($tab_bull['nom_cat_complet'][$m]);
						$longeur_test_s = $pdf->GetStringWidth($text_s);

						// gestion de la taille du texte vertical
						$taille_texte = $hauteur_regroupement;
						$grandeur_texte = 'test';
						while($grandeur_texte != 'ok') {
							if($taille_texte < $longeur_test_s)
							{
								$hauteur_caractere_vertical = $hauteur_caractere_vertical-0.3;
								$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere_vertical);
								$longeur_test_s = $pdf->GetStringWidth($text_s);
							}
							else {
								$grandeur_texte = 'ok';
							}
						}


						//décalage pour centre le texte
						$deca = ($hauteur_regroupement-$longeur_test_s)/2;
						$deca = 0;
						$deca = ($hauteur_regroupement-$longeur_test_s)/2;

						//place le texte dans le cadre
						$placement = $Y_decal+$espace_entre_matier-$deca;
						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere_vertical);
						$pdf->TextWithDirection($X_bloc_matiere-1,$placement,unhtmlentities($text_s),'U');
						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
						$pdf->SetFillColor(0, 0, 0);
					}
				}

				if($tab_modele_pdf["active_regroupement_cote"][$classe_id]==='1' or $tab_modele_pdf["active_entete_regroupement"][$classe_id]==='1') {
					// fin d'affichage catégorie sur le coté
					$categorie_passe=$tab_bull['nom_cat_complet'][$m];
					// fin de gestion de catégorie
				}
				//============================

				// Lignes de Matière, Note, Rang,... Appréciation

				$pdf->SetXY($X_bloc_matiere, $Y_decal);

				// Si c'est une matière suivie par l'élève
				if(isset($tab_bull['note'][$m][$i])) {

					// calcul la taille du titre de la matière
					$hauteur_caractere_matiere=10;
					if ( $tab_modele_pdf["taille_texte_matiere"][$classe_id] != '' and $tab_modele_pdf["taille_texte_matiere"][$classe_id] != '0' and $tab_modele_pdf["taille_texte_matiere"][$classe_id] < '11' )
					{
						$hauteur_caractere_matiere = $tab_modele_pdf["taille_texte_matiere"][$classe_id];
					}
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'B',$hauteur_caractere_matiere);
					$val = $pdf->GetStringWidth($tab_bull['groupe'][$m]['matiere']['nom_complet']);
					$taille_texte = $tab_modele_pdf["largeur_matiere"][$classe_id] - 2;
					$grandeur_texte='test';
					while($grandeur_texte!='ok') {
						if($taille_texte<$val)
						{
							$hauteur_caractere_matiere = $hauteur_caractere_matiere-0.3;
							$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'B',$hauteur_caractere_matiere);
							$val = $pdf->GetStringWidth($tab_bull['groupe'][$m]['matiere']['nom_complet']);
						}
						else {
							$grandeur_texte='ok';
						}
					}
					$grandeur_texte='test';
					$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_entre_matier/2, $tab_bull['groupe'][$m]['matiere']['nom_complet'],'LR',1,'L');
					$Y_decal = $Y_decal+($espace_entre_matier/2);
					$pdf->SetXY($X_bloc_matiere, $Y_decal);
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);

					// nom des professeurs

					if ( isset($tab_bull['groupe'][$m]["profs"]["list"]) )
					{

						$nb_prof_matiere = count($tab_bull['groupe'][$m]["profs"]["list"]);
						$espace_matiere_prof = $espace_entre_matier/2;
						if($nb_prof_matiere>0){
							$espace_matiere_prof = $espace_matiere_prof/$nb_prof_matiere;
						}
						$nb_pass_count = '0';
						$text_prof = '';
						while ($nb_prof_matiere > $nb_pass_count)
						{
							// calcul de la hauteur du caractère du prof
							$tmp_login_prof=$tab_bull['groupe'][$m]["profs"]["list"][$nb_pass_count];
							$text_prof=$tab_bull['groupe'][$m]["profs"]["users"]["$tmp_login_prof"]["civilite"];
							$text_prof.=" ".$tab_bull['groupe'][$m]["profs"]["users"]["$tmp_login_prof"]["nom"];
							$text_prof.=" ".substr($tab_bull['groupe'][$m]["profs"]["users"]["$tmp_login_prof"]["prenom"],0,1);

							if ( $nb_prof_matiere <= 2 ) { $hauteur_caractere_prof = 8; }
							elseif ( $nb_prof_matiere == 3) { $hauteur_caractere_prof = 5; }
							elseif ( $nb_prof_matiere > 3) { $hauteur_caractere_prof = 2; }
							$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere_prof);
							$val = $pdf->GetStringWidth($text_prof);
							$taille_texte = ($tab_modele_pdf["largeur_matiere"][$classe_id]);
							$grandeur_texte='test';
							while($grandeur_texte!='ok') {
								if($taille_texte<$val)
								{
									$hauteur_caractere_prof = $hauteur_caractere_prof-0.3;
									$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere_prof);
									$val = $pdf->GetStringWidth($text_prof);
								}
								else {
									$grandeur_texte='ok';
								}
							}
							$grandeur_texte='test';
							$pdf->SetX($X_bloc_matiere);
							if( empty($tab_bull['groupe'][$m]["profs"]["list"][$nb_pass_count+1]) ) {
								$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_matiere_prof, $text_prof,'LRB',1,'L');
							}
							if( !empty($tab_bull['groupe'][$m]["profs"]["list"][$nb_pass_count+1]) ) {
								$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $espace_matiere_prof, $text_prof,'LR',1,'L');
							}
							$nb_pass_count = $nb_pass_count + 1;
						}
					}
					$largeur_utilise = $tab_modele_pdf["largeur_matiere"][$classe_id];

					// coefficient matière
					if($tab_modele_pdf["active_coef_moyenne"][$classe_id]==='1') {
						$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
						$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $espace_entre_matier, $tab_bull['coef_eleve'][$i][$m],1,0,'C');
						$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_coef_moyenne"][$classe_id];
					}
						//permet le calcul total des coefficients
						// if(empty($moyenne_min[$id_classe][$id_periode])) {
						$total_coef_en_calcul=$total_coef_en_calcul+$tab_bull['coef_eleve'][$i][$m];
						//}

					// nombre de note
					if($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1') {
						$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
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
							$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'B',10);
							$pdf->SetFillColor($tab_modele_pdf["couleur_reperage_eleve1"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve2"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve3"][$classe_id]);

							// calcul nombre de sous affichage

							$nb_sousaffichage='1';
							if(empty($active_coef_sousmoyene)) { $active_coef_sousmoyene = ''; }

							if($active_coef_sousmoyene==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; }
							if($tab_modele_pdf["active_nombre_note"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; }
							if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') { if($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }
							if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') { if($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }
							if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') { if($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }

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
							$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, $valeur,1,2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
							$valeur = "";

							if($active_coef_sousmoyene==='1') {
								$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'I',7);
								$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'coef. '.$tab_bull['coef_eleve'][$i][$m],'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
							}

							if($tab_modele_pdf["toute_moyenne_meme_col"][$classe_id]==='1') {
								// On affiche toutes les moyennes dans la même colonne
								$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'I',7);
								if($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1') {
									if ($tab_bull['moy_classe_grp'][$m]=="-") {
										$valeur = "-";
									}
									else {
										$valeur = present_nombre($tab_bull['moy_classe_grp'][$m], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
									}
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'cla.'.$valeur,'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
								}
								if($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1') {
									if ($tab_bull['moy_min_classe_grp'][$m]=="-") {
										$valeur = "-";
									} else {
										$valeur = present_nombre($tab_bull['moy_min_classe_grp'][$m], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
									}
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'min.'.$valeur,'LR',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
								}
								if($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1') {
									if ($tab_bull['moy_max_classe_grp'][$m]=="-") {
										$valeur = "-";
									} else {
										$valeur = present_nombre($tab_bull['moy_max_classe_grp'][$m], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
									}
									$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier/$nb_sousaffichage, 'max.'.$valeur,'LRD',2,'C',$tab_modele_pdf["active_reperage_eleve"][$classe_id]);
									$valeur = ''; // on remet à vide.
								}
							}

							if($tab_modele_pdf["active_nombre_note"][$classe_id]==='1') {
								$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'I',7);
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
							$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
							$pdf->SetFillColor(0, 0, 0);
							$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];

						} // Fin affichage élève

						//classe
						if( $tab_modele_pdf["active_moyenne_classe"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' ) {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
							if ($tab_bull['moy_classe_grp'][$m]=="-") {
								$valeur = "-";
							} else {
								$valeur = present_nombre($tab_bull['moy_classe_grp'][$m], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
							}
							$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
							/*
							//permet le calcul de la moyenne général de la classe
							if(empty($moyenne_classe[$id_classe][$id_periode])) {
								$total_moyenne_classe_en_calcul=$total_moyenne_classe_en_calcul+($matiere[$ident_eleve_aff][$id_periode][$m]['moy_classe']*$matiere[$ident_eleve_aff][$id_periode][$m]['coef']);
							}
							*/
							$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
						}
						//min
						if( $tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' ) {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
							$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
							if ($tab_bull['moy_min_classe_grp'][$m]=="-") {
								$valeur = "-";
							} else {
								$valeur = present_nombre($tab_bull['moy_min_classe_grp'][$m], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
							}
							$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
							/*
							//permet le calcul de la moyenne mini
							if(empty($moyenne_min[$id_classe][$id_periode])) {
								$total_moyenne_min_en_calcul=$total_moyenne_min_en_calcul+($matiere[$ident_eleve_aff][$id_periode][$m]['moy_min']*$matiere[$ident_eleve_aff][$id_periode][$m]['coef']);
							}
							*/
							$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
						}
						//max
						if( $tab_modele_pdf["active_moyenne_max"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' ) {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
							if ($tab_bull['moy_max_classe_grp'][$m]== "-") {
								$valeur = "-";
							} else {
								$valeur = present_nombre($tab_bull['moy_max_classe_grp'][$m], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]);
							}
							$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
							/*
							//permet le calcul de la moyenne maxi
							if(empty($moyenne_max[$id_classe][$id_periode])) {
								$total_moyenne_max_en_calcul=$total_moyenne_max_en_calcul+($matiere[$ident_eleve_aff][$id_periode][$m]['moy_max']*$matiere[$ident_eleve_aff][$id_periode][$m]['coef']);
							}
							*/
							$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
						}
						//$largeur_utilise = $largeur_utilise+$largeur_moyenne;


						// rang de l'élève
						if($tab_modele_pdf["active_rang"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'rang' ) {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
							$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
							// A REVOIR: J'AI l'EFFECTIF DU GROUPE, mais faut-il compter les élèves ABS, DISP,...?
							$pdf->Cell($tab_modele_pdf["largeur_rang"][$classe_id], $espace_entre_matier, $tab_bull['rang'][$i][$m].'/'.$tab_bull['groupe'][$j]['effectif'],1,0,'C');
							$largeur_utilise = $largeur_utilise+$tab_modele_pdf["largeur_rang"][$classe_id];
						}

						// graphique de niveau
						if($tab_modele_pdf["active_graphique_niveau"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'niveau' ) {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
							$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
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
								$place_eleve=$tab_bull['place_eleve'][$m][$i];
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
									$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
									$largeur_texte_sousmatiere = $pdf->GetStringWidth($tab_bull['groupe'][$m][$i]['cn_nom'][$n].': '.$tab_bull['groupe'][$m][$i]['cn_note'][$n]);
									if($largeur_sous_matiere<$largeur_texte_sousmatiere) { $largeur_sous_matiere=$largeur_texte_sousmatiere; }
									$n = $n + 1;
								}
								if($largeur_sous_matiere!='0') { $largeur_sous_matiere = $largeur_sous_matiere + 2; }
								$n=0;
								while( !empty($tab_bull['groupe'][$m][$i]['cn_nom'][$n]) )
								{
									$pdf->SetXY($X_sous_matiere, $Y_sous_matiere);
									$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
									$pdf->Cell($largeur_sous_matiere, $espace_entre_matier/count($tab_bull['groupe'][$m][$i]['cn_nom']), $tab_bull['groupe'][$m][$i]['cn_nom'][$n].': '.$tab_bull['groupe'][$m][$i]['cn_note'][$n],1,0,'L');
									$Y_sous_matiere = $Y_sous_matiere+$espace_entre_matier/count($tab_bull['groupe'][$m][$i]['cn_nom']);
									$n = $n + 1;
								}
								$largeur_utilise = $largeur_utilise+$largeur_sous_matiere;
							}
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
							// calcul de la taille du texte des appréciation
							$hauteur_caractere_appreciation = 9;
							$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere_appreciation);

							//suppression des espace en début et en fin
							$app_aff = trim($tab_bull['app'][$m][$i]);

							$taille_texte_total = $pdf->GetStringWidth($app_aff);
							$largeur_appreciation2 = $largeur_appreciation - $largeur_sous_matiere;

							//$taille_texte = (($espace_entre_matier/3)*$largeur_appreciation2);
							$nb_ligne_app = '2.8';
							$taille_texte_max = $nb_ligne_app * ($largeur_appreciation2-4);
							$grandeur_texte='test';
							while($grandeur_texte!='ok') {
								if($taille_texte_max < $taille_texte_total)
								{
									$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.3;
									$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',$hauteur_caractere_appreciation);
									$taille_texte_total = $pdf->GetStringWidth($app_aff);
								} else { $grandeur_texte='ok'; }
							}
							$grandeur_texte='test';
							$pdf->drawTextBox($app_aff, $largeur_appreciation2, $espace_entre_matier, 'J', 'M', 1);
							$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
							$largeur_utilise = $largeur_utilise + $largeur_appreciation2;
							//$largeur_utilise = 0;
						}

						$cpt_ordre = $cpt_ordre + 1;
					}
					$largeur_utilise = 0;
					// fin de boucle d'ordre
					$Y_decal = $Y_decal+($espace_entre_matier/2);
				}
			}



			// Ligne moyenne générale
			//bas du tableau des note et app si les affichage des moyennes ne sont pas affiché le bas du tableau ne seras pas affiché
			if ( $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $tab_modele_pdf["active_moyenne_general"][$classe_id] === '1' ) {
				$X_note_moy_app = $tab_modele_pdf["X_note_app"][$classe_id];
				$Y_note_moy_app = $tab_modele_pdf["Y_note_app"][$classe_id]+$tab_modele_pdf["hauteur_note_app"][$classe_id]-$tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id];
				$pdf->SetXY($X_note_moy_app, $Y_note_moy_app);
				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
				$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
				$pdf->Cell($tab_modele_pdf["largeur_matiere"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], "Moyenne générale",1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
				$largeur_utilise = $tab_modele_pdf["largeur_matiere"][$classe_id];

				// coefficient matière
				if($tab_modele_pdf["active_coef_moyenne"][$classe_id]==='1') {
					$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
					$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);
					$pdf->Cell($tab_modele_pdf["largeur_coef_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], "",1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
					$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_coef_moyenne"][$classe_id];
				}

				// nombre de note
				if($tab_modele_pdf["active_nombre_note_case"][$classe_id]==='1') {
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
						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'B',10);
						$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);

						// On a deux paramètres de couleur qui se croisent. On utilise une variable tierce.
						$utilise_couleur = $tab_modele_pdf["couleur_moy_general"][$classe_id];
						if($tab_modele_pdf["active_reperage_eleve"][$classe_id]==='1') {
							// Si on affiche une couleur spécifique pour les moyennes de l'élève,
							// on utilise cette couleur ici aussi, quoi qu'il arrive
							$pdf->SetFillColor($tab_modele_pdf["couleur_reperage_eleve1"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve2"][$classe_id], $tab_modele_pdf["couleur_reperage_eleve3"][$classe_id]);
							$utilise_couleur = 1;
						}

						$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], present_nombre($tab_bull['moy_gen_eleve'][$i], $tab_modele_pdf["arrondie_choix"][$classe_id], $tab_modele_pdf["nb_chiffre_virgule"][$classe_id], $tab_modele_pdf["chiffre_avec_zero"][$classe_id]),1,0,'C',$utilise_couleur);

						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
						$pdf->SetFillColor(0, 0, 0);
						$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
					}

					//classe
					if($tab_modele_pdf["active_moyenne_classe"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' ) {
						$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
						$pdf->SetFillColor($tab_modele_pdf["couleur_moy_general1"][$classe_id], $tab_modele_pdf["couleur_moy_general2"][$classe_id], $tab_modele_pdf["couleur_moy_general3"][$classe_id]);

						if( $total_coef_en_calcul != 0){
							$moyenne_classe = $total_moyenne_classe_en_calcul / $total_coef_en_calcul;
						}
						else{
							$moyenne_classe = '-';
						}

						if ( $moyenne_classe != '-' ) {
							$moyenne_classe=$tab_bull['moy_generale_classe'];
							$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $moyenne_classe,1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
						} else {
							$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], '-',1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
						}
						$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
					}

					//min
					if($tab_modele_pdf["active_moyenne_min"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' ) {
						$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
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
							$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $tab_bull['moy_min_classe'],1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
						} else {
							$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], '-',1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
						}
						$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
					}

					//max
					if($tab_modele_pdf["active_moyenne_max"][$classe_id]==='1' and $tab_modele_pdf["active_moyenne"][$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' ) {
						$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
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
							$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], $moyenne_max,1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
						} else {
							$pdf->Cell($tab_modele_pdf["largeur_d_une_moyenne"][$classe_id], $tab_modele_pdf["hauteur_entete_moyenne_general"][$classe_id], '-',1,0,'C', $tab_modele_pdf["couleur_moy_general"][$classe_id]);
						}
						$largeur_utilise = $largeur_utilise + $tab_modele_pdf["largeur_d_une_moyenne"][$classe_id];
					}

					// rang de l'élève
					if($tab_modele_pdf["active_rang"][$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'rang') {
						$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
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
							$place_eleve=$tab_bull['place_eleve_classe'][$i];
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


			// =============== bloc absence ==================
			if($tab_modele_pdf["active_bloc_absence"][$classe_id]==='1') {
				$pdf->SetXY($tab_modele_pdf["X_absence"][$classe_id], $tab_modele_pdf["Y_absence"][$classe_id]);
				$origine_Y_absence = $tab_modele_pdf["Y_absence"][$classe_id];
				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'I',8);
				$info_absence='';
				if($tab_bull['eleve'][$i]['eleve_absences'] != '?') {
					if($tab_bull['eleve'][$i]['eleve_absences'] == '0')
					{
						$info_absence="<i>Aucune demi-journée d'absence</i>.";
					} else {
						$info_absence="<i>Nombre de demi-journées d'absence ";
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
						$info_absence = $info_absence.".";
					}
				}
				if($tab_bull['eleve'][$i]['eleve_retards'] != '0' and $tab_bull['eleve'][$i]['eleve_retards'] != '?')
				{
					$info_absence = $info_absence."<i> Nombre de retards : </i><b>".$tab_bull['eleve'][$i]['eleve_retards']."</b>";
				}

				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);

				$info_absence = $info_absence." (C.P.E. chargé";
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
				$info_absence = $info_absence." du suivi : ".affiche_utilisateur($tab_bull['eleve'][$i]['cperesp_login'],$tab_bull['id_classe']).")";
				$pdf->MultiCellTag(200, 5, $info_absence, '', 'J', '');



				if ( isset($Y_avis_cons_init) ) { $tab_modele_pdf["Y_avis_cons"][$classe_id] = $Y_avis_cons_init; }
				if ( isset($Y_sign_chef_init) ) { $tab_modele_pdf["Y_sign_chef"][$classe_id] = $Y_sign_chef_init; }
				if ( !isset($Y_avis_cons_init) ) { $Y_avis_cons_init = $tab_modele_pdf["Y_avis_cons"][$classe_id] + 0.5; }
				if ( !isset($Y_sign_chef_init) ) { $Y_sign_chef_init = $tab_modele_pdf["Y_sign_chef"][$classe_id] + 0.5; }

				if ( isset($hauteur_avis_cons_init) ) { $tab_modele_pdf["hauteur_avis_cons"][$classe_id] = $hauteur_avis_cons_init; }
				if ( isset($hauteur_sign_chef_init) ) { $tab_modele_pdf["hauteur_sign_chef"][$classe_id] = $hauteur_sign_chef_init; }
				if ( !isset($hauteur_avis_cons_init) ) { $hauteur_avis_cons_init = $tab_modele_pdf["hauteur_avis_cons"][$classe_id] - 0.5; }
				if ( !isset($hauteur_sign_chef_init) ) { $hauteur_sign_chef_init = $tab_modele_pdf["hauteur_sign_chef"][$classe_id] - 0.5; }

				if($tab_bull['eleve'][$i]['appreciation_absences'] != "")
				{
					// supprimer les espaces
					$text_absences_appreciation = trim(str_replace(array("\r\n","\r","\n"), ' ', unhtmlentities($tab_bull['eleve'][$i]['appreciation_absences'])));
					$info_absence_appreciation = "<i>Avis CPE:</i> <b>".$text_absences_appreciation."</b>";
					$text_absences_appreciation = '';
					$pdf->SetXY($tab_modele_pdf["X_absence"][$classe_id], $tab_modele_pdf["Y_absence"][$classe_id]+4);
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',8);
					$pdf->MultiCellTag(200, 3, $info_absence_appreciation, '', 'J', '');
					$val = $pdf->GetStringWidth($info_absence_appreciation);
					// nombre de lignes que prend la remarque cpe
					//Arrondi à l'entier supérieur : ceil()
					$nb_ligne = 1;
					$nb_ligne = ceil($val / 200);
					$hauteur_pris = $nb_ligne * 3;

					$tab_modele_pdf["Y_avis_cons"][$classe_id] = $tab_modele_pdf["Y_avis_cons"][$classe_id] + $hauteur_pris;
					$tab_modele_pdf["hauteur_avis_cons"][$classe_id] = $tab_modele_pdf["hauteur_avis_cons"][$classe_id] - ( $hauteur_pris + 0.5 );
					$tab_modele_pdf["Y_sign_chef"][$classe_id] = $tab_modele_pdf["Y_sign_chef"][$classe_id] + $hauteur_pris;
					$tab_modele_pdf["hauteur_sign_chef"][$classe_id] = $tab_modele_pdf["hauteur_sign_chef"][$classe_id] - ( $hauteur_pris + 0.5 );
					$hauteur_pris = 0;
				} else {
					if($Y_avis_cons_init!=$tab_modele_pdf["Y_avis_cons"][$classe_id])
					{
						$tab_modele_pdf["Y_avis_cons"][$classe_id] = $tab_modele_pdf["Y_avis_cons"][$classe_id] - $hauteur_pris;
						$tab_modele_pdf["hauteur_avis_cons"][$classe_id] = $tab_modele_pdf["hauteur_avis_cons"][$classe_id] + $hauteur_pris;
						$tab_modele_pdf["Y_sign_chef"][$classe_id] = $tab_modele_pdf["Y_sign_chef"][$classe_id] - $hauteur_pris;
						$tab_modele_pdf["hauteur_sign_chef"][$classe_id] = $tab_modele_pdf["hauteur_sign_chef"][$classe_id] + $hauteur_pris;
						$hauteur_pris = 0;
					}
				}
				$info_absence = '';
				$info_absence_appreciation = '';
				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
			}

			// si le bloc absence n'est pas activé
			if($tab_modele_pdf["active_bloc_absence"][$classe_id] != '1') {
				if ( isset($Y_avis_cons_init) ) { $tab_modele_pdf["Y_avis_cons"][$classe_id] = $Y_avis_cons_init; }
				if ( isset($Y_sign_chef_init) ) { $tab_modele_pdf["Y_sign_chef"][$classe_id] = $Y_sign_chef_init; }
				if ( !isset($Y_avis_cons_init) ) { $Y_avis_cons_init = $tab_modele_pdf["Y_avis_cons"][$classe_id]; }
				if ( !isset($Y_sign_chef_init) ) { $Y_sign_chef_init = $tab_modele_pdf["Y_sign_chef"][$classe_id]; }
			}
			// fin

			if($Y_avis_cons_init!=$tab_modele_pdf["Y_avis_cons"][$classe_id]) {
				$Y_avis_cons[$classe_id] = $tab_modele_pdf["Y_avis_cons"][$classe_id] + 0.5;
				$Y_sign_chef[$classe_id] = $tab_modele_pdf["Y_sign_chef"][$classe_id] + 0.5;
			}


			// ================ bloc avis du conseil de classe =================
			if($tab_modele_pdf["active_bloc_avis_conseil"][$classe_id]==='1') {
				if($tab_modele_pdf["cadre_avis_cons"][$classe_id]!=0) {
					$pdf->Rect($tab_modele_pdf["X_avis_cons"][$classe_id], $tab_modele_pdf["Y_avis_cons"][$classe_id], $tab_modele_pdf["longeur_avis_cons"][$classe_id], $tab_modele_pdf["hauteur_avis_cons"][$classe_id], 'D');
				}
				$pdf->SetXY($tab_modele_pdf["X_avis_cons"][$classe_id],$tab_modele_pdf["Y_avis_cons"][$classe_id]);
				if ( $tab_modele_pdf["taille_titre_bloc_avis_conseil"][$classe_id] != '' and $tab_modele_pdf["taille_titre_bloc_avis_conseil"][$classe_id] < '15' ) {
					$taille = $tab_modele_pdf["taille_titre_bloc_avis_conseil"][$classe_id];
				} else {
					$taille = '10';
				}
				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'I',$taille);
				if ( $tab_modele_pdf["titre_bloc_avis_conseil"][$classe_id] != '' ) {
					$tt_avis = $tab_modele_pdf["titre_bloc_avis_conseil"][$classe_id];
				} else {
					$tt_avis = 'Avis du Conseil de classe:';
				}
				$pdf->Cell($tab_modele_pdf["longeur_avis_cons"][$classe_id],5, $tt_avis,0,2,'');
				$pdf->SetXY($tab_modele_pdf["X_avis_cons"][$classe_id]+2.5,$tab_modele_pdf["Y_avis_cons"][$classe_id]+5);
				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
				$texteavis = $tab_bull['avis'][$i];
				$pdf->drawTextBox($texteavis, $tab_modele_pdf["longeur_avis_cons"][$classe_id]-5, $tab_modele_pdf["hauteur_avis_cons"][$classe_id]-10, 'J', 'M', 0);
				$X_pp_aff=$tab_modele_pdf["X_avis_cons"][$classe_id];
				$Y_pp_aff=$tab_modele_pdf["Y_avis_cons"][$classe_id]+$tab_modele_pdf["hauteur_avis_cons"][$classe_id]-5;
				$pdf->SetXY($X_pp_aff,$Y_pp_aff);
				if ( $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id] != '' and $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id] < '15' ) {
					$taille = $tab_modele_pdf["taille_profprincipal_bloc_avis_conseil"][$classe_id];
				} else {
					$taille = '10';
				}
				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'I',$taille);
				// Le nom du professeur principal
				$pp_classe[$i]="";
				if(isset($tab_bull['eleve'][$i]['pp']['login'])) {
					//echo "<b>".ucfirst($gepi_prof_suivi)."</b> ";
					$pp_classe[$i]=affiche_utilisateur($tab_bull['eleve'][$i]['pp']['login'],$tab_bull['eleve'][$i]['id_classe']);
				}
				else {
					$pp_classe[$i]="";
				}
				$pdf->MultiCellTag(200, 5, $pp_classe[$i], '', 'J', '');
			}


			// ======================= bloc du président du conseil de classe ================
			if( $tab_modele_pdf["active_bloc_chef"][$classe_id] === '1' ) {
				if( $tab_modele_pdf["cadre_sign_chef"][$classe_id] != 0 ) {
					$pdf->Rect($tab_modele_pdf["X_sign_chef"][$classe_id], $tab_modele_pdf["Y_sign_chef"][$classe_id], $tab_modele_pdf["longeur_sign_chef"][$classe_id], $tab_modele_pdf["hauteur_sign_chef"][$classe_id], 'D');
				}
				$pdf->SetXY($tab_modele_pdf["X_sign_chef"][$classe_id],$tab_modele_pdf["Y_sign_chef"][$classe_id]);
				$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'',10);
				if( $tab_modele_pdf["affichage_haut_responsable"][$classe_id] === '1' ) {
					if ( $tab_modele_pdf["affiche_fonction_chef"][$classe_id] === '1' ){
						if ( $tab_modele_pdf["taille_texte_fonction_chef"][$classe_id] != '' and $tab_modele_pdf["taille_texte_fonction_chef"][$classe_id] != '0' and $tab_modele_pdf["taille_texte_fonction_chef"][$classe_id] < '15' ) {
							$taille = $tab_modele_pdf["taille_texte_fonction_chef"][$classe_id];
						} else {
							$taille = '10';
						}
						$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'B',$taille);
						$pdf->Cell($tab_modele_pdf["longeur_sign_chef"][$classe_id],5, $info_classe[$id_classe_selection]['fonction_hautresponsable'],0,2,'');
					}
					if ( $tab_modele_pdf["taille_texte_identitee_chef"][$classe_id] != '' and $tab_modele_pdf["taille_texte_identitee_chef"][$classe_id] != '0' and $tab_modele_pdf["taille_texte_identitee_chef"][$classe_id] < '15' ) {
						$taille = $tab_modele_pdf["taille_texte_identitee_chef"][$classe_id];
					} else {
						$taille_avis = '8';
					}
					$pdf->SetFont($tab_modele_pdf["caractere_utilse"][$classe_id],'I',$taille);
					$pdf->Cell($tab_modele_pdf["longeur_sign_chef"][$classe_id],5, $tab_bull['suivi_par'],0,2,'');
				} else {
					//$pdf->MultiCell($longeur_sign_chef[$classe_id],5, "Visa du Chef d'établissement\nou de son délégué",0,2,'');
					$pdf->MultiCell($tab_modele_pdf["longeur_sign_chef"][$classe_id],5, "Visa du Chef d'établissement\nou de son délégué",0,2,'');
				}
			}

		}
	}
}

?>
