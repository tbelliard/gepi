<?php
/**
 * Fichier req_database.php pour accéder à la base de données
 *
 * @version     $Id$
 * @package		GEPI
 * @subpackage	EmploisDuTemps
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

    $sql_request = "SELECT id_cours, duree, id_groupe, heuredeb_dec, id_semaine FROM edt_cours WHERE 
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

    $sql_request = "SELECT id_cours, duree, id_groupe, heuredeb_dec, id_semaine FROM edt_cours WHERE 
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
function LessonsFromDaySlotPeriod($jour_sem, $id_creneau, $calendrier) {

    $sql_request = "SELECT id_groupe, duree, heuredeb_dec, id_semaine, id_cours from edt_cours where
                                jour_semaine = '".$jour_sem."' AND
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

    $sql_request = "SELECT duree, id_groupe, heuredeb_dec, id_semaine FROM edt_cours WHERE 
                                            jour_semaine = '".$jour_sem."' AND
                                            id_salle = '".$id_salle."' AND
                                            id_definie_periode = '".$id_creneau."' AND
                                            id_semaine = '".$id_semaine."' AND
                                            (id_calendrier = '".$period."' OR id_calendrier = '0')";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;

}

?>