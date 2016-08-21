<?php
/*
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
    // Insertion et suppression de périodes
    //
    $pb_reg_per = '';
    $periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$id_classe'");
    $nb_periode = mysqli_num_rows($periode_query);
    if ($nombre_periode < $nb_periode) {
        $k = $nombre_periode + 1;
        $nb_periode++;
        $autorisation_efface = 'oui';
        while ($k < $nb_periode) {
            $test = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM  j_eleves_classes WHERE (periode = '$k' and id_classe='$id_classe')");
            if (mysqli_num_rows($test) !=0) {
                $msg .= "Cette classe contient des élèves pour la periode $k ! Suppression impossible. Vous devez d'abord retirer les élèves de la classe.<br />";
                $autorisation_efface = 'non';
            }
            $k++;
        }
        if ($autorisation_efface == 'oui') {
            $pb_reg_per = 'no';
            $k = $nombre_periode + 1;
            while ($k < $nb_periode) {
                $efface = mysqli_query($GLOBALS["mysqli"], "DELETE FROM periodes WHERE (num_periode = '$k' AND id_classe = '$id_classe')");
                if (!$efface) {$pb_reg_per = 'yes';}
                $test = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM j_eleves_classes WHERE (periode = '$k' AND id_classe = '$id_classe')");
                $nb_ligne = mysqli_num_rows($test);
                $j = 0;
                while ($j < $nb_ligne) {
                    $login_eleve = old_mysql_result($test, $j, 'login');
                    $efface = mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_eleves_groupes WHERE (periode = '$k' AND login = '$login_eleve')");
                    if (!$efface) {$pb_reg_per = 'yes';}
                    $j++;
                }

                $efface = mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_eleves_classes WHERE (periode='$k' AND id_classe='$id_classe')");
                if (!$efface) {$pb_reg_per = 'yes';}
                $k++;

           }
        }
    } else {
        $pb_reg_per = 'no';
        $k = $nb_periode + 1;
        $nombre_periode++;
        while ($k < $nombre_periode) {
            $sql="INSERT INTO periodes SET nom_periode='période ".$k."', num_periode='$k', verouiller = 'N', id_classe='$id_classe';";
            //echo "$sql<br />";
            $register = mysqli_query($GLOBALS["mysqli"], $sql);
            if (!$register) {$pb_reg_per = 'yes';}
            $k++;
        }
    }

    //
    // Verrouillage et déverrouillage; changement de noms
    //

    $date_fin_period=isset($_POST['date_fin_period']) ? $_POST['date_fin_period'] : NULL;

    $periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$id_classe'");
    $nb_periode = mysqli_num_rows($periode_query) + 1 ;
    $k = "1";
    while ($k < $nb_periode) {
        if (!isset($nom_period[$k])) $nom_period[$k] = '';
        $nom_period[$k] = trim($nom_period[$k]);
        if ($nom_period[$k] == '') $nom_period[$k] = "période ".$k;
        //$register = mysql_query("UPDATE periodes SET nom_periode='$nom_period[$k]' WHERE (num_periode='$k' and id_classe='$id_classe')");
        $sql="UPDATE periodes SET nom_periode='".html_entity_decode($nom_period[$k])."'";
        if(isset($date_fin_period[$k])) {
            $tmp_tab=explode("/", $date_fin_period[$k]);
            if((!isset($tmp_tab[2]))||(!checkdate($tmp_tab[1], $tmp_tab[0], $tmp_tab[2]))) {
                $msg.="Erreur sur la date de fin de période en période $k<br />";
            }
            else {
                $sql.=", date_fin='".$tmp_tab[2]."-".$tmp_tab[1]."-".$tmp_tab[0]." 00:00:00'";
            }
        }
        $sql.=" WHERE (num_periode='$k' and id_classe='$id_classe');";
        //echo "$sql<br />";
        $register = mysqli_query($GLOBALS["mysqli"], $sql);
        if (!$register) {$pb_reg_per = 'yes';}
        $k++;
    }

   if ($pb_reg_per == 'no')  {
        $msg.="Les modifications ont été enregistrées !";

    } else if ($pb_reg_per == 'yes') {
        $msg.="Il y a eu un problème lors de la tentative de modification du nombre de périodes !";
    }

}

$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM classes WHERE id = '$id_classe'");
$classe = old_mysql_result($call_data, 0, "classe");
$periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$id_classe'");
$test_periode = mysqli_num_rows($periode_query) ;
include "../lib/periodes.inc.php";



// =================================
// AJOUT: boireaus
$chaine_options_classes="";
$sql="SELECT id, classe FROM classes ORDER BY classe";
$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_class_tmp)>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;

    $cpt_classe=0;
	$num_classe=-1;

	while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
		if($lig_class_tmp->id==$id_classe){
			// Index de la classe dans les <option>
			$num_classe=$cpt_classe;

			$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
			$temoin_tmp=1;
			if($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
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

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

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

<form enctype="multipart/form-data" method="post" name="formulaire" action="periodes.php">
<center><input type='submit' value='Enregistrer' /></center>
<p class='bold'>Classe : <?php echo $classe; ?></p>
<br />
<p style='text-indent:-7em; margin-left:7em;'><b>Remarque&nbsp;: </b>Le verrouillage/déverrouillage d'une période (*) est possible en étant connecté sous un compte ayant le statut "<strong>scolarité</strong>"<br />
(<em>(*) il est question dans cette remarque de verrouillage des saisies dans le carnet de notes, contrairement à la date de fin proposée ci-dessous qui concerne les absences et listes d'élèves proposées</em>).</p>
<br />

<?php

echo add_token_field();

echo "<p>Nombre de périodes&nbsp;: ";

//$sql="SELECT 1=1 FROM j_groupes_classes WHERE id_classe='$id_classe';";
$sql="SELECT 1=1 FROM j_groupes_classes jgc, j_eleves_groupes jeg WHERE jgc.id_classe='$id_classe' AND jeg.id_groupe=jgc.id_groupe;";
$verif=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($verif)>0) {
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
	echo "<p>Remarques&nbsp;: </p>\n";
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
	<th style='padding: 5px;' title="La date précisée ici est prise en compte pour les appartenances des élèves à telle classe sur telle période (notamment pour les élèves changeant de classe).
Il n'est pas question ici de verrouiller automatiquement une période de note à la date saisie.">Date de fin<br />de la période</th>
	</tr>
<?php
	$k = '1';
	$alt=1;

	include("../lib/calendrier/calendrier.class.php");

	while ($k < $nb_periode) {
		if ($nom_periode[$k] == '') {$nom_periode[$k] = "période ".$k;}
		$alt=$alt*(-1);

		//$cal[$k] = new Calendrier("formulaire", "date_fin_period_".$k);

		echo "<tr class='lig$alt'>\n";
		echo "<td style='padding: 5px;'>Période $k</td>\n";
		echo "<td style='padding: 5px;'><input type='text' id='nom_period_$k' name='nom_period[$k]'";
		echo " onchange='changement()'";
		echo " value=\"".$nom_periode[$k]."\" size='30' /></td>\n";
		echo "<td style='padding: 5px;'><input type='text' id='date_fin_period_$k' name='date_fin_period[$k]'";
		echo " onchange='changement()'";
		echo " onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\"";
		echo " value=\"".strftime("%d/%m/%Y", mysql_date_to_unix_timestamp($date_fin_periode[$k]))."\" size='10' />";

		//echo "<a href=\"#calend\" onClick=\"".$cal[$k]->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
		echo img_calendrier_js("date_fin_period_".$k, "img_bouton_date_fin_period_".$k);
		echo "</td>\n";
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

if($nb_periode>1) {
	//$sql="SELECT num_periode, nom_periode, date_fin, COUNT(date_fin) AS eff_date_fin FROM periodes  GROUP BY nom_periode ORDER BY eff_date_fin DESC, num_periode ASC;";
	$sql="SELECT DISTINCT num_periode, nom_periode, date_fin FROM periodes ORDER BY num_periode ASC;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "<p>Prendre modèle sur d'autres classes&nbsp;:</p>
<table class='boireaus'>
	<tr>
		<th>Numéro</th>
		<th colspan='2'>Nom</th>
		<th colspan='2'>Date de fin</th>
		<th>Effectif</th>
		<th>Classes</th>
		<!--th title='Prendre cette date pour la classe courante'><img src='../images/up.png' width='18' height='18' /></th-->
	</tr>";
		$alt=1;
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			$alt=$alt*(-1);
			$date_fin_formatee=formate_date($lig->date_fin);
			echo "
	<tr class='lig$alt white_hover'>
		<td>".$lig->num_periode."</td>
		<td id='modele_nom_periode_$cpt'>".$lig->nom_periode."</td>
		<td><a href=\"javascript:set_nom_periode(".$lig->num_periode.", ".$cpt.")\" title='Prendre ce nom de période pour la classe courante'><img src='../images/icons/wizard.png' width='16' height='16' /></a></td>
		<td>".$date_fin_formatee."</td>
		<td><a href=\"javascript:set_date_fin(".$lig->num_periode.", '".$date_fin_formatee."')\" title='Prendre cette date pour la classe courante'><img src='../images/icons/wizard.png' width='16' height='16' /></a></td>";

			echo "
		<td>";
			//formate_date($lig->date_fin)
			$sql="SELECT COUNT(date_fin) AS eff_date_fin FROM periodes p WHERE p.num_periode='".$lig->num_periode."' AND p.nom_periode='".$lig->nom_periode."' AND p.date_fin='".$lig->date_fin."';";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res2)>0) {
				$lig2=mysqli_fetch_object($res2);
				echo $lig2->eff_date_fin;
			}
		echo "</td>
		<td>";

			$sql="SELECT c.id, c.classe FROM classes c, periodes p WHERE p.id_classe=c.id AND p.num_periode='".$lig->num_periode."' AND p.nom_periode='".$lig->nom_periode."' AND p.date_fin='".$lig->date_fin."' ORDER BY c.classe;";
			//echo "$sql<br />";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res2)>0) {
				$cpt2=0;
				while($lig2=mysqli_fetch_object($res2)) {
					if($cpt2>0) {echo ", ";}
					echo $lig2->classe;
					$cpt2++;
				}
			}
			echo "
	</tr>";
			$cpt++;
		}
		echo "
</table>

<script type='text/javascript'>
	function set_nom_periode(num, num_ligne) {
		if(document.getElementById('nom_period_'+num)) {
			//alert(document.getElementById('modele_nom_periode_'+num_ligne).innerHTML);
			document.getElementById('nom_period_'+num).value=document.getElementById('modele_nom_periode_'+num_ligne).innerHTML;
			changement();
		}
	}

	function set_date_fin(num, valeur) {
		if(document.getElementById('date_fin_period_'+num)) {
			document.getElementById('date_fin_period_'+num).value=valeur;
			changement();
		}
	}
</script>
";

	}
}

$gepi_prof_suivi=ucfirst(retourne_denomination_pp($id_classe));
if(casse_mot($gepi_prof_suivi, "min")!=casse_mot(getSettingValue('gepi_prof_suivi'), "min")) {
	$chaine_gepi_prof_suivi=$gepi_prof_suivi." (<em>".getSettingValue('gepi_prof_suivi')."</em>)";
}
else {
	$chaine_gepi_prof_suivi=$gepi_prof_suivi;
}
echo "<br />
<p><em>NOTES&nbsp;:</em></p>
<ul>
<li><p>Les dates de fin de période indiquées ici ne correspondent pas à une date de verrouillage des saisies de notes.<br />
Il s'agit de dates prises en compte pour l'appartenance d'élèves à la classe pour telle période.<br />
C'est utile notamment pour les élèves qui changent de classe.<br />
Ces dates sont aussi prises en compte dans les modules Absences.</p></li>
<li><p>Le verrouillage des périodes de notes s'effectue en compte \"<strong>scolarité</strong>\" à la rubrique \"Verrouillage/déverrouillage des périodes\".</p></li>
<li><p>L'accès des parents/élèves aux appréciations et avis des conseils de classe se paramètre en compte \"administrateur\" ou sous réserve d'en donner le droit dans Gestion générale/Droits d'accès en compte \"scolarité\" ou \"professeur\" s'il est ".$chaine_gepi_prof_suivi.".</p></li>
</ul>\n";

require("../lib/footer.inc.php");

?>
