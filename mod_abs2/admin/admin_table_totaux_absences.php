<?php
/*
* Copyright 2001, 2013 Thomas Belliard, Eric Lebrun, Stephane Boireau
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

$niveau_arbo = 2;
// Initialisations files
//include("../../lib/initialisationsPropel.inc.php");
require_once("../../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
}

// Check access
// INSERT INTO droits SET id='/mod_abs2/admin/admin_table_totaux_absences.php', administrateur='V', scolarite='F', cpe='F', professeur='F', secours='F', eleve='F', responsable='F';
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}


if(getSettingAOui('abs2_import_manuel_bulletin')) {
    header("Location: ../../accueil.php?msg=Vous n importez pas les absences depuis mod_abs2");
    die();
}

/*
if($_SESSION['statut']!='administrateur') {
	$acces_agr="n";
	if(($_SESSION['statut']=='cpe')&&(getSettingAOui('AccesCpeAgregationAbs2'))) {
		$acces_agr="y";
	}

	if($acces_agr=="n") {
		header("Location: ../../logout.php?auto=1");
		die();
	}
}
*/

$msg="";

if(isset($_POST['vider_table_absences'])) {
	check_token();
	$sql="TRUNCATE TABLE absences;";
	$vidage=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if($vidage) {
		$msg="La table 'absences' a été vidée.<br />";
	}
	else {
		$msg="Erreur lors du vidage de la table 'absences'.<br />";
	}
}

//**************** EN-TETE *****************
$titre_page = "Absences: Totaux";
require_once("../../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

if(isset($_POST['is_posted'])) {
	check_token(false);

	echo "<p class='bold'><a href='./index.php'>Retour</a></p>

<h2>Remplissage de la table 'absences'</h2>
";

	$sql="SELECT id, classe FROM classes ORDER BY classe;";
	$res_clas=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res_clas)>0) {

		// A revoir: On n'utilise pas forcément le CDT
		$date_0=getSettingValue('begin_bookings');

		while($lig_clas=mysqli_fetch_object($res_clas)) {
			// Récupération des dates de périodes de la classe
			$id_classe=$lig_clas->id;
			$classe=$lig_clas->classe;
			include("../../lib/periodes.inc.php");

			echo "<p class='bold' style='margin-left:1em;'>Classe de ".$classe."</p>\n";

			for($i=1;$i<$nb_periode;$i++) {
				echo "<p class='bold' style='margin-left:2em;'>".$nom_periode[$i]."</p>\n";

				if($i==1) {
					$date_debut=strftime("%Y-%m-%d 00:00:00", $date_0);
				}
				else {
					$date_debut=$date_fin_periode[$i-1];
				}
				$date_fin=$date_fin_periode[$i];

				echo "<div style='margin-left:3em; margin-bottom:2em;'>
	<table class='boireaus boireaus_alt' summary='Tableau des absences de la classe de ".$classe." en période $i'>
		<tr>
			<th>Élève</th>
			<th>Nombre d'absences</th>
			<th>Non justifiées</th>
			<th>Retards</th>
			<th>Enregistement</th>
		</tr>";

				// Récupération de la liste des élèves de la classe
				$sql="SELECT e.* FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='$id_classe' AND periode='$i' ORDER BY e.nom, e.prenom;";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res)>0) {
					while($lig=mysqli_fetch_object($res)) {
						// Total des absences de l'élève:
						$sql="select distinct date_demi_jounee from a_agregation_decompte where eleve_id='$lig->id_eleve' and manquement_obligation_presence!='0' and retards='0' and date_demi_jounee>='$date_debut' AND date_demi_jounee<'$date_fin';";
						//echo "$sql<br />";
						$res_abs=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						$nb_abs=mysqli_num_rows($res_abs);

						// Les absences non justifiées de l'élève:
						$sql="select distinct date_demi_jounee from a_agregation_decompte where eleve_id='$lig->id_eleve' and manquement_obligation_presence!='0' and non_justifiee='1' and retards='0' and date_demi_jounee>='$date_debut' AND date_demi_jounee<'$date_fin';";
						//echo "$sql<br />";
						$res_nj=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						$nb_nj=mysqli_num_rows($res_nj);

						// Les retards de l'élève:
						//$sql="select distinct date_demi_jounee from a_agregation_decompte where eleve_id='$lig->id_eleve' and manquement_obligation_presence!='0' and retards='1' and date_demi_jounee>='$date_debut' AND date_demi_jounee<'$date_fin';";
						$sql="select distinct date_demi_jounee from a_agregation_decompte where eleve_id='$lig->id_eleve' and retards='1' and date_demi_jounee>='$date_debut' AND date_demi_jounee<'$date_fin';";
						//echo "$sql<br />";
						$res_ret=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						$nb_ret=mysqli_num_rows($res_ret);

						echo "
		<tr class='white_hover'>
			<td>".$lig->nom." ".$lig->prenom."</td>
			<td>$nb_abs</td>
			<td>$nb_nj</td>
			<td>$nb_ret</td>";

						$sql="SELECT 1=1 FROM absences WHERE login='".$lig->login."' AND periode='$i';";
						$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($test)>0) {
							$sql="UPDATE absences SET nb_absences='$nb_abs',
															non_justifie='$nb_nj',
															nb_retards='$nb_ret'
														WHERE login='".$lig->login."' AND periode='$i';";
						}
						else {
							$sql="INSERT INTO absences SET login='".$lig->login."',
															periode='$i',
															nb_absences='$nb_abs',
															non_justifie='$nb_nj',
															nb_retards='$nb_ret';";
						}
						$reg=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(!$reg) {
							echo "
			<td><img src='../../images/disabled.png' class='icon16' alt='Echec' title=\"Echec de l'enregistrement\"/></td>";
						}
						else {
							echo "
			<td><img src='../../images/enabled.png' class='icon16' alt='Succès' title=\"Succès de l'enregistrement\"/></td>";
						}

						echo "
		</tr>";

						flush();
					}
				}

				echo "
	</table>
</div>
";

			}
		}
	}
	echo "<p>Terminé.</p>";

}
else {
	echo "<p class='bold'><a href='./index.php'>Retour</a></p>

<h2>Table 'absences'</h2>

<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset id='infosPerso' style='border: 1px solid grey; background-image: url(\"../../images/background/opacite50.png\"); '>
		<legend style='border: 1px solid grey; background-color: white; '>Remplissage de la table 'absences'</legend>
		".add_token_field()."
		<p>Remplir la table 'absences'&nbsp;: <br />
		<input type='hidden' name='is_posted' value='y' />
		<input type='submit' value='Valider' />
	</fieldset>
</form>

<p><br /></p>

<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset id='infosPerso' style='border: 1px solid grey; background-image: url(\"../../images/background/opacite50.png\"); '>
		<legend style='border: 1px solid grey; background-color: white; '>Vidage de la table 'absences'</legend>
		".add_token_field()."
		<p>Vider la table 'absences'&nbsp;: <br />
		<input type='hidden' name='vider_table_absences' value='y' />
		<input type='submit' value='Valider' />
	</fieldset>
</form>

<p><br /></p>

<p style='text-indent:-4em;margin-left:4em;'><em>NOTE&nbsp;:</em> Lorsque l'on importe les absences de Sconet, ou que l'on saisit les totaux d'absences manuellement, la table 'absences' est remplie.<br />
Quand on utilise le module Absences 2 de Gepi complètement, la table 'absences' n'est pas remplie.<br />
Le remplissage de cette table peut se révéler utile dans divers modules (<em>Genèse des classes,...</em>).<br />
La présente page permet de remplir la table 'absences' d'après les saisies agrégées dans la table 'a_agregation_decompte'.<br />
Pour que les totaux soient corrects, il peut être utile de procéder au <a href='admin_table_agregation.php'>remplissage de la table 'a_agregation_decompte'</a> d'abord.</p>
\n";
}



require("../../lib/footer.inc.php");

?>
