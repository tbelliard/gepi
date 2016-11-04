<?php
/*
*
* Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

header('Content-Type: text/html; charset=utf-8');

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//debug_var();

// Initialisation des variables utilisées dans le formulaire

$chemin_retour=isset($_GET['chemin_retour']) ? $_GET['chemin_retour'] : (isset($_POST['chemin_retour']) ? $_POST["chemin_retour"] : NULL);
$ancre=isset($_GET['ancre']) ? $_GET['ancre'] : (isset($_POST['ancre']) ? $_POST["ancre"] : NULL);

$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL);
$id_groupe = isset($_GET['id_groupe']) ? $_GET['id_groupe'] : (isset($_POST['id_groupe']) ? $_POST["id_groupe"] : NULL);

if (!is_numeric($id_groupe)) {$id_groupe = 0;}
$current_group = get_group($id_groupe);
if(!isset($current_group["name"])) {
	header("Location: ../accueil.php?msg=Le groupe n°$id_groupe n'existe pas.");
	die();
}
$reg_nom_groupe = $current_group["name"];
$reg_nom_complet = $current_group["description"];
$reg_matiere = $current_group["matiere"]["matiere"];
$reg_id_classe = $current_group["classes"]["list"][0];
$reg_clazz = $current_group["classes"]["list"];
$reg_professeurs = (array)$current_group["profs"]["list"];

//================================
$invisibilite_groupe=array();
$sql="SELECT jgv.* FROM j_groupes_visibilite jgv WHERE id_groupe='$id_groupe' AND jgv.visible='n';";
$res_jgv=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_jgv)>0) {
	while($lig_jgv=mysqli_fetch_object($res_jgv)) {
		$invisibilite_groupe[]=$lig_jgv->domaine;
	}
}
//================================

/*
foreach($reg_clazz as $key => $value) {
echo "\$reg_clazz[$key]=$value<br />";
}
*/

$mode = isset($_GET['mode']) ? $_GET['mode'] : (isset($_POST['mode']) ? $_POST["mode"] : null);
if ($mode == null and $id_classe == null) {
	$mode = "groupe";

	if ((isset($current_group["classes"]["list"]))&&(count($current_group["classes"]["list"]) > 1)) {
		$mode = "regroupement";
	}
} else if ($mode == null and $current_group) {
	if (count($current_group["classes"]["list"]) > 1) {
		$mode = "regroupement";
	} else {
		$mode = "groupe";
	}
}

foreach ($current_group["periodes"] as $period) {
	$reg_eleves[$period["num_periode"]] = $current_group["eleves"][$period["num_periode"]]["list"];
}

function afficher_liste_profs_du_groupe($reg_matiere) {
	global $current_group, $p, $prof_list, $mode, $themessage, $reg_professeurs, $id_classe;

	// Récupération du prof principal

	$tab_prof_suivi=array();
	$nb_prof_suivi=0;
	if(isset($id_classe)) {
		$tab_prof_suivi=get_tab_prof_suivi($id_classe);
		$nb_prof_suivi=count($tab_prof_suivi);
		if($nb_prof_suivi>1) {
			$liste_prof_suivi="";
			for($loop=0;$loop<count($tab_prof_suivi);$loop++) {
				if($loop>0) {$liste_prof_suivi.=", ";}
				$liste_prof_suivi.=civ_nom_prenom($tab_prof_suivi[$loop]);
			}
		}
		$gepi_prof_suivi=getParamClasse($id_classe, 'gepi_prof_suivi', getSettingValue('gepi_prof_suivi'));
	}
	else {
		$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');
	}

	$sql="SELECT u.login, u.nom, u.prenom, u.civilite, u.statut FROM utilisateurs u, j_professeurs_matieres j WHERE (j.id_matiere = '$reg_matiere' and j.id_professeur = u.login and u.etat!='inactif') ORDER BY u.nom;";
	//echo "$sql<br />";
	$calldata = mysqli_query($GLOBALS["mysqli"], $sql);
	$nb = mysqli_num_rows($calldata);
	$prof_list = array();
	$prof_list["list"] = array();
	for ($i=0;$i<$nb;$i++) {
		$prof_login = old_mysql_result($calldata, $i, "login");
		$prof_nom = old_mysql_result($calldata, $i, "nom");
		$prof_prenom = old_mysql_result($calldata, $i, "prenom");
		$civilite = old_mysql_result($calldata, $i, "civilite");
		$prof_statut = old_mysql_result($calldata, $i, "statut");

		$prof_list["list"][] = $prof_login;
		$prof_list["users"][$prof_login] = array("login" => $prof_login, "nom" => casse_mot($prof_nom,'maj'), "prenom" => casse_mot($prof_prenom,'majf2'), "civilite" => $civilite, "statut" => $prof_statut);
	}

	if (count($prof_list["list"]) == "0") {
		echo "<p><span style='color:red'>ERREUR !</span> Aucun professeur n'a été défini comme compétent dans la matière considérée.<br /><a href='../matieres/modify_matiere.php?current_matiere=$reg_matiere'>Associer des professeurs à $reg_matiere</a></p>\n";
	} else {
		$total_profs = array_merge($prof_list["list"], $reg_professeurs);
		$total_profs = array_unique($total_profs);

		$p = 0;
		echo "<table class='boireaus boireaus_alt'>\n";
		$temoin_nettoyage_requis='n';
		foreach($total_profs as $prof_login) {
			if((isset($prof_list["users"][$prof_login]["statut"]))&&($prof_list["users"][$prof_login]["statut"]=='professeur')) {
				echo "<tr>\n";
				echo "<td>\n";
				echo "<input type='hidden' name='proflogin_".$p."' value='".$prof_login."' />\n";
				echo "<input type='checkbox' name='prof_".$p."' id='prof_".$p."' ";
				echo "onchange='checkbox_change($p);changement();'";
				if (in_array($prof_login, $reg_professeurs)) {
					if (array_key_exists($prof_login, $current_group["profs"]["users"])){
						echo " checked />\n";
						echo "</td>\n";
						echo "<td style='text-align:left;'>\n";
						echo "<label id='civ_nom_prenom_prof_$p' for='prof_".$p."' style='cursor: pointer;'>". $current_group["profs"]["users"][$prof_login]["civilite"] . " " .
							$current_group["profs"]["users"][$prof_login]["nom"] . " " .
							$current_group["profs"]["users"][$prof_login]["prenom"] . "</label>\n";
					} else {
						echo " checked />\n";
						echo "</td>\n";
						echo "<td style='text-align:left;'>\n";
						echo "<label id='civ_nom_prenom_prof_$p' for='prof_".$p."' style='cursor: pointer;'>". $prof_list["users"][$prof_login]["civilite"] . " " .
							$prof_list["users"][$prof_login]["nom"] . " " .
							$prof_list["users"][$prof_login]["prenom"] . "</label>\n";
					}
				} else {
					echo " />\n";
					echo "</td>\n";
					echo "<td style='text-align:left;'>\n";
					echo "<label id='civ_nom_prenom_prof_$p' for='prof_".$p."' style='cursor: pointer;'>". $prof_list["users"][$prof_login]["civilite"] . " " .
							$prof_list["users"][$prof_login]["nom"] . " " .
							$prof_list["users"][$prof_login]["prenom"] . "</label>";
				}

				if(in_array($prof_login,$tab_prof_suivi)) {
					echo " <img src='../images/bulle_verte.png' width='9' height='9' title=\"".ucfirst($gepi_prof_suivi)." d'au moins un élève de la classe sur une des périodes.";
					if($nb_prof_suivi>1) {echo " La liste des ".$gepi_prof_suivi." est ".$liste_prof_suivi.".";}
					echo "\" />\n";
				}

				echo "<br />\n";

				echo "</td>\n";
				echo "</tr>\n";
				$p++;
			}
			else {
				echo "<tr>\n";
				echo "<td>\n";
				echo "&nbsp;&nbsp;";
				echo "</td>\n";
				echo "<td style='text-align:left;' title=\"Anomalie : Cet utilisateur est associé au groupe, mais n'est pas professeur, ou pas professeur dans cette matière.\">\n";
				echo "<b>ANOMALIE</b>&nbsp;:";
				//echo " " . $prof_list["users"][$prof_login]["nom"] . " " . $prof_list["users"][$prof_login]["prenom"];
				echo " <a href='../utilisateurs/modify_user.php?user_login=$prof_login'  onclick=\"return confirm_abandon (this, change, '$themessage')\">".civ_nom_prenom($prof_login)."</a>";
				if(isset($prof_list["users"][$prof_login]["statut"])) {
					echo " (<i style='color:red'>compte ".$prof_list["users"][$prof_login]["statut"]."</i>)";
				}
				echo "<br />\n";
				$temoin_nettoyage_requis='y';
				//echo "Un <a href='../utilitaires/clean_tables.php'>nettoyage des tables</a> s'impose.";
				echo "</td>\n";
				echo "</tr>\n";
			}
		}
		echo "</table>\n";
		if($temoin_nettoyage_requis!='n') {
			echo "Un <a href='../utilitaires/clean_tables.php'>nettoyage des tables</a> s'impose.";
		}

		echo "<br />
<a href='#' onclick=\"cocher_tous_profs_de_la_matiere(true);return false;\">Cocher</a> / <a href='#' onclick=\"cocher_tous_profs_de_la_matiere(false);return false;\">décocher</a> tous les professeurs ci-dessus.
<script type='text/javascript'>
	function cocher_tous_profs_de_la_matiere(mode) {
		for(i=0;i<$p;i++) {
			if(document.getElementById('prof_'+i)) {
				document.getElementById('prof_'+i).checked=mode;
				checkbox_change(i);
				changement();
			}
		}
	}
</script>";

		if ($mode == "groupe") {
			echo "<br />
	<input type='checkbox' name='associer_pp_de_la_classe' id='associer_pp_de_la_classe' value='y' onchange=\"checkbox_change_divers(this.id)\" /><label for='associer_pp_de_la_classe' id='texte_associer_pp_de_la_classe'> Associer à cet enseignement le ou les ".$gepi_prof_suivi." de la classe.</label><br />
	<input type='checkbox' name='associer_tous_les_profs_de_la_classe' id='associer_tous_les_profs_de_la_classe' value='y' onchange=\"checkbox_change_divers(this.id)\" /><label for='associer_tous_les_profs_de_la_classe' id='texte_associer_tous_les_profs_de_la_classe'> Associer à cet enseignement tous les professeurs de la classe.</label><br />
	<input type='checkbox' name='associer_tous_les_profs_de_l_etablissement' id='associer_tous_les_profs_de_l_etablissement' value='y' onchange=\"checkbox_change_divers(this.id)\" /><label for='associer_tous_les_profs_de_l_etablissement' id='texte_associer_tous_les_profs_de_l_etablissement'> Associer à cet enseignement tous les professeurs de l'établissement.</label><br />
	";
		}
		else {
			echo "<br />
	<input type='checkbox' name='associer_pp_de_la_classe' id='associer_pp_de_la_classe' value='y' onchange=\"checkbox_change_divers(this.id)\" /><label for='associer_pp_de_la_classe' id='texte_associer_pp_de_la_classe'> Associer à cet enseignement le ou les ".$gepi_prof_suivi." de la (<em>ou des</em>) classe(<em>s</em>).</label><br />
	<input type='checkbox' name='associer_tous_les_profs_de_la_classe' id='associer_tous_les_profs_de_la_classe' value='y' onchange=\"checkbox_change_divers(this.id)\" /><label for='associer_tous_les_profs_de_la_classe' id='texte_associer_tous_les_profs_de_la_classe'> Associer à cet enseignement tous les professeurs de la (<em>ou des</em>) classe(<em>s</em>).</label><br />
	<input type='checkbox' name='associer_tous_les_profs_de_l_etablissement' id='associer_tous_les_profs_de_l_etablissement' value='y' onchange=\"checkbox_change_divers(this.id)\" /><label for='associer_tous_les_profs_de_l_etablissement' id='texte_associer_tous_les_profs_de_l_etablissement'> Associer à cet enseignement tous les professeurs de l'établissement.</label><br />
	";
		}
	}
}

if((isset($_GET['maj_liste_profs_matiere']))&&(isset($_GET['matiere']))) {
	check_token(false);

	$matiere=$_GET['matiere'];
	afficher_liste_profs_du_groupe($matiere);

	die();
}

if((isset($_GET['maj_liste_autres_groupes_meme_matiere']))&&(isset($_GET['matiere']))&&(isset($_GET['id_classe']))) {
	check_token(false);

	$matiere=$_GET['matiere'];
	$id_classe=$_GET['id_classe'];

	$avec_lien_edit_group="y";
	$tab_autres_groupes=tableau_html_groupe_matiere_telle_classe($id_classe, $matiere, array($id_groupe));
	if($tab_autres_groupes!="") {
		echo "<p>Il existe d'autres enseignements dans la même matière pour cette classe&nbsp;:</p>\n";
		echo $tab_autres_groupes;

		echo "<p><a href='repartition_ele_grp.php?id_classe[]=$id_classe&amp;preselect_id_groupe=$id_groupe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Répartir les élèves entre plusieurs groupes</a></p>";
	}
	else {
		echo "";
	}
	die();
}

if (isset($_POST['is_posted'])) {
	check_token();

	$msg="";
	$error = false;
	//=======================================
	// MODIF: boireaus
	/*
	$reg_nom_groupe = $_POST['groupe_nom_court'];
	$reg_nom_complet = $_POST['groupe_nom_complet'];
	*/
	//$reg_nom_groupe = html_entity_decode($_POST['groupe_nom_court']);
	$reg_nom_groupe = html_entity_decode($_POST['groupe_nom_court'],ENT_QUOTES,"UTF-8");
	//$reg_nom_complet = html_entity_decode($_POST['groupe_nom_complet']);
	$reg_nom_complet = html_entity_decode($_POST['groupe_nom_complet'],ENT_QUOTES,"UTF-8");
	//=======================================
	$reg_matiere = $_POST['matiere'];

	if (empty($reg_nom_groupe)) {
		$error = true;
		$msg .= "Vous devez donner un nom court au groupe.<br />\n";
	}

	if (empty($reg_nom_groupe)) {
		$error = true;
		$msg .= "Vous devez donner un nom complet au groupe.<br />\n";
	}

	$clazz = array();

	// Classes

	if ($_POST['mode'] == "groupe") {
		// Ajout sécurité:
		if((!isset($id_classe))||($id_classe=='')) {$id_classe=$current_group['classes']['list'][0];}

		$clazz[] = $id_classe;
		$reg_id_classe = $id_classe;
		$mode = "groupe";
	} else if ($_POST['mode'] == "regroupement") {
		$mode = "regroupement";
		foreach ($_POST as $key => $value) {
			if (preg_match("/^classe\_/", $key)) {
				$temp = explode("_", $key);
				$id = $temp[1];
				$clazz[] = $id;
			}
		}



		foreach ($_POST as $key => $value) {
			if (preg_match("/^precclasse\_/", $key)) {
				$temp = explode("_", $key);
				$tmpid = $temp[1];
				// On vérifie si la classe a été décochée:
				if(!isset($_POST['classe_'.$tmpid])){
					// On vérifie si l'identifiant de classe $tmpid peut être décoché.
		
					unset($tabtmp);
					$tabtmp=array();
					$test=0;
					$test2=0;
					$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$tmpid'";
					$res_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
					while($lig_tmp=mysqli_fetch_object($res_tmp)){
						$sql="SELECT 1=1 FROM matieres_notes WHERE id_groupe='$id_groupe' AND login='$lig_tmp->login'";
						//echo "$sql<br />\n";
						$res_test=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_test)>0){
							//echo "$lig_tmp->login<br />\n";
							if(!in_array($lig_tmp->login,$tabtmp)){$tabtmp[]=$lig_tmp->login;}
							$test++;
						}
						$sql="SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='$id_groupe' AND login='$lig_tmp->login'";
						//echo "$sql<br />\n";
						$res_test=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_test)>0){
							//echo "$lig_tmp->login<br />\n";
							if(!in_array($lig_tmp->login,$tabtmp)){$tabtmp[]=$lig_tmp->login;}
							$test2++;
						}
					}
		
					$sql="SELECT classe FROM classes WHERE id='$tmpid'";
					$res_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
					$lig_tmp=mysqli_fetch_object($res_tmp);
					$clas_tmp=$lig_tmp->classe;
		
					//if(!$verify){
					if(($test>0)||($test2>0)){
						/*
						$sql="SELECT classe FROM classes WHERE id='$tmpid'";
						$res_tmp=mysql_query($sql);
						$lig_tmp=mysql_fetch_object($res_tmp);
						$clas_tmp=$lig_tmp->classe;
						*/
		
						$error = true;
						$msg .= "Des données existantes bloquent la suppression de la classe $clas_tmp du groupe.<br />\nAucune note ni appréciation du bulletin ne doit avoir été saisie pour les élèves de ce groupe pour permettre la suppression du groupe.<br />\n";
						if(count($tabtmp)==1){
							$msg.="L'élève ayant des moyennes ou appréciations saisies est $tabtmp[0].<br />\n";
						}
						else{
							$msg.="Les élèves ayant des moyennes ou appréciations saisies sont $tabtmp[0]";
							for($i=1;$i<count($tabtmp);$i++){
								$msg.=", $tabtmp[$i]";
							}
							$msg.=".<br />\n";
						}
						// Et on remet la classe dans la liste des classes:
								$clazz[] = $tmpid;
					}
					else{
						// On teste aussi si il y a des élèves de la classe dans le groupe.
						$sql="SELECT jeg.login FROM j_eleves_groupes jeg, j_eleves_classes jec WHERE
									jeg.login=jec.login AND
									jeg.periode=jec.periode AND
									jeg.id_groupe='$id_groupe' AND
									jec.id_classe='$tmpid'";
						//echo "$sql<br />\n";
						$res_ele_clas_grp=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_ele_clas_grp)>0){
							$error = true;
							$msg .= "Des données existantes bloquent la suppression de la classe $clas_tmp du groupe.<br />\nAucun élève de la classe ne doit être inscrit dans le groupe.<br />\n<a href='edit_eleves.php?id_groupe=$id_groupe&id_classe=$tmpid'>Enlevez les élèves du groupe</a> avant.<br />\n";
							// Et on remet la classe dans la liste des classes:
							$clazz[] = $tmpid;
						}
					}
				}
			}
		}


	}

	// On ajoute un test pour s'assurer qu'on n'a pas un tableau vide pour les classes
	if (count($clazz) == 0) {
		$clazz[0] = $id_classe;
	}


	for($loo=0;$loo<count($tab_domaines);$loo++) {
		$visibilite_groupe_domaine_courant=isset($_POST['visibilite_groupe_'.$tab_domaines[$loo]]) ? $_POST['visibilite_groupe_'.$tab_domaines[$loo]] : "n";

		if(in_array($tab_domaines[$loo], $invisibilite_groupe)) {
			if($visibilite_groupe_domaine_courant!='n') {
				$sql="DELETE FROM j_groupes_visibilite WHERE id_groupe='".$id_groupe."' AND domaine='".$tab_domaines[$loo]."';";
				//echo "$sql<br />";
				$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$suppr) {$msg.="Erreur lors de la suppression de l'invisibilité du groupe n°".$id_groupe." sur les ".$tab_domaines_texte[$loo].".<br />";}
			}
		}
		else {
			if($visibilite_groupe_domaine_courant=='n') {
				$sql="INSERT j_groupes_visibilite SET id_groupe='".$id_groupe."', domaine='".$tab_domaines[$loo]."', visible='n';";
				//echo "$sql<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {$msg.="Erreur lors de l'enregistrement de l'invisibilité du groupe n°".$id_groupe." sur les ".$tab_domaines_texte[$loo].".<br />";}
			}
		}
	}


	// Professeurs
	$reg_professeurs = array();
	foreach ($_POST as $key => $value) {
		if (preg_match("/^prof\_/", $key)) {
			$id = preg_replace("/^prof\_/", "", $key);
			$proflogin = $_POST["proflogin_".$id];
			// Normalement on a un traitement anti-injection sur $_POST, donc pas de soucis.
			// Mais ça serait bien de faire un test quand même. Si un dev passe par là...
			//$reg_professeurs[] = $proflogin;

			$sql="SELECT 1=1 FROM j_professeurs_matieres WHERE id_professeur='$proflogin' AND id_matiere='$reg_matiere';";
			$test_prof_matiere=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test_prof_matiere)>0) {
				$reg_professeurs[] = $proflogin;
			}
		}
	}

	$reg_clazz = $clazz;


	if(isset($_POST['associer_tous_les_profs_de_la_classe'])) {
		for($loo=0;$loo<count($clazz);$loo++) {
			$sql="SELECT DISTINCT u.login FROM utilisateurs u, 
										j_groupes_professeurs jgp, 
										j_groupes_classes jgc 
								WHERE u.statut='professeur' AND 
									u.etat='actif' AND
									u.login=jgp.login AND
									jgp.id_groupe=jgc.id_groupe AND
									jgc.id_classe='".$clazz[$loo]."';";
			$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_prof)>0) {
				while($lig_prof=mysqli_fetch_object($res_prof)) {
					if(!in_array($lig_prof->login, $reg_professeurs)) {
						$reg_professeurs[]=$lig_prof->login;
					}
				}
			}
		}
	}

	if(isset($_POST['associer_tous_les_profs_de_l_etablissement'])) {
		$sql="SELECT login FROM utilisateurs WHERE statut='professeur' AND etat='actif';";
		$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_prof)>0) {
			while($lig_prof=mysqli_fetch_object($res_prof)) {
				if(!in_array($lig_prof->login, $reg_professeurs)) {
					$reg_professeurs[]=$lig_prof->login;
				}
			}
		}
	}

	if(isset($_POST['associer_pp_de_la_classe'])) {
		for($loo=0;$loo<count($clazz);$loo++) {
			$sql="SELECT DISTINCT u.login FROM utilisateurs u, 
										j_eleves_professeurs jep, 
										j_eleves_classes jec 
								WHERE u.statut='professeur' AND 
									u.etat='actif' AND
									u.login=jep.professeur AND
									jep.login=jec.login AND
									jec.id_classe='".$clazz[$loo]."';";
			$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_prof)>0) {
				while($lig_prof=mysqli_fetch_object($res_prof)) {
					if(!in_array($lig_prof->login, $reg_professeurs)) {
						$reg_professeurs[]=$lig_prof->login;
					}
				}
			}
		}
	}

	$tab_profs_matiere=array();
	$sql="SELECT DISTINCT id_professeur FROM j_professeurs_matieres WHERE id_matiere='$reg_matiere';";
	$res_prof_matiere=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_prof_matiere)>0){
		while($lig_prof_matiere=mysqli_fetch_object($res_prof_matiere)){
			$tab_profs_matiere[]=$lig_prof_matiere->id_professeur;
		}
	}

	// On vérifie que les profs de la liste sont bien associés à la matière:
	for($loo=0;$loo<count($reg_professeurs);$loo++) {
		if(!in_array($reg_professeurs[$loo], $tab_profs_matiere)) {
			$sql="SELECT MAX(ordre_matieres) AS max_ordre_matiere FROM j_professeurs_matieres WHERE id_professeur='".$reg_professeurs[$loo]."';";
			//echo "$sql<br />";
			$res_ordre=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_ordre)==0) {
				$ordre_matiere=1;
			}
			else {
				$ordre_matiere=old_mysql_result($res_ordre, 0, "max_ordre_matiere")+1;
			}

			$sql="INSERT INTO j_professeurs_matieres SET id_professeur='".$reg_professeurs[$loo]."', id_matiere='$reg_matiere', ordre_matieres='$ordre_matiere';";
			//echo "$sql<br />";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
		}
	}

	// 20160419
	//$code_modalite_elect=isset($_POST['code_modalite_elect']) ? $_POST['code_modalite_elect'] : array();
	$code_modalite_elect_eleves=$current_group["modalites"];

	/*
	echo "Apres modif:<br />";
	foreach($reg_clazz as $key => $value) {
		echo "\$reg_clazz[$key]=$value<br />";
	}
	*/

	if (empty($reg_clazz)) {
		$error = true;
		$msg .= "Vous devez sélectionner au moins une classe.<br />\n";
	}

	if (!$error) {
		// pas d'erreur : on continue avec la mise à jour du groupe
		//$create = update_group($id_groupe, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves);
		$create = update_group($id_groupe, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves, $code_modalite_elect_eleves);
		if (!$create) {
			$msg .= "Erreur lors de la mise à jour du groupe.";
		} else {

			//======================================
			// MODIF: boireaus
			//$msg = "Le groupe a bien été mis à jour.";
			$msg = "Enseignement ". stripslashes($reg_nom_complet) . " bien mis à jour.<br />";

			if(isset($_POST['creer_sous_groupes'])) {
				if((!isset($_POST['nb_sous_groupes_a_creer']))||($_POST['nb_sous_groupes_a_creer']=='')||(!preg_match("/^[0-9]*$/", $_POST['nb_sous_groupes_a_creer']))||($_POST['nb_sous_groupes_a_creer']<1)) {
					$msg.="Erreur : Le nombre de sous-groupes demandés est invalide.<br />";
				}
				else {
					//20130912 $reg_categorie à récupérer...
					$current_group=get_group($id_groupe);
					$reg_categorie=$current_group["classes"]["classes"][$reg_clazz[0]]["categorie_id"];

					$nb_sous_groupes_a_creer=$_POST['nb_sous_groupes_a_creer'];

					$suffixe_sous_groupe_a_creer=isset($_POST['suffixe_sous_groupe_a_creer']) ? $_POST['suffixe_sous_groupe_a_creer'] : "";

					for($loop=0;$loop<$nb_sous_groupes_a_creer;$loop++) {
						$reg_nom_sous_groupe=$reg_nom_groupe;
						$reg_nom_complet_sous_groupe=$reg_nom_complet;

						if($suffixe_sous_groupe_a_creer=="1") {
							$reg_nom_sous_groupe.="_".($loop+1);
							$reg_nom_complet_sous_groupe=$reg_nom_complet." (groupe ".($loop+1).")";
						}
						elseif($suffixe_sous_groupe_a_creer=="g1") {
							$reg_nom_sous_groupe.="_g".($loop+1);
							$reg_nom_complet_sous_groupe=$reg_nom_complet." (groupe ".($loop+1).")";
						}
						elseif($suffixe_sous_groupe_a_creer=="A") {
							$alphabet="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
							$reg_nom_sous_groupe.="_".substr($alphabet, $loop, 1);
							$reg_nom_complet_sous_groupe=$reg_nom_complet." (groupe ".substr($alphabet, $loop, 1).")";
						}

						$create = create_group($reg_nom_sous_groupe, $reg_nom_complet_sous_groupe, $reg_matiere, $reg_clazz, $reg_categorie);
						if (!$create) {
							$msg .= "Erreur lors de la création du sous-groupe $reg_nom_sous_groupe.<br />";
						} else {
							// Puis mise à jour avec la liste des élèves, la visibilité
							$id_sous_groupe=$create;

							// Visibilité du sous-groupe
							for($loo=0;$loo<count($tab_domaines);$loo++) {
								$visibilite_groupe_domaine_courant=isset($_POST['visibilite_nouveaux_sous_groupes_'.$tab_domaines[$loo]]) ? $_POST['visibilite_nouveaux_sous_groupes_'.$tab_domaines[$loo]] : "n";

								if(in_array($tab_domaines[$loo], $invisibilite_groupe)) {
									if($visibilite_groupe_domaine_courant!='n') {
										$sql="DELETE FROM j_groupes_visibilite WHERE id_groupe='".$id_sous_groupe."' AND domaine='".$tab_domaines[$loo]."';";
										//echo "$sql<br />";
										$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$suppr) {$msg.="Erreur lors de la suppression de l'invisibilité du groupe n°".$id_sous_groupe." sur les ".$tab_domaines_texte[$loo].".<br />";}
									}
								}
								else {
									if($visibilite_groupe_domaine_courant=='n') {
										$sql="INSERT j_groupes_visibilite SET id_groupe='".$id_sous_groupe."', domaine='".$tab_domaines[$loo]."', visible='n';";
										//echo "$sql<br />";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$insert) {$msg.="Erreur lors de l'enregistrement de l'invisibilité du groupe n°".$id_sous_groupe." sur les ".$tab_domaines_texte[$loo].".<br />";}
									}
								}
							}

							// Elèves du sous-groupe
							$reg_eleves_sous_groupe=array();
							if($nb_sous_groupes_a_creer==1) {
								$reg_eleves_sous_groupe=$reg_eleves;
							}
							else {
								foreach ($current_group["periodes"] as $period) {
									$reg_eleves_sous_groupe[$period["num_periode"]]=array();

									$nb_ele_restants=count($current_group["eleves"][$period["num_periode"]]["list"]);
									$nb_sous_groupes_restants=$nb_sous_groupes_a_creer;
									$rang_prec=0;
									$rang[-1]=0;
									for($loop_grp=0;$loop_grp<$nb_sous_groupes_a_creer;$loop_grp++) {
										$tranche[$loop_grp]=ceil($nb_ele_restants/$nb_sous_groupes_restants);

										$rang[$loop_grp]=$rang_prec+$tranche[$loop_grp];

										$nb_ele_restants-=$tranche[$loop_grp];
										$nb_sous_groupes_restants--;
										$rang_prec=$rang[$loop_grp];
									}

									for($loop_ele=$rang[$loop-1];$loop_ele<$rang[$loop];$loop_ele++) {
											$reg_eleves_sous_groupe[$period["num_periode"]][]=$current_group["eleves"][$period["num_periode"]]["list"][$loop_ele];
									}

									/*
									for($loop_ele=0;$loop_ele<count($current_group["eleves"][$period["num_periode"]]["list"]);$loop_ele++) {

										$tranche=floor(count($current_group["eleves"][$period["num_periode"]]["list"])/$nb_sous_groupes_a_creer);

										if(($loop_ele+1>=$loop*$tranche+1)&&
										($loop_ele+1<($loop+1)*$tranche+1)) {
											$reg_eleves_sous_groupe[$period["num_periode"]][]=$current_group["eleves"][$period["num_periode"]]["list"][$loop_ele];
										}
									}
									*/
								}
							}

							$update = update_group($id_sous_groupe, $reg_nom_sous_groupe, $reg_nom_complet_sous_groupe, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves_sous_groupe);
							if (!$update) {
								$msg .= "Erreur lors de la mise à jour du sous-groupe ".$reg_nom_sous_groupe.".<br />";
							}

						}
					}

				}
			}

			$msg = urlencode($msg);

			if(isset($chemin_retour)) {
				if(strstr($chemin_retour,'utilisateurs/index.php')) {
					// On n'arrive sur edit_group.php en venant de utilisateurs/index.php que depuis la partie Gestion de comptes utilisateurs Personnels de l'établissement
					if(isset($ancre)) {
						header("Location: $chemin_retour?&msg=$msg&mode=personnels#$ancre");
					}
					else {
						header("Location: $chemin_retour?&msg=$msg&mode=personnels");
					}
				}
				else {
					header("Location: $chemin_retour?&msg=$msg");
				}
			}
			else{
				header("Location: ./edit_class.php?id_classe=$id_classe&msg=$msg");
			}
			//======================================
		}
		$current_group = get_group($id_groupe);
	}
}
/* DEBUG
echo "<pre>\n";
print_r($_POST);
echo "</pre>\n";
echo html_entity_decode("prof_ERIC_ALARY");

echo "<pre>\n";
print_r($current_group);
echo "</pre>\n";

echo "<pre>\n";
print_r($reg_professeurs);
echo "</pre>\n";
*/

// Ajout sécurité:
if((!isset($id_classe))||($id_classe=='')) {$id_classe=$current_group['classes']['list'][0];}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
$titre_page = "Gestion des groupes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

//debug_var();

//echo "\$_SERVER['HTTP_REFERER']=".$_SERVER['HTTP_REFERER']."<br />\n";

/*
foreach ($reg_clazz as $tmp_classe) {
	echo "\$tmp_classe=$tmp_classe<br />\n";
}
*/
?>
<p class="bold">
<?php
//============================
// MODIF: boireaus
//if(isset($_GET['chemin_retour'])){
if(isset($chemin_retour)){
	echo "<a href=\"".$_GET['chemin_retour']."\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | ";
}
else{
	echo "<a href=\"edit_class.php?id_classe=$id_classe\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | ";
}
//============================

echo "<a href='mes_listes.php?id_groupe=$id_groupe'>Exporter la composition du groupe</a> | ";
?>
<a href="edit_class.php?id_classe=<?php echo $id_classe;?>&amp;action=delete_group&amp;id_groupe=<?php echo $id_groupe;?><?php echo add_token_in_url();?>" onclick="return confirmlink(this, 'ATTENTION !!! LISEZ CET AVERTISSEMENT : La suppression d\'un enseignement est irréversible. Une telle suppression ne devrait pas avoir lieu en cours d\'année. Si c\'est le cas, cela peut entraîner la présence de données orphelines dans la base. Si des données officielles (notes et appréciations du bulletin) sont présentes, la suppression sera bloquée. Dans le cas contraire, toutes les données liées au groupe seront supprimées, incluant les notes saisies par les professeurs dans le carnet de notes ainsi que les données présentes dans le cahier de texte. Etes-vous *VRAIMENT SÛR* de vouloir continuer ?', 'Confirmation de la suppression')"> Supprimer le groupe</a>
<?php
echo " | <a href='edit_eleves.php?id_groupe=$id_groupe&id_classe=".$id_classe."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Modifier la liste des élèves</a>";

echo " | <a href='repartition_ele_grp.php'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo " title=\"Répartir des élèves entre plusieurs groupes\">Répartir</a> ";

/*
if(count($current_group["classes"]["list"])==1) {
	echo " | <a href='edit_eleves.php?id_groupe=$id_groupe&id_classe=".$current_group["classes"]["list"][0]."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Modifier la liste des élèves</a>";
}
elseif(count($current_group["classes"]["list"])>1) {
	echo " | Modifier la liste des élèves en";
	$cpt_classe=0;
	foreach($current_group["classes"]["classes"] as $key => $value) {
		if($cpt_classe>0) {echo ", ";}
		echo " <a href='edit_eleves.php?id_groupe=$id_groupe&id_classe=$key' onclick=\"return confirm_abandon (this, change, '$themessage')\">".$value['classe']."</a>";
		$cpt_classe++;
	}
}
*/

if ($mode == "groupe") {
	echo "<h3>Modifier le groupe</h3>\n";
} elseif ($mode == "regroupement") {
	echo "<h3>Modifier le regroupement</h3>\n";
}

$message_nom_sur_bulletin1="";
$message_nom_sur_bulletin2="";
$message_nom_sur_bulletin3="";
$choix_nom_sur_bulletin1="";
$choix_nom_sur_bulletin2="";
$choix_nom_sur_bulletin3="";
if(getSettingValue('bul_rel_nom_matieres')=='nom_complet_matiere') {
	$choix_nom_sur_bulletin3="&nbsp;<img src='../images/info.png' width='20' height='20' title=\"C'est votre choix.\" />";
	$message_nom_sur_bulletin3="&nbsp;<img src='../images/info.png' width='20' height='20' title=\"C'est le nom complet de la matière qui apparait sur les bulletins.
C'est le paramétrage que vous avez effectué dans Gestion générale/Configuration générale.
Ce paramétrage est global, commun à toutes les classes.\" />";
}
elseif(getSettingValue('bul_rel_nom_matieres')=='nom_groupe') {
	$choix_nom_sur_bulletin1="&nbsp;<img src='../images/info.png' width='20' height='20' title=\"C'est votre choix.\" />";
	$message_nom_sur_bulletin1="&nbsp;<img src='../images/info.png' width='20' height='20' title=\"C'est le nom court de l'enseignement ('groupe' dans le vocabulaire Gepi) qui apparait sur les bulletins.
C'est le paramétrage que vous avez effectué dans Gestion générale/Configuration générale.
Ce paramétrage est global, commun à toutes les classes.\" />";
}
elseif(getSettingValue('bul_rel_nom_matieres')=='description_groupe') {
	$choix_nom_sur_bulletin2="&nbsp;<img src='../images/info.png' width='20' height='20' title=\"C'est votre choix.\" />";
	$message_nom_sur_bulletin2="&nbsp;<img src='../images/info.png' width='20' height='20' title=\"C'est la description de l'enseignement ('groupe' dans le vocabulaire Gepi) qui apparait sur les bulletins.
C'est le paramétrage que vous avez effectué dans Gestion générale/Configuration générale.
Ce paramétrage est global, commun à toutes les classes.\" />";
}

?>
<form enctype="multipart/form-data" action="edit_group.php" method="post">
<fieldset style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>
<div style="width: 95%;">
<div style="width: 45%; float: left;">
<p>Nom court : <input type='text' size='30' name='groupe_nom_court' id='groupe_nom_court' value = "<?php echo $reg_nom_groupe; ?>" onchange="changement()" /><?php
	echo $message_nom_sur_bulletin1;

	$titre_infobulle="Ajouter un suffixe au nom de l'enseignement";
	$texte_infobulle="<div align='center' style='padding:3px;'>".html_ajout_suffixe_ou_renommer('groupe_nom_court', 'groupe_nom_complet', 'matiere')."</div>";
	$tabdiv_infobulle[]=creer_div_infobulle('suffixe_nom_grp',$titre_infobulle,"",$texte_infobulle,"",30,0,'y','y','n','n');
	echo " <a href=\"javascript:afficher_div('suffixe_nom_grp','y',-100,20)\"><img src='../images/icons/wizard.png' width='16' height='16' alt='Suffixe' title=\"Ajouter un suffixe ou renommer l'enseignement.\" /></a>";

?></p>

<p>Nom complet : <input type='text' size='50' name='groupe_nom_complet' id='groupe_nom_complet' value = "<?php echo $reg_nom_complet; ?>" onchange="changement()" /><?php echo $message_nom_sur_bulletin2;?></p>

<?php

echo add_token_field();

// Classes

if ($mode == "groupe") {
	echo "<p>\n";
	if((isset($current_group))&&(count($current_group["eleves"]["all"]["list"])==0)) {
		echo "Sélectionnez la classe à laquelle appartient le groupe :\n";
		echo "<select name='id_classe' size='1'";
		echo " onchange='changement();'";
		echo ">\n";
	
		$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes ORDER BY classe");
		$nombre_lignes = mysqli_num_rows($call_data);
		if ($nombre_lignes != 0) {
			$i = 0;
			while ($i < $nombre_lignes){
				$id_classe2 = old_mysql_result($call_data, $i, "id");
				$classe = old_mysql_result($call_data, $i, "classe");
				if (get_period_number($id_classe2) != "0") {
					echo "<option value='" . $id_classe2 . "'";
					if (in_array($id_classe2, $reg_clazz)) echo " SELECTED";
					echo ">$classe</option>\n";
				}
			$i++;
			}
		} else {
			echo "<option value='false'>Aucune classe définie !</option>\n";
		}
		echo "</select>\n";
		//echo "<br />[-> <a href='edit_group.php?id_classe=".$id_classe."&id_groupe=".$id_groupe."&mode=regroupement'>sélectionner plusieurs classes</a>]</p>\n";
		echo "<br />\n";
	}
	else {
		echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
		if(isset($current_group)) {
			echo "Enseignement en <b>".$current_group['classlist_string']."</b>.";
			echo "<br />\n";
		}
	}

	echo "[-> <a href='edit_group.php?id_classe=".$id_classe."&amp;id_groupe=".$id_groupe."&amp;mode=regroupement'>sélectionner plusieurs classes</a>]\n";

	// On ne propose de fusionner le groupe avec un/des groupes existants que si le groupe n'a pas déjà de notes,...
	// ... NON: On fera le test sur les groupes à y associer seulement.
	//          Ce sont les autres groupes qui seraient susceptibles de voir leurs notes disparaitre
	echo "<br />[-> <a href='fusion_group.php?id_classe=".$id_classe."&amp;id_groupe=".$id_groupe."'>fusionner le groupe avec un ou des groupes existants</a>]";

	// AJOUTER UN TEST: sur le fait que le groupe est vide...
	//echo "<br />[-> <a href='scinder_group.php?id_classe=".$id_classe."&amp;id_groupe=".$id_groupe."'>scinder le groupe</a>]";

	echo "</p>\n";

} else if ($mode == "regroupement") {
	echo "<input type='hidden' name='id_classe' value='".$id_classe."' />\n";
	echo "<p>Sélectionnez les classes auxquelles appartient le regroupement :";

	//$call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
	//$sql="SELECT * FROM classes c, periodes p WHERE p.id_classe=c.id AND MAX(p.num_periode)='".get_period_number($id_classe)."' ORDER BY classe;";
	$sql="SELECT * FROM classes ORDER BY classe;";
	//echo "$sql<br />";
	$call_data = mysqli_query($GLOBALS["mysqli"], $sql);
	$nombre_lignes = mysqli_num_rows($call_data);
	if ($nombre_lignes != 0) {

		$i = 0;

		$tmp_tab_classe=array();
		$tmp_tab_id_classe=array();
		while ($i < $nombre_lignes){
			$id_classe_temp=old_mysql_result($call_data, $i, "id");
			$classe=old_mysql_result($call_data, $i, "classe");
			if (get_period_number($id_classe_temp) == get_period_number($id_classe)) {
				$tmp_tab_classe[]=$classe;
				$tmp_tab_id_classe[]=$id_classe_temp;
			}
			$i++;
		}

		echo "<table width='100%'>\n";
		echo "<tr valign='top' align='left'>\n";
		echo "<td>\n";
		//$nb_class_par_colonne=round($nombre_lignes/3);
		$nb_class_par_colonne=round(count($tmp_tab_classe)/3);
		for($i=0;$i<count($tmp_tab_classe);$i++) {
			if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
				echo "</td>\n";
				echo "<td>\n";
			}

			$id_classe_temp=$tmp_tab_id_classe[$i];
			$classe=$tmp_tab_classe[$i];

			//echo "<br /><input type='checkbox' name='classe_" . $id_classe_temp . "' value='yes'";
			echo "<input type='checkbox' name='classe_" . $id_classe_temp . "' id='classe_" . $id_classe_temp . "' value='yes'";
			if (in_array($id_classe_temp, $reg_clazz)){
				echo " checked";
			}
			//echo " />$classe</option>\n";
			echo " onchange=\"checkbox_change_classe('classe_".$id_classe_temp."'); changement();\"";
			echo " /><label for='classe_".$id_classe_temp."' id='texte_classe_".$id_classe_temp."' style='cursor: pointer;";
			if (in_array($id_classe_temp, $reg_clazz)){
				echo " font-weight:bold;";
			}
			echo "'>$classe</label>\n";
			if (in_array($id_classe_temp, $reg_clazz)){
				// Pour contrôler les suppressions de classes.
				// On conserve la liste des classes précédemment cochées:
				echo "<input type='hidden' name='precclasse_".$id_classe_temp."' value='y' />\n";
			}
			echo "<br />\n";
		}
		//echo "</p>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		// On ne propose de fusionner le groupe avec un/des groupes existants que si le groupe n'a pas déjà de notes,...
		// ... NON: On fera le test sur les groupes à y associer seulement.
		//          Ce sont les autres groupes qui seraient susceptibles de voir leurs notes disparaitre
		echo "<p>[-> <a href='fusion_group.php?id_classe=".$id_classe."&amp;id_groupe=".$id_groupe."'>fusionner le groupe avec un ou des groupes existants</a>]";

		echo "</p>\n";

	} else {
		echo "<p>Aucune classe définie !</p>\n";
	}
}

//-- Fin classes


?>

<?php
// Visibilité enseignement
echo "<p>Visibilité de l'enseignement sur&nbsp;: <br />\n";
for($loop=0;$loop<count($tab_domaines);$loop++) {
	echo "&nbsp;&nbsp;&nbsp;<input type='checkbox' name='visibilite_groupe_".$tab_domaines[$loop]."' id='visibilite_groupe_".$tab_domaines[$loop]."' value='y' ";
	$sql="SELECT 1=1 FROM j_groupes_visibilite WHERE id_groupe='$id_groupe' AND domaine='".$tab_domaines[$loop]."' AND visible='n';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0) {
		echo "checked ";
	}
	echo " onchange=\"checkbox_change_visibilite('visibilite_groupe_".$tab_domaines[$loop]."'); changement();\"";
	echo "title='Visibilité ".$tab_domaines[$loop]."' /><label for='visibilite_groupe_".$tab_domaines[$loop]."' id='texte_visibilite_groupe_".$tab_domaines[$loop]."'";
	if(mysqli_num_rows($test)==0) {
		echo "style='font-weight:bold;' ";
	}
	echo ">".$tab_domaines_texte[$loop]."</label><br />\n";
}

echo "
	<br />
	<div style='border:1px solid white; background-image: url(\"../images/background/opacite50.png\");'>
		<p><strong>Création de sous-groupes&nbsp;:</strong></p>
		<p style='text-indent:-2em; margin-left:2em;'>
			<input type='checkbox' name='creer_sous_groupes' id='creer_sous_groupes' value='y' onchange='affiche_masque_details_sous_groupes(); changement();' /><label for='creer_sous_groupes' id='texte_creer_sous_groupes'>Créer de nouveaux sous-groupes</label><br />
			<div id='div_details_nouveaux_sous_groupes'>
				en fractionnant l'enseignement en 
				<select name='nb_sous_groupes_a_creer' id='nb_sous_groupes_a_creer' onchange='maj_check_sous_groupes(); changement();'>
					<option value=''>---</option>
					<option value='1'>1</option>
					<option value='2'>2</option>
					<option value='3'>3</option>
					<option value='4'>4</option>
					<option value='5'>5</option>
					<option value='6'>6</option>
					<option value='7'>7</option>
					<option value='8'>8</option>
				</select>&nbsp;groupes<br />
				d'élèves rangés par ordre&nbsp;<select name='ordre_eleves_sous_groupe_a_creer' onchange='changement();'>
					<option value='classe'>de classe, puis par ordre alphabétique</option>
					<option value='alpha'>alphabétique</option>
				</select><br />
				avec des suffixes&nbsp;<select name='suffixe_sous_groupe_a_creer' id='suffixe_sous_groupe_a_creer' onchange='changement();'>
					<option value=''>pas de suffixe</option>
					<option value='1'>_1, _2,...</option>
					<option value='g1'>_g1, _g2,...</option>
					<option value='A'>_A, _B,...</option>
				</select><br />
				visibles sur<br />";

for($loop=0;$loop<count($tab_domaines);$loop++) {
	echo "
				&nbsp;&nbsp;&nbsp;<input type='checkbox' name='visibilite_nouveaux_sous_groupes_".$tab_domaines[$loop]."' id='visibilite_nouveaux_sous_groupes_".$tab_domaines[$loop]."' value='y' onchange=\"checkbox_change_visibilite('visibilite_nouveaux_sous_groupes_".$tab_domaines[$loop]."'); changement();\" title='Visibilité ".$tab_domaines[$loop]."' /><label for='visibilite_nouveaux_sous_groupes_".$tab_domaines[$loop]."' id='texte_visibilite_nouveaux_sous_groupes_".$tab_domaines[$loop]."'>".$tab_domaines_texte[$loop]."</label><br />\n";
}

		echo "
		</p>

		<p><em>NOTE&nbsp;:</em> Ce sont de nouveaux groupes (<em>à part entière</em>), mais créés avec les mêmes professeurs, des noms (<em>au suffixe près</em>) et au partage des élèves près.</p>
		</div>

		<script type='text/javascript'>
			function maj_check_sous_groupes() {
				if(document.getElementById('nb_sous_groupes_a_creer').options[document.getElementById('nb_sous_groupes_a_creer').selectedIndex].value==0) {
					document.getElementById('creer_sous_groupes').checked=false;
				}
				else {
					document.getElementById('creer_sous_groupes').checked=true;

					if(document.getElementById('nb_sous_groupes_a_creer').options[document.getElementById('nb_sous_groupes_a_creer').selectedIndex].value>1) {
						document.getElementById('suffixe_sous_groupe_a_creer').selectedIndex=1;
					}

					if(document.getElementById('nb_sous_groupes_a_creer').options[document.getElementById('nb_sous_groupes_a_creer').selectedIndex].value==1) {
						document.getElementById('suffixe_sous_groupe_a_creer').selectedIndex=0;
					}
				}
			}

			document.getElementById('div_details_nouveaux_sous_groupes').style.display='none';

			function affiche_masque_details_sous_groupes() {
				if(document.getElementById('creer_sous_groupes').checked==true) {
					document.getElementById('div_details_nouveaux_sous_groupes').style.display='';
				}
				else {
					document.getElementById('div_details_nouveaux_sous_groupes').style.display='none';
				}
			}
		</script>

	</div>";

// +++++++++++++++++++++++++++++++++
// 20160417
$sql="SELECT * FROM nomenclature_modalites_election;";
$res_nme=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_nme)>0) {
	$cpt_mod=0;
	$tab_non_assoc=array();
	echo "<div style='margin-top:1em; border:1px solid white;' class='fieldset_opacite50'>
	<p title=\"Les modalités possibles pour les couples MEF/matière sont normalement importés avec les nomenclatures lors de l'initialisation de l'année.\nSi ce n'est pas le cas, commencez par refaire, dans Gestion des bases/Gestion des MEFs, une importation des MEFs d'après un fichier Nomenclature.xml\">Modalités associées à cet enseignement&nbsp;: </p>";
	$cpt_code_modalite_elect=0;
	while($lig_nme=mysqli_fetch_object($res_nme)) {
		$tab_autres_nomenclatures=array();

		$sql="SELECT DISTINCT mm.* FROM matieres m, 
						mef_matieres mm
					WHERE mm.code_modalite_elect='".$lig_nme->code_modalite_elect."' AND 
					mm.code_matiere=m.code_matiere AND 
					m.matiere='".$current_group["matiere"]["matiere"]."' AND 
					mm.mef_code IN (SELECT e.mef_code FROM eleves e, j_eleves_groupes jeg WHERE e.login=jeg.login AND jeg.id_groupe='".$id_groupe."');";
		//echo "$sql<br />";
		$res_mm=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_mm)>0) {

			/*
			echo "
	<input type='checkbox' name='code_modalite_elect[]' id='code_modalite_elect_".$lig_nme->code_modalite_elect."' value='".$lig_nme->code_modalite_elect."' checked /><label for='".$lig_nme->code_modalite_elect."'>".$lig_nme->libelle_court." <em>(".$lig_nme->libelle_long.")</em></label>";
			*/

			echo $lig_nme->libelle_court." <em>(".$lig_nme->libelle_long.")</em>";

			$sql="SELECT DISTINCT jgem.login FROM j_groupes_eleves_modalites jgem
						WHERE jgem.code_modalite_elect='".$lig_nme->code_modalite_elect."' AND 
							jgem.id_groupe='".$id_groupe."';";
			//echo "$sql<br />";
			$res_ele_mod=mysqli_query($mysqli, $sql);
			$eff_ele_mod=mysqli_num_rows($res_ele_mod);

			$sql="SELECT DISTINCT jeg.login FROM matieres m, 
							mef_matieres mm, 
							j_eleves_groupes jeg, 
							eleves e 
						WHERE mm.code_modalite_elect='".$lig_nme->code_modalite_elect."' AND 
							mm.code_matiere=m.code_matiere AND 
							mm.mef_code=e.mef_code AND 
							e.login=jeg.login AND 
							jeg.id_groupe='".$id_groupe."';";
			//echo "$sql<br />";
			$res_ele_mod2=mysqli_query($mysqli, $sql);
			$eff_ele_mod2=mysqli_num_rows($res_ele_mod2);

			echo " <span title=\"$eff_ele_mod élève(s) associés à cette modalité dans cet enseignement sur $eff_ele_mod2 .\">($eff_ele_mod/$eff_ele_mod2)</span>";

			echo " <br />";
			$cpt_code_modalite_elect++;
		}
		/*
		else {
			// Remplir un tableau des modalités non associées
			$tab_non_assoc[$lig_nme->code_modalite_elect]['libelle_court']=$lig_nme->libelle_court;
			$tab_non_assoc[$lig_nme->code_modalite_elect]['libelle_long']=$lig_nme->libelle_long;
		}
		*/
	}

	if($cpt_code_modalite_elect==0) {
		echo "<span style='color:red;'>Aucune modalité élection de la matière ".$current_group["matiere"]["matiere"]." n'est associée aux MEFs des élèves de cet enseignement.</span><br />";
	}

	/*
	// Parcourir le tableau des modalités non associées pour proposer l'ajout via un SELECT.
	if(count($tab_non_assoc)>0) {
		echo "
	<select name='code_modalite_elect[]' id='code_modalite_elect_supplementaire' onchange='changement()'>
		<option value=''>--- Ajouter ---</option>";
		foreach($tab_non_assoc as $current_code_modalite_elect => $tmp_tab) {
			echo "
		<option value='$current_code_modalite_elect' title=\"".$tmp_tab['libelle_long']."\">".$tmp_tab['libelle_long']."</option>";
		}
		echo "
	</select><img src='../images/icons/ico_attention.png' class='icone16' title=\"Vous ne devriez normalement pas ajouter de modalité.\nLes modalités possibles pour les couples MEF/matière sont normalement importés avec les nomenclatures lors de l'initialisation de l'année.\nSi ce n'est pas le cas, commencez par refaire, dans Gestion des bases/Gestion des MEFs, une importation des MEFs d'après un fichier Nomenclature.xml\" />";
	}
	*/
	echo "
</div>";
}
// +++++++++++++++++++++++++++++++++

// +++++++++++++++++++++++++++++++++
$sql="SELECT 1=1 FROM edt_corresp2;";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)>0) {
	$chaine_anomalie_corresp_edt="";

	$sql="SELECT * FROM edt_corresp2 WHERE id_groupe='$id_groupe';";
	$res_edt=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_edt)>0) {
		$lig_edt=mysqli_fetch_object($res_edt);
		//onclick=\"return confirm_abandon (this, change, '$themessage')\"
		echo "<p style='margin-top:1em;'><span id='span_regroupement_edt_associe'>Regroupement EDT associé&nbsp;: ".htmlentities($lig_edt->nom_groupe_edt)."</span> <a href='maj_inscript_ele_d_apres_edt.php?action=editer_ec3&amp;id_groupe=".$id_groupe."' onclick=\"afficher_div('div_regroupement_edt','y',10,10);return false;\" title=\"Editer l'association à un regroupement EDT.\"><img src='../images/edit16.png' class='icone16' alt='Editer' /></a>";
	}
	else {
		echo "<p style='margin-top:1em;'><span id='span_regroupement_edt_associe'>Aucun regroupement EDT n'est associé à ce groupe Gepi</span> <a href='maj_inscript_ele_d_apres_edt.php?action=editer_ec3&amp;id_groupe=".$id_groupe."' onclick=\"afficher_div('div_regroupement_edt','y',10,10);return false;\" title=\"Associer.\"><img src='../images/edit16.png' class='icone16' alt='Associer' /></a>.";
	}

	$texte_infobulle="";
	$tab_assoc=array();
	$sql="SELECT * FROM edt_corresp2 WHERE id_groupe='$id_groupe' ORDER BY nom_groupe_edt;";
	$res2=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res2)>0) {
		while($lig2=mysqli_fetch_object($res2)) {
			$tab_assoc[]=$lig2->nom_groupe_edt;
		}
	}
	if(count($tab_assoc)>1) {
		$texte_infobulle.="<p style='color:red; text-indent:-6em; margin-left:6em;'>ANOMALIE&nbsp;: Le groupe/enseignement Gepi est associé à ".count($tab_assoc)." regroupements EDT (<em>";
		$chaine_anomalie_corresp_edt="<img src='../images/icons/flag2.gif' alt='Anomalie' class='icone16' title=\"ANOMALIE : Le groupe/enseignement Gepi est associé\n                     à ".count($tab_assoc)." regroupements EDT:\n";
		for($loop=0;$loop<count($tab_assoc);$loop++) {
			if($loop>0) {
				$texte_infobulle.=", ";
			}
			$texte_infobulle.=htmlentities($tab_assoc[$loop]);
			$chaine_anomalie_corresp_edt.="                        - ".$tab_assoc[$loop]."\n";
		}
		$texte_infobulle.="</em>).<br />Il ne devrait y en avoir qu'un.<br />Choisissez ci-dessous le bon et validez.</p>";
		$chaine_anomalie_corresp_edt.="Il ne devrait y en avoir qu'un.\" />";
	}
	echo $chaine_anomalie_corresp_edt."</p>";

	$lignes_options="
			<option value=''>---</option>";
	$sql="SELECT * FROM edt_corresp WHERE champ='groupe' ORDER BY nom_edt;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig=mysqli_fetch_object($res)) {
		$selected="";
		if(in_array($lig->nom_edt, $tab_assoc)) {
			$selected=" selected";
		}
		$lignes_options.="
				<option value='$lig->id'$selected>".htmlentities($lig->nom_edt)."</option>";
	}

	$texte_infobulle.="
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi_xml' method='post' style='margin:0.5em;'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field(true)."
		<input type='hidden' name='id_groupe' value='$id_groupe' />
		<input type='hidden' name='action' value='editer_ec3' />
		<input type='hidden' name='mode' value='js' />
		<input type='hidden' name='valider_ec3' value='y' />
		<p>
			Regroupement EDT à associer&nbsp;: 
			<select name='id_nom_edt' id='id_nom_edt'>$lignes_options
			</select>
			 <input type='button' value='Valider' onclick=\"valider_modif_choix_regroupement_edt();\" />
		</p>
	</fieldset>
</form>";

	$tabdiv_infobulle[]=creer_div_infobulle("div_regroupement_edt","Regroupement EDT associé","",$texte_infobulle,"",40,0,'y','y','n','n');

	echo "
<script type='text/javascript'>
	function valider_modif_choix_regroupement_edt() {
		csrf_alea=document.getElementById('csrf_alea').value;
		id_nom_edt=document.getElementById('id_nom_edt').options[document.getElementById('id_nom_edt').selectedIndex].value;

		//new Ajax.Updater($('span_regroupement_edt_associe'),'maj_inscript_ele_d_apres_edt.php?id_groupe=$id_groupe&action=editer_ec3&valider_ec3=y&id_nom_edt='+,{method: 'get'});

		new Ajax.Updater($('span_regroupement_edt_associe'),'maj_inscript_ele_d_apres_edt.php',{method: 'post',
		parameters: {
			id_groupe: $id_groupe,
			action: 'editer_ec3',
			valider_ec3: 'y',
			mode_js: 'y',
			id_nom_edt: id_nom_edt,
			csrf_alea: csrf_alea
		}});

		cacher_div('div_regroupement_edt','y',10,10);
	}
</script>";

}
// +++++++++++++++++++++++++++++++++

echo "</div>\n";

//=================================================
// Matière
echo "<div style='width: 45%; float: right;'>\n";

echo "<p>Sélectionnez la matière enseignée à ce groupe&nbsp;: ";

$query = mysqli_query($GLOBALS["mysqli"], "SELECT matiere, nom_complet FROM matieres ORDER BY matiere");
$nb_mat = mysqli_num_rows($query);

echo "<select name='matiere' id='matiere' size='1'";
echo " onchange='changement(); maj_liste_profs_matiere();'";
echo ">\n";

for ($i=0;$i<$nb_mat;$i++) {
	$matiere = old_mysql_result($query, $i, "matiere");
	$nom_matiere = old_mysql_result($query, $i, "nom_complet");
	echo "<option value='" . $matiere . "'";
	if ($reg_matiere == $matiere) echo " SELECTED";
	echo " nom_matiere=\"$nom_matiere\"";
	echo ">".$matiere." (".htmlspecialchars($nom_matiere).")"."</option>\n";
	//echo ">" . html_entity_decode($nom_matiere) . "</option>\n";
	//echo ">" . htmlspecialchars($nom_matiere) . "</option>\n";
}
echo "</select>";
echo $message_nom_sur_bulletin3;
echo "</p>\n";

//=================================================

echo "<p>Cochez les professeurs qui participent à cet enseignement&nbsp;: </p>\n";

echo "<div id='div_choix_prof'>\n";
afficher_liste_profs_du_groupe($reg_matiere);
echo "</div>\n";

if (count($prof_list["list"]) != "0") {
	echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('prof_'+cpt)) {
		if(document.getElementById('prof_'+cpt).checked) {
			document.getElementById('civ_nom_prenom_prof_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('civ_nom_prenom_prof_'+cpt).style.fontWeight='normal';
		}
	}
}
";

echo js_checkbox_change_style('checkbox_change_divers');
echo js_checkbox_change_style('checkbox_change_visibilite');

echo "
for(i=0;i<$p;i++) {
	checkbox_change(i);
}
</script>\n";

}

$sql="SELECT 1=1 FROM utilisateurs WHERE statut='professeur';";
$res_nb_prof=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_prof_etab=mysqli_num_rows($res_nb_prof);

echo "<script type='text/javascript'>
	function init_checkbox_change() {
		for(i=0;i<$nb_prof_etab;i++) {
			checkbox_change(i);
		}
	}

	function maj_liste_profs_matiere() {
		if($('div_choix_prof')&&$('matiere')) {
			matiere=$('matiere').options[$('matiere').selectedIndex].value;
			new Ajax.Updater($('div_choix_prof'),'./edit_group.php?id_groupe=$id_groupe&id_classe=$id_classe&mode=$mode&maj_liste_profs_matiere=y&matiere='+matiere+'".add_token_in_url(false)."',{method: 'get'});
			setTimeout('init_checkbox_change()', 2000);
			setTimeout('maj_liste_autres_groupes_meme_matiere()', 2000);
		}
	}

	function maj_liste_autres_groupes_meme_matiere() {
		if($('div_autres_groupes_meme_matiere')&&$('matiere')) {
			matiere=$('matiere').options[$('matiere').selectedIndex].value;
			new Ajax.Updater($('div_autres_groupes_meme_matiere'),'./edit_group.php?id_groupe=$id_groupe&id_classe=$id_classe&mode=$mode&maj_liste_autres_groupes_meme_matiere=y&matiere='+matiere+'".add_token_in_url(false)."',{method: 'get'});
		}
	}

	/*
	function ajout_suffixe_nom_grp(suffixe_nom_court, suffixe_nom_complet) {
		document.getElementById('groupe_nom_court').value=document.getElementById('groupe_nom_court').value+suffixe_nom_court;
		document.getElementById('groupe_nom_complet').value=document.getElementById('groupe_nom_complet').value+suffixe_nom_complet;
	}

	function modif_nom_grp(suffixe_nom_court, suffixe_nom_complet) {
		prefixe=document.getElementById('matiere').options[document.getElementById('matiere').selectedIndex].value;
		document.getElementById('groupe_nom_court').value=prefixe+suffixe_nom_court;
		document.getElementById('groupe_nom_complet').value=prefixe+suffixe_nom_complet;
	}
	*/

	if(document.getElementById('groupe_nom_court')) {
		document.getElementById('groupe_nom_court').focus();
	}
</script>

</div>\n";

echo "<div id='div_autres_groupes_meme_matiere' style='width: 45%; float: right; font-size:small; margin-top:1em;'>\n";
$avec_lien_edit_group="y";
$tab_autres_groupes=tableau_html_groupe_matiere_telle_classe($id_classe, $reg_matiere, array($id_groupe));
if($tab_autres_groupes!="") {
	echo "<p>Il existe d'autres enseignements dans la même matière pour cette classe&nbsp;:</p>\n";
	echo $tab_autres_groupes;

	echo "<p><a href='repartition_ele_grp.php?";
	for($loop=0;$loop<count($current_group["classes"]["list"]);$loop++) {
		echo "id_classe[]=".$current_group["classes"]["list"][$loop]."&amp;";
	}
	echo "preselect_id_groupe=$id_groupe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Répartir les élèves entre plusieurs groupes</a></p>";
}
echo "</div>\n";

echo "<div style='float: left; width: 100%'>\n";
echo "<input type='hidden' name='is_posted' value='1' />\n";
echo "<input type='hidden' name='mode' value='" . $mode . "' />\n";
echo "<input type='hidden' name='id_groupe' value='" . $id_groupe . "' />\n";
//============================
// MODIF: boireaus
if(isset($chemin_retour)){
	echo "<input type='hidden' name='chemin_retour' value='$chemin_retour' />\n";
}
if(isset($ancre)){
	echo "<input type='hidden' name='ancre' value='$ancre' />\n";
}
echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
echo "</div>\n";
echo "</div>\n";

echo "</fieldset>
</form>\n";

echo "<div style='clear:both;'></div>
<hr />
<p><br /></p>
<div>
	<p><em>NOTE&nbsp;:</em> Le nom qui apparait dans la colonne matières des bulletins peut être&nbsp;:</p>
	<div style='margin-left:4em;'>
		<ul>
			<li>le nom complet de la matière$choix_nom_sur_bulletin3</li>
			<li>le nom court de l'enseignement (<em>\"groupe\" dans le vocabulaire Gepi</em>)$choix_nom_sur_bulletin1</li>
			<li>la description de l'enseignement (<em>\"groupe\" dans le vocabulaire Gepi</em>)$choix_nom_sur_bulletin2</li>
		</ul>
		<p>Ce paramétrage peut être effectué dans la page <a href='../gestion/param_gen.php#bul_rel_nom_matieres'>Gestion générale/Configuration générale</a>.</p>
	</div>
</div>\n";

require("../lib/footer.inc.php");
?>
