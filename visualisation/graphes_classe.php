<?php
/**
 *
 * Copyright 2015 Stephane Boireau
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

$sql="SELECT 1=1 FROM droits WHERE id='/visualisation/graphes_classe.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/visualisation/graphes_classe.php',
administrateur='F',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Tous les graphes sur une page pour une classe donnée',
statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$id_classe=isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] : NULL);
$num_periode=isset($_POST["num_periode"]) ? $_POST["num_periode"] :(isset($_GET["num_periode"]) ? $_GET["num_periode"] : NULL);
$afficher_graphes=isset($_POST["afficher_graphes"]) ? $_POST["afficher_graphes"] :(isset($_GET["afficher_graphes"]) ? $_GET["afficher_graphes"] : NULL);

//debug_var();

if(isset($_POST['valider_param_graphes'])) {
	check_token();

	$tab_champs=array('graphe_affiche_moy_classe', 'graphe_affiche_minmax', 'graphe_largeur_graphe', 'graphe_hauteur_graphe', 'graphe_taille_police', 'graphe_epaisseur_traits', 'graphe_epaisseur_croissante_traits_periodes', 'graphe_temoin_image_escalier', 'graphe_tronquer_nom_court', 'graphe_affiche_mgen', 'graphe_affiche_moy_annuelle', 'graphe_inserer_saut_page', 'nb_graphes_saut_page');
	for($loop=0;$loop<count($tab_champs);$loop++) {
		if(isset($_POST[$tab_champs[$loop]])) {
			$_SESSION[$tab_champs[$loop]]=$_POST[$tab_champs[$loop]];
		}
	}
}

//**************** DEBUT EN-TETE ***************
if(!isset($afficher_graphes)) {
	$titre_page = "Graphes classe";
}
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "
<p style='margin-bottom:1em;' class='noprint'>
	<a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

$taille_max_police=10;

if(!isset($id_classe)) {
	echo "</p>
<h1>Graphes</h1>";

	$tab_clas=array();
	$sql=retourne_sql_mes_classes();
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig=mysqli_fetch_object($res)) {
		$tab_clas[$lig->id_classe]=$lig->classe;
	}

	$tab_autres_classes=array();
	if(($_SESSION['statut']=='scolarite')||
	($_SESSION['statut']=='cpe')||
	(($_SESSION['statut']=='professeur')&&((getSettingAOui('GepiAccesMoyennesProfToutesClasses'))||(getSettingAOui('GepiAccesBulletinSimpleProfToutesClasses'))))) {

		$sql="SELECT c.id AS id_classe, c.classe FROM classes c, periodes p WHERE p.id_classe=c.id ORDER BY classe;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig=mysqli_fetch_object($res)) {
			if(!array_key_exists($lig->id_classe, $tab_clas)) {
				$tab_autres_classes[$lig->id_classe]=$lig->classe;
			}
		}
	}

	if((count($tab_clas)==0)&&(count($tab_autres_classes)==0)) {
		echo "<p style='color:red'>Aucune classe n'a été trouvée.</p>";
	}
	else {
		if(count($tab_clas)>0) {
			echo "<p>Choisissez parmi vos classes&nbsp;:</p>";
			$tab_txt=array();
			$tab_lien=array();
			foreach($tab_clas as $current_id_classe => $current_classe) {
				$tab_txt[]=$current_classe;
				$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$current_id_classe;
			}
			$nbcol=3;
			tab_liste($tab_txt,$tab_lien,$nbcol);
			echo "<br />";
		}

		if(count($tab_autres_classes)>0) {
			echo "<p>Choisissez parmi les classes que vous n'avez pas en responsabilité&nbsp;:</p>";
			$tab_txt=array();
			$tab_lien=array();
			foreach($tab_autres_classes as $current_id_classe => $current_classe) {
				$tab_txt[]=$current_classe;
				$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$current_id_classe;
			}
			$nbcol=3;
			tab_liste($tab_txt,$tab_lien,$nbcol);
		}
	}

	require_once("../lib/footer.inc.php");
	die();
}
elseif(!isset($num_periode)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a></p>
<h1>Graphes de la classe de ".get_nom_classe($id_classe)."</h1>
<p style='text-indent:-3em; margin-left:3em;'>Choisissez une période&nbsp;:<br />";

	$sql="SELECT p.* FROM periodes p WHERE p.id_classe='$id_classe' ORDER BY num_periode;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig=mysqli_fetch_object($res)) {
		echo "
	<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;num_periode=$lig->num_periode'>$lig->nom_periode</a><br />";
	}
	echo "
	<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;num_periode=toutes'>Toutes les périodes</a><br />
</p>";

	require_once("../lib/footer.inc.php");
	die();
}

// Mettre là la récupération des paramètres communs aux différents choix période
include("param_graphes.php");

if(!isset($afficher_graphes)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a> | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Choisir une autre période</a>
</p>";

	// Formulaire pour modifier les paramètres, insérer des sauts de page

	if((isset($_SESSION['graphe_affiche_mgen']))&&($_SESSION['graphe_affiche_mgen']=="non")) {
		$checked_affiche_mgen_oui="";
		$checked_affiche_mgen_non=" checked";
	}
	else {
		$checked_affiche_mgen_oui=" checked";
		$checked_affiche_mgen_non="";
	}

	if((isset($_SESSION['graphe_affiche_moy_classe']))&&($_SESSION['graphe_affiche_moy_classe']=="non")) {
		$checked_affiche_moy_classe_oui="";
		$checked_affiche_moy_classe_non=" checked";
	}
	else {
		$checked_affiche_moy_classe_oui=" checked";
		$checked_affiche_moy_classe_non="";
	}

	if((isset($_SESSION['graphe_affiche_minmax']))&&($_SESSION['graphe_affiche_minmax']=="non")) {
		$checked_affiche_minmax_oui="";
		$checked_affiche_minmax_non=" checked";
	}
	else {
		$checked_affiche_minmax_oui=" checked";
		$checked_affiche_minmax_non="";
	}

	if((isset($_SESSION['graphe_affiche_moy_annuelle']))&&($_SESSION['graphe_affiche_moy_annuelle']=="oui")) {
		$checked_affiche_moy_annuelle_oui=" checked";
		$checked_affiche_moy_annuelle_non="";
	}
	else {
		$checked_affiche_moy_annuelle_oui="";
		$checked_affiche_moy_annuelle_non=" checked";
	}


	if((isset($_SESSION['graphe_inserer_saut_page']))&&($_SESSION['graphe_inserer_saut_page']=="y")) {
		$checked_graphe_inserer_saut_page_n="";
		$checked_graphe_inserer_saut_page_y=" checked";
	}
	else {
		$checked_graphe_inserer_saut_page_n=" checked";
		$checked_graphe_inserer_saut_page_y="";
	}

	if(isset($_SESSION['nb_graphes_saut_page'])) {
		for($loop=0;$loop<6;$loop++) {
			$selected_nb_graphe_inserer_saut_page[$loop]="";
		}
		$selected_nb_graphe_inserer_saut_page[$_SESSION['nb_graphes_saut_page']]=" selected";
	}
	else {
		for($loop=1;$loop<6;$loop++) {
			$selected_nb_graphe_inserer_saut_page[$loop]="";
		}
		$selected_nb_graphe_inserer_saut_page[0]=" selected";
	}


	if((isset($_SESSION['graphe_epaisseur_croissante_traits_periodes']))&&($_SESSION['graphe_epaisseur_croissante_traits_periodes']=="oui")) {
		$checked_epaisseur_croissante_traits_periodes_oui=" checked";
		$checked_epaisseur_croissante_traits_periodes_non="";
	}
	else {
		$checked_epaisseur_croissante_traits_periodes_oui="";
		$checked_epaisseur_croissante_traits_periodes_non=" checked";
	}

	if((isset($_SESSION['graphe_temoin_image_escalier']))&&($_SESSION['graphe_temoin_image_escalier']=="non")) {
		$checked_temoin_image_escalier_oui="";
		$checked_temoin_image_escalier_non=" checked";
	}
	else {
		$checked_temoin_image_escalier_oui=" checked";
		$checked_temoin_image_escalier_non="";
	}

	if(isset($_SESSION['graphe_tronquer_nom_court'])) {
		for($loop=0;$loop<6;$loop++) {
			$selected_graphe_tronquer_nom_court[$loop]="";
		}
		$selected_graphe_tronquer_nom_court[$_SESSION['graphe_tronquer_nom_court']]=" selected";
	}
	else {
		for($loop=1;$loop<6;$loop++) {
			$selected_graphe_tronquer_nom_court[$loop]="";
		}
		$selected_graphe_tronquer_nom_court[0]=" selected";
	}

	if(isset($_SESSION['graphe_epaisseur_traits'])) {
		for($loop=0;$loop<6;$loop++) {
			$selected_graphe_epaisseur_traits[$loop]="";
		}
		$selected_graphe_epaisseur_traits[$_SESSION['graphe_epaisseur_traits']]=" selected";
	}
	else {
		for($loop=1;$loop<6;$loop++) {
			$selected_graphe_epaisseur_traits[$loop]="";
		}
		$selected_graphe_epaisseur_traits[2]=" selected";
	}

	$taille_max_police=10;
	if(isset($_SESSION['graphe_taille_police'])) {
		//echo "\$_SESSION['graphe_taille_police']=".$_SESSION['graphe_taille_police']."<br />";
		for($loop=1;$loop<$taille_max_police;$loop++) {
			$selected_graphe_taille_police[$loop]="";
		}
		$selected_graphe_taille_police[$_SESSION['graphe_taille_police']]=" selected";
	}
	else {
		for($loop=1;$loop<$taille_max_police;$loop++) {
			$selected_graphe_taille_police[$loop]="";
		}
		$selected_graphe_taille_police[6]=" selected";
	}



	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_param' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<h1>Graphes</h1>
		<p class='bold'>Vous allez afficher les graphes pour la classe de ".get_nom_classe($id_classe).(($num_periode=="toutes") ? " pour toutes les périodes" : " en période ".$num_periode)."</p>

		<p><input type='submit' value='Valider' /></p>
		<table class='boireaus boireaus_alt'>
			<tr valign='top'>
				<td style='text-align:left'>Afficher la moyenne générale:</td>
				<td>
					<input type='radio' name='graphe_affiche_mgen' id='affiche_mgen_oui' value='oui'$checked_affiche_mgen_oui /><label for='affiche_mgen_oui' style='cursor: pointer;'> Oui </label>
					/
					<label for='affiche_mgen_non' style='cursor: pointer;'> Non </label><input type='radio' name='graphe_affiche_mgen' id='affiche_mgen_non' value='non'$checked_affiche_mgen_non />
				</td>
			</tr>
			<tr valign='top'>
				<td style='text-align:left'>
					Afficher la moyenne de la classe:<br />
					(<i>cet affichage n'est pas appliqué en mode 'Toutes_les_periodes'</i>)
				</td>
				<td>
					<input type='radio' name='graphe_affiche_moy_classe' id='affiche_moy_classe_oui' value='oui'$checked_affiche_moy_classe_oui /><label for='affiche_moy_classe_oui' style='cursor: pointer;'> Oui </label>
					/
					<label for='affiche_moy_classe_non' style='cursor: pointer;'> Non </label><input type='radio' name='graphe_affiche_moy_classe' id='affiche_moy_classe_non' value='non'$checked_affiche_moy_classe_non /></label>
				</td>
			</tr>
			<tr valign='top'>
				<td style='text-align:left'>
					Afficher les bandes moyenne minimale/maximale:<br />
					(<i>cet affichage n'est pas appliqué en mode 'Toutes_les_periodes'</i>)
				</td>
				<td>
					<input type='radio' name='graphe_affiche_minmax' id='affiche_minmax_oui' value='oui'$checked_affiche_minmax_oui /><label for='affiche_minmax_oui' style='cursor: pointer;'> Oui </label>
					/
					<label for='affiche_minmax_non' style='cursor: pointer;'> Non </label><input type='radio' name='graphe_affiche_minmax' id='affiche_minmax_non' value='non'$checked_affiche_minmax_non /></label>
				</td>
			</tr>
			<tr valign='top'>
				<td style='text-align:left'>
					Afficher les moyennes annuelles:<br />
					(<i>en mode 'Toutes_les_periodes' uniquement</i>)
				</td>
				<td>
					<input type='radio' name='graphe_affiche_moy_annuelle' id='affiche_moy_annuelle_oui' value='oui'$checked_affiche_moy_annuelle_oui /><label for='affiche_moy_annuelle_oui' style='cursor: pointer;'> Oui </label>
					/
					<label for='affiche_moy_annuelle_non' style='cursor: pointer;'> Non </label><input type='radio' name='graphe_affiche_moy_annuelle' id='affiche_moy_annuelle_non' value='non'$checked_affiche_moy_annuelle_non />
				</td>
			</tr>

			<!-- ++++++++++++++++++++++++++++++++ -->

			<tr>
				<td style='text-align:left'>
					<label for='largeur_graphe' style='cursor: pointer;'>Largeur (<i>en pixels</i>):</label>
				</td>
				<td>
					<input type='text' name='graphe_largeur_graphe' id='largeur_graphe' value='$largeur_graphe' size='3' onkeydown=\"clavier_2(this.id,event,0,2000);\" />
				</td>
			</tr>
			<tr>
				<td style='text-align:left'>
					<label for='hauteur_graphe' style='cursor: pointer;'>Hauteur (<i>en pixels</i>):</label>
				</td>
				<td>
					<input type='text' name='graphe_hauteur_graphe' id='hauteur_graphe' value='$hauteur_graphe' size='3' onkeydown=\"clavier_2(this.id,event,0,2000);\" />
				</td>
			</tr>
			<tr>
				<td style='text-align:left'>
					<label for='taille_police' style='cursor: pointer;'>Taille des polices:</label>
				</td>
				<td>
					<select name='graphe_taille_police' id='taille_police'>\n";
	for($i=1;$i<=$taille_max_police;$i++) {
		echo "
					<option value='$i'".$selected_graphe_taille_police[$i].">$i</option>\n";
	}
	echo "
					</select>
				</td>
			</tr>
			<tr>
				<td style='text-align:left'>
					<label for='epaisseur_traits' style='cursor: pointer;'>Epaisseur des courbes:</label>
				</td>
				<td>
					<select name='graphe_epaisseur_traits' id='epaisseur_traits'>\n";
	for($i=1;$i<=6;$i++) {
		echo "
						<option value='$i'".$selected_graphe_epaisseur_traits[$i].">$i</option>";
	}
	echo "
					</select>
				</td>
			</tr>
			<tr>
				<td style='text-align:left'>
					Epaisseur croissante des courbes de période en période:
				</td>
				<td>
					<input type='radio' name='graphe_epaisseur_croissante_traits_periodes' id='epaisseur_croissante_traits_periodes_oui' value='oui'$checked_epaisseur_croissante_traits_periodes_oui /><label for='epaisseur_croissante_traits_periodes_oui' style='cursor: pointer;'> Oui </label>
					/
					<label for='epaisseur_croissante_traits_periodes_non' style='cursor: pointer;'> Non </label><input type='radio' name='graphe_epaisseur_croissante_traits_periodes' id='epaisseur_croissante_traits_periodes_non' value='non'$checked_epaisseur_croissante_traits_periodes_non />
				</td>
			</tr>

			<tr>
				<td style='text-align:left'>
					Afficher les noms longs de matières:<br />
					(<i>en légende sous le graphe</i>)
				</td>
				<td>
					<input type='radio' name='graphe_temoin_image_escalier' id='temoin_image_escalier_oui' value='oui'$checked_temoin_image_escalier_oui /><label for='temoin_image_escalier_oui' style='cursor: pointer;'> Oui </label>
					/
					<label for='temoin_image_escalier_non' style='cursor: pointer;'> Non </label><input type='radio' name='graphe_temoin_image_escalier' id='temoin_image_escalier_non' value='non'$checked_temoin_image_escalier_non />
				</td>
			</tr>
			<tr>
				<td style='text-align:left'>
					<label for='tronquer_nom_court' style='cursor: pointer;'>Tronquer le nom court de la matière à <a href='#' onclick='alert(\"A zéro caractères, on ne tronque pas le nom court de matière affiché en haut du graphe.\");return false;'>X</a> caractères:<br />
					(<i>pour éviter des collisions de légendes en haut du graphe</i>)</label>
				</td>
				<td>
					<select name='graphe_tronquer_nom_court' id='tronquer_nom_court'>\n";
	for($i=0;$i<=10;$i++) {
		echo "
						<option value='$i'".$selected_graphe_tronquer_nom_court[$i].">$i</option>";
	}
	echo "
					</select>
				</td>
			</tr>

		</table>


		<br />
		<p style='text-indent:-3em; margin-left:3em;'>Si vous comptez imprimer les graphes, il peut être commode d'insérer des sauts de page tous les N graphes.<br />
		<input type='radio' name='graphe_inserer_saut_page' id='graphe_inserer_saut_page_n' value='n' onchange=\"change_graphe_inserer_saut_page()\"$checked_graphe_inserer_saut_page_n /><label for='graphe_inserer_saut_page_n' id='texte_graphe_inserer_saut_page_n'> Ne pas insérer de saut de page</label><br />
		<input type='radio' name='graphe_inserer_saut_page' id='graphe_inserer_saut_page_y' value='y' onchange=\"change_graphe_inserer_saut_page()\"$checked_graphe_inserer_saut_page_y /><label for='graphe_inserer_saut_page_y' id='texte_graphe_inserer_saut_page_y'> Insérer un saut de page tous les </label>
		<select name='nb_graphes_saut_page' id='nb_graphes_saut_page' onchange=\"change_graphe_inserer_saut_page2()\">
			<option value=''".$selected_nb_graphe_inserer_saut_page[0].">---</option>
			<option value='1'".$selected_nb_graphe_inserer_saut_page[1].">1</option>
			<option value='2'".$selected_nb_graphe_inserer_saut_page[2].">2</option>
			<option value='3'".$selected_nb_graphe_inserer_saut_page[3].">3</option>
			<option value='4'".$selected_nb_graphe_inserer_saut_page[4].">4</option>
			<option value='5'".$selected_nb_graphe_inserer_saut_page[5].">5</option>
		</select> graphes<br />
		</p>

		<input type='hidden' name='id_classe' value='$id_classe' />
		<input type='hidden' name='num_periode' value='$num_periode' />
		<input type='hidden' name='afficher_graphes' value='y' />
		<input type='hidden' name='valider_param_graphes' value='y' />
		<p><input type='submit' value='Valider' /></p>
	</fieldset>
</form>

<script type='text/javascript'>
	".js_checkbox_change_style()."

	function change_graphe_inserer_saut_page() {
		checkbox_change('graphe_inserer_saut_page_n');
		checkbox_change('graphe_inserer_saut_page_y');
	}

	function change_graphe_inserer_saut_page2() {
		if(document.getElementById('nb_graphes_saut_page').selectedIndex>0) {
			document.getElementById('graphe_inserer_saut_page_y').checked=true;
			change_graphe_inserer_saut_page();
		}
	}

	change_graphe_inserer_saut_page();
</script>";

	require_once("../lib/footer.inc.php");
	die();
}

// Sécurité
$acces_draw_graphe=acces("/visualisation/draw_graphe.php", $_SESSION['statut']);

$graph_title="Graphe";
$compteur=0;
$eleve2 = "moyclasse";

if($num_periode!="toutes") {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>
	 | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Choisir une autre période</a>
	 | <a href='affiche_eleve.php?id_classe=$id_classe&amp;choix_periode=periode&amp;num_periode_choisie=$num_periode'>Graphes individuels</a>
</p>
<h1 class='noprint'>Graphes de la classe de ".get_nom_classe($id_classe)."<br />
en période $num_periode</h1>
<div align='center'>";

	$sql="SELECT nom_periode FROM periodes WHERE id_classe='$id_classe' AND num_periode='$num_periode';";
	$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_per)==0) {
		$periode="Période ???";
	}
	else {
		$lig_per=mysqli_fetch_object($res_per);
		$periode=$lig_per->nom_periode;
	}

	$liste_matieres="";
	$matiere=array();
	$matiere_nom=array();

	// On calcule les moyennes:
	// Doivent être initialisées, les variables:
	// - $id_classe : la classe concernée
	// - $periode_num
	$periode_num=$num_periode;

	$affiche_graph="n";
	$affiche_rang="y";
	$coefficients_a_1="non";
	include('../lib/calcul_moy_gen.inc.php');

	for($loop_ele=0;$loop_ele<count($current_eleve_login);$loop_ele++) {
		$indice_eleve1=$loop_ele;
		$eleve1=$current_eleve_login[$loop_ele];

		$seriemin="";
		$seriemax="";
		$seriemoy="";
		$mgen[1]="";
		$mgen[2]="";

		$nb_series=2;
		$serie=array();
		for($i=1;$i<=$nb_series;$i++) {$serie[$i]="";}
		$liste_matieres="";

		$mgen[1]=$moy_gen_eleve[$indice_eleve1];
		if(preg_match("/^[0-9.,]*$/", $mgen[1])) {
			$mgen[1]=round(preg_replace('/,/', '.', $mgen[1]),1);
		}

		$mgen[2]=$moy_generale_classe;
		if(preg_match("/^[0-9.,]*$/", $mgen[2])) {
			$mgen[2]=round(preg_replace('/,/', '.', $mgen[2]),1);
		}

		// On remplit $liste_matieres, $serie[1], les tableaux d'appréciations et on génère les infobulles
		$cpt=0;
		for($loop=0;$loop<count($current_group);$loop++) {
			if(isset($current_eleve_note[$loop][$indice_eleve1])) {
				// L'élève suit l'enseignement

				if($liste_matieres!="") {
					$liste_matieres.="|";
					$serie[1].="|";
					$serie[2].="|";
					$seriemin.="|";
					$seriemax.="|";
				}

				// Groupe:
				$id_groupe=$current_group[$loop]["id"];

				// Matières
				$matiere[$cpt]=$current_group[$loop]["matiere"]["matiere"];
				$matiere_nom[$cpt]=$current_group[$loop]["matiere"]["nom_complet"];
				$liste_matieres.=$matiere[$cpt];

				$liste_profs_du_groupe="";
				$cpt_prof=0;
				foreach($current_group[$loop]["profs"]["list"] as $current_grp_prof) {
					if($cpt_prof>0) {$liste_profs_du_groupe.="|";}
					$liste_profs_du_groupe.=$current_grp_prof;
					$cpt_prof++;
				}

				// Elève 1:
				if($current_eleve_statut[$loop][$indice_eleve1]!="") {
					// Mettre le statut pose des problèmes pour le tracé de la courbe... abs, disp,... passent pour des zéros
					//$serie[1].=$current_eleve_statut[$loop][$indice_eleve1];
					$serie[1].="-";
				}
				else {
					$serie[1].=$current_eleve_note[$loop][$indice_eleve1];
				}

				// Elève 2:
				$serie[2].=$current_classe_matiere_moyenne[$loop];

				// Série min et série max pour les bandes min/max:
				// Avec min($current_eleve_note[$loop]) on n'a que les élève de la classe pas ceux de tout l'enseignement si à cheval sur plusieurs classes
				//$seriemin.=min($current_eleve_note[$loop]);
				$seriemin.=$moy_min_classe_grp[$loop];
				//$seriemax.=max($current_eleve_note[$loop]);
				$seriemax.=$moy_max_classe_grp[$loop];

				//$tab_nom_matiere[]=$current_group[$loop]["matiere"]["matiere"];
				$tab_nom_matiere[]=$matiere[$cpt];

				$cpt++;
			}
			else {
				// L'élève n'a pas cette matière.
				echo "<!-- $eleve1 n'a pas la matière ".$current_group[$loop]["matiere"]["matiere"]." -->\n";
			}
		}



		// Ajouter un test dans le cas où un prof de langue n'a accès qu'à ses élèves et pas à toute la classe
		// Test à mettre plus haut avant les extractions

		// Afficher le graphe de l'élève (avec ou sans saut de page (option à mettre))
		if(!$acces_draw_graphe) {
			// Pour éviter de désactiver le compte avec une rafale d'accès indus en admin
			echo "<p style='color:red'>Accès non autorisé.</p>";
		}
		else {
			echo "<p><img src='draw_graphe.php?";
			//echo "&amp;temp1=$serie[1]";
			echo "temp1=".$serie[1];
			echo "&amp;temp2=".$serie[2];
			echo "&amp;etiquette=$liste_matieres";
			echo "&amp;titre=$graph_title";
			echo "&amp;v_legend1=$eleve1";
			echo "&amp;v_legend2=$eleve2";
			echo "&amp;compteur=$compteur";
			echo "&amp;nb_series=$nb_series";
			echo "&amp;id_classe=$id_classe";
			if($affiche_mgen=='oui') {
				echo "&amp;mgen1=".$mgen[1];
				echo "&amp;mgen2=".$mgen[2];
			}
			//echo "&amp;periode=$periode";
			echo "&amp;periode=".rawurlencode($periode);
			echo "&amp;largeur_graphe=$largeur_graphe";
			echo "&amp;hauteur_graphe=$hauteur_graphe";
			echo "&amp;taille_police=$taille_police";
			echo "&amp;epaisseur_traits=$epaisseur_traits";
			echo "&amp;epaisseur_croissante_traits_periodes=$epaisseur_croissante_traits_periodes";
			if($affiche_minmax=="oui") {
				echo "&amp;seriemin=$seriemin";
				echo "&amp;seriemax=$seriemax";
			}
			echo "&amp;tronquer_nom_court=$tronquer_nom_court";
			//echo "'>";
			//echo "&amp;temoin_imageps=$temoin_imageps";
			echo "&amp;temoin_image_escalier=$temoin_image_escalier";
			if($affiche_moy_classe!='oui') {
				echo "&amp;avec_moy_classe=n";
			}
			echo "' style='border: 1px solid black;' height='$hauteur_graphe' width='$largeur_graphe' alt=\"Graphe $eleve1\" ";
			//echo "usemap='#imagemap' ";
			echo "/>\n";
			echo "</p>\n";
		}

		echo "<br />\n";

		if(($graphe_inserer_saut_page=="y")&&(preg_match("/^[0-9]{1,}$/", $nb_graphes_saut_page))&&($nb_graphes_saut_page>0)) {
			if(($loop_ele+1)%$nb_graphes_saut_page==0) {
				//echo "<hr />";
				echo "<p class='saut'></p>";
			}
		}

	}

	echo "</div>";

	require_once("../lib/footer.inc.php");
	die();
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>
	 | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Choisir une autre période</a>
	 | <a href='affiche_eleve.php?id_classe=$id_classe&amp;choix_periode=toutes_periodes'>Graphes individuels</a>
</p>
<h1 class='noprint'>Graphes de la classe de ".get_nom_classe($id_classe)."<br />
Évolution sur l'année</h1>
<div align='center'>";

	// On va afficher toutes les périodes

	// Récupération de la liste des matières dans l'ordre souhaité:
	if ($affiche_categories) {
		$sql="SELECT DISTINCT jgc.id_groupe, m.* FROM matieres m,
													j_groupes_classes jgc,
													j_groupes_matieres jgm,
													j_matieres_categories_classes jmcc,
													matieres_categories mc
												WHERE (mc.id=jmcc.categorie_id AND 
													jgc.id_classe=jmcc.classe_id AND 
													m.matiere=jgm.id_matiere AND 
													jgm.id_groupe=jgc.id_groupe AND 
													jgc.id_classe='$id_classe' AND 
													jgc.categorie_id = jmcc.categorie_id AND 
													jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n')) 
													ORDER BY jmcc.priority,mc.priority,jgc.priorite,m.nom_complet";
	}
	else{
		$sql="SELECT DISTINCT jgc.id_groupe, m.* FROM matieres m,j_groupes_classes jgc,j_groupes_matieres jgm WHERE (m.matiere=jgm.id_matiere AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe' AND 
		jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n')) ORDER BY jgc.priorite,m.matiere";
	}
	//echo "$sql<br />";
	$call_classe_infos = mysqli_query($GLOBALS["mysqli"], $sql);
	$nombre_lignes = mysqli_num_rows($call_classe_infos);


	$coefficients_a_1="non";
	$affiche_graph="n";

	$tab_per=array();
	$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode;";
	$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_periode=mysqli_num_rows($res_per);
	while($lig_per=mysqli_fetch_object($res_per)) {

		$periode_num=$lig_per->num_periode;

		// Réinitialisations:
		unset($current_eleve_login);
		unset($current_group);
		unset($moy_gen_eleve);
		unset($current_eleve_note);
		unset($current_eleve_statut);
		// Puis extraction de la période $periode_num
		include('../lib/calcul_moy_gen.inc.php');

		$tab_per[$lig_per->num_periode]['nom_periode']=$lig_per->nom_periode;
		$tab_per[$lig_per->num_periode]['current_eleve_login']=$current_eleve_login;
		$tab_per[$lig_per->num_periode]['current_eleve_note']=$current_eleve_note;
		$tab_per[$lig_per->num_periode]['current_eleve_statut']=$current_eleve_statut;
		$tab_per[$lig_per->num_periode]['current_group']=$current_group;
		$tab_per[$lig_per->num_periode]['moy_gen_eleve']=$moy_gen_eleve;
	}

	// Initialisation des séries:
	$nb_series=$nb_periode;
	for($i=1;$i<=$nb_series;$i++) {$serie[$i]="";}

	$cpt_ele=0;
	$sql="SELECT DISTINCT jec.login FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='$id_classe' AND jec.login=e.login ORDER BY e.nom, e.prenom, e.naissance;";
	//echo "$sql<br />";
	$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig_ele=mysqli_fetch_object($res_ele)) {
		//$liste_temp="";

		$eleve1=$lig_ele->login;

		$id_groupe=array();
		$liste_matieres="";
		$matiere=array();
		$matiere_nom=array();

		$cpt=0;
		// Boucle pour ne retenir que les matières de l'élève courant:
		for($loop=0;$loop<count($current_group);$loop++) {
			if(in_array($eleve1, $current_group[$loop]['eleves']['all']['list'])) {
				$id_groupe[$cpt]=$current_group[$loop]['id'];
				$matiere[$cpt]=$current_group[$loop]['matiere']['matiere'];
				$matiere_nom[$cpt]=$current_group[$loop]['matiere']['nom_complet'];

				if($liste_matieres=="") {
					$liste_matieres="$matiere[$cpt]";
				}
				else{
					$liste_matieres=$liste_matieres."|$matiere[$cpt]";
				}
				// DEBUG
				// echo "$liste_matieres<br />";

				$cpt++;
			}
		}

		// Boucler sur les périodes
		foreach($tab_per as $current_num_periode => $current_tab) {
			$current_eleve_login=$tab_per[$current_num_periode]['current_eleve_login'];
			$current_eleve_note=$tab_per[$current_num_periode]['current_eleve_note'];
			$current_eleve_statut=$tab_per[$current_num_periode]['current_eleve_statut'];
			$current_group=$tab_per[$current_num_periode]['current_group'];
			$moy_gen_eleve=$tab_per[$current_num_periode]['moy_gen_eleve'];

			$cpt=$current_num_periode;

			// Rechercher l'indice de l'élève dans current_eleve_login

			// On recherche l'indice de l'élève courant: $eleve1
			$indice_eleve1=-1;
			for($loop=0;$loop<count($current_eleve_login);$loop++) {
				//if($current_eleve_login[$loop]==$eleve1) {
				if(mb_strtolower($current_eleve_login[$loop])==mb_strtolower($eleve1)) {
					$indice_eleve1=$loop;
					break;
				}
			}

			if($indice_eleve1==-1) {
				// L'élève n'est pas dans la classe sur la période?
				for($loop=0;$loop<count($matiere);$loop++) {
					if($serie[$cpt]!="") {$serie[$cpt].="|";}
					$serie[$cpt].="-";
				}

				$mgen[$cpt]="-";
			}
			else {
				// Si l'élève est inscrit dans la classe sur la période courante, on poursuit

				// Moyenne générale de l'élève $eleve1 sur la période $cpt
				$mgen[$cpt]=$moy_gen_eleve[$indice_eleve1];

				// DEBUG
				//echo "\$mgen[$cpt]=$mgen[$cpt]<br />";

				// Boucle sur les groupes:
				for($j=0;$j<count($id_groupe);$j++) {
					if($serie[$cpt]!="") {$serie[$cpt].="|";} // Cette ligne impose que si un élève n'a pas la première matière de la liste sur une période, on mette quand même quelque chose (tiret,... mais pas vide sans quoi on a un décalage dans le nombre de champs entre $liste_matieres et $serie[$cpt])

					// Recherche de l'indice du groupe retourné en $current_group par calcul_moy_gen.inc.php
					$indice_groupe=-1;
					for($loop=0;$loop<count($current_group);$loop++) {
						if($current_group[$loop]['id']==$id_groupe[$j]) {
							$indice_groupe=$loop;
							// DEBUG
							//echo "\$current_group[$loop]['name']=".$current_group[$loop]['name']."<br />";
							break;
						}
					}

					// DEBUG
					//echo "\$indice_groupe=$indice_groupe<br />";

					if($indice_groupe==-1) {
						$serie[$cpt].="-";
					}
					else {
						if(isset($current_eleve_note[$indice_groupe][$indice_eleve1])) {
							// L'élève suit l'enseignement sur cette période
							if($current_eleve_statut[$indice_groupe][$indice_eleve1]!="") {
								$serie[$cpt].=$current_eleve_statut[$indice_groupe][$indice_eleve1];
								//$serie[$cpt].="-";
							}
							else {
								$serie[$cpt].=$current_eleve_note[$indice_groupe][$indice_eleve1];
							}
						}
						else{
							// L'élève n'a pas cette matière sur la période...
							// Pas sûr qu'on puisse arriver là: si, cf ci-dessous
							echo "<!-- $eleve1 n'a pas la matière ".$current_group[$indice_groupe]["matiere"]["matiere"]." sur la période ".$cpt." -->\n";
							// mais en mode 'toutes les périodes', il faut afficher un champ (cas de l'Histoire des arts au T3 seulement)
							$serie[$cpt].="-";
						}
					}
				}

				if((isset($mgen[$cpt]))&&(preg_match("/^[0-9.,]*$/", $mgen[$cpt]))) {
					$mgen[$cpt]=round(preg_replace('/,/', '.', $mgen[$cpt]),1);
				}

			}
		}

		$liste_temp="";
		for($loop=1;$loop<=count($serie);$loop++) {
			if($liste_temp!="") {$liste_temp.="&amp;";}
			$liste_temp.="temp$loop=".$serie[$loop];
			if($affiche_mgen=='oui') {
				$liste_temp.="&amp;mgen$loop=".$mgen[$loop];
			}
		}

		echo "<p>".get_nom_prenom_eleve($eleve1, "avec_classe")."<br />";
		echo "<img src='draw_graphe.php?";
		// $liste_temp contient les séries et les moyennes générales.
		echo "$liste_temp";
		echo "&amp;etiquette=$liste_matieres";
		echo "&amp;titre=$graph_title";
		echo "&amp;v_legend1=$eleve1";
		//echo "&amp;v_legend2=Toutes_les_périodes";
		echo "&amp;v_legend2=".rawurlencode("Toutes_les_périodes");
		echo "&amp;compteur=$compteur";
		echo "&amp;nb_series=$nb_series";
		echo "&amp;id_classe=$id_classe";
		echo "&amp;largeur_graphe=$largeur_graphe";
		echo "&amp;hauteur_graphe=$hauteur_graphe";
		echo "&amp;taille_police=$taille_police";
		echo "&amp;epaisseur_traits=$epaisseur_traits";
		echo "&amp;epaisseur_croissante_traits_periodes=$epaisseur_croissante_traits_periodes";
		if($affiche_moy_annuelle=="oui") {
			echo "&amp;affiche_moy_annuelle=$affiche_moy_annuelle";
		}
		echo "&amp;tronquer_nom_court=$tronquer_nom_court";
		//echo "'>";
		//echo "&amp;temoin_imageps=$temoin_imageps";
		echo "&amp;temoin_image_escalier=$temoin_image_escalier";
		echo "' style='border: 1px solid black;' height='$hauteur_graphe' width='$largeur_graphe' alt=\"Graphe $eleve1\" ";
		//echo "usemap='#imagemap' ";
		echo "/>\n";
		echo "</p>";

		echo "<br />\n";

		// Gérer les sauts de page ou non...
		if(($graphe_inserer_saut_page=="y")&&(preg_match("/^[0-9]{1,}$/", $nb_graphes_saut_page))&&($nb_graphes_saut_page>0)) {
			if(($cpt_ele+1)%$nb_graphes_saut_page==0) {
				//echo "<hr />";
				echo "<p class='saut'></p>";
			}
		}

		$cpt_ele++;

	}

	echo "</div>";

	require_once("../lib/footer.inc.php");
	die();

}



require_once("../lib/footer.inc.php");
?>

