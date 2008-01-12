<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2007
 */


?>

<h1>L'emploi du temps de Gepi !</h1>

<p>Utilisez le menu &agrave; gauche.</p>

<?php
if ($_SESSION["statut"] == "administrateur") {
	echo '
		<p>
			<a href="../mod_absences/admin/admin_periodes_absences.php?action=visualiser">
			<img src="../images/icons/absences.png" alt="Créneaux" title="Réglage des créneaux" />Paramétrer les créneaux
			</a>
		</p>
	';
}
else{}


