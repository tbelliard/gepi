<?php
/* $Id$
*
* Copyright 2001, 2008 Thomas Belliard
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

$niveau_arbo = 0;

// Initialisations files
require_once("./lib/initialisations.inc.php");

# Cette page a pour vocation de gérer les authentification SSO.
# Si l'authentification SSO n'est pas paramétrée, on renvoie tout de suite
# vers la page de login classique.

if (!$session_gepi->auth_sso) {
	session_write_close();
	header("Location:login.php");
	die();
}

# L'instance de Session permettant de gérer directement les authentifications
# SSO, on ne s'embête pas :

$auth = $session_gepi->authenticate();
if ($auth == "1") {
	# Authentification réussie
	session_write_close();
	header("Location:accueil.php");
	die();
} else {
	# Echec d'authentification.
	session_write_close();
	header("Location:login_failure.php?error=".$auth."&mode=sso");
	die();
}
?>