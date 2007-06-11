<?php
/*
 * Last modification  : 22/08/2006
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

$periode_query = mysql_query("select max(num_periode) max from periodes");
$max_periode = mysql_result($periode_query, 0, 'max');

if (isset($_POST['is_posted'])) {
    $msg = '';
    $reg_ok = '';
    // Première boucle sur le nombre de periodes
    $per = 0;
    while ($per < $max_periode) {
        $per++;
        // On dresse la liste de toutes les classes non virtuelles
        $classes_list = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe");
        $nb_classe = mysql_num_rows($classes_list);
        // $nb : nombre de classes ayant un nombre de periodes égal à $per
        $nb=0;
        $nbc = 0;
        while ($nbc < $nb_classe) {
            $modif_classe = 'no';
            $id_classe = mysql_result($classes_list,$nbc,'id');
            $query_per = mysql_query("SELECT p.num_periode FROM classes c, periodes p WHERE (p.id_classe = c.id  and c.id = '".$id_classe."')");
            $nb_periode = mysql_num_rows($query_per);
            if ($nb_periode == $per) {
                // la classe dont l'identifiant est $id_classe a $per périodes
                $temp = "case_".$id_classe;
                if (isset($_POST[$temp])) {
                    $k = '1';
                    While ($k < $per+1) {
                        $temp2 = "nb_".$per."_".$k;
                        if ($_POST[$temp2] != '') {
                            $register = mysql_query("UPDATE periodes SET nom_periode='".$_POST[$temp2]."' where (id_classe='".$id_classe."' and num_periode='".$k."')");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                       }
                        $k++;
                    }
                    $temp2 ="nb_".$per."_reg_suivi_par";
                    if ($_POST[$temp2] != '') {
                        $register = mysql_query("UPDATE classes SET suivi_par='".$_POST[$temp2]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
//                        echo "classe : ".$id_classe." - reg_suivi_par".$per." : ".$_POST[$temp2]."</br>";
                    }
                    $temp2 = "nb_".$per."_reg_formule";
                    if ($_POST[$temp2] != '') {
                        //$register = mysql_query("UPDATE classes SET formule='".$_POST[$temp2]."' where id='".$id_classe."'");
                        $register = mysql_query("UPDATE classes SET formule='".html_entity_decode($_POST[$temp2])."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
//                        echo "classe : ".$id_classe." - reg_formule".$per." : ".$_POST[$temp2]."</br>";
                    }
                    if (isset($_POST['nb_'.$per.'_reg_format'])) {
                        $tab = explode("_", $_POST['nb_'.$per.'_reg_format']);
                        $register = mysql_query("UPDATE classes SET format_nom='".$tab[2]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
//                        echo "classe : ".$id_classe." - ".$_POST['nb_'.$per.'_reg_format']."</br>";
                    }
                    if (isset($_POST['display_rang_'.$per])) {
                        $register = mysql_query("UPDATE classes SET display_rang='".$_POST['display_rang_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }
		    //====================================
		    // AJOUT: boireaus
                    if (isset($_POST['display_address_'.$per])) {
                        $register = mysql_query("UPDATE classes SET display_address='".$_POST['display_address_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }
                    if (isset($_POST['display_coef_'.$per])) {
                        $register = mysql_query("UPDATE classes SET display_coef='".$_POST['display_coef_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }
                    if (isset($_POST['display_nbdev_'.$per])) {
                        $register = mysql_query("UPDATE classes SET display_nbdev='".$_POST['display_nbdev_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }


                    if (isset($_POST['display_moy_gen_'.$per])) {
                        $register = mysql_query("UPDATE classes SET display_moy_gen='".$_POST['display_moy_gen_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }


                    if (isset($_POST['display_mat_cat_'.$per])) {
                        $register = mysql_query("UPDATE classes SET display_mat_cat='".$_POST['display_mat_cat_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }

					if ((isset($_POST['modele_bulletin_'.$per])) AND ($_POST['modele_bulletin_'.$per]!=0)) {
                        $register = mysql_query("UPDATE classes SET modele_bulletin_pdf='".$_POST['modele_bulletin_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }

					if (isset($_POST['rn_nomdev_'.$per])) {
                        $register = mysql_query("UPDATE classes SET rn_nomdev='".$_POST['rn_nomdev_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }

					if (isset($_POST['rn_toutcoefdev_'.$per])) {
                        $register = mysql_query("UPDATE classes SET rn_toutcoefdev='".$_POST['rn_toutcoefdev_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }

					if (isset($_POST['rn_coefdev_si_diff_'.$per])) {
                        $register = mysql_query("UPDATE classes SET rn_coefdev_si_diff='".$_POST['rn_coefdev_si_diff_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }

					if (isset($_POST['rn_datedev_'.$per])) {
                        $register = mysql_query("UPDATE classes SET rn_datedev='".$_POST['rn_datedev_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }

					if (isset($_POST['rn_sign_chefetab_'.$per])) {
                        $register = mysql_query("UPDATE classes SET rn_sign_chefetab='".$_POST['rn_sign_chefetab_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }

					if (isset($_POST['rn_sign_pp_'.$per])) {
                        $register = mysql_query("UPDATE classes SET rn_sign_pp='".$_POST['rn_sign_pp_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }

					if (isset($_POST['rn_sign_resp_'.$per])) {
                        $register = mysql_query("UPDATE classes SET rn_sign_resp='".$_POST['rn_sign_resp_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }


					if(strlen(ereg_replace("[0-9]","",$_POST['rn_sign_nblig_'.$per]))!=0){$_POST['rn_sign_nblig_'.$per]=3;}

					if (isset($_POST['rn_sign_nblig_'.$per])) {
                        $register = mysql_query("UPDATE classes SET rn_sign_nblig='".$_POST['rn_sign_nblig_'.$per]."' where id='".$id_classe."'");
                        if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
                    }


					if (isset($_POST['rn_formule_'.$per])) {
						if ($_POST['rn_formule_'.$per]!='') {
							$register = mysql_query("UPDATE classes SET rn_formule='".$_POST['rn_formule_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}

			// On enregistre les infos relatives aux catégories de matières
			$get_cat = mysql_query("SELECT id, nom_court, priority FROM matieres_categories");
			while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
				$reg_priority = $_POST['priority_'.$row["id"].'_'.$per];
				if (isset($_POST['moyenne_'.$row["id"].'_'.$per])) {$reg_aff_moyenne = 1;} else { $reg_aff_moyenne = 0;}
				if (!is_numeric($reg_priority)) $reg_priority = 0;
				if (!is_numeric($reg_aff_moyenne)) $reg_aff_moyenne = 0;
				$test = mysql_result(mysql_query("select count(classe_id) FROM j_matieres_categories_classes WHERE (categorie_id = '" . $row["id"] . "' and classe_id = '" . $id_classe . "')"), 0);
				if ($test == 0) {
					// Pas d'entrée... on créé
					$res = mysql_query("INSERT INTO j_matieres_categories_classes SET classe_id = '" . $id_classe . "', categorie_id = '" . $row["id"] . "', priority = '" . $reg_priority . "', affiche_moyenne = '" . $reg_aff_moyenne . "'");
				} else {
					// Entrée existante, on met à jour
					$res = mysql_query("UPDATE j_matieres_categories_classes SET priority = '" . $reg_priority . "', affiche_moyenne = '" . $reg_aff_moyenne . "' WHERE (classe_id = '" . $id_classe . "' and categorie_id = '" . $row["id"] . "')");
				}
				if (!$res) {
					$msg .= "<br/>Une erreur s'est produite lors de l'enregistrement des données de catégorie.";
				}
			}

			//====================================
                }
            }
            $nbc++;
        }
    }

    if ($reg_ok=='') {
        $message_enregistrement = "Aucune modification n'a été effectuée !";
        $affiche_message = 'yes';
    } else if ($reg_ok=='yes') {
        $message_enregistrement = "Les modifications ont été effectuées avec succès.";
        $affiche_message = 'yes';
    } else {
        $message_enregistrement = "Il y a eu un problème lors de l'enregistrement des modification.";
        $affiche_message = 'yes';
    }
}

//**************** EN-TETE *****************
$titre_page = "Gestion des classes - Paramétrage des classes par lots";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

If ($max_periode <= 0) {
   echo "Aucune classe comportant des périodes n'a été définie.";
   die();
}
echo "<FORM METHOD=post ACTION=\"classes_param.php\">";
echo "<p class=bold><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>| <INPUT TYPE=SUBMIT VALUE='Enregistrer' /></p>";
echo "Sur cette page, vous pouvez modifier différents paramètres par lots de classes cochées ci-dessous.";
echo "<script language='javascript' type='text/javascript'>
 function checkAll(){
      champs_input=document.getElementsByTagName('input');
      for(i=0;i<champs_input.length;i++){
      type=champs_input[i].getAttribute('type');
      //if(type==\"checkbox\"){
      name=champs_input[i].getAttribute('name');
      if((type==\"checkbox\")&&(name.substr(0,5)=='case_')){
        champs_input[i].checked=true;
      }
    }
    alert(champs_input[i-1])
  }
  function UncheckAll(){
    champs_input=document.getElementsByTagName('input');
    for(i=0;i<champs_input.length;i++){
      type=champs_input[i].getAttribute('type');
      //if(type==\"checkbox\"){
      name=champs_input[i].getAttribute('name');
      if((type==\"checkbox\")&&(name.substr(0,5)=='case_')){
        champs_input[i].checked=false;
      }
    }
  }
</script>\n";
echo "<p><a href='javascript:checkAll();'>Cocher toutes les classes</a> / <a href='javascript:UncheckAll();'>Tout décocher</a></p>\n";

// Première boucle sur le nombre de periodes
$per = 0;
while ($per < $max_periode) {
    $per++;
    // On dresse la liste de toutes les classes non virtuelles
    $classes_list = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe");
    $nb_classe = mysql_num_rows($classes_list);
    // $nb : nombre de classes ayant un nombre de periodes égal à $per
    $nb=0;
    $nbc = 0;
    while ($nbc < $nb_classe) {
        $id_classe = mysql_result($classes_list,$nbc,'id');
        $query_per = mysql_query("SELECT p.num_periode FROM classes c, periodes p WHERE (p.id_classe = c.id  and c.id = '".$id_classe."')");
        $nb_periode = mysql_num_rows($query_per);
        if ($nb_periode == $per) {
            $tab_id_classe[$nb] = $id_classe;
            $tab_nom_classe[$nb] = mysql_result($classes_list,$nbc,'classe');
            $nb++;
        }
        $nbc++;
    }
    If ($nb != 0) {
        echo "<center><p class='grand'>Classes ayant ".$per." période";
        if ($per > 1) echo "s";
        echo "</p></center>";
        // S'il existe des classe ayant un nombre de periodes égal = $per :
        $nb_ligne = intval($nb/3)+1;
        echo "<table width = 100% border=1>";
        $i ='0';
        while ($i < $nb_ligne) {
            echo "<tr>";
            $j = 0;
            while ($j < 3) {
                unset($nom_case);
                $nom_classe = '';
                if (isset($tab_id_classe[$i+$j*$nb_ligne])) $nom_case = "case_".$tab_id_classe[$i+$j*$nb_ligne];
                if (isset($tab_nom_classe[$i+$j*$nb_ligne])) $nom_classe = $tab_nom_classe[$i+$j*$nb_ligne];
                echo "<td>";
                if ($nom_classe != '') echo "<input type=\"checkbox\" name=\"".$nom_case."\" checked />&nbsp;".$nom_classe;
                echo "</td>";
                $j++;
            }
            echo "</tr>";
            $i++;
        }
        echo "</table>";
        ?>
        <p class='bold'>Pour la ou les classe(s) sélectionnée(s) ci-dessus : </p>
        <p>Aucune modification ne sera apportée aux champs laissés vides</p>

        <table width=100% border=2 cellspacing=1  cellpadding=3>
        <tr>
        <td>&nbsp;</td>
        <td>Nom de la période</td>
        </tr>

        <?php
        $k = '1';
        While ($k < $per+1) {
            echo "<tr>";
            echo "<td>Période ".$k."</td>";
            echo "<td><INPUT TYPE=TEXT NAME='nb_".$per."_".$k."' VALUE=\"\" SIZE=30 /></td>";
            echo"</tr>";
            $k++;
        }

        ?>

        </table>
        <p>Prénom et nom du chef d'établissement ou de son représentant apparaissant en bas de chaque bulletin :
        <br /><input type="text" size="30" name=<?php echo "nb_".$per."_reg_suivi_par"; ?> value = "" ></input></p>
        <p>Formule à insérer sur les bulletins (cette formule sera suivie des nom et prénom de la personne désignée ci_dessus :
        <br /><input type="text" size="80" name=<?php echo "nb_".$per."_reg_formule"; ?> value = "" ></input></p>
        <p>Formatage de l'identité des professeurs :

        <br /><input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" value="<?php echo "nb_".$per."_np"; ?>" />Nom Prénom (Durand Albert)
        <br /><input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" value="<?php echo "nb_".$per."_pn"; ?>" />Prénom Nom (Albert Durand)
        <br /><input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" value="<?php echo "nb_".$per."_in"; ?>" />Initiale-Prénom Nom (A. Durand)
        <br /><input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" value="<?php echo "nb_".$per."_ni"; ?>" />Initiale-Prénom Nom (Durand A.)
        <br /><input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" value="<?php echo "nb_".$per."_cnp"; ?>" />Civilité Nom Prénom (M. Durand Albert)
        <br /><input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" value="<?php echo "nb_".$per."_cpn"; ?>" />Civilité Prénom Nom (M. Albert Durand)
        <br /><input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" value="<?php echo "nb_".$per."_cin"; ?>" />Civ. initiale-Prénom Nom (M. A. Durand)
        <br /><input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" value="<?php echo "nb_".$per."_cni"; ?>" />Civ. Nom initiale-Prénom (M. Durand A.)
        <br />
<br />
<br />
<table border='0'>
<tr>
	<td colspan='3'>
	  <h2><b>Paramètres généraux : </b></h2>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-weight: bold;">
    Afficher les rubriques de matières sur le bulletin (HTML),<br />les relevés de notes (HTML), et les outils de visualisation :
    </td>
    <td>
	<?php
		echo "<input type='checkbox' value='y' name='display_mat_cat_".$per."' />\n";
	?>
    </td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-weight: bold;" valign="top">
	Paramétrage des catégories de matière pour cette classe<br />
	(<i>la prise en compte de ce paramètrage est conditionnée<br />
	par le fait de cocher la case<br />
	'Afficher les rubriques de matières...' ci-dessus</i>)
	</td>
	<td>
		<table style='border: 1px solid black;'>
		<tr>
			<td style='width: auto;'>Catégorie</td><td style='width: 100px; text-align: center;'>Priorité d'affichage</td><td style='width: 100px; text-align: center;'>Afficher la moyenne sur le bulletin</td>
		</tr>
		<?php
		$get_cat = mysql_query("SELECT id, nom_court, priority FROM matieres_categories");
		while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
			$current_priority = $row["priority"];
			$current_affiche_moyenne = "0";

			echo "<tr>\n";
			echo "<td style='padding: 5px;'>".$row["nom_court"]."</td>\n";
			echo "<td style='padding: 5px; text-align: center;'>\n";
			echo "<select name='priority_".$row["id"]."_".$per."' size='1'>\n";
			for ($i=0;$i<11;$i++) {
				echo "<option value='$i'";
				//if ($current_priority == $i) echo " SELECTED";
				echo ">$i</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";
			echo "<td style='padding: 5px; text-align: center;'>\n";
			echo "<input type='checkbox' name='moyenne_".$row["id"]."_".$per."'";
			//if ($current_affiche_moyenne == '1') echo " CHECKED";
			echo " />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		?>
		</table>
	</td>
</tr>
    <tr>
	<td colspan='3'>
	  <h2><b>Paramètres bulletin HTML : </b></h2>
	</td>
	<td>
	</td>
	</tr>
	<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td valign="top">
        <b>Afficher sur le bulletin le rang de chaque élève : </b>
	</td>
	<td valign="bottom">
        <input type="radio" name="<?php echo "display_rang_".$per; ?>" value="y" />Oui
        <input type="radio" name="<?php echo "display_rang_".$per; ?>" value="n" />Non
	</td>
	</tr>

	<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td valign="top">
	<b>Afficher le bloc adresse du responsable de l'élève : </b>
	</td>
	<td valign="bottom">
        <input type="radio" name="<?php echo "display_address_".$per; ?>" value="y" />Oui
        <input type="radio" name="<?php echo "display_address_".$per; ?>" value="n" />Non
	</td>
	</tr>

	<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td valign="top">
	<b>Afficher les coefficients des matières<br />(<i>uniquement si au moins un coef différent de 0</i>) : </b>
	</td>
	<td valign="bottom">
        <input type="radio" name="<?php echo "display_coef_".$per; ?>" value="y" />Oui
        <input type="radio" name="<?php echo "display_coef_".$per; ?>" value="n" />Non
	</td>
	</tr>

	<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td valign="top">
	<b>Afficher les moyennes générales sur les bulletins<br />(<i>uniquement si au moins un coef différent de 0</i>) : </b>
	</td>
	<td valign="bottom">
        <input type="radio" name="<?php echo "display_moy_gen_".$per; ?>" value="y" />Oui
        <input type="radio" name="<?php echo "display_moy_gen_".$per; ?>" value="n" />Non
	</td>
	</tr>

	<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td valign="top">
	<b>Afficher sur le bulletin le nombre de devoirs : </b>
	</td>
	<td valign="bottom">
        <input type="radio" name="<?php echo "display_nbdev_".$per; ?>" value="y" />Oui
        <input type="radio" name="<?php echo "display_nbdev_".$per; ?>" value="n" />Non
	</td>
	</tr>
    <tr>
	<td colspan='3'>
	  <h2><b>Paramètres bulletin PDF : </b></h2>
	</td>
	<td>
	</td>
	</tr>
	<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
	   Sélectionner le modèle de bulletin pour l'impression en PDF :
	</td>
	<td><?PHP
		echo "<select tabindex=\"5\" name=\"modele_bulletin_".$per."\">";
		// sélection des modèle des bulletins.
	    $requete_modele = mysql_query('SELECT id_model_bulletin, nom_model_bulletin FROM '.$prefix_base.'model_bulletin ORDER BY '.$prefix_base.'model_bulletin.nom_model_bulletin ASC');
		echo "<option value=\"0\">Aucun changement</option>";
		while($donner_modele = mysql_fetch_array($requete_modele)) {
		    echo "<option value=\"".$donner_modele['id_model_bulletin']."\"";
			echo ">".ucfirst($donner_modele['nom_model_bulletin'])."</option>\n";
		}
		 echo "</select>\n";
		?>
	</td>
</tr>


<!-- ========================================= -->
<tr>
	<td colspan='3'>
	  <h2><b>Paramètres des relevés de notes : </b></h2>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Afficher le nom des devoirs :</td>
    <td>
        <input type="radio" name="<?php echo "rn_nomdev_".$per; ?>" value="y" />Oui
        <input type="radio" name="<?php echo "rn_nomdev_".$per; ?>" value="n" />Non
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Afficher tous les coefficients des devoirs :</td>
    <td>
        <input type="radio" name="<?php echo "rn_toutcoefdev_".$per; ?>" value="y" />Oui
        <input type="radio" name="<?php echo "rn_toutcoefdev_".$per; ?>" value="n" />Non
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Afficher les coefficients des devoirs si des coefficients différents sont présents :</td>
    <td>
        <input type="radio" name="<?php echo "rn_coefdev_si_diff_".$per; ?>" value="y" />Oui
        <input type="radio" name="<?php echo "rn_coefdev_si_diff_".$per; ?>" value="n" />Non
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Afficher les dates des devoirs :</td>
    <td>
        <input type="radio" name="<?php echo "rn_datedev_".$per; ?>" value="y" />Oui
        <input type="radio" name="<?php echo "rn_datedev_".$per; ?>" value="n" />Non
	</td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td>Formule/Message à insérer sous le relevé de notes :</td>
	<td><input type=text size=40 name="rn_formule_<?php echo $per;?>" value="" /></td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Afficher une case pour la signature du chef d'établissement :</td>
    <td>
        <input type="radio" name="<?php echo "rn_sign_chefetab_".$per; ?>" value="y" />Oui
        <input type="radio" name="<?php echo "rn_sign_chefetab_".$per; ?>" value="n" />Non
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Afficher une case pour la signature du prof principal :</td>
    <td>
        <input type="radio" name="<?php echo "rn_sign_pp_".$per; ?>" value="y" />Oui
        <input type="radio" name="<?php echo "rn_sign_pp_".$per; ?>" value="n" />Non
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Afficher une case pour la signature des parents/responsables :</td>
    <td>
        <input type="radio" name="<?php echo "rn_sign_resp_".$per; ?>" value="y" />Oui
        <input type="radio" name="<?php echo "rn_sign_resp_".$per; ?>" value="n" />Non
	</td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Nombre de lignes pour la signature :</td>
    <td><input type="text" name="rn_sign_nblig_<?php echo $per;?>" value="" size="3" /></td>
</tr>


</table>
<hr />
<?php

    }
}


?>

<center><INPUT TYPE=SUBMIT VALUE='Enregistrer' /></center>
<input type=hidden name=is_posted value="yes" />
</FORM>
<?php require("../lib/footer.inc.php");?>