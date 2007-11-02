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

?>

VOIR Sandrine pour l'initialisation &agrave; partir d'un xml d'export vers STSWeb