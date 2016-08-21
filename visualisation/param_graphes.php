<?php

$pref_affiche_moy_classe=isset($_SESSION['graphe_affiche_moy_classe']) ? $_SESSION['graphe_affiche_moy_classe'] : getPref($_SESSION['login'],'graphe_affiche_moy_classe','');
if(($pref_affiche_moy_classe=='oui')||($pref_affiche_moy_classe=='non')) {
	$affiche_moy_classe=$pref_affiche_moy_classe;
}
else {
	if(getSettingValue('graphe_affiche_moy_classe')) {
		$affiche_moy_classe=getSettingValue('graphe_affiche_moy_classe');
	}
	else{
		$affiche_moy_classe="oui";
	}
}

$pref_affiche_minmax=isset($_SESSION['graphe_affiche_minmax']) ? $_SESSION['graphe_affiche_minmax'] : getPref($_SESSION['login'],'graphe_affiche_minmax','');
if(($pref_affiche_minmax=='oui')||($pref_affiche_minmax=='non')) {
	$affiche_minmax=$pref_affiche_minmax;
}
else {
	if(getSettingValue('graphe_affiche_minmax')) {
		$affiche_minmax=getSettingValue('graphe_affiche_minmax');
	}
	else{
		$affiche_minmax="oui";
	}
}

$pref_largeur_graphe=isset($_SESSION['graphe_largeur_graphe']) ? $_SESSION['graphe_largeur_graphe'] : getPref($_SESSION['login'],'graphe_largeur_graphe','');
if($pref_largeur_graphe!='') {
	$largeur_graphe=$pref_largeur_graphe;
}
else {
	if(getSettingValue('graphe_largeur_graphe')) {
		$largeur_graphe=getSettingValue('graphe_largeur_graphe');
	}
	else{
		$largeur_graphe=600;
	}
}

if((mb_strlen(preg_replace("/[0-9]/","",$largeur_graphe))!=0)||($largeur_graphe=="")) {
	$largeur_graphe=600;
}

$pref_hauteur_graphe=isset($_SESSION['graphe_hauteur_graphe']) ? $_SESSION['graphe_hauteur_graphe'] : getPref($_SESSION['login'],'graphe_hauteur_graphe','');
if($pref_hauteur_graphe!='') {
	$hauteur_graphe=$pref_hauteur_graphe;
}
else {
	if(getSettingValue('graphe_hauteur_graphe')) {
		$hauteur_graphe=getSettingValue('graphe_hauteur_graphe');
	}
	else{
		$hauteur_graphe=400;
	}
}

if((mb_strlen(preg_replace("/[0-9]/","",$hauteur_graphe))!=0)||($hauteur_graphe=="")) {
	$hauteur_graphe=400;
}

$pref_taille_police=isset($_SESSION['graphe_taille_police']) ? $_SESSION['graphe_taille_police'] : getPref($_SESSION['login'],'graphe_taille_police','');
if($pref_taille_police!='') {
	$taille_police=$pref_taille_police;
}
else {
	if(getSettingValue('graphe_taille_police')) {
		$taille_police=getSettingValue('graphe_taille_police');
	}
	else{
		$taille_police=2;
	}
}

if((mb_strlen(preg_replace("/[0-9]/","",$taille_police))!=0)||($taille_police<1)||($taille_police>$taille_max_police)||($taille_police=="")) {
	$taille_police=2;
}

$pref_epaisseur_traits=isset($_SESSION['graphe_epaisseur_traits']) ? $_SESSION['graphe_epaisseur_traits'] : getPref($_SESSION['login'],'graphe_epaisseur_traits','');
if($pref_epaisseur_traits!='') {
	$epaisseur_traits=$pref_epaisseur_traits;
}
else {
	if(getSettingValue('graphe_epaisseur_traits')) {
		$epaisseur_traits=getSettingValue('graphe_epaisseur_traits');
	}
	else{
		$epaisseur_traits=2;
	}
}

if((mb_strlen(preg_replace("/[0-9]/","",$epaisseur_traits))!=0)||($epaisseur_traits<1)||($epaisseur_traits>6)||($epaisseur_traits=="")) {
	$epaisseur_traits=2;
}

$pref_epaisseur_croissante_traits_periodes=isset($_SESSION['graphe_epaisseur_croissante_traits_periodes']) ? $_SESSION['graphe_epaisseur_croissante_traits_periodes'] : getPref($_SESSION['login'],'graphe_epaisseur_croissante_traits_periodes','');
if(($pref_epaisseur_croissante_traits_periodes=='oui')||($pref_epaisseur_croissante_traits_periodes=='non')) {
	$epaisseur_croissante_traits_periodes=$pref_epaisseur_croissante_traits_periodes;
}
else {
	if(getSettingValue('graphe_epaisseur_croissante_traits_periodes')) {
		$epaisseur_croissante_traits_periodes=getSettingValue('graphe_epaisseur_croissante_traits_periodes');
	}
	else{
		$epaisseur_croissante_traits_periodes="non";
	}
}

$pref_temoin_image_escalier=isset($_SESSION['graphe_temoin_image_escalier']) ? $_SESSION['graphe_temoin_image_escalier'] : getPref($_SESSION['login'],'graphe_temoin_image_escalier','');
if(($pref_temoin_image_escalier=='oui')||($pref_temoin_image_escalier=='non')) {
	$temoin_image_escalier=$pref_temoin_image_escalier;
}
else {
	if(getSettingValue('graphe_temoin_image_escalier')) {
		$temoin_image_escalier=getSettingValue('graphe_temoin_image_escalier');
	}
	else{
		$temoin_image_escalier="non";
	}
}

$pref_tronquer_nom_court=isset($_SESSION['graphe_tronquer_nom_court']) ? $_SESSION['graphe_tronquer_nom_court'] : getPref($_SESSION['login'],'graphe_tronquer_nom_court','');
if(preg_match("/^[0-9]{1,}$/", $pref_tronquer_nom_court)) {
	$tronquer_nom_court=$pref_tronquer_nom_court;
}
else {
	if(getSettingValue('graphe_tronquer_nom_court')) {
		$tronquer_nom_court=getSettingValue('graphe_tronquer_nom_court');
	}
	else{
		$tronquer_nom_court=0;
	}
}

$pref_affiche_mgen=isset($_SESSION['graphe_affiche_mgen']) ? $_SESSION['graphe_affiche_mgen'] : getPref($_SESSION['login'],'graphe_affiche_mgen','');
if(($pref_affiche_mgen=='oui')||($pref_affiche_mgen=='non')) {
	$affiche_mgen=$pref_affiche_mgen;
}
else {
	if(getSettingValue('graphe_affiche_mgen')) {
		$affiche_mgen=getSettingValue('graphe_affiche_mgen');
	}
	else{
		$affiche_mgen="non";
	}
}

$pref_affiche_moy_annuelle=isset($_SESSION['graphe_affiche_moy_annuelle']) ? $_SESSION['graphe_affiche_moy_annuelle'] : getPref($_SESSION['login'],'graphe_affiche_moy_annuelle','');
if(($pref_affiche_moy_annuelle=='oui')||($pref_affiche_moy_annuelle=='non')) {
	$affiche_moy_annuelle=$pref_affiche_moy_annuelle;
}
else {
	if(getSettingValue('graphe_affiche_moy_annuelle')) {
		$affiche_moy_annuelle=getSettingValue('graphe_affiche_moy_annuelle');
	}
	else{
		$affiche_moy_annuelle="non";
	}
}

//===========================================
$graphe_inserer_saut_page=isset($_SESSION['graphe_inserer_saut_page']) ? $_SESSION['graphe_inserer_saut_page'] : "n";
$nb_graphes_saut_page=isset($_SESSION['nb_graphes_saut_page']) ? $_SESSION['nb_graphes_saut_page'] : "0";
//===========================================

$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
if ($affiche_categories == "y") {
	$affiche_categories = true;
} else {
	$affiche_categories = false;
}

?>
