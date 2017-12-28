<?php
/**
 *
 *
 * Copyright 2017-2018 Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_discipline/extraire_pointages.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/mod_discipline/extraire_pointages.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Discipline: Pointages petits incidents: Extraction',
statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if(!getSettingAOui("active_mod_discipline")) {
	die("Le module n'est pas activé.");
}

if(!getSettingAOui("active_mod_disc_pointage")) {
	die("Le dispositif de pointages disciplinaires n'est pas activé.");
}

$tab_type_pointage_discipline=get_tab_type_pointage_discipline();
/*
echo "<pre>";
print_r($tab_type_pointage_discipline);
echo "</pre>";
*/
//debug_var();

if(count($tab_type_pointage_discipline)==0) {
	$sql="INSERT INTO sp_types_saisies SET nom='Travail', description='Travail non fait';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	$sql="INSERT INTO sp_types_saisies SET nom='Matériel', description='Matériel manquant';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	$sql="INSERT INTO sp_types_saisies SET nom='Comportement', description='Comportement gênant';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);
$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : (isset($_GET['display_date_debut']) ? $_GET['display_date_debut'] : NULL);
$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : (isset($_GET['display_date_fin']) ? $_GET['display_date_fin'] : NULL);
$id_creneau=isset($_POST['id_creneau']) ? $_POST['id_creneau'] : (isset($_GET['id_creneau']) ? $_GET['id_creneau'] : NULL);

$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : (isset($_GET['login_ele']) ? $_GET['login_ele'] : NULL);

//==================================
if(!isset($display_date_debut)) {
	$display_date_debut=strftime("%d/%m/%Y", getSettingValue('begin_bookings'));
}
$tab_date=explode("/", $display_date_debut);
$mois=$tab_date[1];
$jour=$tab_date[0];
$annee=$tab_date[2];
$ts_display_date_debut=mktime(0, 0, 0, $mois, $jour, $annee);


if(!isset($display_date_fin)) {
	$display_date_fin=strftime("%d/%m/%Y");
}
$tab_date=explode("/", $display_date_fin);
$mois=$tab_date[1];
$jour=$tab_date[0];
$annee=$tab_date[2];
$ts_display_date_fin=mktime(0, 0, 0, $mois, $jour, $annee);
//==================================


if((isset($id_classe))&&(is_array($id_classe))&&(isset($_POST['export_csv']))) {
	$msg="";

	$tab_nom_classe=array();
	for($loop=0;$loop<count($id_classe);$loop++) {
		$tab_nom_classe[$id_classe[$loop]]=get_valeur_champ("classes", "id='".$id_classe[$loop]."'", "classe");
	}

	if(isset($login_ele)) {
		$sql="SELECT DISTINCT sps.*
						FROM sp_saisies sps 
						WHERE sps.login='".$login_ele."' AND 
							date_sp>='".strftime("%Y-%m-%d 00:00:00", $ts_display_date_debut)."' AND 
							date_sp<='".strftime("%Y-%m-%d 23:59:00", $ts_display_date_fin)."' 
						ORDER BY date_sp, id_type;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$eleve=get_info_eleve($login_ele);
			$csv="Élève;Date mysql;Date;Type;Commentaire;Saisi par;\r\n";
			while($lig=mysqli_fetch_object($res)) {
				$csv.=$eleve['nom'].' '.$eleve['prenom'].';';
				$csv.=$lig->date_sp.';';
				$csv.=formate_date($lig->date_sp, "y", "court").';';
				$csv.=$tab_type_pointage_discipline['id_type'][$lig->id_type]['nom'].';';
				$csv.=preg_replace('/[ ]{2,}/', ' ', str_replace("\n", ' ',str_replace("\r", ' ', str_replace(';', '.,', $lig->commentaire)))).';';
				$csv.=civ_nom_prenom($lig->created_by).';';
				$csv.="\r\n";
			}

			$designation_eleve=remplace_accents($eleve['nom'].' '.$eleve['prenom'],'all');

			$nom_fic="extraction_pointages_menus_incidents_".$designation_eleve."_du_".strftime("%Y%m%d", $ts_display_date_debut)."_au_".strftime("%Y%m%d", $ts_display_date_fin)."_effectuee_le_".strftime("%Y%m%d_%H%M%S").".csv";
			send_file_download_headers('text/x-csv',$nom_fic);
			echo echo_csv_encoded($csv);
			die();
		}
		else {
			$msg.="Aucune donnée n'est extraite.<br />";
		}
	}
	else {
		$designation_classes='';

		$tab_deja=array();
		$tab_eff=array();
		for($loop=0;$loop<count($id_classe);$loop++) {
			$sql="SELECT DISTINCT sps.*, jec.id_classe, e.nom, e.prenom
							FROM sp_saisies sps, 
								j_eleves_classes jec, 
								eleves e 
							WHERE jec.login=e.login AND 
								jec.login=sps.login AND 
								date_sp>='".strftime("%Y-%m-%d 00:00:00", $ts_display_date_debut)."' AND 
								date_sp<='".strftime("%Y-%m-%d 23:59:00", $ts_display_date_fin)."' AND 
								jec.id_classe='".$id_classe[$loop]."' 
							ORDER BY login, date_sp;";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				if($designation_classes!='') {$designation_classes.='_';}
				$designation_classes.=get_nom_classe($id_classe[$loop]);
				while($lig=mysqli_fetch_object($res)) {
					if(!in_array($lig->id, $tab_deja)) {
						$tab_eff[$lig->login]['nom']=$lig->nom;
						$tab_eff[$lig->login]['prenom']=$lig->prenom;
						$tab_eff[$lig->login]['id_classe']=$lig->id_classe;

						if(!isset($tab_eff[$lig->login]['type'][$lig->id_type])) {
							$tab_eff[$lig->login]['type'][$lig->id_type]=0;
						}
						$tab_eff[$lig->login]['type'][$lig->id_type]++;

						if(!isset($tab_eff[$lig->login]['total'])) {
							$tab_eff[$lig->login]['total']=0;
						}
						$tab_eff[$lig->login]['total']++;

						$tab_deja[]=$lig->id;
					}
				}
			}
		}

		if(count($tab_eff)>0) {

			$tab_type_pointage_discipline=get_tab_type_pointage_discipline();

			$csv="Élève;Classe;Total;";

			for($loop=0;$loop<count($tab_type_pointage_discipline['indice']);$loop++) {
				$csv.=$tab_type_pointage_discipline['indice'][$loop]['nom'].";";
			}
			$csv.="\r\n";

			foreach($tab_eff as $login_ele => $eleve) {
				$csv.=$eleve['nom']." ".$eleve['prenom'].";";
				$csv.=$tab_nom_classe[$eleve['id_classe']].";";
				$csv.=$eleve['total'].";";

				for($loop=0;$loop<count($tab_type_pointage_discipline['indice']);$loop++) {
					$valeur=0;
					if(isset($eleve['type'][$tab_type_pointage_discipline['indice'][$loop]['id_type']])) {
						$valeur=$eleve['type'][$tab_type_pointage_discipline['indice'][$loop]['id_type']];
					}
					$csv.=$valeur.";";
				}
				$csv.="\r\n";
			}

			$designation_classes=remplace_accents($designation_classes,'all');

			$nom_fic="extraction_pointages_menus_incidents_".$designation_classes."_du_".strftime("%Y%m%d", $ts_display_date_debut)."_au_".strftime("%Y%m%d", $ts_display_date_fin)."_effectuee_le_".strftime("%Y%m%d_%H%M%S").".csv";
			send_file_download_headers('text/x-csv',$nom_fic);
			echo echo_csv_encoded($csv);
			die();
		}
		else {
			$msg.="Aucune donnée n'est extraite.<br />";
		}
	}
}

//$active_module_trombinoscopes=getSettingAOui('active_module_trombinoscopes');

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** DEBUT EN-TETE ***************
$titre_page = "Pointages disciplinaires";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

$acces_visu_eleve=acces("/eleves/visu_eleve.php", $_SESSION['statut']);

$ajout_lien="";
if(acces_param_pointage_discipline()) {
	$ajout_lien=" | <a href='param_pointages.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Paramétrer, définir les types de pointages</a>";
}
//debug_var();

// Choix du jour
if(!isset($jour)) {
	$jour=strftime("%d/%m/%Y");
	//$ts_jour=;
}
// Choix de l'enseignement ou de la classe ou d'un élève
if((!isset($id_classe))&&(!isset($id_groupe))&&(!isset($login_ele))) {

	echo "
<p class='bold' style='margin-bottom:1em;'>
	<a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
	 | <a href='saisie_pointages.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir</a>
	$ajout_lien
</p>

<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" style=\"width: 100%;\" name=\"formulaire_choix\">
	<!--fieldset class='fieldset_opacite50' style='margin-bottom:1em;'-->
	<p style='margin-bottom:1em;'>Extraire les saisies entre le 
		<input type='text' name='display_date_debut' id='display_date_debut' size='10' value='$display_date_debut' 
					onkeydown='clavier_date_plus_moins(this.id,event);' />".img_calendrier_js("display_date_debut", "img_bouton_display_date_debut")." 
		et le 
		<input type='text' name='display_date_fin' id='display_date_fin' size='10' value='$display_date_fin' 
					onkeydown='clavier_date_plus_moins(this.id,event);' />".img_calendrier_js("display_date_fin", "img_bouton_display_date_fin")."
	</p>
	<!--
	<p>Pour la classe ou le groupe suivant&nbsp;:<br />
	-->
	<p>Pour la ou les classes suivantes&nbsp;:<br />";

	/*
	// 20171130 : Récupérer la liste des classes pour lesquelles il y a des saisies
	// PB: On cumule avec les jec.periode de l'élève
	$tab_eff=array();
	$sql="SELECT DISTINCT id_classe, COUNT(sps.id) AS nb FROM sp_saisies sps, 
						j_eleves_classes jec 
					WHERE jec.login=sps.login AND 
						date_sp>='".strftime("%Y-%m-%d 00:00:00", $ts_display_date_debut)."' AND 
						date_sp<='".strftime("%Y-%m-%d 23:59:00", $ts_display_date_fin)."'
					GROUP BY jec.id_classe;";
	echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_eff[$lig->id_classe]=$lig->nb;
		}
	}
	*/

	/*
	MariaDB [gepidev]> SELECT DISTINCT id_classe, sps.* FROM sp_saisies sps, j_eleves_classes jec WHERE jec.login=sps.login AND date_sp>='2017-09-01 00:00:00' AND date_sp<='2017-11-30 23:59:00' and jec.login='duchnock';
	+-----------+----+---------+----------+---------------------+-------------+---------------------+------------+
	| id_classe | id | id_type | login    | date_sp             | commentaire | created_at          | created_by |
	+-----------+----+---------+----------+---------------------+-------------+---------------------+------------+
	|        39 |  8 |       3 | duchnock | 2017-11-30 13:27:00 |             | 2017-11-30 12:30:54 | LOPEZM     |
	|        39 |  9 |       1 | duchnock | 2017-11-30 13:27:00 |             | 2017-11-30 12:30:54 | LOPEZM     |
	|        39 | 16 |       3 | duchnock | 2017-11-30 08:55:00 |             | 2017-11-30 12:31:08 | LOPEZM     |
	|        39 | 17 |       2 | duchnock | 2017-11-30 08:55:00 |             | 2017-11-30 12:31:08 | LOPEZM     |
	|        39 | 18 |       1 | duchnock | 2017-11-30 08:55:00 |             | 2017-11-30 12:31:08 | LOPEZM     |
	+-----------+----+---------+----------+---------------------+-------------+---------------------+------------+
	5 rows in set (0.01 sec)

	MariaDB [gepidev]> SELECT DISTINCT id_classe, COUNT(sps.id) AS nb FROM sp_saisies sps, j_eleves_classes jec WHERE jec.login=sps.login AND date_sp>='2017-09-01 00:00:00' AND date_sp<='2017-11-30 23:59:00' GROUP BY jec.id_classe;
	+-----------+----+
	| id_classe | nb |
	+-----------+----+
	|        37 |  6 |
	|        38 |  9 |
	|        39 | 21 |
	+-----------+----+
	3 rows in set (0.00 sec)

	MariaDB [gepidev]> 
	*/

	$sql="SELECT id, classe FROM classes ORDER BY classe;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>Aucune classe n'a été trouvée.</p>";
	}
	else {
		echo "<div class='fieldset_opacite50' style='float:left; width:45%; padding:0.5em; margin-right:0.5em;'>
	<p style='text-indent:-4em; margin-left:4em;'>Choisissez une ou des classes pour laquelle consulter les menus incidents pointés&nbsp;:<br />";
		while($lig=mysqli_fetch_object($res)) {
			echo "
		<input type='checkbox' name='id_classe[]' id='id_classe_".$lig->id."' value='".$lig->id."' onchange=\"checkbox_change(this.id)\" /><label for='id_classe_".$lig->id."' id='texte_id_classe_".$lig->id."'>".$lig->classe;
			//if(isset($tab_eff[$lig->id])) {
			//	echo " <em title=\"".$tab_eff[$lig->id]." pointage(s) relevés sur la période choisie.\">(".$tab_eff[$lig->id].")</em>";
			//}
			echo "</label><br />";
		}
		echo "</p>
		<p><input type='submit' value='Extraire' /></p>
</form>";
		/*
		if($_SESSION['statut']=='professeur') {
			$groups=get_groups_for_prof($_SESSION['login']);

			echo "<div class='fieldset_opacite50' style='float:left; width:45%; padding:0.5em;'>
	<p style='text-indent:-4em; margin-left:4em;'>Choisissez un enseignement pour lequel effectuer une saisie&nbsp;:<br />";
			foreach($groups as $current_group) {
				echo "
		<a href='".$_SERVER['PHP_SELF']."?mode=groupe&amp;id_groupe=".$current_group['id']."'>".$current_group['name']." (".$current_group['description'].") en ".$current_group['classlist_string']."</a><br />";
			}
			echo "</p>
</div>";
		}
		*/
	}

	echo "<script type='text/javascript'>".js_checkbox_change_style()."</script>";

	require_once("../lib/footer.inc.php");
	die();
}


// Affichage des classes/dates choisies et ce qui est extrait

//if(($mode=="groupe")||($mode=="classe")) {
if((isset($id_classe))&&(!isset($login_ele))) {
	echo "<p class='bold' style='margin-bottom:1em;'>
<a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/>Choisir d'autres classes</a>
$ajout_lien
</p>";

	echo "
<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" style=\"width: 100%;\" name=\"formulaire_extraction\">
	<fieldset class='fieldset_opacite50' style='margin-bottom:1em;'>

	<p>Classe(s) de&nbsp;: ";
	$tab_nom_classe=array();
	for($loop=0;$loop<count($id_classe);$loop++) {
		$tab_nom_classe[$id_classe[$loop]]=get_valeur_champ("classes", "id='".$id_classe[$loop]."'", "classe");
		if($loop>0) {echo ", ";}
		echo $tab_nom_classe[$id_classe[$loop]];
		echo "
		<input type='hidden' name='id_classe[]' value='".$id_classe[$loop]."' />";
	}
	echo "</p>
	<p>Du
		<input type='text' name='display_date_debut' id='display_date_debut' size='10' value='$display_date_debut' 
					onkeydown='clavier_date_plus_moins(this.id,event);' />".img_calendrier_js("display_date_debut", "img_bouton_display_date_debut")." 
		au 
		<input type='text' name='display_date_fin' id='display_date_fin' size='10' value='$display_date_fin' 
					onkeydown='clavier_date_plus_moins(this.id,event);' />".img_calendrier_js("display_date_fin", "img_bouton_display_date_fin")."
			<input type='submit' value='Valider' />
	</p>";

	$tab_deja=array();
	$tab_eff=array();
	for($loop=0;$loop<count($id_classe);$loop++) {
		$sql="SELECT DISTINCT sps.*, jec.id_classe, e.nom, e.prenom
						FROM sp_saisies sps, 
							j_eleves_classes jec, 
							eleves e 
						WHERE jec.login=e.login AND 
							jec.login=sps.login AND 
							date_sp>='".strftime("%Y-%m-%d 00:00:00", $ts_display_date_debut)."' AND 
							date_sp<='".strftime("%Y-%m-%d 23:59:00", $ts_display_date_fin)."' AND 
							jec.id_classe='".$id_classe[$loop]."' 
						ORDER BY login, date_sp;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				if(!in_array($lig->id, $tab_deja)) {
					$tab_eff[$lig->login]['nom']=$lig->nom;
					$tab_eff[$lig->login]['prenom']=$lig->prenom;
					$tab_eff[$lig->login]['id_classe']=$lig->id_classe;

					if(!isset($tab_eff[$lig->login]['type'][$lig->id_type])) {
						$tab_eff[$lig->login]['type'][$lig->id_type]=0;
					}
					$tab_eff[$lig->login]['type'][$lig->id_type]++;

					if(!isset($tab_eff[$lig->login]['total'])) {
						$tab_eff[$lig->login]['total']=0;
					}
					$tab_eff[$lig->login]['total']++;

					$tab_deja[]=$lig->id;
				}
			}
		}
	}

	if(count($tab_eff)>0) {
		$tab_type_pointage_discipline=get_tab_type_pointage_discipline();
		echo "<table class='boireaus boireaus_alt resizable sortable'>
	<thead>
		<tr>
			<th class='text'>Élève</th>".($acces_visu_eleve ? "
			<th class='nosort'></th>" : "")."
			<th class='text'>Classe</th>
			<th class='number'>Total</th>";

		for($loop=0;$loop<count($tab_type_pointage_discipline['indice']);$loop++) {
			echo "
			<th class='number' title=\"".$tab_type_pointage_discipline['indice'][$loop]['nom']."\n".$tab_type_pointage_discipline['indice'][$loop]['description']."\">".$tab_type_pointage_discipline['indice'][$loop]['nom']."</th>";
		}
		echo "
		</tr>
	</thead>
	<tbody>";
		foreach($tab_eff as $login_ele => $eleve) {
			echo "
		<tr>
			<td>".$eleve['nom']." ".$eleve['prenom']."</td>".($acces_visu_eleve ? "
			<td><a href='$gepiPath/eleves/visu_eleve.php?ele_login=".$login_ele."&onglet=discipline' title=\"Voir les incidents disciplinaires de l'élève dans le classeur élève.\"><img src='$gepiPath/images/icons/ele_onglets.png' class='icone16' alt='Visu' /></a></td>" : "")."
			<td>".$tab_nom_classe[$eleve['id_classe']]."</td>
			<td>".$eleve['total']."</td>";

			for($loop=0;$loop<count($tab_type_pointage_discipline['indice']);$loop++) {
				$valeur=0;
				if(isset($eleve['type'][$tab_type_pointage_discipline['indice'][$loop]['id_type']])) {
					$valeur=$eleve['type'][$tab_type_pointage_discipline['indice'][$loop]['id_type']];
					$valeur="<a href='".$_SERVER['PHP_SELF']."?id_classe=".$eleve['id_classe']."&display_date_debut=$display_date_debut&display_date_fin=$display_date_fin&login_ele=$login_ele' title=\"Voir/extraire les $mod_disc_terme_menus_incidents de cet élève sur l'intervalle de dates choisi.\">".$valeur."</a>";
				}
				echo "
			<td>".$valeur."</td>";
			}
			echo "
		</tr>";
		}
		echo "
	</tbody>
</table>
</fieldset>
</form>";
	}


	echo "
<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" style=\"width: 100%;\" name=\"formulaire_export\">
	<fieldset class='fieldset_opacite50' style='margin-bottom:1em;margin-top:1em;'>
	<p style='margin-bottom:1em;margin-top:1em;'>
		<input type='hidden' name='export_csv' value='y' />
		<input type='hidden' name='display_date_debut' value='$display_date_debut' />
		<input type='hidden' name='display_date_fin' value='$display_date_fin' />";
	for($loop=0;$loop<count($id_classe);$loop++) {
		echo "
		<input type='hidden' name='id_classe[]' value='".$id_classe[$loop]."' />";
	}
	echo "
		<input type='submit' value='Exporter en CSV' />
	</p>
	</fieldset>
</form>";

}
elseif(isset($login_ele)) {
	// Ajouter un lien autres élèves de la même classe si id_classe est défini
	$ajout_form="";
	$champ_id_classe="";
	if(isset($id_classe)) {
		$champ_id_classe="<input type='hidden' name='id_classe' value='$id_classe' />";
		$champ_id_classe2="<input type='hidden' name='id_classe[]' value='$id_classe' />";
		$sql="SELECT DISTINCT jec.login, e.nom, e.prenom
						FROM sp_saisies sps, 
							j_eleves_classes jec, 
							eleves e 
						WHERE jec.login=e.login AND 
							jec.login=sps.login AND 
							date_sp>='".strftime("%Y-%m-%d 00:00:00", $ts_display_date_debut)."' AND 
							date_sp<='".strftime("%Y-%m-%d 23:59:00", $ts_display_date_fin)."' AND 
							jec.id_classe='".$id_classe."' 
						ORDER BY e.nom, e.prenom;";
		$res_ele=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_ele)>0) {
			$ajout_form.="
		 | 
		<input type='hidden' name='display_date_debut' value='$display_date_debut' />
		<input type='hidden' name='display_date_fin' value='$display_date_fin' />
		<input type='hidden' name='id_classe' value='$id_classe' />
		<select name='login_ele' id='login_ele' onchange=\"document.getElementById('form_choix_ele_classe').submit();\">";
			while($lig_ele=mysqli_fetch_object($res_ele)) {
				$selected='';
				if($lig_ele->login==$login_ele) {
					$selected=' selected';
				}
				$ajout_form.="
			<option value='".$lig_ele->login."'".$selected.">".$lig_ele->nom." ".$lig_ele->prenom."</option>";
			}
			$ajout_form.="
		</select>";
		}
	}

	echo "<form action='".$_SERVER['PHP_SELF']."' method='post' id='form_choix_ele_classe'>
	<p class='bold' style='margin-bottom:1em;'>
		<a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/>Choisir une classe</a>
		$ajout_lien
		$ajout_form
	</p>
</form>";

	$eleve=get_info_eleve($login_ele);
	$chaine_liens_classes='';
	if(isset($eleve['id_classes'])) {
		$chaine_liens_classes=' (';
		for($loop=0;$loop<count($eleve['id_classes']);$loop++) {
			if($loop>0) {$chaine_liens_classes.=', ';}
			$chaine_liens_classes.="<a href='".$_SERVER['PHP_SELF']."?id_classe[0]=".$eleve['id_classes'][$loop]."&display_date_debut=$display_date_debut&display_date_fin=$display_date_fin' title=\"Voir les pointages pour cette classe.\">".get_nom_classe($eleve['id_classes'][$loop])."</a>";
			
		}
		$chaine_liens_classes.=')';
	}

	if($acces_visu_eleve) {
		$lien_visu_eleve=" <a href='$gepiPath/eleves/visu_eleve.php?ele_login=".$login_ele."&onglet=discipline' title=\"Voir les incidents disciplinaires de l'élève dans le classeur élève.\"><img src='$gepiPath/images/icons/ele_onglets.png' class='icone16' alt='Visu' /></a>";
	}

	echo "
<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" style=\"width: 100%;\" name=\"formulaire_extraction\">
	<fieldset class='fieldset_opacite50' style='margin-bottom:1em;'>
		$champ_id_classe

		<p class='bold'>
			Élève&nbsp;: ".$eleve['nom'].' '.$eleve['prenom'].$chaine_liens_classes.$lien_visu_eleve."
			<input type='hidden' name='login_ele' value='".$login_ele."' />
		</p>

		<p>Du
			<input type='text' name='display_date_debut' id='display_date_debut' size='10' value='$display_date_debut' 
						onkeydown='clavier_date_plus_moins(this.id,event);' />".img_calendrier_js("display_date_debut", "img_bouton_display_date_debut")." 
			au 
			<input type='text' name='display_date_fin' id='display_date_fin' size='10' value='$display_date_fin' 
						onkeydown='clavier_date_plus_moins(this.id,event);' />".img_calendrier_js("display_date_fin", "img_bouton_display_date_fin")."
				<input type='submit' value='Valider' />
		</p>";

	echo "
		<table class='boireaus boireaus_alt resizable sortable'>
			<thead>
				<tr>
					<th class='text'>Date</th>
					<th class='text'>Type</th>
					<th class='text'>Commentaire</th>
					<th class='text'>Saisi par</th>
				</tr>
			</thead>
			<tbody>";

	$sql="SELECT DISTINCT sps.*
					FROM sp_saisies sps 
					WHERE sps.login='".$login_ele."' AND 
						date_sp>='".strftime("%Y-%m-%d 00:00:00", $ts_display_date_debut)."' AND 
						date_sp<='".strftime("%Y-%m-%d 23:59:00", $ts_display_date_fin)."' 
					ORDER BY date_sp, id_type;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			echo "
				<tr>
					<td><span style='display:none'>".$lig->date_sp."</span>".formate_date($lig->date_sp, "y", "court")."</td>
					<td>".$tab_type_pointage_discipline['id_type'][$lig->id_type]['nom']."</td>
					<td>".$lig->commentaire."</td>
					<td>".civ_nom_prenom($lig->created_by)."</td>
				</tr>";
		}
	}
	echo "
			</tbody>
		</table>
	</fieldset>
</form>";



	echo "
<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" style=\"width: 100%;\" name=\"formulaire_export\">
	<fieldset class='fieldset_opacite50' style='margin-bottom:1em;margin-top:1em;'>
	<p style='margin-bottom:1em;margin-top:1em;'>
		<input type='hidden' name='export_csv' value='y' />
		<input type='hidden' name='display_date_debut' value='$display_date_debut' />
		<input type='hidden' name='display_date_fin' value='$display_date_fin' />
		$champ_id_classe2
		<input type='hidden' name='login_ele' value='".$login_ele."' />
		<input type='submit' value='Exporter en CSV' />
	</p>
	</fieldset>
</form>";

}
else {
	echo "<p style='color:red'>Mode non encore implémenté.</p>";
}

//echo "<p style='margin-top:1em;'><span style='color:red'>A FAIRE&nbsp;:</span> Afficher le total annuel et le total de la période courante.</p>";
require_once("../lib/footer.inc.php");
?>
