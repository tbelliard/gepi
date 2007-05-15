<?php
#########################################################################
#                                                                       #
#               Paramètres de configuration de GEPI (partie I)          #
#                                                                       #
#########################################################################

// Configuration des mini-calendrier
// $weekstarts = 0 -> la semaine commence le dimanche
// $weekstarts = 1 -> la semaine commence le lundi
// etc.
$weekstarts = 1;

// longueur maximale autorisée d'un identifiant (attention, ceci n'est pas valable
// lors de l'import depuis GEP, mais seulement lors de la création manuelle d'un
// utilisateur
$longmax_login = "10";

// caracteres speciaux pour les mots de passe
$char_spec = "&\"'-|=}+<>,?;.:/!§%";

// labels des périodes
$gepiClosedPeriodLabel = "période close";
$gepiOpenPeriodLabel = "période ouverte";
// La fonction gethostbyaddr utilisée dans le script gestion_connect.php et mon_compte.php retourne le nom d'hôte correspondant ? une IP ("r?solution inverse").
// Chez certains hébergeurs, ou dans certaines configurations de serveurs, le temps d'exécution peut être très long.
// Une solution consiste donc à ne pas faire de gethostbyaddr sur les IP locales , c'est-à-dire qui commence par 127., 10., 192.168.
// Une solution plus radicale consiste à ne pas faire du tout de gethostbyaddr
// $active_hostbyaddr = "all" : la résolution inverse de toutes les adresses IP est activée
// $active_hostbyaddr = "no" : la résolution inverse des adresses IP est désactivée
// $active_hostbyaddr = "no_local" : la r?solution inverse des adresses IP locales est désactivée
$active_hostbyaddr = "all";


// labels des périodes
$gepiClosedPeriodLabel = "période close";
$gepiOpenPeriodLabel = "période ouverte";

// Blocage de l'authentification en Single Sign-On
// -> repasse en authentification normale
// A n'utiliser que de manière temporaire pour régler un problème !!
$block_sso = false ; // false|true


##############################################################
# Paramètres propres à une authentification sur un serveur LCS
##############################################################
// Page d'authentification LCS
define('LCS_PAGE_AUTHENTIF',"../../lcs/auth.php");
// Page de la librairie ldap
define('LCS_PAGE_LDAP_INC_PHP',"/var/www/Annu/includes/ldap.inc.php");
// R?alise la connexion ? la base d'authentification du LCS et include des fonctions de lcs/includes/functions.inc.php
define('LCS_PAGE_AUTH_INC_PHP',"/var/www/lcs/includes/headerauth.inc.php");
// adresse du serveur LDAP du SE3 (necessaire pour l'importation initiale de la base)
$lcs_ldap_host = '192.168.1.2';
// le port usuel du serveur
$lcs_ldap_port = '389';
// la base DN
$lcs_ldap_base_dn = "dc=etab,dc=ac-xxxxx,dc=fr";

# Apr?s installation de GEPI, si vous avez le message "Fatal error: Call to undefined function: mysql_real_escape_string() ...",
# votre version de PHP est inf?rieure ? 4.3.0.
# En effet, la fonction mysql_real_escape_string() est disponible ? partir de la version 4.3.0 de php.
# Vous devriez mettre ? jour votre version de php.
# Sinon, positionnez la variable suivante ? "0"; (valeur par d?faut = 1)
$use_function_mysql_real_escape_string = 1;

# Apres installation de GEPI, si vous avez le message "Fatal error: Call to undefined function: html_entity_decode() ...",
# votre version de PHP est inferieure a 4.3.0.
# En effet, la fonction html_entity_decode() est disponible a partir de la version 4.3.0 de php.
# Vous devriez mettre a jour votre version de php.
# Sinon, positionnez la variable suivante a "0"; (valeur par defaut = 1)
$use_function_html_entity_decode = 1;

// Gepi est configur?s de mani?re ? bloquer temporairement le compte d'un utilisateur
// apr?s un certain nombre de tentatives de connexion infructueuses (voir interface en ligne de gestion des connexions).
// En contrepartie, un pirate peut se servir de ce m?canisme d'auto-d?fense pour bloquer en permanence des comptes utilisateur ou administrateur.
// Pour faire face ? cette situation d'urgence, vous pouvez forcer le d?bloquage des comptes administrateur
// et/ou mettre en liste noire, la ou les adresses IP incrimin?es.

// Bloquer/débloquer les comptes administrateur en cas d'un trop grand nombre de connexions infructueuses
// deux valeurs possibles :
// "y" : (recommandé) le compte administrateur est temporairement bloqué en cas d'un trop grand nombre de connexions infructueuses.
// "n" : le compte administrateur n'est pas bloqué même en cas d'un trop grand nombre de connexions infructueuses.
// Si vous choisissez de mettre "n", veillez à choisir pour les administrateurs des mots de passes suffisamment compliqués,
// contenant à la fois des lettres et des chiffres et des caractères spéciaux.
$bloque_compte_admin = "y";

$liste_noire_ip = array();
// Liste des adresses IP qui ne peuvent pas se connecter à GEPI
// Pour mettre une adresse IP en liste noire, dans la ou les lignes suivantes remplacer 195.1.1.* par la ou les adresses à exclure et supprimez les deux premiers caractères // de la ligne
//$liste_noire_ip[] = "195.1.1.1";
//$liste_noire_ip[] = "195.1.1.2";

####################################################
#                                                  #
#   Paramètres de configuration du cahier de texte #
#                                                  #
####################################################

// Notices de type compte-rendu
$color_fond_notices["c"] = "#C7FF99";
$couleur_entete_fond["c"] = '#C7FF99';
$couleur_cellule["c"]="#E5FFCF";
$couleur_cellule_alt["c"] = "#D3FFAF";

// Notices de type  travail à faire)
$color_fond_notices["t"] = "#FFCCCF";
$couleur_entete_fond["t"] = '#FFCCCF';
$couleur_cellule["t"] ="#FFEFF0";
$couleur_cellule_alt["t"] = "#FFDFE2";

// Notice informations générales
$color_fond_notices["i"] = "#ACACFF";
$couleur_entete_fond["i"] = "#EFEFFF";
$couleur_cellule["i"]="#EFEFFF";
$couleur_cellule_alt["i"] = "#C8C8FF";

// Travaux à faire "futurs"
$color_fond_notices["f"] = "#FFFF80";
$couleur_cellule["f"] = "#FFFFDF";

$color_police_travaux = "#FF4444";
$color_police_matieres = "green";
$couleur_bord_tableau_notice = "#6F6968";
$couleur_cellule_gen = "#F6F7EF";


##################################################################################
#                                                                                #
#               Paramètres de configuration de GEPI (partie II)                  #
#  Normalement, vous ne devriez pas avoir à modifier les paramètres ci-dessous   #
#                                                                                #
##################################################################################

// Version de GEPI
$gepiVersion = "1.5.0";
$gepiRcVersion = "2";
$gepiBetaVersion = "";

// Forcer l'utilisation du module de gestion des absences au quoditien
//
// Ce paramètre sert à forcer l'utilisation du module de gestion des
// absences au quotidien, qui n'est pas encore considéré comme stable
// mais reste présent dans l'archive
// Note : ce module devrait être stabilisé pour la prochaine version de Gepi
$force_abs = true; // bool "true|false"

// Forcer l'utilisation du module de gestion des mise à jour de GEPI
//
// Ce paramètre sert à forcer l'utilisation du module de gestion des
// des mise à jour de GEPI, qui n'est pas encore considéré comme stable
// mais reste présent dans l'archive
// Note : ce module devrait être stabilisé pour la prochaine version de Gepi
$force_msj = false; // bool "true|false"

// Forcer l'utilisation du module de référencement de GEPI
//
// Ce paramètre sert à forcer l'utilisation du module d'enregistrement
// de GEPI, qui n'est pas encore considéré comme stable
// mais reste présent dans l'archive
// Note : ce module devrait être stabilisé pour la prochaine version de Gepi
$force_ref = false; // bool "true|false"

// Contacts des développeurs
$gepiAuthors = array(
    "Thomas Belliard" => "thomas.belliard@free.fr",
    "Laurent Delineau" => "laurent.delineau@ac-poitiers.fr",
    "Eric Lebrun" => "eric.lebrun@ac-poitiers.fr",
    "Stéphane Boireau" => "stephane.boireau@ac-rouen.fr"
);

// Affichage des dates en français
// Valable pour version php >= 4.3.0
if (!@setlocale(LC_TIME,'fr_FR.ISO8859-1','French','france','fra','french','FR','fr_FR','fr_FR@euro','fr-utf-8'))
{
// Valable pour version php < 4.3.0
   setlocale(LC_TIME,'fr_FR.ISO8859-1');
   setlocale(LC_TIME,'French');
   setlocale(LC_TIME,'france');
   setlocale(LC_TIME,'fra');
   setlocale(LC_TIME,'french');
   setlocale(LC_TIME,'FR');
   setlocale(LC_TIME,'fr_FR');
   setlocale(LC_TIME,'fr_FR@euro');
   setlocale(LC_TIME,'fr-utf-8');
}

$gepiShowGenTime = "no"; // Pour afficher le temps de génération de certaines pages.

// Global settings array
$gepiSettings = array();

# Prefix de la base GEPI
# Note : ceci n'est utilisé qu'à titre préventif par le module absences. Il ne s'agit pas d'une fonction
# de préfixage de toutes les tables de GEPI...
# ex : $prefix="0290542E_"
$prefix_base = "";

$type_etablissement = array();
$type_etablissement["ecole"] = "Ecole";
$type_etablissement["college"] = "Collège";
$type_etablissement["lycee"] = "Lycée";
$type_etablissement["lprof"] = "Lyc. Prof.";
$type_etablissement["EREA"] = "EREA";
$type_etablissement["tous_niveaux"] = "Tous niveaux";
$type_etablissement["aucun"] = "";

$type_etablissement2 = array();
$type_etablissement2["public"]["ecole"] = "publique";
$type_etablissement2["public"]["college"] = "public";
$type_etablissement2["public"]["lycee"] = "public";
$type_etablissement2["public"]["lprof"] = "public";
$type_etablissement2["public"]["EREA"] = "public";
$type_etablissement2["public"]["tous_niveaux"] = "public";
$type_etablissement2["public"]["aucun"] = "";
$type_etablissement2["prive"]["ecole"] = "privée";
$type_etablissement2["prive"]["college"] = "privé";
$type_etablissement2["prive"]["lycee"] = "privé";
$type_etablissement2["prive"]["lprof"] = "privé";
$type_etablissement2["prive"]["EREA"] = "privé";
$type_etablissement2["prive"]["tous_niveaux"] = "privé";
$type_etablissement2["prive"]["aucun"] = "";


# Liste des tables
$liste_tables = array(
"absences",
"absences_gep",
"aid",
"aid_appreciations",
"aid_config",
"avis_conseil_classe",
"classes",
"droits",
"eleves",
"responsables",
"etablissements",
"j_aid_eleves",
"j_aid_utilisateurs",
"j_eleves_classes",
"j_eleves_cpe",
"j_eleves_etablissements",
"j_eleves_professeurs",
"j_eleves_regime",
"j_professeurs_matieres",
"log",
"matieres",
"matieres_appreciations",
"matieres_notes",
"periodes",
"tempo2",
"temp_gep_import",
"tempo",
"utilisateurs",
"setting",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
"ct_entry",
"ct_devoirs_entry",
"ct_types_documents",
"ct_documents",
"absences_eleves",
"absences_creneaux",
"absences_motifs",
"suivi_eleve_cpe",
"groupes",
"j_eleves_groupes",
"j_groupes_classes",
"j_groupes_professeurs",
"j_groupes_matieres"
);

# Make sure notice errors are not reported
//error_reporting (E_ALL ^ E_NOTICE);

//=============================
// SAISIE DE COMMENTAIRES-TYPES
// Pour permettre la saisie de commentaires-type, renseigner à 'y' la variable $commentaires_types dans /lib/global.inc
//$commentaires_types="y";
// Ce n'est plus utilisé. Des valeurs dans la table setting remplacent maintenant ce paramètre: CommentairesTypesScol et CommentairesTypesPP
?>
