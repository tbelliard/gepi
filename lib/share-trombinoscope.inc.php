<?php
/**********************************************************************************************
 *                                  Fonctions Trombinoscope
 **********************************************************************************************/

/**
 * Crée les répertoires photos/RNE_Etablissement, photos/RNE_Etablissement/eleves et
 * photos/RNE_Etablissement/personnels s'ils n'existent pas
 * @return boolean TRUE si tout se passe bien ou FALSE si la création d'un répertoire échoue
 * @see getSettingValue()
 */
function cree_repertoire_multisite() {
  if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		// On récupère le RNE de l'établissement
	if (!$repertoire=$_COOKIE['RNE'])
	  return FALSE;
	//on vérifie que le dossier photos/RNE_Etablissement n'existe pas
	if (!is_dir("../photos/".$repertoire)){
	  // On crée le répertoire photos/RNE_Etablissement
	  if (!mkdir("../photos/".$repertoire, 0700))
		return FALSE;
	  // On enregistre un fichier index.html dans photos/RNE_Etablissement
	  if (!copy  (  "../photos/index.html"  ,  "../photos/".$repertoire."/index.html" ))
		return FALSE;
	}
	//on vérifie que le dossier photos/RNE_Etablissement/eleves n'existe pas
	if (!is_dir("../photos/".$repertoire."/eleves")){
	  // On crée le répertoire photos/RNE_Etablissement/eleves
	  if (!mkdir("../photos/".$repertoire."/eleves", 0700))
		return FALSE;
	  // On enregistre un fichier index.html dans photos/RNE_Etablissement/eleves
	  if (!copy  (  "../photos/index.html"  ,  "../photos/".$repertoire."/eleves/index.html" ))
		return FALSE;
	 }
	//on vérifie que le dossier photos/RNE_Etablissement/personnels n'existe pas
	if (!is_dir("../photos/".$repertoire."/personnels")){
	  // On crée le répertoire photos/RNE_Etablissement/personnels
	  if (!mkdir("../photos/".$repertoire."/personnels", 0700))
		return FALSE;
	  // On enregistre un fichier index.html dans photos/RNE_Etablissement/personnels
	  if (!copy  (  "../photos/index.html"  ,  "../photos/".$repertoire."/personnels/index.html" ))
		return FALSE;
	  }
	}
	return TRUE;
}


/**
 * Crée si nécessaire l'entrée 'encodage_nom_photo' dans la table 'setting'
 * Crée ou modifie la valeur aléatoire 'alea_nom_photo' utilisée pour encoder
 *  le nom des fichiers photo des élèves et l'enregistre dans la table 'setting'
 * Renvoie true si l'encodage est activé ou réactivé, false sinon.
 *
 * @param string $alea_nom_photo Si non vide détermine la valeur à donner à 'alea_nom_photo'
 * @return boolean true : succès, false : échec
 * @see encode_nom_photo()
 */
function active_encodage_nom_photo($alea_nom_photo="") {
	$retour=true;
	if ((getSettingValue('encodage_nom_photo')==NULL) || !getSettingAOui('encodage_nom_photo')) $retour=$retour && saveSetting('encodage_nom_photo','yes');
	if ($alea_nom_photo=="") $alea_nom_photo=md5(time());
	return $retour && saveSetting('alea_nom_photo',$alea_nom_photo);
}


/**
 * Recherche les élèves sans photos
 *
 * @return array tableau de login - nom - prénom - classe - classe court - eleonet
 * @see nom_photo()
 */
function recherche_eleves_sans_photo() {
  $eleve=NULL;
  $requete_liste_eleve = "SELECT e.elenoet, e.login, e.nom, e.prenom, c.nom_complet, c.classe
	FROM eleves e, j_eleves_classes jec, classes c
	WHERE e.login = jec.login
	AND jec.id_classe = c.id
	GROUP BY e.login
	ORDER BY id_classe, nom, prenom ASC";
  $res_eleve = mysql_query($requete_liste_eleve);
  while ($row = mysql_fetch_object($res_eleve)) {
	$nom_photo = nom_photo($row->elenoet);
	if (!($nom_photo and file_exists($nom_photo))) {
	  $eleve[]=$row;
	}
  }
  return $eleve;
}

/**
 * Recherche les personnels sans photos
 *
 * @param string $statut statut recherché
 * @return array tableau des personnels sans photo ou NULL
 * @see nom_photo()
 */
function recherche_personnel_sans_photo($statut='professeur') {
  $personnel=NULL;
  $requete_liste_personnel = "SELECT login,nom,prenom FROM utilisateurs u
	WHERE u.statut='".$statut."' AND u.etat='actif' 
	ORDER BY nom, prenom ASC";
  $res_personnel = mysql_query($requete_liste_personnel);
  while ($row = mysql_fetch_object($res_personnel)) {
	$nom_photo = nom_photo($row->login,"personnels");
	if (!($nom_photo and file_exists($nom_photo))) {
	  $personnel[]=$row;
	}
  }
  return $personnel;
}

/**
 * Efface le dossier photo passé en argument
 * @param string $photos le dossier à effacer personnels ou eleves
 * @return string L'état de la suppression
 * @see cree_zip_archive()
 * @see getSettingValue()
 */
function efface_photos($photos) {
// on liste les fichier du dossier photos/personnels ou photos/eleves
  if (!($photos=="eleves" || $photos=="personnels"))
	return ("Le dossier <strong>".$photos."</strong> n'est pas valide.");
  if (cree_zip_archive("photos")==TRUE){
	$fichier_sup=array();
	if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		  // On récupère le RNE de l'établissement
	  if (!$repertoire=$_COOKIE['RNE'])
		return ("Erreur lors de la récupération du dossier établissement.");
	} else {
	  $repertoire="";
	}
	$folder = "../photos/".$repertoire.$photos."/";
	$dossier = opendir($folder);
	while ($Fichier = readdir($dossier)) {
	  if (mb_strtolower(pathinfo($Fichier,PATHINFO_EXTENSION))=="jpg") {
		$nomFichier = $folder."".$Fichier;
		$fichier_sup[] = $nomFichier;
	  }
	}
	closedir($dossier);
	if(count($fichier_sup)==0) {
	  return ("Le dossier <strong>".$folder."</strong> ne contient pas de photo.") ;
	} else {
	  $nb_erreurs=0; $erreurs="";
	  foreach ($fichier_sup as $fic_efface) {
		if(file_exists($fic_efface)) {
		  @unlink($fic_efface);
		  if(file_exists($fic_efface)) {
			$nb_erreurs++;
			$erreurs.="Le fichier  <strong>".$fic_efface."</strong> n'a pas pu être effacé.<br />";
		  }
		}
	  }
	  unset ($fic_efface);
	  if ($nb_erreurs>0) {
		if ($nb_erreurs>10) return $nb_erreurs." fichiers n'ont pu être effacés.";
			else return $erreurs;
	  }
		else return ("Le dossier <strong>".$folder."</strong> a été vidé.") ;
	}
  }else{
	return ("Erreur lors de la création de l'archive.") ;
  }
}

/**
 * Calcule les dimensions pour afficher une photo
 * dans un cadre de dimensions largeur_max X hauteur_max
 * en conservant le ratio initial
 *
 * @param string $photo L'adresse de la photo
 * @param integer $largeur_max Largeur du cadre
 * @param integer $hauteur_max Hauteur du cadre
 * @return array Les nouvelles dimensions de l'image (largeur, hauteur)
 */
function dimensions_affichage_photo($photo,$photo_largeur_max, $photo_hauteur_max) {
	$nouvelle_largeur=$photo_largeur_max;
	$nouvelle_hauteur=$photo_hauteur_max;

	// prendre les informations sur l'image
	$info_image=getimagesize($photo);
	if(!$info_image) {
		echo "<span style='color:red'>Erreur sur $photo</span>";
	}
	else {
		// largeur et hauteur de l'image d'origine
		$largeur=$info_image[0];
		$hauteur=$info_image[1];

		// calcule le ratio de redimensionnement
		$ratio_l=$largeur/$photo_largeur_max;
		$ratio_h=$hauteur/$photo_hauteur_max;
		$ratio=($ratio_l>$ratio_h)?$ratio_l:$ratio_h;

		// définit largeur et hauteur pour la nouvelle image
		$nouvelle_largeur=round($largeur/$ratio);
		$nouvelle_hauteur=round($hauteur/$ratio);
	}
	return array($nouvelle_largeur, $nouvelle_hauteur);
}

?>
