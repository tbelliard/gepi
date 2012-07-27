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

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
    die("Le module n'est pas activé.");
}

//Initialisation pour le pdf
$w_pdf=array();
$w1 = "i"; //largeur de la première colonne
$w2 = "n"; // largeur des colonnes "notes"
$w3 = "c"; // largeur des colonnes "commentaires"

$header_pdf=array();
$data_pdf=array();
$titre =  "Toutes les notes";
$titre_pdf = urlencode($titre);


// Initialisation
isset($id_groupe);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] : (isset($_GET["id_groupe"]) ? $_GET["id_groupe"] : NULL);
$current_group = get_group($id_groupe);
$id_classe = $current_group["classes"]["list"][0];

$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];

include "../lib/periodes.inc.php";

//**************** EN-TETE *****************
$titre_page = "Visualisation de toutes les notes de l'année";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<script type='text/javascript' language='javascript'>
chargement = false;
</script>
<?php
echo "<form enctype=\"multipart/form-data\" name= \"form1\" action=\"".$_SERVER['PHP_SELF']."\" method=\"get\">\n";

echo "<p class='bold'>";
echo "<a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | ";
echo "<a href=\"../fpdf/imprime_pdf.php?titre=$titre_pdf&amp;id_groupe=$id_groupe\" target=\"_blank\" onclick=\"return VerifChargement()\">Imprimer au format PDF</a> |";


if(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='secours')) {
	if($_SESSION['statut']=='professeur') {
		$login_prof_groupe_courant=$_SESSION["login"];
	}
	else {
		$tmp_current_group=get_group($id_groupe);

		$login_prof_groupe_courant=$tmp_current_group["profs"]["list"][0];
	}

	$tab_groups = get_groups_for_prof($login_prof_groupe_courant,"classe puis matière");

	if(!empty($tab_groups)) {

		$chaine_options_classes="";

		$num_groupe=-1;
		$nb_groupes_suivies=count($tab_groups);

		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		for($loop=0;$loop<count($tab_groups);$loop++) {

			if($tab_groups[$loop]['id']==$id_groupe){
				$num_groupe=$loop;

				$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='true'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";

				$temoin_tmp=1;
				if(isset($tab_groups[$loop+1])){
					$id_grp_suiv=$tab_groups[$loop+1]['id'];
				}
				else{
					$id_grp_suiv=0;
				}
			}
			else {
				$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
			}

			if($temoin_tmp==0){
				$id_grp_prec=$tab_groups[$loop]['id'];
			}
		}
		// =================================
		if(($chaine_options_classes!="")&&($nb_groupes_suivies>1)) {

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
				document.getElementById('id_groupe').selectedIndex=$num_groupe;
			}
		}
	}
</script>\n";

			echo " <select name='id_groupe' id='id_groupe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
			echo $chaine_options_classes;
			echo "</select> | \n";
		}
	}
	// =================================
}


echo "</p>\n";

echo "</form>\n";

echo "<p class=cn><b>Classe : $nom_classe | Enseignement : " . $current_group["description"] . "</b></p>\n";


// Couleurs utilisées
$couleur_devoirs = '#AAE6AA';
$couleur_moy_cont = '#96C8F0';
$couleur_moy_sous_cont = '#FAFABE';
$couleur_calcul_moy = '#AAAAE6';

// Calcul du nombre de periodes à afficher : $nb_cahier_note
$appel_cahier_notes = mysql_query("SELECT periode, id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe='$id_groupe') ORDER BY periode");
$nb_cahier_note = mysql_num_rows($appel_cahier_notes);

if ($nb_cahier_note == 0) {
   echo "<p class='grand'>Aucune données à afficher !</p>\n";
   echo "</div>\n";
   echo "</body></html>";
   die();
}


// Déclaration des tableaux
$nb_dev  = array();
$id_sous_cont  = array();
$nom_sous_cont = array();
$coef_sous_cont = array();
$display_bulletin_sous_cont = array();
$nb_sous_cont = array();
$ponderation_sous_cont = array();

// Initialisation
$nb_dev[-1] = 0;
$id_sous_cont[-1] = 0;
$nom_sous_cont[-1] = 0;
$coef_sous_cont[-1] = 0;
$display_bulletin_sous_cont[-1] = 0;
$nb_sous_cont[-1] = 0;
$ponderation_sous_cont[-1] = 0;

$j=0;
$num_per = 0;
while ($num_per < $nb_cahier_note) {
    $id_conteneur[$num_per]  = mysql_result($appel_cahier_notes , $num_per, 'id_cahier_notes');
    $appel_conteneur = mysql_query("SELECT * FROM cn_conteneurs WHERE id ='".$id_conteneur[$num_per]."'");
    $nom_conteneur[$num_per] = mysql_result($appel_conteneur, 0, 'nom_court');
    $mode[$num_per] = mysql_result($appel_conteneur, 0, 'mode');
    $periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
    $periode_num = mysql_result($appel_cahier_notes , $num_per, 'periode');
    $nom_periode[$num_per] = mysql_result($periode_query, $periode_num-1, "nom_periode");

    // On teste si les cahiers de notes appartiennent bien à la personne connectée
    if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_conteneur[$num_per]))) {
        $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
        header("Location: index.php?msg=$mess");
        die();
    }
    //
    // Détermination des sous-conteneurs
    //
    $nb_sous_cont[$num_per] = $nb_sous_cont[$num_per-1];
    sous_conteneurs($id_conteneur[$num_per],$nb_sous_cont[$num_per],$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'all',$ponderation_sous_cont);

    // Détermination du nombre de devoirs à afficher
    $appel_dev = mysql_query("select * from cn_devoirs where (id_conteneur='".$id_conteneur[$num_per]."' and id_racine='".$id_conteneur[$num_per]."') order by date");
    $nb_dev[$num_per]  = $nb_dev[$num_per-1] + mysql_num_rows($appel_dev);
    // Détermination des noms et identificateurs des devoirs
    $k=0;
    for ($j = $nb_dev[$num_per-1]; $j < $nb_dev[$num_per]; $j++) {
        $nom_dev[$j] = mysql_result($appel_dev, $k, 'nom_court');
        $id_dev[$j] = mysql_result($appel_dev, $k, 'id');
        $coef[$j] = mysql_result($appel_dev, $k, 'coef');
        $note_sur[$j] = mysql_result($appel_dev, $k, 'note_sur');
        $ramener_sur_referentiel[$j] = mysql_result($appel_dev, $k, 'ramener_sur_referentiel');
        $facultatif[$j] = mysql_result($appel_dev, $k, 'facultatif');
        $date = mysql_result($appel_dev, $k, 'date');
        $annee = mb_substr($date,0,4);
        $mois =  mb_substr($date,5,2);
        $jour =  mb_substr($date,8,2);
        $display_date[$j] = $jour."/".$mois."/".$annee;
        $k++;
    }
    $num_per++;
}


$nombre_lignes = count($current_group["eleves"]["all"]["list"]);
$i = 0;
foreach ($current_group["eleves"]["all"]["list"] as $_login) {
    $eleve_login[$i] = $_login;
    $flag = 1;
    while (!in_array($_login, $current_group["eleves"][$flag]["list"])) {
        $flag++;
    }
    $eleve_nom[$i] = $current_group["eleves"][$flag]["users"][$_login]["nom"];
    $eleve_prenom[$i] = $current_group["eleves"][$flag]["users"][$_login]["prenom"];

    $somme_coef = 0;

        $k=0;
        while ($k < $nb_dev[$nb_cahier_note-1]) {
            $note_query = mysql_query("SELECT * FROM cn_notes_devoirs WHERE (login='$eleve_login[$i]' AND id_devoir='$id_dev[$k]')");
            $eleve_statut = @mysql_result($note_query, 0, "statut");
            $eleve_note = @mysql_result($note_query, 0, "note");
            $mess_note[$i][$k] = '';
            $mess_note[$i][$k] .= "<td class=cn bgcolor=$couleur_devoirs><center><b>";
            if (($eleve_statut != '') and ($eleve_statut != 'v')) {
                $mess_note[$i][$k] .= $eleve_statut;
                $mess_note_pdf[$i][$k] = $eleve_statut;
            } else if ($eleve_statut == 'v') {
                $mess_note[$i][$k] .="&nbsp;";
                $mess_note_pdf[$i][$k] = "";
            } else {
                if ($eleve_note != '') {
                   $mess_note[$i][$k] .=$eleve_note;
                   $mess_note_pdf[$i][$k] = $eleve_note;
                } else {
                    $mess_note[$i][$k] .="&nbsp;";
                    $mess_note_pdf[$i][$k] = "";
                }
            }
            $mess_note[$i][$k] .="</b></center></td>\n";
            $k++;
        }
   $i++;
}

//
// Affichage du tableau
//
echo "<table summary='Toutes les notes' border='1' cellspacing='2' cellpadding='1'>\n";

// Affichage première ligne
echo "<tr><td class=cn>&nbsp;</td>\n";
$num_per = 0;
while ($num_per < $nb_cahier_note) {
    // on calcule le nombre de colonnes à scinder
    $nb_colspan = $nb_dev[$num_per]-$nb_dev[$num_per-1];
    $i = $nb_sous_cont[$num_per-1];
    while ($i < $nb_sous_cont[$num_per]) {
        $query_nb_dev = mysql_query("SELECT * FROM cn_devoirs where (id_conteneur='$id_sous_cont[$i]') order by date");
        $nb_colspan++;
        $nb_colspan += mysql_num_rows($query_nb_dev);
        $i++;
    }
    // On rajoute 1 à colspan pour l'afichage de la colonne moyenne
    $nb_colspan++;
    echo "<td class=cn colspan='$nb_colspan' valign='top'><center><b>".ucfirst($nom_periode[$num_per])."</b></center></td>\n";
    $num_per++;
}
echo "</tr>\n";

// Affichage deuxième ligne
//echo "<tr><td class=cn><b>Boite :</b></td>\n";
echo "<tr><td class=cn><b>".casse_mot(getSettingValue("gepi_denom_boite",'majf2'))." :</b></td>\n";
$num_per = 0;
while ($num_per < $nb_cahier_note) {
    $nb_colspan = $nb_dev[$num_per]-$nb_dev[$num_per-1];
    if ($nb_colspan != 0) {
        echo "<td class=cn colspan='$nb_colspan' valign='top'><center><b>$nom_conteneur[$num_per]</b></center></td>\n";
    }

    $i = $nb_sous_cont[$num_per-1];
    while ($i < $nb_sous_cont[$num_per]) {
        $query_nb_dev = mysql_query("SELECT * FROM cn_devoirs where (id_conteneur='$id_sous_cont[$i]') order by date");
        $nb_dev_s_cont[$i]  = mysql_num_rows($query_nb_dev);
        $m = 0;
        while ($m < $nb_dev_s_cont[$i]) {
            $id_s_dev[$i][$m] = mysql_result($query_nb_dev, $m, 'id');
            $nom_sous_dev[$i][$m] = mysql_result($query_nb_dev, $m, 'nom_court');
            $coef_s_dev[$i][$m]  = mysql_result($query_nb_dev, $m, 'coef');
            $note_sur_s_dev[$i][$m]  = mysql_result($query_nb_dev, $m, 'note_sur');
            $ramener_sur_referentiel_s_dev[$i][$m]  = mysql_result($query_nb_dev, $m, 'ramener_sur_referentiel');
            $fac_s_dev[$i][$m]  = mysql_result($query_nb_dev, $m, 'facultatif');
            $date = mysql_result($query_nb_dev, $m, 'date');
            $annee = mb_substr($date,0,4);
            $mois =  mb_substr($date,5,2);
            $jour =  mb_substr($date,8,2);
            $display_date_s_dev[$i][$m] = $jour."/".$mois."/".$annee;
            $m++;
        }
        if ($nb_dev_s_cont[$i] != 0) echo "<td class=cn colspan='$nb_dev_s_cont[$i]' valign='top'><center><b>$nom_sous_cont[$i]</b></center></td>\n";
        echo "<td class=cn valign='top'><center><b>$nom_sous_cont[$i]</b>";
        if ($display_bulletin_sous_cont[$i] == '1') echo "<br /><font color='red'>Aff.&nbsp;bull.</font>";
        echo "</center></td>\n";
        $i++;
    }
    echo "<td class=cn  valign='top'><center><b>$nom_conteneur[$num_per]</b><br /><font color='red'>Aff.&nbsp;bull.</font></center></td>\n";
    $num_per++;
}
echo "</tr>";

// Troisième ligne
echo "<tr><td class=cn valign='top'>&nbsp;</td>\n";
$header_pdf[] = "Evaluation :";
$w_pdf[] = $w1;

$num_per = 0;
while ($num_per < $nb_cahier_note) {
    $i = $nb_dev[$num_per-1];
    while ($i < $nb_dev[$num_per]) {
        if ($coef[$i] != 0) {
            $tmp = " bgcolor = $couleur_calcul_moy ";
        } else {
            $tmp = '';
        }

        echo "<td class=cn".$tmp." valign='top'><center><b>$nom_dev[$i]</b><br />";
	if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $note_sur[$i]!=getSettingValue("referentiel_note")) {
		if ($ramener_sur_referentiel[$i] != 'V') {
			echo "<font size=-2>Note sur $note_sur[$i]<br />";
		} else {
			$tabdiv_infobulle[]=creer_div_infobulle('ramenersurReferentiel_'.$i,"Ramener sur referentiel","","La note est ramené sur ".getSettingValue("referentiel_note")." pour le calcul de la moyenne","",14,0,'y','y','n','n');
			echo "<a href='#' onmouseover=\"afficher_div('ramenersurReferentiel_$i','y',-150,20	);\" >";
			echo "<font size=-2>Note sur $note_sur[$i]";
			echo "</a><br />";
		}
		echo "($display_date[$i])</font></center></td>\n";
	}	
	else {
		echo "($display_date[$i])</center></td>\n";
	}

        $header_pdf[] = $nom_dev[$i]." (".$display_date[$i].")";
        $w_pdf[] = $w2;
        $i++;
    }
    $i = $nb_sous_cont[$num_per-1];
    while ($i < $nb_sous_cont[$num_per]) {
        $tmp = '';
        $m = 0;
        while ($m < $nb_dev_s_cont[$i]) {
            $tmp = '';
            if (($mode[$num_per] == 1) and ($coef_s_dev[$i][$m] != 0)) $tmp = " bgcolor = $couleur_calcul_moy ";
            echo "<td class=cn".$tmp." valign='top'><center><b>".$nom_sous_dev[$i][$m]."</b><br />";
	    if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $note_sur_s_dev[$i][$m]!=getSettingValue("referentiel_note")) {
		if ($ramener_sur_referentiel_s_dev[$i][$m] != 'V') {
			echo "<font size=-2>Note sur ".$note_sur_s_dev[$i][$m]."<br />";
		} else {
			$tabdiv_infobulle[]=creer_div_infobulle("ramenersurReferentiel_s_dev_".$i."_".$m,"Ramener sur referentiel","","La note est ramené sur ".getSettingValue("referentiel_note")." pour le calcul de la moyenne","",14,0,'y','y','n','n');
			echo "<a href='#' onmouseover=\"afficher_div('ramenersurReferentiel_s_dev_".$i."_".$m."','y',-150,20	);\" >";
			echo "<font size=-2>Note sur ".$note_sur_s_dev[$i][$m];
			echo "</a><br />";
		}
	    }	

	    echo "(".$display_date_s_dev[$i][$m].")</font></center></td>\n";
            $header_pdf[] = $nom_sous_dev[$i][$m]." (".$display_date_s_dev[$i][$m].")";
            $w_pdf[] = $w2;

            $m++;
        }
        $tmp = '';
        if (($mode[$num_per] == 2) and ($coef_sous_cont[$i] != 0)) $tmp = " bgcolor = $couleur_calcul_moy ";
        echo "<td class=cn".$tmp." valign='top'><center><b>Moy.</b></center></td>\n";
        $header_pdf[] = "Moy. : ".$nom_sous_cont[$i];
        $w_pdf[] = $w2;

        $i++;
    }
    echo "<td class=cn valign='top'><center><b>Moy.</b></center></td>\n";
    $header_pdf[] = "Moy. (".$nom_periode[$num_per].")";
    $w_pdf[] = $w2;

    $num_per++;
}
echo "</tr>";


//
// quatrième ligne
//
echo "<tr><td class=cn valign='top'><b>Nom&nbsp;Prénom&nbsp;\&nbsp;Coef.</b></td>\n";
if(getSettingValue("note_autre_que_sur_referentiel")=="V") {
	$data_pdf[0][] = "Nom Prénom\Coef. /Note sur";
} else {
	$data_pdf[0][] = "Nom Prénom\Coef.";
}
$num_per = 0;
while ($num_per < $nb_cahier_note) {
    $i = $nb_dev[$num_per-1];
    while ($i < $nb_dev[$num_per]) {
        echo "<td class=cn valign='top'><center><b>$coef[$i]</b>";
	if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $note_sur[$i]!=getSettingValue("referentiel_note")) {
		$data_pdf[0][] = $coef[$i]." /".$note_sur[$i];
	} else {
	        $data_pdf[0][] = $coef[$i];
	}
        if (($facultatif[$i] == 'B') or ($facultatif[$i] == 'N')) echo "<br />Bonus";
        echo "</center></td>\n";
        $i++;
    }
    $i = $nb_sous_cont[$num_per-1];
    while ($i < $nb_sous_cont[$num_per]) {
        $m = 0;
        while ($m < $nb_dev_s_cont[$i]) {
            echo "<td class=cn valign='top'><center><b>".$coef_s_dev[$i][$m]."</b>";
	    if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $note_sur_s_dev[$i][$m]!=getSettingValue("referentiel_note")) {
		$data_pdf[0][] = $coef_s_dev[$i][$m]." /".$note_sur_s_dev[$i][$m];
	    } else {
	        $data_pdf[0][] = $coef_s_dev[$i][$m];
	    }
            if (($fac_s_dev[$i][$m] == 'B') or ($fac_s_dev[$i][$m] == 'N')) echo "<br />Bonus";
            echo "</center></td>\n";
            $m++;
        }
        if ($mode[$num_per]==2) {
            echo "<td class=cn valign='top'><center><b>$coef_sous_cont[$i]</b></center></td>\n";
            $data_pdf[0][] = $coef_sous_cont[$i];
        } else {
            echo "<td class=cn valign='top'><center>&nbsp;</center></td>\n";
            $data_pdf[0][] = "";
        }
        $i++;
    }
    $num_per++;
    echo "<td class=cn valign='top'><center>&nbsp;</center></td>\n";
    $data_pdf[0][] = "";
}
echo "</tr>\n";

//
// Affichage des lignes "elèves"
//
$i = 0;
$tot_data_pdf = 1;
while($i < $nombre_lignes) {
        $tot_data_pdf++;
        $data_pdf[$i+1][] = $eleve_nom[$i]." ".$eleve_prenom[$i];
        echo "<tr><td class=cn>$eleve_nom[$i] $eleve_prenom[$i]</td>\n";
        $num_per = 0;
        while ($num_per < $nb_cahier_note) {
            $k=$nb_dev[$num_per-1];
            while ($k < $nb_dev[$num_per]) {
                echo $mess_note[$i][$k];
                $data_pdf[$i+1][] = $mess_note_pdf[$i][$k];
                $k++;
            }
            //
            // Affichage de la moyenne de tous les sous-conteneurs
            //
            $k=$nb_sous_cont[$num_per-1];
            while ($k < $nb_sous_cont[$num_per]) {
                $m = 0;
                while ($m < $nb_dev_s_cont[$k]) {
                    $temp = $id_s_dev[$k][$m];
                    $note_query = mysql_query("SELECT * FROM cn_notes_devoirs WHERE (login='$eleve_login[$i]' AND id_devoir='$temp')");
                    $eleve_statut = @mysql_result($note_query, 0, "statut");
                    $eleve_note = @mysql_result($note_query, 0, "note");
                    if (($eleve_statut != '') and ($eleve_statut != 'v')) {
                        $tmp = $eleve_statut;
                        $data_pdf[$i+1][] = $eleve_statut;
                    } else if ($eleve_statut == 'v') {
                        $tmp = "&nbsp;";
                        $data_pdf[$i+1][] = "";
                    } else {
                        if ($eleve_note != '') {
                            $tmp = $eleve_note;
                            $data_pdf[$i+1][] = $eleve_note;
                        } else {
                            $tmp = "&nbsp;";
                            $data_pdf[$i+1][] = "";
                        }
                    }
                    echo "<td class=cn bgcolor=$couleur_devoirs><center><b>$tmp</b></center></td>\n";
                    $m++;
                }
                $moyenne_query = mysql_query("SELECT * FROM cn_notes_conteneurs WHERE (login='$eleve_login[$i]' AND id_conteneur='$id_sous_cont[$k]')");
                $statut_moy = @mysql_result($moyenne_query, 0, "statut");
                if ($statut_moy == 'y') {
                    $moy = @mysql_result($moyenne_query, 0, "note");
                    $data_pdf[$i+1][] = $moy;
                } else {
                    $moy = '&nbsp;';
                    $data_pdf[$i+1][] = "";
                }
                echo "<td class=cn bgcolor=$couleur_moy_sous_cont><center>$moy</center></td>\n";
                $k++;
            }
            //
            // affichage des moyennes du conteneur
            //
            $moyenne_query = mysql_query("SELECT * FROM cn_notes_conteneurs WHERE (login='$eleve_login[$i]' AND id_conteneur='".$id_conteneur[$num_per]."')");
            $statut_moy = @mysql_result($moyenne_query, 0, "statut");
            if ($statut_moy == 'y') {
                $moy = @mysql_result($moyenne_query, 0, "note");
                $data_pdf[$i+1][] = $moy;
            } else {
                $moy = '&nbsp;';
                $data_pdf[$i+1][] = "";
            }
            echo "<td class=cn bgcolor=$couleur_moy_cont><center><b>$moy</b></center></td>\n";
        $num_per++;
        }

    echo "</tr>\n";
    $i++;
}

//
// Dernière ligne
//
echo "<tr><td class=cn><b>Moyennes :</b></td>\n";
$data_pdf[$tot_data_pdf][] = "Moyennes";

$num_per = 0;
while ($num_per < $nb_cahier_note) {
    $i = $nb_dev[$num_per-1];
    while ($i < $nb_dev[$num_per]) {
        $call_moyenne = mysql_query("SELECT round(avg(n.note),1) moyenne FROM cn_notes_devoirs n, j_eleves_groupes j WHERE (
        j.id_groupe='$id_groupe' AND
        j.login = n.login AND
        j.periode = '".($num_per+1)."' AND
        n.statut='' AND
        n.id_devoir='$id_dev[$i]'
        )");
        $moyenne = mysql_result($call_moyenne, 0, "moyenne");
        if ($moyenne != '') {
            echo "<td class=cn><center><b>$moyenne</b></center></td>\n";
            $data_pdf[$tot_data_pdf][] = $moyenne;
        } else {
            echo "<td class=cn>&nbsp;</td></td>\n";
            $data_pdf[$tot_data_pdf][] = "";
        }
        $i++;
    }
    //
    // Moyenne des moyennes des sous-conteneurs
    //
    $i = $nb_sous_cont[$num_per-1];
    while ($i < $nb_sous_cont[$num_per]) {
        $m = 0;
        while ($m < $nb_dev_s_cont[$i]) {
            $call_moy = mysql_query("SELECT round(avg(n.note),1) moyenne FROM cn_notes_devoirs n, j_eleves_groupes j WHERE (
            j.id_groupe='$id_groupe' AND
            j.login = n.login AND
            j.periode = '".($num_per+1)."' AND
            n.statut='' AND
            n.id_devoir='".$id_s_dev[$i][$m]."'
            )");
            $moy_s_dev = mysql_result($call_moy, 0, "moyenne");
            if ($moy_s_dev != '') {
                echo "<td class=cn><center><b>$moy_s_dev</b></center></td>\n";
                $data_pdf[$tot_data_pdf][] = $moy_s_dev;
            } else {
                echo "<td class=cn>&nbsp;</td>\n";
                $data_pdf[$tot_data_pdf][] = "";
            }
            $m++;
        }
        $call_moy_moy = mysql_query("SELECT round(avg(n.note),1) moyenne FROM cn_notes_conteneurs n, j_eleves_groupes j WHERE (
        j.id_groupe='$id_groupe' AND
        j.login = n.login AND
        j.periode = '".($num_per+1)."' AND
        n.statut='y' AND
        n.id_conteneur='".$id_sous_cont[$i]."'
        )");
        $moy_moy = mysql_result($call_moy_moy, 0, "moyenne");
        if ($moy_moy != '') {
            echo "<td class=cn><center><b>$moy_moy</b></center></td>\n";
            $data_pdf[$tot_data_pdf][] = $moy_moy;
        } else {
            echo "<td class=cn>&nbsp;</td>\n";
            $data_pdf[$tot_data_pdf][] = "";
        }
        $i++;
    }
    //
    // Moyenne des moyennes du conteneur
    //
    $call_moy_moy = mysql_query("SELECT round(avg(n.note),1) moyenne FROM cn_notes_conteneurs n, j_eleves_groupes j WHERE (
    j.id_groupe='$id_groupe' AND
    j.periode = '".($num_per+1)."' AND
    j.login = n.login AND
    n.statut='y' AND
    n.id_conteneur='".$id_conteneur[$num_per]."'
    )");
    $moy_moy = mysql_result($call_moy_moy, 0, "moyenne");
    if ($moy_moy != '') {
        echo "<td class=cn><center><b>$moy_moy</b></center></td>\n";
        $data_pdf[$tot_data_pdf][] = $moy_moy;
    } else {
        echo "<td class=cn>&nbsp;</td>\n";
        $data_pdf[$tot_data_pdf][] = "";
    }
    $num_per++;
}
echo "</tr></table>\n";

// Préparation du pdf
$header_pdf=serialize($header_pdf);
$_SESSION['header_pdf']=$header_pdf;
$w_pdf=serialize($w_pdf);
$_SESSION['w_pdf']=$w_pdf;
$data_pdf=serialize($data_pdf);
$_SESSION['data_pdf']=$data_pdf;
echo "<br /><center><a href=\"../fpdf/imprime_pdf.php?titre=$titre_pdf&amp;id_groupe=$id_groupe\" target=\"_blank\">Imprimer au format PDF</a></center>\n";

?>
<script type='text/javascript' language='javascript'>
chargement = true;
</script>
<?php require("../lib/footer.inc.php");?>
