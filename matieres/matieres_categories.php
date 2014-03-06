<?php
/*
* $Id$
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

$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : (isset($_POST['orderby']) ? $_POST["orderby"] : 'priority,nom_complet');
if ($orderby != "nom_court" AND $orderby != "nom_complet" AND $orderby != "priority, nom_court") {
    $orderby = "priority,nom_complet";
}

$msg = null;

if (isset($_POST['action'])) {
	check_token();
    $error = false;
    if ($_POST['action'] == "add") {
        // On enregistre une nouvelle catégorie
        // On filtre un peu
        if (!is_numeric($_POST['priority'])) $_POST['priority'] = "0";
        // Le reste passera sans soucis, mais on coupe quand même si jamais c'est trop long
        if (mb_strlen($_POST['nom_court']) > 250) $_POST['nom_court'] = mb_substr($_POST['nom_court'], 0, 250);
        if (mb_strlen($_POST['nom_complet']) > 250) $_POST['nom_complet'] = mb_substr($_POST['nom_complet'], 0, 250);
        // On enregistre
        if ($_POST['nom_court'] == '') {
            $msg .= "Le nom court ne peut pas être vide.<br/>";
            $error = true;
            $res = false;
        }
        if (my_strtolower($_POST['nom_court']) == 'aucune') {
            $msg .= "Le nom court ne peut pas être 'Aucune'.<br/>";
            $error = true;
            $res = false;
        }
        if ($_POST['nom_complet'] == '') {
            $msg .= "L'intitulé ne peut pas être vide.<br/>";
            $error = true;
            $res = false;
        }

        if (!$error) {
            $res = mysqli_query($GLOBALS["mysqli"], "INSERT INTO matieres_categories SET nom_court = '" . ($_POST['nom_court']) . "', nom_complet = '" . ($_POST['nom_complet']) . "', priority = '" . $_POST["priority"] . "'");
        }
        if (!$res) {
            $msg .= "Erreur lors de l'enregistrement de la nouvelle catégorie.</br>";
            echo mysqli_error($GLOBALS["mysqli"]);
        }
    } elseif ($_POST['action'] == "edit") {
        // On met à jour une catégorie
        // On filtre un peu
        if (!is_numeric($_POST['priority'])) $_POST['priority'] = "0";
        if (!is_numeric($_POST['categorie_id'])) $_POST['categorie_id'] = "0";
        // Le reste passera sans soucis, mais on coupe quand même si jamais c'est trop long
        if (mb_strlen($_POST['nom_court']) > 250) $_POST['nom_court'] = mb_substr($_POST['nom_court'], 0, 250);
        if (mb_strlen($_POST['nom_complet']) > 250) $_POST['nom_complet'] = mb_substr($_POST['nom_complet'], 0, 250);

        if ($_POST['nom_court'] == '') {
            $msg .= "Le nom court ne peut pas être vide.<br/>";
            $error = true;
            $res = false;
        }
        if (my_strtolower($_POST['nom_court']) == 'aucune') {
            $msg .= "Le nom court ne peut pas être 'Aucune'.<br/>";
            $error = true;
            $res = false;
        }
        if ($_POST['nom_complet'] == '') {
            $msg .= "L'intitulé ne peut pas être vide.<br/>";
            $error = true;
            $res = false;
        }

        if (!$error) {
            // On enregistre
            $res = mysqli_query($GLOBALS["mysqli"], "UPDATE matieres_categories SET nom_court = '" . ($_POST['nom_court']) . "', nom_complet = '" . ($_POST['nom_complet']) . "', priority = '" . $_POST["priority"] . "' WHERE id = '".$_POST['categorie_id']."'");
        }

        if (!$res) $msg .= "Erreur lors de la mise à jour de la catégorie.";

    } elseif($_POST['action']=='modif_ordre_categories') {
		check_token();

		$tab_priorites_categories=array();
		$temoin_pb_ordre_categories="n";
		foreach($_POST as $key => $value) {
			if(preg_match("/^priority_/", $key)) {
				if(in_array($value, $tab_priorites_categories)) {
					$temoin_pb_ordre_categories="y";
					$value=max($tab_priorites_categories)+1;
				}
				$tab_priorites_categories[]=$value;

				$cat_id=preg_replace("/^priority_/", "", $key);
				$sql="UPDATE matieres_categories SET priority = '" . $value . "' WHERE id = '".$cat_id."';";
				//echo "$sql<br />";
				$res = mysqli_query($GLOBALS["mysqli"], $sql);

				if (!$res) $msg .= "Erreur lors de la mise à jour de la catégorie.";
			}
		}

		if($temoin_pb_ordre_categories=="y") {
			$msg.="<br /><strong>Anomalie&nbsp;:</strong> Les catégories de matières ne doivent pas avoir le même rang.<br />Cela risque de provoquer des problèmes sur les bulletins.<br />Des mesures ont été prises pour imposer des ordres différents, mais il se peut que l'ordre ne vous convienne pas.<br />\n";
		}
    } elseif ($_POST['action'] == "delete") {
        // On teste d'abord l'ID
        if (!is_numeric($_POST['categorie_id'])) {
            // Inutile d'en dire plus...
            $msg .= "Erreur.";
        } else {
            // On a un ID valide.
            // Si c'est l'ID 1, on ne supprime pas. C'est la catégorie par défaut
            if ($_POST['categorie_id'] == 1) {
                $msg .= "Vous ne pouvez pas supprimer la catégorie par défaut !";
            } else {

                // On teste l'utilisation de cette catégorie
                $res = mysqli_query($GLOBALS["mysqli"], "SELECT matiere FROM matieres WHERE categorie_id = '" . $_POST['categorie_id'] ."'");
                $test = mysqli_num_rows($res);

                //$res2 = mysql_query("SELECT DISTINCT id_groupe, c.id, c.classe FROM j_groupes_classes jgc, classes c WHERE c.id=jgc.id_classe AND categorie_id='".$_POST['categorie_id']."'");
                $res2 = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.id, c.classe FROM j_groupes_classes jgc, classes c WHERE c.id=jgc.id_classe AND categorie_id='".$_POST['categorie_id']."'");
                $test2 = mysqli_num_rows($res2);

                if ($test>0) {
                    // On a des entrées... la catégorie a déjà été associée à des matières, donc on ne la supprime pas.
					$liste_matieres_associees="";
					while($lig=mysqli_fetch_object($res)) {
						if($liste_matieres_associees!='') {$liste_matieres_associees.=", ";}
						$liste_matieres_associees.="<a href='index.php' target='_blank'>".$lig->matiere."</a>";
					}
                    $msg .= "La catégorie n'a pas pu être supprimée, car elle a déjà été associée à des matières (<i>$liste_matieres_associees</i>).<br/>";
				}
                elseif ($test2>0) {
					$liste_classes_associees="";
					while($lig=mysqli_fetch_object($res2)) {
						if($liste_classes_associees!='') {$liste_classes_associees.=", ";}
						$liste_classes_associees.="<a href='../groupes/edit_class.php?id_classe=$lig->id' target='_blank'>".get_class_from_id($lig->id)."</a>";
					}
                    $msg .= "La catégorie n'a pas pu être supprimée, car elle a déjà été associée à des enseignements pour des classes (<i>$liste_classes_associees</i>).<br/>";
                }
				else {
                    $res = mysqli_query($GLOBALS["mysqli"], "DELETE FROM matieres_categories WHERE id = '" . $_POST['categorie_id']."'");
                    if (!$res) {
                        $msg .= "Erreur lors de la suppression de la catégorie.<br/>";
                    } else {
                        $msg .= "La catégorie a bien été supprimée.<br/>";
                    }
                }
            }
        }
    }
}
elseif((isset($_GET['action']))&&($_GET['action'] == "corrige_accents_html")) {
	check_token();

	$tab = array_flip (get_html_translation_table(HTML_ENTITIES));
	$sql="SELECT * FROM matieres_categories;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$correction=ensure_utf8(strtr($lig->nom_complet, $tab));
			if($lig->nom_complet!=$correction) {
				$sql="UPDATE matieres_categories SET nom_complet='$correction' WHERE id='$lig->id';";
				//echo "$sql<br />";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if($update) {
					$msg .= "Correction de l'encodage d'un nom de catégorie de matière en '$correction'<br />";
				}
				else {
					$msg .= "Erreur lors de la correction de l'encodage du nom de catégorie de matière '$lig->nom_complet' en '$correction'<br />";
				}
			}
		}
	}
	unset($_GET['action']);
}


//**************** EN-TETE **************************************
$titre_page = "Gestion des catégories de matières";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

//debug_var();

if (isset($_GET['action'])) {
    // On a une action : soit on ajoute soit on édite soit on delete
    ?>
    <p class="bold"><a href="matieres_categories.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>



<?php
    if ($_GET['action'] == "add") {
        // On ajoute une catégorie
        // On affiche le formulaire d'ajout
        echo "<form enctype='multipart/form-data' action='matieres_categories.php' name='formulaire' method=post>";
		echo add_token_field();
        echo "<input type='hidden' name='action' value='add'>";
        echo "<p>Nom court (utilisé dans les outils de configuration) : <input type='text' name='nom_court'></p>";
        echo "<p>Intitulé complet (utilisé sur les documents officiels) : <input type='text' name='nom_complet'></p>";
        echo "<p>Priorité d'affichage par défaut : ";
        echo "<select name='priority' size='1'>";
        for ($i=0;$i<11;$i++) {
            echo "<option value='$i'>$i</option>";
        }
        echo "</select>";

        echo "<p>";
        echo "<input type='submit' value='Enregistrer'>";
        echo "</p>";
        echo "</form>";
    } elseif ($_GET['action'] == "edit") {
        // On édite la catégorie existante
        if (!is_numeric($_GET['categorie_id'])) $_GET['categorie_id'] == 0;

        $res = mysqli_query($GLOBALS["mysqli"], "SELECT id, nom_court, nom_complet, priority FROM matieres_categories WHERE id = '" . $_GET['categorie_id'] . "'");
        $current_cat = mysqli_fetch_array($res,  MYSQLI_ASSOC);

        if ($current_cat) {
			if($current_cat["nom_court"]=='Aucune') {
				echo "<p style='color:red'>ANOMALIE&nbsp;: Il ne devrait pas exister de catégorie intitulée 'Aucune'.<br />Voir <a href='http://www.sylogix.org/wiki/gepi/Enseignement_invisible'>http://www.sylogix.org/wiki/gepi/Enseignement_invisible</a> et <a href='http://www.sylogix.org/wiki/gepi/Suppr_Cat_Aucune'>http://www.sylogix.org/wiki/gepi/Suppr_Cat_Aucune</a> pour des explications</p>\n";
			}
            echo "<form enctype='multipart/form-data' action='matieres_categories.php' name='formulaire' method=post>";
			echo add_token_field();
            echo "<input type='hidden' name='action' value='edit'>";
            echo "<input type='hidden' name='categorie_id' value='".$current_cat["id"] . "'>";
            //echo "<p>Nom court (utilisé dans les outils de configuration) : <input type='text' name='nom_court' value='".html_entity_decode($current_cat["nom_court"]) ."' /></p>";
            //echo "<p>Intitulé complet (utilisé sur les documents officiels) : <input type='text' name='nom_complet' value='".html_entity_decode($current_cat["nom_complet"]) ."' /></p>";
            echo "<p>Nom court (utilisé dans les outils de configuration) : <input type='text' name='nom_court' value='".$current_cat["nom_court"] ."' /></p>";
            echo "<p>Intitulé complet (utilisé sur les documents officiels) : <input type='text' name='nom_complet' value='".$current_cat["nom_complet"] ."' /></p>";
            echo "<p>Priorité d'affichage par défaut : ";
            echo "<select name='priority' size='1'>";
            for ($i=0;$i<11;$i++) {
                echo "<option value='$i'";
                if ($current_cat["priority"] == $i) echo " SELECTED";
                echo ">$i</option>";
            }
            echo "</select>";

            echo "<p>";
            echo "<input type='submit' value='Enregistrer'>";
            echo "</p>";
            echo "</form>";
        }

    }



} else {
    // Pas d'action. On affiche la liste des rubriques
    ?>
    <p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href="matieres_categories.php?action=add">Ajouter une catégorie</a></p>
    <!--p style='text-indent:-6em;padding-left:6em;'><em>Remarque&nbsp;:</em> la catégorie par défaut ne peut pas être supprimée. Elle est automatiquement associée aux matières existantes et aux nouvelles matières, et pour tous les groupes. Vous pouvez la renommer (<em>Autres, Hors catégories, etc.</em>), mais laissez toujours un nom générique.</p-->

	<?php
		$tab_priorites_categories=array();
		$temoin_pb_ordre_categories="n";
		$sql="select * from matieres_categories;";
		$res_cat=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_cat)>0) {
			while($lig_cat=mysqli_fetch_object($res_cat)) {
				$current_priority=$lig_cat->priority;
				if(in_array($current_priority, $tab_priorites_categories)) {
					$temoin_pb_ordre_categories="y";
				}
				$tab_priorites_categories[]=$current_priority;
			}
		}

		if($temoin_pb_ordre_categories=="y") {
			echo "<p style='color:red; text-indent:-6em;padding-left:6em;'><strong>Anomalie&nbsp;:</strong> Les catégories de matières ne doivent pas avoir le même rang.<br />Cela risque de provoquer des problèmes sur les bulletins.<br />Vous devriez corriger.</p>\n";
		}

		echo "<form action=\"".$_SERVER['PHP_SELF']."\" method='post'>\n";
		echo "<fieldset style='border:1px solid white; background-image: url(\"../images/background/opacite50.png\");'>\n";
		echo add_token_field();
	?>

    <table class='boireaus' width='100%' border='1' cellpadding='5' summary='Tableau des catégories'>
<tr>
    <th><p class='bold'><a href='./matieres_categories.php?orderby=nom_court'>Nom court</a></p></th>
    <th><p class='bold'><a href='./matieres_categories.php?orderby=m.nom_complet'>Intitulé complet</a></p></th>
    <th><p class='bold'><a href='./matieres_categories.php?orderby=m.priority,m.nom_complet'>Ordre d'affichage<br />par défaut</a></p></th>
    <th><p class='bold'>Supprimer</p></th>
</tr>
    <?php
	$max_priority_cat=0;
	$get_max_cat = mysqli_query($GLOBALS["mysqli"], "SELECT priority FROM matieres_categories ORDER BY priority DESC LIMIT 1");
	if(mysqli_num_rows($get_max_cat)>0) {
		$max_priority_cat=old_mysql_result($get_max_cat, 0, "priority");
	}

	$temoin_anomalie_categ_Aucune='n';
	$alt=1;
    $res = mysqli_query($GLOBALS["mysqli"], "SELECT id, nom_court, nom_complet, priority FROM matieres_categories ORDER BY $orderby");
    while ($current_cat = mysqli_fetch_array($res,  MYSQLI_ASSOC)) {
		$alt=$alt*(-1);
        echo "<tr class='lig$alt white_hover'>\n";
        //echo "<td><a href='matieres_categories.php?action=edit&categorie_id=".$current_cat["id"]."'>".html_entity_decode($current_cat["nom_court"])."</a></td>\n";
        //echo "<td>".html_entity_decode($current_cat["nom_complet"])."</td>\n";
        echo "<td><a href='matieres_categories.php?action=edit&categorie_id=".$current_cat["id"]."'>".$current_cat["nom_court"]."</a></td>\n";
        echo "<td>".$current_cat["nom_complet"]."</td>\n";
        echo "<td>";

			echo "<select name='priority_".$current_cat["id"]."' size='1'>\n";
			for ($i=0;$i<max(100,$max_priority_cat);$i++) {
				echo "<option value='$i'";
				if ($current_cat["priority"] == $i) echo " SELECTED";
				echo ">$i</option>\n";
			}
			echo "</select>\n";
			//echo $current_cat["priority"];

        echo "</td>\n";
        echo "<td>";
        if ($current_cat["id"] != "1") {
            echo "<form enctype='multipart/form-data' action='matieres_categories.php' name='formulaire' method=post>\n";
			echo add_token_field();
            echo "<input type='hidden' name='action' value='delete' />\n";
            echo "<input type='hidden' name='categorie_id' value='".$current_cat["id"]."' />\n";
            echo "<input type='submit' value='Supprimer' />\n</form>\n";
        } else {
            echo "Catégorie par défaut (<em>suppression impossible</em>)";
        }
		echo "</td>\n";
        echo "</tr>\n";
		if($current_cat["nom_court"]=='Aucune') {$temoin_anomalie_categ_Aucune='y';}
    }
    echo "</table>\n";

	echo "
	<center><input type='submit' name='enregistrer' value=\"Modifier l'ordre des catégories\" /></center>
	<input type=hidden name='is_posted' value='yes' />
	<input type=hidden name='action' value='modif_ordre_categories' />
	</fieldset>
	</form>\n";

	if($temoin_anomalie_categ_Aucune=='y') {
		echo "<p style='color:red'>ANOMALIE&nbsp;: Il ne devrait pas exister de catégorie intitulée 'Aucune'.<br />Voir <a href='http://www.sylogix.org/wiki/gepi/Enseignement_invisible'>http://www.sylogix.org/wiki/gepi/Enseignement_invisible</a> et <a href='http://www.sylogix.org/wiki/gepi/Suppr_Cat_Aucune'>http://www.sylogix.org/wiki/gepi/Suppr_Cat_Aucune</a> pour des explications</p>\n";
	}

	$tab = array_flip (get_html_translation_table(HTML_ENTITIES));
	$sql="SELECT * FROM matieres_categories;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$alerte_accents_html="<br /><p><span style='color:red; font-weight:bold;'>Attention&nbsp;:</span> Une ou des catégories ont des accents HTML dans leur nom complet (<em>accents enregistrés sous forme HTML dans la base de données</em>).<br />Cela peut perturber l'affichage des noms de catégories de matières dans les bulletins PDF (<em>et ailleurs peut-être</em>)</p>\n<ul>\n";
		$ajout="";
		while($lig=mysqli_fetch_object($res)) {
			$correction=ensure_utf8(strtr($lig->nom_complet, $tab));
			if($lig->nom_complet!=$correction) {
				$ajout.="<li>La catégorie de matière <strong>$lig->nom_complet</strong> a un ou des accents HTML</li>\n";
			}
		}
		if($ajout!="") {
			echo $alerte_accents_html.$ajout."</ul>\n";
			echo "<p>Vous pouvez <a href='".$_SERVER['PHP_SELF']."?action=corrige_accents_html".add_token_in_url()."'>corriger d'un clic ce problème</a>.</p>\n";
		}
	}

	echo "<br />
<p><em>NOTES&nbsp;:</em></p>
<ul>
	<li><p>La catégorie par défaut ne peut pas être supprimée.<br />
	Elle est automatiquement associée aux matières existantes et aux nouvelles matières, et pour tous les groupes.<br />
	Vous pouvez la renommer (<em>Autres, Hors catégories, etc.</em>), mais laissez toujours un nom générique.</p></li>
	<li><p>L'ordre des catégories défini ici est pris en compte comme ordre par défaut pour les nouvelles classes.<br />
	Pour les classes existantes, vous pouvez modifier l'ordre dans&nbsp;:<br />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='../classes/index.php'>Gestion des bases/Gestion des classes/&lt;Telle_classe&gt; Paramètres</a><br />
	Vous pouvez aussi effectuer ce paramétrage par lots de classes&nbsp;:<br />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='../classes/classes_param.php#parametres_generaux'>Gestion des bases/Gestion des classes/Paramétrage des classes par lots</a></p></li>
</ul>\n";
}
require("../lib/footer.inc.php");
?>
