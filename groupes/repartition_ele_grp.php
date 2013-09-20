<?php
/*
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//INSERT INTO droits VALUES ('/groupes/repartition_ele_grp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Répartir des élèves dans des groupes', '');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


function test_before_eleve_grp_removal($_login, $_id_groupe, $_periode) {
    $test = mysql_result(mysql_query("select count(*) FROM matieres_notes WHERE (login = '" . $_login . "' AND id_groupe = '" . $_id_groupe . "' AND periode = '" . $_periode . "')"), 0);

    $test2 = mysql_result(mysql_query("select count(*) FROM matieres_appreciations WHERE (login = '" . $_login . "' AND id_groupe = '" . $_id_groupe . "' AND periode = '" . $_periode . "')"), 0);

	// cnd.statut='': on ne compte que les vraies notes: pas abs, disp, - ou v
    $test3 = mysql_result(mysql_query("select count(*) FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE (cnd.login = '" . $_login . "' AND cnd.statut='' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe = '" . $_id_groupe . "' AND ccn.periode = '" . $_periode . "')"), 0);

    if ($test == 0 and $test2 == 0 and $test3 == 0) {
        return true;
    } else {
        return false;
    }
}



// Initialisation des variables utilisées dans le formulaire

$chemin_retour=isset($_GET['chemin_retour']) ? $_GET['chemin_retour'] : (isset($_POST['chemin_retour']) ? $_POST["chemin_retour"] : NULL);

$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL);
$id_groupe = isset($_GET['id_groupe']) ? $_GET['id_groupe'] : (isset($_POST['id_groupe']) ? $_POST["id_groupe"] : NULL);
$num_periode = isset($_GET['num_periode']) ? $_GET['num_periode'] : (isset($_POST['num_periode']) ? $_POST["num_periode"] : NULL);

$order_by=isset($_POST['order_by']) ? $_POST['order_by'] : (isset($_GET['order_by']) ? $_GET['order_by'] : '');

//if(isset($_POST['Valider_repartition'])) {
if(isset($_POST['enregistrer_repartition'])) {
	check_token();

	$msg="";
	$nb_modif=0;

	$login_ele=isset($_POST['login_ele']) ? $_POST["login_ele"] : array();
	$grp_eleve=isset($_POST['grp_eleve']) ? $_POST["grp_eleve"] : array();

	// Listes initiales des élèves dans les groupes
	for($i=0;$i<count($id_groupe);$i++) {
		$tab_eleve[$id_groupe[$i]]=array();
		$sql="SELECT name FROM groupes WHERE id='".$id_groupe[$i]."';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);
			$nom_groupe[$i]=$lig->name;
		}
		else {
			$nom_groupe[$i]="Groupe inconnu???";
		}
	}

	for($i=0;$i<count($id_groupe);$i++) {
		$sql="SELECT login FROM j_eleves_groupes WHERE periode='$num_periode' AND id_groupe='".$id_groupe[$i]."';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			while($lig=mysql_fetch_object($res)) {
				$tab_eleve[$id_groupe[$i]][]=$lig->login;
			}
		}
	}

	// Traitement des ré-affectations
	for($j=0;$j<count($login_ele);$j++) {
		$temoin_insertion_possible="y";
		$requete_insertion="";
		$requete_insertion_assoc_grp_clas="";

		$sql="SELECT id_classe FROM j_eleves_classes WHERE login='".$login_ele[$j]."' AND periode='$num_periode';";
		$res_clas=mysql_query($sql);
		$lig_clas=mysql_fetch_object($res_clas);
		$current_id_classe_ele=$lig_clas->id_classe;

		for($i=0;$i<count($id_groupe);$i++) {
			if($grp_eleve[$j]==$id_groupe[$i]) {
				if(!in_array($login_ele[$j],$tab_eleve[$id_groupe[$i]])) {
					// On affecte l'élève dans le groupe
					//$sql="INSERT INTO j_eleves_groupes SET login='".$login_ele[$j]."', id_groupe='".$id_groupe[$i]."', periode='$num_periode';";
					//$insert=mysql_query($sql);
					$requete_insertion="INSERT INTO j_eleves_groupes SET login='".$login_ele[$j]."', id_groupe='".$id_groupe[$i]."', periode='$num_periode';";

					// Vérification s'il y a bien déjà une association du groupe avec la classe:
					$sql="SELECT 1=1 FROM j_groupes_classes WHERE id_classe='$current_id_classe_ele' AND id_groupe='".$id_groupe[$i]."';";
					$test_clas_grp=mysql_query($sql);
					if(mysql_num_rows($test_clas_grp)==0) {
						// Récupérer les paramètres de j_groupes_classes pour le groupe actuel pour imposer le même dans l'association avec la classe de l'élève courant
						$sql="SELECT * FROM j_groupes_classes WHERE id_groupe='".$id_groupe[$i]."' LIMIT 1;";
						$res_grp=mysql_query($sql);
						$lig_grp=mysql_fetch_object($res_grp);

						$requete_insertion_assoc_grp_clas="INSERT INTO j_groupes_classes SET id_classe='$current_id_classe_ele', id_groupe='".$id_groupe[$i]."', priorite='$lig_grp->priorite', coef='$lig_grp->coef', categorie_id='$lig_grp->categorie_id', saisie_ects='$lig_grp->saisie_ects', valeur_ects='$lig_grp->valeur_ects', mode_moy='$lig_grp->mode_moy';";
					}
				}
			}
			else {
				if(in_array($login_ele[$j],$tab_eleve[$id_groupe[$i]])) {
					// On va tester si on peut désinscrire l'élève
					if(test_before_eleve_grp_removal($login_ele[$j], $id_groupe[$i], $num_periode)) {
						// On désinscrit effectivement
						$sql="DELETE FROM j_eleves_groupes WHERE login='".$login_ele[$j]."' AND id_groupe='".$id_groupe[$i]."' AND periode='$num_periode';";
						$suppr=mysql_query($sql);
						if(!$suppr) {$msg.="Echec de la désinscription de $login_ele[$j] du groupe $nom_groupe[$i] ($id_groupe[$i]) pour la période $num_periode.<br />";} else {$nb_modif++;}
					}
					else {
						$msg.="Des données bloquent la désincription de ".get_nom_prenom_eleve($login_ele[$j])." du groupe ".$nom_groupe[$i]." ($id_groupe[$i])<br />";
						$temoin_insertion_possible="n";
					}
				}
			}
		}

		if(($temoin_insertion_possible=="y")&&($requete_insertion!='')) {
			// On affecte l'élève dans son nouveau groupe
			$insert=mysql_query($requete_insertion);
			if(!$insert) {$msg.="Echec de l'inscription de $login_ele[$j] dans le groupe $nom_groupe[$i] ($id_groupe[$i]) pour la période $num_periode.<br />";} else {$nb_modif++;}
			if($requete_insertion_assoc_grp_clas!='') {
				$insert=mysql_query($requete_insertion_assoc_grp_clas);
				if(!$insert) {$msg.="Echec de l'insertion de l'association du groupe $nom_groupe[$i] ($id_groupe[$i]) avec la classe de l'élève sur la période $num_periode.<br />";}
			}
		}
	}

	if($nb_modif>0) {
		$msg.="$nb_modif inscription(s)/désinscription(s) enregistrée(s).<br />";
	}
}




//if(isset($_POST['Valider_recopie'])) {
if(isset($_POST['enregistrer_recopie'])) {
	check_token();

	$msg="";
	$nb_modif=0;

	$login_ele=isset($_POST['login_ele']) ? $_POST["login_ele"] : array();
	$maxper=isset($_POST['maxper']) ? $_POST["maxper"] : 0;
	$nb_grp=isset($_POST['nb_grp']) ? $_POST["nb_grp"] : 0;

	//echo "\$maxper=$maxper<br />";

	// Récupération des variables (checkbox)
	for($i=0;$i<count($id_groupe);$i++) {
		for($m=1;$m<=$maxper;$m++) {
			$grp_eleve[$i][$m]=isset($_POST['grp_eleve_'.$i.'_'.$m]) ? $_POST['grp_eleve_'.$i.'_'.$m] : NULL;
		}
	}

	// Récupération des noms des groupes
	for($i=0;$i<count($id_groupe);$i++) {
		$tab_eleve[$id_groupe[$i]]=array();
		$sql="SELECT name FROM groupes WHERE id='".$id_groupe[$i]."';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);
			$nom_groupe[$i]=$lig->name;
		}
		else {
			$nom_groupe[$i]="Groupe inconnu???";
		}
	}

	// Listes initiales des élèves dans les groupes
	// Boucle pour récupérer la liste actuelle des élèves dans chaque groupe pour chaque période
	$tab_eleve=array();
	for($i=0;$i<count($id_groupe);$i++) {
		$tab_eleve[$id_groupe[$i]]=array();
		for($m=1;$m<=$maxper;$m++) {
			$tab_eleve[$id_groupe[$i]][$m]=array();
			$sql="SELECT login FROM j_eleves_groupes WHERE periode='$m' AND id_groupe='".$id_groupe[$i]."';";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				while($lig=mysql_fetch_object($res)) {
					$tab_eleve[$id_groupe[$i]][$m][]=$lig->login;
				}
			}
		}
	}

	// Traitement des ré-affectations
	for($j=0;$j<count($login_ele);$j++) {
		//echo "<p>\$login_ele[$j]=$login_ele[$j]<br />\n";
		$temoin_insertion_possible="y";
		$requete_insertion="";
		$requete_insertion_assoc_grp_clas="";

		// Classe de l'élève courant sur chaque période
		unset($current_id_classe_ele);
		for($m=1;$m<=$maxper;$m++) {
			$sql="SELECT id_classe FROM j_eleves_classes WHERE login='".$login_ele[$j]."' AND periode='$m';";
			$res_clas=mysql_query($sql);
			$lig_clas=mysql_fetch_object($res_clas);
			$current_id_classe_ele[$m]=$lig_clas->id_classe;
			//echo "\$current_id_classe_ele[$m]=$current_id_classe_ele[$m]<br />\n";
		}

		// Boucle sur les groupes
		for($i=0;$i<count($id_groupe);$i++) {
			//echo "\$id_groupe[$i]=$id_groupe[$i]<br />\n";
			// Boucle sur les périodes
			for($m=1;$m<=$maxper;$m++) {
				$requete_insertion='';
				$requete_insertion_assoc_grp_clas='';

				//echo "Période $m<br />\n";
				// Si la case est cochée pour cet élève, ce groupe et cette période
				if(isset($grp_eleve[$i][$m][$j])) {
					//echo "\$grp_eleve[$i][$m][$j]=".$grp_eleve[$i][$m][$j]."<br />\n";
					//if($grp_eleve[$i][$m][$j]==$id_groupe[$i]) {
					//echo "Le groupe est sélectionné: $id_groupe[$i]<br />\n";
					if(!in_array($login_ele[$j],$tab_eleve[$id_groupe[$i]][$m])) {
						// On affecte l'élève dans le groupe
						//$sql="INSERT INTO j_eleves_groupes SET login='".$login_ele[$j]."', id_groupe='".$id_groupe[$i]."', periode='$num_periode';";
						//$insert=mysql_query($sql);
						$requete_insertion="INSERT INTO j_eleves_groupes SET login='".$login_ele[$j]."', id_groupe='".$id_groupe[$i]."', periode='$m';";
	
						// Vérification s'il y a bien déjà une association du groupe avec la classe:
						$sql="SELECT 1=1 FROM j_groupes_classes WHERE id_classe='$current_id_classe_ele[$m]' AND id_groupe='".$id_groupe[$i]."';";
						$test_clas_grp=mysql_query($sql);
						if(mysql_num_rows($test_clas_grp)==0) {
							// Récupérer les paramètres de j_groupes_classes pour le groupe actuel pour imposer le même dans l'association avec la classe de l'élève courant
							$sql="SELECT * FROM j_groupes_classes WHERE id_groupe='".$id_groupe[$i]."' LIMIT 1;";
							$res_grp=mysql_query($sql);
							$lig_grp=mysql_fetch_object($res_grp);
	
							$requete_insertion_assoc_grp_clas="INSERT INTO j_groupes_classes SET id_classe='$current_id_classe_ele[$m]', id_groupe='".$id_groupe[$i]."', priorite='$lig_grp->priorite', coef='$lig_grp->coef', categorie_id='$lig_grp->categorie_id', saisie_ects='$lig_grp->saisie_ects', valeur_ects='$lig_grp->valeur_ects', mode_moy='$lig_grp->mode_moy';";
						}
					}
				}
				else {
					//echo "Le groupe n'est pas sélectionné $id_groupe[$i]<br />\n";
					if(in_array($login_ele[$j],$tab_eleve[$id_groupe[$i]][$m])) {
						//echo "$login_ele[$j] était dans le groupe.<br />On va tester si on peut le désinscrire.<br />";
						// On va tester si on peut désinscrire l'élève
						if(test_before_eleve_grp_removal($login_ele[$j], $id_groupe[$i], $m)) {
							//echo "On peut le désinscrire.<br />";
							// On désinscrit effectivement
							$sql="DELETE FROM j_eleves_groupes WHERE login='".$login_ele[$j]."' AND id_groupe='".$id_groupe[$i]."' AND periode='$m';";
							//echo "$sql<br />";
							$suppr=mysql_query($sql);
							if(!$suppr) {$msg.="Echec de la désinscription de $login_ele[$j] du groupe $nom_groupe[$i] ($id_groupe[$i]) pour la période $m.<br />";}
							else {$nb_modif++;}
						}
						else {
							$msg.="Des données bloquent la désincription de ".get_nom_prenom_eleve($login_ele[$j])." du groupe ".$nom_groupe[$i]." ($id_groupe[$i]) pour la période $m.<br />";
							$temoin_insertion_possible="n";
						}
					}
				}

				if(($temoin_insertion_possible=="y")&&($requete_insertion!='')) {
					// On affecte l'élève dans son nouveau groupe
					$insert=mysql_query($requete_insertion);
					if(!$insert) {$msg.="Echec de l'inscription de $login_ele[$j] dans le groupe $nom_groupe[$i] ($id_groupe[$i]) pour la période $m.<br />";} else {$nb_modif++;}
					if($requete_insertion_assoc_grp_clas!='') {
						$insert=mysql_query($requete_insertion_assoc_grp_clas);
						if(!$insert) {$msg.="Echec de l'insertion de l'association du groupe $nom_groupe[$i] ($id_groupe[$i]) avec la classe $current_id_classe_ele[$m].<br />";}
					}
				}

			}
	
		}
	}

	if($nb_modif>0) {
		$msg.="$nb_modif modification(s) enregistrée(s).<br />";
	}
}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
$titre_page = "Répartition d'élèves dans des groupes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

//debug_var();

//=================================================================================================
// Choix de la classe 

if(!isset($id_classe)) {
	echo "<p class='bold'\n>";
	echo "<a href=\"../classes/index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	echo "</p>\n";

	echo "<p><b>Choix des classes&nbsp;:</b><br />\n";

	$sql="SELECT DISTINCT c.* FROM classes c ORDER BY classe;";
	$call_classes=mysql_query($sql);
	$nb_classes=mysql_num_rows($call_classes);

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

	// Affichage sur 3 colonnes
	$nb_classes_par_colonne=round($nb_classes/3);

	echo "<table width='100%' summary='Choix des classes'>\n";
	echo "<tr valign='top' align='center'>\n";

	$cpt = 0;

	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";

	while($lig_clas=mysql_fetch_object($call_classes)) {

		//affichage 2 colonnes
		if(($cpt>0)&&(round($cpt/$nb_classes_par_colonne)==$cpt/$nb_classes_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='id_classe[]' id='tab_id_classe_$cpt' value='$lig_clas->id' onchange='change_style_classe($cpt)' /> $lig_clas->classe</label>";
		echo "<br />\n";
		$cpt++;
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('tab_id_classe_'+k)){
				document.getElementById('tab_id_classe_'+k).checked = mode;
				change_style_classe(k);
			}
		}
	}

	function change_style_classe(num) {
		if(document.getElementById('tab_id_classe_'+num)) {
			if(document.getElementById('tab_id_classe_'+num).checked) {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";

	require("../lib/footer.inc.php");
	die();

}

// Choix des groupes
if(!isset($id_groupe)) {
	echo "<p class='bold'\n>";
	echo "<a href=\"../classes/index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
	echo "</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

	echo "<p class='bold'>Cochez les enseignements&nbsp;:</p>\n";

	// Liste des enseignements de la classe $id_classe
	echo "<table class='boireaus' summary='Choix des enseignements'>\n";
	echo "<tr>\n";
	for($i=0;$i<count($id_classe);$i++) {
		$classe=get_class_from_id($id_classe[$i]);
		echo "<th>$classe</th>\n";
	}
	echo "</tr>\n";

	$cpt=0;
	echo "<tr>\n";
	$alt=1;
	for($i=0;$i<count($id_classe);$i++) {
		$alt=$alt*(-1);

		echo "<td class='lig$alt' style='text-align:left; vertical-align:top;'>\n";
		$groups = get_groups_for_class($id_classe[$i],"","n");
		if(count($groups)==0){
			echo "<p>Aucun enseignement n'a encore été créé dans cette classe.</p>\n";
		}
		else {
			echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
	
			foreach ($groups as $group) {
				$current_group=get_group($group["id"]);
		
				//echo "<input type='checkbox' name='id_groupe[]' id='id_groupe_$cpt' value='".$current_group['id']."' onchange='change_style_grp($cpt)' /><label id='label_id_groupe_$cpt' for='id_groupe_$cpt'>".$current_group['name'];
			echo "<input type='checkbox' name='id_groupe[]' id='id_groupe_$cpt' value='".$current_group['id']."' onchange='change_style_grp($cpt);controle_doublons($cpt);' /><label id='label_id_groupe_$cpt' for='id_groupe_$cpt' title=\"Enseignement de ".$current_group['matiere']['matiere']." dispensé en ".$current_group['classlist_string']." par ".$current_group['profs']['proflist_string']."\">".$current_group['name'];
				echo "<span style='font-size:x-small;'>";
				echo " (<i>".$current_group['description']."</i>)";
				if(count($current_group["classes"]["list"])>1) {echo " en ".$current_group['classlist_string'];}
				echo " <span style='color:red'>".$current_group['id']."</span>";
				echo "</span>";
				echo "</label><br />\n";
				$cpt++;
	
			}
		}
		echo "</td>\n";
	}
	echo "</tr>\n";
	echo "</table>\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";


	echo "<script type='text/javascript'>

	function change_style_grp(num) {
		if(document.getElementById('id_groupe_'+num)) {
			if(document.getElementById('id_groupe_'+num).checked) {
				document.getElementById('label_id_groupe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_id_groupe_'+num).style.fontWeight='normal';
			}
		}
	}

	function controle_doublons(num) {
		if(document.getElementById('id_groupe_'+num)) {
			valeur_id_groupe=document.getElementById('id_groupe_'+num).value;
			//alert('valeur_id_groupe='+valeur_id_groupe)
			if(document.getElementById('id_groupe_'+num).checked) {
				for(i=0;i<$cpt;i++) {
					if(i!=num) {
						if(document.getElementById('id_groupe_'+i)) {
							//alert('document.getElementById(id_groupe_'+i+').value='+document.getElementById('id_groupe_'+i).value)
							if(document.getElementById('id_groupe_'+i).value==valeur_id_groupe) {
								document.getElementById('id_groupe_'+i).checked=true;
								change_style_grp(i);
							}
						}
					}
				}
			}
			else {
				for(i=0;i<$cpt;i++) {
					if(i!=num) {
						if(document.getElementById('id_groupe_'+i)) {
							//alert('document.getElementById(id_groupe_'+i+').value='+document.getElementById('id_groupe_'+i).value)
							if(document.getElementById('id_groupe_'+i).value==valeur_id_groupe) {
								document.getElementById('id_groupe_'+i).checked=false;
								change_style_grp(i);
							}
						}
					}
				}
			}
		}
	}

</script>\n";

	require("../lib/footer.inc.php");
	die();
}

if(!isset($num_periode)) {

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form0' method='post'>\n";
	echo "<p class='bold'\n>";
	echo "<a href=\"../classes/index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";

	echo " | \n";
	echo "<input type='submit' name='Autre_groupe' value=\"Choisir d'autres groupes\" />\n";
	for($i=0;$i<count($id_classe);$i++) {echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";}
	echo "</p>\n";
	echo "</form>\n";

	echo "<p class='bold'>Choisissez la période&nbsp;</p>\n";

	$maxper=0;
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
	for($i=0;$i<count($id_classe);$i++) {

		$sql="SELECT MAX(num_periode) AS maxper FROM periodes WHERE id_classe='".$id_classe[$i]."';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
			$lig_per=mysql_fetch_object($res);
			if($lig_per->maxper>$maxper) {$maxper=$lig_per->maxper;}
		}
		else {
			echo "La classe ".get_class_from_id($id_classe[$i])." n'a pas de période?<br />\n";
		}
	}

	for($i=1;$i<=$maxper;$i++) {
		echo "<input type='radio' name='num_periode' id='num_periode_$i' value='$i' ";
		if($i==1) {echo "checked ";}
		echo "/><label for='num_periode_$i'> Période $i</label>\n";
	}

	$tmp_id_grp=array();
	for($i=0;$i<count($id_groupe);$i++) {
		if(!in_array($id_groupe[$i],$tmp_id_grp)) {
			echo "<input type='hidden' name='id_groupe[]' value='$id_groupe[$i]' />\n";
			$tmp_id_grp[]=$id_groupe[$i];
		}
	}
	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<br />\n";
	echo "<p><em>NOTE&nbsp;:</em> Vous allez effectuer la répartition entre les groupes sur une période dans un premier temps, et dans un deuxième temps, vous pourrez recopier le paramétrage de cette période pour d'autres périodes.</p>\n";

	require("../lib/footer.inc.php");
	die();
}

//=================================================================================================

// On passe à la répartition
if(!isset($_POST['recopie_select'])) {
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form0' method='post'>\n";

	echo "<p class='bold'\n>";
	echo "<a href=\"../classes/index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

	// Il faut récupérer toutes les classes associées aux groupes choisis,
	// sinon lorsqu'on trie par classe on ne retient que la classe sélectionnée au départ 
	// (si on ne prend pas soin de sélectionner toutes les classes)
	$id_classe=array();
	for($i=0;$i<count($id_groupe);$i++) {
		echo "<input type='hidden' name='id_groupe[]' value='$id_groupe[$i]' />\n";
		$sql="SELECT DISTINCT id_classe FROM j_groupes_classes WHERE id_groupe='".$id_groupe[$i]."';";
		$res_clas_grp=mysql_query($sql);
		if(mysql_num_rows($res_clas_grp)>0) {
			while($lig_clas_grp=mysql_fetch_object($res_clas_grp)) {
				if(!in_array($lig_clas_grp->id_classe, $id_classe)) {$id_classe[]=$lig_clas_grp->id_classe;}
			}
		}
	}

	for($i=0;$i<count($id_classe);$i++) {echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";}

	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";

	echo " | \n";

	echo "<input type='button' name='Autre_periode' id='Autre_periode' value=\"Choisir d'autres périodes\" onclick=\"confirm_changement_periode(change, '$themessage');\" style='display:none' />\n";
	echo "<noscript>";
	echo " <input type='submit' name='Autre_periode' value=\"Choisir d'autres périodes\" />\n";
	echo "</noscript>";
	echo "</p>\n";
	echo "</form>\n";

	//===============================
	echo "<div style='float:right; text-align:center; width:15em;'>\n";
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form2' method='post'>\n";
	echo "<fieldset style='padding-top: 8px; padding-bottom: 8px;  margin-left: 3px; margin-right: auto; background-image: url(\"../images/background/opacite50.png\");'>\n";
	for($i=0;$i<count($id_classe);$i++) {echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";}
	for($i=0;$i<count($id_groupe);$i++) {echo "<input type='hidden' name='id_groupe[]' value='$id_groupe[$i]' />\n";}
		echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";
	echo "<input type='hidden' name='recopie_select' value='y' />\n";

	echo "<input type='button' name='Passer_a_copie' id='Passer_a_copie' value=\"Recopier des affectations\" onclick=\"confirm_Passer_a_copie(change, '$themessage');\" style='display:none' /> pour d'autres périodes\n";
	echo "<noscript>";
	echo "<input type='submit' name='Passer_a_copie' value='Recopier des affectations' /> pour d'autres périodes\n";
	echo "</noscript>";

	echo "</fieldset>\n";
	echo "</form>\n";
	echo "</div>\n";
	//===============================

	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	document.getElementById('Autre_periode').style.display='';
	document.getElementById('Passer_a_copie').style.display='';

	function confirm_changement_periode(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form0.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form0.submit();
			}
		}
	}

	function confirm_Passer_a_copie(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form2.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form2.submit();
			}
		}
	}

	function confirm_Passer_a_tel_tri(thechange, themessage, tri)
	{
		if(tri=='classe') {
			document.getElementById('order_by_classe').checked=true;
		}
		else {
			document.getElementById('order_by_nom').checked=true;
		}

		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form3.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form3.submit();
			}
		}
	}
</script>\n";

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
	echo "<fieldset style='padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: 1em; background-image: url(\"../images/background/opacite50.png\"); '>\n";
	echo add_token_field();
	for($i=0;$i<count($id_classe);$i++) {echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";}
	for($i=0;$i<count($id_groupe);$i++) {echo "<input type='hidden' name='id_groupe[]' value='$id_groupe[$i]' />\n";}
	
	echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";
	echo "<input type='hidden' name='repartition_eleves' value='y' />\n";
	echo "<input type='hidden' name='order_by' value='$order_by' />\n";
	
	echo "<p class='bold'>Répartition des élèves dans les groupes pour la période $num_periode&nbsp;:</p>\n";

	// Pour le moment, on ne récupère que les élèves déjà inscrits dans ces groupes.
	// A FAIRE: Permettre d'afficher tous les élèves des classes concernées

	$tab_eleve=array();
	//$tmp_tab_eleve=array();
	echo "<table class='boireaus' summary='Répartition des élèves'>\n";
	echo "<tr>\n";
	//echo "<th>Elève</th>\n";
	echo "<th><input type='submit' name='Valider_repartition2' value='Enregistrer' />\n";
	//echo "<br /><a href=\"javascript:document.getElementById('order_by_nom').checked=true;document.form3.submit();\">Elève</a>";
	echo "<br /><a href=\"javascript:confirm_Passer_a_tel_tri(change, '$themessage', 'nom');\">Elève</a>";
	echo "</th>\n";
	//echo "<th><a href=\"javascript:document.getElementById('order_by_classe').checked=true;document.form3.submit();\">Classe</a></th>\n";
	echo "<th><a href=\"javascript:confirm_Passer_a_tel_tri(change, '$themessage', 'classe');\">Classe</a></th>\n";
	for($i=0;$i<count($id_groupe);$i++) {
		echo "<th>\n";
		$group[$i]=get_group($id_groupe[$i]);
		echo $group[$i]['name'];
		echo "<br />\n";
		echo "<span style='font-size:small;'>".$group[$i]['classlist_string']."</span>\n";
		echo "<br /><a href='edit_group.php?id_groupe=".$id_groupe[$i]."&amp;mode=regroupement' title=\"Paramétrer le groupe n°".$id_groupe[$i]."\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><span style='font-size:small; color:red;'>".$id_groupe[$i]."</span></a>";
		echo "<br />".preg_replace("/,/","<br />",$group[$i]['profs']['proflist_string']);

		/*
		//$tmp_tab_eleve=array_merge($tmp_tab_eleve,$group[$i]["eleves"][$num_periode]["list"]);
		for($j=0;$j<count($group[$i]["eleves"][$num_periode]["list"]);$j++) {
			if(!in_array($group[$i]["eleves"][$num_periode]["list"][$j],$tab_eleve)) {
				$tab_eleve[]=$group[$i]["eleves"][$num_periode]["list"][$j];
			}
		}
		*/
		echo "</th>\n";
	}
	echo "<th>\n";
	echo "Info\n";
	/*
	echo "<table><tr><td>";
	for($j=0;$j<count($tab_eleve);$j++) {
		echo "$tab_eleve[$j]<br />";
	}
	echo "</td><td>";
	*/

	/*
	if($order_by=='classe') {
		$tmp_tab_eleve=$tab_eleve;
		unset($tab_eleve);

		for($i=0;$i<count($id_classe);$i++) {
			for($j=0;$j<count($tmp_tab_eleve);$j++) {
				$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe[$i]' AND login='$tmp_tab_eleve[$j]' AND periode='$num_periode';";
				//echo "$sql<br />";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0) {
					$tab_eleve[]=$tmp_tab_eleve[$j];
					echo "$tmp_tab_eleve[$j]<br />";
				}
			}
		}
	}
	else {
		// On trie suivant le login... ce n'est pas forcément correct
		//sort($tab_eleve);
	}
	*/

	$tab_eleve=array();

	$chaine_groupes="jeg.id_groupe='".$id_groupe[0]."'";
	for($loop=1;$loop<count($id_groupe);$loop++) {
		$chaine_groupes.=" OR jeg.id_groupe='".$id_groupe[$loop]."'";
	}

	if($order_by=='classe') {

		for($loop=0;$loop<count($id_classe);$loop++) {
			$sql="SELECT DISTINCT jeg.login FROM j_eleves_classes jec, j_eleves_groupes jeg, eleves e WHERE (jec.id_classe='".$id_classe[$loop]."' AND jec.periode='$num_periode' AND jec.login=jeg.login AND jeg.login=e.login AND ($chaine_groupes) AND jeg.periode='$num_periode') ORDER BY e.nom, e.prenom;";
			$res_clas_grp=mysql_query($sql);
			if(mysql_num_rows($res_clas_grp)>0) {
				while($lig_clas_grp=mysql_fetch_object($res_clas_grp)) {
					$tab_eleve[]=$lig_clas_grp->login;
				}
			}
		}
	}
	else {
		$sql="SELECT DISTINCT jeg.login FROM j_eleves_groupes jeg, eleves e WHERE jeg.login=e.login AND ($chaine_groupes) AND jeg.periode='$num_periode' ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />";
		$res_ele_grp=mysql_query($sql);
		if(mysql_num_rows($res_ele_grp)>0) {
			while($lig_ele_grp=mysql_fetch_object($res_ele_grp)) {
				$tab_eleve[]=$lig_ele_grp->login;
			}
		}
	}

	//echo "</td></tr></table>";
	echo "</th>\n";
	echo "</tr>\n";
	$alt=1;
	
	//$tab_eleve=array_unique($tmp_tab_eleve);
	
	/*
	echo "<tr>\n";
	echo "<td>\n";
	for($j=0;$j<count($tmp_tab_eleve);$j++) {
	echo $tmp_tab_eleve[$j].", ";
	}
	echo "</td>\n";
	echo "</tr>\n";
	
	echo "<tr>\n";
	echo "<td>\n";
	for($j=0;$j<count($tab_eleve);$j++) {
	echo $tab_eleve[$j].", ";
	}
	echo "</td>\n";
	echo "</tr>\n";
	*/

	echo "<tr id='tr_effectifs' style='display:none;'>\n";
	echo "<th>Effectifs</th>\n";
	echo "<th id='effectif_total'></th>\n";
	for($i=0;$i<count($id_groupe);$i++) {
		echo "<th id='effectif_colonne_$i'></th>\n";
	}
	echo "<th></th>\n";
	echo "</tr>\n";

	// LISTE FOIREUSE UNE FOIS QU'ON A VALIDE UNE FOIS
	//for($j=0;$j<count($group["eleves"]["all"]["list"]);$j++) {
	$cpt=0;
	$classe_prec="";
	$nb_col=2+count($id_groupe)+1;
	//foreach($tab_eleve as $key => $login_ele) {
	for($j=0;$j<count($tab_eleve);$j++) {
		$login_ele=$tab_eleve[$j];
		$tmp_tab_class_ele=get_class_from_ele_login($login_ele);

		if(($classe_prec!="")&&($tmp_tab_class_ele["liste"]!=$classe_prec)) {
			if($order_by=='classe') {
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td colspan='$nb_col' style='background-color:grey;'>".$tmp_tab_class_ele["liste"]."</td>\n";
				echo "</tr>\n";
			}
		}

		$classe_prec=$tmp_tab_class_ele["liste"];

		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td>\n";
		echo "<input type='hidden' name='login_ele[$cpt]' value='".$login_ele."' />\n";
		$nom_prenom_ele=get_nom_prenom_eleve($login_ele);
		echo $nom_prenom_ele;
		echo "</td>\n";
	
		echo "<td>\n";
		echo $tmp_tab_class_ele["liste"];
		echo "</td>\n";

		$nb_grp_ele=0;
		$info_plusieurs_grp_ele="";
		$ligne_si_desinscription_possible="";
		$ligne_si_desinscription_impossible="";
		$temoin_desinscription_impossible="n";
		for($i=0;$i<count($id_groupe);$i++) {
			$k=$i+1;
			/*
			echo "<td>\n";
			echo "<input type='radio' name='grp_eleve[$cpt]' value='".$id_groupe[$i]."' onchange='changement()' title=\"$tab_eleve[$j] -&gt; ".$group[$i]['name']." de ".$group[$i]['classlist_string']."\" ";
			if(in_array($login_ele,$group[$i]["eleves"][$num_periode]["list"])) {
				echo "checked ";
				if($nb_grp_ele>0) {$info_plusieurs_grp_ele.=", ";}
				$info_plusieurs_grp_ele.=$group[$i]['name']." <span style='color:red; font-size:x-small;'>($k)</span>";
				$nb_grp_ele++;
			}
			echo "/>\n";
			echo "</td>\n";
			*/

			$ligne_si_desinscription_possible.="<td onclick=\"document.getElementById('grp_eleve_".$i."_".$cpt."').checked=true;calcule_effectifs();changement();\" title=\"Mettre $nom_prenom_ele dans le groupe ".$group[$i]['name']." de ".$group[$i]['classlist_string']."\">\n";
			$ligne_si_desinscription_possible.="<input type='radio' name='grp_eleve[$cpt]' id='grp_eleve_".$i."_".$cpt."' value='".$id_groupe[$i]."' onchange='calcule_effectifs();changement()' ";
			if(in_array($login_ele,$group[$i]["eleves"][$num_periode]["list"])) {
				$ligne_si_desinscription_possible.="checked ";
				if($nb_grp_ele>0) {$info_plusieurs_grp_ele.=", ";}
				$info_plusieurs_grp_ele.=$group[$i]['name']." <span style='color:red; font-size:x-small;'>($k)</span>";
				$nb_grp_ele++;
			}
			$ligne_si_desinscription_possible.="/>\n";
			$ligne_si_desinscription_possible.="</td>\n";


			$ligne_si_desinscription_impossible.="<td>\n";
			if(in_array($login_ele,$group[$i]["eleves"][$num_periode]["list"])) {
				$ligne_si_desinscription_impossible.="<img src='../images/enabled.png' width='20' height='20' alt='Affecté dans le groupe' title='Affecté dans le groupe' />\n";

				if(!test_before_eleve_grp_removal($login_ele, $id_groupe[$i], $num_periode)) {
					$ligne_si_desinscription_impossible.="<img src='../images/icons/securite.png' width='16' height='16' alt='Bulletin ou carnet de notes non vide' title='Bulletin ou carnet de notes non vide' />\n";
					$temoin_desinscription_impossible="y";
				}
			}
			$ligne_si_desinscription_impossible.="</td>\n";
		}

		//if($nb_grp_ele>1) {echo $ligne_si_desinscription_impossible;}
		if($temoin_desinscription_impossible=="y") {echo $ligne_si_desinscription_impossible;}
		else {echo $ligne_si_desinscription_possible;}

		echo "<td>\n";
		if($nb_grp_ele>1) {echo $info_plusieurs_grp_ele;}
		else {echo "&nbsp;";}
		echo "</td>\n";
		echo "</tr>\n";
		$cpt++;
	}

	echo "<tr>\n";
	echo "<th>\n";
	//echo "&nbsp;";
	echo "<input type='submit' name='Valider_repartition' value='Enregistrer' />\n";
	echo "</th>\n";
	echo "<th>\n";
	echo "&nbsp;";
	echo "</th>\n";
	for($i=0;$i<count($id_groupe);$i++) {
		echo "<th>\n";
		echo "<a href=\"javascript:CocheColonne($i);changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>\n";
		//echo " / <a href=\"javascript:DecocheColonne($i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
		echo "</th>\n";
	}
	echo "<th>\n";
	echo "&nbsp;";
	echo "</th>\n";
	echo "</tr>\n";


	echo "<input type='hidden' name='nb_ele' value='$cpt' />\n";
	echo "<input type='hidden' name='enregistrer_repartition' value='y' />\n";
	echo "</table>\n";
	echo "<p align='center'><input type='submit' name='Valider_repartition3' value='Enregistrer la répartition' /></p>\n";
	echo "</fieldset>\n";
	echo "</form>\n";

	echo "<br />\n";

	//===============================
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form3' method='post'>\n";
	echo "<fieldset style='padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto; background-image: url(\"../images/background/opacite50.png\"); '>\n";
	echo add_token_field();
	for($i=0;$i<count($id_classe);$i++) {echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";}
	for($i=0;$i<count($id_groupe);$i++) {echo "<input type='hidden' name='id_groupe[]' value='$id_groupe[$i]' />\n";}
	echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";
	//echo "<input type='hidden' name='recopie_select' value='y' />\n";
	echo "<p>Trier par<br /><input type='radio' name='order_by' id='order_by_nom' value='' ";
	if($order_by!='classe') {echo "checked ";}
	echo "/><label for='order_by_nom'> Nom</label><br />\n";
	echo "<input type='radio' name='order_by' id='order_by_classe' value='classe' ";
	if($order_by=='classe') {echo "checked ";}
	echo "/><label for='order_by_classe'> Classe</label><br />\n";
	echo "<input type='button' name='modifier_le_tri' id='modifier_le_tri' value=\"Modifier le tri\" onclick=\"confirm_Modif_tri(change, '$themessage');\" style='display:none' />\n";
	echo "<noscript>";
	echo "<input type='submit' value='Modifier le tri' />\n";
	echo "</noscript>";
	echo "</fieldset>\n";
	echo "</form>\n";
	//===============================



	echo "<p><br /></p>\n";
	echo "<script type='text/javascript'>

	function CocheColonne(indice_grp) {
		for (var ki=0;ki<$cpt;ki++) {
			if(document.getElementById('grp_eleve_'+indice_grp+'_'+ki)){
				type_cible=document.getElementById('grp_eleve_'+indice_grp+'_'+ki).getAttribute('type');
				if(type_cible=='radio') {
					document.getElementById('grp_eleve_'+indice_grp+'_'+ki).checked = true;
				}
			}
		}
	}

	/*
	function DecocheColonne(indice_grp) {
		for (var ki=0;ki<$cpt;ki++) {
			if(document.getElementById('grp_eleve_'+indice_grp+'_'+ki)){
				type_cible=document.getElementById('grp_eleve_'+indice_grp+'_'+ki).getAttribute('type');
				if(type_cible=='radio') {
					document.getElementById('grp_eleve_'+indice_grp+'_'+ki).checked = false;
				}
			}
		}
	}
	*/

	document.getElementById('modifier_le_tri').style.display='';
	function confirm_Modif_tri(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form3.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form3.submit();
			}
		}
	}

	function calcule_effectifs() {
		for (var i=0;i<".count($id_groupe).";i++) {
			total=0;
			for (var ki=0;ki<$cpt;ki++) {
				if(document.getElementById('grp_eleve_'+i+'_'+ki)) {
					if(document.getElementById('grp_eleve_'+i+'_'+ki).checked==true) {total++;}
				}
			}
			document.getElementById('effectif_colonne_'+i).innerHTML=total;
		}
		document.getElementById('effectif_total').innerHTML=$cpt;
	}
	calcule_effectifs();
	document.getElementById('tr_effectifs').style.display='';
</script>\n";

	//====================================
	/*
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
	for($i=0;$i<count($id_classe);$i++) {echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";}
	for($i=0;$i<count($id_groupe);$i++) {echo "<input type='hidden' name='id_groupe[]' value='$id_groupe[$i]' />\n";}
	
	echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";
	echo "<input type='hidden' name='recopie_select' value='y' />\n";

	echo "<input type='submit' name='Passer_a_copie' value='Recopier des affectations' /> pour d'autres périodes\n";
	echo "</form>\n";
	*/
}
else {
	// Recopie ou copie inverse des sélections pour d'autres périodes

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form0' method='post'>\n";
	echo "<p class='bold'\n>";
	echo "<a href=\"../classes/index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

	for($i=0;$i<count($id_classe);$i++) {echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";}
	for($i=0;$i<count($id_groupe);$i++) {echo "<input type='hidden' name='id_groupe[]' value='$id_groupe[$i]' />\n";}

	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";

	echo " | \n";
	echo "<input type='submit' name='Autre_periode' value=\"Choisir d'autres périodes\" />\n";
	echo "</p>\n";
	echo "</form>\n";

	//===============================
	echo "<div style='float:right; text-align:center; width:15em;'>\n";
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form2' method='post'>\n";
	for($i=0;$i<count($id_classe);$i++) {echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";}
	for($i=0;$i<count($id_groupe);$i++) {echo "<input type='hidden' name='id_groupe[]' value='$id_groupe[$i]' />\n";}
	
	echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";

	echo "<input type='submit' name='Retour_affect' value='Retour aux affectations' />\n";
	echo "</form>\n";
	echo "</div>\n";
	//===============================

	$maxper=0;
	for($i=0;$i<count($id_classe);$i++) {

		$sql="SELECT MAX(num_periode) AS maxper FROM periodes WHERE id_classe='".$id_classe[$i]."';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$lig_per=mysql_fetch_object($res);
			if($lig_per->maxper>$maxper) {$maxper=$lig_per->maxper;}
		}
		else {
			echo "La classe ".get_class_from_id($id_classe[$i])." n'a pas de période?<br />\n";
		}
	}

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
	echo add_token_field();
	for($i=0;$i<count($id_classe);$i++) {echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";}
	for($i=0;$i<count($id_groupe);$i++) {echo "<input type='hidden' name='id_groupe[]' value='$id_groupe[$i]' />\n";}
	
	echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";
	echo "<input type='hidden' name='recopie_select' value='y' />\n";
	echo "<input type='hidden' name='order_by' value='$order_by' />\n";

	echo "<p class='bold'>Recopie de la répartition des élèves dans les groupes&nbsp;:</p>\n";

	// Pour le moment, on ne récupère que les élèves déjà inscrits dans ces groupes.
	// A FAIRE: Permettre d'afficher tous les élèves des classes concernées

	$tab_eleve=array();
	//$tmp_tab_eleve=array();
	echo "<table class='boireaus' summary='Recopie de la répartition des élèves'>\n";
	echo "<tr>\n";
	echo "<th rowspan='2'><a href=\"javascript:document.getElementById('order_by_nom').checked=true;document.form3.submit();\">Elève</a><br />\n";
	echo "<br /><input type='submit' name='Valider_recopie2' value='Valider' />\n";
	echo "</th>\n";
	//echo "<th rowspan='2'>Classe</th>\n";
	echo "<th rowspan='2'><a href=\"javascript:document.getElementById('order_by_classe').checked=true;document.form3.submit();\">Classe</a></th>\n";
	for($i=0;$i<count($id_groupe);$i++) {
		echo "<th colspan='$maxper'>";
		$group[$i]=get_group($id_groupe[$i]);
		echo $group[$i]['name'];
		echo "<br />";
		echo "<span style='font-size:small;'>".$group[$i]['classlist_string']."</span>";

		echo "<br /><span style='font-size:small; color:red;'>".$id_groupe[$i]."</span>";

		for($j=0;$j<count($group[$i]["eleves"][$num_periode]["list"]);$j++) {
			if(!in_array($group[$i]["eleves"][$num_periode]["list"][$j],$tab_eleve)) {
				$tab_eleve[]=$group[$i]["eleves"][$num_periode]["list"][$j];
				//echo $group[$i]["eleves"][$num_periode]["list"][$j]."<br />";
			}
		}

		echo "</th>\n";
	}
	//echo "<th rowspan='2'>Info</th>\n";
	echo "</tr>\n";



	echo "<tr>\n";
	//echo "<th>Elève</th>\n";
	//echo "<th>Classe</th>\n";
	for($i=0;$i<count($id_groupe);$i++) {
		for($m=1;$m<=$maxper;$m++) {
			echo "<th>";
			echo "P$m";
			echo "</th>\n";
		}
	}
	//echo "<th>Info</th>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<th>Modèle de sélection</th>\n";

	echo "<th>\n";
	echo "&nbsp;\n";
	//echo "\$order_by=$order_by<br />";
	//$order_by='classe';
	if($order_by=='classe') {
		$tmp_tab_eleve=$tab_eleve;
		unset($tab_eleve);

		for($i=0;$i<count($id_classe);$i++) {
			for($j=0;$j<count($tmp_tab_eleve);$j++) {
				$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe[$i]' AND login='$tmp_tab_eleve[$j]' AND periode='$num_periode';";
				//echo "$sql<br />";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0) {
					$tab_eleve[]=$tmp_tab_eleve[$j];
					//echo "$tmp_tab_eleve[$j]<br />";
				}
			}
		}

	}
	else {
		sort($tab_eleve);
	}
	/*
	if($order_by=='classe') {
		$tmp_tab_eleve=$tab_eleve;
		unset($tab_eleve);

		for($i=0;$i<count($id_classe);$i++) {
			for($j=0;$j<count($tmp_tab_eleve);$j++) {
				$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe[$i] AND login='$tmp_tab_eleve[$j]' AND periode='$num_periode';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0) {
					$tab_eleve[]=$tmp_tab_eleve[$j];
				}
			}
		}

	}
	*/
	echo "</th>\n";

	// 20130919
	$tab_champs_modele_coller=array();
	for($i=0;$i<count($id_groupe);$i++) {
		for($m=1;$m<=$maxper;$m++) {
			echo "<th>";
			echo "<input type='radio' name='modele' id='modele_".$i."_".$m."' value='$i.$m' onchange=\"update_liens_coller_visibles();changement();\" />\n";
			$tab_champs_modele_coller[]=$i."_".$m;
			echo "</th>\n";
		}
	}
	//echo "<th>Info</th>\n";
	echo "</tr>\n";



	echo "<tr>\n";
	echo "<th>Coller la sélection</th>\n";
	echo "<th>&nbsp;</th>\n";
	for($i=0;$i<count($id_groupe);$i++) {
		for($m=1;$m<=$maxper;$m++) {
			echo "<th>";
			echo "<a id='coller_".$i."_".$m."' href='javascript:copier_selection($i,$m);changement();'><img src='../images/icons/coller_23x24.png' width='23' height='24' alt='Coller la sélection' title='Coller la sélection' /></a>\n";
			echo "</th>\n";
		}

	}
	//echo "<th>Info</th>\n";
	echo "</tr>\n";




	$alt=1;

	//$tab_eleve=array_unique($tmp_tab_eleve);
	//sort($tab_eleve);
	
	$cpt=0;
	for($j=0;$j<count($tab_eleve);$j++) {
		$login_ele=$tab_eleve[$j];
	
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td>";
		echo "<input type='hidden' name='login_ele[$cpt]' value='".$login_ele."' />\n";
		echo get_nom_prenom_eleve($login_ele);
		echo "</td>\n";
	
		echo "<td>";
		$tmp_tab_class_ele=get_class_from_ele_login($login_ele);
		echo $tmp_tab_class_ele["liste"];
		echo "</td>\n";
	
		$nb_grp_ele=0;
		$info_plusieurs_grp_ele="";
		for($i=0;$i<count($id_groupe);$i++) {
			$k=$i+1;
				for($m=1;$m<=$maxper;$m++) {
					echo "<td>\n";

					if(in_array($login_ele,$group[$i]["eleves"][$m]["list"])) {
						if(!test_before_eleve_grp_removal($login_ele, $id_groupe[$i], $m)) {
							echo "<img src='../images/enabled.png' width='20' height='20' alt='Affecté dans le groupe' title='Affecté dans le groupe' />\n";
							echo "<input type='hidden' name='grp_eleve_".$i."_".$m."[$cpt]' id='grp_eleve_".$i."_".$m."_$cpt' value='$id_groupe[$i]' />\n";
						}
						else {
							echo "<input type='checkbox' name='grp_eleve_".$i."_".$m."[$cpt]' id='grp_eleve_".$i."_".$m."_$cpt' value='".$id_groupe[$i]."' onchange='changement()' title=\"$tab_eleve[$j] -&gt; ".$group[$i]['name']." de ".$group[$i]['classlist_string']."\" checked />\n";
						}
					}
					else {
						echo "<input type='checkbox' name='grp_eleve_".$i."_".$m."[$cpt]' value='".$id_groupe[$i]."' id='grp_eleve_".$i."_".$m."_$cpt' onchange='changement()' title=\"$tab_eleve[$j] -&gt; ".$group[$i]['name']." de ".$group[$i]['classlist_string']."\" />\n";
					}

					/*
					//echo "\$m=$m<br />";
					if($m==$num_periode) {
						echo "<input type='hidden' name='grp_eleve_".$m."[$cpt]' id='grp_eleve_$cpt' ";
						if(in_array($login_ele,$group[$i]["eleves"][$m]["list"])) {
							echo "value='".$id_groupe[$i]."' />\n";

							echo "<img src='../images/enabled.png' width='20' height='20' alt='Groupe sélectionné' />\n";
						}
						else {
							echo "value='' />\n";
						}
						//echo "/>\n";
					}
					else {
						echo "<input type='checkbox' name='grp_eleve_".$m."[$cpt]' value='".$id_groupe[$i]."' onchange='changement()' title=\"$tab_eleve[$j] -&gt; ".$group[$i]['name']." de ".$group[$i]['classlist_string']."\" ";
						if(in_array($login_ele,$group[$i]["eleves"][$m]["list"])) {
							echo "checked ";
							//if($nb_grp_ele>0) {$info_plusieurs_grp_ele.=", ";}
							//$info_plusieurs_grp_ele.=$group[$i]['name']." <span style='color:red; font-size:x-small;'>($k)</span>";
							$nb_grp_ele++;
						}
						echo "/>\n";
					}
					*/
					echo "</td>\n";
				}
		}
		/*




			$ligne_si_desinscription_possible.="<input type='radio' name='grp_eleve[$cpt]' value='".$id_groupe[$i]."' onchange='changement()' title=\"$tab_eleve[$j] -&gt; ".$group[$i]['name']." de ".$group[$i]['classlist_string']."\" ";
			if(in_array($login_ele,$group[$i]["eleves"][$num_periode]["list"])) {
				$ligne_si_desinscription_possible.="checked ";
				if($nb_grp_ele>0) {$info_plusieurs_grp_ele.=", ";}
				$info_plusieurs_grp_ele.=$group[$i]['name']." <span style='color:red; font-size:x-small;'>($k)</span>";
				$nb_grp_ele++;
			}
			$ligne_si_desinscription_possible.="/>\n";
			$ligne_si_desinscription_possible.="</td>\n";


			$ligne_si_desinscription_impossible.="<td>\n";
			if(in_array($login_ele,$group[$i]["eleves"][$num_periode]["list"])) {
				$ligne_si_desinscription_impossible.="<img src='../images/enabled.png' width='20' height='20' alt='Affecté dans le groupe' title='Affecté dans le groupe' />\n";

				if(!test_before_eleve_grp_removal($login_ele, $id_groupe[$i], $num_periode)) {
					$ligne_si_desinscription_impossible.="<img src='../images/icons/securite.png' width='16' height='16' alt='Bulletin ou carnet de notes non vide' title='Bulletin ou carnet de notes non vide' />\n";
					$temoin_desinscription_impossible="y";
				}
			}



		echo "<td>";
		if($nb_grp_ele==0) {echo "&nbsp;";}
		else {echo $info_plusieurs_grp_ele;}
		echo "</td>";
		*/
		echo "</tr>\n";
		$cpt++;
	}

	echo "<tr>\n";
	echo "<th>\n";
	//echo "&nbsp;\n";
	echo "<input type='submit' name='Valider_recopie' value='Valider' />\n";
	echo "</th>\n";

	echo "<th>\n";
	echo "&nbsp;\n";
	echo "</th>\n";
	for($i=0;$i<count($id_groupe);$i++) {
		for($m=1;$m<=$maxper;$m++) {
			echo "<th>\n";
			echo "<a href=\"javascript:CocheColonne($i,$m)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne($i,$m)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</th>\n";
		}
	}
	echo "</tr>\n";

	echo "<input type='hidden' name='nb_ele' value='$cpt' />\n";
	echo "<input type='hidden' name='nb_grp' value='".count($group)."' />\n";
	echo "<input type='hidden' name='maxper' value='$maxper' />\n";
	echo "<input type='hidden' name='enregistrer_recopie' value='y' />\n";
	echo "</table>\n";
	//echo "<input type='submit' name='Valider_recopie' value='Valider' />\n";
	echo "</form>\n";


	//===============================
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form3' method='post'>\n";
	for($i=0;$i<count($id_classe);$i++) {echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";}
	for($i=0;$i<count($id_groupe);$i++) {echo "<input type='hidden' name='id_groupe[]' value='$id_groupe[$i]' />\n";}
	echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";
	echo "<input type='hidden' name='recopie_select' value='y' />\n";
	echo "<p>Trier par<br /><input type='radio' name='order_by' id='order_by_nom' value='' ";
	if($order_by!='classe') {echo "checked ";}
	echo "/><label for='order_by_nom'> Nom</label><br />\n";
	echo "<input type='radio' name='order_by' id='order_by_classe' value='classe' ";
	if($order_by=='classe') {echo "checked ";}
	echo "/><label for='order_by_classe'> Classe</label><br />\n";
	echo "<input type='submit' value='Valider' />\n";
	echo "</form>\n";
	//===============================

	echo "<p><br /></p>\n";


	//===================================
/*
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form2' method='post'>\n";
	for($i=0;$i<count($id_classe);$i++) {echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";}
	for($i=0;$i<count($id_groupe);$i++) {echo "<input type='hidden' name='id_groupe[]' value='$id_groupe[$i]' />\n";}
	
	echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";

	echo "<input type='submit' name='Retour_affect' value='Retour aux affectations' />\n";
	echo "</form>\n";
*/

	// 20130919
	$init_coches="var tab_suffixe_coller=new Array();\n";
	for($loop=0;$loop<count($tab_champs_modele_coller);$loop++) {
		$init_coches.="
		if(document.getElementById('coller_".$tab_champs_modele_coller[$loop]."')) {
			document.getElementById('coller_".$tab_champs_modele_coller[$loop]."').style.display='none';
		}
		tab_suffixe_coller[$loop]='".$tab_champs_modele_coller[$loop]."';\n";
	}

	echo "<script type='text/javascript'>
	$init_coches;

	function update_liens_coller_visibles() {
		for(i=0;i<tab_suffixe_coller.length;i++) {
			if(document.getElementById('coller_'+tab_suffixe_coller[i])) {
				document.getElementById('coller_'+tab_suffixe_coller[i]).style.display='';
			}

			if(document.getElementById('modele_'+tab_suffixe_coller[i])) {
				if(document.getElementById('modele_'+tab_suffixe_coller[i]).checked==true) {
					if(document.getElementById('coller_'+tab_suffixe_coller[i])) {
						document.getElementById('coller_'+tab_suffixe_coller[i]).style.display='none';
					}
				}
			}
		}
	}

	function copier_selection(indice_grp,num_per) {
		// Récupération du bouton radio sélectionné pour trouver la colonne modèle.
		modele='';
		k=0;
		for(s=0;s<".count($id_groupe).";s++) {
			for(u=1;u<=$maxper;u++) {
				if(document.form1.modele[k].checked==true) {
					modele=document.form1.modele[k].value;
				}
				k++;
			}
		}
		//alert('modele='+modele);

		if(modele!='') {
			tab=modele.split('.');

			// Découpage du modèle formaté en 'indice_grp_modele.num_per_modele'
			indice_grp_modele=tab[0];
			num_per_modele=tab[1];

			// On parcourt les lignes élèves avec 'j'
			for(j=0;j<$cpt;j++) {
				//alert('Test du modele '+indice_grp_modele+'_'+num_per_modele+'_'+j+'...')
				if(document.getElementById('grp_eleve_'+indice_grp_modele+'_'+num_per_modele+'_'+j)) {
					// Le champ existe dans la colonne modèle pour l'élève n°j
					//alert('Le modele '+indice_grp_modele+'_'+num_per_modele+'_'+j+' existe.')
					type_cible=document.getElementById('grp_eleve_'+indice_grp+'_'+num_per+'_'+j).getAttribute('type');

					// Si le champ cible est de type checkbox on poursuit.
					// S'il est hidden, on ne tente pas de le décocher.
					if(type_cible=='checkbox') {
						type_modele=document.getElementById('grp_eleve_'+indice_grp_modele+'_'+num_per_modele+'_'+j).getAttribute('type');

						// Si le modèle est hidden, il est comme sélectionné/coché
						if(type_modele=='hidden') {
							document.getElementById('grp_eleve_'+indice_grp+'_'+num_per+'_'+j).checked=true;
						}
						else {
							// Sinon, on teste si le modèle est coché pour savoir s'il faut cocher ou décocher la cible
							if(document.getElementById('grp_eleve_'+indice_grp_modele+'_'+num_per_modele+'_'+j).checked) {
								document.getElementById('grp_eleve_'+indice_grp+'_'+num_per+'_'+j).checked=true;
							}
							else {
								document.getElementById('grp_eleve_'+indice_grp+'_'+num_per+'_'+j).checked=false;	
							}
						}
					}
				}
			}
		}
		else {
			alert(\"Vous n'avez pas coché le modèle de sélection dans la deuxième ligne du tableau.\")
		}
	}

	function CocheColonne(indice_grp,num_per) {
		for (var ki=0;ki<$cpt;ki++) {
			if(document.getElementById('grp_eleve_'+indice_grp+'_'+num_per+'_'+ki)){
				type_cible=document.getElementById('grp_eleve_'+indice_grp+'_'+num_per+'_'+ki).getAttribute('type');
				if(type_cible=='checkbox') {
					document.getElementById('grp_eleve_'+indice_grp+'_'+num_per+'_'+ki).checked = true;
				}
			}
		}
	}
	
	function DecocheColonne(indice_grp,num_per) {
		for (var ki=0;ki<$cpt;ki++) {
			if(document.getElementById('grp_eleve_'+indice_grp+'_'+num_per+'_'+ki)){
				type_cible=document.getElementById('grp_eleve_'+indice_grp+'_'+num_per+'_'+ki).getAttribute('type');
				if(type_cible=='checkbox') {
					document.getElementById('grp_eleve_'+indice_grp+'_'+num_per+'_'+ki).checked = false;
				}
			}
		}
	}
</script>\n";


}
require("../lib/footer.inc.php");
?>
