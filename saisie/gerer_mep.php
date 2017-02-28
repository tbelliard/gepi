<?php
/*
 *
 * Copyright 2001-2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
//$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
//$resultat_session = resumeSession();
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/saisie/gerer_mep.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/saisie/gerer_mep.php',
administrateur='V',
professeur='V',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Gérer les éléments de programme',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(($_SESSION['statut']=='scolarite')&&(!getSettingAOui('ScolGererMEP'))) {
	header("Location: ../accueil.php?msg=Vous n êtes pas autorisé à gérer les éléments de programme");
	die();
}

include_once 'scripts/fonctions.php';

$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);

$msg="";

if(isset($_POST['is_posted'])) {
	check_token();

	$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : array();
	if($_SESSION['statut']=="professeur") {
		for($loop=0;$loop<count($suppr);$loop++) {
			$sql="SELECT * FROM matiere_element_programme mep WHERE id_user='".$_SESSION['login']."' AND id='".$suppr[$loop]."';";
			//echo "$sql<br />";
			$res_mep=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_mep)>0) {
				$lig_mep=mysqli_fetch_object($res_mep);
				$sql="SELECT DISTINCT id_prof FROM j_mep_prof WHERE idEP='".$lig_mep->id."';";
				$res_mep_prof=mysqli_query($mysqli, $sql);
				$nb_prof=mysqli_num_rows($res_mep_prof);

				if($nb_prof>1) {
					$msg.="Suppression impossible de l'élément n°".$suppr[$loop]." <em>(utilisé par d'autres)</em>.<br />";
				}
				else {
					$sql="DELETE FROM j_mep_eleve WHERE idEP='".$suppr[$loop]."';";
					$del=mysqli_query($mysqli, $sql);
					if(!$del) {
						$msg.="Erreur lors de la suppression de l'association élève pour l'élément n°".$suppr[$loop]."<br />";
					}
					else {
						$sql="DELETE FROM j_mep_prof WHERE idEP='".$suppr[$loop]."';";
						$del=mysqli_query($mysqli, $sql);
						if(!$del) {
							$msg.="Erreur lors de la suppression de l'association professeur pour l'élément n°".$suppr[$loop]."<br />";
						}
						else {
							$sql="DELETE FROM j_mep_groupe WHERE idEP='".$suppr[$loop]."';";
							$del=mysqli_query($mysqli, $sql);
							if(!$del) {
								$msg.="Erreur lors de la suppression de l'association groupe pour l'élément n°".$suppr[$loop]."<br />";
							}
							else {

								$sql="DELETE FROM j_mep_niveau WHERE idEP='".$suppr[$loop]."';";
								$del=mysqli_query($mysqli, $sql);
								if(!$del) {
									$msg.="Erreur lors de la suppression de l'association niveau/MEF pour l'élément n°".$suppr[$loop]."<br />";
								}
								else {
									$sql="DELETE FROM matiere_element_programme WHERE id='".$suppr[$loop]."';";
									$del=mysqli_query($mysqli, $sql);
									if(!$del) {
										$msg.="Erreur lors de la suppression de l'élément n°".$suppr[$loop]."<br />";
									}
								}
							}
						}
					}
				}
			}
			else {
				$msg.="Vous n'êtes pas l'auteur de l'élément n°".$suppr[$loop].".<br />";
			}
		}

		/*
		echo "<pre>";
		print_r($suppr);
		echo "</pre>";
		*/

		$element=isset($_POST['element']) ? $_POST['element'] : array();
		foreach($element as $idEP => $libelle) {
			if(!in_array($idEP, $suppr)) {
				$sql="SELECT * FROM matiere_element_programme mep WHERE id_user='".$_SESSION['login']."' AND id='".$idEP."';";
				//echo "$sql<br />";
				$res_mep=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_mep)>0) {
					$sql="UPDATE matiere_element_programme SET libelle='".mysqli_real_escape_string($mysqli, stripslashes($libelle))."' WHERE id='".$idEP."';";
					//echo "$sql<br />";
					$update=mysqli_query($mysqli, $sql);
					if(!$update) {
						$msg.="Erreur lors de la mise à jour de l'élément n°$idEP<br />";
					}
				}
				else {
					$msg.="Vous n'êtes pas l'auteur de l'élément n°".$idEP.".<br />";
				}
			}
		}
	}
	elseif(in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
		for($loop=0;$loop<count($suppr);$loop++) {
			$sql="SELECT * FROM matiere_element_programme mep WHERE id='".$suppr[$loop]."';";
			//echo "$sql<br />";
			$res_mep=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_mep)>0) {
				$sql="DELETE FROM j_mep_eleve WHERE idEP='".$suppr[$loop]."';";
				$del=mysqli_query($mysqli, $sql);
				if(!$del) {
					$msg.="Erreur lors de la suppression de l'association élève pour l'élément n°".$suppr[$loop]."<br />";
				}
				else {
					$sql="DELETE FROM j_mep_prof WHERE idEP='".$suppr[$loop]."';";
					$del=mysqli_query($mysqli, $sql);
					if(!$del) {
						$msg.="Erreur lors de la suppression de l'association professeur pour l'élément n°".$suppr[$loop]."<br />";
					}
					else {
						$sql="DELETE FROM j_mep_groupe WHERE idEP='".$suppr[$loop]."';";
						$del=mysqli_query($mysqli, $sql);
						if(!$del) {
							$msg.="Erreur lors de la suppression de l'association groupe pour l'élément n°".$suppr[$loop]."<br />";
						}
						else {

							$sql="DELETE FROM j_mep_niveau WHERE idEP='".$suppr[$loop]."';";
							$del=mysqli_query($mysqli, $sql);
							if(!$del) {
								$msg.="Erreur lors de la suppression de l'association niveau/MEF pour l'élément n°".$suppr[$loop]."<br />";
							}
							else {
								$sql="DELETE FROM matiere_element_programme WHERE id='".$suppr[$loop]."';";
								$del=mysqli_query($mysqli, $sql);
								if(!$del) {
									$msg.="Erreur lors de la suppression de l'élément n°".$suppr[$loop]."<br />";
								}
							}
						}
					}
				}
			}
			else {
				$msg.="L'élément n°".$suppr[$loop]." n'existe pas.<br />";
			}
		}

		/*
		echo "<pre>";
		print_r($suppr);
		echo "</pre>";
		*/

		$element=isset($_POST['element']) ? $_POST['element'] : array();
		foreach($element as $idEP => $libelle) {
			if(!in_array($idEP, $suppr)) {
				$sql="SELECT * FROM matiere_element_programme mep WHERE id='".$idEP."';";
				//echo "$sql<br />";
				$res_mep=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_mep)>0) {
					$sql="UPDATE matiere_element_programme SET libelle='".mysqli_real_escape_string($mysqli, stripslashes($libelle))."' WHERE id='".$idEP."';";
					//echo "$sql<br />";
					$update=mysqli_query($mysqli, $sql);
					if(!$update) {
						$msg.="Erreur lors de la mise à jour de l'élément n°$idEP<br />";
					}
				}
				else {
				$msg.="L'élément n°".$idEP." n'existe pas.<br />";
				}
			}
		}
	}

	if($msg=="") {
		$msg="Enregistrement effectué (".strftime("le %d/%m/%Y à %H:%M:%S").")";
	}
}

$themessage = 'Des modifications n ont pas été enregistrées. Voulez-vous vraiment quitter sans enregistrer ?';
//================================
if($_SESSION['statut']=="professeur") {
	$titre_page = "Gérer mes éléments de programme";
}
else {
	$titre_page = "Gérer les éléments de programme";
}
require_once("../lib/header.inc.php");
//================================

//echo "<p class='bold'><a href='../accueil.php' onclick=\"if(confirm_abandon (this, change, '$themessage')) {self.close();};return false;\">Refermer</a>";
echo "<p class='bold'><a href='../accueil.php' onclick=\"return confirm_abandon (this, change, '$themessage');\">Accueil</a>";
if(($_SESSION["statut"]=="professeur")&&(isset($id_groupe))) {
	echo " | <a href='saisie_appreciations.php?id_groupe=$id_groupe'>Retour saisie appréciations</a>";
}
echo "</p>\n";

if($_SESSION['statut']=="professeur") {
	$sql="SELECT DISTINCT mep.* FROM matiere_element_programme mep, j_mep_prof jmp WHERE mep.id=jmp.idEP AND jmp.id_prof='".$_SESSION['login']."' ORDER BY mep.libelle;";
	$res_mep=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_mep)>0) {
		while($lig_mep=mysqli_fetch_object($res_mep)) {
			$sql="SELECT DISTINCT id_prof FROM j_mep_prof jmp WHERE jmp.idEP='".$lig_mep->id."';";
			$test_mep=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test_mep)==1) {
				// On réattribue la propriété au seul prof associé.
				$sql="UPDATE matiere_element_programme SET id_user='".$_SESSION['login']."' WHERE id='".$lig_mep->id."';";
				$update=mysqli_query($mysqli, $sql);
			}
		}
	}

	$tab_mes_elements_en_propre=array();
	$sql="SELECT * FROM matiere_element_programme mep WHERE id_user='".$_SESSION['login']."';";
	//echo "$sql<br />";
	$res_mep=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_mep)>0) {
		echo "
<form action='".$_SERVER['PHP_SELF']."' method='post'/>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='is_posted' value='y' />
		".(isset($id_groupe) ? "<input type='hidden' name='id_groupe' value='$id_groupe' />" : "")."
		<p>Modifier/corriger mes éléments de programme&nbsp;:</p>
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th>Id</th>
					<th>Libellé</th>
					<th>Attribué</th>
					<th>Supprimer</th>
				</tr>
			</thead>
			<tbody>";
		while($lig_mep=mysqli_fetch_object($res_mep)) {

			$liste_profs="";
			$sql="SELECT DISTINCT id_prof FROM j_mep_prof WHERE idEP='".$lig_mep->id."';";
			$res_mep_prof=mysqli_query($mysqli, $sql);
			$nb_prof=mysqli_num_rows($res_mep_prof);
			if($nb_prof>0) {
				while($lig_mep_prof=mysqli_fetch_object($res_mep_prof)) {
					if($liste_profs!="") {
						$liste_profs.=", ";
					}
					$liste_profs.=civ_nom_prenom($lig_mep_prof->id_prof);
				}
			}

			$suppr="";
			if($nb_prof==1) {
				$suppr="<input type='checkbox' name='suppr[]' value='".$lig_mep->id."' onchange=\"changement()\" />";
			}

			$sql="SELECT DISTINCT idEleve FROM j_mep_eleve WHERE idEP='".$lig_mep->id."';";
			$res_mep_ele=mysqli_query($mysqli, $sql);
			$nb_ele=mysqli_num_rows($res_mep_ele);

			echo "
				<tr>
					<td>
						".$lig_mep->id."
					</td>
					<td style='text-align:left;'>
						<input type='text' name='element[".$lig_mep->id."]' value=\"".$lig_mep->libelle."\" onchange=\"changement()\" size='50' />
					</td>
					<td>
						<span title=\"$liste_profs\">$nb_prof prof(s)</span> et $nb_ele élève(s)
					</td>
					<td>$suppr</td>
				</tr>";
			$tab_mes_elements_en_propre[]=$lig_mep->id;
		}
		echo "
			</tbody>
		</table>
		<p><input type='submit' value='Valider' /></p>
		<p style='margin-top:1em;'><em>NOTES&nbsp;:</em></p>
		<ul>
			<li><p>Si vous modifiez des libellés pour des éléments utilisés par d'autres professeurs, vous affecterez leurs saisies.<br />Ces éléments sont partagés.</p></li>
			<li><p>Des éléments ont pu (ou pourront) être attribués pour d'autres années scolaires.<br />
			Il ne serait pas judicieux de supprimer des éléments de programmes en affectant les saisies d'autres années.</p></li>
		</ul>
	</fieldset>
</form>";
	}
	else {
		echo "<p style='color:red'>Vous n'avez défini en propre aucun élément de programme.<br />Contactez l'auteur des éléments que vous avez utilisés pour une éventuelle modification de libellé.</p>";
	}

	$sql="SELECT DISTINCT mep.* FROM matiere_element_programme mep, j_mep_prof jmp WHERE mep.id=jmp.idEP AND jmp.id_prof='".$_SESSION['login']."' ORDER BY mep.libelle;";
	$res_mep=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_mep)>0) {
		$lignes_tableau="";
		while($lig_mep=mysqli_fetch_object($res_mep)) {
			if(!in_array($lig_mep->id, $tab_mes_elements_en_propre)) {
				$infos_auteur="";
				if($lig_mep->id_user=="") {
					$infos_auteur="<span title=\"Cet élément de programme a dû être créé avant la mise en place de la mémorisation de l'auteur.\nSeul un administrateur peut maintenant corriger ou ré-attribuer la propriété de cet élément.\">Aucun</span>";
					$email=getSettingValue('gepiAdminAdress');
					if(($email!="")&&(check_mail($email))) {
						$infos_auteur.="<a href=\"mailto:$email?".urlencode("subject=".getSettingValue('gepiPrefixeSujetMail')."[GEPI] Élément de programme ".$lig_mep->libelle)."' title=\"Envoyer un mail\" target='_blank'><img src='../images/icons/mail.png' class='icone16' alt='Mail' /></a>";
					}
				}
				else {
					$infos_auteur=$lig_mep->id_user;
					$tab_user=get_info_user($lig_mep->id_user);
					if(isset($tab_user['civ_denomination'])) {
						$infos_auteur=$tab_user['civ_denomination'];
					}
					$email=$tab_user['email'];
					if(($email!="")&&(check_mail($email))) {
						$infos_auteur.="<a href=\"mailto:$email?".urlencode("subject=".getSettingValue('gepiPrefixeSujetMail')."[GEPI] Élément de programme ".$lig_mep->libelle)."'\" title=\"Envoyer un mail\" target='_blank'><img src='../images/icons/mail.png' class='icone16' alt='Mail' /></a>";
					}
				}

				$lignes_tableau.="
		<tr>
			<td style='text-align:left;'>".$lig_mep->libelle."</td>
			<td>".$infos_auteur."</td>
		</tr>";
			}
		}

		if($lignes_tableau!="") {
			echo "<p>Voici la liste des éléments de programme que vous avez utilisé et qui ont été créés par d'autres utilisateurs.</p>
<table class='boireaus boireaus_alt'>
	<thead>
		<tr>
			<th>Libellé</th>
			<th>Auteur</th>
		</tr>
	</thead>
	<tbody>".$lignes_tableau."
	</tbody>
</table>";
		}
	}

}
else {
	//scol ou admin

	$sql="SELECT * FROM matiere_element_programme mep ORDER BY libelle;";
	//echo "$sql<br />";
	$res_mep=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_mep)>0) {
		echo "
<form action='".$_SERVER['PHP_SELF']."' method='post'/>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='is_posted' value='y' />
		".(isset($id_groupe) ? "<input type='hidden' name='id_groupe' value='$id_groupe' />" : "")."
		<p>Modifier/corriger les éléments de programme&nbsp;:</p>
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th>Id</th>
					<th>Libellé</th>
					<th>Auteur</th>
					<th>Utilisé par</th>
					<th>Nombre d'élèves</th>
					<th>Supprimer</th>
				</tr>
			</thead>
			<tbody>";
		while($lig_mep=mysqli_fetch_object($res_mep)) {

			$liste_profs="";
			$sql="SELECT DISTINCT id_prof FROM j_mep_prof WHERE idEP='".$lig_mep->id."';";
			$res_mep_prof=mysqli_query($mysqli, $sql);
			$nb_prof=mysqli_num_rows($res_mep_prof);
			if($nb_prof>0) {
				while($lig_mep_prof=mysqli_fetch_object($res_mep_prof)) {
					if($nb_prof==1) {
						// On réattribue la propriété au seul prof associé.
						$sql="UPDATE matiere_element_programme SET id_user='".$lig_mep_prof->id_prof."' WHERE id='".$lig_mep->id."';";
						$update=mysqli_query($mysqli, $sql);
					}

					if($liste_profs!="") {
						$liste_profs.=",<br />";
					}
					$liste_profs.=civ_nom_prenom($lig_mep_prof->id_prof);
				}
			}

			$suppr="<input type='checkbox' name='suppr[]' value='".$lig_mep->id."' onchange=\"changement()\" />";

			$sql="SELECT DISTINCT idEleve FROM j_mep_eleve WHERE idEP='".$lig_mep->id."';";
			$res_mep_ele=mysqli_query($mysqli, $sql);
			$nb_ele=mysqli_num_rows($res_mep_ele);

			echo "
				<tr>
					<td>
						".$lig_mep->id."
					</td>
					<td style='text-align:left;'>
						<input type='text' name='element[".$lig_mep->id."]' value=\"".$lig_mep->libelle."\" onchange=\"changement()\" size='50' />
					</td>
					<td>
						".civ_nom_prenom($lig_mep->id_user)."
					</td>
					<td>
						$liste_profs
					</td>
					<td>
						$nb_ele
					</td>
					<td><input type='checkbox' name='suppr[]' value='".$lig_mep->id."' onchange=\"changement()\" /></td>
				</tr>";
			$tab_mes_elements_en_propre[]=$lig_mep->id;
		}
		echo "
			</tbody>
		</table>
		<p><input type='submit' value='Valider' /></p>
		<p style='margin-top:1em;'><em>NOTES&nbsp;:</em></p>
		<ul>
			<li><p>Si vous modifiez des libellés pour des éléments utilisés par d'autres professeurs, vous affecterez leurs saisies.<br />Ces éléments sont partagés.</p></li>
			<li><p>Des éléments ont pu (ou pourront) être attribués pour d'autres années scolaires.<br />
			Il ne serait pas judicieux de supprimer des éléments de programmes en affectant les saisies d'autres années.</p></li>
		</ul>
	</fieldset>
</form>";
	}
	else {
		echo "<p style='color:red'>Aucun élément de programme n'est encore défini.</p>";
	}

}

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
