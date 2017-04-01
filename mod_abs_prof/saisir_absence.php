<?php
/*
 * $Id$
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

//$traite_anti_inject="yes";

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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_abs_prof/saisir_absence.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/mod_abs_prof/saisir_absence.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Saisie des absences de professeurs',
statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('active_mod_abs_prof')) {
	header("Location: ../accueil.php?msg=Module désactivé");
	die();
}

if(($_SESSION['statut']=='scolarite')&&(!getSettingAOui('AbsProfSaisieAbsScol'))) {
	header("Location: ../accueil.php?msg=Vous n êtes pas autorisé à saisir les absences de professeurs");
	die();
}

if(($_SESSION['statut']=='cpe')&&(!getSettingAOui('AbsProfSaisieAbsCpe'))) {
	header("Location: ../accueil.php?msg=Vous n êtes pas autorisé à saisir les absences de professeurs");
	die();
}

$login_user=isset($_POST['login_user']) ? $_POST['login_user'] : (isset($_GET['login_user']) ? $_GET['login_user'] : NULL);
$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : NULL;
$display_heure_debut=isset($_POST['display_heure_debut']) ? $_POST['display_heure_debut'] : NULL;
$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : NULL;
$display_heure_fin=isset($_POST['display_heure_fin']) ? $_POST['display_heure_fin'] : NULL;
$titre=isset($_POST['titre']) ? $_POST['titre'] : NULL;
$description=isset($_POST['description']) ? $_POST['description'] : NULL;

$id_absence=isset($_POST['id_absence']) ? $_POST['id_absence'] : (isset($_GET['id_absence']) ? $_GET['id_absence'] : NULL);

//debug_var();

if((isset($login_user))&&
(isset($display_date_debut))&&
(isset($display_heure_debut))&&
(isset($display_date_fin))&&
(isset($display_heure_fin))&&
(isset($titre))&&
(isset($description))&&
(isset($_POST['enregistrer_dates']))) {
	check_token();

	$record="yes";

	$msg="";

	//get_mysql_date_from_slash_date($display_date_debut)

	if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $display_date_debut)) {
		$anneed = mb_substr($display_date_debut,6,4);
		$moisd = mb_substr($display_date_debut,3,2);
		$jourd = mb_substr($display_date_debut,0,2);
		while ((!checkdate($moisd, $jourd, $anneed)) and ($jourd > 0)) $jourd--;
		//$date_debut=$anneed."-".$moisd."-".$jourd." 00:00:00";
		$date_debut=$anneed."-".$moisd."-".$jourd;
	} else {
		$msg.= "ATTENTION : La date de début d'absence (<em>$display_date_debut</em>) n'est pas valide.<br />";
		$record = 'no';
	}

	if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $display_date_fin)) {
		$anneef = mb_substr($display_date_fin,6,4);
		$moisf = mb_substr($display_date_fin,3,2);
		$jourf = mb_substr($display_date_fin,0,2);
		while ((!checkdate($moisf, $jourf, $anneef)) and ($jourf > 0)) $jourf--;
		//$date_fin=$anneef."-".$moisf."-".$jourf." 00:00:00";
		$date_fin=$anneef."-".$moisf."-".$jourf;
	} else {
		$msg.= "ATTENTION : La date de fin d'absence (<em>$display_date_fin</em>) n'est pas valide.<br />";
		$record = 'no';
	}

	if(check_heure($display_heure_debut)) {
		$heure_debut=get_mysql_heure($display_heure_debut);
	}
	else {
		$msg.= "ATTENTION : L'heure de début d'absence (<em>$display_heure_debut</em>) n'est pas valide.<br />";
		$record = 'no';
	}

	if(check_heure($display_heure_fin)) {
		$heure_fin=get_mysql_heure($display_heure_fin, "fin");
	}
	else {
		$msg.= "ATTENTION : L'heure de début d'absence (<em>$display_heure_debut</em>) n'est pas valide.<br />";
		$record = 'no';
	}

	$description=html_entity_decode($description);

	$pos_crsf_alea=strpos($description,"_CSRF_ALEA_");
	if($pos_crsf_alea!==false) {
		$description=preg_replace("/_CSRF_ALEA_/","",$description);
		$msg.= "Contenu interdit.<br />";
		$record = 'no';
	}

	if((count($login_user)>1)&&(isset($id_absence))) {
		$msg.= "ERREUR : Il n'est pas possible de mettre à jour plusieurs absences d'un coup.<br />";
		$record = 'no';
	}

	if($record=="yes") {
		$date_debut.=" ".$heure_debut;
		$date_fin.=" ".$heure_fin;

		if(isset($id_absence)) {
			/*
			A FAIRE
			Si un remplacement a été accepté et que l'on change des dates, il faut supprimer le remplacement accepté et/ou avertir le prof.
			*/

// PROBLEME:
// Si on saisit l'absence de deux profs, il faut pouvoir en annuler un.
// Il vont avoir un id_absence chacun... du coup il faut les éditer un par un en cas de modif.

			$sql="UPDATE abs_prof SET date_debut='$date_debut', 
								date_fin='$date_fin', 
								titre='$titre',
								description='$description',
								login_user='".$login_user[0]."'
							WHERE id='$id_absence';";
			//echo "$sql<br />";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if($update) {
				$msg.="Mise à jour effectuée.<br />";
			}
			else {
				$msg.="Erreur lors de la mise à jour de l'absence.<br />";
			}
		}
		else {
			$tab_id_absence=array();
			$cpt=0;
			for($loop=0;$loop<count($login_user);$loop++) {
				// Contrôler si l'absence est déjà saisie... ou s'il y a une intersection avec une autre absence.
				$sql="SELECT * FROM abs_prof WHERE login_user='".$login_user[$loop]."' AND 
										(
										(date_debut<='$date_debut' AND date_fin>='$date_debut') OR 
										(date_debut<='$date_fin' AND date_fin>='$date_fin') OR 
										(date_debut>='$date_debut' AND date_fin<='$date_fin')
										);";
				//echo "$sql<br />";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$lig=mysqli_fetch_object($test);
					$msg.="L'absence saisie pour ".civ_nom_prenom($login_user[$loop])." ne peut pas être enregistrée&nbsp;:<br />Elle chevauche une autre absence (n°<a href='".$_SERVER['PHP_SELF']."?id_absence=$lig->id'>".$lig->id."</a>) du professeur.<br />";
				}
				else {
					$sql="INSERT INTO abs_prof SET date_debut='$date_debut', 
										date_fin='$date_fin', 
										titre='$titre',
										description='$description',
										login_user='".$login_user[$loop]."';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if($insert) {
						$tab_id_absence[$cpt]['id_absence']=mysqli_insert_id($GLOBALS["mysqli"]);
						$tab_id_absence[$cpt]['login_user']=$login_user[$loop];
						$msg.="Enregistrement effectué pour ".civ_nom_prenom($login_user[$loop]).".<br />";
						$cpt++;
					}
					else {
						$msg.="Erreur lors de l'enregistrement de l'absence pour ".civ_nom_prenom($login_user[$loop]).".<br />";
					}
				}
			}
			if(count($tab_id_absence)==1) {
				$id_absence=$tab_id_absence[0]['id_absence'];
			}
		}
	}

}

if((isset($id_absence))&&(!is_array($id_absence))) {
	$sql="SELECT * FROM abs_prof WHERE id='$id_absence';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		unset($id_absence);
	}
	else {
		$lig=mysqli_fetch_object($res);

		$date_debut=$lig->date_debut;
		$date_fin=$lig->date_fin;

		$display_date_debut=formate_date($date_debut);
		$display_date_fin=formate_date($date_fin);

		// Extraire l'heure de début/fin
		$display_heure_debut=get_heure_2pt_minute_from_mysql_date($date_debut);
		$display_heure_fin=get_heure_2pt_minute_from_mysql_date($date_fin);

		$titre=$lig->titre;
		$description=$lig->description;
		unset($login_user);
		$login_user[]=$lig->login_user;
	}
}

// Configuration du calendrier
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

/*
$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";
*/

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_suppression = "Confirmation de suppression";
//**************** EN-TETE *****************
$titre_page = "Saisie abs.prof";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

?>
<script src="../ckeditor_4/ckeditor.js"></script>
<?php

/*
A FAIRE
Si un remplacement a été accepté et que l'on change des dates, il faut supprimer le remplacement accepté et/ou avertir le prof.
*/

//==================================================================
echo "<a name=\"debut_de_page\"></a>
<p class='bold'>
	<a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if(!isset($login_user)) {
	echo "</p>

<h2>Saisie d'une absence de professeur(s)</h2>";

	echo js_checkbox_change_style("checkbox_change", 'texte_', "y");

	echo "
<form action=\"".$_SERVER['PHP_SELF']."#debut_de_page\" method=\"post\" style=\"width: 100%;\" name=\"formulaire_saisie_login_user\">
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		".((isset($id_absence)) ? "<input type='hidden' name='id_absence' value='".$id_absence."' />" : "")."

		<p>Sélectionnez la liste des professeur(s) absent(s) sur qui un créneau, qui quelques créneaux, qui un jour,...&nbsp;:</p>
		".liste_checkbox_utilisateurs(array('professeur'))."

		<p><input type='submit' value='Valider' /></p>

		<p><a href='javascript:cocher_decocher(true)'>Tout cocher</a> / <a href='javascript:cocher_decocher(false)'>Tout décocher</a></p>

	</fieldset>
</form>

";
	require("../lib/footer.inc.php");
	die();
}
//==================================================================
echo " | <a href='".$_SERVER['PHP_SELF']."'>Saisir une absence pour d'autres professeurs</a></p>

<h2>Saisie d'une absence de professeur(s)</h2>
";

// Problème: Si on a saisi d'un coup plusieurs absences sur un même créneau, on va avoir plusieurs id_absence...
// Il faut juste afficher les saisies effectuées à l'instant et proposer de les modifier ou de saisir une proposition de remplacement
// Remplir un...
/*
echo "DEBUG:<br />
count(\$tab_id_absence)=".count($tab_id_absence)."<br />";
echo "\$id_absence=".$id_absence."<br />";
*/
if((isset($tab_id_absence))&&(count($tab_id_absence)>1)) {
	echo "<p>Faire une proposition de remplacement ou un appel à remplacement pour l'absence de&nbsp;:</p>
	<ul>";
	for($loop=0;$loop<count($tab_id_absence);$loop++) {
		echo "
		<li><a href='proposer_remplacement.php?id_absence=".$tab_id_absence[$loop]['id_absence']."'>".civ_nom_prenom($tab_id_absence[$loop]['login_user'])."</a></li>";
	}
	echo "
	</ul>";

	echo "<p>Ou contrôler/modifier la saisie pour l'absence de&nbsp;:</p>
	<ul>";
	/*
	echo "<pre>";
	print_r($tab_id_absence);
	echo "</pre>";
	*/
	for($loop=0;$loop<count($tab_id_absence);$loop++) {
		echo "
		<li><a href='saisir_absence.php?id_absence=".$tab_id_absence[$loop]['id_absence']."&amp;login_user[]=".$tab_id_absence[$loop]['login_user']."'>".civ_nom_prenom($tab_id_absence[$loop]['login_user'])."</a></li>";
	}
	echo "
	</ul>";

	require("../lib/footer.inc.php");
	die();
}

//===============================================
$chaine_js_journee="";
$tab_creneau=get_tab_creneaux();
if(isset($tab_creneau["list"][0])) {
	$premier_creneau=$tab_creneau["list"][0]['nom_definie_periode'];
	$dernier_creneau=$tab_creneau["list"][count($tab_creneau["list"])-1]['nom_definie_periode'];
	$chaine_js_journee="<a href='#' onclick=\"document.getElementById('display_date_fin').value=document.getElementById('display_date_debut').value;
								document.getElementById('display_heure_debut').value='".$premier_creneau."';
								document.getElementById('display_heure_fin').value='".$dernier_creneau."';
								return false;\"
						title=\"Prendre la même date de fin que la date de début.\nPrendre le premier créneau de la journée comme heure de début et le dernier créneau de la journée pour l'heure de fin.\">Toute la journée</a>";
}
//===============================================

echo "
<form action=\"".$_SERVER['PHP_SELF']."#debut_de_page\" method=\"post\" style=\"width: 100%;\" name=\"formulaire_saisie_details\">
	<fieldset class='fieldset_opacite50'>

		".((isset($id_absence)) ? "<div style='float:right; width:10em;text-align:center;'><p><a href='proposer_remplacement.php?id_absence=$id_absence'>Faire une proposition de remplacement ou un appel à remplacement</a>.</p><p style='color:red'>A FAIRE: Indiquer le nombre de créneaux déjà remplacés sur le nombre de créneaux libérés.</p></div>" :"")."

		".add_token_field()."
		".((isset($id_absence)) ? "<input type='hidden' name='id_absence' value='".$id_absence."' />" : "")."

		<p>Vous souhaitez saisir une absence pour le ou les professeurs suivants&nbsp;:<br /><span class='bold'>";

$tab_prof=array();
for($loop=0;$loop<count($login_user);$loop++) {
	$tab_prof[$loop]=get_info_user($login_user[$loop]);
	if($loop>0) {
		echo ", ";
	}

	echo "<input type='hidden' name='login_user[]' value='".$login_user[$loop]."' />";
	echo $tab_prof[$loop]['denomination'];
}
echo "</span></p>

		<p>Veuillez préciser les dates/heures de début et de fin de l'".(isset($id_absence) ? "<span class='bold'>absence n°$id_absence</span>" : "absence")."&nbsp;:</p>

		<table class='boireaus boireaus_alt'>
			<tr>
				<th>Date de début</th>
				<td style='text-align:left;'>
					<input type='text' name='display_date_debut' id='display_date_debut' size='10' value=\"".(isset($display_date_debut) ? $display_date_debut : "")."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />
		".img_calendrier_js("display_date_debut", "img_bouton_display_date_debut")."
					 à/en 
					<input type='text' name = 'display_heure_debut' id= 'display_heure_debut' size='5' value = \"".(isset($display_heure_debut) ? $display_heure_debut : "")."\" onKeyDown=\"clavier_heure(this.id,event);\" AutoComplete=\"off\" />
					".choix_heure('display_heure_debut','div_choix_heure_debut', 'return')."
					".$chaine_js_journee."
				</td>
			</tr>

			<tr>
				<th>Date de fin</th>
				<td style='text-align:left;'>
					<input type='text' name='display_date_fin' id='display_date_fin' size='10' value=\"".(isset($display_date_fin) ? $display_date_fin : "")."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />
		".img_calendrier_js("display_date_fin", "img_bouton_display_date_fin")."
					 à/en 
					<input type='text' name = 'display_heure_fin' id= 'display_heure_fin' size='5' value = \"".(isset($display_heure_fin) ? $display_heure_fin : "")."\" onKeyDown=\"clavier_heure(this.id,event);\" AutoComplete=\"off\" />
					".choix_heure('display_heure_fin','div_choix_heure_fin', 'return')."
				</td>
			</tr>

			<tr>
				<th>Titre/motif</th>
				<td style='text-align:left;'>
					<input type='text' name='titre' id='titre' size='50' value=\"".(isset($titre) ? $titre : "")."\" />
				</td>
			</tr>

			<tr>
				<th>Description/détails</th>
				<td>";

?>

<textarea name="description" id ="description" style="border: 1px solid gray; width: 600px; height: 250px;"><?php echo preg_replace("/\\\\n/","",$description); ?></textarea>
<script type='text/javascript'>
// Configuration via JavaScript
CKEDITOR.replace('description',{
    customConfig: '../lib/ckeditor_gepi_config_mini.js'
});
</script>

<?php
echo "
				</td>
			</tr>

		</table>

		<input type='hidden' name='enregistrer_dates' value='y' />

		<p style='color:red'><input type='checkbox' name='info_ele_parents' id='info_ele_parents' value='y' /><label for='info_ele_parents'> Informer sans attendre les parents en page d'accueil.</label><br />
		<span style='color:red'>Cette option n'est pas encore implémentée...</span></p>

		<p><input type='submit' value='Valider' /></p>

	</fieldset>
</form>";

require("../lib/footer.inc.php");
?>
