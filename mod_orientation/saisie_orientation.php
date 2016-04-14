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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_orientation/saisie_orientation.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_orientation/saisie_orientation.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Saisie orientation élève',
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
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('OrientationSaisieOrientationScolarite')))||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('OrientationSaisieOrientationCpe')))||
(($_SESSION['statut']=='professeur')&&(getSettingAOui('OrientationSaisieOrientationPP'))&&(is_pp($_SESSION['login'])))) {
	$acces="y";
}

if($acces=="n") {
	header("Location: ../accueil.php?msg=Accès à la saisie des orientations non autorisé");
	die();
}

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$tab_orientation=array();
$tab_orientation2=array();
$sql="SELECT oob.*, oom.mef_code FROM o_orientations_base oob, o_orientations_mefs oom WHERE oob.id=oom.id_orientation ORDER BY titre;";
//echo "$sql<br />";
$res_o=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_o)>0) {
	$cpt=0;
	while($lig_o=mysqli_fetch_object($res_o)) {
		$tab_orientation[$lig_o->mef_code]['id_orientation'][]=$lig_o->id;
		$tab_orientation[$lig_o->mef_code]['titre'][]=$lig_o->titre;
		$tab_orientation[$lig_o->mef_code]['description'][]=$lig_o->description;

		$tab_orientation2[$lig_o->id]['id_orientation']=$lig_o->id;
		$tab_orientation2[$lig_o->id]['titre']=$lig_o->titre;
		$tab_orientation2[$lig_o->id]['description']=$lig_o->description;

		$cpt++;
	}
}

$OrientationNbMaxVoeux=getSettingValue('OrientationNbMaxVoeux');
$OrientationNbMaxOrientation=getSettingValue('OrientationNbMaxOrientation');

$msg="";


if((isset($_POST['id_eleve']))&&(isset($_POST['mode']))&&($_POST['mode']=="saisie_orientation_eleve")) {
	check_token();

	$id_eleve=$_POST['id_eleve'];
	$sql="SELECT * FROM eleves WHERE id_eleve='$id_eleve';";
	//echo "$sql<br />";
	$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ele)==0) {
		echo "Élève non trouvé.<br />";
		die();
	}

	$lig_ele=mysqli_fetch_object($res_ele);

	$tab_orientation_eleve=get_tab_voeux_orientations_ele($lig_ele->login);
	$tab_voeux_ele=$tab_orientation_eleve['voeux'];
	$tab_o_ele=$tab_orientation_eleve['orientation_proposee'];

	$date_courante=strftime("%Y-%m-%d %H:%M:%S");

	$rang=1;
	for($loop=1;$loop<=$OrientationNbMaxOrientation;$loop++) {
		if(isset($_POST['tab_orientation_'.$loop])) {

			$orientation[$loop]=$_POST['tab_orientation_'.$loop];

			$commentaire[$loop]=$_POST['tab_commentaires_orientation_'.$loop];
			$commentaire[$loop]=urldecode($commentaire[$loop]);
			// Le $purifier est initialisé dans lib/traitement_data.inc.php
			$commentaire[$loop]=$purifier->purify($commentaire[$loop]);
			$commentaire[$loop]=stripslashes($commentaire[$loop]);

			if((trim($orientation[$loop])=="")&&(trim($commentaire[$loop])=="")) {
				if(isset($tab_o_ele[$loop])) {
					$sql="DELETE FROM o_orientations WHERE login='".$lig_ele->login."' AND rang='".$loop."';";
					//echo "$sql<br />";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					//$nb_reg++;
				}
			}
			else {
				if(isset($tab_o_ele[$loop])) {
					if($orientation[$loop]!=$tab_o_ele[$loop]['id_orientation']) {
						$sql="UPDATE o_orientations SET id_orientation='".$orientation[$loop]."', commentaire='".mysqli_real_escape_string($mysqli, $commentaire[$loop])."', date_orientation='".$date_courante."', saisi_par='".$_SESSION['login']."' WHERE login='".$lig_ele->login."' AND rang='".$rang."';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						//$nb_reg++;
					}
					elseif(trim($commentaire[$loop])!=$tab_o_ele[$loop]['commentaire']) {
						$sql="UPDATE o_orientations SET id_orientation='".$orientation[$loop]."', commentaire='".mysqli_real_escape_string($mysqli, trim($commentaire[$loop]))."', date_orientation='".$date_courante."', saisi_par='".$_SESSION['login']."' WHERE login='".$lig_ele->login."' AND rang='".$rang."';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						//$nb_reg++;
					}
				}
				else {
					$sql="INSERT INTO o_orientations SET id_orientation='".$orientation[$loop]."', commentaire='".mysqli_real_escape_string($mysqli, $commentaire[$loop])."', date_orientation='".$date_courante."', login='".$lig_ele->login."', rang='".$rang."', saisi_par='".$_SESSION['login']."';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					//$nb_reg++;
				}
				$rang++;
			}

		}
	}

	$avis_orientation=isset($_POST['avis_orientation']) ? $_POST['avis_orientation'] : "";
	$avis_orientation=urldecode($avis_orientation);
	// Le $purifier est initialisé dans lib/traitement_data.inc.php
	$avis_orientation=$purifier->purify($avis_orientation);
	$avis_orientation=nl2br($avis_orientation);
	$avis_orientation=stripslashes($avis_orientation);

	$avis_o_ele="";
	$sql="SELECT DISTINCT * FROM o_avis oa WHERE oa.login='".$lig_ele->login."';";
	//echo "$sql<br />";
	$res_o=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_o)>0) {
		$cpt=1;
		$lig_o=mysqli_fetch_object($res_o);
		$avis_o_ele=$lig_o->avis;

		//if(stripslashes($avis_orientation)!=$avis_o_ele) {
		if($avis_orientation!=$avis_o_ele) {
			$sql="UPDATE o_avis SET avis='".mysqli_real_escape_string($mysqli, $avis_orientation)."' WHERE login='".$lig_ele->login."';";
			//echo strftime("%Y%m%d %H%M%S")."<br />";
			//echo "$sql<br />";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$update) {
				echo "<span style='color:red'>Erreur lors de la mise à jour de l'avis</span>.<br />";
			}
		}
	}
	else {
		$sql="INSERT INTO o_avis SET avis='".mysqli_real_escape_string($mysqli, $avis_orientation)."', login='".$lig_ele->login."';";
		//echo strftime("%Y%m%d %H%M%S")."<br />";
		//echo "$sql<br />";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$insert) {
			echo "<span style='color:red'>Erreur lors de l'enregistrement de l'avis</span>.<br />";
		}
	}


	// Afficher les orientations:
	echo get_liste_orientations_proposees($lig_ele->login);
	//echo nl2br(get_avis_orientations_proposees($lig_ele->login));
	echo get_avis_orientations_proposees($lig_ele->login);
	
	//debug_var();

	die();
}

if((isset($id_classe))&&(isset($_POST['enregistrer_orientation']))) {
	check_token();

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
				$tab_o_ele=array();
				$sql="SELECT * FROM o_orientations WHERE login='".$lig_ele->login."' ORDER BY rang;";
				//echo "$sql<br />";
				$res_o=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_o)>0) {
					$cpt=0;
					while($lig_o=mysqli_fetch_object($res_o)) {
						$tab_o_ele[$cpt]['id_orientation']=$lig_o->id_orientation;
						$tab_o_ele[$cpt]['commentaire']=$lig_o->commentaire;
						$tab_o_ele[$cpt]['date_orientation']=formate_date($lig_o->date_orientation, "y");
						$cpt++;
					}
				}

				$rang=1;
				$orientation=isset($_POST['orientation_'.$lig_ele->id_eleve]) ? $_POST['orientation_'.$lig_ele->id_eleve] : array();
				$commentaire=isset($_POST['commentaire_'.$lig_ele->id_eleve]) ? $_POST['commentaire_'.$lig_ele->id_eleve] : array();
				for($loop=0;$loop<$OrientationNbMaxOrientation;$loop++) {
					if(isset($orientation[$loop])) {
						if((trim($orientation[$loop])=="")&&(trim($commentaire[$loop])=="")) {
							if(isset($tab_o_ele[$loop])) {
								$sql="DELETE FROM o_orientations WHERE login='".$lig_ele->login."' AND rang='".($loop+1)."';";
								//echo "$sql<br />";
								$del=mysqli_query($GLOBALS["mysqli"], $sql);
								$nb_reg++;
							}
						}
						else {
							if(isset($tab_o_ele[$loop])) {
								if($orientation[$loop]!=$tab_o_ele[$loop]['id_orientation']) {
									$sql="UPDATE o_orientations SET id_orientation='".$orientation[$loop]."', commentaire='".mysqli_real_escape_string($mysqli, $commentaire[$loop])."', date_orientation='".$date_courante."', saisi_par='".$_SESSION['login']."' WHERE login='".$lig_ele->login."' AND rang='".$rang."';";
									//echo "$sql<br />";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									$nb_reg++;
								}
								elseif(trim($commentaire[$loop])!=$tab_o_ele[$loop]['commentaire']) {
									$sql="UPDATE o_orientations SET id_orientation='".$orientation[$loop]."', commentaire='".mysqli_real_escape_string($mysqli, trim($commentaire[$loop]))."', date_orientation='".$date_courante."', saisi_par='".$_SESSION['login']."' WHERE login='".$lig_ele->login."' AND rang='".$rang."';";
									//echo "$sql<br />";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									$nb_reg++;
								}
							}
							else {
								$sql="INSERT INTO o_orientations SET id_orientation='".$orientation[$loop]."', commentaire='".mysqli_real_escape_string($mysqli, $commentaire[$loop])."', date_orientation='".$date_courante."', login='".$lig_ele->login."', rang='".$rang."', saisi_par='".$_SESSION['login']."';";
								//echo "$sql<br />";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								$nb_reg++;
							}
							$rang++;
						}
					}
					else {
						// On ne devrait pas passer là sauf modification du nombre d'orientations autorisées pendant qu'un autre fait la saisie des orientations
						// ou ajout d'élève à la classe pendant la saisie
					}
				}

				// Dans le cas où par exemple le orientation 1 a été vidé en laissant les orientations suivants, il faut mettre à jour les rangs.
				$tab_o_ele=array();
				$sql="SELECT * FROM o_orientations WHERE login='".$lig_ele->login."' ORDER BY rang;";
				//echo "$sql<br />";
				$res_o=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_o)>0) {
					$cpt=0;
					while($lig_o=mysqli_fetch_object($res_o)) {
						$tab_o_ele[$cpt]['id']=$lig_o->id;
						$tab_o_ele[$cpt]['id_orientation']=$lig_o->id_orientation;
						$tab_o_ele[$cpt]['commentaire']=$lig_o->commentaire;
						$tab_o_ele[$cpt]['rang']=$lig_o->rang;
						$tab_o_ele[$cpt]['date_orientation']=formate_date($lig_o->date_orientation, "y");
						$cpt++;
					}
				}

				$cpt=1;
				for($loop=0;$loop<count($tab_o_ele);$loop++) {
					if($tab_o_ele[$loop]['rang']!=$cpt) {
						$sql="UPDATE o_orientations SET date_orientation='".$date_courante."', rang='$cpt', saisi_par='".$_SESSION['login']."' WHERE id='".$tab_o_ele[$loop]['id']."';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						$nb_reg++;
					}
					$cpt++;
				}


				if (isset($NON_PROTECT["avis_orientation_".$lig_ele->id_eleve])){
					$avis_orientation=suppression_sauts_de_lignes_surnumeraires($NON_PROTECT["avis_orientation_".$lig_ele->id_eleve]);
					$avis_orientation=nl2br($avis_orientation);

					$sql="SELECT * FROM o_avis WHERE login='".$lig_ele->login."';";
					//echo "$sql<br />";
					$res_o=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_o)>0) {
						$lig_o=mysqli_fetch_object($res_o);
						$avis_o_ele=$lig_o->avis;

						if($avis_orientation!=$avis_o_ele) {
							$sql="UPDATE o_avis SET avis='".mysqli_real_escape_string($mysqli, $avis_orientation)."' WHERE login='".$lig_ele->login."';";
							//echo strftime("%Y%m%d %H%M%S")."<br />";
							//echo "$sql<br />";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$update) {
								$msg.="Erreur lors de la mise à jour de l'avis pour ".$lig_ele->login.".<br />";
							}
							else {
								$nb_reg++;
							}
						}
					}
					else {
						$sql="INSERT INTO o_avis SET avis='".mysqli_real_escape_string($mysqli, $avis_orientation)."', login='".$lig_ele->login."';";
						//echo strftime("%Y%m%d %H%M%S")."<br />";
						//echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							$msg.="Erreur lors de l'enregistrement de l'avis pour ".$lig_ele->login.".<br />";
						}
						else {
							$nb_reg++;
						}
					}
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

$themessage = 'Des modifications n ont pas été validées. Voulez-vous vraiment quitter sans enregistrer ?';
//================================
$titre_page = "Saisie orientation";
require_once("../lib/header.inc.php");
//================================

//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2>Saisie de l'orientation proposée par le conseil de classe".(isset($id_classe) ? " (".get_nom_classe($id_classe).")" : "")."</h2>

<p>Ce module est destiné à saisir les voeux et orientations proposées par le conseil de classe.</p>";

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

	$tab_classe_o=array();
	$sql="SELECT DISTINCT id_classe FROM o_mef om, j_eleves_classes jec, eleves e WHERE om.affichage='y' AND e.mef_code=om.mef_code AND e.login=jec.login;";
	//echo "$sql<br />";
	$res_clas_o=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig_clas_o=mysqli_fetch_object($res_clas_o)) {
		$tab_classe_o[]=$lig_clas_o->id_classe;
	}

	if(mysqli_num_rows($res_clas)==1) {
		$lig_clas=mysqli_fetch_object($res_clas);
		$id_classe=$lig_clas->id;
		if(!in_array($id_classe, $tab_classe_o)) {
			echo "<p style=color:red'>Aucune classe avec MEF associé à un niveau d'orientation ne vous est associée.</p>";
			require("../lib/footer.inc.php");
			die();
		}
	}
	else {
		$tab_txt=array();
		$tab_lien=array();
		while($lig_clas=mysqli_fetch_object($res_clas)) {
			if(in_array($lig_clas->id, $tab_classe_o)) {
				$tab_lien[] = $_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id;
				$tab_txt[] = $lig_clas->classe;
			}
		}

		if(count($tab_lien)==0) {
			echo "<p style=color:red'>Aucune classe avec MEF associé à un niveau d'orientation ne vous est associée.</p>";
			require("../lib/footer.inc.php");
			die();
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

if(!mef_avec_proposition_orientation($id_classe)) {
	echo "<p style=color:red'>La classe de '<strong>".get_nom_classe($id_classe)."</strong>' n'est pas associée à des MEFs d'un niveau d'orientation.</p>";
	require("../lib/footer.inc.php");
	die();
}

$tab_orientation_classe_courante=get_tab_voeux_orientations_classe($id_classe);
/*
echo "<pre>";
print_r($tab_orientation_classe_courante);
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
					<th title=\"Copier les voeux vers les choix d'orientation de même rang\"><img src='../images/icons/copy.png' class='icone16' alt='Copier' /></th>
					<th>Orientation proposée/conseillée</th>
				</tr>
			</thead>
			<tbody>";

while($lig_ele=mysqli_fetch_object($res_ele)) {
	$chaine_copie_voeu="";

	$tab_voeux_ele=array();
	$tab_o_ele=array();
	$avis_o_ele="";

	if(isset($tab_orientation_classe_courante['voeux'][$lig_ele->login])) {
		$tab_voeux_ele=$tab_orientation_classe_courante['voeux'][$lig_ele->login];
	}

	if(isset($tab_orientation_classe_courante['orientation_proposee'][$lig_ele->login])) {
		$tab_o_ele=$tab_orientation_classe_courante['orientation_proposee'][$lig_ele->login];
	}

	if(isset($tab_orientation_classe_courante['avis'][$lig_ele->login])) {
		$avis_o_ele=$tab_orientation_classe_courante['avis'][$lig_ele->login];
	}

	echo "
				<tr class='white_hover'>
					<td><a href='../eleves/visu_eleve.php?ele_login=".$lig_ele->login."' target='_blank' title=\"Voir le classeur/dossier élève dans un nouvel onglet.\"><img src='../images/icons/ele_onglets.png' class='icone16' alt='Onglets' /></a></td>
					<td>".$lig_ele->nom." ".$lig_ele->prenom."</td>
					<td style='text-align:left; vertical-align:top;'>";
	//++++++
	// VOEUX
	//++++++
	for($loop=1;$loop<=$OrientationNbMaxVoeux;$loop++) {
		$commentaire="";
		if(isset($tab_voeux_ele[$loop])) {
			$commentaire=trim(preg_replace('/"/', " ", $tab_voeux_ele[$loop]['commentaire']));

			$temoin_copie_possible="n";
			echo "
						<p style='text-indent:-5em; margin-left:5em;'><strong>Voeu ".($loop)."&nbsp;:</strong> ";
			if((($tab_voeux_ele[$loop]=="")||($tab_voeux_ele[$loop]['id_orientation']==0))&&($commentaire=="")) {
				echo "<span style='color:red'>???</span>";
			}
			elseif((($tab_voeux_ele[$loop]=="")||($tab_voeux_ele[$loop]['id_orientation']==0))) {
				echo "Autre orientation&nbsp: \"<span title=\"Commentaire saisi pour ce voeu.\" id='commentaire_voeu_".$lig_ele->id_eleve."_".$loop."'>".htmlentities($commentaire)."</span>\"";

				echo "<span id='id_voeu_orientation_".$lig_ele->id_eleve."_".$loop."' style='display:none'>0</span>";

				$temoin_copie_possible="y";
			}
			else {
				if(isset($tab_orientation[$lig_ele->mef_code])) {
					$orientation_trouvee="n";
					for($loop2=0;$loop2<count($tab_orientation[$lig_ele->mef_code]['id_orientation']);$loop2++) {
						if((isset($tab_voeux_ele[$loop]))&&($tab_voeux_ele[$loop]['id_orientation']==$tab_orientation[$lig_ele->mef_code]['id_orientation'][$loop2])) {
							$orientation_trouvee="y";
							break;
						}
					}
					if($orientation_trouvee=="y") {
						echo "<span id='id_voeu_orientation_".$lig_ele->id_eleve."_".$loop."' style='display:none'>".$tab_orientation[$lig_ele->mef_code]['id_orientation'][$loop2]."</span>
						".$tab_orientation[$lig_ele->mef_code]['titre'][$loop2]." 
						<em style='font-size:x-small'>(".$tab_orientation[$lig_ele->mef_code]['description'][$loop2].")</em>";
						$temoin_copie_possible="y";
					}
					else {
						if(isset($tab_orientation2[$tab_voeux_ele[$loop]['id_orientation']])) {
							echo "<span id='id_voeu_orientation_".$lig_ele->id_eleve."_".$loop."' style='display:none'>".$tab_orientation2[$tab_voeux_ele[$loop]['id_orientation']]."</span>
							<span style='color:red' title=\"Orientation non associée dans la base au MEF de l'élève.\">".$tab_orientation2[$tab_voeux_ele[$loop]['id_orientation']]['titre']."</span> <em style='font-size:x-small'>(".$tab_orientation2[$tab_voeux_ele[$loop]['id_orientation']]['description'].")</em>";
							$temoin_copie_possible="y";
						}
						else {
							echo "Orientation n°".$tab_voeux_ele[$loop]['id_orientation']." non trouvée dans la base <em style='color:red' title=\"La base des types d'orientations a dû être modifiée.\">(anomalie)</em>";
						}
					}
					if($commentaire!="") {
						echo "<br />\"<span title=\"Commentaire saisi pour ce voeu.\" id='commentaire_voeu_".$lig_ele->id_eleve."_".$loop."'>".htmlentities($commentaire)."</span>\"";
						$temoin_copie_possible="y";
					}
				}
				else {
					if(isset($tab_orientation2[$tab_voeux_ele[$loop]['id_orientation']])) {
						echo "<span id='id_voeu_orientation_".$lig_ele->id_eleve."_".$loop."' style='display:none'>".$tab_orientation2[$tab_voeux_ele[$loop]['id_orientation']]."</span>
						<span style='color:red' title=\"Orientation non associée dans la base au MEF de l'élève.\">".$tab_orientation2[$tab_voeux_ele[$loop]['id_orientation']]['titre']."</span> <em style='font-size:x-small'>(".$tab_orientation2[$tab_voeux_ele[$loop]['id_orientation']]['description'].")</em>";
						$temoin_copie_possible="y";
					}
					else {
						echo "Orientation n°".$tab_voeux_ele[$loop]['id_orientation']." non trouvée dans la base <em style='color:red' title=\"La base des types d'orientations a dû être modifiée.\">(anomalie)</em>";
					}
					if($commentaire!="") {
						echo "<br />\"<span title=\"Commentaire saisi pour ce voeu.\" id='commentaire_voeu_".$lig_ele->id_eleve."_".$loop."'>".htmlentities($commentaire)."</span>\"";
						$temoin_copie_possible="y";
					}
				}
			}
			if(($loop<=$OrientationNbMaxOrientation)&&($temoin_copie_possible=="y")) {
				echo " <a href='#' onclick=\"copie_voeu_vers_orientation('".$lig_ele->id_eleve."_".$loop."'); return false;\" title=\"Copier ce voeu vers le choix d'orientation n°$loop de l'élève.\"><img src='../images/icons/forward.png' class='icone16' alt='Copier' /></a>";
				$chaine_copie_voeu.="copie_voeu_vers_orientation('".$lig_ele->id_eleve."_".$loop."'); ";
			}
			echo "<br />";
		}
	}
	echo "
					</td>
					<td>";
						if($chaine_copie_voeu!="") {
							echo "
						 <a href='#' onclick=\"".$chaine_copie_voeu." return false;\" title=\"Copier les voeux vers les choix d'orientation de même rang pour cet élève.\"><img src='../images/icons/forward.png' class='icone16' alt='Copier' /></a>";
						}
	echo "
					</td>
					<td style='text-align:left; vertical-align:top;'>";
	//++++++++++++
	// ORIENTATION
	//++++++++++++
	for($loop=1;$loop<=$OrientationNbMaxOrientation;$loop++) {

		$commentaire="";
		$selected_aucun="";
		if(!isset($tab_o_ele[$loop])) {
			$selected_aucun=" selected";
		}
		else {
			$commentaire=preg_replace('/"/', " ", $tab_o_ele[$loop]['commentaire']);
		}
		echo "
						Choix ".($loop)."
						<select name='orientation_".$lig_ele->id_eleve."[]' id='orientation_".$lig_ele->id_eleve."_".$loop."' onchange=\"changement();\">
							<option value='' title=\"Choisissez une orientation.\nSi l'orientation souhaitée n'est pas dans la liste proposée, choisissez 'Autre orientation' et précisez en commentaire l'orientation.\"".$selected_aucun.">---</option>";
		if(isset($tab_orientation[$lig_ele->mef_code])) {
			for($loop2=0;$loop2<count($tab_orientation[$lig_ele->mef_code]['id_orientation']);$loop2++) {
				$selected="";
				if((isset($tab_o_ele[$loop]))&&($tab_o_ele[$loop]['id_orientation']==$tab_orientation[$lig_ele->mef_code]['id_orientation'][$loop2])) {
					$selected=" selected";
				}
				echo "
							<option value='".$tab_orientation[$lig_ele->mef_code]['id_orientation'][$loop2]."' title=\"".preg_replace('/"/', " ", $tab_orientation[$lig_ele->mef_code]['description'][$loop2])."\"".$selected.">".$tab_orientation[$lig_ele->mef_code]['titre'][$loop2]."</option>";
			}
		}
		$selected="";
		if((isset($tab_o_ele[$loop]))&&($tab_o_ele[$loop]['id_orientation']=="0")) {
			$selected=" selected";
		}
		echo "
							<option value='0' title=\"Si l'orientation souhaitée n'est pas dans la liste proposée, choisissez 'Autre orientation' et précisez en commentaire l'orientation.\"".$selected.">Autre orientation</option>
						</select>
						<input type='text' name='commentaire_".$lig_ele->id_eleve."[]'  id='commentaire_".$lig_ele->id_eleve."_".$loop."'value=\"".$commentaire."\" size='30' onchange=\"changement();\" /><br />";

	}

	echo "<label for='no_anti_inject_avis_orientation_".$lig_ele->id_eleve."' style='vertical-align:top'><b title=\"Avis sur l'orientation proposée\">Avis&nbsp;</b> </label><textarea name='no_anti_inject_avis_orientation_".$lig_ele->id_eleve."' id='no_anti_inject_avis_orientation_".$lig_ele->id_eleve."' rows='5' cols='60' class='wrap' onchange=\"changement()\">";
	if(isset($tab_orientation_classe_courante['avis'][$lig_ele->login])) {
		echo preg_replace("#<br />#i", "", $tab_orientation_classe_courante['avis'][$lig_ele->login]);
	}
	echo "</textarea>\n";

	echo "
					</td>
				</tr>";
}
echo "
			</tbody>
		</table>
		<input type='hidden' name='enregistrer_orientation' value='y' />
		<p style='text-align:center;'><input type='submit' value='Valider' /></p>
	</fieldset>
</form>

<script type='text/javascript'>
	var change='no';

	function copie_voeu_vers_orientation(suffixe) {
		if(document.getElementById('id_voeu_orientation_'+suffixe)) {
			if(document.getElementById('id_voeu_orientation_'+suffixe).innerHTML!='') {
				if(document.getElementById('orientation_'+suffixe)) {
					// Copie de l'option dans le champ SELECT
					for(i=0;i<document.getElementById('orientation_'+suffixe).options.length;i++) {
						if(document.getElementById('orientation_'+suffixe).options[i].value==document.getElementById('id_voeu_orientation_'+suffixe).innerHTML) {
							document.getElementById('orientation_'+suffixe).selectedIndex=i;
							changement();
							break;
						}
					}

					// Copie du commentaire
					if(document.getElementById('commentaire_voeu_'+suffixe)) {
						if(document.getElementById('commentaire_voeu_'+suffixe).innerHTML!='') {
							if(document.getElementById('commentaire_'+suffixe)) {
								document.getElementById('commentaire_'+suffixe).value=document.getElementById('commentaire_voeu_'+suffixe).innerHTML;
								changement();
							}
						}
					}
				}
			}
		}
	}
</script>
<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
