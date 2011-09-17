<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2007
 */


// Initialisation des variables
$choix_classe = isset($_GET["id_classe"]) ? $_GET["id_classe"] : (isset($_POST["id_classe"]) ? $_POST["id_classe"] : NULL);


echo '
	<h2>Cette fonctionnalité n\'est pas encore opérationnelle.</h2>
	<br />
	<p><a href="./bilan_absences_quotidien.php">RETOUR</a></p>

	';

?>