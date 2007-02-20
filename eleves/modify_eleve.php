<?php
/*
 * Last modification  : 04/01/2006
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

unset($reg_login);
$reg_login = isset($_POST["reg_login"]) ? $_POST["reg_login"] : NULL;
unset($reg_nom);
$reg_nom = isset($_POST["reg_nom"]) ? $_POST["reg_nom"] : NULL;
unset($reg_prenom);
$reg_prenom = isset($_POST["reg_prenom"]) ? $_POST["reg_prenom"] : NULL;
unset($reg_sexe);
$reg_sexe = isset($_POST["reg_sexe"]) ? $_POST["reg_sexe"] : NULL;
unset($reg_no_nat);
$reg_no_nat = isset($_POST["reg_no_nat"]) ? $_POST["reg_no_nat"] : NULL;
unset($reg_no_gep);
$reg_no_gep = isset($_POST["reg_no_gep"]) ? $_POST["reg_no_gep"] : NULL;
unset($birth_year);
$birth_year = isset($_POST["birth_year"]) ? $_POST["birth_year"] : NULL;
unset($birth_month);
$birth_month = isset($_POST["birth_month"]) ? $_POST["birth_month"] : NULL;
unset($birth_day);
$birth_day = isset($_POST["birth_day"]) ? $_POST["birth_day"] : NULL;
unset($reg_resp1);
$reg_resp1 = isset($_POST["reg_resp1"]) ? $_POST["reg_resp1"] : NULL;
unset($reg_etab);
$reg_etab = isset($_POST["reg_etab"]) ? $_POST["reg_etab"] : NULL;

unset($mode);
$mode = isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : NULL);
unset($order_type);
$order_type = isset($_POST["order_type"]) ? $_POST["order_type"] : (isset($_GET["order_type"]) ? $_GET["order_type"] : NULL);
unset($quelles_classes);
$quelles_classes = isset($_POST["quelles_classes"]) ? $_POST["quelles_classes"] : (isset($_GET["quelles_classes"]) ? $_GET["quelles_classes"] : NULL);
unset($eleve_login);
$eleve_login = isset($_POST["eleve_login"]) ? $_POST["eleve_login"] : (isset($_GET["eleve_login"]) ? $_GET["eleve_login"] : NULL);


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

if (isset($_POST['is_posted']) and ($_POST['is_posted'] == "1")) {
    // Détermination du format de la date de naissance
    $call_eleve_test = mysql_query("SELECT naissance FROM eleves WHERE");
    $test_eleve_naissance = @mysql_result($call_eleve_test, "0", "naissance");
    $format = strlen($test_eleve_naissance);


    // Cas de la création d'un élève
    $reg_nom = trim($reg_nom);
    $reg_prenom = trim($reg_prenom);
    if ($reg_resp1 == '(vide)') $reg_resp1 = '';
    if (!ereg ("^[0-9]{4}$", $birth_year)) $birth_year = "1900";
    if (!ereg ("^[0-9]{2}$", $birth_month)) $birth_month = "01";
    if (!ereg ("^[0-9]{2}$", $birth_day)) $birth_day = "01";
    if ($format == '10')
        // YYYY-MM-DD
        $reg_naissance = $birth_year."-".$birth_month."-".$birth_day." 00:00:00";
    else if ($format == '8') {
        // YYYYMMDD
        $reg_naissance = $birth_year.$birth_month.$birth_day;
        settype($reg_naissance,"integer");
    } else {
        // Format inconnu
        $reg_naissance = $birth_year.$birth_month.$birth_day;
    }
    $continue = 'yes';
    if (($reg_nom == '') or ($reg_prenom == '')) {
       $msg = "Les champs nom et prénom sont obligatoires.";
       $continue = 'no';
    }

    if (($continue == 'yes') and (isset($reg_login))) {
        $msg = '';
        $ok = 'yes';
        if (ereg ("^[a-zA-Z_]{1}[a-zA-Z0-9_]{0,11}$", $reg_login)) {
            if ($reg_no_gep != '') {
                $test1 = mysql_query("SELECT login FROM eleves WHERE elenoet='$reg_no_gep'");
                $count1 = mysql_num_rows($test1);
                if ($count1 != "0") {
                    $msg .= "Erreur : un élève ayant le même numéro GEP existe déjà.<br />";
                    $ok = 'no';
                }
            }
            if ($reg_no_nat != '') {
                $test2 = mysql_query("SELECT login FROM eleves WHERE no_gep='$reg_no_nat'");
                $count2 = mysql_num_rows($test2);
                if ($count2 != "0") {
                    $msg .= "Erreur : un élève ayant le même numéro national existe déjà.";
                    $ok = 'no';
                }
            }
            if ($ok == 'yes') {
              $test = mysql_query("SELECT login FROM eleves WHERE login='$reg_login'");
              $count = mysql_num_rows($test);
              if ($count == "0") {
                $reg_data1 = mysql_query("INSERT INTO eleves SET
                    no_gep = '".$reg_no_nat."',
                    nom='".$reg_nom."',
                    prenom='".$reg_prenom."',
                    login='".$reg_login."',
                    sexe='".$reg_sexe."',
                    naissance='".$reg_naissance."',
                    elenoet = '".$reg_no_gep."',
                    ereno = '".$reg_resp1."'
                    ");
                $reg_data3 = mysql_query("INSERT INTO j_eleves_regime SET login='$reg_login', doublant='-', regime='d/p'");
                $call_test = mysql_query("SELECT * FROM j_eleves_etablissements WHERE id_eleve = '$reg_login'");
                $count2 = mysql_num_rows($call_test);
                if ($count2 == "0") {
                    if ($reg_etab != "(vide)") {
                        $reg_data2 = mysql_query("INSERT INTO j_eleves_etablissements VALUES ('$reg_login','$reg_etab')");
                    }
                } else {
                    if ($reg_etab != "(vide)") {
                        $reg_data2 = mysql_query("UPDATE j_eleves_etablissements SET id_etablissement = '$reg_etab' WHERE id_eleve='$reg_login'");
                    } else {
                        $reg_data2 = mysql_query("DELETE FROM j_eleves_etablissements WHERE id_eleve='$reg_login'");
                    }
                }
                if ((!$reg_data1) or (!$reg_data3)) {
                    $msg = "Erreur lors de l'enregistrement des données";
                } elseif ($mode == "unique") {
                   $mess=rawurlencode("Elève enregistré !");
                    header("Location: index.php?msg=$mess");
                    die();
                } elseif ($mode == "multiple") {
                    $mess=rawurlencode("Elève enregistré.Vous pouvez saisir l'élève suivant.");
                    header("Location: modify_eleve.php?mode=multiple&msg=$mess");
                    die();
                }
              } else {
                $msg="Un élève portant le même identifiant existe déja !";
              }
            }
        } else {
            $msg="L'identifiant choisi est constitué au maximum de 12 caractères : lettres, chiffres ou \"_\" et ne doit pas commencer par un chiffre !";
        }
     } else if ($continue == 'yes') {
        // On nettoie les windozeries
        $reg_data = mysql_query("UPDATE eleves SET no_gep = '$reg_no_nat', nom='$reg_nom',prenom='$reg_prenom',sexe='$reg_sexe',naissance='".$reg_naissance."', ereno='".$reg_resp1."', elenoet = '".$reg_no_gep."' WHERE login='".$eleve_login."'");
        if (!$reg_data) {
            $msg = "Erreur lors de l'enregistrement des données";
        }
        $call_test = mysql_query("SELECT * FROM j_eleves_etablissements WHERE id_eleve = '$eleve_login'");
        $count = mysql_num_rows($call_test);
        if ($count == "0") {
            if ($reg_etab != "(vide)") {
                $reg_data = mysql_query("INSERT INTO j_eleves_etablissements VALUES ('$eleve_login','$reg_etab')");
            }
        } else {
            if ($reg_etab != "(vide)") {
                $reg_data = mysql_query("UPDATE j_eleves_etablissements SET id_etablissement = '$reg_etab' WHERE id_eleve='$eleve_login'");
            } else {
                $reg_data = mysql_query("DELETE FROM j_eleves_etablissements WHERE id_eleve='$eleve_login'");
            }
        }
        if (!$reg_data) {
            $msg = "Erreur lors de l'enregistrement des données !";
        } else {
            $msg = "Les modifications ont bien été enregistrées !";
        }
    }
}

// On appelle les informations de l'utilisateur pour les afficher :
if (isset($eleve_login)) {
    $call_eleve_info = mysql_query("SELECT * FROM eleves WHERE login='$eleve_login'");
    $eleve_nom = mysql_result($call_eleve_info, "0", "nom");
    $eleve_prenom = mysql_result($call_eleve_info, "0", "prenom");
    $eleve_sexe = mysql_result($call_eleve_info, "0", "sexe");
    $eleve_naissance = mysql_result($call_eleve_info, "0", "naissance");
    if (strlen($eleve_naissance) == 10) {
        // YYYY-MM-DD
        $eleve_naissance_annee = substr($eleve_naissance, 0, 4);
        $eleve_naissance_mois = substr($eleve_naissance, 5, 2);
        $eleve_naissance_jour = substr($eleve_naissance, 8, 2);
    } elseif (strlen($eleve_naissance) == 8 ) {
        // YYYYMMDD
        $eleve_naissance_annee = substr($eleve_naissance, 0, 4);
        $eleve_naissance_mois = substr($eleve_naissance, 4, 2);
        $eleve_naissance_jour = substr($eleve_naissance, 6, 2);
    } elseif (strlen($eleve_naissance) == 19 ) {
        // YYYY-MM-DD xx:xx:xx
        $eleve_naissance_annee = substr($eleve_naissance, 0, 4);
        $eleve_naissance_mois = substr($eleve_naissance, 5, 2);
        $eleve_naissance_jour = substr($eleve_naissance, 8, 2);
    } else {
        // Format inconnu
        $eleve_naissance_annee = "??";
        $eleve_naissance_mois = "??";
        $eleve_naissance_jour = "????";
    }
    $eleve_no_resp = mysql_result($call_eleve_info, "0", "ereno");
    $reg_no_nat = mysql_result($call_eleve_info, "0", "no_gep");
    $reg_no_gep = mysql_result($call_eleve_info, "0", "elenoet");
    $call_etab = mysql_query("SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve='$eleve_login' and e.id = j.id_etablissement)");
    $id_etab = @mysql_result($call_etab, "0", "id");

} else {
    if (isset($reg_nom)) $eleve_nom = $reg_nom;
    if (isset($reg_prenom)) $eleve_prenom = $reg_prenom;
    if (isset($reg_sexe)) $eleve_sexe = $reg_sexe;
    if (isset($reg_no_nat)) $reg_no_nat = $reg_no_nat;
    if (isset($reg_no_gep)) $reg_no_gep = $reg_no_gep;
    if (isset($birth_year)) $eleve_naissance_annee = $birth_year;
    if (isset($birth_month)) $eleve_naissance_mois = $birth_month;
    if (isset($birth_day)) $eleve_naissance_jour = $birth_day;
    $eleve_no_resp = 0;
    $id_etab = 0;
}


//**************** EN-TETE *****************
$titre_page = "Gestion des élèves | Ajouter/Modifier une fiche élève";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************


if ((isset($order_type)) and (isset($quelles_classes))) {
    echo "<p class=bold>|<a href=\"index.php?quelles_classes=$quelles_classes&amp;order_type=$order_type\">Retour</a>|</p>";
} else {
    echo "<p class=bold>|<a href=\"index.php\">Retour</a>|";
}

?>
<form enctype="multipart/form-data" action="modify_eleve.php" method=post>
<table CELLPADDING = '5'>
<tr>
<?php
    if (isset($eleve_login)) {
        echo "<td>Identifiant GEPI * : </td>
        <td>".$eleve_login."<input type=hidden name='eleve_login' size=20 ";
        if ($eleve_login) echo "value='$eleve_login'";
        echo " /></td>";
    } else {
        echo "<td>Identifiant GEPI * : </td>
        <td><input type=text name=reg_login size=20 value=\"\" /></td>";
    }
    ?>
</tr><tr>
    <td>Nom * : </td>
    <td><input type=text name='reg_nom' size=20 <?php if (isset($eleve_nom)) { echo "value=\"".$eleve_nom."\"";}?> /></td>
</tr><tr>
    <td>Prénom * : </td>
    <td><input type=text name='reg_prenom' size=20 <?php if (isset($eleve_prenom)) { echo "value=\"".$eleve_prenom."\"";}?> /></td>
</tr><tr>
    <td>Identifiant National : </td>
    <?php
    echo "<td><input type=text name='reg_no_nat' size=20 ";
    if (isset($reg_no_nat)) echo "value=\"".$reg_no_nat."\"";
    echo " /></td>";
    ?>
</tr>
<?php
    echo "<tr><td>Numéro GEP : </td><td><input type=text name='reg_no_gep' size=20 ";
    if (isset($reg_no_gep)) echo "value=\"".$reg_no_gep."\"";
    echo " /></td>";


    ?>

</table>
<?php
if (($reg_no_gep == '') and (isset($eleve_login))) {
   echo "<font color=red>ATTENTION : Cet élève ne possède pas de numéro GEP. Vous ne pourrez pas importer les absences à partir des fichiers GEP pour cet élève.</font>";
}
?>
<center><table border = '1' CELLPADDING = '5'>
<tr><td><div class='norme'>Sexe : <br />
<?php
if (!(isset($eleve_sexe))) $eleve_sexe="M";
?>
<input type=radio name=reg_sexe value=M <?php if ($eleve_sexe == "M") { echo "CHECKED" ;} ?> /> Masculin
<input type=radio name=reg_sexe value=F <?php if ($eleve_sexe == "F") { echo "CHECKED" ;} ?> /> Féminin
</div></td><td><div class='norme'>
Date de naissance (respecter format 00/00/0000) : <br />
Jour <input type=text name=birth_day size=2 value=<?php if (isset($eleve_naissance_jour)) echo $eleve_naissance_jour;?> />
Mois<input type=text name=birth_month size=2 value=<?php if (isset($eleve_naissance_mois)) echo $eleve_naissance_mois;?> />
Année<input type=text name=birth_year size=4 value=<?php if (isset($eleve_naissance_annee)) echo $eleve_naissance_annee;?> />
</div></td></tr>
</table></center>

<p><b>Remarques</b> :
<br />- la modification du régime de l'élève (demi-pensionnaire, interne, ...) s'effectue dans le module de gestion des classes !
<br />- Les champs * sont obligatoires.</p>
<?php
$call_resp = mysql_query("SELECT * FROM responsables ORDER BY nom1, prenom1");
$nombreligne = mysql_num_rows($call_resp);
// si la table des responsables est non vide :
if ($nombreligne != 0) {
    $chaine_adr1 = '';
    $chaine_adr2 = '';
    $chaine_resp2 = '';
    echo "<br /><hr /><H3>Envoi des bulletins par voie postale</H3>";
    echo "<i>Si vous n'envoyez pas les bulletins scolaires par voie postale, vous pouvez ignorer cette rubrique.</i>";
    echo "<br /><br /><table><tr><td><b>Responsable légal principal : </b></td>";

    echo "<td><select size = 1 name = 'reg_resp1'>";
    echo "<option value='(vide)' "; if (!(isset($eleve_no_resp))) {echo " SELECTED";} echo ">(vide)</option>";
    $i = 0;
    while ($i < $nombreligne){
        $ereno = mysql_result($call_resp , $i, "ereno");
        $nom1 = mysql_result($call_resp , $i, "nom1");
        $prenom1 = mysql_result($call_resp , $i, "prenom1");
        $adr1 = mysql_result($call_resp , $i, "adr1");
        $adr1_comp = mysql_result($call_resp , $i, "adr1_comp");
        $commune1 = mysql_result($call_resp , $i, "commune1");
        $cp1 = mysql_result($call_resp , $i, "cp1");
        $nom2 = mysql_result($call_resp , $i, "nom2");
        $prenom2 = mysql_result($call_resp , $i, "prenom2");
        $adr2 = mysql_result($call_resp , $i, "adr2");
        $commune2 = mysql_result($call_resp , $i, "commune2");
        $cp2 = mysql_result($call_resp , $i, "cp2");
        echo "<option value=".$ereno." ";
        if ($ereno == $eleve_no_resp) {
            echo " SELECTED";
            $chaine_adr1 = $adr1." - ".$cp1.", ".$commune1;
            if ($adr2 != '') {
                $chaine_adr2 = $adr2." - ".$cp2.", ".$commune2;
                $chaine_resp2 = $nom2." ".$prenom2;
            }
            if (substr($adr1, 0, strlen($adr1)-1) == substr($adr2, 0, strlen($adr1)-1) and ($cp1 == $cp2) and ($commune1 == $commune2)) {
                $message = "<b>Les adresses des deux responsables légaux sont identiques. Par conséquent, le bulletin ne sera envoyé qu'à la première adresse.</b>";
            } else {
                if ($chaine_adr2 != '') {
                    $message =  "<b>Les adresses des deux responsables légaux ne sont pas identiques. Par conséquent, le bulletin sera envoyé aux deux responsables légaux.</b>";
                } else {
                    $message =  "<b>Le bulletin sera envoyé au responsable légal ci-dessus.</b>";
                }
            }


        }
        echo ">".$nom1." ".$prenom1." | ".$adr1." ".$adr1_comp." - ".$cp1.", ".$commune1."</option>";

        $i++;
    }
    echo "</select></td></tr>";
    if ($chaine_adr2 != '') {
        echo "<tr><td><b>Deuxième responsable légal : </b></td>";
        echo "<td>".$chaine_resp2." | ".$chaine_adr2."</td></tr>" ;
    }
    echo "</table>";
    echo "<br />Si le responsable légal ne figure pas dans la liste, vous pouvez l'ajouter à la base
    (après avoir, le cas échéant, sauvegardé cette fiche)
    <br />en vous rendant dans [Gestion des bases-><a href='../responsables/index.php'>Gestion des responsables élèves</a>]";

    if ($chaine_adr1 != '') {
        echo "<br />";
        echo $message;
        echo "<br />";
    }

}




?>
<br /><hr /><H3>Etablissement d'origine</h3>
<p>Etablissement d'origine :
<select size = 1 name = 'reg_etab'>
<?php
$calldata = mysql_query("SELECT * FROM etablissements ORDER BY id");
$nombreligne = mysql_num_rows($calldata);
echo "<option value='(vide)' "; if (!($id_etab)) {echo " SELECTED";} echo ">(vide)</option>";
$i = 0;
while ($i < $nombreligne){
    $list_etab_id = mysql_result($calldata, $i, "id");
    $list_etab_nom = mysql_result($calldata, $i, "nom");
    $list_etab_cp = mysql_result($calldata, $i, "cp");
    if ($list_etab_cp == 0) {$list_etab_cp = '';}
    $list_etab_ville = mysql_result($calldata, $i, "ville");
    $list_etab_niveau = mysql_result($calldata, $i, "niveau");
    foreach ($type_etablissement as $type_etab => $nom_etablissement) {
        if ($list_etab_niveau == $type_etab) {$list_etab_niveau = $nom_etablissement;}
    }
    echo "<option value=$list_etab_id "; if ($list_etab_id == $id_etab) {echo " SELECTED";} echo ">$list_etab_id | $list_etab_nom - $list_etab_niveau ($list_etab_cp";
    if ($list_etab_cp != '') {echo ", ";}
    echo "$list_etab_ville)</option>";
$i++;
}

echo "</select>";
echo "<input type=hidden name=is_posted value=\"1\" />";
if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />";
if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />";
if (isset($eleve_login)) echo "<input type=hidden name=eleve_login value=\"$eleve_login\" />";
if (isset($mode)) echo "<input type=hidden name=mode value=\"$mode\" />";
echo "<center><input type=submit value=Enregistrer /></center>";
echo "</form></body></html>";