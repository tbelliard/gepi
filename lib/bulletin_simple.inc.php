<?php
/*
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*/
$delais_apres_cloture=getSettingValue('delais_apres_cloture');
//echo "\$delais_apres_cloture=$delais_apres_cloture<br />";


if(!isset($signalement_id_groupe)) {
	$signalement_id_groupe=array();
}

function bulletin($tab_moy,$current_eleve_login,$compteur,$total,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$test_coef,$affiche_categories,$couleur_lignes=NULL) {
//global $nb_notes,$nombre_eleves,$type_etablissement,$type_etablissement2;
global $nb_notes,$type_etablissement,$type_etablissement2;

global $display_moy_gen;
global $affiche_coef;
global $bull_intitule_app;

global $affiche_deux_moy_gen;

global $affiche_colonne_moy_classe;
//$affiche_colonne_moy_classe="n";

global $affiche_moy_gen;
global $affiche_moy_cat;

if(($_SESSION['statut']=='eleve')&&(!getSettingAOui('GepiAccesBulletinSimpleMoyGenEleve'))) {
	$affiche_moy_gen="n";
}
elseif(($_SESSION['statut']=='responsable')&&(!getSettingAOui('GepiAccesBulletinSimpleMoyGenResp'))) {
	$affiche_moy_gen="n";
}

if(($_SESSION['statut']=='eleve')&&(!getSettingAOui('GepiAccesBulletinSimpleMoyCatEleve'))) {
	$affiche_moy_cat="n";
}
elseif(($_SESSION['statut']=='responsable')&&(!getSettingAOui('GepiAccesBulletinSimpleMoyCatResp'))) {
	$affiche_moy_cat="n";
}

//echo "\$affiche_moy_gen=$affiche_moy_gen<br />";
//echo "\$affiche_moy_cat=$affiche_moy_cat<br />";

if($affiche_moy_gen=="n") {
	$display_moy_gen="n";
	$affiche_deux_moy_gen="n";
}

global $gepi_denom_mention;
if($gepi_denom_mention=='') {$gepi_denom_mention="mention";}

//=========================
global $utilisation_tablekit;
if($periode1!=$periode2) {
	//unset($utilisation_tablekit);
	$utilisation_tablekit="no";
}
//=========================
// Pour éviter dans bulletin() d'afficher le lien vers saisie_avis2.php depuis la page saisie_avis2.php
global $temoin_page_courante;

global $bull_simp_larg_tab,
	$bull_simp_larg_col1,
	$bull_simp_larg_col2,
	$bull_simp_larg_col3,
	$bull_simp_larg_col4;

global $themessage;
if((!isset($themessage))||($themessage=="")) {
	$themessage="Des saisies n\'ont pas été enregistrées. Voulez-vous vraiment quitter sans enregistrer ?";
}

$alt=1;

// Initialisation des tableaux 
// $tab_afficher_liens_modif_app[$id_groupe][$num_per]
// $tab_afficher_liens_valider_modif_app[$loop_per]
//$tab_afficher_liens_modif_app=array();
//$tab_afficher_liens_valider_modif_app=array();
$tmp_tab=afficher_liens_modif_app($id_classe, $periode1, $periode2);
$tab_afficher_liens_modif_app=$tmp_tab[0];
$tab_afficher_liens_valider_modif_app=$tmp_tab[1];

$afficher_proposition_correction="n";
if(count($tab_afficher_liens_modif_app)>0) {
	$afficher_proposition_correction="y";
}
/*
echo "<pre>";
print_r($tab_afficher_liens_modif_app);
echo "</pre>";
*/
$tab_statuts_signalement_faute_autorise=array('administrateur', 'professeur', 'cpe', 'scolarite');
$afficher_signalement_faute="n";
if(in_array($_SESSION['statut'],$tab_statuts_signalement_faute_autorise)) {
	if(($_SESSION['statut']=='professeur')&&(mb_substr(getSettingValue('autoriser_signalement_faute_app_prof'),0,1)=='y')) {
		$afficher_signalement_faute="y";
	}
	elseif(($_SESSION['statut']=='professeur')&&(mb_substr(getSettingValue('autoriser_signalement_faute_app_pp'),0,1)=='y')) {
		// Tester si le prof est pp de la classe
		if(is_pp($_SESSION['login'],$id_classe)) {$afficher_signalement_faute="y";}
	}
	elseif(($_SESSION['statut']=='scolarite')&&(mb_substr(getSettingValue('autoriser_signalement_faute_app_scol'),0,1)=='y')) {
		$afficher_signalement_faute="y";
	}
	elseif(($_SESSION['statut']=='cpe')&&(mb_substr(getSettingValue('autoriser_signalement_faute_app_cpe'),0,1)=='y')) {
		$afficher_signalement_faute="y";
	}
}

if(($afficher_signalement_faute=='y')||($afficher_proposition_correction=="y")) {
	// A N'INSERER QUE POUR LES COMPTES DE PERSONNELS... de façon à éviter de donner les mails des profs à des élèves

	if((!isset($necessaire_signalement_fautes_insere))||($necessaire_signalement_fautes_insere=="n")) {
		lib_signalement_fautes();
	}
	global $signalement_id_groupe;

	$envoi_mail_actif=getSettingValue('envoi_mail_actif');
}

global $mysqli;
$tab_modif_app_proposees=array();
$tab_mes_groupes=array();
if($_SESSION['statut']=='professeur') {
	//$tab_mes_groupes=array();
	$sql = "SELECT jgp.id_groupe FROM j_groupes_professeurs jgp WHERE login = '" . $_SESSION['login'] . "';" ;
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_mes_groupes[]=$lig->id_groupe;

			$sql="SELECT * FROM matieres_app_corrections WHERE id_groupe='$lig->id_groupe';";
			$res_mad=mysqli_query($mysqli, $sql);
			if($res_mad->num_rows>0) {
				while($lig_mad=$res_mad->fetch_object()) {
					$tab_modif_app_proposees[$lig_mad->id_groupe][$lig_mad->periode][$lig_mad->login]=$lig_mad->appreciation;
				}
			}
		}
	}

	if(getSettingAOui('visuCorrectionsAppProposeesProfs')) {
		$sql="SELECT DISTINCT ma.* FROM matieres_app_corrections ma,j_groupes_classes jgc WHERE jgc.id_groupe=ma.id_groupe AND jgc.id_classe='$id_classe';";
		//echo "$sql<br />";
		$res_mad=mysqli_query($mysqli, $sql);
		if($res_mad->num_rows>0) {
			while($lig_mad=$res_mad->fetch_object()) {
				$tab_modif_app_proposees[$lig_mad->id_groupe][$lig_mad->periode][$lig_mad->login]=$lig_mad->appreciation;
			}
		}
	}

	if((!isset($necessaire_corriger_appreciation_insere))||($necessaire_corriger_appreciation_insere=="n")) {
		lib_corriger_appreciation();
	}
	global $corriger_app_id_groupe;
	//global $tab_mes_groupes;
/*
echo "tab_mes_groupes:<pre>";
print_r($tab_mes_groupes);
echo "</pre>";
*/
}
elseif($_SESSION['statut']=='scolarite') {
	$sql="SELECT DISTINCT ma.* FROM matieres_app_corrections ma,j_groupes_classes jgc WHERE jgc.id_groupe=ma.id_groupe AND jgc.id_classe='$id_classe';";
	//echo "$sql<br />";
	$res_mad=mysqli_query($mysqli, $sql);
	if($res_mad->num_rows>0) {
		while($lig_mad=$res_mad->fetch_object()) {
			$tab_modif_app_proposees[$lig_mad->id_groupe][$lig_mad->periode][$lig_mad->login]=$lig_mad->appreciation;
		}
	}

	if(getSettingAOui('AccesModifAppreciationScol')) {
		if((!isset($necessaire_corriger_appreciation_insere))||($necessaire_corriger_appreciation_insere=="n")) {
			lib_corriger_appreciation();
		}
	}
}

// données requise :
//- le login de l'élève    : $current_eleve_login
//- $compteur : compteur
//- $total : nombre total d'élèves
//- $periode1 : numéro de la première période à afficher
//- $periode2 : numéro de la dernière période à afficher
//- $nom_periode : tableau des noms de période
//- $gepiYear : année
//- $id_classe : identifiant de la classe.

//==========================================================
// AJOUT: boireaus 20080218
//        Dispositif de restriction des accès aux appréciations pour les comptes responsables/eleves
//echo "\$_SESSION['statut']=".$_SESSION['statut']."<br />";
//echo "\$periode1=$periode1<br />";
//echo "\$periode2=$periode2<br />";

unset($tab_acces_app);
$tab_acces_app=array();
//$tab_acces_app = acces_appreciations($periode1, $periode2, $id_classe);
$tab_acces_app = acces_appreciations($periode1, $periode2, $id_classe, '', $current_eleve_login);
//==========================================================

$nb_periodes = $periode2 - $periode1 + 1;
$on_continue = "yes";
if ($nb_periodes == 1) {
	// S'il n'est demandé qu'une seule période:
	// Test pour savoir si l'élève appartient à la classe pour la période considérée
	$test_eleve_app = sql_query1("select count(login) from j_eleves_classes where login='".$current_eleve_login."' and id_classe='".$id_classe."' and periode='".$periode1."'");
	if ($test_eleve_app == 0) {$on_continue = "no";}
}

if ($on_continue == 'yes') {

	// Mis hors de la fonction
	//$affiche_coef=sql_query1("SELECT display_coef FROM classes WHERE id='".$id_classe."'");

	//echo "\$affiche_categories=$affiche_categories<br />";
	$data_eleve = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM eleves WHERE login='$current_eleve_login'");

	// Récupération du champ auto_increment
	$current_id_eleve = old_mysql_result($data_eleve, 0, "id_eleve");

	$current_eleve_nom = old_mysql_result($data_eleve, 0, "nom");
	$current_eleve_prenom = old_mysql_result($data_eleve, 0, "prenom");
	$current_eleve_sexe = old_mysql_result($data_eleve, 0, "sexe");
	$current_eleve_naissance = old_mysql_result($data_eleve, 0, "naissance");
	$current_eleve_naissance = affiche_date_naissance($current_eleve_naissance);
	$current_eleve_elenoet = old_mysql_result($data_eleve, 0, "elenoet");
	$data_profsuivi = mysqli_query($GLOBALS["mysqli"], "SELECT u.login FROM utilisateurs u, j_eleves_professeurs j WHERE (j.login='$current_eleve_login' AND j.professeur = u.login AND j.id_classe='$id_classe') ");
	$current_eleve_profsuivi_login = @old_mysql_result($data_profsuivi, 0, "login");

	echo "<input type='hidden' name='nom_prenom_eleve[$current_id_eleve]' id='nom_prenom_eleve_$current_id_eleve' value=\"$current_eleve_nom $current_eleve_prenom\" />\n";

	$data_etab = mysqli_query($GLOBALS["mysqli"], "SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve ='$current_eleve_elenoet' AND e.id = j.id_etablissement) ");
	$current_eleve_etab_id = @old_mysql_result($data_etab, 0, "id");
	$current_eleve_etab_nom = @old_mysql_result($data_etab, 0, "nom");
	$current_eleve_etab_niveau = @old_mysql_result($data_etab, 0, "niveau");
	$current_eleve_etab_type = @old_mysql_result($data_etab, 0, "type");
	$current_eleve_etab_cp = @old_mysql_result($data_etab, 0, "cp");
	$current_eleve_etab_ville = @old_mysql_result($data_etab, 0, "ville");
	
	if ($current_eleve_etab_niveau!='') {
		foreach ($type_etablissement as $type_etab => $nom_etablissement) {
			if ($current_eleve_etab_niveau == $type_etab) {$current_eleve_etab_niveau_nom = $nom_etablissement;}
	
		}
		if ($current_eleve_etab_cp == 0) {$current_eleve_etab_cp = '';}
		if ($current_eleve_etab_type == 'aucun')
			$current_eleve_etab_type = '';
		else
			$current_eleve_etab_type = $type_etablissement2[$current_eleve_etab_type][$current_eleve_etab_niveau];
	}
	$classe_eleve = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes WHERE id='$id_classe'");
	$current_eleve_classe = old_mysql_result($classe_eleve, 0, "classe");
	$id_classe = old_mysql_result($classe_eleve, 0, "id");
	
	$regime_doublant_eleve = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM j_eleves_regime WHERE login = '$current_eleve_login'");
	$current_eleve_regime = old_mysql_result($regime_doublant_eleve, 0, "regime");
	$current_eleve_doublant = old_mysql_result($regime_doublant_eleve, 0, "doublant");
	
	//-------------------------------
	// On affiche l'en-tête : Les données de l'élève
	//-------------------------------
	echo "<span class='bull_simpl'><span class='bold'>$current_eleve_nom $current_eleve_prenom</span>";
	if ($current_eleve_sexe == "M") {
		echo ", né le $current_eleve_naissance";
		} else {
		echo ", née le $current_eleve_naissance";
	}
	if ($current_eleve_regime == "d/p") {echo ",&nbsp;demi-pensionnaire";}
	if ($current_eleve_regime == "ext.") {echo ",&nbsp;externe";}
	if ($current_eleve_regime == "int.") {echo ",&nbsp;interne";}
	if ($current_eleve_regime == "i-e")
		if ($current_eleve_sexe == "M") echo ",&nbsp;interne&nbsp;externé"; else echo ",&nbsp;interne&nbsp;externée";
	if ($current_eleve_doublant == 'R')
		if ($current_eleve_sexe == "M") echo ", <b>redoublant</b>"; else echo ", <b>redoublante</b>";
	echo "&nbsp;&nbsp;-&nbsp;&nbsp;Classe de $current_eleve_classe, année scolaire $gepiYear<br />\n";
	
	if ($current_eleve_etab_nom != '') {
		echo "Etablissement d'origine : ";
		if ($current_eleve_etab_id != '990') {
			echo "$current_eleve_etab_niveau_nom $current_eleve_etab_type $current_eleve_etab_nom ($current_eleve_etab_cp $current_eleve_etab_ville)<br />\n";
		} else {
			echo "hors de France<br />\n";
		}
	}
	if ($periode1 < $periode2) {
		echo "Résultats de : ";
		$nb = $periode1;
		while ($nb < $periode2+1) {
		echo $nom_periode[$nb];
		if ($nb < $periode2) echo " - ";
		$nb++;
		}
		echo ".</span>";
	} else {
		$temp = my_strtolower($nom_periode[$periode1]);
		echo "Résultats du $temp.</span>";
	
	}
	//
	//-------------------------------
	// Fin de l'en-tête

	// Initialisation de variable pour le cas où un élève n'est dans aucun enseignement sur une période
	$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black; border-bottom: 1px dashed black;";

	// On initialise le tableau :
	$bull_simp_larg_tab_defaut = 680;
	$bull_simp_larg_col1_defaut = 120;
	$bull_simp_larg_col2_defaut = 38;
	$bull_simp_larg_col3_defaut = 38;
	$bull_simp_larg_col4_defaut = 20;

	$larg_tab = $bull_simp_larg_tab_defaut;
	$larg_col1 = $bull_simp_larg_col1_defaut;
	$larg_col2 = $bull_simp_larg_col3_defaut;
	$larg_col3 = $bull_simp_larg_col3_defaut;
	$larg_col4 = $bull_simp_larg_col4_defaut;

	if(preg_match("/^[0-9]{1,}$/", $bull_simp_larg_tab)) {
		$larg_tab=$bull_simp_larg_tab;
	}
	if(preg_match("/^[0-9]{1,}$/", $bull_simp_larg_col1)) {
		$larg_col1=$bull_simp_larg_col1;
	}
	if(preg_match("/^[0-9]{1,}$/", $bull_simp_larg_col2)) {
		$larg_col2=$bull_simp_larg_col2;
	}
	if(preg_match("/^[0-9]{1,}$/", $bull_simp_larg_col3)) {
		$larg_col3=$bull_simp_larg_col3;
	}
	if(preg_match("/^[0-9]{1,}$/", $bull_simp_larg_col4)) {
		$larg_col4=$bull_simp_larg_col4;
	}

	if($bull_simp_larg_tab<$bull_simp_larg_col1+$bull_simp_larg_col2+$bull_simp_larg_col3+$bull_simp_larg_col4) {
		$larg_tab = $bull_simp_larg_tab_defaut;
		$larg_col1 = $bull_simp_larg_col1_defaut;
		$larg_col2 = $bull_simp_larg_col2_defaut;
		$larg_col3 = $bull_simp_larg_col3_defaut;
		$larg_col4 = $bull_simp_larg_col4_defaut;
	}

	$larg_col5 = $larg_tab - $larg_col1 - $larg_col2 - $larg_col3 - $larg_col4;

	//=========================
	// MODIF: boireaus 20080315
	//echo "<table width=$larg_tab border=1 cellspacing=1 cellpadding=1>\n";
	echo "<table width='$larg_tab' style='background-color:white' class='boireaus";
	if($utilisation_tablekit=="ok") {
		echo " sortable resizable";
	}
	echo "' cellspacing='1' cellpadding='1' summary='Matières/Notes/Appréciations'>\n";
	//=========================
	echo "<thead>\n";
	echo "<tr><td width=\"$larg_col1\" class='bull_simpl text'>$compteur";
	if ($total != '') {echo "/$total";}
	echo "</td>\n";
	
	//====================
	// Modif: boireaus 20070626
	if($affiche_coef=='y'){
		if ($test_coef != 0) echo "<td width=\"$larg_col2\" align=\"center\" class='number'><p class='bull_simpl'>Coef.</p></td>\n";
	}
	//====================

	if($affiche_colonne_moy_classe!='n') {
		echo "<td width=\"$larg_col2\" align=\"center\" class='bull_simpl text'>Classe</td>\n";
	}
	echo "<td width=\"$larg_col3\" align=\"center\" class='bull_simpl number'>&Eacute;lève</td>\n";
	if ($affiche_rang=='y') {
		echo "<td width=$larg_col4 align=\"center\" class='bull_simpl number'><i>Rang</i></td>\n";
	}
	echo "<td width=\"$larg_col5\" class='bull_simpl nosort'>$bull_intitule_app</td>\n";
	if($afficher_signalement_faute=='y') {
		// A N'INSERER QUE POUR LES COMPTES DE PERSONNELS... de façon à éviter de donner les mails des profs à des élèves
		echo "<td class='bull_simpl noprint'>Signaler</td>\n";
	}
	echo "</tr>\n";
	echo "</thead>\n";

	echo "<tbody>\n";
	//echo "</table>";
	// On attaque maintenant l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en tête des bulletins :
	$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_config WHERE order_display1 = 'b' ORDER BY order_display2");
	$nb_aid = mysqli_num_rows($call_data);
	$z=0;
	while ($z < $nb_aid) {
		$display_begin = old_mysql_result($call_data, $z, "display_begin");
		$display_end = old_mysql_result($call_data, $z, "display_end");
		if (($periode1 >= $display_begin) and ($periode2 <= $display_end)) {
			$indice_aid = @old_mysql_result($call_data, $z, "indice_aid");
			$aid_query = mysqli_query($GLOBALS["mysqli"], "SELECT id_aid FROM j_aid_eleves WHERE (login='$current_eleve_login' and indice_aid='$indice_aid')");
			$aid_id = @old_mysql_result($aid_query, 0, "id_aid");
			if ($aid_id != '') {
				affiche_aid_simple($affiche_rang, $test_coef,$indice_aid,$aid_id,$current_eleve_login,$periode1,$periode2,$id_classe, 'bull_simpl', $affiche_coef);
			}
		}
		$z++;
	}
	//------------------------------
	// Boucle 'groupes'
	//------------------------------

	// Récupération des noms de categories
	$get_cat = mysqli_query($GLOBALS["mysqli"], "SELECT id FROM matieres_categories");
	$categories = array();
	while ($row = mysqli_fetch_array($get_cat,  MYSQLI_ASSOC)) {
		$categories[] = $row["id"];
	}
	
	$cat_names = array();
	foreach ($categories as $cat_id) {
		$cat_names[$cat_id] = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT nom_complet FROM matieres_categories WHERE id = '" . $cat_id . "'"), 0);
	}

	// Nombre de groupes sur la classe
	$nombre_groupes=count($tab_moy['current_group']);

	// Récupération des indices de l'élève $current_eleve_login dans $tab_moy
	unset($tab_login_indice);
	$nb=$periode1;
	while ($nb < $periode2+1) {
		//$tab_login_indice[$nb]=$tab_moy['periodes'][$nb]['tab_login_indice'][$current_eleve_login];
		// Un élève qui arrive ou part en cours d'année ne sera pas dans la classe ni dans les groupes sur certaines périodes
		//if(isset($tab_moy['periodes'][$nb]['tab_login_indice'][$current_eleve_login])) {
		if(isset($tab_moy['periodes'][$nb]['tab_login_indice'][my_strtoupper($current_eleve_login)])) {
			//$tab_login_indice[$nb]=$tab_moy['periodes'][$nb]['tab_login_indice'][$current_eleve_login];
			$tab_login_indice[$nb]=$tab_moy['periodes'][$nb]['tab_login_indice'][my_strtoupper($current_eleve_login)];
			//echo "\$tab_login_indice[$nb]=$tab_login_indice[$nb]<br />";
		}
		/*
		else {
			echo "\$tab_moy['periodes'][$nb]['tab_login_indice'][$current_eleve_login] n'est pas affecté.<br />";
		}
		*/
		$nb++;
	}

	//$j = 0;

	$prev_cat_id = null;

	//while ($j < $nombre_groupes) {
	for($j=0;$j<$nombre_groupes;$j++) {

			//echo "<table width=$larg_tab border=1 cellspacing=0 cellpadding=1 style='margin-bottom: 0px; border-bottom: 1px solid black; border-top: none;'>";
	
		$inser_ligne='no';

		//$group_id = old_mysql_result($appel_liste_groupes, $j, "id_groupe");
		//$current_group = get_group($group_id);

		// On récupère le groupe depuis $tab_moy
		$current_group=$tab_moy['current_group'][$j];
		//echo "<p>Groupe n°$j: ".$current_group['name']."<br />\n";

		// Coefficient pour le groupe
		$current_coef=$current_group["classes"]["classes"][$id_classe]["coef"];
	
		// Pour les enseignements à bonus,...
		$mode_moy=$current_group["classes"]["classes"][$id_classe]["mode_moy"];
	
		$current_matiere_professeur_login = $current_group["profs"]["list"];

		//$current_matiere_nom_complet = $current_group["matiere"]["nom_complet"];
		if(getSettingValue('bul_rel_nom_matieres')=='nom_groupe') {
			$current_matiere_nom_complet = $current_group["name"];
		}
		elseif(getSettingValue('bul_rel_nom_matieres')=='description_groupe') {
			$current_matiere_nom_complet = $current_group["description"];
		}
		else {
			$current_matiere_nom_complet = $current_group["matiere"]["nom_complet"];
		}


		$nb=$periode1;
		while ($nb < $periode2+1) {
			$current_classe_matiere_moyenne[$nb]=$tab_moy['periodes'][$nb]['current_classe_matiere_moyenne'][$j];

			// On teste si des notes de une ou plusieurs boites du carnet de notes doivent être affichée
			$test_cn = mysqli_query($GLOBALS["mysqli"], "select c.nom_court, c.id from cn_cahier_notes cn, cn_conteneurs c
			where (cn.periode = '$nb' and cn.id_groupe='".$current_group["id"]."' and cn.id_cahier_notes = c.id_racine and c.id_racine!=c.id and c.display_bulletin = 1) ");
			$nb_ligne_cn[$nb] = mysqli_num_rows($test_cn);
			$n = 0;
			while ($n < $nb_ligne_cn[$nb]) {
				$cn_id[$nb][$n] = old_mysql_result($test_cn, $n, 'id');
				$cn_nom[$nb][$n] = old_mysql_result($test_cn, $n, 'nom_court');
				$n++;
			}
			$nb++;
	
		}
	
	
	
		// Maintenant on regarde si l'élève suit bien cette matière ou pas
		//-----------------------------
		$nb=$periode1;
		while ($nb < $periode2+1) {
			// Test supplémentaire pour savoir si l'élève appartient à la classe pour la période considérée
			$test_eleve_app = sql_query1("select count(login) from j_eleves_classes where login='".$current_eleve_login."' and id_classe='".$id_classe."' and periode='".$nb."'");
			if(
				(
					(in_array($current_eleve_login, $current_group["eleves"][$nb]["list"])) or
					(in_array(my_strtolower($current_eleve_login), $current_group["eleves"][$nb]["list"])) or
					(in_array(my_strtoupper($current_eleve_login), $current_group["eleves"][$nb]["list"]))
				) and 
				($test_eleve_app!=0)
			) {
				$inser_ligne='yes';
				$current_eleve_note[$nb]=$tab_moy['periodes'][$nb]['current_eleve_note'][$j][$tab_login_indice[$nb]];
				$current_eleve_statut[$nb]=$tab_moy['periodes'][$nb]['current_eleve_statut'][$j][$tab_login_indice[$nb]];

				$current_eleve_appreciation_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_appreciations ma, j_eleves_classes jec WHERE (ma.login='$current_eleve_login' AND ma.id_groupe='" . $current_group["id"] . "' AND ma.periode='$nb' and jec.periode='$nb' and jec.login='$current_eleve_login' and jec.id_classe='$id_classe')");
				$current_eleve_appreciation[$nb] = @old_mysql_result($current_eleve_appreciation_query, 0, "appreciation");

				$coef_eleve=$tab_moy['periodes'][$nb]['current_coef_eleve'][$tab_login_indice[$nb]][$j];

				//echo "\$coef_eleve=\$tab_moy['periodes'][$nb]['current_coef_eleve'][".$tab_login_indice[$nb]."][$j]=".$coef_eleve."<br />\n";

			} else {
				$current_eleve_note[$nb] = '';
				$current_eleve_statut[$nb] = 'Non suivie';
				$current_eleve_appreciation[$nb] = '';
			}
	

			//++++++++++++++++++++++++
			// Modif d'après F.Boisson
			// notes dans appreciation
			$sql="SELECT cnd.note, cd.note_sur FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE cnd.login='".$current_eleve_login."' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group["id"]."' AND ccn.periode='$nb' AND cnd.statut='';";
			$result_nbct=mysqli_query($GLOBALS["mysqli"], $sql);
			$string_notes='';
			if ($result_nbct ) {
				while ($snnote =  mysqli_fetch_assoc($result_nbct)) {
					if ($string_notes != '') $string_notes .= ", ";
					$string_notes .= $snnote['note'];
					if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $snnote['note_sur']!=getSettingValue("referentiel_note")) {
						$string_notes .= "/".$snnote['note_sur'];
					}
				}
			}
			$current_eleve_appreciation[$nb] = str_replace('@@Notes', $string_notes,$current_eleve_appreciation[$nb]);
			//++++++++++++++++++++++++
		
	
			$nb++;
		}




		if ($inser_ligne == 'yes') {
			if ($affiche_categories) {
			// On regarde si on change de catégorie de matière
				if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat_id) {
					$prev_cat_id = $current_group["classes"]["classes"][$id_classe]["categorie_id"];
					// On est dans une nouvelle catégorie
					// On récupère les infos nécessaires, et on affiche une ligne
	
					// On détermine le nombre de colonnes pour le colspan
					if($affiche_colonne_moy_classe=='n') {
						$nb_total_cols = 3;
					}
					else {
						$nb_total_cols = 4;
					}
					//====================
					// Modif: boireaus 20070626
					if($affiche_coef=='y'){
						if ($test_coef != 0) {$nb_total_cols++;}
					}
					//====================
					if ($affiche_rang == 'y') {$nb_total_cols++;}
	
					// On regarde s'il faut afficher la moyenne de l'élève pour cette catégorie
					$affiche_cat_moyenne_query = mysqli_query($GLOBALS["mysqli"], "SELECT affiche_moyenne FROM j_matieres_categories_classes WHERE (classe_id = '" . $id_classe . "' and categorie_id = '" . $prev_cat_id . "')");
					if (mysqli_num_rows($affiche_cat_moyenne_query) == "0") {
						$affiche_cat_moyenne = false;
					} else {
						$affiche_cat_moyenne = old_mysql_result($affiche_cat_moyenne_query, 0);
					}
	
					// On a toutes les infos. On affiche !
					echo "<tr>\n";
					echo "<td colspan='" . $nb_total_cols . "'>\n";
					echo "<p style='padding: 0; margin:0; font-size: 10px; text-align:left;'>".$cat_names[$prev_cat_id]."</p></td>\n";

					if($afficher_signalement_faute=='y') {
						// A N'INSERER QUE POUR LES COMPTES DE PERSONNELS... de façon à éviter de donner les mails des profs à des élèves
						echo "<td class='bull_simpl noprint'>-</td>\n";
					}

					echo "</tr>\n";
				}
			}
			if($couleur_lignes=='y') {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				$alt2=$alt;
			}
			else {
				echo "<tr>\n";
			}
			echo "<td ";
			if ($nb_periodes > 1) echo " rowspan= ".$nb_periodes;
			//echo" width=\"$larg_col1\" class='bull_simpl'><b>$current_matiere_nom_complet</b>";
			echo " width=\"$larg_col1\" class='bull_simpl'><b>".htmlspecialchars($current_matiere_nom_complet)."</b>";
			$k = 0;
			//echo "(".$current_group['id'].")";
			$liste_email_profs_du_groupe="";
			$liste_profs_du_groupe="";
			while ($k < count($current_matiere_professeur_login)) {
				echo "<br /><i>".affiche_utilisateur($current_matiere_professeur_login[$k],$id_classe)."</i>";
				if($k>0) {$liste_profs_du_groupe.="|";}
				$liste_profs_du_groupe.=$current_matiere_professeur_login[$k];

				$tmp_mail=retourne_email($current_matiere_professeur_login[$k]);
				if($tmp_mail!='') {
					if($liste_email_profs_du_groupe!='') {
						$liste_email_profs_du_groupe.=", ";
					}
					$liste_email_profs_du_groupe.=$tmp_mail;
				}
				$k++;
			}

			if(!isset($signalement_id_groupe[$current_group['id']])) {
				echo "<input type='hidden' name='signalement_id_groupe[".$current_group['id']."]' id='signalement_id_groupe_".$current_group['id']."' value=\"".$current_group['name']." (".$current_group['name']." en ".$current_group['classlist_string'].")\" />\n";
			}

			echo "</td>\n";
	
			//====================
			// Modif: boireaus 20070626
			if($affiche_coef=='y'){
				if ($test_coef != 0) {
					//if ($current_coef > 0) $print_coef= $current_coef ; else $print_coef='-';
					//if ($coef_eleve > 0) $print_coef= $coef_eleve; else $print_coef='-';
					if ($coef_eleve > 0) {$print_coef= number_format($coef_eleve,1, ',', ' ');} else {$print_coef='-';}
					echo "<td width=\"$larg_col2\"";
					if ($nb_periodes > 1) echo " rowspan= ".$nb_periodes;
					echo " align=\"center\"><p class='bull_simpl'>".$print_coef."</p></td>\n";
				}
			}
			//====================
	
			$nb=$periode1;
			$print_tr = 'no';
			while ($nb < $periode2+1) {
				if ($print_tr == 'yes') {
					//echo "<tr style='border-width: 5px;'>\n";
					if($couleur_lignes=='y') {
						$alt2=$alt2*(-1);
						echo "<tr class='lig$alt2' style='border-width: 5px;'>\n";
					}
					else {
						echo "<tr>\n";
					}
				}
				//=========================
				// MODIF: boireaus 20080315
				//echo "<td width=\"$larg_col2\" align=\"center\" class='bull_simpl'>\n";
				//if($nb>$periode1) {$style_bordure_cell="border: 1px dashed black;";} else {$style_bordure_cell="";}
				//$style_bordure_cell="border: 1px dashed black;";
				if($nb==$periode1) {
					if($nb==$periode2) {
						$style_bordure_cell="border: 1px solid black";
					}
					else {
						$style_bordure_cell="border: 1px solid black; border-bottom: 1px dashed black";
					}
				}
				elseif($nb==$periode2) {
					$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black;";
				}
				else {
					$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black; border-bottom: 1px dashed black;";
				}

				if($affiche_colonne_moy_classe!='n') {
					echo "<td width=\"$larg_col2\" align=\"center\" class='bull_simpl' style='$style_bordure_cell'>\n";
					//=========================
					//echo "\$nb=$nb<br />";
					$note=number_format($current_classe_matiere_moyenne[$nb],1, ',', ' ');
					if ($note != "0,0")  {echo $note;} else {echo "-";}
					echo "</td>\n";
				}

				echo "<td width=\"$larg_col3\" align=\"center\" class='bull_simpl' style='$style_bordure_cell'>\n<b>";
				$flag_moy[$nb] = 'no';
				if ($current_eleve_note[$nb] != '') {
					if ($current_eleve_statut[$nb] != '') {
						echo $current_eleve_statut[$nb];
					} else {
						//$note=number_format($current_eleve_note[$nb],1, ',', ' ');
						$note=nf($current_eleve_note[$nb]);
						echo "$note";
						$flag_moy[$nb] = 'yes';
					}
				} else {
					echo '-';
				}
				echo "</b></td>\n";

				//Affichage des cellules rang le cas échéant
				if ($affiche_rang == 'y')  {
					/*
					$rang = sql_query1("select rang from matieres_notes where (
					periode = '".$nb."' and
					id_groupe = '".$current_group["id"]."' and
					login = '".$current_eleve_login."' )
					");
					if (($rang == 0) or ($rang == -1)){
						$rang = "-";
					}
					else{
						//$rang.="/".$nb_notes[$current_group["id"]][$nb];
						//if(isset($nb_notes[$current_group["id"]][$nb])){
							$rang.="/".$nb_notes[$current_group["id"]][$nb];
						//}
						//$rang.="<br />\$nb_notes[".$current_group["id"]."][$nb]";
					}
					*/

					$rang="-";
					if(isset($tab_login_indice[$nb])) {
						if(isset($tab_moy['periodes'][$nb]['current_eleve_rang'][$j][$tab_login_indice[$nb]])) {
							// Si l'élève n'est dans le groupe que sur une période (cas des IDD), son rang n'existera pas sur certaines périodes
							//echo "\$tab_moy['periodes'][$nb]['current_eleve_rang'][$j][".$tab_login_indice[$nb]."]=";
							$rang=$tab_moy['periodes'][$nb]['current_eleve_rang'][$j][$tab_login_indice[$nb]];
							//echo "$rang<br />";
						}
					}
					$eff_grp_avec_note=$tab_moy['periodes'][$nb]['current_group_effectif_avec_note'][$j];
					if(($rang!=0)&&($rang!=-1)&&($rang!='')&&($rang!='-')){
						$rang.="/$eff_grp_avec_note";
					}

					echo "<td width=\"$larg_col4\" align=\"center\" class='bull_simpl' style='$style_bordure_cell'><i>".$rang."</i></td>\n";
				}

				// Affichage des cases appréciations
				echo "<td width=\"$larg_col5\" class='bull_simpl' style='$style_bordure_cell; text-align:left;'>\n";
				// Affichage des moyennes secondaires
				if ($nb_ligne_cn[$nb] != 0) {
					$tiret = 'no';
					for ($cn=0; $cn<$nb_ligne_cn[$nb]; $cn++) {
						$appel_cn = mysqli_query($GLOBALS["mysqli"], "select note, statut from cn_notes_conteneurs where (login='$current_eleve_login' and id_conteneur='".$cn_id[$nb][$cn]."')");
						$cn_statut = @old_mysql_result($appel_cn,0,'statut');
						if ($cn_statut == 'y') {
							$cn_note = @old_mysql_result($appel_cn,0,'note');
							if ($tiret == 'yes')   echo " - ";
							echo $cn_nom[$nb][$cn]."&nbsp;:&nbsp;".$cn_note;
							$tiret = 'yes';
						}
					}
					echo "<br />\n";
				}
				//==========================================================
				// MODIF: boireaus 20080218
				//        Dispositif de restriction des accès aux appréciations pour les comptes responsables/eleves
				//if ($current_eleve_appreciation[$nb]) {
				if (($current_eleve_appreciation[$nb])&&($tab_acces_app[$nb]=="y")) {
				//==========================================================
					//======================================
					// MODIF: boireaus
					//echo $current_eleve_appreciation[$nb];
					if ($current_eleve_appreciation[$nb]=="-1") {
						// 20120409
						echo "<div id='app_".$current_id_eleve."_".$current_group['id']."_$nb'>";
						echo "<span class='noprint'>-</span>\n";
						echo "</div>\n";
					}
					else {
						// 20120409
						echo "<div id='app_".$current_id_eleve."_".$current_group['id']."_$nb'>";

						if((strstr($current_eleve_appreciation[$nb],">"))||(strstr($current_eleve_appreciation[$nb],"<"))){
							echo "$current_eleve_appreciation[$nb]";
						}
						else{
							echo nl2br($current_eleve_appreciation[$nb]);
						}

						echo "</div>\n";

						// 20131207
						echo "<div id='proposition_app_".$current_id_eleve."_".$current_group['id']."_$nb' class='noprint'>";
						if(isset($tab_modif_app_proposees[$current_group['id']][$nb][$current_eleve_login])) {
							echo "<div style='border:1px solid red; color: green'><strong>Proposition de correction en attente&nbsp;:</strong><br />".$tab_modif_app_proposees[$current_group['id']][$nb][$current_eleve_login]."</div>";
						}
						echo "</div>";

						echo "<textarea name='appreciation_".$current_id_eleve."_".$current_group['id']."[$nb]' id='appreciation_".$current_id_eleve."_".$current_group['id']."_$nb' style='display:none;'>".$current_eleve_appreciation[$nb]."</textarea>\n";

					}
					//======================================
				} else {
					// 20120409
					echo "<div id='app_".$current_id_eleve."_".$current_group['id']."_$nb'>";
					echo "<span class='noprint'>-</span>\n";
					echo "</div>\n";
				}
				echo "</td>\n";

				if(($afficher_signalement_faute=='y')||($afficher_proposition_correction=='y')) {
					// A N'INSERER QUE POUR LES COMPTES DE PERSONNELS... de façon à éviter de donner les mails des profs à des élèves
					echo "<td class='bull_simpl noprint'>";

					if($current_group["classe"]["ver_periode"][$id_classe][$nb]=='O') {
						echo "-";
					}
					else {
						// 20120409
						echo affiche_lien_proposition_ou_correction_appreciation($current_eleve_login, $current_id_eleve, $current_eleve_prenom, $current_eleve_nom, $current_group, $id_classe, $nb, $liste_profs_du_groupe, $tab_mes_groupes, $tab_afficher_liens_modif_app);
						/*
						if(($_SESSION['statut']=='professeur')&&(in_array($current_group['id'],$tab_mes_groupes))) {
							if($current_group["classe"]["ver_periode"][$id_classe][$nb]=='N') {
								echo "<a href='#' onclick=\"modifier_une_appreciation('$current_eleve_login', '$current_id_eleve', '".$current_group['id']."', '$liste_profs_du_groupe', '$nb', 'corriger') ;return false;\" title=\"Modifier l'appréciation en période $nb pour $current_eleve_prenom $current_eleve_nom.
Si vous vous apercevez que vous avez fait une faute de frappe, ou si vous souhaitez modifier votre appréciation, ce lien est là pour ça.\" class='noprint'><img src='../images/edit16.png' width='16' height='16' /></a> ";
							}
							elseif(isset($tab_afficher_liens_modif_app[$current_group['id']][$nb])) {
								if($tab_afficher_liens_modif_app[$current_group['id']][$nb]=='y') {
									echo "<a href='#' onclick=\"modifier_une_appreciation('$current_eleve_login', '$current_id_eleve', '".$current_group['id']."', '$liste_profs_du_groupe', '$nb', 'proposer') ;return false;\" title=\"Proposer une correction de l'appréciation en période $nb pour $current_eleve_prenom $current_eleve_nom.
Si vous vous apercevez que vous avez fait une faute de frappe, ou si vous souhaitez simplement modifier votre appréciation, ce lien est là pour ça.\" class='noprint'><img src='../images/edit16.png' width='16' height='16' /></a> ";
								}
								elseif($tab_afficher_liens_modif_app[$current_group['id']][$nb]=='yy') {
									echo "<a href='#' onclick=\"modifier_une_appreciation('$current_eleve_login', '$current_id_eleve', '".$current_group['id']."', '$liste_profs_du_groupe', '$nb', 'corriger') ;return false;\" title=\"Modifier l'appréciation en période $nb pour $current_eleve_prenom $current_eleve_nom.
Si vous vous apercevez que vous avez fait une faute de frappe, ou si vous souhaitez modifier votre appréciation, ce lien est là pour ça.\" class='noprint'><img src='../images/edit16.png' width='16' height='16' /></a> ";
								}
								//echo "plop";
							}
						}
						*/

						// Tester si l'adresse mail du/des profs de l'enseignement est renseignée et si l'envoi de mail est actif.
						// Sinon, on pourrait enregistrer le signalement dans une table actions_signalements pour affichage comme le Panneau d'affichage
						if($afficher_signalement_faute=='y') {
							echo "<a href=\"mailto:$liste_email_profs_du_groupe?Subject=[Gepi]: Signaler un problème/faute&body=Bonjour,Je pense que vous avez commis une faute de frappe pour $current_eleve_login dans l enseignement n°".$current_group['id'].".Cordialement.-- ".casse_mot($_SESSION['prenom'],'majf2')." ".$_SESSION['nom']."\"";
							if($envoi_mail_actif!='n') {
								//echo " onclick=\"alert('plop');return false;\"";
								echo " onclick=\"signaler_une_faute('$current_eleve_login', '$current_id_eleve', '".$current_group['id']."', '$liste_profs_du_groupe', '$nb') ;return false;\"";
							}
							echo " title=\"Signaler une faute de frappe, d'orthographe ou autre...
Si vous vous apercevez que ce collègue a fait une erreur,
vous pouvez lui envoyer un mail pour l'alerter.
Ce lien est là pour ça.\" target='_blank'><img src='../images/icons/mail.png' width='16' height='16' alt='Signaler un problème/faute par mail' /></a>";

							echo "<span id='signalement_effectue_".$current_id_eleve."_".$current_group['id']."_$nb'></span>";
						}
					}
					echo "</td>\n";
				}

				echo "</tr>\n";
				$print_tr = 'yes';
				$nb++;
			}

			/*
			// On calcule les moyennes générales de l'élève et de la classe :
			if ($test_coef != 0) {
				$nb=$periode1;
				while ($nb < $periode2+1) {
					if ($flag_moy[$nb] == 'yes') {
		
						//===========================
						// MODIF: boireaus 20070627
						//$total_coef[$nb] += $current_coef;
						//$total_points_classe[$nb] += $current_coef*$current_classe_matiere_moyenne[$nb];
						//$total_points_eleve[$nb] += $current_coef*$current_eleve_note[$nb];
		
						if($mode_moy=='-') {
							$total_coef_eleve[$nb] += $coef_eleve;
							$total_points_eleve[$nb] += $coef_eleve*$current_eleve_note[$nb];
		
							$total_coef_classe[$nb] += $current_coef;
							$total_points_classe[$nb] += $current_coef*$current_classe_matiere_moyenne[$nb];
						}
						elseif($mode_moy=='sup10') {
							if($current_eleve_note[$nb]>10) {
								$total_points_eleve[$nb] += $coef_eleve*($current_eleve_note[$nb]-10);
							}
		
							if($current_classe_matiere_moyenne[$nb]>0) {
								$total_points_classe[$nb] += $current_coef*($current_classe_matiere_moyenne[$nb]-10);
							}
						}
						else {
							echo "<p>ANOMALIE&nbsp;: \$mode_moy='$mode_moy' mode inconnu pour ".$current_group['name']."</p>\n";
						}
		
						//===========================
		
						//if($affiche_categories=='1'){
						if(($affiche_categories=='1')||($affiche_categories==true)){
							$total_cat_classe[$nb][$prev_cat_id] += $current_coef*$current_classe_matiere_moyenne[$nb];
		
							//===========================
							// MODIF: boireaus 20070627
							//$total_cat_eleve[$nb][$prev_cat_id] += $current_coef*$current_eleve_note[$nb];
							$total_cat_eleve[$nb][$prev_cat_id] += $coef_eleve*$current_eleve_note[$nb];
							//$total_cat_coef[$nb][$prev_cat_id] += $current_coef;
							$total_cat_coef_eleve[$nb][$prev_cat_id] += $coef_eleve;
							$total_cat_coef_classe[$nb][$prev_cat_id] += $current_coef;
							//===========================
						}
					}
					$nb++;
				}
			}
			*/
		}
		//$j++;
	//  echo "</table>";
	}

	//echo "<table width=$larg_tab border=1 cellspacing=1 cellpadding=1>";
	// On attaque maintenant l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en fin de bulletin :
	$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_config WHERE order_display1 = 'e' ORDER BY order_display2");
	$nb_aid = mysqli_num_rows($call_data);
	$z=0;
	while ($z < $nb_aid) {
		$display_begin = old_mysql_result($call_data, $z, "display_begin");
		$display_end = old_mysql_result($call_data, $z, "display_end");
		if (($periode1 >= $display_begin) and ($periode2 <= $display_end)) {
			$indice_aid = @old_mysql_result($call_data, $z, "indice_aid");
			$aid_query = mysqli_query($GLOBALS["mysqli"], "SELECT id_aid FROM j_aid_eleves WHERE (login='$current_eleve_login' and indice_aid='$indice_aid')");
			$aid_id = @old_mysql_result($aid_query, 0, "id_aid");
			if ($aid_id != '') {
				affiche_aid_simple($affiche_rang, $test_coef,$indice_aid,$aid_id,$current_eleve_login,$periode1,$periode2,$id_classe, 'bull_simpl', $affiche_coef);
			}
		}
		$z++;
	}
	//echo "</table>";
	
	//echo "<table width=$larg_tab border=1 cellspacing=1 cellpadding=1>";

	//====================================================================
	//====================================================================
	//====================================================================

	echo "</tbody>\n";

	// Affichage des moyennes générales
	echo "<tfoot>\n";
	if($display_moy_gen=="y") {
		if ($test_coef != 0) {
			echo "<tr>\n<td";
			if ($nb_periodes > 1) echo " rowspan=".$nb_periodes;
			echo ">\n<p class='bull_simpl'><b>Moyenne générale</b></p>\n</td>\n";
			//====================
			// Modif: boireaus 20070626
			if($affiche_coef=='y'){
				echo "<td";
				if ($nb_periodes > 1) echo " rowspan=".$nb_periodes;
				echo " align=\"center\" style='$style_bordure_cell'>-</td>\n";
			}
			//====================
	
			$nb=$periode1;
			$print_tr = 'no';
			while ($nb < $periode2+1) {
				//=============================
				//if($nb==$periode1){echo "<tr>\n";}
				if($print_tr=='yes'){echo "<tr style='border-width: 5px;'>\n";}
				//=============================
	
				//=========================
				// AJOUT: boireaus 20080315
				if($nb==$periode1) {
					if($nb==$periode2) {
						$style_bordure_cell="border: 1px solid black";
					}
					else {
						$style_bordure_cell="border: 1px solid black; border-bottom: 1px dashed black";
					}
				}
				elseif($nb==$periode2) {
					$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black;";
				}
				else {
					$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black; border-bottom: 1px dashed black;";
				}
				//=========================
				if($affiche_colonne_moy_classe!='n') {
					echo "<td class='bull_simpl' align=\"center\" style='$style_bordure_cell'>\n";
					/*
					//echo "\$total_points_classe[$nb]=$total_points_classe[$nb]<br />\n";
					//echo "\$tab_moy_gen[$nb]=$tab_moy_gen[$nb]<br />\n";
					//if ($total_points_classe[$nb] != 0) {
					if(($total_points_classe[$nb]!=0)||(isset($tab_moy_gen[$nb]))) {
						//$moy_classe=number_format($total_points_classe[$nb]/$total_coef[$nb],1, ',', ' ');
	
						//=========================
						// MODIF: boireaus 20080316
						//$moy_classe=number_format($total_points_classe[$nb]/$total_coef_classe[$nb],1, ',', ' ');
						//$moy_classe=number_format($tab_moy_gen[$nb],1, ',', ' ');
						$moy_classe=$tab_moy_gen[$nb];
						//=========================
					} else {
						$moy_classe = '-';
					}
					//echo "$moy_classe";
					echo nf($moy_classe);
					*/

					echo nf($tab_moy['periodes'][$nb]['moy_generale_classe'],2);
					if ($affiche_deux_moy_gen==1) {
						echo "<br />\n";
						$moy_classe1=$tab_moy['periodes'][$nb]['moy_generale_classe1'];
						echo "<i>".nf($moy_classe1,2)."</i>\n";
					}
					echo "</td>\n";
				}

				echo "<td class='bull_simpl' align=\"center\" style='$style_bordure_cell'>\n";
				/*
				if ($total_points_eleve[$nb] != '0') {
					//$moy_eleve=number_format($total_points_eleve[$nb]/$total_coef[$nb],1, ',', ' ');
					$moy_eleve=number_format($total_points_eleve[$nb]/$total_coef_eleve[$nb],1, ',', ' ');
				} else {
					$moy_eleve = '-';
				}
				*/
				if(isset($tab_login_indice[$nb])) {
					$moy_eleve=$tab_moy['periodes'][$nb]['moy_gen_eleve'][$tab_login_indice[$nb]];
					echo "<b>".nf($moy_eleve,2)."</b>\n";

					if ($affiche_deux_moy_gen==1) {
						echo "<br />\n";
						$moy_eleve1=$tab_moy['periodes'][$nb]['moy_gen_eleve1'][$tab_login_indice[$nb]];
						echo "<i><b>".nf($moy_eleve1,2)."</b></i>\n";
					}
				}
				else {
					echo "-\n";
				}
				echo "</td>\n";

				if ($affiche_rang == 'y')  {
					$rang = sql_query1("select rang from j_eleves_classes where (
					periode = '".$nb."' and
					id_classe = '".$id_classe."' and
					login = '".$current_eleve_login."' )
					");

					$nombre_eleves=count($tab_moy['periodes'][$nb]['current_eleve_login']);
					if (($rang == 0) or ($rang == -1)) {$rang = "-";} else  {$rang .="/".$nombre_eleves;}
						echo "<td class='bull_simpl' align=\"center\" style='$style_bordure_cell'>".$rang."</td>\n";
				}

				if ($affiche_categories) {
					echo "<td class='bull_simpl' style='$style_bordure_cell; text-align:left;'>\n";
					foreach($categories as $cat_id) {

						if($affiche_moy_cat!="n") {
							// MODIF: boireaus 20070627 ajout du test et utilisation de $total_cat_coef_eleve, $total_cat_coef_classe
							// Tester si cette catégorie doit avoir sa moyenne affichée
							$affiche_cat_moyenne_query = mysqli_query($GLOBALS["mysqli"], "SELECT affiche_moyenne FROM j_matieres_categories_classes WHERE (classe_id = '".$id_classe."' and categorie_id = '".$cat_id."')");
							if (mysqli_num_rows($affiche_cat_moyenne_query) == "0") {
								$affiche_cat_moyenne = false;
							} else {
								$affiche_cat_moyenne = old_mysql_result($affiche_cat_moyenne_query, 0);
							}
	
							if($affiche_cat_moyenne){
								// Si l'élève est bien dans la classe sur la période $nb
								if(isset($tab_login_indice[$nb])) {
									$moy_eleve=$tab_moy['periodes'][$nb]['moy_cat_eleve'][$tab_login_indice[$nb]][$cat_id];
									$moy_classe=$tab_moy['periodes'][$nb]['moy_cat_classe'][$tab_login_indice[$nb]][$cat_id];
	
									echo $cat_names[$cat_id] . " - <b>".nf($moy_eleve,2)."</b> (classe : " . nf($moy_classe,2) . ")<br/>\n";
								}
							}
						}
					}
					echo "</td>\n</tr>\n";
				} else {
					echo "<td class='bull_simpl' style='text-align:left; $style_bordure_cell'>-</td>\n</tr>\n";
				}
				$nb++;
				$print_tr = 'yes';
			}
		}
	}
	elseif(($affiche_categories)&&($affiche_moy_cat!="n")) {
		echo "<tr>\n<td";
		if ($nb_periodes > 1) echo " rowspan=".$nb_periodes;
		echo ">\n<p class='bull_simpl'><b>Moyennes de catégories</b></p>\n</td>\n";
		//====================
		// Modif: boireaus 20070626
		if($affiche_coef=='y'){
			echo "<td";
			if ($nb_periodes > 1) echo " rowspan=".$nb_periodes;
			echo " align=\"center\" style='$style_bordure_cell'>-</td>\n";
		}
		//====================

		$nb=$periode1;
		$print_tr = 'no';
		while ($nb < $periode2+1) {
			//=============================
			//if($nb==$periode1){echo "<tr>\n";}
			if($print_tr=='yes'){echo "<tr style='border-width: 5px;'>\n";}
			//=============================

			//=========================
			// AJOUT: boireaus 20080315
			if($nb==$periode1) {
				if($nb==$periode2) {
					$style_bordure_cell="border: 1px solid black";
				}
				else {
					$style_bordure_cell="border: 1px solid black; border-bottom: 1px dashed black";
				}
			}
			elseif($nb==$periode2) {
				$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black;";
			}
			else {
				$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black; border-bottom: 1px dashed black;";
			}
			//=========================
			if($affiche_colonne_moy_classe!='n') {
				echo "<td class='bull_simpl' align=\"center\" style='$style_bordure_cell'>\n";
				echo "-";
				/*
				echo nf($tab_moy['periodes'][$nb]['moy_generale_classe'],2);
				if ($affiche_deux_moy_gen==1) {
					echo "<br />\n";
					$moy_classe1=$tab_moy['periodes'][$nb]['moy_generale_classe1'];
					echo "<i>".nf($moy_classe1,2)."</i>\n";
				}
				*/
				echo "</td>\n";
			}

			echo "<td class='bull_simpl' align=\"center\" style='$style_bordure_cell'>\n";
			echo "-";
			/*
			if(isset($tab_login_indice[$nb])) {
				$moy_eleve=$tab_moy['periodes'][$nb]['moy_gen_eleve'][$tab_login_indice[$nb]];
				echo "<b>".nf($moy_eleve,2)."</b>\n";

				if ($affiche_deux_moy_gen==1) {
					echo "<br />\n";
					$moy_eleve1=$tab_moy['periodes'][$nb]['moy_gen_eleve1'][$tab_login_indice[$nb]];
					echo "<i><b>".nf($moy_eleve1,2)."</b></i>\n";
				}
			}
			else {
				echo "-\n";
			}
			*/
			echo "</td>\n";

			if ($affiche_rang == 'y')  {
				$rang = sql_query1("select rang from j_eleves_classes where (
				periode = '".$nb."' and
				id_classe = '".$id_classe."' and
				login = '".$current_eleve_login."' )
				");

				$nombre_eleves=count($tab_moy['periodes'][$nb]['current_eleve_login']);
				if (($rang == 0) or ($rang == -1)) {$rang = "-";} else  {$rang .="/".$nombre_eleves;}
					echo "<td class='bull_simpl' align=\"center\" style='$style_bordure_cell'>".$rang."</td>\n";
			}

			if ($affiche_categories) {
				echo "<td class='bull_simpl' style='$style_bordure_cell; text-align:left;'>\n";
				foreach($categories as $cat_id) {

					// MODIF: boireaus 20070627 ajout du test et utilisation de $total_cat_coef_eleve, $total_cat_coef_classe
					// Tester si cette catégorie doit avoir sa moyenne affichée
					$affiche_cat_moyenne_query = mysqli_query($GLOBALS["mysqli"], "SELECT affiche_moyenne FROM j_matieres_categories_classes WHERE (classe_id = '".$id_classe."' and categorie_id = '".$cat_id."')");
					if (mysqli_num_rows($affiche_cat_moyenne_query) == "0") {
						$affiche_cat_moyenne = false;
					} else {
						$affiche_cat_moyenne = old_mysql_result($affiche_cat_moyenne_query, 0);
					}

					if($affiche_cat_moyenne) {

						// Si l'élève est bien dans la classe sur la période $nb
						if(isset($tab_login_indice[$nb])) {
							$moy_eleve=$tab_moy['periodes'][$nb]['moy_cat_eleve'][$tab_login_indice[$nb]][$cat_id];
							$moy_classe=$tab_moy['periodes'][$nb]['moy_cat_classe'][$tab_login_indice[$nb]][$cat_id];

							echo $cat_names[$cat_id] . " - <b>".nf($moy_eleve,2)."</b> (classe : " . nf($moy_classe,2) . ")<br/>\n";
						}
					}
				}
				echo "</td>\n</tr>\n";
			} else {
				echo "<td class='bull_simpl' style='text-align:left; $style_bordure_cell'>-</td>\n</tr>\n";
			}
			$nb++;
			$print_tr = 'yes';
		}
	}
	echo "</tfoot>\n";
	echo "</table>\n";
	
	// Les absences
	// On ne les affiche que si dans le bulletin HTML, on affiche les absences
	if(getSettingAOui('bull_affiche_absences')) {
		echo "<span class='bull_simpl'><b>Absences et retards:</b></span>\n";
		//echo "<table width=$larg_tab border=1 cellspacing=1 cellpadding=1>\n";
		echo "<table width='$larg_tab' style='background-color:white' class='boireaus' cellspacing='1' cellpadding='1' summary='Absences et retards'>\n";
		$nb=$periode1;
		while ($nb < $periode2+1) {
			// Initialisation:
			$eleve_abs[$nb]='';
			$eleve_abs_nj[$nb]='';
			$eleve_abs_j[$nb]="";
			$eleve_retards[$nb]="";
			$current_eleve_appreciation_absences="";

			//On vérifie si le module est activé
			if (getSettingValue("active_module_absence")!='2' || getSettingValue("abs2_import_manuel_bulletin")=='y') {
			    $current_eleve_absences_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM absences WHERE (login='$current_eleve_login' AND periode='$nb')");
			    $eleve_abs[$nb] = @old_mysql_result($current_eleve_absences_query, 0, "nb_absences");
			    $eleve_abs_nj[$nb] = @old_mysql_result($current_eleve_absences_query, 0, "non_justifie");
			    $eleve_retards[$nb] = @old_mysql_result($current_eleve_absences_query, 0, "nb_retards");
			    $current_eleve_appreciation_absences = @old_mysql_result($current_eleve_absences_query, 0, "appreciation");
			    $eleve_app_abs[$nb] = @old_mysql_result($current_eleve_absences_query, 0, "appreciation");
			} else {
			    // Initialisations files
			    require_once("../lib/initialisationsPropel.inc.php");
			    $eleve = EleveQuery::create()->findOneByLogin($current_eleve_login);
			    if ($eleve != null) {
				$current_eleve_absences_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM absences WHERE (login='$current_eleve_login' AND periode='$nb')");
				$eleve_abs[$nb] = $eleve->getDemiJourneesAbsenceParPeriode($nb)->count();
				$eleve_abs_nj[$nb] = $eleve->getDemiJourneesNonJustifieesAbsenceParPeriode($nb)->count();
				$eleve_retards[$nb] = $eleve->getRetardsParPeriode($nb)->count();
				$current_eleve_appreciation_absences = @old_mysql_result($current_eleve_absences_query, 0, "appreciation");
				$eleve_app_abs[$nb] = @old_mysql_result($current_eleve_absences_query, 0, "appreciation");
			    }
			}
			if (($eleve_abs[$nb] !== '') and ($eleve_abs_nj[$nb] !== '')) {
				$eleve_abs_j[$nb] = $eleve_abs[$nb]-$eleve_abs_nj[$nb];
			} else {
				$eleve_abs_j[$nb] = "?";
			}
			if ($eleve_abs_nj[$nb] === '') { $eleve_abs_nj[$nb] = "?"; }
			if ($eleve_retards[$nb] === '') { $eleve_retards[$nb] = "?";}
	
			//====================================
			// AJOUT: boireaus 20080317
			if($nb==$periode1) {
				if($nb==$periode2) {
					$style_bordure_cell="border: 1px solid black";
				}
				else {
					$style_bordure_cell="border: 1px solid black; border-bottom: 1px dashed black";
				}
			}
			elseif($nb==$periode2) {
				$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black;";
			}
			else {
				$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black; border-bottom: 1px dashed black;";
			}
			//====================================

			$nb_colspan_abs=0;
			if(getSettingValue('bull_affiche_abs_tot')=='y') {$nb_colspan_abs++;}
			if(getSettingValue('bull_affiche_abs_nj')=='y') {$nb_colspan_abs++;}
			if(getSettingValue('bull_affiche_abs_ret')=='y') {$nb_colspan_abs++;}

			echo "<tr>\n<td valign=top class='bull_simpl' style='$style_bordure_cell'>$nom_periode[$nb]</td>\n";
			// Test pour savoir si l'élève appartient à la classe pour la période considérée
			$test_eleve_app = sql_query1("select count(login) from j_eleves_classes where login='".$current_eleve_login."' and id_classe='".$id_classe."' and periode='".$nb."'");
			if((getSettingValue('bull_affiche_abs_tot')=='y')||(getSettingValue('bull_affiche_abs_nj')=='y')||(getSettingValue('bull_affiche_abs_ret')=='y')) {
				if ($test_eleve_app != 0) {

					// 20130215
					if(getSettingValue('bull_affiche_abs_tot')=='y') {
						if(getSettingValue('bull_affiche_abs_nj')=='y') {
							echo "<td valign=top class='bull_simpl' style='$style_bordure_cell'>\n";
							if ($eleve_abs_j[$nb] == "1") {
								echo "Absences justifiées : une demi-journée";
							} else if ($eleve_abs_j[$nb] != "0") {
								echo "Absences justifiées : $eleve_abs_j[$nb] demi-journées";
							} else {
								echo "Aucune absence justifiée";
							}
							echo "</td>\n";

							echo "<td valign=top class='bull_simpl' style='$style_bordure_cell'>\n";
							if ($eleve_abs_nj[$nb] == '1') {
								echo "Absences non justifiées : une demi-journée";
							} else if ($eleve_abs_nj[$nb] != '0') {
								echo "Absences non justifiées : $eleve_abs_nj[$nb] demi-journées";
							} else {
								echo "Aucune absence non justifiée";
							}
							echo "</td>\n";
						}
						else {
							echo "<td valign=top class='bull_simpl' style='$style_bordure_cell'>\n";
							if ($eleve_abs[$nb]>0) {
								echo "Nombre de demi-journées : ".$eleve_abs[$nb];
							} else {
								echo "Aucune absence";
							}
							echo "</td>\n";
						}
					}
					elseif(getSettingValue('bull_affiche_abs_nj')=='y') {
						echo "<td valign=top class='bull_simpl' style='$style_bordure_cell'>\n";
						if ($eleve_abs_nj[$nb] == "1") {
							echo "Absences non-justifiées : une demi-journée";
						} else if ($eleve_abs_nj[$nb] != "0") {
							echo "Absences non-justifiées : $eleve_abs_nj[$nb] demi-journées";
						} else {
							echo "Aucune absence non-justifiée";
						}
						echo "</td>\n";
					}

					if(getSettingValue('bull_affiche_abs_ret')=='y') {
						echo "<td valign=top class='bull_simpl' style='$style_bordure_cell'>Nb. de retards : $eleve_retards[$nb]</td>\n";
					}
					echo "</tr>\n";
				} else {
					if(getSettingValue('bull_affiche_abs_tot')=='y') {
						echo "<td valign=top class='bull_simpl' style='$style_bordure_cell'>-</td>\n";
					}
					if(getSettingValue('bull_affiche_abs_nj')=='y') {
						echo "<td valign=top class='bull_simpl' style='$style_bordure_cell'>-</td>\n";
					}
					if(getSettingValue('bull_affiche_abs_ret')=='y') {
						echo "<td valign=top class='bull_simpl' style='$style_bordure_cell'>-</td>\n";
					}
					echo "</tr>\n";
				}
			}
			else {
				if($nb_colspan_abs>0) {
					echo "<td colspan='$nb_colspan_abs' valign=top class='bull_simpl' style='$style_bordure_cell'>-</td>\n";
				}
				else {
					echo "<td valign=top class='bull_simpl' style='$style_bordure_cell'>-</td>\n";
				}
				echo "</tr>\n";
			}

			//Ajout Eric
			if ($current_eleve_appreciation_absences != "") {
				if ($test_eleve_app != 0) {
					echo "<tr>\n";
					echo "<td valign=top class='bull_simpl' style='$style_bordure_cell'>&nbsp;</td>\n";
					if($nb_colspan_abs>0) {
						echo "<td valign=top class='bull_simpl' colspan=\"$nb_colspan_abs\" style='text-align:left; $style_bordure_cell'>";
					}
					else {
						echo "<td valign=top class='bull_simpl' style='text-align:left; $style_bordure_cell'>";
					}
					echo " Observation(s) : $current_eleve_appreciation_absences</td>\n</tr>\n";
				} else {
					echo "<tr><td valign=top class='bull_simpl' style='$style_bordure_cell'>&nbsp;</td><td valign=top class='bull_simpl' colspan=\"3\" style='$style_bordure_cell'>-</td>\n</tr>\n";
				}
			}
			$nb++;
		}
		echo "</table>\n";
	}

	$tab_classe_periode=get_infos_classe_periode($id_classe);

	// Maintenant, on met l'avis du conseil de classe :
	
	echo "<span class='bull_simpl'><b>Avis du conseil de classe </b> ";
	if ($current_eleve_profsuivi_login) {
		$gepi_prof_suivi=ucfirst(retourne_denomination_pp($id_classe));
		echo "<b>(".ucfirst($gepi_prof_suivi)." : <i>".affiche_utilisateur($current_eleve_profsuivi_login,$id_classe)."</i>)</b>";
	}
	echo " :</span>\n";
	$larg_col1b = $larg_tab - $larg_col1 ;
	echo "<table width=\"$larg_tab\" style='background-color:white' class='boireaus' cellspacing='1' cellpadding='1' summary='Avis du conseil de classe'>\n";
	$nb=$periode1;
	while ($nb < $periode2+1) {
	
		//=========================
		if($nb==$periode1) {
			if($nb==$periode2) {
				$style_bordure_cell="border: 1px solid black";
			}
			else {
				$style_bordure_cell="border: 1px solid black; border-bottom: 1px dashed black";
			}
		}
		elseif($nb==$periode2) {
			$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black;";
		}
		else {
			$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black; border-bottom: 1px dashed black;";
		}
		//=========================
	
		$current_eleve_avis_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM avis_conseil_classe WHERE (login='$current_eleve_login' AND periode='$nb')");
		$current_eleve_avis[$nb] = @old_mysql_result($current_eleve_avis_query, 0, "avis");

		// **** AJOUT POUR LA MENTION ****
		$current_eleve_mention[$nb] = @old_mysql_result($current_eleve_avis_query, 0, "id_mention");
		// **** FIN D'AJOUT POUR LA MENTION ****

		// Test pour savoir si l'élève appartient à la classe pour la période considérée
		$test_eleve_app = sql_query1("select count(login) from j_eleves_classes where login='".$current_eleve_login."' and id_classe='".$id_classe."' and periode='".$nb."'");
		if (($current_eleve_avis[$nb]== '') or ($tab_acces_app[$nb]!="y") or ($test_eleve_app == 0)) {$current_eleve_avis[$nb] = ' -';}
		
		echo "<tr>\n<td valign=\"top\" width =\"$larg_col1\" class='bull_simpl' style='text-align:left; $style_bordure_cell'>$nom_periode[$nb]</td>\n";
		
		echo "<td valign=\"top\"  width = \"$larg_col1b\" class='bull_simpl' style='text-align:left; $style_bordure_cell' title=\"Avis du conseil de classe en période n°$nb pour ".$current_eleve_prenom." ".$current_eleve_nom."\">";

		if(($temoin_page_courante!="saisie_avis2")&&($tab_classe_periode[$nb]['verouiller']!="O")) {
			if(($_SESSION['statut']=='scolarite')||
			(($_SESSION['statut']=='professeur')&&(is_pp($_SESSION['login'], $id_classe)))) {
				echo "<div style='float:right; width:16px;' title=\"Saisir/Modifier l'avis du conseil de classe.\" class='noprint'><a href = '../saisie/saisie_avis2.php?periode_num=$nb&amp;id_classe=$id_classe&amp;fiche=y&amp;current_eleve_login=$current_eleve_login#app' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/edit16.png' class='icone16' alt='Editer' /></a></div>";
				//&amp;ind_eleve_login_suiv=$ind_eleve_login_suiv
			}
		}

		echo $current_eleve_avis[$nb];

		// Ajouter par la suite une option pour faire apparaître les mentions même si c'est "-"
		//if(($current_eleve_mention[$nb]=="F")||($current_eleve_mention[$nb]=="M")||($current_eleve_mention[$nb]=="E")) {
		$afficher_les_mentions="y";
		if (($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
			if($tab_acces_app[$nb]!="y") {
				$afficher_les_mentions="n";
			}
		}

		if($afficher_les_mentions=="y") {
			if((!isset($tableau_des_mentions_sur_le_bulletin))||(!is_array($tableau_des_mentions_sur_le_bulletin))||(count($tableau_des_mentions_sur_le_bulletin)==0)) {
				$tableau_des_mentions_sur_le_bulletin=get_mentions();
			}
	
			if(isset($tableau_des_mentions_sur_le_bulletin[$current_eleve_mention[$nb]])) {
				echo "<br />\n";
				echo "<br />\n";
				echo "<b>".$gepi_denom_mention." : </b>";
				echo $tableau_des_mentions_sur_le_bulletin[$current_eleve_mention[$nb]];
				//else {echo "-";}
			}
		}

		echo "</td>\n";
		echo "</tr>\n";
		$nb++;
	}
	echo "</table>\n";

	} // fin de la condition if ($on_continue == 'yes')
} // Fin de la fonction

function affiche_aid_simple($affiche_rang, $test_coef, $indice_aid, $aid_id, $current_eleve_login, $periode1, $periode2, $id_classe, $style_bulletin, $affiche_coef) {

global $affiche_colonne_moy_classe;
global $couleur_lignes, $alt, $alt2;

unset($tab_acces_app);
$tab_acces_app=array();
//$tab_acces_app = acces_appreciations($periode1, $periode2, $id_classe);
$tab_acces_app = acces_appreciations($periode1, $periode2, $id_classe, "", $current_eleve_login);

	$nb_periodes = $periode2 - $periode1 + 1;
	$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
	$AID_NOM = @old_mysql_result($call_data, 0, "nom");
	$note_max = @old_mysql_result($call_data, 0, "note_max");
	$type_note = @old_mysql_result($call_data, 0, "type_note");
	$display_begin = @old_mysql_result($call_data, 0, "display_begin");
	$display_end = @old_mysql_result($call_data, 0, "display_end");
	$bull_simplifie = @old_mysql_result($call_data, 0, "bull_simplifie");
	// On vérifie que cette AID soit autorisée à l'affichage dans le bulletin simplifié
	if ($bull_simplifie == "n") {
		return "";
	}

	$aid_nom_query = mysqli_query($GLOBALS["mysqli"], "SELECT nom FROM aid WHERE (id='$aid_id' and indice_aid='$indice_aid')");
	$aid_nom = @old_mysql_result($aid_nom_query, 0, "nom");
	//------
	// On regarde maintenant quels sont les profs responsables de cette AID
	$aid_prof_resp_query = mysqli_query($GLOBALS["mysqli"], "SELECT id_utilisateur FROM j_aid_utilisateurs WHERE (id_aid='$aid_id' and indice_aid='$indice_aid')");
	$nb_lig = mysqli_num_rows($aid_prof_resp_query);
	$n = '0';
	while ($n < $nb_lig) {
		$aid_prof_resp_login[$n] = old_mysql_result($aid_prof_resp_query, $n, "id_utilisateur");
		$n++;
	}
	//------
	// On appelle l'appréciation de l'élève, et sa note le cas échéant
	//------
	$nb=$periode1;
	while($nb < $periode2+1) {
		//=========================
		// AJOUT: boireaus 20080317
		if($nb==$periode1) {
			if($nb==$periode2) {
				$style_bordure_cell="border: 1px solid black";
			}
			else {
				$style_bordure_cell="border: 1px solid black; border-bottom: 1px dashed black";
			}
		}
		elseif($nb==$periode2) {
			$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black;";
		}
		else {
			$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black; border-bottom: 1px dashed black;";
		}
		//=========================

		$current_eleve_aid_appreciation_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_appreciations WHERE (login='$current_eleve_login' AND periode='$nb' and id_aid='$aid_id' and indice_aid='$indice_aid')");
		$eleve_aid_app[$nb] = @old_mysql_result($current_eleve_aid_appreciation_query, 0, "appreciation");
		if ($eleve_aid_app[$nb] == '') {$eleve_aid_app[$nb] = ' -';}
		$periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$id_classe'");
		$periode_max = mysqli_num_rows($periode_query);
		$last_periode_aid = min($periode_max,$display_end);
		if (($type_note == 'every') or (($type_note == 'last') and ($nb == $last_periode_aid))) {
			$current_eleve_aid_note[$nb] = @old_mysql_result($current_eleve_aid_appreciation_query, 0, "note");
			$current_eleve_aid_statut[$nb] = @old_mysql_result($current_eleve_aid_appreciation_query, 0, "statut");
			if ($note_max != 20) {
				$eleve_aid_app[$nb] = "(note sur ".$note_max.") ".$eleve_aid_app[$nb];
			}
			if ($current_eleve_aid_note[$nb] != '') $current_eleve_aid_note[$nb]=number_format($current_eleve_aid_note[$nb],1, ',', ' ');
			$aid_note_min_query = mysqli_query($GLOBALS["mysqli"], "SELECT MIN(note) note_min FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$nb' and j.periode='$nb' and a.indice_aid='$indice_aid')");

			$aid_note_min[$nb] = @old_mysql_result($aid_note_min_query, 0, "note_min");
			if ($aid_note_min[$nb] == '') {$aid_note_min[$nb] = '-';}
			$aid_note_max_query = mysqli_query($GLOBALS["mysqli"], "SELECT MAX(note) note_max FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$nb' and j.periode='$nb' and a.indice_aid='$indice_aid')");

			$aid_note_max[$nb] = @old_mysql_result($aid_note_max_query, 0, "note_max");
			if ($aid_note_max[$nb] == '') {$aid_note_max[$nb] = '-';}

			$aid_note_moyenne_query = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(note),1) moyenne FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$nb' and j.periode='$nb' and a.indice_aid='$indice_aid')");

			$aid_note_moyenne[$nb] = @old_mysql_result($aid_note_moyenne_query, 0, "moyenne");
			if ($aid_note_moyenne[$nb] == '') {
				$aid_note_moyenne[$nb] = '-';
			} else {
				$aid_note_moyenne[$nb]=number_format($aid_note_moyenne[$nb],1, ',', ' ');
			}
		} else {
			$current_eleve_aid_statut[$nb] = '-';
			$current_eleve_aid_note[$nb] = '-';
			$aid_note_min[$nb] = '-';
			$aid_note_max[$nb] = '-';
			$aid_note_moyenne[$nb] = '-';
		}
		$nb++;
	}
	//------
	// On affiche l'appréciation aid :
	//------

	if($couleur_lignes=='y') {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		$alt2=$alt;
	}
	else {
		echo "<tr>\n";
	}
	echo "<td";
	if ($nb_periodes > 1) echo " rowspan= ".$nb_periodes;
	echo " class='$style_bulletin' style='$style_bordure_cell'><b>$AID_NOM : $aid_nom</b><br /><i>";
	$n = '0';
	while ($n < $nb_lig) {
		echo affiche_utilisateur($aid_prof_resp_login[$n],$id_classe)."<br />";
		$n++;
	}
	echo "</i></td>";
	if($affiche_coef=='y'){
		if ($test_coef != 0) {
			echo "<td ";
			if ($nb_periodes > 1) echo " rowspan= ".$nb_periodes;
			echo " align=\"center\" style='$style_bordure_cell'><p class='".$style_bulletin."'>-</p></td>";
		}
	}

	$nb=$periode1;
	$print_tr = 'no';
	while ($nb < $periode2+1) {
		if ($print_tr == 'yes') echo "<tr>";
		if($affiche_colonne_moy_classe!='n') {
			echo "<td align=\"center\" class='$style_bulletin' style='$style_bordure_cell'>$aid_note_moyenne[$nb]</td>";
		}
		echo "<td align=\"center\" class='$style_bulletin' style='$style_bordure_cell'><b>";
		// L'élève fait-il partie de la classe pour la période considérée ?
		$test_eleve_app = sql_query1("select count(login) from j_eleves_classes where login='".$current_eleve_login."' and id_classe='".$id_classe."' and periode='".$nb."'");
		if ($test_eleve_app !=0) {
			if ($current_eleve_aid_statut[$nb] == '') {
				if ($current_eleve_aid_note[$nb] != '') {
					echo $current_eleve_aid_note[$nb];
				} else {
					echo "-";
				}
			} else if ($current_eleve_aid_statut[$nb] != 'other') {
				echo "$current_eleve_aid_statut[$nb]";
			} else {
				echo "-";
			}
		} else  echo "-";
		echo "</b></td>";
		if ($affiche_rang == 'y') echo "<td align=\"center\" class='".$style_bulletin."' style='$style_bordure_cell'>-</td>";
		if ($test_eleve_app !=0) {
			if (($eleve_aid_app[$nb]== '') or ($tab_acces_app[$nb]!="y")) {$eleve_aid_app[$nb] = ' -';}
			echo "<td class='$style_bulletin' style='text-align:left; $style_bordure_cell'>$eleve_aid_app[$nb]</td></tr>";
		} else echo "<td class='$style_bulletin' style='$style_bordure_cell'>-</td></tr>";
		$print_tr = 'yes';
		$nb++;
	}


	//------

}

$necessaire_signalement_fautes_insere="n";
function lib_signalement_fautes() {
	global $necessaire_signalement_fautes_insere, $id_classe;
	global $inclusion_depuis_graphes;

	if($necessaire_signalement_fautes_insere=="n") {

		//========================================================
		echo "<div id='div_signaler_faute' style='position: absolute; top: 220px; right: 20px; width: 700px; text-align:center; color: black; padding: 0px; border:1px solid black; display:none;'>\n";
		
			echo "<div class='infobulle_entete' style='color: #ffffff; cursor: move; width: 700px; font-weight: bold; padding: 0px;' onmousedown=\"dragStart(event, 'div_signaler_faute')\">\n";
				echo "<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>\n";
				echo "<a href='#' onClick=\"cacher_div('div_signaler_faute');return false;\">\n";
				echo "<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />\n";
				echo "</a>\n";
				echo "</div>\n";
		
				echo "<div id='titre_entete_signaler_faute'></div>\n";
			echo "</div>\n";
			
			echo "<div id='corps_signaler_faute' class='infobulle_corps' style='color: #ffffff; cursor: auto; font-weight: bold; padding: 0px; height: 15em; width: 700px; overflow: auto;'>";

echo "
<form name='form_signalement_faute' id='form_signalement_faute' action ='../lib/ajax_signaler_faute.php' method='post' target='_blank'>
<input type='hidden' name='signalement_login_eleve' id='signalement_login_eleve' value='' />
<input type='hidden' name='signalement_id_groupe' id='signalement_id_groupe' value='' />
<input type='hidden' name='signalement_id_eleve' id='signalement_id_eleve' value='' />
<input type='hidden' name='signalement_num_periode' id='signalement_num_periode' value='' />
<input type='hidden' name='signalement_id_classe' id='signalement_id_classe' value='$id_classe' />
<input type='hidden' name='suppression_possible' id='signalement_suppression_possible' value='oui' />

<div id='div_signalement_message'></div>
<!--textarea name='signalement_message' id='signalement_message' cols='50' rows='12'></textarea-->

<input type='button' onclick='valider_signalement_faute()' name='Envoyer' value='Envoyer' />\n";
echo add_token_field();
echo "</form>\n";

			echo "</div>\n";
		
		echo "</div>\n";
		//========================================================

		//========================================================
		echo "<script type='text/javascript'>
	// <![CDATA[

	function signaler_une_faute(eleve_login, id_eleve, id_groupe, liste_profs_du_groupe, num_periode) {

		info_eleve=eleve_login;
		if(document.getElementById('nom_prenom_eleve_'+id_eleve)) {
			info_eleve=document.getElementById('nom_prenom_eleve_'+id_eleve).value;
		}

		document.getElementById('titre_entete_signaler_faute').innerHTML='Signaler un problème/faute pour '+info_eleve+' période '+num_periode;

		document.getElementById('signalement_login_eleve').value=eleve_login;
		document.getElementById('signalement_id_groupe').value=id_groupe;

		document.getElementById('signalement_id_eleve').value=id_eleve;
		document.getElementById('signalement_num_periode').value=num_periode;

		info_groupe=''
		if(document.getElementById('signalement_id_groupe_'+id_groupe)) {
			info_groupe=document.getElementById('signalement_id_groupe_'+id_groupe).value;
		}

		message='Bonjour,\\n\\nL\'appréciation de l\'élève '+info_eleve+' sur l\'enseignement n°'+id_groupe+' ('+info_groupe+') en période n°'+num_periode+' présente un problème ou une faute:\\n';
		message=message+'================================\\n';
		// Le champ textarea n'existe que si une appréciation a été enregistrée
		if(document.getElementById('appreciation_'+id_eleve+'_'+id_groupe+'_'+num_periode)) {
			//message=message+addslashes(document.getElementById('appreciation_'+id_eleve+'_'+id_groupe+'_'+num_periode).innerHTML);
			message=message+document.getElementById('appreciation_'+id_eleve+'_'+id_groupe+'_'+num_periode).innerHTML;
		}
		//alert('document.getElementById(\'appreciation_'+id_eleve+'_'+id_groupe+'_'+num_periode+').innerHTML');
		message=message+'\\n================================\\n'
";
		if(getSettingValue('url_racine_gepi')!="") {
			//echo "		message=message+'\\nAprès connexion dans Gepi, l\'adresse pour corriger est ".getSettingValue('url_racine_gepi')."/saisie/saisie_appreciations.php?id_groupe='+id_groupe+'#saisie_app_'+eleve_login;\n";
			echo "		message=message+'\\nAprès connexion dans Gepi, l\'adresse pour corriger est ___URL_PAGE_CORRECTION___';\n";
			echo "		message=message+'\\n';";
		}
		if(getSettingValue('url_racine_gepi_interne')!="") {
			echo "		message=message+'\\nDepuis le réseau de l\'établissement, après connexion dans Gepi, l\'adresse pour corriger est ___URL_PAGE_CORRECTION_INTERNE___';\n";
			echo "		message=message+'\\n';";
		}
		echo "
		message=message+'\\n\\nCordialement\\n-- \\n".casse_mot($_SESSION['prenom'],'majf2')." ".$_SESSION['nom']."';

		//alert('message='+message);

		document.getElementById('div_signalement_message').innerHTML='<textarea name=\'signalement_message\' id=\'signalement_message\' cols=\'50\' rows=\'11\'></textarea>';

		document.getElementById('signalement_message').innerHTML=message;
";

		if((isset($inclusion_depuis_graphes))&&($inclusion_depuis_graphes=='y')) {
			echo "		afficher_div('div_signaler_faute','n',0,0);\n";
		}
		else {
			echo "		afficher_div('div_signaler_faute','y',100,100);\n";
		}

echo "
	}

	function valider_signalement_faute() {
		signalement_id_groupe=document.getElementById('signalement_id_groupe').value;
		signalement_login_eleve=document.getElementById('signalement_login_eleve').value;

		//signalement_message=escape(document.getElementById('signalement_message').value);
		signalement_message=document.getElementById('signalement_message').value;

		//signalement_message=encodeURIComponent(document.getElementById('signalement_message').value);

		signalement_id_eleve=document.getElementById('signalement_id_eleve').value;
		signalement_num_periode=document.getElementById('signalement_num_periode').value;
		signalement_id_classe=document.getElementById('signalement_id_classe').value;

		//alert(signalement_message);

		//new Ajax.Updater($('signalement_effectue_'+signalement_id_eleve+'_'+signalement_id_groupe+'_'+signalement_num_periode),'../lib/ajax_signaler_faute.php?signalement_login_eleve='+signalement_login_eleve+'&signalement_id_groupe='+signalement_id_groupe+'&signalement_id_classe='+signalement_id_classe+'&signalement_num_periode='+signalement_num_periode+'&signalement_message='+signalement_message+'".add_token_in_url(false)."',{method: 'get'});

		document.getElementById('signalement_effectue_'+signalement_id_eleve+'_'+signalement_id_groupe+'_'+signalement_num_periode).innerHTML=\"<img src='../images/spinner.gif' />\";

		new Ajax.Updater($('signalement_effectue_'+signalement_id_eleve+'_'+signalement_id_groupe+'_'+signalement_num_periode),'../lib/ajax_signaler_faute.php?a=a&".add_token_in_url(false)."',{method: 'post',
		parameters: {
			signalement_login_eleve: signalement_login_eleve,
			signalement_id_groupe: signalement_id_groupe,
			signalement_id_classe: signalement_id_classe,
			signalement_num_periode: signalement_num_periode,
			no_anti_inject_signalement_message: signalement_message,
			suppression_possible:'oui'
		}});

		cacher_div('div_signaler_faute');
		//document.getElementById('signalement_message').innerHTML='';

	}
	//]]>
</script>\n";
		//========================================================

	}
}

// 20120409
$necessaire_corriger_appreciation_insere="n";
function lib_corriger_appreciation() {
	global $necessaire_corriger_appreciation_insere, $id_classe;
	global $inclusion_depuis_graphes;

	if($necessaire_corriger_appreciation_insere=="n") {

		//========================================================
		echo "<div id='div_corriger_app' style='position: absolute; top: 220px; right: 20px; width: 700px; text-align:center; color: black; padding: 0px; border:1px solid black; display:none;'>\n";
		
			echo "<div class='infobulle_entete' style='color: #ffffff; cursor: move; width: 700px; font-weight: bold; padding: 0px;' onmousedown=\"dragStart(event, 'div_corriger_app')\">\n";
				echo "<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>\n";
				echo "<a href='#' onClick=\"cacher_div('div_corriger_app');return false;\">\n";
				echo "<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />\n";
				echo "</a>\n";
				echo "</div>\n";
		
				echo "<div id='titre_entete_corriger_app'></div>\n";
			echo "</div>\n";
			
			echo "<div id='corps_corriger_app' class='infobulle_corps' style='color: #ffffff; cursor: auto; font-weight: bold; padding: 0px; height: 15em; width: 700px; overflow: auto;'>";

echo "
<form name='form_corriger_app' id='form_signalement_faute' action ='../lib/ajax_corriger_app.php' method='post' target='_blank'>
<input type='hidden' name='corriger_app_login_eleve' id='corriger_app_login_eleve' value='' />
<input type='hidden' name='corriger_app_id_groupe' id='corriger_app_id_groupe' value='' />
<input type='hidden' name='corriger_app_id_eleve' id='corriger_app_id_eleve' value='' />
<input type='hidden' name='corriger_app_num_periode' id='corriger_app_num_periode' value='' />
<input type='hidden' name='corriger_app_id_classe' id='corriger_app_id_classe' value='$id_classe' />
<input type='hidden' name='corriger_app_mode' id='corriger_app_mode' value='' />

<div id='div_corriger_appreciation'></div>
<!--textarea name='app' id='app' cols='50' rows='12'></textarea-->

<input type='button' onclick='valider_corriger_app()' name='Valider' value='Valider' />\n";
echo add_token_field();
echo "</form>\n";

			echo "</div>\n";
		
		echo "</div>\n";
		//========================================================

		//========================================================
		echo "<script type='text/javascript'>
	// <![CDATA[

	function modifier_une_appreciation(eleve_login, id_eleve, id_groupe, liste_profs_du_groupe, num_periode, mode) {

		info_eleve=eleve_login;
		if(document.getElementById('nom_prenom_eleve_'+id_eleve)) {
			info_eleve=document.getElementById('nom_prenom_eleve_'+id_eleve).value;
		}

		if(mode=='corriger') {
			document.getElementById('titre_entete_corriger_app').innerHTML='Corriger  l appréciation pour '+info_eleve+' période '+num_periode;
		}
		else {
			document.getElementById('titre_entete_corriger_app').innerHTML='Proposer une correction de l appréciation pour '+info_eleve+' période '+num_periode;
		}
		document.getElementById('corriger_app_mode').value=mode;

		document.getElementById('corriger_app_login_eleve').value=eleve_login;
		document.getElementById('corriger_app_id_groupe').value=id_groupe;

		document.getElementById('corriger_app_id_eleve').value=id_eleve;
		document.getElementById('corriger_app_num_periode').value=num_periode;

		info_groupe=''
		if(document.getElementById('corriger_app_id_groupe_'+id_groupe)) {
			info_groupe=document.getElementById('corriger_app_id_groupe_'+id_groupe).value;
		}

		message='';
		// Le champ textarea n'existe que si une appréciation a été enregistrée
		if(document.getElementById('appreciation_'+id_eleve+'_'+id_groupe+'_'+num_periode)) {
			message=message+document.getElementById('appreciation_'+id_eleve+'_'+id_groupe+'_'+num_periode).innerHTML;
		}

		//alert('message='+message);

		document.getElementById('div_corriger_appreciation').innerHTML='<textarea name=\'app\' id=\'app\' cols=\'50\' rows=\'11\'>'+message+'</textarea>';

		//document.getElementById('signalement_message').innerHTML=message;
";

		if((isset($inclusion_depuis_graphes))&&($inclusion_depuis_graphes=='y')) {
			echo "
		afficher_div('div_corriger_app','n',0,0);\n";
		}
		else {
			echo "		afficher_div('div_corriger_app','y',100,100);\n";
		}

echo "
	}

	function valider_corriger_app() {
		corriger_app_id_groupe=document.getElementById('corriger_app_id_groupe').value;
		corriger_app_login_eleve=document.getElementById('corriger_app_login_eleve').value;

		app=document.getElementById('app').value;

		corriger_app_id_eleve=document.getElementById('corriger_app_id_eleve').value;
		corriger_app_num_periode=document.getElementById('corriger_app_num_periode').value;
		corriger_app_id_classe=document.getElementById('corriger_app_id_classe').value;

		corriger_app_mode=document.getElementById('corriger_app_mode').value;

		if(corriger_app_mode=='corriger') {
			new Ajax.Updater($('app_'+corriger_app_id_eleve+'_'+corriger_app_id_groupe+'_'+corriger_app_num_periode),'../lib/ajax_corriger_app.php?a=a&".add_token_in_url(false)."',{method: 'post',
			parameters: {
				corriger_app_login_eleve: corriger_app_login_eleve,
				corriger_app_id_groupe: corriger_app_id_groupe,
				corriger_app_id_classe: corriger_app_id_classe,
				corriger_app_num_periode: corriger_app_num_periode,
				no_anti_inject_app: app,
			}});
		}
		else {
			document.getElementById('proposition_app_'+corriger_app_id_eleve+'_'+corriger_app_id_groupe+'_'+corriger_app_num_periode).innerHTML=\"<img src='../images/spinner.gif' class='icone16' title='Traitement en cours...' alt='En cours...' />\";

			new Ajax.Updater($('proposition_app_'+corriger_app_id_eleve+'_'+corriger_app_id_groupe+'_'+corriger_app_num_periode),'../lib/ajax_corriger_app.php?a=a&".add_token_in_url(false)."',{method: 'post',
			parameters: {
				corriger_app_login_eleve: corriger_app_login_eleve,
				corriger_app_id_groupe: corriger_app_id_groupe,
				corriger_app_id_classe: corriger_app_id_classe,
				corriger_app_num_periode: corriger_app_num_periode,
				no_anti_inject_app: app,
			}});
		}
		cacher_div('div_corriger_app');

	}
	//]]>
</script>\n";
		//========================================================

	}
}

function afficher_liens_modif_app($id_classe, $periode1, $periode2) {
	//global $tab_afficher_liens_modif_app;
	//global $tab_afficher_liens_valider_modif_app;
	global $mysqli;

	$tab_afficher_liens_modif_app=array();
	$tab_afficher_liens_valider_modif_app=array();

	if($_SESSION['statut']=='professeur') {
		$tab_grp_classe_prof=array();
		$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgc.id_classe='$id_classe' AND jgp.login='".$_SESSION['login']."';";
		//echo "$sql<br />";
		$res=mysqli_query($mysqli, $sql);
		if($res->num_rows>0) {
			while($lig=$res->fetch_object()) {
				$tab_grp_classe_prof[]=$lig->id_groupe;
			}
			$res->close();

			$date_courante=time();
		}
	}

	/*
	echo "<pre>";
	print_r($tab_grp_classe_prof);
	echo "</pre>";
	*/

	for($loop_per=$periode1;$loop_per<=$periode2;$loop_per++) {
		//$tab_afficher_liens_modif_app[$loop_per]="n";
		$tab_afficher_liens_valider_modif_app[$loop_per]="n";

		if(($_SESSION['statut']=='professeur')&&(count($tab_grp_classe_prof)>0)) {
			$sql="SELECT verouiller FROM periodes WHERE id_classe='$id_classe' AND num_periode='$loop_per';";
			//echo "$sql<br />";
			$res=mysqli_query($mysqli, $sql);
			$lig=$res->fetch_object();
			if($lig->verouiller=='N') {
				for($loop_grp=0;$loop_grp<count($tab_grp_classe_prof);$loop_grp++) {
					$tab_afficher_liens_modif_app[$tab_grp_classe_prof[$loop_grp]][$loop_per]="yy";
				}
			}
			elseif($lig->verouiller=='P') {
				if(getSettingAOui('autoriser_correction_bulletin')) {
					for($loop_grp=0;$loop_grp<count($tab_grp_classe_prof);$loop_grp++) {
						$tab_afficher_liens_modif_app[$tab_grp_classe_prof[$loop_grp]][$loop_per]="y";
						//echo "\$tab_afficher_liens_modif_app[$tab_grp_classe_prof[$loop_grp]][$loop_per]=".$tab_afficher_liens_modif_app[$tab_grp_classe_prof[$loop_grp]][$loop_per]."<br />";
					}
				}

				// On teste si un droit exceptionnel de modif a été donné.
				for($loop_grp=0;$loop_grp<count($tab_grp_classe_prof);$loop_grp++) {
					$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite, mode FROM matieres_app_delais WHERE id_groupe='".$tab_grp_classe_prof[$loop_grp]."' AND periode='$loop_per';";
					$res_mad=mysqli_query($mysqli, $sql);
					if($res_mad->num_rows>0) {
						$lig_mad=$res_mad->fetch_object();
						$date_limite=$lig_mad->date_limite;
						// 20131204
						//echo "\$date_limite=$date_limite en période $k.<br />";
						//echo "\$date_courante=$date_courante.<br />";

						if($date_courante<$date_limite) {
							$tab_afficher_liens_modif_app[$tab_grp_classe_prof[$loop_grp]][$loop_per]='y';
							if($lig_mad->mode=='acces_complet') {
								$tab_afficher_liens_modif_app[$tab_grp_classe_prof[$loop_grp]][$loop_per]='yy';
							}
						}
					}
				}
			}
			$res->close();
		}
		elseif(in_array($_SESSION['statut'], array('scolarite', 'secours'))) {
			$sql="SELECT verouiller FROM periodes WHERE id_classe='$id_classe' AND num_periode='$loop_per';";
			$res=mysqli_query($mysqli, $sql);
			$lig=$res->fetch_object();
			if($lig->verouiller!='O') {
				$tab_afficher_liens_valider_modif_app[$loop_per]="y";
			}
			$res->close();
		}
		elseif(($_SESSION['statut']=='administrateur')&&(getSettingAOui('GepiAdminValidationCorrectionBulletins'))) {
			$sql="SELECT verouiller FROM periodes WHERE id_classe='$id_classe' AND num_periode='$loop_per';";
			$res=mysqli_query($mysqli, $sql);
			$lig=$res->fetch_object();
			if($lig->verouiller!='O') {
				$tab_afficher_liens_valider_modif_app[$loop_per]="y";
			}
			$res->close();
		}
	}

	return array($tab_afficher_liens_modif_app, $tab_afficher_liens_valider_modif_app);
}


function affiche_lien_proposition_ou_correction_appreciation($current_eleve_login, $current_id_eleve, $current_eleve_prenom, $current_eleve_nom, $current_group, $id_classe, $nb, $liste_profs_du_groupe, $tab_mes_groupes, $tab_afficher_liens_modif_app) {
	//global $tab_mes_groupes;
	//global $tab_afficher_liens_modif_app;

	global $inclusion_depuis_graphes;

	$ajout="";
	if($inclusion_depuis_graphes=="y") {
		$ajout="afficher_div('div_bull_simp','y',-100,-200); affiche_bull_simp('$current_eleve_login','$id_classe','$nb','$nb');";
	}

	$retour="";

	if(($_SESSION['statut']=='professeur')&&(in_array($current_group['id'],$tab_mes_groupes))) {
		if($current_group["classe"]["ver_periode"][$id_classe][$nb]=='N') {
			$retour.="<a href='#' onclick=\"".$ajout."modifier_une_appreciation('$current_eleve_login', '$current_id_eleve', '".$current_group['id']."', '$liste_profs_du_groupe', '$nb', 'corriger') ;return false;\" title=\"Modifier l'appréciation en période $nb pour $current_eleve_prenom $current_eleve_nom.
Si vous vous apercevez que vous avez fait une faute de frappe, ou si vous souhaitez modifier votre appréciation, ce lien est là pour ça.\" class='noprint' target='_blank'><img src='../images/edit16.png' width='16' height='16' /></a> ";
		}
		elseif(isset($tab_afficher_liens_modif_app[$current_group['id']][$nb])) {
			if($tab_afficher_liens_modif_app[$current_group['id']][$nb]=='y') {
				$retour.="<a href='#' onclick=\"".$ajout."modifier_une_appreciation('$current_eleve_login', '$current_id_eleve', '".$current_group['id']."', '$liste_profs_du_groupe', '$nb', 'proposer') ;return false;\" title=\"Proposer une correction de l'appréciation en période $nb pour $current_eleve_prenom $current_eleve_nom.
Si vous vous apercevez que vous avez fait une faute de frappe, ou si vous souhaitez simplement modifier votre appréciation, ce lien est là pour ça.\" class='noprint' target='_blank'><img src='../images/edit16.png' width='16' height='16' /></a> ";
			}
			elseif($tab_afficher_liens_modif_app[$current_group['id']][$nb]=='yy') {
				$retour.="<a href='#' onclick=\"".$ajout."modifier_une_appreciation('$current_eleve_login', '$current_id_eleve', '".$current_group['id']."', '$liste_profs_du_groupe', '$nb', 'corriger') ;return false;\" title=\"Modifier l'appréciation en période $nb pour $current_eleve_prenom $current_eleve_nom.
Si vous vous apercevez que vous avez fait une faute de frappe, ou si vous souhaitez modifier votre appréciation, ce lien est là pour ça.\" class='noprint' target='_blank'><img src='../images/edit16.png' width='16' height='16' /></a> ";
			}
			//echo "plop";
		}
	}
	elseif(($_SESSION['statut']=='professeur')&&
		(((getSettingAOui('GepiAccesPPTousElevesDeLaClasse'))&&(is_pp($_SESSION['login'], $id_classe)))||
		(is_pp($_SESSION['login'], $id_classe, $current_eleve_login)))) {

		if(getSettingAOui('PeutAutoriserPPaCorrigerSesApp')) {
			if(acces_correction_app_pp($current_group['id'])) {
				if($current_group["classe"]["ver_periode"][$id_classe][$nb]!='O') {
					$retour.="<a href='#' onclick=\"".$ajout."modifier_une_appreciation('$current_eleve_login', '$current_id_eleve', '".$current_group['id']."', '$liste_profs_du_groupe', '$nb', 'corriger') ;return false;\" title=\"Modifier l'appréciation en période $nb pour $current_eleve_prenom $current_eleve_nom.
Si vous vous apercevez d'une faute de frappe, ou si vous souhaitez modifier l'appréciation, ce lien est là pour ça.
Le professeur recevra un mail l'informant de la modification.\" class='noprint' target='_blank'><img src='../images/edit16.png' width='16' height='16' /></a> ";
				}
			}
		}

	}
	elseif(($_SESSION['statut']=='scolarite')&&(getSettingAOui('AccesModifAppreciationScol'))) {
		if($current_group["classe"]["ver_periode"][$id_classe][$nb]!='O') {
			$retour.="<a href='#' onclick=\"".$ajout."modifier_une_appreciation('$current_eleve_login', '$current_id_eleve', '".$current_group['id']."', '$liste_profs_du_groupe', '$nb', 'corriger') ;return false;\" title=\"Modifier l'appréciation en période $nb pour $current_eleve_prenom $current_eleve_nom.
Si vous vous apercevez d'une faute de frappe, ou si vous souhaitez modifier l'appréciation, ce lien est là pour ça.
Le professeur recevra un mail l'informant de la modification.\" class='noprint' target='_blank'><img src='../images/edit16.png' width='16' height='16' /></a> ";
		}
	}

	return $retour;
}

?>
