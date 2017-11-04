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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_abs_prof/index.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/mod_abs_prof/index.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='F',
autre='F',
description='Absences et remplacements de professeurs',
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

$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM abs_prof_remplacement LIKE 'id_aid';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE abs_prof_remplacement ADD id_aid INT(11) NOT NULL AFTER id_groupe;");
}

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

if((isset($mode))&&($mode=="suppr_abs")&&(isset($_POST['suppr_abs']))) {
	check_token();

	$msg="";
	$suppr_abs=$_POST['suppr_abs'];

	$nb_suppr=0;
	for($loop=0;$loop<count($suppr_abs);$loop++) {
		// A FAIRE : Commencer par sélectionner les remplacements validés ou acceptés pour avertir par mail et/ou en page d'accueil l'utilisateur.

		$sql="DELETE FROM abs_prof_remplacement WHERE id_absence='".$suppr_abs[$loop]."';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$del) {
			$msg.="Erreur lors de la suppression des propositions de remplacements associées à l'absence n°".$suppr_abs[$loop].".<br />";
		}
		else {
			$sql="DELETE FROM abs_prof WHERE id='".$suppr_abs[$loop]."';";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$del) {
				$msg.="Erreur lors de la suppression de l'absence n°".$suppr_abs[$loop].".<br />";
			}
			else {
				$nb_suppr++;
			}
		}
	}

	$msg.=$nb_suppr." absence(s) supprimée(s).<br />";
}

if($_SESSION['statut']=='professeur') {
	//if((isset($_POST['is_posted']))&&($_POST['is_posted']==1)) {
	if(isset($_POST['is_posted'])) {
		check_token();

		$msg="";

		$reponse_proposition=isset($_POST['reponse_proposition']) ? $_POST['reponse_proposition'] : array();
		$commentaire_proposition=isset($_POST['commentaire_proposition']) ? $_POST['commentaire_proposition'] : array();

		$nb_update=0;
		foreach($reponse_proposition as $key => $value) {
			$sql="SELECT * FROM abs_prof_remplacement WHERE id='$key' AND login_user='".$_SESSION['login']."' AND date_fin_r>='".strftime('%Y-%m-%d %H:%M:%S')."';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)==0) {
				$msg.="La proposition de remplacement n°$key ne vous est pas attribuée ou la date du remplacement est passée.<br />";
			}
			else {
				if(check_proposition_remplacement_validee2($key)==$_SESSION['login']) {
					// On ne devrait pas arriver là sauf saisies quasi-simultanées côté admin et prof
					$msg.="La proposition de remplacement n°$key a été validée par l'Administration.<br />Le remplacement vous est attribué.<br />S'il faut revenir sur ce choix, il est indispensable de le faire oralement en prenant langue avec l'Administration.<br />";
				}
				else {
					$detail_sql="";
					$lig=mysqli_fetch_object($test);
					if($reponse_proposition[$key]!=$lig->reponse) {
						$detail_sql.="reponse='".$reponse_proposition[$key]."', date_reponse='".strftime('%Y-%m-%d %H:%M:%S')."'";
					}

					// Pas sûr que le test fonctionne bien avec les antislashes, retours à la ligne,...
					//if($commentaire_proposition[$key]!=$lig->commentaire_prof) {
					if(stripslashes(preg_replace('/(\\\n)/',"\n",$commentaire_proposition[$key]))!=$lig->commentaire_prof) {
						if($detail_sql!="") {
							$detail_sql.=", ";
						}
						$detail_sql.="commentaire_prof='".$commentaire_proposition[$key]."'";
					}

					if($detail_sql!="") {
						$sql="UPDATE abs_prof_remplacement SET $detail_sql WHERE id='$key';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$update) {
							$msg.="Erreur lors de l'enregistrement de la réponse ou du commentaire pour la proposition n°$key<br />";
						}
						else {
							$nb_update++;
						}
					}
				}
			}
		}

		if($nb_update>0) {
			$msg.="$nb_update réponse(s) ou commentaire(s) saisis.<br />";
		}
	}
}

//$javascript_specifique[] = "lib/tablekit";
//$utilisation_tablekit="ok";

$avec_js_et_css_edt="y";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
// onclick=\"return confirm_abandon (this, change, '$themessage')\"
$message_suppression = "Confirmation de suppression";
//**************** EN-TETE *****************
$titre_page = "Absences prof";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

if((($_SESSION['statut']=="administrateur")||
	(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AbsProfSaisieAbsScol')))||
	(($_SESSION['statut']=="cpe")&&(getSettingAOui('AbsProfSaisieAbsCpe'))))&&
	(isset($mode))&&($mode=="suppr_abs")) {

	echo "<a name=\"debut_de_page\"></a>
<p class='bold'>
	<a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>

<h2>Absences de professeurs</h2>";

	$sql="SELECT * FROM abs_prof WHERE date_fin>='".strftime("%Y-%m-%d %H:%M:%S")."';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "
<p>Aucune absence à venir n'est saisie.</p>";
	}
	else {
		echo "
<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='mode' value='suppr_abs' />

		<p>Les absences à venir sont les suivantes.<br />
		Cochez les absences que vous souhaitez supprimer.<br />
		Les propositions de remplacements et affichages associés seront supprimés également.</p>";
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			echo "<p><input type='checkbox' name='suppr_abs[]' id='suppr_abs_$cpt' value='$lig->id' /><label for='suppr_abs_$cpt'> Absence de ".civ_nom_prenom($lig->login_user)." entre le ".formate_date($lig->date_debut, "y2", "court")." et le ".formate_date($lig->date_fin, "y2", "court")."</label></p>
			<div style='margin-left:2em; color:green;'".$lig->description."</div>";
			$cpt++;
		}
		echo "
		<p><input type='submit' value=\"Supprimer les absences cochées\" /></p>
	</fieldset>
</form>";


	}

	require("../lib/footer.inc.php");
	die();
}

echo "<a name=\"debut_de_page\"></a>
<p class='bold'>
	<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>

<h2>Absences de professeurs</h2>
<ul>";

if(($_SESSION['statut']=="administrateur")||
($_SESSION['statut']=="scolarite")||
($_SESSION['statut']=="cpe")) {


	if(($_SESSION['statut']=="administrateur")||
	(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AbsProfSaisieAbsScol')))||
	(($_SESSION['statut']=="cpe")&&(getSettingAOui('AbsProfSaisieAbsCpe')))) {
		echo "
	<li>
		<p><a href='saisir_absence.php'>Saisir une absence</a></p>
	</li>";
	}

	$sql="SELECT * FROM abs_prof WHERE date_fin>='".strftime('%Y-%m-%d %H:%M:%S')."' ORDER BY date_debut;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "
	<li>
		<p><span style='color:red'>Aucune absence à venir ou en cours n'est saisie.</span></p>
	</li>";
	}
	else {
		echo "
	<li>
		<p><strong>Suivre les propositions de remplacements&nbsp;:</strong></p>
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th colspan='3'>Absences</th>
					<th colspan='4'>Propositions de remplacements</th>
					<th rowspan='2'>Remplacements</th>
					<th rowspan='2'>Afficher<br />Informer les familles</th>
				</tr>
				<tr>
					<th>Id</th>
					<th>Prof</th>
					<th>Dates</th>

					<th>Créneaux proposés</th>
					<th>Accepté</th>
					<th>Rejeté</th>
					<th>Validé</th>
				</tr>
			</thead>
			<tbody>";

		$nom_classe=array();
		$nom_prof=array();
		while($lig=mysqli_fetch_object($res)) {
			$ts1=mysql_date_to_unix_timestamp($lig->date_debut);
			$date_heure=french_strftime("Du %A %d/%m/%Y %H:%M", $ts1);
			$ts2=mysql_date_to_unix_timestamp($lig->date_fin);
			$date_heure.="<br />".french_strftime(" au %A %d/%m/%Y %H:%M", $ts2);
			//formate_date($lig->date_debut,"y")."<br />au ".formate_date($lig->date_fin,"y")

			echo "
				<tr>";
			if(($_SESSION['statut']=="administrateur")||
			(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AbsProfSaisieAbsScol')))||
			(($_SESSION['statut']=="cpe")&&(getSettingAOui('AbsProfSaisieAbsCpe')))) {
				echo "
					<td><a href='saisir_absence.php?id_absence=$lig->id' title=\"Consulter/Modifier l'absence\">".$lig->id."</a></td>
					<td><a href='saisir_absence.php?id_absence=$lig->id' title=\"Consulter/Modifier l'absence\">".civ_nom_prenom($lig->login_user)."</a></td>
					<td><a href='saisir_absence.php?id_absence=$lig->id' title=\"Consulter/Modifier l'absence\">".$date_heure."</a></td>";
			}
			else {
				echo "
				<td>".$lig->id."</td>
				<td>".civ_nom_prenom($lig->login_user)."</td>
				<td>".$date_heure."</td>";
			}

			$sql="SELECT DISTINCT jour, id_creneau FROM abs_prof_remplacement WHERE id_absence='$lig->id';";
			$res_rempl=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb=mysqli_num_rows($res_rempl);
			if(($_SESSION['statut']=="administrateur")||
			(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AbsProfProposerRemplacementScol')))||
			(($_SESSION['statut']=="cpe")&&(getSettingAOui('AbsProfProposerRemplacementCpe')))) {
				echo "
					<td title='$nb créneaux avec remplacement proposé.\n\nCliquer pour consulter/modifier/ effectuer des propositions.'><a href='proposer_remplacement.php?id_absence=$lig->id'>$nb</a></td>";
			}
			else {
			echo "
					<td title='$nb créneaux avec remplacement proposé.'>";
				echo $nb;
			echo "</td>";
			}

			$sql="SELECT 1=1 FROM abs_prof_remplacement WHERE id_absence='$lig->id' AND reponse='oui';";
			$res_rempl=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb=mysqli_num_rows($res_rempl);
			if(($nb>0)&&
				(($_SESSION['statut']=="administrateur")||
				(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AbsProfAttribuerRemplacementScol')))||
				(($_SESSION['statut']=="cpe")&&(getSettingAOui('AbsProfAttribuerRemplacementCpe'))))) {
				echo "
					<td><a href='attribuer_remplacement.php'>".$nb."</a></td>";
			}
			else {
				echo "
						<td title='$nb réponses positives.'>".$nb."</td>";
			}

			$sql="SELECT 1=1 FROM abs_prof_remplacement WHERE id_absence='$lig->id' AND reponse='non';";
			$res_rempl=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb=mysqli_num_rows($res_rempl);
			echo "
					<td title='$nb réponses négatives.'>".$nb."</td>";

			$sql="SELECT * FROM abs_prof_remplacement WHERE id_absence='$lig->id' AND validation_remplacement='oui' ORDER BY date_debut_r, id_classe;";
			$res_rempl=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb=mysqli_num_rows($res_rempl);
			echo "
					<td title='$nb remplacements validés.'>".$nb."</td>";

			// Liste des remplacements
			echo "
					<td>
						<table class='boireaus boireaus_alt'>";
			while($lig_rempl=mysqli_fetch_object($res_rempl)) {
				$ts1=mysql_date_to_unix_timestamp($lig_rempl->date_debut_r);
				$date_heure=french_strftime("%A %d/%m/%Y de %H:%M", $ts1);
				$ts2=mysql_date_to_unix_timestamp($lig_rempl->date_fin_r);
				$date_heure.=strftime(" à %H:%M", $ts2);


				$style="";
				if($ts2<time()) {
					$style=" style='background-color:grey;'";
				}

				echo "
							<tr$style>
								<td>";
				if(!isset($nom_classe[$lig_rempl->id_classe])) {
					$nom_classe[$lig_rempl->id_classe]=get_nom_classe($lig_rempl->id_classe);
				}
				echo $nom_classe[$lig_rempl->id_classe];

				echo "
								</td>
								<td>";
				echo $date_heure;
				echo "
								</td>
								<td>";

				if(!isset($nom_prof[$lig_rempl->login_user])) {
					$nom_prof[$lig_rempl->login_user]=affiche_utilisateur($lig_rempl->login_user, $lig_rempl->id_classe);
				}
				echo $nom_prof[$lig_rempl->login_user];
				echo "
								</td>
							</tr>";
			}
			echo "
						</table>
					</td>";

			// Familles informées
			$sql="SELECT 1=1 FROM abs_prof_remplacement WHERE id_absence='$lig->id' AND validation_remplacement='oui' AND info_famille='oui';";
			$res_rempl=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb=mysqli_num_rows($res_rempl);
			echo "
					<td><a href='afficher_remplacements.php?mode=tous'>".$nb."</a></td>";


			echo "
				</tr>";
		}

		echo "
			</tbody>
		</table>
	</li>";

	}

	// Conserver des liens pour valider après coup,...
	// Le tableau ne donne que l'en-cours

	/*
	echo "
<p>Choix&nbsp;:</p>
<ul>";

	if(($_SESSION['statut']=="administrateur")||
	(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AbsProfSaisieAbsScol')))||
	(($_SESSION['statut']=="cpe")&&(getSettingAOui('AbsProfSaisieAbsCpe')))) {
		echo "
	<li><a href='saisir_absence.php'>Saisir une absence</a></li>";
	}

	if(($_SESSION['statut']=="administrateur")||
	(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AbsProfSaisieAbsScol')))||
	(($_SESSION['statut']=="cpe")&&(getSettingAOui('AbsProfSaisieAbsCpe')))) {
		echo "
	<li>Consulter/modifier les absences à venir, en cours, proposer des remplacements&nbsp;:<br />";

		$sql="SELECT * FROM abs_prof WHERE date_fin>='".strftime('%Y-%m-%d %H:%M:%S')."' ORDER BY date_debut;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<span style='color:red'>Aucune absence à venir ou en cours n'est saisie.</span>";
		}
		else {
			while($lig=mysqli_fetch_object($res)) {
				$chaine_nb_creneaux_remplacement_propose="";
				$sql="SELECT DISTINCT jour, id_creneau FROM abs_prof_remplacement WHERE id_absence='$lig->id';";
				$res_rempl=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_rempl)>0) {
					$chaine_nb_creneaux_remplacement_propose=" (<em>".mysqli_num_rows($res_rempl)." créneaux avec remplacement proposé</em>)";
				}

				$chaine_nb_creneaux_remplacement_valides="";
				$sql="SELECT DISTINCT jour, id_creneau FROM abs_prof_remplacement WHERE id_absence='$lig->id' AND validation_remplacement='oui';";
				$res_rempl=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_rempl)>0) {
					$chaine_nb_creneaux_remplacement_valides=" (<em>".mysqli_num_rows($res_rempl)." créneaux avec remplacement validé</em>)";
				}

				echo "
		<strong>Absence n°$lig->id&nbsp;:</strong> <a href='saisir_absence.php?id_absence=$lig->id'>".civ_nom_prenom($lig->login_user)." (<em>du ".formate_date($lig->date_debut,"y")." au ".formate_date($lig->date_fin,"y")."</em>)</a>".$chaine_nb_creneaux_remplacement_propose.$chaine_nb_creneaux_remplacement_valides."<br />";
			}
		}
	}

	if(($_SESSION['statut']=="administrateur")||
	(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AbsProfProposerRemplacementScol')))||
	(($_SESSION['statut']=="cpe")&&(getSettingAOui('AbsProfProposerRemplacementCpe')))) {
		echo "</li>
	<li>Proposer des remplacements&nbsp;:<br />";

		$sql="SELECT * FROM abs_prof WHERE date_fin>='".strftime('%Y-%m-%d %H:%M:%S')."' ORDER BY date_debut;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<span style='color:red'>Aucune absence à venir ou en cours n'est saisie.</span>";
		}
		else {
			while($lig=mysqli_fetch_object($res)) {
				$chaine_nb_creneaux_remplacement_propose="";
				$sql="SELECT DISTINCT jour, id_creneau FROM abs_prof_remplacement WHERE id_absence='$lig->id';";
				$res_rempl=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_rempl)>0) {
					$chaine_nb_creneaux_remplacement_propose=" (<em>".mysqli_num_rows($res_rempl)." créneaux avec remplacement proposé</em>)";
				}

				$chaine_nb_creneaux_remplacement_valides="";
				$sql="SELECT DISTINCT jour, id_creneau FROM abs_prof_remplacement WHERE id_absence='$lig->id' AND validation_remplacement='oui';";
				$res_rempl=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_rempl)>0) {
					$chaine_nb_creneaux_remplacement_valides=" (<em>".mysqli_num_rows($res_rempl)." créneaux avec remplacement validé</em>)";
				}

				echo "
		<strong>Absence n°$lig->id&nbsp;:</strong> <a href='proposer_remplacement.php?id_absence=$lig->id'>".civ_nom_prenom($lig->login_user)." (<em>du ".formate_date($lig->date_debut,"y")." au ".formate_date($lig->date_fin,"y")."</em>)</a>".$chaine_nb_creneaux_remplacement_propose.$chaine_nb_creneaux_remplacement_valides."<br />";
			}
		}
		echo "
	</li>";
	}
	*/

	if(($_SESSION['statut']=="administrateur")||
	(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AbsProfAttribuerRemplacementScol')))||
	(($_SESSION['statut']=="cpe")&&(getSettingAOui('AbsProfAttribuerRemplacementCpe')))) {
		$tab_propositions_avec_reponse_positive=array();
		$sql="SELECT * FROM abs_prof_remplacement WHERE date_debut_r>='".strftime('%Y-%m-%d %H:%M:%S')."' AND reponse='oui';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				if(check_proposition_remplacement_validee2($lig->id)=="") {
					// Créneau sans remplacement programmé
					$tab_propositions_avec_reponse_positive[]=$lig->id;
				}
			}
		}
		if(count($tab_propositions_avec_reponse_positive)>0) {
			echo "
	<li>
		<p><strong>Remplacements à venir&nbsp;:</strong><br />".count($tab_propositions_avec_reponse_positive)." proposition(s) a(ont) reçu une réponse favorable (<em>pour des cours/créneaux dont le remplacement n'est pas encore attribué</em>).<br />
		<a href='attribuer_remplacement.php'>Valider/attribuer le(s) remplacement(s)</a></p>
	</li>";
		}


		$tab_remplacements=array();
		//$sql="SELECT * FROM abs_prof_remplacement WHERE date_debut_r<'".strftime('%Y-%m-%d %H:%M:%S')."';";
		$sql="SELECT DISTINCT id_absence, id_classe, id_groupe, id_aid, jour, id_creneau FROM abs_prof_remplacement WHERE date_debut_r<'".strftime('%Y-%m-%d %H:%M:%S')."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$tab_remplacements_creneaux_testes=array();
			while($lig=mysqli_fetch_object($res)) {
				if(!in_array($lig->id_absence."|".$lig->id_groupe."|".$lig->id_aid."|".$lig->id_classe."|".$lig->jour."|".$lig->id_creneau ,$tab_remplacements_creneaux_testes)) {
					if(check_proposition_remplacement_validee($lig->id_absence, $lig->id_groupe, $lig->id_aid, $lig->id_classe, $lig->jour, $lig->id_creneau)=="") {
						// Créneau sans remplacement programmé
						$tab_remplacements[]=$lig->id_absence."|".$lig->id_groupe."|".$lig->id_aid."|".$lig->id_classe."|".$lig->jour."|".$lig->id_creneau;
					}
					$tab_remplacements_creneaux_testes[]=$lig->id_absence."|".$lig->id_groupe."|".$lig->id_aid."|".$lig->id_classe."|".$lig->jour."|".$lig->id_creneau;
				}
			}
		}
		if(count($tab_remplacements)>0) {
			echo "
	<li>
		<p><strong>Éventuels remplacements passés non validés&nbsp;:</strong><br />".count($tab_remplacements)." remplacement(s) de cours (<em>dans le passé</em>) n'a(ont) pas été enregistré(s).<br />
		S'il(s) a(ont) eu lieu, vous pouvez les attribuer maintenant à des fins de statistiques/totaux dans le cas où des rémunérations de remplacements sont prévues.<br />
		<a href='attribuer_remplacement.php?mode=anciens'>Valider/attribuer le(s) remplacement(s)</a></p>
	</li>";
		}
	}

	echo "
	<li><p><a href='afficher_remplacements.php'>Afficher les remplacements validés</a> et informer ou non les familles.</p></li>";
	$sql="SELECT * FROM abs_prof WHERE date_fin>='".strftime("%Y-%m-%d %H:%M:%S")."';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "
	<li>Supprimer des absences à venir saisies&nbsp;: Aucune absence à venir n'est saisie.</li>";
	}
	else {
		echo "
	<li><a href='".$_SERVER['PHP_SELF']."?mode=suppr_abs'>Supprimer des absences à venir saisies</a></li>";
	}
	echo "
	<li><a href='consulter_remplacements.php'>Consulter les absences passées pour générer des listes d'absences, de remplacements,... entre telle date et telle date</a>.</li>
</ul>";


echo "<p style='color:red; margin-top:1em; text-indent:-5em; margin-left:5em;'>A FAIRE : 
Pouvoir afficher tous les remplacements à effectuer pour une journée donnée de façon à proposer au mieux les remplacements aux divers professeurs (a priori) disponibles.<br />
Pouvoir générer un tableau/listing des remplacements par semaine.<br />
</p>";

}
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
elseif($_SESSION['statut']=="professeur") {

	//============================================================================================================
	// Dispositif pour l'affichage EDT en infobulle
	if(getSettingAOui('autorise_edt_tous')) {

		$titre_infobulle="EDT de <span id='id_ligne_titre_infobulle_edt'></span>";
		$texte_infobulle="";
		$tabdiv_infobulle[]=creer_div_infobulle('edt_prof',$titre_infobulle,"",$texte_infobulle,"",40,0,'y','y','n','n');

		function affiche_lien_edt_prof($login_prof, $info_prof) {
			return " <a href='../edt_organisation/index_edt.php?login_edt=".$login_prof."&amp;type_edt_2=prof&amp;no_entete=y&amp;no_menu=y&amp;lien_refermer=y' onclick=\"affiche_edt_prof_en_infobulle('$login_prof', '".addslashes($info_prof)."');return false;\" title=\"Emploi du temps de ".$info_prof."\" target='_blank'><img src='../images/icons/edt.png' class='icone16' alt='EDT' /></a>";
		}

		$titre_infobulle="EDT de la classe de <span id='span_id_nom_classe'></span>";
		$texte_infobulle="";
		$tabdiv_infobulle[]=creer_div_infobulle('edt_classe',$titre_infobulle,"",$texte_infobulle,"",40,0,'y','y','n','n');

		echo "
<style type='text/css'>
	.lecorps {
		margin-left:0px;
	}
</style>

<script type='text/javascript'>
	function affiche_edt_classe_en_infobulle(id_classe, classe) {
		document.getElementById('span_id_nom_classe').innerHTML=classe;

		new Ajax.Updater($('edt_classe_contenu_corps'),'../edt_organisation/index_edt.php?login_edt='+id_classe+'&type_edt_2=classe&visioedt=classe1&no_entete=y&no_menu=y&mode_infobulle=y',{method: 'get'});
		afficher_div('edt_classe','y',-20,20);
	}

	function affiche_edt_prof_en_infobulle(login_prof, info_prof) {
		document.getElementById('id_ligne_titre_infobulle_edt').innerHTML=info_prof;

		new Ajax.Updater($('edt_prof_contenu_corps'),'../edt_organisation/index_edt.php?login_edt='+login_prof+'&type_edt_2=prof&no_entete=y&no_menu=y&mode_infobulle=y',{method: 'get'});
		afficher_div('edt_prof','y',-20,20);
	}
</script>\n";
	}
	else {
		function affiche_lien_edt_prof($login_prof, $info_prof) {
			return "";
		}
	}
	//============================================================================================================

	$info_prof=civ_nom_prenom($_SESSION['login']);

	echo "<div id='fixe'>".affiche_lien_edt_prof($_SESSION['login'], $info_prof)."</div>";

	$tab_creneaux=get_heures_debut_fin_creneaux();

	// Total des propositions, remplacements et remplacements passés
	$nb_propositions_ou_remplacements=0;

	// Propositions en attente de réponse
	$tab=get_tab_propositions_remplacements($_SESSION['login'], "en_attente");
	if(count($tab)>0) {
		$nb_propositions_ou_remplacements+=count($tab);
		echo "
<h3>Propositions de remplacement en attente d'une réponse de votre part</h3>

<form action=\"".$_SERVER['PHP_SELF']."#debut_de_page\" method=\"post\" style=\"width: 100%; margin-left:3em;\">
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."

		<p class='bold'>".count($tab)." proposition(s) vous est(sont) soumise(s).<br />Une réponse serait bienvenue.</p>
		<ul>";
		$temoin_saisie_possible=0;
		for($loop=0;$loop<count($tab);$loop++) {
			/*
			echo "<pre>";
			print_r($tab[$loop]);
			echo "</pre>";
			*/

			$nom_classe=get_nom_classe($tab[$loop]['id_classe']);
			if(($tab[$loop]['id_groupe']!="")&&($tab[$loop]['id_groupe']!="0")) {
				$info_groupe=get_info_grp($tab[$loop]['id_groupe'], array('description', 'matieres', 'classes', 'profs'));
			}
			else {
				$info_groupe=get_info_aid($tab[$loop]['id_aid'], array('nom_general_complet', 'classes', 'profs'));
			}


			// Ne pas proposer de répondre à un remplacement déjà attribué
			$attribue_a=check_proposition_remplacement_validee($tab[$loop]['id_absence'], $tab[$loop]['id_groupe'], $tab[$loop]['id_aid'], $tab[$loop]['id_classe'], $tab[$loop]['jour'], $tab[$loop]['id_creneau']);

			if($attribue_a=="") {
				echo "
			<li>
				<p style='margin-top:1em;'><strong>Le ".formate_date($tab[$loop]['date_debut_r'], "n", "complet")." en ".$tab_creneaux[$tab[$loop]['id_creneau']]['nom_creneau']." (<em>".$tab_creneaux[$tab[$loop]['id_creneau']]['debut_court']." à ".$tab_creneaux[$tab[$loop]['id_creneau']]['fin_court']."</em>)&nbsp;: $nom_classe</strong> (<em style='font-size:x-small;'>remplacement de $info_groupe</em>)</p>
				<input type='radio' name='reponse_proposition[".$tab[$loop]['id']."]' id='reponse_proposition_".$tab[$loop]['id']."_oui' value='oui' onchange=\"checkbox_change('reponse_proposition_".$tab[$loop]['id']."_oui');checkbox_change('reponse_proposition_".$tab[$loop]['id']."_non');checkbox_change('reponse_proposition_".$tab[$loop]['id']."_vide')\" /><label for='reponse_proposition_".$tab[$loop]['id']."_oui' id='texte_reponse_proposition_".$tab[$loop]['id']."_oui'> Accepter la proposition</label><br />
				<input type='radio' name='reponse_proposition[".$tab[$loop]['id']."]' id='reponse_proposition_".$tab[$loop]['id']."_non' value='non' onchange=\"checkbox_change('reponse_proposition_".$tab[$loop]['id']."_oui');checkbox_change('reponse_proposition_".$tab[$loop]['id']."_non');checkbox_change('reponse_proposition_".$tab[$loop]['id']."_vide')\" /><label for='reponse_proposition_".$tab[$loop]['id']."_non' id='texte_reponse_proposition_".$tab[$loop]['id']."_non'> Rejeter la proposition</label><br />
				<input type='radio' name='reponse_proposition[".$tab[$loop]['id']."]' id='reponse_proposition_".$tab[$loop]['id']."_vide' value='' onchange=\"checkbox_change('reponse_proposition_".$tab[$loop]['id']."_oui');checkbox_change('reponse_proposition_".$tab[$loop]['id']."_non');checkbox_change('reponse_proposition_".$tab[$loop]['id']."_vide')\" checked /><label for='reponse_proposition_".$tab[$loop]['id']."_vide' id='texte_reponse_proposition_".$tab[$loop]['id']."_vide' style='font-weight:bold;'> Ne pas répondre pour le moment</label><br />
				Commentaire&nbsp;: <textarea name='commentaire_proposition[".$tab[$loop]['id']."]' style='vertical-align:top;'></textarea>
			</li>";
				$temoin_saisie_possible++;
			}
			else {
				echo "
			<li>
				<p style='color:grey; margin-top:1em; margin-bottom:1em;'><strong>Le ".formate_date($tab[$loop]['date_debut_r'], "n", "complet")." en ".$tab_creneaux[$tab[$loop]['id_creneau']]['nom_creneau']." (<em>".$tab_creneaux[$tab[$loop]['id_creneau']]['debut']." à ".$tab_creneaux[$tab[$loop]['id_creneau']]['fin']."</em>)&nbsp;: $nom_classe</strong> (<em style='font-size:x-small;'>remplacement de $info_groupe</em>)&nbsp:<br />Remplacement attribué à ".$attribue_a.".</p>
			</li>";
			}
		}
		echo "
		</ul>

		".(($temoin_saisie_possible>0) ? "<input type='hidden' name='is_posted' value='1' /><p><input type='submit' value='Valider' /></p>" : "")."

		<p style='text-indent:-4em;margin-left:4em;margin-top:1em;'><em>NOTE&nbsp;:</em> Une fois que vous avez accepté une proposition, une validation de la part de l'Administration doit encore être faite.<br />
		La proposition de remplacer un professeur sur un créneau peut en effet avoir été soumise à plusieurs professeurs.<br />
		L'Administration choisira qui sera l'heureux élu;)</p>
	</fieldset>
</form>";
	}


	// Propositions (dans le futur) ayant reçu une réponse de la part du professeur
	$tab=get_tab_propositions_remplacements($_SESSION['login'], "futures_avec_reponse");
	if(count($tab)>0) {
		$nb_propositions_ou_remplacements+=count($tab);
		echo "
<h3>Propositions de remplacement auxquelles vous avez répondu</h3>

<form action=\"".$_SERVER['PHP_SELF']."#debut_de_page\" method=\"post\" style=\"width: 100%; margin-left:3em;\">
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."

		<p class='bold'>Vous avez répondu à la(aux) ".count($tab)." proposition(s) suivante(s).</p>
		<ul>";
		$temoin_saisie_possible=0;
		for($loop=0;$loop<count($tab);$loop++) {
			/*
			echo "<pre>";
			print_r($tab[$loop]);
			echo "</pre>";
			*/

			$nom_classe=get_nom_classe($tab[$loop]['id_classe']);
			if(($tab[$loop]['id_groupe']!="")&&($tab[$loop]['id_groupe']!="0")) {
				$info_groupe=get_info_grp($tab[$loop]['id_groupe'], array('description', 'matieres', 'classes', 'profs'));
			}
			else {
				$info_groupe=get_info_aid($tab[$loop]['id_aid'], array('nom_general_complet', 'classes', 'profs'));
			}

			// Ne pas proposer de répondre à un remplacement déjà attribué
			$attribue_a=check_proposition_remplacement_validee($tab[$loop]['id_absence'], $tab[$loop]['id_groupe'], $tab[$loop]['id_aid'], $tab[$loop]['id_classe'], $tab[$loop]['jour'], $tab[$loop]['id_creneau']);

			if($attribue_a=="") {
				if($tab[$loop]['reponse']=='oui') {
					$checked_oui=" checked";
					$checked_non="";

					$style_oui=" style='font-weight:bold;'";
					$style_non="";
				}
				elseif($tab[$loop]['reponse']=='non') {
					$checked_oui="";
					$checked_non=" checked";

					$style_oui="";
					$style_non=" style='font-weight:bold;'";
				}

				echo "
			<li>
				<p><strong>Le ".formate_date($tab[$loop]['date_debut_r'], "n", "complet")." en ".$tab_creneaux[$tab[$loop]['id_creneau']]['nom_creneau']." (<em>".$tab_creneaux[$tab[$loop]['id_creneau']]['debut_court']." à ".$tab_creneaux[$tab[$loop]['id_creneau']]['fin_court']."</em>)&nbsp;: $nom_classe</strong> (<em style='font-size:x-small;'>remplacement de $info_groupe</em>)</p>
				<input type='radio' name='reponse_proposition[".$tab[$loop]['id']."]' id='reponse_proposition_".$tab[$loop]['id']."_oui' value='oui' onchange=\"checkbox_change('reponse_proposition_".$tab[$loop]['id']."_oui');checkbox_change('reponse_proposition_".$tab[$loop]['id']."_non');checkbox_change('reponse_proposition_".$tab[$loop]['id']."_vide')\"$checked_oui /><label for='reponse_proposition_".$tab[$loop]['id']."_oui' id='texte_reponse_proposition_".$tab[$loop]['id']."_oui'$style_oui> Proposition acceptée</label><br />
				<input type='radio' name='reponse_proposition[".$tab[$loop]['id']."]' id='reponse_proposition_".$tab[$loop]['id']."_non' value='non' onchange=\"checkbox_change('reponse_proposition_".$tab[$loop]['id']."_oui');checkbox_change('reponse_proposition_".$tab[$loop]['id']."_non');checkbox_change('reponse_proposition_".$tab[$loop]['id']."_vide')\"$checked_non /><label for='reponse_proposition_".$tab[$loop]['id']."_non' id='texte_reponse_proposition_".$tab[$loop]['id']."_non'$style_non> Proposition rejetée</label><br />
				<input type='radio' name='reponse_proposition[".$tab[$loop]['id']."]' id='reponse_proposition_".$tab[$loop]['id']."_vide' value='' onchange=\"checkbox_change('reponse_proposition_".$tab[$loop]['id']."_oui');checkbox_change('reponse_proposition_".$tab[$loop]['id']."_non');checkbox_change('reponse_proposition_".$tab[$loop]['id']."_vide')\" /><label for='reponse_proposition_".$tab[$loop]['id']."_vide' id='texte_reponse_proposition_".$tab[$loop]['id']."_vide'> Pas de réponse</label><br />
				Commentaire&nbsp;: <textarea name='commentaire_proposition[".$tab[$loop]['id']."]' style='vertical-align:top;'>".$tab[$loop]['commentaire_prof']."</textarea>
			</li>";
				$temoin_saisie_possible++;
			}
			else {

				echo "
			<li>
				<p style='color:grey; margin-top:1em; margin-bottom:1em;'><strong>Le ".formate_date($tab[$loop]['date_debut_r'], "n", "complet")." en ".$tab_creneaux[$tab[$loop]['id_creneau']]['nom_creneau']." (<em>".$tab_creneaux[$tab[$loop]['id_creneau']]['debut_court']." à ".$tab_creneaux[$tab[$loop]['id_creneau']]['fin_court']."</em>)&nbsp;: $nom_classe</strong> (<em style='font-size:x-small;'>remplacement de $info_groupe</em>)&nbsp:<br />Remplacement attribué à ".$attribue_a.".</p>
			</li>";

			}
		}
		echo "
		</ul>

		".(($temoin_saisie_possible>0) ? "<input type='hidden' name='is_posted' value='2' /><p><input type='submit' value='Valider' /></p>" : "")."

		<p style='text-indent:-4em;margin-left:4em;margin-top:1em;'><em>NOTE&nbsp;:</em> Une fois que vous avez accepté une proposition, une validation de la part de l'Administration doit encore être faite.<br />
		La proposition de remplacer un professeur sur un créneau peut en effet avoir été soumise à plusieurs professeurs.<br />
		L'Administration choisira qui sera l'heureux élu;)</p>
	</fieldset>
</form>";
	}

	echo js_checkbox_change_style('checkbox_change', 'texte_', 'y', 0.5);

	// Remplacements validés
	$tab=get_tab_propositions_remplacements($_SESSION['login'], "futures_validees");
	if(count($tab)>0) {
		$nb_propositions_ou_remplacements+=count($tab);
		echo "
<h3>Remplacements (<em>à venir</em>) validés</h3>

	<div class='fieldset_opacite50' style='margin-left:3em;'>

		<p class='bold'>Le(s) remplacement(s) suivant(s) vous a(ont) été attribué(s)&nbsp;:</p>
		<ul>";
		for($loop=0;$loop<count($tab);$loop++) {
			/*
			echo "<pre>";
			print_r($tab[$loop]);
			echo "</pre>";
			*/

			$nom_classe=get_nom_classe($tab[$loop]['id_classe']);
			if(($tab[$loop]['id_groupe']!="")&&($tab[$loop]['id_groupe']!="0")) {
				$info_groupe=get_info_grp($tab[$loop]['id_groupe'], array('description', 'matieres', 'classes', 'profs'));
			}
			else {
				$info_groupe=get_info_aid($tab[$loop]['id_aid'], array('nom_general_complet', 'classes', 'profs'));
			}

			$info_salle="";
			if($tab[$loop]['salle']!="") {
				$info_salle="<br />Salle ".$tab[$loop]['salle'];
			}

			$commentaire_validation="";
			if($tab[$loop]['commentaire_validation']!="") {
				$commentaire_validation="<br />".$tab[$loop]['commentaire_validation'];
			}

			echo "
			<li>
				<p><strong>Le ".formate_date($tab[$loop]['date_debut_r'], "n", "complet")." en ".$tab_creneaux[$tab[$loop]['id_creneau']]['nom_creneau']." (<em>".$tab_creneaux[$tab[$loop]['id_creneau']]['debut']." à ".$tab_creneaux[$tab[$loop]['id_creneau']]['fin']."</em>)&nbsp;: $nom_classe</strong> (<em style='font-size:x-small;'>remplacement de $info_groupe</em>)".$info_salle.$commentaire_validation."</p>
			</li>";
		}
		echo "
		</ul>
	</div>";
	}

	// Remplacements validés/effectués dans le passé
	$tab=get_tab_propositions_remplacements($_SESSION['login'], "validees_passees");
	if(count($tab)>0) {
		$nb_propositions_ou_remplacements+=count($tab);
		echo "
<h3>Remplacements validés/effectués dans le passé</h3>

	<div class='fieldset_opacite50' style='margin-left:3em;'>

		<p class='bold'>Le(s) remplacement(s) suivant(s) vous a(ont) été attribué(s)&nbsp;:</p>
		<ul>";
		for($loop=0;$loop<count($tab);$loop++) {
			/*
			echo "<pre>";
			print_r($tab[$loop]);
			echo "</pre>";
			*/

			$nom_classe=get_nom_classe($tab[$loop]['id_classe']);
			if(($tab[$loop]['id_groupe']!="")&&($tab[$loop]['id_groupe']!="0")) {
				$info_groupe=get_info_grp($tab[$loop]['id_groupe'], array('description', 'matieres', 'classes', 'profs'));
			}
			else {
				$info_groupe=get_info_aid($tab[$loop]['id_aid'], array('nom_general_complet', 'classes', 'profs'));
			}

			$info_salle="";
			if($tab[$loop]['salle']!="") {
				$info_salle=" (<em>salle ".$tab[$loop]['salle']."</em>)";
			}

			$commentaire_validation="";
			if($tab[$loop]['commentaire_validation']!="") {
				$commentaire_validation="<br />".$tab[$loop]['commentaire_validation'];
			}

			echo "
			<li>
				<p><strong>Le ".formate_date($tab[$loop]['date_debut_r'], "n", "complet")." en ".$tab_creneaux[$tab[$loop]['id_creneau']]['nom_creneau']." (<em>".$tab_creneaux[$tab[$loop]['id_creneau']]['debut']." à ".$tab_creneaux[$tab[$loop]['id_creneau']]['fin']."</em>)&nbsp;: $nom_classe</strong>".$info_salle." (<em style='font-size:x-small;'>remplacement de $info_groupe</em>)".$commentaire_validation."</p>
			</li>";
		}
		echo "
		</ul>
	</div>";
	}

	if($nb_propositions_ou_remplacements==0) {
		echo "<p>Aucune proposition de remplacement ne vous a été faite.</p>";
	}

}
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
elseif($_SESSION['statut']=="eleve") {

	echo "<p style='color:red'>L'information des élèves n'est pas encore implémentée.</p>";

}
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
elseif($_SESSION['statut']=="responsable") {

	echo "<p style='color:red'>L'information des responsables n'est pas encore implémentée.</p>";

}

require("../lib/footer.inc.php");
?>
