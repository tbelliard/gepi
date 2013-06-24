<?php
/*
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
//require_once("../lib/initialisations.inc.php");
$niveau_arbo = 0;
require_once("./lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	//header("Location: ../logout.php?auto=1");
	header("Location: ./logout.php?auto=1");
	die();
} else if ($resultat_session == '0') {
	//header("Location: ../logout.php?auto=1");
	header("Location: ./logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/a_lire.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/a_lire.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='V',
autre='V',
description='A lire...',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
	//header("Location: ../logout.php?auto=1");
	header("Location: ./logout.php?auto=1");
	die();
}

//**************** EN-TETE *****************
$titre_page = "A lire...";
require_once("./lib/header.inc.php");
//**************** FIN EN-TETE *************

echo "<div class='norme'><p class='bold'>\n";
echo "<a href=\"./accueil.php\">Accueil</a>";
echo "</p>\n";
echo "</div>\n";

echo "<p>Voici quelques liens vers des fichiers d'information à propos de Gepi&nbsp;:</p>
<ul>
	<li><a href='a_lire.php?fichier=INSTALL.txt#affichage_fichier'>INSTALL.txt</a>&nbsp;: Les explications concernant l'installation de Gepi<br />
	(<em>si vous êtes ici, l'installation est probablement déjà faite;</em>)</li>
	<li><a href='a_lire.php?fichier=MAJ.TXT#affichage_fichier'>MAJ.TXT</a>&nbsp;: Les explications concernant la mise à jour vers la présente version de Gepi<br />
	(<em>c'est le fichier que la personne qui a mis à jour le Gepi dans la présente version a assurément lu</em>)</li>
	<li><a href='a_lire.php?fichier=README.txt#affichage_fichier'>README.txt</a>&nbsp;: À lire... comme son nom l'indique.</li>
	<li><a href='a_lire.php?fichier=changelog.txt#affichage_fichier'>changelog.txt</a>&nbsp;: L'historique des modifications et ajouts au fil des versions de Gepi.</li>
	<li><a href='a_lire.php?fichier=COPYING.txt#affichage_fichier'>COPYING.txt</a>&nbsp;: La licence GPL.</li>
</ul>

<p>Voici par ailleurs les adresses de quelques ressources concernant Gepi&nbsp;:</p>
<ul>
	<li><a href='http://www.sylogix.org/projects/gepi/wiki'>La documentation officielle de Gepi</a></li>
	<li><a href='http://lists.sylogix.net/mailman/listinfo/gepi-users'>S'inscrire à la liste de diffusion Gepi</a></li>
	<li><a href='http://www.mail-archive.com/gepi-users@lists.sylogix.net/'>Les archives de la liste de diffusion Gepi</a><br />
	(<em>pour rechercher si la question que vous vous posez a déjà trouvé une réponse</em>)</li>
</ul>\n";

echo "<br />
<a name='affichage_fichier'></a>\n";
if (isset($_GET['fichier']) && is_file($_GET['fichier'])) {
	$pathinfo_fichier=pathinfo($_GET['fichier']);
	// on affiche que les fichiers .txt du dossier courant
	if (is_file($pathinfo_fichier['basename']) && isset($pathinfo_fichier['extension']) && strtolower($pathinfo_fichier['extension'])=="txt") {
		echo "<div style=\"margin-left: 3%; margin-right: 3%;\">";
		echo "<hr style=\"margin: 0;\"/>";
		echo "Fichier ".$pathinfo_fichier['basename'];
		echo "<hr style=\"margin: 0;\"/>";
		echo "<br />";
		echo "<div style=\"padding: 2%; font-size: small;  color: black; background-color:white; border-style: solid; border-color: black; border-width: 1px;\">";
		$f=fopen($pathinfo_fichier['basename'],"r");
		while(!feof($f)) {
			echo htmlspecialchars(fgets($f))."<br />";
			}
		fclose($f);
		echo "</div>";
		echo "<br />";
		echo "<hr style=\"margin: 0;\"/>";
		echo "<a href=\"#haut_de_page\">Retour en haut de page</a>";
		echo "<hr style=\"margin: 0;\"/>";
		echo "</div>";
	}
}
require("lib/footer.inc.php");
?>
