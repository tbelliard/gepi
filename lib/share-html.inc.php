<?php
/**Fonctions créant du html
 * 
 * $Id$
 * 
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Initialisation
 * @subpackage html
 *
*/

/**
 * Construit du html pour les cahiers de textes
 *
 * @deprecated La requête SQL n'est plus valide
 * @param string $link Le lien
 * @param <type> $current_classe
 * @param <type> $current_matiere
 * @param integer $year année
 * @param integer $month le mois
 * @param integer $day le jour
 * @return string echo résultat
 */
function make_area_list_html($link, $current_classe, $current_matiere, $year, $month, $day) {
  echo "<strong><em>Cahier&nbsp;de&nbsp;texte&nbsp;de&nbsp;:</em></strong><br />";
  $appel_donnees = mysql_query("SELECT * FROM classes ORDER BY classe");
  $lignes = mysql_num_rows($appel_donnees);
  $i = 0;
  while($i < $lignes){
    $id_classe = mysql_result($appel_donnees, $i, "id");
    $appel_mat = mysql_query("SELECT DISTINCT m.* FROM matieres m, j_classes_matieres_professeurs j WHERE (j.id_classe='$id_classe' AND j.id_matiere=m.matiere) ORDER BY m.nom_complet");
    $nb_mat = mysql_num_rows($appel_mat);
    $j = 0;
    while($j < $nb_mat){
      $flag2 = "no";
      $matiere_nom = mysql_result($appel_mat, $j, "nom_complet");
      $matiere_nom_court = mysql_result($appel_mat, $j, "matiere");
      $id_matiere = mysql_result($appel_mat, $j, "matiere");
      $call_profs = mysql_query("SELECT * FROM j_classes_matieres_professeurs WHERE ( id_classe='$id_classe' and id_matiere = '$id_matiere') ORDER BY ordre_prof");
      $nombre_profs = mysql_num_rows($call_profs);
      $k = 0;
      while ($k < $nombre_profs) {
        $temp = strtoupper(@mysql_result($call_profs, $k, "id_professeur"));
        if ($temp == $_SESSION['login']) {$flag2 = "yes";}
        $k++;
      }
      if ($flag2 == "yes") {
        echo "<strong>";
        $display_class = mysql_result($appel_donnees, $i, "classe");
        if (($id_classe == $current_classe) and ($id_matiere == $current_matiere)) {
           echo ">$display_class&nbsp;-&nbsp;$matiere_nom&nbsp;($matiere_nom_court)&nbsp;";
        } else {
       echo "<a href=\"".$link."?id_classe=$id_classe&amp;id_matiere=$id_matiere&amp;year=$year&amp;month=$month&amp;day=$day\">$display_class&nbsp;-&nbsp;$matiere_nom&nbsp;($matiere_nom_court)</a>";

        }
        echo "</strong><br />";
      }
      $j++;
    }
    $i++;
  }
}

?>
