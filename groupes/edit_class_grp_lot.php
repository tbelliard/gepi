<?php
/*
 * Last modification  : 30/08/2006
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

//INSERT INTO `droits` VALUES ('/groupes/edit_class_grp_lot.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des enseignements simples par lot.', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL);
if (!is_numeric($id_classe)) $id_classe = 0;
$classe = get_classe($id_classe);
$display = isset($_GET['display']) ? $_GET['display'] : (isset($_POST['display']) ? $_POST["display"] : NULL);
if ($display != "new") $display = "current";


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
}// =================================


if (isset($_POST['is_posted'])) {
	$checkmat=$_POST['checkmat'];
	//$id_grp=$_POST['id_grp'];
	$id_grp=isset($_POST['id_grp']) ? $_POST['id_grp'] : NULL;
	$prof=$_POST['prof'];
	$id_matiere=$_POST['id_matiere'];

	echo "<!--count(\$id_matiere)=".count($id_matiere)."-->\n";

	//for($i=0;$i<count($id_matiere);$i++){
	for($i=0;$i<$_POST['compteur_matieres'];$i++){
		unset($reg_clazz);
		if(isset($id_matiere[$i])){
            echo "<!--\$id_matiere[$i]=".$id_matiere[$i]."-->\n";
            if($id_matiere[$i]!=""){
                if(isset($checkmat[$i])){
                    if($checkmat[$i]=="nouveau_groupe"){
                        // C'est un nouveau groupe

                        echo "<!--\$checkmat[$i]=nouveau_groupe-->\n";

                        $sql="SELECT * FROM matieres WHERE matiere='$id_matiere[$i]'";
                        $resultat_matiere=mysql_query($sql);
                        $ligne_matiere=mysql_fetch_object($resultat_matiere);

                        $reg_clazz[0]=$id_classe;

                        //$create = create_group($reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz);
                        //echo "<!-- create_group($id_matiere[$i], $ligne_matiere->nom_complet, $id_matiere[$i], $reg_clazz); -->\n";
                        $create = create_group($id_matiere[$i], $ligne_matiere->nom_complet, $id_matiere[$i], $reg_clazz);
                        if (!$create) {
                            //echo "<!-- erreur -->\n";
                            $msg .= "Erreur lors de la création du groupe $id_matiere[$i]";
                        }
                        else{
                            $id_groupe=$create;
                            $sql="INSERT INTO j_groupes_professeurs VALUES('$id_groupe','$prof[$i]','')";
                            $resultat_prof=mysql_query($sql);

                            // Affectation de tous les élèves de la classe dans le groupe:
                            $current_group = get_group($id_groupe);
                            $reg_professeurs = (array)$current_group["profs"]["list"];
                            unset($reg_eleves);
                            $reg_eleves = array();

                            /*
                            //$sql="SELECT * FROM periodes WHERE id_classe='$id_classe'";
                            $sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY periode,login";
                            $result_list_eleves=mysql_query($sql);
                            while($ligne_eleve=mysql_fetch_object($result_list_eleves)){
                                $reg_eleves[$ligne_eleve->periode][]=$ligne_eleve->login;
                            }
                            */
                            $sql="SELECT * FROM periodes WHERE id_classe='$id_classe'";
                            $result_list_periodes=mysql_query($sql);
                            while($ligne_periode=mysql_fetch_object($result_list_periodes)){
                                //echo "<!-- \$ligne_periode->num_periode=$ligne_periode->num_periode -->\n";
                                //echo "\$ligne_periode->num_periode=$ligne_periode->num_periode <br />\n";
                                $reg_eleves[$ligne_periode->num_periode]=array();
                                //$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY periode,login";
                                $sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='$ligne_periode->num_periode' ORDER BY periode,login";
                                $result_list_eleves=mysql_query($sql);
                                while($ligne_eleve=mysql_fetch_object($result_list_eleves)){
                                    $reg_eleves[$ligne_periode->num_periode][]=$ligne_eleve->login;
                                    //echo "<!-- \$ligne_eleve->login=$ligne_eleve->login -->\n";
                                }
                            }

                            $create = update_group($id_groupe, $id_matiere[$i], $ligne_matiere->nom_complet, $id_matiere[$i], $reg_clazz, $reg_professeurs, $reg_eleves);
                            if (!$create) {
                                $msg .= "Erreur lors de la mise à jour du groupe $id_matiere[$i]";
                            }
                            //else {
                            //	$msg .= "Le groupe a bien été mis à jour.";
                            //}


                        }
                    }
                    elseif($checkmat[$i]!=""){
                        // Mise à jour du groupe $id_groupe=$checkmat[$i]
                        $id_groupe=$checkmat[$i];
                        //echo "\$id_groupe=$id_groupe<br />\n";
                        $group=get_group($id_groupe);

                        $sql="SELECT * FROM matieres WHERE matiere='$id_matiere[$i]'";
                        $resultat_matiere=mysql_query($sql);
                        $ligne_matiere=mysql_fetch_object($resultat_matiere);

                        $reg_clazz[0]=$id_classe;

                        /*
                        for($k=0;$k<count();$k++){
                            echo "\$group[profs][list][$k]=".$group["profs"]["list"][$k]."<br />\n";
                        }
                        */

                        //$create = update_group($id_groupe, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves);
                        //echo "<!--update_group($id_groupe, $id_matiere[$i], $ligne_matiere->nom_complet, $id_matiere[$i], $reg_clazz, ".$group["profs"]["list"].", ".$group["eleves"]["list"].");-->\n";
                        //echo "update_group($id_groupe, $id_matiere[$i], $ligne_matiere->nom_complet, $id_matiere[$i], $reg_clazz, ".$group["profs"]["list"].", ".$group["eleves"]["list"].");<br />\n";

                        //$create = update_group($id_groupe, $id_matiere[$i], $ligne_matiere->nom_complet, $id_matiere[$i], $reg_clazz, $group["profs"]["list"], $group["eleves"]["list"]);
                        if(isset($group["profs"]["list"])){
                            $tabprof=$group["profs"]["list"];
                        }
                        else{
                            $tabprof=array();
                        }
                        if(isset($group["eleves"]["list"])){
                            $tabele=$group["eleves"]["list"];
                        }
                        else{
                            $tabele=array();
                        }
                        $create = update_group($id_groupe, $id_matiere[$i], $ligne_matiere->nom_complet, $id_matiere[$i], $reg_clazz, $tabprof, $tabele);

                        if (!$create) {
                            $msg .= "Erreur lors de la mise à jour du groupe $id_matiere[$i]";
                        }
                        else{
                            if($prof[$i]==""){
                                $sql="DELETE FROM j_groupes_professeurs WHERE id_groupe='$id_groupe'";
                                $resultat_suppr_prof=mysql_query($sql);
                            }
                            else{
                                $sql="SELECT * FROM j_groupes_professeurs WHERE id_groupe='$id_groupe' AND login='$prof[$i]'";
                                $resultat_verif_prof=mysql_query($sql);
                                if(mysql_num_rows($resultat_verif_prof)==0){
                                    $sql="INSERT INTO j_groupes_professeurs VALUES('$id_groupe','$prof[$i]','')";
                                    $resultat_prof=mysql_query($sql);
                                }
                                else{
                                    // Le prof est déjà affecté au groupe.
                                }
                            }
                        }
                    }
                }
                else{
                    // On supprime le groupe:
                    //$id_groupe=$checkmat[$i];
                    $id_groupe=$id_grp[$i];
                    if($id_groupe!=""){
                        //echo "Suppression... \$id_groupe=$id_groupe<br />";
                        if(test_before_group_deletion($id_groupe)){
                            if(!delete_group($id_groupe)){
                                $msg="Erreur lors de la suppression du groupe.";
                            }
                        }
                        else{
                            $msg="Des notes sons saisies pour ce groupe. La suppression n'est pas possible.";
                        }
                    }
                }
            }
        }
	}
}


//**************** EN-TETE **************************************
//$titre_page = "Gestion des groupes";
$titre_page = "Gestion des enseignements 'simples' par lot";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

echo "<p class='bold'>";
echo "<a href='../classes/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec'>Classe précédente</a>";}
if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv'>Classe suivante</a>";}
echo "</p>\n";

?>



<?php
echo "<h3>Gestion des enseignements simples pour la classe :" . $classe["classe"]."</h3>\n";

echo "<p>Ne doivent être saisis ici que les enseignements ne concernant qu'une classe (<i>pas les regroupements</i>) et un seul professeur par matière.</p>\n";


echo "<script language='javascript' type='text/javascript'>
	function test_prof(nb){
		// On ne décoche pas le fait de ne pas mettre de prof...
		// ... il peut arriver en début d'année qu'un prof ne soit pas nommé...
		if(document.getElementById('prof_'+nb).selectedIndex!=0){
			document.getElementById('checkmat_'+nb).checked='true';
		}
	}
</script>\n";

// On peut basculer entre deux modes de saisis : seulement les groupes déjà associés, ou bien nouveaux groupes
if ($display == "current") {
	echo "<p>--> <a href='edit_class_grp_lot.php?id_classe=".$id_classe."&amp;display=new'>Ajouter de nouveaux groupes</a></p>";
} else {
	echo "<p>--> <a href='edit_class_grp_lot.php?id_classe=".$id_classe."&amp;display=current'>Editer les groupes existants</a></p>";
}
//echo "<form enctype='multipart/form-data' action='add_group.php' name='new_group' method='get'>";
echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' name='new_groups' method='post'>";

echo "<table border='0'>\n";
echo "<tr valign='top'>";
echo "<td>&nbsp;</td>\n";
echo "<td>Matière</td>\n";
echo "<td>Professeur</td>\n";
echo "</tr>\n";
$result_matiere=mysql_query("SELECT matiere, nom_complet FROM matieres ORDER BY matiere");
$nb_mat=mysql_num_rows($result_matiere);
$cpt=0;
while($ligne_matiere=mysql_fetch_object($result_matiere)){
	$groupe_existant="non";


	$display_current = false;
	// Récupération des infos déjà saisies:
	$sql="SELECT jgm.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgc.id_classe='$id_classe' AND jgm.id_matiere='$ligne_matiere->matiere' AND jgc.id_groupe=jgm.id_groupe";
	$result_grp=mysql_query($sql);
	if(mysql_num_rows($result_grp)==0 and $display == "new"){
		$display_current = true;
		echo "<tr>\n";
		echo "<td>\n";
		echo "<input type='hidden' name='id_matiere[$cpt]' value='$ligne_matiere->matiere' />\n";
		echo "<input type='checkbox' name='checkmat[$cpt]' id='checkmat_".$cpt."' value='nouveau_groupe' />\n";
		//echo "<input type='hidden' name='id_matiere[]' value='$ligne_matiere->matiere' />\n";
		//echo "<input type='checkbox' name='checkmat[]' id='checkmat_".$cpt."' value='nouveau_groupe' />\n";
		//echo "<input type='hidden' name='id_grp[$cpt]' value='' />\n";
		echo "</td>\n";
	}
	elseif(mysql_num_rows($result_grp)==1 and $display == "current"){
		$display_current = true;
		echo "<tr>\n";
		$ligne_grp=mysql_fetch_object($result_grp);

		$sql="SELECT * FROM j_groupes_professeurs WHERE id_groupe='$ligne_grp->id_groupe'";
		$result_verif_grp_prof=mysql_query($sql);
		if(mysql_num_rows($result_verif_grp_prof)>1){
			//echo "<td colspan='3'>Le groupe associé à la matière $ligne_matiere->matiere pour cette classe a plusieurs professeurs définis.<br />Ce n'est pas un enseignement 'simple'.<br />A traiter ailleurs...</td>\n";
			echo "<td colspan='3'>$ligne_matiere->matiere: groupe complexe (<i>plusieurs professeurs</i>), accessible par <a href='edit_class.php?id_classe=$id_classe'>Gérer les enseignements</a>.</td>\n";
			$groupe_existant="trop";
		}
		else{
			$sql="SELECT * FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgc.id_groupe='$ligne_grp->id_groupe' AND jgm.id_matiere='$ligne_matiere->matiere' AND jgc.id_groupe=jgm.id_groupe";
			//echo "<td>$sql</td>\n";
			$result_verif_grp_classes=mysql_query($sql);
			if(mysql_num_rows($result_verif_grp_classes)==1){
				echo "<td>\n";
				echo "<input type='hidden' name='id_matiere[$cpt]' value='$ligne_matiere->matiere' />\n";
				echo "<input type='checkbox' name='checkmat[$cpt]' id='checkmat_".$cpt."' value='$ligne_grp->id_groupe' checked />\n";
				echo "<input type='hidden' name='id_grp[$cpt]' value='$ligne_grp->id_groupe' />\n";
				echo "</td>\n";
				$groupe_existant="oui";
			}
			else{
				//echo "<td colspan='3'>Le groupe associé à la matière $ligne_matiere->matiere est associé à plusieurs classes.<br />Ce n'est pas un enseignement 'simple'.<br />A traiter ailleurs...</td>\n";
				echo "<td colspan='3'>$ligne_matiere->matiere: groupe complexe (<i>plusieurs classes</i>), accessible par <a href='edit_class.php?id_classe=$id_classe'>Gérer les enseignements</a>.</td>\n";
				$groupe_existant="trop";
			}
		}
	}
	elseif(mysql_num_rows($result_grp)>1 and $display == "current"){
		$display_current = true;
		echo "<tr>\n";
		// C'est le bazar... plusieurs groupes existent pour cette matière dans cette classe
		//echo "<td colspan='3'>La matière $ligne_matiere->matiere a plusieurs groupes définis pour cette classe.<br />Ce n'est pas un enseignement 'simple'.<br />Elle devra être traitée ailleurs...</td>\n";
		echo "<td colspan='3'>$ligne_matiere->matiere: groupe complexe (<i>plusieurs classes</i>), accessible par <a href='edit_class.php?id_classe=$id_classe'>Gérer les enseignements</a>.</td>\n";
		$groupe_existant="trop";
	}

	//echo "<td><input type='checkbox' name='checkmat[$cpt]' id='checkmat_".$cpt."' value='coche' /></td>\n";
	if($groupe_existant!="trop" and $display_current){
		echo "<td>".htmlentities($ligne_matiere->nom_complet)."</td>\n";
		$sql="SELECT jpm.id_professeur,u.nom,u.prenom,u.civilite FROM j_professeurs_matieres jpm, matieres m, utilisateurs u WHERE jpm.id_matiere=m.matiere AND m.matiere='$ligne_matiere->matiere' AND u.login=jpm.id_professeur ORDER BY jpm.id_professeur";
		$result_prof=mysql_query($sql);
		echo "<td>\n";
		echo "<select name='prof[$cpt]' id='prof_".$cpt."' onchange='test_prof($cpt);'>\n";
		echo "<option value=''>---</option>\n";
		$selected="";
		while($ligne_prof=mysql_fetch_object($result_prof)){
			if($groupe_existant=="oui"){
				$sql="SELECT * FROM j_groupes_professeurs jgp WHERE jgp.id_groupe='$ligne_grp->id_groupe' AND jgp.login='$ligne_prof->id_professeur'";
				$result_verif=mysql_query($sql);
				if(mysql_num_rows($result_verif)==0){
					$selected="";
				}
				else{
					$selected=" selected";
				}
			}
			echo "<option value='$ligne_prof->id_professeur'$selected>".ucfirst(strtolower($ligne_prof->prenom))." ".strtoupper($ligne_prof->nom)."</option>\n";
		}
		echo "</select>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	$cpt++;
}
echo "</table>\n";


echo "<input type='hidden' name='compteur_matieres' value='$cpt' />\n";

echo "<input type='hidden' name='mode' value='groupe' />\n";
echo "<input type='hidden' name='id_classe' value='" . $id_classe . "' />\n";
echo "<input type='hidden' name='is_posted' value='oui' />\n";
echo "<input type='submit' value='Valider' />\n";
echo "</form>\n";

/*
$groups = get_groups_for_class($id_classe);
foreach ($groups as $group) {
	//$group["description"]
	$current_group = get_group($group["id"]);

}
foreach($current_group["profs"]["list"] as $prof) {
	if (!$first) echo ", ";
	echo $current_group["profs"]["users"][$prof]["prenom"];
	echo " ";
	echo $current_group["profs"]["users"][$prof]["nom"];
	$first = false;
}
*/
require("../lib/footer.inc.php");
?>