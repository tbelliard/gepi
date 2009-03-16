<?php
/**
 *
 *
 * @version $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
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

// ====== SECURITE =======

if (!$_SESSION["login"]) {
    header("Location: ../logout.php?auto=2");
    die();
}

// On permet de modifier la couleur du menu pour savoir où on est
$menu = isset($menu) ? $menu : '';
$aff_saisir = $aff_suivre = $aff_envoyer = $aff_stats = $aff_exporter = $aff_parametrer = NULL;
switch($menu){
  case 'saisir':
    $aff_saisir = ' style="background-color: red;"';
    break;
  case 'suivre':
    $aff_suivre = ' style="background-color: red;"';
    break;
  case 'envoyer':
    $aff_envoyer = ' style="background-color: red;"';
    break;
  case 'stats':
    $aff_stats = ' style="background-color: red;"';
    break;
  case 'exporter':
    $aff_exporter = ' style="background-color: red;"';
    break;
  case 'parametrer':
    $aff_parametrer = ' style="background-color: red;"';
    break;
  default:
    $aff_saisir = $aff_suivre = $aff_envoyer = $aff_stats = $aff_exporter = $aff_parametrer = NULL;;
} // switch

echo '
	<ol id="essaiMenu">
    <li' . $aff_saisir . '><a href="saisir_absences.php"><img src="../images/edit16.png" alt="Saisie" /> - Saisie</a></li>
		<li' . $aff_suivre . '><a href="suivi_absences.php"><img src="../images/icons/releve.png" alt="Suivi" /> - Suivi</a></li>
		<li' . $aff_envoyer . '><a href="envoi_absences.php"><img src="../images/icons/mail.png" alt="courrier" /> - Envoi aux familles</a></li>
		<li' . $aff_stats . '><a href="stats_absences.php"><img src="../images/icons/stats.png" alt="Stats" /> - Statistiques</a></li>
		<li' . $aff_exporter . '><a href="exports_absences.php"><img src="../images/icons/absences.png" alt="Exports" /> - Exports</a></li>
		<li' . $aff_parametrer . '><a href="parametrage_absences.php"><img src="../images/icons/configure.png" alt="param&eacute;trer" /> - Param&egrave;tres</a></li>
	</ol>
	';
?>
