<?php
/*
 * $Id: index.php 2356 2008-09-05 14:02:27Z jjocal $
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
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

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
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

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
    die("Le module n'est pas activé.");
}

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurPeer::retrieveByPK( $_SESSION['login']);
$_SESSION['utilisateur'] = $utilisateur;

// On met le header en petit par défaut
$_SESSION['cacher_header'] = "y";
//**************** EN-TETE *****************
$titre_page = "Cahier de textes";

$style_specifique = "cahier_texte_2/calendar/calendarstyle";
$javascript_specifique = "cahier_texte_2/init_cahier_texte_2";
$utilisation_win = 'oui';
//$utilisation_scriptaculous = "ok";
//$scriptaculous_effet = "effects";

require_once("../lib/header.inc");
//**************** FIN EN-TETE *************

//-----------------------------------------------------------------------------------
echo "<div id='compte_rendu_en_cours_'></div>";
echo "<table width=\"98%\" cellspacing=0 align=\"center\" summary=\"Tableau d'entète\">\n";
echo "<tr>\n";

// **********************************************
// Affichage des différents groupes du professeur
// Récupération de toutes les infos sur le groupe
echo "<td width=\"65%\" valign='top'><br>\n";
$groups = $utilisateur->getGroupes();
if (empty($groups)) {
    echo "<br /><br />";
    echo "<b>Aucun cahier de textes n'est disponible.</b>";
    echo "<br /><br />";
}

$a = 1;
foreach($groups as $group) {
	echo "<span style=\"font-weight: bold;\">";
	echo "<a href=\"#\" onclick=\"javascript:
			id_groupe = '".$group->getId()."';
			getWinDernieresNotices().hide();
			getWinListeNotices();
			new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$group->getId()."', {encoding: 'ISO-8859-1'});
			getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=".$group->getId()."&today='+getCalendarUnixDate(), { 
	            		encoding: 'ISO-8859-1',
	            		onComplete : 
	            		function() {
	            			getWinEditionNotice().updateWidth();
						}
					}
			);
			getWinEditionNotice().updateWidth();
			return false;
    	\">";
	echo $group->getNameAvecClasses();
	echo "</a>&nbsp;</span>\n";

    if ($a == 3) {
    	$a = 1;
    } else {
		$a = $a + 1;
	}
}
echo "</td>";
// Fin Affichage des différents groupes du professeur
// **********************************************

// Deuxième cellule de la première ligne du tableau
echo "<td>\n";
echo "</td>\n";
echo "</tr>\n";
echo "<tr><td align='right' padding='10px'>\n";
echo "<button onclick=\"javascript:
						getWinDernieresNotices().show();
						getWinDernieresNotices().toFront();
						return false;
				\">Voir les dernières notices</button>\n";
echo "<button onclick=\"javascript:
						getWinDernieresNotices().setLocation(70, 200);
						getWinDernieresNotices().hide();
						getWinCalendar().setLocation(0, GetWidth() - 245);
						getWinEditionNotice().setLocation(180, 340);
						getWinListeNotices().setLocation(80, 0);
						return false;
				\">Repositionner les fenetres</button>\n";
echo "</td><td></td></tr>\n";
echo "</table>\n<hr />";
?>
