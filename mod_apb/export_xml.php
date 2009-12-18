<?php
/**
 *
 * $Id$
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
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

/*

Génération du fichier XML devant être transmis au système
"admission post-bac"

La structure de ce fichier est connue et documentée. Ce script génère
un fichier XML conforme aux spécifications.

Trois types de données sont requises, chacune nécessitant un traitement
spécifique :
- les données de l'année en cours : facilement accessibles, connues
- les données des années précédentes : plus difficilement accessible,
et pas nécessairement complètes pour tous les élèves. Ce script part du
principe qu'on intègre tout ce qu'on peut, et que l'absence de données ne
constitue pas en soit une cause de blocage de l'export. Il serait néanmoins
judicieux de vérifier que ce comportement est conforme à ce qui est attendu
par APB.
- les données de configuration pour chaque enseignement, permettant de
déterminer si l'enseignement est une LV1/2/3, s'il s'agit d'un enseignement
de spécialité ou non, etc. Ces données sont paramétrées directement
dans ce module, pour ne pas surcharger les pages de paramétrage des
groupes. L'absences de paramètres pour un groupe donné entraîne un blocage
de l'export, pour éviter les erreurs.


Ce script n'a besoin d'aucun paramètre particulier pour pouvoir fonctionner.
L'utilisation des informations de paramétrage pour chaque classe permet 
de déterminer ce qui devra être inclus ou non.

*/




$utiliser_pdo = 'on';

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



$data = array();

// On va initialiser toutes les données dans un grand tableau. Ensuite
// on va parcourir le tableau et utiliser les outils adéquats pour générer le XML

$data['etablissement'] = array();
$data['etablissement']['rne'] = $gepiSettings['rne'];
$data['etablissement']['nom'] = $gepiSettings['gepiSchoolName'];
$data['etablissement']['cp'] = $gepiSettings['gepiSchoolZipCode'];

// Liste des classes courantes concernées par l'export (en principe les classes de terminale)

// PROBLEME : on a besoin du professeur principal de la classe... Or, dans Gepi, on peut très bien avoir
// plusieurs profs principaux pour une même classe...
$req_classes = mysql_query("SELECT c.id, c.classe, c.nom_complet, MAX(p.num_periode) periode, c.apb_niveau FROM classes c, periodes p
								WHERE c.apb_exportable == 1
									AND p.id_classe = c.id");
$data['etablissement']['classes'] = array();
while ($c = mysql_fetch_object($req_classes)) {
	$data['etablissement']['classes'][] = array('code' => $c->classe,
												'nom' => $c->nom_complet,
												'annee' => apb_annee($gepiSettings['gepiSchoolYear']),
												'niveau' => $c->apb_niveau,
												'decoupage' => $c->periode);
}


// Liste des élèves



// Récupération et traitement des données archivées














