<?php

/**
 * EdT Gepi : le menu pour les includes require_once().
 *
 * @version $Id:  $
 *
 * Copyright 2011 Pascal Fautrero
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// Sécurité : éviter que quelqu'un appelle ce fichier seul
$serveur_script = $_SERVER["SCRIPT_NAME"];
$analyse = explode("/", $serveur_script);
	if ($analyse[3] == "mini_calendrier.php") {
		die();
	}

// ========================= Récupérer le bon fichier de langue
require_once('../edt_organisation/choix_langue.php');

?>
	<div id="agauche">
		<?php if (getSettingValue("use_only_cdt") != 'y' OR $_SESSION["statut"] != 'professeur') { ?>
		<div class="dates_header"></div>
			<div class="dates">
				<p><?php echo (WEEK_NUMBER.date("W")); ?></p>
				<p class="dates_text"><?php echo $SemaineCourante; ?></p>
				<p><?php if ($NomPeriode) echo $NomPeriode; ?></p>
				<p><?php if ($TypeSemaineCourante) echo "Semaine ".$TypeSemaineCourante; ?></p>
			</div>
        <?php } ?>
	</div>
