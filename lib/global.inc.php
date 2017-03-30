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

// La fonction gethostbyaddr utilisée dans le script gestion_connect.php et mon_compte.php retourne le nom d'hôte correspondant à une IP ("résolution inverse").
// Chez certains hébergeurs, ou dans certaines configurations de serveurs, le temps d'exécution peut être très long.
// Une solution consiste donc à ne pas faire de gethostbyaddr sur les IP locales , c'est-à-dire qui commence par 127., 10., 192.168.
// Une solution plus radicale consiste à ne pas faire du tout de gethostbyaddr
// $active_hostbyaddr = "all" : la résolution inverse de toutes les adresses IP est activée
// $active_hostbyaddr = "no" : la résolution inverse des adresses IP est désactivée
// $active_hostbyaddr = "no_local" : la résolution inverse des adresses IP locales est désactivée
$active_hostbyaddr = "no_local";

// labels des périodes
$gepiClosedPeriodLabel = "période close";
$gepiOpenPeriodLabel = "période ouverte";

$traduction_verrouillage_periode['O']="close";
$traduction_verrouillage_periode['P']="partiellement close";
$traduction_verrouillage_periode['N']="ouverte en saisie";

$couleur_verrouillage_periode['O']="red";
$couleur_verrouillage_periode['P']="darkorange";
$couleur_verrouillage_periode['N']="green";

$explication_verrouillage_periode['O']="Lorsqu'une période est verrouillée totalement,
le remplissage et la modification du carnet de notes et du bulletin
pour la période concernée sont impossibles.
La visualisation et l'impression sont autorisées.";
$explication_verrouillage_periode['P']="Lorsqu'une période est verrouillée partiellement,
seuls le remplissage et/ou la modification de l'avis du conseil de classe sont possibles.
La visualisation et l'impression des bulletins officiels et relevés de notes sont autorisées.";
$explication_verrouillage_periode['N']="Lorsqu'une période est déverrouillée,
le remplissage de toutes les rubriques (notes, appréciations, avis) est autorisé,
la visualisation des bulletins simples est autorisée,
mais la visualisation et l'impression des bulletins officiels sont impossibles.";

// Blocage de l'authentification en Single Sign-On
// -> repasse en authentification normale
// A n'utiliser que de manière temporaire pour régler un problème !!
$block_sso = false ; // false|true

$style_screen_ajout = "n";

// Gepi est configuré de manière à bloquer temporairement le compte d'un utilisateur
// après un certain nombre de tentatives de connexion infructueuses (voir interface en ligne de gestion des connexions).
// En contrepartie, un pirate peut se servir de ce mécanisme d'auto-défense pour bloquer en permanence des comptes utilisateur ou administrateur.
// Pour faire face à cette situation d'urgence, vous pouvez forcer le débloquage des comptes administrateur
// et/ou mettre en liste noire, la ou les adresses IP incriminées.

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
$ordre_menus['bloc_listes_personnelles'] = 7; // Création de listes personnelles
$ordre_menus['bloc_emploi_du_temps'] = 8; // Emploi du temps
$ordre_menus['bloc_responsable'] = 9; // Accès des responsables à : Cahier de textes - Relevés de notes - Equipes pédagogiques - Bulletins simplifiés - Graphiques - absences
$ordre_menus['bloc_outil_comp_gestion_aid'] = 10; // Outils complémentaires de gestion des AID
$ordre_menus['bloc_gestion_bulletins_scolaires'] = 11; // Bulletins scolaires
$ordre_menus['bloc_visulation_impression'] = 12; // Visualisation et Impression
$ordre_menus['bloc_notanet_fiches_brevet'] = 13; // Notanet - Fiche Brevet
$ordre_menus['bloc_annees_antérieures'] = 14; // Années antérieures
$ordre_menus['bloc_panneau_affichage'] = 15; // Panneau d'affichage
$ordre_menus['bloc_module_inscriptions'] = 16; // Module "inscriptions"
$ordre_menus['bloc_module_discipline'] = 17; // Module "Discipline"
$ordre_menus['bloc_modeles_Open_Office'] = 18; // Modèles Open Office
$ordre_menus['bloc_Genese_classes'] = 19; // Génèse des classes
$ordre_menus['bloc_navigation'] = 20; // Navigation
$ordre_menus['bloc_epreuve_blanche'] = 21; // Epreuve blanche
$ordre_menus['bloc_examen_blanc'] = 22; // Examen blanc
$ordre_menus['bloc_admissions_post_bac'] = 23; // Module Admissions Post-Bac
$ordre_menus['bloc_Gestionnaire_aid'] = 24 ;// Module Gestionnaire d'AID


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
// les trois variables suivantes vont être remplies par un script de build avec les données svn ou git
// dans le cas ou les variables ne sont pas remplies (donc pas de script de build), on regarde dans header.inc et header_template.inc
// si on peut obtenir des informations sur la version dans le répertoire .git
$gepiVersion = "master";
$gepiSvnRev = "";
$gepiGitCommit = "";
$gepiGitBranch = "";

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
//ATTENTION, changer la locale LC_NUMERIC peut faire bugger la génération pdf
@setlocale(LC_ALL,'fr-utf-8','fr_FR.utf-8','fr_FR.utf8','fr_FR.UTF-8','fr_FR.UTF8','C');
@setlocale(LC_NUMERIC,'C');

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
$type_etablissement["ecole"] = "École";
$type_etablissement["college"] = "Collège";
$type_etablissement["lycee"] = "Lycée";
$type_etablissement["lprof"] = "Lyc. Prof.";
$type_etablissement["EREA"] = "EREA";
$type_etablissement["universite"] = "Université";
$type_etablissement["tous_niveaux"] = "Tous niveaux";
$type_etablissement["aucun"] = "";

$type_etablissement2 = array();
$type_etablissement2["public"]["ecole"] = "publique";
$type_etablissement2["public"]["college"] = "public";
$type_etablissement2["public"]["lycee"] = "public";
$type_etablissement2["public"]["lprof"] = "public";
$type_etablissement2["public"]["EREA"] = "public";
$type_etablissement2["public"]["universite"] = "public";
$type_etablissement2["public"]["tous_niveaux"] = "public";
$type_etablissement2["public"]["aucun"] = "";
$type_etablissement2["prive"]["ecole"] = "privée";
$type_etablissement2["prive"]["college"] = "privé";
$type_etablissement2["prive"]["lycee"] = "privé";
$type_etablissement2["prive"]["lprof"] = "privé";
$type_etablissement2["prive"]["EREA"] = "privé";
$type_etablissement2["public"]["universite"] = "privé";
$type_etablissement2["prive"]["tous_niveaux"] = "privé";
$type_etablissement2["prive"]["aucun"] = "";

# Visibilité des groupes dans tel ou tel module:
$tab_domaines=array('bulletins', 'cahier_notes', 'cahier_texte');
$tab_domaines_sigle=array('B', 'CN', 'CDT');
$tab_domaines_texte=array('Bulletins', 'Cahiers de Notes', 'Cahiers de Textes');

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

$message_cnil_bons_usages="* En conformité avec la CNIL, les utilisateurs s'engagent à ne faire figurer dans Gepi que des commentaires respectueux des élèves, responsables et personnels.<br />";
$message_cnil_bons_usages.="<br />";
$message_cnil_bons_usages.="Veillez donc à respecter les préconisations suivantes&nbsp;:<br />";
$message_cnil_bons_usages.="<strong>Règle n° 1 :</strong> Avoir à l'esprit, quand on renseigne ces zones commentaires, que la personne qui est concernée peut exercer son droit d'accès et lire ces commentaires !<br />";
$message_cnil_bons_usages.="<strong>Règle n° 2 :</strong> Rédiger des commentaires purement objectifs et jamais excessifs ou insultants.<br />";
$message_cnil_bons_usages.="<br />";
$message_cnil_bons_usages.="Pour plus de détails, consultez <a href='http://www.cnil.fr/la-cnil/actualite/article/article/zones-bloc-note-et-commentaires-les-bons-reflexes-pour-ne-pas-deraper/' target='_blank'>l'article de la CNIL</a>?<br /><br />";

// Icone du module Alertes
//$icone_deposer_alerte="mail.png";
$icone_deposer_alerte="module_alerte32.png";

// Liste des drapeaux/icones autorisés pour les tag CDT
$tab_drapeaux_tag_cdt=array("images/bulle_bleue.png", "images/bulle_verte.png", "images/bulle_rouge.png", "images/icons/flag.png", "images/icons/flag_green.png", "images/icons/flag_yellow.png", "images/icons/flag2.gif", "images/icons/ap.png", "images/icons/epi.png");

// Types d'AID:
$tab_type_aid=array();
$tab_type_aid[0]["nom_court"]="";
$tab_type_aid[0]["nom_complet"]="";
$tab_type_aid[0]["nom_complet_pluriel"]="";

$tab_type_aid[1]["nom_court"]="AP";
$tab_type_aid[1]["nom_complet"]="Accompagnement personnalisé";
$tab_type_aid[1]["nom_complet_pluriel"]="Accompagnements personnalisés";

$tab_type_aid[2]["nom_court"]="EPI";
$tab_type_aid[2]["nom_complet"]="Enseignement pratique interdisciplinaire";
$tab_type_aid[2]["nom_complet_pluriel"]="Enseignements pratiques interdisciplinaires";

$tab_type_aid[3]["nom_court"]="Parcours";
$tab_type_aid[3]["nom_complet"]="Parcours éducatif";
$tab_type_aid[3]["nom_complet_pluriel"]="Parcours éducatifs";

// Tableau des couleurs pas trop épouvantablement sombres:
$tab_couleurs_html=array("aliceblue",
"antiquewhite",
"aquamarine",
"azure",
"beige",
"bisque",
"blanchedalmond",
"blueviolet",
"brown",
"burlywood",
"cadetblue",
"chartreuse",
"chocolate",
"coral",
"cornflowerblue",
"cornsilk",
"crimson",
"cyan",
"darkcyan",
"darkgoldenrod",
"darkgray",
"darkgreen",
"darkkhaki",
"darkmagenta",
"darkolivegreen",
"darkorange",
"darkorchid",
"darkred",
"darksalmon",
"darkseagreen",
"darkslateblue",
"darkslategray",
"darkturquoise",
"darkviolet",
"deeppink",
"deepskyblue",
"dimgray",
"dodgerblue",
"firebrick",
"floralwhite",
"forestgreen",
"gainsboro",
"ghostwhite",
"gold",
"goldenrod",
"greenyellow",
"honeydew",
"hotpink",
"indianred",
"indigo",
"ivory",
"khaki",
"lavender",
"lavenderblush",
"lawngreen",
"lemonchiffon",
"lightblue",
"lightcoral",
"lightcyan",
"lightgoldenrodyellow",
"lightgreen",
"lightgrey",
"lightpink",
"lightsalmon",
"lightseagreen",
"lightskyblue",
"lightslategray",
"lightsteelblue",
"lightyellow",
"limegreen",
"linen",
"magenta",
"mediumaquamarine",
"mediumblue",
"mediumorchid",
"mediumpurple",
"mediumseagreen",
"mediumslateblue",
"mediumspringgreen",
"mediumturquoise",
"mediumvioletred",
"mintcream",
"mistyrose",
"moccasin",
"navajowhite",
"oldlace",
"olivedrab",
"orange",
"orangered",
"orchid",
"palegoldenrod",
"palegreen",
"paleturquoise",
"palevioletred",
"papayawhip",
"peachpuff",
"peru",
"pink",
"plum",
"powderblue",
"rosybrown",
"royalblue",
"saddlebrown",
"salmon",
"sandybrown",
"seagreen",
"seashell",
"sienna",
"silver",
"skyblue",
"slateblue",
"slategray",
"snow",
"springgreen",
"steelblue",
"tan",
"thistle",
"tomato",
"turquoise",
"violet",
"wheat",
"whitesmoke");

$tab_modalites_election_par_defaut=array();
$tab_modalites_election_par_defaut["S"]["code_modalite_elect"]="S";
$tab_modalites_election_par_defaut["O"]["code_modalite_elect"]="O";
$tab_modalites_election_par_defaut["F"]["code_modalite_elect"]="F";
$tab_modalites_election_par_defaut["N"]["code_modalite_elect"]="N";
$tab_modalites_election_par_defaut["X"]["code_modalite_elect"]="X";
$tab_modalites_election_par_defaut["L"]["code_modalite_elect"]="L";
$tab_modalites_election_par_defaut["R"]["code_modalite_elect"]="R";

$tab_modalites_election_par_defaut["S"]["libelle_court"]="TRONC COMM";
$tab_modalites_election_par_defaut["O"]["libelle_court"]="OBLIGATOIR";
$tab_modalites_election_par_defaut["F"]["libelle_court"]="FACULTATIF";
$tab_modalites_election_par_defaut["N"]["libelle_court"]="OBL OU FAC";
$tab_modalites_election_par_defaut["X"]["libelle_court"]="MESURE SPE";
$tab_modalites_election_par_defaut["L"]["libelle_court"]="AJOUT ACAD";
$tab_modalites_election_par_defaut["R"]["libelle_court"]="ENS.RELIG.";

$tab_modalites_election_par_defaut["S"]["libelle_long"]="MATIERE ENSEIGNEE EN TRONC COMMUN";
$tab_modalites_election_par_defaut["O"]["libelle_long"]="MATIERE ENSEIGNEE OPTION OBLIGATOIRE";
$tab_modalites_election_par_defaut["F"]["libelle_long"]="MATIERE ENSEIGNEE OPTION FACULTATIVE";
$tab_modalites_election_par_defaut["N"]["libelle_long"]="MATIERE ENSEIGNEE OBLIG. OU FACULTATIVE";
$tab_modalites_election_par_defaut["X"]["libelle_long"]="MESURE SPECIFIQUE";
$tab_modalites_election_par_defaut["L"]["libelle_long"]="AJOUT ACADEMIQUE AU PROGRAMME";
$tab_modalites_election_par_defaut["R"]["libelle_long"]="ENSEIGNEMENT RELIGIEUX";

?>
