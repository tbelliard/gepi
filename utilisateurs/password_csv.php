<?php
/*
 * Last modification  : 29/11/2006
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
 * but WITHOUT ANY WARRANTY; without even the  warranty of
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
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};


//INSERT INTO droits VALUES ('/utilisateurs/password_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Export des identifiants et mots de passe', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!isset($_SESSION['donnees_export_csv_password'])) { $MargeHaut = false ; } else {$donnees_personne_csv =  $_SESSION['donnees_export_csv_password'];}

$date_heure = gmdate('d-m-y-H:i:s');

$nom_fic = "export_csv_password_".$date_heure . ".csv";


$now = gmdate('D, d M Y H:i:s') . ' GMT';
header('Content-Type: text/x-csv');
header('Expires: ' . $now);
// lem9 & loic1: IE need specific headers
if (ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
    header('Content-Disposition: inline; filename="' . $nom_fic . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
} else {
    header('Content-Disposition: attachment; filename="' . $nom_fic . '"');
    header('Pragma: no-cache');
}

$fd = '';

$fd.="CLASSE;LOGIN;NOM;PRENOM;PASSWORD;EMAIL\n";

$nb_enr_tableau = sizeof ($donnees_personne_csv['login']);
//echo $nb_enr_tableau;

if (($donnees_personne_csv)) {
    for ($i=0 ; $i<$nb_enr_tableau ; $i++) {
	    $classe = $donnees_personne_csv['classe'][$i];
		$login = $donnees_personne_csv['login'][$i];
		$nom = $donnees_personne_csv['nom'][$i];
		$prenom = $donnees_personne_csv['prenom'][$i];
		$password = $donnees_personne_csv['new_password'][$i];
		$email = $donnees_personne_csv['user_email'][$i];
		
		$fd.="$classe;$login;$nom;$prenom;$password;$email\n";
		}
} else {
  echo "Erreur de session";
}
echo $fd;
?>