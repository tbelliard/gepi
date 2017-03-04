<?php

/*
*
* Copyright 2016 Régis Bouguin
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
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

$selectionClasse = $_SESSION['afficheClasse'];

if (0 == count($selectionClasse)) {
	echo "Vous devez valider la sélection d'au moins une classe";
	die();
}
$myData = implode(",", $selectionClasse);
$millesime = LSUN_annee(getSettingValue('gepiYear'));

/*===== Responsable =====*/
$listeResponsables = getResponsables();
//$idResponsable = "RESP_01";
//$libelle = 'À déterminer, civilité nom prénom par exemple';

/*===== Élève =====*/
// On récupère tous les élève
$sqlEleves = "SELECT t3.* , c.classe FROM "
	. "(SELECT DISTINCT t1.* , t2.id_classe "
	. "FROM eleves AS t1 "
	. "INNER JOIN j_eleves_classes AS t2 "
	. "ON t1.login=t2.login "
	. "WHERE  t2.id_classe IN (".$myData.")) AS t3 "
	. "INNER JOIN classes AS c "
	. "ON t3.id_classe = c.id";
//echo $sqlEleves;
$listeEleves = $mysqli->query($sqlEleves);

/*===== Périodes =====*/
// Il faudra vérifier que toutes les classes ont bien le même nombre de période et interdire de créer un fichier mixte
$listePeriodes = getPeriodes($myData);

/*===== Disciplines =====*/
//Code identifiant la matière «nationale». 
//code nationale si la matière est une matière nationale, code académique si la matière est une matière académique).
//Modalité d’élection (S : tronc commun, O : option obligatoire, F : option facultative) de la matière issue des programmes
// nom à récupérer dans nomenclatures_valeurs
/*
$sqlDisciplines = "SELECT DISTINCT m.matiere , m.nom_complet , m.code_matiere , mm.code_modalite_elect AS election FROM mef_matieres AS mm "
	. "INNER JOIN matieres AS m ON m.code_matiere = mm.code_matiere ORDER BY m.code_matiere , mm.code_modalite_elect DESC ";

$sqlDisciplines = "SELECT DISTINCT mm.code_matiere , mm.code_modalite_elect , nv.valeur AS nom_complet FROM mef_matieres AS mm "
	. "INNER JOIN nomenclatures_valeurs AS nv ON nv.code = mm.code_matiere WHERE nom = 'libelle_long' ";
 * 
 */

//à t2 vérifier qu'on a autant de matière qu'à t1 sinon une matière n'a pas de modalité

$sqlDisciplines01 = "SELECT id_groupe , id_classe FROM `j_groupes_classes` WHERE id_classe IN ($myData)";
$sqlDisciplines02 = "SELECT t0.* , jgm.id_matiere FROM (
				$sqlDisciplines01
			) AS t0
			INNER JOIN
				j_groupes_matieres AS jgm
			ON jgm.id_groupe = t0.id_groupe
			WHERE NOT EXISTS 
			(
				SELECT *
				FROM j_groupes_types
				WHERE j_groupes_types.id_groupe = t0.id_groupe
			)";
//$nbMat01 = $mysqli->query($sqlDisciplines02)->num_rows;
//echo $sqlDisciplines02."<br>";
$sqlDisciplines = "SELECT DISTINCT t2.id_matiere , t2.code_modalite_elect , m.nom_complet ,  m.code_matiere FROM (
		SELECT DISTINCT t1.* , jgem.code_modalite_elect FROM (
			$sqlDisciplines02
		) AS t1
		INNER JOIN
			j_groupes_eleves_modalites AS jgem
		ON jgem.id_groupe = t1.id_groupe
	) AS t2
	INNER JOIN
		matieres AS m
	ON m.matiere = t2.id_matiere
";

//echo $sqlDisciplines."<br>";
$listeDisciplines = $mysqli->query($sqlDisciplines);

// il faut afficher les données en $sqlDisciplines02 et pas en $sqlDisciplines sinon c'est la galère pour l'administrateur
$sqlErreurs = "SELECT DISTINCT t3.id_matiere FROM ($sqlDisciplines02) AS t3 WHERE t3.id_matiere NOT IN (SELECT DISTINCT t4.id_matiere FROM (($sqlDisciplines) AS t4 )) ";
$reqErreurs = $mysqli->query($sqlErreurs);

if ($reqErreurs->num_rows) {
	$matErreur = "";
	while ($erreur = $reqErreurs-> fetch_object()) {
		$matErreur .= $erreur->id_matiere." ";
	}
	$msgErreur .= "Les modalités d'élection des matières $matErreur semblent ne pas être attribuées. <em><a href='../../classes/index.php'  target='_BLANK'>Corriger</a></em><br>";
	//$msgErreur .= $sqlErreurs;
}

/*===== Enseignants =====*/

$myClasses = implode(",", $_SESSION['afficheClasse']);
// on récupère les groupes des classes choisies
//$sqlEns01 = "SELECT DISTINCT jgc.id_groupe, jgc.id_classe FROM j_groupes_classes AS jgc WHERE jgc.id_classe IN ($myClasses) AND jgc.id_groupe NOT IN (SELECT id_groupe FROM `j_groupes_types`)";
//$sqlEns01 = "SELECT DISTINCT jgc.id_groupe, jgc.id_classe FROM j_groupes_classes AS jgc WHERE jgc.id_classe IN ($myClasses) ";
$sqlEns01 = "SELECT DISTINCT jgc.id_groupe, jgc.id_classe FROM j_groupes_classes AS jgc WHERE jgc.id_classe IN ($myClasses) ";
// puis les logins prof
//$sqlEns02 = "SELECT DISTINCT jgp.login, t1.* FROM ($sqlEns01) AS t1 INNER JOIN j_groupes_professeurs AS jgp ON t1.id_groupe = jgp.id_groupe";
$sqlEns02 = "SELECT DISTINCT jgp.login FROM ($sqlEns01) AS t1 INNER JOIN j_groupes_professeurs AS jgp ON t1.id_groupe = jgp.id_groupe";
$sqlEns12 = "SELECT DISTINCT u3.* FROM ((SELECT DISTINCT id_utilisateur AS login FROM j_aid_utilisateurs) UNION ($sqlEns02)) AS u3 ORDER BY u3.login";
//echo $sqlEns12;
//puis les profs
$sqlEns03 = "SELECT DISTINCT t10.login, t10.nom, t10.prenom, t10.civilite, t10.numind, t10.type FROM ($sqlEns12) AS t2 INNER JOIN utilisateurs AS t10 ON t10.login = t2.login";


$sqlEnseignants = $sqlEns03;
// echo $sqlEnseignants;
$listeEnseignants = $mysqli->query($sqlEnseignants);

/*===== Éléments de programmes ===== */
$sqlElementsProgramme = "SELECT * FROM matiere_element_programme ORDER BY id;";
$listeElementsProgramme = $mysqli->query($sqlElementsProgramme);

/*===== parcours éducatifs communs ===== */
$sqlParcoursCommun = "SELECT DISTINCT periode, classe FROM lsun_parcours_communs WHERE classe IN ($myData) ORDER BY id;";
$listeParcoursCommuns = $mysqli->query($sqlParcoursCommun);
// Voir comment on gère les parcours des classes

/*===== commentaires de vie scolaire communs ===== */
// Voir comment on gère les commentaires de vie scolaire communs
$sqlVieScoCommun = "SELECT aa.* , c.classe FROM absences_appreciations_grp AS aa "
	. "INNER JOIN classes as c ON aa.id_classe = c.id "
	. " WHERE aa.id_classe IN ($myData) ORDER BY aa.id_classe , aa.periode ;";
//echo $sqlVieScoCommun;
$listeVieScoCommun = $mysqli->query($sqlVieScoCommun);

/*===== EPI ===== */
// Définitions des EPI


/*===== epis-groupes ===== */
// Description des EPI pour chaque groupe

/*===== accompagnements personnalisés ===== */
// Définitions des accompagnements personnalisés

/*===== AP groupes ===== */





