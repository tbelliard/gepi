<?php
/**
 *
 * @version $Id: include_chargement.php 7822 2011-08-18 18:56:42Z dblanqui $
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

header('Content-Type: text/html; charset=ISO-8859-1');
$compteur = isset($_GET['compteur']) ? (int) htmlentities($_GET['compteur']) : Null;
if (!is_int($compteur)) {
    die();
}
if ($compteur == "0") {
    echo 'Chargement débuté';
} elseif ($compteur == "100") {
    echo 'Chargement terminé';
} else {
    echo 'Chargement effectué à ' . $compteur . ' %';
}
echo'<table style="border:1px solid black;width:215px;">
<tr style="border:1px solid black">';
for ($i = 1; $i <= 10; $i++) {
    if ((10 * $i) <= $compteur) {
        echo'<td style="border:1px solid black;background-color:#CFE2F4;">&nbsp;</td>';
    } else {
        echo'<td style="border:1px solid black;">&nbsp;</td>';
    }
}
echo'</tr>
</table>';
?>
