<?php

/**
 * Vue pour afficher les emplois du temps
 *
 * @version     $Id: view_edt_view.php 4059 2010-01-31 20:03:48Z adminpaulbert $
 * @package		GEPI
 * @subpackage	EmploisDuTemps
 * @copyright	Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Pascal Fautrero
 * @license		GNU/GPL, see COPYING.txt
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

if ( getSettingValue("autorise_edt_tous") === 'y') {
	if ($IE6) {
		echo "<div class=\"cadreInformation\">Votre navigateur (Internet Explorer 6) est obsolète et se comporte mal vis à vis de l'affichage des emplois du temps. Faites absolument une mise à jour vers les versions 7 ou 8 ou changez de navigateur (FireFox, Chrome, Opera, Safari)</div>";
	}


	// On ajoute le menu EdT
	require_once("../edt_organisation/menu.inc.php");


	echo "<br />\n";
	echo '<div id="lecorps">';

	//require_once("./menu.inc.new.php");

	// ========================= AFFICHAGE DES MESSAGES

	if ($message != "") {
		echo ("<div class=\"cadreInformation\">".$message."</div>");
	}

	// ========================= AFFICHAGE DE LA BARRE DE COMMUTATION DES PERIODES

	if (($DisplayPeriodBar) AND ($DisplayEDT)) {
			AfficheBarCommutateurPeriodes($login_edt, $visioedt, $type_edt_2);
	}

	// ========================= AFFICHAGE DE LA BARRE DE COMMUTATION DES SEMAINES

	if (($DisplayWeekBar) AND ($DisplayEDT)) {
			AfficheBarCommutateurSemaines_CDT($login_edt, $visioedt, $type_edt_2, $week_min, $_SESSION['week_selected']);
	}


	// ========================= AFFICHAGE DES EMPLOIS DU TEMPS

	if ($DisplayEDT) {
			//AfficheImprimante(true);
			//AfficheBascule(true, $login_edt, $visioedt, $type_edt_2);
			AfficherEDT_CDT($tab_data, $entetes, $creneaux, $type_edt, $login_edt, $_SESSION['period_id']);
	}

	echo '</div>';
}
?>