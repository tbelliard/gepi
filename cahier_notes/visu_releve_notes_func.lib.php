<?php

/*
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
*/

function fich_debug($texte) {
	$fich=fopen("/tmp/visu_releve.txt","a+");
	fwrite($fich,$texte);
	fclose($fich);
}

function redimensionne_image_releve($photo){
	//global $bull_photo_largeur_max, $bull_photo_hauteur_max;
	global $releve_photo_largeur_max, $releve_photo_hauteur_max;

	// prendre les informations sur l'image
	$info_image=getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur=$info_image[0];
	$hauteur=$info_image[1];

	// calcule le ratio de redimensionnement
	$ratio_l=$largeur/$releve_photo_largeur_max;
	$ratio_h=$hauteur/$releve_photo_hauteur_max;
	$ratio=($ratio_l>$ratio_h)?$ratio_l:$ratio_h;

	// définit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur=round($largeur/$ratio);
	$nouvelle_hauteur=round($hauteur/$ratio);

	//fich_debug("photo=$photo\nlargeur=$largeur\nhauteur=$hauteur\nratio_l=$ratio_l\nratio_h=$ratio_h\nratio=$ratio\nnouvelle_largeur=$nouvelle_largeur\nnouvelle_hauteur=$nouvelle_hauteur\n===============\n");

	return array($nouvelle_largeur, $nouvelle_hauteur);
}

//echo "\$releve_photo_largeur_max=$releve_photo_largeur_max<br />";
//echo "\$releve_photo_hauteur_max=$releve_photo_hauteur_max<br />";

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

// $tab_reletin[$id_classe][$periode_num]
// $i indice élève
//function releve_html($tab_rel,$i) {
function releve_html($tab_rel,$i,$num_releve_specifie) {
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
		$choix_periode,	// 'periode' ou 'intervalle'
		$chaine_coef,	// 'coef.:'
		//============================================

		// Paramètres d'impression des bulletins HTML:

		// Mise en page du bulletin scolaire
		$releve_body_marginleft,
		// $titlesize, $textsize, $p_bulletin_margin sont récupérés plus haut dans l'entête pour écrire les styles
		$releve_largeurtableau,

		$releve_col_matiere_largeur,
		//$col_note_largeur,
		//$col_boite_largeur,
		//$col_hauteur,		// La hauteur minimale de ligne n'est exploitée que dans les boites/conteneurs
		$releve_cellpadding,
		$releve_cellspacing,
		$releve_ecart_entete,
		//$bull_espace_avis,
		// $bull_bordure_classique permet de renseigner $class_bordure
		//$class_bordure,
		$releve_class_bordure,

		$releve_categ_font_size,
		$releve_categ_bgcolor,
		//======================
		//$bull_categ_font_size_avis,
		//$bull_police_avis,
		//$bull_font_style_avis,
		// Ils sont utilisés dans l'entête pour générer les styles
		//======================
		$genre_periode,
		$releve_affich_nom_etab,
		$releve_affich_adr_etab,

		// Informations devant figurer sur le bulletin scolaire
		$releve_mention_nom_court,
		$releve_mention_doublant,
		$releve_affiche_eleve_une_ligne,
		//$releve_affiche_appreciations,
		//$releve_affiche_absences,
		//$releve_affiche_avis,
		//$releve_affiche_aid,
		$releve_affiche_numero,		// affichage du numéro du bulletin
		// L'affichage des graphes devrait provenir des Paramètres d'impression des bulletins HTML, mais le paramètre a été stocké dans $tab_rel
		//$releve_affiche_signature,	// affichage du nom du PP et du chef d'établissement
		$releve_affiche_etab,			// Etablissement d'origine


		$activer_photo_releve,
		// $releve_photo_largeur_max et $releve_photo_hauteur_max sont récupérées via global dans redimensionne_image()

		$releve_affiche_tel,
		$releve_affiche_fax,
		$releve_intitule_app,
		$releve_affiche_INE_eleve,
		$releve_affiche_formule,
		$releve_formule_bas,
		// Nom du fichier déterminé d'après le paramètre choix_bulletin
		$fichier_bulletin,
		$min_max_moyclas,

		// Bloc adresse responsable
		$releve_addressblock_padding_right,
		$releve_addressblock_padding_top,
		$releve_addressblock_padding_text,
		$releve_addressblock_length,
		$releve_addressblock_font_size,
		//addressblock_logo_etab_prop correspond au pourcentage $largeur1 et $largeur2 est le complément à 100%
		$releve_addressblock_logo_etab_prop,
		$releve_addressblock_autre_prop,
		// Pourcentage calculé par rapport au tableau contenant le bloc Classe, Année,...
		$releve_addressblock_classe_annee2,
		// Nombre de sauts de ligne entre le bloc Logo+Etablissement et le bloc Nom, prénom,... de l'élève
		$releve_ecart_bloc_nom,
		$releve_addressblock_debug,

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

    // Pour retourner à la ligne entre les devoirs dans le cas où le nom ou l'appréciation du devoir est demandée:
    $retour_a_la_ligne="y";
    // Passer à "n" pour désactiver le retour à la ligne.


/*
$affiche_categories

$avec_appreciation_devoir
$avec_nom_devoir
$avec_tous_coef_devoir

$avec_coef_devoir
$tab_releve[$id_classe]['rn_coefdev_si_diff']

$tab_ele['groupe'][$j]['differents_coef']
$affiche_coef

$avec_date_devoir
*/

	$id_classe=$tab_rel['id_classe'];

	// Pour n'imprimer qu'un relevé dans le cas où on n'imprime pas les adresses des responsables
	$nb_releves=1;

	unset($tab_adr_ligne1);
	unset($tab_adr_ligne2);
	unset($tab_adr_ligne3);

	//if ($tab_rel['affiche_adresse'] == 'y') {
	// On fait le travail sur $tab_adr_ligne1 même si on ne souhaite pas afficher l'adresse des responsables parce que c'est aussi cette démarche qui permet de déterminer $nb_releves

		// Préparation des lignes adresse responsable
		if (!isset($tab_rel['eleve'][$i]['resp'][0])) {
			$tab_adr_ligne1[0]="<font color='red'><b>ADRESSE MANQUANTE</b></font>";
			$tab_adr_ligne2[0]="";
			$tab_adr_ligne3[0]="";
		}
		else {
			if (isset($tab_rel['eleve'][$i]['resp'][1])) {
				if((isset($tab_rel['eleve'][$i]['resp'][1]['adr1']))&&
					(isset($tab_rel['eleve'][$i]['resp'][1]['adr2']))&&
					(isset($tab_rel['eleve'][$i]['resp'][1]['adr3']))&&
					(isset($tab_rel['eleve'][$i]['resp'][1]['adr4']))&&
					(isset($tab_rel['eleve'][$i]['resp'][1]['cp']))&&
					(isset($tab_rel['eleve'][$i]['resp'][1]['commune']))
				) {
					// Le deuxième responsable existe et est renseigné
					if (($tab_rel['eleve'][$i]['resp'][0]['adr_id']==$tab_rel['eleve'][$i]['resp'][1]['adr_id']) OR
						(
							($tab_rel['eleve'][$i]['resp'][0]['adr1']==$tab_rel['eleve'][$i]['resp'][1]['adr1'])&&
							($tab_rel['eleve'][$i]['resp'][0]['adr2']==$tab_rel['eleve'][$i]['resp'][1]['adr2'])&&
							($tab_rel['eleve'][$i]['resp'][0]['adr3']==$tab_rel['eleve'][$i]['resp'][1]['adr3'])&&
							($tab_rel['eleve'][$i]['resp'][0]['adr4']==$tab_rel['eleve'][$i]['resp'][1]['adr4'])&&
							($tab_rel['eleve'][$i]['resp'][0]['cp']==$tab_rel['eleve'][$i]['resp'][1]['cp'])&&
							($tab_rel['eleve'][$i]['resp'][0]['commune']==$tab_rel['eleve'][$i]['resp'][1]['commune'])
						)
					) {
						// Les adresses sont identiques
						$nb_releves=1;

						if(($tab_rel['eleve'][$i]['resp'][0]['nom']!=$tab_rel['eleve'][$i]['resp'][1]['nom'])&&
							($tab_rel['eleve'][$i]['resp'][1]['nom']!="")) {
							// Les noms des responsables sont différents
							$tab_adr_ligne1[0]=$tab_rel['eleve'][$i]['resp'][0]['civilite']." ".$tab_rel['eleve'][$i]['resp'][0]['nom']." ".$tab_rel['eleve'][$i]['resp'][0]['prenom']." et ".$tab_rel['eleve'][$i]['resp'][1]['civilite']." ".$tab_rel['eleve'][$i]['resp'][1]['nom']." ".$tab_rel['eleve'][$i]['resp'][1]['prenom'];
						}
						else{
							if(($tab_rel['eleve'][$i]['resp'][0]['civilite']!="")&&($tab_rel['eleve'][$i]['resp'][1]['civilite']!="")) {
								$tab_adr_ligne1[0]=$tab_rel['eleve'][$i]['resp'][0]['civilite']." et ".$tab_rel['eleve'][$i]['resp'][1]['civilite']." ".$tab_rel['eleve'][$i]['resp'][0]['nom']." ".$tab_rel['eleve'][$i]['resp'][0]['prenom'];
							}
							else {
								$tab_adr_ligne1[0]="M. et Mme ".$tab_rel['eleve'][$i]['resp'][0]['nom']." ".$tab_rel['eleve'][$i]['resp'][0]['prenom'];
							}
						}

						$tab_adr_ligne2[0]=$tab_rel['eleve'][$i]['resp'][0]['adr1'];
						if($tab_rel['eleve'][$i]['resp'][0]['adr2']!=""){
							$tab_adr_ligne2[0].="<br />\n".$tab_rel['eleve'][$i]['resp'][0]['adr2'];
						}
						if($tab_rel['eleve'][$i]['resp'][0]['adr3']!=""){
							$tab_adr_ligne2[0].="<br />\n".$tab_rel['eleve'][$i]['resp'][0]['adr3'];
						}
						if($tab_rel['eleve'][$i]['resp'][0]['adr4']!=""){
							$tab_adr_ligne2[0].="<br />\n".$tab_rel['eleve'][$i]['resp'][0]['adr4'];
						}
						$tab_adr_ligne3[0]=$tab_rel['eleve'][$i]['resp'][0]['cp']." ".$tab_rel['eleve'][$i]['resp'][0]['commune'];

						if(($tab_rel['eleve'][$i]['resp'][0]['pays']!="")&&(strtolower($tab_rel['eleve'][$i]['resp'][0]['pays'])!=strtolower($gepiSchoolPays))) {
							if($tab_adr_ligne3[0]!=" "){
								$tab_adr_ligne3[0].="<br />";
							}
							$tab_adr_ligne3[0].=$tab_rel['eleve'][$i]['resp'][0]['pays'];
						}
					}
					else {
						// Les adresses sont différentes
						//if ($un_seul_bull_par_famille!="oui") {
						// On teste en plus si la deuxième adresse est valide
						if (($un_seul_bull_par_famille!="oui")&&
							($tab_rel['eleve'][$i]['resp'][1]['adr1']!="")&&
							($tab_rel['eleve'][$i]['resp'][1]['commune']!="")
						) {
							$nb_releves=2;
						}
						else {
							$nb_releves=1;
						}

						for($cpt=0;$cpt<$nb_releves;$cpt++) {
							if($tab_rel['eleve'][$i]['resp'][$cpt]['civilite']!="") {
								$tab_adr_ligne1[$cpt]=$tab_rel['eleve'][$i]['resp'][$cpt]['civilite']." ".$tab_rel['eleve'][$i]['resp'][$cpt]['nom']." ".$tab_rel['eleve'][$i]['resp'][$cpt]['prenom'];
							}
							else {
								$tab_adr_ligne1[$cpt]=$tab_rel['eleve'][$i]['resp'][$cpt]['nom']." ".$tab_rel['eleve'][$i]['resp'][$cpt]['prenom'];
							}

							$tab_adr_ligne2[$cpt]=$tab_rel['eleve'][$i]['resp'][$cpt]['adr1'];
							if($tab_rel['eleve'][$i]['resp'][$cpt]['adr2']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_rel['eleve'][$i]['resp'][$cpt]['adr2'];
							}
							if($tab_rel['eleve'][$i]['resp'][$cpt]['adr3']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_rel['eleve'][$i]['resp'][$cpt]['adr3'];
							}
							if($tab_rel['eleve'][$i]['resp'][$cpt]['adr4']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_rel['eleve'][$i]['resp'][$cpt]['adr4'];
							}
							$tab_adr_ligne3[$cpt]=$tab_rel['eleve'][$i]['resp'][$cpt]['cp']." ".$tab_rel['eleve'][$i]['resp'][$cpt]['commune'];

							if(($tab_rel['eleve'][$i]['resp'][$cpt]['pays']!="")&&(strtolower($tab_rel['eleve'][$i]['resp'][$cpt]['pays'])!=strtolower($gepiSchoolPays))) {
								if($tab_adr_ligne3[$cpt]!=" "){
									$tab_adr_ligne3[$cpt].="<br />";
								}
								$tab_adr_ligne3[$cpt].=$tab_rel['eleve'][$i]['resp'][$cpt]['pays'];
							}

						}

					}
				}
				else {
					// Il n'y a pas de deuxième adresse, mais il y aurait un deuxième responsable???
					// CA NE DEVRAIT PAS ARRIVER ETANT DONNé LA REQUETE EFFECTUEE QUI JOINT resp_pers ET resp_adr...
						if ($un_seul_bull_par_famille!="oui") {
							$nb_releves=2;
						}
						else {
							$nb_releves=1;
						}

						for($cpt=0;$cpt<$nb_releves;$cpt++) {
							if($tab_rel['eleve'][$i]['resp'][$cpt]['civilite']!="") {
								$tab_adr_ligne1[$cpt]=$tab_rel['eleve'][$i]['resp'][$cpt]['civilite']." ".$tab_rel['eleve'][$i]['resp'][$cpt]['nom']." ".$tab_rel['eleve'][$i]['resp'][$cpt]['prenom'];
							}
							else {
								$tab_adr_ligne1[$cpt]=$tab_rel['eleve'][$i]['resp'][$cpt]['nom']." ".$tab_rel['eleve'][$i]['resp'][$cpt]['prenom'];
							}

							$tab_adr_ligne2[$cpt]=$tab_rel['eleve'][$i]['resp'][$cpt]['adr1'];
							if($tab_rel['eleve'][$i]['resp'][$cpt]['adr2']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_rel['eleve'][$i]['resp'][$cpt]['adr2'];
							}
							if($tab_rel['eleve'][$i]['resp'][$cpt]['adr3']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_rel['eleve'][$i]['resp'][$cpt]['adr3'];
							}
							if($tab_rel['eleve'][$i]['resp'][$cpt]['adr4']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_rel['eleve'][$i]['resp'][$cpt]['adr4'];
							}
							$tab_adr_ligne3[$cpt]=$tab_rel['eleve'][$i]['resp'][$cpt]['cp']." ".$tab_rel['eleve'][$i]['resp'][$cpt]['commune'];

							if(($tab_rel['eleve'][$i]['resp'][$cpt]['pays']!="")&&(strtolower($tab_rel['eleve'][$i]['resp'][$cpt]['pays'])!=strtolower($gepiSchoolPays))) {
								if($tab_adr_ligne3[$cpt]!=" "){
									$tab_adr_ligne3[$cpt].="<br />";
								}
								$tab_adr_ligne3[$cpt].=$tab_rel['eleve'][$i]['resp'][$cpt]['pays'];
							}
						}
				}
			}
			else {
				// Il n'y a pas de deuxième responsable
				$nb_releves=1;

				if($tab_rel['eleve'][$i]['resp'][0]['civilite']!="") {
					$tab_adr_ligne1[0]=$tab_rel['eleve'][$i]['resp'][0]['civilite']." ".$tab_rel['eleve'][$i]['resp'][0]['nom']." ".$tab_rel['eleve'][$i]['resp'][0]['prenom'];
				}
				else {
					$tab_adr_ligne1[0]=$tab_rel['eleve'][$i]['resp'][0]['nom']." ".$tab_rel['eleve'][$i]['resp'][0]['prenom'];
				}

				$tab_adr_ligne2[0]=$tab_rel['eleve'][$i]['resp'][0]['adr1'];
				if($tab_rel['eleve'][$i]['resp'][0]['adr2']!=""){
					$tab_adr_ligne2[0].="<br />\n".$tab_rel['eleve'][$i]['resp'][0]['adr2'];
				}
				if($tab_rel['eleve'][$i]['resp'][0]['adr3']!=""){
					$tab_adr_ligne2[0].="<br />\n".$tab_rel['eleve'][$i]['resp'][0]['adr3'];
				}
				if($tab_rel['eleve'][$i]['resp'][0]['adr4']!=""){
					$tab_adr_ligne2[0].="<br />\n".$tab_rel['eleve'][$i]['resp'][0]['adr4'];
				}
				$tab_adr_ligne3[0]=$tab_rel['eleve'][$i]['resp'][0]['cp']." ".$tab_rel['eleve'][$i]['resp'][0]['commune'];

				if(($tab_rel['eleve'][$i]['resp'][0]['pays']!="")&&(strtolower($tab_rel['eleve'][$i]['resp'][0]['pays'])!=strtolower($gepiSchoolPays))) {
					if($tab_adr_ligne3[0]!=" "){
						$tab_adr_ligne3[0].="<br />";
					}
					$tab_adr_ligne3[0].=$tab_rel['eleve'][$i]['resp'][0]['pays'];
				}
			}
		}
	//}
	// Fin de la préparation des lignes adresse responsable

	//echo "\$nb_releves=$nb_releves<br />";
	//echo "\$num_releve_specifie=$num_releve_specifie<br />";

	$num_premier_releve=0;
	if($num_releve_specifie!=-1) {
		$num_premier_releve=$num_releve_specifie;
		$nb_releves=$num_releve_specifie+1;
	}

	//echo "\$num_premier_releve=$num_premier_releve<br />";
	//echo "\$nb_releves=$nb_releves<br />";

	// Début des bulletins
	for ($num_releve=$num_premier_releve; $num_releve<$nb_releves; $num_releve++) {

		//echo "\$num_releve=$num_releve<br />";
		//echo "\$i=$i<br />";
		//echo "\$tab_rel['eleve'][$i]['nom']=".$tab_rel['eleve'][$i]['nom']."<br />";

		// Page de garde
		//if ( $affiche_page_garde == 'yes' OR $tab_rel['affiche_adresse'] == 'y') {
		if ($tab_rel['affiche_adresse'] == 'y') {

			// Affectation des lignes adresse responsable avec les lignes correspondant au bulletin en cours
			$ligne1=$tab_adr_ligne1[$num_releve];
			$ligne2=$tab_adr_ligne2[$num_releve];
			$ligne3=$tab_adr_ligne3[$num_releve];

			// Info affichée en haut de la page de garde
			$info_eleve_page_garde="Elève: ".$tab_rel['eleve'][$i]['nom']." ".$tab_rel['eleve'][$i]['prenom'].", ".$tab_rel['eleve'][$i]['classe'];

			//if ($affiche_page_garde == "yes") {
			//	include "./page_garde.php";
			//	// Saut de page
			//	echo "<p class='saut'>&nbsp;</p>\n";
			//}
		}


		if($tab_rel['affiche_adresse'] == 'y') {
			//-------------------------------
			// Maintenant, on affiche l'en-tête : Les données de l'élève, le bloc adresse responsable et l'adresse du lycée.
			//-------------------------------

			echo "\n<!-- Début du cadre entête -->\n";
			echo "<div";
			if($releve_addressblock_debug=="y"){echo " style='border:1px solid red;'";}
			echo ">\n";
			// Pour éviter que le bloc-adresse ne remonte au-delà du saut de page:
			echo "<div style='clear: both; font-size: xx-small;'>&nbsp;</div>\n";

			// Cadre adresse du responsable:
			echo "<div style='float:right;
width:".$releve_addressblock_length."mm;
padding-top:".$releve_addressblock_padding_top."mm;
padding-bottom:".$releve_addressblock_padding_text."mm;
padding-right:".$releve_addressblock_padding_right."mm;\n";
			if($releve_addressblock_debug=="y"){echo "border: 1px solid blue;\n";}
			echo "font-size: ".$releve_addressblock_font_size."pt;
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
width:".$releve_addressblock_logo_etab_prop."%;\n";
			if($releve_addressblock_debug=="y"){echo "border: 1px solid green;\n";}
			echo "'>\n";

			echo "<table";
			if($releve_addressblock_debug=="y"){echo " border='1'";}
			echo " summary='Tableau des informations établissement'";
			echo ">\n";
			echo "<tr>\n";

			$nom_fic_logo = $logo_etab;
			$nom_fic_logo_c = "../images/".$nom_fic_logo;

			if (($nom_fic_logo != '') and (file_exists($nom_fic_logo_c))) {
				echo "<td style=\"text-align: left;\"><img src=\"".$nom_fic_logo_c."\" border=\"0\" alt=\"Logo\" /></td>\n";
			}
			echo "<td style='text-align: center;'>";
			echo "<p class='bulletin'>";
			if($releve_affich_nom_etab=="y"){
				echo "<span class=\"releve_grand\">".$gepiSchoolName."</span>";
			}
			if($releve_affich_adr_etab=="y"){
				echo "<br />\n".$gepiSchoolAdress1."<br />\n".$gepiSchoolAdress2."<br />\n".$gepiSchoolZipCode." ".$gepiSchoolCity;
				if($releve_affiche_tel=="y"){echo "<br />\nTel: ".$gepiSchoolTel;}
				if($releve_affiche_fax=="y"){echo "<br />\nFax: ".$gepiSchoolFax;}
			}
			echo "</p>\n";

			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			echo "<br />";


			// On rajoute des lignes vides
			$n = 0;
			while ($n < $releve_ecart_bloc_nom) {
				echo "<br />";
				$n++;
			}

			if ($activer_photo_releve=='y' and $active_module_trombinoscopes=='y') {
				$photo=nom_photo($tab_rel['eleve'][$i]['elenoet']);
				//echo "$photo";
				if("$photo"!=""){
					$photo="../photos/eleves/".$photo;
					if(file_exists($photo)){
						$dimphoto=redimensionne_image_releve($photo);
						echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />'."\n";
					}
				}
			}


			//affichage des données sur une seule ligne ou plusieurs
			if  ($releve_affiche_eleve_une_ligne == 'no') { // sur plusieurs lignes
				echo "<p class='bulletin'>\n";
				echo "<b><span class=\"releve_grand\">".$tab_rel['eleve'][$i]['nom']." ".$tab_rel['eleve'][$i]['prenom']."</span></b><br />";
				echo "Né";
				if (strtoupper($tab_rel['eleve'][$i]['sexe'])== "F") {echo "e";}
				echo "&nbsp;le&nbsp;".$tab_rel['eleve'][$i]['naissance'];
				//Eric Ajout
				echo "<br />";
				if ($tab_rel['eleve'][$i]['regime'] == "d/p") {echo "Demi-pensionnaire";}
				if ($tab_rel['eleve'][$i]['regime'] == "ext.") {echo "Externe";}
				if ($tab_rel['eleve'][$i]['regime'] == "int.") {echo "Interne";}
				if ($tab_rel['eleve'][$i]['regime'] == "i-e"){
					echo "Interne&nbsp;externé";
					if (strtoupper($tab_rel['eleve'][$i]['sexe'])!= "F") {echo "e";}
				}
				//Eric Ajout
				if ($releve_mention_doublant == 'yes'){
					if ($tab_rel['eleve'][$i]['doublant'] == 'R'){
					echo "<br />";
					echo "Redoublant";
					if (strtoupper($tab_rel['eleve'][$i]['sexe'])!= "F") {echo "e";}
					}
				}

				if ($releve_mention_nom_court == 'no') {
					//Eric Ajout et supp
					//echo "<BR />";
					//echo ", $current_classe";
				} else {
					echo "<br />";
					echo $tab_rel['eleve'][$i]['classe'];
				}
			}
			else { //sur une ligne
				echo "<p class='bulletin'>\n";
				echo "<b><span class=\"releve_grand\">".$tab_rel['eleve'][$i]['nom']." ".$tab_rel['eleve'][$i]['prenom']."</span></b><br />";
				echo "Né";
				if (strtoupper($tab_rel['eleve'][$i]['sexe'])== "F") {echo "e";}
				echo "&nbsp;le&nbsp;".$tab_rel['eleve'][$i]['naissance'];
				if ($tab_rel['eleve'][$i]['regime'] == "d/p") {echo ", Demi-pensionnaire";}
				if ($tab_rel['eleve'][$i]['regime'] == "ext.") {echo ", Externe";}
				if ($tab_rel['eleve'][$i]['regime'] == "int.") {echo ", Interne";}
				if ($tab_rel['eleve'][$i]['regime'] == "i-e"){
					echo ", Interne&nbsp;externé";
					if (strtoupper($tab_rel['eleve'][$i]['sexe'])!= "F") {echo "e";}
				}
				if ($releve_mention_doublant == 'yes'){
					if ($tab_rel['eleve'][$i]['doublant'] == 'R'){
						echo ", Redoublant";
						if (strtoupper($tab_rel['eleve'][$i]['sexe'])!= "F") {echo "e";}
					}
				}
				if ($releve_mention_nom_court == 'yes') {
					echo ", ".$tab_rel['eleve'][$i]['classe'];
				}
			}

			if($releve_affiche_INE_eleve=="y"){
				echo "<br />\n";
				echo "Numéro INE: ".$tab_rel['eleve'][$i]['no_gep'];
			}

			if($releve_affiche_etab=="y"){
				if ((isset($tab_rel['eleve'][$i]['etab_nom']))&&($tab_rel['eleve'][$i]['etab_nom']!='')) {
					echo "<br />\n";
					if ($tab_rel['eleve'][$i]['etab_id'] != '990') {
						if ($RneEtablissement != $tab_rel['eleve'][$i]['etab_id']) {
							echo "Etablissement d'origine : ";
							echo $tab_rel['eleve'][$i]['etab_niveau_nom']." ".$tab_rel['eleve'][$i]['etab_type']." ".$tab_rel['eleve'][$i]['etab_nom']." (".$tab_rel['eleve'][$i]['etab_cp']." ".$tab_rel['eleve'][$i]['etab_ville'].")\n";
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
			echo "<table width='".$releve_addressblock_autre_prop."%' ";
			if($releve_addressblock_debug=="y"){echo "border='1' ";}
			echo "summary=\"Tableau de l'entête\" ";
			echo "cellspacing='".$releve_cellspacing."' cellpadding='".$releve_cellpadding."'>\n";
			echo "<tr>\n";
			echo "<td class='releve_empty'>\n";
			echo "&nbsp;\n";
			echo "</td>\n";
			echo "<td style='width:".$releve_addressblock_classe_annee2."%;'>\n";
			echo "<p class='bulletin' align='center'><span class=\"releve_grand\">Classe de ".$tab_rel['eleve'][$i]['classe_nom_complet']."<br />Année scolaire ".$gepiYear."</span><br />\n";

			if ($choix_periode=='intervalle') {
				echo "Relevé de notes du <b>".$tab_rel['intervalle']['debut']."</b> au <b>".$tab_rel['intervalle']['fin']."</b></span>";
			} else {
				echo "<b>".$tab_rel['nom_periode']."</b> : Relevé de notes";
			}

			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			// Pour que le tableau des appréciations ne vienne pas s'encastrer dans les DIV float:
			echo "<div style='clear: both; font-size: xx-small;'>&nbsp;</div>\n";

			// Fin du cadre entête:
			echo "</div>\n";
			echo "<!-- Fin du cadre entête -->\n\n";

		}
		else{
			//-------------------------------
			// Maintenant, on affiche l'en-tête : Les données de l'élève, et l'adresse du lycée.
			// sans bloc adresse responsable
			//-------------------------------

            echo "<div align='center'>\n";
			echo "<table width='$releve_largeurtableau' border='0' cellspacing='".$releve_cellspacing."' cellpadding='".$releve_cellpadding."'";
			echo " summary=\"Tableau de l'entête\"";
			echo ">\n";

			echo "<tr>\n";
			echo "<td style=\"width: 30%;\">\n";
			if ($activer_photo_releve=='y' and $active_module_trombinoscopes=='y') {
				$photo=nom_photo($tab_rel['eleve'][$i]['elenoet']);
				//echo "$photo";
				if("$photo"!=""){
					$photo="../photos/eleves/".$photo;
					if(file_exists($photo)){
						$dimphoto=redimensionne_image_releve($photo);

						echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />'."\n";
					}
				}
			}

				//affichage des données sur une seule ligne ou plusieurs
			if  ($releve_affiche_eleve_une_ligne == 'no') { // sur plusieurs lignes
				echo "<p class='bulletin'>\n";
				echo "<b><span class=\"releve_grand\">".$tab_rel['eleve'][$i]['nom']." ".$tab_rel['eleve'][$i]['prenom']."</span></b><br />";
				echo "Né";
				if (strtoupper($tab_rel['eleve'][$i]['sexe'])== "F") {echo "e";}
				echo "&nbsp;le&nbsp;".$tab_rel['eleve'][$i]['naissance'];
				//Eric Ajout
				echo "<br />";
				if ($tab_rel['eleve'][$i]['regime'] == "d/p") {echo "Demi-pensionnaire";}
				if ($tab_rel['eleve'][$i]['regime'] == "ext.") {echo "Externe";}
				if ($tab_rel['eleve'][$i]['regime'] == "int.") {echo "Interne";}
				if ($tab_rel['eleve'][$i]['regime'] == "i-e"){
					echo "Interne&nbsp;externé";
					if (strtoupper($tab_rel['eleve'][$i]['sexe'])!= "F") {echo "e";}
				}
				//Eric Ajout
				if ($releve_mention_doublant == 'yes'){
					if ($tab_rel['eleve'][$i]['doublant'] == 'R'){
					echo "<br />";
					echo "Redoublant";
					if (strtoupper($tab_rel['eleve'][$i]['sexe'])!= "F") {echo "e";}
					}
				}


				if ($releve_mention_nom_court == 'no') {
					//Eric Ajout et supp
					//echo "<BR />";
					//echo ", $current_classe";
				} else {
					echo "<br />";
					echo $tab_rel['eleve'][$i]['classe'];
				}

			} else { //sur une ligne
				echo "<p class='bulletin'>\n";
				echo "<b><span class=\"releve_grand\">".$tab_rel['eleve'][$i]['nom']." ".$tab_rel['eleve'][$i]['prenom']."</span></b><br />";
				echo "Né";
				if (strtoupper($tab_rel['eleve'][$i]['sexe'])== "F") {echo "e";}
				echo "&nbsp;le&nbsp;".$tab_rel['eleve'][$i]['naissance'];

				if ($tab_rel['eleve'][$i]['regime'] == "d/p") {echo ", Demi-pensionnaire";}
				if ($tab_rel['eleve'][$i]['regime'] == "ext.") {echo ", Externe";}
				if ($tab_rel['eleve'][$i]['regime'] == "int.") {echo ", Interne";}
				if ($tab_rel['eleve'][$i]['regime'] == "i-e"){
					echo ", Interne&nbsp;externé";
					if (strtoupper($tab_rel['eleve'][$i]['sexe'])!= "F") {echo "e";}
				}
				//Eric Ajout
				if ($releve_mention_doublant == 'yes'){
					if ($tab_rel['eleve'][$i]['doublant'] == 'R'){
					echo ", Redoublant";
					if (strtoupper($tab_rel['eleve'][$i]['sexe'])!= "F") {echo "e";}
					}
				}
				if ($releve_mention_nom_court == 'yes') {
					echo ", ".$tab_rel['eleve'][$i]['classe'];
				}
			}

			if($releve_affiche_INE_eleve=="y"){
				echo "<br />\n";
				echo "Numéro INE: ".$tab_rel['eleve'][$i]['no_gep'];
			}

			if($releve_affiche_etab=="y"){
				if ((isset($tab_rel['eleve'][$i]['etab_nom']))&&($tab_rel['eleve'][$i]['etab_nom']!='')) {
					echo "<br />\n";
					if ($tab_rel['eleve'][$i]['etab_id'] != '990') {
						if ($RneEtablissement != $tab_rel['eleve'][$i]['etab_id']) {
							echo "Etablissement d'origine : ";
							echo $tab_rel['eleve'][$i]['etab_niveau_nom']." ".$tab_rel['eleve'][$i]['etab_type']." ".$tab_rel['eleve'][$i]['etab_nom']." (".$tab_rel['eleve'][$i]['etab_cp']." ".$tab_rel['eleve'][$i]['etab_ville'].")\n";
						}
					} else {
						echo "Etablissement d'origine : ";
						echo "hors de France\n";
					}
				}
			}

			echo "</p></td>\n<td style=\"width: 40%;text-align: center;\">\n";

			if ($tab_rel['affiche_adresse'] != "y") {
				echo "<p class='bulletin'><span class=\"releve_grand\">Classe de ".$tab_rel['eleve'][$i]['classe_nom_complet']."<br />Année scolaire ".$gepiYear."</span><br />\n";

				if ($choix_periode=='intervalle') {
					echo "Relevé de notes du <b>".$tab_rel['intervalle']['debut']."</b> au <b>".$tab_rel['intervalle']['fin']."</b>";
				} else {
					echo "<b>".$tab_rel['nom_periode']."</b> : Relevé de notes";
				}
				echo "</p>\n";
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
			if($releve_affich_nom_etab=="y"){
				echo "<span class=\"releve_grand\">".$gepiSchoolName."</span>";
			}
			if($releve_affich_adr_etab=="y"){
				//echo "<span class=\"releve_grand\">".$gepiSchoolName."</span>";
				if($releve_affich_nom_etab=="y"){echo "<br />\n";}
				echo $gepiSchoolAdress1."<br />\n";
				echo $gepiSchoolAdress2."<br />\n";
				echo $gepiSchoolZipCode." ".$gepiSchoolCity;

				if($releve_affiche_tel=="y"){echo "<br />\nTel: ".$gepiSchoolTel;}
				if($releve_affiche_fax=="y"){echo "<br />\nFax: ".$gepiSchoolFax;}
			}
			echo "</p>\n";

			echo "</td>\n</tr>\n</table>\n";
            echo "</div>\n";
			//-------------------------------
			// Fin de l'en-tête
		}


		// On rajoute des lignes vides
		$n = 0;
		while ($n < $releve_ecart_entete) {
			echo "<br />";
			$n++;
		}



        //=============================================

		// Tableau des matieres/devoirs/notes/appréciations

		//include ($fichier_bulletin);

		// On initialise le tableau :

		$larg_tab = $releve_largeurtableau;
		$larg_col1 = $releve_col_matiere_largeur;
		$larg_col2 = $larg_tab - $larg_col1;
		//echo "<table width=\"$larg_tab\" class='boireaus' border=1 cellspacing=3 cellpadding=3>\n";
        echo "<div align='center'>\n";
		echo "<table width=\"$larg_tab\"$releve_class_bordure border='1' cellspacing='3' cellpadding='3'";
		echo "summary=\"Tableau des notes\" ";
		echo ">\n";
		echo "<tr>\n";
		echo "<td width=\"$larg_col1\" class='releve'><b>Matière</b><br /><i>Professeur</i></td>\n";
		echo "<td width=\"$larg_col2\" class='releve'>Notes sur 20</td>\n";
		echo "</tr>\n";

		// Boucle groupes
		$j = 0;
		$prev_cat_id = null;
		while ($j < count($tab_rel['eleve'][$i]['groupe'])) {

			if ($tab_rel['affiche_categories']) {
				// On regarde si on change de catégorie de matière
				if ($tab_rel['eleve'][$i]['groupe'][$j]['id_cat'] != $prev_cat_id) {
					$prev_cat_id = $tab_rel['eleve'][$i]['groupe'][$j]['id_cat'];

					echo "<tr>\n";
					echo "<td colspan='2'>\n\n";
					//echo "<p style='padding: 0; margin:0; font-size: 10px;'>".$tab_rel['categorie'][$prev_cat_id]."</p>\n";
					echo "<p style='padding: 0; margin:0; font-size: ".$releve_categ_font_size."px;";
					if($releve_categ_bgcolor!="") {echo "background-color:$releve_categ_bgcolor;";}
					echo "'>".$tab_rel['categorie'][$prev_cat_id]."</p>\n";


					echo "</td>\n";
					echo "</tr>\n";
				}
			}

			echo "<tr>\n";
			echo "<td class='releve'>\n";
			echo "<b>".htmlentities($tab_rel['eleve'][$i]['groupe'][$j]['matiere_nom_complet'])."</b>";
			$k = 0;
			While ($k < count($tab_rel['eleve'][$i]['groupe'][$j]['prof_login'])) {
				echo "<br /><i>".affiche_utilisateur(htmlentities($tab_rel['eleve'][$i]['groupe'][$j]['prof_login'][$k]),$id_classe)."</i>";
				$k++;
			}
			echo "</td>\n";

			echo "<td class='releve'>\n";

			// Boucle sur la liste des devoirs
			if(!isset($tab_rel['eleve'][$i]['groupe'][$j]['devoir'])) {
				echo "&nbsp;";
			}
			else {
				$m=0;
				$tiret = "no";
				while($m<count($tab_rel['eleve'][$i]['groupe'][$j]['devoir'])) {
					// Note de l'élève sur le devoir:
					$eleve_note=$tab_rel['eleve'][$i]['groupe'][$j]['devoir'][$m]['note'];
					// Statut de l'élève sur le devoir:
					$eleve_statut=$tab_rel['eleve'][$i]['groupe'][$j]['devoir'][$m]['statut'];
					// Appréciation de l'élève sur le devoir:
					$eleve_app=$tab_rel['eleve'][$i]['groupe'][$j]['devoir'][$m]['app'];
					// Le professeur a-t-il autorisé l'accès à l'appréciation lors de la saisie du devoir
					$eleve_display_app=$tab_rel['eleve'][$i]['groupe'][$j]['devoir'][$m]['display_app'];
					// Nom court du devoir:
					$eleve_nom_court=$tab_rel['eleve'][$i]['groupe'][$j]['devoir'][$m]['nom_court'];
					// Date du devoir:
					$eleve_date=$tab_rel['eleve'][$i]['groupe'][$j]['devoir'][$m]['date'];
					// Coef du devoir:
					$eleve_coef=$tab_rel['eleve'][$i]['groupe'][$j]['devoir'][$m]['coef'];

					//==========================================
					// On teste s'il y aura une "Note" à afficher
					if (($eleve_statut != '') and ($eleve_statut != 'v')) {
						$affiche_note = $eleve_statut;
					}
					elseif ($eleve_statut == 'v') {
						$affiche_note = "";
					}
					elseif ($eleve_note != '') {
						$affiche_note = $eleve_note;
					}
					else {
						$affiche_note = "";
					}
					//==========================================

					// Nom du devoir ou pas
					if(($tab_rel['rn_app']=="y") and ($eleve_display_app=="1")) {
						if ($affiche_note=="") {
							if ($tab_rel['rn_nomdev']!="y") {
								$affiche_note = $eleve_nom_court;
							}
							else {
								$affiche_note = "&nbsp;";
							}
						}
					}

					// Si une "Note" doit être affichée
					if ($affiche_note != '') {
						if ($tiret == "yes") {
							if ((($tab_rel['rn_app']=="y") or ($tab_rel['rn_nomdev']=="y"))&&($retour_a_la_ligne=='y')) {
								echo "<br />";
							}
							else {
								echo " - ";
							}
						}
						if($tab_rel['rn_nomdev']=="y"){
							echo "$eleve_nom_court: <b>".$affiche_note."</b>";
						}
						else{
							echo "<b>".$affiche_note."</b>";
						}

						// Coefficient (si on affiche tous les coef...
						// ou si on ne les affiche que s'il y a plusieurs coef différents)
						if(($tab_rel['rn_toutcoefdev']=="y")||
							(($tab_rel['rn_coefdev_si_diff']=="y")&&($tab_rel['eleve'][$i]['groupe'][$j]['differents_coef']=="y"))) {
							echo " (<i><small>".$chaine_coef.$eleve_coef."</small></i>)";
						}

						// Si on a demandé à afficher les appréciations
						// et si le prof a coché l'autorisation d'accès à l'appréciations
						if(($tab_rel['rn_app']=="y") and ($eleve_display_app=="1")) {
							echo " - Appréciation : ";
							if ($eleve_app!="") {
								echo $eleve_app;
							}
							else {
								echo "-";
							}
						}

						if($tab_rel['rn_datedev']=="y"){
							// Format: 2006-09-28 00:00:00
							$tmpdate=explode(" ",$eleve_date);
							$tmpdate=explode("-",$tmpdate[0]);
							echo " (<i><small>$tmpdate[2]/$tmpdate[1]/$tmpdate[0]</small></i>)";
						}
						//====================================================================
						// Après un tour avec affichage dans la boucle:
						$tiret = "yes";
					}

					$m++;
				}
			}
			echo "</td>\n";
			echo "</tr>\n";
			$j++;
		}

		echo "</table>\n";
        //=============================================

		/*
		// Avis du conseil de classe à ramener par là

		if (($releve_affiche_avis == 'y')||($releve_affiche_signature == 'y')) {
			// Tableau de l'avis des conseil de classe
			echo "<table $class_bordure width='$largeurtableau' border='1' cellspacing='".$releve_cellspacing."' cellpadding='".$releve_cellpadding."'>\n";
			echo "<tr>\n";
		}

        if ($releve_affiche_avis == 'y') {
			//
			// Case de gauche : avis des conseils de classe
			//
			echo "<td style='vertical-align: top; text-align: left;'>\n";
			// 1) l'avis
			echo "<span class='bulletin'><i>Avis du conseil de classe:</i></span><br />\n";

			if($tab_rel['avis'][$i]!="") {
				echo "<span class='avis_bulletin'>";
				echo texte_html_ou_pas($tab_rel['avis'][$i]);
				echo "</span>";
				if($releve_affiche_signature == 'y'){
					echo "<br />\n";
				}
			}
			else {
				echo "&nbsp;";
				// Si il n'y a pas d'avis, on rajoute des lignes vides selon les paramètres d'impression
				$n = 0;
				if ($releve_espace_avis >0){
					while ($n < $releve_espace_avis) {
						echo "<br />\n";
						$n++;
					}
				}
			}
		}
        elseif ($releve_affiche_signature == 'y') {
            echo "<td style=\"vertical-align: top;\">";
        }

        if ($releve_affiche_signature == 'y') {
            // 2) Le nom du professeur principal
			if(isset($tab_rel['eleve'][$i]['pp']['login'])) {
				echo "<b>".ucfirst($gepi_prof_suivi)."</b> ";
				echo "<i>".affiche_utilisateur($tab_rel['eleve'][$i]['pp']['login'],$tab_rel['eleve'][$i]['id_classe'])."</i>";
			}

			echo "</td>\n";
			//
			// Case de droite : paraphe du proviseur
			//
			echo "<td style='vertical-align: top; text-align: left;' width='30%'>\n";
			echo "<span class='bulletin'><b>".$tab_rel['formule']."</b>:</span><br />";
			echo "<span class='bulletin'><i>".$tab_rel['suivi_par']."</i></span>";
		}

        // Si une des deux variables 'releve_affiche_avis' ou 'releve_affiche_signature' est à 'y', il faut fermer le tableau
        if (($releve_affiche_avis == 'y')||($releve_affiche_signature == 'y')) {
            echo "</td>\n";
            // Fin du tableau
            echo "</tr>\n";
			echo "</table>\n";
        }
		*/
		//================================


		//================================
		if(($tab_rel['rn_sign_chefetab']=='y')||($tab_rel['rn_sign_pp']=='y')||($tab_rel['rn_sign_resp']=='y')){
			$nb_cases=0;
			if($tab_rel['rn_sign_chefetab']=='y'){
				$nb_cases++;
			}
			if($tab_rel['rn_sign_pp']=='y'){
				$nb_cases++;
			}
			if($tab_rel['rn_sign_resp']=='y'){
				$nb_cases++;
			}
			$largeur_case=round($releve_largeurtableau/$nb_cases);

			echo "<table$releve_class_bordure border='1' width='$releve_largeurtableau'";
			echo " summary=\"Tableau des signatures\"";
			echo ">\n";
			echo "<tr>\n";

			if($tab_rel['rn_sign_chefetab']=='y'){
				echo "<td width='$largeur_case'>\n";
				echo "<b>Signature du chef d'établissement:</b>";
				for($m=0;$m<$tab_rel['rn_sign_nblig'];$m++) {
					echo "<br />\n";
				}
				echo "</td>\n";
			}

			if($tab_rel['rn_sign_pp']=='y'){
				echo "<td width='$largeur_case'>\n";
				echo "<b>Signature du ".$gepi_prof_suivi.":</b>";
				for($m=0;$m<$tab_rel['rn_sign_nblig'];$m++) {
					echo "<br />\n";
				}
				echo "</td>\n";
			}

			if($tab_rel['rn_sign_resp']=='y'){
				echo "<td width='$largeur_case'>\n";
				echo "<b>Signature des responsables:</b>";
				for($m=0;$m<$tab_rel['rn_sign_nblig'];$m++) {
					echo "<br />\n";
				}
				echo "</td>\n";
			}

			echo "</tr>\n";
			echo "</table>\n";
		}

		if($tab_rel['rn_formule']!=""){
			//echo "<p>".htmlentities($tab_rel['rn_formule'])."</p>\n";
			//echo "<p>".$tab_rel['rn_formule']."</p>\n";

			echo "<table width='$releve_largeurtableau' style='margin-left:5px; margin-right:5px;' border='0' cellspacing='".$releve_cellspacing."' cellpadding='".$releve_cellpadding."' summary='Formule du bas de relevé de notes'>\n";
			echo "<tr>";
			echo "<td><p align='center' class='bulletin'>".$tab_rel['rn_formule']."</p></td>\n";
			echo "</tr></table>";

		}
		//================================
        echo "</div>\n";


		//================================
		/*
		// Affichage de la formule de bas de page
		//echo "\$releve_formule_bas=$releve_formule_bas<br />";
		//echo "\$releve_affiche_formule=$releve_affiche_formule<br />";
		if (($releve_formule_bas != '') and ($releve_affiche_formule == 'y')) {
			// Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
			echo "<table width='$releve_largeurtableau' style='margin-left:5px; margin-right:5px;' border='0' cellspacing='".$releve_cellspacing."' cellpadding='".$releve_cellpadding."'>\n";
			echo "<tr>";
			echo "<td><p align='center' class='bulletin'>".$releve_formule_bas."</p></td>\n";
			echo "</tr></table>";
		}
		*/
		//================================


		if(($num_releve==0)&&($nb_releves==2)){
			echo "<p class='saut'>&nbsp;</p>\n";
		}
	}
}

?>
