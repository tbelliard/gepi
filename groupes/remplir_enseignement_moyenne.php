<?php
/*
*
* Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/groupes/remplir_enseignement_moyenne.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/groupes/remplir_enseignement_moyenne.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Création d enseignement moyenne',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//debug_var();

// Initialisation des variables utilisées dans le formulaire

$id_classe=isset($_POST['id_classe']) ? $_POST["id_classe"] : (isset($_GET['id_classe']) ? $_GET["id_classe"] : NULL);
$matiere=isset($_POST['matiere']) ? $_POST["matiere"] : (isset($_GET['matiere']) ? $_GET["matiere"] : NULL);
$matiere_dest=isset($_POST['matiere_dest']) ? $_POST["matiere_dest"] : (isset($_GET['matiere_dest']) ? $_GET["matiere_dest"] : NULL);
$id_groupe=isset($_POST['id_groupe']) ? $_POST["id_groupe"] : (isset($_GET['id_groupe']) ? $_GET["id_groupe"] : NULL);
$id_groupe_dest=isset($_POST['id_groupe_dest']) ? $_POST["id_groupe_dest"] : (isset($_GET['id_groupe_dest']) ? $_GET["id_groupe_dest"] : NULL);

//$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
$titre_page = "Enseignement moyenne";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

//debug_var();
echo "<p class='bold'\n>";
echo "<a href=\"../classes/index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

//=================================================================================================
// Choix des classes

if(!isset($id_classe)) {
	echo "</p>\n";

	echo "<h2>Remplissage d'enseignement moyenne</h2>";

	echo "<p><b>Choix des classes&nbsp;:</b><br />\n";

	$sql="SELECT DISTINCT c.* FROM classes c ORDER BY classe;";
	$call_classes=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_classes=mysqli_num_rows($call_classes);

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

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

</script>";

	require("../lib/footer.inc.php");
	die();

}

//=================================================================================================
// Choix des matières

if((!isset($matiere))||(!isset($matiere_dest))) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choix des classes</a></p>\n";

	echo "<h2>Remplissage d'enseignement moyenne</h2>";

	echo "<p>Classe(s) sélectionnée(s)&nbsp;: ";
	for($loop=0;$loop<count($id_classe);$loop++) {
		if($loop>0) {
			echo ", ";
		}
		echo get_nom_classe($id_classe[$loop]);
	}
	echo "</p>";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

	echo "<p style='margin-top:1em;'><b>Choix des matières&nbsp;:</b><br />\n";

	$sql_classes="";
	for($loop=0;$loop<count($id_classe);$loop++) {
		echo "
		<input type='hidden' name='id_classe[]' value='".$id_classe[$loop]."' />";
		if($loop>0) {
			$sql_classes.=" OR ";
		}
		$sql_classes.="jgc.id_classe='".$id_classe[$loop]."'";
	}

	$sql="SELECT DISTINCT m.* FROM matieres m, 
						j_groupes_matieres jgm, 
						j_groupes_classes jgc 
					WHERE m.matiere=jgm.id_matiere AND 
						jgm.id_groupe=jgc.id_groupe AND 
						(".$sql_classes.") 
					ORDER BY m.matiere;";
	$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_mat=mysqli_num_rows($res_mat);

	$tab_mat=array();
	while($lig_mat=mysqli_fetch_assoc($res_mat)) {
		$tab_mat[]=$lig_mat;
	}

	// Affichage sur 3 colonnes
	$nb_mat_par_colonne=round($nb_mat/3);


	echo "<p style='margin-top:1em;margin-left:1em;'><b>Matière des enseignements à remplir&nbsp;:</b></p>
	<div style='margin-left:3em;'>\n";

	echo "<table width='100%' summary='Choix de la matière'>\n";
	echo "<tr valign='top' align='center'>\n";

	$cpt = 0;

	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";

	for($loop=0;$loop<count($tab_mat);$loop++) {

		//affichage 2 colonnes
		if(($cpt>0)&&(round($cpt/$nb_mat_par_colonne)==$cpt/$nb_mat_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		echo "<label id='label_tab_mat_dest_$cpt' for='tab_mat_dest_$cpt' style='cursor: pointer;'><input type='radio' name='matiere_dest' id='tab_mat_dest_$cpt' value=\"".$tab_mat[$loop]["matiere"]."\" onchange='change_style_radio()' /> ".$tab_mat[$loop]["matiere"]." <em>(".$tab_mat[$loop]["nom_complet"].")</em></label>";
		echo "<br />\n";
		$cpt++;
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	//echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";
	echo "</div>\n";




	echo "<p style='margin-top:1em;margin-left:1em;'><b>Matières des enseignements source des notes et appréciations&nbsp;:</b></p>
	<div style='margin-left:3em;'>\n";

	echo "<table width='100%' summary='Choix des matières'>\n";
	echo "<tr valign='top' align='center'>\n";

	$cpt = 0;

	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";

	for($loop=0;$loop<count($tab_mat);$loop++) {

		//affichage 2 colonnes
		if(($cpt>0)&&(round($cpt/$nb_mat_par_colonne)==$cpt/$nb_mat_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		echo "<input type='checkbox' name='matiere[]' id='tab_mat_$cpt' value=\"".$tab_mat[$loop]["matiere"]."\" onchange='change_style_mat($cpt)' /><label id='label_tab_mat_$cpt' for='tab_mat_$cpt' style='cursor: pointer;'> ".$tab_mat[$loop]["matiere"]." <em>(".$tab_mat[$loop]["nom_complet"].")</em></label>";
		echo "<br />\n";
		$cpt++;
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";
	echo "</div>\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('tab_mat_'+k)){
				document.getElementById('tab_mat_'+k).checked = mode;
				change_style_mat(k);
			}
		}
	}

	function change_style_mat(num) {
		if(document.getElementById('tab_mat_'+num)) {
			if(document.getElementById('tab_mat_'+num).checked) {
				document.getElementById('label_tab_mat_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_tab_mat_'+num).style.fontWeight='normal';
			}
		}
	}

	function change_style_mat_dest(num) {
		if(document.getElementById('tab_mat_dest_'+num)) {
			if(document.getElementById('tab_mat_dest_'+num).checked) {
				document.getElementById('label_tab_mat_dest_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_tab_mat_dest_'+num).style.fontWeight='normal';
			}
		}
	}

	".js_change_style_radio("change_style_radio", "n", "y", 'checkbox_change', 'label_')."
</script>";

	require("../lib/footer.inc.php");
	die();

}

//=================================================================================================
// Choix des enseignements

//if((!isset($id_groupe))||(!isset($id_groupe_dest))) {
if(!isset($id_groupe_dest)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choix des classes</a>";

	$chaine_retour="?";
	for($loop=0;$loop<count($id_classe);$loop++) {
		if($loop>0) {
			$chaine_retour.="&amp;";
		}
		$chaine_retour.="id_classe[]=".$id_classe[$loop];
	}
	echo " | <a href='".$_SERVER['PHP_SELF'].$chaine_retour."'>Choix des matières</a></p>

	<h2>Remplissage d'enseignement moyenne</h2>";


	$tab_domaines_check=array();
	$tab_domaines_img=array();
	if(getSettingAOui("active_bulletins")) {
		$tab_domaines_check[]="bulletins";
		$tab_domaines_img["bulletins"]="<img src='../images/icons/bulletin_16.png' class='icone16' alt='Bull' title=\"Visible sur les bulletins\" />";
	}
	if(getSettingAOui("active_carnets_notes")) {
		$tab_domaines_check[]="cahier_notes";
		$tab_domaines_img["cahier_notes"]="<img src='../images/icons/cn_16.png' class='icone16' alt='Carnet Notes' title=\"Visible pour les carnets de notes\" />";
	}
	if(getSettingAOui("active_cahiers_texte")) {
		$tab_domaines_check[]="cahier_texte";
		$tab_domaines_img["cahier_texte"]="<img src='../images/icons/cahier_textes.png' class='icone16' alt='Cahier textes' title=\"Visible pour les cahiers de textes\" />";
	}


	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>
		".add_token_field()."
		<input type='hidden' name='matiere_dest' value='".$matiere_dest."' />";
	for($loop=0;$loop<count($matiere);$loop++) {
		echo "
		<input type='hidden' name='matiere[]' value='".$matiere[$loop]."' />";
	}
/*
	echo "<p><b>Nouvel enseignement&nbsp;:</b><br />
	Créer un nouvel enseignement de&nbsp;:</p>\n";

Matière
Visibilité
Coef

	echo "<p>Un nouvel enseignement sera créé dans chacune des classes qui suivent.<br />
	Vous allez choisir ci-dessous les enseignements dont il faudra faire les moyennes et concaténer les appréciations.</p>";
*/

	echo "<p><b>Paramètres&nbsp;:</b></p>
<div style='margin-left:3em;'>
	<p style='margin-left:3em;text-indent:-3em;'>
		Dans le cas où aucune moyenne ne peut être calculée pour un élève sur une période faute de note <em>(élève absent, non noté ou dispensé)</em>,<br />

		<input type='radio' name='conserver_note_precedente' id='conserver_note_precedente_y' value='y' onchange='change_style_radio()' checked /><label for='conserver_note_precedente_y' id='texte_conserver_note_precedente_y' style='font-weight:bold'> conserver l'enregistrement précédent</label><br />
		ou<br />

		<input type='radio' name='conserver_note_precedente' id='conserver_note_precedente_n' value='n' onchange='change_style_radio()' /><label for='conserver_note_precedente_n' id='texte_conserver_note_precedente_n'> commencer par vider les notes des bulletins pour les enseignements destination des moyennes</label>.
	</p>

	<p style='margin-top:1em; margin-left:3em;text-indent:-3em;'>
		Lors de la concaténation des appréciations des différents enseignements,<br />
		<input type='radio' name='separateur_app' id='separateur_app_nom_court_matiere' value='nom_court_matiere' onchange='change_style_radio()' checked /><label for='separateur_app_nom_court_matiere' id='texte_separateur_app_nom_court_matiere' style='font-weight:bold'> faire précéder l'appréciation du nom court de matière,</label><br />

		<input type='radio' name='separateur_app' id='separateur_app_nom_complet_matiere' value='nom_complet_matiere' onchange='change_style_radio()' /><label for='separateur_app_nom_complet_matiere' id='texte_separateur_app_nom_complet_matiere'> faire précéder l'appréciation du nom complet de matière,</label><br />

		<input type='radio' name='separateur_app' id='separateur_app_nom_groupe' value='nom_groupe' onchange='change_style_radio()' /><label for='separateur_app_nom_groupe' id='texte_separateur_app_nom_groupe'> faire précéder l'appréciation du nom court de l'enseignement,</label><br />

		<input type='radio' name='separateur_app' id='separateur_app_description_groupe' value='description_groupe' onchange='change_style_radio()' /><label for='separateur_app_description_groupe' id='texte_separateur_app_description_groupe'> faire précéder l'appréciation du nom complet (description) de l'enseignement,</label><br />

		<input type='radio' name='separateur_app' id='separateur_app_retour_ligne' value='retour_ligne' onchange='change_style_radio()' /><label for='separateur_app_retour_ligne' id='texte_separateur_app_retour_ligne'> juste faire un retour à la ligne.</label><br />
	</p>
</div>
<br />";

	$tab_champs_grp=array("classes", "matieres", "profs", "visibilite");

	echo "<p><b>Choix des enseignements&nbsp;:</b><br />
	Les enregistrements ne seront effectués que pour les périodes pour lesquelles les élèves sont inscrits dans les groupes destination.</p>\n";

	//$tab_id_app_per=array();
	for($loop=0;$loop<count($id_classe);$loop++) {
		$sql="SELECT MAX(num_periode) AS maxper FROM periodes WHERE id_classe='".$id_classe[$loop]."';";
		$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_per)==0) {
			echo "
		<p class='bold' style='color:red'>".get_nom_classe($id_classe[$loop])."&nbsp;: Pas de périodes</p>";
		}
		else {
			$lig_per=mysqli_fetch_object($res_per);
			echo "
		<p class='bold'>".get_nom_classe($id_classe[$loop])."</p>
		<input type='hidden' name='id_classe[]' value='".$id_classe[$loop]."' />
		<div style='margin-left:3em;'>";
			$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgc.id_groupe=jgm.id_groupe AND jgm.id_matiere='".$matiere_dest."' ANd jgc.id_classe='".$id_classe[$loop]."';";
			$res_grp_dest=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_grp_dest)==0) {
				echo "
			<p class='bold' style='color:red'>Aucun enseignement de $matiere_dest dans cette classe.</p>";
			}
			else {
				echo "
			<p style='margin-left:3em;text-indent:-3em;'>Enseignement à remplir&nbsp;:<br />";
				while($lig_grp_dest=mysqli_fetch_object($res_grp_dest)) {
					$current_group=get_group($lig_grp_dest->id_groupe, $tab_champs_grp);
					echo "
				<input type='radio' name='id_groupe_dest[".$id_classe[$loop]."]' id='id_groupe_dest_".$id_classe[$loop]."_".$lig_grp_dest->id_groupe."' value='".$lig_grp_dest->id_groupe."' onchange=\"change_style_radio(); document.getElementById('texte_id_groupe_dest_".$id_classe[$loop]."_".$lig_grp_dest->id_groupe."').style.color='black';\" /><label for='id_groupe_dest_".$id_classe[$loop]."_".$lig_grp_dest->id_groupe."' id='texte_id_groupe_dest_".$id_classe[$loop]."_".$lig_grp_dest->id_groupe."' style='color:red'>".$current_group['name']." (".$current_group['description'].")</em> <em title='Matière'>(".$current_group['matiere']['matiere'].")</em> <em title='Professeur(s)'>(".$current_group['profs']['proflist_string'].")</em> "."</label><br />";
				}
				echo "
			</p>
			<p>Enseignements source des notes et appréciations&nbsp;:</p>
			<table class='boireaus boireaus_alt'>
				<thead>
					<tr>
						<th rowspan='2'>Matière</th>
						<th colspan='4'>Enseignement</th>
						<th colspan='".$lig_per->maxper."'>Coefficients</th>
						<th colspan='".$lig_per->maxper."'>Appréciations</th>
					</tr>
					<tr>
						<th>Nom</th>
						<th>Description</th>
						<th>Classes</th>
						<th>Visibilité</th>";
				for($loop_per=1;$loop_per<=$lig_per->maxper;$loop_per++) {
					echo "
						<th title=\"Période $loop_per\">P$loop_per<br />
							<select name='coef_".$id_classe[$loop]."_TOUS_GRP[$loop_per]' id='coef_".$id_classe[$loop]."_TOUS_GRP_$loop_per' onchange=\"change_coef_".$id_classe[$loop]."_TOUS_GRP($loop_per)\" title=\"Imposer le même coefficient pour tous les enseignements de la période $loop_per.\nAvec un coefficient à zéro, l'enseignement ne sera pas pris en compte dans le calcul de la moyenne sur cette période.\">
								<option value=''>---</option>";
								for($i=0;$i<50;$i++) {
									echo "
								<option value='$i'>$i</option>";
								}
								echo "
							</select>
						</th>";
				}
				for($loop_per=1;$loop_per<=$lig_per->maxper;$loop_per++) {
					$chaine_app_per[$loop_per]="";
					echo "
						<th title=\"Période $loop_per\"><a href='#' onclick=\"check_app_per_".$id_classe[$loop]."_".$loop_per."(); return false;\">P$loop_per</a></th>";
				}
				echo "
					</tr>
				</thead>
				<tbody>";
				$tab_tmp_grp_id=array();
				for($loop2=0;$loop2<count($matiere);$loop2++) {
					$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, 
								j_groupes_matieres jgm 
							WHERE jgc.id_groupe=jgm.id_groupe AND 
								jgc.id_classe='".$id_classe[$loop]."' AND 
								jgm.id_matiere='".$matiere[$loop2]."';";
					$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_grp)>0) {
						while($lig_grp=mysqli_fetch_object($res_grp)) {
							$current_group=get_group($lig_grp->id_groupe, $tab_champs_grp);
							$tab_tmp_grp_id[]=$lig_grp->id_groupe;

							$chaine_visibilite="";
							for($loop3=0;$loop3<count($tab_domaines_check);$loop3++) {
								if((!isset($current_group['visibilite'][$tab_domaines_check[$loop3]]))||($current_group['visibilite'][$tab_domaines_check[$loop3]]!="n")) {
									$chaine_visibilite.=" ".$tab_domaines_img[$tab_domaines_check[$loop3]];
								}
							}

							echo "
					<tr>
						<td>".$matiere[$loop2]."</td>
						<td>
							".$current_group['name']."
							<input type='hidden' name='id_groupe_".$id_classe[$loop]."[]' value='".$lig_grp->id_groupe."' />
						</td>
						<td>".$current_group['description']."</td>
						<td>".$current_group['classlist_string']."</td>
						<td>".$chaine_visibilite."</td>";
							for($loop_per=1;$loop_per<=$lig_per->maxper;$loop_per++) {
								echo "
						<td>
							<select name='coef_".$id_classe[$loop]."_".$lig_grp->id_groupe."[$loop_per]' id='coef_".$id_classe[$loop]."_".$lig_grp->id_groupe."_$loop_per' title=\"Avec un coefficient à zéro, l'enseignement ne sera pas pris en compte dans le calcul de la moyenne sur cette période.\">";
								for($i=0;$i<50;$i++) {
									echo "
								<option value='$i'>$i</option>";
								}
								echo "
							</select>
						</td>";
							}
							for($loop_per=1;$loop_per<=$lig_per->maxper;$loop_per++) {
								//$tab_id_app_per[$id_classe[$loop]][$loop_per][]="app_".$id_classe[$loop]."_".$lig_grp->id_groupe."_".$loop_per;

								$chaine_app_per[$loop_per].="document.getElementById('app_".$id_classe[$loop]."_".$lig_grp->id_groupe."_".$loop_per."').checked=true;\n";

								echo "
						<td>
							<input type='checkbox' name='app_".$id_classe[$loop]."_".$lig_grp->id_groupe."[$loop_per]' id='app_".$id_classe[$loop]."_".$lig_grp->id_groupe."_$loop_per' value='y' \">
						</td>";
							}
							echo "
					</tr>";
						}
					}
				}
				echo "
				</tbody>
			</table>
			<script type='text/javascript'>
				function change_coef_".$id_classe[$loop]."_TOUS_GRP(num_per) {
					if(document.getElementById('coef_".$id_classe[$loop]."_TOUS_GRP_'+num_per)) {
						if(document.getElementById('coef_".$id_classe[$loop]."_TOUS_GRP_'+num_per).selectedIndex>0) {";
					for($loop_grp_id=0;$loop_grp_id<count($tab_tmp_grp_id);$loop_grp_id++) {
						echo "
							document.getElementById('coef_".$id_classe[$loop]."_".$tab_tmp_grp_id[$loop_grp_id]."_'+num_per).selectedIndex=eval(document.getElementById('coef_".$id_classe[$loop]."_TOUS_GRP_'+num_per).selectedIndex-1);";
					}
					echo "
						}
					}
				}";

				for($loop_per=1;$loop_per<=$lig_per->maxper;$loop_per++) {
					echo "
				function check_app_per_".$id_classe[$loop]."_".$loop_per."() {
					".$chaine_app_per[$loop_per]."
				}";
				}

				echo "
			</script>
			<p><br /></p>
		</div>";
			}
		}
	}

//$tab_domaines=array('bulletins', 'cahier_notes', 'cahier_texte');
/*
echo "<pre>";
print_r($current_group);
echo "</pre>";
*/

	//echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";

	echo "<p style='margin-bottom:2em;'><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	//echo js_change_style_radio("change_style_radio", "y", "y", 'checkbox_change', 'texte_');

	echo "<script type='text/javascript'>
	".js_change_style_radio("change_style_radio")."

	function checkbox_change(id) {
		//alert(id);
		if(document.getElementById(id)) {
			if(document.getElementById('texte_'+id)) {
				//alert('texte_'+id);
				if(document.getElementById(id).checked) {
					document.getElementById('texte_'+id).style.fontWeight='bold';
				}
				else {
					document.getElementById('texte_'+id).style.fontWeight='normal';
				}
			}
		}
	}
</script>\n";

	/*
	echo "<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('tab_mat_'+k)){
				document.getElementById('tab_mat_'+k).checked = mode;
				change_style_mat(k);
			}
		}
	}

	function change_style_mat(num) {
		if(document.getElementById('tab_mat_'+num)) {
			if(document.getElementById('tab_mat_'+num).checked) {
				document.getElementById('label_tab_mat_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_tab_mat_'+num).style.fontWeight='normal';
			}
		}
	}

</script>";
	*/

	echo "<p style='margin-top:1em; margin-bottom:2em; margin-left:6.7em;text-indent:-6.7em;'><em>ATTENTION&nbsp;:</em> L'opération est irréversible pour les groupes destination.<br />Les saisies antérieures dans ces groupes pour les périodes sélectionnées ne pourront pas être récupérées après écrasement.</p>\n";

	echo "<p style='color:red'>A FAIRE : Ajouter des facilités pour cocher les appréciations, pour imposer un même coef (en entête de colonne période de chaque tableau, avant chaque tableau, et globalement),<br />
	Cocher par défaut le groupe destination s'il n'y en a qu'un ou mettre un témoin drapeau si aucune groupe destination n'est sélectionné.</p>";

	require("../lib/footer.inc.php");
	die();

}


//=================================================================================================
// Action
check_token(false);

echo " | <a href='".$_SERVER['PHP_SELF']."'>Choix des classes</a>";

$chaine_retour="?";
for($loop=0;$loop<count($id_classe);$loop++) {
	if($loop>0) {
		$chaine_retour.="&amp;";
	}
	$chaine_retour.="id_classe[]=".$id_classe[$loop];
}
echo " | <a href='".$_SERVER['PHP_SELF'].$chaine_retour."'>Choix des matières</a>";

$chaine_retour.="&amp;matiere_dest=$matiere_dest";
for($loop=0;$loop<count($matiere);$loop++) {
	//if($loop>0) {
		$chaine_retour.="&amp;";
	//}
	$chaine_retour.="matiere[]=".$matiere[$loop];
}
echo " | <a href='".$_SERVER['PHP_SELF'].$chaine_retour."'>Choix des enseignements</a></p>

<h2>Remplissage d'enseignement moyenne</h2>";

$conserver_note_precedente=isset($_POST['conserver_note_precedente']) ? $_POST['conserver_note_precedente'] : (isset($_GET['conserver_note_precedente']) ? $_GET['conserver_note_precedente'] : "y");
$separateur_app=isset($_POST['separateur_app']) ? $_POST['separateur_app'] : (isset($_GET['separateur_app']) ? $_GET['separateur_app'] : "nom_court_matiere");

if($conserver_note_precedente=="n") {
	for($loop=0;$loop<count($id_classe);$loop++) {
		if(isset($id_groupe_dest[$id_classe[$loop]])) {
			$tab_grp_src=$_POST["id_groupe_".$id_classe[$loop]];

			$sql="SELECT MAX(num_periode) AS maxper FROM periodes WHERE id_classe='".$id_classe[$loop]."';";
			$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_per)>0) {
				$lig_per=mysqli_fetch_object($res_per);

				$tab_grp_src_moy=array();
				for($loop2=0;$loop2<count($tab_grp_src);$loop2++) {
					$current_id_groupe_src=$tab_grp_src[$loop2];

					if(isset($_POST['coef_'.$id_classe[$loop].'_'.$current_id_groupe_src])) {
						$tab_coef=$_POST['coef_'.$id_classe[$loop].'_'.$current_id_groupe_src];
						for($loop_per=1;$loop_per<=$lig_per->maxper;$loop_per++) {
							if((isset($tab_coef[$loop_per]))&&($tab_coef[$loop_per]!=0)) {
								echo "<p>Suppression des notes de l'enseignement à remplir ".get_info_grp($id_groupe_dest[$id_classe[$loop]])." en période $loop_per&nbsp;: ";
								$sql="DELETE FROM matieres_notes WHERE id_groupe='".$id_groupe_dest[$id_classe[$loop]]."' AND periode='".$loop_per."';";
								//echo "$sql<br />";
								$del=mysqli_query($GLOBALS["mysqli"], $sql);
								if($del) {
									echo "<span style='color:green'>OK</span>";
								}
								else {
									echo "<span style='color:red'>Echec</span>";
								}
							}
						}
					}
				}
			}
		}
	}
}

for($loop=0;$loop<count($id_classe);$loop++) {
	echo "<p class='bold'>Classe de&nbsp;: ".get_nom_classe($id_classe[$loop])."</p>
<div style='margin-left:3em;'>";
	if(!isset($id_groupe_dest[$id_classe[$loop]])) {
		echo "<p style='color:red'>Aucun enseignement à remplir n'a été choisi.</p>";
	}
	else {
		$nb_reg=0;
		echo "<p class='bold'>Enseignement à remplir&nbsp;: ".get_info_grp($id_groupe_dest[$id_classe[$loop]])."</p>";

		if(!isset($_POST["id_groupe_".$id_classe[$loop]])) {
			echo "<p style='color:red; margin-left:3em;'>Aucun enseignement source des moyennes et appréciations pour cette classe.</p>";
		}
		else {
			//$tab_grp_src=array();
			$tab_grp_src=$_POST["id_groupe_".$id_classe[$loop]];


			$sql="SELECT MAX(num_periode) AS maxper FROM periodes WHERE id_classe='".$id_classe[$loop]."';";
			$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_per)==0) {
				echo "
			<p class='bold' style='color:red; margin-left:3em;'>".get_nom_classe($id_classe[$loop])."&nbsp;: Pas de périodes</p>";
			}
			else {
				$lig_per=mysqli_fetch_object($res_per);

				// Récupérer le groupe dest pour tester l'inscription des élèves sur telle période
				//echo "<p>Enseignement destination&nbsp;: ".get_info_grp($id_groupe_dest[$id_classe[$loop]])."</p>";
				$current_group_dest=get_group($id_groupe_dest[$id_classe[$loop]]);

				$tab_grp_src_moy=array();
				for($loop2=0;$loop2<count($tab_grp_src);$loop2++) {
					$current_id_groupe_src=$tab_grp_src[$loop2];

					if(isset($_POST['coef_'.$id_classe[$loop].'_'.$current_id_groupe_src])) {
						$tab_coef=$_POST['coef_'.$id_classe[$loop].'_'.$current_id_groupe_src];
						for($loop_per=1;$loop_per<=$lig_per->maxper;$loop_per++) {
							if((isset($tab_coef[$loop_per]))&&($tab_coef[$loop_per]!=0)) {
								$tab_grp_src_moy[$loop_per][$current_id_groupe_src]["coef"]=$tab_coef[$loop_per];

								$sql="SELECT * FROM matieres_notes WHERE id_groupe='".$current_id_groupe_src."' AND periode='".$loop_per."' AND statut='';";
								//echo "$sql<br />";
								$res_note=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_note)>0) {
									while($lig_note=mysqli_fetch_assoc($res_note)) {
										$tab_grp_src_moy[$loop_per][$current_id_groupe_src]["login"][$lig_note["login"]]["note"]=$lig_note["note"];
									}
								}
							}
						}
					}
					/*
					else {
						echo "\$_POST['coef_'.$id_classe[$loop].'_'.$current_id_groupe_src] non défini.<br />";
					}
					*/

					if(isset($_POST['app_'.$id_classe[$loop].'_'.$current_id_groupe_src])) {
						$tab_app=$_POST['app_'.$id_classe[$loop].'_'.$current_id_groupe_src];
						for($loop_per=1;$loop_per<=$lig_per->maxper;$loop_per++) {
							if(isset($tab_app[$loop_per])) {
								$tab_grp_src_moy[$loop_per][$current_id_groupe_src]["app"]="y";

								$sql="SELECT * FROM matieres_appreciations WHERE id_groupe='".$current_id_groupe_src."' AND periode='".$loop_per."';";
								//echo "$sql<br />";
								$res_app=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_app)>0) {
									while($lig_app=mysqli_fetch_assoc($res_app)) {
										$tab_grp_src_moy[$loop_per][$current_id_groupe_src]["login"][$lig_app["login"]]["appreciation"]=$lig_app["appreciation"];
									}
								}
							}
						}
					}
				}

				/*
				echo "<pre>";
				print_r($tab_grp_src_moy);
				echo "</pre>";
				*/

				foreach($tab_grp_src_moy as $periode => $tab_tmp) {
					$tab_total_notes=array();
					$tab_total_coef=array();
					$tab_app_concat=array();
					foreach($tab_tmp as $current_id_groupe_src => $tab_app_coef) {
						if(isset($tab_app_coef["login"])) {
							if(isset($tab_app_coef["coef"])) {
								foreach($tab_app_coef["login"] as $current_login => $tab_ele) {
									if(isset($tab_ele["note"])) {
										if(!isset($tab_total_notes[$current_login])) {
											$tab_total_notes[$current_login]=0;
											$tab_total_coef[$current_login]=0;
										}
										$tab_total_notes[$current_login]+=$tab_ele["note"];
										$tab_total_coef[$current_login]+=$tab_app_coef["coef"];
									}
								}
							}

							if(isset($tab_app_coef["app"])) {
								foreach($tab_app_coef["login"] as $current_login => $tab_ele) {
									if(isset($tab_ele["appreciation"])) {
										if(!isset($tab_app_concat[$current_login])) {
											$tab_app_concat[$current_login]="";
										}
										elseif(($tab_app_concat[$current_login]!="")&&($tab_ele["appreciation"]!="")) {
											$tab_app_concat[$current_login].="\n";
										}

										if($tab_ele["appreciation"]!="") {
											if($separateur_app=="nom_court_matiere") {
												if(!isset($tab_info_groupe[$current_id_groupe_src]["nom_court_matiere"])) {
													$tab_info_groupe[$current_id_groupe_src]["nom_court_matiere"]=get_valeur_champ("j_groupes_matieres", "id_groupe='".$current_id_groupe_src."'", "id_matiere");
												}

												$tab_app_concat[$current_login].="<b>".$tab_info_groupe[$current_id_groupe_src]["nom_court_matiere"]." :</b> ";
											}
											elseif($separateur_app=="nom_complet_matiere") {
												if(!isset($tab_info_groupe[$current_id_groupe_src]["nom_complet_matiere"])) {
													$sql="SELECT m.nom_complet FROM matieres m, j_groupes_matieres jgm WHERE m.matiere=jgm.id_matiere AND jgm.id_groupe='".$current_id_groupe_src."';";
													//echo "$sql<br />";
													$res_mat=mysqli_query($mysqli, $sql);
													if(mysqli_num_rows($res_mat)>0) {
														$lig_mat=mysqli_fetch_object($res_mat);
														$tab_info_groupe[$current_id_groupe_src]["nom_complet_matiere"]=$lig_mat->nom_complet;
													}
													else {
														$tab_info_groupe[$current_id_groupe_src]["nom_complet_matiere"]="-";
													}
												}

												$tab_app_concat[$current_login].="<b>".$tab_info_groupe[$current_id_groupe_src]["nom_complet_matiere"]." :</b> ";
											}
											elseif($separateur_app=="nom_groupe") {
												if(!isset($tab_info_groupe[$current_id_groupe_src]["nom_groupe"])) {
													$tab_info_groupe[$current_id_groupe_src]["nom_groupe"]=get_valeur_champ("groupes", "id='".$current_id_groupe_src."'", "name");
												}

												$tab_app_concat[$current_login].="<b>".$tab_info_groupe[$current_id_groupe_src]["nom_groupe"]." :</b> ";
											}
											elseif($separateur_app=="description_groupe") {
												if(!isset($tab_info_groupe[$current_id_groupe_src]["description_groupe"])) {
													$tab_info_groupe[$current_id_groupe_src]["description_groupe"]=get_valeur_champ("groupes", "id='".$current_id_groupe_src."'", "description");
												}

												$tab_app_concat[$current_login].="<b>".$tab_info_groupe[$current_id_groupe_src]["description_groupe"]." :</b> ";
											}

											$tab_app_concat[$current_login].=$tab_ele["appreciation"];
										}
									}
								}
							}
						}
					}

					/*
					echo "\$tab_total_notes<pre>";
					print_r($tab_total_notes);
					echo "</pre>";
					*/

					foreach($tab_total_notes as $current_login => $total) {
						if(in_array($current_login, $current_group_dest["eleves"][$periode]["list"])) {
							$note=round(10*$total/$tab_total_coef[$current_login])/10;

							$sql="SELECT 1=1 FROM matieres_notes WHERE id_groupe='".$id_groupe_dest[$id_classe[$loop]]."' AND periode='".$periode."' AND login='".$current_login."';";
							//echo "$sql<br />";
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test)>0) {
								$sql="UPDATE matieres_notes SET note='".$note."' WHERE id_groupe='".$id_groupe_dest[$id_classe[$loop]]."' AND periode='".$periode."' AND login='".$current_login."';";
							}
							else {
								$sql="INSERT INTO matieres_notes SET id_groupe='".$id_groupe_dest[$id_classe[$loop]]."', periode='".$periode."', login='".$current_login."', note='".$note."';";
							}
							//echo "$sql<br />";
							$update=mysqli_query($mysqli, $sql);
							if($update) {
								$nb_reg++;
							}
							else {
								echo "<span style='color:red'>Erreur&nbsp;: $sql</span><br />";
							}
						}
						/*
						else {
							echo "$current_login n'est pas dans \$current_group_dest[\"eleves\"][$periode][\"list\"]<br />";
						}
						*/
					}

					foreach($tab_app_concat as $current_login => $appreciation) {
						if(in_array($current_login, $current_group_dest["eleves"][$periode]["list"])) {
							$sql="SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='".$id_groupe_dest[$id_classe[$loop]]."' AND periode='".$periode."' AND login='".$current_login."';";
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test)>0) {
								$sql="UPDATE matieres_appreciations SET appreciation='".mysqli_real_escape_string($mysqli, $appreciation)."' WHERE id_groupe='".$id_groupe_dest[$id_classe[$loop]]."' AND periode='".$periode."' AND login='".$current_login."';";
							}
							else {
								$sql="INSERT INTO matieres_appreciations SET id_groupe='".$id_groupe_dest[$id_classe[$loop]]."', periode='".$periode."', login='".$current_login."', appreciation='".mysqli_real_escape_string($mysqli, $appreciation)."';";
							}
							//echo "$sql<br />";
							$update=mysqli_query($mysqli, $sql);
							if($update) {
								$nb_reg++;
							}
							else {
								echo "<span style='color:red'>Erreur&nbsp;: $sql</span><br />";
							}
						}
					}


				}
			}
		}
		echo "<p>$nb_reg enregistrement(s) effectué(s).</p>";
	}
	echo "
</div>";
}

echo "<p style='margin-top:1em;'><em>Rappel&nbsp;:</em> Les enregistrements ne seront effectués que pour les périodes pour lesquelles les élèves sont inscrits dans les groupes destination.</p>\n";

require("../lib/footer.inc.php");
?>
