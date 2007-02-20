<?php

function make_classes_select_html($link, $current, $year, $month, $day)

{
  $out_html = "<form name=\"classe\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\"><b><i>Classe :</i></b><br />
  <select name=\"classe\" onChange=\"classe_go()\">";
  $out_html .= "<option value=\"".$link."?year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;id_classe=-1\">(Choisissez une classe)";
  // Ligne suivante corrigée sur suggestion tout à fait pertinente de Stéphane, mail du 1er septembre 06
  $sql = "select DISTINCT c.id, c.classe from classes c, j_groupes_classes jgc, ct_entry ct WHERE (c.id = jgc.id_classe and jgc.id_groupe = ct.id_groupe) order by classe";

  $res = sql_query($sql);
  if ($res) for ($i = 0; ($row = sql_row($res, $i)); $i++)
  {
    $selected = ($row[0] == $current) ? "selected" : "";
    $link2 = "$link?year=$year&amp;month=$month&amp;day=$day&amp;id_classe=$row[0]";
    $out_html .= "<option $selected value=\"$link2\">" . htmlspecialchars($row[1]);
  }
  $out_html .= "</select>
  <script type=\"text/javascript\">
  <!--
  function classe_go()
  {
  box = document.forms[\"classe\"].classe;
  destination = box.options[box.selectedIndex].value;
  if (destination) location.href = destination;
  }
  // -->
  </SCRIPT>
  <noscript>
  <input type=submit value=\"OK\" />
  </noscript>
  </form>";
  return $out_html;
}

function make_matiere_select_html( $link, $id_classe, $current, $year, $month, $day)
{
  $out_html = "<form name=\"matiere\"  method=\"post\" action=\"".$_SERVER['PHP_SELF']."\"><b><i>Matière :</i></b><br />
  <select name=\"matiere\" onChange=\"matiere_go()\">";
  $out_html .= "<option value=\"".$link."?&amp;year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;id_classe=$id_classe\">(Choisissez un enseignement)";
  $sql = "select DISTINCT g.id, g.name, g.description from j_groupes_classes jgc, groupes g, ct_entry ct where (" .
        "jgc.id_classe='".$id_classe."' and " .
        "g.id = jgc.id_groupe and " .
        "jgc.id_groupe = ct.id_groupe" .
        ") order by g.name";
  $res = sql_query($sql);
  if ($res) for ($i = 0; ($row = sql_row($res, $i)); $i++)
  {
   $test_prof = "SELECT nom, prenom FROM j_groupes_professeurs j, utilisateurs u WHERE (j.id_groupe='".$row[0]."' and u.login=j.login) ORDER BY nom, prenom";
   $res_prof = sql_query($test_prof);
   $chaine = "";
   for ($k=0;$prof=sql_row($res_prof,$k);$k++) {
     if ($k != 0) $chaine .= ", ";
     $chaine .= htmlspecialchars($prof[0])." ".substr(htmlspecialchars($prof[1]),0,1).".";
   }
   //$chaine .= ")";


   $selected = ($row[0] == $current) ? "selected" : "";
   $link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;id_classe=$id_classe&amp;id_groupe=$row[0]";
   $out_html .= "<option $selected value=\"$link2\">" . htmlspecialchars($row[2] . " - ")." ".$chaine;
   }
  $out_html .= "</select>
  <script type=\"text/javascript\">
  <!--
  function matiere_go()
  {
    box = document.forms[\"matiere\"].matiere;
    destination = box.options[box.selectedIndex].value;
    if (destination) location.href = destination;
  }
  // -->
  </SCRIPT>

  <noscript>
  <input type=submit value=\"OK\" />
  </noscript>
  </form>";

  return $out_html;
}

function affiche_docs_joints($id_ct,$type_notice) {
// documents joints
$html = '';
$architecture="/documents/cl_dev";
if ($type_notice == "t")
    $sql = "SELECT titre, emplacement FROM ct_documents WHERE id_ct='$id_ct' AND emplacement LIKE '%".$architecture."%'  ORDER BY 'titre'";
else
    $sql = "SELECT titre, emplacement FROM ct_documents WHERE id_ct='$id_ct' AND emplacement NOT LIKE '%".$architecture."%'  ORDER BY 'titre'";

$res = sql_query($sql);
  if (($res) and (sql_count($res)!=0)) {
    $html .= "<small style=\"font-weight: bold;\">Document(s) joint(s):</small>";
    $html .= "<ul type=\"disc\" style=\"padding-left: 15px;\">";
    for ($i=0; ($row = sql_row($res,$i)); $i++) {
              $titre = $row[0];
              $emplacement = $row[1];
              $html .= "<li style=\"padding: 0px; margin: 0px; font-family: arial, sans-serif; font-size: 80%;\"><a href=\"$emplacement\" target=\"blank\">$titre</a></li>";
    }
    $html .= "</ul>";
   }
  return $html;
 }
?>