<?php
/*
 * $Id$
 *
 * Copyright 2001-2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$msg = '';
$error = false;
if (isset($_POST['is_posted'])) {
    // Les données ont été postées, on met à jour
    check_token();

    $get_all_matieres = mysql_query("SELECT matiere, priority, categorie_id FROM matieres");
    while ($row = mysql_fetch_object($get_all_matieres)) {
        // On passe les matières une par une et on met à jour
        $varname_p = my_strtolower($row->matiere)."_priorite";
		//echo "<p>Test \$varname_p=$varname_p<br />";
        if (isset($_POST[$varname_p])) {
			//echo "isset(\$_POST[$varname_p]) oui<br />";
            if (is_numeric($_POST[$varname_p])) {
				//echo "is_numeric(\$_POST[$varname_p]) oui<br />";
            	// La valeur est correcte
            	if ($_POST[$varname_p] != $row->priority) {
                // On a une valeur différente. On met à jour.
                    $res = mysql_query("UPDATE matieres SET priority = '".$_POST[$varname_p] . "' WHERE matiere = '" . $row->matiere . "'");
                    if (!$res) {
                        $msg .= "<br/>Erreur lors de la mise à jour de la priorité de la matière ".$row->matiere.".";
                        $error = true;
                    }
                }
                // On met à jour toutes les priorités dans les classes si ça a été demandé
                if (isset($_POST['forcer_defauts']) AND $_POST['forcer_defauts'] == "yes") {
			        $sql="UPDATE j_groupes_matieres jgm, j_groupes_classes jgc SET jgc.priorite='".$_POST[$varname_p]."' " .
			        		"WHERE (jgc.id_groupe = jgm.id_groupe AND jgm.id_matiere='".$row->matiere."')";
					//echo "$sql<br />";
					$req = mysql_query($sql);
			        if (!$req) {
			        	$msg .="<br/>Erreur lors de la mise à jour de la priorité de matière dans les classes pour la matière ".$row->matiere.".";
			        	$error = true;
			        }
                }
            }
        }

        // La même chose pour la catégorie de matière
        $varname_c = my_strtolower($row->matiere)."_categorie";
        if (isset($_POST[$varname_c])) {
        	if (is_numeric($_POST[$varname_c])) {
        		// On a une valeur correcte. On y va !
            	if ($_POST[$varname_c] != $row->categorie_id) {
                	// On a une valeur différente. On met à jour.
                    $res = mysql_query("UPDATE matieres SET categorie_id = '".$_POST[$varname_c] . "' WHERE matiere = '" . $row->matiere . "'");
                    if (!$res) {
                        $msg .= "<br/>Erreur lors de la mise à jour de la catégorie de la matière ".$row->matiere.".";
                        $error = true;
                    }
                }

                // On met à jour toutes les catégories dans les classes si ça a été demandé
                if (isset($_POST['forcer_defauts']) AND $_POST['forcer_defauts'] == "yes") {
			        $req = mysql_query("UPDATE j_groupes_classes jgc, j_groupes_matieres jgm SET jgc.categorie_id='".$_POST[$varname_c]."' " .
			        		"WHERE (jgc.id_groupe = jgm.id_groupe AND jgm.id_matiere='".$row->matiere."')");
			        if (!$req) {
			        	$msg .="<br/>Erreur lors de la mise à jour de la catégorie de matière dans les classes pour la matière ".$row->matiere.".";
			        	$error = true;
			        }
                }
            }
        }


    }
    if ($error) {
        $msg .= "<br/>Des erreurs se sont produites lors de la mise à jour des données.";
    } else {
        $msg .= "<br/>Mise à jour effectuée.";
    }
}

$themessage = 'Des modifications ont été effectuées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Gestion des matières";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>

<p class=bold><a href="../accueil_admin.php"<?php echo insert_confirm_abandon();?>><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
 | <a href="modify_matiere.php"<?php echo insert_confirm_abandon();?>>Ajouter matière</a>
 | <a href='matieres_param.php'<?php echo insert_confirm_abandon();?>>Paramétrage de plusieurs matières par lots</a>
 | <a href='matieres_categories.php'<?php echo insert_confirm_abandon();?>>Editer les catégories de matières</a>
 | <a href='matieres_csv.php'<?php echo insert_confirm_abandon();?>>Importer un CSV de la liste des matières</a>
</p>

<?php
	$tab_priorites_categories=array();
	$temoin_pb_ordre_categories="n";
	$sql="select * from matieres_categories;";
	$res_cat=mysql_query($sql);
	if(mysql_num_rows($res_cat)>0) {
		while($lig_cat=mysql_fetch_object($res_cat)) {
			$current_priority=$lig_cat->priority;
			if(in_array($current_priority, $tab_priorites_categories)) {
				$temoin_pb_ordre_categories="y";
			}
			$tab_priorites_categories[]=$current_priority;
		}
	}

	if($temoin_pb_ordre_categories=="y") {
		echo "<p style='color:red; text-indent:-6em;padding-left:6em;'><strong>Anomalie&nbsp;:</strong> Les catégories de matières ne doivent pas avoir le même rang.<br />Cela risque de provoquer des problèmes sur les bulletins.<br />Vous devriez corriger les ordres de catégories de matières dans <a href='matieres_categories.php'".insert_confirm_abandon().">Editer les catégories de matières</a></p>\n";
	}
?>

<form enctype="multipart/form-data" action="index.php" method=post>
<?php
echo add_token_field();
?>
<input type='submit' value='Enregistrer' style='margin-left: 10%; margin-bottom: 0px;' />
<p><label for='forcer_defauts' style='cursor: pointer;'>Pour toutes les classes, forcer les valeurs définies pour toutes les matières ci-dessous <input type='checkbox' name='forcer_defauts' id='forcer_defauts' value='yes' /></label>
<br/><b>Attention !</b> Cette fonction effacera tous vos changements manuels concernant la priorité et la catégorie de chaque matière dans les différentes classes !</p>
<input type='hidden' name='is_posted' value='1' />
<table class='boireaus' width = '100%' cellpadding = '5'>
<tr>
    <th>
    <p class='bold'><a href='./index.php?orderby=m.matiere'<?php echo insert_confirm_abandon();?>>Identifiant matière</a><br />
    <a href="javascript:afficher_masquer_matieres_sans_grp('afficher')"><img src="../images/icons/visible.png" width="19" height="16" title="Afficher les matières sans enseignement associé" alt="Afficher les matières sans enseignement associé" /></a> / <a href="javascript:afficher_masquer_matieres_sans_grp('masquer')"><img src="../images/icons/invisible.png" width="19" height="16" title="Masquer les matières sans enseignement associé" alt="Masquer les matières sans enseignement associé" /></a>
    </p></th>
    <th><p class='bold'><a href='./index.php?orderby=m.nom_complet'<?php echo insert_confirm_abandon();?>>Nom complet</a></p></th>
    <th><p class='bold'><a href='./index.php?orderby=m.priority,m.nom_complet'<?php echo insert_confirm_abandon();?>>Ordre d'affichage<br />par défaut</a></p></th>
    <th><p class='bold'>Catégorie par défaut</p></th>
    <th><p class='bold'>Supprimer</p></th>
</tr>
<?php
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : (isset($_POST['orderby']) ? $_POST["orderby"] : 'm.priority,m.nom_complet');
if ($orderby != "m.matiere" AND $orderby != "m.nom_complet" AND $orderby != "m.priority,m.matiere") {
    $orderby = "m.priority,m.nom_complet";
}
$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];
// On va chercher les classes déjà existantes, et on les affiche.

$categories=array();
$call_data = mysql_query("SELECT m.matiere, m.nom_complet, m.priority, m.categorie_id FROM matieres m ORDER BY $orderby");
$get_cat = mysql_query("SELECT id, nom_court FROM matieres_categories");
while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
    $categories[] = $row;
}

$nombre_lignes = mysql_num_rows($call_data);
$i = 0;
$alt=1;
while ($i < $nombre_lignes){
    $alt=$alt*(-1);

	$current_matiere = mysql_result($call_data, $i, "matiere");
	$current_matiere_nom = mysql_result($call_data, $i, "nom_complet");
    $current_matiere_priorite = mysql_result($call_data, $i, "priority");
    $current_matiere_categorie_id = mysql_result($call_data, $i, "categorie_id");

    if ($current_matiere_priorite > 1) {$current_matiere_priorite -= 10;}

	$sql="SELECT 1=1 FROM j_groupes_matieres WHERE id_matiere='$current_matiere';";
	$res_grp_associes=mysql_query($sql);
	$nb_grp_assoc=mysql_num_rows($res_grp_associes);

	if($nb_grp_assoc==0) {
		echo "<tr style='background-color:grey;' class='white_hover' id='tr_sans_grp_assoc_$i'><td title=\"Aucun enseignement n'est associé à cette matière\"><a href='modify_matiere.php?current_matiere=$current_matiere'".insert_confirm_abandon()." style=\"color:#0000AA\">$current_matiere</a></td>\n";
	}
	else {
		echo "<tr class='lig$alt white_hover'><td title=\"$nb_grp_assoc enseignement(s) associé(s) à cette matière\"><a href='modify_matiere.php?current_matiere=$current_matiere'".insert_confirm_abandon().">$current_matiere</a></td>\n";
	}
    //echo "<td>$current_matiere_nom</td>";
    //echo "<td>".html_entity_decode($current_matiere_nom)."</td>";
    echo "<td>".htmlspecialchars($current_matiere_nom)."</td>\n";
    // La priorité par défaut
    echo "<td>\n";
    echo "<select size=1 name='" . my_strtolower($current_matiere)."_priorite' onchange='changement()'>\n";
    $k = '0';
    echo "<option value=0>0</option>\n";
    $k='11';
    $j = '1';
    //while ($k < '51'){
    while ($k < '61'){
        echo "<option value=$k"; if ($current_matiere_priorite == $j) {echo " SELECTED";} echo ">$j</option>\n";
        $k++;
        $j = $k - 10;
    }
    //echo "</select></td>\n";
    echo "</select>\n";

    "</td>\n";

    echo "<td>\n";
    echo "<select size=1 name='" . my_strtolower($current_matiere)."_categorie' onchange='changement()'>\n";

	echo "<option value='0'";
	if ($current_matiere_categorie_id == '0') {echo " SELECTED";}
	echo ">Aucune</option>\n";
    foreach ($categories as $row) {
        echo "<option value='".$row["id"]."'";
        if ($current_matiere_categorie_id == $row["id"]) {echo " SELECTED";}
        echo ">".html_entity_decode($row["nom_court"])."</option>\n";
    }
    echo "</select>\n";
    echo "</td>\n";
    //echo "<td><a href=\"../lib/confirm_query.php?liste_cible=$current_matiere&amp;action=del_matiere\" onclick=\"return confirmlink(this, 'La suppression d\'une matière est irréversible. Une telle suppression ne devrait pas avoir lieu en cours d\'année. Si c\'est le cas, cela peut entraîner la présence de données orphelines dans la base. Etes-vous sûr de vouloir continuer ?', 'Confirmation de la suppression')\">Supprimer</a></td></tr>\n";
    echo "<td><a href=\"suppr_matiere.php?matiere=$current_matiere\" onclick=\"return confirmlink(this, 'La suppression d\'une matière est irréversible. Une telle suppression ne devrait pas avoir lieu en cours d\'année. Si c\'est le cas, cela peut entraîner la présence de données orphelines dans la base. Etes-vous sûr de vouloir continuer ?', 'Confirmation de la suppression')\">Supprimer</a></td></tr>\n";
	$i++;
}
?>
</table>
<input type='submit' value='Enregistrer' style='margin-left: 70%; margin-top: 25px; margin-bottom: 100px;' />
</form>

<p style='text-indent: -4em; margin-left: 4em;'><em>NOTE&nbsp;:</em> Les matières qui ne sont associées à aucun enseignement apparaissent en gris.</p>

<script type='text/javascript'>
function afficher_masquer_matieres_sans_grp(mode) {
	for(i=0;i<<?php
	echo $nombre_lignes;
	?>;i++) {
		if(document.getElementById('tr_sans_grp_assoc_'+i)) {
			//alert(i);
			if(mode=='afficher') {
				document.getElementById('tr_sans_grp_assoc_'+i).style.display='';
			}
			else {
				document.getElementById('tr_sans_grp_assoc_'+i).style.display='none';
			}
		}
	}
}
</script>
<?php require("../lib/footer.inc.php");?>
