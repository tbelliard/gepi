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








<a name='modules_actives'></a><h3>Modules activés</h3>
<table class='boireaus boireaus_alt resizable sortable'>
	<tr>
		<th>Module</th>
		<th>Finalité</th>
		<th>Explications</th>
	</tr>

</table>



<a name='droits_acces'></a><h3>Droits d'accès</h3>
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
