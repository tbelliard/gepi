<?php

/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/groupes/update_champs_periode.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits VALUES ('/groupes/update_champs_periode.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Ajax: Mise à jour de champs', '');";
$insert=mysql_query($sql);
}

// INSERT INTO droits VALUES ('/groupes/update_champs_periode.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Ajax: Mise à jour de champs', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

header('Content-Type: text/xml; charset=utf-8');

$id_groupe=isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL;
$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;

if((isset($id_groupe))&&(is_numeric($id_groupe))&&($id_groupe!=0)) {
	check_token();

	$sql="SELECT MAX(periode) AS maxper FROM j_eleves_groupes WHERE id_groupe='$id_groupe';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);

		for($i=1;$i<=$lig->maxper;$i++) {
			echo "<input type='radio' id='periode_num_$i' name='periode_num' value='".$i."' ";
			if((isset($_SESSION['mes_listes_periode_num']))&&($_SESSION['mes_listes_periode_num']<=$lig->maxper)) {
				if($_SESSION['mes_listes_periode_num']==$i) {
					echo "checked ";
				}
			}
			else {
				if($i==1) {echo "checked ";}
			}
			echo "/><label for='periode_num_$i'> Période $i</label><br />\n";
		}
	}
}
elseif((isset($id_classe))&&(is_numeric($id_classe))&&($id_classe!=0)) {
	check_token();

	$sql="SELECT MAX(num_periode) AS maxper FROM periodes WHERE id_classe='$id_classe';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);

		for($i=1;$i<=$lig->maxper;$i++) {
			echo "<input type='radio' id='periode_num_$i' name='periode_num' value='".$i."' ";
			if((isset($_SESSION['mes_listes_periode_num']))&&($_SESSION['mes_listes_periode_num']<=$lig->maxper)) {
				if($_SESSION['mes_listes_periode_num']==$i) {
					echo "checked ";
				}
			}
			else {
				if($i==1) {echo "checked ";}
			}
			echo "/><label for='periode_num_$i'> Période $i</label><br />\n";
		}
	}
}
?>
