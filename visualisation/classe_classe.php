<?php
/*
 * $Id$
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Resume session
$resultat_session = $session_gepi->security_check();
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

//**************** EN-TETE *****************
$titre_page = "Outil de visualisation | Comparaison d'évolution de deux classes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$id_classe2 = isset($_POST['id_classe2']) ? $_POST['id_classe2'] : (isset($_GET['id_classe2']) ? $_GET['id_classe2'] : NULL);
if((isset($id_classe))&&($id_classe!='')) {include "../lib/periodes.inc.php";}

?>
<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/>Retour accueil</a> | <a href='index.php'>Autre outil de visualisation</a>
<?php
if ((!isset($id_classe)) or ($id_classe=='')) {
    ?>
    </p><p>Veuillez sélectionner les classes que vous souhaitez visualiser :<br />
    <?php
    //$call_classes = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
    //$call_classes = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");

	if($_SESSION['statut']=='scolarite'){
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur'){
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
	}
	elseif($_SESSION['statut']=='cpe'){
		/*
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
		*/
		// Les cpe ont accès à tous les bulletins, donc aussi aux courbes
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}

	if(((getSettingValue("GepiAccesReleveProfToutesClasses")=="yes")&&($_SESSION['statut']=='professeur'))||
		((getSettingValue("GepiAccesReleveScol")=='yes')&&($_SESSION['statut']=='scolarite'))) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}
	/*
	if(((getSettingValue("GepiAccesReleveProfToutesClasses")=="yes")&&($_SESSION['statut']=='professeur'))||
		((getSettingValue("GepiAccesReleveScol")=='yes')&&($_SESSION['statut']=='scolarite'))||
		((getSettingValue("GepiAccesReleveCpeTousEleves")=='yes')&&($_SESSION['statut']=='cpe'))) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}
	elseif((getSettingValue("GepiAccesReleveCpe")=='yes')&&($_SESSION['statut']=='cpe')) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
	}
	*/

	$call_classes=mysqli_query($GLOBALS["mysqli"], $sql);
    $nombreligne = mysqli_num_rows($call_classes);

    echo "<form enctype='multipart/form-data' action='classe_classe.php#graph' method='post'>\n";
    echo "<p>Classe n°1 :</p>\n";
    echo "<select name='id_classe' size='1'>\n";
    $i = "0" ;
    while ($i < $nombreligne) {
        $id_classe = mysql_result($call_classes, $i, "id");
        $l_classe = mysql_result($call_classes, $i, "classe");
        //echo "<option value='$id_classe' size='1'>$l_classe</option>\n";
        echo "<option value='$id_classe'>$l_classe</option>\n";
    $i++;
    }
    echo "</select>";
    echo "<p>Classe n°2 :</p>";
    echo "<p><select name='id_classe2' size='1'>";
    $i = "0" ;
    while ($i < $nombreligne) {
        $id_classe = mysql_result($call_classes, $i, "id");
        $l_classe = mysql_result($call_classes, $i, "classe");
        //echo "<option value='$id_classe' size='1'>$l_classe</option>\n";
        echo "<option value='$id_classe'>$l_classe</option>\n";
    $i++;
    }
    echo "</select><p>\n";
    echo "<input type='submit' value='Ok' /></form>\n";

//***************************************************************************************************
} else {
    $k="1";
    while ($k < $nb_periode) {
     $datay1[$k] = array();
      $datay2[$k] = array();
     $k++;
    }
    $etiquette = array();
    $graph_title = "";


    $call_classe = mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM classes WHERE id = '$id_classe'");
    $classe = mysql_result($call_classe, "0", "classe");
    $call_classe = mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM classes WHERE id = '$id_classe2'");
    $classe2 = mysql_result($call_classe, "0", "classe");

    ?> | <a href="classe_classe.php?id_classe=">Choix des classes</a></p><?php
    // On appelle les informations de l'utilisateur pour les afficher :
    $graph_title = "Comparaison des classes de ".$classe." et ".$classe2;
    $v_legend1 = $classe ;
    $v_legend2 = $classe2 ;
    echo "<table  border='1' cellspacing='2' cellpadding='5'>\n";
    echo "<tr><td width='100'>Matière</td>\n";
    $k = '1';
    while ($k < $nb_periode) {
        echo "<td width='100'>$nom_periode[$k] $classe</td><td width='100'><p>$nom_periode[$k] $classe2</p></td>\n";
    $k++;
    }
    echo "</tr>";

    $affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
    if ($affiche_categories == "y") {
        $affiche_categories = true;
    } else {
        $affiche_categories = false;
    }

    if ($affiche_categories) {
        // On utilise les valeurs spécifiées pour la classe en question
        $sql="SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.categorie_id ".
        "FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
        "WHERE ( " .
        "jgc.categorie_id = jmcc.categorie_id AND " .
        "jgc.id_classe='".$id_classe."' AND " .
        "jgm.id_groupe=jgc.id_groupe AND " .
        "m.matiere = jgm.id_matiere" .
		" AND jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n')".
        ") " .
        "ORDER BY jmcc.priority,jgc.priorite,m.nom_complet";
    } else {
        $sql="SELECT DISTINCT jgc.id_groupe, jgc.coef
        FROM j_groupes_classes jgc, j_groupes_matieres jgm
        WHERE (
        jgc.id_classe='".$id_classe."' AND
        jgm.id_groupe=jgc.id_groupe
		AND jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n')
        )
        ORDER BY jgc.priorite,jgm.id_matiere";
    }
    //echo "$sql<br />";
    $call_groupes = mysqli_query($GLOBALS["mysqli"], $sql);

    $nombre_lignes = mysqli_num_rows($call_groupes);

    $i = 0;
    $compteur = 0;
    $prev_cat_id = null;
    while ($i < $nombre_lignes) {

        $group_id = mysql_result($call_groupes, $i, "id_groupe");
        //echo "\$group_id=$group_id<br />";
        $current_group = get_group($group_id);

        // On essaie maintenant de récupérer un groupe avec la même matière, lié à la seconde classe
        $call_group2 = mysqli_query($GLOBALS["mysqli"], "SELECT distinct(jgc.id_groupe) id_groupe, g.description FROM j_groupes_classes jgc, j_groupes_matieres jgm, groupes g WHERE (" .
                "g.id = jgc.id_groupe AND " .
                "jgc.id_classe = '" . $id_classe2 . "' and " .
                "jgc.id_groupe = jgm.id_groupe and " .
                "jgm.id_matiere = '" . $current_group["matiere"]["matiere"] . "')");

        if (mysqli_num_rows($call_group2) == 1) {
            $group2_id = mysql_result($call_group2, 0, "id_groupe");
            $current_group2 = get_group($group2_id);
        } elseif (mysqli_num_rows($call_group2) > 1) {
            while ($row = mysqli_fetch_object($call_group2)) {
                if ($row->description == $current_group["description"]) {
                    //echo "\$row->description=".$row->description."<br />";
                    //echo "\$row->id=".$row->id."<br />";
                    //echo "\$row->id_groupe=".$row->id_groupe."<br />";
                    //$current_group2 = get_group($row->id);
                    $current_group2 = get_group($row->id_groupe);
                    break;
                } else {
                    $current_group2 = false;
                }
            }
        } else {
            $current_group2 = false;
        }

        if ($current_group2) {

            if ($affiche_categories) {
            // On regarde si on change de catégorie de matière
                if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat_id) {
                    $prev_cat_id = $current_group["classes"]["classes"][$id_classe]["categorie_id"];
                    // On est dans une nouvelle catégorie
                    // On récupère les infos nécessaires, et on affiche une ligne
                    //$cat_name = html_entity_decode(mysql_result(mysql_query("SELECT nom_complet FROM matieres_categories WHERE id = '" . $current_group["classes"]["classes"][$id_classe]["categorie_id"] . "'"), 0));
                    $cat_name = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT nom_complet FROM matieres_categories WHERE id = '" . $current_group["classes"]["classes"][$id_classe]["categorie_id"] . "'"), 0);
                    // On détermine le nombre de colonnes pour le colspan
                    $nb_total_cols = 1;
                    $k = '1';
                    while ($k < $nb_periode) {
                        $nb_total_cols++;
                        $k++;
                    }
                    // On a toutes les infos. On affiche !
                    echo "<tr>";
                    echo "<td colspan='" . $nb_total_cols . "'>";
                    echo "<p style='padding: 5; margin:0; font-size: 15px;'>".$cat_name."</p></td>";
                    echo "</tr>";
                }
            }


            echo "<tr><td>" . htmlspecialchars($current_group["matiere"]["nom_complet"]) . "</td>\n";
            $k = '1';
            while ($k < $nb_periode) {
                $moyenne_classe_query = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(note),1) as moyenne FROM matieres_notes WHERE (periode='$k' AND id_groupe='" . $current_group["id"] . "' AND statut = '')");
                $moyenne_classe = mysql_result($moyenne_classe_query, 0, "moyenne");
                $moyenne_classe2_query = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(note),1) as moyenne FROM matieres_notes WHERE (periode='$k' AND id_groupe='" . $current_group2["id"] . "' AND statut = '')");
                $moyenne_classe2 = mysql_result($moyenne_classe2_query, 0, "moyenne");
                if ($moyenne_classe == '') {$moyenne_classe = '-';}
                if ($moyenne_classe2 == '') {$moyenne_classe2 = '-';}
                echo "<td>$moyenne_classe</td><td>$moyenne_classe2</td>\n";
                (my_ereg ("^[0-9\.\,]{1,}$", $moyenne_classe)) ? array_push($datay1[$k],"$moyenne_classe") : array_push($datay1[$k],"0");
                (my_ereg ("^[0-9\.\,]{1,}$", $moyenne_classe2)) ? array_push($datay2[$k],"$moyenne_classe2") : array_push($datay2[$k],"0");
                if ($k == '1') {
                    //array_push($etiquette,$current_group["matiere"]["nom_complet"]);
                    array_push($etiquette,rawurlencode($current_group["matiere"]["nom_complet"]));
                }
                $compteur++;
            $k++;
            }
        }
    $i++;
    }
    echo "</table>\n";
    echo "<a name=\"graph\"></a>\n";
    echo "<p class='bold'>|<a href='../accueil.php'>Accueil</a>|<a href='index.php'>Autre outil de visualisation</a>|<a href='classe_classe.php?id_classe='>Choix des classes</a>|</p>\n";
    $etiq = implode("|", $etiquette);
    $graph_title = urlencode($graph_title);
    $v_legend1 = urlencode($classe);
    $v_legend2 = urlencode($classe2);

    echo "<img src='draw_artichow2.php?";
    $k = "1";
    while ($k < $nb_periode) {
      $temp1=implode("|", $datay1[$k]);
      $temp2=implode("|", $datay2[$k]);
      echo "temp1".$k."=".$temp1."&amp;temp2".$k."=".$temp2."&amp;";
      $k++;
    }
    echo "&amp;v_legend1=".$v_legend1."&amp;v_legend2=".$v_legend2."&amp;etiquette=$etiq&amp;titre=$graph_title&amp;compteur=$compteur&amp;nb_data=$nb_periode' alt='Graphes comparés de deux classes' />\n";
    echo "<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />\n";
}

//===========================================================
echo "<p><em>NOTE&nbsp;:</em></p>\n";
require("../lib/textes.inc.php");
echo "<p style='margin-left: 3em;'>$explication_bulletin_ou_graphe_vide</p>\n";
//===========================================================

require("../lib/footer.inc.php");
?>
