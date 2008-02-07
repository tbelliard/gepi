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

/*
Portion de code qui affiche toutes les fiches, utilisé à la fois dans l'interface publique et dans l'interface privée
 Variables requises :
$indice_aid
$_login -> laisser vide s'il s'agit de l'interface publqiue
$message_avertissement -> message "en construction
$non_defini -> ce qui s'affiche si le champ n'est pas rempli
*/

// Initialisation des variables
$call_productions = mysql_query("select * from aid_productions order by nom");
$nb_productions = mysql_num_rows($call_productions);
$call_public = mysql_query("select * from aid_public order by public");
$nb_public = mysql_num_rows($call_public );


$requete = "SELECT * FROM aid WHERE indice_aid='$indice_aid' ";
// S'il s'agit de l'interface publique, on restreint aux fiches publiques
if ($_login=="")
    $requete .= " and fiche_publique='y'";
$requete .= " ORDER BY nom";
$calldata = mysql_query($requete);
$nombreligne = mysql_num_rows($calldata);

echo "Cliquez sur le symbole <img src=\"../images/plier.png\" alt=\"Plus de détails\" Title=\"Plus de détails\" style=\"vertical-align: middle;\"/> devant chaque projet pour afficher ou cacher les détails du projet. Vous pouvez aussi ";
echo "<a href=\"#\" style=\"background-color:#FF8543; color:white; font-size:130%; font-family:serif\" onclick=\"javascript:";
$i = 0;
while ($i < $nombreligne){
    $aid_id = @mysql_result($calldata, $i, "id");
    echo "Element.show('id_".$aid_id."');";
    $i++;
}
echo "\" > afficher</a>";
echo " ou ";
echo "<a href=\"#\" style=\"background-color:#FF8543; color:white; font-size:130%; font-family:serif\" onclick=\"javascript:";
$i = 0;
while ($i < $nombreligne){
    $aid_id = @mysql_result($calldata, $i, "id");
    echo "Element.hide('id_".$aid_id."');";
    $i++;
}
echo "\" > cacher </a>";
echo " tous les détails.<br />\n";
if ($_login!="")
    echo "Selon le paramétrage effectué par l'administrateur, certaines de ces fiches sont en partie <a href=\"../public/index_fiches.php\" target=\"_blank\">librement consultables et accessibles au public</a>.<br />";
echo "<br />";
$i = 0;
while ($i < $nombreligne){
    $aid_nom = @mysql_result($calldata, $i, "nom");
    $aid_num = @mysql_result($calldata, $i, "numero");
    if ($aid_num =='') {$aid_num='&nbsp;';}
    $aid_id = @mysql_result($calldata, $i, "id");
    // autres champs :
    $call_data_projet = mysql_query("select * from aid where (id = '$aid_id' and indice_aid='$indice_aid')");

    $ressources = @mysql_result($call_data_projet,0,"ressources");
    $resume = @mysql_result($call_data_projet,0,"resume");
    $discipline1 = @mysql_result($call_data_projet,0,"matiere1");
    $discipline2 = @mysql_result($call_data_projet,0,"matiere2");
    $famille = @mysql_result($call_data_projet,0,"famille");
    $adresse1 = @mysql_result($call_data_projet,0,"adresse1");
    $adresse2 = @mysql_result($call_data_projet,0,"adresse2");
    $productions = @mysql_result($call_data_projet,0,"productions");
    $public = @mysql_result($call_data_projet,0,"public_destinataire");
    $mots_cles = @mysql_result($call_data_projet,0,"mots_cles");
    $divers = @mysql_result($call_data_projet,0,"divers");
    $perso1 = @mysql_result($call_data_projet,0,"perso1");
    $perso2 = @mysql_result($call_data_projet,0,"perso2");
    $perso3 = @mysql_result($call_data_projet,0,"perso3");
    $contacts = @mysql_result($call_data_projet,0,"contacts");
    $eleve_peut_modifier = @mysql_result($call_data_projet,0,"eleve_peut_modifier");
    $prof_peut_modifier = @mysql_result($call_data_projet,0,"prof_peut_modifier");
    $cpe_peut_modifier = @mysql_result($call_data_projet,0,"cpe_peut_modifier");
    $fiche_publique = @mysql_result($call_data_projet,0,"fiche_publique");
    $affiche_adresse1 = @mysql_result($call_data_projet,0,"affiche_adresse1");
    $en_construction = @mysql_result($call_data_projet,0,"en_construction");

    echo "<span id=\"info1_".$aid_id."\" style=\"cursor:pointer;\" onclick=\"javascript:Element.show('id_".$aid_id."');Element.hide('info1_".$aid_id."');Element.show('info2_".$aid_id."');\" >
	   <img src=\"../images/plier.png\" alt=\"Plus de détails\" Title=\"Plus de détails\"  style=\"vertical-align: middle;\" /></span>\n";
    echo "<span id=\"info2_".$aid_id."\" style=\"display: none;cursor:pointer;\" onclick=\"javascript:Element.hide('id_".$aid_id."');Element.show('info1_".$aid_id."');Element.hide('info2_".$aid_id."');\" >
	   <img src=\"../images/deplier.png\" alt=\"Moins de détails\" Title=\"Moins de détails\"  style=\"vertical-align: middle;\" /></span>\n";
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'',''))
        echo "<a href='modif_fiches.php?aid_id=$aid_id&amp;indice_aid=$indice_aid&amp;action=modif&amp;retour=visu_fiches.php'><img src=\"../images/edit.png\" alt=\"Modifier la fiche\" Title=\"Modifier la fiche\" style=\"vertical-align: middle;\" /></a>\n";


    echo "&nbsp;<b>$aid_nom</b>\n";

    // Adresse publique :
    if ((VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'adresse1','')) and ($adresse1 != "")) {
        if (($affiche_adresse1 == 'y')or(($affiche_adresse1 != 'y') and ($_login!="") )) {
            if ((substr($adresse1,0,4) == "http") or (substr($adresse1,0,3) == "ftp")) {
                echo " -  Accès publique : <a href='".$adresse1."' title='".$adresse1."' target='_blank' ";
                if (($en_construction == 'y') and ($message_avertissement!="")) echo " onclick='alert(\"".$message_avertissement."\");' ";
                echo ">cliquer pour accéder au site</a>";
             } else
                echo " -  Accès publique : <b>".$adresse1."</b>";
        }
    }
    // Adresse privée :
    if ((VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'adresse2','')) and ($adresse2 != "")) {
        if ($adresse2 != "")  {
            if ((substr($adresse2,0,4) == "http") or (substr($adresse2,0,3) == "ftp"))
                echo " -  Accès restreint : <a href='".$adresse2."' title='".$adresse2."' target='_blank'>cliquer pour accéder au site</a>";
            else
                echo " -  Accès restreint : <b>".$adresse2."</b>";
        }
    }
    echo "<br />";
    echo "<div id=\"id_".$aid_id."\" style=\"display: none;\">\n";
    echo "<table width=\"100%\" cellpadding=\"4\" bgcolor=\"#FFB68E\">\n";
    echo "<tr>\n";
    echo "<td width=50% style=\"vertical-align:top;\">";
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'resume','')) {
        echo "<span class='medium'><b>Résumé : </b>";
        //Résumé
        if ($resume == -1) $resume = $non_defini;
        echo "<br />".htmlentities($resume);
        echo "</span>";
    }
    echo "</td>\n";

    echo "<td style=\"vertical-align:top;\">";
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'famille','')) {
        // Famille
        echo "<span class='medium'><b>Projet classé dans la famille : </b>";
        $famille = sql_query1("select type from aid_familles where id = '".$famille."'");
        if ($famille == -1) $famille = $non_defini;
        echo $famille;
        echo "</span>\n";
    }

    //Mots clés :
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'mots_cles','')) {
        $mc = explode("|",$mots_cles);
        $k = 0;
        while ($k < 5) {
            if ((!isset($mc[$k])) or (trim($mc[$k]) == "")) $mc[$k] = "";
            $k++;
        }
        $aff_motcle = "";
        $k = 0;
        while ($k < 5) {
            if ($mc[$k] != "") {
                if ($aff_motcle != "") $aff_motcle .= " - ";
                $aff_motcle .= $mc[$k];
            }
            $k++;
        }
        echo "<br /><span class='medium'><b>Mots clés : </b>";
        if ($aff_motcle == "")
            echo $non_defini;
        else
            echo $aff_motcle;
        echo "</span>\n";
    }
    //Production
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'productions','')) {
        $p = explode("|",$productions);
        $k = 0;
        while ($k < $nb_productions) {
            if (!isset($p[$k]) or (trim($p[$k]) == "")) $p[$k] = "";
            $k++;
        }
        $k = 0;
        $liste = "";
        while ($k < $nb_productions) {
            $nom_productions = sql_query1("select nom from aid_productions where id = '".$p[$k]."'");
            if ($nom_productions != -1) {
                if ($liste != "") $liste .= " - ";
                $liste .= $nom_productions;
            }
            $k++;
        }
        echo "<br /><span class='medium'><b>Production(s) attendue(s) : </b>";
        if ($liste == "")
            echo $non_defini;
        else
            echo $liste;
        echo "</span>\n";
    }
    //Public
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'public_destinataire','')) {
        $public = explode("|",$public);
        $k = 0;
        while ($k < $nb_public) {
            if (!isset($public[$k]) or (trim($public[$k]) == "")) $public[$k] = "";
            $k++;
        }
        $k = 0;
        $liste = "";
        while ($k < $nb_public) {
            $nom_public = sql_query1("select public from aid_public where id = '".$public[$k]."'");
            if ($nom_public != -1) {
                    if ($liste != "") $liste .= " - ";
                $liste .= $nom_public;
            }
            $k++;
        }
        echo "<br /><span class='medium'><b>Public destinataire : </b>";
        if ($liste == "")
            echo $non_defini;
        else
            echo $liste;
        echo "</span>\n";
    }

    // Disciplines
    if ((VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'matiere1','')) and (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'matiere2',''))) {
        $discipline1 = sql_query1("select nom_complet from matieres where matiere = '".$discipline1."'");
        $discipline2 = sql_query1("select nom_complet from matieres where matiere = '".$discipline2."'");
        echo "<br /><span class='medium'><b>Disciplines attachées : </b>\n";

        if (($discipline1 == "-1") and ($discipline2 == "-1"))
            echo $non_defini."</span>\n";
        else   {
            if ($discipline1 != "-1") {
                echo htmlentities($discipline1)." - ";
            }
            if ($discipline2 != "-1") {
                echo htmlentities($discipline2);
            }
            echo "</span>\n";
        }
    }
    echo "</td></tr>\n";
    echo "<tr><td colspan=2><hr />\n";

    //Divers
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'divers','')) {
        if ($divers == "") $divers = "-";
        echo "<b>Divers : </b>".htmlentities($divers)."<br />";
    }

    // perso1
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'perso1','')) {
        if ($perso1 == "") $perso1 = "-";
        echo "<b>".LibelleChampAid("perso1")." : </b>".htmlentities($perso1)."<br />";
    }

    // perso2
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'perso2','')) {
        if ($perso2 == "") $perso2 = "-";
        echo "<b>".LibelleChampAid("perso2")." : </b>".htmlentities($perso2)."<br />";
    }

    // perso3
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'perso3','')) {
        if ($perso3 == "") $perso3 = "-";
        echo "<b>".LibelleChampAid("perso3")." : </b>".htmlentities($perso3)."<br />";
    }

    //Contacts
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'contacts','')) {
        if ($contacts == "") $contacts = "-";
        echo "<b>Contacts extérieurs, ressources, ... : </b>".htmlentities($contacts)."<br />";
    }

    // Autres infos
    if (
    VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'eleve_peut_modifier','') or
    VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'prof_peut_modifier','') or
    VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'cpe_peut_modifier','') or
    VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'fiche_publique','')
    ) {
    echo "<hr /><b>Autres informations : </b><ul>";
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'eleve_peut_modifier','')) {
      if ($eleve_peut_modifier == "y")
        echo "<li>Les élèves responsables peuvent modifier cette fiche.</li>";
      else
        echo "<li>Les élèves responsables ne peuvent pas modifier cette fiche.</li>";
    }
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'prof_peut_modifier','')) {
      if ($prof_peut_modifier == "y")
        echo "<li>Les professeurs responsables peuvent modifier cette fiche.</li>";
      else
        echo "<li>Les professeurs responsables ne peuvent pas modifier cette fiche.</li>";
    }
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'cpe_peut_modifier','')) {
      if ($cpe_peut_modifier == "y")
        echo "<li>Les CPE peuvent modifier cette fiche.</li>";
      else
        echo "<li>Les CPE ne peuvent pas modifier cette fiche.</li>";
    }
    if (VerifAccesFicheProjet($_login,$aid_id,$indice_aid,'fiche_publique','')) {
      if ($fiche_publique == "y")
        echo "<li>Cette fiche est en ligne dans <a href=\"javascript:centrerpopup('../public/index_fiches.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">l'interface publique</a>.</li>";
      else
        echo "<li>Cette fiche n'est pas disponible dans <a href=\"javascript:centrerpopup('../public/index_fiches.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">l'interface publique</a>.</li>";
    }

    echo "</ul>";
    }
    echo "</td></tr></table></div>\n";

    $i++;
}




?>
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;

<?php
include "../lib/footer.inc.php";
?>