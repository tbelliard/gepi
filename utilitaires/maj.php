<?php
/*
 * Last modification  : 29/09/2006
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// On indique qu'il faut crée des variables non protégées (voir fonction cree_variables_non_protegees())
// cela ici concerne le mot de passe
$variables_non_protegees = 'yes';

// Initialisations files
require_once ("../lib/initialisations.inc.php");

// Resume session
$resultat_session = resumeSession();

function traite_requete($requete = "") {
    global $pb_maj;
    $retour = "";
    $res = mysql_query($requete);
    $erreur_no = mysql_errno();
    if (!$erreur_no) {
        $retour = "";
    } else {
        switch ($erreur_no) {
            case "1060" :
                // le champ existe déjà : pas de problème
                $retour = "";
                break;
            case "1061" :
                // La cléf existe déjà : pas de problème
                $retour = "";
                break;
            case "1062" :
                // Présence d'un doublon : création de la cléf impossible
                $retour = "<font color=\"#FF0000\">Erreur (<b>non critique</b>) sur la requête : <i>" . $requete . "</i> (" . mysql_errno() . " : " . mysql_error() . ")</font><br />\n";
                $pb_maj = 'yes';
                break;
            case "1068" :
                // Des cléfs existent déjà : pas de problème
                $retour = "";
                break;
            case "1091" :
                // Déjà supprimé : pas de problème
                $retour = "";
                break;
            default :
                $retour = "<font color=\"#FF0000\">Erreur sur la requête : <i>" . $requete . "</i> (" . mysql_errno() . " : " . mysql_error() . ")</font><br />\n";
                $pb_maj = 'yes';
                break;
        }
    }
    return $retour;
}

$valid = isset ($_POST["valid"]) ? $_POST["valid"] : 'no';
$force_maj = isset ($_POST["force_maj"]) ? $_POST["force_maj"] : '';

// Numéro de version effective
$version_old = getSettingValue("version");
// Numéro de version RC effective
$versionRc_old = getSettingValue("versionRc");
// Numéro de version Beta effective
$versionBeta_old = getSettingValue("versionBeta");

$rc_old = '';
if ($versionRc_old != '')
    $rc_old = "-RC" . $versionRc_old;
$rc = '';
if ($gepiRcVersion != '')
    $rc = "-RC" . $gepiRcVersion;

$beta_old = '';
if ($versionBeta_old != '')
    $beta_old = "-beta" . $versionBeta_old;
$beta = '';
if ($gepiBetaVersion != '')
    $beta = "-beta" . $gepiBetaVersion;

if (isset ($_POST['submit'])) {
    if (isset ($_POST['login']) && isset ($_POST['no_anti_inject_password'])) {
        $md5password = md5($NON_PROTECT['password']);
        $sql = "select upper(login) login, password, prenom, nom, statut from utilisateurs where login = '" . $_POST['login'] . "' and password = '" . $md5password . "' and etat != 'inactif' and statut='administrateur' ";
        $res_user = sql_query($sql);
        $num_row = sql_count($res_user);
        if ($num_row == 1) {
            $valid = 'yes';
        } else {
            $message = "Identifiant ou mot de passe incorrect, ou bien vous n'êtes pas administrateur.";
        }
    }
}
?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
    <HTML>
    <HEAD>
    <meta HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1" />
    <META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
    <META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
    <META HTTP-EQUIV="Expires" CONTENT="0" />
    <link REL="stylesheet" href="../style.css" type="text/css" />
    <TITLE>Mise à jour de la base de donnée GEPI</TITLE>
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="icon" type="image/ico" href="../favicon.ico" />
    </head>
    <BODY>
<?php


if (($resultat_session == '0') and ($valid != 'yes')) {
?>
    <form action="maj.php" method='POST' style="width: 100%; margin-top: 24px; margin-bottom: 48px;">
    <div class="center">
    <H2 align="center"><?php echo "Mise à jour de la base de donnée GEPI<br />(Accès administrateur)"; ?></H2>

    <?php

    if (isset ($message)) {
        echo ("<p align=\"center\"><font color=red>" . $message . "</font></p>");
    }
?>
    <fieldset style="padding-top: 8px; padding-bottom: 8px; width: 40%; margin-left: auto; margin-right: auto;">
    <legend style="font-variant: small-caps;">Identifiez-vous</legend>
    <table style="width: 100%; border: 0;" cellpadding="5" cellspacing="0">
    <tr>
    <td style="text-align: right; width: 40%; font-variant: small-caps;"><label for="login">Identifiant</label></td>
    <td style="text-align: center; width: 60%;"><input type="text" name="login" size="16" /></td>
    </tr>
    <tr>
    <td style="text-align: right; width: 40%; font-variant: small-caps;"><label for="no_anti_inject_password">Mot de passe</label></td>
    <td style="text-align: center; width: 60%;"><input type="password" name="no_anti_inject_password" size="16" /></td>
    </tr>
    </table>
    <input type="submit" name="submit" value="Envoyer" style="font-variant: small-caps;" />
    </fieldset>
    </div>
    </form>
    </body>
    </html>
    <?php

    die();
};

if ((isset ($_SESSION['statut'])) and ($_SESSION['statut'] != 'administrateur')) {
    echo "<center><p class=grand><font color=red>Mise à jour de la base MySql de GEPI.<br />Vous n'avez pas les droits suffisants pour accéder à cette page.</font></p></center></body></html>";
    die();
}

if (isset ($_POST['maj'])) {
    $pb_maj = '';
    // On commence la mise à jour
    $mess = "Mise à jour effectuée.<br />(lisez attentivement le résultat de la mise à jour, en bas de cette page)";
    $result = '';
    $result_inter = '';
    // A effectuer quelquesoit la mise à jour
    $tab_req[] = "TRUNCATE droits;";
    $tab_req[] = "INSERT INTO droits VALUES ('/absences/index.php', 'F', 'F', 'V', 'F', 'F', 'V', 'Saisie des absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/absences/saisie_absences.php', 'F', 'F', 'V', 'F', 'F', 'V', 'Saisie des absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/accueil_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', ' ', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/accueil_modules.php', 'V', 'F', 'F', 'F', 'F', 'F', '', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/accueil.php', 'V', 'V', 'V', 'V', 'F', 'V', ' ', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/aid/add_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/aid/config_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/aid/export_csv_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/aid/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/aid/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/aid/index2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/aid/modify_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/bulletin/edit.php', 'F', 'V', 'F', 'V', 'F', 'F', 'Edition des bulletins', '1');";
    $tab_req[] = "INSERT INTO droits VALUES ('/bulletin/index.php', 'F', 'V', 'F', 'V', 'F', 'F', 'Edition des bulletins', '1');";
    $tab_req[] = "INSERT INTO droits VALUES ('/bulletin/param_bull.php', 'V', 'V', 'F', 'V', 'F', 'F', 'Edition des bulletins', '1');";
    $tab_req[] = "INSERT INTO droits VALUES ('/bulletin/verif_bulletins.php', 'F', 'V', 'F', 'V', 'F', 'F', 'Vérification du remplissage des bulletins', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/bulletin/verrouillage.php', 'F', 'F', 'F', 'V', 'F', 'F', '(de)Verrouillage des périodes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes_admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des carnets de notes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/add_modif_conteneur.php', 'F', 'V', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/add_modif_dev.php', 'F', 'V', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/saisie_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/toutes_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/visu_releve_notes.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation et impression des relevés de notes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_admin/admin_ct.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de texte', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de texte', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_admin/modify_limites.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de texte', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_admin/modify_type_doc.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de texte', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'Cahier de texte', '1');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte/traite_doc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'Cahier de texte', '1');";
    $tab_req[] = "INSERT INTO droits VALUES ('/classes/classes_ajout.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/classes/classes_const.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/classes/cpe_resp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Affectation des CPE aux classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/classes/duplicate_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/classes/eleve_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/classes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/classes/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/classes/modify_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/classes/modify_nom_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/classes/modify_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/classes/periodes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/classes/prof_suivi.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/eleves/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des élèves', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/eleves/import_eleves_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des élèves', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/eleves/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'Gestion des élèves', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/eleves/modify_eleve.php', 'V', 'F', 'F', 'V', 'F', 'F', 'Gestion des élèves', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/etablissements/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/etablissements/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/etablissements/modify_etab.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/gestion/accueil_sauve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Restauration, suppression et sauvegarde de la base', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/gestion/savebackup.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Téléchargement de sauvegardes la base', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/gestion/efface_base.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Restauration, suppression et sauvegarde de la base', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/gestion/gestion_connect.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des connexions', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/gestion/help_import.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/gestion/help.php', 'V', 'F', 'F', 'F', 'F', 'F', '', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/gestion/import_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/gestion/index.php', 'V', 'F', 'F', 'F', 'F', 'F', '', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/gestion/modify_impression.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des paramètres de la feuille de bienvenue', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/gestion/param_gen.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration générale', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/gestion/traitement_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/groupes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Edition des groupes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/groupes/add_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Ajout de groupes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/groupes/edit_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Edition de groupes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/groupes/edit_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Edition des élèves des groupes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/groupes/edit_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Edition des groupes de la classe', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/groupes/edit_class_grp_lot.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Affectation des matières aux professeurs', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_csv/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_csv/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_csv/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_csv/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_csv/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_csv/eleves_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_csv/prof_disc_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_csv/eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
    $tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
    $tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
    $tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
    $tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
    $tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
    $tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
    $tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
    $tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/disciplines_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
    $tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/prof_disc_classe_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
    $tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
    $tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/lecture_xml_sts_emp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
    $tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/init_pp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
    $tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/save_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_scribe/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_scribe/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_scribe/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_scribe/eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_scribe/prof_disc_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_scribe/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_lcs/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_lcs/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_lcs/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_lcs/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/init_lcs/affectations.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/initialisation/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/initialisation/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/initialisation/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/initialisation/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/initialisation/prof_disc_classe.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/initialisation/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/initialisation/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/initialisation/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/initialisation/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/initialisation/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/lib/confirm_query.php', 'V', 'F', 'F', 'F', 'F', 'F', '', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/matieres/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/matieres/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/matieres/matieres_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Importation des matières en CSV', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/matieres/matieres_categories.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Edition des catégories de matière', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/matieres/modify_matiere.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/matieres/matieres_param.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/edit_limite.php', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/help.php', 'V', 'V', 'V', 'V', 'F', 'F', '', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/index1.php', 'F', 'V', 'F', 'V', 'F', 'F', 'Visualisation des notes et appréciations', '1');";
    $tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation des notes par classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/index3.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/visu_aid.php', 'F', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes et appréciations AID', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/visu_toutes_notes.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation des notes par classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/responsables/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'Configuration et gestion des responsables élèves', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/responsables/modify_resp.php', 'V', 'F', 'F', 'V', 'F', 'F', 'Configuration et gestion des responsables élèves', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/saisie/help.php', 'F', 'V', 'F', 'F', 'F', 'V', '', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/saisie/import_class_csv.php', 'F', 'V', 'F', 'V', 'F', 'V', '', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/saisie/import_note_app.php', 'F', 'V', 'F', 'F', 'F', 'V', '', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/saisie/index.php', 'F', 'V', 'F', 'F', 'F', 'V', '', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_aid.php', 'F', 'V', 'F', 'F', 'F', 'V', 'Saisie des notes et appréciations AID', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_appreciations.php', 'F', 'V', 'F', 'F', 'F', 'V', 'Saisie des appréciations du bulletins', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_avis.php', 'F', 'V', 'F', 'V', 'F', 'V', 'Saisie des avis du conseil de classe', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_avis1.php', 'F', 'V', 'F', 'V', 'F', 'V', 'Saisie des avis du conseil de classe', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_avis2.php', 'F', 'V', 'F', 'V', 'F', 'V', 'Saisie des avis du conseil de classe', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_notes.php', 'F', 'V', 'F', 'F', 'F', 'V', 'Saisie des notes du bulletins', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/saisie/traitement_csv.php', 'F', 'V', 'F', 'F', 'F', 'V', 'Saisie des notes du bulletins', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/change_pwd.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/import_prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/impression_bienvenue.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/reset_passwords.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Réinitialisation des mots de passe', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/modify_user.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/mon_compte.php', 'V', 'V', 'V', 'V', 'F', 'V', 'Gestion du compte (informations personnelles, mot de passe, ...)', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/tab_profs_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Affectation des matieres aux professeurs', '')";
    $tab_req[] = "INSERT INTO droits VALUES ('/visualisation/classe_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/visualisation/eleve_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/visualisation/eleve_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/visualisation/evol_eleve_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/visualisation/evol_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/visualisation/index.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/visualisation/stats_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/classes/classes_param.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/fpdf/imprime_pdf.php', 'V', 'V', 'V', 'V', 'F', 'V', '', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/etablissements/import_etab_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/saisie/import_app_cons.php', 'F', 'V', 'F', 'V', 'F', 'F', 'Importation csv des avis du conseil de classe', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/messagerie/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'Gestion de la messagerie', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/absences/import_absences_gep.php', 'F', 'F', 'V', 'F', 'F', 'V', 'Saisie des absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/absences/seq_gep_absences.php', 'F', 'F', 'V', 'F', 'F', 'V', 'Saisie des absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/utilitaires/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Maintenance', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/gestion/contacter_admin.php', 'V', 'V', 'V', 'V', 'F', 'V', '', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/gestion_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'Gestion des absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/impression_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'Gestion des absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/select.php', 'F', 'F', 'V', 'F', 'F', 'F', 'Gestion des absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/ajout_ret.php', 'F', 'F', 'V', 'F', 'F', 'F', 'Gestion des absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/ajout_dip.php', 'F', 'F', 'V', 'F', 'F', 'F', 'Gestion des absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/ajout_inf.php', 'F', 'F', 'V', 'F', 'F', 'F', 'Gestion des absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/ajout_abs.php', 'F', 'F', 'V', 'F', 'F', 'F', 'Gestion des absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/bilan_absence.php', 'F', 'F', 'V', 'F', 'F', 'F', 'Gestion des absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/bilan.php', 'F', 'F', 'V', 'F', 'F', 'F', 'Gestion des absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/lettre_aux_parents.php', 'F', 'F', 'V', 'F', 'F', 'F', 'Gestion des absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/lib/tableau.php', 'F', 'V', 'V', 'V', 'F', 'F', '', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/admin/admin_motifs_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/admin/admin_periodes_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/lib/liste_absences.php', 'F', 'V', 'V', 'F', 'F', 'F', '', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/lib/graphiques.php', 'F', 'F', 'V', 'F', 'F', 'F', '', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/professeurs/prof_ajout_abs.php', 'F', 'V', 'F', 'F', 'F', 'F', 'Ajout des absences en classe', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_trombinoscopes/trombinoscopes.php', 'F', 'V', 'V', 'F', 'F', 'F', 'Visualiser le trombinoscope', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_trombinoscopes/trombi_impr.php', 'F', 'V', 'V', 'F', 'F', 'F', 'Visualiser le trombinoscope', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_trombinoscopes/trombinoscopes_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', '(des)activation du module trombinoscope', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/groupes/visu_profs_class.php', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualisation des équipes pédagogiques', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/groupes/popup.php', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualisation des équipes pédagogiques', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation des moyennes des carnets de notes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/visu_toutes_notes2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation des moyennes des carnets de notes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/utilitaires/verif_groupes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Vérification des incohérences d appartenances à des groupes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/visualisation/affiche_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/visualisation/draw_graphe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/groupes/mes_listes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'Accès aux CSV des listes d élèves', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/groupes/get_csv.php', 'F', 'V', 'V', 'V', 'F', 'V', 'Génération de CSV élèves', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/visualisation/choix_couleurs.php', 'V', 'F', 'F', 'V', 'F', 'F', 'Choix des couleurs des graphiques des résultats scolaires', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/visualisation/couleur.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Choix d une couleur pour le graphique des résultats scolaires', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/gestion/config_prefs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Définition des préférences d utilisateurs', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/utilitaires/recalcul_moy_conteneurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Recalcul des moyennes des conteneurs', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/classes/scol_resp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Affectation des comptes scolarité aux classes', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/lib/fiche_eleve.php', 'F', 'V', 'V', 'F', 'F', 'F', 'Fiche du suivie de l''élève', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_miseajour/utilisateur/fenetre.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des mises à jour', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_miseajour/admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Administration du module de mise à jour', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/referencement.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Référencement de Gepi sur la base centralisée des utilisateurs de Gepi', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/admin/admin_actions_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des actions absences', '');";
    // Pour un module non présent ni actif par défaut:
    $tab_req[] = "INSERT INTO droits VALUES ('/saisie/commentaires_types.php', 'V', 'V', 'V', 'V', 'F', 'V', 'Saisie de commentaires-types', '');";
    $tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/releve_pdf.php', 'V', 'V', 'F', 'V', 'F', 'V', 'Relevé de note au format PDF', '');";


	$tab_req[] = "INSERT INTO droits VALUES ('/impression/parametres_impression_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Impression des listes PDF; réglage des paramètres', '');";
	$tab_req[] = "INSERT INTO droits VALUES ('/impression/impression_serie.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Impression des listes (PDF) en série', '');";
	$tab_req[] = "INSERT INTO droits VALUES ('/impression/impression.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Impression rapide d une listes (PDF) ', '');";
	$tab_req[] = "INSERT INTO droits VALUES ('/impression/liste_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Impression des listes (PDF)', '');";

    //$tab_req[] = "";


    foreach ($tab_req as $key => $value) {
        $result .= traite_requete($value);
    }

    if (($force_maj == 'yes') or (quelle_maj("1.3.1"))) {
        $result .= "<b>Mise à jour jusqu'à la version 1.3.0 :</b><br />";
        $tab_req = array ();
        $tab_req[] = "ALTER IGNORE TABLE utilisateurs ADD change_mdp CHAR( 1 ) DEFAULT 'n' NOT NULL ;";
        $tab_req[] = "ALTER TABLE temp_gep_import ADD ELENOET VARCHAR( 40 ) NOT NULL AFTER ELEDATNAIS ;";
        $tab_req[] = "ALTER TABLE temp_gep_import ADD ERENO VARCHAR( 40 ) NOT NULL AFTER ELENOET;";
        $tab_req[] = "ALTER TABLE eleves ADD elenoet VARCHAR( 10 ) NOT NULL ;";
        $tab_req[] = "ALTER TABLE eleves ADD ereno VARCHAR( 10 ) NOT NULL ;";
        $tab_req[] = "CREATE TABLE IF NOT EXISTS responsables (ereno VARCHAR( 10 ) NOT NULL , nom1 VARCHAR( 20 ) NOT NULL , prenom1 VARCHAR( 20 ) NOT NULL ,adr1 VARCHAR( 100 ) NOT NULL , adr1_comp VARCHAR( 100 ) NOT NULL , commune1 VARCHAR( 50 ) NOT NULL ,cp1 VARCHAR( 6 ) NOT NULL ,nom2 VARCHAR( 20 ) NOT NULL, prenom2 VARCHAR( 20 ) NOT NULL ,adr2 VARCHAR( 100 ) NOT NULL , adr2_comp VARCHAR( 100 ) NOT NULL, commune2 VARCHAR( 50 ) NOT NULL ,cp2 VARCHAR( 6 ) NOT NULL);";

        foreach ($tab_req as $key => $value) {
            $result_inter .= traite_requete($value);
        }

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'duree_conservation_logs'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('duree_conservation_logs', '365');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'longmin_pwd'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('longmin_pwd', '5');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'gepi_prof_suivi'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('gepi_prof_suivi', 'professeur principal');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'GepiRubConseilProf'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('GepiRubConseilProf', 'yes');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'GepiRubConseilScol'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('GepiRubConseilScol', 'yes');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_ecart_entete'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('bull_ecart_entete', '0');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'page_garde_imprime'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('page_garde_imprime', 'no'); ");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'page_garde_texte'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('page_garde_texte', 'Madame, Monsieur,\r\n\r\nVeuillez trouvez ci-joint le bulletin scolaire de votre enfant. Nous vous rappelons que la journée __Portes ouvertes__ du Lycée aura lieu samedi 20 mai entre 10 h et 17 h.\r\n\r\nVeuillez agréer, Madame, Monsieur, l\'expression de mes meilleurs sentiments.\r\n\r\n\r\n|<p style=\"text-align: right;\">Le proviseur</style>');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'page_garde_padding_top'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('page_garde_padding_top', '4');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'page_garde_padding_left'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('page_garde_padding_left', '11');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'page_garde_padding_text'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('page_garde_padding_text', '6');");

        $req = sql_query1("SELECT VALUE FROM setting WHERE NAME='version'");
        if ($req == -1) {
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('version', '$gepiVersion');");
        } else {
            $result_inter .= traite_requete("UPDATE setting SET VALUE='$gepiVersion' WHERE NAME='version';");
        }
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = '';
    }

    // version Gepi_1.3.1
    if (($force_maj == 'yes') or (quelle_maj("1.3.1"))) {
        $result .= "<b><br />Mise à jour vers la version 1.3.1 : </b><br />";
        $tab_req = array ();
        $tab_req[] = "ALTER TABLE utilisateurs ADD civilite CHAR( 5 ) NOT NULL AFTER prenom;";
        $tab_req[] = "ALTER TABLE classes ADD format_nom CHAR( 5 ) NOT NULL ;";
        $tab_req[] = "ALTER TABLE matieres ADD priority SMALLINT NOT NULL ;";
        $tab_req[] = "ALTER TABLE classes ADD display_rang CHAR( 1 ) DEFAULT 'n' NOT NULL ;";

        foreach ($tab_req as $key => $value) {
            $result_inter .= traite_requete($value);
        }
        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_espace_avis'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('bull_espace_avis', '5');");
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = '';
    }
    // version Gepi_1.3.2
    if (($force_maj == 'yes') or (quelle_maj("1.3.2"))) {
        $result .= "<b><br />Mise à jour vers la version 1.3.2 :</b><br />";

        //Changement des priorités d'affichage
        $req = sql_query1("SELECT VALUE FROM setting WHERE NAME='change_ordre_aff_matieres'");
        if ($req == -1) {
            // On passe de l'affichage selon le "poids" à l'affichage selon la "priorité"
            $req = mysql_query("ALTER TABLE j_classes_matieres_professeurs ADD temp CHAR( 1 ) DEFAULT 'n' NOT NULL ;");
            $req = mysql_query("ALTER TABLE matieres ADD temp CHAR( 1 ) DEFAULT 'n' NOT NULL ;");
            $l = 11;
            while ($l < 51) {
                $new = 61 - $l;
                $maj = mysql_query("UPDATE j_classes_matieres_professeurs set priorite='" . $new . "', temp='y'
                                where (
                                temp = 'n' and
                                priorite='" . $l . "'
                                )
                                ");

                $maj = mysql_query("UPDATE matieres set priority='" . $new . "', temp='y'
                                where (
                                temp = 'n' and
                                priority='" . $l . "'
                                )
                                ");

                $l++;
            }
            $result_inter .= traite_requete("UPDATE j_classes_matieres_professeurs set priorite='50' where priorite='0'");
            $result_inter .= traite_requete("UPDATE matieres set priority='50' where priority='0'");
            $result_inter .= traite_requete("ALTER TABLE j_classes_matieres_professeurs DROP temp;");
            $result_inter .= traite_requete("ALTER TABLE matieres DROP temp;");

            // On re-numérote à partir de 1 j_classes_matieres_professeurs
            $l = 11;
            $new = 11;
            while ($l < 51) {
                $test_query = mysql_query("SELECT priorite from j_classes_matieres_professeurs where priorite = '" . $l . "'");
                $result_test = mysql_num_rows($test_query);
                if ($result_test != 0) {
                    $maj = mysql_query("UPDATE j_classes_matieres_professeurs set priorite='" . $new . "'
                                        where (
                                        priorite='" . $l . "'
                                        )
                                       ");
                    $new++;
                }
                $l++;
            }
            // On re-numérote à partir de 1 matieres
            $l = 11;
            $new = 11;
            while ($l < 51) {
                $test_query = mysql_query("SELECT priority from matieres where priority = '" . $l . "'");
                $result_test = mysql_num_rows($test_query);
                if ($result_test != 0) {
                    $maj = mysql_query("UPDATE matieres set priority='" . $new . "'
                                        where (
                                        priority='" . $l . "'
                                        )
                                       ");
                    $new++;
                }
                $l++;
            }
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('change_ordre_aff_matieres', 'ok');");
            $mess .= "<br /><br />Pour tenir compte d'un changement sur l'ordre d'affichage des matières, les priorités d'affichage ont été modifiées. Il est conseillé de vérifier que l'ordre d'affichage des matières est toujours conforme à vos réglages initiaux";
        }
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = '';
    }
    //
    // version Gepi_1.3.3
    //
    if (($force_maj == 'yes') or (quelle_maj("1.3.3"))) {
        $result .= "<b><br />Mise à jour vers la version 1.3.3 :</b><br />";
        $result_inter .= traite_requete("CREATE TABLE IF NOT EXISTS messages (id int(11) NOT NULL auto_increment, texte text NOT NULL, date_debut int(11) NOT NULL default '0', date_fin int(11) NOT NULL default '0', auteur varchar(20) NOT NULL default '', destinataires varchar(10) NOT NULL default '', PRIMARY KEY  (id) );");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'disable_login'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('disable_login', 'no');");

        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = '';
    }
    //
    // version Gepi_1.3.4
    //
    if (($force_maj == 'yes') or (quelle_maj("1.3.4"))) {
        $result .= "<b><br />Mise à jour vers la version 1.3.4 :</b><br />";
        $result .= "&nbsp;->Première étape de mise à jour : <br />";

        $query = mysql_query("CREATE TABLE IF NOT EXISTS j_eleves_cpe (e_login varchar(50) NOT NULL default '', cpe_login varchar(50) NOT NULL default '', PRIMARY KEY  (e_login,cpe_login)) TYPE=MyISAM;");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_formule_bas'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('bull_formule_bas', 'Bulletin à conserver précieusement. Aucun duplicata ne sera délivré. - GEPI : solution libre de gestion et de suivi des résultats scolaires.');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'delai_devoirs'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('delai_devoirs', '7');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'GepiProfImprBul'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('GepiProfImprBul', 'no');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'GepiProfImprBulSettings'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('GepiProfImprBulSettings', 'no');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'GepiScolImprBulSettings'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('GepiScolImprBulSettings', 'yes');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'GepiAdminImprBulSettings'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('GepiAdminImprBulSettings', 'no');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'GepiAccesReleveScol'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('GepiAccesReleveScol','yes');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'GepiAccesReleveProfP'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('GepiAccesReleveProfP', 'yes');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'GepiAccesReleveProf'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('GepiAccesReleveProf', 'no');");

        $result_inter .= traite_requete("CREATE TABLE IF NOT EXISTS ct_devoirs_entry (id_ct int(11) NOT NULL auto_increment, id_matiere varchar(32) NOT NULL default '', id_classe int(11) NOT NULL default '0', date_ct int(11) NOT NULL default '0', id_login varchar(32) NOT NULL default '', contenu text NOT NULL, KEY id_ct (id_ct));");

        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = '';

        $result .= "&nbsp;->Deuxième étape : optimisation des tables : <br />";

        $tab_req = array ();
        $tab_req[] = "ALTER TABLE matieres_notes ADD rang SMALLINT NOT NULL ;";
        $tab_req[] = "ALTER TABLE j_eleves_classes ADD rang SMALLINT DEFAULT '0' NOT NULL;";
        $tab_req[] = "CREATE TABLE IF NOT EXISTS absences_gep (id_seq char(2) NOT NULL default '', type char(1) NOT NULL default '');";
        $tab_req[] = "ALTER TABLE absences CHANGE login login VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE absences CHANGE nb_absences nb_absences CHAR(3) NOT NULL;";
        $tab_req[] = "ALTER TABLE absences CHANGE non_justifie non_justifie CHAR(3) NOT NULL;";
        $tab_req[] = "ALTER TABLE absences CHANGE nb_retards nb_retards CHAR(3) NOT NULL;";
        $tab_req[] = "ALTER TABLE aid CHANGE id id VARCHAR( 100 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE aid_appreciations CHANGE login login VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE aid_appreciations CHANGE id_aid id_aid VARCHAR( 100 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE aid_appreciations DROP assiduite , DROP investissement , DROP participation_creatrice;";
        $tab_req[] = "ALTER TABLE aid_appreciations CHANGE periode periode INT NOT NULL;";
        $tab_req[] = "ALTER TABLE aid_appreciations CHANGE statut statut VARCHAR( 10 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE avis_conseil_classe CHANGE login login VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE avis_conseil_classe CHANGE periode periode INT NOT NULL;";
        $tab_req[] = "ALTER TABLE avis_conseil_classe CHANGE avis avis TEXT NOT NULL;";
        $tab_req[] = "ALTER TABLE avis_conseil_classe CHANGE statut statut VARCHAR( 10 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE classes CHANGE classe classe VARCHAR( 100 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE cn_notes_conteneurs CHANGE login login VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE cn_notes_devoirs CHANGE login login VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE droits CHANGE id id VARCHAR( 200 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE droits CHANGE administrateur administrateur VARCHAR( 1 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE droits CHANGE professeur professeur VARCHAR( 1 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE droits CHANGE cpe cpe VARCHAR( 1 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE droits CHANGE scolarite scolarite VARCHAR( 1 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE droits CHANGE eleve eleve VARCHAR( 1 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE droits CHANGE secours secours VARCHAR( 1 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE droits CHANGE description description VARCHAR( 255 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE eleves CHANGE login login VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE `eleves` CHANGE `nom` `nom` VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE `eleves` CHANGE `prenom` `prenom` VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE `eleves` CHANGE `sexe` `sexe` VARCHAR( 1 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE j_aid_eleves CHANGE id_aid id_aid VARCHAR( 100 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE j_aid_utilisateurs CHANGE id_aid id_aid VARCHAR( 100 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE j_aid_utilisateurs CHANGE id_utilisateur id_utilisateur VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE j_eleves_classes CHANGE login login VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE j_eleves_etablissements CHANGE id_eleve id_eleve VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE j_eleves_professeurs CHANGE login login VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE j_eleves_professeurs CHANGE professeur professeur VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE j_eleves_regime CHANGE login login VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE j_eleves_regime CHANGE doublant doublant CHAR( 1 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE j_eleves_regime CHANGE regime regime CHAR( 5 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE j_professeurs_matieres CHANGE id_professeur id_professeur VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE log CHANGE LOGIN LOGIN VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE matieres CHANGE matiere matiere VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE matieres CHANGE nom_complet nom_complet VARCHAR( 200 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE matieres_appreciations CHANGE login login VARCHAR( 50 ) NOT NULL;";
        #$tab_req[] = "ALTER TABLE matieres_appreciations CHANGE matiere matiere VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE matieres_appreciations CHANGE periode periode INT NOT NULL;";
        $tab_req[] = "ALTER TABLE matieres_notes CHANGE login login VARCHAR( 50 ) NOT NULL;";
        #$tab_req[] = "ALTER TABLE matieres_notes CHANGE matiere matiere VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE matieres_notes CHANGE periode periode INT NOT NULL;";
        $tab_req[] = "ALTER TABLE matieres_notes CHANGE statut statut VARCHAR( 10 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE messages CHANGE auteur auteur VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE periodes CHANGE nom_periode nom_periode VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE periodes CHANGE num_periode num_periode INT NOT NULL;";
        $tab_req[] = "ALTER TABLE periodes CHANGE verouiller verouiller CHAR( 1 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE periodes CHANGE id_classe id_classe INT DEFAULT '0' NOT NULL;";
        $tab_req[] = "ALTER TABLE utilisateurs CHANGE login login VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE utilisateurs CHANGE nom nom VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE utilisateurs CHANGE prenom prenom VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE utilisateurs CHANGE password password CHAR( 32 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE utilisateurs CHANGE email email VARCHAR( 50 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE utilisateurs CHANGE etat etat VARCHAR( 20 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE utilisateurs CHANGE statut statut VARCHAR( 20 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE `cn_conteneurs` CHANGE `description` `description` VARCHAR( 128 ) NOT NULL;";
        $tab_req[] = "ALTER TABLE `cn_devoirs` CHANGE `description` `description` VARCHAR( 128 ) NOT NULL;";

        foreach ($tab_req as $key => $value) {
            $result_inter .= traite_requete($value);
        }
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = '';

        // Ajout des clefs primaires
        $result .= "&nbsp;->Troisième étape : création des clefs primaires : <br />";
        $tab_req = array ();
        $tab_req[] = "ALTER TABLE absences ADD PRIMARY KEY ( login , periode );";
        $tab_req[] = "ALTER TABLE absences_gep ADD PRIMARY KEY ( id_seq );";
        $tab_req[] = "ALTER TABLE aid ADD PRIMARY KEY ( id );";
        $tab_req[] = "ALTER TABLE aid_appreciations ADD PRIMARY KEY ( login , id_aid , periode );";
        $tab_req[] = "ALTER TABLE avis_conseil_classe ADD PRIMARY KEY ( login , periode );";
        $tab_req[] = "ALTER TABLE droits ADD PRIMARY KEY ( id );";
        $tab_req[] = "ALTER TABLE cn_notes_conteneurs ADD PRIMARY KEY ( login , id_conteneur );";
        $tab_req[] = "ALTER TABLE cn_notes_devoirs ADD PRIMARY KEY ( login , id_devoir );";
        $tab_req[] = "ALTER TABLE eleves ADD PRIMARY KEY ( login );";
        $tab_req[] = "ALTER TABLE aid_config ADD PRIMARY KEY ( indice_aid );";
        $tab_req[] = "ALTER TABLE j_aid_eleves ADD PRIMARY KEY ( id_aid , login );";
        $tab_req[] = "ALTER TABLE j_aid_utilisateurs ADD PRIMARY KEY ( id_aid , id_utilisateur );";
        $tab_req[] = "ALTER TABLE j_eleves_classes ADD PRIMARY KEY ( login , id_classe , periode );";
        $tab_req[] = "ALTER TABLE j_eleves_etablissements ADD PRIMARY KEY ( id_eleve , id_etablissement );";
        $tab_req[] = "ALTER TABLE j_eleves_professeurs ADD PRIMARY KEY ( login , professeur , id_classe );";
        $tab_req[] = "ALTER TABLE j_eleves_regime ADD PRIMARY KEY ( login );";
        $tab_req[] = "ALTER TABLE j_professeurs_matieres ADD PRIMARY KEY ( id_professeur , id_matiere );";
        $tab_req[] = "ALTER TABLE matieres ADD PRIMARY KEY ( matiere );";
        $tab_req[] = "ALTER TABLE matieres_appreciations ADD PRIMARY KEY ( login , matiere , periode );";
        $tab_req[] = "ALTER TABLE matieres_notes ADD PRIMARY KEY ( login , matiere , periode );";
        $tab_req[] = "ALTER TABLE periodes ADD PRIMARY KEY ( num_periode , id_classe );";
        $tab_req[] = "ALTER TABLE responsables ADD PRIMARY KEY ( ereno );";
        $tab_req[] = "ALTER TABLE utilisateurs ADD PRIMARY KEY ( login );";

        foreach ($tab_req as $key => $value) {
            $result_inter .= traite_requete($value);
        }
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
            $result .= "<br /><b>Remarque : </b> Afin de régler le problème ci-dessus de création des clefs primaires,
                        vous pouvez lancer la <b><a href='./clean_tables.php'>procédure de nettoyage des tables de liaison</a></b> puis recommencer la mise à jour en <b>forçant la procédure</b>.";

        }
        $result_inter = '';
        $result .= "<br /><b>Remarque :</b> la version 1.3.4 intègre une fonctionalité d'attribution de CPE responsable du suivi pour chaque élève. Vous devez donc attribuer un CPE aux élèves. Tant que vous n'aurez pas effectué cette opération, les CPE n'auront pas accès à leurs outils respectifs. Rendez-vous dans /Gestion des bases/Gestion des classes/ et utilisez le lien 'Paramétrage rapide CPE responsable' pour attribuer automatiquement les CPE aux élèves des classes que vous sélectionnerez.";
    }
    //
    // version Gepi_1.4.0
    //

    if (($force_maj == 'yes') or (quelle_maj("1.4.0"))) {
        $result .= "<br /><br /><b>Mise à jour vers la version 1.4.0 :</b><br />";

        $result .= "&nbsp;->Création de la table absences_eleves <br />";
        $query = mysql_query("CREATE TABLE IF NOT EXISTS `absences_eleves` (`id_absence_eleve` int(11) NOT NULL auto_increment, `type_absence_eleve` char(1) NOT NULL default '', `eleve_absence_eleve` varchar(25) NOT NULL default '0', `justify_absence_eleve` char(3) NOT NULL default '', `info_justify_absence_eleve` text NOT NULL, `motif_absence_eleve` varchar(4) NOT NULL default '', `info_absence_eleve` text NOT NULL, `d_date_absence_eleve` date NOT NULL default '0000-00-00', `a_date_absence_eleve` date default NULL, `d_heure_absence_eleve` time default NULL, `a_heure_absence_eleve` time default NULL, `saisie_absence_eleve` varchar(50) NOT NULL default '', PRIMARY KEY  (`id_absence_eleve`)) TYPE=MyISAM AUTO_INCREMENT=57;");
        if ($query)
            $result .= "<font color=\"green\">Ok !</font><br />";

        $result .= "&nbsp;->Tentative d'ajout d'un champ dans la table absences_eleves (si ça ne marche pas, c'est simplement que la table existe déjà...) <br />";
        $queryb = mysql_query("ALTER TABLE `absences_eleves` ADD `saisie_absence_eleve` varchar(50) NOT NULL default ''");
        if ($queryb)
            $result .= "<font color=\"green\">Ok !</font><br />";

        $result .= "&nbsp;->Changement de la structure de la table 'classes'<br />";
        $querybis = mysql_query("ALTER TABLE `classes` ADD `display_address` CHAR( 1 ) DEFAULT 'n' NOT NULL , ADD `display_coef` CHAR( 1 ) DEFAULT 'y' NOT NULL");
        if ($querybis)
            $result .= "<font color=\"green\">Ok !</font><br />";

        $result .= "&nbsp;->Insertion de nouveaux paramètres<br />";

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'gepiSchoolTel'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO `setting` VALUES ('gepiSchoolTel', '00 00 00 00 00');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'gepiSchoolFax'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO `setting` VALUES ('gepiSchoolFax', '00 00 00 00 00');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'gepiSchoolEmail'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO `setting` VALUES ('gepiSchoolEmail', 'ce.XXXXXXXX@ac-xxxxx.fr');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'param_module_trombinoscopes'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO setting VALUES ('param_module_trombinoscopes', 'no_gep');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_padding_right'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO setting VALUES ('addressblock_padding_right', '2');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_padding_top'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO setting VALUES ('addressblock_padding_top', '4');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_padding_text'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO setting VALUES ('addressblock_padding_text', '1');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_length'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO setting VALUES ('addressblock_length', '6');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'GepiAccesReleveCpe'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO setting VALUES ('GepiAccesReleveCpe','no');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'col_boite_largeur'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO setting VALUES ('col_boite_largeur','120');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_mention_doublant'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO setting VALUES ('bull_mention_doublant','no');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_affiche_numero'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO setting VALUES ('bull_affiche_numero','no');");

        $result .= "&nbsp;->Création de la table suivi_eleve_cpe <br />";
        $query = mysql_query("CREATE TABLE IF NOT EXISTS `suivi_eleve_cpe` (`id_suivi_eleve_cpe` int(11) NOT NULL auto_increment, `eleve_suivi_eleve_cpe` varchar(30) NOT NULL default '', `date_suivi_eleve_cpe` date NOT NULL default '0000-00-00', `komenti_suivi_eleve_cpe` text NOT NULL, PRIMARY KEY  (`id_suivi_eleve_cpe`)) TYPE=MyISAM;");
        if ($query)
            $result .= "<font color=\"green\">Ok !</font><br />";
    }

    if (($force_maj == 'yes') or (quelle_maj("1.4.1"))) {
        $result .= "<br /><br /><b>Mise à jour vers la version 1.4.1 :</b><br />";

        $result .= "&nbsp;->Tentative de modification du champ AUTOCLOSE dans la table log.<br />";
        $result_inter = traite_requete("ALTER TABLE `log` CHANGE `AUTOCLOSE` `AUTOCLOSE` ENUM( '0', '1', '2' ) DEFAULT '0' NOT NULL");
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }

        $result .= "&nbsp;->Tentative d'ajout d'un champ dans la table utilisateurs.<br />";
        $result_inter = traite_requete("ALTER TABLE `utilisateurs` ADD `date_verrouillage` datetime NOT NULL default '2006-01-01 00:00:00'");
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
        $result_inter = "";
        $result .= "&nbsp;->Insertion de nouveaux paramètres dans la table setting<br />";
        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'temps_compte_verrouille'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO `setting` VALUES ('temps_compte_verrouille', '30');");
        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'nombre_tentatives_connexion'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO `setting` VALUES ('nombre_tentatives_connexion', '10');");

        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }

        $result .= "&nbsp;->On force tous les utilisateurs à mettre à jour leur mot de passe (sécurité)<br />";

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'version'");
        $res_test = mysql_result($req_test, "0");

        if ($res_test < "1.4.1")
            $result_inter .= traite_requete("UPDATE utilisateurs SET change_mdp='y';");

        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }

    }

    if (($force_maj == 'yes') or (quelle_maj("1.4.2"))) {
        $result .= "<br /><br /><b>Mise à jour vers la version 1.4.2 :</b><br />";
        $result .= "&nbsp;->Tentative de modification du champ AUTOCLOSE dans la table log.<br />";
        $result_inter = traite_requete("ALTER TABLE `log` CHANGE `AUTOCLOSE` `AUTOCLOSE` ENUM( '0', '1', '2', '3', '4' ) DEFAULT '0' NOT NULL");
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }
    }

    if (($force_maj == 'yes') or (quelle_maj("1.4.2.1"))) {
        $result .= "<br /><br /><b>Mise à jour vers la version 1.4.2.1 :</b><br />";
        $result .= "&nbsp;->Création de la table absences_creneaux <br />";
        $query2 = mysql_query("CREATE TABLE IF NOT EXISTS `absences_creneaux` (`id_definie_periode` int(11) NOT NULL auto_increment, `nom_definie_periode` varchar(10) NOT NULL default '', `heuredebut_definie_periode` time NOT NULL default '00:00:00', `heurefin_definie_periode` time NOT NULL default '00:00:00', PRIMARY KEY  (`id_definie_periode`)) TYPE=MyISAM AUTO_INCREMENT=42;");
        if ($query2)
            $result .= "<font color=\"green\">Ok !</font><br />";

        $result .= "&nbsp;->Insertion de valeurs par défaut :";
        $test = mysql_result(mysql_query("SELECT count(*) FROM absences_creneaux"), "0");
        if ($test == "0") {
            $result .= "&nbsp;Oui<br />";
            $tab_req = array ();
            $tab_req[] = "INSERT INTO `absences_creneaux` VALUES (1, 'M1', '08:00:00', '08:55:00');";
            $tab_req[] = "INSERT INTO `absences_creneaux` VALUES (2, 'M2', '08:55:00', '09:50:00');";
            $tab_req[] = "INSERT INTO `absences_creneaux` VALUES (3, 'M3', '10:05:00', '11:00:00');";
            $tab_req[] = "INSERT INTO `absences_creneaux` VALUES (4, 'M4', '11:00:00', '11:55:00');";
            $tab_req[] = "INSERT INTO `absences_creneaux` VALUES (5, 'S1', '13:30:00', '14:25:00');";
            $tab_req[] = "INSERT INTO `absences_creneaux` VALUES (6, 'S2', '14:25:00', '15:20:00');";
            $tab_req[] = "INSERT INTO `absences_creneaux` VALUES (7, 'S3', '15:35:00', '16:30:00');";
            $tab_req[] = "INSERT INTO `absences_creneaux` VALUES (8, 'S4', '16:30:00', '17:30:00');";
            $tab_req[] = "INSERT INTO `absences_creneaux` VALUES (32, 'M5', '11:55:00', '12:30:00');";
            $tab_req[] = "INSERT INTO `absences_creneaux` VALUES (31, 'P1', '09:50:00', '10:05:00');";
            $tab_req[] = "INSERT INTO `absences_creneaux` VALUES (33, 'R', '12:00:00', '13:00:00');";
            $tab_req[] = "INSERT INTO `absences_creneaux` VALUES (34, 'R1', '13:00:00', '13:30:00');";
            $tab_req[] = "INSERT INTO `absences_creneaux` VALUES (35, 'P2', '15:20:00', '15:35:00');";
            $tab_req[] = "INSERT INTO `absences_creneaux` VALUES (36, 'S5', '17:30:00', '18:25:00');";

            foreach ($tab_req as $key => $value) {
                $result_inter .= traite_requete($value);
            }
            if ($result_inter == '') {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= $result_inter;
            }
            $result_inter = '';
        } else {
            $result .= "&nbsp;<font color=blue>Non (la table n'est pas vide)</font><br />";
        }

        $result .= "&nbsp;->Création de la table absences_motifs <br />";
        $query = mysql_query("CREATE TABLE IF NOT EXISTS `absences_motifs` (`id_motif_absence` int(11) NOT NULL auto_increment, `init_motif_absence` char(2) NOT NULL default '', `def_motif_absence` varchar(255) NOT NULL default '', PRIMARY KEY  (`id_motif_absence`)) TYPE=MyISAM AUTO_INCREMENT=33 ;");
        if ($query)
            $result .= "<font color=\"green\">Ok !</font><br />";
        $result .= "&nbsp;Insertion de valeurs par défaut :";

        $test = mysql_result(mysql_query("SELECT count(*) FROM absences_motifs"), "0");
        if ($test == "0") {
            $result .= "&nbsp;Oui<br />";
            $tab_req = array ();
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (1, 'A', 'aucun motif');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (2, 'AS', 'accident sport');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (3, 'AT', 'non présent en retenue');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (4, 'C', 'sur la cour');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (5, 'CF', 'convenances familiales');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (6, 'CO', 'convocation bureau');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (7, 'CS', 'competition sportive');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (8, 'DI', 'dispense d''e.p.s.');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (9, 'ET', 'erreur d''emploie du temps');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (10, 'EX', 'examen');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (11, 'H', 'Hospitalisé(e)');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (12, 'JP', 'justifie par le principal');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (13, 'MA', 'Maladie');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (14, 'OR', 'conseiller');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (15, 'PR', 'reveil');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (16, 'RC', 'refus de venir en cours');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (17, 'RE', 'renvoye');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (18, 'RT', 'présent en retenue');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (19, 'RV', 'renvoi du cours');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (20, 'SM', 'refus de justification');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (21, 'SP', 'sorite pédagogique');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (22, 'ST', 'stage à l''extèrieur');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (23, 'T', 'téléphone');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (24, 'TR', 'transport');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (25, 'VM', 'visite médical');";
            $tab_req[] = "INSERT INTO `absences_motifs` VALUES (26, 'IN', 'infirmerie');";
            $tab_req[] = "INSERT INTO `ct_types_documents` ( `id_type` , `titre` , `extension` , `upload` ) VALUES ( '', 'Texte OpenDocument', 'odt', 'oui' );";
            $tab_req[] = "INSERT INTO `ct_types_documents` ( `id_type` , `titre` , `extension` , `upload` ) VALUES ( '', 'Classeur OpenDocument', 'ods', 'oui' );";
            $tab_req[] = "INSERT INTO `ct_types_documents` ( `id_type` , `titre` , `extension` , `upload` ) VALUES ( '', 'Présentation OpenDocument', 'odp', 'oui' );";
            $tab_req[] = "INSERT INTO `ct_types_documents` ( `id_type` , `titre` , `extension` , `upload` ) VALUES ( '', 'Dessin OpenDocument', 'odg', 'oui' );";
            $tab_req[] = "INSERT INTO `ct_types_documents` ( `id_type` , `titre` , `extension` , `upload` ) VALUES ( '', 'Base de données OpenDocument', 'odb', 'oui' );";
            foreach ($tab_req as $key => $value) {
                $result_inter .= traite_requete($value);
            }
            if ($result_inter == '') {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= $result_inter;
            }
            $result_inter = '';

        } else {
            $result .= "&nbsp;<font color=blue>Non (la table n'est pas vide)</font><br />";
        }
        $test = sql_query1("select count(id_definie_periode) from definie_periodes");
        if ($test != -1) {
            $result .= "&nbsp;->Tentative de suppression de la table definie_periodes (si erreur, c'est probablement que la table n'existe plus).<br />";
            $result_inter = traite_requete("DROP TABLE definie_periodes");
            if ($result_inter == '') {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= $result_inter;
            }
        }

        $test = sql_query1("select count(id_motif_absence) from motif_absence");
        if ($test != -1) {
            $result .= "&nbsp;->Tentative de suppression de la table motif_absence (si erreur, c'est probablement que la table n'existe plus).<br />";
            $result_inter = traite_requete("DROP TABLE motif_absence");
            if ($result_inter == '') {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= $result_inter;
            }
        }

        $result .= "&nbsp;->Tentative d'ajout du champ heure_retard_eleve à la table absences_eleves.<br />";
        $result_inter = traite_requete("ALTER TABLE absences_eleves ADD heure_retard_eleve TIME NOT NULL ;");
        if ($result_inter == '') {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= $result_inter;
        }

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'active_module_absence'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO `setting` VALUES ('active_module_absence', 'y');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'active_module_absence_professeur'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO setting VALUES ('active_module_absence_professeur', 'y');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'active_module_trombinoscopes'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result .= traite_requete("INSERT INTO setting VALUES ('active_module_trombinoscopes', 'y');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_affiche_appreciations'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('bull_affiche_appreciations', 'y');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_affiche_absences'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('bull_affiche_absences', 'y');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_affiche_avis'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('bull_affiche_avis', 'y');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_affiche_aid'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('bull_affiche_aid', 'y');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_affiche_formule'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('bull_affiche_formule', 'y');");

        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_affiche_signature'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0)
            $result_inter .= traite_requete("INSERT INTO setting VALUES ('bull_affiche_signature', 'y');");

    }

    if (($force_maj == 'yes') or (quelle_maj("1.4.3"))) {
        $result .= "<br /><br /><b>Mise à jour vers la version 1.4.3 :</b><br />";

/*
 * ATTENTION ! ICI se trouve une update de la 1.4.4 sur les catégories de matière.
 * La raison : pour ceux qui mettent à jour depuis la 1.4.2. En effet la mise à jour des groupes
 * utilise la lib groupes.inc.php, qui, dans ce paquetage, a été modifiée pour prendre en compte
 * les catégories de matière. La procédure de mise à jour est donc problématique
 * pour une mise à jour d'une version inférieure à la 1.4.3 vers la 1.4.4.
 */

        $result .= "&nbsp;->Création de la table matieres_categories<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'matieres_categories'"));
        if ($test1 == 0) {
            $query1 = mysql_query("CREATE TABLE IF NOT EXISTS `matieres_categories` (`id` int(11) NOT NULL auto_increment, `nom_court` varchar(255) NOT NULL default '', `nom_complet` varchar(255) NOT NULL default '', `priority` smallint(6) NOT NULL default '0', PRIMARY KEY  (`id`))");
            if ($query1) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">La table existe déjà.</font><br />";
        }

        $result .= "&nbsp;->Insertion de la catégorie de matière par défaut<br />";
        $test = mysql_result(mysql_query("SELECT count(id) FROM matieres_categories WHERE id='1'"),0);
        if ($test == 0) {
            $query1b = mysql_query("INSERT INTO `matieres_categories` SET id = '1', nom_court = 'Autres', nom_complet = 'Autres', priority = '7'");
            if ($query1b) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">La matière par défaut existe déjà.</font><br />";
        }

        $result .= "&nbsp;->Création de la table j_matieres_categories_classes<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'j_matieres_categories_classes'"));
        if ($test1 == 0) {
            $query2 = mysql_query("CREATE TABLE IF NOT EXISTS `j_matieres_categories_classes` (`categorie_id` int(11) NOT NULL default '0', `classe_id` int(11) NOT NULL default '0', `priority` smallint(6) NOT NULL default '0', `affiche_moyenne` tinyint(1) NOT NULL default '0', PRIMARY KEY  (`categorie_id`,`classe_id`))");
            if ($query2) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">La table existe déjà.</font><br />";
        }

        $result .= "&nbsp;->Ajout du champ categorie_id à la table matieres<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM matieres LIKE 'categorie_id'"));
        if ($test1 == 0) {
            $query3 = mysql_query("ALTER TABLE `matieres` ADD `categorie_id` INT NOT NULL default '1' AFTER `priority`");
            if ($query3) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
        }

        $result .= "&nbsp;->Ajout du champ display_mat_cat à la table classes<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'display_mat_cat'"));
        if ($test1 == 0) {
            $query4 = mysql_query("ALTER TABLE `classes` ADD `display_mat_cat` CHAR(1) NOT NULL default 'n' AFTER `display_coef`");
            if ($query4) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
        }

        /*
         * FIN DE LA PARTIE COPIEE DE LA PROCEDURE DE LA 1.4.4
         *
         * ===================================================
         */




        $result .= "&nbsp;->Création de la table groupes <br />";
        $query1 = mysql_query("CREATE TABLE IF NOT EXISTS `groupes` (`id` int(11) NOT NULL auto_increment, `name` varchar(60) NOT NULL default '', `description` text NOT NULL, PRIMARY KEY  (`id`))");
        if ($query1) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur</font><br />";
        }

        $result .= "&nbsp;->Création de la table j_groupes_classes<br />";
        $test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'j_groupes_classes'"));
        if ($test == 0) {
            $query2 = mysql_query("CREATE TABLE IF NOT EXISTS `j_groupes_classes` (`id_groupe` int(11) NOT NULL default '0', `id_classe` int(11) NOT NULL default '0', `priorite` smallint(6) NOT NULL, `coef` decimal(3,1) NOT NULL, PRIMARY KEY (`id_groupe`, `id_classe`))");
            if ($query2) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">La table existe déjà</font><br />";
        }

    /*
     * AJOUT DEPUIS LA MISE A JOUR 1.4.4
     */
        $result .= "&nbsp;->Ajout du champ categorie_id à la table j_groupes_classes<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM j_groupes_classes LIKE 'categorie_id'"));
        if ($test1 == 0) {
            $query3 = mysql_query("ALTER TABLE `j_groupes_classes` ADD `categorie_id` int(11) NOT NULL default '1' AFTER `coef`");
            if ($query3) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
        }

    /*
     * FIN AJOUT --
     */

        $result .= "&nbsp;->Création de la table j_groupes_matieres<br />";
        $test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'j_groupes_matieres'"));
        if ($test == 0) {
            $query3 = mysql_query("CREATE TABLE IF NOT EXISTS `j_groupes_matieres` (`id_groupe` int(11) NOT NULL default '0',`id_matiere` varchar(50) NOT NULL default '', PRIMARY KEY (`id_groupe`, `id_matiere`))");
            if ($query3) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">La table existe déjà</font><br />";
        }

        $result .= "&nbsp;->Création de la table j_groupes_professeurs<br />";
        $test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'j_groupes_professeurs'"));
        if ($test == 0) {
            $query4 = mysql_query("CREATE TABLE IF NOT EXISTS `j_groupes_professeurs` (`id_groupe` int(11) NOT NULL default '0',`login` varchar(50) NOT NULL default '', `ordre_prof` smallint(6) NOT NULL default '0', PRIMARY KEY (`id_groupe`, `login`))");
            if ($query4) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">La table existe déjà</font><br />";
        }

        $result .= "&nbsp;->Création de la table j_eleves_groupes<br />";
        $test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'j_eleves_groupes'"));
        if ($test == 0) {
            $query4b = mysql_query("CREATE TABLE IF NOT EXISTS `j_eleves_groupes` (`login` varchar(50) NOT NULL default '', `id_groupe` int(11) NOT NULL default '0', `periode` int(11) NOT NULL default '0', PRIMARY KEY (`login`, `id_groupe`, `periode`))");
            if ($query4b) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">La table existe déjà</font><br />";
        }

        $result .= "&nbsp;->Création de la table eleves_groupes_settings<br />";
        $test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'eleves_groupes_settings'"));
        if ($test == 0) {
            $query5 = mysql_query("CREATE TABLE IF NOT EXISTS eleves_groupes_settings (login varchar(50) NOT NULL, id_groupe int(11) NOT NULL, `name` varchar(50) NOT NULL, `value` varchar(50) NOT NULL, PRIMARY KEY (`login`, `id_groupe`, `name`))");
            if ($query5) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">La table existe déjà</font><br />";
        }

        $result .= "&nbsp;->Ajout du champ id_groupe à la table ct_devoirs_entry<br />";
        $test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM `ct_devoirs_entry` LIKE 'id_groupe'"));
        if ($test == 0) {
            $query6 = mysql_query("ALTER TABLE `ct_devoirs_entry` ADD `id_groupe` INT NOT NULL AFTER `id_ct`");
            if ($query6) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà</font><br />";
        }

        $result .= "&nbsp;->Ajout du champ id_groupe à la table ct_entry<br />";
        $test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM `ct_entry` LIKE 'id_groupe'"));
        if ($test == 0) {
            $query7 = mysql_query("ALTER TABLE `ct_entry` ADD `id_groupe` INT NOT NULL AFTER `id_ct`");
            if ($query7) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà</font><br />";
        }

        $result .= "&nbsp;->Ajout du champ id_groupe à la table cn_cahier_notes<br />";
        $test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM `cn_cahier_notes` LIKE 'id_groupe'"));
        if ($test == 0) {
            $query7 = mysql_query("ALTER TABLE `cn_cahier_notes` ADD `id_groupe` INT NOT NULL AFTER `id_cahier_notes`");
            if ($query7) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà</font><br />";
        }

        $result .= "&nbsp;->Ajout du champ id_groupe à la table matieres_notes<br />";
        $test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM `matieres_notes` LIKE 'id_groupe'"));
        if ($test == 0) {
            $query8 = mysql_query("ALTER TABLE `matieres_notes` ADD `id_groupe` INT NOT NULL AFTER `matiere`");
            if ($query8) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà</font><br />";
        }

        $result .= "&nbsp;->Ajout du champ recalcul_rang à la table groupes<br />";
        $test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM `groupes` LIKE 'recalcul_rang'"));
        if ($test == 0) {
            $query9 = mysql_query("ALTER TABLE `groupes` ADD `recalcul_rang` VARCHAR(10) NOT NULL");
            if ($query9) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà</font><br />";
        }

        $result .= "&nbsp;->Ajout du champ id_groupe à la table matieres_appreciations<br />";
        $test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM `matieres_appreciations` LIKE 'id_groupe'"));
        if ($test == 0) {
            $query10 = mysql_query("ALTER TABLE `matieres_appreciations` ADD `id_groupe` INT NOT NULL AFTER `matiere`");
            if ($query10) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà</font><br />";
        }

        $result .= "&nbsp;->Conversion des données vers le nouveau modèles de groupes (cette opération peut prendre plusieurs minutes)<br />";

        $test_groupes = mysql_query("SELECT count(*) FROM groupes");
        $nb_groupes = mysql_result($test_groupes, 0);
        if ($nb_groupes != 0) {
            $result .= "<font color=\"blue\">Non (des groupes existent déjà)</font><br />";
        } else {

            // On récupère la liste des matières pour éviter les requêtes multiples
            $call_matieres = mysql_query("SELECT * FROM matieres");
            $nb = mysql_num_rows($call_matieres);
            $matieres = array ();
            for ($i = 0; $i < $nb; $i++) {
                $id_matiere = mysql_result($call_matieres, $i, "matiere");
                $matiere = mysql_result($call_matieres, $i, "nom_complet");
                $priority = mysql_result($call_matieres, $i, "priority");
                $matieres[$id_matiere] = array (
                    "matiere" => $id_matiere,
                    "nom_complet" => $matiere,
                    "priority" => $priority
                );
            }

            $former_scheme = mysql_query("SELECT * FROM j_classes_matieres_professeurs");
            if (!$former_scheme) {
                $nb = 0;
            } else {
                $nb = mysql_num_rows($former_scheme);
            }

            for ($i = 0; $i < $nb; $i++) {
                $id_classe = mysql_result($former_scheme, $i, "id_classe");
                $id_matiere = mysql_result($former_scheme, $i, "id_matiere");
                $id_professeur = mysql_result($former_scheme, $i, "id_professeur");
                $priorite = mysql_result($former_scheme, $i, "priorite");
                $ordre_prof = mysql_result($former_scheme, $i, "ordre_prof");
                $coef = mysql_result($former_scheme, $i, "coef");
                $recalcul_rang = mysql_result($former_scheme, $i, "recalcul_rang");

                // On regarde si cette association correspond déjà à un groupe
                $test = mysql_query("SELECT g.id FROM groupes g, j_groupes_classes jgc, j_groupes_matieres jgm WHERE (" .
                "g.id = jgc.id_groupe AND " .
                "jgc.id_classe = '" . $id_classe . "' AND " .
                "jgc.id_groupe = jgm.id_groupe AND " .
                "jgm.id_matiere = '" . $id_matiere . "')");

                if (mysql_num_rows($test) != 0) {
                    // Si un enregistrement existe déjà, ça veut dire que le groupe a déjà été traité
                    // il ne reste alors qu'à ajouter le professeur mentionné dans cette association

                    $group_id = mysql_result($test, 0, "id");
                    $insert_prof = mysql_query("INSERT into j_groupes_professeurs SET id_groupe = '" . $group_id . "', login = '" . $id_professeur . "', ordre_prof = '" . $ordre_prof . "'");

                } else {
                    // La première étape consiste à créer le nouveau groupe, pour obtenir son ID
                    $new_group = create_group($matieres[$id_matiere]["nom_complet"], $matieres[$id_matiere]["nom_complet"], $id_matiere, array (
                        $id_classe
                    ));
                    if (!is_numeric($new_group))
                        echo $new_group . "<br />";
                    $update = mysql_query("UPDATE groupes SET recalcul_rang = '" . $recalcul_rang . "' WHERE id = '" . $new_group . "'");
                    $update2 = update_group_class_options($new_group, $id_classe, array (
                        "priorite" => $priorite,
                        "coef" => $coef,
                        "categorie_id" => 1
                    ));
                    // On ajoute le professeur
                    $insert_prof = mysql_query("INSERT into j_groupes_professeurs SET id_groupe = '" . $new_group . "', login = '" . $id_professeur . "', ordre_prof = '" . $ordre_prof . "'");

                    // On s'occupe maintenant des élèves, période par période

                    $call_periodes = mysql_query("select num_periode FROM periodes WHERE id_classe = '" . $id_classe . "'");
                    $nb_per = mysql_num_rows($call_periodes);
                    for ($j = 0; $j < $nb_per; $j++) {
                        $num_periode = mysql_result($call_periodes, $j, "num_periode");
                        $call_eleves = mysql_query("SELECT login FROM j_eleves_classes WHERE (periode = '" . $num_periode . "' AND id_classe = '" . $id_classe . "')");
                        $eleves = array ();
                        while ($row = mysql_fetch_row($call_eleves)) {
                            $eleves[] = $row[0];
                        }

                        $call_options = mysql_query("SELECT login FROM j_eleves_matieres WHERE (periode = '" . $num_periode . "' AND matiere = '" . $id_matiere . "')");
                        $options = array ();
                        while ($row = mysql_fetch_row($call_options)) {
                            $options[] = $row[0];
                        }

                        $list_eleves = array_diff($eleves, $options);

                        foreach ($list_eleves as $_login) {
                            if ($new_group == 0)
                                echo "ERREUR! New_group ID = 0<br />";
                            // Appartenance au groupe
                            $insert = mysql_query("INSERT into j_eleves_groupes SET login = '" . $_login . "', id_groupe = '" . $new_group . "', periode = '" . $num_periode . "'");
                            // Mise à jour de la référence à la note du bulletin
                            $update = mysql_query("UPDATE matieres_notes SET id_groupe = '" . $new_group . "' WHERE (login = '" . $_login . "' AND periode = '" . $num_periode . "' AND matiere = '" . $id_matiere . "')");
                            // Mise à jour de la référence à l'appréciation du bulletin
                            $update = mysql_query("UPDATE matieres_appreciations SET id_groupe = '" . $new_group . "' WHERE (login = '" . $_login . "' AND periode = '" . $num_periode . "' AND matiere = '" . $id_matiere . "')");
                        }
                    }

                    // Et on fait les mises à jours de références pour les carnets de notes et cahiers de texte
                    $update_cn = mysql_query("UPDATE cn_cahier_notes SET id_groupe = '" . $new_group . "' WHERE (matiere = '" . $id_matiere . "' AND id_classe = '" . $id_classe . "')");

                    $update_ct1 = mysql_query("UPDATE ct_devoir_entry SET id_groupe = '" . $new_group . "' WHERE (id_matiere = '" . $id_matiere . "' AND id_classe = '" . $id_classe . "')");
                    $update_ct2 = mysql_query("UPDATE ct_entry SET id_groupe = '" . $new_group . "' WHERE (id_matiere = '" . $id_matiere . "' AND id_classe = '" . $id_classe . "')");
                }
            }
        }

        // Maintenant la migration est faite. On met à jour les primary keys et on supprime les champs et les tables qui ne sont plus utilisés.
        // Cette étape risque de mettre en avant un certain nombre de bugs persitants.

        $result .= "&nbsp;->Mise à jour de l'index de la table ct_devoirs_entry<br />";
        $query6b = mysql_query("ALTER TABLE `ct_devoirs_entry` DROP INDEX id_ct , ADD INDEX id_ct ( `id_ct` , `id_groupe` )");
        if ($query6b) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur</font><br />";
        }

        $result .= "&nbsp;->Mise à jour de l'index de la table ct_entry<br />";
        $query7b = mysql_query("ALTER TABLE `ct_entry` DROP INDEX id_ct, ADD INDEX id_ct ( `id_ct` , `id_groupe` )");
        if ($query7b) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur</font><br />";
        }

        $result .= "&nbsp;->Mise à jour de la clé primaire de la table cn_cahier_notes<br />";
        $query7b = mysql_query("ALTER TABLE `cn_cahier_notes` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id_cahier_notes` , `id_groupe` , `periode` )");
        if ($query7b) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur</font><br />";
        }

        $result .= "&nbsp;->Mise à jour de la clé primaire de la table matieres_notes<br />";
        $query7b = mysql_query("ALTER TABLE `matieres_notes` DROP PRIMARY KEY , ADD PRIMARY KEY ( `login` , `id_groupe` , `periode` )");
        if ($query7b) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur</font><br />";
        }

        $result .= "&nbsp;->Mise à jour de la clé primaire de la table matieres_appreciations<br />";
        $query10b = mysql_query("ALTER TABLE `matieres_appreciations` DROP PRIMARY KEY , ADD PRIMARY KEY ( `login` , `periode` , `id_groupe` )");
        if ($query10b) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur</font><br />";
        }

        $result .= "&nbsp;->Suppression des champs id_classe et id_matiere dans la table ct_devoirs_entry<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM `ct_devoirs_entry` LIKE 'id_classe'"));
        $test2 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM `ct_devoirs_entry` LIKE 'id_matiere'"));
        if ($test1 == 1 AND $test2 == 1) {
            $query10b = mysql_query("ALTER TABLE `ct_devoirs_entry` DROP `id_matiere` , DROP `id_classe`");
            if ($query10b) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Les champs ont déjà été supprimés.</font><br />";
        }

        $result .= "&nbsp;->Suppression des champs id_classe et id_matiere dans la table ct_entry<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM `ct_entry` LIKE 'id_classe'"));
        $test2 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM `ct_entry` LIKE 'id_matiere'"));
        if ($test1 == 1 AND $test2 == 1) {
            $query10b = mysql_query("ALTER TABLE `ct_entry` DROP `id_matiere` , DROP `id_classe`");
            if ($query10b) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Les champs ont déjà été supprimés.</font><br />";
        }

        $result .= "&nbsp;->Suppression des champs id_classe et matiere dans la table cn_cahier_notes<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM `cn_cahier_notes` LIKE 'id_classe'"));
        $test2 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM `cn_cahier_notes` LIKE 'matiere'"));
        if ($test1 == 1 AND $test2 == 1) {
            $query10b = mysql_query("ALTER TABLE `cn_cahier_notes` DROP `matiere` , DROP `id_classe`");
            if ($query10b) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Les champs ont déjà été supprimés.</font><br />";
        }

        $result .= "&nbsp;->Suppression du champ 'matiere' dans la table matieres_notes<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM `matieres_notes` LIKE 'matiere'"));
        if ($test1 == 1) {
            $query10b = mysql_query("ALTER TABLE `matieres_notes` DROP `matiere`");
            if ($query10b) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ a déjà été supprimé.</font><br />";
        }

        $result .= "&nbsp;->Suppression du champ 'matiere' dans la table matieres_appreciations<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM `matieres_appreciations` LIKE 'matiere'"));
        if ($test1 == 1) {
            $query10b = mysql_query("ALTER TABLE `matieres_appreciations` DROP `matiere`");
            if ($query10b) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur (champ déjà supprimé ?)</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ a déjà été supprimé.</font><br />";
        }

        $result .= "&nbsp;->Suppression de la table j_classes_matieres_professeurs<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'j_classes_matieres_professeurs'"));
        if ($test1 == 1) {
            $query10b = mysql_query("DROP TABLE `j_classes_matieres_professeurs`");
            if ($query10b) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">La table a déjà été supprimée.</font><br />";
        }

        $result .= "&nbsp;->Suppression de la table j_eleves_matieres<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'j_eleves_matieres'"));
        if ($test1 == 1) {
            $query10b = mysql_query("DROP TABLE `j_eleves_matieres`");
            if ($query10b) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur (table déjà supprimée ?)</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">La table a déjà été supprimée.</font><br />";
        }
        //=======================================
        // AJOUT: boireaus
        $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'p_bulletin_margin'");
        $res_test = mysql_num_rows($req_test);
        if ($res_test == 0) {
            $result .= "&nbsp;->Ajout du paramètre p_bulletin_margin<br />";
            $query11 = mysql_query("INSERT INTO setting VALUES('p_bulletin_margin','5')");
            if ($query11) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur lors de l'insertion de 'p_bulletin_margin'.</font><br />";
            }
        }
        //=======================================

        $result .= "&nbsp;->Extension de la taille des champs elenoet et ereno de la table 'eleves'<br />";
        $query28 = mysql_query("ALTER TABLE eleves CHANGE elenoet elenoet VARCHAR( 50 ) NOT NULL, CHANGE ereno ereno VARCHAR( 50 ) NOT NULL");
        if ($query28) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur</font><br />";
        }

        $result .= "&nbsp;->Extension de la taille des champs ereno, nom1, prenom1, nom2, prenom2 de la table 'responsables'<br />";
        $query29 = mysql_query("ALTER TABLE responsables CHANGE ereno ereno VARCHAR( 50 ) NOT NULL, " .
                                                                "CHANGE nom1 nom1 VARCHAR( 50 ) NOT NULL, " .
                                                                "CHANGE prenom1 prenom1 VARCHAR( 50 ) NOT NULL, " .
                                                                "CHANGE nom2 nom2 VARCHAR( 50 ) NOT NULL, " .
                                                                "CHANGE prenom2 prenom2 VARCHAR( 50 ) NOT NULL"
                                                                );
        if ($query29) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur</font><br />";
        }

        // On nettoie la base pour s'assurer qu'il ne reste pas d'incohérences concernant les groupes
        // pour les établissements qui ont initialisé leur base sur la 1.4.3-rc2

        $result .= "&nbsp;-> Suppression des incohérences de la base de données en lien avec les groupes<br/>";

        $nb_del1 = 0;
        $res1 = true;
        $test_groupes = mysql_query("select distinct(g.id) FROM groupes g WHERE NOT EXISTS (SELECT distinct(id_groupe) FROM j_groupes_classes jgc WHERE jgc.id_groupe = g.id)");
        for ($g=0;$g<mysql_num_rows($test_groupes);$g++) {
            $del_groupe_id = mysql_result($test_groupes, $g, "id");
            $res1 = mysql_query("DELETE FROM g, jeg, jgm, jgp USING groupes g, j_eleves_groupes jeg, j_groupes_matieres jgm, j_groupes_professeurs jgp WHERE (" .
                    "g.id = '" . $del_groupe_id . "' AND " .
                    "jeg.id_groupe = '" . $del_groupe_id . "' AND " .
                    "jgm.id_groupe = '" . $del_groupe_id . "' AND " .
                    "jgp.id_groupe = '" . $del_groupe_id . "')");
            if ($res1) {
                $nb_del1++;
            } else {
                echo mysql_error();
            }
        }

        $nb_del2 = 0;
        $res2 = true;
        $test_eleves = mysql_query("select jeg.login, jeg.periode, jeg.id_groupe FROM j_eleves_groupes jeg WHERE NOT EXISTS (SELECT jec.login FROM j_eleves_classes jec WHERE (jec.periode = jeg.periode AND jec.login = jeg.login))");
        for ($g=0;$g<mysql_num_rows($test_eleves);$g++) {
            $del_eleve = mysql_result($test_eleves, $g, "login");
            $del_periode = mysql_result($test_eleves, $g, "periode");
            $del_groupe = mysql_result($test_eleves, $g, "id_groupe");
            $res2 = mysql_query("DELETE FROM j_eleves_groupes WHERE (" .
                    "login = '" . $del_eleve . "' AND " .
                    "periode = '" . $del_periode . "' AND " .
                    "id_groupe = '" . $del_groupe . "')");
            if ($res2) {
                $nb_del2++;
            }
        }

        if ($res1 && $res2) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Des erreurs ont été rencontrées.</font><br />";
        }
        $result .= "-- $nb_del1 groupes fantômes ont été supprimés.<br/>-- $nb_del2 associations élève/groupe/période ont été supprimées (un élève ne peut pas appartenir à un enseignement pour une période s'il n'est pas associé à une classe pour cette même période)<br />";

    }

    if (($force_maj == 'yes') or (quelle_maj("1.4.4"))) {
        $result .= "<br /><br /><b>Mise à jour vers la version 1.4.4" . $rc . $beta . " :</b><br />";

        $result .= "&nbsp;->Création de la table matieres_categories<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'matieres_categories'"));
        if ($test1 == 0) {
            $query1 = mysql_query("CREATE TABLE IF NOT EXISTS `matieres_categories` (`id` int(11) NOT NULL auto_increment, `nom_court` varchar(255) NOT NULL default '', `nom_complet` varchar(255) NOT NULL default '', `priority` smallint(6) NOT NULL default '0', PRIMARY KEY  (`id`))");
            if ($query1) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">La table existe déjà.</font><br />";
        }

        $result .= "&nbsp;->Insertion de la catégorie de matière par défaut<br />";
        $test = mysql_result(mysql_query("SELECT count(id) FROM matieres_categories WHERE id='1'"),0);
        if ($test == 0) {
            $query1b = mysql_query("INSERT INTO `matieres_categories` SET id = '1', nom_court = 'Autres', nom_complet = 'Autres', priority = '7'");
            if ($query1b) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">La matière par défaut existe déjà.</font><br />";
        }

        $result .= "&nbsp;->Création de la table j_matieres_categories_classes<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'j_matieres_categories_classes'"));
        if ($test1 == 0) {
            $query2 = mysql_query("CREATE TABLE IF NOT EXISTS `j_matieres_categories_classes` (`categorie_id` int(11) NOT NULL default '0', `classe_id` int(11) NOT NULL default '0', `priority` smallint(6) NOT NULL default '0', `affiche_moyenne` tinyint(1) NOT NULL default '0', PRIMARY KEY  (`categorie_id`,`classe_id`))");
            if ($query2) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">La table existe déjà.</font><br />";
        }

        $result .= "&nbsp;->Ajout du champ categorie_id à la table matieres<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM matieres LIKE 'categorie_id'"));
        if ($test1 == 0) {
            $query3 = mysql_query("ALTER TABLE `matieres` ADD `categorie_id` INT NOT NULL default '1' AFTER `priority`");
            if ($query3) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
        }

        $result .= "&nbsp;->Ajout du champ categorie_id à la table j_groupes_classes<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM j_groupes_classes LIKE 'categorie_id'"));
        if ($test1 == 0) {
            $query3 = mysql_query("ALTER TABLE `j_groupes_classes` ADD `categorie_id` int(11) NOT NULL default '1' AFTER `coef`");
            if ($query3) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
        }

        $result .= "&nbsp;->Ajout du champ display_mat_cat à la table classes<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'display_mat_cat'"));
        if ($test1 == 0) {
            $query4 = mysql_query("ALTER TABLE `classes` ADD `display_mat_cat` CHAR(1) NOT NULL default 'n' AFTER `display_coef`");
            if ($query4) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
        }

    //=================================================
    // AJOUT: boireaus
    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'l_max_aff_trombinoscopes'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test==0){
        $query_tromb1=mysql_query("INSERT INTO setting VALUES ('l_max_aff_trombinoscopes', '120');");
        if($query_tromb1){
            $result.="Définition du paramètre l_max_aff_trombinoscopes à 120: <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Définition du paramètre l_max_aff_trombinoscopes à 120: <font color=\"red\">Erreur !</font><br />";
        }
    }
    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'h_max_aff_trombinoscopes'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test==0){
        $query_tromb2=mysql_query("INSERT INTO setting VALUES ('h_max_aff_trombinoscopes', '160');");
        if($query_tromb2){
            $result.="Définition du paramètre h_max_aff_trombinoscopes à 160: <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Définition du paramètre h_max_aff_trombinoscopes à 160: <font color=\"red\">Erreur !</font><br />";
        }
    }
    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'l_max_imp_trombinoscopes'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test==0){
        $query_tromb3=mysql_query("INSERT INTO setting VALUES ('l_max_imp_trombinoscopes', '70');");
        if($query_tromb3){
            $result.="Définition du paramètre l_max_imp_trombinoscopes à 70: <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Définition du paramètre l_max_imp_trombinoscopes à 70: <font color=\"red\">Erreur !</font><br />";
        }
    }
    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'h_max_imp_trombinoscopes'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test==0){
        $query_tromb4=mysql_query("INSERT INTO setting VALUES ('h_max_imp_trombinoscopes', '100');");
        if($query_tromb4){
            $result.="Définition du paramètre h_max_imp_trombinoscopes à 100: <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Définition du paramètre h_max_imp_trombinoscopes à 100: <font color=\"red\">Erreur !</font><br />";
        }
    }

        $result .= "&nbsp;->Ajout du champ display_nbdev à la table classes<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'display_nbdev'"));
        if ($test1 == 0) {
            $query5 = mysql_query("ALTER TABLE `classes` ADD `display_nbdev` CHAR(1) NOT NULL default 'n' AFTER `display_mat_cat`");
            if ($query5) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
        }
        $result .= "&nbsp;->Ajout du champ heure_entry à la table ct_entry<br />";

        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM ct_entry LIKE 'heure_entry'"));
        if ($test1 == 0) {
            $query5 = mysql_query("ALTER TABLE `ct_entry` ADD `heure_entry` TIME NOT NULL AFTER `id_ct`");
            if ($query5) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
        }



    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'gepi_denom_boite'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test==0){
        $query_boite1=mysql_query("INSERT INTO setting VALUES ('gepi_denom_boite', 'boîte');");
        if($query_boite1){
            $result.="Définition du paramètre gepi_denom_boite à 'boîte': <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Définition du paramètre gepi_denom_boite à 'boîte': <font color=\"red\">Erreur !</font><br />";
        }
    }

    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'gepi_denom_boite_genre'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test==0){
        $query_boite1=mysql_query("INSERT INTO setting VALUES ('gepi_denom_boite_genre', 'f');");
        if($query_boite1){
        $result.="Définition du paramètre gepi_denom_boite_genre à 'f': <font color=\"green\">Ok !</font><br />";
        }
        else{
        $result.="Définition du paramètre gepi_denom_boite_genre à 'f': <font color=\"red\">Erreur !</font><br />";
        }
    }


    // Conversion de cm en mm:
    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'cnv_addressblock_dim_144'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test==0){
        // La mise à jour des dimensions de cm en mm n'a pas encore été effectuée.

        $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_padding_top'");
        $res_test=mysql_num_rows($req_test);
        if ($res_test>0){
            //$lig_addressblock_padding_top=mysql_fetch_object($req_test);
            $lig_addressblock_padding_top=mysql_fetch_array($req_test);
            // Conversion de cm en mm:
            //$addressblock_padding_top0="$lig_addressblock_padding_top->value";
            $addressblock_padding_top0=$lig_addressblock_padding_top[0];
            $addressblock_padding_top1=$addressblock_padding_top0*10;
            $update_addressblock_padding_top=mysql_query("UPDATE setting SET value='$addressblock_padding_top1' WHERE name='addressblock_padding_top';");
            if($update_addressblock_padding_top){
                $result.="-&gt; Mise à jour du paramètre addressblock_padding_top de ".$addressblock_padding_top0."cm à ".$addressblock_padding_top1."mm: <font color=\"green\">Ok !</font><br />";
            }
            else{
                $result.="-&gt; Mise à jour du paramètre addressblock_padding_top de ".$addressblock_padding_top0."cm à ".$addressblock_padding_top1."mm: <font color=\"red\">Erreur !</font><br />";
            }
        }
        else{
            $insert_addressblock_padding_top=mysql_query("INSERT INTO setting VALUES ('addressblock_padding_top', '40');");
            if($insert_addressblock_padding_top){
                $result.="-&gt; Définition du paramètre addressblock_padding_top à '40': <font color=\"green\">Ok !</font><br />";
            }
            else{
                $result.="-&gt; Définition du paramètre addressblock_padding_top à '40': <font color=\"red\">Erreur !</font><br />";
            }
        }




        $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_padding_right'");
        $res_test=mysql_num_rows($req_test);
        if ($res_test>0){
            //$lig_addressblock_padding_right=mysql_fetch_object($req_test);
            $lig_addressblock_padding_right=mysql_fetch_array($req_test);
            // Conversion de cm en mm:
            //$addressblock_padding_right0=$lig_addressblock_padding_right->value;
            $addressblock_padding_right0=$lig_addressblock_padding_right[0];
            $addressblock_padding_right1=$addressblock_padding_right0*10;
            $update_addressblock_padding_right=mysql_query("UPDATE setting SET value='$addressblock_padding_right1' WHERE name='addressblock_padding_right';");
            if($update_addressblock_padding_right){
                $result.="-&gt; Mise à jour du paramètre addressblock_padding_right de ".$addressblock_padding_right0."cm à ".$addressblock_padding_right1."mm: <font color=\"green\">Ok !</font><br />";
            }
            else{
                $result.="-&gt; Mise à jour du paramètre addressblock_padding_right de ".$addressblock_padding_right0."cm à ".$addressblock_padding_right1."mm: <font color=\"red\">Erreur !</font><br />";
            }
        }
        else{
            $insert_addressblock_padding_right=mysql_query("INSERT INTO setting VALUES ('addressblock_padding_right', '20');");
            if($insert_addressblock_padding_right){
                $result.="-&gt; Définition du paramètre addressblock_padding_right à '20': <font color=\"green\">Ok !</font><br />";
            }
            else{
                $result.="-&gt; Définition du paramètre addressblock_padding_right à '20': <font color=\"red\">Erreur !</font><br />";
            }
        }



        $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_padding_text'");
        $res_test=mysql_num_rows($req_test);
        if ($res_test>0){
            //$lig_addressblock_padding_text=mysql_fetch_object($req_test);
            $lig_addressblock_padding_text=mysql_fetch_array($req_test);
            // Conversion de cm en mm:
            //$addressblock_padding_text0=$lig_addressblock_padding_text->value;
            $addressblock_padding_text0=$lig_addressblock_padding_text[0];
            $addressblock_padding_text1=$addressblock_padding_text0*10;
            $update_addressblock_padding_text=mysql_query("UPDATE setting SET value='$addressblock_padding_text1' WHERE name='addressblock_padding_text';");
            if($update_addressblock_padding_text){
                $result.="-&gt; Mise à jour du paramètre addressblock_padding_text de ".$addressblock_padding_text0."cm à ".$addressblock_padding_text1."mm: <font color=\"green\">Ok !</font><br />";
            }
            else{
                $result.="-&gt; Mise à jour du paramètre addressblock_padding_text de ".$addressblock_padding_text0."cm à ".$addressblock_padding_text1."mm: <font color=\"red\">Erreur !</font><br />";
            }
        }
        else{
            $insert_addressblock_padding_text=mysql_query("INSERT INTO setting VALUES ('addressblock_padding_text', '20');");
            if($insert_addressblock_padding_text){
                $result.="-&gt; Définition du paramètre addressblock_padding_text à '20': <font color=\"green\">Ok !</font><br />";
            }
            else{
                $result.="-&gt; Définition du paramètre addressblock_padding_text à '20': <font color=\"red\">Erreur !</font><br />";
            }
        }



        $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_length'");
        $res_test=mysql_num_rows($req_test);
        if ($res_test>0){
            //$lig_addressblock_length=mysql_fetch_object($req_test);
            $lig_addressblock_length=mysql_fetch_array($req_test);
            // Conversion de cm en mm:
            //$addressblock_length0=$lig_addressblock_length->value;
            $addressblock_length0=$lig_addressblock_length[0];
            $addressblock_length1=$addressblock_length0*10;
            $update_addressblock_length=mysql_query("UPDATE setting SET value='$addressblock_length1' WHERE name='addressblock_length';");
            if($update_addressblock_length){
                $result.="-&gt; Mise à jour du paramètre addressblock_length de ".$addressblock_length0."cm à ".$addressblock_length1."mm: <font color=\"green\">Ok !</font><br />";
            }
            else{
                $result.="-&gt; Mise à jour du paramètre addressblock_length de ".$addressblock_length0."cm à ".$addressblock_length1."mm: <font color=\"red\">Erreur !</font><br />";
            }
        }
        else{
            $insert_addressblock_length=mysql_query("INSERT INTO setting VALUES ('addressblock_length', '60');");
            if($insert_addressblock_length){
                $result.="-&gt; Définition du paramètre addressblock_length à '60': <font color=\"green\">Ok !</font><br />";
            }
            else{
                $result.="-&gt; Définition du paramètre addressblock_length à '60': <font color=\"red\">Erreur !</font><br />";
            }
        }

        $sql="INSERT INTO setting SET name='cnv_addressblock_dim_144', value='y'";
        $res_cnv_addressblock_dim_144=mysql_query($sql);
    }
    else{
        $result.="La conversion cm/mm des dimensions du bloc adresse a été effectuée lors d'une précédente mise à jour.<br />";
    }
    /*
    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_padding_top'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test>0){
        $lig_addressblock_padding_top=mysql_fetch_object($req_test);
        // Conversion de cm en mm:
        $addressblock_padding_top0=$lig_addressblock_padding_top->value;
        $addressblock_padding_top1=$addressblock_padding_top0*10;
        $update_addressblock_padding_top=mysql_query("UPDATE setting SET value='$addressblock_padding_top1' WHERE name='addressblock_padding_top';");
        if($update_addressblock_padding_top){
            $result.="Mise à jour du paramètre addressblock_padding_top de ".$addressblock_padding_top0."cm à ".$addressblock_padding_top1."mm: <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Mise à jour du paramètre addressblock_padding_top de ".$addressblock_padding_top0."cm à ".$addressblock_padding_top1."mm: <font color=\"red\">Erreur !</font><br />";
        }
    }
    else{
        $insert_addressblock_padding_top=mysql_query("INSERT INTO setting VALUES ('addressblock_padding_top', '40');");
        if($insert_addressblock_padding_top){
            $result.="Définition du paramètre addressblock_padding_top à '40': <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Définition du paramètre addressblock_padding_top à '40': <font color=\"red\">Erreur !</font><br />";
        }
    }

    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_padding_right'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test>0){
        $lig_addressblock_padding_right=mysql_fetch_object($req_test);
        // Conversion de cm en mm:
        $addressblock_padding_right0=$lig_addressblock_padding_right->value;
        $addressblock_padding_right1=$addressblock_padding_right0*10;
        $update_addressblock_padding_right=mysql_query("UPDATE setting SET value='$addressblock_padding_right1' WHERE name='addressblock_padding_right';");
        if($update_addressblock_padding_right){
            $result.="Mise à jour du paramètre addressblock_padding_right de ".$addressblock_padding_right0."cm à ".$addressblock_padding_right1."mm: <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Mise à jour du paramètre addressblock_padding_right de ".$addressblock_padding_right0."cm à ".$addressblock_padding_right1."mm: <font color=\"red\">Erreur !</font><br />";
        }
    }
    else{
        $insert_addressblock_padding_right=mysql_query("INSERT INTO setting VALUES ('addressblock_padding_right', '20');");
        if($insert_addressblock_padding_right){
            $result.="Définition du paramètre addressblock_padding_right à '20': <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Définition du paramètre addressblock_padding_right à '20': <font color=\"red\">Erreur !</font><br />";
        }
    }

    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_padding_text'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test>0){
        $lig_addressblock_padding_text=mysql_fetch_object($req_test);
        // Conversion de cm en mm:
        $addressblock_padding_text0=$lig_addressblock_padding_text->value;
        $addressblock_padding_text1=$addressblock_padding_text0*10;
        $update_addressblock_padding_text=mysql_query("UPDATE setting SET value='$addressblock_padding_text1' WHERE name='addressblock_padding_text';");
        if($update_addressblock_padding_text){
            $result.="Mise à jour du paramètre addressblock_padding_text de ".$addressblock_padding_text0."cm à ".$addressblock_padding_text1."mm: <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Mise à jour du paramètre addressblock_padding_text de ".$addressblock_padding_text0."cm à ".$addressblock_padding_text1."mm: <font color=\"red\">Erreur !</font><br />";
        }
    }
    else{
        $insert_addressblock_padding_text=mysql_query("INSERT INTO setting VALUES ('addressblock_padding_text', '20');");
        if($insert_addressblock_padding_text){
            $result.="Définition du paramètre addressblock_padding_text à '20': <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Définition du paramètre addressblock_padding_text à '20': <font color=\"red\">Erreur !</font><br />";
        }
    }

    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_length'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test>0){
        $lig_addressblock_length=mysql_fetch_object($req_test);
        // Conversion de cm en mm:
        $addressblock_length0=$lig_addressblock_length->value;
        $addressblock_length1=$addressblock_length0*10;
        $update_addressblock_length=mysql_query("UPDATE setting SET value='$addressblock_length1' WHERE name='addressblock_length';");
        if($update_addressblock_length){
            $result.="Mise à jour du paramètre addressblock_length de ".$addressblock_length0."cm à ".$addressblock_length1."mm: <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Mise à jour du paramètre addressblock_length de ".$addressblock_length0."cm à ".$addressblock_length1."mm: <font color=\"red\">Erreur !</font><br />";
        }
    }
    else{
        $insert_addressblock_length=mysql_query("INSERT INTO setting VALUES ('addressblock_length', '60');");
        if($insert_addressblock_length){
            $result.="Définition du paramètre addressblock_length à '60': <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Définition du paramètre addressblock_length à '60': <font color=\"red\">Erreur !</font><br />";
        }
    }
    */






    // Ajout de nouveaux paramètres pour le bloc adresse des responsables sur le bulletin
    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_font_size'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test==0){
        $query_addressblock_font_size=mysql_query("INSERT INTO setting VALUES ('addressblock_font_size', '12');");
        if($query_addressblock_font_size){
            $result.="Définition du paramètre addressblock_font_size à '12': <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Définition du paramètre addressblock_font_size à '12': <font color=\"red\">Erreur !</font><br />";
        }
    }

    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_logo_etab_prop'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test==0){
        $query_addressblock_logo_etab_prop=mysql_query("INSERT INTO setting VALUES ('addressblock_logo_etab_prop', '50');");
        if($query_addressblock_logo_etab_prop){
            $result.="Définition du paramètre addressblock_logo_etab_prop à '50': <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Définition du paramètre addressblock_logo_etab_prop à '50': <font color=\"red\">Erreur !</font><br />";
        }
    }

    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_classe_annee'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test==0){
        $query_addressblock_classe_annee=mysql_query("INSERT INTO setting VALUES ('addressblock_classe_annee', '35');");
        if($query_addressblock_classe_annee){
            $result.="Définition du paramètre addressblock_classe_annee à '35': <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Définition du paramètre addressblock_classe_annee à '35': <font color=\"red\">Erreur !</font><br />";
        }
    }

    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_ecart_bloc_nom'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test==0){
        $query_bull_ecart_bloc_nom=mysql_query("INSERT INTO setting VALUES ('bull_ecart_bloc_nom', '1');");
        if($query_bull_ecart_bloc_nom){
            $result.="Définition du paramètre bull_ecart_bloc_nom à '1': <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Définition du paramètre bull_ecart_bloc_nom à '1': <font color=\"red\">Erreur !</font><br />";
        }
    }

    $req_test=mysql_query("SELECT VALUE FROM setting WHERE NAME = 'addressblock_debug'");
    $res_test=mysql_num_rows($req_test);
    if ($res_test==0){
        $query_addressblock_debug=mysql_query("INSERT INTO setting VALUES ('addressblock_debug', 'n');");
        if($query_addressblock_debug){
            $result.="Définition du paramètre addressblock_debug à 'n': <font color=\"green\">Ok !</font><br />";
        }
        else{
            $result.="Définition du paramètre addressblock_debug à 'n': <font color=\"red\">Erreur !</font><br />";
        }
    }



    //==============================================

        $result .= "&nbsp;->Ajout du champ display_moy_gen à la table classes<br />";
        $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'display_moy_gen'"));
        if ($test1 == 0) {
            $query5 = mysql_query("ALTER TABLE `classes` ADD `display_moy_gen` CHAR(1) NOT NULL default 'y' AFTER `display_nbdev`");
            if ($query5) {
                $result .= "<font color=\"green\">Ok !</font><br />";
            } else {
                $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
            }
        } else {
            $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
        }


        $result .= "&nbsp;->Création de la table preferences <br />";
        $query = mysql_query("CREATE TABLE IF NOT EXISTS `preferences` (`login` VARCHAR( 50 ) NOT NULL ,`name` VARCHAR( 32 ) NOT NULL ,`value` TEXT NOT NULL);");
    if($query){
        $result.="<font color=\"green\">Ok !</font><br />";
    }


        $result .= "&nbsp;->Création de la table j_scol_classes <br />";
        $query = mysql_query("CREATE TABLE IF NOT EXISTS `j_scol_classes` (`login` VARCHAR( 50 ) NOT NULL ,`id_classe` INT( 11 ) NOT NULL);");
    if($query){
        $result.="<font color=\"green\">Ok !</font><br />";
    }

    $test=mysql_query("SHOW TABLES;");
    //$temoin_j_scol_classes="";
    $notok_j_scol_classes=false;
    if($test){
        while($lig_test=mysql_fetch_array($test)){
            if($lig_test[0]=='j_scol_classes'){
                $call_classes_tmp=mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
                if(mysql_num_rows($call_classes_tmp)>0){
                    while($lig_clas=mysql_fetch_object($call_classes_tmp)){
                        $sql="SELECT login,nom,prenom FROM utilisateurs WHERE (statut='scolarite' AND etat='actif') ORDER BY nom,prenom";
                        $res_scol=mysql_query($sql);
                        if(mysql_num_rows($res_scol)>0){
                            while($lig_scol=mysql_fetch_object($res_scol)){
                                //$test=mysql_query("SELECT 1=1 FROM j_scol_classes WHERE id_classe='".$lig_clas->id."' AND login='".$scol_login[$i]."'");
                                $test=mysql_query("SELECT 1=1 FROM j_scol_classes WHERE id_classe='".$lig_clas->id."' AND login='".$lig_scol->login."'");
                                if(mysql_num_rows($test)==0){
                                    $sql="INSERT INTO j_scol_classes SET id_classe='".$lig_clas->id."', login='".$lig_scol->login."'";
                                    $reg_data=mysql_query($sql);
                                    if(!$reg_data){
                                        $notok_j_scol_classes=true;
                                    }
                                }
                            }
                        }
                    }
                }
                //$temoin_j_scol_classes="ok";
            }
        }
    }
    else{
        $result.="Erreur à la lecture des tables!<br />";
    }

    if($notok_j_scol_classes){
        $result.="Erreur à la l'affectation des classes aux comptes scolarité!<br />";
    }

    // Mise à jour des tables pour le module absences

    $result .= "&nbsp;->Ajout du champ parqui_suivi_eleve_cpe à la table suivi_eleve_cpe<br />";
    $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM suivi_eleve_cpe LIKE 'parqui_suivi_eleve_cpe'"));
    if ($test1 == 0) {
        $query5 = mysql_query("ALTER TABLE `suivi_eleve_cpe` ADD `parqui_suivi_eleve_cpe` varchar(150) NOT NULL AFTER `eleve_suivi_eleve_cpe`");
        if ($query5) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
        }
    } else {
        $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
    }

    $result .= "&nbsp;->Ajout du champ heure_suivi_eleve_cpe à la table suivi_eleve_cpe<br />";
    $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM suivi_eleve_cpe LIKE 'heure_suivi_eleve_cpe'"));
    if ($test1 == 0) {
        $query5 = mysql_query("ALTER TABLE `suivi_eleve_cpe` ADD `heure_suivi_eleve_cpe` time NOT NULL AFTER `date_suivi_eleve_cpe`");
        if ($query5) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
        }
    } else {
        $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
    }

    $result .= "&nbsp;->Ajout du champ niveau_message_suivi_eleve_cpe à la table suivi_eleve_cpe<br />";
    $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM suivi_eleve_cpe LIKE 'niveau_message_suivi_eleve_cpe'"));
    if ($test1 == 0) {
        $query5 = mysql_query("ALTER TABLE `suivi_eleve_cpe` ADD `niveau_message_suivi_eleve_cpe` varchar(1) NOT NULL AFTER `komenti_suivi_eleve_cpe`");
        if ($query5) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
        }
    } else {
        $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
    }

    $result .= "&nbsp;->Création de la table edt_classes<br />";
    $test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'edt_classes'"));
    if ($test1 == 0) {
        $query1 = mysql_query("CREATE TABLE IF NOT EXISTS `edt_classes` (`id_edt_classe` int(11) NOT NULL auto_increment, `groupe_edt_classe` int(11) NOT NULL, `prof_edt_classe` varchar(25) NOT NULL, `matiere_edt_classe` varchar(10) NOT NULL, `semaine_edt_classe` varchar(5) NOT NULL, `jour_edt_classe` tinyint(4) NOT NULL, `datedebut_edt_classe` date NOT NULL, `datefin_edt_classe` date NOT NULL, `heuredebut_edt_classe` time NOT NULL, `heurefin_edt_classe` time NOT NULL, `salle_edt_classe` varchar(50) NOT NULL, PRIMARY KEY (`id_edt_classe`));");
        if ($query1) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur</font><br />";
        }
    } else {
        $result .= "<font color=\"blue\">La table existe déjà.</font><br />";
    }

    $result .= "&nbsp;->Création de la table miseajour<br />";
    $test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'miseajour'"));
    if ($test1 == 0) {
        $query1 = mysql_query("CREATE TABLE IF NOT EXISTS `miseajour` (`id_miseajour` int(11) NOT NULL auto_increment, `fichier_miseajour` varchar(250) NOT NULL, `emplacement_miseajour` varchar(250) NOT NULL, `date_miseajour` date NOT NULL, `heure_miseajour` time NOT NULL, PRIMARY KEY  (`id_miseajour`));");
        if ($query1) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur</font><br />";
        }
    } else {
        $result .= "<font color=\"blue\">La table existe déjà.</font><br />";
    }

    $result .= "&nbsp;->Ajout de valeurs par défaut dans la table setting, si nécessaires.<br />";
    //--
    $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'active_module_msj'");
    $res_test = mysql_num_rows($req_test);
    if ($res_test == 0)
        $query = mysql_query("INSERT INTO setting VALUES ('active_module_msj', 'n');");
    //--
    $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'site_msj_gepi'");
    $res_test = mysql_num_rows($req_test);
    if ($res_test == 0)
        $query = mysql_query("INSERT INTO setting VALUES ('site_msj_gepi', 'http://gepi.sylogix.net/releases/');");
    //--
    $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'rc_module_msj'");
    $res_test = mysql_num_rows($req_test);
    if ($res_test == 0)
        $query = mysql_query("INSERT INTO setting VALUES ('rc_module_msj', 'n');");
    //--
    $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'beta_module_msj'");
    $res_test = mysql_num_rows($req_test);
    if ($res_test == 0)
        $query = mysql_query("INSERT INTO setting VALUES ('beta_module_msj', 'n');");
    //--
    $req_test = mysql_query("SELECT VALUE FROM setting WHERE NAME = 'dossier_ftp_gepi'");
    $res_test = mysql_num_rows($req_test);
    if ($res_test == 0)
        $query = mysql_query("INSERT INTO setting VALUES ('dossier_ftp_gepi', 'gepi');");

    }

    //==========================================
    // AJOUTé APRES LA RC2
	$req_test= mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_intitule_app'");
	$res_test = mysql_num_rows($req_test);
	if ($res_test == 0){
		$query = mysql_query("INSERT INTO setting VALUES ('bull_intitule_app', 'Appréciations/Conseils');");
		$result .= "Initialisation du paramètre bull_intitule_app à 'Appréciations/Conseils': ";
		if($query){
			$result .= "<font color=\"green\">Ok !</font><br />";
		}
		else{
			$result .= "<font color=\"red\">Erreur !</font><br />";
		}
	}

	$req_test= mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_affiche_tel'");
	$res_test = mysql_num_rows($req_test);
	if ($res_test == 0){
		$query = mysql_query("INSERT INTO setting VALUES ('bull_affiche_tel', 'n');");
		$result .= "Initialisation du paramètre bull_affiche_tel à 'n': ";
		if($query){
			$result .= "<font color=\"green\">Ok !</font><br />";
		}
		else{
			$result .= "<font color=\"red\">Erreur !</font><br />";
		}
	}

	$req_test= mysql_query("SELECT VALUE FROM setting WHERE NAME = 'bull_affiche_fax'");
	$res_test = mysql_num_rows($req_test);
	if ($res_test == 0){
		$query = mysql_query("INSERT INTO setting VALUES ('bull_affiche_fax', 'n');");
		$result .= "Initialisation du paramètre bull_affiche_fax à 'n': ";
		if($query){
			$result .= "<font color=\"green\">Ok !</font><br />";
		}
		else{
			$result .= "<font color=\"red\">Erreur !</font><br />";
		}
	}

    $result .= "&nbsp;->Création de la table absences_actions<br />";
    $test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'absences_actions'"));
    if ($test1 == 0) {
        $query1 = mysql_query("CREATE TABLE `absences_actions` (`id_absence_action` int(11) NOT NULL auto_increment, `init_absence_action` char(2) NOT NULL default '', `def_absence_action` varchar(255) NOT NULL default '', PRIMARY KEY  (`id_absence_action`));");
        if ($query1) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur</font><br />";
        }
    } else {
        $result .= "<font color=\"blue\">La table existe déjà.</font><br />";
    }

    $result .= "&nbsp;->Ajout du champ action_suivi_eleve_cpe à la table suivi_eleve_cpe<br />";
    $test1 = mysql_num_rows(mysql_query("SHOW COLUMNS FROM suivi_eleve_cpe LIKE 'action_suivi_eleve_cpe'"));
    if ($test1 == 0) {
        $query5 = mysql_query("ALTER TABLE `suivi_eleve_cpe` ADD `action_suivi_eleve_cpe` varchar(2) NOT NULL AFTER `niveau_message_suivi_eleve_cpe`");
        if ($query5) {
            $result .= "<font color=\"green\">Ok !</font><br />";
        } else {
            $result .= "<font color=\"red\">Erreur (le champ existe déjà ?)</font><br />";
        }
    } else {
        $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
    }

    // Mise à jour du numéro de version
    saveSetting("version", $gepiVersion);
    saveSetting("versionRc", $gepiRcVersion);
    saveSetting("versionBeta", $gepiBetaVersion);
    saveSetting("pb_maj", $pb_maj);
}
// Load settings
if (!loadSettings()) {
    die("Erreur chargement settings");
}

// Numéro de version effective
$version_old = getSettingValue("version");
// Numéro de version RC effective
$versionRc_old = getSettingValue("versionRc");
// Numéro de version beta effective
$versionBeta_old = getSettingValue("versionBeta");

$rc_old = '';
if ($versionRc_old != '')
    $rc_old = "-RC" . $versionRc_old;
$rc = '';
if ($gepiRcVersion != '')
    $rc = "-RC" . $gepiRcVersion;

$beta_old = '';
if ($versionBeta_old != '')
    $beta_old = "-beta" . $versionBeta_old;
$beta = '';
if ($gepiBetaVersion != '')
    $beta = "-beta" . $gepiBetaVersion;

// Pb de mise à jour lors de la dernière mise à jour
$pb_maj_bd = getSettingValue("pb_maj");

if (isset ($mess))
    echo "<center><p class=grand><font color=red>" . $mess . "</font></p></center>";
echo "<center><p class=grand>Mise à jour de la base de données MySql de GEPI</p></center>";

echo "<hr /><h3>Numéro de version actuel de la base MySql : GEPI " . $version_old . $rc_old . $beta_old . "</h3>";
echo "<hr />";
// Mise à jour de la base de donnée

if ($pb_maj_bd != 'yes') {
    if (test_maj()) {
        echo "<h3>Mise à jour de la base de données vers la version GEPI " . $gepiVersion . $rc . $beta . "</h3>";
        if (isset ($_SESSION['statut'])) {
            echo "<p>Il est vivement conseillé de faire une sauvegarde de la base MySql avant de procéder à la mise à jour</p>";
            echo "<center><form enctype=\"multipart/form-data\" action=\"../gestion/accueil_sauve.php?action=dump\" method=post name=formulaire>";
            echo "<input type=\"submit\" value=\"Lancer une sauvegarde de la base de données\" /></form></center>";
        }
        echo "<p>Remarque : la procédure de mise à jour vers la version <b>GEPI" . $gepiVersion . $rc . $beta . "</b> est utilisable à partir d'une version GEPI1.2 ou plus récente.</p>";
        echo "<form action=\"maj.php\" method=\"post\">";
        echo "<p><font color=red><b>ATTENTION : Votre base de données ne semble pas être à jour.";
        if ($version_old != '')
            echo " Numéro de version de la base de données : GEPI" . $version_old . $rc_old . $beta_old;
        echo "</b></font><br />";
        echo "Cliquez sur le bouton suivant pour effectuer la mise à jour vers la version <b>GEPI" . $gepiVersion . $rc . $beta . "</b></p>";
        echo "<center><input type=submit value='Mettre à jour' /></center>";
        echo "<input type=hidden name='maj' value='yes' />";
        echo "<input type=hidden name='valid' value='$valid' />";
        echo "</form>";
    } else {
        echo "<h3>Mise à jour de la base de données</h3>";
        echo "<p><b>Votre base de données est à jour. Vous n'avez pas de mise à jour à effectuer.</b></p>";
        echo "<center><p class='grand'><b><a href='../accueil.php'>Retour</a></b></p></center>";
        echo "<form action=\"maj.php\" method=\"post\">";
        //echo "<p><b>Néanmoins, vous pouvez forcer la mise à jour. Cette procédure, bien que sans risque, n'est utile que dans certains cas précis.</b></font><br />";
        echo "<p><b>Néanmoins, vous pouvez forcer la mise à jour. Cette procédure, bien que sans risque, n'est utile que dans certains cas précis.</b><br />";
        echo "Cliquez sur le bouton suivant pour effectuer la mise à jour forcée vers la version <b>GEPI " . $gepiVersion . $rc . $beta . "</b></p>";
        echo "<center><input type=submit value='Forcer la mise à jour' /></center>";
        echo "<input type=hidden name='maj' value='yes' />";
        echo "<input type=hidden name='force_maj' value='yes' />";
        echo "<input type=hidden name='valid' value='$valid' />";
        echo "</form>";
    }
} else {
    echo "<h3>Mise à jour de la base de données</h3>";
    echo "<p><b><font color = 'red'>Une ou plusieurs erreurs ont été rencontrées lors de la dernière mise à jour de la base de données
        .</font></b></p>";
    echo "<form action=\"maj.php\" method=\"post\">";
    echo "<p><b>Si vous pensez avoir réglé les problèmes entraînant ces erreurs, vous pouvez tenter une nouvelle mise à jour</b>";
    echo " en cliquant sur le bouton suivant pour effectuer la mise à jour vers la version <b>GEPI " . $gepiVersion . $rc . $beta . "</b>.</p>";
    echo "<center><input type=submit value='Tenter une nouvelle mise à jour' /></center>";
    echo "<input type=hidden name='maj' value='yes' />";
    echo "<input type=hidden name='force_maj' value='yes' />";
    echo "<input type=hidden name='valid' value='$valid' />";
    echo "</form>";
}
echo "<hr />";
if (isset ($result)) {
    echo "<center><table width=\"80%\" border=\"1\" cellpadding=\"5\" cellspacing=\"1\"><tr><td><h2 align=\"center\">Résultat de la mise à jour</h2>";
    echo $result;
    echo "</td></tr></table></center>";
}
?>
</html></body>