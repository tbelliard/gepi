<?php

/**
 * edt_init_textes.php est un fichier qui permet d'initialiser l'EdT par les exports de type "Charlemagne".
 * On passe par une table edt_init qui a 4 champs : id_init (auto incrémenté), identifiant, nom_gepi, nom_export
 *
 * CREATE TABLE `edt_init` (
 * `id_init` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 * `ident_export` VARCHAR( 100 ) NOT NULL ,
 * `nom_export` VARCHAR( 100 ) NOT NULL ,
 * `nom_gepi` VARCHAR( 100 ) NOT NULL
 * );
 *
 * @version $Id$
 * @copyright 2007
 */

$titre_page = "Emploi du temps - Initialisation EDT";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

/*/ Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die('Vous devez demander à votre administrateur l\'autorisation de voir cette page.');
}*/
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "edt_organisation/style_edt";
//==============PROTOTYPE===============
$utilisation_prototype = "ok";
//============fin PROTOTYPE=============
// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php");
?>

<h4 class="gepi">Initialisation des l'emploi du temps de Gepi en utilisant les exports textes du type "Charlemagne".</h4>

<p>Certains logiciels propri&eacute;taires de traitement des emplois du temps proposent des exportations en format texte.
Celles-ci doivent avoir 9 parties pour pouvoir les utiliser ici :</p>
<ul>
	<li>PROFESSEUR</li>
	<li>CLASSE</li>
	<li>GROUPE</li>
	<li>PARTIE</li>
	<li>MATIERE</li>
	<li>ETABLISSEMENT</li>
	<li>SEMAINE</li>
	<li>CONGES</li>
	<li>COURS</li>
</ul>

<p>Avant de commencer l'importation, il faut vous munir d'un tableur comme Calc de la suite OpenOffice.Org qui est
un logiciel libre tr&egrave;s performant. Pour commencer, vous devez modifier l'extension du fichier en rempla&ccedil;ant
 le .txt par .csv, puis vous devez l'ouvrir avec le tableur qui va vous demander de pr&eacute;ciser quel est le
 s&eacute;parateur. Vous devez pr&eacute;ciser qu'il s'agit de la tabulation ( -> ).</p>
 <p>Vous devez ensuite diviser le fichier en 9 parties en nommant chacune d'entre elles par son nom
 (professeur.csv, classe.csv,...) en respectant la casse (tout en minuscule).</p>

 <p>Puis, vous allez devoir faire le lien avec les informations de Gepi pour pouvoir terminer par le fichier cours.csv
  qui est le plus lourd et le plus important.</p>