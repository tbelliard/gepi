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

//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

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

$indice_aid = isset($_POST['indice_aid']) ? $_POST['indice_aid'] : (isset($_GET['indice_aid']) ? $_GET['indice_aid'] : NULL);
$aid_id = isset($_POST['aid_id']) ? $_POST['aid_id'] : (isset($_GET['aid_id']) ? $_GET['aid_id'] : NULL);
$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);

//$en_tete = isset($_POST['en_tete']) ? $_POST['en_tete'] : (isset($_GET['en_tete']) ? $_GET['en_tete'] : NULL);

if(is_numeric($indice_aid) && $indice_aid > 0 && is_numeric($aid_id) && $aid_id > 0) {
	if ($_SESSION['statut'] != "secours") {
		if(!acces_saisie_aid($_SESSION['login'], $indice_aid, $aid_id)) {
			$mess=rawurlencode("Vous n&aposêtes pas professeur de cet AID !");
			header("Location: ../accueil.php?msg=$mess");
			die();
		}
	}

	$tab_aid=get_tab_aid($aid_id);
}
else {
	$mess=rawurlencode("AID non identifié !");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

$periode_num = isset($_POST['periode_num']) ? $_POST['periode_num'] : (isset($_GET['periode_num']) ? $_GET['periode_num'] : NULL);
if (!is_numeric($periode_num)) {$periode_num = 0;}

$is_posted = isset($_POST['is_posted']) ? $_POST['is_posted'] : NULL;

$id_dev = isset($_POST['id_dev']) ? $_POST['id_dev'] : (isset($_GET['id_dev']) ? $_GET['id_dev'] : NULL);
$periode_app = isset($_POST['periode_app']) ? $_POST['periode_app'] : (isset($_GET['periode_app']) ? $_GET['periode_app'] : NULL);

$groupes_non_visibles['cn']=array();
$sql="SELECT DISTINCT id_groupe FROM j_groupes_visibilite WHERE domaine='cahier_notes' AND visible='n';";
$res_vis=mysqli_query($GLOBALS["mysqli"], $sql);
while($lig_vis=mysqli_fetch_object($res_vis)) {
	$groupes_non_visibles['cn'][]=$lig_vis->id_groupe;
}
$groupes_non_visibles['bull']=array();
$sql="SELECT DISTINCT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n';";
$res_vis=mysqli_query($GLOBALS["mysqli"], $sql);
while($lig_vis=mysqli_fetch_object($res_vis)) {
	$groupes_non_visibles['bull'][]=$lig_vis->id_groupe;
}


//include "../lib/periodes.inc.php";

//**************** EN-TETE *****************
$titre_page = "AID notes et appréciations | Importation";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

// $long_max : doit être plus grand que la plus grande ligne trouvée dans le fichier CSV
$long_max = 8000;

echo "<p class='bold'><a href='saisie_aid.php?indice_aid=$indice_aid'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil saisie</a>";
//====================================
if($_SESSION['statut']=='professeur') {

	/*
	// A FAIRE: Récupérer la liste des AID de la catégorie courante
	$tab_groups = get_groups_for_prof($_SESSION["login"],"classe puis matière");
	//$tab_groups = get_groups_for_prof($_SESSION["login"]);

	if(!empty($tab_groups)) {
		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		//foreach($tab_groups as $tmp_group) {
		for($loop=0;$loop<count($tab_groups);$loop++) {
			if($tab_groups[$loop]['id']==$id_groupe){
				$temoin_tmp=1;
				if(isset($tab_groups[$loop+1])){
					$id_grp_suiv=$tab_groups[$loop+1]['id'];
				}
				else{
					$id_grp_suiv=0;
				}
			}
			if($temoin_tmp==0){
				$id_grp_prec=$tab_groups[$loop]['id'];
			}
		}
		// =================================

		if(isset($id_grp_prec)){
			if($id_grp_prec!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_prec&amp;periode_num=$periode_num";
				echo "'>Enseignement précédent</a>";
			}
		}
		if(isset($id_grp_suiv)){
			if($id_grp_suiv!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_suiv&amp;periode_num=$periode_num";
				echo "'>Enseignement suivant</a>";
				}
		}
	}
	*/
	// =================================
}
//====================================
if(acces("/saisie/import_note_app_aid.php", $_SESSION['statut'])) {
	echo " | <a href='import_note_app_aid.php?indice_aid=$indice_aid&aid_id=$aid_id' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Importer les notes et/ou appréciations depuis un fichier CSV.\">Import CSV</a>";
}
echo "</p>\n";

echo "<h2>Importation des notes et/ou appréciations depuis un enseignement</h2>

<h3>AID&nbsp;: " . htmlspecialchars($tab_aid["nom_aid"]) ." (" . $tab_aid["classlist_string"] . ") (" . htmlspecialchars($tab_aid["nom_general_complet"]) . ")</h3>

<p>La présente page permet de remplir les notes et/ou appréciations d'un AID d'après des notes/appréciations saisies dans les Carnets de notes ou les Bulletins.</p>\n";

$lignes_radio_periode="";
$tout_verrouille=true;
for($loop_per=1;$loop_per<=$tab_aid['maxper'];$loop_per++) {
	if(($tab_aid["classe"]["ver_periode"]['all'][$loop_per]>=2)||
	(($tab_aid["classe"]["ver_periode"]['all'][$loop_per]!=0)&&($_SESSION['statut']=='secours'))) {
		$tout_verrouille=false;
		//break;
		if($lignes_radio_periode=="") {
			$lignes_radio_periode.="<label for='periode_num_$loop_per' id='texte_periode_num_$loop_per'>Période $loop_per </label><input type='radio' name='periode_num' id='periode_num_$loop_per' value='$loop_per' checked /><br />";
		}
		else {
			$lignes_radio_periode.="<label for='periode_num_$loop_per' id='texte_periode_num_$loop_per'>Période $loop_per </label><input type='radio' name='periode_num' id='periode_num_$loop_per' value='$loop_per' /><br />";
		}
	}
}

if($tout_verrouille) {
	echo "<p style='color:red'>Toutes les périodes sont closes pour les classes associées à cet AID.<br />Les saisies ne sont plus possibles.</p>";
	require("../lib/footer.inc.php");
	die();
}

if (!isset($id_groupe)) {

	$groups=get_groups_for_prof($_SESSION['login']);
	if(count($groups)==0) {
		echo "<p style='color:red'>Vous n'avez aucun enseignement.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	// On ne va retenir que les groupes avec des classes en commun avec l'AID
	$groups2=array();
	foreach($groups as $current_group) {
		for($loop=0;$loop<count($current_group['classes']['list']);$loop++) {
			if(in_array($current_group['classes']['list'][$loop], $tab_aid['classes']['list'])) {
				$groups2[]=$current_group;
			}
		}
	}

	echo "<p style='margin-left:3em;text-indent:-3em;'><span class='bold'>Choix de l'enseignement&nbsp;:</span> <br />\n";

	$temoin_au_moins_un_enseignement=0;
	for($loop=0;$loop<count($groups2);$loop++) {
		if((!in_array($groups2[$loop]["id"], $groupes_non_visibles['cn']))||(!in_array($groups2[$loop]["id"], $groupes_non_visibles['bull']))) {
			echo "<a href='".$_SERVER['PHP_SELF']."?indice_aid=$indice_aid&aid_id=$aid_id&id_groupe=".$groups2[$loop]["id"]."'>".$groups2[$loop]["name"]." (".$groups2[$loop]["description"].") en ".$groups2[$loop]["classlist_string"]."</a><br />";
			$temoin_au_moins_un_enseignement++;
		}
	}
	echo "</p>";

	if($temoin_au_moins_un_enseignement==0) {
		echo "<p style='color:red'>Aucun enseignement visible soit sur les carnets de notes, soit dans les bulletins n'a été trouvé pour des classes correspondant à l'AID courant.</p>";
	}

	require("../lib/footer.inc.php");
	die();
}
elseif((!isset($id_dev))&&(!isset($periode_app))) {
	$group=get_group($id_groupe);
	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='aid_id' value='$aid_id' />
		<input type='hidden' name='indice_aid' value='$indice_aid' />
		<input type='hidden' name='id_groupe' value='$id_groupe' />
		<h4>".$group["name"]." (".$group["description"].") en ".$group["classlist_string"]."</h4>
		<p class='bold'>Choix de l'évaluation ou de la moyenne&nbsp;: </p>\n";

			$groupes_non_visibles['cn']=array();
			$sql="SELECT DISTINCT id_groupe FROM j_groupes_visibilite WHERE domaine='cahier_notes' AND visible='n';";
			$res_vis=mysqli_query($GLOBALS["mysqli"], $sql);
			while($lig_vis=mysqli_fetch_object($res_vis)) {
				$groupes_non_visibles['cn'][]=$lig_vis->id_groupe;
			}
			$groupes_non_visibles['bull']=array();
			$sql="SELECT DISTINCT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n';";
			$res_vis=mysqli_query($GLOBALS["mysqli"], $sql);
			while($lig_vis=mysqli_fetch_object($res_vis)) {
				$groupes_non_visibles['bull'][]=$lig_vis->id_groupe;
			}

			echo "
		<table class='boireaus boireaus_alt' summary='Liste des évaluations/moyennes'>
			<thead>
				<tr>
					<th>Période</th>";
			for($i=1;$i<=count($group["periodes"]);$i++) {
				echo "
					<th>".$group["periodes"][$i]["nom_periode"]."</th>";
			}
			echo "
				</tr>
			</thead>";

			$cpt=0;

			echo "
			<tbody>";

			if(!in_array($id_groupe, $groupes_non_visibles['bull'])) {
				echo "
					<tr>
						<th>
							Appréciations<br />
							<a href='#' onclick=\"decocher_app();return false;\" title=\"Décocher pour ne pas importer\"><img src='../images/disabled.png' class='icone20' alt='Décocher' /></a>
						</th>";

				for($i=1;$i<=count($group["periodes"]);$i++) {
					$sql="SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='$id_groupe' AND periode='$i';";
					$test=mysqli_query($GLOBALS['mysqli'], $sql);
					if(mysqli_num_rows($test)>0) {
						echo "
						<td style='text-align:left; vertical-align:top;'>
							<label for='periode_app_$cpt' id='texte_periode_app_$cpt' style='cursor: pointer;' alt='Appréciations des bulletin pour la période' title='Appréciations des bulletins pour la période'>Appreciations des Bulletins</label><input type='radio' name='periode_app' id='periode_app_$cpt' value='bull".$i."' onchange=\"radio_change();changement();\" />
						</td>";
						$cpt++;
					}
					else {
						echo "
						<td style='color:red'>Pas d'appréciations dans les bulletins.</td>";
					}
				}

				echo "
					</tr>";
			}

			if($tab_aid['type_note']!='no') {
				echo "
					<tr>
						<th>
							Notes<br />
							<a href='#' onclick=\"decocher_dev();return false;\" title=\"Décocher pour ne pas importer\"><img src='../images/disabled.png' class='icone20' alt='Décocher' /></a>
						</th>";
				for($i=1;$i<=count($group["periodes"]);$i++) {
					echo "
					<td style='text-align:left; vertical-align:top;'>\n";

					if(!in_array($id_groupe, $groupes_non_visibles['bull'])) {
						$sql="SELECT 1=1 FROM matieres_notes WHERE id_groupe='$id_groupe' AND periode='$i';";
						$test=mysqli_query($GLOBALS['mysqli'], $sql);
						if(mysqli_num_rows($test)>0) {
							echo "
						<label for='id_dev_$cpt' id='texte_id_dev_$cpt' style='cursor: pointer;' alt='Moyennes du bulletin pour la période' title='Moyennes du bulletin pour la période'>Moyennes des Bulletins</label><input type='radio' name='id_dev' id='id_dev_$cpt' value='bull".$i."' onchange=\"radio_change();changement();\" /><br />";
							$cpt++;
						}
						else {
							echo "
						<span style='color:red'>Pas de notes dans les bulletins.</span><br />";
						}
					}

					if(!in_array($id_groupe, $groupes_non_visibles['cn'])) {
						$sql="SELECT ccn.periode FROM cn_cahier_notes ccn WHERE ccn.id_groupe='$id_groupe' AND ccn.periode='$i' ORDER BY ccn.periode;";
						$test=mysqli_query($GLOBALS['mysqli'], $sql);
						if(mysqli_num_rows($test)>0) {
							echo "
						<label for='id_dev_$cpt' id='texte_id_dev_$cpt' style='cursor: pointer;' alt='Moyenne du carnet de notes pour la période' title='Moyennes du carnet de notes pour la période'>Moyennes du Carnets de notes</label><input type='radio' name='id_dev' id='id_dev_$cpt' value='cn".$i."' onchange=\"radio_change();changement();\" /><br />";
							$cpt++;
						}
						else {
							echo "
						<span style='color:red'>Pas encore de carnet de notes.</span><br />";
						}

						$sql="SELECT cd.*, ccn.periode FROM cn_devoirs cd, cn_cahier_notes ccn WHERE ccn.id_groupe='$id_groupe' AND ccn.id_cahier_notes=cd.id_racine AND ccn.periode='$i' ORDER BY ccn.periode, cd.date, cd.nom_court, cd.nom_complet;";
						//echo "$sql<br />\n";
						$res2=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res2)>0) {
							while($lig2=mysqli_fetch_object($res2)) {
								echo "
						<input type='radio' name='id_dev' id='id_dev_".$cpt."' value='$lig2->id' onchange=\"radio_change();changement();\" /><label for='id_dev_".$cpt."' style='cursor: pointer;'><span id='texte_id_dev_".$cpt."'>".htmlspecialchars($lig2->nom_court)." (<span style='font-style:italic;font-size:x-small;'>".formate_date($lig2->date)."</span>)</span></label><br />\n";

								$cpt++;
							}
						}
						else {
							echo "
						<span style='color:red' title=\"Aucune évaluation n'a été trouvée pour cette période.\">Aucun devoir</span>.<br />";
						}

					}
					echo "
					</td>";
				}
				echo "
				</tr>";
			}
			echo "
			</tbody>
		</table>
		<p style='margin-left:3em;text-indent:-3em;'>Importer dans la période de l'AID&nbsp;:<br />
		$lignes_radio_periode
		</p>
		<input type='hidden' name='valider_choix_dev' value='y' />
		<p align='center'><input type='submit' value=\"Afficher avant import\" /></p>
	</fieldset>
</form>

<p style='margin-left:3em;text-indent:-3em;'><em>NOTE&nbsp;:</em> Les notes/appréciations de l'AID seront écrasées lors de l'import/validation</p>

<script type='text/javascript'>
function radio_change() {
	for(j=0;j<$cpt;j++) {
		if(document.getElementById('id_dev_'+j)) {
			if(document.getElementById('texte_id_dev_'+j)) {
				if(document.getElementById('id_dev_'+j).checked) {
					document.getElementById('texte_id_dev_'+j).style.fontWeight='bold';
				}
				else {
					document.getElementById('texte_id_dev_'+j).style.fontWeight='normal';
				}
			}
		}
	}

	for(j=0;j<$cpt;j++) {
		if(document.getElementById('periode_app_'+j)) {
			if(document.getElementById('texte_periode_app_'+j)) {
				if(document.getElementById('periode_app_'+j).checked) {
					document.getElementById('texte_periode_app_'+j).style.fontWeight='bold';
				}
				else {
					document.getElementById('texte_periode_app_'+j).style.fontWeight='normal';
				}
			}
		}
	}
}

function decocher_dev() {
	for(j=0;j<$cpt;j++) {
		if(document.getElementById('id_dev_'+j)) {
			document.getElementById('id_dev_'+j).checked=false;
			if(document.getElementById('texte_id_dev_'+j)) {
				document.getElementById('texte_id_dev_'+j).style.fontWeight='normal';
			}
		}
	}
}

function decocher_app() {
	for(j=0;j<$cpt;j++) {
		if(document.getElementById('periode_app_'+j)) {
			document.getElementById('periode_app_'+j).checked=false;
			if(document.getElementById('texte_periode_app_'+j)) {
				document.getElementById('texte_periode_app_'+j).style.fontWeight='normal';
			}
		}
	}
}
</script>\n";
}
elseif(!isset($_POST['confirme'])) {
	check_token(false);

	//debug_var();
	$group=get_group($id_groupe);

	if(($tab_aid["classe"]["ver_periode"]['all'][$periode_num]>=2)||
	(($tab_aid["classe"]["ver_periode"]['all'][$periode_num]!=0)&&($_SESSION['statut']=='secours'))) {

		// Générer un CSV de sauvegarde de ce qui est préalablement enregistré dans aid_appreciations?

		$ligne_input_hidden_id_dev="";
		$ligne_input_hidden_periode_app="";

		if($tab_aid['type_note']!='no') {
			$tab_note=array();
			$sql="SELECT * FROM aid_appreciations WHERE id_aid='$aid_id' AND indice_aid='$indice_aid' AND periode='$periode_num';";
			//echo "$sql<br />";
			$res_note_aid=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($res_note_aid)==0) {
				echo "<p style='color:red'>Aucune note n'est actuellement enregistrée dans l'AID pour la période $periode_num.</p>";
			}
			else {
				while($lig_note_aid=mysqli_fetch_object($res_note_aid)) {
					$tab_note["aid"][$lig_note_aid->login]["note"]=$lig_note_aid->note;
					$tab_note["aid"][$lig_note_aid->login]["statut"]=$lig_note_aid->statut;
				}
			}

			if(isset($id_dev)) {
				$ligne_input_hidden_id_dev="
		<input type='hidden' name='id_dev' value='$id_dev' />";
				if((preg_match("/^bull/", $id_dev))&&(!in_array($id_groupe, $groupes_non_visibles['bull']))) {
					$num_per=preg_replace("/^bull/", "", $id_dev);
					if(preg_match("/^[0-9]{1,}$/", $num_per)) {
						$sql="SELECT * FROM matieres_notes WHERE id_groupe='$id_groupe' AND periode='$num_per';";
						//echo "$sql<br />";
						$res_note_import=mysqli_query($GLOBALS['mysqli'], $sql);
						if(mysqli_num_rows($res_note_import)==0) {
							echo "<p style='color:red'>Aucune note n'est actuellement enregistrée dans les bulletins pour la période $periode_num.</p>";
						}
						else {
							while($lig_note_import=mysqli_fetch_object($res_note_import)) {
								$tab_note["import"][$lig_note_import->login]["note"]=$lig_note_import->note;
								$tab_note["import"][$lig_note_import->login]["statut"]=$lig_note_import->statut;
							}
						}
					}
				}
				elseif(!in_array($id_groupe, $groupes_non_visibles['cn'])) {
					if(preg_match("/^cn/", $id_dev)) {
						$num_per=preg_replace("/^cn/", "", $id_dev);
						if(preg_match("/^[0-9]{1,}$/", $num_per)) {
							$sql="SELECT cnc.* FROM cn_notes_conteneurs cnc, cn_conteneurs cc, cn_cahier_notes ccn WHERE ccn.id_groupe='$id_groupe' AND ccn.periode='$num_per' AND ccn.id_cahier_notes=cc.id AND cc.id=cnc.id_conteneur;";
							//echo "$sql<br />";
							$res_note_import=mysqli_query($GLOBALS['mysqli'], $sql);
							if(mysqli_num_rows($res_note_import)==0) {
								echo "<p style='color:red'>Aucune note n'est actuellement enregistrée dans les carnet de notes pour la période $periode_num.</p>";
							}
							else {
								while($lig_note_import=mysqli_fetch_object($res_note_import)) {
									// ATTENTION : Dans les moyennes de carnets de notes, on a 'y' pour une moyenne valide, rien pour une absence de saisie et abs, disp ou - sinon.
									$tab_note["import"][$lig_note_import->login]["note"]=$lig_note_import->note;
									if($lig_note_import->statut=="y") {
										$tab_note["import"][$lig_note_import->login]["statut"]="";
									}
									elseif($lig_note_import->statut=="") {
										$tab_note["import"][$lig_note_import->login]["statut"]="-";
									}
									else {
										$tab_note["import"][$lig_note_import->login]["statut"]=$lig_note_import->statut;
									}
								}
							}
						}
					}
					elseif(preg_match("/^[0-9]{1,}$/", $id_dev)) {
						// A FAIRE : Quand même vérifier que le contrôle appartient au prof
						$sql="SELECT * FROM cn_notes_devoirs WHERE id_devoir='$id_dev';";
						//echo "$sql<br />";
						$res_note_import=mysqli_query($GLOBALS['mysqli'], $sql);
						if(mysqli_num_rows($res_note_import)==0) {
							echo "<p style='color:red'>Aucune note n'est actuellement enregistrée dans le contrôle choisi du carnet de notes pour la période $periode_num.</p>";
						}
						else {
							while($lig_note_import=mysqli_fetch_object($res_note_import)) {
								$tab_note["import"][$lig_note_import->login]["note"]=$lig_note_import->note;
								$tab_note["import"][$lig_note_import->login]["statut"]=$lig_note_import->statut;
							}
						}
					}
				}
			}
		}

		if(isset($periode_app)) {
			$ligne_input_hidden_periode_app="
		<input type='hidden' name='periode_app' value='$periode_app' />";
			$tab_app=array();
			$sql="SELECT * FROM aid_appreciations WHERE id_aid='$aid_id' AND indice_aid='$indice_aid' AND periode='$periode_num';";
			//echo "$sql<br />";
			$res_app_aid=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($res_app_aid)==0) {
				echo "<p style='color:red'>Aucune appréciation n'est actuellement enregistrée dans l'AID pour la période $periode_num.</p>";
			}
			else {
				while($lig_app_aid=mysqli_fetch_object($res_app_aid)) {
					$tab_app["aid"][$lig_app_aid->login]["appreciation"]=$lig_app_aid->appreciation;
				}
			}

			if(preg_match("/^bull/", $periode_app)) {
				$num_per=preg_replace("/^bull/", "", $periode_app);
				if(preg_match("/^[0-9]{1,}$/", $num_per)) {
					$sql="SELECT * FROM matieres_appreciations WHERE id_groupe='$id_groupe' AND periode='$num_per';";
					//echo "$sql<br />";
					$res_app_import=mysqli_query($GLOBALS['mysqli'], $sql);
					if(mysqli_num_rows($res_app_import)==0) {
						echo "<p style='color:red'>Aucune appréciation n'est actuellement enregistrée dans les bulletins pour la période $periode_num.</p>";
					}
					else {
						while($lig_app_import=mysqli_fetch_object($res_app_import)) {
							$tab_app["import"][$lig_app_import->login]["appreciation"]=$lig_app_import->appreciation;
						}
					}
				}
			}
		}

		if((!isset($tab_note["import"]))&&(!isset($tab_app["import"]))) {
			echo "<p style='color:red'>Aucune donné à importer n'a été trouvée.</p>";
		}
		else {
			$aff_col_note_aid=false;
			$aff_col_app_aid=false;
			$aff_col_note_import=false;
			$aff_col_app_import=false;

			/*
			$colonnes_aid="";
			$nb_col_aid=0;
			if(($tab_aid["type_note"]!="no")&&(isset($tab_note["aid"]))) {
				$aff_col_note_aid=true;
				$nb_col_aid++;
				$colonnes_aid.="
					<th>Notes</th>";
			}
			if(isset($tab_app["aid"])) {
			$aff_col_app_aid=true;
				$nb_col_aid++;
				$colonnes_aid.="
					<th>Appréciations</th>";
			}
			$colspan_aid="";
			if($nb_col_aid==2) {
				$colspan_aid=" colspan='2'";
			}

			$nb_col_import=0;
			$colonnes_import="";
			if(isset($tab_note["import"])) {
				$aff_col_note_import=true;
				$nb_col_import++;
				$colonnes_import.="
					<th>Notes</th>";
			}
			if(isset($tab_app["import"])) {
				$aff_col_app_import=true;
				$nb_col_import++;
				$colonnes_import.="
					<th>Appréciations</th>";
			}
			$colspan_import="";
			if($nb_col_import==2) {
				$colspan_import=" colspan='2'";
			}
			*/

			$colonnes_aid="";
			$nb_col_aid=0;
			if($tab_aid["type_note"]!="no") {
			//if(($tab_aid["type_note"]!="no")&&(isset($tab_note["aid"]))) {
				$aff_col_note_aid=true;
				$nb_col_aid++;
				$colonnes_aid.="
					<th>Notes</th>";
			}
			if(isset($tab_app["import"])) {
				$aff_col_app_aid=true;
				$nb_col_aid++;
				$colonnes_aid.="
					<th>Appréciations</th>";
			}
			$colspan_aid="";
			if($nb_col_aid==0) {
				// Pour ne pas avoir de colonne vide sur les données AID
				$aff_col_app_aid=true;
				$colonnes_aid.="
					<th>Appréciations</th>";
			}
			elseif($nb_col_aid==2) {
				$colspan_aid=" colspan='2'";
			}


			$nb_col_import=0;
			$colonnes_import="";
			if(isset($tab_note["import"])) {
				$aff_col_note_import=true;
				$nb_col_import++;
				$colonnes_import.="
					<th>Notes</th>";
			}
			if(isset($tab_app["import"])) {
				$aff_col_app_import=true;
				$nb_col_import++;
				$colonnes_import.="
					<th>Appréciations</th>";
			}
			$colspan_import="";
			if($nb_col_import==2) {
				$colspan_import=" colspan='2'";
			}

			/*
			echo "<pre>";
			print_r($tab_aid);
			echo "</pre>";

			echo "<pre>";
			print_r($tab_note);
			echo "</pre>";
			*/

			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='confirme' value='y' />
		<input type='hidden' name='aid_id' value='$aid_id' />
		<input type='hidden' name='indice_aid' value='$indice_aid' />
		<input type='hidden' name='periode_num' value='$periode_num' />
		<input type='hidden' name='id_groupe' value='$id_groupe' />".$ligne_input_hidden_id_dev.$ligne_input_hidden_periode_app."
		<h4>".$group["name"]." (".$group["description"].") en ".$group["classlist_string"]."</h4>
		<p class='bold'>Dernière étape avant enregistrement/import&nbsp;: </p>
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th rowspan='2'>Élève</th>
					<th rowspan='2'>Classe</th>
					<th".$colspan_aid.">Données présentes dans l'AID</th>
					<th".$colspan_import.">Données à importer</th>
				</tr>
				<tr>".$colonnes_aid.$colonnes_import."
				</tr>
			</thead>
			<tbody>";

			foreach($tab_aid["eleves"][$periode_num]["users"] as $login_ele => $current_ele) {
				echo "
				<tr>
					<td>".$current_ele["nom"]." ".$current_ele["prenom"]."</td>
					<td>".$current_ele["nom_classe"]."</td>";

				if($aff_col_note_aid) {
					$note_aid="";
					if((isset($tab_note["aid"][$login_ele]["statut"]))&&($tab_note["aid"][$login_ele]["statut"]!="")) {
						$note_aid=$tab_note["aid"][$login_ele]["statut"];
					}
					elseif(isset($tab_note["aid"][$login_ele]["note"])) {
						$note_aid=$tab_note["aid"][$login_ele]["note"];
					}
					echo "
					<td>$note_aid</td>";
				}

				if($aff_col_app_aid) {
					$app_aid="";
					if(isset($tab_app["aid"][$login_ele]["appreciation"])) {
						$app_aid=$tab_app["aid"][$login_ele]["appreciation"];
					}
					echo "
					<td>$app_aid</td>";
				}

				if($aff_col_note_import) {
					$note_import="";
					if((isset($tab_note["import"][$login_ele]["statut"]))&&($tab_note["import"][$login_ele]["statut"]!="")) {
						$note_import=$tab_note["import"][$login_ele]["statut"];
					}
					elseif(isset($tab_note["import"][$login_ele]["note"])) {
						$note_import=$tab_note["import"][$login_ele]["note"];
					}
					echo "
					<td>$note_import</td>";
				}

				if($aff_col_app_import) {
					$app_import="";
					if(isset($tab_app["import"][$login_ele]["appreciation"])) {
						$app_import=$tab_app["import"][$login_ele]["appreciation"];
					}
					echo "
					<td>$app_import</td>";
				}
				echo "
				</tr>";

			}
			echo "
			</tbody>
		</table>
		<p align='center'><input type='submit' value=\"Valider l'import\" /></p>
	</fieldset>
</form>\n";
		}
	}
	else {
		echo "<p style='color:red'>La période est close.</p>";
	}

	// Afficher les saisies précédentes et les éventuelles nouvelles valeurs ) valider

	echo "<p><a href='saisie_aid.php?indice_aid=$indice_aid&aid_id=$aid_id'>Retour à la page de saisie des notes/appréciations AID</a>";

}
else {
	// L'import est validé, il reste à enregistrer
	check_token(false);

	//debug_var();
	$group=get_group($id_groupe);

	echo "<h4>Import depuis ".$group["name"]." (".$group["description"].") en ".$group["classlist_string"]."</h4>";

	if(($tab_aid["classe"]["ver_periode"]['all'][$periode_num]>=2)||
	(($tab_aid["classe"]["ver_periode"]['all'][$periode_num]!=0)&&($_SESSION['statut']=='secours'))) {

		if($tab_aid['type_note']!='no') {
			$tab_note=array();
			$sql="SELECT * FROM aid_appreciations WHERE id_aid='$aid_id' AND indice_aid='$indice_aid' AND periode='$periode_num';";
			//echo "$sql<br />";
			$res_note_aid=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($res_note_aid)==0) {
				echo "<p style='color:red'>Aucune note n'est encore enregistrée dans l'AID pour la période $periode_num.</p>";
			}
			else {
				while($lig_note_aid=mysqli_fetch_object($res_note_aid)) {
					$tab_note["aid"][$lig_note_aid->login]["note"]=$lig_note_aid->note;
					$tab_note["aid"][$lig_note_aid->login]["statut"]=$lig_note_aid->statut;
				}
			}

			if(isset($id_dev)) {
				$ligne_input_hidden_id_dev="
		<input type='hidden' name='id_dev' value='$id_dev' />";
				if((preg_match("/^bull/", $id_dev))&&(!in_array($id_groupe, $groupes_non_visibles['bull']))) {
					$num_per=preg_replace("/^bull/", "", $id_dev);
					if(preg_match("/^[0-9]{1,}$/", $num_per)) {
						$sql="SELECT * FROM matieres_notes WHERE id_groupe='$id_groupe' AND periode='$num_per';";
						//echo "$sql<br />";
						$res_note_import=mysqli_query($GLOBALS['mysqli'], $sql);
						if(mysqli_num_rows($res_note_import)==0) {
							echo "<p style='color:red'>Aucune note n'est actuellement enregistrée dans les bulletins pour la période $periode_num.</p>";
						}
						else {
							while($lig_note_import=mysqli_fetch_object($res_note_import)) {
								$tab_note["import"][$lig_note_import->login]["note"]=$lig_note_import->note;
								$tab_note["import"][$lig_note_import->login]["statut"]=$lig_note_import->statut;
							}
						}
					}
				}
				elseif(!in_array($id_groupe, $groupes_non_visibles['cn'])) {
					if(preg_match("/^cn/", $id_dev)) {
						$num_per=preg_replace("/^cn/", "", $id_dev);
						if(preg_match("/^[0-9]{1,}$/", $num_per)) {
							$sql="SELECT cnc.* FROM cn_notes_conteneurs cnc, cn_conteneurs cc, cn_cahier_notes ccn WHERE ccn.id_groupe='$id_groupe' AND ccn.periode='$num_per' AND ccn.id_cahier_notes=cc.id AND cc.id=cnc.id_conteneur;";
							//echo "$sql<br />";
							$res_note_import=mysqli_query($GLOBALS['mysqli'], $sql);
							if(mysqli_num_rows($res_note_import)==0) {
								echo "<p style='color:red'>Aucune note n'est actuellement enregistrée dans les carnet de notes pour la période $periode_num.</p>";
							}
							else {
								while($lig_note_import=mysqli_fetch_object($res_note_import)) {
									// ATTENTION : Dans les moyennes de carnets de notes, on a 'y' pour une moyenne valide, rien pour une absence de saisie et abs, disp ou - sinon.
									$tab_note["import"][$lig_note_import->login]["note"]=$lig_note_import->note;
									if($lig_note_import->statut=="y") {
										$tab_note["import"][$lig_note_import->login]["statut"]="";
									}
									elseif($lig_note_import->statut=="") {
										$tab_note["import"][$lig_note_import->login]["statut"]="-";
									}
									else {
										$tab_note["import"][$lig_note_import->login]["statut"]=$lig_note_import->statut;
									}
								}
							}
						}
					}
					elseif(preg_match("/^[0-9]{1,}$/", $id_dev)) {
						// A FAIRE : Quand même vérifier que le contrôle appartient au prof
						$sql="SELECT * FROM cn_notes_devoirs WHERE id_devoir='$id_dev';";
						//echo "$sql<br />";
						$res_note_import=mysqli_query($GLOBALS['mysqli'], $sql);
						if(mysqli_num_rows($res_note_import)==0) {
							echo "<p style='color:red'>Aucune note n'est actuellement enregistrée dans le contrôle choisi du carnet de notes pour la période $periode_num.</p>";
						}
						else {
							while($lig_note_import=mysqli_fetch_object($res_note_import)) {
								$tab_note["import"][$lig_note_import->login]["note"]=$lig_note_import->note;
								$tab_note["import"][$lig_note_import->login]["statut"]=$lig_note_import->statut;
							}
						}
					}
				}
			}
		}

		if(isset($periode_app)) {
			$tab_app=array();
			$sql="SELECT * FROM aid_appreciations WHERE id_aid='$aid_id' AND indice_aid='$indice_aid' AND periode='$periode_num';";
			//echo "$sql<br />";
			$res_app_aid=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($res_app_aid)==0) {
				echo "<p style='color:red'>Aucune appréciation n'est encore enregistrée dans l'AID pour la période $periode_num.</p>";
			}
			else {
				while($lig_app_aid=mysqli_fetch_object($res_app_aid)) {
					$tab_app["aid"][$lig_app_aid->login]["appreciation"]=$lig_app_aid->appreciation;
				}
			}

			if(preg_match("/^bull/", $periode_app)) {
				$num_per=preg_replace("/^bull/", "", $periode_app);
				if(preg_match("/^[0-9]{1,}$/", $num_per)) {
					$sql="SELECT * FROM matieres_appreciations WHERE id_groupe='$id_groupe' AND periode='$num_per';";
					//echo "$sql<br />";
					$res_app_import=mysqli_query($GLOBALS['mysqli'], $sql);
					if(mysqli_num_rows($res_app_import)==0) {
						echo "<p style='color:red'>Aucune appréciation n'est actuellement enregistrée dans les bulletins pour la période $periode_num.</p>";
					}
					else {
						while($lig_app_import=mysqli_fetch_object($res_app_import)) {
							$tab_app["import"][$lig_app_import->login]["appreciation"]=$lig_app_import->appreciation;
						}
					}
				}
			}
		}

		echo "<p>Enregistrement/import des données&nbsp;: ";
		foreach($tab_aid["eleves"][$periode_num]["users"] as $login_ele => $current_ele) {
			$ajout_sql="";
			if(isset($tab_note["import"][$login_ele]["note"])) {
				if($ajout_sql!="") {$ajout_sql.=", ";}
				$ajout_sql.="note='".$tab_note["import"][$login_ele]["note"]."'";
			}
			if(isset($tab_note["import"][$login_ele]["statut"])) {
				if($ajout_sql!="") {$ajout_sql.=", ";}
				$ajout_sql.="statut='".$tab_note["import"][$login_ele]["statut"]."'";
			}
			if(isset($tab_app["import"][$login_ele]["appreciation"])) {
				if($ajout_sql!="") {$ajout_sql.=", ";}
				$ajout_sql.="appreciation='".mysqli_real_escape_string($GLOBALS['mysqli'], $tab_app["import"][$login_ele]["appreciation"])."'";
			}

			if($ajout_sql!="") {
				if((isset($tab_note["aid"][$login_ele]["note"]))||(isset($tab_app["aid"][$login_ele]["appreciation"]))) {
					$sql="UPDATE aid_appreciations SET ".$ajout_sql." WHERE id_aid='".$aid_id."' AND indice_aid='".$indice_aid."' AND login='".$login_ele."' AND periode='".$periode_num."';";
				}
				else {
					$sql="INSERT INTO aid_appreciations SET ".$ajout_sql.", id_aid='".$aid_id."', indice_aid='".$indice_aid."', login='".$login_ele."', periode='".$periode_num."';";
				}
				//echo "$sql<br/>";
				$reg=mysqli_query($GLOBALS['mysqli'], $sql);
				if($reg) {
					echo " <span style='color:green' title=\"Enregistrement effectué pour ".$current_ele["nom"]." ".$current_ele["prenom"]."\">".$current_ele["nom"]." ".$current_ele["prenom"]."</span>";
				}
				else {
					echo " <span style='color:red' title=\"Erreur lors de l'enregistrement pour ".$current_ele["nom"]." ".$current_ele["prenom"]."\">".$current_ele["nom"]." ".$current_ele["prenom"]."</span>";
				}
			}
			else {
				echo " <span style='color:red' title=\"Pas d'enregistrement pour ".$current_ele["nom"]." ".$current_ele["prenom"]."\">".$current_ele["nom"]." ".$current_ele["prenom"]."</span>";
			}
		}
		echo "</p>";
	}

	echo "<p><a href='saisie_aid.php?indice_aid=$indice_aid&aid_id=$aid_id'>Accéder à la page de saisie des notes/appréciations AID pour vérification</a>";
}
require("../lib/footer.inc.php");
?>
