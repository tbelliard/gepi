<?php

/**
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
$resultat_session = $session_gepi->security_check();
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
$effacer_semaines = isset($_POST["effacer_semaines"]) ? $_POST["effacer_semaines"] : NULL;
$values = '';
$msg = NULL; // le message destiné aux lignes non reconnues par l'import
//$ = isset($_POST[""]) ? $_POST[""] : NULL;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
	<title>Enregistrer les concordances pour l'import de l'EdT</title>
</head>
<body>
<?php
// On indique à quelle étape on se situe
echo '<p>ETAPE n° '.$etape.'</p>';

// Si cette étape n'est pas nulle, on fait le travail demandé
if ($etape != NULL) {
	// On prend d'abord le cas des semaines
	if ($etape == 7) {
		// On récupère les données pour les sauvegarder dans la table edt_semaines
		// Si c'est demandé, on vide la table edt_semaines
		if ($effacer_semaines == "ok") {
			// alors on met à jour la table
				$erreur = 'non'; // sert à enregistrer les erreurs du update
				$type_semaine = array();
			for($s = 1; $s < ($nbre_ligne + 1); $s++){
				$type_semaine[$s] = isset($_POST["semaine_".$s]) ? $_POST["semaine_".$s] : NULL;
				$update = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE edt_semaines SET type_edt_semaine = '".$type_semaine[$s]."' WHERE num_edt_semaine = ".$s."");
				if (!$update) {
					$erreur .= 'erreur'.$s.'('.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)).') | ';
				}
			}
			if ($erreur == 'non') {
				echo '
				<h3>L\'opération est réussie</h3>
				<p>Il y a eu '.$nbre_ligne.' enregistrements dans la base</p>
				<a href="./edt_init_texte.php">Revenez en arrière et recommencer la même opération pour l\'étape '.$prochaine_etape.'.</a>';
			}else{
				// Il y a eu des problèmes et on affiche l'erreur
				echo $erreur;
			}
		}else{
			// On ne fait rien puisque cette table est initialisée à la base (/sql/data_gepi.sql)
			$prochaine_etape = $etape + 1;
			$vers_etape2 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE edt_init SET nom_export = '".$prochaine_etape."' WHERE ident_export = 'fichierTexte'");
			echo '
			<h3>L\'opération a réussi</h3>
			<a href="./edt_init_texte.php">Revenez en arrière et recommencer la même opération pour l\'étape '.$prochaine_etape.'.</a>';
		}


	// Puis on prend le cas des cours
	}elseif($etape == 9){
		// Pour les cours, on fait le lien avec les infos déjà rentrées dans la table edt_init
		require_once("edt_init_fonctions.php");
		// On explose la valeur
		for($c = 1; $c < $nbre_ligne + 1; $c++){
			$cours[$c] = isset($_POST["cours_".$c]) ? $_POST["cours_".$c] : NULL;
			$elements_cours = explode("|", $cours[$c]);
			// Si l'enregistrement n'est pas bon (soit que Gepi ne retrouve pas l'enseignement / AID soit que
			// la base réagit mal, on affiche toutes les infos sur la ligne qui n'est pas enregistrée
			/*echo '<b>Ligne n° '.$c.'</b>
				  classe : '.$elements_cours[0].
				' type semaine : '.$elements_cours[1].
				' jour : '.$elements_cours[2].
				' heure deb : '.$elements_cours[3].
				' heure fin : '.$elements_cours[4].
				' prof : '.$elements_cours[5].
				' grpe : '.$elements_cours[6].
				' partie : '.$elements_cours[7].
				' matière : '.$elements_cours[8].
				' salle : '.$elements_cours[9].
				' Grpe/entière : '.$elements_cours[10].'<br />'."\n";
			*/
			// On cherche à retrouver la salle du cours
			$salle = renvoiIdSalle($elements_cours[9]);
			if ($salle == "inc") {
				// on insère cette nouvelle classe dans la table adéquate
				$query = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO salle_cours SET numero_salle = '".$elements_cours[9]."', nom_salle = ''");
				$salle = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
			}

			// Le type de semaine
			if ($elements_cours[1] == '') {
				$week_type = "0";
			}else{
				$week_type = $elements_cours[1];
			}
			// On veut récupérer le jour de la semaine
			$jour = renvoiJour($elements_cours[2]);
			// Ainsi que l'id du créneau id_definie_periode
			$debut = renvoiIdCreneau($elements_cours[3], $jour);
			// La durée (est-ce qu'on la met à 1 ? )
			$duree = renvoiDuree($elements_cours[3], $elements_cours[4]);
			// on détermine si le cours commence au début ou au milieu d'un créneau
			$debut_dec = renvoiDebut($debut, $elements_cours[3], $jour);
			// Il reste à afficher le login du professeur
			$prof = renvoiLoginProf($elements_cours[5]);
			// On cherche à reconstituer le groupe/enseignement/AID concerné
			$groupe = renvoiIdGroupe($prof, $elements_cours[0], $elements_cours[8], $elements_cours[6], $elements_cours[7], 'texte');
				$choix_groupe = "non";
				if ($groupe == "aucun") {
					// On n'enregistre pas le cours avec "inc" comme id_groupe
					$groupe_insert = "inc";
					//$choix_groupe = "oui";
					$msg .= '<p>Pour la ligne n° '.$c.', Gepi ne trouve pas la concordance, impossible de l\'enregistrer('.$prof.' '.renvoiConcordances($elements_cours[8], 5).').</p>';
				}elseif($groupe == "plusieurs"){
					// On propose un message
					$msg .= '<p>Pour la ligne n° '.$c.', Gepi renvoie trop de réponses possibles.
							Impossible de l\'enregistrer.</p>';

				}else{
					// On vérifie que ce cours n'existe pas déjà
					$query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id_cours FROM edt_cours WHERE
										id_groupe = '".$groupe."' AND
										id_salle = '".$salle."' AND
										jour_semaine = '".$jour."' AND
										id_definie_periode = '".$debut."' AND
										duree = '".$duree."' AND
										heuredeb_dec = '".$debut_dec."' AND
										id_semaine = '".$week_type."' AND
										id_calendrier = '0' AND
										modif_edt = '0' AND
										login_prof = '".$prof."'")
											OR DIE('Erreur dans la vérification sur l\'existence du cours : '.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					$verif_exist = mysqli_num_rows($query);
					if ($verif_exist >= 1) {
						// On n'enregistre pas une deuxième fois
						$choix_groupe = "non";
					}else{
						// Il n'y a qu'une réponse, alors c'est bon
						$choix_groupe = "oui";
						$groupe_insert = $groupe;
					}
				}
				if ($choix_groupe == "oui") {
					// Au final, on insère dans la table edt_cours
					$sql = "INSERT INTO edt_cours (id_cours, id_groupe, id_salle, jour_semaine, id_definie_periode, duree, heuredeb_dec, id_semaine, id_calendrier, modif_edt, login_prof)
								VALUES ('', '".$groupe_insert."', '".$salle."', '".$jour."', '".$debut."', '".$duree."', '".$debut_dec."', '".$week_type."', '0', '0', '".$prof."') ";
					$query = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
								OR DIE('Erreur dans l\'enregistrement du cours '.$sql.'<br /> -> '.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

				}

		} // for($c = 0; $c < $nbre_ligne; $c++)  (de l'étape 9)

	// pour tout ce qui n'est ni les types de semaines, ni des cours, on voit la concordance
	}else{
		// C'est le cas général pour enregistrer les concordances entre le fichier txt et Gepi
		// On réceptionne les données et on les rentre dans la base
		for($a = 1; $a < ($nbre_ligne + 1); $a++){

			$nom_gepi[$a] = isset($_POST["nom_gepi_".$a]) ? $_POST["nom_gepi_".$a] : NULL;
			$numero_texte[$a] = isset($_POST["numero_texte_".$a]) ? $_POST["numero_texte_".$a] : NULL;
			// On prépare la requête
			if ($nom_gepi[$a] != '') {
				$values .= "('', '".$etape."', '".$numero_texte[$a]."', '".$nom_gepi[$a]."'), ";

			}
		}
		// On envoie toutes les requêtes d'un coup
		echo $values;
		$envoie = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO edt_init (id_init, ident_export, nom_export, nom_gepi)
					VALUES ".$values." ('', ".$etape.", 'fin', 'fin')") OR DIE ('Erreur dans la requête $envoie de l\'étape '.$etape.' : '.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)).'<br />'.$envoie);

		// si l'envoi est une réussite alors on passe à l'étape suivante
		if ($envoie) {
			$prochaine_etape = $etape + 1;
			$vers_etape2 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE edt_init SET nom_export = '".$prochaine_etape."' WHERE ident_export = 'fichierTexte'");
			echo '
			<h3>L\'opération a réussi</h3>';
			// Certaines étapes ne donnent lieu à aucun enregistrement
			if ($etape != 4) {
				echo '
				<p>Il y a eu '.$nbre_ligne.' enregistrements dans la base</p>';
			}else{
				// C'est la cas des "PARTIES" qui sont des références à des groupes d'élèves
				// étape 4
				echo '
				<p>Il n\'y a eu aucun enregistrement dans la base</p>';
			}
			echo '
			<a href="./edt_init_texte.php">Revenez en arrière et recommencer la même opération pour l\'étape '.$prochaine_etape.'.</a>';
		}

	} // fin du else
} // fin du if ($etape != NULL)
if (isset($msg) AND $msg != '') {
	echo $msg;
	echo '
	<p>Vous pouvez aller vérifier les emplois du temps des professeurs. <a href="index_edt.php">REVENIR</a></p>
	';
}

?>
</body>
</html>