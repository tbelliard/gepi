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
@set_time_limit(0);

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Classe choisie:
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
// Périodes de visualisation:
$periode1=isset($_POST['periode1']) ? $_POST['periode1'] : NULL;
$periode2=isset($_POST['periode2']) ? $_POST['periode2'] : NULL;
// Période de saisie:
$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);

$url_retour=isset($_POST['url_retour']) ? $_POST['url_retour'] : NULL;
if(preg_match("#/saisie/saisie_avis1.php#",$_SERVER['HTTP_REFERER'])) {$url_retour="../saisie/saisie_avis1.php?id_classe=$id_classe&amp;periode_num=$num_periode";}
if(preg_match("#/saisie/saisie_avis2.php#",$_SERVER['HTTP_REFERER'])) {$url_retour="../saisie/saisie_avis2.php?id_classe=$id_classe&amp;periode_num=$num_periode";}

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

//debug_var();

//if((isset($periode1))&&(isset($periode2))&&(isset($id_classe))&&(isset($num_periode))) {
if((isset($id_classe))&&(isset($num_periode))) {

	if(!isset($periode1)) {$periode1=$num_periode;}
	if(!isset($periode2)) {$periode2=$num_periode;}

	if($_SESSION['statut']=='professeur') {
		// Le prof est-il PP
		$sql="SELECT 1=1 FROM j_eleves_professeurs jep, j_eleves_classes jec WHERE jep.professeur='".$_SESSION['login']."' AND jec.login=jep.login AND jec.id_classe='$id_classe' AND jec.periode='$num_periode';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			//tentative_intrusion("2", "Tentative d'accès par un prof à la saisie de synthèse d'une classe qu'il ne suit pas.");
			header('Location: ../accueil.php&msg='.rawurlencode("Vous n'êtes pas autorisé à saisir la synthèse pour cette classe."));
			die();
		}
	}
	elseif($_SESSION['statut']=='cpe') {
		if(getSettingAOui('GepiRubConseilCpeTous')) {
			// On peut poursuivre: L'accès à toutes les classes est donné.
		}
		elseif(getSettingAOui('GepiRubConseilCpe')) {
			$sql="SELECT 1=1 FROM j_eleves_cpe jecpe, j_eleves_classes jec WHERE jecpe.cpe_login='".$_SESSION['login']."' AND jec.login=jecpe.e_login AND jec.id_classe='$id_classe' AND jec.periode='$num_periode';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)==0) {
				//tentative_intrusion("2", "Tentative d'accès par un cpe à la saisie de synthèse d'une classe qu'il ne suit pas.");
				header('Location: ../accueil.php&msg='.rawurlencode("Vous n'êtes pas autorisé à saisir la synthèse pour cette classe."));
				die();
			}
		}
	}
	elseif($_SESSION['statut']=='scolarite') {
		$sql="SELECT 1=1 FROM j_scol_classes WHERE id_classe='$id_classe';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			//tentative_intrusion("2", "Tentative d'accès par un compte scolarité à la saisie de synthèse d'une classe qu'il ne suit pas.");
			header('Location: ../accueil.php&msg='.rawurlencode("Vous n'êtes pas autorisé à saisir la synthèse pour cette classe."));
			die();
		}
	}
	elseif($_SESSION['statut']=='secours') {
		// On peut poursuivre: L'accès à toutes les classes est donné.
	}
	else {
		header('Location: ../accueil.php&msg='.rawurlencode("Accès non autorisé à la saisir de la synthèse d'une classe."));
		die();
	}

	// Tout est choisi, on va passer à l'affichage

	//$synthese="";

	//if(isset($_POST['no_anti_inject_synthese'])) {
	if (isset($NON_PROTECT["synthese"])) {
		check_token();

		// On enregistre la synthese
		$synthese=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["synthese"]));

		$synthese=suppression_sauts_de_lignes_surnumeraires($synthese);

		$sql="SELECT 1=1 FROM synthese_app_classe WHERE id_classe='$id_classe' AND periode='$num_periode';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			$sql="INSERT INTO synthese_app_classe SET id_classe='$id_classe', periode='$num_periode', synthese='$synthese';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$insert) {$msg="Erreur lors de l'enregistrement de la synthèse.";}
			else {$msg="La synthèse a été enregistrée.";}
		}
		else {
			$sql="UPDATE synthese_app_classe SET synthese='$synthese' WHERE id_classe='$id_classe' AND periode='$num_periode';";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$update) {$msg="Erreur lors de la mise à jour de la synthèse.";}
			else {$msg="La synthèse a été mise à jour.";}
		}
	}

	$sql="SELECT * FROM synthese_app_classe WHERE (id_classe='$id_classe' AND periode='$num_periode');";
	//echo "$sql<br />";
	$res_current_synthese=mysqli_query($GLOBALS["mysqli"], $sql);
	$synthese=@old_mysql_result($res_current_synthese, 0, "synthese");

	//====================================
	$titre_page="Synthèse classe";
	require_once("../lib/header.inc.php");
	//====================================
	include "../lib/periodes.inc.php";
	include "../lib/bulletin_simple.inc.php";
	include "../lib/bulletin_simple_classe.inc.php";

	echo "<p class=\"bold\">";
	if(isset($url_retour)) {
		echo "<a href=\"$url_retour\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à la saisie des avis</a>";
	}
	else {
		echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
	}
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Autre classe</a>\n";
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Autre période</a>\n";
	echo "</p>\n";

	$gepiYear = getSettingValue("gepiYear");
	
	if ($periode1 > $periode2) {
		$temp = $periode2;
		$periode2 = $periode1;
		$periode1 = $temp;
	}
	// On teste la présence d'au moins un coeff pour afficher la colonne des coef
	$test_coef = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
	//echo "\$test_coef=$test_coef<br />";
	// Apparemment, $test_coef est réaffecté plus loin dans un des include()
	$nb_coef_superieurs_a_zero=$test_coef;
	
	// On regarde si on affiche les catégories de matières
	$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
	if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}
	
	// Si le rang des élèves est demandé, on met à jour le champ rang de la table matieres_notes
	$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
	if ($affiche_rang == 'y') {
		$periode_num=$periode1;
		while ($periode_num < $periode2+1) {
			include "../lib/calcul_rang.inc.php";
			$periode_num++;
		}
	}

	$coefficients_a_1="non";
	$affiche_graph = 'n';

	unset($tab_moy_gen);
	unset($tab_moy);
	//unset($tab_moy_cat_classe);
	for($loop=$periode1;$loop<=$periode2;$loop++) {
		$periode_num=$loop;
		include "../lib/calcul_moy_gen.inc.php";
		$tab_moy_gen[$loop]=$moy_generale_classe;
		//$tab_moy_cat_classe

		$tab_moy['periodes'][$periode_num]=array();
		$tab_moy['periodes'][$periode_num]['tab_login_indice']=$tab_login_indice;         // [$login_eleve]
		$tab_moy['periodes'][$periode_num]['moy_gen_eleve']=$moy_gen_eleve;               // [$i]
		$tab_moy['periodes'][$periode_num]['moy_gen_eleve1']=$moy_gen_eleve1;             // [$i]
		//$tab_moy['periodes'][$periode_num]['moy_gen_classe1']=$moy_gen_classe1;           // [$i]
		$tab_moy['periodes'][$periode_num]['moy_generale_classe']=$moy_generale_classe;
		$tab_moy['periodes'][$periode_num]['moy_generale_classe1']=$moy_generale_classe1;
		$tab_moy['periodes'][$periode_num]['moy_max_classe']=$moy_max_classe;
		$tab_moy['periodes'][$periode_num]['moy_min_classe']=$moy_min_classe;
	
		// Il faudrait récupérer/stocker les catégories?
		$tab_moy['periodes'][$periode_num]['moy_cat_eleve']=$moy_cat_eleve;               // [$i][$cat]
		$tab_moy['periodes'][$periode_num]['moy_cat_classe']=$moy_cat_classe;             // [$i][$cat]
		$tab_moy['periodes'][$periode_num]['moy_cat_min']=$moy_cat_min;                   // [$i][$cat]
		$tab_moy['periodes'][$periode_num]['moy_cat_max']=$moy_cat_max;                   // [$i][$cat]
	
		$tab_moy['periodes'][$periode_num]['quartile1_classe_gen']=$quartile1_classe_gen;
		$tab_moy['periodes'][$periode_num]['quartile2_classe_gen']=$quartile2_classe_gen;
		$tab_moy['periodes'][$periode_num]['quartile3_classe_gen']=$quartile3_classe_gen;
		$tab_moy['periodes'][$periode_num]['quartile4_classe_gen']=$quartile4_classe_gen;
		$tab_moy['periodes'][$periode_num]['quartile5_classe_gen']=$quartile5_classe_gen;
		$tab_moy['periodes'][$periode_num]['quartile6_classe_gen']=$quartile6_classe_gen;
		$tab_moy['periodes'][$periode_num]['place_eleve_classe']=$place_eleve_classe;
	
		$tab_moy['periodes'][$periode_num]['current_eleve_login']=$current_eleve_login;   // [$i]
		//$tab_moy['periodes'][$periode_num]['current_group']=$current_group;
		if($loop==$periode1) {
			$tab_moy['current_group']=$current_group;                                     // [$j]
		}
		$tab_moy['periodes'][$periode_num]['current_eleve_note']=$current_eleve_note;     // [$j][$i]
		$tab_moy['periodes'][$periode_num]['current_eleve_statut']=$current_eleve_statut; // [$j][$i]
		//$tab_moy['periodes'][$periode_num]['current_group']=$current_group;
		$tab_moy['periodes'][$periode_num]['current_coef']=$current_coef;                 // [$j]
		$tab_moy['periodes'][$periode_num]['current_classe_matiere_moyenne']=$current_classe_matiere_moyenne; // [$j]
	
		$tab_moy['periodes'][$periode_num]['current_coef_eleve']=$current_coef_eleve;     // [$i][$j] ATTENTION
		$tab_moy['periodes'][$periode_num]['moy_min_classe_grp']=$moy_min_classe_grp;     // [$j]
		$tab_moy['periodes'][$periode_num]['moy_max_classe_grp']=$moy_max_classe_grp;     // [$j]
		if(isset($current_eleve_rang)) {
			// $current_eleve_rang n'est pas renseigné si $affiche_rang='n'
			$tab_moy['periodes'][$periode_num]['current_eleve_rang']=$current_eleve_rang; // [$j][$i]
		}
		$tab_moy['periodes'][$periode_num]['quartile1_grp']=$quartile1_grp;               // [$j]
		$tab_moy['periodes'][$periode_num]['quartile2_grp']=$quartile2_grp;               // [$j]
		$tab_moy['periodes'][$periode_num]['quartile3_grp']=$quartile3_grp;               // [$j]
		$tab_moy['periodes'][$periode_num]['quartile4_grp']=$quartile4_grp;               // [$j]
		$tab_moy['periodes'][$periode_num]['quartile5_grp']=$quartile5_grp;               // [$j]
		$tab_moy['periodes'][$periode_num]['quartile6_grp']=$quartile6_grp;               // [$j]
		$tab_moy['periodes'][$periode_num]['place_eleve_grp']=$place_eleve_grp;           // [$j][$i]
	
		$tab_moy['periodes'][$periode_num]['current_group_effectif_avec_note']=$current_group_effectif_avec_note; // [$j]
	
		/*
		// De calcul_moy_gen.inc.php, on récupère en sortie:
		//     - $moy_gen_eleve[$i]
		//     - $moy_gen_eleve1[$i] idem avec les coef forcés à 1
		//     - $moy_gen_classe[$i]
		//     - $moy_gen_classe1[$i] idem avec les coef forcés à 1
		//     - $moy_generale_classe
		//     - $moy_max_classe
		//     - $moy_min_classe
		
		// A VERIFIER, mais s'il n'y a pas de coef spécifique pour un élève, on devrait avoir
		//             $moy_gen_classe[$i] == $moy_generale_classe
		// NON: Cela correspond à un mode de calcul qui ne retient que les matières suivies par l'élève pour calculer la moyenne générale
		//      Le LATIN n'est pas compté dans cette moyenne générale si l'élève ne fait pas latin.
		//      L'Allemand n'est pas comptabilisé si l'élève ne fait pas allemand
		// FAIRE LE TOUR DES PAGES POUR VIRER TOUS CES $moy_gen_classe s'il en reste?
		
		//     - $moy_cat_classe[$i][$cat]
		//     - $moy_cat_eleve[$i][$cat]
		
		//     - $moy_cat_min[$i][$cat] égale à $moy_min_categorie[$cat]
		//     - $moy_cat_max[$i][$cat] égale à $moy_max_categorie[$cat]
		
		// Là le positionnement au niveau moyenne générale:
		//     - $quartile1_classe_gen
		//       à
		//     - $quartile6_classe_gen
		//     - $place_eleve_classe[$i]
		
		// On a récupéré en intermédiaire les
		//     - $current_eleve_login[$i]
		//     - $current_group[$j]
		//     - $current_eleve_note[$j][$i]
		//     - $current_eleve_statut[$j][$i]
		//     - $current_coef[$j] (qui peut être différent du $coef_eleve pour une matière spécifique)
		//     - $categories -> id
		//     - $current_classe_matiere_moyenne[$j] (moyenne de la classe dans la matière)
		
		// AJOUTé:
		//     - $current_coef_eleve[$i][$j]
		//     - $moy_min_classe_grp[$j]
		//     - $moy_max_classe_grp[$j]
		//     - $current_eleve_rang[$j][$i] sous réserve que $affiche_rang=='y'
		//     - $quartile1_grp[$j] à $quartile6_grp[$j]
		//     - $place_eleve_grp[$j][$i]
		//     - $current_group_effectif_avec_note[$j] pour le nombre de "vraies" moyennes pour le rang (pas disp, abs,...)
		//     - $tab_login_indice[LOGIN_ELEVE]=$i
		
		//     $categories[] = $row["id"];
		//     $tab_noms_categories[$row["id"]]=$row["nom_complet"];
		//     $tab_id_categories[$row["nom_complet"]]=$row["id"];
		
		*/

	}

	$tab_moy['categories']['id']=$categories;
	$tab_moy['categories']['nom_from_id']=$tab_noms_categories;
	$tab_moy['categories']['id_from_nom']=$tab_id_categories;
	
	
	$sql="SELECT DISTINCT e.*
	FROM eleves e, j_eleves_classes c 
	WHERE (
	c.id_classe='$id_classe' AND 
	e.login = c.login
	) ORDER BY e.nom,e.prenom;";
	$res_ele= mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ele)>0) {
		while($lig_ele=mysqli_fetch_object($res_ele)) {
			$tab_moy['eleves'][]=$lig_ele->login;
			/*
			$tab_moy['ele'][$lig_ele->login]=array();
			$tab_moy['ele'][$lig_ele->login]['nom']=$lig_ele->nom;
			$tab_moy['ele'][$lig_ele->login]['prenom']=$lig_ele->prenom;
			$tab_moy['ele'][$lig_ele->login]['sexe']=$lig_ele->sexe;
			$tab_moy['ele'][$lig_ele->login]['naissance']=$lig_ele->naissance;
			$tab_moy['ele'][$lig_ele->login]['elenoet']=$lig_ele->elenoet;
			*/
		}
	}

	$display_moy_gen=sql_query1("SELECT display_moy_gen FROM classes WHERE id='".$id_classe."'");


	bulletin_classe($tab_moy, $nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$nb_coef_superieurs_a_zero,$affiche_categories);

	// Formulaire de saisie
	echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo add_token_field();
	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
	echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";
	echo "<input type='hidden' name='periode1' value='$periode1' />\n";
	echo "<input type='hidden' name='periode2' value='$periode2' />\n";

	if(isset($url_retour)) {echo "<input type='hidden' name='url_retour' value='$url_retour' />\n";}

	//===============================================
	$tabdiv_infobulle[]=creer_div_infobulle('div_explication_cnil',"Saisies et CNIL","",$message_cnil_bons_usages,"",30,0,'y','y','n','n');
	// Paramètres concernant le délai avant affichage d'une infobulle via delais_afficher_div()
	// Hauteur de la bande testée pour la position de la souris:
	$hauteur_survol_infobulle=20;
	// Largeur de la bande testée pour la position de la souris:
	$largeur_survol_infobulle=100;
	// Délais en ms avant affichage:
	$delais_affichage_infobulle=500;
	//===============================================

	echo "<a name='synthese'></a>\n";
	echo "<p><b>Saisie de la synthèse pour le groupe classe en période $num_periode&nbsp;:</b>";
	// 20121101: Mettre une infobulle CNIL
	echo " <a href='#' onclick=\"afficher_div('div_explication_cnil','y',10,-40);return false;\" onmouseover=\"delais_afficher_div('div_explication_cnil','y',10,-40, $delais_affichage_infobulle, $largeur_survol_infobulle, $hauteur_survol_infobulle);\"><img src='../images/info.png' width='20' height='20' title='CNIL : Règles de bon usage' /></a>&nbsp;&nbsp;";

	if(getSettingAOui('GepiAccesBulletinSimpleClasseEleve')) {
		echo "&nbsp;<img src='../images/icons/trombinoscope.png' width='16' height='16' title=\"L'appréciation sur le groupe-classe est visible des élèves\" alt=\"Appréciation sur le groupe-classe visible des élèves\" />\n";
	}
	if(getSettingAOui('GepiAccesBulletinSimpleClasseResp')) {
		echo "&nbsp;<img src='../images/group16.png' width='16' height='16' title=\"L'appréciation sur le groupe-classe est visible des parents\" />\n";
	}

	echo "<br />\n";
	echo "<textarea class='wrap' name=\"no_anti_inject_synthese\" rows='5' cols='60' onchange=\"changement()\"";
	echo ">".stripslashes($synthese)."</textarea>\n";

	echo "<br /><center><input type='submit' value=Valider /></center>\n";

	require("../lib/footer.inc.php");
	die();

}

//********************************
$titre_page="Synthèse classe";
require_once("../lib/header.inc.php");
//********************************

if(!isset($id_classe)) {
	// Choix de la classe:
	if($_SESSION['statut']=='professeur') {
		// Le prof est-il PP
		$sql="SELECT DISTINCT id, classe FROM j_eleves_professeurs jep, j_eleves_classes jec WHERE jep.professeur='".$_SESSION['login']."' AND jec.login=jep.login;";
		$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_classe)==0) {
			header('Location: ../accueil.php&msg='.rawurlencode("Vous n'êtes pas autorisé à saisir la synthèse d'une classe."));
			die();
		}
	}
	elseif($_SESSION['statut']=='scolarite') {
		$sql="SELECT DISTINCT id, classe FROM j_scol_classes jsc, classes c WHERE jsc.id_classe=c.id ORDER BY c.classe;";
		$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_classe)==0) {
			header('Location: ../accueil.php&msg='.rawurlencode("Vous n'êtes pas autorisé à saisir la synthèse pour une classe."));
			die();
		}
	}
	elseif($_SESSION['statut']=='cpe') {
		if(getSettingAOui('GepiRubConseilCpeTous')) {
			$sql="SELECT DISTINCT id, classe FROM classes c ORDER BY c.classe;";
			$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_classe)==0) {
				header('Location: ../accueil.php&msg='.rawurlencode("Aucune classe n'a été trouvée."));
				die();
			}
		}
		elseif(getSettingAOui('GepiRubConseilCpe')) {
			$sql="SELECT DISTINCT id, classe FROM j_eleves_cpe jecpe, j_eleves_classes jec, classes c WHERE jecpe.cpe_login='".$_SESSION['login']."' AND jec.login=jecpe.e_login AND jec.id_classe=c.id ORDER BY c.classe;";
			$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_classe)==0) {
				header('Location: ../accueil.php&msg='.rawurlencode("Vous n'êtes pas autorisé à saisir la synthèse pour une classe."));
				die();
			}
		}
	}
	elseif($_SESSION['statut']=='secours') {
		$sql="SELECT DISTINCT id, classe FROM classes c ORDER BY c.classe;";
		$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_classe)==0) {
			header('Location: ../accueil.php&msg='.rawurlencode("Aucune classe n'a été trouvée."));
			die();
		}
	}
	else {
		header('Location: ../accueil.php&msg='.rawurlencode("Statut incorrect."));
		die();
	}

	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Autre classe</a>\n";
	echo "</p>\n";

	$nombreligne = mysqli_num_rows($res_classe);
	echo "<p>Cliquez sur la classe pour laquelle vous souhaitez saisir la synthèse des appréciations groupe classe.</p>\n";
	//echo "<table border=0>\n";
	$nb_class_par_colonne=round($nombreligne/3);
		//echo "<table width='100%' border='1'>\n";
		echo "<table width='100%' summary='Choix de la classe'>\n";
		echo "<tr valign='top' align='center'>\n";
		echo "<td align='left'>\n";
	$i = 0;
	while ($i < $nombreligne){
		$id_classe = old_mysql_result($res_classe, $i, "id");
		$classe_liste = old_mysql_result($res_classe, $i, "classe");
		//echo "<tr><td><a href='index3.php?id_classe=$id_classe'>$classe_liste</a></td></tr>\n";
		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td align='left'>\n";
		}
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>$classe_liste</a><br />\n";
		$i++;
	}
	echo "</table>\n";

}
else {
	// Choix de la période de saisie et des périodes d'affichage
	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

	echo "</p>\n";

	$sql="SELECT * FROM classes WHERE id='$id_classe'";
	$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_classe)==0) {
		echo "<p style='color:red'>La classe choisie n'existe pas.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	$nom_classe = old_mysql_result($res_classe, 0, "classe");

	echo "<p class='grand'>Classe de $nom_classe</p>\n";

	include "../lib/periodes.inc.php";

	$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' AND verouiller!='O';";
	$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_classe)==0) {
		echo "<p>Toutes les périodes sont closes pour cette classe.<br />Plus aucune modification n'est possible.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";

	echo "<p>Choisissez la période pour laquelle vous souhaitez saisir la synthèse&nbsp;: <br />\n";
	$cpt=0;
	while($lig=mysqli_fetch_object($res_per)) {
		echo "<input type='radio' name='num_periode' id='num_periode_$lig->num_periode' value='$lig->num_periode' ";
		if($cpt==0) {echo "checked ";}
		echo "/><label for='num_periode_$lig->num_periode'>".$nom_periode[$lig->num_periode]."</label><br />\n";
		$cpt++;
	}

	echo "<p>Choisissez la(les) période(s) à afficher dans le bulletin de classe&nbsp;: <br />\n";
	echo "De la période : <select onchange=\"change_periode()\" size=1 name=\"periode1\">\n";
	$i = "1" ;
	while ($i < $nb_periode) {
		echo "<option value=$i>$nom_periode[$i] </option>\n";
		$i++;
	}
	echo "</select>\n";
	echo "&nbsp;à la période : <select size=1 name=\"periode2\">\n";
	$i = "1" ;
	while ($i < $nb_periode) {
		echo "<option value=$i>$nom_periode[$i] </option>\n";
		$i++;
	}
	echo "</select>\n";
	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
	echo "<br /><br /><center><input type='submit' value='Valider' /></center>\n";
	echo "</form>\n";

}

require("../lib/footer.inc.php");
?>
