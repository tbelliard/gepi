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

$sql="SELECT 1=1 FROM droits WHERE id='/responsables/recup_comptes_parents.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/responsables/recup_comptes_parents.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
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
		$f=fopen("/tmp/recup_comptes_parents.sql", "a+");
		fwrite($f, $chaine."\n");
		fclose($f);
	}
}

//debug_var();
if(isset($_POST['rapprocher'])) {
	check_token();
	$msg="";

	$nb_recup=0;
	$recup_login=isset($_POST["recup_login"]) ? $_POST["recup_login"] : array();
	foreach($recup_login as $current_pers_id => $current_login) {
		//echo "\$current_pers_id=$current_pers_id =&gt; \$current_login=$current_login<br />";

		if($current_login!="") {
			$test_unicite = test_unique_login($current_login, "y");
			if ($test_unicite!='yes') {
				$msg.="Le login $current_login est déjà attribué; il ne peut pas être affecté au responsable n°<a href='modify_resp.php?pers_id=$current_pers_id' target='_blank'>".$current_pers_id."</a><br />";
			}
			else {
				$sql="SELECT * FROM tempo_utilisateurs WHERE login='".$current_login."'";
				//echo "$sql<br />";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)==0) {
					$msg.="Le login $current_login n'a pas été trouvé dans la table tempo_utilisateurs des comptes mis en réserve.<br />";
				}
				else {
					$lig_tmp_u=mysqli_fetch_object($test);
					if($lig_tmp_u->statut!='responsable') {
						$msg.="Le login $current_login n'est pas de type 'responsable', mais '".$lig_tmp_u->statut."'; il ne peut pas être attribué à un responsable.<br />";
					}
					elseif($lig_tmp_u->temoin=='recree') {
						$msg.="Le login $current_login a déjà été ré-attribué à un utilisateur; il ne peut pas être attribué au responsable n°<a href='modify_resp.php?pers_id=$current_pers_id' target='_blank'>".$current_pers_id."</a>.<br />";
					}
					else {
						$tab=get_info_responsable("", $current_pers_id);

						$sql="INSERT INTO utilisateurs SET login='".$current_login."', nom='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab["nom"])."', prenom='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab["prenom"])."', ";
						$sql.="password='".$lig_tmp_u->password."', salt='".$lig_tmp_u->salt."', email='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_tmp_u->email)."', statut='responsable', etat='inactif', change_mdp='n', auth_mode='".$lig_tmp_u->auth_mode."';";
						//if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
						//echo "$sql<br />";
						$insert_u=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert_u) {
							echo "<span style='color:red;'>Erreur</span> lors de la création du compte utilisateur pour ".$lig2->nom1." ".$lig2->prenom1."&nbsp;:<br /><span style='color:red;'>$sql</span><br />";
						}
						else {
							fwrite_sql($sql);

							$sql="UPDATE resp_pers SET login='".$lig_tmp_u->login."' WHERE pers_id='".$current_pers_id."';";
							//echo "$sql<br />";
							//if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
							$update_rp=mysqli_query($GLOBALS["mysqli"], $sql);
							if($update_rp) {
								fwrite_sql($sql);
							}

							$sql="UPDATE tempo_utilisateurs SET temoin='recree' WHERE login='".$current_login."' AND statut='responsable';";
							//echo "$sql<br />";
							//if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
							$update_tmp_u=mysqli_query($GLOBALS["mysqli"], $sql);
							if($update_tmp_u) {
								fwrite_sql($sql);
							}
							$nb_recup++;
						}
					}
				}
			}
		}
	}
	$msg.=$nb_recup." compte(s) récupéré(s).<br />";

	/*
	for($loop=0;$loop<count($supprimer);$loop++) {
		$pers_id=$supprimer[$loop];
		$tab=get_info_responsable("", $pers_id);
		if((isset($tab["login"]))&&($tab["login"]!="")) {
			$sql="DELETE FROM tentatives_intrusion WHERE login='".$tab["login"]."';";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);

			$sql="DELETE FROM log WHERE LOGIN='".$tab["login"]."';";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);

			$sql="DELETE FROM utilisateurs WHERE login='".$tab["login"]."' AND statut='responsable';";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
		}

		$sql="DELETE FROM responsables2 WHERE pers_id='".$pers_id."';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);

		$sql="DELETE FROM resp_pers WHERE pers_id='".$pers_id."';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$del) {
			$msg.="Erreur lors de la suppression du responsable n°<a href='modify_resp.php?pers_id=".$pers_id."' target='_blank' title=\"Voir le responsable dans un nouvel onglet.\">".$pers_id." ".$tab["nom"]." ".$tab["prenom"]."</a>";
		}
		else {
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
								}
							}

							if((isset($lig_resp2->login))&&($lig_resp2->login!="")) {
								$sql="DELETE FROM tentatives_intrusion WHERE login='".$lig_resp2->login."';";
								$del=mysqli_query($GLOBALS["mysqli"], $sql);

								$sql="DELETE FROM log WHERE LOGIN='".$lig_resp2->login."';";
								$del=mysqli_query($GLOBALS["mysqli"], $sql);

								$sql="DELETE FROM utilisateurs WHERE login='".$lig_resp2->login."' AND statut='responsable';";
								$del=mysqli_query($GLOBALS["mysqli"], $sql);
							}

							$sql="DELETE FROM responsables2 WHERE pers_id='".$lig_resp2->pers_id."';";
							$del=mysqli_query($GLOBALS["mysqli"], $sql);

							$sql="DELETE FROM resp_pers WHERE pers_id='".$lig_resp2->pers_id."';";
							$del=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$del) {
								$msg.="Erreur lors de la suppression du responsable n°<a href='modify_resp.php?pers_id=".$lig_resp2->pers_id."' target='_blank' title=\"Voir le responsable dans un nouvel onglet.\">".$lig_resp2->pers_id." ".$lig_resp2->nom." ".$lig_resp2->prenom."</a>";
							}
							else {
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
	*/

}


//**************** EN-TETE *****************
$titre_page = "Dédoublonnage des responsables";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();
echo "<p class='bold'><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
echo "<p>Lors d'une initialisation par un autre mode que XML, il arrive que des comptes utilisateur mis en réserve lors du changement d'année ne soient pas récupérés automatiquement faute d'une identification sûre.<br />
Le dispositif est spécialement conçu pour le cas d'une initialisation CSV.</p>";

//$sql="SELECT * FROM resp_pers WHERE login=''";
//$sql="SELECT e.elenoet FROM eleves e, responsables2 r, resp_pers rp WHERE rp.login='' AND r.pers_id=rp.pers_id AND (r.resp_legal='1' OR r.resp_legal='2') AND e.ele_id=r.ele_id;";
$sql="SELECT DISTINCT rp.* FROM eleves e, responsables2 r, resp_pers rp WHERE rp.login='' AND r.pers_id=rp.pers_id AND (r.resp_legal='1' OR r.resp_legal='2') AND e.ele_id=r.ele_id;";
//echo "$sql<br />\n";
$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_resp)==0) {
	echo "<p style='color:red'>Tous les responsables associés à des élèves ont un login.</p>";
	require("../lib/footer.inc.php");
	die();
}

echo "<p>".mysqli_num_rows($res_resp)." responsable(s) n'a(ont) pas de login.<br />
Nous allons chercher parmi eux ceux qui pourraient avoir un couple login et mot de passe mis en réserve lors du changement d'année.</p>

<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='rapprocher' value='y' />

		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th colspan='4'>Responsable</th>
					<th colspan='4'>Candidat</th>
				</tr>
				<tr>
					<th>Id</th>
					<th>Nom</th>
					<th>Prénom</th>
					<th>Enfant(s)</th>
					<th>Rapprocher</th>
					<th>Login</th>
					<th>Nom</th>
					<th>Prénom</th>
				</tr>
			</thead>
			<tbody>";
$cpt=0;
while($lig_resp=mysqli_fetch_object($res_resp)) {

	$login_resp="";
	$tab_candidats=array();
	$sql="SELECT e.elenoet FROM eleves e, responsables2 r WHERE e.ele_id=r.ele_id AND r.pers_id='$lig_resp->pers_id';";
	//echo "$sql<br />\n";
	$res_elenoet=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_elenoet)>0) {
		while($lig_elenoet=mysqli_fetch_object($res_elenoet)) {
			$sql="SELECT * FROM tempo_utilisateurs WHERE identifiant2 LIKE '%|".$lig_elenoet->elenoet."|%' AND nom LIKE '".mysqli_real_escape_string($GLOBALS["mysqli"], preg_replace("/[^A-Za-z]/","%",$lig_resp->nom))."' AND prenom LIKE '".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_resp->prenom)."' AND statut='responsable';";
			//echo "$sql<br />\n";
			$res_tmp_u=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_tmp_u)>0) {
				while($lig_tmp_u=mysqli_fetch_array($res_tmp_u)) {
					if(!array_key_exists($lig_tmp_u['login'],$tab_candidats)) {
						$tab_candidats[$lig_tmp_u['login']]=$lig_tmp_u;
					}
				}
			}
		}
	}

	if(count($tab_candidats)>0) {
		$ligne1=0;
		foreach($tab_candidats as $current_login => $current_candidat) {
			if($ligne1==0) {
				echo "
				<tr>
					<td rowspan='".(count($tab_candidats)+1)."'><a href='modify_resp.php?pers_id=".$lig_resp->pers_id."' target='_blank' title=\"Voir la fiche responsable dans un nouvel onglet.\">".$lig_resp->pers_id."</a></td>
					<td rowspan='".(count($tab_candidats)+1)."'>".$lig_resp->nom."</td>
					<td rowspan='".(count($tab_candidats)+1)."'>".$lig_resp->prenom."</td>
					<td rowspan='".(count($tab_candidats)+1)."'>";
				$current_ele=get_enfants_from_pers_id($lig_resp->pers_id);
				if(count($current_ele)>0) {
					for($i=1;$i<count($current_ele);$i+=2) {
						if($i>1) {echo ", ";}
						echo $current_ele[$i];
					}
				}
				echo "</td>
					<td><input type='radio' name='recup_login[".$lig_resp->pers_id."]' value=\"\" /></td>
					<td colspan='3'>Aucun</td>
				</tr>";
			}

			echo "
				<tr>
					<td><input type='radio' name='recup_login[".$lig_resp->pers_id."]' value=\"".$current_candidat['login']."\" /></td>
					<td>".$current_candidat['login']."</td>
					<td>".$current_candidat['nom']."</td>
					<td>".$current_candidat['prenom']."</td>
				</tr>";

			$ligne1++;
		}
		echo "
				<tr>
					<td colspan='8' style='background-color:grey'>&nbsp;</td>
				</tr>";
	}
}

echo "
			</tbody>
		</table>
		<p><input type='submit' value='Valider' /></p>
	</fieldset>
</form>";

require("../lib/footer.inc.php");
?>
