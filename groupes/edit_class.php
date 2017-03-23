<?php
/*
*
* Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$msg="";

$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL);
if (!is_numeric($id_classe)) {$id_classe = 0;}
$classe = get_classe($id_classe);

include("../lib/periodes.inc.php");

$affiche_categories=isset($_GET['affiche_categories']) ? $_GET['affiche_categories'] : (isset($_POST['affiche_categories']) ? $_POST["affiche_categories"] : 'n');

if(isset($_GET['forcer_recalcul_rang'])) {
	$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode DESC LIMIT 1;";
	$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_per)>0) {
		$lig_per=mysqli_fetch_object($res_per);
		$recalcul_rang="";
		for($i=0;$i<$lig_per->num_periode;$i++) {$recalcul_rang.="y";}
		$sql="UPDATE groupes SET recalcul_rang='$recalcul_rang' WHERE id in (SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe');";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$res) {
			$msg="Erreur lors de la programmation du recalcul des rangs pour cette classe.";
		}
		else {
			$msg="Recalcul des rangs programmé pour cette classe.";
		}
	}
	else {
		$msg="Aucune période n'est définie pour cette classe.<br />Recalcul des rangs impossible pour cette classe.";
	}
}

// 20160709
$tab_type_grp=get_tab_types_groupe();

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
}// =================================

$priority_defaut = 5;

//================================
// Liste de domaines de visibilité des groupes déplacé dans global.inc.php
//================================
$invisibilite_groupe=array();
for($loop=0;$loop<count($tab_domaines);$loop++) {
	$invisibilite_groupe[$tab_domaines[$loop]]=array();
}
$sql="SELECT jgv.* FROM j_groupes_classes jgc, j_groupes_visibilite jgv WHERE jgv.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe' AND jgv.visible='n';";
$res_jgv=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_jgv)>0) {
	while($lig_jgv=mysqli_fetch_object($res_jgv)) {
		$invisibilite_groupe[$lig_jgv->domaine][]=$lig_jgv->id_groupe;
	}
}
//================================

if (isset($_GET['ajouter_suffixes_noms_groupes'])) {
	check_token();

	$mode=isset($_GET['mode']) ? $_GET['mode'] : NULL;
	if(!isset($mode)) {
		$msg.="Mode de renommage invalide.<br />";
	}
	else {
		$groups = get_groups_for_class($id_classe,"","n");

		$alphabet=array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		$nb_renommages=0;

		$tab_grp_id_rech_homonyme=array();
		$tab_grp_descr_homonyme=array();
		$tab_grp_name=array();

		// Récup de la liste des élèves de la classe toutes périodes confondues
		$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe';";
		$res_eff_classe=mysqli_query($GLOBALS["mysqli"], $sql);
		$eff_classe=mysqli_num_rows($res_eff_classe);

		foreach ($groups as $group) {
			if(isset($tab_grp_id_rech_homonyme[$group["description"]])) {
				$tab_grp_descr_homonyme[]=$group["description"];
			}
			$tab_grp_id_rech_homonyme[$group["description"]][]=$group["id"];
			$tab_grp_name[$group["id"]]=$group["name"];
		}

		for($i=0;$i<count($tab_grp_descr_homonyme);$i++) {
			// Y a-t-il aussi des homonymes sur les noms (courts) de ces groupes
			$tab_grp_name_distincts=array();
			for($j=0;$j<count($tab_grp_id_rech_homonyme[$tab_grp_descr_homonyme[$i]]);$j++) {
				if(!in_array($tab_grp_name[$tab_grp_id_rech_homonyme[$tab_grp_descr_homonyme[$i]][$j]], $tab_grp_name_distincts)) {
					$tab_grp_name_distincts[]=$tab_grp_name[$tab_grp_id_rech_homonyme[$tab_grp_descr_homonyme[$i]][$j]];
				}
			}

			$corriger_noms="y";
			if(count($tab_grp_name_distincts)==count($tab_grp_id_rech_homonyme[$tab_grp_descr_homonyme[$i]])) {
				$corriger_noms="n";
			}

			// Ne pas renommer le groupe de plus grand effectif si cela correspond à l'effectif de la classe...
			$max_eff=-1;
			$id_grp_max_eff=-1;
			$tab_eff=array();
			$tab_eff_grp=array();
			for($j=0;$j<count($tab_grp_id_rech_homonyme[$tab_grp_descr_homonyme[$i]]);$j++) {
				$id_groupe_courant=$tab_grp_id_rech_homonyme[$tab_grp_descr_homonyme[$i]][$j];
				$sql="SELECT DISTINCT login FROM j_eleves_groupes WHERE id_groupe='".$id_groupe_courant."';";
				$res_eff=mysqli_query($GLOBALS["mysqli"], $sql);

				$eff=mysqli_num_rows($res_eff);
				if(!in_array($eff, $tab_eff)) {$tab_eff[]=$eff;}
				if($eff>$max_eff) {
					$max_eff=$eff;
					$id_grp_max_eff=$id_groupe_courant;
				}
				$tab_eff_grp[$id_groupe_courant]=$eff;
			}

			for($j=0;$j<count($tab_grp_id_rech_homonyme[$tab_grp_descr_homonyme[$i]]);$j++) {
				$id_groupe_courant=$tab_grp_id_rech_homonyme[$tab_grp_descr_homonyme[$i]][$j];

				$suffixe="";
				if((count($tab_eff)==1)||
				($id_groupe_courant!=$id_grp_max_eff)||
				($tab_eff_grp[$id_groupe_courant]!=$eff_classe)) {
					if($mode=='alpha') {
						if(isset($alphabet[$j])) {
							$suffixe=$alphabet[$j];
						}
						else {
							$suffixe=$j+1;
						}
					}
					elseif($mode=='num') {
						$suffixe=$j+1;
					}
					else {
						// Renommage d'après le numéro du groupe
						$suffixe=$id_groupe_courant;
					}
					$suffixe="_".$suffixe;
				}

				$sql="UPDATE groupes SET description='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_grp_descr_homonyme[$i].$suffixe)."'";
				if($corriger_noms=="y") {
					$nom_groupe_courant=$tab_grp_name[$id_groupe_courant];
					$sql.=", name= '".mysqli_real_escape_string($GLOBALS["mysqli"], $nom_groupe_courant.$suffixe)."'";
				}
				$sql.=" WHERE id='".$id_groupe_courant."';";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$update) {
					$msg.="Erreur lors du renommage du groupe n°".$id_groupe_courant."<br />\n";
				}
				else {
					$nb_renommages++;
				}
			}
		}

		if($nb_renommages>0) {
			$msg.="$nb_renommages renommage(s) effectué(s).<br />\n";
		}

		unset($tab_grp_id_rech_homonyme);
		unset($tab_grp_descr_homonyme);
	}
}

if (isset($_POST['is_posted'])) {
	check_token();

    $error = false;

	$msg="";

	$tab_id_groupe=array();

    foreach ($_POST as $key => $value) {
        $pattern = "/^priorite\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
			$tab_id_groupe[]=$group_id;
            $options[$group_id]["priorite"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^coef\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["coef"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        //$pattern = "/^note\_sup\_10\_/";
        $pattern = "/^mode\_moy\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            //$options[$group_id]["mode_moy"] = "sup10";
            $options[$group_id]["mode_moy"] = $value;
			//echo "mode_moy pour $group_id : $value<br />";
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^no_saisie_ects\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["saisie_ects"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^saisie_ects\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["saisie_ects"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^valeur_ects\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["valeur_ects"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^categorie\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["categorie_id"] = $value;
        }
    }

    foreach ($options as $key => $value) {
        // Toutes les vérifications de sécurité sont faites dans la fonction
        $update = update_group_class_options($key, $id_classe, $value);
    }

	$type_grp=array();
	foreach ($_POST as $key => $value) {
		$pattern = "/^type\_/";
		if (preg_match($pattern, $key)) {
			$group_id = preg_replace($pattern, "", $key);
			$type_grp[$group_id] = $value;

			$sql="SELECT * FROM j_groupes_types WHERE id_groupe='".$group_id."';";
			$res_type_grp=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($res_type_grp)==0) {
				if($type_grp[$group_id]!="") {
					$sql="INSERT INTO j_groupes_types SET id_groupe='".$group_id."', id_type='".$value."';";
					$insert=mysqli_query($GLOBALS['mysqli'], $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'enregistrement du type du groupe n°".$tab_id_groupe[$loop].".<br />";
					}
				}
			}
			else {
				$lig_type=mysqli_fetch_object($res_type_grp);
				if($type_grp[$group_id]=="") {
					$sql="DELETE FROM j_groupes_types WHERE id_groupe='".$group_id."';";
					$suppr=mysqli_query($GLOBALS['mysqli'], $sql);
					if(!$suppr) {
						$msg.="Erreur lors de la remise à vide du type du groupe n°".$tab_id_groupe[$loop].".<br />";
					}
				}
				elseif($type_grp[$group_id]!=$lig_type->id_type) {
					$sql="UPDATE j_groupes_types SET id_type='".$value."' WHERE id_groupe='".$group_id."';";
					$update=mysqli_query($GLOBALS['mysqli'], $sql);
					if(!$update) {
						$msg.="Erreur lors de la mise à jour du type du groupe n°".$tab_id_groupe[$loop].".<br />";
					}
				}
			}
		}
	}

	for($loo=0;$loo<count($tab_domaines);$loo++) {
		/*
		echo "<p>\$tab_domaines[$loo]=$tab_domaines[$loo]<br />";
		foreach($invisibilite_groupe[$tab_domaines[$loo]] as $key => $value) {
			echo "\$invisibilite_groupe[$tab_domaines[$loo]][$key]=$value<br />";
		}
		*/
		unset($visibilite_groupe_domaine_courant);
		$visibilite_groupe_domaine_courant=isset($_POST['visibilite_groupe_'.$tab_domaines[$loo]]) ? $_POST['visibilite_groupe_'.$tab_domaines[$loo]] : array();
		/*
		foreach($visibilite_groupe_domaine_courant as $key => $value) {
			echo "\$visibilite_groupe_domaine_courant[$key]=$value<br />";
		}
		*/
		for($loop=0;$loop<count($tab_id_groupe);$loop++) {
			//echo "\$tab_id_groupe[$loop]=$tab_id_groupe[$loop]<br />";
			if(in_array($tab_id_groupe[$loop], $invisibilite_groupe[$tab_domaines[$loo]])) {
				if(in_array($tab_id_groupe[$loop], $visibilite_groupe_domaine_courant)) {
					$sql="DELETE FROM j_groupes_visibilite WHERE id_groupe='".$tab_id_groupe[$loop]."' AND domaine='".$tab_domaines[$loo]."';";
					//echo "$sql<br />";
					$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$suppr) {$msg.="Erreur lors de la suppression de l'invisibilité du groupe n°".$tab_id_groupe[$loop]." sur les ".$tab_domaines_texte[$loo].".<br />";}
				}
			}
			else {
				if(!in_array($tab_id_groupe[$loop], $visibilite_groupe_domaine_courant)) {
					$sql="INSERT j_groupes_visibilite SET id_groupe='".$tab_id_groupe[$loop]."', domaine='".$tab_domaines[$loo]."', visible='n';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {$msg.="Erreur lors de l'enregistrement de l'invisibilité du groupe n°".$tab_id_groupe[$loop]." sur les ".$tab_domaines_texte[$loo].".<br />";}
				}
			}

		}
	}

	//================================
	$invisibilite_groupe=array();
	for($loop=0;$loop<count($tab_domaines);$loop++) {
		$invisibilite_groupe[$tab_domaines[$loop]]=array();
	}
	$sql="SELECT jgv.* FROM j_groupes_classes jgc, j_groupes_visibilite jgv WHERE jgv.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe' AND jgv.visible='n';";
	$res_jgv=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_jgv)>0) {
		while($lig_jgv=mysqli_fetch_object($res_jgv)) {
			$invisibilite_groupe[$lig_jgv->domaine][]=$lig_jgv->id_groupe;
		}
	}
	//================================

	if(isset($_POST["name_grp"])) {
		$name_grp=$_POST["name_grp"];
		$description_grp=$_POST["description_grp"];

		foreach($name_grp as $id_current_grp => $current_name) {
			$sql="UPDATE groupes SET name='".html_entity_decode($current_name,ENT_QUOTES,"UTF-8")."' WHERE id='".$id_current_grp."';";
			//echo "$sql<br />";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$update) {
				$msg.="Erreur lors de la modification du nom court du groupe n°$id_current_grp<br />";
			}
		}

		foreach($description_grp as $id_current_grp => $current_description) {
			$sql="UPDATE groupes SET description='".html_entity_decode($current_description,ENT_QUOTES,"UTF-8")."' WHERE id='".$id_current_grp."';";
			//echo "$sql<br />";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$update) {
				$msg.="Erreur lors de la modification de la description du groupe n°$id_current_grp<br />";
			}
		}
	}

	$msg="Enregistrement effectué <em>(".strftime("Le %A %d/%m/%Y à %H:%M:%S").")</em>.";

}

if (isset($_GET['action'])) {
	check_token();

    $msg = null;
    //if ($_GET['action'] == "delete_group") {
    if(($_GET['action'] == "delete_group")&&(isset($_GET['confirm_delete_group']))&&($_GET['confirm_delete_group'] == "y")) {
        if (!is_numeric($_GET['id_groupe'])) $_GET['id_groupe'] = 0;
        $verify = test_before_group_deletion($_GET['id_groupe']);
        if ($verify) {
            //================================
            // MODIF: boireaus
            $sql="SELECT * FROM groupes WHERE id='".$_GET['id_groupe']."'";
            $req_grp=mysqli_query($GLOBALS["mysqli"], $sql);
            $ligne_grp=mysqli_fetch_object($req_grp);
            //================================
            $delete = delete_group($_GET['id_groupe']);
            if ($delete == true) {
                //================================
                // MODIF: boireaus
                //$msg .= "Le groupe " . $_GET['id_groupe'] . " a été supprimé.";

                //$sql="SELECT * FROM groupes WHERE id='".$_GET['id_groupe']."'";
                //$req_grp=mysql_query($sql);
                //$ligne_grp=mysql_fetch_object($req_grp);
                // Le groupe n'existe déjà plus
                $msg .= "Le groupe $ligne_grp->name (" . $_GET['id_groupe'] . ") a été supprimé.";
                //================================
            } else {
                $msg .= "Une erreur a empêché la suppression du groupe.";
            }
        } else {
            $msg .= "Des données existantes bloquent la suppression du groupe. Aucune note ni appréciation du bulletin ne doit avoir été saisie pour les élèves de ce groupe pour permettre la suppression du groupe.";
        }
    }
}

$avec_js_et_css_edt="y";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
//$titre_page = "Gestion des groupes";
$titre_page = "Gestion des enseignements";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

//debug_var();

if((isset($_GET['action']))&&($_GET['action']=="delete_group")&&(!isset($_GET['confirm_delete_group']))) {
	check_token(false);

	// On va détailler ce qui serait supprimé en cas de confirmation
	$tmp_group=get_group($_GET['id_groupe'], array("classes"));
	echo "<div class='fieldset_opacite50' style='border: 2px solid red; margin:0.5em; padding:0.5em;'>\n";
	echo "<p><strong>ATTENTION&nbsp;:</strong> Vous souhaitez supprimer l'enseignement suivant&nbsp;: ".$tmp_group['name']." (<i>".$tmp_group['description']."</i>) en ".$tmp_group['classlist_string']."<br />\n";
	echo "Voici quelques éléments sur l'enseignement&nbsp;:</p>\n";
	$suppression_possible='y';

	$lien_bull_simp="";
	$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode DESC LIMIT 1;";
	//echo "$sql<br />";
	$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_per)>0) {
		$lig_per=mysqli_fetch_object($res_per);

		$lien_bull_simp="<a href='../prepa_conseil/edit_limite.php?choix_edit=1&amp;id_classe=$id_classe&amp;periode1=1&amp;periode2=$lig_per->num_periode' target='_blank'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='Bulletin simple dans une nouvelle page' title='Bulletin simple dans une nouvelle page' /></a>";
	}

	echo "<p style='margin-left:5em;'>";
	$sql="SELECT 1=1 FROM matieres_notes WHERE id_groupe='".$_GET['id_groupe']."';";
	$test_mn=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_mn=mysqli_num_rows($test_mn);
	if($nb_mn==0) {
		echo "Aucune note sur les bulletins.<br />\n";
	}
	else {
		echo "<span style='color:red;'>$nb_mn note(s) sur les bulletins</span> (<i>toutes périodes confondues</i>)&nbsp;: $lien_bull_simp<br />\n";
		$suppression_possible='n';
	}

	$sql="SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='".$_GET['id_groupe']."';";
	$test_ma=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_ma=mysqli_num_rows($test_ma);
	if($nb_ma==0) {
		echo "Aucune appréciation sur les bulletins.<br />\n";
	}
	else {
		echo "<span style='color:red;'>$nb_ma appréciation(s) sur les bulletins</span> (<i>toutes périodes confondues</i>)&nbsp;: $lien_bull_simp<br />\n";
		$suppression_possible='n';
	}

	$temoin_non_vide='n';
	// CDT
	$sql="SELECT 1=1 FROM ct_entry WHERE id_groupe='".$_GET['id_groupe']."';";
	$test_notice_cdt=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_notice_cdt=mysqli_num_rows($test_notice_cdt);
	if($nb_notice_cdt==0) {
		echo "Aucune notice dans le cahier de textes.<br />\n";
	}
	else {
		echo "$nb_notice_cdt notice(s) dans le cahier de textes.<br />\n";
		$temoin_non_vide='y';
	}

	$sql="SELECT 1=1 FROM ct_devoirs_entry WHERE id_groupe='".$_GET['id_groupe']."';";
	$test_devoir_cdt=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_devoir_cdt=mysqli_num_rows($test_devoir_cdt);
	if($nb_devoir_cdt==0) {
		echo "Aucun devoir dans le cahier de textes.<br />\n";
	}
	else {
		echo "$nb_devoir_cdt devoir(s) dans le cahier de textes.<br />\n";
		$temoin_non_vide='y';
	}

	// NOTES
	// Récupérer les cahier de notes
	$sql="SELECT DISTINCT id_cahier_notes, periode FROM cn_cahier_notes WHERE id_groupe='".$_GET['id_groupe']."' ORDER BY periode;";
	$res_ccn=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ccn)==0) {
		echo "Aucun cahier de notes n'est initialisé pour cet enseignement.<br />\n";
	}
	else {
		while($lig_id_cn=mysqli_fetch_object($res_ccn)) {
			$sql="SELECT 1=1 FROM cn_devoirs WHERE id_racine='$lig_id_cn->id_cahier_notes';";
			$res_dev=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_dev=mysqli_num_rows($res_dev);
			if($nb_dev==0) {
				echo "Période $lig_id_cn->periode&nbsp;: Aucun devoir.<br />\n";
			}
			else {
				echo "Période $lig_id_cn->periode&nbsp;: $nb_dev devoir(s) dans le carnet de notes.<br />\n";
				$temoin_non_vide='y';
			}
		}
	}
	echo "</p>\n";

	if($suppression_possible=='y') {
		if($temoin_non_vide=='y') {
			echo "<p>Si vous souhaitez effectuer ";
			echo "malgré tout ";
			echo "la suppression de l'enseignement&nbsp;: ";
			echo "<a href='edit_class.php?id_groupe=".$_GET['id_groupe']."&amp;action=delete_group&amp;confirm_delete_group=y&amp;id_classe=$id_classe".add_token_in_url()."' onclick=\"return confirmlink(this, 'ATTENTION !!! L\'enseignement n\'est pas totalement vide, même si les bulletins ne contiennent pas de référence à cet enseignement.\\nEtes-vous *VRAIMENT SÛR* de vouloir continuer ?', 'Confirmation de la suppression')\">Supprimer</a>";
			echo "</p>\n";
		}
		else {
			echo "<p>Si vous souhaitez confirmer la suppression de l'enseignement&nbsp;: ";
			echo "<a href='edit_class.php?id_groupe=".$_GET['id_groupe']."&amp;action=delete_group&amp;confirm_delete_group=y&amp;id_classe=$id_classe".add_token_in_url()."'>Supprimer</a>";
			echo "</p>\n";
		}
	}
	else {
		echo "<p style='color:red;'>Des données existantes bloquent la suppression du groupe.<br />Aucune note ni appréciation du bulletin ne doit avoir été saisie pour les élèves de ce groupe pour permettre la suppression du groupe.</p>\n";
	}
	echo "</div>\n";
}


$display_mat_cat="n";
$sql="SELECT display_mat_cat FROM classes WHERE id='$id_classe';";
$res_display_mat_cat=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_display_mat_cat)>0) {
	$lig_display_mat_cat=mysqli_fetch_object($res_display_mat_cat);
	$display_mat_cat=$lig_display_mat_cat->display_mat_cat;

	$url_wiki="#";
	$sql="SELECT * FROM ref_wiki WHERE ref='enseignement_invisible';";
	$res_ref_wiki=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ref_wiki)>0) {
		$lig_wiki=mysqli_fetch_object($res_ref_wiki);
		$url_wiki=$lig_wiki->url;
	}
	$titre="Enseignement invisible";
	$texte="<p>Cet enseignement n'apparaîtra pas sur les bulletins ni sur les relevés de notes.<br />";
	$texte.="Voir <a href='$url_wiki' target='_blank'>Enseignement invisible sur les bulletins et relevés de notes</a>.<br />";
	$tabdiv_infobulle[]=creer_div_infobulle('enseignement_invisible',$titre,"",$texte,"",25,0,'y','y','n','n');
}
else {
	echo "<p style='color:red;'>Anomalie&nbsp;: Les infos concernant 'display_mat_cat' n'ont pas pu être récupérées pour cette classe.</p>\n";
}

echo "<table border='0' summary='Menu'><tr>\n";
echo "<td width='40%' align='left'>\n";
echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
echo "<p class='bold'>\n";
echo "<a href='../classes/index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
//if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe précédente</a>";}
if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Classe précédente\"><img src='../images/arrow_left.png' class='icone16' alt='Précédente' /></a>";}
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


	echo " <select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
	echo $chaine_options_classes;
	echo "</select>\n";
}
//if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suivante</a>";}
if($id_class_suiv!=0){echo " <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Classe suivante\"><img src='../images/arrow_right.png' class='icone16' alt='Suivante' /></a>";}

//=========================
// AJOUT: boireaus 20081224
$titre="Navigation";
$texte="";
$texte.="<img src='../images/icons/date.png' alt='' /> <a href='../classes/periodes.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Périodes</a><br />";
if($nb_periode>1) {
	// On a $nb_periode = Nombre de périodes + 1
	$texte.="<img src='../images/icons/edit_user.png' alt='' /> <a href='../classes/classes_const.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Élèves</a><br />";
}
//$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignements</a><br />";
$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class_grp_lot.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">config.simplifiée</a><br />";
$texte.="<img src='../images/icons/configure.png' alt='' /> <a href='../classes/modify_nom_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Paramètres</a>";

$ouvrir_infobulle_nav=getSettingValue("ouvrir_infobulle_nav");
//echo "\$ouvrir_infobulle_nav=$ouvrir_infobulle_nav<br />";

if($ouvrir_infobulle_nav=="y") {
	$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;'><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/vert.png' width='16' height='16' alt='Oui' /></a></div>\n";
}
else {
	$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;'><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/rouge.png' width='16' height='16' alt='Non' /></a></div>\n";
}

$texte.="<script type='text/javascript'>
	// <![CDATA[
	function modif_mode_infobulle_nav() {
		new Ajax.Updater($('save_mode_nav'),'../classes/classes_ajax_lib.php?mode=ouvrir_infobulle_nav',{method: 'get'});
	}
	//]]>
</script>\n";

$tabdiv_infobulle[]=creer_div_infobulle('navigation_classe',$titre,"",$texte,"",14,0,'y','y','n','n');

echo " | <a href='#' onclick=\"afficher_div('navigation_classe','y',-100,20);\"";
echo ">";
echo "Navigation";
echo "</a>";
//=========================

echo " | <a href='menage_eleves_groupes.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Désinscriptions par lots</a>";

echo " | <a href='../groupes/repartition_ele_grp.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Répartir des élèves entre plusieurs groupes</a>";

echo " | <a href='../init_xml2/init_alternatif.php?cat=classes' onclick=\"return confirm_abandon (this, change, '$themessage')\">Création par lots</a>";

echo " | <a href='../classes/classes_param.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Paramétrage par lots</a>";

if(acces("/groupes/visu_profs_class.php", $_SESSION['statut'])) {
	echo " | <a href='../groupes/visu_profs_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Voir l'équipe pédagogique\">Équipe</a>";
}

if(acces("/groupes/modify_grp_group.php", $_SESSION['statut'])) {
	echo " | <a href='../groupes/modify_grp_group.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Groupes de groupes\">Groupes de groupes</a>";
}

echo "</p>\n";
echo "</form>\n";


echo "<h3>Gestion des enseignements pour la classe&nbsp;: " . $classe["classe"]."<span id='span_asterisque'></span>";
if(acces("/eleves/index.php", $_SESSION['statut'])) {
	echo " (<a href='../eleves/index.php?quelles_classes=certaines&amp;id_classe=".$id_classe."' title=\"Voir la liste des élèves de cette classe.\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Élèves</a>)";
}
echo "</h3>\n";

echo "</td>\n";
echo "<td width='60%' align='center'>\n";
echo "<form enctype='multipart/form-data' action='add_group.php' name='new_group' method='get'>\n";
//==============================
// MODIF: boireaus
//echo "<p>Ajouter un enseignement : ";
//$query = mysql_query("SELECT matiere, nom_complet FROM matieres");
echo "<fieldset style=\"padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">\n";
echo "<table border='0' summary='Ajout d enseignement'>\n<tr valign='top'>\n<td>\n";
echo "Ajouter un enseignement&nbsp;: ";
echo "</td>\n";
$query = mysqli_query($GLOBALS["mysqli"], "SELECT matiere, nom_complet FROM matieres ORDER BY matiere");
//==============================
$nb_mat = mysqli_num_rows($query);

echo "<td>\n";
echo "<select name='matiere' size='1'>\n";
echo "<option value='null'>-- Sélectionner une matière --</option>\n";
for ($i=0;$i<$nb_mat;$i++) {
    $matiere = old_mysql_result($query, $i, "matiere");
    $nom_matiere = old_mysql_result($query, $i, "nom_complet");

    $sql="SELECT u.nom, u.prenom FROM utilisateurs u, j_professeurs_matieres jpm WHERE jpm.id_professeur=u.login AND jpm.id_matiere='".$matiere."' ORDER BY u.nom, u.prenom;";
    $res_profs_matiere=mysqli_query($GLOBALS["mysqli"], $sql);
    if(mysqli_num_rows($res_profs_matiere)==0) {
        $style_opt=" style='color:grey;'";
        $texte_opt=" - Aucun professeur n'est associé à cette matière.";
    }
    else {
        $style_opt="";
        $texte_opt=" - Professeurs associés: ";
        $cpt_prof_opt=0;
        while($lig_prof_opt=mysqli_fetch_object($res_profs_matiere)) {
            if($cpt_prof_opt>0) {$texte_opt.=", ";}
            $texte_opt.=casse_mot($lig_prof_opt->prenom, 'majf2')." ".casse_mot($lig_prof_opt->nom, 'maj');
            $cpt_prof_opt++;
        }
    }

    //echo "<option value='" . $matiere . "'";
    echo "<option value='" . $matiere . "'";
    echo " title=\"$matiere ($nom_matiere)$texte_opt\"";
    echo $style_opt;
    //echo ">" . htmlspecialchars($nom_matiere) . "</option>\n";
    echo ">" . htmlspecialchars($nom_matiere,ENT_QUOTES,"UTF-8") . "</option>\n";
}
echo "</select>\n";

echo "<a href='../matieres/modify_matiere.php' title=\"Créer d'abord une matière\"><img src='../images/icons/wizard.png' class='icone16' alt=\"Créer d'abord une matière\" /></a>\n";

echo "</td>\n";
echo "<td>\n";
echo "&nbsp;dans&nbsp;";
echo "</td>\n";
//==============================
// MODIF: boireaus
/*
echo "<select name='mode' size='1'>";
echo "<option value='null'>-- Sélectionner mode --</option>";
echo "<option value='groupe' selected>cette classe seulement (" . $classe["classe"] .")</option>";
echo "<option value='regroupement'>plusieurs classes</option>";
echo "</select>";
*/
echo "<td>\n";
echo "<input type='radio' name='mode' id='mode_groupe' value='groupe' checked /><label for='mode_groupe' style='cursor: pointer;'> cette classe seulement (" . $classe["classe"] .")</label><br />\n";
echo "<input type='radio' name='mode' id='mode_regroupement' value='regroupement' /><label for='mode_regroupement' style='cursor: pointer;'> plusieurs classes</label>\n";
echo "</td>\n";
echo "</tr>\n</table>\n";
//==============================

echo "<input type='hidden' name='id_classe' value='" . $id_classe . "' />\n";

echo "<div style='float:right; width:15px;'><a href='../classes/classes_param.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' title=\"Si vous devez créer plusieurs enseignements d'une même matière, vous gagnerez sans doute du temps à effectuer la création via un Paramétrage par lots\" /></a></div>";

echo "<input type='submit' value='Créer' />\n";
echo "</fieldset>\n";
echo "</form>\n";
echo "</td>\n</tr>\n</table>\n";

//$groups = get_groups_for_class($id_classe);

$sql="SELECT 1=1 FROM j_groupes_classes jgc WHERE jgc.id_classe='$id_classe' AND categorie_id NOT IN (SELECT id FROM matieres_categories);";
$test_cat_auc=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test_cat_auc)==0) {
	$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
	if($affiche_categories=='y') {
		$groups = get_groups_for_class($id_classe,"","y");
	}
	else {
		$groups = get_groups_for_class($id_classe,"","n");
	}
}
else {
	if($affiche_categories=='y') {
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Afficher tous les enseignements.</a>";
		$groups = get_groups_for_class($id_classe,"","y");
	}
	else {
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;affiche_categories=y'>N'afficher que les enseignements inscrits dans une vraie catégorie (<em>autre que 'Aucune'</em>).</a>";
		$groups = get_groups_for_class($id_classe,"","n");
	}
}


if(count($groups)==0){

	if($ouvrir_infobulle_nav=='y') {
		echo "<script type='text/javascript'>
		setTimeout(\"afficher_div('navigation_classe','y',-100,20);\",1000)
	</script>\n";
	}
	
	require("../lib/footer.inc.php");

    //echo "</body></html>\n";
    die();
}

$gepi_prof_suivi=getParamClasse($id_classe, 'gepi_prof_suivi', getSettingValue('gepi_prof_suivi'));

// Pour repérer des groupes homonymes
$msg_groupes_homonymes="";
$tab_id_groupe_homonyme=array();
$tab_description_groupe_homonyme=array();

?>
<form enctype="multipart/form-data" action="edit_class.php" name="formulaire" method="post">
<?php
echo add_token_field();
?>
<!--form enctype="multipart/form-data" action="edit_class.php" name="formulaire" id="form_mat" method=post-->

<!--p>Définir les priorités d'après <input type='button' value="l'ordre alphabétique" onClick="ordre_alpha();" /> / <input type='button' value="l'ordre par défaut des matières" onClick="ordre_defaut();" /><br /-->
<!--table border='0' width='100%'><tr align='center'><td width='30%'>&nbsp;</td><td width='30%'>Afficher les matières dans l'ordre <a href='javascript:ordre_alpha();'>alphabétique</a> ou <a href='javascript:ordre_defaut();'>des priorités</a>.</td>
<td width='30%'>Mettre tous les coefficients à <select name='coefficient_recop' id='coefficient_recopie'-->
<!--table border='0' width='100%'><tr align='center'><td>Afficher les matières dans l'ordre <a href='javascript:ordre_alpha();'>alphabétique</a> ou <a href='javascript:ordre_defaut();'>des priorités</a>.</td-->

<table border='0' width='100%' summary='Paramètres'>
<tr align='center'>
<td width='40%'>
<fieldset style="padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;">
<p>Pour cette classe,
<input type='button' value="régler les priorités d'affichage" onClick='choix_ordre();' title="N'oubliez pas d'enregistrer ensuite." />:</p>
<!--ul>
<li><a href='javascript:ordre_defaut();'>égales aux valeurs définies par défaut</a>,</li>
<li><a href='javascript:ordre_alpha();'>suivant l'ordre alphabétique des matières.</a></li>
</ul-->
<input type='radio' name='ordre' id='ordre_defaut' value='ordre_defaut' /><label for='ordre_defaut' style='cursor: pointer;' title="Les valeurs par défaut sont définies dans Gestion des bases/Gestion des matières"> égales aux valeurs définies par défaut,</label><br />
<input type='radio' name='ordre' id='ordre_alpha' value='ordre_alpha' /><label for='ordre_alpha' style='cursor: pointer;'> suivant l'ordre alphabétique des matières</label>
</fieldset>
</td>

<td><input type='submit' value='Enregistrer' /></td>

<td width='40%'>
<?php


$call_nom_class = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes WHERE id = '$id_classe'");
$display_rang = old_mysql_result($call_nom_class, 0, 'display_rang');
if($display_rang=='y') {
	$titre="Recalcul des rangs";
	$texte="<p>Un utilisateur a rencontré un jour le problème suivant&nbsp;:<br />Le rang était calculé pour les enseignements, mais pas pour le rang général de l'élève.<br />Ce lien permet de forcer le recalcul des rangs pour les enseignements comme pour le rang général.<br />Le recalcul sera effectué lors du prochain affichage de bulletin ou de moyennes.</p>";
	$tabdiv_infobulle[]=creer_div_infobulle('recalcul_rang',$titre,"",$texte,"",25,0,'y','y','n','n');
	
	echo "<fieldset style='padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;'>\n";
	echo "<p>Pour cette classe, <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;forcer_recalcul_rang=y' onclick=\"return confirm_abandon (this, change, '$themessage')\">forcer le recalcul des rangs</a> ";
	
	echo "<a href='#' onclick=\"afficher_div('recalcul_rang','y',-100,20);return false;\"";
	echo ">";
	echo "<img src='../images/icons/ico_ampoule.png' width='15' height='25' alt='Forcer le recalcul des rangs' title='Forcer le recalcul des rangs' />\n";
	echo "</a>";
	
	echo ".</p>\n";
	echo "</fieldset>\n";
}
?>

<br />

<fieldset style="padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;">
<!--a href='javascript:coeff();'>Mettre tous les coefficients à</a-->
<input type='button' value='Mettre tous les coefficients à' onClick='coeff(); changement();' title="N'oubliez pas d'enregistrer ensuite." />
<select name='coefficient_recop' id='coefficient_recopie' >
<?php
for($i=0;$i<30;$i++){
    echo "<option value='$i'>$i</option>\n";
}
?>
</select>
<?php
	$temoin_tous_coef_nuls="y";
	foreach ($groups as $group) {
		$current_group = get_group($group["id"], array("classes"));
		if($current_group["classes"]["classes"][$id_classe]['coef']>0) {
			$temoin_tous_coef_nuls="n";
			break;
		}
	}

	if($temoin_tous_coef_nuls=="y") {
		echo "&nbsp;<img src='../images/icons/ico_attention.png' width='22' height='19' title=\"ATTENTION : Tous les coefficients des enseignements sont nuls.
                    Aucun calcul de moyenne générale n'est alors possible.\" />\n";
	}
?>
<!--input type='button' value='Modifier' onClick='coeff();' /-->
<!--Mettre tous les coefficients à <input type='button' value='0' onClick='coeff(0);' /> / <input type='button' value='1' onClick='coeff(1);' /-->
<!--/p-->
</fieldset>
</td></tr></table>
<!--p><i>Pour les enseignements impliquant plusieurs classes, le coefficient s'applique à tous les élèves de la classe courante et peut être réglé indépendamment d'une classe à l'autre (pour le régler individuellement par élève, voir la liste des élèves inscrits).</i-->
<?php
    // si le module ECTS est activé, on calcul la valeur total d'ECTS attribués aux groupes
    if ($gepiSettings['active_mod_ects'] == "y") {
        $total_ects = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT sum(valeur_ects) FROM j_groupes_classes WHERE (id_classe = '".$id_classe."' and saisie_ects = TRUE)"), 0);
        echo "<p style='margin-top: 10px;'>Nombre total d'ECTS actuellement attribués pour cette classe : ".intval($total_ects)."</p>\n";
        if ($total_ects < 30) {
            echo "<p style='color: red;'>Attention, le total d'ECTS pour un semestre devrait être au moins égal à 30.</p>\n";
        }
    }

	// Mettre un témoin pour repérer le prof principal
	$tab_prof_suivi=get_tab_prof_suivi($id_classe);
	$nb_prof_suivi=count($tab_prof_suivi);
	if($nb_prof_suivi>1) {
		$liste_prof_suivi="";
		for($loop=0;$loop<count($tab_prof_suivi);$loop++) {
			if($loop>0) {$liste_prof_suivi.=", ";}
			$liste_prof_suivi.=civ_nom_prenom($tab_prof_suivi[$loop]);
		}
	}

	$cpt_grp=0;
	$res = mysqli_query($GLOBALS["mysqli"], "SELECT id, nom_court, nom_complet, priority FROM matieres_categories");
	$mat_categories = array();
	$nom_categories = array();
	while ($row = mysqli_fetch_object($res)) {
		$mat_categories[] = $row;
		$nom_categories[$row->id]=$row->nom_complet;
	}

	//============================================================================================================
	// Div pour l'affichage de l'EDT

	if((getSettingAOui('autorise_edt_tous'))||
		((getSettingAOui('autorise_edt_admin'))&&($_SESSION['statut']=='administrateur'))) {

		$titre_infobulle="EDT de <span id='id_ligne_titre_infobulle_edt'></span>";
		$texte_infobulle="";
		$tabdiv_infobulle[]=creer_div_infobulle('edt_prof',$titre_infobulle,"",$texte_infobulle,"",40,0,'y','y','n','n');

//https://127.0.0.1/steph/gepi_git_trunk/edt_organisation/index_edt.php?login_edt=boireaus&type_edt_2=prof&no_entete=y&no_menu=y&lien_refermer=y

		function affiche_lien_edt_prof($login_prof, $info_prof) {
			return " <a href='../edt_organisation/index_edt.php?login_edt=".$login_prof."&amp;type_edt_2=prof&amp;no_entete=y&amp;no_menu=y&amp;lien_refermer=y' onclick=\"affiche_edt_en_infobulle('$login_prof', '".addslashes($info_prof)."');return false;\" title=\"Emploi du temps de ".$info_prof."\" target='_blank'><img src='../images/icons/edt.png' class='icone16' alt='EDT' /></a>";
		}

		echo "
<style type='text/css'>
	.lecorps {
		margin-left:0px;
	}
</style>

<script type='text/javascript'>
	function affiche_edt_en_infobulle(login_prof, info_prof) {
		document.getElementById('id_ligne_titre_infobulle_edt').innerHTML=info_prof;

		new Ajax.Updater($('edt_prof_contenu_corps'),'../edt_organisation/index_edt.php?login_edt='+login_prof+'&type_edt_2=prof&no_entete=y&no_menu=y&mode_infobulle=y',{method: 'get'});
		afficher_div('edt_prof','y',-20,20);
	}
</script>\n";
	}
	else {
		function affiche_lien_edt_prof($login_prof, $info_prof) {
			return "";
		}
	}

	//============================================================================================================
	function affiche_lien_mailto_prof($mail_prof, $info_prof) {
		$retour=" <a href='mailto:".$mail_prof."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI&amp;body=";
		$tmp_date=getdate();
		if($tmp_date['hours']>=18) {$retour.="Bonsoir";} else {$retour.="Bonjour";}
		$retour.=",%0d%0aCordialement.' title=\"Envoyer un mail à $info_prof\">";
		$retour.="<img src='../images/icons/mail.png' class='icone16' alt='mail' />";
		$retour.="</a>";
		return $retour;
	}
	//============================================================================================================

	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// Début du tableau des enseignements existants dans la classe
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	$afficher_champs_modif_nom_groupe=isset($_POST['afficher_champs_modif_nom_groupe']) ? $_POST['afficher_champs_modif_nom_groupe'] : (isset($_GET['afficher_champs_modif_nom_groupe']) ? $_GET['afficher_champs_modif_nom_groupe'] : "n");

	echo "<table class='boireaus' summary='Tableau des enseignements'>\n";
	echo "<tr>\n";
	echo "<th rowspan='2'>Supprimer</th>\n";
	echo "<th rowspan='2'>Enseignement<br />\n";
	// 20130412
	if($afficher_champs_modif_nom_groupe=="y") {
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;afficher_champs_modif_nom_groupe=n' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Ne pas afficher les champs de modification des noms de groupes\"><img src='../images/icons/visible.png' width='19' height='16' /></a>";
	}
	else {
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;afficher_champs_modif_nom_groupe=y' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Afficher les champs de modification des noms de groupes\"><img src='../images/icons/invisible.png' width='19' height='16' /></a>";
	}
	echo "</th>\n";
	echo "<th colspan='".($nb_periode-1)."'><img src='../images/icons/edit_user.png' alt=''/> Elèves inscrits</th>\n";
	echo "<th rowspan='2'>Priorité<br />d'affichage</th>\n";
	echo "<th rowspan='2'>Catégorie</th>\n";
	if(!getSettingANon('AutoriserTypesEnseignements')) {
		echo "<th rowspan='2'>Type</th>\n";
	}
	echo "<th colspan='".count($tab_domaines)."'>Visibilité</th>\n";
	echo "<th rowspan='2'>Coefficient</th>\n";
	echo "<th colspan='3'>Mode moy</th>\n";
	$nb_total_col=9+$nb_periode-1+count($tab_domaines);

	if ($gepiSettings['active_mod_ects'] == "y") {
		echo "<th rowspan='2'>Activer la saisie ECTS</th>\n";
		echo "<th rowspan='2'>\n";
		echo "Nombre d'ECTS par défaut pour une période";
		echo "</th>\n";
		$nb_total_col+=2;
	}
	echo "</tr>\n";

	echo "<tr>\n";
	//echo "<th>Supprimer</th>\n";
	//echo "<th>Enseignement</th>\n";
	for($i=1;$i<$nb_periode;$i++) {
		echo "<th>P$i</th>\n";
	}
	//echo "<th>Priorité d'affichage</th>\n";
	//echo "<th>Catégorie</th>\n";
	for($i=0;$i<count($tab_domaines);$i++) {
		echo "<th>".$tab_domaines_sigle[$i]."</th>\n";
	}
	//echo "<th>Coefficient</th>\n";
	echo "<th title='La note compte normalement dans la moyenne.'>La note<br />compte</th>\n";
	echo "<th title='Les points au-dessus de 10 coefficientés sont ajoutés sans augmenter le total des coefficients.'>Bonus</th>\n";
	echo "<th title='La note ne compte que si elle est supérieure ou égale à 10'>Sup10</th>\n";
	echo "</tr>\n";

	$prec_cat="";

	$tab_visib_dom=array();

	$alt=1;
	foreach ($groups as $group) {

		// On récupère le groupe avec toutes les infos sauf les modalités
		$current_group = get_group($group["id"], array('matieres', 'classes', 'eleves', 'periodes', 'profs', 'visibilite'));
		$total = count($group["classes"]);

		if(in_array($current_group['description'], $tab_description_groupe_homonyme)) {
			$msg_ajout="Plusieurs groupes portent la description <strong>".$current_group['description']."</strong>";
			if(!preg_match("#$msg_ajout#", $msg_groupes_homonymes)) {
				$msg_groupes_homonymes.=$msg_ajout."<br />\n";
			}
		}

		$tab_id_groupe_homonyme[]=$current_group['id'];
		$tab_description_groupe_homonyme[]=$current_group['description'];

		//===============================
		unset($result_matiere);
		// On récupère l'ordre par défaut des matières dans matieres pour permettre de fixer les priorités d'après les priorités par défaut de matières.
		// Sinon, pour l'affichage, c'est la priorité dans j_groupes_classes qui est utilisée à l'affichage dans les champs select.
		$sql="SELECT m.priority, m.categorie_id FROM matieres m, j_groupes_matieres jgc WHERE jgc.id_groupe='".$group["id"]."' AND m.matiere=jgc.id_matiere";
		//echo "$sql<br />\n";
		$result_matiere=mysqli_query($GLOBALS["mysqli"], $sql);
		$ligmat=mysqli_fetch_object($result_matiere);
		$mat_priorite[$cpt_grp]=$ligmat->priority;
		$mat_cat_id[$cpt_grp]=$ligmat->categorie_id;
		//===============================


		if ($affiche_categories == "y") {
			if($current_group["classes"]["classes"][$id_classe]["categorie_id"]!=$prec_cat) {
				echo "<tr><td colspan='$nb_total_col' style='background-color:silver;'>";
				if(isset($nom_categories[$current_group["classes"]["classes"][$id_classe]["categorie_id"]])) {
					echo $nom_categories[$current_group["classes"]["classes"][$id_classe]["categorie_id"]];
				}
				else {
					echo "&nbsp;";
				}
				echo "</td></tr>\n";
			}
		}
		$prec_cat=$current_group["classes"]["classes"][$id_classe]["categorie_id"];

		$alt=$alt*(-1);
		echo "<tr id='tr_enseignement_$cpt_grp' class='lig$alt white_hover'>\n";
		// Suppression
		echo "<td>";
		echo "<a name='ancre_enseignement_".$group["id"]."'></a>";
		echo "<a href='edit_class.php?id_groupe=". $group["id"] . "&amp;action=delete_group&amp;id_classe=$id_classe".add_token_in_url()."' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Supprimer cet enseignement\"><img src='../images/icons/delete.png' alt='Supprimer' style='width:13px; heigth: 13px;' /></a>";
		echo "</td>\n";

		// Enseignement
		echo "<td class='norme' style='text-align:left;'>";

		// Faut-il juste mettre en évidence les anomalies ou aussi donner le regroupement associé?
		$sql="SELECT * FROM edt_corresp2 WHERE id_groupe='".$group["id"]."';";
		$test_regroup_edt=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test_regroup_edt)>1) {
			echo "<div style='float:right; width:16px; margin-left:5px;'><img src='../images/icons/flag2.gif' class='icone16' alt='Anomalie' title=\"ANOMALIE : Le groupe est associé à plus d'un regroupement EDT.\nVous devriez éditer le groupe et corriger.\" /></div>";
		}

		echo "<strong>";
		if ($total == "1") {
			echo "<a href='edit_group.php?id_groupe=". $group["id"] . "&amp;id_classe=" . $id_classe . "&amp;mode=groupe' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Modifier l'enseignement de ".$group['matiere']['matiere']."\">";
		} else {
			echo "<a href='edit_group.php?id_groupe=". $group["id"] . "&amp;id_classe=" . $id_classe . "&amp;mode=regroupement' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Modifier l'enseignement de ".$group['matiere']['matiere']."\">";
		}
		echo $group["description"] . "</a></strong>";
		echo "<input type='hidden' name='enseignement_".$cpt_grp."' id='enseignement_".$cpt_grp."' value=\"".$group["description"]."\" />\n";

		$j= 1;
		if ($total > 1) {
			echo "&nbsp;&nbsp;(<em>avec&nbsp;: ";
			unset($tabclasse);
			foreach ($group["classes"] as $classe) {
				if ($classe["id"] != $id_classe) {
					$tabclasse[]=$classe["classe"];
				}
				$j++;
			}
			echo $tabclasse[0];
			for($i=1;$i<count($tabclasse);$i++){
				echo ", $tabclasse[$i]";
			}
			echo "</em>)";
		}

		// 20130412
		if($afficher_champs_modif_nom_groupe=="y") {
			echo "<br />
			<input type='hidden' name='afficher_champs_modif_nom_groupe' value='y' />
			<table>
				<tr><td style='border:0px; text-align:left;'>Nom court&nbsp;:</td><td style='border:0px'><input type='text' name='name_grp[".$group["id"]."]' value=\"".$group["name"]."\" onchange='changement()' /></td></tr>
				<tr><td style='border:0px; text-align:left;'>Nom complet&nbsp;:</td><td style='border:0px'><input type='text' name='description_grp[".$group["id"]."]' value=\"".$group["description"]."\" onchange='changement()' /></td></tr>
			</table>\n";
		}
		else {
			echo "<input type='hidden' name='afficher_champs_modif_nom_groupe' value='n' />\n";
		}

		$first = true;
		foreach($current_group["profs"]["list"] as $prof) {
			if ($first) {echo "<br />";}
			if (!$first) {echo ", ";}
			echo "<a href='../utilisateurs/modify_user.php?user_login=".$current_group["profs"]["users"][$prof]["login"]."' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Modification de l'utilisateur\" style='color:black; font-size:small;'>";
			echo casse_mot($current_group["profs"]["users"][$prof]["prenom"],'majf2');
			echo " ";
			echo $current_group["profs"]["users"][$prof]["nom"];
			echo "</a>";
	
			if(in_array($current_group["profs"]["users"][$prof]["login"],$tab_prof_suivi)) {
				echo " <img src='../images/bulle_verte.png' width='9' height='9' title=\"".ucfirst($gepi_prof_suivi)." d'au moins un élève de la classe sur une des périodes.";
				if($nb_prof_suivi>1) {echo " La liste des ".$gepi_prof_suivi." est ".$liste_prof_suivi.".";}
				echo "\" />\n";
			}

			echo affiche_lien_edt_prof($current_group["profs"]["users"][$prof]["login"], $current_group["profs"]["users"][$prof]["prenom"]." ".$current_group["profs"]["users"][$prof]["nom"]);

			$mail_prof=get_mail_user($current_group["profs"]["users"][$prof]["login"]);
			if(check_mail($mail_prof)) {
				echo affiche_lien_mailto_prof($mail_prof, $current_group["profs"]["users"][$prof]["prenom"]." ".$current_group["profs"]["users"][$prof]["nom"]);
			}

			$first = false;
		}

		echo "</td>\n";

		// Inscription des élèves sur les différentes périodes
		foreach($current_group["periodes"] as $period) {
			if($period["num_periode"]!=""){
				$inscrits = count($current_group["eleves"][$period["num_periode"]]["list"]);

				echo "<td>";
				echo "<a href='edit_eleves.php?id_groupe=". $group["id"] . "&amp;id_classe=" . $id_classe . "' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Modifier la liste des élèves inscrits dans cet enseignement\">" . $inscrits . "</a>";
				echo "</td>\n";
			}
		}

		// Priorité d'affichage
		echo "<td>";
		echo "<select onchange=\"changement()\" size=1 id='priorite_".$cpt_grp."' name='priorite_" . $current_group["id"] . "'>\n";
		echo "<option value='0'";
		if  ($current_group["classes"]["classes"][$id_classe]["priorite"] == '0') echo " selected";
		echo ">0";
		if ($priority_defaut == 0) echo " (valeur par défaut)";
		echo "</option>\n";
		$k = 0;
	
		$k=11;
		$j = 1;
		while ($k < 110){
			echo "<option value=$k";
			if ($current_group["classes"]["classes"][$id_classe]["priorite"] == $k) {echo " selected";}
			echo ">".$j;
			if ($priority_defaut == $k) {echo " (valeur par défaut)";}
			echo "</option>\n";
			$k++;
			$j = $k - 10;
		}
		echo "</select>\n";
		echo "</td>\n";

		// Catégorie
		echo "<td>";
		echo "<select onchange=\"changement()\" size=1 id='categorie_".$cpt_grp."' name='categorie_" .$current_group["id"]. "'>\n";
		echo "<option value='0'";
		if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] == "0") {echo " SELECTED";}
		echo ">Aucune</option>\n";
		$tab_categorie_id=array();
		foreach ($mat_categories as $cat) {
			$tab_categorie_id[]=$cat->id;
			echo "<option value='".$cat->id . "'";
			if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] == $cat->id) {
			echo " selected";
			}
			//echo ">".html_entity_decode_all_version($cat->nom_court)."</option>\n";
			echo ">".htmlspecialchars($cat->nom_court)."</option>\n";
		}
		echo "</select>\n";

		if(($current_group["classes"]["classes"][$id_classe]["categorie_id"]!=0)&&(!in_array($current_group["classes"]["classes"][$id_classe]["categorie_id"],$tab_categorie_id))) {
			$temoin_anomalie_categorie="y";
			echo "<a href='#' onclick=\"afficher_div('association_anormale_enseignement_categorie','y',-100,20);return false;\"'><img src='../images/icons/flag2.gif' width='17' height='18' /></a>";
		}

		if(($display_mat_cat=='y')&&($current_group["classes"]["classes"][$id_classe]["categorie_id"]=="0")) {
			//echo "<br />\n";
			$message_categorie_aucune="La matière n apparaitra pas sur les bulletins et relevés de notes. Voir http://www.sylogix.org/projects/gepi/wiki/Enseignement_invisible";
			//echo "<img src='../images/icons/ico_attention.png' width='22' height='19' alt='$message_categorie_aucune' title='$message_categorie_aucune' />\n";

			echo "<a href='#' onclick=\"afficher_div('enseignement_invisible','y',-100,20);return false;\"";
			echo ">";
			echo "<img src='../images/icons/ico_attention.png' width='22' height='19' alt='$message_categorie_aucune' title='$message_categorie_aucune' />\n";
			echo "</a>";
		}
		echo "</td>\n";


		if(!getSettingANon('AutoriserTypesEnseignements')) {
			// Type (AP, EPI, Parcours)
			echo "<td>";
			echo "<select onchange=\"changement()\" size=1 id='type_".$cpt_grp."' name='type_" .$current_group["id"]. "'>\n";
			echo "<option value='' title=\"Groupe standard\"";
			if ((!isset($current_group["type_grp"]))||(count($current_group["type_grp"])=="0")) {echo " SELECTED";}
			echo ">---</option>\n";
			for($loop_grp_type=0;$loop_grp_type<count($tab_type_grp);$loop_grp_type++) {
				echo "<option value='".$tab_type_grp[$loop_grp_type]["id"] . "' title=\"".$tab_type_grp[$loop_grp_type]["nom_complet"] . "\"";
				if((isset($current_group["type_grp"][0]))&&($current_group["type_grp"][0]["id_type"]==$tab_type_grp[$loop_grp_type]["id"])) {
					echo " selected";
				}
				echo ">".$tab_type_grp[$loop_grp_type]["nom_court"]."</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";
		}

		// Visibilité
		for($loop=0;$loop<count($tab_domaines);$loop++) {
			if(!in_array($current_group["id"],$invisibilite_groupe[$tab_domaines[$loop]])) {
				echo "<td>";
				echo "<input type='checkbox' name='visibilite_groupe_".$tab_domaines[$loop]."[]' value='".$current_group["id"]."' checked title='Visibilité ".$tab_domaines[$loop]."' />\n";
				echo "</td>\n";
			}
			else {
				echo "<td style='background-color: grey;'>";
				echo "<input type='checkbox' name='visibilite_groupe_".$tab_domaines[$loop]."[]' value='".$current_group["id"]."' title='Visibilité ".$tab_domaines[$loop]."' />\n";

				$tab_visib_dom[$tab_domaines_sigle[$loop]][]=$cpt_grp;

				echo "</td>\n";
			}
		}

		// Coefficient
		echo "<td>";
		echo "<input type=\"text\" onchange=\"changement()\" id='coef_".$cpt_grp."' name='". "coef_" . $current_group["id"] . "' value='" . $current_group["classes"]["classes"][$id_classe]["coef"] . "' size=\"3\" onkeydown=\"clavier_2(this.id,event,0,30);changement();\" autocomplete=\"off\" />\n";
		echo "</td>\n";

		// Mode moy
		echo "<td>";
        echo "<input type='radio' name='mode_moy_".$current_group["id"]."' id='note_standard_".$current_group["id"]."' value='-' onchange=\"changement()\" ";
        if($current_group["classes"]["classes"][$id_classe]["mode_moy"]=="-") {echo "checked ";}
        echo "/>\n";
		echo "</td>\n";

		echo "<td>";
        echo "<input type='radio' name='mode_moy_".$current_group["id"]."' id='note_bonus_".$current_group["id"]."' value='bonus' onchange=\"changement()\" ";
        if($current_group["classes"]["classes"][$id_classe]["mode_moy"]=="bonus") {echo "checked ";}
        echo "/>\n";
		echo "</td>\n";

		echo "<td>";
        echo "<input type='radio' name='mode_moy_".$current_group["id"]."' id='note_sup_10_".$current_group["id"]."' value='sup10' onchange=\"changement()\" ";
        if($current_group["classes"]["classes"][$id_classe]["mode_moy"]=="sup10") {echo "checked ";}
        echo "/>\n";
		echo "</td>\n";

		// Ects
		if ($gepiSettings['active_mod_ects'] == "y") {
			echo "<td><input id='saisie_ects_".$cpt_grp."' type='checkbox' name='saisie_ects_".$current_group["id"]."' value='1'";
			if($current_group["classes"]["classes"][$id_classe]["saisie_ects"]) {
				echo " checked";
			}
			echo "/>\n";
			echo "<input id='no_saisie_ects_".$cpt_grp."' type='hidden' name='no_saisie_ects_".$current_group["id"]."' value='0' />\n";
			echo "</td>\n";

			echo "<td>\n";
			echo "<select onchange=\"changement()\" id='valeur_ects_".$cpt_grp."' name='". "valeur_ects_" . $current_group["id"] . "'>\n";
			for($c=0;$c<31;$c++) {
				echo "<option value='$c'";
				if (intval($current_group["classes"]["classes"][$id_classe]["valeur_ects"]) == $c) echo " SELECTED ";
				echo ">$c</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";
		}

		echo "</tr>\n";

		$cpt_grp++;
	}
	echo "</table>\n";

	if(count($tab_visib_dom)>0) {
		$chaine_visibilite_js="";
		for($loop=0;$loop<count($tab_domaines_sigle);$loop++) {
			if(isset($tab_visib_dom[$tab_domaines_sigle[$loop]])) {
				if($chaine_visibilite_js=='') {echo "<p>Griser les enseignements non visibles sur les ";}
				$chaine_visibilite_js.="var tab_vis_$loop=new Array(";
				for($loo=0;$loo<count($tab_visib_dom[$tab_domaines_sigle[$loop]]);$loo++) {
					if($loo>0) {$chaine_visibilite_js.=", ";}
					$chaine_visibilite_js.="'".$tab_visib_dom[$tab_domaines_sigle[$loop]][$loo]."'";
				}
				$chaine_visibilite_js.=");\n";
				echo "<input type='checkbox' name='chk_visibilite_griser_degriser[]' id='chk_visibilite_griser_degriser_$loop' value='y' onchange=\"visibilite_griser_degriser('$loop')\" /><label for='chk_visibilite_griser_degriser_$loop' onclick=\"visibilite_griser_degriser('$loop')\">".$tab_domaines[$loop]."</label>&nbsp;&nbsp;&nbsp;\n";
			}
		}

		echo "<script type='text/javascript'>
	$chaine_visibilite_js
	function visibilite_griser_degriser(num_domaine) {
		tab_vis_num_domaine=eval('tab_vis_'+num_domaine)
		for(i=0;i<tab_vis_num_domaine.length;i++) {
			tr_courant='tr_enseignement_'+tab_vis_num_domaine[i];
			if(document.getElementById(tr_courant)) {
				if(document.getElementById('chk_visibilite_griser_degriser_'+num_domaine)) {
					if(document.getElementById('chk_visibilite_griser_degriser_'+num_domaine).checked==true) {
						document.getElementById(tr_courant).style.backgroundColor='grey';
					}
					else {
						document.getElementById(tr_courant).style.backgroundColor='';
					}
				}
			}
		}
	}
</script>\n";
	}

if(isset($temoin_anomalie_categorie)&&($temoin_anomalie_categorie=='y')) {
	$titre="Anomalie d'association enseignement/catégorie";
	$texte="<p>Cet enseignement est associé à une catégorie qui n'existe pas ou plus.<br />Veuillez contrôler les paramètres et cliquer sur <strong>Enregistrer</strong> pour corriger.";
	$tabdiv_infobulle[]=creer_div_infobulle('association_anormale_enseignement_categorie',$titre,"",$texte,"",30,0,'y','y','n','n');
}

echo "<input type='hidden' name='is_posted' value='1' />\n";
echo "<input type='hidden' name='id_classe' value='" . $id_classe . "' />\n";
echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";
echo "</form>\n";

if($msg_groupes_homonymes!='') {
	echo "<p><br /></p>\n";
	echo "<a name='groupes_homonymes'></a>\n";
	echo "<p style='margin-left:6.3em; text-indent:-6.3em;'><span style='font-weight:bold; color:red'>Attention&nbsp;:</span> $msg_groupes_homonymes";
	echo "Vous devriez renommer ces groupes de façon à ce que vous comme les professeurs,... les distinguent plus facilement.<br />";
	echo "Vous pouvez par exemple ajouter des suffixes _1, _2,...<br />";
	echo "ou laisser le nom sans suffixe pour un groupe classe et mettre des suffixes _1, _2,... pour des sous-groupes.<br />";
	echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;ajouter_suffixes_noms_groupes=y&amp;mode=num".add_token_in_url()."'>Ajouter automatiquement des suffixes _1, _2,...</a> ou des <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;ajouter_suffixes_noms_groupes=y&amp;mode=alpha".add_token_in_url()."'>Ajouter automatiquement des suffixes _A, _B,...</a>";
	echo "</p>\n";
	echo "<p><br /></p>\n";

	echo "<script type='text/javascript'>
document.getElementById('span_asterisque').innerHTML=\" <a href='#groupes_homonymes' title='Plusieurs groupes ont des descriptions identiques'><img src='../images/icons/flag2.gif' width='17' height='18' /></a>\";
</script>\n";
}

//================================================
// AJOUT:boireaus
echo "<script type='text/javascript' language='javascript'>
    function choix_ordre(){
    if(document.getElementById('ordre_alpha').checked){
        ordre_alpha();
    }
    else{
        ordre_defaut();
    }
    }
    function ordre_alpha(){
        cpt=0;
        enseignement=new Array();
        while(cpt<$cpt_grp){
            enseignement[cpt]=document.getElementById('enseignement_'+cpt).value;
            cpt++;
        }
        enseignement.sort();
        cpt=0;
        while(cpt<$cpt_grp){
            for(i=0;i<$cpt_grp;i++){
                docens=document.getElementById('enseignement_'+i).value;
                if(enseignement[cpt]==document.getElementById('enseignement_'+i).value){
                    document.getElementById('priorite_'+i).selectedIndex=cpt+1;
                }
            }
            cpt++;
        }
        //document.forms['formulaire'].submit();
        changement();
    }

    function ordre_defaut(){";
        for($i=0;$i<count($mat_priorite);$i++){
            $rang=0;
            if($mat_priorite[$i]>0){$rang=$mat_priorite[$i]-10;}
            //echo "document.getElementById('priorite_'+$i).selectedIndex=$mat_priorite[$i];\n";
            echo "document.getElementById('priorite_'+$i).selectedIndex=$rang;\n";
        }
echo "}

    function coeff(){
        nombre=document.getElementById('coefficient_recopie').value;
        chaine_reg=new RegExp('[0-9]+');
        if(nombre.replace(chaine_reg,'').length!=0){
            nombre=0;
        }
        cpt=0;
        while(cpt<$cpt_grp){
            document.getElementById('coef_'+cpt).value=nombre;
            cpt++;
        }
        //document.forms['formulaire'].submit();
        changement();
    }
</script>\n";
?>


<!--form enctype="multipart/form-data" action="edit_class.php" name="formulaire2" method=post>
    <input type='button' value="Définir les priorités d'après l'ordre alphabétique" onClick="ordre_alpha();" /><br />
    Mettre tous les coefficients à <input type='button' value='0' onClick='coeff(0);' /> / <input type='button' value='1' onClick='coeff(1);' />
</form-->
<p><i>Remarques:</i></p>
<ul>
<li>Un seul coefficient non nul provoque l'apparition de tous les coefficients sur les bulletins.</li>
<li>Un/des coefficients non nul(s) est/sont nécessaire(s) pour que la ligne moyenne générale apparaisse sur le bulletin.</li>
<!--li>Les coefficients réglés ici ne s'appliquent qu'à la classe <?php echo $classe["classe"]?>, même dans le cas des enseignements concernant d'autres classes.</li-->
<li>Pour les enseignements impliquant plusieurs classes, le coefficient s'applique à tous les élèves de la classe courante et peut être réglé indépendamment d'une classe à l'autre (<em>pour le régler individuellement par élève, voir la liste des élèves inscrits</em>).<br />
Les coefficients réglés ici ne s'appliquent donc qu'à la classe
<?php
    // Bizarre... $classe peut contenir une autre classe que celle en cours???
    $classe_tmp = get_classe($id_classe);
    echo $classe_tmp["classe"];
?>
, même dans le cas des enseignements concernant des regroupements de plusieurs classes.</li>
<li>En revanche, la visibilité d'un groupe dans tel ou tel domaine (<em>bulletins, carnet de notes, cahiers de textes</em>) est propre au groupe (<em>pour toutes les classes associées au groupe</em>).</li>
<li>
	Les modes de prise en compte de la moyenne d'un enseignement dans la moyenne générale sont les suivants&nbsp;:
	<ul>
		<li>La note compte&nbsp;: La note compte normalement dans la moyenne.</li>
		<li>Bonus&nbsp;: Les points au-dessus de 10 sont coefficientés et ajoutés au total des points, mais le total des coefficients n'est pas augmenté.</li>
		<li>Sup10&nbsp;: La note n'est comptée que si elle est supérieure à 10.<br />
		Remarque&nbsp;: Cela n'améliore pas nécessairement la moyenne générale de l'élève puisque s'il avait 13 de moyenne générale sans cette note, il perd des points s'il a 12 à un enseignement compté sup10.<br />
		Et l'élève qui a 9 à cet enseignement ne perd pas de point... injuste, non?</li>
	</ul>
</li>
</ul>
<?php

if($ouvrir_infobulle_nav=='y') {
	echo "<script type='text/javascript'>
	setTimeout(\"afficher_div('navigation_classe','y',-100,20);\",1000)
</script>\n";
}

require("../lib/footer.inc.php");

?>
