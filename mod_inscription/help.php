<?php
/*
 * $Id: accueil_modules.php 1398 2008-01-28 20:42:10Z delineau $
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$resultat_session = resumeSession();
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
<H1>Le module Inscription</H1>
Le module Inscription permet de définir un ou plusieurs items (journée, stage, intervention, ...), au(x)quel(s) les utilisateurs pourront s'inscrire ou se désinscrire en cochant ou décochant une croix.
<ul>
<li>La configuration du module est accéssible aux administrateurs et à la scolarité.</li>
<li>L'interface d'inscription/désinscription est accessible aux professeurs, cpe, administrateurs et vie scolaire.</li>
</ul>

<p>Après avoir activé le module, les administrateurs et la vie scolaire disposent dans la page d'accueil
 d'un nouveau module de configuration.</p>
<p>La première étape consiste à configurer ce module :
<ul>
<li><b>Activation / Désactivation :</b>
<br />Tant que le module n'est pas entièrement configuré, vous avez intérêt à ne pas activer la page autorisant
les inscriptions. De cette façon, ce module reste invisible aux autres utilisateurs (professeurs et cpe).
<br />De même, lorsque les inscriptions sont closes, vous pouvez désactiver les inscriptions, tout en gardant l'accès au module de configuration.
</li>
<li><b>Liste des items :</b>
<br />C'est la liste des entités auxquelles les utilisateurs pourront s'incrire.
<br />Chaque entité est caratérisée par un identifiant numérique, une date (format AAAA/MM/JJ), une heure (20 caractères max), une description (200 caractères max).
</li>
<li><b>Titre du module :</b>
<br />Vous avez ici la possibilité de personnaliser l'intitulé du module visible dans la page d'accueil.
</li>
<li><b>Texte explicatif :</b>
<br />
Ce texte sera visible par les personnes accédant au module d'inscription/désincription.
</li>
</ul>

<?php require("../lib/footer.inc.php");?>