<?php
/*
* $Id$
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

/*
// Commenté pour éviter d'envoyer des mails pour une personne restée 
// sur une page en oubliant de se déconnecter... et dont la session a expiré.
// De plus, les traitements préalables sont plus lourds
// que le malheureux echo à effectuer.

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

// Ajouter une gestion des droits par la suite
// dans la table MySQL appropriée et décommenter ce passage.
// INSERT INTO droits VALUES ('/lib/echo.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'Echo', '1');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
*/


if((isset($_GET['var']))&&($_GET['var']=='maintien_session')) {

	$debug_maintien_session="n";
	if($debug_maintien_session=="y") {
		$fich=fopen("/tmp/update_log.txt", "a+");
		fwrite($fich, strftime("%Y%m%d %H%M%S")." : Echo\n");
		fclose($fich);
	}

	$temoin_pas_d_update_session_table_log="y";
	require_once("../lib/initialisations.inc.php");

	if($debug_maintien_session=="y") {
		$fich=fopen("/tmp/update_log.txt", "a+");
		fwrite($fich, "\n");
		fclose($fich);
	}
	//echo strftime("%Y%m%d%H%M%S");
}
else {
	header('Content-Type: text/html; charset=utf-8');

	$taille=isset($_GET['taille']) ? $_GET['taille'] : 10;
	if((!is_numeric($taille))||($taille<1)) {
		$taille=10;
	}

	//echo time();
	echo "<div style='width:".$taille."px; height:".$taille."px; background-color:green;'></div>";
}
?>
