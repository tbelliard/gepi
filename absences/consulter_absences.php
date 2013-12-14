<?php

/*
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);



// Resume session

$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


include "../lib/periodes.inc.php";

//**************** EN-TETE *****************
$titre_page = "Consultation des absences";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<script type="text/javascript" language="javascript">
change = 'no';
</script>

<p class=bold>
<a href="index.php?id_classe=<?php echo $id_classe; ?>"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Choisir une autre période</a> |
<a href="index.php">Choisir une autre classe</a></p>


<?php
$call_classe = mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM classes WHERE id = '$id_classe'");
$classe = old_mysql_result($call_classe, "0", "classe");
?>
<p><b>Classe de <?php echo "$classe"; ?> - Consultation des absences : <?php $temp = my_strtolower($nom_periode[$periode_num]); echo "$temp"; ?></b>
<br />
<!--table border=1 cellspacing=2 cellpadding=5-->
<table class='boireaus' cellspacing='2' cellpadding='5'>
<tr>
	<th align='center'><b>Nom Prénom</b></th>
	<th align='center'><b>Nb. total de 1/2 journées d'absence</b></th>
	<th align='center'><b>Nb. absences non justifiées</b></th>
	<th align='center'><b>Nb. de retard</b></th>
	<th align='center'><b>Observations</b></th>
</tr>
<?php
if ($_SESSION['statut'] == "cpe") {
		$appel_donnees_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT e.* FROM eleves e, j_eleves_classes c, j_eleves_cpe j WHERE (c.id_classe='$id_classe' AND j.e_login = c.login AND e.login = j.e_login AND j.cpe_login = '".$_SESSION['login'] . "' AND c.periode = '$periode_num') order by e.nom, e.prenom");
	} else {
		$appel_donnees_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT e.* FROM eleves e, j_eleves_classes c WHERE ( c.id_classe='$id_classe' AND c.login = e.login AND c.periode='$periode_num') order by e.nom, e.prenom");
}

$nombre_lignes = mysqli_num_rows($appel_donnees_eleves);
$i = '0';
$num_id=10;
$alt=1;
while($i < $nombre_lignes) {
	$current_eleve_login = old_mysql_result($appel_donnees_eleves, $i, "login");
	$current_eleve_absences_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM  absences WHERE (login='$current_eleve_login' AND periode='$periode_num')");
	$current_eleve_nb_absences = @old_mysql_result($current_eleve_absences_query, 0, "nb_absences");
	$current_eleve_nb_nj = @old_mysql_result($current_eleve_absences_query, 0, "non_justifie");
	$current_eleve_nb_retards = @old_mysql_result($current_eleve_absences_query, 0, "nb_retards");
	$current_eleve_ap_absences = @old_mysql_result($current_eleve_absences_query, 0, "appreciation");
	$current_eleve_nom = old_mysql_result($appel_donnees_eleves, $i, "nom");
	$current_eleve_prenom = old_mysql_result($appel_donnees_eleves, $i, "prenom");
	$current_eleve_login_nb = $current_eleve_login."_nb_abs";
	$current_eleve_login_nj = $current_eleve_login."_nb_nj";
	$current_eleve_login_retard = $current_eleve_login."_nb_retard";
	$current_eleve_login_ap = $current_eleve_login."_ap";

	$alt=$alt*(-1);
	echo "<tr class='lig$alt'><td align='center'>".my_strtoupper($current_eleve_nom)." ".casse_mot($current_eleve_prenom,'majf2')."\n";
	echo "</td>\n";
	echo "<td align='center'>$current_eleve_nb_absences</td>\n";
	echo "<td align='center'>$current_eleve_nb_nj</td>\n";
	echo "<td align='center'>$current_eleve_nb_retards</td>\n";
	echo "<td>".nl2br($current_eleve_ap_absences)."</td></tr>\n";
	//=========================
	$i++;
	$num_id++;
}

?>
</table>
<p><br /></p>
<?php require "../lib/footer.inc.php";?>