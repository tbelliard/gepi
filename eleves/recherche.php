<?php
/*
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$javascript_specifique[] = "lib/tablekit";
$dojo=true;
$utilisation_tablekit="ok";

//**************** EN-TETE *****************
$titre_page = "Recherche";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE *****************

//debug_var();

if(isset($_POST['is_posted_recherche'])) {
	echo "<p><a href='".$_SERVER['PHP_SELF']."'>Effectuer une autre recherche</a></p>";

	$rech_nom=isset($_POST['rech_nom']) ? $_POST['rech_nom'] : "";
	$rech_prenom=isset($_POST['rech_prenom']) ? $_POST['rech_prenom'] : "";
	if($rech_nom=="") {
		unset($_SESSION['rech_nom']);
	}
	else {
		$_SESSION['rech_nom']=$rech_nom;
	}

	if($rech_prenom=="") {
		unset($_SESSION['rech_prenom']);
	}
	else {
		$_SESSION['rech_prenom']=$rech_prenom;
	}

	$statut=isset($_POST['statut']) ? $_POST['statut'] : array();
	if(count($statut)>0) {
		$acces_visu_eleve=acces("/eleves/visu_eleve.php", $_SESSION['statut']);

		if($_SESSION['statut']=='professeur') {
			if(!getSettingAOui('GepiAccesGestElevesProf')) {
				$acces_visu_eleve="n";
			}
		}

		if(in_array("eleve", $statut)) {
			$_SESSION['rech_statut_eleve']="y";

			$acces_modify_eleve=acces("/eleves/modify_eleve.php", $_SESSION['statut']);
			$acces_class_const=acces("/classes/classes_const.php", $_SESSION['statut']);

			$sql="SELECT * FROM eleves WHERE nom LIKE '%$rech_nom%' AND prenom LIKE '%$rech_prenom%' ORDER BY nom, prenom;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				echo "<p style='color:red'>Aucun élève trouvé.</p>\n";
			}
			else {
				// A FAIRE : Ajouter des tests sur les droits d'accès sur les liens

				echo "<p class='bold' style='margin-top:1em;'>Élèves trouvés&nbsp;:</p>
<table class='sortable resizable boireaus boireaus_alt'>
	<tr>
		<th class='text' title=\"Trier sur le login\">Login</th>
		<th class='text' title=\"Trier sur l'état actif ou non du compte\">Compte</th>
		<th class='text' title=\"Trier sur le nom prénom\">Nom prénom</th>
		<th class='text' title=\"Trier sur la classe\">Classe</th>
	</tr>";
				while($lig=mysql_fetch_object($res)) {
					echo "
	<tr>";

					$restriction_acces="n";
					if(($_SESSION['statut']=='professeur')&&
					((!getSettingAOui('GepiAccesGestElevesProf'))||(!is_prof_ele($_SESSION['login'], $lig->login)))) {
						if((getSettingAOui('GepiAccesGestElevesProfP'))&&(is_pp($_SESSION['login'], "", $lig->login))) {
							$restriction_acces="n";
						}
						else {
							$restriction_acces="y";
						}
					}

					if(($acces_modify_eleve)&&($restriction_acces=="n")) {
						echo "
		<td><a href='$gepiPath/eleves/modify_eleve.php?eleve_login=$lig->login' title=\"Modifier les informations élève\">$lig->login</a></td>";
					}
					else {
						echo "
		<td>$lig->login</td>";
					}

					echo "
		<td>";
					if($lig->login!="") {
						if($_SESSION['statut']=='administrateur') {
							echo lien_image_compte_utilisateur($lig->login, "", "", "y");
						}
						else {
							echo lien_image_compte_utilisateur($lig->login, "", "", "n");
						}
					}
					echo "</td>";

					if($acces_visu_eleve) {
						echo "
		<td><a href='$gepiPath/eleves/visu_eleve.php?ele_login=$lig->login' title=\"Consulter la fiche élève\">".casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, "majf2")."</a></td>";
					}
					else {
						echo "
		<td>".casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, "majf2")."</td>";
					}

					echo "
		<td>";
					$sql="SELECT DISTINCT id, classe FROM classes c, j_eleves_classes jec WHERE jec.login='$lig->login' AND jec.id_classe=c.id ORDER BY periode;";
					$res_classe=mysql_query($sql);
					if(mysql_num_rows($res_classe)>0) {
						$cpt_classe=0;
						while($lig_classe=mysql_fetch_object($res_classe)) {
							if($cpt_classe>0) {echo ", ";}
							if($acces_class_const) {
								echo "<a href='$gepiPath/classes/classes_const.php?id_classe=$lig_classe->id' title=\"Accéder à la liste des élèves de la classe.\">$lig_classe->classe</a>";
							}
							else {
								echo "$lig_classe->classe";
							}
							$cpt_classe++;
						}
					}
					echo "</td>
	</tr>";
				}
				echo "
</table>";
			}
		}
		else {
			$_SESSION['rech_statut_eleve']="n";
		}

		if(in_array("responsable", $statut)) {
			$_SESSION['rech_statut_responsable']="y";

			$acces_modify_resp=acces("/responsables/modify_resp.php", $_SESSION['statut']);

			$sql="SELECT * FROM resp_pers WHERE nom LIKE '%$rech_nom%' AND prenom LIKE '%$rech_prenom%' ORDER BY nom, prenom;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				echo "<p style='color:red'>Aucun responsable trouvé.</p>\n";
			}
			else {
				// A FAIRE : Ajouter des tests sur les droits d'accès sur les liens

				echo "<p class='bold' style='margin-top:1em;'>Responsables trouvés&nbsp;:</p>
<table class='sortable resizable boireaus boireaus_alt'>
	<tr>
		<th class='text' title=\"Trier sur le numéro PERSONNE_ID du responsable\">Identifiant</th>
		<th class='text' title=\"Trier sur le nom prénom\">Nom prénom</th>
		<th class='text'>Compte</th>
		<th class='text' title=\"Trier sur le prénom nom de l'élève\">Responsable de</th>
	</tr>";
				while($lig=mysql_fetch_object($res)) {
					echo "
	<tr>";
					if($acces_modify_resp) {
						echo "
		<td><a href='$gepiPath/responsables/modify_resp.php?pers_id=$lig->pers_id' title=\"Modifier les informations responsable\">$lig->pers_id</a></td>";
					}
					else {
						echo "
		<td>$lig->pers_id</td>";
					}
					echo "
		<td>".casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, "majf2")."</td>
		<td>";

					if($lig->login!="") {
						if($_SESSION['statut']=='administrateur') {
							echo lien_image_compte_utilisateur($lig->login, "", "", "y");
						}
						else {
							echo lien_image_compte_utilisateur($lig->login, "", "", "n");
						}
					}

					echo "</td>
		<td>";
					$tab_enfants=get_enfants_from_pers_id($lig->pers_id, "avec_classe");
					for($loop=0;$loop<count($tab_enfants);$loop+=2) {
						if($acces_visu_eleve) {
							echo "<a href='$gepiPath/eleves/visu_eleve.php?ele_login=".$tab_enfants[$loop]."' title=\"Consulter la fiche élève\">".$tab_enfants[$loop+1]."</a><br />";
						}
						else {
							echo $tab_enfants[$loop+1]."<br />";
						}
					}
					echo "</td>
	</tr>";
				}
				echo "
</table>";
			}
		}
		else {
			$_SESSION['rech_statut_responsable']="n";
		}

		if(in_array("personnel", $statut)) {
			$_SESSION['rech_statut_personnel']="y";

			$acces_modify_user=acces("/utilisateurs/modify_user.php", $_SESSION['statut']);
			$acces_edit_class=acces("/groupes/edit_class.php", $_SESSION['statut']);

			$sql="SELECT * FROM utilisateurs WHERE nom LIKE '%$rech_nom%' AND prenom LIKE '%$rech_prenom%' AND statut!='eleve' AND statut!='responsable' ORDER BY nom, prenom;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				echo "<p style='color:red'>Aucun personnel trouvé.</p>\n";
			}
			else {
				// A FAIRE : Ajouter des tests sur les droits d'accès sur les liens

				// Ne pas afficher le login pour d'autres $_SESSION['statut'] que administrateur?

				// Faire une page

				echo "<p class='bold' style='margin-top:1em;'>Personnels trouvés&nbsp;:</p>
<table class='sortable resizable boireaus boireaus_alt'>
	<tr>";
				if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
					echo "
		<th class='text' title=\"Trier sur le login\">Login</th>";
				}
				echo "
		<th class='text' title=\"Trier sur le nom prénom\">Nom prénom</th>
		<th class='text'>Compte</th>
		<th class='text' title=\"Trier sur le statut\">Statut</th>
		<th class='text' title=\"Trier sur les matières\">Matières</th>
		<th class='text' title=\"Trier sur les classes\">Classes</th>
	</tr>";
				while($lig=mysql_fetch_object($res)) {
					$style_ligne="";
					if($lig->etat=='inactif') {
						$style_ligne=" style='background-color:grey;'";
					}
					echo "
	<tr$style_ligne>";
					// Login
					if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
						if($acces_modify_user) {
							echo "
		<td><a href='$gepiPath/utilisateurs/modify_user.php?login=$lig->login' title=\"Modifier les informations utilisateur\">$lig->login</a></td>";
						}
						else {
							echo "
		<td>$lig->login</td>";
						}
					}

					// Nom prénom
					echo "
		<td>".casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, "majf2")."</td>
		<td>";

					// Compte actif ou non
					if($lig->login!="") {
						if($_SESSION['statut']=='administrateur') {
							echo lien_image_compte_utilisateur($lig->login, "", "", "y");
						}
						else {
							echo lien_image_compte_utilisateur($lig->login, "", "", "n");
						}
					}
					echo "</td>";

					// Statut
					echo "
		<td>$lig->statut</td>
		<td>";
					// Matières
					if($lig->statut=='professeur') {
						$tab_matieres_prof=get_matieres_from_prof($lig->login);
						for($loop=0;$loop<count($tab_matieres_prof);$loop++) {
							if($loop>0) {echo ", ";}
							if($tab_matieres_prof[$loop]['enseignee']=='y') {
								echo "<span style='font-weight:bold' title=\"".$tab_matieres_prof[$loop]['nom_complet']."\">".$tab_matieres_prof[$loop]['matiere']."</span>";
							}
							else {
								echo "<span style='font-size:xx-small' title=\"".$tab_matieres_prof[$loop]['nom_complet']." (non enseignée cette année)\">".$tab_matieres_prof[$loop]['matiere']."</span>";
							}
						}
					}
					echo "</td>
		<td>";
					// Classes
					if($lig->statut=='professeur') {
						$tab_classes_prof=get_classes_from_prof($lig->login);
						if(count($tab_classes_prof)>0) {
							$cpt_classe=0;
							foreach($tab_classes_prof as $id_classe_prof => $classe_prof) {
								if($cpt_classe>0) {echo ", ";}
								if($acces_modify_user) {
									echo "<a href='$gepiPath/groupes/edit_class.php?id_classe=$id_classe_prof' title=\"Modifier les enseignements de la classe $classe_prof\">$classe_prof</a>";
								}
								else {
									echo $classe_prof;
								}
								$cpt_classe++;
							}
						}
					}
					echo "</td>
	</tr>";
				}
				echo "
</table>";
			}
		}
		else {
			$_SESSION['rech_statut_personnel']="n";
		}

		require("../lib/footer.inc.php");
		die();
	}
}

?>
<form action='<?php echo $_SERVER['PHP_SELF'];?>' method='post' name='form_rech' onsubmit="valider_form_recherche()">
	<fieldset style='margin-top:0.5em; border: 1px solid grey; background-image: url("<?php echo $gepiPath;?>/images/background/opacite50.png"); '>
	<?php
		echo "		".add_token_field();
	?>
		<p></p>

		<table border='0' summary='Critères de la recherche'>
			<tr>
				<td>
					Le nom contient&nbsp;: 
				</td>
				<td>
					<input type='text' name='rech_nom' id='rech_nom' value='<?php if(isset($_SESSION['rech_nom'])) {echo $_SESSION['rech_nom'];}?>' />
				</td>
			</tr>
			<tr>
				<td>
					Le prénom contient&nbsp;: 
				</td>
				<td>
					<input type='text' name='rech_prenom' id='rech_prenom' value='<?php if(isset($_SESSION['rech_prenom'])) {echo $_SESSION['rech_prenom'];}?>' />
				</td>
			</tr>
			<tr>
				<td style='vertical-align:top'>
					Rechercher parmi&nbsp;: 
				</td>
				<td>
					<input type='checkbox' name='statut[]' id='statut_eleve' value='eleve' <?php if((!isset($_SESSION['rech_statut_eleve']))||($_SESSION['rech_statut_eleve']=="y")) {echo "checked ";}?>/><label for='statut_eleve'> élèves</label><br />
					<input type='checkbox' name='statut[]' id='statut_responsable' value='responsable' <?php if((!isset($_SESSION['rech_statut_responsable']))||($_SESSION['rech_statut_responsable']=="y")) {echo "checked ";}?>/><label for='statut_responsable'> responsables</label><br />
					<input type='checkbox' name='statut[]' id='statut_personnel' value='personnel' <?php if((!isset($_SESSION['rech_statut_personnel']))||($_SESSION['rech_statut_personnel']=="y")) {echo "checked ";}?>/><label for='statut_personnel'> personnels</label>
				</td>
			</tr>
		</table>

		<input type='hidden' name='is_posted_recherche' value='y' />
		<input type='submit' id='submit_chercher' value='Chercher' />
		<input type='button' id='button_chercher' value='Chercher' style='display:none' onclick='valider_form_recherche()' />
	</fieldset>
</form>

<p style='color:red'>A FAIRE :<br />
- Permettre le fonctionnement en ajax en plaçant une partie de la page en include...<br />
Pb pour tri?</p>

<script type='text/javascript'>
	document.getElementById('submit_chercher').style.display='none';
	document.getElementById('button_chercher').style.display='';
	document.getElementById('rech_nom').focus();

	function valider_form_recherche() {
		if((document.getElementById('rech_nom').value=='')&&(document.getElementById('rech_prenom').value=='')) {
			alert('Veuillez saisir une portion de nom ou de prénom.');
		}
		else {
			if((document.getElementById('statut_eleve').checked==false)&&(document.getElementById('statut_responsable').checked==false)&&(document.getElementById('statut_personnel').checked==false)) {
				alert('Veuillez choisir au moins une catégorie.');
			}
			else {
				//alert('OK');
				document.forms['form_rech'].submit();
			}
		}
	}
</script>

<?php
require("../lib/footer.inc.php");
?>
