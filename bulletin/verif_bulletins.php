<?php
/*
* $Id$
*
* Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
};

include "../lib/periodes.inc.php";
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


//**************** EN-TETE *****************
$titre_page = "Vérification du remplissage des bulletins";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// On teste si un professeur peut effectuer cette operation
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiProfImprBul")!='yes') {
die("Droits insuffisants pour effectuer cette opération");
}

//debug_var();

// Selection de la classe
if (!(isset($id_classe))) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";
	echo "<b>Choisissez la classe :</b></p>\n<br />\n";
	//<table><tr><td>\n";
	if ($_SESSION["statut"] == "scolarite") {
		//$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	}
	else {
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)");
	}

	$lignes = mysql_num_rows($appel_donnees);


	if($lignes==0){
		echo "<p>Aucune classe ne vous est attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
	}
	else{
		unset($lien_classe);
		unset($txt_classe);
		$i = 0;
		//while ($i < $nombreligne){
		while ($i < $lignes){
			$lien_classe[]="verif_bulletins.php?id_classe=".mysql_result($appel_donnees, $i, "id");
			$txt_classe[]=ucfirst(mysql_result($appel_donnees, $i, "classe"));
			$i++;
		}

		tab_liste($txt_classe,$lien_classe,3);
	}

	/*
	$nb_class_par_colonne=round($lignes/3);
	echo "<table width='100%'>\n";
	echo "<tr valign='top' align='center'>\n";

	$i = 0;

	echo "<td align='left'>\n";
	while($i < $lignes){
		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		$id_classe = mysql_result($appel_donnees, $i, "id");
		$display_class = mysql_result($appel_donnees, $i, "classe");
		echo "<a href='verif_bulletins.php?id_classe=$id_classe'>".ucfirst($display_class)."</a><br />\n";
		$i++;
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	*/

	//echo "</td><td></td></table>";
} else if (!(isset($per))){
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
	//echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";
	echo "<p class='bold'><a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
	//echo "<p class=bold><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";



	// ===========================================
	// Ajout lien classe précédente / classe suivante
	//if($_SESSION['statut']=='scolarite'){
		$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	/*
	}
	elseif($_SESSION['statut']=='professeur'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
	}
	elseif($_SESSION['statut']=='cpe'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
	}
	*/

	$chaine_options_classes="";
	$res_class_tmp=mysql_query($sql);
	if(mysql_num_rows($res_class_tmp)>0){
		$id_class_prec=0;
		$id_class_suiv=0;
		$temoin_tmp=0;
		while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
			if($lig_class_tmp->id==$id_classe){
				$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
				$temoin_tmp=1;
				if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
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
		}
	}
	// =================================
	if(isset($id_class_prec)){
		if($id_class_prec!=0){
			echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec";
			echo "'>Classe précédente</a>\n";
		}
	}
	if($chaine_options_classes!="") {
		echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
		echo $chaine_options_classes;
		echo "</select>\n";
	}
	if(isset($id_class_suiv)){
		if($id_class_suiv!=0){
			echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv";
			echo "'>Classe suivante</a>\n";
		}
	}
	echo "</form>\n";
	//fin ajout lien classe précédente / classe suivante
	// ===========================================


	// On teste si les élèves ont bien un CPE responsable

	$test1 = mysql_query("SELECT distinct(login) login from j_eleves_classes WHERE id_classe='" . $id_classe . "'");
	$nb_eleves = mysql_num_rows($test1);
	$j = 0;
	$flag = true;
	while ($j < $nb_eleves) {
		$login_e = mysql_result($test1, $j, "login");
		$test = mysql_result(mysql_query("SELECT count(*) FROM j_eleves_cpe WHERE e_login='" . $login_e . "'"), 0);
		if ($test == "0") {
			$flag = false;
			break;
		}
		$j++;
	}

	if (!$flag) {
		echo "<p>ATTENTION : certains élèves de cette classe n'ont pas de CPE responsable attribué. Cela génèrera un message d'erreur sur la page d'édition des bulletins. Il faut corriger ce problème avant impression (contactez l'administrateur).</p>\n";
	}

	$sql_classe="SELECT * FROM `classes`WHERE id=$id_classe";
	$call_classe=mysql_query($sql_classe);
	$nom_classe=mysql_result($call_classe,0,"classe");

	//echo "<p><b> Classe : $nom_classe - Choisissez la période : </b></p><br />\n";
	echo "<p><b> Classe : $nom_classe - Choisissez la période et les points à vérifier: </b></p><br />\n";
	include "../lib/periodes.inc.php";


	echo "<table class='boireaus'>\n";
	echo "<tr>\n";
	echo "<th rowspan='2'>Vérifier</th>\n";
	$i=1;
	while ($i < $nb_periode) {
		echo "<th>".ucfirst($nom_periode[$i])."</th>\n";
		$i++;
	}
	echo "</tr>\n";

	echo "<tr>\n";
	//echo "<th>Vérifier</th>\n";
	$i=1;
	while ($i < $nb_periode) {
		echo "<th>";
		echo "<span style='font-size:x-small;'>";
		if ($ver_periode[$i] == "P")  {
			echo " (période partiellement close, seule la saisie des avis du conseil de classe est possible)\n";
		} else if ($ver_periode[$i] == "O")  {
			echo " (période entièrement close, plus aucune saisie/modification n'est possible)\n";
		} else {
			echo " (période ouverte, les saisies/modifications sont possibles)\n";
		}
		echo "</span>\n";
		echo "</th>\n";
		$i++;
	}
	echo "</tr>\n";

	/*
	$i="1";
	while ($i < $nb_periode) {
		echo "<p><a href='verif_bulletins.php?id_classe=$id_classe&amp;per=$i'>".ucfirst($nom_periode[$i])."</a>\n";
		if ($ver_periode[$i] == "P")  {
			echo " (période partiellement close, seule la saisie des avis du conseil de classe est possible)\n";
		} else if ($ver_periode[$i] == "O")  {
			echo " (période entièrement close, plus aucune saisie/modification n'est possible)\n";
		} else {
			echo " (période ouverte, les saisies/modifications sont possibles)\n";
		}
		//echo "<p>\n";
		echo "</p>\n";
		$i++;
	}
	*/

	$i="1";
	echo "<tr class='lig1'>\n";
	echo "<th>Notes et appréciations</th>\n";
	while ($i < $nb_periode) {

		echo "<td><a href='verif_bulletins.php?id_classe=$id_classe&amp;per=$i&amp;mode=note_app'>";
		echo "<img src='../images/icons/chercher.png' width='32' height='32' alt=\"".ucfirst($nom_periode[$i])." \" title=\"".ucfirst($nom_periode[$i])." \" /></a>";
		/*
		echo "<br />\n";
		echo "<span style='font-size:x-small;'>";
		if ($ver_periode[$i] == "P")  {
			echo " (période partiellement close, seule la saisie des avis du conseil de classe est possible)\n";
		} else if ($ver_periode[$i] == "O")  {
			echo " (période entièrement close, plus aucune saisie/modification n'est possible)\n";
		} else {
			echo " (période ouverte, les saisies/modifications sont possibles)\n";
		}
		echo "</span>\n";
		*/
		echo "</td>\n";
		$i++;
	}
	echo "</tr>\n";

	$i="1";
	echo "<tr class='lig-1'>\n";
	echo "<th>Absences</th>\n";
	while ($i < $nb_periode) {

		echo "<td><a href='verif_bulletins.php?id_classe=$id_classe&amp;per=$i&amp;mode=abs'>";
		echo "<img src='../images/icons/chercher.png' width='32' height='32' alt=\"".ucfirst($nom_periode[$i])." \" title=\"".ucfirst($nom_periode[$i])." \" /></a>";
		/*
		echo "<br />\n";
		echo "<span style='font-size:x-small;'>";
		if ($ver_periode[$i] == "P")  {
			echo " (période partiellement close, seule la saisie des avis du conseil de classe est possible)\n";
		} else if ($ver_periode[$i] == "O")  {
			echo " (période entièrement close, plus aucune saisie/modification n'est possible)\n";
		} else {
			echo " (période ouverte, les saisies/modifications sont possibles)\n";
		}
		echo "</span>\n";
		*/
		echo "</td>\n";
		$i++;
	}
	echo "</tr>\n";

	$i="1";
	echo "<tr class='lig1'>\n";
	echo "<th>Avis du conseil</th>\n";
	while ($i < $nb_periode) {

		echo "<td><a href='verif_bulletins.php?id_classe=$id_classe&amp;per=$i&amp;mode=avis'>";
		echo "<img src='../images/icons/chercher.png' width='32' height='32' alt=\"".ucfirst($nom_periode[$i])." \" title=\"".ucfirst($nom_periode[$i])." \" /></a>";
		/*
		echo "<br />\n";
		echo "<span style='font-size:x-small;'>";
		if ($ver_periode[$i] == "P")  {
			echo " (période partiellement close, seule la saisie des avis du conseil de classe est possible)\n";
		} else if ($ver_periode[$i] == "O")  {
			echo " (période entièrement close, plus aucune saisie/modification n'est possible)\n";
		} else {
			echo " (période ouverte, les saisies/modifications sont possibles)\n";
		}
		echo "</span>\n";
		*/
		echo "</td>\n";
		$i++;
	}
	echo "</tr>\n";

	$i="1";
	echo "<tr class='lig-1'>\n";
	echo "<th>Tout</th>\n";
	while ($i < $nb_periode) {

		echo "<td><a href='verif_bulletins.php?id_classe=$id_classe&amp;per=$i'>";
		echo "<img src='../images/icons/chercher.png' width='32' height='32' alt=\"".ucfirst($nom_periode[$i])." \" title=\"".ucfirst($nom_periode[$i])." \" /></a>";
		/*
		echo "<br />\n";
		echo "<span style='font-size:x-small;'>";
		if ($ver_periode[$i] == "P")  {
			echo " (période partiellement close, seule la saisie des avis du conseil de classe est possible)\n";
		} else if ($ver_periode[$i] == "O")  {
			echo " (période entièrement close, plus aucune saisie/modification n'est possible)\n";
		} else {
			echo " (période ouverte, les saisies/modifications sont possibles)\n";
		}
		echo "</span>\n";
		*/
		echo "</td>\n";
		$i++;
	}
	echo "</tr>\n";



	echo "</table>\n";

} else {

	$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : "");
	if(($mode!='note_app')&&($mode!='abs')&&($mode!='avis')){
		$mode="tout";
	}

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
	//echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";
	echo "<p class=bold><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";


	// ===========================================
	// Ajout lien classe précédente / classe suivante
	//if($_SESSION['statut']=='scolarite'){
		$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	/*
	}
	elseif($_SESSION['statut']=='professeur'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
	}
	elseif($_SESSION['statut']=='cpe'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
	}
	*/
	$chaine_options_classes="";
	$res_class_tmp=mysql_query($sql);
	if(mysql_num_rows($res_class_tmp)>0){
		$id_class_prec=0;
		$id_class_suiv=0;
		$temoin_tmp=0;
		while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
			if($lig_class_tmp->id==$id_classe){
				$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
				$temoin_tmp=1;
				if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
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
		}
	}
	// =================================
	if(isset($id_class_prec)){
		if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec&amp;per=$per&amp;mode=$mode'>Classe précédente</a>\n";}
	}
	if($chaine_options_classes!="") {
		echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
		echo $chaine_options_classes;
		echo "</select>\n";
		echo "<input type='hidden' name='per' value='$per' />\n";
		echo "<input type='hidden' name='mode' value='$mode' />\n";
	}
	if(isset($id_class_suiv)){
		if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv&amp;per=$per&amp;mode=$mode'>Classe suivante</a>\n";}
	}
	echo "</form>\n";
	//fin ajout lien classe précédente / classe suivante
	// ===========================================



	$bulletin_rempli = 'yes';
	$call_classe = mysql_query("SELECT * FROM classes WHERE id = '$id_classe'");
	$classe = mysql_result($call_classe, "0", "classe");
	echo "<p>Classe : $classe - $nom_periode[$per] - Année scolaire : ".getSettingValue("gepiYear")."</p>";

	//
	// Vérification de paramètres généraux
	//
	$current_classe_nom_complet = mysql_result($call_classe, 0, "nom_complet");
	if ($current_classe_nom_complet == '') {
		$bulletin_rempli = 'no';
		echo "<p>Le nom long de la classe n'est pas défini !</p>\n";
	}
	$current_classe_suivi_par = mysql_result($call_classe, 0, "suivi_par");
	if ($current_classe_suivi_par == '') {
		$bulletin_rempli = 'no';
		echo "<p>La personne de l'administration chargée de la classe n'est pas définie !</p>\n";
	}
	$current_classe_formule = mysql_result($call_classe, 0, "formule");
	if ($current_classe_formule == '') {
		$bulletin_rempli = 'no';
		echo "<p>La formule à la fin de chaque bulletin n'est pas définie !</p>\n";
	}
	$appel_donnees_eleves = mysql_query("SELECT e.login, e.nom, e.prenom FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login and j.periode='$per') ORDER BY login");
	$nb_eleves = mysql_num_rows($appel_donnees_eleves);
	$j = 0;
	//
	//Début de la boucle élève
	//

	switch($mode){
		case 'note_app':
			echo "<p class='bold'>Vérification du remplissage des moyennes et appréciations:</p>\n";
			break;
		case 'avis':
			echo "<p class='bold'>Vérification du remplissage des avis du conseil de classe:</p>\n";
			break;
		case 'abs':
			echo "<p class='bold'>Vérification du remplissage des absences:</p>\n";
			break;
		case 'tout':
			echo "<p class='bold'>Vérification du remplissage des moyennes, appréciations, absences et avis du conseil de classe:</p>\n";
			break;
	}

	// Affichage sur 3 colonnes
	$nb_eleve_par_colonne=round($nb_eleves/2);

	echo "<table width='100%'>\n";
	echo "<tr valign='top' align='center'>\n";

	$cpt_i = 0;

	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";


	$temoin_note_app=0;
	$temoin_avis=0;
	$temoin_aid=0;
	$temoin_abs=0;
	while($j < $nb_eleves) {

		//affichage 2 colonnes
		if(($cpt_i>0)&&(round($cpt_i/$nb_eleve_par_colonne)==$cpt_i/$nb_eleve_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}


		$id_eleve[$j] = mysql_result($appel_donnees_eleves, $j, "login");
		$eleve_nom[$j] = mysql_result($appel_donnees_eleves, $j, "nom");
		$eleve_prenom[$j] = mysql_result($appel_donnees_eleves, $j, "prenom");


		$affiche_nom = 1;
		if(($mode=="note_app")||($mode=="tout")){
			$groupeinfo = mysql_query("SELECT DISTINCT id_groupe FROM j_eleves_groupes WHERE login='" . $id_eleve[$j] ."'");
			$lignes_groupes = mysql_num_rows($groupeinfo);
			//
			//Vérification des appréciations
			//

			$i= 0;
			//
			//Début de la boucle matière
			//

			// Variable remontée hors du test sur $mode
			//$affiche_nom = 1;
			$affiche_mess_app = 1;
			$affiche_mess_note = 1;
			while($i < $lignes_groupes){
				$group_id = mysql_result($groupeinfo, $i, "id_groupe");
				$current_group = get_group($group_id);

				if (in_array($id_eleve[$j], $current_group["eleves"][$per]["list"])) { // Si l'élève suit cet enseignement pour la période considérée
					//
					//Vérification des appréciations :
					//
					$test_app = mysql_query("SELECT * FROM matieres_appreciations WHERE (login = '$id_eleve[$j]' and id_groupe = '" . $current_group["id"] . "' and periode = '$per')");
					$app = @mysql_result($test_app, 0, 'appreciation');
					if ($app == '') {
						$bulletin_rempli = 'no';
						if ($affiche_nom != 0) {
							//echo "<br /><br /><br />\n";
							echo "<p style='border:1px solid black;'><span class='bold'>$eleve_prenom[$j] $eleve_nom[$j]";
							//echo "<br />\n";
							echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='bulletin simple dans une nouvelle page' title='bulletin simple dans une nouvelle page' /></a>)</span> :";
						}
						if ($affiche_mess_app != 0) {
							echo "<br /><br />\n";
							echo "<b>Appréciations non remplies</b> pour les matières suivantes : \n";
						}
						$affiche_nom = 0;
						$affiche_mess_app = 0;
						//============================================
						// MODIF: boireaus
						// Pour les matières comme Histoire & Géo,...
						//echo "<br />--> " . $current_group["description"] . " (" . $current_group["classlist_string"] . ")  --  (";
						echo "<br />--> " . htmlentities($current_group["description"]) . " (" . $current_group["classlist_string"] . ")  --  (";
						//============================================
						$m=0;
						$virgule = 1;
						foreach ($current_group["profs"]["list"] as $login_prof) {
							$email = retourne_email($login_prof);
							$nom_prof = $current_group["profs"]["users"][$login_prof]["nom"];
							$prenom_prof = $current_group["profs"]["users"][$login_prof]["prenom"];
							if($email!=""){
								echo "<a href='mailto:$email'>".ucfirst(strtolower($prenom_prof))." ".strtoupper($nom_prof)."</a>";
							}
							else{
								echo ucfirst(strtolower($prenom_prof))." ".strtoupper($nom_prof);
							}
							$m++;
							if ($m == count($current_group["profs"]["list"])) {$virgule = 0;}
							if ($virgule == 1) {echo ", ";}
						}
						echo ")\n";

						$temoin_note_app++;
					}
				}
				$i++;
			}
			//
			//Vérification des moyennes
			//
			$i= 0;
			//
			//Début de la boucle matière
			//
			while($i < $lignes_groupes){
				$group_id = mysql_result($groupeinfo, $i, "id_groupe");
				$current_group = get_group($group_id);

				if (in_array($id_eleve[$j], $current_group["eleves"][$per]["list"])) { // Si l'élève suit cet enseignement pour la période considérée
					//
					//Vérification des moyennes :
					//
					$test_notes = mysql_query("SELECT * FROM matieres_notes WHERE (login = '$id_eleve[$j]' and id_groupe = '" . $current_group["id"] . "' and periode = '$per')");
					$note = @mysql_result($test_notes, 0, 'note');
					if ($note == '') {
						$bulletin_rempli = 'no';
						if ($affiche_nom != 0) {
							//echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j] ";
							echo "<p style='border:1px solid black;'><span class='bold'>$eleve_prenom[$j] $eleve_nom[$j]";
							//echo "<br />\n";
							//echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page</a>)</span> :";
							echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='bulletin simple dans une nouvelle page' title='bulletin simple dans une nouvelle page' /></a>)</span> :";
						}
						if ($affiche_mess_note != 0) {echo "<br /><br /><b>Moyennes non remplies</b> pour les matières suivantes : ";}
						$affiche_nom = 0;
						$affiche_mess_note = 0;
						//============================================
						// MODIF: boireaus
						// Pour les matières comme Histoire & Géo,...
						//echo "<br />--> " . $current_group["description"] . " (" . $current_group["classlist_string"] . ")  --  (";
						echo "<br />--> ".htmlentities($current_group["description"])." (" . $current_group["classlist_string"] . ")  --   (";
						//============================================
						$m=0;
						$virgule = 1;
						foreach ($current_group["profs"]["list"] as $login_prof) {
							$email = retourne_email($login_prof);
							$nom_prof = $current_group["profs"]["users"][$login_prof]["nom"];
							$prenom_prof = $current_group["profs"]["users"][$login_prof]["prenom"];
							//echo "<a href='mailto:$email'>$prenom_prof $nom_prof</a>";
							if($email!=""){
								echo "<a href='mailto:$email'>".ucfirst(strtolower($prenom_prof))." ".strtoupper($nom_prof)."</a>";
							}
							else{
								echo ucfirst(strtolower($prenom_prof))." ".strtoupper($nom_prof);
							}
							$m++;
							if ($m == count($current_group["profs"]["list"])) {$virgule = 0;}
							if ($virgule == 1) {echo ", ";}
						}
						echo ")\n";

						$temoin_note_app++;
					}
				}
				$i++;
			//Fin de la boucle matière
			}
		}


		if(($mode=="avis")||($mode=="tout")){
			//
			//Vérification des avis des conseils de classe
			//
			$query_conseil = mysql_query("SELECT * FROM avis_conseil_classe WHERE (login = '$id_eleve[$j]' and periode = '$per')");
			$avis = @mysql_result($query_conseil, 0, 'avis');
			if ($avis == '') {
				$bulletin_rempli = 'no';
				if ($affiche_nom != 0) {
					//echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j]<br />\n";
					echo "<p style='border:1px solid black;'><span class='bold'>$eleve_prenom[$j] $eleve_nom[$j]";
					//echo "<br />\n";
					//echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page</a>)</span> :";
					echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='bulletin simple dans une nouvelle page' title='bulletin simple dans une nouvelle page' /></a>)</span> :";
				}
				echo "<br /><br />\n";
				echo "<b>Avis du conseil de classe</b> non rempli !";
				$call_prof = mysql_query("SELECT u.login, u.nom, u.prenom FROM utilisateurs u, j_eleves_professeurs j WHERE (j.login = '$id_eleve[$j]' and j.id_classe='$id_classe' and u.login=j.professeur)");
				$nb_result = mysql_num_rows($call_prof);
				if ($nb_result != 0) {
					$login_prof = mysql_result($call_prof, 0, 'login');
					$email = retourne_email($login_prof);
					$nom_prof = mysql_result($call_prof, 0, 'nom');
					$prenom_prof = mysql_result($call_prof, 0, 'prenom');
					//echo " (<a href='mailto:$email'>$prenom_prof $nom_prof</a>)";
					if($email!=""){
						echo "(<a href='mailto:$email'>".ucfirst(strtolower($prenom_prof))." ".strtoupper($nom_prof)."</a>)";
					}
					else{
						echo "(".ucfirst(strtolower($prenom_prof))." ".strtoupper($nom_prof).")";
					}

				} else {
					echo " (pas de ".getSettingValue("gepi_prof_suivi").")";
				}

				$affiche_nom = 0;

				$temoin_avis++;

			}
		}


		if(($mode=="note_app")||($mode=="tout")){
			//
			//Vérification des aid
			//
			$call_data = mysql_query("SELECT * FROM aid_config WHERE display_bulletin!='n' ORDER BY nom");
			$nb_aid = mysql_num_rows($call_data);
			$z=0;
			while ($z < $nb_aid) {
				$display_begin = @mysql_result($call_data, $z, "display_begin");
				$display_end = @mysql_result($call_data, $z, "display_end");
				if (($per >= $display_begin) and ($per <= $display_end)) {
					$indice_aid = @mysql_result($call_data, $z, "indice_aid");
					$type_note = @mysql_result($call_data, $z, "type_note");
					$call_data2 = mysql_query("SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
					$nom_aid = @mysql_result($call_data2, 0, "nom");
					$aid_query = mysql_query("SELECT id_aid FROM j_aid_eleves WHERE (login='$id_eleve[$j]' and indice_aid='$indice_aid')");
					$aid_id = @mysql_result($aid_query, 0, "id_aid");
					if ($aid_id != '') {
						$aid_app_query = mysql_query("SELECT * FROM aid_appreciations WHERE (login='$id_eleve[$j]' AND periode='$per' and id_aid='$aid_id' and indice_aid='$indice_aid')");
						$query_resp = mysql_query("SELECT u.login, u.nom, u.prenom FROM utilisateurs u, j_aid_utilisateurs j WHERE (j.id_aid = '$aid_id' and u.login = j.id_utilisateur and j.indice_aid='$indice_aid')");
						$nb_prof = mysql_num_rows($query_resp);
						//
						// Vérification des appréciations
						//
						$aid_app = @mysql_result($aid_app_query, 0, "appreciation");
						if ($aid_app == '') {
							$bulletin_rempli = 'no';
							if ($affiche_nom != 0) {
								//echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j]<br />\n";
								echo "<p style='border:1px solid black;'><span class='bold'>$eleve_prenom[$j] $eleve_nom[$j]";
								//echo "<br />\n";
								//echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page</a>)</span> :";
								echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='bulletin simple dans une nouvelle page' title='bulletin simple dans une nouvelle page' /></a>)</span> :";
							}
							echo "<br /><br />\n";
							echo "<b>Appréciation $nom_aid </b> non remplie (";
							$m=0;
							$virgule = 1;
							while ($m < $nb_prof) {
								$login_prof = @mysql_result($query_resp, $m, 'login');
								$email = retourne_email($login_prof);
								$nom_prof = @mysql_result($query_resp, $m, 'nom');
								$prenom_prof = @mysql_result($query_resp, $m, 'prenom');
								//echo "<a href='mailto:$email'>$prenom_prof $nom_prof</a>";
								if($email!=""){
									echo "<a href='mailto:$email'>".ucfirst(strtolower($prenom_prof))." ".strtoupper($nom_prof)."</a>";
								}
								else{
									echo ucfirst(strtolower($prenom_prof))." ".strtoupper($nom_prof);
								}
								$m++;
								if ($m == $nb_prof) {$virgule = 0;}
								if ($virgule == 1) {echo ", ";}
							}
							echo ")\n";
							$affiche_nom = 0;

							$temoin_aid++;
						}
						//
						// Vérification des moyennes
						//
						$periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
						$periode_max = mysql_num_rows($periode_query);
						if ($type_note == 'last') {$last_periode_aid = min($periode_max,$display_end);}
						if (($type_note=='every') or (($type_note=='last') and ($per == $last_periode_aid))) {
							$aid_note = @mysql_result($aid_app_query, 0, "note");
							$aid_statut = @mysql_result($aid_app_query, 0, "statut");


							if (($aid_note == '') or ($aid_statut == 'other')) {
								$bulletin_rempli = 'no';
								if ($affiche_nom != 0) {
									//echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j]<br />\n";
									echo "<p style='border:1px solid black;'><span class='bold'>$eleve_prenom[$j] $eleve_nom[$j]";
									//echo "<br />\n";
									//echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page)</a></span> :";
									echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='bulletin simple dans une nouvelle page' title='bulletin simple dans une nouvelle page' /></a>)</span> :";
								}
								echo "<br /><br />\n";
								echo "<b>Note $nom_aid </b>non remplie (";
								$m=0;
								$virgule = 1;
								while ($m < $nb_prof) {
									$login_prof = @mysql_result($query_resp, $m, 'login');
									$email = retourne_email($login_prof);
									$nom_prof = @mysql_result($query_resp, $m, 'nom');
									$prenom_prof = @mysql_result($query_resp, $m, 'prenom');
									//echo "<a href='mailto:$email'>$prenom_prof $nom_prof</a>";
									if($email!=""){
										echo "<a href='mailto:$email'>".ucfirst(strtolower($prenom_prof))." ".strtoupper($nom_prof)."</a>";
									}
									else{
										echo ucfirst(strtolower($prenom_prof))." ".strtoupper($nom_prof);
									}
									$m++;
									if ($m == $nb_prof) {$virgule = 0;}
									if ($virgule == 1) {echo ", ";}
								}
								echo ")\n";
								$affiche_nom = 0;

								$temoin_aid++;
							}
						}
					}
				}
				$z++;
			}
		}

		if(($mode=="abs")||($mode=="tout")){
			//
			//Vérification des absences
			//
			$abs_query = mysql_query("SELECT * FROM absences WHERE (login='$id_eleve[$j]' AND periode='$per')");
			$abs1 = @mysql_result($abs_query, 0, "nb_absences");
			$abs2 = @mysql_result($abs_query, 0, "non_justifie");
			$abs3 = @mysql_result($abs_query, 0, "nb_retards");
			if (($abs1 == '') or ($abs2 == '') or ($abs3 == '')) {
				$bulletin_rempli = 'no';
				if ($affiche_nom != 0) {
					//echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j]<br />\n";
					echo "<p style='border:1px solid black;'><span class='bold'>$eleve_prenom[$j] $eleve_nom[$j]";
					//echo "<br />\n";
					//echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page)</a></span> :";
					echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='bulletin simple dans une nouvelle page' title='bulletin simple dans une nouvelle page' /></a>)</span> :";
				}
				echo "<br /><br />\n";
				echo "<b>Rubrique \"Absences\" </b> non remplie. (";
				$query_resp = mysql_query("SELECT u.login, u.nom, u.prenom FROM utilisateurs u, j_eleves_cpe j WHERE (j.e_login = '$id_eleve[$j]' AND u.login = j.cpe_login)");
				$nb_prof = mysql_num_rows($query_resp);
				$m=0;
				$virgule = 1;
				while ($m < $nb_prof) {
					$login_prof = @mysql_result($query_resp, $m, 'login');
					$email = retourne_email($login_prof);
					$nom_prof = @mysql_result($query_resp, $m, 'nom');
					$prenom_prof = @mysql_result($query_resp, $m, 'prenom');
					//echo "<a href='mailto:$email'>$prenom_prof $nom_prof</a>";
					if($email!=""){
						echo "<a href='mailto:$email'>".ucfirst(strtolower($prenom_prof))." ".strtoupper($nom_prof)."</a>";
					}
					else{
						echo ucfirst(strtolower($prenom_prof))." ".strtoupper($nom_prof);
					}
					$m++;
					if ($m == $nb_prof) {$virgule = 0;}
					if ($virgule == 1) {echo ", ";}
				}
				echo ")\n";
				$affiche_nom = 0;

				$temoin_abs++;
			}
		}

		$j++;
		//Fin de la boucle élève

		$cpt_i++;

		flush();

	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	//if ($bulletin_rempli == 'yes') {
	if (($bulletin_rempli == 'yes')&&($mode=='tout')) {
		echo "<p class='bold'>Toutes les rubriques des bulletins de cette classe ont été renseignées, vous pouvez procéder à l'impression finale.</p>\n";
		echo "<ul><li><p class='bold'>Accéder directement au verrouillage de la période en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=rien'>cliquant ici.</a></p></li>\n";
		echo "<li><p class='bold'>Accéder directement au verrouillage de la période en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=retour'>cliquant ici.</a> puis revenir à la page outil de vérification.</p></li>\n";
		echo "<li><p class='bold'>Accéder directement au verrouillage de la période en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=imprime_html'>cliquant ici.</a> puis aller à la page impression des bulletins HTML.</p></li>\n";
		echo "<li><p class='bold'>Accéder directement au verrouillage de la période en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=imprime_pdf'>cliquant ici.</a> puis aller à la page impression des bulletins PDF.</p></li></ul>\n";
	} elseif(($temoin_note_app==0)&&($temoin_aid==0)&&($mode=='note_app')) {
		echo "<p class='bold'>Toutes les moyennes et appréciations des bulletins de cette classe ont été renseignées.</p>\n";
	} elseif(($temoin_avis==0)&&($mode=='avis')) {
		echo "<p class='bold'>Tous les avis de conseil de classe des bulletins de cette classe ont été renseignés.</p>\n";
	} elseif(($temoin_abs==0)&&($mode=='abs')) {
		echo "<p class='bold'>Toutes les absences et retards des bulletins de cette classe ont été renseignés.</p>\n";
	} else{
		echo "<br /><p class='bold'>*** Fin des vérifications. ***</p>\n";
		/*
		echo "<ul><li><p class='bold'>Accéder directement au verrouillage de la période en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=rien'>cliquant ici.</a></p></li>";
		echo "<li><p class='bold'>Accéder directement au verrouillage de la période en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=retour'>cliquant ici.</a> puis revenir à la page outil de vérification.</p></li>";
		echo "<li><p class='bold'>Accéder directement au verrouillage de la période en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=imprime_html'>cliquant ici.</a> puis aller à la page impression des bulletins HTML.</p></li>";
		echo "<li><p class='bold'>Accéder directement au verrouillage de la période en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=imprime_pdf'>cliquant ici.</a> puis aller à la page impression des bulletins PDF.</p></li></ul>";
			*/
	}
}
require("../lib/footer.inc.php");
?>