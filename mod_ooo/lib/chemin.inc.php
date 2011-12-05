<?PHP

//multisite
//Les dossiers contenant les modèles Gepi et mes modèles. Il faut un / à la fin du chemin.
//$rne='0610559L/';
if ($_SESSION['rne']!='') {
	$rne=$_SESSION['rne']."/";
} else {
	$rne='';
}

$nom_dossier_modeles_ooo_par_defaut='modeles_gepi/'; 
$nom_dossier_modeles_ooo_mes_modeles='mes_modeles/';

//traitement du modèle (par defaut ou modèle personnel ) On fait un test  sur l'existance du fichier dans le dossier mes_modeles/
 if  (file_exists($nom_dossier_modeles_ooo_mes_modeles.$rne.$nom_fichier_modele_ooo))   {
    $nom_dossier_modele_a_utiliser = $nom_dossier_modeles_ooo_mes_modeles.$rne; //Le dossier dans mes modèles et eventuellement RNE
 } else {
    $nom_dossier_modele_a_utiliser = $nom_dossier_modeles_ooo_par_defaut; //le dossier contenant les modèles par defaut.
 }
 
//echo $nom_dossier_modele_a_utiliser;

?>