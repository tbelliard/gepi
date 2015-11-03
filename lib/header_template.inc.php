<?php
/**
 * Construit les tableaux nécessaires au header des gabarits
 * 
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Patrick Duthilleul, Bouguin Régis
 * 
 * Variables envoyées au gabarit
 * - $tbs_gepiSchoolName : nom de l'établissement
 * - $tbs_message_enregistrement
 * - $tbs_temps_max : temps maximal de session
 * - $tbs_gepiPath : chemin de gepi
 * - $tbs_prototype : fichier de la bibliothèque prototype
 * - $tbs_charger_observeur
 * - $tbs_degrade_entete
 * - tbs_modif_bandeau
 * - $tbs_bouton_taille : chemin de gepi si on affiche les boutons de réduction de taille du bandeau
 * - $titre_page
 * - $tbs_nom_prenom
 * - $tbs_last_connection
 * - $tbs_mise_a_jour - chemin de gepi si le module mise à jour est actif
 * - $tbs_version_gepi
 * - $tbs_msg - message à afficher en haut de page
 *
 * - $tbs_refresh = array(tempsmax , lien , debut , id_session ) : initialisation de la méthode refresh
 * - $tbs_librairies = array( )								bibliothèques à ajouter
 * - $tbs_CSS = array( fichier , rel , type , media , title ) : fichiers CSS
 * - $tbs_statut = array(class , texte , ajout ) : statut
 * - $donnees_enfant = array(nom_enfant , classe_enfant )
 * - $tbs_premier_menu = array(lien , confirme , image , alt , title , texte ) : menu du bandeau 1ère ligne
 * - $tbs_deux_menu = array(lien , onclick , texte ) : menu du bandeau 2ème ligne
 * 
 * 
 * @license GNU/GPL v2
 * @package General
 * @subpackage Affichage
 * @see get_noms_classes_from_ele_login()
 * @see get_enfants_from_resp_login()
 * @see getPref()
 * @see getSettingValue()
 * @todo Il faudra définir un nom de la forme style_screen_ajout_RNE.css pour le multisite
 * @todo $prefix et $gepiPath2="."; déjà défini dans initialisation.inc.php, on peut économiser un test
 */

/* This file is part of GEPI.
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
 

$donnees_enfant=array();

/**
 * Renvoie la date et l'heure de la dernière connexion ou d'une tentative de connexion avec mauvais mot de passe
 * 
 * @global string
 * @return string la date et l'heure
 */
function last_connection() {
	global $gepiPath;
    global $mysqli;
   $sql = "select START, AUTOCLOSE, REMOTE_ADDR from log where LOGIN = '".$_SESSION['login']."' and SESSION_ID != '".session_id()."' order by START desc";
           
        $res = mysqli_query($mysqli, $sql);
        $r = '';
        if ($res) {
            $row = $res->fetch_row();
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
                    for ($i = 0; (($row_b = sql_row($res, $i)) and ($flag < 1)); $i++) {
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
        $res->close();
        
    return $r;
    
}

$sessionMaxLength=24;
$session_gc_maxlifetime=ini_get("session.gc_maxlifetime");
$sessionMaxLength=getSettingValue("sessionMaxLength");
if(($sessionMaxLength!="")&&(preg_match("/^[0-9]*$/",$sessionMaxLength))) {
	$sessionMaxLength=$sessionMaxLength*60;
	if(($session_gc_maxlifetime!="")&&(preg_match("/^[0-9]*$/",$session_gc_maxlifetime))&&($session_gc_maxlifetime<$sessionMaxLength)) {
		$sessionMaxLength=$session_gc_maxlifetime;
	}
}
elseif(($session_gc_maxlifetime!="")&&(preg_match("/^[0-9]*$/",$session_gc_maxlifetime))) {
	$sessionMaxLength=$session_gc_maxlifetime;
}
$tbs_temps_max=$sessionMaxLength;

$tbs_gepiPath = $gepiPath;

$tbs_gepiSchoolName = getSettingValue("gepiSchoolName");

$tbs_message_enregistrement="";
if (isset($affiche_message) and ($affiche_message == 'yes') and isset($message_enregistrement)) { 
	$tbs_message_enregistrement = $message_enregistrement;  
} 
 


if(getSettingAOui('active_mod_discipline')) {
	$mod_disc_terme_incident=getSettingValue('mod_disc_terme_incident');
	if($mod_disc_terme_incident=="") {$mod_disc_terme_incident="incident";}
}
$mod_disc_terme_menus_incidents=getSettingValue("mod_disc_terme_menus_incidents");
if($mod_disc_terme_menus_incidents=="") {
	$mod_disc_terme_menus_incidents="menus incidents";
}


// ====================== Affichage des javascripts ===================

// Ajout du framework prototype 1.5.1.1 conditionné à la variable $utilisation_prototype="ok"
$prototype = "ok";
if ($prototype == "ok") {
	// On affiche alors le lien qui charge Prototype
	$tbs_librairies[]=$gepiPath."/lib/prototype.js";
}
	
// Ajout de la librairie Scriptaculous.js conditionné à la variable $utilisation_scriptaculous="ok"
$scriptaculous = isset($utilisation_scriptaculous) ? $utilisation_scriptaculous : NULL;
$script_effet = isset($scriptaculous_effet) ? $scriptaculous_effet : NULL;
if ($scriptaculous == "ok") {
	// On affiche le lien qui charge scriptaculous
	if(isset($script_effet)) {
		$tbs_librairies[]=$gepiPath."/lib/scriptaculous.js?load=".$script_effet;
	}
	else {
		$tbs_librairies[]=$gepiPath."/lib/scriptaculous.js";
	}
}
// Utilisation de windows.js
$windows = isset($utilisation_win) ? $utilisation_win : NULL;
if ($windows == 'oui') {
	$tbs_librairies[]=$gepiPath."/edt_effets/javascripts/effects.js";
	$tbs_librairies[]=$gepiPath."/edt_effets/javascripts/window.js";
	$tbs_librairies[]=$gepiPath."/edt_effets/javascripts/window_effects.js";
	$tbs_CSS[]=array("fichier"=> $gepiPath."/edt_effets/themes/default.css" , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"all" , "title"=>"");
	$tbs_CSS[]=array("fichier"=> $gepiPath."/edt_effets/themes/alphacube.css" , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"all" , "title"=>"");
}

// Utilisation de tablekit
$tablekit = isset($utilisation_tablekit) ? $utilisation_tablekit : NULL;
if ($tablekit == "ok") {
	$tbs_librairies[]=$gepiPath."/lib/tablekit.js";
}

if(isset($avec_js_et_css_edt)) {
	include("../edt_organisation/fonctions_edt.php");
	prendre_en_compte_js_et_css_edt();
}

if(isset($javascript_specifique)) {
	// Il faudrait filtrer le contenu de la variable...
	// On ajoute le ".js" automatiquement et on exclus les "." qui pourrait permettre des ".." pour remonter dans l'arborescence

	if(is_array($javascript_specifique)) {
		foreach($javascript_specifique as $current_javascript_specifique) {
			if(mb_strlen(my_ereg_replace("[A-Za-z0-9_/\-]","",$current_javascript_specifique))==0) {
				// Javascript spécifique à une page:
              $tbs_librairies[]=$gepiPath."/".$current_javascript_specifique.'.js';
			}
		}
	}
	else {
		if(mb_strlen(my_ereg_replace("[A-Za-z0-9_/\-]","",$javascript_specifique))==0) {
			// Javascript spécifique à une page:
			$tbs_librairies[]=$gepiPath."/".$javascript_specifique.'.js';
		}
	}
}


// On affiche tout le temps brainjar sauf quand on dit à Gepi de ne pas le faire
$utilisation_jsdivdrag = isset($utilisation_jsdivdrag) ? $utilisation_jsdivdrag : NULL;
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
$tbs_CSS[]=array("fichier"=>$gepiPath."/css/".$style."_imprime.css"  , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"print" , "title"=>"");

// Couleur de fond des pages
if (!isset($titre_page)) $bgcouleur = "bgcolor= \"#FFFFFF\""; else $bgcouleur = "";

// Styles spécifiques
if(isset($style_specifique)) {
  // Il faudrait filtrer le contenu de la variable...
  // ne doit contenir que certains types de caractères et se terminer par .css
  // Non... on ajoute le ".css" automatiquement et on exclus les "." qui pourrait permettre des ".." pour remonter dans l'arborescence

  if(is_array($style_specifique)) {
	foreach($style_specifique as $current_style_specifique) {
	  if(mb_strlen(my_ereg_replace("[A-Za-z0-9_/]","",$current_style_specifique))==0) {
		//// Styles spécifiques à une page:
		$tbs_CSS_spe[]=array("fichier"=> $gepiPath."/".$current_style_specifique.".css" , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"all" , "title"=>"");

	  }

	}
  } else {
	if(mb_strlen(my_ereg_replace("[A-Za-z0-9_/]","",$style_specifique))==0) {
	  // Styles spécifiques à une page:
	  $tbs_CSS[]=array("fichier"=> $gepiPath."/".$style_specifique.".css" , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"all" , "title"=>"");
	}
  }
}

// vérifie si on est dans le modules absences
$files = array("gestion_absences", "select", "ajout_abs", "ajout_ret", "ajout_dip", "ajout_inf", "tableau", "impression_absences", "prof_ajout_abs", "statistiques", "alert_suivi", "admin_config_semaines", "admin_motifs_absences", "admin_horaire_ouverture", "admin_actions_absences", "admin_periodes_absences");
if(in_array(basename($_SERVER['PHP_SELF'],".php"), $files)) {
	$tbs_CSS[]=array("fichier"=> $gepiPath."/mod_absences/styles/mod_absences.css" , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"all" , "title"=>"");
}


if(isset($accessibilite)) {
	if($accessibilite=="y") {
		$tbs_CSS[]=array("fichier"=> $gepiPath."/accessibilite.css" , "rel"=>"stylesheet" , "type"=>"text/css","media"=>"screen" , "title"=>"");
 // Feuilles de styles imprimante
		$tbs_CSS[]=array("fichier"=> $gepiPath."/accessibilite_print.css" , "rel"=>"stylesheet" , "type"=>"text/css","media"=>"print" , "title"=>"");
	}
}

// Feuilles de styles du telephone
// $tbs_CSS[]=array("fichier"=> $gepiPath."/css/style_telephone.css" , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"handheld" , "title"=>"");

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
	elseif($niveau_arbo == "public") {
	   $gepiPath2="..";
	}

		$Style_CSS=array(); // initialisation du tableau de Style supplémentaire	
		
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			if (@file_exists($gepiPath2.'/style_screen_ajout_'.getSettingValue("gepiSchoolRne").'.css')) {
				$Style_CSS[]=array("fichier"=>$gepiPath."/style_screen_ajout_".getSettingValue("gepiSchoolRne").".css"  , "rel"=>"stylesheet" , "type"=>"text/css","media"=>"all" , "title"=>"");
				
			}
		} else {
			if (@file_exists($gepiPath2.'/style_screen_ajout.css')) {
				$Style_CSS[]=array("fichier"=>$gepiPath."/style_screen_ajout.css"  , "rel"=>"stylesheet" , "type"=>"text/css" , "media"=>"all" , "title"=>"");
			}
		}
	}
}else{
}

/**
 * Définition des couleurs
 */
include 'maj_coul_global.inc';

// ============================== FIN des feuilles de style =======================
// *********** sur le onload *************** //
if (isset($use_observeur) AND $use_observeur == 'ok') {
  $charger_observeur = ' observeur();';
}else{
  $charger_observeur = NULL;
}
$tbs_charger_observeur=$charger_observeur;

if (getSettingValue("impose_petit_entete_prof") == 'y' AND isset($_SESSION['statut']) AND $_SESSION['statut'] == 'professeur') {
	$_SESSION['cacher_header']="y";
}

$petit_entete="";
if(isset($_SESSION['login'])) {$petit_entete=getPref($_SESSION['login'], "petit_entete", "");}
//echo "\$petit_entete=$petit_entete<br />";
if(($petit_entete=='y')||($petit_entete=='n')) {
	$_SESSION['cacher_header']=$petit_entete;
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

	if(!isset($mode_header_reduit)) {
		$tbs_bouton_taille=$gepiPath;
	}


	$tbs_aff_temoin_check_serveur="n";
	if((getSettingAOui('aff_temoin_check_serveur'))&&($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
		// insert into setting set name='aff_temoin_check_serveur', value='y';
		$tbs_aff_temoin_check_serveur="y";
	}


	//=== Nom Prénom utilisateur ===
	if(isset($_SESSION['login'])) {
		if((!isset($_SESSION['prenom']))||(!isset($_SESSION['nom']))) {
			$sql="SELECT nom, prenom FROM utilisateurs WHERE login='".$_SESSION['login']."';";
                    
                $res_np = mysqli_query($mysqli, $sql);
                if($res_np->num_rows > 0) {
                    $lig_np=$res_np->fetch_object();
                    $_SESSION['prenom']=$lig_np->prenom;
                    $_SESSION['nom']=$lig_np->nom;
                }
                $res_np->close(); 
		}
	}
    
	if((isset($_SESSION['prenom']))||(isset($_SESSION['nom']))) {
		$tbs_nom_prenom=$_SESSION['prenom'] . " " . $_SESSION['nom'];
	}else {
		if (isset($_SESSION['statut'])) {
			$tbs_nom_prenom="NOM Prenom";
		}
		else {
			$tbs_nom_prenom="Visiteur";
		}
	}

	$tbs_nom_prenom_statut=$tbs_nom_prenom;

	//=== Dernière connexion ===
	if (isset($affiche_connexion)) {
		$tbs_last_connection=last_connection();
	}
				
//=== statut utilisateur ===
	if (isset($_SESSION['statut'])) {
		$tbs_nom_prenom_statut.=" (".$_SESSION['statut'].")";
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
			if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
				$tab_tmp_ele=get_enfants_from_resp_login($_SESSION['login'], "simple", "yy");
			}
			else {
				$tab_tmp_ele=get_enfants_from_resp_login($_SESSION['login']);
			}
			$chaine_enfants="";
			if(count($tab_tmp_ele)>0) {
				$nom_enfant=$tab_tmp_ele[1];
	//echo "\$chaine_enfants=\$tab_tmp_ele[1]=$tab_tmp_ele[1]<br />";
				$tab_tmp_info_classes=get_noms_classes_from_ele_login($tab_tmp_ele[0]);
				if(count($tab_tmp_info_classes)>0) {
					$classe_enfant=$tab_tmp_info_classes[count($tab_tmp_info_classes)-1];
				}else{
					$classe_enfant="";
				}
				$donnees_enfant[]=array("nom"=>$nom_enfant , "classe"=>$classe_enfant) ;
				for($i=3;$i<count($tab_tmp_ele);$i+=2) {
					$nom_enfant=", ".$tab_tmp_ele[$i];
	//echo "\$nom_enfant=$nom_enfant<br />";
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
	}
	else {
		$tbs_statut[]=array("classe"=>"" , "texte"=>"Visiteur");
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

	/*
	if(in_array($_SESSION['statut'], array('professeur', 'scolarite', 'cpe', 'administrateur'))) {
		$tbs_premier_menu[]=array("lien"=>"#", "alt"=>"Messagerie interne" , "title"=>"Messagerie interne" ,  "texte"=>affichage_temoin_messages_recus());
	}
	*/

	if ($session_gepi->current_auth_mode == "sso" && $gepiSettings['sso_display_portail'] == 'yes') {
	$tbs_premier_menu[]=array("lien"=>$gepiSettings["sso_url_portail"] , "confirme"=>"" , "image"=>$gepiPath."/images/icons/retour_sso.png" , "alt"=>"Portail" , "title"=>"Retour portail" , "texte"=>"Retour portail");
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

	if (isset($_SESSION['statut'])) {
		if ($_SESSION['statut'] == 'administrateur') {
			$tbs_deux_menu[]=array("lien"=>"http://gepi.mutualibre.org" , "onclick"=> "onclick=\"window.open(this.href, '_blank'); return false;\""  , "texte"=>"Visiter le site de GEPI");
		}else{
			if (getSettingValue("contact_admin_mailto")=='y') {
				$gepiAdminAdress=getSettingValue("gepiAdminAdress");
				$tmp_date=getdate();
				$lien="mailto:$gepiAdminAdress?Subject=Gepi&amp;body=";
				if ($tmp_date['hours']>=18) {$lien.= "Bonsoir";} else {$lien.= "Bonjour";}
				$lien.=",%0d%0a%0d%0a%0d%0a%0d%0aCordialement.";
				$tbs_deux_menu[]=array("lien"=>$lien , "onclick"=> ""  , "texte"=>"Contacter l'administrateur");
			}else{
				$tbs_deux_menu[]=array("lien"=>"$gepiPath/gestion/contacter_admin.php" , "onclick"=> "onclick=\"centrerpopup('$gepiPath/gestion/contacter_admin.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes'); return false;\""  , "texte"=>"Contacter l'administrateur");
			}
		}
	}

	$tbs_deux_menu[]=array("lien"=>"$gepiPath/gestion/info_gepi.php" , "onclick"=> "onclick=\"centrerpopup('$gepiPath/gestion/info_gepi.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes'); return false;\""  , "texte"=>"Informations générales");
	$tbs_deux_menu[]=array("lien"=>"$gepiPath/gestion/info_vie_privee.php" , "onclick"=> "onclick=\"centrerpopup('$gepiPath/gestion/info_vie_privee.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes'); return false;\""  , "texte"=>"Vie privée");
	
		 //=== Affichage de la version de Gepi ===
		 //=== Affichage de la version de Gepi ===
	if ((isset($_SESSION['statut']))&&($_SESSION['statut'] == "administrateur")) {
		$version_gepi = 'version '.$gepiVersion;
        if ($gepiSvnRev != null) {
			$version_gepi .= ' svn r'.$gepiSvnRev;
        } else {
            if ($gepiGitCommit == null) {//on va essayer de trouver les infos dans l'arborescence actuelle
				if (file_exists(dirname(__FILE__).'/../.git/HEAD')) {
				    $git_ref = file_get_contents((dirname(__FILE__).'/../.git/HEAD'));
					if ($git_ref != null) {
						if (substr($git_ref,0,4) == 'ref:') {
						    //on a une référence de branche git
							$gepiGitBranch = trim(substr($git_ref, 1+strrpos( $git_ref  , '/')));
							$gepiGitCommit = file_get_contents((dirname(__FILE__).'/../.git/refs/heads/'.$gepiGitBranch));
						} else {
							//on a un commit git dans le HEAD
						    $gepiGitCommit = $git_ref;
						}
				
					}
				}
            }
            //echo "\$gepiGitCommit=$gepiGitCommit<br />";
            if ($gepiGitCommit != null) {
                $version_gepi .= ' '.substr($gepiGitCommit, 0, 6).' '.$gepiGitBranch;
				if(!getSettingAOui('ne_pas_tester_version_via_git_log')) {
		            try {
		                @exec('cd '.dirname(__FILE__).'; git log -1 --format=format:"%ct" '.$gepiGitCommit, $output);
		                if (isset($output[0])) {
		                    $date = new DateTime('@'.$output[0]);
		                    $version_gepi .= ' '.$date->format('d/m/Y H:i');
		                }
		            } catch (Exception $e) {
		            }
				}
            }
        }
		$tbs_version_gepi = $version_gepi;
	} else {
		$tbs_version_gepi=" ";			// nécessaire pour recaler le bandeau dans tous les navigateurs
	}

	// Fin du conteneur de Header

// ----- Régis : fin des modifications du bandeau -----


// ==========> On ajoute la barre de menu <========================= //
$tbs_menu_prof= array();
$tbs_menu_admin = array();
$tbs_menu_scol = array();
$tbs_menu_cpe = array();
$tbs_menu_responsable = array();
$tbs_menu_eleve = array();
if (!isset($nobar)) { $nobar = "non"; }
if(isset($_SESSION['statut'])) {
	if (getSettingValue("utiliserMenuBarre") != "no" AND $_SESSION["statut"] == "professeur" AND $nobar != 'oui') {
		// On vérifie que l'utilisateur ne l'a pas enlevée

		if (getPref($_SESSION["login"], "utiliserMenuBarre", "yes") != "no") {
			// ne pourrait-on pas utiliser $gepiPath plutôt que construire $prefix un peu plus haut ?
			if (file_exists($prefix."edt_organisation/fonctions_calendrier.php")) {
		          /**
		           * Fonctions de calendrier
		           */
				require_once($prefix."edt_organisation/fonctions_calendrier.php");
			}elseif(file_exists("fonctions_calendrier.php")) {
		          /**
		           * Fonctions de calendrier
		           */
				require_once("./fonctions_calendrier.php");
			}
		        /**
		         * Barre de menu de enseignant
		         */
			include("header_barre_prof_template.php");
		}
	} else if ((getSettingValue("utiliserMenuBarre") != "no") AND ($_SESSION["statut"] == "administrateur") AND ($nobar != 'oui')) {

			// Il n'y a pas de préférence enregistrée pour des non_prof
			// Du coup, on récupère la valeur par défaut: 'yes'
			if (getPref($_SESSION["login"], "utiliserMenuBarre", "yes") == "yes") {
				if (file_exists($prefix."edt_organisation/fonctions_calendrier.php")) {
		          /**
		           * Fonctions de calendrier
		           */
					require_once($prefix."edt_organisation/fonctions_calendrier.php");
				} else if(file_exists("fonctions_calendrier.php")) {
		          /**
		           * Fonctions de calendrier
		           */
					require_once("./fonctions_calendrier.php");
				}
		        /**
		         * Barre de menu de administrateur
		         */
				include("header_barre_admin_template.php");
			}

	} else if ((getSettingValue("utiliserMenuBarre") != "no") AND ($_SESSION["statut"] == "scolarite") AND ($nobar != 'oui')) {

			// Il n'y a pas de préférence enregistrée pour des non_prof
			// Du coup, on récupère la valeur par défaut: 'yes'
			if (getPref($_SESSION["login"], "utiliserMenuBarre", "yes") == "yes") {
				if (file_exists($prefix."edt_organisation/fonctions_calendrier.php")) {
		          /**
		           * Fonctions de calendrier
		           */
					require_once($prefix."edt_organisation/fonctions_calendrier.php");
				} else if(file_exists("fonctions_calendrier.php")) {
		          /**
		           * Fonctions de calendrier
		           */
					require_once("./fonctions_calendrier.php");
				}
		        /**
		         * Barre de menu de scolarité
		         */
				include("header_barre_scolarite_template.php");
			}
	
	} else if ((getSettingValue("utiliserMenuBarre") != "no") AND ($_SESSION["statut"] == "cpe") AND ($nobar != 'oui')) {
			// Il n'y a pas de préférence enregistrée pour des non_prof
			// Du coup, on récupère la valeur par défaut: 'yes'
			if (getPref($_SESSION["login"], "utiliserMenuBarre", "yes") == "yes") {
				if (file_exists($prefix."edt_organisation/fonctions_calendrier.php")) {
		          /**
		           * Fonctions de calendrier
		           */
					require_once($prefix."edt_organisation/fonctions_calendrier.php");
				} else if(file_exists("fonctions_calendrier.php")) {
		          /**
		           * Fonctions de calendrier
		           */
					require_once("./fonctions_calendrier.php");
				}
		        /**
		         * Barre de menu de cpe
		         */
				include("header_barre_cpe_template.php");
			}
	} else if ((getSettingValue("utiliserMenuBarre") != "no") AND ($_SESSION["statut"] == "responsable") AND ($nobar != 'oui')) {
			// Il n'y a pas de préférence enregistrée pour des non_prof
			// Du coup, on récupère la valeur par défaut: 'yes'
			if (getPref($_SESSION["login"], "utiliserMenuBarre", "yes") == "yes") {
				if (file_exists($prefix."edt_organisation/fonctions_calendrier.php")) {
		          /**
		           * Fonctions de calendrier
		           */
					require_once($prefix."edt_organisation/fonctions_calendrier.php");
				} else if(file_exists("fonctions_calendrier.php")) {
		          /**
		           * Fonctions de calendrier
		           */
					require_once("./fonctions_calendrier.php");
				}
		        /**
		         * Barre de menu de cpe
		         */
				include("header_barre_responsable_template.php");
			}
	} else if ((getSettingValue("utiliserMenuBarre") != "no") AND ($_SESSION["statut"] == "eleve") AND ($nobar != 'oui')) {
			// Il n'y a pas de préférence enregistrée pour des non_prof
			// Du coup, on récupère la valeur par défaut: 'yes'
			if (getPref($_SESSION["login"], "utiliserMenuBarre", "yes") == "yes") {
				if (file_exists($prefix."edt_organisation/fonctions_calendrier.php")) {
		          /**
		           * Fonctions de calendrier
		           */
					require_once($prefix."edt_organisation/fonctions_calendrier.php");
				} else if(file_exists("fonctions_calendrier.php")) {
		          /**
		           * Fonctions de calendrier
		           */
					require_once("./fonctions_calendrier.php");
				}
		        /**
		         * Barre de menu de cpe
		         */
				include("header_barre_eleve_template.php");
			}
	} else {
		$tbs_menu_prof=array();
	}
}

// ==========> Fin on ajoute la barre de menu <========================= //

	$tbs_msg="" ;
	if ((isset($_GET['msg'])) or (isset($_POST['msg'])) or (isset($msg))) {
		//$msg = isset($_POST['msg']) ? unslashes($_POST['msg']) : (isset($_GET['msg']) ? unslashes($_GET['msg']) : $msg);
        $msg = isset($_POST['msg']) ? stripslashes($_POST['msg']) : (isset($_GET['msg']) ? stripslashes($_GET['msg']) : $msg);
		if ($msg != '') {
			$tbs_msg=$msg ;
		}
	}
}
// Décommenter la ligne ci -dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
//debug_var();

?>
