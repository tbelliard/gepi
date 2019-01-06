<?php
/*
*
* Copyright 2001-2019 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Begin standart header
$niveau_arbo = 1;
$gepiPathJava="./..";

// Initialisations files
require_once("../lib/initialisations.inc.php");
include("../lib/initialisationsPropel.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();

if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/gestion/registre_traitements.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/gestion/registre_traitements.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='V',
autre='V',
description='Registre des traitements',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

include('droits_acces.inc.php');

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

//**************** EN-TETE *****************
$titre_page = "Registre des traitements";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class='bold'><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

echo "<h2>Registre des traitements</h2>

<p>La présente page est destinée à informer les utilisateurs sur les modules activés, les données traitées,...</p>

<pre style='color:red'>

Voir https://www.cnil.fr/fr/comprendre-le-rgpd
et https://www.reseau-canope.fr/fileadmin/user_upload/Projets/RGPD/RGPD_WEB.pdf



A FAIRE :

- la déclaration CNIL
- les responsables de Gepi
- Faire apparaitre les modules activés (et ce que chaque module implique)
- les droits donnés
- Statuts personnalisés à gérer (statut=autre (COP, Infirmière,...))

Les fichiers XML exploités avec quelles infos de Siècle/Sconet, logiciel d'emploi du temps, STS.
Les exports CSV, PDF,...

Pouvoir cocher ce que l'on souhaite faire apparaitre selon ce qui est utilisé dans l'établissement.

Pouvoir éditer/modifier certaines des lignes qui suivent, en ajouter, supprimer selon les usages.
</pre>

<p class='bold'>Description du traitement</p>
<table class='boireaus boireaus_alt'>
	<tr><th>Nom / sigle</th><td>GEPI <em>(Gestion des élèves par internet)</em></td></tr>
	<tr><th>Établissement</th><td>".getSettingValue('gepiSchoolName')."<br />
	".getSettingValue('gepiSchoolAdress1')."<br />
	".getSettingValue('gepiSchoolAdress2')."<br />
	".getSettingValue('gepiSchoolZipCode')." ".getSettingValue('gepiSchoolCity')."</td></tr>
	<tr><th>Réf CNIL</th><td>".getSettingValue('num_enregistrement_cnil')."</td></tr>
	<tr><th>Date de création</th><td></td></tr>
	<tr><th>Mise à jour</th><td></td></tr>
	<tr><th></th><td></td></tr>
</table>
<br />

<p class='bold'>Acteurs</p>
<table class='boireaus boireaus_alt'>
	<tr>
		<th>Acteurs</th>
		<th>Nom</th>
		<th>Adresse</th>
		<th>CP</th>
		<th>Ville</th>
		<th>Pays</th>
		<th>Tél</th>
	</tr>
	<tr>
		<th>Maîtrise d'ouvrage
			<br />
			<span style='color:red'>
			Renvoyer vers la page de<br />
			https://www.reseau-canope.fr/fileadmin/user_upload/Projets/RGPD/RGPD_WEB.pdf<br />
			expliquant ce dont il s'agit
			</span>
		</th>
		<td>".getSettingValue('gepiAdminPrenom')." ".getSettingValue('gepiAdminNom')."<br />(".getSettingValue('gepiAdminFonction').")</td>
		<td>".getSettingValue('gepiSchoolAdress1')."<br />
			".getSettingValue('gepiSchoolAdress2')."</td>
		<td>".getSettingValue('gepiSchoolZipCode')."</td>
		<td>".getSettingValue('gepiSchoolCity')."</td>
		<td>".getSettingValue('gepiSchoolPays')."</td>
		<td>".getSettingValue('gepiSchoolTel')."</td>
	</tr>
	<tr>
		<th>Délégué à la protection des données</th>
		<td>".getSettingValue('gepiAdminPrenom')." ".getSettingValue('gepiAdminNom')."<br />(".getSettingValue('gepiAdminFonction').")</td>
		<td>".getSettingValue('gepiSchoolAdress1')."<br />
			".getSettingValue('gepiSchoolAdress2')."</td>
		<td>".getSettingValue('gepiSchoolZipCode')."</td>
		<td>".getSettingValue('gepiSchoolCity')."</td>
		<td>".getSettingValue('gepiSchoolPays')."</td>
		<td>".getSettingValue('gepiSchoolTel')."</td>
	</tr>
	<tr>
		<th>Représentant</th>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<th>Responsables conjoints</th>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
</table>
<br />

<p class='bold'>Finalité(s) du traitement effectué</p>
<table class='boireaus boireaus_alt'>
	<tr>
		<th>Finalité</th>
		<td>
			Gestion de la scolarité des élèves.<br />
			Selon les modules activés <a href='#modules_actives'>(voir plus bas)</a>, cela peut concerner les notes, les bulletins, les cahiers de textes, les absences,...
		</td>
	</tr>
		<th>Finalité statistique</th>
		<td>
			OUI/<strong>NON</strong><br />
			<span style='color:red'>A voir avec les possibilités d'extractions statistiques anonymées</span>
		</td>
	</tr>
</table>
<br />

<p class='bold'>Mesures de sécurité</p>
<table class='boireaus boireaus_alt'>
	<tr>
		<th>Mesures de sécurité techniques</th>
		<td>
			Serveur dans un local fermé,...<br />
			Prise en main de la machine serveur uniquement via un compte administrateur, pas d'autre compte pour se connecter directement sur la machine serveur.<br />
			Voir pour suggestions https://www.cnil.fr/fr/principes-cles/guide-de-la-securite-des-donnees-personnelles<br />
			et https://www.ssi.gouv.fr/administration/reglementation/rgpd-renforcer-la-securite-des-donnees-a-caractere-personnel<br />
			Pouvoir en cocher, et aussi en ajouter qui ne seraient pas dans la liste suggérée
		</td>
	</tr>
		<th>Mesures de sécurité organisationnelles</th>
		<td>
			Accès uniquement avec un compte utilisateur authentifié<br />
			Sensibilisation à la solidité des mots de passe,...
		</td>
	</tr>
</table>
<br />

<h3>Généralités</h3>
<p>Indépendamment des modules activés et des droits d'accès donnés, Gepi permet aux utilisateurs d'accéder à un certain nombre d'informations.<br />
<span style='color:red'>A détailler...</span><br />
Les comptes administrateurs, scolarité <em>(chef d'établissement, adjoint, secrétaire)</em>, cpe on accès à... <span style='color:red'>à détailler...</span><br />
Les professeurs ont accès à leurs listes d'élèves avec leurs nom, prénom, genre (M/F), date de naissance et classe.<br />
Il peuvent exporter ces listes en fichiers CSV et PDF.</p>

<pre style='color:red'>
- Les statuts, leur rôle et les droits associés (http://www.sylogix.org/projects/gepi/wiki/Gepi_admin).
- Imports XML, quelles infos stockées, qui y a accès...
- ...

</pre>


<pre>Dans Gepi, chaque utilisateur a un compte qui a un statut (et un seul) parmi :

    &quot;administrateur&quot; : Les comptes administrateurs ne servent que pour:
        le paramétrage général (nom de l'établissement, adresse,...)
        l'initialisation de l'année (création des utilisateurs, des classes, des élèves,...)
        les sauvegardes/restaurations
        les réglages des droits d'accès (les professeurs peuvent ou non voir telle ou telle chose, les CPEs peuvent ou non...)
        définir les modules de Gepi à activer
        etc.
    &quot;scolarité&quot; : Ces comptes permettent de:
        Ouvrir/fermer des périodes en saisie pour telle ou telle classe
        Paramétrer et imprimer les bulletins
        Imprimer les relevés de notes
        Saisir les avis des conseils de classe
        Visualiser les graphiques
        etc.
    &quot;CPE&quot; : Les comptes CPE se paramètrent en fonction des besoins :
        Ils peuvent saisir les absences sur les bulletins (import possible depuis Sconet absences)
        Ils peuvent gérer les absences dans l'établissement (suivi et mise en place d'un suivi)
        Faire un suivi de la saisie des absences par les professeurs
        etc.
    &quot;professeur&quot; : Ces comptes peuvent saisir selon ce qui a été paramétré par l'administrateur :
        des devoirs (notes)
        des appréciations (bulletin)
        le cahier de textes
        les absences
        les fiches du brevet (collège)
        etc.
    &quot;secours&quot; : Le statut secours peut :
        Saisir/corriger les appréciations et moyennes sur les bulletins (par exemple pour dépanner un professeur malade au moment du remplissage des bulletins...)
        Saisir les absences à la place d'un CPE indisponible
    &quot;élève&quot; : Ces comptes, quand ils sont créés, permettent à leur titulaire de (suivant les réglages de l'admin) :
        visualiser leur relevé de notes
        visualiser leur cahier de textes
        télécharger leur photo sur le trombinoscope
        visualiser leur équipe pédagogique
        etc.
    &quot;responsable&quot; : Ces comptes, quand ils sont créés, permettent à leur titulaire de (suivant les réglages de l'admin) :
        visualiser le relevé de notes de leur enfant
        visualiser le cahier de textes de leur enfant
        visualiser l'équipe pédagogique de leur enfant
        etc.

    &quot;autre&quot; : Ce statut, dit &quot;personnalisé&quot;, permet à l'admin de paramétrer plus finement les droits confiés à un (ou des) utilisateur(s). Cette gestion particulière se fait à partir de la page de gestion des utilisateurs, onglet &quot;statuts personnalisés&quot;. Cette fonctionnalité permet notamment de créer un statut &quot;Chef d'établissement&quot;, ou bien &quot;Inspecteur&quot;, ou encore &quot;C.O.P.&quot;, en fonction des besoins mais aussi des usages de l'établissement.

C'est un peu résumé, mais voilà pour les grandes lignes.

Le compte administrateur ne doit normalement être utilisé qu'en début d'année.
Par la suite, en gestion courante, c'est plutôt les comptes scolarité qui ont les droits appropriés.</pre>


<a name='modules_actives'></a><h3>Modules activés</h3>
<table class='boireaus boireaus_alt resizable sortable'>
	<tr>
		<th>Module</th>
		<th>Finalité</th>
		<th>Explications</th>
	</tr>".(getSettingValue('active_module_absence')=='y' ? "
	<tr>
		<td>Absences</td>
		<td>Gestion des absences et retards des élèves</td>
		<td style='text-align:left'>
			Le module absences permet de saisir les absences et retards des élèves.
		</td>
	</tr>" : "").(getSettingValue('active_module_absence')=='2' ? "
	<tr>
		<td>Absences 2</td>
		<td>Gestion des absences et retards des élèves</td>
		<td style='text-align:left'>
			Le module absences permet de saisir les absences et retards des élèves.<br />
			Les professeurs constatent l'absence en classe à un moment donné.<br />
			Les personnels de Vie Scolaire (CPE) traitent les absences <em>(pour les catégoriser absence, retard inter-cours, retard extérieur, passage à l'infirmerie,...)</em> et contactent le cas échéant les responsables <em>(parents, tuteurs,...)</em>.<br />
			Les personnels de Vie Scolaire ont donc accès aux adresses postales, téléphoniques et mail.<br />
			Ils peuvent effectuer des extractions CSV/ODT des absences pour par exemple discuter des absences de tel élève ou dans telle classe.<br />

			Des absences répétées, non justifiées (non valides), peuvent amener les CPE à effectuer un signalement à l'Inspection académique.<br />
			Des extractions statistiques peuvent aussi être demandées par l'Éducation Nationale.<br />

			".(getSettingAOui('active_absences_parents') ? "<br />Les parents ont accès aux signalements d'absences enregistrés.<br />Le délai de ...h permet à la Vie Scolaire de traiter une éventuelle erreur de saisie ou un défaut d'information sur une modification dans une activité." : "")."

		</td>
	</tr>" : "").(getSettingAOui('active_module_absence_professeur') ? "
	<tr>
		<td>Abs/remplacements profs</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_mod_actions') ? "
	<tr>
		<td>Actions</td>
		<td>Gestion des inscriptions/présence élèves sur, par exemple, les sorties/actions UNSS.</td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_annees_anterieures') ? "
	<tr>
		<td>Années antérieures</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_bulletins') ? "
	<tr>
		<td>Bulletins</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_cahiers_texte') ? "
	<tr>
		<td>Cahiers de textes</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_carnets_notes') ? "
	<tr>
		<td>Carnets de notes</td>
		<td>Gérer les notes des élèves</td>
		<td style='text-align:left'>Le module permet aux professeurs de saisir des notes, avec commentaire ou non.<br />
		Les professeurs peuvent choisir si les commentaires sur une évaluation doivent être visibles ou non des responsables <em>(les professeurs sont alertés qu'ils ne doivent pas saisir de commentaire déplacé)</em>.<br />

		<span style='color:red'>A FAIRE : Permettre de générer un export commentaires inclus pour le cas où un responsable réclamerait le détail des saisies.<br />
		Les parents peuvent aussi réclamer un export du module Discipline et d'autres... revoir ces possibilités d'export</span><br />

		Les professeurs peuvent choisir à partir de quelle date les notes seront visibles, définir des coefficients,...<br />
		Il est possible de créer des boîtes ou sous-matières dans les carnets de notes.<br />

		Les comptes scolarité peuvent générer des relevés de notes.<br />
		Les autres comptes voient ces droits d'accès définis dans la rubrique <a href='#droits_acces'>droits d'accès</a>.<br />

		".(getSettingAOui('GepiAccesEvalCumulEleve') ? "Les professeurs peuvent aussi créer des évaluations cumulées.<br />
		Il s'agit d'évaluations successives dont le cumul des points donne une note sur le cumul des référentiels.<br />
		Les professeurs peuvent choisir si telle évaluation-cumul est visible ou non des élèves/responsables.<br />
		Dans le cas où l'évaluation n'est pas visible dans le module évaluations-cumul, le cumul obtenu sera visible une fois transféré dans le carnet de notes." : "")."</td>
	</tr>" : "").(getSettingAOui('active_mod_ects') ? "
	<tr>
		<td>Crédits ECTS</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_mod_discipline') ? "
	<tr>
		<td>Discipline</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_mod_disc_pointage') ? "
	<tr>
		<td>Discipline/pointage</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_mod_alerte') ? "
	<tr>
		<td>Dispositif d'alerte</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_edt_ical') ? "
	<tr>
		<td>EDT Ical/Ics</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "");

if((getSettingAOui('autorise_edt'))||(getSettingAOui('autorise_edt_eleve'))||(getSettingAOui('autorise_edt_admin'))) {
	echo "
	<tr>
		<td>Emplois du temps</td>
		<td>Permet de saisir/consulter les emplois du temps des classes, élèves, professeurs, salles.</td>
		<td style='text-align:left'>";
	if(getSettingAOui('autorise_edt_admin')) {
		echo "Les comptes administrateurs ont accès à la consultation, l'import, la saisie des emplois du temps dans Gepi, indépendamment de l'ouverture de l'accès aux emplois du temps aux autres utilisateurs de Gepi.<br />";
	}
	if(getSettingAOui('autorise_edt_tous')) {
		echo "Si les emplois du temps sont remplis, les professeurs, CPE et comptes scolarité on accès aux emplois du temps des classes, élèves, professeurs, salles dans Gepi.<br />
			Par défaut, les professeurs n'ont accès qu'à leur emploi du temps, pas à ce lui de leurs collègues.<br />
			Ce paramètre peut être modifié dans la rubrique <a href='#droits_acces'>droits d'accès</a>.";
			if(getSettingAOui('edt_remplir_prof')) {
				echo "<br />
			Les professeurs sont autorisés à remplir leur emploi du temps eux-mêmes <em>(ce n'est pas une autorisation à modifier son emploi du temps à sa convenance&nbsp;; l'Administration a simplement délègué la saisie dans Gepi)</em>.";
			}
	}
	if(getSettingAOui('autorise_edt_eleve2')) {
		echo "Les élèves et responsables ont accès à leurs emplois du temps et à celui de leur classe dans Gepi.";
	}
	elseif(getSettingAOui('autorise_edt_eleve')) {
		echo "Les élèves et responsables ont accès à leurs emplois du temps dans Gepi.";
	}
	echo "</td>
	</tr>";
}

echo (getSettingAOui('active_mod_engagements') ? "
	<tr>
		<td>Engagements</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_mod_epreuve_blanche') ? "
	<tr>
		<td>Epreuves blanches</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_mod_examen_blanc') ? "
	<tr>
		<td>Examens blancs</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('rss_cdt_eleve') ? "
	<tr>
		<td>Flux RSS</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_mod_genese_classes') ? "
	<tr>
		<td>Genèse des classes</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_mod_gest_aid') ? "
	<tr>
		<td>Gestionnaires AID</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_inscription') ? "
	<tr>
		<td>Inscription</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('GepiListePersonnelles') ? "
	<tr>
		<td>Listes personnelles</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_module_LSUN') ? "
	<tr>
		<td>Livret Scolaire Unique</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_mod_ooo') ? "
	<tr>
		<td>Modèles openDocument</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_notanet') ? "
	<tr>
		<td>Notanet/Brevet</td>
		<td></td>
		<td style='text-align:left'></td>
	</tr>" : "").(getSettingAOui('active_mod_orientation') ? "
	<tr>
		<td>Orientation</td>
		<td>Orientation des élèves</td>
		<td style='text-align:left'>



			Donner l'accès à la saisie des voeux et orientations proposées.
		


		</td>
	</tr>" : "").(getSettingAOui('statuts_prives') ? "
	<tr>
		<td>Statuts perso.</td>
		<td>Créer des statuts utilisateurs avec des droits particuliers</td>
		<td style='text-align:left'>

			Ces statuts, dit &quot;personnalisés&quot;, permettent à l'admin de paramétrer finement les droits confiés à un (ou des) utilisateur(s).<br />
			Cette gestion particulière se fait à partir de la page de gestion des utilisateurs, onglet &quot;statuts personnalisés&quot;.<br />
			Cette fonctionnalité permet notamment de créer un statut &quot;Chef d'établissement&quot;,<br />
			ou bien &quot;Inspecteur&quot;,<br />
			ou encore &quot;C.O.P.&quot;,<br />
			en fonction des besoins mais aussi des usages de l'établissement.<br />
			Il s'agit généralement de comptes avec des besoins limités, qui ne nécessitent pas un accès à toutes les fonctionnalités de Gepi et dont le foisonnement peut perdre un utilisateur qui n'en fait pas un usage très régulier.<br />

		<span style='color:red'>Détailler les droits des statuts existants (ceux avec utilisateur actif associé)</span><br />

		</td>
	</tr>" : "").(getSettingAOui('active_module_trombinoscopes') ? "
	<tr>
		<td>Trombinoscopes</td>
		<td>Permet de consulter les photos des élèves d'une classe, d'un enseignement, d'une activité inter-disciplinaire (AID).</td>
		<td style='text-align:left'>
			Permet, selon les droits accordés dans <a href='#droits_acces'>droits d'accès</a>, aux utilisateurs de consulter les photos des élèves d'une classe, d'un enseignement, d'une activité inter-disciplinaire (AID).<br />
			Les comptes de tous les personnels de l'établissement ont accès en consultation aux photos des élèves de toutes les classes.<br />
			Les élèves ont accès à leur photo dans <a href='../utilisateurs/mon_compte.php'>Gérer mon compte</a>.<br />
			Les personnes autorisées à téléverser les photos des élèves sur le serveur Gepi sont par défaut les comptes 'administrateur' et 'scolarité'.<br />
			Le téléversement peut être ouvert aux CPE et ".getSettingValue('gepi_prof_suivi')." via des <a href='#droits_acces'>droits d'accès</a> supplémentaires.<br />
			".(getSettingAOui('GepiAccesEleTrombiPersonnels') ? "<br />
			Le trombinoscope des personnels de l'établissement est activé<br /><em>(cela ne signifie pas pour autant que la photo a nécessairement été téléversée sur le serveur Gepi)</em>." : "")."
		</td>
	</tr>" : "")."
</table>

<a name='plugins'></a><h3>Plugins</h3>
<p>Des plugins peuvent être développés pour ajouter des fonctionnalités à Gepi.</p>
<p style='color:red'>Ajouter un champ 'description_detaillee' aux plugin.xml pour expliquer les fonctionnalités, qui a le droit de faire quoi,...</p>";

include '../mod_plugins/traiterXml.class.php';
include '../mod_plugins/traiterRequetes.class.php';
//include '../mod_plugins/plugins.class.php';

# On liste les plugins
$liste_plugins  = array();
$open_dir       = scandir("../mod_plugins");

foreach ($open_dir as $dir) {
	// On vérifie la présence d'un point dans le nom retourné
	$test = explode(".", $dir);
	if (count($test) <= 1) {
		$test2 = PlugInPeer::getPluginByNom($dir);

		if (is_object($test2)) {
			$liste_plugins[] = $test2;
		}
		else {
			$liste_plugins[] = $dir;
		}
	}
}
/*
echo "<pre>";
print_r($liste_plugins);
echo "</pre>";
*/

echo "
<table class='boireaus boireaus_alt resizable sortable'>
	<tr>
		<th>Plugin</th>
		<th>Description</th>
		<th>Auteur</th>
		<th>Description détaillée</th>
	</tr>";
foreach ($liste_plugins as $plugin) {
	if (is_object($plugin)){
		// le plugin est installé

		$xml = simplexml_load_file("../mod_plugins/".$plugin->getNom() . "/plugin.xml");
		$versiongepi=$xml->versiongepi;
		// On teste s'il est ouvert
		if ($plugin->getOuvert() == 'y') {
			echo "
	<tr>
		<td>".str_replace("_", " ", $plugin->getNom())."</td>
		<td>".$xml->description."</td>
		<td>".$xml->auteur."</td>
		<td style='text-align:left'>".(isset($xml->description_detaillee) ? nl2br($xml->description_detaillee) : "-")."</td>
	</tr>";
		}
	}
}
echo "
</table>";

//==============================================================================

echo "
<a name='droits_acces'></a><h3>Droits d'accès</h3>
<p>Des droits d'accès permettent de personnaliser des autorisations dans divers modules de Gepi&nbsp;:</p>
<table class='boireaus boireaus_alt resizable sortable'>
	<tr>
		<th>Rubrique ou module</th>
		<th>Public</th>
		<th>Droit</th>
	</tr>";
$rubrique_precedente='';
foreach($tab_droits_acces as $statutItem => $current_statut_item) {
	foreach($current_statut_item as $titreItem => $current_item) {
		if((in_array($_SESSION['statut'], $current_item['visibilite']))&&(getSettingAOui($titreItem))) {
			echo "
	<tr>
		<td style='text-align:left'>".$current_item['rubrique']."</td>
		<td>".ucfirst($statutItem)."</td>
		<td style='text-align:left'>".$current_item['texteItem']."</td>
	</tr>";
		}
	}
}
echo "
</table>";


require("../lib/footer.inc.php");
?>
