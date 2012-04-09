<?php
/** Mise à jour des rangs
 * 
 * 
 * Script à appeler dans le code
 * 
 * Met à jour les champs rang des tables
 * - matieres_notes 
 * - j_eleves_classes
 * 
 * Egalement : calcul  des tableaux suivants
 * - $moy_gen_classe =  tableau des moyennes générales de la classe
 * - $moy_gen_eleve =  tableau des moyennes générales d'élèves

 * le script à besoin de :
 * - $id_classe : la classe concernée
 * - $periode_num : la période concernée
 *
 * @copyright Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @package Notes
 * @subpackage scripts
 */

/* 
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


// On appelle la liste des élèves de la classe.

$appel_liste_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c
    WHERE (
    e.login = c.login and
    c.id_classe = '".$id_classe."' and
    c.periode='".$periode_num."'
    )
    ORDER BY e.nom, e.prenom");
$nombre_eleves = mysql_num_rows($appel_liste_eleves);

// On prépare la boucle 'groupes'

if ($affiche_categories) {
		// On utilise les valeurs spécifiées pour la classe en question
		$appel_liste_groupes = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.categorie_id ".
		"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
		"WHERE ( " .
		"jgc.categorie_id = jmcc.categorie_id AND " .
		"jgc.id_classe='".$id_classe."' AND " .
		"jgm.id_groupe=jgc.id_groupe AND " .
		"m.matiere = jgm.id_matiere" .
		") " .
		"ORDER BY jmcc.priority,jgc.priorite,m.nom_complet");
} else {
	$appel_liste_groupes = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef
	FROM j_groupes_classes jgc, j_groupes_matieres jgm
	WHERE (
	jgc.id_classe='".$id_classe."' AND
	jgm.id_groupe=jgc.id_groupe
	)
	ORDER BY jgc.priorite,jgm.id_matiere");
}

$nombre_groupes = mysql_num_rows($appel_liste_groupes);

// Initialisation des tableaux liés aux calculs des moyennes générales
$moy_gen_classe = array();
$moy_gen_eleve = array();
$total_coef = array();
$i=0;
while ($i < $nombre_eleves) {
    $total_coef[$i] = 0;
    $moy_gen_eleve[$i] = 0;
    $moy_gen_classe[$i] = 0;
    $i++;
}
$calcul_moy_gen = 'no';
$j=0;
while ($j < $nombre_groupes) {
    $group_id = mysql_result($appel_liste_groupes, $j, "id_groupe");
    $current_group[$j] = get_group($group_id);
    // Dans tous les cas, on effectue cette requête qui permet de calculer le tableau $nb_notes[] utilisé lors de l'affichage du rang
    $quer = mysql_query("select distinct note, login  from matieres_notes
        where (
        periode = '".$periode_num."' and
        id_groupe = '".$current_group[$j]["id"]."' and
        statut = ''
        )
        order by note DESC
        ");
        $nb_notes[$group_id][$periode_num] = mysql_num_rows($quer);
	
    // Calcul du rang pour chaque matière
    $recalcul_rang = sql_query1("select recalcul_rang from groupes
        where id='".$current_group[$j]["id"]."' limit 1 ");

    if ($recalcul_rang == "-1") $recalcul_rang = '';

    if (mb_substr($recalcul_rang, $periode_num-1, 1) != 'n') {
        $calcul_moy_gen = 'yes';
        // le recalcul du rang est nécessaire
        $k= 0;
        $rang_prec = 1;
        $note_prec='';
		$max_rang=0;
        //mise à jour du champ des rangs
        while ($k < $nb_notes[$group_id][$periode_num]) {
            if ($k >=1) $note_prec = mysql_result($quer, $k-1, 'note');
            $note = mysql_result($quer, $k, 'note');
            if ($note == $note_prec) $rang = $rang_prec; else $rang = ($k+1);
            $rang_prec = $rang;
            $login_eleve_temp = mysql_result($quer, $k, 'login');
			
            $reg_rang = mysql_query("update matieres_notes set rang='".$rang."' where (
            periode = '".$periode_num."' and
            id_groupe = '".$current_group[$j]["id"]."' and
            login = '".$login_eleve_temp."' )
            ");
			if($rang>$max_rang) {$max_rang=$rang;}
            $k++;
        }

		// Pour ne pas laisser un Rang à zéro (valeur par défaut du champ)... ce qui fait passer un non noté en première position:
		$max_rang++;
		$sql="UPDATE matieres_notes SET rang='$max_rang' WHERE (
			periode = '".$periode_num."' and
			id_groupe = '".$current_group[$j]["id"]."' and
			statut = '-' )
			";
		$update_rang_non_notes=mysql_query($sql);

        // On indique que le recalcul du rang n'est plus nécessaire
        $long = mb_strlen($recalcul_rang);
        if ($long >= $periode_num) {
            $recalcul_rang = substr_replace( $recalcul_rang, "n", $periode_num-1, $periode_num);
        } else {
            $l = $long;
            while ($l < $periode_num-1) {
                $recalcul_rang = $recalcul_rang.'y';
                $l++;
            }
            $recalcul_rang = $recalcul_rang.'n';
        }
        $req = mysql_query("update groupes set recalcul_rang = '".$recalcul_rang."'
        where id='".$current_group[$j]["id"]."'");
    }
    $j++;
}

// S'il existe des matières coefficientées ou si le champ rang de la table j_eleves_classes n'est pas à jour :
if (($test_coef != '0') and ($calcul_moy_gen == 'yes')) {
    $j=0;
    while ($j < $nombre_groupes) {
        // S'il existe des matières coefficientées et si le champ rang de la table j_eleves_classes n'est pas à jour :
        $current_coef[$j] = $current_group[$j]["classes"]["classes"][$id_classe]["coef"];
        $current_mode_moy[$j] = $current_group[$j]["classes"]["classes"][$id_classe]["mode_moy"];

        // Moyenne de la classe dans la groupe $current_group[$j]
        $current_classe_matiere_moyenne_query = mysql_query("SELECT round(avg(note),1) moyenne
            FROM matieres_notes
            WHERE (
            statut = '' AND
            id_groupe='".$current_group[$j]["id"]."' AND
            periode='$periode_num'
            )
            ");
        $current_classe_matiere_moyenne[$j] = mysql_result($current_classe_matiere_moyenne_query, 0, "moyenne");

        // Calcul de la moyenne des élèves et de la moyenne de la classe
        $i=0;
        while ($i < $nombre_eleves) {
            $current_eleve_login[$i] = mysql_result($appel_liste_eleves, $i, "login");
            // Maintenant on regarde si l'élève suit bien cette matière ou pas

            if (in_array($current_eleve_login[$i], $current_group[$j]["eleves"][$periode_num]["list"])) {
                $current_eleve_note_query = mysql_query("SELECT distinct * FROM matieres_notes
                WHERE (
                login='".$current_eleve_login[$i]."' AND
                periode='$periode_num' AND
                id_groupe='".$current_group[$j]["id"]."'
                )");
                $current_eleve_note[$j][$i] = @mysql_result($current_eleve_note_query, 0, "note");
                $current_eleve_statut[$j][$i] = @mysql_result($current_eleve_note_query, 0, "statut");
                
		        // On teste si l'élève a un coef spécifique pour cette matière
		        $test_coef = mysql_query("SELECT value FROM eleves_groupes_settings WHERE (" .
		        		"login = '".$current_eleve_login[$i]."' AND " .
		        		"id_groupe = '".$current_group[$j]["id"]."' AND " .
		        		"name = 'coef')");
		        if (mysql_num_rows($test_coef) > 0) {
		        	$coef_eleve = mysql_result($test_coef, 0);
		        } else {
		        	$coef_eleve = $current_coef[$j];
		        }
                
                if ($coef_eleve != 0) {
                    if (($current_eleve_note[$j][$i] != '') and ($current_eleve_statut[$j][$i] == '')) {
                        if($current_mode_moy[$j]=='sup10') {
                            //if($moy_gen_eleve[$i]>=10) {
                            if($current_eleve_note[$j][$i]>=10) {
                                $total_coef[$i] += $coef_eleve;
                                $moy_gen_eleve[$i] += $coef_eleve*$current_eleve_note[$j][$i];
                            }
                            $moy_gen_classe[$i] += $coef_eleve*$current_classe_matiere_moyenne[$j];
                        }
                        elseif($current_mode_moy[$j]=='bonus') {
                            //if($moy_gen_eleve[$i]>=10) {
                            if($current_eleve_note[$j][$i]>=10) {
                                $moy_gen_eleve[$i] += $coef_eleve*($current_eleve_note[$j][$i]-10);
                            }
                            $moy_gen_classe[$i] += $coef_eleve*$current_classe_matiere_moyenne[$j];
                        }
                        else {
                            $total_coef[$i] += $coef_eleve;
                            $moy_gen_classe[$i] += $coef_eleve*$current_classe_matiere_moyenne[$j];
                            $moy_gen_eleve[$i] += $coef_eleve*$current_eleve_note[$j][$i];
                        }
                    }
               }
            }
            $i++;
        }
        $j++;
    }

    $i = 0;
    while ($i < $nombre_eleves) {
        if ($total_coef[$i] != 0) {
            $temp[$i] = round($moy_gen_eleve[$i]/$total_coef[$i],1);
            $moy_gen_eleve[$i] = number_format($moy_gen_eleve[$i]/$total_coef[$i],1, ',', ' ');
            $moy_gen_classe[$i] = number_format($moy_gen_classe[$i]/$total_coef[$i],1, ',', ' ');
        } else {
            $moy_gen_eleve[$i] = "-";
            $moy_gen_classe[$i] = "-";
            $temp[$i] = "-";
        }
        $rg[$i] = $i;
        $i++;
    }

    // tri des tableau
    array_multisort ($temp, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);
    $i=0;
    $rang_prec = 1;
    $note_prec='';
    while ($i < $nombre_eleves) {
        $ind = $rg[$i];
        if ($temp[$i] == "-") {
            $rang_gen = '0';
        } else {
            if ($temp[$i] == $note_prec) $rang_gen = $rang_prec; else $rang_gen = $i+1;
            $note_prec = $temp[$i];
            $rang_prec = $rang_gen;
        }
        $reg_rang = mysql_query("update j_eleves_classes set rang='".$rang_gen."' where (
        periode = '".$periode_num."' and
        id_classe = '".$id_classe."' and
        login = '".$current_eleve_login[$ind]."')");
         $i++;
    }
}
if (($test_coef == '0') and ($calcul_moy_gen == 'yes')) {
    $i=0;
    while ($i < $nombre_eleves) {
        $reg_rang = mysql_query("update j_eleves_classes set rang='0' where
        (
        periode = '".$periode_num."' and
        id_classe = '".$id_classe."' )");
         $i++;
    }
}

?>
