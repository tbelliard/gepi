<?php

/*
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

//**************** EN-TETE *****************
$titre_page = "Discipline: Aide";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

//debug_var();

echo "<p style='margin-bottom:1em;'>Cette page est destinée à fournir quelques explications sur le module Discipline.</p>

<p style='margin-bottom:1em;'>Un lien sur le site officiel pour commencer (*)&nbsp;: 
<a href='http://www.sylogix.org/projects/gepi/wiki/GuideAdministrateur#Gestion-des-sanctions-et-punitions-module-Discipline' target=\"_blank\">Documentation en ligne</a></p>

<p style='margin-bottom:1em;'>Si vous avez activé le module OpenOffice/libreOffice, vous pouvez produire des documents au format openDocument dans Gepi.<br />
Pour la production de rapports d'incidents, la documentation est <a href='http://www.sylogix.org/projects/gepi/wiki/Mod_discipline_OOo_rapport_incident' class='bold'>Ici</a></p>

<p><em>NOTES&nbsp;:</em></p>
<ul>
	<li><p>(*) Si vous souhaitez contribuer, que ce soit pour la documentation ou l'amélioration fonctionnelle et ergonomique de Gepi, vous pouvez <a href='http://lists.sylogix.net/mailman/listinfo/gepi-users'>vous inscrire</a> ou <a href='http://www.mail-archive.com/gepi-users@lists.sylogix.net/'>consulter liste de diffusion officielle</a>.</p></li>
</ul>";

require("../lib/footer.inc.php");
?>
