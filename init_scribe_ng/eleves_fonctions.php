<?php
 /*This file is part of GEPI.
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
 * Attention ce fichier contient des fonctions spécifiques au module d'import "annuaire federateur",
 * il ne doit être inclu que depuis le module init_annuaire de GEPI
 * sinon des erreurs d'inclusion apparaitront
 */

/*
 * Fonction permettant de vider les tables avant l'import depuis l'annuaire federateur
 * (paramétrables dans le fichier config_init_annuaire.inc.php)
 */
function vider_tables_avant_import() {
    include("config_init_annuaire.inc.php");
    foreach($liste_tables_del as $table) {
        $sql="SHOW TABLES LIKE '$table';";
        $test=mysql_query($sql);
        if(mysql_num_rows($test)>0) {
            $query_empty_table = "truncate table $table;";
            mysql_query($query_empty_table) or die("Impossible de vider les tables.");
        }
    }
}

function vider_table_seule($table_name) {
    $query_empty_table = "truncate table $table_name";
    mysql_query($query_empty_table) or die("Impossible de vider la table $table_name.");
}

function formater_date_pour_mysql($date) {
    if ($date != '') {
        $annee = mb_substr($date, 0, 4);
        $mois = mb_substr($date, 4, 2);
        $jour = mb_substr($date, 6, 2);
        return "$annee-$mois-$jour";
    }
    else return '';
}

function is_table_vide($table_name) {
    $query_count = "select count(*) from $table_name";
    $count = mysql_result(mysql_query($query_count), 0);
    return ($count == 0 ? true : false);
}

?>
