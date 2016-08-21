<?php
/**
 * Ajouter, modifier des modèles de carnets de notes
 * 
*
*  @copyright Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 *
 * @package Carnet_de_notes
 * @subpackage Conteneur
 * @license GNU/GPL
 * @see add_token_field()
 * @see checkAccess()
 * @see check_token()
 * @see corriger_caracteres()
 * @see get_group()
 * @see getPref()
 * @see getSettingValue()
 * @see mise_a_jour_moyennes_conteneurs()
 * @see recherche_enfant()
 * @see Session::security_check()
 * @see sous_conteneurs()
 * @see Verif_prof_cahier_notes()
*/

/*
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

$variables_non_protegees = 'yes';

/**
 * Fichiers d'initialisation
 */
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

$id_modele = isset($_POST["id_modele"]) ? $_POST["id_modele"] : (isset($_GET["id_modele"]) ? $_GET["id_modele"] : NULL);
$id_conteneur = isset($_POST["id_conteneur"]) ? $_POST["id_conteneur"] : (isset($_GET["id_conteneur"]) ? $_GET["id_conteneur"] : NULL);
$mode = isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : NULL);
$mode_modif = isset($_POST["mode_modif"]) ? $_POST["mode_modif"] : (isset($_GET["mode_modif"]) ? $_GET["mode_modif"] : NULL);

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$choix_periodes=isset($_POST['choix_periodes']) ? $_POST['choix_periodes'] : (isset($_GET['choix_periodes']) ? $_GET['choix_periodes'] : NULL);
$choix_matieres=isset($_POST['choix_matieres']) ? $_POST['choix_matieres'] : (isset($_GET['choix_matieres']) ? $_GET['choix_matieres'] : NULL);
$max_per=isset($_POST['max_per']) ? $_POST['max_per'] : (isset($_GET['max_per']) ? $_GET['max_per'] : NULL);


$msg="";

$gepi_denom_boite=casse_mot(getSettingValue("gepi_denom_boite"), 'min');

$tab_arrondir=array();
$tab_arrondir['s1']="dixième supérieur";
$tab_arrondir['s5']="demi-point supérieur";
$tab_arrondir['se']="entier supérieur";
$tab_arrondir['p1']="dixième le plus proche";
$tab_arrondir['p5']="demi-point le plus proche";
$tab_arrondir['pe']="entier le plus proche";

//debug_var();

/*
function get_arbo_boites($id_cahier_notes, $id_groupe="", $periode="") {
	$tab=array();
	if($id_cahier_notes=="") {
		$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE id_groupe='$id_groupe' AND periode='$periode';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$id_cahier_notes=old_mysql_result($res, 0, "id_cahier_notes");
		}
	}

	if($id_cahier_notes!="") {
		$sql="SELECT * FROM cn_conteneurs WHERE id_racine='$id_cahier_notes';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			while($lig=mysql_fetch_object($res)) {
				
			}
		}
	}
	return $tab;
}
*/
if((isset($_POST['valider_nouveau_modele']))&&(isset($_POST['nom_court']))&&(isset($NON_PROTECT['description']))) {
	check_token();

	$nom_court=$_POST['nom_court'];

	$description=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["description"]));

	$sql="INSERT INTO cn_conteneurs_modele SET nom_court='$nom_court', description='$description';";
	if($insert=mysqli_query($GLOBALS["mysqli"], $sql)) {
		$id_modele=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
		$msg="Modèle n°$id_modele : '$nom_court' créé.<br />";
	}
	else {
		$msg="ERREUR lors de la création du modèle '$nom_court'.<br />";
	}

	unset($mode);
}

if((isset($_GET['suppr_conteneur']))&&(is_numeric($_GET['suppr_conteneur']))) {
	check_token();

//		<td title='Supprimer ce(tte) ".$gepi_denom_boite."'><a href='".$_SERVER['PHP_SELF']."?mode=modifier_modele&amp;suppr_conteneur=$lig->id".add_token_in_url()."'><img src='../images/delete16.png' class='icone16' /></a></td>

	$sql="DELETE FROM cn_conteneurs_modele_conteneurs WHERE id='".$_GET['suppr_conteneur']."';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if($res) {
		$msg="Suppression du(de la) $gepi_denom_boite n°".$_GET['suppr_conteneur']." effectuée.<br />";
	}
	else {
		$msg="ERREUR lors de la suppression du(de la) $gepi_denom_boite n°".$_GET['suppr_conteneur'].".<br />";
	}

	unset($mode_modif);
}

if((isset($_POST['enregistrer_conteneur']))&&(isset($id_modele))&&(is_numeric($id_modele))) {
	check_token();

	$reg_ok = "yes";
	$new='no';
	if(!isset($_POST['id_conteneur'])) {
		//$sql="insert into cn_conteneurs_modele_conteneurs (id_racine,nom_court,parent) values ('$id_racine','nouveau','$id_racine')";
		$sql="insert into cn_conteneurs_modele_conteneurs (nom_court, id_modele) values ('nouveau', '$id_modele')";
		//echo "$sql<br />";
		$reg = mysqli_query($GLOBALS["mysqli"], $sql);
		$id_conteneur = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
		if (!$reg) {$reg_ok = "no";}
		$new='yes';
	}

	/*
	if (isset($_POST['mode']) and ($_POST['mode']) and my_ereg("^[12]{1}$", $_POST['mode'])) {
	if ($_POST['mode'] == 1) $_SESSION['affiche_tous'] = 'yes';
	$sql="UPDATE cn_conteneurs_modele_conteneurs SET mode = '".$_POST['mode']."' WHERE id = '$id_conteneur'";
	*/
	$sql="UPDATE cn_conteneurs_modele_conteneurs SET mode = '2' WHERE id = '$id_conteneur'";
	//echo "$sql<br />";
	$reg = mysqli_query($GLOBALS["mysqli"], $sql);
	if (!$reg) {$reg_ok = "no";}
	//}

	if ($_POST['nom_court']) {
		$nom_court = $_POST['nom_court'];
	} else {
		$nom_court = "Cont. ".$id_conteneur;
	}
	$sql="UPDATE cn_conteneurs_modele_conteneurs SET nom_court = '".corriger_caracteres($nom_court)."' WHERE id = '$id_conteneur'";
	//echo "$sql<br />";
	$reg = mysqli_query($GLOBALS["mysqli"], $sql);
	if (!$reg) {$reg_ok = "no";}


	if ($_POST['nom_complet'])  {
		$nom_complet = $_POST['nom_complet'];
	} else {
		$nom_complet = $nom_court;
	}

	$sql="UPDATE cn_conteneurs_modele_conteneurs SET nom_complet = '".corriger_caracteres($nom_complet)."' WHERE id = '$id_conteneur'";
	//echo "$sql<br />";
	$reg = mysqli_query($GLOBALS["mysqli"], $sql);
	if (!$reg) {$reg_ok = "no";}

	if ($_POST['description'])  {
		$sql="UPDATE cn_conteneurs_modele_conteneurs SET description = '".corriger_caracteres($_POST['description'])."' WHERE id = '$id_conteneur'";
		//echo "$sql<br />";
		$reg = mysqli_query($GLOBALS["mysqli"], $sql);
		if (!$reg) {$reg_ok = "no";}
	}
	/*
	if (isset($_POST['parent']))  {
	$parent = $_POST['parent'];
	$sql="UPDATE cn_conteneurs_modele_conteneurs SET parent = '".$parent."' WHERE id = '$id_conteneur'";
	//echo "$sql<br />";
	$reg = mysql_query($sql);
	if (!$reg) {$reg_ok = "no";}
	}
	*/

	if (isset($_POST['coef'])) {
		$tmp_coef=$_POST['coef'];
		if((preg_match("/^[0-9]*$/", $tmp_coef))||(preg_match("/^[0-9]*\.[0-9]$/", $tmp_coef))) {
			// Le coef a le bon format
			//$msg.="Le coefficient proposé $tmp_coef est valide.<br />";
		}
		elseif(preg_match("/^[0-9]*\.[0-9]*$/", $tmp_coef)) {
			$msg.="Le coefficient ne peut avoir plus d'un chiffre après la virgule. Le coefficient va être tronqué.<br />";
		}
		elseif(preg_match("/^[0-9]*,[0-9]$/", $tmp_coef)) {
			$msg.="Correction du séparateur des décimales dans le coefficient de $tmp_coef en ";
			$tmp_coef=preg_replace("/,/", ".", $tmp_coef);
			$msg.=$tmp_coef."<br />";
		}
		else {
			$msg.="Le coefficient proposé $tmp_coef est invalide. Mise à 1.0 du coefficient.<br />";
			$tmp_coef="1.0";
		}
		$sql="UPDATE cn_conteneurs_modele_conteneurs SET coef = '" . $tmp_coef . "' WHERE id = '$id_conteneur'";
		//echo "$sql<br />";
		$reg = mysqli_query($GLOBALS["mysqli"], $sql);
		if (!$reg) {$reg_ok = "no";}
	} else {
		$sql="UPDATE cn_conteneurs_modele_conteneurs SET coef = '0' WHERE id = '$id_conteneur'";
		//echo "$sql<br />";
		$reg = mysqli_query($GLOBALS["mysqli"], $sql);
		if (!$reg) {$reg_ok = "no";}
	}

	if ($_POST['ponderation']) {
		$sql="UPDATE cn_conteneurs_modele_conteneurs SET ponderation = '". $_POST['ponderation']."' WHERE id = '$id_conteneur'";
		//echo "$sql<br />";
		$reg = mysqli_query($GLOBALS["mysqli"], $sql);
		if (!$reg) {$reg_ok = "no";}
	} else {
		$sql="UPDATE cn_conteneurs_modele_conteneurs SET ponderation = '0' WHERE id = '$id_conteneur'";
		//echo "$sql<br />";
		$reg = mysqli_query($GLOBALS["mysqli"], $sql);
		if (!$reg) {$reg_ok = "no";}
	}

	if (($_POST['precision']) and my_ereg("^(s1|s5|se|p1|p5|pe)$", $_POST['precision'])) {
		$sql="UPDATE cn_conteneurs_modele_conteneurs SET arrondir = '". $_POST['precision']."' WHERE id = '$id_conteneur'";
		//echo "$sql<br />";
		$reg = mysqli_query($GLOBALS["mysqli"], $sql);
		if (!$reg) {$reg_ok = "no";}
	}

	if (isset($_POST['display_parents'])) {
		$display_parents = 1;
	} else {
		$display_parents = 0;
	}
	$sql="UPDATE cn_conteneurs_modele_conteneurs SET display_parents = '$display_parents' WHERE id = '$id_conteneur'";
	//echo "$sql<br />";
	$reg = mysqli_query($GLOBALS["mysqli"], $sql);
	if (!$reg) {$reg_ok = "no";}

	if (isset($_POST['display_bulletin'])) {
		$display_bulletin = 1;
	} else {
		$display_bulletin = 0;
	}
	$sql="UPDATE cn_conteneurs_modele_conteneurs SET display_bulletin = '$display_bulletin' WHERE id = '$id_conteneur'";
	//echo "$sql<br />";
	$reg = mysqli_query($GLOBALS["mysqli"], $sql);
	if (!$reg) {$reg_ok = "no";}

	if ($reg_ok=='yes') {
		if ($new=='yes') $msg.="Nouvel enregistrement réussi.";
		else $msg.="Les modifications ont été effectuées avec succès.";
	} else {
		$msg.="Il y a eu un problème lors de l'enregistrement";
	}

}


if((isset($_POST['appliquer_le_modele']))&&(isset($id_modele))&&(is_numeric($id_modele))&&(isset($choix_periodes))&&(isset($choix_matieres))) {
	check_token();

	$debug_appliquer_modele=false;

	// Récupérer le détail du modèle
	$tab_modele=array();
	$sql="SELECT * FROM cn_conteneurs_modele_conteneurs WHERE id_modele='$id_modele' ORDER BY nom_court, nom_complet, description;";
	if($debug_appliquer_modele) {echo "$sql<br />";}
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			$tab_modele[$cpt]['modele_id_conteneur']=$lig->id;
			$tab_modele[$cpt]['nom_court']=$lig->nom_court;
			$tab_modele[$cpt]['nom_complet']=$lig->nom_complet;
			$tab_modele[$cpt]['description']=$lig->description;
			$tab_modele[$cpt]['coef']=$lig->coef;
			$tab_modele[$cpt]['arrondir']=$lig->arrondir;
			$tab_modele[$cpt]['ponderation']=$lig->ponderation;
			$tab_modele[$cpt]['display_parents']=$lig->display_parents;
			$tab_modele[$cpt]['display_bulletin']=$lig->display_bulletin;
			$tab_modele[$cpt]['mode']=$lig->mode;
			/*
			echo "\$tab_modele[$cpt]<pre>";
			print_r($tab_modele[$cpt]);
			echo "</pre><br />";
			*/
			$cpt++;
		}
	}

	if(count($tab_modele)==0) {
		$msg.="Aucun(e) ".$gepi_denom_boite." n'existe dans le modèle n°$id_modele.<br />Il n'est pas possible d'appliquer ce modèle.<br />";
	}
	else {
		$nb_insert=0;
		$nb_update=0;
		$nb_erreur=0;
		// Parcourir les classes, puis boucler sur les enseignements et enfin sur les périodes
		for($i=0;$i<count($id_classe);$i++) {
			if($debug_appliquer_modele) {echo "<br /><p class='bold'>".get_nom_classe($id_classe[$i])."<br />";}

			unset($id_groupe);
			if($choix_matieres=="certaines") {
				$id_groupe=isset($_POST['id_groupe_'.$id_classe[$i]]) ? $_POST['id_groupe_'.$id_classe[$i]] : array();
			}
			else {
				$id_groupe=array();
				//$sql="SELECT g.* FROM groupes g, j_groupes_classes jgc WHERE g.id=jgc.id_groupe AND jgc.id_classe='".$id_classe[$i]."' AND g.id NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='cahier_notes' AND visible='n');";
				$sql="SELECT jgc.id_groupe FROM j_groupes_classes jgc WHERE jgc.id_classe='".$id_classe[$i]."' AND jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='cahier_notes' AND visible='n');";
				
				if($debug_appliquer_modele) {echo "$sql<br />";}
				$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_grp=mysqli_fetch_object($res_grp)) {
					$id_groupe[]=$lig_grp->id_groupe;
				}
			}

			unset($num_periode);
			if($choix_periodes=="certaines") {
				$num_periode=isset($_POST['num_periode_'.$id_classe[$i]]) ? $_POST['num_periode_'.$id_classe[$i]] : array();
			}
			else {
				$num_periode=array();
				$sql="SELECT num_periode FROM periodes WHERE id_classe='".$id_classe[$i]."' ORDER BY num_periode;";
				if($debug_appliquer_modele) {echo "$sql<br />";}
				$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_per=mysqli_fetch_object($res_per)) {
					$num_periode[]=$lig_per->num_periode;
				}
			}

			// Boucler sur les enseignements
			for($j=0;$j<count($id_groupe);$j++) {
				$current_group=get_group($id_groupe[$j]);
				if($debug_appliquer_modele) {echo "<br /><p>".$current_group['name']." ".$current_group['classlist_string']." ".$current_group['profs']['proflist_string']."<br />";}
				// Boucler sur les périodes
				for($k=0;$k<count($num_periode);$k++) {
					unset($id_cahier_notes);

					// Tester si le carnet de notes existe
					$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE id_groupe='".$id_groupe[$j]."' AND periode='".$num_periode[$k]."';";
					if($debug_appliquer_modele) {echo "$sql<br />";}
					$res_ccn=mysqli_query($GLOBALS["mysqli"], $sql);
					// Créer le carnet de notes s'il n'existe pas.
					if(mysqli_num_rows($res_ccn)==0) {
						// Créer le carnet de notes
						if($debug_appliquer_modele) {echo "Le carnet de notes n'existe pas en période ".$num_periode[$k].".<br />";}
						$current_group=get_group($id_groupe[$j]);

						$nom_complet_matiere = $current_group["matiere"]["nom_complet"];
						$nom_court_matiere = $current_group["matiere"]["matiere"];
						$sql="INSERT INTO cn_conteneurs SET id_racine='', 
															nom_court='".traitement_magic_quotes($current_group["description"])."', 
															nom_complet='". traitement_magic_quotes($nom_complet_matiere)."', 
															description = '', 
															mode = '2', 
															coef = '1.0', 
															arrondir = 's1', 
															ponderation = '0.0', 
															display_parents = '0', 
															display_bulletin = '1', 
															parent = '0'";
						if($debug_appliquer_modele) {echo "$sql<br />";}
						$reg = mysqli_query($GLOBALS["mysqli"], $sql);
						if ($reg) {
							$id_cahier_notes = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
							$sql="UPDATE cn_conteneurs SET id_racine='$id_cahier_notes', parent = '0' WHERE id='$id_cahier_notes'";
							if($debug_appliquer_modele) {echo "$sql<br />";}
							$reg = mysqli_query($GLOBALS["mysqli"], $sql);
							$sql="INSERT INTO cn_cahier_notes SET id_groupe = '".$id_groupe[$j]."', periode = '".$num_periode[$k]."', id_cahier_notes='$id_cahier_notes'";
							if($debug_appliquer_modele) {echo "$sql<br />";}
							$reg = mysqli_query($GLOBALS["mysqli"], $sql);
						}
					}
					else {
						if($debug_appliquer_modele) {echo "Le carnet de notes existait déjà en période ".$num_periode[$k].".<br />";}
						$id_cahier_notes=old_mysql_result($res_ccn, 0, "id_cahier_notes");
					}
					if($debug_appliquer_modele) {echo "\$id_cahier_notes=$id_cahier_notes<br />";}

					if(isset($id_cahier_notes)) {
						// Récupérer la liste des conteneurs existants... ou comparer avec les modele_id_conteneur

						// Boucler sur les conteneurs du modèle
						for($m=0;$m<count($tab_modele);$m++) {
							// Tester si le conteneur existe déjà
							if($debug_appliquer_modele) {echo "<br /><p>On va tester si un conteneur correspondant à modele_id_conteneur='".$tab_modele[$m]['modele_id_conteneur']."' existe ou non déjà<br />";}
							$sql="SELECT * FROM cn_conteneurs WHERE id_racine='$id_cahier_notes' AND modele_id_conteneur='".$tab_modele[$m]['modele_id_conteneur']."';";
							if($debug_appliquer_modele) {echo "$sql<br />";}
							$res_cn=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_cn)==0) {
								if($debug_appliquer_modele) {echo "Aucun conteneur n'existe encore avec modele_id_conteneur='".$tab_modele[$m]['modele_id_conteneur']."'<br />";}
								$sql="INSERT INTO cn_conteneurs SET id_racine='$id_cahier_notes',
																	nom_court='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_modele[$m]['nom_court'])."',
																	nom_complet='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_modele[$m]['nom_complet'])."',
																	description='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_modele[$m]['description'])."',
																	mode='".$tab_modele[$m]['mode']."',
																	coef='".$tab_modele[$m]['coef']."',
																	arrondir='".$tab_modele[$m]['arrondir']."',
																	ponderation='".$tab_modele[$m]['ponderation']."',
																	display_parents='".$tab_modele[$m]['display_parents']."',
																	display_bulletin='".$tab_modele[$m]['display_bulletin']."',
																	modele_id_conteneur='".$tab_modele[$m]['modele_id_conteneur']."',
																	parent='$id_cahier_notes';";
								if($debug_appliquer_modele) {echo "$sql<br />";}
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {
									$nb_insert++;
								}
								else {
									$nb_erreur++;
								}
							}
							else {
								if($debug_appliquer_modele) {echo "Un conteneur existe déjà avec modele_id_conteneur='".$tab_modele[$m]['modele_id_conteneur']."'<br />";}
								$id_conteneur=old_mysql_result($res_cn, 0, "id");
								// Faut-il modifier parent pour remettre à la racine du carnet de notes?
								// Faut-il réimposer le id_racine qui doit déjà être à $id_cahier_notes
								$sql="UPDATE cn_conteneurs SET id_racine='$id_cahier_notes',
																	nom_court='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_modele[$m]['nom_court'])."',
																	nom_complet='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_modele[$m]['nom_complet'])."',
																	description='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_modele[$m]['description'])."',
																	mode='".$tab_modele[$m]['mode']."',
																	coef='".$tab_modele[$m]['coef']."',
																	arrondir='".$tab_modele[$m]['arrondir']."',
																	ponderation='".$tab_modele[$m]['ponderation']."',
																	display_parents='".$tab_modele[$m]['display_parents']."',
																	display_bulletin='".$tab_modele[$m]['display_bulletin']."',
																	modele_id_conteneur='".$tab_modele[$m]['modele_id_conteneur']."',
																	parent='$id_cahier_notes'
															WHERE id='$id_conteneur';";
								if($debug_appliquer_modele) {echo "$sql<br />";}
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
								if($update) {
									$nb_update++;
								}
								else {
									$nb_erreur++;
								}
							}
						}
					}
				}
			}
		}
		if($nb_insert>0) {
			$msg.="$nb_insert ".$gepi_denom_boite."s ont été créées.<br />";
		}
		if($nb_update>0) {
			$msg.="$nb_update ".$gepi_denom_boite."s ont été mises à jour.<br />";
		}
		if($nb_erreur>0) {
			$msg.=$nb_erreur." erreurs se sont produites.<br />";
		}
	}
}

/*
CREATE TABLE cn_conteneurs_modele_conteneurs (
id int(11) NOT NULL auto_increment, 
id_modele int(11) NOT NULL default '0', 
id_racine int(11) NOT NULL default '0', 
nom_court varchar(32) NOT NULL default '', 
nom_complet varchar(64) NOT NULL default '', 
description varchar(128) NOT NULL default '', 
mode char(1) NOT NULL default '2', 
coef decimal(3,1) NOT NULL default '1.0', 
arrondir char(2) NOT NULL default 's1', 
ponderation decimal(3,1) NOT NULL default '0.0', 
display_parents char(1) NOT NULL default '0', 
display_bulletin char(1) NOT NULL default '1', 
parent int(11) NOT NULL default '0', 
PRIMARY KEY  (id), 
INDEX parent_racine (parent,id_racine), 
INDEX racine_bulletin (id_racine,display_bulletin)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE cn_conteneurs_modele (
id_modele int(11) NOT NULL auto_increment, 
nom_court varchar(32) NOT NULL default '', 
description varchar(128) NOT NULL default '', 
PRIMARY KEY  (id_modele)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO droits VALUES ('/cahier_notes_admin/creation_conteneurs_par_lots.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F','F', 'Création de conteneurs/boites par lots', '1');

====================================================================

DROP TABLE IF EXISTS `cn_conteneurs`;
CREATE TABLE `cn_conteneurs` ( `id` int(11) NOT NULL auto_increment, `id_racine` int(11) NOT NULL default '0', `nom_court` varchar(32) NOT NULL default '', `nom_complet` varchar(64) NOT NULL default '', `description` varchar(128) NOT NULL default '', `mode` char(1) NOT NULL default '2', `coef` decimal(3,1) NOT NULL default '1.0', `arrondir` char(2) NOT NULL default 's1', `ponderation` decimal(3,1) NOT NULL default '0.0', `display_parents` char(1) NOT NULL default '0', `display_bulletin` char(1) NOT NULL default '1', `parent` int(11) NOT NULL default '0', PRIMARY KEY  (`id`), INDEX parent_racine (`parent`,`id_racine`), INDEX racine_bulletin (`id_racine`,`display_bulletin`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

mysql> select * from cn_conteneurs limit 5;
+------+-----------+----------------------------+----------------------+-------------+------+------+----------+-------------+-----------------+------------------+--------+
| id   | id_racine | nom_court                  | nom_complet          | description | mode | coef | arrondir | ponderation | display_parents | display_bulletin | parent |
+------+-----------+----------------------------+----------------------+-------------+------+------+----------+-------------+-----------------+------------------+--------+
| 4798 |      4798 | ED.PHYSIQUE & SPORT.       | ED.PHYSIQUE & SPORT. |             | 2    |  1.0 | s1       |         0.0 | 0               | 1                |      0 |
| 4799 |      4799 | ANGLAIS LV2 (BIL)          | ANGLAIS LV2          |             | 2    |  1.0 | s1       |         0.0 | 0               | 1                |      0 |
| 4800 |      4800 | ARTS PLASTIQUES            | ARTS PLASTIQUES      |             | 2    |  1.0 | s1       |         0.0 | 0               | 1                |      0 |
| 4801 |      4801 | ACCOMPAGNEMT. PERSO. (AP5) | ACCOMPAGNEMT. PERSO. |             | 2    |  1.0 | s1       |         0.0 | 0               | 1                |      0 |
| 4802 |      4802 | ESPAGNOL LV2               | ESPAGNOL LV2         |             | 2    |  1.0 | s1       |         0.0 | 0               | 1                |      0 |
+------+-----------+----------------------------+----------------------+-------------+------+------+----------+-------------+-----------------+------------------+--------+
5 rows in set (0.02 sec)

mysql> show fields from cn_conteneurs limit 5;
ERROR 1064 (42000): You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'limit 5' at line 1
mysql> show fields from cn_conteneurs;
+------------------+--------------+------+-----+---------+----------------+
| Field            | Type         | Null | Key | Default | Extra          |
+------------------+--------------+------+-----+---------+----------------+
| id               | int(11)      | NO   | PRI | NULL    | auto_increment |
| id_racine        | int(11)      | NO   | MUL | 0       |                |
| nom_court        | varchar(32)  | NO   |     |         |                |
| nom_complet      | varchar(64)  | NO   |     |         |                |
| description      | varchar(128) | NO   |     | NULL    |                |
| mode             | char(1)      | NO   |     | 2       |                |
| coef             | decimal(3,1) | NO   |     | 1.0     |                |
| arrondir         | char(2)      | NO   |     | s1      |                |
| ponderation      | decimal(3,1) | NO   |     | 0.0     |                |
| display_parents  | char(1)      | NO   |     | 0       |                |
| display_bulletin | char(1)      | NO   |     | 1       |                |
| parent           | int(11)      | NO   | MUL | 0       |                |
+------------------+--------------+------+-----+---------+----------------+
12 rows in set (0.03 sec)

mysql> 

*/

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Carnet de notes - Ajout/modification de ".my_strtolower(getSettingValue("gepi_denom_boite"))." par lots";
/**
 * Entête de la page
 */
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

if((isset($_POST['temoin_suhosin_1']))&&(!isset($_POST['temoin_suhosin_2']))) {
	echo "<p style='color:red'>Il semble que certaines variables n'ont pas été transmises.<br />Cela peut arriver lorsqu'on tente de transmettre (<em>cocher trop de cases</em>) trop de variables.<br />Vous devriez tenter de créer des ".$gepi_denom_boite."s pour moins de classes/périodes/enseignements à la fois.</p>\n";
	echo alerte_config_suhosin();
}

echo "<div class='norme'><p class='bold'><a href='../classes/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if(!isset($mode)) {
	echo "
	</p>
</div>";

	$tab_modele=array();
	$sql="SELECT id_modele, nom_court, description FROM cn_conteneurs_modele ORDER BY nom_court;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_modeles=mysqli_num_rows($res);
	$cpt=0;
	while($lig=mysqli_fetch_object($res)) {
		$tab_modele[$cpt]['id_modele']=$lig->id_modele;
		$tab_modele[$cpt]['nom_court']=$lig->nom_court;
		$tab_modele[$cpt]['description']=$lig->description;
		$cpt++;
	}

	echo "
<p>La présente page est destinée à créer des modèles de carnets de notes, en y paramétrant des ".$gepi_denom_boite."s.<br />
Une fois un ou des modèles créés, vous pourrez appliquer ces modèles à un ensemble d'enseignements choisis parmi une sélection de classes.<br />
Toutes les classes et enseignements choisis auront ainsi la même structure de ".$gepi_denom_boite."s.</p>

<ul>
	<li><a href='".$_SERVER['PHP_SELF']."?mode=creer_modele'>Créer un nouveau modèle</a></li>";

	if($nb_modeles>0) {
		echo "
	<li style='margin-top:1em;'>
		Modifier un modèle&nbsp;:
		<ul>";
		for($loop=0;$loop<$nb_modeles;$loop++) {
			echo "
			<li><a href='".$_SERVER['PHP_SELF']."?mode=modifier_modele&amp;id_modele=".$tab_modele[$loop]['id_modele']."'>".$tab_modele[$loop]['nom_court']." (<em>".nl2br($tab_modele[$loop]['description'])."</em>)</a></li>";
		}
		echo "
		</ul>
		Modifier, cela peut être ajouter des ".$gepi_denom_boite."s, en modifier les paramètres,...
	</li>
	<li style='margin-top:1em;'>Supprimer un modèle et ses ".$gepi_denom_boite."s&nbsp;:
		<ul>";
		for($loop=0;$loop<$nb_modeles;$loop++) {
			echo "
			<li><a href='".$_SERVER['PHP_SELF']."?mode=supprimer_modele&amp;id_modele=".$tab_modele[$loop]['id_modele'].add_token_in_url()."'";
			echo " onclick=\"return confirm('Etes-vous sûr de vouloir supprimer ce modèle ?')\"";
			echo ">".$tab_modele[$loop]['nom_court']." (<em>".nl2br($tab_modele[$loop]['description'])."</em>)</a></li>";
		}
		echo "
		</ul>
		Les ".$gepi_denom_boite."s éventuellement créés sur le modèle dans des enseignements ne seront pas supprimés.
	</li>
	<li style='margin-top:1em;'>
		Appliquer un modèle à une sélection d'enseignements&nbsp;:<br />
		<ul>";
		for($loop=0;$loop<$nb_modeles;$loop++) {
			echo "
			<li><a href='".$_SERVER['PHP_SELF']."?mode=appliquer_modele&amp;id_modele=".$tab_modele[$loop]['id_modele']."'>".$tab_modele[$loop]['nom_court']." (<em>".nl2br($tab_modele[$loop]['description'])."</em>)</a></li>";
		}
		echo "
		</ul>
	</li>";
	}
	echo "
</ul>

<p style='text-indent:-4em; margin-left:4em; margin-top:2em;'><em>NOTES&nbsp;</em></p>
<ul>
	<li style='color:red'>A faire: Permettre de consulter l'état actuel des boites des enseignements...<br />
	Modifier: choisir les classes, périodes, enseignements, puis choisir le modèle à appliquer.</li>
	<li>Ce dispositif ne permet pas de supprimer des ".$gepi_denom_boite."s existant(e)s.</li>
	<li>Si vous créez dans un enseignement, via un modèle, une ".$gepi_denom_boite." <strong>DM</strong> alors que le professeur a déjà créé une ".$gepi_denom_boite." de même nom, le professeur se retrouvera avec deux ".$gepi_denom_boite."s <strong>DM</strong>.<br />
	Seule celle que vous aurez vous-même créé pourra être modifiée depuis la présente page.<br />
	Un administrateur ne peut pas intervenir sur les ".$gepi_denom_boite."s qu'un professeur se crée lui-même.<br />
	(<em>les ".$gepi_denom_boite."s créées depuis la présente page ont un identifiant les rattachant au modèle sur lequel elles ont été créées (champ 'modele_id_conteneur' de la table 'cn_conteneurs')</em>)</li>
</ul>";
}
//==============================================================================
elseif($mode=="creer_modele") {
	echo " | <a href='".$_SERVER['PHP_SELF']."'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">Index de la création de modèles</a>
	</p>
</div>";

	echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method='post'>
	".add_token_field()."
	<p class='bold'>Création d'un modèle&nbsp;:</p>
	<blockquote>
		<table summary='Paramètres'>
			<tr>
				<td>Nom court&nbsp;:</td>
				<td><input type='text' name='nom_court' value='' onchange='changement()' /></td>
			</tr>
			<tr>
				<td valign='top'>Description&nbsp;:</td>
				<td><textarea class='wrap' name=\"no_anti_inject_description\" rows='4' cols='40' onchange='changement()'></textarea></td>
			</tr>
		</table>
		<input type='hidden' name='valider_nouveau_modele' value='y' />
		<p><input type='submit' value='Créer le modèle' /></p>
		<p style='margin-top:2em;'>Il est recommandé de saisir des noms explicites si vous créez plusieurs modèles.</p>
	</blockquote>
</form>\n";
}
//==============================================================================
elseif($mode=="supprimer_modele") {
	echo " | <a href='".$_SERVER['PHP_SELF']."'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">Index de la création de modèles</a>
	</p>
</div>";

	$sql="SELECT * FROM cn_conteneurs_modele WHERE id_modele='$id_modele';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Le modèle n°$id_modele est inconnu.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	check_token(false);

	echo "<p>Suppression du modèle n°$id_modele et de ses ".$gepi_denom_boite."s&nbsp;:<br />
Suppression d'éventuel(le)s ".$gepi_denom_boite."s&nbsp;: ";
	$sql="DELETE FROM cn_conteneurs_modele_conteneurs WHERE id_modele='$id_modele';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(!$res) {
		echo "<span style='color:red'>ERREUR</span><br />
On ne supprime pas le modèle lui-même.<br />
Essayez de trouver la cause de l'erreur.</p>";
	}
	else {
		echo "<span style='color:green'>OK</span><br />
Suppression du modèle lui-même&nbsp;: ";

		$sql="DELETE FROM cn_conteneurs_modele WHERE id_modele='$id_modele';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$res) {
			echo "<span style='color:red'>ERREUR</span><br />
Essayez de trouver la cause de l'erreur.</p>";
		}
		else {
			echo "<span style='color:green'>OK</span></p>";
		}
	}
}
//==============================================================================
elseif($mode=="modifier_modele") {
	echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Index de la création de modèles</a>";
	if(isset($mode_modif)) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modifier_modele&amp;id_modele=$id_modele' onclick=\"return confirm_abandon (this, change, '$themessage')\">Modèle courant</a>";
	}
	echo "
	</p>
</div>";

	$sql="SELECT * FROM cn_conteneurs_modele WHERE id_modele='$id_modele';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Le modèle n°$id_modele est inconnu.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$lig=mysqli_fetch_object($res);
	$nom_court_modele=$lig->nom_court;
	$description_modele=$lig->description;

	echo "
<p><span class='bold'>Modèle $nom_court_modele</span><br />
<em>$description_modele</em></p>";

	//++++++++++++++++++++++++++++++++++++++++++
	if(!isset($mode_modif)) {
		echo "
<p><a href='".$_SERVER['PHP_SELF']."?mode=modifier_modele&amp;mode_modif=ajouter_conteneur&amp;id_modele=$id_modele'>Ajouter un(e) ".$gepi_denom_boite." dans le modèle</a>.</p>";

		$sql="SELECT * FROM cn_conteneurs_modele_conteneurs WHERE id_modele='$id_modele' ORDER BY nom_court, nom_complet, description;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			echo "
<p>Voici la liste des ".$gepi_denom_boite."s du modèle&nbsp;:</p>
<blockquote>
<table class='boireaus boireaus_alt' summary='Conteneurs/boites dans ce modèle'>
	<tr>
		<th title='Modifier ce(tte) ".$gepi_denom_boite."'>Modif</th>
		<th title='Supprimer ce(tte) ".$gepi_denom_boite."'>Suppr</th>
		<th>Nom court</th>
		<th>Nom complet</th>
		<th>Description</th>
		<!--th>Mode de calcul de la moyenne???</th-->
		<th>Coef</th>
		<th>Arrondi</th>
		<th title=\"Pour chaque élève, le coefficient de la meilleure note de ce(tte) ".$gepi_denom_boite." augmente ou diminue de...\">Pondération</th>
		<th title=\"Faire apparaître la moyenne sur le relevé de notes destiné aux parents\">Rel.Not</th>
		<th title=\"Faire apparaître la moyenne sur le bulletin scolaire.
Si la case ci-contre est cochée, la moyenne de cette boîte apparaît sur le bulletin scolaire, en plus de la moyenne générale, à titre d'information.\">Bulletin</th>
	</tr>";

			while($lig=mysqli_fetch_object($res)) {
				echo "
		<tr>
			<td title='Modifier ce(tte) ".$gepi_denom_boite."'><a href='".$_SERVER['PHP_SELF']."?mode=modifier_modele&amp;id_modele=$id_modele&amp;mode_modif=modifier_conteneur&amp;id_conteneur=$lig->id".add_token_in_url()."'><img src='../images/edit16.png' class='icone16' /></a></td>
			<td title='Supprimer ce(tte) ".$gepi_denom_boite."'><a href='".$_SERVER['PHP_SELF']."?mode=modifier_modele&amp;id_modele=$id_modele&amp;suppr_conteneur=$lig->id".add_token_in_url()."' onclick=\"return confirm('Etes-vous sûr de vouloir supprimer ce(tte) ".addslashes($gepi_denom_boite)." ?')\"><img src='../images/delete16.png' class='icone16' /></a></td>
			<td>$lig->nom_court</td>
			<td>$lig->nom_complet</td>
			<td>$lig->description</td>
			<td>$lig->coef</td>
			<td title=\"".$tab_arrondir[$lig->arrondir]."\">$lig->arrondir</td>
			<td title=\"Pour chaque élève, le coefficient de la meilleure note de ce(tte) ".$gepi_denom_boite." augmente ou diminue de...\">$lig->ponderation</td>
			<td title=\"Faire apparaître la moyenne sur le relevé de notes destiné aux parents\"><img src='../images/".(($lig->display_parents==1) ? "enabled" : "disabled").".png' class='icone16' /></td>
			<td title=\"Faire apparaître la moyenne sur le bulletin scolaire.
Si la case ci-contre est cochée, la moyenne de cette boîte apparaît sur le bulletin scolaire, en plus de la moyenne générale, à titre d'information.\"><img src='../images/".(($lig->display_bulletin==1) ? "enabled" : "disabled").".png' class='icone16' /></td>
		</tr>";

			}
		}
		echo "
	</table>
</blockquote>";
	}
	//++++++++++++++++++++++++++++++++++++++++++
	else {
		echo "<form id=\"form1\" name=\"form1\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">";

		// Valeurs par défaut:
		$nom_court="Nouvelle boite";
		$nom_complet="";
		$description="";
		$coef=1;
		$arrondir="s1";
		$ponderation=0;
		$display_parents=1;
		$display_bulletin=0;

		if($mode_modif=="modifier_conteneur") {
			// Récupération des valeurs du conteneur:
			$sql="SELECT * FROM cn_conteneurs_modele_conteneurs WHERE id='$id_conteneur';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				echo "<p style='color:red'>Anomalie le(la) $gepi_denom_boite n°$id_conteneur n'existe pas.</p>";
				require("../lib/footer.inc.php");
				die();
			}

			$lig=mysqli_fetch_object($res);
			$nom_court=$lig->nom_court;
			$nom_complet=$lig->nom_complet;
			$description=$lig->description;
			$coef=$lig->coef;
			$arrondir=$lig->arrondir;
			$ponderation=$lig->ponderation;
			$display_parents=$lig->display_parents;
			$display_bulletin=$lig->display_bulletin;

			echo "
<input type='hidden' name='id_conteneur' value='$id_conteneur' />
<blockquote>
	<p class='bold'>Modifier les paramètres du(de la) $gepi_denom_boite $nom_court&nbsp;:</p>";
		}
		else {
			echo "
<blockquote>
	<p class='bold'>Nouveau(elle) $gepi_denom_boite&nbsp;:</p>";
		}

	echo "
	".add_token_field()."
	<blockquote>
		<table>
			<tr><td>Nom court : </td><td><input type='text' name = 'nom_court' size='40' value = \"".$nom_court."\" onfocus=\"javascript:this.select()\" /></td></tr>
			<tr><td>Nom complet : </td><td><input type='text' name = 'nom_complet' size='40' value = \"".$nom_complet."\" onfocus=\"javascript:this.select()\" /></td></tr>
			<tr><td>Description : </td><td><input type='text' name = 'description' size='40' value = \"".$description."\" onfocus=\"javascript:this.select()\" /></td></tr>
		</table>
	</blockquote>";

	// Pour le moment, on ne permet pas de faire plusieurs niveaux
	/*
	if(getSettingValue("gepi_denom_boite_genre")=='f'){
		echo "<table><tr><td><h3 class='gepi'>Emplacement de la ".my_strtolower(getSettingValue("gepi_denom_boite"))." : </h3></td>\n<td>\n";
	}
	else{
		echo "<table><tr><td><h3 class='gepi'>Emplacement du ".my_strtolower(getSettingValue("gepi_denom_boite"))." : </h3></td>\n<td>\n";
	}
	echo "<select size='1' name='parent'>\n";
	
	$sql="SELECT * FROM cn_conteneurs WHERE id_racine ='$id_racine' order by nom_court";
	$appel_conteneurs = mysql_query($sql);
	$nb_cont = mysql_num_rows($appel_conteneurs);
	$i = 0;
	while ($i < $nb_cont) {
		$lig_cont=mysql_fetch_object($appel_conteneurs);
		$id_cont=$lig_cont->id;
		$id_parent=$lig_cont->parent;
		if(($id_cont!=$id_conteneur)&&($id_parent!=$id_conteneur)){
			// On recherche si le conteneur est un descendant du conteneur courant.
			$tmp_parent=$id_parent;
			$temoin_display="oui";
			while($tmp_parent!=0){
				$sql="SELECT * FROM cn_conteneurs WHERE id_racine ='$id_racine' AND id='$tmp_parent'";
				//echo "<!-- $sql -->\n";
				$res_parent=mysql_query($sql);
				$lig_parent=mysql_fetch_object($res_parent);
				$tmp_parent=$lig_parent->parent;
				if($tmp_parent==$id_conteneur){
					$temoin_display="non";
				}
			}

			if($temoin_display=="oui"){
				$nom_conteneur=$lig_cont->nom_court;
				echo "<option value='$id_cont'";
				if ($parent == $id_cont){echo " selected ";}
				if($nom_conteneur==""){echo " >---</option>\n";}else{echo " >$nom_conteneur</option>\n";}
			}
		}
		//==========================================
		$i++;
	}
	echo "</select></td></tr></table>\n";
	*/

	if(getSettingValue("gepi_denom_boite_genre")=='f'){
		echo "
	<h3 class='gepi'>Coefficient de la ".my_strtolower(getSettingValue("gepi_denom_boite"))." $nom_court</h3>\n";
	}
	else{
		echo "
	<h3 class='gepi'>Coefficient du ".my_strtolower(getSettingValue("gepi_denom_boite"))." $nom_court</h3>\n";
	}

	echo "
	<blockquote>";
	echo "
		<table>
			<tr>
				<td>
					Valeur de la pondération dans le calcul de la moyenne de l'enseignement<br />
					<i>(si 0, la moyenne de <b>$nom_court</b> n'intervient pas dans le calcul de la moyenne du carnet de note)</i>.
				</td>
				<td>
					<input type='text' name = 'coef' id = 'coef' size='4' value = \"".$coef."\" onfocus=\"javascript:this.select()\" onkeydown=\"clavier_2(this.id,event,0,10);\" autocomplete=\"off\" title=\"Vous pouvez modifier le coefficient à l'aide des flèches Up et Down du pavé de direction.\" />
				</td>
			</tr>
		</table>\n";
	echo "
	</blockquote>";

	// On force actuellement le mode à 2
	/*
	if ($nb_sous_cont != 0) {
		$chaine_sous_cont="";
		$i=0;
		while ($i < $nb_sous_cont) {
			$chaine_sous_cont.="<b>$nom_sous_cont[$i]</b>, ";
			$i++;
		}

		if(getSettingValue("gepi_denom_boite_genre")=='f'){
			echo "<h3 class='gepi'>Notes prises en comptes dans le calcul de la moyenne de la ".my_strtolower(getSettingValue("gepi_denom_boite"))." $nom_court</h3>\n";
		}
		else{
			echo "<h3 class='gepi'>Notes prises en comptes dans le calcul de la moyenne du ".my_strtolower(getSettingValue("gepi_denom_boite"))." $nom_court</h3>\n";
		}
		if($i>1){
			echo "<table>\n<tr>";
			echo "<td style='padding-left:3em; vertical-align:top;'>";
			echo "<label for='mode_2'>la moyenne s'effectue sur toutes les notes contenues à la racine de <b>$nom_court</b> et sur les moyennes des ";
			echo my_strtolower(getSettingValue("gepi_denom_boite"))."s ";
			echo " $chaine_sous_cont";
			echo "en tenant compte des options dans ces ";
			echo my_strtolower(getSettingValue("gepi_denom_boite"))."s.</label>";
			echo "</td><td style='vertical-align:top;'><input type='radio' name='mode' id='mode_2' value='2' "; if ($mode=='2') echo "checked"; echo " /></td>";
			echo "</tr>\n";

			echo "<tr>";
			echo "<td style='padding-left:3em; vertical-align:top;'>";
			echo "<label for='mode_1'>la moyenne s'effectue sur toutes les notes contenues dans <b>$nom_court</b> et dans les ";
			echo my_strtolower(getSettingValue("gepi_denom_boite"))."s";
			echo " $chaine_sous_cont";
			echo "sans tenir compte des options définies dans ces ";
			echo my_strtolower(getSettingValue("gepi_denom_boite"))."s.</label>";
		}
		else{
			if(getSettingValue("gepi_denom_boite_genre")=='f'){
				echo "<table>\n<tr>";
				echo "<td style='padding-left:3em; vertical-align:top;'>";
				echo "<label for='mode_2'>la moyenne s'effectue sur toutes les notes contenues à la racine de <b>$nom_court</b> et sur les moyennes de la ";
				echo my_strtolower(getSettingValue("gepi_denom_boite"));
				echo " $chaine_sous_cont";
				echo "en tenant compte des options dans cette ";
				echo my_strtolower(getSettingValue("gepi_denom_boite"))."</label>";
				echo "</td><td style='vertical-align:top;'><input type='radio' name='mode' id='mode_2' value='2' "; if ($mode=='2') echo "checked"; echo " /></td>";
				echo "</tr>\n";

				echo "<tr>";
				echo "<td style='padding-left:3em; vertical-align:top;'>";
				echo "<label for='mode_1'>la moyenne s'effectue sur toutes les notes contenues dans <b>$nom_court</b> et dans la ";
				echo my_strtolower(getSettingValue("gepi_denom_boite"));
				echo " $chaine_sous_cont";
				echo "sans tenir compte des options définies dans cette ";
				echo my_strtolower(getSettingValue("gepi_denom_boite"))."</label>";
			}
			else{
				echo "<table>\n<tr>";
				echo "<td style='padding-left:3em; vertical-align:top;'>";
				echo "<label for='mode_2'>la moyenne s'effectue sur toutes les notes contenues à la racine de <b>$nom_court</b> et sur les moyennes du ".my_strtolower(getSettingValue("gepi_denom_boite"));
				echo " $chaine_sous_cont";
				echo "en tenant compte des options dans ce ".my_strtolower(getSettingValue("gepi_denom_boite")).".</label>";
				echo "</td><td style='vertical-align:top;'><input type='radio' name='mode' id='mode_2' value='2' "; if ($mode=='2') echo "checked"; echo " /></td>";
				echo "</tr>\n";

				echo "<tr>";
				echo "<td style='padding-left:3em; vertical-align:top;'>";
				echo "<label for='mode_1'>la moyenne s'effectue sur toutes les notes contenues dans <b>$nom_court</b>";
				echo " et dans le ".my_strtolower(getSettingValue("gepi_denom_boite"))."";
				echo " $chaine_sous_cont";
				echo "sans tenir compte des options définies dans ce ".my_strtolower(getSettingValue("gepi_denom_boite")).".</label>";
			}
		}
		echo "</td><td style='vertical-align:top;'><input type='radio' name='mode' id='mode_1' value='1' "; if ($mode=='1') echo "checked"; echo " /></td>";
		echo "</tr>\n</table>\n";
	}
	*/

	echo "<h3 class='gepi'>Précision du calcul de la moyenne de $nom_court : </h3>\n";
	echo "
	<blockquote>";
	echo "<table>\n";
	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='precision' id='precision_s1' value='s1' "; if ($arrondir=='s1') echo "checked"; echo " />\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='precision_s1' style='cursor: pointer;'>";
	echo "Arrondir au dixième de point supérieur";
	echo "</label>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='precision' id='precision_s5' value='s5' "; if ($arrondir=='s5') echo "checked"; echo " />\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='precision_s5' style='cursor: pointer;'>";
	echo "Arrondir au demi-point supérieur";
	echo "</label>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='precision' id='precision_se' value='se' "; if ($arrondir=='se') echo "checked"; echo " />\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='precision_se' style='cursor: pointer;'>";
	echo "Arrondir au point entier supérieur";
	echo "</label>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='precision' id='precision_p1' value='p1' "; if ($arrondir=='p1') echo "checked"; echo " />\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='precision_p1' style='cursor: pointer;'>";
	echo "Arrondir au dixième de point le plus proche";
	echo "</label>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='precision' id='precision_p5' value='p5' "; if ($arrondir=='p5') echo "checked"; echo " />\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='precision_p5' style='cursor: pointer;'>";
	echo "Arrondir au demi-point le plus proche";
	echo "</label>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='precision' id='precision_pe' value='pe' "; if ($arrondir=='pe') echo "checked"; echo " />\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='precision_pe' style='cursor: pointer;'>";
	echo "Arrondir au point entier le plus proche";
	echo "</label>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "
	</blockquote>";




	echo "<h3 class='gepi'>Pondération</h3>\n";
	echo "
	<blockquote>";
	echo "<table>\n<tr><td>";
	echo "Pour chaque élève, le coefficient de la meilleure note de <b>$nom_court</b> augmente ou diminue de : &nbsp;</td>\n";
	echo "<td><input type='text' name='ponderation' id='ponderation' size='4' value = \"".$ponderation."\" onfocus=\"javascript:this.select()\" onkeydown=\"clavier_2(this.id,event,0,10);\" autocomplete=\"off\" title=\"Vous pouvez modifier le coefficient de la meilleure note à l'aide des flèches Up et Down du pavé de direction.\" /></td></tr>\n</table>\n";
	echo "
	</blockquote>";




	echo "<h3 class='gepi'>Affichage de la moyenne de $nom_court :</h3>\n";
	echo "
	<blockquote>";
	echo "<table>\n";
	echo "<tr>\n";

	echo "<td valign='top'>\n";
	echo "<input type='checkbox' name='display_parents' id='display_parents' "; if ($display_parents == 1) echo " checked";
	echo " />\n";
	echo "</td>\n";

	echo "<td>\n";
	echo "<label for='display_parents' style='cursor: pointer;'>";
	echo "Faire apparaître la moyenne sur le relevé de notes destiné aux parents";
	echo "</label>";
	echo "</td>\n";

	echo "</tr>\n";

	echo "<tr>\n";

	echo "<td valign='top'>\n";
	echo "<input type='checkbox' name='display_bulletin' id='display_bulletin'";
	if ($display_bulletin == 1) echo " checked"; echo " />\n";
	echo "</td>\n";

	echo "<td>\n";
	echo "<label for='display_bulletin' style='cursor: pointer;'>";
	echo "Faire apparaître la moyenne sur le bulletin scolaire.";
	echo "<br /><i>Si la case ci-contre est cochée, la moyenne de ce";
	if(getSettingValue("gepi_denom_boite_genre")=='f'){echo "tte";}
	echo " ".my_strtolower(getSettingValue("gepi_denom_boite"))." apparaît sur le bulletin scolaire, en plus de la moyenne générale, à titre d'information.</i>";
	echo "</label>";
	echo "</td>\n";

	echo "</tr>\n";
	echo "</table>\n";
	echo "
	</blockquote>";


	echo "
</blockquote>

<input type='hidden' name='id_modele' value='$id_modele' />
<input type='hidden' name='mode' value='modifier_modele' />
<input type='hidden' name='enregistrer_conteneur' value='y' />
<p><input type='submit' value='Valider' /></p>
</form>";


	}
}
//==============================================================================
elseif($mode=="appliquer_modele") {
	echo " | <a href='".$_SERVER['PHP_SELF']."'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">Index de la création de modèles</a>";

	$sql="SELECT * FROM cn_conteneurs_modele WHERE id_modele='$id_modele';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Le modèle n°$id_modele est inconnu.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$lig=mysqli_fetch_object($res);
	$nom_court_modele=$lig->nom_court;
	$description_modele=$lig->description;

	// Choisir des classes, puis des enseignements

	if(!isset($id_classe)) {

		echo "
	</p>
</div>

<p><span class='bold'>Modèle $nom_court_modele</span><br />
<em>$description_modele</em></p>

<p>Vous allez choisir les classes, les périodes, puis les enseignements dans lesquels créer l'arborescence de ".$gepi_denom_boite."s du modèle choisi.</p>";

		echo "<p class='bold'>Choix des classes&nbsp;:</p>\n";

		// Liste des classes avec élève:
		//$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) ORDER BY c.classe;";
		// Liste des classes avec périodes:
		$sql="SELECT DISTINCT c.* FROM periodes p, classes c WHERE (c.id=p.id_classe) ORDER BY c.classe;";
		$call_classes=mysqli_query($GLOBALS["mysqli"], $sql);

		$nb_classes=mysqli_num_rows($call_classes);
		if($nb_classes==0){
			echo "<p>Aucune classe avec périodes définies n'a été trouvée.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>
	<input type='hidden' name='id_modele' value='$id_modele' />
	<input type='hidden' name='mode' value='appliquer_modele' />
	\n";

		// Affichage sur 3 colonnes
		$nb_classes_par_colonne=round($nb_classes/3);

		echo "<table width='100%' summary='Choix des classes'>\n";
		echo "<tr valign='top' align='center'>\n";

		$cpt = 0;

		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while($lig_clas=mysqli_fetch_object($call_classes)) {

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
	}
	elseif(!isset($choix_periodes)) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=appliquer_modele&amp;id_modele=$id_modele'>Retour au choix des classes</a>";

		echo "
	</p>
</div>

<p><span class='bold'>Modèle $nom_court_modele</span><br />
<em>$description_modele</em></p>";

		echo "<p class='bold'>Choix des périodes&nbsp;:</p>\n";

		//echo "<p style='color:red;'>A FAIRE: afficher les périodes closes...</p>\n";

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>
	<input type='hidden' name='id_modele' value='$id_modele' />
	<input type='hidden' name='mode' value='appliquer_modele' />\n";

		echo "<ul style='list-style-type: none;'>\n";
		echo "<li>\n";
		echo "<input type='radio' name='choix_periodes' id='choix_periodes_toutes' value='toutes' onchange='display_div_liste_periodes()' checked /><label for='choix_periodes_toutes'> Toutes les périodes</label>\n";
		echo "</li>\n";
		echo "<li>\n";
		echo "<input type='radio' name='choix_periodes' id='choix_periodes_certaines' onchange='display_div_liste_periodes()' value='certaines' /><label for='choix_periodes_certaines'> Certaines périodes seulement</label>\n";

		echo "<div id='div_liste_periodes' style='margin-left: 2em;'>\n";

		echo "<div id='div_coche_lot' style='float: right; width: 20em;'></div>\n";

		$max_per=0;
		$cpt=0;
		for($i=0;$i<count($id_classe);$i++) {
			$sql="SELECT * FROM periodes WHERE id_classe='".$id_classe[$i]."' ORDER BY num_periode;";
			//echo "$sql<br />";
			$call_per=mysqli_query($GLOBALS["mysqli"], $sql);
			$nombre_ligne=mysqli_num_rows($call_per);
			if($nombre_ligne==0) {
				echo "<p style='color:red;'>Aucune période  n'est définie dans la classe de ".get_class_from_id($id_classe[$i]).".</p>\n";
			}
			else {
				echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";

				$first_per[$id_classe[$i]]=$cpt;
				echo "<table class='boireaus' summary='Classe n°$id_classe[$i]'/>\n";
				echo "<tr>\n";
				echo "<th colspan='4'>\n";
				echo "Classe de ".get_class_from_id($id_classe[$i])."\n";
				echo "</th>\n";
				echo "</tr>\n";

				echo "<tr>\n";
				echo "<th>\n";
				//echo "Cocher/décocher\n";
				echo "<p><a href='#' onClick='ModifCase(".$id_classe[$i].",true);return false;'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href='#' onClick='ModifCase(".$id_classe[$i].",false);return false;'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a></p>\n";
				echo "</th>\n";
				echo "<th>Num</th>\n";
				echo "<th>Période</th>\n";
				echo "<th>Etat</th>\n";
				echo "</tr>\n";

				$alt=1;
				while($lig_per=mysqli_fetch_object($call_per)) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt white_hover'>\n";
					echo "<td>\n";
					echo "<input type='checkbox' name='num_periode_".$id_classe[$i]."[]' id='num_periode_$cpt' value='$lig_per->num_periode' onchange='change_style_per($cpt)' checked />\n";
					echo "</td>\n";

					echo "<td>\n";
					if($lig_per->num_periode>$max_per) {$max_per=$lig_per->num_periode;}
					echo $lig_per->num_periode;
					echo "</td>\n";

					echo "<td style='text-align:left; font-weight: bold;'><label for='num_periode_$cpt' id='label_periode_$cpt'>$lig_per->nom_periode</label></td>\n";

					echo "<td style='text-align:center;'>";
					if($lig_per->verouiller=='O') {echo "Close";}
					elseif($lig_per->verouiller=='P') {echo "Partiellement close";}
					elseif($lig_per->verouiller=='N') {echo "Ouverte en saisie";}
					else {echo "???";}
					echo "</td>\n";

					echo "</tr>\n";
					$cpt++;
				}
				echo "</table>\n";
				$last_per[$id_classe[$i]]=$cpt;
			}
			echo "<br />\n";
		}

		echo "<p><a href='javascript:ModifToutesCases(true)'>Cocher</a> / <a href='javascript:ModifToutesCases(false)'>décocher</a>  toutes les périodes</p>\n";

		echo "</div>\n";

		echo "</li>\n";

		echo "</ul>\n";

		echo "<input type='hidden' name='max_per' value='$max_per' />\n";
		echo "<p><input type='submit' value='Valider' /></p>\n";
		echo "</form>\n";

		$chaine_div_coche_lot="Pour toutes les classes,<br />";
		for($j=1;$j<=$max_per;$j++) {
			$chaine_div_coche_lot.="<a href='javascript:coche_lot($j,true)'>Cocher</a> / <a href='javascript:coche_lot($j,false)'>décocher</a> la période $j<br />";
		}
		$chaine_div_coche_lot.="<a href='javascript:ModifToutesCases(true)'>Cocher</a> / <a href='javascript:ModifToutesCases(false)'>décocher</a>  toutes les périodes";

		echo "<script type='text/javascript'>
		document.getElementById('div_liste_periodes').style.display='none';

		function display_div_liste_periodes() {
			if(document.getElementById('choix_periodes_certaines').checked==true) {
				document.getElementById('div_liste_periodes').style.display='block';
			}
			else {
				document.getElementById('div_liste_periodes').style.display='none';
			}
		}

		if(document.getElementById('div_coche_lot')) {
			document.getElementById('div_coche_lot').innerHTML=\"$chaine_div_coche_lot\";
		}

		function coche_lot(num,mode) {";

		for($i=0;$i<count($id_classe);$i++) {
			echo "for(k=0;k<$cpt;k++) {
		if(document.getElementById('num_periode_'+k)) {
			if(document.getElementById('num_periode_'+k).value==num) {
				document.getElementById('num_periode_'+k).checked = mode;
				change_style_per(k);
			}
		}
	}\n";
		}

		echo "
		}

		function ModifToutesCases(mode) {
	";
		for($i=0;$i<count($id_classe);$i++) {
			echo "		ModifCase(".$id_classe[$i].",mode);\n";
		}

		echo "	}

		function ModifCase(id_classe,mode) {
			var first_per=new Array();
			var last_per=new Array();\n";

		for($i=0;$i<count($id_classe);$i++) {
			echo "		first_per[".$id_classe[$i]."]=".$first_per[$id_classe[$i]].";
			last_per[".$id_classe[$i]."]=".$last_per[$id_classe[$i]].";\n";
		}

		echo "
			for (var k=first_per[id_classe];k<last_per[id_classe];k++) {
				if(document.getElementById('num_periode_'+k)){
					document.getElementById('num_periode_'+k).checked = mode;
					change_style_per(k);
				}
			}
		}

		function change_style_per(num) {
			if((document.getElementById('num_periode_'+num))&&(document.getElementById('label_periode_'+num))) {
				if(document.getElementById('num_periode_'+num).checked) {
					document.getElementById('label_periode_'+num).style.fontWeight='bold';
				}
				else {
					document.getElementById('label_periode_'+num).style.fontWeight='normal';
				}
			}
		}

	</script>\n";

	}
	elseif(!isset($choix_matieres)) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=appliquer_modele&amp;id_modele=$id_modele'>Retour au choix des classes</a>";
		//echo " | <a href='javascript: history.go(-1);'>Retour au choix des périodes</a>";
		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=appliquer_modele&amp;id_modele=$id_modele";
		for($i=0;$i<count($id_classe);$i++) {
			echo "&amp;id_classe[]=".$id_classe[$i];
		}
		echo "'>Retour au choix des périodes</a>";
		echo "
	</p>
</div>

<p><span class='bold'>Modèle $nom_court_modele</span><br />
<em>$description_modele</em></p>\n";

		echo "<p class='bold'>Choix des matières/enseignements&nbsp;:</p>\n";

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
		echo "<input type='hidden' name='choix_periodes' value='$choix_periodes' />\n";
		echo "<input type='hidden' name='max_per' value='$max_per' />
	<input type='hidden' name='id_modele' value='$id_modele' />
	<input type='hidden' name='mode' value='appliquer_modele' />
	<input type='hidden' name='temoin_suhosin_1' value='1' />\n";

		echo "<ul style='list-style-type: none;'>\n";
		echo "<li>\n";
		echo "<input type='radio' name='choix_matieres' id='choix_matieres_toutes' value='toutes' onchange='display_div_liste_enseignements()' checked /><label for='choix_matieres_toutes'> Tous les enseignements/matières</label>\n";
		echo "</li>\n";
		echo "<li>\n";
		echo "<input type='radio' name='choix_matieres' id='choix_matieres_certaines' onchange='display_div_liste_enseignements()' value='certaines' /><label for='choix_matieres_certaines'> Certains enseignements/matières seulement</label>\n";

		echo "<div id='div_liste_enseignements' style='margin-left: 2em;'>\n";

		echo "<div id='div_coche_lot' style='float: right; width: 20em;'></div>\n";

		$tab_id_matiere=array();
		$tab_liste_index_grp_matiere=array();
		$cpt=0;
		for($i=0;$i<count($id_classe);$i++) {
			//$sql="SELECT DISTINCT g.id, g.name, g.description FROM groupes g, j_groupes_classes jgc WHERE (g.id=jgc.id_groupe and jgc.id_classe='".$id_classe[$i]."') ORDER BY jgc.priorite, g.name";
			$sql="SELECT DISTINCT g.id, g.name, g.description, jgm.id_matiere FROM groupes g, j_groupes_classes jgc, j_groupes_matieres jgm WHERE (g.id=jgc.id_groupe AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='".$id_classe[$i]."') ORDER BY jgc.priorite, g.name";
			//echo "$sql<br />";
			$call_group = mysqli_query($GLOBALS["mysqli"], $sql);
			$nombre_ligne = mysqli_num_rows($call_group);
			if($nombre_ligne==0) {
				echo "<p style='color:red;'>Aucun enseignement n'est défini dans la classe de ".get_class_from_id($id_classe[$i]).".</p>\n";
			}
			else {

				$temoin_classe[$i]='y';
				if($choix_periodes=='certaines') {
					// =============
					// AJOUTER UN TEST... si on a choisi 'certaines' périodes, mais sans aucune période cochée
					// =============
					if(isset($_POST['num_periode_'.$id_classe[$i]])) {
						$tmp_per=$_POST['num_periode_'.$id_classe[$i]];
						for($loop=0;$loop<$max_per;$loop++) {
							if(isset($tmp_per[$loop])) {
								echo "<input type='hidden' name='num_periode_".$id_classe[$i]."[]' value='$tmp_per[$loop]' />\n";
							}
						}
					}
					else {
						$temoin_classe[$i]='n';
					}
				}

				if($temoin_classe[$i]=='y') {
					echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";

					$first_grp[$id_classe[$i]]=$cpt;
					echo "<table class='boireaus' summary='Classe n°$id_classe[$i]'/>\n";
					echo "<tr>\n";
					echo "<th colspan='3'>\n";
					echo "Classe de ".get_class_from_id($id_classe[$i])."\n";
					echo "</th>\n";
					echo "</tr>\n";
	
					echo "<tr>\n";
					echo "<th>\n";
					//echo "Cocher/décocher\n";
					echo "<p><a href='#' onClick='ModifCase(".$id_classe[$i].",true);return false;'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href='#' onClick='ModifCase(".$id_classe[$i].",false);return false;'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a></p>\n";
					echo "</th>\n";
					echo "<th>Enseignement</th>\n";
					echo "<th>Professeur</th>\n";
					echo "</tr>\n";

					$alt=1;
					while($lig_grp=mysqli_fetch_object($call_group)) {
						$alt=$alt*(-1);
						echo "<tr class='lig$alt white_hover'>\n";
						echo "<td>\n";
						echo "<input type='checkbox' name='id_groupe_".$id_classe[$i]."[]' id='id_groupe_$cpt' value='$lig_grp->id' onchange='change_style_groupe($cpt)' checked />\n";
						echo "</td>\n";
						echo "<td style='text-align:left; font-weight: bold;'><label for='id_groupe_$cpt' id='label_groupe_$cpt'>$lig_grp->name (<i>$lig_grp->description</i>)</label></td>\n";
						echo "<td style='text-align:left;'>\n";
						$sql="SELECT DISTINCT nom,prenom,civilite FROM utilisateurs u, j_groupes_professeurs jgp WHERE u.login=jgp.login AND jgp.id_groupe='$lig_grp->id' ORDER BY u.nom, u.prenom;";
						$res_prof_grp=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_prof_grp)>0) {
							$lig_prof_grp=mysqli_fetch_object($res_prof_grp);
							echo $lig_prof_grp->civilite." ".strtoupper($lig_prof_grp->nom)." ".casse_mot($lig_prof_grp->prenom,"majf2");
							while($lig_prof_grp=mysqli_fetch_object($res_prof_grp)) {
								echo ", ";
								echo $lig_prof_grp->civilite." ".strtoupper($lig_prof_grp->nom)." ".casse_mot($lig_prof_grp->prenom,"majf2");
							}
						}
						echo "</td>\n";
						echo "</tr>\n";

						$tab_liste_index_grp_matiere[$lig_grp->id_matiere][]=$cpt;
						if(!in_array($lig_grp->id_matiere, $tab_id_matiere)) {$tab_id_matiere[]=$lig_grp->id_matiere;}

						$cpt++;
					}
					echo "</table>\n";
					$last_grp[$id_classe[$i]]=$cpt;
				}
			}
			echo "<br />\n";
		}


		echo "<p><a href='javascript:ModifToutesCases(true)'>Cocher</a> / <a href='javascript:ModifToutesCases(false)'>décocher</a>  tous les enseignements</p>\n";


		echo "</div>\n";

		echo "</li>\n";

		echo "</ul>\n";

		echo "<p><input type='submit' value='Valider' /></p>\n";
		echo "<input type='hidden' name='temoin_suhosin_2' value='1' />
</form>\n";

		$chaine_div_coche_lot="Pour toutes les classes,<br />";

		for($j=0;$j<count($tab_id_matiere);$j++) {
			$chaine_div_coche_lot.="<a href='javascript:coche_lot($j,true)'>Cocher</a> / <a href='javascript:coche_lot($j,false)'>décocher</a> $tab_id_matiere[$j]<br />";

			for($k=0;$k<count($tab_liste_index_grp_matiere[$tab_id_matiere[$j]]);$k++) {
				if(!isset($chaine_array_index[$j])) {
					//$chaine_array_index[$j]="tab_index_$j=new Array(";
					$chaine_array_index[$j]="tab_index[$j]=new Array(";
					$chaine_array_index[$j].="'".$tab_liste_index_grp_matiere[$tab_id_matiere[$j]][$k]."'";
				}
				else {
					$chaine_array_index[$j].=", "."'".$tab_liste_index_grp_matiere[$tab_id_matiere[$j]][$k]."'";
				}
			}
			if(isset($chaine_array_index[$j])) {
				$chaine_array_index[$j].=");";
			}
		}
		$chaine_div_coche_lot.="<a href='javascript:ModifToutesCases(true)'>Cocher</a> / <a href='javascript:ModifToutesCases(false)'>décocher</a>  tous les enseignements";

		echo "<script type='text/javascript'>
		document.getElementById('div_liste_enseignements').style.display='none';

		function display_div_liste_enseignements() {
			if(document.getElementById('choix_matieres_certaines').checked==true) {
				document.getElementById('div_liste_enseignements').style.display='block';
			}
			else {
				document.getElementById('div_liste_enseignements').style.display='none';
			}
		}

		if(document.getElementById('div_coche_lot')) {
			document.getElementById('div_coche_lot').innerHTML=\"$chaine_div_coche_lot\";
		}

		function coche_lot(num,mode) {
			tab_index=new Array();
	";

		for($j=0;$j<count($tab_id_matiere);$j++) {
			echo "		".$chaine_array_index[$j];
		}

		echo "
			tab=tab_index[num];
			for(k=0;k<tab.length;k++) {
				//alert('id_groupe_'+tab[k]);
				if(document.getElementById('id_groupe_'+tab[k])) {
					document.getElementById('id_groupe_'+tab[k]).checked = mode;
					change_style_groupe(tab[k]);
				}
			}
		}

		function ModifToutesCases(mode) {
	";
		for($i=0;$i<count($id_classe);$i++) {
			if($temoin_classe[$i]=='y') {
				echo "		ModifCase(".$id_classe[$i].",mode);\n";
			}
		}

		echo "	}

		function ModifCase(id_classe,mode) {
			var first_grp=new Array();
			var last_grp=new Array();\n";

		for($i=0;$i<count($id_classe);$i++) {
			if($temoin_classe[$i]=='y') {
				echo "		first_grp[".$id_classe[$i]."]=".$first_grp[$id_classe[$i]].";
			last_grp[".$id_classe[$i]."]=".$last_grp[$id_classe[$i]].";\n";
			}
		}

		echo "
			for (var k=first_grp[id_classe];k<last_grp[id_classe];k++) {
				if(document.getElementById('id_groupe_'+k)){
					document.getElementById('id_groupe_'+k).checked = mode;
					change_style_groupe(k);
				}
			}
		}

		function change_style_groupe(num) {
			//if(document.getElementById('id_groupe_'+num)) {
			if((document.getElementById('id_groupe_'+num))&&(document.getElementById('label_groupe_'+num))) {
				if(document.getElementById('id_groupe_'+num).checked) {
					document.getElementById('label_groupe_'+num).style.fontWeight='bold';
				}
				else {
					document.getElementById('label_groupe_'+num).style.fontWeight='normal';
				}
			}
		}

	</script>\n";
	}
	else {
		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=appliquer_modele&amp;id_modele=$id_modele'>Retour au choix des classes</a>";
		//echo " | <a href='javascript: history.go(-1);'>Retour au choix des périodes</a>";
		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=appliquer_modele&amp;id_modele=$id_modele";
		for($i=0;$i<count($id_classe);$i++) {
			echo "&amp;id_classe[]=".$id_classe[$i];
		}
		echo "'>Retour au choix des périodes</a>";
		/*
		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=appliquer_modele&amp;id_modele=$id_modele&amp;=$choix_periodes";
		for($i=0;$i<count($id_classe);$i++) {
			echo "&amp;id_classe[]=".$id_classe[$i];
		}
		// Boucler sur la liste des périodes
		echo "'>Retour au choix des enseignements</a>";
		*/
		echo "
	</p>
</div>

<p><span class='bold'>Modèle $nom_court_modele</span><br />
<em>$description_modele</em></p>\n";

		echo "<p class='bold'>Vous allez maintenant créer les ".$gepi_denom_boite."s du modèle $nom_court_modele dans les enseignements sélectionnés&nbsp;:</p>\n";

		// Récapituler avant de valider

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>
	".add_token_field()."
	<input type='hidden' name='choix_periodes' value='$choix_periodes' />
	<input type='hidden' name='choix_matieres' value='$choix_matieres' />
	<input type='hidden' name='max_per' value='$max_per' />
	<input type='hidden' name='id_modele' value='$id_modele' />
	<input type='hidden' name='mode' value='appliquer_modele' />
	<input type='hidden' name='temoin_suhosin_1' value='1' />

	<table class='boireaus boireaus_alt'>
		<tr>
			<th>Classes</th>
			<th>Périodes</th>
			<th>Enseignements</th>
		</tr>";

		/*
		for($i=0;$i<count($id_classe);$i++) {
			echo "
		<tr>
			<td>
				<input type='hidden' name='id_classe[]' value='".$id_classe[$i]."' />".get_nom_classe($id_classe[$i])."
			</td>
			<td>";

			if($choix_periodes=='certaines') {
				if(isset($_POST['num_periode_'.$id_classe[$i]])) {
					$tmp_per=$_POST['num_periode_'.$id_classe[$i]];
					$temoin_periode=0;
					for($loop=0;$loop<$max_per;$loop++) {
						if(isset($tmp_per[$loop])) {
							if($temoin_periode>0) {echo ", ";}
							echo "
				<input type='hidden' name='num_periode_".$id_classe[$i]."[]' value='$tmp_per[$loop]' />P".$tmp_per[$loop];
							$temoin_periode++;
						}
					}
				}
			}
			else {
				echo "
				Toutes";
			}

			echo "
			</td>
			<td style='text-align:left;'>";


			if($choix_matieres=='certaines') {
				if(isset($_POST['id_groupe_'.$id_classe[$i]])) {
					$tmp_grp=$_POST['id_groupe_'.$id_classe[$i]];
					$temoin_grp=0;
					for($loop=0;$loop<$max_per;$loop++) {
						if(isset($tmp_grp[$loop])) {
							if($temoin_grp>0) {
								//echo ", ";
								echo "<br />";
							}
							echo "
				<input type='hidden' name='id_groupe_".$id_classe[$i]."[]' value='$tmp_grp[$loop]' />";

							$current_group=get_group($tmp_grp[$loop]);
							//echo $current_group['id']." ";
							echo $current_group['name']." (<em>".$current_group['description']."</em>) ".$current_group['profs']['proflist_string'];

							$temoin_grp++;
						}
					}
				}
			}
			else {
				echo "
				Tous";
			}

			echo "
			</td>
		</tr>";
		}
		*/


		for($i=0;$i<count($id_classe);$i++) {
			$tab_per=array();

			if($choix_periodes=='certaines') {
				if(isset($_POST['num_periode_'.$id_classe[$i]])) {
					$tmp_per=$_POST['num_periode_'.$id_classe[$i]];
					for($loop=0;$loop<$max_per;$loop++) {
						if(isset($tmp_per[$loop])) {
							$tab_per[$tmp_per[$loop]]="Période ".$tmp_per[$loop];

							$sql="SELECT * FROM periodes WHERE id_classe='".$id_classe[$i]."' AND num_periode='".$tmp_per[$loop]."';";
							$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_per)>0) {
								$lig_per=mysqli_fetch_object($res_per);
								$tab_per[$lig_per->num_periode]=$lig_per->nom_periode;
							}
						}
					}
				}
			}
			else {
				$sql="SELECT * FROM periodes WHERE id_classe='".$id_classe[$i]."' ORDER BY num_periode;";
				$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_per=mysqli_fetch_object($res_per)) {
					$tab_per[$lig_per->num_periode]=$lig_per->nom_periode;
				}
			}

			echo "
		<tr>
			<td rowspan='".count($tab_per)."'>
				<input type='hidden' name='id_classe[]' value='".$id_classe[$i]."' />".get_nom_classe($id_classe[$i])."
			</td>";

			$cpt_per=0;
			foreach($tab_per as $key => $value) {
				if($cpt_per>0) {
					echo "
		<tr>";
				}
				echo "
			<td><input type='hidden' name='num_periode_".$id_classe[$i]."[]' value='$key' />$value</td>
			<td style='text-align:left;'>";

				if($choix_matieres=='certaines') {
					if(isset($_POST['id_groupe_'.$id_classe[$i]])) {
						$tmp_grp=$_POST['id_groupe_'.$id_classe[$i]];
						$temoin_grp=0;

						for($loop=0;$loop<count($tmp_grp);$loop++) {
							if(isset($tmp_grp[$loop])) {
								if($temoin_grp>0) {
									//echo ", ";
									echo "<br />";
								}

								// Pour ne pas pas mettre autant de fois le groupe qu'il y a de période:
								if($cpt_per==0) {
									echo "
				<input type='hidden' name='id_groupe_".$id_classe[$i]."[]' value='$tmp_grp[$loop]' />";
								}

								$current_group=get_group($tmp_grp[$loop]);
								//echo $current_group['id']." ";
								echo $current_group['name']." (<em>".$current_group['description']."</em>) ".$current_group['profs']['proflist_string'];

								$temoin_grp++;
							}
						}
					}
				}
				else {
					echo "
					Tous";
				}

				echo "
			</td>
		</tr>";
				$cpt_per++;
			}
		}

		echo "
	</table>

	<input type='hidden' name='appliquer_le_modele' value='y' />
	<input type='submit' value='Valider' />
	<input type='hidden' name='temoin_suhosin_2' value='1' />
</form>\n";
		//debug_var();
	}
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Index de la création de modèles</a>
	</p>
</div>";

	echo "<p>Mode non implémenté.</p>";
}

/**
 * Pied de page
 */
require("../lib/footer.inc.php");
?>
