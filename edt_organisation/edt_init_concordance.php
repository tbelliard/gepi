<?php

/**
 * @version $Id$
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

// On initialise les variables
$etape = isset($_POST["etape"]) ? $_POST["etape"] : NULL;
$nbre_ligne = isset($_POST["nbre_ligne"]) ? $_POST["nbre_ligne"] : NULL;
$values = '';
//$ = isset($_POST[""]) ? $_POST[""] : NULL;
echo $etape;
if ($etape != NULL) {
	// On réceptionne les données et on les rentre dans la base
	for($a = 0; $a < $nbre_ligne; $a++){

		$nom_gepi[$a] = isset($_POST["nom_gepi_".$a]) ? $_POST["nom_gepi_".$a] : NULL;
		$numero_texte[$a] = isset($_POST["numero_texte_".$a]) ? $_POST["numero_texte_".$a] : NULL;
		// On prépare la requête
		if ($nom_gepi[$a] != '') {
			$values .= "('', '".$etape."', '".$numero_texte[$a]."', '".$nom_gepi[$a]."'), ";

		}
	}
	// On envoie toutes les requêtes d'un coup
	echo $values;
	$envoie = mysql_query("INSERT INTO edt_init (id_init, ident_export, nom_export, nom_gepi)
				VALUE ".$values." ('', ".$etape.", 'fin', 'fin')") OR DIE ('Erreur dans la requête $envoie de l\'étape '.$etape.' : '.mysql_error().'<br />'.$envoie);

	// si l'envoi est une réussite alors on pass à l'étape 2
	if ($envoie) {
		$prochaine_etape = $etape + 1;
		$vers_etape2 = mysql_query("UPDATE edt_init SET nom_export = '".$prochaine_etape."' WHERE ident_export = 'fichierTexte'");
		echo '
		<h3>L\'opération a réussi</h3>';
		// Certaines étapes ne donnent lieu à aucun enregistrement
		if ($etape != 4) {
			echo '
			<p>Il y a eu '.$nbre_ligne.' enregistrements dans la base</p>';
		}else{
			echo '
			<p>Il n\'y a eu aucun enregistrement dans la base</p>';
		}
		echo '
		<a href="./edt_init_texte.php">Revenez en arrière et recommencer la même opération pour l\'étape '.$prochaine_etape.'.</a>';
	}

}


?>
