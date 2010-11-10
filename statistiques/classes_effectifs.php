<?php
/*
 *
 * @version $Id$
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions complémentaires et/ou librairies utiles

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../logout.php?auto=1");
    die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/statistiques/classes_effectifs.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/statistiques/classes_effectifs.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Export de données des bulletins',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}


//$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

function clean_string_csv($texte) {
	// Pour remplacer les ; par ., et les " par '' et virer les retours à la ligne
	$texte=my_ereg_replace(";",".,",$texte);
	$texte=my_ereg_replace('"',"''",$texte);
	$texte=my_ereg_replace('\\\r\\\n','',$texte);
	return $texte;
}

/*
if() {
	$nom_fic = "export_classes_effectifs_".date("Ymd_His").".csv";
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
	echo $csv;
	die();
}
*/

// ===================== entete Gepi ======================================//
$titre_page = "Classes, effectifs,...";
require_once("../lib/header.inc");
// ===================== fin entete =======================================//

//debug_var();

//echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

echo "<p style='color:red'>A FAIRE...</p>\n";

$sql="SELECT DISTINCT id, classe FROM classes c, j_eleves_classes jec WHERE c.id=jec.id_classe;";
echo "$sql<br />\n";

$sql="SELECT COUNT(e.login) AS nb_filles FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='XXX' AND e.login=jec.login AND e.sexe='F';";
echo "$sql<br />\n";

$sql="SELECT COUNT(e.login) AS nb_garcons FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='XXX' AND e.login=jec.login AND e.sexe='M';";
echo "$sql<br />\n";

$sql="SELECT COUNT(e.login) AS nb_filles FROM j_eleves_classes jec, eleves e WHERE e.login=jec.login AND e.sexe='F';";
echo "$sql<br />\n";

$sql="SELECT COUNT(e.login) AS nb_garcons FROM j_eleves_classes jec, eleves e WHERE e.login=jec.login AND e.sexe='M';";
echo "$sql<br />\n";

// Pour afficher au-dessus du photocopieur:...


require_once("../lib/footer.inc.php");
?>
