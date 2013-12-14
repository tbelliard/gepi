<?php
/*
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// INSERT INTO `droits` VALUES ('/saisie/export_cmnt_type_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Export des commentaires type des profs', '');

// Initialisations files
require_once("../lib/initialisations.inc.php");

$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
	die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// On n'autorise que les profs à accéder à cette page
if($_SESSION['statut']!='professeur') {
    header("Location: ../logout.php?auto=1");
    die();
}

$date_heure = gmdate('d-m-y-H:i:s');
$nom_fic = "Commentaires_type_".$_SESSION['login']."_".$date_heure.".txt";
//echo $nom_fic;

$now=gmdate('D, d M Y H:i:s').' GMT';

header('Content-Type: text/plain');
header('Expires: ' . $now);
// lem9 & loic1: IE need specific headers
if(my_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
	header('Content-Disposition: inline; filename="'.$nom_fic.'"');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}
else {
	header('Content-Disposition: attachment; filename="'.$nom_fic.'"');
	header('Pragma: no-cache');
}

// Initialisation du contenu du fichier:
$fd='';

$sql="SELECT * FROM commentaires_types_profs WHERE login='".$_SESSION['login']."';";
$txt=mysqli_query($GLOBALS["mysqli"], $sql);

while($lig=mysqli_fetch_object($txt)){
	$fd.=$lig->app;
	$fd.="\r\n";
}
echo $fd;

?>
