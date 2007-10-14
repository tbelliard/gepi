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

if (isset($is_posted) and ($is_posted == "yes")) {
    $msg = '';
    //
    // Insertion et suppresion de périodes
    //
    $pb_reg_per = '';
    $periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
    $nb_periode = mysql_num_rows($periode_query);
    if ($nombre_periode < $nb_periode) {
        $k = $nombre_periode + 1;
        $nb_periode++;
        $autorisation_efface = 'oui';
        while ($k < $nb_periode) {
            $test = mysql_query("SELECT * FROM  j_eleves_classes WHERE (periode = '$k' and id_classe='$id_classe')");
            if (mysql_num_rows($test) !=0) {
                $msg .= "Cette classe contient des élèves pour la periode $k ! Suppression impossible. Vous devez d'abord retirer les élèves de la classe.<br />";
                $autorisation_efface = 'non';
            }
            $k++;
        }
        if ($autorisation_efface == 'oui') {
            $pb_reg_per = 'no';
            $k = $nombre_periode + 1;
            while ($k < $nb_periode) {
                $efface = mysql_query("DELETE FROM periodes WHERE (num_periode = '$k' AND id_classe = '$id_classe')");
                if (!$efface) {$pb_reg_per = 'yes';}
                $test = mysql_query("SELECT login FROM j_eleves_classes WHERE (periode = '$k' AND id_classe = '$id_classe')");
                $nb_ligne = mysql_num_rows($test);
                $j = 0;
                while ($j < $nb_ligne) {
                    $login_eleve = mysql_result($test, $j, 'login');
                    $efface = mysql_query("DELETE FROM j_eleves_groupes WHERE (periode = '$k' AND login = '$login_eleve')");
                    if (!$efface) {$pb_reg_per = 'yes';}
                    $j++;
                }

                $efface = mysql_query("DELETE FROM j_eleves_classes WHERE (periode='$k' AND id_classe='$id_classe')");
                if (!$efface) {$pb_reg_per = 'yes';}
                $k++;

           }
        }
    } else {
        $pb_reg_per = 'no';
        $k = $nb_periode + 1;
        $nombre_periode++;
        while ($k < $nombre_periode) {
            $register = mysql_query("INSERT INTO periodes SET nom_periode='période ".$k."', num_periode='$k', verouiller = 'N', id_classe='$id_classe'");
            if (!$register) {$pb_reg_per = 'yes';}
            $k++;
        }
    }

    //
    // Verrouillage et déverrouillage; changement de noms
    //

   $periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
    $nb_periode = mysql_num_rows($periode_query) + 1 ;
    $k = "1";
    while ($k < $nb_periode) {
        if (!isset($nom_period[$k])) $nom_period[$k] = '';
        $nom_period[$k] = trim($nom_period[$k]);
        if ($nom_period[$k] == '') $nom_period[$k] = "période ".$k;
        //$register = mysql_query("UPDATE periodes SET nom_periode='$nom_period[$k]' WHERE (num_periode='$k' and id_classe='$id_classe')");
        $register = mysql_query("UPDATE periodes SET nom_periode='".html_entity_decode($nom_period[$k])."' WHERE (num_periode='$k' and id_classe='$id_classe')");
        if (!$register) {$pb_reg_per = 'yes';}
        $k++;
    }

   if ($pb_reg_per == 'no')  {
        $msg.="Les modifications ont été enregistrées !";

    } else if ($pb_reg_per == 'yes') {
        $msg.="Il y a eu un problème lors de la tentative de modification du nombre de périodes !";
    }

}

$call_data = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");
$classe = mysql_result($call_data, 0, "classe");
$periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
$test_periode = mysql_num_rows($periode_query) ;
include "../lib/periodes.inc.php";



// =================================
// AJOUT: boireaus
$sql="SELECT id, classe FROM classes ORDER BY classe";
$res_class_tmp=mysql_query($sql);
if(mysql_num_rows($res_class_tmp)>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;
	while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
		if($lig_class_tmp->id==$id_classe){
			$temoin_tmp=1;
			if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				$id_class_suiv=$lig_class_tmp->id;
			}
			else{
				$id_class_suiv=0;
			}
		}
		if($temoin_tmp==0){
			$id_class_prec=$lig_class_tmp->id;
		}
	}
}
// =================================



//**************** EN-TETE *****************
$titre_page = "Gestion des classes - Gestion des périodes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>


<form enctype="multipart/form-data" method="post" action="periodes.php">
<p class='bold'><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>
<?php
if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec'>Classe précédente</a>";}
if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv'>Classe suivante</a>";}
?>
</p>
<center><input type='submit' value='Enregistrer' /></center>
<p class='bold'>Classe : <?php echo $classe; ?></p>
<p><b>Remarque : </b>Le verrouillage/déverrouillage d'une période est possible en étant connecté sous un compte ayant le statut "scolarité".</p>

<?php echo "<p>Nombre de périodes : "; ?>
<select size=1 name='nombre_periode'>
<?php
$temp = $nb_periode - 1;
$i = "0" ;
while ($i < '7') {

   echo "<option value=$i "; if ($i == $temp) {echo " SELECTED";} echo ">$i</option>";

   $i++;
}
?>
</select></p>
<?php
if ($test_periode == 0) {
    echo "<p>Si vous choisissez de ne pas définir de périodes pour cette classe (nombre de périodes = 0), cette classe sera considérée comme virtuelle.</p><p>Remarques : </p>";
    echo "<ul><li>Vous pouvez affecter une ou plusieurs matières à une classe virtuelle.</li>";
    echo "<li>Vous ne pouvez pas affecter d'élèves à une classe virtuelle.</li>";
    echo "<li>Une classe virtuelle peut être utilisée dans le cadre des cahiers de texte : création d'une rubrique accessible au public et remplie par un professeur d'une matière affectée à cette classe.</li></ul>";

} else {
?>
    <center>
    <!--table width=100% border=2 cellspacing=1 bordercolor=#330033 cellpadding=3-->
    <table width='100%' class='bordercolor'>
    <tr>
    <td>&nbsp;</td>
    <td><p >Nom de la période</p></td>
    </tr>
    <?php
    $k = '1';
    while ($k < $nb_periode) {
        if ($nom_periode[$k] == '') $nom_periode[$k] = "période ".$k;
        echo "<tr>";
        echo "<td><p>Période $k</p></td>";
        echo "<td><input type='text' name='nom_period[$k]' value=\"".$nom_periode[$k]."\" size='30' /></td>";
        echo"</tr>";
        $k++;
    }
    ?>
    </table>
    </center>
<?php } ?>
<center><input type='submit' value='Enregistrer' style='margin: 30px 0 30px 0;'/></center>
<input type='hidden' name='is_posted' value="yes" />
<input type='hidden' name='id_classe' value='<?php echo $id_classe; ?>' />
</form>
<?php require("../lib/footer.inc.php");?>