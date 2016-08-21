<?php
/**
 * language file
 *
 * @version $Id: fr-lang.edt.php  $
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

// fichier appelé par edt_organisation/choix_langue.php
// langue = fr - iso latin-1
// Si GEPI UTF-8, utiliser utf8_encode dans chaque define

// --------------------------------------
// edt_organisation/...
// --------------------------------------

define('ASK_AUTHORIZATION_TO_ADMIN', "Vous devez demander à votre administrateur l'autorisation de voir cette page.");

// --------------------------------------
// edt_organisation/ajax_edtcouleurs.php
// --------------------------------------

define('NO_COLOR', "pas de couleur");
define('IMPOSSIBLE_TO_UPDATE', "Impossible de mettre à jour la table edt_setting");

// --------------------------------------
// edt_organisation/ajouter_salle.php
// --------------------------------------

define('TITLE_ADD_CLASSROOM', "Emploi du temps");
define('MANAGE_GEPI_CLASSROOMS', "Gérer les salles de GEPI");
define('ADD_CLASSROOM_IN_DB', "Ajouter une salle");
define('CHANGE_CLASSROOM_NAME', "La salle numéro %s s'appelle désormais %s");
//... à finir

// --------------------------------------
// edt_organisation/voir_edt.php
// --------------------------------------

define('TITLE_VOIR_EDT', "L'emploi du temps de :");

// --------------------------------------
// edt_organisation/edt_param_couleurs.php
// --------------------------------------

define('TITLE_EDT_PARAM_COLORS', "Paramétrer les couleurs des matières");
define('CLICK_ON_COLOR', "Cliquez sur la couleur pour la modifier.");
define('TEXT1_EDT_PARAM_COLORS', "Pour voir ces couleurs dans les emplois du temps, il faut modifier les paramètres.");
define('FIELD', "Matière");
define('SHORT_NAME', "Nom court");
define('COLOR', "Couleur");
define('MODIFY_COLOR', "Modifier");

// --------------------------------------
// edt_organisation/edt_parametrer.php
// --------------------------------------

define('TITLE_EDT_PARAMETRER', "Emploi du temps - Paramètres");
define('FIELDS_PARAM', "Les matières");
define('FIELDS_PARAM_BUTTON1', "Noms courts (du type HG,...).");
define('FIELDS_PARAM_BUTTON2', " Noms longs (Histoire Géographie,...).");
//.... à finir

// --------------------------------------
// edt_organisation/voir_edt_eleves.php
// --------------------------------------
define('LOOKFOR_STUDENTS_BY_NAME', "Rechercher tous les noms commençant par :");
define('NEXT_LETTER', "la lettre suivante");
define('LOOKFOR_STUDENTS_BY_CLASS', " ou la liste des élèves de ");
define('THIS_CLASS', "Cette classe");
define('PREVIOUS_STUDENT', "élève précédent");
define('NEXT_STUDENT', "élève suivant");
define('CHOOSE_STUDENT', "Choix de l'élève");

// --------------------------------------
// edt_organisation/voir_edt_prof.php
// --------------------------------------
define('PREVIOUS_TEACHER', "Prof. précédent");
define('NEXT_TEACHER', "Prof. suivant");
define('CHOOSE_TEACHER', "Choix du professeur");

// --------------------------------------
// edt_organisation/voir_edt_classe.php
// --------------------------------------
define('PREVIOUS_CLASS', "Classe précédente");
define('NEXT_CLASS', "Classe suivante");
define('CHOOSE_CLASS', "Choix de la classe");

// --------------------------------------
// edt_organisation/voir_edt_salle.php
// --------------------------------------
define('PREVIOUS_CLASSROOM', "Salle précédente");
define('NEXT_CLASSROOM', "Salle suivante");
define('CHOOSE_CLASSROOM', "Choix de la salle");

// --------------------------------------
// edt_organisation/menu.inc.php
// --------------------------------------
define('WEEK_NUMBER', "Semaine n° ");
define('VIEWS', "Visionner");
define('TEACHERS', "Professeurs");
define('CLASSES', "Classes");
define('CLASSROOMS', "Salles");
define('STUDENTS', "Elèves");
define('MODIFY', "Modifier");
define('LOOKFOR', "Chercher");
define('FREE_CLASSROOMS', "Salles libres");
define('ADMINISTRATOR', "Admin");
define('LESSONS', "Enseignements");
define('GROUPS', "Groupes");
define('INITIALIZE', "Initialiser");
define('PARAMETER', "Paramétrer");
define('COLORS', "Couleurs");
define('CALENDAR', "calendrier");
define('PERIODS', "Périodes");
define('WEEKS', "Semaines");


// --------------------------------------
// edt_organisation/effacer_cours.php
// --------------------------------------
define('TITLE_DELETE_LESSON', "Effacer un cours de l'emploi du temps");
define('CANT_DELETE_OTHER_COURSE', "Vous ne pouvez pas effacer un cours d'un collègue");
define('DELETE_CONFIRM', "Etes-vous sûr de vouloir supprimer ce cours ?");
define('DELETE_FAILURE', "échec de l'effacement");
define('DELETE_SUCCESS', "effacement effectué avec succès");
define('DELETE_NOTHING', "Vous tentez de supprimer un cours inexistant");
define('DELETE_BAD_RIGHTS', "Vous ne disposez pas des droits suffisants pour réaliser cette opération");
define('CONFIRM_BUTTON', "Confirmer");
define('ABORT_BUTTON', "Annuler");

// --------------------------------------
// edt_organisation/fonctions_cours.php
// --------------------------------------
define('INCOMPATIBLE_LESSON_LENGTH', "la durée du cours n'est pas compatible avec les horaires de l'établissement.");
define('LESSON_OVERLAPPING', "Ce cours en chevauche un autre ");
define('CLASSROOM_NOT_FREE', "La salle demandée est déjà occupée par ");
define('STUDENTS_NOT_FREE', "Attention : Certains élèves ont déjà cours avec ");
define('SOME_STUDENTS_NOT_FREE', "Cours créé bien que certains élèves soient déjà en cours avec ");
define('GROUP_IS_EMPTY', "Veuillez choisir un enseignement pour créer le créneau");

// --------------------------------------
// edt_organisation/modifier_cours_popup.php
// --------------------------------------
define('TITLE_MODIFY_LESSON_POPUP', "Modifier un cours de l'emploi du temps");
define('TITLE_PAGE', "Gepi - Modifier un cours");
define('LESSON_MODIFICATION', "Modification du cours");
define('CHOOSE_LESSON', "Choix de l'enseignement");
define('LESSON_START_AT_THE_BEGINNING', "Le cours commence au début d'un créneau");
define('LESSON_START_AT_THE_MIDDLE', "Le cours commence au milieu d'un créneau");
define('HOUR1', "1/2 heure");
define('HOUR2', "1 heure");
define('HOUR3', "1,5 heure");
define('HOUR4', "2 heures");
define('HOUR5', "2,5 heures");
define('HOUR6', "3 heures");
define('HOUR7', "3,5 heures");
define('HOUR8', "4 heures");
define('HOUR9', "4,5 heures");
define('HOUR10', "5 heures");
define('HOUR11', "5,5 heures");
define('HOUR12', "6 heures");
define('HOUR13', "6,5 heures");
define('HOUR14', "7 heures");
define('HOUR15', "7,5 heures");
define('HOUR16', "8 heures");
define('ALL_WEEKS', "Toutes les semaines");
define('ENTIRE_YEAR', "Année entière");
define('REGISTER', "Enregistrer");
define('HOURS', "Horaire");
define('CLASSROOM', "Salle");
?>