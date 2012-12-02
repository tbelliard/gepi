<?php
/**
 * calcul de la moyenne générale
 * 
 * 
 * Script à appeler dans le code
 * 
 * calcul des tableaux :
 * - $moy_gen_classe = tableau des moyennes générales de la classe
 * - $moy_gen_eleve  = tableau des moyennes générales d'élèves
 * 
 * le script à besoin de :
 * - $id_classe : la classe concernée
 * - $periode_num : la période concernée
 * 
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL
 * @package Notes
 * @subpackage scripts
 * @see calc_moy_debug()
 * @see get_group()
 */



// Ce parametre n'est pas pris en compte... si on l'augmente, des filtrages ont lieu plus loin hors de ce fichier
$nb_chiffres_moy=1;

//===============
// Ajout J.Etheve
$tab_login_indice = array();
//===============

$current_eleve_login = array();

$moy_min_classe_grp = array();
$current_classe_matiere_moyenne = array();
$moy_max_classe_grp = array();
$place_eleve_grp = array();

$quartile1_classe_gen = 0;
$quartile2_classe_gen = 0;
$quartile3_classe_gen = 0;
$quartile4_classe_gen = 0;
$quartile5_classe_gen = 0;
$quartile6_classe_gen = 0;

$quartile1_grp=array();
$quartile2_grp=array();
$quartile3_grp=array();
$quartile4_grp=array();
$quartile5_grp=array();
$quartile6_grp=array();

$place_eleve_classe=array();

// On appelle la liste des élèves de la classe

$sql="SELECT e.* FROM eleves e, j_eleves_classes c
	WHERE (
	e.login = c.login and
	c.id_classe = '".$id_classe."' and
	c.periode='".$periode_num."'
	)
	ORDER BY e.nom, e.prenom";
$appel_liste_eleves = mysql_query($sql);
calc_moy_debug($sql."\n");
//echo "$sql<br />";
$nombre_eleves = mysql_num_rows($appel_liste_eleves);
calc_moy_debug("\$nombre_eleves=$nombre_eleves\n");


// On appelle la liste des matières de la classe
if ($affiche_categories) {
	calc_moy_debug("\$affiche_categories=$affiche_categories\n");
	// On utilise les valeurs spécifiées pour la classe en question

	$sql="SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.categorie_id, jgc.mode_moy ".
	"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m, matieres_categories mc " .
	"WHERE ( " .
	"mc.id=jmcc.categorie_id AND ".
	"jgc.categorie_id = jmcc.categorie_id AND " .
	"jgc.id_classe=jmcc.classe_id AND " .
	"jgc.id_classe='".$id_classe."' AND " .
	"jgm.id_groupe=jgc.id_groupe AND " .
	"m.matiere = jgm.id_matiere " .
	"AND jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n')".
	") " .
	"ORDER BY jmcc.priority,mc.priority,jgc.priorite,m.nom_complet";
	calc_moy_debug($sql."\n");
	$appel_liste_groupes = mysql_query($sql);
} else {
	calc_moy_debug("\$affiche_categories=\n");
	$sql="SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.mode_moy
	FROM j_groupes_classes jgc, j_groupes_matieres jgm
	WHERE (
	jgc.id_classe='".$id_classe."' AND
	jgm.id_groupe=jgc.id_groupe
	AND jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n')
	)
	ORDER BY jgc.priorite,jgm.id_matiere";
	calc_moy_debug($sql."\n");
	$appel_liste_groupes = mysql_query($sql);
}

//echo "$sql<br />";

$nombre_groupes = mysql_num_rows($appel_liste_groupes);
calc_moy_debug("\$nombre_groupes=$nombre_groupes\n");

// Initialisation des tableaux liés aux calculs des moyennes générales
$current_group = array();
$current_coef = array();
$current_mode_moy = array();

//======================================
// Ajout: boireaus 20080408
$current_coef_eleve=array();
//======================================

$moy_gen_classe = array();
$moy_gen_eleve = array();
$moy_gen_eleve1 = array();
//===============
$moy_cat_eleve = array();
$moy_cat_classe = array();
$moy_cat_min = array();
$moy_cat_max = array();
$total_coef_classe = array();
$total_coef_eleve = array();
$tot_points_eleve = array();
$moy_gen_classe1=array();
$total_coef_classe1=array();
$total_coef_eleve1=array();
$tot_points_eleve1=array();
$current_coef_eleve1=array();
$total_coef_cat_classe = array();
$total_coef_cat_eleve = array();

$i=0;
$get_cat = mysql_query("SELECT id,nom_complet FROM matieres_categories");
$categories = array();
$tab_noms_categories = array();
$tab_id_categories = array();
while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
	$categories[] = $row["id"];
	$tab_noms_categories[$row["id"]]=$row["nom_complet"];
	$tab_id_categories[$row["nom_complet"]]=$row["id"];
}

while ($i < $nombre_eleves) {
	$total_coef_classe[$i] = 0;
	$total_coef_eleve[$i] = 0;
	$current_coef_eleve[$i]=array();
	$moy_gen_eleve[$i] = 0;
	$tot_points_eleve[$i] = 0;
	$moy_gen_eleve1[$i] = 0;
	$total_coef_classe1[$i] = 0;
	$total_coef_eleve1[$i] = 0;
	$tot_points_eleve1[$i] = 0;
	$moy_gen_classe1[$i] = 0;
	$moy_gen_classe[$i] = 0;
	$moy_cat_classe[$i] = array();
	$moy_cat_min[$i] = array();
	$moy_cat_max[$i] = array();
	$moy_cat_eleve[$i] = array();

	$total_coef_cat_classe[$i] = array();
	$total_coef_cat_eleve[$i] = array();
	foreach($categories as $cat_id) {
		$moy_cat_eleve[$i][$cat_id] = 0;
		$total_coef_cat_classe[$i][$cat_id] = 0;
		$total_coef_cat_eleve[$i][$cat_id] = 0;
		$moy_cat_classe[$i][$cat_id] = 0;

		$moy_cat_min[$i][$cat_id] = "-";
		$moy_cat_max[$i][$cat_id] = "-";
	}

	// Pour le cas où une matière est saisie en "Aucune" catégorie dans
	// Gestion des bases/Gestion des classes/<classe> Enseignements
	// La catégorie "Aucune" n'existe pas dans 'matieres_categories'
	$moy_cat_eleve[$i][0] = 0;
	$total_coef_cat_classe[$i][0] = 0;
	$total_coef_cat_eleve[$i][0] = 0;
	$moy_cat_classe[$i][0] = 0;

	$moy_cat_min[$i][0] = "-";
	$moy_cat_max[$i][0] = "-";
	//=================================

	// Temoin que la moyenne générale de l'élève peut avoir une signification
	$temoin_au_moins_une_matiere_avec_note[$i]="n";

	$i++;
}

// Pour débugger:
$lignes_debug="";
$ele_login_debug="rom.abela";

// Témoin destiné à tester si tous les coefficients sont à 1
// S'ils le sont, on n'imprime pas deux lignes de moyenne générale (moy.gen.coefficientée d'après Gestion des classes/<Classe> Enseignements et moy.gen avec coef à 1) même si la case est cochée dans le modèle PDF.
$temoin_tous_coef_a_1="y";

// Préparation des données
$j=0;
$prev_cat = null;
while ($j < $nombre_groupes) {
	$group_id = mysql_result($appel_liste_groupes, $j, "id_groupe");
	$current_group[$j] = get_group($group_id);

	calc_moy_debug("\$current_group[$j]['name']=".$current_group[$j]['name']."\n");
	calc_moy_debug("\$current_group[$j]['matiere']['matiere']=".$current_group[$j]['matiere']['matiere']."\n");
	// DEBUG
	//echo "\$current_group[$j]['name']=".$current_group[$j]['name']."<br />";

	$current_coef[$j] = mysql_result($appel_liste_groupes, $j, "coef");
	calc_moy_debug("\$current_coef[$j]=mysql_result(\$appel_liste_groupes, $j, \"coef\")=$current_coef[$j]\n");
	if($current_coef[$j]!=1) {$temoin_tous_coef_a_1="n";}

	if(isset($coefficients_a_1)){
		if($coefficients_a_1=="oui"){
			$current_coef[$j]=1;
		}
	}

	if((isset($utiliser_coef_perso))&&($utiliser_coef_perso=='y')) {
		if(isset($coef_perso[$current_group[$j]["id"]])) {
			$current_coef[$j]=$coef_perso[$current_group[$j]["id"]];
		}
	}

	//===============
	// Ajout J.Etheve
	$current_coef1[$j]=1;
	//===============
	calc_moy_debug("\$current_coef[$j]=$current_coef[$j]\n");

	$current_mode_moy[$j]=mysql_result($appel_liste_groupes, $j, "mode_moy");
	calc_moy_debug("\$current_mode_moy[$j]=mysql_result(\$appel_liste_groupes, $j, \"mode_moy\")=$current_mode_moy[$j]\n");

	if((isset($utiliser_coef_perso))&&($utiliser_coef_perso=='y')) {
		if(isset($mode_moy_perso[$current_group[$j]["id"]])) {
			$current_mode_moy[$j]=$mode_moy_perso[$current_group[$j]["id"]];
		}
	}

	if ($current_group[$j]["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat) {
		$prev_cat = $current_group[$j]["classes"]["classes"][$id_classe]["categorie_id"];
	}

	// Moyenne de la classe dans la matière $current_matiere[$j]
	$sql="SELECT round(avg(note),$nb_chiffres_moy) moyenne
		FROM matieres_notes
		WHERE (
		statut ='' AND
		id_groupe='".$current_group[$j]["id"]."' AND
		periode='$periode_num'
		)
		";
	calc_moy_debug("$sql\n");
	$current_classe_matiere_moyenne_query = mysql_query($sql);

	$current_classe_matiere_moyenne[$j] = mysql_result($current_classe_matiere_moyenne_query, 0, "moyenne");
	calc_moy_debug("\$current_classe_matiere_moyenne[$j]=$current_classe_matiere_moyenne[$j]\n");

	//===================================
	// Effectif du groupe pour le rang:
	$sql="SELECT 1=1
		FROM matieres_notes
		WHERE (
		statut ='' AND
		id_groupe='".$current_group[$j]["id"]."' AND
		periode='$periode_num'
		)
		";
	calc_moy_debug("$sql\n");
	$req_current_group_effectif_avec_note = mysql_query($sql);
	$current_group_effectif_avec_note[$j] = mysql_num_rows($req_current_group_effectif_avec_note);
	//===================================

	// Calcul de la moyenne des élèves et de la moyenne de la classe pour l'enseignement courant ($j)
	$i=0;

	$sql="SELECT MIN(note) note_min, MAX(note) note_max FROM matieres_notes
		WHERE (
		periode='$periode_num' AND
		id_groupe='".$current_group[$j]["id"]."' AND
		statut=''
		)";
	$res_note_min_max=mysql_query($sql);
	$moy_min_classe_grp[$j]= @mysql_result($res_note_min_max, 0, "note_min");
	$moy_max_classe_grp[$j]= @mysql_result($res_note_min_max, 0, "note_max");
	//======================================

	$sql="SELECT COUNT(note) as quartile1 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note>=15)";
	//echo "$sql<br />";
	$quartile1_grp[$j]=sql_query1($sql);
	$sql="SELECT COUNT(note) as quartile2 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note>=12 AND
								note<15)";
	//echo "$sql<br />";
	$quartile2_grp[$j]=sql_query1($sql);
	$sql="SELECT COUNT(note) as quartile3 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note>=10 AND
								note<12)";
	//echo "$sql<br />";
	$quartile3_grp[$j]=sql_query1($sql);
	$sql="SELECT COUNT(note) as quartile4 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note>=8 AND
								note<10)";
	//echo "$sql<br />";
	$quartile4_grp[$j]=sql_query1($sql);
	$sql="SELECT COUNT(note) as quartile5 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note>=5 AND
								note<8)";
	//echo "$sql<br />";
	$quartile5_grp[$j]=sql_query1($sql);
	$sql="SELECT COUNT(note) as quartile6 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note<5)";
	//echo "$sql<br />";
	$quartile6_grp[$j]=sql_query1($sql);

	if ((isset($affiche_rang))&&($affiche_rang=='y')) {
		$current_eleve_rang[$j]=array();
	}

	//======================================
	while ($i < $nombre_eleves) {
		$current_eleve_login[$i] = mysql_result($appel_liste_eleves, $i, "login");
		//===============
		// Ajout J.Etheve
		$tab_login_indice[my_strtoupper($current_eleve_login[$i])]=$i;
		//===============

		if($current_eleve_login[$i]==$ele_login_debug) {
			$lignes_debug.="<p>\$tab_login_indice[\$current_eleve_login[$i]]=\$tab_login_indice[".$current_eleve_login[$i]."]=$i<br />";
			$lignes_debug.="<p>\$current_group[$j]['name']=".$current_group[$j]['name']."<br />";
		}

		// Maintenant on regarde si l'élève suit bien cette matière ou pas
		if (in_array($current_eleve_login[$i], $current_group[$j]["eleves"][$periode_num]["list"])) {
			calc_moy_debug("\$current_group[$j]['name']=".$current_group[$j]['name']."\n");

			//=====================================
			// Récupération de la note et du statut
			$sql="SELECT distinct * FROM matieres_notes
			WHERE (
			login='".$current_eleve_login[$i]."' AND
			periode='$periode_num' AND
			id_groupe='".$current_group[$j]["id"]."'
			)";
			calc_moy_debug("$sql\n");
			$current_eleve_note_query = mysql_query($sql);

			if(mysql_num_rows($current_eleve_note_query)>0) {
				$lig_tmp=mysql_fetch_object($current_eleve_note_query);
				$current_eleve_note[$j][$i]=$lig_tmp->note;
				calc_moy_debug("\$current_eleve_note[$j][$i]=".$current_eleve_note[$j][$i]."\n");

				$current_eleve_statut[$j][$i]=$lig_tmp->statut;
				calc_moy_debug("\$current_eleve_statut[$j][$i]=".$current_eleve_statut[$j][$i]."\n");

			}
			else {
				$current_eleve_note[$j][$i]="-";
				$current_eleve_statut[$j][$i]="";
			}

			if($current_eleve_login[$i]==$ele_login_debug) {
				$lignes_debug.="\$current_eleve_note[$j][$i]=".$current_eleve_note[$j][$i]."<br />";
				$lignes_debug.="\$current_eleve_statut[$j][$i]=".$current_eleve_statut[$j][$i]."<br />";
			}
			//=====================================
			// DEBUG
			//echo "\$current_eleve_login[$i]=$current_eleve_login[$i]<br />";

			//=====================================
			// On teste si l'élève a un coef spécifique pour cette matière
			calc_moy_debug("\$coefficients_a_1=$coefficients_a_1\n");
			if((isset($coefficients_a_1))&&($coefficients_a_1=="oui")) {
				$coef_eleve=1;
			}
			else{
				$sql="SELECT value FROM eleves_groupes_settings WHERE (" .
						"login = '".$current_eleve_login[$i]."' AND " .
						"id_groupe = '".$current_group[$j]["id"]."' AND " .
						"name = 'coef')";
				//echo "$sql<br />";
				calc_moy_debug("$sql\n");
				$test_coef = mysql_query($sql);
				if (mysql_num_rows($test_coef) > 0) {
					$coef_eleve = mysql_result($test_coef, 0);
				} else {
					$coef_eleve = $current_coef[$j];
				}

				// On refait ce test pour dans le cas des coef_perso autres que ceux de eleves_groupes_settings forcer les coef choisis dans prepa_conseil/index2bis.php
				if(isset($utiliser_coef_perso)) {
					if(isset($coef_perso[$current_group[$j]["id"]])) {
						$coef_eleve=$coef_perso[$current_group[$j]["id"]];
					}
				}
			}
			//===============
			// Ajout d'après J.Etheve
			$coef_eleve1=1;
			//===============
			// Réserve pour les moyennes de catégorie plus bas:
			$coef_eleve_reserve=$coef_eleve;
			// Pour les catégories, on ne tient pas compte des mode_moy
			//===============

			if($current_mode_moy[$j]=='sup10') {
				// Si la matière est une matière à "bonus" (seules les notes supérieures à 10 comptent), on passe le coef à zéro si la note n'est pas numérique ou si elle est inférieure à 10.
				if(($current_eleve_note[$j][$i]!="")&&($current_eleve_note[$j][$i]!="-")&&($current_eleve_note[$j][$i]<10)) {
					$coef_eleve=0;
				}
			}

			$current_coef_eleve[$i][$j]=$coef_eleve;
			//===============
			// Ajout J.Etheve
			$current_coef_eleve1[$i][$j]=$coef_eleve1;
			//===============
			if($current_eleve_login[$i]==$ele_login_debug) {$lignes_debug.="\$current_coef_eleve[$i][$j]=".$current_coef_eleve[$i][$j]."<br />";}
			//=====================================

			//=====================================
			if ((isset($affiche_rang))&&($affiche_rang=='y')) {
				$current_eleve_rang[$j][$i]=@mysql_result($current_eleve_note_query, 0, "rang");
				if(($current_eleve_rang[$j][$i]==0)||($current_eleve_rang[$j][$i]=="-1")) {$current_eleve_rang[$j][$i]="-";}
			}
			//=====================================

			//=====================================
			if (($current_eleve_note[$j][$i] != '') and ($current_eleve_statut[$j][$i] == '')) {
				if($current_eleve_note[$j][$i]>=15) {
					$place_eleve_grp[$j][$i]=1;
				}
				elseif(($current_eleve_note[$j][$i]>=12)&&($current_eleve_note[$j][$i]<15)) {
					$place_eleve_grp[$j][$i]=2;
				}
				elseif(($current_eleve_note[$j][$i]>=10)&&($current_eleve_note[$j][$i]<12)) {
					$place_eleve_grp[$j][$i]=3;
				}
				elseif(($current_eleve_note[$j][$i]>=8)&&($current_eleve_note[$j][$i]<12)) {
					$place_eleve_grp[$j][$i]=4;
				}
				elseif(($current_eleve_note[$j][$i]>=5)&&($current_eleve_note[$j][$i]<8)) {
					$place_eleve_grp[$j][$i]=5;
				}
				elseif($current_eleve_note[$j][$i]<5) {
					$place_eleve_grp[$j][$i]=6;
				}
			}
			//=====================================

			calc_moy_debug("\$coef_eleve=$coef_eleve\n");
			calc_moy_debug("\$current_eleve_note[$j][$i]=".$current_eleve_note[$j][$i]."\n");
			calc_moy_debug("\$current_eleve_statut[$j][$i]=".$current_eleve_statut[$j][$i]."\n");
			// DEBUG

			//=====================================
			if ($coef_eleve_reserve!=0) {
				//if (($current_eleve_note[$j][$i] != '') and ($current_eleve_statut[$j][$i] == '')) {
				if (($current_eleve_note[$j][$i] != '') and ($current_eleve_note[$j][$i] != '-') and ($current_eleve_statut[$j][$i] == '')) {

					// Temoin que la moyenne générale de l'élève peut avoir une signification
					if($coef_eleve!=0) {$temoin_au_moins_une_matiere_avec_note[$i]="y";}

					if($current_mode_moy[$j]=='sup10') {
						// La note compte si elle est supérieure à 10
						// Une telle note peut faire baisser la moyenne si la moyenne est supérieure à la note courante à comptabiliser
						// Si coef_eleve>0, c'est que la note est supérieure à 10

						$total_coef_eleve[$i] += $coef_eleve;
						$moy_gen_eleve[$i] += $coef_eleve*$current_eleve_note[$j][$i];

					}
					elseif($current_mode_moy[$j]=='bonus') {
						// Mode bac
						// Les points au-dessus de 10 sont coefficientés et ajoutés sans augmenter le total des coefs
						if(($current_eleve_note[$j][$i]!="")&&($current_eleve_note[$j][$i]!="-")&&($current_eleve_note[$j][$i]>10)) {
							$moy_gen_eleve[$i] += $coef_eleve*($current_eleve_note[$j][$i]-10);
						}

						// On n'augmente pas le total des coef pour la moyenne générale
					}
					elseif($current_mode_moy[$j]=='ameliore') {
						// Non traité pour le moment

						//*********
						// A FAIRE
						//*********

						// Stocker ces notes dans un tableau temporaire et parcourir après le calcul de la moyenne générale de l'élève pour voir si cela améliore la moyenne générale
					}
					else {
						// Mode classique

						$total_coef_eleve[$i] += $coef_eleve;
						calc_moy_debug("\$total_coef_eleve[$i]=$total_coef_eleve[$i]\n");
	
						$moy_gen_eleve[$i] += $coef_eleve*$current_eleve_note[$j][$i];
						calc_moy_debug("\$moy_gen_eleve[$i]=$moy_gen_eleve[$i]\n");

					}

					// Faut-il ne pas compter à bonus quand on force les coef à 1? Oui
					$total_coef_eleve1[$i] += $coef_eleve1;
					// La note compte normalement pour le mode avec coef forcés à 1:	
					$moy_gen_eleve1[$i] += $coef_eleve1*$current_eleve_note[$j][$i];

					// On fait en sorte que les coef comptent au niveau des catégories: on ne prend pas en compte les mode_moy
					// On prend le coef pour la moyenne de catégorie
					$total_coef_cat_eleve[$i][$prev_cat] += $coef_eleve_reserve;
					calc_moy_debug("\$total_coef_cat_eleve[$i][$prev_cat]=".$total_coef_cat_eleve[$i][$prev_cat]."\n");
					$moy_cat_eleve[$i][$prev_cat] += $coef_eleve_reserve*$current_eleve_note[$j][$i];
					calc_moy_debug("\$moy_cat_eleve[$i][$prev_cat]=".$moy_cat_eleve[$i][$prev_cat]."\n");


					if($current_eleve_login[$i]==$ele_login_debug) {
						$lignes_debug.="\$current_mode_moy[$j]=".$current_mode_moy[$j]."<br />";
						$lignes_debug.="\$total_coef_cat_eleve[$i][$prev_cat]=".$total_coef_cat_eleve[$i][$prev_cat]."<br />";
						$lignes_debug.="\$moy_gen_eleve[$i]=".$moy_gen_eleve[$i]."<br />";
						$lignes_debug.="\$total_coef_eleve[$i]=".$total_coef_eleve[$i]."<br />";
						$lignes_debug.="\$moy_cat_eleve[$i][$prev_cat]=".$moy_cat_eleve[$i][$prev_cat]."<br />";
						$lignes_debug.="\$total_coef_cat_eleve[$i][$prev_cat]=".$total_coef_cat_eleve[$i][$prev_cat]."<br />";
					}

				}
			}
		}
		$i++;
		calc_moy_debug("==============================\n");
	}
	$j++;
}

$lignes_debug.="<p>";

$i = 0;
while ($i < $nombre_eleves) {
	if ($total_coef_eleve[$i] != 0) {
		$place_eleve_classe[$i] = "";
		if($temoin_au_moins_une_matiere_avec_note[$i]=="y") {
			$tot_points_eleve[$i]=$moy_gen_eleve[$i];
			$moy_gen_eleve[$i] = $moy_gen_eleve[$i]/$total_coef_eleve[$i];

			if($current_eleve_login[$i]==$ele_login_debug) {
				$lignes_debug.="\$moy_gen_eleve[$i]=".$moy_gen_eleve[$i]."/".$total_coef_eleve[$i]."=".$moy_gen_eleve[$i]."<br />";
			}

			if ($total_coef_eleve1[$i] != 0) {
				$tot_points_eleve1[$i]=$moy_gen_eleve1[$i];
				$moy_gen_eleve1[$i] = $moy_gen_eleve1[$i]/$total_coef_eleve1[$i];
			}

			if($current_eleve_login[$i]==$ele_login_debug) {
				$lignes_debug.="\$moy_gen_eleve[$i]=".$moy_gen_eleve[$i]."/".$total_coef_eleve[$i]."<br />";
			}
		}
		else {
			$moy_gen_eleve[$i]="-";
			$moy_gen_eleve1[$i]="-";
		}
		calc_moy_debug("\$moy_gen_eleve[$i]=$moy_gen_eleve[$i]\n");

		if($current_eleve_login[$i]==$ele_login_debug) {
			$lignes_debug.="\$moy_gen_eleve[$i]=".$moy_gen_eleve[$i]."<br />";
		}

		// Préparation des données pour affichage des graphiques
		if ($affiche_graph == 'y')  {
			if ($moy_gen_eleve[$i] >= 15) {$quartile1_classe_gen++; $place_eleve_classe[$i] = 1;}
			else if (($moy_gen_eleve[$i] >= 12) and ($moy_gen_eleve[$i] < 15)) {$quartile2_classe_gen++;$place_eleve_classe[$i] = 2;}
			else if (($moy_gen_eleve[$i] >= 10) and ($moy_gen_eleve[$i] < 12)) {$quartile3_classe_gen++;$place_eleve_classe[$i] = 3;}
			else if (($moy_gen_eleve[$i] >= 8) and ($moy_gen_eleve[$i] < 10)) {$quartile4_classe_gen++;$place_eleve_classe[$i] = 4;}
			else if (($moy_gen_eleve[$i] >= 5) and ($moy_gen_eleve[$i] < 8)) {$quartile5_classe_gen++;$place_eleve_classe[$i] = 5;}
			else {$quartile6_classe_gen++;$place_eleve_classe[$i] = 6;}
		}

	} else {
		$moy_gen_eleve[$i] = "-";
		$moy_gen_eleve1[$i] = "-";
	}

	foreach($categories as $cat) {
		
		if ($total_coef_cat_eleve[$i][$cat] != 0) {
			if($current_eleve_login[$i]==$ele_login_debug) {
				$lignes_debug.="\$moy_cat_eleve[$i][$cat]=".$moy_cat_eleve[$i][$cat]."/".$total_coef_cat_eleve[$i][$cat]."<br />";
			}

			$moy_cat_eleve[$i][$cat] = $moy_cat_eleve[$i][$cat]/$total_coef_cat_eleve[$i][$cat];
			calc_moy_debug("\$moy_cat_eleve[$i][$cat]=".$moy_cat_eleve[$i][$cat]."\n");
		} else {
			$moy_cat_eleve[$i][$cat] = "-";
		}

		if((isset($current_eleve_login[$i]))&&($current_eleve_login[$i]==$ele_login_debug)) {
			$lignes_debug.="\$moy_cat_eleve[$i][$cat]=".$moy_cat_eleve[$i][$cat]."<br />";
		}
	}
	$i++;
	calc_moy_debug("==============================\n");
}


// Recherche des moyennes min/max/classe de catégories
foreach($categories as $cat) {
	$moy_min_categorie[$cat]=1000;
	$moy_max_categorie[$cat]=-1;

	$moy_classe_categorie[$cat]=0;
	$tmp_eff=0;

	$i = 0;
	while ($i < $nombre_eleves) {
		if($moy_cat_eleve[$i][$cat]!="-") {
			if($moy_cat_eleve[$i][$cat]<$moy_min_categorie[$cat]) {
				$moy_min_categorie[$cat]=$moy_cat_eleve[$i][$cat];
			}
			if($moy_cat_eleve[$i][$cat]>$moy_max_categorie[$cat]) {
				$moy_max_categorie[$cat]=$moy_cat_eleve[$i][$cat];
			}
			$moy_classe_categorie[$cat]+=$moy_cat_eleve[$i][$cat];

			// On formate avec virgule la moyenne de catégorie pour l'élève
			// *** A FAIRE *** Il faudrait prendre en compte le nombre de décimales demandées sur le modèle
			
			// On incrémente le nombre d'élèves qui ont une moyenne sur la catégorie
			$tmp_eff++;
		}

		$i++;
	}

	if($moy_min_categorie[$cat]==1000) {
		$moy_min_categorie[$cat]="-";
	}

	if($moy_max_categorie[$cat]==-1) {
		$moy_max_categorie[$cat]="-";
	}

	if($tmp_eff>0) {
		$lignes_debug.="\$moy_classe_categorie[$cat]=$moy_classe_categorie[$cat]/$tmp_eff=";
		$moy_classe_categorie[$cat]=$moy_classe_categorie[$cat]/$tmp_eff;
		$lignes_debug.="$moy_classe_categorie[$cat]<br />";
	}
	else {
		$moy_classe_categorie[$cat]="-";
		$lignes_debug.="\$moy_classe_categorie[$cat]=$moy_classe_categorie[$cat]<br />";
	}

	$lignes_debug.="\$moy_max_categorie[$cat]=$moy_max_categorie[$cat]<br />";
	$lignes_debug.="\$moy_min_categorie[$cat]=$moy_min_categorie[$cat]<br />";

	// Pour chaque élève, on met les mêmes moyennes min/classe/max
	// de catégorie parce que sinon, on pourrait arriver à l'aberration suivante:
	// Un élève, seul de la classe à avoir une combinaison d'options, aurait
	// sa moyenne de catégorie qui serait également min, max et classe
	$i = 0;
	while ($i < $nombre_eleves) {
		$moy_cat_min[$i][$cat]=$moy_min_categorie[$cat];
		$moy_cat_max[$i][$cat]=$moy_max_categorie[$cat];
		$moy_cat_classe[$i][$cat]=$moy_classe_categorie[$cat];

		$i++;
	}
}

// DEBUG:
//echo $lignes_debug;

$moy_min_classe=21;
for ( $i=0 ; $i < sizeof($moy_gen_eleve) ; $i++ ) {
	if($moy_gen_eleve[$i]!="-"){
		if($moy_gen_eleve[$i]<$moy_min_classe){
			$moy_min_classe=$moy_gen_eleve[$i];
		}
	}
}
if($moy_min_classe==21) {
	$moy_min_classe="-";
}

$moy_min_classe1=21;
for ( $i=0 ; $i < sizeof($moy_gen_eleve1) ; $i++ ) {
	if($moy_gen_eleve1[$i]!="-"){
		if($moy_gen_eleve1[$i]<$moy_min_classe1){
			$moy_min_classe1=$moy_gen_eleve1[$i];
		}
	}
}
if($moy_min_classe1==21) {
	$moy_min_classe1="-";
}

if(count($moy_gen_eleve)>0) {
	$moy_max_classe = max($moy_gen_eleve);
}
if(count($moy_gen_eleve1)>0) {
	$moy_max_classe1 = max($moy_gen_eleve1);
}

//Calcul de la moyenne générale de la classe
$nb_elv_classe=sizeof($moy_gen_eleve);
$moy_generale_classe = 0;
$effectif_avec_moyenne=0;
for ( $i=0 ; $i < $nb_elv_classe ; $i++ ) {
	$moy_generale_classe += $moy_gen_eleve[$i];
	if($temoin_au_moins_une_matiere_avec_note[$i]=='y') {$effectif_avec_moyenne++;}
}
if($effectif_avec_moyenne!=0) {
	$moy_generale_classe=$moy_generale_classe/$effectif_avec_moyenne;
}
else {
	$moy_generale_classe="-";
}

$nb_elv_classe=sizeof($moy_gen_eleve);
$moy_generale_classe1 = 0;
$effectif_avec_moyenne1=0;
for ( $i=0 ; $i < $nb_elv_classe ; $i++ ) {
	$moy_generale_classe1 += $moy_gen_eleve1[$i];
	if($temoin_au_moins_une_matiere_avec_note[$i]=='y') {$effectif_avec_moyenne1++;}
}
if($effectif_avec_moyenne1!=0) {
	$moy_generale_classe1=$moy_generale_classe1/$effectif_avec_moyenne1;
}
else {
	$moy_generale_classe1="-";
}

for ( $i=0 ; $i < $nb_elv_classe ; $i++ ) {
	$moy_gen_classe[$i]=$moy_generale_classe;
	$moy_gen_classe1[$i]=$moy_generale_classe1;
}


?>
