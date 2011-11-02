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




if (!checkAccess()) {

    header("Location: ../logout.php?auto=1");

die();

}

//**************** EN-TETE *****************

$titre_page = "Aide en ligne";

require_once("../lib/header.inc");

//**************** FIN EN-TETE *****************

?>

<p class=bold>L'aide en ligne n'est pas disponible !</p>

<?php require("../lib/footer.inc.php");?>