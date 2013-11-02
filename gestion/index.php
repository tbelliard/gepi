<?php
/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
 
 
/* ---------Variables envoyées au gabarit
*
*	$tbs_menu
*				-> classe								classe CSS
*				-> image								icone du lien
*				-> texte								texte du titre du menu
*				-> entree								entrées du menu
*							-> lien						lien vers la page
*							-> titre   				texte du lien
*							-> expli					explications
*	$niveau_arbo									Niveau dans l'arborescence
*	$titre_page										Titre de la page
*	$tbs_last_connection					Vide, pour ne pas avoir d'erreur dans le bandeau
*	$tbs_retour										Lien retour arrière
*	$tbs_ariane										Fil d'arianne
*
*
*	Variables héritées de :
*
*	header_template.inc
*	header_barre_prof_template.inc
*	footer_template.inc.php
*
 */
// Passage à mysqli
$useMysqli = TRUE;

$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");
require_once("../lib/share-trombinoscope.inc.php");

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

//**************** EN-TETE *****************
// Begin standart header
$titre_page = "Gestion générale";
$tbs_last_connection="";

// ====== Inclusion des balises head et du bandeau =====
include_once("../lib/header_template.inc.php");
//**************** FIN EN-TETE *****************

/*
<!-- <p class='bold'><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour</a></p> -->
*/
	$tbs_retour="../accueil.php"; 
	$tbs_ariane[0]=array("titre" => "accueil" , "lien"=>"../accueil.php");
	
if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la création du fil d'ariane";

	$nummenu=0;
	$tbs_menu[$nummenu]=array('classe'=>'accueil' , 'image'=>'../images/icons/securite.png' , 'texte'=>"Sécurité");
	$chemin = array();
  $titre = array();
  $expli = array();
  $ancre = array();
  
  $chemin = "gestion_connect.php";
  $titre = "Gestion des connexions";
  $expli = "Affichage des connexions en cours, activation/désactivation des connexions pour le site, protection contre les attaques forces brutes, journal des connexions, changement de mot de passe obligatoire.";
  $ancre="gestion_connect";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "security_panel.php";
  $titre = "Panneau de contrôle sécurité";
  $expli = "Visualiser les tentatives d'utilisation illégale de Gepi.";
  $ancre="security_panel";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "security_policy.php";
  $titre = "Politique de sécurité";
  $expli = "Définir les seuils d'alerte et les actions à entreprendre dans le cas de tentatives d'intrusion ou d'accès illégal à des ressources.";
  $ancre="security_policy";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "../mod_serveur/test_serveur.php";
  $titre = "Configuration serveur";
  $expli = "Voir la configuration du serveur php/Mysql pour v&eacute;rifier la compatibilit&eacute; avec Gepi.";
  $ancre="test_serveur";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);


	$nummenu=1;
	$tbs_menu[$nummenu]=array('classe'=>'accueil' , 'image'=>'../images/icons/configure.png' , 'texte'=>"Général");
	$chemin = array();
  $titre = array();
  $expli = array();
  
  $chemin = "param_gen.php";
  $titre = "Configuration générale";
  $expli = "Permet de modifier des paramètres généraux (nom de l'établissement, adresse, ...).";
  $ancre="param_gen";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "droits_acces.php";
  $titre = "Droits d'accès";
  $expli = "Modifier les droits d'accès à certaines fonctionnalités selon le statut de l'utilisateur.";
  $ancre="droits_acces";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "options_connect.php";
  $titre = "Options de connexions";
  $expli = "Gestion de la procédure automatisée de récupération de mot de passe, paramétrage du mode de connexion (autonome ou Single Sign-On), changement de mot de passe obligatoire, réglage de la durée de conservation des connexions, suppression de toutes les entrées du journal de connexion.";
  $ancre="options_connect";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "modify_impression.php";
  $titre = "Gestion de la fiche \"bienvenue\"";
  $expli = "Permet de modifier la feuille d'information à imprimer pour chaque nouvel utilisateur créé.";
  $ancre="modify_impression";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "config_prefs.php";
  //$titre = "Paramétrage de l'interface ".$gepiSettings['denomination_professeur'];
  $titre = "Paramétrage des interfaces simplifiées et complémentaires";
  $expli = "Paramétrage des items de l'interface simplifiée pour certaines pages.<br />Gestion du menu en barre horizontale.";
  $ancre="config_prefs";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);

  $chemin = "param_couleurs.php";
  $titre = "Paramétrage des couleurs";
  $expli = "Paramétrage des couleurs de fond d'écran et du dégradé d'entête.";
  $ancre="param_couleurs";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);

  $chemin = "param_ordre_item.php";
  $titre = "Paramétrage de l'ordre des menus";
  $expli = "Paramétrage de l'ordre des items dans les menus";
  $ancre="param_ordre_item";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);

	$nummenu=2;
	$tbs_menu[$nummenu]=array('classe'=>'accueil' , 'image'=>'../images/icons/database.png' , 'texte'=>"Gestion des bases de données");
	$chemin = array();
  $titre = array();
  $expli = array();  

  $chemin = "accueil_sauve.php";
  $titre = "Sauvegardes et restauration";
  $expli = "Sauvegarder la base GEPI sous la forme d'un fichier au format \"mysql\".<br />
    Restaurer des données dans la base Mysql de GEPI à partir d'un fichier.";
  $ancre="accueil_sauve";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "../utilitaires/maj.php";
  $titre = "Mise à jour de la base";
  $expli = "Permet d'effectuer une mise à jour de la base MySql après un changement de version  de GEPI.";
  $ancre="maj";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "../utilitaires/clean_tables.php";
  $titre = "Nettoyage des tables";
  $expli = "Procéder à un nettoyage des tables de la base MySql de GEPI (suppression de certains doublons et/ou lignes obsolètes ou orphelines).";
  $ancre="clean_tables";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
if(!getSettingAOui('gepi_en_production')) {
	  $chemin = "efface_base.php";
	  $titre = "Effacer la base";
	  $expli = "Permet de réinitialiser les bases en effaçant toutes les données ".$gepiSettings['denomination_eleves']." de la base.";
	  $ancre="efface_base";
	  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
}

  if ($multisite != 'y') {
	$chemin = "../mod_trombinoscopes/trombinoscopes_admin.php#purge";
	$titre = "Effacer les photos";
	$expli = "Permet d'effacer les photos des ".$gepiSettings['denomination_eleves']." qui ne sont plus dans la base.";
	$ancre="efface_photos";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  }
  
  $chemin = "gestion_temp_dir.php";
  $titre = "Gestion des dossiers temporaires";
  $expli = "Permet de contrôler le volume occupé par les dossiers temporaires (<em>utilisés notamment pour générer les fichiers tableur OpenOffice (ODS), lorsque la fonction est activée dans le module carnet de notes</em>), de supprimer ces dossiers,...";
  $ancre="gestion_temp_dir";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
if(!getSettingAOui('gepi_en_production')) {
  $chemin = "gestion_base_test.php";
  $titre = "Gestion des données de test";
  $expli = "Permet d'inserer des données de test dans la base. Ne pas utiliser sur une base de production.";
  $ancre="gestion_base_test";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
}

	$nummenu=3;
	$tbs_menu[$nummenu]=array('classe'=>'accueil' , 'image'=>'../images/icons/package.png' , 'texte'=>"Outils d'initialisation");
	$chemin = array();
  $titre = array();
  $expli = array();

	$chemin="changement_d_annee.php";
	$titre = "Changement d'année";
	$expli = "Permet d'effectuer les opérations d'archivage de fin d'année et les opérations précédent l'initialisation de la nouvelle année.";
	$ancre="chgt_annee";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);

if (LDAPServer::is_setup()) {
	
	$chemin="../init_scribe_ng/index.php";
	$titre = "Initialisation à partir de l'annuaire LDAP du serveur Eole Scribe NG";
	$expli = "Permet d'importer les données ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", matières directement depuis le serveur LDAP de Scribe NG.";
	$ancre="init_scribe_ng";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
}

if(file_exists("../secure/config_lcs.inc.php")) {
	$chemin="../init_lcs/index.php";
	$titre = "Initialisation à partir de l'annuaire LDAP du serveur LCS";
	$expli = "Permet d'importer les données ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", matières directement depuis le serveur LDAP de LCS.";
	$ancre="init_lcs";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
    
}
	
	$chemin = "../init_csv/index.php";
	$titre = "Initialisation des données à partir de fichiers CSV";
	$expli = "Permet d'importer les données ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", matières depuis des fichiers CSV, par exemple des exports depuis Sconet.";
	$ancre="init_csv";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "../init_xml2/index.php";
  $titre = "Initialisation des données à partir de fichiers XML";
  $expli = "Permet d'importer les données ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", matières depuis les exports XML de Sconet/STS.<br />
	<strong>Nouvelle procédure:</strong> Plus simple et moins gourmande en ressources que l'ancienne méthode ci-dessous.";
	$ancre="init_xml2";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);
  
  $chemin = "../init_xml/index.php";
  $titre = "Initialisation des données à partir de fichiers XML convertis en CSV";
  $expli = "Permet d'importer les données ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", matières depuis les exports XML de Sconet/STS.<br />
	<em>Les XML sont traités pour générer des fichiers CSV qui sont ensuite réclamés dans les différentes étapes de l'initialisation.</em>";
	$ancre="init_xml";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli, 'ancre'=>$ancre);


	$tbs_microtime	="";
	$tbs_pmv="";
	//require_once ("./../lib/footer_template.inc.php");
	require_once ("../lib/footer_template.inc.php");

	
//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
//debug_var();

//include('./../templates/origine/gestion_generale_template.php');
include('../templates/origine/gestion_generale_template.php');

?>
