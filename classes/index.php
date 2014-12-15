<?php
/*
*
* Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//**************** EN-TETE **************************************
$titre_page = "Gestion des classes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];
?>
<p class=bold>
<a href="../accueil_admin.php"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour </a>
| <a href='classes_param.php'>Paramétrage de plusieurs classes par lots</a>
| <a href='../init_xml2/init_alternatif.php?cat=classes' title="Création d'enseignements par lots">Créations par lots</a>
| <a href='cpe_resp.php'>Paramétrage rapide CPE Responsable</a>
| <a href='scol_resp.php' title="Définir les comptes scolarité associés à telles et telles classes.
Ce choix permet de limiter la liste des classes proposées aux différents comptes scolarité quand le suivi est réparti entre plusieurs personnes.">Paramétrage scolarité</a>
| <a href='acces_appreciations.php'>Paramétrage de l'accès aux appréciations</a>
<?php
	if(acces("/groupes/maj_inscript_ele_d_apres_edt.php", $_SESSION['statut'])) {
		$sql="SELECT 1=1 FROM edt_corresp2;";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0) {
			echo "| <a href='../groupes/maj_inscript_ele_d_apres_edt.php' title=\"Si vous avez importé votre emploi du temps depuis le fichier EXP_Cours.xml d'EDT, vous pouvez mettre à jour les inscriptions d'élèves dans les groupes Gepi à l'aide de l'export EXP_Eleves.xml d'EDT.\">Màj inscriptions élèves d'après EDT </a>";
		}
	}

	$groupes_de_groupes=getSettingValue('denom_groupes_de_groupes');
	if($groupes_de_groupes=="") {
		$groupes_de_groupes="ensembles de groupes";
	}

?>
| <a href='../groupes/repartition_ele_grp.php'>Répartir des élèves entre plusieurs groupes</a>
| <a href='../groupes/modify_grp_group.php'><?php echo ucfirst($groupes_de_groupes);?></a>
| <a href='../groupes/correction_inscriptions_grp_csv.php'>Correction CSV</a>
<?php
	if(getSettingAOui('active_carnets_notes')) {echo "| <a href='../cahier_notes_admin/creation_conteneurs_par_lots.php'>Créer des ".casse_mot(getSettingValue("gepi_denom_boite"), 'min')."s par lots</a>";}

	if(acces("/classes/dates_classes.php", $_SESSION['statut'])) {
		echo "| <a href='dates_classes.php' title=\"Définir des événements particuliers pour les classes (conseils de classe, arrêt des notes,...).\">Événements classe</a>";
	}

	if((getSettingAOui("active_mod_engagements"))&&(acces("/mod_engagements/saisie_engagements.php", $_SESSION['statut']))) {
		echo "| <a href='../mod_engagements/saisie_engagements.php'>Saisir les engagements </a>";
	}
?>
</p>
<p style='margin-top: 10px;'>
<img src='../images/icons/add.png' alt='' class='back_link' /> <a href="modify_nom_class.php">Ajouter une classe</a>
</p>

<?php
// On va chercher les classes déjà existantes, et on les affiche.
$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes ORDER BY classe");
$nombre_lignes = mysqli_num_rows($call_data);
if ($nombre_lignes != 0) {
	// 20130313
	$classe_sans_scol="n";
	$sql="SELECT c.* FROM classes c, periodes p WHERE p.id_classe=c.id AND c.id NOT IN (SELECT id_classe FROM j_scol_classes jsc, utilisateurs u WHERE u.login=jsc.login AND u.etat='actif');";
	//echo "$sql<br />";
	$test_scol=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_scol)>0) {
		$classe_sans_scol="y";
		$tab_classe_sans_scol=array();
		while($lig_tmp=mysqli_fetch_object($test_scol)) {
			$tab_classe_sans_scol[]=$lig_tmp->id;
		}
	}

	$flag = 1;
	//echo "<table cellpadding=3 cellspacing=0 style='border: none; border-collapse: collapse;'>\n";
	echo "<table class='boireaus padd_et_bordg'>\n";
	$i = 0;
	$alt=1;
	while ($i < $nombre_lignes){
		$alt=$alt*(-1);
		$id_classe = old_mysql_result($call_data, $i, "id");
		$classe = old_mysql_result($call_data, $i, "classe");
		echo "<tr";
		echo " class='lig$alt white_hover'";
		echo ">\n";
		echo "<td>\n";
		echo "<b>$classe</b> ";
		echo "</td>\n";

		echo "<td>\n";
		echo "<a href='periodes.php?id_classe=$id_classe'><img src='../images/icons/date.png' alt='Éditer les périodes associés à la classe' title='Éditer les périodes associés à la classe' /> Périodes</a></td>\n";
		//echo "<td>|<a href='modify_class.php?id_classe=$id_classe'>Gérer les matières</a></td>\n";

		$sql="select id_classe from periodes where id_classe = '$id_classe';";
		$res_nb_per=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_per = mysqli_num_rows($res_nb_per);
		echo "<td>\n";
		if ($nb_per != 0) {
			echo "<a href='classes_const.php?id_classe=$id_classe'><img src='../images/icons/edit_user.png' alt=\"Éditer les élèves associés à la classe\" title=\"Éditer les élèves associés à la classe\" /> Élèves</a>\n";
		}
		else {
			echo "&nbsp;";
		}
		echo "</td>\n";

		echo "<td>\n";
		echo "<a href='../groupes/edit_class.php?id_classe=$id_classe'> <img src='../images/icons/document.png' alt=\"Éditer les enseignements associés à la classe\" title=\"Éditer les enseignements associés à la classe\" /> Enseignements</a>\n";
		echo "</td>\n";

		echo "<td>\n";
		if ($nb_per != 0) {
			echo "[<a href='../groupes/edit_class_grp_lot.php?id_classe=$id_classe'>config. simplifiée</a>]\n";
		}
		else {
			echo "&nbsp;\n";
		}
		echo "</td>\n";

		echo "<td>\n";
		echo "<a href='modify_nom_class.php?id_classe=$id_classe'><img src='../images/icons/configure.png' alt=\"Éditer les paramètres de la classe\" title=\"Éditer les paramètres de la classe\" /> Paramètres</a>\n";
		echo "</td>\n";

		echo "<td>\n";
		echo "<a href='../lib/confirm_query.php?liste_cible=$id_classe&amp;action=del_classe".add_token_in_url()."'><img src='../images/icons/delete.png' alt='' /> Supprimer</a>\n";
		echo "</td>\n";

		//=======================================
		echo "<td>\n";
		if ($nb_per == 0) echo " <b>(Classe virtuelle)</b> "; else echo "&nbsp;";
		echo "</td>\n";

		// 20130313
		if($classe_sans_scol=="y") {
			echo "<td>\n";
			if(in_array($id_classe, $tab_classe_sans_scol)) {
				echo "<a href='scol_resp.php'><img src='../images/icons/ico_attention.png' width='22' height='19' title=\"Aucun compte scolarité n'est associé à cette classe.\" alt='Classe sans compte scolarité' /></a>\n";
			}
			echo "</td>\n";
		}
		echo "</tr>\n";
		$i++;
	}

	echo "</table>\n";
}
else {
	echo "<p class='grand'>Attention : aucune classe n'a été définie dans la base GEPI !</p>\n";
	echo "<p>Vous pouvez ajouter des classes à la base en cliquant sur le lien ci-dessus, ou bien directement<br />
	<a href='../init_xml2/index.php'>importer les élèves et les classes à partir de fichiers XML de Sconet</a><br />
	ou encore <a href='../init_csv/index.php'>importer les élèves et les classes à partir de fichiers CSV</a></p>\n";
}
require("../lib/footer.inc.php");
?>
