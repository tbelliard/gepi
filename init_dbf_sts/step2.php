<?php
@set_time_limit(0);
/*
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
extract($_POST, EXTR_OVERWRITE);

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

// Page bourrinée... la gestion du token n'est pas faite... et ne sera faite que si quelqu'un utilise encore ce mode d'initialisation et le manifeste sur la liste de diffusion gepi-users
check_token();

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des élèves - Etape 2";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

// On vérifie si l'extension d_base est active
verif_active_dbase();

?>
<script type="text/javascript">
<!--
function CocheCase(boul){
  len = document.formulaire.elements.length;
  for( i=0; i<len; i++) {
    if (document.formulaire.elements[i].type=='checkbox') {
      document.formulaire.elements[i].checked = boul ;
    }
  }
 }

function InverseSel(){
  len = document.formulaire.elements.length;
  for( i=0; i<len; i++) {
    if (document.formulaire.elements[i].type=='checkbox') {
      a=!document.formulaire.elements[i].checked  ;
      document.formulaire.elements[i].checked = a
    }
   }
}

function MetVal(cible){
len = document.formulaire.elements.length;
if ( cible== 'nom' ) {
  a=2;
  b=document.formulaire.nom.value;
  } else {
  a=3;
  b=document.formulaire.pour.value;
  }
for( i=0; i<len; i++) {
if ((document.formulaire.elements[i].type=='checkbox')
     &&
    (document.formulaire.elements[i].checked)
    ) {
document.formulaire.elements[i+a].value = b ;
}}}
 // -->
</script>

<?php
echo "<center><h3 class='gepi'>Première phase d'initialisation<br />Importation des élèves,  constitution des classes et affectation des élèves dans les classes</h3></center>";
echo "<center><h3 class='gepi'>Deuxième étape : Enregistrement des classes</h3></center>";

$liste_tables_del = array(
"absences",
"absences_gep",
"aid",
"aid_appreciations",
//"aid_config",
"avis_conseil_classe",
//"classes",
//"droits",
"eleves",
"responsables",
"responsables2",
"resp_pers",
"resp_adr",
//"etablissements",
"j_aid_eleves",
"j_aid_eleves_resp",
"j_aid_utilisateurs",
"j_eleves_classes",
//==========================
// On ne vide plus la table chaque année
// Problème avec Sconet qui récupère seulement l'établissement de l'année précédente qui peut être l'établissement courant
//"j_eleves_etablissements",
//==========================
"j_eleves_professeurs",
"j_eleves_regime",
//"j_professeurs_matieres",
//"log",
//"matieres",
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
//==========================
// Tables notanet
'notanet',
'notanet_avis',
'notanet_app',
'notanet_verrou',
'notanet_socles',
'notanet_ele_type',
//==========================
//"periodes",
"tempo2",
//"temp_gep_import",
"tempo",
//"utilisateurs",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
//"setting"
);

if (!isset($step2)) {
    $j=0;
    $flag=0;
    while (($j < count($liste_tables_del)) and ($flag==0)) {
        if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
            $flag=1;
        }
        $j++;
    }
    if ($flag != 0){
        echo "<p><b>ATTENTION ...</b><br />";
        echo "Des données concernant la constitution des classes et l'affectation des élèves dans les classes sont présentes dans la base GEPI ! Si vous poursuivez la procédure, ces données seront définitivement effacées !</p>";
        echo "<form enctype='multipart/form-data' action='step2.php' method=post>";
        echo "<input type=hidden name='step2' value='y' />";
        echo "<input type='submit' value='Poursuivre la procédure' />";
        echo "</form>";
        die();
    }
}



if (isset($is_posted)) {
    $j=0;
    while ($j < count($liste_tables_del)) {
        if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
            $del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
        }
        $j++;
    }

    // On va enregistrer la liste des classes, ainsi que les périodes qui leur seront attribuées
    $call_data = mysql_query("SELECT distinct(DIVCOD) classe FROM temp_gep_import WHERE DIVCOD!='' ORDER BY DIVCOD");
    $nb = mysql_num_rows($call_data);
    $i = "0";

    while ($i < $nb) {
        $classe = mysql_result($call_data, $i, "classe");
        // On enregistre la classe
        // On teste d'abord :
        $test = mysql_result(mysql_query("SELECT count(*) FROM classes WHERE (classe='$classe')"),0);
        if ($test == "0") {
            //$reg_classe = mysql_query("INSERT INTO classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($reg_nom_complet[$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($reg_suivi[$classe]))."',formule='".traitement_magic_quotes(corriger_caracteres($reg_formule[$classe]))."', format_nom='np'");
            $reg_classe = mysql_query("INSERT INTO classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($reg_nom_complet[$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($reg_suivi[$classe]))."',formule='".html_entity_decode(traitement_magic_quotes(corriger_caracteres($reg_formule[$classe])))."', format_nom='np'");
        } else {
            //$reg_classe = mysql_query("UPDATE classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($reg_nom_complet[$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($reg_suivi[$classe]))."',formule='".traitement_magic_quotes(corriger_caracteres($reg_formule[$classe]))."', format_nom='np' WHERE classe='$classe'");
			$reg_classe = mysql_query("UPDATE classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($reg_nom_complet[$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($reg_suivi[$classe]))."',formule='".html_entity_decode(traitement_magic_quotes(corriger_caracteres($reg_formule[$classe])))."', format_nom='np' WHERE classe='$classe'");
        }
        if (!$reg_classe) echo "<p>Erreur lors de l'enregistrement de la classe $classe.";

        // On enregistre les périodes pour cette classe
        // On teste d'abord :
        $id_classe = mysql_result(mysql_query("select id from classes where classe='$classe'"),0,'id');
        $test = mysql_result(mysql_query("SELECT count(*) FROM periodes WHERE (id_classe='$id_classe')"),0);
        if ($test == "0") {
            $j = '0';
            while ($j < $reg_periodes_num[$classe]) {
                $num = $j+1;
                $nom_per = "Période ".$num;
                if ($num == "1") { $ver = "N"; } else { $ver = 'O'; }
                $register = mysql_query("INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                if (!$register) echo "<p>Erreur lors de l'enregistrement d'une période pour la classe $classe";
                $j++;
            }
        } else {
            // on "démarque" les périodes des classes qui ne sont pas à supprimer
            $sql = mysql_query("UPDATE periodes SET verouiller='N' where (id_classe='$id_classe' and num_periode='1')");
            $sql = mysql_query("UPDATE periodes SET verouiller='O' where (id_classe='$id_classe' and num_periode!='1')");
            //
            $nb_per = mysql_num_rows(mysql_query("select num_periode from periodes where id_classe='$id_classe'"));
            if ($nb_per > $reg_periodes_num[$classe]) {
                // Le nombre de périodes de la classe est inférieur au nombre enregistré
                // On efface les périodes en trop
                $k = 0;
                for ($k=$reg_periodes_num[$classe]+1; $k<$nb_per+1; $k++) {
                    $del = mysql_query("delete from periodes where (id_classe='$id_classe' and num_periode='$k')");
                }
            }
            if ($nb_per < $reg_periodes_num[$classe]) {

                // Le nombre de périodes de la classe est supérieur au nombre enregistré
                // On enregistre les périodes
                $k = 0;
                $num = $nb_per;
                for ($k=$nb_per+1 ; $k < $reg_periodes_num[$classe]+1; $k++) {
                    $num++;
                    $nom_per = "Période ".$num;
                    if ($num == "1") { $ver = "N"; } else { $ver = 'O'; }
                    $register = mysql_query("INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                    if (!$register) echo "<p>Erreur lors de l'enregistrement d'une période pour la classe $classe";
                }
            }
        }

        $i++;
    }

	$sql="update periodes set date_verrouillage='0000-00-00 00:00:00';";
	$res=mysql_query($sql);
	if($res) {
		echo "Réinitialisation des dates de verrouillage de périodes effectuée.<br />";
	}
	else {
		echo "Erreur lors de la réinitialisation des dates de verrouillage de périodes.<br />";
	}

    // On efface les classes qui ne sont pas réutilisées cette année  ainsi que les entrées correspondantes dans  j_groupes_classes
    $sql = mysql_query("select distinct id_classe from periodes where verouiller='T'");
    $k = 0;
    while ($k < mysql_num_rows($sql)) {
       $id_classe = mysql_result($sql, $k);
       $res1 = mysql_query("delete from classes where id='".$id_classe."'");
       $res2 = mysql_query("delete from j_groupes_classes where id_classe='".$id_classe."'");
       $k++;
    }
    // On supprime les groupes qui n'ont plus aucune affectation de classe
    $res = mysql_query("delete from groupes g, j_groupes_classes jgc, j_eleves_groupes jeg, j_groupes_professeurs jgp, j_groupes_matieres jgm WHERE (" .
            "g.id != jgc.id_groupe and jeg.id_groupe != jgc.id_groupe and jgp.id_groupe != jgc.id_groupe and jgm.id_groupe != jgc.id_groupe)");

    $res = mysql_query("delete from periodes where verouiller='T'");
    echo "<p>Vous venez d'effectuer l'enregistrement des données concernant les classes. S'il n'y a pas eu d'erreurs, vous pouvez aller à l'étape suivante pour enregistrer les données concernant les élèves.";
    echo "<center><p><a href='step3.php'>Accéder à l'étape 3</a></p></center>";

	// On sauvegarde le témoin du fait qu'il va falloir convertir pour remplir les nouvelles tables responsables:
	saveSetting("conv_new_resp_table", 0);

} else {
    // On commence par "marquer" les classes existantes dans la base
    $sql = mysql_query("UPDATE periodes SET verouiller='T'");
    //
    $call_data = mysql_query("SELECT distinct(DIVCOD) classe FROM temp_gep_import WHERE DIVCOD!='' ORDER BY DIVCOD");
    $nb = mysql_num_rows($call_data);
    $i = "0";
    echo "<form enctype='multipart/form-data' action='step2.php' method=post name='formulaire'>";
    echo "<input type=hidden name='is_posted' value='yes' />";
    echo "<p>Les classes en vert indiquent des classes déjà existantes dans la base GEPI.<br />Les classes en rouge indiquent des classes nouvelles et qui vont être ajoutées à la base GEPI.<br /></p>";
    echo "<p>Pour les nouvelles classes, des noms standards sont utilisés pour les périodes (période 1, période 2...), et seule la première période n'est pas verrouillée. Vous pourrez modifier ces paramètres ultérieurement</p>";
    echo "<p>Attention !!! Il n'y a pas de tests sur les champs entrés. Soyez vigilant à ne pas mettre des caractères spéciaux dans les champs ...</p>";
    echo "<p>Essayez de remplir tous les champs, cela évitera d'avoir à le faire ultérieurement.</p>";
    echo "<p>N'oubliez pas <b>d'enregistrer les données</b> en cliquant sur le bouton en bas de la page<br /><br />";
?>
<fieldset style="padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;">
<legend style="font-variant: small-caps;"> Aide au remplissage </legend>
<table border="0">
<tr>
  <td width="2%">&nbsp;</td>
  <td width="2%">&nbsp;</td>
  <td width="2%">&nbsp;</td>
  <td width="2%">&nbsp;</td>
  <td width="25%">&nbsp;</td>
  <td width="53%">&nbsp;</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td colspan="5">Vous pouvez remplir les cases <font color="red">
une à une</font> et/ou <font color="red">globalement</font> grâce aux
fonctionnalités offertes ci-dessous :</td>
</tr>
<tr>
  <td colspan="2">&nbsp;</td>
  <td colspan="4">1) D'abord, cochez les lignes une à une</td>
</tr>
  <tr>
  <td colspan="3">&nbsp;</td>
  <td colspan="3">Vous pouvez aussi &nbsp;
  <a href="javascript:CocheCase(true)">
  COCHER</a> ou
  <a href="javascript:CocheCase(false)">
  DECOCHER</a> toutes les lignes , ou
  <a href="javascript:InverseSel()">
  INVERSER </a>la sélection</td>
</tr>
<tr>
  <td colspan="2">&nbsp;</td>
  <td colspan="4">2) Puis, pour les lignes cochées :</td>
</tr>
 <tr>
  <td colspan="4">&nbsp;</td>
  <td align="right">le nom au bas du bulletin sera &nbsp;:&nbsp;</td>
  <td><input type="text" name="nom" maxlength="80" size="40" />
  <input type ="button" name="but_nom" value="Recopier"
onclick="javascript:MetVal('nom')" /></td>
 </td>
</tr>
 <tr>
  <td colspan="4">&nbsp;</td>
  <td align="right">la formule au bas du bulletin sera
&nbsp;:&nbsp;</td>
  <td><input type="text" name="pour" maxlength="80" size="40" />
  <input type ="button" name="but_pour" value="Recopier"
onclick="javascript:MetVal('pour')" /></td>
 </td>
</tr>
<tr>
  <td colspan="2">&nbsp;</td>
  <td colspan="4">3) Cliquez sur les boutons "Recopier" pour remplir les champs selectionnés.</td>
</tr>

</table>
</fieldset>
<br />
<?php

    echo "<table border=1 cellpadding=2 cellspacing=2>";
    echo "<tr><td><p class=\"small\"><center>Aide<br />Remplissage</center></p></td><td><p class=\"small\">Identifiant de la classe</p></td><td><p class=\"small\">Nom complet</p></td><td><p class=\"small\">Nom apparaissant au bas du bulletin</p></td><td><p class=\"small\">formule au bas du bulletin</p></td><td><p class=\"small\">Nombres de périodes</p></td></tr>";
    while ($i < $nb) {
        $classe_id = mysql_result($call_data, $i, "classe");
        $test_classe_exist = mysql_query("SELECT * FROM classes WHERE classe='$classe_id'");
        $nb_test_classe_exist = mysql_num_rows($test_classe_exist);

        if ($nb_test_classe_exist==0) {
            $nom_complet = $classe_id;
            $nom_court = "<font color=red>".$classe_id."</font>";
            $suivi_par = getSettingValue("gepiAdminPrenom")." ".getSettingValue("gepiAdminNom").", ".getSettingValue("gepiAdminFonction");
            $formule = "";
            $nb_per = '3';
        } else {
            $id_classe = mysql_result($test_classe_exist, 0, 'id');
            $nb_per = mysql_num_rows(mysql_query("select num_periode from periodes where id_classe='$id_classe'"));
            $nom_court = "<font color=green>".$classe_id."</font>";
            $nom_complet = mysql_result($test_classe_exist, 0, 'nom_complet');
            $suivi_par = mysql_result($test_classe_exist, 0, 'suivi_par');
            $formule = mysql_result($test_classe_exist, 0, 'formule');
        }
        echo "<tr>";
        echo "<td><center><input type=\"checkbox\" /></center></td>";
        echo "<td>";
        echo "<p><b><center>$nom_court</center></b></p>";
        echo "";
        echo "</td>";
        echo "<td>";
        echo "<input type=text name='reg_nom_complet[$classe_id]' value=\"".$nom_complet."\" /> ";
        echo "</td>";
        echo "<td>";
        echo "<input type=text name='reg_suivi[$classe_id]' value=\"".$suivi_par."\" />";
        echo "</td>";
        echo "<td>";
        echo "<input type=text name='reg_formule[$classe_id]' value=\"".$formule."\" />";
        echo "</td>";
        echo "<td>";
        echo "<select size=1 name='reg_periodes_num[$classe_id]'>";
        for ($k=1;$k<7;$k++) {
            echo "<option value='$k'";
            if ($nb_per == "$k") echo " SELECTED";
            echo ">$k";
        }
        echo "</select>";
        echo "</td></tr>";
        $i++;
    }
    echo "</table>";
    echo "<input type=hidden name='step2' value='y' />";
    echo "<center><input type='submit' value='Enregistrer les données' /></center>";
    echo "</form>";
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
