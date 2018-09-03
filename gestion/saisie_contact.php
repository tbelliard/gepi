<?php

/*
 *
 * Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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
//require_once("../lib/initialisationsPropel.inc.php");
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

$sql="SELECT * FROM droits WHERE id='/gestion/saisie_contact.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/gestion/saisie_contact.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='V',
autre='F',
description='Saisie contact téléphonique, mail',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

/*
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}
*/

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : "");
$pers_id=isset($_POST['pers_id']) ? $_POST['pers_id'] : (isset($_GET['pers_id']) ? $_GET['pers_id'] : "");
$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : (isset($_GET['login_ele']) ? $_GET['login_ele'] : "");
$target=isset($_POST['target']) ? $_POST['target'] : (isset($_GET['target']) ? $_GET['target'] : "");

if($mode=="js") {
	header('Content-Type: text/html; charset=utf-8');

	if(isset($_POST["valide_correction_telephone"])) {
		check_token();
		if($pers_id!="") {
			if(!acces_saisie_telephone("responsable")) {
				echo "<p style='color:red'>Vous n'avez pas accès à la saisie des numéros de téléphone des responsables.</p>";
			}
			else {
				// Récupérer les infos
				$tab=get_info_responsable("", $pers_id);
				if(isset($tab["nom"])) {
					$sql_modif="";
					if((isset($_POST["tel_pers"]))&&($_POST["tel_pers"]!=$tab["tel_pers"])) {
						$sql_modif.=", tel_pers='".$_POST["tel_pers"]."'";
					}
					if((isset($_POST["tel_port"]))&&($_POST["tel_port"]!=$tab["tel_port"])) {
						$sql_modif.=", tel_port='".$_POST["tel_port"]."'";
					}
					if((isset($_POST["tel_prof"]))&&($_POST["tel_prof"]!=$tab["tel_prof"])) {
						$sql_modif.=", tel_prof='".$_POST["tel_prof"]."'";
					}
					if((isset($_POST["mel"]))&&($_POST["mel"]!=$tab["mel"])) {
						$sql_modif.=", mel='".$_POST["mel"]."'";
					}
					if($sql_modif=="") {
						echo "<p style='color:red'>Aucune modification demandée pour le responsable n°$pers_id.</p>";
					}
					else {
						$sql="UPDATE resp_pers SET ".substr($sql_modif,1)." WHERE pers_id='$pers_id';";
						//echo "$sql<br />";
						$update=mysqli_query($mysqli, $sql);
						if($update) {
							echo "<span style='color:green' title=\"Il peut être nécessaire de re-charger/rafraichir la page à l'origine de l'affichage du formulaire, pour constater la modification.\">Modification effectuée.</span>";
						}
						else {
							echo "<span style='color:red'>Erreur lors de la modification.</span>";
						}
					}
				}
				else {
					echo "<p style='color:red'>Le responsable n°$pers_id n'a pas été trouvé.</p>";
				}
			}
		}
		elseif($login_ele!="") {
			if(!acces_saisie_telephone("eleve")) {
				echo "<p style='color:red'>Vous n'avez pas accès à la saisie des numéros de téléphone des responsables.</p>";
			}
			else {
				// Récupérer les infos
				$tab=get_info_eleve($login_ele);
				if(isset($tab["nom"])) {
					$sql_modif="";
					if((isset($_POST["tel_pers"]))&&($_POST["tel_pers"]!=$tab["tel_pers"])) {
						$sql_modif.=", tel_pers='".$_POST["tel_pers"]."'";
					}
					if((isset($_POST["tel_port"]))&&($_POST["tel_port"]!=$tab["tel_port"])) {
						$sql_modif.=", tel_port='".$_POST["tel_port"]."'";
					}
					if((isset($_POST["tel_prof"]))&&($_POST["tel_prof"]!=$tab["tel_prof"])) {
						$sql_modif.=", tel_prof='".$_POST["tel_prof"]."'";
					}
					if((isset($_POST["email"]))&&($_POST["email"]!=$tab["email"])) {
						$sql_modif.=", email='".$_POST["email"]."'";
					}
					if($sql_modif=="") {
						echo "<p style='color:red'>Aucune modification demandée pour l'élève ".$tab["nom"]." ".$tab["prenom"].".</p>";
					}
					else {
						$sql="UPDATE eleves SET ".substr($sql_modif,1)." WHERE login='$login_ele';";
						//echo "$sql<br />";
						$update=mysqli_query($mysqli, $sql);
						if($update) {
							echo "<span style='color:green' title=\"Il peut être nécessaire de re-charger/rafraichir la page à l'origine de l'affichage du formulaire, pour constater la modification.\">Modification effectuée.</span>";
						}
						else {
							echo "<span style='color:red'>Erreur lors de la modification.</span>";
						}
					}
				}
				else {
					echo "<p style='color:red'>L'élève $login_ele n'a pas été trouvé.</p>";
				}
			}
		}
		else {
			echo "<p style='color:red'>Responsable ou élève non choisi.</p>";
			die();
		}
	}

	// Affichage du formulaire
	if(($pers_id!="")&&(preg_match("/^[0-9]{1,}$/", $pers_id))) {
		if(!acces_saisie_telephone("responsable")) {
			echo "<p style='color:red'>Vous n'avez pas accès à la saisie des numéros de téléphone des responsables.</p>";
		}
		else {
			// Récupérer les infos
			$tab=get_info_responsable("", $pers_id);
			if(isset($tab["nom"])) {
				echo "<form action='".$_SERVER["PHP_SELF"]."' method=\"post\" target=\"_blank\">
	<fieldset class='fieldset_opacite50'>
		".add_token_field(true)."
		<input type='hidden' name='mode' value='js' />
		<input type='hidden' name='valide_correction_telephone' value='y' />
		<input type='hidden' name='pers_id' id='pers_id' value='$pers_id' />
		<strong>".$tab["civilite"]." ".$tab["nom"]." ".$tab["prenom"]."</strong>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th title=\"Téléphone Personnel\">Tel.pers</th>
				<td style='text-align:left;'>
					<input type='text' name='tel_pers' id='tel_pers' value=\"".$tab["tel_pers"]."\" />".((preg_match("/^\+/", $tab["tel_pers"])) ? "soit ".affiche_numero_tel_sous_forme_classique($tab["tel_pers"]) : "")."
				</td>
			</tr>
			<tr title=\"Téléphone Portable\">
				<th>Tel.port</th>
				<td style='text-align:left;'>
					<input type='text' name='tel_port' id='tel_port' value=\"".$tab["tel_port"]."\" />".((preg_match("/^\+/", $tab["tel_port"])) ? "soit ".affiche_numero_tel_sous_forme_classique($tab["tel_port"]) : "")."
				</td>
			</tr>
			<tr title=\"Téléphone Professionnel\">
				<th>Tel.prof</th>
				<td style='text-align:left;'>
					<input type='text' name='tel_prof' id='tel_prof' value=\"".$tab["tel_prof"]."\" />".((preg_match("/^\+/", $tab["tel_prof"])) ? "soit ".affiche_numero_tel_sous_forme_classique($tab["tel_prof"]) : "")."
				</td>
			</tr>
			<tr>
				<th>Email</th>
				<td style='text-align:left'>
					<input type='text' name='mel' id='mel' value=\"".$tab["mel"]."\" />
				</td>
			</tr>
		</table>
		<!--
		<p><input type='submit' value='Enregistrer' /></p>
		-->
		<p><input type='button' value='Enregistrer' onclick=\"valider_correction_tel()\"/></p>
	</fieldset>
</form>";
			}
			else {
				echo "<p style='color:red'>Le responsable n°$pers_id n'a pas été trouvé.</p>";
			}
		}
	}
	elseif($login_ele!="") {
		if(!acces_saisie_telephone("eleve")) {
			echo "<p style='color:red'>Vous n'avez pas accès à la saisie des numéros de téléphone des élèves.</p>";
		}
		else {
			// Récupérer les infos
			$tab=get_info_eleve($login_ele);
			if(isset($tab["nom"])) {
				echo "<form action='".$_SERVER["PHP_SELF"]."' method=\"post\" target=\"_blank\">
	<fieldset class='fieldset_opacite50'>
		".add_token_field(true)."
		<input type='hidden' name='mode' value='js' />
		<input type='hidden' name='valide_correction_telephone' value='y' />
		<input type='hidden' name='login_ele' id='login_ele' value=\"".$login_ele."\" />
		<strong>".$tab["nom"]." ".$tab["prenom"]." (".$tab["classes"].")</strong>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th title=\"Téléphone Personnel\">Tel.pers</th>
				<td style='text-align:left;'>
					<input type='text' name='tel_pers' id='tel_pers' value=\"".$tab["tel_pers"]."\" />".((preg_match("/^\+/", $tab["tel_pers"])) ? "soit ".affiche_numero_tel_sous_forme_classique($tab["tel_pers"]) : "")."
				</td>
			</tr>
			<tr title=\"Téléphone Portable\">
				<th>Tel.port</th>
				<td style='text-align:left;'>
					<input type='text' name='tel_port' id='tel_port' value=\"".$tab["tel_port"]."\" />".((preg_match("/^\+/", $tab["tel_port"])) ? "soit ".affiche_numero_tel_sous_forme_classique($tab["tel_port"]) : "")."
				</td>
			</tr>
			<tr title=\"Téléphone Professionnel\">
				<th>Tel.prof</th>
				<td style='text-align:left;'>
					<input type='text' name='tel_prof' id='tel_prof' value=\"".$tab["tel_prof"]."\" />".((preg_match("/^\+/", $tab["tel_prof"])) ? "soit ".affiche_numero_tel_sous_forme_classique($tab["tel_prof"]) : "")."
				</td>
			</tr>
			<tr>
				<th>Email</th>
				<td style='text-align:left'>
					<input type='text' name='email' id='email' value=\"".$tab["email"]."\" />
				</td>
			</tr>
		</table>
		<!--
		<p><input type='submit' value='Enregistrer' /></p>
		-->
		<p><input type='button' value='Enregistrer' onclick=\"valider_correction_tel()\"/></p>
	</fieldset>
</form>";
			}
			else {
				echo "<p style='color:red'>L'élève $login_ele n'a pas été trouvé.</p>";
			}
		}

	}
	else {
		echo "<p style='color:red'>Responsable ou élève non choisi.</p>";
	}

	die();
}

$msg="";
// Validation du formulaire sans JS
if(isset($_POST["valide_correction_telephone"])) {
	check_token();
	if($pers_id!="") {
		if(!acces_saisie_telephone("responsable")) {
			$msg.="<span style='color:red'>Vous n'avez pas accès à la saisie des numéros de téléphone des responsables.</span><br />";
		}
		else {
			// Récupérer les infos
			$tab=get_info_responsable("", $pers_id);
			if(isset($tab["nom"])) {
				$sql_modif="";
				if((isset($_POST["tel_pers"]))&&($_POST["tel_pers"]!=$tab["tel_pers"])) {
					$sql_modif.=", tel_pers='".$_POST["tel_pers"]."'";
				}
				if((isset($_POST["tel_port"]))&&($_POST["tel_port"]!=$tab["tel_port"])) {
					$sql_modif.=", tel_port='".$_POST["tel_port"]."'";
				}
				if((isset($_POST["tel_prof"]))&&($_POST["tel_prof"]!=$tab["tel_prof"])) {
					$sql_modif.=", tel_prof='".$_POST["tel_prof"]."'";
				}
				if((isset($_POST["mel"]))&&($_POST["mel"]!=$tab["mel"])) {
					$sql_modif.=", mel='".$_POST["mel"]."'";
				}
				if($sql_modif=="") {
					$msg.="<span style='color:red'>Aucune modification demandée pour le responsable n°$pers_id.</span><br />";
				}
				else {
					$sql="UPDATE resp_pers SET ".substr($sql_modif,1)." WHERE pers_id='$pers_id';";
					//echo "$sql<br />";
					$update=mysqli_query($mysqli, $sql);
					if($update) {
						$msg.="<span style='color:green' title=\"Il peut être nécessaire de re-charger/rafraichir la page à l'origine de l'affichage du formulaire, pour constater la modification.\">Modification effectuée.</span><br />";
					}
					else {
						$msg.="<span style='color:red'>Erreur lors de la modification.</span><br />";
					}
				}
			}
			else {
				$msg.="<span style='color:red'>Le responsable n°$pers_id n'a pas été trouvé.</span><br />";
			}
		}
	}
	elseif($login_ele!="") {
		if(!acces_saisie_telephone("eleve")) {
			$msg.="<span style='color:red'>Vous n'avez pas accès à la saisie des numéros de téléphone des responsables.</span><br />";
		}
		else {
			// Récupérer les infos
			$tab=get_info_eleve($login_ele);
			if(isset($tab["nom"])) {
				$sql_modif="";
				if((isset($_POST["tel_pers"]))&&($_POST["tel_pers"]!=$tab["tel_pers"])) {
					$sql_modif.=", tel_pers='".$_POST["tel_pers"]."'";
				}
				if((isset($_POST["tel_port"]))&&($_POST["tel_port"]!=$tab["tel_port"])) {
					$sql_modif.=", tel_port='".$_POST["tel_port"]."'";
				}
				if((isset($_POST["tel_prof"]))&&($_POST["tel_prof"]!=$tab["tel_prof"])) {
					$sql_modif.=", tel_prof='".$_POST["tel_prof"]."'";
				}
				if((isset($_POST["email"]))&&($_POST["email"]!=$tab["email"])) {
					$sql_modif.=", email='".$_POST["email"]."'";
				}
				if($sql_modif=="") {
					$msg.="<span style='color:red'>Aucune modification demandée pour l'élève ".$tab["nom"]." ".$tab["prenom"].".</span><br />";
				}
				else {
					$sql="UPDATE eleves SET ".substr($sql_modif,1)." WHERE login='$login_ele';";
					//echo "$sql<br />";
					$update=mysqli_query($mysqli, $sql);
					if($update) {
						$msg.="<span style='color:green' title=\"Il peut être nécessaire de re-charger/rafraichir la page à l'origine de l'affichage du formulaire, pour constater la modification.\">Modification effectuée.</span><br />";
					}
					else {
						$msg.="<span style='color:red'>Erreur lors de la modification.</span><br />";
					}
				}
			}
			else {
				$msg.="<span style='color:red'>L'élève $login_ele n'a pas été trouvé.</span><br />";
			}
		}
	}
	else {
		$msg.="<span style='color:red'>Responsable ou élève non choisi.</span><br />";
	}
}

// Sans JavaScript, page classique avec header.inc.php,...

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';

//**************** EN-TETE **************************************
$titre_page = "Saisie contact";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

echo "<p class='bold'>
		<a href='../accueil.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>
</p>

<h2>Saisie contact téléphonique et mail</h2>";

	// Affichage du formulaire
	if(($pers_id!="")&&(preg_match("/^[0-9]{1,}$/", $pers_id))) {
		if(!acces_saisie_telephone("responsable")) {
			echo "<p style='color:red'>Vous n'avez pas accès à la saisie des numéros de téléphone des responsables.</p>";
		}
		else {
			// Récupérer les infos
			$tab=get_info_responsable("", $pers_id);
			if(isset($tab["nom"])) {
				echo "<form action='".$_SERVER["PHP_SELF"]."' method=\"post\">
	<fieldset class='fieldset_opacite50'>
		".add_token_field(true)."
		<!--
		<input type='hidden' name='mode' value='js' />
		-->
		<input type='hidden' name='valide_correction_telephone' value='y' />
		<input type='hidden' name='pers_id' id='pers_id' value='$pers_id' />
		<strong>".$tab["civilite"]." ".$tab["nom"]." ".$tab["prenom"]."</strong>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th title=\"Téléphone Personnel\">Tel.pers</th>
				<td style='text-align:left;'>
					<input type='text' name='tel_pers' id='tel_pers' value=\"".$tab["tel_pers"]."\" />".((preg_match("/^\+/", $tab["tel_pers"])) ? "soit ".affiche_numero_tel_sous_forme_classique($tab["tel_pers"]) : "")."
				</td>
			</tr>
			<tr title=\"Téléphone Portable\">
				<th>Tel.port</th>
				<td style='text-align:left;'>
					<input type='text' name='tel_port' id='tel_port' value=\"".$tab["tel_port"]."\" />".((preg_match("/^\+/", $tab["tel_port"])) ? "soit ".affiche_numero_tel_sous_forme_classique($tab["tel_port"]) : "")."
				</td>
			</tr>
			<tr title=\"Téléphone Professionnel\">
				<th>Tel.prof</th>
				<td style='text-align:left;'>
					<input type='text' name='tel_prof' id='tel_prof' value=\"".$tab["tel_prof"]."\" />".((preg_match("/^\+/", $tab["tel_prof"])) ? "soit ".affiche_numero_tel_sous_forme_classique($tab["tel_prof"]) : "")."
				</td>
			</tr>
			<tr>
				<th>Email</th>
				<td style='text-align:left'>
					<input type='text' name='mel' id='mel' value=\"".$tab["mel"]."\" />
				</td>
			</tr>
		</table>
		<p><input type='submit' value='Enregistrer' /></p>
		<!--
		<p><input type='button' value='Enregistrer' onclick=\"valider_correction_tel()\"/></p>
		-->
	</fieldset>
</form>";
			}
			else {
				echo "<p style='color:red'>Le responsable n°$pers_id n'a pas été trouvé.</p>";
			}
		}
	}
	elseif($login_ele!="") {
		if(!acces_saisie_telephone("eleve")) {
			echo "<p style='color:red'>Vous n'avez pas accès à la saisie des numéros de téléphone des élèves.</p>";
		}
		else {
			// Récupérer les infos
			$tab=get_info_eleve($login_ele);
			if(isset($tab["nom"])) {
				echo "<form action='".$_SERVER["PHP_SELF"]."' method=\"post\">
	<fieldset class='fieldset_opacite50'>
		".add_token_field(true)."
		<!--
		<input type='hidden' name='mode' value='js' />
		-->
		<input type='hidden' name='valide_correction_telephone' value='y' />
		<input type='hidden' name='login_ele' id='login_ele' value=\"".$login_ele."\" />
		<strong>".$tab["nom"]." ".$tab["prenom"]." (".$tab["classes"].")</strong>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th title=\"Téléphone Personnel\">Tel.pers</th>
				<td style='text-align:left;'>
					<input type='text' name='tel_pers' id='tel_pers' value=\"".$tab["tel_pers"]."\" />".((preg_match("/^\+/", $tab["tel_pers"])) ? "soit ".affiche_numero_tel_sous_forme_classique($tab["tel_pers"]) : "")."
				</td>
			</tr>
			<tr title=\"Téléphone Portable\">
				<th>Tel.port</th>
				<td style='text-align:left;'>
					<input type='text' name='tel_port' id='tel_port' value=\"".$tab["tel_port"]."\" />".((preg_match("/^\+/", $tab["tel_port"])) ? "soit ".affiche_numero_tel_sous_forme_classique($tab["tel_port"]) : "")."
				</td>
			</tr>
			<tr title=\"Téléphone Professionnel\">
				<th>Tel.prof</th>
				<td style='text-align:left;'>
					<input type='text' name='tel_prof' id='tel_prof' value=\"".$tab["tel_prof"]."\" />".((preg_match("/^\+/", $tab["tel_prof"])) ? "soit ".affiche_numero_tel_sous_forme_classique($tab["tel_prof"]) : "")."
				</td>
			</tr>
			<tr>
				<th>Email</th>
				<td style='text-align:left'>
					<input type='text' name='email' id='email' value=\"".$tab["email"]."\" />
				</td>
			</tr>
		</table>
		<p><input type='submit' value='Enregistrer' /></p>
		<!--
		<p><input type='button' value='Enregistrer' onclick=\"valider_correction_tel()\"/></p>
		-->
	</fieldset>
</form>";
			}
			else {
				echo "<p style='color:red'>L'élève $login_ele n'a pas été trouvé.</p>";
			}
		}

	}
	else {
		echo "<p style='color:red'>Responsable ou élève non choisi.</p>";
	}



?>
