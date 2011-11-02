<?php
/*
 * $Id: index.php 6197 2010-12-18 20:26:12Z crob $
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
$titre_page = "Outil de visualisation";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a></p>
<center>
<p>
Vous pouvez choisir ci-dessous différents moyens de visualisation :
</p>

<!--table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5 -->
<table class="boireaus" width="700" class="bordercolor" summary="Choix de l'outil">
<tr class='lig1'>
    <td width=200><a href="eleve_classe.php"><?php echo ucfirst($gepiSettings['denomination_eleve']);?> par rapport à la classe</a></td>
    <td>Permet de comparer les résultats d'un <?php echo $gepiSettings['denomination_eleve'];?> vis à vis des résultats moyens de la classe, matière par matière, période par période.</td>
</tr>
<tr class='lig-1'>
    <td width=200><a href="eleve_eleve.php"><?php echo ucfirst($gepiSettings['denomination_eleve']);?> par rapport à un autre <?php echo $gepiSettings['denomination_eleve'];?></a></td>
    <td>Permet de comparer les résultats d'un <?php echo $gepiSettings['denomination_eleve'];?> vis à vis des résultats d'un autre <?php echo $gepiSettings['denomination_eleve'];?> (quelconque), matière par matière, période par période (permet également de comparer les résultats de l'année passée pour un redoublant).</td>
</tr>
<tr class='lig1'>
    <td width=200><a href="evol_eleve.php">Evolution d'un <?php echo $gepiSettings['denomination_eleve'];?> sur l'année</a></td>
    <td>Permet de visualiser l'évolution des résultats d'un <?php echo $gepiSettings['denomination_eleve'];?> sur l'année, matière par matière.</td>
</tr>
<tr class='lig-1'>
    <td width=200><a href="evol_eleve_classe.php">Evolution d'un <?php echo $gepiSettings['denomination_eleve'];?> et classe sur l'année</a></td>
    <td>Permet de visualiser l'évolution des résultats d'un <?php echo $gepiSettings['denomination_eleve'];?> vis à vis de l'évolution de la classe, matière par matière.</td>
</tr>
<tr class='lig1'>
    <td width=200><a href="stats_classe.php">Evolution des moyennes de classes</a></td>
    <td>Permet d'obtenir les différentes moyennes de la classe (maxi, mini, moyenne, etc.) matière par matière, avec évolution sur l'année.</td>
</tr>
<tr class='lig-1'>
    <td width=200><a href="classe_classe.php">Classe par rapport à autre classe</a></td>
    <td>Permet de comparer les résultats d'une classe vis à vis d'une autre classe, matière par matière, période par période.</td>
</tr>
<tr class='lig1'>
    <td width=200><a href="affiche_eleve.php?type_graphe=courbe"><?php echo ucfirst($gepiSettings['denomination_eleve']);?> par rapport à un <?php echo $gepiSettings['denomination_eleve'];?> ou une moyenne</a></td>
    <td><b>Graphique en courbe</b>: Permet de comparer les résultats d'un <?php echo $gepiSettings['denomination_eleve'];?>, par rapport aux moyennes min/max/classe et par rapport à un autre <?php echo $gepiSettings['denomination_eleve'];?>, matière par matière, période par période.<br />Alternativement, ce choix permet d'obtenir les courbes des 3 trimestres.</td>
</tr>
<tr class='lig-1'>
    <td width=200><a href="affiche_eleve.php?type_graphe=etoile"><?php echo ucfirst($gepiSettings['denomination_eleve']);?> par rapport à un <?php echo $gepiSettings['denomination_eleve'];?> ou une moyenne</a></td>
    <td><b>Graphique en étoile/polygone</b>: Permet de comparer les résultats d'un <?php echo $gepiSettings['denomination_eleve'];?>, par rapport aux moyennes min/max/classe et par rapport à un autre <?php echo $gepiSettings['denomination_eleve'];?>, matière par matière, période par période.<br />Alternativement, ce choix permet d'obtenir les polygones des 3 trimestres.</td>
</tr>
</table>
<p><br /></p>
</center>
<?php require("../lib/footer.inc.php");?>