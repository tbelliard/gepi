<?php
/*
 *
 * Copyright 2001-2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Initialisation du répertoire actuel de sauvegarde
$dirname = getSettingValue("backup_directory");
//$fileid=isset($_POST['fileid']) ? $_POST['fileid'] : (isset($_GET['fileid']) ? $_GET['fileid'] : NULL);
$fileid=isset($_GET['fileid']) ? $_GET['fileid'] : NULL;


$handle=opendir('../backup/' . $dirname);
$tab_file = array();
$n=0;
while ($file = readdir($handle)) {
    if (($file != '.') and ($file != '..') and ($file != 'remove.txt')
	//=================================
	// AJOUT: boireaus
	and ($file != 'csv')
	//=================================
    and ($file != '.htaccess') and ($file != '.htpasswd') and ($file != 'index.html')) {
        $tab_file[] = $file;
        $n++;
    }
}
closedir($handle);
arsort($tab_file);

$filepath = null;
$filename = null;

if ($n > 0) {

    $m = 0;
    foreach($tab_file as $value) {
    		if ($m == $fileid) {
    			$filepath = "../backup/".$dirname."/".$value;
    			$filename = $value;
    		}
        $m++;
    }
    clearstatcache();
}

send_file_download_headers('text/x-sql',$filename);
readfile($filepath);
?>
