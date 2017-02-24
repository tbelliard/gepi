<?php
/*
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
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

?>

<H2>Outils complémentaires de gestion des AIDs</H2>
<p>En activant les <b>outils complémentaires de gestion des AIDs</b>, vous avez
accès à des champs supplémentaires (attribution d'une salle, possibilité
de définir un résumé, le type de production, des mots_clés, un public
destinataire...).</p>

<p>Ces données supplémentaires sont accessibles à travers des fiches dite
"fiches projet" <a href='http://www.sylogix.org/projects/gepi/wiki/Outils_compl%C3%A9mentaires_de_gestion_des_AIDs' title="Explications supplémentaires sur le site Gepi."><img src='../images/info.png' class='icone16' alt='Info' /></a>.</p>

<p>Ces fiches sont accessibles dans GEPI à différents
types d'utilisateurs connectés (administrateur, professeur, cpe, élève ou responsable)</p>

<p>Ces fiches sont également en partie accessibles dans l'interface publique de GEPI à différents. Un paramètrage permet de déterminer les champs visibles ou non par le public.</p>

<p>Selon son statut (professeurs responsables, cpe ou élèves responsables) et lorsque l'administrateur a ouvert cette possibilité,
l'utilisateur a accès en modification à certains champs de cette fiche.</p>
<p>En plus des professeurs responsable de chaque AID, l'administrateur peut désigner des utilisateurs (professeurs ou CPE) ayant le droit de
modifier les fiches projet même lorsque l'administrateur a désactivé
cette possibilité pour les professeurs responsables.</p>
<?php require("../lib/footer.inc.php");?>
