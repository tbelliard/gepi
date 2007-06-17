<?php
/*
 * $Id$
 *
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

// Initialisations files
require_once("../lib/initialisations.inc.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);
// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!isset($en_tete)) $en_tete = "yes";
if (!isset($stat)) $stat = "no";
if (!isset($larg_tab)) $larg_tab = 680;
if (!isset($bord)) $bord = 1;
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "nom");
$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
    $current_group = get_group($id_groupe);
} else {
    $current_group = false;
}

if (count($current_group["classes"]["list"]) > 1) {
    $multiclasses = true;
} else {
    $multiclasses = false;
    $order_by = "nom";
}

include "../lib/periodes.inc.php";
    //**************** EN-TETE *****************
if ($en_tete == "yes") $titre_page = "Visualisation des moyennes et appréciations";
require_once("../lib/header.inc");
    //**************** FIN EN-TETE *****************
if (isset($_SESSION['chemin_retour'])) $retour = $_SESSION['chemin_retour'] ; else $retour = "index1.php";

if ($en_tete!="yes"){
	echo "<script type='text/javascript'>
	document.body.style.backgroundColor='white';
</script>\n";
}


if (!$current_group) {
    unset($_SESSION['chemin_retour']);
    echo "<p class='bold'><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";
    echo "<p>Votre choix :</p>\n";
    //$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
    //$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	if($_SESSION['statut']=='scolarite'){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	}
	elseif($_SESSION['statut']=='professeur'){
		$appel_donnees=mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe");
	}
	elseif($_SESSION['statut']=='cpe'){
		$appel_donnees=mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe");
	}

	$lignes = mysql_num_rows($appel_donnees);

	if($lignes==0){
		echo "<p>Aucune classe ne vous est attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
	}
	else{
		$nb_class_par_colonne=round($lignes/3);
		/*
		echo "<table width='100%'>\n";
		echo "<tr valign='top' align='center'>\n";
			echo "<td>\n";
		*/

		$i = 0;
		while($i < $lignes){
		/*
		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			echo "<td>\n";
		}
		*/

			$id_classe = mysql_result($appel_donnees, $i, "id");
			$aff_class = 'no';
			$groups = get_groups_for_class($id_classe);

			foreach($groups as $group){
				$flag2 = "no";
				if ($_SESSION['statut']!='scolarite') {
					$test = mysql_query("SELECT count(*) FROM j_groupes_professeurs
					WHERE (id_groupe='" . $group["id"]."' and login = '" . $_SESSION["login"] . "')");
					if (mysql_result($test, 0) == 1) $flag2 = 'yes';
				} else {
					$flag2 = 'yes';
				}

				if ($flag2 == "yes") {
					$display_class = mysql_result($appel_donnees, $i, "classe");
				echo "<span class='norme'>";
					//if ($aff_class == 'no') {echo "<span class='norme'><b>$display_class</b> : ";$aff_class = 'yes';}
					if ($aff_class == 'no') {echo "<b>$display_class</b> : ";$aff_class = 'yes';}
					//echo "<a href='index1.php?id_groupe=" . $group["id"] . "'>" . $group["description"] . "</a> - ";
					//echo "<a href='index1.php?id_groupe=" . $group["id"] . "'>" . htmlentities($group["description"]) . "</a></span> - \n";

					echo "<a href='index1.php?id_groupe=" . $group["id"] . "'>" . htmlentities($group["description"]) . " </a>\n";

					// pas de nom si c'est un prof qui demande la page.
					if ($_SESSION['statut']!='professeur') {
						$id_groupe_en_cours = $group["id"];
						//recherche profs du groupe
						$sql_prof_groupe = "SELECT jgp.login,u.nom,u.prenom FROM j_groupes_professeurs jgp,utilisateurs u WHERE jgp.id_groupe='$id_groupe_en_cours' AND u.login=jgp.login";
						$result_prof_groupe=mysql_query($sql_prof_groupe);
						echo "(";
						$cpt=0;
						$nb_profs = mysql_num_rows($result_prof_groupe);
						while($lig_prof=mysql_fetch_object($result_prof_groupe)){
							if (($nb_profs !=1) AND ($cpt<$nb_profs-1)){
							echo "$lig_prof->nom ".ucfirst(strtolower($lig_prof->prenom))." - ";
							} else {
							echo "$lig_prof->nom ".ucfirst(strtolower($lig_prof->prenom));
							}
							$cpt++;
						}
						echo ")";
					}

					echo "<br />\n";
				}
			}
			//if ($flag2 == 'yes') {echo "</span><br /><br />\n";}
			if ($flag2 == 'yes') {echo "<br /><br />\n";}
			$i++;
		}
	}
        /*
	echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
	*/

} else if (!isset($choix_visu)) {
    echo "<p class=bold><a href=\"".$retour."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";
    if ((!(check_prof_groupe($_SESSION['login'],$id_groupe))) and ($_SESSION['statut']!='scolarite') and ($_SESSION['statut']!='secours')) {
        echo "<p>Vous n'êtes pas dans cette classe le professeur de la matière choisie !</p>\n";
        echo "<p><a href='index1.php'>Retour à l'accueil</a></p>\n";
        die();
    }

    echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire\">";
    echo "<p class='bold'>Groupe : " . htmlentities($current_group["description"]) ." " . htmlentities($current_group["classlist_string"]) . " | Matière : " . htmlentities($current_group["matiere"]["nom_complet"]) . "&nbsp;&nbsp;<input type='submit' value='Valider' /></p>\n";
    echo "<p>Choisissez les données à imprimer (vous pouvez cocher plusieurs cases) : </p>\n";
    $i="1";
    while ($i < $nb_periode) {
        $name = "visu_note_".$i;
        echo "<p><INPUT TYPE='CHECKBOX' NAME='$name' VALUE='yes' />".ucfirst($nom_periode[$i])." - Extraire les moyennes</p>\n";
    $i++;
    }
    $i="1";
    while ($i < $nb_periode) {
            $name = "visu_app_".$i;
            echo "<p><INPUT TYPE='CHECKBOX' NAME='$name' VALUE='yes' />".ucfirst($nom_periode[$i])." - Extraire les appréciations</p>\n";
    $i++;
    }
    echo "<p><INPUT TYPE='CHECKBOX' NAME='stat' VALUE='yes' />Afficher les statistiques sur les moyennes extraites (moyenne générale, pourcentages, ...)</p>\n";
    if ($multiclasses) {
        echo "<p><input type='radio' name='order_by' value='nom' checked /> Classer les élèves par ordre alphabétique ";
        echo "<br/><input type='radio' name='order_by' value='classe' /> Classer les élèves par classe</p>";
    }
    echo "<input type='submit' value='Valider' />\n";
    echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
    echo "<input type='hidden' name='choix_visu' value='yes' />\n";
    echo "</form>\n";
} else {
    $nombre_eleves = count($current_group["eleves"]["all"]["list"]);

    // On commence par mettre la liste dans l'ordre souhaité
    if ($order_by != "classe") {
        $liste_eleves = $current_group["eleves"]["all"]["list"];
    } else {
        // Ici, on tri par classe
        // On va juste créer une liste des élèves pour chaque classe
        $tab_classes = array();
        foreach($current_group["classes"]["list"] as $classe_id) {
            $tab_classes[$classe_id] = array();
        }
        // On passe maintenant élève par élève et on les met dans la bonne liste selon leur classe
        foreach($current_group["eleves"]["all"]["list"] as $eleve_login) {
            $classe = $current_group["eleves"]["all"]["users"][$eleve_login]["classe"];
            $tab_classes[$classe][] = $eleve_login;
        }
        // On met tout ça à la suite
        $liste_eleves = array();
        foreach($current_group["classes"]["list"] as $classe_id) {
            $liste_eleves = array_merge($liste_eleves, $tab_classes[$classe_id]);
        }
    }


    $nb_col = 1;
    if ($multiclasses) $nb_col++;
    $total_notes = 0;
    $min_notes = '-';
    $max_notes = '-';
    $pourcent_i8 = 0;
    $pourcent_se12 = 0;
    $pourcent_se8_ie12 = 0;
    $i = "0";
    $eleve_login = null;
    foreach($liste_eleves as $eleve_login) {
        // La variable affiche_ligne teste si on affiche une ligne ou non : si l'élève suit la matière pour au moins une période, on affiche la ligne concernant l'élève. Si l'élève ne suit pas la matière pour aucune des périodes, on n'affiche pas la ligne conernant l'élève.
        $affiche_ligne[$i] = 'no';
        $login_eleve[$i] = $eleve_login;
        $k=0;
        while ($k < $nb_periode) {
            $temp1 = "visu_note_".$k;
            $temp2 = "visu_app_".$k;
            if (isset($_POST[$temp1]) or isset($_POST[$temp2]) or isset($_GET[$temp1]) or isset($_GET[$temp2])) {

                if (!in_array($eleve_login, $current_group["eleves"][$k]["list"])) {
                    $option[$i][$k] = "non";
                } else {
                    $option[$i][$k] = "oui";
                    $affiche_ligne[$i] = 'yes';
                }
            }
            $k++;
        }
        $i++;
    }
    //
    // Calcul du nombre de colonnes à afficher et définition de la première ligne à afficher
    //
    $ligne1[1] = "Nom Prénom";
    if ($multiclasses) $ligne1[2] = "Classe";
    $k = 1;
//    if (isset($_POST['stat']) or isset($_GET['stat'])) $only_stats = 'yes'; else $only_stats = 'no';
    while ($k < $nb_periode) {
        $temp = "visu_note_".$k;
        if (isset($_POST[$temp]) or isset($_GET[$temp])) {
            $nb_col++;
//            $only_stats = 'no';
            $ligne1[$nb_col] = "Note P".$k;
        }
        $temp = "visu_app_".$k;
        if (isset($_POST[$temp]) or isset($_GET[$temp])) {
            $nb_col++;
            $ligne1[$nb_col] = "Appréciation P".$k;
        }
        $k++;
    }
    if ($stat == "yes") {
        $nb_col++;
        $ligne1[$nb_col] = "Moyenne";
    }
    $i = 0;
    $nb_lignes = '0';
    $nb_notes = '0';
    while($i < $nombre_eleves) {
        if ($affiche_ligne[$i] == 'yes') {
            // Calcul de la moyenne
            if ($stat == "yes") $col[$nb_col][$nb_lignes] = '';
            for ($k=1;$k<$nb_periode;$k++) {
                if (in_array($login_eleve[$i], $current_group["eleves"][$k]["list"])) {
                    $col[1][$nb_lignes] = $current_group["eleves"][$k]["users"][$login_eleve[$i]]["prenom"] . " " . $current_group["eleves"][$k]["users"][$login_eleve[$i]]["nom"];
                    if ($multiclasses) $col[2][$nb_lignes] = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$login_eleve[$i]]["classe"]]["classe"];
                    break;
                }
            }

            $k=1;
            $j=1;
            if ($multiclasses) $j++;
            while ($k < $nb_periode) {
                $temp = "visu_note_".$k;
                if (isset($_POST[$temp]) or isset($_GET[$temp])) {
                    $j++;
                    $note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$login_eleve[$i]' AND id_groupe = '".$current_group["id"] . "' AND periode='$k')");
                    $_statut = @mysql_result($note_query, 0, "statut");
                    $note = @mysql_result($note_query, 0, "note");
                    if ($option[$i][$k] == "non") {
                        $col[$j][$nb_lignes] = "-";
                    } else {
                        if ($_statut != '') {
                            $col[$j][$nb_lignes] = $_statut;
                        } else {
                            if ($note != '') {
                                $col[$j][$nb_lignes] = number_format($note,1,',','');
                                if ($stat == "yes") {
                                    $col[$nb_col][$nb_lignes] += $note;
                                    if (!isset($nb_note[$nb_lignes])) $nb_note[$nb_lignes]=1; else $nb_note[$nb_lignes]++;
                                }
                            } else {
                                $col[$j][$nb_lignes] = '-';
                            }
                        }
                    }
                }
                $temp = "visu_app_".$k;
                if (isset($_POST[$temp]) or isset($_GET[$temp])) {

                    $j++;
                    $app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$login_eleve[$i]' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
                    $app = @mysql_result($app_query, 0, "appreciation");

		    //++++++++++++++++++++++++
		    // Modif d'après F.Boisson
		    // notes dans appreciation
        	    $sql="SELECT cnd.note FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE cnd.login='".$login_eleve[$i]."' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group["id"]."' AND ccn.periode='$k' AND cnd.statut='';";
	            $result_nbct=mysql_query($sql);
		    $string_notes='';
		    if ($result_nbct ) {
			while ($snnote =  mysql_fetch_assoc($result_nbct)) {
				if ($string_notes != '') $string_notes .= ", ";
				$string_notes .= $snnote['note'];
			}
		    }
		    $app = str_replace('@@Notes', $string_notes,$app);
		    //++++++++++++++++++++++++


                    if ($app != '') {
			// =========================================
			// MODIF: boireaus
                        //$col[$j][$nb_lignes] = $app;
			if((strstr($app,">"))||(strstr($app,"<"))){
				$col[$j][$nb_lignes] = $app;
			}
			else{
				$col[$j][$nb_lignes] = nl2br($app);
			}
			// =========================================
                    } else {
                        $col[$j][$nb_lignes] = '-';
                    }
                }
                $k++;
            }
            if ($stat == "yes") {
                if ($col[$nb_col][$nb_lignes] != '') {
                    // moyenne de chaque élève
                    $temp = round($col[$nb_col][$nb_lignes]/$nb_note[$nb_lignes],1);
                    $col[$nb_col][$nb_lignes] = number_format($temp,1,',','');
                    // Total des moyennes de chaque élève
                    $total_notes += $temp;
                    $nb_notes++;
                    if ($min_notes== '-') $min_notes = 20;
                    $min_notes = min($min_notes,$temp);
                    $max_notes = max($max_notes,$temp);
                    if ($temp < 8) $pourcent_i8++;
                    if ($temp >= 12) $pourcent_se12++;
                } else {
                    $col[$nb_col][$nb_lignes] = '-' ;
                }
            }
            $nb_lignes++;
        }
        $i++;
    }
    //
    // On teste s'il y a des moyennes, min et max à calculer :
    //
    $k = 1;
    $test = 0;
    while ($k < $nb_periode) {
        $temp = "visu_note_".$k;
        if (isset($_POST[$temp]) or isset($_GET[$temp])) {$test = 1;}
        $k++;
    }
    //
    // S'il y a des moyennes, min et max à calculer, on le fait :
    //
    if ($test == 1) {
        $k=1;
        $j=1;
        if ($multiclasses) $j++;
        while ($k < $nb_periode) {
            $temp = "visu_note_".$k;
            if (isset($_POST[$temp]) or isset($_GET[$temp])) {

                $j++;
                $call_moyenne = mysql_query("SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (id_groupe='$id_groupe' AND statut ='' AND periode='$k')");
                $call_max = mysql_query("SELECT max(note) note_max FROM matieres_notes WHERE (id_groupe='$id_groupe' AND statut ='' AND periode='$k')");
                $call_min = mysql_query("SELECT min(note) note_min FROM matieres_notes WHERE (id_groupe='$id_groupe' AND statut ='' AND periode='$k')");
                $temp = @mysql_result($call_moyenne, 0, "moyenne");
                if ($temp != '') {
                    $col[$j][$nb_lignes] = number_format($temp,1,',','');
                } else {
                    $col[$j][$nb_lignes] = '-';
                }
                $temp = @mysql_result($call_min, 0, "note_min");
                if ($temp != '') {
                    $col[$j][$nb_lignes+1] = number_format($temp,1,',','');
                } else {
                    $col[$j][$nb_lignes+1] = '-';
                }
                $temp = @mysql_result($call_max, 0, "note_max");
                if ($temp != '') {
                    $col[$j][$nb_lignes+2] = number_format($temp,1,',','');
                } else {
                    $col[$j][$nb_lignes+2] = '-';
                }
            }
            $temp = "visu_app_".$k;
            if (isset($_POST[$temp]) or isset($_GET[$temp])) {
                $j++;
                $col[$j][$nb_lignes] = '-';
                $col[$j][$nb_lignes+1] = '-';
                $col[$j][$nb_lignes+2] = '-';
            }
            $k++;
        }
        if ($stat == "yes") {
            // moyenne générale de la classe
            if ($total_notes != 0) $col[$nb_col][$nb_lignes] = number_format(round($total_notes/$nb_notes,1),1,',','') ; else $col[$nb_col][$nb_lignes] = '-';
            $moy_gen = $col[$nb_col][$nb_lignes];
            $col[$nb_col][$nb_lignes+1] = $min_notes;
            $col[$nb_col][$nb_lignes+2] = $max_notes;
		if($nb_notes!=0){
			$pourcent_se8_ie12 = number_format(($nb_notes-$pourcent_se12-$pourcent_i8)*100/$nb_notes,1,',','');
			if ($pourcent_i8 != '-') $pourcent_i8 = number_format(round($pourcent_i8*100/$nb_notes,1),1,',','');
			if ($pourcent_se12 != '-') $pourcent_se12 = number_format(round($pourcent_se12*100/$nb_notes,1),1,',','');
		}
		else{
			$pourcent_se8_ie12="-";
			$pourcent_i8='-';
			$pourcent_se12='-';
		}
        }
    }
    if ($test == 1) {
        $col[1][$nb_lignes] = '<b>Moyenne</b>';
        $col[1][$nb_lignes+1] = '<b>Min.</b>';
        $col[1][$nb_lignes+2] = '<b>Max.</b>';
        if ($multiclasses) {
            $col[2][$nb_lignes] = '&nbsp;';
            $col[2][$nb_lignes+1] = '&nbsp;';
            $col[2][$nb_lignes+2] = '&nbsp;';
        }
        $nb_lignes = $nb_lignes + 3;
    }

    //
    // Affichage du tableau
    //
    if (!isset($larg_tab)) {$larg_tab = 680;}
    if (!isset($bord)) {$bord = 1;}
    echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire1\"  target=\"_blank\">\n";
    if ($en_tete == "yes") echo "<p class=bold><a href=\"index1.php?id_groupe=$id_groupe\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <input type=\"submit\" value=\"Visualiser sans l'en-tête\" /></p>\n";
    echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
    echo "<input type='hidden' name='choix_visu' value='yes' />\n";
    if ($stat == "yes") echo "<input type='hidden' name='stat' value='yes' />\n";
    $i="1";
    while ($i < $nb_periode) {
        $name1 = "visu_note_".$i;
        if (isset($_POST[$name1])) {
            $temp1 = $_POST[$name1];
            echo "<input type='hidden' name='$name1' value='$temp1' />\n";
        }
        $name2 = "visu_app_".$i;
        if (isset($_POST[$name2])) {
            $temp2 = $_POST[$name2];
            echo "<input type='hidden' name='$name2' value='$temp2' />\n";
        }
        $i++;
    }
    echo "<input type='hidden' name='en_tete' value='no' />\n";
    echo "<input type='hidden' name='larg_tab' value='$larg_tab' />\n";
    echo "<input type='hidden' name='bord' value='$bord' />\n";
    echo "</form>\n";

    echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire2\">\n";
    if ($en_tete == "yes")
        parametres_tableau($larg_tab, $bord);
//    else echo "<p class=small><a href=\"index1.php?id_classe=$id_classe&choix_matiere=$choix_matiere\">Retour</a>";
    echo "<p class='bold'>" . $_SESSION['nom'] . " " . $_SESSION['prenom'] . " | Année : ".getSettingValue("gepiYear")." | Groupe : " . htmlentities($current_group["description"]) . " (" . $current_group["classlist_string"] . ") | Matière : " . htmlentities($current_group["matiere"]["nom_complet"]);
    echo "</p>\n";
    echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
    echo "<input type='hidden' name='choix_visu' value='yes' />\n";
    if ($stat == "yes") echo "<input type='hidden' name='stat' value='yes' />\n";
    $i="1";
    while ($i < $nb_periode) {
        $name1 = "visu_note_".$i;
        if (isset($_POST[$name1])) {
            $temp1 = $_POST[$name1];
            echo "<input type='hidden' name='$name1' value='$temp1' />\n";
        }
        $name2 = "visu_app_".$i;
        if (isset($_POST[$name2])) {
            $temp2 = $_POST[$name2];
            echo "<input type='hidden' name='$name2' value='$temp2' />\n";
        }
        $i++;
    }
    echo "</form>\n";
//    $appel_donnees_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe='$id_classe' AND c.login = e.login) ORDER BY e.nom, e.prenom");
//    $nombre_eleves = mysql_num_rows($appel_donnees_eleves);

    if (isset($col)) affiche_tableau($nb_lignes, $nb_col, $ligne1, $col, $larg_tab, $bord,0,0,"");
    if ($test == 1 and  $stat == "yes") {
        echo "<br /><table border=\"$bord\" cellpadding=\"5\" cellspacing=\"1\" width=\"$larg_tab\"><tr><td>
        <b>Moyenne générale de la classe : ".$moy_gen."</b>
        <br /><br /><b>Pourcentage des élèves ayant une moyenne générale : </b><ul>\n";
        echo "<li>inférieure strictement à 8 : <b>".$pourcent_i8."</b></li>\n";
        echo "<li>entre 8 et 12 : <b>".$pourcent_se8_ie12."</b></li>\n";
        echo "<li>supérieure ou égale à 12 : <b>".$pourcent_se12."</b></li></ul></td></tr></table>\n";
    }
}
require("../lib/footer.inc.php");
?>