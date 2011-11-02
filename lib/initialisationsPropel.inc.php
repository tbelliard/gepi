<?php
/**
 * Appel des fichiers de configuration de Propel
 * @version $Id: initialisationsPropel.inc.php 7849 2011-08-20 18:19:12Z regis $
 *
 * @Copyright 2001, 2009 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stéphane Boireau, Julien Jocal
 *
 * @license GNU/GPL, 
 * @package Initialisation
 * @subpackage initialisation
 */

/*
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
$propel_conf_file_name = 'gepi-conf.php';
//$propel_conf_file_name = 'gepi-conf_debug.php';

// Pour les scripts situés à la racine de GEPI
if (isset($niveau_arbo) and ($niveau_arbo == "0")) {
   // Database configuration file
   require_once("./secure/connect.inc.php");
   //propel objects
   set_include_path("./orm/propel-build/classes" . PATH_SEPARATOR . "./orm" . PATH_SEPARATOR . get_include_path());
   require_once("propel/Propel.php");
   Propel::init('./orm/propel-build/conf/'.$propel_conf_file_name);

// Pour les scripts situés dans un sous-répertoire à l'intérieur d'une sous-répertoire de GEPI
} else if (isset($niveau_arbo) and ($niveau_arbo == "2")) {
   // Database configuration file
   require_once("../../secure/connect.inc.php");
   //propel objects
   set_include_path("../../orm/propel-build/classes" . PATH_SEPARATOR . "../../orm/propel" . PATH_SEPARATOR . "../../orm" . PATH_SEPARATOR . get_include_path());
   require_once("propel/Propel.php");
   Propel::init('../../orm/propel-build/conf/'.$propel_conf_file_name);

// Pour les scripts situés dans un sous-sous-répertoire à l'intérieur d'une sous-répertoire de GEPI
} else if (isset($niveau_arbo) and ($niveau_arbo == "3")) {
   // Database configuration file
   require_once("../../../secure/connect.inc.php");
   //propel objects
   set_include_path("../../../orm/propel-build/classes" . PATH_SEPARATOR . "../../../orm/propel" . PATH_SEPARATOR . "../../../orm" . PATH_SEPARATOR . get_include_path());
   require_once("propel/Propel.php");
   Propel::init('../../../orm/propel-build/conf/'.$propel_conf_file_name);

// Pour les scripts situés dans le sous-répertoire "public"
// Ces scripts font appel au fichier /public/secure/connect.inc et non pas /secure/connect.inc
} else if (isset($niveau_arbo) and ($niveau_arbo == "public")) {
    // Database configuration file
    require_once("../secure/connect.inc.php");
	//propel objects
    set_include_path("../orm/propel-build/classes" . PATH_SEPARATOR . "../orm/propel" . PATH_SEPARATOR . "../orm" . PATH_SEPARATOR . get_include_path());
    require_once("propel/Propel.php");
    Propel::init('../orm/propel-build/conf/'.$propel_conf_file_name);

// Pour les scripts situés dans un sous-répertoire GEPI
} else {
   // Database configuration file
   require_once("../secure/connect.inc.php");
	//propel objects
   set_include_path("../orm/propel-build/classes" . PATH_SEPARATOR . "../orm/propel" . PATH_SEPARATOR . "../orm" . PATH_SEPARATOR . get_include_path());
   require_once("../orm/propel/Propel.php");
   Propel::init('../orm/propel-build/conf/'.$propel_conf_file_name);
}
?>
