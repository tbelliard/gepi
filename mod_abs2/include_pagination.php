<?php
/**
 *
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

/**
 * Ce fichier sert a gérer les parametre de requete pour paginer les listes de abs2
 *
*/


$basename_serveur=explode("?", basename($_SERVER["REQUEST_URI"]));
$onglet_abs = reset($basename_serveur);

$page_number = isset($_POST["page_number"]) ? $_POST["page_number"] :(isset($_GET["page_number"]) ? $_GET["page_number"] :(isset($_SESSION["page_number".$onglet_abs]) ? $_SESSION["page_number".$onglet_abs] : NULL));
if (!is_numeric($page_number) || (isset($_POST["reinit_filtre"]) &&  $_POST["reinit_filtre"] == 'y')) {
    $page_number = 1;
}

$page_deplacement = isset($_POST["page_deplacement"]) ? $_POST["page_deplacement"] :(isset($_GET["page_deplacement"]) ? $_GET["page_deplacement"] :NULL);
if ($page_deplacement == "+") {
    $page_number = $page_number + 1;
} else if ($page_deplacement == "-") {
    $page_number = $page_number - 1;
}
if ($page_number < 1) {
    $page_number = 1;
}
if (isset($page_number) && $page_number != null) $_SESSION['page_number'.$onglet_abs] = $page_number;
//if (isset($page_deplacement) && $page_deplacement != null) $_SESSION['page_deplacement'] = $page_deplacement;

$item_per_page = isset($_POST["item_per_page"]) ? $_POST["item_per_page"] :(isset($_GET["item_per_page"]) ? $_GET["item_per_page"] :(isset($_SESSION["item_per_page".$onglet_abs]) ? $_SESSION["item_per_page".$onglet_abs] : NULL));
if (!is_numeric($item_per_page) || (isset($_POST["reinit_filtre"]) &&  $_POST["reinit_filtre"] == 'y')) {
    $item_per_page = 14;
}
if ($item_per_page < 1) {
    $item_per_page = 1;
}
if (isset($item_per_page) && $item_per_page != null) $_SESSION['item_per_page'.$onglet_abs] = $item_per_page;

?>
