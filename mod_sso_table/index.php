<?php
/*
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Initialisations files
$AllowedFilesExtensions = array("csv");
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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
//creation de la table de correspondance
$sql="CREATE TABLE IF NOT EXISTS `sso_table_correspondance` ( `login_gepi` varchar(100) NOT NULL
                default '', `login_sso` varchar(100) NOT NULL
                default '', PRIMARY KEY (`login_gepi`) )
                 ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$test=mysql_query($sql);

// **************** EN-TETE *****************
$titre_page = "Gestion de la table de correspondance SSO";
$style_specifique = "mod_sso_table/css/sso_table";
$utilisation_tablekit= "ok";
require_once("../lib/header.inc.php");
// **************** FIN EN-TETE *****************


$root = dirname(__FILE__) . DIRECTORY_SEPARATOR ;
set_include_path('.' .
    PATH_SEPARATOR . $root . 'lib' . DIRECTORY_SEPARATOR .
    PATH_SEPARATOR . $root . 'apps' . DIRECTORY_SEPARATOR .
    PATH_SEPARATOR . $root . 'apps/modeles' . DIRECTORY_SEPARATOR .
    PATH_SEPARATOR . $root . 'apps/classes' . DIRECTORY_SEPARATOR .
    PATH_SEPARATOR . $root . 'apps/vues' . DIRECTORY_SEPARATOR .
    PATH_SEPARATOR . get_include_path());

require_once('Frontal.php');

$front_controleur = new Frontal();
try {
    $front_controleur=Frontal::getInstance()->execute();
    
}
catch(Exception $e) {
    echo "Exception levée dans l'application. <br />"
     . "<strong>Message</strong> " . $e->getMessage() . "<br />"
     . "<strong>Fichier</strong> " . $e->getFile() . "<br />"
     . "<strong>Ligne</strong> " . $e->getLine() . "<br />";
}
 
?>
