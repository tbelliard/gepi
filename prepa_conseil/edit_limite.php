<?php
/*
 * Last modification  : 08/12/2005
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
@set_time_limit(0);



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
include "../lib/periodes.inc.php";
include "../lib/bulletin_simple.inc.php";
require_once("../lib/header.inc");
$gepiYear = getSettingValue("gepiYear");

if ($periode1 > $periode2) {
  $temp = $periode2;
  $periode2 = $periode1;
  $periode1 = $temp;
}
// On teste la présence d'au moins un coeff pour afficher la colonne des coef
$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));


// On regarde si on affiche les catégories de matières
$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}


// Si le rang des élèves est demandé, on met à jour le champ rang de la table matieres_notes
$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
if ($affiche_rang == 'y') {
    $periode_num=$periode1;
    while ($periode_num < $periode2+1) {
        include "../lib/calcul_rang.inc.php";
        $periode_num++;
    }
}

/*
// On regarde si on affiche les catégories de matières
$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}
*/

if ($choix_edit == '2') {
    bulletin($login_eleve,1,1,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$test_coef,$affiche_categories);
}

if ($choix_edit != '2') {
    if ($choix_edit == '1') {
        $appel_liste_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe='$id_classe' AND e.login = c.login) ORDER BY e.nom,e.prenom");
    } else {
        $appel_liste_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c, j_eleves_professeurs p WHERE (c.id_classe='$id_classe' AND e.login = c.login AND p.login=c.login AND p.professeur='$login_prof') ORDER BY e.nom,e.prenom");
    }
    $nombre_eleves = mysql_num_rows($appel_liste_eleves);
    $i=0;
    $k=0;
    while ($i < $nombre_eleves) {
        $current_eleve_login = mysql_result($appel_liste_eleves, $i, "login");
        $k++;
        bulletin($current_eleve_login,$k,$nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$test_coef,$affiche_categories);
        if ($i != $nombre_eleves-1) {echo "<p class=saut>&nbsp;</p>";}
        $i++;
    }

}
?>

</body>
</html>