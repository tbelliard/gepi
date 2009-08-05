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

$rss = isset($_GET["rss"]) ? $_GET["rss"] : NULL;
// Seul l'admin peut récupérer l'URI des rss des élèves
if ($rss == "y") {

	if ($_SESSION["statut"] != "administrateur") {
		Die();
	}

	$nom_fic = "eleves_gepi_rss.csv";

}else{

	$nom_fic = "base_eleves_gepi.csv";

}

$now = gmdate('D, d M Y H:i:s') . ' GMT';

header('Content-Type: text/x-csv');

header('Expires: ' . $now);

// lem9 & loic1: IE need specific headers

if (my_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {

    header('Content-Disposition: inline; filename="' . $nom_fic . '"');

    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

    header('Pragma: public');

} else {

    header('Content-Disposition: attachment; filename="' . $nom_fic . '"');

    header('Pragma: no-cache');

}


//header('Content-Type: application/octetstream');
//header('Content-Disposition: filename="' . $nom_fic . '"');
//header('Pragma: no-cache');
//header('Expires: 0');


$fd = '';
$call_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
$nb_classes = mysql_num_rows($call_classe);
$i = 0;
while ($i < $nb_classes) {
    $id_classe = mysql_result($call_classe, $i, 'id');
    $classe = mysql_result($call_classe, $i, 'classe');
    $appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login) ORDER BY nom, prenom");
    $nombre_lignes = mysql_num_rows($appel_donnees_eleves);
    $j= 0;
    while($j< $nombre_lignes) {
        $eleve_login = mysql_result($appel_donnees_eleves, $j, "login");
        $eleve_nom = mysql_result($appel_donnees_eleves, $j, "nom");
        $eleve_prenom = mysql_result($appel_donnees_eleves, $j, "prenom");
        //$fd.=$eleve_nom.";".$eleve_prenom.";".$eleve_login.";".$classe."\n";
        $eleve_elenoet = mysql_result($appel_donnees_eleves, $j, "elenoet");

        // Dispositif pour les URI des rss
        if ($rss == "y") {
        	// On récupère l'URI de cet élève
        	$uri = mysql_query("SELECT user_uri FROM rss_users WHERE user_login = '".$eleve_login."' LIMIT 1");
        	$nb_uri = mysql_num_rows($uri);
        	if ($nb_uri == 1) {
        		$eleve_uri = mysql_result($uri, "user_uri");
        		$eleve_elenoet = 'class_php/syndication.php?rne='.getSettingValue("gepiSchoolRne").'&type=cdt&uri='.$eleve_uri.'&ele_l='.$eleve_login;
        	}
        }

        $fd.=$eleve_nom.";".$eleve_prenom.";".$eleve_login.";".$classe.";".$eleve_elenoet."\n";
        $j++;
    }
    $i++;
}

echo $fd;
?>