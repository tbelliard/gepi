<?php

/**
 * Fichier de gestion de l'emploi du temps dans Gepi version 1.5.x
 *
 * index_edt.php
 * @copyright 2007
 */

$titre_page = "Emploi du temps";
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

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die('Vous devez demander à votre administrateur l\'autorisation de voir cette page.');
}
// CSS particulier à l'EdT
$style_specifique = "edt_organisation/style_edt";

// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php");

// Pour revenir proprement, on crée le $_SESSION["retouredt"]
$_SESSION["retour"] = "edt";
?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php include($page_inc_edt); ?>

	</div>
<br />
<br />
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>
