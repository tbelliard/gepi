<?php
/**
 * Copie des Notes
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*
 * @copyright Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @license GNU/GPL
 * @package Carnet_de_notes
 * @subpackage saisie
 * @see add_token_field()
 * @see check_token()
 * @see checkAccess()
 * @see corriger_caracteres()
 * @see creer_div_infobulle()
 * @see formate_date()
 * @see getSettingValue()
 * @see get_group()
 * @see get_groups_for_prof()
 * @see getPref()
 * @see html_entity_decode()
 * @see javascript_tab_stat()
 * @see mise_a_jour_moyennes_conteneurs()
 * @see nom_photo()
 * @see recherche_enfant()
 * @see Session::security_check()
 * @see sous_conteneurs()
 * @see traitement_magic_quotes()
 * @see Verif_prof_cahier_notes()
 */

/*
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

/**
 * Fichiers d'initialisation
 */
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

// INSERT INTO droits VALUES ('/cahier_notes/copie_dev.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
	die("Le module n'est pas activé.");
}

$msg="";

unset($id_devoir);
$id_devoir = isset($_POST["id_devoir"]) ? $_POST["id_devoir"] : (isset($_GET["id_devoir"]) ? $_GET["id_devoir"] : NULL);

if (!isset($id_devoir)) {
	header("Location: index.php?msg=Aucun devoir choisi");
	die();
}

if(!preg_match("/^[0-9]*$/", $id_devoir)) {
	header("Location: index.php?msg=".rawurlencode("Le devoir choisi n°$id_devoir est invalide."));
	die();
}

$sql="SELECT * FROM cn_devoirs WHERE id ='$id_devoir';";
//echo "$sql<br />";
$appel_devoir = mysqli_query($GLOBALS["mysqli"], $sql);
if (mysqli_num_rows($appel_devoir)==0) {
	header("Location: index.php?msg=".rawurlencode("Le devoir choisi n°$id_devoir est invalide."));
	die();
}

$nom_devoir = old_mysql_result($appel_devoir, 0, 'nom_court');
//$ramener_sur_referentiel_dev_choisi=old_mysql_result($appel_devoir, 0, 'ramener_sur_referentiel');
//$note_sur_dev_choisi=old_mysql_result($appel_devoir, 0, 'note_sur');

$sql="SELECT id_conteneur, id_racine FROM cn_devoirs WHERE id = '$id_devoir';";
$query = mysqli_query($GLOBALS["mysqli"], $sql);
$id_racine = old_mysql_result($query, 0, 'id_racine');
$id_conteneur = old_mysql_result($query, 0, 'id_conteneur');

if(!Verif_prof_cahier_notes ($_SESSION['login'],$id_racine)) {
	$mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
	header("Location: index.php?msg=$mess");
	die();
}

$sql="SELECT * FROM cn_cahier_notes WHERE id_cahier_notes='$id_racine';";
$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_grp)==0) {
	$mess=rawurlencode("Le carnet de notes n°$id_racine n'existe pas???");
	header("Location: index.php?msg=$mess");
	die();
}
else {
	$lig_grp=mysqli_fetch_object($res_grp);
	$id_groupe_src=$lig_grp->id_groupe;
	$periode_num_src=$lig_grp->periode;

	$group_src=get_group($id_groupe_src);
}

$id_groupe_dest=isset($_GET['id_groupe_dest']) ? $_GET['id_groupe_dest'] : NULL;
$periode_num_dest=isset($_GET['periode_num_dest']) ? $_GET['periode_num_dest'] : NULL;

if((isset($id_groupe_dest))&&(isset($periode_num_dest))) {
	check_token();

	if(!preg_match("/^[0-9]*$/", $id_groupe_dest)) {
		unset($id_groupe_dest);
		$msg.="Choix de groupe ($id_groupe_dest) invalide.<br />";
	}
	elseif(!preg_match("/^[0-9]*$/", $periode_num_dest)) {
		unset($periode_num_dest);
		$msg.="Choix de période ($periode_num_dest) invalide.<br />";
	}
	else {
		$groupe_dest=get_group($id_groupe_dest);

		if((!isset($groupe_dest["profs"]["list"]))||(!in_array($_SESSION['login'], $groupe_dest["profs"]["list"]))) {
			$msg.="Vous n'êtes pas professeur du groupe n°$id_groupe_dest.<br />";
		}
		elseif(!isset($groupe_dest["classe"]["ver_periode"]["all"][$periode_num_dest])) {
			$msg.="La période choisie $periode_num_dest n'existe pas pour le groupe n°$id_groupe_dest.<br />";
		}
		elseif($groupe_dest["classe"]["ver_periode"]["all"][$periode_num_dest]<2) {
			$msg.="La période choisie $periode_num_dest est close pour le groupe n°$id_groupe_dest.<br />";
		}
		else {
			// Création ou récupération du CN
			$id_cn_dest=creer_carnet_notes($id_groupe_dest, $periode_num_dest);

			if(!$id_cn_dest) {
				$msg.="Echec de la création/récupération du carnet de notes sur la période $periode_num_dest pour le groupe n°$id_groupe_dest.<br />";
			}
			else {
				// Récupération des infos du devoir d'origine
				$sql="SELECT * FROM cn_devoirs WHERE id='$id_devoir';";
				//echo "$sql<br />";
				$appel_devoir = mysqli_query($GLOBALS["mysqli"], $sql);
				$lig_dev_src=mysqli_fetch_object($appel_devoir);

				$tab_note=array();
				$sql="SELECT * FROM cn_notes_devoirs WHERE id_devoir='$id_devoir';";
				$appel_notes_devoir = mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($appel_notes_devoir)>0) {
					while($lig_note_src=mysqli_fetch_object($appel_notes_devoir)) {
						$tab_note[$lig_note_src->login]['note']=$lig_note_src->note;
						$tab_note[$lig_note_src->login]['statut']=$lig_note_src->statut;
						$tab_note[$lig_note_src->login]['comment']=$lig_note_src->comment;
					}
				}

				// Créer le devoir destination
				$sql="insert into cn_devoirs SET
					id_racine='$id_cn_dest',
					id_conteneur='$id_cn_dest',
					nom_court='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig_dev_src->nom_court) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
					nom_complet='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig_dev_src->nom_complet) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
					description='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig_dev_src->description) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
					coef='".$lig_dev_src->coef."',
					note_sur='".$lig_dev_src->note_sur."',
					ramener_sur_referentiel='".$lig_dev_src->ramener_sur_referentiel."',
					facultatif='".$lig_dev_src->facultatif."',
					date='".$lig_dev_src->date."',
					date_ele_resp='".$lig_dev_src->date_ele_resp."',
					display_parents='".$lig_dev_src->display_parents."',
					display_parents_app='".$lig_dev_src->display_parents_app."';";
				//echo "$sql<br />\n";
				$creation_dev=mysqli_query($GLOBALS["mysqli"], $sql);
				$id_devoir_dest = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

				if(!$id_devoir_dest) {
					$msg.="Echec de la création du devoir destination sur la période $periode_num_dest pour le groupe n°$id_groupe_dest.<br />";
				}
				else {
					// Copier les notes
					foreach($groupe_dest["classes"]["list"] as $tmp_id_classe) {
						if($groupe_dest["classe"]["ver_periode"][$tmp_id_classe][$periode_num_dest]=='N') {
							foreach($groupe_dest["eleves"][$periode_num_dest]["telle_classe"][$tmp_id_classe] as $ele_login) {
								if(isset($tab_note[$ele_login])) {
									$sql="INSERT INTO cn_notes_devoirs SET id_devoir='$id_devoir_dest', login='$ele_login', note='".$tab_note[$ele_login]['note']."', statut='".$tab_note[$ele_login]['statut']."', comment='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab_note[$ele_login]['comment']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$insert) {
										$msg.="Erreur lors de l'enregistrement de la note pour ".civ_nom_prenom($ele_login)."<br />";
									}
								}
							}
						}
					}

					// Mise à jour des moyennes
					$arret = 'no';
					mise_a_jour_moyennes_conteneurs($groupe_dest, $periode_num_dest,$id_cn_dest,$id_cn_dest,$arret);

					if($msg=="") {
						$msg="Devoir copié : <a href='saisie_notes.php?id_groupe=$id_groupe_dest&periode_num=$periode_num_dest'>$lig_dev_src->nom_court</a>.<br />";
					}
				}
			}
		}
	}
}

//require('cc_lib.php');

//$themessage  = 'Des notes ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Copie de devoir";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();

echo "<p class='bold'>\n";
//  onclick=\"return confirm_abandon (this, change, '$themessage')\"
echo "<a href=\"saisie_notes.php?id_groupe=$id_groupe_src&periode_num=$periode_num_src\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>";
echo "</p>\n";

echo "<p>Vous souhaitez copier le devoir <strong>$nom_devoir</strong> (<em>et éventuellement les notes</em>) de l'enseignement <strong>".$group_src['name']." (<em>".$group_src['description']."</em>) en ".$group_src["classlist_string"]."</strong>.</p>\n";
echo "<p>Vers quel enseignement/période souhaitez-vous copier ce devoir&nbsp;?</p>\n";

echo "<ul>\n";
$groups=get_groups_for_prof($_SESSION['login'], NULL, array('matieres', 'classes', 'periodes', 'visibilite'));
for($i=0;$i<count($groups);$i++) {
	if((!isset($groups[$i]["visibilite"]['cahier_notes']))||($groups[$i]["visibilite"]['cahier_notes']=='y')) {
		echo "<li>\n";
		echo "<p><strong>".$groups[$i]['name']." (<em>".$groups[$i]['description']."</em>) en ".$groups[$i]["classlist_string"]."&nbsp;:</strong>";
		for($j=1;$j<=count($groups[$i]["periodes"]);$j++) {
			if($j>1) {echo " - ";}
			if($groups[$i]["classe"]["ver_periode"]["all"][$j]>=2) {
				echo "<a href='".$_SERVER['PHP_SELF']."?id_devoir=".$id_devoir."&amp;id_groupe_dest=".$groups[$i]["id"]."&amp;periode_num_dest=$j".add_token_in_url()."' title=\"Copier le devoir (et les notes) vers le carnet de notes de cet enseignement/période.\">".$groups[$i]["periodes"][$j]["nom_periode"]."</a>";
			}
			else {
				echo "<span title='Période close'>".$groups[$i]["periodes"][$j]["nom_periode"]."</span>";
			}
		}
		echo "</p>\n";
		echo "</li>\n";
	}
}
echo "</ul>\n";
/**
 * Pied de page
 */
require("../lib/footer.inc.php");
?>
