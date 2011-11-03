<?php

/**
 * Fichier qui gère une requête ajax et qui renvoie la bonne couleur pour la matière
 *
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

// Initialisations
require_once("../lib/initialisations.inc.php");
require_once("./choix_langue.php");
// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die(ASK_AUTHORIZATION_TO_ADMIN);
}
// Initialisation des variables
$M_couleur = isset($_GET["var1"]) ? $_GET["var1"] : NULL;
$nouvelle_couleur = isset($_GET["var2"]) ? $_GET["var2"] : "non";
$matiere = isset($M_couleur) ? mb_substr($M_couleur, 2) : NULL; // pour récupérer le nom court de la matière
$couleur = "";

// on récupère les éléments sur la matière en question
$sql = mysql_query("SELECT nom_complet FROM matieres WHERE matiere = '".$matiere."'");
$matiere_long = mysql_fetch_array($sql);
// les requêtes AJAX se font en utf8, il faut donc encoder utf8 pour être tranquille
//$aff_matiere_long = utf8_encode($matiere_long["nom_complet"]);
$aff_matiere_long = $matiere_long["nom_complet"];

// On récupère la couleur de la matière en question
$verif_couleur = GetSettingEdt($M_couleur);
	if ($verif_couleur == "") {
		$couleur = "";
	} else {
		$couleur = $verif_couleur;
	}

if ($nouvelle_couleur == "non") {
	echo '
<td>'.htmlspecialchars($aff_matiere_long).'</td>
<td>'.$matiere.'</td>
<td class="cadreCouleur'.$couleur.'">
	<form id="choixCouleur" method="get" action="">
		<select id="selectColeur" name="couleur">
			<option value="rien"class="cadre" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'none\');">'.NO_COLOR.'</option>
			<option value="blue" class="cadreCouleur1" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'1\');"></option>
			<option value="fuchsia" class="cadreCouleur2" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'2\');"></option>
			<option value="lime" class="cadreCouleur3" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'3\');"></option>
			<option value="maroon" class="cadreCouleur4" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'4\');"></option>
			<option value="purple" class="cadreCouleur5" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'5\');"></option>
			<option value="red" class="cadreCouleur6" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'6\');"></option>
			<option value="white" class="cadreCouleur7" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'7\');"></option>
			<option value="yellow" class="cadreCouleur8" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'8\');"></option>
			<option value="aqua" class="cadreCouleur9" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'9\');"></option>
			<option value="grey" class="cadreCouleur10" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'10\');"></option>
			<option value="green" class="cadreCouleur11" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'11\');"></option>
			<option value="olive" class="cadreCouleur12" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'12\');"></option>
			<option value="teal" class="cadreCouleur13" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'13\');"></option>
			<option value="14" class="cadreCouleur14" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'14\');"></option>
			<option value="15" class="cadreCouleur15" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'15\');"></option>
			<option value="16" class="cadreCouleur16" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'16\');"></option>
			<option value="17" class="cadreCouleur17" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'17\');"></option>
			<option value="18" class="cadreCouleur18" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'18\');"></option>
			<option value="19" class="cadreCouleur19" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'19\');"></option>
			<option value="20" class="cadreCouleur20" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'20\');"></option>
			<option value="21" class="cadreCouleur21" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'21\');"></option>
			<option value="22" class="cadreCouleur22" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'22\');"></option>
			<option value="23" class="cadreCouleur23" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'23\');"></option>
			<option value="24" class="cadreCouleur24" onclick="couleursEdtAjax(\''.$M_couleur.'\', \'24\');"></option>			
			</select>
	</form>
</td>
	';
} else {
	// On vérifie si le réglage existe et on le met à jour, sinon on le crée
	$sql = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = '".$M_couleur."'");
	$nbre_rep = mysql_num_rows($sql);

	if ($nbre_rep !== 0) {
		$miseajour = mysql_query("UPDATE edt_setting SET valeur = '".$nouvelle_couleur."'
													WHERE reglage = '".$M_couleur."'")
												OR DIE (IMPOSSIBLE_TO_UPDATE);
	} else {
		$create = mysql_query("INSERT INTO edt_setting (`id`, `reglage`, `valeur`)
												VALUES ('', '$M_couleur', '$nouvelle_couleur')");
	}

	echo '
<td>'.htmlspecialchars($aff_matiere_long).'</td>
<td>'.$matiere.'</td>
<td class="cadreCouleur'.$nouvelle_couleur.'">
	<p onclick="couleursEdtAjax(\''.$M_couleur.'\', \'non\');">'.MODIFY.'</p>
</td>
	';
}

?>