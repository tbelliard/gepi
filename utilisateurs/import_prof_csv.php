<?php

/*

 * Last modification  : 04/04/2005

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

};



include "../lib/periodes.inc.php";


if (!checkAccess()) {

    header("Location: ../logout.php?auto=1");

die();

}



$nom_fic = "base_professeurs_gepi.csv";

header('Content-Type: application/octetstream');

header('Content-Disposition: filename="' . $nom_fic . '"');

header('Pragma: no-cache');

header('Expires: 0');



$fd = '';



//$appel_donnees = mysql_query("SELECT * FROM utilisateurs ORDER BY nom, prenom");
$appel_donnees = mysql_query("SELECT * FROM utilisateurs WHERE statut='professeur' ORDER BY nom, prenom");

$nombre_lignes = mysql_num_rows($appel_donnees);

$j= 0;

while($j< $nombre_lignes) {

    $prof_login = mysql_result($appel_donnees, $j, "login");

    $prof_nom = mysql_result($appel_donnees, $j, "nom");

    $prof_prenom = mysql_result($appel_donnees, $j, "prenom");

    $fd.=$prof_nom.";".$prof_prenom.";".$prof_login."\n";

    $j++;

}

echo $fd;

?>
