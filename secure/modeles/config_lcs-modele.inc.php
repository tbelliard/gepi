<?php
/*
*/
##############################################################
# Parametres propres a une authentification sur un serveur LCS
##############################################################
#---
# Ce fichier doit être renommé en config_lcs.inc.php dans le repertoire secure pour pouvoir être
# pris en compte !
#---

/// Page d'authentification LCS
define('LCS_PAGE_AUTHENTIF',"../../lcs/auth.php");
// Page de la librairie ldap
define('LCS_PAGE_LDAP_INC_PHP',"/var/www/Annu/includes/ldap.inc.php");
// Realise la connexion a la base d'authentification du LCS et include des fonctions de lcs/includes/functions.inc.php
define('LCS_PAGE_AUTH_INC_PHP',"/var/www/lcs/includes/headerauth.inc.php");

// adresse du serveur LDAP du SE3 (necessaire pour l'importation initiale de la base)
//$lcs_ldap_host = 'ldap.example.com';
// le port usuel du serveur
//$lcs_ldap_port = '389';
// la base DN
//$lcs_ldap_base_dn = "dc=etab,dc=academie,dc=fr";

// On recupere desormais ces informations dans la config du LCS:
require_once ('/var/www/lcs/includes/config.inc.php'); 
// adresse du serveur LDAP du SE3 (necessaire pour l'importation initiale de la base)
$lcs_ldap_host = $ldap_server;
// le port usuel du serveur
$lcs_ldap_port = $ldap_port;
// la base DN
$lcs_ldap_base_dn = $ldap_base_dn;
?>
