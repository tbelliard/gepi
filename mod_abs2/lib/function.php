<?php
/* 
 * multisite
 * Les dossiers contenant les modèles Gepi et mes modèles. Il faut un / à la fin du chemin.
*/

function repertoire_modeles($nom_fichier_modele){
  if ($_SESSION['rne']!='') {
	$rne=$_SESSION['rne']."/";
  } else {
	$rne='';
  }
  $nom_dossier_modeles_par_defaut='../mod_ooo/modeles_gepi/';
  $nom_dossier_modeles_mes_modeles='../mod_ooo/mes_modeles/';

//traitement du modèle (par defaut ou modèle personnel ) On fait un test  sur l'existance du fichier dans le dossier mes_modeles/
  if  (file_exists($nom_dossier_modeles_mes_modeles.$rne.$nom_fichier_modele))   {
    $nom_dossier_modele_a_utiliser = $nom_dossier_modeles_mes_modeles.$rne.$nom_fichier_modele; //Le dossier dans mes modèles et eventuellement RNE
  } else {
    $nom_dossier_modele_a_utiliser = $nom_dossier_modeles_par_defaut.$nom_fichier_modele; //le dossier contenant les modèles par defaut.
  }
  return $nom_dossier_modele_a_utiliser;
}

?>
