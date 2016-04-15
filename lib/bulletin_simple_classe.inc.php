<?php
/*
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*/

//function bulletin_classe_bis($tab_moy,$total,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$test_coef,$affiche_categories) {
function bulletin_classe($tab_moy,$total,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$test_coef,$affiche_categories,$couleur_lignes=NULL) {
global $nb_notes,$nombre_eleves,$type_etablissement,$type_etablissement2;

global $affiche_colonne_moy_classe;
//$affiche_colonne_moy_classe="n";

// 20121209
global $avec_moy_min_max_classe;

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

//global $avec_rapport_effectif;
$avec_rapport_effectif="y";

$alt=1;

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

//if(($afficher_signalement_faute=='y')||($afficher_proposition_correction=="y")) {
if($afficher_signalement_faute=='y') {
	// A N'INSERER QUE POUR LES COMPTES DE PERSONNELS... de façon à éviter de donner les mails des profs à des élèves

	if((!isset($necessaire_signalement_fautes_insere))||($necessaire_signalement_fautes_insere=="n")) {
		lib_signalement_fautes();
	}
	global $signalement_id_groupe;

	$envoi_mail_actif=getSettingValue('envoi_mail_actif');
}

// données requises :
//- $total : nombre total d'élèves
//- $periode1 : numéro de la première période à afficher
//- $periode2 : numéro de la dernière période à afficher
//- $nom_periode : tableau des noms de période
//- $gepiYear : année
//- $id_classe : identifiant de la classe.

//====================
$affiche_coef=sql_query1("SELECT display_coef FROM classes WHERE id='".$id_classe."'");
//echo "\$affiche_coef=$affiche_coef<br />\n";
//====================

//=========================
// AJOUT: boireaus 20080316
//global $tab_moy_gen;
//global $tab_moy_cat_classe;
global $display_moy_gen;
//=========================

global $affiche_deux_moy_gen;

if($affiche_moy_gen=="n") {
	$display_moy_gen="n";
	$affiche_deux_moy_gen="n";
}

global $bull_simp_larg_tab,
	$bull_simp_larg_col1,
	$bull_simp_larg_col2,
	$bull_simp_larg_col3,
	$bull_simp_larg_col4;

//echo "\$affiche_categories=$affiche_categories<br />";
if(!getSettingValue("bull_intitule_app")){
	$bull_intitule_app="Appréciations/Conseils";
}
else{
	$bull_intitule_app=getSettingValue("bull_intitule_app");
}

$nb_periodes = $periode2 - $periode1 + 1;

//============================
// Liste des profs principaux:
$data_profsuivi = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT u.login FROM utilisateurs u, j_eleves_professeurs j WHERE (j.professeur = u.login AND j.id_classe='$id_classe') ");
$current_profsuivi_login=array();
if(mysqli_num_rows($data_profsuivi)>0){
	while($lig_profsuivi=mysqli_fetch_object($data_profsuivi)) {
		$current_profsuivi_login[] = $lig_profsuivi->login;
	}
}
//============================

unset($tab_acces_app);
$tab_acces_app=array();
$tab_acces_app = acces_appreciations($periode1, $periode2, $id_classe);

$call_classe = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes WHERE id='$id_classe'");
$classe = old_mysql_result($call_classe, 0, "classe");

//-------------------------------
// On affiche l'en-tête : Les données de la classe
//-------------------------------
echo "<span class='bull_simpl'><span class='bold'>Classe de $classe</span>";
echo ", année scolaire $gepiYear<br />\n";

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

//echo "\$test_coef=$test_coef<br />";

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

//echo "<table width=$larg_tab border=1 cellspacing=1 cellpadding=1>\n";
echo "<table width='$larg_tab' class='boireaus' cellspacing='1' cellpadding='1' summary=''>\n";
echo "<tr><td width=\"$larg_col1\" class='bull_simpl'>$total élèves";
echo "</td>\n";

//====================
if($affiche_coef=='y'){
	if ($test_coef != 0) echo "<td width=\"$larg_col2\" align=\"center\"><p class='bull_simpl'>Coef.</p></td>\n";
}
//====================

if($avec_rapport_effectif=="y") {
	echo "<td width=\"$larg_col2\" align=\"center\" class='bull_simpl'>Effectif</td>\n";
}

if($affiche_colonne_moy_classe!='n') {
	echo "<td width=\"$larg_col2\" align=\"center\" class='bull_simpl'>Classe</td>\n";
}
echo "<td width=\"$larg_col5\" class='bull_simpl'>$bull_intitule_app</td>";
if($afficher_signalement_faute=='y') {
	echo "<td class='bull_simpl noprint'>Signaler</td>\n";
}
echo "</tr>\n";

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

$prev_cat_id = null;

//while ($j < $nombre_groupes) {
for($j=0;$j<$nombre_groupes;$j++) {

	$inser_ligne='no';

	// On récupère le groupe depuis $tab_moy
	$current_group=$tab_moy['current_group'][$j];

	$ligne_groupe_visible="y";
	if($_SESSION['statut']=='eleve') {
		$sql="SELECT 1=1 FROM j_eleves_groupes WHERE id_groupe='".$current_group['id']."' AND login='".$_SESSION['login']."';";
		$test_grp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test_grp)==0) {
			$ligne_groupe_visible="n";
		}
	}
	elseif($_SESSION['statut']=='responsable') {
		$sql="SELECT 1=1 FROM j_eleves_groupes WHERE id_groupe='".$current_group['id']."' AND login IN (SELECT e.login FROM eleves e, resp_pers rp, responsables2 r WHERE e.ele_id=r.ele_id AND rp.pers_id=r.pers_id AND (r.resp_legal='1' OR r.resp_legal='2') AND rp.login='".$_SESSION['login']."');";
		//echo "$sql<br />";
		$test_grp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test_grp)==0) {
			$ligne_groupe_visible="n";
		}
	}

	if ($ligne_groupe_visible == 'y') {

		// Coefficient pour le groupe
		$current_coef = $current_group["classes"]["classes"][$id_classe]["coef"];

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


		//echo "\$current_matiere_nom_complet=$current_matiere_nom_complet<br />\n";
		$nb=$periode1;
		while ($nb < $periode2+1) {
			$current_classe_matiere_moyenne[$nb]=$tab_moy['periodes'][$nb]['current_classe_matiere_moyenne'][$j];
			// 20121209
			$moy_min_classe_grp[$nb]=$tab_moy['periodes'][$nb]['moy_min_classe_grp'][$j];
			$moy_max_classe_grp[$nb]=$tab_moy['periodes'][$nb]['moy_max_classe_grp'][$j];

			// On teste si des notes de une ou plusieurs boites du carnet de notes doivent être affichées
			$test_cn = mysqli_query($GLOBALS["mysqli"], "select c.nom_court, c.id from cn_cahier_notes cn, cn_conteneurs c
			where (cn.periode = '$nb' and cn.id_groupe='".$current_group["id"]."' and cn.id_cahier_notes = c.id_racine and c.id_racine!=c.id and c.display_bulletin = 1) ");
			$nb_ligne_cn[$nb] = mysqli_num_rows($test_cn);
			$n = 0;
			while ($n < $nb_ligne_cn[$nb]) {
				$cn_id[$nb][$n] = old_mysql_result($test_cn, $n, 'id');
				$cn_nom[$nb][$n] = old_mysql_result($test_cn, $n, 'nom_court');
				$n++;
			}

			//echo "\$nb=$nb<br />\n";
			$nb++;

		}


		$nb=$periode1;
		while ($nb < $periode2+1) {
			$current_grp_appreciation_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_appreciations_grp WHERE (id_groupe='" . $current_group["id"] . "' AND periode='$nb')");
			$current_grp_appreciation[$nb] = @old_mysql_result($current_grp_appreciation_query, 0, "appreciation");
			//echo "\$current_grp_appreciation[$nb]=$current_grp_appreciation[$nb]<br />\n";
			$nb++;
		}


		if ($affiche_categories) {
		// On regarde si on change de catégorie de matière
			if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat_id) {
				$prev_cat_id = $current_group["classes"]["classes"][$id_classe]["categorie_id"];
				// On est dans une nouvelle catégorie
				// On récupère les infos nécessaires, et on affiche une ligne

				// On détermine le nombre de colonnes pour le colspan
				//$nb_total_cols = 4;
				if($affiche_colonne_moy_classe!='n') {
					$nb_total_cols = 2;
				}
				else {
					$nb_total_cols = 3;
				}
				//====================
				if($affiche_coef=='y'){
					if ($test_coef != 0) $nb_total_cols++;
				}
				//====================
				if($avec_rapport_effectif=='y'){
					$nb_total_cols++;
				}
				//====================

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
				echo "<p style='padding: 0; margin:0; font-size: 10px;'>".$cat_names[$prev_cat_id]."</p></td>\n";
				if($afficher_signalement_faute=='y') {
					echo "<td class='bull_simpl noprint'>-</td>\n";
				}
				echo "</tr>\n";
			}
		}
		//echo "<tr>\n";
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
			echo "<input type='hidden' name='signalement_id_groupe[".$current_group['id']."]' id='signalement_id_groupe_grp_".$current_group['id']."' value=\"".$current_group['name']." (".$current_group['name']." en ".$current_group['classlist_string'].")\" />\n";
		}

		echo "</td>\n";

		//====================
		if($affiche_coef=='y'){
			if ($test_coef != 0) {
				$print_coef= number_format($current_coef,1, ',', ' ');
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


			if($avec_rapport_effectif=="y") {
				//$sql="SELECT 1=1 FROM j_eleves_classes jec,
				$sql="SELECT DISTINCT jeg.login FROM j_eleves_classes jec,
									j_eleves_groupes jeg,
									j_groupes_classes jgc
								WHERE jec.id_classe='$id_classe' AND
										jec.periode='$nb' AND
										jec.periode=jeg.periode AND
										jec.login=jeg.login AND
										jeg.id_groupe=jgc.id_groupe AND
										jeg.id_groupe='".$current_group["id"]."';";
				//$sql0=$sql;
				$res_effectif=mysqli_query($GLOBALS["mysqli"], $sql);
				$effectif_grp_classe=mysqli_num_rows($res_effectif);

				$sql="SELECT 1=1 FROM j_eleves_groupes jeg
								WHERE jeg.periode='$nb' AND
										jeg.id_groupe='".$current_group["id"]."';";
				$res_effectif_tot=mysqli_query($GLOBALS["mysqli"], $sql);
				$effectif_grp_total=mysqli_num_rows($res_effectif_tot);

				echo "<td width=\"$larg_col2\" align=\"center\" class='bull_simpl' style='$style_bordure_cell'>";
				//echo "$sql0<br /><br />";
				//echo "$sql<br /><br />";
				if($effectif_grp_classe==$effectif_grp_total) {
					echo $effectif_grp_classe.'&nbsp;él.';
				}
				else {
					echo "$effectif_grp_classe&nbsp;él. /$effectif_grp_total";
				}
				echo "</td>\n";
			}

			if($affiche_colonne_moy_classe!='n') {
				echo "<td width=\"$larg_col2\" align=\"center\" class='bull_simpl' style='$style_bordure_cell'>\n";
				// 20121209
				//$note=number_format($current_classe_matiere_moyenne[$nb],1, ',', ' ');
				$note=nf($current_classe_matiere_moyenne[$nb]);
				if ($note != "0,0") {
					if($avec_moy_min_max_classe=='y') {
						echo "<span title=\"Moyenne minimale sur l'enseignement\">".nf($moy_min_classe_grp[$nb])."</span> ";
					}
					echo "<span style='font-weight:bold' title=\"Moyenne du groupe sur l'enseignement\">".$note."</span>";
					if($avec_moy_min_max_classe=='y') {
						echo " <span title=\"Moyenne maximale sur l'enseignement\">".nf($moy_max_classe_grp[$nb])."</span>";
					}
				}
				else {echo "-";}
				echo "</td>\n";
			}

			// Affichage des cases appréciations
			echo "<td width=\"$larg_col5\" class='bull_simpl' style='text-align:left; $style_bordure_cell'>\n";

			//if ($current_grp_appreciation[$nb]) {
			if (($current_grp_appreciation[$nb])&&($tab_acces_app[$nb]=="y")) {
				if ($current_grp_appreciation[$nb]=="-1") {
					echo "<span class='noprint'>-</span>\n";
				}
				else{
					if((strstr($current_grp_appreciation[$nb],">"))||(strstr($current_grp_appreciation[$nb],"<"))){
						echo "$current_grp_appreciation[$nb]";
					}
					else{
						echo nl2br($current_grp_appreciation[$nb]);
					}

					echo "<textarea name='appreciation_grp_".$current_group['id']."[$nb]' id='appreciation_grp_".$current_group['id']."_$nb' style='display:none;'>".$current_grp_appreciation[$nb]."</textarea>\n";
				}
				//======================================
			} else {
				echo " -";
			}


			echo "</td>";
			if($afficher_signalement_faute=='y') {
				echo "<td class='bull_simpl noprint'>";

				if($current_group["classe"]["ver_periode"][$id_classe][$nb]=='O') {
					echo "-";
				}
				else {
					//echo affiche_lien_proposition_ou_correction_appreciation($current_eleve_login, $current_id_eleve, $current_eleve_prenom, $current_eleve_nom, $current_group, $id_classe, $nb, $liste_profs_du_groupe, $tab_mes_groupes, $tab_afficher_liens_modif_app);

					// Tester si l'adresse mail du/des profs de l'enseignement est renseignée et si l'envoi de mail est actif.
					// Sinon, on pourrait enregistrer le signalement dans une table actions_signalements pour affichage comme le Panneau d'affichage
					//if($afficher_signalement_faute=='y') {
						echo "<a href=\"mailto:$liste_email_profs_du_groupe?Subject=[Gepi]: Signaler un problème/faute&body=Bonjour,Je pense que vous avez commis une faute de frappe pour l appréciation sur le groupe dans l enseignement n°".$current_group['id'].".Cordialement.-- ".casse_mot($_SESSION['prenom'],'majf2')." ".$_SESSION['nom']."\"";
						if($envoi_mail_actif!='n') {
							//echo " onclick=\"alert('plop');return false;\"";
							echo " onclick=\"signaler_une_faute_classe('".$current_group['id']."', '$liste_profs_du_groupe', '$nb') ;return false;\"";
						}
						echo " title=\"Signaler une faute de frappe, d'orthographe ou autre...
Si vous vous apercevez que ce collègue a fait une erreur,
vous pouvez lui envoyer un mail pour l'alerter.
Ce lien est là pour ça.\" target='_blank'><img src='../images/icons/mail.png' width='16' height='16' alt='Signaler un problème/faute par mail' /></a>";

						echo "<span id='signalement_effectue_groupe_".$current_group['id']."_$nb'></span>";
					//}
				}
				echo "</td>\n";
			}
			echo "</tr>\n";
			$print_tr = 'yes';
			$nb++;
		}
	}
}

// Affichage des moyennes générales
if($display_moy_gen=="y") {
	if ($test_coef != 0) {
		echo "<tr>\n<td";
		if ($nb_periodes > 1) echo " rowspan=".$nb_periodes;
		echo ">\n<p class='bull_simpl'><b>Moyenne générale</b></p>\n</td>\n";
		//====================
		if($affiche_coef=='y'){
			echo "<td";
			if ($nb_periodes > 1) echo " rowspan=".$nb_periodes;
			echo " align=\"center\">-</td>\n";
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

			if($avec_rapport_effectif=="y") {
				$sql="SELECT 1=1 FROM j_eleves_classes WHERE periode='$nb' AND id_classe='$id_classe';";
				$res_eff_classe=mysqli_query($GLOBALS["mysqli"], $sql);

				echo "<td class='bull_simpl' align=\"center\" style='$style_bordure_cell'>\n";
				//echo "$sql<br />";
				echo mysqli_num_rows($res_eff_classe).' él.';
				echo "</td>\n";
			}


			if($affiche_colonne_moy_classe!='n') {
				echo "<td class='bull_simpl' align=\"center\" style='$style_bordure_cell'>\n";
				/*
				if ($total_points_classe[$nb] != 0) {
					//$moy_classe=number_format($total_points_classe[$nb]/$total_coef[$nb],1, ',', ' ');
					//=========================
					// MODIF: boireaus 20080316
					//$moy_classe=number_format($total_points_classe[$nb]/$total_coef_classe[$nb],1, ',', ' ');
					$moy_classe=$tab_moy_gen[$nb];
					//=========================
				} else {
					$moy_classe = '-';
				}
				*/

				// 20121209
				if($avec_moy_min_max_classe=='y') {
					echo "<span title=\"Moyenne générale minimale\">".nf($tab_moy['periodes'][$nb]['moy_min_classe'],2)."</span> ";
				}
				echo "<span style='font-weight:bold' title=\"Moyenne des moyennes générales de la classe\">".nf($tab_moy['periodes'][$nb]['moy_generale_classe'],2)."</span>";
				if($avec_moy_min_max_classe=='y') {
					echo " <span title=\"Moyenne générale maximale\">".nf($tab_moy['periodes'][$nb]['moy_max_classe'],2)."</span>";
				}

				if ($affiche_deux_moy_gen==1) {
					echo "<br />\n";
					echo "<i>";
					/*
					if($avec_moy_min_max_classe=='y') {
						echo "<span title=\"Moyenne générale minimale avec tous les coefficients à 1\">".nf($tab_moy['periodes'][$nb]['moy_min_classe1'],2)."</span> ";
					}
					*/
					echo "<span style='font-weight:bold' title=\"Moyenne des moyennes générales de la classe avec tous les coefficients à 1\">".nf($tab_moy['periodes'][$nb]['moy_generale_classe1'])."</span>";
					/*
					if($avec_moy_min_max_classe=='y') {
						echo " <span title=\"Moyenne générale maximale avec tous les coefficients à 1\">".nf($tab_moy['periodes'][$nb]['moy_max_classe1'],2)."</span>";
					}
					*/
					echo "</i>\n";
				}
				echo "</td>\n";
			}
			/*
			echo "<td class='bull_simpl' align=\"center\">\n";
			if ($total_points_eleve[$nb] != '0') {
				//$moy_eleve=number_format($total_points_eleve[$nb]/$total_coef[$nb],1, ',', ' ');
				$moy_eleve=number_format($total_points_eleve[$nb]/$total_coef_eleve[$nb],1, ',', ' ');
			} else {
				$moy_eleve = '-';
			}
			echo "<b>".$moy_eleve."</b>\n</td>\n";
			if ($affiche_rang == 'y')  {
				$rang = sql_query1("select rang from j_eleves_classes where (
				periode = '".$nb."' and
				id_classe = '".$id_classe."' and
				login = '".$current_eleve_login."' )
				");
				if (($rang == 0) or ($rang == -1)) $rang = "-"; else  $rang .="/".$nombre_eleves;
					echo "<td class='bull_simpl' align=\"center\">".$rang."</td>\n";
			}
			*/
			if ($affiche_categories) {
				echo "<td class='bull_simpl' style='text-align:left; $style_bordure_cell'>\n";
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
							/*
							//if ($total_cat_coef[$nb][$cat_id] != "0") {
							//if ($total_cat_coef_eleve[$nb][$cat_id] != "0") {
								//$moy_eleve=number_format($total_cat_eleve[$nb][$cat_id]/$total_cat_coef[$nb][$cat_id],1, ',', ' ');
								//$moy_classe=number_format($total_cat_classe[$nb][$cat_id]/$total_cat_coef[$nb][$cat_id],1, ',', ' ');
								//$moy_eleve=number_format($total_cat_eleve[$nb][$cat_id]/$total_cat_coef_eleve[$nb][$cat_id],1, ',', ' ');
								//echo "\$total_cat_coef_classe[$nb][$cat_id]=".$total_cat_coef_classe[$nb][$cat_id]."<br />";
								if ($total_cat_coef_classe[$nb][$cat_id] != "0") {
									$moy_classe=number_format($total_cat_classe[$nb][$cat_id]/$total_cat_coef_classe[$nb][$cat_id],1, ',', ' ');
								}
								else{
									$moy_classe="-";
								}

								//echo $cat_names[$cat_id] . " - <b>$moy_eleve</b> (classe : " . $moy_classe . ")<br/>\n";
								echo $cat_names[$cat_id] . " - <b>$moy_classe</b><br />\n";
							//}
							*/
							$moy_classe="-";
							$loop_i=0;
							while($loop_i<count($tab_moy['periodes'][$nb]['current_eleve_login'])) {
								if(isset($tab_moy['periodes'][$nb]['moy_cat_classe'][$loop_i][$cat_id])) {
									$moy_classe=$tab_moy['periodes'][$nb]['moy_cat_classe'][$loop_i][$cat_id];
									break;
								}
								$loop_i++;
							}

							echo $cat_names[$cat_id] . " - <b>".nf($moy_classe,2)."</b><br/>\n";

						}
					}
				}
				echo "</td>\n";
				if($afficher_signalement_faute=='y') {
					echo "<td class='bull_simpl noprint'>-</td>\n";
				}
				echo "</tr>\n";
			} else {
				echo "<td class='bull_simpl' style='text-align:left; $style_bordure_cell'>-</td>\n";
				if($afficher_signalement_faute=='y') {
					echo "<td class='bull_simpl noprint'>-</td>\n";
				}
				echo "</tr>\n";
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
	if($affiche_coef=='y'){
		echo "<td";
		if ($nb_periodes > 1) echo " rowspan=".$nb_periodes;
		echo " align=\"center\">-</td>\n";
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

		if($avec_rapport_effectif=="y") {
			$sql="SELECT 1=1 FROM j_eleves_classes WHERE periode='$nb' AND id_classe='$id_classe';";
			$res_eff_classe=mysqli_query($GLOBALS["mysqli"], $sql);

			echo "<td class='bull_simpl' align=\"center\" style='$style_bordure_cell'>\n";
			//echo "$sql<br />";
			echo mysqli_num_rows($res_eff_classe).' él.';
			echo "</td>\n";
		}


		if($affiche_colonne_moy_classe!='n') {
			echo "<td class='bull_simpl' align=\"center\" style='$style_bordure_cell'>\n";
			echo "-";
			/*
			if($avec_moy_min_max_classe=='y') {
				echo "<span title=\"Moyenne générale minimale\">".nf($tab_moy['periodes'][$nb]['moy_min_classe'],2)."</span> ";
			}
			echo "<span style='font-weight:bold' title=\"Moyenne des moyennes générales de la classe\">".nf($tab_moy['periodes'][$nb]['moy_generale_classe'],2)."</span>";
			if($avec_moy_min_max_classe=='y') {
				echo " <span title=\"Moyenne générale maximale\">".nf($tab_moy['periodes'][$nb]['moy_max_classe'],2)."</span>";
			}

			if ($affiche_deux_moy_gen==1) {
				echo "<br />\n";
				echo "<i>";
				echo "<span style='font-weight:bold' title=\"Moyenne des moyennes générales de la classe avec tous les coefficients à 1\">".nf($tab_moy['periodes'][$nb]['moy_generale_classe1'])."</span>";
				echo "</i>\n";
			}
			*/
			echo "</td>\n";
		}

		//if ($affiche_categories) {
			echo "<td class='bull_simpl' style='text-align:left; $style_bordure_cell'>\n";
			foreach($categories as $cat_id) {

				if($affiche_moy_cat!="n") {
					$affiche_cat_moyenne_query = mysqli_query($GLOBALS["mysqli"], "SELECT affiche_moyenne FROM j_matieres_categories_classes WHERE (classe_id = '".$id_classe."' and categorie_id = '".$cat_id."')");
					if (mysqli_num_rows($affiche_cat_moyenne_query) == "0") {
						$affiche_cat_moyenne = false;
					} else {
						$affiche_cat_moyenne = old_mysql_result($affiche_cat_moyenne_query, 0);
					}

					if($affiche_cat_moyenne){
						$moy_classe="-";
						$loop_i=0;
						while($loop_i<count($tab_moy['periodes'][$nb]['current_eleve_login'])) {
							if(isset($tab_moy['periodes'][$nb]['moy_cat_classe'][$loop_i][$cat_id])) {
								$moy_classe=$tab_moy['periodes'][$nb]['moy_cat_classe'][$loop_i][$cat_id];
								break;
							}
							$loop_i++;
						}

						echo $cat_names[$cat_id] . " - <b>".nf($moy_classe,2)."</b><br/>\n";

					}
				}
			}
			echo "</td>\n";
			if($afficher_signalement_faute=='y') {
				echo "<td class='bull_simpl noprint'>-</td>\n";
			}
			echo "</tr>\n";
		/*
		} else {
			echo "<td class='bull_simpl' style='text-align:left; $style_bordure_cell'>-</td>\n";
			if($afficher_signalement_faute=='y') {
				echo "<td class='bull_simpl noprint'>-</td>\n";
			}
			echo "</tr>\n";
		}
		*/
		$nb++;
		$print_tr = 'yes';
	}
}

echo "</table>\n";

/*
// Les absences

echo "<table width=$larg_tab border=1 cellspacing=1 cellpadding=1>\n";
$nb=$periode1;
while ($nb < $periode2+1) {
	$current_eleve_absences_query = mysql_query("SELECT * FROM absences WHERE (login='$current_eleve_login' AND periode='$nb')");
	$eleve_abs[$nb] = @old_mysql_result($current_eleve_absences_query, 0, "nb_absences");
	$eleve_abs_nj[$nb] = @old_mysql_result($current_eleve_absences_query, 0, "non_justifie");
	$eleve_retards[$nb] = @old_mysql_result($current_eleve_absences_query, 0, "nb_retards");
	$current_eleve_appreciation_absences = @old_mysql_result($current_eleve_absences_query, 0, "appreciation");
	if (($eleve_abs[$nb] != '') and ($eleve_abs_nj[$nb] != '')) {
		$eleve_abs_j[$nb] = $eleve_abs[$nb]-$eleve_abs_nj[$nb];
	} else {
		$eleve_abs_j[$nb] = "?";
	}
	$eleve_app_abs[$nb] = @old_mysql_result($current_eleve_absences_query, 0, "appreciation");
	if ($eleve_abs_nj[$nb] == '') { $eleve_abs_nj[$nb] = "?"; }
	if ($eleve_retards[$nb] == '') { $eleve_retards[$nb] = "?"; }
	echo "<tr>\n<td valign=top class='bull_simpl'>$nom_periode[$nb]</td>\n";
	echo "<td valign=top class='bull_simpl'>\n";
	if ($eleve_abs_j[$nb] == "1") {
		echo "Absences justifiées : une demi-journée";
	} else if ($eleve_abs_j[$nb] != "0") {
		echo "Absences justifiées : $eleve_abs_j[$nb] demi-journées";
	} else {
		echo "Aucune absence justifiée";
	}
	echo "</td>\n";
	echo "<td valign=top class='bull_simpl'>\n";
	if ($eleve_abs_nj[$nb] == '1') {
		echo "Absences non justifiées : une demi-journée";
	} else if ($eleve_abs_nj[$nb] != '0') {
		echo "Absences non justifiées : $eleve_abs_nj[$nb] demi-journées";
	} else {
		echo "Aucune absence non justifiée";
	}
	echo "</td>\n";
	echo "<td valign=top class='bull_simpl'>Nb. de retards : $eleve_retards[$nb]</td>\n</tr>\n";
	//Ajout Eric
	if ($current_eleve_appreciation_absences != "") {
	echo "<tr>\n";
	echo "<td valign=top class='bull_simpl'>&nbsp;</td>\n";
	echo "<td valign=top class='bull_simpl' colspan=\"3\">";
	echo " Observation(s) : $current_eleve_appreciation_absences</td>\n</tr>\n";
	}

	$nb++;
}
echo "</table>\n";
*/

// Maintenant, on met l'avis du conseil de classe :

echo "<span class='bull_simpl'><b>Avis du conseil de classe </b> ";
$gepi_prof_suivi=ucfirst(retourne_denomination_pp($id_classe));
/*
if ($current_eleve_profsuivi_login) {
	echo "<b>(".ucfirst(getSettingValue("gepi_prof_suivi"))." : <i>".affiche_utilisateur($current_eleve_profsuivi_login,$id_classe)."</i>)</b>";
}
*/
if(empty($current_profsuivi_login)) {
	//echo "Pas de $gepi_prof_suivi désigné.";
	//echo "(-)";
	echo "";
}
else {
	echo "<b>($gepi_prof_suivi <i>";
	for($loop=0;$loop<count($current_profsuivi_login);$loop++) {
		if($loop>0) {echo ", ";}
		echo affiche_utilisateur($current_profsuivi_login[$loop],$id_classe);
	}
	echo "</i></b>)";
}

echo " :</span>\n";
$larg_col1b = $larg_tab - $larg_col1 ;
echo "<table width=\"$larg_tab\" class='boireaus' cellspacing='1' cellpadding='1' summary=''>\n";
$nb=$periode1;
while ($nb < $periode2+1) {
	$sql="SELECT * FROM synthese_app_classe WHERE (id_classe='$id_classe' AND periode='$nb');";
	//echo "$sql<br />";
	$res_current_synthese=mysqli_query($GLOBALS["mysqli"], $sql);
	$current_synthese[$nb] = @old_mysql_result($res_current_synthese, 0, "synthese");
	if ($current_synthese[$nb] == '') {$current_synthese[$nb] = ' -';}

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

	echo "<tr>\n<td valign=\"top\" width =\"$larg_col1\" class='bull_simpl' style='$style_bordure_cell'>$nom_periode[$nb]</td>\n";
	echo "<td valign=\"top\" width = \"$larg_col1b\" class='bull_simpl' style='text-align:left; $style_bordure_cell'>";
	if ($tab_acces_app[$nb]=="y") {
		echo nl2br($current_synthese[$nb]);
	}
	echo "</td>\n";
	//=====================
	echo "</tr>\n";
	//=====================
	$nb++;
}
echo "</table>\n";
}


//========================================================
echo "<div id='div_signaler_faute_grp' style='position: absolute; top: 220px; right: 20px; width: 700px; text-align:center; color: black; padding: 0px; border:1px solid black; display:none;'>
	<div class='infobulle_entete' style='color: #ffffff; cursor: move; width: 700px; font-weight: bold; padding: 0px;' onmousedown=\"dragStart(event, 'div_signaler_faute_grp')\">
		<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>
			<a href='#' onClick=\"cacher_div('div_signaler_faute_grp');return false;\">
				<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />
			</a>
		</div>

		<div id='titre_entete_signaler_faute_grp'></div>
	</div>

	<div id='corps_signaler_faute_grp' class='infobulle_corps' style='color: #ffffff; cursor: auto; font-weight: bold; padding: 0px; height: 15em; width: 700px; overflow: auto;'>
		<form name='form_signalement_faute_grp' id='form_signalement_faute_grp' action ='../lib/ajax_signaler_faute.php' method='post' target='_blank'>
			<input type='hidden' name='signalement_app_grp' id='signalement_app_grp' value='y' />
			<input type='hidden' name='signalement_id_groupe_grp' id='signalement_id_groupe_grp' value='' />
			<input type='hidden' name='signalement_num_periode_grp' id='signalement_num_periode_grp' value='' />
			<input type='hidden' name='signalement_id_classe_grp' id='signalement_id_classe_grp' value='$id_classe' />
			<input type='hidden' name='suppression_possible_grp' id='signalement_suppression_possible_grp' value='oui' />

			<div id='div_signalement_message_grp'></div>

			<input type='button' onclick='valider_signalement_faute_classe()' name='Envoyer' value='Envoyer' />
			".add_token_field()."
		</form>
	</div>
</div>\n";
//========================================================

//========================================================
echo "<script type='text/javascript'>
// <![CDATA[

function signaler_une_faute_classe(id_groupe, liste_profs_du_groupe, num_periode) {

document.getElementById('titre_entete_signaler_faute_grp').innerHTML='Signaler un problème/faute pour le groupe n°'+id_groupe+' période '+num_periode;
document.getElementById('signalement_id_groupe_grp').value=id_groupe;

document.getElementById('signalement_num_periode_grp').value=num_periode;

info_groupe=''
if(document.getElementById('signalement_id_groupe_grp_'+id_groupe)) {
	info_groupe=document.getElementById('signalement_id_groupe_grp_'+id_groupe).value;
}

message='Bonjour,\\n\\nL\'appréciation de le groupe classe de l\'enseignement n°'+id_groupe+' ('+info_groupe+') en période n°'+num_periode+' présente un problème ou une faute:\\n';
message=message+'================================\\n';
// Le champ textarea n'existe que si une appréciation a été enregistrée
if(document.getElementById('appreciation_grp_'+id_groupe+'_'+num_periode)) {
	//message=message+addslashes(document.getElementById('appreciation_grp_'+id_groupe+'_'+num_periode).innerHTML);
	message=message+document.getElementById('appreciation_grp_'+id_groupe+'_'+num_periode).innerHTML;
}
//alert('document.getElementById(\'appreciation_grp_'+id_groupe+'_'+num_periode+').innerHTML');
message=message+'\\n================================\\n'
";
if(getSettingValue('url_racine_gepi')!="") {
	//echo "		message=message+'\\nAprès connexion dans Gepi, l\'adresse pour corriger est ".getSettingValue('url_racine_gepi')."/saisie/saisie_appreciations.php?id_groupe='+id_groupe;\n";
	echo "		message=message+'\\nAprès connexion dans Gepi, l\'adresse pour corriger est ___URL_PAGE_CORRECTION___';\n";
	echo "		message=message+'\\n'";
}
echo "
message=message+'\\n\\nCordialement\\n-- \\n".casse_mot($_SESSION['prenom'],'majf2')." ".$_SESSION['nom']."'

//alert('message='+message);

document.getElementById('div_signalement_message_grp').innerHTML='<textarea name=\'signalement_message_grp\' id=\'signalement_message_grp\' cols=\'50\' rows=\'11\'></textarea>';

document.getElementById('signalement_message_grp').innerHTML=message;
";

if((isset($inclusion_depuis_graphes))&&($inclusion_depuis_graphes=='y')) {
	echo "		afficher_div('div_signaler_faute_grp','n',0,0);\n";
}
else {
	echo "		afficher_div('div_signaler_faute_grp','y',100,100);\n";
}

echo "
}

function valider_signalement_faute_classe() {
signalement_id_groupe=document.getElementById('signalement_id_groupe_grp').value;

signalement_message=document.getElementById('signalement_message_grp').value;

signalement_num_periode=document.getElementById('signalement_num_periode_grp').value;
signalement_id_classe=document.getElementById('signalement_id_classe_grp').value;

//alert(signalement_message);

document.getElementById('signalement_effectue_groupe_'+signalement_id_groupe+'_'+signalement_num_periode).innerHTML=\"<img src='../images/spinner.gif' />\";

new Ajax.Updater($('signalement_effectue_groupe_'+signalement_id_groupe+'_'+signalement_num_periode),'../lib/ajax_signaler_faute.php?a=a&".add_token_in_url(false)."',{method: 'post',
parameters: {
	signalement_app_grp: 'y',
	signalement_id_groupe: signalement_id_groupe,
	signalement_id_classe: signalement_id_classe,
	signalement_num_periode: signalement_num_periode,
	no_anti_inject_signalement_message: signalement_message,
	suppression_possible:'oui'
}});

cacher_div('div_signaler_faute_grp');

}
//]]>
</script>\n";
//========================================================

?>
