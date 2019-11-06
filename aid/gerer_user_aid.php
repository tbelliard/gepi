<?php
/*
 * Copyright 2001, 2019 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Régis Bouguin, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/aid/gerer_user_aid.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/aid/gerer_user_aid.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Gérer les professeurs, gestionnaires,... d AID',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg='';

$indice_aid=isset($_POST['indice_aid']) ? $_POST['indice_aid'] : (isset($_GET['indice_aid']) ? $_GET['indice_aid'] : NULL);
//$indice_aid=isset($_POST['indice_aid']) ? $_POST['indice_aid'] : (isset($_GET['indice_aid']) ? $_GET['indice_aid'] : NULL);

if((isset($_GET['indice_aid']))&&(preg_match('/^[0-9]{1,}$/', $_GET['indice_aid']))&&(isset($_GET['supprime_super_gestionnaire']))) {
	check_token();

	//new Ajax.Updater($(span_id),'gerer_user_aid?indice_aid='+indice_aid+'&supprime_super_gestionnaire='+login+'".add_token_in_url(false)."',{method: 'get'});

	$info_cat_aid=get_info_categorie_aid2($indice_aid);
	if($info_cat_aid=='') {
		$msg.="La catégorie n°$indice_aid n'existe pas.<br />";
	}
	else {
		$login_user=trim($_GET['supprime_super_gestionnaire']);
		if($login_user=='') {
			echo "<span style='color:red'>Erreur login vide</span>";
		}
		else {
			$sql="SELECT 1=1 FROM j_aidcateg_super_gestionnaires WHERE indice_aid='".$_GET['indice_aid']."' AND id_utilisateur='".mysqli_real_escape_string($mysqli, $login_user)."';";
			$test=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test)==0) {
				echo "<span style='color:red'>Erreur '".civ_nom_prenom($login_user)."' n'est pas super-gestionnaire de la catégorie AID $info_cat_aid</span>";
			}
			else {
				$sql="DELETE FROM j_aidcateg_super_gestionnaires WHERE indice_aid='".$_GET['indice_aid']."' AND id_utilisateur='".mysqli_real_escape_string($mysqli, $login_user)."';";
				$del=mysqli_query($mysqli, $sql);
				if(!$del) {
					echo "<span style='color:red'>Erreur lors de la suppression de '".civ_nom_prenom($login_user)."' comme super-gestionnaire de la catégorie AID $info_cat_aid</span>";
				}
				else {
					echo "<span style='color:green'>'".civ_nom_prenom($login_user)."' supprimé</span>";
				}
			}
		}
	}

	die();
}



if((isset($_GET['indice_aid']))&&(preg_match('/^[0-9]{1,}$/', $_GET['indice_aid']))&&(isset($_GET['maj_liste_super_gestionnaire_dans_form']))) {
	check_token();

	//new Ajax.Updater($('div_liste_super_gest_ajoutables'),'gerer_user_aid.php?indice_aid='+indice_aid+'&maj_liste_super_gestionnaire_dans_form=y".add_token_in_url(false)."',{method: 'get'});

	$indice_aid=$_GET['indice_aid'];
	$info_cat_aid=get_info_categorie_aid2($indice_aid);
	if($info_cat_aid=='') {
		echo "<p style='color:red'>La catégorie AID n°$indice_aid n'existe pas.</p>";
	}
	else {
		echo "<p>Ajouter les utilisateurs suivants comme super-gestionnaires de ".$info_cat_aid."&nbsp;:
			<img src='../images/icons/ico_aide.png' class='icone16' title=\"Pour sélectionner plusieurs utilisateurs, effectuer Ctrl+Clic dans le champ SELECT ci-dessous.\" />
		</p>";

		$sql="SELECT u.* FROM utilisateurs u WHERE (statut='professeur' OR statut='scolarite' or statut='cpe') AND login NOT IN (SELECT id_utilisateur FROM j_aidcateg_super_gestionnaires WHERE indice_aid='".$_GET['indice_aid']."') ORDER BY statut, nom, prenom;";
		//echo "$sql<br />";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<span style='color:red'>Aucun personnel n'est disponible</span>";
		}
		else {
			$optgroup_prec='';
			echo "<select name='login_user[]' id='login_ajout_super_gest' size='20' multiple>";
			while($lig=mysqli_fetch_object($res)) {
				if($lig->statut!=$optgroup_prec) {
					if($optgroup_prec!='') {
						echo "</optgroup>";
					}
					echo "<optgroup label='".$lig->statut."'>";
				}
				echo "<option value=\"".$lig->login."\">".$lig->civilite." ".casse_mot($lig->nom, 'maj')." ".casse_mot($lig->prenom, 'majf2')."</option>";
				$optgroup_prec=$lig->statut;
			}
			echo "</optgroup>";
			echo "</select>";
		}
	}

	die();
}

if((isset($_POST['indice_aid']))&&
(preg_match('/^[0-9]{1,}$/', $_POST['indice_aid']))&&
(isset($_POST['valider_ajout_super_gest']))&&
(isset($_POST['login_user']))) {
	check_token();

	$indice_aid=$_POST['indice_aid'];
	$login_user=$_POST['login_user'];

	$info_cat_aid=get_info_categorie_aid2($indice_aid);
	if($info_cat_aid=='') {
		$msg.="La catégorie n°$indice_aid n'existe pas.<br />";
	}
	else {
		//echo "plop<br />";
		foreach($login_user as $key => $current_login) {
			$sql="SELECT 1=1 FROM utilisateurs u WHERE (statut='professeur' OR statut='scolarite' or statut='cpe') AND login='".mysqli_real_escape_string($mysqli, $current_login)."';";
			//echo "$sql<br />";
			$test=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test)==0) {
				$msg.=$current_login." n'est pas professeur, cpe ou compte scolarité.<br />";
			}
			else {
				$sql="SELECT 1=1 FROM j_aidcateg_super_gestionnaires WHERE indice_aid='".$indice_aid."' AND id_utilisateur='".mysqli_real_escape_string($mysqli, $current_login)."';";
				//echo "$sql<br />";
				$test=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($test)>0) {
					$msg.=civ_nom_prenom($current_login)." est déjà super-gestionnaire de la catégorie <a href='#cat_aid_".$indice_aid."'>$info_cat_aid</a>.<br />";
				}
				else {
					$sql="INSERT INTO j_aidcateg_super_gestionnaires SET indice_aid='".$indice_aid."', id_utilisateur='".mysqli_real_escape_string($mysqli, $current_login)."';";
					//echo "$sql<br />";
					$insert=mysqli_query($mysqli, $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'ajout de ".civ_nom_prenom($current_login)." comme super-gestionnaire de la catégorie <a href='#cat_aid_".$indice_aid."'>$info_cat_aid</a>.<br />";
					}
					else {
						$msg.="<span style='color:green'>".civ_nom_prenom($current_login)." ajouté comme super-gestionnaire de la catégorie <a href='#cat_aid_".$indice_aid."'>$info_cat_aid</a>.</span><br />";
					}
				}
			}
		}
	}
}

//==============================================================================

if((isset($_GET['indice_aid']))&&(preg_match('/^[0-9]{1,}$/', $_GET['indice_aid']))&&(isset($_GET['supprime_aid_c_u']))) {
	check_token();

	$info_cat_aid=get_info_categorie_aid2($indice_aid);
	if($info_cat_aid=='') {
		$msg.="La catégorie n°$indice_aid n'existe pas.<br />";
	}
	else {
		$login_user=trim($_GET['supprime_aid_c_u']);
		if($login_user=='') {
			echo "<span style='color:red'>Erreur login vide</span>";
		}
		else {
			$sql="SELECT 1=1 FROM j_aidcateg_utilisateurs WHERE indice_aid='".$_GET['indice_aid']."' AND id_utilisateur='".mysqli_real_escape_string($mysqli, $login_user)."';";
			$test=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test)==0) {
				echo "<span style='color:red'>Erreur '".civ_nom_prenom($login_user)."' n'est pas gestionnaire des fiches projet de la catégorie AID $info_cat_aid</span>";
			}
			else {
				$sql="DELETE FROM j_aidcateg_utilisateurs WHERE indice_aid='".$_GET['indice_aid']."' AND id_utilisateur='".mysqli_real_escape_string($mysqli, $login_user)."';";
				$del=mysqli_query($mysqli, $sql);
				if(!$del) {
					echo "<span style='color:red'>Erreur lors de la suppression de '".civ_nom_prenom($login_user)."' comme gestionnaire des fiches projet de la catégorie AID $info_cat_aid</span>";
				}
				else {
					echo "<span style='color:green'>'".civ_nom_prenom($login_user)."' supprimé</span>";
				}
			}
		}
	}

	die();
}



if((isset($_GET['indice_aid']))&&(preg_match('/^[0-9]{1,}$/', $_GET['indice_aid']))&&(isset($_GET['maj_liste_aid_c_u_dans_form']))) {
	check_token();

	$indice_aid=$_GET['indice_aid'];
	$info_cat_aid=get_info_categorie_aid2($indice_aid);
	if($info_cat_aid=='') {
		$msg.="La catégorie n°$indice_aid n'existe pas.<br />";
	}
	else {
		echo "<p>Ajouter les utilisateurs suivants comme gestionnaires des fiches projet de ".$info_cat_aid."&nbsp;:
			<img src='../images/icons/ico_aide.png' class='icone16' title=\"Pour sélectionner plusieurs utilisateurs, effectuer Ctrl+Clic dans le champ SELECT ci-dessous.\" />
		</p>";

		// Les comptes scolarité n'ont pas accès aux fiches projet /aid/index_fiches.php
		//$sql="SELECT u.* FROM utilisateurs u WHERE (statut='professeur' OR statut='scolarite' or statut='cpe') AND login NOT IN (SELECT id_utilisateur FROM j_aidcateg_utilisateurs WHERE indice_aid='".$_GET['indice_aid']."') ORDER BY statut, nom, prenom;";
		$sql="SELECT u.* FROM utilisateurs u WHERE (statut='professeur' or statut='cpe') AND login NOT IN (SELECT id_utilisateur FROM j_aidcateg_utilisateurs WHERE indice_aid='".$_GET['indice_aid']."') ORDER BY statut, nom, prenom;";
		//echo "$sql<br />";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<span style='color:red'>Aucun personnel n'est disponible</span>";
		}
		else {
			$optgroup_prec='';
			echo "<select name='login_user[]' id='login_ajout_aid_c_u' size='20' multiple>";
			while($lig=mysqli_fetch_object($res)) {
				if($lig->statut!=$optgroup_prec) {
					if($optgroup_prec!='') {
						echo "</optgroup>";
					}
					echo "<optgroup label='".$lig->statut."'>";
				}
				echo "<option value=\"".$lig->login."\">".$lig->civilite." ".casse_mot($lig->nom, 'maj')." ".casse_mot($lig->prenom, 'majf2')."</option>";
				$optgroup_prec=$lig->statut;
			}
			echo "</optgroup>";
			echo "</select>";
		}
	}

	die();
}

if((isset($_POST['indice_aid']))&&
(preg_match('/^[0-9]{1,}$/', $_POST['indice_aid']))&&
(isset($_POST['valider_ajout_aid_c_u']))&&
(isset($_POST['login_user']))) {
	check_token();

	$indice_aid=$_POST['indice_aid'];
	$login_user=$_POST['login_user'];

	$info_cat_aid=get_info_categorie_aid2($indice_aid);
	if($info_cat_aid=='') {
		$msg.="La catégorie n°$indice_aid n'existe pas.<br />";
	}
	else {
		//echo "plop<br />";
		foreach($login_user as $key => $current_login) {
			$sql="SELECT 1=1 FROM utilisateurs u WHERE (statut='professeur' OR statut='scolarite' or statut='cpe') AND login='".mysqli_real_escape_string($mysqli, $current_login)."';";
			//echo "$sql<br />";
			$test=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test)==0) {
				$msg.=$current_login." n'est pas professeur, cpe ou compte scolarité.<br />";
			}
			else {
				$sql="SELECT 1=1 FROM j_aidcateg_utilisateurs WHERE indice_aid='".$indice_aid."' AND id_utilisateur='".mysqli_real_escape_string($mysqli, $current_login)."';";
				//echo "$sql<br />";
				$test=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($test)>0) {
					$msg.=civ_nom_prenom($current_login)." est déjà gestionnaire des fiches projet de la catégorie <a href='#cat_aid_".$indice_aid."'>$info_cat_aid</a>.<br />";
				}
				else {
					$sql="INSERT INTO j_aidcateg_utilisateurs SET indice_aid='".$indice_aid."', id_utilisateur='".mysqli_real_escape_string($mysqli, $current_login)."';";
					//echo "$sql<br />";
					$insert=mysqli_query($mysqli, $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'ajout de ".civ_nom_prenom($current_login)." comme gestionnaire des fiches projet de la catégorie <a href='#cat_aid_".$indice_aid."'>$info_cat_aid</a>.<br />";
					}
					else {
						$msg.="<span style='color:green'>".civ_nom_prenom($current_login)." ajouté comme gestionnaire des fiches projet de la catégorie <a href='#cat_aid_".$indice_aid."'>$info_cat_aid</a>.</span><br />";
					}
				}
			}
		}
	}
}

//==============================================================================

if((isset($_GET['indice_aid']))&&(preg_match('/^[0-9]{1,}$/', $_GET['indice_aid']))&&(isset($_GET['id_aid']))&&(preg_match('/^[0-9]{1,}$/', $_GET['id_aid']))&&(isset($_GET['supprime_gestionnaire']))) {
	check_token();

	$id_aid=$_GET['id_aid'];
	$info_aid=get_info_aid($id_aid);
	if($info_aid=='') {
		$msg.="L'AID n°$id_aid n'existe pas.<br />";
	}
	else {
		$login_user=trim($_GET['supprime_gestionnaire']);
		if($login_user=='') {
			echo "<span style='color:red'>Erreur login vide</span>";
		}
		else {
			$sql="SELECT 1=1 FROM j_aid_utilisateurs_gest WHERE indice_aid='".$_GET['indice_aid']."' AND id_aid='".$_GET['id_aid']."' AND id_utilisateur='".mysqli_real_escape_string($mysqli, $login_user)."';";
			//echo "$sql<br />";
			$test=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test)==0) {
				echo "<span style='color:red'>Erreur '".civ_nom_prenom($login_user)."' n'est pas gestionnaire de l'AID $info_aid</span>";
			}
			else {
				$sql="DELETE FROM j_aid_utilisateurs_gest WHERE indice_aid='".$_GET['indice_aid']."' AND id_aid='".$_GET['id_aid']."' AND id_utilisateur='".mysqli_real_escape_string($mysqli, $login_user)."';";
				//echo "$sql<br />";
				$del=mysqli_query($mysqli, $sql);
				if(!$del) {
					echo "<span style='color:red'>Erreur lors de la suppression de '".civ_nom_prenom($login_user)."' comme gestionnaire de l'AID $info_aid</span>";
				}
				else {
					echo "<span style='color:green'>'".civ_nom_prenom($login_user)."' supprimé</span>";
				}
			}
		}
	}
	die();
}



if((isset($_GET['indice_aid']))&&(preg_match('/^[0-9]{1,}$/', $_GET['indice_aid']))&&(isset($_GET['id_aid']))&&(preg_match('/^[0-9]{1,}$/', $_GET['id_aid']))&&(isset($_GET['maj_liste_gestionnaire_dans_form']))) {
	check_token();

	$id_aid=$_GET['id_aid'];
	$info_aid=get_info_aid($id_aid);
	if($info_aid=='') {
		echo "<p style='color:red'>L'AID n°$id_aid n'existe pas.</p>";
	}
	else {
		echo "<p>Ajouter les utilisateurs suivants comme gestionnaires de ".$info_aid."&nbsp;:
			<img src='../images/icons/ico_aide.png' class='icone16' title=\"Pour sélectionner plusieurs utilisateurs, effectuer Ctrl+Clic dans le champ SELECT ci-dessous.\" />
		</p>";

		$sql="SELECT u.* FROM utilisateurs u WHERE (statut='professeur' OR statut='scolarite' or statut='cpe') AND login NOT IN (SELECT id_utilisateur FROM j_aid_utilisateurs_gest WHERE indice_aid='".$_GET['indice_aid']."' AND id_aid='".$_GET['id_aid']."') ORDER BY statut, nom, prenom;";
		//echo "$sql<br />";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<span style='color:red'>Aucun personnel n'est disponible</span>";
		}
		else {
			$optgroup_prec='';
			echo "<select name='login_user[]' id='login_ajout_gest' size='20' multiple>";
			while($lig=mysqli_fetch_object($res)) {
				if($lig->statut!=$optgroup_prec) {
					if($optgroup_prec!='') {
						echo "</optgroup>";
					}
					echo "<optgroup label='".$lig->statut."'>";
				}
				echo "<option value=\"".$lig->login."\">".$lig->civilite." ".casse_mot($lig->nom, 'maj')." ".casse_mot($lig->prenom, 'majf2')."</option>";
				$optgroup_prec=$lig->statut;
			}
			echo "</optgroup>";
			echo "</select>";
		}
	}

	die();
}

if((isset($_POST['indice_aid']))&&
(preg_match('/^[0-9]{1,}$/', $_POST['indice_aid']))&&
(isset($_POST['id_aid']))&&
(preg_match('/^[0-9]{1,}$/', $_POST['id_aid']))&&
(isset($_POST['valider_ajout_gest']))&&
(isset($_POST['login_user']))) {
	check_token();

	$indice_aid=$_POST['indice_aid'];
	$id_aid=$_POST['id_aid'];
	$login_user=$_POST['login_user'];

	$info_cat_aid=get_info_categorie_aid2($indice_aid);
	$info_aid=get_info_aid($id_aid);
	if($info_cat_aid=='') {
		$msg.="La catégorie n°$indice_aid n'existe pas.<br />";
	}
	elseif($info_aid=='') {
		$msg.="L'AID n°$id_aid n'existe pas.<br />";
	}
	else {
		foreach($login_user as $key => $current_login) {
			$sql="SELECT 1=1 FROM utilisateurs u WHERE (statut='professeur' OR statut='scolarite' or statut='cpe') AND login='".mysqli_real_escape_string($mysqli, $current_login)."';";
			//echo "$sql<br />";
			$test=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test)==0) {
				$msg.=$current_login." n'est pas professeur, cpe ou compte scolarité.<br />";
			}
			else {
				$sql="SELECT 1=1 FROM j_aid_utilisateurs_gest WHERE indice_aid='".$indice_aid."' AND id_aid='".$id_aid."' AND id_utilisateur='".mysqli_real_escape_string($mysqli, $current_login)."';";
				//echo "$sql<br />";
				$test=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($test)>0) {
					$msg.=civ_nom_prenom($current_login)." est déjà gestionnaire de l'AID <a href='#aid_".$id_aid."'>$info_aid</a>.<br />";
				}
				else {
					$sql="INSERT INTO j_aid_utilisateurs_gest SET indice_aid='".$indice_aid."', id_aid='".$id_aid."', id_utilisateur='".mysqli_real_escape_string($mysqli, $current_login)."';";
					//echo "$sql<br />";
					$insert=mysqli_query($mysqli, $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'ajout de ".civ_nom_prenom($current_login)." comme gestionnaire de l'AID <a href='#aid_".$id_aid."'>$info_aid</a>.<br />";
					}
					else {
						$msg.="<span style='color:green'>".civ_nom_prenom($current_login)." ajouté comme gestionnaire de l'AID <a href='#aid_".$id_aid."'>$info_aid</a>.</span><br />";
					}
				}
			}
		}
	}
}

//==============================================================================

if((isset($_GET['indice_aid']))&&(preg_match('/^[0-9]{1,}$/', $_GET['indice_aid']))&&(isset($_GET['id_aid']))&&(preg_match('/^[0-9]{1,}$/', $_GET['id_aid']))&&(isset($_GET['supprime_aid_u']))) {
	check_token();

	$id_aid=$_GET['id_aid'];
	$info_aid=get_info_aid($id_aid);
	if($info_aid=='') {
		$msg.="L'AID n°$id_aid n'existe pas.<br />";
	}
	else {
		$login_user=trim($_GET['supprime_aid_u']);
		if($login_user=='') {
			echo "<span style='color:red'>Erreur login vide</span>";
		}
		else {
			$sql="SELECT 1=1 FROM j_aid_utilisateurs WHERE indice_aid='".$_GET['indice_aid']."' AND id_aid='".$_GET['id_aid']."' AND id_utilisateur='".mysqli_real_escape_string($mysqli, $login_user)."';";
			//echo "$sql<br />";
			$test=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test)==0) {
				echo "<span style='color:red'>Erreur '".civ_nom_prenom($login_user)."' n'est pas professeur de l'AID $info_aid</span>";
			}
			else {
				$sql="DELETE FROM j_aid_utilisateurs WHERE indice_aid='".$_GET['indice_aid']."' AND id_aid='".$_GET['id_aid']."' AND id_utilisateur='".mysqli_real_escape_string($mysqli, $login_user)."';";
				//echo "$sql<br />";
				$del=mysqli_query($mysqli, $sql);
				if(!$del) {
					echo "<span style='color:red'>Erreur lors de la suppression de '".civ_nom_prenom($login_user)."' comme professeur de l'AID $info_aid</span>";
				}
				else {
					echo "<span style='color:green'>'".civ_nom_prenom($login_user)."' supprimé</span>";
				}
			}
		}
	}
	die();
}



if((isset($_GET['indice_aid']))&&(preg_match('/^[0-9]{1,}$/', $_GET['indice_aid']))&&(isset($_GET['id_aid']))&&(preg_match('/^[0-9]{1,}$/', $_GET['id_aid']))&&(isset($_GET['maj_liste_aid_u_dans_form']))) {
	check_token();

	$id_aid=$_GET['id_aid'];
	$info_aid=get_info_aid($id_aid);
	if($info_aid=='') {
		echo "<p style='color:red'>L'AID n°$id_aid n'existe pas.</p>";
	}
	else {
		echo "<p>Ajouter les utilisateurs suivants comme utilisateurs/professeurs de ".$info_aid."&nbsp;:
			<img src='../images/icons/ico_aide.png' class='icone16' title=\"Pour sélectionner plusieurs utilisateurs, effectuer Ctrl+Clic dans le champ SELECT ci-dessous.\" />
		</p>";

		$sql="SELECT u.* FROM utilisateurs u WHERE (statut='professeur' OR statut='scolarite' or statut='cpe') AND login NOT IN (SELECT id_utilisateur FROM j_aid_utilisateurs WHERE indice_aid='".$_GET['indice_aid']."' AND id_aid='".$_GET['id_aid']."') ORDER BY statut, nom, prenom;";
		//echo "$sql<br />";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<span style='color:red'>Aucun personnel n'est disponible</span>";
		}
		else {
			$optgroup_prec='';
			echo "<select name='login_user[]' id='login_ajout_gest' size='20' multiple>";
			while($lig=mysqli_fetch_object($res)) {
				if($lig->statut!=$optgroup_prec) {
					if($optgroup_prec!='') {
						echo "</optgroup>";
					}
					echo "<optgroup label='".$lig->statut."'>";
				}
				echo "<option value=\"".$lig->login."\">".$lig->civilite." ".casse_mot($lig->nom, 'maj')." ".casse_mot($lig->prenom, 'majf2')."</option>";
				$optgroup_prec=$lig->statut;
			}
			echo "</optgroup>";
			echo "</select>";
		}
	}

	die();
}

if((isset($_POST['indice_aid']))&&
(preg_match('/^[0-9]{1,}$/', $_POST['indice_aid']))&&
(isset($_POST['id_aid']))&&
(preg_match('/^[0-9]{1,}$/', $_POST['id_aid']))&&
(isset($_POST['valider_ajout_aid_u']))&&
(isset($_POST['login_user']))) {
	check_token();

	$indice_aid=$_POST['indice_aid'];
	$id_aid=$_POST['id_aid'];
	$login_user=$_POST['login_user'];

	$info_cat_aid=get_info_categorie_aid2($indice_aid);
	$info_aid=get_info_aid($id_aid);
	if($info_cat_aid=='') {
		$msg.="La catégorie n°$indice_aid n'existe pas.<br />";
	}
	elseif($info_aid=='') {
		$msg.="L'AID n°$id_aid n'existe pas.<br />";
	}
	else {
		foreach($login_user as $key => $current_login) {
			$sql="SELECT 1=1 FROM utilisateurs u WHERE (statut='professeur' OR statut='scolarite' or statut='cpe') AND login='".mysqli_real_escape_string($mysqli, $current_login)."';";
			//echo "$sql<br />";
			$test=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test)==0) {
				$msg.=$current_login." n'est pas professeur, cpe ou compte scolarité.<br />";
			}
			else {
				$sql="SELECT 1=1 FROM j_aid_utilisateurs WHERE indice_aid='".$indice_aid."' AND id_aid='".$id_aid."' AND id_utilisateur='".mysqli_real_escape_string($mysqli, $current_login)."';";
				//echo "$sql<br />";
				$test=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($test)>0) {
					$msg.=civ_nom_prenom($current_login)." est déjà professeur de l'AID <a href='#aid_".$id_aid."'>$info_aid</a>.<br />";
				}
				else {
					$sql="INSERT INTO j_aid_utilisateurs SET indice_aid='".$indice_aid."', id_aid='".$id_aid."', id_utilisateur='".mysqli_real_escape_string($mysqli, $current_login)."';";
					//echo "$sql<br />";
					$insert=mysqli_query($mysqli, $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'ajout de ".civ_nom_prenom($current_login)." comme professeur de l'AID <a href='#aid_".$id_aid."'>$info_aid</a>.<br />";
					}
					else {
						$msg.="<span style='color:green'>".civ_nom_prenom($current_login)." ajouté comme professeur de l'AID <a href='#aid_".$id_aid."'>$info_aid</a>.</span><br />";
					}
				}
			}
		}
	}
}


if((isset($_POST['valider_saisie_tel_user']))&&
(isset($_POST['login_user']))&&
(count($_POST['login_user'])>0)) {
	check_token();

	$super_gest=isset($_POST['super_gest']) ? $_POST['super_gest'] : array();
	$c_u=isset($_POST['c_u']) ? $_POST['c_u'] : array();
	$u=isset($_POST['u']) ? $_POST['u'] : array();
	$gest=isset($_POST['gest']) ? $_POST['gest'] : array();

	$login_user=isset($_POST['login_user']) ? $_POST['login_user'] : array();

	//=================================

	// Super-gestionnaires
	// Boucle sur les catégories
	$nb_del=0;
	$super_gest_deja=array();
	$sql="SELECT * FROM j_aidcateg_super_gestionnaires";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if((in_array($lig->id_utilisateur, $login_user))&&
			(!in_array($lig->indice_aid.'|'.$lig->id_utilisateur, $super_gest))) {
				$sql="DELETE FROM j_aidcateg_super_gestionnaires WHERE indice_aid='".$lig->indice_aid."' AND id_utilisateur='".$lig->id_utilisateur."';";
				$del=mysqli_query($mysqli, $sql);
				if(!$del) {
					$msg.="Erreur lors de la suppression du super-gestionnaire ".civ_nom_prenom($lig->id_utilisateur)." sur la catégorie n°".$lig->indice_aid."<br />";
				}
				else {
					$nb_del++;
				}
			}
			else {
				$super_gest_deja[]=$lig->indice_aid.'|'.$lig->id_utilisateur;
			}
		}
	}
	if($nb_del>0) {
		$msg.=$nb_del." super-gestionnaire(s) supprimé(s)<br />";
	}


	$nb_reg=0;
	$tab_super_gest=array();
	foreach($super_gest as $key => $value) {
		$tab=explode('|', $value);

		$indice_aid=$tab[0];
		$current_login=$tab[1];

		// Ajouter les super-gest qui ne le sont pas encore
		if(!in_array($indice_aid.'|'.$current_login, $super_gest_deja)) {
			$sql="INSERT INTO j_aidcateg_super_gestionnaires SET indice_aid='".$indice_aid."', id_utilisateur='".$current_login."';";
			$insert=mysqli_query($mysqli, $sql);
			if(!$insert) {
				$msg.="Erreur lors de l'ajout du super-gestionnaire ".civ_nom_prenom($lig->id_utilisateur)." sur la catégorie n°".$lig->indice_aid."<br />";
			}
			else {
				$nb_reg++;
			}
		}
	}
	if($nb_reg>0) {
		$msg.=$nb_reg." super-gestionnaire(s) ajouté(s)<br />";
	}

	//=================================


	//=================================

	// Gestionnaires de fiches projet
	// Boucle sur les catégories
	$nb_del=0;
	$c_u_deja=array();
	$sql="SELECT * FROM j_aidcateg_utilisateurs";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if((in_array($lig->id_utilisateur, $login_user))&&
			(!in_array($lig->indice_aid.'|'.$lig->id_utilisateur, $c_u))) {
				$sql="DELETE FROM j_aidcateg_utilisateurs WHERE indice_aid='".$lig->indice_aid."' AND id_utilisateur='".$lig->id_utilisateur."';";
				$del=mysqli_query($mysqli, $sql);
				if(!$del) {
					$msg.="Erreur lors de la suppression du gestionnaire de fiches projet ".civ_nom_prenom($lig->id_utilisateur)." sur la catégorie n°".$lig->indice_aid."<br />";
				}
				else {
					$nb_del++;
				}
			}
			else {
				$c_u_deja[]=$lig->indice_aid.'|'.$lig->id_utilisateur;
			}
		}
	}
	if($nb_del>0) {
		$msg.=$nb_del." gestionnaire(s) de fiches projet supprimé(s)<br />";
	}

	$nb_reg=0;
	$tab_c_u=array();
	foreach($c_u as $key => $value) {
		$tab=explode('|', $value);

		$indice_aid=$tab[0];
		$current_login=$tab[1];

		// Ajouter les gestionnaires de fiches projet qui ne le sont pas encore
		if(!in_array($indice_aid.'|'.$current_login, $c_u_deja)) {
			$sql="INSERT INTO j_aidcateg_utilisateurs SET indice_aid='".$indice_aid."', id_utilisateur='".$current_login."';";
			$insert=mysqli_query($mysqli, $sql);
			if(!$insert) {
				$msg.="Erreur lors de l'ajout du gestionnaire des fiches projet ".civ_nom_prenom($lig->id_utilisateur)." sur la catégorie n°".$lig->indice_aid."<br />";
			}
			else {
				$nb_reg++;
			}
		}
	}
	if($nb_reg>0) {
		$msg.=$nb_reg." gestionnaire(s) de fiches projet ajouté(s)<br />";
	}

	//=================================

	// Professeurs de l'AID
	// Boucle sur les catégories
	$nb_del=0;
	$u_deja=array();
	$sql="SELECT * FROM j_aid_utilisateurs";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if((in_array($lig->id_utilisateur, $login_user))&&
			(!in_array($lig->indice_aid.'|'.$lig->id_aid.'|'.$lig->id_utilisateur, $u))) {
				$sql="DELETE FROM j_aid_utilisateurs WHERE indice_aid='".$lig->indice_aid."' AND id_aid='".$lig->id_aid."' AND id_utilisateur='".$lig->id_utilisateur."';";
				$del=mysqli_query($mysqli, $sql);
				if(!$del) {
					$msg.="Erreur lors de la suppression du professeur ".civ_nom_prenom($lig->id_utilisateur)." sur l'AID n°".$lig->id_aid."<br />";
				}
				else {
					$nb_del++;
				}
			}
			else {
				$u_deja[]=$lig->indice_aid.'|'.$lig->id_aid.'|'.$lig->id_utilisateur;
			}
		}
	}
	if($nb_del>0) {
		$msg.=$nb_del." professeur(s) supprimé(s)<br />";
	}


	$nb_reg=0;
	$tab_u=array();
	foreach($u as $key => $value) {
		$tab=explode('|', $value);

		$indice_aid=$tab[0];
		$id_aid=$tab[1];
		$current_login=$tab[2];

		// Ajouter les profs qui ne le sont pas encore
		if(!in_array($indice_aid.'|'.$id_aid.'|'.$current_login, $u_deja)) {
			$sql="INSERT INTO j_aid_utilisateurs SET indice_aid='".$indice_aid."', id_aid='".$id_aid."', id_utilisateur='".$current_login."';";
			$insert=mysqli_query($mysqli, $sql);
			if(!$insert) {
				$msg.="Erreur lors de l'ajout du professeur ".civ_nom_prenom($lig->id_utilisateur)." sur l'AID n°".$lig->id_aid."<br />";
			}
			else {
				$nb_reg++;
			}
		}
	}
	if($nb_reg>0) {
		$msg.=$nb_reg." professeur(s) ajouté(s)<br />";
	}

	//=================================

	// Gestionnaires de l'AID
	// Boucle sur les catégories
	$nb_del=0;
	$gest_deja=array();
	$sql="SELECT * FROM j_aid_utilisateurs_gest";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if((in_array($lig->id_utilisateur, $login_user))&&
			(!in_array($lig->indice_aid.'|'.$lig->id_aid.'|'.$lig->id_utilisateur, $gest))) {
				$sql="DELETE FROM j_aid_utilisateurs_gest WHERE indice_aid='".$lig->indice_aid."' AND id_aid='".$lig->id_aid."' AND id_utilisateur='".$lig->id_utilisateur."';";
				$del=mysqli_query($mysqli, $sql);
				if(!$del) {
					$msg.="Erreur lors de la suppression du gestionnaire ".civ_nom_prenom($lig->id_utilisateur)." sur l'AID n°".$lig->id_aid."<br />";
				}
				else {
					$nb_del++;
				}
			}
			else {
				$gest_deja[]=$lig->indice_aid.'|'.$lig->id_aid.'|'.$lig->id_utilisateur;
			}
		}
	}
	if($nb_del>0) {
		$msg.=$nb_del." gestionnaire(s) supprimé(s)<br />";
	}


	$nb_reg=0;
	$tab_gest=array();
	foreach($gest as $key => $value) {
		$tab=explode('|', $value);

		$indice_aid=$tab[0];
		$id_aid=$tab[1];
		$current_login=$tab[2];

		// Ajouter les gestionnaires qui ne le sont pas encore
		if(!in_array($indice_aid.'|'.$id_aid.'|'.$current_login, $gest_deja)) {
			$sql="INSERT INTO j_aid_utilisateurs_gest SET indice_aid='".$indice_aid."', id_aid='".$id_aid."', id_utilisateur='".$current_login."';";
			$insert=mysqli_query($mysqli, $sql);
			if(!$insert) {
				$msg.="Erreur lors de l'ajout du gestionnaire ".civ_nom_prenom($lig->id_utilisateur)." sur l'AID n°".$lig->id_aid."<br />";
			}
			else {
				$nb_reg++;
			}
		}
	}
	if($nb_reg>0) {
		$msg.=$nb_reg." gestionnaire(s) ajouté(s)<br />";
	}

	//=================================

}
//==============================================================================

include_once 'fonctions_aid.php';
$mysqli = $GLOBALS["mysqli"];
$javascript_specifique = "aid/aid_ajax";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *********************
$titre_page = "Gestionnaires des AID";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

/*
$NiveauGestionAid_categorie=NiveauGestionAid($_SESSION["login"],$indice_aid);
$NiveauGestionAid_AID_courant=NiveauGestionAid($_SESSION["login"],$indice_aid, $aid_id);

if ($_SESSION['statut'] == 'professeur') {
	$retour = 'index2.php';
} else {
	$retour = 'index.php';
}
*/
?>
<p class="bold">
	<a href="index.php" onclick="return confirm_abandon (this, change, '<?php echo $themessage;?>')">
		<img src='../images/icons/back.png' alt='Retour' class='back_link'/>
		Retour
	</a>
	 | 
	<a href="gerer_user_aid.php" onclick="return confirm_abandon (this, change, '<?php echo $themessage;?>')">Tableau des catégories et AID</a>
	 | 
	<a href="gerer_user_aid.php?saisie_tel_user=y" onclick="return confirm_abandon (this, change, '<?php echo $themessage;?>')">Saisir pour un ou des utilisateurs particuliers</a>
</p>

<?php
	echo "<h2>Professeurs, gestionnaires,... d'AID</h2>";

	// Consulter/modifier les AID et catégories d'AID pour un ou des utilisateurs

	// Afficher l'état actuel

	$sql="SELECT * FROM aid_config ORDER BY type_aid, nom, nom_complet;";
	$res_cat=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_cat)==0) {
		echo "<p style='color:red'>Il n'existe encore aucune catégorie AID.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	//========================================================================
	$login_user=isset($_POST['login_user']) ? $_POST['login_user'] : (isset($_GET['login_user']) ? $_GET['login_user'] : NULL);
	$saisie_tel_user=isset($_POST['saisie_tel_user']) ? $_POST['saisie_tel_user'] : (isset($_GET['saisie_tel_user']) ? $_GET['saisie_tel_user'] : NULL);
	if(isset($saisie_tel_user)) {
		if((!isset($login_user))||(!is_array($login_user))) {

			echo "<form action='gerer_user_aid.php' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='saisie_tel_user' value='y' />

		<p>Pour quel(s) utilisateur(s) souhaitez-vous attribuer des rôles sur des catégories ou AID&nbsp;?</p>

		".liste_checkbox_utilisateurs(array('professeur', 'scolarite', 'cpe'))."
		".js_checkbox_change_style('checkbox_change', 'texte_', 'y')."

		<p><a href=\"javascript:cocher_decocher(true)\">Tout cocher</a> - <a href=\"javascript:cocher_decocher(false)\">Tout décocher</a></p>

		<p><input type='submit' value='Valider' /></p>
	</fieldset>
</form>";

		}
		else {

			echo "
<form action='gerer_user_aid.php' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='saisie_tel_user' value='y' />
		<input type='hidden' name='valider_saisie_tel_user' value='y' />

		<div id='fixe'>
			<input type='submit' value='Valider' />
		</div>

			<p>Choisir les catégories et AID pour ";
			$cpt=0;
			$tab_civ_nom_prenom=array();
			$tab_statut_user=array();
			foreach($login_user as $key => $current_login) {
				if($cpt>0) {
					echo ", ";
				}
				$tab_civ_nom_prenom[$current_login]=civ_nom_prenom($current_login);
				$tab_statut_user[$current_login]=get_valeur_champ('utilisateurs', "login='".$current_login."'", 'statut');
				echo "<span title='Compte ".$current_login."'>".$tab_civ_nom_prenom[$current_login]." <em>(".$tab_statut_user[$current_login].")</em></span>";
				echo "
		<input type='hidden' name='login_user[]' value='".$current_login."' />";
				$cpt++;
			}
			echo "</p>";



//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

		// A FAIRE : Ne pas proposer les colonnes Super-gestionnaire et gestionnaire si ce n'est pas activé
		if(getSettingAOui('active_mod_gest_aid')) {
			$colspan_categorie_aid=6;
			$colspan_aid=4;
		}
		else {
			$colspan_categorie_aid=5;
			$colspan_aid=5;
		}


		echo "<table class='boireaus boireaus_alt'>
		<thead>
			<tr>
				<th colspan='".$colspan_categorie_aid."'>Catégorie AID</th>
				<th colspan='".$colspan_aid."'>AID</th>

				<!--
				<th></th>
				<th></th>
				-->
			</tr>
			<tr>
				<th>Nom</th>
				<th>Nom complet</th>
				<th>Type</th>
				<th>Périodes</th>";
		if(getSettingAOui('active_mod_gest_aid')) {
			echo "
				<th>Super-gestionnaires</th>";
		}
		echo "
				<th>Gestion Fiches Projet</th>

				<th>Nom</th>
				<th>Classes</th>
				<th>Professeurs</th>";
		if(getSettingAOui('active_mod_gest_aid')) {
			echo "
				<th>Gestionnaires</th>";
		}
		echo "

				<!--
				<th>Résumé</th>
				<th>Résumé sur le bulletin</th>
				-->
			</tr>
		</thead>
		<tbody>";

		$cpt_lignes=0;

		$cpt_super_gest_checkbox=0;
		$cpt_c_u_checkbox=0;
		$cpt_gest_checkbox=0;
		$cpt_u_checkbox=0;

		$cpt_super_gest=0;
		$cpt_cat_u=0;
		$cpt_gest=0;
		$cpt_u=0;
		while($lig_cat=mysqli_fetch_object($res_cat)) {

			// Super-gestionnaires de l'ensemble de la catégorie
			$super_gest_deja=array();
			$liste_super_gest='';
			$liste_super_gest_checkbox='';
			$sql="SELECT u.* FROM j_aidcateg_super_gestionnaires jasg, utilisateurs u WHERE jasg.indice_aid='".$lig_cat->indice_aid."' AND jasg.id_utilisateur=u.login ORDER BY u.nom, u.prenom;";
			$res_sup=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_sup)>0) {
				while($lig_sup=mysqli_fetch_object($res_sup)) {
					$super_gest_deja[]=$lig_sup->login;

					if(!in_array($lig_sup->login, $login_user)) {
						//if($liste_super_gest!='') {
							$liste_super_gest.='<br />';
						//}

						$liste_super_gest.="<span id='super_gest_".$cpt_super_gest."'>";

						$liste_super_gest.="<a href='".$_SERVER['PHP_SELF']."?saisie_tel_user=y&login_user[0]=".$lig_sup->login."' onclick=\"return confirm_abandon (this, change, '$themessage')\">";
						$liste_super_gest.=$lig_sup->civilite.' '.casse_mot($lig_sup->nom, 'maj').' '.casse_mot($lig_sup->prenom, 'majf2');
						$liste_super_gest.="</a>";

						$liste_super_gest.="<a href='#ligne_".$cpt_lignes."' 
							onclick=\"supprime_super_gestionnaire('".$lig_cat->indice_aid."', '".$lig_sup->login."', 'super_gest_".$cpt_super_gest."'); return false;\" 
							title=\"Supprimer ce super-gestionnaire.\">
								<img src='../images/delete16.png' class='icone16' />
							</a>";

						$liste_super_gest.="</span>";

						$cpt_super_gest++;
					}
				}
			}

			for($loop=0;$loop<count($login_user);$loop++) {
				if($loop>0) {
					$liste_super_gest_checkbox.="<br />";
				}

				$checked='';
				if(in_array($login_user[$loop], $super_gest_deja)) {
					$checked=' checked';
				}

				$liste_super_gest_checkbox.="
			<input type='checkbox' name='super_gest[]' id='checkbox_super_gest_".$cpt_super_gest_checkbox."' value='".$lig_cat->indice_aid."|".$login_user[$loop]."' onchange=\"checkbox_change(this.id); changement();\"".$checked." />
			<label for='checkbox_super_gest_".$cpt_super_gest_checkbox."' id='texte_checkbox_super_gest_".$cpt_super_gest_checkbox."' title=\"Compte ".$login_user[$loop]." (".$tab_statut_user[$login_user[$loop]].")\" >".$tab_civ_nom_prenom[$login_user[$loop]]."</label>";
				$cpt_super_gest_checkbox++;
			}







			// Utilisateurs autorisés à modifier les fiches projet
			$liste_c_u='';
			$liste_c_u_checkbox='';
			$c_u_deja=array();
			$sql="SELECT u.* FROM j_aidcateg_utilisateurs jasg, utilisateurs u WHERE jasg.indice_aid='".$lig_cat->indice_aid."' AND jasg.id_utilisateur=u.login ORDER BY u.nom, u.prenom;";
			$res_sup=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_sup)>0) {
				while($lig_sup=mysqli_fetch_object($res_sup)) {
					$c_u_deja[]=$lig_sup->login;

					if(!in_array($lig_sup->login, $login_user)) {
						$liste_c_u.='<br />';

						$liste_c_u.="<span id='aid_c_u_".$cpt_cat_u."'>";

						$liste_c_u.="<a href='".$_SERVER['PHP_SELF']."?saisie_tel_user=y&login_user[0]=".$lig_sup->login."' onclick=\"return confirm_abandon (this, change, '$themessage')\">";
						$liste_c_u.=$lig_sup->civilite.' '.casse_mot($lig_sup->nom, 'maj').' '.casse_mot($lig_sup->prenom, 'majf2');
						$liste_c_u.="</a>";

						$liste_c_u.="<a href='#ligne_".$cpt_lignes."' 
							onclick=\"supprime_aid_c_u('".$lig_cat->indice_aid."', '".$lig_sup->login."', 'aid_u_".$cpt_u."'); return false;\" 
							title=\"Supprimer cet utilisateur AID autorisé à gérer les fiches projet.\">
								<img src='../images/delete16.png' class='icone16' />
							</a>";

						$liste_c_u.="</span>";

						$cpt_cat_u++;
					}
				}
			}

			for($loop=0;$loop<count($login_user);$loop++) {
				if(($tab_statut_user[$login_user[$loop]]=='professeur')||($tab_statut_user[$login_user[$loop]]=='cpe')) {
					//if($loop>0) {
					if($liste_c_u_checkbox!='') {
						$liste_c_u_checkbox.="<br />";
					}

					$checked='';
					if(in_array($login_user[$loop], $c_u_deja)) {
						$checked=' checked';
					}

					$liste_c_u_checkbox.="
				<input type='checkbox' name='c_u[]' id='checkbox_c_u_".$cpt_c_u_checkbox."' value='".$lig_cat->indice_aid."|".$login_user[$loop]."' onchange=\"checkbox_change(this.id); changement();\"".$checked." />
				<label for='checkbox_c_u_".$cpt_c_u_checkbox."' id='texte_checkbox_c_u_".$cpt_c_u_checkbox."' title=\"Compte ".$login_user[$loop]." (".$tab_statut_user[$login_user[$loop]].")\" >".$tab_civ_nom_prenom[$login_user[$loop]]."</label>";
					$cpt_c_u_checkbox++;
				}
			}







			$sql="SELECT * FROM aid WHERE indice_aid='".$lig_cat->indice_aid."';";
			$res_aid=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_aid)==0) {
				echo "
			<tr style='background-color:grey'>
				<td title=\"Éditer la catégorie d'AID.\">
					<a name='cat_aid_".$lig_cat->indice_aid."'></a>
					<a href='config_aid.php?indice_aid=".$lig_cat->indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">".$lig_cat->nom."</a>
				</td>
				<td>".$lig_cat->nom_complet."</td>
				<td>".traduction_type_aid($lig_cat->type_aid)."</td>
				<td title=\"Cet AID court sur les périodes ".$lig_cat->display_begin." à ".$lig_cat->display_end."\">".$lig_cat->display_begin."-&gt;".$lig_cat->display_end."</td>";
				if(getSettingAOui('active_mod_gest_aid')) {
					echo "
				<td>
					".$liste_super_gest_checkbox."
					".$liste_super_gest."
				</td>";
				}

				if($lig_cat->outils_complementaires=='y') {
					echo "
				<td>
					".$liste_c_u_checkbox."
					".$liste_c_u."
				</td>";
				}
				else {
					echo "
				<td title=\"Outils complémenatires non activés sur cette catégorie AID.\"><img src='../images/disabled.png' class='icone20' /></td>";
				}

				echo "

				<td style='color:red'>
					aucun
					<a name='ligne_".$cpt_lignes."'></a>
				</td>
				<td style='color:red'>aucunes</td>
				<td style='color:red'>aucun</td>";
				if(getSettingAOui('active_mod_gest_aid')) {
					echo "
				<td style='color:red'>aucun</td>";
				}
				echo "

				<!--
				<td>Résumé</td>
				<td>Résumé sur le bulletin</td>
				-->
			</tr>";
				$cpt_lignes++;
			}
			else {
				$cpt_aid=0;
				$nb_aid=mysqli_num_rows($res_aid);
				while($lig_aid=mysqli_fetch_object($res_aid)) {
					$liste_classes='';
					$sql="SELECT DISTINCT c.classe FROM classes c, 
											j_eleves_classes jec, 
											j_aid_eleves jae 
										WHERE c.id=jec.id_classe AND 
											jec.login=jae.login AND 
											jec.periode>='".$lig_cat->display_begin."' AND 
											jec.periode<='".$lig_cat->display_end."' AND 
											jae.id_aid='".$lig_aid->id."' AND 
											jae.indice_aid='".$lig_cat->indice_aid."' 
										ORDER BY c.classe;";
					$res_clas=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res_clas)>0) {
						while($lig_clas=mysqli_fetch_object($res_clas)) {
							if($liste_classes!='') {
								$liste_classes.=', ';
							}
							$liste_classes.=$lig_clas->classe;
							// Mettre un lien pour ajouter/supprimer des élèves
						}
					}

					$effectif_eleves='';
					$sql="SELECT DISTINCT jae.login FROM j_aid_eleves jae 
										WHERE jae.id_aid='".$lig_aid->id."' AND 
											jae.indice_aid='".$lig_cat->indice_aid."';";
					$res_eff=mysqli_query($mysqli, $sql);
					$effectif_eleves=mysqli_num_rows($res_eff);

					// Gestionnaires de l'AID
					$liste_gest='';
					$liste_gest_checkbox='';
					$gest_deja=array();
					$sql="SELECT u.* FROM j_aid_utilisateurs_gest jasg, utilisateurs u WHERE jasg.indice_aid='".$lig_cat->indice_aid."' AND jasg.id_aid='".$lig_aid->id."' AND jasg.id_utilisateur=u.login ORDER BY u.nom, u.prenom;";
					$res_sup=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res_sup)>0) {
						while($lig_sup=mysqli_fetch_object($res_sup)) {
							$gest_deja[]=$lig_sup->login;

							if(!in_array($lig_sup->login, $login_user)) {
								$liste_gest.='<br />';

								$liste_gest.="<span id='gest_".$cpt_gest."'>";

								$liste_gest.="<a href='".$_SERVER['PHP_SELF']."?saisie_tel_user=y&login_user[0]=".$lig_sup->login."' onclick=\"return confirm_abandon (this, change, '$themessage')\">";
								$liste_gest.=$lig_sup->civilite.' '.casse_mot($lig_sup->nom, 'maj').' '.casse_mot($lig_sup->prenom, 'majf2');
								$liste_gest.="</a>";
								// Mettre un lien vers tout ce qui concerne cet utilisateur
								// Un lien supprimer ou une case à cocher

								$liste_gest.="<a href='#ligne_".$cpt_lignes."' 
							onclick=\"supprime_gestionnaire('".$lig_cat->indice_aid."', '".$lig_aid->id."', '".$lig_sup->login."', 'gest_".$cpt_gest."'); return false;\" 
							title=\"Supprimer ce gestionnaire.\">
								<img src='../images/delete16.png' class='icone16' />
							</a>";

								$liste_gest.="</span>";

								$cpt_gest++;
							}
						}
					}

					for($loop=0;$loop<count($login_user);$loop++) {
						if($loop>0) {
							$liste_gest_checkbox.="<br />";
						}

						$checked='';
						if(in_array($login_user[$loop], $gest_deja)) {
							$checked=' checked';
						}

						$liste_gest_checkbox.="
					<input type='checkbox' name='gest[]' id='checkbox_gest_".$cpt_gest_checkbox."' value='".$lig_cat->indice_aid."|".$lig_aid->id."|".$login_user[$loop]."' onchange=\"checkbox_change(this.id); changement();\"".$checked." />
					<label for='checkbox_gest_".$cpt_gest_checkbox."' id='texte_checkbox_gest_".$cpt_gest_checkbox."' title=\"Compte ".$login_user[$loop]." (".$tab_statut_user[$login_user[$loop]].")\" >".$tab_civ_nom_prenom[$login_user[$loop]]."</label>";
						$cpt_gest_checkbox++;
					}







					// Professeurs,... de l'AID
					$liste_u='';
					$liste_u_checkbox='';
					$u_deja=array();
					$sql="SELECT u.* FROM j_aid_utilisateurs jasg, utilisateurs u WHERE jasg.indice_aid='".$lig_cat->indice_aid."' AND jasg.id_aid='".$lig_aid->id."' AND jasg.id_utilisateur=u.login ORDER BY u.nom, u.prenom;";
					//echo "$sql<br />";
					$res_sup=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res_sup)>0) {
						while($lig_sup=mysqli_fetch_object($res_sup)) {
							$u_deja[]=$lig_sup->login;

							if(!in_array($lig_sup->login, $login_user)) {
								$liste_u.='<br />';

								$liste_u.="<span id='aid_u_".$cpt_u."'>";

								$liste_u.="<a href='".$_SERVER['PHP_SELF']."?saisie_tel_user=y&login_user[0]=".$lig_sup->login."' onclick=\"return confirm_abandon (this, change, '$themessage')\">";
								$liste_u.=$lig_sup->civilite.' '.casse_mot($lig_sup->nom, 'maj').' '.casse_mot($lig_sup->prenom, 'majf2');
								$liste_u.="</a>";

								$liste_u.="<a href='#ligne_".$cpt_lignes."' 
							onclick=\"supprime_aid_u('".$lig_cat->indice_aid."', '".$lig_aid->id."', '".$lig_sup->login."', 'aid_u_".$cpt_u."'); return false;\" 
							title=\"Supprimer cet utilisateur AID.\">
								<img src='../images/delete16.png' class='icone16' />
							</a>";

								$liste_u.="</span>";

								$cpt_u++;
							}
						}
					}

					for($loop=0;$loop<count($login_user);$loop++) {
						if($loop>0) {
							$liste_u_checkbox.="<br />";
						}

						$checked='';
						if(in_array($login_user[$loop], $u_deja)) {
							$checked=' checked';
						}

						$liste_u_checkbox.="
					<input type='checkbox' name='u[]' id='checkbox_u_".$cpt_u_checkbox."' value='".$lig_cat->indice_aid."|".$lig_aid->id."|".$login_user[$loop]."' onchange=\"checkbox_change(this.id); changement();\"".$checked." />
					<label for='checkbox_u_".$cpt_u_checkbox."' id='texte_checkbox_u_".$cpt_u_checkbox."' title=\"Compte ".$login_user[$loop]." (".$tab_statut_user[$login_user[$loop]].")\" >".$tab_civ_nom_prenom[$login_user[$loop]]."</label>";
						$cpt_u_checkbox++;
					}






					if($cpt_aid==0) {
						echo "
			<tr>
				<td rowspan='".$nb_aid."' title=\"Éditer la catégorie d'AID.\">
					<a name='cat_aid_".$lig_cat->indice_aid."'></a>
					<a href='config_aid.php?indice_aid=".$lig_cat->indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">".$lig_cat->nom."</a>
				</td>
				<td rowspan='".$nb_aid."'>".$lig_cat->nom_complet."</td>
				<td rowspan='".$nb_aid."'>".traduction_type_aid($lig_cat->type_aid)."</td>
				<td rowspan='".$nb_aid."' title=\"Cet AID court sur les périodes ".$lig_cat->display_begin." à ".$lig_cat->display_end."\">".$lig_cat->display_begin."-&gt;".$lig_cat->display_end."</td>";

						if(getSettingAOui('active_mod_gest_aid')) {
							echo "
				<td rowspan='".$nb_aid."'>
					".$liste_super_gest_checkbox."
					".$liste_super_gest."
				</td>";
						}

						if($lig_cat->outils_complementaires=='y') {
							echo "
				<td rowspan='".$nb_aid."'>
					".$liste_c_u_checkbox."
					".$liste_c_u."
				</td>";
						}
						else {
							echo "
				<td rowspan='".$nb_aid."' title=\"Outils complémenatires non activés sur cette catégorie AID.\"><img src='../images/disabled.png' class='icone20' /></td>";
						}

						echo "
				<td title=\"Éditer cet AID.\">
					<a name='aid_".$lig_aid->id."'></a>
					<a href='".($lig_cat->outils_complementaires=='y' ? "modif_fiches.php?aid_id=".$lig_aid->id."&indice_aid=".$lig_cat->indice_aid."&action=modif" : "add_aid.php?action=modif_aid&aid_id=".$lig_aid->id."&indice_aid=".$lig_cat->indice_aid)."'>
						".$lig_aid->nom."
					</a>
					<a name='ligne_".$cpt_lignes."'></a>
				</td>
				<td".($liste_classes!='' ? " title=\"".$effectif_eleves." élève(s) parmi la ou les classes $liste_classes.\">".$liste_classes." (".$effectif_eleves.")" : '>')."</td>
				<td>
					".$liste_u_checkbox."
					".$liste_u."
				</td>";
						if(getSettingAOui('active_mod_gest_aid')) {
							echo "
				<td>
					".$liste_gest_checkbox."
					".$liste_gest."
				</td>";
						}
						echo "

				<!--
				<td>Résumé</td>
				<td>Résumé sur le bulletin</td>
				-->
			</tr>";
						$cpt_lignes++;

					}
					else {

						// les lignes après la ligne annonçant la catégorie d aid
						echo "
			<tr>
				<td title=\"Éditer cet AID.\">
					<a name='aid_".$lig_aid->id."'></a>
					<a href='".($lig_cat->outils_complementaires=='y' ? "modif_fiches.php?aid_id=".$lig_aid->id."&indice_aid=".$lig_cat->indice_aid."&action=modif" : "add_aid.php?action=modif_aid&aid_id=".$lig_aid->id."&indice_aid=".$lig_cat->indice_aid)."' onclick=\"return confirm_abandon (this, change, '$themessage')\">
						".$lig_aid->nom."
					</a>
					<a name='ligne_".$cpt_lignes."'></a>
				</td>
				<td".($liste_classes!='' ? " title=\"".$effectif_eleves." élève(s) parmi la ou les classes $liste_classes.\">".$liste_classes." (".$effectif_eleves.")" : '>')."</td>
				<td>
					".$liste_u_checkbox."
					".$liste_u."
				</td>";
						if(getSettingAOui('active_mod_gest_aid')) {
							echo "
				<td>
					".$liste_gest_checkbox."
					".$liste_gest."
				</td>";
						}
						echo "

				<!--
				<td>Résumé</td>
				<td>Résumé sur le bulletin</td>
				-->
			</tr>";
					}
					$cpt_aid++;

					$cpt_lignes++;
				}
			}

		}
		echo "
		</tbody>
	</table>

		<p><input type='submit' value='Valider' /></p>
	</fieldset>
</form>

	<script type='text/javascript'>

		".js_checkbox_change_style()."

		item=document.getElementsByTagName('input');
		for(i=0;i<item.length;i++) {
			if(item[i].getAttribute('type')=='checkbox') {
				checkbox_change(item[i].getAttribute('id'));
			}
		}

		//====================================

		function supprime_super_gestionnaire(indice_aid, login, span_id) {
			//alert('supprime_super_gestionnaire('+indice_aid+', '+login+', '+span_id+')');
			new Ajax.Updater($(span_id),'gerer_user_aid.php?indice_aid='+indice_aid+'&supprime_super_gestionnaire='+login+'".add_token_in_url(false)."',{method: 'get'});
			changement();
		}

		//====================================

		function supprime_gestionnaire(indice_aid, id_aid, login, span_id) {
			new Ajax.Updater($(span_id),'gerer_user_aid.php?indice_aid='+indice_aid+'&id_aid='+id_aid+'&supprime_gestionnaire='+login+'".add_token_in_url(false)."',{method: 'get'});
			changement();
		}

		//====================================

		function supprime_aid_u(indice_aid, id_aid, login, span_id) {
			new Ajax.Updater($(span_id),'gerer_user_aid.php?indice_aid='+indice_aid+'&id_aid='+id_aid+'&supprime_aid_u='+login+'".add_token_in_url(false)."',{method: 'get'});
			changement();
		}

		//====================================

		function supprime_aid_c_u(indice_aid, login, span_id) {
			new Ajax.Updater($(span_id),'gerer_user_aid.php?indice_aid='+indice_aid+'&supprime_aid_c_u='+login+'".add_token_in_url(false)."',{method: 'get'});
			changement();
		}

		//====================================
	</script>";


//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


		}

		require("../lib/footer.inc.php");
		die();
	}
	//========================================================================


	// A FAIRE : Ne pas proposer les colonnes Super-gestionnaire et gestionnaire si ce n'est pas activé
	if(getSettingAOui('active_mod_gest_aid')) {
		$colspan_categorie_aid=6;
		$colspan_aid=4;
	}
	else {
		$colspan_categorie_aid=5;
		$colspan_aid=5;
	}


	echo "<table class='boireaus boireaus_alt'>
	<thead>
		<tr>
			<th colspan='".$colspan_categorie_aid."'>Catégorie AID</th>
			<th colspan='".$colspan_aid."'>AID</th>

			<!--
			<th></th>
			<th></th>
			-->
		</tr>
		<tr>
			<th>Nom</th>
			<th>Nom complet</th>
			<th>Type</th>
			<th>Périodes</th>";
	if(getSettingAOui('active_mod_gest_aid')) {
		echo "
			<th>Super-gestionnaires</th>";
	}
	echo "
			<th>Gestion Fiches Projet</th>

			<th>Nom</th>
			<th>Classes</th>
			<th>Professeurs</th>";
	if(getSettingAOui('active_mod_gest_aid')) {
		echo "
			<th>Gestionnaires</th>";
	}
	echo "

			<!--
			<th>Résumé</th>
			<th>Résumé sur le bulletin</th>
			-->
		</tr>
	</thead>
	<tbody>";

	$cpt_lignes=0;

	$cpt_super_gest=0;
	$cpt_cat_u=0;
	$cpt_gest=0;
	$cpt_u=0;
	while($lig_cat=mysqli_fetch_object($res_cat)) {

		// Super-gestionnaires de l'ensemble de la catégorie
		$liste_super_gest='';
		$sql="SELECT u.* FROM j_aidcateg_super_gestionnaires jasg, utilisateurs u WHERE jasg.indice_aid='".$lig_cat->indice_aid."' AND jasg.id_utilisateur=u.login ORDER BY u.nom, u.prenom;";
		$res_sup=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_sup)>0) {
			while($lig_sup=mysqli_fetch_object($res_sup)) {
				if($liste_super_gest!='') {
					$liste_super_gest.='<br />';
				}

				$liste_super_gest.="<span id='super_gest_".$cpt_super_gest."'>";

				$liste_super_gest.="<a href='".$_SERVER['PHP_SELF']."?saisie_tel_user=y&login_user[0]=".$lig_sup->login."'>";
				$liste_super_gest.=$lig_sup->civilite.' '.casse_mot($lig_sup->nom, 'maj').' '.casse_mot($lig_sup->prenom, 'majf2');
				$liste_super_gest.="</a>";

				$liste_super_gest.="<a href='#ligne_".$cpt_lignes."' 
					onclick=\"supprime_super_gestionnaire('".$lig_cat->indice_aid."', '".$lig_sup->login."', 'super_gest_".$cpt_super_gest."'); return false;\" 
					title=\"Supprimer ce super-gestionnaire.\">
						<img src='../images/delete16.png' class='icone16' />
					</a>";

				$liste_super_gest.="</span>";

				// Mettre un lien vers tout ce qui concerne cet utilisateur
				// Un lien supprimer ou une case à cocher
				$cpt_super_gest++;
			}
		}

		// Utilisateurs autorisés à modifier les fiches projet
		$liste_c_u='';
		$sql="SELECT u.* FROM j_aidcateg_utilisateurs jasg, utilisateurs u WHERE jasg.indice_aid='".$lig_cat->indice_aid."' AND jasg.id_utilisateur=u.login ORDER BY u.nom, u.prenom;";
		$res_sup=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_sup)>0) {
			while($lig_sup=mysqli_fetch_object($res_sup)) {
				if($liste_c_u!='') {
					$liste_c_u.='<br />';
				}

				$liste_c_u.="<span id='aid_c_u_".$cpt_cat_u."'>";

				$liste_c_u.="<a href='".$_SERVER['PHP_SELF']."?saisie_tel_user=y&login_user[0]=".$lig_sup->login."'>";
				$liste_c_u.=$lig_sup->civilite.' '.casse_mot($lig_sup->nom, 'maj').' '.casse_mot($lig_sup->prenom, 'majf2');
				$liste_c_u.="</a>";

				$liste_c_u.="<a href='#ligne_".$cpt_lignes."' 
					onclick=\"supprime_aid_c_u('".$lig_cat->indice_aid."', '".$lig_sup->login."', 'aid_u_".$cpt_u."'); return false;\" 
					title=\"Supprimer cet utilisateur AID autorisé à gérer les fiches projet.\">
						<img src='../images/delete16.png' class='icone16' />
					</a>";

				$liste_c_u.="</span>";

				$cpt_cat_u++;
			}
		}




		$sql="SELECT * FROM aid WHERE indice_aid='".$lig_cat->indice_aid."';";
		$res_aid=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_aid)==0) {
			echo "
		<tr style='background-color:grey'>
			<td title=\"Éditer la catégorie d'AID.\">
				<a name='cat_aid_".$lig_cat->indice_aid."'></a>
				<a href='config_aid.php?indice_aid=".$lig_cat->indice_aid."'>".$lig_cat->nom."</a>
			</td>
			<td>".$lig_cat->nom_complet."</td>
			<td>".traduction_type_aid($lig_cat->type_aid)."</td>
			<td title=\"Cet AID court sur les périodes ".$lig_cat->display_begin." à ".$lig_cat->display_end."\">".$lig_cat->display_begin."-&gt;".$lig_cat->display_end."</td>";
			if(getSettingAOui('active_mod_gest_aid')) {
				echo "
			<td>
				".$liste_super_gest."
				<div id='div_super_gest_".$lig_cat->indice_aid."'></div>
				<div style='float:right; width:16px;'>
					<a href='#ligne_".$cpt_lignes."' 
						onclick=\"ajouter_super_gest($lig_cat->indice_aid); return false;\" 
						title=\"\"><img src='../images/icons/plus_moins.png' class='icone16'/></a>
				</div>
			</td>";
			}

			if($lig_cat->outils_complementaires=='y') {
				echo "
			<td>".$liste_c_u."</td>";
			}
			else {
				echo "
			<td title=\"Outils complémenatires non activés sur cette catégorie AID.\"><img src='../images/disabled.png' class='icone20' /></td>";
			}

			echo "

			<td style='color:red'>
				aucun
				<a name='ligne_".$cpt_lignes."'></a>
			</td>
			<td style='color:red'>aucunes</td>
			<td style='color:red'>aucun</td>";
			if(getSettingAOui('active_mod_gest_aid')) {
				echo "
			<td style='color:red'>aucun</td>";
			}
			echo "

			<!--
			<td>Résumé</td>
			<td>Résumé sur le bulletin</td>
			-->
		</tr>";
			$cpt_lignes++;
		}
		else {
			$cpt_aid=0;
			$nb_aid=mysqli_num_rows($res_aid);
			while($lig_aid=mysqli_fetch_object($res_aid)) {
				$liste_classes='';
				$sql="SELECT DISTINCT c.classe FROM classes c, 
										j_eleves_classes jec, 
										j_aid_eleves jae 
									WHERE c.id=jec.id_classe AND 
										jec.login=jae.login AND 
										jec.periode>='".$lig_cat->display_begin."' AND 
										jec.periode<='".$lig_cat->display_end."' AND 
										jae.id_aid='".$lig_aid->id."' AND 
										jae.indice_aid='".$lig_cat->indice_aid."' 
									ORDER BY c.classe;";
				$res_clas=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_clas)>0) {
					while($lig_clas=mysqli_fetch_object($res_clas)) {
						if($liste_classes!='') {
							$liste_classes.=', ';
						}
						$liste_classes.=$lig_clas->classe;
						// Mettre un lien pour ajouter/supprimer des élèves
					}
				}

				$effectif_eleves='';
				$sql="SELECT DISTINCT jae.login FROM j_aid_eleves jae 
									WHERE jae.id_aid='".$lig_aid->id."' AND 
										jae.indice_aid='".$lig_cat->indice_aid."';";
				$res_eff=mysqli_query($mysqli, $sql);
				$effectif_eleves=mysqli_num_rows($res_eff);

				// Gestionnaires de l'AID
				$liste_gest='';
				$sql="SELECT u.* FROM j_aid_utilisateurs_gest jasg, utilisateurs u WHERE jasg.indice_aid='".$lig_cat->indice_aid."' AND jasg.id_aid='".$lig_aid->id."' AND jasg.id_utilisateur=u.login ORDER BY u.nom, u.prenom;";
				$res_sup=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_sup)>0) {
					while($lig_sup=mysqli_fetch_object($res_sup)) {
						if($liste_gest!='') {
							$liste_gest.='<br />';
						}

						$liste_gest.="<span id='gest_".$cpt_gest."'>";

						$liste_gest.="<a href='".$_SERVER['PHP_SELF']."?saisie_tel_user=y&login_user[0]=".$lig_sup->login."'>";
						$liste_gest.=$lig_sup->civilite.' '.casse_mot($lig_sup->nom, 'maj').' '.casse_mot($lig_sup->prenom, 'majf2');
						$liste_gest.="</a>";
						// Mettre un lien vers tout ce qui concerne cet utilisateur
						// Un lien supprimer ou une case à cocher

						$liste_gest.="<a href='#ligne_".$cpt_lignes."' 
					onclick=\"supprime_gestionnaire('".$lig_cat->indice_aid."', '".$lig_aid->id."', '".$lig_sup->login."', 'gest_".$cpt_gest."'); return false;\" 
					title=\"Supprimer ce gestionnaire.\">
						<img src='../images/delete16.png' class='icone16' />
					</a>";

						$liste_gest.="</span>";

						$cpt_gest++;
					}
				}

				// Professeurs,... de l'AID
				$liste_u='';
				$sql="SELECT u.* FROM j_aid_utilisateurs jasg, utilisateurs u WHERE jasg.indice_aid='".$lig_cat->indice_aid."' AND jasg.id_aid='".$lig_aid->id."' AND jasg.id_utilisateur=u.login ORDER BY u.nom, u.prenom;";
				//echo "$sql<br />";
				$res_sup=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_sup)>0) {
					while($lig_sup=mysqli_fetch_object($res_sup)) {
						if($liste_u!='') {
							$liste_u.='<br />';
						}

						$liste_u.="<span id='aid_u_".$cpt_u."'>";

						$liste_u.="<a href='".$_SERVER['PHP_SELF']."?saisie_tel_user=y&login_user[0]=".$lig_sup->login."'>";
						$liste_u.=$lig_sup->civilite.' '.casse_mot($lig_sup->nom, 'maj').' '.casse_mot($lig_sup->prenom, 'majf2');
						$liste_u.="</a>";

						$liste_u.="<a href='#ligne_".$cpt_lignes."' 
					onclick=\"supprime_aid_u('".$lig_cat->indice_aid."', '".$lig_aid->id."', '".$lig_sup->login."', 'aid_u_".$cpt_u."'); return false;\" 
					title=\"Supprimer cet utilisateur AID.\">
						<img src='../images/delete16.png' class='icone16' />
					</a>";

						$liste_u.="</span>";

						$cpt_u++;
					}
				}

				if($cpt_aid==0) {
					echo "
		<tr>
			<td rowspan='".$nb_aid."' title=\"Éditer la catégorie d'AID.\">
				<a name='cat_aid_".$lig_cat->indice_aid."'></a>
				<a href='config_aid.php?indice_aid=".$lig_cat->indice_aid."'>".$lig_cat->nom."</a>
			</td>
			<td rowspan='".$nb_aid."'>".$lig_cat->nom_complet."</td>
			<td rowspan='".$nb_aid."'>".traduction_type_aid($lig_cat->type_aid)."</td>
			<td rowspan='".$nb_aid."' title=\"Cet AID court sur les périodes ".$lig_cat->display_begin." à ".$lig_cat->display_end."\">".$lig_cat->display_begin."-&gt;".$lig_cat->display_end."</td>";

					if(getSettingAOui('active_mod_gest_aid')) {
						echo "
			<td rowspan='".$nb_aid."'>
				".$liste_super_gest."
				<div id='div_super_gest_".$lig_cat->indice_aid."'></div>
				<div style='float:right; width:16px;'>
					<a href='#ligne_".$cpt_lignes."' 
						onclick=\"ajouter_super_gest($lig_cat->indice_aid); return false;\" 
						title=\"Ajouter un ou des super-gestionnaires.\"><img src='../images/icons/plus_moins.png' class='icone16'/></a>
				</div>
			</td>";
					}

					if($lig_cat->outils_complementaires=='y') {
						echo "
			<td rowspan='".$nb_aid."'>
				".$liste_c_u."
				<div style='float:right; width:16px;'>
					<a href='#ligne_".$cpt_lignes."' 
						onclick=\"ajouter_aid_c_u($lig_cat->indice_aid); return false;\" 
						title=\"Ajouter un ou des utilisateurs autorisés à gérer les fiches projet.\"><img src='../images/icons/plus_moins.png' class='icone16'/></a>
				</div>
			</td>";
					}
					else {
						echo "
			<td rowspan='".$nb_aid."' title=\"Outils complémenatires non activés sur cette catégorie AID.\"><img src='../images/disabled.png' class='icone20' /></td>";
					}

					echo "
			<td title=\"Éditer cet AID.\">
				<a name='aid_".$lig_aid->id."'></a>
				<a href='".($lig_cat->outils_complementaires=='y' ? "modif_fiches.php?aid_id=".$lig_aid->id."&indice_aid=".$lig_cat->indice_aid."&action=modif" : "add_aid.php?action=modif_aid&aid_id=".$lig_aid->id."&indice_aid=".$lig_cat->indice_aid)."'>
					".$lig_aid->nom."
				</a>
				<a name='ligne_".$cpt_lignes."'></a>
			</td>
			<td".($liste_classes!='' ? " title=\"".$effectif_eleves." élève(s) parmi la ou les classes $liste_classes.\">".$liste_classes." (".$effectif_eleves.")" : '>')."</td>
			<td>
				".$liste_u."
				<div style='float:right; width:16px;'>
					<a href='#ligne_".$cpt_lignes."' 
						onclick=\"ajouter_aid_u($lig_cat->indice_aid, $lig_aid->id); return false;\" 
						title=\"Ajouter un ou des utilisateurs/professeurs.\"><img src='../images/icons/plus_moins.png' class='icone16'/></a>
				</div>
			</td>";
					if(getSettingAOui('active_mod_gest_aid')) {
						echo "
			<td>
				".$liste_gest."
				<div style='float:right; width:16px;'>
					<a href='#ligne_".$cpt_lignes."' 
						onclick=\"ajouter_gest($lig_cat->indice_aid, $lig_aid->id); return false;\" 
						title=\"Ajouter un ou des gestionnaires.\"><img src='../images/icons/plus_moins.png' class='icone16'/></a>
				</div>
			</td>";
					}
					echo "

			<!--
			<td>Résumé</td>
			<td>Résumé sur le bulletin</td>
			-->
		</tr>";
					$cpt_lignes++;

				}
				else {

					// les lignes après la ligne annonçant la catégorie d aid
					echo "
		<tr>
			<td title=\"Éditer cet AID.\">
				<a name='aid_".$lig_aid->id."'></a>
				<a href='".($lig_cat->outils_complementaires=='y' ? "modif_fiches.php?aid_id=".$lig_aid->id."&indice_aid=".$lig_cat->indice_aid."&action=modif" : "add_aid.php?action=modif_aid&aid_id=".$lig_aid->id."&indice_aid=".$lig_cat->indice_aid)."'>
					".$lig_aid->nom."
				</a>
				<a name='ligne_".$cpt_lignes."'></a>
			</td>
			<td".($liste_classes!='' ? " title=\"".$effectif_eleves." élève(s) parmi la ou les classes $liste_classes.\">".$liste_classes." (".$effectif_eleves.")" : '>')."</td>
			<td>
				".$liste_u."
				<div style='float:right; width:16px;'>
					<a href='#ligne_".$cpt_lignes."' 
						onclick=\"ajouter_aid_u($lig_cat->indice_aid, $lig_aid->id); return false;\" 
						title=\"Ajouter un ou des utilisateurs/professeurs.\"><img src='../images/icons/plus_moins.png' class='icone16'/></a>
				</div>
			</td>";
					if(getSettingAOui('active_mod_gest_aid')) {
						echo "
			<td>
				".$liste_gest."
				<div style='float:right; width:16px;'>
					<a href='#ligne_".$cpt_lignes."' 
						onclick=\"ajouter_gest($lig_cat->indice_aid, $lig_aid->id); return false;\" 
						title=\"Ajouter un ou des gestionnaires.\"><img src='../images/icons/plus_moins.png' class='icone16'/></a>
				</div>
			</td>";
					}
					echo "

			<!--
			<td>Résumé</td>
			<td>Résumé sur le bulletin</td>
			-->
		</tr>";
				}
				$cpt_aid++;

				$cpt_lignes++;
			}
		}

	}
	echo "
	</tbody>
</table>

<script type='text/javascript'>

	//====================================

	function supprime_super_gestionnaire(indice_aid, login, span_id) {
		new Ajax.Updater($(span_id),'gerer_user_aid.php?indice_aid='+indice_aid+'&supprime_super_gestionnaire='+login+'".add_token_in_url(false)."',{method: 'get'});
		changement();
	}

	/*
	// Avec un formulaire, des cases à cocher ajoutées, on pourrait utiliser:
	function removeElement(id) {
		element = document.getElementById(id);
		element.parentNode.removeChild(element);
	}
	*/

	function ajouter_super_gest(indice_aid) {
		new Ajax.Updater($('div_liste_super_gest_ajoutables'),'gerer_user_aid.php?indice_aid='+indice_aid+'&maj_liste_super_gestionnaire_dans_form=y".add_token_in_url(false)."',{method: 'get'});

		document.getElementById('indice_aid_ajout_super_gest').value=indice_aid;

		afficher_div('div_ajout_super_gest','y',-20,20);
	}

	/*
	function valider_ajout_super_gest() {
		//alert('login_user='+document.getElementById('login_ajout_super_gest').value);
		// On ne récupère que le premier

	}
	*/

	//====================================

	function supprime_gestionnaire(indice_aid, id_aid, login, span_id) {
		new Ajax.Updater($(span_id),'gerer_user_aid.php?indice_aid='+indice_aid+'&id_aid='+id_aid+'&supprime_gestionnaire='+login+'".add_token_in_url(false)."',{method: 'get'});
		changement();
	}

	function ajouter_gest(indice_aid, id_aid) {
		new Ajax.Updater($('div_liste_gest_ajoutables'),'gerer_user_aid.php?indice_aid='+indice_aid+'&id_aid='+id_aid+'&maj_liste_gestionnaire_dans_form=y".add_token_in_url(false)."',{method: 'get'});

		document.getElementById('indice_aid_ajout_gest').value=indice_aid;
		document.getElementById('id_aid_ajout_gest').value=id_aid;

		afficher_div('div_ajout_gest','y',-20,20);
	}

	//====================================

	function supprime_aid_u(indice_aid, id_aid, login, span_id) {
		new Ajax.Updater($(span_id),'gerer_user_aid.php?indice_aid='+indice_aid+'&id_aid='+id_aid+'&supprime_aid_u='+login+'".add_token_in_url(false)."',{method: 'get'});
		changement();
	}

	function ajouter_aid_u(indice_aid, id_aid) {
		new Ajax.Updater($('div_liste_aid_u_ajoutables'),'gerer_user_aid.php?indice_aid='+indice_aid+'&id_aid='+id_aid+'&maj_liste_aid_u_dans_form=y".add_token_in_url(false)."',{method: 'get'});

		document.getElementById('indice_aid_ajout_aid_u').value=indice_aid;
		document.getElementById('id_aid_ajout_aid_u').value=id_aid;

		afficher_div('div_ajout_aid_u','y',-20,20);
	}

	//====================================

	function supprime_aid_c_u(indice_aid, login, span_id) {
		new Ajax.Updater($(span_id),'gerer_user_aid.php?indice_aid='+indice_aid+'&supprime_aid_c_u='+login+'".add_token_in_url(false)."',{method: 'get'});
		changement();
	}

	function ajouter_aid_c_u(indice_aid) {
		new Ajax.Updater($('div_liste_aid_c_u_ajoutables'),'gerer_user_aid.php?indice_aid='+indice_aid+'&maj_liste_aid_c_u_dans_form=y".add_token_in_url(false)."',{method: 'get'});

		document.getElementById('indice_aid_ajout_aid_c_u').value=indice_aid;

		afficher_div('div_ajout_aid_c_u','y',-20,20);
	}

	//====================================
</script>";

$titre_infobulle="Ajout super-gestionnaires";
$texte_infobulle="<form action='gerer_user_aid.php' method='post'>
	<div id='div_liste_super_gest_ajoutables'>
		<!-- 
		<p>Ajouter les utilisateurs suivants comme super-gestionnaires&nbsp;:</p>
		-->
	</div>
	<!--input type='button' value='Ajouter' onclick='valider_ajout_super_gest()' /-->
	".add_token_field()."
	<input type='hidden' name='indice_aid' id='indice_aid_ajout_super_gest' value='' />
	<input type='hidden' name='valider_ajout_super_gest' value='y' />
	<input type='submit' value='Ajouter' />
</form>";
$tabdiv_infobulle[]=creer_div_infobulle("div_ajout_super_gest", $titre_infobulle, "", $texte_infobulle, "", 20, 0, 'y', 'y', 'n', 'n');

$titre_infobulle="Ajout gestionnaires fiches projet";
$texte_infobulle="<form action='gerer_user_aid.php' method='post'>
	<div id='div_liste_aid_c_u_ajoutables'>
		<!-- 
		<p>Ajouter les utilisateurs suivants comme gestionnaires des fiches projet&nbsp;:</p>
		-->
	</div>
	<!--input type='button' value='Ajouter' onclick='valider_ajout_aid_c_u()' /-->
	".add_token_field()."
	<input type='hidden' name='indice_aid' id='indice_aid_ajout_aid_c_u' value='' />
	<input type='hidden' name='valider_ajout_aid_c_u' value='y' />
	<input type='submit' value='Ajouter' />
</form>";
$tabdiv_infobulle[]=creer_div_infobulle("div_ajout_aid_c_u", $titre_infobulle, "", $texte_infobulle, "", 20, 0, 'y', 'y', 'n', 'n');

$titre_infobulle="Ajout gestionnaires";
$texte_infobulle="<form action='gerer_user_aid.php' method='post'>
	<div id='div_liste_gest_ajoutables'>
		<!-- 
		<p>Ajouter les utilisateurs suivants comme gestionnaires&nbsp;:</p>
		-->
	</div>
	".add_token_field()."
	<input type='hidden' name='indice_aid' id='indice_aid_ajout_gest' value='' />
	<input type='hidden' name='id_aid' id='id_aid_ajout_gest' value='' />
	<input type='hidden' name='valider_ajout_gest' value='y' />
	<input type='submit' value='Ajouter' />
</form>";
$tabdiv_infobulle[]=creer_div_infobulle("div_ajout_gest", $titre_infobulle, "", $texte_infobulle, "", 20, 0, 'y', 'y', 'n', 'n');

$titre_infobulle="Ajout utilisateurs";
$texte_infobulle="<form action='gerer_user_aid.php' method='post'>
	<div id='div_liste_aid_u_ajoutables'>
		<!-- 
		<p>Ajouter les utilisateurs suivants comme utilisateurs/professeurs&nbsp;:</p>
		-->
	</div>
	".add_token_field()."
	<input type='hidden' name='indice_aid' id='indice_aid_ajout_aid_u' value='' />
	<input type='hidden' name='id_aid' id='id_aid_ajout_aid_u' value='' />
	<input type='hidden' name='valider_ajout_aid_u' value='y' />
	<input type='submit' value='Ajouter' />
</form>";
$tabdiv_infobulle[]=creer_div_infobulle("div_ajout_aid_u", $titre_infobulle, "", $texte_infobulle, "", 20, 0, 'y', 'y', 'n', 'n');


	require("../lib/footer.inc.php");
	//die();
