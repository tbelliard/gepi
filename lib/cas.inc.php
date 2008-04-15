<?php
/*
 * @version $Id$
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


// Le package phpCAS doit etre stocké dans un sous-répertoire « CAS »
// dans un répertoire correspondant a l'include_path du php.ini (exemple : /var/lib/php)
include_once('CAS/CAS.php');

// cas.sso.php est le fichier d'informations de connexions au serveur cas
// Le fichier cas.sso.php doit etre stocké dans un sous-répertoire « CAS »
// dans un répertoire correspondant a l'include_path du php.ini (exemple : /var/lib/php)
include('CAS/cas.sso.php');

// declare le script comme un client CAS
// Le dernier argument (true par défaut) donne la possibilité à phpCAS d'ouvrir une session php.
// Si tel est le cas, l'authentification CAS n'est pas répercutée dans GEPI (et il faut se réauthentifier dans l'appli),
// car le "start_session()" de l'application (environ ligne 232 dans le fichier "session.inc.php") ne marche pas ===> la session a été ouverte par phpCAS => inexact dans gepi 1.5.x, le session_start est vers la ligne 375
// et les variables de session positionnées par la suite par gepi ne sont pas récupérables.

phpCAS::client(CAS_VERSION_2_0,$serveurSSO,$serveurSSOPort,$serveurSSORacine,false);

phpCAS::setLang('french');

// redirige vers le serveur CAS si nécessaire
phpCAS::forceAuthentication();

// A ce stade, l'utilisateur est authentifié
$user  = phpCAS::getUser();
$login = phpCAS::getUser();

?>