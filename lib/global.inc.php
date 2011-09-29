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
// Cette longueur est liée avec le réglage longmax_login de la table setting (voir /lib/initialisations.inc.php)
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

$style_screen_ajout = "n";

# Après installation de GEPI, si vous avez le message "Fatal error: Call to undefined function: mysql_real_escape_string() ...",
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

/* Ordre des menus
******************
Le tableau ci-dessous donne l'ordre des différents blocs du menu d'accueil.
Vous pouvez modifier ce tableau ou ajouter des lignes pour y inclure d'autres blocs correspondant aux plugins installés
Si le nom du plugin est "nom_du_plugin", ajoutez une ligne qui ressemble à :
$ordre_menus['nom_du_plugin']= 22;

Attention, chaque bloc doit avoir un numéro unique !
*/

$ordre_menus = array();
$ordre_menus['bloc_administration']= 0; // Administration
$ordre_menus['bloc_absences_vie_scol']= 1; // Gestion des retards et absences -> Vie scolaire
$ordre_menus['bloc_absences_professeur']= 2; // Gestion des retards et absences -> professeur
$ordre_menus['bloc_saisie']= 3; // Saisie (Cahier de texte - Carnet de notes - Bulletin - saisie des appréciations AID)
$ordre_menus['bloc_trombinoscope']= 4; // Trombinoscope
$ordre_menus['bloc_releve_notes']= 5; // Relevés de notes
$ordre_menus['bloc_releve_ects'] = 6; // Outils de relevé ECTS
$ordre_menus['bloc_emploi_du_temps'] = 7; // Emploi du temps
$ordre_menus['bloc_responsable'] = 8; // Accès des responsables à : Cahier de textes - Relevés de notes - Equipes pédagogiques - Bulletins simplifiés - Graphiques - absences
$ordre_menus['bloc_outil_comp_gestion_aid'] = 9; // Outils complémentaires de gestion des AID
$ordre_menus['bloc_gestion_bulletins_scolaires'] = 10; // Bulletins scolaires
$ordre_menus['bloc_visulation_impression'] = 11; // Visualisation et Impression
$ordre_menus['bloc_notanet_fiches_brevet'] = 12; // Notanet - Fiche Brevet
$ordre_menus['bloc_annees_antérieures'] = 13; // Années antérieures
$ordre_menus['bloc_panneau_affichage'] = 14; // Panneau d'affichage
$ordre_menus['bloc_module_inscriptions'] = 15; // Module "inscriptions"
$ordre_menus['bloc_module_discipline'] = 16; // Module "Discipline"
$ordre_menus['bloc_modeles_Open_Office'] = 17; // Modèles Open Office
$ordre_menus['bloc_Genese_classes'] = 18; // Génèse des classes
$ordre_menus['bloc_navigation'] = 19; // Navigation
$ordre_menus['bloc_epreuve_blanche'] = 20; // Epreuve blanche
$ordre_menus['bloc_examen_blanc'] = 21; // Examen blanc
$ordre_menus['bloc_admissions_post_bac'] = 22; // Module Admissions Post-Bac
$ordre_menus['bloc_Gestionnaire_aid'] = 23 ;// Module Gestionnaire d'AID


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

// Notice privee
$color_fond_notices["p"] = "#f6f3a8";
$couleur_entete_fond["p"] = "#f6f3a8";
$couleur_cellule["p"]="#f6f3a8";
$couleur_cellule_alt["p"] = "#f6f3a8";

// Travaux à faire "futurs"
$color_fond_notices["f"] = "#FFFF80";
$couleur_cellule["f"] = "#FFFFDF";

$color_police_travaux = "#FF4444";
$color_police_matieres = "green";
$couleur_bord_tableau_notice = "#6F6968";
$couleur_cellule_gen = "#F6F7EF";

// Certaines couleurs sont outrepassées par le contenu du fichier /cahier_texte.css
// Il faudra modifier le dispositif pour pouvoir ré-écrire le contenu de /cahier_texte.css
// d'après le contenu de /lib/global.inc

##################################################################################
#                                                                                #
#               Paramètres de configuration de GEPI (partie II)                  #
#  Normalement, vous ne devriez pas avoir à modifier les paramètres ci-dessous   #
#                                                                                #
##################################################################################

// Version de GEPI
$gepiVersion = "1.5.5"; // Attention : cette ligne doit conserver une valeur respectant ce schéma : trunk/1.5.1/1.5.2, etc. avec des guillemets doubles.
$gepiVersionMaintenance = ""; // 1, 2, 3, etc. N'est plus utilisé actuellement, remplacé plutôt par le numéro de révision SVN.
$gepiRcVersion = "";
$gepiBetaVersion = "";

// Cette variable est utilisée par le système de packaging
// automatique du site web de Gepi. Elle n'est vraiment utile que pour les
// branches stables, pas pour le trunk, qui correspond de toute façon à la
// version la plus récente.
$gepiSvnRev="8383";


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
    "Stéphane Boireau" => "stephane.boireau@ac-rouen.fr",
    "Julien Jocal" => "collegerb@free.fr"
);

// Affichage des dates en français
// Valable pour version php >= 4.3.0
if (!@setlocale(LC_TIME,'fr_FR.ISO8859-1','French','france','fra','french','FR','fr_FR','fr_FR@euro','fr-utf-8','fr_FR.utf-8', 'French_France.1252'))
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
   setlocale(LC_TIME,'fr_FR.utf-8');
   setlocale(LC_TIME,'French_France.1252');
}

$gepiShowGenTime = "no"; // Pour afficher le temps de génération de certaines pages.
$pageload_starttime = microtime(true);

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

# Make sure notice errors are not reported
//error_reporting (E_ALL ^ E_NOTICE);

//=============================
// SAISIE DE COMMENTAIRES-TYPES
// Pour permettre la saisie de commentaires-type, renseigner à 'y' la variable $commentaires_types dans /lib/global.inc
//$commentaires_types="y";
// Ce n'est plus utilisé. Des valeurs dans la table setting remplacent maintenant ce paramètre: CommentairesTypesScol et CommentairesTypesPP

//=============================
// Le fichier style_screen_ajout.css est destiné à recevoir des paramètres définis depuis la page /gestion/param_couleurs.php
// Chargé juste avant la section <body> dans le /lib/header.inc,
// ses propriétés écrasent les propriétés définies auparavant dans le </head>.
// Une sécurité... il suffit de passer la variable $style_screen_ajout à 'n' pour désactiver le fichier CSS style_screen_ajout.css et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
$style_screen_ajout='y';

//=============================
// Un dispositif de filtrage des balises HTML soumises a été mis en place.
// Sans ce dispositif, il est possible de déposer un code sournois pour provoquer des dégats lors de la visite par l'utilisateur auquel le piège est destiné.
// Il est très fortement recommandé de ne pas passer la variable ci-dessous à 'n'
//$filtrage_html="y";
//$classe_filtrage='HTMLPurifier'; // HTMLPurifier fonctionne à partir de php5.0.5 et est plus complet que InputFilter
// Ou:
//$classe_filtrage='InputFilter'; // InputFilter fonctionne avec php4/php5

// $filtrage_html est maintenant paramétré dans gestion/security_policy.php
// Et prend les valeurs inputfilter, htmlpurifier ou pas_de_filtrage_html

// Liste des balises autorisées pour InputFilter:
$aAllowedTags = array("a", "b", "blink", "blockquote", "br", "caption", "center", "col", "colgroup", "comment", "em", "font", "h1", "h2", "h3", "h4", "h5", "h6", "hr", "i", "img", "li", "marquee", "ol", "p", "pre", "s", "small", "span", "strike", "strong", "sub", "sup", "table", "tbody", "td", "tfoot", "th", "thead", "tr", "tt", "u", "ul", "color");
// N'ajoutez pas de balises sans y réfléchir à deux fois... ou demander avis sur la liste gepi-users
// Liste des attributs autorisés:
$aAllowedAttr = array("abbr", "align", "alt", "axis", "background", "behavior", "bgcolor", "border", "bordercolor", "bordercolordark", "bordercolorlight", "bottompadding", "cellpadding", "cellspacing", "char", "charoff", "cite", "clear", "color", "cols", "direction", "face", "font-weight", "headers", "height", "href", "hspace", "leftpadding", "loop", "noshade", "nowrap", "point-size", "rel", "rev", "rightpadding", "rowspan", "rules", "scope", "scrollamount", "scrolldelay", "size", "span", "src", "start", "summary", "target", "title", "toppadding", "type", "valign", "value", "vspace", "width", "wrap", "style");
//$aAllowedAttr = array("abbr", "align", "alt", "axis", "background", "behavior", "bgcolor", "border", "bordercolor", "bordercolordark", "bordercolorlight", "bottompadding", "cellpadding", "cellspacing", "char", "charoff", "cite", "clear", "color", "cols", "direction", "face", "font-weight", "headers", "height", "hspace", "leftpadding", "loop", "noshade", "nowrap", "point-size", "rel", "rev", "rightpadding", "rowspan", "rules", "scope", "scrollamount", "scrolldelay", "size", "span", "start", "summary", "target", "title", "toppadding", "type", "valign", "value", "vspace", "width", "wrap", "style");
//=============================

?>
