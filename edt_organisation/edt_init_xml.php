<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2007
 */
// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die('Vous devez demander à votre administrateur l\'autorisation de voir cette page.');
}

// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "edt_organisation/style_edt";

?>

VOIR Sandrine pour l'initialisation &agrave; partir d'un xml d'export vers STSWeb