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
}


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des élèves - Etape 2";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

// On vérifie si l'extension d_base est active
//verif_active_dbase();

//debug_var();
// Passer à 'y' pour afficher les requêtes
$debug_ele="n";

?>
<script type="text/javascript" language="JavaScript">
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

include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_eleves;

if (!isset($step2)) {
    $j=0;
    $flag=0;
	$chaine_tables="";
    while (($j < count($liste_tables_del)) and ($flag==0)) {
		$test = mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "SHOW TABLES LIKE '$liste_tables_del[$j]'"));
		if($test==1){
			if (mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				$flag=1;
			}
		}
        $j++;
    }

	for($loop=0;$loop<count($liste_tables_del);$loop++) {
		if($chaine_tables!="") {$chaine_tables.=", ";}
		$chaine_tables.="'".$liste_tables_del[$loop]."'";
	}

    if ($flag != 0){
        echo "<p><b>ATTENTION...</b><br />\n";
        echo "Des données concernant la constitution des classes et l'affectation des élèves dans les classes sont présentes dans la base GEPI ! Si vous poursuivez la procédure, ces données seront définitivement effacées !</p>\n";

		echo "<p>Les tables vidées seront&nbsp;: $chaine_tables</p>\n";

        echo "<form enctype='multipart/form-data' action='step2.php' method='post'>\n";
		echo add_token_field();
        echo "<input type=hidden name='step2' value='y' />\n";
        echo "<input type='submit' value='Poursuivre la procédure' />\n";
        echo "</form>\n";
        echo "<p><br /></p>\n";
		require("../lib/footer.inc.php");
        die();
    }
}

check_token(false);

if (isset($is_posted)) {

	echo "<p><em>On vide d'abord les tables suivantes&nbsp;:</em> ";
	$j=0;
	$k=0;
	while ($j < count($liste_tables_del)) {
		$sql="SHOW TABLES LIKE '".$liste_tables_del[$j]."';";
		//echo "$sql<br />";
		$test = sql_query1($sql);
		if ($test != -1) {
			if($k>0) {echo ", ";}
			$sql="SELECT 1=1 FROM $liste_tables_del[$j];";
			$res_test_tab=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($res_test_tab)>0) {
				$sql="DELETE FROM $liste_tables_del[$j];";
				$del = @mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				echo "<b>".$liste_tables_del[$j]."</b>";
				echo " (".mysqli_num_rows($res_test_tab).")";
			}
			else {
				echo $liste_tables_del[$j];
			}
			$k++;
		}
		$j++;
	}

	// Suppression des comptes d'élèves:
	echo "<br />\n";
	echo "<p><em>On supprime les anciens comptes élèves...</em> ";
	$sql="DELETE FROM utilisateurs WHERE statut='eleve';";
	$del=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

	// Liste des comptes scolarité pour associer aux nouvelles classes
	$sql="SELECT login FROM utilisateurs WHERE statut='scolarite';";
	$res_scol=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	$tab_user_scol=array();
	if(mysqli_num_rows($res_scol)>0) {
		while($lig_scol=mysqli_fetch_object($res_scol)) {$tab_user_scol[]=$lig_scol->login;}
	}

    // On va enregistrer la liste des classes, ainsi que les périodes qui leur seront attribuées
    $call_data = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT distinct(DIVCOD) classe FROM temp_gep_import2 WHERE DIVCOD!='' ORDER BY DIVCOD");
    $nb = mysqli_num_rows($call_data);
    $i = "0";

    while ($i < $nb) {
        $classe = mysql_result($call_data, $i, "classe");
        // On enregistre la classe
        // On teste d'abord :
        $test = mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT count(*) FROM classes WHERE (classe='$classe')"),0);
        if ($test == "0") {
            $reg_classe = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO classes SET classe='".((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], nettoyer_caracteres_nom($classe, "an", " -_", "")) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',nom_complet='".((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], nettoyer_caracteres_nom($reg_nom_complet[$classe], "an", " '-_", "")) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',suivi_par='".((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], nettoyer_caracteres_nom($reg_suivi[$classe], "an", " .,'-_", "")) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',formule='".html_entity_decode(((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], nettoyer_caracteres_nom($reg_formule[$classe], "an", " .,'-_", "")) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")))."', format_nom='cni'");

			$id_classe=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
			for($loop=0;$loop<count($tab_user_scol);$loop++) {
				// TEST déjà assoc... cela peut arriver si des scories subsistent...
				$sql="SELECT 1=1 FROM j_scol_classes WHERE login='$tab_user_scol[$loop]' AND id_classe='$id_classe';";
				$test_j_scol_class=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($test_j_scol_class)==0) {
					//$tab_user_scol
					$sql="INSERT INTO j_scol_classes SET login='$tab_user_scol[$loop]', id_classe='$id_classe';";
					$insert_j_scol_class=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				}
			}

        } else {
            $reg_classe = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE classes SET classe='".((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], nettoyer_caracteres_nom($classe, "an", " -_", "")) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',nom_complet='".((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], nettoyer_caracteres_nom($reg_nom_complet[$classe], "an", " '-_", "")) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',suivi_par='".((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], nettoyer_caracteres_nom($reg_suivi[$classe], "an", " .,'-_", "")) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',formule='".html_entity_decode(((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], nettoyer_caracteres_nom($reg_formule[$classe], "an", " .,'-_", "")) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")))."', format_nom='cni' WHERE classe='$classe'");
        }
        if (!$reg_classe) {echo "<p style='color:red'>Erreur lors de l'enregistrement de la classe $classe.";}

        // On enregistre les périodes pour cette classe
        // On teste d'abord :
        $id_classe = mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "select id from classes where classe='$classe'"),0,'id');
        $test = mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT count(*) FROM periodes WHERE (id_classe='$id_classe')"),0);
        if ($test == "0") {
            $j = '0';
            while ($j < $reg_periodes_num[$classe]) {
                $num = $j+1;
                $nom_per = "Période ".$num;
                if ($num == "1") { $ver = "N"; } else { $ver = 'O'; }
                $register = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                if (!$register) echo "<p>Erreur lors de l'enregistrement d'une période pour la classe $classe";
                $j++;
            }
        } else {
            // on "démarque" les périodes des classes qui ne sont pas à supprimer
            $sql = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE periodes SET verouiller='N' where (id_classe='$id_classe' and num_periode='1')");
            $sql = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE periodes SET verouiller='O' where (id_classe='$id_classe' and num_periode!='1')");
            //
            $nb_per = mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select num_periode from periodes where id_classe='$id_classe'"));
            if ($nb_per > $reg_periodes_num[$classe]) {
                // Le nombre de périodes de la classe est inférieur au nombre enregistré
                // On efface les périodes en trop
                $k = 0;
                for ($k=$reg_periodes_num[$classe]+1; $k<$nb_per+1; $k++) {
                    $del = mysqli_query($GLOBALS["___mysqli_ston"], "delete from periodes where (id_classe='$id_classe' and num_periode='$k')");
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
                    $register = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                    if (!$register) echo "<p>Erreur lors de l'enregistrement d'une période pour la classe $classe";
                }
            }
        }

        $i++;
    }

	$sql="update periodes set date_verrouillage='0000-00-00 00:00:00';";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if($res) {
		echo "Réinitialisation des dates de verrouillage de périodes effectuée.<br />";
	}
	else {
		echo "Erreur lors de la réinitialisation des dates de verrouillage de périodes.<br />";
	}

    // On efface les classes qui ne sont pas réutilisées cette année  ainsi que les entrées correspondantes dans  j_groupes_classes
    $sql = mysqli_query($GLOBALS["___mysqli_ston"], "select distinct id_classe from periodes where verouiller='T'");
    $k = 0;
    while ($k < mysqli_num_rows($sql)) {
       $id_classe = mysql_result($sql, $k);
       $res1 = mysqli_query($GLOBALS["___mysqli_ston"], "delete from classes where id='".$id_classe."'");
       $res2 = mysqli_query($GLOBALS["___mysqli_ston"], "delete from j_groupes_classes where id_classe='".$id_classe."'");
       $k++;
    }
    // On supprime les groupes qui n'ont plus aucune affectation de classe
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "delete from groupes g, j_groupes_classes jgc, j_eleves_groupes jeg, j_groupes_professeurs jgp, j_groupes_matieres jgm WHERE (" .
    		"g.id != jgc.id_groupe and jeg.id_groupe != jgc.id_groupe and jgp.id_groupe != jgc.id_groupe and jgm.id_groupe != jgc.id_groupe)");

    $res = mysqli_query($GLOBALS["___mysqli_ston"], "delete from periodes where verouiller='T'");
    echo "<p>Vous venez d'effectuer l'enregistrement des données concernant les classes. S'il n'y a pas eu d'erreurs, vous pouvez aller à l'étape suivante pour enregistrer les données concernant les élèves.";
    echo "<center><p><a href='step3.php'>Accéder à l'étape 3</a></p></center>";
} else {
    // On commence par "marquer" les classes existantes dans la base
    $sql = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE periodes SET verouiller='T'");
    //
    //$call_data = mysql_query("SELECT distinct(DIVCOD) classe FROM temp_gep_import WHERE DIVCOD!='' ORDER BY DIVCOD");
    $call_data = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT distinct(DIVCOD) classe FROM temp_gep_import2 WHERE DIVCOD!='' ORDER BY DIVCOD");
    $nb = mysqli_num_rows($call_data);
    $i = "0";
    echo "<form enctype='multipart/form-data' action='step2.php' method=post name='formulaire'>";
	echo add_token_field();
    echo "<input type=hidden name='is_posted' value='yes' />";
    echo "<p>Les classes en vert indiquent des classes déjà existantes dans la base GEPI.<br />Les classes en rouge indiquent des classes nouvelles et qui vont être ajoutées à la base GEPI.<br /></p>";
    echo "<p>Pour les nouvelles classes, des noms standards sont utilisés pour les périodes (période 1, période 2...), et seule la première période n'est pas verrouillée. Vous pourrez modifier ces paramètres ultérieurement</p>";
    echo "<p>Attention !!! Il n'y a pas de tests sur les champs entrés. Soyez vigilant à ne pas mettre des caractères spéciaux dans les champs ...</p>";
    echo "<p>Essayez de remplir tous les champs, cela évitera d'avoir à le faire ultérieurement.</p>";
    echo "<p>N'oubliez pas <b>d'enregistrer les données</b> en cliquant sur le bouton en bas de la page<br /><br />";
?>
<fieldset style="padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;">
<legend style="font-variant: small-caps;"> Aide au remplissage </legend>
<table border="0" summary='Tableau de remplissage'>
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
  <td>
    <input type="text" name="nom" maxlength="80" size="40" />
  <input type ="button" name="but_nom" value="Recopier"
onclick="javascript:MetVal('nom')" />
 </td>
</tr>
 <tr>
  <td colspan="4">&nbsp;</td>
  <td align="right">la formule au bas du bulletin sera
&nbsp;:&nbsp;</td>
  <td><input type="text" name="pour" maxlength="80" size="40" />
  <input type ="button" name="but_pour" value="Recopier"
onclick="javascript:MetVal('pour')" />
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

    echo "<table border=1 class='boireaus' cellpadding='2' cellspacing='2' summary='Tableau des classes'>";
    echo "<tr>
<th><p class=\"small\" align=\"center\">Aide<br />Remplissage</p></th>
<th><p class=\"small\">Identifiant de la classe</p></th>
<th><p class=\"small\">Nom complet</p></th>
<th><p class=\"small\">Nom apparaissant au bas du bulletin</p></th>
<th><p class=\"small\">formule au bas du bulletin</p></th>
<th><p class=\"small\">Nombres de périodes</p></th></tr>\n";
	$num_id1=1;
	$num_id2=$nb+1;
	$num_id3=2*$nb+1;
	$alt=1;
    while ($i < $nb) {
		$alt=$alt*(-1);
        $classe_id = mysql_result($call_data, $i, "classe");
        $test_classe_exist = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM classes WHERE classe='$classe_id'");
        $nb_test_classe_exist = mysqli_num_rows($test_classe_exist);

        if ($nb_test_classe_exist==0) {
            $nom_complet = $classe_id;
            $nom_court = "<font color=red>".$classe_id."</font>";
            $suivi_par = getSettingValue("gepiAdminPrenom")." ".getSettingValue("gepiAdminNom").", ".getSettingValue("gepiAdminFonction");
            $formule = "";
            $nb_per = '3';
        } else {
            $id_classe = mysql_result($test_classe_exist, 0, 'id');
            $nb_per = mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select num_periode from periodes where id_classe='$id_classe'"));
            $nom_court = "<font color=green>".$classe_id."</font>";
            $nom_complet = mysql_result($test_classe_exist, 0, 'nom_complet');
            $suivi_par = mysql_result($test_classe_exist, 0, 'suivi_par');
            $formule = mysql_result($test_classe_exist, 0, 'formule');
        }
        echo "<tr class='lig$alt'>\n";
        echo "<td><center><input type=\"checkbox\" /></center></td>\n";
        echo "<td>\n";
        echo "<p align='center'><b>$nom_court</b></p>\n";
        //echo "";
        echo "</td>\n";
        echo "<td>\n";
        echo "<input type=text id=\"n".$num_id1."\" onKeyDown=\"clavier(this.id,event);\" name='reg_nom_complet[$classe_id]' value=\"".$nom_complet."\" /> \n";
        echo "</td>\n";
        echo "<td>\n";
        echo "<input type=text id=\"n".$num_id2."\" onKeyDown=\"clavier(this.id,event);\" name='reg_suivi[$classe_id]' value=\"".$suivi_par."\" />\n";
        echo "</td>\n";
        echo "<td>\n";
        echo "<input type=text id=\"n".$num_id3."\" onKeyDown=\"clavier(this.id,event);\" name='reg_formule[$classe_id]' value=\"".$formule."\" />\n";
        echo "</td>\n";
        echo "<td>\n";
        echo "<select size=1 name='reg_periodes_num[$classe_id]'>\n";
        for ($k=1;$k<7;$k++) {
            echo "<option value='$k'";
            if ($nb_per == "$k") echo " SELECTED";
            echo ">$k</option>\n";
        }
        echo "</select>\n";
        echo "</td></tr>\n";
        $i++;
		$num_id1++;
		$num_id2++;
		$num_id3++;
    }
    echo "</table>\n";
    echo "<input type=hidden name='step2' value='y' />\n";
    echo "<p align='center'><input type='submit' value='Enregistrer les données' /></p>\n";
    echo "</form>\n";
}

?>
<p><em>Remarque sur les périodes&nbsp;:</em></p>
<blockquote>
	<p>Le nombre de périodes doit correspondre au nombre de bulletins qui sera édité pour chaque élève sur l'année.<br />
	En collège par exemple, on saisira trois périodes (<em>trimestres</em>).<br />
	Cela n'empêchera pas d'éditer six relevés de notes par élève au cours de l'année si vous souhaitez des relevés de notes de demi-période.<br />
	Il ne serait en revanche pas possible d'éditer un bulletin fusion de deux périodes.</p>
</blockquote>
<p><br /></p>
</div>
</body>
</html>
