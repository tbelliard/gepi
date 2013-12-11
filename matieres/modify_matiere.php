<?php
/*
 * $Id$
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

//debug_var();

if (isset($_POST['isposted'])) {
	check_token();
    $ok = 'yes';
    $ok_categorie = 'yes';
    if (isset($_POST['reg_current_matiere'])) {
        // On vérifie d'abord que l'identifiant est constitué uniquement de lettres et de chiffres :
        $matiere_name = $_POST['reg_current_matiere'];
        if ((!isset($_POST['matiere_categorie']))||(!is_numeric($_POST['matiere_categorie']))) {
            // On empêche les mise à jour globale automatiques, car on n'est pas sûr de ce qui s'est passé si l'ID n'est pas numérique...
            //$ok = "no";
            $ok_categorie = 'no';
            $matiere_categorie = "0";
        } else {
            $matiere_categorie = $_POST['matiere_categorie'];
        }
        //if (ereg ("^[a-zA-Z_]{1}[a-zA-Z0-9_]{1,19}$", $matiere_name)) {
        if (preg_match("/^[a-zA-Z_]{1}[a-zA-Z0-9_-]{1,19}$/", $matiere_name)) {
            $verify_query = mysqli_query($GLOBALS["mysqli"], "SELECT * from matieres WHERE matiere='$matiere_name'");
            $verify = mysqli_num_rows($verify_query);
            if ($verify == 0) {
                //========================
                // MODIF: boireaus
                // Quand on poste un &, c'est un &amp; qui est reçu.
                //$matiere_nom_complet = $_POST['matiere_nom_complet'];
				//echo "\$matiere_nom_complet=$matiere_nom_complet<br />\n";
                $matiere_nom_complet = html_entity_decode($_POST['matiere_nom_complet']);
				//echo "\$matiere_nom_complet=$matiere_nom_complet<br />\n";
                //========================
                $matiere_priorite = $_POST['matiere_priorite'];
                $sql="INSERT INTO matieres SET matiere='".$matiere_name."', nom_complet='".$matiere_nom_complet."', priority='".$matiere_priorite."', categorie_id = '" . $matiere_categorie . "',matiere_aid='n',matiere_atelier='n';";
				//echo "$sql<br />\n";
                $register_matiere = mysqli_query($GLOBALS["mysqli"], $sql);
                if (!$register_matiere) {
                    $msg = "Une erreur s'est produite lors de l'enregistrement de la nouvelle matière. <br />";
                    $ok = 'no';
                } else {
                    $msg = "La nouvelle matière a bien été enregistrée. <br />";
                }
            } else {
                $msg = "Cette matière existe déjà !! <br />";
                $ok = 'no';
            }
        } else {
            $msg = "L'identifiant de matière doit être constitué uniquement de lettres et de chiffres avec un maximum de 19 caractères ! <br />";
            $ok = 'no';
        }
    } else {

        $matiere_nom_complet = $_POST['matiere_nom_complet'];
		$matiere_nom_complet = html_entity_decode($_POST['matiere_nom_complet']);
        $matiere_priorite = $_POST['matiere_priorite'];
        $matiere_name = $_POST['matiere_name'];
        if ((!isset($_POST['matiere_categorie']))||(!is_numeric($_POST['matiere_categorie']))) {
            $matiere_categorie = "0";
            $ok_categorie = 'no';
        } else {
            $matiere_categorie = $_POST['matiere_categorie'];
        }

        $sql="UPDATE matieres SET nom_complet='".$matiere_nom_complet."', priority='".$matiere_priorite."', categorie_id = '" . $matiere_categorie . "' WHERE matiere='".$matiere_name."';";
		//echo "$sql<br />\n";
        $register_matiere = mysqli_query($GLOBALS["mysqli"], $sql);

        if (!$register_matiere) {
            $msg = "Une erreur s'est produite lors de la modification de la matière <br />";
            $ok = 'no';
        } else {
            $msg = "Les modifications ont été enregistrées ! <br />";
        }
    }

    if ((isset($_POST['force_defaut'])) and ($ok == 'yes')) {
        $sql="UPDATE j_groupes_matieres jgm, j_groupes_classes jgc SET jgc.priorite='".$matiere_priorite."'
        WHERE (jgc.id_groupe = jgm.id_groupe AND jgm.id_matiere='".$matiere_name."')";
        //echo "$sql<br />";
        //$msg = rawurlencode($sql);
        $req = mysqli_query($GLOBALS["mysqli"], $sql);
    }

    if ((isset($_POST['force_defaut_categorie'])) and ($ok == 'yes') and ($ok_categorie == 'yes')) {
        $sql="UPDATE j_groupes_classes jgc, j_groupes_matieres jgm SET jgc.categorie_id='".$matiere_categorie."'
        WHERE (jgc.id_groupe = jgm.id_groupe AND jgm.id_matiere='".$matiere_name."')";
        //echo "$sql<br />";
        //$msg = rawurlencode($sql);
        $req = mysqli_query($GLOBALS["mysqli"], $sql);
    }

	if($ok=='yes') {
		$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : NULL;
		if(isset($login_prof)) {
			// Récupérer la liste des profs actuellement associés
			$tab_profs_associes=array();
			$sql="SELECT u.login FROM j_professeurs_matieres jpm, utilisateurs u WHERE jpm.id_professeur=u.login and id_matiere='$matiere_name' ORDER BY u.nom, u.prenom;";
			//echo "$sql<br />\n";
			$res_profs=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_profs)>0) {
				while($lig=mysqli_fetch_object($res_profs)) {
					$tab_profs_associes[]=$lig->login;
				}
			}
	
			$nb_inser=0;
			for($loop=0;$loop<count($login_prof);$loop++) {
				if(!in_array($login_prof[$loop], $tab_profs_associes)) {
					// Recherche de l'ordre matière le plus élevé pour ce prof
					$sql="SELECT MAX(ordre_matieres) max_ordre FROM j_professeurs_matieres WHERE id_professeur='".$login_prof[$loop]."';";
					//echo "$sql<br />\n";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)==0) {
						$ordre_matieres=1;
					}
					else {
						$ordre_matieres=old_mysql_result($res, 0, "max_ordre")+1;
					}
	
					// On ajoute le prof
					$sql="INSERT INTO j_professeurs_matieres SET id_professeur='$login_prof[$loop]', id_matiere='$matiere_name', ordre_matieres='$ordre_matieres';";
					//echo "$sql<br />\n";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'association de ".$login_prof[$loop]." avec la matière $matiere_name<br />";
					}
					else {
						$nb_inser++;
					}
				}

			}
	
			if($nb_inser>0) {
				$msg.="$nb_inser professeur(s) a(ont) été associé(s) avec la matière $matiere_name<br />";
			}
	
			$nb_suppr=0;
			for($loop=0;$loop<count($tab_profs_associes);$loop++) {
				if(!in_array($tab_profs_associes[$loop], $login_prof)) {
					$sql="SELECT 1=1 FROM j_groupes_professeurs jgp, j_groupes_matieres jgm WHERE (jgp.login='".$tab_profs_associes[$loop]."' AND jgm.id_matiere='$matiere_name' AND jgm.id_groupe=jgp.id_groupe)";
					//echo "$sql<br />";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)==0) {
						/*
						$sql="SELECT ordre_matieres FROM j_professeurs_matieres WHERE id_professeur='$login_prof' AND id_matiere='$matiere_name';";
						$res=mysql_query($sql);
						*/
	
						$sql="DELETE FROM j_professeurs_matieres WHERE id_professeur='".$tab_profs_associes[$loop]."' AND id_matiere='$matiere_name';";
						//echo "$sql<br />\n";
						$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$suppr) {
							$msg.="Erreur lors de la suppression de l'association de ".$tab_profs_associes[$loop]." avec la matière $matiere_name<br />";
						}
						else {
							$nb_suppr++;
						}
					}
					else {
						$msg.="Dissociation impossible : Le professeur ".$tab_profs_associes[$loop]." enseigne la matière $matiere_name dans un ou des enseignements.<br />";
					}
				}
			}
	
			if($nb_suppr>0) {
				$msg.="$nb_suppr professeur(s) a(ont) été dissocié(s) de la matière $matiere_name<br />";
			}
	
		}

	}

	//$msg = rawurlencode($msg);
    header("location: index.php?msg=$msg");
    die();

}

$themessage = 'Des modifications ont été effectuées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *******************************
$titre_page = "Gestion des matières | Modifier une matière";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE ****************************
?>
<form enctype="multipart/form-data" action="modify_matiere.php" method=post>
<p class=bold><a href="index.php"<?php echo insert_confirm_abandon();?>><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <input type=submit value=Enregistrer></input>
</p>
<?php
echo add_token_field();
// On va chercher les infos de la matière que l'on souhaite modifier
if (isset($_GET['current_matiere'])) {
    $call_data = mysqli_query($GLOBALS["mysqli"], "SELECT nom_complet, priority, categorie_id from matieres WHERE matiere='".$_GET['current_matiere']."'");
    $matiere_nom_complet = old_mysql_result($call_data, 0, "nom_complet");
    $matiere_priorite = old_mysql_result($call_data, 0, "priority");
    $matiere_cat_id = old_mysql_result($call_data, 0, "categorie_id");
    $current_matiere = $_GET['current_matiere'];
} else {
    $matiere_nom_complet = "";
    $matiere_priorite = "0";
    $current_matiere = "";
    $matiere_cat_id = "0";
}
?>

<div style='float:right; width: 20em; border: 1px solid black; margin-left: 1em;'>
<?php
	$tab_profs_associes=array();
	if($current_matiere!="") {
		$sql="SELECT u.login FROM j_professeurs_matieres jpm, utilisateurs u WHERE jpm.id_professeur=u.login and id_matiere='$current_matiere' ORDER BY u.nom, u.prenom;";
		$res_profs=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_profs)>0) {
			while($lig=mysqli_fetch_object($res_profs)) {
				$tab_profs_associes[]=$lig->login;
			}
		}

		if(count($tab_profs_associes)>0) {
			echo "<div style='float:right; width:16px;'><a href='../eleves/recherche.php?is_posted_recherche2b=y&amp;rech_matiere[]=".$current_matiere.add_token_in_url()."' title=\"Extraire la liste des professeurs (qu'ils soient associés à la matière dans des enseignements ou non).\" target='_blank'><img src='../images/group16.png' class='icone16' /></a></div>";
			if(count($tab_profs_associes)>1) {
				echo "<p class='bold'>Les professeurs associés sont&nbsp;<br />\n";
			}
			elseif(count($tab_profs_associes)==1) {
				echo "<p class='bold'>Un professeur est associé&nbsp;<br />\n";
			}
			echo "<br />\n";
			echo "<table class='boireaus' style='margin-left: 1em;'>\n";
			$alt=1;
			for($loop=0;$loop<count($tab_profs_associes);$loop++) {
				$alt=$alt*(-1);
				//echo civ_nom_prenom($tab_profs_associes[$loop],"ini")."<br />\n";
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td>\n";
				echo civ_nom_prenom($tab_profs_associes[$loop],"ini");
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		}
	}

	$cpt=0;
	$sql="SELECT DISTINCT u.login,u.nom,u.prenom,u.civilite FROM utilisateurs u WHERE u.statut='professeur' AND u.etat='actif' ORDER BY u.nom, u.prenom;";
	$res_profs=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_profs)>0) {
		echo "<p class='bold'>Associer des professeurs&nbsp;:</p>\n";
		//$cpt=0;
		while($lig=mysqli_fetch_object($res_profs)) {
			echo "<input type='checkbox' name='login_prof[]' id='login_prof_$cpt' value='$lig->login' ";
			echo "onchange=\"checkbox_change($cpt)\" ";
			if(in_array($lig->login,$tab_profs_associes)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
			echo "/><label for='login_prof_$cpt'><span id='texte_login_prof_$cpt'$temp_style>".$lig->civilite." ".$lig->nom." ".mb_substr($lig->prenom,0,1).".</span></label><br />\n";
			$cpt++;
		}
	}

	echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('login_prof_'+cpt)) {
		if(document.getElementById('login_prof_'+cpt).checked) {
			document.getElementById('texte_login_prof_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_login_prof_'+cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";

?>
</div>

<table>
<tr>
<td>Nom de matière : </td>
<td>
<?php
if (!isset($_GET['current_matiere'])) {
    echo "<input type=text size='19' maxlength='19' name='reg_current_matiere' onchange='changement()' /> (<span style='font-style: italic; font-size: small;'>19 caractères maximum</span>)";
} else {
    echo "<input type=hidden name=matiere_name value=\"".$current_matiere."\" />".$current_matiere;
}
?>
</td></tr>
<tr>
<td>Nom complet : </td>
<td><input type='text' name='matiere_nom_complet' value="<?php echo $matiere_nom_complet;?>" onchange='changement()' /></td>
</tr>
<tr>
<td>Priorité d'affichage par défaut</td>
<td>
<?php
echo "<select size='1' name='matiere_priorite' onchange='changement()' >\n";
$k = '0';
echo "<option value=0>0</option>\n";
$k='11';
$j = '1';
//while ($k < '51'){
while ($k < 110){
    echo "<option value=$k"; if ($matiere_priorite == $k) {echo " SELECTED";} echo ">$j</option>\n";
    $k++;
    $j = $k - 10;
}
echo "</select></td>";
?>
<tr>
<td>Catégorie par défaut</td>
<td>
<?php
echo "<select size='1' name='matiere_categorie' onchange='changement()' >\n";
$get_cat = mysqli_query($GLOBALS["mysqli"], "SELECT id, nom_court FROM matieres_categories");
$test = mysqli_num_rows($get_cat);

if ($test == 0) {
    echo "<option disabled>Aucune catégorie définie</option>";
} else {
    while ($row = mysqli_fetch_array($get_cat,  MYSQLI_ASSOC)) {
        echo "<option value='".$row["id"]."'";
        if ($matiere_cat_id == $row["id"]) echo " SELECTED";
        echo ">".html_entity_decode($row["nom_court"])."</option>";
    }
}
echo "</select>";

?>
</td>
</table>
<p>
<label for='force_defaut' style='cursor: pointer;'><b>Pour toutes les classes, forcer la valeur de la priorité d'affichage à la valeur par défaut ci-dessus :</b>
<input type="checkbox" name="force_defaut" id="force_defaut" onchange="changement()" checked /></label>
</p>
<p>
<label for='force_defaut_categorie' style='cursor: pointer;'><b>Pour toutes les classes, forcer la valeur de la catégorie de matière à la valeur par défaut ci-dessus :</b>
<input type="checkbox" name="force_defaut_categorie" id="force_defaut_categorie" onchange="changement()" checked /></label>
</p>
<input type="hidden" name="isposted" value="yes" />
</form>
<!-- ============================================================================ -->
<hr />

<?php
if((isset($current_matiere))&&($current_matiere!="")) {
	$sql="SELECT DISTINCT g.id, g.name, g.description FROM groupes g, j_groupes_matieres jgm, j_groupes_classes jgc, classes c WHERE jgm.id_matiere='".$current_matiere."' AND jgm.id_groupe=g.id AND jgc.id_groupe=g.id AND jgc.id_classe=c.id ORDER BY c.classe, c.nom_complet";
	//echo "$sql<br />";
	$res_ens=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_ens=mysqli_num_rows($res_ens);
	if($nb_ens==0) {
		echo "<p>Aucun enseignement n'est associé à la matière $current_matiere.</p>\n";
	}
	else {
		echo "<p>$nb_ens enseignement(s) associé(s) à la matière $current_matiere&nbsp;: ";
		$chaine_domaines="";
		for($loop=0;$loop<count($tab_domaines);$loop++) {
			$chaine_domaines.="&amp;rech_domaine[]=".$tab_domaines[$loop];
		}
		echo "<a href='../eleves/recherche.php?is_posted_recherche2=y&amp;rech_matiere[]=".$current_matiere.$chaine_domaines.add_token_in_url()."' title=\"Extraire la liste des professeurs (associés aux enseignements ci-dessous).\" target='_blank'><img src='../images/group16.png' class='icone16' /></a>";
		echo "<br />";
		while($lig_ens=mysqli_fetch_object($res_ens)) {

			$sql="SELECT c.id, c.classe FROM j_groupes_classes jgc, classes c WHERE jgc.id_classe=c.id AND jgc.id_groupe='$lig_ens->id' ORDER BY c.classe, c.nom_complet;";
			$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
			$chaine_clas="";
			if(mysqli_num_rows($res_clas)>0) {
				$cpt_clas=0;
				while($lig_clas=mysqli_fetch_object($res_clas)) {
					if($cpt_clas>0) {$chaine_clas.=", ";}
					$chaine_clas.="<a href='../groupes/edit_class.php?id_classe=$lig_clas->id' title=\"Accéder à la liste des enseignements de $lig_clas->classe\">$lig_clas->classe</a>";
					$cpt_clas++;
				}
			}

			$sql="SELECT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.login=u.login AND jgp.id_groupe='$lig_ens->id' ORDER BY u.nom, u.prenom;";
			$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
			$chaine_prof="";
			if(mysqli_num_rows($res_prof)>0) {
				$cpt_prof=0;
				while($lig_prof=mysqli_fetch_object($res_prof)) {
					if($cpt_prof>0) {$chaine_prof.=", ";}
					$chaine_prof.="<a href='../utilisateurs/modify_user.php?user_login=$lig_prof->login' title=\"Modifier l'utilisateur $lig_prof->login\">$lig_prof->civilite $lig_prof->nom ".mb_substr($lig_prof->prenom,0,1)."</a>";
					$cpt_prof++;
				}
			}

			echo "<a href='../groupes/edit_group.php?id_groupe=$lig_ens->id' title=\"Modifier l'enseignement n°$lig_ens->id\">$lig_ens->name (<em>$lig_ens->description</em>)</a>";
			if($chaine_clas!="") {
				echo " en $chaine_clas";
			}
			if($chaine_prof!="") {
				echo " avec $chaine_prof";
			}
			echo "<br />";
		}
		echo "</p>\n";
	}
	echo "<hr />\n";
}
?>

<p><b>Aide :</b></p>
<ul>
<li><b>Nom de matière</b>
<br /><br />Il s'agit de l'identifiant de la matière. Il est constitué au maximum de 20 caractères : lettres, chiffres ou "_" et ne doit pas commencer par un chiffre.
Une fois enregistré, il n'est plus possible de le modifier.
</li>
<li><b>Nom complet</b>
<br /><br />Il s'agit de l'intitulé de la matière, tel qu'il apparaît aux utilisateurs sur les bulletins, les relevés de notes, etc.
Une fois enregistré, il est toujours possible de le modifier.
</li>
<li><b>Priorité d'affichage par défaut</b>
<br /><br />Permet de définir l'ordre d'affichage par défaut des matières dans le bulletin scolaire et dans les tableaux récapitulatifs des moyennes.
<br /><b>Remarques :</b>
<ul>
<li>Lors de la gestion des matières dans une classe, c'est cette valeur qui est enregistrée par défaut. Il est alors possible de changer la valeur pour une classe donnée.</li>
<li>Il est possible d'attribuer le même poids à plusieurs matières n'apparaissant pas sur un même bulletin. Par exemple, toutes les LV1 peuvent avoir le même poids, etc.</li>
<li>Si deux matières apparaissant sur un même bulletin ont la même priorité, GEPI affiche la première matière extraite de la base.</li>
</ul>
</ul>
<!--/li>
</ul-->
<?php require("../lib/footer.inc.php");?>
