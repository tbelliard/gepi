<?php
/*
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$titre_page = "Gestion générale";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

?>
<p class=bold><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<center>
<table class='menu'>
<tr>
	<th colspan='2'><img src='../images/icons/securite.png' alt='Sécurité' class='link'/> - Sécurité</th>
</tr>
<tr>
    <td width=200><a href="gestion_connect.php">Gestion des connexions</a></td>
    <td>Affichage des connexions en cours, activation/désactivation des connexions pour le site, protection contre les attaques forces brutes, journal des connexions, changement de mot de passe obligatoire.
    </td>
</tr>
<tr>
    <td width=200><a href="security_panel.php">Panneau de contrôle sécurité</a></td>
    <td>Visualiser les tentatives d'utilisation illégale de Gepi.
    </td>
</tr>
<tr>
    <td width=200><a href="security_policy.php">Politique de sécurité</a></td>
    <td>Définir les seuils d'alerte et les actions à entreprendre dans le cas de tentatives d'intrusion ou d'accès illégal à des ressources.
    </td>
</tr>
<tr>
	<td width="200"><a href="../class_php/test_serveur.php">Configuration serveur</a></td>
	<td>Voir la configuration du serveur php/Mysql pour v&eacute;rifier la compatibilit&eacute; avec Gepi.</td>
</tr>
</table>

<table class='menu'>
<tr>
	<th colspan='2'><img src='../images/icons/configure.png' alt='Configuration' class='link'/> - Général</th>
</tr>
<tr>
    <td width=200><a href="param_gen.php">Configuration générale</a></td>
    <td>Permet de modifier des paramètres généraux (nom de l'établissement, adresse, ...).
    </td>
</tr>
<tr>
    <td width=200><a href="droits_acces.php">Droits d'accès</a></td>
    <td>Modifier les droits d'accès à certaines fonctionnalités selon le statut de l'utilisateur.
    </td>
</tr>
<tr>
    <td width=200><a href="options_connect.php">Options de connexions</a></td>
    <td>Gestion de la procédure automatisée de récupération de mot de passe, paramétrage du mode de connexion (autonome ou Single Sign-On), changement de mot de passe obligatoire, réglage de la durée de conservation des connexions, suppression de toutes les entrées du journal de connexion.
    </td>
</tr>
<tr>
    <td width=200><a href="modify_impression.php">Gestion de la fiche "bienvenue"</a></td>
    <td>Permet de modifier la feuille d'information à imprimer pour chaque nouvel utilisateur créé.
    </td>
</tr>
<tr>
    <td width=200><a href="config_prefs.php">Paramétrage de l'interface professeur</a></td>
    <td>Paramétrage des items de l'interface simplifiée pour certaines pages. Gestion du menu en barre horizontale.</td>
</tr>
<tr>
    <td width=200><a href="param_couleurs.php">Paramétrage des couleurs</a></td>
    <td>Paramétrage des couleurs de fond d'écran et du dégradé d'entête.</td>
</tr>
</table>

<table class='menu'>
<tr>
	<th colspan='2'><img src='../images/icons/database.png' alt='Gestion bases de données' class='link'/> - Gestion des bases de données </th>
</tr>
<tr>
    <td width=200><a href="accueil_sauve.php">Sauvegardes et restauration</a></td>
    <td>Sauvegarder la base GEPI sous la forme d'un fichier au format "mysql".<br />
    Restaurer des données dans la base Mysql de GEPI à partir d'un fichier.
    </td>
</tr>
<tr>
    <td width=200><a href="../utilitaires/maj.php">Mise à jour de la base</a></td>
    <td>Permet d'effectuer une mise à jour de la base MySql après un changement de version  de GEPI.
    </td>
</tr>
<tr>
    <td width=200><a href="../utilitaires/clean_tables.php">Nettoyage des tables</a></td>
    <td>Procéder à un nettoyage des tables de la base MySql de GEPI (suppression de certains doublons et/ou lignes obsolètes ou orphelines).
    </td>
</tr>
<tr>
    <td width=200><a href="efface_base.php">Effacer la base</a></td>
    <td>Permet de réinitialiser les bases en effaçant toutes les données élèves de la base.
    </td>
</tr>
<tr>
    <td width=200><a href="efface_photos.php">Effacer les photos</a></td>
    <td>Permet d'effacer les photos des élèves qui ne sont plus dans la base.</td>
</tr>
<tr>
    <td width=200><a href="gestion_temp_dir.php">Gestion des dossiers temporaires</a></td>
    <td>Permet de contrôler le volume occupé par les dossiers temporaires (<i>utilisés notamment pour générer les fichiers tableur OpenOffice (ODS), lorsque la fonction est activée dans le module carnet de notes</i>), de supprimer ces dossiers,...</td>
</tr>

</table>

<table class='menu'>
<tr>
<th colspan='2'><img src='../images/icons/package.png' alt='Initialisation' class='link'/> - Outils d'initialisation</th>
</tr>
<?php
$use_sso = getSettingValue('use_sso');
if ($use_sso == "ldap_scribe") {
    ?>
<tr>
    <td width=200><a href="../init_scribe/index.php">Initialisation à partir de l'annuaire LDAP du serveur Eole Scribe</a></td>
    <td>Permet d'importer les données élèves, classes, professeurs, matières directement depuis le serveur LDAP de Scribe.
    </td>
</tr>
<?php
} else if ($use_sso == "lcs") {
    ?>
<tr>
    <td width=200><a href="../init_lcs/index.php">Initialisation à partir de l'annuaire LDAP du serveur LCS</a></td>
    <td>Permet d'importer les données élèves, classes, professeurs, matières directement depuis le serveur LDAP de LCS.
    </td>
</tr>
<?php
} else {
?>
<tr>
    <td width=200><a href="../init_csv/index.php">Initialisation des données à partir de fichiers CSV</a></td>
    <td>Permet d'importer les données élèves, classes, professeurs, matières depuis des fichiers CSV, par exemple des exports depuis Sconet.
    </td>
</tr>
<tr>
    <td width=200><a href="../init_dbf_sts/index.php">Initialisation des données à partir de fichiers DBF et XML</a></td>
    <td>Permet d'importer les données élèves, classes, professeurs, matières depuis deux fichiers DBF et l'export XML de STS.
    </td>
</tr>
<tr>
    <td width=200><a href="../init_xml/index.php">Initialisation des données à partir de fichiers XML</a></td>
    <td>Permet d'importer les données élèves, classes, professeurs, matières depuis les exports XML de Sconet/STS.
    </td>
</tr>
<tr>
    <td width=200><a href="../init_xml2/index.php">Initialisation des données à partir de fichiers XML</a></td>
    <td>Permet d'importer les données élèves, classes, professeurs, matières depuis les exports XML de Sconet/STS.<br />
	<b>Nouvelle procédure:</b> Plus simple et moins gourmande en ressources.
    </td>
</tr>
<?php
}
?>

<tr>
    <td width=200><a href="../initialisation/index.php">Initialisation des données à partir des fichiers GEP</a> (OBSOLETE)</td>
    <td>Permet d'importer les données élèves, classes, professeurs, matières depuis les fichiers GEP. Cette procédure est désormais obsolète avec la généralisation de Sconet.
    </td>
</tr>
</table>
</center>
<?php require("../lib/footer.inc.php");?>