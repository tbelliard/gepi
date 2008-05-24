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

function redimensionne_image($photo){
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

?>
