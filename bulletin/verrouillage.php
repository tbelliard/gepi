<?php
/*
 * Last modification  : 14/03/2005
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
if (isset($_POST['ok'])) {

   $pb_reg_ver = 'no';
   //$calldata = sql_query("SELECT DISTINCT c.id, c.classe FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
   $calldata = sql_query("SELECT DISTINCT c.id, c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
   if ($calldata) for ($k = 0; ($row = sql_row($calldata, $k)); $k++) {
      $id_classe = $row[0];
      $periode_query = sql_query("SELECT verouiller FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
      $nb_periode = sql_count($periode_query) + 1 ;
      if ($periode_query) for ($i = 0; ($row_per = sql_row($periode_query, $i)); $i++) {
         $nom_classe = "cl_".$id_classe."_".$i;
         $t = $i+1;
         if (isset($_POST[$nom_classe]))  {
            $register = sql_query("UPDATE periodes SET verouiller='".$_POST[$nom_classe]."' WHERE (num_periode='".$t."' and id_classe='".$id_classe."')");
            if (!$register) {$pb_reg_ver = 'yes';}
         }
      }
   }
   if ($pb_reg_ver == 'no') {
      $msg = "Les modifications ont été enregistrées.";
   } else {
      $msg = "Il y a eu un problème lors de l'enregistrement des données.";
   }
}


//**************** EN-TETE **************************************
$titre_page = "Verrouillage et déverrouillage des périodes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************
?>
<script type='text/javascript' language='javascript'>
function CocheCase(rang,per) {
 nbelements = document.formulaire.elements.length;
 for (i=0;i<nbelements;i++) {
   if (document.formulaire.elements[i].type =='hidden') {
     if (document.formulaire.elements[i].value ==per) {
       document.formulaire.elements[i+1].checked = false ;
       document.formulaire.elements[i+2].checked = false ;
       document.formulaire.elements[i+3].checked = false ;
       document.formulaire.elements[i+rang].checked = true ;
      }
   }
 }
}
</script>
<p class=bold>
|<a href="../accueil.php">Retour</a> |
</p>

<?php
$texte_deverrouiller = urlencode("Déverrouiller");
$texte_verrouiller_part = urlencode("Verrouiller part.");
$texte_verrouiller_tot = urlencode("Verrouiller tot.");


// On va chercher les classes déjà existantes, et on les affiche.

$max_per = sql_query1("SELECT num_periode FROM periodes ORDER BY num_periode DESC LIMIT 1");
   //$calldata = sql_query("SELECT DISTINCT c.id, c.classe FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
   $calldata = mysql_query("SELECT DISTINCT c.id, c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
   $nombreligne = sql_count($calldata);
   echo "Total : $nombreligne classes\n";
   echo "<ul>
   <li>Lorsqu'une période est <b>déverrouillée</b>, le remplissage de toutes les rubriques (notes, appréciations, avis) est autorisé, la visualisation des
   bulletins simples est autorisée mais la visualisation et l'impression des bulletins officiels sont impossibles.<br /><br /></li>
   <li>Lorsqu'une période est <b>verrouillée partiellement</b>, seuls le remplissage et/ou la modification
   de l'avis du conseil de classe sont possibles. La visualisation et l'impression des bulletins officiels sont autorisées.<br /><br /></li>
   <li>Lorsqu'une période est <b>verrouillée totalement</b>, le remplissage et la modification du bulletin pour la période concernée
   sont impossibles. la visualisation et l'impression sont autorisées.</li>\n";
   echo "</ul>\n";
   echo "<br /><br />\n";


   if ($nombreligne != 0) {
      echo "<form action=\"verrouillage.php\" name=\"formulaire\" method=\"post\">";

      echo "<table cellpadding='3' cellspacing='0' border='1'>";
      echo "<tr class='fond_sombre'><td>&nbsp;</td>";
      for ($i = 0; $i < $max_per; $i++) echo "<td>
        <a href=\"javascript:CocheCase(1,".$i.")\">Tout déverrouiller</a><br />
        <a href=\"javascript:CocheCase(2,".$i.")\">Tout verrouiller partiellement</a><br />
        <a href=\"javascript:CocheCase(3,".$i.")\">Tout verrouiller  totalement</a>
        </td>
        <td><IMG SRC=\"../lib/create_im_mat.php?texte=".$texte_deverrouiller."&amp;width=22\" WIDTH=\"22\" BORDER=0 alt=\"Déverrouiller\" /></td>
        <td><IMG SRC=\"../lib/create_im_mat.php?texte=".$texte_verrouiller_part."&amp;width=22\" WIDTH=\"22\" BORDER=0 alt=\"Verrouiller partiellement\" /></td>
        <td><IMG SRC=\"../lib/create_im_mat.php?texte=".$texte_verrouiller_tot."&amp;width=22\" WIDTH=\"22\" BORDER=0 alt=\"Verrouiller totalement\" /></td>";
      echo "</tr>\n";
      $flag = 0;
      if ($calldata) for ($k = 0; ($row = sql_row($calldata, $k)); $k++) {
          $id_classe = $row[0];
          $classe = $row[1];
          echo "<tr";
          if ($flag==1) { echo " class='fond_sombre'"; $flag = 0;} else {$flag=1;};
          echo "><td><b>$classe</b> ";
          echo "</td>";

          $periode_query = sql_query("SELECT nom_periode, verouiller FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
          $nb_periode = sql_count($periode_query) + 1 ;
          $j = 0;
          if ($periode_query) for ($i = 0; ($row_per = sql_row($periode_query, $i)); $i++) {
             $nom_classe = "cl_".$id_classe."_".$i;
             echo "<td>".ucfirst($row_per[0])."</td>\n";
             //echo "<input type=\"hidden\" name=\"numperiode\" value=\"$i\" />";
             echo "<td><input type=\"hidden\" name=\"numperiode\" value=\"$i\" />";
             //echo "<td><input type=\"radio\" name=\"".$nom_classe."\" value=\"N\" ";
             echo "<input type=\"radio\" name=\"".$nom_classe."\" value=\"N\" ";
             if ($row_per[1] == "N") echo "checked";
             echo " /></td>\n";
             echo "<td><input type=\"radio\" name=\"".$nom_classe."\" value=\"P\" ";
             if ($row_per[1] == "P") echo "checked";
             echo " /></td>\n";
             echo "<td><input type=\"radio\" name=\"".$nom_classe."\" value=\"O\" ";
             if ($row_per[1] == "O") echo "checked";
             echo " /></td>\n";
             $j++;
          }
          for ($i = $j; $i < $max_per; $i++) echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>\n";

          echo "</tr>\n";
      }
      echo "</table>\n";
      echo "<center><input type=\"submit\" name=\"ok\" value=\"Enregistrer\" /></center>\n";
      echo "</form>\n";
   } else {
      echo "<p class='grand'>Attention : aucune classe n'a été définie dans la base GEPI !</p>\n";
   }


?>
</body>
</html>