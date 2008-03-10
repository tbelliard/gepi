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

// fonctions edt
require_once("./fonctions_edt.php");
require_once("./edt_init_fonctions.php");

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
$aff_infos = isset($_POST["aff_infos"]) ? $_POST["aff_infos"] : NULL;
//$ = isset($_POST[""]) ? $_POST[""] : NULL;
$msg_enreg = '';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
	<title>Enregistrer les concordances(2) pour l'import de l'EdT</title>
	<LINK REL="SHORTCUT ICON" href="/gepi_trunk/favicon.ico" />
</head>
<body>
<?php
// traitement des données qui arrivent

if ($etape != NULL) {
	// Alors on peut commencer le traitement

	echo '<p>Etape numéro : '.$etape.'</p>';
	echo '<p>Nbre lignes : '.$nbre_lignes.'</p>';

	if ($etape != 12 AND $etape != 5) {
			$values = NULL;
		// C'est le cas général pour enregistrer les concordances entre le fichier csv et Gepi
		// On réceptionne les données et on les rentre dans la base
		for($a = 0; $a < $nbre_lignes; $a++){

			$nom_gepi[$a] = isset($_POST["nom_gepi_".$a]) ? $_POST["nom_gepi_".$a] : NULL;
			$nom_export[$a] = isset($_POST["nom_export_".$a]) ? $_POST["nom_export_".$a] : NULL;

			// On prépare la requête en vérifiant qu'elle doit bien être construite
			if ($nom_gepi[$a] != '' AND $nom_gepi[$a] != 'none') {
				$values .= "('', '".$etape."', '".$nom_export[$a]."', '".$nom_gepi[$a]."'), ";

			}
		}
		// On envoie toutes les requêtes d'un coup
		echo $values;
		$envoie = mysql_query("INSERT INTO edt_init (id_init, ident_export, nom_export, nom_gepi)
					VALUE ".$values." ('', ".$etape.", 'fin', 'fin')")
					OR error_reporting('Erreur dans la requête $envoie de l\'étape '.$etape.' : '.mysql_error().'<br />'.$envoie);
		// On récupère le nombre de valeurs enregistrées et on affiche

		echo '<p>'.$nbre_lignes.' lignes ont été enregistrées dans la base.</p>';

	}elseif($etape == 5){
			$enre = $deja = 0;
		// Ce sont les salles. On va enregistrer celles qui ne sont pas encore dans Gepi
		for($a = 0; $a < $nbre_lignes; $a++){
			$nom_export[$a] = isset($_POST["nom_export_".$a]) ? $_POST["nom_export_".$a] : NULL;
			$test = testerSalleCsv2($nom_export[$a]);
			if ($test == "enregistree") {
				$enre++;
			}elseif($test == "ok"){
				$deja++;
			}
		}
		echo '
		<p>'.$enre.' nouvelles salles ont été enregistrées et '.$deja.' existaient déjà.</p>';

	}elseif($etape == 12){

		// Ce sont les cours qui arrivent, car on a terminé les concordances
		for($i = 0; $i < $nbre_lignes; $i++){
			// On initialise toutes les variables et on affiche la valeur de chaque cours
			$ligne = isset($_POST["ligne_".$i]) ? $_POST["ligne_".$i] : NULL;
			//echo $ligne.'<br />';
			// On explose la variable pour récupérer toutes les données
			$tab = explode("|", $ligne);
			// Toutes les infos sont envoyées en brut
			for($v = 0; $v < 12; $v++){
				if (!isset($tab[$v])) {
					$tab[$v] = '';
				}
			}
			$enregistre = enregistreCoursCsv2($tab[0], $tab[1], $tab[2], $tab[3], $tab[4], $tab[5], $tab[6], $tab[7], $tab[8], $tab[9], $tab[10], $tab[11]);
			if ($enregistre["reponse"] == 'ok') {
				// On affiche les infos si c'est demandé
				if ($aff_infos == 'oui') {
					$msg_enreg .= 'La ligne '.$i.' a bien été enregistrée.<br />';
				}
			}elseif($enregistre["reponse"] == 'non'){
				if ($aff_infos == 'oui') {
					$msg_enreg .= 'La ligne '.$i.' n\'a pas été enregistrée.'.$enregistre["msg_erreur"].'<br />';
				}
			}else{
				echo '(ligne '.$i.')&nbsp;->&nbsp;Il y a eu un souci car ce n\'est pas ok ou non qui arrive mais '.$enregistre["msg_erreur"].'.<br />';
			}
		}
		echo $msg_enreg; // permet d'afficher le message de bilan
	}

	// On incrémente le numéro de l'étape
	if ($etape != 12) {
		$prochaine_etape = $etape + 1;
		$vers_etape2 = mysql_query("UPDATE edt_init SET nom_export = '".$prochaine_etape."' WHERE ident_export = 'fichierTexte2'");
		// et on affiche un lien qui permet de continuer
		echo '
		<a href="edt_init_csv2.php">Pour continuer les concordances, veuillez recommencer la même procédure pour l\'étape n° '.$prochaine_etape.'</a>';

	}else{
		// On affiche un lien pour revenir à la page de départ
		echo '
		<a href="edt_init_csv2.php">Retour</a>';
	}
} // on a finit de bosser

?>
</body>
</html>