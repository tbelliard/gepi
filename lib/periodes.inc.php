<?php
/*
 *
 * Copyright 2001, 2016, 2021 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Romain Neil
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


//if (isset($id_classe) OR isset($current_group)) {
if ((isset($id_classe)) || (isset($current_group))) {
	//if (isset($id_classe) and $id_classe > 0) {
	if ((isset($id_classe)) && ($id_classe > 0)) {
		$_id_classe = $id_classe;
	} elseif (isset($current_group["classes"]["list"][0])) {
		$_id_classe = $current_group["classes"]["list"][0];
	}

	if (isset($_id_classe)) {
		if (!is_array($_id_classe)) {
			$periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$_id_classe' ORDER BY num_periode");
		} else {
			$periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '" . $_id_classe[0] . "' ORDER BY num_periode");
		}
		$nb_periode = mysqli_num_rows($periode_query) + 1;
		$i = "1";
		while ($i < $nb_periode) {
			$nom_periode[$i] = old_mysql_result($periode_query, $i - 1, "nom_periode");
			$ver_periode[$i] = old_mysql_result($periode_query, $i - 1, "verouiller");
			$date_ver_periode[$i] = old_mysql_result($periode_query, $i - 1, "date_verrouillage");
			$date_fin_periode[$i] = old_mysql_result($periode_query, $i - 1, "date_fin");
			$date_conseil_periode[$i] = old_mysql_result($periode_query, $i - 1, "date_conseil_classe");
			$i++;
		}
	}
}
