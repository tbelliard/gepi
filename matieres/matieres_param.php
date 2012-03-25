<?php
/*
 * $Id$
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
}


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();

}
$periode_query = mysql_query("select max(num_periode) max from periodes");
$max_periode = mysql_result($periode_query, 0, 'max');

// On dresse la liste de toutes les classes non virtuelles
$classes_list = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe");
$nb_classe = mysql_num_rows($classes_list);

// On va chercher les matières existantes
$matieres_list = mysql_query("SELECT * FROM matieres ORDER BY matiere");
$nb_matieres = mysql_num_rows($matieres_list);


if (isset($_POST['is_posted'])) {
	check_token();

    $msg = '';
    $reg_ok = '';
    $nbc = 0;
    while ($nbc < $nb_classe) {
        $id_classe = mysql_result($classes_list,$nbc,'id');
        $temp = "case_".$id_classe;
        if (isset($_POST[$temp])) {
            // boucle sur les matières
            $i = 0;
            while ($i < $nb_matieres) {
                $current_matiere = @mysql_result($matieres_list, $i, "matiere");
                if (isset($_POST[$current_matiere.'_priorite']) and ($_POST[$current_matiere.'_priorite']!='')) {
        //=============================
        // MODIF: boireaus
/*
                    $reg_data = mysql_query("UPDATE j_groupes_classes jgc, j_groupes_matieres jgm
                    SET jgc.priorite='".$_POST[$current_matiere.'_priorite']."'
                    where
                    (jgc.id_classe='".$id_classe."' and jgc.id_groupe = jgm.id_groupe and jgm.id_matiere='".$current_matiere."')
                    ");
*/
            $sql="UPDATE j_groupes_classes jgc, j_groupes_matieres jgm
                    SET jgc.priorite='".$_POST[$current_matiere.'_priorite']."'
                    where
                    (jgc.id_classe='".$id_classe."' and jgc.id_groupe = jgm.id_groupe and jgm.id_matiere='".$current_matiere."')
                    ";
            //echo "$sql<br />\n";
            // BIZARRE: Il ajoute 10 ???
                    $reg_data = mysql_query($sql);
        //=============================
                    if (!$reg_data) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                }

                // La catégorie de matière
                if (isset($_POST[$current_matiere.'_categorie']) and ($_POST[$current_matiere.'_categorie']!='') and is_numeric($_POST[$current_matiere.'_categorie'])) {
                $sql="UPDATE j_groupes_classes jgc, j_groupes_matieres jgm
                    SET jgc.categorie_id='".$_POST[$current_matiere.'_categorie']."'
                    where
                    (jgc.id_classe='".$id_classe."' and jgc.id_groupe = jgm.id_groupe and jgm.id_matiere='".$current_matiere."')
                    ";
                    $reg_data = mysql_query($sql);
                    if (!$reg_data) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                }

                // Le coef
                if (isset($_POST[$current_matiere.'_coef']) and ($_POST[$current_matiere.'_coef']!='')) {
                    $reg_data = mysql_query("UPDATE j_groupes_classes jgc, j_groupes_matieres jgm, groupes g
                    SET jgc.coef='".$_POST[$current_matiere.'_coef']."' , g.recalcul_rang='y'
                    where
                    (jgc.id_classe='".$id_classe."' and jgc.id_groupe = jgm.id_groupe and g.id = jgm.id_groupe and jgm.id_matiere='".$current_matiere."')
                    ");
                    if (!$reg_data) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                }
                $i++;
           }
        }
        $nbc++;
    }
    if ($reg_ok=='') {
        $message_enregistrement = "Aucune modification n'a été effectuée !";
        $affiche_message = 'yes';
    } else if ($reg_ok=='yes') {
        $message_enregistrement = "Les modifications ont été effectuées avec succès.";
        $affiche_message = 'yes';
    } else {
        $message_enregistrement = "Il y a eu un problème lors de l'enregistrement des modification.";
        $affiche_message = 'yes';
    }
}
//**************** EN-TETE *****************
$titre_page = "Outil de gestion - Paramétrage des matières par lots";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

if ($max_periode <= 0) {
   echo "Aucune classe comportant des périodes n'a été définie.";
   die();
}

echo "<form method=\"post\" action=\"matieres_param.php\">\n";
echo add_token_field();
echo "<p class=bold><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <input type='submit' value='Enregistrer' /></p>\n";
echo "<p>Sur cette page, vous pouvez modifier les coefficients et les priorités d'affichage d'une ou plusieurs matières
par lots de classes cochées ci-dessous.</p>\n";

//================================================
// AJOUT:boireaus
echo "<p><input type='button' name='cochetout' value='Tout cocher' onClick='coche_tout();' /> / <input type='button' name='decochetout' value='Tout décocher' onClick='decoche_tout();' /></p>";
//============================================


$nbc = 0;
while ($nbc < $nb_classe) {
    $tab_id_classe[$nbc] = mysql_result($classes_list,$nbc,'id');;
    $tab_nom_classe[$nbc] = mysql_result($classes_list,$nbc,'classe');
    $nbc++;
}
$nb_ligne = intval($nb_classe/3);
if ($nb_ligne*3 < $nb_classe) $nb_ligne++;
//echo "<table width = 100% border=1>\n";
echo "<table width='100%' border='1' class='boireaus' summary='Choix des classes'>\n";
$i ='0';
//============================================
// AJOUT: boireaus
$cpt_classe=0;
$alt=1;
//============================================
while ($i < $nb_ligne) {
	$alt=$alt*(-1);
    echo "<tr class='lig$alt white_hover'>\n";
    $j = 0;
    while ($j < 3) {
        unset($nom_case);
        $nom_classe = '';
        if (isset($tab_id_classe[$i+$j*$nb_ligne])) $nom_case = "case_".$tab_id_classe[$i+$j*$nb_ligne];
        if (isset($tab_nom_classe[$i+$j*$nb_ligne])) $nom_classe = $tab_nom_classe[$i+$j*$nb_ligne];
        echo "<td>";
    //============================================
    // MODIF: boireaus
        //if ($nom_classe != '') echo "<input type=\"checkbox\" name=\"".$nom_case."\" />&nbsp;".$nom_classe;
        if ($nom_classe != ''){
        echo "<input type=\"checkbox\" name=\"".$nom_case."\" id=\"classe_num".$cpt_classe."\" onchange='change_style_classe($cpt_classe)' /><label id=\"label_classe_num".$cpt_classe."\" for='classe_num".$cpt_classe."'>&nbsp;".$nom_classe."</label>";
        $cpt_classe++;
    }
    //============================================
        echo "</td>\n";
        $j++;
    }
    echo "</tr>\n";
    $i++;
}
echo "</table>\n";


//============================================
// AJOUT: boireaus
echo "<script type='text/javascript' language='javascript'>
	function change_style_classe(num) {
		if(document.getElementById('classe_num'+num)) {
			if(document.getElementById('classe_num'+num).checked) {
				document.getElementById('label_classe_num'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_classe_num'+num).style.fontWeight='normal';
			}
		}
	}

    function coche_tout(){
        cpt=0;
        while(cpt<$cpt_classe){
            document.getElementById('classe_num'+cpt).checked=true;
			change_style_classe(cpt);
            cpt++;
        }
    }

    function decoche_tout(){
        cpt=0;
        while(cpt<$cpt_classe){
            document.getElementById('classe_num'+cpt).checked=false;
			change_style_classe(cpt);
            cpt++;
        }
    }
</script>\n";
//============================================

?>
<p class='bold'>Pour la ou les classe(s) sélectionnée(s) ci-dessus : </p>
<p>Remarque : Aucune modification n'est apportée aux champs laissés vides.</p>

<table width='100%' border='1' cellpadding='5' class='boireaus' summary='Paramétrage des matières'>
<tr>
    <th><p class='bold'>Identifiant matière</p></th>
    <th><p class='bold'>Nom complet</p></th>
    <th><p class='bold'>Ordre d'affichage</p></th>
    <th><p class='bold'>Coefficient</p></th>
    <th><p class='bold'>Catégorie</p></th>
</tr>

<?php
$i = 0;
$alt=1;
while ($i < $nb_matieres){
    $current_matiere = @mysql_result($matieres_list, $i, "matiere");
    $current_matiere_nom = @mysql_result($matieres_list, $i, "nom_complet");
    $matquery = mysql_query("select 1=1 from j_groupes_matieres jgm, j_groupes_classes jgc, classes c
    where (
    c.id = jgc.id_classe and
    jgc.id_groupe = jgm.id_groupe and
    jgm.id_matiere = '".$current_matiere."'
    )");
    $nb_mat = mysql_num_rows($matquery);
    if ($nb_mat != 0) {
		$alt=$alt*(-1);
        echo "<tr class='lig$alt white_hover'><td>$current_matiere</td>\n";
        //echo "<td>$current_matiere_nom</td>\n";
        echo "<td>".htmlspecialchars($current_matiere_nom)."</td>\n";
        echo "<td>\n";
        echo "<select size=1 name=".$current_matiere."_priorite>\n";
        $k = '0';
        //echo "<option value=''></option>";
        echo "<option value=''>---</option>\n";
        echo "<option value=0>0</option>\n";
        $k='11';
        $j = '1';
        while ($k < '51'){
            echo "<option value=$k>$j</option>\n";
            $k++;
            $j = $k - 10;
        }
        echo "</select>\n";
        echo "</td>\n";
        echo "<td><input type=\"text\" name=\"".$current_matiere."_coef\" value=\"\" size=\"5\" /></td>\n";

        // Catégorie de matière
        echo "<td>";
        echo "<select size=1 name=\"".$current_matiere."_categorie\">\n";
        $get_cat = mysql_query("SELECT id, nom_court FROM matieres_categories");

        echo "<option value=''>-----</option>";
        while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
            echo "<option value='".$row["id"]."'>".html_entity_decode($row["nom_court"])."</option>";
        }
        echo "</select>";
        echo "</td>";
        echo "</tr>\n";
    }
$i++;
}
?>
</table>
<center><input type='submit' value='Enregistrer' /></center>
<input type='hidden' name='is_posted' value="yes" />

<?php
/*$i = 0;
while ($i < $nb_matieres){
    $current_matiere = @mysql_result($matieres_list, $i, "matiere");
    $current_matiere_nom = @mysql_result($matieres_list, $i, "nom_complet");
        echo "<DIV ID=\"".$current_matiere."\" STYLE=\"position:absolute; visibility: hidden; left: 300px; top: 10px;\">";
    echo "<table width=\"200\" border=\"1\" cols=\"1\" cellpadding=\"1\" cellspacing=\"1\" bgcolor=\"#FFFFFF\">";
    echo "<tr><td>
    <i><b>".$current_matiere_nom." - Matière présente dans les classes suivantes :</b></i>
    </td></tr>";

    $matquery = mysql_query("select c.classe from j_classes_matieres_professeurs j, classes c
    where (
    c.id = j.id_classe and
    j.id_matiere = '".$current_matiere."'
    )");
    $nb_mat = mysql_num_rows($matquery);
    $k = 0;
    while ($k < $nb_mat) {
        $classe = mysql_result($matquery, $k, 'classe');
        echo "<tr><td>".$classe."</td></tr>";
        $k++;
    }

    echo "</table>
    </DIV>";

    $i++;
}
*/
?>

</FORM>
<?php require("../lib/footer.inc.php");?>