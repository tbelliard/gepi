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

//**************** EN-TETE *****************
$titre_page = "Outil de visualisation | Statistiques de la classe";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

include "../lib/periodes.inc.php";
?>
<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <a href='index.php'>Autre outil de visualisation</a>|
<?php
if (!isset($id_classe)) {
    ?>
    </p><p>Veuillez choisir la classe que vous souhaiter visualiser :<br />
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
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
	}

	if(((getSettingValue("GepiAccesReleveProfToutesClasses")=="yes")&&($_SESSION['statut']=='professeur'))||
		((getSettingValue("GepiAccesReleveScol")=='yes')&&($_SESSION['statut']=='scolarite'))||
		((getSettingValue("GepiAccesReleveCpe")=='yes')&&($_SESSION['statut']=='cpe'))) {
		$sql="SELECT DISTINCT c.* FROM classes c ORDER BY classe";
	}

	$call_classes=mysql_query($sql);
    $nombreligne = mysql_num_rows($call_classes);
    $i = "0" ;
 	$nb_class_par_colonne=round($nombreligne/3);
        //echo "<table width='100%' border='1'>\n";
        echo "<table width='100%'>\n";
        echo "<tr valign='top' align='center'>\n";
        echo "<td align='left'>\n";
   while ($i < $nombreligne) {
        $id_classe = mysql_result($call_classes, $i, "id");
        $l_classe = mysql_result($call_classes, $i, "classe");
	if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
		echo "</td>\n";
		//echo "<td style='padding: 0 10px 0 10px'>\n";
		echo "<td align='left'>\n";
	}
        echo "<a href='stats_classe.php?id_classe=$id_classe#graph'>$l_classe</a><br />\n";
	$i++;
    }
    //echo "</p>";
        echo "</table>\n";
 } else {
    $k="1";
    while ($k < $nb_periode) {
        $datay[$k] = array();
        $k++;
    }
    $etiquette = array();
    $graph_title = "";

    $call_data = mysql_query("SELECT classe FROM classes WHERE id = $id_classe");
    $classe = mysql_result($call_data, 0, "classe");
    ?><a href="stats_classe.php">Choisir une autre classe</a>|</p><?php
    // On appelle les informations de l'utilisateur pour les afficher :
    $graph_title = "Classe de ".$classe.", évolution sur l'année";
    echo "<table  border=1 cellspacing=2 cellpadding=5>";
    echo "<tr><td width='100'>Matière</td>";
    $k = '1';
    while ($k < $nb_periode) {
        echo "<td width='100'>$nom_periode[$k]</td>";
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
            $call_groupes = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.categorie_id ".
            "FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
            "WHERE ( " .
            "jgc.categorie_id = jmcc.categorie_id AND " .
            "jgc.id_classe='".$id_classe."' AND " .
            "jgm.id_groupe=jgc.id_groupe AND " .
            "m.matiere = jgm.id_matiere" .
            ") " .
            "ORDER BY jmcc.priority,jgc.priorite,m.nom_complet");
    } else {
        $call_groupes = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef
        FROM j_groupes_classes jgc, j_groupes_matieres jgm
        WHERE (
        jgc.id_classe='".$id_classe."' AND
        jgm.id_groupe=jgc.id_groupe
        )
        ORDER BY jgc.priorite,jgm.id_matiere");
    }


    $nombre_lignes = mysql_num_rows($call_groupes);

    $i = 0;
    $compteur = 0;
    $prev_cat_id = null;
    while ($i < $nombre_lignes) {
        $group_id = mysql_result($call_groupes, $i, "id_groupe");
        $current_group = get_group($group_id);

        if ($affiche_categories) {
        // On regarde si on change de catégorie de matière
            if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat_id) {
                $prev_cat_id = $current_group["classes"]["classes"][$id_classe]["categorie_id"];
                // On est dans une nouvelle catégorie
                // On récupère les infos nécessaires, et on affiche une ligne
                $cat_name = html_entity_decode_all_version(mysql_result(mysql_query("SELECT nom_complet FROM matieres_categories WHERE id = '" . $current_group["classes"]["classes"][$id_classe]["categorie_id"] . "'"), 0));
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

        echo "<tr><td>" . htmlentities($current_group["description"]) . "</td>\n";
        $k = '1';
        while ($k < $nb_periode) {
            $moyenne_classe_query = mysql_query("SELECT round(avg(note),1) as moyenne FROM matieres_notes WHERE (periode='$k' AND id_groupe='" . $current_group["id"] . "' AND statut ='')");
            $moyenne_classe = mysql_result($moyenne_classe_query, 0, "moyenne");
            if ($moyenne_classe == '') {$moyenne_classe = '-';}
            echo "<td>$moyenne_classe</td>";
            (ereg ("^[0-9\.\,]{1,}$", $moyenne_classe)) ? array_push($datay[$k],"$moyenne_classe") : array_push($datay[$k],"0");
            if ($k == '1') {
                //array_push($etiquette,$current_group["description"]);
                array_push($etiquette,rawurlencode($current_group["description"]));
            }
            $k++;
        }
        $compteur++;
    $i++;
    }
/*    echo "<tr><td>Moyenne générale :</td>";
    $k = '1';
    while ($k < $nb_periode) {
        $moyenne_generale_classe_query = mysql_query("SELECT round(avg(n.note),1) moyenne_generale FROM matieres_notes n, j_eleves_classes c WHERE (c.id_classe='$id_classe' AND c.login = n.login AND n.periode='$k' AND n.statut ='')");
        $moyenne_generale_classe = mysql_result($moyenne_generale_classe_query, 0, "moyenne_generale");
        if ($moyenne_generale_classe == '') {$moyenne_generale_classe = '-';}
        echo "<td>$moyenne_generale_classe</td>";
        $k++;
    }
    echo "</tr>";
*/
    echo "</table>\n";
    echo "<a name=\"graph\"></a>\n";
    $etiq = implode("|", $etiquette);
    $graph_title = urlencode($graph_title);

//    echo "<img src='../".$ver_jpgraph."/view_jpgraph.php?";
    echo "<img src='./draw_artichow1.php?";
    $k = "1";
    while ($k < $nb_periode) {
      $temp=implode("|", $datay[$k]);
      echo "temp".$k."=".$temp."&amp;v_legend".$k."=".urlencode($nom_periode[$k])."&amp;";
      $k++;
    }
    echo "etiquette=$etiq&amp;titre=$graph_title&amp;compteur=$compteur&amp;nb_data=$nb_periode' alt='Statistiques de la classe' />\n";
    echo "<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />&nbsp;";


}
require("../lib/footer.inc.php");
?>