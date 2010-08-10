<?php
/*
 * $Id: header_template.inc $
 *
 * Basé sur  header.inc 2968 2009-03-03 20:11:00Z jjocal $
 * modifié avec getSettingValue("contact_admin_mailto")=='y'
 
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Patrick Duthilleul
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
 
 
 
// Based off of code from:header.inc
 

/* ---------Variables envoyées au gabarit
*	$tbs_gepiSchoolName							nom de l'établissement
*	$tbs_message_enregistrement
* $tbs_temps_max									temps maximal de session
*	$tbs_gepiPath										chemin de gepi
*	$tbs_prototype									fichier de la bibliothèque prototype
*	$tbs_charger_observeur
* $tbs_degrade_entete
*	tbs_modif_bandeau
*	$tbs_bouton_taille							chemin de gepi si on affiche les boutons de réduction de taille du bandeau
*	$titre_page
*	$tbs_nom_prenom
*	$tbs_last_connection
* $tbs_mise_a_jour								chemin de gepi si le module mise à jour est actif
*	$tbs_version_gepi
*	$tbs_msg												message à afficher en haut de page
*
*	----- tableaux -----
*	$tbs_refresh										initialisation de la méthode refresh
*				-> tempsmax								
*				-> lien
*				-> debut
*				-> id_session
* $tbs_librairies									bibliothèques à ajouter
*	$tbs_CSS												fichiers CSS
*				-> fichier
*				-> rel
*				-> type
*				-> media
*				-> title
*	$tbs_statut											statut
*				-> class									modifie la police
*				-> texte									
*				-> ajout									pour mettre en évidence du texte
*	$donnees_enfant
*				-> nom_enfant
*				-> classe_enfant
* $tbs_premier_menu								menu du bandeau 1ère ligne
*				-> lien
*				-> confirme
*				-> image
*				-> alt
*				-> title
*				-> texte
*	$tbs_deux_menu									menu du bandeau 2ème ligne
*				-> lien
*				-> onclick
*				-> texte


$TBS->MergeBlock('tbs_refresh',$tbs_refresh) ;
$TBS->MergeBlock('tbs_librairies',$tbs_librairies) ;
$TBS->MergeBlock('tbs_CSS',$tbs_CSS) ;
$TBS->MergeBlock('tbs_statut',$tbs_statut) ;
$TBS->MergeBlock('donnees_enfant',$donnees_enfant) ;
$TBS->MergeBlock('tbs_premier_menu',$tbs_premier_menu	) ;
$TBS->MergeBlock('tbs_deux_menu',$tbs_deux_menu) ;

unset($tbs_refresh,$tbs_librairies,$tbs_CSS,$tbs_statut,$donnees_enfant,$tbs_premier_menu,$tbs_deux_menu);

*/
 
$donnees_enfant=array();

function last_connection() {
	global $gepiPath;
   $sql = "select START, AUTOCLOSE, REMOTE_ADDR from log where LOGIN = '".$_SESSION['login']."' and SESSION_ID != '".session_id()."' order by START desc";
   $res = sql_query($sql);
   $r = '';
   if ($res) {
      $row = sql_row($res, 0);
      $annee_b = substr($row[0],0,4);
      $mois_b =  substr($row[0],5,2);
      $jour_b =  substr($row[0],8,2);
      $heures_b = substr($row[0],11,2);
      $minutes_b = substr($row[0],14,2);
      $secondes_b = substr($row[0],17,2);
      if ($row[0]  != '') {
          if ($row[1]  == "4") {
              $r = "<span style=\"color: red\"><strong>Tentative de connexion le ".$jour_b."/".$mois_b."/".$annee_b." à ".$heures_b." h ".$minutes_b. " avec un mot de passe erroné</strong></span> (<a href='".$gepiPath."/utilisateurs/mon_compte.php#connexion'".insert_confirm_abandon().">journal des connexions</a>)";
              // On compte le nombre de tentatives infructueuses successives
              $nb_tentative = 0;
              $flag = 0;
              for ($i = 0; (($row_b = sql_row($res, $i)) and ($flag < 1)); $i++)
              {
				if (($row_b[1]  == "2") and ($row_b[2]  == $row[2])) {
					$nb_tentative++;
				}
				else {
					$flag = 1;
				}
              }
              if ($nb_tentative > 1) {$r .= "<br /><strong>Nombre de tentatives de connexion successives : ".$nb_tentative.".</strong></font>";}
          } else {
              $r = "  Dernière session ouverte le ".$jour_b."/".$mois_b."/".$annee_b." à ".$heures_b." h ".$minutes_b. " (<a href='".$gepiPath."/utilisateurs/mon_compte.php#connexion'".insert_confirm_abandon().">journal des connexions</a>)";
			}
      }
    }
    return $r;
    
}

$tbs_temps_max = getSettingValue("sessionMaxLength")*60;
$tbs_gepiPath = $gepiPath;

//$tbs_refresh[]=array("tempsmax"=> $tbs_temps_max , "lien"=>$gepiPath , "debut"=>urlencode($_SESSION['start']) , "id_session"=>session_id());

$tbs_gepiSchoolName = getSettingValue("gepiSchoolName");

$tbs_message_enregistrement="";
if (isset($affiche_message) and ($affiche_message == 'yes')) { 
	$tbs_message_enregistrement = $message_enregistrement;  
} 
 
 

// ====================== Affichage des javascripts ===================

// Ajout du framework prototype 1.5.1.1 conditionné à la variable $utilisation_prototype="ok"
$prototype = "ok"; //isset($utilisation_prototype) ? $utilisation_prototype : NULL;
if ($prototype == "ok") {
	// On affiche alors le lien qui charge Prototype
	$tbs_librairies[]=$gepiPath."/lib/prototype.js";
}
	
// Ajout de la librairie Scriptaculous.js conditionné à la variable $utilisation_scriptaculous="ok"
$scriptaculous = isset($utilisation_scriptaculous) ? $utilisation_scriptaculous : NULL;
$script_effet = isset($scriptaculous_effet) ? $scriptaculous_effet : NULL;
if ($scriptaculous == "ok") {
	// On affiche le lien qui charge scriptaculous
	$tbs_librairies[]=$gepiPath."/lib/scriptaculous.js?load=".$script_effet;
}
// Utilisation de windows.js
$windows = isset($utilisation_win) ? $utilisation_win : NULL;
if ($windows == 'oui') {
	$tbs_librairies[]=$gepiPath."/edt_effets/javascripts/effects.js";
	$tbs_librairies[]=$gepiPath."/edt_effets/javascripts/window.js";
	$tbs_librairies[]=$gepiPath."/edt_effets/javascripts/window_effects.js";
	$tbs_CSS[]=array("fichier"=> $gepiPath."/edt_effets/themes/default.css" , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"" , "title"=>"");
	$tbs_CSS[]=array("fichier"=> $gepiPath."/edt_effets/themes/alphacube.css" , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"" , "title"=>"");
}
// js spécifique
/*
if(isset($javascript_specifique)) {
	// Il faudrait filtrer le contenu de la variable...
	// On ajoute le ".js" automatiquement et on exclus les "." qui pourrait permettre des ".." pour remonter dans l'arborescence
	if(strlen(my_ereg_replace("[A-Za-z0-9_/]","",$javascript_specifique))==0) {
		// Javascript spécifique à une page:
		$tbs_librairies[]=$gepiPath/$javascript_specifique.'js';
	}
}
*/

if(isset($javascript_specifique)) {
	// Il faudrait filtrer le contenu de la variable...
	// On ajoute le ".js" automatiquement et on exclus les "." qui pourrait permettre des ".." pour remonter dans l'arborescence

	if(is_array($javascript_specifique)) {
		foreach($javascript_specifique as $current_javascript_specifique) {
			if(strlen(my_ereg_replace("[A-Za-z0-9_/\-]","",$current_javascript_specifique))==0) {
				// Javascript spécifique à une page:
				//echo "<script type='text/javascript' src='$gepiPath/$current_javascript_specifique.js'></script>\n";
		$tbs_librairies[]=$gepiPath."/".$current_javascript_specifique.'.js';
			}
		}
	}
	else {
		if(strlen(my_ereg_replace("[A-Za-z0-9_/\-]","",$javascript_specifique))==0) {
			// Javascript spécifique à une page:
			//echo "<script type='text/javascript' src='$gepiPath/$javascript_specifique.js'></script>\n";
			$tbs_librairies[]=$gepiPath."/".$javascript_specifique.'.js';
		}
	}
}


// On affiche tout le temps brainjar sauf quand on dit à Gepi de ne pas le faire
$utilisation_jsdivdrag = isset($utilisation_jsdivdrag) ? $utilisation_jsdivdrag : NULL;
//if (isset($utilisation_jsdivdrag) AND $utilisation_jsdivdrag == "non") {
if (isset($utilisation_jsdivdrag) AND ($utilisation_jsdivdrag == "non")) {
	//echo "<!-- Pas de brainjar-->\n";(
}else{
	//===================================
	// Pour aérer les infobulles si jamais Javascript n'est pas actif.
	// Sinon, avec le position:absolute, les div se superposent.
	$posDiv_infobulle=0;
	// $posDiv_infobulle permet de fixer la position horizontale initiale du Div.

	$tabdiv_infobulle=array();
	$tabid_infobulle=array();

	// Choix de l'unité pour les dimensions des DIV: em, px,...
	$unite_div_infobulle="em";
	// Pour l'overflow dans les DIV d'aide, il vaut mieux laisser 'em'.

		$tbs_librairies[]=$gepiPath.'/lib/brainjar_drag.js';
		$tbs_librairies[]=$gepiPath.'/lib/position.js';

} 




// ======================= Début de l'affichage des feuilles de style ================
$style = getSettingValue("gepi_stylesheet");

// style.css
if (empty($style)) $style = "style";

//===== utiliser de préférence $gepiPath."/css/".$style.".css"
$tbs_CSS[]=array("fichier"=>$gepiPath."/css/".$style.".css"  , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"screen" , "title"=>"");

// Couleur de fond des pages
if (!isset($titre_page)) $bgcouleur = "bgcolor= \"#FFFFFF\""; else $bgcouleur = "";

// Styles spécifiques
/*
if(isset($style_specifique)) {
	// Il faudrait filtrer le contenu de la variable...
	// ne doit contenir que certains types de caractères et se terminer par .css
	// Non... on ajoute le ".css" automatiquement et on exclus les "." qui pourrait permettre des ".." pour remonter dans l'arborescence
	if(strlen(my_ereg_replace("[A-Za-z0-9_/]","",$style_specifique))==0) {
		// Styles spécifiques à une page:
		$tbs_CSS[]=array("fichier"=> $gepiPath."/".$style_specifique.".css" , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"" , "title"=>"");
	}
}
*/
if(isset($style_specifique)) {
  // Il faudrait filtrer le contenu de la variable...
  // ne doit contenir que certains types de caractères et se terminer par .css
  // Non... on ajoute le ".css" automatiquement et on exclus les "." qui pourrait permettre des ".." pour remonter dans l'arborescence

  if(is_array($style_specifique)) {
	foreach($style_specifique as $current_style_specifique) {
	  if(strlen(my_ereg_replace("[A-Za-z0-9_/]","",$current_style_specifique))==0) {
		//// Styles spécifiques à une page:
		$tbs_CSS[]=array("fichier"=> $gepiPath."/".$current_style_specifique.".css" , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"" , "title"=>"");

	  }

	}
  } else {
	if(strlen(my_ereg_replace("[A-Za-z0-9_/]","",$style_specifique))==0) {
	  // Styles spécifiques à une page:
	  $tbs_CSS[]=array("fichier"=> $gepiPath."/".$style_specifique.".css" , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"" , "title"=>"");
	}
  }
}

// vérifie si on est dans le modules absences
$files = array("gestion_absences", "select", "ajout_abs", "ajout_ret", "ajout_dip", "ajout_inf", "tableau", "impression_absences", "prof_ajout_abs", "statistiques", "alert_suivi", "admin_config_semaines", "admin_motifs_absences", "admin_horaire_ouverture", "admin_actions_absences", "admin_periodes_absences");
if(in_array(basename($_SERVER['PHP_SELF'],".php"), $files)) {
	$tbs_CSS[]=array("fichier"=> $gepiPath."/mod_absences/styles/mod_absences.css" , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"" , "title"=>"");
}


if(isset($accessibilite)) {
	if($accessibilite=="y") {
		$tbs_CSS[]=array("fichier"=> $gepiPath."/accessibilite.css" , "rel"=>"stylesheet" , "type"=>"text/css","media"=>"screen" , "title"=>"");
 // Feuilles de styles imprimante
		$tbs_CSS[]=array("fichier"=> $gepiPath."/accessibilite_print.css" , "rel"=>"stylesheet" , "type"=>"text/css","media"=>"print" , "title"=>"");
	}
}

// Feuilles de styles du telephone
$tbs_CSS[]=array("fichier"=> $gepiPath."/css/style_telephone.css" , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"handheld" , "title"=>"");

// Feuilles de styles du bandeau
// $tbs_CSS[]=array("fichier"=> $gepiPath."/css/bandeau_r01.css" , "rel"=>"stylesheet" , "type"=>"text/css","media"=>"screen" , "title"=>"bandeau");


// fin modification Régis

if (isset($style_screen_ajout))  {

	// Styles paramétrables depuis l'interface:
	if($style_screen_ajout=='y') {
		// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
		// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
		// Il faudra définir un nom de la forme style_screen_ajout_RNE.css pour le multisite
		
		if (!isset($niveau_arbo)) $niveau_arbo = 1;
	 if ($niveau_arbo == "0") {
	   $gepiPath2=".";
	} elseif ($niveau_arbo == "1") {
	   $gepiPath2="..";
	} elseif ($niveau_arbo == "2") {
	   $gepiPath2="../..";
	} elseif ($niveau_arbo == "3") {
	   $gepiPath2="../../..";
	}
	
		$Style_CSS=array(); // initialisation du tableau de Style supplémentaire	
		
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			if (@file_exists($gepiPath2.'/style_screen_ajout_'.getSettingValue("gepiSchoolRne").'.css')) {
				$Style_CSS[]=array("fichier"=>$gepiPath."/style_screen_ajout_".getSettingValue("gepiSchoolRne").".css"  , "rel"=>"stylesheet" , "type"=>"text/css","media"=>"" , "title"=>"");
				
			}
		} else {
			if (@file_exists($gepiPath2.'/style_screen_ajout.css')) {
				$Style_CSS[]=array("fichier"=>$gepiPath."/style_screen_ajout.css"  , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"" , "title"=>"");
			}
		}
	}
}else{
}

include 'maj_coul_global.inc';

// ============================== FIN des feuilles de style =======================
// *********** sur le onload *************** //
if (isset($use_observeur) AND $use_observeur == 'ok') {
  $charger_observeur = ' observeur();';
}else{
  $charger_observeur = NULL;
}
$tbs_charger_observeur=$charger_observeur;

if (getSettingValue("impose_petit_entete_prof") == 'y' AND $_SESSION['statut'] == 'professeur') {
	$_SESSION['cacher_header']="y";
}
// Taille à récupérer dans la base pour initialiser $_SESSION['cacher_header']
	// petit bandeau toute valeur sauf "n" ;
	// grand bandeau "n";
if (isset($titre_page)) {
	if(!isset($_SESSION['cacher_header'])) {
		$_SESSION['cacher_header']="n";
	}


/* ===== affichage du bandeau ===== */

	if(getSettingValue('gepi_stylesheet')=='style') {
		// Détermine le fond du bandeau
		if(getSettingValue('utiliser_degrade')=='y') {
			$degrade_entete="degrade1";
		}else{
			$degrade_entete="darkfade";
		}
	}else{
			$degrade_entete="no_style";
	}
// Initialisation du bandeau 
	$tbs_degrade_entete=$degrade_entete;
// Initialisation du bandeau à la bonne taille
	$cacher_header = isset($_SESSION['cacher_header']) ? $_SESSION['cacher_header'] : "n";

	//=====================
	// AJOUT boireaus 20080806
	if(isset($mode_header_reduit)) {
		$cacher_header="y";
	}
	//=====================

	if($cacher_header=="n") {
		$taille_bandeau_header="g";
	}else {
		$taille_bandeau_header="p";
	}
	// 	echo $_SESSION['login'];
	$tbs_modif_bandeau="gd_bandeau";
	if($taille_bandeau_header=="p") {
		$tbs_modif_bandeau="pt_bandeau";
	}

// Bandeau de gauche

	//=====================
	// AJOUT boireaus 20080806
	if(!isset($mode_header_reduit)) {
		$tbs_bouton_taille=$gepiPath;
	}
	//=====================
	// AJOUT boireaus 20080806
	//=====================

	// insert into setting set name='afficher_temoin_filtrage_html',value='y';
	$tbs_afficher_temoin_filtrage_html=getSettingValue('afficher_temoin_filtrage_html');

	//=== Titre de la page ===
	
	//=== Nom Prénom utilisateur ===
	if((!isset($_SESSION['prenom']))||(!isset($_SESSION['nom']))) {
		$sql="SELECT nom, prenom FROM utilisateurs WHERE login='".$_SESSION['login']."';";
		$res_np=mysql_query($sql);
		if(mysql_num_rows($res_np)>0) {
			$lig_np=mysql_fetch_object($res_np);
			$_SESSION['prenom']=$lig_np->prenom;
			$_SESSION['nom']=$lig_np->nom;
		}
	}
	if((isset($_SESSION['prenom']))||(isset($_SESSION['nom']))) {
		$tbs_nom_prenom=$_SESSION['prenom'] . " " . $_SESSION['nom'];
	}else {
		$tbs_nom_prenom="NOM Prenom";
	}
	
	//=== Dernière connexion ===
	if (isset($affiche_connexion)) {
		$tbs_last_connection=last_connection();
	}
				
//=== statut utilisateur ===
	if ($_SESSION['statut'] == "administrateur") {
		$tbs_statut[]=array("classe"=>"rouge" , "texte"=>"Administrateur");
	}elseif ($_SESSION['statut'] == "professeur") {
		$nom_complet_matiere = sql_query1("select nom_complet from matieres
		where matiere = '".$_SESSION['matiere']."'");
		if ($nom_complet_matiere != '-1') {
			$nom_complet_matiere=my_ereg_replace("&", "&amp;" , $nom_complet_matiere);
			$tbs_statut[]=array("classe"=>"" , "texte"=>"Professeur de : " . ($nom_complet_matiere));
		}else{
			$tbs_statut[]=array("classe"=>"" , "texte"=>"Invité");
		}
	}elseif ($_SESSION['statut'] == "scolarite") {
			$tbs_statut[]=array("classe"=>"" , "texte"=>"Scolarité");
	}elseif ($_SESSION['statut'] == "cpe") {
			$tbs_statut[]=array("classe"=>"" , "texte"=>"CPE");
	}elseif ($_SESSION['statut'] == "eleve") {
		$tab_tmp_info_classes=get_noms_classes_from_ele_login($_SESSION['login']);
		$tbs_statut[]=array("classe"=>"" , "texte"=>"Élève de ".$tab_tmp_info_classes[count($tab_tmp_info_classes)-1]);
	}elseif ($_SESSION['statut'] == "responsable") {
		$tab_tmp_ele=get_enfants_from_resp_login($_SESSION['login']);
		$chaine_enfants="";
		if(count($tab_tmp_ele)>0) {
			$nom_enfant=$tab_tmp_ele[1];
			$tab_tmp_info_classes=get_noms_classes_from_ele_login($tab_tmp_ele[0]);
			if(count($tab_tmp_info_classes)>0) {
				$classe_enfant=$tab_tmp_info_classes[count($tab_tmp_info_classes)-1];
			}else{
				$classe_enfant="";
			}
			$donnees_enfant[]=array("nom"=>$nom_enfant , "classe"=>$classe_enfant) ;
			for($i=3;$i<count($tab_tmp_ele);$i+=2) {
				$nom_enfant=", ".$tab_tmp_ele[1];
				unset($tab_tmp_info_classes);
				$tab_tmp_info_classes=get_noms_classes_from_ele_login($tab_tmp_ele[$i-1]);
				if(count($tab_tmp_info_classes)>0) {
					$chaine_enfants.=" (<em>".$tab_tmp_info_classes[count($tab_tmp_info_classes)-1]."</em>)";
					$classe_enfant=$tab_tmp_info_classes[count($tab_tmp_info_classes)-1];
				}else{
					$classe_enfant="";
				}
			$donnees_enfant[]=array("nom"=>$nom_enfant , "classe"=>$classe_enfant) ;
			}
		}
		$tbs_statut[]=array("classe"=>"" , "texte"=>"Responsable de ");
		
	}elseif($_SESSION["statut"] == "autre") {
		$tbs_statut[]=array("classe"=>"" , "texte"=>$_SESSION["statut_special"]);
	}elseif($_SESSION["statut"] == "secours") {
		$tbs_statut[]=array("classe"=>"" , "texte"=>"<strong class='rouge'>compte secours</strong>");
	}
					
	//On vérifie si le module de mise à jour est activé
	$tbs_mise_a_jour="";
	if (getSettingValue("active_module_msj")==='y' and $_SESSION['statut'] == 'administrateur') {
		$tbs_mise_a_jour=$gepiPath;
	}
				
	//christian
	// menus de droite
	// menu accueil
	$tbs_premier_menu[]=array("lien"=>$gepiPath."/accueil.php" , "confirme"=>"insert_confirm_abandon()" , "image"=>$gepiPath."/images/icons/home.png" , "alt"=>"Accueil" , "title"=>"Accueil" , "texte"=>"Accueil");
	$tbs_premier_menu[]=array("lien"=>$gepiPath."/utilisateurs/mon_compte.php" , "confirme"=>"insert_confirm_abandon()" , "image"=>$gepiPath."/images/icons/buddy.png" , "alt"=>"Mon compte" , "title"=>"Mon compte" ,  "texte"=>"Gérer mon compte");
	if ($session_gepi->current_auth_mode == "sso" && $gepiSettings['sso_display_portail'] == 'yes') {
	$tbs_premier_menu[]=array("lien"=>$gepiSettings["sso_url_portail"] , "confirme"=>"" , "image"=>$gepiPath."/images/icons/home.png" , "alt"=>"Portail" , "title"=>"Retour portail" , "texte"=>"Retour portail");
	}
	if ($session_gepi->current_auth_mode != "sso" || $gepiSettings["sso_hide_logout"] != 'yes') {
		$tbs_premier_menu[]=array("lien"=> $gepiPath."/logout.php?auto=0" , "confirme"=>"insert_confirm_abandon()", "image"=>$gepiPath."/images/icons/quit_16.png" , "alt"=>"Se déconnecter" , "title"=>"Se déconnecter" , "texte"=>"Déconnexion");
	}
				
				// menu contact
	$prefix = '';
	if (!isset($niveau_arbo)) {
		$prefix = "../";
	}elseif($niveau_arbo==1) {
		$prefix = "../";
	}elseif ($niveau_arbo==2) {
		$prefix = "../../";
	}

	if ($_SESSION['statut'] == 'administrateur') {
		$tbs_deux_menu[]=array("lien"=>"http://gepi.mutualibre.org" , "onclick"=> "onclick=\"window.open(this.href, '_blank'); return false;\""  , "texte"=>"Visiter le site de GEPI");
	}else{
		if (getSettingValue("contact_admin_mailto")=='y') {
			$gepiAdminAdress=getSettingValue("gepiAdminAdress");
			$tmp_date=getdate();
			$lien="<a href='mailto:$gepiAdminAdress?Subject=Gepi&amp;body=";
			if ($tmp_date['hours']>=18) {$lien.= "Bonsoir";} else {$lien.= "Bonjour";}
			$lien.=",%0d%0aCordialement.";
			$tbs_deux_menu[][]=array("lien"=>$lien , "onclick"=> ""  , "texte"=>"Contacter l'administrateur");
		}else{
			$tbs_deux_menu[]=array("lien"=>"$gepiPath/gestion/contacter_admin.php" , "onclick"=> "onclick=\"centrerpopup('$gepiPath/gestion/contacter_admin.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes'); return false;\""  , "texte"=>"Contacter l'administrateur");
		}
	}
		
	$tbs_deux_menu[]=array("lien"=>"$gepiPath/gestion/info_gepi.php" , "onclick"=> "onclick=\"centrerpopup('$gepiPath/gestion/info_gepi.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes'); return false;\""  , "texte"=>"Informations générales");
	$tbs_deux_menu[]=array("lien"=>"$gepiPath/gestion/info_vie_privee.php" , "onclick"=> "onclick=\"centrerpopup('$gepiPath/gestion/info_vie_privee.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes'); return false;\""  , "texte"=>"Vie privée");
	
		 //=== Affichage de la version de Gepi ===
	if ($_SESSION['statut'] == "administrateur") {
		$version_gepi = '';
		if ($gepiVersion == 'trunk') {
				// On a une version trunk
			$version_gepi .= "gepi-trunk";
		} else {
				// On a une version numérotée
				$version_gepi .= "v".$gepiVersion;
			if ($gepiRcVersion != '') $version_gepi .= "-RC".$gepiRcVersion;
			if ($gepiBetaVersion != '') $version_gepi .= "-beta".$gepiBetaVersion;
			if ($gepiVersionMaintenance != '') $version_gepi .= ".".$gepiVersionMaintenance;
		}
		if ($gepiSvnRev != '') $version_gepi .= " r".$gepiSvnRev;
		$tbs_version_gepi=$version_gepi;
	}else{
		$tbs_version_gepi=" ";			// nécessaire pour recaler le bandeau dans tous les navigateurs
	}
	// Fin du conteneur de Header

// ----- Régis : fin des modifications du bandeau -----


// ==========> On ajoute la barre de menu <========================= //

if (!isset($nobar)) { $nobar = "non"; }
if (getSettingValue("utiliserMenuBarre") == "yes" AND $_SESSION["statut"] == "professeur" AND $nobar != 'oui') {
	// On vérifie que l'utilisateur ne l'a pas enlevée

	if (getPref($_SESSION["login"], "utiliserMenuBarre", "yes") == "yes") {
		// ne pourrait-on pas utiliser $gepiPath plutôt que construire $prefix un peu plus haut ?
		if (file_exists($prefix."edt_organisation/fonctions_calendrier.php")) {
			require_once($prefix."edt_organisation/fonctions_calendrier.php");
		}elseif(file_exists("fonctions_calendrier.php")) {
			require_once("./fonctions_calendrier.php");
		}
		include("header_barre_prof_template.php");
	}
}else{
	$tbs_menu_prof=array();
}

// ==========> Fin on ajoute la barre de menu <========================= //

	$tbs_msg="" ;
	if ((isset($_GET['msg'])) or (isset($_POST['msg'])) or (isset($msg))) {
		$msg = isset($_POST['msg']) ? unslashes($_POST['msg']) : (isset($_GET['msg']) ? unslashes($_GET['msg']) : $msg);
		if ($msg != '') {
			$tbs_msg=$msg ;
		}
	}
}
// Décommenter la ligne ci -dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
//debug_var();

//if (isset($titre_page)) echo "</div>";
?>

