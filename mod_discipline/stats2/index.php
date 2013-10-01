<?php
/*
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer, Didier Blanqui
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
}

//===========================================
$sql="CREATE TABLE IF NOT EXISTS `s_categories` ( `id` INT(11) NOT NULL
                auto_increment, `categorie` varchar(50) NOT NULL
                default '',`sigle` varchar(20) NOT NULL
                default '', PRIMARY KEY (`id`) )
                 ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$test=mysql_query($sql);
$sql="SELECT 1=1 FROM `s_categories`;";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
    $categories[]=Array('categorie'=>'Travail','sigle'=>'T');
    $categories[]=Array('categorie'=>'Degradation','sigle'=>'D');
    $categories[]=Array('categorie'=>'Retards Répétés','sigle'=>'R');
    $categories[]=Array('categorie'=>'Oubli de matériel','sigle'=>'O');
    $categories[]=Array('categorie'=>'Insolence et comportement','sigle'=>'IC');
    $categories[]=Array('categorie'=>'Violence verbale ou physique','sigle'=>'V');
    $categories[]=Array('categorie'=>'Bavardages répétés','sigle'=>'B');    
    foreach($categories as $categorie) {
        $sql="INSERT INTO `s_categories`(categorie,sigle) VALUES ('".$categorie['categorie']."','".$categorie['sigle']."');";
        $test=mysql_query($sql);
    }
}

$test_champ_categorie=mysql_query("SHOW COLUMNS FROM s_incidents LIKE 'id_categorie';");
if(mysql_num_rows($test_champ_categorie)==0) {
	$sql="ALTER TABLE `s_incidents` ADD `id_categorie` INT(11) AFTER `nature`;";
	$test=mysql_query($sql);
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_discipline/stats2/index.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
    $sql="INSERT INTO droits SET id='/mod_discipline/stats2/index.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Module discipline: Statistiques',
statut='';";
    $insert=mysql_query($sql);
}

if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}
//===========================================

//**************** EN-TETE *****************

$titre_page = "Discipline: Statistiques";      
$utilisation_scriptaculous="ok";
$utilisation_tablekit="ok";
//$utilisation_win = 'oui';
$scriptaculous_effet="effects,controls,builder,dragdrop";
$style_specifique[] = "mod_discipline/stats2/apps/css/stats";
$javascript_specifique[] = "mod_discipline/stats2/apps/js/stats";

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

require_once("../../lib/header.inc.php");

//**************** FIN EN-TETE *****************

$root = dirname(__FILE__) . DIRECTORY_SEPARATOR ;
set_include_path('.' .
        PATH_SEPARATOR . $root . 'lib' . DIRECTORY_SEPARATOR .
        PATH_SEPARATOR . $root . 'apps' . DIRECTORY_SEPARATOR .
        PATH_SEPARATOR . $root . 'apps/modeles' . DIRECTORY_SEPARATOR .
        PATH_SEPARATOR . $root . 'apps/js' . DIRECTORY_SEPARATOR .
        PATH_SEPARATOR . $root . 'apps/classes' . DIRECTORY_SEPARATOR .
        PATH_SEPARATOR . $root . 'apps/vues' . DIRECTORY_SEPARATOR .
        PATH_SEPARATOR . get_include_path());

require_once('Frontal.php');
include("menu_stats.php");

try {
    $front_controleur=Frontal::getInstance()->execute();
    require("../../lib/footer.inc.php");
}
catch(Exception $e) {
    echo "Exception levée dans l'application. <br />"
            . "<b>Message</b> " . $e->getMessage() . "<br />"
            . "<b>Fichier</b> " . $e->getFile() . "<br />"
            . "<b>Ligne</b> " . $e->getLine() . "<br />";
}
?>
