<?php
/*
* @version: $Id$
*
* Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//pour gérer le verrouillage de la période depuis le fichier Vérif_bulletins.php
$classe=isset($_GET['classe']) ? $_GET['classe'] : 0;
$periode=isset($_GET['periode']) ? $_GET['periode'] : 0;
// quelle action après le verrouillage ?
$action_apres=isset($_GET['action']) ? $_GET['action'] : NULL;


if (isset($_POST['deverouillage_auto_periode_suivante'])) {
	check_token();
	if (!saveSetting("deverouillage_auto_periode_suivante", $_POST['deverouillage_auto_periode_suivante'])) {
		$msg .= "Erreur lors de l'enregistrement de deverouillage_auto_periode_suivante !";
		$reg_ok = 'no';
	}
}

if (isset($_POST['ok'])) {
	check_token();

	$pb_reg_ver = 'no';
	//$calldata = sql_query("SELECT DISTINCT c.id, c.classe FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	$calldata = sql_query("SELECT DISTINCT c.id, c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	if ($calldata) {
		for ($k = 0; ($row = sql_row($calldata, $k)); $k++) {
			$id_classe = $row[0];
			$periode_query = sql_query("SELECT verouiller, date_fin FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
			$nb_periode = sql_count($periode_query) + 1 ;
			if ($periode_query) {
				for ($i = 0; ($row_per = sql_row($periode_query, $i)); $i++) {
					$nom_classe = "cl_".$id_classe."_".$i;
					//echo "\$nom_classe=$nom_classe<br />";
					//echo "\$row_per[0]=$row_per[0]<br />";
					$t = $i+1;
					//if (isset($_POST[$nom_classe]))  {
					if ((isset($_POST[$nom_classe]))&&($_POST[$nom_classe]!=$row_per[0]))  {
						//$register = sql_query("UPDATE periodes SET verouiller='".$_POST[$nom_classe]."' WHERE (num_periode='".$t."' and id_classe='".$id_classe."')");
						if ($row_per[1] == 0) {//la date de fin n'est pas renseignee, on la renseigne
						    $register = sql_query("UPDATE periodes SET verouiller='".$_POST[$nom_classe]."', date_verrouillage=NOW(), date_fin=NOW() WHERE (num_periode='".$t."' and id_classe='".$id_classe."')");
						    if (!$register) {$pb_reg_ver = 'yes';}
						} else {
						    $register = sql_query("UPDATE periodes SET verouiller='".$_POST[$nom_classe]."', date_verrouillage=NOW() WHERE (num_periode='".$t."' and id_classe='".$id_classe."')");
						    if (!$register) {$pb_reg_ver = 'yes';}
						}
					}
					if ((isset($_POST["date_fin_".$nom_classe]))&&($_POST["date_fin_".$nom_classe]!=""))  {
						try {
						    $date_fin = new DateTime(str_replace("/",".",$_POST["date_fin_".$nom_classe]));
						    $date_fin->setTime(23,59,59);
						    if ($date_fin->format('U') != $row_per[1]) {
							$register = sql_query("UPDATE periodes SET date_fin='".$date_fin->format('Y-m-d')."' WHERE (num_periode='".$t."' and id_classe='".$id_classe."')");
							if (!$register) {$pb_reg_ver = 'yes';}
						    }
						    //$register = sql_query("UPDATE periodes SET verouiller='".$_POST[$nom_classe]."' WHERE (num_periode='".$t."' and id_classe='".$id_classe."')");
						    //$register = sql_query("UPDATE periodes SET verouiller='".$_POST[$nom_classe]."', date_verrouillage=NOW() WHERE (num_periode='".$t."' and id_classe='".$id_classe."')");
						    //if (!$register) {$pb_reg_ver = 'yes';}
						} catch (Exception $x) {
						}
					}
				}
			}
		}
	}
	
	// Déverrouillage de la période suivante si le bouton radio est à Oui.
	//if ((($action_apres == 'retour') OR ($action_apres == 'imprime_html') OR ($action_apres == 'imprime_pdf') OR ($action_apres == 'rien')) AND isset($_POST['deverouillage_auto_periode_suivante'])) {
	if ((($action_apres == 'retour') OR ($action_apres == 'imprime_bull') OR ($action_apres == 'rien')) AND isset($_POST['deverouillage_auto_periode_suivante'])) {
		if (($_POST['deverouillage_auto_periode_suivante'])=='y') {
			//recherche du nombre de période pour la classe
			$sql_periode = "SELECT * FROM periodes WHERE id_classe='$classe';";
			$result_periode = mysqli_query($GLOBALS["mysqli"], $sql_periode);
			$nb_periodes_classe = mysqli_num_rows($result_periode);
			//echo $nb_periodes_classe;
			$periode_en_cours = $periode;
			$periode_suivante = $periode+1;
			//Pour la période modifiée on récupère son état
			$etat_periode=old_mysql_result($result_periode, $periode-1, "verouiller");
			//echo "<br/>".$etat_periode;
			//echo "<br/>".$periode_en_cours;
			//echo "<br/>".$nb_periodes_classe;
			//si l'état est P ou O on dévérouille totalement la période +1 (di elle existe !)
			if (($etat_periode=='P') OR $etat_periode=='O') {
				if ($periode_en_cours  < $nb_periodes_classe) {
				//echo "<br/>On déverrouille $periode_suivante";
				//$sql_maj_periode_suivante = "UPDATE periodes SET verouiller='N' WHERE (num_periode='".$periode_suivante."' and id_classe='".$classe."')";
				//$sql_maj_periode_suivante = "UPDATE periodes SET verouiller='N', date_verrouillage='".time()."' WHERE (num_periode='".$periode_suivante."' and id_classe='".$classe."')";
				$sql_maj_periode_suivante = "UPDATE periodes SET verouiller='N', date_verrouillage=NOW() WHERE (num_periode='".$periode_suivante."' and id_classe='".$classe."')";
				//echo "<br/>".$sql_maj_periode_suivante;
				$result_maj_periode_suivante = mysqli_query($GLOBALS["mysqli"], $sql_maj_periode_suivante);
				if (!$result_maj_periode_suivante) {$pb_reg_ver = 'yes';}
				}
			}
		}
	}
	
	if ($pb_reg_ver == 'no') {
		$msg = "Les modifications ont été enregistrées.";
	} else {
		$msg = "Il y a eu un problème lors de l'enregistrement des données.";
	}

	if ($action_apres == 'retour') {
		header("Location: ./verif_bulletins.php");
	}

	//if ($action_apres == 'imprime_html') {
	if ($action_apres == 'imprime_bull') {
		//header("Location: ./index.php?id_classe=$classe");
		//echo "Location: ./bull_index.php?tab_id_classe[0]=$classe&tab_periode_num[0]=$periode&choix_periode_num=fait";
		header("Location: ./bull_index.php?tab_id_classe[0]=$classe&tab_periode_num[0]=$periode&choix_periode_num=fait");
	}
	/*
	if ($action_apres == 'imprime_pdf') {
		//header("Location: ./index.php?format=pdf");
		header("Location: ./bull_index.php?tab_id_classe[0]=$classe&tab_periode_num[0]=$periode&choix_periode_num=fait");
	}
	*/
}

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
$titre_page = "Verrouillage et déverrouillage des périodes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************
//debug_var();
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
				nom_du_champ=document.formulaire.elements[i+rang].getAttribute('name');
				if(nom_du_champ!="") {
					tmp_var=nom_du_champ.split("_");
					id_classe=tmp_var[1];
					actualise_cell_(id_classe,per);
				}
			}
		}
	}
}
</script>
<?php
echo "<p class='bold'><a href='../accueil.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if($_SESSION['statut']=='scolarite') {
	echo " | <a href='bull_index.php'>Visualisation et impression des bulletins</a>";
}

if(($_SESSION['statut']=='scolarite')&&(getSettingValue('GepiScolImprBulSettings')=='yes')) {
	echo " | <a href='param_bull.php'>Paramétrage des bulletins</a>";
}
echo "</p>\n";

$texte_deverrouiller = urlencode("Déverrouiller");
$texte_verrouiller_part = urlencode("Verrouiller part.");
$texte_verrouiller_tot = urlencode("Verrouiller tot.");

// si la classe et la période sont définies (on vient de verif_bulletiin.php)
if (!(($classe != 0) AND ($periode !=0))) {
	// On va chercher les classes déjà existantes, et on les affiche.
	$max_per = sql_query1("SELECT num_periode FROM periodes ORDER BY num_periode DESC LIMIT 1");
	//$calldata = sql_query("SELECT DISTINCT c.id, c.classe FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	$calldata = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.id, c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	$nombreligne = sql_count($calldata);
	echo "Total : $nombreligne classes\n";
}

echo "<ul>
<li>Lorsqu'une période est <b>déverrouillée</b>, le remplissage de toutes les rubriques (notes, appréciations, avis) est autorisé, la visualisation des
bulletins simples est autorisée mais la visualisation et l'impression des bulletins officiels sont impossibles.<br /><br /></li>
<li>Lorsqu'une période est <b>verrouillée partiellement</b>, seuls le remplissage et/ou la modification
de l'avis du conseil de classe";
if ($gepiSettings['active_mod_ects'] == 'y') echo "et des crédits ECTS ";
echo "sont possibles. La visualisation et l'impression des bulletins officiels sont autorisées.<br /><br /></li>
<li>Lorsqu'une période est <b>verrouillée totalement</b>, le remplissage et la modification du bulletin pour la période concernée
sont impossibles. la visualisation et l'impression sont autorisées.</li>\n";
echo "</ul>\n";
echo "<br /><br />\n";


// si la classe et la période sont définies (on vient de verif_bulletin.php)
if (($classe != 0) AND ($periode !=0)) {

	echo "<form action=\"verrouillage.php?classe=$classe&periode=$periode&action=$action_apres\" name=\"formulaire\" method=\"post\">\n";
	echo add_token_field();
	echo "<table class='boireaus' cellpadding='3' cellspacing='0' border='1' align='center'>\n";
	//echo "<tr class='fond_sombre'>\n<td>&nbsp;</td>\n";
	echo "<tr>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>\n";
	echo "</th>\n";
	echo "<th><img src=\"../lib/create_im_mat.php?texte=".$texte_deverrouiller."&amp;width=22\" width=\"22\" border='0' alt=\"Déverrouiller\" /></th>\n";
	echo "<th><img src=\"../lib/create_im_mat.php?texte=".$texte_verrouiller_part."&amp;width=22\" width=\"22\" border='0' alt=\"Verrouiller partiellement\" /></th>\n";
	echo "<th><img src=\"../lib/create_im_mat.php?texte=".$texte_verrouiller_tot."&amp;width=22\" width=\"22\" border='0' alt=\"Verrouiller totalement\" /></th>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	// Affichage de la classe (nom court)
	$sql_classe = "SELECT classe FROM classes WHERE id = '$classe'";
	$requete_classe = sql_query($sql_classe);
	$donner_modele = mysqli_fetch_array($requete_classe);
	$nom_court_classe = $donner_modele['classe'];
	echo "<td><b>$nom_court_classe</b> ";
	echo "</td>\n";

	$sql_periode = "SELECT nom_periode, verouiller FROM periodes WHERE (id_classe = '$classe' AND num_periode='$periode')";
	$periode_query = sql_query($sql_periode);
	$nb_periode = sql_count($periode_query) + 1 ;
	$j = 0;

	//ajustement de l'indice periode 1, 2 , 3 dans la base en réalité : 0, 1, 2
	$indice_periode = $periode-1;
	if ($periode_query) for ($i = 0; ($row_per = sql_row($periode_query, $i)); $i++) {
		$nom_classe = "cl_".$classe."_".$indice_periode;
		echo "<td>".ucfirst($row_per[0])."</td>\n";
		//echo "<input type=\"hidden\" name=\"numperiode\" value=\"$i\" />";
		echo "<td><input type=\"hidden\" name=\"numperiode\" value=\"$indice_periode\" />";
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

	echo "</table>\n<br />\n";

	// Option de déverrouillage automatique
	echo "<br />\n<table align='center'>\n";
	echo "<tr>\n";
	echo "<td>\nProcéder également au déverrouillage automatique de la période suivante <br />lors du verrouillage partiel ou total de la période ci-dessus : ";
	echo "\n</td>\n<td>\n";

		echo "<input type=\"radio\" name=\"deverouillage_auto_periode_suivante\" id='deverouillage_auto_periode_suivante_y' value=\"y\" ";
		if (getSettingValue("deverouillage_auto_periode_suivante") == 'y') echo " checked";
		echo " /><label for='deverouillage_auto_periode_suivante_y'>&nbsp;Oui</label>\n";
		echo "<input type=\"radio\" name=\"deverouillage_auto_periode_suivante\" id='deverouillage_auto_periode_suivante_n' value=\"n\" ";
		if (getSettingValue("deverouillage_auto_periode_suivante") != 'y') echo " checked";
		echo " /><label for='deverouillage_auto_periode_suivante_n'>&nbsp;Non</label>\n";

	echo "\n</td>\n</tr>\n</table>\n<br />\n";

	if ($action_apres == 'rien') {

	echo "<center><input type=\"submit\" name=\"ok\" value=\"Enregistrer\" /></center>\n";

	//} elseif ($action_apres == 'imprime_html') {
	} elseif ($action_apres == 'imprime_bull') {

	echo "<center><input type=\"submit\" name=\"ok\" value=\"Enregistrer puis aller à la page d'impression des bulletins\" /></center>\n";
	}
	/*
	elseif ($action_apres == 'imprime_pdf') {
	echo "<center><input type=\"submit\" name=\"ok\" value=\"Enregistrer puis aller à la page impression PDF\" /></center>\n";
	}
	*/
	elseif ($action_apres == 'retour') {

	echo "<center><input type=\"submit\" name=\"ok\" value=\"Enregistrer puis retour à la page vérification\" /></center>\n";

	}

	echo "</form>\n";

} else {
	if ($nombreligne != 0) {
		echo "<form action=\"verrouillage.php\" name=\"formulaire\" method=\"post\">";
		echo add_token_field();

		echo "<p align='center'><input type=\"submit\" name=\"ok\" value=\"Enregistrer\" /></p>\n";
		//echo "<table cellpadding='3' cellspacing='0' border='1' align='center'>";
		echo "<table class='boireaus' summary='Verrouillage des périodes' cellpadding='3' cellspacing='0' align='center'>\n";
		echo "<tr class='fond_sombre'><th>&nbsp;</th>\n";
		for ($i = 0; $i < $max_per; $i++) {
			echo "<th colspan='2'>\n";
			echo "<a href=\"javascript:CocheCase(1,".$i.")\">Tout déverrouiller</a><br />\n";
			echo "<a href=\"javascript:CocheCase(2,".$i.")\">Tout verrouiller partiellement</a><br />\n";
			echo "<a href=\"javascript:CocheCase(3,".$i.")\">Tout verrouiller  totalement</a>\n";
			echo "</th>\n";
			echo "<th><img src=\"../lib/create_im_mat.php?texte=".$texte_deverrouiller."&amp;width=22\" width=\"22\" border=0 alt=\"Déverrouiller\" /></th>\n";
			echo "<th><img src=\"../lib/create_im_mat.php?texte=".$texte_verrouiller_part."&amp;width=22\" width=\"22\" border=0 alt=\"Verrouiller partiellement\" /></th>\n";
			echo "<th><img src=\"../lib/create_im_mat.php?texte=".$texte_verrouiller_tot."&amp;width=22\" width=\"22\" border=0 alt=\"Verrouiller totalement\" /></th>\n";
            if(getSettingValue("active_module_absence")=="2"){
                echo "<th title=\"Il est possible de mettre à jour d'un coup, en compte administrateur, les dates de fin de période depuis le paramétrage du module Emploi du temps : Menu Gestion/Gestion du calendrier/Mettre à jour les dates de fin de période pour le module Absences, d'après les date de périodes de cours ci-dessous.\">Date Fin</th>\n";
            }
		}
		echo "</tr>\n";
		//$flag = 0;
			$alt=1;
		if ($calldata) {
			for ($k = 0; ($row = sql_row($calldata, $k)); $k++) {
				$precedente_date_fin="0000-00-00 00:00:00";
				$id_classe = $row[0];
				$classe = $row[1];
				$alt=$alt*(-1);
					echo "<tr class='lig$alt white_hover'";
				//if ($flag==1) { echo " class='fond_sombre'"; $flag = 0;} else {$flag=1;};
				echo ">\n";
				echo "<td>\n";
				echo "<b>$classe</b> ";
				echo "</td>\n";
		
				$periode_query = sql_query("SELECT nom_periode, verouiller, date_fin FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
				$nb_periode = sql_count($periode_query) + 1 ;
				$j = 0;
				if ($periode_query) {
					for ($i = 0; ($row_per = sql_row($periode_query, $i)); $i++) {
						$nom_classe = "cl_".$id_classe."_".$i;
						echo "<td>".ucfirst($row_per[0])."</td>\n";
			
						if ($row_per[1] == "N") {
							echo "<td id='c_".$id_classe."_".$i."' style='font-size:small; color:green;'>Ouvert</td>\n";
						}
						elseif ($row_per[1] == "P") {
							echo "<td id='c_".$id_classe."_".$i."' style='font-size:small; color:orange;'>Partiel.clos</td>\n";
						}
						elseif ($row_per[1] == "O") {
							echo "<td id='c_".$id_classe."_".$i."' style='font-size:small; color:red;'>Clos</td>\n";
						}
						else {
							// Ca ne devrait pas arriver
							echo "<td>&nbsp;</td>\n";
						}
			
						//echo "<input type=\"hidden\" name=\"numperiode\" value=\"$i\" />";
						echo "<td><input type=\"hidden\" name=\"numperiode\" value=\"$i\" />";
						//echo "<td><input type=\"radio\" name=\"".$nom_classe."\" value=\"N\" ";
						echo "<input type=\"radio\" name=\"".$nom_classe."\" id='radio_".$nom_classe."_N' value=\"N\" onchange=\"changement();actualise_cell_($id_classe,$i);\" ";
						if ($row_per[1] == "N") {echo "checked";}
						echo " /></td>\n";
						echo "<td><input type=\"radio\" name=\"".$nom_classe."\" id='radio_".$nom_classe."_P' value=\"P\" onchange=\"changement();actualise_cell_($id_classe,$i);\" ";
						if ($row_per[1] == "P") {echo "checked";}
						echo " /></td>\n";
						echo "<td><input type=\"radio\" name=\"".$nom_classe."\" id='radio_".$nom_classe."_O' value=\"O\" onchange=\"changement();actualise_cell_($id_classe,$i);\" ";
						if ($row_per[1] == "O") {echo "checked";}
						echo " /></td>\n";
                        if(getSettingValue("active_module_absence")=="2"){
                            if($precedente_date_fin>$row_per[2]) {
                                echo "<td style='background-color:red' title='ANOMALIE: La date de fin de cette période semble antérieure à la date de fin de la période précédente.'>";
                            }
                            else {
                                echo "<td>";
                            }
                            $precedente_date_fin=$row_per[2];
                            echo "<input type=\"text\" size=\"8\" name=\"date_fin_".$nom_classe."\" id=\"date_fin_".$nom_classe."\" value=\"";
                            if ($row_per[2] != 0) {
                                echo date("d/m/Y", strtotime($row_per[2]));
                            }
                            echo "\"/>";

 echo '
<script type="text/javascript">
Calendar.setup({
    inputField     :    "date_fin_'.$nom_classe.'",     // id of the input field
    ifFormat       :    "%d/%m/%Y",      // format of the input field
    button         :    "date_fin_'.$nom_classe.'",  // trigger for the calendar (button ID)
    align          :    "Bl",           // alignment (defaults to "Bl")
    singleClick    :    true
});
</script>&nbsp;';

                            echo "</td>\n";
                        }
						$j++;
					}
				}
				for ($i = $j; $i < $max_per; $i++) echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>\n";
		
				echo "</tr>\n";
			}
		}
		echo "</table>\n";
		echo "<br />\n";
		echo "<center><input type=\"submit\" name=\"ok\" value=\"Enregistrer\" /></center>\n";
		echo "</form>\n";
	
		echo "<script type='text/javascript'>
		function actualise_cell_(id_classe,i) {
			// id_classe correspond à la ligne (pas nécessairement le numéro de ligne)
			// i correspond au numéro de la période -1 (colonne)

			if(document.getElementById('c_'+id_classe+'_'+i)) {
				if(document.getElementById('radio_cl_'+id_classe+'_'+i+'_N')) {
					if(document.getElementById('radio_cl_'+id_classe+'_'+i+'_N').checked==true) {
						// Période ouverte en saisie
						document.getElementById('c_'+id_classe+'_'+i).innerHTML='Ouvert (*)';
						document.getElementById('c_'+id_classe+'_'+i).style.color='green';
					}
				}

				if(document.getElementById('radio_cl_'+id_classe+'_'+i+'_P')) {
					if(document.getElementById('radio_cl_'+id_classe+'_'+i+'_P').checked==true) {
						// Période partiellement close
						document.getElementById('c_'+id_classe+'_'+i).innerHTML='Partiel.clos (*)';
						document.getElementById('c_'+id_classe+'_'+i).style.color='orange';
					}
				}

				if(document.getElementById('radio_cl_'+id_classe+'_'+i+'_O')) {
					if(document.getElementById('radio_cl_'+id_classe+'_'+i+'_O').checked==true) {
						// Période close
						document.getElementById('c_'+id_classe+'_'+i).innerHTML='Clos (*)';
						document.getElementById('c_'+id_classe+'_'+i).style.color='red';
					}
				}
			}
		}
	</script>\n";

		echo "<br />\n";

		echo "<p><i>Remarques&nbsp;:</i></p>
<ul>
	<li><p><span style='margin-left: 3em;'>Si vous ne voyez pas toutes les classes, il se peut que certaines classes ne vous soient pas associées.</span><br /><span style='margin-left: 3em;'>Demandez alors à un compte administrateur de vous associer des classes dans <b>Gestion des bases/Gestion des classes/Paramétrage scolarité</b></span></p></li>";
		if(getSettingValue("active_module_absence")=="2"){
			echo "
	<li><p>Il est possible de mettre à jour d'un coup, en compte administrateur, les dates de fin de période depuis le paramétrage du module Emploi du temps : Menu Gestion/Gestion du calendrier/Mettre à jour les dates de fin de période pour le module Absences, d'après les date de périodes de cours ci-dessous.</p></li>";
		}
		echo "
</ul>\n";

	}
	else {
		echo "<p class='grand'>Attention : aucune classe n'a été définie dans la base GEPI !<br />Ou alors aucune classe ne vous est associée (<i>demandez alors à un compte administrateur de vous associer des classes dans <b>Gestion des bases/Gestion des classes/Paramétrage scolarité</b></i>)</p>\n";
	}
} //else
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
