<?php
/*
 * $Id : $
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
    die();};

// INSERT INTO droits VALUES ('/mod_annees_anterieures/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Index données antérieures', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// Si le module n'est pas activé...
if(getSettingValue('active_annees_anterieures')!="y"){
	// A DEGAGER
	// A VOIR: Comment enregistrer une tentative d'accès illicite?

	header("Location: ../logout.php?auto=1");
	die();
}




//**************** EN-TETE *****************
$titre_page = "Données antérieures";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<div class='norme'><p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";
echo "</p></div>\n";

echo "<p>L'utilisation de ce module nécessite peut-être une autorisation particulière auprès de la CNIL.<br />
Voici pour info un extrait de mail passé sur la liste de diffusion [gepi-users]:</p>\n";
echo "<pre>c'est dans mon document
http://julien.noel.free.fr/gepi/arguments_gepi_v_1.0.3.sxw

article 4
« Durée de conservation : A l'exception de celles concernant la classe,
le groupe, la division fréquentés et des options suivies au cours de
l'année scolaire précédente qui peuvent être conservées pendant deux
années scolaires, les informations relatives à la scolarité des élèves
ainsi qu'à leur situation financière visées à l'article 3 c et d ne
doivent pas être conservées au-delà de l'année scolaire pour laquelle
elles ont été enregistrées, sauf dispositions légales contraires ; Les
informations relatives à l'identité de l'élève ainsi que de son
responsable légal visées à l'article 3 a et b ne doivent pas être
conservées au-delà du départ de l'élève de l'établissement. ». Voir
http://www.cnil.fr/index.php?id=1232 pour plus d'informations.</pre>\n";
echo "<p><br /></p>\n";


echo "<p>Au menu:</p>\n";
echo "<p>Les pages d'administration:</p>\n";
echo "<ul>\n";
echo "<li><p><a href='conservation_annee_anterieure.php'>Conservation des données (*) de l'année qui se termine</a><br />(<i>(*) autres que les AIDs</i>).</p></li>\n";
echo "<li><p><a href='archivage_aid.php'>Conservation des données des AIDs</a>.</p></li>\n";
echo "<li><p><a href='nettoyer_annee_anterieure.php'>Nettoyage des données d'élèves ayant quitté l'établissement</a>.</p></li>\n";
echo "<li><p><a href='corriger_ine.php'>Correction des INE non renseignés ou mal renseignés lors de la conservation</a>.</p></li>\n";
echo "</ul>\n";

echo "<p>Les pages de consultation:</p>\n";
echo "<ul>\n";
echo "<li><p><a href='consultation_annee_anterieure.php'>Consulter les saisies antérieures</a></p></li>\n";
echo "<li><p>Une fonction de consultation s'ouvrant en popup: popup_annee_anterieure.php?logineleve=...<br />La fonction et la page sont prêtes... reste à placer ici et là des liens vers popup_annee_anterieure.php?logineleve=... en testant si le module est activé, si le statut de l'utilisateur lui permet l'accès,...</p></li>\n";
echo "<li><p>Consultation d'un récapitulatif des avis des conseils de classe.<br />La fonction et la page sont prêtes... reste à placer ici et là des liens vers popup_annee_anterieure.php?logineleve=... en testant si le module est activé, si le statut de l'utilisateur lui permet l'accès,...</p></li>\n";
echo "</ul>\n";

/*
echo "<p>...</p>\n";
echo "<ul>\n";
echo "<li><p>A FAIRE: Une page de recherche selon divers critères: nom, prénom, année,...</p></li>\n";
echo "<li><p>...</p></li>\n";
echo "</ul>\n";
*/

require("../lib/footer.inc.php");
?>