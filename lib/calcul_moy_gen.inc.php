<?php
/*
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * Portion de code qui calcule des tableaux suivants
     $moy_gen_classe =  tableau des moyennes générales de la classe
     $moy_gen_eleve =  tableau des moyennes générales d'élèves
   le script à besoin de :
     - $id_classe : la classe concernée
     - $periode_num : la période concernée
*/

/*
function calc_moy_debug($texte){
	// Passer à 1 la variable pour générer un fichier de debug...
	$debug=0;
	if($debug==1){
		$fich=fopen("/tmp/calc_moy_debug.txt","a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}
*/

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

// On appelle la liste des élèves de la classe
/*
$appel_liste_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c
    WHERE (
    e.login = c.login and
    c.id_classe = '".$id_classe."' and
    c.periode='".$periode_num."'
    )
    ORDER BY e.nom, e.prenom");
*/
$sql="SELECT e.* FROM eleves e, j_eleves_classes c
    WHERE (
    e.login = c.login and
    c.id_classe = '".$id_classe."' and
    c.periode='".$periode_num."'
    )
    ORDER BY e.nom, e.prenom";
$appel_liste_eleves = mysql_query($sql);
calc_moy_debug($sql."\n");
$nombre_eleves = mysql_num_rows($appel_liste_eleves);
calc_moy_debug("\$nombre_eleves=$nombre_eleves\n");


// On appelle la liste des matières de la classe
if ($affiche_categories) {
		// On utilise les valeurs spécifiées pour la classe en question
		/*
		$appel_liste_groupes = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.categorie_id ".
		"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
		"WHERE ( " .
		"jgc.categorie_id = jmcc.categorie_id AND " .
		"jgc.id_classe='".$id_classe."' AND " .
		"jgm.id_groupe=jgc.id_groupe AND " .
		"m.matiere = jgm.id_matiere" .
		") " .
		"ORDER BY jmcc.priority,jgc.priorite,m.nom_complet");
		*/
		$sql="SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.categorie_id ".
		"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
		"WHERE ( " .
		"jgc.categorie_id = jmcc.categorie_id AND " .
		"jgc.id_classe='".$id_classe."' AND " .
		"jgm.id_groupe=jgc.id_groupe AND " .
		"m.matiere = jgm.id_matiere" .
		") " .
		"ORDER BY jmcc.priority,jgc.priorite,m.nom_complet";
		calc_moy_debug($sql."\n");
		$appel_liste_groupes = mysql_query($sql);
} else {
	/*
	$appel_liste_groupes = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef
	FROM j_groupes_classes jgc, j_groupes_matieres jgm
	WHERE (
	jgc.id_classe='".$id_classe."' AND
	jgm.id_groupe=jgc.id_groupe
	)
	ORDER BY jgc.priorite,jgm.id_matiere");
	*/
	$sql="SELECT DISTINCT jgc.id_groupe, jgc.coef
	FROM j_groupes_classes jgc, j_groupes_matieres jgm
	WHERE (
	jgc.id_classe='".$id_classe."' AND
	jgm.id_groupe=jgc.id_groupe
	)
	ORDER BY jgc.priorite,jgm.id_matiere";
	calc_moy_debug($sql."\n");
	$appel_liste_groupes = mysql_query($sql);
}

$nombre_groupes = mysql_num_rows($appel_liste_groupes);
calc_moy_debug("\$nombre_groupes=$nombre_groupes\n");

// Initialisation des tableaux liés aux calculs des moyennes générales
$current_group = array();
$current_coef = array();

//======================================
// Ajout: boireaus 20080408
$current_coef_eleve=array();
//======================================

$moy_gen_classe = array();
$moy_gen_eleve = array();
$moy_cat_eleve = array();
$moy_cat_classe = array();
//$total_coef = array();
$total_coef_classe = array();
$total_coef_eleve = array();

//$total_coef_cat = array();
$total_coef_cat_classe = array();
$total_coef_cat_eleve = array();

$i=0;
$get_cat = mysql_query("SELECT id FROM matieres_categories");
$categories = array();
while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
  	$categories[] = $row["id"];
}

while ($i < $nombre_eleves) {
    //$total_coef[$i] = 0;
    $total_coef_classe[$i] = 0;
    $total_coef_eleve[$i] = 0;

	//======================================
	// Ajout: boireaus 20080408
	$current_coef_eleve[$i]=array();
	//======================================

    $moy_gen_eleve[$i] = 0;
    $moy_gen_classe[$i] = 0;
    $moy_cat_classe[$i] = array();
    $moy_cat_eleve[$i] = array();

    //$total_coef_cat[$i] = array();
    $total_coef_cat_classe[$i] = array();
    $total_coef_cat_eleve[$i] = array();
	foreach($categories as $cat_id) {
    	$moy_cat_eleve[$i][$cat_id] = 0;
    	//$total_coef_cat[$i][$cat_id] = 0;
    	$total_coef_cat_classe[$i][$cat_id] = 0;
    	$total_coef_cat_eleve[$i][$cat_id] = 0;
    	$moy_cat_classe[$i][$cat_id] = 0;
    }
    $i++;
}


// Préparation des données
$j=0;
$prev_cat = null;
while ($j < $nombre_groupes) {
    $group_id = mysql_result($appel_liste_groupes, $j, "id_groupe");
    $current_group[$j] = get_group($group_id);
	$current_coef[$j] = mysql_result($appel_liste_groupes, $j, "coef");
	calc_moy_debug("\$current_coef[$j]=mysql_result(\$appel_liste_groupes, $j, \"coef\")=$current_coef[$j]\n");

	if(isset($coefficients_a_1)){
		if($coefficients_a_1=="oui"){
			$current_coef[$j]=1;
		}
	}
	calc_moy_debug("\$current_coef[$j]=$current_coef[$j]\n");

    if ($current_group[$j]["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat) {
    	$prev_cat = $current_group[$j]["classes"]["classes"][$id_classe]["categorie_id"];
    }
    // Moyenne de la classe dans la matière $current_matiere[$j]
    /*
	$current_classe_matiere_moyenne_query = mysql_query("SELECT round(avg(note),1) moyenne
        FROM matieres_notes
        WHERE (
        statut ='' AND
        id_groupe='".$current_group[$j]["id"]."' AND
        periode='$periode_num'
        )
        ");
	*/
    $sql="SELECT round(avg(note),1) moyenne
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
    // Calcul de la moyenne des élèves et de la moyenne de la classe
    $i=0;
	//======================================
	// Ajout: boireaus 20080408
	//$current_coef_eleve=array();

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
	//echo "\$quartile3_grp[$j]=".$quartile3_grp[$j]."<br />";
	$sql="SELECT COUNT(note) as quartile4 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note>=8 AND
								note<10)";
	//echo "$sql<br />";
	$quartile4_grp[$j]=sql_query1($sql);
	//echo "\$quartile4_grp[$j]=".$quartile4_grp[$j]."<br />";
	$sql="SELECT COUNT(note) as quartile5 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note>=5 AND
								note<8)";
	//echo "$sql<br />";
	$quartile5_grp[$j]=sql_query1($sql);
	//echo "\$quartile5_grp[$j]=".$quartile5_grp[$j]."<br />";
	$sql="SELECT COUNT(note) as quartile6 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note<5)";
	//echo "$sql<br />";
	$quartile6_grp[$j]=sql_query1($sql);
	//echo "\$quartile6_grp[$j]=".$quartile6_grp[$j]."<br />";

	//======================================
    while ($i < $nombre_eleves) {
        $current_eleve_login[$i] = mysql_result($appel_liste_eleves, $i, "login");

		//======================================
		// Ajout: boireaus 20080408
		//$current_coef_eleve[$i]=array();
		//======================================

        // Maintenant on regarde si l'élève suit bien cette matière ou pas
        if (in_array($current_eleve_login[$i], $current_group[$j]["eleves"][$periode_num]["list"])) {
        	//$count[$j][$i] == "0"
			/*
            $current_eleve_note_query = mysql_query("SELECT distinct * FROM matieres_notes
            WHERE (
            login='".$current_eleve_login[$i]."' AND
            periode='$periode_num' AND
            id_groupe='".$current_group[$j]["id"]."'
            )");
			*/
            $sql="SELECT distinct * FROM matieres_notes
            WHERE (
            login='".$current_eleve_login[$i]."' AND
            periode='$periode_num' AND
            id_groupe='".$current_group[$j]["id"]."'
            )";
			calc_moy_debug("$sql\n");
			$current_eleve_note_query = mysql_query($sql);

            $current_eleve_note[$j][$i] = @mysql_result($current_eleve_note_query, 0, "note");
			calc_moy_debug("\$current_eleve_note[$j][$i]=".$current_eleve_note[$j][$i]."\n");
            $current_eleve_statut[$j][$i] = @mysql_result($current_eleve_note_query, 0, "statut");
			calc_moy_debug("\$current_eleve_statut[$j][$i]=".$current_eleve_statut[$j][$i]."\n");
            // On teste si l'élève a un coef spécifique pour cette matière
            /*
			$test_coef = mysql_query("SELECT value FROM eleves_groupes_settings WHERE (" .
            		"login = '".$current_eleve_login[$i]."' AND " .
            		"id_groupe = '".$current_group[$j]["id"]."' AND " .
            		"name = 'coef')");
			*/
			calc_moy_debug("\$coefficients_a_1=$coefficients_a_1\n");
			if((isset($coefficients_a_1))&&($coefficients_a_1=="oui")) {
				$coef_eleve=1;
			}
			else{
				$sql="SELECT value FROM eleves_groupes_settings WHERE (" .
						"login = '".$current_eleve_login[$i]."' AND " .
						"id_groupe = '".$current_group[$j]["id"]."' AND " .
						"name = 'coef')";
				calc_moy_debug("$sql\n");
				$test_coef = mysql_query($sql);
				if (mysql_num_rows($test_coef) > 0) {
					$coef_eleve = mysql_result($test_coef, 0);
				} else {
					$coef_eleve = $current_coef[$j];
				}
			}

			//======================================
			// Ajout: boireaus 20080408
			$current_coef_eleve[$i][$j]=$coef_eleve;

			if ((isset($affiche_rang))&&($affiche_rang=='y')) {
				$current_eleve_rang[$j][$i] = @mysql_result($current_eleve_note_query, 0, "rang");
			}

			//======================================

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


			calc_moy_debug("\$coef_eleve=$coef_eleve\n");
            if ($coef_eleve != 0) {
               if (($current_eleve_note[$j][$i] != '') and ($current_eleve_statut[$j][$i] == '')) {

                    //$total_coef[$i] += $coef_eleve;
					//calc_moy_debug("\$total_coef[$i]=$total_coef[$i]\n");
                    $total_coef_classe[$i] += $current_coef[$j];
					calc_moy_debug("\$total_coef_classe[$i]=$total_coef_classe[$i]\n");
                    $total_coef_eleve[$i] += $coef_eleve;
					calc_moy_debug("\$total_coef_eleve[$i]=$total_coef_eleve[$i]\n");

                    //$total_coef_cat[$i][$prev_cat] += $coef_eleve;
                    $total_coef_cat_classe[$i][$prev_cat] += $current_coef[$j];
                    $total_coef_cat_eleve[$i][$prev_cat] += $coef_eleve;

                    //$moy_gen_classe[$i] += $coef_eleve*$current_classe_matiere_moyenne[$j];
                    $moy_gen_classe[$i] += $current_coef[$j]*$current_classe_matiere_moyenne[$j];
					calc_moy_debug("\$moy_gen_classe[$i]=$moy_gen_classe[$i]\n");

                    //$moy_cat_classe[$i][$prev_cat] += $coef_eleve*$current_classe_matiere_moyenne[$j];
                    $moy_cat_classe[$i][$prev_cat] += $current_coef[$j]*$current_classe_matiere_moyenne[$j];

                    $moy_gen_eleve[$i] += $coef_eleve*$current_eleve_note[$j][$i];
					calc_moy_debug("\$moy_gen_eleve[$i]=$moy_gen_eleve[$i]\n");

                    $moy_cat_eleve[$i][$prev_cat] += $coef_eleve*$current_eleve_note[$j][$i];
                }
            }
        }
        $i++;
		calc_moy_debug("==============================\n");
    }
    $j++;
}

$i = 0;
while ($i < $nombre_eleves) {
    //if ($total_coef[$i] != 0) {
    if ($total_coef_eleve[$i] != 0) {
        $place_eleve_classe[$i] = "";
        //$moy_gen_eleve[$i] = $moy_gen_eleve[$i]/$total_coef[$i];
        $moy_gen_eleve[$i] = $moy_gen_eleve[$i]/$total_coef_eleve[$i];
		calc_moy_debug("\$moy_gen_eleve[$i]=$moy_gen_eleve[$i]\n");

		if($total_coef_classe[$i] != 0){
			//$moy_gen_classe[$i] = $moy_gen_classe[$i]/$total_coef[$i];
			$moy_gen_classe[$i] = $moy_gen_classe[$i]/$total_coef_classe[$i];
			calc_moy_debug("\$moy_gen_classe[$i]=$moy_gen_classe[$i]\n");
			$moy_gen_classe[$i] = number_format($moy_gen_classe[$i],1, ',', ' ');
		}
		else{
			$moy_gen_classe[$i]="-";
		}

        // Préparation des données pour affichage des graphqiues
        if ($affiche_graph == 'y')  {
            if ($moy_gen_eleve[$i] >= 15) {$quartile1_classe_gen++; $place_eleve_classe[$i] = 1;}
            else if (($moy_gen_eleve[$i] >= 12) and ($moy_gen_eleve[$i] < 15)) {$quartile2_classe_gen++;$place_eleve_classe[$i] = 2;}
            else if (($moy_gen_eleve[$i] >= 10) and ($moy_gen_eleve[$i] < 12)) {$quartile3_classe_gen++;$place_eleve_classe[$i] = 3;}
            else if (($moy_gen_eleve[$i] >= 8) and ($moy_gen_eleve[$i] < 10)) {$quartile4_classe_gen++;$place_eleve_classe[$i] = 4;}
            else if (($moy_gen_eleve[$i] >= 5) and ($moy_gen_eleve[$i] < 8)) {$quartile5_classe_gen++;$place_eleve_classe[$i] = 5;}
            else {$quartile6_classe_gen++;$place_eleve_classe[$i] = 6;}
        }
        // Eric déplacé en fin de fichier dans une nouvelle boucle.
        //$moy_gen_eleve[$i] = number_format($moy_gen_eleve[$i],1, ',', ' ');
        //$moy_gen_classe[$i] = number_format($moy_gen_classe[$i],1, ',', ' ');

    } else {
        $moy_gen_eleve[$i] = "-";
        $moy_gen_classe[$i] = "-";

    }
    foreach($categories as $cat) {
	    //if ($total_coef_cat[$i][$cat] != 0) {
	    if ($total_coef_cat_classe[$i][$cat] != 0) {
	        $moy_cat_classe[$i][$cat] = $moy_cat_classe[$i][$cat]/$total_coef_cat_classe[$i][$cat];
	        $moy_cat_classe[$i][$cat] = number_format($moy_cat_classe[$i][$cat],1, ',', ' ');
	    } else {
	        $moy_cat_classe[$i][$cat] = "-";
	    }

	    if ($total_coef_cat_eleve[$i][$cat] != 0) {
	        $moy_cat_eleve[$i][$cat] = $moy_cat_eleve[$i][$cat]/$total_coef_cat_eleve[$i][$cat];
	        $moy_cat_eleve[$i][$cat] = number_format($moy_cat_eleve[$i][$cat],1, ',', ' ');
	    } else {
	        $moy_cat_eleve[$i][$cat] = "-";
	    }
    }
    $i++;
	calc_moy_debug("==============================\n");
}

//Ajout Eric pour avoir les moyennes générales minimum et maximum sur les bulletins

//$moy_min_classe = min($moy_gen_eleve);
$moy_min_classe=20;
for ( $i=0 ; $i < sizeof($moy_gen_eleve) ; $i++ ) {
	if($moy_gen_eleve[$i]!="-"){
		if($moy_gen_eleve[$i]<$moy_min_classe){
			$moy_min_classe=$moy_gen_eleve[$i];
		}
	}
}

$moy_min_classe = number_format($moy_min_classe,1, ',', ' ');
$moy_max_classe = max($moy_gen_eleve);
$moy_max_classe = number_format($moy_max_classe,1, ',', ' ');

//Calcul de la moyenne générale de la classe
$nb_elv_classe=sizeof($moy_gen_eleve);
$moy_generale_classe = 0;
for ( $i=0 ; $i < $nb_elv_classe ; $i++ ) {
  $moy_generale_classe += $moy_gen_eleve[$i];
}
$moy_generale_classe = $moy_generale_classe / $nb_elv_classe;

$moy_generale_classe = number_format($moy_generale_classe,1, ',', ' ');


for ( $i=0 ; $i < sizeof($moy_gen_eleve) ; $i++ ) {
  $moy_gen_eleve[$i] = number_format($moy_gen_eleve[$i],1, ',', ' ');
}

// On fournit en entrée:
//     - $id_classe : la classe concernée
//     - $periode_num : la période concernée
// On récupère en sortie:
//     - $moy_gen_eleve[$i]
//     - $moy_gen_classe[$i]
//     - $moy_generale_classe
//     - $moy_max_classe
//     - $moy_min_classe

// A VERIFIER, mais s'il n'y a pas de coef spécifique pour un élève, on devrait avoir
//             $moy_gen_classe[$i] == $moy_generale_classe

//     - $moy_cat_classe[$i][$cat]
//     - $moy_cat_eleve[$i][$cat]

// Là le positionnement au niveau moyenne générale:
//     - $quartile1_classe_gen
//       à
//     - $quartile6_classe_gen
//     - $place_eleve_classe[$i]

// On a récupéré en intermédiaire les
//     - $current_eleve_login[$i]
//     - $current_group[$j]
//     - $current_eleve_note[$j][$i]
//     - $current_eleve_statut[$j][$i]
//     - $current_coef[$j] (qui peut être différent du $coef_eleve pour une matière spécifique)
//     - $categories -> id
//     - $current_classe_matiere_moyenne[$j] (moyenne de la classe dans la matière)

// AJOUTé:
//     - $current_coef_eleve[$i][$j]
//     - $moy_min_classe_grp[$j]
//     - $moy_max_classe_grp[$j]
//     - $current_eleve_rang[$j][$i] sous réserve que $affiche_rang=='y'
//     - $quartile1_grp[$j] à $quartile6_grp[$j]
//     - $place_eleve_grp[$j][$i]

?>