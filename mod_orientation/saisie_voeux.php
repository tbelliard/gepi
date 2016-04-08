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
$variables_non_protegees = 'yes';

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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_orientation/saisie_voeux.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_orientation/saisie_voeux.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Saisie des voeux',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$acces="n";
if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('OrientationSaisieVoeuxScolarite')))||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('OrientationSaisieVoeuxCpe')))||
(($_SESSION['statut']=='professeur')&&(getSettingAOui('OrientationSaisieVoeuxPP'))&&(is_pp($_SESSION['login'])))) {
	$acces="y";
}

if($acces=="n") {
	header("Location: ../accueil.php?msg=Accès à la saisie des voeux non autorisé");
	die();
}

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$tab_orientation=array();
$sql="SELECT oob.*, oom.mef_code FROM o_orientations_base oob, o_orientations_mefs oom WHERE oob.id=oom.id_orientation ORDER BY titre;";
//echo "$sql<br />";
$res_o=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_o)>0) {
	$cpt=0;
	while($lig_o=mysqli_fetch_object($res_o)) {
		$tab_orientation[$lig_o->mef_code]['id_orientation'][]=$lig_o->id;
		$tab_orientation[$lig_o->mef_code]['titre'][]=$lig_o->titre;
		$tab_orientation[$lig_o->mef_code]['description'][]=$lig_o->description;
		$cpt++;
	}
}

$OrientationNbMaxVoeux=getSettingValue('OrientationNbMaxVoeux');

$msg="";

if((isset($id_classe))&&(isset($_POST['enregistrer_voeux']))) {

/*
$_POST['id_classe']=	33
$_POST['voeu_4921']=	Array (*)
$_POST[voeu_4921]['0']=	1
$_POST[voeu_4921]['1']=	2
$_POST[voeu_4921]['2']=	
$_POST['commentaire_4921']=	Array (*)
$_POST[commentaire_4921]['0']=	
$_POST[commentaire_4921]['1']=	
$_POST[commentaire_4921]['2']=	
*/

	$acces="n";
	if($_SESSION['statut']=="administrateur") {
		$acces="y";
	}
	elseif(($_SESSION['statut']=="scolarite")&&(is_scol_classe($_SESSION['login'], $id_classe))) {
		$acces="y";
	}
	elseif(($_SESSION['statut']=="cpe")&&(is_cpe($_SESSION['login'], $id_classe))) {
		$acces="y";
	}
	elseif(($_SESSION['statut']=="professeur")&&(is_pp($_SESSION['login'], $id_classe))) {
		$acces="y";
	}

	if($acces=="n") {
		$msg.="Vous n'avez pas accès à la classe ".get_nom_classe($id_classe).".<br />";
		unset($id_classe);
	}
	else {
		$nb_reg=0;

		include("../lib/periodes.inc.php");
		$sql="SELECT e.login, e.nom, e.prenom, e.mef_code, e.id_eleve FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='$id_classe' AND jec.periode='".($nb_periode-1)."' ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ele)==0) {
			$msg.="Aucun élève dans la classe de&nbsp;: ".get_nom_classe($id_classe)."<br />";
		}
		else {
			$date_courante=strftime("%Y-%m-%d %H:%M:%S");

			while($lig_ele=mysqli_fetch_object($res_ele)) {
				$tab_voeux_ele=array();
				$sql="SELECT * FROM o_voeux WHERE login='".$lig_ele->login."' ORDER BY rang;";
				//echo "$sql<br />";
				$res_o=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_o)>0) {
					$cpt=0;
					while($lig_o=mysqli_fetch_object($res_o)) {
						$tab_voeux_ele[$cpt]['id_orientation']=$lig_o->id_orientation;
						$tab_voeux_ele[$cpt]['commentaire']=$lig_o->commentaire;
						$tab_voeux_ele[$cpt]['rang']=$lig_o->rang;
						$tab_voeux_ele[$cpt]['saisi_par']=$lig_o->saisi_par;
						$tab_voeux_ele[$cpt]['saisi_par_cnp']=civ_nom_prenom($lig_o->saisi_par);
						$tab_voeux_ele[$cpt]['date_voeu']=formate_date($lig_o->date_voeu, "y");
						$cpt++;
					}
				}

				$rang=1;
				$voeu=isset($_POST['voeu_'.$lig_ele->id_eleve]) ? $_POST['voeu_'.$lig_ele->id_eleve] : array();
				$commentaire=isset($_POST['commentaire_'.$lig_ele->id_eleve]) ? $_POST['commentaire_'.$lig_ele->id_eleve] : array();
				for($loop=0;$loop<$OrientationNbMaxVoeux;$loop++) {
					if(isset($voeu[$loop])) {
						if((trim($voeu[$loop])=="")&&(trim($commentaire[$loop])=="")) {
							if(isset($tab_voeux_ele[$loop])) {
								$sql="DELETE FROM o_voeux WHERE login='".$lig_ele->login."' AND rang='".($loop+1)."';";
								//echo "$sql<br />";
								$del=mysqli_query($GLOBALS["mysqli"], $sql);
								$nb_reg++;
							}
						}
						else {
							if(isset($tab_voeux_ele[$loop])) {
								if($voeu[$loop]!=$tab_voeux_ele[$loop]['id_orientation']) {
									$sql="UPDATE o_voeux SET id_orientation='".$voeu[$loop]."', commentaire='".mysqli_real_escape_string($mysqli, $commentaire[$loop])."', date_voeu='".$date_courante."', saisi_par='".$_SESSION['login']."' WHERE login='".$lig_ele->login."' AND rang='".$rang."';";
									//echo "$sql<br />";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									$nb_reg++;
								}
								elseif(trim($commentaire[$loop])!=$tab_voeux_ele[$loop]['commentaire']) {
									$sql="UPDATE o_voeux SET id_orientation='".$voeu[$loop]."', commentaire='".mysqli_real_escape_string($mysqli, trim($commentaire[$loop]))."', date_voeu='".$date_courante."', saisi_par='".$_SESSION['login']."' WHERE login='".$lig_ele->login."' AND rang='".$rang."';";
									//echo "$sql<br />";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									$nb_reg++;
								}
							}
							else {
								$sql="INSERT INTO o_voeux SET id_orientation='".$voeu[$loop]."', commentaire='".mysqli_real_escape_string($mysqli, $commentaire[$loop])."', date_voeu='".$date_courante."', login='".$lig_ele->login."', rang='".$rang."', saisi_par='".$_SESSION['login']."';";
								//echo "$sql<br />";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								$nb_reg++;
							}
							$rang++;
						}
					}
					else {
						// On ne devrait pas passer là sauf modification du nombre de voeu autorisés pendant qu'un autre fait la saisie des voeux
						// ou ajout d'élève à la classe pendant la saisie
					}
				}

				// Dans le cas où par exemple le voeu 1 a été vidé en laissant les voeux suivants, il faut mettre à jour les rangs.
				$tab_voeux_ele=array();
				$sql="SELECT * FROM o_voeux WHERE login='".$lig_ele->login."' ORDER BY rang;";
				//echo "$sql<br />";
				$res_o=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_o)>0) {
					$cpt=0;
					while($lig_o=mysqli_fetch_object($res_o)) {
						$tab_voeux_ele[$cpt]['id']=$lig_o->id;
						$tab_voeux_ele[$cpt]['id_orientation']=$lig_o->id_orientation;
						$tab_voeux_ele[$cpt]['commentaire']=$lig_o->commentaire;
						$tab_voeux_ele[$cpt]['rang']=$lig_o->rang;
						$tab_voeux_ele[$cpt]['saisi_par']=$lig_o->saisi_par;
						$tab_voeux_ele[$cpt]['saisi_par_cnp']=civ_nom_prenom($lig_o->saisi_par);
						$tab_voeux_ele[$cpt]['date_voeu']=formate_date($lig_o->date_voeu, "y");
						$cpt++;
					}
				}

				$cpt=1;
				for($loop=0;$loop<count($tab_voeux_ele);$loop++) {
					if($tab_voeux_ele[$loop]['rang']!=$cpt) {
						$sql="UPDATE o_voeux SET date_voeu='".$date_courante."', rang='$cpt', saisi_par='".$_SESSION['login']."' WHERE id='".$tab_voeux_ele[$loop]['id']."';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						$nb_reg++;
					}
					$cpt++;
				}
			}
		}

		if($nb_reg>0) {
			$msg.=$nb_reg." modification(s)/enregistrement(s) effectué(s) (".strftime("%d/%m/%Y à %H:%M:%S").").<br />";
		}
		elseif($msg=="") {
			$msg.="Aucune modification n'a été effectuée (".strftime("%d/%m/%Y à %H:%M:%S").").<br />";
		}
	}
}

/*
$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";
*/
$themessage = 'Des modifications n ont pas été validées. Voulez-vous vraiment quitter sans enregistrer ?';
//================================
$titre_page = "Saisie voeux";
require_once("../lib/header.inc.php");
//================================

//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2>Saisie des voeux d'orientation</h2>

<p>Ce module est destiné à saisir les voeux d'orientation des élèves et les orientations proposées par le conseil de classe.</p>";

if(!isset($id_classe)) {
	if($_SESSION['statut']=='professeur') {
	$sql="SELECT DISTINCT c.id, c.classe FROM classes c, j_eleves_classes jec, j_eleves_professeurs jep WHERE c.id=jec.id_classe AND jep.id_classe=jec.id_classe AND jec.login=jep.login AND jep.professeur='".$_SESSION['login']."' ORDER BY c.classe;";
	}
	else {
		$sql=retourne_sql_mes_classes();
	}
	$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_clas)==0) {
		echo "<p style=color:red'>Aucune classe ne vous est associée.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	if(mysqli_num_rows($res_clas)==1) {
		$lig_clas=mysqli_fetch_object($res_clas);
		$id_classe=$lig_clas->id;
	}
	else {
		$tab_txt=array();
		$tab_lien=array();
		while($lig_clas=mysqli_fetch_object($res_clas)) {
			$tab_lien[] = $_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id;
			$tab_txt[] = $lig_clas->classe;
		}

		$nbcol=3;
		tab_liste($tab_txt,$tab_lien,$nbcol);

		require("../lib/footer.inc.php");
		die();
	}
}

include("../lib/periodes.inc.php");
$sql="SELECT e.login, e.nom, e.prenom, e.mef_code, e.id_eleve FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='$id_classe' AND jec.periode='".($nb_periode-1)."' ORDER BY e.nom, e.prenom;";
//echo "$sql<br />";
$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_ele)==0) {
	echo "<p style=color:red'>Aucun élève dans la classe de&nbsp;: ".get_nom_classe($id_classe)."</p>";
	require("../lib/footer.inc.php");
	die();
}

/*
echo "<pre>";
print_r($tab_orientation);
echo "</pre>";
*/

echo "
<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>
	<fieldset class='fieldset_opacite50'>
		<p style='text-align:center;'><input type='submit' value='Valider' /></p>
		".add_token_field()."
		<input type='hidden' name='id_classe' value='$id_classe' />
		<table class='boireaus boireaus_alt' summary='Saisie des voeux'>
			<thead>
				<tr>
					<th>Élève</th>
					<th>Nom prénom</th>
					<th>Voeux</th>
				</tr>
			</thead>
			<tbody>";

while($lig_ele=mysqli_fetch_object($res_ele)) {
	$tab_voeux_ele=array();
	$sql="SELECT * FROM o_voeux WHERE login='".$lig_ele->login."' ORDER BY rang;";
	//echo "$sql<br />";
	$res_o=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_o)>0) {
		$cpt=1;
		while($lig_o=mysqli_fetch_object($res_o)) {
			$tab_voeux_ele[$cpt]['id_orientation']=$lig_o->id_orientation;
			$tab_voeux_ele[$cpt]['commentaire']=$lig_o->commentaire;
			$tab_voeux_ele[$cpt]['rang']=$lig_o->rang;
			$tab_voeux_ele[$cpt]['saisi_par']=$lig_o->saisi_par;
			$tab_voeux_ele[$cpt]['saisi_par_cnp']=civ_nom_prenom($lig_o->saisi_par);
			$tab_voeux_ele[$cpt]['date_voeu']=formate_date($lig_o->date_voeu, "y");
			$cpt++;
		}
	}

	echo "
				<tr>
					<td><a href='../eleves/visu_eleve.php?ele_login=".$lig_ele->login."' target='_blank' title=\"Voir le classeur/dossier élève dans un nouvel onglet.\"><img src='../images/icons/ele_onglets.png' class='icone16' alt='Onglets' /></a></td>
					<td>".$lig_ele->nom." ".$lig_ele->prenom."</td>
					<td>";
	for($loop=1;$loop<=$OrientationNbMaxVoeux;$loop++) {
		$commentaire="";
		$selected_aucun="";
		if(!isset($tab_voeux_ele[$loop])) {
			$selected_aucun=" selected";
		}
		else {
			$commentaire=preg_replace('/"/', " ", $tab_voeux_ele[$loop]['commentaire']);
		}
		echo "
						Voeu ".($loop)."
						<select name='voeu_".$lig_ele->id_eleve."[]' onchange=\"changement();\">
							<option value='' title=\"Choisissez un voeu.\nSi l'orientation souhaitée n'est pas dans la liste proposée, choisissez 'Autre orientation' et précisez en commentaire l'orientation.\"".$selected_aucun.">---</option>";
		if(isset($tab_orientation[$lig_ele->mef_code])) {
			for($loop2=0;$loop2<count($tab_orientation[$lig_ele->mef_code]['id_orientation']);$loop2++) {
				$selected="";
				if((isset($tab_voeux_ele[$loop]))&&($tab_voeux_ele[$loop]['id_orientation']==$tab_orientation[$lig_ele->mef_code]['id_orientation'][$loop2])) {
					$selected=" selected";
				}
				echo "
							<option value='".$tab_orientation[$lig_ele->mef_code]['id_orientation'][$loop2]."' title=\"".preg_replace('/"/', " ", $tab_orientation[$lig_ele->mef_code]['description'][$loop2])."\"".$selected.">".$tab_orientation[$lig_ele->mef_code]['titre'][$loop2]."</option>";
			}
		}
		$selected="";
		if((isset($tab_voeux_ele[$loop]))&&($tab_voeux_ele[$loop]['id_orientation']=="0")) {
			$selected=" selected";
		}
		echo "
							<option value='0' title=\"Si l'orientation souhaitée n'est pas dans la liste proposée, choisissez 'Autre orientation' et précisez en commentaire l'orientation.\"".$selected.">Autre orientation</option>
						</select>
						<input type='text' name='commentaire_".$lig_ele->id_eleve."[]' value=\"".$commentaire."\" size='30' onchange=\"changement();\" /><br />";
	}
	echo "
					</td>
				</tr>";
}
echo "
			</tbody>
		</table>
		<input type='hidden' name='enregistrer_voeux' value='y' />
		<p style='text-align:center;'><input type='submit' value='Valider' /></p>
	</fieldset>
</form>
<p style='color:red;margin-top:1em;'><em>A FAIRE&nbsp;:</em></p>
<ul>
	<li>Permettre de faire apparaitre les voeux dans les bulletins (<em>sous la ligne absences/retards</em>).<br />
	Pouvoir ne faire apparaitre que le 1er voeu sur le bulletin.<br />
	Et pouvoir faire apparaitre l'orientation proposée/conseillée par le conseil de classe.</li>
	<li>Permettre la saisie de l'orientation conseillée depuis les graphes, depuis les pages de saisie de l'avis du conseil de classe.</li>
	<li>Permettre la saisie des voeux en parent/élève.</li>
	<li>Permettre de produire un PDF des voeux formulés, des orientations proposées.</li>
</ul>
<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
