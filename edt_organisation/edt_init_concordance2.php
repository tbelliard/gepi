<?php

/**
 * Fichier qui enregistre les concordances et les cours du fichier edt_init_csv2.php
 *
 * @version $Id$
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau, Julien Jocal
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
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Initialisation des variables
$etape = isset($_POST["etape"]) ? $_POST["etape"] : NULL;
$concord_csv2 = isset($_POST["concord_csv2"]) ? $_POST["concord_csv2"] : NULL;
$nbre_lignes = isset($_POST["nbre_lignes"]) ? $_POST["nbre_lignes"] : NULL;
//$ = isset($_POST[""]) ? $_POST[""] : NULL;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
	<title>Enregistrer les concordances(2) pour l'import de l'EdT</title>
</head>
<body>
<?php
// traitement des données qui arrivent

if ($etape != NULL) {
	// Alors on peut commencer le traitement

	echo '<p>Etape numéro : '.$etape.'</p>';
	echo '<p>Nbre lignes : '.$nbre_lignes.'</p>';
	if ($etape != 12) {

	}elseif($etape == 12){
		for($i = 0; $i < $nbre_lignes; $i++){
			// On initialisise toutes les variables et on affiche la valeur de chaque cours
			$ligne = isset($_POST["ligne_".$i]) ? $_POST["ligne_".$i] : NULL;
			echo $ligne.'<br />';
		}
	}
	// On affiche un lien pour revenir à la page de départ
	echo '
	<a href="edt_init_csv2.php">Retour</a>';

	// On incrément le numéro de l'étape
	$prochaine_etape = $etape + 1;
	$vers_etape2 = mysql_query("UPDATE edt_init SET nom_export = '".$prochaine_etape."' WHERE ident_export = 'fichierTexte2'");

}

?>
</body>
</html>