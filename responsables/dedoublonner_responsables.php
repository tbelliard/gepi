<?php
/*
 *
 * Copyright 2001-2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$sql="SELECT 1=1 FROM droits WHERE id='/responsables/dedoublonner_responsables.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/responsables/dedoublonner_responsables.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Dédoublonner les responsables.',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

function fwrite_sql($chaine) {
	$generer_sql="y";
	if($generer_sql=="y") {
		$f=fopen("/tmp/dedoublonnage_comptes_parents.sql", "a+");
		fwrite($f, $chaine."\n");
		fclose($f);
	}
}

//debug_var();
if(isset($_POST['dedoublonner'])) {
	check_token();
	$msg="";

	// Boucle sur les suppressions
	$nb_suppr=0;
	$supprimer=isset($_POST["supprimer"]) ? $_POST["supprimer"] : array();
	for($loop=0;$loop<count($supprimer);$loop++) {
		$pers_id=$supprimer[$loop];
		$tab=get_info_responsable("", $pers_id);
		if((isset($tab["login"]))&&($tab["login"]!="")) {
			$sql="DELETE FROM tentatives_intrusion WHERE login='".$tab["login"]."';";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
			if($del) {
				fwrite_sql($sql);
			}

			$sql="DELETE FROM log WHERE LOGIN='".$tab["login"]."';";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
			if($del) {
				fwrite_sql($sql);
			}

			$sql="DELETE FROM utilisateurs WHERE login='".$tab["login"]."' AND statut='responsable';";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
			if($del) {
				fwrite_sql($sql);
			}
		}

		$sql="DELETE FROM responsables2 WHERE pers_id='".$pers_id."';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
		if($del) {
			fwrite_sql($sql);
		}

		$sql="DELETE FROM resp_pers WHERE pers_id='".$pers_id."';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$del) {
			$msg.="Erreur lors de la suppression du responsable n°<a href='modify_resp.php?pers_id=".$pers_id."' target='_blank' title=\"Voir le responsable dans un nouvel onglet.\">".$pers_id." ".$tab["nom"]." ".$tab["prenom"]."</a>";
		}
		else {
			fwrite_sql($sql);
			$nb_suppr++;
		}
	}
	if($nb_suppr>0) {
		$msg.=$nb_suppr." responsable(s) supprimé(s) purement et simplement sans récupérer/transférer les éventuelles assocations avec des élèves.";
	}

	if(isset($_POST['cpt'])) {
		$cpt=$_POST['cpt'];
	}
	else {
		$sql="SELECT pers_id,nom,prenom,COUNT(*) AS nb_doublons FROM resp_pers GROUP BY nom,prenom HAVING COUNT(*)>1 ORDER BY nom,prenom;";
		$test_resp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test_resp)>0) {
			$cpt=mysqli_num_rows($test_resp);

			$msg.="Un trop grand nombre de variables a probablement été posté.<br />Peut-être une restriction PHP ou suhosin.<br />Il faudra peut-être effectuer le traitement en plusieurs fois.<br />";
		}
	}

	if(isset($cpt)) {
		$nb_suppr=0;
		for($loop=0;$loop<$cpt;$loop++) {
			$pers_id=isset($_POST["fusionner_conserver_".$loop]) ? $_POST["fusionner_conserver_".$loop] : NULL;
			if(isset($pers_id)) {
				$tab=get_info_responsable("", $pers_id);
				if((isset($tab["nom"]))&&(isset($tab["prenom"]))) {
					$sql="SELECT * FROM resp_pers WHERE nom='".mysqli_real_escape_string($GLOBALS['mysqli'], $tab["nom"])."' AND prenom='".mysqli_real_escape_string($GLOBALS['mysqli'], $tab["prenom"])."' AND pers_id!='".$pers_id."';";
					$res_resp2=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_resp2)>0) {
						$tab_ele_id=array();
						$sql="SELECT * FROM responsables2 WHERE pers_id='".$pers_id."';";
						$res_resp3=mysqli_query($GLOBALS["mysqli"], $sql);
						while($lig_resp3=mysqli_fetch_object($res_resp3)) {
							$tab_ele_id[]=$lig_resp3->ele_id;
						}

						while($lig_resp2=mysqli_fetch_object($res_resp2)) {
							$sql="SELECT * FROM responsables2 WHERE pers_id='".$lig_resp2->pers_id."';";
							$res_resp3=mysqli_query($GLOBALS["mysqli"], $sql);
							while($lig_resp3=mysqli_fetch_object($res_resp3)) {
								if(!in_array($lig_resp3->ele_id, $tab_ele_id)) {
									$sql="UPDATE responsables2 SET pers_id='".$pers_id."' WHERE pers_id='".$lig_resp2->pers_id."' AND ele_id='".$lig_resp3->ele_id."' ;";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									if($update) {
										fwrite_sql($sql);
									}
								}
							}

							if((isset($lig_resp2->login))&&($lig_resp2->login!="")) {
								$sql="DELETE FROM tentatives_intrusion WHERE login='".$lig_resp2->login."';";
								$del=mysqli_query($GLOBALS["mysqli"], $sql);
								if($del) {
									fwrite_sql($sql);
								}

								$sql="DELETE FROM log WHERE LOGIN='".$lig_resp2->login."';";
								$del=mysqli_query($GLOBALS["mysqli"], $sql);
								if($del) {
									fwrite_sql($sql);
								}

								$sql="DELETE FROM utilisateurs WHERE login='".$lig_resp2->login."' AND statut='responsable';";
								$del=mysqli_query($GLOBALS["mysqli"], $sql);
								if($del) {
									fwrite_sql($sql);
								}
							}

							$sql="DELETE FROM responsables2 WHERE pers_id='".$lig_resp2->pers_id."';";
							$del=mysqli_query($GLOBALS["mysqli"], $sql);
							if($del) {
								fwrite_sql($sql);
							}

							$sql="DELETE FROM resp_pers WHERE pers_id='".$lig_resp2->pers_id."';";
							$del=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$del) {
								$msg.="Erreur lors de la suppression du responsable n°<a href='modify_resp.php?pers_id=".$lig_resp2->pers_id."' target='_blank' title=\"Voir le responsable dans un nouvel onglet.\">".$lig_resp2->pers_id." ".$lig_resp2->nom." ".$lig_resp2->prenom."</a>";
							}
							else {
								fwrite_sql($sql);
								$nb_suppr++;
							}
						}
					}
				}
			}
		}
		if($nb_suppr>0) {
			$msg.=$nb_suppr." responsable(s) supprimé(s) après transfert des associations élèves vers le responsable à conserver.";
		}
	}
	else {
		$msg="Aucun dédoublonnage n'a été validé.<br />";
	}


}


//**************** EN-TETE *****************
$titre_page = "Dédoublonnage des responsables";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();
echo "<p class='bold'><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

echo "<p>Il peut arriver que certains parents soient déclarés deux fois.<br />
Cela peut se produire dans le cas où un parent est associé à deux enfants dans l'établissement.<br />
Cela arrive assez fréquemment avec des saisies dans Sconet.<br />
Cela peut aussi se produire dans le cas d'une initialisation CSV.</p>
<p style='margin-top:1em;margin-bottom:1em;'>Dans le cas d'une initialisation d'après Sconet, il peut être intéressant de consulter les liens suivants pour régler le problème à la source&nbsp;:<br />
<a href='http://www.sylogix.org/projects/gepi/wiki/Adresses_resp_doublons' target='_blank'>http://www.sylogix.org/projects/gepi/wiki/Adresses_resp_doublons</a> et <br /><a href='http://www.sylogix.org/attachments/download/1152/2012-09-13_SIECLE-methode%20pour%20supprimer%20les%20doublons%20responsables.pdf' target='_blank'>http://www.sylogix.org/attachments/download/1152/2012-09-13_SIECLE-methode%20pour%20supprimer%20les%20doublons%20responsables.pdf</a></p>";

$sql="SELECT pers_id,nom,prenom,COUNT(*) AS nb_doublons FROM resp_pers GROUP BY nom,prenom HAVING COUNT(*)>1 ORDER BY nom,prenom;";
$test_resp=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test_resp)==0) {
	echo "<p style='color:red'>Aucun cas d'homonymie susceptible de correspondre à un doublon responsable n'a été détectéé.</p>";
	require("../lib/footer.inc.php");
	die();
}

echo "<p>".mysqli_num_rows($test_resp)." responsable(s) potentiellement en doublon ont été détectés.</p>

<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='dedoublonner' value='y' />

		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th>Id</th>
					<th>Login</th>
					<th>Nom</th>
					<th>Prénom</th>
					<th>Tel</th>
					<th>Adresse</th>
					<th>Enfant(s)</th>
					<th>Transférer les associations élèves vers le responsable sélectionné, puis supprimer le(s) doublon(s)</th>
					<th>Supprimer sans transférer les associations élève vers l'homonyme</th>
				</tr>
			</thead>
			<tbody>";
$cpt=0;
while($lig_resp=mysqli_fetch_object($test_resp)) {
	$tab=get_info_responsable("", $lig_resp->pers_id);
	if((isset($tab["nom"]))&&(isset($tab["prenom"]))) {
		$sql="SELECT * FROM resp_pers WHERE nom='".mysqli_real_escape_string($GLOBALS['mysqli'], $tab["nom"])."' AND prenom='".mysqli_real_escape_string($GLOBALS['mysqli'], $tab["prenom"])."' AND pers_id!='".$lig_resp->pers_id."';";
		$res_resp2=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_resp2)>0) {
			echo "
				<tr>
					<td><a href='modify_resp.php?pers_id=".$lig_resp->pers_id."' target='_blank' title=\"Voir la fiche responsable dans un nouvel onglet.\">".$lig_resp->pers_id."</a></td>
					<td>".$tab["login"];
			if($tab["login"]!="") {
				echo lien_image_compte_utilisateur($tab["login"], "responsable", "_blank", "n");
			}
			echo "</td>
					<td>".$tab["nom"]."</td>
					<td>".$tab["prenom"]."</td>
					<td>";
			$chaine_tel="";
			if((isset($tab["tel_pers"]))&&($tab["tel_pers"]!="")) {
				$chaine_tel.=$tab["tel_pers"];
			}
			if((isset($tab["tel_prof"]))&&($tab["tel_prof"]!="")) {
				if($chaine_tel!="") {
					$chaine_tel.="<br />";
				}
				$chaine_tel.=$tab["tel_prof"];
			}
			if((isset($tab["tel_port"]))&&($tab["tel_port"]!="")) {
				if($chaine_tel!="") {
					$chaine_tel.="<br />";
				}
				$chaine_tel.=$tab["tel_port"];
			}
			echo "</td>
					<td>".$tab["adresse"]["en_ligne"]."</td>
					<td>";
			for($i=1;$i<count($tab["enfants"]);$i+=2) {
				if($i>1) {echo ", ";}
				echo $tab["enfants"][$i];
			}
			echo "</td>
					<td><input type='radio' name='fusionner_conserver_".$cpt."' value='".$lig_resp->pers_id."' /></td>
					<td><input type='checkbox' name='supprimer[]' value='".$lig_resp->pers_id."' /></td>
				</tr>";
			while($lig_resp2=mysqli_fetch_object($res_resp2)) {
				echo "
				<tr>
					<td><a href='modify_resp.php?pers_id=".$lig_resp2->pers_id."' target='_blank' title=\"Voir la fiche responsable dans un nouvel onglet.\">".$lig_resp2->pers_id."</a></td>
					<td>";
				if($lig_resp2->login!="") {
					echo lien_image_compte_utilisateur($lig_resp2->login, "responsable", "_blank", "n");
				}
						echo "</td>
					<td>".$lig_resp2->nom."</td>
					<td>".$lig_resp2->prenom."</td>
					<td>";
				$chaine_tel="";
				if((isset($lig_resp2->tel_pers))&&($lig_resp2->tel_pers!="")) {
					$chaine_tel.=$lig_resp2->tel_pers;
				}
				if((isset($lig_resp2->tel_prof))&&($lig_resp2->tel_prof!="")) {
					if($chaine_tel!="") {
						$chaine_tel.="<br />";
					}
					$chaine_tel.=$lig_resp2->tel_prof;
				}
				if((isset($lig_resp2->tel_port))&&($lig_resp2->tel_port!="")) {
					if($chaine_tel!="") {
						$chaine_tel.="<br />";
					}
					$chaine_tel.=$lig_resp2->tel_port;
				}
				echo "</td>
					<td>";
				$current_adr=get_adresse_responsable($lig_resp2->pers_id);
				if(isset($current_adr["en_ligne"])) {
					echo $current_adr["en_ligne"];
				}
						echo "</td>
					<td>";
			$current_ele=get_enfants_from_pers_id($lig_resp2->pers_id);
			if(count($current_ele)>0) {
				for($i=1;$i<count($current_ele);$i+=2) {
					if($i>1) {echo ", ";}
					echo $current_ele[$i];
				}
			}
					echo "</td>
					<td><input type='radio' name='fusionner_conserver_".$cpt."' value='".$lig_resp2->pers_id."' /></td>
					<td><input type='checkbox' name='supprimer[]' value='".$lig_resp2->pers_id."' /></td>
				</tr>";
			}
			echo "
				<tr>
					<td colspan='9' style='background-color:grey'>&nbsp;</td>
				</tr>";
			$cpt++;
		}
	}
}
echo "
			</tbody>
		</table>
		<input type='hidden' name='cpt' value='$cpt' />
		<p><input type='submit' value='Valider' /></p>
	</fieldset>
</form>";


require("../lib/footer.inc.php");
?>
