<?php
/**
 * Fichier req_database.php pour accéder à la base de données
 *
 * @version     $Id: req_database.php 7726 2011-08-12 23:37:39Z regis $
 * @package		EmploisDuTemps
 * @copyright	Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Pascal Fautrero
 * @license		GNU/GPL, see COPYING.txt
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
// =============================================================================
//
//
//          
//
//
// =============================================================================
function LessonsFromDayTeacherSlotPeriod($jour_sem, $login_edt, $id_creneau, $period) {

    $sql_request = "SELECT id_cours, id_aid, duree, id_groupe, heuredeb_dec, id_semaine FROM edt_cours WHERE 
                                    jour_semaine = '".$jour_sem."' AND
                                    login_prof = '".$login_edt."' AND
                                    id_definie_periode = '".$id_creneau."' AND
                                    (id_calendrier = '".$period."' OR id_calendrier = '0')";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}

// =============================================================================
//
//
//          
//
//
// =============================================================================
function LessonsFromDayTeacherSlotWeekPeriod($jour_sem, $login_edt, $id_creneau, $id_semaine, $period) {

    $sql_request = "SELECT id_cours, id_aid, duree, id_groupe, heuredeb_dec, id_semaine FROM edt_cours WHERE 
                                            jour_semaine = '".$jour_sem."' AND
                                            login_prof = '".$login_edt."' AND
                                            id_definie_periode = '".$id_creneau."' AND
                                            id_semaine = '".$id_semaine."' AND
                                            (id_calendrier = '".$period."' OR id_calendrier = '0')";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}


// =============================================================================
//
//
//          
//
//
// =============================================================================
function LessonsFromStudentDaySlotPeriod($login_eleve, $jour_sem, $id_creneau, $calendrier) {

    $sql_request = "SELECT id_groupe, id_aid, duree, heuredeb_dec, id_semaine, id_cours from edt_cours where
                                jour_semaine = '".$jour_sem."' AND
                                id_groupe IN (SELECT id_groupe from j_eleves_groupes WHERE login = '".$login_eleve."') AND
                                id_definie_periode = '".$id_creneau."' AND
                                $calendrier";


    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}
// =============================================================================
//
//
//          
//
//
// =============================================================================
function LessonsFromClassDaySlotPeriodWeek($id_classe, $jour_sem, $id_creneau, $calendrier, $id_semaine) {

    $sql_request = "SELECT id_groupe, id_aid, duree, heuredeb_dec, id_semaine, id_cours from edt_cours where
                                jour_semaine = '".$jour_sem."' AND
                                id_groupe IN (SELECT id_groupe FROM j_groupes_classes WHERE id_classe = '".$id_classe."') AND
                                id_semaine = '".$id_semaine."' AND
                                id_definie_periode = '".$id_creneau."' AND
                                $calendrier";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}
// =============================================================================
//
//
//          
//
//
// =============================================================================
function LessonsFromStudentDaySlotPeriodWeek($login_eleve, $jour_sem, $id_creneau, $calendrier, $id_semaine) {

    $sql_request = "SELECT id_groupe, id_aid, duree, heuredeb_dec, id_semaine, id_cours from edt_cours where
                                jour_semaine = '".$jour_sem."' AND
                                id_groupe IN (SELECT id_groupe from j_eleves_groupes WHERE login = '".$login_eleve."') AND
                                id_semaine = '".$id_semaine."' AND
                                id_definie_periode = '".$id_creneau."' AND
                                $calendrier";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}
// =============================================================================
//
//
//          
//
//
// =============================================================================
function LessonsFromClassDaySlotPeriodNotWeek($id_classe, $jour_sem, $id_creneau, $calendrier, $id_semaine) {

    $sql_request = "SELECT id_groupe, id_aid, duree, heuredeb_dec, id_semaine, id_cours from edt_cours where
                                jour_semaine = '".$jour_sem."' AND
                                id_groupe IN (SELECT id_groupe FROM j_groupes_classes WHERE id_classe = '".$id_classe."') AND
                                id_semaine != '".$id_semaine."' AND
                                id_semaine != '0' AND
                                id_definie_periode = '".$id_creneau."' AND
                                $calendrier";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}
// =============================================================================
//
//
//          
//
//
// =============================================================================
function LessonsFromStudentDaySlotPeriodNotWeek($login_eleve, $jour_sem, $id_creneau, $calendrier, $id_semaine) {

    $sql_request = "SELECT id_groupe, id_aid, duree, heuredeb_dec, id_semaine, id_cours from edt_cours where
                                jour_semaine = '".$jour_sem."' AND
                                id_groupe IN (SELECT id_groupe from j_eleves_groupes WHERE login = '".$login_eleve."') AND
                                id_semaine != '".$id_semaine."' AND
                                id_semaine != '0' AND
                                id_definie_periode = '".$id_creneau."' AND
                                $calendrier";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}

// =============================================================================
//
//
//          
//
//
// =============================================================================
function LessonsFromClassDaySlotPeriod($id_classe, $jour_sem, $id_creneau, $calendrier) {

    $sql_request = "SELECT id_groupe, id_aid, duree, heuredeb_dec, id_semaine, id_cours from edt_cours where
                                jour_semaine = '".$jour_sem."' AND
                                id_groupe IN (SELECT id_groupe FROM j_groupes_classes WHERE id_classe = '".$id_classe."') AND
                                id_definie_periode = '".$id_creneau."' AND
                                $calendrier";
    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}
// =============================================================================
//
//
//          
//
//
// =============================================================================
function AidFromClassDaySlotPeriod($id_classe, $jour_sem, $id_creneau, $calendrier ) {

    $sql_request = "SELECT id_groupe, id_aid, duree, heuredeb_dec, id_semaine, id_cours from edt_cours where
                                id_aid IN (SELECT id_aid FROM j_aid_eleves WHERE login IN
                                    (SELECT login FROM j_eleves_classes WHERE id_classe = '".$id_classe."')) AND
                                jour_semaine = '".$jour_sem."' AND
                                id_definie_periode = '".$id_creneau."' AND
                                $calendrier ";
    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;
}
// =============================================================================
//
//
//          
//
//
// =============================================================================
function AidFromClassDaySlotPeriod2($id_classe, $jour_sem, $id_creneau, $calendrier, &$tab_enseignement_final, &$j) {

    $sql = "SELECT id_groupe, id_aid, duree, heuredeb_dec, id_semaine, id_cours from edt_cours where
                                jour_semaine = '".$jour_sem."' AND
                                id_definie_periode = '".$id_creneau."' AND
                                $calendrier";
    $req = mysql_query($sql);
    $sql2 = "SELECT id_aid FROM j_aid_eleves WHERE login IN
                                    (SELECT login FROM j_eleves_classes WHERE id_classe = '".$id_classe."') ";
    $req2 = mysql_query($sql2);
    while ($rep2 = mysql_fetch_array($req2)) {
        $tab_tmp[] = $rep2['id_aid'];
    }
    while ($rep = mysql_fetch_array($req)) {
        if (in_array($rep['id_aid'], $tab_tmp)) {
            $tab_enseignement_final['id_aid'][$j] = $rep['id_aid'];
            $tab_enseignement_final['id_groupe'][$j] = 0;
            $tab_enseignement_final['duree'][$j] = $rep['duree'];
            $tab_enseignement_final['heuredeb_dec'][$j] = $rep['heuredeb_dec'];
            $tab_enseignement_final['id_semaine'][$j] = $rep['id_semaine'];
            $tab_enseignement_final['id_cours'][$j] = $rep['id_cours'];
            $tab_enseignement_final['aid'][$j] = 1;
            $tab_enseignement_final['couleur'][$j] = "cadreCouleur";
            $j++;
        }
    }
    $tab_enseignement_final['id_groupe'][$j] = '';
    $tab_enseignement_final['id_aid'][$j] = '';
    $nb_enseignements = $j;
    if ($nb_enseignements < 0) 
    {
        $nb_enseignements = 0;
    }
    return $nb_enseignements;
}
// =============================================================================
//
//
//          
//
//
// =============================================================================
function AidFromStudentDaySlotPeriod($login_eleve, $jour_sem, $id_creneau, $calendrier) {

    $sql_request = "SELECT id_groupe, id_aid, duree, heuredeb_dec, id_semaine, id_cours from edt_cours where
                                jour_semaine = '".$jour_sem."' AND
                                id_aid IN (SELECT DISTINCT id_aid FROM j_aid_eleves WHERE login = '".$login_eleve."') AND
                                id_definie_periode = '".$id_creneau."' AND
                                $calendrier";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}
// =============================================================================
//
//
//          
//
//
// =============================================================================
function AidFromClassDaySlotPeriodWeek($id_classe, $jour_sem, $id_creneau, $calendrier, $id_semaine) {

    $sql_request = "SELECT id_groupe, id_aid, duree, heuredeb_dec, id_semaine, id_cours from edt_cours where
                                jour_semaine = '".$jour_sem."' AND
                                id_aid IN (SELECT DISTINCT id_aid FROM j_aid_eleves WHERE login IN
                                    (SELECT login FROM j_eleves_classes WHERE id_classe = '".$id_classe."')) AND
                                id_semaine = '".$id_semaine."' AND
                                id_definie_periode = '".$id_creneau."' AND
                                $calendrier";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}
// =============================================================================
//
//
//          
//
//
// =============================================================================
function AidFromStudentDaySlotPeriodWeek($login_eleve, $jour_sem, $id_creneau, $calendrier, $id_semaine) {

    $sql_request = "SELECT id_groupe, id_aid, duree, heuredeb_dec, id_semaine, id_cours from edt_cours where
                                jour_semaine = '".$jour_sem."' AND
                                id_aid IN (SELECT DISTINCT id_aid FROM j_aid_eleves WHERE login = '".$login_eleve."') AND
                                id_semaine = '".$id_semaine."' AND
                                id_definie_periode = '".$id_creneau."' AND
                                $calendrier";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}
// =============================================================================
//
//
//          
//
//
// =============================================================================
function AidFromClassDaySlotPeriodNotWeek($id_classe, $jour_sem, $id_creneau, $calendrier, $id_semaine) {

    $sql_request = "SELECT id_groupe, id_aid, duree, heuredeb_dec, id_semaine, id_cours from edt_cours where
                                jour_semaine = '".$jour_sem."' AND
                                id_aid IN (SELECT DISTINCT id_aid FROM j_aid_eleves WHERE login IN
                                    (SELECT login FROM j_eleves_classes WHERE id_classe = '".$id_classe."')) AND
                                id_semaine != '".$id_semaine."' AND
                                id_semaine != '0' AND
                                id_definie_periode = '".$id_creneau."' AND
                                $calendrier";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}
// =============================================================================
//
//
//          
//
//
// =============================================================================
function AidFromStudentDaySlotPeriodNotWeek($login_eleve, $jour_sem, $id_creneau, $calendrier, $id_semaine) {

    $sql_request = "SELECT id_groupe, id_aid, duree, heuredeb_dec, id_semaine, id_cours from edt_cours where
                                jour_semaine = '".$jour_sem."' AND
                                id_aid IN (SELECT DISTINCT id_aid FROM j_aid_eleves WHERE login = '".$login_eleve."') AND
                                id_semaine != '".$id_semaine."' AND
                                id_semaine != '0' AND
                                id_definie_periode = '".$id_creneau."' AND
                                $calendrier";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}
// =============================================================================
//
//
//          
//
//
// =============================================================================
function LessonsFromDayClassroomSlotWeekPeriod($jour_sem, $id_salle, $id_creneau, $id_semaine, $period) {

    $sql_request = "SELECT duree, id_groupe, id_aid, heuredeb_dec, id_semaine FROM edt_cours WHERE 
                                            jour_semaine = '".$jour_sem."' AND
                                            id_salle = '".$id_salle."' AND
                                            id_definie_periode = '".$id_creneau."' AND
                                            id_semaine = '".$id_semaine."' AND
                                            (id_calendrier = '".$period."' OR id_calendrier = '0')";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}

?>