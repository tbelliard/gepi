<?php
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
extract($_GET, EXTR_OVERWRITE);
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

if (isset($is_posted) and ($is_posted == "yes")) {
	check_token();

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
$chaine_options_classes="";
$sql="SELECT id, classe FROM classes ORDER BY classe";
$res_class_tmp=mysql_query($sql);
if(mysql_num_rows($res_class_tmp)>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;

    $cpt_classe=0;
	$num_classe=-1;

	while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
		if($lig_class_tmp->id==$id_classe){
			// Index de la classe dans les <option>
			$num_classe=$cpt_classe;

			$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
			$temoin_tmp=1;
			if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
				$id_class_suiv=$lig_class_tmp->id;
			}
			else{
				$id_class_suiv=0;
			}
		}
		else {
			$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
		}

		if($temoin_tmp==0){
			$id_class_prec=$lig_class_tmp->id;
		}

		$cpt_classe++;
	}
}
// =================================

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Gestion des classes - Gestion des périodes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>\n";

if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe précédente</a>\n";}
if($chaine_options_classes!="") {

	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_classe').selectedIndex=$num_classe;
			}
		}
	}
</script>\n";


	echo " | <select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
	echo $chaine_options_classes;
	echo "</select>\n";
}
if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suivante</a>\n";}

//=========================
// AJOUT: boireaus 20081224
$titre="Navigation";
$texte="";

//$texte.="<img src='../images/icons/date.png' alt='' /> <a href='periodes.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Périodes</a><br />";
if($nb_periode>1) {
	// On a $nb_periode = Nombre de périodes + 1
	$texte.="<img src='../images/icons/edit_user.png' alt='' /> <a href='classes_const.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Élèves</a><br />";
}
$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignements</a><br />";
$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class_grp_lot.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">config.simplifiée</a><br />";
$texte.="<img src='../images/icons/configure.png' alt='' /> <a href='modify_nom_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Paramètres</a>";

$ouvrir_infobulle_nav=getSettingValue("ouvrir_infobulle_nav");

if($ouvrir_infobulle_nav=="y") {
	$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;'><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/vert.png' width='16' height='16' /></a></div>\n";
}
else {
	$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;'><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/rouge.png' width='16' height='16' /></a></div>\n";
}

$texte.="<script type='text/javascript'>
	// <![CDATA[
	function modif_mode_infobulle_nav() {
		new Ajax.Updater($('save_mode_nav'),'classes_ajax_lib.php?mode=ouvrir_infobulle_nav',{method: 'get'});
	}
	//]]>
</script>\n";

$tabdiv_infobulle[]=creer_div_infobulle('navigation_classe',$titre,"",$texte,"",14,0,'y','y','n','n');

echo " | <a href='#' onclick=\"afficher_div('navigation_classe','y',-100,20);\"";
echo ">";
echo "Navigation";
echo "</a>";
//=========================

echo "</p>\n";
echo "</form>\n";

?>

<form enctype="multipart/form-data" method="post" action="periodes.php">
<center><input type='submit' value='Enregistrer' /></center>
<p class='bold'>Classe : <?php echo $classe; ?></p>
<p><b>Remarque : </b>Le verrouillage/déverrouillage d'une période est possible en étant connecté sous un compte ayant le statut "scolarité".</p>

<?php

echo add_token_field();

echo "<p>Nombre de périodes : ";

//$sql="SELECT 1=1 FROM j_groupes_classes WHERE id_classe='$id_classe';";
$sql="SELECT 1=1 FROM j_groupes_classes jgc, j_eleves_groupes jeg WHERE jgc.id_classe='$id_classe' AND jeg.id_groupe=jgc.id_groupe;";
$verif=mysql_query($sql);
if(mysql_num_rows($verif)>0) {
	$temp = $nb_periode - 1;
	echo "<b>".$temp."</b>";
	echo "<input type='hidden' name='nombre_periode' value='$temp' />\n";
	echo "<br />\n";
	echo "<a href='ajouter_periode.php?id_classe=$id_classe'>Ajouter</a> / <a href='supprimer_periode.php?id_classe=$id_classe'>Supprimer</a> des périodes<br />\n";
}
else {
	echo "<select size=1 name='nombre_periode'";
	echo " onchange='changement()'";
	echo ">\n";

	$temp = $nb_periode - 1;
	$i = "0" ;
	while ($i < '7') {
		echo "<option value=$i "; if ($i == $temp) {echo " selected";} echo ">$i</option>\n";
		$i++;
	}
	echo "</select>\n";
}
echo "</p>\n";

if ($test_periode == 0) {
	echo "<p>Si vous choisissez de ne pas définir de périodes pour cette classe (nombre de périodes = 0), cette classe sera considérée comme virtuelle.</p>\n";
	echo "<p>Remarques : </p>\n";
	echo "<ul><li>Vous pouvez affecter une ou plusieurs matières à une classe virtuelle.</li>\n";
	echo "<li>Vous ne pouvez pas affecter d'élèves à une classe virtuelle.</li>\n";
	echo "<li>Une classe virtuelle peut être utilisée dans le cadre des cahiers de texte : création d'une rubrique accessible au public et remplie par un professeur d'une matière affectée à cette classe.</li>\n";
	echo "</ul>\n";

} else {
?>
    <!--center-->
    <!--table width=100% border=2 cellspacing=1 bordercolor=#330033 cellpadding=3-->
    <table class='boireaus'>
    <tr>
    <th>&nbsp;</th>
    <th style='padding: 5px;'>Nom de la période</th>
    </tr>
    <?php
    $k = '1';
	$alt=1;
    while ($k < $nb_periode) {
        if ($nom_periode[$k] == '') {$nom_periode[$k] = "période ".$k;}
        $alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
        echo "<td style='padding: 5px;'>Période $k</td>\n";
        echo "<td style='padding: 5px;'><input type='text' name='nom_period[$k]'";
		echo " onchange='changement()'";
		echo " value=\"".$nom_periode[$k]."\" size='30' /></td>\n";
        echo "</tr>\n";
        $k++;
    }
    ?>
    </table>
    <!--/center-->
<?php } ?>
<center><input type='submit' value='Enregistrer' style='margin: 30px 0 30px 0;'/></center>
<input type='hidden' name='is_posted' value="yes" />
<input type='hidden' name='id_classe' value='<?php echo $id_classe; ?>' />
</form>
<?php

if($ouvrir_infobulle_nav=='y') {
	echo "<script type='text/javascript'>
	setTimeout(\"afficher_div('navigation_classe','y',-100,20);\",1000)
</script>\n";
}

require("../lib/footer.inc.php");

?>
