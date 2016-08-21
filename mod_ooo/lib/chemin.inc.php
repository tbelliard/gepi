<?php

// multisite
// Les dossiers contenant les modèles Gepi et mes modèles. Il faut un / à la fin du chemin.
// $rne='0610559L/';
if ($_SESSION['rne']!='') {
	$rne=$_SESSION['rne']."/";
} else {
	$rne='';
}

// Dans le cas où la fabrication du tableau et les préparatifs du fichier OOo sont en dehors de mod_ooo, il faut tenir compte d'un préfixe de la forme ../mod_ooo/
if(!isset($prefixe_generation_hors_dossier_mod_ooo)) {
	$prefixe_generation_hors_dossier_mod_ooo="";
}

$persoAutorise = isset($persoAutorise) ? $persoAutorise : FALSE;
$nom_dossier_modeles_ooo_par_defaut='modeles_gepi/'; 
$nom_dossier_modeles_ooo_mes_modeles='mes_modeles/';
$nom_dossier_modeles_ooo_perso=$_SESSION['login'].'/';

// Traitement du modèle (par defaut ou modèle personnel ) On fait un test  sur l'existance du fichier dans le dossier mes_modeles/
if ($persoAutorise && file_exists($prefixe_generation_hors_dossier_mod_ooo.$nom_dossier_modeles_ooo_mes_modeles.$rne.$nom_dossier_modeles_ooo_perso.$nom_fichier_modele_ooo))   {
	// On utilise le dossier dans mes modèles et eventuellement RNE
	$nom_dossier_modele_a_utiliser = $prefixe_generation_hors_dossier_mod_ooo.$nom_dossier_modeles_ooo_mes_modeles.$rne.$nom_dossier_modeles_ooo_perso;
} else if (file_exists($prefixe_generation_hors_dossier_mod_ooo.$nom_dossier_modeles_ooo_mes_modeles.$rne.$nom_fichier_modele_ooo))   {
	// On utilise le dossier dans mes modèles et eventuellement RNE
	$nom_dossier_modele_a_utiliser = $prefixe_generation_hors_dossier_mod_ooo.$nom_dossier_modeles_ooo_mes_modeles.$rne;
} else {
	// On utilise le dossier contenant les modèles par defaut.
	$nom_dossier_modele_a_utiliser = $prefixe_generation_hors_dossier_mod_ooo.$nom_dossier_modeles_ooo_par_defaut;
}

